<?php
$ignoreAuth = true;
require_once('../../../interface/globals.php');

require_once '../send_api.php';

//header('Content-Type:  application/json');


$mdp = new  sendApiClient();
$fields  = array(
	'customerId' => API_CUSTOMER_ID
	,'patientId' => 413605
	,'patientDob' => '1980-01-01'
	,'apptStartDate' => '2016-11-29 10:15:00'
	,'visitTypeId' => '8'
	,'locationId' => '1'
	,'doctorId' => '196'

	);

$response = $mdp->post('/appointments/book_appointment',$fields);


$responseArray=(array)json_decode($response);
if ($responseArray)
	print_r($responseArray);
else
	echo $response;




