<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = "ALTER TABLE `insurance_companies`  ADD `transmit_ndc` SMALLINT NOT NULL DEFAULT '0' COMMENT '0=off,1=on; for NDC number sending in claim data'";
$r = imw_query($q) or $msg_info[]=imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 84 run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 84 run successfully!</b>";
	$color = "green";	
}	

?>
<html>
<head>
    <title>Update 84</title>
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