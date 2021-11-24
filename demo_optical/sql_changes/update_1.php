<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_item` ADD  `threshold` VARCHAR( 55 ) NOT NULL AFTER  `qty_on_hand`") or die(imw_error());
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>