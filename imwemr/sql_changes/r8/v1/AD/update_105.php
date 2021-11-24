<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "Select * from custom_reports where template_name = 'Followups' and report_type ='practice_analytic'";
$query = imw_query($qry);
$row_count = imw_num_rows($query);
if ($row_count == 0) {
	$qry = "INSERT INTO `custom_reports` (`template_name`, `template_fields`, `delete_status`, `default_report`, `report_type`, `report_sub_type`) VALUES
('Followups', 'a:8:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:0:\"\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:0:\"\";}i:2;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:3;a:2:{s:4:\"name\";s:9:\"physician\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:16:\"output_view_only\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:10:\"output_pdf\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:10:\"output_csv\";s:5:\"value\";s:1:\"1\";}}', 0, 1, 'practice_analytic', '');
";
	
	imw_query($qry) or $msg_info[] = imw_error();
	if(count($msg_info)>0){
		$msg_info[] = "<br><br><b>Update 105 Failed!</b>";
		$color = "red";
	}else{
		$msg_info[] = "<br><br><b>Update 105 completed successfully.</b>";
		$color = "green";
	}
} else{
	$msg_info[] = "<br><br><b>Data already exit.</b>";
	$color = "red";
}
?>
<html>
<head>
<title>Update 36</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>