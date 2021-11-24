<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = "ALTER TABLE `facility` ADD `enable_hp` TINYINT(1) NOT NULL;";
$r = imw_query($q) or $msg_info[]=imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 85 run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 85 run successfully!</b>";
	$color = "green";	
}	

?>
<html>
<head>
    <title>Update 85</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>