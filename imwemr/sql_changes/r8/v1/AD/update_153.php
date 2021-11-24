<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql="ALTER TABLE `facility` ADD `iportal_pos_user` INT(10) NOT NULL DEFAULT '0' ";
imw_query($sql) or $msg_info[] = imw_error();

$sql1="ALTER TABLE `facility` ADD `iportal_def_facility` INT(10) NOT NULL DEFAULT '0' ";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="ALTER TABLE `patient_pre_payment` ADD `iportal_payment` TINYINT(5) NOT NULL DEFAULT '0' ";
imw_query($sql2) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 153 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 153 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 153</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>