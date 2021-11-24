<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
CREATE TABLE `laserpredefine_procedure_notes` (
  `predefine_procedure_notes_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`predefine_procedure_notes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `surgeonprofile` ADD `medicalEvaluation` TEXT NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `laser_procedure_patient_table` ADD `laser_medical_evaluation` TEXT NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `laser_procedure_patient_table` ADD `laser_procedure_notes` TEXT NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();


$sql = "
ALTER TABLE `surgeonprofileprocedure` ADD `cpt_id` VARCHAR( 255 ) NOT NULL ,
ADD `cpt_id_default` VARCHAR( 255 ) NOT NULL ,
ADD `dx_id` VARCHAR( 255 ) NOT NULL ,
ADD `dx_id_default` VARCHAR( 255 ) NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `laser_procedure_patient_table` ADD `stable_chbx` VARCHAR( 255 ) NOT NULL ,
ADD `stable_other_chbx` VARCHAR( 255 ) NOT NULL ,
ADD `stable_other_txtbx` TEXT NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 22 run OK";

?>

<html>
<head>
<title>Mysql Updates For Query Optimization</title>
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







