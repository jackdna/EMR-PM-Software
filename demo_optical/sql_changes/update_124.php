<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[] = "ALTER TABLE `in_frame_sources` DROP COLUMN `ManufacturerFramesMasterID`";
$sql[] = "ALTER TABLE `xml_frames_collections` ADD COLUMN `Status` CHAR( 1 ) NOT NULL AFTER `Market`";
$sql[] = "ALTER TABLE `in_item` ADD COLUMN `fd_price_modified_on` DATE NOT NULL COMMENT 'Frames Data Price Last Modification Date'";
$sql[] = "ALTER TABLE `in_item` ADD COLUMN `fd_price_temp` DOUBLE( 12, 2 ) NOT NULL COMMENT 'Frames Data Price temperory container'";
$sql[] = "ALTER TABLE `in_item` ADD COLUMN `fd_price_change_alert` BOOLEAN NOT NULL COMMENT 'Flag to Show item in Price Change Alerts'";

$sql[] = "ALTER TABLE `in_order_details` ADD COLUMN `job_type` CHAR( 3 ) NOT NULL COMMENT 'Vision Web Job Type'";

$sql[] = "ALTER TABLE `in_order` ADD COLUMN `overall_discount_code` INT( 11 ) NOT NULL AFTER `overall_discount`";

$sql[] = "ALTER TABLE `in_order` ADD COLUMN `tax_pt_resp` DOUBLE( 12, 2 ) NOT NULL AFTER `tax_pt_paid`";

$sql[] = "ALTER TABLE `in_prac_codes` ADD COLUMN `retail_price` DOUBLE( 12, 2 ) NOT NULL AFTER `prac_code`";

$sql[] = "ALTER TABLE `in_return_reason` ADD COLUMN `price` DOUBLE( 12, 2 ) NOT NULL AFTER `prac_code_id`";

$sql[] = "ALTER TABLE `in_cl_disinfecting` ADD COLUMN `price` DOUBLE( 12, 2 ) NOT NULL AFTER `prac_code`";

$sql[] = "ALTER TABLE `in_batch_records` ADD COLUMN `retail_price_flag` BOOLEAN NOT NULL AFTER `prev_tot_qty`";
$sql[] = "ALTER TABLE `in_batch_records` ADD COLUMN `retail_price` DOUBLE( 12, 2 ) NOT NULL AFTER `retail_price_flag`";
$sql[] = "ALTER TABLE `in_batch_records` ADD COLUMN `discount` VARCHAR(255) NOT NULL AFTER `retail_price`";

foreach($sql as $qry){
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 124 run successfully...</div>';
}

?>