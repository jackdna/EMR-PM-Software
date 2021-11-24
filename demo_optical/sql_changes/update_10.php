<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_order_details` ADD  `style_id_os` INT( 11 ) NOT NULL AFTER  `brand_id_os`") or die(imw_error());
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>