<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[]="CREATE TABLE `scheduler_custom_labels_log` (
  `l_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_provider` int(11) NOT NULL,
  `l_facility` int(11) NOT NULL,
  `l_date` date NOT NULL,
  `l_start_time` time NOT NULL,
  `l_end_time` time NOT NULL,
  `l_type` varchar(20) NOT NULL,
  `l_text` varchar(50) NOT NULL,
  `l_text_before` VARCHAR( 100 ) NOT NULL,
  `l_text_after` VARCHAR( 100 ) NOT NULL,
  `temp_id` int(11) NOT NULL,
  `act` varchar(50) NOT NULL,
  `act_id` INT NOT NULL,
  `time_stamp` datetime NOT NULL,
  `operator_id` int(11) NOT NULL,
  PRIMARY KEY (`l_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";

foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 18 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 18 completed successfully.</b>`";
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