<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[]	= 'ALTER TABLE `in_order_details` ADD `lens_vision` VARCHAR(10) NOT NULL DEFAULT \'od\' COMMENT \'Lens selected vision type\' AFTER `type_id`';
$sql[] = 'ALTER TABLE `in_order_details` CHANGE `lens_vision` `lens_vision` VARCHAR(20) NOT NULL COMMENT \'Lens selected vision type\'';

$sql[]	= 'ALTER TABLE `in_order_details` ADD `seg_type_od` INT(11) NOT NULL DEFAULT 0 AFTER `lens_vision`';
$sql[]	= 'ALTER TABLE `in_order_details` ADD `seg_type_os` INT(11) NOT NULL DEFAULT 0 AFTER `seg_type_od`';

//$sql[]	= 'ALTER TABLE `in_order_details` CHANGE `material_id` `material_id_od` INT(11) NOT NULL DEFAULT 0';
$sql[]	= 'ALTER TABLE `in_order_details` ADD `material_id_od` INT(11) NOT NULL DEFAULT 0 AFTER `material_id`';
$sql[]	= 'ALTER TABLE `in_order_details` ADD `material_id_os` INT(11) NOT NULL DEFAULT 0 AFTER `material_id_od`';

//$sql[]	= 'ALTER TABLE `in_order_details` CHANGE `design_id` `design_id_od` INT(11) NOT NULL DEFAULT 0';
$sql[]	= 'ALTER TABLE `in_order_details` ADD `design_id_od` INT(11) NOT NULL DEFAULT 0 AFTER `design_id`';
$sql[]	= 'ALTER TABLE `in_order_details` ADD `design_id_os` INT(11) NOT NULL DEFAULT 0 AFTER `design_id_od`';

//$sql[]	= 'ALTER TABLE `in_order_details` CHANGE `a_r_id` `a_r_id_od` VARCHAR(255) NOT NULL COMMENT \'Lens Treatment OD\'';
$sql[]	= 'ALTER TABLE `in_order_details` ADD `a_r_id_od` VARCHAR(255) NOT NULL COMMENT \'Lens Treatment OD\' AFTER `a_r_id`';
$sql[]	= 'ALTER TABLE `in_order_details` ADD `a_r_id_os` VARCHAR(255) NOT NULL COMMENT \'Lens Treatment OS\' AFTER `a_r_id_od`';

$sql[]	= 'ALTER TABLE `in_order_details` ADD `lens_usage` INT(11) NOT NULL DEFAULT 0 AFTER `a_r_id_od`';
$sql[]	= 'ALTER TABLE `in_order_details` ADD `lens_type` INT(11) NOT NULL DEFAULT 0 AFTER `lens_usage`';

$sql[]	= 'ALTER TABLE `in_order_lens_price_detail` ADD `row_id` VARCHAR(255) NOT NULL AFTER `itemized_name`';
$sql[]	= 'ALTER TABLE `in_order_lens_price_detail` ADD `vision` VARCHAR(20) NOT NULL DEFAULT \'od\' AFTER `itemized_id`';

/*Vision column for Lens Details* /
$sql[] = 'ALTER TABLE `in_order_lens_price_detail` ADD `vision` VARCHAR(10) NOT NULL DEFAULT \'od\' AFTER `id`';

/*Contact Lens Fields*/
$sql[] = 'ALTER TABLE `in_order_details` ADD `item_id_os` INT(11) NOT NULL AFTER `item_id`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `upc_code_os` INT(11) NOT NULL AFTER `upc_code`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `total_amount_os` DOUBLE(12,2) NOT NULL AFTER `total_amount`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `item_name_os` VARCHAR(255) NOT NULL AFTER `item_name`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `price_retail_os` DOUBLE(12,2) NOT NULL AFTER `price_retail`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `price_os` DOUBLE(12,2) NOT NULL AFTER `price`';

$sql[] = 'ALTER TABLE `in_order_details` ADD `discount_os` VARCHAR(250) NOT NULL AFTER `discount`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `tax_rate_os` INT(2) NOT NULL AFTER `tax_rate`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `tax_paid_os` DOUBLE(12,2) NOT NULL AFTER `tax_paid`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `tax_applied_os` TINYINT(1) NOT NULL AFTER `tax_applied`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `ins_amount_os` DOUBLE(12,2) NOT NULL AFTER `ins_amount`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `pt_paid_os` DOUBLE(12,2) NOT NULL AFTER `pt_paid`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `pt_resp_os` DOUBLE(12,2) NOT NULL AFTER `pt_resp`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `order_chld_id_os` INT(11) NOT NULL AFTER `order_chld_id`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `discount_val_os` DOUBLE(12,2) NOT NULL AFTER `discount_val`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `overall_discount_os` DOUBLE(12,2) NOT NULL AFTER `overall_discount`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `item_prac_code_os` INT(11) NOT NULL AFTER `item_prac_code`';

$sql[] = 'ALTER TABLE `in_order_details` ADD `vendor_id` INT(11) NOT NULL COMMENT \'Contact Lens\' AFTER `manufacturer_id_os`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `vendor_id_os` INT(11) NOT NULL COMMENT \'Contact Lens\' AFTER `vendor_id`';


$sql[] = 'ALTER TABLE `in_order_details` ADD `ins_case_id_os` INT( 11 ) NOT NULL AFTER `ins_case_id`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `discount_code_os` INT( 11 ) NOT NULL AFTER `discount_code`';

$sql[] = 'ALTER TABLE `in_order_details` ADD `ins_id_os` INT( 11 ) NOT NULL AFTER `ins_id`';
$sql[] = 'ALTER TABLE `in_order_details` ADD `allowed_os` DOUBLE( 12, 2 ) NOT NULL AFTER `allowed`';

$sql[] = 'ALTER TABLE `in_order_details` ADD `trial_chk_os` TINYINT( 2 ) NOT NULL AFTER `trial_chk`';


foreach($sql as $qry){
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	/*copy Lens Type id to new column*/
	imw_query('UPDATE `in_order_details` SET `seg_type_od`=`type_id` WHERE `seg_type_od`=0 AND `module_type_id`=2');
	
	echo '<div style="color:green;"><br><br>Update 119 run successfully...</div>';
}

?>