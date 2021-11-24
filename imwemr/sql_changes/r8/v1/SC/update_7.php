<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "CREATE TABLE `schedule_icon_list_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon_name` varchar(50) NOT NULL,
  `icon_order` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;";
$sql_sts=imw_query($qry) or $msg_info[] = imw_error();

if($sql_sts)
{
	imw_query("INSERT INTO `schedule_icon_list_order` (`id`, `icon_name`, `icon_order`, `user_id`) VALUES (NULL, 'A&P', '1', ''), (NULL, 'Appt. Hx', '2', ''), (NULL, 'Cancel', '3', ''), (NULL, 'Check In', '4', ''), (NULL, 'Check Out', '5', ''), (NULL, 'CL-Sply', '6', ''), (NULL, 'CL-Disp', '7', ''), (NULL, 'Confirm', '8', ''), (NULL, 'eRx', '9', ''), (NULL, 'Facesheet', '10', ''), (NULL, 'First Available', '11', ''), (NULL, 'Make Appointment', '12', ''), (NULL, 'New Patient', '13', ''), (NULL, 'PMT', '14', ''), (NULL, 'Recall', '15', ''), (NULL, 'Super Bill', '16', ''), (NULL, 'Save', '17', ''), (NULL, 'Reschedule', '18', ''), (NULL, 'Add Appt', '19', '');") or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 7 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 7 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 7</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>