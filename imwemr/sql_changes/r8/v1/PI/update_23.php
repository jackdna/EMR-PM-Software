<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql1 = "CREATE TABLE IF NOT EXISTS `uga_api_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `url_endpoint` varchar(100) NOT NULL COMMENT 'API URL',
  `parameters_sent` text NOT NULL,
  `response_code` char(4) NOT NULL COMMENT 'HTTP Response Code',
  `response_data` longtext NOT NULL COMMENT 'API Response Data',
  `facility_id` int(11) NOT NULL COMMENT 'Logged in facility',
  `user_id` int(11) NOT NULL COMMENT 'IMW User ID',
  `created_at` datetime NOT NULL COMMENT 'When API Call'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
imw_query($sql1) or $error[] = imw_error();

$sql2 = "ALTER TABLE `patient_data` ADD `uas_account_number` varchar(50) NOT NULL COMMENT 'UGA Finance unique account no'";
imw_query($sql2) or $error[] = imw_error();

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 23 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 23 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 23</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>