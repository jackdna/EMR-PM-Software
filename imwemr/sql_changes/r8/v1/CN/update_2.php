<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="ALTER TABLE `amendments` ADD `deleted_by` INT NOT NULL AFTER `dos`";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="ALTER TABLE `chart_memo_text` ADD `deleted_by` INT NOT NULL AFTER `provider_id`";
imw_query($sql2) or $msg_info[] = imw_error();


/*************************************/

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 2 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 2 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 2 (CN)</title>
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