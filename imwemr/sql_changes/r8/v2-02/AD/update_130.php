<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$rs=imw_query("Select * from custom_reports WHERE template_name='Prepayments' AND report_type='financial'");

if(imw_num_rows($rs)<=0){
	$sql="Insert INTO custom_reports SET template_name='Prepayments',
	report_type='financial',
	report_sub_type='daily',
	default_report='1',
	delete_status='0',
	template_fields='a:15:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:2:\"77\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"0\";}i:2;a:2:{s:4:\"name\";s:15:\"report_sub_type\";s:5:\"value\";s:5:\"daily\";}i:3;a:2:{s:4:\"name\";s:6:\"groups\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:9:\"physician\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:9:\"operators\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:8;a:2:{s:4:\"name\";s:14:\"summary_detail\";s:5:\"value\";s:1:\"1\";}i:9;a:2:{s:4:\"name\";s:3:\"dor\";s:5:\"value\";s:1:\"1\";}i:10;a:2:{s:4:\"name\";s:3:\"dot\";s:5:\"value\";s:1:\"1\";}i:11;a:2:{s:4:\"name\";s:14:\"payment_method\";s:5:\"value\";s:1:\"1\";}i:12;a:2:{s:4:\"name\";s:16:\"output_view_only\";s:5:\"value\";s:1:\"1\";}i:13;a:2:{s:4:\"name\";s:10:\"output_pdf\";s:5:\"value\";s:1:\"1\";}i:14;a:2:{s:4:\"name\";s:10:\"output_csv\";s:5:\"value\";s:1:\"1\";}}'";

	imw_query($sql) or $msg_info[] = imw_error();
}else{
	$msg_info[] = '<br><br><b>Report already exist</b><br>';
    $color = "red";
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 130 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 130 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 130</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>