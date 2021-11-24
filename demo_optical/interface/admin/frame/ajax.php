<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Get Data
Access Type: Include File
*/
require_once("../../../config/sql_conf.php");
if($_REQUEST['action']!="" && $_REQUEST['action']=="managestock")
{
	$qry = imw_query("select in_item.*, DATE_FORMAT(in_item.discount_till,'%m-%d-%Y') as discount_till from in_item where upc_code = '".trim($_REQUEST['upc'])."' ");
	$returnArr = array();
	while($row = imw_fetch_array($qry))
	{
		//get latest wholesale, purchase from latest stock
		if($row['id'])
		{
			$q=imw_query("select wholesale_price, purchase_price from in_item_lot_total where wholesale_price>0 order by id desc limit 0,1");
			$d=imw_fetch_array($q);
			if($d['wholesale_price']>0)
			{
				$row['wholesale_cost']=$d['wholesale_price'];
			}
			if($d['purchase_price']>0)
			{
				$row['purchase_price']=$d['purchase_price'];
			}
		}
		$returnArr[] = $row;
	}	
	echo json_encode($returnArr);
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_brand")
{
	$sql = "select fs.frame_source,bm.brand_id, bm.manufacture_id from in_brand_manufacture as bm
	 inner join in_frame_sources as fs on fs.id=bm.brand_id 
	 where bm.manufacture_id = '".$_REQUEST['mid']."' and fs.del_status = '0' order by fs.frame_source asc";
	 
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
	 	$sel="";
		while($rows = imw_fetch_array($res)) 
		{
			if($_REQUEST['bid']==$rows['brand_id'])
			{
				$sel = 'selected';
			}
			else
			{
				$sel="";
			}
			echo "<option ".$sel." value='".$rows['brand_id']."'>".$rows['frame_source']."</option>";
		}
	 }
}
?>