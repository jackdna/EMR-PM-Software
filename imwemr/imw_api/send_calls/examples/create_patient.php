<?php
$ignoreAuth = true;
require_once('../../../interface/globals.php');

require_once '../send_api.php';


//header('Content-Type:  application/json');


$mdp = new  MdpApiClient();
/*$fields  = array(
	'customerId' => API_CUSTOMER_ID
	,'epm_patient_id' => 123
	,'first_name' => 'MDWeb'
	,'last_name' => 'Test'
	,'dob' => '01/01/1955'
	,'email'=>'testing@glacial.com'
	,'gender'=>'M'
	,'marital_status'=>'S'
	,'primary_phone'=>'555-454-1551'
	,'cell_phone'=>''
	,'secondary_phone'=>''
	,'address'=>'test'
	,'city'=>'test'
	,'state'=>'CA'
	,'zip'=>'54124'
	,'ethnicity'=>''
	
	);*/

	$responsArray=array();
	
	$qry_patient="SELECT id as epm_patient_id,fname as first_name,lname as last_name,
	DOB as 'dob',email,SUBSTRING(sex,1,1) as gender,UPPER(SUBSTRING(status,1,1)) as marital_status,
	phone_home as primary_phone,phone_cell as cell_phone,phone_biz as secondary_phone,street2 as address,city,state,postal_code as zip,
	ethnicity FROM patient_data WHERE id='".$mdp->patient_id."'";
	$res_patient=mysql_query($qry_patient) or die(mysql_error());
	$row_patient=mysql_fetch_assoc($res_patient);
	$row_patient['customerId']=$mdp->customerId;
	$row_patient['primary_phone']=$mdp->set_phone_format($row_patient['primary_phone']);
	$row_patient['cell_phone']=$mdp->set_phone_format($row_patient['cell_phone']);
	$row_patient['secondary_phone']=$mdp->set_phone_format($row_patient['secondary_phone']);
	
	$fields=array();
	$fields=$row_patient;
	$response = $this->post('/patients/create_patient',$fields);
	$responseArray=(array)json_decode($response);

	//CHECK AND INSERT ID
	if($responseArray['patientId']>0){
		$qry="UPDATE patient_data SET api_id='".$responseArray['patientId']."' WHERE id='".$mdp->patient_id."'";
		mysql_query($qry);
	}

	$response = $mdp->post('/patients/create_patient',$fields);


$responseArray=(array)json_decode($response);
if ($responseArray)
	print_r($responseArray);
else
	echo $response;




