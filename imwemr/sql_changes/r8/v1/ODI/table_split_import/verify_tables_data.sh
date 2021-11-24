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
        for tbl_name in chart_acuity chart_ak chart_ant_chamber chart_bat chart_blood_vessels chart_conjunctiva chart_cornea chart_drawings chart_exo chart_iris chart_lac_sys chart_lens chart_lesion chart_lid_pos chart_lids chart_macula chart_pam chart_periphery chart_retinal_exam chart_sca chart_vis_master chart_vitreous
                do
                        echo Number of records in Table: $tbl_name
                        $mysql_conn -e "SELECT COUNT(1) FROM $tbl_name;" $db_name
                        echo ""
                done
fi
