<?php
include_once(dirname(__FILE__) .'/../../config/globals.php');
include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
include_once($GLOBALS['fileroot']."/library/classes/cls_common_function.php");

$OBJCommonFunction = new CLSCommonFunction;
$OBJRabbitmqExchange = new Rabbitmq_exchange();

class patient_medications
{
    
    public function addUpdateMedication($patient_id=0, $arrMedications=array())
    {
        if($patient_id==0) return false;
        
        global $OBJRabbitmqExchange;
      
        //API Resource
        $resource='PatientMedications';
        $method='POST';

        //GETTING DOCTOR ID
        if(empty($arrMedications['doctorExternalId'])==true){ 
            $qry="Select providerID from patient_data where id = '".$patient_id."'";
            $rs=imw_query($qry);
            $res=imw_fetch_assoc($rs);
            $doctor_id=$res['providerID'];
            if($doctor_id<=0){
                $qry="Select sa_doctor_id from schedule_appointments where sa_patient_id = '".$patient_id."' ORDER BY sa_app_start_date, sa_app_starttime LIMIT 0,1";
                $rs=imw_query($qry);
                $res=imw_fetch_assoc($rs);
                $doctor_id=$res['sa_doctor_id'];
            }
            if($doctor_id<=0) return false;
            $arrMedications['doctorExternalId']=$doctor_id;
        }
        
        //CHECK IF erp_id EXIST
        $qry="Select erp_id from lists where id = '".$arrMedications['externalId']."'";
        $rs=imw_query($qry);
        $res=imw_fetch_assoc($rs);
        $erp_id=$res['erp_id'];
        $arrMedications['id']=$erp_id;

        /*Rabbit MQ call to create patient at Portal*/
        $response=$OBJRabbitmqExchange->send_request($arrMedications, $arrMedications['externalId'], $resource,$method);
        $response=json_decode($response, true);
    
        if(count($response) > 0 && $response['externalId']==$arrMedications['externalId']) {
            $this->updateErpMedication($response);
        }
        
    }

    public function updateErpMedication($response=array()) {
        $qry = "Update lists SET erp_id = '".$response['id']."' 
                WHERE pid =".$response['patientExternalId']." 
                AND id ='".$response['externalId']."'";

		$rs = imw_query($qry);
    }
    
    public function deleteMedication($medication_id=0)
    {
        if($medication_id==0) return false;
        
        global $OBJRabbitmqExchange;
        
        //API Resource
        $resource='PatientMedications?externalId='.$medication_id;
        $method='DELETE';

        /*Rabbit MQ call*/
        $response=$OBJRabbitmqExchange->send_request(array(), $medication_id, $resource,$method);
        
        $response=json_decode($response, true);
    }
    
    public function addUpdateMedicationRecords($patient_id=0, $arrMedications=array())
    {
        if($patient_id==0) return false;
        
        global $OBJRabbitmqExchange;
        
        //API Resource
        $resource='PatientMedicationRecords';
        $method='POST';
        
        /*Rabbit MQ call to create patient at Portal*/
        $response=$OBJRabbitmqExchange->send_request($arrMedications, $arrMedications['externalId'], $resource,$method);
        $response=json_decode($response, true);
        if(count($response) > 0 && $response['externalId']==$arrMedications['externalId']) {
            $this->updateErpMedicationRecords($response);
        }
        
    }

    public function updateErpMedicationRecords($response=array()) {
        $qry = "Update lists SET medication_record_erp_id = '".$response['id']."' 
                WHERE pid =".$response['patientExternalId']." 
                AND id ='".$response['externalId']."'";

        $rs = imw_query($qry);
    }
    
    public function deleteMedicationRecords($medication_id=0)
    {
        if($medication_id==0) return false;
        
        global $OBJRabbitmqExchange;
        
        //API Resource
        $resource='PatientMedicationRecords?externalId='.$medication_id;
        $method='DELETE';

        /*Rabbit MQ call*/
        $response=$OBJRabbitmqExchange->send_request(array(), $medication_id, $resource,$method);
        
        $response=json_decode($response, true);
    }
}

