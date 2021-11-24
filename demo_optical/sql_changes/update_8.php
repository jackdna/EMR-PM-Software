<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE `in_order_lens_price_detail` ADD `order_enc_id` INT( 11 ) NOT NULL AFTER `order_detail_id` ,ADD `order_chld_id` INT( 11 ) NOT NULL AFTER `order_enc_id`") or die(imw_error());
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>