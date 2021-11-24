<?php
/*
File: lens_price_detail.php
Coded in PHP7
Purpose: Fetch Item Price Details
Access Type: Direct access
*/
require_once("../../config/sql_conf.php");
if($_REQUEST['action']!="" && $_REQUEST['action']=="managestock" && $_REQUEST['item_id']>0)
{
	$qry = imw_query("select * from in_item_price_details where item_id = '".trim($_REQUEST['item_id'])."' LIMIT 1");
	$returnArr = array();
	$nums = imw_num_rows($qry);
	if($nums > 0)
	{
		while($row = imw_fetch_array($qry))
		{
			$returnArr[] = $row;
		}	
		echo json_encode($returnArr);
	}
}

?>