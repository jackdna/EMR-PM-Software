<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Class File Related to General Health
 Access Type: Indirect Access.
 
*/
include_once 'medical_history.class.php';
class GeneralHealth extends MedicalHistory
{
	public $vocabulary = false;
	public $data = false;
	public $pat_relation = false;			//arrPtRel
	public $show_code_arr = array();
	public $show_smoke_arr = array();
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->vocabulary = $this->get_vocabulary("medical_hx", "general_health");
		$this->pat_relation = get_relationship_array('general_health');
		$this->data = $this->load_general_health();
		$this->smoke();
	}
	
	public function load_general_health()
	{
		global $cls_common;
		$return = array();
		
		$reff_physician = $this->get_reff_physicians();
		$gen_medicine = $this->general_medicine();
		$patPCPMedHx = $cls_common->get_ref_phy_name($gen_medicine['med_doctor_id']);
		$strPCPPhyMulti = '';
		foreach($reff_physician['id'] as $i => $v)
		{
			if($v != $gen_medicine['med_doctor_id'])
				$tmpArr[] = $reff_physician['name'][$i];
		}
		$strPCPPhyMulti = implode("; ",$tmpArr);;
		if($strPCPPhyMulti){
			$patPCPMedHx .= ($patPCPMedHx ?  "; " : '').$strPCPPhyMulti;
		}
		
		//Create Detaile popover for primary care providers
		$popoverPCP = '';
		$counter = 0;
		foreach($reff_physician['data'] as $d){ $counter++;
			$str = '';
			
			$strRefNameDB = $cls_common->get_ref_phy_name($d["refPhyID"]);
			
			$address = "";
			$address = core_extract_user_input($d['Address1']);
			if($address != "" && $d['Address2']!= ""){
				$address .= ", ".core_extract_user_input($d['Address2']);
			}
			else if($address == "" && $d['Address2']!= ""){
				$address = core_extract_user_input($d['Address2']);
			}
			if($address != ""){
				$address .= ", ".core_extract_user_input($d['City']);
			}
			else{
				$address = core_extract_user_input($d['City']);
			}
			if($address != ""){
				$address .= ", ".core_extract_user_input($d['State'])." ".core_extract_user_input($d['ZipCode']);
			}
			else{
				$address = core_extract_user_input($d['State'])." ".core_extract_user_input($d['ZipCode']);
			}
			if(trim($address) != ""){
				$address .= "\n";
			}
			if($d['physician_phone'] != ""){
				$address .= "Phone: ".$d['physician_phone']."\n";
			}
			$address = trim($address);
			$str .= "<div><b>".$strRefNameDB."</b><br>";
			$str .= $address."</div>";
			$str .= ($counter < count($reff_physician['data'])) ? "<div class='border-dashed'></div>" : "";
			$popoverPCP .= $str;
		}
		$return['popover'] = $popoverPCP;
		$return['arrPCPPhy'] = $reff_physician['id'];
		$return['arrPCPPhyName'] = $reff_physician['name'];
		$return['gen_medicine'] = 	$gen_medicine;						//result_ado
		$return['patPCPMedHx'] = $patPCPMedHx;
		
		//any conditions you
		$any_conditions_u1_arr = explode(",",$gen_medicine["any_conditions_you"]);
		foreach($any_conditions_u1_arr as $key => $val){	
			if($val != ""){
				$return['acya_p1'][$val] = "checked";
			}
		}

		//any conditions others
		$any_conditions_others_pat = get_set_pat_rel_values_retrive($gen_medicine["any_conditions_others_both"],'pat',$this->delimiter);
		$any_conditions_others_both_arr = explode(",",$any_conditions_others_pat);
		foreach($any_conditions_others_both_arr as $key => $val){	
			if($val != ""){
				if($key==1){
					$anyConditionsOthersBothArrGenHealth = ",1,";
				}
				$return['acob'][$val]="checked";	
			}
		}
		//under control
		$chk_under_control_pat = get_set_pat_rel_values_retrive($gen_medicine["chk_under_control"],'pat',$this->delimiter);
		$chk_under_control_arr = explode(",",$chk_under_control_pat);
		foreach($chk_under_control_arr as $key => $val){	
			if($val != ""){
				$return['is_checked_under_control'][$val] = "checked";
			}
		}
		//Constitutional & Integumentary
		$review_const_arr = explode(",",$gen_medicine["review_const"]);
		foreach($review_const_arr as $key => $val){	
			if($val != ""){
				$return['review_const'][$val] = "checked";	
			}
		}
		//Head/ Neck 
		$review_head_arr = explode(",",$gen_medicine["review_head"]);
		foreach($review_head_arr as $key => $val){	
			if($val != ""){
				$return['review_head'][$val] = "checked";			
			}
		}
		//Respiratory 
		$review_resp_arr = explode(",",$gen_medicine["review_resp"]);
		foreach($review_resp_arr as $key => $val){	
			if($val != ""){
				$return['review_resp'][$val] = "checked";
			}
		}
		//Cardiovascular
		$review_card_arr = explode(",",$gen_medicine["review_card"]);
		foreach($review_card_arr as $key => $val){	
			if($val != ""){
				$return['review_card'][$val] = "checked";		
			}
		}
		//Gastrointenstinal 
		$review_gastro_arr=explode(",",$gen_medicine["review_gastro"]);
		foreach($review_gastro_arr as $key => $val){	
			if($val != ""){
				$return['review_gastro'][$val]="checked";		
			}
		}
		//Genitourinary
		$review_genit_arr = explode(",",$gen_medicine["review_genit"]);
		foreach($review_genit_arr as $key => $val){	
			if($val != ""){
				$return['review_genit'][$val]="checked";		
			}
		}
		//Allergic/Immunologic & Blood/ Lymphatic 
		$review_aller_arr = explode(",",$gen_medicine["review_aller"]);
		foreach($review_aller_arr as $key => $val){	
			if($val != ""){
				$return['review_aller'][$val]="checked";	
			}
		}
		//Neurological Psychiatry & Musculoskeletal 
		$review_neuro_arr = explode(",",$gen_medicine["review_neuro"]);
		foreach($review_neuro_arr as $key => $val){	
			if($val != ""){
				$return['review_neuro'][$val]="checked";	
			}
		}
		
		$review_sys = $gen_medicine["review_sys"];
		if(!empty($review_sys)){
			$ar_review_sys = json_decode($review_sys, true);
			$ar_tmp = array('review_intgmntr',	'review_psychiatry', 'review_blood_lymph','review_musculoskeletal','review_endocrine','review_eye');
			foreach($ar_tmp as $k => $v){
				$review_tmp_arr = explode(",",$ar_review_sys[$v]);
				foreach($review_tmp_arr as $key => $val){	
					if($val != ""){
						$return[$v][$val]="checked";	
					}
				}
				$vother = $v."_others";
				$return[$vother] = $ar_review_sys[$vother];
			}
		}		
		
		//Ticked as Negative
		$negChkBxArr = explode(',',$gen_medicine["negChkBx"]);
		foreach($negChkBxArr as $key => $val){	
			if($val != ""){
				$return['negChkBx'][$val] = "checked";	
			}
		}

		//any conditions you No - patient
		$arrAnyConditionsYouN = array();
		$any_conditions_u1_arr_n = explode(",",$gen_medicine["any_conditions_you_n"]);
		foreach($any_conditions_u1_arr_n as $key => $val){	
			if($val != ""){
				$return['arrAnyConditionsYouN'][$val] = "checked";
			}
		}

		//any conditions you No - family
		$arrAnyConditionsRelativeN = array();
		$any_conditions_relative1_n_arr = explode(",",$gen_medicine["any_conditions_relative1_n"]);
		foreach($any_conditions_relative1_n_arr as $key => $val){	
			if($val != ""){
				$return['arrAnyConditionsRelativeN'][$val] = "checked";
			}
		}
		//pre($arrAnyConditionsRelativeN);
		//Sub Conditions
		$elem_subCondition_pat_val = get_set_pat_rel_values_retrive($gen_medicine["sub_conditions_you"],'pat',$this->delimiter);
		$return['elem_subCondition_u1'] = explode(",",$elem_subCondition_pat_val);

		//desc
		$return['elem_desc_u'] = $gen_medicine["desc_u"];
		$return['elem_desc_r'] = $gen_medicine["desc_r"];
		
		$return['intLastReview'] = 0;
		
		//$allRelVals = $this->get_combo_multi($strRelDescHighBp,$this->pat_relation,'forString');
		//$return['arrAllRelVals']=explode(',', $allRelVals);
		$arrayField = array( "relDescHighBp", "relDescHeartProb", "relDescArthritisProb", "relDescLungProb", "relDescStrokeProb", "relDescThyroidProb",
												 "desc_r","relDescLDL", "relDescUlcersProb", "relDescCancerProb", "ghRelDescOthers");
		foreach($arrayField as $val)
		{
			$key = ($val == 'desc_r') ? 'elem_desc_r' :$val;
			$return['other_'.$key] = $this->get_other_field_val($gen_medicine[$val],$this->pat_relation);
		}

		//Vital Sign Data
		$return['vs_data'] = $this->get_vs_data();
		
		return $return;
	}
	
	public function get_reff_physicians()
	{
		global $cls_common;
		$return = array();
		$arrPCPPhy = $arrPCPPhyName = $data = array();
		$query = "select distinct(pmrf.ref_phy_id), pmrf.phy_type, TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName,if(refPhy.MiddleName!='',' ',''),refPhy.Title)) as refName, refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, refPhy.physician_email, refPhy.physician_Reffer_id refPhyID
											From patient_multi_ref_phy pmrf
											Inner Join refferphysician refPhy
											ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
											where pmrf.patient_id = '".$this->patient_id."' 
											And pmrf.phy_type IN(3,4) And pmrf.status = '0' ";
		$sql = imw_query($query);
		if(imw_num_rows($sql) > 0)
		{
			while($row = imw_fetch_array($sql))
			{
				$arrPCPPhy[] = $row["ref_phy_id"];
				$arrPCPPhyName[] = $cls_common->getRefPhyName($row["ref_phy_id"]);
				$data[$row["ref_phy_id"]] = $row;
			}
			imw_free_result($sql);
		}
		
		$return['id'] = $arrPCPPhy;
		$return['name'] = $arrPCPPhyName;
		$return['data'] = $data;
		return $return;
		
	}
	
	public function general_medicine()
	{
		$query = "select PD.id as ptID, PD.ado_option as ptAdoOption, PD.sex as ptGender, PD.desc_ado_other_txt as ptDescAdoOtherTxt,
									PD.primary_care_phy_id as med_doctor_id, PD.primary_care_phy_name as med_doctor, PD.assigned_nurse, GM.* 
									from general_medicine GM INNER JOIN 
									patient_data PD on PD.Id = GM.patient_id 
									where GM.patient_id  = '".$this->patient_id."' LIMIT 1";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		$row = imw_fetch_assoc($sql);
		$row['record_count'] = $cnt;
		
		//Blood Sugar dropdown arr
		$row['blood_sugar_opt_arr'] = array("Most recent hemoglobin A1c level > 9.0%", "Hemoglobin A1c level was not performed last 12 months", "Most recent hemoglobin A1c (HbA1c) level < 7.0%", "Most recent hemoglobin A1c (HbA1c) level 7.0 to 9.0%");
		
		//Vaccine dropdown array
		$row['vaccine_flu_arr'] = array("Influenza immunization administered or previously received", "Influenza immunization not administered for medical reasons", "Influenza immunization not administered, no reason given");
		
		//Pneumococcal Vaccine dropdown array
		$row['pneu_vac_arr'] = array("Pneumococcal vaccination administered or previously received", "Pneumococcal vaccine was not administered or previously received, reason not otherwise specified");

		$row['fall_risk_assess_array'] = array("Falls Risk Assessment Documented and patient screened for future falls risk", "Not peformed – patient not ambulatory", "Patient not at risk for falls", "Falls status not documented");
		
		return $row;
	}
	
	public function smoke()
	{
		$query = "select * from smoking_status_tbl where status='0'";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		while($row = imw_fetch_assoc($sql))
		{
			$show_smoke= ucfirst($row['desc']);
			$this->show_code_arr[$row['id']]= $row['code'];
	 		$this->show_smoke_arr[$row['id']]= $show_smoke;
		}		
	}
	
	
	/*
	* Save No Known Medical Conditions of patient 
	* From General Health Chart in H&P Chart.
	*/
	public function save_med_cond_hp(){
		
		$HPGHMap = array(1=>'htnCP',2=>'cadMI',7=>'arthritis',4=>'respiratoryAsthma',5=>'cvaTIA',6=>'thyroid',3=>'diabetes',13=>'highCholesterol',8=>'ulcer',14=>'historyCancer');
		
		// Get patient record from General Health Chart
		$fld = "";
		$fld.= "any_conditions_you, any_conditions_you_n, any_conditions_others, desc_u as diabetesDesc,";
		$fld.= "desc_high_bp as htnCPDesc, desc_arthrities as arthritisDesc, desc_lung_problem as respiratoryAsthmaDesc, desc_stroke as cvaTIADesc,";
		$fld.= "desc_thyroid_problems as thyroidDesc, desc_ulcers as ulcerDesc, desc_LDL as highCholesterolDesc, desc_cancer as historyCancerDesc, desc_heart_problem as cadMIDesc";
		$qry = "Select ".$fld."  From general_medicine Where patient_id = ".(int)$this->patient_id." ";
		$sql = imw_query($qry);
		$rowGH = imw_fetch_assoc($sql);
		
		// Get patient record from H&P Chart
		$qry = "Select * From surgerycenter_pt_history_physical Where patient_id = ".(int) $this->patient_id." ";
		$sql = imw_query($qry);
		$cnt = imw_num_rows($sql);
		
		$saveArr= array();
		
		$tmpArr = explode(",",$rowGH['any_conditions_you']);
		$tmpArr = array_filter($tmpArr);
		foreach($tmpArr as $q) {
			if( $q && $HPGHMap[$q] ) {
				$d = $HPGHMap[$q].'Desc';
				$desc = get_set_pat_rel_values_retrive($rowGH[$d],'pat');
				$saveArr[$HPGHMap[$q]] = 'Yes';
				$saveArr[$d] = $desc;
			}
		}
		
		
		$tmpArr = explode(",",$rowGH['any_conditions_you_n']);
		$tmpArr = array_filter($tmpArr);
		foreach($tmpArr as $q) {
			if( $q && $HPGHMap[$q] ) {
				$d = $HPGHMap[$q].'Desc';
				$desc = get_set_pat_rel_values_retrive($rowGH[$d],'pat');
				$saveArr[$HPGHMap[$q]] = 'No';
				$saveArr[$d] = $desc;
			}
		}
		
		$otherVal = get_set_pat_rel_values_retrive($rowGH['any_conditions_others'],'pat');
		
		if( (is_array($saveArr) && count($saveArr) > 0) || $otherVal ) {
			
			// Start Updating No Known Medical Conditions in H&P Chart
			$upQry = "Insert Into surgerycenter_pt_history_physical Set ";
			$upQry.= "patient_id = ".(int)$this->patient_id.", ";
			$upQry.= "create_date_time = '".date('Y-m-d H:i:s')."', ";
			$upQry.= "create_operator_id = ".(int)$_SESSION['authId'].", ";

			$upWhere = '';
			if( $cnt > 0 ) {
				$upQry = "Update surgerycenter_pt_history_physical Set ";
				$upQry.= "save_date_time = '".date('Y-m-d H:i:s')."', ";
				$upQry.= "save_operator_id = ".(int)$_SESSION['authId'].", ";
				$upWhere = "Where patient_id = ".(int)$this->patient_id."  ";
			} 
		
			foreach($saveArr as $hpFld => $hpFldVal) {
				$upQry.= $hpFld ." = '".$hpFldVal."', ";		
			}
			
			
			$upQry.= "otherHistoryPhysical = '".$otherVal."' ";
			$upQry.= $upWhere;
		
			$upSql = imw_query($upQry);
		}
		
		return false;
	}
	
}


?>