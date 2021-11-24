<?php
require_once '../imwemr_api.php';
$IMEDIC_API_CALL_OBJ=new iMedicApiClient();
$fields = array(
        'patient_id' => '193150' //CONDITIONAL
        ,'first_name' => 'Test'
        ,'last_name' => 'Doe'
        ,'dob' => '1975-05-08'
        ,'gender'=>'male'
        ,'zip'=>'01002hngh'
        );
$response = $IMEDIC_API_CALL_OBJ->post('get_patient_details',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';
print_r($responseArray);
?>