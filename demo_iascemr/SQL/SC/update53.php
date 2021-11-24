<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");


$sql = "
CREATE TABLE `chart_log` (
  `chart_log_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `confirmation_id` bigint(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `operator_type` varchar(255) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `chart_open_date` date NOT NULL,
  `chart_open_time` time NOT NULL,
  `chart_close_date` date NOT NULL,
  `chart_close_time` time NOT NULL,
  PRIMARY KEY (`chart_log_id`),
  KEY `confirmation_id` (`confirmation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "ALTER TABLE `left_navigation_forms`  ADD `history_physical_form` VARCHAR(5) NOT NULL DEFAULT 'true' AFTER `pre_op_health_ques_form`";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `history_physicial_clearance` 
DROP `malignanyHyperthermia`,
DROP `malignanyHyperthermiaDesc`,
DROP `typicalResponse`,
DROP `typicalResponseDesc`,
DROP `muscleHistory`,
DROP `muscleHistoryDesc`,
DROP `surgicalHistory`,
DROP `surgicalHistoryDesc`,
DROP `preAdmTest`,
DROP `preAdmTestDesc`,
DROP `proposedAnesthetic`,
DROP `proposedAnestheticDesc`,
DROP `proposedAnestheticGA`,
DROP `proposedAnestheticMAC`,
DROP `proposedAnestheticLOC`,
DROP `outPtSurgery`,
DROP `outPtSurgeryDesc`,
DROP `changesInHp`,
DROP `changesInHpDesc`,
DROP `general`,
DROP `generalDesc`,
DROP `looseTeeth`,
DROP `looseTeethDesc`,
DROP `heent`,
DROP `heentDesc`,
DROP `heart`,
DROP `heartDesc`,
DROP `lungs`,
DROP `lungsDesc`,
DROP `regional`,
DROP `regionalDesc`,
DROP `otherHistory`;
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `history_physicial_clearance`
ADD `cadMI` VARCHAR(255) NOT NULL AFTER `date_of_h_p`,
ADD `cadMIDesc` TEXT NOT NULL AFTER `cadMI`,
ADD `cvaTIA` VARCHAR(255) NOT NULL AFTER `cadMIDesc`,
ADD `cvaTIADesc` TEXT NOT NULL AFTER `cvaTIA`,
ADD `htnCP` VARCHAR(255) NOT NULL AFTER `cvaTIADesc`,
ADD `htnCPDesc` TEXT NOT NULL AFTER `htnCP`,
ADD `anticoagulationTherapy` VARCHAR(255) NOT NULL AFTER `htnCPDesc`,
ADD `anticoagulationTherapyDesc` TEXT NOT NULL AFTER `anticoagulationTherapy`,
ADD `respiratoryAsthma` VARCHAR(255) NOT NULL AFTER `anticoagulationTherapyDesc`,
ADD `respiratoryAsthmaDesc` TEXT NOT NULL AFTER `respiratoryAsthma`,
ADD `arthritis` VARCHAR(255) NOT NULL AFTER `respiratoryAsthmaDesc`,
ADD `arthritisDesc` TEXT NOT NULL AFTER `arthritis`,
ADD `diabetes` VARCHAR(255) NOT NULL AFTER `arthritisDesc`,
ADD `diabetesDesc` TEXT NOT NULL AFTER `diabetes`,
ADD `recreationalDrug` VARCHAR(255) NOT NULL AFTER `diabetesDesc`,
ADD `recreationalDrugDesc` TEXT NOT NULL AFTER `recreationalDrug`,
ADD `giGerd` VARCHAR(255) NOT NULL AFTER `recreationalDrugDesc`,
ADD `giGerdDesc` TEXT NOT NULL AFTER `giGerd`,
ADD `ocular` VARCHAR(255) NOT NULL AFTER `giGerdDesc`,
ADD `ocularDesc` TEXT NOT NULL AFTER `ocular`,
ADD `kidneyDisease` VARCHAR(255) NOT NULL AFTER `ocularDesc`,
ADD `kidneyDiseaseDesc` TEXT NOT NULL AFTER `kidneyDisease`,
ADD `hivAutoimmune` VARCHAR(255) NOT NULL AFTER `kidneyDiseaseDesc`,
ADD `hivAutoimmuneDesc` TEXT NOT NULL AFTER `hivAutoimmune`,
ADD `historyCancer` VARCHAR(255) NOT NULL AFTER `hivAutoimmuneDesc`,
ADD `historyCancerDesc` TEXT NOT NULL AFTER `historyCancer`,
ADD `organTransplant` VARCHAR(255) NOT NULL AFTER `historyCancerDesc`,
ADD `organTransplantDesc` TEXT NOT NULL AFTER `organTransplant`,
ADD `badReaction` VARCHAR(255) NOT NULL AFTER `organTransplantDesc`,
ADD `badReactionDesc` TEXT NOT NULL AFTER `badReaction`,
ADD `otherHistoryPhysical` TEXT NOT NULL AFTER `badReactionDesc`,
ADD `wearContactLenses` VARCHAR(255) NOT NULL AFTER `otherHistoryPhysical`,
ADD `wearContactLensesDesc` TEXT NOT NULL AFTER `wearContactLenses`,
ADD `smoking` VARCHAR(255) NOT NULL AFTER `wearContactLensesDesc`,
ADD `smokingDesc` TEXT NOT NULL AFTER `smoking`,
ADD `drinkAlcohal` VARCHAR(255) NOT NULL AFTER `smokingDesc`,
ADD `drinkAlcohalDesc` TEXT NOT NULL AFTER `drinkAlcohal`,
ADD `haveAutomatic` VARCHAR(255) NOT NULL AFTER `drinkAlcohalDesc`,
ADD `haveAutomaticDesc` TEXT NOT NULL AFTER `haveAutomatic`,
ADD `medicalHistoryObtained` VARCHAR(255) NOT NULL AFTER `haveAutomaticDesc`,
ADD `medicalHistoryObtainedDesc` TEXT NOT NULL AFTER `medicalHistoryObtained`,
ADD `otherNotes` TEXT NOT NULL AFTER `medicalHistoryObtainedDesc`,
ADD `signSurgeon1Id` INT(11) NOT NULL AFTER `otherNotes`,  
ADD `signSurgeon1FirstName` VARCHAR(255) NOT NULL AFTER `signSurgeon1Id`,  
ADD `signSurgeon1MiddleName` VARCHAR(255) NOT NULL AFTER `signSurgeon1FirstName`,  
ADD `signSurgeon1LastName` VARCHAR(255) NOT NULL AFTER `signSurgeon1MiddleName`,  
ADD `signSurgeon1Status` VARCHAR(5) NOT NULL AFTER `signSurgeon1LastName`,  
ADD `signSurgeon1DateTime` DATETIME NOT NULL AFTER `signSurgeon1Status`,  
ADD `signAnesthesia1Id` INT(11) NOT NULL AFTER `signSurgeon1DateTime`,  
ADD `signAnesthesia1FirstName` VARCHAR(255) NOT NULL AFTER `signAnesthesia1Id`,  
ADD `signAnesthesia1MiddleName` VARCHAR(255) NOT NULL AFTER `signAnesthesia1FirstName`,  
ADD `signAnesthesia1LastName` VARCHAR(255) NOT NULL AFTER `signAnesthesia1MiddleName`,  
ADD `signAnesthesia1Status` VARCHAR(5) NOT NULL AFTER `signAnesthesia1LastName`,  
ADD `signAnesthesia1DateTime` DATETIME NOT NULL AFTER `signAnesthesia1Status`;
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 53 run OK";

?>

<html>
<head>
<title>Update 53</title>
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







