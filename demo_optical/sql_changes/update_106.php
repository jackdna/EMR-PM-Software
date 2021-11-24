<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$rows_inserted = 0;
$err = array();

/*Table for keeping record of lots during Stock Reconciliation*/
$sql0 = "CREATE TABLE `in_batch_lot_records` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `batch_record_id` bigint(20) NOT NULL,
		  `lot_no` varchar(250) NOT NULL,
		  `stock` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
imw_query($sql0) or $err[] = imw_error();

/*Purchase Price column for Batch*/
imw_query("ALTER TABLE `in_item_lot_total` ADD `purchase_price` DOUBLE (12, 2) NOT NULL DEFAULT 0.00") or $err[] = imw_error();

/*Add Batch/Lot for each Item Except Medicines as they already have*/
$sql = "INSERT INTO `in_item_lot_total`(
			`item_id`, `loc_id`, `stock`, `lot_no`, 
			`purchase_price`
		) 
		SELECT 
			`lt`.`item_id`, 
			`lt`.`loc_id`, 
			`lt`.`stock`, 
			'1' AS 'lot_no', 
			`i`.`purchase_price` 
		FROM 
			`in_item_loc_total` `lt` 
			LEFT JOIN `in_item_lot_total` `lot` ON(
				`lot`.`item_id` = `lt`.`item_id` 
				AND `lot`.`loc_id` = `lt`.`loc_id`
			) 
			LEFT JOIN `in_item` `i` ON(`lt`.`item_id` = `i`.`id`) 
		WHERE 
			`i`.`module_type_id` != 6 
			AND `i`.`module_type_id` != 0 
			AND `i`.`del_status` = 0 
			AND `lot`.`id` IS NULL";
imw_query($sql) or $err[] = imw_error();
$rows_inserted = imw_affected_rows();

echo '<div style="color:green;"><br><br>Batch/Lot Record added for ';
	echo $rows_inserted.' Item(s).';
echo '</div>';

if(count($err)){
	echo '<div style="color:red;"><br><pre>'.implode("\n", $err).'<pre></div>';	
}
?>