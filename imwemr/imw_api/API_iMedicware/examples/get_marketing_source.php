<?php
require_once '../imwemr_api.php';
$IMEDIC_API_CALL_OBJ=new iMedicApiClient();
$fields=array();
$response = $IMEDIC_API_CALL_OBJ->post('get_marketing_source',$fields);
$responseArray=json_decode($response, true);
echo '<pre>';
print_r($responseArray);
?>