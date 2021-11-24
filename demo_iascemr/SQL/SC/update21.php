<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
ALTER TABLE `localanesthesiarecord` ADD `anes_ScanUploadName` VARCHAR( 255 ) NOT NULL ,
ADD `anes_ScanUploadPath` VARCHAR( 255 ) NOT NULL ,
ADD `anes_ScanUploadDateTime` DATETIME NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `localanesthesiarecord` CHANGE `anes_ScanStatus` `anes_ScanStatus` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 21 run OK";

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







