<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$sql1="
CREATE TABLE IF NOT EXISTS `tsys_device_details` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) NOT NULL,
  `updated_on` datetime NOT NULL,
  `deviceName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deviceID` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `developerID` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `applicationID` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ipAddress` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `port` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `device_status` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="
    CREATE TABLE IF NOT EXISTS `tsys_merchant` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `updated_on` datetime NOT NULL,
  `merchantName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mid` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `userID` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mid_paswrd` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Company` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `api_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `paswrd` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `merchant_status` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
";
imw_query($sql2) or $msg_info[] = imw_error();

$sql3="
   CREATE TABLE IF NOT EXISTS `tsys_payment_type_log` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `patient_id` int(15) NOT NULL,
  `posMachine` varchar(150) CHARACTER SET latin1 NOT NULL,
  `refrenceNumber` varchar(255) CHARACTER SET latin1 NOT NULL,
  `added_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
";
imw_query($sql3) or $msg_info[] = imw_error();

$sql4="
   CREATE TABLE IF NOT EXISTS `tsys_possale_transaction` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) NOT NULL,
  `operator_id` int(10) NOT NULL,
  `scheduID` int(10) NOT NULL,
  `encounter_id` int(10) NOT NULL,
  `laneId` int(10) NOT NULL,
  `transactionAmount` decimal(10,2) NOT NULL,
  `HostInformation` text CHARACTER SET latin1 NOT NULL,
  `AmountInformation` text CHARACTER SET latin1 NOT NULL,
  `AccountInformation` text CHARACTER SET latin1 NOT NULL,
  `TraceInformation` text CHARACTER SET latin1 NOT NULL,
  `AVSinformation` text CHARACTER SET latin1 NOT NULL,
  `CommercialInformation` text CHARACTER SET latin1 NOT NULL,
  `motoEcommerce` text CHARACTER SET latin1 NOT NULL,
  `AdditionalInformation` text CHARACTER SET latin1 NOT NULL,
  `TransactionNumber` varchar(150) CHARACTER SET latin1 NOT NULL,
  `Account` varchar(150) CHARACTER SET latin1 NOT NULL,
  `ExpireDate` varchar(150) CHARACTER SET latin1 NOT NULL,
  `CardType` varchar(150) CHARACTER SET latin1 NOT NULL,
  `HostResponseCode` varchar(150) CHARACTER SET latin1 NOT NULL,
  `HostResponseMessage` varchar(255) CHARACTER SET latin1 NOT NULL,
  `AuthCode` varchar(200) CHARACTER SET latin1 NOT NULL,
  `TraceNumber` varchar(200) CHARACTER SET latin1 NOT NULL,
  `HostReferenceNumber` varchar(200) CHARACTER SET latin1 NOT NULL,
  `BatchNumber` varchar(200) CHARACTER SET latin1 NOT NULL,
  `TimeStamp` datetime NOT NULL,
  `added_on` datetime NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ResponseCode` varchar(150) CHARACTER SET latin1 NOT NULL,
  `ResponseMessage` varchar(512) CHARACTER SET latin1 NOT NULL,
  `TransactionType` varchar(150) CHARACTER SET latin1 NOT NULL,
  `log_referenceNumber` varchar(255) CHARACTER SET latin1 NOT NULL,
  `tsys_payment_type_log_id` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
";
imw_query($sql4) or $msg_info[] = imw_error();


$sql6="ALTER TABLE `tsys_device_details` ADD `facility_id` int(10) NOT NULL ;";
imw_query($sql6) or $msg_info[] = imw_error();

$sql6="ALTER TABLE `patient_pre_payment` ADD `log_referenceNumber` VARCHAR( 255 ) NOT NULL ;";
imw_query($sql6) or $msg_info[] = imw_error();
$sql7="ALTER TABLE `patient_pre_payment` ADD `tsys_transaction_id` int(15) NOT NULL ;";
imw_query($sql7) or $msg_info[] = imw_error();
$sql8="ALTER TABLE `patient_pre_payment` ADD `tsys_status` VARCHAR( 150 ) NOT NULL ;";
imw_query($sql8) or $msg_info[] = imw_error();


$sql9="ALTER TABLE `check_in_out_payment` ADD `log_referenceNumber` VARCHAR( 255 ) NOT NULL ;";
imw_query($sql9) or $msg_info[] = imw_error();
$sql10="ALTER TABLE `check_in_out_payment` ADD `tsys_transaction_id` int(15) NOT NULL ;";
imw_query($sql10) or $msg_info[] = imw_error();
$sql11="ALTER TABLE `check_in_out_payment` ADD `tsys_status` VARCHAR( 150 ) NOT NULL ;";
imw_query($sql11) or $msg_info[] = imw_error();


$sql12="ALTER TABLE `patient_charges_detail_payment_info` ADD `log_referenceNumber` VARCHAR( 255 ) NOT NULL ;";
imw_query($sql12) or $msg_info[] = imw_error();
$sql13="ALTER TABLE `patient_charges_detail_payment_info` ADD `tsys_transaction_id` int(15) NOT NULL ;";
imw_query($sql13) or $msg_info[] = imw_error();
$sql14="ALTER TABLE `patient_charges_detail_payment_info` ADD `tsys_status` VARCHAR( 150 ) NOT NULL ;";
imw_query($sql14) or $msg_info[] = imw_error();




/*************************************/
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 80 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 80 completed successfully.</b>";
	$color = "green";
}


?>
<html>
<head>
<title>Update 80</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>