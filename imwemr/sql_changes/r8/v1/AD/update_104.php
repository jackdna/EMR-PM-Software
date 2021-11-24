<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql7="CREATE TABLE IF NOT EXISTS `office_hours_settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `enable_office_hours` tinyint(5) unsigned NOT NULL DEFAULT '0',
  `weekdays` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `start_hour` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `start_min` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `start_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `end_hour` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `end_min` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `end_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `excluded_users` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `operator_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `added_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;  ";
imw_query($sql7) or $msg_info[] = imw_error();



if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 104  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 104  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 104</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>