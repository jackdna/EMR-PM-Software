<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql3= "ALTER TABLE `paymentscomment` ADD `task_onreminder` TINYINT(5) NOT NULL DEFAULT '0' ";
imw_query($sql3) or $msg_info[] = imw_error();

$sql4= "ALTER TABLE `tm_assigned_rules` ADD `task_on_reminder` TINYINT(5) NOT NULL DEFAULT '0' ";
imw_query($sql4) or $msg_info[] = imw_error();



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