<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="CREATE TABLE `vital_interactions` (
  `vi_id` int(2) NOT NULL AUTO_INCREMENT,
  `write_back_comments` tinyint(2) NOT NULL DEFAULT '0' COMMENT ' 0=NO, 1=YES',
  `op_id` int(5) NOT NULL DEFAULT '0',  
  PRIMARY KEY (`vi_id`)
)";

imw_query($sql) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 8 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 8 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 8</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>