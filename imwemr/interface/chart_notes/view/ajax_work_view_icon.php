<?php
require_once("../../../config/globals.php");
$db_unpin_icon="";
if($_REQUEST["authUserID"]){
	$authUserID=$_REQUEST["authUserID"];
	$qry="SELECT unpin_icon from users where id='".$authUserID."'";
	$res=imw_query($qry);
	$row=imw_fetch_assoc($res);
	$arr_unpin_icon=array();
	$db_unpin_icon=trim($row["unpin_icon"]);
	
	if($_REQUEST["objval"] && $_REQUEST["status"]){
		$objval=$_REQUEST["objval"];
		$status=$_REQUEST["status"];
		$qry="SELECT unpin_icon from users where id='".$authUserID."'";
		$res=imw_query($qry);
		$row=imw_fetch_assoc($res);
	
		if(trim($db_unpin_icon)){
			$arr_unpin_icon_db=explode(",",$db_unpin_icon);
			foreach($arr_unpin_icon_db as $dbobjval){
				$arr_unpin_icon[$dbobjval]=$dbobjval;
			}
		}
		if($status=="pin"){
			unset($arr_unpin_icon[$objval]);
		}else if($status=="unpin"){
			$arr_unpin_icon[$objval]=$objval;
		}
		$implode_icon="";
		if(count($arr_unpin_icon)>0){
			$implode_icon=implode(",",$arr_unpin_icon);
		}
		
		$qry_update="UPDATE users set unpin_icon='".$implode_icon."' WHERE id='".$authUserID."'";
		$res_update=imw_query($qry_update);
		$db_unpin_icon=$implode_icon;
	}

	echo $db_unpin_icon;
	exit();
}
?>