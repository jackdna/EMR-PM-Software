<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "ALTER TABLE `operatingroomrecords` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `operativereport` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `patient_allergies_tbl` ADD INDEX `patient_confirmation_id` (`patient_confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `preopnursingrecord` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `preopphysicianorders` ADD INDEX `patient_confirmation_id` (`patient_confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `laser_procedure_patient_table` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `dischargesummarysheet` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `localanesthesiarecord` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `left_navigation_forms` ADD INDEX `confirmationId` (`confirmationId`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `chartnotes_change_audit_tbl` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `stub_tbl` ADD INDEX `patient_confirmation_id` (`patient_confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `patientconfirmation` ADD INDEX `patientConfirmationId` (`patientConfirmationId`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `patientconfirmation` ADD INDEX `dos` (`dos`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `patientconfirmation` ADD INDEX `patientId` (`patientId`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `preophealthquestionnaire` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `postopnursingrecord` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `postopphysicianorders` ADD INDEX `patient_confirmation_id` (`patient_confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `preopgenanesthesiarecord` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `genanesthesiarecord` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `genanesthesianursesnotes` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `patient_anesthesia_medication_tbl` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `patient_instruction_sheet` ADD INDEX `patient_confirmation_id` (`patient_confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();


//line 4200


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 12 run OK";

?>

<html>
<head>
<title>Mysql Updates For Query Optimization</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







