<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql="ALTER TABLE `chart_assessment_plans` ADD `refer_to_code` VARCHAR( 250 ) NOT NULL  ";
imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 128  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 128  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 128</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>