<?php
$ignoreAuth = true;
require_once('../../../interface/globals.php');
require_once '../send_api.php';

header('Content-Type:  application/json');


$mdp = new  MdpApiClient();
$fields  = array(
	'customerId' => API_CUSTOMER_ID
	);

$response = $mdp->post('/support/get_types',$fields);


$responseArray=(array)json_decode($response);
if ($responseArray){
	$responsArray=$responseArray;
	//CHECK AND INSERT ID
	foreach($responsArray as $data){
		if($data['locationName']!=''){
			$qry="UPDATE slot_procedures SET api_id='".$data['visitTypeId']."' WHERE proc='".$data['visitType']."' AND LOWER(active_status)='yes'";
			mysql_query($qry);
		}
	}

	print_r($responseArray);
}else{
	echo $response;
}




