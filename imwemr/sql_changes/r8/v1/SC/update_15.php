<?php 
/*update to clean label table*/
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[]="CREATE TABLE IF NOT EXISTS `verification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appt_id` int(11) NOT NULL DEFAULT '0',
  `v_required` tinyint(2) NOT NULL DEFAULT '0',
  `assigned_user` int(10) NOT NULL,
  `professional_cost` decimal(10,2) NOT NULL,
  `facility_cost` decimal(10,2) NOT NULL,
  `anesthesia_cost` decimal(10,2) NOT NULL,
  `status` enum('pending','followup','completed') NOT NULL,
  `comment` text NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";

$sql[]="CREATE TABLE IF NOT EXISTS `verification_hx` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v_id` int(11) NOT NULL DEFAULT '0',
  `appt_id` int(11) NOT NULL,
  `v_required` tinyint(2) NOT NULL DEFAULT '0',
  `assigned_user` int(10) NOT NULL,
  `professional_cost` decimal(10,2) NOT NULL,
  `facility_cost` decimal(10,2) NOT NULL,
  `anesthesia_cost` decimal(10,2) NOT NULL,
  `status` enum('pending','followup','completed') NOT NULL,
  `comment` text NOT NULL,
  `created_on` datetime NOT NULL,
  `delete_status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";


$sql[]="ALTER TABLE `slot_procedures` ADD `verification_req` TINYINT( 2 ) NOT NULL DEFAULT '0';";
$sql[]="ALTER TABLE `previous_status` ADD `old_verification_req` TINYINT( 2 ) NOT NULL;";
$sql[]="ALTER TABLE `previous_status` ADD `new_verification_req` TINYINT( 2 ) NOT NULL;";

foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 15 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 15 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 15</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>