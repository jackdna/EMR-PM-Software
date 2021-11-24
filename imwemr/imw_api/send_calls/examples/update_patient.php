<?php
$ignoreAuth = true;
require_once('../../../interface/globals.php');
require_once '../send_api.php';

header('Content-Type:  application/json');


$mdp = new  MdpApiClient();
/*$fields  = array(
	'customerId' => API_CUSTOMER_ID
	,'patientId'=> 1
	,'current_dob'=> '01/01/1955'
	,'new_dob' => ''
	,'first_name'=>'MDweb'
	,'last_name'=>'Updated'
	,'email'=>'email_changed@glacial.com'
	,'gender'=>'F'
	,'marital_status'=>'S'
	,'primary_phone'=>'555-454-1551'
	,'cell_phone'=>'444 (555) 1474'
	,'secondary_phone'=>''
	,'address'=>'test'
	,'city'=>'test'
	,'state'=>'CA'
	,'zip'=>'54124'
	,'ethnicity'=>''
	
	);*/

	$responsArray=array();
	
	$qry_patient="SELECT api_id as 'patientId', DOB as 'current_dob',DOB as new_dob, fname as first_name,lname as last_name,
				email,UPPER(SUBSTRING(sex,1,1)) as gender,UPPER(SUBSTRING(status,1,1)) as marital_status,
				phone_home as primary_phone, phone_cell as cell_phone,phone_biz as secondary_phone,street2 as address,city,state,
				postal_code as zip,ethnicity FROM patient_data WHERE id='".$mdp->patient_id."'";
	$res_patient=mysql_query($qry_patient) or die(mysql_error());
	$row_patient=mysql_fetch_assoc($res_patient);
	$row_patient['customerId']=$mdp->customerId;
	$row_patient['primary_phone']=$mdp->set_phone_format($row_patient['primary_phone']);
	$row_patient['cell_phone']=$mdp->set_phone_format($row_patient['cell_phone']);
	$row_patient['secondary_phone']=$mdp->set_phone_format($row_patient['secondary_phone']);

	$fields=array();
	$fields=$row_patient;
	
	$response = $mdp->post('/patients/update_patient',$fields);


$responseArray=(array)json_decode($response);
if ($responseArray)
	print_r($responseArray);
else
	echo $response;




