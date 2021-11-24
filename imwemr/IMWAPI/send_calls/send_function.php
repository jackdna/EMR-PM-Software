<?php
$ignoreAuth = true;
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once('send_api.php');
class send_api_call extends sendApiClient{
	function __construct($patient_id,$form_id,$customerId=API_CUSTOMER_ID){
		$this->patient_id = $patient_id;	
		$this->form_id = $form_id;	
		$this->customerId =$customerId;
	}
	private function set_phone_format($phone_no){
		$phone_number="";
		$array_replace=array("(",")","-"," ");
		$phone_no_set=trim(str_replace($array_replace,"",$phone_no));	
		$phone1=substr($phone_no_set,0,3);
		$phone2=substr($phone_no_set,3,3);
		$phone3=substr($phone_no_set,6,4);
		$phone_number=$phone1."-".$phone2."-".$phone3;
		return $phone_number;
	}
	function CREATE_PATIENT(){
		$responsArray=array();
		
		$qry_patient="SELECT id as epm_patient_id,fname as first_name,lname as last_name,
		DOB as 'dob',email,SUBSTRING(sex,1,1) as gender,UPPER(SUBSTRING(status,1,1)) as marital_status,
		phone_home as primary_phone,phone_cell as cell_phone,phone_biz as secondary_phone,street2 as address,city,state,postal_code as zip,
		ethnicity FROM patient_data WHERE id='".$this->patient_id."'";
		$res_patient=imw_query($qry_patient) or die(imw_error());
		$row_patient=imw_fetch_assoc($res_patient);
		$row_patient['customerId']=$this->customerId;
		$row_patient['primary_phone']=$this->set_phone_format($row_patient['primary_phone']);
		$row_patient['cell_phone']=$this->set_phone_format($row_patient['cell_phone']);
		$row_patient['secondary_phone']=$this->set_phone_format($row_patient['secondary_phone']);
			
		$fields=array();
		$fields=$row_patient;
		$response = $this->post('/patients/create_patient',$fields);
		$responseArray=(array)json_decode($response);

		//CHECK AND INSERT ID
		if($responseArray['patientId']>0){
			$qry="UPDATE patient_data SET api_id='".$responseArray['patientId']."' WHERE id='".$this->patient_id."'";
			imw_query($qry);
		}
		
		if($responsArray){
			$responsArray=$responseArray;
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}
	function UPDATE_PATIENT(){
		$responsArray=array();
		
		$qry_patient="SELECT api_id as 'patientId', DOB as 'current_dob',DOB as new_dob, fname as first_name,lname as last_name,
					email,UPPER(SUBSTRING(sex,1,1)) as gender,UPPER(SUBSTRING(status,1,1)) as marital_status,
					phone_home as primary_phone, phone_cell as cell_phone,phone_biz as secondary_phone,street2 as address,city,state,
					postal_code as zip,ethnicity FROM patient_data WHERE id='".$this->patient_id."'";
		$res_patient=imw_query($qry_patient) or die(imw_error());
		$row_patient=imw_fetch_assoc($res_patient);
		$row_patient['customerId']=$this->customerId;
		$row_patient['primary_phone']=$this->set_phone_format($row_patient['primary_phone']);
		$row_patient['cell_phone']=$this->set_phone_format($row_patient['cell_phone']);
		$row_patient['secondary_phone']=$this->set_phone_format($row_patient['secondary_phone']);

		//==========================================//	
		$fields=array();
		$fields=$row_patient;

		$response = $this->post('/patients/update_patient',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			//$this->prp($responseArray);
			$responsArray=$responseArray;
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}
	function GET_TIMES($startDate='', $endDate=''){
		$row_patient  = array(
			'customerId' => $this->customerId
			,'startDate' => $startDate
			,'endDate' => $endDate
			,'locationId' => '0'
			,'visitTypeId' => '0'
			,'doctorId' => '0'
			,'dateOnly'=>false
			,'limit'=>0
			);
		
		$fields=array();
		$fields=$row_patient;
		$response = $this->post('/appointments/get_times',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			$responsArray=$responseArray;
			foreach($responsArray as $data){
				$qry="Select id FROM api_appointments WHERE 
				endTime='".$data['endTime']."'
				AND visitTypeId='".$data['visitTypeId']."'
				AND locationId='".$data['locationId']."'
				AND doctorId='".$data['doctorId']."'";
				$rs=imw_query($qry);
				
				if(imw_num_rows($rs)<=0){
					$qry1="Insert INTO api_appointments SET
					startDate='".$startDate."',
					endDate='".$endDate."',
					startTime='".$data['startTime']."',
					endTime='".$data['endTime']."',
					visitTypeId='".$data['visitTypeId']."',
					locationId='".$data['locationId']."',
					doctorId='".$data['doctorId']."',
					timezone='".$data['timezone']."'";
					imw_query($qry1);
				}
			}
			$rs.close;
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}
	
	function GET_APPOINTMENTS($startDate='', $endDate=''){
		$responsArray=array();
		$fields=array();
		
		$fields  = array(
		'customerId' => $this->customerId
		,'apptStartDate' => $startDate
		,'apptEndDate' => $endDate
		,'modifiedDateStart' => ''//10/07/2015 12:00:00 modify date overrides appointment dates
		,'modifiedDateEnd' => '' //empty means until today
		,'patientId' => '' //lists only one patient
		,'limit'=>0 //default limit is 1000
		,'page'=>0 //page number
		,'sortOrder'=>'' //asc is default or desc
		);

		$response = $this->post('/appointments/get_appointments',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			$responsArray=$responseArray;
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}
			
	function BOOK_APPOINTMENT($row_patient=array()){
		$responsArray=array();
		
		if(sizeof($row_patient)<=0){
			$qry_patient="SELECT 
			sa.id as 'apptId',
			sa.sa_patient_id as 'patientId',
			CONCAT(sa.sa_app_start_date, ' ', sa.sa_app_starttime)  as 'apptStartDate',
			sa.procedureid as 'visitTypeId',
			sa.sa_facility_id as 'locationId',
			sa.sa_doctor_id as 'doctorId',
			pd.DOB as 'patientDob' 
			FROM schedule_appointments sa JOIN patient_data pd ON pd.id= sa.sa_patient_id 
			WHERE sa.sa_patient_id='".$this->patient_id."' ORDER BY sa.id DESC LIMIT 0,1";
			$res_patient=imw_query($qry_patient) or die(imw_error());
			$row_patient=imw_fetch_assoc($res_patient);
			
			$apptId = $row_patient['apptId'];
			$row_patient['customerId']=$this->customerId;
			unset($row_patient['apptId']);
		}

		$fields=array();
		$fields=$row_patient;
		$response = $this->post('/appointments/book_appointment',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			$responsArray=$responseArray;
			//CHECK AND INSERT ID
			if($responsArray['apptId']>0){
				$qry="UPDATE schedule_appointments SET api_id='".$responsArray['apptId']."' WHERE id='".$apptId."'";
				imw_query($qry);
			}
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}	

	function UPDATE_APPOINTMENT($row_patient=array()){
		$responsArray=array();
		
		if(sizeof($row_patient)<=0){
			$apptStatus='';
			
			$qry_patient="SELECT 
			sa.api_id as 'apptId',
			sa.sa_patient_id as 'patientId', sa.sa_patient_app_status_id as 'appt_sts',
			pd.DOB as 'patientDob',  
			FROM schedule_appointments sa JOIN patient_data pd ON pd.id= sa.sa_patient_id 
			WHERE sa.sa_patient_id='".$this->patient_id."' AND sa.id='".$appointment_id."'";
			$res_patient=imw_query($qry_patient) or die(imw_error());
			$row_patient=imw_fetch_assoc($res_patient);
	
			switch($row_patient['appt_sts']){
				case 17:	//Confirm
					$apptStatus='C';	//Confirmed
					break;
				case 18:	//Cancelled
					$apptStatus='X';	//Cancelled
					break;
				case 3:		//No Show	
					$apptStatus='N';	//No show
					break;
				case 201:	//To-Do-Rescheduled
				case 271:	//First Available
					$apptStatus='W';	//Wait Listed
					break;
				case 203:	//Deleted
					$apptStatus='B';	//Bumped/Deleted
					break;
				case 11:	//Checked Out
					$apptStatus='D';	//Completed	
					break;
			}
			$apptId = $row_patient['apptId'];
			$row_patient['customerId']=$this->customerId;
			$row_patient['apptStatus']=$apptStatus;
			
			unset($row_patient['appt_sts']);
		}

		$fields=array();
		$fields=$row_patient;

		$response = $this->post('/appointments/update_appointment',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			$responsArray=$responseArray;
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}	
	
	function CANCEL_APPOINTMENT($row_patient=array(), $appointment_id=0){
		$responsArray=array();
		
		if(sizeof($row_patient)<=0){
			if($appointment_id>0){
				
				$qry_patient="SELECT 
				sa.api_id as 'apptId',
				sa.sa_patient_id as 'patientId', pd.DOB as 'patientDob' 
				FROM schedule_appointments sa JOIN patient_data pd ON pd.id= sa.sa_patient_id 
				WHERE sa.sa_patient_id='".$this->patient_id."' AND sa.id='".$appointment_id."'";
				$res_patient=imw_query($qry_patient) or die(imw_error());
				$row_patient=imw_fetch_assoc($res_patient);
		
				$row_patient['customerId']=$this->customerId;
				$row_patient['apptStatus']='X';
			}
		}


		$fields=array();
		$fields=$row_patient;
	
		$response = $this->post('/appointments/cancel_appointment',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			$responsArray=$responseArray;
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}
		
		
	function GET_DOCTORS(){
		$responsArray=array();
		
		$fields  = array(
			'customerId' => $this->customerId
			);

		$fields=array();
		$fields=$row_patient;
		$response = $this->post('/support/get_doctors',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			$responsArray=$responseArray;
			//CHECK AND INSERT ID
			foreach($responsArray as $data){
				list($fname, $mname, $lname)=explode(' ', $data['doctorName']);
				if($fname!='' && $lname!=''){
					$qry="UPDATE users SET api_id='".$data['doctorId']."' 
					WHERE fname='".$fname."' AND mname='".$mname."' AND lname='".$lname."' AND delete_status='0'";
					imw_query($qry);
				}
			}
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}	
	function GET_LOCATIONS(){
		$responsArray=array();
		
		$fields  = array(
			'customerId' => $this->customerId
			);

		$fields=array();
		$fields=$row_patient;
		$response = $this->post('/support/get_locations',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			$responsArray=$responseArray;
			//CHECK AND INSERT ID
			foreach($responsArray as $data){
				if($data['locationName']!=''){
					$qry="UPDATE facility SET api_id='".$data['locationId']."' WHERE name='".$data['locationName']."'";
					imw_query($qry);
				}
			}
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}
	function GET_TYPES(){
		$responsArray=array();
		
		$fields  = array(
			'customerId' => $this->customerId
			);

		$fields=array();
		$fields=$row_patient;
		$response = $this->post('/support/get_types',$fields);
		$responseArray=(array)json_decode($response);
		if($responseArray){
			$responsArray=$responseArray;
			//CHECK AND INSERT ID
			foreach($responsArray as $data){
				if($data['locationName']!=''){
					$qry="UPDATE slot_procedures SET api_id='".$data['visitTypeId']."' WHERE proc='".$data['visitType']."' AND LOWER(active_status)='yes'";
					imw_query($qry);
				}
			}
		}else{
			$responsArray=$response;
		}
		return $responsArray;
	}			
}

//$patient_id=413605;
$form_id=0;

$SEND_API_CALL_OBJ=new send_api_call($patient_id,$form_id);

$result=$SEND_API_CALL_OBJ->GET_DOCTORS();
//$result=$SEND_API_CALL_OBJ->GET_LOCATIONS();
//$result=$SEND_API_CALL_OBJ->GET_TYPES();
//$result=$SEND_API_CALL_OBJ->CREATE_PATIENT();
//$result=$SEND_API_CALL_OBJ->UPDATE_PATIENT();
//$result=$SEND_API_CALL_OBJ->GET_TIMES('2015-01-20', '2016-12-31');
//$result=$SEND_API_CALL_OBJ->GET_APPOINTMENTS('2015-01-20', '2016-12-31');

pre($result);
?>