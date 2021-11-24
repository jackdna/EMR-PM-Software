<?php
//TO RECEIVE CALLS FROM MD PROSPECTES AND RESPONSING

$ignoreAuth = true;
require_once('../../config/globals.php');

require_once('responseClass.php');
$objResponse=new responseClass;

$USER_KEY = 'MDprospectsAPIclient';
$SECRET_KEY = API_SECRET_KEY;
$USER_AGENT = 'iMedicWareApiclient';


$mode=$_REQUEST['mode'];
//$arrHeader=getallheaders();

//$md5Received=$arrHeader['sec_code'];
$md5Received=$_REQUEST['sec_code'];

$postArrReceived=$_POST;

$md5Created = md5($USER_KEY.$USER_AGENT.$SECRET_KEY);
$returnArr="";

if($md5Received==$md5Created){ 
	switch($mode){
		case 'get_doctors':
		$returnArr=$objResponse->get_doctors();
		break;
		case 'get_locations':
		$returnArr=$objResponse->get_facilities();
		break;
		case 'get_appointment_types':
		$returnArr=$objResponse->get_appt_proc();
		break;
		case 'get_patient_details':
		$returnArr=$objResponse->get_patient_details($postArrReceived);
		break;
		case 'create_patient':
		$returnArr=$objResponse->create_patient($postArrReceived);
		break;
		case 'get_appointments':
		$returnArr=$objResponse->get_appointments($postArrReceived);
		break;
		case 'get_available_times':
		$returnArr=$objResponse->get_available_times($postArrReceived);
		break;
		case 'book_appointment':
		$returnArr=$objResponse->book_appointment($postArrReceived);
		break;
		case 'get_doctors_availability':
		$returnArr=$objResponse->get_doctors_availability($postArrReceived);
		break;		
		case 'get_marketing_source':
		$returnArr=$objResponse->get_marketing_source($postArrReceived);
		break;		
		case 'get_appointments_info':
		$returnArr=$objResponse->get_appointments_info($postArrReceived);
		break;		
		case 'get_statement':
		$returnArr=$objResponse->get_statement($postArrReceived);
		break;		
		case 'update_appointment':
		$returnArr=$objResponse->update_appointment($postArrReceived);
		break;		

	}
	
}else{
	$returnArr['success']=0;
	$returnArr['error_code']=101;
	$returnArr['error_description']='Authorization failed.';
}
echo json_encode($returnArr);
?>
