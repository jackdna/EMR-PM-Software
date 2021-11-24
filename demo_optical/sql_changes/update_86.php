<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_order` ADD  `re_order_id` INT( 11 ) NOT NULL";
$sql[]="ALTER TABLE  `in_cl_prescriptions` ADD `rx_dos` DATE NOT NULL";
$sql[]="ALTER TABLE  `in_item` CHANGE  `a_r_id`  `a_r_id` VARCHAR( 255 ) NOT NULL";
$sql[]="ALTER TABLE  `in_item_price_details` CHANGE  `ar_prac_code`  `ar_prac_code` VARCHAR( 255 ) NOT NULL";
$sql[]="ALTER TABLE  `in_item_price_details` CHANGE  `a_r_retail`  `a_r_retail` VARCHAR( 255 ) NOT NULL";
$sql[]="ALTER TABLE  `in_item_price_details` CHANGE  `a_r_wholesale`  `a_r_wholesale` VARCHAR( 255 ) NOT NULL";
$sql[]="ALTER TABLE  `in_order_details` CHANGE  `a_r_id`  `a_r_id` VARCHAR( 255 ) NOT NULL";

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
	echo '<div style="color:green;"><br><br>Update 86 run successfully...</div>';	
}

?>