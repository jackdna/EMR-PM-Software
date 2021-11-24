<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_item` ADD  `axis_max` VARCHAR( 30 ) NOT NULL AFTER  `cylindep_negative` ,
ADD  `sphere_positive_max` VARCHAR( 20 ) NOT NULL AFTER  `axis_max` ,
ADD  `sphere_negative_max` VARCHAR( 20 ) NOT NULL AFTER  `sphere_positive_max` ,
ADD  `cylindep_positive_max` VARCHAR( 20 ) NOT NULL AFTER  `sphere_negative_max` ,
ADD  `cylindep_negative_max` VARCHAR( 20 ) NOT NULL AFTER  `cylindep_positive_max`") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>