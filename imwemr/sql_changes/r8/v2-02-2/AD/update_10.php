<?php
/*
 *------------------------------------------------------------
 * Create Table For Admin=>Billing=>CCDA SFTP Credentials Tab
 *------------------------------------------------------------
 *
 * This section needs to create to save the CCDA SFTP Credentials
 *
 */
$ignoreAuth = true;

include("../../../../config/globals.php");

$msg_info=array();

$sql1="
	CREATE TABLE `allergy_severity` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`erp_severity_id` varchar(255) DEFAULT NULL,
		`name` varchar(255) NOT NULL,
		`status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=>Active, 1=>Inactive',
		`created_at` timestamp NOT NULL DEFAULT current_timestamp(),
		`updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4";

imw_query($sql1) or $msg_info[] = imw_error();

$sql2 = "INSERT INTO `allergy_severity` (`id`,`name`) VALUES 
('1', 'fatal'),
('2', 'mild'),
('3', 'mild to moderate'),
('4', 'moderate'),
('5', 'moderate to severe'), 
('6', 'severe');";
imw_query($sql2) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 10 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 10 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 10</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>