<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $error = array();

$sql[] = "ALTER TABLE `in_order_details` CHANGE `overall_discount` `overall_discount` DOUBLE( 12, 2 ) NOT NULL DEFAULT 0.00 COMMENT 'items\'s share in overall discount'";
$sql[] = "ALTER TABLE `in_order_lens_price_detail` ADD `overall_discount` DOUBLE( 12, 2 ) NOT NULL DEFAULT 0.00 COMMENT 'items\'s share in overall discount' AFTER `discount`";
$sql[] = "ALTER TABLE `in_order_cl_detail` ADD `overall_discount` DOUBLE( 12, 2 ) NOT NULL DEFAULT 0.00 COMMENT 'items\'s share in overall discount' AFTER `discount`";
$sql[] = "ALTER TABLE `in_order_remake_details` ADD `overall_discount` DOUBLE( 12, 2 ) NOT NULL DEFAULT 0.00 COMMENT 'items\'s share in overall discount' AFTER `discount`";

foreach($sql as $qry){	
	imw_query($qry) or $error[] = imw_error();
}

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 105 run successfully...</div>';	
}
?>