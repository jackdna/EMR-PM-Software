<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="ALTER TABLE `patient_charge_list` ADD `billing_type` INT( 2 ) NOT NULL COMMENT '1=anesthesia,2=institution'";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 2 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 2 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 2 (BI)</title>
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