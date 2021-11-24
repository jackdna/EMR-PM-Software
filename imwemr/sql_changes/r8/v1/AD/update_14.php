<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "ALTER TABLE  `users` ADD  `updox_user_id` VARCHAR( 50 ) NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE  `direct_messages` ADD  `updox_user_id` VARCHAR( 50 ) NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "CREATE TABLE `direct_messages_log` (
`log_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`direct_message_id` INT( 11 ) NOT NULL ,
`updox_message_id` INT( 11 ) NOT NULL ,
`status` VARCHAR( 50 ) NOT NULL ,
`entered_date_time` DATETIME NOT NULL) ENGINE = InnoDB";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 14 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 14 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 14</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>