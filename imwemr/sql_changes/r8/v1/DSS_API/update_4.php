<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql1="CREATE TABLE IF NOT EXISTS `lab_sample` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lab_test_id` int(11) NOT NULL,
  `smp_collection_type` varchar(250) NOT NULL,
  `sample_start_date` date NOT NULL,
  `sample_start_time` time NOT NULL,
  `sample_end_date` date NOT NULL,
  `sample_end_time` time NOT NULL,
  `sample_condition` varchar(250) NOT NULL,
  `sample_rejection` varchar(250) NOT NULL,
  `sample_comments` text NOT NULL,
  `del_status` int(11) NOT NULL,
  `del_date` date NOT NULL,
  `del_operator_id` int(11) NOT NULL,
  `sample_entered_by` int(11) NOT NULL,
  `sample_entered_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;";
imw_query($sql1) or $error[] = imw_error();

$sql2 = "ALTER TABLE `lists` CHANGE `service_eligibility` `service_eligibility` VARCHAR(200) NOT NULL COMMENT 'Specify if DSS Medication is service connected eligibility for each options';";
imw_query($sql2) or $error[] = imw_error();

$sql3 = "ALTER TABLE `pt_problem_list` CHANGE `service_eligibility` `service_eligibility` VARCHAR(500) NOT NULL COMMENT 'specify if DSS problem is service connected eligibility 1=Yes and 0=No';";
imw_query($sql3) or $error[] = imw_error();

$sql4 = "ALTER TABLE `patient_data` ADD `service_eligibility` VARCHAR(500) NOT NULL COMMENT 'DSS patient service connected allowed types';";
imw_query($sql4) or $error[] = imw_error();

$sql5 = "ALTER TABLE `patient_data` ADD `service_eligibility_status` TEXT NOT NULL COMMENT 'DSS patient service connected eligibility status';";
imw_query($sql5) or $error[] = imw_error();

$sql6 = "ALTER TABLE `lab_specimen` ADD `dss_collection_id` INT NOT NULL COMMENT 'DSS Specimen Id';";
imw_query($sql6) or $error[] = imw_error();

$sql7 = "ALTER TABLE `lab_radiology_tbl` ADD `dss_lab_id` INT NOT NULL COMMENT 'DSS Orderable Lab Id';";
imw_query($sql7) or $error[] = imw_error();

$sql8 = "ALTER TABLE `lab_sample` ADD `dss_sample_id` INT NOT NULL COMMENT 'DSS Sample Id';";
imw_query($sql8) or $error[] = imw_error();

$sql9 = "ALTER TABLE `lab_test_data` ADD `dss_collection_type` VARCHAR( 50 )  NOT NULL ;";
imw_query($sql9) or $error[] = imw_error();

$sql10 = "ALTER TABLE `lab_test_data` ADD `dss_urgency` VARCHAR( 50 )  NOT NULL ;";
imw_query($sql10) or $error[] = imw_error();

$sql11 = "ALTER TABLE `lab_test_data` ADD `dss_schedules` VARCHAR( 50 )  NOT NULL ;";
imw_query($sql11) or $error[] = imw_error();

$sql12 = "ALTER TABLE `lab_test_data` ADD `dss_lab_order_number` INT NOT NULL ;";
imw_query($sql12) or $error[] = imw_error();

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 4 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 4 Success.</b>";
	$color = "green";	
}



?>

<html>
<head>
<title>Update 4</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>