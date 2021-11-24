<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "ALTER TABLE `schedule_status` ADD `erp_sch_status_id` VARCHAR(200) NOT null";
imw_query($sql2) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 9 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 9 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 9</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>