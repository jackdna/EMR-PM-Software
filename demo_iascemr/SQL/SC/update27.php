<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
ALTER TABLE `scan_documents`  ADD `stub_id` INT(11) NOT NULL,  ADD INDEX (stub_id) ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `scan_upload_tbl`  ADD `stub_id` INT(11) NOT NULL,  ADD INDEX (stub_id) ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `eposted`  ADD `stub_id` INT(11) NOT NULL,  ADD INDEX (stub_id) ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `scan_log_tbl`  ADD `stub_id` INT(11) NOT NULL,  ADD INDEX (stub_id) ";
$row = imw_query($sql) or $msg_info[] = imw_error();

if(constant("ARCHIVE_SCAN_DB")) {
	$sql = "ALTER TABLE ".constant("ARCHIVE_SCAN_DB").".scan_upload_tbl   ADD `stub_id` INT(11) NOT NULL,  ADD INDEX (stub_id) ";
	$row = imw_query($sql) or $msg_info[] = imw_error();
	
}





$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 27 run OK";

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







