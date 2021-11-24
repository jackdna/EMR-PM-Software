<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


$sql[]="CREATE TABLE IF NOT EXISTS `hl7_interface_connection` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `in_out` varchar(3) NOT NULL COMMENT 'IN or OUT',
  `interface_id` smallint(6) NOT NULL COMMENT 'PKID of interface_master',
  `connectivity` varchar(25) NOT NULL COMMENT 'SFTP or FTP or TCP or DIR',
  `ip_domain` varchar(150) NOT NULL,
  `port` varchar(25) NOT NULL,
  `un` varchar(150) NOT NULL,
  `pw` varchar(150) NOT NULL,
  `path` varchar(250) NOT NULL,
  `ack_wait_seconds` smallint(6) NOT NULL DEFAULT '10' COMMENT 'for TCP method, ack wait time',
  `parsing_script` varchar(150) NOT NULL COMMENT 'for inbound conneciton',
  `msg_encryption` varchar(25) NOT NULL COMMENT 'if in/out msg is encrypted',
  `static_ack_text` varchar(100) NOT NULL COMMENT 'Dynamic normal ACK generated if empty',
  `application_module` varchar(50) NOT NULL COMMENT 'demographics',
  `msg_type` varchar(10) NOT NULL COMMENT 'ADT or SIU or DFT or ZMS or ORU',
  `send_with_connection` int(11) NOT NULL DEFAULT '0' COMMENT 'other connection id',
  `status` smallint(6) NOT NULL COMMENT '0=inactive; 1=active',
  PRIMARY KEY (`id`)
)";


$sql[]="CREATE TABLE IF NOT EXISTS `hl7_interface_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(150) NOT NULL,
  `table_pkid` int(11) NOT NULL,
  `dt` datetime NOT NULL,
  `op` int(11) NOT NULL COMMENT 'operator id',
  `action` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
)";

$sql[]="CREATE TABLE IF NOT EXISTS `hl7_interface_master` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `interface_name` varchar(150) NOT NULL,
  `interface_with` varchar(50) NOT NULL COMMENT 'keyword for script',
  `setup_date` datetime NOT NULL,
  `go_live_date` datetime NOT NULL,
  `interface_mode` varchar(1) NOT NULL COMMENT 'P or T',
  `HL7_version` varchar(10) NOT NULL COMMENT '2.3.1 or 2.5.1 or 2.4',
  `status` smallint(6) NOT NULL COMMENT '0 or 1',
  PRIMARY KEY (`id`)
)";

$sql[]="CREATE TABLE IF NOT EXISTS `hl7_interface_messages_out` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `interface_id` int(11) NOT NULL COMMENT 'FKID of hl7_interface_master',
  `patient_id` int(11) NOT NULL,
  `msg` text NOT NULL,
  `msg_type` varchar(50) NOT NULL,
  `saved_on` datetime NOT NULL,
  `sent` smallint(6) NOT NULL DEFAULT '0' COMMENT '0=not sent; 1=sent successfully',
  `sent_on` datetime NOT NULL,
  `response` varchar(500) NOT NULL,
  `operator` int(11) NOT NULL,
  `source_id` bigint(20) NOT NULL COMMENT 'form_id or sch_id or pcl_id or lab_id',
  `source_name` varchar(50) NOT NULL,
  `msg_for` varchar(100) NOT NULL COMMENT 'receiver_name',
  `txt_file` smallint(6) NOT NULL DEFAULT '0',
  `pdf_file` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)";

$sql[]="CREATE TABLE IF NOT EXISTS `hl7_interface_message_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_id` smallint(6) NOT NULL COMMENT 'PKID of table interface_master',
  `msg_type` varchar(10) NOT NULL,
  `msg_segments` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
)";

$sql[]="CREATE TABLE IF NOT EXISTS `hl7_interface_message_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_type` varchar(10) NOT NULL,
  `msg_segments` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
)";

$sql[] = "CREATE TABLE IF NOT EXISTS `hl7_interface_segment_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_id` smallint(6) NOT NULL,
  `segment` varchar(5) NOT NULL,
  `sequence` smallint(6) NOT NULL,
  `val_type` varchar(100) NOT NULL,
  `val` varchar(500) NOT NULL COMMENT 'STATIC or MASK or ELSE',
  `format` varchar(150) NOT NULL COMMENT 'format for val field value',
  PRIMARY KEY (`id`)
)";

$sql[] = "CREATE TABLE IF NOT EXISTS `hl7_interface_segment_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `segment` varchar(5) NOT NULL,
  `sequence` smallint(6) NOT NULL,
  `val_type` varchar(100) NOT NULL,
  `val` varchar(500) NOT NULL COMMENT 'STATIC or MASK or ELSE',
  `format` varchar(150) NOT NULL COMMENT 'format for val field value',
  PRIMARY KEY (`id`)
)";

$sql[] = "CREATE TABLE IF NOT EXISTS `hl7_interface_messages_in` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `connection_id` int(11) NOT NULL COMMENT 'FKID of hl7_interface_connection',
  `patient_id` int(11) NOT NULL,
  `msg` text NOT NULL,
  `msg_type` varchar(10) NOT NULL,
  `msg_sub_type` varchar(10) NOT NULL,
  `saved_on` datetime NOT NULL,
  `ack_text` varchar(500) NOT NULL,
  `hl7_parsed` smallint(6) NOT NULL,
  `hl7_parsed_time` datetime NOT NULL,
  `hl7_parsed_result` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
)";

$sql[] = "ALTER TABLE `hl7_interface_message_master`  ADD `trigger_events` VARCHAR(255) NOT NULL";
$sql[] = "ALTER TABLE `hl7_interface_message_custom`  ADD `trigger_events` VARCHAR(255) NOT NULL";
$sql[] = "UPDATE `hl7_interface_message_master` SET `trigger_events` = '0,11,13,18,202,203' WHERE msg_type LIKE 'SIU' LIMIT 1";

foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}


if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>HL7 &gt; Update 4 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>HL7 &gt; Update 4 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 4 (HL7 New Interface)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>