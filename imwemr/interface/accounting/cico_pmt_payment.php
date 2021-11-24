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

$cico_det_arr=$cico_total_post_amt_arr=array();
if($payment_method=="Check In_Out"){
	$cico_qry=imw_query("select ciopd.id,ciopd.item_payment,ciop.payment_id,ciop.payment_method,ciop.check_no,ciop.cc_no,ciop.cc_expire_date,ciop.cc_type
						 from check_in_out_payment as ciop join check_in_out_payment_details as ciopd on ciopd.payment_id=ciop.payment_id
						 where ciop.patient_id='$patient_id' and ciopd.status='0' and ciopd.item_payment>0 order by ciopd.id asc");
	while($row_cico=imw_fetch_array($cico_qry)){
		$cico_det_arr[$row_cico['id']]=$row_cico;
	}
	
	if(count($cico_det_arr)>0){
		$qry_cico_post = imw_query("select pcdpi.paidForProc,pcdpi.overPayment,ciopp.check_in_out_payment_id,ciopp.check_in_out_payment_detail_id	
									from patient_chargesheet_payment_info as pcpi 
									join patient_charges_detail_payment_info as pcdpi on pcpi.payment_id=pcdpi.payment_id
									join check_in_out_payment_post as ciopp on ciopp.acc_payment_id=pcpi.payment_id
									where ciopp.patient_id ='$patient_id'
									and pcdpi.deletePayment ='0' and ciopp.status='0' and pcpi.unapply='0'");
		while($row_cico_post = imw_fetch_array($qry_cico_post)){
			$cico_total_post_amt_arr[$row_cico_post['check_in_out_payment_detail_id']][]=$row_cico_post['overPayment']+$row_cico_post['paidForProc'];
		}
	
		$sel_ref_qry=imw_query("select ci_co_id,ref_amt from ci_pmt_ref where patient_id='$patient_id' and del_status='0' and ci_co_id>0");
		while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
			$cico_total_post_amt_arr[$sel_ref_row['ci_co_id']][]=$sel_ref_row['ref_amt'];
		}
	}
}

if($payment_method=='Patient Pre Pmts'){
	$cico_qry=imw_query("select * from patient_pre_payment where patient_id='$patient_id' and del_status='0' and paid_amount>0 and apply_payment_type!='manually' order by id asc");
	while($row_cico=imw_fetch_array($cico_qry)){
		$cico_det_arr[$row_cico['id']]=$row_cico;
	}
	
	if(count($cico_det_arr)>0){
		$qry_cico_post = imw_query("select pcdpi.paidForProc,pcdpi.overPayment,pcdpi.patient_pre_payment_id
									from patient_chargesheet_payment_info as pcpi 
									join patient_charges_detail_payment_info as pcdpi on pcpi.payment_id=pcdpi.payment_id
									join patient_pre_payment on patient_pre_payment.id=pcdpi.patient_pre_payment_id
									where patient_pre_payment.patient_id='$patient_id'
									and pcdpi.deletePayment='0' and patient_pre_payment.del_status='0'");
		while($row_cico_post = imw_fetch_array($qry_cico_post)){
			$cico_total_post_amt_arr[$row_cico_post['patient_pre_payment_id']][]=$row_cico_post['overPayment']+$row_cico_post['paidForProc'];
		}
		
		$sel_ref_qry=imw_query("select pmt_id,ref_amt from ci_pmt_ref where patient_id='$patient_id' and del_status='0' and pmt_id>0");
		while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
			$cico_total_post_amt_arr[$sel_ref_row['pmt_id']][]=$sel_ref_row['ref_amt'];
		}
	}
}
if(count($cico_det_arr)>0){
	foreach($paid_by_arr as $paid_key=>$paid_val){
		$paid_amt=$_POST[$paid_val.'_paid_'.$chk_key];
		if($paid_amt>0){
			foreach($cico_det_arr as $cico_did=>$cico_val){
				if($payment_method=="Check In_Out"){
					$cico_not_posted_amt=$cico_det_arr[$cico_did]['item_payment']-array_sum($cico_total_post_amt_arr[$cico_did]);
					$check_in_out_pay_id=$cico_det_arr[$cico_did]['payment_id'];
					$check_in_out_pay_detail_id=$cico_did;
					$payment_type = $cico_det_arr[$cico_did]['payment_method'];
					$check_no = $cico_det_arr[$cico_did]['check_no'];
					$cc_no = $cico_det_arr[$cico_did]['cc_no'];
					$cc_type = $cico_det_arr[$cico_did]['cc_type'];
					$cc_exp_date = $cico_det_arr[$cico_did]['cc_expire_date'];
					$patient_pre_payment_id=0;
				}
				
				if($payment_method=="Patient Pre Pmts"){
					$cico_not_posted_amt=$cico_det_arr[$cico_did]['paid_amount']-array_sum($cico_total_post_amt_arr[$cico_did]);
					$patient_pre_payment_id=$cico_did;
					$payment_type = $cico_det_arr[$cico_did]['payment_mode'];
					$check_no = $cico_det_arr[$cico_did]['check_no'];
					$cc_no = $cico_det_arr[$cico_did]['cc_no'];
					$cc_type = $cico_det_arr[$cico_did]['credit_card_co'];
					$cc_exp_date = $cico_det_arr[$cico_did]['cc_exp_date'];
				}
				
				if($cico_not_posted_amt>0 && $paid_amt>0){
					if($cico_not_posted_amt>=$paid_amt){
						$cico_apply_amt=$paid_amt;
						$paid_amt=0;
					}else{
						$cico_apply_amt=$cico_not_posted_amt;
						$paid_amt=$paid_amt-$cico_not_posted_amt;
					}
					if($paid_val=="pri" || $paid_val=="sec" || $paid_val=="ter"){
						$paid_by="Insurance";
						$ins_prov_id=$_POST[$paid_val.'_ins_'.$chk_key];
						$pay_type="ins";
					}else if($paid_val=="resp"){
						$paid_by="Res. Party";
						$ins_prov_id=0;
						$pay_type="pat";
					}else{
						$paid_by="Patient";
						$ins_prov_id=0;
						$pay_type="pat";
					}
				
					$pcpi_sql=imw_query("insert into patient_chargesheet_payment_info set encounter_id='$encounter_id',paid_by='$paid_by',
					payment_amount='$cico_apply_amt',payment_mode='$payment_type',checkNo='$check_no',creditCardNo='$cc_no',creditCardCo='$cc_type',
					date_of_payment='$paid_date',payment_time='$trans_time',operatorId='$operator_id',insProviderId='$ins_prov_id',insCompany='$paid_key',
					paymentClaims='Paid',transaction_date='$trans_date',facility_id='$facility_id'");
					$pcpi_id=imw_insert_id();
					$pcdpi_sql=imw_query("insert into patient_charges_detail_payment_info set payment_id='$pcpi_id',paidBy='$paid_by',
					charge_list_detail_id='$chld_id',paidDate='$paid_date',paid_time='$trans_time',paidForProc='$cico_apply_amt',operator_id='$operator_id',
					entered_date='$trans_date_time',CAS_type='$cas_type',CAS_code='$cas_code',patient_pre_payment_id='$patient_pre_payment_id'");
					patient_proc_tx_update($chld_id,$cico_apply_amt,$pay_type,$paid_key);
					if($payment_method=="Check In_Out"){
						$pcdpi_sql=imw_query("insert into check_in_out_payment_post set encounter_id='$encounter_id',charge_list_detail_id='$chld_id',
						patient_id='$patient_id',acc_payment_id='$pcpi_id',check_in_out_payment_id='$check_in_out_pay_id',check_in_out_payment_detail_id='$cico_did'");
					}
					
					imw_query("UPDATE patient_charge_list SET lastPayment = '$cico_apply_amt',lastPaymentDate = '$paid_date' WHERE encounter_id = '$encounter_id'");

					$cico_total_post_amt_arr[$cico_did][]=$cico_apply_amt;
					set_payment_trans($encounter_id);
				}
			}
		}
	}
}
?>