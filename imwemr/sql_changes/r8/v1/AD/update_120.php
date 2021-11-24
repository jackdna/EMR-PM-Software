<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$qry="Select * from custom_reports WHERE report_type='clinical' AND template_name='Patient Procedures'";
$rs=imw_query($qry);

if(imw_num_rows($rs)<=0){
	$sql="INSERT INTO `custom_reports` (`template_name`, `template_fields`, `delete_status`, `default_report`, `report_type`, `report_sub_type`) VALUES
('Patient Procedures', '', 0, 1, 'clinical', '');";
	imw_query($sql) or $msg_info[] = imw_error();
}else{
	$msg_info[]='Report name already exist.';
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 120 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 120 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 120</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
