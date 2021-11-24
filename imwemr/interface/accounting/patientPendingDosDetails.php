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
?>
<?php
$encounter_id="";
if($encounter_id==""){
	$seq = 0;
	$dis_proc_arr=array();
?>
	<input type="hidden" name="copay" id="copay" value="<?php echo $copay; ?>">
	<table style="width:100%;">
	<?php
		$pcl_enc_imp=implode(',',$pcl_enc_arr);
		$pcl_chl_imp=implode(',',$pcl_chl_arr);
		
		$qry=imw_query("SELECT * FROM patient_charge_list_details WHERE charge_list_id in($pcl_chl_imp) and del_status='0' and (newBalance>0 || overPaymentForProc>0) ORDER BY display_order,charge_list_detail_id  ASC");
		while($row=imw_fetch_array($qry)){
			$pcld_data[$row['charge_list_id']][]=$row;
			$proc_Arr[$row['charge_list_id']][] =$cpt_fee_tbl_data[$row['procCode']]['cpt4_code'];
		}
		
		$sel_batch=imw_query("select encounter_id from manual_batch_transactions where encounter_id in($pcl_enc_imp) and del_status='0' and post_status='0'");
		while($row_batch=imw_fetch_array($sel_batch)){
			$batch_arr[$row_batch['encounter_id']]=$row_batch['encounter_id'];
		}
		
		$sql = imw_query("select pcdpi.*,pcpi.* from patient_chargesheet_payment_info pcpi join patient_charges_detail_payment_info pcdpi
					 on pcpi.payment_id=pcdpi.payment_id WHERE pcpi.encounter_id in($pcl_enc_imp) and pcdpi.deletePayment='0'");
		while($row=imw_fetch_array($sql)){
			$payment_chld_paid_data[$row['encounter_id']][$row['charge_list_detail_id']][]=$row['paidForProc'];
		}
		$num_bal=0;
		foreach($pcl_data as $pcl_key=>$pcl_val){
			$cpt_onetime=$copayPaid=$min_copay_one=0;
			$all_prov_ids=$copay_proc_code_arr=$copay_chld_id_arr=array();
			$getEncounterDetailsRow=$pcl_data[$pcl_key];
			if(($getEncounterDetailsRow['totalBalance'] || $getEncounterDetailsRow['overPayment']>0) && count($pcld_data[$getEncounterDetailsRow['charge_list_id']])>0){
				$num_bal=1;
				$encounter_id=$getEncounterDetailsRow['encounter_id'];
				$date_of_service = $getEncounterDetailsRow['date_of_service'];
				list($year, $month, $day) = explode("-", $date_of_service);
				$date_of_service = date('m-d-y',mktime(0,0,0,$month,$day,$year));
				$case_type_id = $getEncounterDetailsRow['case_type_id'];
				$copay = $getEncounterDetailsRow['copay'];
				$pri_copay= $getEncounterDetailsRow['pri_copay'];
				$sec_copay= $getEncounterDetailsRow['sec_copay'];
				$auth_no= $getEncounterDetailsRow['auth_no'];
				$auth_amount= $getEncounterDetailsRow['auth_amount'];
				$primaryInsuranceCoId = $getEncounterDetailsRow['primaryInsuranceCoId'];
				$secondaryInsuranceCoId = $getEncounterDetailsRow['secondaryInsuranceCoId'];
				$tertiaryInsuranceCoId = $getEncounterDetailsRow['tertiaryInsuranceCoId'];
				
				$insTypeCode = $ins_case_name_arr[$case_type_id];
				if(($case_type_id=='0') ||($primaryInsuranceCoId=='0' && $secondaryInsuranceCoId=='0' && $tertiaryInsuranceCoId=='0')){ 
					$case_type_nam = 'Self Pay';
				}else{
					$case_type_nam = $insTypeCode;
				}
				
				$insCo1NameCode = $ins_comp_data[$primaryInsuranceCoId]['in_house_code'];
				if(!$insCo1NameCode){
					$insCo1Name = $ins_comp_data[$primaryInsuranceCoId]['name'];
					$insCo1NameLen = strlen($insCo1Name);
					if($insCo1NameLen>13){
						$insCo1NameCode = substr($insCo1Name, 0, 13)."..";
					}else{
						$insCo1NameCode=$insCo1Name;
					}
				}	
				
				$insCo2NameCode = $ins_comp_data[$secondaryInsuranceCoId]['in_house_code'];
				if(!$insCo2NameCode){
					$insCo2Name = $ins_comp_data[$secondaryInsuranceCoId]['name'];
					$insCo2NameLen = strlen($insCo2Name);
					if($insCo2NameLen>13){
						$insCo2NameCode = substr($insCo2Name, 0, 13)."..";
					}else{
						$insCo2NameCode = $insCo2Name;
					}
				}	
				
				$insCo3NameCode = $ins_comp_data[$tertiaryInsuranceCoId]['in_house_code'];
				if(!$insCo3NameCode){
					$insCo3Name = $ins_comp_data[$tertiaryInsuranceCoId]['name'];
					$insCo3NameLen = strlen($insCo3Name);
					if($insCo3NameLen>13){
						$insCo3NameCode = substr($insCo3Name, 0, 13)."..";
					}else{
						$insCo3NameCode=$insCo3Name;
					}
				}	
				$insCoArray[] = $insCo1NameCode;
				$insCoArray[] = $insCo2NameCode;
				$insCoArray[] = $insCo3NameCode;
				?>
				<tr>
					<td>
					<input type="hidden" name="encounter_id_arr[]" id="encounter_id_arr_<?php echo $encounter_id; ?>" value="<?php echo $encounter_id; ?>">
					<table class="table table-bordered table-striped" style="margin-bottom:2px;">
						<tr class="grythead">
							<th>DOS</th>
							<th>EId</th>
							<th>Ins. Case</th>
							<th>Total CoPay</th>
							<th>Pri Ins.</th>
							<th>Pri CoPay</th>
							<th>Sec Ins.</th>
							<th>Sec CoPay</th>
							<th>Tri Ins.</th>
							<th>Auth#</th>
							<th>Auth Amount</th>
						</tr>
						<tr class="text-center">
							<td><?php echo $date_of_service; ?></td>
							<td>
								<?php
								if(count($batch_arr[$encounter_id])>0){
								?>
									<img src="../../library/images/batch_transaction_pending.png" title="Batch Transaction Pending">
								<?php }  ?>
								<?php echo $encounter_id; ?>
							</td>
							<td><?php echo $case_type_nam; ?></td>
							<td><?php if($copay>0) echo "$".number_format($copay, 2); else echo '&nbsp;'; ?></td>
								
								<?php $tooltip=show_ins_tooltip($ins_comp_data[$primaryInsuranceCoId]);?>
							
							<td class="text_purple" <?php echo $tooltip; ?>><?php if($insCo1NameCode) echo $insCo1NameCode; else echo '-'; ?></td>
							<td><?php if($pri_copay>0) echo "$".number_format($pri_copay, 2); else echo '&nbsp;'; ?> </td>
								
								<?php $tooltip=show_ins_tooltip($ins_comp_data[$secondaryInsuranceCoId]);?>
							
							<td class="text_purple" <?php echo $tooltip; ?>><?php if($insCo2NameCode) echo $insCo2NameCode; else echo '-'; ?></td>
							<td><?php if($sec_copay>0) echo "$".number_format($sec_copay, 2); else echo '&nbsp;'; ?></td>
							
								<?php $tooltip=show_ins_tooltip($ins_comp_data[$tertiaryInsuranceCoId]);?>
							
							<td class="text_purple" <?php echo $tooltip; ?>><?php if($insCo3NameCode) echo $insCo3NameCode; else echo '-'; ?></td>
							<td><?php if($auth_no) echo $auth_no; else echo '&nbsp;'; ?></td>
							<td><?php if($auth_amount>0) echo "$".number_format($auth_amount,2); else echo '&nbsp;'; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				<table class="table table-bordered table-striped">
					<tr class="grythead">
						<th>Apply</th>
						<th>CPT</th>
						<th class="text-nowrap">Dx Code</th>
						<th class="text-nowrap">Total Charges</th>
						<th>Allowed</th>
						<th>Deductible</th>
						<th>Adj</th>
						<th>Credit</th>					
						<th>Paid</th>
						<th>Amount</th>
						<th class="text-nowrap">New Balance</th>
						<th class="text-nowrap">Over Paid </th>
						<th class="text-nowrap">Submitted Date</th>
						<th>Oper</th>
					</tr>
					<?php
					
					$chargeListId = $getEncounterDetailsRow['charge_list_id'];
					$coPay = $getEncounterDetailsRow['copay'];
					$copayPaid = $getEncounterDetailsRow['copayPaid'];
					$referactionPaid = $getEncounterDetailsRow['referactionPaid'];
					$coPayNotRequired = $getEncounterDetailsRow['coPayNotRequired'];
					$coPayWriteOff = $getEncounterDetailsRow['coPayWriteOff'];
					$amountDue = $getEncounterDetailsRow['amountDue'];
					$totalPaidAmt = $getEncounterDetailsRow['amtPaid'];
					$totalAmt = $getEncounterDetailsRow['totalAmt'];
					$approvedTotalAmt = $getEncounterDetailsRow['approvedTotalAmt'];
					$deductibleTotalAmt = $getEncounterDetailsRow['deductibleTotalAmt'];
					$totalEncounterBalance = number_format($getEncounterDetailsRow['totalBalance'], 2);
					$coPayAdjusted = $getEncounterDetailsRow['coPayAdjusted'];
					$creditAmountBalance = $getEncounterDetailsRow['creditAmount'];
					$overPayment = $getEncounterDetailsRow['overPayment'];
					$operator_id = $getEncounterDetailsRow['operator_id'];
				
					$all_prov_ids[] = $getEncounterDetailsRow['primaryProviderId'];
					$all_prov_ids[] = $getEncounterDetailsRow['secondaryProviderId'];
					$all_prov_ids[] = $getEncounterDetailsRow['tertiaryProviderId'];
				
					//GET OPERATOR INITIALS
					$operatorNamePaid=$usr_alias_name[$operator_id];
					
					$encCommentsInt = $getEncounterDetailsRow['encCommentsInt'];
					$encCommentsExt = $getEncounterDetailsRow['encCommentsExt'];
					
					$encCommentsIntDate = $getEncounterDetailsRow['encCommentsIntDate'];
					list($yearComment, $monthComment, $dayComment) = explode("-", $encCommentsIntDate);
					$encCommentsIntDate = $monthComment."-".$dayComment."-".$yearComment;
					
					$encCommentsExtDate = $getEncounterDetailsRow['encCommentsExtDate'];
					list($yearComment, $monthComment, $dayComment) = explode("-", $encCommentsExtDate);
					$encCommentsExtDate = $monthComment."-".$dayComment."-".$yearComment;
					$encCommentsIntOperatorId = $getEncounterDetailsRow['encCommentsIntOperatorId'];
				
					$postedDate = $getEncounterDetailsRow['firstSubmitDate'];
					list($year, $month, $day)=explode("-", $postedDate);
					$postedDate=$month."-".$day."-".$year;
					
					$totalBalance = $totalAmt - $totalPaidAmt;
					$deductAmt = false;
					$whoPaidAmt = '';
					
					//-------------------- ASC ORDER BY CPT DESC. --------------------//
				
				$totalRefractionAmountFor = $reflactionAmt = $amountToPay = $total_write_off_amount = 0;
				if(($copay>0) && ($copayPaid<=0) && ($coPayNotRequired!=1) && ($coPayWriteOff!='1')){
					$amountToPay = $amountToPay + $copay;
				}
				
				$tot_paid_chk1=array_sum($payment_chld_paid_data[$encounter_id][0]);
				
				$proc_code_imp=implode(',',$proc_Arr[$chargeListId]);
				$copay_collect_proc=copay_apply_chk($proc_code_imp,'','');
				
				$show_row="";
				$getProcCountRows_all+=count($pcld_data[$chargeListId]);
				foreach($pcld_data[$chargeListId] as $pcld_key=>$pcld_val){
					$getProcDetailsRows=$pcld_data[$chargeListId][$pcld_key];	
					$charge_list_detail_id = $getProcDetailsRows['charge_list_detail_id'];
					$procIdForCredit = $getProcDetailsRows['procCode'];		
					$dx1 = $getProcDetailsRows['diagnosis_id1'];		
					$dx2 = $getProcDetailsRows['diagnosis_id2'];		
					$dx3 = $getProcDetailsRows['diagnosis_id3'];		
					$dx4 = $getProcDetailsRows['diagnosis_id4'];
					$dx5 = $getProcDetailsRows['diagnosis_id5'];
					$dx6 = $getProcDetailsRows['diagnosis_id6'];
					$dx7 = $getProcDetailsRows['diagnosis_id7'];
					$dx8 = $getProcDetailsRows['diagnosis_id8'];
					$dx9 = $getProcDetailsRows['diagnosis_id9'];
					$dx10 = $getProcDetailsRows['diagnosis_id10'];
					$dx11 = $getProcDetailsRows['diagnosis_id11'];
					$dx12 = $getProcDetailsRows['diagnosis_id12'];
					$write_off_code_id = $getProcDetailsRows['write_off_code_id'];
					$paymentwrite_off_code=$wrt_name_arr[$write_off_code_id];
					$dx_code="";
					$dx_code=$dx1.' '.$dx2.' '.$dx3.' '.$dx4.' '.$dx5.' '.$dx6.' '.$dx7.' '.$dx8.' '.$dx9.' '.$dx10.' '.$dx11.' '.$dx12;						
					
					$writeOffAmount = 0;
					$balForProc = $getProcDetailsRows['balForProc'];
					$procNewBalance = $getProcDetailsRows['newBalance'];
					$approvedAmt = $getProcDetailsRows['approvedAmt'];
					$deductAmt = $getProcDetailsRows['deductAmt'];
					$claimDenied = $getProcDetailsRows['claimDenied'];
					$paidStatus = $getProcDetailsRows['paidStatus'];
					$procId = $getProcDetailsRows['procCode'];
					$units = $getProcDetailsRows['units'];
					$procCharges = $getProcDetailsRows['procCharges'];
					$totalAmount = $getProcDetailsRows['totalAmount'];
					$paidForProc = $getProcDetailsRows['paidForProc'];
					$balForProc = $getProcDetailsRows['balForProc'];
					$write_off_Proc = $getProcDetailsRows['write_off'];
					$writeOffId = $getProcDetailsRows['charge_list_detail_id'];
					
					foreach($payment_wrt_data[$encounter_id][$charge_list_detail_id] as $w_key=>$w_val){
						if($payment_wrt_data[$encounter_id][$charge_list_detail_id][$w_key]['delStatus']=='0'){	
							$writeOffAmount = $writeOffAmount+$payment_wrt_data[$encounter_id][$charge_list_detail_id][$w_key]['write_off_amount'];
						}
					}					
					
					$NewBalance = $getProcDetailsRows['newBalance'];
					$coPayAdjustedAmount =  $getProcDetailsRows['coPayAdjustedAmount'];
					$creditProcAmount =  $getProcDetailsRows['creditProcAmount'];
					$overPaymentForProc = $getProcDetailsRows['overPaymentForProc'];
					
					$cptPracCode = $cpt_fee_tbl_data[$procId]['cpt_prac_code'];
					$cpt4_code = $cpt_fee_tbl_data[$procId]['cpt4_code'];
					$cptDesc = $cpt_fee_tbl_data[$procId]['cpt_desc'];
					
					if(($cptPracCode=='92015') || ($cpt4_code=='92015')){
						$totalRefractionAmountFor = $totalRefractionAmountFor + $NewBalance;
						$reflactionAmt = $NewBalance;
						$refractionExists = true;
						if($paidStatus == 'Paid'){
							$paid_by = 'Patient';
						}
					}else{
						$refractionExists = false;
					}
					
					if(($reflactionAmt>0) && ($referactionPaid<=0)){
						$amountToPay = $amountToPay + $reflactionAmt;
					}
					
					$writeOff = $totalAmount - $approvedAmt;
					
					++$seq;
					
					$totalBalanceNewDue = $approvedTotalAmt - $creditAmount - $totalPaidAmt - $deductibleTotalAmt;
					if($copay){
						if(($copayPaid!=1) && ($coPayNotRequired != 0) && ($coPayWriteOff!='1')){
							$amount = $amount - $copay;
						}
					}
					$approvedAmt = round($approvedAmt, 2);
					$deductAmt = number_format($deductAmt, 2);
					$creditProcAmount = number_format($creditProcAmount, 2);
					$totalAmount = number_format($totalAmount, 2);
					$amount = number_format($amount, 2);
					
					$all_prov_imp_ids=implode(',',$all_prov_ids);
					$copay_proc_code_arr[$charge_list_detail_id]=$cptPracCode;
					$copay_chld_id_arr[]=$charge_list_detail_id;
					if($getProcDetailsRows['proc_selfpay']==1 && $getProcDetailsRows['cash_discount']==0 && $_SESSION['cs_dis_enc'][$encounter_id]!="yes"){
						$dis_proc_arr[]=$seq;
					}
					$copay_collect=copay_apply_chk($cpt4_code,'','');
					?>
					
					<input type="hidden" value="<?php echo $refractionChk; ?>" id="refractionChk<?php echo $seq; ?>" name="refractionChk<?php echo $seq; ?>">
					<input type="hidden" value="<?php echo $encounter_id; ?>" id="enc_arr<?php echo $seq; ?>" name="enc_arr[]">
					
					<tr>
						<td>
							<div class="checkbox">
								<input type="checkbox" name="chkbx[]" id="chkbx<?php echo $seq; ?>" value="<?php echo $charge_list_detail_id; ?>" onClick="return checkPaymentBox('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>','<?php echo $getProcCountRows_all; ?>');"/>
								<label for="chkbx<?php echo $seq; ?>"></label>
							</div>
							<?php 
							if($cpt_onetime==0){
								$cpt_onetime=0;
								if($copay_collect_proc==true && $copay_collect_proc[0]==true){
									if($copay_collect[0]==true && $copay_collect[0]==true){
										$copay_apply_for_chld=$charge_list_detail_id;
							?>
										<input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure<?php echo $seq; ?>" id="copay_apply_procedure<?php echo $seq; ?>">
							<?php
										$cpt_onetime++; 
									}
								}else{
									if($balForProc>0){
										$copay_apply_for_chld=$charge_list_detail_id;
							?>
										<input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure<?php echo $seq; ?>" id="copay_apply_procedure<?php echo $seq; ?>">
							<?php	
									$cpt_onetime++;	
									}
								}
							
							}
					
							$tot_paid_chk2=0;
							if($min_copay_one==0){
								$min_copay_one=0;
								$minus_copay_value=0;
								if($coPayAdjustedAmount==1){									
									if($coPay==$tot_paid_chk1){
										$minus_copay_value=$coPay;
										$min_copay_one++;
									}else{
										if($tot_paid_chk1<>0){
											$minus_copay_value=$coPay-$tot_paid_chk1;
											$min_copay_one++;
										}
									}
								}
								if($min_copay_one>0){
									$tot_paid_chk2=$tot_paid_chk1;
								}
							}else{
								$minus_copay_value=0.00;
							}
					?>
							<input type="hidden" value="<?php echo $tot_paid_chk2; ?>" name="copay_paid<?php echo $seq; ?>" id="copay_paid<?php echo $seq; ?>">
							<input type="hidden" value="<?php echo $minus_copay_value; ?>" name="minus_copay<?php echo $seq; ?>" id="minus_copay<?php echo $seq; ?>">
						</td>
						<td>
							<span><?php if($coPayAdjustedAmount==1){ ?><img src="../../library/images/confirm.gif" style="width:16px; vertical-align:middle;"/><?php } ?></span>
							<span id="cptIdTd<?php echo $seq; ?>" <?php echo show_tooltip($cptDesc); ?>  style="margin-right:10px;"> <?php echo $cptPracCode; ?> </span>
						</td>
						<td><?php echo $dx_code; ?></td>
						<td id="totalFee<?php echo $seq; ?>"><?php echo "$".$totalAmount; ?></td>
						<td style="display:none;" id="getBalTd<?php echo $seq; ?>"><?php echo $balForProc; ?></td>
						<td>
							<div class="input-group">
								<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
								<input type="text" name="approvedText<?php echo $seq; ?>" id="approvedText<?php echo $seq; ?>" class="form-control" style="width:90px;" value="<?php echo str_replace(',','',number_format($approvedAmt,2)); ?>" onChange="set_write_off_id('<?php echo $seq; ?>',event.y);return checkChkBox(<?php echo $seq; ?>);" onBlur="return approvedBlur('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>', this);">
							</div>
							<input type="hidden" name="appActualText<?php echo $seq; ?>" id="appActualText<?php echo $seq; ?>" value="<?php echo $approvedAmt; ?>">
						</td>
						<td>
							<div class="input-group">
								<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
								<input type="text" style="width:90px; <?php if($deductAmt>0){echo"color:#FFC000;";}?>" id="deductibleText<?php echo $seq; ?>" name="deductibleText<?php echo $seq; ?>" class="form-control" value="<?php echo $deductAmt; ?>" onChange="return deductChange('<?php echo $seq; ?>'),paymentChange_bydeduct('<?php echo $seq; ?>',this.value,'<?php echo $deductAmt; ?>'),paymentChange('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>')">
							</div>
							<input type="hidden" name="write_off_code<?php echo $seq; ?>" id="write_off_code<?php echo $seq; ?>" value="<?php echo $write_off_code_id; ?>">
						</td>
						<td id="writeOffTd<?php echo $seq; ?>">
							<?php 
							if($writeOffAmount>0){
								$tot_writeOff=$writeOffAmount+$writeOff;
								echo "$".number_format($tot_writeOff, 2); 
							}else{
								echo "$".number_format($writeOff, 2); 
							}
							?>
						</td>
						<!-- Credit Paid Pay -->
						<td  style="color:#009900;font-weight:bold;" id="creditAmtTd<?php echo $seq; ?>">
							<?php 
							//Credit amount 
							$amt_adust=$adj_amt=0;
							foreach($credit_record_adj_data[$charge_list_detail_id] as $c_key=>$c_val){
								$crd_adust_rec=$credit_record_adj_data[$charge_list_detail_id][$c_key];
								if($crd_adust_rec['delete_credit']=='0'){	
									$amt_adust = $amt_adust+$crd_adust_rec['amountApplied'];
								}
							}
							
							echo "$".number_format($amt_adust,2);
							
							//Adjustment/Over Adjustment amount 
							foreach($account_payments_data[$charge_list_detail_id] as $a_key=>$a_val){
								$det_adust_rec=$account_payments_data[$charge_list_detail_id][$a_key];
								if($det_adust_rec['del_status']=='0' && ($det_adust_rec['payment_type']=='Adjustment' || $det_adust_rec['payment_type']=='Over Adjustment')){	
									$adj_amt = $adj_amt+$det_adust_rec['payment_amount'];
								}
							}
							?>
						</td>
						<td  style="color:#009900;font-weight:bold;" id="paidAmtPrev<?php echo $seq; ?>">
							<?php echo '$'.number_format($paidForProc,2); ?>
						</td>
						<td>
							<div class="input-group">
								<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
								<input type="text" name="payNew<?php echo $seq; ?>" id="payNew<?php echo $seq; ?>" class="form-control" style="width:90px;" value="<?php echo "0.00"; ?>" onBlur="return selectChanges('<?php echo $seq; ?>')" onChange="return paymentChange('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>')">
							</div>
							<input type="hidden" name="counterIdArr[]" id="counterIdArr_<?php echo $seq; ?>" value="<?php echo $seq; ?>">
							<input type="hidden" name="chargeListDetailIdArr[]" id="chargeListDetailIdArr_<?php echo $charge_list_detail_id; ?>" value="<?php echo $charge_list_detail_id; ?>">
							<input type="hidden" name="paidAmtText<?php echo $seq; ?>" id="paidAmtText<?php echo $seq; ?>" class="text_10" onChange="return checkChkBox(<?php echo $seq; ?>);" value="<?php echo "0.00"; ?>">
							<input type="hidden" name="adj_amt<?php echo $seq; ?>" id="adj_amt<?php echo $seq; ?>" value="<?php echo $adj_amt;?>">
						</td>
						<td id="newBalanceTd<?php echo $seq; ?>">
							<span style="color:<?php echo ($NewBalance <= 0) ? "Green" : "Red";?>">
								<?php if($NewBalance<0){ echo '0.00'; }else{ if($overPaymentForProc>0) echo '-'. "$".number_format($overPaymentForProc,2); else echo "$".number_format($NewBalance,2); } ?>
							</span>
							<input type="hidden" value="<?php if($NewBalance<0){ echo '0.00'; }else{ if($overPaymentForProc>0) echo '0.00'; else echo $NewBalance; } ?>" name="bal_chk_for_copay" id="bal_chk_for_copay<?php echo $seq; ?>">
						</td>
						<td style="color:#5D738E;font-weight:bold;" id="overPaidPrev<?php echo $seq; ?>">
							<?php if($overPaymentForProc>0) echo "$".number_format($overPaymentForProc,2); else echo "$".number_format($overPaymentForProc,2); ?>
							<input type="hidden" name="overPayment<?php echo $seq; ?>" id="overPayment<?php echo $seq; ?>" class="text_10" value="<?php echo $overPaymentForProc; ?>">
							<input type="hidden" name="overPaymentNow<?php echo $seq; ?>" id="overPaymentNow<?php echo $seq; ?>" class="text_10" value="">
							<input type="hidden" name="overPayments_chk<?php echo $seq; ?>" id="overPayments_chk<?php echo $seq; ?>" class="text_10" value="<?php echo $overPaymentForProc; ?>">
						</td>
						<td>
							<?php
							if(count($submit_record_data[$encounter_id])>0){
							?>
								<select name="hcfaSubmittedDat" id="hcfaSubmittedDat" class="selectpicker" data-width="100%">
								<?php
								foreach($submit_record_data[$encounter_id] as $s_key=>$s_val){
									$getSubmittedDateRow=$submit_record_data[$encounter_id][$s_key];				
									$hcfaSubmittedDate = $getSubmittedDateRow['submited_date'];	
									$Ins_type = $getSubmittedDateRow['Ins_type'];				
									list($hcfaYear, $hcfaMonth, $hcfaDay) = explode("-", $hcfaSubmittedDate);
									$hcfaSubmittedDate = date('m-d-y',mktime(0,0,0,$hcfaMonth,$hcfaDay,$hcfaYear));
									if($Ins_type=="primary" || $Ins_type=="Primary"){
										$Ins_type_show="Pri";
									}else if($Ins_type=="secondary" || $Ins_type=="Secondary"){
										$Ins_type_show="Sec";
									}else if($Ins_type=="tertiary" || $Ins_type=="Tertiary"){
										$Ins_type_show="Tri";
									}
								?>
									<option><?php echo $Ins_type_show.'-'.$hcfaSubmittedDate; ?></option>
								<?php } ?>
								</select>
							<?php }else{ echo '-';}?>
						</td>					
						<td><?php echo $operatorNamePaid; ?></td>
					</tr>
				<?php
					}
					if($copay>0){
						if(count($payment_wrt_data[$encounter_id][0])>0){
							foreach($payment_wrt_data[$encounter_id][0] as $wrt_key=>$wrt_val){
								$getCoPayWriteOffIDRow=$payment_wrt_data[$encounter_id][0][$wrt_key];
								if($getCoPayWriteOffIDRow['delStatus']==0){
									$coPay_write_off_id = $getCoPayWriteOffIDRow['write_off_id'];
									$paymentStatusType = $getCoPayWriteOffIDRow['paymentStatus'];
									$write_off_code_copay = $getCoPayWriteOffIDRow['write_off_code_id'];
								}
							}
						}
						if(($coPayNotRequired == 0) && ($coPayWriteOff!='1')){
							$coPayNotRequired=1;
							if($copayPaid<=0){
								$chk_copay3=$coPay-$tot_paid_chk1;
								$show_row="
								<tr>
									<td>
										<div class='checkbox'>
											<input type='checkbox' name='coPayChk".$seq."' id='coPayChk".$seq."' value='true' onClick='return changeAmt(".$chk_copay3.",".$getProcCountRows_all.",".$seq.");' />
											<label for='coPayChk".$seq."'></label>
										</div>
										<input  name='copayAmount_hid' id='copayAmount_hid".$seq."' type='hidden' value='".number_format(($chk_copay3),2)."' class='text_10' />
									</td>
									<td colspan='13'>CoPay ";
										$show_row .="&nbsp;&nbsp;&nbsp;&nbsp;<select name='proc_copay".$seq."' id='proc_copay".$seq."' onChange='changeAmt(".$chk_copay3.",".$getProcCountRows_all.",".$seq.");'  class='selectpicker' data-width='10%'>";
										for($i=0;$i<count($copay_chld_id_arr);$i++){
											$sel_copay="";
											if($copay_apply_for_chld==$copay_chld_id_arr[$i]){
												$sel_copay="selected";
											}	
											$show_row .="<option value='".$copay_chld_id_arr[$i]."' ".$sel_copay.">".$copay_proc_code_arr[$copay_chld_id_arr[$i]]."</option>";
										}		
									$show_row .="</select></td></tr>";
							}else{
						?>
							<tr style="height:25px;">									
								<td colspan="2">&nbsp;&nbsp;<img src="../../library/images/confirm.gif"  style="width:16px;"/></td>
								<td colspan="13" style="font-weight:bold;">CoPay</td>
							</tr>
						<?php
							}
						}	
					} 
					echo $show_row;
					
				?>
				</table>
				<input type="hidden" name="amountDue" id="amountDue_<?php echo $seq; ?>" value="<?php echo $amountDue; ?>" />
				<input type="hidden" name="copaySt" id="copaySt_<?php echo $seq; ?>" value="<?php echo $getRowValidation; ?>" />
				<input type="hidden" name="totalBalance" id="totalBalance_<?php echo $seq; ?>" value="<?php echo $totalBalanceNewDue; ?>">
				<input type="hidden" name="charge_list_detail_id" id="charge_list_detail_id_<?php echo $seq; ?>" value="<?php echo $seq; ?>">
				<input type="hidden" value="<?php echo $totalRefractionAmountFor; ?>" name="totalRefractionAmountFor" id="totalRefractionAmountFor_<?php echo $seq; ?>">
				<input type="hidden" value="<?php echo $seq; ?>" name="sequence" id="sequence_<?php echo $seq; ?>">
			</td>
		</tr>
	<?php
		}
	}
?>
</table>
<?php
}if($num_bal==0){ ?>
<left>
	<table>
		<tr style="height:100%;">
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><span style="font-family:Arial; font-size:11px; font-weight:bold;">No Dues For Any DOS.</span></td>
		</tr>
	</table>
</left>
<?php
}
?>
<?php 
$dis_proc_imp=implode(',',$dis_proc_arr);
?>
<script type="text/javascript">
<?php if(count($dis_proc_arr)>0){?>
getDiscount('<?php print $dis_proc_imp; ?>');
<?php } ?>
</script>
