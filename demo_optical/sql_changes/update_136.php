<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();
set_time_limit(0);

$sql[] = "ALTER TABLE  `in_item_lot_total` ADD  `wholesale_price` DOUBLE(12, 2) NOT NULL AFTER `purchase_price`";
//$sql[] = "ALTER TABLE  `in_item_lot_total` ADD  `retail_price` DOUBLE(12, 2) NOT NULL AFTER `wholesale_price`";

foreach($sql as $qry)
{
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	
    /*Create Batch for all the item if - it does not exists already*/
    $sqlBatchInsert = "INSERT INTO `in_item_lot_total` (`item_id`, `loc_id`, `lot_no`, `stock`, `purchase_price`, `wholesale_price`, `expiry_date`) SELECT `loc`.`item_id`, `loc`.`loc_id`, CONCAT('1001-', `loc`.`item_id`, '-', `loc`.`loc_id`) AS 'lot_id', `loc`.`stock`, `i`.`purchase_price`, `i`.`wholesale_cost`, `i`.`expiry_date` FROM `in_item_loc_total` `loc` INNER JOIN `in_item` `i` ON(`loc`.`item_id` = `i`.`id`) LEFT JOIN `in_item_lot_total` `lot` ON(`loc`.`item_id`=`lot`.`item_id` AND `loc`.`loc_id`=`lot`.`loc_id`) WHERE `lot`.`id` IS NULL";
     imw_query($sqlBatchInsert) or die(imw_error());
    
    echo '<div style="color:green;"><br><br>Update 136 run successfully</div>';
}

?>
