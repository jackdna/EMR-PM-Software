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
$title = "Edit Batch Transaction";
require_once("../accounting/acc_header.php");
$operatorName = $_SESSION['authUser'];
$operatorId = $_SESSION['authUserID'];
$edit_transid=$_REQUEST['edit_transid'];
$b_id=$_REQUEST['b_id'];
$enc_id=$_REQUEST['enc_id'];
$sort_by=$_REQUEST['sort_by'];
$chk_view=$_REQUEST['chk_view'];
$act_show_view=$_REQUEST['act_show_view'];
$batch_pat_id=$_REQUEST['batch_pat_id'];
if($_REQUEST['sbmtFrm']){
	$edit_amt=$_REQUEST['edit_amt'];
	if($_REQUEST['type_chk']=='Allowed'){
		$old_edit_amt=$_REQUEST['old_edit_amt'];
		if($old_edit_amt>$edit_amt){
			$proc_allow_amt=$old_edit_amt-$edit_amt;
			$whr_all_amt=",proc_allow_amt=proc_allow_amt+$proc_allow_amt";
		}else{
			$proc_allow_amt=$edit_amt-$old_edit_amt;
			$whr_all_amt=",proc_allow_amt=proc_allow_amt-$proc_allow_amt";
		}
	}
	$up_trans=imw_query("update manual_batch_transactions set trans_amt='$edit_amt' $whr_all_amt where trans_id='$edit_transid'");
?>
	<script type="text/javascript">
		var file_name = window.opener.$("#edit_page").val();
		window.opener.load_batch_file(file_name,"<?php echo 'b_id='.$b_id.'&batch_pat_id='.$batch_pat_id.'&encounter_id='.$enc_id.'&sort_by='.$sort_by.'&chk_view='.$chk_view; ?>");
		window.close();
	</script>
<?php
}
$sel_tran=imw_query("select * from manual_batch_transactions where trans_id='$edit_transid'");
$fet_tran=imw_fetch_array($sel_tran);
$patient_id=$fet_tran['patient_id'];	
$encounter_id=$fet_tran['encounter_id'];	
$charge_list_detaill_id=$fet_tran['charge_list_detaill_id'];	
$trans_amt=$fet_tran['trans_amt'];	
$payment_claims=$fet_tran['payment_claims'];	

// GET PATIENT INFO
$pat_qry = imw_query("select * from patient_data where id = '$patient_id'");
$patientDetails = imw_fetch_object($pat_qry);
// GET DETAILS OF DENIAL

// GET CHARGES INFO
$patientChargesDetailsqRY = imw_query("select * from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'");
$patientChargesDetails = imw_fetch_object($patientChargesDetailsqRY);
$primaryInsCoId = $patientChargesDetails->primaryInsuranceCoId;

// GET  P INS CO NAMES
$getPrimaryDetailsQry = imw_query("select * from insurance_companies where id = '$primaryInsCoId'");
$getPrimaryDetails = imw_fetch_object($getPrimaryDetailsQry);
$insCoNameArr[] = $getPrimaryDetails->in_house_code;
$insCoIdArr[] = $getPrimaryDetails->id;

$secoudaryInsCoId = $patientChargesDetails->secondaryInsuranceCoId;
// GET S INS CO NAMES
$getSecondaryDetailsQry = imw_query("select * from insurance_companies where id = '$secoudaryInsCoId'");
$getSecondaryDetails = imw_fetch_object($getSecondaryDetailsQry);
$insCoNameArr[] = $getSecondaryDetails->in_house_code;
$insCoIdArr[] = $getSecondaryDetails->id;

$tertiaryInsCoId = $patientChargesDetails->tertiaryInsuranceCoId;
// GET T INS CO NAMES
$getTertiaryDetailsQry = imw_query("select * from insurance_companies where id = '$tertiaryInsCoId'");
$getTertiaryDetails = imw_fetch_object($getTertiaryDetailsQry);
$insCoNameArr[] = $getTertiaryDetails->in_house_code;
$insCoIdArr[] = $getTertiaryDetails->id;

	$chargeListDetailsQry = imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_detail_id = '$charge_list_detaill_id'");
	$chargeListDetails = imw_fetch_object($chargeListDetailsQry);
	$cpt_id = $chargeListDetails->procCode;
	$totalAmount = $chargeListDetails->totalAmount;
	$approvedAmt = $chargeListDetails->approvedAmt;
	$deductAmt = $chargeListDetails->deductAmt;
	$write_off = $chargeListDetails->write_off;
	
	$cpt4CodeDetailsQry = imw_query("select * from cpt_fee_tbl where cpt_fee_id = '$cpt_id'");
	$cpt4CodeDetails = imw_fetch_object($cpt4CodeDetailsQry);
	$cpt4_code = $cpt4CodeDetails->cpt4_code;
	$cpt_desc = $cpt4CodeDetails->cpt_desc;
	
	$sel_tran_allow=imw_query("select proc_allow_amt from manual_batch_transactions where encounter_id='$encounter_id' and payment_claims='Allowed'
						and charge_list_detaill_id = '$charge_list_detaill_id'");
	$fet_tran_allow = imw_fetch_array($sel_tran_allow);
	$proc_allow_amt=$fet_tran_allow['proc_allow_amt'];	
	
	if($proc_allow_amt>0){
		$approvedAmt=$proc_allow_amt;
	}
	
?>
<form name="editFrm" method="post" action="batch_edit_trans.php">
<input type="hidden" name="edit_transid" id="edit_transid" value="<?php echo $edit_transid; ?>">
<input type="hidden" name="b_id" id="b_id" value="<?php echo $b_id; ?>">
<input type="hidden" name="enc_id" id="enc_id" value="<?php echo $encounter_id; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
<input type="hidden" name="chk_view" id="chk_view" value="<?php echo $chk_view; ?>">
<input type="hidden" name="act_show_view" id="act_show_view" value="<?php echo $act_show_view; ?>">
<input type="hidden" name="type_chk" id="type_chk" value="<?php echo $payment_claims; ?>">
<input type="hidden" name="batch_pat_id" id="batch_pat_id" value="<?php echo $patient_id; ?>">


<div class="purple_bar">
	<div class="row">
		<div class="col-sm-4">
			<label>Edit Transaction</label>
		</div>
		<div class="col-sm-5">
			<label>Patient Name :</label><span> <?php echo $patientDetails->fname.', '.$patientDetails->lname.' ('.$patientDetails->id.')'; ?></span>
		</div>
		
		<div class="col-sm-3">
			<label>EId :</label><span> <?php echo $encounter_id; ?></span>	
		</div>
	</div>	
</div>
<table class="table table-bordered">
    <tr class="grythead">
        <th>Procedure</th>
        <th>Description</th>
        <th class="text-nowrap">Total Charges</th>
        <th>Allowed</th>
        <th class="text-nowrap"><?php echo $payment_claims; ?> Amount</th>
    </tr>
    <tr class="text-center">
        <td><?php echo $cpt4_code; ?> </td>
        <td><?php echo $cpt_desc; ?></td>
        <td><?php echo number_format($totalAmount, 2); ?></td>
        <td><?php echo number_format($approvedAmt, 2); ?></td>
        <td>
            <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                <input type="text" name="edit_amt" id="edit_amt" class="form-control" value="<?php echo $trans_amt; ?>">
            </div>
            <input type="hidden" name="old_edit_amt" id="old_edit_amt" value="<?php echo $trans_amt; ?>">
        </td>
    </tr>
</table>
<div class="row ad_modal_footer mt10">	
    <div class="col-sm-12 text-center" id="module_buttons">
        <input type="submit" name="sbmtFrm" id="sbmtFrm" class="btn btn-success" value="Update">
        <input type="button" name="CancelBtn" id="CancelBtn" class="btn btn-danger" value="Cancel" onClick="window.close();">
    </div>
</div>
</form>
