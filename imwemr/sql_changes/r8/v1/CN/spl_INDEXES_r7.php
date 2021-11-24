<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

$sql="SHOW INDEX FROM chart_master_table WHERE key_name = 'finalize' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_master_table` ADD INDEX(`finalize`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_records_archive_binary WHERE key_name = 'chart_rec_arc_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_records_archive_binary` ADD INDEX(`chart_rec_arc_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM icg WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `icg` ADD INDEX(`form_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_data WHERE key_name = 'providerID' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_data` ADD INDEX(`providerID`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM superbill WHERE key_name = 'dateOfService' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `superbill` ADD INDEX(`dateOfService`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM memo_tbl WHERE key_name = 'patient_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `memo_tbl` ADD INDEX (  `patient_id` )";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Release 6: Update Index run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Release 6: Update Index run successfully </b>";
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