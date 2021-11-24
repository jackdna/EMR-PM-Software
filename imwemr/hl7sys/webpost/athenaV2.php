<?php
//class to manipulate and save values captured in attributes
class athena{
	public $query_string;
	private $response;
	public $obj_hl7;
	private $hl7_method;
	private $resp_log_mode;
	private $execution_status;
	private $patient_id;
	private $appointment_id;
	private $ins_comp_id;
	private $ins_case_id;
	private $doctor_id;
	private $facility_id;
	private $procedure_id;
	private $appt_id;
	private $operator_username;
	private $operator_id;
	private $this_message_id;
	
	function __construct(){
		global $query_string;
		$this->attributes = $query_string;
		$this->hl7_method = "";
		$this->resp_log_mode = "NORMAL";
		$this->execution_status = true;
		$this->patient_id = 0;
		$this->appointment_id = 0;
		$this->ins_comp_id = 0;
		$this->ins_case_id = 0;
		$this->doctor_id = 0;
		$this->facility_id = 0;
		$this->procedure_id = 0;
		$this->appt_id = 0;
		$this->operator_username = "";
		$this->operator_id = 0;
		$this->this_message_id = false;
	}

	/*
	Function: getSegmentByName
	Purpose: getting index number of line of particular segment by name
	Update On: 04th Feb 2012
	*/
	function getSegmentByName($name) 
    {
		for($i=0;$i<count($this->obj_hl7->_segments);$i++){
			$currSeg = '';
			if($this->obj_hl7->_segments[$i]){
				$currSeg = $this->obj_hl7->getSegmentFieldAsString($i,0);
				if(empty($currSeg)===false){
					if($currSeg==$name){
						return $i;
					}//else{var_dump($currSeg);echo 'goog'.$name.'::'.$i.'<br>';}
				}//else{echo '<hr>';var_dump($currSeg);}
			}
		}
		return NULL;
	}
	
	/*
	Function: authenticate
	Purpose: authenticating the request script
	Update On: 25th May 2011
	*/
	function authenticate(){
		return (isset($this->attributes['uname']) && $this->attributes['uname'] == ATHENA_USERNAME && isset($this->attributes['upass']) && $this->attributes['upass'] == ATHENA_PASSWORD) ? true : false;
	}
	
	/*
	Function: router
	Purpose: routing to appropriate function according to the method recieved in the post
	Update On: 25th May 2011
	*/
	function router($strEncryptedData, $intEncoded,$boolLog=true){
		if($this->authenticate() === true){
			$this->response = XML_HEADER."<authentication>success.</authentication>";
				
			//parsing method details
			if($intEncoded == 1){
				$strEncryptedData = urldecode($strEncryptedData);
			}
				
			//decrypting method	
			$this->obj_hl7 = new Net_HL7_Message($strEncryptedData);				
			$MSHIndex = $this->getSegmentByName('MSH');
			$this->hl7_method = $this->obj_hl7->getSegmentFieldAsString($MSHIndex,9);	//0 - messege header, 9 - method	die

			//make database log
			if($boolLog){
				$this->database_log();
			}
			//inserting data
			switch($this->hl7_method){
				case "ADT^A28":		//message type 1
					$this->response .= "<method>registering patient...</method>";
					$this->response .= $this->new_patient_registration();
					break;
				case "ADT^A31":		//message type 2 and 3
					$this->response .= "<method>updating patient record (insurance and address)...</method>";
					$this->response .= $this->update_existing_patient();
					break;
				case "SIU^S12":		//message type 4 and 5
					$this->response .= "<method>scheduling or rescheduling an appointment...</method>";
					$this->response .= $this->new_appointment("addorupdate");
					break;
				case "SIU^S15":		//message type 6
					$this->response .= "<method>cancelling an appointment...</method>";
					$this->response .= $this->new_appointment("cancel");
					break;
				case "SIU^S14":		//message type 7 and 8
					$this->response .= "<method>checking in or checking out for an appointment...</method>";
					$this->response .= $this->new_appointment("check");
					break;
				default:
					$this->response .= "<error>Invalid Method.</error>";
					break;
			}
			$this->response .= XML_FOOTER;
		}else{
			$this->response = XML_HEADER."<authentication>failed.</authentication>".XML_FOOTER;
		}
		if(RESPONSE_LOG == 1){
			$response_file = fopen(RESPONSE_LOG_PATH."RESPONSE_".date('d-m-Y H i s')."-".rand().".xml","a+");
			fwrite($response_file, $this->response);
			fclose($response_file);

			if($this->resp_log_mode == "ALERT"){
				$resp_alert_file = fopen(RESPONSE_LOG_PATH."ALERT_".date('d-m-Y H i s')."-".rand().".xml","a+");
				fwrite($resp_alert_file, $this->response);
				fclose($resp_alert_file);
			}
		}
		$this->send_response();
	}

	/*
	Function: database_log
	Purpose: Logging posts in the database, if set also in filesystem
	Update On: 25th May 2011
	*/
	function database_log(){
		//inserting data
		$strQry = "INSERT INTO tblathenaposts (fldPostId, fldPostMessage, fldDateTimeCreated, fldMethod, fldUsernamePassword) VALUES ('', '".addslashes(urldecode($this->attributes['data']))."', '".date("Y-m-d H:i:s")."', '".$this->hl7_method."', '".$this->attributes['uname']."/".$this->attributes['upass']."')";
		$blInsert = imw_query($strQry);
		$this->this_message_id = imw_insert_id();
		if($blInsert === false){
			$this->error_logs();
		}
		//writing post in a file
		if(POSTS_LOG == 1){
			$posts_file = fopen(ATHENA_POSTS_PATH."DO_NOT_DELETE_Athena_Posts_".date('dmY').".inc","a+");
			fwrite($posts_file, date("d-m-Y H:i:s")."\t".$strQry."\n");
			fclose($posts_file);
		}
	}
	
	/*
	Function: error_logs
	Purpose: Logging sql errors, if any encountered while execution
	Update On: 25th May 2011
	*/
	function error_logs(){
		if(ERROR_LOG == 1){
			$err_file = fopen(ERROR_LOG_PATH."ATHENA_ERR_".date('dmY').".inc","a+");
			fwrite($err_file, date("d-m-Y H:i:s")."\t".imw_errno().": ".imw_error()."\n");
			fclose($err_file);
		}
	}

	/*
	Function: send_response
	Purpose: send final response
	Update On: 25th May 2011
	*/
	function send_response(){
		if($this->this_message_id){
			imw_query("UPDATE tblathenaposts SET response = '".addslashes($this->response)."' WHERE fldPostId='".$this->this_message_id."'");
		}
		echo $this->response;
	}
	
	/*
	Function: debug_code
	Purpose: to print select segments, useful in debugging
	Update On: 25th May 2011
	*/
	function debug_code(){
		if(DEBUG_MODE == 1){
			for($i = 0; $i < count($this->obj_hl7->_segments); $i++){
				echo $i." ".$this->obj_hl7->getSegmentAsString($i)."<br>";
				for($j = 1; $j < count($this->obj_hl7->_segments[$i]->_fields); $j++){
					echo "&nbsp;&nbsp;".($j)."&nbsp;&nbsp;".$this->obj_hl7->getSegmentFieldAsString($i,$j)."<br>";
				}
			}
		}
	}

	/*
	Function: add_single_quotes_to_val
	Purpose: to add single quotes to val, useful in insert/update queries
	Update On: 25th May 2011
	*/
	function add_single_quotes_to_val($arr_val){
		$arr_new_val = $arr_val;
		if(is_array($arr_val) && count($arr_val) > 0){
			foreach($arr_val as $this_val){
				$arr_new_val[] = "'".$this_val."'";
			}
		}
		return $arr_new_val;
	}
	
	/*
	Function: new_patient_registration
	Purpose: to register new patient into the system
	Update On: 25th May 2011
	*/
	function new_patient_registration(){
		$this->debug_code();
		$this->add_update_patient();
		$this->add_update_pt_resp_party();
		$this->add_update_patient_ins();
	}

	/*
	Function: update_existing_patient
	Purpose: to update details of an existing patient into the system
	Update On: 25th May 2011
	*/
	function update_existing_patient(){
		$this->debug_code();
		$this->add_update_patient();
		$this->add_update_pt_resp_party();
		$this->add_update_patient_ins();
	}
	
	/*
	Function: add_update_patient
	Purpose: to add / update patient records based on athenaID = recieved id
	Update On: 25th May 2011
	*/
	function add_update_patient(){
		//getting athena id
		$PIDIndex = $this->getSegmentByName('PID');
		$athena_id = $this->obj_hl7->getSegmentFieldAsString($PIDIndex, 2);
		$this->patient_id = $athena_id;

		//setting column data
		$arrFields = $arrValues = array();
		
		$arrFields[] = "id";					$arrValues[] = $athena_id;
		$arrFields[] = "pid";					$arrValues[] = $athena_id;	

		//getting first name and last name
		$arrTempName = explode("^",$this->obj_hl7->getSegmentFieldAsString($PIDIndex,5));
		
		$arrFields[] = "fname";					$arrValues[] = ucfirst(strtolower(addslashes($arrTempName[1])));		
		$arrFields[] = "lname";					$arrValues[] = ucfirst(strtolower(addslashes($arrTempName[0])));
		
		//refining DOB
		$strDOB = $this->obj_hl7->getSegmentFieldAsString($PIDIndex,7);
		$strTempDOB = substr($strDOB,0,4)."-".substr($strDOB,4,2)."-".substr($strDOB,6);
		
		$arrFields[] = "DOB";					$arrValues[] = $strTempDOB;
		
		//refining sex
		$strSex = $this->obj_hl7->getSegmentFieldAsString($PIDIndex,8);
		$strTempSex = ($strSex == "M") ? "Male" : "Female";
		
		$arrFields[] = "sex";					$arrValues[] = $strTempSex;
		
		//refining patient address
		$strAddress = $this->obj_hl7->getSegmentFieldAsString($PIDIndex,11);
		
		$strTempAddress = explode("^",strrev($strAddress));
		
		$strTempZipCode = str_replace('-','',trim(strrev($strTempAddress[0])));
		$strTempZipCodeExt	= '';
		if(strlen($strTempZipCode)>5){
			$strTempZipCode 	= substr($strTempZipCode,0,5);
			$strTempZipCodeExt 	= substr($strTempZipCode,5);
		}
		
		$strTempState = strrev($strTempAddress[1]);
		$strTempCity = ucfirst(strtolower(addslashes(strrev($strTempAddress[2]))));
		
		if(count($strTempAddress) > 4){
			$strTempStreet2 = ucwords(strtolower(addslashes(strrev($strTempAddress[3]))));
			$strTempStreet1 = ucwords(strtolower(addslashes(strrev($strTempAddress[4]))));
		}else{
			$strTempStreet1 = ucwords(strtolower(addslashes(strrev($strTempAddress[3]))));
			$strTempStreet2 = "";
		}
		
		$arrFields[] = "street";				$arrValues[] = $strTempStreet1;		
		$arrFields[] = "street2";				$arrValues[] = $strTempStreet2;		
		$arrFields[] = "city";					$arrValues[] = $strTempCity;
		$arrFields[] = "state";					$arrValues[] = $strTempState;		
		$arrFields[] = "postal_code";			$arrValues[] = $strTempZipCode;
		$arrFields[] = "zip_ext";				$arrValues[] = $strTempZipCodeExt;

		//refining home phone number
		$strHomePhone = $this->obj_hl7->getSegmentFieldAsString($PIDIndex,13);
		$arrHomePhone = explode("^", $strHomePhone);
		$strTempHomePhone = preg_replace("/[^\d\s]/","",$arrHomePhone[0]);
		$strTempMobilePhone = preg_replace("/[^\d\s]/","",$arrHomePhone[1]);
		$strTempHomePhone = substr($strTempHomePhone,0,3)."-".substr($strTempHomePhone,3,3)."-".substr($strTempHomePhone,6);
		if(trim($strTempMobilePhone) != ""){
			$strTempMobilePhone = substr($strTempMobilePhone,0,3)."-".substr($strTempMobilePhone,3,3)."-".substr($strTempMobilePhone,6);
		}
		
		$arrFields[] = "phone_home";			$arrValues[] = $strTempHomePhone;

		$arrFields[] = "phone_cell";			$arrValues[] = $strTempMobilePhone;
		
		//refining work phone number
		$strTempBizPhone = "";
		$strBizPhone = $this->obj_hl7->getSegmentFieldAsString($PIDIndex,14);
		if(trim($strBizPhone) != ""){
			$strTempBizPhone = preg_replace("/[^\d\s]/","",$strBizPhone);
			$strTempBizPhone = substr($strTempBizPhone,0,3)."-".substr($strTempBizPhone,3,3)."-".substr($strTempBizPhone,6);
		}
		
		$arrFields[] = "phone_biz";			$arrValues[] = $strTempBizPhone;

		$arrFields[] = "athenaID";				$arrValues[] = $athena_id;

		//heard about us
		$PV2Index = $this->getSegmentByName('PV2');
		if($PV2Index>0 && $PV2Index!=NULL){
			$arrElemHeardAbtUs = explode("^", $this->obj_hl7->getSegmentFieldAsString($PV2Index,13));
			$elem_heardAbtUs = addslashes($arrElemHeardAbtUs[0]);
			$elem_heardAbtUsValue = addslashes($arrElemHeardAbtUs[1]);
			$heardDate = date('Y-m-d');
	
			$chkqryHeardMaster = "SELECT heard_id FROM heard_about_us WHERE heard_options = '".addslashes($elem_heardAbtUs)."' limit 1";
			$rschkqryHeardMaster = imw_query($chkqryHeardMaster);
			if($rschkqryHeardMaster){
				if(imw_num_rows($rschkqryHeardMaster) == 0){
					$qryHeard = "INSERT INTO heard_about_us SET heard_options = '".addslashes($elem_heardAbtUs)."'";
					$resHeard = imw_query($qryHeard);
					$elem_heardAbtUs = imw_insert_id();
				}else{
					$rowChkQryHeardMaster = imw_fetch_array($rschkqryHeardMaster);
					$elem_heardAbtUs = $rowChkQryHeardMaster['heard_id'];
				}	
			}
	
			$hdesc_qry = "SELECT id FROM heard_about_us_desc WHERE heard_id = '".addslashes($elem_heardAbtUs)."' and heard_desc = '".addslashes($elem_heardAbtUsValue)."'";
			$hdesc_res = imw_query($hdesc_qry);
			if($hdesc_res){
				if(imw_num_rows($hdesc_res) == 0){
					$inshdesc_qry = "INSERT INTO heard_about_us_desc SET heard_desc = '".addslashes($elem_heardAbtUsValue)."', heard_id = '".$elem_heardAbtUs."'";
					imw_query($inshdesc_qry);
				}
			}
	
			$arrFields[] = "heard_abt_us";			$arrValues[] = $elem_heardAbtUs;
			$arrFields[] = "heard_abt_desc";		$arrValues[] = $elem_heardAbtUsValue;
			$arrFields[] = "heard_about_us_date";	$arrValues[] = $heardDate;
		}
		//$arrNewValues = $this->add_single_quotes_to_val($arrValues);
		//print_r($arrNewValues);

		$strQry = "";
		$jk = 0;
		foreach($arrFields as $strColumn){
			$strQry .= " ".$strColumn." = '".$arrValues[$jk]."', ";
			$jk++;
		}
		$strQry = substr($strQry,0,-2);

		//checking for existing records
		$strPatQry = "SELECT id, fname, lname FROM patient_data WHERE athenaID = '".$athena_id."'"; 
		$rsPatData = imw_query($strPatQry);	
		
		if(imw_num_rows($rsPatData) > 0){
			$this->response .= "<error>Patient with ID ".$athena_id." already exists.</error>";
			$arrPatientId = imw_fetch_assoc($rsPatData);
			$intPatientId = $arrPatientId['id'];
			
			if($intPatientId != $athena_id){
				$this->response .= "<error>Record mismatching Alert! Patient with ID ".$intPatientId." has ".$athena_id." Athena ID.</error>";
				$this->resp_log_mode = "ALERT";
				$this->execution_status = false;
			}else if(strtolower($arrPatientId['fname']) != strtolower(addslashes($arrTempName[1])) || strtolower($arrPatientId['lname']) != strtolower(addslashes($arrTempName[0]))){
				$this->response .= "<error>Record mismatching Alert! Patient has different name in records with athena ID ".$athena_id.".</error>";
				$this->resp_log_mode = "ALERT";
				$this->execution_status = false;
			}else{
				$this->response .= "<info>Updating Patient record with new received data.</info>";
				
				$strQry = "UPDATE patient_data SET ".$strQry." WHERE id = '".$intPatientId."'";
				$blRESULT = imw_query($strQry);
				if($blRESULT === false){
					$this->error_logs();
				}
			}
		}else{
			$strPatQry = "SELECT id, fname, lname FROM patient_data WHERE id = '".$athena_id."'";
			$rsPatData = imw_query($strPatQry);	
			
			if(imw_num_rows($rsPatData) > 0){
				$this->response .= "<error>Overwrite Alert! Patient with ID ".$athena_id." needs to be removed.</error>";
				$this->resp_log_mode = "ALERT";
				$this->execution_status = false;
			}else{
				$this->response .= "<info>Adding new Patient with ID ".$athena_id.".</info>";
				
				$strQry = "INSERT INTO patient_data SET ".$strQry;
				$blRESULT = imw_query($strQry);
				if($blRESULT === false){
					$this->error_logs();
				}
				
				$erx_entry = 0;
				$copay_Res = imw_query("select Allow_erx_medicare,erx_entry from copay_policies WEHRE policies_id='1'");
				if($copay_Res && imw_num_rows($copay_Res)==1){
					$copay_Rs = imw_fetch_assoc($copay_Res);
					$erx_entry = $copay_Rs['erx_entry'];	
				}
				
				$strQry = "UPDATE patient_data SET pid = '".$athena_id."', erx_entry='".$erx_entry."' WHERE id = '".$intPatientId."'";
				$blRESULT = imw_query($strQry);
				if($blRESULT === false){
					$this->error_logs();
				}
			}
		}
	}

	/*
	Function: add_update_pt_resp_party
	Purpose: to add / update patient responsible party information
	Update On: 25th May 2011
	*/
	function add_update_pt_resp_party(){
		$GT1Index = $this->getSegmentByName('GT1');
		if($this->execution_status == true && !empty($this->patient_id) && $GT1Index){
			$this->response .= "<info>Updating guarantor information for the Patient.</info>";

			//inserting values into resp_party table (guarantor)
			$strQry = "SELECT id FROM resp_party WHERE patient_id = '".$this->patient_id."'"; 
			$rsData = imw_query($strQry);
			if($rsData === false){
				$this->error_logs();
			}
			
			//setting columns
			$arrRPFields = $arrRPValues = array();
			
			//getting first name and last name of guarantor
			$arrTempRPName = explode("^",$this->obj_hl7->getSegmentFieldAsString($GT1Index,3));
			
			$arrRPFields[] = "fname";						$arrRPValues[] = ucfirst(strtolower($arrTempRPName[0]));	
			$arrRPFields[] = "lname";						$arrRPValues[] = ucfirst(strtolower($arrTempRPName[1]));	
			$arrRPFields[] = "patient_id";					$arrRPValues[] = $this->patient_id;
			
			//refining guarantor address
			$strRPAddress = $this->obj_hl7->getSegmentFieldAsString($GT1Index,5);
			
			$strTempRPAddress = explode("^",strrev($strRPAddress));
			
			$strTempRPZipCode = strrev($strTempRPAddress[0]);
			$strTempRPState = strrev($strTempRPAddress[1]);
			$strTempRPCity = strrev($strTempRPAddress[2]);
			
			if(count($strTempRPAddress) > 4){
				$strTempRPAddress = strrev($strTempRPAddress[3])." ".strrev($strTempRPAddress[4]);
			}else{
				$strTempRPAddress = strrev($strTempRPAddress[3]);
			}
			
			$arrRPFields[] = "address";						$arrRPValues[] = $strTempRPAddress;			
			$arrRPFields[] = "city";						$arrRPValues[] = $strTempRPCity;			
			$arrRPFields[] = "state";						$arrRPValues[] = $strTempRPState;			
			$arrRPFields[] = "zip";							$arrRPValues[] = $strTempRPZipCode;
			
			//refining home phone number of guarantor
			$PIDIndex = $this->getSegmentByName('PID');
			$strRPHomePhone = $this->obj_hl7->getSegmentFieldAsString($PIDIndex,13);
			$strTempRPHomePhone = preg_replace("/[^\d\s]/","",$strRPHomePhone);
			$strTempRPHomePhone = substr($strTempRPHomePhone,0,3)."-".substr($strTempRPHomePhone,3,3)."-".substr($strTempRPHomePhone,6);
			
			$arrRPFields[] = "home_ph";						$arrRPValues[] = $strTempRPHomePhone;
			
			//$arrValues = $this->add_single_quotes_to_val($arrRPValues);
		
			$strQry = "";
			$jk = 0;
			foreach($arrRPFields as $strColumn){
				$strQry .= " ".$strColumn." = '".$arrRPValues[$jk]."', ";
				$jk++;
			}
			$strQry = substr($strQry,0,-2);

			if($arrTempRPName[0] != "" && $arrTempRPName[1] != ""){
				if(imw_num_rows($rsData) > 0){
					$strQry = "DELETE FROM resp_party WHERE patient_id = '".$this->patient_id."'";
					$blRes = imw_query($strQry);
					if($blRes === false){
						$this->error_logs();
					}
				}
				
				$strQry = "INSERT INTO resp_party SET ".$strQry;		
				$blRes = imw_query($strQry);
				if($blRes === false){
					$this->error_logs();
				}
			}
		}
	}

	/*
	Function: add_update_patient_ins
	Purpose: to add / update patient insurance information
	Update On: 25th May 2011
	*/
	function add_update_patient_ins($IN1Index=''){
		if(!$IN1Index || $IN1Index==''){
			$IN1Index = $this->getSegmentByName('IN1');
		}
		if($this->obj_hl7->getSegmentFieldAsString($IN1Index,0)=='IN1'){
			if($this->execution_status == true && !empty($this->patient_id)){
				$this->response .= "<info>Updating Insurance information for the Patient.</info>";
				if($IN1Index){
					$this->add_update_ins_company($IN1Index);
					$this->add_update_pt_ins_case($IN1Index);
					$this->add_update_pt_insurance($IN1Index);
					/*--IT NEEDS TO REPEAT FOR EVERY ACTIVE INSURANCE CARRIER FOR THIS PATIENT--*/
					if($this->obj_hl7->getSegmentFieldAsString($IN1Index+1,0)=='IN1'){
						$this->add_update_patient_ins($IN1Index+1);
					}else if($this->obj_hl7->getSegmentFieldAsString($IN1Index+2,0)=='IN1'){
						$this->add_update_patient_ins($IN1Index+2);
					}
				}
			}
		}
	}
	
	/*
	Function: add_update_patient_ins
	Purpose: to add / update insurance compnay into the system based on athenaID = id
	Update On: 25th May 2011
	*/
	function add_update_ins_company($segment_no){
		//updating company name in insurance companies table
		$arrInsComp = explode("^", $this->obj_hl7->getSegmentFieldAsString($segment_no, 3));
		$intInsCompId = $arrInsComp[0];
		$strInsCompName = $arrInsComp[1];

		//settting columsn
		$arrInsCompFields = $arrInsCompValues = array();
		
		$arrInsCompFields[] = "name";						$arrInsCompValues[] = addslashes($strInsCompName);	
		
		$arrInsCompAddress = explode("^",$this->obj_hl7->getSegmentFieldAsString($segment_no,5));		
		$arrInsCompFields[] = "contact_address";			$arrInsCompValues[] = addslashes($arrInsCompAddress[0]);		
		$arrInsCompFields[] = "City";						$arrInsCompValues[] = addslashes($arrInsCompAddress[1]);	
		$arrInsCompFields[] = "State";						$arrInsCompValues[] = addslashes($arrInsCompAddress[2]);		
		$arrInsCompFields[] = "Zip";						$arrInsCompValues[] = $arrInsCompAddress[3];
		
		$strPhone = $this->obj_hl7->getSegmentFieldAsString($segment_no,7);
		$strTempPhone = preg_replace("/[^\d\s]/","",$strPhone);
		$strTempPhone = substr($strTempPhone,0,3)."-".substr($strTempPhone,3,3)."-".substr($strTempPhone,6);
		
		$arrInsCompFields[] = "phone";						$arrInsCompValues[] = $strTempPhone;		
		$arrInsCompFields[] = "athenaID";					$arrInsCompValues[] = $intInsCompId;
		
		//$arrNewInsCompValues = $this->add_single_quotes_to_val($arrInsCompValues);

		$strQry = "";
		$jk = 0;
		foreach($arrInsCompFields as $strColumn){
			$strQry .= " ".$strColumn." = '".$arrInsCompValues[$jk]."', ";
			$jk++;
		}
		$strQry = substr($strQry,0,-2);
		
		//checking for existing records
		$strQry1 = "SELECT id, name FROM insurance_companies WHERE athenaID = '".$intInsCompId."'";
		$rsData = imw_query($strQry1);
		if($rsPatData === false){
			$this->error_logs();
		}
		
		if(imw_num_rows($rsData) > 0){
			$this->response .= "<error>Insurance Company with ID ".$intInsCompId." already exists.</error>";
			$arrInsuranceCompId = imw_fetch_assoc($rsData);
			$intInsuranceCompId = $arrInsuranceCompId['id'];
			$this->ins_comp_id = $intInsuranceCompId;
		}else{
			
			$this->response .= "<info>Adding new Insurance Company with ID ".$intInsCompId.".</info>";
				
			$strQry = "INSERT INTO insurance_companies SET ".$strQry;
			$blres = imw_query($strQry);	
			if($blres === false){
				$this->error_logs();
			}
					
			$this->ins_comp_id = imw_insert_id();	
			$this->response .= "<info>Insurance Company added successfully.</info>";
		}
	}
	
	/*
	Function: add_update_pt_ins_case
	Purpose: to add / update patient insurance case
	Update On: 25th May 2011
	*/	
	function add_update_pt_ins_case($segment_no){
		if($this->execution_status == true && !empty($this->ins_comp_id)){
			
			//getting insuracnce case
			$arrInsCase = explode("^", $this->obj_hl7->getSegmentFieldAsString($segment_no, 2));
			$intInsCaseId = $arrInsCase[0];
			$strInsCaseName = $arrInsCase[1];

			//setting columns
			$arrInsCaseFields = $arrInsCaseValues = array();	

			//getting normal case type id
			$qry = "SELECT case_id FROM insurance_case_types WHERE normal = 1 LIMIT 1";
			$res = imw_query($qry);
			if(imw_num_rows($res) > 0){
				$arr = imw_fetch_assoc($res);
				$ins_case_type_id = $arr["case_id"];
			}else{
				$ins_case_type_id = constant('HL7_DEFAULT_INSURANCE_CASE_TYPE');
			}
			
			$arrInsCaseFields[] = "ins_case_name";						$arrInsCaseValues[] = addslashes($strInsCaseName);			
			$arrInsCaseFields[] = "ins_case_type";						$arrInsCaseValues[] = $ins_case_type_id;			
			$arrInsCaseFields[] = "patient_id";							$arrInsCaseValues[] = $this->patient_id;			
			$arrInsCaseFields[] = "start_date";							$arrInsCaseValues[] = date("Y-m-d H:i:s");			
			$arrInsCaseFields[] = "end_date";							$arrInsCaseValues[] = "0000-00-00 00:00:00";			
			$arrInsCaseFields[] = "case_id";							$arrInsCaseValues[] = "";			
			$arrInsCaseFields[] = "case_status";						$arrInsCaseValues[] = "Open";		
			$arrInsCaseFields[] = "athenaID";							$arrInsCaseValues[] = $intInsCaseId;
			
			//$arrNewInsCompValues = $this->add_single_quotes_to_val($arrInsCaseValues);

			$strQry = "";
			$jk = 0;
			foreach($arrInsCaseFields as $strColumn){
				$strQry .= " ".$strColumn." = '".$arrInsCaseValues[$jk]."', ";
				$jk++;
			}
			$strQry = substr($strQry,0,-2);
			
			if(!empty($intInsCaseId)){
				//checking for existing records
				$strQry1 = "SELECT ins_caseid FROM insurance_case WHERE patient_id = '".$this->patient_id."'";
				$rsData = imw_query($strQry1);
				if($rsPatData === false){
					$this->error_logs();
				}
				
				if(imw_num_rows($rsData) > 0){ 
					$this->response .= "<error>Insurance Case for the patient already exists.</error>";
					$arrInsuranceCompId = imw_fetch_assoc($rsData);
					$intInsuranceCompId = $arrInsuranceCompId['ins_caseid'];
					$this->ins_case_id = $intInsuranceCompId;
				}else{
					$this->response .= "<info>Adding new Patient Insurance Case with ID ".$intInsCaseId.".</info>";
						
					$strQry = "INSERT INTO insurance_case SET ".$strQry;		
					$blres = imw_query($strQry);	
					if($blres === false){
						$this->error_logs();
					}
							
					$this->ins_case_id = imw_insert_id();	
					$this->response .= "<info>Patient Insurance Case added successfully.</info>";
				}
			}
		}
	}
	
	/*
	Function: add_update_pt_insurance
	Purpose: to add / update patient insurance data
	Update On: 25th May 2011
	*/
	function add_update_pt_insurance($segment_no){

		if($this->execution_status == true && !empty($this->ins_comp_id) && !empty($this->ins_case_id)){
			
			$blItsNewDetail = true;

			//checking if any insurance exists for this patient
			//		1 - expired
			//		0 - not expired
			switch ($this->obj_hl7->getSegmentFieldAsString($segment_no,22)){
				case 1:			$strInsType = "primary";			break;
				case 2:			$strInsType = "secondary";			break;
				case 3:			$strInsType = "tertiary";			break;
				default:		$strInsType = "tertiary";			break;
			}
			
			//name
			$arrTempName = explode("^",$this->obj_hl7->getSegmentFieldAsString($segment_no,16));
			
			//refining relationship
			switch (strtolower($this->obj_hl7->getSegmentFieldAsString($segment_no,17))){
				case "self":		$strRelation = "self";		break;
				case "father":		$strRelation = "Father";	break;
				case "mother":		$strRelation = "Mother";	break;
				case "son":			$strRelation = "Son";		break;
				case "daughter":	$strRelation = "Doughter";	break;
				case "spouse":		$strRelation = "Spouse";	break;
				case "guardian":	$strRelation = "Guardian";	break;
				case "poa":			$strRelation = "POA";		break;
				case "employee":	$strRelation = "Employee";	break;
				default:			$strRelation = "Other";		break;
			}
			
			//refining DOB
			$strDOB = $this->obj_hl7->getSegmentFieldAsString($segment_no,18);
			$strTempDOB = substr($strDOB,0,4)."-".substr($strDOB,4,2)."-".substr($strDOB,6);

			$strQry = "SELECT id, provider, pid, type, actInsComp, policy_number, subscriber_lname, subscriber_fname, subscriber_relationship, subscriber_DOB, ins_caseid FROM insurance_data WHERE pid = '".$this->patient_id."' AND type = '".$strInsType."' AND actInsComp = '1' LIMIT 1";
			$rsData = imw_query($strQry);
			if($rsData === false){
				$this->error_logs();
			}
			
			if(imw_num_rows($rsData) > 0){
				//expiring old insurance
				$arrOldData = imw_fetch_assoc($rsData);
				$intOldInsId = $arrOldData['id'];
				
				//checking if same insurance deatsils ar sent			
				if($arrOldData['provider'] == $this->ins_comp_id && $arrOldData['policy_number'] == $this->obj_hl7->getSegmentFieldAsString($segment_no,36) && ucfirst(strtolower($arrOldData['subscriber_lname'])) == ucfirst(strtolower($arrTempName[1])) && ucfirst(strtolower($arrOldData['subscriber_fname'])) == ucfirst(strtolower($arrTempName[0])) && $arrOldData['subscriber_relationship'] == $strRelation && $arrOldData['subscriber_DOB'] == $strTempDOB && $arrOldData['ins_caseid'] == $this->ins_case_id){
					$blItsNewDetail = false;
				}
				
				if($blItsNewDetail == true){
					$arrUpdOldInsFields = $arrUpdOldInsValues = array();
					
					$arrUpdOldInsFields[] = "expiration_date";					$arrUpdOldInsValues[] = date("Y-m-d H:i:s");					
					$arrUpdOldInsFields[] = "actInsComp";						$arrUpdOldInsValues[] = 0;
					
					//$arrNewInsCompValues = $this->add_single_quotes_to_val($arrUpdOldInsValues);

					$strQry = "";
					$jk = 0;
					foreach($arrUpdOldInsFields as $strColumn){
						$strQry .= " ".$strColumn." = '".$arrUpdOldInsValues[$jk]."', ";
						$jk++;
					}
					$strQry = substr($strQry,0,-2);
					
					$strQry = "UPDATE insurance_data SET ".$strQry." WHERE id = '".$intOldInsId."'";	
					$resbl = imw_query($strQry);
					if($resbl === false){
						$this->error_logs();
					}
				}
			}else{
				//inserting new insurance
				$arrInsOldInsFields = $arrInsOldInsValues = array();
				
				$arrInsOldInsFields[] = "pid";							$arrInsOldInsValues[] = $this->patient_id;				
				$arrInsOldInsFields[] = "provider";						$arrInsOldInsValues[] = $this->ins_comp_id;				
				$arrInsOldInsFields[] = "type";							$arrInsOldInsValues[] = $strInsType;				
				$arrInsOldInsFields[] = "ins_caseid";					$arrInsOldInsValues[] = $this->ins_case_id;				
				$arrInsOldInsFields[] = "actInsComp";					$arrInsOldInsValues[] = 1;				
				$arrInsOldInsFields[] = "actInsCompDate";				$arrInsOldInsValues[] = date("Y-m-d H:i:s");
				$arrInsOldInsFields[] = "expiration_date";				$arrInsOldInsValues[] = "0000-00-00 00:00:00";				
				$arrInsOldInsFields[] = "effective_date";				$arrInsOldInsValues[] = date("Y-m-d H:i:s");				
				$arrInsOldInsFields[] = "policy_number";				$arrInsOldInsValues[] = $this->obj_hl7->getSegmentFieldAsString($segment_no,36);				
				$arrInsOldInsFields[] = "subscriber_relationship";		$arrInsOldInsValues[] = $strRelation;			
				$arrInsOldInsFields[] = "subscriber_DOB";				$arrInsOldInsValues[] = $strTempDOB;
				
				//refining sex
				$strSex = $this->obj_hl7->getSegmentFieldAsString($segment_no,43);
				$strTempSex = ($strSex == "M") ? "Male" : "Female";
				
				$arrInsOldInsFields[] = "subscriber_sex";				$arrInsOldInsValues[] = $strTempSex;			
				$arrInsOldInsFields[] = "subscriber_lname";				$arrInsOldInsValues[] = ucfirst(strtolower($arrTempName[1]));				
				$arrInsOldInsFields[] = "subscriber_fname";				$arrInsOldInsValues[] = ucfirst(strtolower($arrTempName[0]));
				
			//	$arrNewInsCompValues = $this->add_single_quotes_to_val($arrInsOldInsValues);

				$strQry = "";
				$jk = 0;
				foreach($arrInsOldInsFields as $strColumn){
					$strQry .= " ".$strColumn." = '".$arrInsOldInsValues[$jk]."', ";
					$jk++;
				}
				$strQry = substr($strQry,0,-2);
				
				$strQry = "INSERT INTO insurance_data SET ".$strQry;
				$resbl = imw_query($strQry);
				if($resbl === false){
					$this->error_logs();
				}
			}
		}
	}

	function new_appointment($strMode){
		$this->debug_code();
		$this->add_update_patient();
		$this->add_update_pt_resp_party();
		$this->add_doctor();
		$this->add_operator();
		$this->add_facility();
		$this->add_procedure();
		$this->add_update_appointment($strMode);
	}

	function add_doctor(){
		if($this->execution_status == true){
			//checking provider if not added adding it
			$PV1Index = $this->getSegmentByName('PV1');
			$arrAthenaProvider = explode("^",$this->obj_hl7->getSegmentFieldAsString($PV1Index,17));
			$intAthenaProviderId = $arrAthenaProvider[0];
			$strAthenaProviderName = $arrAthenaProvider[1];
			
			//setting columns
			$arrProviderName = explode(" ",$strAthenaProviderName);
			if(count($arrProviderName) > 2){
				$strLname = $arrProviderName[0];
				$strMname = $arrProviderName[1];
				$strFname = $arrProviderName[2];				
			}else{
				$strLname = $arrProviderName[0];
				$strMname = "";
				$strFname = $arrProviderName[1];
			}
		
			//getting provider color
			$strProviderColor = "#".strtoupper(dechex(rand(0,10000000)));
			
			$strUsername = strtolower(str_replace("'","",$strLname));
			$strPassword = md5(strtolower(str_replace("'","",$strLname)).rand(1,10000));

			$privileges = array(
								"priv_cl_work_view" => 1,
								"priv_cl_tests" => 1,
								"priv_cl_medical_hx" => 1,
								
								"priv_Front_Desk" => 1,
								"priv_Billing" => 0,
								"priv_Accounting" => 1,
								"priv_Security" => 0,

								"priv_sc_scheduler" => 0,
								"priv_sc_house_calls" => 0,
								"priv_sc_recall_fulfillment" => 0,
								"priv_bi_front_desk" => 0,
								"priv_bi_ledger" => 0,
								"priv_bi_prod_payroll" => 0,
								"priv_bi_ar" => 0,
								"priv_bi_statements" => 0,
								"priv_bi_end_of_day" => 0,								
								"priv_cl_clinical" => 0,
								"priv_cl_visits" => 0,
								"priv_cl_ccd" => 0,
								"priv_cl_order_set" => 0,
								
								"priv_vo_clinical" => 0,
								"priv_vo_pt_info" => 0,
								"priv_vo_acc" => 0,

								"priv_Sch_Override" => 0,
								"priv_pt_Override" => 0,
								"priv_admin" => 0,	
								"priv_Optical" => 0,
								"priv_iOLink" => 0
							);
			$str_access_priv = serialize($privileges);

			//checking existing records
			$strUQry = "SELECT id, fname, lname FROM users WHERE athenaID = '".$intAthenaProviderId."'";
			$rsUData = imw_query($strUQry);
			if($rsUData === false){
				$this->error_logs();
			}
			
			if(imw_num_rows($rsUData) > 0){				
				$strMessage .= "<error>Provider with ID ".$intAthenaProviderId." already exists.</error>";

				$arrUProviderId = imw_fetch_assoc($rsUData);
				$intUProviderId = $arrUProviderId['id'];
				$this->doctor_id = $intUProviderId;
			}else{
				$this->response .= "<error>Provider not found.</error>";
				$this->execution_status = false;
				return false;
				
				$this->response .= "<info>Adding new Provider with ID ".$intAthenaProviderId.".</info>";
					
				$strQry = "INSERT INTO users SET 
							username = '".$strUsername."',
							password = '".$strPassword."', 
							fname = '".addslashes($strFname)."',
							mname = '".addslashes($strMname)."',
							lname = '".addslashes($strLname)."', 
							see_auth = '1',
							provider_color = '".$strProviderColor."',  						
							user_type = '1',
							gro_id = '".$arrGroupId['gro_id']."',
							default_group = '".$arrGroupId['gro_id']."',
							access_pri = '".$str_access_priv."',
							default_facility = '".$intFacilityId."',  						
							schedule_warnings = 'disallow',
							superuser = 'no',
							locked = '0',
							passwordChanged = '0',
							loginAttempts = '0',
							passCreatedOn = '".date("Y-m-d")."',
							HIPPA_STATUS  = 'no', 
							Enable_Scheduler = '1',
							athenaID = '".$intAthenaProviderId."'";	
				$rsPatData = imw_query($strQry);
				if($rsPatData === false){
					$this->error_logs();
				}
				$this->doctor_id = imw_insert_id();
			}			
		}
	}

	function add_operator(){
		if($this->execution_status == true){
			//checking provider if not added adding it
			$SCHIndex = $this->getSegmentByName('SCH');
			$strAthenaOperatorUsername = $this->obj_hl7->getSegmentFieldAsString($SCHIndex,16);
			$intAthenaProviderId = "-1";
		
			//setting columns
			$strLname = $strAthenaOperatorUsername;
			$strMname = "";
			$strFname = "";
			
			$strProviderColor = "";
			
			$strUsername = $strAthenaOperatorUsername;
			$strPassword = md5(strtolower(str_replace("'","",$strAthenaOperatorUsername)).rand(1,10000));

			$privileges = array(
								"priv_cl_work_view" => 1,
								"priv_cl_tests" => 1,
								"priv_cl_medical_hx" => 1,
								
								"priv_Front_Desk" => 1,
								"priv_Billing" => 0,
								"priv_Accounting" => 1,
								"priv_Security" => 0,

								"priv_sc_scheduler" => 0,
								"priv_sc_house_calls" => 0,
								"priv_sc_recall_fulfillment" => 0,
								"priv_bi_front_desk" => 0,
								"priv_bi_ledger" => 0,
								"priv_bi_prod_payroll" => 0,
								"priv_bi_ar" => 0,
								"priv_bi_statements" => 0,
								"priv_bi_end_of_day" => 0,								
								"priv_cl_clinical" => 0,
								"priv_cl_visits" => 0,
								"priv_cl_ccd" => 0,
								"priv_cl_order_set" => 0,
								
								"priv_vo_clinical" => 0,
								"priv_vo_pt_info" => 0,
								"priv_vo_acc" => 0,

								"priv_Sch_Override" => 0,
								"priv_pt_Override" => 0,
								"priv_admin" => 0,	
								"priv_Optical" => 0,
								"priv_iOLink" => 0
							);
			$str_access_priv = serialize($privileges);

			$strOQry = "SELECT id FROM users WHERE LOWER(username) = '".strtolower(addslashes($strAthenaOperatorUsername))."'";
			$rsUData = imw_query($strOQry);
			if($rsUData === false){
				$this->error_logs();
			}
			
			if(imw_num_rows($rsUData) > 0){				
				$this->response .= "<error>Operator with Username ".$strAthenaOperatorUsername." already exists.</error>";
				$arrUData = imw_fetch_assoc($rsUData);
				$this->operator_username = $strAthenaOperatorUsername;
				$this->operator_id = $arrUData["id"];
			}else{
				$this->response .= "<info>Adding new Operator with username ".$strAthenaOperatorUsername.".</info>";
					
				$strQry = "INSERT INTO users SET 
							username = '".$strUsername."',
							password = '".$strPassword."', 
							fname = '".addslashes($strFname)."',
							mname = '".addslashes($strMname)."',
							lname = '".addslashes($strLname)."', 
							see_auth = '1',
							provider_color = '".$strProviderColor."',  						
							user_type = '1',
							gro_id = '".$arrGroupId['gro_id']."',
							default_group = '".$arrGroupId['gro_id']."',
							access_pri = '".$str_access_priv."',
							default_facility = '".$intFacilityId."',  						
							schedule_warnings = 'disallow',
							superuser = 'no',
							locked = '0',
							passwordChanged = '0',
							loginAttempts = '0',
							passCreatedOn = '".date("Y-m-d")."',
							HIPPA_STATUS  = 'no', 
							Enable_Scheduler = '1',
							athenaID = '".$intAthenaProviderId."'";	
				$rsPatData = imw_query($strQry);
				if($rsPatData === false){
					$this->error_logs();
				}
				$this->operator_username = $strAthenaOperatorUsername;
				$this->operator_id = imw_insert_id();
			}
		}
	}

	function add_facility(){	
		if($this->execution_status == true){
			//checking facility if not added adding it
			$facLineIndex = $this->getSegmentByName('AIL');
			$arrAthenaFacility = explode("^",$this->obj_hl7->getSegmentFieldAsString($facLineIndex,3));
			$intAthenaFacilityId = $arrAthenaFacility[0];
			$strAthenaFacilityName = $arrAthenaFacility[1];
			$strQry = "SELECT id FROM facility WHERE athenaID = '".$intAthenaFacilityId."'";
			$rsData = imw_query($strQry);
			
			if(imw_num_rows($rsData) > 0){
				$this->response .= "<error>Facility with ID ".$intAthenaFacilityId." already exists.</error>";
				$arrFacilityId = imw_fetch_assoc($rsData);
				$intFacilityId = $arrFacilityId['id'];
				$this->facility_id = $intFacilityId;
			}else{			
				//getting group id
				$strGQry = "SELECT gro_id FROM groups_new WHERE del_status='0' ORDER BY gro_id ASC LIMIT 1";
				$rsGData = imw_query($strGQry);
				$arrGroupId = imw_fetch_assoc($rsGData);
				
				$strQry = "INSERT INTO facility SET
							name = '".addslashes($strAthenaFacilityName)."',
							facility_type = '2',
							default_group = '".$arrGroupId['gro_id']."',
							gro_id = '".$arrGroupId['gro_id']."',
							regular_time_slot = '10',						
							fac_flag = 'false',
							athenaID = '".$intAthenaFacilityId."'";
				imw_query($strQry);
				$this->facility_id = imw_insert_id();
				$this->response .= "<info>Facility added successfully.</info>";			
			}
		}
	}

	function add_procedure(){
		if($this->execution_status == true){
			//checking procedure if not added adding it
			$SCHIndex = $this->getSegmentByName('SCH');
			$strAthenaProcedureName = $this->obj_hl7->getSegmentFieldAsString($SCHIndex,7);
			$intAthenaProcTime = $this->obj_hl7->getSegmentFieldAsString($SCHIndex,9);
			
			$strPQry = "SELECT id FROM slot_procedures WHERE LOWER(proc) = '".addslashes(strtolower($strAthenaProcedureName))."'";
			$rsPData = imw_query($strPQry);
			
			if(imw_num_rows($rsPData) > 0){
				$arrProcId = imw_fetch_assoc($rsPData);
				$intProcId = $arrProcId['id'];
				$this->response .= "<error>Procedure with name ".addslashes($strAthenaProcedureName)." already exists.</error>";
				$this->procedure_id = $intProcId;
			}else{
				//adding proc time
				$strTQry = "SELECT id FROM slot_procedures WHERE proc = '' AND times = '".$intAthenaProcTime."'";
				$rsTData = imw_query($strTQry);
				
				if(imw_num_rows($rsTData) > 0){
					$arrProcTId = imw_fetch_assoc($rsTData);
					$this->procedure_id = $arrProcTId['id'];
				}else{
					$strQry = "INSERT INTO slot_procedures SET
								proc = '',
								times = '".$intAthenaProcTime."',
								proc_time = '',							
								doctor_id = 0,
								procedureId = 0,
								active_status = 'yes',
								source='HL7', 
								source_uid='".$this->this_message_id."'";
					imw_query($strQry);
					$this->procedure_id = imw_insert_id();
				}
				
				//inserting procedure
				$arrAcronym = explode(" ",$strAthenaProcedureName);
				$strAcronym = "";
				if($arrAcronym){
					foreach ($arrAcronym as $strPart){
						$strAcronym .= strtoupper(substr($strPart,0,1));
					}
				}
				
				$strProcColor = "#".strtoupper(dechex(rand(0,10000000)));
				
				$strQry = "INSERT INTO slot_procedures SET
							proc = '".addslashes($strAthenaProcedureName)."',
							times = '',
							proc_time = '".$intProcTId."',
							acronym = '".addslashes($strAcronym)."',
							proc_color = '".$strProcColor."',
							doctor_id = 0,
							procedureId = '',
							active_status = 'yes',
							source='HL7',
							source_uid='".$this->this_message_id."'";
				imw_query($strQry);
				$this->procedure_id = imw_insert_id();
				
				$strQry = "UPDATE slot_procedures SET
							procedureId = '".$intProcId."'
							WHERE id = '".$intProcId."'";
				imw_query($strQry);
				
				$this->response .= "<info>Procedure added successfully.</info>";
			}
		}
	}

	function add_update_appointment($strMode){	
		if($this->execution_status == true && !empty($this->procedure_id) && !empty($this->facility_id) && !empty($this->doctor_id) && !empty($this->operator_id)){
			
			//checking if existing appointment rescheduling it
			$SCHIndex = $this->getSegmentByName('SCH');
			$intAthenaApptId = $this->obj_hl7->getSegmentFieldAsString($SCHIndex,1);
			$strDateTime = $this->obj_hl7->getSegmentFieldAsString($SCHIndex,11);
			$intDuration = $this->obj_hl7->getSegmentFieldAsString($SCHIndex,9);
			$dtStartDate = substr($strDateTime,0,4)."-".substr($strDateTime,4,2)."-".substr($strDateTime,6,2);
			$tmStartTime = substr($strDateTime,8,2).":".substr($strDateTime,10,2).":00";
			
			$strApptQry = "SELECT id, sa_app_start_date, sa_app_starttime FROM schedule_appointments WHERE athenaID = '".$intAthenaApptId."'";		
			$rsApptData = imw_query($strApptQry);
			
			//refining data
			$arrField = array();
			$arrValue = array();
			
			$arrField[] = "sa_aid";
			$arrValue[] = 0;
			
			$arrField[] = "sa_doctor_id";
			$arrValue[] = $this->doctor_id;
			
			$arrField[] = "sa_test_id";
			$arrValue[] = "0";
			
			$arrField[] = "sa_patient_id";
			$arrValue[] = $this->patient_id;
			
			//getting patient name
			$PIDIndex = $this->getSegmentByName('PID');
			$arrTempName = explode("^",$this->obj_hl7->getSegmentFieldAsString($PIDIndex,5));
			$strPatientName = ucfirst(strtolower(addslashes($arrTempName[1])))." ".ucfirst(strtolower(addslashes($arrTempName[0])));

			$arrField[] = "sa_patient_name";
			$arrValue[] = $strPatientName;
			
			$arrField[] = "sa_patient_app_status_id";
			$arrValue[] = 0;
			
			$arrField[] = "status_date";
			$arrValue[] = "0000-00-00";
			
			$arrField[] = "sa_app_time";
			$arrValue[] = date("Y-m-d H:i:s");
			
			$arrField[] = "sa_app_starttime";
			$arrValue[] = $tmStartTime;
			
			$arrField[] = "sa_app_endtime";
			$arrValue[] = $this->toAddTime($tmStartTime, "00:".$intDuration.":00");		
			
			$arrField[] = "sa_app_duration";
			$arrValue[] = $intDuration * 60;
			
			$arrField[] = "sa_facility_id";
			$arrValue[] = $this->facility_id;
			
			$arrField[] = "sa_app_start_date";
			$arrValue[] = $dtStartDate;
			
			$arrField[] = "sa_app_end_date";
			$arrValue[] = $dtStartDate;
			
			$arrField[] = "procedureid";
			$arrValue[] = $this->procedure_id;
			
			$arrField[] = "sa_madeby";
			$arrValue[] = $this->operator_username;
			
			$arrField[] = "status_update_operator_id";
			$arrValue[] = $this->operator_id;
			
			$arrField[] = "athenaID";
			$arrValue[] = $intAthenaApptId;

			//$arrNewValues = $this->add_single_quotes_to_val($arrValue);

			$jk = 0;
			foreach ($arrField as $strColumn){
				$strQry .= " $strColumn = '".$arrValue[$jk]."', ";	
				$jk++;
			}
			$strQry = substr($strQry,0,-2);	
			
			if(imw_num_rows($rsApptData) > 0){
				
				$arrApptId = imw_fetch_assoc($rsApptData);
				$intApptId = $arrApptId['id'];
				$this->appt_id = $intApptId;
				
				if($strMode == "addorupdate"){
					$strQry = "UPDATE schedule_appointments SET ".$strQry;						
					$strQry .= " WHERE id = '".$intApptId."'";		
					imw_query($strQry);
				
				
					//rescheduling
					$strMessage .= $this->changeStatus($this->appt_id, $this->patient_id, $arrApptId['sa_app_start_date'], $arrApptId['sa_app_starttime'], $this->operator_id, constant("HL7_DEFAULT_RESCHEDULE_STATUS_ID"));
				}			
			}else{
				
				//adding new appointment
				$strQry = "INSERT INTO schedule_appointments SET ".$strQry;					
				imw_query($strQry);
				$this->appt_id = imw_insert_id();
				
				$strMessage .= "<info>Appointment added successfully.</info>";
			}
			
			//setting status
			if($strMode != "addorupdate"){
				switch ($strMode){
					case "cancel":		//cancel
						$strMessage .= $this->changeStatus($this->appt_id, $this->patient_id, $dtStartDate, $tmStartTime, $this->operator_id, constant("HL7_DEFAULT_CANCEL_STATUS_ID"));
						break;
					case "check":
						$strCheckStatus = $this->obj_hl7->getSegmentFieldAsString($SCHIndex,25);
						if($strCheckStatus == "COMPLETED")	//check out
							$strMessage .= $this->changeStatus($this->appt_id, $this->patient_id, $dtStartDate, $tmStartTime, $this->operator_id, constant("HL7_DEFAULT_CHECKOUT_STATUS_ID"));
						elseif ($strCheckStatus == "ARRIVED")	//check in
							$strMessage .= $this->changeStatus($this->appt_id, $this->patient_id, $dtStartDate, $tmStartTime, $this->operator_id, constant("HL7_DEFAULT_CHECKIN_STATUS_ID"));					
						break;				
				}
			}
			$this->response .= $strMessage;
		}
	}

	//to add minutes to a time
	function toAddTime($tmHHIISS, $strTimeToAdd){
		$strQry = "SELECT ADDTIME('$tmHHIISS', '$strTimeToAdd') as tmNewTime";
		$rsData = imw_query($strQry);
		$arrData = imw_fetch_assoc($rsData);
		return $arrData['tmNewTime'];
	}	
	
	function changeStatus($intApptId, $intPatientId, $dtOldDate, $tmOldTime, $intOperatorId, $strMode){
		
		$strMessage = "";
		
		//copying appointment in previous status table before changing status in main table
		$strQry = "select id from previous_status where sch_id='$intApptId' and patient_id='$intPatientId' and status='$strMode'";
		$rsData = imw_query($strQry);
		list($intPrevStatusId) = imw_fetch_array($rsData);
		
		if(empty($intPrevStatusId)){
			$vquery_ind = " insert into previous_status set sch_id='$intApptId', patient_id='$intPatientId', status_time='".date('H:i:s')."',status_date='".date('Y-m-d')."',status='$strMode',old_date='$dtOldDate',old_time='$tmOldTime' ";
		}else{
			$vquery_ind = " update previous_status set sch_id='$intApptId',patient_id='$intPatientId', status_time='".date('H:i:s')."',status_date='".date('Y-m-d')."',status='$strMode', old_date='$dtOldDate', old_time='$tmOldTime' where id = '$intPrevStatusId'";
		}
		
		$vsql_ind = imw_query($vquery_ind);
		
		//updating status in main table
		$vquery_update = " update schedule_appointments set status_update_operator_id = '$intOperatorId', sa_patient_app_status_id='$strMode', status_date='".date('Y-m-d H:i:s')."' where id='$intApptId' ";		
		$vsql_update = @imw_query($vquery_update);
		
		//status specific updates
		if($strMode == constant("HL7_DEFAULT_RESCHEDULE_STATUS_ID")){
			
			//audit for reschedule - not sure whether in use or not - amit
			$vquery_re = "select * from re_schedule where schedule_id=$intApptId";												
			$vsql_re = imw_query($vquery_re);
			$vrs_re=imw_num_rows($vsql_re);	
			
			if($vrs_re == 0){
				$vquery_ins = "  insert into re_schedule set schedule_id=$intApptId ";												
				$vsql_ins = imw_query($vquery_ins);				
			}
			
			$this->response .= "<info>Appointment Rescheduled successfully.</info>";
		}
		if($strMode == constant("HL7_DEFAULT_CHECKIN_STATUS_ID")){
			
			//null
			
			$this->response .= "<info>Appointment Checked In successfully.</info>";
		}
		if($strMode == constant("HL7_DEFAULT_CHECKOUT_STATUS_ID")){
			
			//increasing visit count
			$que_update = " select * from schedule_appointments where id=$intApptId ";												
			$vsq_update = @imw_query($que_update);	
			
			$recs_update=@imw_fetch_array($vsq_update);
			
			$pat_ids=$recs_update['sa_patient_id'];		
					
			for($i=0;$i<=3;$i++){
				$green_id="";
				$vquery_d = "SELECT min(end_date) , reff_id
							 FROM `patient_reff` 
							 WHERE end_date >= '".date('Y-m-d')."' and effective_date <= '".date('Y-m-d')."' and reff_type='$i'
							 and patient_id='$pat_ids'
							 GROUP BY reff_id
							 ORDER BY end_date limit 0,1 ";
				$vsql_d = @imw_query($vquery_d);	
				$vrs_d=@imw_fetch_array($vsql_d);			
				
				$green_id=$vrs_d['reff_id'];
				
				if($green_id==""){
					$vquery_d = "SELECT min(no_of_reffs) , reff_id
								 FROM `patient_reff` 
								 WHERE no_of_reffs > 0 and reff_type='$i'
								 and patient_id='$pat_ids'
								 GROUP BY reff_id
								 ORDER BY no_of_reffs limit 0,1 ";	
					$vsql_d = @imw_query($vquery_d);	
					$vrs_d=@imw_fetch_array($vsql_d);	
					$green_id=$vrs_d['reff_id'];
				}
				if($green_id<>""){
					$reff_ids[]=$green_id;
				}
			}
			if(sizeof($reff_ids)>0){
				$reff_to=@implode(',',$reff_ids);	
			
				$saveq = "update patient_reff set reff_used = reff_used + 1, no_of_reffs = no_of_reffs - 1 ";				
				$saveq.= " where reff_id in($reff_to)";											
				@imw_query($saveq);	
			}
			
			$this->response .= "<info>Appointment Checked Out successfully.</info>";
		}
		if($strMode == constant("HL7_DEFAULT_CANCEL_STATUS_ID")){
			
			//null
			
			$this->response .= "<info>Appointment Cancelled successfully.</info>";
		}
	}
}
?>