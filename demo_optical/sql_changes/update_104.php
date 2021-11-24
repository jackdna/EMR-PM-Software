<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$rs=imw_query("ALTER TABLE `in_vision_web` ADD `vw_loc_id` INT( 11 ) NOT NULL ");

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}
?>