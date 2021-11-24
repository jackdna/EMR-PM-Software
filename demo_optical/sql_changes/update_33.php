<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_vendor_details` ADD  `second_vendor_id` VARCHAR( 30 ) NOT NULL AFTER  `vendor_id`") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>