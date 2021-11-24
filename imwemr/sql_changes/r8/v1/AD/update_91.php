<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "Select * from custom_reports where (template_name = 'Transaction Details') and report_sub_type ='analytics' AND delete_status='0'";
$query = imw_query($qry);
$row_count = imw_num_rows($query);
if ($row_count == 0) {
	$qry = "INSERT INTO `custom_reports` (`id`, `template_name`, `template_fields`, `delete_status`, `default_report`, `report_type`, `report_sub_type`) VALUES (NULL, 'Transaction Details', '', 0, 1, 'financial', 'analytics');";
	imw_query($qry) or $msg_info[] = imw_error();
	if(count($msg_info)>0){
		$msg_info[] = "<br><br><b>Update 91 Failed!</b>";
		$color = "red";
	}else{
		$msg_info[] = "<br><br><b>Update 91 completed successfully.</b>";
		$color = "green";
	}
} else{
	$msg_info[] = "<br><br><b>Report already exit.</b>";
	$color = "red";
}
?>
<html>
<head>
<title>Update 45</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>