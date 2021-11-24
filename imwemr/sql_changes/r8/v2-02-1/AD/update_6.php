<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

imw_query("Update custom_reports SET template_fields='a:12:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:1:\"1\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"0\";}i:2;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:3;a:2:{s:4:\"name\";s:9:\"physician\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:3:\"day\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:16:\"inc_demographics\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:13:\"inc_insurance\";s:5:\"value\";s:1:\"1\";}i:8;a:2:{s:4:\"name\";s:14:\"inc_portal_key\";s:5:\"value\";s:1:\"1\";}i:9;a:2:{s:4:\"name\";s:16:\"output_view_only\";s:5:\"value\";s:1:\"1\";}i:10;a:2:{s:4:\"name\";s:10:\"output_pdf\";s:5:\"value\";s:1:\"1\";}i:11;a:2:{s:4:\"name\";s:10:\"output_csv\";s:5:\"value\";s:1:\"1\";}}'
			WHERE report_type='scheduler' and template_name='Day Appointments'")  or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 6 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 6 completed successfully.</b><br>Below reports criteria updated.<br> - Day Appointments";
	
	$color = "green";
}
?>
<html>
<head>
<title>Update 6</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>