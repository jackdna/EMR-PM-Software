<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql="UPDATE `custom_reports` SET `report_sub_type` = 'claims' WHERE `custom_reports`.`template_name` = 'Denial Records'";
imw_query($sql) or $msg_info[] = imw_error();


/*************************************/
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 54 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 54 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 54</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>