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
require_once("acc_header.php"); 	
$patient_id=$_SESSION['patient'];
$operatorId = $_SESSION['authUserID'];
$operator_id = $_SESSION['authId'];
$entered_date = date('Y-m-d H:i:s');
$chld=$_REQUEST['chld'];
$payment_id=$_REQUEST['payment_id'];
$payment_detail_id=$_REQUEST['payment_detail_id'];
$encounter_id=$_REQUEST['encounter_id'];
$pay_amt=$_REQUEST['pay_amt'];
if($pay_amt>0 && $chld>0){

	$total_post_amt=0;
	//$sel_check_pay=imw_query("select sum(total_payment) as total_payment from check_in_out_payment where patient_id='$patient_id' and payment_id='$payment_id' and del_status='0'");
	$sel_check_pay=imw_query("select item_payment from check_in_out_payment_details where id='$payment_detail_id' and payment_id='$payment_id' and status='0'");
	$fet_check_pay=imw_fetch_array($sel_check_pay);
	if($fet_check_pay['item_payment']>0){
		$qry_check_in_out_post = imw_query("SELECT paidForProc,overPayment FROM 
												patient_chargesheet_payment_info as a,
												check_in_out_payment_post as b,
												patient_charges_detail_payment_info as c
												WHERE 
												a.payment_id=b.acc_payment_id 
												and a.payment_id=c.payment_id
												and b.patient_id='$patient_id'
												and b.check_in_out_payment_id='$payment_id'
												and b.check_in_out_payment_detail_id='$payment_detail_id'
												and c.deletePayment ='0'
												and b.status='0'");
		while($get_check_in_out_post = imw_fetch_array($qry_check_in_out_post)){
			$total_post_amt = $get_check_in_out_post['overPayment']+$get_check_in_out_post['paidForProc']+$total_post_amt;
		}
		
		$sel_ref_qry=imw_query("select ci_pmt_ref.ci_co_id,ci_pmt_ref.ref_amt from ci_pmt_ref 
		join check_in_out_payment_details on ci_pmt_ref.ci_co_id=check_in_out_payment_details.id
		join check_in_out_payment on check_in_out_payment.payment_id=check_in_out_payment_details.payment_id
		where ci_pmt_ref.patient_id='$patient_id' and ci_pmt_ref.del_status='0' 
		and check_in_out_payment.payment_id in($payment_id) and check_in_out_payment_details.id in($payment_detail_id) and ci_pmt_ref.ci_co_id>0");
		while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
			if($sel_ref_row['ci_co_id']>0){
				$total_post_amt = $total_post_amt+$sel_ref_row['ref_amt'];
			}
		}
		
		$un_post_amount_chk=$fet_check_pay['item_payment']-$total_post_amt;
	}
	if((float)$pay_amt>(float)$un_post_amount_chk){
		$pay_amt=$un_post_amount_chk;
	}
	$un_post_amt_show=$un_post_amount_chk-$pay_amt;
	
	$check_in_qry=imw_query("select * from check_in_out_payment where payment_id='$payment_id' and del_status='0'");
	$check_in_fet=imw_fetch_array($check_in_qry);
	$payment_method = $check_in_fet['payment_method'];
	$check_no		= $check_in_fet['check_no'];
	$cc_type 		= $check_in_fet['cc_type'];
	$cc_no  		= $check_in_fet['cc_no'];
	$cc_expire_date = $check_in_fet['cc_expire_date'];
	$sch_id = $check_in_fet['sch_id'];
	
	$sch_in_qry=imw_query("select sa_facility_id from schedule_appointments where id='$sch_id'");
	$sch_in_fet=imw_fetch_array($sch_in_qry);
	$facility_id = $sch_in_fet['sa_facility_id'];
	
	$tday=date('Y-m-d');
	$dop=$check_in_fet['created_on'];
	$insertPaymentInfoStr = "INSERT INTO patient_chargesheet_payment_info SET
								encounter_id = '$encounter_id', 
								paid_by ='Patient',
								payment_amount = '$pay_amt',
								payment_mode='$payment_method', 
								checkNo='$check_no',
								creditCardNo ='$cc_no', 
								creditCardCo = '$cc_type',
								expirationDate = '$cc_expire_date',
								date_of_payment = '$dop', 
								operatorId = '$operatorId',
								transaction_date = '$tday',
								paymentClaims = 'paid',
								facility_id='$facility_id'";
	$insertPaymentInfoQry = imw_query($insertPaymentInfoStr);
	$paymentInsertId = imw_insert_id();
	
	$insertCheckInPostPayment = "INSERT INTO check_in_out_payment_post SET
									patient_id = '$patient_id',
									encounter_id = '$encounter_id',
									charge_list_detail_id ='$chld',
									check_in_out_payment_id = '$payment_id', 
									check_in_out_payment_detail_id = '$payment_detail_id',
									acc_payment_id  = '$paymentInsertId'";
									
	$insertCheckInPostPaymentQry = imw_query($insertCheckInPostPayment);
	if($copay_chk=='copay'){
		$insertPaymentDetailsInfoStr = "INSERT INTO patient_charges_detail_payment_info SET
											payment_id = '$paymentInsertId',
											charge_list_detail_id ='0',
											paidBy = 'Patient', 
											paidDate = '$dop',
											paidForProc = '$pay_amt',
											operator_id='$operator_id',
											entered_date='$entered_date'";
		$insertPaymentDetailsInfoQry = imw_query($insertPaymentDetailsInfoStr);
		
		$ApplyCopayQry = imw_query("UPDATE patient_charge_list_details SET
										newBalance = newBalance-$pay_amt,
										balForProc = balForProc-$pay_amt,
										paidStatus = 'Paid',
										coPayAdjustedAmount = '1'
										WHERE charge_list_detail_id = '$chld'");
		
		$chld_detail_cico = imw_query("select copay from patient_charge_list where del_status='0' and encounter_id='$encounter_id'");
		$chld_detail_fet_cico=imw_fetch_array($chld_detail_cico);
		$cico_chl_copay_amt= $chld_detail_fet_cico['copay'];
										
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
		$copay_pending_amt=$cico_chl_copay_amt-$paidForProc_chk;
		
		if((float)$un_post_amount_chk>=(float)$pay_amt){
			if($copay_pending_amt>0){
			}else{
				$ApplyCopayQry1 = imw_query("UPDATE patient_charge_list  SET
											copayPaid = '1',
											coPayAdjusted = '1',
											coPayAdjustedDate = '$tday'
											WHERE encounter_id = '$encounter_id'");
			}
		}									
	}else{
	
		$insertPaymentDetailsInfoStr = "INSERT INTO patient_charges_detail_payment_info SET
											payment_id = '$paymentInsertId',
											charge_list_detail_id ='$chld',
											paidBy = 'Patient', 
											paidDate = '$dop',
											paidForProc = '$pay_amt',
											operator_id='$operator_id',
											entered_date='$entered_date'";
		$insertPaymentDetailsInfoQry = imw_query($insertPaymentDetailsInfoStr);
		
		
		
		$ApplyCopayQry = imw_query("UPDATE patient_charge_list_details SET
										newBalance = newBalance-$pay_amt,
										balForProc = balForProc-$pay_amt,
										paidForProc = paidForProc + $pay_amt,
										paidStatus = 'Paid'
										WHERE charge_list_detail_id = '$chld'");
	}									
	
	include("manageEncounterAmounts.php");	
	
	if($pay_amt>0){
		$pay_type="pat";
		patient_proc_tx_update($chld,$pay_amt,$pay_type,$ins_type);
		set_payment_trans($encounter_id);
	}
	
	$total_post_amt_chk=0;
	$sel_check_pay=imw_query("select sum(total_payment) as total_payment from check_in_out_payment where patient_id='$patient_id' and del_status='0'");
	$fet_check_pay=imw_fetch_array($sel_check_pay);
	if($fet_check_pay['total_payment']>0){
		
		$cico_pay_detail_ids_arr=array();
		$total_cico_post_amt_enc_arr=array();
		$qry_check_in_out_manual_post = imw_query("SELECT a.manually_payment,a.manually_by,b.id FROM 
										check_in_out_payment_post as a,
										check_in_out_payment_details as b
										 WHERE 
										a.check_in_out_payment_detail_id=b.id
										and a.patient_id='$patient_id'
										and a.status='0'
										and b.status='0'");
		while($get_check_in_out_manual_post= imw_fetch_array($qry_check_in_out_manual_post)){
			if($get_check_in_out_manual_post['manually_payment']>0){
				$total_cico_post_amt_enc_arr[] = $get_check_in_out_manual_post['manually_payment'];
			}
			$cico_pay_detail_ids_arr[$get_check_in_out_manual_post['id']]=$get_check_in_out_manual_post['id'];
		}
		
		$cico_pay_detail_ids_imp=implode(',',$cico_pay_detail_ids_arr);	
		
		$qry_check_in_out_post = imw_query("SELECT paidForProc,overPayment FROM 
												patient_chargesheet_payment_info as a,
												check_in_out_payment_post as b,
												patient_charges_detail_payment_info as c
												WHERE 
												a.payment_id=b.acc_payment_id 
												and a.payment_id=c.payment_id
												and b.patient_id='$patient_id'
												and c.deletePayment ='0'
												and b.status='0'
												and b.check_in_out_payment_detail_id in($cico_pay_detail_ids_imp)");
		while($get_check_in_out_post = imw_fetch_array($qry_check_in_out_post)){
			$total_post_amt_chk = $get_check_in_out_post['overPayment']+$get_check_in_out_post['paidForProc']+$total_post_amt_chk;
		}
		
		$sel_ref_qry=imw_query("select ci_pmt_ref.ci_co_id,ci_pmt_ref.ref_amt from ci_pmt_ref 
		where ci_pmt_ref.patient_id='$patient_id' and ci_pmt_ref.del_status='0' 
		 and ci_pmt_ref.ci_co_id in($cico_pay_detail_ids_imp) and ci_pmt_ref.ci_co_id>0");
		while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
			if($sel_ref_row['ci_co_id']>0){
				$total_post_amt_chk = $total_post_amt_chk+$sel_ref_row['ref_amt'];
			}
		}
		
		$total_post_amt=$total_post_amt+array_sum($total_cico_post_amt_enc_arr);
		
		$un_post_amt_show_final=$fet_check_pay['total_payment']-$total_post_amt_chk;
	}
	echo "CI/CO Pmts: $".number_format($un_post_amt_show_final,2);
}		

?>
