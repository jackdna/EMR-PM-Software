<?php
require_once("../../../config/globals.php");

$erx_request = (empty($_REQUEST['erx_request']) == false) ? $_REQUEST['erx_request'] : '';
$ajax_request = (empty($_REQUEST['ajax_request']) == false) ? $_REQUEST['ajax_request'] : '';

$qryRes = imw_query("select EmdeonUrl from copay_policies where Allow_erx_medicare = 'Yes'");
$qryRes = imw_fetch_assoc($qryRes);
$EmdeonUrl = $qryRes['EmdeonUrl'];

$pid = $_SESSION['authId'];

$qryRes = imw_query("select eRx_user_name, erx_password,eRx_facility_id from users where id = '$pid'");
$qryRes = imw_fetch_assoc($qryRes);
$eRx_user_name = $qryRes['eRx_user_name'];
$erx_password = $qryRes['erx_password'];
//$eRx_facility_id = $qryRes['eRx_facility_id'];
$eRx_facility_id = trim($_SESSION['login_facility_erx_id']);

if(isset($ajax_request) && empty($ajax_request) == false){
	if(count($qryRes)>0 && $eRx_user_name != '' && $erx_password != '' && $EmdeonUrl != ''){
		switch($erx_request){
			case 'DUR':
					$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true&target=jsp/lab/preference/DURPreference.jsp&LoadList=true";
			break;
			case 'Pharmacy':
					$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true&target=jsp/lab/preference/PharmacyPrefView.jsp&LoadList=true";
			break;
			case 'Prescription':
					$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true&target=jsp/lab/preference/RxPreferenceMgt.jsp&LoadList=true";
					$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true&target=jsp/lab/preference/RxPreferenceMgt.jsp&actionCommand=GetListsNew";
			break;
		}
		echo $url;
	}
	exit();
}
?>