<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[] = "UPDATE `in_order_details` SET `lens_vision`='od' WHERE `module_type_id`=2 AND `lens_vision`=''";
$sql[] = "UPDATE `in_order_details` SET `seg_type_od`=`type_id` WHERE `seg_type_od`=0 AND `module_type_id`=2";
$sql[] = "UPDATE `in_order_details` SET `material_id_od`=`material_id` WHERE `material_id_od`=0 AND `module_type_id`=2";
$sql[] = "UPDATE `in_order_details` SET `design_id_od`=`design_id` WHERE `design_id_od`=0 AND `module_type_id`=2";
$sql[] = "UPDATE `in_order_details` SET `a_r_id_od`=`a_r_id` WHERE `a_r_id_od`=0 AND `module_type_id`=2";
$sql[] = "UPDATE `in_order_details` SET `material_id_od`=`material_id` WHERE `material_id_od`=0 AND `module_type_id`=2";

/*Lens Treatment*/
$sql[] = "ALTER TABLE `in_order_details` CHANGE `a_r_id_od` `a_r_id_od` TEXT NOT NULL COMMENT 'Lens Treatment OD'";
$sql[] = "ALTER TABLE `in_order_details` CHANGE `a_r_id_os` `a_r_id_os` TEXT NOT NULL COMMENT 'Lens Treatment OD'";

/*Default prac Code Length*/
$sql[] = "ALTER TABLE `in_prac_codes` CHANGE `prac_code` `prac_code` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;";

/*Frames Data - Increase Column Limit*/
$sql[] = "ALTER TABLE `in_frame_types` CHANGE `StyleFramesMasterID` `StyleFramesMasterID` VARCHAR(50) NOT NULL COMMENT 'Style Frame ID from Frames Data'";
$sql[] = "ALTER TABLE `in_frame_styles` CHANGE `StyleFramesMasterID` `StyleFramesMasterID` VARCHAR(50) NOT NULL COMMENT 'Style Frame ID from Frames Data'";
$sql[] = "ALTER TABLE `in_frame_shapes` CHANGE `StyleFramesMasterID` `StyleFramesMasterID` VARCHAR(50) NOT NULL COMMENT 'Style Frame ID from Frames Data'";
$sql[] = "ALTER TABLE `in_item` CHANGE `StyleFramesMasterID` `StyleFramesMasterID` VARCHAR(50) NOT NULL COMMENT 'Style Frame ID from Frames Data'";
$sql[] = "ALTER TABLE `in_item` CHANGE `ConfigurationFramesMasterID` `ConfigurationFramesMasterID` VARCHAR(50) NOT NULL COMMENT 'Configuration ID from Frames Data'";
$sql[] = "ALTER TABLE `in_frame_color` CHANGE `import_id` `import_id` VARCHAR(50) NOT NULL";

/*Frames Data Collection column for manual checking of update status*/
$sql[] = "ALTER TABLE `xml_frames_collections` ADD `updated` BOOL NOT NULL";

foreach($sql as $qry){
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 120 run successfully...</div>';
}

?>