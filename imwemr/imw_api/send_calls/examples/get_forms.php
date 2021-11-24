<?php
$ignoreAuth = true;
require_once('../../../interface/globals.php');

require_once '../send_api.php';


//header('Content-Type:  application/json');


$mdp = new  MdpApiClient();
$fields  = array(
	'customerId' => API_CUSTOMER_ID
	);

$response = $mdp->post('/support/get_forms',$fields);


$responseArray=(array)json_decode($response);
if ($responseArray)
	print_r($responseArray);
else
	echo $response;




