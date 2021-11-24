<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql4="ALTER TABLE ".$sqlconf['scan_db_name'].".`folder_categories` ADD INDEX  `folder_status` (  `folder_status` )";
imw_query($sql4) or $msg_info[] = imw_error();

$sql4="ALTER TABLE ".$sqlconf['scan_db_name'].".`folder_categories` ADD `favourite` TINYINT(2) NOT NULL AFTER `alertPhysician`;";
imw_query($sql4) or $msg_info[] = imw_error();


/*************************************/
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 62 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 62 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 62</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>