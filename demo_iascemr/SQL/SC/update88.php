<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include("../../common/conDb.php");

$sql1="ALTER TABLE `left_navigation_forms` ADD `transfer_and_followups_form` VARCHAR( 5 ) NOT NULL DEFAULT 'true' AFTER `post_op_instruction_sheet_form` ";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="CREATE TABLE transfer_followups (
  transferFollowupId int(11) NOT NULL AUTO_INCREMENT,
  confirmation_id int(11) NOT NULL,
  transfer_reason varchar(30) NOT NULL,
  transfer_reason_detail text NOT NULL,
  hospital_contacted tinyint(1) NOT NULL,
  form_status varchar(30) NOT NULL,
  contacted_time time NOT NULL,
  signNurseId int(11) NOT NULL,
  signNurseFirstName varchar(255) NOT NULL,
  signNurseMiddleName varchar(255) NOT NULL,
  signNurseLastName varchar(255) NOT NULL,
  signNurseStatus varchar(5) NOT NULL,
  signNurseDateTime datetime NOT NULL,
  transfer_method varchar(64) NOT NULL,
  ambulance_provider text NOT NULL,
  lv_running tinyint(1) NOT NULL COMMENT '0-default|1-No|2-Yes ',
  airway_support tinyint(1) NOT NULL COMMENT '0-default|1-No|2-Yes ',
  o2at varchar(255) NOT NULL,
  transfer_forms tinyint(1) NOT NULL COMMENT '0-default|1-NA|2-Not Sent|3 for Sent ',
  demographics tinyint(1) NOT NULL COMMENT '0-default|1-NA|2-Not Sent|3 for Sent ',
  chart_note tinyint(1) NOT NULL COMMENT '0-default|1-NA|2-Not Sent|3 for Sent ',
  lab_work tinyint(1) NOT NULL COMMENT '0-default|1-NA|2-Not Sent|3 for Sent ',
  ekg tinyint(1) NOT NULL COMMENT '0-default|1-NA|2-Not Sent|3 for Sent ',
  advance_directive tinyint(1) NOT NULL COMMENT '0-default|1-NA|2-Not Sent|3 for Sent ',
  cpr_report tinyint(1) NOT NULL COMMENT '0-default|1-NA|2-Not Sent|3 for Sent ',
  patient_belongings text NOT NULL,
  additional_comments text NOT NULL,
  surgeon_reassessment tinyint(1) NOT NULL,
  signSurgeon1Id int(11) NOT NULL,
  signSurgeon1FirstName varchar(255) NOT NULL,
  signSurgeon1MiddleName varchar(255) NOT NULL,
  signSurgeon1LastName varchar(255) NOT NULL,
  signSurgeon1Status varchar(5) NOT NULL,
  signSurgeon1DateTime datetime NOT NULL,
  summary_of_care_time time NOT NULL,
  summary_of_care_notes text NOT NULL,
  followup_status_filled tinyint(1) NOT NULL,
  date_discharge_from_hospital date NOT NULL,
  fDate date NOT NULL,
  signNurse1Id int(11) NOT NULL,
  signNurse1FirstName varchar(255) NOT NULL,
  signNurse1MiddleName varchar(255) NOT NULL,
  signNurse1LastName varchar(255) NOT NULL,
  signNurse1Status varchar(5) NOT NULL,
  signNurse1DateTime datetime NOT NULL,
  discharge_comments text NOT NULL,
  PRIMARY KEY (transferFollowupId)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1; ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `preophealthquestionnaire` ADD `smokeAdvise` VARCHAR( 5 ) NOT NULL AFTER `smokeHowMuch`  ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `preophealthquestionnaire` ADD `alchoholAdvise` VARCHAR( 5 ) NOT NULL AFTER `alchoholHowMuch`  ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `procedures`  ADD `codeFacility` VARCHAR(255) NOT NULL AFTER `code`,  ADD `codeAnesthesia` VARCHAR(255) NOT NULL AFTER `codeFacility`,  ADD `codePractice` VARCHAR(255) NOT NULL AFTER `codeAnesthesia`  ";
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error())
{
	$msg_info[] = "<br><br><b>Update 88 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 88 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 88</title>
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