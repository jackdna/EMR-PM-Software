<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql = array();

// Update to add external fields/table for Advacned MD Api integration
$sql[] = "ALTER TABLE `patient_data_tbl` ADD `amd_patient_id` INT NOT NULL;";

$sql[] = "ALTER TABLE `patient_in_waiting_tbl` ADD `amd_visit_id` INT NOT NULL;";

$sql[]="CREATE TABLE `amd_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT 'Code Value',
  `amd_id` varchar(50) NOT NULL COMMENT 'AMD ID for the Code',
  `code_type` enum('1','2','3') NOT NULL COMMENT '1=Procedure Codes, 2=Diagnosis Codes, 3=Modifiers',
  `amd_object` varchar(255) NOT NULL COMMENT 'Complete AMD Object Response for the Value',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

$sql[] = "ALTER TABLE `amd_codes` ADD UNIQUE `unique_code` (`code`, `code_type`)";

$sql[] = "ALTER TABLE  `amd_codes` CHANGE  `code`  `code` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT  'Code Value'";
$sql[] = "ALTER TABLE  `amd_codes` CHANGE  `amd_id`  `amd_id` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'AMD ID for the Code'";
$sql[] = "ALTER TABLE  `amd_codes` CHANGE  `code_type`  `code_type` enum('1','2','3') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT '1=Procedure Codes, 2=Diagnosis Codes, 3=Modifiers'";
$sql[] = "ALTER TABLE  `amd_codes` CHANGE  `amd_object`  `amd_object` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Complete AMD Object Response for the Value'";

$sql[] ="CREATE TABLE  `amd_visit_log` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`visit_id` VARCHAR( 255 ) NOT NULL COMMENT  'AMD Visit ID',
	`provider_id` VARCHAR( 255 ) NOT NULL COMMENT  'AMD provider id associated with the visit',
	`pt_id` VARCHAR( 255 ) NOT NULL COMMENT  'AMD patient id for the visit',
	`dos` VARCHAR( 255 ) NOT NULL COMMENT  'Visit Date',
	`m_visit_id` VARCHAR( 255 ) NOT NULL COMMENT  'Master Visit ID'
) ENGINE = MYISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";

$sql[] = "ALTER TABLE  `amd_visit_log` ADD  `amd_visit_data` VARCHAR( 500 ) NOT NULL COMMENT  'Visit Object returned fro AMD'";

$sql[] = "CREATE TABLE `amd_facility_codes` (
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`amd_code` varchar(20) COLLATE latin1_general_ci NOT NULL COMMENT 'AMD Facility Code',
	`amd_id` varchar(20) COLLATE latin1_general_ci NOT NULL COMMENT 'AMD Facility ID'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";

$sql[] = "CREATE TABLE `amd_batch_log` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`batch_id` varchar(20) COLLATE latin1_general_ci NOT NULL COMMENT 'AMD batch ID',
	`creating_date` date NOT NULL COMMENT 'Date of Batch Creation',
	`batch_data` varchar(500) COLLATE latin1_general_ci NOT NULL COMMENT 'Complete batch Data',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;";

$sql[] = "CREATE TABLE `amd_charges_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_id` int(11) NOT NULL,
  `amd_visit_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=Not Posted, 1=Posted',
  `reason` varchar(500) NOT NULL,
  `date_posted` datetime NOT NULL,
  `m_amd_visit_id` int(11) NOT NULL,
  `type` enum('1','2','3') NOT NULL COMMENT 'Entry Type 1=Provider, 2=Facility, 3=Anesthesia',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

$sql[] = "ALTER TABLE  `amd_codes` ADD  `amd_fee` DECIMAL( 10, 2 ) NOT NULL AFTER  `amd_id`";
$sql[] = "ALTER TABLE  `amd_codes` ADD  `amd_allowed` DECIMAL( 10, 2 ) NOT NULL AFTER  `amd_fee`";
$sql[] = "ALTER TABLE  `amd_codes` ADD  `amd_units` DECIMAL( 10, 1 ) NOT NULL AFTER  `amd_allowed`";

$sql[] = "ALTER TABLE `patient_data_tbl` ADD  `amd_ins_order` VARCHAR( 50 ) NOT NULL COMMENT 'AMD INS order. Mainly used in posting charges' AFTER `amd_patient_id`";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 136 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 136 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 136</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>