<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_location` ADD  `contact_person` VARCHAR( 255 ) NOT NULL AFTER  `loc_name` ,
ADD  `fax` VARCHAR( 100 ) NOT NULL AFTER  `contact_person` ,
ADD  `tel_num` VARCHAR( 100 ) NOT NULL AFTER  `fax` ,
ADD  `tax` VARCHAR( 100 ) NOT NULL AFTER  `tel_num` ,
ADD  `address` TEXT NOT NULL AFTER  `tax` ,
ADD  `zip` VARCHAR( 55 ) NOT NULL AFTER  `address` ,
ADD  `zip_ext` VARCHAR( 55 ) NOT NULL AFTER  `zip` ,
ADD  `state` VARCHAR( 100 ) NOT NULL AFTER  `zip_ext` ,
ADD  `city` VARCHAR( 100 ) NOT NULL AFTER  `state`,
ADD  `npi` VARCHAR( 100 ) NOT NULL AFTER  `city` ,
ADD  `pos` INT( 11 ) NOT NULL AFTER  `npi`") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>