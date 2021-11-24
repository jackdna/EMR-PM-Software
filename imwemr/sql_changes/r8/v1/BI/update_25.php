<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("ALTER TABLE `patient_charge_list` CHANGE `coPayWriteOff` `coPayWriteOff` DOUBLE( 12, 2 ) NOT NULL") or $msg_info[] = imw_error();
imw_query("ALTER TABLE `patient_charge_list` CHANGE `pri_copay` `pri_copay` DOUBLE( 12, 2 ) NOT NULL") or $msg_info[] = imw_error();
imw_query("ALTER TABLE `patient_charge_list` CHANGE `sec_copay` `sec_copay` DOUBLE( 12, 2 ) NOT NULL") or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 25 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 25 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 25</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>