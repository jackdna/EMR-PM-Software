<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE `in_order_details` ADD `ins_case_id` INT( 11 ) NOT NULL AFTER `ins_id`");
$rss=imw_query("ALTER TABLE `in_order_lens_price_detail` ADD `ins_case_id` INT( 11 ) NOT NULL AFTER `ins_id`");
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}
?>