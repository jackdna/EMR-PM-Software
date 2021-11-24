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
$title = "Batch Transactions List Printing";
require_once("../accounting/acc_header.php");
require_once("../../library/classes/billing_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
$batch_id=$_REQUEST['b_id'];
ob_start();
$left_margin=30;
$operator_id=$_SESSION['authId'];
$operatorDetailsQry=imw_query("select id,fname,mname,lname from users where id in($operator_id)");
$operatorDetails=imw_fetch_object($operatorDetailsQry);
$operatorName_mod = substr($operatorDetails->fname,0,1).substr($operatorDetails->lname,0,1);
ob_start();
$left_margin=30;
?>
<style>
	.tb_heading{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		color:#FFFFFF;
		background-color:#4684ab;
	}
	.text_b{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		background-color:#FFFFFF;
	}
	.text_10{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#FFFFFF;
	}
	.font_14b{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		background-color:#FFFFFF;
	}
</style>
<page backtop="17mm" backbottom="7mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>
    	<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
            	<td width="<?php echo $left_margin; ?>">&nbsp;</td>
                <td class="tb_heading" width="350" style="height:25px;">Batch Processing Report</td>
                <td class="tb_heading" width="440" style="height:25px;">
                <?php
					$sel_batch_qry=imw_query("select * from manual_batch_file where batch_id='$b_id'");
					$row_batch_qry=imw_fetch_array($sel_batch_qry);
				?>
                Batch Tracking# : <?php echo $row_batch_qry['tracking']; ?>
                </td>
                <td class="tb_heading" width="240" style="text-align:center;">
                    Created By <?php echo $operatorName_mod; ?> on <?php echo date("".phpDateFormat()." h:m A"); ?>
                </td>
            </tr>
            <tr><td style="padding-bottom:2px;"></td></tr>
        </table>	
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="<?php echo $left_margin; ?>">&nbsp;</td>
				<td width="200" class="tb_heading">Patient ID</td>
				<td width="80" class="tb_heading">E.Id</td>
				<td width="70" class="tb_heading">C.P.T.</td>
                <td width="70" class="tb_heading">Check No.</td>
				<td width="70" class="tb_heading" align="right">T.Charges</td>
				<td width="80" class="tb_heading" align="right">Allowed</td>
				<td width="80" class="tb_heading" align="right">Deductible</td>
				<td width="70" class="tb_heading" align="right">Write off</td>
				<td width="60" class="tb_heading" align="right">Adj</td>
				<td width="70" class="tb_heading" align="right">Paid</td>
                <td width="80" class="tb_heading" align="right">Negative Amt</td>
				<td width="10" align="right" class="tb_heading">&nbsp;</td>
				<td width="50" class="tb_heading">Paid By</td>
			</tr>
		</table>
	</page_header>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<?php
		$final_tot_proc_amt=array();
		$final_allow_proc_amt=array();
		$total_proc_charges=array();
		$total_paid_proc="";
		$total_deduct_proc="";
		$total_write_proc="";
		$total_allow_proc=array();
		if($sort_by=="encounter"){
			$order_by="manual_batch_transactions.encounter_id";
		}else if($sort_by=="patient"){
			$order_by="patient_data.lname,patient_data.fname";
		}else{
			$order_by="manual_batch_transactions.trans_id";
		}
		$default_check_no_arr=array();
		$res_rec="no";
		$sel_file=imw_query("select default_check_no from manual_batch_file where batch_id='$b_id'");
		$row_file=imw_fetch_array($sel_file);
		$default_check_no_arr=explode(',',$row_file['default_check_no']);
		$default_check_no_arr = array_unique($default_check_no_arr);
		sort($default_check_no_arr);
		if($default_check_no_arr[0] == ''){
			unset($default_check_no_arr[0]);
		}
		$default_check_no_arr = array_values($default_check_no_arr);
		$i = 0;$j=$i+1;
		$show_num=0;
		for($ii=0;$ii<count($default_check_no_arr);$ii++){
		$def_chk_no=$default_check_no_arr[$ii];
		$detail_chk_data="";
		$total_proc_charges="";
		$total_allow_amt_arr="";
		$total_deduct_proc="0";
		$total_proc_charges="";
		$total_write_proc="0";
		$total_adj_proc="0";
		$total_paid_proc="0";
		$total_neg_proc="0";
		$sel_trans_rec=imw_query("select manual_batch_transactions.*
				from
			manual_batch_transactions as manual_batch_transactions,
			patient_data as  patient_data
				where 
			patient_data.id=manual_batch_transactions.patient_id
			and manual_batch_transactions.batch_id='$b_id' 
			and manual_batch_transactions.del_status!='1' 
			and manual_batch_transactions.payment_mode = 'Check'
			and manual_batch_transactions.check_no = '$def_chk_no'
			order by $order_by");
			//and manual_batch_transactions.payment_claims!='Allowed'
			$d = 0;
			$res_rec="yes";
			while($row_trans_rec=imw_fetch_array($sel_trans_rec)){
				$InsComId=$row_trans_rec['insurance_id'];
				$patient_id=$row_trans_rec['patient_id'];
				$trans_id=$row_trans_rec['trans_id'];
				$payment_claims=$row_trans_rec['payment_claims'];
				$charge_list_detaill_id=$row_trans_rec['charge_list_detaill_id'];
				$encounter_id=$row_trans_rec['encounter_id'];
				
				//$total_proc_charges=$total_proc_charges+$row_trans_rec['proc_total_amt'];
				
				$pat_qry=imw_query("select * from patient_data where id in($patient_id)");
				$pat_details=imw_fetch_object($pat_qry);
				$pat_lname= $pat_details->lname;
				$pat_fname= $pat_details->fname;
				
				if($pat_fname){
					$comma_add=", ";
				}
				$pat_nam=$pat_lname.$comma_add.$pat_fname;
				
				$ins_qry=imw_query("select * from insurance_companies where id in($InsComId)");
				$InsComDetails=imw_fetch_object($ins_qry);
				$insurance_co = $InsComDetails->in_house_code;
				
				
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
				
				
				$getCptFeeDetailsStr = "SELECT * FROM cpt_fee_tbl WHERE cpt_fee_id = '$procId' AND delete_status = '0'";
				$getCptFeeDetailsQry = imw_query($getCptFeeDetailsStr);
				$getCptFeeDetailsRow = imw_fetch_array($getCptFeeDetailsQry);
				$cptPracCode = $getCptFeeDetailsRow['cpt_prac_code'];
										
				$sel_tran_proc_qry=imw_query("select b.cpt_prac_code,a.del_status from
							patient_charge_list_details as a,
							cpt_fee_tbl as b
							where
							a.procCode=b.cpt_fee_id
							and a.charge_list_detail_id='$charge_list_detaill_id'");
				$fet_tran_proc=imw_fetch_array($sel_tran_proc_qry);
				$trans_proc_code=$fet_tran_proc['cpt_prac_code'];	
				
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
					$link="batch_transactions.php?b_id=$b_id&enc_id=$encounter_id";
				}else{
					$link="batch_transactions.php?b_id=$b_id";
				}
				$total_allow_amt_arr[$charge_list_detaill_id]=$allow_amt;
				$total_proc_charges[$charge_list_detaill_id]=$row_trans_rec['proc_total_amt'];
				$final_tot_proc_amt[$charge_list_detaill_id]=$row_trans_rec['proc_total_amt'];
				$final_allow_proc_amt[$charge_list_detaill_id]=$allow_amt;
				//$total_allow_proc=$total_allow_proc+$allow_amt;
			$d++;
			$act_show_view=$def_chk_no."---".$d;
			if($payment_claims=='CoPay'){
				$trans_proc_code="Copay";
			}
			$detail_chk_data .='<tr bgcolor="#E1E5EF">	
				<td width="'.$left_margin.'">&nbsp;</td>		
				<td width="200" class="text_10">'.$pat_nam.' - '.$row_trans_rec['patient_id'].'</td>
				<td width="80" class="text_10">'.$row_trans_rec['encounter_id'].'</td>
				<td width="70" class="text_10">'.$trans_proc_code.'</td>
                <td width="70" class="text_10" align="right">&nbsp;</td>
				<td width="70" class="text_10" align="right">'.numberFormat($row_trans_rec['proc_total_amt'],2).'</td>
				<td width="80" class="text_10" align="right">'.numberFormat($allow_amt,2).'</td>
				<td width="80" class="text_10" align="right">'.numberFormat($deduct_trans_amt,2,'yes').'</td>
				<td width="70" class="text_10" align="right">'.numberFormat($write_trans_amt,2,'yes').'</td>
				<td width="60" class="text_10" align="right">'.numberFormat($adj_trans_amt,2,'yes').'</td>
				<td width="70" class="text_10" align="right">'.numberFormat($paid_trans_amt,2,'yes').'</td>
				<td width="70" class="text_10" align="right">'.numberFormat($neg_trans_amt,2,'yes').'</td>
				<td width="20">&nbsp;</td>
				<td width="90" class="text_10">'.$paid_by.'</td>
				</tr>';
			 } 
			 $final_deduct_proc_amt[]=$total_deduct_proc;
			 $final_wrt_proc_amt[]=$total_write_proc;
			 $final_adj_proc_amt[]=$total_adj_proc;
			 $final_paid_proc_amt[]=$total_paid_proc;
			 $final_neg_paid_proc_amt[]=$total_neg_proc;
			?>
		<tr class="text_10">
			<td width="<?php echo $left_margin;?>">&nbsp;</td>
			<td class="text_b" width="200">&nbsp;</td>
			<td class="text_b" width="80">&nbsp;</td>
            <td class="text_b" width="70">&nbsp;</td>
            <td class="text_b" width="70" align="right"><?php echo $def_chk_no;?></td>
			<td class="text_b" width="70" align="right"><?php echo numberFormat(array_sum($total_proc_charges),2); ?></td>
			<td class="text_b" width="80" align="right"><?php echo numberFormat(array_sum($total_allow_amt_arr),2); ?></td>
			<td class="text_b" width="80" align="right"><?php echo numberFormat($total_deduct_proc,2,'yes'); ?></td>
			<td class="text_b" width="70" align="right"><?php echo numberFormat($total_write_proc,2,'yes'); ?></td>
			<td class="text_b" width="60" align="right"><?php echo numberFormat($total_adj_proc,2,'yes'); ?></td>
			<td class="text_b" width="70" align="right"><?php echo numberFormat($total_paid_proc,2,'yes'); ?></td>
            <td class="text_b" width="70" align="right"><?php echo numberFormat($total_neg_proc,2,'yes'); ?></td>
			<td class="text_10" width="20">&nbsp;</td>
			<td class="text_10" width="90">&nbsp;</td>
		</tr>
        <?php echo $detail_chk_data; ?>
	<?php  } ?>
    	<tr>
			<td colspan="5"></td>
			<td colspan="7" height="1px" bgcolor="#009933"></td>
			<td colspan="3"></td>
		</tr>
		<tr class="text_10">
			<td width="<?php echo $left_margin;?>">&nbsp;</td>
			<td class="text_10">&nbsp;</td>
			<td class="text_10">&nbsp;</td>
			<td class="text_10">&nbsp;</td>
            <td class="text_10">&nbsp;</td>
			<td align="right"><?php echo numberFormat(array_sum($final_tot_proc_amt),2); ?></td>
            <td align="right"><?php echo numberFormat(array_sum($final_allow_proc_amt),2); ?></td>
            <td align="right"><?php echo numberFormat(array_sum($final_deduct_proc_amt),2); ?></td>
            <td align="right"><?php echo numberFormat(array_sum($final_wrt_proc_amt),2); ?></td>
            <td align="right"><?php echo numberFormat(array_sum($final_adj_proc_amt),2); ?></td>
            <td align="right"><?php echo numberFormat(array_sum($final_paid_proc_amt),2); ?></td>
            <td align="right"><?php echo numberFormat(array_sum($final_neg_paid_proc_amt),2); ?></td>
			<td class="text_10">&nbsp;</td>
			<td class="text_10">&nbsp;</td>
			<td width="13" class="text_10">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5"></td>
			<td colspan="7" height="1px" bgcolor="#009933"></td>
			<td colspan="3"></td>
		</tr>
	</table>
</page>
<?php
$file_content = ob_get_contents();
ob_end_clean();

if($file_content){
		$filePath=write_html($file_content);
	?>
	<script type="text/javascript">
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
		html_to_pdf('<?php echo $filePath; ?>','l','',true);
	</script>
<?php
}
?>
