<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php

require_once($GLOBALS['fileroot'] .'/library/classes/class.electronic_billing.php');
$objEBilling= new ElectronicBilling();
$edi_today_short 	= date('ymd');
$edi_now_short		= date('Hi');

$edi_today 			= date('Ymd');
$edi_now			= date('Hi');

$edi_not_possible		= false;
$edi_error_encounters 	= array();

$edi_charge_list_ids 	= implode(',',$arr_charge_list_ids);

$edi_submitter_id		= $THCICSubmitterId; 	//Differenct practice group wise.
if(empty($edi_submitter_id) && in_array(strtolower($billing_global_server_name), array('berkeleyeye'))){
	$edi_submitter_id		= 'SUB751'; 	//Differenct practice group wise.
}

$edi_receiver_id		= 'YTH837';	//FIXED VALUE FOR system13, inc.
$edi_submitter_id_space	= str_repeat(' ',15 - strlen($edi_submitter_id));
$edi_receiver_id_space	= str_repeat(' ',15 - strlen($edi_receiver_id));

$edi_ten_spaces			= str_repeat(' ',10);
$edi_unique_headers 	= edi_get_unique_headers();

$edi_version_identifier	= '005010X222A1';
if($group_institution=='1') $edi_version_identifier	= '005010X223A2';

$production_mode_code = 'P';

//INTERCHANGE HEADER DATA SEGMENTS
$edi_837_data = 'ISA*00*'.$edi_ten_spaces.'*01*'.$edi_ten_spaces.'*ZZ*'.$edi_submitter_id.$edi_submitter_id_space.'*ZZ*'.$edi_receiver_id.$edi_receiver_id_space.'*'.$edi_today_short.'*'.$edi_now_short.'*^*00501*'.$edi_unique_headers['interchange_control_num'].'*1*'.$production_mode_code.'*:~
GS*HC*'.$edi_submitter_id.$edi_submitter_id_space.'*'.$edi_receiver_id.$edi_receiver_id_space.'*'.$edi_today.'*'.$edi_now.'*'.$edi_unique_headers['new_interchange_num'].'*X*'.$edi_version_identifier.'~
ST*837*'.$edi_unique_headers['new_interchange_num'].'*'.$edi_version_identifier.'~
BHT*0019*00*'.$edi_unique_headers['new_interchange_num'].'*'.$edi_today.'*'.$edi_now.'*RP~
NM1*41*2*'.$group_name.'*****46*'.$edi_submitter_id.'~
NM1*40*2*THCIC*****46*'.$edi_receiver_id.'~
';

//HEIRARCHI STRUCTURCE STARTED
$edi_HL_counter = 1;
$edi_837_data .= 'HL*'.$edi_HL_counter.'**20*1~
';$edi_HL_counter++;

$edi_group_info_rs = $objEBilling->get_groups_detail($grp_id);
$force_pos_fac_code83 = false;
if($group_institution=='1' && stristr($edi_group_info_rs['name'],'surgery')){
	$force_pos_fac_code83 = true;	
}

//BILLING PROVIDER LOOP
if(empty($pos_facility) === false){
	//IF pos FACILITY SELECTED
	$edi_pos_info_rs 	= (object)getRecords('pos_facilityies_tbl','pos_facility_id',$pos_facility);
	$edi_837_data .= 'NM1*85*2*'.trim(substr($edi_pos_info_rs->facility_name,0,60)).'*****XX*'.$edi_pos_info_rs->npiNumber.'~
N3*'.preg_replace('/\*/',' ',trim($edi_pos_info_rs->pos_facility_address)).'~
N4*'.$edi_pos_info_rs->pos_facility_city.'*'.$objEBilling->correctStateName($edi_pos_info_rs->pos_facility_state).'*'.$edi_pos_info_rs->pos_facility_zip.$edi_pos_info_rs->zip_ext.'~
';	
}else{
	//IF pos FACILITY not SELECTED, SEND GROUP DETAILS
	$edi_837_data .= 'NM1*85*2*'.trim(substr($edi_group_info_rs['name'],0,60)).'*****XX*'.$edi_group_info_rs['group_NPI'].'~
N3*'.preg_replace('/\*/',' ',trim($edi_group_info_rs['group_Address1'].'*'.$edi_group_info_rs['group_Address2'])).'~
N4*'.$edi_group_info_rs['group_City'].'*'.$objEBilling->correctStateName($edi_group_info_rs['group_State']).'*'.$edi_group_info_rs['group_Zip'].$edi_group_info_rs['zip_ext'].'~
';
}

//BILLING PROVIDER THCIC IDENTIFICATION
if($selected_thcic_id != ''){
$edi_837_data .= 'REF*1J*'.$selected_thcic_id.'~
';
}

//SUBSCRIBER INFORMATION
$edi_master_dataset = edi_get_master_data($edi_charge_list_ids);
$edi_encounter_ids_arr = array();
$edi_pcld_ids_arr = array();
if($edi_master_dataset){
	
	foreach($edi_master_dataset as $pcl_rs){
		$edi_pcl_id				= $pcl_rs['ClId'];
		$edi_patient_id			= $pcl_rs['PtId'];
		$edi_encounter_id		= $pcl_rs['EncId'];
		$edi_encounter_ids_arr[]= $edi_encounter_id;
		$edi_ins_case_id 		= $pcl_rs['case_type_id'];
		$edi_date_of_service 	= $pcl_rs['date_of_service'];
		$enc_icd10				= intval($pcl_rs['enc_icd10']);
		
		$edi_insurance_rs		= $objEBilling->get_patient_insurance($edi_ins_case_id,$edi_patient_id,'primary',$edi_date_of_service);
		
		if(!$edi_insurance_rs){
			$edi_error_encounters[$edi_pcl_id] = 'Patient insurance not found';
			/*
			continue; //skip for this subscriber.
			$edi_sbr02	= '18'; //self
			*/
			$edi_sbr09	= 'ZZ'; //UNKNOWN or SELFPAY
			$edi_insurance_rs['InsName'] = 'UNKNOWN';
			$edi_nm1pr08 = 'ZZ';
			$edi_nm1pr09 = 'UNKNOWN';
			$edi_insurance_rs['subscriber_lname'] 	= $pcl_rs['ptLname'];
			$edi_insurance_rs['subscriber_fname'] 	= $pcl_rs['ptFname'];
			$edi_insurance_rs['policy_number'] 		= '';
			$edi_insurance_rs['subscriber_street'] 	= $pcl_rs['ptStreet'];
			$edi_insurance_rs['subscriber_street_2']= $pcl_rs['ptStreet2'];
			$edi_insurance_rs['subscriber_city'] 	= $pcl_rs['ptCity'];
			$edi_insurance_rs['subscriber_state'] 	= $pcl_rs['ptState'];
			$edi_insurance_rs['subscriber_postal_code'] = $pcl_rs['ptPostal_code'];
			$edi_insurance_rs['zip_ext'] 			= '';
			$edi_insurance_rs['subscriber_country']	= $pcl_rs['ptCountry'];
			$edi_insurance_rs['subscriber_DOB'] 	= $pcl_rs['ptDOB'];
			$edi_insurance_rs['subscriber_sex'] 	= $pcl_rs['ptSex'];
			$edi_insurance_rs['subscriber_ss']		= $pcl_rs['ptSS'];
			$edi_insurance_rs['subscriber_relationship']	= 'self';
			
		}else{
			$edi_sbr09	= 'CI';
			if($edi_insurance_rs['claim_type'] == "1"){
				$edi_sbr09 = 'MB';
			}else if($edi_insurance_rs['ins_type'] != '' && strlen(trim($edi_insurance_rs['ins_type']))==2){
				$edi_sbr09 = strtoupper(trim($edi_insurance_rs['ins_type']));
			}
			$edi_nm1pr08 = 'PI';
			$edi_nm1pr09 = $edi_insurance_rs['InsCompPayerId'];
			if($group_institution!='1') $edi_nm1pr09 = $edi_insurance_rs['InsCompPayerIdPro'];
		}
		$edi_sbr02				= get_sub_rel_code($edi_insurance_rs['subscriber_relationship']);
		
		$sub_HL04 = '0';
		if($edi_sbr02!='18'){$edi_sbr02=''; $sub_HL04 = '1';}
		$edi_837_data .= 'HL*'.$edi_HL_counter.'*1*22*'.$sub_HL04.'~
';
		$non_self_HLID = $edi_HL_counter;
		$edi_HL_counter++;
		
		$edi_837_data .= 'SBR*P*'.$edi_sbr02.'*******'.$edi_sbr09.'~
';
		//IF SUBSCRIBER IS THE PATIENT.
		if($edi_sbr02=='18'){
			$edi_state_name = $objEBilling->correctStateName($edi_insurance_rs['subscriber_state']);
			
			$pt_race_code	= edi_get_race_ethinicity_code($pcl_rs['race'],'race');
			$ethnicity_code	= edi_get_race_ethinicity_code($pcl_rs['ethnicity'],'ethnicity');
			$p_subscriber_country	= edi_get_subs_country_code($edi_insurance_rs['subscriber_country']);
			
			
			$edi_837_data .= 'NM1*IL*1*'.$edi_insurance_rs['subscriber_lname'].'*'.$edi_insurance_rs['subscriber_fname'].'****MI*'.$edi_insurance_rs['policy_number'].'~
N3*'.preg_replace('/\*/',' ',trim($edi_insurance_rs['subscriber_street'])).'*'.$edi_insurance_rs['subscriber_street_2'].'~
N4*'.$edi_insurance_rs['subscriber_city'].'*'.$edi_state_name.'*'.$edi_insurance_rs['subscriber_postal_code'].$edi_insurance_rs['zip_ext'].$p_subscriber_country.'~
DMG*D8*'.preg_replace("/-/","",$edi_insurance_rs['subscriber_DOB']).'*'.substr($edi_insurance_rs['subscriber_sex'],0,1).'**'.$pt_race_code.'~
';
			if(trim($edi_insurance_rs['subscriber_ss'])==''){
				$edi_insurance_rs['subscriber_ss'] = '999999999';
			}
			$edi_837_data .= 'REF*SY*'.preg_replace("/-/","",$edi_insurance_rs['subscriber_ss']).'~
';
		/*	$edi_837_data .= 'K3*'.$ethnicity_code.$pt_race_code.'~
';		*/
			
		}
		
		//PAYER INFO
		$edi_837_data	.= 'NM1*PR*2*'.$edi_insurance_rs['InsName'].'*****'.$edi_nm1pr08.'*'.$edi_nm1pr09.'~
';

		//IS SUBSCRIBER IS NOT THE PATIENT.
		if($edi_sbr02 != '18'){
		
			$edi_837_data	.= 'HL*'.$edi_HL_counter.'*'.$non_self_HLID.'*23*0~
';
			$edi_HL_counter++;
		/*
			if($edi_sbr02 != '01'){$edi_sbr02 = 'G8';}
';*/
			$edi_837_data .= 'PAT*18~
';
			$edi_837_data .= 'NM1*QC*1*'.trim($pcl_rs['ptLname']).'*'.trim($pcl_rs['ptFname']).'*'.trim(substr($pcl_rs['ptMname'],0,1)).'**~
';
			if(trim($pcl_rs['ptStreet'])==''){
				$pcl_rs['ptStreet'] = trim($pcl_rs['ptCity']);
			}
			$edi_837_data .= 'N3*'.preg_replace('/\*/',' ',$pcl_rs['ptStreet']);
			if(trim($pcl_rs['ptStreet2'])){
				$edi_837_data .= '*'.preg_replace('/\*/',' ',trim($pcl_rs['ptStreet2']));
			}
			$edi_837_data .= '~
';
			$edi_837_data .= 'N4*'.trim($pcl_rs['ptCity']).'*';
			$state_name = $objEBilling->correctStateName($pcl_rs['ptState']);
			$edi_837_data .= $state_name.'*'.trim($pcl_rs['ptPostal_code']).'~
';
			$patDOB = preg_replace("/-/","",$pcl_rs['ptDOB']);
			$pt_race_code	= edi_get_race_ethinicity_code($pcl_rs['race'],'race');
			$ethnicity_code	= edi_get_race_ethinicity_code($pcl_rs['ethnicity'],'ethnicity');
			$edi_837_data .= 'DMG*D8*'.$patDOB.'*'.substr($pcl_rs['ptSex'],0,1).'**~
';
			if(trim($pcl_rs['ptSS'])==''){
				$pcl_rs['ptSS'] = '999999999';
			}
			$edi_837_data .= 'K3*'.$ethnicity_code.$pt_race_code.preg_replace("/-/","",$pcl_rs['ptSS']).'~
';
		}
		
		//CLAIM DETAILS START
		$edi_pcld_rs = get_charge_list_details($edi_pcl_id);
		//pre($edi_pcld_rs);
		if(!$edi_pcld_rs){
			$edi_error_encounters[$edi_pcl_id] = 'Changes detail not found.';
		}else{
			$diagnosis_id = array();
			$totalAmt = 0;
			
			foreach($edi_pcld_rs as $i=>$pcld_rs){
				$edi_pcld_ids_arr[] = $pcld_rs['charge_list_detail_id'];;
				$posFacilityId 		= $pcld_rs['posFacilityId'];
				$place_of_service 	= $pcld_rs['place_of_service'];
				$posCodeDetails 	= (object)getRecords('pos_tbl','pos_id',$place_of_service);
				$posFacilityDetail 	= (object)getRecords('pos_facilityies_tbl','pos_facility_id',$posFacilityId);
				$fac_pos_code 		= $posCodeDetails->pos_code;
				if(ucwords($posFacilityDetail->facilityPracCode) == 'HOME' && strtoupper($posFacilityId) == 'HOME'){
					$fac_pos_code = NULL;
				}
				if(is_null($fac_pos_code) || empty($fac_pos_code) || $force_pos_fac_code83){
					$fac_pos_code = '83';
				}
				for($x=1;$x<=12;$x++){
					if(isset($pcld_rs['diagnosis_id'.$x]) && empty($pcld_rs['diagnosis_id'.$x]) === false){
						$diagnosis_id[] = preg_replace("/\./","",$pcld_rs['diagnosis_id'.$x]);
					}
				}
			}
			
			//----- MERGE ALL UNIQUE DIAGNOSIS CODE ---------
			$diagnosisId = array_unique($diagnosis_id);
			if($enc_icd10==1){
				$diagnosisIds = join("*ABF:",$diagnosisId);
			}else{
				$diagnosisIds = join("*BF:",$diagnosisId);
			}
			
			$edi_837_data	.= 'CLM*'.$edi_patient_id.'*'.$pcl_rs['postedAmount'].'***'.$fac_pos_code.'::1***************~
';
			$edi_837_data	.= 'DTP*434*RD8*'.preg_replace("/-/","",$pcl_rs['date_of_service']).'-'.preg_replace("/-/","",$pcl_rs['date_of_service']).'~
';
			$edi_837_data	.= 'REF*EA*'.$edi_patient_id.'~
';

			if($edi_sbr02 == '18'){
				/*if(trim($pcl_rs['ptSS'])==''){
					$pcl_rs['ptSS'] = '999999999';
				}*/
				$edi_837_data .= 'K3*'.$ethnicity_code.$pt_race_code.'~
';
			}else if($edi_sbr02 != '18'){
				if(trim($pcl_rs['ptSS'])==''){
					$pcl_rs['ptSS'] = '999999999';
				}
				$edi_837_data .= 'K3*'.$ethnicity_code.$pt_race_code.preg_replace("/-/","",$pcl_rs['ptSS']).'~
';
			}

			//$ethnicity_code	= edi_get_race_ethinicity_code($pcl_rs['ethnicity'],'ethnicity');
			/*if($ethnicity_code){
				$edi_837_data	.= 'NTE*UPI*'.$ethnicity_code.'~
';
			}*/
					
			if($enc_icd10==1){
				$edi_837_data .= 'HI*ABK:'.$diagnosisIds.'~
';
			}else{
				$edi_837_data .= 'HI*BK:'.$diagnosisIds.'~
';
			}
			
			//ATTENDING PHYSICIAN LOOP (prof)/ OPERATING PHYSICIAN LOOP (INST.)
			if($group_institution=='0'){
				$edi_837_data	.= 'NM1*82*1*'.$pcl_rs['usersLname'].'*'.$pcl_rs['usersFname'].'*'.$pcl_rs['usersMname'].'***XX*'.$pcl_rs['userNpi'].'~
';
				$edi_837_data 	.= 'NM1*77*1*'.$posFacilityDetail->facility_name.'*'.$posFacilityDetail->facility_name.'****XX*'.$posFacilityDetail->npiNumber.'~
';
			}else if($group_institution=='1'){
				$edi_837_data 	.= 'NM1*72*1*'.$pcl_rs['usersLname'].'*'.$pcl_rs['usersFname'].'*'.$pcl_rs['usersMname'].'***XX*'.$pcl_rs['userNpi'].'~
';
				$edi_837_data 	.= 'NM1*FA*2*'.$posFacilityDetail->facility_name.'*****XX*'.$posFacilityDetail->npiNumber.'~
';
			}
			if($selected_thcic_id != ''){
				$edi_837_data .= 'REF*1J*'.$selected_thcic_id.'~
';
			}
			
		/******SECONDARY PAYER INFO LOOP*******/
		$edi_insurance_rs_SEC	= $objEBilling->get_patient_insurance($edi_ins_case_id,$edi_patient_id,'secondary',$edi_date_of_service);
			
		if(!$edi_insurance_rs_SEC){
			$edi_error_encounters[$edi_pcl_id] = 'Patient Secondary insurance not found';
			$edi_sbr09_SEC	= 'ZZ'; //UNKNOWN or SELFPAY
			$edi_insurance_rs_SEC['InsName'] = 'UNKNOWN';
			$edi_nm1pr08_SEC = 'ZZ';
			$edi_nm1pr09_SEC = 'UNKNOWN';
			$edi_insurance_rs_SEC['subscriber_lname'] 	= $pcl_rs['ptLname'];
			$edi_insurance_rs_SEC['subscriber_fname'] 	= $pcl_rs['ptFname'];
			$edi_insurance_rs_SEC['policy_number'] 		= '';
			$edi_insurance_rs_SEC['subscriber_street'] 	= $pcl_rs['ptStreet'];
			$edi_insurance_rs_SEC['subscriber_street_2']= $pcl_rs['ptStreet2'];
			$edi_insurance_rs_SEC['subscriber_city'] 	= $pcl_rs['ptCity'];
			$edi_insurance_rs_SEC['subscriber_state'] 	= $pcl_rs['ptState'];
			$edi_insurance_rs_SEC['subscriber_postal_code'] = $pcl_rs['ptPostal_code'];
			$edi_insurance_rs_SEC['zip_ext'] 			= '';
			$edi_insurance_rs_SEC['subscriber_country']	= $pcl_rs['ptCountry'];
			$edi_insurance_rs_SEC['subscriber_DOB'] 	= $pcl_rs['ptDOB'];
			$edi_insurance_rs_SEC['subscriber_sex'] 	= $pcl_rs['ptSex'];
			$edi_insurance_rs_SEC['subscriber_ss']		= $pcl_rs['ptSS'];
			$edi_insurance_rs_SEC['subscriber_relationship']	= 'self';
		}else{
			$edi_sbr09_SEC	= 'CI';
			if($edi_insurance_rs_SEC['claim_type'] == "1"){
				$edi_sbr09_SEC = 'MB';
			}else if($edi_insurance_rs_SEC['ins_type'] != '' && strlen(trim($edi_insurance_rs_SEC['ins_type']))==2){
				$edi_sbr09_SEC = strtoupper(trim($edi_insurance_rs_SEC['ins_type']));
			}
			$edi_nm1pr08_SEC = 'PI';
			$edi_nm1pr09_SEC = $edi_insurance_rs_SEC['InsCompPayerId'];
			if($group_institution!='1') $edi_nm1pr09_SEC = $edi_insurance_rs_SEC['InsCompPayerIdPro'];
		}
		$edi_sbr02_SEC		= get_sub_rel_code($edi_insurance_rs_SEC['subscriber_relationship']);
			
			
			
			
		if($edi_insurance_rs_SEC){
			$edi_837_data .= 'SBR*S*'.$edi_sbr02_SEC.'*******'.$edi_sbr09_SEC.'~
';
			$s_subscriber_country	= edi_get_subs_country_code($edi_insurance_rs_SEC['subscriber_country']);
			//IF SUBSCRIBER IS THE PATIENT.
		//if($edi_sbr02_SEC=='18'){
			$edi_state_name_SEC = $objEBilling->correctStateName($edi_insurance_rs_SEC['subscriber_state']);
			$pt_race_code_SEC	= edi_get_race_ethinicity_code($pcl_rs['race'],'race');			
			$edi_837_data .= 'NM1*IL*1*'.$edi_insurance_rs_SEC['subscriber_lname'].'*'.$edi_insurance_rs_SEC['subscriber_fname'].'****MI*'.$edi_insurance_rs_SEC['policy_number'].'~
';
			$edi_837_data .= 'N3*'.preg_replace('/\*/',' ',trim($edi_insurance_rs_SEC['subscriber_street'])).'*'.$edi_insurance_rs_SEC['subscriber_street_2'].'~
';
			$edi_837_data .= 'N4*'.$edi_insurance_rs_SEC['subscriber_city'].'*'.$edi_state_name.'*'.$edi_insurance_rs_SEC['subscriber_postal_code'].$edi_insurance_rs_SEC['zip_ext'].$s_subscriber_country.'~
';
			$edi_837_data .= 'DMG*D8*'.preg_replace("/-/","",$edi_insurance_rs_SEC['subscriber_DOB']).'*'.substr($edi_insurance_rs_SEC['subscriber_sex'],0,1).'**'.$pt_race_code.'~
';
			/*
			if(trim($edi_insurance_rs_SEC['subscriber_ss'])==''){
				$edi_insurance_rs_SEC['subscriber_ss'] = '999999999';
			}
			$edi_837_data .= 'REF*SY*'.preg_replace("/-/","",$edi_insurance_rs_SEC['subscriber_ss']).'~
';			*/
			
		//}
		
		//PAYER INFO
		$edi_837_data	.= 'NM1*PR*2*'.$edi_insurance_rs_SEC['InsName'].'*****'.$edi_nm1pr08_SEC.'*'.$edi_nm1pr09_SEC.'~
';

		

			/*
			NM1*IL*1*SMITH*EDWARD****MI*111112223333
			N3*1405 OLD BRIDGE
			N4*PINE BEACH*NJ*08741
			NM1*PR*2*HIGHMARK MEDICARE*****PI*SMNJ0
			*/
			
		}
		/******END OF SECONDARY PAYER INFO LOOP*******/
			
			
			//LINE LEVEL ITEMS START (CPT LEVEL)
			foreach($edi_pcld_rs as $i=>$pcld_rs){
				
				if($group_institution=='1'){
					//--- REV CODE DETAILS ---
					$q_rev_code_val = empty($pcld_rs['rev_code']) ? $pcld_rs['cpt_rev_code'] : $pcld_rs['rev_code'];
					$rev_qry = imw_query("select r_code from revenue_code where r_id = '".$q_rev_code_val."'");
					$rev_qry_res = imw_fetch_assoc($rev_qry);
					$rev_code = $rev_qry_res["r_code"];
				}
				//--- MODIFIERS ---
				$modifierIdArr = array();
				if(empty($pcld_rs['modifier_id1']) === false){$modifierIdArr[] = $pcld_rs['modifier_id1'];}
				if(empty($pcld_rs['modifier_id2']) === false){$modifierIdArr[] = $pcld_rs['modifier_id2'];}
				if(empty($pcld_rs['modifier_id3']) === false){$modifierIdArr[] = $pcld_rs['modifier_id3'];}
				
				$mod_code_arr = array();
				for($mo=0;$mo<count($modifierIdArr);$mo++){
					$mod_id = $modifierIdArr[$mo];
					$mod_code_arr[] = $modifierCodeArr[$mod_id];
				}
				
				$modifierCodes = NULL;
				$mod_count = 4;
				$mod_count = count($mod_code_arr);
				if($mod_code_arr[0] != ''){
					$modifierCodes = ':';
					$modifierCodes .= join(":",array_unique($mod_code_arr));
				}
				
				//--- PROC CHARGES ---
				$procCharge = $pcld_rs['totalAmount'];
				
				//--- PROC UNITS ---
				$proc_units = $pcld_rs['units'];
				if(substr($proc_units,-3)=='.00'){$proc_units = intval($proc_units);}
				
				
				$edi_837_data 	.= 'LX*'.($i+1).'~
';
				if($group_institution=='1'){
					$edi_837_data 	.= 'SV2*'.$rev_code.'*HC:'.$pcld_rs['cpt4_code'].$modifierCodes.'*'.$procCharge.'*UN*'.$proc_units.'~
';
				}else{
					$edi_837_data 	.= 'SV1*HC:'.$pcld_rs['cpt4_code'].$modifierCodes.'*'.$procCharge.'*UN*'.$proc_units.'~
';
				}
				$edi_837_data 	.= 'DTP*472*D8*'.preg_replace("/-/","",$pcl_rs['date_of_service']).'~
';
			}
		}
		
	}
}else{
	$edi_not_possible	= true;
}

$segmentCountArr = preg_split('/~/',$edi_837_data);

//FOOTER SEGMENTS
$edi_837_data .= 'SE*'.(count($segmentCountArr)-2).'*'.$edi_unique_headers['new_interchange_num'].'~
GE*1*'.$edi_unique_headers['new_interchange_num'].'~
IEA*1*'.$edi_unique_headers['interchange_control_num'].'~
';
//echo $edi_837_data;

if($selected_thcic_id == ''){
	$edi_837_data	= '';
}else if(!$edi_not_possible){
	//---INSERT RECORD TO TABLE--
	$Q_INS_EDI = "INSERT INTO batch_file_tx_report SET 
				  header_control_identifier 		= '".$edi_unique_headers['header_control_identifier']."',
				  Transaction_set_unique_control	= '".$edi_unique_headers['transaction_set_unique_control']."',
				  file_name							= '".'tx_state_report'.date('YmdHis')."',
				  file_data							= '".addslashes($edi_837_data)."',
				  create_date						= '".date('Y-m-d')."',
				  encounter_id						= '".implode(',',$edi_encounter_ids_arr)."',
				  pcld_id							= '".implode(',',array_unique($edi_pcld_ids_arr))."',
				  submitter_id						= '".$edi_submitter_id."',
				  reciever_id						= '".$edi_receiver_id."',
				  group_id							= '".$grp_id."',
				  production_code					= '".$production_mode_code."',
				  Interchange_control				= '".$edi_unique_headers['interchange_control_num']."',
				  operatorId						= '".$_SESSION['authId']."'
				 ";
	imw_query($Q_INS_EDI);
}

function edi_padd_string($str,$totLength,$paddwith=' ',$prefixOrSuffix = 'prefix'){
	$diff = $totLength - strlen($str);
	$padd = '';
	if($diff > 0){
		$padd = str_repeat($paddwith,$diff);
	}
	if($prefixOrSuffix=='prefix'){return $padd.$str;}else{return $str.$padd;}
}
/*GETTING NEW UNIQUE HEADERS WHILE CREATING NEW RECORD FOR BATCH FILE, CLAIM STATUS REQUEST, PRE-AUTH ETC.*/
function edi_get_unique_headers($type=837){
	$ARR_headerNumbers = array();
	$res1 = imw_query("SELECT max(Interchange_control)+1 as NewInterchangeNum FROM batch_file_tx_report");
	if($res1 && imw_num_rows($res1)==1){
		$rs1 = imw_fetch_assoc($res1);
		$Interchange_controls = (is_null($rs1['NewInterchangeNum']) && intval($rs1['NewInterchangeNum'])==0) ? 1 : intval($rs1['NewInterchangeNum']);
		$InterchangeControlNumber = edi_padd_string($Interchange_controls,9,'0','prefix');
		$set_number = $Interchange_controls * 100000;
		$set_number = substr($set_number,0,7);
		$set_number = $set_number + $Interchange_controls;
		$Transaction_set_unique_control = $set_number;
		$header_control_identifier = $set_number;
		$ARR_headerNumbers['new_id_num'] 						= $Interchange_controls;
		$ARR_headerNumbers['new_interchange_num'] 				= $Interchange_controls;
		$ARR_headerNumbers['interchange_control_num'] 			= $InterchangeControlNumber;
		$ARR_headerNumbers['transaction_set_unique_control']	= $Transaction_set_unique_control;
		$ARR_headerNumbers['header_control_identifier'] 		= $header_control_identifier;
		return $ARR_headerNumbers;			
	}else{
		return false;
	}
}

function edi_get_master_data($edi_charge_list_ids){
	global $from_report;
	$q = "SELECT pcl.charge_list_id AS ClId, 
		pcl.encounter_id AS EncId, pcl.patient_id AS PtId, pcl.primaryInsuranceCoId AS PriIns, 
		pcl.secondaryInsuranceCoId AS SecIns, pcl.tertiaryInsuranceCoId AS TerIns, 
		pcl.date_of_service AS encDOS, pcl.postedDate AS PostedDate, 
		pcl.primaryProviderId AS PhysicianId, pcl.admit_date, pcl.disch_date, 
		pcl.moaQualifier, pcl.case_type_id, 
		pcl.claim_ctrl_pri, pcl.claim_ctrl_sec, 
		pcl.reff_phy_id, pcl.enc_icd10, pcl.reff_phy_nr, pcl.void_notify, 
		pcl.billing_type, pcl.date_of_service, pcl.postedAmount, 
		pd.fname AS ptFname, pd.lname AS ptLname, substring(pd.mname,1,1) AS ptMname, pd.street AS ptStreet, 
		pd.street2 AS ptStreet2, CONCAT(pd.postal_code,pd.zip_ext) AS ptPostal_code, pd.city AS ptCity, 
		pd.country_code as ptCountry, 
		pd.state AS ptState, pd.sex AS ptSex, pd.DOB as ptDOB, pd.default_facility AS ptDefaultFacility, 
		pd.primary_care_id AS ptPrimaryCare, pd.providerID AS ptProviderID, 
		pd.race, pd.ethnicity, pd.ss as ptSS, 
		u.user_npi AS userNpi, u.BlueShieldId AS BlueShieldIds,
		trim(u.fname) AS usersFname, substring(trim(u.mname),1,1) AS usersMname,
		trim(u.lname) AS usersLname, u.id AS usersId,
		u.TaxonomyId AS usersTaxonomyId, u.TaxId AS usersTaxId,
		u.default_group AS users_default_group,
		u.federaltaxid AS usersFederaltaxid 
		
		FROM patient_charge_list pcl 
		
		LEFT JOIN patient_charge_list_details pcld ON (pcld.charge_list_id = pcl.charge_list_id AND pcld.differ_insurance_bill != 'true') 
		LEFT JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode 
		LEFT JOIN users u ON u.id = pcl.primaryProviderId 
		LEFT JOIN patient_data pd ON pcl.patient_id = pd.id	
		
		WHERE (pcld.del_status='0' 
			AND pcl.submitted = 'true') 
			AND (cft.not_covered = '0' or cft.not_covered is NULL) ";
		if($from_report != 'tx_state_report'){
			$q	.= " AND pcld.proc_selfpay = '1' ";
		}
		$q	.= "AND pcld.posted_status = '1' 
		AND pcl.charge_list_id IN ($edi_charge_list_ids) GROUP BY pcl.charge_list_id 	
		";
	//	echo $q;
		$res = imw_query($q);
		if(!$res){
			return false;
		}else{
			$result = array();
			while($rs = imw_fetch_assoc($res)){
				$result[] = $rs;
			}
			return $result;
		}
		echo $q;
}

function get_sub_rel_code($rel_text){
	$rel_text = strtolower($rel_text);
	switch($rel_text){
		case '':
		case 'self':
			return '18';
			break;
		case 'son':
		case 'daughter':
			return '19';
			break;
		case 'mother':
		case 'father':
		case 'guardian':
			return 'G8';
			break;
		case 'employee':
			return '20';
			break;
		case 'spouse':
			return '01';
			break;
		default:
			return 'G8';
	}
}

function get_charge_list_details($charge_list_id){
	//-- GET PATIENT CHARGE LIST DETAILS -----
	$q = "SELECT pcld.*, cft.cpt4_code, cft.cpt_desc, cft.cpt_comments, cft.rev_code AS cpt_rev_code 
			FROM patient_charge_list_details pcld 
			LEFT JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode	
			WHERE pcld.charge_list_id = '$charge_list_id' 
			AND pcld.posted_status='1'  AND (pcld.procCode != '' AND pcld.procCode != '0') 
			AND pcld.del_status='0' ";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$result = array();
		while($rs = imw_fetch_assoc($res)){
			//--- INVALID CLAIM IF PROCEDURE CODE MISSING ----
			/*$notes = $patientChargeDetails[$u]['notes'];
			if($notes != 'NOTES...' and empty($notes) == false){
				$notesArr[] = $notes;
			}
			*/
			
			//$tot_proc_amt 	= $rs['totalAmount'];
			//$totalAmt 		+= preg_replace('/,/','',$tot_proc_amt);
			
			//$posFacilityId = $rs['posFacilityId'];
			//$place_of_service = $rs['place_of_service'];
			
			$result[] 			= $rs;
		}
		return $result;
	}
	return false;
}

function edi_get_race_ethinicity_code($val,$type){
	$val = strtoupper($val);
	switch($type){
		case 'race':{
			switch($val){
				case 'AMERICAN INDIAN OR ALASKA NATIVE':
				case 'AMERICAN INDIAN':				
					return '1';
					break;
				case 'ASIAN':
				case 'NATIVE HAWAIIAN OR OTHER PACIFIC ISLANDER':
					return '2';
					break;
				case 'BLACK OR AFRICAN AMERICAN':
					return '3';
					break;
				case 'WHITE':
					return '4';
					break;
				default:
					return '5';
				
			}
			/*
			1 AMERICAN INDIAN/ESKIMO/ALEUT
			2 ASIAN OR NATIVE HAWAIIAN OR PACIFIC ISLANDER
			3 BLACK OR AFRICAN AMERICAN
			4 WHITE
			5 OTHER RACE
			*/
			break;
		}
		case 'ethnicity':{
			if($val=='HISPANIC OR LATINO') return '1';//else if($val=='NOT HISPANIC OR LATINO') return '2';
			else return '2';
			break;
		}
	}
	
}

function edi_get_subs_country_code($val){
	$val = trim(strtoupper($val));
	if(empty($val)) $val = 'USA';
	switch($val){
		case 'US':
		case 'USA':
		case 'UNITED STATES':
		case 'UNITEDSTATES':
		case 'UNITED STATE':
			return '';
			break;
		default:
			return '*00000';
	}
}
?>