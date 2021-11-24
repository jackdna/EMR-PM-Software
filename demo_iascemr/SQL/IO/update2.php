<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql = "CREATE TABLE IF NOT EXISTS `iolink_insurance_case` (
  `ins_caseid` int(11) NOT NULL AUTO_INCREMENT,
  `ins_case_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ins_case_type` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `case_id` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `case_status` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `athenaID` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `case_name` varchar(225) COLLATE latin1_general_ci NOT NULL,
  `vision` int(2) NOT NULL,
  `normal` int(2) NOT NULL,
  `waiting_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  PRIMARY KEY (`ins_caseid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Created for storing insurance case details.';
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "CREATE TABLE IF NOT EXISTS `iolink_insurance_scan_documents` (
  `scan_documents_id` int(12) NOT NULL AUTO_INCREMENT,
  `type` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `ins_caseid` int(12) NOT NULL,
  `scan_card` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `scan_label` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `scan_card2` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `scan_label2` varchar(256) COLLATE latin1_general_ci NOT NULL,
  `created_date` date NOT NULL,
  `operator_id` int(12) NOT NULL,
  `document_status` enum('0','1') COLLATE latin1_general_ci NOT NULL,
  `cardscan_operator` int(11) NOT NULL,
  `cardscan_date` datetime NOT NULL,
  `cardscan_comments` text COLLATE latin1_general_ci NOT NULL,
  `waiting_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  PRIMARY KEY (`scan_documents_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ;
";
$row = imw_query($sql) or $msg_info[] = imw_error();




$sql = "ALTER TABLE `insurance_data` CHANGE `waiting_id` `waiting_id` INT( 11 ) NOT NULL ,
CHANGE `patient_id` `patient_id` INT( 11 ) NOT NULL 
";	
imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `insurance_data` 
		ADD `scan_card` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		ADD `scan_label` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		ADD `ins_caseid` bigint(20) NOT NULL,
		ADD `claims_adjustername` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		ADD `claims_adjusterphone` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		ADD `Sec_HCFA` int(2) NOT NULL DEFAULT '0',
		ADD `newComDate` date NOT NULL,
		ADD `actInsComp` int(2) NOT NULL,
		ADD `actInsCompDate` date NOT NULL,
		ADD `scan_card2` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		ADD `scan_label2` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		ADD `cardscan_date` datetime NOT NULL,
		ADD `cardscan_comments` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		ADD `cardscan1_datetime` datetime NOT NULL,
		ADD `self_pay_provider` int(2) NOT NULL;
";	
imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Updates 2 run OK";

?>

<html>
<head>
<title>Mysql Updates After Launch</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







