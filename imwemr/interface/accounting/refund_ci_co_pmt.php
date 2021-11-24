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
$title = "Refund CI/CO Payments";
require_once("acc_header.php");  

$operatorName = $_SESSION['authUser'];
$patient_id = $_SESSION['patient'];
$operatorId = $_SESSION['authUserID'];

$login_facility=$_SESSION['login_facility'];
$pos_device=false;
$devices_sql="Select *, tsys_device_details.id as d_id from tsys_device_details 
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
              WHERE device_status=0 
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND merchant_status=0
              ";
$resp = imw_query($devices_sql);
if($resp && imw_num_rows($resp)>0){
    $pos_device=true;
}

$qry = "select * from patient_data where pid = '$patient_id'";
$res = imw_query($qry);
$row = imw_fetch_array($res);
$pt_dob = get_date_format($row['DOB']);
$pt_age = show_age($row['DOB']);
$patient_name=ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname']));

$item_ids_arr=array();
$item_names_arr=array();
$sel_row=imw_query("select * from check_in_out_fields");
while($fet_row=imw_fetch_array($sel_row)){
	$item_ids_arr[]=$fet_row['id'];
	$item_names_arr[$fet_row['id']]=$fet_row['item_name'];
}

if($_REQUEST['tot_cont']>0 && $patient_id>0){
	for($j=1;$j<=$_REQUEST['tot_cont'];$j++){
		$entered_date=date('Y-m-d');
		$entered_time=date('H:i:s');
		$payment_mode=$_REQUEST['payment_mode'];
		$check_no="";
		$cc_no="";
		$cc_co="";
		$cc_expiration_date="";
		if($_REQUEST['payment_mode']=="Check"){
			$check_no=$_REQUEST['check_no'];
		}
		if($_REQUEST['payment_mode']=="Credit Card"){
			$cc_no=$_REQUEST['cCNo'];
			$cc_co=$_REQUEST['creditCardCo'];
			$cc_expiration_date=$_REQUEST['expireDate'];
            if(isset($_REQUEST['card_details_str_id']) && $_REQUEST['card_details_str_id']!=''){
                $card_details_arr=explode('~~',trim($_REQUEST['card_details_str_id']));
                $cc_co=$card_details_arr[0];
                $cc_no=$card_details_arr[1];
                $cc_expiration_date=$card_details_arr[2];
            }
		}
		if($_REQUEST['ci_ref_chk'][$j]>0){
			$ci_pay_id=$_REQUEST['ci_ref_chk'][$j];
			if($_REQUEST['ci_ref_amt'][$ci_pay_id]>0){
				$ref_amt=str_replace(',','',$_REQUEST['ci_ref_amt'][$ci_pay_id]);
				imw_query("insert into ci_pmt_ref set ci_co_id='$ci_pay_id',ref_amt='$ref_amt',payment_method='$payment_mode',check_no='$check_no',
							patient_id='$patient_id',entered_date='$entered_date',entered_time='$entered_time',entered_by='$operatorId',
							cc_no='$cc_no',cc_co='$cc_co',cc_expiration_date='$cc_expiration_date'");
			}
		}
		if($_REQUEST['pmt_ref_chk'][$j]>0){
			$pmt_pay_id=$_REQUEST['pmt_ref_chk'][$j];
			if($_REQUEST['pmt_ref_amt'][$pmt_pay_id]>0){
				$ref_amt=str_replace(',','',$_REQUEST['pmt_ref_amt'][$pmt_pay_id]);
				imw_query("insert into ci_pmt_ref set pmt_id='$pmt_pay_id',ref_amt='$ref_amt',payment_method='$payment_mode',check_no='$check_no',
							patient_id='$patient_id',entered_date='$entered_date',entered_time='$entered_time',entered_by='$operatorId',
							cc_no='$cc_no',cc_co='$cc_co',cc_expiration_date='$cc_expiration_date'");
			}
		}
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
//print_r($_REQUEST);
?>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">

<script type="text/javascript">
    var pos_device='<?php echo $pos_device;?>';
    
	function removeCommas(val){
		if(val.indexOf(",") != -1){
			do{
				var val = val.replace(",", "");
			}while(val.indexOf(",") != -1)
		}
		if(val.indexOf("$") != -1){
			do{
				var val = val.replace("$", "");
			}while(val.indexOf("$") != -1)
		}
		return val;
	}
	function changeMode(thisVal){
        var no_pos_device=false;
        if($('#tsys_device_url').val()=='no_pos_device') {
            no_pos_device=true;
        }
		if(thisVal == 'Cash'){
			if($('#checkTd').hasClass('hide') === false){
				$('#checkTd').addClass('hide');
			}
			
			if($('#ccTd').hasClass('hide') === false){
				$('#ccTd').addClass('hide');
			}
		}else if(thisVal == 'Check'){
			if($('#checkTd').hasClass('hide') === true){
				$('#checkTd').removeClass('hide');
			}
			
			if($('#ccTd').hasClass('hide') === false){
				$('#ccTd').addClass('hide');
			}
		}else if(thisVal == 'Credit Card'){
			if($('#checkTd').hasClass('hide') === false){
				$('#checkTd').addClass('hide');
			}
			
			if($('#ccTd').hasClass('hide') === true){
				$('#ccTd').removeClass('hide');
			}
            if(typeof(pos_device)!='undefined' && pos_device && no_pos_device==false) {
            	uncheckOther(thisVal);
			}
		}
	}
	function set_ref(id,type){
		if(document.getElementById(type+'ref_chk_'+id).checked==true){
			var ref_pending_amt=document.getElementById(type+'ref_pending_amt_'+id).value;
			document.getElementById(type+'ref_amt_'+id).value=ref_pending_amt;
		}else{
			document.getElementById(type+'ref_amt_'+id).value="";
		}
		tot_ref(0);
	}
	function tot_ref(id){
		if(document.getElementById('ci_ref_chk_'+id)){
			document.getElementById('ci_ref_chk_'+id).checked=true;
		}
		if(document.getElementById('pmt_ref_chk_'+id)){
			document.getElementById('pmt_ref_chk_'+id).checked=true;
		}
		document.getElementById('total_ref').value='0.00';
		var tot_cont=document.getElementById('tot_cont').value;
		for(i=1;i<=tot_cont;i++){
			if(document.getElementById('ci_ref_chk_'+i)){
				if(document.getElementById('ci_ref_chk_'+i).checked==true){
					document.getElementById('ci_ref_amt_'+i).value = removeCommas(document.getElementById('ci_ref_amt_'+i).value);
					if(parseFloat(document.getElementById('ci_ref_amt_'+i).value)>parseFloat(document.getElementById('ci_ref_pending_amt_'+i).value)){
						document.getElementById('ci_ref_amt_'+i).value = document.getElementById('ci_ref_pending_amt_'+i).value;
					}
					document.getElementById('total_ref').value=(parseFloat(document.getElementById('total_ref').value)+parseFloat(document.getElementById('ci_ref_amt_'+i).value)).toFixed(2);
				}
			}
			if(document.getElementById('pmt_ref_chk_'+i)){
				if(document.getElementById('pmt_ref_chk_'+i).checked==true){
					document.getElementById('pmt_ref_amt_'+i).value = removeCommas(document.getElementById('pmt_ref_amt_'+i).value);
					if(parseFloat(document.getElementById('pmt_ref_amt_'+i).value)>parseFloat(document.getElementById('pmt_ref_pending_amt_'+i).value)){
						document.getElementById('pmt_ref_amt_'+i).value = document.getElementById('pmt_ref_pending_amt_'+i).value;
					}
					document.getElementById('total_ref').value=(parseFloat(document.getElementById('total_ref').value)+parseFloat(document.getElementById('pmt_ref_amt_'+i).value)).toFixed(2);
				}
			}
		}
	}
</script>
<div class="row">
	<!-- Heading -->
	<div class="purple_bar col-sm-12">
		<div class="row">
			<div class="col-sm-4">
				<label>Refund CI/CO and Prepayment</label>	
			</div>
			<div class="col-sm-4 text-center">
				<label><?php echo $patient_name.' - '.$patient_id; ?></label>	
			</div>	
		</div>
	</div>
	
	<!-- Content table -->
	<div class="col-sm-12">
		<form action="refund_ci_co_pmt.php" method="post" name="frm" id="refundFrm">
			<div class="col-sm-12" style="height:410px; background:#fff; overflow-y:auto; overflow-x:hidden;">
				<div class="row">
					<table class="table table-bordered table-striped">
						<tr class="grythead">
							<th>Apply</th>
							<th>Field Name</th>
							<th>Total Payment</th>
							<th>Pending Payment</th>
							<th>Payment Method</th>
							<th>CC / Ch.<?php getHashOrNo(); ?></th>
							<th>Refund</th>
						</tr>
						<?php
							$ci_qry="select check_in_out_payment_details.payment_type,check_in_out_payment_details.item_payment,
										check_in_out_payment.payment_method,check_in_out_payment.check_no,check_in_out_payment.cc_type,
										check_in_out_payment.cc_no,check_in_out_payment_details.id,check_in_out_payment_details.item_id,
                                        check_in_out_payment.log_referenceNumber,check_in_out_payment_details.payment_id 
										 from  check_in_out_payment join check_in_out_payment_details
											on check_in_out_payment.payment_id=check_in_out_payment_details.payment_id
											where check_in_out_payment.patient_id='$patient_id'
											and check_in_out_payment.total_payment>0 
											and check_in_out_payment.del_status='0'
											and check_in_out_payment_details.status='0'
											and check_in_out_payment_details.item_payment>0
											order by check_in_out_payment.payment_id desc,check_in_out_payment_details.id";
							$ci_sel=imw_query($ci_qry);
							$seq_no=0;
                            
							while($ci_row=imw_fetch_assoc($ci_sel)){
                                $cicodata_str='';
                                if($ci_row['log_referenceNumber']!=''){
                                    $cicoSql='select id,scheduID,encounter_id,laneId,TransactionNumber,is_api from tsys_possale_transaction where log_referenceNumber="'.$ci_row['log_referenceNumber'].'" and patient_id="'.$patient_id.'" ';
                                    $cicoRs=imw_query($cicoSql);
                                    $cicoRow=imw_fetch_assoc($cicoRs);
                                    $cicoTransactionNumber=$cicoRow['TransactionNumber'];
                                    $cicolaneID=$cicoRow['laneId'];
                                    $cicoscheduID=$cicoRow['scheduID'];
                                    $cicoencounter_id=$cicoRow['encounter_id'];
                                    $cicoreturnid=$ci_row['payment_id'];
                                    $cicois_api=$cicoRow['is_api'];
                                    
                                    $cicodata_str.=' data-is_api_'.$ci_row['id'].'="'.$cicois_api.'"  ';
                                    $cicodata_str.=' data-laneid_'.$ci_row['id'].'="'.$cicolaneID.'"  ';
                                    $cicodata_str.=' data-transactionnumber_'.$ci_row['id'].'="'.$cicoTransactionNumber.'"  ';
                                    $cicodata_str.=' data-scheduid_'.$ci_row['id'].'="'.$cicoscheduID.'"  ';
                                    $cicodata_str.=' data-encounterid_'.$ci_row['id'].'="'.$cicoencounter_id.'"  ';
                                    $cicodata_str.=' data-returnid_'.$ci_row['id'].'="'.$cicoreturnid.'"  ';
                                }
								$payment_check_cc="";	
								if($ci_row['payment_method']=='Check' or $ci_row['payment_method']=='EFT' or $ci_row['payment_method']=='Money Order' or $ci_row['payment_method']=='VEEP'){
									$payment_check_cc=$ci_row['check_no'];
                                    $cicodata_str='';
								}else if($ci_row['payment_method']=='Credit Card'){
									$payment_check_cc=$ci_row['cc_type'] .'-'. substr($ci_row['cc_no'],-4);
								}
								$ci_payment_detail_id=$ci_row['id'];
								$query = "SELECT sum(patient_charges_detail_payment_info.paidForProc+patient_charges_detail_payment_info.overPayment) as app_ci_pmt 
										FROM patient_chargesheet_payment_info as a
										JOIN check_in_out_payment_post as b ON b.acc_payment_id = a.payment_id 
										JOIN patient_charge_list as c ON c.encounter_id = a.encounter_id 
										JOIN patient_charges_detail_payment_info ON patient_charges_detail_payment_info.payment_id = a.payment_id 
										WHERE 
										c.del_status='0' and b.patient_id = '".$patient_id."' AND c.patient_id = '".$patient_id."' 
										AND patient_charges_detail_payment_info.deletePayment = '0' and b.status='0' and a.unapply='0' and b.check_in_out_payment_detail_id='$ci_payment_detail_id' ORDER BY a.date_of_payment";
								$qry_check_in_out_post = imw_query($query);
								$get_check_in_out_post = imw_fetch_array($qry_check_in_out_post);
								$app_ci_pmt=$get_check_in_out_post['app_ci_pmt'];
								
								$sel_ref_qry=imw_query("select sum(ref_amt) as ref_amt from ci_pmt_ref where ci_co_id='$ci_payment_detail_id' and del_status='0' and ci_co_id>0");
								$sel_ref_row=imw_fetch_array($sel_ref_qry);
								$app_ci_pmt=$app_ci_pmt+$sel_ref_row['ref_amt'];
								
								$manual_qry_payment=imw_query("select sum(manually_payment) as manually_payment from check_in_out_payment_post where patient_id  ='$patient_id' and check_in_out_payment_detail_id ='$ci_payment_detail_id' and status='0'");
								$manual_fet_payment=imw_fetch_array($manual_qry_payment);
								$app_ci_pmt=$app_ci_pmt+$manual_fet_payment['manually_payment'];
								
								if($ci_row['item_payment']>$app_ci_pmt){
									$ci_ref_pending_amt=$ci_row['item_payment']-$app_ci_pmt;	
								$seq_no++;
						?>
						<tr>
							<td>
								<div class="checkbox checkbox-inline">
									<input type="checkbox" data-paymode="<?php echo $ci_row['payment_method']; ?>" name="ci_ref_chk[<?php echo $seq_no; ?>]" id="ci_ref_chk_<?php echo $seq_no; ?>" value="<?php echo $ci_row['id']; ?>" onClick="set_ref('<?php echo $seq_no; ?>','ci_');">
									<label for="ci_ref_chk_<?php echo $seq_no; ?>"></label>	
								</div>
							</td>
							<td>
								<?php 
									if($ci_row['payment_type']=='checkout'){
										echo "CO";
									}else{
										echo "CI";
									}
								 ?>
								 <?php echo '- '.$item_names_arr[$ci_row['item_id']]; ?>
							</td>
							<td >
								$<?php echo number_format($ci_row['item_payment'],2); ?>
							</td>
							<td >
							   $<?php echo number_format($ci_ref_pending_amt,2); ?>
								<input type="hidden" value="<?php echo $ci_ref_pending_amt;?>" name="ci_ref_pending_amt_<?php echo $seq_no; ?>" id="ci_ref_pending_amt_<?php echo $seq_no; ?>">
							</td>
							<td >
								<?php echo $ci_row['payment_method']; ?>
							</td>
							<td class="text-nowrap">
								<?php echo $payment_check_cc; ?>
							</td>
							<td>
                                <input type="text" class="form-control" <?php echo $cicodata_str;?> value="" name="ci_ref_amt[<?php echo $ci_row['id']; ?>]" id="ci_ref_amt_<?php echo $seq_no; ?>" onChange="tot_ref('<?php echo $seq_no; ?>');">
                                <?php /* if($ci_row['payment_method']=='Credit Card' && $cicodata_str!=''){  ?>
                                    <input type="text" class="form-control" <?php echo $cicodata_str;?> value="" name="ci_ref_amt[<?php echo $ci_row['id']; ?>]" id="ci_ref_amt_<?php echo $seq_no; ?>" onChange="tot_ref('<?php echo $seq_no; ?>');">
                                <?php  } else { ?>
                                    <input type="text" class="form-control" value="" name="ci_ref_amt[<?php echo $ci_row['id']; ?>]" id="ci_ref_amt_<?php echo $seq_no; ?>" onChange="tot_ref('<?php echo $seq_no; ?>');">
                                <?php } */ ?>
								
							</td>
						</tr>
						<?php }}?>
						<?php
							$pmt_qry="select * from patient_pre_payment where del_status='0' and apply_payment_type!='manually' and patient_id='$patient_id'";
							$pmt_sel=imw_query($pmt_qry);
                            
							while($pmt_row=imw_fetch_array($pmt_sel)){
                                $pmtdata_str='';
                                if($pmt_row['log_referenceNumber']!=''){
                                    $pmtSql='select id,scheduID,encounter_id,laneId,TransactionNumber,is_api from tsys_possale_transaction where log_referenceNumber="'.$pmt_row['log_referenceNumber'].'" and patient_id="'.$patient_id.'" ';
                                    $pmtRs=imw_query($pmtSql);
                                    $pmtRow=imw_fetch_assoc($pmtRs);
                                    $pmtTransactionNumber=$pmtRow['TransactionNumber'];
                                    $pmtlaneID=$pmtRow['laneId'];
                                    $pmtscheduID=$pmtRow['scheduID'];
                                    $pmtencounter_id=$pmtRow['encounter_id'];
                                    $pmtreturnid=$pmt_row['id'];
                                    $pmtis_api=$pmtRow['is_api'];
                                    
                                    $pmtdata_str.=' data-is_api_'.$pmt_row['id'].'="'.$pmtis_api.'"  ';
                                    $pmtdata_str.=' data-laneid_'.$pmt_row['id'].'="'.$pmtlaneID.'"  ';
                                    $pmtdata_str.=' data-transactionnumber_'.$pmt_row['id'].'="'.$pmtTransactionNumber.'"  ';
                                    $pmtdata_str.=' data-scheduid_'.$pmt_row['id'].'="'.$pmtscheduID.'"  ';
                                    $pmtdata_str.=' data-encounterid_'.$pmt_row['id'].'="'.$pmtencounter_id.'"  ';
                                    $pmtdata_str.=' data-returnid_'.$pmt_row['id'].'="'.$pmtreturnid.'"  ';
                                }
								$payment_check_cc="";	
								if($pmt_row['payment_mode']=='Check' or $pmt_row['payment_mode']=='EFT' or $pmt_row['payment_mode']=='Money Order' or $pmt_row['payment_mode']=='VEEP'){
									$payment_check_cc=$pmt_row['check_no'];
                                    $pmtdata_str='';
								}else if($pmt_row['payment_mode']=='Credit Card'){
									$payment_check_cc=$pmt_row['credit_card_co'] .'-'. substr($pmt_row['cc_no'],-4);
								}
								$patient_pre_payment_id=$pmt_row['id'];
								$sum_pmt_qry=imw_query("select sum(paidForProc+overPayment) as app_pre_pmt from patient_charges_detail_payment_info where patient_pre_payment_id='$patient_pre_payment_id' and deletePayment='0' and unapply='0'");
								$sum_pmt_row=imw_fetch_array($sum_pmt_qry);
								$app_pre_pmt=$sum_pmt_row['app_pre_pmt'];
								
								$sel_ref_qry=imw_query("select sum(ref_amt) as ref_amt from ci_pmt_ref where pmt_id='$patient_pre_payment_id' and del_status='0' and pmt_id>0");
								$sel_ref_row=imw_fetch_array($sel_ref_qry);
								$app_pre_pmt=$app_pre_pmt+$sel_ref_row['ref_amt'];
								
							if($pmt_row['paid_amount']>$app_pre_pmt){
								$pmt_ref_pending_amt=$pmt_row['paid_amount']-$app_pre_pmt;	
								$seq_no++;
						?>
							<tr>
								<td >
									<div class="checkbox checkbox-inline">
										<input type="checkbox" data-paymode="<?php echo $pmt_row['payment_mode']; ?>" name="pmt_ref_chk[<?php echo $seq_no; ?>]" id="pmt_ref_chk_<?php echo $seq_no; ?>" value="<?php echo $pmt_row['id']; ?>" onClick="set_ref('<?php echo $seq_no; ?>','pmt_');">
										<label for="pmt_ref_chk_<?php echo $seq_no; ?>"></label>	
									</div>
								</td>
								<td >
									<?php 
										echo "Prepayment";
									 ?>
								</td>
								<td >
									$<?php echo number_format($pmt_row['paid_amount'],2); ?>
								</td>
								<td >
									$<?php echo number_format($pmt_ref_pending_amt,2); ?>
									<input type="hidden" value="<?php echo $pmt_ref_pending_amt;?>" name="pmt_ref_pending_amt_<?php echo $seq_no; ?>" id="pmt_ref_pending_amt_<?php echo $seq_no; ?>">
								</td>
								<td >
									<?php echo $pmt_row['payment_mode']; ?>
								</td>
								<td class="text-nowrap">
									<?php echo $payment_check_cc; ?>
								</td>
								<td>
                                    <input type="text" class="form-control"  <?php echo $pmtdata_str;?> value="" name="pmt_ref_amt[<?php echo $pmt_row['id']; ?>]" id="pmt_ref_amt_<?php echo $seq_no; ?>"  onChange="tot_ref('<?php echo $seq_no; ?>');">
                                    <?php /* if($pmt_row['payment_mode']=='Credit Card' && $pmtdata_str!=''){  ?>
                                        <input type="text" class="form-control"  <?php echo $pmtdata_str;?> value="" name="pmt_ref_amt[<?php echo $pmt_row['id']; ?>]" id="pmt_ref_amt_<?php echo $seq_no; ?>"  onChange="tot_ref('<?php echo $seq_no; ?>');">
                                    <?php } else { ?>
                                        <input type="text" class="form-control" value="" name="pmt_ref_amt[<?php echo $pmt_row['id']; ?>]" id="pmt_ref_amt_<?php echo $seq_no; ?>"  onChange="tot_ref('<?php echo $seq_no; ?>');">
                                    <?php } */ ?>
									
								</td>
							</tr>
						<?php }}?>
					</table>
				</div>				
			</div>
			
			<!-- Payment method block -->
			<div class="col-sm-12">
				<div class="row">
					<!-- Total Refund -->
					<div class="col-sm-2">
						<label>Total Refund:</label>
						<div class="input-group">
							<label for="total_ref" class="input-group-addon">
								<span class="glyphicon glyphicon-usd"></span>
							</label>
							<input type="text" name="total_ref" id="total_ref" value="0.00" class="form-control" readonly>
						</div>	
						<input type="hidden" name="tot_cont" id="tot_cont" value="<?php echo $seq_no; ?>">		
					</div>	
					
					<!-- Payment method -->
					<div class="col-sm-3">
						<label>Method:</label>
						<select name="payment_mode" id="payment_mode" class="selectpicker show-menu-arrow" data-width="100%" onChange="return changeMode(this.value);">
							<option value="Cash">Cash</option>
							<option value="Check">Check</option>
							<option value="Credit Card">Credit Card</option>
						</select>	
					</div>
					
					
					<!-- Check field -->
					<div id="checkTd" class="col-sm-2 hide">
						<label>Check&nbsp;#:&nbsp;</label>	
						<input name="check_no" type="text" class="form-control" value="" />	
					</div>	
					
					<!-- Credit card details -->
					<div id="ccTd" class="col-sm-5 hide">
						<div class="row">	
							<div class="col-sm-4">
								<label>CC&nbsp;#:</label>
								<input name="cCNo" id="cCNo" type="text" class="form-control" value="<?php echo $checkCCNo; ?>" />	
							</div>
							
							<div id="creditCardCoTd" class="col-sm-4">
								<label>Type.:</label>
								<select name="creditCardCo" id="creditCardCo" class="selectpicker show-menu-arrow" data-width="100%" data-title="Select">
									<option value="AX">American Express</option>
									<option value="Care Credit">Care Credit</option>
									<option value="Dis">Discover</option>
									<option value="MC">Master Card</option>
									<option value="Visa">Visa</option>
								</select>
							</div>

							<div class="col-sm-4">
								<label>Exp.&nbsp;Date:</label>
								<input type="text" name="expireDate" id="expireDate" value="" maxlength="10" class="form-control" />
							</div>	
						</div>
					</div>	
				</div>
                <?php if($pos_device) { ?>
                    <div class="row">
                        <div class="col-sm-8">
                            <?php include_once 'pos/include_cc_payment.php'; ?>
                        </div>	
                    </div>
                <?php } ?>
                <div class="text-center pt10">
                    <footer id="module_buttons">
                        <input type="hidden" name="log_referenceNumber" id="log_referenceNumber" value=""/>
                        <input type="hidden" name="tsys_payment_type_log_id" id="tsys_payment_type_log_id" value=""/>
                        <input type="hidden" name="tsys_transaction_id" id="tsys_transaction_id" value="" />
                        <input type="hidden" name="tsys_void_id" id="tsys_void_id" value="" />
                        <input type="hidden" name="tsys_last_status" id="tsys_last_status" value="" />
                        <input type="hidden" name="pos_counter" id="pos_counter" value="0" />
                        <input type="hidden" name="card_details_str_id" id="card_details_str_id" value="" />

<!--                        <input name="ApplyBtn" id="ApplyBtn" type="submit" class="btn btn-success" value="Apply">&nbsp;-->
                        <input name="ApplyBtn" id="ApplyBtn" type="button" class="btn btn-success" value="Done" onclick="refund_ci_co_pmt();">&nbsp;
                        <input name="CancelBtn" id="CancelBtn" type="button" class="btn btn-danger" value="Cancel" onClick="window.close();">
                    </footer>
                </div>
			</div>	
		</form>
	</div>
</div>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>

<script>
var totalAmtArr={};
var laneIDArr={};
var referenceNumberArr={};
var transNumberArr=[];
var scheduidArr={};
var encounteridArr={};
var returnidArr={};
var is_apiArr={};
function refund_ci_co_pmt() {
    var no_pos_device=false;
    if($('#tsys_device_url').val()=='no_pos_device') {
        no_pos_device=true;
    }
    
    var paymentMode=$('#payment_mode').val();
    if(paymentMode!='Credit Card' || typeof(pos_device)=='undefined' || pos_device=='' || no_pos_device==true) {
        $(".btn").prop('disabled', true);
        pos_submit_frm();
        return false;
    }
    
    uncheckOther(paymentMode);

    var tempTotalAmt=0;
    $('[id^=ci_ref_chk_]:checked').each(function(key, elem){
        var id=elem.value;
        var cicoobj=$("input[name='ci_ref_amt["+id+"]']");
        var pmtAmtVal=$("input[name='ci_ref_amt["+id+"]']").val();
        if(typeof(cicoobj.data('transactionnumber_'+id))!='undefined' && cicoobj.data('transactionnumber_'+id)!='') {
            var TransactionNumber=cicoobj.data('transactionnumber_'+id);
        }
        if(typeof(cicoobj.data('laneid_'+id))!='undefined' && cicoobj.data('laneid_'+id)!='') {
            var laneID=cicoobj.data('laneid_'+id);
        }
        if(typeof(cicoobj.data('referencenumber_'+id))!='undefined' && cicoobj.data('referencenumber_'+id)!='') {
            var referenceNumber=cicoobj.data('referencenumber_'+id);
        }
        if(typeof(cicoobj.data('scheduid_'+id))!='undefined' && cicoobj.data('scheduid_'+id)!='') {
            var scheduid=cicoobj.data('scheduid_'+id);
        }
        if(typeof(cicoobj.data('encounterid_'+id))!='undefined' && cicoobj.data('encounterid_'+id)!='') {
            var encounterid=cicoobj.data('encounterid_'+id);
        }
        if(typeof(cicoobj.data('returnid_'+id))!='undefined' && cicoobj.data('returnid_'+id)!='') {
            var returnid=cicoobj.data('returnid_'+id);
        }
        if(typeof(cicoobj.data('is_api_'+id))!='undefined' && cicoobj.data('is_api_'+id)!='') {
            var is_api=cicoobj.data('is_api_'+id);
        }
        
        pmtAmtVal=parseFloat(pmtAmtVal);
        if(TransactionNumber) {
            transNumberArr.push(TransactionNumber);
            if (typeof(totalAmtArr[TransactionNumber])!= "undefined" && totalAmtArr[TransactionNumber]) {
                pmtAmtVal=parseFloat(totalAmtArr[TransactionNumber])+pmtAmtVal;
            }
            totalAmtArr[TransactionNumber]=pmtAmtVal;
            if(laneID)
                laneIDArr[TransactionNumber]=laneID;
            if(referenceNumber)
                referenceNumberArr[TransactionNumber]=referenceNumber;
            if(scheduid)
                scheduidArr[TransactionNumber]=scheduid;
            if(encounterid)
                encounteridArr[TransactionNumber]=encounterid;
            if(returnid)
                returnidArr[TransactionNumber]=returnid;
            if(is_api)
                is_apiArr[TransactionNumber]=is_api;
        }
 
    });
    
    $('[id^=pmt_ref_chk_]:checked').each(function(key, val){
        var id=val.value;
        var pmtobj=$("input[name='pmt_ref_amt["+id+"]']");
        var pmtAmtVal=pmtobj.val();
        pmtAmtVal=parseFloat(pmtAmtVal);
        
        if(typeof(pmtobj.data('transactionnumber_'+id))!='undefined' && pmtobj.data('transactionnumber_'+id)!='') {
            var TransactionNumber=pmtobj.data('transactionnumber_'+id);
        }
        if(typeof(pmtobj.data('laneid_'+id))!='undefined' && pmtobj.data('laneid_'+id)!='') {
            var laneID=pmtobj.data('laneid_'+id);
        }
        if(typeof(pmtobj.data('referencenumber_'+id))!='undefined' && pmtobj.data('referencenumber_'+id)!='') {
            var referenceNumber=pmtobj.data('referencenumber_'+id);
        }
        if(typeof(pmtobj.data('scheduid_'+id))!='undefined' && pmtobj.data('scheduid_'+id)!='') {
            var scheduid=pmtobj.data('scheduid_'+id);
        }
        if(typeof(pmtobj.data('encounterid_'+id))!='undefined' && pmtobj.data('encounterid_'+id)!='') {
            var encounterid=pmtobj.data('encounterid_'+id);
        }
        if(typeof(pmtobj.data('returnid_'+id))!='undefined' && pmtobj.data('returnid_'+id)!='') {
            var returnid=pmtobj.data('returnid_'+id);
        }
        if(typeof(pmtobj.data('is_api_'+id))!='undefined' && pmtobj.data('is_api_'+id)!='') {
            var is_api=pmtobj.data('is_api_'+id);
        }


        pmtAmtVal=parseFloat(pmtAmtVal);
        if(TransactionNumber) {
            transNumberArr.push(TransactionNumber);
            if (typeof(totalAmtArr[TransactionNumber])!= "undefined" && totalAmtArr[TransactionNumber]) {
                pmtAmtVal=parseFloat(totalAmtArr[TransactionNumber])+pmtAmtVal;
            }
            totalAmtArr[TransactionNumber]=pmtAmtVal;
            if(laneID)
                laneIDArr[TransactionNumber]=laneID;
            if(referenceNumber)
                referenceNumberArr[TransactionNumber]=referenceNumber;
            if(scheduid)
                scheduidArr[TransactionNumber]=scheduid;
            if(encounterid)
                encounteridArr[TransactionNumber]=encounterid;
            if(returnid)
                returnidArr[TransactionNumber]=returnid;
            if(is_api)
                is_apiArr[TransactionNumber]=is_api;
        }

    });

    transNumberArr=$.unique(transNumberArr);
    if(totalAmtArr.length<=0) {
        blankallArray();
        return false;
    }
    func_recursive();
}


function func_recursive() {
    var i=$('#pos_counter').val();
    if(transNumberArr.length>0 && transNumberArr[i]) {
        var transactionNumber=transNumberArr[i];
        var totalAmt=totalAmtArr[transactionNumber];
        var laneID=laneIDArr[transactionNumber];
        var scheduID=scheduidArr[transactionNumber];
        var encounter_id=encounteridArr[transactionNumber];
        var returnid=returnidArr[transactionNumber];
        var is_api=is_apiArr[transactionNumber];
        var transactionType='02';

        $('.btn').prop('disabled', true);
        main_return_transaction(transactionNumber,totalAmt,transactionType,laneID,scheduID,encounter_id,returnid,is_api);
    } else {
        check_no_pos_case();
    }
}
                            
function main_return_transaction(transactionNumber,totalAmt,transactionType,laneID,scheduID,encounter_id,returnid,is_api) {
    if(totalAmt) {
        totalAmt=parseFloat(totalAmt);
        totalAmt=Math.round(totalAmt*100);
    } else {
        totalAmt=false;  
    }

    if(!transactionNumber) {
        fAlert('Invalid Transaction.');
        return false;
    }
    
    var MotoMode=$('#moto_trans_mode').val();
    if( (MotoMode && MotoMode!='') || is_api==1) {
        var posMachine='NOT PRESENT';
    } else {
        var posMachine='PRESENT';
    }
    /*Create referenceNumber using ajax Log table entry */
    createReferenceNumber(posMachine);
    var referenceNumber=$('#log_referenceNumber').val();
    if(!referenceNumber) {
        console.log('referenceNumber does not exists.');
        return false;
    }
    
    var tsysOrderNumber = {};
    var MotoMode=$('#moto_trans_mode').val();
    if(MotoMode && MotoMode!='') {
        tsysOrderNumber.MotoMode=MotoMode;
        generateOrderNumber();
        tsysOrderNumber.OrderNumber=$('#tsys_OrderNumber').val();
    }
    
    show_cc_loading_image('show','', 'Please Wait...');
    var tsys_token='';if($('#tsys_token').length>0 && $('#tsys_token option:selected').val()!=''){tsys_token=$('#tsys_token option:selected').val();}
    if(is_api==1) {
        tsysOrderNumber.transactionNumber=transactionNumber;
        tsysOrderNumber.scheduID=scheduID;
        tsysOrderNumber.encounter_id=encounter_id;
        tsysOrderNumber.laneID=laneID;
        if(tsys_token==''){
            if(MotoMode!='' && saveccardData()) { delete tsysOrderNumber['transactionNumber']; }
        }
        pos_api_payment(totalAmt,referenceNumber,tsysOrderNumber,'refund');
    } else {
        chargeAmount( totalAmt, laneID, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, returnid );
    }
}


function pos_submit_frm() {
    //$('.btn').prop('disabled', false);

    $('#refundFrm').submit();
}

function blankallArray() {
    totalAmtArr={};
    laneIDArr={};
    referenceNumberArr={};
    transNumberArr=[];
    scheduidArr={};
    encounteridArr={};
    returnidArr={};
    is_apiArr={};
}

function uncheckOther(thisVal) {
    if(thisVal=='Credit Card') {
        $('[id^=ci_ref_chk_]').each( function(key,obj) {
            var paymode = $(obj).data('paymode');
            if(paymode!='Credit Card') {
                $(obj).prop('checked', false);
                
                $("input[name='ci_ref_amt["+obj.value+"]']").val('');
            }
        });
        
        $('[id^=pmt_ref_chk_]').each( function(key,obj) {
            var paymode = $(obj).data('paymode');
            if(paymode!='Credit Card') {
                $(obj).prop('checked', false);
                
                $("input[name='pmt_ref_amt["+obj.value+"]']").val('');
            }
        });    
    }

    tot_ref(0);
}

function check_no_pos_case() {
    var no_pos_device=false;
    if($('#tsys_device_url').val()=='no_pos_device') {
        no_pos_device=true;
    } else {
        pos_submit_frm();
        return false;
    }    
}

</script>


</body>
</html>