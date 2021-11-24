<?php
/*
*	The MIT License (MIT)
*	Distribute, Modify and Contribute under MIT License
* Use this software under MIT License
*/
session_start();
include_once(__DIR__. "/../../common/conDb.php");
include_once(__DIR__."/../../admin/classObjectFunction.php");
$objManageData = new manageData;

$return = array();
$ptConfId = isset($_POST['confirmation_id']) ? (int)$_POST['confirmation_id'] : 0;
$med_type = isset($_POST['med_fld']) ? trim($_POST['med_fld']) : '';
$qryStr = "";
$success = false;
$arr = array('blank1','blank2','blank3','blank4','propofol','midazolam');
$arrSec = array('Fentanyl','ketamine','labetalol','spo2','o2lpm');
$tblName = 'localanesthesiarecordmedgrid';
if(in_array($med_type,$arrSec)) {
	$tblName = 'localanesthesiarecordmedgridsec';	
}
if( $ptConfId && $med_type ) {

	$qryStr .= "Update ".$tblName." Set ";
	for($i = 1; $i <= 20; $i++) {
		$valD = trim($_POST['o'.$med_type."_".$i]);
		$valD = addslashes($valD);
		$valT = $valD ? ($_POST['ot_'.$med_type."_".$i] ? $_POST['ot_'.$med_type."_".$i] : date("Y-m-d H:i:s")) : "";
		
		$val = ($valD && $valT) ? $valD . '@@' .$valT : '';
		$qryStr .= $med_type."_".$i." = '".$val."'".($i==20 ? '' : ', ');
	}
	$qryStr .= "Where confirmation_id = ".$ptConfId;
	$res = imw_query($qryStr);
	if( $res ) $success = true;
}

$return['success'] = $success;
$return['med_type'] = $med_type;

if( $success ) {
	$fields = "";
	foreach(range(1,20) as $index)
		$fields .= $med_type.'_'.$index.",";

	$qry = "Select ".$fields."confirmation_id From ".$tblName." Where confirmation_id = ".$ptConfId;
	$sql = imw_query($qry) or die(imw_error());
	$row = imw_fetch_assoc($sql);
	$data = array();
	foreach($row as $field => $d ){
		if($field  == 'confirmation_id' ) continue;
		$arr = explode('@@',$d);
		$data[$field] = $arr[0];
		$data['t_'.$field] = $objManageData->getFullDtTmFormatLocalAnes($arr[1]);

	}
	$return['data'] = $data;
}

echo json_encode($return);
?>