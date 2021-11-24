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

?><?php
/*
File: medicare_electronic_file_5010.php
Purpose: Create Medicare 837 batch file
Access Type: Include File  
*/
global $arr_NE_Medicaid_payers;
$files = false;
$operatorId = array();
//--- GET DATA FOR CLAIM FILE ----		
if($createClaims != ''){
	$insertData = $this->count_today_batch_files();
			
	//---- GET UNIQUE HEADER CONTROL IDENTIFIER AND UNIQUE TRANSACTION NUMBER --------
	$batch_unique_headers = $this->get_unique_headers();
	$InterchangeControlNumber 		= $batch_unique_headers['interchange_control_num'];
	$Transaction_set_unique_control = $batch_unique_headers['transaction_set_unique_control'];
	$header_control_identifier 		= $batch_unique_headers['header_control_identifier'];
}

$fileData = 'ISA*00*          *00*          *ZZ*'.$submitterId.''.$submitterSpaceStr.'*ZZ*'.$recieverId.''.$recieveSpaceStr.'*'.date('ymd').'*'.date('hi').'*|*00501*'.$InterchangeControlNumber.'*1*'.$ProductionFile.'*:~';
$recieverId1 = $recieverId;
if(strtolower($billing_global_server_name)=='clearvue'){$recieverId1 = '09102';}
$fileData .= 'GS*HC*'.$submitterId.'*'.$recieverId1.'*'.date('Ymd').'*'.date('hi').'*'.$header_control_identifier.'*X*005010X222A1~';
$fileData .= 'ST*837*'.$Transaction_set_unique_control.'*005010X222A1~';
$fileData .= 'BHT*0019*00*424*'.date('Ymd').'*'.date('hi').'*CH~';

//$fileData .= 'REF*87*005010X222A1~'; commented in 5010.

$fileData .= 'NM1*41*2*'.trim(substr($groupDetails['name'],0,60)).'*****46*'.$submitterId.'~';	
if($facilityDetails->billing_location == 1){
	$facilityDetails->phone = preg_replace("/-/","",$facilityDetails->phone);
	$fileData .= 'PER*IC*'.trim($facilityDetails->billing_attention).'*TE*'.trim(preg_replace("/ /","",$facilityDetails->phone)).'~';
}
else{
	$groupDetails['group_Telephone'] = preg_replace("/-/","",$groupDetails['group_Telephone']);
	$fileData .= 'PER*IC*'.trim($groupDetails['Contact_Name']).'*TE*'.trim(preg_replace("/ /","",$groupDetails['group_Telephone'])).'~';
}	
$fileData .= 'NM1*40*2*'.trim($BatchFile).'*****46*'.$recieverId1.'~';
$subscriberId = 1;
$fileData .= 'HL*'.$subscriberId.'**20*1~';

//Getting insurance details of direct billing payer.
$medicare_ins_details = (object)$this->getInsCompDetails($Insurance,'nocheck');
if($file_type=='P'){$medicare_ins_details->Payer_id = $medicare_ins_details->Payer_id_pro;}
if(strtolower(trim($billing_global_server_name)) == 'sheepshead' && !in_array($InsuranceComDetails['0']['Payer_id_pro'],array('14163','86002'))){
	$billing_global_taxonomy_number = '';
}
if(trim($billing_global_taxonomy_number) != '' && !in_array($medicare_ins_details->Payer_id,$arr_RRM_payers)){
	$fileData .= 'PRV*BI*PXC*'.trim($billing_global_taxonomy_number).'~';
}

$group_npi 			= $groupDetails['group_NPI'];
$optional_anes_npi 	= $groupDetails['optional_anes_npi'];
if($optional_anes_npi != '') $group_npi = $optional_anes_npi;
if(!empty($overRightPayerWiseNPI) && $overRightPayerWiseNPI) $group_npi = $overRightPayerWiseNPI;
$groupDetails['group_NPI'] = $group_npi;

$fileData .= 'NM1*85*2*'.trim(substr($groupDetails['name'],0,60)).'*****XX*'.$groupDetails['group_NPI'].'~';
if($facilityDetails->billing_location == 1){
	if(trim($facilityDetails->street)){
		$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($facilityDetails->street)).'~';	
		$fileData .= 'N4*'.$facilityDetails->city.'*';
	}
	else{
		$fileData .= 'N3*'.$facilityDetails->city.'*';
	}
	$state_name = correct_state_name($facilityDetails->state);
	$fileData .= $state_name.'*'.$facilityDetails->postal_code.$facilityDetails->zip_ext.'~';
}
else{
	$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($groupDetails['group_Address1'].' '.$groupDetails['group_Address2'])).'~';
	$fileData .= 'N4*'.$groupDetails['group_City'].'*';
	$state_name = correct_state_name($groupDetails['group_State']);
	$fileData .= $state_name.'*'.$groupDetails['group_Zip'].$groupDetails['zip_ext'].'~';
}
$fileData .= 'REF*EI*'.preg_replace("/-/","",$groupDetails['group_Federal_EIN']).'~';
$refSubmitterid = substr(trim($groupDetails['site_id']),0,4);
$fileData .= "REF*G5*$refSubmitterid~";

if(in_array(strtolower($billing_global_server_name),array('manahan'))){ // Pay to Address; Loop 2010AB;
	if(strtolower(substr($groupDetails['name'],0,3))=='man'){
		$fileData .= 'NM1*87*2*Manahan Eye Associates PC~';
		$fileData .= 'N3*P O BOX 3701~';
		$fileData .= 'N4*OMAHA*';
		$fileData .= 'NE*681030701~';
	}else if(strtolower(substr($groupDetails['name'],0,3))=='pap'){
		$fileData .= 'NM1*87*2*Papillion Eye Surgical Center~';
		$fileData .= 'N3*P O BOX 3811~';
		$fileData .= 'N4*OMAHA*';
		$fileData .= 'NE*681030811~';
	}
}else if(!empty($groupDetails['rem_address1']) && !empty($groupDetails['rem_zip']) && !empty($groupDetails['rem_city']) && !empty($groupDetails['rem_state'])){ // Pay to Address; Loop 2010AB;
	$fileData .= 'NM1*87*2*'.trim(substr($groupDetails['name'],0,60)).'~';
	$fileData .= 'N3*'.$groupDetails['rem_address1'];
	if(!empty($groupDetails['rem_address2'])){
		$fileData .= '*'.$groupDetails['rem_address2'];	
	}
	$fileData .= '~';
	$fileData .= 'N4*'.$groupDetails['rem_city'].'*'.$groupDetails['rem_state'].'*'.$groupDetails['rem_zip'].$groupDetails['rem_zip_ext'].'~';
}

//--- GET ALL MODIFIERS ----
$modifierCodeArr = $this->get_all_modifiers();

//--- NOT COVERED AND SELF PAY PROCEDURE -----
$cptQry = "SELECT pcld.charge_list_detail_id, pcld.charge_list_id from patient_charge_list_details pcld 
			JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode 
			JOIN patient_charge_list pcl ON (pcl.charge_list_id = pcld.charge_list_id) 
			WHERE pcld.charge_list_id IN($main_charge_list_id) 
			AND (pcld.posted_status='0' OR (pcld.claim_status = '1' AND pcl.void_notify='0') OR 
			cft.not_covered = '1' OR pcld.proc_selfpay = '1' OR (pcld.del_status='1' AND pcl.void_notify='0'))";
$cptQryRes = imw_query($cptQry);
$notCoverProDetailIdArr = array();
while($cptQryRs = imw_fetch_assoc($cptQryRes)){
	$charge_list_id = $cptQryRs['charge_list_id'];
	$notCoverProDetailIdArr[$charge_list_id][] = $cptQryRs['charge_list_detail_id'];
}

//------ MAIN CHARGE LIST QUERY ----------
$chrgs_qry = "select patient_charge_list.*, patient_data.fname AS patientFname,
		patient_data.lname AS patientLname, substring(patient_data.mname,1,1) AS patientMname, 
		patient_data.suffix AS patientSuffix, 
		patient_data.street AS patientStreet, patient_data.street2 AS patientStreet2,
		CONCAT(patient_data.postal_code,patient_data.zip_ext) AS patientPostal_code, patient_data.city AS patientCity,
		patient_data.state AS patientState, patient_data.sex AS patientSex,
		patient_data.DOB as patientDOB,
		patient_data.id AS patientId, patient_data.default_facility AS patientDefaultFacility,
		patient_data.primary_care_id AS patientPrimaryCare,
		patient_data.providerID AS patientProviderID, 
		patient_data.pt_disable, 
		insurance_companies.name AS insurance_companies_name,
		insurance_companies.contact_address AS insurance_companies_contact_address,
		insurance_companies.City AS insurance_companies_City,
		insurance_companies.State AS insurance_companies_State,
		insurance_companies.in_house_code AS insurance_companies_in_house_code,
		insurance_companies.Zip AS insurance_companies_Zip,
		insurance_companies.phone AS insurance_companies_phone,
		insurance_companies.BatchFile AS insurance_companies_BatchFile,
		insurance_companies.payer_type AS insurance_companies_payer_type,
		insurance_companies.Reciever_id AS insurance_companies_Reciever_id,
		insurance_companies.id AS insurance_companies_id,
		insurance_companies.Payer_id AS insurance_Payer_id,
		insurance_companies.Payer_id_pro AS insurance_Payer_id_pro,
		users.user_npi AS userNpi, trim(users.fname) AS usersFname,
		substring(trim(users.mname),1,1) AS usersMname,
		trim(users.lname) AS usersLname, users.id AS usersId,
		users.TaxonomyId AS usersTaxonomyId, users.TaxId AS usersTaxId,
		users.default_group AS users_default_group, users.user_npi AS users_npi,
		patient_charge_list.admit_date, 
		patient_charge_list.disch_date, 
		users.federaltaxid AS usersFederaltaxid from patient_charge_list 
		left join insurance_companies on patient_charge_list.$InsComp = insurance_companies.id
		left join users on patient_charge_list.primaryProviderId = users.id
		left join patient_data on patient_charge_list.patient_id = patient_data.id				
		left join patient_charge_list_details 
		on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
		where ((patient_charge_list_details.del_status='0' and patient_charge_list.$setField = '0') OR (patient_charge_list.void_notify='1' AND patient_charge_list.void_claim_date='0000-00-00 00:00:00'))
		and patient_charge_list_details.differ_insurance_bill != 'true' 
		AND patient_charge_list_details.posted_status='1' 
		and patient_charge_list_details.proc_selfpay != '1' 
		AND (patient_charge_list.totalBalance > '0' OR (patient_charge_list.postedAmount > 0 AND patient_charge_list.date_of_service >= '2013-01-01')) 
		and patient_charge_list.charge_list_id in ($main_charge_list_id) 
		group by patient_charge_list.charge_list_id order by users.lname,
		patient_charge_list.date_of_service desc, patient_data.lname, patient_data.fname";	
				
$ress = imw_query($chrgs_qry);

if($ress && imw_num_rows($ress)>0){
	//--- MAX 25 CLAIM FOR TEST CLAIM FILE ------
	$loopCount = imw_num_rows($ress);
	if($ProductionFile == 'T' and $loopCount > 50){
		$loopCount = 50;
	}

	$encounter_id = array();	
	$transectionCount = 0;
	$i = 0;
	$res = array();
	while($rss = imw_fetch_assoc($ress)){
		$res[$i] = $rss;
		$fileName = $fileNameStart.$insertData.'.8375010.clm';
		
		if($InsComp == 'primaryInsuranceCoId'){
			$primaryFile = true;
			$SecondType = 'secondary';
			$type = 'primary';
			$ins_comp = '0';
			$reffType = 1;
			$secondInsComp = 'secondaryInsuranceCoId';
		}
		else{
			$primaryFile = false;
			$SecondType = 'primary';
			$type = 'secondary';
			$ins_comp = '1';
			$reffType = 2;
			$secondInsComp = 'primaryInsuranceCoId';
		}
		$invalidClaim = false;
		$patientId = $res[$i]['patient_id'];
		$date_of_service = $res[$i]['date_of_service'];
		$ins_caseid = $res[$i]['case_type_id'];
		$charge_list_id = $res[$i]['charge_list_id'];
		$referral = $res[$i]['referral'];
		$enc_icd10= intval($res[$i]['enc_icd10']);
		$enc_facility_id = intval($res[$i]['facility_id']);
		if($enc_facility_id == 0){
			$enc_facility_id = intval($res[$i]['patientDefaultFacility']);
		}
		$ref_phy_not_required = $res[$i]['reff_phy_nr'];
		//--- GET PATIENT NAME DETAILS ----
		$patient_name_arr = array();
		$patient_name_arr["LAST_NAME"] = $res[$i]['patientLname'];
		$patient_name_arr["FIRST_NAME"] = $res[$i]['patientFname'];
		$patient_name_arr["MIDDLE_NAME"] = $res[$i]['patientMname'];
		$pt_disabled					= intval($res[$i]['pt_disable']);
		$patient_name = changeNameFormat($patient_name_arr);
		
		//--- NOT COVERED OR SELF PAY PROCEDURE FOR SINGLE ENCOUNTER ---
		$not_cov_proc_arr = array();
		$not_cov_proc_arr = $notCoverProDetailIdArr[$charge_list_id];
		$not_cov_proc_str = '';
		if(count($not_cov_proc_arr) > 0){
			$not_cov_proc_str = join(",", $not_cov_proc_arr);
		}
	
		//-- GET PATIENT CHARGE LIST DETAILS -----		
		$pcld_q = "select patient_charge_list_details.*, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_comments, cpt_fee_tbl.unit_of_measure, cpt_fee_tbl.measurement 
				from patient_charge_list_details 
				left join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode				
				where patient_charge_list_details.charge_list_id = '$charge_list_id' ";
				//AND patient_charge_list_details.posted_status='1' AND patient_charge_list_details.claim_status='0'";
		if(empty($not_cov_proc_str) === false){
			$pcld_q .= " and patient_charge_list_details.charge_list_detail_id not in ($not_cov_proc_str)";
		}
		
		$pcld_res	= imw_query($pcld_q);
		$patientChargeDetails = array();
		while($pcld_rs = imw_fetch_assoc($pcld_res)){
			$patientChargeDetails[] = $pcld_rs;
		}
		
		$diagnosis_id = array();
		$value_codes_rates = array();
		$onset_date = array();
		$approval1 = array();
		$approval2 = array();		
		$paidProcAmount = 0;
		$totalAmt = 0;
		$detailIdArr = array();
		$writeOffAmtArr = array();
		
		$tempp_admit_date = explode(' ',$res[$i]['admit_date']);
		$admit_date = $tempp_admit_date[0];
		$anes_start_time = $tempp_admit_date[1];
		$tempp_disch_date = explode(' ',$res[$i]['disch_date']);
		$disch_date = $tempp_disch_date[0];
		$anes_stop_time = $tempp_disch_date[1];
		$anesthesia_flag = false;
		if(($groupDetails['group_anesthesia']=='1' && $res[$i]['billing_type']=='0') || $res[$i]['billing_type']=='1'){
			$anesthesia_flag = true;
		}
		
		if($anesthesia_flag){
			if($anes_start_time == '' || $anes_start_time == '00:00:00'){
				$error[$res[$i]['charge_list_id']] = "Anesthesia Start Time required For Encounter-ID.".$res[$i]['encounter_id'];
				$invalidClaim = true;
			}else if($anes_stop_time == '' || $anes_stop_time == '00:00:00'){
				$error[$res[$i]['charge_list_id']] = "Anesthesia Stop Time required For Encounter-ID.".$res[$i]['encounter_id'];
				$invalidClaim = true;
			}
		}

		$anes_proc_array = array();
		$CLIA_Procedure	 = false;
		for($u=0;$u<count($patientChargeDetails);$u++){
			$detailIdArr[] = $patientChargeDetails[$u]['charge_list_detail_id'];
			if(trim($patientChargeDetails[$u]['rev_rate'])!=''){
				$value_codes_rates[] = $patientChargeDetails[$u]['rev_rate'];
			}
			//--- INVALID CLAIM IF PROCEDURE CODE MISSING ----
			if(empty($patientChargeDetails[$u]['procCode']) === true){
				$error[$res[$i]['charge_list_id']] = 'Procedure code missing for encounter-ID.'.$res[$i]['encounter_id'];
				$invalidClaim = true;
			}

			if(trim($patientChargeDetails[$u]['cpt4_code']) == 'G8447'){
				$error[$res[$i]['charge_list_id']] = 'Procedure code G8447 is not valid';
				$invalidClaim = true;
			}
			
			if(trim($patientChargeDetails[$u]['cpt4_code']) == '83861'){
				$CLIA_Procedure = true;
			}			
			
			if(trim($patientChargeDetails[$u]['cpt4_code']) == '83516'){
				$CLIA_Procedure = true;
			}
			
			$anes_proc_array[] = $patientChargeDetails[$u]['cpt4_code'];	
			$notes = $patientChargeDetails[$u]['notes'];
			if($notes != 'NOTES...' and empty($notes) == false){
				$notesArr[] = $notes;
			}
			
			$writeOffAmtArr[] = $patientChargeDetails[$u]['write_off'];
			$tot_proc_amt = $patientChargeDetails[$u]['totalAmount'];
			$totalAmt += preg_replace('/,/','',$tot_proc_amt); 
			
			$posFacilityId = $patientChargeDetails[$u]['posFacilityId'];
			$place_of_service = $patientChargeDetails[$u]['place_of_service'];
			if($patientChargeDetails[$u]['onset_date'] != '0000-00-00'){
				$onset_date[] = $patientChargeDetails[$u]['onset_date'];
			}
			if($patientChargeDetails[$u]['approval1']){
				$approval1[] = preg_replace("/\./","",$patientChargeDetails[$u]['approval1']);
			}
			if($patientChargeDetails[$u]['approval2']){
				$approval2[] = preg_replace("/\./","",$patientChargeDetails[$u]['approval2']);
			}
			
			$dxCntUpto = 12;
			for($x=1;$x<=$dxCntUpto;$x++){
				if(isset($patientChargeDetails[$u]['diagnosis_id'.$x]) && empty($patientChargeDetails[$u]['diagnosis_id'.$x]) === false){
					$diagnosis_id[] = preg_replace("/\./","",$patientChargeDetails[$u]['diagnosis_id'.$x]);
				}
			}
		}
		
		$totalAmt = number_format($totalAmt,2);
		$writeOffAmtTotal = array_sum($writeOffAmtArr);
		
		//----- MERGE ALL UNIQUE DIAGNOSIS CODE ---------
		$diagnosisId = array_unique($diagnosis_id);
		if($enc_icd10==1){
			$diagnosisIds = join("*ABF:",$diagnosisId);
		}else{
			$diagnosisIds = join("*BF:",$diagnosisId);
		}
		
		$posCodeDetails = (object)getRecords('pos_tbl','pos_id',$place_of_service);
		if(empty($posCodeDetails)){
			$error[$res[$i]['charge_list_id']] = 'POS Required For '.$patient_name.' ('.$patientId.')';
			$invalidClaim = true;
		}
		if($posFacilityId == '' || $posFacilityId == '0'){
			$posFacilityId = $res[$i]['patientDefaultFacility'];
		}
		$posFacilityDetail = (object)getRecords('pos_facilityies_tbl','pos_facility_id',$posFacilityId);
		
		if($posFacilityId != 'Home'){
			if($posFacilityDetail == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = 'POS Facility Required For Encounter-ID.'.$res[$i]['encounter_id'];
				$invalidClaim = true;
			}
		}
		
		$reffPhyscianId = $res[$i]['reff_phy_id'];

		if($reffPhyscianId == 0 || $reffPhyscianId == ''){
			$reffPhyscianId = $res[$i]['primaryProviderId'];
			$reffDetail = (object)getRecords('users','id',$reffPhyscianId);
			$reffPhysicianLname = $reffDetail->lname;
			$reffPhysicianFname = $reffDetail->fname;
			$reffPhysicianMname = substr($reffDetail->mname,0,1);
			$npiNumber = $reffDetail->user_npi;
			$Texonomy = $reffDetail->TaxonomyId;
		}
		else{
			$reffDetail = (object)getRecords('refferphysician','physician_Reffer_id',$reffPhyscianId);
			$reffPhysicianLname = $reffDetail->LastName;
			$reffPhysicianFname = $reffDetail->FirstName;
			$reffPhysicianMname = substr($reffDetail->MiddleName,0,1);
			$npiNumber = $reffDetail->NPI;
			$Texonomy = $reffDetail->Texonomy;
		}
		
		$subscriberDetails = (object)$this->get_patient_insurance($ins_caseid,$patientId,$type,$date_of_service);					
		$otherInsDetail = (object)$this->getInsCompDetails($subscriberDetails->provider,'nocheck');
		if($file_type=='P'){$otherInsDetail->Payer_id = $otherInsDetail->Payer_id_pro;}
		//---- INSURANCE COMPANY NAME FOR DISPLAY ---
		$insCompId[$res[$i]['charge_list_id']] = $otherInsDetail->in_house_code;

		$payment_method = trim($otherInsDetail->Insurance_payment);
		if($primaryFile == false){
			$payment_method = trim($otherInsDetail->secondary_payment_method);
		}
		
		if($payment_method != "Electronics" && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier is not set for Electronics File Claims.";
			$invalidClaim = true;
		}
		if(trim($otherInsDetail->Payer_id_pro) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Payer Id is Required.";
			$invalidClaim = true;
		}
		else if(strlen(trim($otherInsDetail->Payer_id_pro)) < 3 && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Payer Id minimum length violation.";
			$invalidClaim = true;
		}
		if(trim($res[$i]['patientSex']) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = 'Patient Gender Infomation is Required.';
			$invalidClaim = true;
		}
		if(trim($res[$i]['users_npi']) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = 'Rendering Physician NPI # is Required.';
			$invalidClaim = true;
		}
		if(trim($res[$i]['usersTaxonomyId']) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = 'Rendering Physician Taxonomy # is Required.';
			$invalidClaim = true;
		}
		if($ref_phy_not_required == '0' && $reffDetail == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = 'Referring Physician is Required.';
			$invalidClaim = true;
		}
		if($ref_phy_not_required == '0' && trim($npiNumber) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = 'Referring Physician NPI # is Required.';
			$invalidClaim = true;
		}
/*		if(trim($Texonomy) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = 'Referring Physician Taxonomy # is Required.';
			$invalidClaim = true;
		}
*/		if(trim($subscriberDetails->policy_number) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Policy # is Required.";
			$invalidClaim = true;
		}
		if(trim($subscriberDetails->subscriber_street) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Subscriber Address is Required.";
			$invalidClaim = true;
		}
		if(trim($subscriberDetails->subscriber_postal_code) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Subscriber Postal Code is Required.";
			$invalidClaim = true;
		}
		if(trim($subscriberDetails->subscriber_state) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Subscriber State is Required.";
			$invalidClaim = true;
		}
		if(trim($subscriberDetails->subscriber_city) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Subscriber City is Required.";
			$invalidClaim = true;
		}
		if(trim($subscriberDetails->subscriber_lname) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Subscriber Last Name is Required.";
			$invalidClaim = true;
		}
		if(trim($subscriberDetails->subscriber_fname) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Subscriber First Name is Required.";
			$invalidClaim = true;
		}
		if(trim($subscriberDetails->subscriber_sex) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Subscriber Gender Information is Required.";
			$invalidClaim = true;
		}
		if(trim($subscriberDetails->subscriber_DOB) == '0000-00-00' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$patient_name $type Subscriber Date of Birth is Required.";
			$invalidClaim = true;
		}

		$subscriberDetails2 = (object)$this->get_patient_insurance($ins_caseid,$patientId,$SecondType,$date_of_service);
		$otherInsDetail2 = (object)$this->getInsCompDetails($subscriberDetails2->provider,'nocheck');
		if($primaryFile != false && ($res[$i]['secondaryInsuranceCoId'] != $subscriberDetails2->provider)){
			$subscriberDetails2 = $otherInsDetail2 = false;
			unset($subscriberDetails2);
			unset($otherInsDetail2);
		}
		if(is_object($subscriberDetails2) === false){
			if($primaryFile == false){
				$error[$res[$i]['charge_list_id']] = "Primary insurance company is require for (DOS - $date_of_service).";
				$invalidClaim = true;
			}
		}
		else{
			if(trim($subscriberDetails2->policy_number) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Insurance Carrier Policy # is Required.";
				$invalidClaim = true;
			}
			
			if(trim($otherInsDetail2->Payer_id_pro) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Insurance Carrier Payer Id is Required.";
				$invalidClaim = true;
			}
			if(trim($subscriberDetails2->subscriber_street) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Subscriber Address is Required.";
				$invalidClaim = true;
			}
			if(trim($subscriberDetails2->subscriber_postal_code) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Subscriber Postal Code is Required.";
				$invalidClaim = true;
			}
			if(trim($subscriberDetails2->subscriber_state) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Subscriber State is Required.";
				$invalidClaim = true;
			}
			if(trim($subscriberDetails2->subscriber_city) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Subscriber City is Required.";
				$invalidClaim = true;
			}
			if(trim($subscriberDetails2->subscriber_lname) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Subscriber Last Name is Required.";
				$invalidClaim = true;
			}
			if(trim($subscriberDetails2->subscriber_fname) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Subscriber First Name is Required.";
				$invalidClaim = true;
			}
			if(trim($subscriberDetails2->subscriber_sex) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Subscriber Gender Information is Required.";
				$invalidClaim = true;
			}
			if(trim($subscriberDetails2->subscriber_DOB) == '0000-00-00' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$patient_name $SecondType Subscriber Date of Birth is Required.";
				$invalidClaim = true;
			}
		}
					
		//--- VALID CLAIMS CHECK --------
		if($invalidClaim == false){
			$clm_control_num = '';
			$files = true;
			$notTaken[] = $res[$i]['charge_list_id'];
			$notTakenId = join(",",$notTaken);										
			$operatorId[] = $res[$i]['operator_id'];
			//---- SET CHANGE FILE STATUS VARIABLE -----
			$insCompanyId[] = $otherInsDetail->id;
			$subscriberId++;
			$encounter_id[] = $res[$i]['encounter_id'];					
			//-- GET SUBSCRIBER RELATION CODE -----
			if($subscriberDetails->subscriber_relationship == '' || $subscriberDetails->subscriber_relationship == 'self'){
				$selfCode = 18;
			}
			elseif($subscriberDetails->subscriber_relationship == 'Son'){
				$selfCode = 19;						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Doughter'){
				$selfCode = 19;						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Mother'){
				$selfCode = "G8";						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Father'){
				$selfCode = "G8";						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Guardian'){
				$selfCode = "G8";
			}
			elseif($subscriberDetails->subscriber_relationship == 'Employee'){
				$selfCode = '20';
			}
			elseif($subscriberDetails->subscriber_relationship == 'Spouse'){
				$selfCode = '01';
			}
			else{
				$selfCode = 'G8';
			}
			if($subscriberDetails->subscriber_relationship != '' || $subscriberDetails->subscriber_relationship != 'self'){
				$ins_comp = 1;
			}
			if($selfCode == 18){
				$ins_comps = 0;
			}
			else{
				$ins_comps = 1;
			}
			if($batchFileStatus == true){
				$ins_comps = 0;
				$selfCode = 18;
			}
			$fileData .= 'HL*'.$subscriberId.'*1*22*'.$ins_comps.'~';			
			$subscriberDetails->policy_number = preg_replace("/[^A-Za-z0-9]/","",$subscriberDetails->policy_number);
			$groupNumber = $subscriberDetails->group_number;
			if($groupNumber == '' || strtolower($groupNumber) == 'none'){
				if(!in_array(strtolower($billing_global_server_name),array('manahan'))){
					//$groupNumber = trim($subscriberDetails->policy_number);
				}
			}
			$plan_name = $subscriberDetails->plan_name;
			if(($plan_name == '' || strtolower($plan_name) == 'none') && strtolower($subscriberDetails->group_number) != 'none'){
				$plan_name = $subscriberDetails->group_number;
			}
			
			$medicade_chk_arr = explode("MEDICADE",strtoupper($otherInsDetail->name));
			$insInstance = NULL;
			if($primaryFile == false){
				preg_match('/MEDICARE/', strtoupper($otherInsDetail->in_house_code), $ins_med_chk_arr);
				if(count($ins_med_chk_arr) > 0){
					$insInstance = '47';
				}				
			}
			
			$SBR109 = 'MB';
			if(strtolower($billing_global_server_name)=='revision_eye' && strtolower(substr($type,0,1))=='s'){$insInstance = '12';}
			if($otherInsDetail->ins_type != '' && strlen(trim($otherInsDetail->ins_type))==2 && strtolower($billing_global_server_name) == 'sheepshead'){
				$SBR109 = strtoupper(trim($otherInsDetail->ins_type));
			}
			if($primaryFile == false && $SBR109=='MB' && strtolower($billing_global_server_name)=='brian'){$insInstance = '47';}
			if($primaryFile == false && $SBR109=='MB' && in_array(strtolower($billing_global_server_name),array('cec','risserthomas','domi','azar'))){		
				$insInstance = '12';
			}
			
			if($pt_disabled==1 && $primaryFile==false){
				$insInstance = '43';	
			}
			
			/***********OVER WRITING SBR05 VALUE IF ITS SET WITH PAYER OR SUBSCRIBER*******/
			if($primaryFile == false){
				$subs_msp_type = trim($subscriberDetails->msp_type);
				$payer_msp_type= trim($otherInsDetail->msp_type);
				if($payer_msp_type!='')	{$insInstance	= $payer_msp_type;}
				if($subs_msp_type!='')	{$insInstance	= $subs_msp_type;}
			}
			
			$fileData .= 'SBR*'.substr($type,0,1).'*'.$selfCode.'***'.$insInstance.'****'.$SBR109.'~';
			
			$subcriber_suffix = $subscriberDetails->subscriber_suffix;
			if($subcriber_suffix=='' && $selfCode=='18'){$subcriber_suffix = trim($res[$i]['patientSuffix']);}
			$fileData .= 'NM1*IL*1*'.trim($res[$i]['patientLname']);
			$fileData .= '*'.trim($res[$i]['patientFname']);
			$patient_policy_number = (string)$subscriberDetails->policy_number;
			$fileData .= '*'.trim($res[$i]['patientMname']).'**'.$subcriber_suffix.'*MI';
			$fileData .= '*'.$patient_policy_number.'~';
			if(trim($subscriberDetails->subscriber_street)){
				$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($subscriberDetails->subscriber_street)).'~';
				$fileData .= 'N4*'.trim($subscriberDetails->subscriber_city).'*';
			}
			else{
				$fileData .= 'N3*'.trim($subscriberDetails->subscriber_city).'~';
				$fileData .= 'N4*'.trim($subscriberDetails->subscriber_city).'*';
			}
			$state_name = correct_state_name($subscriberDetails->subscriber_state);
			$fileData .= $state_name.'*'.trim($subscriberDetails->subscriber_postal_code.$subscriberDetails->zip_ext).'~';
				
			$DOB = preg_replace("/-/","",$subscriberDetails->subscriber_DOB);
			$subscriber_sex = $subscriberDetails->subscriber_sex;
			$fileData .= 'DMG*D8*'.$DOB.'*'.substr($subscriber_sex,0,1).'~';
			
			//--- INSURANCE COMPANY NAME SHOULD BE LESS THAN 35 CHR ---
			$ins_new_name = trim($otherInsDetail->name);
			$ins_new_name = substr($ins_new_name,0,34);
			$fileData .= 'NM1*PR*2*'.trim($ins_new_name).'*****PI*'.$otherInsDetail->Payer_id_pro.'~';
			if(trim($otherInsDetail->contact_address) != ''){
				$fileData .= "N3*".trim(substr(trim($otherInsDetail->contact_address),0,55))."~";
			}else{
				$fileData .= "N3*".trim($otherInsDetail->city)."~";
			}
			$fileData .= "N4*".trim($otherInsDetail->City)."*";
			$state_name = correct_state_name($otherInsDetail->State);
			$insZip1 = preg_replace("/-/","",trim($otherInsDetail->Zip.$otherInsDetail->zip_ext));
			$fileData .= $state_name."*".$insZip1."~";
			
			//--- PATIENT DETAILS IF SUBSCRIBER NOT PATIENT ---
			if($selfCode != 18){
				$subscriberId++;
				if($selfCode != '01'){
					$selfCode = 'G8';
				}				
				$fileData .= 'HL*'.$subscriberId.'*1*23*'.$ins_comps.'~';
				$fileData .= "PAT*$selfCode~";
				$fileData .= 'NM1*QC*1*'.trim($res[$i]['patientLname']).'*'.trim($res[$i]['patientFname']).'*'.trim(substr($res[$i]['patientMname'],0,1)).'**~';
				if(trim($res[$i]['patientStreet'])==''){
					$res[$i]['patientStreet'] = trim($res[$i]['patientCity']);
				}
				$fileData .= 'N3*'.preg_replace('/\*/',' ',$res[$i]['patientStreet']);
				if(trim($res[$i]['patientStreet2'])){
					$fileData .= '*'.preg_replace('/\*/',' ',trim($res[$i]['patientStreet2']));
				}
				$fileData .= '~';
				$fileData .= 'N4*'.trim($res[$i]['patientCity']).'*';
				$state_name = correct_state_name($res[$i]['patientState']);
				$fileData .= $state_name.'*'.trim($res[$i]['patientPostal_code']).'~';
				$patDOB = preg_replace("/-/","",$res[$i]['patientDOB']);
				$fileData .= 'DMG*D8*'.$patDOB.'*'.substr($res[$i]['patientSex'],0,1).'~';
			}

			$clm_type_indicator = 1;
			if(isset($PCLid_CliamType) && trim($PCLid_CliamType) != ''){
				$ARRpostClaimType = json_decode($PCLid_CliamType);
				$clm_type_indicator = (isset($ARRpostClaimType->$charge_list_id) && intval($ARRpostClaimType->$charge_list_id) > 0) ? intval($ARRpostClaimType->$charge_list_id) : 1;
			}
			/*---GET PATIEN'S ICN---*/
			if(intval($billing_global_clm_type_indicator_medicare)==0){
				$clm_control_num = billing_global_get_clm_control_num($res[$i]['patient_id'],$res[$i]['encounter_id'],$ICN_amount,$type);
				if((!$clm_control_num || trim($clm_control_num)=='') && $clm_type_indicator!=8){
					$clm_type_indicator = 1;
				}
			}
			
			if(in_array($SBR109,array('MB','MA')) && $clm_type_indicator!='8'){$clm_type_indicator = 1;$clm_control_num = '';}
			
			$CHLS_Type_Status[$charge_list_id] = $clm_type_indicator;
			if($clm_type_indicator > 1 && intval($billing_global_clm_type_indicator_medicare)>0){
				$clm_type_indicator = intval($billing_global_clm_type_indicator_medicare);
			}
			$CLM07 = 'A';
			$CLM08 = 'Y';
			if(intval($res[$i]['enc_accept_assignment'])==1){
				$CLM07 = 'C';
				$CLM08 = 'N';
			}
			$fileData .= 'CLM*'.$res[$i]['patientId'].'*'.preg_replace("/,/","",$totalAmt).'***';
			
			//-- GET PATIENT`S PROCEDURES DETAILS ----------
			$fileData .= ''.$posCodeDetails->pos_code.':B:'.$clm_type_indicator.'*Y*'.$CLM07.'*'.$CLM08.'*Y*P~';

			$setDate = array_unique($onset_date);
			/*if($setDate[0] != ''){
				$fileData .= 'DTP*431*d8*'.preg_replace("/-/","",$setDate[0]).'~';
			}*/
			
			if($admit_date != '0000-00-00'){
				$fileData .= 'DTP*431*d8*'.preg_replace("/-/","",$admit_date).'~';
				
				$fileData .= 'DTP*435*D8*'.preg_replace("/-/","",$admit_date).'~';
			}else if($posCodeDetails->pos_code == 21){
				$fileData .= 'DTP*435*D8*'.preg_replace("/-/","",$res[$i]['date_of_service']).'~';
			}
			
			if($disch_date != '0000-00-00'){
				$fileData .= 'DTP*096*D8*'.preg_replace("/-/","",$disch_date).'~';
			}else if($posCodeDetails->pos_code == 21){
				$fileData .= 'DTP*096*D8*'.preg_replace("/-/","",$res[$i]['date_of_service']).'~';
			}

			/***CLAIM SUPPLEMENT INFORMATION (LOOP 2300)*****/
			$res[$i]['report_type_code']		= trim($res[$i]['report_type_code']);
			$res[$i]['transmission_code']		= trim($res[$i]['transmission_code']);
			$res[$i]['control_no']				= trim($res[$i]['control_no']);
			if($res[$i]['report_type_code']!='' && $res[$i]['transmission_code']!='' && $res[$i]['control_no']!=''){
				$fileData .= 'PWK*'.$res[$i]['report_type_code'].'*'.$res[$i]['transmission_code'].'***AC*'.$res[$i]['control_no'].'~';
			}
			
			/****CLAIM CROSSOVER INDICATOR (LOOP 2300)*******/
			if($otherInsDetail2->ins_type=='MC'){
				$fileData .= "REF*F5*N~";
			}
			
			/*-Prior Authorization or Referral Number (loop 2300)-*/
			if(trim($res[$i]['auth_no'])!=''){
				$fileData .= "REF*9F*".trim($res[$i]['auth_no'])."~";
			}
			$clm_control_num = clean_n_padd_claim_control_num($clm_control_num);
			if($clm_control_num != false && trim($clm_control_num)!='' && ($clm_type_indicator=='7' || $clm_type_indicator=='8')){
				$fileData .= "REF*F8*".$clm_control_num."~";
				if($primaryFile == true){$insType = 'claim_ctrl_pri';}else{$insType = 'claim_ctrl_sec';}
				update_claim_control_number($insType,$res[$i]['encounter_id'],$clm_control_num);
			}
			
			/**---Claim Identifier for Transmission Intermediaries---**/
			$fileData .= "REF*D9*".$res[$i]['encounter_id']."~";
			
			
			/*$admit_date = $res[$i]['admit_date'];
			if($admit_date != '0000-00-00'){
				$fileData .= 'dtp*435*d8*'.preg_replace("/-/","",$admit_date).'~';
			}
			$disch_date = $res[$i]['disch_date'];
			if($disch_date != '0000-00-00'){
				$fileData .= 'dtp*096*d8*'.preg_replace("/-/","",$disch_date).'~';
			}*/		
			
			
			//--- GET PATIENT PAID AMOUNT ----
			$ins_encounter_id = $res[$i]['encounter_id'];
			$qry = "select patient_charges_detail_payment_info.paidForProc + 
					patient_charges_detail_payment_info.overPayment as paidForProc
					patient_charges_detail_payment_info.paymentClaims
					from patient_charges_detail_payment_info
					join patient_chargesheet_payment_info on patient_chargesheet_payment_info.payment_id
					= patient_charges_detail_payment_info.payment_id
					where patient_chargesheet_payment_info.paid_by = 'Patient'
					and patient_chargesheet_payment_info.encounter_id = '$ins_encounter_id'
					and patient_charges_detail_payment_info.deletePayment = '0'";
			if(empty($not_cov_proc_str) === false){
				$qry .= " and patient_charges_detail_payment_info.charge_list_detail_id not in ($not_cov_proc_str)";
			}
			$piadRs = imw_query($qry);
			$PatientPaidAmountArr = array();
			$piadRes = array();
			while($rs_pt_paid = imw_fetch_assoc($piadRs)){
				$piadRes[] = $rs_pt_paid;
			}

			for($r=0;$r<count($piadRes);$r++){
				$paidForProc = $piadRes[$r]['paidForProc'];
				if($paidForProc[$r]['paymentClaims'] == 'Negative Payment'){
					$paidForProc = '-'.$paidForProc;
				}
				$PatientPaidAmountArr[] = $paidForProc;
			}
			$PatientPaidAmount = array_sum($PatientPaidAmountArr);
			if($PatientPaidAmount>0){
				$fileData .= 'AMT*F5*'.preg_replace("/,/","",number_format($PatientPaidAmount,2)).'~';
			}
			
			$approval = array_unique(array_merge($approval1,$approval2));
			if(trim($referral) != ''){
				if($approval[0] != ''){
					$approvals = join("*",$approval);
					$fileData .= 'REF*G1*'.$referral.'*'.$approvals.'~';	
				}
				$fileData .= 'REF*G1*'.$referral.'~';
			}

			/*loop 2300. CLIA number*/
			$billing_global_clia_num1= $billing_global_clia_num;
			if($billing_global_clia_num=='' && $enc_facility_id > 0){
				$billing_global_clia_num1 = get_CLIA_by_facility_id($enc_facility_id);
			}
			if($CLIA_Procedure && in_array(strtolower($billing_global_server_name), array('revision_eye'))){
				$fileData .= "REF*X4*36D2020283~";	
			}else if($CLIA_Procedure && in_array(strtolower($billing_global_server_name), array('athwal'))){
				$fileData .= "REF*X4*31D2089547~";	
			}else if(isset($billing_global_clia_num1) && $billing_global_clia_num1 != ''){
				$fileData .= "REF*X4*$billing_global_clia_num1~";
			}
			
			//MEDICAL RECORD NUMBER
			if(in_array(strtolower($billing_global_server_name),array('manahan','essi','tyson','waltham','ocean'))){
				$fileData .= "REF*EA*".$ins_encounter_id."~";
			}
			
			//--- ENTER CHARGES NOTES ----
			if($anesthesia_flag){
				$anes_start_time = substr(preg_replace("/:/","",$anes_start_time),0,4);
				$anes_stop_time  = substr(preg_replace("/:/","", $anes_stop_time),0,4);
				$fileData .= "NTE*ADD*".$anes_start_time."-".$anes_stop_time."~";
				//procs here with BP and BO	//$anes_proc_array
				if($enc_icd10==1){
					$fileData .= 'HI*ABK:'.$diagnosisIds.'~';
				}else{
					$fileData .= 'HI*BK:'.$diagnosisIds.'~';
				}
				$arr_anes_procs = array();
				foreach($anes_proc_array as $val_proc){
					$val_proc = trim($val_proc);
					if($val_proc != '' && substr($val_proc,0,1)=='0'){
						$arr_anes_procs[] = $val_proc;
					}
				}
				if(count($arr_anes_procs)>0){
					$str_anes_procs = implode('BO',$arr_anes_procs);
					$fileData .= 'HI*BP:'.$str_anes_procs.'~';
				}
			}else{
				if(count($notesArr) > 0){
					$notes_arr = array();
					if(empty($res[$i]['comment']) === false){
						$notes_arr[] = $res[$i]['comment'];
					}
					$notesArr = array_merge($notes_arr, $notesArr);
					$notesArr = array_unique($notesArr);
					$notesStr = join(",",$notesArr);
					$notesStr = preg_replace("/[^a-zA-Z0-9- _,]/","",substr($notesStr, 0, 78));
					$notesStr = str_replace('*','',$notesStr);
					$fileData .= "NTE*ADD*".strtoupper(trim($notesStr))."~";
				}
				if($enc_icd10==1){
					$fileData .= 'HI*ABK:'.$diagnosisIds.'~';
				}else{
					$fileData .= 'HI*BK:'.$diagnosisIds.'~';
				}	
			}
			
			if(count($value_codes_rates)>0 && in_array(strtolower($billing_global_server_name),array('mackool'))){
				//HI*BE:30:::20~
				$valueCodesRates = array_unique($value_codes_rates);
				$ar_ValueCodesRates = array();
				$last_ValueCode = '';
				foreach($valueCodesRates as $vcr){
					$temp_vcr = preg_split('@/@', $vcr, NULL, PREG_SPLIT_NO_EMPTY);
					if(count($temp_vcr)==2){
						if($last_ValueCode==$temp_vcr[0]) $temp_vcr[0] = '';
						$ar_ValueCodesRates[] = 'BE:'.$temp_vcr[0].':::'.$temp_vcr[1];
						if($temp_vcr[0]!='') $last_ValueCode=$temp_vcr[0];
					}else{
						$temp_vcr = preg_split('@ @', $vcr, NULL, PREG_SPLIT_NO_EMPTY);
						if(count($temp_vcr)==2){							
							if($last_ValueCode==$temp_vcr[0]) $temp_vcr[0] = '';
							$ar_ValueCodesRates[] = 'BE:'.$temp_vcr[0].':::'.$temp_vcr[1];
							if($temp_vcr[0]!='') $last_ValueCode=$temp_vcr[0];
						}
					}					
				}
				if(count($ar_ValueCodesRates)>0){
					$fileData .= 'HI*'.implode('*',$ar_ValueCodesRates).'~';
				}
			}
			
			//-- GET REFERRING PHYSICIAN DETAILS ----------
			if($ref_phy_not_required == '0'){
				$fileData .= 'NM1*DN*1*'.trim($reffPhysicianLname).'*'.trim($reffPhysicianFname).''.trim(substr($reffPhysicianMname,0,1)).'****XX*'.trim(preg_replace("/-/","",$npiNumber)).'~';
	//			$fileData .= 'PRV*RF*ZZ*'.$Texonomy.'~';
			}
			if((trim($groupDetails['group_NPI']) != trim($res[$i]['userNpi']) && ($groupDetails['group_institution']!='1')) || $ClearingHouse['abbr']=='PI'){
				$rendering_provider_entity_type = '1';
				global $FACILITY_BILLING_PROVIDERS;
				if(isset($FACILITY_BILLING_PROVIDERS) && is_array($FACILITY_BILLING_PROVIDERS)){
					if(in_array($res[$i]['usersId'],$FACILITY_BILLING_PROVIDERS)){
						$rendering_provider_entity_type = '2';
					}
				}
				
				$fileData .= 'NM1*82*'.$rendering_provider_entity_type.'*'.trim($res[$i]['usersLname']).'*'.trim($res[$i]['usersFname']).'';
				if(DEFAULT_PRODUCT == 'imwemr'){
					$fileData .= '*'.trim($res[$i]['usersMname']).'***XX*'.trim($res[$i]['userNpi']).'~';
				}
				else{
					$fileData .= '*'.trim($res[$i]['usersMname']).'***XX*'.trim($groupDetails['group_NPI']).'~';
				}
				if(trim($res[$i]['usersTaxonomyId'])!='' && !in_array($medicare_ins_details->Payer_id_pro,$arr_RRM_payers)){
					$fileData .= 'PRV*PE*PXC*'.trim($res[$i]['usersTaxonomyId']).'~';
				}
			}		
		if(strtolower($billing_global_server_name)=='kung' && $otherInsDetail->Payer_id_pro=='12402'){//for kung and hightmark medicare only
			if(strtoupper($posFacilityDetail->facilityPracCode) != 'HOME' && strtoupper($posFacilityId) !=  'HOME'){
				$fileData .= 'NM1*77*2*'.trim($posFacilityDetail->facility_name).'~';
				$pos_facility_address = explode("Suite",$posFacilityDetail->pos_facility_address);
				if(is_array($pos_facility_address) && count($pos_facility_address)>1){
					$facAdd = substr($pos_facility_address[0],0,-2);
				}else{
					$facAdd = trim($posFacilityDetail->pos_facility_address);
				}
				if(count($pos_facility_address)>1){
					$facAdd .= "*Suite".$pos_facility_address[1];
				}
				else{
					$facAdd .= $pos_facility_address[1];
				}
			
				if(trim($posFacilityDetail->pos_facility_address)){
					$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($facAdd));
				}
				if(trim($posFacilityDetail->pos_facility_address2) != ''){
					$fileData .= '*'.preg_replace('/\*/',' ',trim($posFacilityDetail->pos_facility_address2));
				}
				$fileData .= '~';
				$fileData .= 'N4*'.$posFacilityDetail->pos_facility_city.'*';
				$state_name = correct_state_name($posFacilityDetail->pos_facility_state);
				$fileData .= $state_name.'*'.$posFacilityDetail->pos_facility_zip.$posFacilityDetail->zip_ext.'~';
			}
			else{
				$fileData .= 'NM1*77*2*Home~';
				$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($res[$i]['patientStreet']));
				if(trim($res[$i]['patientStreet2'])){
					$fileData .= '*'.preg_replace('/\*/',' ',trim($res[$i]['patientStreet2']));
				}
				$fileData .= '~';
				$fileData .= 'N4*'.$res[$i]['patientCity'].'*';
				$state_name = correct_state_name($res[$i]['patientState']);
				$fileData .= $state_name.'*'.$res[$i]['patientPostal_code'].'~';
			}
		}

			//-- GET PATIENT`S SUBSCRIBER DETAILS -----
			if(is_object($subscriberDetails2) === true){
				if($subscriberDetails2->subscriber_relationship == '' || $subscriberDetails2->subscriber_relationship == 'self'){
					$selfCode2 = 18;
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Son'){
					$selfCode2 = 19;						
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Doughter'){
					$selfCode2 = 19;						
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Mother'){
					$selfCode2 = 'G8';						
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Father'){
					$selfCode2 = 'G8';						
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Guardian'){
					$selfCode2 = 'G8';
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Employee'){
					$selfCode2 = '20';
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Spouse'){
					$selfCode2 = '01';
				}
				else{
					$selfCode2 = 'G8';
				}
				$insCaseQuery2 = imw_query("select insurance_case_types.case_name from insurance_case_types 
								join insurance_case on ins_caseid = '".$subscriberDetails2->ins_caseid."'
								where insurance_case.ins_case_type = insurance_case_types.case_id");
				$RSinsCaseQuery2 = imw_fetch_array($insCaseQuery2);
				$subscriberCaseName2 = $RSinsCaseQuery2['case_name'];
				if($subscriberCaseName2 == 'Workman Comp'){
					$insInstance2 = '15';
				}
				else if($subscriberCaseName2 == 'Auto'){
					$insInstance2 = '14';
				}
				else{
					$MEDICADE2 = explode("MEDICARE",strtoupper($otherInsDetail2->name));
					$MEDICADE2 = explode("MEDICADE",strtoupper($otherInsDetail2->name));
					if(strtoupper($otherInsDetail2->in_house_code) == "MEDICARE"){
						$insInstance2 = '47';
					}
					else if(count($MEDICADE2) > 1){
					//	$insInstance2 = 'mc';
					}
					else{									
						$insInstance2 = '47';
					}					
				}
				$groupNumber2 = trim($subscriberDetails2->group_number);
				$subscriberDetails2->policy_number = trim(preg_replace("/[^A-Za-z0-9]/","",$subscriberDetails2->policy_number));
				$plan_name2 = trim($subscriberDetails2->plan_name);
				if(!in_array(strtolower($billing_global_server_name),array('manahan'))){
					if(strtolower($plan_name2) == 'none'){$plan_name2 = '';}
					if(strtolower($groupNumber2) == 'none'){$groupNumber2 = '';}
				}
				if($plan_name2 == '' && $groupNumber2 != ''){
					$plan_name2 = $groupNumber2;
				}else if($groupNumber2 == '' && $plan_name2 != ''){
					$groupNumber2 = $plan_name2;
				}
				if(in_array($billing_global_server_name,array('farbowitz','brian'))){$groupNumber2='';}
				$fileData .= 'SBR*'.substr($SecondType,0,1).'*'.$selfCode2.'*'.$groupNumber2.'*';
				if(trim(strtolower($groupNumber2))=='none' || trim($groupNumber2)==''){
					$fileData .= $plan_name2;
				}
				
				$secCode = 'CI';
				if($otherInsDetail2->ins_type != '' && strlen(trim($otherInsDetail2->ins_type))==2){
					$secCode = strtoupper(trim($otherInsDetail2->ins_type));
				}
				$fileData .= '*****'.$secCode.'~';
				$provider_id2 = $subscriberDetails2->provider;
				$ins_encounter_id = $res[$i]['encounter_id'];
				
				//--- OTHER INSURANCE COMPANY PAYMENTS DETAILS ---
				if($primaryFile == false){
					$pay_qry = "select patient_charges_detail_payment_info.paidForProc +
							patient_charges_detail_payment_info.overPayment as paidForProc,
							patient_charges_detail_payment_info.paidDate,
							patient_chargesheet_payment_info.paymentClaims, 
							patient_charges_detail_payment_info.charge_list_detail_id, 
							patient_chargesheet_payment_info.insProviderId, 
							patient_chargesheet_payment_info.insCompany 
							from patient_charges_detail_payment_info
							join patient_chargesheet_payment_info on patient_chargesheet_payment_info.payment_id
							= patient_charges_detail_payment_info.payment_id
							where patient_chargesheet_payment_info.insProviderId = '$provider_id2'
							and patient_chargesheet_payment_info.paid_by = 'Insurance'
							and patient_chargesheet_payment_info.encounter_id = '$ins_encounter_id'							
							and patient_charges_detail_payment_info.deletePayment = '0'";
					if(empty($not_cov_proc_str) === false){
						$pay_qry .= " and patient_charges_detail_payment_info.charge_list_detail_id 
							not in ($not_cov_proc_str)";
					}

					$piadRs = imw_query($pay_qry);
					$insPaidAmountArr = array();
					$priInsPaidAmountByCLDidArr = array();
					$piadRes = array();
					while($rs_pt_paid = imw_fetch_assoc($piadRs)){
						$piadRes[] = $rs_pt_paid;
					}
					for($r=0;$r<count($piadRes);$r++){
						$paidForProc = $piadRes[$r]['paidForProc'];
						$paidForProc_CLDid = $piadRes[$r]['charge_list_detail_id'];
						$paidForProc_INSid = $piadRes[$r]['insProviderId'];
						$paidForProc_INStype = $piadRes[$r]['insCompany'];
						if($piadRes[$r]['paymentClaims'] == 'Negative Payment'){
							$paidForProc = '-'.$paidForProc;
						}
						$priInsPaidAmountByCLDidArr[$paidForProc_CLDid][$paidForProc_INStype][$paidForProc_INSid][] = $paidForProc;
						$insPaidAmountArr[] = $paidForProc;
						
						$insPaidDate = $piadRes[$r]['paidDate'];
					}
					$insPaidAmount = array_sum($insPaidAmountArr);					
					
					//--- GET WRITE OFF AMOUNT ----
					$writeOffQry = "select write_off_amount , CAS_type, CAS_code, 
									write_off_date, charge_list_detail_id, era_amt from paymentswriteoff 
									where write_off_by_id = '$provider_id2' 
									and encounter_id = '$ins_encounter_id' and delStatus = '0'";
					if(empty($not_cov_proc_str) === false){
						$writeOffQry .= " and charge_list_detail_id not in ($not_cov_proc_str)";
					}
					
					$writeOffQryRs = imw_query($writeOffQry);
					$writeOffQryRes = array();
					while($write_off_rs = imw_fetch_assoc($writeOffQryRs)){
						$writeOffQryRes[] = $write_off_rs;
					}
					
					$other_payment_detail_arr = array();
					$arr_cas_write_off = array();
					for($w=0;$w<count($writeOffQryRes);$w++){
						//$insPaidAmountArr[] = $writeOffQryRes[$w]['write_off_amount'];
						$charge_list_detail_id = $writeOffQryRes[$w]['charge_list_detail_id'];
						$arr_cas_write_off[$charge_list_detail_id][] = $writeOffQryRes[$w]['write_off_amount'];
						$other_payment_detail_arr[$charge_list_detail_id][] = $writeOffQryRes[$w]['write_off_amount'];
						$writeOffDate = $writeOffQryRes[$w]['write_off_date'];
					}
					if(empty($insPaidDate) == true && $writeOffDate!='' && $writeOffDate!='0000-00-00'){
						$insPaidDate = $writeOffDate;
					}
					
					//--- DEDUCT AMOUNT DETAILS ---
					$detailIdStr = join(",", $detailIdArr);
					$deductible_amount_arr = array();
					$deduct_qry = "select deduct_amount AS write_off_amount, charge_list_detail_id, 
								date_format(deduct_date, '%Y%m%d') as write_off_date 
								from payment_deductible where deduct_ins_id = '$provider_id2' 
								and charge_list_detail_id in($detailIdStr) and delete_deduct = '0'";
					if(empty($not_cov_proc_str) === false){
						$deduct_qry .= " and charge_list_detail_id not in ($not_cov_proc_str)";
					}
					$deductQryRs = imw_query($deduct_qry);
					while($deductQryRes = imw_fetch_assoc($deductQryRs)){
						$charge_list_detail_id = $deductQryRes['charge_list_detail_id'];
						$deductible_amount_arr[$charge_list_detail_id][] = $deductQryRes['write_off_amount'];
						$deductDate = $deductQryRes['write_off_date'];
					}
					if(empty($insPaidDate) == true && $deductDate!='' && $deductDate != '0000-00-00'){
						$insPaidDate = $deductDate;
					}
					
					$insPaidAmount = array_sum($insPaidAmountArr);
					$fileData .= 'AMT*D*'.preg_replace("/,/","",number_format($insPaidAmount,2)).'~';
					
					//--- TOTAL APPROVED AMOUNT ---
					$totalAmt = preg_replace('/,/','',$totalAmt) - $writeOffAmtTotal;
					$totalAmt = preg_replace('/,/','',number_format($totalAmt,2));					
					
					//$fileData .= 'AMT*B6*'.$totalAmt.'~';
				}
				
				$DOB2 = preg_replace("/-/","",$subscriberDetails2->subscriber_DOB);							
				$subscriber_sex2 = $subscriberDetails2->subscriber_sex;
			//	$fileData .= 'DMG*D8*'.$DOB2.'*'.substr($subscriber_sex2,0,1).'~';
				$fileData .= 'OI***Y***Y~';
				if($subscriberDetails2->subscriber_mname != ''){
					$patientMname2 = substr($subscriberDetails2->subscriber_mname,0,1);
				}
				else{
					$patientMname2 = $res[$i]['patientMname'];
				}
				$patient_policy_number2 = (string)$subscriberDetails2->policy_number;
				$fileData .= 'NM1*IL*1*'.trim($subscriberDetails2->subscriber_lname).'*'.trim($subscriberDetails2->subscriber_fname);
				$fileData .= '*'.trim($patientMname2).'***MI*'.trim($patient_policy_number2).'~';
				if(trim($subscriberDetails2->subscriber_street)){
					$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($subscriberDetails2->subscriber_street)).'~';
					$fileData .= 'N4*'.trim($subscriberDetails2->subscriber_city).'*';					
				}
				else{
					$fileData .= 'N3*'.trim($subscriberDetails2->subscriber_city).'~';
					$fileData .= 'N4*'.trim($subscriberDetails2->subscriber_city).'*';					
				}
				$state_name = correct_state_name($subscriberDetails2->subscriber_state);
				$fileData .= $state_name.'*'.trim($subscriberDetails2->subscriber_postal_code.$subscriberDetails2->zip_ext).'~';
					
				//--- INSURANCE COMPANY NAME SHOULD BE LESS THAN 35 CHR ---
				$ins_new_name2 = trim($otherInsDetail2->name);
				$ins_new_name2 = substr($ins_new_name2,0,34);			
				$fileData .= 'NM1*PR*2*'.trim($ins_new_name2).'*****PI*'.$otherInsDetail2->Payer_id_pro.'~';
				if(trim($otherInsDetail2->contact_address) != ''){
					$fileData .= "N3*".trim(substr(trim($otherInsDetail2->contact_address),0,55))."~";
				}else{
					$fileData .= "N3*".trim($otherInsDetail2->City)."~";
				}
				$fileData .= "N4*".trim($otherInsDetail2->City)."*";
				$state_name2 = correct_state_name($otherInsDetail2->State);
				$insZip2 = preg_replace("/-/","",trim($otherInsDetail2->Zip.$otherInsDetail2->zip_ext));
				$fileData .= $state_name2."*".$insZip2."~";
				if(empty($insPaidDate) === false){
					$insPaidDate = preg_replace('/-/','',$insPaidDate);
					if(!in_array(strtolower($billing_global_server_name), array('patel','niec','manahan','crandalleye','keystone','gewirtz','brian','tyson'))){
						$fileData .= "DTP*573*D8*$insPaidDate~";
					}
				}
			}
			//-- SET PATIENT CHARGE DETAIL DATA ------
			$units = '';					
			$modifierId = array();
			$procCode = array();
			$modifier = array();
			for($u=0;$u<count($patientChargeDetails);$u++){				
				$modifierIdArr = array();
				$validChargeListDetailsId[] = $patientChargeDetails[$u]['charge_list_detail_id'];
				$fileData .= 'LX*'.($u+1).'~';
				
				//--- GET DX CODE POINTER -----
				$dxPointerArr = array();
				for($z=1; $z<=12; $z++){
					if(empty($patientChargeDetails[$u]['diagnosis_id'.$z]) === false){
						$dxPointerArr[] = preg_replace("/\./","",$patientChargeDetails[$u]['diagnosis_id'.$z]);
					}
				}
				
				$dxPointerKeyArr = array();
				$diagnosisIdPointArr = array_values($diagnosisId);
				foreach($dxPointerArr as $dxPointerVal){
					$dxPointerKey = array_search($dxPointerVal, $diagnosisIdPointArr);
					$dxPointerKeyArr[] = $dxPointerKey + 1;
				}
				$dxPointerKeyArr = array_slice($dxPointerKeyArr,0,4);
				$dxPointerKey = join(':', $dxPointerKeyArr);
				
				//--- GET ALL MODIFIER DETAILS ----
				if(empty($patientChargeDetails[$u]['modifier_id1']) === false){
					$modifierIdArr[] = $patientChargeDetails[$u]['modifier_id1'];
				}
				if(empty($patientChargeDetails[$u]['modifier_id2']) === false){
					$modifierIdArr[] = $patientChargeDetails[$u]['modifier_id2'];
				}
				if(empty($patientChargeDetails[$u]['modifier_id3']) === false){
					$modifierIdArr[] = $patientChargeDetails[$u]['modifier_id3'];
				}
				if(empty($patientChargeDetails[$u]['modifier_id4']) === false){
					$modifierIdArr[] = $patientChargeDetails[$u]['modifier_id4'];
				}
				
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

				$jcode_cpt_desc = '';$jcode_colons_str = '';
				$proc_acc_notes = trim(preg_replace("/[^A-Za-z0-9 ]/", "", $patientChargeDetails[$u]['notes']));
				$proc_admin_desc = trim(preg_replace("/[^A-Za-z0-9 ]/", "", $patientChargeDetails[$u]['cpt_desc']));
				
				$temp_cpt_comments_ar = explode(' ',trim($patientChargeDetails[$u]['cpt_comments']));
				if(strpos(trim($temp_cpt_comments_ar[0]),'/')>0){
					$temp_cpt_comments_ar = explode('/',trim($patientChargeDetails[$u]['cpt_comments']));
				}
				$proc_admin_cmnt = trim(preg_replace("/[^A-Za-z0-9 ]/", "", trim($temp_cpt_comments_ar[0])));
				$proc_admin_UoM = trim($patientChargeDetails[$u]['unit_of_measure']);
				$proc_admin_U = trim($patientChargeDetails[$u]['measurement']);
								
				$jcode_colons = 4-$mod_count;
				$jcode_colons_str = str_repeat(":",$jcode_colons);
				$jcode_cpt_desc = $jcode_colons_str.':'.$proc_acc_notes;
				$firstCharOfCPT = strtolower(substr($patientChargeDetails[$u]['cpt4_code'],0,1));
				if(in_array($firstCharOfCPT,array('j','l')) && $proc_admin_desc=='' && $proc_acc_notes!=''){
					$proc_admin_desc = $proc_acc_notes;
				}
				if($this->is_not_otherwise_classified_code($patientChargeDetails[$u]['cpt4_code']) && in_array($firstCharOfCPT,array('j','l','6')) && $proc_admin_desc!=''){
					$jcode_cpt_desc = $jcode_colons_str.':'.$proc_admin_desc;
				}
				/*if($proc_acc_notes=='' && in_array($firstCharOfCPT,array('j','l'))){
					$jcode_cpt_desc = '';
				}*/
			
				//procedure charges
				$procCharge = $patientChargeDetails[$u]['totalAmount'];
				if($batchFileStatus == true){
					if(strlen($procCharge) == 4){
						$procCharge = str_replace('0.','.',$procCharge);
					}
				}
				if(!$this->is_not_otherwise_classified_code($patientChargeDetails[$u]['cpt4_code'])){$jcode_cpt_desc = '';$proc_admin_cmnt ='';}
				
				$fileData .= 'SV1*HC:'.$patientChargeDetails[$u]['cpt4_code'].$modifierCodes.$jcode_cpt_desc.'*'.$procCharge;
				$proc_first_letter 	= substr($patientChargeDetails[$u]['cpt4_code'],0,1);
				$proc_last_letter 	= substr($patientChargeDetails[$u]['cpt4_code'],-1);
				$proc_units = $patientChargeDetails[$u]['units'];
				if(substr($proc_units,-3)=='.00'){$proc_units = intval($proc_units);}
				if($anesthesia_flag && $proc_first_letter=='0' && strtoupper($proc_last_letter) != 'T' && !in_array(trim($patientChargeDetails[$u]['cpt4_code']),array('01995,01996'))){
					$acc_anes_time = $res[$i]['acc_anes_time'];
					$fileData .= '*MJ*'.$acc_anes_time.'*'.$posCodeDetails->pos_code.'**'.$dxPointerKey.'~';
				}else{
					$fileData .= '*UN*'.$proc_units.'*'.$posCodeDetails->pos_code.'**'.$dxPointerKey.'~';	
				}
				$fileData .= 'DTP*472*D8*'.preg_replace("/-/","",$res[$i]['date_of_service']).'~';	
				$PRACTICE_IDENTIFIER = "";
				global $billing_global_practice_dentifier;
				if($billing_global_practice_dentifier && $billing_global_practice_dentifier!=''){
					$PRACTICE_IDENTIFIER = "".$billing_global_practice_dentifier;
				}
				$fileData .= "REF*6R*".$res[$i]['encounter_id']."mcr".$patientChargeDetails[$u]['charge_list_detail_id'].$billing_global_tsuc_separator."TSUC".$billing_global_tsuc_separator.$Transaction_set_unique_control.$PRACTICE_IDENTIFIER."~";
				
				/*-----CLEAR VUE requirement------*/
				$LIN_segment = '';
				if(in_array($firstCharOfCPT,array('j','l','6')) && $proc_admin_cmnt != '' && $otherInsDetail->transmit_ndc=='1'){
					$CTU_UoM	= 'UN';
					$CTP_U		= $proc_units;
					if(!empty($proc_admin_UoM) && !empty($proc_admin_U)){
						$CTU_UoM	= $proc_admin_UoM;
						$CTP_U		= $proc_admin_U;
					}
					
					$proc_admin_cmnt = substr($proc_admin_cmnt,0,11);
					$LIN_segment .= 'LIN**N4*'.$proc_admin_cmnt.'~';
					$LIN_segment .= 'CTP****'.$CTP_U.'*'.$CTU_UoM.'~';
				}
				$fileData .= $LIN_segment;
				/*******LIN SEGMENT END-----------*/
				
				//--- CHARGE LIST DETAIL ID FOR PAYMENT DETAILS OF PRIMARY INSURANCE ----
				if($primaryFile == false){
					$charge_list_detail_id = $patientChargeDetails[$u]['charge_list_detail_id'];				
					$otherPaymentDetailArr = $other_payment_detail_arr[$charge_list_detail_id];
					$CAS_codes_arr = getCAScodes($charge_list_detail_id,$provider_id2);
					//pre($CAS_codes_arr);
					$WriteOffAmt = $DeniedAmt = 0;
					unset($deniedCAStype);unset($deniedCAScode);unset($deniedCASamt);unset($deniedDate);
					foreach($CAS_codes_arr as $chld_id=>$CAS_arr){
						foreach($CAS_arr as $casARR){
							if($casARR['type']=='denied'){
								$deniedCAStype = $casARR['CAS_type'];
								$deniedCAScode = $casARR['CAS_code'];
								$deniedCASamt  = $casARR['amount'];
								$deniedDate    = $casARR['date'];
							}
							
						}
					}
					
					//PROCEDURE CHARGES
					$billed_amount = $procCharge;
					
					//ALLOWED AMOUNT
					$allowedForProc = $patientChargeDetails[$u]['approvedAmt'];
					$allowedForProc = preg_replace('/,/','',number_format($allowedForProc,2));
					
					//PAID AMOUNT
					$paidForProc = preg_replace('/,/','',$patientChargeDetails[$u]['paidForProc']);
					$paidForProc += preg_replace('/,/','',$patientChargeDetails[$u]['overPaymentForProc']);
					$paidForProc = preg_replace('/,/','',number_format($paidForProc,2));
					
					//PAID BY INSURANCE ONLY
					$paidForProcByPriIns = array_sum($priInsPaidAmountByCLDidArr[$charge_list_detail_id]['1'][$provider_id2]);
					$paidForProcByPriIns = preg_replace('/,/','',number_format($paidForProcByPriIns,2));
					
					//CO-PAYMENT AMOUNT (not calculated yet).
						
					
					//writeOff for procedure
					$writeoffForProc2 	= array_sum($arr_cas_write_off[$charge_list_detail_id]);
					$AdjustmentAmount 	= ($procCharge - $allowedForProc)+$writeoffForProc2;

					//deductible for procedure
					$deductible4Proc 	= array_sum($deductible_amount_arr[$charge_list_detail_id]);

					//balance for procedure
					$balForProc = ((($procCharge - $AdjustmentAmount)-$deductible4Proc) - $paidForProcByPriIns)-number_format($deniedCASamt,2);
					$balForProc = preg_replace('/,/','',number_format($balForProc,2));
					
					$cas_codes_string = $this->get_835_cas_codes($charge_list_detail_id);
					
					if($insPaidAmount >= 0 && ($paidForProcByPriIns >= 0 or count($otherPaymentDetailArr)>0)){
						if($paidForProcByPriIns >= 0){
							$fileData .= 'SVD*'.$otherInsDetail2->Payer_id_pro.'*'.$paidForProcByPriIns.'*';
							$fileData .= 'HC:'.$patientChargeDetails[$u]['cpt4_code'].''.$modifierCodes.'**1~';
						}	

						if($cas_codes_string){
							$fileData .= $cas_codes_string;
						}else if(($AdjustmentAmount != '' && $AdjustmentAmount >0) || ($deductible4Proc != '' && $deductible4Proc > 0) || ($balForProc != '' && $balForProc > 0) || ($deniedCASamt !='' && $deniedCASamt >0)){

							if($AdjustmentAmount != '' && $AdjustmentAmount >0){//Charges exceed our fee schedule or maximum allowab...
								$fileData .= "CAS*CO*45*".preg_replace('/,/','',number_format($AdjustmentAmount,2))."*1~";
							}
							$CAS_codes = '';
							if(($deductible4Proc != '' && $deductible4Proc > 0) || ($balForProc != '' && $balForProc > 0) || ($deniedCASamt !='' && $deniedCASamt >0)){
								$CAS_codes = 'CAS*PR';
								if($deductible4Proc != '' && $deductible4Proc > 0){//deductible available for this proc.
									$CAS_codes .= "*1*".preg_replace('/,/','',number_format($deductible4Proc,2))."*1";
								}
								if($balForProc != '' && $balForProc > 0){
									$CAS_codes .= "*2*".preg_replace('/,/','',number_format($balForProc,2))."*1";
								}
								if($deniedCASamt != '' && $deniedCASamt > 0){
									$CAS_codes .= "*".$deniedCAScode."*".preg_replace('/,/','',number_format($deniedCASamt,2))."*1";	
								}
								if(strlen($CAS_codes)>6){
									$fileData .= $CAS_codes."~";
								}
							}
						}
						
						if($paidForProcByPriIns>=0 || $AdjustmentAmount>0){
							if(empty($insPaidDate) === true || $insPaidDate=='00000000' || $insPaidDate=='0000-00-00'){
								$insPaidDate = $patientChargeDetails[$u]['write_off_date'];
							}
							$insPaidDate = preg_replace('/-/','',$insPaidDate);
							if(strlen($insPaidDate)==8){
								$fileData .= "DTP*573*D8*$insPaidDate~";
							}
						}
					}
				}
			}

			$validChargeListId[] = $res[$i]['charge_list_id'];
			$segmentEnd = preg_split('/~/',$fileData);
			$segmentCountstart[] = $segmentStart;
			$segmentCountEnd[] = count($segmentEnd);
			$segmentStart = count($segmentEnd) +1;									
		}
		else{					
			$invalidChargeListId[] = $res[$i]['charge_list_id'];					
		}
		$i++;
	}

	if($files == true and empty($createClaims) === false){
		$transectionCount++;
		//---- INSERT CLAIM FILE DATA -----------
		$encounterId = join(",",$encounter_id);
		$pcld_ids = join(",",array_unique($validChargeListDetailsId));
		$segmentCountArr = preg_split('/~/',$fileData);
		$fileData .= 'SE*'.(count($segmentCountArr)-2).'*'.$Transaction_set_unique_control.'~';
		$fileData .= 'GE*'.$transectionCount.'*'.$header_control_identifier.'~';
		$fileData .= 'IEA*1*'.$InterchangeControlNumber.'~';
		$insCompanyIds = join(",",array_unique($insCompanyId));unset($insCompanyId);
		$operatorIds = join(",",$operatorId);
		$segmentCountstarts = join(',',$segmentCountstart);
		$segmentCountEnds = join(',',$segmentCountEnd);
		$fileData = preg_replace('/[^a-zA-Z0-9_*\- ~.:|\']/','',$fileData);
		
		$record_data_arr = array();
		$record_data_arr['header_control_identifier'] = $header_control_identifier;
		$record_data_arr['Transaction_set_unique_control'] = $Transaction_set_unique_control;
		$record_data_arr['file_name'] = $fileName;
		$record_data_arr['status'] = 0;
		$record_data_arr['create_date'] = date('Y-m-d');
		$record_data_arr['ins_company_id'] = $insCompanyIds;
		$record_data_arr['file_data'] = strtoupper(addslashes($fileData));
		$record_data_arr['encounter_id'] = $encounterId;
		$record_data_arr['pcld_id'] = $pcld_ids;
		$record_data_arr['clm_type_indicator'] = json_encode($CHLS_Type_Status);
		$record_data_arr['ins_comp'] = $type;
		$record_data_arr['submitter_id'] = $submitterId;
		$record_data_arr['reciever_id'] = $recieverId;
		$record_data_arr['default_facility_id'] = $facilityDetails->id;
		$record_data_arr['sender_id'] = $_SESSION['authId'];
		$record_data_arr['group_id'] = $groupDetails['gro_id'];
		$record_data_arr['production_code'] = $ProductionFile;
		$record_data_arr['Interchange_control'] = $InterchangeControlNumber;
		$record_data_arr['operatorId'] = $operatorIds;
		$record_data_arr['segment_start'] = $segmentCountstarts;
		$record_data_arr['segment_end'] = $segmentCountEnds;
		$record_data_arr['file_format'] = '5010';
		$record_data_arr['clearing_house'] = 'visionshare';
		$insert_data = AddRecords($record_data_arr,'batch_file_submitte');
		if($insert_data > 0){
			batch_file_log($insert_data,"created");
			$getData = true;
		}
		
		//---- INSERT FILE NAME COUNT -----------
		$record = array("submit_date"=>date("Y-m-d"),"file_status"=>"1");		
		$insertData = AddRecords($record,'batch_file_detail');
		file_put_contents('../batchfiles/'.$fileName.'',strtoupper($fileData));
	}								
}
?>