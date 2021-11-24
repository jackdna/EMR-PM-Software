<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();
//CREATE SURGERY CONSENT LOG TABLE
$sql="
CREATE TABLE IF NOT EXISTS `surgery_consent_log_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surgery_consent_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_data` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `cur_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `operator_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `appt_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
";
imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 90 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 90 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 90</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>