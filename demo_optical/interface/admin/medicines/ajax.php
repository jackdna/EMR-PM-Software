<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Get Data from UPC code
Access Type: Include File
*/
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
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="dx_code_id")
{
	$dx_code = str_replace("~~~",";",$_REQUEST['dx_code']);
	$getdxCode=imw_query("SELECT * FROM icd10_data WHERE icd9='".$dx_code."' OR icd10='".$dx_code."' OR icd10_desc='".$dx_code."'");
	$get_row=imw_fetch_array($getdxCode);
	echo $dx_code=$get_row['icd10'];
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="cpt_code_id")
{
	$cpt_code = str_replace("~~~",";",$_REQUEST['cpt_code']);
	$getdxCode=imw_query("SELECT * FROM cpt_fee_tbl WHERE cpt_prac_code='".$cpt_code."' OR cpt_desc='".$cpt_code."'");
	$get_row=imw_fetch_array($getdxCode);
	echo $cpt_code=$get_row['cpt_prac_code'];
}
?>