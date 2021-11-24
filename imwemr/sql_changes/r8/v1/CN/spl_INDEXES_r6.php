<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

$sql="SHOW INDEX FROM patient_consent_form_information WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_consent_form_information` ADD INDEX ( `patient_id` ) ;	";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM posted_record WHERE key_name = 'encounter_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `posted_record` ADD INDEX ( `encounter_id` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM current_time_locator WHERE key_name = 'uid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `current_time_locator` ADD INDEX ( `uid` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_chargesheet_payment_info WHERE key_name = 'operatorId' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_chargesheet_payment_info` ADD INDEX ( `operatorId` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM era_835_patient_details WHERE key_name = 'CLP_claim_submitter_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `era_835_patient_details` ADD INDEX ( `CLP_claim_submitter_id` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_charge_list WHERE key_name = 'gro_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_charge_list` ADD INDEX ( `gro_id` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Release 6: Update 22 run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Release 6: Update 22 run successfully </b>";
	$color = "green";
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Update 22</title>
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