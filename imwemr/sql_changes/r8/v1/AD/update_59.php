<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql4="ALTER TABLE `slot_procedures` ADD `ref_management` TINYINT(2) NOT NULL;";
imw_query($sql4) or $msg_info[] = imw_error();

$sql4="ALTER TABLE `insurance_companies` ADD `ref_management` TINYINT(2) NOT NULL;";
imw_query($sql4) or $msg_info[] = imw_error();

/*************************************/
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 59 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 59 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 59</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>