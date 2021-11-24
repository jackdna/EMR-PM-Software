<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

$sql[] = "ALTER TABLE `account_payments` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `payment_deductible` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `patient_chargesheet_payment_info` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `deniedpayment` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `paymentswriteoff` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `defaultwriteoff` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `creditapplied` ADD `facility_id` INT( 11 ) NOT NULL ";

$sql[] = "ALTER TABLE `manual_batch_transactions` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `manual_batch_creditapplied` ADD `facility_id` INT( 11 ) NOT NULL ";

$sql[] = "ALTER TABLE `report_enc_trans` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `patient_charge_list` ADD `billing_facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `report_enc_detail` ADD `billing_facility_id` INT( 11 ) NOT NULL ";

foreach(  $sql as $query)
	imw_query($query) or $msg_info[] = imw_error();
	
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 11 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 11 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 11</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>