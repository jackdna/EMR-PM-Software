<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `localanesthesiarecord` ADD `bp_p_rr_time` TIME NOT NULL AFTER `sao` "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `anesthesia_profile_tbl` ADD  `confirmIPPSC_signin` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `anesthesia_profile_tbl` ADD  `siteMarked` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `anesthesia_profile_tbl` ADD  `patientAllergies` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `anesthesia_profile_tbl` ADD  `difficultAirway` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `anesthesia_profile_tbl` ADD  `anesthesiaSafety` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `anesthesia_profile_tbl` ADD  `allMembersTeam` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `anesthesia_profile_tbl` ADD  `riskBloodLoss` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `anesthesia_profile_tbl` ADD  `bloodLossUnits` VARCHAR( 25 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();



$sql1="ALTER TABLE `localanesthesiarecord` ADD  `reliefNurseId` INT NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `confirmIPPSC_signin` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `siteMarked` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `patientAllergies` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `difficultAirway` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `riskBloodLoss` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `bloodLossUnits` VARCHAR( 25 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `anesthesiaSafety` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `allMembersTeam` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `signAnesthesia4Id` INT NOT NULL COMMENT 'Signature Added at Top for Form version no. 2 or above'"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `signAnesthesia4FirstName` VARCHAR(255) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `signAnesthesia4MiddleName` VARCHAR(255) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `signAnesthesia4LastName` VARCHAR(255) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `signAnesthesia4Status` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `signAnesthesia4DateTime` DATETIME NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `version_num` TINYINT NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `localanesthesiarecord` ADD `version_date_time` DATETIME NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE `genanesthesiarecord` ADD  `reliefNurseId` INT NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `confirmIPPSC_signin` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `siteMarked` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `patientAllergies` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `difficultAirway` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `riskBloodLoss` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `bloodLossUnits` VARCHAR( 25 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `anesthesiaSafety` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `allMembersTeam` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `signAnesthesia2Id` INT NOT NULL COMMENT 'Signature Added at Top for Form version no. 2 or above'"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `signAnesthesia2FirstName` VARCHAR(255) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `signAnesthesia2MiddleName` VARCHAR(255) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `signAnesthesia2LastName` VARCHAR(255) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `signAnesthesia2Status` VARCHAR(5) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `signAnesthesia2DateTime` DATETIME NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `version_num` TINYINT NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `genanesthesiarecord` ADD `version_date_time` DATETIME NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `surgical_check_list` ADD  `version_num` TINYINT NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `surgical_check_list` ADD  `version_date_time` DATETIME NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE surgical_check_list Set version_num = 1, version_date_time='".date('Y-m-d H:i:s')."' Where (form_status = 'completed' || form_status = 'not completed') And version_num = 0  "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE localanesthesiarecord Set version_num = 1, version_date_time='".date('Y-m-d H:i:s')."' Where (form_status = 'completed' || form_status = 'not completed') And version_num = 0  "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE genanesthesiarecord Set version_num = 1, version_date_time='".date('Y-m-d H:i:s')."' Where (form_status = 'completed' || form_status = 'not completed') And version_num = 0  "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1=" ALTER TABLE  `surgery_cost` ADD  `datetime` DATETIME NOT NULL ,
ADD  `previous_cost` TEXT NOT NULL COMMENT  'serialize string of previous values',
ADD  `previous_datetime` DATETIME NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 103 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 103 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 103</title>
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