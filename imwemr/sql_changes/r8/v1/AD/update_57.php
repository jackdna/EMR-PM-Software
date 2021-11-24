<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql4="UPDATE `custom_reports` SET `template_fields` = 'a:16:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:2:\"15\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"0\";}i:2;a:2:{s:4:\"name\";s:15:\"report_sub_type\";s:5:\"value\";s:18:\"account_receivable\";}i:3;a:2:{s:4:\"name\";s:6:\"groups\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:9:\"physician\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:14:\"summary_detail\";s:5:\"value\";s:1:\"1\";}i:8;a:2:{s:4:\"name\";s:3:\"dos\";s:5:\"value\";s:1:\"1\";}i:9;a:2:{s:4:\"name\";s:3:\"dor\";s:5:\"value\";s:1:\"1\";}i:10;a:2:{s:4:\"name\";s:3:\"dot\";s:5:\"value\";s:1:\"1\";}i:11;a:2:{s:4:\"name\";s:14:\"grpby_facility\";s:5:\"value\";s:1:\"1\";}i:12;a:2:{s:4:\"name\";s:15:\"grpby_physician\";s:5:\"value\";s:1:\"1\";}i:13;a:2:{s:4:\"name\";s:22:\"output_actvity_summary\";s:5:\"value\";s:1:\"1\";}i:14;a:2:{s:4:\"name\";s:10:\"output_pdf\";s:5:\"value\";s:1:\"1\";}i:15;a:2:{s:4:\"name\";s:10:\"output_csv\";s:5:\"value\";s:1:\"1\";}}' 
WHERE `custom_reports`.`template_name` = 'Provider A/R'";
imw_query($sql4) or $msg_info[] = imw_error();


/*************************************/
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 57 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 57 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 42</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>