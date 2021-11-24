<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$rs=imw_query("Select * from custom_reports WHERE template_name='User Log' AND report_type='compliance'");
if(imw_num_rows($rs)>0){
    $msg_info[] = "<br><br><b>\"User Log\" Report already exist!</b>";
    $color = "red"; 
}else{
    imw_query("INSERT INTO custom_reports (`id`, `template_name`, `template_fields`, `delete_status`, `default_report`, `report_type`, `report_sub_type`) 
    VALUES (NULL, 'User Log', '', '0', '1', 'compliance', 'User Log');")  or $msg_info[] = imw_error();

    if(count($msg_info)>0){
        $msg_info[] = "<br><br><b>Update 7 Failed!</b>";
        $color = "red";
    }else{
        $msg_info[] = "<br><br><b>Update 7 completed successfully.</b><br>Below report created.<br> - User Log";
        
        $color = "green";
    }
}

imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 7 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 7 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 7</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>