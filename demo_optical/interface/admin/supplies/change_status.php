<?php
/*
File: change_status.php
Coded in PHP7
Purpose: Change States of Supplies
Access Type: Include File
*/
require_once("../../../config/config.php");
$opr_id = $_SESSION['authId'];
$date = date("Y-m-d");
$time = date("h:i:s");
if($_REQUEST['page']!="" && $_REQUEST['page']=="change")
{
	$table = $_REQUEST['table'];
	$id = $_REQUEST['id'];
	$value = $_REQUEST['value'];
	$column = $_REQUEST['column'];
	if($table!="" && $id!="" && $value!="" && $column!="")
	{
		$sql = "update ".$table." set ".$column."='".$value."', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='".$id."'";
		imw_query($sql);
		echo "true";
	}
	else
	{
		echo "false";
	}
}

?>