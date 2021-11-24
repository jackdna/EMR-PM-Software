<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("CREATE TABLE `in_order_lens_price_detail` (`id` INT( 11 ) NULL AUTO_INCREMENT PRIMARY KEY ,`itemized_id` INT( 11 ) NOT NULL ,`itemized_name` VARCHAR( 100 ) NOT NULL ,`item_id` INT( 11 ) NOT NULL ,
`patient_id` INT( 11 ) NOT NULL ,`order_id` INT( 11 ) NOT NULL ,`order_detail_id` INT( 11 ) NOT NULL ,
`module_type_id` INT( 11 ) NOT NULL ,`wholesale_price` DOUBLE( 12, 2 ) NOT NULL ,`discount` VARCHAR( 50 ) NOT NULL ,
`total_amt` DOUBLE( 12, 2 ) NOT NULL ,`ins_amount` DOUBLE( 12, 2 ) NOT NULL ,`pt_paid` DOUBLE( 12, 2 ) NOT NULL ,
`pt_resp` DOUBLE( 12, 2 ) NOT NULL ,`item_prac_code` INT( 11 ) NOT NULL ,`ins_id` INT( 11 ) NOT NULL ,`comment` TEXT NOT NULL ,`del_status` INT( 2 ) NOT NULL DEFAULT  '0',`del_date` DATE NOT NULL ,`del_time` TIME NOT NULL ,`del_operator_id` INT( 11 ) NOT NULL) ENGINE = MyISAM;") or die(imw_error());
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>