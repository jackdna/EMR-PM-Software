<?php
require_once('../globals.php');
function core_name_format1($lname, $fname, $mname = "", $pfx = "", $sfx = ""){
	$return = "";
	if($lname != "" && $fname != "" && $mname != ""){
		$return .= $lname.", ".$fname." ".substr($mname, 0, 1).".";
	}else if($lname != "" && $fname != ""){
		$return .= $lname.", ".$fname;
	}else if($lname != ""){
		$return .= $lname;
	}
	if($pfx != ""){
		$return = $pfx." ".$return;
	}
	if($sfx != ""){
		$return = $return." ".$sfx;
	}
	return $return;
}

function objectsIntoArray($arrObjData, $arrSkipIndices = array()){
	$arrData = array();

	// if input is object, convert into array
	if (is_object($arrObjData)) {
		$arrObjData = get_object_vars($arrObjData);
	}
	
	if (is_array($arrObjData)) {
		foreach ($arrObjData as $index => $value) {
			if (is_object($value) || is_array($value)) {
				$value = objectsIntoArray($value, $arrSkipIndices); // recursive call
			}
			if (in_array($index, $arrSkipIndices)) {
				continue;
			}
			$arrData[$index] = $value;
		}
	}
	return $arrData;
}

function parse_xml_data($str_xml){
	$xmlObj = simplexml_load_string($str_xml);
	$arrXml = objectsIntoArray($xmlObj);
	//print_r($arrXml);
	return $arrXml;
}


function refine_input($str){
	$str = preg_replace("/-/", " ", $str);
	$str = str_replace("/", " ", $str);
	$str = preg_replace("/&/", "and", $str);
	$str = str_replace('<','&lt;',$str);
	$str = str_replace('>','&gt;',$str);
	return $str;
}

function iMonRooms(){
	$rooms=false;
	$q = "SELECT id,room_no FROM mac_room_desc WHERE delete_status=0";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$rooms=array();
		while($rs = imw_fetch_assoc($res)){
			$rooms[$rs['room_no']]=$rs['id'];
		}
	}
	return $rooms;
}

function get_imon_recent_tech_scribe_from_list($provIds){
	$tmp_provIds_array = preg_split('@,@', trim($provIds), NULL, PREG_SPLIT_NO_EMPTY);
	$tmp_provIds_array = array_reverse($tmp_provIds_array);
	foreach($tmp_provIds_array as $wv_user_id){
		$uname = get_imon_user_types($wv_user_id);
		if(!empty($uname)) return $uname;
	}
	return '';
}

function get_imon_user_types($user){
	$q = "SELECT user_type,fname,lname,mname FROM users WHERE id='$user' LIMIT 1";
	$res = imw_query($q);
	if($res && imw_num_rows($res)==1){
		$rs = imw_fetch_assoc($res);
		if($rs['user_type']=='3' || $rs['user_type']=='13'){
			return core_name_format($rs['lname'], $rs['fname'], $rs['mname']);
		}
	}
	return '';
}

?>