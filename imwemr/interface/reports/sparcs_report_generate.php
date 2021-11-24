<?php
//echo $_POST['sparcs_file_mode'].'<br>';
//echo $_POST['selected_charge_ids'];

set_time_limit(180);//3 minutes.
include_once(dirname(__FILE__)."/../../config/globals.php");
error_reporting(-1);
ini_set("display_errors",-1);
include_once(dirname(__FILE__)."/../../library/classes/class.sparcs.php");
$objSparcs		= new SPARCS;
$cpt_proc_table_array = $objSparcs->get_cpt_proc_table_array();
$HL_counter = 1;



$sparcs_file_type = isset($_POST['sparcs_file_mode']) ? strtoupper($_POST['sparcs_file_mode']): 'T';
$today = date('ymd');
$todaY = date('Ymd');
$time = date('Hi');
$submitter_id 				= $objSparcs->padd_string($SPARCS_collector_code,15,' ','suffix');
$receiver_id				= $objSparcs->padd_string($SPARCS_collector_code,15,' ','suffix');
$trans_set_control_number 	= mt_rand(100000, 999999);


$sparcs_file_data = array();
$sparcs_file_data[] = 'ISA*00*          *00*          *ZZ*'.$submitter_id.'*ZZ*'.$receiver_id.'*'.$today.'*'.$time.'*^*00501*000000105*0*'.$sparcs_file_type.'*:~';
$sparcs_file_data[] = 'GS*HC*'.$SPARCS_collector_code.'*SPARCS*'.$todaY.'*'.$time.'*37*X*005010X225A2~';
$sparcs_file_data[] = 'ST*837*'.$trans_set_control_number.'*005010X225A2~';
$sparcs_file_data[] = 'BHT*0019*00*000036*'.$todaY.'*'.$time.'~';

//SUBMITTER
$sparcs_file_data[] = 'NM1*41*2*'.$SPARCS_fac_name.'*****46*'.$SPARCS_collector_code.'~';
$sparcs_file_data[] = 'PER*IC*'.$SPARCS_fac_contact_name.'*TE*'.$SPARCS_fac_contact_phone.'~';

//RECEIVER
$sparcs_file_data[] = 'NM1*40*2*SPARCS*****46*'.$SPARCS_natalie_code.'~';
$sparcs_file_data[] = 'HL*'.$HL_counter.'**20*1~';$HL_counter++;

//SERVICE PROVIDER
$sparcs_file_data[] = 'NM1*SJ*2*'.$SPARCS_fac_name.'*****XX*'.$SPARCS_fac_npi.'~';
//$sparcs_file_data[] = 'N3*'.$SPARCS_fac_address.'~';
//$sparcs_file_data[] = 'N4*'.$SPARCS_fac_city.'*NY*'.$SPARCS_fac_zip.'~';

//SERVICE PROVIDER SECONDARY IDENTIFICATION
$sparcs_file_data[] = 'REF*1J*'.$SPARCS_ID.'~';

$arr_pcl_ids = explode(',',$_POST['selected_charge_ids']);

for($i=0; $i<count($arr_pcl_ids); $i++){
	$charge_list_id = $arr_pcl_ids[$i];
	$charge_list_rs = $objSparcs->get_charge_list_id_details($charge_list_id);
	$patient_id		= $charge_list_rs['patient_id'];
	$proc_code_essi = $charge_list_rs['proc_code_essi'];
	//pre($charge_list_rs);
	extract($charge_list_rs);
	$subscriber_details = $objSparcs->get_patient_insurance($case_type_id,$patient_id,'primary',$date_of_service);
	//pre($subscriber_details,1);
	if(!$subscriber_details || !is_array($subscriber_details)) continue;
	else{
		if(empty($subscriber_details['policy_number'])) continue;
		if(empty($subscriber_details['InsCompPayerIdPro'])) continue;
	}
	$subscriber_details['policy_number'] = str_replace(array(' ','-'),'',$subscriber_details['policy_number']);
	
	if ($objSparcs->cptDisplayCheck($procCode, $SPARCS_avoid_cpts)) {
		$patient_data_rs = $objSparcs->get_patient_details($patient_id);
	}
	//pre($patient_data_rs);
	$birthdate 		=  date("Ymd", strtotime($patient_data_rs['DOB']));
	$gender 		= substr($patient_data_rs['sex'], 0,1);
	$patient_data_rs['ss'] = str_replace("-", "", $patient_data_rs['ss']);
	if(empty($patient_data_rs['ss'])) $patient_data_rs['ss'] = '000000000';
	$patient_unique_id = $objSparcs->getUniqueID($patient_id,$patient_data_rs['fname'],$patient_data_rs['lname'],$patient_data_rs['ss']); //Need to send Unique id of ssn is zeros.
	
	$sparcs_source_of_payment = "09";
	$individual_relationship_code = 18;
	$county_code 	= $objSparcs->get_county_code($patient_data_rs['state'],$patient_data_rs['country_code'],$patient_data_rs['postal_code']);
	$state 			= $patient_data_rs['state'];
	if($county_code=='99') $state='XX';
	$race 			= $objSparcs->getRaceCode($patient_data_rs['race']);
	$ethnic 		= $objSparcs->getEthnic($patient_data_rs['ethnicity']);
	
	$all_dx_codes_array = $objSparcs->get_all_dx_codes($charge_list_id);
	$sc_data_array = $objSparcs->get_sc_details($patient_id,$date_of_service);
	
	$discharge_hour = $admission_hour = "";
	if($sc_data_array && is_array($sc_data_array)){
		extract($sc_data_array);
		if(!empty($checked_out_time)){
			// reformate parameters for SPARCS format
			$discharge_hour = str_replace(array(":"," AM", " PM"), "", $checked_out_time);
			$discharge_hour = $objSparcs->getHourCode($discharge_time);
			$discharge_hour = str_replace(" ", "0", $discharge_hour);
		}
		if(!empty($checked_in_time)){
			// Admission
			$admission_hour = $objSparcs->getHourCode($checked_in_time);
			$admission_hour = str_replace(" ", "0", $admission_hour);
		}
	}
	
	/*****skip if basic validation failed for record**/
	$error = false;
	if (empty($charge_list_rs['primary_provider_id_for_reports'])) $error = true;
	if (empty($patient_data_rs['DOB']) || $patient_data_rs['DOB']=='0000-00-00') $error = true;
	if (empty($patient_id)) $error = true;
	if (empty($charge_list_rs['procCode'])) $error = true;
	if(!$all_dx_codes_array || !is_array($all_dx_codes_array) || count($all_dx_codes_array)==0) $error = true;
	if (empty($admission_hour)) $error = true;
	if (empty($discharge_hour)) $error = true;
	/******skip rule end here**********************/
	
	if(isset($sparcs_file_data_pt)) unset($sparcs_file_data_pt);
	$sparcs_file_data_pt = array();
	if(!$error){
		$sparcs_file_data_pt[] = 'HL*'.$HL_counter.'*1*22*0~';$HL_counter++;
		$sparcs_file_data_pt[] = 'SBR*P*'.$individual_relationship_code.'*******'.$sparcs_source_of_payment.'~';
		$sparcs_file_data_pt[] = 'PAT*********N~'; // Patient Information If Pregnant 'Y' or 'N' Set to 'N' for now.
		$sparcs_file_data_pt[] = 'NM1*IL*1*'.$patient_data_rs['lname'].'*'.$patient_data_rs['fname'].'****MI*'.str_replace(array(' ','-'),'',$subscriber_details['policy_number']).'~';
		$sparcs_file_data_pt[] = 'N3*'.$patient_data_rs['street'].'~';
		$sparcs_file_data_pt[] = 'N4*'.$patient_data_rs['city'].'*'.$state.'*'.$patient_data_rs['postal_code'].$patient_data_rs['zip_ext'].'**CO*'.$county_code.'~';
		$sparcs_file_data_pt[] = 'DMG*D8*'.$birthdate.'*'.$gender.'*K*:RET:'.$race.'^:RET:'.$ethnic.'*****REC*2~';
	
		if(substr($patient_data_rs['ss'],-4) != '0000'){
			$sparcs_file_data_pt[] = 'REF*SY*'.$patient_data_rs['ss'].'~';	//X225 patient ssn
		}else if($patient_unique_id){
			$sparcs_file_data_pt[] = 'REF*ABB*'.$patient_unique_id.'~';
		}
		
		list($insurance_name,$payer,$sparcs_typology_of_payment,$rbcode,$sparcs_source_of_payment) =  $objSparcs->get_sparcs_insurance_details($primaryInsuranceCoId);
		
		$insurance_name = trim($insurance_name);
		if (empty($insurance_name)) {
			$insurance_name = "SELF PAY";
		}
		
		$claim_amount = $objSparcs->getTotalAmountExact($charge_list_id,$SPARCS_avoid_cpts);
		
		$sparcs_file_data_pt[] = 'NM1*PR*2*'.$insurance_name.'*****PI*'.$subscriber_details['InsCompPayerIdPro'].'~';
		$sparcs_file_data_pt[] = 'CLM*'.$patient_id.'*'.$claim_amount.'***73:A:1~';
		
		$sparcs_file_data_pt[] = 'DTP*096*TM*'.$discharge_hour.'~';
		$sparcs_file_data_pt[] = 'DTP*434*RD8*'.str_replace('-','',$date_of_service).'-'.str_replace('-','',$date_of_service).'~';
		$sparcs_file_data_pt[] = 'DTP*435*DT*'.str_replace('-','',$date_of_service).$admission_hour.'~';
		$sparcs_file_data_pt[] = 'CL1*9*9*01~';
		$sparcs_file_data_pt[] = 'REF*EA*'.$patient_id.'~';
		
		$space = "";
		$sparcs_typology_of_payment = trim($sparcs_typology_of_payment);
		if (empty($sparcs_typology_of_payment)) {
			$sparcs_typology_of_payment = "81";
		}
		$sparcs_file_data_pt[] = 'NTE*UPI*'.$sparcs_typology_of_payment.'~';
		
		$first_dx_code 		= array_shift($all_dx_codes_array);
		$sparcs_file_data_pt[] = 'HI*ABK:'.$first_dx_code.'~';
		$sparcs_file_data_pt[] = 'HI*APR:'.$first_dx_code.'~';
		
		
		$cause_of_injury_code = 'Y999';	// Unspecified external cause status
		$place_of_injury_code = 'Y929';	// Unspecified place or not applicable
		
		$cause_and_place_of_injury_flag = null;
		$icd10y16_Injury_R_array = array();
		$count = 0;
		$HI_segment_data = "";
		if(($handle_R = fopen(dirname(__FILE__)."/../../library/sparcs_injury_codes/icd10y16_Injury_R_Data.csv", "r")) !== FALSE) {
			while(($data_R = fgetcsv($handle_R,  ",")) !== FALSE ) {
				$icd10y16_Injury_R_array[$count]["type"]			= trim($data_R[0]);
				$icd10y16_Injury_R_array[$count]["code"]			= trim($data_R[1]);
				$icd10y16_Injury_R_array[$count]["description"]		= trim($data_R[2]);
				$icd10y16_Injury_R_array[$count]["injury"]			= trim($data_R[3]);
				$count++;
			}
			fclose($handle_R);
		}
		if(($handle_Y = fopen(dirname(__FILE__)."/../../library/sparcs_injury_codes/icd10y16_InjuryDx_Y_Data.csv", "r")) !== FALSE) {
			while(($data = fgetcsv($handle_Y, ",")) !== FALSE ) {
				$field_diagnosis_code	= trim($data[1]);
				$field_injury_dx		= $data[4];
				if ($first_dx_code == $field_diagnosis_code) {
					//if ($cause_of_injury_code in icd10y16_Injury_RC_Data.csv)
					$HI_segment_data .= 'HI*ABN:'.$cause_of_injury_code;
					$cause_and_place_of_injury_flag = 1;
					break 1;
				}
			}
			fclose($handle_Y);
		}
		if (in_array($cause_of_injury_code, $icd10y16_Injury_R_array)) {
			$HI_segment_data .= '*ABN:'.$place_of_injury_code;
			$cause_and_place_of_injury_flag = 2;
		}
		if (isset($cause_and_place_of_injury_flag) && $cause_and_place_of_injury_flag>0) {
			$HI_segment_data .= '~';
			$sparcs_file_data_pt[] = $HI_segment_data;
		}
		
		if(count($all_dx_codes_array)>0){
			$hi_dx_str = '';
			foreach($all_dx_codes_array as $dx){
				$hi_dx_str .= '*ABF:'.$dx.':::::::';
			}
			$sparcs_file_data_pt[] = 'HI'.$hi_dx_str.'~';		
		}
		
		$HCPCS = "";
		list($HCPCS) = imw_fetch_array(imw_query("SELECT `proc_code` FROM `proc_code_tbl` WHERE `proc_code_id` = ".$proc_code_essi));
		list($hcpcs_cpt) = imw_fetch_array(imw_query("SELECT cpt4_code FROM cpt_fee_tbl WHERE cpt_fee_id=".$procCode." AND delete_status=0"));
		if ($proc_code_essi == 0) {
			foreach ($cpt_proc_table_array as $key => $value) {
				if ($hcpcs_cpt == $key) {
					$HCPCS = $value;
				}
			}
		}
		$hcode = str_replace(".", "", $HCPCS);
		if (!empty($hcode) && isset($hcode)) {
			$sparcs_file_data_pt[] = 'HI*BE:83:::'.$hcode.'~';
		}
		
		list($dr_fname,$dr_lname,$dr_license,$dr_npi) =$a =  $objSparcs->get_doctor_details($primary_provider_id_for_reports);
		$dr_license = str_pad($dr_license, 7, "0", STR_PAD_LEFT); // State License Number Seven Digits
		$dr_license = substr($dr_license, -7, 7);	// returns all seven characters from the right side.
		$sparcs_file_data_pt[] = 'NM1*71*1*'.trim($dr_lname).'*'.trim($dr_fname).'****XX*'.$dr_npi.'~'; //"NM1*71 Attending Provider Name X225.pdf page 229
		$sparcs_file_data_pt[] = 'REF*0B*'.trim($dr_license).'~';
		$sparcs_file_data_pt[] = 'NM1*72*1*'.trim($dr_lname).'*'.trim($dr_fname).'****XX*'.$dr_npi.'~'; //NM1*72 Operating Physician Name X225.pdf page 234
		$sparcs_file_data_pt[] = 'REF*0B*'.trim($dr_license).'~';
		
		$lx_CNT = 1;
		$pcld_res = $objSparcs->get_charges_details($charge_list_id);
		while (list($cptCode, $procedure_charge, $moda) = imw_fetch_array($pcld_res)) {
			$procedure_charge = number_format($procedure_charge, 2, ".", "");
			list($mod) = imw_fetch_array(imw_query("SELECT modifier_code FROM modifiers_tbl WHERE modifiers_id = ".$moda));
			list($cpt) = imw_fetch_array(imw_query("SELECT cpt4_code FROM cpt_fee_tbl WHERE cpt_fee_id = ".$cptCode));
			if($objSparcs->cptDisplay($cpt, $SPARCS_avoid_cpts)){
				$sparcs_file_data_pt[] = 'LX*'.$lx_CNT.'~';
				$sparcs_file_data_pt[] = 'SV2*0490*HC:'.trim($cpt).':'.$mod.'*'.$procedure_charge.'*UN*1~';												
				$sparcs_file_data_pt[] = 'DTP*472*D8*'.str_replace('-','',$date_of_service).'~';
				$lx_CNT++;
			}
		}
		$sparcs_file_data = array_merge($sparcs_file_data,$sparcs_file_data_pt);
	}
}
$sparcs_file_data[] = 'SE*'.(count($sparcs_file_data)-1).'*'.$trans_set_control_number.'*~';
$sparcs_file_data[] = 'GE*1*37~';
$interchange_control_number = "000000105";
$sparcs_file_data[] = 'IEA*1*'.$interchange_control_number.'~';

$FINAL_SPARCS_DATA = implode("\r\n",$sparcs_file_data);
?>
<form action="downloadFile.php" method="post" name="downloadFrm">
	<input type="hidden" name="edi837text" id="edi837text" value="<? echo $FINAL_SPARCS_DATA;?>" />
    <input type="hidden" name="self_pay_report" id="self_pay_report" value="yes" />
    <input type="hidden" name="edi_file_name" id="edi_file_name" value="sparcs_report.txt" />
</form>
<script>top.show_loading_image('hide');document.forms.downloadFrm.submit();</script>
<!--<pre><? echo $FINAL_SPARCS_DATA;?></pre>-->