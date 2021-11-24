<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE `in_vendor_details` ADD `sales_rep_fname` VARCHAR( 250 ) NOT NULL ,
ADD `sales_rep_mname` VARCHAR( 250 ) NOT NULL ,
ADD `sales_rep_lname` VARCHAR( 250 ) NOT NULL ,
ADD `sales_rep_work_no` VARCHAR( 250 ) NOT NULL ,
ADD `sales_rep_cell_no` VARCHAR( 250 ) NOT NULL ,
ADD `sales_rep_email` VARCHAR( 250 ) NOT NULL ") or die(imw_error());
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>