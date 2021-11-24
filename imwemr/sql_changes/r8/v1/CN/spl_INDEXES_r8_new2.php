<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

$sql="SHOW INDEX FROM superbill WHERE key_name = 'superbill_dos' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX superbill_dos ON  `superbill` (`dateOfService`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM send_fax_log_tbl WHERE key_name = 'sendfaxlogtbl_ptConsultid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX sendfaxlogtbl_ptConsultid ON send_fax_log_tbl(patient_consult_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM user_messages WHERE key_name = 'usermessages_msgid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX usermessages_msgid ON  `user_messages`(msg_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_monitor WHERE key_name = 'ptmonitor_actiondt' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ptmonitor_actiondt ON patient_monitor(action_date_time);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'reportencdetail_dos' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX reportencdetail_dos ON report_enc_detail(date_of_service);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_previous_data WHERE key_name = 'patientpreviousdata_prevphonehome' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientpreviousdata_prevphonehome ON patient_previous_data(prev_phone_home);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_previous_data WHERE key_name = 'patientpreviousdata_newphonehome' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientpreviousdata_newphonehome ON patient_previous_data(new_phone_home);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

/*$sql="SHOW INDEX FROM patient_previous_data WHERE key_name = 'patientpreviousdata_prevphonebiz' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientpreviousdata_prevphonebiz ON patient_previous_data(prev_phone_biz);";
$row = imw_query($sql)or $msg_info[] = imw_error();
}*/

$sql="SHOW INDEX FROM patient_previous_data WHERE key_name = 'patientpreviousdata_newphonebiz' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientpreviousdata_newphonebiz ON patient_previous_data(new_phone_biz);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_previous_data WHERE key_name = 'patientpreviousdata_prevphonecell' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientpreviousdata_prevphonecell ON patient_previous_data(prev_phone_cell);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_previous_data WHERE key_name = 'patientpreviousdata_newphonecell' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientpreviousdata_newphonecell ON patient_previous_data(new_phone_cell);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'reportenctrans_transdot' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX reportenctrans_transdot ON report_enc_trans(trans_dot);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'reportenctrans_transtype' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX reportenctrans_transtype ON report_enc_trans(trans_type);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_reff WHERE key_name = 'patientreff_insdataid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientreff_insdataid ON patient_reff(ins_data_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_auth WHERE key_name = 'patientauth_insdataid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientauth_insdataid ON patient_auth(ins_data_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_charge_list_details WHERE key_name = 'patientchargelistdetails_transdeldt' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientchargelistdetails_transdeldt ON patient_charge_list_details(trans_del_date);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM check_in_out_payment_post WHERE key_name = 'checkinoutpymntpost_ptid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX checkinoutpymntpost_ptid ON check_in_out_payment_post(patient_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_charge_list WHERE key_name = 'patientchargelist_multicol' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientchargelist_multicol ON patient_charge_list(first_posted_date,facility_id,gro_id,primary_provider_id_for_reports,first_posted_opr_id,date_of_service);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}


$sql="SHOW INDEX FROM patient_charges_detail_payment_info WHERE key_name = 'patientchargesdetailpaymentinfo_ptprepaymentid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patientchargesdetailpaymentinfo_ptprepaymentid ON patient_charges_detail_payment_info(patient_pre_payment_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="ALTER TABLE `patient_specialty_question_options_answer` ADD INDEX `patient_specialty_question_answer_id` (`patient_specialty_question_answer_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();

$sql = "ALTER TABLE `patient_specialty_question_answer` ADD INDEX `patient_id`(`patient_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();

$sql = "ALTER TABLE `patient_specialty_question_answer` ADD INDEX `question_id`(`question_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();

$sql = "ALTER TABLE `chart_gonio` ADD INDEX `form_id`(`form_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();



if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Release 8: Create Index run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Release 8: Create Index run successfully </b>";
	$color = "green";
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Create Indexes</title>
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