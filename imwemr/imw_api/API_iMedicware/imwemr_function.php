<?php
require_once('imwemr_api.php');

class iMedicWare_api_call extends iMedicApiClient{
//CUSTOMER ID- 5271

	function GET_DOCTORS(){
		$responseArray=array();
		$fields=array();
		$response = $this->post('get_doctors',$fields);
		$responseArray=json_decode($response, true);
		return $responseArray;
	}	
	function GET_LOCATIONS(){
		$responseArray=array();
		$fields=array();
		$response = $this->post('get_locations',$fields);
		$responseArray=json_decode($response, true);
		return $responseArray;
	}
	function GET_APPOINTMENT_TYPE(){
		$responseArray=array();
		$fields=array();
		$response = $this->post('get_appointment_types',$fields);
		$responseArray=json_decode($response, true);
		return $responseArray;
	}

	function GET_MARKETING_SOURCE(){
		$responseArray=array();
		$fields=array();
		$response = $this->post('get_marketing_source',$fields);
		$responseArray=json_decode($response, true);
		return $responseArray;
	}
		
	function GET_PATIENT_DETAILS(){
		$responseArray=array();
		$fields=array();
		$fields = array(
				'patient_id' => '6024'
				,'first_name' => 'Test'
				,'last_name' => 'Doe'
				,'dob' => '1975-05-08'
				,'gender'=>'male'
				,'zip'=>'01002hngh'
				);
		$response = $this->post('get_patient_details',$fields);
		$responseArray=json_decode($response, true);
		return $responseArray;
	}

	function CREATE_PATIENT(){
		$responseArray=array();
		$fields=array();
		$fields = array(
				'md_patient_id' => 20359
				,'first_name' => 'Test'
				,'middle_name' => 'Mos' //optional
				,'last_name' => 'Doe'
				,'dob' => '1975-05-08'
				,'gender'=>'male'
				,'zip'=>'01002hngh'
				,'phone'=>'9855460844'
				,'email'=>'ab@gmail.com'
				,'address'=>'Street 1'
				,'marketing_source'=>''
				);
		$response = $this->post('create_patient',$fields);
		$responseArray=json_decode($response, true);
		return $responseArray;
	}	

	function GET_APPOINTMENTS(){
		$responseArray=array();
		$fields=array();

		//FROM BELOW LIST ANY ONE PARAMETER MUST PASS TO GET RESULT
		// -PATIENT ID
		// -DOCTOR ID
		// -LOCATION ID
		// -START DATE & END DATE
		// -MODIFIED START DATE & MODIFED END DATE 

		$fields = array(
				'patient_id' => '',	
				'doctor_id' => '',	
				'location' => '',	
				'start_date' => '2017-05-01',	
				'end_date' => '2017-05-22',		
				'modified_date_start' => '',	
				'modified_date_end' => '',		
				'modified_time_start' => '13:00',	//(H:i - 24 HOURS FORMAT)
				'modified_time_end' => '16:30',		//(H:i - 24 HOURS FORMAT)
				'sort_order' => 'asc',	// (asc/desc)
				'limit' => '0',			// (Default-1000)
				'page_number' => 0
				);

		$response = $this->post('get_appointments',$fields);
		$responseArray=json_decode($response, true);

		return $responseArray;
	}

	function GET_DOCTORS_AVAILABILITY(){
		$responseArray=array();
		$fields=array();

		$fields = array(
				'location_id' => '110',
				'schedule_date' => '2013-01-11'
				);

		$response = $this->post('get_doctors_availability',$fields);
		$responseArray=json_decode($response, true);

		return $responseArray;
	}
	
	function GET_AVAILABLE_TIMES(){
		$responseArray=array();
		$fields=array();

		$fields = array(
				'doctor_id' => '',   //optional
				'location_id' => '1',
				'schedule_from_date' => '2017-04-01',
				'schedule_to_date' => '2017-05-30'
				);

		$response = $this->post('get_available_times',$fields);
		$responseArray=json_decode($response, true);

		return $responseArray;
	}	
	

	function GET_APPOINTMENTS_INFO(){
		$responseArray=array();
		$fields=array();

		$fields = array(
				'schedule_from_date' => '2017-04-01',
				'schedule_to_date' => '2017-05-30',
				'procedures'=>'' //Values=empty and all. Empty refer to only global set procedures. 'all' refer to any procedure.
				);

		$response = $this->post('get_appointments_info',$fields);
		$responseArray=json_decode($response, true);

		return $responseArray;
	}	

	function GET_STATEMENT(){
		$responseArray=array();
		$fields=array();

		$fields = array(
				'patient_id' => '12564' //PATIENT ID 
				);

		$response = $this->post('get_statement',$fields);
		$responseArray=json_decode($response, true);

		return $responseArray;
	}	

	function BOOK_APPOINTMENT(){
		$responseArray=array();
		$fields=array();

		$fields = array(
				'patient_id' => '6024',
				'appt_start_date' => '2013-01-11',
				'appt_start_time' => '10:15',	
				'visit_type_id' => '76',
				'location_id' => '110',
				'doctor_id' => '25'
				);

		$response = $this->post('book_appointment',$fields);
		$responseArray=json_decode($response, true);

		return $responseArray;
	}

	function UPDATE_APPOINTMENT(){
		$responseArray=array();
		$fields=array();

		$fields = array(
			'patient_id' => '6024',	//required
			'imw_appt_id' => '25',	//either imw_appt_id or appt_start_date		
			'appt_date' => '2013-01-11', 	//either appt_start_date or imw_appt_id
			'appt_status' => 'D',	 //required
			'appt_comment' => 'Status changed by API'	 //required
			);

		$response = $this->post('update_appointment',$fields);
		$responseArray=json_decode($response, true);
		return $responseArray;
	}


}


$IMEDIC_API_CALL_OBJ=new iMedicWare_api_call($customerId);
$main_return=$IMEDIC_API_CALL_OBJ->GET_APPOINTMENTS();

echo "<pre>";print_r($main_return);
?>