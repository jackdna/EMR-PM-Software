<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_item_price_details` ADD  `progressive_prac_code` INT( 11 ) NOT NULL AFTER  `uv_prac_code`, ADD  `edge_prac_code` INT( 11 ) NOT NULL AFTER  `progressive_prac_code`, ADD  `color_prac_code` INT( 11 ) NOT NULL AFTER  `edge_prac_code`, ADD  `other_prac_code` INT( 11 ) NOT NULL AFTER  `color_prac_code`, ADD  `pgx_prac_code` INT( 11 ) NOT NULL AFTER  `other_prac_code`") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>