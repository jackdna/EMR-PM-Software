<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info = array();


$sql="CREATE TABLE `clearing_houses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `house_name` varchar(100) NOT NULL,
  `abbr` varchar(5) NOT NULL COMMENT 'abbreviatoin',
  `test_url` varchar(150) NOT NULL,
  `prod_url` varchar(150) NOT NULL,
  `CL_receiver_id` varchar(30) NOT NULL COMMENT 'ISA-08 value',
  `connect_mode` varchar(1) NOT NULL COMMENT 'T=test, P =Production',
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '1=active;0=not in use',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM";
imw_query($sql) or $msg_info[] = imw_error();

$sql="INSERT INTO `clearing_houses` (`id`, `house_name`, `abbr`, `test_url`, `prod_url`, `CL_receiver_id`, `connect_mode`, `status`) VALUES
(1, 'Change HealthCare', 'EMD', 'https://cert.its.changehealthcare.com/', 'https://its.changehealthcare.com/', '', 'T', 1),
(2, 'Practice Insight', 'PI', 'https://qa2.ediinsight.com/', '', '161622439', 'T', 0)";
imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `emdeon_reports` ADD `group_id` INT NOT NULL COMMENT 'pkid of groups_new table' AFTER `read_status`";
imw_query($sql) or $msg_info[] = imw_error();


$sql="UPDATE `clearing_houses` SET `prod_url` = 'https://e1.ediinsight.com/' WHERE `clearing_houses`.`id` =2 AND `abbr` = 'PI' LIMIT 1";
imw_query($sql) or $msg_info[] = imw_error();



if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 5 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 5 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 5 (BI)</title>
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