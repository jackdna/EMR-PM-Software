<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../../config/globals.php");

$auth_id = $_SESSION['authId'];

extract($_POST);

$data['success'] = false;


if( $auth_id) {
	
	// get current column settings value
	$currSet = getColumnsList();
	
	$currSet[$page] = $cols;
	$currSet = serialize($currSet);
	
	$qry = "Update users Set column_settings = '".$currSet."' Where id = ".$auth_id."  ";
	$sql = imw_query($qry);
	
	if( $sql ) $data['success'] = true;
}

echo json_encode($data);

?>