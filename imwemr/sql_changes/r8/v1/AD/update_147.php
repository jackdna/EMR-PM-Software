<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$rs=imw_query("Select * from custom_reports WHERE report_type='financial' AND report_sub_type='analytics' AND LOWER(template_name)='office production'");

if(imw_num_rows($rs)<=0)
{
    imw_query("INSERT INTO custom_reports (`id`, `template_name`, `template_fields`, `delete_status`, `default_report`, `report_type`, `report_sub_type`) 
    VALUES (NULL, 'Office Production', '', '0', '1', 'financial', 'analytics');")  or $msg_info[] = imw_error();
}else
{
    $msg_info[] = "<br><br><b><br>Office Production report already exist.</b>";
	$color = "red";
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 147 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 147 completed successfully.</b><br>Reports Office Production created successfully.";
	$color = "green";
}
?>
<html>
<head>
<title>Update 147</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>