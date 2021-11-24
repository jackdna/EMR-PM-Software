<?php
require_once '../imwemr_api.php';
$IMEDIC_API_CALL_OBJ=new iMedicApiClient();

$fields = array(
		'doctor_id' => '',		//optional
		'location_id' => '9',
		'schedule_from_date' => '2016-09-05',
		'schedule_to_date' => '2016-11-30'
		);
		
$response = $IMEDIC_API_CALL_OBJ->post('get_available_times',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';
print_r($responseArray);
?>