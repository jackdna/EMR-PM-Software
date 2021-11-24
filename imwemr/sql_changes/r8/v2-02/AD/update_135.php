<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$sql1="ALTER TABLE `schedule_template_log` ADD `developer_summary` VARCHAR( 255 ) NOT NULL AFTER `summary`";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 135 run FAILED!</b><br>';
    $color = "red";
}
else
{
	//if this column successfully added only then take backup and create new table
	imw_query("RENAME TABLE `schedule_template_log` TO `schedule_template_log_".date('dMy')."`") or $msg_info[] = imw_error();
	//create fresh table
	imw_query("CREATE TABLE IF NOT EXISTS `schedule_template_log` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `act_typ` varchar(30) NOT NULL,
	  `summary` varchar(255) NOT NULL,
	  `developer_summary` varchar(255) NOT NULL,
	  `provider_id` int(11) NOT NULL,
	  `provider_name` varchar(255) NOT NULL,
	  `facility_id` int(11) NOT NULL,
	  `facility_name` varchar(255) NOT NULL,
	  `template_id` varchar(100) NOT NULL,
	  `template_name` varchar(500) NOT NULL,
	  `on_date` date NOT NULL,
	  `weekday` varchar(10) NOT NULL,
	  `for_future` varchar(10) NOT NULL COMMENT '1=yes, 0 = no',
	  `act_datetime` datetime NOT NULL,
	  `logged_user_id` int(11) NOT NULL,
	  `logged_user_name` varchar(255) NOT NULL,
	  `logged_facility` varchar(255) NOT NULL,
	  `ip` varchar(255) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;") or $msg_info[] = imw_error();
	$msg_info[] = "<br><br>Update 135  run successfully!";
    $color = "green";
}
?>
<html>
<head>
<title>Update 135 - Column added in scheduler template log table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>