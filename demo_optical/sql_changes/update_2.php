<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("CREATE TABLE `in_lens_items_detail` (`id` INT NULL AUTO_INCREMENT PRIMARY KEY ,`lens_item_name` VARCHAR( 100 ) NOT NULL) ENGINE = MyISAM;") or die(imw_error());
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>