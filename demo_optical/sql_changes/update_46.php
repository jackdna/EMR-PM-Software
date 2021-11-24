<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("CREATE TABLE `in_item_lot_total` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`item_id` INT( 11 ) NOT NULL ,
`loc_id` INT( 11 ) NOT NULL ,
`lot_no` VARCHAR( 250 ) NOT NULL ,
`stock` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;") or die(imw_error());



if($rs){
	echo '<b><em>Query Executed Successfuly</em></b>';
}else{
	echo '<b><em>Error in Query.</em></b><br>'.$rs;
}
?>