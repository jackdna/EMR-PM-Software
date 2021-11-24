<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="CREATE TABLE `in_order_cl_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `module_type_id` int(4) NOT NULL,
  `item_type` varchar(20) NOT NULL,
  `prac_code_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;";

$sql[]="ALTER TABLE `in_order_cl_detail` ADD `del_status` INT(2) NOT NULL";
$sql[]="ALTER TABLE `in_order_cl_detail` ADD `del_operator_id` INT(11) NOT NULL";
$sql[]="ALTER TABLE `in_order_cl_detail` ADD `del_date` DATE NOT NULL";
$sql[]="ALTER TABLE `in_order_cl_detail` ADD `del_time` TIME NOT NULL;";

/*Tax calculation modification*/
$sql[]="ALTER TABLE `in_order` DROP `tax_rate`";

$sql[]="ALTER TABLE `in_order_details` ADD `tax_rate` INT(2) NOT NULL DEFAULT 0";
$sql[]="ALTER TABLE `in_order_details` ADD `tax_paid` DOUBLE(12,2) NOT NULL DEFAULT 0.00";
$sql[]="ALTER TABLE `in_order_details` ADD `tax_applied` BOOLEAN NOT NULL DEFAULT 0";
$sql[]="ALTER TABLE `in_order_lens_price_detail` ADD `tax_rate` INT(2) NOT NULL DEFAULT 0";
$sql[]="ALTER TABLE `in_order_lens_price_detail` ADD `tax_paid` DOUBLE(12,2) NOT NULL DEFAULT 0.00";
$sql[]="ALTER TABLE `in_order_lens_price_detail` ADD `tax_applied` BOOLEAN NOT NULL DEFAULT 0";
$sql[]="ALTER TABLE `in_order_cl_detail` ADD `tax_rate` INT(2) NOT NULL DEFAULT 0";
$sql[]="ALTER TABLE `in_order_cl_detail` ADD `tax_paid` DOUBLE(12,2) NOT NULL DEFAULT 0.00";
$sql[]="ALTER TABLE `in_order_cl_detail` ADD `tax_applied` BOOLEAN NOT NULL DEFAULT 0";

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
	echo '<div style="color:green;"><br><br>Update 91 run successfully...</div>';	
}

?>