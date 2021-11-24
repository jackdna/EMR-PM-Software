<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$error = array();

/*Lenses Price for Procedures*/
$sql[] = "ALTER TABLE `in_stock_detail` ADD `order_id` INT NOT NULL";
$sql[] = "ALTER TABLE `in_stock_detail` ADD `order_detail_id` INT NOT NULL";
$sql[] = "ALTER TABLE `in_stock_detail` ADD `is_return` BOOLEAN NOT NULL DEFAULT 0";

foreach($sql as $qry){
	imw_query($qry) or $error[] = imw_error();
}

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 100 run successfully...</div>';	
}
?>