<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../connect_imwemr.php");
// update to add fields to store Start/Stop time in superbill table for anesthesia type bill
$msg_info = array();
$sql = array();
$sql[] = " ALTER TABLE `superbill` ADD `anes_start_time` TIME NOT NULL , ADD `anes_stop_time` TIME NOT NULL;";

foreach($sql as $qry){
	imw_query($qry) or $msg_info[] = imw_error().$qry;
}

include("../../common/conDb.php");
$sql = array();
$sql[] = " UPDATE patient_in_waiting_tbl SET idoc_sch_athena_id = amd_visit_id WHERE amd_visit_id!='0' AND idoc_sch_athena_id = ''"; 
$sql[] = " ALTER TABLE `preopnursequestionadmin` ADD INDEX confirmation_id (confirmation_id);";
$sql[] = " ALTER TABLE `patientconfirmation` ADD INDEX finalize_status (finalize_status);";
$sql[] = " ALTER TABLE `insurance_data` ADD INDEX waiting_id(waiting_id);";
$sql[] = " ALTER TABLE `patient_in_waiting_tbl` ADD INDEX patient_id(patient_id), ADD INDEX iasc_facility_id(iasc_facility_id), ADD INDEX amd_visit_id(amd_visit_id), ADD INDEX patient_status(patient_status);";
$sql[] = " ALTER TABLE `patient_data_tbl` ADD INDEX patient_fname(patient_fname), ADD INDEX patient_lname(patient_lname), ADD INDEX amd_patient_id(amd_patient_id);";
$sql[] = " ALTER TABLE `finalize_history` ADD INDEX patient_confirmation_id(patient_confirmation_id), ADD INDEX finalize_action(finalize_action), ADD INDEX finalize_action_datetime(finalize_action_datetime);";
$sql[] = " ALTER TABLE `stub_tbl` ADD INDEX iolink_patient_in_waiting_id(iolink_patient_in_waiting_id);";
$sql[] = " ALTER TABLE `amd_charges_log` ADD INDEX m_amd_visit_id(m_amd_visit_id), ADD INDEX amd_visit_id(amd_visit_id), ADD INDEX date_posted(date_posted), ADD INDEX visit_type(type);";
$sql[] = " ALTER TABLE `scan_upload_tbl` ADD INDEX iolink_scan_consent_id(iolink_scan_consent_id);";
$sql[] = " ALTER TABLE `iolink_scan_consent`  ADD INDEX idoc_consent_template_id(idoc_consent_template_id), ADD INDEX patient_in_waiting_id(patient_in_waiting_id);";

foreach($sql as $qry){
	imw_query($qry) or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 177 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 177 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 177</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
imw_close();
}
?> 
</body>
</html>