<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql="CREATE TABLE `patient_rx_notification_consent` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `rx_notification_consent` tinyint(5) NOT NULL DEFAULT 0,
  `patient_id` int(11) NOT NULL DEFAULT 0,
  `operator_id` int(11) NOT NULL DEFAULT 0,
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT 0
) ";
imw_query($sql) or $msg_info[] = imw_error();


$sql1="CREATE TABLE `rx_notification_consent_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `request_header` text NOT NULL,
  `request_url` varchar(255) NOT NULL,
  `request_to` varchar(255) NOT NULL,
  `request_data` text NOT NULL,
  `request_date_time` datetime NOT NULL,
  `response_data` text NOT NULL,
  `response_date_time` datetime NOT NULL,
  `operator_id` int(11) NOT NULL 
) ";
imw_query($sql1) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 18 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 18 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 18</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
