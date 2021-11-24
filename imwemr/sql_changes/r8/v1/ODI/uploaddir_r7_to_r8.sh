#!/bin/bash

#R7_site=/var/www/html/iMedicWareR7_old
#R8_site=/var/www/html/iMedicWareR8-Dev
#practice_name=aves

echo "Uploaddir rsync started at: `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
#rsync -avzh --progress --remove-source-files --exclude='.*svn' $R7_site/interface/main/uploaddir/ $R8_site/data/$practice_name/ >> $R8_site/sql_changes/r8/v1/ODI/logs/uploaddir_$practice_name.log

if [ $? -eq 0 ]; then
  echo "Uploaddir rsync successfully completed `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
  echo -e "\n" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
else
  echo "Error in rsync process `date`" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
  echo -e "\n" >> $R8_site/sql_changes/r8/v1/ODI/logs/migrate_$practice_name.log
fi
