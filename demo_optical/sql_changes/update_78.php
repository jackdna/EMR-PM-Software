<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="INSERT INTO `in_prac_codes` (`id`, `module_id`, `sub_module`, `prac_code`) VALUES ('12', '0', 'Overall Discount', '')";
$sql[]="ALTER TABLE `in_order` ADD `overall_discount` VARCHAR( 50 ) NOT NULL ,ADD `total_overall_discount` DOUBLE( 12, 2 ) NOT NULL ,ADD `overall_discount_prac_code` VARCHAR( 100 ) NOT NULL";
$sql[]="ALTER TABLE `in_order` ADD `overall_discount_chld` INT( 11 ) NOT NULL";
$sql[]="INSERT INTO `in_prac_codes` (`id`, `module_id`, `sub_module`, `prac_code`) VALUES ('13', '8', 'tax', '')";
$sql[]="ALTER TABLE `in_order_details` ADD `tap` INT( 2 ) NOT NULL";
 
$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 78 run successfully...</div>';	
}

?>