<?php
/*
File: change_status.php
Coded in PHP7
Purpose: Change States of Contact Lense
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
		$del_data = "";
		if($column=="del_status" && $value==1){
			$del_data = ", del_date='$date', del_time='$time', del_by='$opr_id'";
		}
		$sql = "update ".$table." set ".$column."='".$value."' , modified_date='$date', modified_time='$time', modified_by='$opr_id' $del_data where id='".$id."'";
		imw_query($sql);
		echo "true";
	}
	else
	{
		echo "false";
	}
}

?>