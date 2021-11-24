<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

$sql="SHOW INDEX FROM user_messages WHERE key_name = 'message_to' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `user_messages` ADD INDEX(`message_to`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM user_messages WHERE key_name = 'patientId' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `user_messages` ADD INDEX(`patientId`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_monitor WHERE key_name = 'user_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_monitor` ADD INDEX(`user_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_monitor WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_monitor` ADD INDEX(`patient_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM vital_sign_patient WHERE key_name = 'vital_sign_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `vital_sign_patient` ADD INDEX(`vital_sign_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM vital_sign_patient WHERE key_name = 'vital_master_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `vital_sign_patient` ADD INDEX(`vital_master_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}
/*
$sql="SHOW INDEX FROM schedule_appointments WHERE key_name = 'sa_test_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `schedule_appointments` ADD INDEX (  `sa_test_id` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
}

$sql="SHOW INDEX FROM schedule_appointments WHERE key_name = 'sa_facility_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `schedule_appointments` ADD INDEX (  `sa_facility_id` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
}
*/
$sql="SHOW INDEX FROM schedule_appointments WHERE key_name = 'sa_doctor_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `schedule_appointments` ADD INDEX (  `sa_doctor_id` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl WHERE key_name = 'folder_categories_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl ADD INDEX (  `folder_categories_id` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl WHERE key_name = 'task_status' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl ADD INDEX (  `task_status` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_data WHERE key_name = 'ss' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `patient_data` ADD INDEX (  `ss` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_data WHERE key_name = 'DOB' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `patient_data` ADD INDEX (  `DOB` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_data WHERE key_name = 'patientStatus' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `patient_data` ADD INDEX (  `patientStatus` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM insurance_data WHERE key_name = 'effective_date' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `insurance_data` ADD INDEX (  `effective_date` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM in_lens_design WHERE key_name = 'lens_vw_code' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `in_lens_design` ADD INDEX (  `lens_vw_code` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

//Feb 3,2018


$sql="SHOW INDEX FROM previous_status WHERE key_name = 'status_date' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `previous_status` ADD INDEX(`status_date`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_procedures WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_procedures` ADD INDEX(`form_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_procedures WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_procedures` ADD INDEX(`patient_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM scheduler_custom_labels WHERE key_name = 'start_date' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `scheduler_custom_labels` ADD INDEX(`start_date`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM scheduler_custom_labels WHERE key_name = 'facility' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `scheduler_custom_labels` ADD INDEX(`facility`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM scheduler_custom_labels WHERE key_name = 'provider' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `scheduler_custom_labels` ADD INDEX(`provider`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_master_table WHERE key_name = 'encounterId' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_master_table` ADD INDEX(`encounterId`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM upload_lab_rad_data WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `upload_lab_rad_data` ADD INDEX(`patient_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM upload_lab_rad_data WHERE key_name = 'uplaod_primary_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `upload_lab_rad_data` ADD INDEX(`uplaod_primary_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_detail` ADD INDEX(`patient_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'date_of_service' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_detail` ADD INDEX(`date_of_service`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM resp_party WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `resp_party` ADD INDEX(`patient_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM users WHERE key_name = 'user_type' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `users` ADD INDEX(`user_type`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM icd10_data WHERE key_name = 'icd9' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `icd10_data` ADD INDEX(`icd9`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_location WHERE key_name = 'cur_date' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_location` ADD INDEX(`cur_date`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'trans_del_operator_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_trans` ADD INDEX(`trans_del_operator_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_trans` ADD INDEX(`patient_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'charge_list_detail_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_detail` ADD INDEX(`charge_list_detail_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM insurance_data WHERE key_name = 'subscriber_relationship' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `insurance_data` ADD INDEX(`subscriber_relationship`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM insurance_data WHERE key_name = 'subscriber_sex' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `insurance_data` ADD INDEX(`subscriber_sex`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM real_time_medicare_eligibility WHERE key_name = 'ins_data_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `real_time_medicare_eligibility` ADD INDEX(`ins_data_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM audit_trail WHERE key_name = 'pid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `audit_trail` ADD INDEX(`pid`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'encounter_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_detail` ADD INDEX(`encounter_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM paymentswriteoff WHERE key_name = 'charge_list_detail_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `paymentswriteoff` ADD INDEX(`charge_list_detail_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM account_trans WHERE key_name = 'parent_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `account_trans` ADD INDEX(`parent_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

/* MARCH 10 */

$sql="SHOW INDEX FROM patient_data WHERE key_name = 'username' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_data` ADD INDEX(`username`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}


$sql="SHOW INDEX FROM real_time_medicare_eligibility WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `real_time_medicare_eligibility` ADD INDEX (  `patient_id` )";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}


$sql="SHOW INDEX FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl WHERE key_name = 'sch_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE ".constant("IMEDIC_SCAN_DB").".`scan_doc_tbl` ADD INDEX(`sch_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_charge_list_details WHERE key_name = 'report_date_timestamp' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_charge_list_details` ADD INDEX(`report_date_timestamp`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_consent_form_information WHERE key_name = 'chart_procedure_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_consent_form_information` ADD INDEX(`chart_procedure_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM electronicfiles_tbl WHERE key_name = 'post_status' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `electronicfiles_tbl` ADD INDEX(`post_status`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_charge_list WHERE key_name = 'date_of_service' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_charge_list` ADD INDEX(`date_of_service`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM submited_record WHERE key_name = 'encounter_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `submited_record` ADD INDEX(`encounter_id`)";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM paymentswriteoff WHERE key_name = 'delStatus' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `paymentswriteoff` ADD INDEX(`delStatus`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM paymentswriteoff WHERE key_name = 'paymentStatus' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `paymentswriteoff` ADD INDEX(`paymentStatus`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

/* MARCH 10 */

$sql="SHOW INDEX FROM emdeon_reports WHERE key_name = 'report_status' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `emdeon_reports` ADD INDEX(`report_status`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM groups_new WHERE key_name = 'user_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `groups_new` ADD INDEX(`user_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

/*MARCH 23*/

$sql="SHOW INDEX FROM hl7_sent WHERE key_name = 'send_to' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `hl7_sent` ADD INDEX(`send_to`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM pt_problem_list_log WHERE key_name = 'pt_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `pt_problem_list_log` ADD INDEX(`pt_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM audit_trail WHERE key_name = 'Operater_Id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `audit_trail` ADD INDEX(`Operater_Id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}


/*
$sql="SHOW INDEX FROM x WHERE key_name = 'x' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `x` ADD INDEX(`x`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
}
*/


//"?

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Release 8: Update Index run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Release 8: Update Index run successfully </b>";
	$color = "green";
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Update Indexes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color_sts;?>" size="2">
    <?php echo(@implode("<br>",$msg_info_sts));?>
</font>
</body>
</html>