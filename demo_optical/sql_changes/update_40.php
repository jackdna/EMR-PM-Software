<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_order_lens_price_detail` ADD  `discount_code` INT( 11 ) NOT NULL AFTER  `ins_case_id`") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>