<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

/*Table for Retail Price Markup*/
$sql_table = "CREATE TABLE `in_retail_price_markup` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `module_type_id` INT(11) NOT NULL COMMENT 'Module Id of the Item for which the formula is applicable',
  `manufacturer_id` INT(11) NOT NULL,
  `brand_id` INT(11) NOT NULL,
  `vendor_id` INT(11) NOT NULL,
  `style_id` INT(11) NOT NULL,
  `formula` VARCHAR(20) NOT NULL,
  `entered_by` INT(11) NOT NULL,
  `entered_data_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` INT(11) NOT NULL,
  `modified_data_time` TIMESTAMP NOT NULL,
  `del_status` BOOLEAN NOT NULL DEFAULT 0,
  `del_by` INT(11) NOT NULL,
  `del_data_time` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM";

if( imw_query($sql_table) ){
	/*Insert default Data only when table created*/	
	$sql = "INSERT INTO `in_retail_price_markup` ( `module_type_id`, `manufacturer_id`, `brand_id`, `vendor_id` ) VALUES
			 ( 1, 0, 0, 0 ), ( 3, 0, 0, 0 ), ( 5, 0, 0, 0 ), ( 6, 0, 0, 0 )";
	imw_query($sql) or $errors[] = imw_error();
}
else{
	$errors[] = imw_error();
}

$sql = 'ALTER TABLE `in_item` ADD COLUMN `formula` VARCHAR(20) NOT NULL AFTER `retail_price`';
imw_query($sql) or $errors[] = imw_error();

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 122 run successfully...</div>';
}

?>