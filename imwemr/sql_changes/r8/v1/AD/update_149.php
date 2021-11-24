<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql="CREATE TABLE `erp_api_credentials` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `account_id` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `synchronization_username` varchar(255) NOT NULL,
  `synchronization_password` varchar(255) NOT NULL
) ";
imw_query($sql) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 149 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 149 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 149</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>