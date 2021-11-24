<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$rs=imw_query("ALTER TABLE  `in_item` ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs1=imw_query("ALTER TABLE  `in_item_price_details` ADD  `entered_by` INT( 11 ) NOT NULL ,
ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL ") or die(imw_error());

$rs2=imw_query("ALTER TABLE  `in_optical_order_form` ADD  `entered_date` DATE NOT NULL AFTER  `del_status` ,
ADD  `entered_time` TIME NOT NULL AFTER  `entered_date` ,
ADD  `entered_by` INT( 11 ) NOT NULL AFTER  `entered_time` ,
ADD  `modified_date` DATE NOT NULL AFTER  `entered_by` ,
ADD  `modified_time` TIME NOT NULL AFTER  `modified_date` ,
ADD  `modified_by` INT( 11 ) NOT NULL AFTER  `modified_time`") or die(imw_error());

$rs3=imw_query("ALTER TABLE  `in_order_lens_price_detail` ADD  `entered_date` DATE NOT NULL AFTER  `del_status` ,
ADD  `entered_time` TIME NOT NULL AFTER  `entered_date` ,
ADD  `entered_by` INT( 11 ) NOT NULL AFTER  `entered_time` ,
ADD  `modified_date` DATE NOT NULL AFTER  `entered_by` ,
ADD  `modified_time` TIME NOT NULL AFTER  `modified_date` ,
ADD  `modified_by` INT( 11 ) NOT NULL AFTER  `modified_time`") or die(imw_error());

$rs4=imw_query("ALTER TABLE  `in_order_details` ADD  `modified_date` DATE NOT NULL AFTER  `del_status` ,
ADD  `modified_time` TIME NOT NULL AFTER  `modified_date` ,
ADD  `modified_by` INT( 11 ) NOT NULL AFTER  `modified_time`") or die(imw_error());

$rs5=imw_query("ALTER TABLE  `in_order` ADD  `modified_date` DATE NOT NULL AFTER  `del_status` ,
ADD  `modified_time` TIME NOT NULL AFTER  `modified_date` ,
ADD  `modified_by` INT( 11 ) NOT NULL AFTER  `modified_time`") or die(imw_error());

$rs6=imw_query("ALTER TABLE  `in_alternative_settings` ADD  `modified_date` DATE NOT NULL ,
ADD  `modified_time` TIME NOT NULL ,
ADD  `modified_by` INT( 11 ) NOT NULL") or die(imw_error());

$rs7=imw_query("ALTER TABLE  `in_cl_prescriptions` ADD  `entered_date` DATE NOT NULL AFTER  `del_status` ,
ADD  `entered_time` TIME NOT NULL AFTER  `entered_date` ,
ADD  `entered_by` INT( 11 ) NOT NULL AFTER  `entered_time` ,
ADD  `modified_date` DATE NOT NULL AFTER  `entered_by` ,
ADD  `modified_time` TIME NOT NULL AFTER  `modified_date` ,
ADD  `modified_by` INT( 11 ) NOT NULL AFTER  `modified_time`") or die(imw_error());


if($rs7){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>