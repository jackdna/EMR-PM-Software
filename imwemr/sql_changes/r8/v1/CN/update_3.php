<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="ALTER TABLE `chart_procedures` ADD `intravitreal_frequency` TEXT NOT NULL , ADD `dr_injection_notes` TEXT NOT NULL";
imw_query($sql1) or $msg_info[] = imw_error();

/*************************************/

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 3 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 3 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 3 (CN)</title>
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