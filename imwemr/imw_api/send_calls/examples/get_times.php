<?php
$ignoreAuth = true;
require_once('../../../interface/globals.php');

require_once '../send_api.php';


header('Content-Type:  application/json');


$mdp = new  MdpApiClient();
$fields  = array(
	'customerId' => API_CUSTOMER_ID
	,'startDate' => '10/07/2016'
	,'endDate' => '11/07/2016'
	,'locationId' => '0'
	,'visitTypeId' => '0'
	,'doctorId' => '0'
	,'dateOnly'=>false
	,'limit'=>0


	);

$response = $mdp->post('/appointments/get_times',$fields);

$responseArray=(array)json_decode($response);
if ($responseArray)
	print_r($responseArray);
else
	echo $response;




