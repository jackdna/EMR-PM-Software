<?php
include_once(dirname(__FILE__) .'/../../config/globals.php');
include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");

//$OBJRabbitmqExchange = new Rabbitmq_exchange();

class patient_allergies
{
    public $OBJRabbitmqExchange;

    public function __construct()
    {
        $this->OBJRabbitmqExchange = new Rabbitmq_exchange();
    }
    
    public function addUpdateAllergy($patient_id=0, $arrAllergies=array())
    {
        if($patient_id==0) return false;
        
        //API Resource
        $resource='PatientAllergies';
        $method='POST';
        /*Rabbit MQ call to create patient allergy at Portal*/
        $response=$this->OBJRabbitmqExchange->send_request($arrAllergies, $arrAllergies['externalId'], $resource,$method);
        $response=json_decode($response, true);
        
        if(count($response) > 0 && $response['externalId']==$arrAllergies['externalId']) {
            $this->updateErpAllergy($response);
        }
        
    }

    public function updateErpAllergy($response=array()) {
        $qry = "Update lists SET erp_id = '".$response['id']."' 
                WHERE pid =".$response['patientExternalId']." 
                AND id ='".$response['externalId']."'";

		$rs = imw_query($qry);
    }
    
    public function deleteAllergy($allergy_id=0)
    {
        if($allergy_id==0) return false;
        
        //API Resource
        $resource='PatientAllergies?externalId='.$allergy_id;
        $method='DELETE';

        /*Rabbit MQ call*/
        $response=$this->OBJRabbitmqExchange->send_request(array(), $allergy_id, $resource,$method);
        $response=json_decode($response, true);
    }     
}

