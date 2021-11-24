<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$qry="Select * from custom_reports WHERE report_type='financial' AND template_name='Itemized Receipts'";
$rs=imw_query($qry);

if(imw_num_rows($rs)<=0){
	$sql="INSERT INTO `custom_reports` (`template_name`, `template_fields`, `delete_status`, `default_report`, `report_type`, `report_sub_type`) VALUES
('Itemized Receipts', 'a:8:{i:0;a:2:{s:4:\"name\";s:7:\"edit_id\";s:5:\"value\";s:2:\"72\";}i:1;a:2:{s:4:\"name\";s:7:\"disable\";s:5:\"value\";s:1:\"0\";}i:2;a:2:{s:4:\"name\";s:15:\"report_sub_type\";s:5:\"value\";s:9:\"analytics\";}i:3;a:2:{s:4:\"name\";s:6:\"groups\";s:5:\"value\";s:1:\"1\";}i:4;a:2:{s:4:\"name\";s:8:\"facility\";s:5:\"value\";s:1:\"1\";}i:5;a:2:{s:4:\"name\";s:15:\"filing_provider\";s:5:\"value\";s:1:\"1\";}i:6;a:2:{s:4:\"name\";s:10:\"date_range\";s:5:\"value\";s:1:\"1\";}i:7;a:2:{s:4:\"name\";s:10:\"output_pdf\";s:5:\"value\";s:1:\"1\";}}', 0, 1, 'financial', 'analytics');";
	imw_query($sql) or $msg_info[] = imw_error();
}else{
	$msg_info[]='Report name already exist.';
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 117 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 117 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 117</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
