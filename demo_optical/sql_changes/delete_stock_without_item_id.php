<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");


$rows_deleted = 0;
$sql = $err = array();

/*Delete All Records 0 item id*/
$sql[] = "DELETE FROM `in_item_loc_total` WHERE `item_id`=0";

/*Delete Enteries in_location for fixing multiple rows in Add stock PopUp*/
$sql[] = "DELETE `t1` 
			FROM 
				`in_item_loc_total` `t1` 
				LEFT JOIN `in_item_loc_total` `t2` ON(
					`t1`.`item_id` = `t2`.`item_id` 
					AND `t1`.`loc_id` = `t2`.`loc_id` 
					AND `t1`.`id` < `t2`.`id`
				) 
			WHERE 
				`t2`.`id` IS NOT NULL";

foreach($sql as $qry){
	imw_query($qry) or $err[] = imw_error();
	$rows_deleted += imw_affected_rows();
}

echo '<div style="color:green;"><br><br>Update 99 run successfully...<br /><br />';
	echo $rows_deleted.' row(s) deleted.';
echo '</div><br /><br />';

if(count($err)){
	echo '<div style="color:red;"><br><pre>'.implode("\n", $err).'<pre></div>';	
}
?>