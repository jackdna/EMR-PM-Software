<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1 = "ALTER TABLE `left_navigation_forms` ADD `medication_reconciliation_sheet_form` VARCHAR( 5 ) NOT NULL DEFAULT 'true' AFTER `post_op_instruction_sheet_form` ";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1 = "CREATE TABLE `patient_medication_reconciliation_sheet` (
  `recon_sheet_id` int(11) NOT NULL AUTO_INCREMENT,
  `confirmation_id` int(11) NOT NULL,
  `form_status` varchar(15) NOT NULL,
  `aspirin` char(3) NOT NULL,
  `allergic_med` char(3) NOT NULL,
  `allergic_med_detail` longtext NOT NULL,
  `allergic_betadine` char(3) NOT NULL,
  `allergic_latex` char(3) NOT NULL,
  `drop_schedule` tinyint(2) NOT NULL,
  `start_post_op_drops` tinyint(2) NOT NULL,
  `resume_med` tinyint(2) NOT NULL,
  `discontinue` longtext NOT NULL,
  `signNurseId` int(11) NOT NULL,
  `signNurseFirstName` varchar(255) NOT NULL,
  `signNurseMiddleName` varchar(255) NOT NULL,
  `signNurseLastName` varchar(255) NOT NULL,
  `signNurseStatus` varchar(5) NOT NULL,
  `signNurseDateTime` datetime NOT NULL,
  `signSurgeon1Id` int(11) NOT NULL,
  `signSurgeon1FirstName` varchar(255) NOT NULL,
  `signSurgeon1MiddleName` varchar(255) NOT NULL,
  `signSurgeon1LastName` varchar(255) NOT NULL,
  `signSurgeon1Status` varchar(5) NOT NULL,
  `signSurgeon1DateTime` datetime NOT NULL,
  PRIMARY KEY (`recon_sheet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1 = "ALTER TABLE  `patient_prescription_medication_healthquest_tbl` ADD  `prescription_medication_reason` TEXT NOT NULL AFTER  `prescription_medication_desc`";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1 = "ALTER TABLE  `patient_prescription_medication_tbl` ADD  `prescription_medication_reason` TEXT NOT NULL AFTER  `prescription_medication_desc`";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "ALTER TABLE `patient_physician_orders` ADD `physician_order_not_given` VARCHAR( 10 ) NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1 = "ALTER TABLE  `anesthesia_profile_tbl` 
ADD  `topical_bupivacaine75` CHAR( 3 ) NOT NULL ,
ADD  `topical_marcaine75` CHAR( 3 ) NOT NULL ,
ADD  `block1_bupivacaine75` CHAR( 3 ) NOT NULL ,
ADD  `block1_marcaine75` CHAR( 3 ) NOT NULL ,
ADD  `block2_bupivacaine75` CHAR( 3 ) NOT NULL ,
ADD  `block2_marcaine75` CHAR( 3 ) NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1 = "ALTER TABLE  `localanesthesiarecord` 
ADD  `bupivacaine75` CHAR( 3 ) NOT NULL ,
ADD  `marcaine75` CHAR( 3 ) NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 117 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 117 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 117</title>
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