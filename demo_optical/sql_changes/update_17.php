<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_item_price_details` ADD  `progressive_wholesale` DOUBLE( 12, 2 ) NOT NULL AFTER  `other_retail`, ADD  `progressive_retail` DOUBLE( 12, 2 ) NOT NULL AFTER  `progressive_wholesale`, ADD  `edge_wholesale` DOUBLE( 12, 2 ) NOT NULL AFTER  `progressive_retail`, ADD  `edge_retail` DOUBLE( 12, 2 ) NOT NULL AFTER  `edge_wholesale`, ADD  `color_wholesale` DOUBLE( 12, 2 ) NOT NULL AFTER  `edge_retail`, ADD  `color_retail` DOUBLE( 12, 2 ) NOT NULL AFTER  `color_wholesale`") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>