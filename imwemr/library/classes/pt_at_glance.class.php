<?php 
	include_once $GLOBALS['srcdir'].'/classes/common_function.php';
	include_once $GLOBALS['srcdir'].'/classes/work_view/wv_functions.php';
	include_once $GLOBALS['srcdir'].'/classes/work_view/Admn.php';
	include_once $GLOBALS['srcdir'].'/classes/work_view/ChartAP.php';
	include_once $GLOBALS['srcdir'].'/classes/work_view/CLSDrawingData.php';
	include_once $GLOBALS['srcdir'].'/classes/work_view/CLSImageManipulation.php';
	include_once $GLOBALS['srcdir'].'/classes/medical_hx/medical_history.class.php';
	include_once $GLOBALS['srcdir'].'/classes/medical_hx/problem_list.class.php';
	
	$pt_prob_obj = new ProblemLst();
	
	class Pt_at_glance{
		public $patient_id = '';
		public $auth_user = '';
		public $elem_displayAP = 'Active';
		public $elem_dap = 2;
		public $pachy_arr = array();
		public $completedOrderArr = array();
		public $pendingOrderArr = array();
		public $testOrderarr = array();
		
		public $trgtOd = '';
		public $trgtOs = '';
		
		
		public function __construct($pid=0,$auth=0,$request=array()){
			$this->patient_id = $pid;
			$this->auth_user = $auth;
			if(isset($request["elem_dap"]) && empty($request["elem_dap"]) === false){
				$this->elem_dap = $request["elem_dap"];
				switch($request["elem_dap"]){
					case 1:
						$this->elem_displayAP = "All";
					break;
					case 2:
						$this->elem_displayAP = "Active";
					break;
					case 3:
						$this->elem_displayAP = "Resolved";
					break;
					case 4:
						$this->elem_displayAP = "Charted in Error";
					break;
				}
			}
			
			if(isset($request['st']) && empty($request['st']) === false){
				$this->st_index = $request['st'];
			}
			
			if(isset($request['cameFrom']) && empty($request['cameFrom']) === false){
				$this->cameFrom = $request['cameFrom'];
			}
		}
		
		//Returns Pt. data array
		public function get_patient_data($patient_id){
			$sql = "SELECT * FROM patient_data WHERE id = '$patient_id' ";
			$row = sqlQuery($sql);
			if($row != false){
				//Change name format
				$pat_name_arr = array();
				//$pat_name_arr['TITLE'] = $row['title'];
				$pat_name_arr['LAST_NAME'] = $row['lname'];
				$pat_name_arr['FIRST_NAME'] = $row['fname'];
				$pat_name_arr['MIDDLE_NAME'] = $row['mname'];
				$patientName = changeNameFormat($pat_name_arr);
				$patientId = $row['id'];
				$patientNickName = stripslashes($row['nick_name']);
				$patientPhoneticName = stripslashes($row['phonetic_name']);
				$patientDob = (($row["DOB"] != "0000-00-00") && !empty($row["DOB"])) ? get_date_format($row['DOB']) : "";
				$patientAge = $row[''];
				$patientSex = $row['sex'];
				$street = (!empty($row['street2'])) ? $row['street2'].", " : "";
				$patientAddress = $row['street'].", ".$street."".$row['city'].", ".$row['state']." ".$row['postal_code'];
				$patientHomePhone = $row['phone_home'];
				$patientImg = $row['p_imagename'];

				//PatientFacility
				$facilityId=$row['default_facility'];
				if($facilityId != ""){
					$sql="select * from facility where id='$facilityId'";
					$rows =sqlQuery($sql);
					if($rows != false){
						$patientFacility="(".$rows['name'].")";
					}
				}
				
				//External Id --
				if(((empty($row["External_MRN_1"]) == false) || (empty($row["External_MRN_2"]) == false)) && (constant("EXTERNAL_MRN_SEARCH") == "YES")){
					if(empty($row["External_MRN_1"]) == false){
						if(strlen($row["External_MRN_1"]) == 6){
							$ptExtrnId = "0".$row["External_MRN_1"];	
						}
						else{
							$ptExtrnId = $row["External_MRN_1"];
						}
					}
					elseif(empty($row["External_MRN_2"]) == false){
						if(strlen($row["External_MRN_2"]) == 6){
							$ptExtrnId = "0".$row["External_MRN_2"];	
						}
						else{
							$ptExtrnId = $row["External_MRN_2"];
						}
					}
				}
				else{
					$ptExtrnId = $row["ptAthenaID"];
				}
			}
			
			//Insurance case
			$insurance_data = $this->get_insurance_case($this->patient_id);
			if($insurance_data['pt_insurance_case']){
				$patientInsuranceCase = $insurance_data['pt_insurance_case'];
			}

			if($insurance_data['pt_primary_ins']){
				$patientPrimaryIns = $insurance_data['pt_primary_ins'];
			}
			
			//Pt. info string
			$strPtinfo=$patientName ." - ".$patientId;
			if(!empty($ptExtrnId)){  $strPtinfo.=" / ".$ptExtrnId."";  }
			if(!empty($patientDob)){  $strPtinfo.="&nbsp;&nbsp;&nbsp;(DOB: ".$patientDob.")";  }
			
			$return_arr = array();
			$return_arr['patientName'] = $patientName;
			$return_arr['patientNickName'] = $patientNickName;
			$return_arr['patientPhoneticName'] = $patientPhoneticName;
			$return_arr['patientId'] = $patientId;
			$return_arr['patientDob'] = $patientDob;
			$return_arr['patientSex'] = $patientSex;
			$return_arr['patientAddress'] = $patientAddress;
			$return_arr['patientHomePhone'] = $patientHomePhone;
			$return_arr['patientInsuranceCase'] = $patientInsuranceCase;
			$return_arr['patientPrimaryIns'] = $patientPrimaryIns;
			$return_arr['patientImg'] = $patientImg;
			$return_arr['patientFacility'] = $patientFacility;
			$return_arr['ptExtrnId'] = $ptExtrnId;
			$return_arr['strPtinfo'] = $strPtinfo;
			
			// Patient Heard About Us data
			$heardValueArr = !empty($row['heard_abt_us']) ? get_heard_about_list($row['heard_abt_us'],true) : array();
			$heardAboutStr = '';
			if( $heardValueArr[0]['heard_options'] ) $heardAboutStr .= $heardValueArr[0]['heard_options'];
			if( $row['heard_abt_search'] ) $heardAboutStr .= '&nbsp;-&nbsp;'.$row['heard_abt_search'];
			elseif($row['heard_abt_desc'] ) $heardAboutStr .= '&nbsp;-&nbsp;'.$row['heard_abt_desc'];
			
			$return_arr['heard_abt_us'] = $row['heard_abt_us'];
			$return_arr['heard_abt_us_str'] = $heardAboutStr;
			$return_arr['heard_abt_desc'] = $row['heard_abt_desc'];
			$return_arr['heard_about_us_date'] = $row['heard_about_us_date'];
			$return_arr['heard_abt_search'] = $row['heard_abt_search'];
			$return_arr['heard_abt_search_id'] = $row['heard_abt_search_id'];
			
			return $return_arr;
		}
		
		//Returns Pt. ins. data
		public function get_insurance_case($patient_id){
			$insurance_case_id = '';
			$sql = "select ins_caseid from insurance_case where patient_id='".$patientId."' and  case_status='Open' order by ins_caseid DESC LIMIT 0 ,1";
			$row = sqlQuery($sql);
			if($row != false){
				$insurance_case_id = $row["ins_caseid"];
			}
			
			if(empty($insurance_case_id)){
				$pt_insurance_case = "No Insurance Case Opened";
			}
			else if(!empty($insurance_case_id)){
				$pt_insurance_case = get_insurance_case_name($insurance_case_id);
			}
			list($insProvider, $insProviderPracCode, $insProvAddress, $insPhone) = $this->getInsuranceProvider($this->patient_id,$insurance_case_id);
			$pt_primary_ins = (!empty($insProviderPracCode)) ? $insProviderPracCode : substr($insProvider,0,4)."..";
			
			$return_arr = array();
			$return_arr['insProvider'] = $insProvider;
			$return_arr['insProviderPracCode'] = $insProviderPracCode;
			$return_arr['insProvAddress'] = $insProvAddress;
			$return_arr['insPhone'] = $insPhone;
			$return_arr['pt_insurance_case'] = $pt_insurance_case;
			$return_arr['pt_primary_ins'] = $pt_primary_ins;
			return $return_arr;
		}
		
		//Returns provider data
		public function get_provider_data($authUser){
			$return_arr = array();
			$sql="SELECT * FROM users WHERE username='$authUser'";
			$row = sqlQuery($sql);
			if($row != false){
				 //Provider Name
				 $providerName = $row["fname"].' '.$row["lname"].' '.$row["mname"];
				 $facility_id=$row['default_facility'];
				 //Facility Name
				if($facility_id != ""){
					$sql="select * from facility where id='$facility_id'";
					$row =sqlQuery($sql);
					if($row != false){
						$providerFacility= $row['name'];
					}
				}
			}
			$return_arr['providerName'] = $providerName;
			$return_arr['providerFacility'] = $providerFacility;
			return $return_arr;
		}
		
		//Returns Ocular Hx data
		public function get_ocular_hx_data($patient_id){
			$strOcularHx = '';
			$sql = "select * from ocular where patient_id='".$patient_id."' ";
			$row = sqlQuery($sql);
			if($row != false){
				$strAnyConditionsYou = get_set_pat_rel_values_retrive($row["any_conditions_you"],"pat","~|~");
				$arrConYou = explode(",",$strAnyConditionsYou);
				$strOtherDesc = "";
				$strOtherDesc = get_set_pat_rel_values_retrive($row["OtherDesc"],"pat","~|~");
				foreach($arrConYou as $key => $val){
					$strOcularHx .= ($val == 1)? "Dry Eyes<br>" : "";
					$strOcularHx .= ($val == 2)? "Macula Degeneration<br>" : "";
					$strOcularHx .= ($val == 3)? "Glaucoma<br>" : "";
					$strOcularHx .= ($val == 4)? "Retinal Detachment<br>" : "";
					$strOcularHx .= ($val == 5)? "Cataracts<br>" : "";
					$strOcularHx .= ($val == 6)? "Keratoconus<br>" : "";
				}
				$strOcularHx .= ($strOtherDesc != "")? $strOtherDesc."<br>" : "";

				// desc --
				$delimiter = '~|~';
				$strSep="~!!~~";
				$strSep2=":*:";

				$strDesc = get_set_pat_rel_values_retrive($row["chronicDesc"],"pat",$delimiter);
				if(!empty($strDesc))
				{
					$finalStr = '';
					$arrDescTmp = explode($strSep, $strDesc);
					if(count($arrDescTmp) > 0){
						foreach($arrDescTmp as $key => $val){
							$arrTmp = explode($strSep2,$val);
							if($arrTmp[0]=="other"){
								$finalStr = $arrTmp[1];
							}
						}
					}
					$strOcularHx .= ($finalStr != "")? $finalStr."<br>" : "";
				}
				/*
				$strDesc = $finalStr = '';
				$strDesc = get_set_pat_rel_values_retrive($row["chronicDesc"],"rel",$delimiter);
				if(!empty($strDesc))
				{
					$finalStr = '';
					$arrDescTmp = explode($strSep, $strDesc);
					if(count($arrDescTmp) > 0){
						foreach($arrDescTmp as $key => $val){
							$arrTmp = explode($strSep2,$val);
							if($arrTmp[0]=="other"){
								$finalStr = $arrTmp[1];
							}
						}
					}
					$strOcularHx .= ($finalStr != "")? $finalStr."<br>" : "";
				}
				*/
			}
			return $strOcularHx;
		}
		
		//Returns Medical Hx data
		public function get_medical_hx_data($patient_id){
			$strMedicalHx = "";
			$sql = "select * from general_medicine where patient_id='".$patient_id."' ";
			$row = sqlQuery($sql);
			if($row != false)
			{
				$arrConMedHx = explode(",",$row["any_conditions_you"]);
				foreach($arrConMedHx as $key => $val)
				{
					$strMedicalHx .= ($val == 1)? "High Blood Pressure<br>" : "";
					$strMedicalHx .= ($val == 3)? "Diabetes<br>" : "";
					$strMedicalHx .= ($val == 4)? "Lung Problems<br>" : "";
					$strMedicalHx .= ($val == 5)? "Stroke<br>" : "";
					$strMedicalHx .= ($val == 6)? "Thyroid Problems<br>" : "";
				}
				if($row["any_conditions_others"]){
					$strMedicalHxPatientOther=get_set_pat_rel_values_retrive($row["any_conditions_others"],"pat","~|~");
					$strMedicalHxRelationOther=get_set_pat_rel_values_retrive($row["any_conditions_others"],"rel","~|~");
					if(trim($strMedicalHxPatientOther)){$strMedicalHx .='Patient: '.trim($strMedicalHxPatientOther)."<br>";}
					if(trim($strMedicalHxRelationOther)){$strMedicalHx .='Family: '.trim($strMedicalHxRelationOther)."<br>";}
					
				}
			}
			return $strMedicalHx;
		}
		
		//Return test medications
		public function get_test_medications_data($patient_id){
			$sql = "select * from lists where pid='$patient_id' and (type='1' or type='4')
			and allergy_status='Active' ";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				if($row["type"] == "1")
				{
					if(!empty($row["title"]) && (!in_array($row["title"],$arrTmpGen)))
					{
						$strGenMedication .= $row["title"]."<br>";
						$arrTmpGen[] = $row["title"];
					}
				}
				else if($row["type"] == "4")
				{
					if(!empty($row["title"]) && !in_array($row["title"],$arrTmpOcu))
					{
						$strOcuMedication .= $row["title"]."<br>";
						$arrTmpOcu[] = $row["title"];
					}
				}
			}
			$return_arr = array();
			$return_arr['strOcuMedication'] = $strOcuMedication;
			$return_arr['arrTmpOcu'] = $arrTmpOcu;
			$return_arr['strGenMedication'] = $strGenMedication;
			$return_arr['arrTmpGen'] = $arrTmpGen;
			return $return_arr;
		}
		
		//Returns allergies data
		public function get_allergies_data($patient_id){
			$strAllergies = "";
			$strAllergyReaction = "";
			$rez = $this->getAllergies($patient_id, "title, comments",0,"Active");
			for($i=1;$row = sqlFetchArray($rez);$i++)
			{
				if(!empty($row["title"]))
				{
					$strAllergies .= $row["title"]."";
					$strAllergies .= (!empty($row["comments"])) ? " - ".$row["comments"] : "";
					$strAllergies .="<br>";
				}
			}
			return $strAllergies;
		}
		
		//Returns allergies data
		public function getAllergies($patientId, $sel=" * ", $retbool=0, $st=""){
			if($retbool==0){
				$sql = "select ".$sel." from lists where pid='".$patientId."' and type in(3,7) ";
				if(!empty($st)) $sql.=" AND allergy_status = '".$st."' ";
				$rez = sqlStatement($sql);
				return $rez;
			}else{
				$ret="0";
				$sql = "select count(title) as num from lists
						where pid='".$patientId."'
						and type in(3,7)
						and trim(title) != '' AND UCASE(title)!='NKA' AND UCASE(title)!='NKDA'
						and allergy_status != 'Deleted' ";
				$row = sqlQuery($sql);
				//echo $sql;die();
				if($row != false && $row["num"]>0){
					$ret="1";
				}
				return $ret;
			}
		}
		
		//Returns surgeires data
		public function get_surgeries_data($patient_id,$st = 'Active'){
			$ar_sites = array("3"=>"OU", "2"=>"OD", "1"=>"OS");
			$strSurgeries = "";
			$type=" (type='6') ";
			$sql = "select * from lists where pid='".$patient_id."' and ".$type;
			if(!empty($st)) $sql.=" AND allergy_status = '".$st."' ";
			$sql.= " ORDER BY begdate DESC, id DESC ";
			$rez = sqlStatement($sql);
			for($i=1;$row = sqlFetchArray($rez);$i++){
				if(!empty($row["title"])){
					//$surDate = (($row["begdate"] != "0000-00-00") && ($row["begdate"] != "-00-00") &&($row["begdate"] != "0000-00") && !empty($row["begdate"])) ? "(".get_date_format(date("Y-m-d",strtotime($row["begdate"])),'','','2').")": "";
					$surDate = "";
					if( $row["begdate"] != "0000-00-00" ) {
						list($surYear,$surMon,$surDay) = explode("-",$row['begdate']);
						if( (int)$surYear && (int)$surMon && (int)$surDay ){
							$surDate = "(".get_date_format(date("Y-m-d",strtotime($row["begdate"])),'','','2').")";
						}
						else {
							$surDate .= (int)$surMon ? '-'.$surMon : '';
							//$surDate .= (int)$surDay ? '-'.$surDay : '';
							$surDate .= (int)$surYear ? '-'.$surYear : '';
							$surDate = $surDate ? "(".substr($surDate,1).")" : '';
						}
					}
					
					$comments = "".trim($row["comments"]);
					if(!empty($comments)){  $comments=" : ".$comments; }
					if(!empty($row["sites"])){ $site = $ar_sites[$row["sites"]]; if(!empty($site)){ $site=" - ".$site.""; }  }else{ $site=""; }
					$strSurgeries .= $row["title"].$site."".$comments." ".$surDate."<br>";
				}
			}
			return $strSurgeries;
		}
		
		//Returns Site target values
		public function get_target_vals($patientId){
			//$strTrgtVal = $trgtOd = $trgtOs = "";
			list($this->trgtOd, $this->trgtOs) = $this->getIopTrgtVals($patientId);	// iop
			if( empty($this->trgtOd) && empty($this->trgtOs) ){
				list($this->trgtOd, $this->trgtOs) = $this->getGlucomaTargetIop($patientId); // glucoma
			}
			if( empty($this->trgtOd) && empty($this->trgtOs) ){ //def_table
				$row = $this->getIopTrgtDef($patientId);
				$this->trgtOd = $row["iopTrgtOd"];
				$this->trgtOs = $row["iopTrgtOs"];
			}
			return array($this->trgtOd,$this->trgtOs);
		}
		
		//Returns pt. diag comments
		public function get_pt_diag_comm($patient_id){
			$elem_commentsta="";
			$sql = "SELECT comments FROM chart_ptPastDiagnosis WHERE patient_id='".$patient_id."' ";
			$row=sqlQuery($sql);
			if($row != false){
				$elem_commentsta=$row["comments"];
			}
			return $elem_commentsta;
		}
		
		
		//Returns patchy values
		public function set_def_pachy_vals($formId=0){
			$elem_pachy_od_readings=$elem_pachy_od_average=$elem_pachy_od_correction_value="";
			$elem_pachy_os_readings=$elem_pachy_os_average=$elem_pachy_os_correction_value="";

			if(!empty($formId)){
				//
				$sql="SELECT reading_od,avg_od,cor_val_od,reading_os,avg_os,cor_val_os,cor_date
					  FROM chart_correction_values WHERE patient_id='".$this->patient_id."' AND form_id='".$formId."'  ";
				$row=sqlQuery($sql);
				if($row!=false){
					$elem_pachy_od_readings=$row["reading_od"];
					$elem_pachy_od_average=$row["avg_od"];
					$elem_pachy_od_correction_value=trim($row["cor_val_od"]);
					$elem_pachy_os_readings=$row["reading_os"];
					$elem_pachy_os_average=$row["avg_os"];
					$elem_pachy_os_correction_value=trim($row["cor_val_os"]);
					if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = get_date_format($row["cor_date"]); 
				}		

				//Pachy
				if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){			
					$sql = "SELECT pachy_od_readings,pachy_od_average,pachy_od_correction_value,
							pachy_os_readings,pachy_os_average,pachy_os_correction_value,examDate
							FROM pachy WHERE formId='".$formId."' AND patientId='".$this->patient_id."' ";
					$row=sqlQuery($sql);
					if($row!=false){
						$elem_pachy_od_readings=$row["pachy_od_readings"];
						$elem_pachy_od_average=$row["pachy_od_average"];
						$elem_pachy_od_correction_value=trim($row["pachy_od_correction_value"]);

						$elem_pachy_os_readings=$row["pachy_os_readings"];
						$elem_pachy_os_average=$row["pachy_os_average"];
						$elem_pachy_os_correction_value=trim($row["pachy_os_correction_value"]);
						if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = get_date_format($row["examDate"]); 
					}
					//
				}

				//A/scan
				if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
					$sql = "SELECT pachymetryValOD,pachymetryCorrecOD,
							pachymetryValOS,pachymetryCorrecOS,examDate
							FROM surgical_tbl WHERE form_id='".$formId."' AND patient_id='".$this->patient_id."' ";
					$row=sqlQuery($sql);
					if($row!=false){
						$elem_pachy_od_readings=$row["pachymetryValOD"];
						//$elem_pachy_od_average=$row["pachy_od_average"];
						$elem_pachy_od_correction_value=trim($row["pachymetryCorrecOD"]);

						$elem_pachy_os_readings=$row["pachymetryValOS"];
						//$elem_pachy_os_average=$row["pachy_os_average"];
						$elem_pachy_os_correction_value=trim($row["pachymetryCorrecOS"]);
						if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = get_date_format($row["examDate"]); 
					}
				}
			}

			//if Empty , Get Values from glucoma main
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
				
				$elem_activate = "1";

				$sql = "SELECT
						glucomaId,activate,
						datePachy,
						pachyOdReads,pachyOdAvg,pachyOdCorr,
						pachyOsReads,pachyOsAvg,pachyOsCorr,activate
					  FROM glucoma_main 
					  WHERE patientId = '".$this->patient_id."' 
					  AND activate = '1' ";
				
				$row=sqlQuery($sql);
				if($row != false && !empty($row["datePachy"]) && $row["datePachy"] != "0000-00-00" && ($row["pachyOdReads"]!=""||$row["pachyOsReads"]!="")){
						$elem_pachy_od_readings = $row["pachyOdReads"];
						$elem_pachy_od_average = $row["pachyOdAvg"];
						$elem_pachy_od_correction_value = $row["pachyOdCorr"];
						$elem_pachy_os_readings = $row["pachyOsReads"];
						$elem_pachy_os_average = $row["pachyOsAvg"];
						$elem_pachy_os_correction_value = $row["pachyOsCorr"];
						$elem_cor_date =  get_date_format($row["datePachy"],'mm-dd-yyyy');
						$elem_activate = $row["activate"];
						$elem_glucomaId = $row["glucomaId"];
				}	
				
				if($elem_activate!=-1){
					$arrInitialTop = $this->getIntialTop($this->patient_id);
					$lenInitialTop = count($arrInitialTop);
					
					if((empty($elem_pachy_od_readings) && empty($elem_pachy_od_correction_value) &&
						empty($elem_pachy_os_readings) && empty($elem_pachy_os_correction_value)
						) || empty($elem_glucomaId) || empty($arrInitialTop["Pachy"][0]["new"]))
					{			
					
						if(isset($arrInitialTop["Pachy"][0])){
							$elem_cor_date = $arrInitialTop["Pachy"][0]["date"];
							$elem_pachy_od_readings = $arrInitialTop["Pachy"][0]["od"]["Read"];
							$elem_pachy_od_average = $arrInitialTop["Pachy"][0]["od"]["Avg"];
							$elem_pachy_od_correction_value = $arrInitialTop["Pachy"][0]["od"]["Corr"];
							$elem_pachy_os_readings = $arrInitialTop["Pachy"][0]["os"]["Read"];
							$elem_pachy_os_average = $arrInitialTop["Pachy"][0]["os"]["Avg"];
							$elem_pachy_os_correction_value = $arrInitialTop["Pachy"][0]["os"]["Corr"];
						}
					}
				}
			}

			//if Empty , Get Values from previous visit
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
				
				$row = $this->valuesNewRecordsCorrectionValues($this->patient_id);
				if($row != false){
					$elem_pachy_od_readings = $row["reading_od"];
					$elem_pachy_od_average = $row["avg_od"];
					$elem_pachy_od_correction_value = $row["cor_val_od"];
					$elem_pachy_os_readings = $row["reading_os"];
					$elem_pachy_os_average = $row["avg_os"];
					$elem_pachy_os_correction_value = $row["cor_val_os"];
					if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = get_date_format($row["cor_date"]);
				}

			}
			
			//Pachy
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
				
				$sql = "SELECT pachy_od_readings,pachy_od_average,pachy_od_correction_value,
						pachy_os_readings,pachy_os_average,pachy_os_correction_value,examDate
						FROM pachy 
						WHERE patientId='".$this->patient_id."' 
						AND (pachy_od_correction_value!='' OR pachy_os_correction_value!='')
						ORDER BY examDate DESC, pachy_id DESC
						LIMIT 0,1
						";
				$row=sqlQuery($sql);
				if($row!=false){
					$elem_pachy_od_readings=$row["pachy_od_readings"];
					$elem_pachy_od_average=$row["pachy_od_average"];
					$elem_pachy_od_correction_value=trim($row["pachy_od_correction_value"]);
					$elem_pachy_os_readings=$row["pachy_os_readings"];
					$elem_pachy_os_average=$row["pachy_os_average"];
					$elem_pachy_os_correction_value=trim($row["pachy_os_correction_value"]);
					if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = get_date_format($row["examDate"]);
				}
			}
			
			//A/Scan
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
				$sql = "SELECT pachymetryValOD,pachymetryCorrecOD,
						pachymetryValOS,pachymetryCorrecOS,examDate
						FROM surgical_tbl WHERE patient_id='".$this->patient_id."' 
						AND (pachymetryCorrecOD!='' OR pachymetryCorrecOS!='')
						ORDER BY examDate DESC, surgical_id DESC
						LIMIT 0,1
						";
				$row=sqlQuery($sql);
				if($row!=false){
					$elem_pachy_od_readings=$row["pachymetryValOD"];
					$elem_pachy_od_average="";
					$elem_pachy_od_correction_value=trim($row["pachymetryCorrecOD"]);
					$elem_pachy_os_readings=$row["pachymetryValOS"];
					$elem_pachy_os_average="";
					$elem_pachy_os_correction_value=trim($row["pachymetryCorrecOS"]);
					if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = get_date_format($row["examDate"]);
				}
			}
			
			//pt past diag
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
				$sql = "SELECT pachy FROM chart_ptPastDiagnosis WHERE patient_id='".$this->patient_id."' ";
				$row=sqlQuery($sql);
				if($row!=false){
					$pachy = unserialize($row["pachy"]);
					
					$elem_pachy_od_readings=$pachy["od_readings"];
					$elem_pachy_od_average=$pachy["od_average"];
					$elem_pachy_od_correction_value=trim($pachy["od_correction_value"]);
					$elem_pachy_os_readings=$pachy["os_readings"];
					$elem_pachy_os_average=$pachy["os_average"];
					$elem_pachy_os_correction_value=trim($pachy["os_correction_value"]);
					if(!empty($pachy["cor_date"]) && ($pachy["cor_date"] != "0000-00-00") && ($pachy["cor_date"] != "12-31-1969")){ $elem_cor_date = $pachy["cor_date"];}
				}
			}
			$return_arr = array();
			if(empty($elem_pachy_od_readings) === false){
				$return_arr[0] = $elem_pachy_od_readings;
				$this->pachy_arr['elem_od_readings'] = $elem_pachy_od_readings;
			}
			if(empty($elem_pachy_od_average) === false){
				$return_arr[1] = $elem_pachy_od_average;
				$this->pachy_arr['elem_od_average'] = $elem_pachy_od_average;
			}
			if(empty($elem_pachy_od_correction_value) === false){
				$return_arr[2] = $elem_pachy_od_correction_value;
				$this->pachy_arr['elem_od_correction_value'] = $elem_pachy_od_correction_value;
			}
			if(empty($elem_pachy_os_readings) === false){
				$return_arr[3] = $elem_pachy_os_readings;
				$this->pachy_arr['elem_os_readings'] = $elem_pachy_os_readings;
			}
			if(empty($elem_pachy_os_average) === false){
				$return_arr[4] = $elem_pachy_os_average;
				$this->pachy_arr['elem_os_average'] = $elem_pachy_os_average;
			}
			if(empty($elem_pachy_os_correction_value) === false){
				$return_arr[5] = $elem_pachy_os_correction_value;
				$this->pachy_arr['elem_os_correction_value'] = $elem_pachy_os_correction_value;
			}	
			if(empty($elem_cor_date) === false){
				$return_arr[6] = $elem_cor_date;
				$this->pachy_arr['elem_cor_date'] = $elem_cor_date;
			}else{
				$this->pachy_arr['elem_cor_date'] = get_date_format(date("Y-m-d"));
			}
			
			return $return_arr;
		}
		
		
		public function get_print_html(){
			$return_str = '';
			$patient_data = $this->get_patient_data($this->patient_id);
			$pt_active_prob_html = $this->pt_active_prob_list($this->patient_id);
			$pt_active_orders_html = $this->pt_active_order($this->patient_id);
			$pt_active_test_html = $this->pt_active_test($this->patient_id);
			
			//Get Ocular hx data
			$ocular_hx = $this->get_ocular_hx_data($this->patient_id);

			//Get medical hx data
			$medical_hx_data = $this->get_medical_hx_data($this->patient_id);

			//Get test medication data
			$test_medication_data = $this->get_test_medications_data($this->patient_id);

			//Get allergies data
			$allergies_data = $this->get_allergies_data($this->patient_id);

			//Get surgeries data
			$surgeries_data = $this->get_surgeries_data($this->patient_id);

			//Get Site target values
			$site_vals = $this->get_target_vals($this->patient_id);

			//Get pt. diag comments
			$pt_comments = $this->get_pt_diag_comm($this->patient_id);
			
			//Pt. diagnosis data
			$pt_diagnosis_data = $this->get_pt_diagnostic($this->patient_id,'','','','priting');
			
			$title_row = '<tr><td align="center" colspan="3"><strong>Patient at glance</strong></td></tr>
						<tr><td align="center" colspan="3">'.$patient_data['strPtinfo'].'</td></tr>';
			
			$str_MH="";
			$arr_echo= array("Ocular Hx."=>$ocular_hx,"Ocular Medi."=>$test_medication_data['strOcuMedication'],
							"Ocular Surgeries"=>$surgeries_data, "Allergies-Reactions"=>$allergies_data,
							"Medical Hx."=>$medical_hx_data, "General Medi."=>$test_medication_data['strGenMedication']);
			$counter = 1;
			foreach($arr_echo as $key => $val){
				if(!empty($val)){
					$str_MH .= "<td>
							<b>".$key."</b><br/>
							".$val."
						</td>
					";
					$flgMH = 1;
					$counter++;
				}
			}

			if($flgMH == 1){
				$str_medical_history ="
					<table width=\"100%\">
						<tr colspan=\"".$counter."\"><td align=\"center\">Medical History</td></tr>
						<tr valign=\"top\">
						".$str_MH."
						</tr >		
					</table>";
			}
			$comments = '';
			if(empty($pt_comments) === false){
				$comments = '<tr><td><b>Comments:</b></td></tr><tr><td>'.$pt_comments.'</td></tr>';
			}
			
			$return_str .= '<body id="print_body" onload="window.print()"><table>';
				$return_str .= $title_row;
				//Pt. prob list
				$return_str .= '<tr>';
					$return_str .= '<td><table style="width:100%"><tr ><td colspan="3" style="border-bottom:1px solid #000"><b>Active Patient Problem List</b></td></tr>'.$pt_active_prob_html.'</table></td>';
				$return_str .= '</tr>';
				$return_str .= '<tr style="height:15px"></tr>';
				//Pt. Active Orders 
				$return_str .= '<tr>';
					$return_str .= '<td><table style="width:100%"><tr ><td colspan="3" style="border-bottom:1px solid #000"><b>Active Orders</b></td></tr>'.$pt_active_orders_html.'</table></td>';
					$return_str .= '<td></td>';
				$return_str .= '</tr>';
				$return_str .= '<tr style="height:15px"></tr>';
				//Pt. Active Test 
				$return_str .= '<tr>';
					$return_str .= '<td><table style="width:100%"><tr ><td colspan="3" style="border-bottom:1px solid #000"><b>Active Tests</b></td></tr>'.$pt_active_test_html.'</table></td>';
					$return_str .= '<td></td>';
				$return_str .= '</tr>';
				$return_str .= '<tr><td>'.$str_medical_history.'<td></tr>';
				$return_str .= $comments;
			$return_str .= '</table>'.$pt_diagnosis_data.'</body>';
			
			return $return_str;
		}
		
		public function save_chart_comments($request){
			$patientId = $this->patient_id;
			if(isset($request["comments"])){
				$comments = $request["comments"];

				if( !empty($patientId) ){
					$sql = "SELECT * FROM chart_ptPastDiagnosis WHERE patient_id='".$patientId."' ";
					$row = sqlQuery($sql);
					if( $row != false ){
						$sql = "UPDATE chart_ptPastDiagnosis SET comments = '".imw_real_escape_string($comments)."' WHERE patient_id='".$patientId."' ";
						$res = sqlQuery($sql);
					}else{	
						$sql = "INSERT INTO chart_ptPastDiagnosis(ptDiag_id, patient_id, comments) VALUES (NULL, '".$patientId."','".imw_real_escape_string($comments)."')  ";
						$res = sqlQuery($sql);
					}
				}
				echo $comments;
			} else if (isset($request['elem_heardAbtUs'])) {
				
				if( !empty($patientId) ){ 
					$arrElemHeardAbtUs = explode("-",$request['elem_heardAbtUs']);
					$elem_heardAbtUs = addslashes($arrElemHeardAbtUs[0]);
					$elem_heardAbtUsValue = addslashes($arrElemHeardAbtUs[1]);
					$heardAbtOther = addslashes($request['heardAbtOther']);
					$heardAbtDesc = addslashes($request['heardAbtDesc']);
					$heardAbtSearchId = $request['heardAbtSearchId'];
					$heardAbtSearch = addslashes($request['heardAbtSearch']);
					$heardDate = date('Y-m-d');
					
					// Add Heard About us Other field value
					if($heardAbtOther != ''){
						$chkqryHeardMaster = "SELECT heard_id FROM heard_about_us WHERE heard_options = '".$heardAbtOther."' limit 1";
						$rschkqryHeardMaster = imw_query($chkqryHeardMaster);
						if($rschkqryHeardMaster){
							if(imw_num_rows($rschkqryHeardMaster) == 0){
								$priv_query_part = ", for_all=0 ";
								$qryHeard = "Insert into heard_about_us set heard_options = '".$heardAbtOther."'".$priv_query_part;
								$resHeard = imw_query($qryHeard);
								$elem_heardAbtUs = imw_insert_id();
							}
							else{
								$rowChkQryHeardMaster = imw_fetch_array($rschkqryHeardMaster);
								$elem_heardAbtUs = $rowChkQryHeardMaster['heard_id'];		
								imw_free_result($rschkqryHeardMaster);
							}	
						}
					}
					
					// Add heard About us description values for use in typeahead
					if(in_array($elem_heardAbtUsValue,array('Family','Friends','Doctor','Previous Patient.','Previous Patient')) ) {
						$heardAbtDesc = '';
					}
					else {
						$heardAbtSearch = $heardAbtSearchId = ''; 
	
						$query_string = "SELECT id FROM heard_about_us_desc WHERE heard_id = '$elem_heardAbtUs' and heard_desc = '$heardAbtDesc'";
						$sql = imw_query($query_string);
						$heardQryRes = imw_fetch_assoc($sql);
						$heard_id = $heardQryRes['id'];
						if(empty($heard_id) == true){
							$heardDataArr = array();
							$heardDataArr["heard_desc"] = $heardAbtDesc;
							$heardDataArr["heard_id"] = $elem_heardAbtUs;
							//$heardDataArr["timestamp"] = '0000-00-00 00:00:00';

							AddRecords($heardDataArr,"heard_about_us_desc");
						}
					}
					
					// Update Heard About us information in patient_data table
					echo $qry = "Update patient_data set heard_abt_us = '".$elem_heardAbtUs."', heard_abt_desc = '".$heardAbtDesc."', heard_about_us_date = '".$heardDate."', heard_abt_search = '".$heardAbtSearch."', heard_abt_search_id = '".$heardAbtSearchId."' Where id = '".$patientId."'  ";
					
					$sql = imw_query($qry);
					
					
					
					
				}
				
			}else if(isset($request["elem_od_readings"])){
				$arr["od_readings"]=$request["elem_od_readings"];
				$arr["od_average"]=$request["elem_od_average"];
				$arr["od_correction_value"]=$request["elem_od_correction_value"];
				$arr["os_readings"]=$request["elem_os_readings"];
				$arr["os_average"]=$request["elem_os_average"];
				$arr["os_correction_value"]=$request["elem_os_correction_value"];
				$arr["cor_date"]=wv_formatDate($request["elem_cor_date"]);
				$pachy = serialize($arr);
				//$patientId = $_SESSION["patient"];
				
				$sql = "SELECT * FROM chart_ptPastDiagnosis WHERE patient_id='".$patientId."' ";
				$row = sqlQuery($sql);
				if( $row != false ){
					$sql = "UPDATE chart_ptPastDiagnosis SET pachy = '".imw_real_escape_string($pachy)."' WHERE patient_id='".$patientId."' ";
					$res = sqlQuery($sql);
				}else{	
					$sql = "INSERT INTO chart_ptPastDiagnosis(ptDiag_id, patient_id, pachy) VALUES (NULL, '".$patientId."','".imw_real_escape_string($pachy)."')  ";
					$res = sqlQuery($sql);
				}	
				
				$curformId = $this->isChartOpened($patientId);	
				if($curformId != false){
					
					$elem_formId = $curformId;
					$od_readings=$arr["od_readings"];
					$od_average=$arr["od_average"];
					$od_correction_value=$arr["od_correction_value"];
					$os_readings=$arr["os_readings"];
					$os_average=$arr["os_average"];
					$os_correction_value=$arr["os_correction_value"];
					$patientid=$patientId;
					$cor_date=$arr["cor_date"];
					
					//Correction Values --------
					$arr = array("elem_formId" => $elem_formId, "elem_od_readings" => $od_readings,
								"elem_od_average" => $od_average, "elem_od_correction_value" => $od_correction_value,
								"elem_os_readings" => $os_readings, "elem_os_average" => $os_average,
								"elem_os_correction_value" => $os_correction_value, "patientid" => $patientid,
								"elem_cor_date" => $cor_date
								);
					$this->saveCorrectionValues($arr);

					if( !empty($od_correction_value) || !empty($os_correction_value) ){
						//check if Pachy is made and syncronize correction values
						//check
						$sql = "SELECT * FROM pachy WHERE formId='".$elem_formId."' AND patientId='".$patientid."' ";
						$row = sqlQuery($sql);
						if($row != false){
							//Update
							$sql = "UPDATE pachy SET ".
								 "pachy_od_readings = '".$od_readings."', ".
								 "pachy_od_average='".$od_average."', ".
								 "pachy_od_correction_value = '".$od_correction_value."', ".
								 "pachy_os_readings = '".$os_readings."', ".
								 "pachy_os_average = '".$os_average."', ".
								 "pachy_os_correction_value = '".$os_correction_value."' ".
								 "WHERE formId = '".$elem_formId."' AND patientId='".$patientid."' ";
							$row = sqlQuery($sql);
						}
						//check if A/Scan is made and syncronize correction values
						//check
						$sql = "SELECT * FROM surgical_tbl WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' ";
						$row = sqlQuery($sql);
						if($row != false){
							$pachymetryValOD = !empty($od_average) ? $od_average : $od_readings ;
							$pachymetryValOS = !empty($os_average) ? $os_average : $os_readings ;
							
							//Update
							$sql = "UPDATE surgical_tbl SET ".
								 "pachymetryValOD = '".$pachymetryValOD."', ".
								 "pachymetryCorrecOD = '".$od_correction_value."', ".
								 "pachymetryValOS = '".$pachymetryValOS."', ".
								 "pachymetryCorrecOS = '".$os_correction_value."' ".
								 "WHERE form_id = '".$elem_formId."' AND patient_id='".$patientid."' ";
							$row = sqlQuery($sql);
						}
					}
				}
			}
		}
		
		public function save_chart_values($request){
			$patientId = $request["ptId"];
			if(isset($request['save_target'])){
				$elem_trgtIopOd  = (!empty($request["trgtOd"])) ? $request["trgtOd"] : 0 ;
				$elem_trgtIopOs  = (!empty($request["trgtOs"])) ? $request["trgtOs"] : 0 ;
				
				$sumOdIop = "0";
				$sumOsIop = "0";
				
				//Sync. with iop
				$curformId = $this->isChartOpened($patientId);
				if($curformId == false){$curformId = 0;}
				if(!empty($elem_trgtIopOd) || !empty($elem_trgtIopOs)){				
					if($curformId != false){
						//check
						$cQry = "select sumOdIop, sumOsIop FROM chart_iop WHERE form_id='".$curformId."' ";
						$row = sqlQuery($cQry);			
						
						if($row == false){	
							$sumOdIop = "0";
							$sumOsIop = "0";
						}else{
							$sumOdIop = (!empty($row["sumOdIop"])) ? $row["sumOdIop"] : "0" ;
							$sumOsIop = (!empty($row["sumOsIop"])) ? $row["sumOsIop"] : "0" ;
							
							$sumOdIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOd,$sumOdIop);
							$sumOsIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOs,$sumOsIop);
							
							//Update
							$sql = "UPDATE chart_iop SET 
										trgtOd='".imw_real_escape_string($elem_trgtIopOd)."', 
										trgtOs='".imw_real_escape_string($elem_trgtIopOs)."', 
										sumOdIop='".imw_real_escape_string($sumOdIop)."', 
										sumOsIop='".imw_real_escape_string($sumOsIop)."' 
										WHERE form_id='".$curformId."' ";
							$res = sqlQuery($sql);
						}
					}else{
						$curformId = 0;
					}
					//Save
					$this->saveIopTrgt($elem_trgtIopOd,$elem_trgtIopOs,$patientId,$curformId);
				}else{//if empty targt values		
					$this->remIopTrgtDefVal($elem_trgtIopOd,$elem_trgtIopOs,$patientId,$curformId);
				}	
				
				//Sync. With Glucoma	
				if($this->isGlucomaActivated($patientId)){
					//update
					$sql = "UPDATE glucoma_main ".
						 "SET 
							iopTrgtOd='".imw_real_escape_string($elem_trgtIopOd)."', 
							iopTrgtOs='".imw_real_escape_string($elem_trgtIopOs)."' ".
						 "WHERE patientId = '".$patientId."' ".
						 "AND activate = '1' ";
					$res = sqlQuery($sql);
				}
				$return_val =  $elem_trgtIopOd.",".$elem_trgtIopOs.":".$sumOdIop.",".$sumOsIop;
			}
			return $return_val;
		}
		
		public function isGlucomaActivated($pId){
			$sql = "SELECT * ".
				  "FROM glucoma_main ".
				  "WHERE patientId = '".$pId."' ".
				  "AND activate = '1' ";
			$row = sqlQuery($sql);
			if($row != false){
				return true;
			}
			return false;
		}

		public function remIopTrgtDefVal($trgtOd,$trgtOs,$pId,$formId=0){
			$trgtOd = trim($trgtOd);
			$trgtOs = trim($trgtOs);
			if(!empty($pId) && empty($trgtOd) && empty($trgtOs)){
				$flgEmp=1;
				if(!empty($formId)){
					//check in IOP
					$cQry = "select trgtOd, trgtOs FROM chart_iop 
								WHERE form_id='".$formId."' AND patient_id='".$pId."' AND purged='0' ";
					$row = sqlQuery($cQry);
					if($row != false){
						if(trim($row["trgtOd"])!="" || trim($row["trgtOs"])!=""){
							//do not empty def values;
							$flgEmp=0;
						}
					}
				}
				//VF
				$sql="SELECT iopTrgtOd, iopTrgtOs FROM vf WHERE formId = '".$formId."' AND patientId='".$pId."'";
				$row = sqlQuery($sql);
				if($row != false){
					if(trim($row["iopTrgtOd"])!="" || trim($row["iopTrgtOs"])!=""){
						//do not empty def values;
						$flgEmp=0;
					}
				}
				
				//NFA
				$sql="SELECT iopTrgtOd, iopTrgtOs FROM nfa WHERE form_id = '".$formId."' AND patient_id='".$pId."'";
				$row = sqlQuery($sql);
				if($row != false){
					if(trim($row["iopTrgtOd"])!="" || trim($row["iopTrgtOs"])!=""){
						//do not empty def values;
						$flgEmp=0;
					}
				}

				if($flgEmp==1){
					$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='".$formId."' ";
					$res = sqlQuery($sql);
					
					//Update zero
					if(!empty($formId)){
						$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='0' ";
						$res = sqlQuery($sql);
					}
				}
			}
		}
		
		public function saveIopTrgt($trgtOd,$trgtOs,$pId,$formId=0){
			if( !empty($pId) ){
				$row = $this->getIopTrgtDef($pId,$formId,1);
				if($row != false){
					$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='".$formId."' ";
					$res = sqlQuery($sql);
				}else{
					if(!empty($trgtOd) || !empty($trgtOs)){
						$sql = "INSERT INTO tbl_def_val(tbl_def_val_id, iopTrgtOd, iopTrgtOs, ptId, form_id)  ".
							 "VALUES(NULL, '".$trgtOd."', '".$trgtOs."', '".$pId."', '".$formId."' ) ";
						$res = sqlQuery($sql);
					}	 
				}
				
				//Update zero
				if(!empty($formId)){
					$this->saveIopTrgt($trgtOd,$trgtOs,$pId,0);
				}
			}
		}
		
		public function saveCorrectionValues($arr){
			//Check
			$sql = "SELECT cor_id FROM chart_correction_values WHERE form_id = '".$arr["elem_formId"]."' AND patient_id='".$arr["patientid"]."' ";
			$row = sqlQuery($sql);
			if($row != false){
				//Update
				$sql = "UPDATE chart_correction_values SET ".
					 "reading_od = '".$arr["elem_od_readings"]."', ".
					 "avg_od = '".$arr["elem_od_average"]."', ".
					 "cor_val_od = '".$arr["elem_od_correction_value"]."', ".
					 "reading_os = '".$arr["elem_os_readings"]."', ".
					 "avg_os='".$arr["elem_os_average"]."', ".
					 "cor_val_os = '".$arr["elem_os_correction_value"]."', ".
					 "cor_date = '".getDateFormatDB($arr["elem_cor_date"])."', ".
					 "uid='".$_SESSION["authId"]."' ".
					 "WHERE form_id = '".$arr["elem_formId"]."' AND patient_id='".$arr["patientid"]."' ";
				$row = sqlQuery($sql);
			}else{
				if(!empty($arr["elem_od_readings"]) || !empty($arr["elem_od_correction_value"]) ||
					!empty($arr["elem_os_readings"]) || !empty($arr["elem_os_correction_value"]) ){
					//Insert
					$sql= "INSERT INTO chart_correction_values ".
						"(cor_id, patient_id, form_id, cor_date, reading_od, avg_od, cor_val_od, reading_os, avg_os, cor_val_os,uid) ".
						"VALUES ".
						"(NULL, '".$arr["patientid"]."', '".$arr["elem_formId"]."', '".getDateFormatDB($arr["elem_cor_date"])."', '".$arr["elem_od_readings"]."', '".$arr["elem_od_average"]."', ".
						"'".$arr["elem_od_correction_value"]."', '".$arr["elem_os_readings"]."', '".$arr["elem_os_average"]."', '".$arr["elem_os_correction_value"]."','".$_SESSION["authId"]."' ) ";
					$row = sqlQuery($sql);
				}
			}
		}
		
		public function isChartOpened($pid){
			$sql = "SELECT id FROM chart_master_table 
					WHERE patient_id='".$pid."' AND finalize='0' AND delete_status='0'
					ORDER BY id DESC LIMIT 0,1 ";
			$row = sqlQuery($sql);
			if($row != false){
				return $row["id"];
			}
			return false;
		}
		
		public function valuesNewRecordsCorrectionValues($patient_id,$sel=" * ",$LF="0"){
			$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
			$qry = "SELECT ".$sel." FROM chart_master_table ".
				  "INNER JOIN chart_correction_values ON chart_master_table.id = chart_correction_values.form_id ".
				  "WHERE chart_master_table.patient_id = '".$patient_id."' ".
				  "AND chart_master_table.record_validity = '1' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
				  $LF.
				  "ORDER BY chart_master_table.create_dt DESC, chart_master_table.id DESC ".
				  "LIMIT 0,1 ";
			$row = sqlQuery($qry);
			return $row;
		}
		
		
		public function getIopTrgtDef($pId,$formId=0,$strict=0){
			$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='".$formId."' ";
			$row = sqlQuery($sql);
			if( ($row == false) && ($formId != 0) && ($strict == 0) ){
				$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='0' ";
				$row = sqlQuery($sql);
			}
			return $row;
		}
		
		public function getIopTrgtVals($patientId){
			$targetTpOd = $targetTpOs = $targetTaOd = $targetTaOs = "";
			$sql = "SELECT
				  chart_master_table.id,
				  chart_iop.iop_id,
				  chart_iop.puff_trgt_od,
				  chart_iop.puff_trgt_os,
				  chart_iop.app_trgt_od,
				  chart_iop.app_trgt_os,
				  chart_iop.trgtOd,
				  chart_iop.trgtOs,
				  chart_iop.exam_date
				  FROM chart_master_table
				  INNER JOIN chart_iop ON chart_iop.form_id = chart_master_table.id  AND chart_iop.purged = '0'
				  WHERE chart_master_table.patient_id='".$patientId."'
				  AND ((chart_iop.puff_trgt_od != '') OR (chart_iop.puff_trgt_os != '') OR
					  (chart_iop.app_trgt_od != '') OR (chart_iop.app_trgt_os != '') OR
					  (chart_iop.trgtOd != '') OR (chart_iop.trgtOs != '') )
				  ORDER BY chart_master_table.update_date DESC, chart_master_table.id DESC
				  LIMIT 0,1
				";
			$row = sqlQuery($sql);
			if($row != false)
			{
				$targetTpOd = !empty($row["puff_trgt_od"]) ? $row["puff_trgt_od"] :$targetTpOd ;
				$targetTpOs = !empty($row["puff_trgt_os"]) ? $row["puff_trgt_os"] : $targetTpOs;
				$targetTaOd = !empty($row["app_trgt_od"]) ? $row["app_trgt_od"] : $targetTaOd;
				$targetTaOs = !empty($row["app_trgt_os"]) ? $row["app_trgt_os"] : $targetTaOs;
				$this->trgtOd = !empty($row["trgtOd"]) ? $row["trgtOd"] : $targetTaOd;
				$this->trgtOs = !empty($row["trgtOs"]) ? $row["trgtOs"] : $targetTaOs;
			}

			//return array($targetTaOd,$targetTaOs,$targetTpOd,$targetTpOs);
			return array($this->trgtOd,$this->trgtOs);
		}
		
		public function getGlucomaTargetIop($patient_id){
			$tOd=$tOs="";
			$sql = "SELECT iopTrgtOd,iopTrgtOs FROM glucoma_main WHERE patientId='".$patient_id."' ORDER BY glucomaId DESC ";
			$row = sqlQuery($sql);
			if($row != false){
				$tOd = $row["iopTrgtOd"];
				$tOs = $row["iopTrgtOs"];
			}
			return array($tOd,$tOs);
		}
	

		public function get_insurance_case_name($case_id,$flg=""){
			$selqry = imw_query("select *from insurance_case where ins_caseid='".$case_id."'");
			$resarray = imw_fetch_array($selqry);
			$ret_val="";
			if($resarray){
				$selqrtype = imw_query("select *from insurance_case_types  where case_id='".$resarray["ins_case_type"]."'");
				$resarraytype = imw_fetch_array($selqrtype);
				if($resarraytype){
					if($resarraytype["case_name"] == "Workman Comp"){
						$caseName = 'Work Comp';
					}
					else{
						$caseName = $resarraytype["case_name"];
					}

					if($flg=="NoCaseId"){
						$ret_val=$caseName;
					}else{
						$ret_val=$caseName."-".$resarray["ins_caseid"];
					}
				}
			}
			return($ret_val);
		}
		
		public function getInsuranceProvider($patientId,$ins_caseid){
			$sql = "SELECT ".
				   "insurance_companies.name, ".
				   "insurance_companies.in_house_code, ".
				   "insurance_companies.contact_address, ".
				   "insurance_companies.phone ".
				   "FROM insurance_data ".
				   "LEFT JOIN insurance_companies ON insurance_companies.id = insurance_data.provider ".
				   "WHERE pid = '".$patientId."' AND type='primary' AND ins_caseid = '".$ins_caseid."' ";
			$row = sqlQuery($sql);
			if($row != false)
			{
				return array($row["name"],$row["in_house_code"],$row["contact_address"],$row["phone"]);
			}
			return "";
		}
		
		
		public function pt_active_prob_list($patient_id,$idquery="",$limit_records=""){
			global $pt_prob_obj;
			if( !$pt_prob_obj ) $pt_prob_obj = new ProblemLst();
			
			$arrTmp=array();
			$ret="";
			$arrProbList = $pt_prob_obj->get_prob_list_array("", $idquery, "yes","DESC",$limit_records, $patient_id);	
			
			if(count($arrProbList) > 0){
				foreach($arrProbList as $key => $val){
					//Remove  attached  dx codes :  PAG should only show latest unique Active problem list i.e. if a Problem is reviewed multiple time then only show that problem once with latest review date.
					$tmp = $this->remSiteDxFromAssessment($val["problem_name"]);
					if(!$this->in_array_nocase($tmp,$arrTmp)){
						$arrTmp[]=$tmp;			
						$ret.= "<tr>".
							"<td id=\"redBold\" class=\"text-nowrap\">".$val["onset_dates"]."</td>".
							"<td id=\"redBold\" >".trim($val["problem_name"])."</td>".
						"</tr>";				
					}			
				}
			}
			return $ret;
		}
		
		public function pt_active_test($patient_id,$flg_complete='0',$pgp_showpop=0){		
			$objTests				= new Tests;
			$arrActiveTests = array();
			$qry = "SELECT * FROM tests_name WHERE del_status=0";
			$res = imw_query($qry);
			while($row = imw_fetch_assoc($res)){
				$table_name = $row['test_table'];
				$exam_field = $row['exam_date_key'];
				$patient_field = $row['patient_key'];
				$phy_id_field = $row['phy_id_key'];
				$temp_id = $row['id'];
				$test_tbl_pk_id = $row["test_table_pk_id"];
				//
				$phrs = "";
				if($table_name=="test_other"||$table_name=="test_custom_patient"){  $z = !empty($row['test_type']) ? $temp_id : "0"; 	$phrs = "AND test_template_id='".$z."'";	}
				$phrs_active=" AND ($phy_id_field = 0 OR $phy_id_field = '' OR $phy_id_field IS NULL) ";
				if($flg_complete==1||$flg_complete=='true'){ $phrs_active=" AND ($phy_id_field != 0 AND $phy_id_field != '' AND $phy_id_field IS NOT NULL) "; }		
				
				$test_sql = imw_query("SELECT date_format(".$exam_field.",'".get_sql_date_format('','y')."') as examDate, ".$test_tbl_pk_id." as test_id, ".$exam_field." as exm_dt_db  FROM ".$table_name."
												WHERE  $patient_field = '$patient_id' 
														AND purged = '0' 
														AND del_status = '0'
														".$phrs."".
														$phrs_active);
														
				while($test = imw_fetch_array($test_sql)){
					$this_test_images = $objTests->get_test_images($patient_id,$table_name,$test["test_id"],$row["test_type"]);
					$tst_image=$tst_scn_id="";
					if(count($this_test_images) > 0){
						if(isset($this_test_images[0]['fileName']) && isset($this_test_images[0]['scan_id'])){
							$tst_image =  $this_test_images[0]['fileName'] ;
							$tst_scn_id =  $this_test_images[0]['scan_id'] ;
						}
					}					
					$arrActiveTests[$test['exm_dt_db']][] = array($test['examDate'], $tst_image, $test["test_id"], $tst_scn_id,$row['test_name']);
				}							
			}
			krsort($arrActiveTests);
			//--- Display all active tests -------
			foreach($arrActiveTests as $testName=>$arrTest){
				foreach($arrTest as $arr){
				$str .= "
					<tr data-test-id=\"".$arr[2]."\" data-test-scan-id=\"".$arr[3]."\">
						<td class=\"text-nowrap\" id=\"redBold\">".$arr[0]."</td>
						<td class=\"text-nowrap ".($pgp_showpop?'':'clickable itstnm')."\" ".($pgp_showpop?'id="redBold"':'')." >".$arr[4]."</td>
						<td class=\"text-nowrap ".($pgp_showpop?'':'clickable imgnm')."\" ".($pgp_showpop?'id="redBold"':'')." >".$arr[1]."</td>
					</tr>
				";
				}
			}
			return $str;
		}
		
		public function get_chart_procedures($patient_id,$form_id=''){
			$arr_return_table = '';
			if(!$form_id){
					$doc_tr = "<tr class='grythead'><td class='text-nowrap'><strong>Date</strong></td>";
			} else {
				$doc_tr = "<tr>";
			}
			
			$table_header ="	$doc_tr
								<td class='text-nowrap'><strong>Procedure</strong></td>
								<td class='text-nowrap'><strong>Site</strong></td>
								<td class='text-nowrap'><strong>Post Op IOP</strong></td>
								<td class='text-nowrap'><strong>CMT</strong></td>
								<td class='text-nowrap'><strong>Botox </strong></td>
								<td class='text-nowrap'><strong>Comments</strong></td>
							</tr>";
			$qry="     SELECT cp.site as site,op.procedure_name as proc_name,iop_type,if(iop_od,concat('OD:',iop_od),'') as iop_od,if(iop_os,concat('OS:',iop_os),'') as iop_os,
					cmt,comments,DATE_FORMAT(exam_date,'".get_sql_date_format()." %H:%i') as exam_date, exam_date AS exam_date_org,
					btx_usd
					from chart_procedures as cp 
					LEFT JOIN chart_procedures_botox cpb ON cp.id = cpb.chart_proc_id
					inner join operative_procedures as op on(cp.proc_id=op.procedure_id) 
					where cp.patient_id='".$patient_id."' AND deleted_by='0' ";
			
			$tdDOSlbl="<td style=\"width:10%\">Date</td>";
			if($form_id){
				$qry.=" and cp.form_id='".$form_id."'";
			
				$tdDOSlbl="";
			}
			$qry.=" ORDER BY exam_date_org DESC, cp.form_id DESC ";
			
			$res=imw_query($qry) or die(imw_error()."<br>".$qry);
			if(imw_num_rows($res)>0){
				while($row=imw_fetch_assoc($res)){
					$op_proc=$row['proc_name'];
					$site=$row['site'];
					$iop_type=trim($row['iop_type']."&nbsp;".$row['iop_od']."&nbsp;".$row['iop_os']);
					$cmt=trim($row['cmt']);
					$comments_full = $comments=trim($row['comments']);
					if(strlen($comments)>=10){ $comments=substr($comments,0,8).".."; }
					$DOS=trim($row['exam_date']);
					$btox = trim($row['btx_usd']);
					if(!$form_id){
						$tdDOS="<td style=\"width:15%;white-space:nowrap;\">".$DOS."</td>";
					}
					$arr_return_table.= "<tr>".$tdDOS."
										<td>".$op_proc."</td>
										<td>".$site."</td>
										<td>".$iop_type."</td>
										<td>".$cmt."</td>
										<td>".$btox."</td>
										<td class=\"align-top\" title=\"".$comments_full."\">".$comments."</td>
									</tr>";
				}
			}
			if(strlen($arr_return_table) > 0){
				$arr_return_table = $table_header.$arr_return_table;
			}
			return $arr_return_table; 
		}
		
		public function get_pt_image($p_imagename){
			$patientImage = '';
			if(empty($p_imagename) === false){
				$dir_real_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$p_imagename;
				//$dir_real_path = realpath($dirPath);
				$dir_path = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$p_imagename;
				if(file_exists($dir_real_path)){
					$fileSize = getimagesize($dir_real_path);
					if($fileSize[0]>80 || $fileSize[0]>90){
						$imageWidth2 = imageResize($fileSize[0],$fileSize[1],90);
						$patientImage = "<img style=\"vertical-align:baseline\" src=\"".$dir_path."\" alt=\"patient Image\" ".$imageWidth2.">";
					}
					else{
						$patientImage = "<img style=\"vertical-align:baseline\" src=\"".$dir_path."\" alt=\"patient Image\">";
					}
				}
			}
			return $patientImage;
		}
		
		
		public function pt_active_order($patient_id){
			$logged_provider_id = $_SESSION['authId'];	

			//--- GET ALL ORDER SET DETAILS ----
			$orderSetArr = array();
			$order_sql = imw_query("select id,orderset_name from order_sets");
			while($row = imw_fetch_array($order_sql)){
				$id = $row['id'];
				$orderSetArr[$id] = $row['orderset_name'];
			}

			//-- GET ALL ORDERS DETAILS ------
			$ordersNameArr = array();
			$ordersTypeArr = array();
			$order_detail_sql = imw_query("select id,name,o_type from order_details");
			while($row = imw_fetch_array($order_detail_sql)){
				$id = $row['id'];
				$name = $row['name'];
				$ordersNameArr[$id] = $name;
				$ordersTypeArr[$id] = $row['o_type'];
			}
			
			$ordersData = '';
			
			$pat_form_id = $_SESSION['form_id'];

			//--- GET ORDERS/ORDER SET DETAILS ---------
			$order_set_sql = imw_query("select order_set_associate_chart_notes.order_set_associate_id,
					order_set_associate_chart_notes.order_set_id,
					date_format(order_set_associate_chart_notes.created_date, '".get_sql_date_format('','y')."')
					as created_date_show,order_set_associate_chart_notes.form_id,
					date_format(order_set_associate_chart_notes.modified_date, '".get_sql_date_format()."')
					as modified_date , users.lname, users.fname, users.mname
					from order_set_associate_chart_notes
					join users on users.id = order_set_associate_chart_notes.logged_provider_id
					where order_set_associate_chart_notes.patient_id = '$patient_id'
					and order_set_associate_chart_notes.form_id > 0
					and order_set_associate_chart_notes.logged_provider_id = '$logged_provider_id'
					and order_set_associate_chart_notes.delete_status = '0'
					order by created_date_show desc, order_set_associate_chart_notes.order_set_id desc");
			
			while($qryRes = imw_fetch_array($order_set_sql)){
				
				
				//--- Get Active and Pending orders --------
				$form_id = $qryRes['form_id'];
				$order_set_associate_id = $qryRes['order_set_associate_id'];
				$order_set_id = $qryRes['order_set_id'];
				//--- GET ORDERS UNDER SINGLE ORDER SET -----
				$single_odr_set_sql = imw_query("select order_id,orders_status,
						date_format(modified_date ,'".get_sql_date_format()."') as modified_date
						from order_set_associate_chart_notes_details
						where order_set_associate_id = '$order_set_associate_id'
						and delete_status = '0'");
				$order_name_arr = array();
				while($ordersQryRes = imw_fetch_array($single_odr_set_sql)){
					$order_id = $ordersQryRes['order_id'];
					$orders_status = $ordersQryRes['orders_status'];
					//---  ORDER TYPE ------
					$o_type = $ordersTypeArr[$order_id];
					preg_match('/Information/',$o_type,$infCheck);

					if(count($infCheck) == 0){
						$order_name_arr[] = $ordersNameArr[$order_id];
						if($orders_status == 2){
							$opName = strtoupper($qryRes['fname'][0].$qryRes['lname'][0]);
							$completeddate1 = $ordersQryRes['modified_date'].' '.$opName;
							$this->completedOrderArr[$form_id][] = trim($ordersNameArr[$order_id]).' '.$completeddate1;
						}
						else{
							$this->pendingOrderArr[$form_id][] = trim($ordersNameArr[$order_id]);
						}
						$name = preg_replace('/\//','_',$ordersNameArr[$order_id]);
						if(empty($name) == false){
							$this->testOrderarr[] = strtoupper($name);
						}
					}
				}

				$created_date_show = $qryRes['created_date_show'];
				$order_name_str = join(', ',$order_name_arr);
				$orderset_name = ucfirst($orderSetArr[$order_set_id]);

				if(empty($order_name_str) === false){
					$ordersData .= '<tr>
							<td class="text-nowrap" id="redBold">'.$created_date_show.'</td>
							<td class="text-nowrap" id="redBold">'.$orderset_name.'</td>
							<td class="text-nowrap" id="redBold">'.$order_name_str.'</td>
						</tr>';
				}
			}
			return $ordersData;
		}
		
		public function ptDiag_getMemoText($patient_id, $form_id){
			$datamemo="";
			$sql = "SELECT ".
				 "chart_memo_text.memo_text_id, ".
				 "chart_memo_text.memo_text, ".
				 "chart_memo_text.memo_date, ".
				 "chart_memo_text.provider_id ".
				 "FROM memo_tbl ".
				 "INNER JOIN chart_memo_text ON chart_memo_text.memo_id = memo_tbl.memo_id ".
				 "WHERE form_id = '".$form_id."' AND patient_id ='".$patient_id."' and chart_memo_text.deleted_by='0' ";
			$rez = sqlStatement($sql);
			$i=1;
			for(;$row=sqlFetchArray($rez);$i++){
				$memo_text_id = $row['memo_text_id'];
				$memo_text = $row['memo_text'];
				$memo_date = wv_formatDate($row['memo_date']);
				$memo_providerId = $row['provider_id'];
				
				if(!empty($datamemo)) { $datamemo .= "<br/>"; }
				$datamemo .=$memo_text ; 
			}
			
			return $datamemo;

		}
		
		function extractDate($str){
			$srch = "<~ED~>";
			$dt = "";
			$indx = strpos($str,$srch);
			if($indx !== false){
				$dt = str_replace($srch,"",substr($str,$indx));
				$str = substr($str,0,$indx);
			}
			return array(trim($str),trim($dt));
		}
		
		public function getVisInfo($vis_id, $vis_statusElements, $releaseNumber, $dtdos){
			$vision = $visionOD = $visionOS = "";
			$sql = "SELECT sel_od, txt_od, sel_os, txt_os, sec_indx, sec_name  FROM chart_acuity where id_chart_vis_master = '".$vis_id."' AND ((sec_name='Distance' AND sec_indx IN (1,2)) OR (sec_name='Ad. Acuity' AND sec_indx = '3' )) ORDER BY sec_indx ";
			$rez = sqlStatement($sql);
			for($i=1; $row = sqlFetchArray($rez); $i++){
				$cc = $row["sec_indx"];
				$sec_nm = $row["sec_name"];
				
				$fEdit_od = $fEdit_os = true;
				if(($releaseNumber == "1")){
					if( (strpos($vis_statusElements,"elem_visDisOdSel".$cc."=1") !== false) || (strpos($vis_statusElements,"elem_visDisOdTxt".$cc."=1") !== false) 
					  ){
						$fEdit_od = true;
					  }
					  else{
						$fEdit_od = false;
					  }
					  
					 if( (strpos($vis_statusElements,"elem_visDisOsSel".$cc."=1") !== false) || (strpos($vis_statusElements,"elem_visDisOsTxt".$cc."=1") !== false)  	
					  ){
						$fEdit_os = true;
					  }
					  else{
						$fEdit_os = false;
					  }
				}
				
				$visionOD=$visionOS="";
				
				if($fEdit_od == true){  
					$visionOD = ($row["sel_od"] != "") ? $row["sel_od"]." " : "";
					$visionOD .= ($row["txt_od"] != "" && $row["txt_od"] != "20/") ? "(".$row["txt_od"].")" : "";
				}
				if($fEdit_os == true){  	
					$visionOS = ($row["sel_os"] != "") ? $row["sel_os"]." " : "";
					$visionOS .= ($row["txt_os"] != "" && $row["txt_os"] != "20/") ? "(".$row["txt_os"].")" : "";
				}
				
				if(!empty($visionOD) && !empty($visionOS) && $sec_nm=="Ad. Acuity"){
					if(!empty($vision)){ $vision .= "<br/>";  }
					$vision .= $sec_nm."";
				}
				
				if(!empty($visionOD)){
					if(!empty($vision)){ $vision .= "<br/>";  }
					$vision .= "<font color=\"Blue\"><b>OD</b></font>:".$visionOD."";
				}
				if(!empty($visionOS)){	
					if(!empty($vision)){ $vision .= "<br/>";  }
					$vision .= "<font color=\"Green\"><b>OS</b></font>:".$visionOS;					
				}
			}
			
			//------------
			$arrMrDone = array();
			$arrMrDone_give = $arrMrDone_nogive = array();
			$flgElse_nogive=true;
			$visionMr_First="";$vision_nogive="";$vision_give="";			

			$sql = "
				SELECT
				c1.exam_date, c1.mr_none_given,  c1.provider_id, c1.ex_number, c1.ex_desc, 
				c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.txt_1 as txt_1_r, c2.prsm_p AS prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, c2.sel2v as sel2v_r,
				c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.txt_1 as txt_1_l, c3.prsm_p AS prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, c3.sel2v as sel2v_l
				FROM chart_pc_mr c1
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
				WHERE c1.ex_type='MR' AND c1.id_chart_vis_master='".$vis_id."' AND c1.delete_by='0'
				ORDER BY c1.ex_number
			";
			$rez = sqlStatement($sql);
			for($i=1; $row=sqlFetchArray($rez); $i++){
				
				$cc = $row["ex_number"];
				$indx1 = $indx2 = "";
				if($cc>1){
					$indx1 = "Other";
					if($cc>2){
						$indx2 = "_".$cc;
					}
				}	
				
				
				$visionMr = $visionMrOD = $visionMrOS = $mrAdd = "";
				$elem_visMrDesc=$elem_examDateMR=$elem_examDateDistance=$elem_examDateARAK=$elem_examDatePC=$elem_examDateCR="";
				$flagMrDate=true;
				if(!empty($row["ex_desc"]) && ($releaseNumber == "0"))
				{	
					list($elem_visMrDesc,$elem_examDateMR) = $this->extractDate($row["ex_desc"]);
					
					if(!empty($elem_examDateMR))
					{
						$elem_examDateMR = get_date_format($elem_examDateMR);
						$flagMrDate = (!empty($elem_examDateMR) && ($elem_examDateMR >= $dtdos)) ? true : false;// Stoped in R2
					}
				}
				
				
				if( 
					!empty($row["sph_r"]) || !empty($row["cyl_r"]) || !empty($row["axs_r"]) || !empty($row["ad_r"]) || 
					!empty($row["sph_l"]) || !empty($row["cyl_l"]) || !empty($row["axs_l"]) || !empty($row["ad_l"])
				)
				{
					$fEdit_od = $fEdit_os = true;
					if(($releaseNumber == "1")){
						if(((strpos($vis_statusElements,"elem_visMr".$indx1."OdS".$indx2."=1") !== false) || (strpos($vis_statusElements,"elem_visMr".$indx1."OdC".$indx2."=1") !== false) ||
							(strpos($vis_statusElements,"elem_visMr".$indx1."OdA".$indx2."=1") !== false) || (strpos($vis_statusElements,"elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false) ||
							(strpos($vis_statusElements,"elem_visMr".$indx1."OdAdd".$indx2."=1") !== false)
							) 
						){
							$fEdit_od = true;
						}else{
							$fEdit_od = false;
						}
						
						if(((strpos($vis_statusElements,"elem_visMr".$indx1."OsS".$indx2."=1") !== false) || (strpos($vis_statusElements,"elem_visMr".$indx1."OsC".$indx2."=1") !== false) ||
							(strpos($vis_statusElements,"elem_visMr".$indx1."OsA".$indx2."=1") !== false) || (strpos($vis_statusElements,"elem_visMr".$indx1."OsTxt1".$indx2."=1") !== false) ||
							(strpos($vis_statusElements,"elem_visMr".$indx1."OsAdd".$indx2."=1") !== false)	) 
						){
							$fEdit_os = true;
						}else{
							$fEdit_os = false;
						}
					}
					
					if( $fEdit_od == true ){
						$visionMrOD = "";
						$visionMrOD .= "".$row["sph_r"]."";
						$visionMrOD .= " ".$row["cyl_r"]."";
						$visionMrOD .= ((strpos($row["axs_r"],"x") === false) && 
										(strpos($row["axs_r"],"X") === false)) ? " x" : " ";							
						$visionMrOD .= $row["axs_r"]."&#176;";
						if(strlen($row["txt_1_r"])>3){
							$visionMrOD .= " (".$row["txt_1_r"].")";
						}
						$mrAdd = (!empty($row["ad_r"])) ? "".$row["ad_r"]."" : "" ;
					}
					if( $fEdit_os == true ){	
						$visionMrOS = "";
						$visionMrOS .= "".$row["sph_l"]."";
						$visionMrOS .= " ".$row["cyl_l"]."";
						$visionMrOS .= ((strpos($row["axs_l"],"x") === false) && 
										(strpos($row["axs_l"],"X") === false)) ? " x" : " ";
						$visionMrOS .= $row["axs_l"]."&#176;";
						if(strlen($row["txt_1_l"])>3){
							$visionMrOS .= " (".$row["txt_1_l"].")";
						}
						$mrAdd .= (!empty($row["ad_l"])) ? "/".$row["ad_l"]."" : "" ;
						$mrAdd = !empty($mrAdd) ? "<br>ADD ".$mrAdd : "";
					}
				}
				
				$mrGiven = "MR(Given=None)";
				if(( ( (!empty($visionMrOD)) && (trim($visionMrOD) != "x&#176;") ) || 
					( (!empty($visionMrOS)) && (trim($visionMrOS) != "x&#176;") ) ) && 
					($flagMrDate==true) && (strpos($row["mr_none_given"],"MR ".$cc)!==false && strpos($vis_statusElements,"elem_mrNoneGiven".$cc."=1") !== false))
				{
					$visionMr .= "MR ".$cc;
					//$mrGiven = (($row["vis_mr_none_given"] == "MR 1")) ? "MR(Given=MR 1)" : $mrGiven ;
					$mrGiven = (strpos($row["mr_none_given"],"MR ".$cc)!==false && (strpos($vis_statusElements,"elem_mrNoneGiven".$cc."=1") !== false)) ? "MR(Given=MR ".$cc.")," : "" ;
					//$visionMr .= (($row["vis_mr_none_given"] == "MR 1")) ? " - Given<br>" : "<br>" ;							
					$visionMr .= (strpos($row["mr_none_given"], "MR ".$cc)!==false  && (strpos($vis_statusElements,"elem_mrNoneGiven".$cc."=1") !== false)) ? " - Given<br>" : "<br>" ;
					if(( (!empty($visionMrOD)) && (trim($visionMrOD) != "x&#176;") )){$visionMr .= "<font color=\"Blue\"><b>OD</b></font>:".$visionMrOD."<br>";}
					if( (!empty($visionMrOS)) && (trim($visionMrOS) != "x&#176;") ){$visionMr .= "<font color=\"Green\"><b>OS</b></font>:".$visionMrOS;}
					$visionMr .= $mrAdd;
					
					//Glare
					if(!empty($row["sel_2_r"]) || (!empty($row["sel2v_r"]) && trim($row["sel2v_r"])!="20/") || !empty($row["sel_2_l"]) || (!empty($row["sel2v_l"]) && trim($row["sel2v_l"])!="20/")){
						$visionMr.="<br>Glare<br>";					$visionOD=$visionOS="";
						if(( (!empty($visionMrOD)) && (trim($visionMrOD) != "x&#176;") )){
							$visionOD=($row["sel_2_r"]!= "") ? "".$row["sel_2_r"]." " : "";
							$visionOD.=($row["sel2v_r"]!= "") ? "".$row["sel2v_r"]." " : "";
							$visionOD = "<font color=\"Blue\"><b>OD</b></font>:".$visionOD."<br/>";
						}
						
						if(( (!empty($visionMrOS)) && (trim($visionMrOS) != "x&#176;") )){
							$visionOS=($row["sel_2_l"]!= "") ? "".$row["sel_2_l"]." " : "";
							$visionOS.=($row["sel2v_l"]!= "") ? "".$row["sel2v_l"]." " : "";
							$visionOS = "<font color=\"Green\"><b>OS</b></font>:".$visionOS;
						}
						$visionMr.="".$visionOD."".$visionOS;
					}
					
					//Prism
					if($row["prsm_p_r"] || $row["prsm_p_l"] || $row["slash_r"] || $row["slash_l"] ){
						$visionMr.="<br>Prism<br>";					$visionOD=$visionOS="";
						if(( (!empty($visionMrOD)) && (trim($visionMrOD) != "x&#176;") )){
							$visionOD=($row["prsm_p_r"]!= "") ? " P&nbsp;".$row["prsm_p_r"]." " : "";
							$visionOD.=($row["sel_1_r"]!= "") ? "&#9650; ".$row["sel_1_r"]: "";
							$visionOD.=($row["slash_r"]!= "") ? "  / ".$row["slash_r"]: "";
							$visionOD.=($row["prism_r"]!= "") ? " ".$row["prism_r"]: "";
							if($row["prsm_p_r"] || $row["slash_r"] ){
								$visionOD = "<font color=\"Blue\"><b>OD</b></font>:".$visionOD;
							}else{ $visionOD = ""; }
						}
						if( (!empty($visionMrOS)) && (trim($visionMrOS) != "x&#176;") ){
							$visionOS=($row["prsm_p_l"]!= "") ? " P&nbsp;".$row["prsm_p_l"]." " : "";
							$visionOS.=($row["sel_1_l"]!= "") ? "&#9650; ".$row["sel_1_l"]: "";
							$visionOS.=($row["slash_l"]!= "") ? "  / ".$row["slash_l"]: "";
							$visionOS.=($row["prism_l"]!= "") ? " ".$row["prism_l"]: "";
							if($row["prsm_p_l"] || $row["slash_l"] ){							
								$visionOS = "<font color=\"Green\"><b>OS</b></font>:".$visionOS;
								if(!empty($visionOD)) { $visionOS = "<br/>".$visionOS; }
							}else{$visionOS = ""; }						
						}
						$visionMr.="".$visionOD."".$visionOS;						
					}
				}
				
				//MR first
				if($cc == 1){
					$visionMr_First = $visionMr;
				}
				
				//Show Given Mr				
				if( !empty($mrGiven) && ($mrGiven != "MR(Given=None)") ){ //mr is given
					if(strpos($mrGiven, "MR(Given=MR ".$cc.")")!==false){
						if(!empty($visionMr)){
							$vision_give .= (!empty($vision_give)) ? "<br>".$visionMr : $visionMr ;
							$arrMrDone_give[]="MR ".$cc;
						}
					}
				}else{					
					//check dr. mr else mr 1
					if(!empty($visionMr)){
						$vision_nogive .= (!empty($vision_nogive)) ? "<br>".$visionMr : $visionMr ;
						$flgElse_nogive=false;
						$arrMrDone_nogive[]="MR ".$cc;
					}
				}				
			}
			
			//if no given is done: give first Mr 1 done
			if(empty($vision_give) && $flgElse==true){
				if(!empty($visionMr_First)){
					$vision_nogive .= (!empty($vision_nogive)) ? "<br>".$visionMr_First : $visionMr_First ;
					$arrMrDone_nogive[]="MR 1";
				}
			}
			
			if(!empty($vision_give)){
				if(!empty($vision)){ $vision .= "<br>"; }
				$vision .= $vision_give;
				$arrMrDone = $arrMrDone_give;
			}else if(!empty($vision_nogive)){
				if(!empty($vision)){ $vision .= "<br>"; }
				$vision .= $vision_nogive;
				$arrMrDone = $arrMrDone_nogive;
			}
			
			return array($vision, $arrMrDone);
		}
		
		//Return Pt. diagnostic html
		public function get_pt_diagnostic($patient_id,$pdg_showpop=0,$pdg_hiderow1=0,$st_idnx="",$callFrom="", $appView = false){
			$return_str = '';
			$lmt_cn_pag = $this->usrChartLimit_Pag(1);
			$this->el_shw_rec = $lmt_cn_pag;
			//Get pt. pagging results
			list($this->str_dig_info, $this->paging_links) = $this->getPatientDiagnosis_info($patient_id, $lmt_cn_pag, $st_idnx,$pdg_showpop);
			
			//Get Pt Diagnosis
			$rezPatientDiagnosis = $this->getPatientDiagnosis($patient_id, $lmt_cn_pag, $st_idnx);
			if($pdg_showpop==1) {
				if($provider_id == '')
				$provider_id = $_SESSION["authId"];
				//list($this->completedOrderArr, $this->pendingOrderArr) = getOrderSetInfo($patient_id,$provider_id);
				//This func. creates completedOrderArr,pendingOrderArr arrays
				$this->pt_active_order($this->patient_id);
				//Get Site target values
				$site_vals = $this->get_target_vals($this->patient_id);
				//Get pachy values
				$arrPachy = $this->set_def_pachy_vals();
			}
			
			$display_inline = 'inline-block';
			$display_none = 'none';
			$display_visible = 'visible';
			$display_hidden = 'hidden';
			
			$return_str .= '<table class="table table-bordered table-striped table-hover" border="1">';
				$return_str .='<tr>';
				$target_div = '';
				if($pdg_showpop!=1 || !empty($this->trgtOd) || !empty($this->trgtOs)){
					$target_value_od = '<input type="text" size="4" class="form-control" name="elem_trgtOd" id="elem_trgtOd" value="'.$this->trgtOd.'" onChange="saveTrgt()">';
						
					$target_value_os = '<input type="text" size="4" class="form-control" name="elem_trgtOs" id="elem_trgtOs" value="'.$this->trgtOs.'" onChange="saveTrgt()" >';
				
					$target_div = '
						<input type="hidden" name="elem_ptId" id="elem_ptId" value="'.$this->patient_id.'">
						<div class="col-sm-3">
							<div class="row">
								<div class="col-sm-1">
									<a href="javascript:void(0)">
										<img src="'.$GLOBALS["webroot"].'/library/images/flow_sheet.png" onClick="IOP_showGraphsAm();" style="max-width:inherit;height:inherit"/>
									</a>	
								</div>
								<div class="col-sm-2 text-right">
									<label style="vertical-align:sub">Trgt :</label>	
								</div>
								<div class="col-sm-9">
									<div class="row">
										<div class="col-sm-6">
											<div class="input-group">
												<div class="input-group-addon">
													<span class="od"><strong>OD</strong></span>	
												</div>
												'.$target_value_od.'	
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-group">
												<div class="input-group-addon">
													<span class="os"><strong>OS</strong></span>
												</div>
												'.$target_value_os.'	
											</div>
										</div>	
									</div>	
								</div>	
							</div>
						</div>';
				}
				
				$pachy_div = '';
				if($pdg_showpop!=1 || !empty($this->pachy_arr['elem_od_readings']) || !empty($this->pachy_arr['elem_os_readings'])){
					//OD
					if($this->pachy_arr['elem_od_correction_value'] == 'undefined'){
						$this->pachy_arr['elem_od_correction_value'] = '';
					}
					
					if($this->pachy_arr['elem_od_average'] == 'undefined'){
						$this->pachy_arr['elem_od_average'] = '';
					}
					
					if($this->pachy_arr['elem_os_correction_value'] == 'undefined'){
						$this->pachy_arr['elem_os_correction_value'] = '';
					}
					
					if($this->pachy_arr['elem_os_average'] == 'undefined'){
						$this->pachy_arr['elem_os_average'] = '';
					}	
					
					//if(!empty($this->pachy_arr['elem_od_average']) && $this->pachy_arr['elem_od_average'] != 'undefined'){
						$pachy_od_avg = '<div class="col-sm-3"><input type="text" name="elem_od_average" value="'.$this->pachy_arr['elem_od_average'].'" readonly="readonly" class="form-control" style="display:none"></div>';
				//	}
					
					//if(!empty($this->pachy_arr['elem_od_readings']) || !empty($this->pachy_arr['elem_od_correction_value'])){
						$pachy_od_correction_val = '<div class="col-sm-3"><input type="text" name="elem_od_correction_value" value="'.$this->pachy_arr['elem_od_correction_value'].'" readonly="readonly" class="form-control"></div>';
				//	}
					
					
					//OS
					//if(!empty($this->pachy_arr['elem_os_average']) && $this->pachy_arr['elem_os_average'] != 'undefined'){
						$pachy_os_avg = '<div class="col-sm-3"><input type="text" name="elem_os_average" value="'.$this->pachy_arr['elem_os_average'].'"  readonly="readonly" class="form-control" style="display:none"></div>';
					//}
					
					//if(!empty($this->pachy_arr['elem_os_readings']) || !empty($this->pachy_arr['elem_os_correction_value'])){
						$pachy_os_correction_val = '<div class="col-sm-3"><input type="text" name="elem_os_correction_value" value="'.$this->pachy_arr['elem_os_correction_value'].'" readonly="readonly" class="form-control"></div>';
					//}
					
					$pachy_div = '
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-2 text-right">
									<label style="vertical-align:sub">Pachy :</label>	
								</div>
								<div class="col-sm-10">
									<div class="row">
										<div class="col-sm-6">
											<div class="row">
												<div class="col-sm-6">
													<div class="input-group">
														<div class="input-group-addon">
															<span class="od"><strong>OD</strong></span>	
														</div>
														<input type="text" name="elem_od_readings" value="'.$this->pachy_arr['elem_od_readings'].'" onChange="calCorrectionVal(this.value,\'OD\');" class="form-control">
													</div>	
												</div>	
												'.$pachy_od_avg.'	
												'.$pachy_od_correction_val.'	
											</div>
										</div>
										<div class="col-sm-6">
											<div class="row">
												<div class="col-sm-6">
													<div class="input-group">
														<div class="input-group-addon">
															<span class="os"><strong>OS</strong></span>	
														</div>
														<input type="text" name="elem_os_readings" value="'.$this->pachy_arr['elem_os_readings'].'" onChange="calCorrectionVal(this.value,\'OS\');" class="form-control">
													</div>	
												</div>	
												'.$pachy_os_avg.'	
												'.$pachy_os_correction_val.'	
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>';
				}
				
				$exam_display_opt_arr = array('1'=>'All','2'=>'Active','3'=>'Resolved','4'=>'Charted in Error');
				$exam_display_opt= '';
				foreach($exam_display_opt_arr as $key => $val){
					$selected = '';
					if($key == $this->elem_dap){
						$selected = 'selected';
					}
					$exam_display_opt .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
				}
				
				//Table Header
				if(empty($callFrom)){
					
					$elem_cor_date_tmp = "";
					if(!empty($this->pachy_arr['elem_cor_date']) && $this->pachy_arr['elem_cor_date']!='12-31-1969' && $this->pachy_arr['elem_cor_date']!='12/31/1969'){
						$tmp = strtotime(str_replace('-', '/', $this->pachy_arr['elem_cor_date']));
						if(!empty($tmp)){
							$elem_cor_date_tmp = date(phpDateFormat(), $tmp);
						}
					}
					
					$return_str .='<td colspan="8">
							<div class="row">
								'.$target_div.'
								'.$pachy_div.'
								<div class="col-sm-2 form-inline">
									<label>Date:</label>
									<div class="input-group">
										<input type="text" name="elem_cor_date" id="elem_cor_date" value="'.$elem_cor_date_tmp.'" class="datepicker form-control" onChange="saveCorrectVals()" size="12">	
										<label for="elem_cor_date" class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>	
										</label>
									</div>	
								</div>
								<div class="col-sm-1 text-nowrap">
									<label>'.$this->ptDiag_getLastDilated($this->patient_id).'</label>
								</div>
								<div class="col-sm-2">
									<div class="row text-right">
										<div class="col-sm-6">
											<label>EXAMS&nbsp;Display:</label>	
										</div>
										<div class="col-sm-6">
											<select name="elem_displayAP" class="form-control minimal" onChange="changeApDis(this.value);">
												<option value=""></option>
												'.$exam_display_opt.'
											</select>	
										</div>	
									</div>	
								</div>	
							</div>
						</td>';
				}else{
					$span = '';
					if( $callFrom == 'popup') $span = '<span class="pull-left" style="margin-right:35px;"><strong>Exam Display:</strong>:Active</span>';
					
					$return_str .= '<td colspan="3">'.$span.'<b>Trgt:&nbsp;OD:</b> '.$this->trgtOd.' &nbsp;&nbsp;&nbsp;<b>OS:</b> '.$this->trgtOs.'</td>';
					
					if(!empty($this->pachy_arr['elem_od_readings'])){
						$od_str_values .= $this->pachy_arr['elem_od_readings'].'&nbsp;';
					}
					if(!empty($this->pachy_arr['elem_od_average'])){
						$od_str_values .= $this->pachy_arr['elem_od_average'].'&nbsp;';
					}
					if(!empty($this->pachy_arr['elem_od_correction_value'])){
						$od_str_values .= $this->pachy_arr['elem_od_correction_value'].'&nbsp;';
					}
					
					if(!empty($this->pachy_arr['elem_os_readings'])){
						$os_str_values .= $this->pachy_arr['elem_os_readings'].'&nbsp;';
					}
					if(!empty($this->pachy_arr['elem_os_average'])){
						$os_str_values .= $this->pachy_arr['elem_os_average'].'&nbsp;';
					}
					if(!empty($this->pachy_arr['elem_os_correction_value'])){
						$os_str_values .= $this->pachy_arr['elem_os_correction_value'].'&nbsp;';
					}
					$return_str .= '<td colspan="3"><b>Pachy:&nbsp;OD:</b> '.$od_str_values.' &nbsp;&nbsp;&nbsp;<b>OS:</b> '.$os_str_values.'</td>';
					$return_str .= '<td>Date : '.get_date_format($this->pachy_arr['elem_cor_date'],inter_date_format()).'</td>';
					$return_str .= '<td>'.$this->ptDiag_getLastDilated($this->patient_id).'</td>';
				}
				
				$return_str .='</tr></tabel>';
				$vis_graph = ($pdg_showpop!=1) ? '<img src="'.$GLOBALS["webroot"].'/library/images/flow_sheet.png" onClick="show_vis_graphs(0)" class="pull-right clickable" style="max-width:inherit;height:inherit"/>' : '' ;
				$return_str .= '<tabel><tr class="purple_bar">';
					$return_str .= '<td style="width:10%;"><strong>Date</strong></td>';
					$return_str .= '<td style="width:10%;"><strong>Vision</strong>'.$vis_graph.'</td>';
					$return_str .= '<td style="width:10%;" class="text-center">
						<table class="col-sm-12">
							<tr>
								<td ><strong>IOP</strong></td>
								<td class="od"><strong>OD</strong></td>
								<td class="os"><strong>OS</strong></td>
							</tr>	
						</table>
					</td>';
					$return_str .= '<td style="width:3%;"><strong>C:D</strong></td>';	
					$return_str .= '<td style="width:3%;"><strong>C/L</strong></td>';	
					$return_str .= '<td style="width:25%;"><strong>Assessment</strong></td>';	
					$return_str .= '<td style="width:20%;"><strong>Plan</strong></td>';	
					$return_str .= '<td style="width:29%;"><strong>Rx</strong></td>';	
				$return_str .= '</tr>';
				$patient_id = $this->patient_id;
				if(imw_num_rows($rezPatientDiagnosis) > 0){
					$prevDos = NULL;
					$arrValues=array();
					$flgAsm = $flgPln = $flgOrdr = 0;
					for($i=0;$row=sqlFetchArray($rezPatientDiagnosis);$i++){						
						$formID = $row["formID"];
						$releaseNumber = $row["releaseNumber"];
						$date = "";
						$dbDos=$row["date_of_service"];
						$date2 = get_date_format($dbDos);
						
						$typeChart = (!empty($row["memo"])) ? "Memo Chart Note" : "Chart Note";
						$chartStatus = "Final" ; 
						
						//App View
						if($appView === false || $appView === true && $callFrom == 'popup'){
							$call_func = "pag_showDos";
							if($appView === true && $callFrom == 'popup'){$call_func = "showFinalize";}
							$date = "<a href=\"javascript:void(0);\" style=\"color:purple;\" onclick=\"".$call_func."('".$typeChart."','".$formID."','".$chartStatus."','".$releaseNumber."')\" >".$row["date_of_service2"]."</a>";
						}else{
							$date = "<span>".$row["date_of_service2"]."</span>";
						}
						
						
						//$date = $row["date_of_service2"];
						//$date .= ($row["ptVisit"] == "CEE") ? "<br>CEE" : "";
						$date_suff="";
						$date_suff .= ($row["ptVisit"] != "") ? "".$row["ptVisit"] : "";
						$memo = $row["memo"];
						if($memo=="1"){$memo_text = $this->ptDiag_getMemoText($patient_id,$formID);}else{$memo_text = "" ;}
						$chartfacilityid = $row["facilityid"];
						
						//Chart Note Server Abbr.---			
						$serverAbbr="";
						if(constant("REMOTE_SYNC") == 1){
							$serverId = $row["serverId"]; //serverId
							if(!empty($arrRemoteServerAbbr[$serverId])){	
								if(!empty($date_suff)){  $date_suff .= "-"; }
								$date_suff .= " ".$arrRemoteServerAbbr[$serverId]."";	
							}
						}else if(!empty($chartfacilityid)){ //facility id
							
							$chartfacilityid_sc =  $this->getChartFacilityFromSchApp($patient_id, $dbDos);	
							if(!empty($chartfacilityid_sc)){$chartfacilityid=$chartfacilityid_sc;}
							$serverAbbr = $this->getFacilityAbbr($chartfacilityid);
							if(!empty($serverAbbr)){ 
								if(!empty($date_suff)){  $date_suff .= "-"; }						
								$date_suff .= " ".$serverAbbr."";
							}
						
						}
						//Chart Note Server Abbr.---
						
						
						
						if(!empty($date_suff)){
							$date .= "<br/>".$date_suff;
						}				
						
						///Asssessment and plans -------------------------
						//Xml file
						$assessment = $plan = $strAp= "";
						$strXml = stripslashes($row["assess_plan"]);
						$chartApXml = new ChartAP($patient_id,$formID);
						//$arrTmp = $chartApXml->getVal_Str($strXml);
						$arrTmp = $chartApXml->getVal();
						$len = count($arrTmp["data"]["ap"]);
						$proc_info_val = $this->get_chart_procedures($patient_id,$formID);
						for($m=0;$m<$len;$m++){
							$k=$m+1;
							/*$tmpPlan = getTypedPlan($arrTmp["data"]["ap"][$m]["plan"], $formID, $k);*/
							$tmpPlan = $arrTmp["data"]["ap"][$m]["plan"];


							//if NE is checked then do not include//
							if($arrTmp["data"]["ap"][$m]["ne"]==0){
								$tmpAP = $this->getAPDisplayOpt($arrTmp["data"]["ap"][$m]["assessment"], $tmpPlan, $arrTmp["data"]["ap"][$m]["resolve"], $arrTmp["data"]["ap"][$m]["ne"], $elem_displayAP,$arrTmp["data"]["ap"][$m]["eye"]);
								$assessment .= $tmpAP[0];
								//$plan .= str_replace(';','.',$tmpAP[1]);
								$plan .= $tmpAP[1];
								$strAp.= $tmpAP[2];
								if(!empty($arrTmp["data"]["ap"][$m]["assessment"])) $flgAsm = 1;
								if(!empty($tmpPlan)) $flgPln = 1;

							}
							//if NE is checked then do not include//
						}
						if(trim($proc_info_val)!=""){
							$br="";if(trim($plan)){$br="<br/>";}
							$plan .=$br.'<table class="table table-bordered">'.$proc_info_val.'</table>';
						}
						//Signature ---------------------
						$sign = "";
						$signNm="";
						$cosign = "";
						$cosignNm = "";

						if(!empty($row["doctorId"])){
							list($signNm,$signNU,$sign) = getUserFirstName($row["doctorId"],2);
						}
						
						if(!empty($row["cosigner_id"])){
							list($cosignNm, $cosignNU,$cosign) = getUserFirstName($row["cosigner_id"],2);
						}
						//Signature ---------------------
						
						list($vision, $arrMrDone) = $this->getVisInfo($row["id_chart_vis_master"], $row["vis_statusElements"], $releaseNumber, $dbDos);
						
						
						$tests = "";
						//VF -----------
						if(($releaseNumber == "0") && ($row["vis_fil"] == "1"))
						{
							$tests .= "VF";
							$tests .= ($row["vis_rad"] == "1") ? "(OU)" : "";
							$tests .= ($row["vis_rad"] == "3") ? "(OD)" : "";
							$tests .= ($row["vis_rad"] == "2") ? "(OS)" : "";
							$tests .= "<br>";
						}else if(($releaseNumber == "1") && (!empty($row["vfId"]))){
							$tests .= "VF(OU)";
							$tests .= "<br>";
						}
						//VF -----------
						//HRT -----------
						if(($releaseNumber == "0") && ($row["scan_laser"] == "1"))
						{
							$tests .= "HRT";
							$tests .= ($row["sca_rad"] == "1") ? "(OU)" : "";
							$tests .= ($row["sca_rad"] == "3") ? "(OD)" : "";
							$tests .= ($row["sca_rad"] == "2") ? "(OS)" : "";
							$tests .= "<br>";
						}else if(($releaseNumber == "1") && (!empty($row["scanLaserEye"]))){
							$tests .= "HRT";
							$tests .= ($row["scanLaserEye"] == "OU") ? "(OU)" : "";
							$tests .= ($row["scanLaserEye"] == "OD") ? "(OD)" : "";
							$tests .= ($row["scanLaserEye"] == "OS") ? "(OS)" : "";
							$tests .= "<br>";			
						}
						//HRT -----------
						//OCT -----------
						if(!empty($row["oct_id"])){
							$tests .= "OCT";
							$tests .= ($row["octEye"] == "OU") ? "(OU)" : "";
							$tests .= ($row["octEye"] == "OD") ? "(OD)" : "";
							$tests .= ($row["octEye"] == "OS") ? "(OS)" : "";
							$tests .= "<br>";
						}						
						//OCT -----------
						//Pachy -----------
						if(($releaseNumber == "0") && ($row["pachymeter"] == "1"))
						{
							$tests .= "Pachy";
							$tests .= ($row["pac_rad"] == "1") ? "(OU)" : "";
							$tests .= ($row["pac_rad"] == "3") ? "(OD)" : "";
							$tests .= ($row["pac_rad"] == "2") ? "(OS)" : "";
							$tests .= "<br>";							
						}else if(($releaseNumber == "1") && (!empty($row["pachyMeterEye"]))){
							$tests .= "Pachy";
							$tests .= ($row["pachyMeterEye"] == "OU") ? "(OU)" : "";
							$tests .= ($row["pachyMeterEye"] == "OD") ? "(OD)" : "";
							$tests .= ($row["pachyMeterEye"] == "OS") ? "(OS)" : "";
							$tests .= "<br>";
						}
						//Pachy -----------
						//IVFA -----------
						if((($releaseNumber == "0") && ($row["ivfaExam"] == "on")) || (($releaseNumber == "1") && (!empty($row["ivfa_od"]))))
						{
							$tests .= "IVFA";
							$tests .= ($row["ivfa_od"] == "1") ? "(OU)" : "";
							$tests .= ($row["ivfa_od"] == "2") ? "(OD&gt;OS)" : "";
							$tests .= ($row["ivfa_od"] == "3") ? "(OD&lt;OS)" : "";
							$tests .= "<br>";
						}
						//IVFA -----------
						//Fundus Photo -----------
						if(($releaseNumber == "0") && ($row["disc_fundus"] == "1"))
						{
							$tests .= "Fundus Photo";
							$tests .= ($row["disc_os_od"] == "1") ? "(OU)": "";
							$tests .= ($row["disc_os_od"] == "2") ? "(OD)": "";
							$tests .= ($row["disc_os_od"] == "3") ? "(OS)": "";
							$tests .= "<br>";										
						}else if(($releaseNumber == "1") && ($row["fundusDiscPhoto"] == "1")){
							$tests .= "Fundus Photo";
							$tests .= ($row["photoEye"] == "OU") ? "(OU)": "";
							$tests .= ($row["photoEye"] == "OD") ? "(OD)": "";
							$tests .= ($row["photoEye"] == "OS") ? "(OS)": "";
							$tests .= "<br>";							
						}
						//Fundus Photo -----------
						//External -----------
						if(!empty($row["external_id"])){
							$tests .= "External";
							$tests .= ($row["external_eye"] == "OU") ? "(OU)" : "";
							$tests .= ($row["external_eye"] == "OD") ? "(OD)" : "";
							$tests .= ($row["external_eye"] == "OS") ? "(OS)" : "";
							$tests .= "<br>";
						}
						//External -----------
						//Topo -----------
						if(!empty($row["topo_id"])){
							$tests .= "Topography";
							$tests .= ($row["topoMeterEye"] == "OU") ? "(OU)" : "";
							$tests .= ($row["topoMeterEye"] == "OD") ? "(OD)" : "";
							$tests .= ($row["topoMeterEye"] == "OS") ? "(OS)" : "";
							$tests .= "<br>";
						}
						//Topo -----------
						//Ophth -----------
						if(!empty($row["ophtha_id"])){
							
							if((!empty($row["ophtha_od"]) && ($row["ophtha_od"] != "0-0-0:;")) &&
								(!empty($row["ophtha_os"]) && ($row["ophtha_os"] != "0-0-0:;"))
							   ){
								$tEye = "(OU)";
							}else if((!empty($row["ophtha_od"]) && ($row["ophtha_od"] != "0-0-0:;"))){
								$tEye = "(OD)";
							}else if((!empty($row["ophtha_os"]) && ($row["ophtha_os"] != "0-0-0:;"))){
								$tEye = "(OS)";
							}
							
							$tests .= "Ophth";							 
							$tests .= $tEye;														
							$tests .= "<br>";
						}
						//Ophth -----------

						//Gonio -----------
						if(!empty($row["gonio_id"])){
							if(!empty($row["gonio_od_summary"]) && !empty($row["gonio_os_summary"])){
								$tEye = "(OU)";	
							}else if(!empty($row["gonio_od_summary"])){
								$tEye = "(OD)";
							}else if(!empty($row["gonio_os_summary"])){
								$tEye = "(OS)";
							}
							
							$tests .= "Gonio";							 
							$tests .= $tEye;														
							$tests .= "<br>";
						}
						//Gonio -----------
						
						$prescription="";
						$prescription = $this->getPrescriptionFromDate($patient_id,$row["date_of_service"],$prevDos);
						$prevDos = $row["date_of_service"];
						
						//IOP ---------------------
						$iop = "";
						if(!empty($row["multiple_pressure"])){
							$arrMiop = $this->getMIop($row["multiple_pressure"]);
							if(!empty($arrMiop["iop"])){
								$iop .= $arrMiop["iop"];
							}
						}else{						
						
							if(($row["applanation"] == "1") && (!empty($row["app_od"]) || !empty($row["app_os_1"])))
							{							
								
								$iop .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
								$iop .= "<tr><td class=\"text\" width=\"17\">"; 
								$iop .= "T<sub><b>A</b></sub>:</td>";
								$iop .= "<td align=\"center\" class=\"text\" width=\"23\">".$row["app_od"].",</td>";
								$iop .= "<td align=\"center\" class=\"text\" width=\"23\">".$row["app_os_1"]."</td></tr>";
								$iop .= "</table>";

							}
							
							if(($row["puff"] == "1") && (!empty($row["puff_od"]) || !empty($row["puff_os_1"])))
							{
								$iop .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
								$iop .= "<tr><td class=\"text\" width=\"17\">"; 
								$iop .= "T<sub><b>P</b></sub>:</td>";
								$iop .= "<td align=\"center\" class=\"text\" width=\"23\">".$row["puff_od"].",</td>";
								$iop .= "<td align=\"center\" class=\"text\" width=\"23\">".$row["puff_os_1"]."</td></tr>";
								$iop .= "</table>";
							}

							if(($row["tx"] == "1") && (!empty($row["tx_od"]) || !empty($row["tx_os"])))
							{
								$iop .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
								$iop .= "<tr><td class=\"text\" width=\"17\">"; 
								$iop .= "T<sub><b>x</b></sub>:</td>";
								$iop .= "<td align=\"center\" class=\"text\" width=\"23\">".$row["tx_od"].",</td>";
								$iop .= "<td align=\"center\" class=\"text\" width=\"23\">".$row["tx_os"]."</td></tr>";
								$iop .= "</table>";
							}
						}
						
						//target values
						if(!empty($row["trgtOd"])&&!empty($row["trgtOs"])){
							$tmpod=(!empty($row["trgtOd"]))?$row["trgtOd"].",":"&nbsp;";
							$tmpos=(!empty($row["trgtOs"]))?$row["trgtOs"]:"&nbsp;";
							$iop .= "Trgt:<span style=\"width:23px;\">".$tmpod."</span><span style=\"width:23px;\">".$tmpos."</span>";
						}
						
						//Dilation
						$ar_pt_dilate = $this->ptDiag_isPtDilated($patient_id,$formID);
						if(!empty($ar_pt_dilate)){
							if(!empty($iop)){ $iop.="<br/>"; }
							$iop .= $ar_pt_dilate;
						}

						//IOP ---------------------
						//CD ---------------------
						$cd = "";
						$cd .= (($row["od_text"] != "0.") && (!empty($row["od_text"])))||!empty($row["cd_val_od"]) ? "<font color=\"Blue\"><b>OD</b></font>:".trim($row["cd_val_od"]." ".$row["od_text"])."<br>" : "";
						$cd .= (($row["os_text"]  != "0.") && (!empty($row["os_text"])))||!empty($row["cd_val_os"]) ? "<font color=\"Green\"><b>OS</b></font>:".trim($row["cd_val_os"]." ".$row["os_text"])."<br>" : "";						
						//Follow Up
						$follow_up = "";
						if(!empty($row["followup"])){
							list($len_arrFu,$arrFu) = $this->fu_getXmlValsArr($row["followup"]);
							if(count($arrFu) > 0){
								foreach($arrFu as $val){
									$tmpPro = (!empty($val["provider"])) ? getUserFirstName($val["provider"],3) : "";
									$tmp = trim($val["number"]." ".$val["time"]." ".$val["visit_type"]." ".$tmpPro);
									if(!empty($tmp)){
										$follow_up .= $tmp."<br>";
									}
								}
							}
							
						}else if(!empty($row['follow_up_numeric_value']) || !empty($row['follow_up']) ){
							if($row['follow_up_numeric_value']=='13')
								$row['follow_up_numeric_value'] = 'PRN';
							if($row['follow_up_numeric_value']=='14')
								$row['follow_up_numeric_value'] = 'PMD';
						
							$follow_up.=$row['follow_up_numeric_value']." ".$row['follow_up'];
							$follow_up.= (!empty($row["followUpVistType"])) ? " ".$row["followUpVistType"] : "";
							$follow_up .= "<br>";
						}
						
						$retina = $row['retina'];
						$neuro_ophth = $row['neuro_ophth'];
						$doctor_name = $row['doctor_name'];								
						if(!empty($retina) || !empty($neuro_ophth) || !empty($doctor_name)){							
							$follow_up .= ($retina == "1") ? "Retina<BR>" : "" ;
							$follow_up .= ($neuro_ophth == "1") ? "Neuro ophth<BR>" : "" ;
							$follow_up .= (!empty($doctor_name)) ? $doctor_name."<br>" : "";							
						}
						
						$continue_meds = $row['continue_meds'];
						$monitor_ag = $row['monitor_ag'];
						$id_precation = $row['id_precation'];
						$rd_precation = $row['rd_precation'];
						$lid_scrubs_oint = $row['lid_scrubs_oint'];					
						
						if(!empty($continue_meds) || !empty($monitor_ag) || !empty($id_precation) || !empty($rd_precation) || !empty($lid_scrubs_oint)){
							$follow_up .= ($continue_meds == "1") ? "Continue Meds.<BR>" : "" ;
							$follow_up .= ($monitor_ag == "1") ? "Monitor AG<BR>" : "" ;
							$follow_up .= ($id_precation == "1") ? "ID Precautions<BR>" : "" ;
							$follow_up .= ($rd_precation == "1") ? "RD Precautions<BR>" : "" ;
							$follow_up .= ($lid_scrubs_oint == "1") ? "Lid Scrubs & oint " : "" ;
						}
					
						
						/* Testing */
						$testing = "";
						$sx = "";
						$active_tests = ""; //Active Tests
						if($releaseNumber == "0"){
							$hrt = $row["hrt"];
							$hrtEye = $row["hrtEye"];
							$oct = $row["oct"];
							$octEye = $row["octEye"];
							$avf = $row["avf"];
							$avfEye = $row["avfEye"];								
							$ivfa = $row["ivfa"];
							$ivfaEye = $row["ivfaEye"];								
							$dfe = $row["dfe"];
							$dfeEye = $row["dfeEye"];								
							$photos = $row["photos"];
							$photosEye = $row["photosEye"];								
							$pachy = $row["pachy"];
							$pachyEye = $row["pachyEye"];
							//sx
							$cat_iol = $row["cat_iol"];
							$catIolEye = $row["catIolEye"];
							$yag_cap = $row["yag_cap"];
							$yagCapEye = $row["yagCapEye"];
							$slt = $row["altp"];
							$sltEye = $row["altpEye"];
							$pi = $row["pi"];
							$piEye = $row["piEye"];
							$ratinal_laser = $row["ratinal_laser"];
							$ratinalLaserEye = $row["ratinalLaserEye"];							
							
							if(!empty($hrt)){
								$testing .= ($hrt == "1") ? "HRT" : "";		
								$testing .= (!empty($hrtEye)) ? "($hrtEye)" : "";		
								$testing .= "<BR>";
							}
							
							if(!empty($oct)){
								$testing .= "OCT:<BR>";
								$testing .= $oct;
								$testing .= (!empty($octEye)) ? "($octEye)" : "";		
								$testing .= "<BR>";
							}
							
							if(!empty($avf)){
								$testing .= "AVF:<BR>";
								$testing .= $avf;
								$testing .= (!empty($avfEye)) ? "($avfEye)" : "";		
								$testing .= "<BR>";
							}
							
							if(!empty($ivfa)){
								$testing .= ($ivfa == "1") ? "IVFA" : "";		
								$testing .= (!empty($ivfaEye)) ? "($ivfaEye)" : "";		
								$testing .= "<BR>";
							}
							
							if(!empty($dfe)){
								$testing .= ($dfe == "1") ? "DFE" : "";		
								$testing .= (!empty($dfeEye)) ? "($dfeEye)" : "";		
								$testing .= "<BR>";
							}
							
							if(!empty($photos)){
								$testing .= "Photos:<BR>";
								$testing .= $photos;
								$testing .= (!empty($photosEye)) ? "($photosEye)" : "";		
								$testing .= "<BR>";	
							}
							
							if(!empty($pachy)){
								$testing .= ($pachy == "1") ? "Pachy" : "";		
								$testing .= (!empty($pachyEye)) ? "($pachyEye)" : "";		
								$testing .= "<BR>";
							}
							
							//Sx
							if(!empty($cat_iol)){
								$sx .= "Cat.IOL";
								$sx.= (!empty($catIolEye)) ? "($catIolEye)" : ""; 
								$sx.= "<br>";
							}
							if(!empty($yag_cap)){
								$sx .= "Yag cap.";
								$sx.= (!empty($yagCapEye)) ? "($yagCapEye)" : ""; 
								$sx.= "<br>";
							}
							if(!empty($slt)){
								$sx .= "SLT";
								$sx.= (!empty($sltEye)) ? "($sltEye)" : ""; 
								$sx.= "<br>";
							}
							if(!empty($pi)){
								$sx .= "PI";
								$sx.= (!empty($piEye)) ? "($piEye)" : ""; 
								$sx.= "<br>";
							}
							if(!empty($ratinal_laser)){
								$sx .= "R.Laser";
								$sx.= (!empty($ratinalEye)) ? "($ratinalLaserEye)" : ""; 
								$sx.= "<br>";
							}
							
							$testing = "<div>".$testing."</div>";
							
						}else if($releaseNumber == "1"){
							list($testing,$sx) = $this->getTestingR2($formID,0);
						}					
						//Concate Plan and F/U
						if(!empty($follow_up)){
							//$strAp .=  "<tr><td colspan=\"2\"><b>F/U:</b> ".$follow_up."</td></tr>";
							$plan .= "<b>F/U:</b> ".$follow_up; 
							$strAp.= "<tr><td>&nbsp;</td><td><b>F/U:</b> ".$follow_up."</td></tr>";
							$flgPln = 1;
						}

						//comments: assessment & plan
						if(!empty($row["plan_notes"]) && trim($row["plan_notes"])!="Comments:"){
							
							//IF COMMENT DATE AND USER ID ADDED
							if(strpos($row["plan_notes"], '~')>0){
								$row["plan_notes"]= str_replace('||', '', $row["plan_notes"]);
								$row["plan_notes"]= str_replace('~~', '~', $row["plan_notes"]);
								list($comment, $comment_date, $user_id) = explode('~', $row["plan_notes"]);

								if($user_id>0){
									$rs=imw_query("Select lname, fname FROM users WHERE id='".$user_id."'");
									$res=imw_fetch_array($rs);
									$cmnt_user_name=$res['lname'].', '.$res['fname'];
								}
								
								$row["plan_notes"]= $comment.'; Added on '.$comment_date.' by '.$cmnt_user_name;
							}
							$plan .= "<b>Comments:</b> ".$row["plan_notes"]."<br/>";
							$strAp.= "<tr><td>&nbsp;</td><td><b>Comments:</b> ".$row["plan_notes"]."</td></tr>";
							$flgPln = 1;
						}
						
						//Pt Comments and Discussion
						if(!empty($row["commentsForPatient"]) && trim($row["commentsForPatient"])!="Comments:"){
							
							//IF COMMENT DATE AND USER ID ADDED
							if(strpos($row["commentsForPatient"], '~')>0){
								$row["commentsForPatient"]= str_replace('||', '', $row["commentsForPatient"]);
								$row["commentsForPatient"]= str_replace('~~', '~', $row["commentsForPatient"]);
								list($comment4pt, $comment4pt_date, $user_id_4pt) = explode('~', $row["commentsForPatient"]);

								if($user_id_4pt>0){
									$rs=imw_query("Select lname, fname FROM users WHERE id='".$user_id_4pt."'");
									$res=imw_fetch_array($rs);
									$cmnt_user_name_4pt=$res['lname'].', '.$res['fname'];
								}
								
								$row["commentsForPatient"]= $comment4pt.'; Added on '.$comment4pt_date.' by '.$cmnt_user_name_4pt;
							}
							$plan .= "<b>Pt Discussion / Comments:</b> ".$row["commentsForPatient"];
							$strAp.= "<tr><td>&nbsp;</td><td><b>Pt Discussion / Comments:</b> ".$row["commentsForPatient"]."</td></tr>";
							$flgPln = 1;
						}

						//Make ap table
						
						$pendingOrderStr = @join('<br>',$pendingOrderArr[$formID]);
						$completedOrderStr = @join('<br>',$completedOrderArr[$formID]);
						if(!empty($completedOrderStr)) $flgOrdr = 1;
						
						//Draw
						$draw="";
						//echo "t:".getChartDrawing($formID,"check").";";
						if($this->getChartDrawing($formID,"check",'',$patient_id)){
							if($pdg_showpop==0) { 
								$draw="<input type=\"button\" name=\"elm_btndrw\" value=\"Drawing\" class=\"btn btn-xs btn-primary\" align=\"center\" ";
								$draw.= "onclick=\"showBigImage('".$formID."')\" class=\"hand_cur\" title=\"Click to See Drawing\" >" ; 
							}else{
								$draw.="<span class=\"btnddrw\">Drawing</span>";
							}
						}
						
						//rx
						$rx="";		
						$rxArr = $this->getChartRx($formID);
						if(count($rxArr)>0){
							$rx="";
							foreach($rxArr as $keyrx=> $valrx){
								$rx .= "&bull;".$valrx."<br/>";
							}
							$rx.="";
						}
						
						//Consult Letter INFO
						$con_ltr="";
						$con_ltr = $this->getCnsltLtrInfo($patient_id, $formID);
						
						//SOC
						$oAdmn = new Admn();
						$soc_str=$oAdmn->get_standrad_of_care($row["vst_soc"], 1, $row["soc_desc"]);
						
						//$strAP
						if(!empty($strAp)){$strAp = "<table class=\"tblap\" border=\"0\">".$strAp."</table>";}
						
						// Make Array
						$arrValues[$i] = array( "date"=>$date, "vision"=>$vision, "iop"=>$iop, "cd"=>$cd,
										"tests"=>$completedOrderStr,"assessment"=>$assessment, "plan"=>$plan,"strAP"=>$strAp,
										"follow_up"=>$follow_up, "testing"=>$pendingOrderStr, "prescription"=>$prescription,
										"date2"=>$date2, "sx"=>$sx, 
										"sign"=>$sign, "cosign"=>$cosign,
										"signNm"=>$signNm, "cosignNm"=>$cosignNm,"draw"=>$draw,
										"rx"=>$rx,"memo"=>$memo, "memo_text"=>$memo_text, "con_ltr" => $con_ltr, 
										"soc_str"=>$soc_str);
						// Check previous
						if($i>0){
							if($vision == $visionChk) {
								//$arrValues[$i-1]["vision"] = "";
							}else{
								$visionChk = $vision ;
							}
							if($iop == $iopChk){
								//$arrValues[$i-1]["iop"] = "";
							}else{
								$iopChk = $iop ;
							}
							if($cd == $cdChk){
								//$arrValues[$i-1]["cd"] = "";
							}else{
								$cdChk = $cd ;
							}
							if($tests == $testsChk){
								//$arrValues[$i-1]["tests"] = "";
							}else{
								$testsChk = $tests;
							}
						}
						else
						{
							$visionChk = $vision;
							$iopChk = $iop;
							$cdChk = $cd;
							$testsChk = $tests;
							$assessmentChk = $assessment;
							$planChk = $plan;
							$follow_upChk = $follow_up;
							$testingChk = $testing;
						}
					}
					//AssesPlan width
					$wdtAsm=270*$flgAsm;
					$wdtPln=310*$flgPln;
                    //$wdtOrdr=(!empty($flgOrdr)) ? 180 : 40;
                    //$wdtOrdr = 70;
                    
                    // Display Values
                    $len = count($arrValues);
					for($i=0;$i<$len;$i++)
                    {
				$bgColor = ($i%2) ? "#efefef" : "";
				//Sign
				$tmp = "";
				$ht = 30;
				if(!empty($arrValues[$i]["sign"])){
					$tmp .= "<span title=\"".$arrValues[$i]["signNm"]."\" >".$arrValues[$i]["signNm"]."</span><br />";
					$ht += 30;
				}
				
				if(!empty($arrValues[$i]["cosign"])){
					$tmp .= "<span title=\"".$arrValues[$i]["cosignNm"]."\" >".$arrValues[$i]["cosignNm"]."</span><br />";
					$ht += 30;
				}						
				//				
				$encounterStatus = $this->getPtChargestblData($this->patient_id, $arrValues[$i]["date2"]);
				$encounterId = $encounterStatus['encounter_id'];
				$encounter_first_posted_date = $encounterStatus['first_posted_date'];
				$encounter_totalBalance = $encounterStatus['totalBalance'];
				$charge_title  = show_tooltip("Charge", "top");
				if($encounter_first_posted_date != '0000-00-00' && $encounterId != "") {
					$charge_color = '#5cb85c';
				} else {
					$charge_color =  '#e83636';
				}
				$billedStatus = $this->getPtBilledData($this->patient_id, $encounterId);
				$billedencounterId = $billedStatus['encounter_id'];
				$billed_title  = show_tooltip("Billed", "top");
				if($billedencounterId != "") {
					$billed_color = '#5cb85c';
				} else {
					$billed_color =  '#e83636';
				}
				
				$InsPaymentStatus = $this->getPtInsData($encounterId, $paid_by='Insurance' );
				$paymentByIns  =  $InsPaymentStatus['paid_by'];
				$ins_title  = show_tooltip("Insurance", "top");
				if($encounter_totalBalance <= 0 && $encounterId > 0 ){
					$ins_color = '#5cb85c';
				} else if($paymentByIns == 'Insurance' && $encounterId > 0 ) {
					$ins_color = '#fbef56';
				} else {
					$ins_color =  '#e83636';
				}
				
				
				$PtPaymentStatus = $this->getPtInsData($encounterId, $paid_by='Patient' );
				$paymentByPt =  $PtPaymentStatus['paid_by'];
				$pt_title  = show_tooltip("Patient", "top");
				if($encounter_totalBalance <= 0 && $encounterId > 0 ){
					$pt_color = '#5cb85c';
				} else if($paymentByPt == 'Patient' && $encounterId > 0) {
					$pt_color = '#fbef56';
				} else {
					$pt_color =  '#e83636';
				}
				
				$drawbtn = "";
				if($arrValues[$i]["draw"]){
					$drawbtn = "<span>".$arrValues[$i]["draw"]."</span><br />";
				}
				$soc_info = "";
				if(!empty($arrValues[$i]["soc_str"])){ $soc_info = "<span title=\"Standrads of Care\" data-toggle=\"tooltip\" class=\"soc\" >".$arrValues[$i]["soc_str"]."</span>"; }
					
				
				$return_str .= "<tr>";
					$return_str .= "<td class=\"text text-nowrap\" style=\"vertical-align: top!important;\">".
						 "<span title=\"".$arrValues[$i]["date2"]."\">".$arrValues[$i]["date"]."</span><br />";						 
					$return_str .= $tmp;
					$return_str .= $drawbtn;
					$return_str .= $soc_info;
					$return_str .= "<span><br /><label><b>Billed</b></label></span><br />";
					$return_str .= "<span $charge_title>
										<svg height='15' width='25'>
											<circle cx='8' cy='8' r='5' stroke='black' stroke-width='0.3' fill= '$charge_color'  />
										</svg>
									</span>
									<span $billed_title>
										<svg height='15' width='25'>
											<circle cx='8' cy='8' r='5' stroke='black' stroke-width='0.3' fill= '$billed_color'  />
										</svg>
									</span>
									<span $ins_title>
										<svg height='15' width='25'>
											<circle cx='8' cy='8' r='5' stroke='black' stroke-width='0.3' fill= '$ins_color'  />
										</svg>
									</span>
									<span $pt_title>
										<svg height='15' width='25'>
											<circle cx='8' cy='8' r='5' stroke='black' stroke-width='0.3' fill= '$pt_color'  />
										</svg>
									</span>";
					$return_str .= "</td>";
					if($arrValues[$i]["memo"]=="1"){
						$return_str .= "<td class=\"text\" style=\"vertical-align: top!important;\" colspan=\"6\">".nl2br($arrValues[$i]["memo_text"])."</td>";
						
					}else{
					
						$return_str .= "<td style=\"vertical-align: top!important;\" class=\"text\" >".$arrValues[$i]["vision"]."</td>";
						$return_str .= "<td style=\"vertical-align: top!important;\" class=\"text\" valign=\"top\" >".$arrValues[$i]["iop"]."</td>";
						$return_str .= "<td style=\"vertical-align: top!important;\"  class=\"text\" >".$arrValues[$i]["cd"]."</td>";
						$return_str .= "<td style=\"vertical-align: top!important;\" class=\"text\" >".$arrValues[$i]["con_ltr"]."</td>";
						
						if($callFrom=="priting" || $callFrom=="printing"){
						$return_str .= "<td style=\"vertical-align: top!important;\" class=\"text pgd_apTbl\" colspan=\"2\">".$arrValues[$i]["strAP"].
									 "</td>";
						}else{
						$return_str .= "<td style=\"vertical-align: top!important;\" class=\"text pgd_apTbl\" >".
								"<div id=\"div_assess_".$i."\" style=\"word-wrap: break-word;\" >".$arrValues[$i]["assessment"]."</div>".
							 "</td>";
						
						$return_str .= "<td style=\"vertical-align: top!important;\" class=\"text pgd_apTbl\" >".
								"<div id=\"div_plan_".$i."\" style=\"word-wrap: break-word;\" >".$arrValues[$i]["plan"]."</div>".
							 "</td>";
						
						}	
						$return_str .= "<td style=\"vertical-align: top!important;\" class=\"text\" >".$arrValues[$i]["rx"]."</td>";
					}
				$return_str .= "</tr>";
			}
				}else{
					$return_str .=  "<tr>";
                    $return_str .=  "<td colspan=\"8\" class=\"text\"><div >No Past Diagnosis is available of this Patient.</div></td>";
                    $return_str .=  "</tr>";
				}	
			$return_str .= '</table>';
			return $return_str;
		}
		
		public function getFacilityAbbr($id){
			if(constant("SHOW_SERVER_LOCATION") == 1){
				$sql = "SELECT name, c2.abbre  FROM facility c1 
						LEFT JOIN server_location c2 ON c1.server_location = c2.id
						WHERE c1.id = '".$id."' ";
			}else{
				$sql = "SELECT name  FROM facility WHERE id = '".$id."' ";
			}	
			
			$row=sqlQuery($sql);
			if($row != false){
				
				if(constant("SHOW_SERVER_LOCATION") == 1){
					if(!empty($row["abbre"])){
						$ret=$row["abbre"];
					}
				}
				
				if(empty($ret)){
					$ret=$row["name"];
				}
			}
			return trim($ret);
		}
		
		/*
		public function get_graph_data($request){
			$pId = $this->patient_id;
			$elem_opts = $request["elem_opts"];
			$series = array();
			$seriesName = array();
			$axisName = array("Date", "IOP");
			$graphTitle = " IOP Values ";
			
			$seriesColor = array();

			$arr_ta_od=$arr_ta_os=$arr_tp_od=$arr_tp_os=$arr_tx_od=$arr_tx_os=$arr_dates=array();
			
			$sql = "SELECT ".
					"c2.puff,c2.puff_od,c2.puff_os_1, ".
					"c2.applanation,c2.app_od,c2.app_os_1, ".
					"c2.tx,c2.tx_od,c2.tx_os,c2.fieldCount, ".
					"c2.multiple_pressure, ".
					"c2.iop_id, ".
					//"c3.date_of_service, ".
					"c1.date_of_service, ".
					"c1.create_dt,c1.update_date, c1.id ".
				   "FROM chart_master_table c1 ".
				   "LEFT JOIN chart_iop c2 ON c2.form_id=c1.id ".
				  // "LEFT JOIN chart_left_cc_history c3 ON c3.form_id=c1.id ".	
				   "WHERE c1.patient_id='".$pId."' ".
				   //"ORDER BY IFNULL(c3.date_of_service,c1.create_dt), c1.id ";
				   "ORDER BY c1.date_of_service, c1.id ";
			
			$rez=sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++){
				
				if(empty($row["multiple_pressure"])){
					$arrMP["multiplePressuer"]["elem_applanation"] = $row["applanation"];
					$arrMP["multiplePressuer"]["elem_appOd"] = $row["app_od"];
					$arrMP["multiplePressuer"]["elem_appOs"] = $row["app_os_1"];
					
					$arrMP["multiplePressuer"]["elem_puff"] = $row["puff"];
					$arrMP["multiplePressuer"]["elem_puffOd"] = $row["puff_od"];
					$arrMP["multiplePressuer"]["elem_puffOs"] = $row["puff_os_1"];

					$arrMP["multiplePressuer"]["elem_tx"] = $row["tx"];
					$arrMP["multiplePressuer"]["elem_appTrgtOd"] = $row["tx_od"];
					$arrMP["multiplePressuer"]["elem_appTrgtOs"] = $row["tx_os"];		
					$fieldCount="0";
				}else{
					$arrMP=unserialize($row["multiple_pressure"]);
					$fieldCount=$row["fieldCount"];
				}

				$ta_od=$ta_os=$tp_od=$tp_os=$tx_od=$tx_os=0;
				$dos=$row["date_of_service"];
				if(empty($dos))$dos=$row["create_dt"];
				if(empty($dos))$dos=$row["update_date"];

				//Loop values
				$arrFC = explode(",",$fieldCount);
				$lenFC = count($arrFC);
				
				for($cnt=0,$j=1;$j<=$lenFC;$j++,$cnt++){
					
					$indx=$indx2="";
					if($j>1){
						$indx = $arrFC[$cnt];
						$indx2=$j;
					}
					

					$v_Ta=$arrMP["multiplePressuer".$indx2]["elem_applanation".$indx];				
					if(!empty($v_Ta)){
						$v_Od=$arrMP["multiplePressuer".$indx2]["elem_appOd".$indx];
						$v_Os=$arrMP["multiplePressuer".$indx2]["elem_appOs".$indx];
						
						if(!empty($v_Od)){
							$ta_od=$v_Od;
						}
					
						if(!empty($v_Os)){
							$ta_os=$v_Os;
						}
					}
					
					$v_Tp=$arrMP["multiplePressuer".$indx2]["elem_puff".$indx];
					if(!empty($v_Tp)){
						$v_Od=$arrMP["multiplePressuer".$indx2]["elem_puffOd".$indx];
						$v_Os=$arrMP["multiplePressuer".$indx2]["elem_puffOs".$indx];

						if(!empty($v_Od)){
							$tp_od=$v_Od;
						}
					
						if(!empty($v_Os)){
							$tp_os=$v_Os;
						}					
					}
					
					$v_Tx=$arrMP["multiplePressuer".$indx2]["elem_tx".$indx];
					if(!empty($v_Tx)){
						$v_Od=$arrMP["multiplePressuer".$indx2]["elem_appTrgtOd".$indx];
						$v_Os=$arrMP["multiplePressuer".$indx2]["elem_appTrgtOs".$indx];

						if(!empty($v_Od)){
							$tx_od=$v_Od;
						}
					
						if(!empty($v_Os)){
							$tx_os=$v_Os;
						}				
					}
				}

				if(!empty($ta_od)||!empty($ta_os)||!empty($tp_od)||!empty($tp_os)||!empty($tx_od)||!empty($tx_os)){
					
					if(strpos($elem_opts,"TAOD")!==false || $elem_opts=="All"){
						$arr_ta_od[]=$ta_od;
					}
					if(strpos($elem_opts,"TAOS")!==false || $elem_opts=="All"){
						$arr_ta_os[]=$ta_os;
					}
					if(strpos($elem_opts,"TPOD")!==false || $elem_opts=="All"){
						$arr_tp_od[]=$tp_od;
					}
					if(strpos($elem_opts,"TPOS")!==false || $elem_opts=="All"){
						$arr_tp_os[]=$tp_os;
					}
					if(strpos($elem_opts,"TXOD")!==false || $elem_opts=="All"){
						$arr_tx_od[]=$tx_od;
					}
					if(strpos($elem_opts,"TXOS")!==false || $elem_opts=="All"){
						$arr_tx_os[]=$tx_os;
					}
					$arr_dates[]=get_date_format($dos);
				}
			}
			
			if(count($arr_ta_od)>0){
				$series[] = $arr_ta_od;
				$seriesName [] = "TA OD";
				$seriesColor [] = array(0,0,205);
				$ckd_taod="checked=\"checked\"";
			}

			if(count($arr_ta_os)>0){
				$series[] = $arr_ta_os;
				$seriesName [] = "TA OS";
				$seriesColor [] = array(34,139,34);
				$ckd_taos="checked=\"checked\"";
			}
			if(count($arr_tp_od)>0){
				$series[] = $arr_tp_od;
				$seriesName [] = "TP OD";
				$seriesColor [] = array(255,185,15);
				$ckd_tpod="checked=\"checked\"";
			}
			if(count($arr_tp_os)>0){		
				$series[] = $arr_tp_os;
				$seriesName [] = "TP OS";
				$seriesColor [] = array(255,0,0);
				$ckd_tpos="checked=\"checked\"";
			}
			if(count($arr_tx_od)>0){	
				$series[] = $arr_tx_od;
				$seriesName [] = "TX OD";
				$seriesColor [] = array(160,32,240);
				$ckd_txod="checked=\"checked\"";
			}
			if(count($arr_tx_os)>0){		
				$series[] = $arr_tx_os;
				$seriesName [] = "TX OS";
				$seriesColor [] = array(30,144,255);
				$ckd_txos="checked=\"checked\"";
			}
			
			if(count($series)>0){
				$series[] = $arr_dates;	//Dates			
				
				$len = count($series);		
				$absLabel = "Serie".$len;	
				
			}else{
				
				$msg='Graph can not created becuase of insufficient data.';
			}		
				
			
			if( $len > 0 ){
				$line_chart_data = $this->line_chart($seriesName,$series);
			
				$line_pay_graph_var_arr_js=json_encode($line_chart_data['line_pay_graph_var_detail']);
				$line_payment_tot_arr_js=json_encode($line_chart_data['line_payment_tot_detail']);
			}
			
			
			$ajax_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr_js;
			$ajax_arr['line_payment_tot_detail']=$line_payment_tot_arr_js;
			return json_encode($ajax_arr);
		}
		
		function line_chart($graph_name,$graph_data){
			$key_i=0;$kk=0;
			
			foreach($graph_data[6] as $key=>$val){
				$line_payment_tot_arr[$key]["category"]=$val;
			}

			foreach($graph_data as $key=>$val){
				if($key!=6){	
					$key_i++;
					$title="";
					$title=$graph_name[$key];
					$line_pay_graph_var_arr[]=array("alphaField"=> "C",
						"balloonText"=> "[[title]] of [[category]]: [[value]]",
						"bullet"=> "round",
						"bulletField"=> "C",
						"bulletSizeField"=> "C",
						"closeField"=> "C",
						"colorField"=> "C",
						"customBulletField"=> "C",
						"dashLengthField"=> "C",
						"descriptionField"=> "C",
						"errorField"=> "C",
						"fillColorsField"=> "C",
						"gapField"=> "C",
						"highField"=> "C",
						"id"=> "AmGraph-$key_i",
						"labelColorField"=> "C",
						"lineColorField"=> "C",
						"lowField"=> "C",
						"openField"=> "C",
						"patternField"=> "C",
						"title"=> $title,
						"valueField"=> "column-$key_i",
						"xField"=> "C",
						"yField"=> "C");
					
					foreach($graph_data[$key] as $key2=>$val2){
						if($graph_data[$key][$key2]>0){	
							$line_payment_tot_arr[$key2]["column-".$key_i]=$graph_data[$key][$key2];
						}
						$kk++;
					}
				}
			}
			$return_arr['line_payment_tot_detail']=$line_payment_tot_arr;
			$return_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr;
			return $return_arr;
		}
		*/
		public function getAPDisplayOpt($assess, $plan, $resolve, $no_change, $elem_displayAP,$eye){
			$plan = str_replace(';','.',$plan);
			
			$strResolve = ($resolve == "1") ? "Resolved: " : "";
			$cssap =  ($resolve == "1") ? "apClrRes" : "";

			if(!empty($assess))$assess=nl2br($assess);
			if(!empty($plan)){ 
				$plan=htmlentities($plan, ENT_COMPAT, 'UTF-8'); $plan=nl2br($plan);  
			}
			if(!empty($eye)){ $eye="(".$eye.")"; }else{ $eye=""; }
			
			$w1=" width=\"56%\" "; $w2=" width=\"44%\" ";
			//$w1=$w2="";
			
			if(strpos($elem_displayAP, "All") !== false){
				//All -----------------------					
				$assess = (($assess != "")) ? "<div class=\"".$cssap."\" ><p>&bull;</p><p>".$strResolve."".$assess." ".$eye."</p></div>" : "<div></div>";
				$plan = (($plan != "")) ? "<div class=\"".$cssap."\" ><p>&bull;</p><p>".$plan."</p></div>" : "<div></div>";		
				$ap = "<tr><td ".$w1." >".$assess."</td><td ".$w2.">".$plan."</td></tr>";		
				//All -----------------------
				
			}else if((strpos($elem_displayAP, "Resolved") !== false) || (strpos($elem_displayAP, "Charted in Error") !== false)){	
				
				if((strpos($elem_displayAP, "Resolved") !== false) && (strpos($elem_displayAP, "Charted in Error") === false)){
					//Resolved ----------------
					$assess = (($assess != "") && ($resolve == "1")) ? "<div class=\"".$cssap."\"><p>&bull;</p><p>".$strResolve.$assess." ".$eye."</p></div>" : "<div></div>";
					$plan = (($plan != "") && ($resolve == "1")) ? "<div class=\"".$cssap."\"><p>&bull;</p><p>".$plan."</p></div>" : "<div></div>";			
					$ap = ($resolve == "1") ? "<tr><td ".$w1.">".$assess."</td><td ".$w2.">".$plan."</td></tr>" : "<tr><td></td><td></td></tr>";
					//Resolved ----------------
				}else if((strpos($elem_displayAP, "Resolved") === false) && (strpos($elem_displayAP, "Charted in Error") !== false)){
					//Charted in Error ----------------			
					$assess = (($assess != "") && ($no_change == "1")) ? "<div><p>&bull;</p><p>".$assess." ".$eye."</p></div>" : "<div></div>";
					$plan = (($plan != "") && ($no_change == "1")) ? "<div><p>&bull;</p><p>".$plan."</p></div>" : "<div></div>";
					$ap = ($no_change == "1") ? "<tr><td ".$w1.">".$assess."</td><td ".$w2.">".$plan."</td></tr>" : "<tr><td></td><td></td></tr>" ;
					//Charted in Error ----------------
				}else{
					//any ----------------
					$assess = (($assess != "") && (($no_change == "1") || ($resolve == "1"))) ? 
											"<div class=\"".$cssap."\"><p>&bull;</p><p>".$strResolve.$assess." ".$eye."</p></div>" : "<div></div>";
					$plan = (($plan != "") && (($no_change == "1") || ($resolve == "1"))) ?
											"<div class=\"".$cssap."\"><p>&bull;</p><p>".$plan."</p></div>" : "<div></div>";
					$ap = ($no_change == "1" || $resolve == "1") ? "<tr><td ".$w1.">".$assess."</td><td ".$w2.">".$plan."</td></tr>" : "<tr><td></td><td></td></tr>" ;
					//any ----------------
				}
				
			}else{
				//Active  --------------------
				// 7/18/2011: We need to show Resolved entries for that DOS as they are still Active.
				/* Stopped
				// As per Feedback in #357 task on mantis:--
				// Active – All active A&P and NE (Not Examined is still active even though they did not examined	
				// that diagnosis).  DO NOT show Visit entries that are Resolved or charted in Error.
				*/
				//
				//Active  --------------------
				$assess = ($assess != "") ? "<div class=\"".$cssap."\"><p>&bull;</p><p>".$strResolve.$assess." ".$eye."</p></div>" : "<div></div>";
				$plan = ($plan != "") ? "<div class=\"".$cssap."\"><p>&bull;</p><p>".$plan."</p></div>" : "<div></div>";
				$ap =  "<tr><td ".$w1.">".$assess."</td><td ".$w2.">".$plan."</td></tr>";
				//Active  --------------------
			}
			return array($assess, $plan,$ap);
		}
		
		public function getPrescriptionFromDate($patient_id,$date,$prevDos){
			$eye_array = array("PO","OU","OS","OD","RLL","RUL","LLL","LUL","O/O","IV","IM","Topical","L/R Ear","Both Ears");
			$use_array= array("qd","qhs","qAM","qid","bid","tid","qod","__hrs","__Xdaily");

			$sql = "SELECT * ,DATE_FORMAT(date_added,'".get_sql_date_format('','y')."') as date_added2 FROM prescriptions ".
				   "WHERE patient_id = '$patient_id' AND  date_added >= '$date' ";
			$sql .= (!empty($prevDos) && ($date != $prevDos)) ? "AND date_added < '$prevDos' " : "";

			$rez = sqlStatement($sql);

			$presc = "";
			for($i=0;$row=sqlFetchArray($rez);$i++)
			{
				// $presc .= $row["drug"]." ".$row["size"]." ".$row["dosage"].", ".$row["quantity"]." ".$row["quantity_unit"].", ".$row["unit"]." ".$eye_array[$row["eye"]]." ".$row["usage_2"]." ".$use_array[$row["usage_1"]]."<br>";
				if(!empty($row["drug"]))
				{
					$presDate = "";
					if(!empty($row["date_added2"]) && ($row["date_added2"] != "0000-00-00") )
					{
						$presDate = "<span title=\"".$row["drug"].get_date_format($row["date_added"])."\">(".$row["date_added2"].")</span>";
					}
					$presc .= $row["drug"].$presDate."<br>";
				}
			}
			return $presc;
		}
		
		public function ptDiag_isPtDilated($patient_id,$formID){
			$ret = ""; $str = "";
			$sql = "SELECT sumDilation_od,sumDilation_os,statusElem, dilated_time FROM chart_dialation where patient_id = '".$patient_id."' AND form_id='".$formID."' AND purged='0' ";
			$row=sqlQuery($sql);
			if($row!=false){
				if((!empty($row["sumDilation_od"]) || !empty($row["sumDilation_os"])) && !empty($row["statusElem"]) ){			
					//if(!empty($row["dilated_time"])){  $str .= "Dilation Time: ".$row["dilated_time"]."\n";  }
					if(!empty($row["sumDilation_od"])){  $str .= "OD: ".str_replace( '<br/>', "\n", $row["sumDilation_od"])."\n";  }
					if(!empty($row["sumDilation_os"])){  $str .= "OS: ".str_replace(  '<br/>', "\n", $row["sumDilation_os"])."\n";  }	
					//$ret = 1;	
				}
				
				if(!empty($str)){
					$ar_pt_dilate_tmp = str_replace(array("OD:", "OS:"), array("<b class=\"od\">OD:</b>", "<b class=\"os\">OS:</b>"), $str);					
					//$ret .= "<br/>";
					$ret .= "<b style=\"color:purple;cursor:pointer;\" title=\"".$str."\" data-toggle=\"tooltip\">Pt Dilated</b>";
					$ret .= "<p>".nl2br($ar_pt_dilate_tmp)."</p>";
				}
			}
			
			//IM 1645
			$str="";
			$sql = "SELECT spOODTime,sumOOD_od,sumOOD_os,statusElem FROM chart_ood where patient_id = '".$patient_id."' AND form_id='".$formID."' AND purged='0' ";
			$row=sqlQuery($sql);
			if($row!=false){
				if((!empty($row["sumOOD_od"]) || !empty($row["sumOOD_os"])) && !empty($row["statusElem"]) ){
					//if(!empty($row["dilated_time"])){  $str .= "Dilation Time: ".$row["dilated_time"]."\n";  }
					if(!empty($row["sumOOD_od"])){  $str .= "OD: ".str_replace( '<br/>', "\n", $row["sumOOD_od"])."\n";  }
					if(!empty($row["sumOOD_os"])){  $str .= "OS: ".str_replace(  '<br/>', "\n", $row["sumOOD_os"])."\n";  }	
					//$ret = 1;
				}
				
				if(!empty($str)){
					$ar_pt_dilate_tmp = str_replace(array("OD:", "OS:"), array("<b class=\"od\">OD:</b>", "<b class=\"os\">OS:</b>"), $str);					
					if(!empty($ret)){ $ret .= "<br/>"; }
					$ret .= "<b style=\"color:purple;cursor:pointer;\" title=\"".$str."\" data-toggle=\"tooltip\">OOD</b>";
					$ret .= "<p>".nl2br($ar_pt_dilate_tmp)."</p>";					
				}
			}
			
			return $ret; //array($ret, $str);
		}
		
		public function fu_getXmlValsArr($xfup=""){
			$arr = array();

			if(!empty($xfup)){
				//echo "<xmp>".$xfup."</xmp>";
				$ox = simplexml_load_string($xfup);
				$len = count($ox->fu);
				if($len > 0){
					foreach($ox->fu as $fux){
						if(!empty($fux->number)){
							$arrTmp = array();
							$arrTmp["number"] = "".$fux->number;
							$arrTmp["time"] = "".$fux->time;
							$arrTmp["visit_type"] = "".$fux->visit_type;
							$arrTmp["provider"] = "".$fux->provider;
							$arrTmp["chk_str"] = $this->getChkStr($arrTmp["number"], $arrTmp["time"], $arrTmp["visit_type"]);
							$arr[] = $arrTmp;
						}
					}
				}
			}

			return array($len, $arr);
		}
		
		public function getChkStr($nm, $tm, $vt){
			$nm = trim("".$nm);
			$tm = trim($tm);
			$vt = trim($vt);
			if(!empty($nm)||!empty($tm)||!empty($vt)){
				$str = $nm."-".$tm."-".$vt;
			}else{
				$str = "";
			}
			return $str;
		}
		
		public function getChartDrawing($fid,$mode="",$fileType="",$pid=''){
			$OBJDrawingData = new CLSDrawingData();
			if(empty($pid)||$pid==''){$pid=$this->patient_id;}
			$drawdocId=$flg=0;
			$drawapp="";
			$sql = "SELECT 
				  c2.id as rv_id, 
				  c2.idoc_drawing_id AS drw_idoc_rv,
				  c2.exm_drawing AS drw_rv_2, c2.statusElem as statusElemRV,
				  c3.id as sle_id,
				  c3.idoc_drawing_id AS drw_idoc_sle,
				  c3.exm_drawing AS drw_sle_2, c3.statusElem as statusElemSLE,
				  c4.gonio_id,
				  c4.idoc_drawing_id AS drw_idoc_gon,
				  c4.gonio_od_drawing AS drw_gon_2, c4.statusElem as statusElemGonio,
				  c5.id as la_id,
				  c5.idoc_drawing_id AS drw_idoc_la,
				  c5.exm_drawing AS drw_la_2, c5.statusElem as statusElemLA,
				  c6.ee_id,
				  c6.ee_drawing AS drw_ee_2, c6.statusElem as statusElemEE
				  FROM chart_master_table c1
				  LEFT JOIN chart_drawings c2 ON c2.form_id = c1.id  AND c2.purged='0' AND c2.exam_name='FundusExam' AND ( c2.statusElem LIKE '%elem_chng_div5_Od=1%' OR c2.statusElem LIKE '%elem_chng_div5_Os=1%' )
				  LEFT JOIN chart_drawings c3 ON c3.form_id = c1.id AND c3.purged='0'  AND c3.exam_name='SLE' AND (c3.statusElem LIKE '%elem_chng_div6_Od=1%' OR c3.statusElem LIKE '%elem_chng_div6_Os=1%')
				  LEFT JOIN chart_gonio c4 ON c4.form_id = c1.id AND c4.purged='0'  AND (c4.statusElem LIKE '%elem_chng_divIop3_Od=1%' OR c4.statusElem LIKE '%elem_chng_divIop3_Os=1%')
				  LEFT JOIN chart_drawings c5 ON c5.form_id = c1.id AND c5.purged='0' AND c5.exam_name='LA'  AND (c5.statusElem LIKE '%elem_chng_div5_Od=1%' OR c5.statusElem LIKE '%elem_chng_div5_Os=1%')
				  LEFT JOIN chart_external_exam c6 ON c6.form_id = c1.id AND c6.purged='0'  AND (c6.statusElem LIKE '%elem_chng_divDraw_Od=1%' OR c6.statusElem LIKE '%elem_chng_divDraw_Os=1%')
				  WHERE c1.id='".$fid."' AND c1.patient_id='".$pid."'
				";
				
			//echo "<br/><br/>".$sql;	
			$rv_id_arc=$sle_id_arc=$la_id_arc="";
			
			$row=sqlQuery($sql);
			if($row != false){
				extract($row);
				
				//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
				if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
					if(empty($rv_id)){
						$sql = "SELECT  id as rv_id, 
								idoc_drawing_id AS drw_idoc_rv,
								exm_drawing AS drw_rv_2, statusElem as statusElemRV FROM chart_drawings_archive 
								WHERE form_id='".$fid."' AND patient_id='".$pid."' 
								AND purged='0' AND exam_name='FundusExam' AND ( statusElem LIKE '%elem_chng_div5_Od=1%' OR statusElem LIKE '%elem_chng_div5_Os=1%' )   ";
						$row=sqlQuery($sql);
						if($row != false){ extract($row); $rv_id_arc='_archive'; }
					}
					
					if(empty($sle_id)){
						$sql = "SELECT  id as sle_id,
								  idoc_drawing_id AS drw_idoc_sle,
								  exm_drawing AS drw_sle_2, statusElem as statusElemSLE FROM chart_drawings_archive 
								WHERE form_id='".$fid."' AND patient_id='".$pid."' 
								AND purged='0'  AND exam_name='SLE' AND (statusElem LIKE '%elem_chng_div6_Od=1%' OR statusElem LIKE '%elem_chng_div6_Os=1%')   ";
						$row=sqlQuery($sql);
						if($row != false){ extract($row); $sle_id_arc='_archive'; }
					}
					
					if(empty($la_id)){
						$sql = "SELECT id as la_id,
								idoc_drawing_id AS drw_idoc_la,
								exm_drawing AS drw_la_2, statusElem as statusElemLA FROM chart_drawings_archive 
								WHERE form_id='".$fid."' AND patient_id='".$pid."' 
								AND purged='0' AND exam_name='LA'  AND (statusElem LIKE '%elem_chng_div5_Od=1%' OR statusElem LIKE '%elem_chng_div5_Os=1%')  ";
						$row=sqlQuery($sql);
						if($row != false){ extract($row); $la_id_arc='_archive'; }
					}
				}
				//-------- End ----------------------------------------------------------------------------------
				
				if($flg==0 && !empty($drw_idoc_rv)){
					$flg=1;
					$drawdocId=$rv_id;
					$enm_var="RV";
					
					//
					//echo "C1:";
					
					$tmp = $OBJDrawingData->isExamDrawingExits($pid,$fid,$drawdocId,$enm_var);
					
					//echo "D".$tmp.";";
					
					if(empty($tmp)){
						$flg=0;
					}
					
				}else	if($flg==0 && !empty($drw_rv_2)){
					$flg=1;
					$drawapp=$drw_rv_2;
					$enm_var="RV";
					$idExm=$rv_id;
				}
				if($flg==0 && !empty($drw_idoc_sle)){
					$flg=1;
					$drawdocId=$sle_id;
					$enm_var="SLE";			
					
					//
					$tmp=$OBJDrawingData->isExamDrawingExits($pid,$fid,$drawdocId,$enm_var);
					if(empty($tmp)){
						$flg=0;
					}
				}else	if($flg==0 && !empty($drw_sle_2)){
					$flg=1;
					$drawapp=$drw_sle_2;
					$enm_var="SLE";
					$idExm=$sle_id;			
					
				}
				if($flg==0 && !empty($drw_idoc_gon)){
					$flg=1;
					$drawdocId=$gon_id;
					$enm_var="Gonio";
					
					//
					$tmp = $OBJDrawingData->isExamDrawingExits($pid,$fid,$drawdocId,$enm_var);
					if(empty($tmp)){
						$flg=0;
					}
				}else	if($flg==0 && !empty($drw_gon_2)){
					$flg=1;
					$drawapp=$drw_gon_2;
					$enm_var="Gonio";
					$idExm=$gon_id;
					
					
				} 
				if($flg==0 && !empty($drw_idoc_la)){
					$flg=1;
					$drawdocId=$la_id;
					$enm_var="LA";
					
					//
					$tmp = $OBJDrawingData->isExamDrawingExits($pid,$fid,$drawdocId,$enm_var);
					if(empty($tmp)){
						$flg=0;
					}
				}else if($flg==0 && !empty($drw_la_2)){
					$flg=1;
					$drawapp=$drw_la_2;
					$enm_var="LA";
					$idExm=$la_id;
					
					
				}
				if($flg==0 && !empty($drw_ee_2)){
					$flg=1;
					$drawapp=$drw_ee_2;
					$enm_var="EE";
					$idExm=$ee_id;			
					
				}
			}
			
			
			//echo($drawdocId." - ".$idExm." - ".$enm_var);
			
			
			if($mode=="check"){	
				return $flg;
			}else{
				$strDrw="";
				$oSaveFile = new SaveFile($pid);		
				if(!empty($drawdocId)){	
					
					$OBJDrawingData = new CLSDrawingData();
					global $objImageManipulation;
					$objImageManipulation = new CLSImageManipulation();
					
					$exm_idDrwingImage =$OBJDrawingData->getIDOCdrawingsImage($enm_var,$primaryId=$drawdocId) ;
					//exit("113344".$exm_idDrwingImage."--");
					if($exm_idDrwingImage!=""){
						
						//$strDrw .= getDrwThumb($enm_var,$exm_idDrwingImage);
						if($fileType=="full"){
							$strDrw = $exm_idDrwingImage;
						}else{
							$pthDrwingImage = $oSaveFile->createThumbs($exm_idDrwingImage,"",70,70);
							$strDrw = $oSaveFile->getFilePath($pthDrwingImage, $type="i");
						}
						
					}else{
						$strDrw = $GLOBALS['srcdir']."/images/tpixel.gif";
					}
				}else if(!empty($drawapp)){//Applet Drawing
					
					if(strpos($drawapp,"0-0-0:")===false){
						$im = imagecreatefromstring(base64_decode($drawapp));
						if ($im != false) {
							$fileNameTempOldData = dirname(__FILE__)."/tmp/".time()."-".session_id().".png";
							imagepng($im,$fileNameTempOldData);
							if($fileType=="full"){
								$strDrw =	$fileNameTempOldData;
							}else{
								//$strDrw .= getDrwThumb($enm_var,$fileNameTempOldData);
								$pthDrwingImage = $oSaveFile->createThumbs($fileNameTempOldData,"",70,70);
								$strDrw = $oSaveFile->getFilePath($pthDrwingImage, $type="i");
								unlink($fileNameTempOldData);
							}
						}else{
							$strDrw = $GLOBALS['incdir']."/chart_notes/images/tpixel.gif";
						}				
					}else{
						//
						if($OBJDrawingData->isAppletModified($drawapp)){
							if($enm_var=="LA"){
								$tableLA = 'chart_drawings'.$la_id_arc;
								$idNameLA = 'id';
								$pixelLaDrawing = 'exm_drawing';
								$imageLA = realpath(dirname(__FILE__).'/../../../images/La.jpg');//'../../../images/La.jpg';
								$altLA = 'LA'; 
								$idLa = $idExm;
							}else if($enm_var=="SLE"){
								//Sle
								$idLA = $idExm;
								$tableLA = 'chart_drawings'.$sle_id_arc;
								$idNameLA = 'id';
								$pixelLaDrawing = 'exm_drawing';
								$imageLA = realpath(dirname(__FILE__).'/../../../images/pic_con_od.jpg');
								$altLA = 'SLE';
							}else if($enm_var=="RV"){
								$idLA = $rv_id;
								$tableLA = 'chart_drawings'.$rv_id_arc;
								$idNameLA = 'id';
								$pixelLaDrawing = 'exm_drawing';
								$imageLA = realpath(dirname(__FILE__).'/../../../images/LeftEyeOpticNerve.jpg');
								$altLA = 'RV'; 
							
							}else if($enm_var=="Gonio"){
								$idLA = $gon_id;
								$tableLA = 'chart_gonio';
								$idNameLA = 'gon_id';
								$pixelLaDrawing = 'gonio_od_drawing';
								$imageLA = realpath(dirname(__FILE__).'/../../../images/bgImage6.png');
								$altLA = 'Gonio';
							
							}else if($enm_var=="EE"){
								$idLA = $idExm;
								$tableLA = 'chart_external_exam';
								$idNameLA = 'ee_id';
								$pixelLaDrawing = 'ee_drawing';
								$imageLA = realpath(dirname(__FILE__).'/../../images/pic_face.jpg');
								$altLA = 'EE';
							}
							$saveImg="1"; 
							getAppletImage($idLa,$tableLA,$idNameLA,$pixelLaDrawing,$imageLA,$altLA,$saveImg);
							global $gdFilename;
							$jppth=$GLOBALS['incdir']."/main/html2pdfprint/".$gdFilename;
							if(file_exists($jppth)){
								$im=imagecreatefromjpeg($jppth);
								if ($im != false) {
									$fileNameTempOldData = dirname(__FILE__)."/tmp/".time()."-".session_id().".png";
									imagepng($im,$fileNameTempOldData);
									if($fileType=="full"){
										$strDrw =	$fileNameTempOldData;
									}else{
										//$strDrw .= getDrwThumb($enm_var,$fileNameTempOldData);
										$pthDrwingImage = $oSaveFile->createThumbs($fileNameTempOldData,"",70,70);
										$strDrw = $oSaveFile->getFilePath($pthDrwingImage, $type="i");
										unlink($fileNameTempOldData);
									}
								}else{
									$strDrw = $GLOBALS['incdir']."/chart_notes/images/tpixel.gif";
								}
								unlink($jppth);
							}
						}
					}
				}
				return $strDrw;
			}
		}
		
		public function getArcOcuMedHx($pid, $fid,$medication = ''){
			$flg=0;
			$arrC=array();
			$sql = "SELECT lists FROM chart_genhealth_archive WHERE patient_id='".$pid."' AND form_id='".$fid."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$arrC_tmp = array();	
				$arrLists = unserialize($row["lists"]);		
				
				$medQryRes = $arrLists[4];
				$len=count($medQryRes);$arrFields=array();
				for($m=0;$m<$len;$m++){
					if($medQryRes[$m]['allergy_status']!='Active'){continue;}				
					$med_name = ucfirst($medQryRes[$m]['title']);
					
					//$med_destination = $medQryRes[$m]['destination'];
					if($medQryRes[$m]['sites'] == 3){
						$site = "OU";
					}elseif($medQryRes[$m]['sites'] == 2){
						$site = "OD";
					}elseif($medQryRes[$m]['sites'] == 1){
						$site = "OS";
					}elseif($medQryRes[$m]['sites'] == 4){
						$site = "PO";
					}else $site = '';
					$med_sig = $medQryRes[$m]['sig'];
					$dosage = $medQryRes[$m]['destination'];
					$comment = $medQryRes[$m]['med_comments'];
					$compliant = $medQryRes[$m]['compliant'];
					$date = $medQryRes[$m]['date'];
					$tmp ="";
					$tmp = trim($med_name);
					if(!empty($dosage))
					$tmp .=" ".$dosage;
					if(!empty($site))
					$tmp .=" ".$site;
					if(!empty($med_sig))
					$tmp .=" ".$med_sig;
					if(!empty($comment))
					$tmp .="; ".$comment;
					
					$bgdate = $medQryRes[$m]['begdate'];
					$arrFields[$med_name]['compliant']= $compliant;
					$arrFields[$med_name]['date']= $date;
					if(!empty($bgdate)){
						$arrC_tmp[$bgdate][]=array($tmp,$med_name,$compliant,$date);
					}else{
						$arrC_tmp["0000-00-00"][]=array($tmp,$med_name." ".$site,$compliant,$date);
					}
				}		

				//Check for Duplicacy
				if(count($arrC_tmp)>0){
					$arrUniqueCheck = array();
					//sort by key
					krsort($arrC_tmp);
					$arrC=array();
					foreach($arrC_tmp as $key=>$val){
						if(count($val)>0){
							foreach($val as $key2=>$val2){
								$med_name=$val2[1];
								if(!empty($med_name) && in_array($med_name,$arrUniqueCheck)){continue;}
								$arrUniqueCheck[]=$med_name;
								$arrC[]=$val2[0];
							}	
						}
					}
				}		
				
				if(count($arrC)>0){
					$flg=1;
				}
				
			}
			return array($arrC,$flg,$arrFields);
		}
		
		public function getChartRx($fid,$mode="",$pid=""){
			if(!$pid)$pid=$this->patient_id;
			$flg=0;
			$arrC=array();
			$sql="SELECT c2.ocularMeds  FROM chart_master_table c1
				LEFT JOIN chart_left_provider_issue c2  ON  c2.form_id=c1.id
				WHERE c1.patient_id='".$pid."' AND c1.id='".$fid."' ";
			$row = sqlQuery($sql);
			if($row!=false&&!empty($row["ocularMeds"])){
				$om_c=$row["ocularMeds"];
				$sepOctMeds = "<+OMeds&%+>";
				$arrC = (!empty($om_c)) ? explode($sepOctMeds,$om_c) : array();
				$arrC=array_values(array_filter($arrC));
				if(count($arrC)>0){
					$flg=1;
				}
			}
			
			//Check From Med Hx Archeive Table also : bcz values are note saved in chart notes now
			if($flg==0){
				list($arrC,$flg)=$this->getArcOcuMedHx($pid, $fid);
			}
			//--
			
			if($mode=="check"){
				return $flg;
			}else{
				//--
				
				//
				if(count($arrC) > 0){ //date:2012-10-03
					foreach($arrC as $x => $y){				
						if(!empty($y)){
							//Fix date
							$z = strrpos($y,"date:");
							if($z !== false){
								$str1 = substr($y, 0, $z);
								$str2 = substr($y, $z);
								$str2 = str_replace("date:", "", $str2);
								$str2 = trim($str2);						
								$str2 = wv_formatDate($str2);
								$arrC[$x] = $str1." date:".$str2;
							}
						}				
					}			
				}		
				
				return $arrC;
				//--
			}
		}
		
		public function getMIop($str, $callfrom=""){
			$arr=array();
			$strAll="";
			$tmpiop = unserialize($str);
			
			//echo "<br/>";
			//print_r($tmpiop);
			
			//
			$len = count($tmpiop);
			for($k=0;$k<$len;$k++){

				$iter = ($k==0) ? "" : $k+1;
				$iter1 = ($iter=="") ? "" : $k;
				//Ta
				if($tmpiop["multiplePressuer".$iter]["elem_applanation".$iter1] == 1){

					$tta_od = $tmpiop["multiplePressuer".$iter]["elem_appOd".$iter1];
					$tta_os = $tmpiop["multiplePressuer".$iter]["elem_appOs".$iter1];
					$tta_time = $tmpiop["multiplePressuer".$iter]["elem_appTime".$iter1];
					$tta_dsc = trim($tmpiop["multiplePressuer".$iter]["elem_descTa".$iter1]);
					$tta_method = trim($tmpiop["multiplePressuer".$iter]["elem_appMethod".$iter1]);
					$tta_method = !empty($tta_method) ? $tta_method : "T<sub><b>A</b></sub>";

					if(!empty($tta_od) || !empty($tta_os)){
						$arr["applanation"]="1";				
						if($callfrom=="GFS"){
						if(!empty($tta_od)){$arr["app_od"] = $tta_od;}
						if(!empty($tta_os)){$arr["app_os_1"] = $tta_os;}				
						}else{
						$arr["app_od"] = (!empty($tta_od)) ? $tta_od : "";
						$arr["app_os_1"] = (!empty($tta_os)) ? $tta_os : "";
						}
						$arr["app_time"] = (!empty($tta_time)) ? $tta_time : "";
						$strAll .= "".$tta_method.":<span class=\"pag_iop\">".$arr["app_od"]."</span>, <span class=\"pag_iop\">".$arr["app_os_1"]."</span> <span class=\"pag_iopT pag_iop \">".$arr["app_time"]."</span><br/>";
					}else if(!empty($tta_dsc)){
						$strAll .= "".$tta_method.":<span class=\"pag_iop\">".$tta_dsc."</span> <span class=\"pag_iopT pag_iop \">".$arr["app_time"]."</span><br/>";
					}
				}

				//Tp
				if($tmpiop["multiplePressuer".$iter]["elem_puff".$iter1] == 1){

					$ttp_od = $tmpiop["multiplePressuer".$iter]["elem_puffOd".$iter1];
					$ttp_os = $tmpiop["multiplePressuer".$iter]["elem_puffOs".$iter1];
					$ttp_time = $tmpiop["multiplePressuer".$iter]["elem_puffTime".$iter1];
					$ttp_dsc = trim($tmpiop["multiplePressuer".$iter]["elem_descTp".$iter1]);
					$tta_method = trim($tmpiop["multiplePressuer".$iter]["elem_puffMethod".$iter1]);
					$tta_method = !empty($tta_method) ? $tta_method : "T<sub><b>P</b></sub>";

					if(!empty($ttp_od) || !empty($ttp_os)){
						$arr["puff"]="1";
						
						if($callfrom=="GFS"){
						if(!empty($ttp_od)){ $arr["puff_od"] =  $ttp_od ;}
						if(!empty($ttp_os)){$arr["puff_os_1"] =  $ttp_os ;}				
						}else{
						$arr["puff_od"] = (!empty($ttp_od)) ? $ttp_od : "";
						$arr["puff_os_1"] = (!empty($ttp_os)) ? $ttp_os : "";
						}
						$arr["puff_time"] = (!empty($ttp_time)) ? $ttp_time : "";
						$strAll .= "".$tta_method.":<span class=\"pag_iop\">".$arr["puff_od"]."</span>, <span class=\"pag_iop\">".$arr["puff_os_1"]."</span> <span class=\"pag_iopT pag_iop \">".$arr["puff_time"]."</span><br/>";
					}else if(!empty($ttp_dsc)){
						$strAll .= "".$tta_method.":<span class=\"pag_iop\">".$ttp_dsc."</span> <span class=\"pag_iopT pag_iop \">".$arr["puff_time"]."</span><br/>";
					}
				}
				
				//Tx
				if($tmpiop["multiplePressuer".$iter]["elem_tx".$iter1] == 1){

					$ttp_od = $tmpiop["multiplePressuer".$iter]["elem_appTrgtOd".$iter1];
					$ttp_os = $tmpiop["multiplePressuer".$iter]["elem_appTrgtOs".$iter1];
					$ttp_time = $tmpiop["multiplePressuer".$iter]["elem_xTime".$iter1];
					$ttp_dsc = trim($tmpiop["multiplePressuer".$iter]["elem_descTx".$iter1]);
					$tta_method = trim($tmpiop["multiplePressuer".$iter]["elem_tactMethod".$iter1]);
					$tta_method = !empty($tta_method) ? $tta_method : "T<sub><b>X</b></sub>";
					
					if(!empty($ttp_od) || !empty($ttp_os)){
						$arr["tx"]="1";				
						if($callfrom=="GFS"){
						if(!empty($ttp_od)){ $arr["tx_od"] =  $ttp_od ;}
						if(!empty($ttp_os)){$arr["tx_os"] =  $ttp_os ;}
						}else{
						$arr["tx_od"] = (!empty($ttp_od)) ? $ttp_od : "";
						$arr["tx_os"] = (!empty($ttp_os)) ? $ttp_os : "";
						}
						$arr["tx_time"] = (!empty($ttp_time)) ? $ttp_time : "";
						$strAll .= "".$tta_method.":<span class=\"pag_iop\">".$arr["tx_od"]."</span>, <span class=\"pag_iop\">".$arr["tx_os"]."</span> <span class=\"pag_iopT pag_iop \">".$arr["tx_time"]."</span><br/>";
					}else if(!empty($ttp_dsc)){
						$strAll .= "".$tta_method.":<span class=\"pag_iop\">".$ttp_dsc."</span> <span class=\"pag_iopT pag_iop \">".$arr["tx_time"]."</span><br/>";
					}
				}
				
				//Tt
				if($tmpiop["multiplePressuer".$iter]["elem_tt".$iter1] == 1){

					$ttp_od = $tmpiop["multiplePressuer".$iter]["elem_tactTrgtOd".$iter1];
					$ttp_os = $tmpiop["multiplePressuer".$iter]["elem_tactTrgtOs".$iter1];
					$ttp_time = $tmpiop["multiplePressuer".$iter]["elem_ttTime".$iter1];
					$ttp_dsc = trim($tmpiop["multiplePressuer".$iter]["elem_descTt".$iter1]);
					$tta_method = trim($tmpiop["multiplePressuer".$iter]["elem_ttMethod".$iter1]);
					$tta_method = !empty($tta_method) ? $tta_method : "T<sub><b>t</b></sub>";
					
					if(!empty($ttp_od) || !empty($ttp_os)){
						$arr["tt"]="1";				
						if($callfrom=="GFS"){
						if(!empty($ttp_od)){ $arr["tt_od"] =  $ttp_od ;}
						if(!empty($ttp_os)){$arr["tt_os"] =  $ttp_os ;}
						}else{
						$arr["tt_od"] = (!empty($ttp_od)) ? $ttp_od : "";
						$arr["tt_os"] = (!empty($ttp_os)) ? $ttp_os : "";
						}
						$arr["tt_time"] = (!empty($ttp_time)) ? $ttp_time : "";
						$strAll .= "".$tta_method.":<span class=\"pag_iop\">".$arr["tt_od"]."</span>, <span class=\"pag_iop\">".$arr["tt_os"]."</span> <span class=\"pag_iopT pag_iop \">".$arr["tt_time"]."</span><br/>";
					}else if(!empty($ttp_dsc)){
						$strAll .= "".$tta_method.":<span class=\"pag_iop\">".$ttp_dsc."</span> <span class=\"pag_iopT pag_iop \">".$arr["tt_time"]."</span><br/>";
					}
				}
				
			}
			
			//Join All summary --
			if($strAll!=""){
				$arr["iop"]=$strAll;
			}
			//Join All summary --
			
			return $arr;
		}
		
		public function getCnsltLtrInfo($patientId, $formID){
			$ret = "";
			$sql = " select templateName, patient_consult_letter_to, patient_consult_letter_to_other,  
						CONCAT(c2.FirstName, ' ', c2.MiddleName, ' ', c2.LastName) as cc1,
						CONCAT(c3.FirstName, ' ', c3.MiddleName, ' ', c3.LastName) as cc2,
						CONCAT(c4.FirstName, ' ', c4.MiddleName, ' ', c4.LastName) as cc3				
						from patient_consult_letter_tbl c1
					LEFT JOIN refferphysician c2 ON c2.physician_Reffer_id = c1.cc1_ref_phy_id
					LEFT JOIN refferphysician c3 ON c3.physician_Reffer_id = c1.cc2_ref_phy_id
					LEFT JOIN refferphysician c4 ON c4.physician_Reffer_id = c1.cc3_ref_phy_id
					WHERE c1.patient_form_id='".$formID."' AND c1.patient_id='".$patientId."' 
					ORDER BY c1.patient_consult_id DESC ";
			//echo "<br/>".$sql;		
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				$tmp = "";
				if(!empty($row["patient_consult_letter_to"])){  $tmp = $row["patient_consult_letter_to"];  }
				else if(!empty($row["patient_consult_letter_to_other"])){  $tmp = $row["patient_consult_letter_to_other"];  }
				
				if(!empty($tmp)){ $tmp = "Sent to: ".$tmp; }
				
				if(!empty($row["cc1"])){ if(!empty($tmp)){ $tmp.=" ; "; }  $tmp .= "CC1: ".$row["cc1"]; }
				if(!empty($row["cc2"])){ if(!empty($tmp)){ $tmp.=" ; "; } $tmp .= "CC2: ".$row["cc2"]; }
				if(!empty($row["cc3"])){ if(!empty($tmp)){ $tmp.=" ; "; } $tmp .= "CC3: ".$row["cc3"]; }
				
				$ret .= "".$i.". Name: ".$row["templateName"]." ; ".$tmp."\n";
				
			}
			
			if(!empty($ret)){
				$ret = "<div title=\"".$ret."\" style=\"cursor:pointer;color:purple;\"><b>Yes</b></div>";
			}
			return $ret;
		}
		
		public function getChartFacilityFromSchApp($patient_id, $dos){
			$ret=0;
			$sql="select sa_facility_id from schedule_appointments where sa_app_start_date='$dos' and sa_patient_id='$patient_id' and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_start_date,sa_app_starttime asc";
			$row=sqlQuery($sql);
			if($row!=false){
				if(!empty($row["sa_facility_id"])){
					$ret=$row["sa_facility_id"];	
				}
			}
			return $ret;
		}
		
		public function ptDiag_getLastDilated($pid){
			list($lastPtDilation,$formIdDilation,$chartStDilation,$relNumDilation,$curyrDilation) = $this->getLastDoneDilation($pid);
			if(!empty($lastPtDilation)&&!empty($formIdDilation)){
				return "<span class=\"hand_cur\" onclick=\"openDilateCN('".$formIdDilation."', 'Chart Note', '".$chartStDilation."','".$relNumDilation."')\">".$lastPtDilation."</span>";
			}
		}
		
		public function getLastDoneDilation($patient_id){
			$lastPtDilation = "";
			$formIdDilation = "";
			$cyr = "-".date("y");
			$cdt = date("Y-m-d");
			global $oUtifun;
			
			$sql = "SELECT ".
				// "DATE_FORMAT(chart_left_cc_history.date_of_service, '%m-%d-%y') as dosDilation, ".
				 "DATE_FORMAT(chart_master_table.date_of_service, '".get_sql_date_format('','y')."') as dosDilation, ".
				 "chart_dialation.exam_date as dateDilation, ".
				 "DATE_FORMAT(chart_dialation.exam_date, '".get_sql_date_format('','y')."') as dateDilation1, ".
				 "chart_dialation.pheny10, chart_dialation.pheny25, chart_dialation.mydiacyl1, chart_dialation.mydiacyl5, ".
				 "chart_dialation.tropicanide, chart_dialation.cyclogel, chart_dialation.dilated_other,chart_dialation.dilation, ".
				 "chart_dialation.noDilation, chart_dialation.unableDilation, ".
				 "chart_master_table.id AS form_id, ".
				 "chart_master_table.finalize, ".
				 "chart_master_table.releaseNumber ".
				 "FROM chart_master_table ".
				// "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
				 "INNER JOIN chart_dialation ON chart_dialation.form_id = chart_master_table.id AND chart_dialation.purged='0'  ".
				 "WHERE chart_master_table.patient_id='".$patient_id."'  ".
				 "AND chart_master_table.delete_status='0' ".
				 "AND chart_master_table.purge_status='0' ".
				 //"ORDER BY chart_left_cc_history.date_of_service DESC, chart_master_table.id DESC ".
				 //"ORDER BY IFNULL(chart_left_cc_history.date_of_service,DATE_FORMAT(chart_master_table.create_dt,'%Y-%m-%d')) DESC, chart_master_table.id DESC ".
				 "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ".
				 "";
			$rez = sqlStatement($sql);
			for($i=0;$row = sqlFetchArray($rez);$i++){
				
				$flg_dialation_done=0;
				if(!empty($row["dilation"])){			
					$arrD = unserialize($row["dilation"]);
					if(count($arrD)>=1&&(!empty($arrD[0]["dilate"])||!empty($arrD[0]["other_desc"]))){
						$flg_dialation_done=1;
					}
				}		
				
				if( !empty($flg_dialation_done) || (!empty($row["pheny10"]) || !empty($row["pheny25"]) ||
					!empty($row["mydiacyl1"]) || !empty($row["mydiacyl5"]) ||
					!empty($row["tropicanide"]) || !empty($row["cyclogel"]) ||
					!empty($row["dilated_other"]) ||
					!empty($row["noDilation"]) || !empty($row["unableDilation"]) ) 	
					&& (!empty($row["dateDilation"]))){
					$lblNo= (!empty($row["noDilation"]) || !empty($row["unableDilation"])) ? "No " : "";
					
					if(!empty($row["dateDilation1"]) && ( $row["dateDilation1"] != "00-00-00" )){
						$lastPtDilation = $lblNo."Dilation (".$row["dateDilation1"].")";
						$formIdDilation = $row["form_id"];
						$chartStDilation = ($row["finalize"] == "1") ? "Final" : "Active";
						$relNumDilation = $row["releaseNumber"];
						$curyrDilation = isDt12mOld($cdt, $row["dateDilation"])?"0":"1";
						//(strpos($row["dateDilation1"],$cyr) !== false) ? "1" : "0";
						break;
					}
				}
			}
			return array($lastPtDilation,$formIdDilation,$chartStDilation,$relNumDilation,$curyrDilation);
		}
		
		
		public function usrChartLimit_Pag($op,$v=""){ //1for get, 2 for set/
			$u=$_SESSION["authId"];
			if($op=="1"){//get
				$lmt_pag_cn = $this->getUserSettings($u,"lmt_pag_cn");
				return $lmt_pag_cn;	
			}else if($op=="2"){ //set		
				$this->setUserSettings($u,"lmt_pag_cn", $v);
			}
		}
		
		function getUserSettings($u,$sn=''){
			$q = "SELECT user_settings FROM users WHERE id='".$u."' LIMIT 0,1";
			$r = imw_query($q);
			if($r && imw_num_rows($r)==1){
				$rs		= imw_fetch_assoc($r);
				$rsa	= json_decode(html_entity_decode($rs['user_settings']),true);
				if(is_array($rsa)){
					if($sn != '') return $rsa[$sn];
					else return $rsa;
				}
			}
		}

		public function setUserSettings($u,$s,$v){
			$settings = $this->getUserSettings($u);
			if(!is_array($settings)){$settings = array();}
			$settings[$s] = $v;
			$str = htmlentities(json_encode($settings));
			$q = "UPDATE users SET user_settings='".$str."' WHERE id='".$u."'";
			$r = imw_query($q);
			if(!$r){
				return imw_error();
			}else return 'true';
		}
		
		public function getPatientDiagnosis_info($patientId, $flgLmt=0, $st="",$pdg_showpop= 0){
			$ret="";	$strdvs="";
			$sql = "SELECT count(*) as num ".
					"FROM chart_master_table ".
					"WHERE chart_master_table.patient_id = '".$patientId."'
					AND chart_master_table.finalize = '1'
					AND chart_master_table.record_validity = '1'
					AND chart_master_table.delete_status = '0'
					AND chart_master_table.purge_status = '0'			
					ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ";		
			$row = sqlQuery($sql);
			if($row!=false){
				$num = $row["num"];
				$str_t_nm = "(Total ".$num.")";
				if(!empty($flgLmt)){			
					$st=(!empty($st)) ? $st : 0;				
					$st_org = $st;
					$st_end = $st + $flgLmt;
					$st=$st+1;
					if($st_end>$num){ $st_end = $num; }
					$ret = "Showing ".$st." to ".$st_end." of ".$num." record(s)";
					if($num == 0 ) $ret = '';
					//123 --
					$strdvs= "";			
					$jinx = ceil($num/$flgLmt);			
					
					if( $pdg_showpop ){
						
						$start = 0;
						$end = $jinx;
						if( $jinx > 10) {
							$start = ($st_org > ($jinx-5)) ? $st_org-(10-($jinx-$st_org)) : (($st_org > 5) ? ($st_org-5) : 0);
							$end = ($st_org < 5) ?  ($st_org+(10-$st_org)): (($st_org < ($jinx-4)) ? $st_org+5 : $jinx);
							//$start = ($st_org > 5) ? ($st_org-5) : 0;
							//$end = ($st_org < ($jinx-4)) ? $st_org+5 : $jinx;
						}
						for($i=$start;$i<$end;$i++){
							$t = $i*$flgLmt;
							$cls = ($st_org==$t) ? "selected" : "";
							$strdvs .= "<span class=\"num_cnt ".$cls."\" onclick=\"pging_cn_dig('".$t."')\" id=\"sel_".($i+1)."\" >".($i+1)."</span>";
						}
					}
					else {
						for($i=0;$i<$jinx;$i++){
							$t = $i*$flgLmt;
							$cls = ($st_org==$t) ? "selected" : "";
							//$strdvs.="<label class=\"pg ".$cls."\" onclick=\"pging_cn_dig('".$t."')\">".($i+1)."</label>";
							$strdvs.="<option value=\"".$t."\" ".$cls." >".($i+1)."</option>";
						}
					}
					
					
					$lb_1="";$lb_prv="";			
					if(!empty($st_org) && $st_org >= $flgLmt){
						$lb_1 = " onclick=\"pging_cn_dig('0')\" ".($pdg_showpop?"class=\"num_cnt\" ":'');
						$pindx=$st_org-$flgLmt;if($pindx<0){ $pindx=0; }
						$lb_prv = " onclick=\"pging_cn_dig('".$pindx."')\" ".($pdg_showpop?"class=\"num_cnt\" ":'');
					}else{
						$lb_1=" class=\"".($pdg_showpop?"num_cnt hide":'inactive')."\" ";
						$lb_prv=" class=\"".($pdg_showpop?"num_cnt hide":'inactive')."\" ";
					}
					
					$lb_lst="";$lb_nxt="";
					$st_lst=$num - ($num-($jinx-1)*$flgLmt);
					if((!empty($st_org) && $st_org >= $st_lst)||($st_lst<=0)){
						$lb_lst=" class=\"".($pdg_showpop?"num_cnt hide":'inactive')."\" ";
						$lb_nxt=" class=\"".($pdg_showpop?"num_cnt hide":'inactive')."\" ";
					}else{
						$lb_lst = " onclick=\"pging_cn_dig('".$st_lst."')\" ".($pdg_showpop?"class=\"num_cnt\" ":'');
						$pindx=$st_org+$flgLmt;//if($pindx>$st_lst){ $pindx=$st_lst; }
						$lb_nxt = " onclick=\"pging_cn_dig('".$pindx."')\" ".($pdg_showpop?"class=\"num_cnt\" ":'');
					}
					
					$pagingLinks = '';
					if( $pdg_showpop ) {
						$pagingLinks.= "<ul class=\"pagination\" style=\"margin:10px 0;\">";
						$pagingLinks.= "<li id=\"div_pages\">";
						$pagingLinks .= "<span ".$lb_1." ><label class='glyphicon glyphicon-backward'></label></span>";
						$pagingLinks .= "<span ".$lb_prv."><label class='glyphicon glyphicon-triangle-left'></label></span>";
						$pagingLinks.= $strdvs;
						$pagingLinks .= "<span ".$lb_nxt."><label class='glyphicon glyphicon-triangle-right'></label></span>";
						$pagingLinks .= "<span ".$lb_lst."><label class='glyphicon glyphicon-forward'></label></span>";
						$pagingLinks.= "</li>";
						$pagingLinks.= "</ul>";
					}
					else {
						$pagingLinks .= "<label ".$lb_1." class='glyphicon glyphicon-backward'></label> | ";
						$pagingLinks .= "<label ".$lb_prv." class='glyphicon glyphicon-triangle-left'></label>";
						$pagingLinks .= "<select id=\"sl_paging\" onchange=\"pging_cn_dig(this)\" onclick=\"stopClickBubble()\" >".$strdvs."</select>";
						$pagingLinks .= "<label ".$lb_nxt."  class='glyphicon glyphicon-triangle-right'></label> |";
						$pagingLinks .= "<label ".$lb_lst." class='glyphicon glyphicon-forward'></label>";
					}
					
					
					
					if($jinx <= 0 ) $pagingLinks = '';
				}else{
					$ret = "Showing all records ".$str_t_nm;
				}
			}
			
			return array($ret, $pagingLinks);
		}
		
		public function getPatientDiagnosis($patientId, $flgLmt=0, $st=""){
			$sql = "SELECT
					chart_master_table.date_of_service,
					DATE_FORMAT(chart_master_table.date_of_service,'".get_sql_date_format('','')."') AS date_of_service2 ,".
					"chart_assessment_plans.hrt,
					chart_assessment_plans.hrtEye,
					chart_assessment_plans.oct,
					chart_assessment_plans.octEye,
					chart_assessment_plans.avf,
					chart_assessment_plans.avfEye,
					chart_assessment_plans.ivfa,
					chart_assessment_plans.ivfaEye,
					chart_assessment_plans.dfe,
					chart_assessment_plans.dfeEye,
					chart_assessment_plans.photos,
					chart_assessment_plans.photosEye,
					chart_assessment_plans.pachy,
					chart_assessment_plans.pachyEye,
					chart_assessment_plans.cat_iol,
					chart_assessment_plans.catIolEye,
					chart_assessment_plans.yag_cap,
					chart_assessment_plans.yagCapEye,
					chart_assessment_plans.altp,
					chart_assessment_plans.altpEye,
					chart_assessment_plans.pi,
					chart_assessment_plans.piEye,
					chart_assessment_plans.ratinal_laser,
					chart_assessment_plans.ratinalLaserEye,
					chart_assessment_plans.follow_up,
						chart_assessment_plans.follow_up_numeric_value,
					chart_assessment_plans.followUpVistType,
					chart_assessment_plans.followup,
						chart_assessment_plans.retina,
						chart_assessment_plans.neuro_ophth,
						chart_assessment_plans.monitor_ag,
						chart_assessment_plans.id_precation,
						chart_assessment_plans.lid_scrubs_oint,
					chart_assessment_plans.continue_meds,
					chart_assessment_plans.doctor_name,
					chart_assessment_plans.doctorId,
					chart_assessment_plans.sign_coords,
					chart_assessment_plans.cosigner_id,
					chart_assessment_plans.sign_coordsCosigner,
					chart_assessment_plans.assess_plan,
					chart_assessment_plans.plan_notes,
					chart_assessment_plans.commentsForPatient,
					chart_assessment_plans.vst_soc, chart_assessment_plans.soc_desc,
					chart_assessment_plans.id AS chart_AP_id, ".
					
					"chart_vis_master.status_elements AS vis_statusElements, 
					 chart_vis_master.id AS id_chart_vis_master,
					".

					"chart_iop.puff,
					chart_iop.applanation,
					chart_iop.puff_od,
					chart_iop.puff_os_1,
					chart_iop.app_od,
					chart_iop.app_os_1,
					chart_iop.multiple_pressure,
					chart_iop.trgtOd,
					chart_iop.trgtOs,
					chart_iop.tx,
					chart_iop.tx_od,
					chart_iop.tx_os,
					".

					//Gonio
					"
					chart_gonio.gonio_id,
					chart_gonio.gonio_od_summary,
					chart_gonio.gonio_os_summary,
					".

					//Optic
					"chart_optic.od_text,
					chart_optic.os_text, 
					chart_optic.cd_val_od, 
					chart_optic.cd_val_os, 
					".

					//IVFA
					"ivfa.ivfa AS ivfaExam,
					ivfa.ivfa_od,
					ivfa.disc_fundus,
					ivfa.disc_os_od, ".

					//VF NFA Pacy this is required for old data only
					"vf_nfa.vis_fil,
					vf_nfa.vis_rad,
					vf_nfa.scan_laser,
					vf_nfa.sca_rad,
					vf_nfa.pachymeter,
					vf_nfa.pac_rad, ".

					//VF
					"vf.vf_id As vfId, ".

					//NFA
					"nfa.nfa_id,
					nfa.scanLaserEye, ".

					//Pachy
					"pachy.pachy_id ,
					pachy.pachyMeterEye, ".

					//Disc
					"disc.disc_id,
					disc.fundusDiscPhoto,
					disc.photoEye, ".

					//OCT
					"oct.oct_id,
					 oct.scanLaserEye as octEye, ".

					//Topography
					"topography.topo_id,
					 topography.topoMeterEye, ".

					//disc_external
					"disc_external.disc_id AS external_id,
					 disc_external.photoEye AS external_eye, ".

					 //Opth
					 "ophtha.ophtha_id,
					  ophtha.ophtha_os,
					  ophtha.ophtha_od,
					 ".

					"chart_master_table.id AS formID,
					chart_master_table.ptVisit,
					chart_master_table.serverId,
					chart_master_table.memo,
					chart_master_table.facilityid,
					chart_master_table.releaseNumber ".

					"FROM chart_master_table ".
					//"LEFT JOIN chart_left_cc_history ON chart_master_table.id = chart_left_cc_history.form_id ".
					"LEFT JOIN chart_assessment_plans ON chart_master_table.id = chart_assessment_plans.form_id
					LEFT JOIN chart_vis_master ON chart_master_table.id = chart_vis_master.form_id
					".
					"LEFT JOIN vf_nfa ON chart_master_table.id = vf_nfa.form_id ".
					"LEFT JOIN chart_iop ON chart_master_table.id = chart_iop.form_id AND chart_iop.purged='0'
					LEFT JOIN chart_optic ON chart_master_table.id = chart_optic.form_id AND chart_optic.purged='0'
					LEFT JOIN chart_gonio ON chart_master_table.id = chart_gonio.form_id AND chart_gonio.purged='0'

					LEFT JOIN disc ON chart_master_table.id = disc.formId AND disc.purged='0'
					LEFT JOIN nfa ON chart_master_table.id = nfa.form_id AND nfa.purged='0'
					LEFT JOIN pachy ON chart_master_table.id = pachy.formId AND pachy.purged='0'
					LEFT JOIN vf ON chart_master_table.id = vf.formId AND vf.purged='0'
					LEFT JOIN ivfa ON chart_master_table.id = ivfa.form_id AND ivfa.purged='0'
					LEFT JOIN disc_external ON chart_master_table.id = disc_external.formId AND disc_external.purged='0'
					LEFT JOIN oct ON chart_master_table.id = oct.form_id AND oct.purged='0'
					LEFT JOIN topography ON chart_master_table.id = topography.formId AND topography.purged='0'
					LEFT JOIN ophtha ON chart_master_table.id = ophtha.form_id AND ophtha.purged='0'

					WHERE chart_master_table.patient_id = '".$patientId."'
					AND chart_master_table.finalize = '1'
					AND chart_master_table.record_validity = '1'
					AND chart_master_table.delete_status = '0'
					AND chart_master_table.purge_status = '0'
					GROUP BY chart_master_table.id
					ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ";
			
			//getLimit as set in user records	
			if(!empty($flgLmt)){
				$lmt_cn_pag = $flgLmt;
				if(!empty($lmt_cn_pag) && $lmt_cn_pag>0){
					$st = (!empty($st)) ? $st : 0;
					$sql .=" LIMIT ".$st.",  ".$lmt_cn_pag." ";
				}
			}		
			
			$rez = sqlStatement($sql);
			return $rez;
		}
		
		
		//Extra functions
		public function remSiteDxFromAssessment($asmt){
			$asmt = trim($asmt);
			
			//remove comments	
			$indxTmp = strpos($asmt,";");
			if($indxTmp !== false){
				$asmt = substr($asmt,0,$indxTmp);
				$asmt = trim($asmt);
			}
			
			$ptrn = "/\s+(\-\s+(OD|OS|OU)\s+)?\((\s*\w{3}(\.[\w\-]{1,4})?(\,)?)+\)$/";
			if(preg_match($ptrn, $asmt, $pre_match)){
				if(!empty($pre_match[0])){ 
					$ptrn22="/[0-9]+/";					
					if(preg_match($ptrn22, $pre_match[0])){ //check alphanumeric dx code, if not do not remove
						$asmt = preg_replace($ptrn, "", $asmt);	
					}
				}
			}
			return trim($asmt);
		}
		public function in_array_nocase($ndl, $hs, $flgRet=0){
			$ret1=false;
			$ret2=array();
			
			if(count($hs)>0){
			foreach($hs as $key => $val){
				if(strcasecmp($val, $ndl) == 0){
					if($flgRet==1){
						$ret1=true;
						$ret2[]=$key;
					}else{
						return true;
						break;
					}
				}
			}
			}	
			return ($flgRet==0) ? false : array($ret1, $ret2) ;
		}
		
		
		public function get_rvs_info($patient_id){
			$retVal = "";
			$sql = "SELECT ".
				 "noFlashing,noFloaters,vpDistance,vpMidDistance, ".
				 "vpNear,vpGlare,vpOther,irrLidsExternal,".
				 "irrOcular,psSpots,psFloaters,psFlashingLights,".
				 "psAmslerGrid,neuroDblVision, neuroTempArtSymp,neuroVisionLoss,".
				 "neuroHeadaches,neuroMigHead,neuroOther ".
				 "FROM chart_master_table ".
				 "INNER JOIN chart_left_provider_issue ON chart_left_provider_issue.form_id = chart_master_table.id ".
				 "WHERE chart_master_table.patient_id = '".$patient_id."' ".
				 "ORDER BY chart_master_table.id DESC LIMIT 0,1";
			$row = sqlQuery($sql);
			if($row != false){
				$noFlashing = $row["noFlashing"];
				$noFloaters = $row["noFloaters"];
				$vpDistance = $row["vpDistance"];

				$str_vpDis = $this->separateOtherRvs($vpDistance,1);
				$retVal .= (!empty($str_vpDis)) ? $str_vpDis : "";

				$vpMidDistance = $row["vpMidDistance"];
				$str_vpMidDis = $this->separateOtherRvs($vpMidDistance,1);
				$retVal .= (!empty($str_vpMidDis)) ? $str_vpMidDis : "";

				$vpNear = $row["vpNear"];
				$str_vpNear = $this->separateOtherRvs($vpNear,1);
				$retVal .= (!empty($str_vpNear)) ? $str_vpNear : "";

				$retVal = (!empty($retVal)) ? "Difficulty in ".$retVal : "";

				$vpGlare = $row["vpGlare"];
				$retVal .= (!empty($vpGlare)) ? "Causing Poor Vision ".$vpGlare : "";

				$vpOther = $row["vpOther"];
				$str_vpOther = $this->separateOtherRvs($vpOther,1);
				$retVal .= (!empty($str_vpOther)) ? $str_vpOther : "";

				$irrLidsExternal = $row["irrLidsExternal"];
				$str_irrLidsExt = $this->separateOtherRvs($irrLidsExternal,1);
				$retVal .= (!empty($str_irrLidsExt)) ? "Lids - External ".$str_irrLidsExt : "";

				$sepType="<+type%$+>";
				$irrOcular = $row["irrOcular"];
				$arrTemp = explode($sepType,$irrOcular);
				$irrOcularTemp=$arrTemp[0];
				$elem_irrOcuItchingType=$arrTemp[1];
				$elem_irrOcuPresSensType=$arrTemp[2];
				$str_irrOcu = $this->separateOtherRvs($irrOcularTemp,1);
				$retVal .= (!empty($str_irrOcu)) ? "Ocular ".$str_irrOcu : "";

				$psSpots = $row["psSpots"];
				$retVal .= (!empty($psSpots)) ? $psSpots : "";

				$sepFloat="<+Float*&+>";
				$psFloaters = $row["psFloaters"];
				$arrTemp = explode($sepFloat,$psFloaters);
				$strrequestSegFloat=$arrTemp[0];
				$retVal .= (!empty($strrequestSegFloat)) ? "Floaters ".$strrequestSegFloat : "";
				$elemrequestSegFloatCobwebs=$arrTemp[1];
				$elemrequestSegFloatBlackSpots=$arrTemp[2];

				$sepFL = "<+FL@^+>";
				$psFlashingLights = $row["psFlashingLights"];
				$arrTemp = explode($sepFL,$psFlashingLights);
				$strrequestSegFL=$arrTemp[0];
				$retVal .= (!empty($strrequestSegFL)) ? "Flashing Lights ".$strrequestSegFL : "";
				$elemrequestSegFLSparks=$arrTemp[1];
				$elemrequestSegFLBolts=$arrTemp[2];
				$elemrequestSegFLArcs=$arrTemp[3];
				$elemrequestSegFLStrobe=$arrTemp[4];

				$psAmslerGrid = $row["psAmslerGrid"];
				$strrequestSegAmsler = $this->separateOtherRvs($psAmslerGrid,1);
				$retVal .= (!empty($strrequestSegAmsler)) ? "Amsler Grid ".$strrequestSegAmsler : "";

				$neuroDblVision = $row["neuroDblVision"];
				$retVal .= (!empty($neuroDblVision)) ? "Double Vision ".$neuroDblVision : "";

				$neuroTempArtSymp = $row["neuroTempArtSymp"];
				$str_neuroTAS = $this->separateOtherRvs($neuroTempArtSymp,1);
				$retVal .= (!empty($str_neuroTAS)) ? "Temporal Arteritis symptoms ".$str_neuroTAS : "";

				$neuroVisionLoss = $row["neuroVisionLoss"];
				$str_neuroVisLoss = $this->separateOtherRvs($neuroVisionLoss,1);
				$retVal .= (!empty($str_neuroVisLoss)) ? "Loss of Vision ".$str_neuroVisLoss : "";
				$neuroHeadaches = $row["neuroHeadaches"];
				$retVal .= (!empty($neuroHeadaches)) ? "Headaches ".$neuroHeadaches : "";

				$tmpMig="";
				$sepMig = "<+Mig&$+>";
				$neuroMigHead = $row["neuroMigHead"];
				$arrTemp = explode($sepMig,$neuroMigHead);
				$str_neuroMigHead =  $this->separateOtherRvs($arrTemp[0],1);
				$tmpMig .= (!empty($str_neuroMigHead)) ? $str_neuroMigHead : "";
				$str_neuroMigHeadAura =  $this->separateOtherRvs($arrTemp[1],1);
				$tmpMig .= (!empty($str_neuroMigHeadAura)) ? $str_neuroMigHeadAura : "";
				$neuroOther = $row["neuroOther"];
				$tmpMig .= (!empty($neuroOther)) ? $neuroOther : "";

				$retVal .= (!empty($tmpMig)) ? "Migraine Headaches ".$tmpMig : "";

			}
			return !empty($retVal) ? str_replace(",", ", ",$retVal)  : "";
		}
		
		public function get_active_o_test($pid){
			//GET active form id
			$fid = $this->is_chart_opened($pid);
			if($fid != false){
				//get Test Done
				$arrTestDone = $this->getTestsDone($fid);
				$strTestDone = implode(",",$arrTestDone);
				//get Tests Ordered
				list($testing,$sx,$activeTests) = $this->getTestingR2($fid,0,$strTestDone);
				//Get active Orders
				if(!empty($activeTests)){
					$arrActTests = explode("<BR>",$activeTests);
				}
				return $arrActTests;
			}
		}
		
		public function getIntialTop($patient_id){
			$highTaOd = $highTaOs = $highTpOd = $highTpOs = $highTxOd = $highTxOs = NULL;
			$arrInitialTop = array();
			$sql = "SELECT 
				  glaucoma_past_readings.*, 
				  SUBSTRING_INDEX(dateReading,'-',-1) AS strYear,
				  IF(dateReading REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$',CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(dateReading,'-',2),'-',-1) AS SIGNED),0) AS strDate,
				  IF(dateReading REGEXP '^[0-9]{4}$',0,CAST(SUBSTRING_INDEX(dateReading,'-',1) AS SIGNED)) AS strMonth
				  FROM glaucoma_past_readings 
				  WHERE patientId ='".$patient_id."' 				
				  ORDER BY strYear DESC, strMonth DESC, strDate DESC, time_read_mil DESC, id DESC
				 ";
			
			$rez = sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++)
			{
				// High Ta Od
				if(($row["taOd"] > $highTaOd) || (($highTaOd == NULL) && !empty($row["taOd"])))
				{
					$date = get_date_format($row["highTaOdDate"],'mm-dd-yyyy');
					$arrInitialTop["HighTaOd"] = array("id" => $row["id"],"od" => $row["taOd"], 
													  "os" => $row["taOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTaOd = $row["taOd"];
				}
				// High Ta Os
				if(($row["taOs"] > $highTaOs) || (($highTaOs == NULL) && !empty($row["taOs"])))
				{
					$date = get_date_format($row["highTaOsDate"],'mm-dd-yyyy');
					$arrInitialTop["HighTaOs"] = array("id" => $row["id"],"od" => $row["taOd"], 
														"os" => $row["taOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTaOs = $row["taOs"];
				}
				// High Tp Od 
				if(($row["tpOd"] > $highTpOd) || (($highTpOd == NULL) && !empty($row["tpOd"])))
				{
					$date = get_date_format($row["highTpOdDate"],'mm-dd-yyyy');
					$arrInitialTop["HighTpOd"] = array("id" => $row["id"],"od" => $row["tpOd"], 
														"os" => $row["tpOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTpOd = $row["tpOd"];
				}
				// High Tp Os
				if(($row["tpOs"] > $highTpOs) || (($highTpOs == NULL) && !empty($row["tpOs"])))
				{
					$date = get_date_format($row["highTpOsDate"],'mm-dd-yyyy');
					$arrInitialTop["HighTpOs"] = array("id" => $row["id"],"od" => $row["tpOd"], 
														"os" => $row["tpOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTpOs = $row["tpOs"];
				}
				// High Tx Od 
				if(($row["txOd"] > $highTxOd) || (($highTxOd == NULL) && !empty($row["txOd"])))
				{
					$date = get_date_format($row["highTxOdDate"],'mm-dd-yyyy');
					$arrInitialTop["HighTxOd"] = array("id" => $row["id"],"od" => $row["txOd"], 
														"os" => $row["txOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTxOd = $row["txOd"];
				}
				// High Tx Os
				if(($row["txOs"] > $highTxOs) || (($highTxOs == NULL) && !empty($row["txOs"])))
				{
					$date = get_date_format($row["highTxOsDate"],'mm-dd-yyyy');
					$arrInitialTop["HighTxOs"] = array("id" => $row["id"],"od" => $row["txOd"], 
														"os" => $row["txOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTxOs = $row["txOs"];
				}
				
				
				// VF
				if(!empty($row["vfOdSummary"]) || !empty($row["vfOsSummary"]))
				{
					$date = get_date_format($row["vfDate"],'mm-dd-yyyy');
					$arrInitialTop["VF"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
													"od" => $this->getMenuValue($row["vfOdSummary"]), "os" => $this->getMenuValue($row["vfOsSummary"]));
				}			
				// NFA
				if(!empty($row["nfaOdSummary"]) || !empty($row["nfaOsSummary"]))
				{
					$date = get_date_format($row["nfaDate"],'mm-dd-yyyy');	
					$arrInitialTop["NFA"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
													"od" => $this->getMenuValue($row["nfaOdSummary"]), "os" => $this->getMenuValue($row["nfaOsSummary"]));
				}

				// Gonio
				if((!empty($row["gonioOdSummary"]) && ($row["gonioOdSummary"] != "Empty")) || 
					(!empty($row["gonioOsSummary"]) && ($row["gonioOsSummary"] != "Empty")))
				{				
					$date = get_date_format($row["gonioDate"],'mm-dd-yyyy');	
					$arrInitialTop["Gonio"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
													  "od" => $row["gonioOdSummary"], "os" => $row["gonioOsSummary"]);
				}		
				//Pachy			
				$tmpOd = trim($row["pachyOdCorr"]);
				$tmpOs = trim($row["pachyOsCorr"]);			
				if(!empty($tmpOd) || !empty($tmpOs) || !empty($row["pachyOdReads"]) || !empty($row["pachyOsReads"]))
				{
					$date = get_date_format($row["pachyDate"],'mm-dd-yyyy');	
					$arrInitialTop["Pachy"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
													  "od" => array("Read" => $row["pachyOdReads"], "Avg" => $row["pachyOdAvg"], "Corr" => $row["pachyOdCorr"]), "os" => array("Read" => $row["pachyOsReads"], "Avg" => $row["pachyOsAvg"], "Corr" => $row["pachyOsCorr"]));				
				}
				//Disk Photo
				if(($row["diskPhotoOd"] == "Done") || ($row["diskPhotoOs"] == "Done"))
				{
					$date = get_date_format($row["diskPhotoDate"],'mm-dd-yyyy');	
					$arrInitialTop["Disk"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
													  "od" =>$row["diskPhotoOd"] , "os" =>$row["diskPhotoOs"] );				
				}
				//CD
				if(!empty($row["cdOd"]) || !empty($row["cdOs"]))
				{
					$date = get_date_format($row["cdDate"],'mm-dd-yyyy');				
					$arrInitialTop["CD"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
													  "od" =>$row["cdOd"] , "os" =>$row["cdOs"] );				
				}
				//CEE
				if(!empty($row["cee"]))
				{
					$date = get_date_format($row["ceeDate"],'mm-dd-yyyy');            
					$arrInitialTop["CEE"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"], 
													  "cee" => $row["cee"],"notes"=>$row["ceeNotes"]); 				
				}           						
			}
			return $arrInitialTop;		
		}
		
		public function getMenuValue($str){
			$retStr = "";
			$arrCheck = array("Normal","Border Line", "PS", "Increase Abnormal", "Decrease Abnormal", "No Change Abnormal", "Abnormal","Stable");
			if(!empty($str)){
			foreach($arrCheck as $key => $val){			   
					if(strpos($str,$val) !== false){
						$retStr = $val;
						break;
					}
				}
			}
			return $retStr;
		}
		
		public function is_chart_opened($pid){
			$q = "SELECT id FROM chart_master_table 
					WHERE patient_id='".$pid."' AND finalize='0' AND delete_status='0'
					ORDER BY id DESC LIMIT 0,1 ";
			$res = imw_query($q);
			if($res && imw_num_rows($res)==1){
				$rs = imw_fetch_assoc($res);
				return $rs["id"];
			}
			return false;
		}
		
		public function getTestsDone($fid){
			$arrDone = array();
			$sql="SELECT ".
				//vf
				"c2.vf_id AS vfId, ".
				//vf-GL
				"c15.vf_gl_id, ".
				
				//hrt
				"c3.nfa_id,
				c3.scanLaserEye, ".
				//oct
				"c4.oct_id,
				c4.scanLaserEye as octEye, ".
				//oct-rnfl
				"c16.oct_rnfl_id,
				c16.scanLaserEye as oct_rnflEye, ".
				//test_gdx
				"c14.gdx_id,
				c14.scanLaserEye as gdxEye, ".
				//pachy
				"c5.pachy_id,
				c5.pachyMeterEye, ".
				//ivfa
				"c6.ivfa AS ivfaExam,
				 c6.ivfa_od,
				 c6.disc_fundus,
				 c6.disc_os_od, ".
				//disc
				"c7.disc_id,
				c7.fundusDiscPhoto,
				c7.photoEye, ".
				//External
				"c8.disc_id AS external_id,
				c8.photoEye AS external_eye, ".
				//topo
				"c9.topo_id,
				c9.topoMeterEye, ".
				//ophth
				"c10.ophtha_id,
				c10.ophtha_od,
				c10.ophtha_os, ".
				//chart_gonio
				"c11.gonio_id,
				c11.gonio_od_summary, ".
			   //VF_NFA
				"c12.vis_fil,
				c12.vis_rad,
				c12.scan_laser,
				c12.sca_rad,
				c12.pachymeter,
				c12.pac_rad, ".

				//icg
				"c13.icg AS icgExam,
				 c13.icg_od,
				 c13.disc_fundus,
				 c13.disc_os_od, ".
				
				//Chart_master_table
				"c1.id AS formID,
				c1.ptVisit,
				c1.releaseNumber ".

				 "FROM chart_master_table c1 ".

				 "LEFT JOIN vf c2 ON c2.formId = c1.id AND c2.purged='0' AND c2.del_status='0'  ".
				 "LEFT JOIN vf_gl c15 ON c15.formId = c1.id AND c15.purged='0' AND c15.del_status='0'  ".
				 "LEFT JOIN nfa c3 ON c3.form_id = c1.id AND c3.purged='0' AND c3.del_status='0' ".
				 "LEFT JOIN oct c4 ON c4.form_id = c1.id AND c4.purged='0' AND c4.del_status='0' ".
				 "LEFT JOIN oct_rnfl c16 ON c16.form_id = c1.id AND c16.purged='0' AND c16.del_status='0' ".
				 "LEFT JOIN pachy c5 ON c5.formId = c1.id AND c5.purged='0' AND c5.del_status='0' ".
				 "LEFT JOIN ivfa c6 ON c6.form_id = c1.id AND c6.purged='0' AND c6.del_status='0' ".
				 "LEFT JOIN icg c13 ON c13.form_id = c1.id AND c13.purged='0' AND c13.del_status='0' ".
				 "LEFT JOIN disc c7 ON c7.formId = c1.id AND c7.purged='0' AND c7.del_status='0' ".
				 "LEFT JOIN disc_external c8 ON c8.formId = c1.id AND c8.purged='0' AND c8.del_status='0' ".
				 "LEFT JOIN topography c9 ON c9.formId = c1.id AND c9.purged='0' AND c9.del_status='0' ".
				 "LEFT JOIN ophtha c10 ON c10.form_id = c1.id AND c10.purged='0' ".
				 "LEFT JOIN chart_gonio c11 ON c11.form_id = c1.id AND c11.purged='0'  ".
				 "LEFT JOIN vf_nfa c12 ON c12.form_id = c1.id ".
				 "LEFT JOIN test_gdx c14 ON c14.form_id = c1.id AND c14.purged='0' AND c14.del_status='0' ".
				 
				 "WHERE c1.id = '".$fid."' ".
				 "ORDER BY c1.id DESC LIMIT 0,1 ";

			//echo "CHECK: ".$sql;
			$row = sqlQuery($sql);
			if($row != false){

				$formID = $row["formID"];
				$releaseNumber = $row["releaseNumber"];

				$tests = "";
				//VF -----------
				if(($releaseNumber == "0") && ($row["vis_fil"] == "1"))
				{
					$arrDone[] = "VF";
				}else if(($releaseNumber == "1") && (!empty($row["vfId"]))){
					$arrDone[] = "VF";
				}
				//VF -----------
				
				//VF-GL--
				if((!empty($row["vf_gl_id"]))){
					$arrDone[] = "VF-GL";
				}
				
				//HRT -----------
				if(($releaseNumber == "0") && ($row["scan_laser"] == "1"))
				{
					$arrDone[] = "HRT";
				}else if(($releaseNumber == "1") && (!empty($row["scanLaserEye"]))){
					$arrDone[] = "HRT";
				}
				//HRT -----------
				//OCT -----------
				if(!empty($row["oct_id"])){
					$arrDone[] = "OCT";
				}
				//OCT -----------
				//OCT -RNFL-----------
				if(!empty($row["oct_rnfl_id"])){
					$arrDone[] = "OCT-RNFL";
				}
				//OCT -RNFL-----------
				
				//TEST_GDX-----------
				if(!empty($row["gdx_id"])){
					$arrDone[] = "GDX";
				}
				//TEST_GDX-----------		
				//Pachy -----------
				if(($releaseNumber == "0") && ($row["pachymeter"] == "1"))
				{
					$arrDone[] = "Pachy";
				}else if(($releaseNumber == "1") && (!empty($row["pachyMeterEye"]))){
					$arrDone[] = "Pachy";
				}
				//Pachy -----------
				//IVFA -----------
				if((($releaseNumber == "0") && ($row["ivfaExam"] == "on")) || (($releaseNumber == "1") && (!empty($row["ivfa_od"]))))
				{
					$arrDone[] = "IVFA";
				}
				//IVFA -----------

				//ICG -----------
				if((($releaseNumber == "0") && ($row["icgExam"] == "on")) || (($releaseNumber == "1") && (!empty($row["icg_od"]))))
				{
					$arrDone[] = "ICG";
				}
				//ICG -----------		
				
				//Fundus Photo -----------
				//echo "<pre>$releaseNumber:".$row["fundusDiscPhoto"]."</pre>";
				//$tests .= ($row["disc_fundus"] == "1") ? "Fundus Photo" : "";
				//$tests .= ($row["disc_fundus"] == "2") ? "Disc Photo" : "";
				if(($releaseNumber == "0") && ($row["disc_fundus"] == "1"))
				{
					$arrDone[] = "Fundus Photo";
				}else if(($releaseNumber == "1") && ($row["fundusDiscPhoto"] == "1")){
					$arrDone[] = "Fundus Photo";
				}
				//Fundus Photo -----------
				//External -----------
				if(!empty($row["external_id"])){
					$arrDone[] = "External";
				}
				//External -----------
				//Topo -----------
				if(!empty($row["topo_id"])){
					$arrDone[] = "Topography";
				}
				//Topo -----------
				//Ophth -----------
				if(!empty($row["ophtha_id"])){
					$arrDone[] = "Ophth";
				}
				//Ophth -----------

				//Gonio -----------
				if(!empty($row["gonio_id"])){
					$arrDone[] = "Gonio";
				}
				//Gonio -----------

			}
			//Active Test
			return $arrDone;
		}
	
		public function getTestingR2($formID,$pln="0",$tsts=""){
			$numPln = 0;
			$arrRetVal = array();
			$retVal = "";
			$retValSx = "";
			$active_tests = "";
			// Extract Values
			$sql = "SELECT * FROM schedule WHERE form_Id = '$formID' ";
			$sql .= (!empty($pln)) ? "AND pln_id='".$pln."' " : "" ;
			$sql .= "ORDER BY pln_id ";
			$rez = sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++){
				$numPln = $row["pln_id"];
				$tmp = "";
				$tmpSx = "";
				$tmpToday = "";
				$tmpTodaySx = "";
				$tmpDWM = "";
				$tmpDWMSx = "";
				$tmpAsFU = "";
				$tmpAsFUSx = "";
				//Testing
				if(($row["hrt"] == "HRT") ){
					switch( $row["hrtTestTime"] ){
						case "Today":
							$tmpToday .= $row["hrt"]." (".$row["hrtEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= $row["hrt"]." (".$row["hrtEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= $row["hrt"]." (".$row["hrtEye"].")<br>";
						break;
						default:
							$tmpToday .= $row["hrt"]." (".$row["hrtEye"].")<br>";
						break;
					}
					//Active Tests
					if((strpos($tsts,"HRT") === false) && (strpos($active_tests,"HRT") === false)){
						$active_tests .= "HRT<BR>";
					}
				}

				if(($row["oct"] != "") ){
					switch( $row["octTestTime"] ){
						case "Today":
							$tmpToday .= "OCT (".$row["octEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= "OCT (".$row["octEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= "OCT (".$row["octEye"].")<br>";
						break;
						default:
							$tmpToday .= "OCT (".$row["octEye"].")<br>";
						break;
					}
					//Active Tests
					if((strpos($tsts,"OCT") === false) && (strpos($active_tests,"OCT") === false)){
						$active_tests .= "OCT<BR>";
					}
				}

				if(($row["avf"] != "") ){
					switch( $row["avfTestTime"] ){
						case "Today":
							$tmpToday .= "VF (".$row["avfEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= "VF (".$row["avfEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= "VF (".$row["avfEye"].")<br>";
						break;
						default:
							$tmpToday .= "VF (".$row["avfEye"].")<br>";
						break;
					}

					//Active Tests
					if((strpos($tsts,"VF") === false) && (strpos($active_tests,"VF") === false)){
						$active_tests .= "VF<BR>";
					}
				}

				if(($row["ivfa"] == "IVFA") ){
					switch( $row["ivfaTestTime"] ){
						case "Today":
							$tmpToday .= $row["ivfa"]." (".$row["ivfaEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= $row["ivfa"]." (".$row["ivfaEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= $row["ivfa"]." (".$row["ivfaEye"].")<br>";
						break;
						default:
							$tmpToday .= $row["ivfa"]." (".$row["ivfaEye"].")<br>";
						break;
					}

					//Active Tests
					if((strpos($tsts,"IVFA") === false) && (strpos($active_tests,"IVFA") === false)){
						$active_tests .= "IVFA<BR>";
					}
				}

				if(($row["icg"] == "ICG") ){
					switch( $row["icgTestTime"] ){
						case "Today":
							$tmpToday .= $row["icg"]." (".$row["icgEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= $row["icg"]." (".$row["icgEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= $row["icg"]." (".$row["icgEye"].")<br>";
						break;
						default:
							$tmpToday .= $row["icg"]." (".$row["icgEye"].")<br>";
						break;
					}

					//Active Tests
					if((strpos($tsts,"ICG") === false) && (strpos($active_tests,"ICG") === false)){
						$active_tests .= "ICG<BR>";
					}
				}
				
				if((trim($row["dfe"]) == "DFE") ){
					switch( trim($row["dfeTestTime"]) ){
						case "Today":
						$tmpToday .= trim($row["dfe"])."(".trim($row["dfeEye"]).")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= trim($row["dfe"])."(".trim($row["dfeEye"]).")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= trim($row["dfe"])."(".trim($row["dfeEye"]).")<br>";
						break;
						default:
							$tmpToday .= trim($row["dfe"])."(".trim($row["dfeEye"]).")<br>";
						break;
					}

				}
				if(($row["photos"] != "") ){
					$photos = $row["photos"];
					switch($row["photos"]){
						case "Disc":
							$photos = "DP";
						break;
						case "Macula":
							$photos = "MP";
						break;
						case "External":
							$photos = "EP";
						break;
						case "Anterior Segment":
							$photos = "ASP";
						break;
					}

					switch( $row["photosTime"] ){
						case "Today":
							$tmpToday .= $photos." (".$row["photosEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= $photos." (".$row["photosEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= $photos." (".$row["photosEye"].")<br>";
						break;
						default:
							$tmpToday .= $photos." (".$row["photosEye"].")<br>";
						break;
					}
					//Active Tests
					if(($photos == "MP") || ($photos == "DP")){
						if((strpos($tsts,"Fundus Photo") === false) &&
							(strpos($active_tests,"Fundus Photo") === false)){
							$active_tests .= "Fundus Photo<BR>";
						}
					}else if(($photos == "ASP") || ($photos == "EP")){
						if((strpos($tsts,"External") === false) && (strpos($active_tests,"External") === false)){
							$active_tests .= "External<BR>";
						}
					}
				}

				if((trim($row["gonio"])=="Gonio") ){
					switch( trim($row["gonioTestTime"]) ){
						case "Today":
							$tmpToday .= "Gonio (".trim($row["gonioEye"]).")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= "Gonio (".trim($row["gonioEye"]).")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= "Gonio (".trim($row["gonioEye"]).")<br>";
						break;
						default:
							$tmpToday .= "Gonio (".trim($row["gonioEye"]).")<br>";
						break;
					}
					//Active Tests
					if((strpos($tsts,"Gonio") === false) && (strpos($active_tests,"Gonio") === false)){
						$active_tests .= "Gonio<BR>";
					}
				}

				if(($row["pachy"] == "PACHY") ){
					$row["pachy"] = strtolower($row["pachy"]);
					switch( $row["pachyTestTime"] ){
						case "Today":
							$tmpToday .= ucfirst($row["pachy"])." (".$row["pachyEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= ucfirst($row["pachy"])." (".$row["pachyEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= ucfirst($row["pachy"])." (".$row["pachyEye"].")<br>";
						break;
						default:
							$tmpToday .= ucfirst($row["pachy"])." (".$row["pachyEye"].")<br>";
						break;
					}
					//Active Tests
					if((strpos($tsts,"Pachy") === false) && (strpos($active_tests,"Pachy") === false)){
						$active_tests .= "Pachy<BR>";
					}
				}

				if((trim($row["optha"]) == "OPTHA") ){
					switch( trim($row["opthaTestTime"]) ){
						case "Today":
							$tmpToday .= "Ophth (".trim($row["opthaEye"]).")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= "Ophth (".trim($row["opthaEye"]).")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= "Ophth (".trim($row["opthaEye"]).")<br>";
						break;
						default:
							$tmpToday .= "Ophth (".trim($row["opthaEye"]).")<br>";
						break;
					}
					//Active Tests
					if((strpos($tsts,"Ophth") === false) && (strpos($active_tests,"Ophth") === false)){
						$active_tests .= "Ophth<BR>";
					}
				}

				if( !empty($row["other1"]) && !empty($row["other1Value"]) ){ //&& ($row["other1TestTime"] == "Today")
					switch( $row["other1TestTime"] ){
						case "Today":
							$tmpToday .= $row["other1Value"]." (".$row["other1Eye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= $row["other1Value"]." (".$row["other1Eye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= $row["other1Value"]." (".$row["other1Eye"].")<br>";
						break;
						default:
							$tmpToday .= $row["other1Value"]." (".$row["other1Eye"].")<br>";
						break;
					}
				}

				if( !empty($row["other2"]) && !empty($row["other2Value"]) ){ //&& ($row["other2TestTime"] == "Today")
					switch( $row["other2TestTime"] ){
						case "Today":
							$tmpToday .= $row["other2Value"]." (".$row["other2Eye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= $row["other2Value"]." (".$row["other2Eye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= $row["other2Value"]." (".$row["other2Eye"].")<br>";
						break;
						default:
							$tmpToday .= $row["other2Value"]." (".$row["other2Eye"].")<br>";
						break;
					}
				}

				if( !empty($row["other3"]) && !empty($row["other3Value"]) ){ //&& ($row["other3TestTime"] == "Today")
					switch( $row["other3TestTime"] ){
						case "Today":
							$tmpToday .= $row["other3Value"]." (".$row["other3Eye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWM .= $row["other3Value"]." (".$row["other3Eye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFU .= $row["other3Value"]." (".$row["other3Eye"].")<br>";
						break;
						default:
							$tmpToday .= $row["other3Value"]." (".$row["other3Eye"].")<br>";
						break;
					}
				}

				$tmp .= (!empty($tmpToday)) ? "".trim($tmpToday) : "" ;
				$tmp .= (!empty($tmpAsFU)) ? "<b>As F/u:</b><br>".trim($tmpAsFU) : "";
				$tmp .= (!empty($tmpDWM)) ? "".trim($tmpDWM) : "";
				$arrRetVal[$numPln] = ($tmp != "") ? "<div><b>Plan ".($numPln)."</b><br>".$tmp."</div>" : "<div></div>" ;
				//Sx
				if( !empty($row["catIol"]) ){
					switch( $row["catIolTestTime"] ){
						case "Today":
							$tmpTodaySx .= "Cat.IOL(".$row["catIolEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWMSx .= "Cat.IOL(".$row["catIolEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFUSx .= "Cat.IOL(".$row["catIolEye"].")<br>";
						break;
						default:
							$tmpTodaySx .= "Cat.IOL(".$row["catIolEye"].")<br>";
						break;

					}
				}
				if( !empty($row["yagCap"]) ){
					switch( $row["yagCapTestTime"] ){
						case "Today":
							$tmpTodaySx .= "Yag cap.(".$row["yagCapEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWMSx .= "Yag cap.(".$row["yagCapEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFUSx .= "Yag cap.(".$row["yagCapEye"].")<br>";
						break;
						default:
							$tmpTodaySx .= "Yag cap.(".$row["yagCapEye"].")<br>";
						break;
					}
				}
				if( !empty($row["slt"]) ){
					switch( $row["sltTestTime"] ){
						case "Today":
							$tmpTodaySx .= "SLT (".$row["sltEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWMSx .= "SLT (".$row["sltEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFUSx .= "SLT (".$row["sltEye"].")<br>";
						break;
						default:
							$tmpTodaySx .= "SLT (".$row["sltEye"].")<br>";
						break;
					}
				}
				if( !empty($row["pi"]) ){
					switch( $row["piTestTime"] ){
						case "Today":
							$tmpTodaySx .= "PI (".$row["piEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWMSx .= "PI (".$row["piEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFUSx .= "PI (".$row["piEye"].")<br>";
						break;
						default:
							$tmpTodaySx .= "PI (".$row["piEye"].")<br>";
						break;
					}
				}
				if( !empty($row["retinalLaser"]) ){
					switch( $row["retinalLaserTestTime"] ){
						case "Today":
							$tmpTodaySx .= "R.Laser(".$row["retinalLaserEye"].")<br>";
						break;
						case "Days":
						case "Weeks":
						case "Month":
							$tmpDWMSx .= "R.Laser(".$row["retinalLaserEye"].")<br>";
						break;
						case "As/Follow-up":
							$tmpAsFUSx .= "R.Laser(".$row["retinalLaserEye"].")<br>";
						break;
						default:
							$tmpTodaySx .= "R.Laser(".$row["retinalLaserEye"].")<br>";
						break;
					}
				}

				$tmpSx .= (!empty($tmpTodaySx)) ? "".trim($tmpTodaySx) : "" ;
				$tmpSx .= (!empty($tmpAsFUSx)) ? "<b>As F/u:</b><br>".trim($tmpAsFUSx) : "";
				$tmpSx .= (!empty($tmpDWMSx)) ? "".trim($tmpDWMSx) : "";

				$retValSx .= ($tmpSx != "") ? "".$tmpSx."" : "" ;

			}
			for($i=0;$i<$numPln;$i++){
				if(!empty($arrRetVal[$i+1])){
					$retVal .= $arrRetVal[$i+1];
				}else if(empty($pln)){
					$retVal .= "<div></div>";
				}
			}
			return array($retVal, $retValSx,$active_tests);
		}

		public function separateOtherRvs($str){
			$arr=array();
			$elem_other="";
			$sep = "<+O@#+>";
			if(!empty($str))
			{
				$tmp = explode($sep,$str);		
				$arr = explode(",",$tmp[0]);	
				$elem_other = $tmp[1];		
			}
			return array($arr,$elem_other);
		}
		
		
		public function getPtChargestblData($pt_id, $dos){
			$dos = getDateFormatDB($dos);
			$sql="SELECT encounter_id, first_posted_date, totalBalance FROM `patient_charge_list` WHERE `del_status`='0' and `patient_id` ='".$pt_id."' and date_of_service in ('".$dos."')";
			$result_array= array();
			$res = imw_query($sql);
			$rs = imw_fetch_assoc($res);
			$encounter_id = $rs["encounter_id"];
			$first_posted_date =  $rs["first_posted_date"];
			$totalBalance =  $rs["totalBalance"];
			$result_array['encounter_id'] = $encounter_id;
			$result_array['first_posted_date'] = $first_posted_date;
			$result_array['totalBalance'] = $totalBalance;
			return $result_array;
		}
		
		
		public function getPtBilledData($pt_id, $encounter_id){
			$ret=0;
			if(!empty($encounter_id)){
			$sql="SELECT encounter_id FROM submited_record WHERE encounter_id ='$encounter_id' AND patient_id ='$pt_id'";
			$res = imw_query($sql);
			$res = imw_fetch_assoc($res);
			$ret=$res["encounter_id"];
			}
			return $ret;
		}
		
		public function getPtInsData($encounter_id, $paidby){
			$res=false;
			if(!empty($encounter_id)){
			$sql="SELECT paid_by FROM patient_chargesheet_payment_info WHERE encounter_id ='$encounter_id' AND paid_by='$paidby'";
			$res = imw_query($sql);
			$res = imw_fetch_assoc($res);
			}
			return $res;
		}
		
		function show_drawing_img(){
			$formId=$_GET["formId"];
			$filetype=$_GET["filetype"];
			$pid = $this->pid; //(isset($_REQUEST['pid']) && $_REQUEST['pid']!="")?$_REQUEST['pid']:"";
			
			//print_r($_GET);
			
			
			$pth = $this->getChartDrawing($formId,$mode="",$filetype,$pid);
			
			//print_r($pth);exit();
			
			if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
				if(!empty($pth)){
					if(strpos($pth,".gif")!==false){
						$zRemoteServerData["header_content_type"] = 'Content-type: image/gif';
					}else{
						$zRemoteServerData["header_content_type"] = 'Content-type: image/png';
					}
				}else{
					$pth=$GLOBALS['incdir']."/chart_notes/images/tpixel.gif";
					$zRemoteServerData["header_content_type"] = 'Content-type: image/gif';
				}
			}else{
				if(!empty($pth)){
					if(strpos($pth,".gif")!==false){
					header('Content-type: image/gif');
					}else{
					header('Content-type: image/png');
					}
				}else{
					$pth=$GLOBALS['incdir']."/chart_notes/images/tpixel.gif";
					header('Content-type: image/gif');
				}
			}
			if(file_exists($pth)){readfile($pth);}		
		}
		
		function getPtCLInfo($clType = 'scl'){
			$clArray = array();
			$clQuery = "select clm.clws_id, clm.dos, clm.clws_type, clwd.* from contactlensmaster clm join contactlensworksheet_det clwd on clm.clws_id = clwd.clws_id where clm.patient_id = '".$this->patient_id."' and clwd.clType = LOWER('SCL')";
			if($clType == "rgp"){
				$clQuery = "select clm.clws_id, clm.dos, clm.clws_type, clwd.* from contactlensmaster clm join contactlensworksheet_det clwd on clm.clws_id = clwd.clws_id where clm.patient_id = '".$this->patient_id."' and clwd.clType != LOWER('SCL')";
			}
			$clResult = imw_query($clQuery) or die(imw_error()." - ".$clQuery);
			while($clRow = imw_fetch_assoc($clResult)){
				$tempArray = array();
				
				$clVisitType = $clRow['clws_type'];
				$lensType = $clRow['clType'];
				$clEye = $clRow['clEye'];
				
				$tempArray['cl_visit_type'] = $clVisitType;
				$tempArray['lens_type'] = $lensType;
				$tempArray['cl_eye'] = $clEye;
				$tempArray['date_of_service'] = $clRow['dos'];
				if($lensType == "scl"){
					$tempArray['sphere'.$clEye] = $clRow['Sclsphere'.$clEye];
					$tempArray['cylinder'.$clEye] = $clRow['SclCylinder'.$clEye];
					$tempArray['axis'.$clEye] = $clRow['Sclaxis'.$clEye];
					$tempArray['base_curve'.$clEye] = $clRow['SclBcurve'.$clEye];
					$tempArray['diameter'.$clEye] = $clRow['SclDiameter'.$clEye];
					$tempArray['add'.$clEye] = $clRow['SclAdd'.$clEye];
					$tempArray['dva'.$clEye] = $clRow['SclDva'.$clEye];
					$tempArray['nva'.$clEye] = $clRow['SclNva'.$clEye];
					$tempArray['make'.$clEye] = $clRow['SclType'.$clEye];
					$tempArray['id'.$clEye] = $clRow['SclType'.$clEye.'_ID'];
					$tempArray['color'.$clEye] = $clRow['SclColor'.$clEye];
				}else if($lensType == "rgp"){
					$tempArray['sphere'.$clEye] = $clRow['RgpPower'.$clEye];
					$tempArray['cylinder'.$clEye] = $clRow['RgpCylinder'.$clEye];
					$tempArray['axis'.$clEye] = $clRow['RgpAxis'.$clEye];
					$tempArray['base_curve'.$clEye] = $clRow['RgpBC'.$clEye];
					$tempArray['diameter'.$clEye] = $clRow['RgpDiameter'.$clEye];
					$tempArray['optical_zone'.$clEye] = $clRow['RgpOZ'.$clEye];
					$tempArray['center_thickness'.$clEye] = $clRow['RgpCT'.$clEye];
					$tempArray['add'.$clEye] = $clRow['RgpAdd'.$clEye];
					$tempArray['dva'.$clEye] = $clRow['RgpDva'.$clEye];
					$tempArray['nva'.$clEye] = $clRow['RgpNva'.$clEye];
					$tempArray['make'.$clEye] = $clRow['RgpType'.$clEye];
					$tempArray['id'.$clEye.'_ID'] = $clRow['RgpType'.$clEye.'_ID'];
					$tempArray['color'.$clEye] = $clRow['RgpColor'.$clEye];
				}else if($lensType == "cust_rgp"){
					$tempArray['sphere'.$clEye] = $clRow['RgpCustomPower'.$clEye];
					$tempArray['cylinder'.$clEye] = $clRow['RgpCustomCylinder'.$clEye];
					$tempArray['axis'.$clEye] = $clRow['RgpCustomAxis'.$clEye];
					$tempArray['base_curve'.$clEye] = $clRow['RgpCustomBC'.$clEye];
					$tempArray['diameter'.$clEye] = $clRow['RgpCustomDiameter'.$clEye];
					$tempArray['optical_zone'.$clEye] = $clRow['RgpCustomOZ'.$clEye];
					$tempArray['center_thickness'.$clEye] = $clRow['RgpCustomCT'.$clEye];
					$tempArray['add'.$clEye] = $clRow['RgpCustomAdd'.$clEye];
					$tempArray['dva'.$clEye] = $clRow['RgpCustomDva'.$clEye];
					$tempArray['nva'.$clEye] = $clRow['RgpCustomNva'.$clEye];
					$tempArray['make'.$clEye] = $clRow['RgpCustomType'.$clEye];
					$tempArray['id'.$clEye.'_ID'] = $clRow['RgpCustomType'.$clEye.'_ID'];
					$tempArray['color'.$clEye] = $clRow['RgpCustomColor'.$clEye];
				}
				$clArray[$clRow['dos']][] = $tempArray;
			}
			return $clArray;
		}
	}
?>
