<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$qry=" ALTER TABLE `tm_assigned_rules` ADD `task_appt_id` INT( 10 ) NOT NULL DEFAULT '0' ";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 121 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 121 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 121</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
