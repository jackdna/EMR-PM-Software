<?php
set_time_limit(600);
require_once("../../../../config/globals.php");
$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$s		= isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'pos_prac_code';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$p		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$f		= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';
switch($task){
	case 'delete':
		$pos_id = $_POST['pkId'];
		$q 		= "DELETE FROM pos_tbl WHERE pos_id IN ($pos_id)";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$pos_id = $_POST['pos_id'];
		unset($_POST['pos_id']);
		unset($_POST['task']);
		$query_part = "";
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		if($pos_id==''){
			$q = "INSERT INTO pos_tbl SET ".$query_part;
		}else{
			$q = "UPDATE pos_tbl SET ".$query_part." WHERE pos_id='".$pos_id."'";
		}
		$res = imw_query($q);
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';//.imw_error()."\n".$q;
		}
		break;
	case 'show_list':
		$q = "SELECT p.pos_id, p.pos_code, p.pos_prac_code, p.pos_description, p.facility AS facility, if(f.name is null,'',f.name) AS facilityName, 
				p.admit_discharge, p.nocopay, p.category_code, p.status FROM pos_tbl p 
				LEFT JOIN facility f ON (f.id=p.facility) ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$facilities = facilities();
		echo json_encode(array('records'=>$rs_set,'facilities'=>$facilities));
		break;
	default: 
}

function facilities()	{
	$q="select id, name from  facility";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		$result=array();
		while($rs=imw_fetch_assoc($res)){
			$result[]=$rs;
		}
		return $result;
	}
	return false;
}
?>
