<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("ALTER TABLE `patient_charge_list` ADD `hl7_order_id` VARCHAR( 50 ) NOT NULL ") or $msg_info[] = imw_error();
imw_query("ALTER TABLE `patient_charge_list_details` ADD `hl7_sub_order_id` VARCHAR( 50 ) NOT NULL ") or $msg_info[] = imw_error();
imw_query("ALTER TABLE `patient_charge_list_details` ADD `hl7_sub_order_id2` VARCHAR( 50 ) NOT NULL ") or $msg_info[] = imw_error();
imw_query("ALTER TABLE `paymentswriteoff` ADD `hl7_sub_order_id2` VARCHAR( 50 ) NOT NULL ") or $msg_info[] = imw_error();
imw_query("ALTER TABLE `tx_payments` ADD `hl7_sub_order_id2` VARCHAR( 50 ) NOT NULL") or $msg_info[] = imw_error(); 

 
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 22 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 22 completed successfully.</b>";
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