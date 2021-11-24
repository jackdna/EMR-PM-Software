<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql = "ALTER TABLE `surgical_check_list` DROP `check_list_nurse_id`";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `surgical_check_list` 
		ADD `procedure_check_in_nurse_id` INT( 11 ) NOT NULL ,
		ADD `sign_in_nurse_id` INT( 11 ) NOT NULL AFTER `procedure_check_in_nurse_id` ,
		ADD `time_out_nurse_id` INT( 11 ) NOT NULL AFTER `sign_in_nurse_id` ,
		ADD `sign_out_nurse_id` INT( 11 ) NOT NULL AFTER `time_out_nurse_id`";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 10 run OK";

?>

<html>
<head>
<title>Mysql Updates For Create Table in surgical_check_list</title>
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







