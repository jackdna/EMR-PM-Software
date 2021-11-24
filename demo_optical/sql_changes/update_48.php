<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$record=0;
$sync=0;
//create array for lot items
$lot_query=imw_query("select id,item_id,loc_id from in_item_lot_total") or die(imw_error().' _1');
while($lot_data=imw_fetch_object($lot_query))
{
	$lotArray[$lot_data->loc_id][$lot_data->item_id]=$lot_data->id;
}
//get medicine items
$query=imw_query("select id as item_id from in_item where module_type_id=6") or die(imw_error().' _2');
while($data=imw_fetch_object($query))
{
	//check this item entry in loc total
	$check_loc=imw_query("select loc_id,stock from in_item_loc_total where item_id=$data->item_id limit 1") or die(imw_error().' _3');
	$loc_data=imw_fetch_object($check_loc);
	//check for lot entry
	if(!$lotArray[$loc_data->loc_id][$data->item_id])
	{
		imw_query("insert into in_item_lot_total set item_id='$data->item_id', loc_id='$loc_data->loc_id', stock='$loc_data->stock'") or die(imw_error().' _4');
		$newlyAdded[]=imw_insert_id();
		$record++;
	}
}
if(is_array($newlyAdded))
$excludeStr=implode(',',$newlyAdded);
else
$excludeStr=0;

//sync data in lot and loc tables exclude newly entered data 
$query=imw_query("select sum(stock) as stock,loc_id, item_id from in_item_lot_total where id NOT IN($excludeStr) group by loc_id, item_id")or die(imw_error().' _5');
while($data=imw_fetch_object($query))
{
	imw_query("update in_item_loc_total set stock ='$data->stock' where item_id=$data->item_id and loc_id=$data->loc_id") or die(imw_error().' _6');
	$sync++;
}
echo "<b><em>Update complete $record added and $sync sync Successfuly</em></b>";
?>