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
$without_pat="yes"; 
$title = "ERA Post Payments";
require_once("../accounting/acc_header.php");
require_once("../../library/classes/billing_functions.php");
require_once("../../interface/common/assign_new_task.php");
$send_era_id=$_REQUEST['send_era_id'];
$send_era_chk_id=$_REQUEST['send_era_chk_id'];
$send_era_pat_id=$_REQUEST['send_era_pat_id'];
if( !$send_era_pat_id ) $send_era_pat_id = 0;
$pay_location=$_SESSION['login_facility'];
$operatorId = $_SESSION['authId'];
?>
<?php
function era_reason_amt($case_type,$case_reason,$case_amt,$cas_action_arr){
	$CAS_typeArr = explode(", ",$case_type);
	$CAS_amtArr = explode(", ",$case_amt);
	$CAS_reason_codeArr = explode(", ", $case_reason);
	$set_cas_type_arr=array();
	$old_cas_type="";
	foreach($CAS_typeArr as $cas_key => $cas_type){	
		$casTypeChkNew = @preg_replace('/\s+/','', $cas_type);
		$CASReasonCodeChkArr[$cas_key] = @preg_replace('/\s+/','', $CAS_reason_codeArr[$cas_key]);
		$typeCodeChk = $casTypeChkNew.' '.$CASReasonCodeChkArr[$cas_key];
		if($cas_key>0){
			$comma_br=", ";
		}
		$cas_reason = $cas_reason.$comma_br.$CAS_typeArr[$cas_key].' '.$CAS_reason_codeArr[$cas_key];
		$cas_amount = $cas_amount.$comma_br.numberFormat($CAS_amtArr[$cas_key],2);
		
		if(trim($CAS_typeArr[$cas_key])>0){
			$set_cas_type_arr[100]=$old_cas_type;
		}else{
			$set_cas_type_arr[]=$CAS_typeArr[$cas_key];
			$old_cas_type=$CAS_typeArr[$cas_key];
		}
		
		if(in_array($typeCodeChk,$cas_action_arr['Deductible'][0])){
			$deduct_amount = $deduct_amount+$CAS_amtArr[$cas_key];
		}
		if(in_array($typeCodeChk,$cas_action_arr['Write Off'][1]) || in_array($typeCodeChk,$cas_action_arr['Write Off'][0])){
			$writeoff_amount = $writeoff_amount + $CAS_amtArr[$cas_key];
		}
	}
	$ret_arr['era_writeoff']=$writeoff_amount;
	$ret_arr['era_deduct']=$deduct_amount;
	$ret_arr['era_reason']=$cas_reason;
	$ret_arr['era_amount']=$cas_amount;
	$ret_arr['era_pat_res_amount']=$pat_res_amount;
	return $ret_arr;
}
if($_REQUEST['era_action']=="process"){
	$ERA_FILE_Id=$_REQUEST['send_era_id'];
	$sel_enc_chk_id=$_REQUEST['era_proc_id'];
	$sel_enc_chk_id_str=implode(',',$sel_enc_chk_id);
	
	$matchUnits = $_REQUEST['matchUnits'];
	$use_era_allowed = $_REQUEST['use_era_allowed'];
	$write_off_code=$_REQUEST['write_off_code'];
	$payment_method=$_REQUEST['payment_method'];
	$dateOfPayment = $_REQUEST['date1'];
	$plb_patient_txt=$_REQUEST['plb_patient_txt'];
	$plb_patient_exp=explode(',',$plb_patient_txt);
	
	$qry=imw_query("select * from cas_reason_code");
	while($row=imw_fetch_array($qry)){
		$cas_code=trim($row['cas_code']);
		$cas_action_arr[$row['cas_action_type']][$row['cas_update_allowed']][]=$cas_code;
		if($row['cas_adjustment_negative']>0){
			$cas_adjust_negative_arr[]=$cas_code;
		}
	}

	//find PQRI and G code id
	$chk_cpt_cat_id=array();
	$sel_pqri = imw_query("select * from cpt_category_tbl where cpt_category like 'PQRI%' or cpt_category like 'G%'");
	while($fet_pqr_gcode = imw_fetch_array($sel_pqri)){
		$chk_cpt_cat_id[] = $fet_pqr_gcode['cpt_cat_id'];
	}

	//GET DETAILS OF ELECTRONIC FILE
	$getDetailsForTrancactionsStr = "SELECT * FROM era_835_details WHERE electronicFilesTblId = '$ERA_FILE_Id'";
	$getDetailsForTrancactionsQry = imw_query($getDetailsForTrancactionsStr);
	while($getDetailsForTrancactionsRows = imw_fetch_assoc($getDetailsForTrancactionsQry)){
		extract($getDetailsForTrancactionsRows);		
		$Era_835_Id = $getDetailsForTrancactionsRows['835_Era_Id'];
		// IF SELECTED CHECK IS POSTED		
		if($chkNumberDetails && ($Era_835_Id!=$EraId)){
			continue;
		}
		// IF SELECTED CHECK IS POSTED
		$payDate = $chk_issue_EFT_Effective_date;
		if($dateOfPayment){
			$payDate = $dateOfPayment;
			$payDate = getDateFormatDB($payDate);
		}
		$checkNo = $TRN_payment_type_number;

		//GET ALL DETAILS OF TRANSACTION
		unset($CASTypeArray);
		unset($CASReasonCodeArr);
		unset($CASAmtArr);
		$status = '';		
		
		$getPatProcDetailsStr = "SELECT * FROM era_835_proc_details WHERE `835_Era_Id` = '$Era_835_Id' and 835_Era_proc_Id in($sel_enc_chk_id_str)";
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
				//if($SVC_proc_charge<0) continue;
			$AMT_amount = $getPatProcDetailsRow['AMT_amount'];
			$DTM_date = $getPatProcDetailsRow['DTM_date'];
			$CAS_type = $getPatProcDetailsRow['CAS_type'];
			$CAS_reason_code = $getPatProcDetailsRow['CAS_reason_code'];
			$CAS_amt = $getPatProcDetailsRow['CAS_amt'];
			$postedStatus = $getPatProcDetailsRow['postedStatus'];
			$REF_prov_identifier = $getPatProcDetailsRow['REF_prov_identifier'];			
			$REF_prov_identifier = @preg_replace('/\s+/','',$REF_prov_identifier);
			if($getPatProcDetailsRow['SVC_proc_unit']>$getPatProcDetailsRow['unit']){
				$era_proc_unit = $getPatProcDetailsRow['SVC_proc_unit'];
			}else{
				$era_proc_unit = $getPatProcDetailsRow['unit'];
			}
			$refraction_code=$SVC_proc_code;
			// GET PROC ID FROM CPT4_CODE
			$qry = imw_query("select * from cpt_fee_tbl where cpt4_code = '$SVC_proc_code'");
			$getCptFeeId1 = imw_fetch_object($qry);
			$cpt_cat_id = $getCptFeeId1->cpt_cat_id;				
			// GET PROC ID FROM CPT4_CODE
						
			// GET MOA QUALIFIER AND Processed By Details
			$qry = imw_query("select * from era_835_patient_details where ERA_patient_details_id = '$ERA_patient_details_id'");
			$moaDetails = imw_fetch_object($qry);
			$MOAQualifier = $moaDetails->MOA_qualifier;
			$CLP_claim_status = $moaDetails->CLP_claim_status;
			$CLP_claim_submitter_id = $moaDetails->CLP_claim_submitter_id;
			$CLP_claim_payment_amount = $moaDetails->CLP_claim_payment_amount;
			
			if($CLP_claim_status != 1 && $CLP_claim_status!=2 && $CLP_claim_status!=3 && $CLP_claim_status!=4 && $CLP_claim_status!=19 && $CLP_claim_status!=20 && $CLP_claim_status!=21) 
				continue;
			$paymentOf=$insCoType=$ins_company_no='';
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
			// GET MOA QUALIFIER AND Processed By Details				
			$mcrPos = strpos($REF_prov_identifier, 'MCR');
			if($mcrPos){
				// REF*6R EXISTS
				$encounter_id = trim(substr($REF_prov_identifier, 0, $mcrPos));
				$restStr = substr($REF_prov_identifier, $mcrPos+3);
				if(strpos($restStr, '_TSUC_')){
					$tsucPos = strpos($restStr, '_TSUC_');
					$tsucId = $tsucPos+6;
				}else if(strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator)){
					$tsucPos = strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator);
					if($billing_global_tsuc_separator==""){
						$tsucId = $tsucPos+4;
					}else{
						$tsucId = $tsucPos+6;
					}
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
						if($insCoType==""){
							if($ins_comp == 'primary'){
								$insCoType = 'Primary';
								$paymentOf = 'primary_paid';
								$ins_company_no=1;
								if($CLP_claim_status!=4){
									$CLP_claim_status=1;
								}
							}else if($ins_comp == 'secondary'){
								$insCoType = 'Secondary';
								$paymentOf = 'secondary_paid';
								$ins_company_no=2;
								if($CLP_claim_status!=4){
									$CLP_claim_status=2;
								}
							}
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
					/*$getCptFeeId = $manageImedicObj->getRowRecord('cpt_fee_tbl', 'cpt4_code', $SVC_proc_code);
						$cpt_fee_id = $getCptFeeId->cpt_fee_id;	*/	
					$cpt_fee_id_arr=array();	
					$cpt_qry=imw_query("select cpt_fee_id from cpt_fee_tbl where cpt4_code='$SVC_proc_code'");
					while($cpt_row=imw_fetch_array($cpt_qry)){
						$cpt_fee_id_arr[$cpt_row['cpt_fee_id']]=$cpt_row['cpt_fee_id'];
					}							
					$cpt_fee_id=implode(',',$cpt_fee_id_arr);			
					// GET PROC ID FROM CPT4_CODE
					
					//GET MODIFIERS ID
					$modifiersId1="";
					$modifiersId2="";
					$SVC_mod_code_exp="";
					$SVC_mod_code_exp=explode(',',$SVC_mod_code);
					if($SVC_mod_code_exp[0]!=""){
						$modifiers_code1=trim($SVC_mod_code_exp[0]);
						$qry = imw_query("select * from modifiers_tbl where mod_prac_code = '$modifiers_code1'");
						$getModID = imw_fetch_object($qry);
						$modifiersId1 = $getModID->modifiers_id;
					}
					if($SVC_mod_code_exp[1]!=""){
						$modifiers_code2=trim($SVC_mod_code_exp[1]);
						$qry = imw_query("select * from modifiers_tbl where mod_prac_code = '$modifiers_code2'");
						$getModID = imw_fetch_object($qry);
						$modifiersId2 = $getModID->modifiers_id;
					}
					//GET MODIFIERS ID
					
					$getChargeListDetailsStr = "SELECT * FROM 
												patient_charge_list a,
												patient_charge_list_details b
												WHERE b.del_status='0' and a.patient_id = '$CLP_claim_submitter_id'
												AND a.charge_list_id = b.charge_list_id
												AND a.date_of_service = '$DOS'
												AND b.procCode in($cpt_fee_id)";
					if($modifiersId1>0 && $modifiersId2>0){
						$getChargeListDetailsStr.=" AND ((b.modifier_id1 = '$modifiersId1' and b.modifier_id2 = '$modifiersId2') 
													or (b.modifier_id1 = '$modifiersId2' and b.modifier_id2 = '$modifiersId1'))";
					}else{							
						if($modifiersId1>0){
							$getChargeListDetailsStr.=" AND b.modifier_id1 = '$modifiersId1'";
						}
						if($modifiersId2>0){
							$getChargeListDetailsStr.=" AND b.modifier_id2 = '$modifiersId2'";
						}
						if($modifiersId1<=0 && $modifiersId2<=0){
							$getChargeListDetailsStr.=" AND b.modifier_id1 = '' and b.modifier_id2 = ''";
						}
					}
					$getChargeListDetailsQry = imw_query($getChargeListDetailsStr);
					$countRows = imw_num_rows($getChargeListDetailsQry);
					if($countRows){
						while($getChargeListDetailsRows = imw_fetch_assoc($getChargeListDetailsQry)){
							$encounterId = $getChargeListDetailsRows['encounter_id'];
							$charge_list_id = $getChargeListDetailsRows['charge_list_id'];
							$listChargeDetailId = $getChargeListDetailsRows['charge_list_detail_id'];							
						}
						if($countRows==1){
							$encounter_id = $encounterId;
							$chargeListDetailId = $listChargeDetailId;
						}
					}
				}else{
					continue;
				}
				// GET ENCOUNTER AND CHARGE LIST DETAILS BASED ON PATIENT ID
			}
			
			if($REF_provider_ref_id!="" && $CLP_claim_status!=4){
				$getInsStr = "select primaryInsuranceCoId,secondaryInsuranceCoId from patient_charge_list
						where del_status='0' and encounter_id = '$encounter_id'";				
				$getInsQry = imw_query($getInsStr);
				$getInsRow = imw_fetch_array($getInsQry);
				$insuranceCoId_pri = $getInsRow['primaryInsuranceCoId'];
				$insuranceCoId_sec = $getInsRow['secondaryInsuranceCoId'];
				if($insuranceCoId_pri!=$insuranceCoId_sec){
					$getInsStr2 = "select id from insurance_companies where Payer_id = '$REF_provider_ref_id'";				
					$getInsQry2 = imw_query($getInsStr2);
					while($getInsRow2 = imw_fetch_array($getInsQry2)){
						if($getInsRow2['id']==$insuranceCoId_pri){
							$CLP_claim_status=1;
							$insCoType = 'Primary';
							$paymentOf = 'primary_paid';
							$ins_company_no=1;
						}else if($getInsRow2['id']==$insuranceCoId_sec){
							$CLP_claim_status=2;
							$insCoType = 'Secondary';
							$paymentOf = 'secondary_paid';
							$ins_company_no=2;
						}
					}
				}
			}
			//$encounter_id_arr[$encounter_id]=$encounter_id;
			$listChargeDetailId = trim($chargeListDetailId);			
			if(!$encounter_id || !$insCoType || !$listChargeDetailId) continue;
			$getChkChargeListDetailsStr = "SELECT charge_list_detail_id,totalAmount,approvedAmt FROM patient_charge_list_details 
										WHERE del_status='0' and charge_list_detail_id = '$listChargeDetailId' and patient_id='$CLP_claim_submitter_id'";
			$getChkChargeListDetailsQry = imw_query($getChkChargeListDetailsStr);		
			$getChkChargeListDetailsRun=imw_fetch_array($getChkChargeListDetailsQry);	
			$allow_post_chk=1;
			if($use_era_allowed==""){
				if($getChkChargeListDetailsRun['totalAmount']!=$getChkChargeListDetailsRun['approvedAmt'] && $getChkChargeListDetailsRun['approvedAmt']!=$AMT_amount){
					$allow_post_chk=0;
				}
			}
			if(imw_num_rows($getChkChargeListDetailsQry)>0 && $encounter_id>0){
			//MOA 18 CHECK
				if(strstr($MOAQualifier, 'MA18') || strstr($MOAQualifier, 'MA07')){
					unset($arrayRecord);
					$arrayRecord['secondarySubmit'] = 1;
					$arrayRecord['moaQualifier'] = $MOAQualifier;
					UpdateRecords($encounter_id,'encounter_id',$arrayRecord,'patient_charge_list');
				}else{
					$qry = imw_query("select * from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'");
					$getsec_ins = imw_fetch_object($qry);
					$secondaryInsuranceCoId = $getsec_ins->secondaryInsuranceCoId;
					if($secondaryInsuranceCoId>0){
						$process_file_chk=$_REQUEST['hcfa_process'];
						$elect_process_chk=$_REQUEST['elect_process'];
						if($elect_process_chk){
							$process_file_chk=$elect_process_chk;
						}
						$getrec=getPatientCharList_era($encounter_id,$process_file_chk);
						$count = count($getrec);
						if($count>0 && $CLP_claim_status!=4 && $insCoType == 'Primary'){
							$ma18_enc_arr[]=$encounter_id;
						}
					}
				}
			//MOA 18 CHECK

			//GET INSURANCE COMPANIES
								
				$getInsStr = "select primaryInsuranceCoId,secondaryInsuranceCoId,tertiaryInsuranceCoId from patient_charge_list
							where del_status='0' and encounter_id = '$encounter_id'";				
				$getInsQry = imw_query($getInsStr);
				while($getInsRow = imw_fetch_array($getInsQry)){
					$pri_insuranceCoId = $getInsRow['primaryInsuranceCoId'];
					if($insCoType=="Primary"){
						$insuranceCoId = $getInsRow['primaryInsuranceCoId'];
					}
					if($insCoType=="Secondary"){
						$insuranceCoId = $getInsRow['secondaryInsuranceCoId'];
					}
					if($insCoType=="Tertiary"){
						$insuranceCoId = $getInsRow['tertiaryInsuranceCoId'];
					}
					
				}
			//GET INSURANCE COMPANIES
			
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

			// GET ENCOUNTER DETAILS.
				$qry = imw_query("select * from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'");
				$getAccountDetails = imw_fetch_assoc($qry);
				if(is_array($getAccountDetails))
					extract($getAccountDetails);
			// GET ENCOUNTER DETAILS.
			
			if((($listChargeDetailId!='') || ($listChargeDetailId!=0)) && $paymentOf!=""){	
				
				$sql_med_qry=imw_query("select id,in_house_code,Payer_id,Payer_id_pro from insurance_companies where id='$insuranceCoId'");	
				$med_ins_chk=false;
				$ins_tbl_arr=imw_fetch_array($sql_med_qry);
				if(strtolower($ins_tbl_arr['in_house_code'])=='medicare'){	
					$med_ins_chk=true;
				}
				
				$pat_qry=imw_query("select fname,lname from patient_data where id='$patient_id'");
				$pat_row=imw_fetch_array($pat_qry);
				$fname_frm = $pat_row['fname'];
				$lname_frm = $pat_row['lname'];
				$mname_frm = $pat_row['mname'];
				$patient_name = ucwords(trim($lname_frm.", ".$fname_frm));
				
			//GET BALANC FROM ACCOUNTING
				$write_off_chk="";
				$listChargeDetailId = @preg_replace('/\s+/','', $listChargeDetailId);
				$qry = imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_detail_id = '$listChargeDetailId'");
				$chargesBalDetail = imw_fetch_object($qry);
				$newAccountingBalance = $chargesBalDetail->newBalance;						
				$approvedAmt = $chargesBalDetail->approvedAmt;
				$chargeTotalAmt = $chargesBalDetail->totalAmount;
				$chargeWrite_off = $chargesBalDetail->write_off;
				$chargeBalForProc = $chargesBalDetail->balForProc;
				$chargeNewBalance = $chargesBalDetail->newBalance;
				$write_off_chk=$chargesBalDetail->write_off;
				$unit_chk=$chargesBalDetail->units;
				$charge_list_id_chk=$chargesBalDetail->charge_list_id;
				$totalAmount_chk=$chargesBalDetail->totalAmount;
				// GET MOA QUALIFIER
				$getClpCasStr = "SELECT * FROM era835clpcas WHERE era835Id = '$Era_835_Id' AND ERAPatientdetailsId = '$ERA_patient_details_id'";
				$getClpCasQry = imw_query($getClpCasStr);
					
				$chk_cont_proc_num=0;
				if(imw_num_rows($getClpCasQry)>0){
					while($getClpCasRows = imw_fetch_array($getClpCasQry)){
						$enc_level_adj_arr[$getClpCasRows['clpCasId']] = $getClpCasRows['casReasonAmt'];
					}
					$getDetailsOfStr_num = imw_query("SELECT * FROM era_835_proc_details WHERE `ERA_patient_details_id` = '$ERA_patient_details_id'");
					$chk_cont_proc_num=imw_num_rows($getDetailsOfStr_num);
				}
				if($chk_cont_proc_num==1){
					$SVC_provider_pay_amt=$CLP_claim_payment_amount;
				}
				$chk_g_code_proc="";
				if($SVC_provider_pay_amt==0 && (in_array($cpt_cat_id,$chk_cpt_cat_id) || strtoupper($SVC_proc_code[0])=='G')){
					$chk_g_code_proc="yes";
				}
				if(($matchUnits!="" && $unit_chk!=$era_proc_unit && $era_proc_unit>0) || (imw_num_rows($getClpCasQry)>0) || in_array($CLP_claim_submitter_id,$plb_patient_exp) || ($totalAmount_chk<=0 && $chk_g_code_proc=="")){
					//echo $unit_chk.'<br>';
					//echo $CLP_claim_submitter_id.'<br>';
				}else{
					$write_off_amt_arr=array();
					$write_off_code_type_arr=array();
					$write_off_code_reason_arr=array();	
					if(strpos($CAS_type, 'OA') && strpos($CAS_reason_code, '100')){
					}else{
						unset($typeCodeArr);
						$tot_cas_wrt_amt_arr=array();
						$CASTypeArray = explode(", ", $CAS_type);
						$CASReasonCodeArr = explode(", ", $CAS_reason_code);
						$CASAmtArr = explode(", ", $CAS_amt);
						foreach($CASTypeArray as $incChk => $casTypeChk){
							$casTypeChkNew = @preg_replace('/\s+/','', $casTypeChk);
							$CASReasonCodeChkArr[$incChk] = @preg_replace('/\s+/','', $CASReasonCodeArr[$incChk]);
							$typeCodeChk = trim($casTypeChkNew.' '.$CASReasonCodeChkArr[$incChk]);
							if(in_array($typeCodeChk,$cas_action_arr['Write Off'][1]) || in_array($typeCodeChk,$cas_action_arr['Write Off'][0])){	
								if($CASAmtArr[$incChk]>0){
									$tot_cas_wrt_amt_arr[]=$CASAmtArr[$incChk];
								}
								$refraction_code="";
							}
						}
						$tot_cas_wrt_amt=str_replace(',','',number_format(array_sum($tot_cas_wrt_amt_arr),2));
						
						foreach($CASTypeArray as $inc => $casTypeNew){
							$status = '';						
							if($postedStatus=='Not Posted'){
								$qry = imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_detail_id = '$listChargeDetailId'");
								$chargesBalDetail = imw_fetch_object($qry);
								$newAccountingBalance = $chargesBalDetail->newBalance;	
								$newAccountingApprovedAmt = $chargesBalDetail->approvedAmt;
									
								$casTypeNew = @preg_replace('/\s+/','', $casTypeNew);
								$CASReasonCodeArr[$inc] = @preg_replace('/\s+/','', $CASReasonCodeArr[$inc]);
								$CASTypeArray[$inc] = @preg_replace('/\s+/','', $CASTypeArray[$inc]);
								$typeCode = trim($casTypeNew.' '.$CASReasonCodeArr[$inc]);
								$chk_237_code=$CASTypeArray[$inc+1].' '.$CASReasonCodeArr[$inc+1];
								$chk_237_code_prev=$CASTypeArray[$inc-1].' '.$CASReasonCodeArr[$inc-1];
								$typeCodeArr[] = $typeCode;
								
								$AppAmtChk=number_format($newAccountingApprovedAmt,2);
								$svc_pay_chk=number_format($SVC_provider_pay_amt,2);
								
								if($CASAmtArr[$inc]>0){
									if(in_array($typeCode,$cas_action_arr['Deductible'][0])){
										$refraction_code="";
										$save_code_type="";
										$save_code_reason="";
										$save_code_type=$CASTypeArray[$inc];
										$save_code_reason=$CASReasonCodeArr[$inc]; 
										$sel_deduct_qry=imw_query("select deductible_id from payment_deductible where charge_list_detail_id='$listChargeDetailId' and cas_type='$save_code_type' and cas_code='$save_code_reason' and delete_deduct='0'");	
										if(imw_num_rows($sel_deduct_qry)==0){
											// START UPDATE DEDUCTIBLE AMT
											$deductibleAmt = $CASAmtArr[$inc];
											$updateChargeListDetailStr = imw_query("UPDATE patient_charge_list_details SET deductAmt = deductAmt + $deductibleAmt,paidStatus = 'Paid' WHERE charge_list_detail_id = '$listChargeDetailId'");
											$updateChargeListStr = imw_query("UPDATE patient_charge_list SET deductibleTotalAmt = deductibleTotalAmt + $deductibleAmt WHERE encounter_id = '$encounter_id'");
												
											// START INSERT DEDUCTIBLE TRANSACTION
											unset($arrayRecord);
											$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
											$arrayRecord['deduct_amount'] = $deductibleAmt;
											$arrayRecord['deductible_by'] = 'Insurance';
											$arrayRecord['deduct_ins_id'] = $insuranceCoId;
											$arrayRecord['deduct_operator_id'] = $operatorId;
											$arrayRecord['deduct_date'] = $payDate;
											$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
											$arrayRecord['cas_type'] = $save_code_type;
											$arrayRecord['cas_code'] = $save_code_reason;
											$arrayRecord['facility_id'] = $pay_location;		
											$insertDeductAmt = addRecords($arrayRecord, 'payment_deductible');
											
											$task_insert_arr=array();
											$task_insert_arr['patientid']=$patient_id;
											$task_insert_arr['operatorid']=$operatorId;
											$task_insert_arr['section']='reason_code';
											$task_insert_arr['status_id']=$save_code_type.'~~'.$save_code_reason;
											$task_insert_arr['encounter_id']=$encounter_id;
											$task_insert_arr['date_of_service']=$date_of_service;
											$task_insert_arr['cpt_code']=$SVC_proc_code;
											$task_insert_arr['patient_name']=$patient_name;
											assign_acc_task_rules_to($task_insert_arr);
											
											$status = 'Paid';
											if($deductibleAmt>0){
												$pay_type="ins";
												$ins_type=$ins_company_no;
												patient_proc_tx_update($listChargeDetailId,0,$pay_type,$ins_type);
											}
										}
											
										if($status == 'Paid'){
											unset($arrayRecord);
											$arrayRecord['postedStatus'] = 'Posted';
											$updatePostedStatus = UpdateRecords($procClaimId,'835_Era_proc_Id',$arrayRecord,'era_835_proc_details');
										}
									}
									
									if(in_array($typeCode,$cas_action_arr['Co-Insurance'][0])){
										$refraction_code="";
										// START INSERT CO Insurance
										unset($arrayRecord);
										$arrayRecord['patient_id'] = $patient_id;
										$arrayRecord['encounter_id'] = $encounter_id;
										$arrayRecord['charge_list_id'] = $charge_list_id_chk;
										$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
										$arrayRecord['payment_by'] = 'Insurance';
										$arrayRecord['ins_id'] = $insuranceCoId;
										$arrayRecord['payment_amount'] = $CASAmtArr[$inc];
										$arrayRecord['payment_date'] = $payDate;
										$arrayRecord['operator_id'] = $operatorId;
										$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
										$arrayRecord['payment_type'] = 'Co-Insurance';
										$arrayRecord['cas_type'] = $CASTypeArray[$inc];
										$arrayRecord['cas_code'] = $CASReasonCodeArr[$inc]; 
										$arrayRecord['facility_id'] = $pay_location;
										$addDeductible = addRecords($arrayRecord, 'account_payments');
	
										$task_insert_arr=array();
										$task_insert_arr['patientid']=$patient_id;
										$task_insert_arr['operatorid']=$operatorId;
										$task_insert_arr['section']='reason_code';
										$task_insert_arr['status_id']=$CASTypeArray[$inc].'~~'.$CASReasonCodeArr[$inc]; 
										$task_insert_arr['encounter_id']=$encounter_id;
										$task_insert_arr['date_of_service']=$date_of_service;
										$task_insert_arr['cpt_code']=$SVC_proc_code;
										$task_insert_arr['patient_name']=$patient_name;
										assign_acc_task_rules_to($task_insert_arr);
										
										$status = 'Paid';
										if($CASAmtArr[$inc]>0){
											$pay_type="ins";
											$ins_type=$ins_company_no;
											patient_proc_tx_update($listChargeDetailId,0,$pay_type,$ins_type);
										}
									}
									if(in_array($typeCode,$cas_action_arr['Co-Payment'][0])){
										$refraction_code="";
										// START INSERT CO Insurance
										unset($arrayRecord);
										$arrayRecord['patient_id'] = $patient_id;
										$arrayRecord['encounter_id'] = $encounter_id;
										$arrayRecord['charge_list_id'] = $charge_list_id_chk;
										$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
										$arrayRecord['payment_by'] = 'Insurance';
										$arrayRecord['ins_id'] = $insuranceCoId;
										$arrayRecord['payment_amount'] = $CASAmtArr[$inc];
										$arrayRecord['payment_date'] = $payDate;
										$arrayRecord['operator_id'] = $operatorId;
										$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
										$arrayRecord['payment_type'] = 'Co-Payment';
										$arrayRecord['cas_type'] = $CASTypeArray[$inc];
										$arrayRecord['cas_code'] = $CASReasonCodeArr[$inc]; 
										$arrayRecord['facility_id'] = $pay_location;
										$addDeductible = addRecords($arrayRecord, 'account_payments');
	
										$task_insert_arr=array();
										$task_insert_arr['patientid']=$patient_id;
										$task_insert_arr['operatorid']=$operatorId;
										$task_insert_arr['section']='reason_code';
										$task_insert_arr['status_id']=$CASTypeArray[$inc].'~~'.$CASReasonCodeArr[$inc]; 
										$task_insert_arr['encounter_id']=$encounter_id;
										$task_insert_arr['date_of_service']=$date_of_service;
										$task_insert_arr['cpt_code']=$SVC_proc_code;
										$task_insert_arr['patient_name']=$patient_name;
										assign_acc_task_rules_to($task_insert_arr);
										
										$status = 'Paid';
										if($CASAmtArr[$inc]>0){
											$pay_type="ins";
											$ins_type=$ins_company_no;
											patient_proc_tx_update($listChargeDetailId,0,$pay_type,$ins_type);
										}
									}
									if($paymentOf=='secondary_paid' && $CASAmtArr[$inc]>0 && $typeCode == 'OA 23' && in_array(strtolower($billing_global_server_name), array('shoreline')) && (in_array($ins_tbl_arr['Payer_id_pro'],array('38217')) || in_array($ins_tbl_arr['Payer_id'],array('38217')))){
										$refraction_code=$save_code_type=$save_code_reason="";
										$writeOffAmt = $CASAmtArr[$inc];
										$save_code_type=$CASTypeArray[$inc];
										$save_code_reason=$CASReasonCodeArr[$inc];
										$sel_wrt_qry=imw_query("select write_off_id from paymentswriteoff where write_off_by_id>0 and charge_list_detail_id='$listChargeDetailId' and CAS_type like '%$save_code_type%' and CAS_code like '%$save_code_reason%' and delStatus='0'");	
										if(imw_num_rows($sel_wrt_qry)==0){
											$sqlQry = imw_query("UPDATE patient_charge_list_details SET balForProc = balForProc - $writeOffAmt,newBalance = newBalance - $writeOffAmt WHERE charge_list_detail_id = '$listChargeDetailId'");
											
											$status = 'Paid';
											unset($arrayRecord);
											$arrayRecord['patient_id'] = $patient_id;
											$arrayRecord['encounter_id'] = $encounter_id;
											$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
											$arrayRecord['write_off_by_id'] = $insuranceCoId;
											$arrayRecord['write_off_amount'] = $writeOffAmt;
											$arrayRecord['write_off_operator_id'] = $operatorId;
											$arrayRecord['write_off_date'] = $payDate;
											$arrayRecord['CAS_type'] = $save_code_type;
											$arrayRecord['CAS_code'] = $save_code_reason;
											$arrayRecord['write_off_code_id']=$write_off_code;
											$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
											$arrayRecord['facility_id'] = $pay_location;
											$insertWriteOffAmt = addRecords($arrayRecord, 'paymentswriteoff');
											
											if($writeOffAmt>0){
												$pay_type="ins";
												$ins_type=$ins_company_no;
												patient_proc_tx_update($listChargeDetailId,$writeOffAmt,$pay_type,$ins_type);
											}
										}
									}
									if($refraction_code=='92015' || in_array($typeCode,$cas_action_arr['Denied'][0])){
										if($SVC_provider_pay_amt>0){
										}else{
											unset($arrayRecord);
											$arrayRecord['patient_id'] = $patient_id;
											$arrayRecord['encounter_id'] = $encounter_id;
											$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
											$arrayRecord['deniedBy'] = 'Insurance';
											$arrayRecord['deniedById'] = $insuranceCoId;
											$arrayRecord['deniedAmount'] = $CASAmtArr[$inc];
											$arrayRecord['denialOperatorId'] = $operatorId;
											$arrayRecord['CAS_type'] = $casTypeNew;
											$arrayRecord['CAS_code'] = $CASReasonCodeArr[$inc];								
											$arrayRecord['deniedDate'] = $payDate;
											$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
											$arrayRecord['facility_id'] = $pay_location;
	
											$denail_data_arr=array();
											$denail_data_arr['denial_cpt_code']=$chargesBalDetail->procCode;
											$denail_data_arr['denial_cas_code']=$CASReasonCodeArr[$inc];
											$denial_resp=denial_resp_fun($denail_data_arr);
											if($denial_resp>0){
												$arrayRecord['next_responsible_by'] = $operatorId;
											}
	
											$addDenied = addRecords($arrayRecord, 'deniedpayment');
	
											$task_insert_arr=array();
											$task_insert_arr['patientid']=$patient_id;
											$task_insert_arr['operatorid']=$operatorId;
											$task_insert_arr['section']='reason_code';
											$task_insert_arr['status_id']=$casTypeNew.'~~'.$CASReasonCodeArr[$inc];
											$task_insert_arr['encounter_id']=$encounter_id;
											$task_insert_arr['date_of_service']=$date_of_service;
											$task_insert_arr['cpt_code']=$SVC_proc_code;
											$task_insert_arr['patient_name']=$patient_name;
											$task_insert_arr['task_group']=$gro_id;
											$task_insert_arr['task_ins_comp']=$insuranceCoId;
											assign_acc_task_rules_to($task_insert_arr);
	
	
											// START UPDATE CHARGELIST DETAIL CLAIM STATUS TO 1
												unset($arrayRecord);
												$arrayRecord['claimDenied'] = '1';
												$updateStatus = UpdateRecords($listChargeDetailId,'charge_list_detail_id',$arrayRecord,'patient_charge_list_details');
											$status = 'Paid';	
											if($CASAmtArr[$inc]>0 && $denial_resp>0){
												$pay_type="ins";
												$ins_type=$ins_company_no;
												patient_proc_tx_update($listChargeDetailId,0,$pay_type,$ins_type);
											}
	
											if($status == 'Paid'){
												unset($arrayRecord);
												$arrayRecord['postedStatus'] = 'Posted';
												$updatePostedStatus = UpdateRecords($procClaimId,'835_Era_proc_Id',$arrayRecord,'era_835_proc_details');
											}
										}
									}	
								}
								
								if(($AppAmtChk==$svc_pay_chk || $allow_post_chk==0) && $paymentOf=='secondary_paid'){
								}else{
								// DENIED AMOUNT
									if($CASAmtArr[$inc]>0 && ($paymentOf=='primary_paid' || ($write_off_chk!=$tot_cas_wrt_amt && $write_off_chk!=$CASAmtArr[$inc]))){
										// WRITE OFF AMOUNT SETTINGS
										if($write_off_chk==$CASAmtArr[$inc]){
											$allow_bal_amt=0;
											$allow_ovr_amt=0;
										}else{			
											if($paymentOf=='secondary_paid' && $write_off_chk>0){
												if($CASAmtArr[$inc]>$write_off_chk){
													$sec_write_diff=$CASAmtArr[$inc]-$write_off_chk;
												}else{
													$sec_write_diff=0;
												}
												if($newAccountingBalance>=$sec_write_diff){
													$allow_bal_amt=$sec_write_diff;
													$allow_ovr_amt=0;
												}else{
													$allow_diff_ovr_amt=$sec_write_diff-$newAccountingBalance;
													$allow_bal_amt=$newAccountingBalance;
													$allow_ovr_amt=$allow_diff_ovr_amt;
												}
											}else{	
												if($CASAmtArr[$inc]>$write_off_chk){
													$pri_write_diff=$CASAmtArr[$inc]-$write_off_chk;
												}else{
													$pri_write_diff=0;
												}		
												if($newAccountingBalance>=$pri_write_diff){
													$allow_bal_amt=$pri_write_diff;
													$allow_ovr_amt=0;
												}else{
													$allow_diff_ovr_amt=$pri_write_diff-$newAccountingBalance;
													$allow_bal_amt=$newAccountingBalance;
													$allow_ovr_amt=$allow_diff_ovr_amt;
												}
											}
										}
										$save_code_type="";
										$save_code_reason="";
										$save_code_type=$CASTypeArray[$inc];
										$save_code_reason=$CASReasonCodeArr[$inc];
										// WRITE OFF AMOUNT SETTINGS
										
										if(in_array($typeCode,$cas_action_arr['Write Off'][1]) || ((in_array($cpt_cat_id,$chk_cpt_cat_id) || strtoupper($SVC_proc_code[0])=='G') && $med_ins_chk==true)){
											// START UPDATE Approved & WriteOff Amounts
											if($paymentOf=='secondary_paid'){
											}else{
												$write_off_amt_arr[]=$CASAmtArr[$inc];
												$write_off_code_type_arr[]=$CASTypeArray[$inc];
												$write_off_code_reason_arr[]=$CASReasonCodeArr[$inc];
											}
										}else if(in_array($typeCode,$cas_action_arr['Write Off'][0])){
											$writeOffAmt = $CASAmtArr[$inc];
											$sel_wrt_qry=imw_query("select write_off_id from paymentswriteoff where write_off_by_id>0 and charge_list_detail_id='$listChargeDetailId' and CAS_type like '%$save_code_type%' and CAS_code like '%$save_code_reason%' and delStatus='0'");	
											if(imw_num_rows($sel_wrt_qry)==0){
												
												$sqlQry = imw_query("UPDATE patient_charge_list_details SET
																	balForProc = balForProc - $writeOffAmt,
																	newBalance = newBalance - $writeOffAmt
																	WHERE charge_list_detail_id = '$listChargeDetailId'");
												$status = 'Paid';
												unset($arrayRecord);
												$arrayRecord['patient_id'] = $patient_id;
												$arrayRecord['encounter_id'] = $encounter_id;
												$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
												$arrayRecord['write_off_by_id'] = $insuranceCoId;
												$arrayRecord['write_off_amount'] = $writeOffAmt;
												$arrayRecord['write_off_operator_id'] = $operatorId;
												$arrayRecord['write_off_date'] = $payDate;
												$arrayRecord['CAS_type'] = $save_code_type;
												$arrayRecord['CAS_code'] = $save_code_reason;
												$arrayRecord['write_off_code_id']=$write_off_code;
												$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
												$arrayRecord['facility_id'] = $pay_location;
												$insertWriteOffAmt = addRecords($arrayRecord, 'paymentswriteoff');
												
												$task_insert_arr=array();
												$task_insert_arr['patientid']=$patient_id;
												$task_insert_arr['operatorid']=$operatorId;
												$task_insert_arr['section']='reason_code';
												$task_insert_arr['status_id']=$save_code_type.'~~'.$save_code_reason;
												$task_insert_arr['encounter_id']=$encounter_id;
												$task_insert_arr['date_of_service']=$date_of_service;
												$task_insert_arr['cpt_code']=$SVC_proc_code;
												$task_insert_arr['patient_name']=$patient_name;
												assign_acc_task_rules_to($task_insert_arr);
												
												if($writeOffAmt>0){
													$pay_type="ins";
													$ins_type=$ins_company_no;
													patient_proc_tx_update($listChargeDetailId,$writeOffAmt,$pay_type,$ins_type);
												}
											}
										}
									}
									
									if((count($CASTypeArray)-1)==$inc && $paymentOf=='primary_paid' && $SVC_proc_charge>=0){
										$write_off_amt="";
										$write_off_code_type="";
										$write_off_code_reason="";
	
										$write_off_amt = array_sum($write_off_amt_arr);
										$write_off_code_type = implode(',',$write_off_code_type_arr);
										$write_off_code_reason=implode(',',$write_off_code_reason_arr);
										
										if($write_off_amt>0 && $write_off_chk!=$write_off_amt){
											if($write_off_amt>$write_off_chk){
												$pri_write_diff=$write_off_amt-$write_off_chk;
											}else{
												$pri_write_diff=0;
											}	
													
											if($newAccountingBalance>=$pri_write_diff){
												$allow_bal_amt=$pri_write_diff;
												$allow_ovr_amt=0;
											}else{
												$allow_diff_ovr_amt=$pri_write_diff-$newAccountingBalance;
												$allow_bal_amt=$newAccountingBalance;
												$allow_ovr_amt=$allow_diff_ovr_amt;
											}
									
											$allowedAmt = $SVC_proc_charge - $write_off_amt;
											if($allowedAmt>=0){	
												$sqlQry = imw_query("UPDATE patient_charge_list_details SET
																		approvedAmt = '$allowedAmt',
																		write_off = '$write_off_amt',
																		write_off_code_id='$write_off_code',
																		write_off_date = '$payDate',
																		write_off_dot = '$today',
																		write_off_opr_id='$operatorId',
																		write_off_by = '$insuranceCoId',
																		balForProc = balForProc - $allow_bal_amt,
																		newBalance = newBalance - $allow_bal_amt,
																		overPaymentForProc = overPaymentForProc + $allow_ovr_amt
																		WHERE charge_list_detail_id = '$listChargeDetailId'");
												$status = 'Paid';
												unset($arrayRecord);
												$arrayRecord['patient_id'] = $patient_id;
												$arrayRecord['encounter_id'] = $encounter_id;
												$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
												$arrayRecord['write_off_by_id'] = $insuranceCoId;
												$arrayRecord['write_off_amount'] = 0;
												$arrayRecord['write_off_operator_id'] = $operatorId;
												$arrayRecord['write_off_date'] = $payDate;
												$arrayRecord['CAS_type'] = $write_off_code_type;
												$arrayRecord['CAS_code'] = $write_off_code_reason;
												$arrayRecord['write_off_code_id']=$write_off_code;
												$arrayRecord['era_amt']=$write_off_amt;
												$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
												$arrayRecord['facility_id'] = $pay_location;
												$insertWriteOffAmt = addRecords($arrayRecord, 'paymentswriteoff');
											
												$status = 'Paid';
												
												$qry1=imw_query("Insert INTO defaultwriteoff SET
														  patient_id='".$patient_id."',
														  encounter_id='".$encounter_id."',
														  charge_list_id='".$charge_list_id_chk."',
														  charge_list_detail_id='".$listChargeDetailId."',
														  write_off_amount='".$write_off_amt."',	
														  write_off_by='".$insuranceCoId."',		  		  
														  write_off_operator_id='".$operatorId."',
														  write_off_dop='".$payDate."',
														  write_off_dot='".date('Y-m-d H:i:s')."',
														  write_off_code_id='".$write_off_code."',
														  facility_id='".$pay_location."',
														  cas_type='".$write_off_code_type."',
														  cas_code='".$write_off_code_reason."'");
														  
												$task_insert_arr=array();
												$task_insert_arr['patientid']=$patient_id;
												$task_insert_arr['operatorid']=$operatorId;
												$task_insert_arr['section']='reason_code';
												$task_insert_arr['status_id']=$write_off_code_type.'~~'.$write_off_code_reason;
												$task_insert_arr['encounter_id']=$encounter_id;
												$task_insert_arr['date_of_service']=$date_of_service;
												$task_insert_arr['cpt_code']=$SVC_proc_code;
												$task_insert_arr['patient_name']=$patient_name;
												assign_acc_task_rules_to($task_insert_arr);		  
														  
												if($write_off_amt>0){
													$pay_type="ins";
													$ins_type=$ins_company_no;
													patient_proc_tx_update($listChargeDetailId,$write_off_amt,$pay_type,$ins_type);
												}
											}
										}
									}
									
									$adj_cas_amt="";
									if($paymentOf=='primary_paid' && $CASAmtArr[$inc]<0 && $CASAmtArr[$inc]!="" && (in_array($typeCode,$cas_action_arr['Adjustment'][0]) || in_array($typeCode,$cas_adjust_negative_arr))){
										$adj_cas_amt=str_replace('-','',$CASAmtArr[$inc]);
										unset($arrayRecord);
										$arrayRecord['patient_id'] = $patient_id;
										$arrayRecord['encounter_id'] = $encounter_id;
										$arrayRecord['charge_list_id'] = $charge_list_id_chk;
										$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
										$arrayRecord['payment_by'] = 'Insurance';
										$arrayRecord['ins_id'] = $insuranceCoId;
										$arrayRecord['payment_amount'] = $adj_cas_amt;
										$arrayRecord['payment_date'] = $payDate;
										$arrayRecord['operator_id'] = $operatorId;
										$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
										$arrayRecord['payment_type'] = 'Adjustment';
										$arrayRecord['cas_type'] = $CASTypeArray[$inc];
										$arrayRecord['cas_code'] = $CASReasonCodeArr[$inc];
										$arrayRecord['facility_id'] = $pay_location;
										$addDeductible = addRecords($arrayRecord, 'account_payments');
										$status = 'Paid';
									}
									
									if($status == 'Paid'){
										unset($arrayRecord);
										$arrayRecord['postedStatus'] = 'Posted';
										$updatePostedStatus = UpdateRecords($procClaimId,'835_Era_proc_Id',$arrayRecord,'era_835_proc_details');
									}
								}	
							}
						}
						
						// PAYMENT MAID ID PAY AMOUNT EXISTS
						$qry = imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_detail_id = '$listChargeDetailId'");
						$chargesBalDetail = imw_fetch_object($qry);
						$newAccountingBalance = $chargesBalDetail->newBalance;	
						$newAccountingApprovedAmt = $chargesBalDetail->approvedAmt;
						
						$AppAmtChk=number_format($newAccountingApprovedAmt,2);
						$SVC_pay_chk=number_format($SVC_provider_pay_amt,2);
						$pri_ins_pay_amt=0;
						if($paymentOf=='secondary_paid' && in_array('OA',$CASTypeArray) && in_array('23',$CASReasonCodeArr) && $SVC_provider_pay_amt>0){
							$old_pay_qry=imw_query("select payment_amount from patient_chargesheet_payment_info where encounter_id='$encounter_id' and markPaymentDelete='0' and insCompany='1' and payment_amount='$SVC_provider_pay_amt'");
							$pri_ins_pay_amt=imw_num_rows($old_pay_qry);
						}
						
						if($paymentOf=='secondary_paid' && in_array('OA',$CASTypeArray) && in_array('23',$CASReasonCodeArr) && $SVC_provider_pay_amt>0 && $pri_ins_pay_amt>0){
						}else{
							if($postedStatus=='Not Posted'){			
									
								if($newAccountingBalance>=$SVC_provider_pay_amt){
									$provider_paid_amt=$SVC_provider_pay_amt;
									$overPaymentForProc_amt=0;
								}else{
									$overPaymentForProc_amt=$SVC_provider_pay_amt-$newAccountingBalance;
									$provider_paid_amt=$newAccountingBalance;
								}
								if($SVC_provider_pay_amt>0 || $SVC_provider_pay_amt<0){
									//INSERT MASTER PAYMENT
									$postedStatus = 'Paid';
									$paymentClaims="paid";
									if($SVC_provider_pay_amt<0){
										$paymentClaims="Negative Payment";
										$SVC_provider_pay_amt=str_replace('-','',$SVC_provider_pay_amt);
									}
										
									unset($arrayRecord);
									$arrayRecord['encounter_id'] = $encounter_id;
									$arrayRecord['paid_by'] = 'Insurance';
									$arrayRecord['payment_amount'] = $SVC_provider_pay_amt;
									$arrayRecord['payment_mode'] = $payment_method;
									if($payment_method=="Credit Card"){
										$arrayRecord['creditCardNo'] = $checkNo;
									}else{
										$arrayRecord['checkNo'] = $checkNo;
									}
									$arrayRecord['date_of_payment'] = $payDate;
									$arrayRecord['operatorId'] = $operatorId;
									$arrayRecord['insProviderId'] = $insuranceCoId;
									$arrayRecord['insCompany'] = $ins_company_no;
									$arrayRecord['paymentClaims'] = $paymentClaims;
									$arrayRecord['transaction_date'] = date('Y-m-d');
									$arrayRecord['facility_id'] = $pay_location;
									$addPaymentId = addRecords($arrayRecord, 'patient_chargesheet_payment_info');
									//INSERT DETAIL PAYMENT
									unset($arrayRecord);
									$arrayRecord['payment_id'] = $addPaymentId;
									$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
									$arrayRecord['paidBy'] = 'Insurance';
									$arrayRecord['paidDate'] = $payDate;
									$arrayRecord['paidForProc'] = $SVC_provider_pay_amt;
									$arrayRecord['overPayment'] = '';
									$arrayRecord['CAS_type'] = '';
									$arrayRecord['CAS_code'] = '';
									$arrayRecord['operator_id'] = $_SESSION['authId'];
									$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
									$addDetailPayments = addRecords($arrayRecord, 'patient_charges_detail_payment_info');									
									//	START UPDATE PATIENT CHARGE LIST DETAIL TABLE
									$updateChargeListDetailStr = "UPDATE patient_charge_list_details SET
																	paidForProc = paidForProc + $provider_paid_amt,																	
																	balForProc = balForProc - $provider_paid_amt,
																	newBalance = newBalance - $provider_paid_amt,
																	overPaymentForProc = overPaymentForProc + $overPaymentForProc_amt,
																	paidStatus = 'Paid'
																	WHERE charge_list_detail_id = '$listChargeDetailId'";
									$updateChargeListDetailQry = imw_query($updateChargeListDetailStr);
									imw_query("UPDATE patient_charge_list SET lastPayment = '$SVC_provider_pay_amt',lastPaymentDate = '$payDate' WHERE encounter_id = '$encounter_id'");
									$status = 'Paid';
									if($SVC_provider_pay_amt>0){
										$pay_type="ins";
										$ins_type=$ins_company_no;
										patient_proc_tx_update($listChargeDetailId,$SVC_provider_pay_amt,$pay_type,$ins_type);
									}
									//include("../accounting/manageEncounterAmounts.php");
								}else{
								   if((($CAS_type == 'PR' && ($CAS_reason_code == '1' || $CAS_reason_code == '45')) 
									  || ($CAS_type == 'PI' && $CAS_reason_code == '59')
									  || ($SVC_proc_code=='92015')
									  || (($CAS_type == 'CO') && ($CAS_reason_code == '96' || $CAS_reason_code == '42' || $CAS_reason_code == '45' || $CAS_reason_code == '237' || $CAS_reason_code == '59' || $CAS_reason_code == '150' || $CAS_reason_code == '172' || $CAS_reason_code == 'B10' || $CAS_reason_code == 'B15' || $CAS_reason_code == '104' || $CAS_reason_code == '144' || $CAS_reason_code == '131' || $CAS_reason_code == '187' || $CAS_reason_code == '35' || $CAS_reason_code == '97' || $CAS_reason_code == '15' || $CAS_reason_code == '5487'.$skip_5487_co))
									 ) && ($CAS_reason_code !='' && $CAS_type!=''))
								   {}else{
									   if($SVC_provider_pay_amt==0 && (in_array($cpt_cat_id,$chk_cpt_cat_id) || strtoupper($SVC_proc_code[0])=='G')){
										   //INSERT MASTER PAYMENT
											unset($arrayRecord);
											$arrayRecord['encounter_id'] = $encounter_id;
											$arrayRecord['paid_by'] = 'Insurance';
											$arrayRecord['payment_amount'] = 0;
											$arrayRecord['payment_mode'] = $payment_method;
											if($payment_method=="Credit Card"){
												$arrayRecord['creditCardNo'] = $checkNo;
											}else{
												$arrayRecord['checkNo'] = $checkNo;
											}
											$arrayRecord['date_of_payment'] = $payDate;
											$arrayRecord['operatorId'] = $operatorId;
											$arrayRecord['insProviderId'] = $insuranceCoId;
											$arrayRecord['insCompany'] = $ins_company_no;
											$arrayRecord['paymentClaims'] = 'paid';
											$arrayRecord['transaction_date'] = date('Y-m-d');
											$arrayRecord['facility_id'] = $pay_location;
											$addPaymentId = addRecords($arrayRecord, 'patient_chargesheet_payment_info');
											//INSERT DETAIL PAYMENT
											unset($arrayRecord);
											$arrayRecord['payment_id'] = $addPaymentId;
											$arrayRecord['charge_list_detail_id'] = $listChargeDetailId;
											$arrayRecord['paidBy'] = 'Insurance';
											$arrayRecord['paidDate'] = $payDate;
											$arrayRecord['paidForProc'] = 0;
											$arrayRecord['overPayment'] = 0;
											$arrayRecord['CAS_type'] = '';
											$arrayRecord['CAS_code'] = '';
											$arrayRecord['operator_id'] = $_SESSION['authId'];
											$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
											$addDetailPayments = addRecords($arrayRecord, 'patient_charges_detail_payment_info');									
											$status = 'Paid';
											
											if($SVC_provider_pay_amt==0){
												$pay_type="ins";
												$ins_type=$ins_company_no;
												patient_proc_tx_update($listChargeDetailId,0,$pay_type,$ins_type);
											}
									   }
								   }
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
			}
		}
		if($encounter_id>0){
			$chk_era_835_proc_posted=imw_query("select id from era_835_proc_posted where era_835_proc_id='$procClaimId'");
			if(imw_num_rows($chk_era_835_proc_posted)==0){
				imw_query("insert era_835_proc_posted set era_835_proc_id='$procClaimId',patient_id='$patient_id',encounter_id='$encounter_id',
				charge_list_id='$charge_list_id_chk',charge_list_detail_id='$listChargeDetailId',cas_type='$CAS_type',cas_code='$CAS_reason_code',
				cas_amt='$CAS_amt',ins_type='$ins_company_no'");
			}
			set_write_off_trans_vip($encounter_id);
			set_payment_trans($encounter_id);
			patient_proc_bal_update($encounter_id);
			include("../accounting/manageEncounterAmounts.php");
		}
	}
	if($stop_era_neg_pay_post==""){
		//include "PostNegtiveAmt.php";
	}
}
	
	//CHNGE FILE POST STATUS
	$getPostDetailsStr = "SELECT d.postedStatus
							FROM era_835_details a,
							era_835_patient_details b,
							era_835_nm1_details c,
							era_835_proc_details d
							WHERE a.electronicFilesTblId = '$ERA_FILE_Id'
							AND a.`835_Era_Id` = b.`835_Era_Id`
							AND b.ERA_patient_details_id = c.ERA_patient_details_id
							AND d.`835_Era_Id` = a.`835_Era_Id`
							AND d.ERA_patient_details_id = b.ERA_patient_details_id";
	$getPostDetailsQry = imw_query($getPostDetailsStr);
	while($getPostDetailsRow = imw_fetch_assoc($getPostDetailsQry)){
		extract($getPostDetailsRow);		
		if($postedStatus=='Posted'){
			$postedS = true;
		}else{
			$not_postedS = true;
		}		
	}
	if(($postedS == true) && ($not_postedS == true)){
		$paidStatusForFile = 'Partially Posted';
	}else if(($postedS == true) && ($not_postedS != true)){
		$paidStatusForFile = 'Posted';
	}
	if($paidStatusForFile){
		unset($arrayRecord);
		$arrayRecord['post_status'] = $paidStatusForFile;
		$arrayRecord['processed_date'] = $today;
		$updatePostedStatus = updateRecords($arrayRecord, 'electronicfiles_tbl', 'id', $ERA_FILE_Id);
		$updatePostedStatus = UpdateRecords($ERA_FILE_Id,'id',$arrayRecord,'electronicfiles_tbl');
	}
	//CHNGE FILE POST STATUS
	$checkNo = '';
	$ma18_enc_imp=@implode(',',array_unique($ma18_enc_arr));
	$process_file=$_REQUEST['hcfa_process'];
	$elect_process=$_REQUEST['elect_process'];
	if($elect_process){
		$process_file=$elect_process;
	}
	?>
	<script>
		function moveMess(){
			document.getElementById("messageDiv").style.display = "none";
			var ma18_enc_imp='<?php echo $ma18_enc_imp; ?>';
			var process_file='<?php echo $process_file; ?>';
			if(ma18_enc_imp){
				window.open("era_hcfa_electronic.php?ma18_enc_imp="+ma18_enc_imp+"&process_file="+process_file,'Era','width=850,height=550,top=75,left=100,scrollbars=yes,resizable=1');
			}
		}
		moveMess();
	</script>
	<?php

	//Set ERA File Status
	$getPostedDetailsStr = imw_query("select postedStatus from era_835_details a,era_835_proc_details b
							WHERE a.electronicFilesTblId = '$ERA_FILE_Id' AND a.835_Era_Id = b.`835_Era_Id` 
							AND b.postedStatus = 'Not Posted'");
	if(imw_num_rows($getPostedDetailsStr)<=0){
		unset($arrayRecord);
		$arrayRecord['post_status'] = 'Posted';
		$arrayRecord['processed_date'] = date('Y-m-d');
		UpdateRecords($ERA_FILE_Id,'id',$arrayRecord,'electronicfiles_tbl');
	}else{
		unset($arrayRecord);
		$arrayRecord['post_status'] = 'Partially Posted';
		$arrayRecord['processed_date'] = date('Y-m-d');
		UpdateRecords($ERA_FILE_Id,'id',$arrayRecord,'electronicfiles_tbl');
	}
}

	$qry=imw_query("select modifiers_id,mod_prac_code from modifiers_tbl");
	while($row=imw_fetch_array($qry)){
		$mod_prac_id_arr[$row['mod_prac_code']]=$row['modifiers_id'];
	}
	
	$qry=imw_query("select * from cas_reason_code");
	while($row=imw_fetch_array($qry)){
		$all_cas_arr[$row['cas_code']]=$row['cas_desc'];
		$cas_code=trim($row['cas_code']);
		$cas_action_arr[$row['cas_action_type']][$row['cas_update_allowed']][]=$cas_code;
		if($row['cas_adjustment_negative']>0){
			$cas_adjust_negative_arr[]=$cas_code;
		}
	}
	
	$qry=imw_query("select id,file_name,file_temp_name,post_status,modified_date,file_contents from electronicfiles_tbl where id='$send_era_id'");
	$era_detail=imw_fetch_array($qry);
	$era_name=$era_detail['file_name'];
	$era_file_name_exp=explode('.tar',$era_detail['file_name']);
	
	$qry=imw_query("select * from era_835_details where electronicFilesTblId ='$send_era_id' and 835_Era_Id='$send_era_chk_id'");
	$chk_detail=imw_fetch_array($qry);
	$providerId = explode(",", $chk_detail['REF_payee_add_info']);
	$TRN_payment_type_number=$chk_detail['TRN_payment_type_number'];
	$provider_payment_amount = $chk_detail['provider_payment_amount'];
	
	$qry=imw_query("select * from era_835_patient_details join era_835_nm1_details 
					on era_835_patient_details.ERA_patient_details_id=era_835_nm1_details.ERA_patient_details_id
					where 835_Era_Id='$send_era_chk_id' and era_835_patient_details.ERA_patient_details_id in($send_era_pat_id) and era_835_nm1_details.NM1_type='QC' 
					order by era_835_nm1_details.NM1_last_name ASC");
	while($pat_detail=imw_fetch_array($qry)){
		$NM1_last_name = stripslashes($pat_detail['NM1_last_name']);
		$NM1_first_name = stripslashes($pat_detail['NM1_first_name']);
		$Patient_name = $NM1_last_name.', '.$NM1_first_name;
		$Patient_id_hic = $pat_detail['NM1_patient_id'];
		$CLP_payer_claim_control_number = $pat_detail['CLP_payer_claim_control_number'];
		$CLP_claim_submitter_id = $pat_detail['CLP_claim_submitter_id'];
		$MOA_qualifier = $pat_detail['MOA_qualifier'];
		$CLP_claim_status = $pat_detail['CLP_claim_status'];
		$era_pat_detail[$pat_detail['ERA_patient_details_id']]=$pat_detail;
	}
	
	$qry=imw_query("select * from era_835_proc_details where 835_Era_Id ='$send_era_chk_id' and ERA_patient_details_id in($send_era_pat_id)");
	while($proc_row=imw_fetch_array($qry)){
		$era_proc_detail[$proc_row['ERA_patient_details_id']][$proc_row['835_Era_proc_Id']]=$proc_row;
	}

	$clp_qry = imw_query("select * from era835clpcas where era835Id='$send_era_chk_id' and ERAPatientdetailsId in($send_era_pat_id)");
	while($clp_row=imw_fetch_array($clp_qry)){
		$casReasonType[$clp_row['ERAPatientdetailsId']][] = $clp_row['casReasonType'].' '.$clp_row['casReasonCode'];
		$casReasonAmt[$clp_row['ERAPatientdetailsId']][] = numberFormat(trim($clp_row['casReasonAmt']),2);
		$enc_level_adj_arr[$clp_row['clpCasId']] = $clp_row['casReasonAmt'];
	}
	
	if($CLP_claim_status == 1 || $CLP_claim_status == 19){
		$processedBy = 'Primary Insurance';
	}else if($CLP_claim_status == 2 || $CLP_claim_status == 20){
		$processedBy = 'Secondary Insurance';
	}else if($CLP_claim_status == 3 || $CLP_claim_status == 21){
		$processedBy = 'Tertiary Insurance';
	}else if($CLP_claim_status == 4){
		$processedBy = 'Denied';
	}else{
		$processedBy = 'No Processed By information exists.';
	}
	$colspan6_lh="6";
	$colspan4_lh="4";
?>
<div class="table-responsive" style="height:565px; overflow:auto; width:100%;">
	<div class="purple_bar"> 
    	<span>ERA - Payment Details</span>
        <span style="padding-left:30%;"><?php echo $era_name; ?></span>
    </div>
    <table class="table table-condensed" style="margin-bottom:2px;">
    	<tr>
            <td>
                <?php if($chk_detail['N1_payer_name']){ echo '<b>'.$chk_detail['N1_payer_name'].'</b><br>'.$chk_detail['N3_payer_address'].'<br>'.$chk_detail['N4_payer_city'].', '.$chk_detail['N4_payer_state'].' '.$chk_detail['N4_payer_zip'];  }?>
            </td>
            <td><strong>MEDICAL REMITTANCE NOTICE</strong></td>
        </tr>
        <tr>
            <td style="border:none;">
                <?php if($chk_detail['N3_payee_address']){ echo '<b>'.$chk_detail['N3_payee_address'].'</b><br>'.$chk_detail['N4_payee_city'].', '.$chk_detail['N4_payee_state'].' '.$chk_detail['N4_payee_zip']; } ?>
            </td>
            <td style="border:none;">
            	<div><strong>Provider # <span style="padding-left:30px;"></span>:</strong> <?php echo $providerId[1]; ?></div>
                <div><strong>Date <span style="padding-left:65px;"></span>:</strong> <?php echo get_date_format($chk_detail['chk_issue_EFT_Effective_date']); ?></div>
                <div><strong>Amount <span style="padding-left:45px;"></span>:</strong> <?php echo numberFormat($provider_payment_amount,2); ?></div>
                <div><strong>Check / EFT # <span style="padding-left:5px;"></span>:</strong> <?php echo $TRN_payment_type_number; ?></div>
            </td>
        </tr>
    </table>
    <form name="era_frm" id="era_frm" action="era_post_payments_popup.php" method="post">
    <input type="hidden" name="era_action" id="era_action" value="">
    <input type="hidden" name="send_era_id" id="send_era_id" value="<?php echo $send_era_id; ?>">
    <input type="hidden" name="send_era_chk_id" id="send_era_chk_id" value="<?php echo $send_era_chk_id; ?>">
    <input type="hidden" name="send_era_pat_id" id="send_era_pat_id" value="<?php echo $send_era_pat_id; ?>">
    <table class="table table-bordered" style="margin-bottom:2px;">
    	<tr>
            <td><b>Patient : </b><?php echo $Patient_name; ?></td>
            <td><b>HIC # : </b><?php echo $Patient_id_hic; ?></td>
            <td><b>ICN # : </b><?php echo $CLP_payer_claim_control_number; ?></td>
            <td><b>Acc. # : </b><?php echo $CLP_claim_submitter_id; ?></td>							
            <td><b>Reason : </b><?php echo $MOA_qualifier; ?></td>
        </tr>
        <tr>
            <td><b>Processed By : </b><?php echo $processedBy; ?></td>
            <?php if($era_manual_payments!="yes"){?>
                <td><b>Write off Code : </b>
                   <select name="write_off_code"  id="write_off_code" class="selectpicker" data-width="180px">
                        <option value="">Write off Code</option>
                        <?php
                        $sel_rec=imw_query("select w_id,w_code,w_default from write_off_code");
                        while($sel_write=imw_fetch_array($sel_rec)){
                        ?>
                            <option value="<?php echo $sel_write['w_id'];?>" <?php if($sel_write['w_default']=='yes'){ echo "selected";} ?>><?php echo $sel_write['w_code'];?></option>
                        <?php } ?>
                    </select>
                </td>
                <td><b>Method: </b>
                     <select name="payment_method" id="payment_method" class="selectpicker" data-width="auto">
                     	<?php
						$sel_rec=imw_query("select pm_id,pm_name from payment_methods where del_status='0' order by default_method desc, pm_name");
						while($sel_pm=imw_fetch_array($sel_rec)){
						?>
							<option value="<?php echo $sel_pm['pm_name'];?>" <?php if($sel_pm['pm_name']=='EFT'){ echo "selected";} ?>><?php echo $sel_pm['pm_name'];?></option>
						<?php } ?>
                    </select>
                </td>
                <td>
                     <div class="checkbox">
                        <input type="checkbox" id="hcfa_process" name="hcfa_process" value="hcfa" onClick="chk_paper_elect(this.value);"/>
                        <label for="hcfa_process"><strong>Paper</strong></label>
                    </div>
                </td>
                <td>
                   <div class="checkbox">
                        <input type="checkbox" id="elect_process" name="elect_process" value="electronic" onClick="chk_paper_elect(this.value);" checked/>
                        <label for="elect_process"><strong>Electronic</strong></label>
                    </div>
                </td>
          <?php } ?>     
        </tr>
    </table>
    <?php
		$getChkChargeListDetailsStr = "SELECT charge_list_detail_id FROM patient_charge_list_details WHERE del_status='0' and patient_id = '$CLP_claim_submitter_id'";
		$getChkChargeListDetailsQry = imw_query($getChkChargeListDetailsStr);
		$ChkcountRows = imw_num_rows($getChkChargeListDetailsQry);
		if($ChkcountRows){
			while($getChkChargeListDetailsRows = imw_fetch_assoc($getChkChargeListDetailsQry)){
				$chl_id_arr[] = $getChkChargeListDetailsRows['charge_list_detail_id'];
			}
		}
		
		$rend_prov=$secondaryInsName="";
		$qry = imw_query("select NM1_patient_id,NM1_type,NM1_last_name FROM era_835_nm1_details where ERA_patient_details_id in($send_era_pat_id) and (NM1_type = '82' or NM1_type = 'TT')");
		while($get_rend_prov_row = imw_fetch_array($qry)){
			if($get_rend_prov_row['NM1_type']=="82"){
				$rend_prov = $get_rend_prov_row['NM1_patient_id'];
			}
			if($get_rend_prov_row['NM1_type']=="TT"){
				$secondaryInsName = stripslashes($get_rend_prov_row['NM1_last_name']);
			}
		}
		foreach($era_pat_detail as $pat_key=>$pat_val){
		$tot_chrg_arr=$tot_allow_arr=$tot_deduct_arr=$tot_paid_arr=$tot_pat_res_arr=array();
	?>
        <table class="table table-bordered table-hover table-striped" style="margin-bottom:2px;">
            <tr class="grythead">
            	<?php if(!in_array(strtolower($billing_global_server_name), array('lehigh'))){?>
                	<th></th>
                <?php }else{$colspan6_lh="5";$colspan4_lh="3";} ?>
                <th class="text-nowrap">Rend Prov</th>
                <th class="text-nowrap">DOS</th>
                <th>POS</th>
                <th class="text-nowrap">Proc Code</th>
                <th>Mod</th>
                <th>Charges</th>
                <th>Allowed</th>
                <th>Deduct</th>
                <th>Paid</th>
                <th class="text-nowrap">Pt Res</th>
                <th>Amount</th>
                <th>CAS</th>
                <th class="text-nowrap">Rem Code</th>
            </tr>
            <?php
            $era_pat_dt=$era_pat_detail[$pat_key];	
            foreach($era_proc_detail[$pat_key] as $proc_key=>$proc_val){
                $era_proc_dt=$era_proc_detail[$pat_key][$proc_key];
                $mod_arr = explode(", ",$era_proc_dt['SVC_mod_code']);
                $REF_prov_identifier = $era_proc_dt['REF_prov_identifier'];
                if(count($era_proc_detail[$pat_key])==1){
                    $SVC_provider_pay_amt=number_format($era_pat_dt['CLP_claim_payment_amount'],2);
                }
                
                $DTM_type = $era_proc_dt['DTM_type'];
                $DTM_date = $era_proc_dt['DTM_date'];
                if($DTM_type==''){
                    if($era_pat_dt['DTM_qualifier']=="232"){
                        $DTM_type = $era_pat_dt['DTM_qualifier'];
                        $DTM_date = $era_pat_dt['DTM_date'];
                    }
                }
                $REF_prov_identifier_exp=explode('MCR',$REF_prov_identifier);
                if(strpos($REF_prov_identifier_exp[1], '_TSUC_')>0){
                    $REF_prov_identifier_exp=explode('_TSUC_',$REF_prov_identifier_exp[1]);
                }else if(strpos($REF_prov_identifier_exp[1], $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator)>0){
                    $REF_prov_identifier_exp=explode($billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator,$REF_prov_identifier_exp[1]);
                }else{
                    $REF_prov_identifier_exp=explode('TSUC',$REF_prov_identifier_exp[1]);
                }
                $listChargeDetailId_chk=$REF_prov_identifier_exp[0];
                if(strpos($REF_prov_identifier, 'MCR')){
                    $encounter_id = substr($REF_prov_identifier, 0, strpos($REF_prov_identifier, 'MCR'));
                }else{
                    $encounter_id = '';
                }
                $tsucPos = strpos($REF_prov_identifier, '_TSUC_');
                if($tsucPos){
                    $tsucId = $tsucPos+6;
                }else{
                    $tsucPos = strpos($REF_prov_identifier, 'TSUC');
                    if($tsucPos)
                        $tsucId = $tsucPos+4;
                }
                if($tsucPos){
                    $tsuc_identifier = substr($REF_prov_identifier, $tsucId);
                    if(strpos($tsuc_identifier, ',')){
                        $tsuc_identifier = trim(substr($tsuc_identifier, 0, strpos($tsuc_identifier, ',')));
                    }
                    
                    if(!$insCoType || ($ins_company_id<=0 && is_numeric($tsuc_identifier))){
                        //GET BATCH FILE INFO
                        $getBatchInfoQry = imw_query("select ins_company_id, ins_comp from batch_file_submitte where Transaction_set_unique_control = '$tsuc_identifier'");
                        $getBatchInfoRow = imw_fetch_assoc($getBatchInfoQry);
                        $ins_company_id = $getBatchInfoRow['ins_company_id'];
                        $ins_comp = $getBatchInfoRow['ins_comp'];
                        if($ins_comp == 'primary'){
                            $insCoType = 'Primary';
                        }else if($ins_comp == 'secondary'){
                            $insCoType = 'Secondary';
                        }
                    }
                    $REF_prov_identifier = substr($REF_prov_identifier, 0, $tsucPos);												
                }
                
                if(strpos($REF_prov_identifier, ",")){
                    $REFProvId = explode(",", $REF_prov_identifier);
                }
                if(strlen($REFProvId[1])>3){
                    $REFProvId[1] = substr($REFProvId[1], 0, 3).'...';
                }
                $mcrPos = strpos($REF_prov_identifier, 'MCR');
                if($mcrPos){
                    $chargeListDetailId =  substr($REF_prov_identifier, $mcrPos+3);
                }											
                if(strpos($chargeListDetailId, ",")){
                    $charge_list_detail_id = trim(substr($chargeListDetailId, 0, strpos($chargeListDetailId, ",")));
                }else{
                    $charge_list_detail_id = trim($chargeListDetailId);
                }
                
                if($CLP_claim_submitter_id!="" && !$encounter_id){
                    $chk_enc_allow_amt=$enc_base_dos_arr=array();
                    $mod_id1=$mod_id2="";
                    if($mod_arr[0]!=""){
                        $mod_id1=$mod_prac_id_arr[$mod_arr[0]];
                    }
                    if($mod_arr[1]!=""){
                        $mod_id2=$mod_prac_id_arr[$mod_arr[1]];
                    }
                                                        
                    $chl_qry = "select pcl.charge_list_id,pcl.encounter_id,pcld.approvedAmt,pcld.totalAmount,pcld.charge_list_detail_id
                                from patient_charge_list pcl join patient_charge_list_details pcld on pcl.charge_list_id=pcld.charge_list_id
                                where pcld.del_status='0' and pcl.date_of_service='$DTM_date'
                                and pcld.patient_id ='$CLP_claim_submitter_id'";
                                
                    if($mod_id1>0 && $mod_id2>0){
                        $chl_qry.=" AND ((pcld.modifier_id1 = '$mod_id1' and pcld.modifier_id2 = '$mod_id2') 
                                    or (pcld.modifier_id1 = '$mod_id2' and pcld.modifier_id2 = '$mod_id1'))";
                    }else{
                        if($mod_id1>0){
                            $chl_qry.=" AND pcld.modifier_id1 = '$mod_id1'";
                        }
                        if($mod_id2>0){
                            $chl_qry.=" AND pcld.modifier_id2 = '$mod_id2'";
                        }		
                        if($mod_id1<=0 && $mod_id2<=0){
                            $chl_qry.=" AND pcld.modifier_id1 = '' and pcld.modifier_id2 = ''";
                        }	
                    }
                    $chl_sql = imw_query($chl_qry);
                    $chl_cont=imw_num_rows($chl_sql);
                    while($chl_row = imw_fetch_assoc($chl_sql)){
                        $charge_list_id = $chl_row['charge_list_id'];
                        $charge_list_detail_id = $chl_row['charge_list_detail_id'];
                        $encounterId = $chl_row['encounter_id'];
                        $enc_base_dos_arr[$chl_row['encounter_id']]=$chl_row['encounter_id'];
                        if($chl_cont==1){
                            $encounter_id = $chl_row['encounter_id'];
                        }
                        if($chl_row['approvedAmt']!=$chl_row['totalAmount']){
                            $chk_enc_allow_amt[$chl_row['charge_list_detail_id']]=$chl_row['approvedAmt'];
                        }
                    }
                    if(count($enc_base_dos_arr)==1 && $encounter_id<=0){
                        $encounter_id = $encounterId;
                    }
                    
                    if($encounter_id>0){
                        $not_match_enc_col="";
                    }else{
                        $not_match_enc_col='style="background-color:#FF9;"';
                    }
                    
                    $enc_allow_amt="";
                    if($chk_enc_allow_amt[$listChargeDetailId_chk]!=""){
                        $enc_allow_amt=$chk_enc_allow_amt[$listChargeDetailId_chk];
                    }
                    $chl_dos = $DTM_date;
                }else{
                    $not_match_enc_col=$enc_allow_amt="";
                    $chl_qry = "select pcl.charge_list_id,pcld.approvedAmt,pcld.totalAmount,pcl.date_of_service
                                from patient_charge_list pcl join patient_charge_list_details pcld on pcl.charge_list_id=pcld.charge_list_id
                                where pcld.del_status='0' and pcl.encounter_id='$encounter_id'
                                and pcld.charge_list_detail_id ='$listChargeDetailId_chk'";
                    $chl_sql = imw_query($chl_qry);		
                    $chl_row=imw_fetch_array($chl_sql);
                    $charge_list_detail_id = $listChargeDetailId_chk;
                        
                    if(count($era_file_name_exp)<2){
                        if(imw_num_rows($chl_sql)==0){
                            $not_match_enc_col='style="background-color:#FF9;"';
                        }
                    }
                    
                    if($chl_row['approvedAmt']!=$chl_row['totalAmount']){
                        $enc_allow_amt=$chl_row['approvedAmt'];
                    }
                    $chl_dos = $chl_row['date_of_service'];
                }
                
                $cas_reason=$cas_amount='';
                $deduct_amount=$writeoff_amount=0;
                $era_ret_cas_arr=era_reason_amt($era_proc_dt['CAS_type'],$era_proc_dt['CAS_reason_code'],$era_proc_dt['CAS_amt'],$cas_action_arr);
                $writeoff_amount=$era_ret_cas_arr['era_writeoff'];
                $deduct_amount=$era_ret_cas_arr['era_deduct'];
                $cas_reason=$era_ret_cas_arr['era_reason'];
                $cas_amount=$era_ret_cas_arr['era_amount'];
                $pat_res_amount=$era_ret_cas_arr['era_pat_res_amount'];
    
    
                $file_data=$era_detail['file_contents'];
                $plb_exp_data=explode('~',$file_data);
                $plb_srn=0;
                $plb_patient_arr=array();
                $plb_patient_arr=array();
                for($j=0;$j<count($plb_exp_data);$j++){
                    if(strstr($plb_exp_data[$j],'PLB')){
                        $plb_srn++;
                        $final_plb_code_arr=array();
                        $plb_exp_final_data=explode('*',$plb_exp_data[$j]);
                        for($k=0;$k<count($plb_exp_final_data);$k++){
                            if($k>2){
                                $plb_res_code_arr=explode(':',$plb_exp_final_data[$k]);
                                $final_plb_code_arr['reason_code'][]=$plb_res_code_arr[0];
                                if(count($plb_res_code_arr)>1){
                                    $final_plb_code_arr['clp_control_number'][trim($plb_res_code_arr[1])]=trim($plb_res_code_arr[1]);
                                }
                                $final_plb_code_arr['reason_amount'][]=numberFormat(trim($plb_exp_final_data[$k+1]),2);
                                $k++;
                            }
                        }
                        $patientName_frm_arr=array();
                        $Encounter_frm_arr=array();
                        foreach($final_plb_code_arr['clp_control_number'] as $val){
                            $final_val="";
                            $final_val_arr=explode(' ',$val);
                            $final_val=$final_val_arr[0];
                            $clp_qry=imw_query("select CLP_claim_submitter_id,ERA_patient_details_id from era_835_patient_details where CLP_payer_claim_control_number like '$final_val%'");
                            while($clp_row=imw_fetch_array($clp_qry)){
                                $CLP_patient_id=$clp_row['CLP_claim_submitter_id'];
                                $plb_patient_arr[$CLP_patient_id]=$CLP_patient_id;
                            }
                        } 
                    }
                }
                
                $chk_proc_dis="";
                $getERADetailchkstr = imw_query("SELECT * FROM era_835_details WHERE TRN_payment_type_number = '$TRN_payment_type_number'");
                if(imw_num_rows($chl_sql)==0 || !in_array($charge_list_detail_id,$chl_id_arr) || imw_num_rows($clp_qry)>0 || in_array($CLP_claim_submitter_id,$plb_patient_arr) || imw_num_rows($getERADetailchkstr)>1){
                     $chk_proc_dis="disabled";
                }
				
				if(strtolower($era_proc_dt['rem_code'])=='n706'){
					$chk_proc_dis="disabled";
				}
				
				if(in_array(strtolower($billing_global_server_name), array('northshore'))){
					$qry = imw_query("select charge_list_id from patient_charge_list where del_status='0' and encounter_id = '$encounter_id' and date_of_service<'2019-04-29' and gro_id in('105','201')");
					if(imw_num_rows($qry)>0){
						$chk_proc_dis="disabled";
					}
				}
                
                $tot_chrg_arr[]=$era_proc_dt['SVC_proc_charge'];
                $tot_allow_arr[]=$era_proc_dt['AMT_amount'];
                $tot_deduct_arr[]=$deduct_amount;
                $tot_paid_arr[]=$era_proc_dt['SVC_provider_pay_amt'];
                $tot_pat_res_arr[]=$pat_res_amount;
             
               ?>
                <tr>
                	<?php if(!in_array(strtolower($billing_global_server_name), array('lehigh'))){?>
                        <td class="text-nowrap">
                            <?php
                            if($era_manual_payments!=""){
                                echo $era_proc_dt['postedStatus'];
                            }else{
                                if($era_proc_dt['postedStatus']!='Posted'){
                                    ?>
                                     <div class="checkbox">
                                        <input style="cursor:pointer;" <?php echo $chk_proc_dis; ?> type="checkbox" id="era_proc_id_<?php echo $proc_key; ?>" name="era_proc_id[]" onClick="return chkOrNot('<?php echo $era_proc_dt['SVC_proc_code']; ?>', '<?php echo $era_proc_dt['AMT_amount']; ?>', '<?php echo $era_proc_dt['SVC_provider_pay_amt']; ?>', '<?php echo $CLP_claim_status; ?>');" value="<?php echo $proc_key; ?>">
                                        <label for="era_proc_id_<?php echo $proc_key; ?>"></label>
                                    </div>
                                    <?php
                                }else{
                                    ?>
                                    <img style="cursor:pointer;" src="../../library/images/confirm.gif" border="0">
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    <?php } ?>    
                    <td><?php echo $rend_prov; ?></td>
                    <td class="text-nowrap"><?php echo get_date_format($era_proc_dt['DTM_date']); ?></td>
                    <td><?php if($REFProvId[1]) echo $REFProvId[1]; else echo '-'; ?></td>
                    <td><?php echo $era_proc_dt['SVC_proc_code']; ?></td>
                    <td><?php if($era_proc_dt['SVC_mod_code']) echo $era_proc_dt['SVC_mod_code']; else echo 'N/A'; ?></td>
                    <td class="text-right"><?php echo numberFormat($era_proc_dt['SVC_proc_charge'],2); ?></td>
                    <td class="text-right">
                        <?php echo numberFormat($era_proc_dt['AMT_amount'],2);?>
                        <?php if($era_proc_dt['AMT_amount']!=$enc_allow_amt && $enc_allow_amt!=""){?>
                            <span style="color:#F00; font-weight:bold;">!</span>
                        <?php }
                            $oa_color="";
                            if(strpos($cas_reason, 'OA 100')){
                                $oa_color="color:#F00";
                            }
                        ?>
                    </td>
                    <td class="text-right"><?php echo numberFormat($deduct_amount,2); ?></td>
                    <td class="text-right"><?php echo numberFormat($era_proc_dt['SVC_provider_pay_amt'],2);?></td>
                    <td class="text-right"><?php echo numberFormat($pat_res_amount,2); ?></td>
                    <td><?php if($cas_amount!=""){ echo $cas_amount;}else{ echo "-";} ?></td>
                    <td style="cursor:pointer; <?php echo $oa_color; ?>" <?php echo show_tooltip($cas_reason_detail); ?>>
                        <?php if($cas_reason) echo $cas_reason; else echo "-"; ?>
                    </td>
                    <td><?php echo $era_proc_dt['rem_code']; ?></td>
                </tr>
            <?php } ?>
            <?php if(count($enc_level_adj_arr)>0){?>
             <tr>
                <th colspan="<?php echo $colspan6_lh; ?>" class="text-right">Sub Total :</th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_chrg_arr),2); ?></th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_allow_arr),2); ?></th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_deduct_arr),2); ?></th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_paid_arr),2); ?></th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_pat_res_arr),2); ?></th>
                <th colspan="6"></th>
            </tr>
            <tr>
                <th colspan="<?php echo $colspan6_lh; ?>" class="text-right">Encounters Level Adjustment Amount :</th>
                <th></th>
                <th></th>
                <th></th>
                <th class="text-right"><?php echo numberFormat(trim(array_sum($enc_level_adj_arr)),2); ?></th>
                <th></th>
            </tr>
            <?php } ?>
            <tr>
                <th colspan="<?php echo $colspan4_lh; ?>" class="text-left"><?php echo $secondaryInsName; ?></th>
                <th colspan="2" class="text-right">Grand Total :</th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_chrg_arr),2); ?></th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_allow_arr),2); ?></th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_deduct_arr),2); ?></th>
                <th class="text-right">
                    <?php
                        $grand_CLP_claim_payment_amount=array_sum($tot_paid_arr)-array_sum($enc_level_adj_arr);
                        echo numberFormat($grand_CLP_claim_payment_amount,2); 
                    ?>
                </th>
                <th class="text-right"><?php echo numberFormat(array_sum($tot_pat_res_arr),2); ?></th>
                <th colspan="6"></th>
            </tr>
             <input type="hidden" name="plb_patient_txt" value="<?php echo implode(',',$plb_patient_arr); ?>">
        </table>
    <?php } ?>
    </form>
</div>
</div>
<?php
	if(in_array(strtolower($billing_global_server_name), array('lehigh','shoreline'))){
		$print_fun="window.print();";
	}else{
		$print_fun="printEraDetailsFn('".$_REQUEST['send_era_id']."','".$_REQUEST['send_era_pat_id']."')";
	}
?>
<footer>
	<div class="text-center">
    	<?php if($era_manual_payments!=""){?>
        <input type="button" id="close" class="btn btn-danger" value="Close"  onClick="window.close();">
        <?php }else{?>
		<input type="button" id="process" class="btn btn-success" value="Process" onClick="checkVal();">
        <?php } ?>
        <input type="button" id="era_print" class="btn btn-success" value="Print" onClick="<?php echo $print_fun; ?>">
	</div>
</footer>
</body>
</html>
<script type="text/javascript">
function chk_paper_elect(val){
	if(val=='hcfa'){
		if($('#hcfa_process').is(':checked')==true){
			$('#elect_process').prop("checked",false);
		}else{
			$('#elect_process').prop("checked",true);
			$('#hcfa_process').prop("checked",false);
		}
	}else{
		if($('#elect_process').is(':checked')==true){
			$('#hcfa_process').prop("checked",false);
		}else{
			$('#hcfa_process').prop("checked",true);
			$('#elect_process').prop("checked",false);
		}
	}
}
function checkVal(){
	$('#era_action').val('');
	var flag = 0;
	var obj = document.getElementsByName('era_proc_id[]');
	var checkBxLen = obj.length;
	for(i=0;i<checkBxLen;i++){
		if(obj[i].checked == true){			
			++flag;
		}
	}
	if(flag<=0){
		alert('Please select any claim to post!');
	}else if(document.getElementById("write_off_code").value==""){
		alert('Please select the Write off Code');
	}else{
		$('#era_action').val('process');
		document.era_frm.submit();
	}
}
function chkOrNot(proAmt, allowAmt, payAmt, status){
	var postCancel = false;
	if(status!=1 && status!=2 && status!=3 && status!=4 && status!=19 && status!=20 && status!=21 && status!=22){
		var postCancel = true;
	}
	if(payAmt<0){
	}else{
		if(proAmt<0 || allowAmt<0){
			var postCancel = true;
		}
	}
	if(postCancel == true){
		alert("Can't post the charges.")
		return false;
	}
}
function printEraDetailsFn(eFileId,send_era_pat_id){
	window.open("../billing/era_post_payments_print.php?ERA_FILE_id="+eFileId+"&send_era_pat_id="+send_era_pat_id,'ClaimPrint','width=150,height=100,top=25,left=70,scrollbars=no,resizable=1');
}
</script>    