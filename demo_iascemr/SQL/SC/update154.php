<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "CREATE TABLE `nurse_profile_tbl` (
  `profile_id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nurseId` int(10) NOT NULL DEFAULT '0',
  `labTest` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `ekg` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `consentSign` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `hp` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `admitted2Hospital` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `healthQuestionnaire` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `standingOrders` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `patVoided` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `hearingAids` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `hearingAidsRemoved` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `anyPain` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `painLevel` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `painLocation` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `doctorNotified` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `denture` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `dentureRemoved` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `foodDrinkToday` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `listFoodTake` text COLLATE latin1_general_ci NOT NULL,
  `preOpComments` text COLLATE latin1_general_ci NOT NULL,
  `reason` text COLLATE latin1_general_ci NOT NULL,
  `relivedNurseIdPre` int(10) NOT NULL DEFAULT '0',
  `comments` text COLLATE latin1_general_ci NOT NULL,
  `chbx_saline_lockStart` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `chbx_saline_lock` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `ivSelection` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ivSelectionOther` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ivSelectionSide` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `chbx_KVO` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `chbx_rate` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `txtbox_rate` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `chbx_flu` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `txtbox_flu` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `gauge` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `gauge_other` text COLLATE latin1_general_ci NOT NULL,
  `txtbox_other_new` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `postOpSite` text COLLATE latin1_general_ci NOT NULL,
  `nourishKind` text COLLATE latin1_general_ci NOT NULL,
  `removedIntact` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `patient_aox3` char(3) COLLATE latin1_general_ci NOT NULL,
  `other_mental_status` text COLLATE latin1_general_ci NOT NULL,
  `patientReleased2Adult` char(3) COLLATE latin1_general_ci NOT NULL,
  `patientsRelation` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `patientsRelationOther` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `relivedNurseId` int(11) NOT NULL,
  `recoveryComments` text COLLATE latin1_general_ci NOT NULL,
  `nurse_profile_sign` longtext COLLATE latin1_general_ci NOT NULL,
  `nurse_profile_sign_path` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date_time` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 154 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 154 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 154</title>
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