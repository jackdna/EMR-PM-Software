<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql = "CREATE TABLE `surgical_check_list` ( `check_list_id` BIGINT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `patient_id` INT(11) NOT NULL, `confirmation_id` INT(11) NOT NULL, `form_status` VARCHAR(20) NOT NULL, `user_id` INT(11) NOT NULL, `save_date_time` DATETIME NOT NULL, `identity` VARCHAR(5) NOT NULL, `procedureAndProcedureSite` VARCHAR(5) NOT NULL, `siteMarkedByPerson` VARCHAR(5) NOT NULL, `consent` VARCHAR(5) NOT NULL, `historyAndPhysical` VARCHAR(5) NOT NULL, `preanesthesiaAssessment` VARCHAR(5) NOT NULL, `diagnosticAndRadiologic` VARCHAR(5) NOT NULL, `bloodProduct` VARCHAR(5) NOT NULL, `anySpecialEquipment` VARCHAR(5) NOT NULL, `betaBlockerMedication` VARCHAR(5) NOT NULL, `venousThromboembolism` VARCHAR(5) NOT NULL, `jormothermiaMeasures` VARCHAR(5) NOT NULL, `confirmIPPSC_signin` VARCHAR(5) NOT NULL, `siteMarked` VARCHAR(5) NOT NULL, `patientAllergies` VARCHAR(5) NOT NULL, `difficultAirway` VARCHAR(5) NOT NULL, `riskBloodLoss` VARCHAR(5) NOT NULL, `bloodLossUnits` VARCHAR(25) NOT NULL, `anesthesiaSafety` VARCHAR(5) NOT NULL, `allMembersTeam` VARCHAR(5) NOT NULL, `introducationTeamMember` VARCHAR(5) NOT NULL, `confirmIPPSC` VARCHAR(5) NOT NULL, `siteMarkedAndVisible` VARCHAR(5) NOT NULL, `relevantImages` VARCHAR(5) NOT NULL, `anyEquipmentConcern` VARCHAR(5) NOT NULL, `criticalStep` VARCHAR(5) NOT NULL, `caseDuration` VARCHAR(5) NOT NULL, `anticipatedBloodLoss` VARCHAR(5) NOT NULL, `antibioticProphylaxis` VARCHAR(5) NOT NULL, `anesthesiaAdditionalConcerns` VARCHAR(5) NOT NULL, `sterilizationIndicators` VARCHAR(5) NOT NULL, `nurseAdditionalConcerns` VARCHAR(5) NOT NULL, `nameOperativeProcedure` VARCHAR(5) NOT NULL, `specimensIdentified` VARCHAR(5) NOT NULL, `anyEquipmentProblem` VARCHAR(5) NOT NULL, `comments` TEXT NOT NULL ) ENGINE = MyISAM;";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 6 run OK";

?>

<html>
<head>
<title>Mysql Updates For Create Table in surgical_check_list</title>
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







