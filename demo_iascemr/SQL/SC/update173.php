<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");

$msg_info = array();
$sql[] = " ALTER TABLE  `iolink_scan_consent` ADD  `pdf_external_id` VARCHAR( 255 ) NOT NULL , ADD INDEX pdf_external_id( pdf_external_id );";
$sql[] = " ALTER TABLE  `iolink_patient_allergy` ADD  `allergy_external_id` VARCHAR( 255 ) NOT NULL , ADD INDEX allergy_external_id( allergy_external_id );";
$sql[] = " ALTER TABLE  `iolink_patient_prescription_medication` ADD  `medication_external_id` VARCHAR( 255 ) NOT NULL , ADD INDEX medication_external_id( medication_external_id );";

$sql[] = " ALTER TABLE  `scan_upload_tbl` ADD  `pdf_external_id` VARCHAR( 255 ) NOT NULL , ADD INDEX pdf_external_id( pdf_external_id );";
$sql[] = " ALTER TABLE  `patient_allergies_tbl` ADD  `allergy_external_id` VARCHAR( 255 ) NOT NULL , ADD INDEX allergy_external_id( allergy_external_id );";
$sql[] = " ALTER TABLE  `patient_prescription_medication_healthquest_tbl` ADD  `medication_external_id` VARCHAR( 255 ) NOT NULL , ADD INDEX medication_external_id( medication_external_id );";
$sql[] = " ALTER TABLE  `patient_anesthesia_medication_tbl` ADD  `medication_external_id` VARCHAR( 255 ) NOT NULL , ADD INDEX medication_external_id( medication_external_id );";

$sql[] = " ALTER TABLE  `surgerycenter` ADD  `small_label_enable_patient_dob` VARCHAR( 5 ) NOT NULL , ADD  `large_label_enable_patient_dob` VARCHAR( 5 ) NOT NULL ;";
$sql[] = " ALTER TABLE  `surgerycenter` ADD  `small_label_enable_site` VARCHAR( 5 ) NOT NULL , ADD  `large_label_enable_site` VARCHAR( 5 ) NOT NULL ;";

$sql[] = " ALTER TABLE `operativereport`  ADD `opreport_pdf_file_path` VARCHAR(255) NOT NULL,  
			ADD `opreport_inte_sync_status` INT(11) NOT NULL,  
			ADD INDEX opreport_pdf_file_path(opreport_pdf_file_path),  
			ADD INDEX opreport_inte_sync_status(opreport_inte_sync_status);"; 

$sql[] = " ALTER TABLE `operativereport` 
			ADD `opreport_pdf_save_date_time` DATETIME NOT NULL,  
			ADD `opreport_inte_sync_date_time` DATETIME NOT NULL;"; 

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 173 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 173 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 173</title>
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