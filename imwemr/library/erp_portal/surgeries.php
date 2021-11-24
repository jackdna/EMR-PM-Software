<?php
include_once(dirname(__FILE__) .'/../../config/globals.php');
include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");

//$OBJRabbitmqExchange = new Rabbitmq_exchange();

class Surgeries
{

    public $OBJRabbitmqExchange;

    public function __construct()
    {
        $this->OBJRabbitmqExchange = new Rabbitmq_exchange();
    }

    public function addUpdateSurgeries($patient_id=0, $arrSurgeries=array())
    { 
        /*ini_set('display_startup_errors',1);
        ini_set('display_errors',1);
        error_reporting(-1);*/
        if($patient_id==0) return false;

        $resource='PatientSurgeries';
        $method='POST';
        /*Rabbit MQ call to create patient at Portal*/
        $response=$this->OBJRabbitmqExchange->send_request($arrSurgeries, $arrSurgeries['ExternalId'], $resource,$method);
        $response=json_decode($response, true);
      if(count($response) > 0 && $response['externalId']==$arrSurgeries['ExternalId']) {
          $this->updateErpSurgery($response);
      }
        
    }

    public function updateErpSurgery($response=array()) {
        $qry = "Update lists SET erp_id = '".$response['id']."' 
                WHERE pid =".$response['patientExternalId']." 
                AND id ='".$response['externalId']."'";
                
		$rs = imw_query($qry);
    }
    
    public function deleteSurgery($surgery_id=0)
    {
        if($surgery_id==0) return false;
        
        //API Resource
        $resource='PatientSurgeries?externalId='.$surgery_id;
        $method='DELETE';

        /*Rabbit MQ call*/
        $response=$this->OBJRabbitmqExchange->send_request(array(), $surgery_id, $resource,$method);
        $response=json_decode($response, true);
    }

}