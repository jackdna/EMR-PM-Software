<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");


$sql1="CREATE TABLE `injection` (
  `injId` int(11) NOT NULL AUTO_INCREMENT,
  `form_status` varchar(32) NOT NULL,
  `confirmation_id` int(11) NOT NULL,
  `preVitalTime` time NOT NULL,
  `preVitalBp` varchar(128) NOT NULL,
  `preVitalPulse` varchar(128) NOT NULL,
  `preVitalResp` varchar(128) NOT NULL,
  `preVitalSpo` varchar(128) NOT NULL,
  `timeoutReq` tinyint(2) NOT NULL,
  `timeoutTime` time NOT NULL,
  `timeoutProcVerified` tinyint(2) NOT NULL,
  `timeoutSiteVerified` tinyint(2) NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `chkConsentSigned` tinyint(2) NOT NULL,
  `procedureComments` text NOT NULL,
  `preOpMeds` longtext NOT NULL,
  `intravitrealMeds` longtext NOT NULL,
  `postOpMeds` longtext NOT NULL,
  `complications` char(4) NOT NULL,
  `comments` text NOT NULL,
  `postVitalTime` time NOT NULL,
  `postVitalBp` varchar(128) NOT NULL,
  `postVitalPulse` varchar(128) NOT NULL,
  `postVitalResp` varchar(128) NOT NULL,
  `postVitalSpo` varchar(128) NOT NULL,
  `postIop` varchar(64) NOT NULL,
  `postIopSite` varchar(128) NOT NULL,
  `postIopTime` time NOT NULL,
  `signSurgeon1Id` int(11) NOT NULL,
  `signSurgeon1FirstName` varchar(255) NOT NULL,
  `signSurgeon1MiddleName` varchar(255) NOT NULL,
  `signSurgeon1LastName` varchar(255) NOT NULL,
  `signSurgeon1Status` varchar(5) NOT NULL,
  `signSurgeon1DateTime` datetime NOT NULL,
  `signNurse1Id` int(11) NOT NULL,
  `signNurse1FirstName` varchar(255) NOT NULL,
  `signNurse1MiddleName` varchar(255) NOT NULL,
  `signNurse1LastName` varchar(255) NOT NULL,
  `signNurse1Status` varchar(5) NOT NULL,
  `signNurse1DateTime` datetime NOT NULL,
  `signNurse2Id` int(11) NOT NULL,
  `signNurse2FirstName` varchar(255) NOT NULL,
  `signNurse2MiddleName` varchar(255) NOT NULL,
  `signNurse2LastName` varchar(255) NOT NULL,
  `signNurse2Status` varchar(5) NOT NULL,
  `signNurse2DateTime` datetime NOT NULL,
  PRIMARY KEY (`injId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `preopnursingrecord` 
ADD  `signSurgeon1Id` INT NOT NULL ,
ADD  `signSurgeon1FirstName` VARCHAR( 255 ) NOT NULL ,
ADD  `signSurgeon1MiddleName` VARCHAR( 255 ) NOT NULL ,
ADD  `signSurgeon1LastName` VARCHAR( 255 ) NOT NULL ,
ADD  `signSurgeon1Status` VARCHAR( 5 ) NOT NULL ,
ADD  `signSurgeon1DateTime` DATETIME NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `inj_misc_procedure_template` ADD  `operativeReportID` INT NOT NULL AFTER  `instructionSheetID`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `patientconfirmation` ADD  `prim_proc_is_misc` VARCHAR( 10 ) NOT NULL AFTER  `patient_tertiary_procedure`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `patient_data_tbl` ADD `patient_image_path` VARCHAR( 255 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `patientconfirmation` 
		ADD `secondary_site` INT( 10 ) NOT NULL AFTER `surgeon_name` ,
		ADD `secondary_site_description` VARCHAR( 255 ) NOT NULL AFTER `secondary_site` "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `stub_tbl` 
		ADD `stub_secondary_site` VARCHAR( 255 ) NOT NULL AFTER `site` "; 
imw_query($sql1)or $msg_info[] = imw_error();


//REPORT GRAPH
$sql2="CREATE TABLE  `vision_success` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`confirmation_id` INT( 10 ) NOT NULL ,
`patientId` INT( 10 ) NOT NULL ,
`dos` DATE NOT NULL ,
`ascId` BIGINT( 10 ) NOT NULL ,
`site` VARCHAR( 20 ) NOT NULL ,
`vision_20_40` VARCHAR( 10 ) NOT NULL ,
`complication` VARCHAR( 10 ) NOT NULL ,
`status` TINYINT( 1 ) NOT NULL
) ENGINE = MYISAM ;"; 
imw_query($sql2)or $msg_info[] = imw_error();

$sql2="CREATE TABLE  `proceduregroup` (
`proceduresGroupId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`procedures` TEXT NOT NULL ,
`del_status` TINYINT NOT NULL
) ENGINE = MYISAM ;"; 
imw_query($sql2)or $msg_info[] = imw_error();


$sql2="ALTER TABLE  `vision_success` 
ADD  `procedure` INT NOT NULL ,
ADD  `surgeonId` INT NOT NULL "; 
imw_query($sql2)or $msg_info[] = imw_error();

$sql2="ALTER TABLE  `patientpreopmedication_tbl` CHANGE  `sourcePage`  `sourcePage` INT( 11 ) NOT NULL COMMENT  '0 for PreOp Physician Orders and 1 for Laser Procedure And 2 for Pre Op Nursing'"; 
imw_query($sql2)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 105 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 105 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 105</title>
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