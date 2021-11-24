<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql9="
CREATE TABLE IF NOT EXISTS `tsys_trans_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_key` varchar(255) NOT NULL,
  `request_type` varchar(255) NOT NULL,
  `request_url` varchar(255) NOT NULL,
  `request_data` text NOT NULL,
  `request_date_time` datetime NOT NULL,
  `response_data` text NOT NULL,
  `response_date_time` datetime NOT NULL,
  `operator_id` int(11) NOT NULL,
  `imw_trans_id` int(15) NOT NULL,
  `log_referenceNumber` varchar(100) NOT NULL,
   PRIMARY KEY (`log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
imw_query($sql9) or $msg_info[] = imw_error();

$sql10="
CREATE TABLE IF NOT EXISTS `tsys_sale_request` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `patient_id` int(15) NOT NULL DEFAULT '0',
  `operator_id` int(15) NOT NULL DEFAULT '0',
  `tsys_dtls_id` int(15) NOT NULL DEFAULT '0',
  `cardNumber` varchar(255) NOT NULL DEFAULT '',
  `expirationDate` varchar(255) NOT NULL DEFAULT '',
  `transactionAmount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `added_on` datetime NOT NULL,
  `status` varchar(100) NOT NULL,
  `card_source` varchar(150) NOT NULL,
  `paymentStr` text NOT NULL,
  `scheduID` int(10) NOT NULL,
  `encountID` int(10) NOT NULL,
  `reqLaneID` int(10) NOT NULL,
  `log_referenceNumber` varchar(255) NOT NULL,
  `tsys_payment_type_log_id` int(15) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ";
imw_query($sql10) or $msg_info[] = imw_error();

$sql11="
CREATE TABLE IF NOT EXISTS `tsys_sale_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_req_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `status` varchar(150) NOT NULL,
  `responseCode` varchar(100) NOT NULL,
  `responseMessage` varchar(512) NOT NULL,
  `authCode` varchar(255) NOT NULL,
  `hostReferenceNumber` varchar(150) NOT NULL,
  `hostResponseCode` int(15) NOT NULL,
  `taskID` int(15) NOT NULL,
  `transactionID` int(15) NOT NULL,
  `transactionTimestamp` datetime NOT NULL,
  `transactionAmount` decimal(10,2) NOT NULL,
  `processedAmount` decimal(10,2) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `addressVerificationCode` int(10) NOT NULL,
  `cardType` varchar(150) NOT NULL,
  `maskedCardNumber` varchar(255) NOT NULL,
  `aci` varchar(150) NOT NULL,
  `cardTransactionIdentifier` varchar(255) NOT NULL,
  `customerReceipt` longtext NOT NULL,
  `merchantReceipt` longtext NOT NULL,
  `added_on` datetime NOT NULL,
  `token` varchar(255) NOT NULL,
  `expirationDate` varchar(50) NOT NULL,
  `commercialCard` varchar(50) NOT NULL,
  `return_resp_id` int(10) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ";
imw_query($sql11) or $msg_info[] = imw_error();

$sql12="
CREATE TABLE IF NOT EXISTS `tsys_void_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `tsys_dtls_id` int(11) NOT NULL,
  `sale_trans_id` int(11) NOT NULL,
  `trans_amount` decimal(10,2) NOT NULL,
  `lane_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `added_on` datetime NOT NULL,
  `status` varchar(5) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1  ";
imw_query($sql12) or $msg_info[] = imw_error();

$sql13="
CREATE TABLE IF NOT EXISTS `tsys_void_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `status` varchar(5) NOT NULL,
  `responseCode` varchar(10) NOT NULL,
  `responseMessage` varchar(255) NOT NULL,
  `authCode` varchar(32) NOT NULL,
  `hostReferenceNumber` varchar(255) NOT NULL,
  `hostResponseCode` varchar(255) NOT NULL,
  `taskID` int(11) NOT NULL,
  `transactionID` int(11) NOT NULL,
  `transactionTimestamp` datetime NOT NULL,
  `orderNumber` varchar(32) NOT NULL,
  `externalReferenceID` varchar(128) NOT NULL,
  `transactionAmount` decimal(10,2) NOT NULL,
  `voidedAmount` decimal(10,2) NOT NULL,
  `cardType` varchar(10) NOT NULL,
  `maskedCardNumber` int(11) NOT NULL,
  `customerReceipt` text NOT NULL,
  `merchantReceipt` text NOT NULL,
  `added_on` datetime NOT NULL,
  `token` varchar(150) NOT NULL,
  `expirationDate` varchar(10) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1  ";
imw_query($sql13) or $msg_info[] = imw_error();

$sql14="
CREATE TABLE IF NOT EXISTS `tsys_return_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT ,
  `patient_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `tsys_dtls_id` int(11) NOT NULL,
  `sale_trans_id` int(11) NOT NULL,
  `trans_amount` decimal(10,2) NOT NULL,
  `lane_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `added_on` datetime NOT NULL,
  `status` varchar(5) NOT NULL,
  `card_source` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ";
imw_query($sql14) or $msg_info[] = imw_error();

$sql15="
CREATE TABLE IF NOT EXISTS `tsys_return_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `status` varchar(15) NOT NULL,
  `responseCode` varchar(20) NOT NULL,
  `responseMessage` varchar(255) NOT NULL,
  `authCode` varchar(50) NOT NULL,
  `hostReferenceNumber` varchar(255) NOT NULL,
  `hostResponseCode` varchar(255) NOT NULL,
  `taskID` int(11) NOT NULL,
  `transactionID` int(11) NOT NULL,
  `transactionTimestamp` datetime NOT NULL,
  `orderNumber` varchar(50) NOT NULL,
  `externalReferenceID` varchar(128) NOT NULL,
  `transactionAmount` decimal(10,2) NOT NULL,
  `returnedAmount` decimal(10,2) NOT NULL,
  `cardType` varchar(10) NOT NULL,
  `maskedCardNumber` int(11) NOT NULL,
  `token` varchar(150) NOT NULL,
  `expirationDate` varchar(150) NOT NULL,
  `customerReceipt` text NOT NULL,
  `merchantReceipt` text NOT NULL,
  `added_on` datetime NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ";
imw_query($sql15) or $msg_info[] = imw_error();


$sql1="ALTER TABLE `tsys_payment_type_log` ADD `orderNumber` VARCHAR( 255 ) NOT NULL ";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="ALTER TABLE `tsys_possale_transaction` ADD `motoType` VARCHAR( 20 ) NOT NULL ";
imw_query($sql2) or $msg_info[] = imw_error();

$sql3="ALTER TABLE `tsys_possale_transaction` ADD `motoOrderNumber` VARCHAR( 255 ) NOT NULL  ";
imw_query($sql3) or $msg_info[] = imw_error();

//$sql4="ALTER TABLE `tsys_trans_log` ADD `transaction_key` VARCHAR( 255 ) NOT NULL  ";
//imw_query($sql4) or $msg_info[] = imw_error();

$sql5="ALTER TABLE `tsys_possale_transaction` ADD `token` VARCHAR( 255 ) NOT NULL  ";
imw_query($sql5) or $msg_info[] = imw_error();

$sql6="ALTER TABLE `tsys_possale_transaction` ADD `is_api` tinyint(5) NOT NULL DEFAULT '0'  ";
imw_query($sql6) or $msg_info[] = imw_error();

$sql7="ALTER TABLE `facility` ADD `iportal_payments_settings` TINYINT( 5 ) NOT NULL DEFAULT '0'  ";
imw_query($sql7) or $msg_info[] = imw_error();

$sql8="ALTER TABLE `facility` ADD `iportal_pos_device` INT( 11 ) NOT NULL DEFAULT '0'  ";
imw_query($sql8) or $msg_info[] = imw_error();

$sql16="ALTER TABLE `tsys_possale_transaction` ADD  `isRecurring` varchar(20) NOT NULL  ";
imw_query($sql16) or $msg_info[] = imw_error();

$sql17="ALTER TABLE `tsys_possale_transaction` ADD  `paymentCount` varchar(30) NOT NULL  ";
imw_query($sql17) or $msg_info[] = imw_error();

$sql18="ALTER TABLE `tsys_possale_transaction` ADD  `currentPaymentCount` varchar(30) NOT NULL  ";
imw_query($sql18) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 101  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 101  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 101</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>