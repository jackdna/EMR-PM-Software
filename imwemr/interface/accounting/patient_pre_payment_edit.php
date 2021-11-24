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
$title = "Edit Patient Pre Payment";
require_once("acc_header.php"); 

$operatorName = $_SESSION['authUser'];
$patient_id = $_SESSION['patient'];
$operatorId = $_SESSION['authUserID'];
$edit_pay_id = $_REQUEST['edit_pay_id'];
$qry = "select * from patient_data where pid = '$patient_id'";
$res = imw_query($qry);
$row = imw_fetch_array($res);
$patient_name=ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname']));
if($_REQUEST['btn_submit']!=""){
	$edit_id=$_POST['edit_id'];
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	if($edit_id>0){
		if($payment_mode=="Cash"){
			$check_no="";
			$cc_no="";
			$credit_card_co="";
			$cc_exp_date="";
		}
		if($payment_mode=="Check" || $payment_mode=="Money Order" || $payment_mode=="VEEP"){
			$cc_no="";
			$credit_card_co="";
			$cc_exp_date="";
		}
		if($payment_mode=="Credit Card"){
			$check_no="";
		}
		$paid_amount=str_replace(',','',$paid_amount);
		$ins_qry=imw_query("update patient_pre_payment set paid_amount='$paid_amount',
		payment_mode='$payment_mode',check_no='$check_no',cc_no='$cc_no',credit_card_co='$credit_card_co',cc_exp_date='$cc_exp_date',
		modified_date='$entered_date',modified_time='$entered_time',modified_by='$operatorId' where id='$edit_id' and patient_id='$patient_id'");
	}
?>
	<script language="javascript">
        if(window.opener.parent){
		 	window.opener.location.href="check_in_out_acc.php?Check_inout_chk=Pre_payments";
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
		}else if(thisVal == 'Check'){
			$('#checkTd').css('display','block');
			$('#ccTd').css('display','none');
		}else if(thisVal == 'Credit Card'){
			$('#checkTd').css('display','none');
			$('#ccTd').css('display','block');
		}else{
			$('#checkTd').css('display','none');
			$('#ccTd').css('display','none');
		}
	}
	function frm_submit(){
		if($('#payment_mode').val()=='Credit Card' && $('#cc_no').val()==''){
			alert("Please enter the Credit Card Number.");
			return false;
		}else if($('#payment_mode').val()=='Credit Card' && $('#credit_card_co').val()==''){
			alert("Please select the Credit Card Type.");
			return false;
		}else if($('#payment_mode').val()=='Credit Card' && $('#cc_exp_date').val()==''){
			alert("Please enter the Credit Card Exp. Date.");
			return false;
		}
		$("#btn_submit").val('yes');
		$("#editWriteOffFrm").submit();
	}
</script>
	<form name="editWriteOffFrm" id="editWriteOffFrm" method="post" action="patient_pre_payment_edit.php">
    <input type="hidden" name="btn_submit" id="btn_submit" value="">	
		<div class="row" style="min-height:205px;">
			<div class="col-sm-12 purple_bar">
				<div class="row">
					<div class="col-sm-4 ">
						<span class="lead">Edit Patient Pre Payment</span>
					</div>	
					<div class="col-sm-8">
						<span class="lead"><small>Patient Name&nbsp;:&nbsp;</b><?php echo $patient_name.' ('.$patient_id.')'; ?></small></span>
					</div>	
				</div>	
			</div>
			<div class="col-sm-12 pt10">
				<div class="row">
					<div class="col-sm-3">
						<?php
							if($edit_pay_id>0)
							{
								$editRecordQry = "select * from patient_pre_payment where id ='$edit_pay_id'";
								$editRecordQryMysql = imw_query($editRecordQry);
								$editRow = imw_fetch_array($editRecordQryMysql);
							}
						?>
						<label>Pre Payment :</label>	
						<div class="input-group">
							<div class="input-group-addon "><a href=""><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></a></div>
							<input type="hidden" name="edit_id" value="<?php echo $editRow['id'] ; ?>">
							<input type="text" class="form-control" id="paid_amount" name="paid_amount" value="<?php echo $editRow['paid_amount']; ?>">
						</div>
					</div>	
				</div>
			</div>
			<div class="col-sm-12 pt10">
				<div class="row">
					<div class="col-sm-3">
						<label>Method:</label>	
						<select name="payment_mode" id="payment_mode" class="selectpicker" onChange="return changeMode(value);">
							<option <?php if($editRow['payment_mode']=="Cash"){ echo "selected='selected'"; } ?> value="Cash">Cash</option>
							<option <?php if($editRow['payment_mode']=="Check"){ echo "selected='selected'"; } ?>  value="Check">Check</option>
							<option <?php if($editRow['payment_mode']=="Credit Card"){ echo "selected='selected'"; } ?> value="Credit Card">Credit Card</option>
							<option <?php if($editRow['payment_mode']=="Money Order"){ echo "selected='selected'"; } ?> value="Money Order">Money Order</option>
							<option <?php if($editRow['payment_mode']=="VEEP"){ echo "selected='selected'"; } ?> value="VEEP">VEEP</option>
						</select>
					</div>	
					<div class="col-sm-1"></div>	
					<div class="col-sm-2" id="checkTd" style="display:<?php if($editRow['payment_mode']=="Check" or $editRow['payment_mode']=="EFT" or $editRow['payment_mode']=="Money Order" or $editRow['payment_mode']=="VEEP"){ echo 'block'; }else{ echo 'none'; } ?>;">
						<label>Check&nbsp;#:</label>	
						<input name="check_no" id="check_no" type="text" class="form-control" value="<?php echo $editRow['check_no']; ?>" />
					</div>	
					<div class="col-sm-8" id="ccTd" style="display:<?php if($editRow['payment_mode']=="Credit Card"){ echo 'block'; }else{ echo 'none'; } ?>;">
						<div class="row">
							<div class="col-sm-4">
								<label>CC&nbsp;#:</label>
								<input name="cc_no" id="cc_no" type="input_text_10" class="form-control" value="<?php echo $editRow['cc_no']; ?>" />
							</div>
							<div class="col-sm-4">
								<label>Type :</label>
								<div id="creditCardCoTd">
									 <select name="credit_card_co" id="credit_card_co" class="selectpicker" data-width="100%">
										<option value=""><?php echo imw_msg('drop_sel'); ?></option>
										<option <?php if($editRow['credit_card_co']=="AX"){ echo "selected='selected'"; } ?> value="AX">American Express</option>
										<option <?php if($editRow['credit_card_co']=="Care Credit"){ echo "selected='selected'"; } ?> value="Care Credit">Care Credit</option>
										<option <?php if($editRow['credit_card_co']=="Dis"){ echo "selected='selected'"; } ?> value="Dis">Discover</option>
										<option <?php if($editRow['credit_card_co']=="MC"){ echo "selected='selected'"; } ?> value="MC">Master Card</option>
										<option <?php if($editRow['credit_card_co']=="Visa"){ echo "selected='selected'"; } ?> value="Visa">Visa</option>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<label>Exp.&nbsp;Date :</label>
								 <input type="text" name="cc_exp_date" id="cc_exp_date" value="<?php echo $editRow['cc_exp_date']; ?>" class="form-control" />
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
        <div id="module_buttons" class="text-center pt10">
            <footer>
                <input name="UpdateBtn" id="UpdateBtn" type="button" class="btn btn-success" value="Update" onClick="frm_submit();">	
                <input name="CancelBtn" id="CancelBtn" type="button" class="btn btn-danger" value="Cancel" onClick="window.close();">
            </footer>    
        </div>	
	</form>
</body>
</html>
