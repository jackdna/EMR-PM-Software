<?php
set_time_limit(0);
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql = "

ALTER TABLE ".constant("IMEDIC_SCAN_DB").".`idoc_drawing`  ADD `drwNE` VARCHAR(20) NOT NULL

";

$result = imw_query($sql) or $msg_info[] = imw_error();

if(!empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){ 
	
	$sql = "

	ALTER TABLE ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".`idoc_drawing`  ADD `drwNE` VARCHAR(20) NOT NULL

	";

	$result = imw_query($sql) or $msg_info[] = imw_error();
	
	$sql = " ALTER TABLE ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".`idoc_drawing` ADD `drw_data_json` LONGTEXT NOT NULL ; ";
	$result = imw_query($sql) or $msg_info[] = imw_error();
	
}



if(!$result)
{
	$msg_info[] = '<br><br><b>Update 120 :: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 120 :: Update run successfully!<br></b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 120 :: </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(implode("<br>",$msg_info));?>
</font>
</body>
</html>