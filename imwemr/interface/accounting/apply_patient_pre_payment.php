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
$encounter_id=$_REQUEST['encounter_id'];
$pay_amt=$_REQUEST['pay_amt'];
if($pay_amt>0 && $chld>0){
	
	$total_post_amt=0;
	$check_in_qry=imw_query("select * from patient_pre_payment where id='$payment_id' and del_status='0'");
	$check_in_fet=imw_fetch_array($check_in_qry);
	$payment_method = $check_in_fet['payment_mode'];
	$check_no		= $check_in_fet['check_no'];
	$cc_type 		= $check_in_fet['credit_card_co'];
	$cc_no  		= $check_in_fet['cc_no'];
	$cc_expire_date = $check_in_fet['cc_exp_date'];
	$facility_id = $check_in_fet['facility_id'];
	
	if($payment_id>0){
		$qry_check_in_out_post = imw_query("SELECT paidForProc,overPayment FROM  patient_charges_detail_payment_info 
												WHERE 
												patient_pre_payment_id='$payment_id'
												and deletePayment ='0' and unapply='0'");
		while($get_check_in_out_post = imw_fetch_array($qry_check_in_out_post)){
			$total_post_amt = $get_check_in_out_post['overPayment']+$get_check_in_out_post['paidForProc']+$total_post_amt;
		}
		
		$sel_ref_qry=imw_query("select pmt_id,ref_amt from ci_pmt_ref where ci_pmt_ref.del_status='0' and ci_pmt_ref.pmt_id in($payment_id) 
		and ci_pmt_ref.pmt_id>0");
		while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
			if($sel_ref_row['pmt_id']>0){
				$total_post_amt=$sel_ref_row['ref_amt']+$total_post_amt;
			}
		}
		
		$un_post_amount_chk=$check_in_fet['paid_amount']-$total_post_amt;
	}
	if((float)$pay_amt>(float)$un_post_amount_chk){
		$pay_amt=$un_post_amount_chk;
		//$apply_payment_type="paid";
	}else{
		//$apply_payment_type="partialy paid";
	}
	$tday=date('Y-m-d');
	$entered_time=date('H:i:s');
	$dop=$check_in_fet['paid_date'];
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
	$insertPaymentInfoQry = @imw_query($insertPaymentInfoStr);
	$paymentInsertId = @imw_insert_id();
	
	if($copay_chk=='copay'){
		$copay_charge_list_detail_id=0;
		$coPayAdjustedAmount=",coPayAdjustedAmount='1'";
	}else{
		$copay_charge_list_detail_id=$chld;
		$coPayAdjustedAmount="";
	}
	$insertPaymentDetailsInfoStr = "INSERT INTO patient_charges_detail_payment_info SET
										payment_id = '$paymentInsertId',
										charge_list_detail_id ='$copay_charge_list_detail_id',
										paidBy = 'Patient', 
										paidDate = '$dop',
										paidForProc = '$pay_amt',
										operator_id='$operator_id',
										entered_date='$entered_date',
										patient_pre_payment_id='$payment_id'";
	$insertPaymentDetailsInfoQry = imw_query($insertPaymentDetailsInfoStr);
	
	$ApplyCopayQry = imw_query("UPDATE patient_charge_list_details SET
									newBalance = newBalance-$pay_amt,
									balForProc = balForProc-$pay_amt,
									paidStatus = 'Paid'
									$coPayAdjustedAmount
									WHERE charge_list_detail_id = '$chld'");
	
	if($copay_chk=='copay'){	
	
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
						and a.unapply='0'
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
	}
	$apply_payment_time = date('H:i:s');
	imw_query("update patient_pre_payment set apply_payment_type='$apply_payment_type',apply_payment_by='$operator_id',
		apply_payment_date='$tday',apply_payment_time='$apply_payment_time' where id='$payment_id'");
}								

if($pay_amt>0){
	$pay_type="pat";
	patient_proc_tx_update($chld,$pay_amt,$pay_type,$ins_type);
	set_payment_trans($encounter_id);
}

$sel_ref_qry=imw_query("select encounter_id from patient_charge_list where patient_id='$patient_id' and del_status='0'");
while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
	$sel_enc_arr[$sel_ref_row['encounter_id']]=$sel_ref_row['encounter_id'];
}
$sel_enc_imp=implode(',',$sel_enc_arr);

$depo_qry = " select sum(paid_amount) as pt_pre_amount, group_concat(id) as pt_pre_patients_id from patient_pre_payment where del_status = '0' and  patient_id='$patient_id'
						and apply_payment_type!='manually' order by entered_date desc";
$depo_mysql = imw_query($depo_qry);
$depo_fet=imw_fetch_array($depo_mysql);
$un_post_pre_amt= $depo_fet['pt_pre_amount'];
$pt_pre_patients_id= $depo_fet['pt_pre_patients_id'];
if($pt_pre_patients_id!=""){
	$pre_amt_qry=imw_query("SELECT sum(patient_charges_detail_payment_info.paidForProc+patient_charges_detail_payment_info.overPayment) as apply_pt_pre_amount
					FROM  patient_chargesheet_payment_info 
					JOIN patient_charges_detail_payment_info on patient_charges_detail_payment_info.payment_id=patient_chargesheet_payment_info.payment_id 
					WHERE 
					patient_charges_detail_payment_info.deletePayment='0' 
					and patient_charges_detail_payment_info.patient_pre_payment_id>0
					and  patient_charges_detail_payment_info.patient_pre_payment_id in($pt_pre_patients_id)
					and patient_chargesheet_payment_info.unapply='0'
					and patient_chargesheet_payment_info.encounter_id in($sel_enc_imp)
					ORDER BY patient_chargesheet_payment_info.date_of_payment");
	$pre_amt_fet=imw_fetch_array($pre_amt_qry);
	$apply_post_pre_amt=$pre_amt_fet['apply_pt_pre_amount'];
	
	$sel_ref_qry=imw_query("select sum(ref_amt) as ref_amt from ci_pmt_ref where ci_pmt_ref.del_status='0' 
	and ci_pmt_ref.pmt_id in($pt_pre_patients_id) and ci_pmt_ref.pmt_id>0");
	$sel_ref_row=imw_fetch_array($sel_ref_qry);
	$apply_post_pre_amt=$sel_ref_row['ref_amt']+$apply_post_pre_amt;
	
	$final_show_pre_amt=$un_post_pre_amt-$apply_post_pre_amt;
}
echo " Patient Pre Pmts: $".number_format($final_show_pre_amt,2);

?>
