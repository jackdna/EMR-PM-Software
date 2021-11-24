<?php
require_once '../imwemr_api.php';
$IMEDIC_API_CALL_OBJ=new iMedicApiClient();

$fields = array(
		'schedule_from_date' => '2013-01-11',
		'schedule_to_date' => '2013-01-31',
		);
		
$response = $IMEDIC_API_CALL_OBJ->post('get_appointments_info',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';
print_r($responseArray);
?>