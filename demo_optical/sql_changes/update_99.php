<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$error = array();

/*Lenses Price for Procedures*/
$sql[] = "ALTER TABLE `in_lens_design` ADD COLUMN `wholesale_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";
$sql[] = "ALTER TABLE `in_lens_design` ADD COLUMN `purchase_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";
$sql[] = "ALTER TABLE `in_lens_design` ADD COLUMN `retail_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";

$sql[] = "ALTER TABLE `in_lens_material` ADD COLUMN `wholesale_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";
$sql[] = "ALTER TABLE `in_lens_material` ADD COLUMN `purchase_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";
$sql[] = "ALTER TABLE `in_lens_material` ADD COLUMN `retail_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";

$sql[] = "ALTER TABLE `in_lens_ar` ADD COLUMN `wholesale_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";
$sql[] = "ALTER TABLE `in_lens_ar` ADD COLUMN `purchase_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";
$sql[] = "ALTER TABLE `in_lens_ar` ADD COLUMN `retail_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";

/*Pos Row Description for Lens*/
$sql[] = "ALTER TABLE `in_order_lens_price_detail` ADD COLUMN `item_description` VARCHAR(255) NOT NULL AFTER `itemized_name`";

/*Multiple Prac Codes for Item*/
$sql[] = "ALTER TABLE `in_item_price_details` CHANGE `material_prac_code` `material_prac_code` VARCHAR(255) NOT NULL";
$sql[] = "ALTER TABLE `in_item_price_details` CHANGE `material_retail` `material_retail` VARCHAR(255) NOT NULL";
$sql[] = "ALTER TABLE `in_item_price_details` CHANGE `material_wholesale` `material_wholesale` VARCHAR(255) NOT NULL";

/*Multiple Prices for Lens Material in Admin Section*/
$sql[] = "ALTER TABLE `in_lens_material` CHANGE `wholesale_price` `wholesale_price` VARCHAR(255) NOT NULL";
$sql[] = "ALTER TABLE `in_lens_material` CHANGE `purchase_price` `purchase_price` VARCHAR(255) NOT NULL";
$sql[] = "ALTER TABLE `in_lens_material` CHANGE `retail_price` `retail_price` VARCHAR(255) NOT NULL";

/*Reduce Quantity*/
$sql[] = "ALTER TABLE `in_order_details` ADD `qty_reduced` BOOLEAN NOT NULL DEFAULT FALSE";

foreach($sql as $qry){
	imw_query($qry) or $error[] = imw_error();
}

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 99 run successfully...</div>';	
}
?>