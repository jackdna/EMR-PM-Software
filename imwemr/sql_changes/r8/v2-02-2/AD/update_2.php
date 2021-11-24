<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$rs=imw_query("SELECT * FROM custom_reports WHERE template_name='Patient Status' AND report_type='financial' AND report_sub_type='analytics'");

if(imw_num_rows($rs)<=0){
    imw_query("INSERT INTO `custom_reports` 
    (`id`, `template_name`, `template_fields`, `delete_status`, `default_report`, `report_type`, `report_sub_type`) 
    VALUES (NULL, 'Patient Status', '', '', '1', 'financial', 'analytics');") or $msg_info[] = imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 2  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 2  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>