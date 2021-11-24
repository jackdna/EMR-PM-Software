<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql="ALTER TABLE `posted_record` ADD `ins_comp_id` INT( 11 ) NOT NULL ";
imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 3 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 3 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 7 Updates 3 (BI)</title>
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