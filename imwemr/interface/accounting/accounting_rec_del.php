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
if($_REQUEST['enc_id']){
	$stop_clm_status=1;
	$encounter_id=$_REQUEST['enc_id'];
	$chld_id_exp=explode(',',$_REQUEST['chld_id']);
	$chld_id_imp=implode("','",$chld_id_exp);
	$sel_chl=imw_query("select charge_list_id,patient_id from patient_charge_list_details where charge_list_detail_id in('".$chld_id_imp."') and charge_list_detail_id>0 limit 0,1");
	$row_chl=imw_fetch_array($sel_chl);
	$charge_list_id=$row_chl['charge_list_id'];
	$pat_id=$row_chl['patient_id'];
	$curr_date_time=date('Y-m-d H:i:s');
	$operator_id=$_SESSION['authId'];
	$curr_time = date('H:i:s');
	$curr_date = date('Y-m-d');
	
	$delSelectedStr = "update patient_charge_list_details set del_status='1',del_operator_id='".$_SESSION['authId']."',trans_del_date='".date('Y-m-d H:i:s')."',
	paidForProc='0',balForProc=totalAmount,pri_due='0',sec_due='0',tri_due='0',pat_due=totalAmount,write_off='0',total_write_off='0',approvedAmt=totalAmount,
	deductAmt='0',newBalance=totalAmount,coPayAdjustedAmount='0',overPaymentForProc='0',creditProcAmount='0' WHERE charge_list_detail_id in('".$chld_id_imp."')";
	$delSelectedQry = imw_query($delSelectedStr);

	$pcdpi_qry = imw_query("select group_concat(payment_id) as payment_id_mul from patient_charges_detail_payment_info WHERE charge_list_detail_id in('".$chld_id_imp."') and deletePayment='0' and charge_list_detail_id>0");
	$row_pcdpi_qry=imw_fetch_array($pcdpi_qry);
	$payment_id_mul=$row_pcdpi_qry['payment_id_mul'];
	
	$del_qry = imw_query("update account_payments set del_status='1',del_date_time='$curr_date_time',del_operator_id='$operator_id' WHERE charge_list_detail_id in ('".$chld_id_imp."') and del_status='0' and charge_list_detail_id>0");
	$del_qry = imw_query("update creditapplied set delete_credit='1',del_date_time='$curr_date_time',del_operator_id='$operator_id' WHERE charge_list_detail_id in ('".$chld_id_imp."') and delete_credit='0'and charge_list_detail_id>0 ");
	$del_qry = imw_query("update creditapplied set delete_credit='1',del_date_time='$curr_date_time',del_operator_id='$operator_id' WHERE charge_list_detail_id_adjust in ('".$chld_id_imp."') and delete_credit='0' and charge_list_detail_id_adjust>0");
	$del_qry = imw_query("update deniedpayment set denialDelStatus='1',denialDelDate='$curr_date',denialDelTime='$curr_time',del_operator_id='$operator_id' WHERE charge_list_detail_id in ('".$chld_id_imp."') and denialDelStatus='0' and charge_list_detail_id>0");
	$del_qry = imw_query("update payment_deductible set delete_deduct='1',delete_deduct_date='$curr_date',delete_deduct_time='$curr_time',delete_operator_id='$operator_id' WHERE charge_list_detail_id in('".$chld_id_imp."') and delete_deduct='0' and charge_list_detail_id>0");
	$del_qry = imw_query("update paymentswriteoff set delStatus='1',write_off_del_date='$curr_date',write_off_del_time='$curr_time',del_operator_id='$operator_id' WHERE charge_list_detail_id in('".$chld_id_imp."') and delStatus='0' and charge_list_detail_id>0");
	$del_qry = imw_query("update patient_chargesheet_payment_info set markPaymentDelete='1' WHERE payment_id in ($payment_id_mul) and markPaymentDelete='0'");
	$del_qry = imw_query("update patient_charges_detail_payment_info set deletePayment='1',deleteDate='$curr_date',deleteTime='$curr_time',del_operator_id='$operator_id' WHERE charge_list_detail_id in('".$chld_id_imp."') and deletePayment='0' and charge_list_detail_id>0");
	
	foreach($chld_id_exp as $chld_key => $chld_val){
		if($chld_val>0){
			$sel_def_wrt=imw_query("select write_off_amount,facility_id from defaultwriteoff where patient_id='$pat_id' and charge_list_detail_id='".$chld_val."' and del_status='0' order by write_off_id desc limit 0,1");
			$row_def_wrt=imw_fetch_array($sel_def_wrt);
			if(str_replace(',','',number_format($row_def_wrt['write_off_amount'],2))>0){
				imw_query("insert into defaultwriteoff set patient_id='".$pat_id."',encounter_id='".$encounter_id."',charge_list_id='".$charge_list_id."',charge_list_detail_id='".$chld_val."',
					write_off_amount='0',write_off_operator_id='".$operator_id."',write_off_dop='".$curr_date."',write_off_dot='".$curr_date_time."',facility_id='".$row_def_wrt['facility_id']."'");
			}
		}
	}
	
	//------------------------- DELETE ENCOUNTER DETAILS -------------------------//
	$getEncounterExitsStr = "SELECT * FROM patient_charge_list_details WHERE charge_list_id='$charge_list_id' and del_status='0'";
	$getEncounterExitsQry = imw_query($getEncounterExitsStr);
	$getEncounterExitsRows = imw_num_rows($getEncounterExitsQry);
	if($getEncounterExitsRows<=0){
		$deleteEncDetailsStr = "update patient_charge_list set del_status='1',del_operator_id='".$_SESSION['authId']."',trans_del_date='".date('Y-m-d H:i:s')."'
								WHERE charge_list_id='$charge_list_id'";
		$deleteEncDetailsQry = imw_query($deleteEncDetailsStr);
	}
	if($getEncounterExitsRows==0){
		$encounter_id="";
	}
	//------------------------- DELETE ENCOUNTER DETAILS -------------------------//
	include("manageEncounterAmounts.php");
	echo"<script>location.href='../accounting/accounting_view.php?encounter_id=".$encounter_id."&uniqueurl=".$encounter_id."'</script>";
}
?>