<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$error = array();
$msg = array();

/*Drop Unnecessary indexes*/
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_11";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_11";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_10";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_10";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_9";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_9";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_8";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_8";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_7";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_7";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_6";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_6";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_5";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_5";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_4";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_4";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_3";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_3";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX material_id_2";
$sql[] = "ALTER TABLE in_lens_material_design DROP INDEX design_id_2";


$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_11";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_11";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_10";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_10";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_9";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_9";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_8";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_8";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_7";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_7";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_6";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_6";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_5";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_5";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_4";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_4";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_3";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_3";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX material_id_2";
$sql[] = "ALTER TABLE in_lens_ar_material DROP INDEX ar_id_2";

/*Add iDoc Flag to contact Lens Brands*/
$sql[] = "ALTER TABLE `in_contact_brand` ADD COLUMN `source_idoc` BOOLEAN NOT NULL DEFAULT 0";

/*Prac Code column for Contact Lens Brands*/
$sql[] = "ALTER TABLE `in_contact_brand` ADD COLUMN `prac_code` INT (11) NOT NULL DEFAULT 0 AFTER `brand_name`";

/*Contact Lens Price in Brand Tab*/
$sql[] = "ALTER TABLE `in_contact_brand` ADD COLUMN `wholesale_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";
$sql[] = "ALTER TABLE `in_contact_brand` ADD COLUMN `purchase_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";
$sql[] = "ALTER TABLE `in_contact_brand` ADD COLUMN `retail_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00";

foreach($sql as $qry){
	imw_query($qry) or $error[] = imw_error();
}

/*Fix for default negative quantity of item*/
$sql_qty = "UPDATE `in_item_loc_total` SET `stock`=0 WHERE `item_id`=0";
$qty_resp = imw_query($sql_qty) or $error[] = imw_error();

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 98 run successfully...</div>';	
}
?>