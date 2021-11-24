<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$msg_info = array();
$sql = array();
$sql[]="ALTER TABLE `patient_in_waiting_tbl` ADD `iosync_hl7` DATETIME NOT NULL COMMENT 'Timestamp when iasc sync is run by HL7 message generation operation' AFTER `iAscSyncroStatusDateTime`";
$sql[]="ALTER TABLE `patient_in_waiting_tbl` ADD `external_id` VARCHAR(255) NOT NULL COMMENT 'External Application Id, Added from HL7 message'";
$sql[]="ALTER TABLE `facility_tbl` ADD `external_id` VARCHAR(255) NOT NULL COMMENT 'External ASC/Facility Id, Added from HL7 message'";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 1 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 128 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 1</title>
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