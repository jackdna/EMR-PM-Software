<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="
CREATE TABLE `user_console_monitoring_track` (
  `id` int(11) NOT NULL,
  `pre_provider_Id` int(11) NOT NULL,
  `update_provider_Id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `update_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `table_primary_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
imw_query($sql) or $msg_info[] = imw_error();

$sql="
ALTER TABLE `user_console_monitoring_track`
  ADD PRIMARY KEY (`id`);
";
imw_query($sql) or $msg_info[] = imw_error();

$sql="
ALTER TABLE `user_console_monitoring_track`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
";
imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 148  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 148 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 148</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>