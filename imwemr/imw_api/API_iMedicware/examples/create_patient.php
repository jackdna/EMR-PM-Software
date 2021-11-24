<?php
require_once '../imwemr_api.php';
$IMEDIC_API_CALL_OBJ=new iMedicApiClient();
$fields=array();

$fields = array(
		'md_patient_id' => 20356
		,'first_name' => 'Test_api3'
		,'last_name' => 'doe'
		,'middle_name' => '' 	//optional
		,'dob' => '1987-05-16'
		,'gender'=>'male'
		,'zip'=>'025698'
		,'phone'=>''			//optional
		,'email'=>''			//optional
		,'address'=>''			//optional
		,'marketing_source'=>'' //optional
		);
$response = $IMEDIC_API_CALL_OBJ->post('create_patient',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';
print_r($responseArray);
?>

