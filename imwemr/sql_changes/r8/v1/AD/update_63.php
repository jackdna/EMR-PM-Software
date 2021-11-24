<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//Task Manager Rules Category
//$qry1 = "DROP TABLE `tm_rule_category`";
//imw_query($qry1) or $msg_info[] = imw_error();

$qry2 = "
CREATE TABLE IF NOT EXISTS `tm_rule_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tm_rule_category` varchar(255) NOT NULL DEFAULT '',
  `tm_rule_cat_alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM 
";
imw_query($qry2) or $msg_info[] = imw_error();

$qry3 = "
INSERT INTO `tm_rule_category` (`id`, `tm_rule_category`, `tm_rule_cat_alias`) VALUES
(1, 'Accounting', 'accounting'),
(2, 'Appointment', 'appointment'),
(3, 'A/R Aging', 'ar_aging');
";

imw_query($qry3) or $msg_info[] = imw_error();

$qry4="ALTER TABLE `tm_rules_list` CHANGE `addedon` `addedon` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
imw_query($qry4) or $msg_info[] = imw_error();
$qry5="ALTER TABLE `tm_rules_list` DROP `updatedon` ";
imw_query($qry5) or $msg_info[] = imw_error();
$qry6="ALTER TABLE `tm_assigned_rules` CHANGE `added_on` `added_on` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
imw_query($qry6) or $msg_info[] = imw_error();
$qry7="ALTER TABLE `tm_assigned_rules` DROP `updated_on` ";
imw_query($qry7) or $msg_info[] = imw_error();

$qry8="ALTER TABLE `tm_assigned_rules` ADD `comments` TEXT NOT NULL ";
imw_query($qry8) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 63 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 63 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 63</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>