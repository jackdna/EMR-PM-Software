<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql="ALTER TABLE `users` ADD `erp_user_postaladdresses_id` VARCHAR(200) NOT NULL, 
    ADD `erp_user_contact_id` VARCHAR(200) NOT NULL, 
    ADD `erp_user_id` VARCHAR(200) NOT NULL, 
    ADD `erp_doctor_id` VARCHAR(200) NOT NULL ";
imw_query($sql) or $msg_info[] = imw_error();

$sql1="CREATE TABLE `erp_api_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `request_header` text NOT NULL,
  `request_type` varchar(255) NOT NULL,
  `request_url` varchar(255) NOT NULL,
  `request_data` text NOT NULL,
  `request_date_time` datetime NOT NULL,
  `response_data` text NOT NULL,
  `response_date_time` datetime NOT NULL,
  `operator_id` int(11) NOT NULL 
) ";
imw_query($sql1) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 154 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 154 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 154</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>