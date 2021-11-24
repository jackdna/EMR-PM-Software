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
File: ghn_electronic_file_i_5010.php
Purpose: Create commercial 837 batch file (Institutional)
Access Type: Include File  
*/
$files = false;
$operatorId = array();
//--- START CLAIM FILE DATA ----
if($createClaims != '' && $chk_institution == $gro_id){
	$insertData = $this->count_today_batch_files();	
	
	//---- GET UNIQUE HEADER CONTROL IDENTIFIER AND UNIQUE TRANSACTION NUMBER --------
	$batch_unique_headers = $this->get_unique_headers();
	$InterchangeControlNumber 		= $batch_unique_headers['interchange_control_num'];
	$Transaction_set_unique_control = $batch_unique_headers['transaction_set_unique_control'];
	$header_control_identifier 		= $batch_unique_headers['header_control_identifier'];
}

//--- CLAIM FILE HEADER ---
$fileData = 'ISA*00*          *00*          *ZZ*'.$submitterId.''.$submitterSpaceStr.'*ZZ*'.$recieverId.''.$recieveSpaceStr.'*'.date('ymd').'*'.date('hm').'*^*00501*'.$InterchangeControlNumber.'*1*'.$ProductionFile.'*:~';
$fileData .= 'GS*HC*'.$submitterId.'*'.$recieverId.'*'.date('Ymd').'*'.date('hm').'*'.$header_control_identifier.'*X*005010X223A2~';
$fileData .= 'ST*837*'.$Transaction_set_unique_control.'*005010X223A2~';
$fileData .= 'BHT*0019*00*'.$InterchangeControlNumber.'*'.date('Ymd').'*'.date('hm').'*CH~';
//$fileData .= 'REF*87*004010X096A1~'; /*removed in 5010*/

//--- Start 1000A SUBMITTER
$fileData .= 'NM1*41*2*'.substr($groupDetails['name'],0,50).'*****46*'.$submitterId.'~';	
if($facilityDetails->billing_location == 1){
	$facilityDetails->phone = preg_replace("/-/","",$facilityDetails->phone);
	$fileData .= 'PER*IC*'.$facilityDetails->billing_attention.'*TE*'.preg_replace("/ /","",$facilityDetails->phone).'~';
}
else{
	$groupDetails['group_Telephone'] = preg_replace("/-/","",$groupDetails['group_Telephone']);
	$fileData .= 'PER*IC*'.$groupDetails['Contact_Name'].'*TE*'.preg_replace("/ /","",$groupDetails['group_Telephone']).'~';
}	
//End 1000A SUBMITTER
//Start 1000B RECEIVER
$fileData .= 'NM1*40*2*'.$BatchFile.'*****46*'.$recieverId.'~';
//End 1000B RECEIVER
//Start 2000A BILLING/PAY-TO PROVIDER HL LOOP
$subscriberId = 1;
$fileData .= 'HL*'.$subscriberId.'**20*1~';
//End 2000A BILLING/PAY-TO PROVIDER HL LOOP

//Start 2010AA BILLING PROVIDER
$fileData .= 'NM1*85*2*'.substr($groupDetails['name'],0,40).'*****XX*'.$groupDetails['group_NPI'].'~';
if($facilityDetails->billing_location == 1){
	if(trim($facilityDetails->street)){
		$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($facilityDetails->street)).'~';	
		$fileData .= 'N4*'.$facilityDetails->city.'*';
	}
	else{
		$fileData .= 'N3*'.$facilityDetails->city.'~';
		$fileData .= 'N4*'.$facilityDetails->city.'*';
	}
	$state_name = correct_state_name($facilityDetails->state);
	$fileData .= $state_name.'*'.$facilityDetails->postal_code.$facilityDetails->zip_ext.'~';
}
else{
	$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($groupDetails['group_Address1'])).'~';	
	$fileData .= 'N4*'.$groupDetails['group_City'].'*';
	$state_name = correct_state_name($groupDetails['group_State']);
	$fileData .= $state_name.'*'.$groupDetails['group_Zip'].$groupDetails['zip_ext'].'~';
}
$fileData .= 'REF*EI*'.preg_replace("/-/","",$groupDetails['group_Federal_EIN']).'~';
$refSubmitterid = substr(trim($groupDetails['site_id']),0,4);
//$fileData .= "REF*G2*$refSubmitterid~";

//--- GET ALL MODIFIERS ----
$modifierCodeArr = $this->get_all_modifiers();

//--- NOT COVERED AND SELF PAY PROCEDURE -----
$cptQry = "SELECT pcld.charge_list_detail_id, pcld.charge_list_id from patient_charge_list_details pcld 
			JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode 
			WHERE pcld.del_status='0' AND pcld.charge_list_id IN($main_charge_list_id) 
			AND (pcld.posted_status='0' OR pcld.claim_status='1' OR 
			cft.not_covered = '1' OR pcld.proc_selfpay = '1')";
$cptQryRes = imw_query($cptQry);
$notCoverProDetailIdArr = array();
while($cptQryRs = imw_fetch_assoc($cptQryRes)){
	$charge_list_id = $cptQryRs['charge_list_id'];
	$notCoverProDetailIdArr[$charge_list_id][] = $cptQryRs['charge_list_detail_id'];
}

//Start 2010AA BILLING PROVIDER

//------ MAIN CHARGE LIST QUERY ----------
$chrgs_qry = "select patient_charge_list.*, patient_data.DOB AS patientDOB,
		patient_data.fname AS patientFname, patient_data.lname AS patientLname,
		patient_data.suffix AS patientSuffix, 
		substring(patient_data.mname,1,1) AS patientMname,
		patient_data.street AS patientStreet, patient_data.street2 AS patientStreet2,
		CONCAT(patient_data.postal_code,patient_data.zip_ext) AS patientPostal_code, patient_data.city AS patientCity, 
		patient_data.state AS patientState, patient_data.sex AS patientSex,
		patient_data.id AS patientId, patient_data.default_facility AS patientDefaultFacility,
		patient_data.primary_care_id AS patientPrimaryCare,
		patient_data.providerID AS patientProviderID,
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
		users.user_npi AS userNpi, users.BlueShieldId AS BlueShieldIds,
		trim(users.fname) AS usersFname, substring(trim(users.mname),1,1) AS usersMname,
		trim(users.lname) AS usersLname, users.id AS usersId,
		users.TaxonomyId AS usersTaxonomyId, users.TaxId AS usersTaxId,
		users.default_group AS users_default_group, users.user_npi AS users_npi,
		users.federaltaxid AS usersFederaltaxid,
		patient_charge_list.admit_date, 
		patient_charge_list.disch_date 
		from patient_charge_list 
		left join insurance_companies
		on patient_charge_list.$InsComp = insurance_companies.id
		left join users on patient_charge_list.primaryProviderId = users.id
		left join patient_data on patient_charge_list.patient_id = patient_data.id				
		left join patient_charge_list_details 
		on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
		where patient_charge_list_details.del_status='0' and patient_charge_list.submitted = 'true'
		and patient_charge_list.charge_list_id in($main_charge_list_id) 
		AND patient_charge_list_details.posted_status='1' AND patient_charge_list_details.claim_status='0' 
		and patient_charge_list.$setField = '0' and patient_charge_list.gro_id= '$gro_id'
		and patient_charge_list_details.differ_insurance_bill != 'true'
		and patient_charge_list_details.proc_selfpay != '1'
		and patient_charge_list.totalAmt > '0'
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
		$ins_caseid = $res[$i]['case_type_id'];
		$charge_list_id = $res[$i]['charge_list_id'];
		$referral = $res[$i]['referral'];
		$enc_icd10= intval($res[$i]['enc_icd10']);
		
		//--- GET PATIENT NAME DETAILS ----
		$patient_name_arr = array();
		$patient_name_arr["LAST_NAME"] = $res[$i]['patientLname'];
		$patient_name_arr["FIRST_NAME"] = $res[$i]['patientFname'];
		$patient_name_arr["MIDDLE_NAME"] = substr($res[$i]['patientMname'],0,1);
		$patient_name = changeNameFormat($patient_name_arr);
		
		//--- NOT COVERED OR SELF PAY PROCEDURE FOR SINGLE ENCOUNTER ---
		$not_cov_proc_arr = array();
		$not_cov_proc_arr = $notCoverProDetailIdArr[$charge_list_id];
		$not_cov_proc_str = '';
		if(count($not_cov_proc_arr) > 0){
			$not_cov_proc_str = join(",", $not_cov_proc_arr);
		}
		
		//-- GET PATIENT CHARGE LIST DETAILS -----
		$pcld_q = "select patient_charge_list_details.*, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_desc 
				from patient_charge_list_details 
				left join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode				
				where patient_charge_list_details.del_status='0' and patient_charge_list_details.charge_list_id = '$charge_list_id' 
				AND patient_charge_list_details.posted_status='1' AND patient_charge_list_details.claim_status='0' ";
		if(empty($not_cov_proc_str) === false){
			$pcld_q .= " and patient_charge_list_details.charge_list_detail_id not in ($not_cov_proc_str)";
		}
				
		$pcld_res	= imw_query($pcld_q);
		$patientChargeDetails = array();
		while($pcld_rs = imw_fetch_assoc($pcld_res)){
			$patientChargeDetails[] = $pcld_rs;
		}
		
		$diagnosis_id = array();
		$onset_date = array();
		$approval1 = array();
		$approval2 = array();		
		$paidProcAmount = 0;
		$totalAmt = 0;
		$writeOffAmtArr = $detailIdArr = array();
		for($u=0;$u<count($patientChargeDetails);$u++){
			$detailIdArr[] = $patientChargeDetails[$u]['charge_list_detail_id'];
			//--- INVALID CLAIM IF PROCEDURE CODE MISSING ----
			if(empty($patientChargeDetails[$u]['procCode']) === true){
				$error[$res[$i]['charge_list_id']] = 'Procedure code missing for encounter-ID.'.$res[$i]['encounter_id'];
				$invalidClaim = true;
			}

			if(trim($patientChargeDetails[$u]['cpt4_code']) == 'G8447'){
				$error[$res[$i]['charge_list_id']] = 'Procedure code G8447 is not valid';
				$invalidClaim = true;
			}
			
			$notes = $patientChargeDetails[$u]['notes'];
			if($notes != 'NOTES...' and empty($notes) == false){
				$notesArr[] = $notes;
			}
			
			$writeOffAmtArr[] = $patientChargeDetails[$u]['write_off'];
			
			//$tot_proc_amt = $patientChargeDetails[$u]['procCharges']*$patientChargeDetails[$u]['units'];
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
		
		$posCodeDetails = (object)getRecords('pos_tbl','pos_id',$place_of_service);
		if(empty($posCodeDetails)){
			$error[$res[$i]['charge_list_id']] = 'POS Required For '.trim($patient_name).' ('.$patientId.')';
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
		$date_of_service = $res[$i]['date_of_service'];
		$subscriberDetails = (object)$this->get_patient_insurance($ins_caseid,$patientId,$type,$date_of_service);					
		$otherInsDetail = (object)$this->getInsCompDetails($subscriberDetails->provider,'nocheck');
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
		if(trim($otherInsDetail->institutional_type)=='INST_ONLY'){
			if(trim($otherInsDetail->Payer_id) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Payer Id (Institutional) is Required.";
				$invalidClaim = true;
			}
			else if(strlen(trim($otherInsDetail->Payer_id)) < 3 && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Payer Id (Institutional) minimum length violation.";
				$invalidClaim = true;
			}
			$provider1_payerId = trim($otherInsDetail->Payer_id);
		}else{
			if(trim($otherInsDetail->Payer_id_pro) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Payer Id (Professional) is Required.";
				$invalidClaim = true;
			}
			else if(strlen(trim($otherInsDetail->Payer_id_pro)) < 3 && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Payer Id (Professional) minimum length violation.";
				$invalidClaim = true;
			}
			$provider1_payerId = trim($otherInsDetail->Payer_id_pro);
		}
		if($provider1_payerId == 'SPRNT' && $invalidClaim == false){
			if($otherInsDetail->contact_address == '' || $otherInsDetail->City == '' || $otherInsDetail->State == '' || $otherInsDetail->Zip ==''){
				$error[$res[$i]['charge_list_id']] = '$type Insurance Carrier Address information is required.';
				$invalidClaim = true;
			}
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
		if($reffDetail == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = 'Referring Physician is Required.';
			$invalidClaim = true;
		}
		if(trim($npiNumber) == '' && $invalidClaim == false){
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
		if($subscriberDetails->subscriber_street == '0'){ 
			$subscriberDetails->subscriber_street = '';
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
			
			if(trim($otherInsDetail2->institutional_type)=='INST_ONLY'){
				if(trim($otherInsDetail2->Payer_id) == '' && $invalidClaim == false){
					$error[$res[$i]['charge_list_id']] = "$SecondType Insurance Carrier Payer Id (Institutional) is Required.";
					$invalidClaim = true;
				}
				else if(strlen(trim($otherInsDetail2->Payer_id)) < 3 && $invalidClaim == false){
					$error[$res[$i]['charge_list_id']] = "$SecondType Insurance Carrier Payer Id (Institutional) minimum length violation.";
					$invalidClaim = true;
				}
				$provider2_payerId = trim($otherInsDetail2->Payer_id);
			}else{
				if(trim($otherInsDetail2->Payer_id_pro) == '' && $invalidClaim == false){
					$error[$res[$i]['charge_list_id']] = "$SecondType Insurance Carrier Payer Id (Professional) is Required.";
					$invalidClaim = true;
				}
				else if(strlen(trim($otherInsDetail2->Payer_id_pro)) < 3 && $invalidClaim == false){
					$error[$res[$i]['charge_list_id']] = "$SecondType Insurance Carrier Payer Id (Professional) minimum length violation.";
					$invalidClaim = true;
				}
				$provider2_payerId = trim($otherInsDetail2->Payer_id_pro);
			}
			if($provider2_payerId == 'SPRNT' && $invalidClaim == false){
				if($otherInsDetail2->contact_address == '' || $otherInsDetail2->City == '' || $otherInsDetail2->State == '' || $otherInsDetail2->Zip ==''){
					$error[$res[$i]['charge_list_id']] = '$SecondType Insurance Carrier Address information is required.';
					$invalidClaim = true;
				}
			}
			if($subscriberDetails2->subscriber_street == '0') 
				$subscriberDetails2->subscriber_street = '';
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
					
		//--- VALID CLAIM CHECK ---
		if($invalidClaim == false){
			$clm_control_num = '';
			$files = true;
			$notTaken[] = $res[$i]['charge_list_id'];
			$notTakenId = join(",",$notTaken);										
			$operatorId[] = $res[$i]['operator_id'];
			//--- SUBMITED INSERT QUERY ARRAY ---
			$insCompanyId[] = $otherInsDetail->id;
			$subscriberId++;
			$encounter_id[] = $res[$i]['encounter_id'];					
			if($subscriberDetails->subscriber_relationship == '' || $subscriberDetails->subscriber_relationship == 'self'){
				$selfSubCode = '18';
			}
			elseif($subscriberDetails->subscriber_relationship == 'Son'){
				$selfSubCode = '19';						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Doughter'){
				$selfSubCode = '19';						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Mother'){
				$selfSubCode = "G8";						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Father'){
				$selfSubCode = "G8";						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Guardian'){
				$selfSubCode = "G8";
			}
			elseif($subscriberDetails->subscriber_relationship == 'Employee'){
				$selfSubCode = '20';
			}
			elseif($subscriberDetails->subscriber_relationship == 'Spouse'){
				$selfSubCode = '01';
			}
			else{
				$selfSubCode = 'G8';
			}
			if($subscriberDetails->subscriber_relationship != '' || $subscriberDetails->subscriber_relationship != 'self'){
				$ins_comp = 1;
			}
			$selfCode = $selfSubCode;
			if($selfSubCode == 18){
				$ins_comps = 0;
			}
			else{
				$ins_comps = 1;
			}
			if($batchFileStatus == true){
				$ins_comps = 0;
			}
			if($provider1_payerId == 22099 || $provider1_payerId == 61101){
				$bcbsRef = 'G2';				
			}
			else if($provider1_payerId == 'SRRGA'){
				$bcbsRef = 'G2';
			}			
			else{
				$bcbsRef = 'G2';
			}
			$BlueShieldId = preg_replace("/-/","",$groupDetails['group_Federal_EIN']);
			
			$bcbs = "CI";
			//--- MEDICARE PAYER ID ---
			$payer_arr = array('SDMEA','SRRGA');
			
			//--- MEDICAID PAYER ID -----
			$medicaid_arr = array('23225','38692','86047','86048','86049','25140','SX114','53172','95748',
							'25137','75228','58247','00178','84133','TH003','HM033','39186','35177','37330',
							'SX135','SX136','09822','TH037');
			
			//--- BLUE SHIELD PAYER ID ----
			$bl_sh_arr = array('23738','BS001','54763','54721','HM007','940360000','94036','12011','22099');
			
			if(in_array($provider1_payerId,$payer_arr) === true){
				$bcbs = "MB";
			}
			else if(in_array($provider1_payerId,$medicaid_arr) === true){
				$bcbs = "MC";
			}
			else if(in_array($provider1_payerId,$bl_sh_arr) === true){
				$bcbs = "BL";
			}
			
			//Start 2000B SUBSCRIBER HL LOOP
			if(trim(strtolower($subscriberDetails->subscriber_relationship)) == "self"){
							$fileData .= "HL*$subscriberId*1*22*0~";
			} else {
							$fileData .= "HL*$subscriberId*1*22*1~";
			}

			//$fileData .= "HL*$subscriberId*1*22*1~";			
			$groupNumber = trim($subscriberDetails->group_number);
			$groupVar = true;
			$subscriberDetails->policy_number = preg_replace("/[^A-Za-z0-9]/","",$subscriberDetails->policy_number);
			if($groupNumber == '' || strtolower($groupNumber) == 'none'){
				$groupVar = false;
			}

			$plan_name = NULL;
			$plan_name = trim($subscriberDetails->plan_name);
			if(($plan_name == '' || strtolower($plan_name) == 'none') && $groupVar == false){
				$plan_name = trim($subscriberDetails->group_number);
			}
			$selfCode1 = $selfCode;
			if($selfCode==18){
//				$selfCode1 = '';
				$selfCode = 'G8';
			}

			$fileData .= 'SBR*'.substr($type,0,1).'*'.$selfCode1.'*'.$groupNumber.'*'.$plan_name.'*****'.$bcbs.'~';
			//End 2000B SUBSCRIBER HL LOOP
			
			//Start 2010BA SUBSCRIBER
			$subcriber_suffix = $subscriberDetails->subscriber_suffix;
			if($subcriber_suffix=='' && $selfCode=='18'){$subcriber_suffix = trim($res[$i]['patientSuffix']);}
			$fileData .= 'NM1*IL*1*'.trim($subscriberDetails->subscriber_lname).'*'.trim($subscriberDetails->subscriber_fname).'';
			$fileData .= '*'.substr(trim($subscriberDetails->subscriber_mname),0,1).'**'.$subcriber_suffix.'*MI';
			$patient_policy_number = (string)$subscriberDetails->policy_number;
			$fileData .= '*'.trim($patient_policy_number).'~';
			$subscriber_street = $subscriberDetails->subscriber_street.' '.$subscriberDetails->subscriber_street_2;
			$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($subscriber_street)).'~';
			$fileData .= 'N4*'.trim($subscriberDetails->subscriber_city).'*';
			$state_name = correct_state_name($subscriberDetails->subscriber_state);
			$fileData .= $state_name.'*'.trim($subscriberDetails->subscriber_postal_code.$subscriberDetails->zip_ext).'~';

			$p_sex = $subscriberDetails->subscriber_sex;
			$pat_DOB = preg_replace("/-/","",$subscriberDetails->subscriber_DOB);
			$fileData .= 'DMG*D8*'.$pat_DOB.'*'.$p_sex[0].'~';

			//End 2010BA SUBSCRIBER
			//Start 2010BC PAYER
			//--- INSURANCE COMPANY NAME SHOULD BE LESS THAN 35 CHR ---
			$ins_new_name = trim($otherInsDetail->name);
			$ins_new_name = substr($ins_new_name,0,55);
			$fileData .= 'NM1*PR*2*'.$ins_new_name.'*****PI*'.$provider1_payerId.'~';					
			$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($otherInsDetail->contact_address)).'~';	
			$fileData .= 'N4*'.trim($otherInsDetail->City).'*';
			$state_name = correct_state_name($otherInsDetail->State);
			$insZip1 = preg_replace("/-/","",trim($otherInsDetail->Zip.$otherInsDetail->zip_ext));
			$fileData .= $state_name."*".$insZip1."~";
			
			$fileData .= "REF*FY*NOCD~";
			
			//End 2010BC PAYER
			if(trim(strtolower($subscriberDetails->subscriber_relationship)) != "self"){
				$subscriberId++;
				//Start 2000C PATIENT HL LOOP
				$fileData .= "HL*".$subscriberId."*2*23*0~";		
				//End 2000C PATIENT HL LOOP
				//Start 2010CA PATIENT
				
				$fileData .= 'PAT*'.$selfCode.'~';
				$pat_ids = $res[$i]['patient_id'];
				if(strlen($pat_ids) < 7){
					$pat_ids = '000000'.$pat_ids;
				}
				$fileData .= 'NM1*QC*1*'.trim($res[$i]['patientLname']).'*'.trim($res[$i]['patientFname']).'*'.trim(substr($res[$i]['patientMname'],0,1)).'**~';
				$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($res[$i]['patientStreet'])).'~';	
				$fileData .= 'N4*'.trim($res[$i]['patientCity']).'*';
				$state_name = correct_state_name($res[$i]['patientState']);
				$fileData .= $state_name.'*'.trim($res[$i]['patientPostal_code']).'~';
				
				$subscriber_sex = $subscriberDetails->subscriber_sex;
				$pat_DOB = preg_replace("/-/","",$res[$i]['patientDOB']);
				$fileData .= 'DMG*D8*'.$pat_DOB.'*'.$res[$i]['patientSex'][0].'~';

			}else{
				$pat_ids = $res[$i]['patient_id'];
				if(strlen($pat_ids) < 7){
					$pat_ids = '000000'.$pat_ids;
				}
			}

			//End 2010CA PATIENT
			//Start 2300 CLAIM
			$clm_type_indicator = 1;
			if(isset($PCLid_CliamType) && trim($PCLid_CliamType) != ''){
				$ARRpostClaimType = json_decode($PCLid_CliamType);
				$clm_type_indicator = (isset($ARRpostClaimType->$charge_list_id) && intval($ARRpostClaimType->$charge_list_id) > 0) ? intval($ARRpostClaimType->$charge_list_id) : 1;
			}
			$CHLS_Type_Status[$charge_list_id] = $clm_type_indicator;
			$fileData .= 'CLM*'.$pat_ids.'*'.preg_replace("/,/","",$totalAmt).'***83:A:'.$clm_type_indicator.'**A*Y*Y~';

			$admit_date = $res[$i]['admit_date'];
			if($admit_date != '0000-00-00 00:00:00' && $admit_date != '0000-00-00'){
				$admit_date = preg_replace("/-/","",$admit_date);
				$admit_date = preg_replace("/ /","",$admit_date);
				$admit_date = preg_replace("/:/","",$admit_date);
				$fileData .= 'DTP*435*D8*'.$admit_date.'~';
			}else if($posCodeDetails->pos_code == 21){
				$fileData .= 'DTP*435*D8*'.preg_replace("/-/","",$res[$i]['date_of_service']).'~';
			}
			$disch_date = $res[$i]['disch_date'];
			if($disch_date != '0000-00-00 00:00:00' && $disch_date != '0000-00-00'){
				$disch_date = preg_replace("/-/","",$disch_date);
				$disch_date = preg_replace("/ /","",$disch_date);
				$disch_date = preg_replace("/:/","",$disch_date);
				$fileData .= 'DTP*096*D8*'.$disch_date.'~';
			}else if($posCodeDetails->pos_code == 21){
				$fileData .= 'DTP*096*D8*'.preg_replace("/-/","",$res[$i]['date_of_service']).'~';
			}
			
			//$adm_time = explode(':',$res[$i]['adm_time']);
			//$dis_time = explode(':',$res[$i]['dis_time']);
			//$admit_time = $adm_time[0].$adm_time[1];
			//$disch_time = $dis_time[0].$dis_time[1];
			/*if($disch_time != '0000' and trim($disch_time) != ''){
				$fileData .= 'DTP*096*TM*'.preg_replace("/-/","",$disch_time).'~';
			}*/
			$date_of_service = preg_replace("/-/","",$date_of_service);
			$fileData .= "DTP*434*RD8*".$date_of_service."-".$date_of_service."~";
			/*
			if($admit_date != '0000-00-00'){
				$fileData .= 'DTP*435*DT*'.$admit_date.$admit_time.'~';
			}*/
			
			$fileData .= 'CL1**1*01~';
			//End 2300 CLAIM
			
			$ins_encounter_id = $res[$i]['encounter_id'];
			
			$approval = array_unique(array_merge($approval1,$approval2));
			
			if(count($approval)>0){
				foreach($approval as $approval_val){
					$fileData .= "REF*G1*$approval_val~";	
				}
			}
			if(trim($referral) != ''){
				$fileData .= "REF*9F*$referral~";
			}
			
			$diagnosisId = array_unique($diagnosis_id);
			if($enc_icd10==1){
				$fileData .= 'HI*ABK:'.$diagnosisId[0].'~';
			}else{
				$fileData .= 'HI*BK:'.$diagnosisId[0].'~';
			}

			unset($diagnosisId[0]);
			if(trim($diagnosisId[1]) != ''){
				if($enc_icd10==1){
					$fileData .= 'HI*ABF:'.join('*ABF:',$diagnosisId).'~';
				}else{
					$fileData .= 'HI*BF:'.join('*BF:',$diagnosisId).'~';
				}
			}
			
			//Start 2310A ATTENDING PROVIDER
			$fileData .= 'NM1*71*1*'.trim($res[$i]['usersLname']).'*'.trim($res[$i]['usersFname']).'****XX*'.preg_replace("/-/","",$groupDetails['group_NPI']).'~';
			
			$fileData .= "REF*$bcbsRef*$BlueShieldId~";
			//End 2310A ATTENDING PROVIDER
			
			//Start 2310B OPERATING PROVIDER
			//-- Reffering Physicain Details ---------
			if($provider1_payerId<>'12402'){
				$fileData .= 'NM1*72*1*'.$res[$i]['usersLname'].'*'.$res[$i]['usersFname'].'****XX*'.preg_replace("/-/","",$groupDetails['group_NPI']).'~';
				$fileData .= "REF*$bcbsRef*$BlueShieldId~";
			}			
			//-- Renderring Physicain Details ---------
			//End 2310B OPERATING PROVIDER
			
			//--- START LOOP 2310D ----
			$pos_facility_name = $posFacilityDetail->facility_name;
			$pos_facility_npi = $posFacilityDetail->npiNumber;
			if($groupDetails['group_NPI'] != $pos_facility_npi){//.'*****XX*'.$groupDetails['group_NPI'].'~';
				$fileData .= "NM1*82*2*$pos_facility_name*****XX*$pos_facility_npi~";
			}
			$pos_facility_address = preg_replace('/\*/',' ',trim($posFacilityDetail->pos_facility_address));
		//	$fileData .= "N3*$pos_facility_address~"; //commented in 5010.
			$pos_facility_city = $posFacilityDetail->pos_facility_city;			
			$pos_facility_state = correct_state_name($posFacilityDetail->pos_facility_state);
			$pos_facility_zip = $posFacilityDetail->pos_facility_zip.$posFacilityDetail->zip_ext;
		//	$fileData .= "N4*$pos_facility_city*$pos_facility_state*$pos_facility_zip~"; //commented in 5010
			//--- END LOOP 2310D ----
			
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
					$selfCode2 = 'G2';						
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Father'){
					$selfCode2 = 'G2';						
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Guardian'){
					$selfCode2 = 'G2';
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Employee'){
					$selfCode2 = '20';
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Spouse'){
					$selfCode2 = '01';
				}
				else{
					$selfCode2 = 'G2';
				}
				
				$secCode = 'CI';
				if(in_array($provider2_payerId,$payer_arr) === true){
					$secCode = 'MB';
				}
				else if(in_array($provider2_payerId,$medicaid_arr) === true){
					$secCode = 'MC';
				}
				else if(in_array($provider2_payerId,$bl_sh_arr) === true){
					$secCode = 'BL';
				}
				
				$groupNumber2 = trim($subscriberDetails2->group_number);
				$subscriberDetails2->policy_number = preg_replace("/[^A-Za-z0-9]/","",$subscriberDetails2->policy_number);
				if($groupNumber2 == '' || strtolower($groupNumber2) == 'none'){
					$groupNumber2 = NULL;
				}
				$plan_name2 = trim($subscriberDetails2->plan_name);
				if(($plan_name2 == '' || strtolower($plan_name2) == 'none') && strtolower($subscriberDetails2->group_number) != 'none'){
					$plan_name2 = $subscriberDetails2->group_number;
				}
				
				//Start 2320 OTHER SUBSCRIBER INFORMATION
				if(strtoupper($SecondType[0]) == 'S'){
					if($selfCode2 == 18){
//						$selfCode2 = '';
					}
				}
				
				$fileData .= 'SBR*'.substr($SecondType,0,1).'*'.$selfCode2.'*'.$groupNumber2.'*'.$plan_name2.'*****'.$secCode.'~';
				
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
						$priInsPaidAmountByCLDidArr[$paidForProc_CLDid][$paidForProc_INStype][$paidForProc_INSid][] = $paidForProc;
						if($piadRes[$r]['paymentClaims'] == 'Negative Payment'){
							$paidForProc = '-'.$paidForProc;
						}										
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
					$arr_cas_type_ptChLsDetailIdWise = $arr_cas_code_ptChLsDetailIdWise = $arr_cas_amount_ptChLsDetailIdWise = $arr_cas_date_ptChLsDetailIdWise = array();
					$cas_value_added_chlId = 0;
					$other_payment_detail_arr = array();
					$arr_cas_write_off = array();
					for($w=0;$w<count($writeOffQryRes);$w++){
						//$insPaidAmountArr[] = $writeOffQryRes[$w]['write_off_amount'];
						$charge_list_detail_id = $writeOffQryRes[$w]['charge_list_detail_id'];
						$arr_cas_write_off[$charge_list_detail_id][] = $writeOffQryRes[$w]['write_off_amount'];
						if($cas_value_added_chlId != $charge_list_detail_id){
							$cas_value_added_chlId = $charge_list_detail_id;
							$arr_cas_type_ptChLsDetailIdWise[$charge_list_detail_id] = $writeOffQryRes[$w]['CAS_type'];
							$arr_cas_code_ptChLsDetailIdWise[$charge_list_detail_id] = $writeOffQryRes[$w]['CAS_code'];
							$arr_cas_amount_ptChLsDetailIdWise[$charge_list_detail_id] = $writeOffQryRes[$w]['era_amt'];
							$arr_cas_date_ptChLsDetailIdWise[$charge_list_detail_id] = $writeOffQryRes[$w]['write_off_date'];
						}
						$other_payment_detail_arr[$charge_list_detail_id][] = $writeOffQryRes[$w]['write_off_amount'];
						$writeOffDate = $writeOffQryRes[$w]['write_off_date'];
					}
					if(empty($insPaidDate) == true){
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
						//$insPaidAmountArr[] = $writeOffQryRes['write_off_amount'];
						$charge_list_detail_id = $deductQryRes['charge_list_detail_id'];
						//$other_payment_detail_arr[$charge_list_detail_id][] = $deductQryRes['write_off_amount'];
						$deductible_amount_arr[$charge_list_detail_id][] = $deductQryRes['write_off_amount'];
						$writeOffDate = $writeOffQryRes['write_off_date'];
					}
					if(empty($insPaidDate) == true){
						$insPaidDate = $writeOffDate;
					}

					//--- DENIED AMOUNT DETAILS ---
					$denied_qry = "select deniedAmount, deniedDate, CAS_type, CAS_code, charge_list_detail_id   
								FROM deniedpayment where deniedById = '$provider_id2' 
								AND patient_id='$patientId' AND encounter_id='$ins_encounter_id' 
								AND denialDelStatus = '0' AND status='1'";
					if(empty($not_cov_proc_str) === false){
						$denied_qry .= " and charge_list_detail_id not in ($not_cov_proc_str)";
					}
					$denied_qry .= " order by deniedId DESC";
					$deniedQryRs = imw_query($denied_qry);
					while($deniedQryRes = imw_fetch_assoc($deniedQryRs)){
						$charge_list_detail_id = $deniedQryRes['charge_list_detail_id'];
						if($arr_cas_type_ptChLsDetailIdWise[$charge_list_detail_id]==''){
							$arr_cas_type_ptChLsDetailIdWise[$charge_list_detail_id] = $deniedQryRes['CAS_type'];
							$arr_cas_code_ptChLsDetailIdWise[$charge_list_detail_id] = $deniedQryRes['CAS_code'];
							$arr_cas_amount_ptChLsDetailIdWise[$charge_list_detail_id] = $deniedQryRes['deniedAmount'];
							$arr_cas_date_ptChLsDetailIdWise[$charge_list_detail_id] = $deniedQryRes['deniedDate'];
						}
					}
					
					$insPaidAmount = array_sum($insPaidAmountArr);
					$fileData .= 'AMT*D*'.preg_replace("/,/","",number_format($insPaidAmount,2)).'~';
					
					//--- TOTAL APPROVED AMOUNT ---
					$totalAmt = preg_replace('/,/','',$totalAmt) - $writeOffAmtTotal;
					$totalAmt = preg_replace('/,/','',number_format($totalAmt,2));					
					
					//$fileData .= 'AMT*EAF*'.$totalAmt.'~';//commented in 5010 on 06thNov2013.
				}
				
				$DOB2 = preg_replace("/-/","",$subscriberDetails2->subscriber_DOB);							
				$subscriber_sex2 = $subscriberDetails2->subscriber_sex;
			//	$fileData .= 'DMG*D8*'.$DOB2.'*'.substr($subscriber_sex2,0,1).'~';
				$fileData .= 'OI***N***Y~';

				//End 2320 OTHER SUBSCRIBER INFORMATION
				//Start 2330A OTHER SUBSCRIBER NAME
				$fileData .= 'NM1*IL*1*'.trim($subscriberDetails2->subscriber_lname).'*'.trim($subscriberDetails2->subscriber_fname).'';
				if($subscriberDetails2->subscriber_mname != ''){
					$patientMname2 = substr($subscriberDetails2->subscriber_mname,0,1);
				}
				else{
					$patientMname2 = $res[$i]['patientMname'];
				}
				$fileData .= '*'.trim($patientMname2).'***MI';
				$patient_policy_number2 = (string)$subscriberDetails2->policy_number;
				$fileData .= '*'.trim($patient_policy_number2).'~';
				if(trim($subscriberDetails2->subscriber_street)){
					$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($subscriberDetails2->subscriber_street)).'~';
					$fileData .= 'N4*'.trim($subscriberDetails2->subscriber_city).'*';
				}
				else{
					$fileData .= 'N3*'.trim($subscriberDetails2->subscriber_city).'~';
					$fileData .= 'N4*'.trim($subscriberDetails2->subscriber_city).'*';
				}
				$state_name = correct_state_name($subscriberDetails2->subscriber_state);
				$fileData .= $state_name.'*'.trim($subscriberDetails2->subscriber_postal_code).'~';
				//End 2330A OTHER SUBSCRIBER NAME
				
				//Start 2330B OTHER PAYER
				//--- INSURANCE COMPANY NAME SHOULD BE LESS THAN 35 CHR ---
				$ins_new_name2 = trim($otherInsDetail2->name);
				$ins_new_name2 = substr($ins_new_name2,0,34);
				$fileData .= 'NM1*PR*2*'.$ins_new_name2.'*****PI*'.$provider2_payerId.'~';
				$fileData .= 'REF*FY*'.$provider2_payerId.'~';				
				$fileData .= 'REF*G1*'.$Texonomy.'~';
				if(empty($insPaidDate) === false){
					//$fileData .= "DTP*573*D8*$insPaidDate~";
				}
				
				//End 2330B OTHER PAYER
			}
			
			//-- PUT ENCOUNTER DETAILS IN FILE DATA ------
			for($u=0;$u<count($patientChargeDetails);$u++){
				$modifierIdArr = array();
				$validChargeListDetailsId[] = $patientChargeDetails[$u]['charge_list_detail_id'];
				$fileData .= 'LX*'.($u+1).'~';
				
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
				$jcode_colons = 4-$mod_count;
				$jcode_colons_str = str_repeat(":",$jcode_colons);
				$jcode_cpt_desc = $jcode_colons_str.':'.$proc_acc_notes;
				$firstCharOfCPT = strtolower(substr($patientChargeDetails[$u]['cpt4_code'],0,1));
				if($firstCharOfCPT=='j' && $proc_acc_notes=='' && $proc_admin_desc!=''){
					$jcode_cpt_desc = $jcode_colons_str.':'.$proc_admin_desc;
				}
				if($proc_acc_notes=='' && $firstCharOfCPT!='j'){
					$jcode_cpt_desc = '';
				}
									
				$procCharge = $patientChargeDetails[$u]['totalAmount'];
				if($batchFileStatus == true){
					if(strlen($procCharge) == 4){
						$procCharge = str_replace('0.','.',$procCharge);
					}
				}
				
				//--- REV CODE DETAILS ---				
				$rev_qry_rs = imw_query("select r_code from revenue_code where r_id = '".$patientChargeDetails[$u]['rev_code']."'");
				$rev_qry_res = imw_fetch_assoc($rev_qry_rs);
				$rev_code = $rev_qry_res["r_code"];
				
				$fileData .= 'SV2*'.$rev_code.'*HC:'.$patientChargeDetails[$u]['cpt4_code'].''.$modifierCodes.$jcode_cpt_desc.'*'.$procCharge.'';
				$fileData .= '*UN*'.$patientChargeDetails[$u]['units'].'~';
				$fileData .= 'DTP*472*D8*'.preg_replace("/-/","",$res[$i]['date_of_service']).'~';
				
				//--- CHARGE LIST DETAIL ID FOR PAYMENT DETAILS OF PRIMARY INSURANCE ----
				if($primaryFile == false){
					$charge_list_detail_id = $patientChargeDetails[$u]['charge_list_detail_id'];
					$otherPaymentDetailArr = $other_payment_detail_arr[$charge_list_detail_id];
					
					$paidForProc = preg_replace('/,/','',$patientChargeDetails[$u]['paidForProc']);
					$paidForProc += preg_replace('/,/','',$patientChargeDetails[$u]['overPaymentForProc']);
					$paidForProc = preg_replace('/,/','',number_format($paidForProc,2));					
					
					/*-------------*/					
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

					//CAS codes if returned by primary payer.
					$cas_type_returned = strtoupper(trim($arr_cas_type_ptChLsDetailIdWise[$charge_list_detail_id]));
					if(strlen($cas_type_returned)>2 && strpos($cas_type_returned,',')){
						$temp_ar_castype = explode(',',$cas_type_returned);
						if(strlen(trim($temp_ar_castype[0]))==2){
							$cas_type_returned = trim($temp_ar_castype[0]);
						}
					}
					$cas_code_returned = trim($arr_cas_code_ptChLsDetailIdWise[$charge_list_detail_id]);
					if(strlen($cas_code_returned)>2 && strpos($cas_code_returned,',')){
						$temp_ar_cascode = explode(',',$cas_code_returned);
						if(strlen(trim($temp_ar_cascode[0]))>=2){
							$cas_code_returned = strtoupper(trim($temp_ar_cascode[0]));
						}
					}
					$cas_amount_returned = preg_replace('/,/','',number_format($arr_cas_amount_ptChLsDetailIdWise[$charge_list_detail_id],2));
					$cas_date_adjudication = $arr_cas_date_ptChLsDetailIdWise[$charge_list_detail_id];
					//echo $cas_type_returned.':'.$cas_code_returned.':'.$cas_amount_returned.'<br>';
					
					//writeOff for procedure
					$writeoffForProc2 	= array_sum($arr_cas_write_off[$charge_list_detail_id]);
					$AdjustmentAmount 	= ($procCharge - $allowedForProc)+$writeoffForProc2;

					//deductible for procedure
					$deductible4Proc 	= array_sum($deductible_amount_arr[$charge_list_detail_id]);
					
					//balance for procedure
					//$balForProc = $patientChargeDetails[$u]['balForProc'];
					$balForProc = (($procCharge - $AdjustmentAmount)-$deductible4Proc) - $paidForProcByPriIns;
					$balForProc = preg_replace('/,/','',number_format($balForProc,2));
					
					if($insPaidAmount >= 0 && $paidForProcByPriIns >=0){
						if($paidForProcByPriIns >= 0){
							$fileData .= 'SVD*'.$provider2_payerId.'*'.$paidForProcByPriIns.'*HC:'.$patientChargeDetails[$u]['cpt4_code'].''.$modifierCodes.'**1~';
						}
						
						if(($AdjustmentAmount != '' && $AdjustmentAmount >0) || ($deductible4Proc != '' && $deductible4Proc > 0) || ($balForProc != '' && $balForProc > 0)){
							if($AdjustmentAmount != '' && $AdjustmentAmount >0){//Charges exceed our fee schedule or maximum allowab...
								$fileData .= "CAS*CO*45*".preg_replace('/,/','',number_format($AdjustmentAmount,2))."*1~";
							}
							$CAS_codes = '';
							if(($deductible4Proc != '' && $deductible4Proc > 0) || ($balForProc != '' && $balForProc > 0)){
								$CAS_codes = 'CAS';
								if($deductible4Proc != '' && $deductible4Proc > 0){//deductible available for this proc.
									$CAS_codes .= "*PR*1*".preg_replace('/,/','',number_format($deductible4Proc,2))."*1";
								}
								if($balForProc != '' && $balForProc > 0){
									$CAS_codes .= "*PR*2*".preg_replace('/,/','',number_format($balForProc,2))."*1";
								}
								if(strlen($CAS_codes)>3){
									$fileData .= $CAS_codes."~";
								}
							}
						}

						if(($paidForProcByPriIns>=0 || $AdjustmentAmount>0)){
							if(empty($insPaidDate) === true){
								$insPaidDate = $patientChargeDetails[$u]['write_off_date'];
							}
							$insPaidDate = preg_replace('/-/','',$insPaidDate);
							if(strlen($insPaidDate)==8){
								$fileData .= "DTP*573*D8*$insPaidDate~";
							}
						}									
					}else if($insPaidAmount == 0){
						if($cas_type_returned != '' && $cas_code_returned > 0 && $cas_amount_returned > 0){//denied payment
							$fileData .= 'SVD*'.$provider2_payerId.'*'.$paidForProcByPriIns.'*';
							$fileData .= 'HC:'.$patientChargeDetails[$u]['cpt4_code'].''.$modifierCodes.'**1~';
							$fileData .= "CAS*$cas_type_returned*$cas_code_returned*$cas_amount_returned*1~";
							$fileData .= "AMT*EAF*$cas_amount_returned~";//remaining pt liablity.
							if(empty($insPaidDate) === true){
								$insPaidDate = $cas_date_adjudication;
							}
							$insPaidDate = preg_replace('/-/','',$insPaidDate);
							if(strlen($insPaidDate)==8){
								$fileData .= "DTP*573*D8*$insPaidDate~";
							}
						}else{//deductible case.
							$fileData .= 'SVD*'.$provider2_payerId.'*'.$paidForProcByPriIns.'*';
							$fileData .= 'HC:'.$patientChargeDetails[$u]['cpt4_code'].''.$modifierCodes.'**1~';
							$fileData .= "CAS*PR*1*$procCharge*1~";
							$fileData .= "AMT*EAF*$procCharge~";//remaining pt liablity.
							if(empty($insPaidDate) === true){
								$insPaidDate = $cas_date_adjudication;
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
	$fileData = preg_replace('/new jersey/','nj',strtolower($fileData));
	$fileData = preg_replace('/\n/',', ',$fileData);
	if($files == true){
		if($createClaims != '' && $chk_institution==$gro_id){
			$transectionCount++;
			
			$encounterId = join(",",$encounter_id);
			$pcld_ids = join(",",array_unique($validChargeListDetailsId));
			$segmentCountArr = preg_split('/~/',$fileData);
			
			//Start TRANSACTION SET TRAILER
			$fileData .= 'SE*'.(count($segmentCountArr)-2).'*'.$Transaction_set_unique_control.'~';
			$fileData .= 'GE*'.$transectionCount.'*'.$header_control_identifier.'~';
			$fileData .= 'IEA*1*'.$InterchangeControlNumber.'~';	
			//End TRANSACTION SET TRAILER
			
			//---- INSERT CLAIM FILE DATA -----------
			$insCompanyIds = join(",",array_unique($insCompanyId));
			$operatorIds = join(",",$operatorId);
			$segmentCountstarts = join(',',$segmentCountstart);
			$segmentCountEnds = join(',',$segmentCountEnd);
			$fileData = preg_replace('/[^a-zA-Z0-9_*\- ~.:\^\']/',' ',$fileData);
			
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
			$record_data_arr['clearing_house'] = 'ghn';
			$insert_data = AddRecords($record_data_arr,'batch_file_submitte');			
			if($insert_data > 0){
				batch_file_log($insert_data,"created");
				$getData = true;
			}
			
			//---- INSERT FILE NAME COUNT -----------
			$record = array("submit_date"=>date("Y-m-d"),"file_status"=>"1");
			$insertData = AddRecords($record,'batch_file_detail');
			file_put_contents("../batchfiles/$fileName",strtoupper($fileData));
		}
	}								
}
if($separation_done == true){
	$vfactor = count($arr_valid);
	for($v = 0; $v < count($validChargeListId); $v++){
		$arr_valid[$v+$vfactor] = $validChargeListId[$v];
	}
	$ivfactor = count($arr_invalid);
	for($iv = 0; $iv < count($invalidChargeListId); $iv++){
		$arr_invalid[$iv+$ivfactor] = $invalidChargeListId[$iv];
	}
	$rfactor = count($arr_res);
	for($r = 0; $r < count($res); $r++){
		$arr_res[$r+$rfactor] = $res[$r];
	}
}
?>