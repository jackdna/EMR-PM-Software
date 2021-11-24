#!/bin/bash

R7_site=D:/imedic/apache/htdocs/iMedicR6-QA
R8_site=D:/imedic/apache/htdocs/iMedicWare_R8
R7_rsync_path=/cygdrive/d/imedic/apache/htdocs/iMedicR6-QA
R8_rysnc_path=/cygdrive/d/imedic/apache/htdocs/iMedicWare_R8
env_path=D:/imedic/apache/htdocs/iMedicWare_R8/sql_changes/r8/v1/ODI/shell_for_windows
practice_name=imedicware_r8
rsync_bin=$env_path/rsync/rsync.exe

base_src_path=$R7_site/interface/common/new_html2pdf/bar_code_images
base_dst_path=$R8_site/data/$practice_name
base_src_rsync_path=$R7_rsync_path/interface/common/new_html2pdf/bar_code_images
base_dst_rsync_path=$R8_rysnc_path/data/$practice_name

cmd=`ls -ld $base_src_path/patient_id_* | awk  '{print $9 }'`

for list in $cmd
do
	id=`echo $list | sed -r 's/^.*patient_id_(.*)/\1/'`
	
	if [ ! -d "$base_dst_path/PatientId_$id/consent_forms/bar_code_images/" ]; then
		mkdir --mode=777 -p $base_dst_path/PatientId_$id/consent_forms/bar_code_images/
	fi
	$rsync_bin -avzh --progress --exclude='.*svn' $base_src_rsync_path/patient_id_$id/ $base_dst_rsync_path/PatientId_$id/consent_forms/bar_code_images
done

echo "Running Data-files rsync process: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
mkdir -p $R8_site/data/$practice_name/redactor/images/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/interface/main/uploaddir/drawicon/ $R8_rysnc_path/data/$practice_name/drawicon/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/interface/admin/iportal/preferred_images/ $R8_rysnc_path/data/$practice_name/preferred_images/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/interface/admin/console/alert/upload/ $R8_rysnc_path/data/$practice_name/site_care_plan/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/interface/main/uploaddir/document_logos/ $R8_rysnc_path/data/$practice_name/gn_images/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/images/facilitylogo/ $R8_rysnc_path/data/$practice_name/facilitylogo/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/redactor/images/ $R8_rysnc_path/data/$practice_name/redactor/images/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/interface/BatchFiles/ $R8_rysnc_path/data/$practice_name/BatchFiles/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/addons/iOLink/ $R8_rysnc_path/data/$practice_name/iOLink/
$rsync_bin -avzh --progress --exclude='.*svn' $R7_rsync_path/interface/main/html2pdfprint/ $R8_rysnc_path/data/$practice_name/html2pdfprint/

if [ $? -eq 0 ]; then
  echo "Data-files rsync completed successfully: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
  echo -e "\n" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
else
  echo "Error in rsync: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
  echo -e "\n" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
fi