#!/bin/bash

#R7_site=/var/www/html/iMedicWareR7_old
#R8_site=/var/www/html/iMedicWareR8-Dev
#practice_name=aves

base_src_path=$R7_site/interface/common/new_html2pdf/bar_code_images
base_dst_path=$R8_site/data/$practice_name

cmd=`ls -ld $base_src_path/patient_id_* | awk  '{print $9 }'`

for list in $cmd
do
	id=`echo $list | sed -r 's/^.*patient_id_(.*)/\1/'`
	
	if [ ! -d "$base_dst_path/PatientId_$id/consent_forms/bar_code_images/" ]; then
		mkdir --mode=777 -p $base_dst_path/PatientId_$id/consent_forms/bar_code_images/
	fi
	rsync -avzh --progress --exclude='.*svn' $base_src_path/patient_id_$id/ $base_dst_path/PatientId_$id/consent_forms/bar_code_images
done

echo "Running SQL Path replacement updates: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name providers
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name doc_logos
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name resp_party
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name surgery_consent_filled_form
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name surgery_consent_form_signature
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name surgery_center_pre_op_health_ques
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name patient_consult_letter_tbl
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name pt_consent_form_info
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name consent_form_signature
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name previous_statement
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name pt_docs_template
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name pt_docs_patient_templates
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name consent_form
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name consulttemplate
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name pn_reports
/usr/bin/php $R8_site/sql_changes/r8/v1/ODI/copy_images.php $practice_name upload_lab_rad_data

if [ $? -eq 0 ]; then
  echo "SQL Path replacement updates completed successfully: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
else
  echo "Error in sql updates: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
fi

echo "Running Data-files rsync process: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
mkdir -p $R8_site/data/$practice_name/redactor/images/
rsync -avzh --progress --exclude='.*svn' $R7_site/interface/main/uploaddir/drawicon/ $R8_site/data/$practice_name/drawicon/
rsync -avzh --progress --exclude='.*svn' $R7_site/interface/admin/iportal/preferred_images/ $R8_site/data/$practice_name/preferred_images/
rsync -avzh --progress --exclude='.*svn' $R7_site/interface/admin/console/alert/upload/ $R8_site/data/$practice_name/site_care_plan/
rsync -avzh --progress --exclude='.*svn' $R7_site/interface/main/uploaddir/document_logos/ $R8_site/data/$practice_name/gn_images/
rsync -avzh --progress --exclude='.*svn' $R7_site/images/facilitylogo/ $R8_site/data/$practice_name/facilitylogo/
rsync -avzh --progress --exclude='.*svn' $R7_site/redactor/images/ $R8_site/data/$practice_name/redactor/images/
rsync -avzh --progress --exclude='.*svn' $R7_site/interface/BatchFiles/ $R8_site/data/$practice_name/BatchFiles/
rsync -avzh --progress --exclude='.*svn' $R7_site/addons/iOLink/ $R8_site/data/$practice_name/iOLink/
rsync -avzh --progress --exclude='.*svn' $R7_site/interface/main/html2pdfprint/ $R8_site/data/$practice_name/html2pdfprint/

if [ $? -eq 0 ]; then
  echo "Data-files rsync completed successfully: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
  echo -e "\n" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
else
  echo "Error in rsync: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
  echo -e "\n" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
fi
