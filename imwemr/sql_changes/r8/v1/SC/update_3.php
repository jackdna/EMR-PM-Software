<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "ALTER TABLE  `chain_event` ADD  `master_setting` INT( 1 ) NOT NULL COMMENT  '1: Regular, 2: Waterfall, 3: Consolidated'";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE  `chain_event_detail` ADD  `master_setting` INT( 1 ) NOT NULL COMMENT  '1: Regular, 2: Waterfall, 3: Consolidated'";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "update `chain_event` set `master_setting`=1 where `master_setting`=0";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "update `chain_event_detail` set `master_setting`=1 where `master_setting`=0";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE  `chain_event_detail` ADD  `consolidation_time` INT NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "CREATE TABLE IF NOT EXISTS `chain_event_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `dated` date NOT NULL,
  `proc` int(11) NOT NULL,
  `provider` int(11) NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  `ampm` varchar(5) NOT NULL,
  `label` varchar(50) NOT NULL,
  `template_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `label_type` text NOT NULL,
  `timestamp` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 3 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 3 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>