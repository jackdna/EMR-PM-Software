<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

/*
 * File: amd_patient.php
 * Coded in PHP7
 * Purpose: Patient Class for Advanced MD API Calls
 * Access Type: Include
*/
//session_start();
ini_set('max_execution_time', 0);
include_once(dirname(__FILE__).'/amd_patient.php');

class amd_visit extends amd_patient
{
	
	private $defTimeSlot=DEFAULT_TIME_SLOT;
	private $APP_STATUS;
	public function __construct()
	{
		parent::__construct();
		
		$this->APP_STATUS[0]='Scheduled';//MADE
		$this->APP_STATUS[1]='Scheduled';//ARRIVED
		$this->APP_STATUS[2]='Scheduled';//OTHER--
		$this->APP_STATUS[3]='Scheduled';//SEEN--
		$this->APP_STATUS[5]='Scheduled';//MOVED
		$this->APP_STATUS[10]='Canceled';//CANCELLED
		$this->APP_STATUS[11]='Canceled';//DELETED
		$this->APP_STATUS[12]='Scheduled';//NO SHOW
	}
	
	public function field_list($category)
	{
		
		$visit=array( "@visit_uid"		=>	"Visit_UID",
						"@patientid"		=>	"PatientID",
						/*"@columnheading"	=>	"ColumnHeading",*/
						"@visit_type"	=>	"AppointmentType",
                        "@visit_type_id" 	=> "AppointmentTypeID",
						"@visitstartdatetime"	=>	"VisitStartDateTime",
						/*"@appt_start_date"	=>	"VisitDate",
						"@visitstarttime"=>	"VisitStartTime",*/
						"@duration"		=>	"Duration",
						/*"@color"		=>	"Color",
						"@profilecode"	=>	"ProfileCode",
						"@profilename"	=>	"ProfileName",*/
                        "@doctor_id" 	=> 	"ProfileUID",
						"@providername"	=>	"ProviderName",
						"@comments"		=>	"Comments",
						"@apptstatus"	=>	"ApptStatus",
						/*"@arrivetime"	=>	"ArriveTime",
						"@othertime"	=>	"OtherTime",
						"@seentime"		=>	"SeenTime",
						
						"@chargesposted"=>	"ChargesPosted",
						"@confirmmethod"=>	"ConfirmMethod",
						"@confirmedat"	=>	"ConfirmedAt",
						"@confirmedby"	=>	"ConfirmedBy",
						"@episode"		=>	"Episode",*/
						
						"@location_id"	=>	"FacilityCode",
						"@facilityname"	=>	"FacilityName",
						
						"@visitnote"	=>	"VisitNote",
						/*"@insurancebillingorder"=>	"InsuranceBillingOrder",
						"@acceptassignment"		=>	"AcceptAssignment",
						"@forcepaperclaim"		=>	"ForcePaperClaim",*/
						"@createdat"	=>	"CreatedAt",
						"@createdby"	=>	"CreatedBy",
						"@modifiedat"	=>	"ModifiedAt",
						"@modifiedby"	=>	"ModifiedBy",
						
						/*"@reftype"		=>	"RefType",
						"@refreason"	=>	"RefReason",
						"@refcreation"	=>	"RefCreation",
						"@refexpiration"=>	"RefExpiration",
						"@byrefprovcode"=>	"ByRefProvCode",
						"@byrefprovlastname"	=>	"ByRefProvLastName",
						"@byrefprovfirstname"	=>	"ByRefProvFirstName",
						"@byrefprovmiddlename"	=>	"ByRefProvMiddleName",
						"@byrefprovtitle"		=>	"ByRefProvTitle",
						"@byreferringproviderfid"=>	"ByReferringProviderFID",
						"@istelemedicine"		=>"IsTeleMedicine"*/);
			
		if($category=='visit')return $visit;
		elseif($category=='patient')return $patient;
	}
	public function refine_result($arr)
	{
		$arrnew=json_decode($arr, true);
		return $arrnew['PPMDResults']['Results'];
	}
	
	public function getUpdatedVisits()
	{
		/*if(!$_SESSION['result'])
		{*/
		$last_sync=self::get_sync_time();
		if(!$last_sync)$last_sync=($GLOBALS['LOCAL_SERVER'] == "SCC_INDIANA") ? '2019-07-07T00:48:47.617' : '2020-01-01T00:48:47.617';
		$this->parameters = array('ppmdmsg' => array("usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "getUpdatedVisits",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@datechanged"=> $last_sync
													));
		//add requested fields
		$this->parameters['ppmdmsg']['visit']=$this->field_list('visit');
		$this->parameters['ppmdmsg']['patient']=$this->patient_fields;
		$this->parameters['ppmdmsg']['insurance']=$this->insurance_fields;
		//echo"<pre>";print_r($this->parameters);die();
		$result = self::CURL($this->appURL, $this->parameters);
		
		$_SESSION['result']=$result;
		/*}
		else
		{
			$result=$_SESSION['result'];
		}*/
		
		self::print_resp($result);
		$user_data=$this->refine_result($result);
		
		if($user_data['@visitcount']>0)
		{
			//save last syc time
			self::set_sync_time($user_data['@servertime']);
			$this->api_log['message']="------------------------------------------------------------------\n";
			$this->api_log['message'].=date('Y-m-d H:i:s')."\n";
			$this->api_log['message'].="------------------------------------------------------------------\n";
			$this->api_log['message'].=print_r($user_data,true);
			if($user_data['@visitcount']>1000)
			{
				$new_arr=array_chunk($user_data['visitlist']['visit'],400);
				foreach($new_arr as $ids_arr)
				{
					$this->getUpdatedVisits_by_ids($ids_arr);
				}
			}
			else
			{
				if(trim($user_data['@visitcount'])==1)
				{
					$this->book_appointment($user_data['visitlist']['visit']);	
				}
				else
				{
					foreach($user_data['visitlist']['visit'] as $data)
					{
						$this->book_appointment($data);
					}
				}
			}
		
			echo"<pre>";
			print_r($this->api_log['message']);
			echo"</pre><br/>";
			//write log
			$this->write_log($this->api_log['message']);
			unset($this->api_log['message']);
		}
		else
		{
			echo"No data received to Sync!";	
		}
	}
	
	public function getUpdatedVisits_by_ids($ids_arr)
	{
		$ids_arr_new['visit']=$ids_arr;
		$last_sync=self::get_sync_time();
		$this->parameters = array('ppmdmsg' => array("usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "getUpdatedVisits",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@datechanged"=> $last_sync,
													"visitlist"=>$ids_arr_new
													));
									
		//add requested fields
		$this->parameters['ppmdmsg']['visit']=$this->field_list('visit');
		$this->parameters['ppmdmsg']['patient']=$this->patient_fields;
		$this->parameters['ppmdmsg']['insurance']=$this->insurance_fields;
		
		$result = self::CURL($this->appURL, $this->parameters);
		
		self::print_resp($result);
		
		$user_data=$this->refine_result($result);
		if($user_data['@visitcount']>1000)
		{
			foreach($user_data['visitlist']['visit'] as $data)
			{
				$this->book_appointment($data);
			}
		}
	}
	
	public function getDateVisits($dated)
	{
		//if( !$this->webServerURL) die('Not Getting URL');
		//if(!$_SESSION['result'])
		//{
		$this->parameters = array('ppmdmsg' => array("usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "getDateVisits",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@visitdate"=> $dated
													));
		//add requested fields
		$this->parameters['ppmdmsg']['visit']=$this->field_list('visit');
		$this->parameters['ppmdmsg']['patient']=$this->patient_fields;
		$this->parameters['ppmdmsg']['insurance']=$this->insurance_fields;
		
		$result = self::CURL($this->appURL, $this->parameters);
		//$_SESSION['result']=$result;
		//}
		//else
		//{$result=$_SESSION['result'];}
		
		self::print_resp($result);
		$user_data=$this->refine_result($result);
		//save last syc time
		//self::set_sync_time($user_data['@servertime']);
		
		$this->api_log['message']="------------------------------------------------------------------\n";
		$this->api_log['message'].=date('Y-m-d H:i:s')."\n";
		$this->api_log['message'].="------------------------------------------------------------------\n";
		$this->api_log['message'].=print_r($user_data,true);

		if(trim($user_data['@visitcount'])==1)
		{	
			$this->book_appointment($user_data['visitlist']['visit']);	
		}
		else
		{
			foreach($user_data['visitlist']['visit'] as $data)
			{
				$this->book_appointment($data);
			}
		}
				
		echo"<pre>";
		print_r($this->api_log['message']);
		echo"</pre><br/>";
		//write log
		$this->write_log($this->api_log['message']);
		unset($this->api_log['message']);
	}
	
	function book_appointment($postArrReceived)
	{
		$this->api_log['message'].="Saving/Updating external visit id ".$postArrReceived['@visit_uid']." for AMD Pt. ID :".$postArrReceived['@patientid'].".\n";
		$resArr=array();
		$sc_patient_id=$imw_doc_id=$imw_loc_id=$imw_visit_id=0;
		$appt_status=$this->APP_STATUS[$postArrReceived['@apptstatus']];
		//for now we are adding default facility id
		//if(!trim($postArrReceived['@location_id']))$postArrReceived['@location_id']='MSC';
		//get appt date and time from time stamp
		list($postArrReceived['appt_start_date'],$postArrReceived['appt_start_time'])=explode('T',$postArrReceived['@visitstartdatetime']);
		
		//GET PATIENT ID
		if($postArrReceived['@patientid']>0){
			/*$arrRet=$this->get_imw_pat_id($postArrReceived['@patientid']);
			$imw_pat_id=$postArrReceived['@patientid'];
			$imw_pat_name=$arrRet['pat_name'];*/
			$sc_patient_id=$this->manage_patient_info($postArrReceived['patientlist']['patient']);
			if(!$sc_patient_id){
				$this->api_log['message'].="Patient not found. External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
				return false;
			}
		}else{
			$this->api_log['message']="Patient id not found. External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
			return false;
		}
		
		$doctor_id=self::get_provider($postArrReceived['@providername'],$postArrReceived['@doctor_id']);
		if(!$doctor_id)
		{
			$this->api_log['message'].="Advance MD provider not mapped. Cannot proceed. External visit id ".$postArrReceived['@visit_uid']." is skipped."."\n";
			return false;
		}
		//$this->add_operator('scemr');
		$facility_id=self::get_facility($postArrReceived['@location_id']);
		if(!$facility_id)
		{
			$this->api_log['message'].="Facilty mapping not done for ID:".$postArrReceived['@location_id'].". Cannot proceed. External visit id ".$postArrReceived['@visit_uid']." is skipped."."\n";
			return false;
		}
		
		$response=self::get_procedure($postArrReceived['@visit_type']);
		list($procedure_id,$appt_cmnt)=explode('~:~',$response);
		if($procedure_id && $facility_id && $doctor_id){
			
			//checking if existing appointment rescheduling it
			
			$strDateTime = str_replace('T',' ',$postArrReceived['@visitstartdatetime']);
			
			$intDuration = $postArrReceived['@duration'];
			$dtStartDate = $postArrReceived['appt_start_date'];
			$tmStartTime = $postArrReceived['appt_start_time'];
			$tmEndTime = $this->toAddTime($tmStartTime, "00:".$intDuration.":00");
			
			$strApptQry = "SELECT patient_in_waiting_id  AS 'id', patient_id AS 'sa_patient_id', dos AS sa_dos FROM patient_in_waiting_tbl WHERE amd_visit_id = '".$postArrReceived['@visit_uid']."' AND LOWER(patient_status) != 'canceled' ORDER BY patient_in_waiting_id DESC LIMIT 0,1";
			$rsApptData = imw_query($strApptQry);
			if(imw_error())
			{
				$this->api_log['message'] .= "Database error(207): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
				return false;
			}
			if($rsApptData && imw_num_rows($rsApptData) == 1){
				
				$arrApptId = imw_fetch_assoc($rsApptData);
				$intApptId = $arrApptId['id'];
				$sa_patient_id = $arrApptId['sa_patient_id'];
				$sa_dos = $arrApptId['sa_dos'];
				$strModeBackup = '';
				if($sa_patient_id!=$sc_patient_id){
					//mark the appointment as cancelled if different patient received.
					$strModeBackup = $appt_status;
					$appt_status='Canceled';
				}
				$this->api_log['message'].="Appointment found with ID ".$intApptId."\n";
								
				if($appt_status=='Canceled' && $intApptId){
					if($strModeBackup=='')
						$this->api_log['message'] .= "Cancelling Appointment."."\n";
					else
						$this->api_log['message'] .= "Cancelling Existing Appointment. Now Advance MD message received with different patient."."\n";
					
					$sqlCancel = "UPDATE `patient_in_waiting_tbl` SET `patient_status`='Canceled' WHERE `patient_in_waiting_id`='".$intApptId."'";
					
					if(imw_query($sqlCancel)){
						$this->api_log['message'] .= "Appointment Cancelled."."\n";
						//START UPDATE STATUS IN IMW AND ASCEMR
						$sqlCancelStub = "UPDATE `stub_tbl` SET `patient_status`='Canceled' WHERE `iolink_patient_in_waiting_id`='".$intApptId."' AND dos = '".$sa_dos."' AND checked_in_time='' ";
						imw_query($sqlCancelStub);
						include(dirname(__FILE__).'/../../connect_imwemr.php');
						$sqlCancelImwAppt = "UPDATE schedule_appointments SET sa_patient_app_status_id = '18' WHERE iolink_iosync_waiting_id='".$intApptId."' AND sa_app_start_date = '".$sa_dos."' ";
						imw_query($sqlCancelImwAppt);
						imw_close($link_imwemr);
						include(dirname(__FILE__).'/../../common/conDb.php');
						//END UPDATE STATUS IN IMW AND ASCEMR
						if($strModeBackup=='') {return;}
						else {$intApptId=false; $appt_status = $strModeBackup;}
					}
					if(imw_error())
					{
						$this->api_log['message'] .= "Database error(236): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
						return false;
					}
				}
			}else if(imw_num_rows($rsApptData) == 0){
				//nothing to do
			}else if($rsApptData && imw_num_rows($rsApptData) > 1){
				$this->api_log['message'] .= "Error: Mutiple appointment record found with same External ID. External visit id ".$postArrReceived['@visit_uid']." is skipped."."\n";
				return false;
			}
			
			if($facility_id=='' || $facility_id=='0'){
				$this->api_log['message'] .= "Error: Facility mapping error. External visit id ".$postArrReceived['@visit_uid']." is skipped."."\n";
				return false;
			}
			
			/*Physician Details*/
			$phySql = "SELECT `fname`, `mname`, `lname` FROM `users` WHERE `usersId`=".$doctor_id;
			$phySql = imw_query($phySql);
			if(imw_error())
			{
				$this->api_log['message'] .= "Database error(257): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
				return false;
			}
			$phyFname = $phyMname = $phyLname ='';
			if($phySql){
				$phySql = imw_fetch_assoc($phySql);
				$phyFname = $phySql['fname'];
				$phyMname = $phySql['mname'];
				$phyLname = $phySql['lname'];
			}
			
			/*Procedure Data*/
			$sqalProc = "SELECT `name` FROM `procedures` WHERE `procedureId`=".$procedure_id;
			$sqalProc = imw_query($sqalProc);
			if(imw_error())
			{
				$this->api_log['message'] .= "Database error(274): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
				return false;
			}
			$procName = '';
			if($sqalProc){
				$sqalProc = imw_fetch_assoc($sqalProc);
				$procName = $sqalProc['name'];
			}
			
			/*iDoc Facility Id*/
			$sqliDocFacId = "SELECT `fac_idoc_link_id` FROM `facility_tbl` WHERE `fac_id`=".$facility_id;
			$sqliDocFacId = imw_query($sqliDocFacId);
			if(imw_error())
			{
				$this->api_log['message'] .= "Database error(284): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
				return false;
			}
			$iDocFacId = 0;
			if($sqliDocFacId){
				$sqliDocFacId = imw_fetch_assoc($sqliDocFacId);
				$iDocFacId = $sqliDocFacId['fac_idoc_link_id'];
			}
			
			/*Patient_data*/
			$sqliPtData = "SELECT `patient_fname`, patient_mname, patient_lname, date_of_birth, sex, homePhone, workPhone, street1, street2, city, state, zip FROM `patient_data_tbl` WHERE `patient_id`=".$sc_patient_id;
			$sqliPtData = imw_query($sqliPtData);
			if(imw_error())
			{
				$this->api_log['message'] .= "Database error(298): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
				return false;
			}
			$scPtFName = $scPtMName = $scPtLName = $scdate_of_birth =  $scse =  $schomePhone =  $scworkPhone = $scstreet1 =  $scstreet2 = $sccity = $scstate = $sczip = '';
			if($sqliPtData){
				$sqliPtData = imw_fetch_assoc($sqliPtData);
				$scPtFName = $sqliPtData['patient_fname'];
				$scPtMName = $sqliPtData['patient_mname'];
				$scPtLName = $sqliPtData['patient_lname'];
				
				$scdate_of_birth = $sqliPtData['patient_lname'];
				$scsex = $sqliPtData['patient_lname'];
				$schomePhone = $sqliPtData['patient_lname'];
				$scworkPhone = $sqliPtData['patient_lname'];
				$scstreet1 = $sqliPtData['patient_lname'];
				$scstreet2 = $sqliPtData['patient_lname'];
				$sccity = $sqliPtData['patient_lname'];
				$scstate = $sqliPtData['patient_lname'];
				$sczip = $sqliPtData['patient_lname'];
			}
			
			$arrField = array();
			$arrValue = array();
			
			$arrField[] = "dos";
			$arrValue[] = $dtStartDate;

			$arrField[] = "surgery_time";
			$arrValue[] = $tmStartTime;

			$arrField[] = "surgeon_fname";
			$arrValue[] = $phyFname;
			
			$arrField[] = "surgeon_mname";
			$arrValue[] = $phyMname;
			
			$arrField[] = "surgeon_lname";
			$arrValue[] = $phyLname;
			
			if(intval($intApptId) > 0)
			{
				if(strtolower($procName)!='import')
				{
					$arrField[] = "patient_primary_procedure";
					$arrValue[] = $procName;
				}
			}
			else
			{
				$arrField[] = "patient_primary_procedure";
				$arrValue[] = $procName;
			}
			
			$arrField[] = "patient_status";
			$arrValue[] = 'Scheduled';
			
			$arrField[] = "patient_id";
			$arrValue[] = $sc_patient_id;
			
			$arrField[] = "operator_id";
			$arrValue[] = $operator_id;
			
			$arrField[] = "iasc_facility_id";
			$arrValue[] = $iDocFacId;
			
			$arrField[] = "source";
			$arrValue[] = 'AMD';

			// Save AMD VISIT comments
			$AMD_comments = '';
			if($postArrReceived['@comments'] != "") {
				$commentString = str_replace("'", "", $postArrReceived['@comments']);
				$AMD_comments = "\nComment: ".imw_real_escape_string($commentString)." ";
			}
			
			$arrField[] = "comment";
			$arrValue[] = $appt_cmnt.$AMD_comments;

			$arrField[] = "idoc_sch_athena_id";
			$arrValue[] = $postArrReceived['@visit_uid'];
			
			$arrField[] = "amd_visit_id";
			$arrValue[] = $postArrReceived['@visit_uid'];
			
			$arrField[] = "amd_user_id";
			$arrValue[] = $postArrReceived['@doctor_id'];
			
			$arrField[] = "amd_facility_code";
			$arrValue[] = $postArrReceived['@location_id'];
			
			$arrField[] = "amd_respparty";
			$arrValue[] = $postArrReceived['patientlist']['patient']['@respparty'];
			
			$arrField[] = "amd_finclasscode";
			$arrValue[] = $postArrReceived['patientlist']['patient']['@finclasscode'];
			
			
			$jk = 0;
			foreach ($arrField as $strColumn){
				$strQry .= " $strColumn = '".$arrValue[$jk]."', ";	
				$jk++;
			}
			$strQry = substr($strQry,0,-2);	
						
			if(intval($intApptId) > 0){//APPT FOUND, UPDATING....	
				if($appt_status != "Canceled"){
					$strQry = "UPDATE patient_in_waiting_tbl SET ".$strQry;						
					$strQry .= " WHERE 	patient_in_waiting_id = '".$intApptId."'";
					imw_query($strQry);
					if(imw_error())
					{
						$this->api_log['message'] .= "Database error(371): ".imw_error()."\n";
						return false;
					}
					if(imw_error())
					{
						$this->api_log['message'] .= "Database error(375): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
						return false;
					}
					//rescheduling
					$this->api_log['message'] .= "Appointment updated in iASCEMR for Ext. ID:". $postArrReceived['@visit_uid']."\n";
				}			
			}else{
				if($appt_status != "Canceled")
				{
					//adding new appointment
					$strQry = "INSERT INTO patient_in_waiting_tbl SET ".$strQry;
					imw_query($strQry);
					if(imw_error())
					{
						$this->api_log['message'] .= "Database error(386): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
					}
					$intApptId = imw_insert_id();
					$this->api_log['message'] .= "Appointment created for Ext. ID:". $postArrReceived['@visit_uid']."\n";
				}
				return true;
			}
			
			//setting status
			if($appt_status != "Scheduled"){
				switch ($appt_status){
					case "Canceled":		//cancel
						$this->api_log['message'] .= "Cancelling Appointment."."\n";
						$sqlCancelAppt = "UPDATE `patient_in_waiting_tbl` SET `patient_status`='Cancelled' WHERE `patient_in_waiting_id`='".$intApptId."'" ;
						if(imw_query($sqlCancelAppt)) {
							//START UPDATE STATUS IN IMW AND ASCEMR
							if(!$sa_dos) {$sa_dos = $dtStartDate;}
							$sqlCancelApptStub = "UPDATE `stub_tbl` SET `patient_status`='Canceled' WHERE `iolink_patient_in_waiting_id`='".$intApptId."' AND dos = '".$sa_dos."' AND checked_in_time='' ";
							imw_query($sqlCancelApptStub);
							include(dirname(__FILE__).'/../../connect_imwemr.php');
							$sqlCancelApptImwAppt = "UPDATE schedule_appointments SET sa_patient_app_status_id = '18' WHERE iolink_iosync_waiting_id='".$intApptId."' AND sa_app_start_date = '".$sa_dos."' ";
							imw_query($sqlCancelApptImwAppt);
							imw_close($link_imwemr);
							include(dirname(__FILE__).'/../../common/conDb.php');
							//END UPDATE STATUS IN IMW AND ASCEMR
						}
						if(imw_error())
						{
							$this->api_log['message'] .= "Database error(401): ".imw_error().". External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";
							return false;
						}
						$this->api_log['message'] .= 'Appointment Calcelled'."\n";
					break;			
				}
			}
		}
		else
		{
			$this->api_log['message'].="Insufficent data received. Procedure, Facility or Doctor field missing. External visit id ".$postArrReceived['@visit_uid']." is skipped.\n";	
			return false;
		}
		return false;
	}
	//to add minutes to a time
	function toAddTime($tmHHIISS, $strTimeToAdd){
		$strQry = "SELECT ADDTIME('$tmHHIISS', '$strTimeToAdd') as tmNewTime";
		$rsData = imw_query($strQry);
		$arrData = imw_fetch_array($rsData,imw_ASSOC);
		return $arrData['tmNewTime'];
	}
	
	//function to print available field for appointment
	public function getupdatedvisitstemplate()
	{
		$this->parameters = array('ppmdmsg' => array("usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "getupdatedvisitstemplate",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A')
													));
		$result = self::CURL($this->appURL, $this->parameters);
		self::print_resp($result);
	}
	
	public function getupdatedproviderstemplate()
	{
		$this->parameters = array('ppmdmsg' => array("usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "getupdatedproviderstemplate",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A')
													));
									
		//add requested fields
		//$this->parameters['ppmdmsg']['visit']=$this->field_list('visit');
		
		$result = self::CURL($this->appURL, $this->parameters);
		
		self::print_resp($result);
	}
	 
	public function getupdatedproviders()
	{
		$provider= array("@name"=>"Name","@changedat"=> "ChangedAt","@createdat"=> "CreatedAt");
		$profile=array("@id" => "[auto]",
														"@status" => "Status",
														"@profilecode" => "ProfileCode",
														"@license" => "License",
														"@federalid" => "FederalID",
														"@clianumber" => "CLIANumber",
														"@userfilefid" => "UserFileFID",
														"@taxonomy" => "Taxonomy",
														"@npinumber" => "NPINumber",
														"@billasfid" => "BillAsFID",
														"@feeschedulefid" => "FeeScheduleFID",
														"@referringproviderfid" => "ReferringProviderFID",
														"@groupfid" => "GroupFID",
														"@statementgroupfid" => "StatementGroupFID",
														"@description" => "Description",
														"@facilityfid" => "FacilityFID",
														"@xrefidschangedat" => "XRefIDsChangedAt",
														"@address1" => "Address1",
														"@address2" => "Address2",
														"@zipcode" => "ZipCode",
														"@city" => "City",
														"@state" => "State",
														"@areacode" => "AreaCode",
														"@countrycode" => "CountryCode",
														"@officephone" => "OfficePhone",
														"@officeextension" => "OfficeExtension",
														"@fax" => "Fax",
														"@changedat" => "ChangedAt",
														"@changedby" => "ChangedBy",
														"@createuser" => "CreateUser",
														"@createdat" => "CreatedAt",
														"@isdefault" => "IsDefault",
														"@hideinchargeentry" => "HideInChargeEntry",
														"@providername" => "ProviderName",
														"@upinnumber" => "UPINNumber");
		$this->parameters = array('ppmdmsg' => array("usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "getupdatedproviders",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@datechanged"=>"2005-03-01"
													));
									
		//add requested fields
		$this->parameters['ppmdmsg']['provider']=$provider;
		$this->parameters['ppmdmsg']['profile']=$profile;
		$result = self::CURL($this->appURL, $this->parameters);
		
		self::print_resp($result);
	}
}

$visit = new amd_visit();
//$visit->getupdatedproviders();
if($_REQUEST['dated'])
{
	$visit->getDateVisits($_REQUEST['dated']);
	
}
else
{
	//update databse using last sync date	
	$visit->getUpdatedVisits();
}