<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE iolink_patient_alert_tbl add Index patient_id (patient_id);";

$sql[] = "CREATE TABLE `user_log` (
`user_id` int(11) NOT NULL,
`user_token` varchar(45) DEFAULT NULL,
`active` tinyint(4) NOT NULL DEFAULT '0',
`createdOn` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`modifiedOn` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);";

$sql[] = "CREATE TABLE `device` (
`id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`deviceId` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`devicetoken` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`imei` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`devicetype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`active` tinyint(1) NOT NULL,
`createdOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 188 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 188 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 188</title>
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