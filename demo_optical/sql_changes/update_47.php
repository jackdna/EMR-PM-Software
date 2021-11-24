<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE `in_stock_detail` ADD `lot_id` INT( 11 ) NOT NULL") or die(imw_error());

if($rs){
	echo '<b><em>Query Executed Successfuly</em></b>';
}else{
	echo '<b><em>Error in Query.</em></b><br>'.$rs;
}
?>