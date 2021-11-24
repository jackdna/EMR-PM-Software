<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: MEdical History Main Class 
 Access Type: Indirect Access.
 
*/
include_once $GLOBALS['srcdir'].'/classes/class.language.php';
include_once $GLOBALS['srcdir'].'/classes/cls_common_function.php';
include_once $GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php';
include_once $GLOBALS['srcdir'].'/classes/class.cls_notifications.php';
$cls_common = new CLSCommonFunction();
$cls_review  = new CLSReviewMedHx();

class MedicalHistory extends core_lang 
{
	public $patient_id = '';
	public $patient_older = false;
	public $current_tab = '';
	public $delimiter = '~|~';
	public $max_chars = 23;
	public $max_showc = 20;
	public $patProviderID = 0;
	public $policy_status = false;
	public $patientGenderInfoButton = '';
	public $patientAgeInfoButton = '';
	
	public function __construct($tab = 'ocular')
	{
		core_lang::__construct();
		$this->patient_id = (int) $_SESSION['patient'];
		$this->current_tab = $tab ? $tab : 'ocular';
		$this->policy_status = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
		$this->is_patient_older();
		$this->set_provider_id();
	}
	
	public function is_patient_older()
	{
		$query = "SELECT DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(dob, '00-%m-%d')) AS patAge, pd.sex as ptGender FROM patient_data pd WHERE pd.pid = '".$this->patient_id."' LIMIT 1";
		$sql = imw_query($query);
		if($sql)
		{
			if(imw_num_rows($sql) > 0)
			{
				$row = imw_fetch_object($sql);
				if($row->patAge > 13) $this->patient_older = true;
				switch($row->ptGender) {
					case "Male":
					$ptGenderNew = "M";
					break;
					case "Female":
					$ptGenderNew = "F";
					break;
					case "Unknown":
					$ptGenderNew = "UKN";
					break;
				}
				$this->patientGenderInfoButton 	= $ptGenderNew;
				$this->patientAgeInfoButton 	= $row->patAge;
			}
		}
		imw_free_result($sql);
	}
	
	public function set_provider_id()
	{
		/*$query = "select sa_doctor_id as provider_id from schedule_appointments where sa_patient_id = '".$this->patient_id."' and sa_patient_app_status_id NOT IN(201,18,19,20,203) and sa_app_start_date >= CURRENT_DATE ORDER BY sa_app_start_date LIMIT 1";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		if(imw_num_rows($sql) == 0)
		{*/
			$query = "SELECT providerID as provider_id FROM patient_data WHERE pid = '".$this->patient_id."' LIMIT 1";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
		//}
		if($cnt > 0 )
		{
			$row = imw_fetch_assoc($sql);
			$this->patProviderID = $row['provider_id'];
			
		}	
	}
	
	public function last_examine_detail()
	{
		$return = array();
		$query = "select CONCAT_WS('',SUBSTRING(us.fname,1,1),SUBSTRING(us.lname,1,1)) as opName, ple.operator_id,
										 date_format(ple.created_date,'%m-%d-%y') as createdDate, 
										 time_format(ple.created_date,'%h:%i %p') as createdTime
										 from patient_last_examined ple
										 LEFT JOIN users us ON us.id = ple.operator_id 
										 where ple.patient_id = '".$this->patient_id."'
										 and (ple.save_or_review = '2' or ple.save_or_review = '1')
										 order by ple.patient_last_examined_id desc limit 1";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		$row = imw_fetch_object($sql);
		
		$return['total'] = $cnt;
		$return['operator_id'] = $row->operator_id;
		$return['createdDate'] = $row->createdDate;
		$return['createdTime'] = $row->createdTime;
		$return['phy_name'] = $row->opName;
		
		return $return;
	
	}
	
	public function get_other_field_val($string,$arrPtRel)
	{ // used in Ocular And General Health
		trim($string);
		if(empty($string)) return;
		
		$arr_string = $arr_string_tmp = array();
		$arrAllRelVals = $arrOtherVals = array();
		
		$other_val_string = '';
		if(preg_match("/\bOther\b/i", $string))
		{
			$arr_string = explode(',',$string);
			foreach($arr_string as $val)
			{
				$arr_string_tmp[]=trim($val);
			}
			$arrAllRelVals = explode(',',$this->get_combo_multi($string,$arrPtRel,'forString'));
			$arrOtherVals = array_diff($arr_string_tmp,$arrAllRelVals);
			$other_val_string = implode(',',$arrOtherVals);
		}
		
		return $other_val_string;
		
	}
	
	public function getPtOcularInfo()
	{
		$qry = "SELECT you_wear, eye_problems, any_conditions_you, any_conditions_relative, eye_problems_other, OtherDesc, chronicDesc from ocular where patient_id = '".$this->patient_id."'";
		$sql = imw_query($qry);
		$row = imw_fetch_assoc($sql);
		
		$retArr = array();
		$arrEyeHistory = array('None','Glasses','Contact Lenses','Glasses And Contact Lenses');
		
		$eyeProblems = array('Blurred or Poor Vision','Poor Night Vision','Gritty Sensation','Trouble Reading Signs',
												 'Glare From Lights','Tearing','Poor Depth Perception','Halos Around Lights','Itching or Burning',
												 'Trouble Identifying Colors','See Spots or Floaters','Eye Pain','Double Vision','See Light Flashes',
												 'Redness or Bloodshot','Others');
							   
		$arrCondition = array('Dry Eyes','Macula Degeneration','Glaucoma','Retinal Detachment','Cataracts', 'Keratoconus');
		
		$retArr['eye_history'] = $arrEyeHistory[$row['you_wear']];
		
		$arrEyeProb = explode(',',$row['eye_problems']);		

		$strAnyConditionsYou = $row["any_conditions_you"];
		$strAnyConditionsYou = get_set_pat_rel_values_retrive($strAnyConditionsYou,"pat","~|~");

		$arrYou = explode(',',$strAnyConditionsYou);
		$arrRelative = explode(',',$row['any_conditions_relative']);
		
		//******* GET OCULAR MED HX BLOOD RELATIVE DATA ********
		$arrBldRel = $row['any_conditions_relative'];
		$strAnyConditionsRelative = get_set_pat_rel_values_retrive($arrBldRel,"pat","~|~");
		$arrRel = explode(',',$strAnyConditionsRelative);
		$arrBloodRel = $arrRel;
		$arrBloodRel = array_unique($arrBloodRel);	//	Removes duplicate values from an array
		sort($arrBloodRel);
		
		$arrYouRel = $arrYou;
		$arrYouRel = array_unique($arrYouRel);			//	Removes duplicate values from an array
		sort($arrYouRel);
		$k = 0;
		
		if(count($arrEyeProb) > 0)
		{	
			for($i = 0; $i<count($arrEyeProb); $i++)
			{
				$val = (int)$arrEyeProb[$i];
				$val = $val - 1;
				$retArr['eye_problem'][$i] = $eyeProblems[$val];
			}
		}
		
		for($j = 0; $j<count($arrYouRel); $j++)
		{
			if($arrYouRel[$j] != ''){
				$arrVal = (int)$arrYouRel[$j];
				$arrVal = $arrVal - 1;
				$retArr['you_rel'][$k] = $arrCondition[$arrVal];
				$k++;
			}
		}
		
		//******* GET OCULAR MED HX BLOOD RELATIVE DATA ********
		for($l = 0; $l<count($arrBloodRel); $l++)
		{
			if($arrBloodRel[$l] != ''){
				$arrValRel = (int)$arrBloodRel[$l];
				$arrValRel = $arrValRel - 1;
				$retArr['blood_rel'][$k] = $arrCondition[$arrValRel];
				$k++;
			}
		}
		
		
		$retArr["eye_problems_other"] = $row["eye_problems_other"];
		$strOtherDesc = $row["OtherDesc"];
		$strOtherDesc = get_set_pat_rel_values_retrive($strOtherDesc,"pat","~|~");		
		$retArr["OtherDesc"] = $strOtherDesc;		
		
		// desc --
		$delimiter = '~|~';
		$strSep="~!!~~";
		$strSep2=":*:";
		$strDesc = $row["chronicDesc"];
		
		//********** EXPLODE BLOOD RELATIVE OTHER DATA *******
		$strDescRel =explode($delimiter,$row["chronicDesc"]);
		$strDescRel= $strDescRel[1];
		
		//**********separating chronicDesc of patient and relative ******																		
		$strDesc = get_set_pat_rel_values_retrive($strDesc,"pat",$delimiter);
	
		if(!empty($strDesc))
		{
			$arrDescTmp = explode($strSep, $strDesc);
			if(count($arrDescTmp) > 0){
				foreach($arrDescTmp as $key => $val){
					$arrTmp = explode($strSep2,$val);
					if($arrTmp[0]=="other"){
						$retArr["OtherDesc"] = $arrTmp[1];
					}
				}
			}
		}
		
		//*** GET OCULAR MEDHX BLOOD RELATIVE OTHER DATA ***
		if(!empty($strDescRel)){ 
			$arrDescTmpRel = explode($strSep, $strDescRel);
			if(count($arrDescTmpRel) > 0){
				foreach($arrDescTmpRel as $key => $val){
					$arrTmpRel = explode($strSep2,$val);
					if($arrTmpRel[0]=="other"){
						$retArr["relOtherDesc"] = $arrTmpRel[1];
					}
				}
			}
		}		
		
		return $retArr;		
	}
	
	public function getPtOcularMisc($for = 'ocular')
	{
		$return = array();
		if( !in_array($for,array('ocular','general_health'))) return $return;
		
		$forTab = ($for == 'general_health') ? 'General Health' : 'Ocular';
		
		$OcuadminControlId = $OcuadminControlLable = $OcuadminControltype = $OcupatientControlVal = $OcupatientCbkControlVal = "";
		$qryOcuMscllnous= "select cf.id as adminControlId,
																cf.control_lable as adminControlLable,
																cf.control_type as adminControltype,
																cf.cbk_default_select as adminCbkDefaultSelect,
																cf.default_value as adminDefaultvalue,
																cf.control_name as adminControlName, 
																cf.module_section as adminModuleSection,
																pcf.id as patientControlId,
																pcf.patient_id as patientControlPatientId, 
																pcf.patient_control_value as patientControlVal,
																pcf.patient_cbk_control_value as patientCbkControlVal 
															FROM 
															custom_fields cf 
															LEFT JOIN patient_custom_field pcf on 
															cf.id = pcf.admin_control_id and pcf.patient_id = '".$this->patient_id."'
															where cf.module = 'Med_Hx' 
															and cf.sub_module ='Medical Hx -> ".$forTab."'
															and cf.status = '0' order by cf.id ";
		$resOcuMscllnous = imw_query($qryOcuMscllnous);
		if(imw_num_rows($resOcuMscllnous)>0){
			while($rowOcuMscllnous = imw_fetch_assoc($resOcuMscllnous)){
				$OcuadminControlId = $rowOcuMscllnous['adminControlId'];
				$OcuadminControlLable = trim($rowOcuMscllnous['adminControlLable']);
				$OcuadminControlName = trim($rowOcuMscllnous['adminControlName']);
				$OcuadminControltype = trim($rowOcuMscllnous['adminControltype']);
				$OcupatientControlVal = trim($rowOcuMscllnous['patientControlVal']);
				$OcupatientCbkControlVal = trim($rowOcuMscllnous['patientCbkControlVal']);
				if($OcuadminControlLable && $OcuadminControltype=='text' && !empty($OcupatientControlVal)){
					$return[$OcuadminControlName] = array('label'=> $OcuadminControlLable,'val'=>$OcupatientControlVal);	
				}
		
				if($OcuadminControlLable && $OcuadminControltype=='checkbox' && $OcupatientCbkControlVal=='checked'){
					$return[$OcuadminControlName] = array('label'=> $OcuadminControlLable,'val'=>$OcupatientControlVal);	
				}
			}
		}
		
		return $return;
	}
	
	public function getPtGenHealthInfo()
	{		
		$retVal = array();
		$query = "select any_conditions_you, chk_annual_colorectal_cancer_screenings, chk_receiving_annual_mammogram,
									 chk_received_flu_vaccine, chk_high_risk_for_cardiac, sub_conditions_you, any_conditions_others_both,
									 any_conditions_others, any_conditions_others, diabetes_values, chk_under_control, desc_r, relDescHighBp, 
									 relDescStrokeProb, relDescHeartProb, relDescLungProb, relDescThyroidProb, relDescArthritisProb, relDescUlcersProb,
									 relDescCancerProb, relDescLDL, ghRelDescOthers, desc_u, desc_high_bp, desc_arthrities, desc_lung_problem, desc_stroke,
									 desc_thyroid_problems, desc_ulcers, desc_cancer, desc_heart_problem, desc_LDL, any_conditions_others, genMedComments, 
									 any_conditions_others_both, review_const, review_head, review_resp, review_card, review_gastro, review_genit,
									 review_aller, review_neuro, negChkBx, review_const_others, review_head_others, review_resp_others, review_card_others,
									 review_gastro_others	, review_genit_others, review_aller_others, review_neuro_others, review_sys,
									 
									 received_flu_vaccine_type, chk_received_pneumococcal_vaccine, pneumococcal_vaccine_type,
									 nutrition_counseling, nutrition_counseling_date,
									 physical_activity_counseling, physical_activity_counseling_date, 
									 chk_fall_risk_assd, fall_risk_ass_type,
									 chk_blood_pressure, blood_pressure_type,
									 chk_bmi, bmi_type
								from general_medicine where patient_id='".$this->patient_id."' ";
		$sql = imw_query($query);
		$row = imw_fetch_assoc($sql);
		
		//Any Condition
		$arrPtAnyCond = array();
		$arrAnyCond = array("High Blood Pressure","Heart Problem","Diabetes","Lung Problems","Stroke","Thyroid Problems","Arthritis", "Ulcers", "", "", "", "", "LDL", "Cancer" );
		
		//Patient
		$any_conditions_u1_arr=explode(" ",trim(str_replace(","," ",$row["any_conditions_you"])));
		
		$arrTmp = array("You"=>$any_conditions_u1_arr);
		foreach( $arrTmp as $key => $val )
		{
			$tmp = $val;				
			if( count($tmp) > 0 )
			{
				$arrPtAnyCond[$key]=array();
				foreach($tmp as $keyTmp => $valTmp)
				{
					if(!empty($arrAnyCond[$valTmp-1]))
					{
						$arrPtAnyCond[$key][]=$arrAnyCond[$valTmp-1];
					}
				}
			}
		}
		
		if($row["chk_annual_colorectal_cancer_screenings"]==1){
		  $strAnnual .= "Annual colorectal cancer screenings,";
		}
				
		if($row["chk_receiving_annual_mammogram"]==1){
			$strAnnual .= "Receiving annual mammogram,";
		}
		
		if($row["chk_received_flu_vaccine"]==1){
			$strAnnual .= "Received flu vaccine :".$row['received_flu_vaccine_type'].",";
		}
		
		if($row["chk_high_risk_for_cardiac"]==1){
			$strAnnual .= "High-risk for cardiac events on aspirin prophylaxis,";
		}
		
		if($row["chk_received_pneumococcal_vaccine"]==1){
			$strAnnual .= "Received Pneumococcal Vaccine :".$row['pneumococcal_vaccine_type'].",";
		}
		
		if($row["chk_fall_risk_assd"]==1){
			$strAnnual .= "Falls: Risk Assessment :".$row['fall_risk_ass_type'].",";
		}
		
		if($row["nutrition_counseling"]==1){
			$strAnnual .= "Counseling for Nutrition/Diet :".get_date_format($row['nutrition_counseling_date']).",";
		}
		
		if($row["physical_activity_counseling"]==1){
			$strAnnual .= "Counseling for Physical Activity :".get_date_format($row['physical_activity_counseling_date']).",";
		}
		
		if( $row["chk_blood_pressure"] || $row["chk_bmi"] ) {
			$bpBmiData = $this->get_vs_data();
		}
		
		if($row["chk_blood_pressure"]==1){
			$bpBmiData['BP_sys'] = trim($bpBmiData['BP_sys']);
			$bpBmiData['BP_dys'] = trim($bpBmiData['BP_dys']);
			
			$bp = "";
			if( $bpBmiData['BP_sys'] || $bpBmiData['BP_dys'] ) {
				$bp = "- <b>".$bpBmiData['BP_sys'].($bpBmiData['BP_sys']&& $bpBmiData['BP_dys']? "/":"").$bpBmiData['BP_dys']."</b>";
			}
			$strAnnual .= "Blood Pressure ".$bp." :".$row['blood_pressure_type'].",";
		}
		
		if($row["chk_bmi"]==1){
			$bmi = "- <b>".$bpBmiData['BMI_result']."</b>";
			
			$strAnnual .= "BMI ".$bmi." :".$row['bmi_type'].",";
		}
		
		$retVal["str_annaual"] = $strAnnual;
		
		//Sub Conditions
		$arrSubConditions=array();
		$arrSbConText=array("7.1"=>"RA","7.2"=>"OA");
		$elem_subCondition_pat_val = get_set_pat_rel_values_retrive($row["sub_conditions_you"],'pat',"~|~");
		$arr_sub_condition_you = explode(",", $elem_subCondition_pat_val);
		$lenSCds = count($arr_sub_condition_you);
		for($i=0;$i<$lenSCds;$i++)
		{
			$arrSubConditions["Arthritis"][]=$arrSbConText[$arr_sub_condition_you[$i]];
		}
		$retVal["SubCond"]=$arrSubConditions;
		
		//Other Condition
		$any_conditions_others_both_arr=explode(" ",trim(str_replace(","," ",$row["any_conditions_others_both"])));
		$strOthersTxtPat = get_set_pat_rel_values_retrive($row["any_conditions_others"],"pat","~|~");	
		$otherCondition = $strOthersTxtPat;
		if(!empty($otherCondition))
		{
			foreach( $any_conditions_others_both_arr as $key => $val )
			{
				if($val == "1")
				{
					$arrPtAnyCond["You"][]= $otherCondition;
					$retVal["patient_other"]= $row["any_conditions_others"];
				}
				if($val == "2")
				{
					$arrPtAnyCond["Relatives"][]= $otherCondition;
				}
			}
		}
		
		$retVal["AnyCond"]= $arrPtAnyCond;
		
		$strDiabetesIdTxtPat =  get_set_pat_rel_values_retrive($row["diabetes_values"],'pat',"~|~"); 
		$retVal["diabetes_values"]= $strDiabetesIdTxtPat;
		$arrChkUnderControl = explode(',',$row["chk_under_control"]);
		$retVal["chkUnderControl"]= $arrChkUnderControl;
		$retVal["desc_r"]= $row["desc_r"];
		$retVal["relDescHighBp"]= $row["relDescHighBp"];
		$retVal["relDescStrokeProb"]= $row["relDescStrokeProb"];
		$retVal["relDescHeartProb"]= $row["relDescHeartProb"];
		$retVal["relDescLungProb"]= $row["relDescLungProb"];
		$retVal["relDescThyroidProb"]= $row["relDescThyroidProb"];
		$retVal["relDescArthritisProb"]= $row["relDescArthritisProb"];
		$retVal["relDescUlcersProb"]= $row["relDescUlcersProb"];
		$retVal["relDescCancerProb"]= $row["relDescCancerProb"];
		$retVal["relDescLDL"]= $row["relDescLDL"];
		$retVal["ghRelDescOthers"]= $row["ghRelDescOthers"];
		$strDiabetesTxtPat = get_set_pat_rel_values_retrive($row["desc_u"],"pat","~|~");
		$retVal["desc_u"]= $strDiabetesTxtPat;
		$strHighBPTxtPat = get_set_pat_rel_values_retrive($row["desc_high_bp"],"pat","~|~");
		$retVal["desc_high_bp"]= $strHighBPTxtPat;
		$strArthritiesTxtPat = get_set_pat_rel_values_retrive($row["desc_arthrities"],"pat","~|~");
		$retVal["desc_arthrities"]= $strArthritiesTxtPat;
		$strLungProblemTxtPat = get_set_pat_rel_values_retrive($row["desc_lung_problem"],"pat","~|~");
		$retVal["desc_lung_problem"]= $strLungProblemTxtPat;
		$strStrokeTxtPat = get_set_pat_rel_values_retrive($row["desc_stroke"],"pat","~|~");
		$retVal["desc_stroke"]= $strStrokeTxtPat;
		$strThyroidProbTxtPat = get_set_pat_rel_values_retrive($row["desc_thyroid_problems"],"pat","~|~");
		$retVal["desc_thyroid_problems"]= $strThyroidProbTxtPat;
		$strUclearTxtPat = get_set_pat_rel_values_retrive($row["desc_ulcers"],"pat","~|~");
		$retVal["desc_ulcers"]= $strUclearTxtPat;
		$strCancerTxtPat = get_set_pat_rel_values_retrive($row["desc_cancer"],"pat","~|~");
		$retVal["desc_cancer"]= $strCancerTxtPat;
		$strHeartProbTxtPat = get_set_pat_rel_values_retrive($row["desc_heart_problem"],"pat","~|~");
		$retVal["desc_heart_problem"]= $strHeartProbTxtPat;
		$strLDLTxtPat = get_set_pat_rel_values_retrive($row["desc_LDL"],"pat","~|~");
		$retVal["desc_LDL"]= $strLDLTxtPat;
		$strOthersTxtPat = get_set_pat_rel_values_retrive($row["any_conditions_others"],"pat","~|~");
		$retVal["any_conditions_others"]= $strOthersTxtPat;
		$retVal["genMedComments"]= $row["genMedComments"]; 
		$retVal["Other_case"]= $row["any_conditions_others_both"]; 
		
		//Review Of System
		$review_const_arr=explode(" ",trim(str_replace(","," ",$row["review_const"])));
		$review_head_arr=explode(" ",trim(str_replace(","," ",$row["review_head"])));
		$review_resp_arr=explode(" ",trim(str_replace(","," ",$row["review_resp"])));
		$review_card_arr=explode(" ",trim(str_replace(","," ",$row["review_card"])));
		$review_gastro_arr=explode(" ",trim(str_replace(","," ",$row["review_gastro"])));
		$review_genit_arr=explode(" ",trim(str_replace(","," ",$row["review_genit"])));
		$review_aller_arr=explode(" ",trim(str_replace(","," ",$row["review_aller"])));
		$review_neuro_arr=explode(" ",trim(str_replace(","," ",$row["review_neuro"])));
		$negChkBxArr = explode(',',$row["negChkBx"]);
		
		
		
		$arrROS = array("Constitutional" => array("arr"=>$review_const_arr,"arrNames"=>array("Fever","Weight Loss","Rash", "Skin Disease", "Fatigue"), "Other"=>$row["review_const_others"]),
						        "Ear, Nose, Mouth & Throat" => array("arr"=>$review_head_arr,"arrNames"=>array("Sinus Infection", "Post Nasal Drips", "Runny Nose","Dry Mouth","Deafness"), "Other"=>$row["review_head_others"]),
							"Respiratory" => array("arr"=>$review_resp_arr,"arrNames"=>array("Cough","Bronchitis","Shortness of Breath","Asthma","Emphysema","COPD","TB"), "Other"=>$row["review_resp_others"]),
							"Cardiovascular" => array("arr"=>$review_card_arr,"arrNames"=>array("Chest Pain","Congestive Heart Failure","Irregular Heart beat","Shortness of Breath","High Blood Pressure", "Low Blood Pressure", "Pacemaker/defibrillator"), "Other"=>$row["review_card_others"]),
							"Gastrointestinal" => array("arr"=>$review_gastro_arr,"arrNames"=>array("Vomiting","Ulcers","Diarrhea","Bloody Stools","Hepatitis","Jaundice","Constipation"), "Other"=>$row["review_gastro_others"]),
							"Genitourinary" => array("arr"=>$review_genit_arr,"arrNames"=>array("Genital Ulcers","Discharge","Kidney Stones","Blood in Urine"), "Other"=>$row["review_genit_others"]),
							"Allergic/Immunologic" => array("arr"=>$review_aller_arr,"arrNames"=>array("Seasonal Allergies","Hay Fever"), "Other"=>$row["review_aller_others"]),
							"Neurological" => array("arr"=>$review_neuro_arr,"arrNames"=>array("Headache","Migraines","Paralysis Fever","Joint Ache","Seizures","Numbness","Faints","Stroke","Multiple Sclerosis", "Alzheimer's Disease", "Parkinson's Disease","Dementia"), "Other"=>$row["review_neuro_others"]) ,
							"negChkBx" => array("arr"=>$negChkBxArr,"arrNames"=>array("Constitutional","Ear, Nose, Mouth & Throat","Respiratory","Cardiovascular","Gastrointestinal","Genitourinary","Allergic/Immunologic","Neurological","Integumentary","Psychiatry","Hemotologic/Lymphatic","Musculoskeletal","Endocrine", "Eyes")) 
							);
							
		//ros --
		$review_sys = $row["review_sys"];		
		$ar_review_sys = (!empty($review_sys)) ? json_decode($review_sys, true) : array() ;
		$ar_tmp = array('Integumentary'=>array('review_intgmntr', array("Rashes", "Wounds", "Breast Lumps","Eczema","Dermatitis")),	
					'Psychiatry'=>array('review_psychiatry', array("Depression", "Anxiety", "Paranoia", "Sleep Patterns","Mental and/or emotional factors", "Alzheimer's Disease", "Parkinson's disease","Memory Loss")), 
					'Hemotologic/Lymphatic'=>array('review_blood_lymph', array("Anemia", "Blood Transfusions", "Excessive Bleeding", "Purpura", "Infection")),
					'Musculoskeletal'=>array('review_musculoskeletal', array("Pain", "Joint Ache", "Stiffness", "Swelling","Paralysis Fever")),
					'Endocrine'=>array('review_endocrine', array("Mood Swings", "Constipation", "Polydipsia","Hypothyroidism","Hyperthyroidism")),
					'Eyes'=>array('review_eye', array("Vision loss", "Eye pain", "Double vision", "Headache")));
		foreach($ar_tmp as $k => $arv){
			$v = $arv[0];
			$tmpar = array();			
			$artmp = isset($ar_review_sys[$v]) ? explode(" ",trim(str_replace(","," ",$ar_review_sys[$v]))) : array() ;
			$tmpar["arr"] = $artmp;
			$tmpar["arrNames"] = $arv[1];
			
			//
			$vother = $v."_others";
			$tmpar["Other"] = isset($ar_review_sys[$vother]) ? $ar_review_sys[$vother] : "" ;
			
			if(count($tmpar)){
				$arrROS[$k] = $tmpar;
			}
		}
		
		$arrPtROS=array();
		foreach($arrROS as $key => $val)
		{
			$tmp = $val["arr"];
			$tmpName = $val["arrNames"];
			$otherTmp = $val["Other"];
			
			$arrPtROS[$key]=array();
			
			if( count($tmp) > 0 )
			{
				foreach($tmp as $keyTmp => $valTmp)
				{
					if(!empty($tmpName[$valTmp-1]))
					{
						$arrPtROS[$key][]=$tmpName[$valTmp-1];
					}
				}
			}
			
			if(!empty($otherTmp))
			{
				$arrPtROS[$key][]=$otherTmp;
			}
		}
		
		ksort($arrPtROS);
		
		$retVal["ROS"]= $arrPtROS;
		
		
		return ($retVal) ? $retVal : false;
	}	
	
	public function getExaminedData($count,$proName,$section_name,$dt, $prev_date, $patient_id,$patientLastExaminedId,$nochangeSinceDate,$operator_id)
	{
		$primary_key_id = ""; $category = ""; $table_name = ""; $where = "";

		switch($section_name)
		{
			case "Ocular":
				$primary_key_id = "ocular_id";
				$table_name 	= "ocular";
				$category		= "ocular";
				$where 			= "patient_id";
			break;
			case "General_health":
				$primary_key_id = "general_id";
				$table_name 	= "general_medicine";
				$category		= "general_medicine";
				$where 			= "patient_id";
			break;
			case "Medication":
				$primary_key_id = "id";
				$table_name 	= "lists";
				$category		= "medication";
				$where 			= "pid";
			break;
			case "Sx_procedures":
				$primary_key_id = "id";
				$table_name 	= "lists";
				$category		= "surgeries";
				$where 			= "pid";
			break;
			case "Allergies":
				$primary_key_id = "id";
				$table_name 	= "lists";
				$category		= "allergies";
				$where 			= "pid";
			break;
			case "Immunizations":
				$primary_key_id = "id";
				$table_name 	= "immunizations";
				$category		= "immunizations";
				$where 			= "patient_id";
			break;
			case "Social":
				$primary_key_id = "social_id";
				$table_name 	= "social_history";
				$category		= "social_history";
				$where 			= "patient_id";
			break;
			case "Cc_history":
				$primary_key_id = "ocular_id";
				$table_name 	= "ocular";
				$where 			= "patient_id";
			break;
			case "Vs":
				$primary_key_id = "ocular_id";
				$table_name 	= "ocular";
				$where 			= "patient_id";
			break;
			case "Complete":
				$primary_key_id = "ocular_id";
				$table_name 	= "ocular";
				$where 			= "patient_id";
			break;
		}
		
		$tempSecName = "";
		$tempSecName = $section_name;
		if($nochangeSinceDate){
			$section_name = "No change was observed in ".$section_name." since last review ".$nochangeSinceDate;
		}
		
		$on_click = 'onClick="show_review_detail(\''.$patientLastExaminedId.'\',\''.$tempSecName.'\',\''.$operator_id.'\',\''.$dt.'\');"';
		
		$count = '<a href="javascript:void(0);" '.$on_click.'">'.$count.'</a>';
		$proName = '<a class="purple-text" href="javascript:void(0);" '.$on_click.'">'.$proName.'</a>';
		$section_name = '<a href="javascript:void(0);" '.$on_click.'">'.$section_name.'</a>';
		$dt = '<a href="javascript:void(0);" '.$on_click.'">'.$dt.'</a>';
		
		$data = '
			<tr>
				<td class="text-center">'.$count.'</td>
				<td class="text-left">'.$proName.'</td>
				<td class="text-left">'.$section_name.'</td>
				<td class="text-center">'.$dt.'</td>
			</tr>';
		return $data;
	}	
	
	public function getExaminedDataForComplete($count,$proName,$rsGetCompleteReviewed,$date_time,$section_name,$operator_id)
	{
		$tempSecName = "";
		$tempSecName = $section_name;
		if($nochangeSinceDate){
			$section_name = "No change in ".$section_name." since last review ".$nochangeSinceDate;
		}
		
		
		$arrMedHxSecName = array("Ocular Hx","General Health","Medications","Allergies","Sx/Procedure","Immunizations","Lab","Advance Directive");
		$completeRevDivData = "";
		$patLastExaminedIdForComp = "";
		for($intCounterComp=0;$intCounterComp<count($arrMedHxSecName);$intCounterComp++)
		{			
			$patientLastExaminedId = "";
			$patientLastExaminedId = $rsGetCompleteReviewed[$intCounterComp]['patient_last_examined_id'];
			$secName = trim($rsGetCompleteReviewed[$intCounterComp]["section_name"]);
			$sectionComplete = $rsGetCompleteReviewed[$intCounterComp]["section_complete"];
			$sectionCompleteId = $rsGetCompleteReviewed[$intCounterComp]["section_complete_id"];
			if($patientLastExaminedId){
				$patLastExaminedIdForComp .= $patientLastExaminedId.",";
			}
			
			$on_click = 'onClick="show_review_detail(\''.$patientLastExaminedId.'\',\''.$secName.'\',\''.$operator_id.'\',\''.$date_time.'\');"';
			if(in_array($secName,$arrMedHxSecName))
			{
				$completeRevDivData .= '<a class="purple-text pointer" href="javascript:void(0);" '.$on_click.'>Reviewed '.$secName.'</a><br>';
				foreach($arrMedHxSecName as $key => $value)
				{
					if($secName == $value) 
					{
						unset($arrMedHxSecName[$key]); 
					}
				} 
			}
		}	
		
		foreach($arrMedHxSecName as $key => $value)
		{
			$completeRevDivData .= '<span class="col-xs-12">Reviewed '.$value.'</span>';
		} 	
		
		$patLastExaminedIdForComp = substr(trim($patLastExaminedIdForComp), 0, -1);
		$on_click = 'onClick="show_review_detail(\''.$patLastExaminedIdForComp.'\',\''.$tempSecName.'\',\''.$operator_id.'\',\''.$date_time.'\');"';
		$proName = '<a class="purple-text" href="javascript:void(0);" '.$on_click.' >'.$proName.'</a>';
		
		$data = '
			<tr>
				<td class="text-center">'.$count.'</td>
				<td class="text-left">'.$proName.'</td>
				<td class="text-left">'.$completeRevDivData.'</td>
				<td class="text-center">'.$date_time.'</td>
			</tr>';
			
		return $data;
	}
	
	public function get_combo_multi($valSel, $arrPtRel, $callFor='')
	{
		$strOption = "";
		$arrTemp = array();
		$arrTemp = explode(",", $valSel);
		$arrTemp = array_map("trim",$arrTemp);
		$counter = 0;
		foreach($arrPtRel as $intPtRelKey => $arrPtRelVal)
		{
			$counter++;
			$valueOutput = is_array($arrPtRelVal) ? trim($arrPtRelVal[2]) : trim($arrPtRelVal);
			$valueOutput = (empty($valueOutput) && $counter == 1) ?  'All' : $valueOutput;
			$selected = (in_array($valueOutput,$arrTemp)) ? 'selected' : '';
			
			if(!$valueOutput) continue;
			if($callFor=='forString')
			{
				$strOption.= $valueOutput.',';
			}
			else
			{
				$strOption .= '<option value = "'.$valueOutput.'" '.$selected.'>'.$valueOutput.'</option>';
			}
		}
		if($callFor=='forString')
		{
			$strOption=substr($strOption, 0,-1);
		}
		return $strOption;
	}
	
	public function print_misc_question($for)
	{
		$for = trim($for);
		if($for <> 'ocular' && $for <> 'general_health') return;
		
		$sub_module = 'Medical Hx -> ';
		$sub_module .= ($for == 'general_health') ? 'General Health' : 'Ocular' ;
		
		
		$query = "select cf.id as adminControlId, cf.control_lable as adminControlLable,
																cf.control_type as adminControltype, cf.cbk_default_select as adminCbkDefaultSelect,
																cf.default_value as adminDefaultvalue, cf.control_name as adminControlName, 
																cf.module_section as adminModuleSection, pcf.id as patientControlId, 
																pcf.patient_id as patientControlPatientId, pcf.patient_control_value as patientControlVal,
																pcf.patient_cbk_control_value as patientCbkControlVal 
														FROM  custom_fields cf 
														LEFT JOIN patient_custom_field pcf on 
														cf.id = pcf.admin_control_id and pcf.patient_id = '".$this->patient_id."'
														where cf.module = 'Med_Hx' 
														and cf.sub_module ='".$sub_module."' 
														and cf.status = '0' order by cf.id ";
			$sql = imw_query($query);
			if($sql)
			{
				$serialized = "";
				if(imw_num_rows($sql) > 0)
				{
					$counter= 1; $controlText = ''; $controlLabel = '';
					echo '<div class="misocu">';
					echo '<div class="head"><span>Miscellaneous</span></div>';
					echo '<div class="clearfix"></div>';
					while($row = imw_fetch_assoc($sql))
					{
						$cbkTextBox = $cbkTextBoxLabel = $checked = $controlType = $cbkValue = "";
						
						if($row['adminControltype'] == "checkbox")
						{
							$controlType = "checkbox";
							if($row['patientCbkControlVal'])
								$checked = ($row['patientCbkControlVal'] == "checked") ? "checked" : '';
							
							elseif($row['adminCbkDefaultSelect'] == 1)
								$checked = "checked";
							
							
							if($row['patientControlVal'] != "")
								$cbkValue = $row['adminDefaultvalue'] ? $row['adminDefaultvalue'] : $row['patientControlVal'] ;
							elseif($row['adminDefaultvalue'] != "")
								$cbkValue = $row['adminDefaultvalue'];
							elseif($checked == "checked")
								$cbkValue = "checked";
							else
								$cbkValue = "checked";
							
							$cbkTextBoxLabel = $row['adminControlLable'];
							$cbkTextBoxLabel .= ($row['adminDefaultvalue']) ? "(".$row['adminDefaultvalue'].")" : ''; 
							
							$cbkTextBox = '<div class="checkbox">
															<input type="checkbox" onClick="chk_change(\''.$checked.'\',this,event);" value="'.$cbkValue.'" name="'.$row['adminControlName'].'" id="'.$row['adminControlName'].'" '.$checked.' />
															<label for="'.$row['adminControlName'].'">'.$cbkTextBoxLabel.'</label>
														</div>';
							
						}
						elseif($row['adminControltype'] == "text")
						{
							$controlType = "text";
							$cbkValue =	($row['patientControlVal'] != "") ? $row['patientControlVal'] : $row['adminDefaultvalue'];
							$cbkTextBoxLabel = $row['adminControlLable'];
							
							$cbkTextBox = '<div class="col-sm-4"><label for="'.$row['adminControlName'].'">'.$cbkTextBoxLabel.'</label></div>
														 <div class="col-sm-8">
																<input type="text" class="form-control" onKeyUp="chk_change(\''.addslashes($cbkValue).'\',this,event);" value="'.html_entity_decode($cbkValue).'" name="'.$row['adminControlName'].'" id="'.$row['adminControlName'].'" />
															</div>';
						}
						
						echo '<div class="col-sm-3">
										<input type="hidden" name="hidPatientControlPId[]" value="'.$row['patientControlId'].'" />
										<input type="hidden" name="hidcustomField[]" value="'.$row['adminControlName'].'_'.$row['adminControlId'].'_'.$controlType.'" />
									'.$cbkTextBox.'</div>';
						
						if($counter%4 == 0) 
							echo '<div class="clearfix"></div>';
						if($counter%2 == 0)	
							echo '<div class="clearfix visible-sm"></div>';
						
						// Audit Functionality
						if($for == 'ocular') {
							$customDataFields = array(); 
							$customDataFields = make_field_type_array("patient_custom_field");
							if($customDataFields == 1146){
								$customError = "Error : Table 'patient_custom_field' doesn't exist";
							}	
							if($this->policy_status == 1){ 
									$arrCustumAuditTrailOculer = array();
									$arrCustumAuditTrailOculer [] = 
											array(							
													"Pk_Id"=> (($row["patientControlPatientId"]) ? $row["patientControlPatientId"] : $this->patient_id),
													"Table_Name"=>"patient_custom_field",																	
													"Data_Base_Field_Name"=> "patient_control_value" ,
													"Filed_Label"=> $row['adminControlName'],
													"Filed_Text"=> "Patient Custom Filed ".$row['adminControlLable'],
													"Data_Base_Field_Type"=> fun_get_field_type($customDataFields,"patient_control_value") ,
													"Category"=> "patient_info-medical_history",
													"Category_Desc"=> "ocular",	
													"Old_Value"=> addcslashes(addslashes((($row['patientControlVal'] != "") ? $row['patientControlVal'] : $row['adminDefaultvalue'])),"\0..\37!@\177..\377")
												);	
								}
						}
						
						$counter++;
					} 
					
					echo '</div>';
					
					
					if($for == 'ocular' && $this->policy_status == 1){
						require_once(getcwd()."/ocular/audit.php");

						$result = array();
						$result = $arrCustumAuditTrailOculer;

						$arrMergeAuditCustomAudit = array();
						$arrMergeAuditCustomAudit = array_merge($arrAuditTrail_Ocular, $result);

						$arrAuditTrail_Ocular = array();
						$arrAuditTrail_Ocular = $arrMergeAuditCustomAudit;

						$serialized = urlencode(serialize($arrAuditTrail_Ocular));
					}
				} else {
					//if custom fields are not available
					if($for == 'ocular' && $this->policy_status == 1){
						require_once(getcwd()."/ocular/audit.php");
						$serialized = urlencode(serialize($arrAuditTrail_Ocular));
					}
				}
				return $serialized;
			}
		
	}
	
	public function print_speacialty_question($for)
	{
		$for = trim($for);
		if($for <> 'ocular' && $for <> 'general_health') return;
		
		$sub_module_pre = 'Medical Hx -> ';
		$sub_module = ($for == 'general_health') ? 'General Health' : 'Ocular' ;
		
		$arrAnsOpElName = array();
		if($this->patProviderID > 0)
		{
			$arrSpelQue = array();

			$query = "SELECT if(amed.spl_id > 0, adSpl.name,'') as splName, amed.ques splQue, amed.id as queID, amed.spl_id as queSplID, 
									amed.answer_type as ansType, '".$this->patProviderID."' AS splPhyId
									FROM admn_medhx amed 
									INNER JOIN admn_medhx_tab adMedTab ON adMedTab.admn_medhx_question_id = amed.id
									INNER JOIN admn_speciality adSpl ON (adSpl.id = amed.spl_id or amed.spl_id=0)
									WHERE (adMedTab.tab_name = '".$sub_module."' or adMedTab.tab_name = '".$sub_module_pre.$sub_module."')
									and adMedTab.active_status = '0'
									and adSpl.status = '0'
									and amed.spl_id = 0
									GROUP BY amed.id
									UNION 											
					 SELECT if(amed.spl_id > 0, adSpl.name,'') as splName, amed.ques splQue, amed.id as queID, amed.spl_id as queSplID, 
									amed.answer_type as ansType, phs.phyId AS splPhyId
									FROM admn_medhx amed 
									INNER JOIN admn_medhx_tab adMedTab ON adMedTab.admn_medhx_question_id = amed.id
									INNER JOIN admn_speciality adSpl ON (adSpl.id = amed.spl_id or amed.spl_id=0)
									INNER JOIN phy_speciality phs ON (phs.spId = amed.spl_id AND phs.status = 0)
									WHERE (adMedTab.tab_name = '".$sub_module."' or adMedTab.tab_name = '".$sub_module_pre.$sub_module."')
									and adMedTab.active_status = '0'
									and adSpl.status = '0'
									and phs.phyId = '".$this->patProviderID."'
									GROUP BY amed.id ORDER BY 1,2";
            $sql = imw_query($query);
			if(imw_num_rows($sql))
			{
				$counter = 0;
				while($row = imw_fetch_assoc($sql))
				{
					$counter++;
					$arrQueAnsOp = $arrPatQueAnsOp = array();
					if($row["ansType"] == 1)
					{
						//Collecting this question options
						$qry_inn = "select id, option_value from med_hx_question_answer_options where question_id = '".$row["queID"]."' and del_status = '0'";
						$sql_inn = imw_query($qry_inn);
						if(imw_num_rows($sql_inn) > 0)
						{
							while($row_inn = imw_fetch_assoc($sql_inn))
							{
								$intOpValueIdDB = 0;
								$strOpValueDB = "";
								$intOpValueIdDB = $row_inn["id"];
								$strOpValueDB = html_entity_decode(stripslashes($row_inn["option_value"]));
								$arrQueAnsOp[] = array("intOpValueIdDB" => $intOpValueIdDB, "strOpValueDB" => $strOpValueDB);
							}
						}
					}
					
					$strPatAnsDB = "";
					$qry_ans = "Select id as patAnsId, ans_type as patAnsType, pat_answer as patAns from patient_specialty_question_answer where specialty_id = '".$row["queSplID"]."' and question_id = '".$row["queID"]."' and patient_id = '".$this->patient_id."' and med_hx_tab = '".$sub_module."' and row_del_status = '0' LIMIT 1";
					$sql_ans = imw_query($qry_ans);
					if(imw_num_rows($sql_ans) > 0)
					{
						$row_ans = imw_fetch_row($sql_ans);
						if((int)$row_ans[1] == 1)
						{
							//Collecting this question answer options for current patient
							$qryGetPatOp = "select * from patient_specialty_question_options_answer 
																			where patient_specialty_question_answer_id = '".$row_ans[0]."' ";
							$rsGetPatOp = imw_query($qryGetPatOp);
							while($rowGetPatOp = imw_fetch_array($rsGetPatOp))
							{
								$intPatQueId = $intPatOpId = $intPatStateId = 0;
								$intPatQueId = $rowGetPatOp["question_id"];
								$intPatOpId = $rowGetPatOp["option_id"];
								$intPatStateId = $rowGetPatOp["state"];
								$arrPatQueAnsOp[$intPatOpId] = $intPatStateId;
							}
						}
						
						$strPatAnsDB = trim($row_ans[2]);
						$strPatAnsDB = core_extract_user_input($strPatAnsDB);
						imw_free_result($rsGetProName);
					}
					
					$arrSpelQue[$row["queSplID"].'_'.$row["splName"]][] = array("splName" => $row["splName"], "splQue" => $row["splQue"], "queID" => $row["queID"], "queSplID" => $row["queSplID"], "ansType" => $row["ansType"], "arrQueAnsOp" => $arrQueAnsOp, "strPatAnsDB" => $strPatAnsDB, "arrPatQueAnsOp" => $arrPatQueAnsOp, "splPhyId" => $row["splPhyId"]);
				}
					
				imw_free_result($rsGetProSpeQue);
			}
			
			$strHtmlSpelQue = "";
			$totQuestion = $counter;
			if($totQuestion > 0)
			{
				$strHtmlSpelQue .= '<div class="row pt10" id="accordionSpl">';
				$strHtmlSpelQue .= '<div class="head"><span>Specialty Question(s)</span></div>';
				
				$strHtmlSpelQue .= '<div class="clearfix"></div>';
				$strHtmlSpelQue .= '<input type="hidden" id="totQuestion" name="totQuestion" value="'.$totQuestion.'" >';
				$strHtmlSpelQue .= '<input type="hidden" id="patProviderID" name="patProviderID" value="'.$this->patProviderID.'" />';
				
				$intSplQueKey = -1;
				foreach($arrSpelQue as $splKey => $arrSplQuest)
				{
					$splDataArr = explode("_",$splKey);
					$spl_id = (int) $splDataArr[0];
					$spl_name = $splDataArr[1];
					
					$strDispTR = ($spl_id != $patProviderID) ? 'hidden' : '';
					
					$questRow = '';
					$qCounter = 0;
					foreach($arrSplQuest as $arrSplQueVal)
					{
						$qCounter++;
						$intSplQueKey++;
						$questRow .= '<div class="col-sm-12 pt5">';
						$questRow .= '<div class="row">';
						// Question 
						$questRow .= '<div class="col-sm-3"><label>'.$arrSplQueVal["splQue"].'</label></div>';
						// Answer Field 
						$questRow .= '<div class="col-sm-9">';
						$questRow .= '<input type="hidden" id="hidSplId'.$intSplQueKey.'" name="hidSplId'.$intSplQueKey.'" value="'.$arrSplQueVal["queSplID"].'" />';
						$questRow .= '<input type="hidden" id="hidQueId'.$intSplQueKey.'" name="hidQueId'.$intSplQueKey.'" value="'.$arrSplQueVal["queID"].'" />';
						if($arrSplQueVal["ansType"] == 0)
						{
							$questRow .= '<input type="hidden" id="hidAnsElType'.$intSplQueKey.'" name="hidAnsElType'.$intSplQueKey.'" value="textarea"/>';
							$questRow .= '<textarea id="txtAreaAnswer'.$intSplQueKey.'" name="txtAreaAnswer'.$intSplQueKey.'" class="form-control" rows="1" onKeyUp="chk_change(\''.$arrSplQueVal["strPatAnsDB"].'\',this,event);" placeholder="Answer" >'.$arrSplQueVal["strPatAnsDB"].'</textarea>';
						}
						elseif($arrSplQueVal["ansType"] == 1)
						{
							$strAnsOpElName = "";
							$strAnsOpElName = "txtAreaAnswer".$intSplQueKey;
							$arrAnsOpElName[] = $strAnsOpElName;

							$strHtmlSpelQue .= '<input type="hidden" id="hidAnsElType'.$intSplQueKey.'" name="hidAnsElType'.$intSplQueKey.'" value="multipleSelect" />';
							$arrQueAnsOption = $arrPatQueAnsOption = array();
							$arrQueAnsOption = $arrSplQueVal["arrQueAnsOp"];
							$arrPatQueAnsOption = $arrSplQueVal["arrPatQueAnsOp"];
							
							$tempCounter = 0;
							foreach($arrQueAnsOption as $intQueAnsOptionKey => $arrQueAnsOptionVal)
							{
								$tempCounter++;
								$checked = "";
								if(array_key_exists($arrQueAnsOptionVal["intOpValueIdDB"], $arrPatQueAnsOption))
								{
									if($arrPatQueAnsOption[$arrQueAnsOptionVal["intOpValueIdDB"]] == 1)
									{
										$checked = "checked";
									}
								}
								$questRow .= '<div class="checkbox checkbox-inline">';
								$questRow .= '<input type="checkbox" name="'.$strAnsOpElName.'[]" id="'.$strAnsOpElName.$tempCounter.'" value="'.$arrQueAnsOptionVal["intOpValueIdDB"].'" '.$checked.' ><label for="'.$strAnsOpElName.$tempCounter.'">'.$arrQueAnsOptionVal["strOpValueDB"].'</label>';	
								$questRow .= "</div>";
								
								//if( $tempCounter%4 == 0)
									//$questRow .= '<div class="clearfix"></div>';
								
							}
						}
						
						$questRow .= '</div>';
				
						$questRow .= '</div>';
						$questRow .= '</div>';
					
						$questRow .= '<div class="clearfix mb5"></div>';
					}
					
					if( $spl_id > 0 )
					{
						$strHtmlSpelQue .= '<div class="panel-group accordion pt5" id="accordionSpl_'.$spl_id.'" role="tablist" aria-multiselectable="true">';
						$strHtmlSpelQue .= '<div class="panel panel-default">';
						$strHtmlSpelQue .= '<div class="panel-heading" role="tab" id="heading_'.$spl_id.'">';
						$strHtmlSpelQue .= '<h4 class="panel-title">';
						$strHtmlSpelQue .= '<a class="collapsed" data-toggle="collapse" data-parent="#accordionSpl" href="#collapse_'.$spl_id.'" aria-expanded="true" aria-controls="collapse_'.$spl_id.'">'.$spl_name.'</a>';
						$strHtmlSpelQue .= '</h4>';
						$strHtmlSpelQue .= '</div>';
					
					
						$strHtmlSpelQue .= '<div id="collapse_'.$spl_id.'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_'.$spl_id.'">';
						$strHtmlSpelQue .= '<div class="panel-body">'.$questRow.'</div>';
						$strHtmlSpelQue .= '</div>';
						$strHtmlSpelQue .= '</div>';
						$strHtmlSpelQue .= '</div>';
					}
					else
					{
						$strHtmlSpelQue .= $questRow;
					}
				}
				
				
				$strHtmlSpelQue .= '</div>';
				echo $strHtmlSpelQue;
			}
		}
		
	}
	
	public function graph_data($for)
	{
		$return = false;
		if(!trim($for)) return $return;
		
		$allowed_graphs = array('blood_sugar','cholesterol','cholesterol_ldl','cholesterol_hdl');
		$allowed = in_array($for,$allowed_graphs) ? true : false ;
		$getSqlDateFormatSmall = str_replace("Y","y",get_sql_date_format());
		
		if( $allowed )
		{
			$table = ($for === 'blood_sugar') ? 'patient_blood_sugar' : 'patient_cholesterol';
			$field = 'cholesterol_total'; $title = 'Cholesterol' ;
			if($for == 'blood_sugar')					{ $field = 'sugar_value'; $title = 'Blood Sugar'; }
			elseif($for == 'cholesterol_ldl')	{ $field = 'cholesterol_LDL'; $title = 'Cholesterol LDL'; }
			elseif($for == 'cholesterol_hdl')	{ $field = 'cholesterol_HDL'; $title = 'Cholesterol HDL'; }
			
			$query = "select *, Date_Format(creation_date, '".$getSqlDateFormatSmall." %l:%i %p') as date from ".$table." where patient_id='".$this->patient_id."' order by creation_date ";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
			
			if($cnt > 1)
			{
				$return = array();
				while($row = imw_fetch_assoc($sql))
				{
					list($date,$time) = explode(' ',$row["date"]);
					if(get_number($date) == '00000000') continue;
					
					$data = array('date' => $row["date"], $for => $row[$field] );
					array_push($return,$data);
				}
			}
			else if($cnt == 1)
				$return = $title . ' Graph needs atleast two records';
			else
				$return = 'No Records found';
			
		}
			
		return $return;
	}
	
	public function hx_data($for)
	{
		$return = false;
		if(!trim($for)) return $return;
		
		$allowed_data = array('blood_sugar','cholesterol');
		$allowed = in_array($for,$allowed_data) ? true : false ;
		$getSqlDateFormatSmall = str_replace("Y","y",get_sql_date_format());
		if( $allowed )
		{
			$table = ($for === 'blood_sugar') ? 'patient_blood_sugar' : 'patient_cholesterol';
			
			if($for == 'blood_sugar')
			{
				$fields = "id, sugar_value, hba1c, hba1c_val, is_fasting, time_of_day, time_of_day_other, description, Date_Format(creation_date, '".$getSqlDateFormatSmall." %l:%i %p') as date";
			}
			elseif($for == 'cholesterol')
			{
				$fields = "id, cholesterol_total, cholesterol_triglycerides, cholesterol_LDL, cholesterol_HDL, description, Date_Format(creation_date, '".$getSqlDateFormatSmall." %l:%i %p') as date";
			}
				
			$fields = trim($fields) ? $fields : '*';
			$query = "select ".$fields."  from ".$table." where patient_id='".$this->patient_id."' order by creation_date Desc ";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
			
			$return = array();
			while($row = imw_fetch_assoc($sql))
			{
				list($date,$time) = explode(' ',$row["date"]);
				if(get_number($date) == '00000000') continue;
					
				if( $for == 'blood_sugar' )
				{
					$row['is_fasting']  = ($row['is_fasting']) ? 'Yes' : 'No';
					$row['time_of_day'] = $row['time_of_day'] == 'Other' ? $row['time_of_day_other']  : $row['time_of_day'] ;
				}
				array_push($return,$row);
			}
			if( count($return) == 0)	$return = 'No Records found';
		}
	
		return $return;
	}
	
	public function admin_medicine($action,$medicineName,$id)
	{
		$action = trim($action) ? trim($action) : '';
		$medicineName = trim($medicineName) ? trim($medicineName) : '';
		$id = trim($id);
		$id = (int) $id;
		
		if( empty($action) ) return false;
		
		global $cls_common;
		
		if( $action === 'insert' || $action === 'update' && !empty($medicineName) )
		{
			$query = "select * from medicine_data where UCASE(medicine_name)=UCASE('".$medicineName."') AND del_status = '0' ".($id > 0 ? " And id <> '".$id."'" : '')." ";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
			
			if($cnt > 0)	return 2;
			else
			{
				$query = (( $action == 'update')  ? 'Update ' : 'Insert Into ' ). " medicine_data set medicine_name='".$medicineName."' ";
				$query .= (( $action == 'update')  ? "Where id = '".$id."'" : '' );
				
				$sql = imw_query($query);
				$cls_common->create_medications_xml();
				if($sql) return 1;
			}
		}
		elseif($action === 'delete')
		{
			$query = "Delete from medicine_data where id='".$id."' AND del_status = '0' ";
			$sql = imw_query($query);
			$cls_common->create_medications_xml();
			if($sql) return 1;
		}
		
		return false;
		
		
	}
	
	public function admin_medicine_data()
	{
			$query = "Select DISTINCT medicine_name,id from medicine_data where del_status = '0' order by medicine_name asc"; 
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);
			 
			$html  = '';
			$html	.=	'<table id="admin_med_table" class="table table-bordered table-hover table-striped scroll release-table">';
			$html	.=	'<thead class="header">';
			$html	.=	'<tr class="grythead">';	
			$html	.=	'<th class="col-xs-10">Medication</th>';
			$html	.=	'<th class="col-xs-2">Action</th>';
			$html	.=	'</tr>';
			$html	.=	'</thead>';
			$html	.=	'<tbody>';
			
			
			if( $cnt > 0 )
			{
				
				while( $row = imw_fetch_assoc($sql) )
				{
					
					$medName = !empty($row['medicine_name']) ? $row["medicine_name"] : "&nbsp;";
					$html .= '<tr id="admin_med_row'.$row['id'].'">';
					$html .= '<td>';
					$html .= '<a class="pointer set-medicine" data-medicine-name="'.$medName.'" href ="javascript:void(0);">'.$medName.'</a>';
					$html .= '</td>';
					$html .= '<td >';
					$html .= '<img src="'.($GLOBALS['webroot'].'/library/images/editrec.png').'" width="25" class="pointer edit-medicine" data-record-id="'.$row['id'].'" data-medicine-name="'.$medName.'" title="Edit"/>&nbsp;';
					$html .= '<img src="'.($GLOBALS['webroot'].'/library/images/delete1.png').'" width="22" class="pointer del-medicine" data-record-id="'.$row['id'].'" title="Delete" />';
					$html .= '</td>';
					$html .= '</tr>';
				}
			}
			else
			{
				$html .= '<tr><td colspan="2" class="bg bg-info">No Record Found</td></tr>'; 
			}
			$html	.=	'</tbody>';
			$html	.=	'</table>';	
			
			return $html;		
	}
	
	public function medicine_typeahead()
	{
		global $cls_common;
		$return = array();
		$medicationTitleArr = $medication_ccdacode_Arr = $arrMedicines = $fdb_id_arr = array();
		
		$medXMLFileExits = false;
		$medXMLFile = data_path() ."xml/Medications.xml";
		if(file_exists($medXMLFile)){
			$medXMLFileExits = true;
		}
		else
		{
			$cls_common->create_medications_xml();	
			if(file_exists($medXMLFile)){
				$medXMLFileExits = true;	
			}	
		}
		
		if($medXMLFileExits == true)
		{
			$values = array();
			$XML = file_get_contents($medXMLFile);
			$values = $cls_common->xml_to_array($XML);
			
			foreach($values as $key => $val)
			{	
				$medicationName = "";
				if( ($val["tag"] =="medicationInfo") && ($val["type"]=="complete") && ($val["level"]=="2") )
				{		
					$medicationName = "";
					$intMedOcular = 0;
					$medicationName = str_replace("'","",$val["attributes"]["name"]);	
					$intMedOcular = $val["attributes"]["medOcular"];
					if($intMedOcular != 0){
						$medicationName = $medicationName." * Ocular";
					}
					$medicationTitleArr[]=$medicationName;
					$medication_ccdacode_Arr[$medicationName] = $val["attributes"]["ccda_code"];
					$medication_doses_Arr[$medicationName] = $val["attributes"]["dosage"];
					$medication_sig_Arr[$medicationName] = $val["attributes"]["sig"];
					$fdb_id_arr[$medicationName] = $val["attributes"]["fdb_id"];
					if((int)$val["attributes"]["medAlert"] == 1){
						$arrMedicines[$medicationName] = $medicationName;
					}
				}
			}
			
		}
		
		$return['medicationTitleArr'] = $medicationTitleArr;
		$return['medication_ccdacode_Arr'] = $medication_ccdacode_Arr;
		$return['medication_doses_Arr'] = $medication_doses_Arr;
		$return['medication_sig_Arr'] = $medication_sig_Arr;
		$return['arrMedicines'] = $arrMedicines;
		$return['fdb_id_arr'] = $fdb_id_arr; 
		
		return $return;
		
	}
	
	public function med_external()
	{
		$query = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl WHERE patient_id = '".$this->patient_id."' ORDER BY scan_doc_id ";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		
		$html  = '';
		$html	.=	'<table id="med_external" class="table table-bordered table-hover table-striped scroll release-table">';
		$html	.=	'<thead class="header">';
		$html	.=	'<tr class="grythead">';	
		$html	.=	'<th class="col-xs-1">Sr.</th>';
		$html	.=	'<th class="col-xs-2">Date</th>';
		$html	.=	'<th class="col-xs-2">Type</th>';
		$html	.=	'<th class="col-xs-7">File</th>';
		$html	.=	'</tr>';
		$html	.=	'</thead>';
		$html	.=	'<tbody>';
		
		if($cnt > 0 )
		{	
			for($i=0;$i<$cnt ;$i++)
			{
				$row = imw_fetch_array($sql);
				
				$row['upload_date'] = get_date_format($row['upload_date']);
				$row['chart_note_date'] = get_date_format($row['chart_note_date']);
				$row['upload_docs_date'] = get_date_format($row['upload_docs_date']);
				
				$date ="";
				if( !empty($row["upload_date"]) ) $date = $row['upload_date'];
				elseif( !empty($row["chart_note_date"]) ) $date = $row['chart_note_date'];
				elseif( !empty($row["upload_docs_date"]) ) $date = $row['upload_docs_date'];
				
				$type = ucfirst($row["CCDA_type"]);	
				$file = $row["doc_title"];
				if(empty($file)) { $file=$row["pdf_url"]; }
				
				$onclick = 'window.top.fmain.openWindowCCDImport(\''.data_path().substr($row["file_path"],1).'\',\''.$row["CCDA_type"].'\')';
				$html .= '<tr class="pointer" onClick="'.$onclick.'">';
				$html .= '<td>'.($i+1).'</td>';
				$html .= '<td>'.$date.'</td>';
				$html .= '<td>'.$type.'</td>';
				$html .= '<td>'.$file.'</td>';
				$html .= '</tr>';
		
			}
		}
		else
		{
				$html .= '<tr><td colspan="4" class="bg bg-info">No Record Found</td></tr>';
		}
		$html	.=	'</tbody>';
		$html	.=	'</table>';	
		
		return $html	;
	}
	
	public function users()
	{
		$query = "SELECT id, fname, mname, lname, user_type FROM users WHERE fname!='' AND lname!='' AND delete_status = 0 ORDER BY fname,lname ";
		$sql = imw_query($query);
		if($sql)
		{
			while( $row = imw_fetch_assoc($sql) )
			{
				$arrUserId[$row["id"]]["fname"] = $row["fname"];
				$arrUserId[$row["id"]]["lname"] = $row["lname"];
			}
		}
		
		return $arrUserId;
	}
	
	public function med_history($record_id,$tab = 'Medications')
	{
		$arrUserId = $this->users();
		
		$query = "SELECT ple.operator_id, DATE_FORMAT(plec.date_time, '".get_sql_date_format()." %I:%i %p') as recDateTime, 
								 plec.section_table_primary_key as tablePriKey, plec.operator_id  as operator_id_plec, plec.action
								 FROM patient_last_examined ple 
								 INNER JOIN patient_last_examined_child plec ON ple.patient_last_examined_id = plec.master_pat_last_exam_id
								 INNER JOIN users us ON us.id = ple.operator_id  
								 WHERE 
									 ple.patient_id ='".$this->patient_id."'
									 AND plec.section_table_primary_key = '".$record_id."'
									 AND (ple.section_name = '".$tab."' OR ple.section_name = 'complete') 
								 GROUP BY 
								 	plec.section_table_primary_key,
									plec.date_time 
								ORDER BY 
									plec.date_time DESC";	
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		
		$html  = '';
		$html	.=	'<table id="med_history" class="table table-bordered table-hover table-striped scroll release-table">';
		$html	.=	'<thead class="header">';
		$html	.=	'<tr class="grythead">';	
		$html	.=	'<th class="col-xs-1">Sr.</th>';
		$html	.=	'<th class="col-xs-4">Operator</th>';
		$html	.=	'<th class="col-xs-4">Date Time</th>';
		$html	.=	'<th class="col-xs-3">Action</th>';
		$html	.=	'</tr>';
		$html	.=	'</thead>';
		$html	.=	'<tbody>';
		
		if($cnt > 0)
		{
			$i = 0;
			while( $row = imw_fetch_array($sql))
			{
				$i++;
				$opName = "";
				if($row['operator_id_plec']>0){
					$operator_id_plec=$row['operator_id_plec'];
				}else{
					$operator_id_plec=$row['operator_id'];
				}
				$opName = core_name_format($arrUserId[$operator_id_plec]['lname'],$arrUserId[$operator_id_plec]['fname']);
                
				$html .= '<tr>';
				$html .= '<td>'.$i.'</td>';
				$html .= '<td>'.$opName.'</td>';
				$html .= '<td>'.$row["recDateTime"].'</td>';
				$html .= '<td>'.$row["action"].'</td>';
				$html .= '</tr>';
				
			}
		}
		else
		{
				$html .= '<tr><td colspan="4" class="bg bg-info">No Record Found</td></tr>';
		}
		$html	.=	'</tbody>';
		$html	.=	'</table>';	
		
		return $html;
	}
	
	public function get_tab_title($tab_name){
		switch($tab_name){
			case 'cc_history':
				$tab_name = 'cc_&_hx';
			break;
			
			case 'hms':
				$tab_name = 'phms';
			break;
			
			case 'vs':
				$tab_name = 'vital_signs';
			break;
			case 'hp':
				$tab_name = 'H_&_P';
			break;	
		}
		$title = ucwords(str_replace('_',' ',$tab_name));
		return $title;
	}
	
	public function get_snomed($name, $type = 'reaction', $from = 'icd10' )
	{
		$tbl = ($from == 'icd9') ? 'diagnosis_code_tbl DCT' : 'icd10_data ICD JOIN diagnosis_code_tbl DCT on ICD.icd9 = DCT.dx_code ';
		$where = ($from == 'icd9') ? "Where DCT.diag_description = '".$name."'" : "Where ICD.icd10_desc = '".$name."' ";
		
		$code = '';
		$qry = "Select DCT.snowmed_ct From ".$tbl." ".$where." ";
		$sql = imw_query($qry);
		$cnt = imw_num_rows($sql);
		if( $cnt > 0 )
		{
			$row = imw_fetch_assoc($sql);
			$code = $row['snowmed_ct'];
		}
		
		return $code;
	}
	
	//Returns Vital sign fields data
	public function get_vs_data(){
		$vs_form_id = $_SESSION['form_id'];
		if(!isset($_SESSION['form_id'])){
			$vs_form_id = $_SESSION['finalize_id'];
		}
		$return_arr = array();
		$getSysBP = $getDysBP = $getBmiHeight = $getBmiHeightUnit = $getBmiWeight = $getBmiWeightUnit = $getBmiResult = ""; 
		
		$ptFormId = '';
		$chkForm = imw_query('SELECT id FROM chart_master_table where patient_id = "'.$this->patient_id.'" AND id = "'.$vs_form_id.'"  AND delete_status = "0" ORDER BY date_of_service') or die(imw_error());
		if(imw_num_rows($chkForm) > 0){
			$rowFetch = imw_fetch_assoc($chkForm);
			$ptFormId = $rowFetch['id'];
		}else {
			$chkForm = imw_query('SELECT id FROM chart_master_table where patient_id = "'.$this->patient_id.'"  AND delete_status = 0 ORDER BY date_of_service DESC LIMIT 0,1') or die(imw_error());
			if(imw_num_rows($chkForm) > 0){
				$rowFetch = imw_fetch_assoc($chkForm);
				$ptFormId = $rowFetch['id'];
			}
		}
		
		$vsDataQry = "SELECT vsp.vital_master_id, vsp.vital_sign_id, vsp.range_vital, vsp.unit, vsm.form_id, vsm.patient_id FROM vital_sign_patient as vsp INNER JOIN vital_sign_master as vsm ON vsp.vital_master_id=vsm.id AND vsm.patient_id='".$this->patient_id."' AND vsp.vital_sign_id IN(1,2,7,8,9) AND vsm.status = 0 AND vsm.form_id = '".$ptFormId."'";
		
		$vsDataRes = imw_query($vsDataQry);
		if(imw_num_rows($vsDataRes) == 0 ){
			$vsDataQry = "SELECT vsp.vital_master_id, vsp.vital_sign_id, vsp.range_vital, vsp.unit, vsm.form_id, vsm.patient_id FROM vital_sign_patient as vsp INNER JOIN vital_sign_master as vsm ON vsp.vital_master_id=vsm.id AND vsm.patient_id='".$this->patient_id."' AND vsp.vital_sign_id IN(1,2,7,8,9) AND vsm.status = 0 INNER JOIN (Select id from vital_sign_master WHERE patient_id='".$this->patient_id."' ORDER BY date_vital DESC LIMIT 0,1)T ON T.id = vsm.id;";
			$vsDataRes = imw_query($vsDataQry);
		}

		if(imw_num_rows($vsDataRes)>0){
			while($vsDataRow = imw_fetch_assoc($vsDataRes)){
				/* //Getting form id for the patient to validate if it is the correct form id or not
				$ptFormId = '';
				$chkForm = imw_query('SELECT id FROM chart_master_table where patient_id = "'.$vsDataRow['patient_id'].'" AND id = "'.$vsDataRow['form_id'].'"  AND delete_status = 0 ORDER BY date_of_service DESC');
				if(imw_num_rows($chkForm) > 0){
					$rowFetch = imw_fetch_assoc($chkForm);
					$ptFormId = $rowFetch['id'];
				} */
				
				//If form id is empty so skip the below code
				//if(empty($ptFormId)) break;
				
				if($vsDataRow['vital_sign_id']==1){
				  $getSysBP = $vsDataRow['range_vital'];
				}
				if($vsDataRow['vital_sign_id']==2){
				  $getDysBP = $vsDataRow['range_vital'];
				}
				//CONVERT INCHES TO FEET AND INCHES
				if($vsDataRow['vital_sign_id']==7 && trim($vsDataRow['unit'])== 'inch'){
				  $getBmiHeight = intval($vsDataRow['range_vital']/12);
				  $getBmiHeightUnit = intval($vsDataRow['range_vital'] % 12);
				}
				//CONVERT METER TO INCHES AND INCHES TO  FEET AND INCHES
				if($vsDataRow['vital_sign_id']==7 && trim($vsDataRow['unit'])== 'm'){
				  $getBmiHeight = intval($vsDataRow['range_vital']  * 39.37);
				  $getBmiHeight = intval($getBmiHeight/12);
				  $getBmiHeightUnit = intval($getBmiHeight % 12);
				}
				//CONVERT CM TO INCHES AND INCHES TO  FEET AND INCHES
				if($vsDataRow['vital_sign_id']==7 && trim($vsDataRow['unit'])== 'cm'){
				  $getBmiHeight = intval($vsDataRow['range_vital']  * 0.3937);
				  $getBmiHeight = intval($getBmiHeight/12);
				  $getBmiHeightUnit = intval($getBmiHeight % 12);
				}
				if($vsDataRow['vital_sign_id']==8){
				  $getBmiWeight = $vsDataRow['range_vital'];
				  $getBmiWeightUnit = $vsDataRow['unit'];
				  
				}
				if($vsDataRow['vital_sign_id']==9){
				  $getBmiResult = $vsDataRow['range_vital'];
				}
			}
			$return_arr['BP_sys'] = $getSysBP;
			$return_arr['BP_dys'] = $getDysBP;
			$return_arr['BMI_height'] = $getBmiHeight;
			$return_arr['BMI_height_unit'] = $getBmiHeightUnit;
			$return_arr['BMI_weight'] = $getBmiWeight;
			$return_arr['BMI_weight_unit'] = $getBmiWeightUnit;
			$return_arr['BMI_result'] = $getBmiResult;
			
			//break;
		}
		
		$return_arr['BP_array'] = array("Normal BP reading documented, follow-up not required", "Hypertensive BP reading documented, AND the indicated follow-up is documented", "Patient not eligible – known hypertensive", "BP reading not documented, reason not given","Hypertensive BP reading documented, indicated follow-up not documented, reason not given");
		
		$return_arr['BMI_array'] = array("BMI is documented within normal parameters", "BMI is documented above normal parameters and a follow-up plan is documented", "BMI is documented below normal parameters and a follow-up plan is documented", "BMI not documented and no reason is given","BMI documented outside normal parameters, no follow-up plan documented");
		
		$return_arr['Pt_Form_Id'] = $ptFormId;
		
		return $return_arr;
	}
	
}

?>