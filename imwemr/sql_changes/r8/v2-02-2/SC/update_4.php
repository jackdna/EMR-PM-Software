<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql1 = "CREATE TABLE `updox_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_type` varchar(255) NOT NULL,
  `request_url` varchar(255) NOT NULL,
  `request_data` text NOT NULL,
  `request_date_time` datetime NOT NULL,
  `response_data` text NOT NULL,
  `response_date_time` datetime NOT NULL,
  `response_status` varchar(50) NOT NULL,
  `operator_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);";
imw_query($sql1) or $error[] = imw_error();

$sql1 = "ALTER TABLE patient_data ADD pt_sync_updox INT(1) NOT NULL, 
		ADD pt_sync_updox_date_time datetime NOT NULL,
		ADD INDEX pt_sync_updox(pt_sync_updox);";
imw_query($sql1) or $error[] = imw_error();


$sql1 = "ALTER TABLE schedule_appointments 
			ADD appt_sync_updox INT(1) NOT NULL,
			ADD appt_sync_updox_date_time datetime NOT NULL,
			ADD updox_appt_id VARCHAR(255) NOT NULL,
			ADD INDEX appt_sync_updox(appt_sync_updox),
			ADD INDEX updox_appt_id(updox_appt_id);";
imw_query($sql1) or $error[] = imw_error();

$sql1 = "ALTER TABLE `slot_procedures` ADD INDEX proc_type(proc_type);";
imw_query($sql1) or $error[] = imw_error();


if(count($error)>0)
{
	$error[] = "<br><br><b>Update 4 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 4 Success.</b>";
	$color = "green";	
}



?>

<html>
<head>
<title>Update 4</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>