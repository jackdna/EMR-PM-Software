<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

$sql="SHOW INDEX FROM provider_view_log_tbl WHERE key_name = 'scan_doc_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `provider_view_log_tbl` ADD INDEX ( `scan_doc_id` ) ;	";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_assessment_plans WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_assessment_plans` ADD INDEX ( `form_id` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_tests WHERE key_name = 'facility' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_tests` ADD INDEX ( `facility` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM users WHERE key_name = 'username' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `users` ADD INDEX ( `username` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM previous_statement WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `previous_statement` ADD INDEX ( `patient_id` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM superbill WHERE key_name = 'patientId' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `superbill` ADD INDEX ( `patientId` ) ;";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_previous_data WHERE key_name = 'patientId' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_previous_data` ADD INDEX(`patient_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM contactlensmaster WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `contactlensmaster` ADD INDEX ( `form_id` )";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

/*
$sql="";
$row = imw_query($sql)or $msg_info[] = imw_error();

$sql="";
$row = imw_query($sql)or $msg_info[] = imw_error();
*/


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