<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE `patient_charge_list` ADD `opt_order_id` INT( 11 ) NOT NULL") or die(imw_error());

$rss=imw_query("ALTER TABLE `patient_charge_list_details` ADD `opt_order_detail_id` INT( 11 ) NOT NULL") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>