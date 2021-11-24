<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="ALTER TABLE `cpt_fee_tbl` ADD `cpt_category2` TINYINT(5) NOT NULL DEFAULT '0' COMMENT '1: for service, 2: for material, 0: for none' ";

imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 143 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 143 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 143</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>