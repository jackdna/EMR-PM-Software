<?php
require_once '../imwemr_api.php';
$IMEDIC_API_CALL_OBJ=new iMedicApiClient();

$fields = array(
		'location_id' => '9',
		'schedule_date' => '2016-09-19'
		);
		
$response = $IMEDIC_API_CALL_OBJ->post('get_doctors_availability',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';
print_r($responseArray);
?>