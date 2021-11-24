<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = array();
$sql[]="CREATE TABLE `in_order_remake_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `prac_code_id` int(11) NOT NULL,
  `price` double(12,2) NOT NULL,
  `qty` varchar(4) NOT NULL,
  `allowed` double(12,2) NOT NULL,
  `discount` varchar(10) NOT NULL,
  `total_amount` double(12,2) NOT NULL,
  `ins_amount` double(12,2) NOT NULL,
  `pt_paid` double(12,2) NOT NULL,
  `pt_resp` double(12,2) NOT NULL,
  `discount_code` int(11) NOT NULL,
  `ins_case_id` int(11) NOT NULL,
  `order_chld_id` int(11) NOT NULL,
  `entered_by` int(11) NOT NULL,
  `entered_date` date NOT NULL,
  `entered_time` time NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` date NOT NULL,
  `modified_time` time NOT NULL,
  `del_status` int(2) NOT NULL,
  `del_operator_id` int(11) NOT NULL,
  `del_date` date NOT NULL,
  `del_time` time NOT NULL,
  `tax_rate` int(2) NOT NULL DEFAULT '0',
  `tax_paid` double(12,2) NOT NULL DEFAULT '0.00',
  `tax_applied` tinyint(1) NOT NULL DEFAULT '0',
  `remake_reason` text NOT NULL,
  `remake_doctor` int(11) NOT NULL,
  `remake_optician` int(11) NOT NULL,
  `remake_lab` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;";

$sql[]="ALTER TABLE `in_order` ADD `re_make_id` INT(11) NOT NULL DEFAULT 0";

$sql[]="ALTER TABLE `in_order_remake_details` ADD `remake_reason_id` INT(2) NOT NULL DEFAULT 0 AFTER `tax_applied`";
$sql[]="ALTER TABLE `in_return_reason` ADD `prac_code_id` INT(11) NOT NULL DEFAULT 0 AFTER `prac_code`";

/*Add fields for Rx.*/
$sql[]="ALTER TABLE `in_optical_order_form` ADD `oc_od` VARCHAR(50) NOT NULL AFTER `near_pd_od`";
$sql[]="ALTER TABLE `in_optical_order_form` ADD `oc_os` VARCHAR(50) NOT NULL AFTER `near_pd_os`";

/*Safety Glass Fields*/
$sql[]="ALTER TABLE `in_order_details` ADD `safety_glass` TINYINT(1) NOT NULL DEFAULT 0 AFTER `tax_applied`";

/*Mark Incorrect Rx.*/
$sql[]="ALTER TABLE `in_optical_order_form` ADD `incorrect_rx_status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'If custom Rx. is incorrect' AFTER `custom_rx`";
$sql[]="ALTER TABLE `in_optical_order_form` ADD `incorrect_rx_operator_id` TINYINT(1) NOT NULL DEFAULT 0 AFTER `incorrect_rx_status`";
$sql[]="ALTER TABLE `in_optical_order_form` ADD `incorrect_rx_date` DATE NOT NULL AFTER `incorrect_rx_operator_id`";
$sql[]="ALTER TABLE `in_optical_order_form` ADD `incorrect_rx_time` TIME NOT NULL AFTER `incorrect_rx_date`";

/*Rx make data for Contact Lens*/
$sql[] = "ALTER TABLE `in_cl_prescriptions` ADD `rx_make_od` VARCHAR(255) NOT NULL COMMENT 'Rx Make Name + Style + Type'";
$sql[] = "ALTER TABLE `in_cl_prescriptions` ADD `rx_make_os` VARCHAR(255) NOT NULL COMMENT 'Rx Make Name + Style + Type'";

/*discount Value - generally to be used in lenses*/
$sql[] = "ALTER TABLE `in_order_details` ADD `discount_val` DOUBLE(12,2) NOT NULL COMMENT 'Discount value - mainly used for lenses' AFTER `discount`";

/*Alter in_item table price columns to decimal values*/
$sql[] = "ALTER TABLE `in_item` CHANGE `purchase_price` `purchase_price` DOUBLE(12,2) NOT NULL";
$sql[] = "ALTER TABLE `in_item` CHANGE `wholesale_cost` `wholesale_cost` DOUBLE(12,2) NOT NULL;";
$sql[] = "ALTER TABLE `in_item` CHANGE `retail_price` `retail_price` DOUBLE(12,2) NOT NULL;";
$sql[] = "ALTER TABLE `in_item` CHANGE `amount` `amount` DOUBLE(12,2) NOT NULL;";

/*Prac. Code Field for Contact Lens - Empty Sub Module*/
$sql[] = "UPDATE `in_prac_codes` SET `sub_module`='' WHERE `module_id`=3 AND `sub_module`='brand'";

/*Optical Location Id for Reverse Tracking*/
$sql[] = "ALTER TABLE `in_order_fac` ADD `loc_id` INT(11) NOT NULL COMMENT 'Location Id for which order was created' AFTER `facility_id`";

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
	echo '<div style="color:green;"><br><br>Update 93 run successfully...</div>';	
}

?>