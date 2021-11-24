<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_item` ADD  `del_status` TINYINT( 2 ) NOT NULL ,
ADD  `del_date` DATE NOT NULL ,
ADD  `del_time` TIME NOT NULL ,
ADD  `del_by` INT( 11 ) NOT NULL") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>