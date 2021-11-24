<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

imw_query("Update custom_reports SET template_fields='a:15:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:2:\"14\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"0\";}i:2;a:2:{s:4:\"name\";s:15:\"report_sub_type\";s:5:\"value\";s:9:\"analytics\";}i:3;a:2:{s:4:\"name\";s:6:\"groups\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:15:\"filing_provider\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:18:\"crediting_provider\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:8;a:2:{s:4:\"name\";s:14:\"summary_detail\";s:5:\"value\";s:1:\"1\";}i:9;a:2:{s:4:\"name\";s:3:\"dor\";s:5:\"value\";s:1:\"1\";}i:10;a:2:{s:4:\"name\";s:3:\"dot\";s:5:\"value\";s:1:\"1\";}i:11;a:2:{s:4:\"name\";s:14:\"grpby_facility\";s:5:\"value\";s:1:\"1\";}i:12;a:2:{s:4:\"name\";s:22:\"output_actvity_summary\";s:5:\"value\";s:1:\"1\";}i:13;a:2:{s:4:\"name\";s:10:\"output_pdf\";s:5:\"value\";s:1:\"1\";}i:14;a:2:{s:4:\"name\";s:10:\"output_csv\";s:5:\"value\";s:1:\"1\";}}'
			WHERE report_sub_type='analytics' and template_name='Facility Revenue'")  or $msg_info[] = imw_error();

imw_query("Update custom_reports SET template_fields='a:21:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:2:\"37\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"0\";}i:2;a:2:{s:4:\"name\";s:15:\"report_sub_type\";s:5:\"value\";s:9:\"analytics\";}i:3;a:2:{s:4:\"name\";s:6:\"groups\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:15:\"filing_provider\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:18:\"crediting_provider\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:8;a:2:{s:4:\"name\";s:14:\"summary_detail\";s:5:\"value\";s:1:\"1\";}i:9;a:2:{s:4:\"name\";s:3:\"dos\";s:5:\"value\";s:1:\"1\";}i:10;a:2:{s:4:\"name\";s:3:\"dor\";s:5:\"value\";s:1:\"1\";}i:11;a:2:{s:4:\"name\";s:3:\"dot\";s:5:\"value\";s:1:\"1\";}i:12;a:2:{s:4:\"name\";s:9:\"ins_group\";s:5:\"value\";s:1:\"1\";}i:13;a:2:{s:4:\"name\";s:12:\"ins_carriers\";s:5:\"value\";s:1:\"1\";}i:14;a:2:{s:4:\"name\";s:9:\"ins_types\";s:5:\"value\";s:1:\"1\";}i:15;a:2:{s:4:\"name\";s:7:\"cpt_cat\";s:5:\"value\";s:1:\"1\";}i:16;a:2:{s:4:\"name\";s:3:\"cpt\";s:5:\"value\";s:1:\"1\";}i:17;a:2:{s:4:\"name\";s:15:\"grpby_physician\";s:5:\"value\";s:1:\"1\";}i:18;a:2:{s:4:\"name\";s:22:\"output_actvity_summary\";s:5:\"value\";s:1:\"1\";}i:19;a:2:{s:4:\"name\";s:10:\"output_pdf\";s:5:\"value\";s:1:\"1\";}i:20;a:2:{s:4:\"name\";s:10:\"output_csv\";s:5:\"value\";s:1:\"1\";}}'
			WHERE report_sub_type='analytics' and template_name='Insurance Analytics'")  or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 146 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 146 completed successfully.</b><br>Below reports criteria updated.<br> - Provider Revenue<br> - Referring Revenue<br> - Provider Analytics";
	
	$color = "green";
}
?>
<html>
<head>
<title>Update 146</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>