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
File: reprint_emdeon_electronic_file_i_5010.php
Purpose: Re-create commercial 837 batch file (Institutional)
Access Type: Include File 
*/
//--- CLAIM FILE HEADER ---
$fileData = 'ISA*00*          *00*          *ZZ*'.$submitterId.''.$submitterSpaceStr.'*ZZ*'.$recieverId.''.$recieveSpaceStr.'*'.date('ymd').'*'.date('hi').'*^*00501*'.$InterchangeControlNumber.'*1*'.$production_code.'*:~';
$fileData .= 'GS*HC*'.$submitterId.'*'.$recieverId.'*'.date('Ymd').'*'.date('hi').'*'.$header_control_identifier.'*X*005010X223A2~';
$fileData .= 'ST*837*'.$Transaction_set_unique_control.'*005010X223A2~';
$fileData .= 'BHT*0019*00*'.$Interchange_controls.'*'.date('Ymd').'*'.date('hi').'*CH~';
//$fileData .= 'REF*87*005010X223A2~';

//--- Start 1000A SUBMITTER
$fileData .= 'NM1*41*2*'.trim(substr($groupDetails['name'],0,60)).'*****46*'.$submitterId.'~';	
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
$group_taxonomy = trim($groupDetails['taxonomy']);
if($group_taxonomy!=''){$billing_global_taxonomy_number=$group_taxonomy;}
if(strtolower(trim($billing_global_server_name)) == 'sheepshead' && !in_array($InsuranceComDetails['0']['Payer_id'],array('14163','86002'))){
	$billing_global_taxonomy_number = '';
}
if(trim($billing_global_taxonomy_number) != ''){
	if(!in_array(strtoupper($InsuranceComDetails['0']['Payer_id']),array('SRRGA','SKNJ0')) || ($billing_global_server_name == 'gewirtz')){
		$fileData .= 'PRV*BI*PXC*'.trim($billing_global_taxonomy_number).'~';
	}else if(!in_array(strtoupper($InsuranceComDetails['0']['Payer_id']),array('48145','27514','87726','60495'))){
		$fileData .= 'PRV*BI*PXC*'.trim($billing_global_taxonomy_number).'~';
	}
}

//End 2000A BILLING/PAY-TO PROVIDER HL LOOP

//Start 2010AA BILLING PROVIDER
$group_npi = $groupDetails['group_NPI'];
$optional_anes_npi 	= $groupDetails['optional_anes_npi'];
if($optional_anes_npi != '') $group_npi = $optional_anes_npi;
if(!empty($overRightPayerWiseNPI) && $overRightPayerWiseNPI) $group_npi = $overRightPayerWiseNPI;
$groupDetails['group_NPI'] = $group_npi;

if(in_array(strtoupper($InsuranceComDetails['0']['Payer_id']),array('SKNJ0'))){
	if($billing_global_server_name == 'duncan'){
		$group_npi = '1063419810';
	}else if($billing_global_server_name == 'scott'){
		$group_npi = '1780729921';
	}
}

$fileData .= 'NM1*85*2*'.trim(substr($groupDetails['name'],0,60)).'*****XX*'.$group_npi.'~';
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
if(trim($recieverId) != '141650868'){
	if((strtolower($billing_global_server_name)=='hammad_iasc' || constant('DEFAULT_PRODUCT') == 'imwemr') && strtolower($billing_global_server_name)!='palisades'){
		$fileData .= "REF*G5*$refSubmitterid~";
	}
}

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

//--- GET MAIN CHARGE LIST ID ---
$query = "select charge_list_id from patient_charge_list where (del_status='0' OR  AND void_notify='1') and  encounter_id in ($encounter_id)";
$chrQryRes = imw_query($query);
$main_charge_list_id_arr = array();
while($chrQryRs = imw_fetch_assoc($chrQryRes)){
	$main_charge_list_id_arr[] = $chrQryRs['charge_list_id'];
}
$main_charge_list_id = join(',', $main_charge_list_id_arr);

//--- NOT COVERED AND SELF PAY PROCEDURE -----
$cptQry = "SELECT pcld.charge_list_detail_id, pcld.charge_list_id FROM patient_charge_list_details pcld 
			JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode 
			JOIN patient_charge_list pcl ON (pcl.charge_list_id = pcld.charge_list_id) 
			WHERE pcld.charge_list_id in ($main_charge_list_id) 
			AND (pcld.charge_list_detail_id NOT IN ($pcld_id) OR 
			cft.not_covered = '1' OR pcld.proc_selfpay = '1' OR (pcld.del_status='1' AND pcl.void_notify='0'))";
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
		substring(patient_data.mname,1,1) AS patientMname, patient_data.street AS patientStreet,
		patient_data.street2 AS patientStreet2, CONCAT(patient_data.postal_code,patient_data.zip_ext) AS patientPostal_code,
		patient_data.city AS patientCity, patient_data.state AS patientState,
		patient_data.sex AS patientSex, patient_data.id AS patientId,
		patient_data.providerID AS patientProviderID, patient_data.default_facility AS patientDefaultFacility,
		insurance_companies.name AS insurance_companies_name,
		insurance_companies.contact_address AS insurance_companies_contact_address,
		insurance_companies.City AS insurance_companies_City,
		insurance_companies.State AS insurance_companies_State,
		insurance_companies.Zip AS insurance_companies_Zip,
		insurance_companies.phone AS insurance_companies_phone,
		insurance_companies.BatchFile AS insurance_companies_BatchFile,
		insurance_companies.Reciever_id AS insurance_companies_Reciever_id,
		insurance_companies.id AS insurance_companies_id,
		insurance_companies.Payer_id AS insurance_Payer_id,
		users.user_npi AS userNpi, trim(users.fname) AS usersFname,
		substring(trim(users.mname),1,1) AS usersMname, trim(users.lname) AS usersLname,
		users.BlueShieldId AS BlueShieldIds, users.TaxonomyId AS usersTaxonomyId,
		users.TaxId AS usersTaxId, users.default_group AS users_default_group,
		users.user_npi AS users_npi, refferphysician.NPI, users.federaltaxid AS usersFederaltaxid
		from patient_charge_list left join insurance_companies		
		on patient_charge_list.$insComp = insurance_companies.id
		left join users on patient_charge_list.primaryProviderId = users.id
		left join patient_data on patient_charge_list.patient_id = patient_data.id
		left join refferphysician on refferphysician.physician_Reffer_id = patient_data.primary_care
		left join patient_charge_list_details 
		on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
		where ((patient_charge_list_details.del_status='0' AND patient_charge_list.submitted = 'true') OR (patient_charge_list.void_notify='1' AND patient_charge_list.void_claim_date='0000-00-00 00:00:00'))
		and patient_charge_list.gro_id= '$default_group_id'
		and patient_charge_list.encounter_id in ($encounter_id)
		and patient_charge_list_details.proc_selfpay != '1' 
		AND patient_charge_list_details.charge_list_detail_id IN ($pcld_id) 
		group by patient_charge_list.charge_list_id order by users.lname";	

$ress = imw_query($chrgs_qry);

if($ress && imw_num_rows($ress)>0){
	//--- MAX 25 CLAIM FOR TEST CLAIM FILE ------
	$loopCount = count($res);
	if($production_code == 'T' and $loopCount > 50){
		$loopCount = 50;
	}
	$encounter_id = array();
	$transectionCount = 0;
	if(file_exists('../batchfiles/'.$fileName)){				
		unlink('../batchfiles/'.$fileName);
	}
	$i = 0;
	$res = array();
	while($rss = imw_fetch_assoc($ress)){
		$res[$i] = $rss;
		if($insComp == 'primaryInsuranceCoId'){
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
		$charge_list_ids = $res[$i]['charge_list_id'];			
		$referral = trim($res[$i]['referral']);
		$enc_icd10= intval($res[$i]['enc_icd10']);
		$ref_phy_not_required = $res[$i]['reff_phy_nr'];
		//--- GET PATIENT NAME DETAILS ----
		$patient_name_arr = array();
		$patient_name_arr["LAST_NAME"] = $res[$i]['patientLname'];
		$patient_name_arr["FIRST_NAME"] = $res[$i]['patientFname'];
		$patient_name_arr["MIDDLE_NAME"] = $res[$i]['patientMname'];
		$patient_name = changeNameFormat($patient_name_arr);
		
		//--- NOT COVERED OR SELF PAY PROCEDURE FOR SINGLE ENCOUNTER ---
		$not_cov_proc_arr = array();
		$not_cov_proc_arr = $notCoverProDetailIdArr[$charge_list_ids];
		$not_cov_proc_str = '';
		if(count($not_cov_proc_arr) > 0){
			$not_cov_proc_str = join(",", $not_cov_proc_arr);
		}
		
		//-- GET PATIENT CHARGE LIST DETAILS -----
		$charge_list_id = $res[$i]['charge_list_id'];		
		$pcld_q = "select patient_charge_list_details.*, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_comments, cpt_fee_tbl.unit_of_measure, cpt_fee_tbl.measurement 
				from patient_charge_list_details 
				left join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode				
				where patient_charge_list_details.charge_list_id = '$charge_list_id' 
				AND patient_charge_list_details.charge_list_detail_id IN ($pcld_id) ";
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
		$writeOffAmtArr = $detailIdArr = array();
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
			
			$notes = $patientChargeDetails[$u]['notes'];
			if($notes != 'NOTES...' and empty($notes) == false){
				$notesArr[] = $notes;
			}
			
			//$tot_proc_amt = $patientChargeDetails[$u]['procCharges']*$patientChargeDetails[$u]['units'];
			
			$writeOffAmtArr[] = $patientChargeDetails[$u]['write_off'];
			$totalAmt += preg_replace('/,/','',$patientChargeDetails[$u]['totalAmount']); 
			
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
		//	$error[$res[$i]['charge_list_id']] = 'POS Required For '.trim($patient_name).' ('.$patientId.')';
		//	$invalidClaim = true;
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
		
		$payment_method = trim($otherInsDetail->Insurance_payment);
		if($primaryFile == false){
			$payment_method = trim($otherInsDetail->secondary_payment_method);
		}
		
		if($payment_method != "Electronics" && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier is not set for Electronics File Claims.";
			$invalidClaim = true;
		}
		if(trim($otherInsDetail->Payer_id) == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Payer Id is Required.";
			$invalidClaim = true;
		}
		else if(strlen(trim($otherInsDetail->Payer_id)) < 3 && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = "$type Insurance Carrier Payer Id minimum length violation.";
			$invalidClaim = true;
		}
		if($otherInsDetail->Payer_id == 'SPRNT' && $invalidClaim == false){
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
		
		if($ref_phy_not_required=='0' && $reffDetail == '' && $invalidClaim == false){
			$error[$res[$i]['charge_list_id']] = 'Referring Physician is Required.';
			$invalidClaim = true;
		}
		if($ref_phy_not_required=='0' && trim($npiNumber) == '' && $invalidClaim == false){
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
		if($subscriberDetails->subscriber_street == '0') 
			$subscriberDetails->subscriber_street = '';
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
			if(trim($otherInsDetail2->Payer_id) == '' && $invalidClaim == false){
				$error[$res[$i]['charge_list_id']] = "$SecondType Insurance Carrier Payer Id is Required.";
				$invalidClaim = true;
			}
			if($otherInsDetail2->Payer_id == 'SPRNT' && $invalidClaim == false){
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
		$transectionCount = 0;
		$encounter_id[] = $res[$i]['encounter_id'];
		if($invalidClaim == false){
			$clm_control_num = '';
			$files = true;
			$subscriberId++;
			$insCompanyId[] = $otherInsDetail->id;			
			$patientId = $res[$i]['patient_id'];
			$ins_caseid = $res[$i]['case_type_id'];
						
			if($subscriberDetails->subscriber_relationship == '' || $subscriberDetails->subscriber_relationship == 'self'){
				$selfSubCode = 18;
			}
			elseif($subscriberDetails->subscriber_relationship == 'Son'){
				$selfSubCode = 19;						
			}
			elseif($subscriberDetails->subscriber_relationship == 'Doughter'){
				$selfSubCode = 19;						
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
				$selfSubCode = "G8";
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
			if($otherInsDetail->Payer_id == 22099 || $otherInsDetail->Payer_id == 61101){
				$bcbsRef = "G2";
			}
			else if($otherInsDetail->Payer_id == 'SRRGA'){
				$bcbsRef = "G2";
			}			
			else{
				$bcbsRef = "G2";
			}
			$BlueShieldId = preg_replace("/-/","",$groupDetails['group_Federal_EIN']);
			
			$bcbs = "CI";
			//--- MEDICARE PAYER ID ---
			$payer_arr = $arr_Medicare_payers;
			
			//--- MEDICAID PAYER ID -----
			$medicaid_arr = array('23225','38692','86047','86048','86049','25140','SX114','53172','95748',
							'25137','75228','58247','00178','84133','TH003','HM033','39186','35177','37330',
							'SX135','SX136','09822','TH037','610515','141797357');

			if(in_array($otherInsDetail->Payer_id,$payer_arr) === true){
				$bcbs = "MB";
			}
			else if(in_array($otherInsDetail->Payer_id,$medicaid_arr) === true){
				$bcbs = "MC";
			}
			else if(in_array($otherInsDetail->Payer_id,$arr_BL_payers) === true){
				$bcbs = "BL";
			}
			if($otherInsDetail->ins_type != '' && strlen(trim($otherInsDetail->ins_type))==2){
				$bcbs = strtoupper(trim($otherInsDetail->ins_type));
			}
			
			//Start 2000B SUBSCRIBER HL LOOP
			$fileData .= "HL*$subscriberId*1*22*0~";			
			$groupNumber = trim($subscriberDetails->group_number);
			$groupVar = true;
			$subscriberDetails->policy_number = preg_replace("/[^A-Za-z0-9]/","",$subscriberDetails->policy_number);
			if($groupNumber == '' || strtolower($groupNumber) == 'none'){
				$groupVar = false;
			}

			$plan_name = NULL;
			if(($plan_name == '' || strtolower($plan_name) == 'none') && $groupVar == false){
				$plan_name = trim($subscriberDetails->plan_name);
			}
			$selfCode1 = $selfCode;
			if($selfCode==18){
			//	$selfCode1 = '';
				$selfCode = 'G8';
			}
			$ins_type_code1 = '';
			if(in_array(strtolower($billing_global_server_name), array('liasc','liesc','leps')) && in_array($bcbs,array('MB','MA'))){$ins_type_code1 = '16';}
			
			if(trim($recieverId) == '141650868')	$plan_name = '';
			$fileData .= 'SBR*'.substr($type,0,1).'*'.$selfCode1.'*'.$groupNumber.'*'.$plan_name.'*'.$ins_type_code1.'****'.$bcbs.'~';
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
			$ins_new_name = substr($ins_new_name,0,34);
			$ins_new_address = trim(str_replace("  "," ",$otherInsDetail->contact_address));
			$ins_new_address = preg_replace('/\*/','',$ins_new_address);
			$ins_new_address = substr(trim($ins_new_address),0,55);
			$fileData .= 'NM1*PR*2*'.$ins_new_name.'*****PI*'.$otherInsDetail->Payer_id.'~';					
			$fileData .= 'N3*'.$ins_new_address.'~';	
			$fileData .= 'N4*'.trim($otherInsDetail->City).'*';
			$state_name = correct_state_name($otherInsDetail->State);
			$insZip1 = preg_replace("/-/","",trim($otherInsDetail->Zip.$otherInsDetail->zip_ext));
			$fileData .= $state_name."*".$insZip1."~";
			/*
			if(!in_array(strtolower($billing_global_server_name), array('gewirtz', 'brian', 'farbowitz', 'huang', 'niec','manahan','silverman','revision_eye','northshore','westfall','cfe','thomasandthomas','north central','patel','scott','keystone','shnayder','centerforsight','cec','leps','edison','azar','LarkinChiropractic'))){
				$fileData .= "REF*FY*NOCD~"; // COMMENTED IN 5010 INSTITUTIONAL
			}*/
			
			//End 2010BC PAYER
			$pat_ids = $res[$i]['patient_id'];
			if(strlen($pat_ids) < 7){
				$pat_ids = '000000'.$pat_ids;
			}
			if($selfCode1!='18'){
				//Start 2000C PATIENT HL LOOP
				$subscriberId++;
				$fileData .= "HL*$subscriberId*2*23*0~";		
				//End 2000C PATIENT HL LOOP
				//Start 2010CA PATIENT
				
				$fileData .= 'PAT*'.$selfCode.'~';	

				$fileData .= 'NM1*QC*1*'.trim($res[$i]['patientLname']).'*'.trim($res[$i]['patientFname']).'*'.substr(trim($res[$i]['patientMname']),0,1).'**~';
				if(trim($res[$i]['patientStreet'])==''){
					$res[$i]['patientStreet'] = trim($res[$i]['patientCity']);
				}
				$fileData .= 'N3*'.preg_replace('/\*/',' ',trim($res[$i]['patientStreet'])).'~';	
				$fileData .= 'N4*'.trim($res[$i]['patientCity']).'*';
				$state_name = correct_state_name($res[$i]['patientState']);
				$fileData .= $state_name.'*'.trim($res[$i]['patientPostal_code']).'~';
	
				$subscriber_sex = $subscriberDetails->subscriber_sex;
				$p_sex = $res[$i]['patientSex'];
				$pat_DOB = preg_replace("/-/","",$res[$i]['patientDOB']);
				$fileData .= 'DMG*D8*'.$pat_DOB.'*'.$p_sex[0].'~';
			}
			//End 2010CA PATIENT
			//Start 2300 CLAIM
			$clm_type_indicator = 1;
			if(isset($ARR_CHLS_Type_Status) && trim($ARR_CHLS_Type_Status) != ''){
				$ARRpostClaimType = json_decode($ARR_CHLS_Type_Status);
				$clm_type_indicator = (isset($ARRpostClaimType->$charge_list_id) && intval($ARRpostClaimType->$charge_list_id) > 0) ? intval($ARRpostClaimType->$charge_list_id) : 1;
			}
			/*---GET PATIEN'S ICN---*/
			if(isset($ARR_CHLS_Clm_Ctrl_Num) && trim($ARR_CHLS_Clm_Ctrl_Num) != ''){
				$ARRpostClaimCtrlNum = json_decode($ARR_CHLS_Clm_Ctrl_Num);
				$clm_control_num = (isset($ARRpostClaimCtrlNum->$charge_list_id) && trim($ARRpostClaimCtrlNum->$charge_list_id) != '') ? trim($ARRpostClaimCtrlNum->$charge_list_id) : '';
			}
			
			if(intval($billing_global_clm_type_indicator_emdeon)==0 && $clm_control_num==''){
				$clm_control_num = billing_global_get_clm_control_num($res[$i]['patient_id'],$res[$i]['encounter_id'],$ICN_amount,$type);
				if((!$clm_control_num || trim($clm_control_num)=='') && $clm_type_indicator!=8){
					$clm_type_indicator = 1;
				}
			}
			if(in_array($bcbs,array('MB','MA')) && $clm_type_indicator!='8'){$clm_type_indicator = 1;$clm_control_num = '';}
			
			$CHLS_Ctrl_Num[$charge_list_id]		= $clm_control_num;
			$CHLS_Type_Status[$charge_list_id] = $clm_type_indicator;
			if($clm_type_indicator > 1 && intval($billing_global_clm_type_indicator_emdeon)>0){
				$clm_type_indicator = intval($billing_global_clm_type_indicator_emdeon);
			}
			$CLM07 = 'A';
			$CLM08 = 'Y';
			if(intval($res[$i]['enc_accept_assignment'])==1){
				$CLM07 = 'C';
				$CLM08 = 'N';
			}
			$fileData .= 'CLM*'.$pat_ids.'*'.preg_replace("/,/","",$totalAmt).'***83:A:'.$clm_type_indicator.'**'.$CLM07.'*'.$CLM08.'*Y';
			$ICN_amount = $totalAmt;
			if(trim($recieverId) != '141650868'){
				if(constant('DEFAULT_PRODUCT') != 'imwemr' && !in_array(strtolower($billing_global_server_name), array('essi','sheepshead'))){
					$fileData .= '*';			
				}
				$is_delayed = check_claim_old_days($res[$i]['date_of_service']);
				if($is_delayed>=90 && (in_array($otherInsDetail->Payer_id,$arr_NE_Medicaid_payers) || in_array($bcbs,array('MC')))){
					$db_delay_code 		= check_db_delay_code($charge_list_id);
					$delay_reason_code 	= ($db_delay_code!='') ? $db_delay_code : '7';
					$fileData .= "**********".$delay_reason_code."";
				}
			}
			$fileData .= '~';
			$tempp_disch_date = explode(' ',$res[$i]['disch_date']);
			$disch_date = $tempp_disch_date[0];
			$disch_time = substr(preg_replace("/:/","",trim($tempp_disch_date[1])),0,4);
			
			$tempp_admit_date = explode(' ',$res[$i]['admit_date']);
			$admit_date = $tempp_admit_date[0];
			$admit_time = substr(preg_replace("/:/","",trim($tempp_admit_date[1])),0,4);
			
			if($disch_time != '0000' and trim($disch_time) != ''){
				$fileData .= 'DTP*096*TM*'.preg_replace("/-/","",$disch_time).'~';
			}
			$date_of_service = preg_replace("/-/","",$date_of_service);
			$fileData .= 'DTP*434*RD8*'.$date_of_service.'-'.$date_of_service.'~';
			
			if($admit_date != '0000-00-00'){
				$fileData .= 'DTP*435*DT*'.preg_replace("/-/","",$admit_date).$admit_time.'~';
			}
			if(in_array(strtolower($billing_global_server_name), array('austineeye'))){
				if(in_array($InsuranceComDetails['0']['Payer_id'],array('66006'))){
					$fileData .= 'CL1*9*1*01~';
				}else{
					$fileData .= 'CL1*9*9*01~';
				}
			}else{
				$fileData .= 'CL1*9*1*01~';
			}

			/***CLAIM SUPPLEMENT INFORMATION (LOOP 2300)*****/
			$res[$i]['report_type_code']		= trim($res[$i]['report_type_code']);
			$res[$i]['transmission_code']		= trim($res[$i]['transmission_code']);
			$res[$i]['control_no']				= trim($res[$i]['control_no']);
			if($res[$i]['report_type_code']!='' && $res[$i]['transmission_code']!='' && $res[$i]['control_no']!=''){
				$fileData .= 'PWK*'.$res[$i]['report_type_code'].'*'.$res[$i]['transmission_code'].'***AC*'.$res[$i]['control_no'].'~';
			}
						
			/*-Prior Authorization or Referral Number (loop 2300)-*/
			if(trim($referral) != ''){
				$fileData .= "REF*9F*$referral~";
			}
			$ref_g1_added = false;
			if(trim($res[$i]['auth_no'])!=''){
				$fileData .= "REF*G1*".trim($res[$i]['auth_no'])."~";
				$ref_g1_added = true;
			}
			$clm_control_num = clean_n_padd_claim_control_num($clm_control_num);
			if($clm_control_num != false && trim($clm_control_num)!='' && ($clm_type_indicator=='7' || $clm_type_indicator=='8')){
				$fileData .= "REF*F8*".$clm_control_num."~";
				if($primaryFile == true){$insType = 'claim_ctrl_pri';}else{$insType = 'claim_ctrl_sec';}
				update_claim_control_number($insType,$res[$i]['encounter_id'],$clm_control_num);
			}
			
			/**---Claim Identifier for Transmission Intermediaries---**/
			$fileData .= "REF*D9*".$res[$i]['encounter_id']."~";
			
			//End 2300 CLAIM
			
			$ins_encounter_id = $res[$i]['encounter_id'];

			$approval = array_unique(array_merge($approval1,$approval2));
			
			if(count($approval)>0 && !$ref_g1_added){
				foreach($approval as $approval_val){
					$fileData .= "REF*G1*$approval_val~";	
				}
			}
			
			//MEDICAL RECORD NUMBER
			if(in_array(strtolower($billing_global_server_name),array('manahan','essi','tyson','waltham','ocean'))){
				$fileData .= "REF*EA*".$ins_encounter_id."~";
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
			
			if(count($value_codes_rates)>0 && in_array(strtolower($billing_global_server_name),array('sheepshead','mackool','liasc','shoreline','hammad_iasc','manhattan'))){
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
			
			//Start 2310A ATTENDING PROVIDER
			$fileData .= 'NM1*71*1*'.trim(preg_replace("/\'/","",$res[$i]['usersLname'])).'*'.trim(preg_replace("/\'/","",$res[$i]['usersFname'])).'****XX*';
			$fileData .= preg_replace("/-/","",$res[$i]['users_npi']).'~';
			if(in_array($bcbs,array('BL','MC')) === false){
				$fileData .= "REF*$bcbsRef*$BlueShieldId~";
			}
			//End 2310A ATTENDING PROVIDER
			
			//Start 2310B OPERATING PROVIDER
			//-- Reffering Physicain Details ---------
			if(in_array($bcbs,array('MC')) === false || in_array(strtolower($billing_global_server_name), array('mackool'))){
				if($otherInsDetail->Payer_id<>'12402'){
					$fileData .= 'NM1*72*1*'.preg_replace("/\'/","",$res[$i]['usersLname']).'*'.preg_replace("/\'/","",$res[$i]['usersFname']).'****XX*';
					$fileData .= preg_replace("/-/","",$res[$i]['users_npi']).'~';
					if(in_array($otherInsDetail->Payer_id,$arr_BL_payers) === false){
						$fileData .= "REF*$bcbsRef*$BlueShieldId~";
					}
				}
			}
			//-- Renderring Physicain Details ---------
			//End 2310B OPERATING PROVIDER
			
			//--- START LOOP 2310D ----
			$pos_facility_name = trim(substr($posFacilityDetail->facility_name,0,60));
			$pos_facility_npi = $posFacilityDetail->npiNumber;
			if(in_array($otherInsDetail->Payer_id,$arr_BL_payers) === true){
			//	$fileData .= "NM1*82*1*".$res[$i]['usersLname']."*".$res[$i]['usersFname']."****XX*".$res[$i]['users_npi']."~";
				//$fileData .= "REF*$bcbsRef*$BlueShieldId~";
			}else if(!$ClearingHouse || $ClearingHouse['abbr']!='PI'){
				$fileData .= "NM1*82*1*$pos_facility_name*".trim(substr($posFacilityDetail->facility_name,0,35))."****XX*$pos_facility_npi~";
			}
			$pos_facility_address = preg_replace('/\*/',' ',trim($posFacilityDetail->pos_facility_address));
		//	$fileData .= "N3*$pos_facility_address~";
			$pos_facility_city = $posFacilityDetail->pos_facility_city;			
			$pos_facility_state = correct_state_name($posFacilityDetail->pos_facility_state);
			$pos_facility_zip = $posFacilityDetail->pos_facility_zip.$posFacilityDetail->zip_ext;
		//	$fileData .= "N4*$pos_facility_city*$pos_facility_state*$pos_facility_zip~";

			//--- END LOOP 2310D ----
			
			//2310F - Referring Provider LOOP
			if(in_array(strtoupper($otherInsDetail->Payer_id),array('61101','11324')) && $referral==''){
				$ref_phy_not_required = 1;
			}
			if($ref_phy_not_required == '0'){
				if(in_array($bcbs,array('MC')) === true || ($billing_global_server_name != 'mackool')){
					//-- REFERRING PHYSICIAN DETAILS ---------
					$fileData .= 'NM1*DN*1*'.$reffPhysicianLname.'*'.$reffPhysicianFname.'*'.substr($reffPhysicianMname,0,1).'***XX*'.preg_replace("/-/","",$npiNumber).'~';;
					if(!in_array(strtolower($billing_global_server_name), array('sheepshead'))){
						if(trim($recieverId) != '141650868') $fileData .= "REF*$bcbsRef*$BlueShieldId~";
					}
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
					$selfCode2 = "G8";						
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Father'){
					$selfCode2 = "G8";						
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Guardian'){
					$selfCode2 = "G8";
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Employee'){
					$selfCode2 = '20';
				}
				elseif($subscriberDetails2->subscriber_relationship == 'Spouse'){
					$selfCode2 = '01';
				}
				else{
					$selfCode2 = "G8";
				}
				
				$secCode = 'CI';
				if(in_array($otherInsDetail2->Payer_id,$payer_arr) === true){
					$secCode = 'MB';
				}
				else if(in_array($otherInsDetail2->Payer_id,$medicaid_arr) === true){
					$secCode = 'MC';
				}
				else if(in_array($otherInsDetail2->Payer_id,$arr_BL_payers) === true){
					$secCode = 'BL';
				}
				else if($otherInsDetail2->claim_type == "1"){
					$secCode = 'MB';
				}
				if($otherInsDetail2->ins_type != '' && strlen(trim($otherInsDetail2->ins_type))==2){
					$secCode = strtoupper(trim($otherInsDetail2->ins_type));
				}
				
				$groupNumber2 = trim($subscriberDetails2->group_number);
				$subscriberDetails2->policy_number = preg_replace("/[^A-Za-z0-9]/","",$subscriberDetails2->policy_number);
				if($groupNumber2 == '' || strtolower($groupNumber2) == 'none'){
					if(!in_array(strtolower($billing_global_server_name),array('manahan'))){
						$groupNumber2 = NULL;
					}
				}
				$plan_name2 = trim($subscriberDetails2->plan_name);
				if(($plan_name2 == '' || strtolower($plan_name2) == 'none') && strtolower($subscriberDetails2->group_number) != 'none'){
					$plan_name2 = $subscriberDetails2->group_number;
				}
				
				//Start 2320 OTHER SUBSCRIBER INFORMATION
				if(strtoupper($SecondType[0]) == 'S'){
					if($selfCode2 == 18){
				//		$selfCode2 = '';
					}
				}
				$send_icn = false;$ins_type_code = '';if(in_array($secCode,array('MB','MA'))){$send_icn = true;$ins_type_code = '47';}
				if(in_array(strtolower($billing_global_server_name), array('sheepshead','safar','essi','palisades'))){$ins_type_code = '';}
				if(in_array(strtolower($billing_global_server_name), array('liasc','liesc','leps')) && in_array($secCode,array('MB','MA'))){$ins_type_code = '16';}
				$fileData .= 'SBR*'.substr($SecondType,0,1).'*'.$selfCode2.'*'.$groupNumber2.'*'.$plan_name2.'*'.$ins_type_code.'****'.$secCode.'~';
				
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
					$arr_cas_type_ptChLsDetailIdWise = $arr_cas_code_ptChLsDetailIdWise = $arr_cas_amount_ptChLsDetailIdWise = $arr_cas_date_ptChLsDetailIdWise = array();
					$other_payment_detail_arr = array();
					$arr_claimLevel_writeoff = array();
					for($w=0;$w<count($writeOffQryRes);$w++){
						//$insPaidAmountArr[] = $writeOffQryRes[$w]['write_off_amount'];
						$charge_list_detail_id = $writeOffQryRes[$w]['charge_list_detail_id'];
						$arr_cas_write_off[$charge_list_detail_id][] = $writeOffQryRes[$w]['write_off_amount'];
						$arr_claimLevel_writeoff[] = $writeOffQryRes[$w]['write_off_amount'];
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
					$arr_claimLevel_deductible = array();
					$deduct_qry = "select deduct_amount AS write_off_amount, charge_list_detail_id, 
								date_format(deduct_date, '%Y%m%d') as write_off_date 
								from payment_deductible where deduct_ins_id = '$provider_id2' 
								and charge_list_detail_id in($detailIdStr) and delete_deduct = '0'";
					if(empty($not_cov_proc_str) === false){
						$deduct_qry .= " and charge_list_detail_id not in ($not_cov_proc_str)";
					}
					$deductQryRs = imw_query($deduct_qry);
					while($deductQryRes = imw_fetch_assoc($deductQryRs)){
						//$insPaidAmountArr[] = $writeOffQryRes[$w]['write_off_amount'];
						$charge_list_detail_id = $deductQryRes['charge_list_detail_id'];
						//$other_payment_detail_arr[$charge_list_detail_id][] = $deductQryRes[$d]['write_off_amount'];
						$deductible_amount_arr[$charge_list_detail_id][] = $deductQryRes['write_off_amount'];
						$arr_claimLevel_deductible[] = $deductQryRes['write_off_amount'];
						$writeOffDate = $deductQryRes['write_off_date'];
					}
					if(empty($insPaidDate) == true){
						$insPaidDate = $writeOffDate;
					}
					
					$insPaidAmount = array_sum($insPaidAmountArr);
					$fileData .= 'AMT*D*'.preg_replace("/,/","",number_format($insPaidAmount,2)).'~';
					
					//--- TOTAL APPROVED AMOUNT ---
					$totalAmt = preg_replace('/,/','',$totalAmt) - $writeOffAmtTotal;
					$totalAmt = preg_replace('/,/','',number_format($totalAmt,2));					
					
				//	$fileData .= 'AMT*EAF*'.$totalAmt.'~';
				}
				
				$DOB2 = preg_replace("/-/","",$subscriberDetails2->subscriber_DOB);							
				$subscriber_sex2 = $subscriberDetails2->subscriber_sex;
//				$fileData .= 'DMG*D8*'.$DOB2.'*'.substr($subscriber_sex2,0,1).'~'; not required in 5010.
				$fileData .= 'OI***N***Y~';
				//End 2320 OTHER SUBSCRIBER INFORMATION
				
				//Start 2330A OTHER SUBSCRIBER NAME
				$fileData .= 'NM1*IL*1*'.trim($subscriberDetails2->subscriber_lname).'';
				$fileData .= '*'.trim($subscriberDetails2->subscriber_fname).'';
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
					$fileData .= 'N4*'.trim($subscriberDetails2->subscriber_city).'~';
					$fileData .= 'N3*'.trim($subscriberDetails2->subscriber_city).'*';
				}
				$state_name = correct_state_name($subscriberDetails2->subscriber_state);
				$fileData .= $state_name.'*'.trim($subscriberDetails2->subscriber_postal_code.$subscriberDetails2->zip_ext).'~';
						
				//End 2330A OTHER SUBSCRIBER NAME
				
				//Start 2330B OTHER PAYER
				//--- INSURANCE COMPANY NAME SHOULD BE LESS THAN 35 CHR ---
				$ins_new_name2 = trim($otherInsDetail2->name);
				$ins_new_name2 = substr($ins_new_name2,0,34);
				$fileData .= 'NM1*PR*2*'.$ins_new_name2.'*****PI*'.$otherInsDetail2->Payer_id.'~';
				if(!$ClearingHouse || $ClearingHouse['abbr']!='PI'){
					$fileData .= 'REF*FY*'.$otherInsDetail2->Payer_id.'~';
				}
				if(trim($Texonomy)!=''){
					$fileData .= 'REF*G1*'.trim($Texonomy).'~';
				}
				if(empty($insPaidDate) === false){
					$patICNdate = $insPaidDate;
					$insPaidDate = preg_replace('/-/','',$insPaidDate);
					//$fileData .= "DTP*573*D8*$insPaidDate~";
				}
				
				/*---GET PATIEN'S ICN---*/
				if($send_icn && empty($patICNdate) === false){
					//$pat_icn = get_patient_icn($res[$i]['patient_id'],$patICNdate,$otherInsDetail2->Payer_id,$ICN_amount);
					$pat_icn = billing_global_get_clm_control_num($res[$i]['patient_id'],$res[$i]['encounter_id'],$ICN_amount,$secondType);
					if($pat_icn != false && strlen($pat_icn>5)){
						$pat_icn = clean_n_padd_claim_control_num($pat_icn);
						$fileData .= "REF*F8*".$pat_icn."~";
					}
				}
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
													
				$procCharge = $patientChargeDetails[$u]['totalAmount'];
				if($batchFileStatus == true){
					if(strlen($procCharge) == 4){
						$procCharge = str_replace('0.','.',$procCharge);
					}
				}
				
				//--- REV CODE DETAILS --- loop 2400
				$rev_qry_rs = imw_query("select r_code from revenue_code where r_id = '".$patientChargeDetails[$u]['rev_code']."'");
				$rev_qry_res = imw_fetch_assoc($rev_qry_rs);
				$rev_code = $rev_qry_res["r_code"];
				$proc_units = $patientChargeDetails[$u]['units'];
				if(substr($proc_units,-3)=='.00'){$proc_units = intval($proc_units);}

				/*if(in_array($otherInsDetail->Payer_id, array('22099','61101')) && $proc_admin_cmnt != ''){
					$jcode_cpt_desc = '';
				}*/
				if(!$this->is_not_otherwise_classified_code($patientChargeDetails[$u]['cpt4_code'])){$jcode_cpt_desc = '';$proc_admin_cmnt ='';}

				$fileData .= 'SV2*'.$rev_code.'*HC:'.$patientChargeDetails[$u]['cpt4_code'].$modifierCodes.$jcode_cpt_desc.'*'.$procCharge.'';
				$fileData .= '*UN*'.$proc_units.'~';
				$fileData .= 'DTP*472*D8*'.preg_replace("/-/","",$res[$i]['date_of_service']).'~';
				
				/*-----Jcode requirement------*/
				$LIN_segment = '';
				if(in_array($firstCharOfCPT,array('j','l','6')) && $proc_admin_cmnt != '' && (in_array($otherInsDetail->Payer_id, array('22099','61101')) || $otherInsDetail->transmit_ndc=='1')){
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
					
					//--- GET PROCEDURE PAID AMOUNT ---
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
					$balForProc = (($procCharge - $AdjustmentAmount)-$deductible4Proc) - $paidForProcByPriIns;
					
					$cas_codes_string = $this->get_835_cas_codes($charge_list_detail_id);
					/*-------------*/
					if($insPaidAmount >= 0 && $paidForProcByPriIns >=0){
						$fileData .= 'SVD*'.$otherInsDetail2->Payer_id.'*'.$paidForProcByPriIns.'*';
						$fileData .= 'HC:'.$patientChargeDetails[$u]['cpt4_code'].''.$modifierCodes.'*'.$rev_code.'*1~';
						if($cas_codes_string){
							$fileData .= $cas_codes_string;
						}else if(($AdjustmentAmount != '' && $AdjustmentAmount >0) || ($deductible4Proc != '' && $deductible4Proc > 0) || ($balForProc != '' && $balForProc > 0)){
							$CAS_codes = '';
							if($AdjustmentAmount != '' && $AdjustmentAmount >0){//Charges exceed our fee schedule or maximum allowab...
								$CAS_codes .= "CAS*CO*45*".preg_replace('/,/','',number_format($AdjustmentAmount,2))."*1~";
							}
							if(($deductible4Proc != '' && $deductible4Proc > 0) || ($balForProc != '' && $balForProc > 0)){
								$CAS_PR_codes = 'CAS*PR';
								if($deductible4Proc != '' && $deductible4Proc > 0){//deductible available for this proc.
									$CAS_PR_codes .= "*1*".preg_replace('/,/','',number_format($deductible4Proc,2))."*1";
								}
								if($balForProc != '' && $balForProc > 0){
									$CAS_PR_codes .= "*2*".preg_replace('/,/','',number_format($balForProc,2))."*1";
								}
								$CAS_PR_codes .= '~';
								$CAS_codes .= $CAS_PR_codes;
							}
							$fileData .= $CAS_codes;
						}
						
						if(($paidForProcByPriIns>=0 || $AdjustmentAmount>0)){
							if(empty($insPaidDate) === true){
								$insPaidDate = $patientChargeDetails[$u]['write_off_date'];
							}
													
							$insPaidDate = preg_replace('/-/','',$insPaidDate);
							if(strlen($insPaidDate)==8){
								if(trim($insPaidDate)=='00000000'){
									if(isset($cas_date_adjudication)){
										$cas_date_adjudication = preg_replace('/-/','',$insPaidDate);
										$insPaidDate = $cas_date_adjudication;
									}	
								}
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
			$inValidChargeListId[] = $res[$i]['charge_list_id'];					
		}
		$i++;
	}
	
	$fileData = preg_replace('/new jersey/','nj',strtolower($fileData));
	$fileData = preg_replace('/\n/',', ',$fileData);
	if($files == true){
		$transectionCount = 1;
		//---- UPDATE CLAIM FILE DATA -----------
		$segmentCountArr = preg_split('/~/',$fileData);
		$fileData .= 'SE*'.(count($segmentCountArr)-2).'*'.$Transaction_set_unique_control.'~';
		$fileData .= 'GE*'.$transectionCount.'*'.$header_control_identifier.'~';
		$fileData .= 'IEA*1*'.$InterchangeControlNumber.'~';			
		
		if(count($insCompanyId)>0){
			$insCompanyIds = join(",",array_unique($insCompanyId));
			$encounter_ids = join(',',$encounter_id);
			$pcld_ids = join(",",array_unique($validChargeListDetailsId));
			$segmentCountstarts = join(',',$segmentCountstart);
			$segmentCountEnds = join(',',$segmentCountEnd);
		}
		
		$fileData = preg_replace('/[^a-zA-Z0-9_*\- ~.:\^\']/',' ',$fileData);

		$qry = "UPDATE batch_file_submitte SET 
				file_data = '".strtoupper(addslashes($fileData))."', 
				encounter_id = '$encounter_ids', 
				pcld_id = '$pcld_ids', 
				clm_type_indicator = '".htmlentities(json_encode($CHLS_Type_Status))."', 
				claim_ctrl_num='".htmlentities(json_encode($CHLS_Ctrl_Num))."', 
				segment_start = '$segmentCountstarts', 
				segment_end = '$segmentCountEnds', 
				ins_company_id = '$insCompanyIds' 
				WHERE batch_file_submitte_id = '$fileNameID'";
		$updateQry = imw_query($qry);
		if($updateQry){batch_file_log($fileNameID,"regenrate");}
		//--- CREATE CLAIM FILE ----
		file_put_contents("../batchfiles/$fileName",strtoupper($fileData));
		$getData = true;
		
		if(empty($err) === true){
			$err = 'File Re-Generate Successfully.';
		}
	}								
}
?>