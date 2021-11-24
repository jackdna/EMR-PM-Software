<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_optical_order_form` ADD  `physician_name` VARCHAR( 255 ) NOT NULL AFTER  `physician_id`") or die(imw_error());
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>