<?php 

$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$sql="ALTER TABLE `tsys_payment_type_log` ADD `orderNumber` VARCHAR( 255 ) NOT NULL";
imw_query($sql) or $msg_info[] = imw_error();

$sql1="ALTER TABLE `tsys_possale_transaction` ADD `motoType` VARCHAR( 20 ) NOT NULL";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="ALTER TABLE `tsys_possale_transaction` ADD `motoOrderNumber` VARCHAR( 255 ) NOT NULL";
imw_query($sql2) or $msg_info[] = imw_error();



if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 97  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 97  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 97</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>