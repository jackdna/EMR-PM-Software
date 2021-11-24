<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql_select='Select `id` from `tm_rules` where `tm_rule_name`="Payments Received From Portal" ';
$select_row=imw_query($sql_select);
if($select_row && imw_num_rows($select_row)==0){
    $sql="INSERT INTO `tm_rules` (`id`, `tm_rcat_id`, `tm_rule_name`) VALUES (NULL, '1', 'Payments Received From Portal') ";
    imw_query($sql) or $msg_info[] = imw_error();
}

$sql1="ALTER TABLE `erp_api_credentials` ADD `portal_def_user` int(11) NOT NULL DEFAULT 0 ";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="ALTER TABLE `erp_api_credentials` ADD `portal_def_facility` int(11) NOT NULL DEFAULT 0 ";
imw_query($sql2) or $msg_info[] = imw_error();

$sql3= "CREATE TABLE `erp_patient_payments_data` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`portal_req_id` VARCHAR( 200 ) NOT NULL ,
	`transactionId` VARCHAR( 200 ) NOT NULL ,
	`createdUtc` VARCHAR( 200 ) NOT NULL ,
	`patientExternalId` VARCHAR( 200 ) NOT NULL ,
	`patientComments` text COLLATE utf8_unicode_ci NOT NULL ,
	`referenceNumber` VARCHAR( 200 ) NOT NULL ,
	`cardholderName` VARCHAR( 200 ) NOT NULL ,
	`cardholderAddress1` VARCHAR( 200 ) NOT NULL ,
	`cardholderAddress2` VARCHAR( 200 ) NOT NULL ,
	`cardholderCity` VARCHAR( 200 ) NOT NULL ,
	`cardholderState` VARCHAR( 200 ) NOT NULL ,
	`cardholderZip` VARCHAR( 200 ) NOT NULL ,
	`cardholderPhoneNumber` VARCHAR( 200 ) NOT NULL ,
	`cardholderEmail` VARCHAR( 200 ) NOT NULL ,
	`cardType` VARCHAR( 200 ) NOT NULL ,
	`last4CardNumber` VARCHAR( 200 ) NOT NULL ,
	`cardExpiration` VARCHAR( 200 ) NOT NULL ,
	`amount` decimal(10,2) NOT NULL DEFAULT '0.00' ,
	`processedAmount` decimal(10,2) NOT NULL DEFAULT '0.00' ,
	`paymentSuccessful` VARCHAR( 200 ) NOT NULL ,
	`alreadySent` VARCHAR( 200 ) NOT NULL ,
	`sentOn` VARCHAR( 200 ) NOT NULL ,
	`created_on` datetime NOT NULL,
	`operator` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`action_date` datetime NOT NULL
	)
	";
imw_query($sql3) or $msg_info[] = imw_error();

$sql4="ALTER TABLE `tm_assigned_rules` ADD `patient_portal_payments` text COLLATE utf8_unicode_ci NOT NULL ";
imw_query($sql4) or $msg_info[] = imw_error();

$sql5="ALTER TABLE `tm_assigned_rules` ADD `patient_portal_payment_id` int(11) NOT NULL DEFAULT 0 ";
imw_query($sql5) or $msg_info[] = imw_error();

$sql6="ALTER TABLE `patient_pre_payment` ADD `erp_transaction_id` VARCHAR( 200 ) NOT NULL ";
imw_query($sql6) or $msg_info[] = imw_error();

$sql7="ALTER TABLE `patient_pre_payment` ADD `erp_reference_number` VARCHAR( 200 ) NOT NULL ";
imw_query($sql7) or $msg_info[] = imw_error();

$sql8="ALTER TABLE `patient_pre_payment` ADD `erp_patient_portal_payment_id` int(11) NOT NULL DEFAULT 0 ";
imw_query($sql8) or $msg_info[] = imw_error();

$sql9="ALTER TABLE `patient_pre_payment` ADD `erp_patient_portal_payment` tinyint(4) NOT NULL DEFAULT '0' ";
imw_query($sql9) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 8 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 8 run successfully!</b>";
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