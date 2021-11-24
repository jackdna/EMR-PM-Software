<?php
include_once(dirname(__FILE__) .'/../../config/globals.php');
include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");

$OBJRabbitmqExchange = new Rabbitmq_exchange();

class Portal_patients
{
    	
	public function getPortalPatients() {
		global $OBJRabbitmqExchange;
        $data_arr = array();

        $resource = 'portalpatients/search?alreadySent=false';
        $method = 'GET';
        $message_id = $_SESSION['authId']."-".time();
		
        if($OBJRabbitmqExchange) {
			$response = $OBJRabbitmqExchange->send_request($data_arr, $message_id, $resource, $method);
		
			$response = json_decode($response, true);
		}
		
		if(is_array($response) && isset($response['rows']) && count($response['rows']) >0 ) {
			$this->log_portal_patients_data($response['rows']);
		}
        
	}
	
	public function log_portal_patients_data($patients=array()) {
		$pt_portal_ids_arr=array();
		if(is_array($patients) && count($patients) >0 ) {
			foreach($patients as $row) {
				$portal_patient_id=trim($row['id']);
				$sql="Select id from erp_iportal_patients_data where pt_portal_id='".$portal_patient_id."' ";
				$QryRes=imw_query($sql);
				if($QryRes && imw_num_rows($QryRes)==0) {
					$data=array();
					$data['pt_portal_id']=$row['id'];
					$data['portalCreated']=$row['portalCreated'];
					$data['contactPrefixExternalId']=$row['contactPrefixExternalId'];
					$data['firstName']=$row['firstName'];
					$data['middleName']=$row['middleName'];
					$data['lastName']=$row['lastName'];
					$data['contactSuffixExternalId']=$row['contactSuffixExternalId'];
					$data['sexExternalId']=$row['sexExternalId'];
					$data['username']=$row['username'];
					$data['birthday']=$row['birthday'];
					$data['address1']=$row['address1'];
					$data['address2']=$row['address2'];
					$data['city']=$row['city'];
					$data['state']=$row['state'];
					$data['zipCode']=$row['zipCode'];
					$data['countryName']=$row['countryName'];
					$data['communicationsVoicePhone']=$row['communicationsVoicePhone'];
					$data['communicationsTextPhone']=$row['communicationsTextPhone'];
					$data['communicationsEmail']=$row['communicationsEmail'];
					$data['created_on']=date('Y-m-d H:i:s');
					$data['operator']=$_SESSION['authId'];
					$data['approved_declined']=0;
					
					AddRecords($data,'erp_iportal_patients_data');
					
					$pt_portal_ids_arr[]=$row['id'];
				}
			}
		}
		
		if(count($pt_portal_ids_arr) >0) {
			$this->send_acknow($pt_portal_ids_arr);
		}
		
	}
	
	
	function send_acknow($pt_portal_ids_arr=array()) {
        if (count($pt_portal_ids_arr) > 0) {
            foreach ($pt_portal_ids_arr as $k => $ppid) {
                if (!empty($ppid)) {
                    $data_arr = array();
                    $data_arr["id"] = $ppid;

                    global $OBJRabbitmqExchange;

					$resource = 'PortalPatientsSent';
					$method = 'POST';
					$message_id = $ppid."-".time();
					
					if($OBJRabbitmqExchange) {
						$OBJRabbitmqExchange->send_request($data_arr, $message_id, $resource, $method);
					}
                }
            }
        }
    }
	
	
	
	function get_pt_reqs_qry() {
        $sql = "SELECT patient_id as pt_id, approved_declined as is_approved, 'erp_iportal_patients_data' as tb_name,
            id, 'Request to add patient' AS title_msg, created_on as reqDateTime,
            DATE_FORMAT(created_on,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime2, pt_portal_id, null as can_reason
            FROM `erp_iportal_patients_data`
            WHERE pt_portal_id!=''
            ";
        return $sql;
    }
	
	
}

