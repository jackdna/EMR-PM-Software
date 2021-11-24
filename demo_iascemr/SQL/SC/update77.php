<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");


// This update is created for internal use 
// to check source of prefilMedicationStatus field
// Possible Values 0|1
// 0 set for all previous records and where prefilMedicationStatus updated from chart 
// 1 set for records where prefilMedicationStatus
// updated from signAll Pre Op Orders Surgeon page

$sql1="ALTER TABLE  `preopphysicianorders` ADD  `prefilMedicationStatusSource` TINYINT NOT NULL COMMENT  'value set to 0 when source is chart and set to 1 when source is sign all pre op orders' AFTER  `prefilMedicationStatus`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `laser_procedure_patient_table` ADD  `prefilMedicationStatusSource` TINYINT NOT NULL COMMENT  'value set to 0 when source is chart and set to 1 when source is sign all pre op orders' AFTER  `prefilMedicationStatus`"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `laser_procedure_patient_table` ADD  `saveFromChart` TINYINT NOT NULL COMMENT  'value set to 1 if save button used from sign all pre op orders' AFTER  `form_status`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopphysicianorders` ADD  `saveFromChart` TINYINT NOT NULL COMMENT  'value set to 1 if save button used from sign all pre op orders' AFTER  `form_status`"; 
imw_query($sql1)or $msg_info[] = imw_error();




if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 77 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 77 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 77</title>
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