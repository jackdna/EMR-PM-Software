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
$global_date_Format = phpDateFormat();
$operatorId = $_SESSION['authUserID'];
$b_id = $_REQUEST['b_id'];
$encounter_id = $_REQUEST['encounter_id'];
$patient_id = $_REQUEST['patient_id'];
$chargeListId=$_REQUEST['chargeListId'];
$insCoId = $_REQUEST['insProviderName'];
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
if($_REQUEST['paymentClaims']=="Write Off"){
	$write_off_code=$_REQUEST['write_off_code'];
}else{
	$write_off_code=$_REQUEST['write_off_code'];
}
$adj_code=$_REQUEST['adj_code'];
$apply = $_REQUEST['apply'];
$chkbxArr = $_REQUEST['chkbx'];
$counterIds = $_REQUEST['counterIdArr'];
$chargeListDetailIds = $_REQUEST['chargeListDetailIdArr'];
$insSelected=$_REQUEST['insSelected'];
$paidAmountPaying = $_REQUEST['paidAmount'];
$paidAmountPaying = approvedAmtText($paidAmountPaying);

$paidDate = $_REQUEST['paidDate'];
if($global_date_Format == "d-m-Y")
{
	list($day, $month, $year)=explode("-", $paidDate);
}
else
{
	list($month, $day, $year)=explode("-", $paidDate);
}
$paidDate = $year."-".$month."-".$day;
$paymentClaims = $_REQUEST['paymentClaims'];
$cas_code = $_REQUEST['cas_code'];
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

if($_REQUEST['commentsArea']){
	$commentsDate = date('Y-m-d');
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
									c_type='batch',
									encCommentsOperatorId = '$operatorId'";
		$insertCommentsQry = imw_query($insertCommentsStr);
	}
}
if($paymentMode=='Check' || $paymentMode=='EFT' || $paymentMode=='Money Order' || $paymentMode=='VEEP'){
	$sel_chk=imw_query("select default_check_no from manual_batch_file where batch_id='$b_id'");
	$row_chk=imw_fetch_array($sel_chk);
	$chk_arr=explode(",",$row_chk['default_check_no']);
	$chk_arr[]=$checkNo;
	$chk_imp=implode(",",array_unique($chk_arr));
	$up_batch_chk="update  manual_batch_file set default_check_no='$chk_imp' where batch_id='$b_id'";
	imw_query($up_batch_chk);
}
if($paymentClaims){
	
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
								
								$ProcChargeText_val = $_REQUEST['proc_total_amt'.$value];
									$ProcChargeText_val = approvedAmtText($ProcChargeText_val);
									
								$ProcUnitText_val = $_REQUEST['proc_unit'.$value];
									$ProcUnitText_val = approvedAmtText($ProcUnitText_val);
									
								if($_REQUEST['paymentClaims']=="Update Charges"){	
									if($ProcUnitText_val>0){
										$SingleProcCharge=$ProcChargeText_val/$ProcUnitText_val;
										$edit_proc_charges=round($SingleProcCharge,2);
										$edit_proc_total_charges=$edit_proc_charges*$ProcUnitText_val;
									}else{
										$edit_proc_charges=$ProcChargeText_val;
										$edit_proc_total_charges=$ProcChargeText_val;
									}
									$modifier_on=date('Y-m-d h:i:s a');
									$reason_arr=array();
									$reason_arr['charge_list_detail_id']=$cId;
									$reason_arr['encounter_id']=$encounter_id;
									$reason_arr['procCharges']=$edit_proc_charges;
									$reason_arr['totalAmount']=$edit_proc_charges;
									$reason_arr['approvedAmt']=$approvedAmtText;
									$reason=serialize($reason_arr);
									imw_query("update patient_charge_list_details set procCharges='$edit_proc_charges',totalAmount='$edit_proc_total_charges',approvedAmt='$approvedAmtText' where charge_list_detail_id='$cId'");
									imw_query("insert into patient_charge_list_modifiy set enc_id='$encounter_id',modifier_by='$operatorId',modifier_on='$modifier_on',reason='$reason'");
									imw_query("update manual_batch_transactions set del_status='1' where charge_list_detaill_id='$cId' and  post_status='0' and  payment_claims='Allowed'");
								}else{
									if($_REQUEST['paymentClaims']=="Write Off"){
										$write_off_codetNow = $_REQUEST['write_off_code'.$value];
									}else{
										//$write_off_codetNow=0;
										$write_off_codetNow = $_REQUEST['write_off_code'.$value];
	
									}
									
									$proc_total_amt_val = $_REQUEST['proc_total_amt'.$value];
										$proc_total_amt_val = approvedAmtText($proc_total_amt_val);
									
									$app_amt_chld =	 $_REQUEST['app_amt_chld'.$value];
									
									if($payAmountText_val>0 && $paymentClaims=='Deductible'){
										$payAmountText_val=$payAmountText_val;
									}else if($paymentClaims=='Deductible' && $deductibleAmtText>0){
										$ded_fet_qry1 = imw_query("SELECT sum(deduct_amount) as deduct_amount  
																	FROM payment_deductible
																	WHERE charge_list_detail_id = '$cId'
																	AND delete_deduct<> '1'");
										$fet_ded_row1 = imw_fetch_array($ded_fet_qry1);
										$tot_deduct_amount1=$fet_ded_row1['deduct_amount'];
										
										$ded_fet_qry2 = imw_query("SELECT sum(trans_amt) as trans_amt_deduct  
																	FROM manual_batch_transactions
																	WHERE charge_list_detaill_id = '$cId'
																	and payment_claims='Deductible'
																	AND del_status <> '1'");
										$fet_ded_row2 = imw_fetch_array($ded_fet_qry2);
										$tot_deduct_amount2=$fet_ded_row2['trans_amt_deduct'];
										
										$final_deduct_amt_minus=$tot_deduct_amount1+$tot_deduct_amount2;
										if($deductibleAmtText>0 && $deductibleAmtText>$final_deduct_amt_minus){
											$payAmountText_val=$deductibleAmtText-$final_deduct_amt_minus;
										}
									}
									
									if($paymentMode!='Check' && $paymentMode!='EFT' && $paymentMode!='Money Order' && $paymentMode!='VEEP'){
										$checkNo="";
									}
									if($paymentClaims=="Discount" || $paymentClaims=="Denied" || $paymentClaims=="Deductible" || $paymentClaims=="Write Off"){
										$paymentMode="Cash";
										$checkNo="";
										$expDate="";
										$cCNo="";
										$creditCardCo="";
									}
									if($paidBy=='Patient'){
										$insCoId="";
									}
									if($paymentClaims!="Write Off"){
										$write_off_codetNow=0;
										$write_off_code=0;
									}
									$allowed_batch_amt=$proc_total_amt_val-$approvedAmtText;
									$chk_allow_amt=imw_query("select write_off_code_id from 
																manual_batch_transactions
																where batch_id='$b_id' and
																charge_list_detaill_id='$cId'
																and payment_claims='Allowed' and del_status='0'");
									$num_allow=imw_num_rows($chk_allow_amt);		
									if($allowed_batch_amt>0 && $num_allow==0 && $app_amt_chld!=$approvedAmtText){
										$ins_batch_allow="insert into manual_batch_transactions set 
										batch_id='$b_id',patient_id='$patient_id',
										encounter_id='$encounter_id',charge_list_id='$chargeListId',
										charge_list_detaill_id='$cId',trans_amt='$allowed_batch_amt',
										insurance_id='$insCoId',ins_selected='$insSelected',
										proc_total_amt='$proc_total_amt_val',
										proc_allow_amt='$approvedAmtText',trans_date='$paidDate',
										operator_id='$operatorId',payment_mode='$paymentMode',
										check_no='$checkNo',credit_card_type='$creditCardCo',
										credit_card_no='$cCNo',credit_card_exp='$expDate',
										payment_claims='Allowed',trans_by='$paidBy',
										write_off_code_id='$write_off_codetNow',
										adj_code_id='$adj_code',cas_type='$cas_type',cas_code='$cas_code'";
										imw_query($ins_batch_allow);
									}else{
										if($num_allow>0){
											$up_batch_allow="update  manual_batch_transactions set 
											trans_amt='$allowed_batch_amt',
											insurance_id='$insCoId',ins_selected='$insSelected',
											proc_total_amt='$proc_total_amt_val',
											proc_allow_amt='$approvedAmtText',trans_date='$paidDate',
											operator_id='$operatorId',payment_mode='$paymentMode',
											check_no='$checkNo',credit_card_type='$creditCardCo',
											credit_card_no='$cCNo',credit_card_exp='$expDate',
											payment_claims='Allowed',trans_by='$paidBy',
											write_off_code_id='$write_off_codetNow',
											adj_code_id='$adj_code',cas_type='$cas_type',cas_code='$cas_code'
											where 
											batch_id='$b_id' and charge_list_detaill_id='$cId'
											and trans_amt!=$allowed_batch_amt 
											and payment_claims='Allowed'";
											imw_query($up_batch_allow);
										}
									}
									
									$getChargeDertailsStr = "SELECT procCode FROM patient_charge_list_details 
															WHERE del_status='0' and charge_list_detail_id='$cId'";
									$getChargeDertailsQry = imw_query($getChargeDertailsStr);
									$getChargeDertailsRow = imw_fetch_assoc($getChargeDertailsQry);
										$charge_list_id = $getChargeDertailsRow['charge_list_id'];
										$procCode = $getChargeDertailsRow['procCode'];
										
									if($overPaymentAmt>0){
										$app_crd_chld=$cId;
										$app_deb_enc=$encounter_id;
										$app_deb_proc=$procCode;
										$app_crd_chld=$cId;
										$app_proc_amt_chld=$proc_total_amt_val;
										$app_proc_allow_amt_chld=$approvedAmtText;
									}
									if($_REQUEST['paymentClaims']=="Update Allow Amt"){
									}else{
										if($paymentClaims=='Debit_Credit'){
											$sql3 ="SELECT cpt4_code FROM cpt_fee_tbl WHERE cpt_fee_id = '$app_deb_proc' AND delete_status = '0'";
											$query3=imw_query($sql3);
											$row3=imw_fetch_array($query3);
											$cpt4_code=$row3['cpt4_code'];
											if($adjust_other=='other_procedure'){
											}else{
												if($overPaymentAmt<=0){
													$insertWriteOffStr = "INSERT INTO manual_batch_creditapplied SET
													patient_id = '$patient_id',
													batch_id   =  '$b_id',
													amountApplied = '$payAmountText_val',
													overpayamount = '$payAmountText_val',
													dos = '$dos',
													dateApplied = '$paidDate',
													operatorApplied = '$operatorId',
													crAppliedTo = 'adjustment',
													crAppliedToEncId = '$encounter_id',
													type = '$paidBy',
													ins_case = '$insCoId',
													insCompany = '$insSelected',
													credit_note = '$credit_note',
													patient_id_adjust='$patient_id',
													crAppliedToEncId_adjust = '$encounter_id',
													charge_list_detail_id_adjust='$cId',
													cpt_code = '$cpt4_code',
													cpt_code_id = '$app_deb_proc',
													credit_applied='1'";
													$insertWriteOffQry = imw_query($insertWriteOffStr);
													$ins_crd_id=imw_insert_id();
													$ins_crd_id_arr[]=$ins_crd_id;
													
													$ins_batch="insert into manual_batch_transactions set 
													batch_id='$b_id',patient_id='$patient_id',
													encounter_id='$encounter_id',charge_list_id='$chargeListId',
													charge_list_detaill_id='$cId',trans_amt='0',
													insurance_id='$insCoId',ins_selected='$insSelected',
													proc_total_amt='$proc_total_amt_val',
													proc_allow_amt='$approvedAmtText',trans_date='$paidDate',
													operator_id='$operatorId',payment_mode='$paymentMode',
													check_no='$checkNo',credit_card_type='$creditCardCo',
													credit_card_no='$cCNo',credit_card_exp='$expDate',
													payment_claims='$paymentClaims',trans_by='$paidBy',del_status='$ins_crd_id',
													cas_type='$cas_type',cas_code='$cas_code'";
													imw_query($ins_batch);
												}
												$ins_crd_id_imp="";
												$ins_crd_id_imp=@implode(',',$ins_crd_id_arr);
												if(count($ins_crd_id_arr)>0){
													$updateStrs = "UPDATE manual_batch_creditapplied SET
																	charge_list_detail_id = '$app_crd_chld'
																	WHERE crAppId in($ins_crd_id_imp)";
													$updateQrys = imw_query($updateStrs);
													
													$ins_batch="insert into manual_batch_transactions set 
													batch_id='$b_id',patient_id='$patient_id',
													encounter_id='$encounter_id',charge_list_id='$chargeListId',
													charge_list_detaill_id='$app_crd_chld',trans_amt='0',
													insurance_id='$insCoId',ins_selected='$insSelected',
													proc_total_amt='$app_proc_amt_chld',
													proc_allow_amt='$app_proc_allow_amt_chld',trans_date='$paidDate',
													operator_id='$operatorId',payment_mode='$paymentMode',
													check_no='$checkNo',credit_card_type='$creditCardCo',
													credit_card_no='$cCNo',credit_card_exp='$expDate',
													payment_claims='$paymentClaims',trans_by='$paidBy',del_status='$ins_crd_id',
													cas_type='$cas_type',cas_code='$cas_code'";
													imw_query($ins_batch);
												}
											}
										}else if($paymentClaims=='Refund'){
											$sql3 ="SELECT cpt4_code FROM cpt_fee_tbl WHERE cpt_fee_id = '$app_deb_proc' AND delete_status = '0'";
											$query3=imw_query($sql3);
											$row3=imw_fetch_array($query3);
											$cpt4_code=$row3['cpt4_code'];
											$insertRefundOffStr = "INSERT INTO manual_batch_creditapplied SET
											patient_id = '$patient_id',
											batch_id   =  '$b_id',
											amountApplied = '$payAmountText_val',
											overpayamount = '',
											dos = '$dos',
											dateApplied = '$paidDate',
											operatorApplied = '$operatorId',
											crAppliedTo = 'payment',
											crAppliedToEncId = '$encounter_id',
											charge_list_detail_id='$cId',
											type = '$paidBy',
											ins_case = '$insCoId',
											insCompany = '$insSelected',
											credit_note = '$credit_note',
											cpt_code = '$cpt4_code',
											cpt_code_id = '$app_deb_proc',
											credit_applied='1'";
											
											$insertRefundOffary = imw_query($insertRefundOffStr);
											$ins_ref_id=imw_insert_id();
											
											$ins_batch="insert into manual_batch_transactions set 
											batch_id='$b_id',patient_id='$patient_id',
											encounter_id='$encounter_id',charge_list_id='$chargeListId',
											charge_list_detaill_id='$cId',trans_amt='0',
											insurance_id='$insCoId',ins_selected='$insSelected',
											proc_total_amt='$proc_total_amt_val',
											proc_allow_amt='$approvedAmtText',trans_date='$paidDate',
											operator_id='$operatorId',payment_mode='$paymentMode',
											check_no='$checkNo',credit_card_type='$creditCardCo',
											credit_card_no='$cCNo',credit_card_exp='$expDate',
											payment_claims='$paymentClaims',trans_by='$paidBy',del_status='$ins_ref_id',
											cas_type='$cas_type',cas_code='$cas_code'";
											imw_query($ins_batch);
										}else{
											if($payAmountText_val>0){
												$ins_batch="insert into manual_batch_transactions set 
												batch_id='$b_id',patient_id='$patient_id',
												encounter_id='$encounter_id',charge_list_id='$chargeListId',
												charge_list_detaill_id='$cId',trans_amt='$payAmountText_val',
												insurance_id='$insCoId',ins_selected='$insSelected',
												proc_total_amt='$proc_total_amt_val',
												proc_allow_amt='$approvedAmtText',trans_date='$paidDate',
												operator_id='$operatorId',payment_mode='$paymentMode',
												check_no='$checkNo',credit_card_type='$creditCardCo',
												credit_card_no='$cCNo',credit_card_exp='$expDate',
												payment_claims='$paymentClaims',trans_by='$paidBy',
												write_off_code_id='$write_off_code',
												adj_code_id='$adj_code',cas_type='$cas_type',cas_code='$cas_code'";
												imw_query($ins_batch);
											}else{
												if(($payAmountText_val =='0.00' || $payAmountText_val =='0') && $insCoId>0 && strtolower($paymentClaims)=="paid"){
													$ins_batch="insert into manual_batch_transactions set 
													batch_id='$b_id',patient_id='$patient_id',
													encounter_id='$encounter_id',charge_list_id='$chargeListId',
													charge_list_detaill_id='$cId',trans_amt='$payAmountText_val',
													insurance_id='$insCoId',ins_selected='$insSelected',
													proc_total_amt='$proc_total_amt_val',
													proc_allow_amt='$approvedAmtText',trans_date='$paidDate',
													operator_id='$operatorId',payment_mode='$paymentMode',
													check_no='$checkNo',credit_card_type='$creditCardCo',
													credit_card_no='$cCNo',credit_card_exp='$expDate',
													payment_claims='$paymentClaims',trans_by='$paidBy',
													write_off_code_id='$write_off_code',
													adj_code_id='$adj_code',cas_type='$cas_type',cas_code='$cas_code'";
													imw_query($ins_batch);
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}										
}

if($_REQUEST['paymentClaims']=="Update Charges"){	
	patient_proc_bal_update($encounter_id);
	include("../accounting/manageEncounterAmounts.php");	
}
echo "<Script>window.location.href='batch_transactions.php?enc_id=$encounter_id&b_id=$b_id'</script>";
?>
