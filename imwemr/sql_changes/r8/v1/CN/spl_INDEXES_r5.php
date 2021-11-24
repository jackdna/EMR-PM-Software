<?php
//--- UPDATE CREATED BY ---
$ignoreAuth = true;
include("../../../../config/globals.php");


$msg_info=array();   

$sql="SHOW INDEX FROM `amsler_grid` WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `amsler_grid` ADD INDEX( `patient_id`);";
}

$sql="SHOW INDEX FROM chart_assessment_plans WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_assessment_plans` ADD INDEX( `patient_id`);";
}

$sql="SHOW INDEX FROM chart_correction_values WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_correction_values` ADD INDEX( `patient_id`);";
}

$sql="SHOW INDEX FROM chart_cvf WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_cvf` ADD INDEX( `patientId`);";
}

$sql="SHOW INDEX FROM chart_dialation WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_dialation` ADD INDEX( `patient_id`);";
}

$sql="SHOW INDEX FROM chart_diplopia WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_diplopia` ADD INDEX (`patientId`) ;";
}

$sql="SHOW INDEX FROM chart_eom WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_eom` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_external_exam WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_external_exam` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_gonio WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_gonio` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_iop WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_iop` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_la WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_la` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_left_cc_history WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_left_cc_history` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_left_provider_issue WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_left_provider_issue` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_master_table WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_master_table` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_ood WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_ood` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_optic WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_optic` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_ptpastdiagnosis WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_ptpastdiagnosis` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_pt_lock WHERE key_name = 'pt_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_pt_lock` ADD INDEX (`pt_id`) ;";
}

$sql="SHOW INDEX FROM chart_pupil WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_pupil` ADD INDEX (`patientId`) ;";
}

$sql="SHOW INDEX FROM chart_records_archive WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_records_archive` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_ref_surgery WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_ref_surgery` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_rv WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_rv` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM chart_slit_lamp_exam WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_slit_lamp_exam` ADD INDEX (`patient_id`) ;";
}

$sql="SHOW INDEX FROM amsler_grid WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `amsler_grid` ADD INDEX( `form_id`);";
}

$sql="SHOW INDEX FROM chart_correction_values WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_correction_values` ADD INDEX( `form_id`);";
}

$sql="SHOW INDEX FROM chart_diplopia WHERE key_name = 'formId' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_diplopia` ADD INDEX (`formId`) ;";
}

$sql="SHOW INDEX FROM chart_master_table WHERE key_name = 'encounterId' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_master_table` ADD INDEX (`encounterId`) ;";
}

$sql="SHOW INDEX FROM chart_records_archive WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_records_archive` ADD INDEX (`form_id`) ;";
}

$sql="SHOW INDEX FROM chart_smart_chart WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql1[]="ALTER TABLE `chart_smart_chart` ADD INDEX (`form_id`) ;";
}

foreach($sql1 as $key=>$val){

$result = imw_query($val) or $msg_info[] = imw_error();
echo "<br/>".$val;

}

$msg_info[] = "<br><br><b>Update add index :: Update run successfully!";
$color = "green";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update add index</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(implode("<br>",$msg_info));?>
</font>
</body>
</html>