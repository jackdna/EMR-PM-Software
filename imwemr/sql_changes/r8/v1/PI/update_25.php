<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql2 = "ALTER TABLE `surgery_center_pre_op_health_ques` ADD `smokeAdvise` VARCHAR( 5 ) NOT NULL AFTER `smokeHowMuch`";
imw_query($sql2) or $error[] = imw_error();

$sql2 = "ALTER TABLE `surgery_center_pre_op_health_ques` ADD `alchoholAdvise` VARCHAR( 5 ) NOT NULL AFTER `alchoholHowMuch`";
imw_query($sql2) or $error[] = imw_error();

if(count($error)>0)
{
	$error[] = "<br><br><b>Update 25 Failed!</b>";
	$color = "red";
}
else
{
	$error[] = "<br><br><b>Update 25 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 25</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>