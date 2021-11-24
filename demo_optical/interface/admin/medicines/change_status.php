<?php
/*
File: change_status.php
Coded in PHP7
Purpose: Change States of Medicine
Access Type: Include File
*/
require_once("../../../config/sql_conf.php");
if($_REQUEST['page']!="" && $_REQUEST['page']=="change")
{
	$table = $_REQUEST['table'];
	$id = $_REQUEST['id'];
	$value = $_REQUEST['value'];
	$column = $_REQUEST['column'];
	if($table!="" && $id!="" && $value!="" && $column!="")
	{
		$sql = "update ".$table." set ".$column."='".$value."' where id='".$id."'";
		imw_query($sql);
		echo "true";
	}
	else
	{
		echo "false";
	}
}

?>