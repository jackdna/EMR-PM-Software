#!/bin/bash
echo -n "Enter Database Server ("localhost" if running script at database server): "
read db_host
echo -n "Enter Database Username: "
read db_user
echo -n "Enter Database Password: "
read -s db_pass
echo -n -e "\nEnter IMW Main Database Name: "
read main_db
echo -n "Enter IMW Scan Database Name: "
read scan_db
SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")
export MYSQL_PWD=${db_pass}
mysql_conn="mysql -h ${db_host} --user=${db_user}"
echo "Checking MySQL connection..."
${mysql_conn} -e exit 2>/dev/null
dbstatus=`echo $?`

if [ $dbstatus -ne 0 ] || [ -z $db_host ] || [ -z $db_user ] || [ -z $db_pass ] || [ -z $main_db ] || [ -z $scan_db ]
then
        echo "Connection Failed..."
        exit
else
        echo "Connection success."
        sleep 2
	echo -e "\n"
	query1=`$mysql_conn -e "SELECT Table_Name FROM INFORMATION_SCHEMA.Tables WHERE Table_Schema = '"$main_db"' AND Table_Name = 'chart_vis_master';" $main_db`
	query2=`$mysql_conn -e "SELECT Table_Name FROM INFORMATION_SCHEMA.Tables WHERE Table_Schema = '"$main_db"' AND Table_Name = 'chart_lac_sys';" $main_db`
	query3=`$mysql_conn -e "SELECT Table_Name FROM INFORMATION_SCHEMA.Tables WHERE Table_Schema = '"$main_db"' AND Table_Name = 'chart_retinal_exam';" $main_db`
	query4=`$mysql_conn -e "SELECT Table_Name FROM INFORMATION_SCHEMA.Tables WHERE Table_Schema = '"$main_db"' AND Table_Name = 'chart_conjunctiva';" $main_db`

	if [ ! -z "$query1" ] && [ ! -z "$query2"  ] && [ ! -z "$query3"  ] && [ ! -z "$query4" ]
	then
		echo "Please wait. DataRetention Process is running..."
		${mysql_conn} $main_db < "${SCRIPTPATH}/DataRetention_SQLDELETE_new_PHI.sql"
		${mysql_conn} -e "DELETE S.*  FROM  "${scan_db}".scans S INNER JOIN TestId_ToDel T ON S.test_id = T.test_id AND S.image_form collate latin1_swedish_ci = T.test_name collate latin1_swedish_ci;" $main_db
		${mysql_conn} -e "DELETE S.* FROM "${scan_db}".scans S INNER JOIN ChartMasterId_ToDel T ON S.form_id = T.form_id;" $main_db
		${mysql_conn} -e "DROP TABLE IF EXISTS TestId_ToDel;" $main_db
		${mysql_conn} -e "DROP TABLE IF EXISTS ChartMasterId_ToDel;" $main_db
		echo "Done"

	elif [ -z "$query1" ] && [ -z "$query2" ] && [ -z "$query3" ] && [ -z "$query4" ]
	then
		echo "Please wait. DataRetention Process is running..."
		${mysql_conn} $main_db < "${SCRIPTPATH}/DataRetention_SQLDELETE_old_PHI.sql"
		${mysql_conn} -e "DELETE S.*  FROM  "${scan_db}".scans S INNER JOIN TestId_ToDel T ON S.test_id = T.test_id AND S.image_form collate latin1_swedish_ci = T.test_name collate latin1_swedish_ci;" $main_db
		${mysql_conn} -e "DELETE S.* FROM "${scan_db}".scans S INNER JOIN ChartMasterId_ToDel T ON S.form_id = T.form_id;" $main_db
                ${mysql_conn} -e "DROP TABLE IF EXISTS TestId_ToDel;" $main_db
		${mysql_conn} -e "DROP TABLE IF EXISTS ChartMasterId_ToDel;" $main_db
		echo "Done"

	elif [ -z "$query1" ] || [ -z "$query2" ] || [ -z "$query3" ] || [ -z "$query4" ]
	then
		echo "Failed... Something is wrong inside Main Database."
		exit
	fi
fi
