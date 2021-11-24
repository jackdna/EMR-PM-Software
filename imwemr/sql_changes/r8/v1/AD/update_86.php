<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = "ALTER TABLE  `users` ADD  `sch_index` INT NOT NULL;";
$r = imw_query($q) or $msg_info[]=imw_error();
$q = "update `users` set `sch_index`=100 WHERE `sch_index`=0";
$r = imw_query($q) or $msg_info[]=imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 86 run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 86 run successfully!</b>";
	$color = "green";	
}	

?>
<html>
<head>
    <title>Update 86</title>
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