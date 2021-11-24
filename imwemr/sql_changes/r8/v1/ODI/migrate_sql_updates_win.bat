@ECHO OFF

::set php_path=D:\iMedic\php\php.exe
::set R8_site=D:\iMedic\apache\htdocs\iMedicWareR8-Dev
::set practice_name=demo

%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% providers
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% doc_logos
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% resp_party
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% surgery_consent_filled_form
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% surgery_consent_form_signature
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% surgery_center_pre_op_health_ques
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% patient_consult_letter_tbl
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% pt_consent_form_info
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% consent_form_signature
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% previous_statement
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% pt_docs_template
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% pt_docs_patient_templates
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% consent_form
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% consulttemplate
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% pn_reports
%php_path% %R8_site%\sql_changes\r8\v1\ODI\copy_images.php %practice_name% upload_lab_rad_data