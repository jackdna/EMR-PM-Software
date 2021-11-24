<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();
$sql1="ALTER TABLE `slot_procedures` ADD `non_billable` INT( 2 ) NOT NULL";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 134 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 134  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 134</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>