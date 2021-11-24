<?php
$ignoreAuth = true;
require_once('../../../interface/globals.php');

require_once '../send_api.php';


//header('Content-Type:  application/json');

$mdp = new  sendApiClient();

$fields  = array(
	'customerId' => $mdp->customerId
	);
	
	pre($fields);
exit;	  
$response = $mdp->post('/support/get_doctors',$fields);

$responseArray=(array)json_decode($response);
if ($responseArray){
	$responsArray=$responseArray;
	//CHECK AND INSERT ID
	foreach($responsArray as $data){
		list($fname, $mname, $lname)=explode(' ', $data['doctorName']);
		if($fname!='' && $lname!=''){
			$qry="UPDATE users SET api_id='".$data['doctorId']."' 
			WHERE fname='".$fname."' AND mname='".$mname."' AND lname='".$lname."' AND delete_status='0'";
			mysql_query($qry);
		}
	}	
	print_r($responseArray);
	
}else{
	echo $response;
}




