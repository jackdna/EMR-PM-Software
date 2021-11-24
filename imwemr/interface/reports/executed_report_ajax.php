<?php
$without_pat = "yes";
include_once(dirname(__FILE__)."/../../config/globals.php");

if($_REQUEST['delId']!='' && $_REQUEST['task'] == "delete"){
	$qry="delete from reports_schedules_executed WHERE id='".$_REQUEST['delId']."'";
	$rs=imw_query($qry);
	
	if(strstr($_REQUEST['fileName'], '/')){
		if(file_exists($_REQUEST['fileName'])){
			unlink($_REQUEST['fileName']);
		}
	}
	
	if(isset($rs)){
		echo 'done';
	}else{ echo 'not done';}
}
?>