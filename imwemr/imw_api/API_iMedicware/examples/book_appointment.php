<?php
require_once '../imedicware_api.php';
$IMEDIC_API_CALL_OBJ=new iMedicApiClient();
$fields = array(
		'patient_id' => '413605',
		'appt_start_date' => '2016-11-30',
		'appt_start_time' => '12:15',	
		'visit_type_id' => '34',
		'location_id' => '9',
		'doctor_id' => '196'
		);
$response = $IMEDIC_API_CALL_OBJ->post('book_appointment',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';

print_r($responseArray);
?>