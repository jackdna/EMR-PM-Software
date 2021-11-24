<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$q = array();
$q = "ALTER TABLE `superbill_test` ADD `tests_name_pkid` INT NOT NULL DEFAULT '0' COMMENT 'pkid of table tests_name'";

imw_query($q) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 22 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 22 completed successfully. </b>";
	$color = "green";
}



?>
<html>
<head>
<title>Update 22</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>