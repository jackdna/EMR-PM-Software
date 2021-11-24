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
$title = "Edit CI/CO Payment";
require_once("acc_header.php");  
$operatorName = $_SESSION['authUser'];
$patient_id = $_SESSION['patient'];
$operatorId = $_SESSION['authUserID'];
$edit_pay_id = $_REQUEST['edit_pay_id'];
$edit_detail_id = $_REQUEST['edit_detail_id'];
$paymentMode=$_REQUEST['paymentMode'];
$checkNo=$_REQUEST['checkNo'];
$cCNo=$_REQUEST['cCNo'];
$creditCardCo=$_REQUEST['creditCardCo'];
$expireDate=$_REQUEST['expireDate'];
if($paymentMode=='Check' or $paymentMode=='EFT' or $paymentMode=='Money Order' or $paymentMode=='VEEP'){
	$checkNo=$checkNo;
	$cCNo="";
	$creditCardCo="";
	$expireDate="";
}else if($paymentMode=='Credit Card'){
	$checkNo="";
	$cCNo=$cCNo;
	$creditCardCo=$creditCardCo;
	$expireDate=$expireDate;
}else{
	$checkNo="";
	$cCNo="";
	$creditCardCo="";
	$expireDate="";
}
$qry = "select * from patient_data where pid = '$patient_id'";
$res = imw_query($qry);
$row = imw_fetch_array($res);
$patient_name=ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname']));

if($_REQUEST['UpdateBtn']){
	$pay_arr=$_POST['pay_arr'];
	$edit_pay_id=$_POST['edit_pay_id'];
	$cur_date=date('Y-m-d');
	$cur_time=date('h:i A');
	for($i=0;$i<count($pay_arr);$i++){
		$pay_id=$pay_arr[$i];
		$pay_amt=str_replace(',','',$_POST['pay_amt_'.$pay_id]);
		$pay_old_amt=$_POST['pay_old_amt_'.$pay_id];
		$cico_detail=imw_query("update check_in_out_payment_details set item_payment='$pay_amt' where id='$pay_id'");
	}
	$sel_pay=imw_query("select sum(item_payment) as tot_pay_amt  from check_in_out_payment_details where payment_id='$edit_pay_id' and status='0'");
	$row_pay=imw_fetch_array($sel_pay);
	$main_cico_pay=$row_pay['tot_pay_amt'];					
	$cico_detail=imw_query("update check_in_out_payment set total_payment=$main_cico_pay,
							payment_method='$paymentMode',check_no='$checkNo',cc_type='$creditCardCo',
							cc_no='$cCNo',cc_expire_date='$expireDate',modified_on='$cur_date',
							modified_time='$cur_time',modified_by='$operatorId'
							where payment_id='$edit_pay_id'");
?>
	<script language="javascript">
        if(window.opener.parent){
		 	window.opener.location.href="check_in_out_acc.php?Check_inout_chk=Pre_payments&show_pay_trans_id=<?php echo $edit_pay_id; ?>";
		   window.close();
        }
    </script>
<?php	
}
?>
<script type="text/javascript">
	function changeMode(thisVal){
		if(thisVal == 'Cash'){
			$('#checkTd').css('display','none');
			$('#ccTd').css('display','none');
		}else if(thisVal == 'Check' || thisVal=="EFT" || thisVal=="Money Order" || thisVal=="VEEP"){
			$('#checkTd').css('display','block');
			$('#ccTd').css('display','none');
		}else if(thisVal == 'Credit Card'){
			$('#checkTd').css('display','none');
			$('#ccTd').css('display','block');
		}
	}
</script>

	<?php
    $item_ids_arr=array();
    $item_names_arr=array();
    $sel_row=imw_query("select * from check_in_out_fields");
    while($fet_row=imw_fetch_array($sel_row)){
        $item_ids_arr[]=$fet_row['id'];
        $item_names_arr[$fet_row['id']]=$fet_row['item_name'];
    }
    ?>
	<form name="editWriteOffFrm" method="post" action="cico_payment_edit.php">	
		<input type="hidden" value="<?php echo $edit_pay_id; ?>" name="edit_pay_id">
		<input type="hidden" value="<?php echo $edit_detail_id; ?>" name="edit_detail_id">
		<div class="row" style="min-height:210px;">
			<div class="col-sm-12 purple_bar">
				<div class="row">
					<div class="col-sm-4 ">
						<span class="lead">Edit CI/CO Transactions</span>
					</div>	
					<div class="col-sm-8">
						<span class="lead"><small>Patient Name&nbsp;:&nbsp;</b><?php echo $patient_name.' ('.$patient_id.')'; ?></small></span>
					</div>	
				</div>	
			</div>
			
			<div class="col-sm-12">
				<div class="row">
					<?php
							if($edit_detail_id>0){
								$whr_detail=" and check_in_out_payment_details.id = '$edit_detail_id'";
							}
							$pay_query = imw_query("select check_in_out_payment_details.item_payment,check_in_out_payment_details.item_id,
							check_in_out_payment_details.id,check_in_out_payment.payment_method,check_in_out_payment.check_no,
							check_in_out_payment.cc_type,check_in_out_payment.cc_no,check_in_out_payment.cc_expire_date
								from  
							check_in_out_payment join check_in_out_payment_details
							on check_in_out_payment.payment_id = check_in_out_payment_details.payment_id
							where check_in_out_payment.del_status = '0' 
							and check_in_out_payment_details.status = '0'
							and check_in_out_payment.payment_id= '$edit_pay_id' $whr_detail");
							while($payQryRes = imw_fetch_array($pay_query)){
							$payment_mode=$payQryRes['payment_method'];
							$checkNo=$payQryRes['check_no'];
							$CCNo=$payQryRes['cc_no'];
							$creditCardCo=$payQryRes['cc_type'];
							$expirationDate=$payQryRes['cc_expire_date'];
						?>
							<div class="col-sm-2 pt10">
								<label><?php echo $item_names_arr[$payQryRes['item_id']]; ?> : </label>
								<input type="hidden" name="pay_arr[]" value="<?php echo $payQryRes['id'];?>">
								<input type="hidden" class="input_text_10" id="pay_old_amt_<?php echo $payQryRes['id'];?>" name="pay_old_amt_<?php echo $payQryRes['id'];?>" size="12" value="<?php echo number_format($payQryRes['item_payment'],2); ?>">
								<input type="text" class="form-control" id="pay_amt_<?php echo $payQryRes['id'];?>" name="pay_amt_<?php echo $payQryRes['id'];?>" value="<?php echo number_format($payQryRes['item_payment'],2); ?>">
							</div>
							<?php if($i%2){ echo "</tr><tr >";}?>    
							<?php 
							} 
							if($i%2!=0){
								echo "<td class='text-left' colspan='2'>&nbsp;</td>";
							}
						?>
				</div>				
			</div>
			<div class="col-sm-12 pt10">
				<div class="row">
					<div class="col-sm-3">
						<label>Method:</label>	
						<select name="paymentMode" id="paymentMode" class="selectpicker" onChange="return changeMode(value);">
							<option value="Cash" <?php if($payment_mode=="Cash") echo 'SELECTED'; ?>>Cash</option>
							<option value="Check" <?php if($payment_mode=="Check") echo 'SELECTED'; ?>>Check</option>
							<option value="Credit Card" <?php if($payment_mode=="Credit Card") echo 'SELECTED'; ?>>Credit Card</option>
							<option value="EFT" <?php if($payment_mode=="EFT") echo 'SELECTED'; ?>>EFT</option>
							<option value="Money Order" <?php if($payment_mode=="Money Order") echo 'SELECTED'; ?>>Money Order</option>
							<option value="VEEP" <?php if($payment_mode=="VEEP") echo 'SELECTED'; ?>>VEEP</option>
						</select>
					</div>	
					<div class="col-sm-1"></div>	
					<div class="col-sm-2" id="checkTd" style="display:<?php if($payment_mode=="Check" or $payment_mode=="EFT" or $payment_mode=="Money Order" or $payment_mode=="VEEP"){ echo 'block'; }else{ echo 'none'; } ?>;">
						<label>Check&nbsp;#:</label>	
						<input name="checkNo" type="text" class="form-control" value="<?php echo $checkNo; ?>" />
					</div>	
					<div class="col-sm-8" id="ccTd" style="display:<?php if($payment_mode=="Credit Card"){ echo 'block'; }else{ echo 'none'; } ?>;">
						<div class="row">
							<div class="col-sm-4">
								<label>CC&nbsp;#:</label>
								<input name="cCNo" type="input_text_10" class="form-control" value="<?php echo $CCNo; ?>" />
							</div>
							<div class="col-sm-4">
								<label>Type :</label>
								<div id="creditCardCoTd">
									<select name="creditCardCo" class="selectpicker" data-width="100%">
										<option value=""><?php echo imw_msg('drop_sel'); ?></option>
										<option value="AX" <?php if($creditCardCo == "AX") echo 'SELECTED'; ?>>American Express</option>
										<option value="Care Credit" <?php if($creditCardCo == "Care Credit") echo 'SELECTED'; ?>>Care Credit</option>
										<option value="Dis" <?php if($creditCardCo == "Dis") echo 'SELECTED'; ?>>Discover</option>
										<option value="MC" <?php if($creditCardCo == "MC") echo 'SELECTED'; ?>>Master Card</option>
										<option value="Visa" <?php if($creditCardCo == "Visa") echo 'SELECTED'; ?>>Visa</option>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<label>Exp.&nbsp;Date :</label>
								<input type="text" name="expireDate" value="<?php echo $expirationDate; ?>" class="form-control" />
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
        <div class="pt10">
            <footer>
                <div class="text-center" id="module_buttons">
                    <input name="UpdateBtn" id="UpdateBtn" type="submit" class="btn btn-success" value="Update">
                    <input name="CancelBtn" id="CancelBtn" type="button" class="btn btn-danger" value="Cancel" onClick="window.close();">	
                </div>
           </footer>
       </div>     
	</form>
</body>
</html>
