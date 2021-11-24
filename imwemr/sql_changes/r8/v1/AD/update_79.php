<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql2="UPDATE `custom_reports` SET `template_fields` = 'a:11:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:1:\"5\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"0\";}i:2;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:3;a:2:{s:4:\"name\";s:9:\"physician\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:9:\"operators\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:14:\"summary_detail\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:11:\"heard_about\";s:5:\"value\";s:1:\"1\";}i:8;a:2:{s:4:\"name\";s:14:\"grpby_facility\";s:5:\"value\";s:1:\"1\";}i:9;a:2:{s:4:\"name\";s:15:\"grpby_physician\";s:5:\"value\";s:1:\"1\";}i:10;a:2:{s:4:\"name\";s:15:\"inc_appt_detail\";s:5:\"value\";s:1:\"1\";}}' 
WHERE `custom_reports`.`template_name` = 'Registered New Patient'";
imw_query($sql2) or $msg_info[] = imw_error();


/*************************************/
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 42 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 42 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 78</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>