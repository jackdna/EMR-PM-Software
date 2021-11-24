<?php 
$ignoreAuth = true;
$str= '';
require_once(dirname('__FILE__')."/../config/config.php");

//create table for minimum segment height
$rs1=imw_query("CREATE TABLE  `in_min_seg_ht` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `min_seg_name` VARCHAR( 250 ) NOT NULL ,
 `del_status` TINYINT( 1 ) NOT NULL ,
 `entered_date` DATE NOT NULL ,
 `entered_time` TIME NOT NULL ,
 `entered_by` INT( 11 ) NOT NULL ,
 `modified_date` DATE NOT NULL ,
 `modified_time` TIME NOT NULL ,
 `modified_by` INT( 11 ) NOT NULL ,
 `del_date` DATE NOT NULL ,
 `del_time` TIME NOT NULL ,
 `del_by` INT( 11 ) NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET = latin1;");


if(!imw_error()){
	$str.= '1.Query Executed Successfuly<br/>';
}else{
	$str.=  '1.Error in Query.<br>'.imw_error().'<br/>';
}

//add min_seg_ht id colum along with name
$rs2=imw_query("ALTER TABLE  `in_item` ADD  `minimum_segment_id` INT( 11 ) NOT NULL AFTER  `minimum_segment`;");

if(!imw_error()){
	$str.= '2.Query Executed Successfuly<br/>';
}else{
	$str.=  '2.Error in Query.<br>'.imw_error().'<br/>';
}

$rs3=imw_query("ALTER TABLE `in_order` ADD `payment_mode` VARCHAR( 50 ) NOT NULL ,
ADD `checkNo` VARCHAR( 50 ) NOT NULL ,
ADD `creditCardNo` VARCHAR( 50 ) NOT NULL ,
ADD `creditCardCo` VARCHAR( 50 ) NOT NULL ,
ADD `expirationDate` VARCHAR( 50 ) NOT NULL");

if(!imw_error()){
	$str.= '3.Query Executed Successfuly<br/>';
}else{
	$str.=  '3.Error in Query.<br>'.imw_error().'<br/>';
}

$rs4=imw_query("ALTER TABLE `patient_chargesheet_payment_info` ADD `optical_order_id` INT( 11 ) NOT NULL ");

if(!imw_error()){
	$str.= '4.Query Executed Successfuly<br/>';
}else{
	$str.=  '4.Error in Query.<br>'.imw_error().'<br/>';
}

$rs5=imw_query("ALTER TABLE `patient_charges_detail_payment_info` ADD `optical_order_detail_id` INT( 11 ) NOT NULL ");

if(!imw_error()){
	$str.= '5.Query Executed Successfuly<br/>';
}else{
	$str.=  '5.Error in Query.<br>'.imw_error().'<br/>';
}

$rs6=imw_query("ALTER TABLE  `in_item` ADD  `units` VARCHAR( 250 ) NOT NULL AFTER  `amount` ,
ADD  `dosage` VARCHAR( 100 ) NOT NULL AFTER  `units` ,
ADD  `med_typ`  VARCHAR( 100 ) NOT NULL AFTER  `dosage` ,
ADD  `ndc` VARCHAR( 100 ) NOT NULL AFTER  `med_typ` ,
ADD  `pay_by` VARCHAR( 100 ) NOT NULL AFTER  `ndc` ");

if(!imw_error()){
	$str.= '6.Query Executed Successfuly<br/>';
}else{
	$str.=  '6.Error in Query.<br>'.imw_error().'<br/>';
}

$rs7=imw_query("ALTER TABLE `in_order_lens_price_detail` ADD `allowed` DOUBLE( 12, 2 ) NOT NULL");

if(!imw_error()){
	$str.= '7.Query Executed Successfuly<br/>';
}else{
	$str.=  '7.Error in Query.<br>'.imw_error().'<br/>';
}

$rs8=imw_query("ALTER TABLE `in_order_details` ADD `allowed` DOUBLE( 12, 2 ) NOT NULL");

if(!imw_error()){
	$str.= '8.Query Executed Successfuly<br/>';
}else{
	$str.=  '8.Error in Query.<br>'.imw_error().'<br/>';
}


$rs9=imw_query("ALTER TABLE  `in_item` ADD  `fee` DECIMAL( 10, 2 ) NOT NULL AFTER  `pay_by`,
				ADD  `dx_code` VARCHAR( 250 ) NOT NULL AFTER  `fee` ");

if(!imw_error()){
	$str.= '9.Query Executed Successfuly<br/>';
}else{
	$str.=  '9.Error in Query.<br>'.imw_error().'<br/>';
}


echo $str;
?>