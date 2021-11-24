<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$rs=imw_query("Select * from custom_reports WHERE report_type='practice_analytic' AND LOWER(template_name)='email log'");

if(imw_num_rows($rs)<=0){
    $rs=imw_query("INSERT INTO custom_reports (
        `id` ,
        `template_name` ,
        `template_fields` ,
        `delete_status` ,
        `default_report` ,
        `report_type` ,
        `report_sub_type`
        )
        VALUES (
        NULL , 'Email Log', '', '0', '1', 'practice_analytic', ''
        )"
        ) or $msg_info[] = imw_error();
}else{
    $msg_info[] = '<br><br><b>Report Email Log already exists.</b><br>';
    $color = "red";
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 17  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 17  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 17</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>