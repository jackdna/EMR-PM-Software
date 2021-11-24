<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

$sql="SHOW INDEX FROM schedule_appointments WHERE key_name = 'sa_multiplecol' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX sa_multiplecol ON schedule_appointments(sa_facility_id,sa_doctor_id,sa_test_id,sa_patient_app_status_id,sa_patient_app_show,sa_app_start_date,sa_app_end_date);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM audit_trail WHERE key_name = 'audittrail_DateTime' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX audittrail_DateTime ON audit_trail(Date_Time);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM pt_docs_patient_templates WHERE key_name = 'ptdocspatienttemp_ptdocprimtempeid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ptdocspatienttemp_ptdocprimtempeid ON pt_docs_patient_templates (pt_doc_primary_template_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM pt_docs_patient_templates WHERE key_name = 'ptdocspatienttemp_pid_Delstat' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ptdocspatienttemp_pid_Delstat ON pt_docs_patient_templates (patient_id,delete_status);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM pt_problem_list_log WHERE key_name = 'ptproblemlistlog_ptid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ptproblemlistlog_ptid ON pt_problem_list_log(pt_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM consent_form_signature WHERE key_name = 'consentformsign_patid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX consentformsign_patid ON consent_form_signature (patient_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_charge_list WHERE key_name = 'patchargelist_dos' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patchargelist_dos ON patient_charge_list(date_of_service);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

/*$sql="SHOW INDEX FROM recent_users WHERE key_name = 'recentusers_provid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX recentusers_provid ON recent_users(provider_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
}*/

$sql="SHOW INDEX FROM pt_problem_list_log WHERE key_name = 'ptproblemlistlog_probid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ptproblemlistlog_probid ON pt_problem_list_log(problem_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_assessment_plans WHERE key_name = 'chartasmtplans_patid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX chartasmtplans_patid ON chart_assessment_plans (patient_id); ";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM pt_docs_patient_templates WHERE key_name = 'ptdocspatienttemp_pid_Delstat' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ptdocspatienttemp_pid_Delstat ON pt_docs_patient_templates (patient_id,delete_status);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM emdeon_reports WHERE key_name = 'emdeon_reportstatus' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX emdeon_reportstatus ON emdeon_reports (report_status); ";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM groups_new WHERE key_name = 'groupsnew_userid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX groupsnew_userid ON groups_new (user_id); ";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_gonio WHERE key_name = 'chartgonio_patid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX chartgonio_patid ON chart_gonio(patient_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM account_trans WHERE key_name = 'actrans_parentid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX actrans_parentid ON account_trans(parent_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_charges_detail_payment_info WHERE key_name = 'patchargesdetailpayinfo_delDate' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patchargesdetailpayinfo_delDate ON patient_charges_detail_payment_info(deleteDate);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_consent_form_information WHERE key_name = 'patconsentforminfo_chartprocid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patconsentforminfo_chartprocid ON patient_consent_form_information(chart_procedure_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM alert_tbl WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patient_id ON alert_tbl(patient_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM ophtha WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX form_id ON ophtha(form_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_app_recall WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patient_id ON patient_app_recall(patient_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM scheduler_custom_labels WHERE key_name = 'l_type' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX l_type ON scheduler_custom_labels(l_type);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM scheduler_custom_labels WHERE key_name = 'start_time' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX start_time ON scheduler_custom_labels(start_time);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM schedule_appointments WHERE key_name = 'sa_app_starttime' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX sa_app_starttime ON schedule_appointments(sa_app_starttime);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM slot_procedures WHERE key_name = 'acronym' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX acronym ON slot_procedures(acronym);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM slot_procedures WHERE key_name = 'doctor_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX doctor_id ON slot_procedures(doctor_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM slot_procedures WHERE key_name = 'proc' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX proc ON slot_procedures(proc);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM schedule_appointments WHERE key_name = 'sa_casetypeid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX sa_casetypeid ON  schedule_appointments(case_type_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_charge_list WHERE key_name = 'patchargelist_schappid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX patchargelist_schappid ON patient_charge_list(sch_app_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM paymentswriteoff WHERE key_name = 'paymentswriteoff_chargelistdetailid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX paymentswriteoff_chargelistdetailid ON paymentswriteoff(charge_list_detail_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}
/*
$sql="SHOW INDEX FROM iolink_consent_form_signature WHERE key_name = 'ioconsentformsign_pinwaitid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ioconsentformsign_pinwaitid ON iolink_consent_form_signature(patient_in_waiting_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
}
*/
$sql="SHOW INDEX FROM user_messages WHERE key_name = 'usermsg_multicol' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX usermsg_multicol ON user_messages(message_status,receiver_delete,message_to,message_sender_id,delivery_date,Pt_Communication,user_message_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM `lists` WHERE key_name = 'lists_pid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX lists_pid ON `lists`(parent_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_rv WHERE key_name = 'chartrv_pid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX chartrv_pid ON chart_rv(patient_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}
/*
$sql="SHOW INDEX FROM iolink_consent_filled_form WHERE key_name = 'iolinkconsentfilledform_fldPatientWaitingId' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX iolinkconsentfilledform_fldPatientWaitingId ON iolink_consent_filled_form(fldPatientWaitingId);";
$row = imw_query($sql)or $msg_info[] = imw_error();
}
*/

$sql="SHOW INDEX FROM previous_status WHERE key_name = 'prevstatus_statusdt' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX prevstatus_statusdt ON previous_status(status_date);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM submited_record WHERE key_name = 'subrec_encounterid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX subrec_encounterid ON submited_record(encounter_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM cpt_fee_table WHERE key_name = 'cptfeetable_cptfeeid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX cptfeetable_cptfeeid ON cpt_fee_table(cpt_fee_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_chargesheet_payment_info WHERE key_name = 'ptchargesheetpayinfo_dtofpay' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ptchargesheetpayinfo_dtofpay ON patient_chargesheet_payment_info(date_of_payment);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_chargesheet_payment_info WHERE key_name = 'ptchrgeshtpayinfo_transdt' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX ptchrgeshtpayinfo_transdt ON patient_chargesheet_payment_info(transaction_date);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}
/*
$sql="SHOW INDEX FROM scan_upload_tbl WHERE key_name = 'scanuploadtbl_iolinkscanconsentid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX scanuploadtbl_iolinkscanconsentid ON scan_upload_tbl(iolink_scan_consent_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
}
*/
$sql="SHOW INDEX FROM contactlensmaster WHERE key_name = 'contactlensmaster_formid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX contactlensmaster_formid ON contactlensmaster(form_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_optic WHERE key_name = 'chartoptic_pid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX chartoptic_pid ON chart_optic(patient_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_iop WHERE key_name = 'chartiop_pid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX chartiop_pid On chart_iop(patient_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'reprtenctrans_chargelistid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX reprtenctrans_chargelistid ON report_enc_trans(charge_list_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM insurance_data WHERE key_name = 'insurancedata_inscaseid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX insurancedata_inscaseid ON insurance_data(ins_caseid);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_master_table WHERE key_name = 'chartmastertbl_dos' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX chartmastertbl_dos ON chart_master_table(date_of_service);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'report_enc_trans_multicol' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX report_enc_trans_multicol ON report_enc_trans (parent_id,trans_type,master_tbl_id,report_trans_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'reportenctrans_chargelistdetailid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX reportenctrans_chargelistdetailid ON report_enc_trans(charge_list_detail_id);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM allergies_data WHERE key_name = 'allergiesdata_allergiename' ";
$row=sqlQuery($sql);
if($row == false){
$sql="CREATE INDEX allergiesdata_allergiename ON allergies_data(allergie_name);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM era_835_proc_posted WHERE key_name = 'era_835_proc_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `era_835_proc_posted` ADD INDEX ( `era_835_proc_id` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}
//"?

$sql="SHOW INDEX FROM emdeon_reports WHERE key_name = 'report_status' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `emdeon_reports` ADD INDEX ( `report_status` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'charge_list_detail_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_trans` ADD INDEX ( `charge_list_detail_id` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'trans_del_operator_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_trans` ADD INDEX ( `trans_del_operator_id` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'trans_del_date' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_trans` ADD INDEX ( `trans_del_date` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_trans WHERE key_name = 'master_tbl_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_trans` ADD INDEX ( `master_tbl_id` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'date_of_service' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_detail` ADD INDEX ( `date_of_service` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'gro_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_detail` ADD INDEX ( `gro_id` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM report_enc_detail WHERE key_name = 'first_posted_date' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `report_enc_detail` ADD INDEX ( `first_posted_date` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM batch_file_submitte WHERE key_name = 'Transaction_set_unique_control' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `batch_file_submitte` ADD INDEX ( `Transaction_set_unique_control` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}


$sql="SHOW INDEX FROM schedule_appointments WHERE key_name = 'iolink_iosync_waiting_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `schedule_appointments` ADD INDEX (  `iolink_iosync_waiting_id` ) ";
$row=sqlQuery($sql);
echo "<br/>".$sql;
}





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