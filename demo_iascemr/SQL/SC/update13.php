<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "ALTER TABLE `msg_tbl` ADD INDEX `msg_user_id` (`msg_user_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `consent_forms_template` ADD INDEX `consent_category_id` (`consent_category_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `stub_tbl` ADD INDEX `nextGenPersonId` (`nextGenPersonId`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `surgical_check_list` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `vitalsign_tbl` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `scan_upload_tbl` ADD INDEX `confirmation_id` (`confirmation_id`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `scan_upload_tbl` ADD INDEX `dosOfScan` (`dosOfScan`);";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 13 run OK";

//line 12323
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







