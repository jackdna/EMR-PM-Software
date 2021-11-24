<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql1= "ALTER TABLE `slot_procedures` ADD `erp_appt_reason_id` VARCHAR(200) NOT null";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2= "ALTER TABLE `slot_procedures` ADD `erp_applies_to_new_patinet` TINYINT(5) NOT NULL ";
imw_query($sql2) or $msg_info[] = imw_error();

$sql3= "ALTER TABLE `slot_procedures` ADD `erp_applies_to_ext_patinet` TINYINT(5) NOT NULL ";
imw_query($sql3) or $msg_info[] = imw_error();


$sql4= "ALTER TABLE `schedule_appointments` ADD `erp_appt_id` VARCHAR(200) NOT null";
imw_query($sql4) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 2 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 2 run successfully!</b>";
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