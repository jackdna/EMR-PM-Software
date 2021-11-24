<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "Select * from custom_reports where (template_name = 'Denial Records') and report_type ='financial'";
$query = imw_query($qry);
$row_count = imw_num_rows($query);
if ($row_count == 0) {
	$qry = "INSERT INTO `custom_reports` (`id`, `template_name`, `template_fields`, `delete_status`, `default_report`, `report_type`, `report_sub_type`) 
	VALUES (NULL, 'Denial Records', 'a:9:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:2:\"46\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"0\";}i:2;a:2:{s:4:\"name\";s:15:\"report_sub_type\";s:5:\"value\";s:9:\"analytics\";}i:3;a:2:{s:4:\"name\";s:6:\"groups\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:15:\"filing_provider\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:22:\"output_actvity_summary\";s:5:\"value\";s:1:\"1\";}i:8;a:2:{s:4:\"name\";s:10:\"output_csv\";s:5:\"value\";s:1:\"1\";}}', 0, 1, 'financial', 'analytics');";
	imw_query($qry) or $msg_info[] = imw_error();
	if(count($msg_info)>0){
		$msg_info[] = "<br><br><b>Update 45 Failed!</b>";
		$color = "red";
	}else{
		$msg_info[] = "<br><br><b>Update 45 completed successfully.</b>";
		$color = "green";
	}
} else{
	$msg_info[] = "<br><br><b>Data already exit.</b>";
	$color = "red";
}
?>
<html>
<head>
<title>Update 46</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>