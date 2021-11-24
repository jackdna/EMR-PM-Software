<?php
$ignoreAuth = true;

include("../../../../config/globals.php");

$msg_info=array();

$sql="ALTER TABLE `allergies_data` ADD COLUMN `is_deleted` TINYINT(2) NOT NULL AFTER `dss_order`, 
ADD COLUMN `erp_allergy_id` VARCHAR(200) NOT NULL AFTER `is_deleted`; ";
imw_query($sql) or $msg_info[] = imw_error();

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
