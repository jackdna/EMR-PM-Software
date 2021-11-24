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
$title = "Batch Transactions List";
require_once("../accounting/acc_header.php");
$patient_id=$_SESSION['patient'];
$operator_id=$_SESSION['authId'];
$b_id=$_REQUEST['b_id'];
?>
<script type="text/javascript">
function comp_balance(){
	document.batch_file.submit();
}
function post_file(){
	var id=document.getElementById('edit_id').value;
	window.open("batch_post_acc.php?post_id="+id,'','width=550,height=185,top=200,left=190,location=0,scrollbars=no,resizable=1');
}
</script>
<?php
if($_REQUEST['edit_id']){
	$edit_id=$_REQUEST['edit_id'];
	$total_bill_amount=$_REQUEST['total_bill_amount'];
	$total_allow_amount=$_REQUEST['total_allow_amount'];
	$total_payment=$_REQUEST['total_payment'];
	$total_writeoff=$_REQUEST['total_writeoff'];
	$total_adj=$_REQUEST['total_adj'];
	imw_query("update manual_batch_file set total_bill_amount='$total_bill_amount',
				total_allow_amount='$total_allow_amount',total_payment='$total_payment',
				total_writeoff_amt='$total_writeoff',total_adj_amt='$total_adj'
				where batch_id='$edit_id'");
}
if($b_id){
	$sel_batch=imw_query("select * from manual_batch_file where batch_id='$b_id'");
	$row_batch=imw_fetch_array($sel_batch);
	$batch_name=$row_batch['batch_name'];
	$batch_owner_id=$row_batch['batch_owner'];
	$default_write_code=$row_batch['default_write_code'];
	$default_adj_code=$row_batch['default_adj_code'];
	$tracking=$row_batch['tracking'];
	$total_bill_amount=$row_batch['total_bill_amount'];
	$total_allow_amount=$row_batch['total_allow_amount'];
	$total_adj_amt=$row_batch['total_adj_amt'];
	$total_writeoff_amt=$row_batch['total_writeoff_amt'];
	$total_payment=$row_batch['total_payment'];
}
$total_proc_charges=array();
$total_allow_charges=array();
$total_paid_amt=0;
$total_write_amt=0;
$sel_trans_rec=imw_query("select 
			proc_total_amt,proc_allow_amt,payment_claims,
			trans_amt,encounter_id,charge_list_detaill_id
				from
			manual_batch_transactions 
				where 
			batch_id='$b_id' and del_status!='1'");
while($row_trans_rec=imw_fetch_array($sel_trans_rec)){
	$encounter_id=$row_trans_rec['encounter_id'];
	$charge_list_detaill_id=$row_trans_rec['charge_list_detaill_id'];
	$payment_claims=$row_trans_rec['payment_claims'];
	$trans_amt=$row_trans_rec['trans_amt'];
	$total_proc_charges[$charge_list_detaill_id]=$row_trans_rec['proc_total_amt'];
	if($payment_claims=='Paid' || $payment_claims=='Deposit' || $payment_claims=='Interest Payment' || $payment_claims=='CoPay'){
		$total_paid_amt=$total_paid_amt+$row_trans_rec['trans_amt'];
	}
	
	if($payment_claims=='Write Off' || $payment_claims=='Discount' || $payment_claims=='Allowed'){
		$total_write_amt=$total_write_amt+$row_trans_rec['trans_amt'];
	}
	
	if($payment_claims=='Over Adjustment' || $payment_claims=='Adjustment'){
		$total_adj_amt_trans=$total_adj_amt_trans+$row_trans_rec['trans_amt'];
	}
	
	if($payment_claims=='Negative Payment'){
		$total_neg_amt=$total_neg_amt+$row_trans_rec['trans_amt'];
	}
	
	$allow_amt="";
	$sel_tran_amt_allow=imw_query("select proc_allow_amt 
					from manual_batch_transactions
					where
					encounter_id='$encounter_id'
					and charge_list_detaill_id='$charge_list_detaill_id'
					and (payment_claims='Allowed')
					and del_status=0");
	$trans_allow_amt=imw_fetch_array($sel_tran_amt_allow);
	$trans_allow_amt_total=$trans_allow_amt['proc_allow_amt'];
	if($trans_allow_amt_total>0){
		$allow_amt=$trans_allow_amt_total;
	}else{
		$allow_amt=$row_trans_rec['proc_allow_amt'];
	}
	
	$total_allow_charges[$charge_list_detaill_id]=$allow_amt;
	
}			
if($total_bill_amount>0){
	$final_total_charge_amt=$total_bill_amount;
}else{
	$final_total_charge_amt=array_sum($total_proc_charges);
}

if($total_allow_amount>0){
	$final_total_allow_amount=$total_allow_amount;
}else{
	$final_total_allow_amount=array_sum($total_allow_charges);
}

if($total_payment>0){
	$final_total_paid_amount=$total_payment;
}else{
	$final_total_paid_amount=$total_paid_amt;
}

if($total_adj_amt>0){
	$final_total_adj_amount=$total_adj_amt;
}else{
	$final_total_adj_amount=$total_adj_amt_trans;
}

$operatorDetailsQry=imw_query("select id,fname,mname,lname from users where id in($batch_owner_id)");
$operatorDetails=imw_fetch_object($operatorDetailsQry);
$batch_owner = substr($operatorDetails->fname,0,1).''.substr($operatorDetails->lname,0,1);
$balance_status="open";
if($total_neg_amt>0){
	$chk_total_paid_amt=$total_paid_amt-$total_neg_amt;
}else{
	$chk_total_paid_amt=$total_paid_amt;
}
if($bal_comp=="1"){
	if(round($total_payment,2)==round($chk_total_paid_amt,2)){
		$balance_status="Balanced";
	}else{
		$balance_status="Out of Balance";
	}
}
if($total_writeoff_amt>0){
	$total_write_amt=$total_writeoff_amt;
}
?>
<form name="batch_file" method="post" action="chk_balance_batch.php">
<input type="hidden" name="edit_id" id="edit_id" value="<?php echo $_REQUEST['b_id'];?>">
<input type="hidden" name="b_id" id="b_id" value="<?php echo $_REQUEST['b_id'];?>">
<input type="hidden" id="bal_comp" name="bal_comp" value="1">
<div class="purple_bar"><label>Balance Batch</label></div>
<div class="row pt10">
    <div class="col-sm-3">
        <label for="batch_name">Batch Name</label>
        <input type="text" name="batch_name" id="batch_name" value="<?php print $batch_name;?>" class="form-control">	
    </div>
    <div class="col-sm-3">
        <label for="tracking">Tracking#</label>
        <input type="text" name="tracking" id="tracking" value="<?php print $tracking;?>" class="form-control">	
    </div>
    <div class="col-sm-3">
        <label for="batch_owner">Batch Owner</label>
        <input type="text" name="batch_owner" id="batch_owner" value="<?php print $batch_owner;?>" class="form-control">	
    </div>
    <div class="col-sm-3">
        <label for="batch_status">Status</label>
        <input type="text" name="batch_status" id="batch_status" value="<?php echo $balance_status; ?>" class="form-control">
    </div>
</div> 
<div class="row pt10">
    <div class="col-sm-3">
        <label for="total_bill_amount">Total Charged</label>
         <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
			<input type="text" name="total_bill_amount" id="total_bill_amount" value="<?php echo numberformat($final_total_charge_amt,2,'yes','','no','yes');?>" class="form-control">	
        </div>
    </div>
    <div class="col-sm-2">
        <label for="total_allow_amount">Total Allowed</label>
         <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
			<input type="text" name="total_allow_amount" id="total_allow_amount" value="<?php echo numberformat($final_total_allow_amount,2,'yes','','no','yes');?>" class="form-control">	
        </div>
    </div>
    <div class="col-sm-2">
        <label for="total_writeoff">Total Write Off</label>
        <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
			<input type="text" name="total_writeoff" id="total_writeoff" value="<?php echo numberformat($total_write_amt,2,'yes','','no','yes');?>" class="form-control">	
        </div>
    </div>
    <div class="col-sm-2">
        <label for="total_adj">Total Adj.</label>
        <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
			<input type="text" name="total_adj" id="total_adj" value="<?php echo numberformat($final_total_adj_amount,2,'yes','','no','yes');?>" class="form-control">	
        </div>
    </div>
    <div class="col-sm-3">
        <label for="total_payment">Total Paid</label>
        <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
			<input type="text" name="total_payment" id="total_payment" value="<?php echo numberformat($total_payment,2,'yes','','no','yes');?>" class="form-control">	
        </div>
    </div>
</div>   
<div class="row ad_modal_footer mt10">	
    <div class="col-sm-12 text-center" id="module_buttons">
        <input type="button" name="balance_batch" id="balance_batch" class="btn btn-success" value="Balance Batch" onClick="return comp_balance();">
        <input type="button" name="close" id="close" class="btn btn-danger" value="Close" onClick="window.close();">
    </div>
</div>
</form>
<?php
if($balance_status=='Balanced'){
	echo"<script>post_file();</script>";
}
?>
</body>
</html>