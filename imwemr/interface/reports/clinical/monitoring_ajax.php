<?php 
require_once("../reports_header.php");
	$primaryID = $_POST['primaryID'];
	$providerId =  $_POST['providerId'];
	$tableName = $_POST['tableName'];
	$fieldName =  $_POST['fieldName'];
	$operator_name =  $_POST['operator_name'];
	$updateColum =  $_POST['updateColum'];
	
	$update_Query = "UPDATE ".$tableName." SET ".$fieldName." = ".$operator_name." WHERE ".$updateColum."=".$primaryID."";
	$updateRs=imw_query($update_Query);
	
	$qry="Insert into user_console_monitoring_track SET pre_provider_Id=".$providerId.", update_provider_Id=".$operator_name.", table_primary_id='$primaryID', table_name='$tableName', modify_by=".$_SESSION['authId']."  ";
	imw_query($qry);
	
	echo imw_affected_rows();	
die;
?>