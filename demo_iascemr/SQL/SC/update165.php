<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE  `iolink_patient_prescription_medication` ADD  `endpoint_med_date_time` DATETIME NOT NULL;";
$sql[] = "ALTER TABLE  `iolink_patient_allergy` ADD  `endpoint_allergy_date_time` DATETIME NOT NULL;";
$sql[] = "ALTER TABLE  `patient_in_waiting_tbl` ADD INDEX idoc_sch_athena_id(  `idoc_sch_athena_id` );";
$sql[] = "ALTER TABLE  `iolink_scan_consent` ADD INDEX  `wait_pt_doc_scan` (  `patient_in_waiting_id` ,  `patient_id` ,  `document_name` ,  `iolink_scan_folder_name` );";
foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 165 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 165 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 165</title>
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