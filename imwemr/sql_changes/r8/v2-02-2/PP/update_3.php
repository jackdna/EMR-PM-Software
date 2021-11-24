<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql1= "ALTER TABLE `slot_procedures` CHANGE `erp_applies_to_new_patinet` `erp_applies_to_new_patient` TINYINT(5) NOT NULL;";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2= "ALTER TABLE `slot_procedures` CHANGE `erp_applies_to_ext_patinet` `erp_applies_to_ext_patient` TINYINT(5) NOT NULL; ";
imw_query($sql2) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 3 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 3 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
