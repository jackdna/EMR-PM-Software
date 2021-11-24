<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE  `patientconfirmation` ADD  `surgeon_time_in_mins` DECIMAL NOT NULL ,
		ADD  `surgery_time_in_mins` DECIMAL NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();



$sql1="ALTER TABLE  `procedurescategory` ADD  `isMisc` TINYINT( 2 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `left_navigation_forms` ADD  `injection_misc_form` VARCHAR( 5 ) NOT NULL DEFAULT  'true'"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="CREATE TABLE `inj_misc_procedure_template` (
  `templateID` int(11) NOT NULL AUTO_INCREMENT,
  `templateName` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `procedureID` int(11) NOT NULL,
  `surgeonID` text COLLATE latin1_general_ci NOT NULL,
  `instructionSheetID` int(11) NOT NULL,
  `consentTemplateId` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `timeoutReq` tinyint(2) NOT NULL,
  `preOpMeds` longtext COLLATE latin1_general_ci NOT NULL,
  `intravitrealMeds` longtext COLLATE latin1_general_ci NOT NULL,
  `postOpMeds` longtext COLLATE latin1_general_ci NOT NULL,
  `cpt_id` text COLLATE latin1_general_ci NOT NULL,
  `cpt_id_default` text COLLATE latin1_general_ci NOT NULL,
  `dx_id` text COLLATE latin1_general_ci NOT NULL,
  `dx_id_default` text COLLATE latin1_general_ci NOT NULL,
  `dx_id_icd10` text COLLATE latin1_general_ci NOT NULL,
  `dx_id_default_icd10` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`templateID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;"; 
imw_query($sql1)or $msg_info[] = imw_error();





if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 104 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 104 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 104</title>
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