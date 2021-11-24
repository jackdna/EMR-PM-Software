<?php
require_once(dirname(__FILE__).'/../common/functions.inc.php');
require_once("../reports/mur_stage2/functions.php");
$objManageData = new DataManage;
//error_reporting(-1);
//ini_set("display_errors",-1);

//--- GET PATIENT ID & provider id----
$patientId = $_SESSION['patient'];
$provider = $_SESSION['authId'];

//--- GET ERX STATUS AND EMDEON ACCESS URL -------
$objDataManage->QUERY_STRING = "select Allow_erx_medicare, EmdeonUrl from copay_policies";
$copay_policies_res = $objDataManage->mysqlifetchdata();
$Allow_erx_medicare = $copay_policies_res[0]['Allow_erx_medicare'];
$EmdeonUrl = trim($copay_policies_res[0]['EmdeonUrl']);
if(strtolower($Allow_erx_medicare)=='no'){die('eRx is not allowed. Contact Administrator.');}
if(empty($EmdeonUrl)===true){die('Emdeon clinician service mode not defined. Contact Administrator.');}


//--- GET ERX USERNAME AND PASSWORD --------
$objDataManage->QUERY_STRING = "select eRx_user_name, erx_password,eRx_facility_id,lname,fname,mname 
		from users where id = '$provider'";
$phyQryRes = $objDataManage->mysqlifetchdata();

$eRx_user_name = trim($phyQryRes[0]['eRx_user_name']);
$erx_password = trim($phyQryRes[0]['erx_password']);
$eRx_facility_id = trim($phyQryRes[0]['eRx_facility_id']);
if($eRx_user_name=='' || $erx_password==''){die('Emdeon Clinician credentials are not entered. Contact Administrator.');}

//--- GET ERX FACILITY ID ----------
//echo $eRx_facility_id.'; '.$erx_password.'; '.$eRx_user_name;
if(empty($eRx_facility_id) == true && empty($erx_password) != true && empty($eRx_user_name) != true){
	$eRx_facility_id = trim($objDataManage->geteRxFacilityId($provider,$EmdeonUrl));
}
if($eRx_facility_id==''){die('Emdeon Clinician Facility ID not found. Contact Administrator.');}

//-IF PATIENT ID FOUND---
if(empty($patientId) === false){
	$pat_qry = "select lname, fname, mname from patient_data where id = '$patientId'";
	$patQryRes = $objManageData->mysqlifetchdata($pat_qry);
	$patient_name_arr = array();
	$patient_name_arr["LAST_NAME"] = $patQryRes[0]['lname'];
	$patient_name_arr["FIRST_NAME"] = $patQryRes[0]['fname'];
	$patient_name_arr["MIDDLE_NAME"] = $patQryRes[0]['mname'];
	$patient_name = $objManageData->__changeNameFormat($patient_name_arr);
	$patient_name .= ' - '.$patientId;
	
	//--- GET PATIENT ERX PERSON ID AND HSI VALUE -----
	$objDataManage->QUERY_STRING = "select patient_eRx_person_hsi,patient_eRx_person
			from patient_erx_prescription where patient_eRx_Patient_id = '$patientId'";
	$eRx_pres_res = $objDataManage->mysqlifetchdata();	
	
	$person = $eRx_pres_res[0]['patient_eRx_person'];
	$personhsi = $eRx_pres_res[0]['patient_eRx_person_hsi'];//echo $person.' :: '.$personhsi; die;
	if(empty($person) == true && empty($personhsi) == true){
		$eRx_pres_res = $objDataManage->getPatientPrescription($EmdeonUrl,$eRx_user_name,$erx_password,$eRx_facility_id,$patientId);
		$person = $eRx_pres_res[0]['patient_eRx_person'];
		$personhsi = $eRx_pres_res[0]['patient_eRx_person_hsi'];
	}
	
	$person = $eRx_pres_res[0]['patient_eRx_person'];
	$personhsi = $eRx_pres_res[0]['patient_eRx_person_hsi'];
	
	//--- GET PATIENT RX HISTORY ------
	$dataArr = $objDataManage->getPatientRxHistory($EmdeonUrl,$eRx_user_name,$erx_password,$eRx_facility_id,$patientId,$person,$personhsi,date('m/d/Y'));

	//--- PATIENT RX HISTORY ------
	$medicationCnt = '&lt;none&gt;';
	$rxHistoryArr = $dataArr['rx_history'];
	$medicationCnt = count($rxHistoryArr);
	die('Electronic Prescriptions found <b>'.$medicationCnt.'</b> for today.');
}
echo '&lt;No Result.&gt;';
?>