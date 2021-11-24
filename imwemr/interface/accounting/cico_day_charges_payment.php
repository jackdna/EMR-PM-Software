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
include_once(dirname(__FILE__)."/acc_header.php"); 
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
$operatorId = $_SESSION['authUserID'];
$operator_id = $_SESSION['authId'];
$entered_date = date('Y-m-d H:i:s');
$total_cico_post_amt_arr=array();
$chk_cico_paid_chld_arr=array();
if($check_in_out_paid>0 && $cico_chld_id>0){
	$qry_check_in_out_post = imw_query("SELECT c.paidForProc,c.overPayment,
							b.check_in_out_payment_detail_id	
							 FROM 
							patient_chargesheet_payment_info as a,
							check_in_out_payment_post as b,
							patient_charges_detail_payment_info as c
							WHERE 
							a.payment_id=b.acc_payment_id 
							and a.payment_id=c.payment_id
							and b.patient_id='$dc_patient_id'
							and b.check_in_out_payment_id in($cico_payment_id)
							and c.deletePayment ='0'
							and b.status='0'
							and a.unapply='0'");
	while($get_check_in_out_post = imw_fetch_array($qry_check_in_out_post)){
		$cico_paid_detail_id=$get_check_in_out_post['check_in_out_payment_detail_id'];
		$total_cico_post_amt_arr[$cico_paid_detail_id][] = $get_check_in_out_post['overPayment']+$get_check_in_out_post['paidForProc'];
	}
	
	$cico_ref_arr = array();
	$sql_chk_ref = imw_query("select cpt_fee_id from cpt_fee_tbl where cpt4_code='92015' or cpt_prac_code='92015' or cpt_desc='92015'
								or cpt4_code='refraction' or cpt_prac_code='refraction' or cpt_desc='refraction'");
	while($cico_fet_ref=imw_fetch_array($sql_chk_ref)){
		$cico_ref_arr[]=$cico_fet_ref['cpt_fee_id'];
	}
	$last_paid_tot_amt_neg=0;
	$chld_detail_cico_tot_neg = imw_query("select paidForProc from patient_charge_list_details where del_status='0' and charge_list_detail_id='$cico_chld_id' and paidForProc<0");
	$chld_detail_fet_cico_tot_neg=imw_fetch_array($chld_detail_cico_tot_neg);
	$last_paid_tot_amt_neg=$chld_detail_fet_cico_tot_neg['paidForProc'];
	
	$sel_ref_qry=imw_query("select ci_pmt_ref.ci_co_id,ci_pmt_ref.ref_amt from ci_pmt_ref 
		join check_in_out_payment_details on ci_pmt_ref.ci_co_id=check_in_out_payment_details.id
		join check_in_out_payment on check_in_out_payment.payment_id=check_in_out_payment_details.payment_id
		 where ci_pmt_ref.patient_id='$dc_patient_id' and ci_pmt_ref.del_status='0' 
		 and check_in_out_payment.payment_id in($cico_payment_id) and ci_pmt_ref.ci_co_id>0");
	while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
		if($sel_ref_row['ci_co_id']>0){
			$total_cico_post_amt_arr[$sel_ref_row['ci_co_id']][]=$sel_ref_row['ref_amt'];
		}
	}
	
	$cico_main_qry="select check_in_out_payment_details.id as cico_pay_detail_id,
					check_in_out_payment_details.item_payment as cico_item_payment,
					check_in_out_payment.payment_method,check_in_out_payment.check_no,
					check_in_out_payment.cc_type,check_in_out_payment.cc_no,
					check_in_out_payment.cc_expire_date,
					check_in_out_payment.cc_expire_date,
					check_in_out_payment.payment_id,
					check_in_out_payment.created_on,
					check_in_out_fields.item_name,
					check_in_out_payment.sch_id
						from 
					check_in_out_payment join check_in_out_payment_details on 
					check_in_out_payment.payment_id=check_in_out_payment_details.payment_id
					join check_in_out_fields on check_in_out_fields.id = check_in_out_payment_details.item_id
					where
					check_in_out_payment.payment_id in($cico_payment_id)
					and  check_in_out_payment_details.item_payment>0
					and  check_in_out_payment_details.status='0'";
	$cico_main_run=imw_query($cico_main_qry);
	while($cico_main_fet=imw_fetch_array($cico_main_run)){
		$un_cico_post_amount_chk=0;
		$total_cico_post_amt=0;
		$cico_payment_id_fet=$cico_main_fet['payment_id'];
		$cico_pay_detail_id=$cico_main_fet['cico_pay_detail_id'];	
		$total_cico_post_amt=array_sum($total_cico_post_amt_arr[$cico_pay_detail_id]);
		$cico_item_payment=$cico_main_fet['cico_item_payment'];
		$un_cico_post_amount_chk=$cico_item_payment-$total_cico_post_amt;
		
		$cico_payment_method = $cico_main_fet['payment_method'];
		$cico_check_no		 = $cico_main_fet['check_no'];
		$cico_cc_type 		 = $cico_main_fet['cc_type'];
		$cico_cc_no  		 = $cico_main_fet['cc_no'];
		$cico_cc_expire_date = $cico_main_fet['cc_expire_date'];
		
		$chld_detail_cico = imw_query("select newBalance,charge_list_detail_id,charge_list_id,procCode
							from patient_charge_list_details where del_status='0' and charge_list_detail_id='$cico_chld_id'");
		$chld_detail_fet_cico=imw_fetch_array($chld_detail_cico);
		
		$cico_chld_newBalance  = $chld_detail_fet_cico['newBalance'];
		$cico_chld_id = $chld_detail_fet_cico['charge_list_detail_id'];
		$cico_chl_id = $chld_detail_fet_cico['charge_list_id'];
		$cico_procCode_id = $chld_detail_fet_cico['procCode'];
				
		$cpt_code_ref="";
		if(in_array($cico_procCode_id,$cico_ref_arr)){
			$cpt_code_ref="yes";   
		}
		
		$dop=$cico_main_fet['created_on'];		
		if($post_date!="00-00-0000" && $post_date!=""){
			$post_date_exp=explode('-',$post_date);	
			$tday = $post_date_exp[2].'-'.$post_date_exp[0].'-'.$post_date_exp[1];
		}else{
			$tday = date('Y-m-d');
		}
		
		$sch_id=$cico_main_fet['sch_id'];
		$sch_in_qry=imw_query("select sa_facility_id from schedule_appointments where id='$sch_id'");
		$sch_in_fet=imw_fetch_array($sch_in_qry);
		$sa_facility_id = $sch_in_fet['sa_facility_id'];
		
		if($cico_chl_copay_amt>0){
			if(strtolower($cico_main_fet['item_name'])=='copay-visit' || strtolower($cico_main_fet['item_name'])=='copay-test' || strtolower($cico_main_fet['item_name'])=='copay'){
				
				$getproccode = "SELECT sum(paidForProc) as tot_paidproc FROM 
								patient_chargesheet_payment_info a,
								patient_charges_detail_payment_info b
								WHERE a.encounter_id = '$cico_enc_id'
								AND a.payment_id = b.payment_id
								AND b.charge_list_detail_id = 0
								AND b.deletePayment=0
								ORDER BY a.payment_id DESC";
				$getproccodeQry = imw_query($getproccode);
				$getproccodeRow = imw_fetch_array($getproccodeQry);
				$paidForProc_chk = $getproccodeRow['tot_paidproc'];
				$copay_pending_amt=$cico_chl_copay_amt-$paidForProc_chk;
				
				if($un_cico_post_amount_chk>$copay_pending_amt){
					$copay_pay_amt=$copay_pending_amt;
				}else{
					$copay_pay_amt=$un_cico_post_amount_chk;
				}
				if($copay_pay_amt>0){
					$chk_cico_paid_chld_arr[$cico_chld_id][]=$copay_pay_amt;
					$insertPaymentInfoStr = "INSERT INTO patient_chargesheet_payment_info SET
												encounter_id = '$cico_enc_id', 
												paid_by ='Patient',
												payment_amount = '$copay_pay_amt',
												payment_mode='$cico_payment_method', 
												checkNo='$cico_check_no',
												creditCardNo ='$cico_cc_no', 
												creditCardCo = '$cico_cc_type',
												expirationDate = '$cico_cc_expire_date',
												date_of_payment = '$dop', 
												operatorId = '$operatorId',
												transaction_date = '$tday',
												paymentClaims = 'paid',
												facility_id='$sa_facility_id'";
					$insertPaymentInfoQry = imw_query($insertPaymentInfoStr);
					$paymentInsertId = imw_insert_id();
						
					$insertCheckInPostPayment = "INSERT INTO check_in_out_payment_post SET
												patient_id = '$dc_patient_id',
												encounter_id = '$cico_enc_id',
												charge_list_detail_id ='$cico_chld_id',
												check_in_out_payment_id = '$cico_payment_id_fet', 
												check_in_out_payment_detail_id = '$cico_pay_detail_id',
												acc_payment_id  = '$paymentInsertId'";
					$insertCheckInPostPaymentQry = imw_query($insertCheckInPostPayment);
						
					$insertPaymentDetailsInfoStr = "INSERT INTO patient_charges_detail_payment_info SET
													payment_id = '$paymentInsertId',
													charge_list_detail_id ='0',
													paidBy = 'Patient', 
													paidDate = '$dop',
													paidForProc = '$copay_pay_amt',
													operator_id='$operator_id',
													entered_date='$entered_date'";
					$insertPaymentDetailsInfoQry = imw_query($insertPaymentDetailsInfoStr);
					
					$ApplyCopayQry = imw_query("UPDATE patient_charge_list_details SET
												newBalance = newBalance-$copay_pay_amt,
												balForProc = balForProc-$copay_pay_amt,
												paidStatus = 'Paid',
												coPayAdjustedAmount = '1'
												WHERE charge_list_detail_id = '$cico_chld_id'");
											
											
					if((float)$un_cico_post_amount_chk>=(float)$copay_pending_amt){
						$ApplyCopayQry1 = imw_query("UPDATE patient_charge_list  SET
													copayPaid = '1',
													coPayAdjusted = '1',
													coPayAdjustedDate = '$tday'
													WHERE encounter_id = '$cico_enc_id'");
					}
					$un_cico_post_amount_chk=$un_cico_post_amount_chk-$copay_pay_amt;
					set_payment_trans($cico_enc_id);
				}
			}
		}
		
		if(strtolower($cico_main_fet['item_name'])=='refraction' && $cpt_code_ref=='yes'){
			if($un_cico_post_amount_chk>$cico_chld_newBalance){
				$proc_pay_amt=$cico_chld_newBalance;
			}else{
				$proc_pay_amt=$un_cico_post_amount_chk;
			}
			if($proc_pay_amt>0){
				$chk_cico_paid_chld_arr[$cico_chld_id][]=$proc_pay_amt;
				$insertPaymentInfoStr = "INSERT INTO patient_chargesheet_payment_info SET
											encounter_id = '$cico_enc_id', 
											paid_by ='Patient',
											payment_amount = '$proc_pay_amt',
											payment_mode='$cico_payment_method', 
											checkNo='$cico_check_no',
											creditCardNo ='$cico_cc_no', 
											creditCardCo = '$cico_cc_type',
											expirationDate = '$cico_cc_expire_date',
											date_of_payment = '$dop', 
											operatorId = '$operatorId',
											transaction_date = '$tday',
											paymentClaims = 'paid',
											facility_id='$sa_facility_id'";
				$insertPaymentInfoQry = imw_query($insertPaymentInfoStr);
				$paymentInsertId = imw_insert_id();
					
				$insertCheckInPostPayment = "INSERT INTO check_in_out_payment_post SET
											patient_id = '$dc_patient_id',
											encounter_id = '$cico_enc_id',
											charge_list_detail_id ='$cico_chld_id',
											check_in_out_payment_id = '$cico_payment_id_fet', 
											check_in_out_payment_detail_id = '$cico_pay_detail_id',
											acc_payment_id  = '$paymentInsertId'";
				$insertCheckInPostPaymentQry = imw_query($insertCheckInPostPayment);
					
				$insertPaymentDetailsInfoStr = "INSERT INTO patient_charges_detail_payment_info SET
												payment_id = '$paymentInsertId',
												charge_list_detail_id ='$cico_chld_id',
												paidBy = 'Patient', 
												paidDate = '$dop',
												paidForProc = '$proc_pay_amt',
												operator_id='$operator_id',
												entered_date='$entered_date'";
				$insertPaymentDetailsInfoQry = imw_query($insertPaymentDetailsInfoStr);
				
				$ApplyCopayQry = imw_query("UPDATE patient_charge_list_details SET
											newBalance = newBalance-$proc_pay_amt,
											balForProc = balForProc-$proc_pay_amt,
											paidForProc = paidForProc + $proc_pay_amt,
											paidStatus = 'Paid'
											WHERE charge_list_detail_id = '$cico_chld_id'");
											
				$un_cico_post_amount_chk=$un_cico_post_amount_chk-$proc_pay_amt;
				
				if($proc_pay_amt>0){
					$pay_type="pat";
					patient_proc_tx_update($cico_chld_id,$proc_pay_amt,$pay_type,$ins_type);
				}
				set_payment_trans($cico_enc_id);
			}
		}
		
		if($un_cico_post_amount_chk>0){	
			$chld_detail_cico_tot = imw_query("select paidForProc from patient_charge_list_details where del_status='0' and charge_list_detail_id='$cico_chld_id'");
			$chld_detail_fet_cico_tot=imw_fetch_array($chld_detail_cico_tot);
			$last_paid_tot_amt=$chld_detail_fet_cico_tot['paidForProc'];
			$neg_pay_chk="";
			if($last_paid_tot_amt_neg<0){
				$final_total_neg_paid=$last_paid_tot_amt+(-$last_paid_tot_amt_neg);
				if($chk_enc_tot_paid_amt>$final_total_neg_paid){
					$neg_pay_chk="";
				}else{
					$neg_pay_chk="done";
				}
			}
			if($chk_enc_tot_paid_amt>$last_paid_tot_amt && $neg_pay_chk==""){
				if($now_proc_paid_tot>0 && $cico_chld_newBalance>0){
					
					if($now_proc_paid_tot>=$cico_chld_newBalance){
						if($un_cico_post_amount_chk>=$now_proc_paid_tot){
							$proc_pay_amt=$now_proc_paid_tot;
						}else{
							$proc_pay_amt=$un_cico_post_amount_chk;
						}
					}else{
						if($un_cico_post_amount_chk>=$cico_chld_newBalance){
							$proc_pay_amt=$now_proc_paid_tot;
						}else{
							if($un_cico_post_amount_chk>=$now_proc_paid_tot){
								$proc_pay_amt=$now_proc_paid_tot;
							}else{
								$proc_pay_amt=$un_cico_post_amount_chk;
							}
						}
					}
					
					if($proc_pay_amt>0){
						$chk_cico_paid_chld_arr[$cico_chld_id][]=$proc_pay_amt;
						$insertPaymentInfoStr = "INSERT INTO patient_chargesheet_payment_info SET
													encounter_id = '$cico_enc_id', 
													paid_by ='Patient',
													payment_amount = '$proc_pay_amt',
													payment_mode='$cico_payment_method', 
													checkNo='$cico_check_no',
													creditCardNo ='$cico_cc_no', 
													creditCardCo = '$cico_cc_type',
													expirationDate = '$cico_cc_expire_date',
													date_of_payment = '$dop', 
													operatorId = '$operatorId',
													transaction_date = '$tday',
													paymentClaims = 'paid',
													facility_id='$sa_facility_id'";
							$insertPaymentInfoQry = imw_query($insertPaymentInfoStr);
							$paymentInsertId = imw_insert_id();
								
							$insertCheckInPostPayment = "INSERT INTO check_in_out_payment_post SET
															patient_id = '$dc_patient_id',
															encounter_id = '$cico_enc_id',
															charge_list_detail_id ='$cico_chld_id',
															check_in_out_payment_id = '$cico_payment_id_fet', 
															check_in_out_payment_detail_id = '$cico_pay_detail_id',
															acc_payment_id  = '$paymentInsertId'";
							$insertCheckInPostPaymentQry = imw_query($insertCheckInPostPayment);
								
							$insertPaymentDetailsInfoStr = "INSERT INTO patient_charges_detail_payment_info SET
															payment_id = '$paymentInsertId',
															charge_list_detail_id ='$cico_chld_id',
															paidBy = 'Patient', 
															paidDate = '$dop',
															paidForProc = '$proc_pay_amt',
															operator_id='$operator_id',
															entered_date='$entered_date'";
							$insertPaymentDetailsInfoQry = imw_query($insertPaymentDetailsInfoStr);
							
							if($proc_pay_amt>0){
								$pay_type="pat";
								patient_proc_tx_update($cico_chld_id,$proc_pay_amt,$pay_type,$ins_type);
							}
							set_payment_trans($cico_enc_id);
					}
				}
			}
		}
	}
	$encounter_id = $cico_enc_id;
	include "manageEncounterAmounts.php";
}
if($pt_pmt_paid>0 && $cico_chld_id>0){

	$pt_main_qry="select * from patient_pre_payment where
					del_status='0' and id in($pt_pmt_id) and apply_payment_type!='manually'";
	$pt_main_run=imw_query($pt_main_qry);
	while($pt_main_fet=imw_fetch_array($pt_main_run)){
		
		$un_pt_post_amount_chk=0;
		$total_cico_post_amt=0;
		$pt_pending_post_amt=0;
		$total_pt_pmt_post_amt_enc_arr=array();
		$id=$pt_main_fet['id'];
		$total_cico_post_amt=array_sum($chk_cico_paid_chld_arr[$cico_chld_id]);
		$un_pt_post_amount_chk=$now_proc_paid_tot-$total_cico_post_amt;
		
		$qry_pt_pay_post_enc = imw_query("SELECT paidForProc,overPayment
								 FROM 
								patient_charges_detail_payment_info
								WHERE 
								patient_pre_payment_id in($id)
								and deletePayment ='0' and unapply='0'");
		while($get_pt_pay_post_enc = imw_fetch_array($qry_pt_pay_post_enc)){
			$total_pt_pmt_post_amt_enc_arr[] = $get_pt_pay_post_enc['overPayment']+$get_pt_pay_post_enc['paidForProc'];
		}
		
		$sel_ref_qry=imw_query("select pmt_id,ref_amt from ci_pmt_ref where ci_pmt_ref.del_status='0' and ci_pmt_ref.pmt_id in($id) 
		and ci_pmt_ref.pmt_id>0");
		while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
			if($sel_ref_row['pmt_id']>0){
				$total_pt_pmt_post_amt_enc_arr[]=$sel_ref_row['ref_amt'];
			}
		}
		
		$pt_pending_post_amt=$pt_main_fet['paid_amount']-array_sum($total_pt_pmt_post_amt_enc_arr);
		
		if($un_pt_post_amount_chk>0 && $pt_pending_post_amt>0){
			$proc_pay_amt=0;
			$chld_detail_cico = imw_query("select newBalance,charge_list_detail_id,charge_list_id,procCode
							from patient_charge_list_details where del_status='0' and charge_list_detail_id='$cico_chld_id'");
			$chld_detail_fet_cico=imw_fetch_array($chld_detail_cico);
			
			$cico_chld_newBalance  = $chld_detail_fet_cico['newBalance'];
			$cico_chld_id = $chld_detail_fet_cico['charge_list_detail_id'];
			$cico_chl_id = $chld_detail_fet_cico['charge_list_id'];
			$cico_procCode_id = $chld_detail_fet_cico['procCode'];
			
			if($pt_pending_post_amt>$un_pt_post_amount_chk){
				$proc_pay_amt=$un_pt_post_amount_chk;
			}else{
				$proc_pay_amt=$pt_pending_post_amt;
			}
			
			$chk_cico_paid_chld_arr[$cico_chld_id][]=$proc_pay_amt;
			
			$cico_payment_method = $pt_main_fet['payment_mode'];
			$cico_check_no		 = $pt_main_fet['check_no'];
			$cico_cc_type 		 = $pt_main_fet['credit_card_co'];
			$cico_cc_no  		 = $pt_main_fet['cc_no'];
			$cico_cc_expire_date = $pt_main_fet['cc_exp_date'];
			$ppp_facility_id = $pt_main_fet['facility_id'];
			$tday = date('Y-m-d');
			$dop=$pt_main_fet['paid_date'];
			
			$insertPaymentInfoStr = "INSERT INTO patient_chargesheet_payment_info SET
										encounter_id = '$cico_enc_id', 
										paid_by ='Patient',
										payment_amount = '$proc_pay_amt',
										payment_mode='$cico_payment_method', 
										checkNo='$cico_check_no',
										creditCardNo ='$cico_cc_no', 
										creditCardCo = '$cico_cc_type',
										expirationDate = '$cico_cc_expire_date',
										date_of_payment = '$dop', 
										operatorId = '$operatorId',
										transaction_date = '$tday',
										paymentClaims = 'paid',
										facility_id='$ppp_facility_id'";
				$insertPaymentInfoQry = imw_query($insertPaymentInfoStr);
				$paymentInsertId = imw_insert_id();
					
				$insertPaymentDetailsInfoStr = "INSERT INTO patient_charges_detail_payment_info SET
												payment_id = '$paymentInsertId',
												charge_list_detail_id ='$cico_chld_id',
												paidBy = 'Patient', 
												paidDate = '$dop',
												paidForProc = '$proc_pay_amt',
												operator_id='$operator_id',
												entered_date='$entered_date',
												patient_pre_payment_id='$id'";
				$insertPaymentDetailsInfoQry = imw_query($insertPaymentDetailsInfoStr);
				
				if($proc_pay_amt>0){
					$pay_type="pat";
					patient_proc_tx_update($cico_chld_id,$proc_pay_amt,$pay_type,$ins_type);
				}
				set_payment_trans($cico_enc_id);
		}
	}
	$encounter_id = $cico_enc_id;
	include "manageEncounterAmounts.php";
}

?>