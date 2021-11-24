<?php 
require_once('../globals.php');
//require_once("../remote/common_functions.php");
require_once('class.imm.logic.php');
$dd_fac_id		= isset($_GET['fac']) 		? trim($_GET['fac']) 		: '';
$dd_prov_id		= isset($_GET['prov']) 		? trim($_GET['prov']) 		: '';
$sort_by		= isset($_GET['sort_by']) 	? trim($_GET['sort_by']) 	: '';
$sort_order		= isset($_GET['sort_order']) 	? trim($_GET['sort_order']) 	: '';


$tz_value = trim(urldecode($_GET['local_tz']));
if(isset($iMonitor_Server) && strtolower(trim($iMonitor_Server))=='centerforsight'){
//	$tz_value =  -480;
}else if(isset($iMonitor_Server) && strtolower(trim($iMonitor_Server))=='dedhameye'){
	if($tz_value == '300') $tz_value == '240';
	else if($tz_value == '240') $tz_value == '300'; 
}

$iMMLogic = new iMMLogic($dd_fac_id,$dd_prov_id,$tz_value,$sort_by,$sort_order);

$iMMData = $iMMLogic->getSchData();

echo json_encode($iMMData);die;
?>