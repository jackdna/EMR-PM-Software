<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `iolink_patient_prescription_medication` ADD `prescription_medication_sig` TEXT NOT NULL AFTER `prescription_medication_desc` "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `patient_prescription_medication_healthquest_tbl` ADD `prescription_medication_sig` TEXT NOT NULL AFTER `prescription_medication_desc` "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `patient_anesthesia_medication_tbl` ADD `prescription_medication_sig` TEXT NOT NULL AFTER `prescription_medication_desc` "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `patient_prescription_medication_tbl` ADD `prescription_medication_sig` TEXT NOT NULL AFTER `prescription_medication_desc` "; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 123 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 123 Success.</b><br>".$message;
	$color = "green";			
}

?>
<html>
<head>
<title>Update 123</title>
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