<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql="ALTER TABLE `iolink_scan_consent` ADD `copy_from_scan_consent_id` INT( 11 ) NOT NULL";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `patient_in_waiting_tbl` ADD `iAscReSyncroStatus` VARCHAR( 100 ) NOT NULL";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `patient_in_waiting_tbl` ADD `reSyncroVia` VARCHAR( 255 ) NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Updates 8 run OK";

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







