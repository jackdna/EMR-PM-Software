<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE `in_alternative_settings` ADD `frame_style` TINYINT( 1 ) NOT NULL ,
ADD `frame_color` TINYINT( 1 ) NOT NULL ,
ADD `frame_brand` TINYINT( 1 ) NOT NULL ") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>