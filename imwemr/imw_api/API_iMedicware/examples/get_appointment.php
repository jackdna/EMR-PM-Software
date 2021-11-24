<?php
require_once '../imwemr_api.php';

//FROM BELOW LIST ANY ONE PARAMETER MUST PASS TO GET RESULT
// -PATIENT ID
// -DOCTOR ID
// -LOCATION ID
// -START DATE & END DATE
// -MODIFIED START DATE & MODIFED END DATE 

$IMEDIC_API_CALL_OBJ=new iMedicApiClient();
		$fields = array(
				'patient_id' => '',	
				'doctor_id' => '',	
				'location' => '',	
				'start_date' => '2017-05-01',	
				'end_date' => '2017-05-22',		
				'modified_date_start' => '',	
				'modified_date_end' => '',		
				'modified_time_start' => '13:00',	//(H:i - 24 HOURS FORMAT)
				'modified_time_end' => '16:30',		//(H:i - 24 HOURS FORMAT)
				'sort_order' => 'asc',	// (asc/desc)
				'limit' => '0',			// (Default-1000)
				'page_number' => 0
				);
				
$response = $IMEDIC_API_CALL_OBJ->post('get_appointments',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';
print_r($responseArray);
?>