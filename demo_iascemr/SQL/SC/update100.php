<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE  `patient_data_tbl` CHANGE  `nextGenPersonId`  `imwPatientId` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `patientconfirmation` CHANGE  `nextGenPersonId`  `imwPatientId` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `stub_tbl` CHANGE  `nextGenPersonId`  `imwPatientId` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `iolink_patient_alert_tbl` CHANGE  `next_gen_alert_id`  `imw_alert_id` BIGINT( 11 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 100 Failed! </b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 100 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 100</title>
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