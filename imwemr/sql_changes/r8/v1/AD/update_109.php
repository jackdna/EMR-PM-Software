<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql1="ALTER TABLE `copay_policies` ADD `patient_verbal_communication` TINYINT NOT NULL DEFAULT '1';";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 109  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 109  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 109</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>