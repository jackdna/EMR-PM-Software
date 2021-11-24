<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "ALTER TABLE `resp_party` ADD `erp_resp_imw_password` VARCHAR(512) NOT null ";
imw_query($sql2) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 8 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 8 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 8</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
