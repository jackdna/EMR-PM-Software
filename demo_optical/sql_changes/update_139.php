<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[] = "ALTER TABLE  `in_batch_records` ADD  `lot_no` VARCHAR( 50 ) NOT NULL";

$sql[] = "CREATE TABLE IF NOT EXISTS in_temp_batch_record (
  `user_id` int(11) NOT NULL,
  `loc_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `lot_no` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `dated` date NOT NULL)";

$sql[]="ALTER TABLE  `in_batch_lot_records` ADD  `item_id` INT NOT NULL AFTER  `batch_record_id`";

$sql[]="ALTER TABLE  `in_order_details` CHANGE  `upc_code_os`  `upc_code_os` VARCHAR( 250 ) NOT NULL";

$sql[]="ALTER TABLE  `in_stock_detail` ADD  `lot_no` VARCHAR( 100 ) NOT NULL AFTER  `lot_id`";

$sql[]="ALTER TABLE  `in_log_quant_edit` ADD  `lot_no` VARCHAR( 100 ) NOT NULL";

$sql[]="ALTER TABLE  `in_batch_records` ADD  `in_fac_lot_prev_qty` INT NOT NULL AFTER  `in_item_quant`";

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
    echo '<div style="color:green;"><br><br>Update 139 run successfully</div>';
}

?>
