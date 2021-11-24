<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[]="ALTER TABLE `ccda_export_schedule`  ADD `ins_type` VARCHAR(255) NOT NULL,  ADD `ins_comp_id` TEXT NOT NULL;";
$sql[]="ALTER TABLE  chart_schedule_test_external ADD INDEX  patient_id (patient_id);";
$sql[]="ALTER TABLE  chart_schedule_test_external ADD INDEX  form_id (form_id);";
$sql[]="ALTER TABLE  chart_schedule_test_external ADD INDEX  deleted_by (deleted_by);";

$sql[]="ALTER TABLE  commonNoMedicalHistory ADD INDEX  module_name (module_name);";
$sql[]="ALTER TABLE  patient_goals ADD INDEX  patient_id (patient_id);";
$sql[]="ALTER TABLE  patient_goals ADD INDEX  form_id (form_id);";

$sql[]="ALTER TABLE  hc_observations ADD INDEX  pt_id (pt_id);";
$sql[]="ALTER TABLE  hc_observations ADD INDEX  form_id (form_id);";
$sql[]="ALTER TABLE  hc_observations ADD INDEX  del_status (del_status);";

$sql[]="ALTER TABLE  hc_concerns ADD INDEX  observation_id (observation_id);";
$sql[]="ALTER TABLE  hc_rel_observations ADD INDEX  pt_id (pt_id);";
$sql[]="ALTER TABLE  hc_rel_observations ADD INDEX  form_id (form_id);";
$sql[]="ALTER TABLE  hc_rel_observations ADD INDEX  del_status (del_status);";

$sql[]="ALTER TABLE  vital_sign_master ADD INDEX  patient_id (patient_id);";
$sql[]="ALTER TABLE  vital_sign_master ADD INDEX  date_vital (date_vital);";
$sql[]="ALTER TABLE  vital_sign_master ADD INDEX  status (status);";

$sql[]="ALTER TABLE  rad_test_data ADD INDEX  rad_status (rad_status);";
$sql[]="ALTER TABLE  rad_test_data ADD INDEX  rad_patient_id (rad_patient_id);";

$sql[]="ALTER TABLE  lab_test_data ADD INDEX  lab_status (lab_status);";
$sql[]="ALTER TABLE  lab_test_data ADD INDEX  lab_patient_id (lab_patient_id);";
$sql[]="ALTER TABLE  lab_observation_requested ADD INDEX  lab_test_id (lab_test_id);";
$sql[]="ALTER TABLE  lab_observation_result ADD INDEX  lab_test_id (lab_test_id);";

$sql[]="ALTER TABLE  document_patient_rel ADD INDEX  p_id (p_id);";
$sql[]="ALTER TABLE  document_patient_rel ADD INDEX  form_id (form_id);";
$sql[]="ALTER TABLE  document_patient_rel ADD INDEX  status (status);";

$sql[]="ALTER TABLE  order_set_associate_chart_notes_details ADD INDEX  delete_status (delete_status);";
$sql[]="ALTER TABLE  order_set_associate_chart_notes ADD INDEX  delete_status (delete_status);";

$sql[]="ALTER TABLE  patient_health_status ADD INDEX  del_status (del_status);";
$sql[]="ALTER TABLE  patient_health_status ADD INDEX  patient_id (patient_id);";
$sql[]="ALTER TABLE  patient_health_status ADD INDEX  form_id (form_id);";
$sql[]="ALTER TABLE  patient_health_status ADD INDEX  status_type (status_type);";

$sql[]="ALTER TABLE  lists ADD INDEX  type (type);";
$sql[]="ALTER TABLE  lists ADD INDEX  allergy_status (allergy_status);";

$sql[]="ALTER TABLE  immunizations ADD INDEX  administered_date (administered_date);";

foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 8 Failed!</b>";
	$color = "red";
}
else{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 8 successfull</b>";
	$color = "green";

}
?>
<html>
<head>
<title>Release 8 Updates 8 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>