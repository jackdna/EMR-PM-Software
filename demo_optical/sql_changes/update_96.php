<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

imw_query("CREATE TABLE `in_vision_web` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`vw_user` VARCHAR( 250 ) NOT NULL ,
`vw_pass` VARCHAR( 250 ) NOT NULL ,
`vw_ref_id` VARCHAR( 250 ) NOT NULL,
`modified_date` DATE NOT NULL,
`modified_time` TIME NOT NULL,
`modified_by` INT( 11 ) NOT NULL,
`vw_submitted_id` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;") ;

imw_query("INSERT INTO `in_vision_web` (`id`, `vw_user`, `vw_pass`, `vw_ref_id`, `modified_date`, `modified_time`, `modified_by`) VALUES ('1', '', '', '', '', '', '')");

imw_query("ALTER TABLE `in_lens_lab` ADD `vw_lab_id` VARCHAR( 250 ) NOT NULL AFTER `lab_name`");

imw_query("CREATE TABLE `in_lens_lab_detail` (`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`city` VARCHAR( 250 ) NOT NULL ,`lab_id` INT( 11 ) NOT NULL,
`country` VARCHAR( 250 ) NOT NULL ,`extension` VARCHAR( 250 ) NOT NULL ,`state` VARCHAR( 250 ) NOT NULL ,
`street_name` VARCHAR( 250 ) NOT NULL ,`street_number` VARCHAR( 250 ) NOT NULL ,
`telephone` VARCHAR( 250 ) NOT NULL ,`zip_code` VARCHAR( 250 ) NOT NULL ,`vw_billing_number` VARCHAR( 250 ) NOT NULL ,
`vw_shipping_number` VARCHAR( 250 ) NOT NULL ,`entered_date` DATE NOT NULL ,`entered_time` TIME NOT NULL ,`entered_by` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;");

imw_query("ALTER TABLE `in_lens_type` ADD `vw_code` VARCHAR( 250 ) NOT NULL AFTER `prac_code`"); 
imw_query("ALTER TABLE `in_frame_types` ADD `vw_code` VARCHAR( 250 ) NOT NULL AFTER `type_name`"); 
imw_query("ALTER TABLE `in_lens_material` ADD `vw_code` VARCHAR( 250 ) NOT NULL AFTER `material_name`"); 

imw_query("CREATE TABLE `in_lens_design` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`parameter_id` INT( 11 ) NOT NULL ,
`design_name` VARCHAR( 250 ) NOT NULL ,
`vw_code` VARCHAR( 250 ) NOT NULL ,
`lens_vw_code` VARCHAR( 250 ) NOT NULL ,
`entered_date` DATE NOT NULL ,
`entered_time` TIME NOT NULL ,
`entered_by` INT( 11 ) NOT NULL,
`del_status` INT( 11 ) NOT NULL,
`del_date` DATE NOT NULL,
`del_time` TIME NOT NULL,
`del_by` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;");

imw_query("ALTER TABLE `in_lens_design` ADD `prac_code` INT( 11 ) NOT NULL AFTER `vw_code`,
ADD `modified_date` DATE NOT NULL AFTER `entered_by` ,
ADD `modified_time` TIME NOT NULL AFTER `modified_date` ,
ADD `modified_by` INT( 11 ) NOT NULL AFTER `modified_time`");

imw_query("ALTER TABLE `in_lens_ar` ADD `vw_code` VARCHAR( 250 ) NOT NULL AFTER `ar_name`");

imw_query("ALTER TABLE `in_lens_design` CHANGE `type_name` `design_name` VARCHAR( 250 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");

imw_query("ALTER TABLE `in_lens_design` CHANGE `lens_type` `lens_vw_code` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;");

imw_query("ALTER TABLE `in_lens_material` ADD `design_id` INT( 11 ) NOT NULL AFTER `prac_code`");

imw_query("CREATE TABLE `in_lens_material_design` (`material_id` INT( 11 ) NOT NULL ,
`design_id` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;"); 
imw_query("ALTER TABLE `in_lens_material_design` ADD INDEX ( `material_id` ) ");
imw_query("ALTER TABLE `in_lens_material_design` ADD INDEX ( `design_id` ) ");

imw_query("CREATE TABLE `in_lens_ar_material` (`ar_id` INT( 11 ) NOT NULL ,
`material_id` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;"); 
imw_query("ALTER TABLE `in_lens_ar_material` ADD INDEX ( `material_id` ) ");
imw_query("ALTER TABLE `in_lens_ar_material` ADD INDEX ( `ar_id` ) ");

imw_query("ALTER TABLE `in_order_details` ADD `he_coeff` VARCHAR( 250 ) NOT NULL ,
ADD `st_coeff` VARCHAR( 250 ) NOT NULL ,ADD `nhp_cape` VARCHAR( 250 ) NOT NULL ,
ADD `progression_Len` VARCHAR( 250 ) NOT NULL ,ADD `wrap_angle` VARCHAR( 250 ) NOT NULL ,
ADD `panto_angle` VARCHAR( 250 ) NOT NULL ,ADD `rv_distance` VARCHAR( 250 ) NOT NULL ,
ADD `lv_distance` VARCHAR( 250 ) NOT NULL ,ADD `re_rotation` VARCHAR( 250 ) NOT NULL ,
ADD `le_rotation` VARCHAR( 250 ) NOT NULL ,ADD `reading_distance` VARCHAR( 250 ) NOT NULL,
ADD `design_id` INT( 11 ) NOT NULL");

/*columns for saving Design Value*/
imw_query("ALTER TABLE `in_item` ADD `design_id` INT(11) NOT NULL DEFAULT 0 AFTER `progressive_id`");
imw_query("ALTER TABLE `in_item_price_details` ADD `design_wholesale` DOUBLE(12,2) NOT NULL DEFAULT 0 AFTER `progressive_retail`");
imw_query("ALTER TABLE `in_item_price_details` ADD `design_retail` DOUBLE(12,2) NOT NULL DEFAULT 0.00 AFTER `design_wholesale`");
imw_query("ALTER TABLE `in_item_price_details` ADD `design_prac_code` INT(11) NOT NULL DEFAULT 0 AFTER `progressive_prac_code`");

imw_query("ALTER TABLE `in_order_details` ADD `vw_exchange_id` VARCHAR( 250 ) NOT NULL, ADD vw_order_id VARCHAR( 250 ) NOT NULL, ADD `vw_sent_date` DATE NOT NULL ,ADD `vw_status` INT( 11 ) NOT NULL ");

imw_query("ALTER TABLE `in_order_details` ADD `order_index` INT(2) NOT NULL");

imw_query("ALTER TABLE `in_order_details` ADD `lab_detail_id` INT( 11 ) NOT NULL");

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 96 run successfully...</div>';	
}

?>