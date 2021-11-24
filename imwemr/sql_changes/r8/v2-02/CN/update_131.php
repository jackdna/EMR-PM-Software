<?php
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../../../config/globals.php");

$msg_info=array();

$s = array();
$s[] = "ALTER TABLE `superbill` ADD `modified_date_time` DATETIME NOT NULL , ADD `modified_by` INT( 11 ) NOT NULL ";
foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();	
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 131  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 131  run successfully!</b>";
    $color = "green";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 131: Work for Superbill modified date and Operator</title>
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