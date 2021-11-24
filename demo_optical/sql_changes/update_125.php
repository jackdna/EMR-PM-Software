<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$errors = array();

$sql = "ALTER TABLE `in_item` ADD COLUMN `retail_price_flag` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Retail price to be used for the item. 0=calculated, 1=`retail_price`' AFTER `retail_price`";

if( imw_query($sql) ){
	$sqlPriceFlagChanges = "UPDATE `in_item` SET `retail_price_flag`=IF(`retail_price`=`wholesale_cost` OR `retail_price`=0.00, 0, 1)";
	imw_query($sqlPriceFlagChanges) or $errors[] = imw_error();
}
else
	$errors[] = imw_error();

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 125 run successfully...</div>';
}

?>