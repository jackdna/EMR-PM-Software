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
$facility_id=$_SESSION['login_facility']; 
if($Era_835_proc_detail_Id>0){
	$whr_id=" 835_Era_proc_Id='$Era_835_proc_detail_Id' ";
}else{
	$whr_id=" 835_Era_Id='$Era_835_Id' ";
}
if($payment_method==""){
	$payment_method="Check";
}
//----------------------------------------- START PROCESS -----------------------------------------//
$getPatProcDetailsStr = "SELECT * FROM era_835_proc_details WHERE  $whr_id and SVC_provider_pay_amt<0 and ERA_patient_details_id in($sel_enc_chk_id_str)";
$getPatProcDetailsQry = imw_query($getPatProcDetailsStr);				
while($getPatProcDetailsRow = imw_fetch_array($getPatProcDetailsQry)){
	$status = '';
	$procClaimId = $getPatProcDetailsRow['835_Era_proc_Id'];
	$ERA_patient_details_id = $getPatProcDetailsRow['ERA_patient_details_id'];
	$DOS = $getPatProcDetailsRow['DTM_date'];
	$SVC_proc_code = $getPatProcDetailsRow['SVC_proc_code'];
	$SVC_mod_code = $getPatProcDetailsRow['SVC_mod_code'];
	$SVC_proc_charge = $getPatProcDetailsRow['SVC_proc_charge'];			
	$SVC_provider_pay_amt = $getPatProcDetailsRow['SVC_provider_pay_amt'];
	$AMT_amount = $getPatProcDetailsRow['AMT_amount'];
	$DTM_date = $getPatProcDetailsRow['DTM_date'];
	$CAS_type = $getPatProcDetailsRow['CAS_type'];
	$CAS_reason_code = $getPatProcDetailsRow['CAS_reason_code'];
	$CAS_amt = $getPatProcDetailsRow['CAS_amt'];
	$postedStatus = $getPatProcDetailsRow['postedStatus'];
	$REF_prov_identifier = $getPatProcDetailsRow['REF_prov_identifier'];			
	$REF_prov_identifier = @preg_replace('/\s+/','',$REF_prov_identifier);
	// GET MOA QUALIFIER AND Processed By Details
		$qry = imw_query("select * from era_835_patient_details where ERA_patient_details_id = '$ERA_patient_details_id'");
		$moaDetails = imw_fetch_object($qry);
		$MOAQualifier = $moaDetails->MOA_qualifier;
		$CLP_claim_status = $moaDetails->CLP_claim_status;
		$CLP_claim_submitter_id = $moaDetails->CLP_claim_submitter_id;
		$CLP_payer_claim_control_number = $moaDetails->CLP_payer_claim_control_number;
		
		// GET MOA QUALIFIER AND Processed By Details				
		$mcrPos = strpos($REF_prov_identifier, 'MCR');
		if($mcrPos){
			
		}else{
			$qry = imw_query("select * from era_835_patient_details where CLP_payer_claim_control_number = '$CLP_payer_claim_control_number'");
			$moaDetails2 = imw_fetch_object($qry);
			$CLP_claim_status = $moaDetails2->CLP_claim_status;
		}
		if($CLP_claim_status != 1 && $CLP_claim_status!=2 && $CLP_claim_status!=3 && $CLP_claim_status!=19 && $CLP_claim_status!=20 && $CLP_claim_status!=21 && $CLP_claim_status!=22) 
			continue;
		$paymentOf = '';
		if($CLP_claim_status == 1 || $CLP_claim_status == 19){
			$insCoType = 'Primary';
			$paymentOf = 'primary_paid';
			$ins_company_no=1;
		}else if($CLP_claim_status == 2 || $CLP_claim_status == 20){
			$insCoType = 'Secondary';
			$paymentOf = 'secondary_paid';
			$ins_company_no=2;
		}else if($CLP_claim_status == 3 || $CLP_claim_status == 21){
			$insCoType = 'Tertiary';
			$paymentOf = 'tertiary_paid';
			$ins_company_no=3;
		}	
	
	if($mcrPos){
		// REF*6R EXISTS
		$encounter_id = trim(substr($REF_prov_identifier, 0, $mcrPos));
		$restStr = substr($REF_prov_identifier, $mcrPos+3);
		if(strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator)){
			$tsucPos = strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator);
			$tsucId = $tsucPos+6;
		}else if(strpos($restStr, '_TSUC_')){
			$tsucPos = strpos($restStr, '_TSUC_');
			$tsucId = $tsucPos+6;
		}else if(strpos($REF_prov_identifier, 'TSUC')){
			$tsucPos = strpos($restStr, 'TSUC');
			$tsucId = $tsucPos+4;
		}
		if($tsucId){
			$chargeListDetailId = substr($restStr, 0, $tsucPos);
			$tsuc_identifier = substr($restStr, $tsucId);					
			if(strpos($tsuc_identifier, ',')){
				$tsuc_identifier = trim(substr($tsuc_identifier, 0, strpos($tsuc_identifier, ',')));
			}
			if(!$insCoType && is_numeric($tsuc_identifier)){
				//GET BATCH FILE INFO
				$getBatchInfoQry = imw_query("SELECT ins_company_id, ins_comp FROM batch_file_submitte
											WHERE Transaction_set_unique_control = '$tsuc_identifier'");
				$getBatchInfoRow = imw_fetch_assoc($getBatchInfoQry);
				$ins_company_id = $getBatchInfoRow['ins_company_id'];
				$ins_comp = $getBatchInfoRow['ins_comp'];
				if($ins_comp == 'primary'){
					$insCoType = 'Primary';
				}else if($ins_comp == 'secondary'){
					$insCoType = 'Secondary';
				}
			}
		}else{
			$chargeListDetailId = '';
		}
	}else{
		// REF*6R DOES NOT EXISTS
		$encounter_id = '';
		$chargeListDetailId = '';
		
		// GET ENCOUNTER AND CHARGE LIST DETAILS BASED ON PATIENT ID
		if(is_numeric($CLP_claim_submitter_id)){
			// GET PROC ID FROM CPT4_CODE
			$qry = imw_query("select * from cpt_fee_tbl where cpt4_code = '$SVC_proc_code'");
			$getCptFeeId = imw_fetch_object($qry);
			$cpt_fee_id = $getCptFeeId->cpt_fee_id;							
			// GET PROC ID FROM CPT4_CODE
			
			//GET MODIFIERS ID
			$qry = imw_query("select * from modifiers_tbl where mod_prac_code = '$SVC_mod_code'");
			$getModID = imw_fetch_object($qry);
			$modifiersId = $getModID->modifiers_id;
			//GET MODIFIERS ID
			
			$getChargeListDetailsStr = "SELECT * FROM 
										patient_charge_list a,
										patient_charge_list_details b
										WHERE b.del_status='0' and a.patient_id = '$CLP_claim_submitter_id'
										AND a.charge_list_id = b.charge_list_id
										AND a.date_of_service = '$DOS'
										AND b.procCode = '$cpt_fee_id'";
			if($modifiersId){
				$getChargeListDetailsStr.=" AND b.modifier_id1 = '$modifiersId'";
			}
			$getChargeListDetailsQry = imw_query($getChargeListDetailsStr);
			$countRows = imw_num_rows($getChargeListDetailsQry);
			if($countRows){
				while($getChargeListDetailsRows = imw_fetch_assoc($getChargeListDetailsQry)){
					$encounterId = $getChargeListDetailsRows['encounter_id'];
					$charge_list_id = $getChargeListDetailsRows['charge_list_id'];
					$listChargeDetailId = $getChargeListDetailsRows['charge_list_detail_id'];	
					$primaryInsuranceCoId = $getChargeListDetailsRows['primaryInsuranceCoId'];
					$secondaryInsuranceCoId = $getChargeListDetailsRows['secondaryInsuranceCoId'];
					$tertiaryInsuranceCoId = $getChargeListDetailsRows['tertiaryInsuranceCoId'];						
				}
				if($countRows==1){
					$encounter_id = $encounterId;
					$chargeListDetailId = $listChargeDetailId;
				}
			}
			if($insCoType=="" && $primaryInsuranceCoId>0 && $secondaryInsuranceCoId == 0 && $tertiaryInsuranceCoId == 0){
				$insCoType = 'Primary';
				$paymentOf = 'primary_paid';
				$ins_company_no=1;
			}
		}else{
			continue;
		}
		// GET ENCOUNTER AND CHARGE LIST DETAILS BASED ON PATIENT ID
	}
	$listChargeDetailId = trim($chargeListDetailId);			
	if(!$encounter_id || !$insCoType || !$listChargeDetailId) continue;

	//GET INSURANCE COMPANIES
		$getInsStr = "select b.provider as insCoId from patient_charge_list a, insurance_data b
						where a.encounter_id = '$encounter_id'
						AND a.del_status='0' and a.case_type_id = b.ins_caseid AND b.pid = a.patient_id
						AND b.type = '$insCoType' AND b.actInsComp = 1 and b.provider > 0";
		$getInsQry = imw_query($getInsStr);
		$getInsRow = imw_fetch_array($getInsQry);
		$insuranceCoId = $getInsRow['insCoId'];
	//GET INSURANCE COMPANIES
	
	// GET ENCOUNTER DETAILS.
		$qry = imw_query("select * from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'");
		$getAccountDetails = imw_fetch_assoc($qry);
		if(is_array($getAccountDetails))
			extract($getAccountDetails);
	// GET ENCOUNTER DETAILS.
	
	if(($listChargeDetailId!='') || ($listChargeDetailId!=0)){	

	$getClpCasStr = "SELECT * FROM era835clpcas WHERE 
								era835Id = '$Era_835_Id'
								AND ERAPatientdetailsId = '$ERA_patient_details_id'";
	$getClpCasQry = imw_query($getClpCasStr);
	//GET BALANC FROM ACCOUNTING
		$listChargeDetailId = @preg_replace('/\s+/','', $listChargeDetailId);
		// GET MOA QUALIFIER
		if(in_array($CLP_claim_submitter_id,$plb_patient_exp) || imw_num_rows($getClpCasQry)>0){	
		}else{
			if($postedStatus=='Not Posted'){		
					if($SVC_provider_pay_amt<0){	
						$amt_without_minus=substr($SVC_provider_pay_amt,1);	
					
						$postedStatus = 'Paid';
						unset($arrayRecord);
						$arrayRecord['encounter_id'] = $encounter_id;
						$arrayRecord['paid_by'] = 'Insurance';
						$arrayRecord['payment_amount'] = $amt_without_minus;
						$arrayRecord['payment_mode'] = $payment_method;
						$arrayRecord['checkNo'] = $checkNo;
						$arrayRecord['date_of_payment'] = $payDate;
						$arrayRecord['operatorId'] = $operatorId;
						$arrayRecord['insProviderId'] = $insuranceCoId;
						$arrayRecord['insCompany'] = $ins_company_no;
						$arrayRecord['paymentClaims'] = 'Negative Payment';
						$arrayRecord['transaction_date'] = date('Y-m-d');
						$arrayRecord['facility_id'] = $facility_id;
						$addPaymentId = addRecords($arrayRecord, 'patient_chargesheet_payment_info');
						
						//INSERT DETAIL PAYMENT
						unset($arrayRecord);
						$arrayRecord['payment_id'] = $addPaymentId;
						$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
						$arrayRecord['paidBy'] = 'Insurance';
						$arrayRecord['paidDate'] = $payDate;
						$arrayRecord['paidForProc'] = $amt_without_minus;
						$arrayRecord['overPayment'] = 0;
						$arrayRecord['CAS_type'] = $casTypeNew;
						$arrayRecord['CAS_code'] = $CASReasonCodeArr[$inc];
						$arrayRecord['operator_id'] = $_SESSION['authId'];
						$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
						$addDetailPayments = addRecords($arrayRecord, 'patient_charges_detail_payment_info');									
						//	START UPDATE PATIENT CHARGE LIST DETAIL TABLE
						$status = 'Paid';
						set_payment_trans($encounter_id);
					}
				}
			}
			if($status == 'Paid'){
				unset($arrayRecord);
				$arrayRecord['postedStatus'] = 'Posted';								
				$updatePostedStatus = UpdateRecords($procClaimId,'835_Era_proc_Id',$arrayRecord,'era_835_proc_details');
			}
	}
}
//----------------------------------------------- START PROCESS -----------------------------------------------//
?>
