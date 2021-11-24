<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql1 = "ALTER TABLE `pt_problem_list` ADD `dss_last_modified_date` DATE NOT NULL";
imw_query($sql1) or $error[] = imw_error();

$sql2 = "ALTER TABLE `pt_problem_list` ADD `service_eligibility` INT( 10 ) NOT NULL DEFAULT '0' COMMENT 'specify if DSS problem is service connected eligibility 1=Yes and 0=No' ";
imw_query($sql2) or $error[] = imw_error();

$sql3 = "ALTER TABLE `pt_problem_list_log` ADD `service_eligibility` INT( 10 ) NOT NULL DEFAULT '0' COMMENT 'specify if DSS problem is service connected eligibility 1=Yes and 0=No' ";
imw_query($sql3) or $error[] = imw_error();

$sql4 = "ALTER TABLE `lists` ADD `service_eligibility` INT( 10 ) NOT NULL DEFAULT '0' COMMENT 'specify if DSS Medication is service connected eligibility 1=Yes and 0=No' ";
imw_query($sql4) or $error[] = imw_error();

$sql5 = "ALTER TABLE `chart_master_table` ADD `service_eligibility` INT( 10 ) NOT NULL DEFAULT '0' COMMENT 'specify if DSS Visit is service connected eligibility 1=Yes and 0=No' ";
imw_query($sql5) or $error[] = imw_error();

$sql6 = "ALTER TABLE `users` ADD `dss_elec_sign` VARCHAR(50) NULL COMMENT 'Dss User Electornic Signature Code'";
imw_query($sql6) or $error[] = imw_error();

$sql7 = "ALTER TABLE `medicine_data` ADD `dss_ien` INT(11) NOT NULL COMMENT 'DSS Drug IEN'";
imw_query($sql7) or $error[] = imw_error();

$sql8="CREATE TABLE IF NOT EXISTS `dss_code_dictionary` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `code_type` varchar(50) NOT NULL,
  `modifiers` text NOT NULL,
  `status` tinyint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `code_type` (`code_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;";
imw_query($sql8) or $error[] = imw_error();

$sql9 = "ALTER TABLE `allergies_data` ADD `dss_type` VARCHAR( 50 ) NOT NULL  ";
imw_query($sql9) or $error[] = imw_error();

$sql10 = "ALTER TABLE `lists` CHANGE `dss_allergy_id` `dss_allergy_id` INT( 10 ) NOT NULL DEFAULT '0' COMMENT 'DSS Allergy Id' ";
imw_query($sql10) or $error[] = imw_error();

$sql11 = "ALTER TABLE `allergies_data` ADD `globalNode` VARCHAR( 255 ) NOT NULL COMMENT 'DSS Allergy globalNode',
ADD `dss_order` VARCHAR( 50 ) NOT NULL COMMENT 'DSS Allergy order' ";
imw_query($sql11) or $error[] = imw_error();

$sql12="CREATE TABLE IF NOT EXISTS `dss_tiu` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `patient_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `consult_data` longtext NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '(1-sent, 0=not)',
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
imw_query($sql12) or $error[] = imw_error();

$sql13 = "ALTER TABLE `chart_master_table` ADD `tiu_ifn` INT( 11 ) NOT NULL COMMENT 'DSS TIU Title ifn'";
imw_query($sql13) or $error[] = imw_error();

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 3 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 3 Success.</b>";
	$color = "green";	
}



?>

<html>
<head>
<title>Update 3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>