<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$rs=imw_query("Select * from custom_reports WHERE template_name='Cash Lag Analysis' AND report_type='financial' AND report_sub_type='analytics'");

if(imw_num_rows($rs)<=0){
	$sql="Insert INTO custom_reports SET template_name='Cash Lag Analysis',
	report_type='financial',
	report_sub_type='analytics',
	default_report='1',
	delete_status='0'";

	imw_query($sql) or $msg_info[] = imw_error();
}else{
	$msg_info[] = '<br><br><b>Report already exist</b><br>';
    $color = "red";
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 142 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 142 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 142</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>