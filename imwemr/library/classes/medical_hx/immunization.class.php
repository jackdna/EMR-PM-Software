<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> Immunization
 Access Type: Indirect Access.
 
*/
include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
$cls_alerts = new CLSAlerts;

class Immunization extends MedicalHistory
{
	//Public variabels
	public $imm_vocab = '';
	public $stringAllimnz = '';
	public $routeset_codes = array();
	public $bodysite_codes = array();
	public $vfc_codes = array();
	public $publicity_code_array = array();
	public $nip1TxtArr 	= array();
	public $nip2TxtArr 	= array();
	public $manufactureTxtArr 	= array();
	public $immunDataArr = array();
	public $immunPubDataArr = array();
	public $cvx_arr_js = array();
	public $arrCVXCodes = array();
	public $filter_data_arr = array();
	public $arr_info_alert = array();

	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		//HL70162 (ROUTESET DATA MAPPING ARRAY)
		$this->routeset_codes = array('ID'=>'Intradermal','IM'=>'Intramuscular','IV'=>'Intravenous','NS'=>'Nasal','OTH'=>'Other/Miscellaneous','PO'=>'Oral','SC'=>'Subcutaneous','TD'=>'Transdermal');
		
		//HL70163 (BODY SITE CODESET, DATA MAPPING ARRAY)
		$this->bodysite_codes = array('LA'=>'Left Upper Arm','LD'=>'Left Deltoid','LG'=>'Left Gluteous Medius','LLFA'=>'Left Lower Forearm','LT'=>'Left Thigh','LVL'=>'Left Vastus Lateralis','RA'=>'Right Upper Arm','RD'=>'Right Deltoid','RG'=>'Right Gluteous Medius','RLFA'=>'Right Lower Forearm','RT'=>'Right Thigh','RVL'=>'Right Vastus Lateralis');
		
		//HL70064 (FINANCIAL CLASS (VFC) CODESET DATA MAPPING ARRAY)
		$this->vfc_codes = array('V01'=>'Not VFC eligible','V02'=>'VFC eligible-Medicaid/Medicaid Managed Care','V03'=>'VFC eligible- Uninsured','V04'=>'VFC eligible- Americal Indian/Alaskan Native','V05'=>'VFC eligible-Federally Qualified Health Center Patient (under-insured)','V06'=>'VFC eligible- State specific eligibility (e.g. S-CHIP plan)','V07'=>'VFC eligibility- Local-specific eligibility','V08'=>'Not VFC eligible-Under-insured');
		
		//PUBLICITY DATA MAPPING ARRAY
		$this->publicity_code_array = array("01"=>"No reminder/recall","02"=>"Reminder/recall - any method","03"=>"Reminder/recall - no calls","04"=>"Reminder only - any method","05"=>"Reminder only - no calls","06"=>"Recall only - any method","07"=>"Recall only - no calls","08"=>"Reminder/recall - to provider","09"=>"Reminder to provider","10"=>"Only reminder to provider no recall","11"=>"Recall to provider","12"=>"Only recall to provider no reminder");
		
		//Filter data array
		$this->filter_data_arr = array(''=>'All','Administered'=>'Administered','Declined'=>'Declined','No Due'=>'No Due','Completed'=>'Completed','Cancelled'=>'Cancelled');
		
		//Setting Nip text array i.e nip1TxtArr,nip2TxtArr
		$table = array('dd_cdc_nip001','dd_cdc_nip002');
		$this->get_nip_arr($table);
		
		//Setting manufacturing array
		$this->set_manufac_arr();
		
		//Setting immunization names array
		$this->set_imm_names_arr();
		
		//Setting CVX code arr
		$this->set_cvx_arr();
		
		//Vocabulary 
		$this->arr_info_alert = $this->get_vocabulary('medical_hx','immunizations');
	}
	
	//Set Nip array
	public function get_nip_arr($table){
		$counter = 0;
		foreach($table as $tbl_name){
			$nipQry = "SELECT code,value_txt FROM ".$tbl_name." ORDER BY value_txt";
			$nipRes = imw_query($nipQry);
			if(imw_num_rows($nipRes)>0) {
				while($nip1Row = imw_fetch_array($nipRes)) {
					if($counter > 0){
						$this->nip2TxtArr[$nip1Row['code']] = $nip1Row['value_txt'];
					}else{
						$this->nip1TxtArr[$nip1Row['code']] = htmlentities($nip1Row['value_txt']);
					}
				}
			}
			$counter++;
		}
	}
	
	//Set manufacturing array
	public function set_manufac_arr(){
		$new_arr = array();
		$manufactureQry = "SELECT MVX_CODE,manufacturer_name FROM immunization_manufacturer ORDER BY manufacturer_name";
		$manufactureRes = imw_query($manufactureQry);
		if(imw_num_rows($manufactureRes)>0) {
			while($manufactureRow = imw_fetch_array($manufactureRes)){
				$new_arr[$manufactureRow['MVX_CODE']] = $manufactureRow['manufacturer_name'];
			}
		}
		$this->manufactureTxtArr = $new_arr;
	}
	
	//Set immunization names arr 
	public function set_imm_names_arr(){
		$sql = "select * from immunization_admin order by imnzn_name ASC";
		$rez = imw_query($sql);	
		while($row=imw_fetch_array($rez)){
			$imnzn_id = $row["imnzn_id"];
			$imnzn_name = $row["imnzn_name"];
			$imunz_cvx_coe = $row["imunz_cvx_coe"];
			$this->stringAllimnz.="'".addslashes($imnzn_name)."',";
			$this->cvx_arr_js[$row["imnzn_id"]]=$row["imunz_cvx_coe"];
		}	
	}
	
	//Set CVX code array
	public function set_cvx_arr(){
		$sql_cvx = imw_query("SELECT imunz_cpt_id, imnzn_name, imunz_cvx_coe, imnzn_type, imunz_mfr_code, imnzn_manufacturer, imnzn_id FROM immunization_admin order by imnzn_name");
		if(imw_num_rows($sql_cvx) > 0){
			while($arr_cvx = imw_fetch_array($sql_cvx)){
				if($arr_cvx["imunz_cvx_coe"] == ""){
					$arr_cvx["imunz_cvx_coe"] = "NA";
				} 
				$array_cvx_code[] = array($arr_cvx["imunz_cvx_coe"]." - ".$arr_cvx["imnzn_name"]." - ".$arr_cvx["imnzn_type"]." - ".$arr_cvx["imunz_mfr_code"]." - ".$arr_cvx["imnzn_manufacturer"]." - ".$arr_cvx["imnzn_id"], $arrEmpty, $arr_cvx["imunz_cvx_coe"]." - ".$arr_cvx["imnzn_name"]." - ".$arr_cvx["imnzn_type"]." - ".$arr_cvx["imunz_mfr_code"]." - ".$arr_cvx["imnzn_manufacturer"]." - ".$arr_cvx["imnzn_id"]);
			}
			$this->arrCVXCodes = $array_cvx_code;
		}
	}
	
	
	//Returns all required immunization data
	public function get_immunization_data($request){
		global $cls_common;
		$search_val = isset($request['searchby']) ? $request['searchby'] : "All";
		$pid = $this->patient_id;
		if($search_val == "Administered"){
			$qryTemp = "and scpStatus = 'Administered'";
		}
		elseif($search_val == "Declined"){
			$qryTemp = "and scpStatus = 'Decline'";
		}	
		elseif($search_val == "No Due"){
			$qryTemp = "and scpStatus = 'No Due'";
		}	
		elseif($search_val == "Completed"){
			$qryTemp = "and scpStatus = 'Completed'";
		}	
		elseif($search_val == "Cancelled"){
			$qryTemp = "and scpStatus = 'Cancelled'";
		}	
		$sql_qry = imw_query( "select *, date_format(administered_date,'".get_sql_date_format()."') as administered_date1,
		date_format(expiration_date,'".get_sql_date_format()."') as expiration_date1,
		date_format(consent_date,'".get_sql_date_format()."') as consent_date1,
		date_format(adverse_reaction_date,'".get_sql_date_format()."') as adverse_reaction_date1, 
		TIME_FORMAT(administered_time, '%h:%i %p') as administeredTime,
		TIME_FORMAT(adverse_reaction_time, '%h:%i %p') as adverseReactionTime,
		if (published_date='0000-00-00','',date_format(published_date,'".get_sql_date_format()."')) as published_date1, 
		if (presented_date='0000-00-00','',date_format(presented_date,'".get_sql_date_format()."')) as presented_date1  
		from immunizations  where patient_id='$this->patient_id' $qryTemp order by id");
		
		$is_disable = '';
		while($immunQryRes = imw_fetch_array($sql_qry)){
			$is_disable = 'disabled="disabled"';
			$dataArr = array();
			$dataArr['imnzn_main_id'] = $immunQryRes['id'];
			$dataArr['immunization_id'] = $immunQryRes['immunization_id'];
			$dataArr['immunization_cvx_code'] = $immunQryRes['immunization_cvx_code'];
			$dataArr['immzn_type'] = $immunQryRes['immzn_type'];	
			$dataArr['chk_child_immunization'] = $immunQryRes['chk_child_immunization'];
			if($immunQryRes['chk_child_immunization'] == 1){
				$dataArr['child_immunization'] = 'checked="checked"';
			}
			if($dataArr['chk_child_immunization'] == 0){
				$dataArr['chk_child_immunization'] = '';
			}
			$dataArr['immzn_dose'] = $immunQryRes['immzn_dose'];
			$dataArr['immzn_dose_unit'] = $immunQryRes['immzn_dose_unit'];

			if(array_key_exists(trim($immunQryRes['immzn_route_site']),$this->routeset_codes))
			{
				$dataArr['immzn_route_site_code'] = $immunQryRes['immzn_route_site'];
				$immunQryRes['immzn_route_site'] = 	$this->routeset_codes[$immunQryRes['immzn_route_site']];
			}
			
			$dataArr['immzn_route_site'] = $immunQryRes['immzn_route_site'];
			
			if(array_key_exists($immunQryRes['site'],$this->bodysite_codes))
			{
				$dataArr['site_code'] = $immunQryRes['site'];
				$immunQryRes['site'] = 	$this->bodysite_codes[$immunQryRes['site']];
			}	
			$dataArr['immzn_site'] = $immunQryRes['site'];
			$dataArr['lot_number'] = $immunQryRes['lot_number'];
			$dataArr['imnzn_id'] = $immunQryRes['imnzn_id'];
			$dataArr['immzn_dose_id'] = $immunQryRes['immzn_dose_id'];
			if(get_number($immunQryRes['expiration_date1']) != '00000000'){
				$dataArr['expiration_date'] = $immunQryRes['expiration_date1'];
			}
			$dataArr['manufacturer_code'] = $immunQryRes['manufacturer_code'];
			$dataArr['manufacturer'] = $immunQryRes['manufacturer'];
			if(get_number($immunQryRes['administered_date1']) != '00000000'){
				$dataArr['administered_date'] = $immunQryRes['administered_date1'];
				$dataArr['administered_time'] = $immunQryRes['administeredTime'];
			}
			
			$dataArr['administered_by_id'] = $immunQryRes['administered_by_id'];
			$dataArr['provider_data'] = $cls_common->drop_down_providers($dataArr['administered_by_id'],'','');
			
			$ordered_by_id=$_SESSION['authId'];
			$ordered_by_id=$immunQryRes['ordered_by'];
			$dataArr['ordered_by_id'] = $immunQryRes['ordered_by_id'];
			$dataArr['ordered_by'] = $cls_common->drop_down_providers($ordered_by_id,'','');


			if(get_number($immunQryRes['consent_date1']) != '00000000'){
				$dataArr['consent_date'] = $immunQryRes['consent_date1'];
			}
			$dataArr['adverse_reaction'] = $immunQryRes['adverse_reaction'];
			if(get_number($immunQryRes['adverse_reaction_date1']) != '00000000'){
				$dataArr['adverse_reaction_date'] = $immunQryRes['adverse_reaction_date1'];
				$dataArr['adverse_reaction_time'] = $immunQryRes['adverseReactionTime'];
			}
			
			$dataArr['note'] = $immunQryRes['note'];
			$dataArr['nip001'] = $immunQryRes['nip001'];
			$dataArr['nip002'] = $immunQryRes['nip002'];

			if(array_key_exists($immunQryRes['funding_program'],$this->vfc_codes))
			{
				$dataArr['funding_program_code'] = $immunQryRes['funding_program'];
				$immunQryRes['funding_program'] = 	$this->vfc_codes[$immunQryRes['funding_program']];
			}
				
			$dataArr['funding_program'] = $immunQryRes['funding_program'];
			
			if(get_number($immunQryRes['published_date1']) != '00000000')
			$dataArr['published_date1'] = $immunQryRes['published_date1'];
			
			if(get_number($immunQryRes['presented_date1']) != '00000000')
			$dataArr['presented_date1'] = $immunQryRes['presented_date1'];
			
			$dataArr['refusal_reason'] = $immunQryRes['refusal_reason'];
			$dataArr['disease_with_immunity'] = $immunQryRes['disease_with_immunity'];
			$dataArr['snomed'] = $immunQryRes['snomed'];
			$dataArr['scp_status'] = $immunQryRes['scpStatus'];
			
			$dataArr['entered_by']=$_SESSION['authProviderName'];
			if($immunQryRes['created_by']>0){
				$rs=imw_query("Select fname,mname,lname FROM users WHERE id='".$immunQryRes['created_by']."'");
				$res=imw_fetch_array($rs);
				$nameArr = array();
				$nameArr["LAST_NAME"] = $res['lname'];
				$nameArr["FIRST_NAME"] = $res['fname'];
				$nameArr["MIDDLE_NAME"] = $res['mname'];
				$dataArr['entered_by'] = changeNameFormat($nameArr);
			}

			$this->immunDataArr[] = $dataArr;
			
			//--- SET AUDIT VARIABLES FOR VIEW ---
			$pkIdAuditTrail = $dataArr['imnzn_main_id'].'-';
			if($pkIdAuditTrailID == ''){
				$pkIdAuditTrailID = $dataArr['imnzn_main_id'];
			}
			
			$pub_dataArr = array();
			$imm_type_qry=imw_query("select *,date_format(published_date,'".get_sql_date_format()."') as published_date1,date_format(presented_date,'".get_sql_date_format()."') as presented_date1
					 from immunizations_type_pt where imnzn_main_id='".$immunQryRes['id']."' order by id");
			$k=0;
			while($immunPubQryRes=imw_fetch_array($imm_type_qry)){
				$k++;
				$imnzn_main_id_chk = $immunPubQryRes['imnzn_main_id'];
				$pub_dataArr[$k]["id"] = $immunPubQryRes['id'];
				$pub_dataArr[$k]["immzn_type"] = $immunPubQryRes['immzn_type'];
				$pub_dataArr[$k]["immunization_cvx_code"] = $immunPubQryRes['immunization_cvx_code'];
				if(get_number($immunQryRes['published_date1']) != '00000000')
				$pub_dataArr[$k]["published_date"] = $immunPubQryRes['published_date1'];
				
				if(get_number($immunQryRes['presented_date1']) != '00000000')
				$pub_dataArr[$k]["presented_date"] = $immunPubQryRes['presented_date1'];
				$this->immunPubDataArr[$immunQryRes['id']] = $pub_dataArr;
			}
		}
		$return_arr['checkImmunizations'] 				= commonNoMedicalHistoryAddEdit($moduleName="Immunizations",$moduleValue="",$mod="get");
		$return_arr['pkIdAuditTrailID'] 				= $pkIdAuditTrailID;
		$return_arr['pkIdAuditTrail'] 				= $pkIdAuditTrail;
		$return_arr['entered_by'] 						= $_SESSION['authProviderName'];
		$return_arr['arr_info_alert'] 					= $this->arr_info_alert;
		$return_arr['ARR_INFO_ALERT_SERIALIZED'] 		= urlencode(serialize($this->arr_info_alert));
		$return_arr['policyStatus'] 					= $this->policy_status;
		$return_arr['searchByData'] 					= $this->filter_data_arr;
		$return_arr['is_disable'] 						= $is_disable;
		$return_arr['immunDataArr'] 					= $this->immunDataArr;
		$return_arr['immunPubDataArr'] 					= $this->immunPubDataArr;
		$return_arr['routest_code_str'] 				= $this->routeset_codes;
		$return_arr['bodysite_codes_str'] 				= $this->bodysite_codes;
		$return_arr['vfc_codes_str'] 					= $this->vfc_codes;
		$return_arr['nip1_txt_str'] 					= $this->nip1TxtArr;
		$return_arr['nip2_txt_str'] 					= $this->nip2TxtArr;
		$return_arr['manufacture_txt_str'] 				= $this->manufactureTxtArr;
		$return_arr['stringAllimnz'] 					= substr($this->stringAllimnz,0,-1);
		$return_arr['cvx_arr_js'] 						= $this->cvx_arr_js;
		$return_arr['arrCVXCodes'] 						= $this->arrCVXCodes;
		$return_arr['arrCVXCodes_json'] 				= json_encode($this->arrCVXCodes);
		
		//Immunization registry info
		$info_val = $this->get_imm_reg_info();
		foreach($info_val as $key => $val){
			$return_arr[$key] = $val;
		}
		return $return_arr;
	}
	
	//Immunization registry info
	public function get_imm_reg_info(){
		$regRs=imw_query("Select id, reg_status,publicity_code,protection_indicator,DATE_FORMAT(indicator_eff_date, '".get_sql_date_format()."') as 'indicator_eff_date',DATE_FORMAT(publicity_code_eff_date, '".get_sql_date_format()."') as 'publicity_code_eff_date',DATE_FORMAT(imm_reg_status_eff_date, '".get_sql_date_format()."') as 'imm_reg_status_eff_date' FROM immunization_reg_info WHERE patient_id ='".$this->patient_id."'");
		$regRes = imw_fetch_array($regRs);
		$publicityOptions='';
		foreach($this->publicity_code_array as $key => $val){
			$sel='';
			$sel=($key==$regRes['publicity_code'])? 'selected' : '';
			$publicityOptions.='<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
		}
		$return_arr['reg_arr'] = $regRes;	
		$return_arr['reg_id'] = $regRes['id'];
		$return_arr['reg_status'] = $regRes['reg_status'];
		$return_arr['protection_indicator'] = $regRes['protection_indicator'];
		$return_arr['indicator_eff_date'] = $regRes['indicator_eff_date'];
		$return_arr['publicity_code_eff_date'] = $regRes['publicity_code_eff_date'];
		$return_arr['imm_reg_status_eff_date'] = $regRes['imm_reg_status_eff_date'];
		$return_arr['publicityOptions'] = $publicityOptions;
		return $return_arr;	
	}
	
	//Deleting Immunization data
	public function delete_immunization($del_id){
		global $cls_review;
		$counter = 0;
		//--- GET IMMUNIZTIONS DATA FOR AUDIT TRAIL ----
		$sql = imw_query("select immunization_id as immName,patient_id as dbPatientId from immunizations where id = '$del_id'");
		while($row = imw_fetch_array($sql)){
			$imnQryRes[] = $row;
		}
		$immName = $imnQryRes[0]['immName'];
		$dbPatientId = $imnQryRes[0]['dbPatientId'];
		
		$reviewImmArr = array();
		$reviewImmArr[0]['Pk_Id'] = $del_id;
		$reviewImmArr[0]['Table_Name'] = 'immunizations';
		$reviewImmArr[0]['Field_Text'] = 'Patient Immunization';
		$reviewImmArr[0]['Operater_Id'] = $_SESSION['authId'];
		$reviewImmArr[0]['Action'] = 'delete';
		$reviewImmArr[0]['Old_Value'] = $immName;
		//CLSReviewMedHx::reviewMedHx($reviewImmArr,$_SESSION['authId'],"Immunizations",$dbPatientId,0,0);
		$cls_review->reviewMedHx($reviewImmArr,$_SESSION['authId'],"Immunizations",$dbPatientId,0,0);
		
		//--- DELETE FROM IMMUNIZATIONS TABLE ----
		$del_query = imw_query("delete from immunizations where id = '$del_id'");
		$counter = ($counter+imw_affected_rows());
		return $counter;
	}
	
	//Saving Imm. Registry info
	public function save_imm_reg_info($request){
		$request['indicator_eff_date'] 		= get_date_format($request['indicator_eff_date'],'mm-dd-yyyy','yyyy-mm-dd');
		$request['publicity_code_eff_date'] = get_date_format($request['publicity_code_eff_date'],'mm-dd-yyyy','yyyy-mm-dd');
		$request['imm_reg_status_eff_date'] = get_date_format($request['imm_reg_status_eff_date'],'mm-dd-yyyy','yyyy-mm-dd');
		$counter = 0;	
		$qryInit='Insert INTO';
		$qryWhere='';
		if($request['reg_id']>0 && $request['reg_id']!=''){
			$qryInit='Update';
			$qryWhere=" WHERE id='".$request['reg_id']."'";
		}

		if($qryWhere==''){
			$q1 = "SELECT * FROM immunization_reg_info Where patient_id='".$this->patient_id."'";
			$res1 = imw_query($q1);
			if($res1 && imw_num_rows($res1)==1){
				$qryInit='Update';
				$rs1 = imw_fetch_assoc($res1);
				$qryWhere=" WHERE id='".$rs1['id']."'";	
			}
		}
		if(trim($request['protection_indicator'])!='' && (empty($request['indicator_eff_date'])===true || $request['indicator_eff_date']=='0000-00-00')){
			$request['indicator_eff_date']=date('Y-m-d');	
		}

		$qry=$qryInit." immunization_reg_info SET 
		patient_id='".$this->patient_id."',
		reg_status='".addslashes($request['reg_status'])."',
		publicity_code='".addslashes($request['publicity_code'])."',
		protection_indicator='".addslashes($request['protection_indicator'])."',
		indicator_eff_date='".$request['indicator_eff_date']."',
		publicity_code_eff_date='".$request['publicity_code_eff_date']."',
		imm_reg_status_eff_date='".$request['imm_reg_status_eff_date']."'
		 ".$qryWhere;
		$rs=imw_query($qry);
		$counter = ($counter+imw_affected_rows());
		return $counter;	
	}
	
	
	//Saving Main immunization data
	public function save_immunizations($request){
		global $cls_review;
		//Checking if a change is made
		$main_imm_id_str = $request["hidImmIdVizChange"];	
		$main_imm_id_str = substr(trim($main_imm_id_str), 0, -1);  		
		$arrImmIdVizChange = array();
		$arrImmIdVizChange = explode(",", $main_imm_id_str);
		
		$immDataArr = array();
		$sql_qry = imw_query("select * from immunizations where id in ($main_imm_id_str)");
		if(imw_num_rows($sql_qry) > 0){
			while($immQryRes = imw_fetch_array($sql_qry)){
				$id = $immQryRes['id'];
				$administered_date = $immQryRes['administered_date'];
				if($administered_date == '0000-00-00'){
					$administered_date = '';
				}
				$immQryRes['administered_date'] = $administered_date;
				
				$administered_time = substr($immQryRes['administered_time'],0,-3);
				$immQryRes['administered_time'] = $administered_time;
				
				$expiration_date = $immQryRes['expiration_date'];
				if($expiration_date == '0000-00-00'){
					$expiration_date = '';
				}
				$immQryRes['expiration_date'] = $expiration_date;
				
				$consent_date = $immQryRes['consent_date'];
				if($consent_date == '0000-00-00'){
					$consent_date = '';
				}
				$immQryRes['consent_date'] = $consent_date;
				
				$adverse_reaction_date = substr($immQryRes['adverse_reaction_date'],0,-9);
				if($adverse_reaction_date == '0000-00-00'){
					$adverse_reaction_date = '';
				}
				$immQryRes['adverse_reaction_date'] = $adverse_reaction_date;

				$adverse_reaction_time = substr($immQryRes['adverse_reaction_time'],0,-3);
				if($adverse_reaction_time == '00:00'){
					$adverse_reaction_time = '';
				}
				$immQryRes['adverse_reaction_time'] = $adverse_reaction_time;
				
				$immDataArr[$id] = $immQryRes;
			}
		}
		
		
		//Saving Immunization data
		for($i=1;$i<=$request['last_cnt'];$i++){
			$dataArr = array();
			$imnzn_main_id = $request['imnzn_main'.$i];
			$dataArr['patient_id'] = $this->patient_id;
			//$dataArr['scpStatus'] = 'Administered';
			$dataArr['scpStatus'] = $request['scp_status'.$i];
			$dataArr['immunization_id'] = $request['immunization_name'.$i];
			$dataArr['immunization_cvx_code'] = $request['immunization_cvx_code'.$i];
			$dataArr['immzn_type'] = $request['immunization_type'.$i];
			$dataArr['chk_child_immunization'] = $request['immunization_child'.$i];
			if($dataArr['chk_child_immunization'] == ''){
				$dataArr['chk_child_immunization'] = 0;
			}
			$dataArr['immzn_dose'] = $request['immunization_dose'.$i];
			$dataArr['immzn_dose_unit'] = $request['immunization_dose_unit'.$i];
			$dataArr['immzn_route_site'] = $request['im_route'.$i]=="" ? $request['immunization_Route_and_site'.$i] : $request['im_route'.$i];	
			$dataArr['site'] = $request['im_site'.$i] == "" ? $request['immunization_site'.$i] : $request['im_site'.$i];
			$dataArr['lot_number'] = $request['immunization_Lot'.$i];
			$dataArr['imnzn_id'] = $imnzn_id[$i];
			$dataArr['immzn_dose_id'] = $immzn_dose_id[$i];	
			$dataArr['expiration_date'] = getDateFormatDB($request['immunization_Expiration_Date'.$i]);
			$dataArr['manufacturer_code'] = $request['immunization_Mfr_Code'.$i];
			$dataArr['manufacturer'] = $request['immunization_Manufacturer'.$i];
			$dataArr['administered_date'] = getDateFormatDB($request['immunization_Admin_date'.$i]);
			list($h,$m,$s) = preg_split('/(:)|( )/',$request['immunization_Admin_time'.$i]);
			if($s == 'PM'){
				$h += 12;
			}
			$adminTime = $h.':'.$m;
			if($adminTime != "00:00"){
			$dataArr['administered_time'] = trim($adminTime,":");
			}
			$dataArr['administered_by_id'] = $request['administered_by_id'.$i];
			$dataArr['consent_date'] = getDateFormatDB($request['immunization_consent_date'.$i]);
			$dataArr['adverse_reaction'] = $request['immunization_reaction'.$i];
			$dataArr['adverse_reaction_date'] = getDateFormatDB($request['immunization_reaction_date'.$i]);
			list($h,$m,$s) = preg_split('/(:)|( )/',$request['immunization_reaction_time'.$i]);
			if($s == 'PM'){
				$h += 12;
			}
			$reactionTime = $h.':'.$m;
			$dataArr['adverse_reaction_time'] = trim($reactionTime,":");
			$dataArr['note'] = $request['immunization_comments'.$i];
			$dataArr['nip001'] = $request['im_comments_code'.$i];
			
			$dataArr['funding_program'] = trim($request['im_funding_program'.$i]) == "" ? trim($request['immunization_funding_program'.$i]) : trim($request['im_funding_program'.$i]);
			$dataArr['published_date'] = getDateFormatDB($request['immunization_published_date'.$i]);
			$dataArr['presented_date'] = getDateFormatDB($request['immunization_presented_date'.$i]);
			$dataArr['ordered_by'] = $request['immunization_ordered_by'.$i];
			$dataArr['refusal_reason'] = $request['immunization_refusal_reason'.$i];
			$dataArr['nip002'] = $request['im_refusal_reason_code'.$i];
			$dataArr['disease_with_immunity'] = $request['disease_with_immunity'.$i];
			$dataArr['snomed'] = $request['immunization_snomed'.$i];
			if($dataArr['immunization_id'] != ''){
				if($imnzn_main_id > 0){
					$imm_action = 'update';
					$dataArr['update_date'] = date('Y-m-d');
					$dataArr['updated_by'] = $_SESSION['authId'];
					//if(in_array($imnzn_main_id, $arrImmIdVizChange) == true){
						UpdateRecords($imnzn_main_id,'id',$dataArr,'immunizations');
					//}
				}
				else{
					$imm_action = 'add';
					$dataArr['create_date'] = date('Y-m-d');
					$dataArr['created_by'] = $_SESSION['authId'];
					
					$imnzn_main_id = AddRecords($dataArr,'immunizations');
				}
				
				$last_cnt_pub = $request['last_cnt_pub_'.$i];
				
				for($k=1;$k<=$last_cnt_pub;$k++){
					$pubdataArr = array();
					$reviewed_imm_arr=array();
					$reviewed_arr = array();
					$action='';
					$imnzn_pub_id = $request['imnzn_pub_id'.$i.'_'.$k];
					$oldAuditRes=array();
					
					if($imnzn_pub_id>0){
						$oldAuditRs=imw_query("Select * FROM immunizations_type_pt WHERE id='".$imnzn_pub_id."'");
						$oldAuditRes=imw_fetch_array($oldAuditRs);
					}
					
					if($request['imnzn_pub_id'.$i.'_'.$k] > 0){
						$action='update';
						$pubdataArr['imnzn_main_id'] = $imnzn_main_id;
						$pubdataArr['immzn_type'] = $request['immunization_type'.$i.'_'.$k];
						$pubdataArr['immunization_cvx_code'] = $request['immunization_cvx_code'.$i.'_'.$k];
						$pubdataArr['published_date'] = getDateFormatDB($request['immunization_published_date'.$i.'_'.$k]);
						$pubdataArr['presented_date'] = getDateFormatDB($request['immunization_presented_date'.$i.'_'.$k]);
						UpdateRecords($imnzn_pub_id,'id',$pubdataArr,'immunizations_type_pt');
					}else{
						if($request['immunization_type'.$i.'_'.$k]!=""){
							$action='add';
							$pubdataArr['imnzn_main_id'] = $imnzn_main_id;
							$pubdataArr['immzn_type'] = $request['immunization_type'.$i.'_'.$k];
							$pubdataArr['immunization_cvx_code'] = $request['immunization_cvx_code'.$i.'_'.$k];
							$pubdataArr['published_date'] = getDateFormatDB($request['immunization_published_date'.$i.'_'.$k]);
							$pubdataArr['presented_date'] = getDateFormatDB($request['immunization_presented_date'.$i.'_'.$k]);
							$imnzn_pub_id = AddRecords($pubdataArr,'immunizations_type_pt');
						}
					}
					
					// SAVE AUDIT RECORDS
					$immType=$request['immunization_type'.$i.'_'.$k];
					$immuDataFields = make_field_type_array("immunizations_type_pt");
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_pub_id;
					$reviewed_arr['Table_Name'] = 'immunizations_type_pt';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_type'.$i.'_'.$k;
					$reviewed_arr['Data_Base_Field_Name']= "immzn_type";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"immzn_type");
					$reviewed_arr['Field_Text'] = 'Patient Vaccine Type';
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $action;
					$reviewed_arr['Old_Value'] = $oldAuditRes['immzn_type'];
					$reviewed_arr['New_Value'] = $request['immunization_type'.$i.'_'.$k];
					$reviewed_imm_arr[] = $reviewed_arr;

					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_pub_id;
					$reviewed_arr['Table_Name'] = 'immunizations_type_pt';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_cvx_code'.$i.'_'.$k;
					$reviewed_arr['Data_Base_Field_Name']= "immunization_cvx_code";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"immunization_cvx_code");
					$reviewed_arr['Field_Text'] = 'Immunization CVX Code - '.$immType;
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $action;
					$reviewed_arr['Old_Value'] = $oldAuditRes['immunization_cvx_code'];
					$reviewed_arr['New_Value'] = $request['immunization_cvx_code'.$i.'_'.$k];
					$reviewed_imm_arr[] = $reviewed_arr;

					$published_date= (get_number($oldAuditRes['published_date'])=='00000000')? '' : $oldAuditRes['published_date'];
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_pub_id;
					$reviewed_arr['Table_Name'] = 'immunizations_type_pt';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_published_date'.$i.'_'.$k;
					$reviewed_arr['Data_Base_Field_Name']= "published_date";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"published_date");
					$reviewed_arr['Field_Text'] = 'Immunization Published Date - '.$immType;
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $action;
					$reviewed_arr['Old_Value'] = $published_date;
					$reviewed_arr['New_Value'] = getDateFormatDB($request['immunization_published_date'.$i.'_'.$k]);
					$reviewed_imm_arr[] = $reviewed_arr;

					$presented_date= (get_number($oldAuditRes['presented_date'])=='00000000')? '' : $oldAuditRes['presented_date'];
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_pub_id;
					$reviewed_arr['Table_Name'] = 'immunizations_type_pt';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_presented_date'.$i.'_'.$k;
					$reviewed_arr['Data_Base_Field_Name']= "presented_date";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"presented_date");
					$reviewed_arr['Field_Text'] = 'Immunization Presented Date - '.$immType;
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $action;
					$reviewed_arr['Old_Value'] = $presented_date;
					$reviewed_arr['New_Value'] = getDateFormatDB($request['immunization_presented_date'.$i.'_'.$k]);
					$reviewed_imm_arr[] = $reviewed_arr;
					//CLSReviewMedHx::reviewMedHx($reviewed_imm_arr,$_SESSION['authId'],"Immunizations",$this->patient_id,0,0);
					$cls_review->reviewMedHx($reviewed_imm_arr,$_SESSION['authId'],"Immunizations",$this->patient_id,0,0);
				}
				
				$oldDataArr = $immDataArr[$imnzn_main_id];
				$blDoRewiev = false;
				if($imm_action == 'update'){
					if(in_array($imnzn_main_id, $arrImmIdVizChange) == true){			
						$blDoRewiev = true;
					}
				}
				elseif($imm_action == 'add'){
					$blDoRewiev = true;
				}
				if($blDoRewiev == true){
					//---- REVIEWED CODE ---
					$reviewed_imm_arr = array();
					$immuDataFields = make_field_type_array("immunizations");
					
					//--- REVIEWED IMMUNIZATION NAME ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_name'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "immunization_id";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"immunization_id");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Administered Text - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['immunization_id'];
					$reviewed_arr['New_Value'] = $dataArr['immunization_id'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION CVX CODE ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_cvx_code'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "immunization_cvx_code";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"immunization_cvx_code");
					$reviewed_arr['Field_Text'] = 'Patient Immunization CVX Code - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['immunization_cvx_code'];
					$reviewed_arr['New_Value'] = $dataArr['immunization_cvx_code'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION TYPE ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_type'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "immzn_type";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"immzn_type");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Type - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['immzn_type'];
					$reviewed_arr['New_Value'] = $dataArr['immzn_type'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION CHILD ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_child'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "chk_child_immunization";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"chk_child_immunization");
					$reviewed_arr['Field_Text'] = 'Patient Child Immunization - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['chk_child_immunization'];
					$reviewed_arr['New_Value'] = $dataArr['chk_child_immunization'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION DOSE ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_dose'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "immzn_dose";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"immzn_dose");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Amount - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['immzn_dose'];
					$reviewed_arr['New_Value'] = $dataArr['immzn_dose'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION UNIT ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_dose_unit'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "immzn_dose_unit";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"immzn_dose_unit");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Unit - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['immzn_dose_unit'];
					$reviewed_arr['New_Value'] = $dataArr['immzn_dose_unit'];
					$reviewed_imm_arr[] = $reviewed_arr;

					//--- REVIEWED IMMUNIZATION ROUTE AND SITE ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_Route_and_site'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "immzn_route_site";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"immzn_route_site");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Route - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['immzn_route_site'];
					$reviewed_arr['New_Value'] = $dataArr['immzn_route_site'];
					$reviewed_imm_arr[] = $reviewed_arr;

					//--- REVIEWED IMMUNIZATION SITE ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_site'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "site";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"site");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Site - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['site'];
					$reviewed_arr['New_Value'] = $dataArr['site'];
					$reviewed_imm_arr[] = $reviewed_arr;

					//--- REVIEWED IMMUNIZATION manufacturer code ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_Mfr_Code'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "manufacturer_code";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"manufacturer_code");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Manufacturer Code - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['manufacturer_code'];
					$reviewed_arr['New_Value'] = $dataArr['manufacturer_code'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION LOT ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_Lot'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "lot_number";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"lot_number");
					$reviewed_arr['Field_Text'] = 'Patient Immunization lot# - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['lot_number'];
					$reviewed_arr['New_Value'] = $dataArr['lot_number'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION EXPIRATION DATE ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_Expiration_Date'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "expiration_date";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"expiration_date");
					$reviewed_arr['Field_Text'] = 'Patient expiration date - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['expiration_date'];
					$reviewed_arr['New_Value'] = $dataArr['expiration_date'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION MANUFACTURER ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_Manufacturer'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "manufacturer";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"manufacturer");
					$reviewed_arr['Field_Text'] = 'Patient Immunization manufacturer - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['manufacturer'];
					$reviewed_arr['New_Value'] = $dataArr['manufacturer'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION ADMINISTRATED DATE ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_Admin_date'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "administered_date";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"administered_date");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Administrated Date - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['administered_date'];
					$reviewed_arr['New_Value'] = $dataArr['administered_date'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION ADMINISTRATED TIME ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_Admin_time'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "administered_time";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"administered_time");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Administrated time - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					if($oldDataArr['administered_time'] != "00:00")
					$reviewed_arr['Old_Value'] = $oldDataArr['administered_time'];
					else
					$reviewed_arr['Old_Value'] = "";
					$reviewed_arr['New_Value'] = $dataArr['administered_time'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION ADMINISTRATED ID ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'administered_by_id'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "administered_by_id";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"administered_by_id");
					$reviewed_arr['Field_Text'] = 'Patient Immunization administered by id  - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Depend_Select'] = "select CONCAT_WS(',',lname,fname) as provider";
					$reviewed_arr['Depend_Table'] = 'users';
					$reviewed_arr['Depend_Search'] = 'id';
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['administered_by_id'];
					$reviewed_arr['New_Value'] = $dataArr['administered_by_id'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION CONSENT DATA ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_consent_date'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "consent_date";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"consent_date");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Consent Date - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['consent_date'];
					$reviewed_arr['New_Value'] = $dataArr['consent_date'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION REACTION ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_reaction'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "adverse_reaction";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"adverse_reaction");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Reaction - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['adverse_reaction'];
					$reviewed_arr['New_Value'] = $dataArr['adverse_reaction'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION REACTION DATE ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_reaction_date'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "adverse_reaction_date";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"adverse_reaction_date");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Reaction Date - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['adverse_reaction_date'];
					$reviewed_arr['New_Value'] = $dataArr['adverse_reaction_date'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION REACTION TIME ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_reaction_time'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "adverse_reaction_time";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"adverse_reaction_time");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Reaction Time - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['adverse_reaction_time'];
					$reviewed_arr['New_Value'] = $dataArr['adverse_reaction_time'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION COMMENTS ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_comments'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "note";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"note");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Administration Notes - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['note'];
					$reviewed_arr['New_Value'] = $dataArr['note'];
					$reviewed_imm_arr[] = $reviewed_arr;

					//--- REVIEWED IMMUNIZATION Refusal Reason ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_refusal_reason'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "refusal_reason";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"refusal_reason");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Refusal Reason - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['refusal_reason'];
					$reviewed_arr['New_Value'] = $dataArr['refusal_reason'];
					$reviewed_imm_arr[] = $reviewed_arr;

					//--- REVIEWED IMMUNIZATION Refusal Reason ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_funding_program'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "funding_program";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"funding_program");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Funding Program - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['funding_program'];
					$reviewed_arr['New_Value'] = $dataArr['funding_program'];
					$reviewed_imm_arr[] = $reviewed_arr;

					//--- REVIEWED IMMUNIZATION Ordered By ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_ordered_by'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "ordered_by";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"ordered_by");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Ordered By - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Depend_Select'] = "select CONCAT_WS(', ',lname,fname) as orderedBy";
					$reviewed_arr['Depend_Table'] = "users";
					$reviewed_arr['Depend_Search'] = "id";
					$reviewed_arr['Old_Value'] = $oldDataArr['ordered_by'];
					$reviewed_arr['New_Value'] = $dataArr['ordered_by'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION Disease With Immunity ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'disease_with_immunity'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "disease_with_immunity";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"disease_with_immunity");
					$reviewed_arr['Field_Text'] = 'Patient Immunization Disease With Presumed Immunity - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['disease_with_immunity'];
					$reviewed_arr['New_Value'] = $dataArr['disease_with_immunity'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//--- REVIEWED IMMUNIZATION SNOMED CT ---
					$reviewed_arr = array();
					$reviewed_arr['Pk_Id'] = $imnzn_main_id;
					$reviewed_arr['Table_Name'] = 'immunizations';
					$reviewed_arr['UI_Filed_Name'] = 'immunization_snomed'.$i;
					$reviewed_arr['Data_Base_Field_Name']= "snomed";
					$reviewed_arr['Data_Base_Field_Type']= fun_get_field_type($immuDataFields,"snomed");
					$reviewed_arr['Field_Text'] = 'Patient Immunization SNOMED CT - '.$request['immunization_name'.$i];
					$reviewed_arr['Operater_Id'] = $_SESSION['authId'];
					$reviewed_arr['Action'] = $imm_action;
					$reviewed_arr['Old_Value'] = $oldDataArr['snomed'];
					$reviewed_arr['New_Value'] = $dataArr['snomed'];
					$reviewed_imm_arr[] = $reviewed_arr;
					
					//CLSReviewMedHx::reviewMedHx($reviewed_imm_arr,$_SESSION['authId'],"Immunizations",$this->patient_id,0,0);
					$cls_review->reviewMedHx($reviewed_imm_arr,$_SESSION['authId'],"Immunizations",$this->patient_id,0,0);
				}
			}
		}
		
		$getRES = commonNoMedicalHistoryAddEdit("Immunizations",$request["commonNoImmunizations"],"save");
		
		//Add Update Pateint Recall Entry For Immunization//
		$sql_getImmnzn="SELECT * FROM immunizations, immunization_admin, immunization_dosedetails
		WHERE immunizations.imnzn_id = immunization_admin.imnzn_id
		AND immunizations.immzn_dose_id = immunization_dosedetails.dose_id
		AND immunizations.patient_id ='".$this->patient_id."' order by immunizations.imnzn_id";
		$rez_Immnzn=imw_query($sql_getImmnzn) or die(imw_error());				
		$num_rows=0;
		if($rez_Immnzn){
			 $num_rows=imw_num_rows($rez_Immnzn);
				if($num_rows>0){
				while($resultRow=imw_fetch_array($rez_Immnzn)){
				$numofdoses=(int)$resultRow["imnzn_numberofdoses"];
				$dose_number=(int)$resultRow["dose_number"];
				$immnzn_id=$resultRow["imnzn_id"];
				$dateFirstDoseGiven=$resultRow["administered_date"];
					if($numofdoses>1 && $dose_number==1 && $dateFirstDoseGiven!="0000-00-00" ){
						$getReturnSTRING = $this->getLatestDoseGiven($immnzn_id,$numofdoses,$dateFirstDoseGiven);
						if($getReturnSTRING!=""){				
							$explodeArray=@explode("---",$getReturnSTRING);
							$getReturnTime=$explodeArray[0];
							$dose_idValue=$explodeArray[1];
							$immnznDoseValue=$explodeArray[1];
							$immnznDoseDueNumber=$explodeArray[3];
							$immnzntypeValue=$resultRow["imnzn_type"];
							$immnznManufacturerValue=$resultRow["imnzn_manufacturer"];
							$immnznNameValue=$resultRow["imnzn_name"];
							
							if($getReturnTime!=""){
								$timeCurrent=mktime(0,0,0,date("m"),date("d"),date("y"));
								if($getReturnTime){
								$doseduedatevalue=date("Y-m-d",$getReturnTime);
								$selCheck=@imw_query("select id from patient_app_recall where patient_id='".$this->patient_id."' and descriptions='".$immnznNameValue."-".$immnzntypeValue."-".$immnznDoseDueNumber."' and recalldate='".$doseduedatevalue."' and procedure_name='Immunization' AND descriptions != 'MUR_PATCH'");
								$resNumRows=@imw_num_rows($selCheck);
									if($resNumRows>0){
										//do not insert twice
									}else{
										$insert="INSERT INTO patient_app_recall SET
										patient_id='".$this->patient_id."',
										procedure_id='-1',
										recall_code='',
										operator='".$_SESSION['authId']."',
										descriptions='".$immnznNameValue."-".$immnzntypeValue."-".$immnznDoseDueNumber."',
										recalldate='".$doseduedatevalue."',
										procedure_name='".$immnznNameValue."- Dosage ".$immnznDoseDueNumber."',
										current_date1 =NOW()";
										$insert=@imw_query($insert);
									}
								}
							}
						}
					}
				}
			}
		}
		
		//making review in database - end
		//redirecting...
		$curr_tab = xss_rem($request["curr_tab"]);
		$next_tab = xss_rem($request["next_tab"]);
		$next_dir = xss_rem($request["next_dir"]);
		if($next_tab != ""){
			$curr_tab = $next_tab;
		}
		// Remove Remote Server Sync Code
		?>
		<script type="text/javascript">
			var curr_tab = '<?php echo xss_rem($curr_tab); ?>';	
			top.show_loading_image("show", 100);
			if(top.document.getElementById('medical_tab_change')) {
				if(top.document.getElementById('medical_tab_change').value!='yes') {
					top.alert_notification_show('<?php echo $this->arr_info_alert["save"];?>');
				}
				if(top.document.getElementById('medical_tab_change').value=='yes') {
					top.chkConfirmSave('yes','set');		
				}
				top.document.getElementById('medical_tab_change').value='';
			}
			top.fmain.location.href = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/index.php?showpage='+curr_tab;	
			top.show_loading_image("hide");
		</script>
		<?php
	}
	
	
	//Gets latest dose given
	public function getLatestDoseGiven($immnzn_id,$numofdoses,$dateFirstDoseGiven){
		$DueDateForDose="";
		$sql_getImmnzn="SELECT immunizations.*,immunization_dosedetails.dose_number FROM immunizations, immunization_dosedetails	
		WHERE immunizations.imnzn_id ='".$immnzn_id."' and
		immunizations.immzn_dose_id = immunization_dosedetails.dose_id and immunizations.patient_id ='".$this->patient_id."'
		order by immunizations.administered_date DESC limit 0,1";
		$rez_Immnzn=imw_query($sql_getImmnzn) or die(imw_error());				
		$num_rows=0;
		if($rez_Immnzn){
			$num_rows=imw_num_rows($rez_Immnzn);
			if($num_rows>0){
				$resultRow=imw_fetch_array($rez_Immnzn);
				$LastgivenDoseNumber=(int)$resultRow["dose_number"];
				if($LastgivenDoseNumber<$numofdoses){
					$NextDueDoseNumber=$LastgivenDoseNumber+1;
					$selquery="select * from immunization_dosedetails where imnzn_id='".$immnzn_id."' and dose_number='".$NextDueDoseNumber."'";
					$result=imw_query($selquery);
					$returnRow=0;
					if($result){
						$numRows=imw_num_rows($result);
						if($numRows>0){
							$resultArray=imw_fetch_array($result);
							if(@is_array($resultArray)){
								$dose_quantity=$resultArray["dose_quantity"];
								$dose_gap=(int)$resultArray["dose_gap"];
								$dose_booster=$resultArray["dose_booster"];
								$dose_id=$resultArray["dose_id"];
								$dose_gapoption=$resultArray["dose_gapoption"];

								$dose_dose_number=$resultArray["dose_number"];
								//Check Date For Alerts//
									$dateFirstDoseGivenArray=explode("-",$dateFirstDoseGiven);
									if($dose_gapoption=="Days" && $dose_gap>0 ){
										$day=$dateFirstDoseGivenArray[2];
										$month=$dateFirstDoseGivenArray[1];
										$year=$dateFirstDoseGivenArray[0];
										$DueDateForDose=mktime(0,0,0,$month,$day,$year) + ($dose_gap * 86400);
									 }
									 if($dose_gapoption=="Weeks" && $dose_gap>0 ){
										$day=$dateFirstDoseGivenArray[2];
										$month=$dateFirstDoseGivenArray[1];
										$year=$dateFirstDoseGivenArray[0];
										$DueDateForDose=mktime(0,0,0,$month,$day,$year) + ($dose_gap * 7 * 86400);
									 }
									  if($dose_gapoption=="Month" && $dose_gap>0 ){
										$day=$dateFirstDoseGivenArray[2];
										$month=$dateFirstDoseGivenArray[1];
										$year=$dateFirstDoseGivenArray[0];
										$queryOption=$year.$month.",".$dose_gap;
										$selectPeriod=@imw_query("SELECT PERIOD_ADD(".$queryOption.") as YearMonth");
										if($selectPeriod){
											$resRow=imw_fetch_row($selectPeriod);
											$YearMonth=$resRow[0];//200909
											$temp_year=substr($YearMonth,0,strlen($YearMonth)-2);
											$temp_month=substr($YearMonth,-2);
											$DueDateForDose=mktime(0,0,0,$temp_month,$day,$temp_year);
										}
									 }
									  if($dose_gapoption=="Year" && $dose_gap>0 ){
										$day=$dateFirstDoseGivenArray[2];
										$month=$dateFirstDoseGivenArray[1];
										$year=$dateFirstDoseGivenArray[0]+$dose_gap;
										$DueDateForDose=mktime(0,0,0,$month,$day,$year);
									 }
									
								//End Check Date For Alerts//
								$DueDateForDose=$DueDateForDose."---".$dose_id."---".$dose_quantity."---".$dose_dose_number;
							}
						}
					}
				}
			}
		}
		return $DueDateForDose;
	}
	
	
	//Set CLS Alerts
	public function set_cls_alerts(){
		global $cls_alerts;
		$return_str= '';
		if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){	
			$alertToDisplayAt = "admin_specific_chart_note_med_hx";
			$return_str .= $cls_alerts->getAdminAlert($this->patient_id,$alertToDisplayAt,$form_id,"350px","250px","",$nxtStatus);
			$alertToDisplayAt = "patient_specific_chart_note_med_hx";
			$return_str .= $cls_alerts->getPatSpecificAlert($this->patient_id,$alertToDisplayAt,"350px","250px",$nxtStatus);	
		}
		if($_SESSION['alertImmShowForThisSession'] == ""){	
			$return_str .= $cls_alerts->ImmunizationAlerts($this->patient_id,'0',"MedHx",$nxtStatus);
		}
		
		if(isset($request['nxtFreqSave']) && $request['nxtFreqSave'] == "yes"){
			list($nxtHTML,$nxtStatus) = $OBJPatSpecificAlert->getNextImm($this->patient_id);
			if($nxtStatus == "yes"){
				$return_str .= $nxtHTML;
			}
		}
		
		$return_str .= $cls_alerts->autoSetDivLeftMargin("200","265");
		$return_str .= $cls_alerts->autoSetDivTopMargin("350","10","180");
		$return_str .= $cls_alerts->writeJS();
		return $return_str;	
	}	
}
?>