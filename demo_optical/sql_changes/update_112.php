<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$errors = array();

/*Backup Lens Materials Table*/
$sql_table_create = 'CREATE TABLE `in_lens_material_bk_prices` LIKE `in_lens_material`';

if(imw_query($sql_table_create)){
	
	/*Copy Records from live table for backup*/
	$sql_copy_records = 'INSERT INTO `in_lens_material_bk_prices` SELECT * FROM `in_lens_material`';
	if(imw_query($sql_copy_records)){
		
		/*Clean Prices Data in Live Table (lens materials)*/
		$sql_clean_data = 'UPDATE `in_lens_material` SET `wholesale_price` = SUBSTRING_INDEX(`wholesale_price`, \';\', 1), `purchase_price` = SUBSTRING_INDEX(`purchase_price`, \';\', 1), `retail_price` = SUBSTRING_INDEX(`retail_price`, \';\', 1)';
		imw_query($sql_clean_data) or $errors[] = imw_error();
		
		/*Price for Seg Types*/
		$sql_price_data = 'UPDATE `in_lens_material`
							SET
							`wholesale_price` = CONCAT(\'{"sv":"\', if(`wholesale_price`=\'\', 0.00, `wholesale_price`),
														\'","pr":"\', if(`wholesale_price`=\'\', 0.00, `wholesale_price`),
														\'","bf":"\', if(`wholesale_price`=\'\', 0.00, `wholesale_price`),
														\'","tf":"\', if(`wholesale_price`=\'\', 0.00, `wholesale_price`), \'"}\'),
							`purchase_price` = CONCAT(\'{"sv":"\', if(`purchase_price`=\'\', 0.00, `purchase_price`),
														\'","pr":"\', if(`purchase_price`=\'\', 0.00, `purchase_price`),
														\'","bf":"\', if(`purchase_price`=\'\', 0.00, `purchase_price`),
														\'","tf":"\', if(`purchase_price`=\'\', 0.00, `purchase_price`), \'"}\'),
							`retail_price` = CONCAT(\'{"sv":"\', if(`retail_price`=\'\', 0.00, `retail_price`),
														\'","pr":"\', if(`retail_price`=\'\', 0.00, `retail_price`),
														\'","bf":"\', if(`retail_price`=\'\', 0.00, `retail_price`),
														\'","tf":"\', if(`retail_price`=\'\', 0.00, `retail_price`), \'"\}\')';
		imw_query($sql_price_data) or $errors[] = imw_error();
	}
	else
		$errors[] = imw_error();	
}
else
	$errors[] = imw_error();


if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 112 run successfully...</div>';	
}
?>

