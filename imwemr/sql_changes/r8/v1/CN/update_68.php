<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$sql = '
ALTER TABLE `chart_optic` ADD `ut_elem` TEXT NOT NULL AFTER `wnl_value_Optic`;
';
$result = imw_query($sql) or $msg_info[] = imw_error();


if(!$result)
{
	$msg_info[] = '<br><br><b>Update 68:: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 68 :: Update run successfully!<br></b>";
	$color = "green";	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 68</title>
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