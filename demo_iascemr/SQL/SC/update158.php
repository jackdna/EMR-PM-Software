<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

/******************************************
*																					*
* Update contains tables used in iASCLink	*
* for History and Physical Chart					*
*																					*			
******************************************/
$sql[] = "CREATE TABLE iolink_history_physical_ques (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  pt_waiting_id int(11) NOT NULL,
  patient_id int(11) NOT NULL DEFAULT '0',
  ques varchar(1024) NOT NULL,
  ques_status varchar(5) NOT NULL,
  ques_desc text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$sql[] = "CREATE TABLE iolink_history_physical (
  history_physical_id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  pt_waiting_id int(11) NOT NULL,
  patient_id int(11) NOT NULL,
  form_status varchar(255) NOT NULL,
  cadMI varchar(255) NOT NULL,
  cadMIDesc text NOT NULL,
  cvaTIA varchar(255) NOT NULL,
  cvaTIADesc text NOT NULL,
  htnCP varchar(255) NOT NULL,
  htnCPDesc text NOT NULL,
  anticoagulationTherapy varchar(255) NOT NULL,
  anticoagulationTherapyDesc text NOT NULL,
  respiratoryAsthma varchar(255) NOT NULL,
  respiratoryAsthmaDesc text NOT NULL,
  arthritis varchar(255) NOT NULL,
  arthritisDesc text NOT NULL,
  diabetes varchar(255) NOT NULL,
  diabetesDesc text NOT NULL,
  recreationalDrug varchar(255) NOT NULL,
  recreationalDrugDesc text NOT NULL,
  giGerd varchar(255) NOT NULL,
  giGerdDesc text NOT NULL,
  ocular varchar(255) NOT NULL,
  ocularDesc text NOT NULL,
  kidneyDisease varchar(255) NOT NULL,
  kidneyDiseaseDesc text NOT NULL,
  hivAutoimmune varchar(255) NOT NULL,
  hivAutoimmuneDesc text NOT NULL,
  historyCancer varchar(255) NOT NULL,
  historyCancerDesc text NOT NULL,
  organTransplant varchar(255) NOT NULL,
  organTransplantDesc text NOT NULL,
  badReaction varchar(255) NOT NULL,
  badReactionDesc text NOT NULL,
  otherHistoryPhysical text NOT NULL,
  heartExam varchar(5) NOT NULL,
  heartExamDesc text NOT NULL,
  lungExam varchar(5) NOT NULL,
  lungExamDesc text NOT NULL,
  discussedAdvancedDirective varchar(5) NOT NULL,
  version_num int(11) NOT NULL,
  version_date_time datetime NOT NULL,
  save_date_time datetime NOT NULL,
  save_operator_id int(11) NOT NULL,
  create_date_time datetime NOT NULL,
  create_operator_id int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";


$sql[] = "ALTER TABLE `iolink_history_physical` ADD `highCholesterol` VARCHAR(5) NOT NULL,
												ADD `highCholesterolDesc` TEXT NOT NULL, 
												ADD `thyroid` VARCHAR(5) NOT NULL, 
												ADD `thyroidDesc` TEXT NOT NULL, 
												ADD `ulcer` VARCHAR(5) NOT NULL, 
												ADD `ulcerDesc` TEXT NOT NULL;";

$sql[] = "ALTER TABLE `iolink_history_physical_ques` ADD `source` VARCHAR(10) NOT NULL;";

$sql[] = "ALTER TABLE `history_physicial_clearance` ADD `highCholesterol` VARCHAR(5) NOT NULL,
												ADD `highCholesterolDesc` TEXT NOT NULL, 
												ADD `thyroid` VARCHAR(5) NOT NULL, 
												ADD `thyroidDesc` TEXT NOT NULL, 
												ADD `ulcer` VARCHAR(5) NOT NULL, 
												ADD `ulcerDesc` TEXT NOT NULL;";

$sql[] = "ALTER TABLE `history_physical_ques` ADD `source` VARCHAR(10) NOT NULL;";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 158 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 158 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 158</title>
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