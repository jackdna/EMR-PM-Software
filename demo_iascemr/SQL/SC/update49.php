<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");


$sql = "CREATE TABLE `hl7_sent` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `msg` text NOT NULL,
  `msg_type` varchar(50) NOT NULL,
  `saved_on` datetime NOT NULL,
  `sent` smallint(6) NOT NULL DEFAULT '0' COMMENT '0=not sent; 1=sent successfully',
  `sent_on` datetime NOT NULL,
  `response` varchar(500) NOT NULL,
  `operator` int(11) NOT NULL,
  `sch_id` bigint(20) NOT NULL COMMENT 'scheduler_id',
  `send_to` varchar(100) NOT NULL COMMENT 'receiver_name',
  `status` varchar(25) NOT NULL COMMENT 'accept or reject',
  `status_text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 49 run OK";

?>

<html>
<head>
<title>Update 49</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







