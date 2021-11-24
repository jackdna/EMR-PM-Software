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

$patient_id = $_SESSION['patient'];
$sel_check_pay = imw_query("select sum(total_payment) as total_payment from check_in_out_payment where patient_id='$patient_id' and del_status='0'");
$fet_check_pay = imw_fetch_array($sel_check_pay);

$sel_ref_qry=imw_query("select * from ci_pmt_ref where patient_id='$patient_id' and del_status='0'");
while($sel_ref_row=imw_fetch_array($sel_ref_qry)){
	if($sel_ref_row['ci_co_id']>0){
		$ci_ref_detail[$sel_ref_row['ci_co_id']][]=$sel_ref_row;
		$past_payment_chk_arr[]=$sel_ref_row['ref_amt'];
	}
	if($sel_ref_row['pmt_id']>0){
		$pmt_ref_detail[$sel_ref_row['pmt_id']][]=$sel_ref_row;
		$pmt_past_payment_chk_arr[]=$sel_ref_row['ref_amt'];
	}
}
	
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
	
	$query = "SELECT paidForProc,overPayment FROM patient_chargesheet_payment_info as a,
			check_in_out_payment_post as b,patient_charges_detail_payment_info as c
			WHERE 
			a.payment_id = b.acc_payment_id and a.payment_id = c.payment_id
			and b.patient_id = '$patient_id' and c.deletePayment ='0' and b.status='0' and a.unapply='0'
			and b.check_in_out_payment_detail_id in($cico_pay_detail_ids_imp)";
	$qry_check_in_out_post = imw_query($query);
	while($get_check_in_out_post = imw_fetch_array($qry_check_in_out_post)){
		$total_post_amt = $get_check_in_out_post['overPayment']+$get_check_in_out_post['paidForProc']+$total_post_amt;
	}
	
	$total_post_amt=$total_post_amt+array_sum($total_cico_post_amt_enc_arr)+array_sum($past_payment_chk_arr);
	
	$un_post_amount_chk=$fet_check_pay['total_payment']-$total_post_amt;
	$un_post_amount = $un_post_amount_chk;
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
	$apply_post_pre_amt=$pre_amt_fet['apply_pt_pre_amount']+array_sum($pmt_past_payment_chk_arr);
	$final_show_pre_amt=$un_post_pre_amt-$apply_post_pre_amt;
}
ob_start();
if($un_post_amount>0){
	require_once(dirname(__FILE__)."/check_in_out_div.php");
	show_modal('check_in_out_list','Check In/Out Payments',$ci_co_data,'','','modal-lg');
}
if($final_show_pre_amt>0){
	require_once(dirname(__FILE__)."/patient_pre_payments_div.php");
	show_modal('patient_pre_payments_list','Prepayments', $pp_data,'','','modal-lg');
}
$ci_pp_data=ob_get_contents();
ob_end_clean();
?> 
<?php
	$ret_data['ci_pp_data']=$ci_pp_data;  
	if($un_post_amount>0){
		$un_post_amount="CI/CO Pmts: <span>$".number_format($un_post_amount,2)."</span>";
	}else{
		$un_post_amount='';
	}
	if($final_show_pre_amt>0){
		$final_show_pre_amt="Prepayments: <span>$".number_format($final_show_pre_amt,2)."</span>";
	}else{
		$final_show_pre_amt='';
	}
	$ret_data['un_post_amount']=$un_post_amount;
	$ret_data['final_show_pre_amt']=$final_show_pre_amt;
?>