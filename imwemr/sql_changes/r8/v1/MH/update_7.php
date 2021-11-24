<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info = array();

// Save H & P Chart
$qry = "CREATE TABLE IF NOT EXISTS `surgerycenter_pt_history_physical` (
  `history_physicial_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `date_of_h_p` datetime DEFAULT NULL,
  `cadMI` varchar(5) NOT NULL,
  `cadMIDesc` text NOT NULL,
  `cvaTIA` varchar(5) NOT NULL,
  `cvaTIADesc` text NOT NULL,
  `htnCP` varchar(5) NOT NULL,
  `htnCPDesc` text NOT NULL,
  `anticoagulationTherapy` varchar(5) NOT NULL,
  `anticoagulationTherapyDesc` text NOT NULL,
  `respiratoryAsthma` varchar(5) NOT NULL,
  `respiratoryAsthmaDesc` text NOT NULL,
  `arthritis` varchar(5) NOT NULL,
  `arthritisDesc` text NOT NULL,
  `diabetes` varchar(5) NOT NULL,
  `diabetesDesc` text NOT NULL,
  `recreationalDrug` varchar(5) NOT NULL,
  `recreationalDrugDesc` text NOT NULL,
  `giGerd` varchar(5) NOT NULL,
  `giGerdDesc` text NOT NULL,
  `ocular` varchar(5) NOT NULL,
  `ocularDesc` text NOT NULL,
  `kidneyDisease` varchar(5) NOT NULL,
  `kidneyDiseaseDesc` text NOT NULL,
  `hivAutoimmune` varchar(5) NOT NULL,
  `hivAutoimmuneDesc` text NOT NULL,
  `historyCancer` varchar(5) NOT NULL,
  `historyCancerDesc` text NOT NULL,
  `organTransplant` varchar(5) NOT NULL,
  `organTransplantDesc` text NOT NULL,
  `badReaction` varchar(5) NOT NULL,
  `badReactionDesc` text NOT NULL,
  `otherHistoryPhysical` text NOT NULL,
  `heartExam` varchar(5) NOT NULL,
  `heartExamDesc` text NOT NULL,
  `lungExam` varchar(5) NOT NULL,
  `lungExamDesc` text NOT NULL,
  `discussedAdvancedDirective` varchar(5) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
imw_query($qry) or $msg_info[]=imw_error();

// To save Dynamic H&P Question for patient
$qry = "CREATE TABLE IF NOT EXISTS `surgerycenter_pt_history_physical_ques` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL DEFAULT '0',
  `ques` varchar(1024) NOT NULL,
  `ques_status` varchar(5) NOT NULL,
  `ques_desc` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
imw_query($qry) or $msg_info[]=imw_error();

// To save Dynamic H&P Question for admin
$qry = "CREATE TABLE IF NOT EXISTS `surgerycenter_history_physical_ques` (
  `id` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `surgerycenter_id` int(11) NOT NULL DEFAULT '0' UNIQUE KEY,
  `name` varchar(1024) COLLATE latin1_general_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

imw_query($qry) or $msg_info[]=imw_error();

$qry = "ALTER TABLE `surgerycenter_pt_history_physical_ques` CHANGE `ques` `ques_id` INT(11) NOT NULL;";
imw_query($qry) or $msg_info[]=imw_error();

$qry = "ALTER TABLE `surgerycenter_pt_history_physical` CHANGE `date_of_h_p` `create_date_time` DATETIME NULL DEFAULT NULL;";
imw_query($qry) or $msg_info[]=imw_error();

$qry = "ALTER TABLE `surgerycenter_pt_history_physical` ADD `create_operator_id` INT NOT NULL, ADD `save_date_time` DATETIME NOT NULL, ADD `save_operator_id` INT NOT NULL;";
imw_query($qry) or $msg_info[]=imw_error();

$qry = "ALTER TABLE `surgerycenter_pt_history_physical` ADD `highCholesterol` VARCHAR(5) NOT NULL,
												ADD `highCholesterolDesc` TEXT NOT NULL, 
												ADD `thyroid` VARCHAR(5) NOT NULL, 
												ADD `thyroidDesc` TEXT NOT NULL, 
												ADD `ulcer` VARCHAR(5) NOT NULL, 
												ADD `ulcerDesc` TEXT NOT NULL;";

imw_query($qry) or $msg_info[]=imw_error();



if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 7 run FAILED!</b><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 7 run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 7 (MH)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>