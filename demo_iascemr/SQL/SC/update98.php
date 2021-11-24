<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
require("../../common/conDb.php");

$sql1="ALTER TABLE  `operatingroomrecords` CHANGE  `Diopter`  `Diopter` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `history_physicial_clearance` ADD  `chart_copied` TINYINT( 1 ) NOT NULL , ADD  `copied_dos` DATE NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `operatingroomrecords` ADD  `Trimaxi` CHAR( 3 ) NOT NULL ,
ADD  `TrimaxiList` VARCHAR( 20 ) NOT NULL ,
ADD  `PhenylLido` CHAR( 3 ) NOT NULL ,
ADD  `PhenylLidoList` VARCHAR( 20 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `patient_in_waiting_tbl` ADD  `iolink_allergiesNKDA_status` VARCHAR( 10 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `surgeonprofile` ADD `del_status` VARCHAR( 20 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `procedureprofile` 
ADD  `save_date` DATETIME NOT NULL ,
ADD  `save_status` INT( 10 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

//Surgical Check List Table 
$sql1="ALTER TABLE  `surgical_check_list` ADD  `checklist_old_new` VARCHAR( 5 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

//Special update - Surgical Check List Table 
$sql1="UPDATE surgical_check_list SET checklist_old_new = 'old' Where checklist_old_new = '' AND (form_status = 'completed' OR form_status = 'not completed' ) "; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `laser_procedure_patient_table` ADD  `verified_nurse_timeout` DATETIME NOT NULL , 
ADD  `verified_surgeon_timeout` DATETIME NOT NULL ,
ADD  `proc_start_time` DATETIME NOT NULL ,
ADD  `proc_end_time` DATETIME NOT NULL ,
ADD  `prelaserVitalSignTime` DATETIME NOT NULL ,
ADD  `postlaserVitalSignTime` DATETIME NOT NULL ,
ADD  `asa_status` VARCHAR( 10 ) NOT NULL
"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `procedureprofile` ADD  `cpt_id_anes` TEXT NOT NULL AFTER  `cpt_id_default` , ADD  `cpt_id_anes_default` TEXT NOT NULL AFTER  `cpt_id_anes`"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `surgeonprofileprocedure` ADD  `cpt_id_anes` TEXT NOT NULL AFTER  `cpt_id_default` , ADD  `cpt_id_anes_default` TEXT NOT NULL AFTER  `cpt_id_anes` "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `procedures` DROP  `codeAnesthesia`"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `superbill_tbl` ADD  `isAnesthesia` INT(5) NOT NULL AFTER  `confirmation_id`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `procedurescategory` ADD `del_status` VARCHAR( 10 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 98 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 98 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 98</title>
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