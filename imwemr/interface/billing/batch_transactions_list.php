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
$without_pat=$hide_acc_div="yes"; 
$title = "Batch Transactions List";
require_once("../accounting/acc_header.php");
require_once("../../library/classes/billing_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
$patient_id=$_SESSION['patient'];
$b_id=$_REQUEST['b_id'];
$sort_by=$_REQUEST['sort_by'];
?>
<?php 
$qry = imw_query("select id,in_house_code from insurance_companies");
while($fet_ins = imw_fetch_array($qry)){
	$ins_tbl_arr[$fet_ins['id']] = $fet_ins;
}

$qry = imw_query("select cpt_fee_id,cpt_cat_id,cpt4_code,cpt_prac_code from cpt_fee_tbl where delete_status='0'");
while($fet_cpt = imw_fetch_array($qry)){
	$cpt_fee_tbl_arr[$fet_cpt['cpt_fee_id']] = $fet_cpt;
}

$str .= '<table class="table table-bordered">
		<input type="hidden" name="edit_page" id="edit_page" value="batch_transactions_list">
		<tr class="grythead">
			<th class="pointer" onclick="load_batch_file(\'batch_transactions_list\',\'b_id='.$b_id.'&sort_by=id\')">
				#
			</th>
			<th class="pointer" onclick="load_batch_file(\'batch_transactions_list\',\'b_id='.$b_id.'&sort_by=patient\')">
				Patient ID
			</th>
			<th class="pointer" onclick="load_batch_file(\'batch_transactions_list\',\'b_id='.$b_id.'&sort_by=encounter\')">
				E. Id
			</th>
			<th>C.P.T.</th>
			<th>Total Charges</th>
			<th>Allowed</th>
			<th>Deductible</th>
			<th>Write off</th>
			<th>Adjustments</th>
			<th>Paid</th>
			<th>Negative Amt</th>
			<th>Method</th>
			<th>Paid By</th>
			<th>Function</th>
		</tr>';
	$total_proc_charges=array();
	$total_paid_proc="0";
	$total_deduct_proc="0";
	$total_write_proc="0";
	$total_allow_proc=array();
	$del_records_arr=array();
	if($sort_by=="encounter"){
		$order_by="manual_batch_transactions.encounter_id";
	}else if($sort_by=="patient"){
		$order_by="patient_data.lname,patient_data.fname";
	}else{
		$order_by="manual_batch_transactions.trans_id";
	}
	$sel_trans_rec=imw_query("select manual_batch_transactions.*
			from
		manual_batch_transactions as manual_batch_transactions,
		patient_data as  patient_data
			where 
		patient_data.id=manual_batch_transactions.patient_id
		and manual_batch_transactions.batch_id='$b_id' 
		and manual_batch_transactions.del_status!='1' 
		order by $order_by");
	//and manual_batch_transactions.payment_claims!='Allowed'
if(imw_num_rows($sel_trans_rec)>0){
	$i = 0;
	$show_num=1;
	while($row_trans_rec=imw_fetch_array($sel_trans_rec)){

		$InsComId=$row_trans_rec['insurance_id'];
		$patient_id=$row_trans_rec['patient_id'];
		$trans_id=$row_trans_rec['trans_id'];
		$payment_claims=$row_trans_rec['payment_claims'];
		$charge_list_detaill_id=$row_trans_rec['charge_list_detaill_id'];
		$encounter_id=$row_trans_rec['encounter_id'];
		$payment_mode=$row_trans_rec['payment_mode'];
		
		$pat_qry=imw_query("select * from patient_data where id in($patient_id)");
		$pat_details=imw_fetch_object($pat_qry);
		$pat_lname= $pat_details->lname;
		$pat_fname= $pat_details->fname;
		
		if($pat_fname){
			$comma_add=", ";
		}
		$pat_nam=$pat_lname.$comma_add.$pat_fname;
		
		$insurance_co = $ins_tbl_arr[$InsComId]['in_house_code'];
		
		
		if($InsComId>0){
			$paid_by=$insurance_co;
		}else{
			$paid_by="Patient";
		}
		
		if($row_trans_rec['payment_claims']=="Debit_Credit"){
			$crAppId_chk=$row_trans_rec['del_status'];
			$batch_crd_amt_adust=0;
			$gettot_crd3 = "SELECT sum(amountApplied) as amt_adust  FROM manual_batch_creditapplied WHERE crAppId='$crAppId_chk' and batch_id='$b_id' and charge_list_detail_id_adjust  = '$charge_list_detaill_id'  and delete_credit='0' and credit_applied='1' and post_status='0'";
			$gettot_crdQry3 = imw_query($gettot_crd3);
			$gettot_crdrow3 = imw_fetch_array($gettot_crdQry3);
			$batch_crd_amt_adust = $gettot_crdrow3['amt_adust'];
			
			$batch_deb_amt_adust=0;
			$gettot_crd4 = "SELECT sum(amountApplied) as amt_adust  FROM manual_batch_creditapplied WHERE crAppId='$crAppId_chk' and batch_id='$b_id' and charge_list_detail_id = '$charge_list_detaill_id'  and delete_credit='0' and credit_applied='1' and post_status='0'";
			$gettot_crdQry4 = imw_query($gettot_crd4);
			$gettot_crdrow4 = imw_fetch_array($gettot_crdQry4);
			$batch_deb_amt_adust = $gettot_crdrow4['amt_adust'];
		}
		$adj_trans_amt="0.00";
		if($row_trans_rec['payment_claims']=="Refund"){
			$refAppId_chk=$row_trans_rec['del_status'];
			$batch_ref_amt_adust=0;
			$gettot_crd3 = "SELECT sum(amountApplied) as amt_adust  FROM manual_batch_creditapplied WHERE crAppId='$refAppId_chk' and batch_id='$b_id' and charge_list_detail_id  = '$charge_list_detaill_id'  and delete_credit='0' and credit_applied='1' and post_status='0'";
			$gettot_crdQry3 = imw_query($gettot_crd3);
			$gettot_crdrow3 = imw_fetch_array($gettot_crdQry3);
			$adj_trans_amt = $gettot_crdrow3['amt_adust'];
			$total_adj_proc=$total_adj_proc+$adj_trans_amt;
		}
		
		if($payment_claims=='Paid' || $payment_claims=='Deposit' || $payment_claims=='Interest Payment' || $payment_claims=='CoPay'){
			$paid_trans_amt=$row_trans_rec['trans_amt'];
			$total_paid_proc=$total_paid_proc+$paid_trans_amt;
		}else{
			$paid_trans_amt="0.00";
		}
		
		if($row_trans_rec['trans_amt']<=0 && $row_trans_rec['del_status']>1 && $payment_claims=='Debit_Credit'){
			$paid_trans_amt=$paid_trans_amt+$batch_crd_amt_adust-$batch_deb_amt_adust;
			$total_paid_proc=$total_paid_proc+$paid_trans_amt;
		}
		
		if($payment_claims=='Deductible'){
			$deduct_trans_amt=$row_trans_rec['trans_amt'];
			$total_deduct_proc=$total_deduct_proc+$deduct_trans_amt;
		}else{
			$deduct_trans_amt="0.00";
		}
		
		if($payment_claims=='Discount' || $payment_claims=='Write Off' || $payment_claims=='Allowed'){
			$write_trans_amt=$row_trans_rec['trans_amt'];
			$total_write_proc=$total_write_proc+$write_trans_amt;
		}else{
			$write_trans_amt="0.00";
		}
		
		if($payment_claims=='Adjustment' || $payment_claims=='Over Adjustment'){
			$adj_trans_amt=$row_trans_rec['trans_amt'];
			$total_adj_proc=$total_adj_proc+$adj_trans_amt;
		}
		
		if($payment_claims=='Negative Payment'){
			$neg_trans_amt=$row_trans_rec['trans_amt'];
			$total_neg_proc=$total_neg_proc+$neg_trans_amt;
		}else{
			$neg_trans_amt="0.00";
		}
		
		$cptPracCode=$cpt_fee_tbl_arr[$procId]['cpt_prac_code'];
								
		$sel_tran_proc_qry=imw_query("select procCode,del_status from patient_charge_list_details where charge_list_detail_id='$charge_list_detaill_id'");
		$fet_tran_proc=imw_fetch_array($sel_tran_proc_qry);
		$trans_proc_code=$cpt_fee_tbl_arr[$fet_tran_proc['procCode']]['cpt_prac_code'];
		
		$allow_amt="";
		$sel_tran_amt_allow=imw_query("select proc_allow_amt 
						from manual_batch_transactions
						where
						encounter_id='$encounter_id'
						and charge_list_detaill_id='$charge_list_detaill_id'
						and (payment_claims='Allowed')
						and del_status=0
						and batch_id='$b_id'");
		$trans_allow_amt=imw_fetch_array($sel_tran_amt_allow);
		$trans_allow_amt_total=$trans_allow_amt['proc_allow_amt'];
		if($trans_allow_amt_total>0){
			$allow_amt=$trans_allow_amt_total;
		}else{
			$allow_amt=$row_trans_rec['proc_allow_amt'];
		}
		if($post_status==0){
			$link="onclick='chk($patient_id);load_batch_file(\"makePayment\",\"b_id=$b_id&encounter_id=$encounter_id&batch_pat_id=$patient_id&pat_srh_block=yes\")'";
		}else{
			$link="";
		}
		$total_allow_amt_arr[$charge_list_detaill_id]=$allow_amt;
		$total_proc_charges[$charge_list_detaill_id]=$row_trans_rec['proc_total_amt'];
		//$total_allow_proc=$total_allow_proc+$allow_amt;
		if($payment_claims=='CoPay'){
			$trans_proc_code="Copay";
		}
		$del_border="";
		if($fet_tran_proc['del_status']==1){
			$del_border='style="background-color:#FF6;"';
			$del_records_arr[$row_trans_rec['encounter_id'].'-'.$trans_proc_code]="CPT ".$trans_proc_code." in Encounter - ".$row_trans_rec['encounter_id']." is deleted.\n";
		}
		$arr[$i]['patient_id']= $pat_nam.' - '.$row_trans_rec['patient_id'];
		$arr[$i]['encounter_id']= $row_trans_rec['encounter_id'];
		$arr[$i]['trans_proc_code'] = $trans_proc_code;
		$arr[$i]['proc_total_amt'] = numberFormat($row_trans_rec['proc_total_amt'],2);
		$arr[$i]['allow_amt'] = numberFormat($allow_amt,2);
		$arr[$i]['deduct_trans_amt'] = numberFormat($deduct_trans_amt,2);
		$arr[$i]['write_trans_amt'] = numberFormat($write_trans_amt,2);
		$arr[$i]['adj_trans_amt'] = str_replace("&nbsp;"," ",numberFormat($adj_trans_amt,2,'yes'));
		$arr[$i]['paid_trans_amt'] = str_replace("&nbsp;"," ",numberFormat($paid_trans_amt,2,'yes'));
		$arr[$i]['neg_trans_amt'] = numberFormat($neg_trans_amt,2); 
		$arr[$i]['payment_mode'] = $payment_mode;
		$arr[$i]['paid_by'] = $paid_by;
		$show_num=$i+1;
	?>
	<?php $str .='<tr class="pointer">
		<td '.$link.'>'.$show_num.'</td>
		<td '.$link.'>'.$pat_nam.' - '.$row_trans_rec['patient_id'].'</td>
		<td '.$link.'>'.$row_trans_rec['encounter_id'].'</td>
		<td '.$link.' '.$del_border.'>'.$trans_proc_code.'</td>
		<td '.$link.' class="text-right">'.numberFormat($row_trans_rec['proc_total_amt'],2).'</td>
		<td '.$link.' class="text-right">'.numberFormat($allow_amt,2).'</td>
		<td '.$link.' class="text-right">'.numberFormat($deduct_trans_amt,2).'</td>
		<td '.$link.' class="text-right">'.numberFormat($write_trans_amt,2).'</td>
		<td '.$link.' class="text-right">'.numberFormat($adj_trans_amt,2,'yes').'</td>
		<td '.$link.' class="text-right">'.numberFormat($paid_trans_amt,2,'yes').'</td>
		<td '.$link.' class="text-right">'.numberFormat($neg_trans_amt,2).'</td>
		<td '.$link.'>'.$payment_mode.'</td>
		<td '.$link.'>'.$paid_by.'</td>
		<td>';
			if($post_status==0){
			 $str .='<a href="javascript:editTrans(\''.$b_id.'\',\''.$trans_id.'\',\''.$sort_by.'\',\''.$_REQUEST['chk_view'].'\');"   class="text_10"><img src="../../library/images/edit.png" alt="Edit" border="0"></a> 
			<a href="javascript:delTrans(\''.$b_id.'\',\''.$trans_id.'\',\''.$sort_by.'\',\''.$_REQUEST['chk_view'].'\');" class="text_10"><img src="../../library/images/del.png" alt="Del" border="0"></a>';
			}else {$str .= "-";}
		$str .= '</td> 
	</tr>';
	$i++;
	}
	$arr[$i]['patient_id'] = "";
	$arr[$i]['encounter_id'] = "";
	$arr[$i]['trans_proc_code'] = "";
	$arr[$i]['proc_total_amt'] =  numberFormat(array_sum($total_proc_charges),2);
	$arr[$i]['allow_amt'] =  numberFormat(array_sum($total_allow_amt_arr),2); 
	$arr[$i]['deduct_trans_amt'] =  numberFormat($total_deduct_proc,2); 
	$arr[$i]['write_trans_amt'] =  numberFormat($total_write_proc,2); 
	$arr[$i]['adj_trans_amt'] =  numberFormat($total_adj_proc,2); 
	$arr[$i]['paid_trans_amt'] =  numberFormat($total_paid_proc,2);
	$arr[$i]['neg_trans_amt'] =  numberFormat($total_neg_proc,2); 
	$arr[$i]['payment_mode'] = '';
	$arr[$i]['paid_by'] = '';
   $str .=  '<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="text-right purple_bar"><b>'.numberFormat(array_sum($total_proc_charges),2).'</b></td>
			<td class="text-right purple_bar"><b>'.numberFormat(array_sum($total_allow_amt_arr),2).'</b></td>
			<td class="text-right purple_bar"><b>'.numberFormat($total_deduct_proc,2).'</b></td>
			<td class="text-right purple_bar"><b>'.numberFormat($total_write_proc,2).'</b></td>
			<td class="text-right purple_bar"><b>'.numberFormat($total_adj_proc,2).'</b></td>
			<td class="text-right purple_bar"><b>'.numberFormat($total_paid_proc,2).'</b></td>
			<td class="text-right purple_bar"><b>'.numberFormat($total_neg_proc,2).'</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr><td colspan="14">';
	   
			$sel_grp_qry=imw_query("select name from patient_charge_list join groups_new on
			groups_new.gro_id=patient_charge_list.gro_id where patient_charge_list.del_status='0' and patient_charge_list.encounter_id='$encounter_id'");
			$sel_grp_data=imw_fetch_array($sel_grp_qry);
			$del_records=implode('',$del_records_arr);
			 $str .=  '<form action="#" name="grp" method="post">
				<input type="hidden" name="grp_name" id="grp_name" value="'.$sel_grp_data['name'].'">
				<input type="hidden" name="del_records" id="del_records" value="'.$del_records.'">
				
			</form>
		&nbsp;
		</td></tr>';
		}else{	
	$str .=' <tr>
		<td colspan="14">
			No Record Found.
		</td>
	</tr>';
		} 
 echo $str .='</table>';
?>
<?php
if(imw_num_rows($sel_trans_rec)>0){
	$_REQUEST['mode']="list";
	include("export_batch_transactions.php");
	$be_filePath=base64_encode($filePath);
}
?>
<input type="hidden" name="be_filePath" id="be_filePath" value="<?php echo $be_filePath; ?>">