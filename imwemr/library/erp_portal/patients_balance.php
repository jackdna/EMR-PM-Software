<?php
include_once(dirname(__FILE__) .'/../../config/globals.php');
include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");

$OBJRabbitmqExchange = new Rabbitmq_exchange();

class Patients_balance
{
    	
	public function patientResponsibleBalance($patient_id=0) {
		if($patient_id>0) {
			$sql=imw_query("SELECT id FROM `patient_data` WHERE id='$patient_id' and erp_patient_id != '' and erp_patient_id != '0' and erp_patient_id != NULL ");
			if($sql && imw_num_rows($sql)==1) {
			
				$curr_time = date('H:i:s');
				$curr_date = date('Y-m-d');
				
				$amountDue=0;
				$query="select SUM(patientDue) as patientBalance from patient_charge_list where del_status='0' and patient_id='$patient_id' group by patient_id ";
				$res=imw_query($query);
				if($res && imw_num_rows($res)>0) {
					$row=imw_fetch_assoc($res);
					$amountDue=$row['patientBalance'];
				}
				
				global $OBJRabbitmqExchange;
				$method="POST";
				$resource='PatientResponsibleBalance';
				
				$createdUtc=$curr_date.'T'.$curr_time;
				
				$params=array();
				$params['patientExternalId']=$patient_id;  
				$params['amountDue']=$amountDue;
				$params['dueDate']=""; 
				$params['createdUtc']=$createdUtc;
				
				$messageId=$patient_id.'-'.time();
				
				if($OBJRabbitmqExchange) {
					//Rabbit MQ call to create Patient Balance at Portal
					$OBJRabbitmqExchange->send_request($params,$messageId,$resource,$method);
				}
			}
		}

	}
	
	
}

