<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1 = "ALTER TABLE  `rad_test_data` ADD  `snowmedCode` VARCHAR( 200 ) NULL";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>MH &gt; Update 1 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>MH &gt; Update 1 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 1 (MH)</title>
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