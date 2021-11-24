<?php
include_once(dirname(__FILE__) .'/../../config/globals.php');
include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");

$OBJRabbitmqExchange = new Rabbitmq_exchange();

class Patient_payments
{
    	
	public function getPatientPayments() {
		global $OBJRabbitmqExchange;
        $data_arr = array();

        $resource = 'patientPayments/search?alreadySent=false';
        $method = 'GET';
        $message_id = $_SESSION['authId']."-".time();
		
        if($OBJRabbitmqExchange) {
			$response = $OBJRabbitmqExchange->send_request($data_arr, $message_id, $resource, $method);
		
			$response = json_decode($response, true);
		
			if(is_array($response) && isset($response['rows']) && count($response['rows']) >0 ) {
				$this->log_portal_patients_data($response['rows']);
			}
		}
        
	}
	
	public function log_portal_patients_data($response=array()) {
		$pt_portal_ids_arr=array();
		if(is_array($response) && count($response) >0 ) {

			foreach($response as $row) {
				$portal_req_id=trim($row['id']);

				$is_valid_pt = $this->is_valid_pt($row['patientExternalId']);
                if (!$is_valid_pt)
                    continue;
				
				$sql="Select id from erp_patient_payments_data where portal_req_id='".$portal_req_id."' ";
				$QryRes=imw_query($sql);
				if($QryRes && imw_num_rows($QryRes)==0) {
					$data=array();
					$data['portal_req_id']=$row['id'];
					$data['transactionId']=$row['transactionId'];
					$data['createdUtc']=$row['createdUtc'];
					$data['patientExternalId']=$row['patientExternalId'];
					$data['patientComments']=$row['patientComments'];
					$data['referenceNumber']=$row['referenceNumber'];
					$data['cardholderName']=$row['cardholderName'];
					$data['cardholderAddress1']=$row['cardholderAddress1'];
					$data['cardholderAddress2']=$row['cardholderAddress2'];
					$data['cardholderCity']=$row['cardholderCity'];
					$data['cardholderState']=$row['cardholderState'];
					$data['cardholderZip']=$row['cardholderZip'];
					$data['cardholderPhoneNumber']=$row['cardholderPhoneNumber'];
					$data['cardholderEmail']=$row['cardholderEmail'];
					$data['cardType']=$row['cardType'];
					$data['last4CardNumber']=$row['last4CardNumber'];
					$data['cardExpiration']=$row['cardExpiration'];
					$data['amount']=$row['amount'];
					$data['processedAmount']=$row['processedAmount'];
					$data['paymentSuccessful']=$row['paymentSuccessful'];
					$data['alreadySent']=$row['alreadySent'];
					$data['sentOn']=$row['sentOn'];
					$data['created_on']=date('Y-m-d H:i:s');
					$data['operator']=$_SESSION['authId'];
					
					$last_insert_id=AddRecords($data,'erp_patient_payments_data');
					
					$pt_portal_ids_arr[]=$row['id'];
					
					if($last_insert_id) {
						$this->createTaskInUserConsolePrepayment($data,$last_insert_id);
					}
					
				}
			}
		}
		
		if(count($pt_portal_ids_arr) >0) {
			$this->send_acknow($pt_portal_ids_arr);
		}
		
	}
	
	
	public function send_acknow($pt_portal_ids_arr=array()) {
        if (count($pt_portal_ids_arr) > 0) {
            foreach ($pt_portal_ids_arr as $k => $ppid) {
                if (!empty($ppid)) {
                    $data_arr = array();
                    $data_arr["id"] = $ppid;

                    global $OBJRabbitmqExchange;

					$resource = 'PatientPaymentsSent';
					$method = 'POST';
					$message_id = $ppid."-".time();
					
					if($OBJRabbitmqExchange) {
						$OBJRabbitmqExchange->send_request($data_arr, $message_id, $resource, $method);
					}
                }
            }
        }
    }
	

	public function createTaskInUserConsolePrepayment($data=array(),$last_insert_id=0) {
		/*Get data to create Task according rule in rule manager*/
		$createdUtcArr=explode('.',$data['createdUtc']);
		$createdUtc=str_replace('T',' ',$createdUtcArr[0]);
		$patient_portal_payments='';
		$patient_portal_payments.='<br />Creation Date => '.$createdUtc;
		$patient_portal_payments.='<br />Transaction ID => '.$data['transactionId'];
		$patient_portal_payments.='<br />Patient ID => '.$data['patientExternalId'];
		$patient_portal_payments.='<br />Comments => '.$data['patientComments'];
		$patient_portal_payments.='<br />Reference Number => '.$data['referenceNumber'];
		$patient_portal_payments.='<br />Card Holder Name => '.$data['cardholderName'];
		$patient_portal_payments.='<br />Card Holder Address => '.$data['cardholderAddress1'].' '.$data['cardholderAddress2'];
		$patient_portal_payments.='<br />Card Holder City => '.$data['cardholderCity'];
		$patient_portal_payments.='<br />Card Holder State => '.$data['cardholderState'];
		$patient_portal_payments.='<br />Card Holder Zip => '.$data['cardholderZip'];
		$patient_portal_payments.='<br />Card Holder Phone Number => '.$data['cardholderPhoneNumber'];
		$patient_portal_payments.='<br />Card Holder Email => '.$data['cardholderEmail'];
		$patient_portal_payments.='<br />Card Type => '.$data['cardType'];
		$patient_portal_payments.='<br />Last 4 Card Digits => '.$data['last4CardNumber'];
		$cardExpirationArr=explode('.',$data['cardExpiration']);
		$cardExpiration=str_replace('T',' ',$cardExpirationArr[0]);
		$patient_portal_payments.='<br />Card Expiration Date => '.$cardExpiration;
		$patient_portal_payments.='<br />Amount => '.$data['amount'];
		$patient_portal_payments.='<br />Processed Amount => '.$data['processedAmount'];
		$patient_portal_payments.='<br />Payment Successful => '.$data['paymentSuccessful'];
		
		$section_task='Payments Received From Portal';
		$patient_id_task=$data['patientExternalId'];
		$operatorid_task=$data['operator'];
		$return_response='';
		$rule_qry_rs = imw_query("SELECT id FROM tm_rules where tm_rule_name='" . $section_task . "' ");
		$rule_row = imw_fetch_assoc($rule_qry_rs);
		$rule_sql = "SELECT id,comment FROM tm_rules_list where rule_id=" . $rule_row['id'] . " and rule_status=0 order by id asc";
		
		$rule_rs = imw_query($rule_sql);
		if($rule_rs && imw_num_rows($rule_rs)>0){
			while ($row = imw_fetch_assoc($rule_rs)) {
				$task_comments=$row['comment'];
				$insert_qry = "INSERT INTO tm_assigned_rules SET 
								section_name='".$section_task."',
								rule_list_id=".((int)$row['id']).",
								status='0',
								changed_value='".$return_response."',
								patient_portal_payments='".$patient_portal_payments."',
								comments='".$task_comments."',
								patientid=".((int)$patient_id_task).",
								operatorid=".((int)$operatorid_task).",
								patient_portal_payment_id=".((int)$last_insert_id)."  ";
				$rs = imw_query($insert_qry);
				$insert_id = imw_insert_id();
			}
		}
		
		/*Add record to prepayment*/
		$default_row=array();
		$qry_rs = imw_query("SELECT portal_def_user,portal_def_facility FROM erp_api_credentials where id=1 ");
		$default_row = imw_fetch_assoc($qry_rs);
		if(count($default_row)>0) {
			switch($data['cardType']) {
				case 'V':
					$credit_card_co='Visa';
					break;
				case 'M':
					$credit_card_co='MC';
					break;
				case 'AX':
				case 'X':
					$credit_card_co='AX';
					break;
				case 'Dis':
				case 'R':
					$credit_card_co='Dis';
					break;
				default:
					$credit_card_co='Other';
					break;
			}
			
			$created_on=explode(' ', $data['created_on']);
			$entered_date=$created_on[0];
			$entered_time=$created_on[1];

			$res_qry = 'insert into patient_pre_payment set patient_id="'.$data['patientExternalId'].'", paid_amount="'.$data['amount'].'",
			facility_id="'.$default_row['portal_def_facility'].'", provider_id="'.$default_row['portal_def_user'].'", credit_card_co="'.$credit_card_co.'", payment_mode="Credit Card",
			cc_no="'.$data['last4CardNumber'].'", cc_exp_date="'.$cardExpiration.'", entered_date="'.$entered_date.'", entered_time="'.$entered_time.'",
			entered_by="'.$data['operator'].'", paid_date="'.$entered_date.'", erp_transaction_id="'.$data['transactionId'].'", erp_patient_portal_payment="1",
			comment="'.$data['patientComments'].'", erp_reference_number="'.$data['referenceNumber'].'",erp_patient_portal_payment_id="'.$last_insert_id.'" ';
			$res_sql = imw_query($res_qry);
		}
		
	}
	
	
	public function is_valid_pt($id) {
        $ret = 0;
        $id = trim($id);
        if (!empty($id)) {
            $sql = "Select id from patient_data where id=$id";
            $rs = imw_query($sql);
            if ($rs && imw_num_rows($rs) == 1) {
                $ret = 1;
            }
        }
        return $ret;
    }
}

