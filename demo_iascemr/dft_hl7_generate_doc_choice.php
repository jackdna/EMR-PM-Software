<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include("common/conDb.php"); 
/*------getting all procedure (surgerycenter)------*/
$arr_procs_mapping	= array();
$procs_query = "SELECT procedureId, name, code FROM procedures";
$procs_res	 = imw_query($procs_query);
if($procs_res && imw_num_rows($procs_res)>0){
	while($procs_rs = imw_fetch_assoc($procs_res)){
		$procs_rs_id							 = $procs_rs['procedureId'];
		$arr_procs_mapping[$procs_rs_id]['name'] = $procs_rs['name'];
		$arr_procs_mapping[$procs_rs_id]['cpt']  = $procs_rs['code'];
	}
}

/*---getting patient location (surgerycenter location)---*/
$location_result = imw_query("SELECT npi,name FROM surgerycenter LIMIT 0,1");
$location_rs	 = imw_fetch_assoc($location_result);
$patient_location= '^^^^'.preg_replace("/[^0-9]/","",$location_rs['npi']).'^^^'.$location_rs['name'];

/*---getting diagnosis codes------*/
$arr_diags_mapping = array();
$diags_query = "SELECT diag_id,diag_code FROM diagnosis_tbl";
$diags_result= imw_query($diags_query);
if($diags_result && imw_num_rows($diags_result)>0){
	while($diags_rs = imw_fetch_assoc($diags_result)){
		$arr_diags_mapping[$diags_rs['diag_id']] = explode(', ',$diags_rs['diag_code']);
	}
}


$HL7MSG_TEXT = '';
$HL7MSG_TEXT_ADT = '';
$HL7MSG_ERROR = array();
//MSH SEGMENT (MESSAGE HEADER)
$newMsgId		= newMessageUniqueId();

if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
	$HL7MSG_TEXT 	.= 'MSH|^~\&|IMW|IMW_KEYWHITMAN|MEDEVOLVE ECENO|MEDEVOLVE ECENO KEYWHITMAN|'.date('Ymdhis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII||||
';
}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades'))){
	$HL7MSG_TEXT 	.= 'MSH|^~\&|IMW|IMW_'.strtoupper($GLOBALS["LOCAL_SERVER"]).'|TEST|TEST|'.date('Ymdhis').'||DFT^P03|'.$newMsgId.'|P|2.3.1||||||ASCII|||
';
	$HL7MSG_TEXT 	.= 'EVN|P03|'.date('Ymdhis').'|||'.$_SESSION['loginUserName'].'||
';
	$HL7MSG_TEXT_ADT 	.= 'EVN|A08|'.date('Ymdhis').'|||'.$_SESSION['loginUserName'].'||
';
}
//GETTING PATIENT DEMOGRAPHICS FROM iASC
$SC_pConfId = $pConfId;
$SC_patient_q = "SELECT sc_pdt.imwPatientId AS patient_id FROM patient_data_tbl sc_pdt 
				 JOIN patientconfirmation sc_pc ON (sc_pc.patientId = sc_pdt.patient_id) WHERE sc_pc.patientConfirmationId = '$SC_pConfId'";
$SC_patient_res = imw_query($SC_patient_q);
if($SC_patient_res && imw_num_rows($SC_patient_res)==1){
	$SC_patient_rs = imw_fetch_assoc($SC_patient_res);
	$iASC_patient_id = $SC_patient_rs['patient_id'];
	$pid_sql = "";
	if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
		$pid_sql .= "select '1' AS set_id, id AS iMWID, 
				CONCAT(External_MRN_2,'^^^^PT~',External_MRN_1,'^^^^PI') AS pt_identifier_list, 
				External_MRN_1 AS Alternate_id, ";
	}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades'))){
		$pid_sql .= "select '0001' AS set_id, 
				'' AS iMWID, 
				id AS pt_identifier_list, 
				'' AS Alternate_id, ";
	}
	$pid_sql .=  " CONCAT(lname,'^',fname,'^',SUBSTR(mname,1,1)) AS ptName, 
				'' AS Mother_Maiden_Name, 
				DATE_FORMAT(DOB,'%Y%m%d') AS DOB, 
				UPPER(SUBSTR(sex,1,1)) AS sex, 
				'' AS Alias, 
				race ";
	$pid_where = "";
	if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
		$pid_sql .= ", '' AS eleventh, 
				'' AS twelve, 
				'' AS thirteen, 
				'' AS forteen, 
				'' AS fifteen, 
				'' AS sixteen,
				'' AS seventeen, 
				External_MRN_2 AS AccountNumber ";
		$pid_where = " AND External_MRN_2 != '' AND External_MRN_1 != ''";
	}else{
		$pid_sql .= " 
			, CONCAT(street,'^',street2,'^',city,'^',state,'^',postal_code,zip_ext) AS Address, 
			'' AS country_code, 
			REPLACE(phone_home,'-','') AS phone_home, 
			REPLACE(phone_biz,'-','') AS phone_biz, 
			language, 
			UPPER(TRIM(status)) AS status, 
			'' AS Religion, 
			'' AS Account_Number, 
			REPLACE(ss,'-','') AS ss, 
			driving_licence, 
			'' AS Mother_Identifier, 
			ethnicity ";
		}
	$pid_sql .= "FROM patient_data 
				WHERE id='".$iASC_patient_id."' 
				AND (DOB != '0000-00-00' AND DOB != '' AND sex != ''".$pid_where.") 
				LIMIT 0,1";
	include('connect_imwemr.php');
	$pid_res = imw_query($pid_sql);	
	if($pid_res && imw_num_rows($pid_res)==1){
		$pid_rs = imw_fetch_assoc($pid_res);
		//PID SEGMENT (PATIENT IDENTIFICATION)
		$HL7MSG_TEXT 	.= 'PID|'.$pid_rs['set_id'].'|'.$pid_rs['iMWID'].'|'.$pid_rs['pt_identifier_list'].'|'.$pid_rs['Alternate_id'].'|'.$pid_rs['ptName'].'||'.$pid_rs['DOB'].'|'.$pid_rs['sex'].'||||||||||'.$pid_rs['AccountNumber'].'|
';
		$HL7MSG_TEXT_ADT .= 'PID|'.implode('|',$pid_rs).'
';

		$GT1_q = "SELECT 
				'0001' AS set_id, 
				'' AS Guarantor_Number, 
				CONCAT(lname,'^',fname,'^',mname) AS GtName, 
				'' AS Spouse_Name, 
				CONCAT(address,'^',address2,'^',city,'^',state,'^',zip,zip_ext) AS address, 
				REPLACE(home_ph,'-','') AS home_ph, 
				REPLACE(work_ph,'-','') AS work_ph, 
				DATE_FORMAT(dob,'%Y%m%d') AS dob, 
				UPPER(SUBSTR(sex,1,1)) AS sex, 
				'' AS Guarantor_Type, 
				relation  
			FROM resp_party WHERE patient_id = '".$iASC_patient_id."'";
		$GT1_res = imw_query($GT1_q);
		if($GT1_res && imw_num_rows($GT1_res)==1){
			$GT1_rs = imw_fetch_assoc($GT1_res);
		}else{
			$GT1_rs = $pid_rs;
		}
		$HL7MSG_TEXT_ADT .= 'GT1|'.implode('|',$GT1_rs).'
';

	}else{
		$HL7MSG_ERROR[] = 'Error in fetching iASC patient demographics.';
	}
	include("common/conDb.php"); 
	
	//FT1 SEGMENT (FINANCIAL TRANSCTION) {IT MAY REPEAT AS PER CPTs}
	$provider_id_field = "npi";
	if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
		$provider_id_field = "external_id";
	}
	$ft1_q = "SELECT dcs.dischargeSummarySheetId AS encounter_id, dcs.procedures_code, dcs.icd10_code, dcs.icd10_id, dcs.summarySaveDateTime, 
				CONCAT(u.".$provider_id_field.",'^',u.lname,'^',u.fname,'^',u.mname) AS provider_npi_name  
				FROM dischargesummarysheet dcs 
				JOIN users u ON (u.usersId = dcs.surgeonId)
				WHERE dcs.confirmation_id = '".$SC_pConfId."' LIMIT 0,1";
	$ft1_res = imw_query($ft1_q);
	if($ft1_res && imw_num_rows($ft1_res)>0){
		$ft_i = 1;
		$ft1_rs = imw_fetch_assoc($ft1_res);
		$all_procs = explode(',',$ft1_rs['procedures_code']);
		$all_diags = '';//str_replace(',','~',$ft1_rs['icd10_code']);
		$all_diags_ids = $ft1_rs['icd10_id'];
		$arr_all_diags = explode(',',$ft1_rs['icd10_code']);
		foreach($arr_all_diags as $d){
			if($all_diags!='') $all_diags .= '~';
			$all_diags .= $d.'^^I10';
		}
		//REPEAT FT1 SEGMENT AS MANY CPT AVAILABEL
		$facility_location = "";
		if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
			$facility_location = "4^FREEHOLD";
		}
		$PV1_SEGMENT = 'PV1|0001||||||'.$ft1_rs['provider_npi_name'].'|||||||||||||||||||||||||||||||||||||
';
		if(strtolower($GLOBALS["LOCAL_SERVER"])!='keywhitman'){
			$HL7MSG_TEXT 	.= $PV1_SEGMENT;
			$HL7MSG_TEXT_ADT .= $PV1_SEGMENT;
		}
		$pr1_segment_str = '';
		for($i=0;$i<count($all_procs);$i++){
			
			$HL7MSG_TEXT 	.= 'FT1|'.($i+1).'|||'.str_replace(array('-',' ',':'),'',$ft1_rs['summarySaveDateTime']).'||CG|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'|||1||||||'.$facility_location.'||O|'.$all_diags.'|'.$ft1_rs['provider_npi_name'].'|||DCS'.$ft1_rs['encounter_id'].'PC'.$SC_pConfId.'|'.$ft1_rs['provider_npi_name'].'|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'||
';
			$pr1_segment_str .= 'PR1|'.($i+1).'|CPT|'.$arr_procs_mapping[$all_procs[$i]]['cpt'].'||'.str_replace(array('-',' ',':'),'',$ft1_rs['summarySaveDateTime']).'|||||||'.$ft1_rs['provider_npi_name'].'||||
';
		}
		$HL7MSG_TEXT 	.= $pr1_segment_str;
		
		//if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
			//GETTING ALL DX CODES DETAILS FOR DG1 SEGMENT
			$res_dg = imw_query("SELECT icd10_desc FROM icd10_data WHERE id IN ($all_diags_ids)");
			if($res_dg && imw_num_rows($res_dg)>0){
				$all_diags_descrips = array();
				while($rs_dg = imw_fetch_assoc($res_dg)){
					$all_diags_descrips[] = $rs_dg['icd10_desc'];
				}
				for($i=0; $i<count($arr_all_diags); $i++){
					$HL7MSG_TEXT 	.= 'DG1|'.($i+1).'|ICD10|'.$arr_all_diags[$i].'^'.$all_diags_descrips[$i].'|
';//.$diag_descrip.'|'.$ft1_rs['summarySaveDateTime'].'|F||||||||||'.$ft1_rs['provider_npi_name'].'|D|'.chr(13);
				}
				
			}
		//}
	}else{
		$HL7MSG_ERROR[] = 'Error in getting Discharge Summary Sheet details.';	
	}
	

	//GETTING INSURANCE..
	$ins_type = array("primary","secondary","tertiary");
	$co_arr = count($ins_type);
	include('connect_imwemr.php');
	for($i=0;$i<$co_arr;$i++)
	{	
		$in_type = $ins_type[$i];
		$in1_sql = "SELECT '000".($i+1)."' AS set_id, 
				'' AS ID1, 
				in_house_code AS ID2,
				ic.name AS provider, 
				CONCAT(ic.contact_address,'^','','^',ic.City,'^',ic.State,'^',ic.Zip,ic.zip_ext) AS address, 
				id.comments AS ins_data_comments, 
				ic.contact_name AS Company_Cont_Person, 
				REPLACE(ic.phone,'-','') AS Company_Phone, 
				id.group_number, 
				'' AS Group_Name, 
				'' AS Group_Employer_id, 
				'' AS Group_Employer_Name, 
				DATE_FORMAT(id.effective_date,'%Y%m%d') AS effective_date, 
				'' AS expiration_date, 
				'' AS Authorization_Information, 
				'' AS Plan_Type, 
				CONCAT(id.subscriber_lname,'^', id.subscriber_fname, '^', SUBSTR(id.subscriber_mname,1,1)) AS subscriber_names, 
				id.subscriber_relationship, 
				DATE_FORMAT(id.subscriber_DOB,'%Y%m%d') AS subscriber_DOB, 
				CONCAT(id.subscriber_street,'^',id.subscriber_street_2,'^', id.subscriber_city,'^', id.subscriber_state,'^', id.subscriber_postal_code) AS subscriber_address, 
				'' AS assign_of_benifit, 
				'' AS cob, 
				'' AS cobp, 
				'' AS noaf, 
				'' AS noad, 
				'' AS rpt_of_ef,
				'' AS rpt_of_ed, 
				'' AS ric, 
				'' AS pac, 
				'' AS veridate, 
				'' AS veryby, 
				'' AS typeOfAC, 
				'' AS billingSt, 
				'' AS lifeReseDays, 
				'' AS delayBefore, 
				id.plan_name AS compPlanCode, 
				REPLACE(id.policy_number,'-','') AS PolicyNumber, 
				'' AS PolicyDeductible, 
				'' AS PolLimAmount, 
				'' AS PolLimDays, 
				'' AS RoomRateSP, 
				'' AS RoomRateP, 
				'' AS InsuEmpStatus, 
				SUBSTR(id.subscriber_sex,1,1) AS subscriber_sex 
				FROM insurance_data id 
				LEFT JOIN insurance_companies ic ON (id.provider=ic.id) 
				LEFT JOIN idx_invision_rco iir ON (iir.id=id.rco_code_id) 
				WHERE pid = '".$iASC_patient_id."' AND type = '".$in_type."' AND actInsComp='1' LIMIT 0,1";

		$in1_res = imw_query($in1_sql);
		if($in1_res && imw_num_rows($in1_res)==1){
			$in1_rs = imw_fetch_assoc($in1_res);
			foreach ($in1_rs as $key=>$val){
				if($key=='subscriber_relationship'){
					$in1_rs[$key] = mapping_vocabulary('REL_IN1',strtoupper($val));
					if($in1_rs[$key]==""){
						$in1_rs[$key] = mapping_vocabulary('REL_NK1',strtoupper($val));
					}
					if($in1_rs[$key]==""){
						$in1_rs[$key] = mapping_vocabulary('REL_GT1',strtoupper($val));
					}
				}
				if($key=='address'){//ins_data_comments
					if(strlen($in1_rs[$key])<=5 && trim($in1_rs['ins_data_comments'])!=''){
						$pt_ins_comments_arr = explode("\n",trim($in1_rs['ins_data_comments']));
						$pt_ins_comments_str = implode('^',$pt_ins_comments_arr);
						$in1_rs[$key] = str_replace("\n","",$pt_ins_comments_str);
						$in1_rs[$key] = str_replace("\r","",$pt_ins_comments_str);
					}else{
						$val = str_replace(array("\n","\r\n","\r"), ', ', $val);
						$in1_rs[$key] = $val;
					}
					unset($in1_rs['ins_data_comments']);
				}
				if($key=='effective_date' && $val == '00000000'){
					$in1_rs[$key] = '';
				}
			}
			
			//IN1 SEGMENT (INSURANCE INFORMATION)
			$HL7MSG_TEXT 	.= 'IN1|'.implode('|',$in1_rs).'
';
			$HL7MSG_TEXT_ADT 	.= 'IN1|'.implode('|',$in1_rs).'
';
		}else if(!$in1_res){
			$HL7MSG_ERROR[] = 'Error in fetching iASC Patient Insurance.';
		}
	}
	include("common/conDb.php"); 

}else{
	$HL7MSG_ERROR[] = 'Error in getting iASC Patient ID.';
}

if(count($HL7MSG_ERROR)==0){
	//MAKE,SAVE MESSAGE.
	$flat_file_path		= dirname(__DIR__)."/".$imwDirectoryName."/hl7sys/sender/outbound";
	//file_put_contents($flat_file_path."/1.txt",$HL7MSG_TEXT);
	$dftID='';
	//create log
	imw_query("insert into hl7_sent set patient_id='".$iASC_patient_id."',
				msg='". imw_real_escape_string($HL7MSG_TEXT) ."',
				msg_type='DFT',
				saved_on='".date('Y-m-d H:i:s')."',
				operator='".$_SESSION[loginUserId]."',
				send_to='drchoice',
				sch_id='".$SC_pConfId."'");
	$dftID=imw_insert_id();
	
	$newMsgId		= newMessageUniqueId();

	if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','palisades'))){
		$HL7MSG_TEXT_ADT 	= 'MSH|^~\&|IMW|IMW_'.strtoupper($GLOBALS["LOCAL_SERVER"]).'|TEST|TEST|'.date('Ymdhis').'||ADT^A08|'.$newMsgId.'|P|2.4||||||ASCII|||
'.$HL7MSG_TEXT_ADT;
		imw_query("insert into hl7_sent set patient_id='".$iASC_patient_id."',
				msg='". imw_real_escape_string($HL7MSG_TEXT_ADT) ."',
				msg_type='ADT',
				saved_on='".date('Y-m-d H:i:s')."',
				operator='".$_SESSION[loginUserId]."',
				send_to='drchoice',
				sch_id='".$SC_pConfId."'");
	}
	
	/*if(strtolower($GLOBALS["LOCAL_SERVER"])=='keywhitman'){
		//WRITE FLAT FILE FOR EACH MESSAGE
		if(is_dir($flat_file_path)){
			$text_file_name_DFT = $flat_file_path."/HL7_DFT_".$dftID.".hl7";
			$fp = fopen($text_file_name_DFT,'w');
			if($fp){
				$fw = fwrite($fp,$HL7MSG_TEXT);
				if($fw){//MARK THE MESSAGE AS SENT.
					imw_query("UPDATE hl7_sent SET sent=1, sent_on = '".date('Y-m-d H:i:s')."' WHERE id = '".$dftID."'");
				}
			}
		}
	}*/
	
}else{
	//LOG ERROR.
	//echo '<pre>';
	//print_r($HL7MSG_ERROR);
}

	
?>