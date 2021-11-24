<?php
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../../../config/globals.php");

$msg_info=array();

$qry = "ALTER TABLE `lists` ADD `erp_id` INT NOT NULL;";
$result = imw_query($qry) or $msg_info[] = imw_error();

$qry1 = "ALTER TABLE `lists` CHANGE `erp_id` `erp_id` VARCHAR(255) NOT NULL;";
$result = imw_query($qry1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 3  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 3 run successfully!</b>";
    $color = "green";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>
