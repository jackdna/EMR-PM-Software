<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "ALTER TABLE  `schedule_template_log` CHANGE  `template_id`  `template_id` VARCHAR( 100 ) NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "CREATE TABLE IF NOT EXISTS `provider_schedule_tmp_child` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `sch_tmp_pid` int(11) NOT NULL,
  `sch_tmp_id` int(11) NOT NULL,
  `provider` int(11) NOT NULL,
  `facility` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint(4) NOT NULL,
  `applied_by` int(11) NOT NULL,
  `applied_on` datetime NOT NULL,
  `deleted_by` int(11) NOT NULL,
  `deleted_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE  `schedule_appointments` ADD  `procedure_sec_site` VARCHAR( 255 ) NOT NULL AFTER  `procedure_site` ,
ADD  `procedure_ter_site` VARCHAR( 255 ) NOT NULL AFTER  `procedure_sec_site`";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 4 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 4 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 4</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>