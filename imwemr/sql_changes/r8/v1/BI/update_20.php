<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("CREATE TABLE `payment_methods` (`pm_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`pm_name` VARCHAR( 250 ) NOT NULL ,`default_method` int(1) NOT NULL,`del_status` INT( 2 ) NOT NULL ,
`del_operator_id` INT( 11 ) NOT NULL ,`del_date_time` DATETIME NOT NULL ,`modified_date` DATETIME NOT NULL ,`modified_by` INT( 11 ) NOT NULL) ") or $msg_info[] = imw_error();

imw_query("INSERT INTO `payment_methods` (`pm_id`, `pm_name`, `default_method`, `del_status`, `del_operator_id`, `del_date_time`, `modified_date`, `modified_by`) VALUES
(1, 'Cash', 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(2, 'Check', 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(3, 'Credit Card', 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(4, 'EFT', 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(5, 'Money Order', 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(6, 'VEEP', 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0)") or $msg_info[] = imw_error();

/*,(7, 'PAN', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(8, 'Care Credit ', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(9, 'CDF', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(10, 'CAP PMT', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(11, 'Non PMT EOB', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0)*/

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 20 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 20 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 20</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>