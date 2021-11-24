<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql="CREATE TABLE IF NOT EXISTS `claim_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(100) NOT NULL,
  `del_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ";
imw_query($sql) or $msg_info[] = imw_error();

$sql="INSERT INTO `claim_status` (`id`, `status_name`, `del_status`) VALUES
(1, '1st Appeal', 0),
(2, '2nd Appeal', 0),
(3, 'Credentialing Issue', 0),
(4, 'In collections', 0),
(5, 'Under paid', 0);";
imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `patient_charge_list` ADD `claim_status` INT NOT NULL";
imw_query($sql) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 145 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 145 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 145</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>