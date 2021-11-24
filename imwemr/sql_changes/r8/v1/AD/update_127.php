<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = " CREATE TABLE IF NOT EXISTS `pos_facility_group` (
  `pos_fac_grp_id` int(10) NOT NULL AUTO_INCREMENT,
  `pos_facility_group` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fac_group_address` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fac_group_address2` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fac_group_city` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fac_group_state` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fac_group_zip` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fac_zip_ext` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fac_phone` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `phone_ext` varchar(12) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `delete_status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pos_fac_grp_id`)
) ENGINE=InnoDB; ";
imw_query($qry) or $msg_info[] = imw_error();

$qry1 = " ALTER TABLE `pos_facilityies_tbl` ADD `posfacilitygroup_id` INT( 10 ) NOT NULL DEFAULT '0' ";
imw_query($qry1) or $msg_info[] = imw_error();

$qry2 = " ALTER TABLE `users` ADD `posfacilitygroup_id` VARCHAR( 1024 ) NOT NULL  ";
imw_query($qry2) or $msg_info[] = imw_error();

$qry3 = " ALTER TABLE `pos_facility_group` ADD `fac_fax` VARCHAR( 30 ) NOT NULL , ADD `fac_tax_id` VARCHAR( 80 ) NOT NULL   ";
imw_query($qry3) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 127 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 127 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 127</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>