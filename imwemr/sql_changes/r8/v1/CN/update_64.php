<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");


$sql = '
	CREATE TABLE `admin_soc` (
  `id` int(10) NOT NULL,
  `soc` varchar(100) NOT NULL,
  `descp` text NOT NULL,
  `del_by` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = '
	ALTER TABLE `admin_soc`
  ADD PRIMARY KEY (`id`);
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql ='
	ALTER TABLE `admin_soc`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `chart_assessment_plans` ADD `vst_soc` VARCHAR(200) NOT NULL AFTER `health_concern`, ADD `soc_desc` TEXT NOT NULL AFTER `vst_soc`;
";

$result = imw_query($sql) or $msg_info[] = imw_error();

if(!$result)
{
	$msg_info[] = '<br><br><b>Update 64 :: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 64 :: Update run successfully!<br></b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update Change xml</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>