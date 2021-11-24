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
ini_set("memory_limit","3072M");
set_time_limit(0); 
$without_pat="yes"; 
require_once("../accounting/acc_header.php");
require_once("../../library/classes/billing_functions.php");
require_once("../../interface/common/assign_new_task.php");
if(!isset($pg_title)){
	$pg_title = 'ERA Post Payments';
}

$era_files_id=$_REQUEST['era_files_id'];
$era_files_id_imp=implode(',',$era_files_id);
$entered_date = date('Y-m-d H:i:s');
$operatorId = $_SESSION['authId'];

//Archive Selected ERA Files
if($_REQUEST['era_action']=="archive"){
	imw_query("update electronicfiles_tbl set archive_status='1' WHERE id in($era_files_id_imp)");
}

//Delete Selected ERA Files
if($_REQUEST['era_action']=="delete"){
	
	$qry = imw_query("SELECT 835_Era_Id FROM era_835_details WHERE electronicFilesTblId in($era_files_id_imp)");
	while($row = imw_fetch_array($qry)){
		$era_detail_arr[]=$row['835_Era_Id'];
	}
	$era_detail_imp=implode(',',$era_detail_arr);
	
	$qry = imw_query("SELECT ERA_patient_details_id FROM era_835_patient_details WHERE 835_Era_Id in($era_detail_imp)");
	while($row = imw_fetch_array($qry)){
		$era_pat_detail_arr[]=$row['ERA_patient_details_id'];
	}
	$era_pat_detail_imp=implode(',',$era_pat_detail_arr);
	
	imw_query("delete FROM electronicfiles_tbl WHERE id in($era_files_id_imp)");
	imw_query("delete FROM era_835_details WHERE electronicFilesTblId in($era_files_id_imp)");
	imw_query("delete FROM era_835_patient_details WHERE 835_Era_Id in($era_detail_imp)");
	imw_query("delete FROM era_835_proc_details WHERE 835_Era_Id in($era_detail_imp)");
	imw_query("delete FROM era_835_nm1_details WHERE ERA_patient_details_id in($era_pat_detail_imp)");
}
if($_REQUEST['era_action']=="process"){
	$ERA_FILE_Id=$_REQUEST['send_era_id'];
	$sel_enc_chk_id=$_REQUEST['sel_enc_chk_id'];
	$sel_enc_chk_id_str=implode(',',$sel_enc_chk_id);
	if( !$sel_enc_chk_id_str ) $sel_enc_chk_id_str = 0;
	if($_REQUEST['pay_location']>0){
		$pay_location=$_REQUEST['pay_location'];
	}else{
		$pay_location=$_SESSION['login_facility'];
	}
	if($era_manual_payments=="yes"){
		imw_query("update era_835_proc_details set postedStatus='Posted',manual_posted='1' where ERA_patient_details_id in($sel_enc_chk_id_str) and postedStatus!='Posted'");
	}else{
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
		
		$qry = imw_query("select cpt_fee_id,cpt_cat_id,cpt4_code,cpt_prac_code from cpt_fee_tbl where delete_status='0'");
		while($fet_cpt = imw_fetch_array($qry)){
			$cpt_fee_tbl_arr[$fet_cpt['cpt_fee_id']] = $fet_cpt;
		}
		
		$qry = imw_query("select id,in_house_code,Payer_id,Payer_id_pro from insurance_companies");
		while($fet_ins = imw_fetch_array($qry)){
			$ins_tbl_arr[$fet_ins['id']] = $fet_ins;
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
			
			$getPatProcDetailsStr = "SELECT * FROM era_835_proc_details WHERE `835_Era_Id` = '$Era_835_Id' and ERA_patient_details_id in($sel_enc_chk_id_str)";
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
				foreach($cpt_fee_tbl_arr as $cpt_key => $cpt_val){
					if(strtolower($cpt_fee_tbl_arr[$cpt_key]['cpt4_code'])==strtolower($SVC_proc_code)){
						$cpt_cat_id = $cpt_fee_tbl_arr[$cpt_key]['cpt_cat_id'];
					}
				}
				// GET PROC ID FROM CPT4_CODE
							
				// GET MOA QUALIFIER AND Processed By Details
					$qry = imw_query("select * from era_835_patient_details where ERA_patient_details_id = '$ERA_patient_details_id'");
					$moaDetails = imw_fetch_object($qry);
					$MOAQualifier = $moaDetails->MOA_qualifier;
					$CLP_claim_status = $moaDetails->CLP_claim_status;
					$CLP_claim_submitter_id = $moaDetails->CLP_claim_submitter_id;
					$CLP_claim_payment_amount = $moaDetails->CLP_claim_payment_amount;
					//1/19 = Claim processed as Primary; 2/20 = Secondary; 3/21 = Tertiary; 4 = Denied; 22 = Reversal
					if($CLP_claim_status != 1 && $CLP_claim_status!=2 && $CLP_claim_status!=3 && $CLP_claim_status!=4 && $CLP_claim_status!=19 && $CLP_claim_status!=20 && $CLP_claim_status!=21 && $CLP_claim_status!=22) 
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
						$cpt_fee_id_arr=array();
						foreach($cpt_fee_tbl_arr as $cpt_key => $cpt_val){
							if(strtolower($cpt_fee_tbl_arr[$cpt_key]['cpt4_code'])==strtolower($SVC_proc_code)){
								$cpt_fee_id_arr[$cpt_fee_tbl_arr[$cpt_key]['cpt_fee_id']]=$cpt_fee_tbl_arr[$cpt_key]['cpt_fee_id'];
							}
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
								$qry = imw_query("select * from modifiers_tbl where mod_prac_code = '$modifiers_code1' order by delete_status asc limit 0,1");
								$getModID = imw_fetch_object($qry);
								$modifiersId1 = $getModID->modifiers_id;
							}
							if($SVC_mod_code_exp[1]!=""){
								$modifiers_code2=trim($SVC_mod_code_exp[1]);
								$qry = imw_query("select * from modifiers_tbl where mod_prac_code = '$modifiers_code2' order by delete_status asc limit 0,1");
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
					$getInsStr = "select primaryInsuranceCoId,secondaryInsuranceCoId from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'";				
					$getInsQry = imw_query($getInsStr);
					$getInsRow = imw_fetch_array($getInsQry);
					$insuranceCoId_pri = $getInsRow['primaryInsuranceCoId'];
					$insuranceCoId_sec = $getInsRow['secondaryInsuranceCoId'];
					if($insuranceCoId_pri!=$insuranceCoId_sec){
						foreach($ins_tbl_arr as $ins_key => $ins_val){
							if($ins_tbl_arr[$ins_key]['Payer_id']==$REF_provider_ref_id){
								$getInsRow2=$ins_tbl_arr[$ins_key];
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
				
				if(in_array(strtolower($billing_global_server_name), array('northshore'))){
					$qry = imw_query("select charge_list_id from patient_charge_list where del_status='0' and encounter_id = '$encounter_id' and date_of_service<'2019-04-29' and gro_id in('105','201')");
					if(imw_num_rows($qry)>0){
						$encounter_id="";
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
									
				$getInsStr = "select primaryInsuranceCoId,secondaryInsuranceCoId,tertiaryInsuranceCoId from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'";				
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
				foreach($cpt_fee_tbl_arr as $cpt_key => $cpt_val){
					if(strtolower($cpt_fee_tbl_arr[$cpt_key]['cpt4_code'])==strtolower($SVC_proc_code)){
						$cpt_fee_id = $cpt_fee_tbl_arr[$cpt_key]['cpt_fee_id'];
					}
				}
				// GET PROC ID FROM CPT4_CODE
		
				// GET ENCOUNTER DETAILS.
				$qry = imw_query("select * from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'");
				$getAccountDetails = imw_fetch_assoc($qry);
				if(is_array($getAccountDetails))
					extract($getAccountDetails);
				// GET ENCOUNTER DETAILS.
				
				$chk_ins_exist=1;
				if($ins_company_no>0 && $encounter_id>0){
					if($ins_company_no==3){
						$ins_comp_id=$tertiaryInsuranceCoId;
					}else if($ins_company_no==2){
						$ins_comp_id=$secondaryInsuranceCoId;
					}else{
						$ins_comp_id=$primaryInsuranceCoId;
					}
					$pr_qry=imw_query("select ins_comp_id from posted_record where encounter_id='$encounter_id' and posted_for='$ins_company_no' order by id desc limit 0,1");
					$pr_result=imw_fetch_array($pr_qry);
					if($pr_result['ins_comp_id']>0 && $pr_result['ins_comp_id']!=$ins_comp_id){
						$chk_ins_exist=0;
					}
				}
				
				if((($listChargeDetailId!='') || ($listChargeDetailId!=0)) && $paymentOf!="" && $chk_ins_exist>0){	
					
					$med_ins_chk=false;
					if(strtolower($ins_tbl_arr[$insuranceCoId]['in_house_code'])=='medicare'){
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
										
										if($paymentOf=='secondary_paid' && $CASAmtArr[$inc]>0 && $typeCode == 'OA 23' && in_array(strtolower($billing_global_server_name), array('shoreline')) && (in_array($ins_tbl_arr[$insuranceCoId]['Payer_id_pro'],array('38217')) || in_array($ins_tbl_arr[$insuranceCoId]['Payer_id'],array('38217')))){
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
									//$AppAmtChk==$svc_pay_chk || $allow_post_chk==0 && $paymentOf=='secondary_paid'
									if($paymentOf=='secondary_paid'){
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
														  
													if($write_off_amt>0){
														$pay_type="ins";
														$ins_type=$ins_company_no;
														patient_proc_tx_update($listChargeDetailId,$write_off_amt,$pay_type,$ins_type);
													}
													
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
											$insertAdjAmt = addRecords($arrayRecord, 'account_payments');
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
									   }else{
										   if((($CAS_type == 'PR' && ($CAS_reason_code == '1' || $CAS_reason_code == '45')) 
											  || ($CAS_type == 'PI' && $CAS_reason_code == '59') 
											  || ($SVC_proc_code=='92015')
											  || (($CAS_type == 'CO') && ($CAS_reason_code == '96' || $CAS_reason_code == '42' || $CAS_reason_code == '45' || $CAS_reason_code == '237' || $CAS_reason_code == '59' || $CAS_reason_code == '150' || $CAS_reason_code == '172' || $CAS_reason_code == 'B10' || $CAS_reason_code == 'B15' || $CAS_reason_code == '104' || $CAS_reason_code == '144' || $CAS_reason_code == '131' || $CAS_reason_code == '187' || $CAS_reason_code == '35' || $CAS_reason_code == '97' || $CAS_reason_code == '15' || $CAS_reason_code == '5487'.$skip_5487_co))
											 ) && ($CAS_reason_code !='' && $CAS_type!=''))
										   {
										   }else{
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
						$entered_date_time=date('Y-m-d H:i:s');
						imw_query("insert era_835_proc_posted set era_835_proc_id='$procClaimId',patient_id='$patient_id',encounter_id='$encounter_id',
						charge_list_id='$charge_list_id_chk',charge_list_detail_id='$listChargeDetailId',cas_type='$CAS_type',cas_code='$CAS_reason_code',
						cas_amt='$CAS_amt',ins_type='$ins_company_no',chk_date='$chk_issue_EFT_Effective_date',entered_date='$entered_date_time'");
					}
					set_write_off_trans_vip($encounter_id);
					set_payment_trans($encounter_id);
					patient_proc_bal_update($encounter_id);
				}
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
			//document.getElementById("messageDiv").style.display = "none";
			var ma18_enc_imp='<?php echo $ma18_enc_imp; ?>';
			var process_file='<?php echo $process_file; ?>';
			if(ma18_enc_imp){
				window.open("era_hcfa_electronic.php?ma18_enc_imp="+ma18_enc_imp+"&process_file="+process_file,'Era','width=1150,height=550,top=75,left=100,scrollbars=yes,resizable=1');
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
function era_reason_amt($case_type,$case_reason,$case_amt,$era_835_proc_id,$cas_action_arr){
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
	
	if(count($set_cas_type_arr[100])>0){
		$set_cas_type_imp=implode(',',$set_cas_type_arr);
		if($case_type!=$set_cas_type_imp){
			$set_cas_type_imp_fin=implode(', ',$set_cas_type_arr);
			imw_query("update era_835_proc_details set CAS_type='$set_cas_type_imp_fin' where 835_Era_proc_Id='$era_835_proc_id' and postedStatus='Not Posted'");
		}
	}
	
	$ret_arr['era_writeoff']=$writeoff_amount;
	$ret_arr['era_deduct']=$deduct_amount;
	$ret_arr['era_reason']=$cas_reason;
	$ret_arr['era_amount']=$cas_amount;
	return $ret_arr;
}
$frm_act="era_post_payments.php";
$process_btn = "Start Process";
if($era_manual_payments=="yes"){
	$frm_act="era_manual_payments.php";
	$process_btn = "Manual Post";
}
/*$grp_qry=imw_query("select N1_payee_name from era_835_details where N1_payee_name!='' group by N1_payee_name");
while($grp_row=imw_fetch_array($grp_qry)){
	$grp_detail[$grp_row['N1_payee_name']]=$grp_row['N1_payee_name'];	
}*/
$grp_qry=imw_query("select group_color,gro_id,name,group_NPI from groups_new");
while($grp_row=imw_fetch_array($grp_qry)){
	$grp_detail[$grp_row['gro_id']]=$grp_row;	
}
?>
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/billinginfo.css" rel="stylesheet">
<form method="post" id="era_frm" name="era_frm" action="<?php echo $frm_act; ?>">
<input type="hidden" name="era_action" id="era_action" value="">
<input type="hidden" name="send_era_id" id="send_era_id" value="<?php echo $_REQUEST['send_era_id']; ?>">
<input type="hidden" name="send_era_chk_id" id="send_era_chk_id" value="<?php echo $_REQUEST['send_era_chk_id']; ?>">
<input type="hidden" name="era_manual_payments" id="era_manual_payments" value="<?php echo $era_manual_payments; ?>">
    <div class="row">
        <div class="col-sm-3">
             <div class="row filterbatchfile" style="padding:5px !important;">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="era_srh_ins" style="padding-bottom:10px;">Search By Insurance</label>
                        <select class="selectpicker" data-width="100%" name="era_srh_ins" id="era_srh_ins" onChange="return era_files();">
                            <option value="Insurance All" <?php if($_REQUEST['era_srh_ins'] == 'Insurance All') echo 'SELECTED'; ?>>Insurance All</option>
                            <option value="Medicare" <?php if($_REQUEST['era_srh_ins'] == 'Medicare') echo 'SELECTED'; ?>>Medicare</option>
                            <option value="Emdeon" <?php if($_REQUEST['era_srh_ins'] == 'Emdeon') echo 'SELECTED'; ?>>Emdeon</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label style="width:100%; padding:2px 0px 10px 0px;">
                    	<select name="era_grp_srh" id="era_grp_srh" class="selectpicker" data-width="100%" data-actions-box="true" data-title="Select Group" data-size="10" onChange="era_files();">
							 <option value="">All Groups</option>
							 <?php
                                    foreach($grp_detail as $g_key => $g_id){
                                        if($grp_detail[$g_key]['gro_id']==$_REQUEST['era_grp_srh']){
                                            $sel = 'selected="selected"';
                                        }else{
                                            $sel = '';
                                        }
                                ?>
                                        <option value="<?php echo $grp_detail[$g_key]['gro_id']; ?>" <?php echo $sel; ?>><?php echo ucfirst($grp_detail[$g_key]['name']); ?></option>
                                        <?php
                                    }
                                ?>
                        </select>
                    </label>
                    <div class="input-group" style=" padding-bottom:5px;">
                        <input type="text" name="srh_key" id="srh_key" class="form-control" value="<?php echo $_REQUEST['srh_key']; ?>" onKeyPress="{if (event.keyCode==13) era_files();}">
                        <div class="input-group-btn">
                            <button type="button" class="btn" onClick="era_files();"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div style="margin:0px; height:<?php echo $_SESSION['wn_height']-460;?>px; overflow-y:auto;" id="era_list_div">
                <?php
                    $era_whr=$pat_srh="";
					$era_whr="a.id>0 ";
					if($srh_key==""){
						if($_REQUEST['era_status']=="Archived"){
							$era_whr.=" and a.archive_status='1'";
						}else{
							$era_whr.=" and a.archive_status='0'";
							if($_REQUEST['era_status']=="Posted"){
								$era_whr.=" and a.post_status ='Posted'";
							}else{
								$era_whr.=" and a.post_status !='Posted'";
							}
						}
					}
					
					$srh_key=$_REQUEST['srh_key'];
                    if($srh_key!=""){
                        $srh_key_arr=explode(',',$srh_key);
                        if(preg_match('/[-]/', $srh_key)){
                            $srh_key_arr=explode('-',$srh_key);
                            if(strlen($srh_key_arr[2])==2){$srh_key_arr[2]="20".$srh_key_arr[2];}
                            $srh_key_date=$srh_key_arr[2].'-'.$srh_key_arr[0].'-'.$srh_key_arr[1];
                            $era_whr.=" AND b.chk_issue_EFT_Effective_date='$srh_key_date'";
                        }else if(preg_match('/[$]/', $srh_key_arr[0])){
                            $srh_key=str_replace(',','',number_format(trim(str_replace('$','',$srh_key)),2));
                            $era_whr.=" AND provider_payment_amount LIKE '$srh_key'";
                        }else if(preg_match('/[0-9]/', $srh_key_arr[0])){
                            $era_whr.=" AND TRN_payment_type_number LIKE '$srh_key%'";
                        }else{
                            /*$pat_srh="yes";
                            if($srh_key_arr[0]!=""){
                                $era_whr.=" AND d.NM1_last_name ='".trim($srh_key_arr[0])."'";
                            }
                            if($srh_key_arr[1]!=""){
                                $era_whr.=" AND d.NM1_first_name ='".trim($srh_key_arr[1])."'";
                            }*/
                        }
                    }
					
					if($_REQUEST['era_srh_ins']!="" && $_REQUEST['era_srh_ins']!='Insurance All'){
                        if($_REQUEST['era_srh_ins'] == 'Medicare'){
                            $era_whr.=" AND b.N1_payer_name LIKE '%Medicare%'";
						}else if($_REQUEST['era_srh_ins'] == 'Emdeon'){
                            $era_whr.=" AND b.N1_payer_name NOT LIKE '%Medicare%'";
						}
                    }
					
					if($_REQUEST['era_grp_srh']!=''){
						$era_whr.=" AND (b.N1_payee_name LIKE '%".$grp_detail[$_REQUEST['era_grp_srh']]['name']."%' or b.N1_payee_id='".$grp_detail[$_REQUEST['era_grp_srh']]['group_NPI']."')";
					}
                    
                    if($_REQUEST['era_type'] == 'check'){
                        if($pat_srh!=""){
                            $era_whr.=" GROUP BY b.TRN_payment_type_number";
                        }
					}
					$era_whr.=" ORDER BY b.chk_issue_EFT_Effective_date DESC";
                ?>
                <table class="table table-striped table-bordered table-hover">
                    <tr class="grythead">
                        <th>
                            <div class="checkbox">
                                <input type="checkbox" id="chkbx_all" name="chkbx_all" onClick="return chk_all();">
                                <label for="chkbx_all"></label>
                            </div>
                        </th> 
                        <th id="era_type_th">
                            <select class="selectpicker" name="era_type" id="era_type" onChange="return era_files();" data-width="100%">
                                <option value="era" <?php if($era_type == 'era') echo 'SELECTED'; ?> selected="selected">Claims File</option>
                                <option value="check" <?php if($era_type == 'check') echo 'SELECTED'; ?>>Check/EFT#</option>
                                <option value="insurance" <?php if($era_type == 'insurance') echo 'SELECTED'; ?>>Insurance</option>
                            </select>
                        </th>
                        <th class="text-nowrap">Check Date</th>
                        <th>Amount</th>
                        <th id="era_status_th"> 
                            <select class="selectpicker" name="era_status" id="era_status" data-width="100%" onChange="era_files()">
                                <option value="Not Posted" <?php if($era_status == 'Not Posted') echo 'SELECTED'; ?>>Not Posted</option>
                                <option value="Posted" <?php if($era_status == 'Posted') echo 'SELECTED'; ?>>Posted</option>
                                <option value="Archived" <?php if($era_status == 'Archived') echo 'SELECTED'; ?>>Archived</option>
                            </select>
                       </th>
                    </tr>
                    <?php
                   $qry="select a.id,a.file_name,a.file_temp_name,a.post_status,a.processed_date,
						b.chk_issue_EFT_Effective_date,b.835_Era_Id,b.TRN_payment_type_number,b.provider_payment_amount,b.N1_payer_name 
						from electronicfiles_tbl a join era_835_details b on a.id=b.electronicFilesTblId where $era_whr";
				    $run=imw_query($qry);
                    while($row=imw_fetch_array($run)){
						if($_REQUEST['era_type'] == 'check'){
                        	$era_file_arr[]=$row;
						}else{
							if(count($era_file_arr[$row['id']])==0){
								$era_file_arr[$row['id']]=$row;
							}
						}
                        $Era835_Id = $row['835_Era_Id'];
                        $TRNPaymentChkNumber[$row['id']][$Era835_Id] = $row['TRN_payment_type_number'];
                        $providerPaymentAmt[$row['id']][$Era835_Id] = $row['provider_payment_amount'];
                        $era_total_amount_arr[$row['id']][] = $row['provider_payment_amount'];
                    }
                    foreach($era_file_arr as $era_key=>$era_val){
                        $era_file_data=$era_file_arr[$era_key];
						$id=$era_file_data['id'];
                        $fileName = $era_file_data['file_name'];
                        $file_temp_name = $era_file_data['file_temp_name'];
                        $status = $era_file_data['post_status'];	
                        $show_status = str_replace("Partially Posted","Partial",$status);		
                        $show_status = str_replace("Partialy Posted","Partial",$show_status);
                        $EFTChkDate=get_date_format($era_file_data['chk_issue_EFT_Effective_date'],'','','2');
                        $processedDate=get_date_format($era_file_data['processed_date'],'','','2');
						$chk_835_Era_Id='';
						if($_REQUEST['era_type'] == 'check'){
							$file_temp_name=$era_file_data['TRN_payment_type_number'];
							$era_total_amount=numberFormat($era_file_data['provider_payment_amount'],2);
							$chk_835_Era_Id=$era_file_data['835_Era_Id'];
						}else if($_REQUEST['era_type'] == 'insurance'){
							$file_temp_name=$era_file_data['N1_payer_name'];
						}else{
							$era_total_amount=numberFormat(array_sum($era_total_amount_arr[$id]),2);
						}
						
						$str_readStatus = isset($_SESSION['era_fread_status']) ? $_SESSION['era_fread_status'][$_SESSION['authId']] : false;
						if($str_readStatus && $str_readStatus != ''){
							$arr_readStatus = explode(',',$str_readStatus);
							if(!in_array($id,$arr_readStatus)){
								$redUnreadClass = '';
							}else{
								$redUnreadClass = 'style="background-color:#FFFFCC;"';
							}
						}else{
							$redUnreadClass = '';
						}
						if($id==$_REQUEST['send_era_id']){
							$redUnreadClass = 'style="background:#99Ff33;"';
						}
                    ?>
                        <tr <?php echo $redUnreadClass;?> id="era_tr_<?php echo $id; ?>">
                            <td>
                                <div class="checkbox">
                                    <input type="checkbox" value="<?php echo $id; ?>" id="era_files_id_<?php echo $id; ?>" name="era_files_id[]" class="chk_box_css">
                                    <label for="era_files_id_<?php echo $id; ?>"></label>
                                </div>
                            </td>    
                            <td>
                                <a class="text_purple" title="<?php echo $file_temp_name;?>" href="javascript:show_era_detail('<?php echo $id; ?>','<?php echo $chk_835_Era_Id; ?>');">
                                    <?php
                                        if(strlen($file_temp_name)>11){
                                            echo substr($file_temp_name,0,9).'..';
                                        }else{
                                            echo $file_temp_name;
                                        }
                                     ?>
                                </a>
                                
                            </td>
                            <td class="text-nowrap pointer" onclick="show_check_detail('<?php echo $id; ?>');">
                                <?php echo $EFTChkDate; ?>
                                <span class="pull-right glyphicon glyphicon-chevron-down"></span>
                            </td>
                            <td class="text-right text-nowrap">
                                 <?php echo $era_total_amount; ?>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    if($era_status != 'Not Posted' && $era_status != ''){
                                        if($processedDate == '00-00-00'){echo ' - ';}else{echo $processedDate;}
                                    }else{
                                        echo $show_status;
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php if($_REQUEST['era_type'] != 'check'){
							$era_chk_dis="style='display:none;'";
							if($_REQUEST['send_era_id']==$id){
								$era_chk_dis="";
							}
						?>
                            <tr id="chk_detail_id_<?php echo $id; ?>" class="chk_detail_css" <?php echo $era_chk_dis; ?>>
                                <td colspan="4">
                                <?php
                                    foreach($TRNPaymentChkNumber[$id] as $Era835Id => $CHKNo){
                                 ?>
                                        <div style="padding:5px;">
                                            <a href="javascript:show_era_detail('<?php echo $id; ?>','<?php echo $Era835Id; ?>')">
                                                <span><b>Check</b> - <?php echo $CHKNo; ?></span>
                                                <span class="pull-right"><?php echo numberFormat($providerPaymentAmt[$id][$Era835Id],2); ?></span>
                                            </a>
                                        </div>
                                  <?php
                                    }
                                  ?>
                                </td>
                                <td></td>
                            </tr>
                        <?php } ?>
                    <?php	
                    }
                    ?>
                </table>
            </div>
            <div class="text-center pt10" id="module_buttons">
                <input type="button" class="btn btn-danger" align="bottom" name="archive_btn" id="archive_btn" onclick="era_files('archive');" value="Archive">
                <input type="button" class="btn btn-danger" align="bottom" name="delete_btn" id="delete_btn" onclick="era_files('delete');" value="Delete">	
            </div>	
        </div>
        <div class="col-sm-9">
        	<?php if($era_manual_payments==""){?>
                <div class="row createbtch form-inline">
                    <div class="pt10 col-sm-12"></div>
                    <div class="col-sm-2">
                        <div class="checkbox">
                            <input type="checkbox" id="matchUnits" name="matchUnits" checked/>
                            <label for="matchUnits"><strong>Match Units</strong></label>
                        </div>
                    </div>
                     <!--<div class="col-sm-2">
                        <div class="checkbox">
                            <input type="checkbox" id="use_era_allowed" name="use_era_allowed"/>
                            <label for="use_era_allowed"><strong>Use ERA Allowed</strong></label>
                        </div>
                    </div>-->
                    <div class="col-sm-2">
                        <div class="checkbox">
                            <input type="checkbox" id="hcfa_process" name="hcfa_process" value="hcfa" onClick="chk_paper_elect(this.value);"/>
                            <label for="hcfa_process"><strong>Paper</strong></label>
                        </div>
                    </div>
                    <div class="col-sm-2">   
                        <div class="checkbox">
                            <input type="checkbox" id="elect_process" name="elect_process" value="electronic" onClick="chk_paper_elect(this.value);" checked/>
                            <label for="elect_process"><strong>Electronic</strong></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pt10 col-sm-12"></div>
                     <div class="clearfix"></div>
                    <div class="col-sm-3">
                        <label>W/O Code : </label>
                        <select name="write_off_code"  id="write_off_code" class="selectpicker">
                            <option value="">Write off Code</option>
                            <?php
                            $sel_rec=imw_query("select w_id,w_code,w_default from write_off_code");
                            while($sel_write=imw_fetch_array($sel_rec)){
                            ?>
                                <option value="<?php echo $sel_write['w_id'];?>" <?php if($sel_write['w_default']=='yes'){ echo "selected";} ?>><?php echo $sel_write['w_code'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Method : </label>
                        <select name="payment_method" id="payment_method" class="selectpicker">
                        	<?php
                            $sel_rec=imw_query("select pm_id,pm_name from payment_methods where del_status='0' order by default_method desc, pm_name");
                            while($sel_pm=imw_fetch_array($sel_rec)){
                            ?>
                                <option value="<?php echo $sel_pm['pm_name'];?>" <?php if($sel_pm['pm_name']=='EFT'){ echo "selected";} ?>><?php echo $sel_pm['pm_name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>DOP : </label>
                        <div class="input-group">
                            <input type="text" value="" class="form-control date-pick" name="date1" id="date1" onBlur="checkdate(this);">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label>Pay Location : </label>
                        <select name="pay_location" id="pay_location" class="selectpicker">
                            <option value="">Pay Location</option>
                            <?php
                                $selQry = imw_query("select * from facility order by name ASC");
								while($selRow = imw_fetch_array($selQry)){
                                    $id = $selRow['id'];
                                    $sel="";
                                   if($_SESSION['login_facility']==$id){
                                        $sel="selected";
                                    }
                                    print '<option '.$sel.' value="'.$id.'">'.$selRow['name'].'</option>';
                                }
                            ?>
                        </select>
                  </div>
                  <div class="pt10 col-sm-12"></div>
               </div>
           <?php } ?>    
           <?php
			if($_REQUEST['send_era_id']>0){
				$send_era_id=$_REQUEST['send_era_id'];
				/*-----READ STATUS SESSION MANAGE------*/
				$str_readStatus = isset($_SESSION['era_fread_status']) ? $_SESSION['era_fread_status'][$_SESSION['authId']] : false;
				$arr_readStatus = array();
				if($str_readStatus && $str_readStatus != ''){
					$arr_readStatus = explode(',',$str_readStatus);
					if(!in_array($send_era_id,$arr_readStatus)){
						$arr_readStatus[] = $send_era_id;
					}
					$str_readStatus = implode(',',$arr_readStatus);
					$_SESSION['era_fread_status'][$_SESSION['authId']] = $str_readStatus;
				}else{
					$_SESSION['era_fread_status'][$_SESSION['authId']] = $send_era_id;
				}
				/*-----READ STATUS SESSION MANAGE------*/

				/*$qry = imw_query("select billing_amount FROM copay_policies");
				$row = imw_fetch_array($qry);
				$billing_amount=$row['billing_amount'];
				
				//------------------------ Insurance Company ------------------------//
				$qry = "select id,FeeTable from insurance_companies order by name ASC";
				$res = imw_query($qry);
				while($row = imw_fetch_array($res)){
					$fee_table_column_arr[$row['id']]=$row['FeeTable'];
				}
				
				//------------------------ CPT Code and Fee Detail------------------------//
				$qry = imw_query("SELECT cpt_fee_table.cpt_fee,cpt_fee_table.fee_table_column_id,cpt_fee_tbl.cpt_prac_code,cpt_fee_tbl.cpt4_code,cpt_fee_tbl.cpt_desc
								 FROM cpt_fee_tbl join cpt_fee_table on cpt_fee_tbl.cpt_fee_id=cpt_fee_table.cpt_fee_id");
				while($row = imw_fetch_array($qry)){
					$cpt_fee_tbl_table_data[$row['cpt_prac_code']][]=$row;
					$cpt_fee_tbl_table_data[$row['cpt4_code']][]=$row;
					$cpt_fee_tbl_table_data[$row['cpt_desc']][]=$row;
				}*/
				
				$qry = imw_query("select cpt_fee_id,cpt_cat_id,cpt4_code,cpt_prac_code from cpt_fee_tbl where delete_status='0'");
				while($fet_cpt = imw_fetch_assoc($qry)){
					$cpt_fee_tbl_arr[$fet_cpt['cpt_fee_id']] = $fet_cpt;
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
				$row=imw_fetch_array($qry);
				$era_file_detail[$row['id']]=$row;
				$era_file_ids[$row['id']]=$row['id'];
				$era_file_name_exp=explode('.tar',$row['file_name']);
				$era_file_contents=$row['file_contents'];
				
				if($_REQUEST['send_era_chk_id']>0){
					$whr_chk=" and 835_Era_Id='".$_REQUEST['send_era_chk_id']."'";
					$manual_whr_chk=" and era_835_details.835_Era_Id='".$_REQUEST['send_era_chk_id']."'";
				}
				
				$era_file_ids_imp=implode(',',$era_file_ids);
				
				if($era_manual_payments=="yes"){
					$qry=imw_query("select group_concat(DISTINCT(era_835_details.835_Era_Id)) as manual_835_Era_Id from era_835_proc_details join era_835_details on era_835_proc_details.835_Era_Id=era_835_details.835_Era_Id 
					where era_835_details.electronicFilesTblId in($era_file_ids_imp) and era_835_proc_details.postedStatus='Not Posted' $manual_whr_chk");
					$row=imw_fetch_array($qry);
					$manual_835_Era_Id_whr=" and 835_Era_Id in(".$row['manual_835_Era_Id'].")";
				}
				
				$qry=imw_query("select * from era_835_details where electronicFilesTblId in($era_file_ids_imp) $whr_chk $manual_835_Era_Id_whr order by chk_issue_EFT_Effective_date desc");
				while($row=imw_fetch_array($qry)){
					$era_chk_detail[$row['electronicFilesTblId']][$row['835_Era_Id']]=$row;
					$era_chk_ids[$row['835_Era_Id']]=$row['835_Era_Id'];
					$era_tot_prob_pay=$era_tot_prob_pay+$row['provider_payment_amount'];
					
					$N1_payer_name_arr[$row['N1_payer_name']] = $row['N1_payer_name'];
					$N3_payer_address_arr[$row['N1_payer_name']] = $row['N3_payer_address'];
					$N4_payer_city_arr[$row['N1_payer_name']] = $row['N4_payer_city'];
					$N4_payer_state_arr[$row['N1_payer_name']] = $row['N4_payer_state'];
					$N4_payer_zip_arr[$row['N1_payer_name']] = $row['N4_payer_zip'];
			
					$N1_payee_name_arr[$row['N1_payer_name']] = $row['N1_payee_name'];
					$N1_payee_id_arr[$row['N1_payer_name']] = $row['N1_payee_id'];
					$X12_standard = $row['X12_standard'];
					$ver = explode(",", $X12_standard);
					$ver_arr[$row['N1_payer_name']]=$ver[1];
					$chk_issue_EFT_Effective_date_exp=explode('-',$row['chk_issue_EFT_Effective_date']);
					$chk_issue_EFT_Effective_date_arr[$row['chk_issue_EFT_Effective_date']] = $chk_issue_EFT_Effective_date_exp[1].'-'.$chk_issue_EFT_Effective_date_exp[2].'-'.$chk_issue_EFT_Effective_date_exp[0];
				}
				$chk_issue_EFT_Effective_date_imp=implode(', ',$chk_issue_EFT_Effective_date_arr);
				
				$era_tot_pat=0;
				$era_chk_ids_imp=implode(',',$era_chk_ids);
				$qry=imw_query("select * from era_835_patient_details join era_835_nm1_details 
								on era_835_patient_details.ERA_patient_details_id=era_835_nm1_details.ERA_patient_details_id
					 		 	where 835_Era_Id in($era_chk_ids_imp) and era_835_nm1_details.NM1_type='QC' 
								order by era_835_nm1_details.NM1_last_name ASC");
				while($row=imw_fetch_array($qry)){
					$era_pat_detail[$row['835_Era_Id']][$row['ERA_patient_details_id']]=$row;
					$era_tot_chrg_arr[$row['ERA_patient_details_id']]=$row['CLP_total_claim_charge'];
				}
				
				if($era_manual_payments=="yes"){
					$whr_proc_chk=" and postedStatus='Not Posted'";
					$div_hg="100";
				}
				$qry=imw_query("select * from era_835_proc_details where 835_Era_Id in($era_chk_ids_imp) $whr_proc_chk $era_manual_proc_whr order by 835_Era_proc_Id");
				while($row=imw_fetch_array($qry)){
					$era_proc_detail[$row['835_Era_Id']][$row['ERA_patient_details_id']][$row['835_Era_proc_Id']]=$row;
					$era_ret_cas_arr=era_reason_amt($row['CAS_type'],$row['CAS_reason_code'],$row['CAS_amt'],$row['835_Era_proc_Id'],$cas_action_arr);
					$era_wrt_tot_arr[]=$era_ret_cas_arr['era_writeoff'];
					$era_deduct_tot_arr[]=$era_ret_cas_arr['era_deduct'];
				}
				$qry = imw_query("select * from era835clpcas where era835Id in($era_chk_ids_imp)");
				while($row=imw_fetch_array($qry)){
					$casReasonType[$row['ERAPatientdetailsId']][] = $row['casReasonType'].' '.$row['casReasonCode'];
					$casReasonAmt[$row['ERAPatientdetailsId']][] = numberFormat(trim($row['casReasonAmt']),2);
				}
				
				$era_name=$era_file_detail[$send_era_id]['file_name'];
				$mod_dat_final=get_date_format($era_file_detail[$send_era_id]['modified_date']);
		   ?>
           
           <div class="row" style="margin:0px;">
           		<table class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                    <tr class='grythead'>
                        <th>ERA File Name</th>
                        <th>Uploaded Date</th>
                        <th># of patients</th>
                        <th>Claim Charges</th>
                        <th>Paid</th>
                        <th>Write-Off</th>
                        <th>Deductible</th>
                    </tr>
                    <tr>
                        <th class="text-center"><?php if($era_name) echo $era_name; else echo '-'; ?></th>
                        <th class="text-center"><?php if($mod_dat_final!='00-00-0000') echo $mod_dat_final; else echo '-'; ?></th>
                        <th class="text-center"><?php echo count($era_tot_chrg_arr); ?></th>
                        <th class="text-center"><?php echo numberFormat(trim(array_sum($era_tot_chrg_arr)),2); ?></th>
                        <th class="text-center"><?php echo numberFormat(trim($era_tot_prob_pay),2); ?></th>
                        <th class="text-center"><?php echo numberFormat(trim(array_sum($era_wrt_tot_arr)),2);?></th>
                        <th class="text-center"><?php echo numberFormat(trim(array_sum($era_deduct_tot_arr)),2); ?></th>
                    </tr>
		  		</table>
           </div>
           <div style="overflow-y:scroll; height:<?php print $_SESSION['wn_height']-(635-$div_hg);?>px;">
           	<?php //if($era_manual_payments==""){?>
           		<table class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                    <tr class='grythead'>
                        <th>Insurance Detail</th>
                        <th>Provider Name</th>
                        <th>VER</th>
                        <th>NPI #</th>
                        <th>Check Date</th>
                    </tr>
                    <?php foreach($N1_payer_name_arr as $n1_key=>$n1_val){?>
                        <tr>
                            <td style="vertical-align:text-top !important; width:350px;">
                                <?php if($N1_payer_name_arr[$n1_key]){
                                         echo '<b>'.$N1_payer_name_arr[$n1_key].
                                            '</b><br>'.$N3_payer_address_arr[$n1_key].
                                            '<br>'.$N4_payer_city_arr[$n1_key].', '.$N4_payer_state_arr[$n1_key].' '.$N4_payer_zip_arr[$n1_key];
                                      }else{ 
                                        echo '-';
                                      }
                                ?>
                            </td>
                            <td style="vertical-align:text-top !important; width:250px;"><?php if($N1_payee_name_arr[$n1_key]) echo $N1_payee_name_arr[$n1_key]; else echo '-'; ?></td>
                            <td style="vertical-align:text-top !important;"><?php if($ver_arr[$n1_key]) echo $ver_arr[$n1_key]; else echo '-'; ?></td>
                            <td style="vertical-align:text-top !important;"><?php if($N1_payee_id_arr[$n1_key]) echo $N1_payee_id_arr[$n1_key]; else echo '-'; ?></td>
                            <td style="vertical-align:text-top !important;"><?php if($chk_issue_EFT_Effective_date_imp) echo $chk_issue_EFT_Effective_date_imp; else echo '-'; ?></td>
                        </tr>
                    <?php } ?>
                </table>
             <?php //} ?>   
                <table class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                    <tr class="grythead">
                        <th rowspan="2">
                            <div class="checkbox">
                                <input type="checkbox" id="main_enc_chk" name="main_enc_chk" onClick="sel_enc_chk_fun();" checked/>
                                <label for="main_enc_chk"></label>
                            </div>
                        </th>
                        <th rowspan="2" class="text-nowrap">Acc. Id</th>
                        <th rowspan="2">E. Id</th>
                        <th rowspan="2">Name</th>
                        <th rowspan="2">DOS</th>
                        <th rowspan="2">CPT</th>
                        <!--<th rowspan="2" class="contract_fee">Contract<br/>Fee</th>-->
                        <th rowspan="2">Charges</th>
                        <th rowspan="2">Allowed</th>
                        <th rowspan="2">Deductible</th>
                        <th rowspan="2">Paid</th>
                        <th rowspan="2">Reason<br/>Code</th>
                        <th rowspan="2">Amount</th>
                        <th rowspan="2">Rem Code</th>
                        <th rowspan="2">Processed By</th>
                        <th colspan="3">MOD</th>
                    </tr>  
                    <tr class="grythead">
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                    </tr>
                    <?php 
					foreach($era_chk_detail[$send_era_id] as $chk_key=>$chk_val){
						$era_chk_dt=$era_chk_detail[$send_era_id][$chk_key];
						$TRN_payment_type_number=$era_chk_dt['TRN_payment_type_number'];
						$chk_issue_EFT_Effective_date=get_date_format($era_chk_dt['chk_issue_EFT_Effective_date']);
						
						$get_other_era_chk_row_data="";
						$get_other_era_chk_qry = imw_query("select 835_Era_Id from era_835_details WHERE TRN_payment_type_number = '$TRN_payment_type_number'");
						$get_other_era_chk_num=imw_num_rows($get_other_era_chk_qry);
						while($get_other_era_chk_row=imw_fetch_array($get_other_era_chk_qry)){
							$get_other_era_chk_row_data[$get_other_era_chk_row['835_Era_Id']]=$get_other_era_chk_row;	
						}
					?>
                        <tr>
                            <th colspan="17">
                                Check # <?php echo $TRN_payment_type_number; ?>
                                <span style="padding-left:60px;">Check Date : <?php echo $chk_issue_EFT_Effective_date; ?></span>
                            </th>
                        </tr>
                        <?php
						foreach($era_pat_detail[$chk_key] as $pat_key=>$pat_val){
							$enc_span_id="enc_id_".$pat_key;
							$era_pat_dt=$era_pat_detail[$chk_key][$pat_key];
							
							$casReasonTypeNow = implode(', ', $casReasonType[$pat_key]);
							$casReasonAmtNow = implode(', ', $casReasonAmt[$pat_key]);
							
							if(count($era_proc_detail[$chk_key][$pat_key])==0 && $era_manual_payments==""){
								imw_query("insert into era_835_proc_details set ERA_patient_details_id='$pat_key',835_Era_Id='$chk_key'");
							}
							
							$CLP_claim_status = $era_pat_dt['CLP_claim_status'];
							if($CLP_claim_status == 1 || $CLP_claim_status == 19){
								$processed_by = $insCoType = 'Primary';
							}else if($CLP_claim_status == 2 || $CLP_claim_status == 20){
								$processed_by = $insCoType = 'Secondary';
							}else if($CLP_claim_status == 3 || $CLP_claim_status == 21){
								$processed_by = $insCoType = 'Tertiary';
							}else if($CLP_claim_status == 4){
								$processed_by = 'Denied';
								$insCoType = '';
							}else{
								$processed_by = '-';
								$insCoType = '';
							}
							
							$chk_proc_dis="disabled";
							$chk_other_era_id=$chk_key;
							if($get_other_era_chk_num>1){
								foreach($get_other_era_chk_row_data as $chk_dup_key=>$chk_dup_val){	
									$get_other_era_proc_qry = imw_query("SELECT 835_Era_Id FROM era_835_proc_details WHERE postedStatus = 'Posted' and 835_Era_Id='".$get_other_era_chk_row_data[$chk_dup_key]['835_Era_Id']."' limit 0,1");
									if(imw_num_rows($get_other_era_proc_qry)>0){
										$chk_other_era_id=$get_other_era_chk_row_data[$chk_dup_key]['835_Era_Id'];
									}
								}
								$getProcDetailchkstr = imw_query("SELECT * FROM era_835_proc_details WHERE ERA_patient_details_id = '$pat_key' and postedStatus='Not Posted' and SVC_proc_code!=''");
								if(imw_num_rows($getProcDetailchkstr)>0 && $chk_other_era_id==$chk_key){
									$chk_proc_dis="";
								}
							}else{
								foreach($era_proc_detail[$chk_key][$pat_key] as $chk_proc_key=>$chk_proc_val){
									if($era_proc_detail[$chk_key][$pat_key][$chk_proc_key]['SVC_proc_code']!=""){
										$chk_proc_dis="";
									}
								}
							}
							if($TRN_payment_type_number=="896459201" || $TRN_payment_type_number=="1090002200"){
								$chk_proc_dis="";
							}
							$show_pat_det=true;
							foreach($era_proc_detail[$chk_key][$pat_key] as $proc_key=>$proc_val){
								$era_proc_dt=$era_proc_detail[$chk_key][$pat_key][$proc_key];
								$mod_arr = explode(", ",$era_proc_dt['SVC_mod_code']);
								$REF_prov_identifier = $era_proc_dt['REF_prov_identifier'];
								
								if(count($era_proc_detail[$chk_key][$pat_key])==1){
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
								
								if($era_pat_dt['CLP_claim_submitter_id']!="" && !$encounter_id){
									
									// GET PROC ID FROM CPT4_CODE
									$cpt_fee_id_arr=array();
									foreach($cpt_fee_tbl_arr as $cpt_key => $cpt_val){
										if(strtolower($cpt_fee_tbl_arr[$cpt_key]['cpt4_code'])==strtolower($era_proc_dt['SVC_proc_code'])){
											$cpt_fee_id_arr[$cpt_fee_tbl_arr[$cpt_key]['cpt_fee_id']]=$cpt_fee_tbl_arr[$cpt_key]['cpt_fee_id'];
										}
									}							
									$cpt_fee_id=implode(',',$cpt_fee_id_arr);			
									// GET PROC ID FROM CPT4_CODE
									
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
												where pcld.del_status='0' and pcl.date_of_service='$DTM_date' AND pcld.procCode in($cpt_fee_id)
												and pcld.patient_id ='".$era_pat_dt['CLP_claim_submitter_id']."'";
												
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
								$era_ret_cas_arr=era_reason_amt($era_proc_dt['CAS_type'],$era_proc_dt['CAS_reason_code'],$era_proc_dt['CAS_amt'],$era_proc_dt['835_Era_proc_Id'],$cas_action_arr);
								$writeoff_amount=$era_ret_cas_arr['era_writeoff'];
								$deduct_amount=$era_ret_cas_arr['era_deduct'];
								$cas_reason=$era_ret_cas_arr['era_reason'];
								$cas_amount=$era_ret_cas_arr['era_amount'];
								$bg_color="";
								if($era_proc_dt['SVC_proc_charge']<0 || $era_proc_dt['AMT_amount']<0 || $deduct_amount<0 || $era_proc_dt['SVC_provider_pay_amt']<0 || $era_proc_dt['CAS_amt']==""){
									//yellow color
									$bg_color="style='background-color:#FDB221;'";
								}
								if($processed_by=="Secondary" && $era_proc_dt['postedStatus']!="Posted" && (stristr($cas_reason,'CO') || stristr($cas_reason,'PI') || stristr($cas_reason,'OA') ||  stristr($cas_reason,'CR'))){
									if(count($era_file_name_exp)<2){
										$show_sec_co_alrt=1;
										$CASReason_exp_chk=explode(',',$cas_reason);
										$CASAmount_exp_chk=explode(',',$cas_amount);
										for($hk=0;$hk<=count($CASReason_exp_chk);$hk++){
											if(trim($CASReason_exp_chk[$hk])!="" && $show_sec_co_alrt>0){
												$chk_CAS_type_exp=explode(' ',trim($CASReason_exp_chk[$hk]));
												$chk_CAS_type=trim($chk_CAS_type_exp[0]);
												$chk_CAS_code=trim($chk_CAS_type_exp[1]);
												$chk_CAS_amt=trim(str_replace('$','',str_replace(',','',trim($CASAmount_exp_chk[$hk]))));
												$chk_wrt_qry=imw_query("select * from paymentswriteoff where charge_list_detail_id='$listChargeDetailId_chk' and CAS_type like '%$chk_CAS_type%' and CAS_code like '%$chk_CAS_code%' and (era_amt='$chk_CAS_amt' or write_off_amount='$chk_CAS_amt')");
												if(imw_num_rows($chk_wrt_qry)>0){
													$show_sec_co_alrt=0;
												}
											}
										}
										if($show_sec_co_alrt>0){
											//blue color
											$bg_color="style='background-color:#19a0d0;'";
										}
									}
								}
								
								if(imw_num_rows($chl_sql)==0){
									$chk_proc_dis="disabled";
								}
								
								if($DTM_date=="0000-00-00" && $encounter_id>0){
									$dos_up_qry=imw_query("update era_835_proc_details set DTM_type='472',DTM_date='$chl_dos' where 835_Era_proc_Id = '$proc_key'");
								}
								
								//Get CPT Contract Price
								$cpt_contract_price="";
								if($era_proc_dt['SVC_proc_code']!="" && count($era_file_name_exp)<2){
									if($billing_amount=='Default'){
										$chk_fee_table_column=$fee_table_column_arr[$ins_company_id];
										foreach($cpt_fee_tbl_table_data[$era_proc_dt['SVC_proc_code']] as $cpt_key=>$cpt_val){
											if($cpt_fee_tbl_table_data[$era_proc_dt['SVC_proc_code']][$cpt_key]['fee_table_column_id']==$chk_fee_table_column){
												$cpt_contract_price=$cpt_fee_tbl_table_data[$era_proc_dt['SVC_proc_code']][$cpt_key]['cpt_fee'];
											}
										}
									}
								}
								
								//Get Reason Code Description
								$final_cas_disc_arr=array();
								$cas_code_exp=array();
								$cas_code_exp=explode(', ',$cas_reason);
								for($k=0;$k<=count($cas_code_exp);$k++){
									if(trim($cas_code_exp[$k])!=""){
										$cas_code_exp2=explode(' ',trim($cas_code_exp[$k]));
										$cas_siz=count($cas_code_exp2)-1;
										if($all_cas_arr[trim($cas_code_exp[$k])]!=""){
											$final_cas_disc_arr[trim($cas_code_exp[$k])]=trim($cas_code_exp[$k]).' - '.$all_cas_arr[trim($cas_code_exp[$k])];
										}else{
											$final_cas_disc_arr[trim($cas_code_exp[$k])]=trim($cas_code_exp[$k]).' - '.$all_cas_arr[$cas_code_exp2[$cas_siz]];
										}
									}
								}
								$cas_reason_detail=implode('<br>',$final_cas_disc_arr);
								if(strtolower($era_proc_dt['rem_code'])=='n706'){
									$chk_proc_dis="disabled";
								}
								
								if(in_array(strtolower($billing_global_server_name), array('northshore'))){
									$qry = imw_query("select charge_list_id from patient_charge_list where del_status='0' and encounter_id = '$encounter_id' and date_of_service<'2019-04-29' and gro_id in('105','201')");
									if(imw_num_rows($qry)>0){
										$chk_proc_dis="disabled";
									}
								}
								
								if($era_manual_payments=="yes"){
									$chk_proc_dis="";
								}
								if($show_pat_det==true){
									$show_pat_det=false;
							?>
                                     <tr>
                                        <th>
                                            <div class="checkbox">
                                                <input type="checkbox" id="sel_enc_chk_id_<?php echo $era_pat_dt['ERA_patient_details_id']; ?>" name="sel_enc_chk_id[]" class="sel_enc_chk_class" value="<?php echo $era_pat_dt['ERA_patient_details_id']; ?>" <?php echo $chk_proc_dis; ?>/>
                                                <label for="sel_enc_chk_id_<?php echo $era_pat_dt['ERA_patient_details_id']; ?>"></label>
                                            </div>
                                        </th>
                                        <th>
                                             <?php echo $era_pat_dt['CLP_claim_submitter_id']; ?>
                                        </th>
                                        <th <?php echo $not_match_enc_col; ?>>
                                             <?php echo $encounter_id; ?>
                                        </th>
                                        <th>
                                            <a href="javascript:showEP('<?php echo $era_pat_dt['CLP_claim_submitter_id']; ?>','<?php echo $encounter_id; ?>','<?php echo $_SESSION['wn_height']; ?>');" class="text_purple">
                                                <?php echo $era_pat_dt['NM1_last_name'].', '.$era_pat_dt['NM1_first_name']; ?>
                                            </a>
                                        </th>
                                        <th>-</th>
                                        <th>-</th>
                                        <!--<th class="contract_fee">-</th>-->
                                        <th class="text-right"><?php echo numberFormat(trim($era_pat_dt['CLP_total_claim_charge']),2); ?></th>
                                        <th>-</th>
                                        <th>-</th>
                                        <th class="text-right"><?php echo numberFormat(trim($era_pat_dt['CLP_claim_payment_amount']),2); ?></th>
                                        <th><?php if($casReasonTypeNow) echo $casReasonTypeNow; else echo '-'; ?></th>
                                        <th><?php if($casReasonAmtNow) echo $casReasonAmtNow; else echo '-'; ?></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr> 
							<?php } ?>
                            <?php
								if($old_p_np_key==$era_proc_dt['ERA_patient_details_id']){
									if($era_p_np_status=="Posted"){
										$era_p_np_status=$era_proc_dt['postedStatus'];
									}
								}else{
									$era_p_np_status=$era_proc_dt['postedStatus'];
								}
								$old_p_np_key=$era_proc_dt['ERA_patient_details_id'];
								$era_p_np_arr['status'][$era_proc_dt['ERA_patient_details_id']]=$era_p_np_status;
								$era_p_np_arr['charges'][$era_proc_dt['ERA_patient_details_id']]=$era_p_np_arr['charges'][$era_proc_dt['ERA_patient_details_id']]+$era_proc_dt['SVC_proc_charge'];
								$era_p_np_arr['paid'][$era_proc_dt['ERA_patient_details_id']]=$era_p_np_arr['paid'][$era_proc_dt['ERA_patient_details_id']]+$era_proc_dt['SVC_provider_pay_amt'];
								$era_p_np_arr['deduct'][$era_proc_dt['ERA_patient_details_id']]=$era_p_np_arr['deduct'][$era_proc_dt['ERA_patient_details_id']]+$deduct_amount;
								$era_p_np_arr['writeoff'][$era_proc_dt['ERA_patient_details_id']]=$era_p_np_arr['writeoff'][$era_proc_dt['ERA_patient_details_id']]+$writeoff_amount;
							
								$Total_SVC_proc_charge[]=$era_proc_dt['SVC_proc_charge'];
								$Total_AMT_amount[]=$era_proc_dt['AMT_amount'];
								$Total_deductAmount[]=$deduct_amount;
								$providerPaymentAmount[]=$era_proc_dt['SVC_provider_pay_amt'];
							?>
								<tr <?php echo $bg_color; ?>>
									<td colspan="2"><?php echo $era_proc_dt['postedStatus']; ?></td>
									<td></td>
									<td><?php echo $era_pat_dt['CLP_payer_claim_control_number'];?></td>
									<td class="text-nowrap">
										<a href="javascript:<?php if($acc_view_only == 1){ ?> view_only_acc_call(0);<?php }else{ ?>era_detail_popup('<?php echo $send_era_id; ?>','<?php echo $chk_key; ?>','<?php echo $pat_key; ?>','<?php echo $era_manual_payments; ?>');<?php } ?>" class="text_purple">
											<?php echo get_date_format($era_proc_dt['DTM_date']); ?>
										</a>
									</td>
									<td><?php echo $era_proc_dt['SVC_proc_code']; ?></td>
									<!-- <td class="contract_fee"><?php //echo numberFormat($cpt_contract_price,2); ?></td>-->
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
									<td style="cursor:pointer; <?php echo $oa_color; ?>" <?php echo show_tooltip($cas_reason_detail); ?>>
										<?php if($cas_reason) echo $cas_reason; else echo "-"; ?>
									</td>
									<td><?php if($cas_amount!=""){ echo $cas_amount;}else{ echo "-";} ?></td>
									<td><?php echo $era_proc_dt['rem_code']; ?></td>
									<td><?php echo $processed_by; ?></td>
									<td><?php if($mod_arr[0]) echo $mod_arr[0]; else echo '-'; ?></td>
									<td><?php if($mod_arr[1]) echo $mod_arr[1]; else echo '-'; ?></td>
									<td><?php if($mod_arr[2]) echo $mod_arr[2]; else echo '-'; ?></td>
								</tr>
						<?php 
								} 
							}
					 	} 
					?>
                    <tr>
                        <th colspan="6" class="text-right">Total : </th>
                        <th class="text-right"><?php echo numberFormat(trim(array_sum($Total_SVC_proc_charge)),2,'yes');?></th>
                        <th class="text-right"><?php echo numberFormat(trim(array_sum($Total_AMT_amount)),2,'yes'); ?></th>
                        <th class="text-right"><?php echo numberFormat(trim(array_sum($Total_deductAmount)),2,'yes'); ?></th>
                        <th class="text-right"><?php echo numberFormat(trim(array_sum($providerPaymentAmount)),2,'yes'); ?></th>
                        <th colspan="7"></th>
                    </tr>
                </table>
                <table class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                    <tr class="grythead">
                        <th>PLB</th>
                        <th>Provider Name</th>
                        <th>Patient Name</th>
                        <th>Encounter Id</th>
                        <th>Reason Code</th>
                        <th>Amount</th>
                    </tr> 
                     <?php
						/*72 = Authorized Return, used for Refund (Note: This value is not returned for Medicare Advantage or Medicare Supplemental products.)
						CS = Adjustment
						FB = Forwarding balance
						L6=Interest Owed, used for Total Interest Paid (Note: This value is not returned for Medicare Advantage or Medicare Supplemental products.)
						WO=Overpayment Recovery, used for Voucher Deduct*/
                        $file_data=$era_file_contents;
                        $plb_exp_data=explode('~',$file_data);
                        $plb_srn=0;
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
                                        $final_plb_code_arr['reason_amount'][]=numberFormat(trim($plb_exp_final_data[$k+1]),2,'yes');
                                        $k++;
                                    }
                                }
                                $ein_num=$plb_exp_final_data[1];
                                $usr_qry=imw_query("select fname,mname,lname from users where user_npi='$ein_num'");
                                $usr_row=imw_fetch_array($usr_qry);
                                $physician_name = $usr_row['lname'].', '.$usr_row['fname'].' '.$usr_row['mname'];
								if(imw_num_rows($usr_qry)==0){
									$ref_usr_qry=imw_query("select FirstName,MiddleName,LastName from refferphysician where NPI='$ein_num'");
									$ref_usr_row=imw_fetch_array($ref_usr_qry);
									$physician_name = $ref_usr_row['LastName'].', '.$ref_usr_row['FirstName'].' '.$ref_usr_row['MiddleName'];
								}
                                $patientName_frm_arr=array();
                                $Encounter_frm_arr=array();
                                foreach($final_plb_code_arr['clp_control_number'] as $val){
                                    $final_val="";
                                    $final_val_arr=explode('/',$val);
                                    if(count($final_val_arr)>1){
                                        $final_val_arr=explode(' ',$final_val_arr[1]);
                                    }else{
                                        $final_val_arr=explode(' ',$val);
                                    }
                                    $final_val=str_replace("DEFER","",$final_val_arr[0]);
									$CLP_patient_id=$final_val_arr[1];
									$plb_DTM_date=$final_val_arr[0];
									$pat_qry=imw_query("select fname,lname from patient_data where id='$CLP_patient_id'");
									$pat_row=imw_fetch_array($pat_qry);
									if(imw_num_rows($pat_qry)>0 && strlen($plb_DTM_date)==8){
										$plb_patient_arr[$CLP_patient_id]=$CLP_patient_id;
										$fname_frm = $pat_row['fname'];
										$lname_frm = $pat_row['lname'];
										$mname_frm = $pat_row['mname'];
										$patientName_frm_arr[$CLP_patient_id] = ucwords(trim($lname_frm.", ".$fname_frm)).' - '. $CLP_patient_id;
										$getPatProcDetailsStr = "SELECT era_835_proc_details.REF_prov_identifier FROM era_835_proc_details join era_835_patient_details 
										on era_835_proc_details.ERA_patient_details_id=era_835_patient_details.ERA_patient_details_id 
										WHERE era_835_patient_details.CLP_claim_submitter_id = '$CLP_patient_id' and REPLACE(era_835_proc_details.DTM_date,'-','')='$plb_DTM_date' limit 0,1";
										$getPatProcDetailsQry = imw_query($getPatProcDetailsStr);				
										while($getPatProcDetailsRow = imw_fetch_array($getPatProcDetailsQry)){
											$REF_prov_identifier_exp=explode('MCR',$getPatProcDetailsRow['REF_prov_identifier']);
											$Encounter_frm_arr[$REF_prov_identifier_exp[0]]=$REF_prov_identifier_exp[0];
										}
									}else{
										$clp_qry=imw_query("select CLP_claim_submitter_id,ERA_patient_details_id from era_835_patient_details where CLP_payer_claim_control_number like '$final_val%'");
										if(imw_num_rows($clp_qry)>0){
											while($clp_row=imw_fetch_array($clp_qry)){
												$CLP_patient_id=$clp_row['CLP_claim_submitter_id'];
												$CLP_ERA_patient_details_id=$clp_row['ERA_patient_details_id'];
												$pat_qry=imw_query("select fname,lname from patient_data where id='$CLP_patient_id'");
												$pat_row=imw_fetch_array($pat_qry);
												if(imw_num_rows($pat_qry)>0){
													$plb_patient_arr[$CLP_patient_id]=$CLP_patient_id;
													$fname_frm = $pat_row['fname'];
													$lname_frm = $pat_row['lname'];
													$mname_frm = $pat_row['mname'];
													$patientName_frm_arr[$CLP_patient_id] = ucwords(trim($lname_frm.", ".$fname_frm)).' - '. $CLP_patient_id;
													$getPatProcDetailsStr = "SELECT REF_prov_identifier FROM era_835_proc_details WHERE `ERA_patient_details_id` = '$CLP_ERA_patient_details_id'";
													$getPatProcDetailsQry = imw_query($getPatProcDetailsStr);				
													while($getPatProcDetailsRow = imw_fetch_array($getPatProcDetailsQry)){
														$REF_prov_identifier_exp=explode('MCR',$getPatProcDetailsRow['REF_prov_identifier']);
														$Encounter_frm_arr[$REF_prov_identifier_exp[0]]=$REF_prov_identifier_exp[0];
													}
												}
											}
										}
									}
                                } 
                    ?>
                            <tr>
                                <td align="center" class="text_10" valign="top"><?php echo $plb_srn;?></td>
                                <td align="center" class="text_10" valign="top"><?php echo $physician_name; ?></td>
                                <td align="center" class="text_10" valign="top"><?php echo implode('<br> ',$patientName_frm_arr); ?></td>
                                <td align="center" class="text_10" valign="top">
                                    <?php 
                                        /*foreach($patientName_frm_arr as $key => $val){
                                            echo implode('<br> ',$Encounter_frm_arr[$key]);
                                        }*/
                                        echo implode('<br> ',$Encounter_frm_arr);
                                     ?>
                                </td>
                                <td align="center" class="text_10" valign="top"><?php echo implode(', ',$final_plb_code_arr['reason_code']); ?></td>
                                <td align="center" class="text_10" valign="top"><?php echo implode(', ',$final_plb_code_arr['reason_amount']); ?></td>
                            </tr> 
                    <?php			
                            }
                        }
                        if($plb_srn==0){
                    ?>
                     <tr>
                        <td class="text-center lead" colspan="6">No PLB Record Found.</td>
                    </tr> 
                    <?php } ?>
                </table>
                 <div>&nbsp;<span style="color:#F00; font-weight:bold;">! - Allowed amount in ERA is different then allowed amount posted in encounter.</span></div>
                 <div>&nbsp;<span style="background-color:#99a0dd;">&nbsp;&nbsp;</span><span style="color:#F00; font-weight:bold;"> - Contract Obligation does not match for this Secondary with Patient Primary Insurance.</span></div>
            	 <input type="hidden" name="plb_patient_txt" value="<?php echo implode(',',$plb_patient_arr); ?>">
            </div>
            <table class="table table-striped table-bordered table-hover">
                <tr class="grythead">
                    <th>Processed</th>
                    <th># of Patients</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Write-Off</th>
                    <th>Deductible</th>
                </tr>
                <?php 
					foreach($era_p_np_arr['status'] as $p_np_key=>$p_np_val){
						$st=$era_p_np_arr['status'][$p_np_key];
						$era_sum['status'][$st][]=$era_p_np_arr['status'][$p_np_key];
						$era_sum['charges'][$st][]=$era_p_np_arr['charges'][$p_np_key];
						$era_sum['paid'][$st][]=$era_p_np_arr['paid'][$p_np_key];
						$era_sum['writeoff'][$st][]=$era_p_np_arr['writeoff'][$p_np_key];
						$era_sum['deduct'][$st][]=$era_p_np_arr['deduct'][$p_np_key];
					}
				?>
                <tr>
                    <th>Applied</th>
                    <td><?php if(count($era_sum['status']['Posted'])>0) echo count($era_sum['status']['Posted']); else echo "-"; ?></td>
                    <td class="text-right"><?php if(count($era_sum['status']['Posted'])>0) echo numberFormat(array_sum($era_sum['charges']['Posted']),2); else echo "-"; ?></td>
                    <td class="text-right"><?php if(count($era_sum['status']['Posted'])>0) echo numberFormat(array_sum($era_sum['paid']['Posted']),2); else echo "-"; ?></td>
                    <td class="text-right"><?php if(count($era_sum['status']['Posted'])>0) echo numberFormat(array_sum($era_sum['writeoff']['Posted']),2); else echo "-"; ?></td>
                    <td class="text-right"><?php if(count($era_sum['status']['Posted'])>0) echo numberFormat(array_sum($era_sum['deduct']['Posted']),2); else echo "-"; ?></td>
                </tr>
                <tr>
                    <th>Not Applied</th>
                    <td><?php if(count($era_sum['status']['Not Posted'])>0) echo count($era_sum['status']['Not Posted']); else echo "-"; ?></td>
                    <td class="text-right"><?php if(count($era_sum['status']['Not Posted'])>0) echo numberFormat(array_sum($era_sum['charges']['Not Posted']),2); else echo "-"; ?></td>
                    <td class="text-right"><?php if(count($era_sum['status']['Not Posted'])>0) echo numberFormat(array_sum($era_sum['paid']['Not Posted']),2); else echo "-"; ?></td>
                    <td class="text-right"><?php if(count($era_sum['status']['Not Posted'])>0) echo numberFormat(array_sum($era_sum['writeoff']['Not Posted']),2); else echo "-"; ?></td>
                    <td class="text-right"><?php if(count($era_sum['status']['Not Posted'])>0) echo numberFormat(array_sum($era_sum['deduct']['Not Posted']),2); else echo "-"; ?></td>
                </tr>
          </table> 
       <?php } ?>   
        </div> 
    </div>
</form>    
</div>
</body>
</html>
<script type="text/javascript">
var send_era_id = "<?php echo $_REQUEST['send_era_id']; ?>";
$(document).ready(function(){
	if(send_era_id!=""){
		$('#era_list_div').animate({scrollTop: $('#era_tr_'+send_era_id).position().top - ($('#era_tr_'+send_era_id).parent().position().top)});
	}
});
function printEraDetailsFn(eFileId,eFileChkId,era_manual_payments){
	window.open("../billing/era_post_payments_print.php?ERA_FILE_id="+eFileId+"&ERA_FILE_CHK_id="+eFileChkId+"&era_manual_payments="+era_manual_payments,'ClaimPrint','width=150,height=100,top=25,left=70,scrollbars=no,resizable=1');
}
function era_files(arg){
	var flag = 0;
	$('#era_action').val('');
	$('#send_era_chk_id').val('');
	$('#send_era_id').val('');
	var arr_files = $("input[name='era_files_id[]']");
	var arr_files_len = arr_files.length;
	if(typeof(arg)!= "undefined" && arg!=""){
		for(i=0; i<arr_files_len; i++){
			if($(arr_files[i]).is(':checked')==true){
				flag = flag+1;
			}		
		}
		if(flag<=0){
			top.fAlert('Please select file to '+arg+'.');
			return false;		
		}else{
			$('#era_action').val(arg);	
			var ask = "Do you want to "+arg+" all selected records?";
			top.fancyConfirm(ask,'', "window.top.fmain.document.era_frm.submit()","window.top.removeMessi()");	
		}
	}else{
		document.era_frm.submit();
	}
}
function show_check_detail(id){
	$('.chk_detail_css').hide();
	$('#chk_detail_id_'+id).show();
}
function sel_enc_chk_fun(){
	if($('#main_enc_chk').is(':checked')==true){
		$('.sel_enc_chk_class').each(function()
		{
			if($(this).prop("disabled")==true || $(this).prop("disabled")=="disabled"){
			}else{
				$(this).prop({"checked":true});
			}					
		});
	}else{
		$('.sel_enc_chk_class').prop({"checked":false});
	}
}
function show_era_detail(id,chk_id){
	top.show_loading_image("show");
	$('#era_action').val('');
	$('#send_era_id').val(id);
	$('#send_era_chk_id').val(chk_id);
	$('.chk_detail_css').hide();
	$('#chk_detail_id_'+id).show();
	document.era_frm.submit();
}

function era_process(id,process_btn){
	$('#era_action').val('');
	if(!id){
		top.show_loading_image("hide");
		top.fAlert('Please select any file to process.');
		return false;
	}else{
		if($("#write_off_code").val()==""){
			top.show_loading_image("hide");
			top.fAlert("Please select the Write off code");
			return false;
		}
	}
	$('#era_action').val('process');
	if(process_btn=="Start Process"){
		top.show_loading_image("show");
		document.era_frm.submit();
	}else{
		var ask = "Are you sure to post ERA manually?";
		top.fancyConfirm(ask,'', "window.top.fmain.document.era_frm.submit()","window.top.removeMessi()");	
	}
}

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

function era_detail_popup(era_id,chk_id,pat_id,era_manual_payments){
	var url="era_post_payments_popup.php?era_manual_payments="+era_manual_payments+"&send_era_id="+era_id+'&send_era_pat_id='+pat_id+'&send_era_chk_id='+chk_id;
	var winFeatures	= "top=50,left=100,width=1100,height=650,scrollbars=yes,resizable=1";
	top.popup_win(url,winFeatures);
}

function download_file_name(file_id){
	var url = "../billing/downloadFile.php?era_id="+file_id;
	window.location=url;
}

function remit_summary(file_id,era_id){
	window.open("../billing/era_remit_summary.php?era_file_id="+file_id+"&era_file_chk_id="+era_id,'ERA Remit Summary','width=150,height=100,top=25,left=70,scrollbars=no,resizable=1');
}

sel_enc_chk_fun();

var mainBtnArr = new Array();
var send_era_id = $('#send_era_id').val();
var send_era_chk_id = $('#send_era_chk_id').val();
var era_manual_payments = $('#era_manual_payments').val();
var start_process = '<?php echo $process_btn; ?>';
if(send_era_id>0){
	mainBtnArr[0] = new Array("start_process",start_process,"top.fmain.era_process("+send_era_id+",'"+start_process+"');");
	mainBtnArr[1] = new Array("era_print","Print","top.fmain.printEraDetailsFn("+send_era_id+",'"+send_era_chk_id+"','"+era_manual_payments+"');");
	mainBtnArr[2] = new Array("era_save","Save","top.fmain.download_file_name("+send_era_id+");");
	mainBtnArr[3] = new Array("remit_summary","Remit Summary","top.fmain.remit_summary("+send_era_id+",'"+send_era_chk_id+"');");
}
top.btn_show("PPR",mainBtnArr);
top.$('#acc_page_name').html('<?php echo $pg_title; ?>');	
</script>
