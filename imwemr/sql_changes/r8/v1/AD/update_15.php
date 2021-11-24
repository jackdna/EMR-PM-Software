<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
/*
msg type field added because we have added send message using Direct and Simple Email in pt portal->Send Message/Direct
*/

$qry = "ALTER TABLE  `direct_messages_patient` ADD  `msg_type` VARCHAR( 20 ) NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 15 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 15 completed successfully.</b>";
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