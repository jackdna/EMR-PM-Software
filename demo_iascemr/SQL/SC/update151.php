<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `patientconfirmation` ADD INDEX(`surgeonId`);";
$sql[] = "ALTER TABLE `patientconfirmation` ADD INDEX(`anesthesiologist_id`);";
$sql[] = "ALTER TABLE `patientconfirmation` ADD INDEX(`patient_primary_procedure_id`);";
$sql[] = "ALTER TABLE `injection` ADD INDEX(`confirmation_id`);";
$sql[] = "ALTER TABLE `vision_success` ADD INDEX(`confirmation_id`);";
$sql[] = "ALTER TABLE `patientconfirmation` ADD `vcna_export_status` TINYINT(1) NOT NULL";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 151 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 151 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 149</title>
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