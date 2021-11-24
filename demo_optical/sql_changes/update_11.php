<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_item` ADD  `item_prac_code` INT( 11 ) NOT NULL AFTER  `upc_code`") or die(imw_error());

$rs2=imw_query("ALTER TABLE  `in_lens_type` ADD  `prac_code` INT( 11 ) NOT NULL AFTER  `type_name`") or die(imw_error());

$rs3=imw_query("ALTER TABLE  `in_lens_ar` ADD  `prac_code` INT( 11 ) NOT NULL AFTER  `ar_name`") or die(imw_error());

$rs4=imw_query("ALTER TABLE  `in_lens_material` ADD  `prac_code` INT( 11 ) NOT NULL AFTER  `material_name`") or die(imw_error());

$rs5=imw_query("ALTER TABLE  `in_lens_polarized` ADD  `prac_code` INT( 11 ) NOT NULL AFTER  `polarized_name`") or die(imw_error());

$rs6=imw_query("ALTER TABLE  `in_lens_tint` ADD  `prac_code` INT( 11 ) NOT NULL AFTER  `tint_type`") or die(imw_error());

$rs7=imw_query("ALTER TABLE  `in_lens_transition` ADD  `prac_code` INT( 11 ) NOT NULL AFTER  `transition_name`") or die(imw_error());

$rs8=imw_query("ALTER TABLE  `in_item_price_details` ADD  `type_prac_code` INT( 11 ) NOT NULL AFTER  `other_retail` ,ADD  `material_prac_code` INT( 11 ) NOT NULL AFTER  `type_prac_code` ,ADD  `ar_prac_code` INT( 11 ) NOT NULL AFTER  `material_prac_code` ,ADD  `transition_prac_code` INT( 11 ) NOT NULL AFTER  `ar_prac_code` ,ADD  `polarized_prac_code` INT( 11 ) NOT NULL AFTER  `transition_prac_code` ,ADD  `tint_prac_code` INT( 11 ) NOT NULL AFTER  `polarized_prac_code` ,ADD  `uv_prac_code` INT( 11 ) NOT NULL AFTER  `tint_prac_code`") or die(imw_error());

if($rs8){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs8;
}

?>