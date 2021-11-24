<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//Task Manager Rules Category
$qry = "
CREATE TABLE IF NOT EXISTS `tm_rule_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tm_rule_category` varchar(255) NOT NULL DEFAULT '',
  `tm_rule_cat_alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM 
";
imw_query($qry) or $msg_info[] = imw_error();

/*
$qry = "
INSERT INTO `tm_rule_category` (`id`, `tm_rule_category`, `tm_rule_cat_alias`) VALUES
(1, 'Accounting', 'accounting'),
(2, 'Appointment', 'appointment'),
(3, 'A/R Aging', 'ar_aging');
"; */
$qry = "
INSERT INTO `tm_rule_category` (`id`, `tm_rule_category`, `tm_rule_cat_alias`) VALUES
(1, 'Accounting', 'accounting'),
(2, 'Appointment', 'appointment');
";
imw_query($qry) or $msg_info[] = imw_error();

//Task Manager Rules
$qry = "
CREATE TABLE IF NOT EXISTS `tm_rules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tm_rcat_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tm_rule_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM 
";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "
INSERT INTO `tm_rules` (`id`, `tm_rcat_id`, `tm_rule_name`) VALUES
(2, 1, 'Denial & Rejection'),
(3, 1, 'Reason code'),
(4, 1, 'Encounter Deleted '),
(5, 1, 'Transaction Deleted'),
(6, 1, 'Payment Deleted or Edited'),
(7, 1, 'Pt Status changed'),
(8, 1, 'Pt Account Status'),
(9, 1, 'Incoming Fax'),
(10, 2, 'Appointment Canceled'),
(11, 2, 'Appointment Created'),
(12, 2, 'Appointment Deleted'),
(13, 2, 'Appointment No Show'),
(14, 2, 'Appointment Rescheduled'),
(15, 3, 'Insurance'),
(16, 3, 'Patient');
";
imw_query($qry) or $msg_info[] = imw_error();

//Task Manager Created Rules Table
$qry = "
CREATE TABLE IF NOT EXISTS `tm_rules_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cat_id` int(10) unsigned NOT NULL DEFAULT '0',
  `reason_code` longtext NOT NULL,
  `pt_status` varchar(255) NOT NULL DEFAULT ' ',
  `pt_account_status` varchar(255) NOT NULL DEFAULT ' ',
  `transaction` varchar(255) NOT NULL,
  `user_group` varchar(255) NOT NULL DEFAULT ' ',
  `user_name` varchar(255) NOT NULL DEFAULT ' ',
  `pt_appt_status` varchar(255) NOT NULL DEFAULT ' ',
  `ar_aging` varchar(255) NOT NULL DEFAULT ' ',
  `comment` longtext NOT NULL,
  `operator_id` int(10) NOT NULL DEFAULT '0',
  `rule_status` int(10) NOT NULL DEFAULT '0',
  `addedon` int(10) unsigned NOT NULL DEFAULT '0',
  `updatedon` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  
";
imw_query($qry) or $msg_info[] = imw_error();


//Task Manager Task Assigned according to rules
$qry = "
CREATE TABLE IF NOT EXISTS `tm_assigned_rules` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `rule_list_id` int(15) NOT NULL DEFAULT '0',
  `status` int(10) NOT NULL DEFAULT '0',
  `changed_value` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `patientid` int(15) NOT NULL DEFAULT '0',
  `patient_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `operatorid` int(15) NOT NULL DEFAULT '0',
  `encounter_id` int(15) NOT NULL,
  `date_of_service` date NOT NULL,
  `cpt_code` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `appt_type` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `appt_date` date NOT NULL,
  `appt_time` time NOT NULL,
  `appt_status` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `appt_facility_id` int(10) unsigned NOT NULL DEFAULT '0',
  `appt_comment` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `amount_due` double(12,2) NOT NULL,
  `days_aged` int(15) NOT NULL,
  `ar_comment` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `inbound_fax_id` int(15) NOT NULL,
  `added_on` int(10) NOT NULL DEFAULT '0',
  `updated_on` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM 
";
imw_query($qry) or $msg_info[] = imw_error();


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 61 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 61 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 61</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>