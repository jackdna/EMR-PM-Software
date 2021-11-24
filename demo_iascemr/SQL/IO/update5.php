<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql="ALTER TABLE `insurance_data` ADD INDEX `patient_id` ( `patient_id` ) ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `iolink_scan_consent` ADD INDEX `waiting_id_folder_name` ( `patient_in_waiting_id` , `iolink_scan_folder_name` ) ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `insurance_data` ADD `inCompleteInsStatus` VARCHAR( 5 ) NOT NULL COMMENT 'Check for imwemr syncronization'";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Updates 5 run OK";

?>

<html>
<head>
<title>Mysql Updates After Launch</title>
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







