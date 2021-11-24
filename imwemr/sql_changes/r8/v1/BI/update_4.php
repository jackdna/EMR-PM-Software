<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql="ALTER TABLE `manual_batch_file` ADD `default_transaction_date` DATE NOT NULL ";
imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 4 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 4 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 4 (BI)</title>
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