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

include_once(dirname(__FILE__).'/amd_base.php');

class amd_patient extends amd_base
{
	public $patient_fields;
	public $insurance_fields;
	public $patient_id;
	private $case_type_id;
	private $case_type_name;
	
	//public $episode_fields;
	//public $custom_fields;
	//public $filter_fields;
	
	public function __construct()
	{
		global $callFromSC;
		
		parent::__construct();
		
		if( !isset($callFromSC) || !$callFromSC)
			$this->ins_default_case_type();
		
		$this->patient_fields = array("@id"=>"[auto]","@updatestatus"=>"[auto]",
																	"@lastname" => "LastName", "@firstname" => "FirstName", "@middlename" => "MiddleName", 
																	"@name" => "Name", "@title" => "Title", "@address1" => "Address1",
																	"@address2" => "Address2", "@zip" => "Zip", "@city"=>"City", "@state"=>"State",
																	"@officephone"=>"OfficePhone", "@homephone"=>"HomePhone",
																	"@email"=>"Email", "@dob"=>"DOB", "@sex"=>"Sex", "@ssn"=>"SSN", "@ethnicity"=>"Ethnicity",
																	"@language"=>"Language", "@races"=>"Races","@respparty"=>"RespParty",
																	"@finclasscode"=>"FinClassCode");
																	
		$this->insurance_fields = array("@id"=>"[auto]", "@effectivestartdate"=>"EffectiveStartDate", 
																		"@effectiveenddate"=>"EffectiveEndDate",
																		"@carrier"=>"Carrier", "@carcode"=>"CarCode", "@carname"=>"CarName",
																		"@caraddress1"=>"CarAddress1", "@caraddress2"=>"CarAddress2",
																		"@carzipcode"=>"CarZipCode", "@carcity"=>"CarCity", "@carstate"=>"CarState",
																		"@carcontactname"=>"CarContactName", 
																		"@carrequirereferral"=>"CarRequireReferral", "@coverage"=>"Coverage", 
																		"@subrelationship"=>"SubRelationship", "@sublastname"=>"SubLastName",
																		"@subfirstname"=>"SubFirstName", "@submiddlename"=>"SubMiddleName",
																		"@subaddress1"=>"SubAddress1", "@subaddress2"=>"SubAddress2", 
																		"@subzipcode"=>"SubZipCode", "@subcity"=>"SubCity",
																		"@substate"=>"SubState", "@subofficephone"=>"SubOfficePhone",
																		"@subhomephone"=>"SubHomePhone", "@otherphone"=>"OtherPhone", 
																		"@subotherphonetype"=>"SubOtherPhoneType",
																		"@subdob"=>"SubDOB", "@subgender"=>"SubGender", "@subssn"=>"SubSSN",
																		"@groupname"=>"GroupName", "@groupnumber"=>"GroupNumber",
																		"@copaydollaramount"=>"CopayDollarAmount",
																		"@copaypercentageamount"=>"CopayPercentageAmount");
		
	}
	
	/**
	*	Funtion returns a element or object that contains
	* a list of all patients that have been modified or added 
	* since the specified date.
	**/
	public function get_updated_patient_list($patient_id = '')
	{
		//"filterlist" => array("filter" => $this->filter_fields)
		$this->parameters = array( 'ppmdmsg' => array("usercontext" => $this->userContext,
																									"@nocookie" => '1',
																									"@action" => "getupdatedpatients",
																									"@class" => "api",
																									"@msgtime" => date('m/d/Y h:i:s A'),
																									"@datechanged" => '',
																									"patient" => $this->patient_fields,
																									"insurance" => $this->insurance_fields));
		
		if( $patient_id )
		{
			if( is_array($patient_id) && count($patient_id) > 0 )
			{
				foreach( $patient_id as $tmp)
				{
					if( !array_key_exists('patientlist',$this->parameters['ppmdmsg']) ) 
						$this->parameters['ppmdmsg']['patientlist']	= array('patient' => array());
					
					$tmp = (int) $tmp;	
					$this->parameters['ppmdmsg']['patientlist']['patient'][] = array('@id' => $tmp); 		
				}
			}
			else
			{
				$patient_id = (int) $patient_id;
				$this->parameters['ppmdmsg']['@patientid'] = $patient_id;
			}
		}
		
		$result = self::CURL($this->appURL, $this->parameters);
		//self::print_resp($result);
		$result = json_decode($result,true);
		
		if( array_key_exists('Error', $result['PPMDResults']) && $result['PPMDResults']['Error']  )
			throw new amdException( 'Server Error', $result['PPMDResults']['Error']['Fault']['detail']['description'] );
		
		if( $result['PPMDResults']['Results']['@patientcount'] > 0 )
			return $result['PPMDResults']['Results'];
		else
			return 'No record found';
	
	}
	
	/**
	*	To handles a patient's updated information retrieved 
	* through Advanced MD API
	**/
	public function patient_info($patient_id = '')
	{
		$patient_info = $this->get_updated_patient_list($patient_id);
		$server_time 	= $patient_info["@servertime"];
		$patient_count= $patient_info["@patientcount"];
		$patient_list = $patient_info['patientlist']['patient'];
		$pid = '';
		if($patient_count > 1)
		{
			foreach( $patient_list as $patient)
			{
				$pid .= ',' .$this->manage_patient_info($patient);
			}
			return substr($pid,1);
		}
		else
		{
			$patient = $patient_list;
			$pid = $this->manage_patient_info($patient);
			return $pid;
		}
	}
	
	public function manage_patient_info($p_data)
	{
		$p_data = $this->trim_arr($p_data);
		$insurance_data = ($p_data['insurancelist']) ? $p_data['insurancelist']['insurance'] : '';
		//unset($p_data['insurancelist']);
		
		$patient_id = $this->add_update_patient($p_data);
		
		//Insurance Data
		if( $insurance_data && $patient_id )
		{
			$this->manage_insurance_info($insurance_data,$patient_id);
		}
		
		return $patient_id;
	}
	
	private function add_update_patient($p_data)
	{
		$tmp_dob = explode('T',$p_data['@dob']);
		$dob = $tmp_dob[0];
		
		$p_data['@lastname'] = addslashes(ucwords(strtolower($p_data['@lastname'])));
		$p_data['@firstname'] = addslashes(ucwords(strtolower($p_data['@firstname'])));
		$p_data['@middlename'] = addslashes(ucwords(strtolower($p_data['@middlename'])));
		
		$validate_patient = array();
		$validate_patient['id'] = $p_data['@id'];
		$validate_patient['lname'] = $p_data['@lastname'];
		$validate_patient['fname'] = $p_data['@firstname'];
		$validate_patient['zip'] = $p_data['@zip'];
		$validate_patient['dob'] = $dob;
		
		$patient_id = $this->chk_patient($validate_patient);
		
		//swap address values if address1 is empty
		if(!$p_data['@address1'] && $p_data['@address2'])
		{
			$p_data['@address1']=$p_data['@address2'];
			$p_data['@address2']='';
		}
		$fields = "amd_patient_id = '".$p_data['@id']."', patient_lname = '".$p_data['@lastname']."', 
							 patient_fname = '".$p_data['@firstname']."', patient_mname = '".$p_data['@middlename']."', 
							 title = '".$p_data['@title']."', street1 = '".$p_data['@address1']."', street2 = '".$p_data['@address2']."',
							 zip = '".$p_data['@zip']."', city = '".$p_data['@city']."', state = '".$p_data['@state']."', 
							 workPhone = '".$p_data['@officephone']."', homePhone = '".$p_data['@homephone']."', 
							 email = '".$p_data['@email']."', date_of_birth = '".$dob."', sex = '".strtolower($p_data['@sex'])."', 
							 ss = '".$p_data['@ssn']."', ethnicity= '".$p_data['@ethnicity']."', language = '".$p_data['@language']."', 
							 race = '".$p_data['@races']."', source = 'AMD' ";
		
		if( $patient_id )
		{
			$pt_qry = "Update patient_data_tbl Set ".$fields." Where patient_id = ".(int) $patient_id."  ";
			$pt_sql = imw_query($pt_qry) ;
			if( imw_error() ){
				$msg = 'Database Error @ Line No. '.__LINE__.': ' .imw_error();
				$this->api_log['message'] .= $msg . "\n";
				return false;
			}
			
		}
		else
		{
			$pt_qry = "Insert Into patient_data_tbl Set ".$fields." ";
			$pt_sql = imw_query($pt_qry);
			if( imw_error() ){
				$msg = 'Database Error @ Line No. '.__LINE__.': ' .imw_error();
				$this->api_log['message'] .= $msg . "\n";
				return false;
			}
			$patient_id = (int) imw_insert_id();
		}
		
		$this->api_log['message'] .= "Get Insurnce Order for Patient ID: ".$p_data['@id']."\n";
		/*Query for Updated Patient Details*/
		try{
			$patientData = $this->getDemographics($p_data['@id']);
			/*Update Data in DB*/
			if( isset($patientData->{'@insorder'}) )
			{
				$sqlUpd = "UPDATE `patient_data_tbl` SET `amd_ins_order`='".$patientData->{'@insorder'}."' WHERE `amd_patient_id`='".$p_data['@id']."'";
				if( imw_query( $sqlUpd ) )
					$this->api_log['message'] .= "Insurance Order saved successfully.\n";
			}
			else
				throw new amdException( 'Patient Data Error', 'Insurance Order Not supplied from Advanced MD' );
		}
		catch( amdException $e )
		{
			$this->api_log['message'] .= "Exception Caught (Insurace Order): ".$e->getErrorType()." - ".$e->getErrorText()."\n";
		}
		
		return $patient_id;		
	}
	
	private function chk_patient($p_data)
	{
		$qry = "Select patient_id From patient_data_tbl Where amd_patient_id = '".(int) $p_data['id']."' ";
		$sql = imw_query($qry) or die('Database Error @ Line No. '.__LINE__.': ' . imw_error() );
		$cnt = imw_num_rows($sql);
		if( $cnt == 0)
		{
			$qry = "select patient_id from patient_data_tbl where 
									patient_fname	= '".$p_data['fname']."'
									and patient_lname 	= '".$p_data['lname']."'
									and date_of_birth 	= '".$p_data['dob']."'
									and zip 			= '".$p_data['zip']."'
									ORDER BY patient_id";
			$sql = imw_query($qry) or die('Database Error @ Line No. '.__LINE__.': ' . $qry . imw_error() );
			$cnt = imw_num_rows($sql);
		}
		
		if( $cnt > 0 )
		{
			$res = imw_fetch_object($sql);
			return $res->patient_id;
		}
		else  return false;
		
		
		
	}
	
	private function manage_insurance_info($insurance_data,$patient_id)
	{
		$tmp_ins =  array();
		if( isset($insurance_data['@id']) )
			array_push($tmp_ins,$insurance_data);
		else
			$tmp_ins = $insurance_data;
		
		if(is_array($tmp_ins) && count($tmp_ins) > 0 )
		{
			foreach($tmp_ins as $insurance )
			{
				$ins_comp = array();
				$ins_comp['@carrier'] = $insurance['@carrier'];
				$ins_comp['@carcode'] = $insurance['@carcode'];
				$ins_comp['@carname'] = $insurance['@carname'];
				$ins_comp['@caraddress1'] = $insurance['@caraddress1'];
				$ins_comp['@caraddress2'] = $insurance['@caraddress2'];
				$ins_comp['@carzipcode'] = $insurance['@carzipcode'];
				$ins_comp['@carcity'] = $insurance['@carcity'];
				$ins_comp['@carstate'] = $insurance['@carstate'];
				$ins_comp['@carcontactname'] = $insurance['@carcontactname'];
				$ins_comp['@carrequirereferral'] = $insurance['@carrequirereferral'];
				
				$ins_comp_name = $this->manage_ins_company($ins_comp);
				if( !$ins_comp_name) 
				{
					$msg = "Error found while updating information of insurance company";
					$this->api_log['message'] .= $msg . "\n";
				}
				include '../../common/conDb.php'; 
				
				$ins_case_id = $this->manage_ins_case($patient_id);
				if( !$ins_case_id )
				{
					$msg = "Error found while updating information of insurance case";
					$this->api_log['message'] .= $msg . "\n";
				}
				
				if( $patient_id && $ins_case_id && $ins_comp_name )
				{
					$ins_type = 'primary';
					if( $insurance['@coverage'] == '2') $ins_type = 'secondary';
					elseif( $insurance['@coverage'] == '3') $ins_type = 'tertiary';
				
					$tmp_sub_dob = explode('T',$insurance['@subdob']);
					$sub_dob = $tmp_sub_dob[0];
				
					$ins_mobile = ($insurance['@subotherphonetype'] == 'CELL') ? "mbl_phone = '".$insurance['@otherphone']."', " : ''  ;
					$copay = ($insurance['@copaydollaramount']) ? $insurance['@copaydollaramount'] : $insurance['@copaypercentageamount'];
					$insurance['@subgender'] = strtolower($insurance['@subgender']);
					$insurance['@subrelationship'] = ucwords(strtolower($insurance['@subrelationship']));
					
					if($insurance['@subrelationship'] == 'Self') $insurance['@subrelationship'] = 'self'; 
					if($insurance['@subrelationship'] == 'Child'){
						if( $insurance['@subgender'] == 'm' ) $insurance['@subrelationship'] = 'Son';
						elseif( $insurance['@subgender'] == 'f' ) $insurance['@subrelationship'] = 'Daughter'; 
					}
					
					if( $insurance['@effectivestartdate'] )
						$insurance['@effectivestartdate'] = date('Y-m-d', strtotime($insurance['@effectivestartdate']));
					if( $insurance['@effectiveenddate'] )	
						$insurance['@effectiveenddate'] = date('Y-m-d', strtotime($insurance['@effectiveenddate']));
					$i_fields = "active_date='".$insurance['@effectivestartdate']."', expiry_Date ='".$insurance['@effectiveenddate']."',
											ins_provider = '".$ins_comp_name."', refer_req = '".$insurance['@carrequirereferral']."',
											type = '".$ins_type."', ins_caseid = ".(int) $ins_case_id.", 
											sub_relation = '".$insurance['@subrelationship']."', lname = '".$insurance['@sublastname']."',
											fname = '".$insurance['@subfirstname']."', mname = '".$insurance['@submiddlename']."',
											address1 = '".$insurance['@subaddress1']."', address2 = '".$insurance['@subaddress2']."',
											zip_code = '".$insurance['@subzipcode']."', city = '".$insurance['@subcity']."', 
											state = '".$insurance['@substate']."', ".$ins_mobile." copay = '".$copay."', 
											work_phone = '".$insurance['@subofficephone']."', home_phone = '".$insurance['@subhomephone']."',  
											dob = '".$sub_dob."', gender = '".$insurance['@subgender']."', 
											ssn = '".$insurance['@subssn']."', plan_name = '".$insurance['@groupname']."', 
											group_name = '".$insurance['@groupnumber']."', patient_id = '".$patient_id."',
											actInsComp = '1' ";
				
					$chk_qry = "select id from insurance_data where  patient_id = '".$patient_id."' AND 
															 ins_caseid='".$ins_case_id."' and type = '".$ins_type."' and actInsComp='1' order by id";
					$chk_sql = imw_query($chk_qry);
					if( imw_error() ){
						$msg = 'Database Error @ Line No. '.__LINE__.': ' .imw_error();
						$this->api_log['message'] .= $msg . "\n";
						return false;
					}
			
					$chk_cnt = imw_num_rows($chk_sql);
					
					if( $chk_cnt > 0 )
					{
						$chk_row = imw_fetch_object($chk_sql);
						$ins_qry = "Update insurance_data Set ".$i_fields." Where id = ".(int) $chk_row->id." ";
						$ins_sql = imw_query($ins_qry) ;
						if( imw_error() ){
							$msg = 'Unable to update insurance information';
							$this->api_log['message'] .= $msg . "\n";
							return false;
						}
					}
					else
					{
						$ins_qry = 	"Insert Into insurance_data set ".$i_fields." ";
						$ins_sql = imw_query($ins_qry);
						if( imw_error() ){
							$msg = "Unable to create insurance information";
							$this->api_log['message'] .= $msg . "\n";	
							return false;
						}
					}
				}
				else
				{
					$msg = "Unable to save insurance information";
					$this->api_log['message'] .= $msg . "\n";	
				}
			}
		}
	}
	
	private function manage_ins_company($ins_comp)
	{
		include '../../connect_imwemr.php';
		$name = str_replace("'", "", $ins_comp['@carname']);
		$qry = "Select * From insurance_companies Where ins_del_status='0' AND name = '".$name."' ";
		$sql = imw_query($qry) ;
		if( imw_error() ){
				$msg = 'Database Error @ Line No. '.__LINE__.': ' .imw_error();
				$this->api_log['message'] .= $msg . "\n";
				return false;
		}
		$cnt = imw_num_rows($sql);
		if( $cnt > 0 ){
			return $name;
		}else{
			
			$address = $ins_comp['@caraddress1'] . ' ' . $ins_comp['@caraddress2'];
			$ins_qry = "Insert into insurance_companies Set in_house_code = '".$ins_comp['@carcode']."', 
												name = '".$name."', contact_address = '".$address."', 
												Zip = '".$ins_comp['@carzipcode']."', City = '".$ins_comp['@carcity']."', 
												State = '".$ins_comp['@carstate']."', contact_name = '".$ins_comp['@carcontactname']."',
												added_date_time = '".date('Y-m-d H:i:s')."' ";
			$ins_sql = imw_query($ins_qry);
			if( imw_error() ){
				$msg = 'Database Error @ Line No. '.__LINE__.': ' .imw_error();
				$this->api_log['message'] .= $msg . "\n";
				return false;
			}
			return $name;
		}
		
		return false;
		
	}
	
	private function manage_ins_case($patient_id)
	{
		$ins_case_id = false;
		
		if( $patient_id )
		{
			$qry = "Select * From iolink_insurance_case Where patient_id='".$patient_id."' And patient_id!=''";
			$sql = imw_query($qry);
			if( imw_error() ){
				$msg = 'Database Error @ Line No. '.__LINE__.': ' .imw_error();
				$this->api_log['message'] .= $msg . "\n";
				return false;
			}
			$cnt = imw_num_rows($sql);
			if( $cnt > 0)
			{
				$row = imw_fetch_object($sql);
				$ins_case_id = $row->ins_caseid;
			}
			else
			{
				if( $this->case_type_id && $this->case_type_name)
				{
					$ins_qry = "Insert Into iolink_insurance_case Set patient_id='".$patient_id."', 
													 ins_case_type ='".$this->case_type_id."', ins_case_name='".$this->case_type_name."',
													 case_name='".$this->case_type_name."', start_date=NOW(), case_status='Open' ";	
					$ins_sql = imw_query($ins_qry);
					if( imw_error() ){
						$msg = 'Database Error @ Line No. '.__LINE__.': ' .imw_error();
						$this->api_log['message'] .= $msg . "\n";
						return false;
					}
					$ins_case_id = imw_insert_id();
				}
			}
		}
		
		return $ins_case_id;
		
	}
	
	private function ins_default_case_type()
	{
		include '../../connect_imwemr.php';
		$defaultCaseTypeQry 	= "SELECT case_id,case_name FROM insurance_case_types WHERE normal = '1'";
		$defaultCaseTypeRes 	= imw_query($defaultCaseTypeQry)or die('Database Error @ Line No. '.__LINE__.': '. imw_error());
		$defaultCaseTypeNumRow 	= imw_num_rows($defaultCaseTypeRes);
		if($defaultCaseTypeNumRow>0) {
			$defaultCaseTypeRow = imw_fetch_array($defaultCaseTypeRes);
			$this->case_type_id 		= $defaultCaseTypeRow['case_id'];
			$this->case_type_name	= $defaultCaseTypeRow['case_name'];
		}
		//$this->unset_constants();
		include '../../common/conDb.php'; 
			
	}
	
	/*Send Charges to Advanced MD*/
	public function saveCharge($patientId, $respParty, $visitId, $visitDate, $visitProvider, $providerIds, $facId, $charges, $insOrder='')
	{
		$patientId = (int)$patientId;
		
		$tempDiagCodeContainer = array();
		$tempProcCodeContainer = array();
		$tempModCodeContainer = array();
		
		$tempProcFeeContainer = array();
		
		//unset($charges['anesthesia'], $charges['surgeon']);
		
		foreach($charges as $key=>&$chargeType){
			foreach($chargeType as &$charge){
				
				/*Map Procedure Codes*/
				if( !isset($tempProcCodeContainer[$charge['@proccode']]) )
				{
					/*Find Code Replacement Value in DB*/
					$sqlCode = "SELECT `amd_id`, `amd_fee`, `amd_allowed`, `amd_units` FROM `amd_codes` WHERE `code_type`='1' AND `code`='".$charge['@proccode']."'";
					$respCode = imw_query($sqlCode);
					if( $respCode && imw_num_rows($respCode)==1)
					{
						$respCode = imw_fetch_assoc($respCode);
						$tempProcCodeContainer[$charge['@proccode']] = $respCode['amd_id'];
						
						$tempProcFeeContainer[$respCode['amd_id']]['fee'] = $respCode['amd_fee'];
						$tempProcFeeContainer[$respCode['amd_id']]['allowed'] = $respCode['amd_allowed'];
						$tempProcFeeContainer[$respCode['amd_id']]['units'] = $respCode['amd_units'];
					}
					else{
						/*Get Data from Advanced MD*/
						$amdProcCode = $this->searchProcCode($charge['@proccode']);
						$code = (is_object($amdProcCode))?$amdProcCode:array_pop($amdProcCode);
						do{
							$sqlAdd = "INSERT INTO `amd_codes` SET `code`='".$code->{'@code'}."', `amd_id`='".$code->{'@id'}."', `code_type`='1', `amd_object`='".json_encode($code)."'";
							imw_query($sqlAdd);
							
							if( $charge['@proccode'] == $code->{'@code'} )
								$tempProcCodeContainer[$charge['@proccode']] = $code->{'@id'};
							
						}while( is_array($amdProcCode) && $code = array_pop($amdProcCode) );
						
						if( !isset($tempProcCodeContainer[$charge['@proccode']]) )
							throw new amdException( 'Data Mapping Error', "No result retuned for Procedure code :".$charge['@proccode']." from Advanced MD. So, unable to map it prgramatically. Charges are not posted to Advanced MD." );
					}
				}
				$charge['@proccode'] = $tempProcCodeContainer[$charge['@proccode']];
				//$charge['@proccode'] = str_replace('pcode', '', $tempProcCodeContainer[$charge['@proccode']]);
				
				/*Add Fee Values for Anesthesia*/
				if( $key == 'anesthesia' )
				{
					if( isset($tempProcFeeContainer[$charge['@proccode']]) )
					{
						$charge['@fee'] = $tempProcFeeContainer[$charge['@proccode']]['fee'];
						$charge['@totalfee'] = $tempProcFeeContainer[$charge['@proccode']]['fee'];
						$charge['@allowed'] = $tempProcFeeContainer[$charge['@proccode']]['allowed'];
						$charge['@units'] = (float)$charge['@units'] + (float)$tempProcFeeContainer[$charge['@proccode']]['units'];
						$charge['@units'] = round($charge['@units'], 1);
					}
					else
					{
						$charge['@fee'] = '0.00';
						$charge['@totalfee'] = '0.00';
						$charge['@allowed'] = '0.00';
						$charge['@units'] = (float)$charge['@units'] + 4;
						$charge['@units'] = round($charge['@units'], 1);
					}
				}
				
				$tempResp = array();
				/*Map Diagnosis Codes*/
				$diagCodes = explode(',', $charge['@diagcodes']);
				foreach($diagCodes as $diagCode)
				{
					$diagCode = trim($diagCode);
					
					if( !isset($tempDiagCodeContainer[$diagCode]) )
					{
						/*Find Code Replacement Value in DB*/
						$sqlCode = "SELECT `amd_id` FROM `amd_codes` WHERE `code_type`='2' AND `code`='".$diagCode."'";
						$respCode = imw_query($sqlCode);
						if( $respCode && imw_num_rows($respCode)==1)
						{
							$respCode = imw_fetch_assoc($respCode);
							$tempDiagCodeContainer[$diagCode] = $respCode['amd_id'];
						}
						else{
							/*Get Data from Advanced MD*/
							$amdDiagCode = $this->searchDiagCode($diagCode);
							$code = (is_object($amdDiagCode))?$amdDiagCode:array_pop($amdDiagCode);
							do{
								$sqlAdd = "INSERT INTO `amd_codes` SET `code`='".$code->{'@code'}."', `amd_id`='".$code->{'@id'}."', `code_type`='2', `amd_object`='".json_encode($code)."'";
								imw_query($sqlAdd);
								
								if( $diagCode == $code->{'@code'} )
									$tempDiagCodeContainer[$diagCode] = $code->{'@id'};
								
							}while( is_array($amdDiagCode) && $code = array_pop($amdDiagCode) );
							
							if( !isset($tempDiagCodeContainer[$diagCode]) )
								throw new amdException( 'Data Mapping Error', "No result retuned for Dx code :".$diagCode." from Advanced MD. So, unable to map it prgramatically. Charges are not posted to Advanced MD." );
						}
					}
					array_push($tempResp, array("@id"=> str_replace('dcode', '', $tempDiagCodeContainer[$diagCode])));
					//array_push($tempResp, array("@id"=> $tempDiagCodeContainer[$diagCode]));
				}
				$charge['diagcodelist']['@codeset'] = "10";
				$charge['diagcodelist']['diagcode'] = $tempResp;
				unset($charge['@diagcodes']);
				
				$tempResp = array();
				/*Map Modifiers Codes*/
				$modfierCodes = explode(' ', $charge['@modcodes']);
				foreach($modfierCodes as $modfierCode)
				{
					$modfierCode = trim($modfierCode);
					
					if( !isset($tempModCodeContainer[$modfierCode]) && $modfierCode!='' )
					{
						/*Find Code Replacement Value in DB*/
						$sqlCode = "SELECT `amd_id` FROM `amd_codes` WHERE `code_type`='3' AND `code`='".$modfierCode."'";
						$respCode = imw_query($sqlCode);
						if( $respCode && imw_num_rows($respCode)==1)
						{
							$respCode = imw_fetch_assoc($respCode);
							$tempModCodeContainer[$modfierCode] = $respCode['amd_id'];
						}
						else{
							/*Get Data from Advanced MD*/
							$amdModCode = $this->searchModCode($modfierCode);
							$code = (is_object($amdModCode))?$amdModCode:array_pop($amdModCode);
							do{
								$sqlAdd = "INSERT INTO `amd_codes` SET `code`='".$code->{'@code'}."', `amd_id`='".$code->{'@id'}."', `code_type`='3', `amd_object`='".json_encode($code)."'";
								imw_query($sqlAdd);
								
								if( $modfierCode == $code->{'@code'} )
									$tempModCodeContainer[$modfierCode] = $code->{'@id'};
								
							}while( is_array($amdModCode) && $code = array_pop($amdModCode) );
							
							if( !isset($tempModCodeContainer[$modfierCode]) )
								throw new amdException( 'Data Mapping Error', "No result retuned for Modifier code :".$modfierCode." from Advanced MD. So, unable to map it prgramatically. Charges are not posted to Advanced MD." );
						}
					}
					array_push($tempResp, $tempModCodeContainer[$modfierCode]);
				}
				$charge['@modcodes'] = implode(' ', $tempResp);
			}
		}
		
		/*GET AMD Facility ID*/
		$sqlFac = "SELECT `amd_id` FROM `amd_facility_codes` WHERE `amd_code`='".$facId."'";
		$respFac = imw_query($sqlFac);
		if( $respFac && imw_num_rows($respFac) > 0 )
		{
			$respFac = imw_fetch_assoc($respFac);
			$facId = $respFac['amd_id'];
		}
		else
		{
			$respFac = trim($this->searchFacId($facId));
			if( $respFac != '' )
			{
				$sqlFac = "INSERT INTO `amd_facility_codes` SET `amd_id`='".$respFac."', `amd_code`='".$facId."'";
				imw_query($sqlFac);
			}
			$facId = $respFac;
		}
		
		foreach($charges as $key=>$chargeList)
		{
			$log_entry_type = ($key=='surgeon') ? 1 :( ($key=='facility') ? 2 : ( ($key=='anesthesia') ? 3 : 0 ) );
			
			if( trim($providerIds[$key]) == '' ){
				if( count($chargeList) > 0 )
				{
					$_SESSION['amd_charge_error'][strtoupper($key)] = "Provider mapping does not exists.";
					
					$sqlLog = "INSERT INTO `amd_charges_log` SET
								`pt_id`='".$patientId."',
								`amd_visit_id`='0',
								`status`='0',
								`reason`='".addslashes($_SESSION['amd_charge_error'][strtoupper($key)])."',
								`date_posted`='".date('Y-m-d H:i:s')."',
								`m_amd_visit_id`='".$visitId."',
								`type`='".$log_entry_type."'";
					imw_query($sqlLog);
				}
				continue;
			}
			
			if( count($chargeList) < 1 ){
				$_SESSION['amd_charge_error'][strtoupper($key)] = "Charges Does not Exists.";
				
				$sqlLog = "INSERT INTO `amd_charges_log` SET
								`pt_id`='".$patientId."',
								`amd_visit_id`='0',
								`status`='0',
								`reason`='".addslashes($_SESSION['amd_charge_error'][strtoupper($key)])."',
								`date_posted`='".date('Y-m-d H:i:s')."',
								`m_amd_visit_id`='".$visitId."',
								`type`='".$log_entry_type."'";
				imw_query($sqlLog);
				
				continue;
			}
			
			/*Create Visit ID for the Professional Charge if Provider is not same as of Visit*/
			if( $key=='surgeon' )
			{
				/*Create New Visit Appointment Provider and Surgeon is different*/
				if( $this->numsOnly($providerIds[$key]) != $this->numsOnly($visitProvider) )
				{
					$callVisitId = $this->addVisit($providerIds[$key], $patientId, $visitDate, $visitId);
				}
				else
				{
					$callVisitId = $visitId;
				}
			}
			else
			{
				$callVisitId = $this->addVisit($providerIds[$key], $patientId, $visitDate, $visitId);
			}
			
			$log_file_name = 'charge_log_'.date('Y_m_d').'.txt';
			
			//if( in_array($key, array('anesthesia', 'surgeon', 'facility')) )
			if( $key == 'anesthesia' )
			{
				foreach($chargeList as &$charge)
				{
					$charge['diagcodelist']['diagcode'] = array_values($charge['diagcodelist']['diagcode']);
					
					if( is_array($charge['diagcodelist']['diagcode']) )
					{
						$charge['@diagcodes'] = array();
						foreach( $charge['diagcodelist']['diagcode'] as $diagCode )
						{
							array_push($charge['@diagcodes'], array_pop($diagCode));
						}
						$charge['@diagcodes'] = implode(' ', $charge['@diagcodes']);
					}
					else
						$charge['@diagcodes'] = implode(' ', $charge['diagcodelist']['diagcode']);
					
					unset($charge['diagcodelist']);
				}
				
				$chargeNote = '';
				if( $key == 'anesthesia' )
				{
					$chargeNote = 'ANESTHESIA BILLING CHARGES '.((int)$chargeList[0]['@duration']).' MINUTES';
				}
				
				$this->parameters = array( 'ppmdmsg' => array(
														"usercontext" => $this->userContext,
														"@nocookie" => '1',
														"@action" => "updvisitwithquickcharges",
														"@class" => "onlinechargeslips",
														"@msgtime" => date('m/d/Y h:i:s A'),
														"@patientid" => (string)$patientId,
															"@approval" => "1",
															"@respparty" => $respParty,
															"@insorder" => (string)$insOrder,
															"@acceptassign" => "1",
														"visit" => array(
																		"@id" => $callVisitId,
																		"@date" => $visitDate,
																		"@force" => "1",
																		"@profile" => $providerIds[$key],
																		"@facility" => $facId,
																		"@note" => $chargeNote,
																		"@insorder" => (string)$insOrder,
																		"chargelist" => array(
																							  "charge"=>$chargeList
																							  )
																		)
														)
									  );
				
				$result = self::CURL($this->appURL, $this->parameters);
				
				/*Log Query Response*/
				$logData =  $key."\n".$visitId.' - '.$callVisitId."\n".$visitDate."\n".json_encode($this->parameters)."\n\n".$result."\n";
				$logData .= "=======================================================\n";
				file_put_contents(dirname(__FILE__).'/data/'.$log_file_name, $logData, FILE_APPEND);
				
				$result = json_decode($result);
				
				//print $key."\n";
				//print_r($this->parameters);
				//print json_encode($this->parameters, JSON_PRETTY_PRINT);
				//print_r($result);
				//print "\n==========\n";
				
				$sqlLog = '';
				if(isset($result->PPMDResults->Error->Fault))
				{
					$_SESSION['amd_charge_error'][strtoupper($key)] = $result->PPMDResults->Error->Fault->detail->description;
					
					$sqlLog = "INSERT INTO `amd_charges_log` SET
								`pt_id`='".$patientId."',
								`amd_visit_id`='".$callVisitId."',
								`status`='0',
								`reason`='".addslashes($_SESSION['amd_charge_error'][strtoupper($key)])."',
								`date_posted`='".date('Y-m-d H:i:s')."',
								`m_amd_visit_id`='".$visitId."',
								`type`='".$log_entry_type."'";
				}
				else
				{
					$chargeid = $result->PPMDResults->Results->{'@chargevalue'};
					$sqlLog = "INSERT INTO `amd_charges_log` SET
								`pt_id`='".$patientId."',
								`amd_visit_id`='".$callVisitId."',
								`status`='1',
								`reason`='".$chargeid."',
								`date_posted`='".date('Y-m-d H:i:s')."',
								`m_amd_visit_id`='".$visitId."',
								`type`='".$log_entry_type."'";
				}
				if( $sqlLog != '' )
					imw_query($sqlLog);
			}
			else
			{	
				if(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'SCC_INDIANA') {

					include "amd_post_charges_scc.php";

				} else {
					/*Currently this block is not being used*/
					$this->parameters = array( 'ppmdmsg' => array(
															"usercontext" => $this->userContext,
															"@nocookie" => '1',
															"@action" => "savecharges",
															"@class" => "onlinechargeslips",
															"@msgtime" => date('m/d/Y h:i:s A'),
															"@patientid" => (string)$patientId,
																"@approval" => "1",
																"@respparty" => $respParty,
															"visit" => array(
																			"@id" => $callVisitId,
																			"@date" => $visitDate,
																			"@force" => "1",
																			"@profile" => $providerIds[$key],
																			"@facility" => $facId,
																			"chargelist" => array(
																								"charge"=>$chargeList
																								)
																			)
															)
										);
					
					$result = self::CURL($this->appURL, $this->parameters);
					
					/*Log Query Response*/
					$logData =  $key."\n".$visitId.' - '.$callVisitId."\n".$visitDate."\n".json_encode($this->parameters)."\n\n".$result."\n";
					$logData .= "=======================================================\n";
					file_put_contents(dirname(__FILE__).'/data/'.$log_file_name, $logData, FILE_APPEND);
					
					$result = json_decode($result);
					
					//print $key."\n";
					//print_r($this->parameters);
					//print json_encode($this->parameters, JSON_PRETTY_PRINT);
					//print_r($result);
					//print "\n==========\n";
					
					$sqlLog = '';
					if(isset($result->PPMDResults->Error->Fault))
					{
						$_SESSION['amd_charge_error'][strtoupper($key)] = $result->PPMDResults->Error->Fault->detail->description;
						
						$sqlLog = "INSERT INTO `amd_charges_log` SET
									`pt_id`='".$patientId."',
									`amd_visit_id`='".$callVisitId."',
									`status`='0',
									`reason`='".addslashes($_SESSION['amd_charge_error'][strtoupper($key)])."',
									`date_posted`='".date('Y-m-d H:i:s')."',
									`m_amd_visit_id`='".$visitId."',
									`type`='".$log_entry_type."'";
					}
					else
					{
						$chargeid = $result->PPMDResults->Results->{'@chargevalue'};
						$sqlLog = "INSERT INTO `amd_charges_log` SET
									`pt_id`='".$patientId."',
									`amd_visit_id`='".$callVisitId."',
									`status`='1',
									`reason`='".$chargeid."',
									`date_posted`='".date('Y-m-d H:i:s')."',
									`m_amd_visit_id`='".$visitId."',
									`type`='".$log_entry_type."'";
					}
					if( $sqlLog != '' )
						imw_query($sqlLog);
			
				}
			}
		}
	}
	
	/*Create New Visit in Advanced MD for posting the charges*/
	private function addVisit($providerId, $patientId, $visitDate, $amdVisitId)
	{
		if( $providerId == '' || $patientId == '' || $visitDate == '' )
			throw new amdException( 'Call Error', "Required Parameter missing for created visit in Advanced MD" );
		
		/*Check if Visit Id already created for the same set of information*/
		$sql = "SELECT `visit_id` FROM `amd_visit_log` WHERE `provider_id`='".$this->numsOnly($providerId)."' AND `pt_id`='".$this->numsOnly($patientId)."' AND `dos`='".$visitDate."' AND `m_visit_id`='".$this->numsOnly($amdVisitId)."'";
		
		$resp = imw_query($sql);
		if( $resp && imw_num_rows($resp) > 0 )
		{
			$respVisitId = imw_fetch_assoc($resp);
			return $respVisitId['visit_id'];
		}
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "addvisit",
													"@class" => "chargeentry",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"appt" => array(
																	"@patient" => $this->numsOnly($patientId),
																	"@profile" => $this->numsOnly($providerId),
																	"@date" => $visitDate
																	)
													)
								  );
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		if(isset($result->PPMDResults->Error))
			throw new amdException( 'API Error', $result->PPMDResults->Error->Fault->detail->description );
		elseif( !isset($result->PPMDResults->Results->visit->{"@id"}) )
			throw new amdException( 'API Error', "Advanced MD has not returned response for ADD Visit API call." );
		
		$visitData = $result->PPMDResults->Results->visit;
		
		/*Save Visit Data in DB*/
		$sqlInsert = "INSERT INTO `amd_visit_log` SET `visit_id`='".$this->numsOnly($visitData->{'@id'})."', `provider_id`='".$this->numsOnly($providerId)."', `pt_id`='".$this->numsOnly($patientId)."', `dos`='".$visitData->{'@date'}."', `m_visit_id`='".$this->numsOnly($amdVisitId)."', `amd_visit_data`='".json_encode($visitData)."'";
		imw_query($sqlInsert);
		
		return $this->numsOnly($visitData->{'@id'});
	}
	
	private function numsOnly($string){
		return preg_replace("/[^0-9,.]/", "", $string);
	}
	
	/*Search Diagnosis code in Advanced MD*/
	public function searchDiagCode($icd10Code=''){
		
		$icd10Code = trim($icd10Code);
		
		if( $icd10Code == '' )
			throw new amdException( 'Call Error', "Blank ICD10 Code Supplied." );
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "lookupdiagcode",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@code" => (string)$icd10Code,
													"@codeset" => "10"
													)
								  );
		
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		if(isset($result->PPMDResults->Error))
			throw new amdException( 'API Error', $result->PPMDResults->Error->Fault->detail->description );
		elseif( !isset($result->PPMDResults->Results->diagcodelist->diagcode) )
			throw new amdException( 'API Error', "No result returned for Diagnosis Code: ".$icd10Code." from Advanced MD." );
		
		return $result->PPMDResults->Results->diagcodelist->diagcode;
	}
	
	/*Search Modifiers Code in Advanced MD*/
	public function searchModCode($modCode=''){
		
		$modCode = trim($modCode);
		
		if( $modCode == '' )
			throw new amdException( 'Call Error', "Blank Modifier Code Supplied." );
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "lookupmodcode",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@code" => (string)$modCode
													)
								  );
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		if(isset($result->PPMDResults->Error))
			throw new amdException( 'API Error', $result->PPMDResults->Error->Fault->detail->description );
		elseif( !isset($result->PPMDResults->Results->modcodelist->modcode) )
			throw new amdException( 'API Error', "No result returned for Modifier Code: ".$modCode." from Advanced MD." );
		
		return $result->PPMDResults->Results->modcodelist->modcode;
	}
	
	/*Search Facility ID in Advanced MD*/
	public function searchFacId($facCode=''){
		
		$facCode = trim($facCode);
		
		if( $facCode == '' )
			throw new amdException( 'Call Error', "Facility Code Not Supplied." );
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "lookupfacility",
													"@class" => "lookup",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@exactmatch" => "0",
													"@code" => $facCode
													)
								  );
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		if(isset($result->PPMDResults->Error))
			throw new amdException( 'API Error', $result->PPMDResults->Error->Fault->detail->description );
		elseif( !isset($result->PPMDResults->Results->facilitylist->facility->{'@id'}) )
			throw new amdException( 'API Error', "Unable to locate Facility Code for: ".$facCode." from Advanced MD." );
		
		return $result->PPMDResults->Results->facilitylist->facility->{'@id'};
	}
	
	/*Search Provider Details in Advanced MD*/
	public function providerDetails($providerName='')
	{
		$providerName = trim($providerName);
		
		if( $providerName == '' )
			throw new amdException( 'Call Error', "Provider Name not supplied." );
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "lookupprofile",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@exactmatch" => "-1",
													"@name" => $providerName,
													"@page" => "1"
													)
								  );
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		if(isset($result->PPMDResults->Error->Fault))
			throw new amdException( 'API Error', $result->PPMDResults->Error->Fault->detail->description );
		elseif( !isset($result->PPMDResults->Results->profilelist->profile) )
			throw new amdException( 'API Error', "Unable to locate Provider : ".$providerName." from Advanced MD." );
		
		return $result->PPMDResults->Results->profilelist->profile;
	}
	
	/* Get Patient Demographic Details
	 * @patientId = AMD Patient ID
	 **/
	public function getDemographics( $patientId )
	{
		$patientId = trim($patientId);
		
		if( $patientId == '' )
			throw new amdException( 'Call Error', "AMD Patient ID not supplied." );
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "getdemographic",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@patientid" => $patientId
													)
								  );
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		if(isset($result->PPMDResults->Error->Fault))
			throw new amdException( 'API Error', $result->PPMDResults->Error->Fault->detail->description );
		elseif( !isset($result->PPMDResults->Results->patientlist->patient->{'@id'}) )
			throw new amdException( 'API Error', "Unable to locate Patient : ".$providerName." Details from Advanced MD." );
		
		return $result->PPMDResults->Results->patientlist->patient;
	}
}