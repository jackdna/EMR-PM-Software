<?php
set_time_limit(0);
$ignoreAuth = true;
include(dirname(__FILE__)."/../../../../config/globals.php");

$sql = "
CREATE TABLE `groups_prevlgs` (
  `id` int(10) NOT NULL,
  `gr_name` varchar(250) NOT NULL,
  `prevlgs` text NOT NULL,
  `deleted_by` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Group Privileges';
";

$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
ALTER TABLE `groups_prevlgs`
  ADD PRIMARY KEY (`id`);
";
  
$result = imw_query($sql) or $msg_info[] = imw_error();

$sql = "  
 ALTER TABLE `groups_prevlgs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";
$result = imw_query($sql) or $msg_info[] = imw_error();

if(!$result)
{
	$msg_info[] = '<br><br><b>Update 61 :: Update run FAILED!</b><br><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 61 :: Update run successfully!<br></b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 57 </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(implode("<br>",$msg_info));?>
</font>
</body>
</html>