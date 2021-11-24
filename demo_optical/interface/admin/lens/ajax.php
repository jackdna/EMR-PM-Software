<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Get Data
Access Type: Include File
*/
require_once("../../../config/config.php");
require_once("../../../config/sql_conf.php");
if($_REQUEST['action']!="" && $_REQUEST['action']=="managestock")
{
	$qry = imw_query("select *, DATE_FORMAT(discount_till,'%m-%d-%Y') as discount_till from in_item where upc_code = '".trim($_REQUEST['upc'])."' ");
	$returnArr = array();
	while($row = imw_fetch_array($qry))
	{
		$returnArr[] = $row;
	}	
	echo json_encode($returnArr);
}

?>