<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$rs=imw_query("ALTER TABLE  `in_color` ADD  `entered_date` DATE NOT NULL , ADD  `entered_time` TIME NOT NULL , ADD  `entered_by` INT( 11 ) NOT NULL , ADD  `modified_date` DATE NOT NULL , ADD  `modified_time` TIME NOT NULL , ADD  `modified_by` INT( 11 ) NOT NULL , ADD  `del_date` DATE NOT NULL , ADD  `del_time` TIME NOT NULL , ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs1=imw_query("ALTER TABLE  `in_supply` ADD  `entered_date` DATE NOT NULL , ADD  `entered_time` TIME NOT NULL , ADD  `entered_by` INT( 11 ) NOT NULL , ADD  `modified_date` DATE NOT NULL , ADD  `modified_time` TIME NOT NULL , ADD  `modified_by` INT( 11 ) NOT NULL , ADD  `del_date` DATE NOT NULL , ADD  `del_time` TIME NOT NULL , ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs2=imw_query("ALTER TABLE  `in_type` ADD  `entered_date` DATE NOT NULL , ADD  `entered_time` TIME NOT NULL , ADD  `entered_by` INT( 11 ) NOT NULL , ADD  `modified_date` DATE NOT NULL , ADD  `modified_time` TIME NOT NULL , ADD  `modified_by` INT( 11 ) NOT NULL , ADD  `del_date` DATE NOT NULL , ADD  `del_time` TIME NOT NULL , ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs3=imw_query("ALTER TABLE  `in_contact_brand` ADD  `entered_date` DATE NOT NULL , ADD  `entered_time` TIME NOT NULL , ADD  `entered_by` INT( 11 ) NOT NULL , ADD  `modified_date` DATE NOT NULL , ADD  `modified_time` TIME NOT NULL , ADD  `modified_by` INT( 11 ) NOT NULL , ADD  `del_date` DATE NOT NULL , ADD  `del_time` TIME NOT NULL , ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs4=imw_query("ALTER TABLE  `in_contact_cat` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs5=imw_query("ALTER TABLE  `in_contact_style` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL ") or die(imw_error());

$rs6=imw_query("ALTER TABLE  `in_frame_color` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs7=imw_query("ALTER TABLE  `in_frame_shapes` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs8=imw_query("ALTER TABLE  `in_frame_styles` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL ") or die(imw_error());

$rs9=imw_query("ALTER TABLE  `in_frame_types` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs10=imw_query("ALTER TABLE  `in_frame_sources` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs11=imw_query("ALTER TABLE  `in_supplies_measurment` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs12=imw_query("ALTER TABLE  `in_supplies_size` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs13=imw_query("ALTER TABLE  `in_reason` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs14=imw_query("ALTER TABLE  `in_location` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs15=imw_query("ALTER TABLE  `in_manufacturer_details` CHANGE  `created_date`  `entered_date` DATE NOT NULL") or die(imw_error());

$rs16=imw_query("ALTER TABLE  `in_manufacturer_details` CHANGE  `operator_id`  `entered_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs17=imw_query("ALTER TABLE  `in_manufacturer_details` ADD  `entered_time` TIME NOT NULL AFTER  `entered_date`") or die(imw_error());

$rs18=imw_query("ALTER TABLE  `in_manufacturer_details` ADD  `modified_date` DATE NOT NULL AFTER  `entered_by` ,
ADD  `modified_time` TIME NOT NULL AFTER  `modified_date` ,
ADD  `modified_by` INT( 11 ) NOT NULL AFTER  `modified_time` ,
ADD  `del_date` DATE NOT NULL AFTER  `modified_by` ,
ADD  `del_time` TIME NOT NULL AFTER  `del_date` ,
ADD  `del_by` INT( 11 ) NOT NULL AFTER  `del_time`") or die(imw_error());

$rs19=imw_query("ALTER TABLE  `in_vendor_details` CHANGE  `created_date`  `entered_date` DATE NOT NULL") or die(imw_error());

$rs20=imw_query("ALTER TABLE  `in_vendor_details` CHANGE  `operator_id`  `entered_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs21=imw_query("ALTER TABLE  `in_vendor_details` ADD  `entered_time` TIME NOT NULL AFTER  `entered_date`") or die(imw_error());

$rs22=imw_query("ALTER TABLE  `in_vendor_details` ADD  `modified_date` DATE NOT NULL AFTER  `entered_by` ,
ADD  `modified_time` TIME NOT NULL AFTER  `modified_date` ,
ADD  `modified_by` INT( 11 ) NOT NULL AFTER  `modified_time` ,
ADD  `del_date` DATE NOT NULL AFTER  `modified_by` ,
ADD  `del_time` TIME NOT NULL AFTER  `del_date` ,
ADD  `del_by` INT( 11 ) NOT NULL AFTER  `del_time`") or die(imw_error());

$rs23=imw_query("ALTER TABLE  `in_lens_type` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs24=imw_query("ALTER TABLE  `in_lens_material` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs25=imw_query("ALTER TABLE  `in_lens_ar` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs26=imw_query("ALTER TABLE  `in_lens_transition` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs27=imw_query("ALTER TABLE  `in_lens_polarized` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs28=imw_query("ALTER TABLE  `in_lens_progressive` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs29=imw_query("ALTER TABLE  `in_lens_color` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs30=imw_query("ALTER TABLE  `in_lens_tint` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs31=imw_query("ALTER TABLE  `in_lens_lab` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs32=imw_query("ALTER TABLE  `in_lens_edge` ADD  `entered_date` DATE NOT NULL ,
ADD  `entered_time` TIME NOT NULL ,
ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());


if($rs32){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>