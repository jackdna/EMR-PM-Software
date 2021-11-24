<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$qry = "ALTER TABLE `users` ADD `portal_refill_direct_access` VARCHAR( 512 ) NOT NULL ";
$result = imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 6 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 6 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 6</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
