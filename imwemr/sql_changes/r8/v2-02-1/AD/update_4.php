<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql="CREATE TABLE `marital_status` (
  `mstatus_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `mstatus_name` varchar(50) NOT NULL,
  `mstatus_code` varchar(20) NOT NULL,
  `is_deleted` tinyint(5) NOT NULL
) ";
imw_query($sql) or $msg_info[] = imw_error();

$sql1 = "INSERT INTO `marital_status` (`mstatus_id`, `mstatus_name`, `mstatus_code`, `is_deleted`) VALUES
(1, ' ', '', 0),
(2, 'divorced', '', 0),
(3, 'domestic partner', '', 0),
(4, 'married', '', 0),
(5, 'single', '', 0),
(6, 'separated', '', 0),
(7, 'widowed', '', 0)";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2= "ALTER TABLE `race` ADD `erp_race_id` VARCHAR(200) NOT null";
imw_query($sql2) or $msg_info[] = imw_error();
$sql3= "ALTER TABLE `ethnicity` ADD `erp_ethn_id` VARCHAR(200) NOT null";
imw_query($sql3) or $msg_info[] = imw_error();
$sql3= "ALTER TABLE `marital_status` ADD `erp_marital_id` VARCHAR(200) NOT null";
imw_query($sql3) or $msg_info[] = imw_error();
$sql3= "ALTER TABLE `gender_code` ADD `erp_gender_id` VARCHAR(200) NOT null";
imw_query($sql3) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 4 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 4 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 4</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>