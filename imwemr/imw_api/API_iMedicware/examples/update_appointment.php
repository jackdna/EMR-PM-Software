<?php
require_once '../imwemr_api.php';
$IMEDIC_API_CALL_OBJ=new iMedicApiClient();
$fields = array(
		'patient_id' => '109725', //required
		'imw_appt_id' => '',	//either imw_appt_id or appt_start_date		
		'appt_date' => '2018-05-07', 	//either appt_date or imw_appt_id YYYY-MM-DD
		'appt_status' => 'D',	 //required
		'appt_comment' => ''	 //optional
		);
$response = $IMEDIC_API_CALL_OBJ->post('update_appointment',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';

print_r($responseArray);
?>