#!/bin/bash
db_host=localhost
db_user=imwadmin
db_pass=my_pass
db_name=testdb
export MYSQL_PWD=${db_pass}
mysql_conn="mysql -h ${db_host} --user=${db_user}"
echo "Checking MySQL connection..."
${mysql_conn} -e exit 2>/dev/null
dbstatus=`echo $?`


if [ $dbstatus -ne 0 ]; then
        echo "Connection Failed..."
        exit
else
        echo "Connection success."
        sleep 2
        table_list=`${mysql_conn} -e "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '"$db_name"' AND ENGINE = 'MyISAM' AND engine IS NOT NULL AND Table_Name NOT IN ('chart_la','chart_rv','chart_slit_lamp_exam','chart_vision')" | sed '1d'`
        if [ -z "$table_list" ]
        then
                echo "No MyISAM table found in database"
                exit
        fi
        for tbl_name in $table_list
                do
                        echo Converting Table: $tbl_name
                        $mysql_conn -e "ALTER TABLE $tbl_name ENGINE = InnoDB;" $db_name
                done
fi
