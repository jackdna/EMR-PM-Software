<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

$sql[] = "ALTER TABLE `account_payments` ADD `cas_type` VARCHAR( 50 ) NOT NULL ,ADD `cas_code` VARCHAR( 50 ) NOT NULL";
$sql[] = "ALTER TABLE `patient_charges_detail_payment_info` CHANGE `CAS_type` `CAS_type` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ";
$sql[] = "ALTER TABLE `paymentswriteoff` CHANGE `CAS_type` `CAS_type` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL  ";

foreach(  $sql as $query)
	imw_query($query) or $msg_info[] = imw_error();
	
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 8 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 8 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 8</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>