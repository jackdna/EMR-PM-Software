<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
imw_query("UPDATE `custom_reports` SET `template_name` = 'Transition ACI 2018' WHERE `report_type` = 'compliance' AND report_sub_type='mur' AND `template_name` = 'Transition ACI 2017'") or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 6 Failed!</b>";
	$color = "red";
}
else{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 6 successfull</b>";
	$color = "green";

}
?>
<html>
<head>
<title>Release 8 Updates 6 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>