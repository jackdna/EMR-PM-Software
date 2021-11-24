<?php
$without_pat = "yes";
include_once(dirname(__FILE__)."/../../config/globals.php");
if($_REQUEST['mode']=='status'){
	$msg_info=array();
	
	if($_REQUEST['pkId']!=''){
		$status = ($_REQUEST['status'] == "active") ? "suspend" : "active";
		$qry="Update reports_crone_jobs SET status='".$status."' WHERE id='".$_REQUEST['pkId']."'";
		$rs=imw_query($qry) or $msg_info[] = imw_error();
	}

	if(sizeof($msg_info)<=0){
		echo 'done';
	}else{ echo 'not done';}
}
?>