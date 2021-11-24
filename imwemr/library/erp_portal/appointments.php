<?php
set_time_limit(0);
include_once(dirname(__FILE__) . "/../../library/erp_portal/erp_portal_core.php");

class Appointments extends ERP_portal_core
{

    public function __construct()
    {
        parent::__construct();
    }

    /* Schedule Statuses add/update function starts here
     * Appointment Statuses mapped with imwemr Portal
     *                                          Tag which needs to be true here
        No Show                 Cancelled 	iscanceled
        Insurance/Financial Issue               Blank
        Checked Out             Checkout 	ischeckedout
        Check-in                Checkin 	ischeckin
        Confirm                 Confirmed 	isconfirmed
        cancelled               Cancelled 	iscanceled
        Reschedule              Reschedule 	isrescheduled
        To-Do-Reschedule                        Blank
        Deleted                                 Blank
        First Available                         Blank
     */

    public function addUpdateAppointmentStatuses($id='') {
		$erp_error=array();
        $q_c=" SELECT * from schedule_status WHERE id=$id ";
        $r_c=imw_query($q_c);
        if($r_c && imw_num_rows($r_c)==1){
            $row=imw_fetch_assoc($r_c);
            if($row['status']==1) {
				try {
					$apptArr=array();

					$isCanceled=($row['status_name']=='No Show' || $row['status_name']=='cancelled')?true:false;
					$isRescheduled=($row['status_name']=='Reschedule')?true:false;
					$isConfirmed=($row['status_name']=='Confirm')?true:false;
					$isCheckedOut=($row['status_name']=='Checked Out')?true:false;
					$isCheckIn=($row['status_name']=='Check-in')?true:false;

					$apptArr['name']=$row['status_name'];
					$apptArr['isCanceled']=$isCanceled;
					$apptArr['isRescheduled']=$isRescheduled;
					$apptArr['isConfirmed']=$isConfirmed;
					$apptArr['isCheckedOut']=$isCheckedOut;
					$apptArr['isCheckIn']=$isCheckIn;
					$apptArr['id']=$row['erp_sch_status_id'];
					$apptArr['externalId']=$id;

					$result = $this->CURL($apptArr, 'api/AppointmentStatuses', 'POST');
					if($result) {
						$qry="Update schedule_status set erp_sch_status_id='".$result['id']."' Where id=$id ";
						imw_query($qry);
					}
					
				} catch(Exception $e) {
					$erp_error[]='Unable to connect to ERP Portal';
				}
            }
        }

    }
    /*Add / Update Schedule statuses function on imwemr Portal ends here*/


    /* DELETE api/AppointmentStatuses?externalId={externalId} */
    public function deleteAppointmentStatuses($id='') {
		$erp_error=array();
		try {
			$externalId=$id;

			$params=array();
			$result = $this->CURL($params, 'api/AppointmentStatuses?externalId='.$externalId, 'DELETE');

			return $result;
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
    }


    /* Add/Update Slot Procedures (appointment Request Reasons) starts Here*/
    /**
     * {
        "internalId": "0e8bbf60-5f6a-49d5-9080-2d7ce811bf62",
        "accountId": "ac6057f6-3398-4e38-bae8-8584fd599e44",
        "name": "sample string 2",
        "appliesToNewPatients": true,
        "appliesToExistentPatients": true,
        "isLogicalDeleted": true,
        "slotDuration": 5,
        "changed": true,
        "active": true,
        "lastChanged": "sample string 10"
      }
     *
     */
    public function addUpdateAppointmentRequestReasons($proc_id=0) {
		$erp_error=array();
				$time_arr=array();
        $timesql="select id,times from slot_procedures where times!='' and proc='' ";
        $time_rs=imw_query($timesql);
        if($time_rs && imw_num_rows($time_rs)>0){
            while($row=imw_fetch_assoc($time_rs) ) {
                $time_arr[$row['id']]=$row['times'];
            }
        }

        $q_c=" SELECT id,proc,times,proc_time,active_status,erp_appt_reason_id,erp_applies_to_new_patient,erp_applies_to_ext_patient
                from slot_procedures WHERE id=$proc_id ";
        $r_c=imw_query($q_c);
        if($r_c && imw_num_rows($r_c)==1){
			try {
				$row=imw_fetch_assoc($r_c);

				$appliesToNewPatients=($row['erp_applies_to_new_patient']==1 || $row['erp_appt_reason_id']=='')?true:false;
				$appliesToExistentPatients=($row['erp_applies_to_ext_patient']==1 || $row['erp_appt_reason_id']=='')?true:false;
				$active_status=($row['active_status']=='yes')?true:false;

				$apptReasonArr=array();
				$apptReasonArr['internalId']=$row['erp_appt_reason_id'];
				$apptReasonArr['accountId']=$this->account_id;
				$apptReasonArr['name']=$row['proc'];
				$apptReasonArr['appliesToNewPatients']=$appliesToNewPatients;
				$apptReasonArr['appliesToExistentPatients']=$appliesToExistentPatients;
				$apptReasonArr['isLogicalDeleted']="";
				$apptReasonArr['slotDuration']=$time_arr[$row['proc_time']];
				$apptReasonArr['changed']="";
				$apptReasonArr['active']=$active_status;
				$apptReasonArr['lastChanged']="";
							
				$result = $this->CURL($apptReasonArr, 'api/AppointmentRequestReasons', 'POST');
				if($result) {
					$qry="Update slot_procedures set erp_appt_reason_id='".$result['internalId']."',
												erp_applies_to_new_patient='".$result['appliesToNewPatients']."',
							erp_applies_to_ext_patient='".$result['appliesToExistentPatients']."',
							Where id=$proc_id ";
					imw_query($qry);
				}
			
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
        }

    }
    /* Add/Update Slot Procedures (appointment Request Reasons) ends Here*/



    /** Create a new appointment on portal from IMW.
    {
        "locationExternalId": "sample string 1",
        "doctorExternalId": "sample string 2",
        "patientExternalId": "sample string 3",
        "patientPracticeManagementSystemId": "sample string 4",
        "appointmentStatusExternalId": "sample string 5",
        "appointmentTypeExternalId": "sample string 6",
        "cancelReason": "sample string 7",
        "start": "2020-04-07T17:23:15.1844782-04:00",
        "end": "2020-04-07T17:23:15.1844782-04:00",
        "id": "d7dc0c1d-e6d2-4370-a8de-cdf247eadfc0",
        "externalId": "sample string 11"
    }
    */
    public function addUpdateAppointments($appt_id=0,$patient_id=0,$ap_act_reason='') {
        $q_c=" SELECT id,sa_doctor_id,sa_patient_id,sa_patient_app_status_id,erp_appt_id,
                sa_app_start_date,sa_app_end_date,sa_app_starttime,sa_app_endtime,sa_facility_id
                from schedule_appointments
                WHERE id=$appt_id";

        $r_c=imw_query($q_c);
        if($r_c && imw_num_rows($r_c)==1){
            $row=imw_fetch_assoc($r_c);

            $start_datetime=$row['sa_app_start_date']."T".$row['sa_app_starttime'];
            $end_datetime=$row['sa_app_end_date']."T".$row['sa_app_endtime'];
            if($row['sa_patient_app_status_id']==0) $row['sa_patient_app_status_id']="";

			$patient_id=$row['sa_patient_id'];

            $apptArr=array();
            $apptArr['locationExternalId']=$row['sa_facility_id'];
            $apptArr['doctorExternalId']=$row['sa_doctor_id'];
            $apptArr['patientExternalId']=$row['sa_patient_id'];
            $apptArr['patientPracticeManagementSystemId']=$row['sa_patient_id'];
            $apptArr['appointmentStatusExternalId']=$row['sa_patient_app_status_id'];
            $apptArr['appointmentTypeExternalId']="";
            $apptArr['cancelReason']=$ap_act_reason;
            $apptArr['start']=$start_datetime;
            $apptArr['end']=$end_datetime;
            $apptArr['id']=$row['erp_appt_id']; // ="9644b58e-20b4-4f84-aa76-70e8aaaea7e8";
            $apptArr['externalId']=$appt_id;

            if(count($apptArr)>0){
				try {
					include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");

					$OBJRabbitmqExchange = new Rabbitmq_exchange;
					 //API Resource
					$resource='Appointments';
					$method='POST';
					/*Rabbit MQ call to create Appointment at Portal*/
					$response=$OBJRabbitmqExchange->send_request($apptArr,$appt_id,$resource,$method);

					$response=json_decode($response, true);

					if(count($response) > 0 && $response['externalId']==$appt_id) {
						$update_sql="Update schedule_appointments set erp_appt_id='".$response['id']."' WHERE id=$appt_id and sa_patient_id=$patient_id ";
						imw_query($update_sql);
					}
					
				} catch(Exception $e) {
					$erp_error[]='Unable to connect to ERP Portal';
				}
            }

        }
    }
    /* Create appointment ends here*/


	/* Delete an appointment from portal starts here*/
	/* This is not working as we are not getting any response from API endpoint*/
    public function deleteAppointments($appt_id=0) {
		if($appt_id>0){
			try {
				include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");

				$OBJRabbitmqExchange = new Rabbitmq_exchange;
				 //API Resource
				$resource='Appointments?externalId='.$appt_id;
				$method='DELETE';
				$apptArr=array();
				/*Rabbit MQ call to create Appointment at Portal*/
				$response=$OBJRabbitmqExchange->send_request($apptArr,$appt_id,$resource,$method);
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
		}
	}
	/* Delete an appointment from portal ends here*/



    public function addUpdateAppointmentCancelStatuses($patient_arr,$id='',$resource,$method='POST') {
		$row_arr=$erp_error=array();
		$msg = 'No cancel request found from Portal';
		try {
			include($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
			$OBJRabbitmqExchange = new Rabbitmq_exchange();
			$response = $OBJRabbitmqExchange->send_request($patient_arr,$id,$resource,$method);

			if($method == 'POST') {
				return $response;
			}
			$response_decode = json_decode($response, true);
			$row_arr = $response_decode['rows'];
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
		//start sample data
		/*
		$response = '{
					  "totalItems": 0,
					  "itemsPerPage": 10,
					  "currentPage": 1,
					  "totalPages": 0,
					  "firstRowOnPage": 1,
					  "lastRowOnPage": 10,
					  "rows": [
						{
						  "appointmentCancelRequestID": "43e50fe3-1e73-4f19-9176-b4ef21579905",
						  "cancelReason": "This is test cancel patient1",
						  "appointmentExternalID": "116364"
						},
						{
						  "appointmentCancelRequestID": "53e50fe3-1e73-4f19-9176-b4ef21579905",
						  "cancelReason": "This is test cancel patient2",
						  "appointmentExternalID": "116365"
						}
					  ]
					}';
		*/
		//end sample data
		if(!$response) {
			$msg = 'Please check if Rabbit MQ is working';
		}
		
		
		$imw_appt_id_comma = '';
		if(count($row_arr)>0) {
			$imw_appt_id_arr = $row_sa_arr = array();
			foreach($row_arr as $pt_arr) {
				$imw_appt_id_arr[] = $pt_arr['appointmentExternalID'];
			}
			if(count($imw_appt_id_arr)>0) {
				$imw_appt_id_comma = implode(',',$imw_appt_id_arr);
				if(!$imw_appt_id_comma) {
					$imw_appt_id_comma = '0';
				}
				if($imw_appt_id_comma) {
					$qry_sa = "SELECT id,sa_patient_id,sa_patient_app_status_id FROM schedule_appointments WHERE id IN($imw_appt_id_comma)";
					$res_sa=imw_query($qry_sa);
					if(imw_num_rows($res_sa)>0) {
						while($row_sa = imw_fetch_assoc($res_sa)) {
							$sa_id = $row_sa['id'];
							$row_sa_arr[$sa_id] = $row_sa;
						}
					}
				}
			}

			//START GET ALREADY EXISTS RECORD
			$app_id_arr = array();
			if($imw_appt_id_comma) {
				$qry_ia = "SELECT id,app_ext_id, app_can_req_id FROM iportal_app_reqs WHERE app_ext_id IN($imw_appt_id_comma) AND app_can_req_id != '' ";
				$res_ia=imw_query($qry_ia);
				if(imw_num_rows($res_ia)>0) {
					while($row_ia = imw_fetch_assoc($res_ia)) {
						$app_id_arr[$row_ia['app_can_req_id']] = $row_ia['app_ext_id'];
					}
				}
			}
			//END GET ALREADY EXISTS RECORD

			$pt_received_arr = array();
			if($imw_appt_id_comma) {
				foreach($row_arr as $row) {
					$imw_appt_id 				= $row['appointmentExternalID'];
					$imw_pt_id 					= $row_sa_arr[$imw_appt_id]['sa_patient_id'];
					$portal_sa_status_reason	= $row['cancelReason'];
					$portal_sa_cancel_request_id= $row['appointmentCancelRequestID'];
					if($imw_appt_id != $app_id_arr[$portal_sa_cancel_request_id] && $imw_pt_id) {//IF RECORD NOT EXISTS THEN ADD
						$qry = "INSERT INTO iportal_app_reqs SET
								app_ext_id = '".$imw_appt_id."', app_can_req_id = '".$portal_sa_cancel_request_id."',
								can_reason = '".imw_real_escape_string($portal_sa_status_reason)."', patient_id = '".$imw_pt_id."', created_on = '".date("Y-m-d H:i:s")."' ";
						$res=imw_query($qry) or die(imw_error().$qry);
						$msg = 'Cancel request load successfully from Portal';
						//file_put_contents('test.php',$qry."\n"."********************"."\n", FILE_APPEND);
						if($res) {
							//START CODE TO SEND ACKNOWLEDGE AS APPOINTMENT CENCEL REQUEST IS RECEIVED
							$appt_received_arr=array();
							$appt_received_arr['internalID']	= $portal_sa_cancel_request_id;
							$appt_received_arr['success']		= true;
							$appt_received_arr['resultMessage']	= 'Received';
							$resource_received					= 'AppointmentCancelRequestsSent';
							$method_received					= 'POST';
							$responsePost  = 'success ';
							$responsePost .= $OBJRabbitmqExchange->send_request($appt_received_arr,$imw_pt_id,$resource_received,$method_received);
							//END CODE TO SEND ACKNOWLEDGE AS APPOINTMENT CENCEL REQUEST IS RECEIVED
						}
					}
				}
			}
		}


		return $msg;
	}
}
