<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("ALTER TABLE `patient_chargesheet_payment_info` CHANGE  `payment_mode`  `payment_mode` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL") or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 27 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 27 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 27</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>