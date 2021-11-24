<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
ALTER TABLE `stub_tbl` ADD `patient_tertiary_procedure` VARCHAR( 255 ) NOT NULL AFTER `patient_secondary_procedure` ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `patientconfirmation` ADD `patient_tertiary_procedure_id` VARCHAR( 255 ) NOT NULL AFTER `patient_secondary_procedure_id`  ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `patientconfirmation` ADD `patient_tertiary_procedure` VARCHAR( 255 ) NOT NULL AFTER `patient_secondary_procedure` ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `patient_in_waiting_tbl` ADD `patient_tertiary_procedure` VARCHAR( 255 ) NOT NULL AFTER `patient_secondary_procedure` ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 23 run OK";

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







