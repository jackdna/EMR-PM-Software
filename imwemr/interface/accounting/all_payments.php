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
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
 
function addRecords_obj($arrayRecord, $table){
	if(is_array($arrayRecord)){
		$countFields = count($arrayRecord);
		$insertStr = "INSERT INTO $table SET ";
		foreach($arrayRecord as $field => $value){
			++$seq;
			$insertStr .= "$field = '".addslashes($value)."'";
			if($seq<$countFields){
				$insertStr .= ", ";
			}
		}
		$insertQry = imw_query($insertStr);
		$insertId = imw_insert_id();
		return $insertId;
	}		
} 
 
function updateRecords_obj($arrayRecord, $table, $condId, $condValue){
	if(is_array($arrayRecord)){
		$countFields = count($arrayRecord);
		$updateStr = "UPDATE $table SET ";
		foreach($arrayRecord as $field => $value){
			++$seq;
			$updateStr .= "$field = '".addslashes($value)."'";
			if($seq<$countFields){
				$updateStr .= ", ";
			}
		}
		$updateStr .= " WHERE $condId = '$condValue'";			
		$updateQry = imw_query($updateStr);
	}		
} 
$transactionDate = date('Y-m-d');
$entered_date=date('Y-m-d H:i:s');
//echo "<pre>";
//print_r($_REQUEST);
//exit;
//-------------------------------- E&M PROCEDURES ARRAY --------------------------------//
	$eAndMCptArray = array(99201, 99202, 99203, 99204, 99205, 99211, 99212, 99213, 99214, 99215, 99242, 99243, 99244, 99245);
//-------------------------------- E&M PROCEDURES ARRAY --------------------------------//
$operatorName = $_SESSION['authUser'];
$operatorId = $_SESSION['authUserID'];
$encounter_id = $_REQUEST['encounter_id'];
$patient_id = $_SESSION['patient'];
$copay = $_REQUEST['copay'];
$insCoId = $_REQUEST['insProviderName'];
$paymentClaims = $_REQUEST['paymentClaims'];
$paidBy = $_REQUEST['paidBy'];
$paymentMode = $_REQUEST['paymentMode'];
$checkNo = $_REQUEST['checkNo'];
$cCNo = $_REQUEST['cCNo'];
$expDate = $_REQUEST['expireDate'];
$creditCardCo=$_REQUEST['creditCardCo'];
$credit_note=addslashes($_REQUEST['credit_note']);
$dos=$_REQUEST['dos'];
$crd_id_up_imp=$_REQUEST['crd_id'];
$adjust_other=$_REQUEST['adjust_other_txt'];
$write_off_code=$_REQUEST['write_off_code'];
$discount_code=$_REQUEST['discount_code'];
$adj_code=$_REQUEST['adj_code'];
$cas_code=$_REQUEST['cas_code'];

if($cas_code!=""){
	$cas_code_exp=explode('--',$cas_code);
	if($cas_code_exp[1]!=""){
		$cas_code_exp_final=explode(' ',$cas_code_exp[0]);
		$cas_type=$cas_code_exp_final[0];
		$cas_code=$cas_code_exp_final[1];
	}else{
		$sel_rec_cas=imw_query("select cas_code from cas_reason_code where cas_desc='$cas_code'");
		$sel_cas=imw_fetch_array($sel_rec_cas);
		$cas_code_exp_final=explode(' ',$sel_cas['cas_code']);
		$cas_type=$cas_code_exp_final[0];
		$cas_code=$cas_code_exp_final[1];
	}
}	

$statement_count=1;


// GETING REQUEST VARIABLES
$apply = $_REQUEST['apply'];
$coPayChk = $_REQUEST['coPayChk'];
$chkbxArr = $_REQUEST['chkbx'];
$counterIds = $_REQUEST['counterIdArr'];
$chargeListDetailIds = $_REQUEST['chargeListDetailIdArr'];
$paidAmountPaying = $_REQUEST['paidAmount'];
	$paidAmountPaying = approvedAmtText($paidAmountPaying);
$paidDate = $_REQUEST['paidDate'];
	list($month, $day, $year)=explode("-", $paidDate);
	$paidDate = $year."-".$month."-".$day;
$commentsArea = $_REQUEST['commentsArea'];
$commentsArea = normalise($commentsArea);
$encCommentsType = $_REQUEST['encComments'];
$commentsDate = $year."-".$month."-".$day;
$insSelected = $_REQUEST['insSelected'];
$copay_apply_procedure = $_REQUEST['copay_apply_procedure'];
$copayAmount = $_REQUEST['copayAmount'];		

// CODE OF HEARDOFF INSURANCE PAYMENT
if($insSelected == 1 && $paidAmountPaying>0){
	$paymentOf = 'primary_paid';
}else if($insSelected == 2 && $paidAmountPaying>0){
	$paymentOf = 'secondary_paid';
}else if($insSelected == 3 && $paidAmountPaying>0){
	$paymentOf = 'tertiary_paid';
}

// CODE OF HEARDOFF INSURANCE PAYMENT
if($paymentOf && $paidAmountPaying>0){
	$ins_pay_enc=array();
	for($ch=0;$ch<count($enc_arr);$ch++){
		$ch_ins=$ch+1;
		if($_REQUEST["payNew".$ch_ins]>0){
			$ins_pay_enc[]=$enc_arr[$ch];
		}
	}
	$ins_pay_enc=array_unique($ins_pay_enc);
}

$paymentClaims = $_REQUEST['paymentClaims'];
// GETING REQUEST VARIABLES
//print '<pre>';
//print_r($_REQUEST);
//exit;
// START TRANSACTIONS 


$qry_pol_main=imw_query("select discount_amount,return_chk_proc,return_chk_amt from copay_policies");
$fet_pol_main=imw_fetch_array($qry_pol_main);
$pol_discount_amount=str_replace('%','',$fet_pol_main['discount_amount']);


if($paymentClaims=='Returned Check' && $checkNo!=""){
	$enc_ret_chk_arr=$_REQUEST['enc_arr'];
	$return_chk_proc=$fet_pol_main['return_chk_proc'];
	$return_chk_amt=$fet_pol_main['return_chk_amt'];
	include "del_payment.php";
}
if($paymentClaims=='check_in_out'){
	$check_paid_by="check_in_out";
	$paymentClaims = "Paid";
	$qry_payment_main=imw_query("select payment_id from  check_in_out_payment 
									where patient_id='$patient_id'
									and del_status='0'  order by payment_id desc");
	$fet_payment_main=imw_fetch_array($qry_payment_main);
	$check_in_out_pay_id=$fet_payment_main['payment_id'];
}
if($paymentClaims == 'Paid' || $paymentClaims == 'Deposit' || $paymentClaims == 'Interest Payment' || $check_paid_by=='check_in_out' || $paymentClaims=='Update Allow Amt'){
	
	if(($apply=='applySubmit') || ($apply=='applyRecieptSubmit')){	
		for($j=1;$j<=count($counterIds);$j++){
			if($_REQUEST['coPayChk'.$j] == 'true' && $_REQUEST['proc_copay'.$j]<>''){
				$coPayAppliedChargeListDetailId[]=$_REQUEST['proc_copay'.$j];
				$encounter_id_ar[]=$_REQUEST['encounter_id_arr'][$j-1];
			}else{
				//$coPayAppliedChargeListDetailId="";
			}
		}	
		
		if($coPayAppliedChargeListDetailId){
			foreach($coPayAppliedChargeListDetailId as $foreach_chldid){
				$getProcDetails_ch_row = imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_detail_id = '".$foreach_chldid."'");
				$getProcDetails_ch = imw_fetch_object($getProcDetails_ch_row);
				$ch_chk_id[] = $getProcDetails_ch->charge_list_id;
			}
		}
		// GETTING ENCOUNTER DETAILS FROM PATIENT CHARGE LIST TABLE
		 $j=0;
		 if($ch_chk_id){
			foreach($ch_chk_id as $ch_chk_id_final){
				$getEncounterDetails_row = imw_query("select * from patient_charge_list where del_status='0' and charge_list_id = '".$ch_chk_id_final."'");
				$getEncounterDetails = imw_fetch_object($getEncounterDetails_row);
				$encounter_id = $getEncounterDetails->encounter_id;
				$copay = $getEncounterDetails->copay;
				$copayPaid = $getEncounterDetails->copayPaid;
				// GETTING total copaypaid amount
				$getproccode = "SELECT sum(paidForProc) as tot_paidproc FROM 
							patient_chargesheet_payment_info a,
							patient_charges_detail_payment_info b
							WHERE a.encounter_id = '$encounter_id'
							AND a.payment_id = b.payment_id
							AND b.charge_list_detail_id = 0
							AND b.deletePayment=0
							ORDER BY a.payment_id DESC";
				$getproccodeQry = imw_query($getproccode);
				$getproccodeRow = imw_fetch_array($getproccodeQry);
				$paidForProc_chk = $getproccodeRow['tot_paidproc'];
				$tot_paid_chk=$paidForProc_chk+$copay;
					
				if($copay>0){
					$coPayAdjustedAmount_chk=1;
					$copayPaid_chk=1;
					$coPayAdjusted_chk=1;
				}
					
			
				// GET ALREADY APPLIED OR NOT
				$getProcDetails_row = imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_id = '".$charge_list_id."'");
				while($row = imw_fetch_object($getProcDetails_row)){
					$getProcDetails[] = $row;
				}
				if(count($getProcDetails)>0){
					foreach($getProcDetails as $details){
						$coPayAdjustedAmount = $details->coPayAdjustedAmount;
						if($coPayAdjustedAmount == 1){
							$alreadyAppliedOn_Id = $details->charge_list_detail_id;
							$newBalance = $details->newBalance;
							break;
						}
					}
				}		
				// COPAY PAID STATUS
				if($copay>0 && $coPayAppliedChargeListDetailId[$j]<>""){ 
					unset($arrayRecord);
					$arrayRecord['copayPaid'] = $copayPaid_chk; 
					$arrayRecord['coPayPaidDate'] = $paidDate;
					$updateCoPayPaid = updateRecords_obj($arrayRecord, 'patient_charge_list', 'encounter_id', $encounter_id);
					// START INSERT MAIN PAYMENT TRANSACTION FOR ONLY CO-PAY
						if(($paidAmountPaying!='') || ($paidAmountPaying!='0.00') || ($paidAmountPaying!='0')){
							unset($arrayRecord);
							$arrayRecord['encounter_id'] = $encounter_id;
							$arrayRecord['paid_by'] = $paidBy;
							$arrayRecord['payment_amount'] = $copay;
							$arrayRecord['insProviderId'] = $insCoId;
							$arrayRecord['payment_mode'] = $paymentMode;
							$arrayRecord['checkNo'] = $checkNo;
							$arrayRecord['creditCardNo'] = $cCNo;
							$arrayRecord['creditCardCo'] = $creditCardCo;
							$arrayRecord['date_of_payment'] = $paidDate;
							$arrayRecord['expirationDate'] = $expDate;
							$arrayRecord['balance_amount'] = 0;
							$arrayRecord['operatorId'] = $operatorId;
							$arrayRecord['insCompany'] = $insSelected;
							$arrayRecord['paymentClaims'] = $paymentClaims;
							$arrayRecord['transaction_date'] = $transactionDate;
							$arrayRecord['statement_pmt'] = $statement_pmt;
							$insertCoPayPaymentInsId = addRecords_obj($arrayRecord, 'patient_chargesheet_payment_info');
							// START CO-PAY DETAIL PAYMENT
							unset($arrayRecord);
							$arrayRecord['payment_id'] = $insertCoPayPaymentInsId;
							$arrayRecord['charge_list_detail_id'] = 0;
							$arrayRecord['paidBy'] = $paidBy;
							$arrayRecord['paidDate'] = $paidDate;
							$arrayRecord['paidForProc'] = $copay;
							$arrayRecord['CAS_type'] = $cas_type;
							$arrayRecord['CAS_code'] = $cas_code;
							$arrayRecord['operator_id'] = $_SESSION['authId'];
							$arrayRecord['entered_date'] = date('Y-m-d H:i:s'); 
							
							$insertPaymentDetails = addRecords_obj($arrayRecord, 'patient_charges_detail_payment_info');
							if($check_paid_by=="check_in_out"){
								unset($arrayRecord);
								$arrayRecord['encounter_id'] = $encounter_id;
								$arrayRecord['charge_list_detail_id'] = $coPayAppliedChargeListDetailId[$j];
								$arrayRecord['patient_id'] = $patient_id;
								$arrayRecord['acc_payment_id'] = $insertCoPayPaymentInsId;
								$arrayRecord['check_in_out_payment_id'] = $check_in_out_pay_id;
								$check_in_out_post = addRecords_obj($arrayRecord, 'check_in_out_payment_post');
							}
						}
					}
					
				
				// APPLY ON IF NOT YET CO-PAY APPLIED ON ANY CHARGES
					if($copay>0){ 
						if($coPayAppliedChargeListDetailId[$j]!=""){
						
							$getProcCountStr123 = "SELECT newBalance FROM patient_charge_list_details WHERE del_status='0' and charge_list_detail_id='$coPayAppliedChargeListDetailId[$j]'";
							$getProcCountQry123 = @imw_query($getProcCountStr123);
							if(imw_num_rows($getProcCountQry123)>0){
								$getProcCountRow123 = @imw_fetch_array($getProcCountQry123);
								$newBalance_chhk = $getProcCountRow123['newBalance'];
							}
							
							if($copay>$newBalance_chhk){
								$copayAmount_min=$newBalance_chhk;
								$over_pay_bycopay=$copay-$newBalance_chhk;
							}else{
								$copayAmount_min=$copay;
								$over_pay_bycopay=0;
							}
							
							
							$ApplyCopayQry = imw_query("UPDATE patient_charge_list_details SET
														coPayAdjustedAmount = '$coPayAdjustedAmount_chk', 
														newBalance = newBalance-$copayAmount_min,
														overPaymentForProc=overPaymentForProc+$over_pay_bycopay,
														balForProc =balForProc-$copayAmount_min,
														paidStatus = 'Paid', superBillUpdate = '1'
														WHERE charge_list_detail_id = '$coPayAppliedChargeListDetailId[$j]'");
						}
					}
					$j++;
				}	
			}	
		$j=0;
		if($chkbxArr!=""){
			foreach($chkbxArr as $chargeListid){
				if($counterIds){
					foreach($counterIds as $key => $value){
						foreach($chargeListDetailIds as $c => $cId){
							if($key == $c){
								if($chargeListid==$cId){
								    $payAmountText_val = $_REQUEST['payNew'.$value];
										$payAmountText_val = approvedAmtText($payAmountText_val);
									$payAmountText = $_REQUEST['paidAmtText'.$value];
										$payAmountText = approvedAmtText($payAmountText);
									$approvedAmtText = $_REQUEST['approvedText'.$value];
										$approvedAmtText = approvedAmtText($approvedAmtText);
									$appActualTextText = $_REQUEST['appActualText'.$value];
										$appActualTextText = approvedAmtText($appActualTextText);
									$deductibleAmtText = $_REQUEST['deductibleText'.$value];
										$deductibleAmtText = approvedAmtText($deductibleAmtText);
									$overPaymentAmt = $_REQUEST['overPayment'.$value];
										$overPaymentAmt = approvedAmtText($overPaymentAmt);									
									$overPaymentNow = $_REQUEST['overPaymentNow'.$value];
										$overPaymentNow = approvedAmtText($overPaymentNow);	
									$write_off_codetNow = $_REQUEST['write_off_code'.$value];	
									$discount_codetNow = $_REQUEST['discount_code'.$value];		
									
									if($paymentClaims=='Update Allow Amt'){
										if($payAmountText_val>0 && $overPaymentNow>0){
											$overPaymentNow=$overPaymentNow-$payAmountText_val;
											$overPaymentAmt=$overPaymentAmt-$payAmountText_val;
										}
										$write_off_codetNow = "";
										$discount_codetNow = "";
										$payAmountText_val="";
										$payAmountText = "";
									}
																							
									$payAmountTextArray[] = $payAmountText;
									$chk_approved_chg=1;
									if($approvedAmtText<>$appActualTextText){
										$chk_payAmountText_val=1;
										$chk_approved_chg=0;
										$ovr_approved_amt_minus=1;
									}else{
										$chk_payAmountText_val=$payAmountText_val;
									}
									
									if($payAmountText_val>0){
										$chk_approved_chg=1;
									}
									
									//echo "<pre>";
									//print_r($_REQUEST);
									// GETTING PREVIOUS DETAILS FOR CHARGE DETAILS
									$getChargeDertailsStr = "SELECT * FROM patient_charge_list_details 
															WHERE del_status='0' and charge_list_detail_id='$cId'";
									$getChargeDertailsQry = imw_query($getChargeDertailsStr);
									$getChargeDertailsRow = imw_fetch_assoc($getChargeDertailsQry);
										$charge_list_id = $getChargeDertailsRow['charge_list_id'];
										$procCode = $getChargeDertailsRow['procCode'];
										$totalAmount = $getChargeDertailsRow['totalAmount'];
										$paidForProc = $getChargeDertailsRow['paidForProc'];
										$balForProc = $getChargeDertailsRow['balForProc'];
										$approvedAmt = $getChargeDertailsRow['approvedAmt'];
										$approvedAmt_chk = $getChargeDertailsRow['approvedAmt'];
										$deductAmt = $getChargeDertailsRow['deductAmt'];										
										$creditProcAmount = $getChargeDertailsRow['creditProcAmount'];
										$coPayAdjustedAmount = $getChargeDertailsRow['coPayAdjustedAmount'];
										$newBalance = $getChargeDertailsRow['newBalance'];
										$write_Off = $getChargeDertailsRow['write_off'];
										$overPaymentForProc_chk = $getChargeDertailsRow['overPaymentForProc'];
										
										/*if($overPaymentForProc>0){
											$overPaymentNow= $overPaymentNow+$overPaymentForProc;
										}*/
										$getChargeDertailsStr_all = "SELECT encounter_id
														 FROM patient_charge_list WHERE del_status='0' and charge_list_id='$charge_list_id'";
										$getChargeDertailsQry_all = imw_query($getChargeDertailsStr_all);
										$getChargeDertailsRow_all = imw_fetch_array($getChargeDertailsQry_all);
										$encounter_id = $getChargeDertailsRow_all['encounter_id'];
										
									
									if($ded_updat==1){
									}else{
										if(($payAmountText =='0.00' || $payAmountText =='0') && $insCoId>0 && strtolower($paymentClaims)=="paid"){
										
											unset($arrayRecord);
											$arrayRecord['encounter_id'] = $encounter_id;
											$arrayRecord['paid_by'] = $paidBy;
											$arrayRecord['payment_amount'] = 0;
											$arrayRecord['insProviderId'] = $insCoId;
											$arrayRecord['payment_mode'] = $paymentMode;
											$arrayRecord['checkNo'] = $checkNo;
											$arrayRecord['creditCardNo'] = $cCNo;
											$arrayRecord['creditCardCo'] = $creditCardCo;
											$arrayRecord['date_of_payment'] = $paidDate;
											$arrayRecord['expirationDate'] = $expDate;
											$arrayRecord['operatorId'] = $operatorId;
											$arrayRecord['insCompany'] = $insSelected;
											$arrayRecord['paymentClaims'] = $paymentClaims;
											$arrayRecord['transaction_date'] = date('Y-m-d');
											$arrayRecord['statement_pmt'] = $statement_pmt;
											
											$paymentInsertId = addRecords_obj($arrayRecord, 'patient_chargesheet_payment_info');
											
											unset($arrayRecord);
											$arrayRecord['payment_id'] = $paymentInsertId;
											$arrayRecord['charge_list_detail_id'] = $chargeListid;
											$arrayRecord['paidBy'] = $paidBy;
											$arrayRecord['paidDate'] = $paidDate;
											$arrayRecord['paidForProc'] = 0;
											$arrayRecord['CAS_type'] = $cas_type;
											$arrayRecord['CAS_code'] = $cas_code;
											$arrayRecord['operator_id'] = $_SESSION['authId'];
											$arrayRecord['entered_date'] = date('Y-m-d H:i:s'); 
											$paymentDetailsId = addRecords_obj($arrayRecord, 'patient_charges_detail_payment_info');
											
										}
									}									
									if(($payAmountText>0 || $overPaymentNow>0 || $approvedAmtText>=0) && ($chk_payAmountText_val>0)){
										$encouter_id_ref_chk[]=$encounter_id;
										$getProcCodeStr_row = imw_query('select * from cpt_fee_tbl where cpt_fee_id = '.$procCode.'');
										$getProcCodeStr = imw_fetch_object($getProcCodeStr_row);
											$cpt4_code = $getProcCodeStr->cpt4_code;
											$cpt_prac_code = $getProcCodeStr->cpt_prac_code;
										// UPDATE PAYMENT AMOUNTS
										if($cpt4_code == 92015){
											unset($arrayRecord);
											$arrayRecord['referactionPaid'] = 1;
											$updateQry = updateRecords_obj($arrayRecord, 'patient_charge_list', 'encounter_id', $encounter_id);
										}
										$det_adust_qry=imw_query("select sum(payment_amount) as adj_amt 
														from account_payments where  charge_list_detail_id='$cId'
														and (payment_type='Adjustment' or payment_type='Over Adjustment')
														and del_status!='1'");
										$det_adust_rec=imw_fetch_array($det_adust_qry);
										$adj_amt=$det_adust_rec['adj_amt'];	
										
										$credit_amt_qry=imw_query("select sum(amountApplied)  as cr_amountApplied
																		from creditapplied
																	where crAppliedTo='adjustment'
																	and crAppliedToEncId_adjust = '$encounter_id'
																	and charge_list_detail_id_adjust = '$cId'
																	and credit_applied='1' and delete_credit ='0'");
										$credit_amt_rec=imw_fetch_array($credit_amt_qry);
										$cr_amt_app=$credit_amt_rec['cr_amountApplied'];
										
										// UPDATE PAYMENT STATUS OF CHARGE LIST DETAIL IDs.
										$paidForProcNew = $paidForProc + $payAmountText;
										$writeOff = $totalAmount - $approvedAmtText;	
										//$balForProcNew = $totalAmount - $writeOff - $paidForProc - $creditProcAmount - $payAmountText;
										$balForProcNew = $totalAmount - $writeOff - $paidForProc  - $payAmountText - $adj_amt;
										//if($copayAmount>0){
											$getproccode = "SELECT sum(paidForProc) as tot_paidproc FROM 
															patient_chargesheet_payment_info a,
															patient_charges_detail_payment_info b
															WHERE a.encounter_id = '$encounter_id'
															AND a.payment_id = b.payment_id
															AND b.charge_list_detail_id = 0
															AND b.deletePayment=0
															ORDER BY a.payment_id DESC";
											$getproccodeQry = imw_query($getproccode);
											$getproccodeRow = imw_fetch_array($getproccodeQry);
											$paidForProc_chk = $getproccodeRow['tot_paidproc'];
											$tot_paid_chk1=0;
											$chk_paidForProc=0;
											//echo $balForProcNew.'-'.$chk_bal_for_proc;
											if($copay>0 && $cId==$coPayAppliedChargeListDetailId[$j]){
												$tot_paid_chk1=$paidForProc_chk;
												$chk_paidForProc=$paidForProc+$tot_paid_chk1;
												$chh_newBalance=$newBalance+$copay;
												if($payAmountText>$newBalance && $newBalance>0){
													$chk_bal_for_proc=$paidForProcNew-$newBalance;
													$paidForProcNew=$paidForProcNew-$chk_bal_for_proc;
													$overPaymentAmt=$overPaymentAmt+$chk_bal_for_proc;
													$balForProcNew=$balForProcNew-$chk_bal_for_proc;
													$add_paid_proc_amt_copay=$paidForProcNew+$copayAmount;
													if((int)($add_paid_proc_amt_copay)==(int)($approvedAmt)){
														$balForProcNew=0;
													}
												}else{
													if($paidForProcNew>=$approvedAmt){
														$overPaymentAmt=$overPaymentAmt+$copayAmount;
													}
													$add_paid_proc_amt_copay=$paidForProcNew+$copayAmount;
													if((int)($add_paid_proc_amt_copay)==(int)($approvedAmt)){
														$balForProcNew=0;
													}
												}
											}else{
												if($coPayAdjustedAmount==1){
													$tot_paid_chk1=$paidForProc_chk;
													$chk_paidForProc=$paidForProc+$tot_paid_chk1;
													$balForProcNew=$balForProcNew-$tot_paid_chk1;
												}else{
													$chk_paidForProc=$paidForProc;
												}	
											}	
										//}else{
											//$chk_paidForProc=$paidForProc;
										//}	
										if($newBalance>0  || $balForProcNew>0){
											//$newBalance= $totalAmount - $writeOff - $chk_paidForProc - $creditProcAmount - $payAmountText;
											$newBalance= $totalAmount - $writeOff - $chk_paidForProc  - $payAmountText -$adj_amt;
										}
										//exit; 
										if(($deductibleAmtText!=0) || ($deductibleAmtText!='')){
											$deductibleDate = $paidDate;
											$write_off_date =  $paidDate;
										}
										// GETTING DATA FROM WRITE OFF TABLE
										$total_write_off_amount = 0;
										$getWriteOffDetailsStr = "SELECT * FROM paymentswriteoff 
																	WHERE patient_id = '$patient_id'
																	AND encounter_id = '$encounter_id'
																	AND charge_list_detail_id = '$cId'
																	AND delStatus = 0";
										$getWriteOffDetailsQry = imw_query($getWriteOffDetailsStr);
										$getWriteOffRowsCount = imw_num_rows($getWriteOffDetailsQry);
										if($getWriteOffRowsCount>0){
											while($getWriteOffDetailsRows = imw_fetch_array($getWriteOffDetailsQry)){
												$write_off_amount = $getWriteOffDetailsRows['write_off_amount'];
												$total_write_off_amount = $total_write_off_amount + $write_off_amount;
											}
											$balForProcNew = $balForProcNew - $total_write_off_amount;
											$newBalance= $newBalance - $total_write_off_amount;
										}		
										
										if($balForProcNew<0){
											$balForProcNew = 0;
										}	
										if($newBalance<0){
											$newBalance=0;
										}
										if($paidForProcNew>$approvedAmt_chk){
											$paidForProcNew=$approvedAmt_chk;
										}
										//echo $overPaymentForProc_chk;
										if($overPaymentAmt>0  && $copay>0 && $cId==$coPayAppliedChargeListDetailId[$j]){
											//$overPaymentAmt=$overPaymentAmt+$overPaymentForProc_chk;
										}
										//echo $paidForProcNew;
										if($overPaymentAmt>0){
											if($balForProcNew>=$overPaymentAmt){
												$balForProcNew=$balForProcNew-$overPaymentAmt;
												$paidForProcNew=$paidForProcNew+$overPaymentAmt;
												$newBalance=$newBalance-$overPaymentAmt;
												$overPaymentAmt=0;
											}else{
												$overPaymentAmt=$overPaymentAmt-$balForProcNew;
												$overPaymentAmt=$overPaymentAmt-$adj_amt;
												$paidForProcNew=$approvedAmtText-$total_write_off_amount;
												$newBalance=0;
												$balForProcNew=0;
											}
										}
										if($paidForProcNew>$paidForProc && $overPaymentAmt>0 && ($paidForProc==0 || $paidForProc==0.00)){
											//$paidForProcNew=$paidForProc;
										}
										//echo"<pre>";
										if($newBalance==0){
											$balForProcNew=0;
										}
										if($newBalance>0){
											$newBalance=$newBalance+$adj_amt;
											$balForProcNew=$balForProcNew+$adj_amt;
										}
										unset($arrayRecord);
										$arrayRecord['paidForProc'] = $paidForProcNew;
										$arrayRecord['balForProc'] = $balForProcNew;
										$arrayRecord['approvedAmt'] = $approvedAmtText;
										$arrayRecord['deductAmt'] = $deductibleAmtText;
										$arrayRecord['newBalance'] = $newBalance;
										$arrayRecord['paidStatus'] ='Paid';
										$arrayRecord['superBillUpdate'] = 1;
										$arrayRecord['deductDate'] = $deductibleDate;
										$arrayRecord['overPaymentForProc'] = $overPaymentAmt;
										
										if($approvedAmt_chk!=$approvedAmtText){
											if($writeOff>0){
												$write_off_code_id=$write_off_codetNow;
												if($write_off_code_id>0){
													$arrayRecord['write_off_code_id'] = $write_off_code_id;
												}
											}else{
												$arrayRecord['write_off_code_id'] = "";
											}
											
											$arrayRecord['write_off'] = $writeOff;
											$arrayRecord['write_off_by'] = $insCoId;
											$arrayRecord['write_off_date'] = $write_off_date;
											$arrayRecord['write_off_opr_id'] = $operatorId;
											$arrayRecord['write_off_dot'] = $transactionDate;
											
											$qry1=imw_query("Insert INTO defaultwriteoff SET
											  patient_id='".$patient_id."',
											  encounter_id='".$encounter_id."',
											  charge_list_id='".$charge_list_id."',
											  charge_list_detail_id='".$cId."',
											  write_off_amount='".$writeOff."',	
											  write_off_by='".$insCoId."',		  		  
											  write_off_operator_id='".$operatorId."',
											  write_off_dop='".$write_off_date."',
											  write_off_dot='".$entered_date."',
											  write_off_code_id='".$write_off_code_id."'");
											
										}
										
										//print_r($arrayRecord);
										//exit;
										$updateQry = updateRecords_obj($arrayRecord, 'patient_charge_list_details', 'charge_list_detail_id', $cId);
										// INSERT PAYMENT DETAILS
										
										if(($payAmountText!="0.00") && ($payAmountText!="") && ($payAmountText!='0') && $payAmountText>0){
											 // UPDATE PAYMENT STATUS OF CHARGE LIST info.
											unset($arrayRecord);
											$arrayRecord['encounter_id'] = $encounter_id;
											$arrayRecord['paid_by'] = $paidBy;
											//$arrayRecord['payment_amount'] = $payAmountText;
											$arrayRecord['payment_amount'] = $payAmountText_val;
											$arrayRecord['insProviderId'] = $insCoId;
											$arrayRecord['payment_mode'] = $paymentMode;
											$arrayRecord['checkNo'] = $checkNo;
											$arrayRecord['creditCardNo'] = $cCNo;
											$arrayRecord['creditCardCo'] = $creditCardCo;
											$arrayRecord['date_of_payment'] = $paidDate;
											$arrayRecord['expirationDate'] = $expDate;
											$arrayRecord['operatorId'] = $operatorId;
											$arrayRecord['insCompany'] = $insSelected;
											$arrayRecord['paymentClaims'] = $paymentClaims;
											$arrayRecord['transaction_date'] = date('Y-m-d');
											$arrayRecord['statement_pmt'] = $statement_pmt;
											
											if($ded_updat==1){
												//$paymentDetailsId = addRecords_obj($arrayRecord, 'patient_charges_detail_payment_info');
											}else{
												$paymentInsertId = addRecords_obj($arrayRecord, 'patient_chargesheet_payment_info');
												if($check_paid_by=="check_in_out"){
													unset($arrayRecord);
													$arrayRecord['encounter_id'] = $encounter_id;
													$arrayRecord['charge_list_detail_id'] = $cId;
													$arrayRecord['patient_id'] = $patient_id;
													$arrayRecord['acc_payment_id'] = $paymentInsertId;
													$arrayRecord['check_in_out_payment_id'] = $check_in_out_pay_id;
													$check_in_out_post = addRecords_obj($arrayRecord, 'check_in_out_payment_post');
												}
											}
											// UPDATE PAYMENT STATUS OF CHARGE LIST DETAIL IDs.
											unset($arrayRecord);
											$arrayRecord['payment_id'] = $paymentInsertId;
											$arrayRecord['charge_list_detail_id'] = $chargeListid;
											$arrayRecord['paidBy'] = $paidBy;
											$arrayRecord['paidDate'] = $paidDate;
											$arrayRecord['paidForProc'] = $payAmountText_val;
											//$arrayRecord['paidForProc'] = $payAmountText;
											//$arrayRecord['overPayment'] = $overPaymentNow;
											$arrayRecord['deduct_amount'] = $deductibleAmtText;
											$arrayRecord['CAS_type'] = $cas_type;
											$arrayRecord['CAS_code'] = $cas_code;
											$arrayRecord['operator_id'] = $_SESSION['authId'];
											$arrayRecord['entered_date'] = date('Y-m-d H:i:s'); 
											
											if($ded_updat==1){
												//$paymentDetailsId = addRecords_obj($arrayRecord, 'patient_charges_detail_payment_info');
											}else{
												$paymentDetailsId = addRecords_obj($arrayRecord, 'patient_charges_detail_payment_info');
											}
											$paymentDetailsArray[] = $paymentDetailsId;
											
											//paid for procedure correct amount
											$final_paid_proc=0;
											$getproccode1 = "SELECT sum(paidForProc) as tot_paidproc FROM 
															patient_chargesheet_payment_info a,
															patient_charges_detail_payment_info b
															WHERE a.encounter_id = '$encounter_id'
															AND a.payment_id = b.payment_id
															AND b.charge_list_detail_id = '$cId'
															AND b.deletePayment=0
															and b.paidForProc>0
															ORDER BY a.payment_id DESC";
											$getproccodeQry1 = imw_query($getproccode1);
											$getproccodeRow1 = imw_fetch_array($getproccodeQry1);
											$final_paid_proc = $getproccodeRow1['tot_paidproc'];
											if($final_paid_proc>0){
												if($paidForProcNew>$final_paid_proc){
													unset($arrayRecord);
													$arrayRecord['paidForProc'] = $final_paid_proc+$cr_amt_app;
													$updateQry = updateRecords_obj($arrayRecord, 'patient_charge_list_details', 'charge_list_detail_id', $cId);
												}
											}
											//paid for procedure correct amount
											
										}else{
											// INSERT PAYMENT TRANSACTION FOR OVER PAYMENT ONLY 										
											$overPaymentForProc = $overPaymentNow;	
											if($ovr_approved_amt_minus>0 && $overPaymentForProc>$payAmountText_val){
												$overPaymentForProc=$payAmountText_val;
											}										
											if(($overPaymentForProc!='0') && ($overPaymentForProc!='0.00') && ($overPaymentForProc!='') && $overPaymentForProc>0 && $chk_approved_chg>0){
												unset($arrayRecord);
												$arrayRecord['encounter_id'] = $encounter_id;
												$arrayRecord['paid_by'] = $paidBy;
												$arrayRecord['payment_amount'] = $payAmountText_val;
												//$arrayRecord['payment_amount'] = $overPaymentForProc;
												$arrayRecord['payment_mode'] = $paymentMode;
												$arrayRecord['checkNo'] = $checkNo;
												$arrayRecord['creditCardNo'] = $cCNo;
												$arrayRecord['creditCardCo'] = $creditCardCo;
												$arrayRecord['date_of_payment'] = $paidDate;
												$arrayRecord['expirationDate'] = $expDate;
												$arrayRecord['operatorId'] = $operatorId;
												$arrayRecord['insProviderId'] = $insCoId;
												$arrayRecord['insCompany'] = $insSelected;
												$arrayRecord['paymentClaims'] = $paymentClaims;
												$arrayRecord['transaction_date'] = date('Y-m-d');
												$arrayRecord['statement_pmt'] = $statement_pmt;
												$inserIdPayment = addRecords_obj($arrayRecord, 'patient_chargesheet_payment_info');
												unset($arrayRecord);
												$arrayRecord['payment_id'] = $inserIdPayment;
												
												$arrayRecord['paidForProc'] = $payAmountText_val;
												//$arrayRecord['overPayment'] = $overPaymentForProc;
												$arrayRecord['charge_list_detail_id'] = $cId;
												$arrayRecord['paidBy'] = $paidBy;
												$arrayRecord['paidDate'] = $paidDate;
												$arrayRecord['CAS_type'] = $cas_type;
												$arrayRecord['CAS_code'] = $cas_code;
												$arrayRecord['operator_id'] = $_SESSION['authId'];
												$arrayRecord['entered_date'] = date('Y-m-d H:i:s'); 
												$inserDetailPayments = addRecords_obj($arrayRecord, 'patient_charges_detail_payment_info');
												if($check_paid_by=="check_in_out"){
													unset($arrayRecord);
													$arrayRecord['encounter_id'] = $encounter_id;
													$arrayRecord['charge_list_detail_id'] = $cId;
													$arrayRecord['patient_id'] = $patient_id;
													$arrayRecord['acc_payment_id'] = $inserIdPayment;
													$arrayRecord['check_in_out_payment_id'] = $check_in_out_pay_id;
													$check_in_out_post = addRecords_obj($arrayRecord, 'check_in_out_payment_post');
												}
											}
											//paid for procedure correct amount
											/*$final_paid_proc=0;
											$getproccode1 = "SELECT sum(paidForProc) as tot_paidproc FROM 
															patient_chargesheet_payment_info a,
															patient_charges_detail_payment_info b
															WHERE a.encounter_id = '$encounter_id'
															AND a.payment_id = b.payment_id
															AND b.charge_list_detail_id = '$cId'
															AND b.deletePayment=0
															and b.paidForProc>0
															ORDER BY a.payment_id DESC";
											$getproccodeQry1 = imw_query($getproccode1);
											$getproccodeRow1 = imw_fetch_array($getproccodeQry1);
											$final_paid_proc = $getproccodeRow1['tot_paidproc'];*/
											
											$final_paid_proc=0;
											$get_paid_chld_row = imw_query('select * from patient_charges_detail_payment_info where charge_list_detail_id = '.$cId.'');
											while($row_data = imw_fetch_object($get_paid_chld_row)){
												$get_paid_chld[] =  $row_data;
											}
											if($get_paid_chld){
												foreach($get_paid_chld as $get_paid_chld_data){
													if($get_paid_chld_data->deletePayment==0){
														$get_paidForProc=$get_paid_chld_data->paidForProc;
														$get_overPayment=$get_paid_chld_data->overPayment;
														$final_paid_proc=$final_paid_proc+$get_paidForProc+$get_overPayment;
													}
												}
											}	
											
											if($final_paid_proc>0){
												if($paidForProcNew>$final_paid_proc){
													unset($arrayRecord);
													$arrayRecord['paidForProc'] = $final_paid_proc;
													$updateQry = updateRecords_obj($arrayRecord, 'patient_charge_list_details', 'charge_list_detail_id', $cId);
												}
											}
											//paid for procedure correct amount
											// UPDATE CHARGE LIST DETAIL TABLE OVER PAYMENT
											if(count($chargeListDetailIds)>0){
												foreach($chargeListDetailIds as $k => $detailId){
													$overPaymentAmount = 0;
													$i = $k + 1;				
													$overPaymentAmount = $_REQUEST['overPayment'.$i];
													if($_REQUEST['overPaymentNow'.$i]>0){
														$overPaymentAmount=str_replace(',','',$overPaymentAmount);
														if($overPaymentAmount>0){
															if($overPaymentAmount>$overPaymentForProc_chk){
																$overPaymentAmount=$overPaymentAmount;
															}else{
																$overPaymentAmount=$overPaymentForProc_chk+$overPaymentAmount;
															}
															$updateOverPaymentStr = "UPDATE patient_charge_list_details SET
																					overPaymentForProc = '$overPaymentAmount'
																					WHERE charge_list_detail_id='$detailId'";
															//$updateOverPaymentQry = imw_query($updateOverPaymentStr);	
														}				
													}
												}	
											}
										}
									}
									if($payAmountText_val>0 or $paidBy=="Insurance"){
										if($paidBy=="Insurance" && $insCoId>0){
											$pay_type="ins";
											$ins_type=$insSelected;
										}else{
											$pay_type="pat";
										}
										patient_proc_tx_update($cId,$payAmountText_val,$pay_type,$ins_type);
									}
									$j++;
								}	
							}
						}
					}
				}
			}
		}
		//exit;
		// INSERT PAYMENT TRANSACTIONS
		if(!empty($payAmountTextArray)){			
			$insCoId = $_REQUEST['insProviderName'];
			unset($arrayRecord);
			$arrayRecord['encounter_id'] = $encounter_id;
			$arrayRecord['paid_by'] = $paidBy;
			$arrayRecord['payment_amount'] = $paidAmountPaying;
			$arrayRecord['insProviderId'] = $insCoId;
			$arrayRecord['payment_mode'] = $paymentMode;
			$arrayRecord['checkNo'] = $checkNo;
			$arrayRecord['creditCardNo'] = $cCNo;
			$arrayRecord['creditCardCo'] = $creditCardCo;
			$arrayRecord['date_of_payment'] = $paidDate;
			$arrayRecord['expirationDate'] = $expDate;
			$arrayRecord['operatorId'] = $operatorId;
			$arrayRecord['insCompany'] = $insSelected;
			$arrayRecord['paymentClaims'] = $paymentClaims;
			$arrayRecord['transaction_date'] = $transactionDate;
			$arrayRecord['statement_pmt'] = $statement_pmt;
			//$paymentInsertId = addRecords_obj($arrayRecord, 'patient_chargesheet_payment_info');
			if($coPayChk == 'true'){				
				unset($arrayRecord);
				$arrayRecord['payment_id'] = $paymentInsertId;
				$arrayRecord['paidBy'] = $paidBy;
				$arrayRecord['paidDate'] = $paidDate;
				$arrayRecord['paidForProc'] = $copay;
				//addRecords_obj($arrayRecord, 'patient_charges_detail_payment_info');
			}
			if(count($paymentDetailsArray)>0){
				foreach($paymentDetailsArray as $keys => $Ids){
					$updatePaymentIdStr = "UPDATE patient_charges_detail_payment_info SET
											payment_id = '$paymentInsertId' 
											WHERE payment_details_id = '$Ids'";
					//$updatePaymentIdQry = @imw_query($updatePaymentIdStr);
				}
			}
		}	
		// UPDATE LAST PAYMENT DATE AND AMOUNT
		$updateLastPaymentInfoStr = "UPDATE patient_charge_list SET
									lastPayment = '$paidAmountPaying',
									lastPaymentDate = '$paidDate'									
									WHERE encounter_id = '$encounter_id'";
		$updateLastPaymentInfoQry = imw_query($updateLastPaymentInfoStr);	
		
		//print_r($encouter_id_ref_chk);encounter_id_arr
		$encounter_id_arr=@array_intersect($encouter_id_ref_chk,$encounter_id_arr);
		include("manageEncounterAmounts.php");
		?>
			<script>
				var eId = '<?php echo $encounter_id; ?>';
				var eId_all = '<?php echo implode(',',$encounter_id_arr); ?>';
				<?php if($apply=='applyRecieptSubmit'){ ?>
					window.open("receipt.php?eId="+eId_all,'','width=1000,height=675,top=10,left=40,scrollbars=yes,resizable=yes');
				<?php } ?>
				top.fmain.location.href="makePayment.php?encounter_id="+eId;
			</script>
		<?php
	}else{
		$commentsDate = $_REQUEST['paidDate'];
			list($month, $day, $year)=explode("-", $commentsDate);
			$commentsDate = $year."-".$month."-".$day;			
		$commentsArea = $_REQUEST['commentsArea'];
		$commentsArea = normalise($commentsArea);		
		$encCommentsType = $_REQUEST['encComments'];
		if($commentsArea!=''){
			$insertCommentsStr = "INSERT INTO paymentscomment SET
										patient_id = '$patient_id',
										encounter_id = '$encounter_id',
										commentsType = '$encCommentsType',
										encComments = '$commentsArea',
										encCommentsDate = '$commentsDate',
										encCommentsOperatorId = '$operatorId'";
			$insertCommentsQry = imw_query($insertCommentsStr);
		}
		?>
		<script>
			var eId = <?php echo $encounter_id; ?>;
			top.fmain.frames[0].location.href="patientPendingDosDetails.php?encounter_id="+eId;
		</script>
	<?php
	
	}
}else{
	if($chkbxArr!=""){
		foreach($chkbxArr as $chargeListid){
			if($counterIds){
				foreach($counterIds as $key => $value){
					foreach($chargeListDetailIds as $c => $cId){
						if($key == $c){							
							if($chargeListid==$cId){							
								$payNewDeniedAmount = $_REQUEST['payNew'.$value];
									$payNewDeniedAmount = approvedAmtText($payNewDeniedAmount);

								$deniedAmount = $_REQUEST['paidAmtText'.$value];
									$deniedAmount = approvedAmtText($deniedAmount);
									
								//$writeOffAmt = $_REQUEST['paidAmtText'.$value];
								$writeOffAmt = $_REQUEST['payNew'.$value];
									$writeOffAmt = approvedAmtText($writeOffAmt);

								//$deductAmount = $_REQUEST['paidAmtText'.$value];
								$deductAmount = $_REQUEST['payNew'.$value];
									$deductAmount = approvedAmtText($deductAmount);
									
								//$deductAmount1 = $_REQUEST['paidAmtText'.$value];
								$deductAmount1 = $_REQUEST['payNew'.$value];
								
									$deductAmount1 = approvedAmtText($deductAmount1);
									
								$approvedAmtText = $_REQUEST['approvedText'.$value];
									$approvedAmount = approvedAmtText($approvedAmtText);
									
								$deductibleAmtText = $_REQUEST['deductibleText'.$value];
									$deductibleAmount = approvedAmtText($deductibleAmtText);
									
								$creditAmount = $_REQUEST['payNew'.$value];
									$creditAmount = approvedAmtText($creditAmount);
									
								$adjAmount = $_REQUEST['payNew'.$value];
									$adjAmount = approvedAmtText($adjAmount);
									
								$payNegtiveAmount = $_REQUEST['payNew'.$value];
									$payNegtiveAmount = approvedAmtText($payNegtiveAmount);	
									
									
								// GETTING PREVIOUS DETAILS FOR CHARGE DETAILS.
								$getChargeDertailsStr = "SELECT * FROM patient_charge_list_details 
														WHERE del_status='0' and charge_list_detail_id='$cId'";
								$getChargeDertailsQry = imw_query($getChargeDertailsStr);
								$getChargeDertailsRow = imw_fetch_array($getChargeDertailsQry);
									$charge_list_id = $getChargeDertailsRow['charge_list_id'];
									$patient_id_adust = $getChargeDertailsRow['patient_id'];
									$procCode = $getChargeDertailsRow['procCode'];
									$totalAmount = $getChargeDertailsRow['totalAmount'];
									$paidForProc = $getChargeDertailsRow['paidForProc'];
									$balForProc = $getChargeDertailsRow['balForProc'];
									$approvedAmt = $getChargeDertailsRow['approvedAmt'];
									$deductAmt = $getChargeDertailsRow['deductAmt'];
									$creditProcAmount = $getChargeDertailsRow['creditProcAmount'];
									$coPayAdjustedAmount = $getChargeDertailsRow['coPayAdjustedAmount'];
									$newBalance = $getChargeDertailsRow['newBalance'];
									$overpayamount = $getChargeDertailsRow['overPaymentForProc'];
									$procCharges = $getChargeDertailsRow['procCharges'];
									
								$getChargeDertailsStr_all = "SELECT encounter_id,date_of_service,overPayment
															 FROM patient_charge_list 
														WHERE del_status='0' and charge_list_id='$charge_list_id'";
								$getChargeDertailsQry_all = imw_query($getChargeDertailsStr_all);
								$getChargeDertailsRow_all = imw_fetch_array($getChargeDertailsQry_all);
								$encouter_id_ref = $getChargeDertailsRow_all['encounter_id'];
								$dos = $getChargeDertailsRow_all['date_of_service'];
								$ovr_paid_ref = $getChargeDertailsRow_all['overPayment'];
								$encouter_id_ref_chk[]=$encouter_id_ref;
								
								$writeOff = $totalAmount - $approvedAmount;
								$ded_updat="";
								//echo $deductAmount;
								//exit;
								/*if(($deductibleAmount!=0) || ($deductibleAmount!='')){
									   if($deductibleAmount<>$deductAmount){
											$deductAmount=$deductAmount+$deductibleAmtText;
											$deductAmount_add=$deductAmount1;
											$deductibleDate = $paidDate;
											$ded_updat=1;
										}else{
											//$deductAmount=$deductAmount+$deductibleAmtText;
											$deductAmount=$deductibleAmtText;
											$deductAmount_add=$deductibleAmtText;
											$deductibleDate = $paidDate;
											$ded_updat=1;
										}	
									}*/
									
								//----------------------	Claim Denied
								if($paymentClaims=='Denied'){
									// UPDATE DENIED PAYMENTS..
									if($payNewDeniedAmount>0){
										$updateStr = "UPDATE patient_charge_list_details SET
														paidStatus = 'Paid',
														superBillUpdate = '1',
														claimDenied = '1'
														WHERE charge_list_detail_id='$cId'";
										$updateQry = imw_query($updateStr);
										//INSERT DENIED PAYMENTS DETAILS
										$insertDeniedStr = "INSERT INTO deniedpayment SET 
															patient_id = '$patient_id',
															encounter_id = '$encouter_id_ref',
															charge_list_detail_id = '$cId',
															deniedBy = '$paidBy',
															deniedById = '$insCoId',
															deniedDate = '$paidDate',
															deniedAmount = '$payNewDeniedAmount',
															denialOperatorId = '$operatorId',
															entered_date = '$entered_date'";
															//$deniedAmount
										$insertDeniedStr.= ", CAS_type = '$cas_type',
																CAS_code = '$cas_code'";				
										if($paidBy == 'Insurance'){
											/*$insertDeniedStr.= ", CAS_type = 'PR',
																CAS_code = '49'";	*/										
										}
										$insertDeniedQry = imw_query($insertDeniedStr);
									}
									if($payNewDeniedAmount>0 or $paidBy=="Insurance"){
										if($paidBy=="Insurance" && $insCoId>0){
											$pay_type="ins";
											$ins_type=$insSelected;
										}else{
											$pay_type="pat";
										}
										patient_proc_tx_update($cId,0,$pay_type,$ins_type);
									}
								}
								
								// Payment adustment or over adustment or interest payment
								
								if($paymentClaims=='Over Adjustment'){
									//---------------- GETTING BALANCE FOR WRITE OFF
									if($adjAmount>0){
										
										if($adjAmount>0 or $paidBy=="Insurance"){
											if($paidBy=="Insurance" && $insCoId>0){
												$pay_type="ins";
												$ins_type=$insSelected;
											}else{
												$pay_type="pat";
											}
											patient_proc_tx_update($cId,$adjAmount,$pay_type,$ins_type);
										}
										
										$insertAdjStr3 = "INSERT INTO account_payments SET
															patient_id = '$patient_id',
															encounter_id = '$encouter_id_ref',
															charge_list_id = '$charge_list_id',
															charge_list_detail_id = '$cId',
															payment_by='$paidBy',
															payment_method='$paymentMode',
															cc_type = '$creditCardCo',
															check_number = '$checkNo',
															cc_number = '$cCNo',
															cc_exp_date = '$expDate',
															ins_id ='$insCoId',
															payment_amount='$adjAmount',
															payment_date='$paidDate',
															operator_id='$operatorId',
															payment_code_id='$adj_code',
															payment_type='$paymentClaims',
															entered_date='$entered_date'
															";
										$insertAdjQry3 = imw_query($insertAdjStr3);
										if($overpayamount>0){
											$bal_adj_amt=0;
											$ovr_adj_amt=$adjAmount;
											$updateStr4 = "UPDATE patient_charge_list_details SET
														   overPaymentForProc = overPaymentForProc + $ovr_adj_amt
														    WHERE charge_list_detail_id='$cId'";
											$updateQry4 = imw_query($updateStr4);
										}else{
											if($newBalance>=$adjAmount){
												$ovr_adj_amt=0;
												$bal_adj_amt=$adjAmount;
												$updateStr5 = "UPDATE patient_charge_list_details SET
													overPaymentForProc = $ovr_adj_amt,
													newBalance = newBalance - $bal_adj_amt,
													balForProc = balForProc - $bal_adj_amt,
													paidForProc = paidForProc + $bal_adj_amt
													WHERE charge_list_detail_id='$cId'";
												$updateQry5 = imw_query($updateStr5);	
											}else{
												$ovr_adj_amt=$adjAmount-$newBalance;
												$bal_adj_amt=$newBalance;
												$updateStr6 = "UPDATE patient_charge_list_details SET
												overPaymentForProc = overPaymentForProc + $ovr_adj_amt,
												newBalance = newBalance - $bal_adj_amt,
												balForProc = balForProc - $bal_adj_amt,
												paidForProc = paidForProc + $bal_adj_amt
												WHERE charge_list_detail_id='$cId'";
												$updateQry6 = imw_query($updateStr6);
											}
										}
									}
								}
								
								$final_paid_chk=$paidForProc+$overpayamount;
								if($paymentClaims=='Adjustment'){
									if($adjAmount>0 && $final_paid_chk>=$adjAmount){
										$insertAdjStr2 = "INSERT INTO account_payments SET
															patient_id = '$patient_id',
															encounter_id = '$encouter_id_ref',
															charge_list_id = '$charge_list_id',
															charge_list_detail_id = '$cId',
															payment_by='$paidBy',
															payment_method='$paymentMode',
															cc_type = '$creditCardCo',
															check_number = '$checkNo',
															cc_number = '$cCNo',
															cc_exp_date = '$expDate',
															ins_id ='$insCoId',
															payment_amount='$adjAmount',
															payment_date='$paidDate',
															operator_id='$operatorId',
															payment_code_id='$adj_code',
															payment_type='$paymentClaims',
															entered_date='$entered_date'
															";
										$insertAdjQry2 = imw_query($insertAdjStr2);
										
										if($overpayamount>0){
											if($adjAmount>=$overpayamount){
												$bal_adj_amt=$adjAmount-$overpayamount;
												$ovr_adj_amt=$overpayamount;
											}else{
												$ovr_adj_amt=$adjAmount;
												$bal_adj_amt=0;
											}
										}else{
												$bal_adj_amt=$adjAmount;
												$ovr_adj_amt=0;
										}
										$updateStr1 = "UPDATE patient_charge_list_details SET
														overPaymentForProc = overPaymentForProc - $ovr_adj_amt,
														newBalance = newBalance + $bal_adj_amt,
														balForProc = balForProc + $bal_adj_amt,
														paidForProc = paidForProc - $bal_adj_amt
														WHERE charge_list_detail_id='$cId'";
										$updateQry1 = imw_query($updateStr1);		
									}
								}
								
								// Write Off OR Discount
								if($paymentClaims=='Write Off' || $paymentClaims=='Discount'){
									$write_off_date = $deductibleDate;
									//---------------- GETTING BALANCE FOR WRITE OFF	
									$getBalForWriteOffStr = "SELECT * FROM patient_charge_list_details
															WHERE del_status='0' and charge_list_detail_id = '$cId'
															AND newBalance >= '$writeOffAmt'";
									$getBalForWriteOffQry = imw_query($getBalForWriteOffStr);
									$countBalExists = imw_num_rows($getBalForWriteOffQry);		
									if($paymentClaims=='Write Off'){
										$write_off_code_id=$write_off_code;
									}
									if($paymentClaims=='Discount'){
										$write_off_code_id=$discount_code;
									}	
									if($writeOffAmt>0 or $paidBy=="Insurance"){
										if($paidBy=="Insurance" && $insCoId>0){
											$pay_type="ins";
											$ins_type=$insSelected;
										}else{
											$pay_type="pat";
										}
										patient_proc_tx_update($cId,$writeOffAmt,$pay_type,$ins_type);
									}							
									//---------------- GETTING BALANCE FOR WRITE OFF
									if($writeOffAmt>0){
										$insertWriteOffStr = "INSERT INTO paymentswriteoff SET
																patient_id = '$patient_id',
																encounter_id = '$encouter_id_ref',
																charge_list_detail_id = '$cId',
																write_off_by_id = '$insCoId',
																write_off_amount = '$writeOffAmt',
																write_off_operator_id = '$operatorId',
																write_off_date = '$paidDate',
																paymentStatus = '$paymentClaims',
																write_off_code_id='$write_off_code_id',
																CAS_type = '$cas_type',
																CAS_code = '$cas_code',
																entered_date='$entered_date'";
										$insertWriteOffQry = imw_query($insertWriteOffStr);
										
										$dis_id=imw_insert_id();
										if($_REQUEST['cash_dis']=="yes"){
											$chld_cash= " ,cash_discount=$dis_id";
										}
										
										$updateStr = "UPDATE patient_charge_list_details SET
														newBalance = newBalance - $writeOffAmt,
														paidStatus = 'Paid',
														superBillUpdate = '1'
														$chld_cash
														WHERE charge_list_detail_id='$cId'";
										$updateQry = imw_query($updateStr);										
									}
								}
								// Deductible
								//$deductAmount = $_REQUEST['payNew'.$value];
								//$deductAmount = approvedAmtText($deductAmount);
								//echo $deductAmount_add;
									//exit;
								
																		
								if(($paymentClaims=='Deductible') && ($deductAmount>0)){
									if($deductAmount>0 or $paidBy=="Insurance"){
										if($paidBy=="Insurance" && $insCoId>0){
											$pay_type="ins";
											$ins_type=$insSelected;
										}else{
											$pay_type="pat";
										}
										patient_proc_tx_update($cId,0,$pay_type,$ins_type);
									}
									unset($arrayRecord);
									$arrayRecord['charge_list_detail_id'] = $cId;
									$arrayRecord['deduct_amount'] = $deductAmount;
									$arrayRecord['deductible_by'] = $paidBy;
									$arrayRecord['deduct_ins_id'] = $insCoId;
									$arrayRecord['deduct_operator_id'] = $operatorId;
									$arrayRecord['deduct_date'] = $paidDate;
									$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
									
									$insertDeductQry = addRecords_obj($arrayRecord, 'payment_deductible');
									
									$ded_fet_qry = imw_query("SELECT sum(deduct_amount) as deduct_amount  FROM payment_deductible
															WHERE charge_list_detail_id = '$cId'
															AND delete_deduct<> '1'");
									$fet_ded_row = imw_fetch_array($ded_fet_qry);
									$tot_deduct_amount=$fet_ded_row['deduct_amount'];
									
									unset($arrayRecord);
									$arrayRecord['deductAmt'] = $tot_deduct_amount;
									updateRecords_obj($arrayRecord, 'patient_charge_list_details', 'charge_list_detail_id', $cId);
									
								}else{
									$ded_fet_qry1 = imw_query("SELECT sum(deduct_amount) as deduct_amount  FROM payment_deductible
															WHERE charge_list_detail_id = '$cId'
															AND delete_deduct<> '1'");
									$fet_ded_row1 = imw_fetch_array($ded_fet_qry1);
									$tot_deduct_amount1=$fet_ded_row1['deduct_amount'];
									
									if($deductibleAmount>0 && $deductibleAmount>$tot_deduct_amount1){
										$final_deduct_amt=$deductibleAmount-$tot_deduct_amount1;
										unset($arrayRecord);
										$arrayRecord['charge_list_detail_id'] = $cId;
										$arrayRecord['deduct_amount'] = $final_deduct_amt;
										$arrayRecord['deductible_by'] = $paidBy;
										$arrayRecord['deduct_ins_id'] = $insCoId;
										$arrayRecord['deduct_operator_id'] = $operatorId;
										$arrayRecord['deduct_date'] = $paidDate;
										$arrayRecord['entered_date'] = date('Y-m-d H:i:s');
										$insertDeductQry = addRecords_obj($arrayRecord, 'payment_deductible');
										
										if($final_deduct_amt>0 or $paidBy=="Insurance"){
											if($paidBy=="Insurance" && $insCoId>0){
												$pay_type="ins";
												$ins_type=$insSelected;
											}else{
												$pay_type="pat";
											}
											patient_proc_tx_update($cId,0,$pay_type,$ins_type);
										}
										
										
										unset($arrayRecord);
										$arrayRecord['deductAmt'] = $deductibleAmount;
										updateRecords_obj($arrayRecord, 'patient_charge_list_details', 'charge_list_detail_id', $cId);
									}
								}
								//----------------------	Deductible
								
								//----------------------	Credit----Refund
								
								if($paymentClaims=='Refund' && $creditAmount>0){
									$sql3 ="SELECT cpt4_code FROM cpt_fee_tbl  WHERE cpt_fee_id = '$procCode' AND delete_status = '0'";
									$query3=imw_query($sql3);
									$row3=imw_fetch_array($query3);
									$cpt4_code=$row3['cpt4_code'];
									
									if($creditAmount<=$overpayamount){
										$insertWriteOffStr = "INSERT INTO creditapplied SET
										patient_id = '$patient_id',
										amountApplied = '$creditAmount',
										overpayamount = '$overpayamount',
										dos = '$dos',
										dateApplied = '$paidDate',
										operatorApplied = '$operatorId',
										crAppliedTo = 'payment',
										crAppliedToEncId = '$encouter_id_ref',
										type = '$paidBy',
										ins_case = '$insCoId',
										insCompany = '$insSelected',
										credit_note = '$credit_note',
										payment_mode = '$paymentMode',
										checkCcNumber = '$checkNo',
										creditCardNo = '$cCNo',
										creditCardCo = '$creditCardCo',
										expirationDateCc = '$expDate',
										cpt_code = '$cpt4_code',
										cpt_code_id = '$procCode',
										credit_applied='1',
										charge_list_detail_id = '$cId',
										entered_date='$entered_date'";
										$insertWriteOffQry = imw_query($insertWriteOffStr);
										//echo "<br>";
										$updateStr = "UPDATE patient_charge_list_details SET
															overPaymentForProc = overPaymentForProc - $creditAmount,
															creditProcAmount = creditProcAmount + $creditAmount,
															credits  = credits  + $creditAmount,
															creditDate  = '$paidDate'
															WHERE charge_list_detail_id='$cId'";
										$updateQry = imw_query($updateStr);		
										//echo "<br>";
										$updateStr1 = "UPDATE patient_charge_list SET
															overPayment = overPayment - $creditAmount,
															creditAmount  = creditAmount  + $creditAmount
															WHERE charge_list_id ='$charge_list_id'";
										$updateQry1 = imw_query($updateStr1);	
									}else{
										$final_paid_chk=$paidForProc+$overpayamount;
										if($final_paid_chk>=$creditAmount){
											$insertWriteOffStr = "INSERT INTO creditapplied SET
											patient_id = '$patient_id',
											amountApplied = '$creditAmount',
											overpayamount = '$overpayamount',
											dos = '$dos',
											dateApplied = '$paidDate',
											operatorApplied = '$operatorId',
											crAppliedTo = 'payment',
											crAppliedToEncId = '$encouter_id_ref',
											type = '$paidBy',
											ins_case = '$insCoId',
											insCompany = '$insSelected',
											credit_note = '$credit_note',
											payment_mode = '$paymentMode',
											checkCcNumber = '$checkNo',
											creditCardNo = '$cCNo',
											creditCardCo = '$creditCardCo',
											expirationDateCc = '$expDate',
											cpt_code = '$cpt4_code',
											cpt_code_id = '$procCode',
											credit_applied='1',
											charge_list_detail_id = '$cId',
											entered_date='$entered_date'";
											$insertWriteOffQry = imw_query($insertWriteOffStr);
										
											if($overpayamount>0){
												if($creditAmount>$overpayamount){
													$crd_ref_amt=$creditAmount-$overpayamount;
													$ovr_ref_amt=$overpayamount;
												}
											}else{
													$crd_ref_amt=$creditAmount;
													$ovr_ref_amt=0;
											}
										
											$updateStr = "UPDATE patient_charge_list_details SET
																overPaymentForProc = overPaymentForProc - $ovr_ref_amt,
																newBalance = newBalance + $crd_ref_amt,
																balForProc = balForProc + $crd_ref_amt,
																paidForProc = paidForProc - $crd_ref_amt,
																creditProcAmount = creditProcAmount + $creditAmount,
																credits  = credits  + $creditAmount,
																creditDate  = '$paidDate'
																WHERE charge_list_detail_id='$cId'";
											$updateQry = imw_query($updateStr);		
											
											$updateStr1 = "UPDATE patient_charge_list SET
																totalBalance = totalBalance + $creditAmount,
																amtPaid = amtPaid - $creditAmount,
																amountDue = amountDue + $creditAmount,
																creditAmount  = creditAmount  + $creditAmount
																WHERE charge_list_id ='$charge_list_id'";
											$updateQry1 = imw_query($updateStr1);	
										}	
									}
									if($_REQUEST['encounter_id_arr']<>""){
										//$encounter_id_arr=$_REQUEST['encounter_id_arr'];
									}
								}
								//exit;
								//----------------------	Credit----Debit_Credit Adustment
								$ins_crd_id="";
								if($paymentClaims=='Debit_Credit'){
									if($adjust_other=='other_procedure'){
										$app_deb_pat=$_REQUEST['app_deb_pat'];
										$app_deb_enc=$_REQUEST['app_deb_enc'];
										$app_deb_chld=$_REQUEST['app_deb_chld'];
										$app_deb_chl=$_REQUEST['app_deb_chl'];
										$app_deb_proc=$_REQUEST['app_deb_procCode'];
										$app_deb_ovr_paid=$_REQUEST['app_deb_ovr_paid'];
									}else{
										if($overpayamount>0){
											$app_deb_chld=$cId;
											$app_deb_chl=$charge_list_id;
											$app_deb_proc=$procCode;
											$app_deb_enc=$encouter_id_ref;
											$app_deb_pat=$patient_id;
											$app_deb_ovr_pay=$overpayamount;
										}
									}
									$encounter_id_arr[]=$app_deb_enc;
									$sql3 ="SELECT cpt4_code FROM cpt_fee_tbl  WHERE cpt_fee_id = '$app_deb_proc' AND delete_status = '0'";
									$query3=imw_query($sql3);
									$row3=imw_fetch_array($query3);
									$cpt4_code=$row3['cpt4_code'];
										if($adjust_other=='other_procedure'){
											if($_REQUEST['b_id']>0){
												$b_id=$_REQUEST['b_id'];
												if($creditAmount>0){
													$chk_creditAmount=$creditAmount;
													$insertWriteOffStr = "INSERT INTO manual_batch_creditapplied SET
													batch_id = '$b_id',
													patient_id = '$app_deb_pat',
													amountApplied = '$creditAmount',
													overpayamount = '$creditAmount',
													dos = '$dos',
													dateApplied = '$paidDate',
													operatorApplied = '$operatorId',
													crAppliedTo = 'adjustment',
													crAppliedToEncId = '$app_deb_enc',
													type = '$paidBy',
													ins_case = '$insCoId',
													insCompany = '$insSelected',
													credit_note = '$credit_note',
													cpt_code = '$cpt4_code',
													cpt_code_id = '$procCode',
													charge_list_detail_id = '$app_deb_chld',
													credit_applied='1'
													";
													$insertWriteOffQry = imw_query($insertWriteOffStr);
													
													$ins_crd_id=imw_insert_id();
												}
												if(($ins_crd_id>0)){
													$encounter_id_arr[]=$encouter_id_ref;
													$updateStrs = "UPDATE manual_batch_creditapplied SET
																	charge_list_detail_id_adjust='$cId',
																	crAppliedToEncId_adjust='$encouter_id_ref',
																	patient_id_adjust='$patient_id_adust'
																	WHERE crAppId ='$ins_crd_id'";
													$updateQrys = imw_query($updateStrs);
													
													$ins_batch="insert into manual_batch_transactions set 
													batch_id='$b_id',patient_id='$patient_id_adust',
													encounter_id='$encouter_id_ref',charge_list_id='$chargeListId',
													charge_list_detaill_id='$cId',trans_amt='0',
													insurance_id='$insCoId',ins_selected='$insSelected',
													proc_total_amt='$procCharges',
													proc_allow_amt='$approvedAmtText',trans_date='$paidDate',
													operator_id='$operatorId',payment_mode='$paymentMode',
													payment_claims='Debit_Credit',trans_by='$paidBy',del_status='$ins_crd_id'";
													imw_query($ins_batch);
												}
											}else{
												if($creditAmount>0){
													$app_deb_chld_arr=array();
													$app_deb_ovr_paid_arr=array();
													$app_deb_chld_arr=explode(',',$app_deb_chld);
													$app_deb_ovr_paid_arr=explode(',',$app_deb_ovr_paid);
													$app_deb_ovr_paid_req=array();
													for($k=0;$k<count($app_deb_chld_arr);$k++){
														$app_deb_chld_val=$app_deb_chld_arr[$k];
														$app_deb_ovr_paid_val=$app_deb_ovr_paid_arr[$k];
														//echo $creditAmount.'--'.$app_deb_ovr_paid_val.'--'.$_REQUEST['payNew'.$value].'--'.$app_deb_ovr_paid.'<br>';
														if($app_deb_chld_val>0){
															if($creditAmount>=$app_deb_ovr_paid_val){
																$amount_applied_deb=$app_deb_ovr_paid_val;
																$creditAmount=$creditAmount-$app_deb_ovr_paid_val;
																$chk_creditAmount=$app_deb_ovr_paid_val;
																$app_deb_ovr_paid_req[]=0;
																$_REQUEST['payNew'.$value]=$creditAmount;
															}else{
																$amount_applied_deb=$creditAmount;
																$chk_creditAmount=$creditAmount;
																$app_deb_ovr_paid_req[]=$app_deb_ovr_paid_val-$creditAmount;
																$_REQUEST['payNew'.$value]=0;
																$creditAmount=0;
															}
															if($amount_applied_deb>0){
																$insertWriteOffStr = "INSERT INTO creditapplied SET
																patient_id = '$app_deb_pat',
																amountApplied = '$amount_applied_deb',
																overpayamount = '$amount_applied_deb',
																dos = '$dos',
																dateApplied = '$paidDate',
																operatorApplied = '$operatorId',
																crAppliedTo = 'adjustment',
																crAppliedToEncId = '$app_deb_enc',
																type = '$paidBy',
																ins_case = '$insCoId',
																insCompany = '$insSelected',
																credit_note = '$credit_note',
																cpt_code = '$cpt4_code',
																cpt_code_id = '$procCode',
																charge_list_detail_id = '$app_deb_chld_val',
																credit_applied='1',entered_date='$entered_date'
																";
																$insertWriteOffQry = imw_query($insertWriteOffStr);
																
																$ins_crd_id=imw_insert_id();
																if($_REQUEST['app_deb_enc']>0){
																	$sel_chk_crd=imw_query("select overPaymentForProc from patient_charge_list_details
																				   where del_status='0' and charge_list_detail_id='$app_deb_chld_val'");
																	$fet_chk_crd=imw_fetch_array($sel_chk_crd);	
																	$deb_overpayment_chk=$fet_chk_crd['overPaymentForProc'];
																	if($amount_applied_deb>$deb_overpayment_chk){
																		$up_creditAmt=$amount_applied_deb-$deb_overpayment_chk;
																		$updateStr = "UPDATE patient_charge_list_details SET
																						newBalance = newBalance + $up_creditAmt,
																						balForProc = balForProc + $up_creditAmt,
																						paidForProc = paidForProc - $up_creditAmt,
																						overPaymentForProc = overPaymentForProc-$deb_overpayment_chk
																						WHERE charge_list_detail_id='$app_deb_chld_val'";
																		$updateQry = imw_query($updateStr);
																		
																		$updateStr1 = "UPDATE patient_charge_list SET
																							totalBalance = totalBalance + $up_creditAmt,
																							amountDue = amountDue + $up_creditAmt,
																							amtPaid = amtPaid - $up_creditAmt,
																							overPayment = overPayment - $deb_overpayment_chk
																							WHERE charge_list_id ='$app_deb_chld_val'";
																		$updateQry1 = imw_query($updateStr1);
																	}else{
																		$updateStr = "UPDATE patient_charge_list_details SET
																						overPaymentForProc = overPaymentForProc - $amount_applied_deb
																						WHERE charge_list_detail_id='$app_deb_chld_val'";
																		$updateQry = imw_query($updateStr);		
																		
																		if($ovr_paid_ref>=$amount_applied_deb){
																			$ovr_paid_min=$amount_applied_deb;
																		}else{
																			$ovr_paid_min=$ovr_paid_ref;
																		}
																		$updateStr1 = "UPDATE patient_charge_list SET
																							overPayment = overPayment - $ovr_paid_min
																							WHERE charge_list_id ='$app_deb_chl'";
																		$updateQry1 = imw_query($updateStr1);	
																	}
																}else{
																		$updateStr = "UPDATE patient_charge_list_details SET
																							overPaymentForProc = overPaymentForProc - $amount_applied_deb
																							WHERE charge_list_detail_id='$app_deb_chld_val'";
																		$updateQry = imw_query($updateStr);		
																		
																		if($ovr_paid_ref>=$amount_applied_deb){
																			$ovr_paid_min=$amount_applied_deb;
																		}else{
																			$ovr_paid_min=$ovr_paid_ref;
																		}
																		$updateStr1 = "UPDATE patient_charge_list SET
																							overPayment = overPayment - $ovr_paid_min
																							WHERE charge_list_id ='$app_deb_chl'";
																		$updateQry1 = imw_query($updateStr1);	
																}
															
																if(($ins_crd_id>0)){
																	$encounter_id_arr[]=$encouter_id_ref;
																	$updateStrs = "UPDATE creditapplied SET
																						charge_list_detail_id_adjust='$cId',
																						crAppliedToEncId_adjust='$encouter_id_ref',
																						patient_id_adjust='$patient_id_adust'
																						WHERE crAppId ='$ins_crd_id'";
																	$updateQrys = imw_query($updateStrs);
																	
																	$updateStr2 = "UPDATE patient_charge_list_details SET
																						newBalance = newBalance - $chk_creditAmount,
																						balForProc = balForProc - $chk_creditAmount,
																						paidForProc = paidForProc + $chk_creditAmount,
																						creditProcAmount = creditProcAmount + $chk_creditAmount,
																						credits  = credits  + $chk_creditAmount,
																						creditDate  = '$paidDate'
																						WHERE charge_list_detail_id='$cId'";
																	$updateQry2 = imw_query($updateStr2);		
																	
																	
																	$updateStr4 = "UPDATE patient_charge_list SET
																						totalBalance = totalBalance - $chk_creditAmount,
																						amtPaid = amtPaid + $chk_creditAmount,
																						amountDue = amountDue - $chk_creditAmount,
																						creditAmount  = creditAmount  + $chk_creditAmount
																						WHERE charge_list_id ='$charge_list_id'";
																						
																	$updateQry4 = imw_query($updateStr4);
																	
																	if($chk_creditAmount>0 or $paidBy=="Insurance"){
																		if($paidBy=="Insurance" && $insCoId>0){
																			$pay_type="ins";
																			$ins_type=$insSelected;
																		}else{
																			$pay_type="pat";
																		}
																		patient_proc_tx_update($cId,$chk_creditAmount,$pay_type,$ins_type);
																	}
																}
															}
														}
													}
													$_REQUEST['app_deb_ovr_paid']=implode(',',$app_deb_ovr_paid_req);
												}
												
											}
										}else{
										$up_deb_enc="no";
										if($creditAmount>0 && $overpayamount<=0){
											$encounter_id_arr[]=$app_deb_enc;
											$chk_creditAmount=$creditAmount;
											$insertWriteOffStr = "INSERT INTO creditapplied SET
											amountApplied = '$creditAmount',
											overpayamount = '$creditAmount',
											dos = '$dos',
											dateApplied = '$paidDate',
											operatorApplied = '$operatorId',
											crAppliedTo = 'adjustment',
											type = '$paidBy',
											ins_case = '$insCoId',
											insCompany = '$insSelected',
											credit_note = '$credit_note',
											credit_applied='1',
											entered_date='$entered_date'
											";
											$insertWriteOffQry = imw_query($insertWriteOffStr);
											
											$ins_crd_id=imw_insert_id();
											
											$ins_crd_id_arr[]=$ins_crd_id;
											$up_deb_enc="yes";
											
										}
										if(($ins_crd_id>0) && $up_deb_enc=='yes'){
											$encounter_id_arr[]=$encouter_id_ref;
											$updateStrs = "UPDATE creditapplied SET
																charge_list_detail_id_adjust='$cId',
																crAppliedToEncId_adjust='$encouter_id_ref',
																patient_id_adjust='$patient_id_adust'
																WHERE crAppId ='$ins_crd_id'";
											$updateQrys = imw_query($updateStrs);
											
											$updateStr2 = "UPDATE patient_charge_list_details SET
																newBalance = newBalance - $chk_creditAmount,
																balForProc = balForProc - $chk_creditAmount,
																paidForProc = paidForProc + $chk_creditAmount,
																creditProcAmount = creditProcAmount + $chk_creditAmount,
																credits  = credits  + $chk_creditAmount,
																creditDate  = '$paidDate'
																WHERE charge_list_detail_id='$cId'";
											$updateQry2 = imw_query($updateStr2);		
											
											
											$updateStr4 = "UPDATE patient_charge_list SET
																totalBalance = totalBalance - $chk_creditAmount,
																amtPaid = amtPaid + $chk_creditAmount,
																amountDue = amountDue - $chk_creditAmount,
																creditAmount  = creditAmount  + $chk_creditAmount
																WHERE charge_list_id ='$charge_list_id'";
																/*patientPaidAmt = patientPaidAmt + $creditAmount,
																patientDue = patientDue -  $creditAmount*/
											$updateQry4 = imw_query($updateStr4);
											
											if($chk_creditAmount>0 or $paidBy=="Insurance"){
												if($paidBy=="Insurance" && $insCoId>0){
													$pay_type="ins";
													$ins_type=$insSelected;
												}else{
													$pay_type="pat";
												}
												patient_proc_tx_update($cId,$chk_creditAmount,$pay_type,$ins_type);
											}
										}
										$ins_crd_id_imp="";
										$ins_crd_id_imp=@implode(',',$ins_crd_id_arr);
										if($ins_crd_id_arr>0 && $app_deb_chld>0 && $app_deb_ovr_pay>0){
											
											$updateStr = "UPDATE patient_charge_list_details SET
																overPaymentForProc = overPaymentForProc - $creditAmount
																WHERE charge_list_detail_id='$app_deb_chld'";
											$updateQry = imw_query($updateStr);		
											
											$updateStr1 = "UPDATE patient_charge_list SET
																overPayment = overPayment - $creditAmount
																WHERE charge_list_id ='$app_deb_chl'";
											$updateQry1 = imw_query($updateStr1);	
											if($encouter_id_ref<>""){
												$app_deb_enc=$encouter_id_ref;
											}
											$app_deb_ovr_pay="";
											
										}
										if($ins_crd_id_arr>0){
											$insertcrd_chld_up = "update  creditapplied SET
											patient_id = '$app_deb_pat',
											crAppliedToEncId = '$app_deb_enc',
											charge_list_detail_id = '$app_deb_chld',
											cpt_code = '$cpt4_code',
											cpt_code_id = '$app_deb_proc'
											where crAppId in($ins_crd_id_imp);
											";
											$insertcrd_chld_qry = imw_query($insertcrd_chld_up);	
										}	
									
									}	
									if($_REQUEST['encounter_id_arr']<>""){
										//$encounter_id_arr=$_REQUEST['encounter_id_arr'];
										//$encounter_id_arr[]=$app_deb_enc;
									}
									//in_array(
								}
									//----------------------	Credit----Debit_Credit Adustment
								  if($paymentClaims=='Negative Payment'){
									
									unset($arrayRecord);
									$arrayRecord['encounter_id'] = $encounter_id;
									$arrayRecord['paid_by'] = $paidBy;
									$arrayRecord['payment_amount'] = $payNegtiveAmount;
									$arrayRecord['payment_mode'] = $paymentMode;
									$arrayRecord['checkNo'] = $checkNo;
									$arrayRecord['creditCardNo'] = $cCNo;
									$arrayRecord['creditCardCo'] = $creditCardCo;
									$arrayRecord['date_of_payment'] = $paidDate;
									$arrayRecord['expirationDate'] = $expDate;
									$arrayRecord['operatorId'] = $operatorId;
									$arrayRecord['insProviderId'] = $insCoId;
									$arrayRecord['insCompany'] = $insSelected;
									$arrayRecord['paymentClaims'] = $paymentClaims;
									$arrayRecord['transaction_date'] = $transactionDate;
									$arrayRecord['statement_pmt'] = $statement_pmt;
									$addPaymentId = addRecords_obj($arrayRecord, 'patient_chargesheet_payment_info');
									
									unset($arrayRecord);
									//INSERT DETAIL PAYMENT
									$arrayRecord['payment_id'] = $addPaymentId;
									$arrayRecord['charge_list_detail_id'] = $cId;
									$arrayRecord['paidBy'] = $paidBy;
									$arrayRecord['paidDate'] = $paidDate;
									$arrayRecord['paidForProc'] = $payNegtiveAmount;
									$arrayRecord['operator_id'] = $_SESSION['authId'];
									$arrayRecord['entered_date'] = date('Y-m-d H:i:s'); 
									$addDetailPayments = addRecords_obj($arrayRecord, 'patient_charges_detail_payment_info');									
									//	START UPDATE PATIENT CHARGE LIST DETAIL TABLE
								}	
							}
						}
					}
				}
			}
		}
	}
	//exit();
	if(($coPayChk == 'true') && (($paymentClaims=='Write Off') || ($paymentClaims=='Discount'))){
		$paidDate = $_REQUEST['paidDate'];
			list($month, $day, $year)=explode("-", $paidDate);
			$coPay_write_off_date = $year."-".$month."-".$day;
		$insCoId = $_REQUEST['insProviderName'];
		//----------------- COPAY NOT REQ.
		$updateCoPayNRStr = "UPDATE patient_charge_list SET
							coPayWriteOff = '1',
							coPayWriteOffDate = '$coPay_write_off_date'
							WHERE encounter_id = '$encounter_id'";
		$updateCoPayNRQry = imw_query($updateCoPayNRStr);	
		// COPAY WRITEOFF
		if($paymentClaims=='Write Off'){
			$write_off_code_id=$write_off_code;
		}	
		if($paymentClaims=='Discount'){
			$write_off_code_id=$discount_code;
		}
		$insertWriteOffStr = "INSERT INTO paymentswriteoff SET
								patient_id = '$patient_id',
								encounter_id = '$encounter_id',
								write_off_by_id = '$insCoId',
								write_off_amount = '$copayAmount',
								write_off_operator_id = '$operatorId',
								paymentStatus = '$paymentClaims',
								write_off_code_id='$write_off_code_id',
								write_off_date = '$coPay_write_off_date',
								entered_date='$entered_date'";
		$insertWriteOffQry = imw_query($insertWriteOffStr);
	}
	//print_r($encounter_id_arr);
	//exit;
	if($_REQUEST['b_id']>0){
	}else{
		if($app_deb_enc<>""){
			$encouter_id_ref_chk[]=$app_deb_enc;
		}
		$encounter_id_arr=@array_intersect($encouter_id_ref_chk,$encounter_id_arr);
		//print_r($encouter_id_ref_chk);
		include("manageEncounterAmounts.php");
	}
}	
if($adjust_other=='other_procedure'){
?>
	<script>
		if(top.ref_win){
			top.ref_win();
		}
	</script>
<?php
}else{
?>
<script>
	var eId = '<?php echo $encounter_id; ?>';
	var eId_all = '<?php echo implode(',',$encounter_id_arr); ?>';
	<?php if($apply=='applyRecieptSubmit'){ ?>
		window.open("receipt.php?eId="+eId_all,'','width=1000,height=675,top=10,left=40,scrollbars=yes,resizable=yes');
	<?php } ?>
	top.fmain.location.href="makePayment.php?encounter_id="+eId;
</script>
<?php } ?>