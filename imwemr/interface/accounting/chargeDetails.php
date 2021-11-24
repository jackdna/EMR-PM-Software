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
$sm_unique_id=1; 
$seq = 0; 
$enc_imp=implode(',',$enc_arr);
$chl_id_imp=implode("','",$chl_id_arr);
$gro_imp=implode(',',$gro_arr);
$trans_copay_total_arr=array();
//------------------------ Manual Batch Transaction Detail ------------------------//
$qry = imw_query("select encounter_id from manual_batch_transactions where encounter_id in($enc_imp) and del_status=0 and post_status=0");
while($row = imw_fetch_array($qry)){ 
	$mbt_data[$row['encounter_id']]=$row['encounter_id'];
}

//------------------------ Group Detail ------------------------//
$qry = imw_query("select gro_id,name from groups_new where gro_id in($gro_imp)");
while($row = imw_fetch_array($qry)){ 
	$gro_data[$row['gro_id']]=$row['name'];
}

$getScanStr = "SELECT upload_lab_rad_data_id,uplaod_primary_id FROM upload_lab_rad_data
				 WHERE uplaod_primary_id in($enc_imp) AND scan_from='accounting' and patient_id='$patient_id'";
$getScanQry = imw_query($getScanStr);
while($getScanRows = imw_fetch_array($getScanQry)){
	$scan_file_arr[$getScanRows['uplaod_primary_id']]=$getScanRows['uplaod_primary_id'];
}

$qry = imw_query("SELECT * FROM patient_charge_list_details WHERE $whr_del_chld charge_list_id in('$chl_id_imp') ORDER BY display_order,charge_list_detail_id ASC");
while($row = imw_fetch_array($qry)){
	$chld_data_arr[$row['charge_list_id']][$row['charge_list_detail_id']]=$row;
	$proc_Arr[$row['charge_list_id']][] =$cpt_fee_tbl_data[$row['procCode']]['cpt4_code'];
	$chld_id_arr[$row['charge_list_detail_id']]=$row['charge_list_detail_id'];
}

//STATUS ARRAY
$statusArr = array();
$qry_claim_status =imw_query("SELECT id,status_name FROM claim_status where del_status='0' ORDER BY status_name ASC");
while($fet_claim_status=imw_fetch_assoc($qry_claim_status)){
	$statusArr[$fet_claim_status['id']]=$fet_claim_status['status_name'];
}

$chld_id_imp=implode("','",$chld_id_arr);
//------------------------ Deductible Detail ------------------------//
$qry = imw_query("SELECT * FROM payment_deductible where charge_list_detail_id in('$chld_id_imp')");
while($row = imw_fetch_array($qry)){
	$payment_deductible_data[$row['charge_list_detail_id']][]=$row;
}
foreach($chl_data_arr as $chl_key=>$chl_val){
	$primaryInsCoName=$secondaryInsCoName=$tertiaryInsCoName="";
	$ins_comp_name_arr=$ins_comp_ihc_arr=$show_total_chrg_arr=$show_total_approved_arr=$show_total_deduct_arr=$show_total_paid_arr=$show_final_paid_arr=$show_total_due_arr=array();
	$getEncounterRow=$chl_data_arr[$chl_key];
	$encounter_id=$getEncounterRow['encounter_id'];
	$collection = $getEncounterRow['collection'];
	$charge_list_id = $getEncounterRow['charge_list_id'];
	$totalEncounterBalance = $getEncounterRow['totalBalance'];
	$creditAmountBalance = $getEncounterRow['creditAmount'];
	$creditAmount = $getEncounterRow['creditAmount'];
	$tot_overPayment = $getEncounterRow['overPayment'];
	$copay = $getEncounterRow['copay'];
	$copayPaid = $getEncounterRow['copayPaid'];
	$referactionPaid = $getEncounterRow['referactionPaid'];
	$coPayNotRequired = $getEncounterRow['coPayNotRequired'];
	$coPayWriteOff = $getEncounterRow['coPayWriteOff'];
	$totalPaidAmt = $getEncounterRow['amtPaid'];
	$letter_sent_date = $getEncounterRow['letter_sent_date'];
	$statement_count = $getEncounterRow['statement_count'];
	$moaQualifier = $getEncounterRow['moaQualifier'];
	$enc_accept_assignment = $getEncounterRow['enc_accept_assignment'];
	$case_type_id = $getEncounterRow['case_type_id'];		
	$ar_claim_status_id = $getEncounterRow['claim_status'];			
	// INSURANCE PROVIDERS 
	$primaryInsProviderId = $getEncounterRow['primaryInsuranceCoId'];	
	$secondaryInsProviderId = $getEncounterRow['secondaryInsuranceCoId'];	
	$tertiaryInsProviderId = $getEncounterRow['tertiaryInsuranceCoId'];	
	
	
	if(($getEncounterRow['case_type_id']=='0') ||($primaryInsProviderId=='0' && $secondaryInsProviderId=='0' && $tertiaryInsProviderId=='0')){ 
		$case_type_nam = 'Self Pay';
	}else{
		$case_type_nam = $ins_case_name_arr[$getEncounterRow['case_type_id']];
	}
	
	$tooltip1=show_ins_tooltip($ins_comp_data[$primaryInsProviderId]);
	$tooltip2=show_ins_tooltip($ins_comp_data[$secondaryInsProviderId]);
	$tooltip3=show_ins_tooltip($ins_comp_data[$tertiaryInsProviderId]);
	
	foreach($ins_comp_data as $key=>$val){
		$ins_comp_data_arr=$ins_comp_data[$key];
		if($key==$primaryInsProviderId){
			$primaryInsCoName = $ins_comp_data_arr['in_house_code'];
			if($ins_comp_data_arr['in_house_code']==""){
				if(strlen($ins_comp_data_arr['name'])>13){
					$primaryInsCoName = substr($ins_comp_data_arr['name'], 0, 13)."..";
				}else{
					$primaryInsCoName = $ins_comp_data_arr['name'];
				}
			}
			$ins_comp_name_arr[$primaryInsProviderId]=$ins_comp_data_arr['name'];	
			$ins_comp_ihc_arr[$primaryInsProviderId]=$ins_comp_data_arr['in_house_code'];
		}
		if($key==$secondaryInsProviderId){
			$secondaryInsCoName = $ins_comp_data_arr['in_house_code'];
			if($ins_comp_data_arr['in_house_code']==""){
				if(strlen($ins_comp_data_arr['name'])>13){
					$secondaryInsCoName = substr($ins_comp_data_arr['name'], 0, 13)."..";
				}else{
					$secondaryInsCoName = $ins_comp_data_arr['name'];
				}
			}
			$ins_comp_name_arr[$secondaryInsProviderId]=$ins_comp_data_arr['name'];	
			$ins_comp_ihc_arr[$secondaryInsProviderId]=$ins_comp_data_arr['in_house_code'];
		}
		if($key==$tertiaryInsProviderId){
			$tertiaryInsCoName = $ins_comp_data_arr['in_house_code'];
			if($ins_comp_data_arr['in_house_code']==""){
				if(strlen($ins_comp_data_arr['name'])>13){
					$tertiaryInsCoName = substr($ins_comp_data_arr['name'], 0, 13)."..";
				}else{
					$tertiaryInsCoName = $ins_comp_data_arr['name'];
				}
			}
			$ins_comp_name_arr[$tertiaryInsProviderId]=$ins_comp_data_arr['name'];	
			$ins_comp_ihc_arr[$tertiaryInsProviderId]=$ins_comp_data_arr['in_house_code'];
		}
	}
		
	
?>
<table class="table table-bordered table-striped enter_pay_wd">
    <tr class="purple_bar border_none">
        <td colspan="2" class="text-nowrap">
            <strong>DOS:&nbsp;</strong><?php echo get_date_format($getEncounterRow['date_of_service']);?><br />
            <strong>E.Id:&nbsp;&nbsp;</strong><?php echo $getEncounterRow['encounter_id'];?>
            <?php
                if($mbt_data[$getEncounterRow['encounter_id']]>0){
            ?>
                <img src="../../library/images/batch_transaction_pending.png" title="Batch Transaction Pending">
            <?php }  ?>
        </td>
        <td colspan="3" class="text-nowrap">
            <span id="pt_name_div" style="display:none;"><strong>Patient Name</strong>: <?php echo $patientName.' - '.$patient_id;?><br /></span>
            <span><strong>Group Name: </strong><?php echo $gro_data[$getEncounterRow['gro_id']];?></span>
        </td>
        <td><strong>Ins. Case</strong> <br /><?php echo $case_type_nam;?></td>
        <?php if($primaryInsProviderId>0){?>
            <td <?php echo $tooltip1; ?>><strong>Primary</strong> <br /><?php echo substr($primaryInsCoName,0,4); ?>/<?php echo numberFormat($getEncounterRow['pri_copay'],2,'yes'); ?></td>
        <?php } ?>
        <?php if($secondaryInsProviderId>0){?>
            <td <?php echo $tooltip2; ?>><strong>Secondary</strong> <br /><?php echo substr($secondaryInsCoName,0,4); ?>/<?php echo numberFormat($getEncounterRow['sec_copay'],2,'yes'); ?></td>
        <?php } ?>
        <?php if($tertiaryInsProviderId>0){?>
            <td <?php echo $tooltip3; ?>><strong>Tertiary</strong> <br /><?php echo substr($tertiaryInsCoName,0,4); ?>/<?php echo numberFormat(0,2,'yes'); ?></td>
        <?php } ?>
         <td>
            <select name="ar_claim_status[<?php echo $getEncounterRow['encounter_id'];?>]" id="ar_claim_status_<?php echo $getEncounterRow['encounter_id'];?>" class="form-control minimal">
            	<option value="0">AR Status</option>
				<?php
                foreach($statusArr as $key=>$name)
                {
					$sel = '';
					if ($key==$ar_claim_status_id){
						$sel = 'SELECTED';
					}
                	echo "<option value='$key' $sel>$name</option>";
                }
                ?>
            </select>
        </td>
        <?php if($show_resp_party>0){ ?>
        	<td></td>
        <?php } ?>
        <td colspan="2"><?php if($_REQUEST['batch_pat_id']<=0 && $_REQUEST['deb_patient_id']<=0 && $_REQUEST['encounter_id']>0){?><div class="btn btn-info" onclick="outstanding_pat('other_procedure');">Credit to other Encounter</div><?php } ?></td>
        <td colspan="3"><strong>Auth#: </strong><?php if($getEncounterRow['auth_no']) echo $getEncounterRow['auth_no']; else echo '&nbsp;'; ?><br /><strong>Auth Amount: </strong><?php echo numberFormat($getEncounterRow['auth_amount'],2,'yes'); ?></td>
       	<td colspan="3">
            <div class="checkbox" style="margin:1px 0px 1px 0px !important;">
				<input type="checkbox" name="statement_pmt" id="statement_pmt" value="1">
                <label for="statement_pmt">Statement Pmt</label>
			</div>
            <span <?php echo show_tooltip($usr_full_name[$getEncounterRow['primaryProviderId']]); ?>><strong>Billing Provider: </strong> <?php echo strtoupper($dr_alias_name[$getEncounterRow['primaryProviderId']]); ?></span>
        </td>
        <td colspan="2"><?php if($_REQUEST['encounter_id']>0){ ?><div class="btn btn-info" style="width:80px;" onclick="show_tran_rec(this);">Active</div><?php } ?></td>
    	<td>
			<?php 
				if($_REQUEST['batch_pat_id']<=0 && $_REQUEST['deb_patient_id']<=0 && $_REQUEST['encounter_id']>0){
					if($scan_file_arr[$encounter_id]>0){
						echo '<img src="../../library/images/scanDcs_active.png" class="pointer" onClick="top.popup_win(\''.$GLOBALS['php_server'].'/interface/billing/scan/view_batch_images.php?upload_from=accounting&lab_id='.$encounter_id.'\',\'resizable=yes\')" >';
					}else{
						echo '<img src="../../library/images/scanDcs_deactive.png" class="pointer" onClick="top.popup_win(\''.$GLOBALS['php_server'].'/interface/billing/scan/view_batch_images.php?upload_from=accounting&lab_id='.$encounter_id.'\',\'resizable=yes\')" >';
					}
				}
			?>
       </td>     
    </tr>
    <tr class="grythead">
        <th>Apply</th>
        <th>CPT</th>
        <th>Dx Code</th>
        <th class="text-nowrap" style="width:63px;">T. Charges</th>	
        <th style="width:63px;">Allowed</th>
        <th style="width:63px;">Deductible</th>
        <?php 
        $due_col=1;
        if($primaryInsProviderId>0){ $due_col=$due_col+1;?>
            <th style="width:63px;">Pri Amt</th>
        <?php }?>
        <?php if($secondaryInsProviderId>0){ $due_col=$due_col+1;?>
             <th style="width:63px;">Sec Amt</th>
        <?php }?>
        <?php if($tertiaryInsProviderId>0){ $due_col=$due_col+1;?>
             <th style="width:63px;">Ter Amt</th>
        <?php }?>
        <th style="width:63px;">Patient Amt</th>
        <?php if($show_resp_party>0){ $due_col=$due_col+1;?>
        	<th style="width:63px;">Res. Party</th>
        <?php } ?>
        <th>Method</th>
        <th style="width:75px;">CC / Ch.# </th>	
        <th>Paid</th>
        <th class="text-nowrap">Balance</th>
        <th style="width:83px;">DOR</th>
        <th>DOT</th>
        <th>Adj</th>
        <th>Credit</th>	
        <th style="width:80px;">Code</th>
        <th>Batch#</th>
        <th>Oper</th>
    </tr>
	<?php
	$show_total_due_arr=array();
	$display_fee_row = false;
	$chargeListId = $getEncounterRow['charge_list_id'];
	$coPay = $getEncounterRow['copay'];
	$copayPaid = $getEncounterRow['copayPaid'];
	$referactionPaid = $getEncounterRow['referactionPaid'];
	$coPayNotRequired = $getEncounterRow['coPayNotRequired'];
	$coPayWriteOff = $getEncounterRow['coPayWriteOff'];
	
	$amountDue = $getEncounterRow['amountDue'];
	$totalPaidAmt = $getEncounterRow['amtPaid'];
	$totalAmt = $getEncounterRow['totalAmt'];
	$approvedTotalAmt = $getEncounterRow['approvedTotalAmt'];
	$deductibleTotalAmt = $getEncounterRow['deductibleTotalAmt'];
	$totalEncounterBalance = number_format($getEncounterRow['totalBalance'], 2);
	$coPayAdjusted = $getEncounterRow['coPayAdjusted'];
	$creditAmountBalance = $getEncounterRow['creditAmount'];
	$overPayment = $getEncounterRow['overPayment'];
	$operator_id = $getEncounterRow['operator_id'];
	
	$operatorNamePaid = $usr_alias_name[$operator_id];
	$docNamePaid = $dr_alias_name[$getEncounterRow['primaryProviderId']];

	$encCommentsInt = $getEncounterRow['encCommentsInt'];
	$encCommentsExt = $getEncounterRow['encCommentsExt'];
	$encCommentsIntDate = $getEncounterRow['encCommentsIntDate'];
	list($yearComment, $monthComment, $dayComment) = explode("-", $encCommentsIntDate);
	$encCommentsIntDate = $monthComment."-".$dayComment."-".$yearComment;
		
	$encCommentsExtDate = $getEncounterRow['encCommentsExtDate'];
	list($yearComment, $monthComment, $dayComment) = explode("-", $encCommentsExtDate);
	$encCommentsExtDate = $monthComment."-".$dayComment."-".$yearComment;
	$encCommentsIntOperatorId = $getEncounterRow['encCommentsIntOperatorId'];
	$encCommentsExtOperatorId = $getEncounterRow['encCommentsExtOperatorId'];
							
	//$postedDate = $getEncounterRow['postedDate'];
	$postedDate = $getEncounterRow['firstSubmitDate'];
	list($year, $month, $day)=explode("-", $postedDate);
	$postedDate=$month."-".$day."-".$year;
		
	$totalBalance = $totalAmt - $totalPaidAmt;
	$deductAmt = false;
	$whoPaidAmt = '';
	//-------------------- ASC ORDER BY CPT DESC. --------------------//

	$totalRefractionAmountFor = 0;
	$reflactionAmt = 0;
	$amountToPay = 0;
	$total_write_off_amount = 0;
	if(($copay>0) && ($copayPaid<=0) && ($coPayNotRequired!=1) && ($coPayWriteOff!='1')){
		$amountToPay = $amountToPay + $copay;
	}
	
	$proc_code_imp=implode(',',$proc_Arr[$chargeListId]);
	$copay_collect_proc=copay_apply_chk($proc_code_imp,'','');
	
	$getProcCountRows = count($chld_data_arr[$chargeListId]);
	$copay_chld_id_arr=array();
	foreach($chld_data_arr[$chargeListId] as $pcld_key=>$pcld_val){
		$getProcDetailsRows=$chld_data_arr[$chargeListId][$pcld_key];	
		$paidByCode="";
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
		$write_off_by = $getProcDetailsRows['write_off_by'];
		$del_wrt="1";
		$auto_wrt_amt=$getProcDetailsRows['totalAmount']-$getProcDetailsRows['approvedAmt'];
		if($getProcDetailsRows['totalAmount']>$getProcDetailsRows['approvedAmt']){
			$del_wrt="";
		}
		auto_writeoff_tran($patient_id,$charge_list_detail_id,$auto_wrt_amt,$write_off_by,$del_wrt);
		if($write_off_code_id>0){
			$write_off_code=$wrt_name_arr[$write_off_code_id];
		}
		
		$show_del_chld_style=$show_del_chld_read=$show_del_chld_disabled='';
		if($getProcDetailsRows['del_status']>0){
			$show_del_chld_style=" hide deleted ";
			$show_del_chld_read=" readonly";
			$show_del_chld_disabled=" disabled";
		}
		$dx_code=$dx1.' '.$dx2.' '.$dx3.' '.$dx4.' '.$dx5.' '.$dx6.' '.$dx7.' '.$dx8.' '.$dx9.' '.$dx10.' '.$dx11.' '.$dx12;									
		//----------------------- GETTING PAYMENT DETAILS -----------------------//
		
		$writeOffAmount = 0;
		//----------------------- GETTING PAYMENT DETAILS -----------------------//
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
		$pri_due = $getProcDetailsRows['pri_due'];
		$sec_due = $getProcDetailsRows['sec_due'];
		$tri_due = $getProcDetailsRows['tri_due'];
		$pat_due = $getProcDetailsRows['pat_due'];
		$write_off_Proc = $getProcDetailsRows['write_off'];
		$writeOffId = $getProcDetailsRows['charge_list_detail_id'];
		$pmt_notes = $getProcDetailsRows['pmt_notes'];

		//------------------------ Procedure total Write-Off amount ------------------------//
		foreach($payment_wrt_data[$encounter_id][$charge_list_detail_id] as $w_key=>$w_val){
			if($payment_wrt_data[$encounter_id][$charge_list_detail_id][$w_key]['delStatus']=='0'){	
				$writeOffAmount = $writeOffAmount+$payment_wrt_data[$encounter_id][$charge_list_detail_id][$w_key]['write_off_amount'];
			}
		}
		//------------------------ Procedure total Write-Off amount ------------------------//
		
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

		if($cptPracCode=='92015'){ 
			//$writeOff = '0.00';
		}					
		
		++$seq;

		$totalBalanceNewDue = $approvedTotalAmt - $creditAmount - $totalPaidAmt - $deductibleTotalAmt;
		if($copay){
			if(($copayPaid!=1) && ($coPayNotRequired != 0) && ($coPayWriteOff!='1')){
				$amount = $amount - $copay;
			}
		}
	
	if($getProcDetailsRows['del_status']==0){
		$copay_proc_code_arr[$charge_list_detail_id]=$cptPracCode;
		$copay_chld_id_arr[]=$charge_list_detail_id;
	}
	
	if($_REQUEST['batch_pat_id']>0 && $encounter_id>0){
		
		$batch_crd_amt_adust_arr=$batch_deb_amt_adust_arr=$trans_amt_total_arr=$trans_neg_total_arr=$trans_copay_total_batch_arr=$trans_deduct_amt_total_arr=$trans_write_amt_total_arr=$trans_allow_write_amt_total_arr=$trans_allow_amt_total_arr=$adj_amt_total_arr=array();
		$batch_crd_amt_adust=$batch_deb_amt_adust=$trans_amt_total=$trans_neg_total=$trans_copay_total=$copay_charge_list_detaill_id=$trans_deduct_amt_total=$trans_write_amt_total=$trans_allow_write_amt_total=$trans_allow_amt_total=$write_off_code_trans_id=$adj_amt_total=0;

		$gettot_crd3 = "SELECT crAppId,amountApplied,charge_list_detail_id_adjust,charge_list_detail_id FROM manual_batch_creditapplied WHERE (charge_list_detail_id_adjust  = '$charge_list_detail_id' or charge_list_detail_id  = '$charge_list_detail_id') and delete_credit='0' and credit_applied='1' and post_status='0'";
		$gettot_crdQry3 = imw_query($gettot_crd3);
		while($gettot_crdrow3 = imw_fetch_array($gettot_crdQry3)){
			if($gettot_crdrow3['charge_list_detail_id_adjust']==$charge_list_detail_id){
				$batch_crd_amt_adust_arr[$gettot_crdrow3['crAppId']] = $gettot_crdrow3['amountApplied'];
			}
			if($gettot_crdrow3['charge_list_detail_id']==$charge_list_detail_id){
				$batch_deb_amt_adust_arr[$gettot_crdrow3['crAppId']] = $gettot_crdrow3['amountApplied'];
			}
		}
		$batch_crd_amt_adust=array_sum($batch_crd_amt_adust_arr);
		$batch_deb_amt_adust=array_sum($batch_deb_amt_adust_arr);
		
		$getPayment_batch = imw_query("SELECT batch_trans_id FROM manual_batch_tx_payments WHERE charge_list_detail_id='$charge_list_detail_id' and del_status='0' and post_status='0' order by id desc limit 0,1");
		$getAccPayRows = imw_fetch_array($getPayment_batch);
		
		$last_batch_tx_trans_id="";
		$batch_trans_arr=$batch_ins_trans_amt_arr=$batch_tx_trans_amt_arr=array();
		$batch_qry=imw_query("select trans_id,batch_id,trans_amt,payment_claims,copay_charge_list_detaill_id,proc_allow_amt,write_off_code_id,ins_selected,cas_type,cas_code from manual_batch_transactions where charge_list_detaill_id='$charge_list_detail_id' and post_status!=1 and del_status=0");
		while($batch_row=imw_fetch_array($batch_qry)){
			if(strtolower($batch_row['payment_claims'])!='tx balance'){
				$batch_trans_arr[$batch_row['trans_id']]=$batch_row;
			}
			if(strtolower($batch_row['payment_claims'])=='tx balance' && $batch_row['trans_id']>=$getAccPayRows['batch_trans_id']){
				$last_batch_tx_trans_id=$batch_row['trans_id'];
				$batch_trans_arr[$batch_row['trans_id']]=$batch_row;
			}
		}
		
		foreach($batch_trans_arr as $trans_key=>$trans_val){
			$payment_claims=strtolower($batch_trans_arr[$trans_key]['payment_claims']);
			if($payment_claims=='paid' || $payment_claims=='deposit' || $payment_claims=='interest payment' || $payment_claims=='over adjustment'){
				$trans_amt_total_arr[]=$batch_trans_arr[$trans_key]['trans_amt'];
				if($trans_key>$last_batch_tx_trans_id){
					$batch_ins_trans_amt_arr[$batch_trans_arr[$trans_key]['ins_selected']]['yes'][]=$batch_trans_arr[$trans_key]['trans_amt'];
					$batch_ins_trans_amt_arr[$batch_trans_arr[$trans_key]['ins_selected']]['cas_code'][]=$batch_trans_arr[$trans_key]['cas_type'].' '.$batch_trans_arr[$trans_key]['cas_code'];
				}
			}
			if($payment_claims=='negative payment'){
				$trans_neg_total_arr[]=$batch_trans_arr[$trans_key]['trans_amt'];
			}
			if($payment_claims=='copay'){
				$trans_copay_total_batch_arr[]=$batch_trans_arr[$trans_key]['trans_amt'];
				$copay_charge_list_detaill_id=$batch_trans_arr[$trans_key]['copay_charge_list_detaill_id'];
				if($trans_key>$last_batch_tx_trans_id){
					$batch_ins_trans_amt_arr[$batch_trans_arr[$trans_key]['ins_selected']]['yes'][]=$batch_trans_arr[$trans_key]['trans_amt'];
				}
			}
			if($payment_claims=='deductible'){
				$trans_deduct_amt_total_arr[]=$batch_trans_arr[$trans_key]['trans_amt'];
				if($trans_key>$last_batch_tx_trans_id){
					$batch_ins_trans_amt_arr[$batch_trans_arr[$trans_key]['ins_selected']]['no'][]=$batch_trans_arr[$trans_key]['trans_amt'];
				}
			}
			if($payment_claims=='write off' || $payment_claims=='discount'){
				$trans_write_amt_total_arr[]=$batch_trans_arr[$trans_key]['trans_amt'];
				if($trans_key>$last_batch_tx_trans_id){
					$batch_ins_trans_amt_arr[$batch_trans_arr[$trans_key]['ins_selected']]['yes'][]=$batch_trans_arr[$trans_key]['trans_amt'];
				}
			}
			if($payment_claims=='allowed'){
				$trans_allow_amt_total_arr[]=$batch_trans_arr[$trans_key]['proc_allow_amt'];
				$trans_allow_write_amt_total_arr=$batch_trans_arr[$trans_key]['trans_amt'];
				$write_off_code_trans_id=$batch_trans_arr[$trans_key]['write_off_code_id'];
				if($trans_key>$last_batch_tx_trans_id){
					$batch_ins_trans_amt_arr[$batch_trans_arr[$trans_key]['ins_selected']]['yes'][]=$batch_trans_arr[$trans_key]['trans_amt'];
				}
			}
			if($payment_claims=='adjustment'){
				$adj_amt_total_arr[]=$batch_trans_arr[$trans_key]['trans_amt'];
			}
			if($payment_claims=='denied'){
				if($trans_key>$last_batch_tx_trans_id){
					$denail_data_arr=array();
					$denail_data_arr['denial_cpt_code']=$procId;
					if($batch_trans_arr[$trans_key]['cas_code']!=""){
						$denail_data_arr['denial_cas_code']=$batch_trans_arr[$trans_key]['cas_type'].' '.$batch_trans_arr[$trans_key]['cas_code'];
					}else{
						$denail_data_arr['denial_cas_code']=$batch_trans_arr[$trans_key]['cas_type'];
					}
					$denial_resp=denial_resp_fun($denail_data_arr);
					if($denial_resp>0){
						$batch_ins_trans_amt_arr[$batch_trans_arr[$trans_key]['ins_selected']]['no'][]=$batch_trans_arr[$trans_key]['trans_amt'];
					}
				}
			}
			if($payment_claims=='tx balance'){
				$batch_tx_trans_amt_arr[$batch_trans_arr[$trans_key]['ins_selected']]=$batch_trans_arr[$trans_key]['trans_amt'];
			}
		}
		$trans_amt_total=$batch_crd_amt_adust+array_sum($trans_amt_total_arr);
		
		$trans_neg_total=array_sum($trans_neg_total_arr);
		
		$trans_copay_total=array_sum($trans_copay_total_batch_arr);
		$trans_copay_total_arr[]=$trans_copay_total;
		
		$trans_deduct_amt_total=array_sum($trans_deduct_amt_total_arr);
		
		$trans_write_amt_total=array_sum($trans_write_amt_total_arr);
		$writeOffAmount=$writeOffAmount+$trans_write_amt_total;
		
		$trans_allow_amt_total=array_sum($trans_allow_amt_total_arr);
		$trans_allow_write_amt_total=array_sum($trans_allow_write_amt_total_arr);
		if($trans_allow_write_amt_total>0){
			$chk_old_writeoff_amt=$trans_allow_write_amt_total-$writeOff;
			$writeOff=$trans_allow_write_amt_total;
		}else{
			$chk_old_writeoff_amt=$trans_allow_write_amt_total;
		}
		
		$adj_amt_total=array_sum($adj_amt_total_arr);
					 
		$total_writ_amt=$trans_write_amt_total+$chk_old_writeoff_amt;
		$tot_allowed_amt_gap=0;
		if($trans_allow_amt_total>0){
			$tot_allowed_amt_gap=$approvedAmt-$trans_allow_amt_total;
			$writeOff=$tot_allowed_amt_gap;
		}
		$NewBalance=($NewBalance+$trans_neg_total)-($tot_allowed_amt_gap+$trans_amt_total+$total_writ_amt+$trans_copay_total);
		
		$overPaymentForProc_trans=$neg_paid_trans="";
		if($NewBalance<0){
			$overPaymentForProc_trans=substr($NewBalance,1);
			$overPaymentForProc=$overPaymentForProc+$overPaymentForProc_trans;
			$NewBalance=0;
		}
		if($adj_amt_total>0){
			if($overPaymentForProc>0){
				if($overPaymentForProc>=$adj_amt_total){
					$overPaymentForProc=$overPaymentForProc-$adj_amt_total;
					$NewBalance=0;
				}else{
					$chk_adj_bal=$adj_amt_total-$overPaymentForProc;
					$NewBalance=$chk_adj_bal;
					$paidForProc=$paidForProc-$chk_adj_bal;
					$overPaymentForProc=0;
				}
			}else{
				$NewBalance=$NewBalance+$adj_amt_total;
				$overPaymentForProc=0;
				$paidForProc=$paidForProc-$adj_amt_total;
			}
		}	
		
		$deductAmt=$deductAmt+$trans_deduct_amt_total;
		//$writeOff=$writeOff+$trans_write_amt_total;
		if($batch_deb_amt_adust>0){
			if($overPaymentForProc>=$batch_deb_amt_adust){
				$overPaymentForProc = $overPaymentForProc-$batch_deb_amt_adust;
			}else{
				$chk_ovr_deb_amt=$batch_deb_amt_adust-$overPaymentForProc;
				$paidForProc = $paidForProc-$chk_ovr_deb_amt;
				$NewBalance = $NewBalance+$chk_ovr_deb_amt;
				$overPaymentForProc = 0;
			}
		}
		if($trans_neg_total>0){
			if($overPaymentForProc>=$trans_neg_total){
				$overPaymentForProc=$overPaymentForProc-$trans_neg_total;
			}else{
				$neg_paid_trans=$trans_neg_total-$overPaymentForProc;
				$overPaymentForProc=0;
			}
		}
		$paidForProc=($paidForProc+$trans_amt_total+$trans_copay_total)-($overPaymentForProc_trans+$neg_paid_trans);
		if($trans_allow_amt_total>0){
			$approvedAmt=$trans_allow_amt_total;
		}
	
		if($getProcDetailsRows['proc_selfpay']==1 || ($primaryInsProviderId==0 && $secondaryInsProviderId==0 && $tertiaryInsProviderId==0)){
			$pat_due = $NewBalance;
		}else{
			if($batch_tx_trans_amt_arr[1]>0 || $batch_tx_trans_amt_arr[2]>0 || $batch_tx_trans_amt_arr[3]>0 || $batch_tx_trans_amt_arr[0]>0){
				$pri_due=$sec_due=$tri_due=$pat_due=0;
				if($batch_tx_trans_amt_arr[1]>0){
					$pri_due=$batch_tx_trans_amt_arr[1];
				}
				if($batch_tx_trans_amt_arr[2]>0){
					$sec_due=$batch_tx_trans_amt_arr[2];
				}
				if($batch_tx_trans_amt_arr[3]>0){
					$tri_due=$batch_tx_trans_amt_arr[3];
				}
				if($batch_tx_trans_amt_arr[0]>0){
					$pat_due=$batch_tx_trans_amt_arr[0];
				}	
			}
			if($primaryInsProviderId>0 && array_sum($batch_ins_trans_amt_arr[1]['no'])==0 && array_sum($batch_ins_trans_amt_arr[1]['yes'])==0 && $pri_due>0 && in_array('OA 100',$batch_ins_trans_amt_arr[1]['cas_code'])<=0){
				$pri_due = $pri_due-array_sum($batch_ins_trans_amt_arr[1]['yes']);
			}else{
				$sec_due = ($sec_due+$pri_due)-array_sum($batch_ins_trans_amt_arr[1]['yes']);
				$pri_due = 0;
			}
			if($secondaryInsProviderId>0 && array_sum($batch_ins_trans_amt_arr[2]['no'])==0 && array_sum($batch_ins_trans_amt_arr[2]['yes'])==0  && $sec_due>0 && in_array('OA 100',$batch_ins_trans_amt_arr[2]['cas_code'])<=0){
				$sec_due = $sec_due-array_sum($batch_ins_trans_amt_arr[2]['yes']);
			}else{
				$tri_due = ($sec_due+$tri_due)-array_sum($batch_ins_trans_amt_arr[2]['yes']);
				$sec_due = 0;
			}
			if($tertiaryInsProviderId>0 && array_sum($batch_ins_trans_amt_arr[3]['no'])==0 && array_sum($batch_ins_trans_amt_arr[3]['yes'])==0  && $tri_due>0 && in_array('OA 100',$batch_ins_trans_amt_arr[3]['cas_code'])<=0){
				$tri_due = $tri_due-array_sum($batch_ins_trans_amt_arr[3]['yes']);
			}else{
				$tri_due = 0;
			}
			
			if($pat_due>0){
				$pat_due = $pat_due-array_sum($batch_ins_trans_amt_arr[0]['yes']);
			}
			
			if($NewBalance!=($pri_due+$sec_due+$tri_due+$pat_due)){
				$diff_balance=$NewBalance-($pri_due+$sec_due+$tri_due+$pat_due);
				if($diff_balance>0){
					if($pat_due>0){
						$pat_due=$pat_due+$diff_balance;
					}else if($pri_due>0){
						$pri_due=$pri_due+$diff_balance;
					}else if($sec_due>0){
						$sec_due=$sec_due+$diff_balance;
					}else if($tri_due>0){
						$tri_due=$tri_due+$diff_balance;
					}
				}
				if($diff_balance<0){
					$diff_balance=str_replace('-','',$diff_balance);
					if($pat_due>=$diff_balance){
						$pat_due=$pat_due-$diff_balance;
					}else if($pri_due>=$diff_balance){
						$pri_due=$pri_due-$diff_balance;
					}else if($sec_due>=$diff_balance){
						$sec_due=$sec_due-$diff_balance;
					}else if($tri_due>=$diff_balance){
						$tri_due=$tri_due-$diff_balance;
					}
				}
			}
			
			if($pri_due>0 || $sec_due>0 || $tri_due>0){
			}else{
				$pat_due = $NewBalance;
			}
			
			$pri_due=numberFormat($pri_due,2,'','','no');
			$sec_due=numberFormat($sec_due,2,'','','no');
			$tri_due=numberFormat($tri_due,2,'','','no');
			$pat_due=numberFormat($pat_due,2,'','','no');
		}
	
	}
	
	$approvedAmt = round($approvedAmt, 2);
	$deductAmt = number_format($deductAmt, 2);
	$paidForProc = $paidForProc;
	$creditProcAmount = number_format($creditProcAmount, 2);
	$totalAmount = $totalAmount;
	$amount = number_format($amount, 2);
	$copay_collect=copay_apply_chk($cpt4_code,'','');
	if($getProcDetailsRows['proc_selfpay']==1 && $getProcDetailsRows['cash_discount']==0 && $_SESSION['cs_dis_enc'][$encounter_id]!="yes"){
		$dis_proc_arr[]=$seq;
	}
	?>
	<?php 
	 if($cpt_onetime==0){
		$cpt_onetime=0;
		 if($copay_collect_proc==true && $copay_collect_proc[0]==true){
			if($copay_collect[0]==true && $copay_collect[0]==true){
				$copay_apply_for_chld=$charge_list_detail_id;
		?>
			<input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure" id="copay_apply_procedure">
		<?php
				$cpt_onetime++; 
			}
		}else{
			if($balForProc>0){
				$copay_apply_for_chld=$charge_list_detail_id;
	?>
				<input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure" id="copay_apply_procedure">
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
		  if($coPay==$tot_paid_chk_arr[$encounter_id]){
				$minus_copay_value=$coPay;
				$min_copay_one++;
			}else{
				if($tot_paid_chk_arr[$encounter_id]<>0){
					$minus_copay_value=$coPay-$tot_paid_chk_arr[$encounter_id];
					$min_copay_one++;
				}
			}
		}
		if($min_copay_one>0){
			$tot_paid_chk2=$tot_paid_chk_arr[$encounter_id];
		}
	}else{
		$minus_copay_value=0.00;
	}
?>
<?php
if($cptPracCode==$return_chk_proc){$proc_red_bg="proc_red_bg";}else{$proc_red_bg="";} 
	if($getProcDetailsRows['del_status']==0){
		$show_total_chrg_arr[]=$totalAmount;
		$show_total_approved_arr[]=$approvedAmt;
		$show_total_deduct_arr[]=$deductAmt;
	}
?>
	<tr class="valign-top <?php echo $show_del_chld_style; ?>">
		<td rowspan="2">
        	<input type="hidden" name="dos_<?php echo $seq; ?>" id="dos_<?php echo $seq; ?>" value="<?php echo $getEncounterRow['date_of_service']; ?>">
            <input type="hidden" name="task_group_id_<?php echo $seq; ?>" id="task_group_id_<?php echo $seq; ?>" value="<?php echo $getEncounterRow['gro_id']; ?>" >
			<input type="hidden" name="encounter_id_<?php echo $seq; ?>" id="encounter_id_<?php echo $seq; ?>" value="<?php echo $encounter_id; ?>">
			<input type="hidden" name="pri_ins_<?php echo $seq; ?>" id="pri_ins_<?php echo $seq; ?>" value="<?php echo $primaryInsProviderId; ?>">
			<input type="hidden" name="sec_ins_<?php echo $seq; ?>" id="sec_ins_<?php echo $seq; ?>" value="<?php echo $secondaryInsProviderId; ?>">
			<input type="hidden" name="ter_ins_<?php echo $seq; ?>" id="ter_ins_<?php echo $seq; ?>" value="<?php echo $tertiaryInsProviderId; ?>">
			<input type="hidden" name="chld_id_<?php echo $seq; ?>" id="chld_id_<?php echo $seq; ?>" value="<?php echo $charge_list_detail_id; ?>">
			<input type="hidden" name="chl_id_<?php echo $seq; ?>" id="chl_id_<?php echo $seq; ?>" value="<?php echo $chargeListId; ?>">
			<input type="hidden" name="proc_selfpay[<?php echo $seq; ?>]" id="proc_selfpay<?php echo $seq; ?>" value="<?php echo $getProcDetailsRows['proc_selfpay']; ?>"> 
			<input type="hidden" name="total_amt_<?php echo $seq; ?>" id="total_amt_<?php echo $seq; ?>" value="<?php echo $totalAmount; ?>">
			<input type="hidden" name="write_off_code<?php echo $seq; ?>" id="write_off_code<?php echo $seq; ?>" value="<?php echo $w_code; ?>">
            <input type="hidden" name="units_<?php echo $seq; ?>" id="units_<?php echo $seq; ?>" value="<?php echo $units; ?>">

			<div class="checkbox">
				<input type="checkbox" <?php echo $show_del_chld_disabled; ?> name="chkbx[<?php echo $seq; ?>]" id="chkbx<?php echo $seq; ?>" value="<?php echo $charge_list_detail_id; ?>" onClick="return checkPaymentBox('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>','<?php echo $getProcCountRows; ?>');"/>
				<label for="chkbx<?php echo $seq; ?>"></label>
			</div>
		</td>
		<td rowspan="2" class="<?php echo $proc_red_bg; ?>">
        	<input type="hidden" name="cpt_prac_code_<?php echo $seq; ?>" id="cpt_prac_code_<?php echo $seq; ?>" value="<?php echo $cptPracCode; ?>">
            <input type="hidden" name="cpt_prac_code_id_<?php echo $seq; ?>" id="cpt_prac_code_id_<?php echo $seq; ?>" value="<?php echo $procId; ?>">
			<input type="hidden" name="copay_paid<?php echo $seq; ?>" id="copay_paid<?php echo $seq; ?>" value="<?php echo $tot_paid_chk2; ?>">
			<input type="hidden" name="minus_copay<?php echo $seq; ?>" id="minus_copay<?php echo $seq; ?>" value="<?php echo $minus_copay_value; ?>">
			<span><?php if($coPayAdjustedAmount==1 || $copay_charge_list_detaill_id>0){ ?><img src="../../library/images/confirm.gif" style="width:16px; vertical-align:middle;"/><?php } ?></span>
			<span id="cptIdTd<?php echo $seq; ?>" <?php echo show_tooltip($cptDesc); ?>  style="margin-right:10px;"> <?php echo $cptPracCode; ?> </span>
		</td>
		<td rowspan="2"><?php echo $dx_code; ?></td>
		<td rowspan="2" class="text-nowrap">
            <div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
				<input type="text" <?php echo $show_del_chld_read; ?> name="totalFee<?php echo $seq; ?>" id="totalFee<?php echo $seq; ?>" class="form-control" value="<?php echo str_replace(',','',number_format($totalAmount,2)); ?>" onChange="ChargesChange('<?php echo $seq; ?>');return checkChkBox('<?php echo $seq; ?>','totalFee');">
			</div>
        </td>
		<td rowspan="2">
			<div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
				<input type="text" <?php echo $show_del_chld_read; ?> name="approvedText<?php echo $seq; ?>" id="approvedText<?php echo $seq; ?>" class="form-control" value="<?php echo str_replace(',','',number_format($approvedAmt,2)); ?>" onChange="set_write_off_id('<?php echo $seq; ?>',event.y);paymentChange_all('<?php echo $seq; ?>','allow');checkChkBox(<?php echo $seq; ?>,'approvedText');" onBlur="approvedBlur('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>', this); paymentChange('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>');">
			</div>
			<input type="hidden" name="appActualText<?php echo $seq; ?>" id="appActualText<?php echo $seq; ?>" value="<?php echo $approvedAmt; ?>">
		</td>
		<td rowspan="2">
			<div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
				<input type="text" <?php echo $show_del_chld_read; ?> style=" <?php if($deductAmt>0){echo"color:#FFC000;";}?>" name="deductibleText<?php echo $seq; ?>" id="deductibleText<?php echo $seq; ?>" class="form-control" value="<?php echo str_replace(',','',$deductAmt);?>" onChange="return deductChange('<?php echo $seq; ?>'),paymentChange_bydeduct('<?php echo $seq; ?>',this.value,'<?php echo $deductAmt; ?>'),paymentChange('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>')">
			</div>
		</td>
        <?php
			$hide_due_box="";
			if($_REQUEST['batch_pat_id']>0){
				//$hide_due_box=" hide";
			}
		?>
		<?php if($primaryInsProviderId>0){?>
			 <td>
				 <div class="input-group mb5">
					<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
					<input type="text" <?php echo $show_del_chld_read; ?> name="pri_paid_<?php echo $seq; ?>" id="pri_paid_<?php echo $seq; ?>" class="form-control" value="" onChange="checkChkBox(<?php echo $seq; ?>,'pri_paid_'); copy_method(<?php echo $seq; ?>);">
				 </div>
			</td>
		<?php }?>
		<?php if($secondaryInsProviderId>0){?>
			<td>
				<div class="input-group mb5">
					<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
					<input type="text" <?php echo $show_del_chld_read; ?> name="sec_paid_<?php echo $seq; ?>" id="sec_paid_<?php echo $seq; ?>" class="form-control" value="" onChange="checkChkBox(<?php echo $seq; ?>,'sec_paid_'); copy_method(<?php echo $seq; ?>);">
				</div>
			</td>
		<?php }?>
		<?php if($tertiaryInsProviderId>0){?>
			<td>
				 <div class="input-group mb5">
					<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
					<input type="text" <?php echo $show_del_chld_read; ?> name="ter_paid_<?php echo $seq; ?>" id="ter_paid_<?php echo $seq; ?>" class="form-control" value="" onChange="checkChkBox(<?php echo $seq; ?>,'ter_paid_'); copy_method(<?php echo $seq; ?>);">
				</div>
			</td>
		<?php }?>
		<td>
			<div class="input-group mb5">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
				<input type="text" <?php echo $show_del_chld_read; ?> name="pat_paid_<?php echo $seq; ?>" id="pat_paid_<?php echo $seq; ?>" class="form-control" value="" onChange="checkChkBox(<?php echo $seq; ?>,'pat_paid_'); copy_method(<?php echo $seq; ?>);">
			</div>
		</td>
        <?php if($show_resp_party>0){ ?>
        <td>
			<div class="input-group mb5">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
				<input type="text" <?php echo $show_del_chld_read; ?> name="resp_paid_<?php echo $seq; ?>" id="resp_paid_<?php echo $seq; ?>" class="form-control" value="" onChange="checkChkBox(<?php echo $seq; ?>,'resp_paid_'); copy_method(<?php echo $seq; ?>);">
			</div>
		</td>
        <?php } ?>
		 <td style="width:83px !important;">
			<?php
				$trans_method_arr=array();
				if($_REQUEST['batch_pat_id']>0){
					/*$trans_arr=array("Paid","Deposit","Discount","Denied","Deductible","Write Off","Adjustment","Over Adjustment",
					"Interest Payment","Refund","Negative Payment","Tx Balance","Update Allow Amt");*/
					$trans_arr["Paid"]="";
					$trans_arr["Deposit"]="";
					$trans_arr["Patient/Guarantor"]=array("Co Insurance","Discount","Adjustment","Co Payment");
					$trans_arr["Insurance"]=array("Denied","Write Off","Adjustment","Over Adjustment","Interest Payment","Negative Payment","Update Allow Amt");
					$trans_arr["Update Amount"]=array("Tx Balance");
					$trans_arr["Deductible"]="";
					$trans_arr["Refund"]="";
				}else{
					//$main_trans_arr=array("Paid","Deposit","Patient/Guarantor","Insurance","Update Amount","Deductible","Returned Check","Refund");
					$trans_arr["Paid"]="";
					$trans_arr["Deposit"]="";
					$trans_arr["Patient/Guarantor"]=array("Patient Pre Pmts","Check In/Out","Co Insurance","Discount","Adjustment","Co Payment");
					$trans_arr["Insurance"]=array("Denied","Write Off","Adjustment","Over Adjustment","Interest Payment","Negative Payment","Update Allow Amt");
					$trans_arr["Update Amount"]=array("Tx Balance","Debit","Credit");
					$trans_arr["Deductible"]="";
					$trans_arr["Returned Check"]="";
					$trans_arr["Refund"]="";
					/*$trans_arr=array("Paid","Deposit","Discount","Denied","Deductible","Write Off","Adjustment","Over Adjustment",
					"Interest Payment","Returned Check","Refund","Debit","Credit","Negative Payment","Tx Balance","Update Allow Amt",
					"Check In/Out","Patient Pre Pmts","Co Insurance");*/
				}
				$show_credit="";
				if($_REQUEST['deb_patient_id']>0){
					$show_credit="Credit";
				}
				
				$paid_ins_arr['Patient']='Patient';
				if($primaryInsProviderId>0){
					$paid_ins_arr['Primary']='Primary';
				}
				if($secondaryInsProviderId>0){
					$paid_ins_arr['Secondary']='Secondary';
				}
				if($tertiaryInsProviderId>0){
					$paid_ins_arr['Tertiary']='Tertiary';
				}
				
				//"Check In/Out","Patient Pre Payments"
				//$method_arr=array("Cash","Check","Credit Card","EFT","Money Order","VEEP");
				$method_arr=$payment_method_arr;
				$method_cc_arr=array("American Express","Care Credit","Discover","Master Card","Visa","Others");
				$no_need_type_arr=array("Discount","Denied","Deductible","Write Off","Debit","Credit","Check In/Out","Tx Balance","Patient Pre Pmts","Returned Check","Co Insurance","Co Payment");
				foreach($trans_arr as $main_trans_key=>$main_trans_val){
					$trans_method_arr[$main_trans_key]=array($main_trans_key,array(),$main_trans_key);
					if($main_trans_key=="Paid" || $main_trans_key=="Deposit" || $main_trans_key=="Refund"){
						foreach($method_arr as $method_key=>$method_val){
							$final_method_val=array($method_val,array(),$main_trans_key.' - '.$method_val);
							$trans_method_arr[$main_trans_key][1][$method_key]=$final_method_val;
							if($method_val=="Credit Card"){
								foreach($method_cc_arr as $method_cc_key=>$method_cc_val){
									$final_method_cc_val=array($method_cc_val,array(),$main_trans_key.' - '.$method_val.' - '.$method_cc_val);
									$trans_method_arr[$main_trans_key][1][$method_key][1][$method_cc_key]=$final_method_cc_val;
								}
							}
						}
					}else{
						foreach($trans_arr[$main_trans_key] as $trans_key=>$trans_val){
							$trans_method_arr[$main_trans_key][1][$trans_key]=array($trans_val,array(),$trans_val);
							if(!in_array($trans_val,$no_need_type_arr)){
								if($trans_val=="Update Allow Amt"){
									foreach($paid_ins_arr as $paid_ins_key=>$paid_ins_val){
										$final_method_val=array($paid_ins_val,array(),$trans_val.' - '.$paid_ins_val);
										$trans_method_arr[$main_trans_key][1][$trans_key][1][$paid_ins_key]=$final_method_val;
									}
								}else{
									foreach($method_arr as $method_key=>$method_val){
										$final_method_val=array($method_val,array(),$trans_val.' - '.$method_val);
										$trans_method_arr[$main_trans_key][1][$trans_key][1][$method_key]=$final_method_val;
										if($method_val=="Credit Card"){
											foreach($method_cc_arr as $method_cc_key=>$method_cc_val){
												$final_method_cc_val=array($method_cc_val,array(),$trans_val.' - '.$method_val.' - '.$method_cc_val);
												$trans_method_arr[$main_trans_key][1][$trans_key][1][$method_key][1][$method_cc_key]=$final_method_cc_val;
											}
										}
									}
								}
							}
						}
					}
				}//pre($trans_method_arr);
			?>
			<div class="input-group mb5">
				<input type="text" name="payment_method_<?php echo $seq; ?>" id="payment_method_<?php echo $seq; ?>" class="form-control payment_method_css" value="<?php echo $show_credit; ?>" readonly style="width:83px !important;" onChange="checkChkBox(<?php echo $seq; ?>,'payment_method_');" data-seq-id="<?php echo $seq; ?>">
				<?php  if($show_credit==""){echo get_simple_menu($trans_method_arr,"menu_method_".$seq,"payment_method_".$seq,"","0",$sm_unique_id++);}?>
			</div>
		</td>
		<td>
			<input type="text" <?php echo $show_del_chld_read; ?> name="check_cc_no_<?php echo $seq; ?>" id="check_cc_no_<?php echo $seq; ?>" class="form-control ccnocls" value="" style="width:75px !important;">
		</td>
		<td class="text_green text-right">
			<?php 
				if($approvedAmt>=$tot_paid_chk2){
					$show_final_paid=$paidForProc+$overPaymentForProc+$tot_paid_chk2;
				}else{
					$show_final_paid=$paidForProc+$overPaymentForProc;
				}
				
				echo numberFormat($show_final_paid,2,'yes'); 
				$show_final_paid_arr[]=$show_final_paid;
			?>
			<!--<input type="text" name="payNew<?php echo $seq; ?>" id="payNew<?php echo $seq; ?>" class="form-control" style="width:90px;" value="<?php echo "0.00"; ?>" onBlur="return selectChanges('<?php echo $seq; ?>')" onChange="paymentChange_all('<?php echo $seq; ?>','');return paymentChange('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>');">-->
			<input type="hidden" name="paidAmtPrev<?php echo $seq; ?>" id="paidAmtPrev<?php echo $seq; ?>" value="<?php echo $paidForProc; ?>">
			<input type="hidden" name="paidAmtText<?php echo $seq; ?>" id="paidAmtText<?php echo $seq; ?>" onChange="return checkChkBox(<?php echo $seq; ?>,'paidAmtText');" value="<?php echo "0.00"; ?>">
			<input type="hidden" name="adj_amt<?php echo $seq; ?>" id="adj_amt<?php echo $seq; ?>" value="<?php echo $adj_amt;?>">
		</td>
		 <?php
			if($NewBalance <= 0){
				$bal_col="Green";
			}else{
				$bal_col="Red";
			}
		?>
		<?php
			if($getProcDetailsRows['del_status']==0){
				if($overPaymentForProc>0){
					$show_total_due_arr[]=-$overPaymentForProc;
				}else{
					$show_total_due_arr[]=$NewBalance;
				}
			}
		?>	
		<td class="text-right f-bold" id="newBalanceTd<?php echo $seq; ?>">
			<span style="color:<?php echo $bal_col;?>">
				<?php 
					if($NewBalance<0){ 
						echo '0.00'; 
					}else{ 
						if($overPaymentForProc>0){
							echo '-'."$".number_format($overPaymentForProc,2);
						}else{
							echo "$".number_format($NewBalance,2);
						} 
					}
				?>
			</span>
			<input type="hidden" value="<?php if($NewBalance<0){ echo '0.00'; }else{ if($overPaymentForProc>0) echo '0.00'; else echo $NewBalance; } ?>" name="bal_chk_for_copay<?php echo $seq; ?>" id="bal_chk_for_copay<?php echo $seq; ?>">
		</td>
		<td>
        	<?php
				$show_paid_date=date(phpDateFormat(), strtotime(str_replace('-', '/', $cur_date)));
				if($_REQUEST['b_id']>0 && $default_payment_date!="0000-00-00"){
					$show_paid_date = get_date_format($default_payment_date, phpDateFormat());
				}
			?>
			<div class="input-group">
				<input type="text" <?php echo $show_del_chld_read; ?> name="paid_date_<?php echo $seq; ?>" id="paid_date_<?php echo $seq; ?>" value="<?php echo $show_paid_date; ?>" onBlur="checkdate(this);" class="form-control date-pick"  style="width:83px !important;"/>
			</div>
			<input type="hidden"  name="overPayment<?php echo $seq; ?>" id="overPayment<?php echo $seq; ?>" value="<?php echo $overPaymentForProc; ?>">
			<input type="hidden" name="overPaymentNow<?php echo $seq; ?>" id="overPaymentNow<?php echo $seq; ?>" value="">
			<input type="hidden" name="overPayments_chk<?php echo $seq; ?>" id="overPayments_chk<?php echo $seq; ?>" value="<?php echo $overPaymentForProc; ?>"> 
		</td>
		<td>&nbsp;</td>
		<td id="writeOffTd<?php echo $seq; ?>" class="text-right">
			<?php 
			if($writeOffAmount>0){
				$tot_writeOff=$writeOffAmount+$writeOff;
				echo "$".number_format($tot_writeOff, 2); 
			}else{
				echo "$".number_format($writeOff, 2); 
			}
			
			?>
		</td>
		<td class="text_green text-right" id="creditAmtTd<?php echo $seq; ?>">
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
		<td class="dropdownfix">
			<select name="write_off_code_<?php echo $seq; ?>[]"  id="write_off_code_<?php echo $seq; ?>" multiple class="selectpicker" data-width="80" data-size="20" data-dropup-auto="false" data-live-search="true">
            	<option value="0">Code</option>
				<optgroup label="Write off Code" class="hide" data-max-options="1">
					<?php
						foreach($write_off_code_data as $d_key=>$d_val){	
							$sel_write=$write_off_code_data[$d_key];

							$val_id=$sel_write['w_id'].'_wrt';
					?>
						<option value="<?php echo $val_id;?>"><?php echo $sel_write['w_code'];?></option>
					<?php } ?>
				</optgroup>
                <optgroup label="Discount Code" class="hide" data-max-options="1">
					<?php
						foreach($discount_code_data as $d_key=>$d_val){	
							$sel_dis=$discount_code_data[$d_key];
							$val_id=$sel_dis['d_id'].'_dis';
					?>
						<option value="<?php echo $val_id;?>"><?php echo $sel_dis['d_code'];?></option>
					<?php } ?>
				</optgroup>
				<optgroup label="Adj Code" class="hide" data-max-options="1">
					<?php
						foreach($adj_code_data as $a_key=>$a_val){	
							$sel_adj=$adj_code_data[$a_key];
							$val_id=$sel_adj['a_id'].'_adj';
					?>
					<option value="<?php echo $val_id;?>"><?php echo $sel_adj['a_code'];?></option>
					<?php } ?>
				</optgroup>
                <optgroup label="Reason Code">
					<?php
						foreach($cas_code_data as $r_key=>$r_val){	
							$sel_adj=$cas_code_data[$r_key];
							$val_id=$sel_adj['cas_id'].'_cas';
							$cas_desc=str_replace("'","",$sel_adj['cas_desc']);
							if(strlen($cas_desc)>100){
								$cas_desc=substr($cas_desc,0,100).'...';
							}
					?>
					<option value="<?php echo $val_id;?>"><?php echo $sel_adj['cas_code'].' - '.$cas_desc;?></option>
					<?php } ?>
				</optgroup>
			</select>
		</td>
        <td></td>
        <td></td>
	</tr>
    <tr class="valign-top <?php echo $show_del_chld_style; ?>">
		<?php if($primaryInsProviderId>0){?>
			 <td>
				 <div class="input-group <?php echo $hide_due_box; ?>">
					<div class="input-group-addon" style="background-color:#F76464; color:#fff;"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
					<input type="text" readonly name="pri_due_<?php echo $seq; ?>" id="pri_due_<?php echo $seq; ?>" class="form-control" value="<?php echo $pri_due; ?>" onChange="checkChkBox(<?php echo $seq; ?>,'pri_due_'); tx_balance();">
				</div>
				<input type="hidden" name="pri_due_old_<?php echo $seq; ?>" id="pri_due_old_<?php echo $seq; ?>" class="input_text_10" value="<?php echo $pri_due; ?>" onChange="checkChkBox(<?php echo $seq; ?>,'pri_due_old_');">
			 </td>
		<?php }?>
		<?php if($secondaryInsProviderId>0){?>
			<td>
				<div class="input-group <?php echo $hide_due_box; ?>">
					<div class="input-group-addon" style="background-color:#F76464; color:#fff;"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
					<input type="text" readonly name="sec_due_<?php echo $seq; ?>" id="sec_due_<?php echo $seq; ?>" class="form-control" value="<?php echo $sec_due; ?>" onChange="checkChkBox(<?php echo $seq; ?>,'sec_due_'); tx_balance();">
				</div>
				<input type="hidden" name="sec_due_old_<?php echo $seq; ?>" id="sec_due_old_<?php echo $seq; ?>" class="input_text_10" value="<?php echo $sec_due; ?>" onChange="checkChkBox(<?php echo $seq; ?>,'sec_due_old_');">
			</td>
		<?php }?>
		<?php if($tertiaryInsProviderId>0){?>
			<td>
				 <div class="input-group <?php echo $hide_due_box; ?>">
					<div class="input-group-addon" style="background-color:#F76464; color:#fff;"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
					<input type="text" readonly name="ter_due_<?php echo $seq; ?>" id="ter_due_<?php echo $seq; ?>" class="form-control" value="<?php echo $tri_due; ?>" onChange="checkChkBox(<?php echo $seq; ?>,'ter_due_'); tx_balance();">
				</div>
				 <input type="hidden" name="ter_due_old_<?php echo $seq; ?>" id="ter_due_old_<?php echo $seq; ?>" class="input_text_10" value="<?php echo $tri_due; ?>" onChange="checkChkBox(<?php echo $seq; ?>,'ter_due_old_');">
			</td>
		<?php }?>
		<td>
			<div class="input-group <?php echo $hide_due_box; ?>">
				<div class="input-group-addon" style="background-color:#F76464; color:#fff;"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
				<input type="text" readonly name="pat_due_<?php echo $seq; ?>" id="pat_due_<?php echo $seq; ?>" class="form-control" value="<?php echo $pat_due; ?>" onChange="checkChkBox(<?php echo $seq; ?>,'pat_due_'); tx_balance();">
			</div>
			<input type="hidden" name="pat_due_old_<?php echo $seq; ?>" id="pat_due_old_<?php echo $seq; ?>" class="input_text_10" value="<?php echo $pat_due; ?>" onChange="checkChkBox(<?php echo $seq; ?>,'pat_due_old_');">
		</td>
        <?php if($show_resp_party>0){ ?>
        	<td></td>
        <?php } ?>
        <td>
        	<div>
                <select name="facility_id_<?php echo $seq; ?>" id="facility_id_<?php echo $seq; ?>" class="form-control minimal" style="width:113px;">
                    <option value="">Pay Location</option>
                    <?php
                        foreach($fac_data_arr as $fac_key=>$fac_val){
                            $FacilityDetails=$fac_data_arr[$fac_key];				
                            $id = $FacilityDetails['id'];
							$sel="";
							if($default_pay_location>0){
								if($default_pay_location==$id){
									$sel="selected";
								}
							}else if($_SESSION['login_facility']==$id){
								$sel="selected";
							}
                            print '<option '.$sel.' value="'.$id.'">'.$FacilityDetails['name'].'</option>';
                        }
                    ?>
                </select>
          </div>
        </td>
		<td colspan="10">
			<input type="text" name="pmt_notes_<?php echo $seq; ?>" id="pmt_notes_<?php echo $seq; ?>" class="form-control" value="<?php echo $pmt_notes; ?>" style="width:100% !important;" placeholder="Comment" onChange="checkChkBox(<?php echo $seq; ?>,'pmt_notes_');">
		</td>
	</tr>

	<?php
			$arr_all=array();
			$arr_date=array();
			$arr_all_deduct="";
			if(count($payment_deductible_data[$charge_list_detail_id])>0){
				foreach($payment_deductible_data[$charge_list_detail_id] as $ded_key=>$ded_val){
					$deductibles=(object)$payment_deductible_data[$charge_list_detail_id][$ded_key];
					$deductible_id = $deductibles->deductible_id;
					$chargeDetailId = $deductibles->charge_list_detail_id;							
					$deduct_amount = $deductibles->deduct_amount;
					$deductible_by = $deductibles->deductible_by;
					$deduct_ins_id = $deductibles->deduct_ins_id;
					$deduct_batch_id = $batch_track_no[$deductibles->batch_id];
					$nameInsCo="";
					if($deduct_ins_id>0){
						$nameInsCo=$ins_comp_data[$deduct_ins_id]['in_house_code'];	
					}
					$deduct_operator_id = $deductibles->deduct_operator_id;
					
					$operatorNameDeduct = $usr_alias_name[$deduct_operator_id];
					$DeloperatorNameDeduct="";
					if($deductibles->delete_operator_id>0){
						$DeloperatorNameDeduct = '- '.$usr_alias_name[$deductibles->delete_operator_id];
					}
					$deduct_date = $deductibles->deduct_date;
					$delete_deduct = $deductibles->delete_deduct;
					$delete_deduct_date = $deductibles->delete_deduct_date;
					$deduct_entered_date = mmddyy_date($deductibles->entered_date);
					$show_cas_code=show_cas_code_fun($deductibles->cas_type,$deductibles->cas_code);
					$deleteRows = '';
					$show_del_style= '';
					if($delete_deduct==1){
						$show_del_style='class="hide deleted"';
						//$deleteRows = 'id="deleted_rows_id[]"';
					}else{
						$show_del_style="";
					}
					$arr_all_deduct ='
					
					<tr '.$show_del_style.' '.$deleteRows.'>
						<td colspan="2">';
							if($delete_deduct!=1 && $_REQUEST['batch_pat_id']<=0){
								$arr_all_deduct .='	<a href="javascript:void(0);" onClick="javascript:edit_enc_trans(\''.$deductible_id.'\',\''.$encounter_id.'\',\'Deductible\');">&nbsp;<img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a>
													<a href="javascript:void(0);" onClick="javascript:del_enc_trans(\''.$deductible_id.'\',\''.$encounter_id.'\',\'deductible\',\''.$charge_list_detail_id.'\');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>';
							}else{
								$arr_all_deduct .='&nbsp;'; 
							}
						$arr_all_deduct .='	
						</td>
						<td colspan="3" style="font-weight:bold;">';
							if($delete_deduct_date=="0000-00-00"){$delete_deduct_date="";}
							if($delete_deduct==1){
								list($year, $month, $day)=explode("-", $delete_deduct_date);
								$delete_deduct_date = date('m-d-y',mktime(0,0,0,$month,$day,$year));
								$delete_deduct_date1 = date('m-d-Y',mktime(0,0,0,$month,$day,$year));
								$arr_all_deduct .='Deductible Deleted : '; 
								if($deductible_by=='Insurance'){ 
									$arr_all_deduct .= $nameInsCo;
								} else{ 
									$arr_all_deduct .= $deductible_by; 
								}
								$deduct_date = $delete_deduct_date;
								$deduct_date1 = $delete_deduct_date1;
							}else{
								list($year, $month, $day)=explode("-", $deduct_date);
								$deduct_date = date('m-d-y',mktime(0,0,0,$month,$day,$year));
								$deduct_date1 = date('m-d-Y',mktime(0,0,0,$month,$day,$year));
								$arr_all_deduct .='Deductible : '; 
								if($deductible_by=='Insurance'){ 
									$arr_all_deduct .= $nameInsCo;
									$paidByCode=$nameInsCo;
								} else{ 
									$arr_all_deduct .= $deductible_by; 
									$paidByCode=$deductible_by;
								}
								if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){	
									$show_moaQualifier="MA18";
									if(strstr($moaQualifier, 'MA07')){
										$show_moaQualifier="MA07";
									}
									$arr_all_deduct .='&nbsp;&nbsp;';
									if($primaryInsCoName == $paidByCode){														
										if($deletePayment!=1){
											$arr_all_deduct .="(".$show_moaQualifier." - Forwarded to ".$secondaryInsCoName.")"; 
										}
									}
								}
							}
						 $paidByCode="";	
						 $deb_sty="";	
						if($delete_deduct==1){ $deb_sty="text-decoration:line-through;color:#FF0000"; } 
						$arr_all_deduct .=' '.$delete_deduct_date.' '.$usr_alias_name[$deductibles->delete_operator_id].'</td>
						<td style="'.$deb_sty.'" id="deductTd'.$deductible_id.'" class="text-right">'."$".number_format($deduct_amount, 2).'</td>
						<td colspan="'.($due_col).'">'.$show_cas_code.'</td>
						<td colspan="4">&nbsp;</td>
						<td class="text-nowrap" style="'.$deb_sty.'">'.$deduct_date.'</td>
						<td class="text-nowrap" style="'.$deb_sty.'">'.$deduct_entered_date.'</td>
						<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
						<td style="'.$deb_sty.'">'.$deduct_batch_id.'</td>
						<td>
							<span style="'.$deb_sty.'">'.$operatorNameDeduct.'</span>
							<span>'.$DeloperatorNameDeduct.'</span>
						</td>
					</tr>';
					$arr_all[$deduct_date1][]=$arr_all_deduct;
					$arr_date[]=$deduct_date1;
				}
			}
			$arr_all_writeoff ="";
			foreach($payment_wrt_data[$encounter_id][$charge_list_detail_id] as $w_key=>$w_val){
				$getWriteOffRows=$payment_wrt_data[$encounter_id][$charge_list_detail_id][$w_key];
				if(count($getWriteOffRows)>0){
					$arr_all_writeoff="";
					$write_off_id = $getWriteOffRows['write_off_id'];
					$write_off_by_id = $getWriteOffRows['write_off_by_id'];	
		
					$yy=$mm=$dd=$write_del_date="";
					list($yy,$mm,$dd)=explode("-",$getWriteOffRows['write_off_del_time']);
					if(checkdate($mm,$dd,$yy)){$write_del_date=$mm."-".$dd."-".$yy;}
					$write_off_by=$ins_comp_data[$write_off_by_id]['in_house_code'];				
					if($write_off_by==''){ $write_off_by = $ins_comp_data[$write_off_by_id]['name']; $paidByCode=$write_off_by;}else{$paidByCode=$write_off_by;}
		
					$paymentStatusMode = $getWriteOffRows['paymentStatus'];
					$paymentwrite_off_code_id = $getWriteOffRows['write_off_code_id'];
					$paymentwrite_adj_code_id  = $getWriteOffRows['adj_code_id'];
					$writeoff_entered_date = mmddyy_date($getWriteOffRows['entered_date']);
					$writeoff_batch_id = $batch_track_no[$getWriteOffRows['batch_id']];
					$paymentwrite_off_code="";
					if($paymentStatusMode=="Discount"){
						$paymentadj_code=$discount_code_data[$paymentwrite_off_code_id]['d_code'];
						$edit_pay_type=$paymentStatusMode;
					}else{	
						$paymentwrite_off_code=$wrt_name_arr[$paymentwrite_off_code_id];
						$paymentadj_code=$adj_code_data[$paymentwrite_adj_code_id]['a_code'];
						$edit_pay_type='Write Off';
					}
					$none_edit=0;
					if($getWriteOffRows['write_off_amount']>0){
						$write_off_amount = $getWriteOffRows['write_off_amount'];
					}else{
						$write_off_amount = $getWriteOffRows['era_amt'];
						if($getWriteOffRows['era_amt']>0){
							$none_edit=1;
						}
					}
					
					$total_write_off_amount = $total_write_off_amount + $write_off_amount;
					
					$write_off_operator_id = $getWriteOffRows['write_off_operator_id'];
					$write_off_operator = $usr_alias_name[$write_off_operator_id];	
		
					$write_off_date = $getWriteOffRows['write_off_date'];
					list($yearWO, $monthWO, $dayWO)=explode("-", $write_off_date);
					$write_off_date = date('m-d-y',mktime(0,0,0,$monthWO,$dayWO,$yearWO));
					$write_off_date1 = date('m-d-Y',mktime(0,0,0,$monthWO,$dayWO,$yearWO));
					
					$delStatus = $getWriteOffRows['delStatus'];
					if($delStatus!=0){
						$write_off_del_date = $getWriteOffRows['write_off_del_date'];
						list($yearDelWO, $monthDelWO, $dayDelWO)=explode("-", $write_off_del_date);
						$write_off_del_date = date('m-d-y',mktime(0,0,0,$monthDelWO,$dayDelWO,$yearDelWO));
					}
					$del_write_off_operator="";
					if($getWriteOffRows['del_operator_id']>0){
						$del_write_off_operator = '- '.$usr_alias_name[$getWriteOffRows['del_operator_id']];
					}	
				$deleteRows = '';
				$show_del_style= '';
				if($delStatus==1){
					$show_del_style='class="hide deleted"';
				}else{
					$show_del_style="";
				}
				
				$show_cas_code=show_cas_code_fun($getWriteOffRows['CAS_type'],$getWriteOffRows['CAS_code']);
				
				$arr_all_writeoff .='	
				<tr '.$deleteRows.' '.$show_del_style.'>
					<td colspan="2">';
					
					if($delStatus==0 && $_REQUEST['batch_pat_id']<=0){
						$arr_all_writeoff .='<a href="javascript:void(0);" onClick="javascript:edit_enc_trans(\''.$write_off_id.'\',\''.$encounter_id.'\',\''.$edit_pay_type.'\',\''.$none_edit.'\');">&nbsp;<img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a>
											 <a href="javascript:void(0);" onClick="javascript:del_enc_trans(\''.$write_off_id.'\',\''.$encounter_id.'\',\''.$edit_pay_type.'\',\''.$charge_list_detail_id.'\');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>';
					}else{
						$arr_all_writeoff .= '&nbsp;';
					}	
					$arr_all_writeoff .= '
					</td>
					<td colspan="4" style="font-weight:bold;">';
						if(($write_off_del_date!='00-00-0000') && ($write_off_del_date!='') && ($delStatus=='1')){
							if($paymentStatusMode) {
								$arr_all_writeoff .= $paymentStatusMode." Deleted"; 
							 }else{
								$arr_all_writeoff .= 'Write Off';
							 } 
							 if($write_off_by){ 
								$arr_all_writeoff .= " : ".$write_off_by;
							 }else{
								 $arr_all_writeoff .= " : Patient"; 
							  }  
							  $arr_all_writeoff .=' '.$write_off_del_date.' '.$usr_alias_name[$getWriteOffRows['del_operator_id']].'</td>';
							
						}else{
							 if($paymentStatusMode){ 
								$arr_all_writeoff .= $paymentStatusMode; 
							}else{ 
								$arr_all_writeoff .= 'Write Off';
							} 
							if($write_off_by){ 
								$arr_all_writeoff .= " : ".$write_off_by; 
							}else{ 
								$arr_all_writeoff .= " : Patient"; 
							 }
							 if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
								$show_moaQualifier="MA18";
								if(strstr($moaQualifier, 'MA07')){
									$show_moaQualifier="MA07";
								}
								$arr_all_writeoff .='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								if($primaryInsCoName == $paidByCode){														
									if($deletePayment!=1){
										$arr_all_writeoff .='('.$show_moaQualifier.' - Forwarded to '.$secondaryInsCoName.')';
									}
								}
							}
						}
					$arr_all_writeoff .='</td>';
					$wri_sty1="";
					$paidByCode="";
					if($delStatus=='1') $wri_sty1="text-decoration:line-through;color:#FF0000;";
					$arr_all_writeoff .='

					<td style="'.$wri_sty1.'" colspan="'.($due_col).'">'.$show_cas_code.'</td>
					<td style="'.$wri_sty1.'" colspan="4"></td>
					<td class="text-nowrap" style="'.$wri_sty1.'">'.$write_off_date.'</td>
					<td class="text-nowrap" style="'.$wri_sty1.'">'.$writeoff_entered_date.'</td>
					<td style="'.$wri_sty1.'" class="text-right">'."$".number_format($write_off_amount, 2).'</td>
					<td style="'.$wri_sty1.'">&nbsp;</td>
					<td style="'.$wri_sty1.'">';
					if($paymentStatusMode=="Discount"){
						$arr_all_writeoff .=$paymentadj_code;
					}else{
						$arr_all_writeoff .=$paymentwrite_off_code;
					}
					$arr_all_writeoff .='</td>
					<td style="'.$wri_sty1.'">'.$writeoff_batch_id.'</td>
					<td>
						<span style="'.$wri_sty1.'">'.$write_off_operator.'</span>
						<span>'.$del_write_off_operator.'</span>
					</td>
				</tr>';
				$arr_all[$write_off_date1][]=$arr_all_writeoff;
				$arr_date[]=$write_off_date1;
				}
			}
	
			$arr_all_acc_pay ="";
			$ret_chk_arr=array();
			if(count($account_payments_data[$charge_list_detail_id])>0){
				foreach($account_payments_data[$charge_list_detail_id] as $a_key=>$a_val){
					$getAccPayRows=$account_payments_data[$charge_list_detail_id][$a_key];
					$arr_all_acc_pay="";
					$c_cc_number="";
					$id = $getAccPayRows['id'];
					$ins_id = $getAccPayRows['ins_id'];
					$payment_amount = $getAccPayRows['payment_amount'];
					$payment_by = $getAccPayRows['payment_by'];
					$payment_method = $getAccPayRows['payment_method'];
					$check_number = $getAccPayRows['check_number'];
					$cc_type = $getAccPayRows['cc_type'];
					$cc_number = $getAccPayRows['cc_number'];
					$cc_exp_date = $getAccPayRows['cc_exp_date'];
					$acc_payments_batch_id = $batch_track_no[$getAccPayRows['batch_id']];
					list($del_date,$del_time)=explode(" ",trim($getAccPayRows['del_date_time']));
					$yy=$mm=$yy="";
					list($yy,$mm,$yy)=explode("-",trim($del_date));
					if($payment_method=='Check' || stripos($payment_method,'Check')>0){
						$c_cc_number=$check_number;
						$cc_exp_date="";
					}
					if($payment_method=='Money Order'){
						$c_cc_number=$check_number;
						$cc_exp_date="";
						$payment_method="MO";
					}
					if($payment_method=='EFT'){
						$c_cc_number=$check_number;
						$cc_exp_date="";
					}
					if($payment_method=='VEEP'){
						$c_cc_number=$check_number;
						$cc_exp_date="";
					}
					if($payment_method=='Credit Card'){
						$c_cc_number=$cc_number;
						$payment_method = 'CC';
					}
					list($year, $month, $day)=explode("-", $getAccPayRows['payment_date']);
					$payment_date = date('m-d-y',mktime(0,0,0,$month,$day,$year));
					$payment_date_for_arr = date('m-d-Y',mktime(0,0,0,$month,$day,$year));
					
					$operator_id = $getAccPayRows['operator_id'];
					$payment_code_id = $getAccPayRows['payment_code_id'];
					$payment_type = $getAccPayRows['payment_type'];
					$del_status = $getAccPayRows['del_status'];
					
					if($payment_type=='Returned Check' && $del_status==0){
						$ret_chk_arr[]=$check_number;
					}
					$del_operator_name="";
					if($getAccPayRows['del_operator_id']>0){
						$del_operator_name = '- '.$usr_alias_name[$getAccPayRows['del_operator_id']];
					}
					
					$acc_pay_ins = $ins_comp_data[$ins_id]['in_house_code'];								
					if($acc_pay_ins==''){ $acc_pay_ins = $ins_comp_data[$ins_id]['name']; }
					
					$payment_code=$adj_code_data[$payment_code_id]['a_code'];
					
					$operator_name = $usr_alias_name[$operator_id];	
		
					$deleteRows = '';
					$show_del_style= '';
					if($del_status==1){
						$show_del_style='class="hide deleted"';
					}else{
						$show_del_style="";
						$chk_ins_type="";
						if($ins_id==0){
							$chk_ins_type=0;
						}else if($primaryInsuranceCoId==$ins_id){
							$chk_ins_type=1;
						}else if($secondaryInsuranceCoId==$ins_id){
							$chk_ins_type=2;
						}else if($tertiaryInsuranceCoId==$ins_id){
							$chk_ins_type=3;
						}
						if($payment_type=="Adjustment" || $payment_type=="Returned Check"){
							$show_total_paid_arr[$chk_ins_type][]=-$payment_amount;
						}
						if($payment_type=="Over Adjustment"){
							$show_total_paid_arr[$chk_ins_type][]=$payment_amount;
						}
					}
					$copay_plus_type="";
					if($getAccPayRows['copay_chld_id']>0){
						$copay_plus_type='(Copay)';
					}
					$cols_case=0;
					$del_trans_mode="ovr_adj";
					if($payment_type=="Adjustment" || $payment_type=="Over Adjustment"){
						$cols_case=2;
					}
					if($payment_type=="Returned Check"){
						$del_trans_mode="returned_check";
					}
					if($payment_type=="Co-Insurance"){
						if(trim($getAccPayRows['cas_type'])=="PR" && trim($getAccPayRows['cas_code'])=="3"){
							$payment_type = "Co-Payment";
						}
						$del_trans_mode="co_insurance";
					}
					if($payment_type=="Co-Payment"){
						$del_trans_mode="co_payment";
					}
					$payment_entered_date = mmddyy_date($getAccPayRows['entered_date']);
					$payment_type=$payment_type.$copay_plus_type;
					
					$show_cas_code=show_cas_code_fun($getAccPayRows['cas_type'],$getAccPayRows['cas_code']);
					
				$arr_all_acc_pay .='	
				<tr '.$deleteRows.' '.$show_del_style.'>
					<td colspan="2">';
					if($del_status==0 && $_REQUEST['batch_pat_id']<=0){
						$arr_all_acc_pay .='<a href="javascript:void(0);" onClick="javascript:edit_enc_trans(\''.$id.'\',\''.$encounter_id.'\',\''.$payment_type.'\');">&nbsp;<img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a>
											<a href="javascript:void(0);" onClick="javascript:del_enc_trans(\''.$id.'\',\''.$encounter_id.'\',\''.$del_trans_mode.'\',\''.$chargeListId.'\');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>';
					}else{
						$arr_all_acc_pay .= '&nbsp;';
					}	
					$arr_all_acc_pay .= '
					</td>
					<td colspan="4" style="font-weight:bold;">';
							 
								if(($payment_date!='00-00-0000') && ($payment_date!='') && ($del_status=='1')){
									if($payment_type) {
										$arr_all_acc_pay .= $payment_type." Deleted"; 
									}
									if($acc_pay_ins){ 
										$arr_all_acc_pay .= " : ".$acc_pay_ins;
									}else{
										$arr_all_acc_pay .= " : Patient"; 
									}  
									$arr_all_acc_pay .="&nbsp;".mmddyy_date($del_date)."&nbsp;".$usr_alias_name[$getAccPayRows['del_operator_id']].'</td>';
		
								}else{
									$acc_pay_sty="";
									if($del_status=='1') $acc_pay_sty="text-decoration:line-through;color:#FF0000;"; 
									 if($payment_type){ 
										$arr_all_acc_pay .= $payment_type; 
									}else{ 
										$arr_all_acc_pay .= 'Write Off';
									} 
									if($acc_pay_ins){ 
										$arr_all_acc_pay .= " : ".$acc_pay_ins; 
									}else{ 
										$arr_all_acc_pay .= " : Patient"; 
									}
								}
					$arr_all_acc_pay .='</td>';
					$acc_pay_sty1=$co_ins_amt=$co_ins_nowrap="";
					if($payment_type=="Co-Insurance" || $payment_type=="Co-Payment"){
						$co_ins_amt=numberFormat($payment_amount,2);
						$co_ins_nowrap=" nowrap";
					}
					if($del_status=='1') $acc_pay_sty1="text-decoration:line-through;color:#FF0000;";
					$arr_all_acc_pay .='
					<td style="'.$acc_pay_sty1.'" colspan="'.($due_col).'" '.$co_ins_nowrap.'>'.$show_cas_code.' '.$co_ins_amt.'</td>
					<td style="'.$acc_pay_sty1.'">'.$payment_method.'</td>
					<td style="'.$acc_pay_sty1.'">'.$c_cc_number.'</td>
					<td style="'.$acc_pay_sty1.'" class="text-right">';
					if($payment_type=="Returned Check" || $payment_type=="Returned Check(Copay)"){
						$arr_all_acc_pay .="$".number_format($payment_amount, 2);
					}
					$arr_all_acc_pay .='</td><td style="'.$acc_pay_sty1.'" >&nbsp;</td>
					<td class="text-nowrap" style="'.$acc_pay_sty1.'">'.$payment_date.'</td>
					<td class="text-nowrap" style="'.$acc_pay_sty1.'">'.$payment_entered_date.'</td>
					<td style="'.$acc_pay_sty1.'" class="text-right">';
					if($payment_type=="Adjustment" || $payment_type=="Over Adjustment"){	
						$arr_all_acc_pay .="$".number_format($payment_amount, 2);
					}
					$arr_all_acc_pay .='</td><td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td style="'.$acc_pay_sty1.'">'.$payment_code.'</td>
					<td style="'.$acc_pay_sty1.'">'.$acc_payments_batch_id.'</td>
					<td>
						<span style="'.$acc_pay_sty1.'">'.$operator_name.'</span>
						<span>'.$del_operator_name.'</span>
					</td>
				</tr>';
				$arr_all[$payment_date_for_arr][]=$arr_all_acc_pay;
				$arr_date[]=$payment_date_for_arr;
				}
			}
	
			$arr_all_acc_pay ="";
			$ret_chk_arr=array();
			if(count($tx_payments_data[$charge_list_detail_id])>0){
				foreach($tx_payments_data[$charge_list_detail_id] as $tx_key=>$tx_val){	
					$getAccPayRows=$tx_payments_data[$charge_list_detail_id][$tx_key];
					$arr_all_tx_pay="";
					$id = $getAccPayRows['id'];
					$pri_due_old = $getAccPayRows['pri_due'];
					$sec_due_old  = $getAccPayRows['sec_due'];
					$tri_due_old = $getAccPayRows['tri_due'];
					$pat_due_old = $getAccPayRows['pat_due'];
		
					list($year, $month, $day)=explode("-", $getAccPayRows['payment_date']);
					$payment_date = date('m-d-y',mktime(0,0,0,$month,$day,$year));
					$payment_date_for_arr = date('m-d-Y',mktime(0,0,0,$month,$day,$year));
					
					$operator_id = $getAccPayRows['operator_id'];
					$del_status = $getAccPayRows['del_status'];
					$del_date=$getAccPayRows['del_date_time'];
					$operator_name = $usr_alias_name[$operator_id];	
				$tx_entered_date = mmddyy_date($getAccPayRows['entered_date']);
				$del_operator=$usr_alias_name[$getAccPayRows['del_operator_id']];
				$deleteRows = '';
				$show_del_style= '';
				if($del_status==1){
					$show_del_style='class="hide deleted"';
				}else{
					$show_del_style="";
				}
				
				$arr_all_tx_pay .='	
				<tr '.$deleteRows.' '.$show_del_style.'>
					<td colspan="2">';
					if($del_status==0 && $_REQUEST['batch_pat_id']<=0){
						$arr_all_tx_pay .='<a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
											<a href="javascript:void(0);" onClick="javascript:del_enc_trans(\''.$id.'\',\''.$encounter_id.'\',\'tx_balance\');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>';
					}else{
						$arr_all_tx_pay .= '&nbsp;';
					}	
					$arr_all_tx_pay .= '
					</td>
					<td colspan="4" style="font-weight:bold;">';
						if(($payment_date!='00-00-0000') && ($payment_date!='') && ($del_status=='1')){
							$arr_all_tx_pay .='Tx Balance Deleted ';
							$arr_all_tx_pay .=mmddyy_date($del_date).' '.$del_operator.'</td>';
						}else{
							$acc_pay_sty="";
								$arr_all_tx_pay .='Tx Balance';
						}
					$arr_all_tx_pay .= '</td>';
					$acc_pay_sty1="";
					if($del_status=='1') $acc_pay_sty1="text-decoration:line-through;color:#FF0000;";
					if($primaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right">$'.$pri_due_old.'</td>';
					if($secondaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right">$'.$sec_due_old.'</td>';
					if($tertiaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right">$'.$tri_due_old.'</td>';
					$arr_all_tx_pay .='
					<td style="'.$acc_pay_sty1.'" class="text-right">$'.$pat_due_old.'</td>';
					if($show_resp_party>0){
						$arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'">&nbsp;</td>';
					}
					$arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td class="text-nowrap" style="'.$acc_pay_sty1.'">'.$payment_date.'</td>
					<td class="text-nowrap" style="'.$acc_pay_sty1.'">'.$tx_entered_date.'</td>
					<td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td style="'.$acc_pay_sty1.'">&nbsp;</td>
					<td style="'.$acc_pay_sty1.'">'.$operator_name.'</td>

				</tr>';
				$arr_all[$payment_date_for_arr][]=$arr_all_tx_pay;
				$arr_date[]=$payment_date_for_arr;
				}
			}
			
			if(count($tx_charges_data[$charge_list_detail_id])>0){
				foreach($tx_charges_data[$charge_list_detail_id] as $tx_key=>$tx_val){	
					$getAccPayRows=$tx_charges_data[$charge_list_detail_id][$tx_key];
					$arr_all_tx_pay="";
					$id = $getAccPayRows['id'];
					$operator_id = $getAccPayRows['operator_id'];
					$del_status = $getAccPayRows['del_status'];
					$del_date=$getAccPayRows['del_date_time'];
					$operator_name = $usr_alias_name[$operator_id];	
					$tx_entered_date = mmddyy_date($getAccPayRows['entered_date']);
					$del_operator=$usr_alias_name[$getAccPayRows['del_operator_id']];
					$new_charges=$getAccPayRows['new_charges'];
					$old_charges=$getAccPayRows['old_charges'];
					$deleteRows = '';
					$show_del_style= '';
					if($del_status==1){
						$show_del_style='class="hide deleted"';
					}else{
						$show_del_style="";
					}
					
					$tx_charges="Negative Charges ";
					if($new_charges>$old_charges){
						$tx_charges="Addition Charges ";
					}
				
					$tx_charges_val=': From $'.$old_charges.' To $'.$new_charges.' ';
					
					$arr_all_tx_pay .='	
					<tr '.$deleteRows.' '.$show_del_style.'>
						<td colspan="2">';
						if($del_status==0 && $_REQUEST['batch_pat_id']<=0){
							$arr_all_tx_pay .='<a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
												<a href="javascript:void(0);" onClick="javascript:del_enc_trans(\''.$id.'\',\''.$encounter_id.'\',\'tx_charge\');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>';
						}else{
							$arr_all_tx_pay .= '&nbsp;';
						}	
						$arr_all_tx_pay .= '
						</td>
						<td colspan="4" style="font-weight:bold;">';
							if($del_status=='1'){
								$arr_all_tx_pay .=$tx_charges.'Deleted '.$tx_charges_val;
								$arr_all_tx_pay .=mmddyy_date($del_date).' '.$del_operator.'</td>';
							}else{
								$acc_pay_sty="";
									$arr_all_tx_pay .=$tx_charges.$tx_charges_val;
							}
						$arr_all_tx_pay .= '</td>';
						$acc_pay_sty1="";
						if($del_status=='1') $acc_pay_sty1="text-decoration:line-through;color:#FF0000;";
						if($primaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right"></td>';
						if($secondaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right"></td>';
						if($tertiaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right"></td>';
						$arr_all_tx_pay .='
						<td style="'.$acc_pay_sty1.'" class="text-right"></td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td class="text-nowrap" style="'.$acc_pay_sty1.'"></td>
						<td class="text-nowrap" style="'.$acc_pay_sty1.'">'.$tx_entered_date.'</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">'.$operator_name.'</td>
					</tr>';
					$arr_all[$payment_date_for_arr][]=$arr_all_tx_pay;
					$arr_date[]=$payment_date_for_arr;
				}
			}
			
			if(count($denied_payment_data[$charge_list_detail_id])>0){
				foreach($denied_payment_data[$charge_list_detail_id] as $den_key=>$den_val){	
					$getDeniedDetailsRow=$denied_payment_data[$charge_list_detail_id][$den_key];		
					$deniedById="";
					$arr_all_denied="";
					$deniedId = $getDeniedDetailsRow['deniedId'];									
					$deniedBy = $getDeniedDetailsRow['deniedBy'];									
					if($deniedBy=='Insurance'){	
						$deniedBy = "Ins.";
					}
					$deniedById = $getDeniedDetailsRow['deniedById'];
					$denied_batch_id = $batch_track_no[$getDeniedDetailsRow['batch_id']];
					
					$deniedByName = $ins_comp_data[$deniedById]['in_house_code'];
					if($deniedByName==''){ $deniedByName = $ins_comp_data[$deniedById]['name']; $paidByCode=$deniedByName;}else{$paidByCode=$deniedByName;}
						
					$deniedDate = $getDeniedDetailsRow['deniedDate'];
						list($yearDenied, $monthDenied, $dayDenied) = explode("-", $deniedDate);
						$deniedDate = date('m-d-y',mktime(0,0,0,$monthDenied,$dayDenied,$yearDenied));
						$deniedDate1 = date('m-d-Y',mktime(0,0,0,$monthDenied,$dayDenied,$yearDenied));
						
					$deniedAmount = number_format($getDeniedDetailsRow['deniedAmount'], 2);
					$denialOperatorId = $getDeniedDetailsRow['denialOperatorId'];
					$denialOperatorName = $usr_alias_name[$denialOperatorId];		
						
					$denialDelStatus = $getDeniedDetailsRow['denialDelStatus'];
					$denialDelDate = $getDeniedDetailsRow['denialDelDate'];
						list($yearDelDenied, $monthDelDenied, $dayDelDenied) = explode("-", $denialDelDate);
						$denialDelDate = date('m-d-y',mktime(0,0,0,$monthDelDenied,$dayDelDenied,$yearDelDenied));					
				$denied_entered_date = mmddyy_date($getDeniedDetailsRow['entered_date']);
				$deleted_date=mmddyy_date($getDeniedDetailsRow['denialDelDate']);
				$deleteRows = '';
				$show_del_style= '';
				if($denialDelStatus==1){
					$show_del_style='class="hide deleted"';
				}else{
					$show_del_style="";
				}
				$del_denialOperatorName="";
				if($getDeniedDetailsRow['del_operator_id']>0){
					$del_denialOperatorName = '- '.$usr_alias_name[$getDeniedDetailsRow['del_operator_id']];
				}
				
				$show_cas_code=show_cas_code_fun($getDeniedDetailsRow['CAS_type'],$getDeniedDetailsRow['CAS_code']);
				
				$arr_all_denied .='
				<tr '.$deleteRows.' '.$show_del_style.'>
					<td colspan="2" id="editDel'.$deniedId.'">';
						if($denialDelStatus!='1' && $_REQUEST['batch_pat_id']<=0){
							$arr_all_denied .='
							<a href="javascript:void(0);" onClick="javascript:edit_enc_trans(\''.$deniedId.'\',\''.$encounter_id.'\',\'Denied\');">
								<img src="../../library/images/edit.png" alt="Edit" style="border:none;">
							</a>
							
							<a href="javascript:void(0);" onClick="javascript:del_enc_trans(\''.$deniedId.'\',\''.$encounter_id.'\',\'denial\',\''.$charge_list_detail_id.'\');">
								<img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;">
							</a>';
							
						}else{
							$arr_all_denied .= '&nbsp;';
						}
					$arr_all_denied .='</td>';
					
					if(($denialDelStatus!=0) && ($denialDelDate!='') &&  ($denialDelDate!='00-00-0000')){
					$arr_all_denied .='
						<td colspan="4" style="font-weight:bold;">Denial Deleted : ';
						 if($deniedByName){
							$arr_all_denied .= $deniedByName; 
						  }else{ 
							$arr_all_denied .= $deniedBy; 
						  }
						  if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
							    $show_moaQualifier="MA18";
								if(strstr($moaQualifier, 'MA07')){
									$show_moaQualifier="MA07";
								}											
								$arr_all_denied .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								if($primaryInsCoName == $paidByCode){														
									if($deletePayment!=1){
										$arr_all_denied .='('.$show_moaQualifier.' - Forwarded to '.$secondaryInsCoName.')';
									}
								}
							}
																
						$arr_all_denied .=' '.$deleted_date.' '.$usr_alias_name[$getDeniedDetailsRow['del_operator_id']].'</td>';
					
					}else{
						$den_sty="";
						if($denialDelStatus==1){ $den_sty="text-decoration:line-through;color:#FF0000"; } 
						$arr_all_denied .='
						<td style="font-weight:bold; '.$den_sty.'" colspan="4"> Denial : ';
							if($deniedByName){
								$arr_all_denied .= $deniedByName; 
							}else{
								$arr_all_denied .= $deniedBy;
								} 
						
							if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
							    $show_moaQualifier="MA18";
								if(strstr($moaQualifier, 'MA07')){
									$show_moaQualifier="MA07";
								}														
								$arr_all_denied .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								if($primaryInsCoName == $paidByCode){														
									if($deletePayment!=1 && $secondaryInsCoName!=""){
										$arr_all_denied .= '('.$show_moaQualifier.' - Forwarded to '.$secondaryInsCoName.')';
									}
								}
							}
																	
						$arr_all_denied .='</td>';
					
					} 
					$paidByCode="";
					$den_sty1="";
					if($denialDelStatus==1){ $den_sty1="text-decoration:line-through;color:#FF0000"; }
					 
					$pri_den_amt=$sec_den_amt=$ter_den_amt=$pat_den_amt=$resp_den_amt="";
					if($primaryInsProviderId>0){
						if($primaryInsProviderId==$deniedById){
							$pri_den_amt=$deniedAmount;
						}
						$arr_all_denied .='<td style="'.$den_sty1.'" id="tdDenialAmt'.$deniedId.'" class="text-right">'.numberFormat($pri_den_amt,2,'no').'</td>';
					}
					if($secondaryInsProviderId>0){
						if($secondaryInsProviderId==$deniedById && $pri_den_amt<=0){
							$sec_den_amt=$deniedAmount;
						}
						$arr_all_denied .='<td style="'.$den_sty1.'" id="tdDenialAmt'.$deniedId.'" class="text-right">'.numberFormat($sec_den_amt,2,'no').'</td>';
					}
					if($tertiaryInsProviderId>0){
						if($tertiaryInsProviderId==$deniedById && $pri_den_amt<=0 && $sec_den_amt<=0){
							$ter_den_amt=$deniedAmount;
						}
						$arr_all_denied .='<td style="'.$den_sty1.'" id="tdDenialAmt'.$deniedId.'" class="text-right">'.numberFormat($ter_den_amt,2,'no').'</td>';
					}
					if($deniedById==0){
						if($deniedBy=="Res. Party"){
							$resp_den_amt=$deniedAmount;
						}else{
							$pat_den_amt=$deniedAmount;
						}
					}
					$arr_all_denied .='<td style="'.$den_sty1.'" id="tdDenialAmt'.$deniedId.'" class="text-right">'.numberFormat($pat_den_amt,2,'no').'</td>';
					if($show_resp_party>0){
						$arr_all_denied .='<td style="'.$den_sty1.'" id="tdDenialAmt'.$deniedId.'" class="text-right">'.numberFormat($resp_den_amt,2,'no').'</td>';
					}
					$arr_all_denied .='
					<td style="'.$den_sty1.'" colspan="4">'.$show_cas_code.'</td>
					<td class="text-nowrap" style="'.$den_sty1.'">'.$deniedDate.'</td>
					<td class="text-nowrap" style="'.$den_sty1.'">'.$denied_entered_date.'</td>
					<td style="'.$den_sty1.'">&nbsp;</td>
					<td style="'.$den_sty1.'">&nbsp;</td>
					<td style="'.$den_sty1.'">&nbsp;</td>
					<td style="'.$den_sty1.'">'.$denied_batch_id.'</td>
					<td> 
						<span style="'.$den_sty1.'">'.$denialOperatorName.'</span>
						<span>'.$del_denialOperatorName.'</span>
					</td>
				</tr>';
				$arr_all[$deniedDate1][]=$arr_all_denied;
				$arr_date[]=$deniedDate1;
				}
			}
			$arr_all_crd="";
			if(count($credit_record_data[$charge_list_detail_id])>0){
				foreach($credit_record_data[$charge_list_detail_id] as $c_key=>$c_val){
					$getCreditsRows=$credit_record_data[$charge_list_detail_id][$c_key];
					$amountApplied = number_format($getCreditsRows['amountApplied'], 2);
					$dateApplied = $getCreditsRows['dateApplied'];
						list($crAppYear, $crAppMonth, $crAppDay) = explode("-", $dateApplied);
						//$dateApplied = $crAppMonth."-".$crAppDay."-".$crAppYear;
						$dateApplied = date('m-d-y',mktime(0,0,0,$crAppMonth,$crAppDay,$crAppYear));
						$dateApplied1 = date('m-d-Y',mktime(0,0,0,$crAppMonth,$crAppDay,$crAppYear));
					$operatorApplied = $getCreditsRows['operatorApplied'];
					$type_credit=$getCreditsRows['type'];
					$ins_case=$getCreditsRows['ins_case'];
					$payment_mode_credit=$getCreditsRows['payment_mode'];
					if($payment_mode_credit=='Check' || $payment_mode_credit=='EFT' || $payment_mode_credit=='Money Order' || $payment_mode_credit=='VEEP' || stripos($payment_mode_credit,'Check')>0){
						$checkCcNumber_credit=$getCreditsRows['checkCcNumber'];
					}
					if($payment_mode_credit=='Credit Card'){
						$cc_type=strtoupper(substr($getCreditsRows['creditCardCo'], 0, 2));
						$checkCcNumber_credit=$cc_type.'-'.$getCreditsRows['creditCardNo'];
						$expirationDateCc=$getCreditsRows['expirationDateCc'];
					}
					$crAppId=$getCreditsRows['crAppId'];
					$credit_note=$getCreditsRows['credit_note'];
					$delete_credit=$getCreditsRows['delete_credit'];
					$crAppliedToEncId=$getCreditsRows['crAppliedToEncId'];
					$delete_credit=$getCreditsRows['delete_credit'];
					$modify=$getCreditsRows['modify'];
					$charge_list_detail_id_adjust=$getCreditsRows['charge_list_detail_id_adjust'];
					$crAppliedTo=$getCreditsRows['crAppliedTo'];
					$charge_list_detail_id_chk=$getCreditsRows['charge_list_detail_id'];
					$patient_id_adjust=$getCreditsRows['patient_id_adjust'];
					$patient_id_chk=$getCreditsRows['patient_id'];
					$crAppliedToEncId_adjust=$getCreditsRows['crAppliedToEncId_adjust'];
					$crApplied_entered_date = mmddyy_date($getCreditsRows['entered_date']);
					$crApplied_batch_id = $batch_track_no[$getCreditsRows['batch_id']];
					$insCompany=$getCreditsRows['insCompany'];
					$del_crOperName=$del_date_time=$deleted="";;
					if($getCreditsRows['del_operator_id']>0){
						$del_crOperName = '- '.$usr_alias_name[$getCreditsRows['del_operator_id']];
						$del_date_time=mmddyy_date($getCreditsRows['del_date_time']);
						$deleted='Deleted';
					}
					
					if($type_credit=='Insurance'){
						$insCoCode = $ins_comp_data[$ins_case]['in_house_code'];
						if($insCoCode<>""){
							$credit_by=$insCoCode;
						}else{
							$credit_by = $getInsCoRow['name'];
						}
					}else{
						$credit_by=$type_credit;
					}
					if($credit_by==""){
						$credit_by="Patient";
					}
						//--------------------	GETTING OPERATOR NAME --------------------//
							$crOperName = $usr_alias_name[$operatorApplied];
						//--------------------	GETTING OPERATOR NAME --------------------//
					//echo $charge_list_detail_id_adjust.'=='.$charge_list_detail_id;
					$credit_note1=htmlentities($credit_note);
					if($crAppliedToEncId_adjust>0){
					
						$qry = imw_query("select * from patient_data where pid = '".$patient_id_adjust."'");
						$getpat_to = imw_fetch_object($qry);
						$fname_to = $getpat_to->fname;
						$lname_to = $getpat_to->lname;
						$mname_to = $getpat_to->mname;
						$patientName_to = ucwords(trim($lname_to.", ".$fname_to));
						
						$qry = imw_query("select * from patient_data where pid = '".$patient_id_chk."'");
						$getpat_frm = imw_fetch_object($qry);
						$fname_frm = $getpat_frm->fname;
						$lname_frm = $getpat_frm->lname;
						$mname_frm = $getpat_frm->mname;
						$patientName_frm = ucwords(trim($lname_frm.", ".$fname_frm));
						//echo $patient_id_adjust.'=='.$patient_id_chk;
						if($patient_id_adjust==$patient_id_chk){
							if($credit_note1<>""){
								$credit_note1=$credit_note1;
							}
							$note="Adjustment Credit $deleted  : $credit_by $credit_note1";
							$note_debit="Adjustment Debit $deleted : $credit_by $credit_note1";
						}else{
							$note="Adjustment Credit : $credit_by  from  $patientName_frm - $patient_id_chk  $credit_note1";
							$note_debit="Adjustment Debit : $credit_by  to $patientName_to - $patient_id_adjust  $credit_note1";
						}
					}else{
						$note="Adjustment Credit : $credit_by $credit_note1";
						$note_debit="Adjustment Debit : $credit_by $credit_note1";
					}
					$deleteRows = '';
					$show_del_style= '';
					if($delete_credit==1){
						$show_del_style='class="hide deleted"';
						//$deleteRows = 'id="deleted_rows_id[]"';
					}else{
						$show_del_style="";
						$chk_ins_type=0;
						if($primaryInsuranceCoId==$ins_case || $insCompany==1){
							$chk_ins_type=1;
						}else if($secondaryInsuranceCoId==$ins_case || $insCompany==2){
							$chk_ins_type=2;
						}else if($tertiaryInsuranceCoId==$ins_case || $insCompany==3){
							$chk_ins_type=3;
						}
						if($crAppliedToEncId==$encounter_id){
							$show_total_paid_arr[$chk_ins_type][]=-$getCreditsRows['amountApplied'];
						}
						if($crAppliedToEncId_adjust==$encounter_id){
							$show_total_paid_arr[$chk_ins_type][]=$getCreditsRows['amountApplied'];
						}
					}
					if($charge_list_detail_id_chk==$charge_list_detail_id  && $crAppliedTo=='payment'){
					
						$arr_all_crd .='
						<tr '.$deleteRows.' '.$show_del_style.'>
						  <td colspan="2">';
							if($delete_credit!=1 && $_REQUEST['batch_pat_id']<=0){
								$arr_all_crd .='<a href="javascript:void(0);" onclick="javascript:edit_enc_trans(\''.$crAppId.'\',\''.$crAppliedToEncId.'\',\'Refund\');" >&nbsp;<img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a>
												<a href="javascript:void(0);" onclick="javascript:del_enc_trans(\''.$crAppId.'\',\''.$crAppliedToEncId.'\',\'refund\');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>';
							}else{
								$arr_all_crd .='&nbsp;';
							}
							$crd_sty="";		
							if($delete_credit==1){ $crd_sty="text-decoration:line-through;color:#FF0000"; }
							$arr_all_crd .='</td>
							<td colspan="4" style="font-weight:bold;">
								Refund '.$deleted.' : '.$credit_by.'&nbsp;'.htmlentities($credit_note).' '.$del_date_time.' '.$usr_alias_name[$getCreditsRows['del_operator_id']].'
							</td>
							<td colspan="'.($due_col).'">&nbsp;</td>
							<td style="'.$crd_sty.'">'.$payment_mode_credit.'</td>
							<td style="'.$crd_sty.'">'.$checkCcNumber_credit.'</td>
							<td>&nbsp;</td><td>&nbsp;</td>
							<td class="text-nowrap" style="'.$crd_sty.'">'.$dateApplied.'</td>
							<td class="text-nowrap" style="'.$crd_sty.'">'.$crApplied_entered_date.'</td>
							<td>&nbsp;</td>
							<td  style="'.$crd_sty.'" class="text-right">'."$".$amountApplied.'</td>
							<td>&nbsp;</td>
							<td style="'.$crd_sty.'">'.$crApplied_batch_id.'</td>
							<td>
								<span style="'.$crd_sty.'">'.$crOperName.'</span>
								<span>'.$del_crOperName.'</span>
							</td>
						</tr>';
						
					}else if($charge_list_detail_id_adjust==$charge_list_detail_id && $crAppliedTo=='adjustment'){
						if($crAppliedToEncId==0){
							$crAppliedToEncId=$crAppliedToEncId_adjust;
						}
						$arr_all_crd .='<tr '.$deleteRows.' '.$show_del_style.'>
						  <td colspan="2">';
								if($delete_credit!=1 && $_REQUEST['batch_pat_id']<=0){
									$arr_all_crd .='<a href="javascript:void(0);" onclick="javascript:edit_enc_trans(\''.$crAppId.'\',\''.$crAppliedToEncId_adjust.'\',\'debit credit\');" >&nbsp;<img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a>
													<a href="javascript:void(0);" onclick="javascript:del_enc_trans(\''.$crAppId.'\',\''.$crAppliedToEncId.'\',\'credit_debit\',\''.$crAppliedToEncId_adjust.'\');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>';
								}else{
									$arr_all_crd .= '&nbsp;';
								}
							$crd_sty="";
							if($delete_credit==1){ $crd_sty="text-decoration:line-through;color:#FF0000";}
							$arr_all_crd .='</td>
							<td colspan="4" style="font-weight:bold;">
								'.$note.' '.$del_date_time.' '.$usr_alias_name[$getCreditsRows['del_operator_id']].'
							</td>
							<td colspan="'.($due_col).'">&nbsp;</td>
							
							<td style="'.$crd_sty.'">&nbsp;';
							if($checkCcNumber_credit<>""){
								$arr_all_crd .=$payment_mode_credit;
							}
							$arr_all_crd .='</td>
							<td style="'.$crd_sty.'">&nbsp;'.$checkCcNumber_credit.'</td>
							<td>&nbsp;</td><td>&nbsp;</td>
							<td class="text-nowrap" style="'.$crd_sty.'">'.$dateApplied.'</td>
							<td class="text-nowrap" style="'.$crd_sty.'">'.$crApplied_entered_date.'</td>
							<td>&nbsp;</td>
							<td style="'.$crd_sty.'" class="text-right">'."$".$amountApplied.'</td>
							<td>&nbsp;</td>
							<td style="'.$crd_sty.'">'.$crApplied_batch_id.'</td>
							<td>
								<span style="'.$crd_sty.'">'.$crOperName.'</span>
								<span>'.$del_crOperName.'</span>
							</td>
						</tr>';
					
					}
					
					if($crAppliedTo=='adjustment' && $charge_list_detail_id_adjust<>$charge_list_detail_id && $charge_list_detail_id_chk==$charge_list_detail_id){
					 $crd_sty1="";
					 if($delete_credit==1){ $crd_sty1="text-decoration:line-through;color:#FF0000";}
					$arr_all_crd .='
					<tr '.$deleteRows.' '.$show_del_style.'>
							<td colspan="2">&nbsp;</td>
							<td colspan="4" style="font-weight:bold;">
								'.$note_debit.' '.$del_date_time.' '.$usr_alias_name[$getCreditsRows['del_operator_id']].'
							</td>
							<td colspan="'.($due_col).'">&nbsp;</td>
							<td style="'.$crd_sty1.'">'.$payment_mode_credit.'</td>
							<td style="'.$crd_sty1.'">'.$checkCcNumber_credit.'</td>
							<td>&nbsp;</td><td>&nbsp;</td>
							<td class="text-nowrap" style="'.$crd_sty1.'">'.$dateApplied.'</td>
							<td class="text-nowrap" style="'.$crd_sty1.'">'.$crApplied_entered_date.'</td>
							<td  style="'.$crd_sty1.'" class="text-right">'."$".$amountApplied.'</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td style="'.$crd_sty1.'">'.$crApplied_batch_id.'</td>
							<td>
								<span style="'.$crd_sty1.'">'.$crOperName.'</span>
								<span>'.$del_crOperName.'</span>
							</td>
		
						</tr>';
					
					}
					$arr_all[$dateApplied1][]=$arr_all_crd;
					$arr_date[]=$dateApplied1;
					$arr_all_crd="";
					$dateApplied1="";
				}
			}
	
			$countPayments = 0;
			foreach($trans_chld_data_arr as $t_key=>$t_val){
				if(count($trans_chld_data_arr[$t_key][$charge_list_detail_id])>0){
					foreach($trans_chld_data_arr[$t_key][$charge_list_detail_id] as $p_key=>$p_val){
					$getPaymentDetailsRows=$trans_chld_data_arr[$t_key][$charge_list_detail_id][$p_key];
					$arr_all_pay=$pay_chk_number=$cCChkNumber="";
					++$countPayments;
					$payment_id = $getPaymentDetailsRows['payment_id'];
					$paidForProc = $getPaymentDetailsRows['paidForProc'];
					$deduct_amount = $getPaymentDetailsRows['deduct_amount'];
					$paidBy = $getPaymentDetailsRows['paidBy'];
					$paidOfDateNew = $getPaymentDetailsRows['date_of_payment'];
					$paidOfDateTran = $getPaymentDetailsRows['transaction_date'];
					$overPayment = $getPaymentDetailsRows['overPayment'];
					$paymentClaims = $getPaymentDetailsRows['paymentClaims'];
					$CAS_type = $getPaymentDetailsRows['CAS_type'];
					$CAS_code = $getPaymentDetailsRows['CAS_code'];
					$pay_batch_id = $batch_track_no[$getPaymentDetailsRows['batch_id']];
					$unapply_type=$getPaymentDetailsRows['unapply_type'];
					$trans_facility_id = $getPaymentDetailsRows['facility_id'];
					
					$show_cas_code=show_cas_code_fun($getPaymentDetailsRows['CAS_type'],$getPaymentDetailsRows['CAS_code']);
					$del_operatorName="";
					if($getPaymentDetailsRows['del_operator_id']>0){
						$del_operatorName = '- '.$usr_alias_name[$getPaymentDetailsRows['del_operator_id']];
						$del_pay_date=($getPaymentDetailsRows['deleteDate']);
					}
					
					$del_fun="delPaymentId";
					$edit_fun="editPaymentFn";
					if($paymentClaims=="Deposit"){
						$paymentClaims_pay="Deposit";
					}else if($paymentClaims=="Interest Payment"){
						$paymentClaims_pay="Interest Payment";
					}else if($paymentClaims=="Negative Payment"){
						$paymentClaims_pay="Negative Payment";
						$del_fun="delNegPaymentId";
						$edit_fun="editNegPaymentFn";
					}else{
						$paymentClaims_pay="Payment";
					}
					
					list($yy, $mm, $dd) = explode("-", $paidOfDateNew);
					
					 $paidOfDateNew = date('m-d-y',mktime(0,0,0,$mm,$dd,$yy));
					 $paidOfDateNew1 = date('m-d-Y',mktime(0,0,0,$mm,$dd,$yy));
					 
					 list($yy1, $mm1, $dd1) = explode("-", $paidOfDateTran);
					 $paidOfDateTran = date('m-d-y',mktime(0,0,0,$mm1,$dd1,$yy1));
						 
					$payment_details_id = $getPaymentDetailsRows['payment_details_id'];
					$deletePayment = $getPaymentDetailsRows['deletePayment'];
					$expirationDate = $getPaymentDetailsRows['expirationDate'];
					$deleteDate = $getPaymentDetailsRows['deleteDate'];
					if($deleteDate!='0000-00-00'){
						 list($delYear, $delMonth, $delDay) = explode("-", $deleteDate);
						 $deleteDate = "Deleted  : ";
					}else{
						$deleteDate = "";
						$chk_ins_type="";
						$insCompany=$getPaymentDetailsRows['insCompany'];
						$chk_ins_type=$insCompany;
						if($paymentClaims=='Negative Payment'){
							$show_total_paid_arr[$chk_ins_type][]=-($getPaymentDetailsRows['paidForProc']+$getPaymentDetailsRows['overPayment']);
						}else{

							$show_total_paid_arr[$chk_ins_type][]=$getPaymentDetailsRows['paidForProc']+$getPaymentDetailsRows['overPayment'];
						}
		
					}
					
					$modified_date = $getPaymentDetailsRows['modified_date'];
					$modified_by = $getPaymentDetailsRows['modified_by'];
					$modifiedBy=$usr_full_name[$modified_by];
					if($modified_date=='0000-00-00'){
						$modified_date = '';
					}else{
						list($modYear, $modMonth, $modDay) = explode("-", $modified_date);
						$modified_date = '&nbsp;Modified Date - '.$modMonth."-".$modDay."-".$modYear.'&nbsp;By&nbsp;'.$modifiedBy;
					}
					
					$insProviderId = $getPaymentDetailsRows['insProviderId'];
					//-------------------- GETTING INS. CO. NAME --------------------//
					if(($insProviderId!=0) && ($insProviderId!="")){
						$insCoCode = $ins_comp_data[$insProviderId]['in_house_code'];
						$insCoName = $ins_comp_data[$insProviderId]['name'];
					}
					if((!$insCoCode) || ($insCoCode=='')){
						$insCoCode = $insCoName;
					}
					//-------------------- GETTING INS. CO. NAME --------------------//
					
					if($paidBy == 'Insurance'){
						$paidByCode = $insCoCode;
						if(strlen($paidByCode)>8){
							$paidByCode = $paidByCode;
						}
					}
					if($paidBy == 'Patient'){
						$paidByCode = 'Patient';
					}
					if($paidBy == 'Res. Party'){
						$paidByCode = 'Res. Party';
					}
					if($paymentClaims=='Negative Payment'){
						if($unapply_type=='pp'){
							$paidByCode=$paidByCode.' (Pre Payments)';
						}
						if($unapply_type=='ci'){
							$paidByCode=$paidByCode.' (CI/CO)';
						}
					}
					
					$paymentMethod = $getPaymentDetailsRows['payment_mode'];
					$operatorId = $getPaymentDetailsRows['operatorId'];
					//------------ GETTING OPERATOR NAME ------------//
					$operatorName=$usr_alias_name[$operatorId];
					//------------ GETTING OPERATOR NAME ------------//
					$paidDate = $getPaymentDetailsRows['paidDate'];
					list($paidYear, $paidMon, $paidDay) = explode("-", $paidDate);
					$paidDate = date('m-d-y',mktime(0,0,0,$paidMon,$paidDay,$paidYear));
					
					if($paymentMethod=='Check' || stripos($paymentMethod,'Check')>0){
						$cCChkNumber = $getPaymentDetailsRows['checkNo'];
						$pay_chk_number = $getPaymentDetailsRows['checkNo'];
					}
					if($paymentMethod=='Money Order'){
						$cCChkNumber = $getPaymentDetailsRows['checkNo'];
						$pay_chk_number = $getPaymentDetailsRows['checkNo'];
						$paymentMethod="MO";
					}
					if($paymentMethod=='EFT'){
						$cCChkNumber = $getPaymentDetailsRows['checkNo'];
						$pay_chk_number = $getPaymentDetailsRows['checkNo'];
					}
					if($paymentMethod=='VEEP'){
						$cCChkNumber = $getPaymentDetailsRows['checkNo'];
						$pay_chk_number = $getPaymentDetailsRows['checkNo'];
					}
					$creditCardCo = strtoupper(substr($getPaymentDetailsRows['creditCardCo'], 0, 2));
					if($paymentMethod=='Credit Card'){
						$paymentMethod = 'CC';
						$cCChkNumber = $getPaymentDetailsRows['creditCardNo'];
						$expDate = $getPaymentDetailsRows['expirationDate'];
						$ccNoLength = strlen($cCChkNumber);
						if($ccNoLength>3){
							$cCChkNumber = strrev($cCChkNumber);
							$cCChkNumber = substr($cCChkNumber, 0, 4);
							$cCChkNumber = "xx".strrev($cCChkNumber);
							$cCChkNumber = $creditCardCo.' - '.$cCChkNumber;
						}
						
						if($deletePayment!=1){
							$expirationDate = $getPaymentDetailsRows['expirationDate'];
						}
					}else{
						$expirationDate = '';
					}									
					++$seque;
						if($overPayment){
							$paidProc = $paidForProc + $overPayment;
						}else{
							$paidProc = $paidForProc;
						}
						$deleteRows = '';
						$show_del_style= '';
						if($deletePayment==1){
							$show_del_style='class="hide deleted"';
						}else{
							$show_del_style="";
						}
						$arr_all_pay .='
						<tr '.$deleteRows.' '.$show_del_style.'>
							<td colspan="2">';
							if($deletePayment!=1 && $_REQUEST['batch_pat_id']<=0){
								if(in_array($pay_chk_number,$ret_chk_arr)){
									$arr_all_pay .='&nbsp;';
								}else{
									$arr_all_pay .='<a href="javascript:void(0);" onClick="javascript:edit_enc_trans(\''.$payment_details_id.'\',\''.$encounter_id.'\',\''.$paymentClaims.'\');">
														<img src="../../library/images/edit.png" alt="Edit" style="border:none;">
													</a>
													<a href="javascript:void(0);" onClick="javascript:del_enc_trans(\''.$payment_details_id.'\',\''.$encounter_id.'\',\'payment\',\''.$payment_id.'\');">
														<img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;">
													</a>';
								}
							}
							$arr_all_pay .='</td>
							<td colspan="4" style="font-weight:bold;">'.$paymentClaims_pay.'';
							
								if($deleteDate){
									$arr_all_pay .= '&nbsp;'.$deleteDate.$paidByCode.' '.mmddyy_date($del_pay_date).' '.$usr_alias_name[$getPaymentDetailsRows['del_operator_id']];
								}else{
									if($modified_date){ 
										$arr_all_pay .= ' : '.$paidByCode. ' - '. $modified_date; 
									}else{ 
										$arr_all_pay .= ' : '.$paidByCode; 
									} 
								}
								if(strstr($moaQualifier, 'MA18') || strstr($moaQualifier, 'MA07')){
									$show_moaQualifier="MA18";
									if(strstr($moaQualifier, 'MA07')){
										$show_moaQualifier="MA07";
									}												
									$arr_all_pay .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									if($primaryInsCoName == $paidByCode){														
										if($deletePayment!=1){
											$arr_all_pay .= '('.$show_moaQualifier.' - Forwarded to '.$secondaryInsCoName.')';
										}
									}
								}
							$pay_sty="";
							if($deletePayment==1){ $pay_sty="text-decoration:line-through;color:#FF0000;"; } 
							$arr_all_pay .='</td><td colspan="'.($due_col).'" style="'.$pay_sty.'">'.$show_cas_code.'</td>
							<td style="'.$pay_sty.'">
							<span style="float:right;" class="text-red pointer" '.show_tooltip($fac_data_arr[$trans_facility_id]['name']).'>'.$fac_ins_name_arr[$trans_facility_id].'</span>
							'.$paymentMethod.'</td>
							 <td style="'.$pay_sty.'">';
								
							if($paymentMethod!='Cash'){
								$arr_all_pay .= $cCChkNumber; 
							}else{
								$arr_all_pay .= "-";
							}
								
							$arr_all_pay .='</td>
							<td style="'.$pay_sty.'" id="paymentTD'.$seque.'" class="text-right">'."$".number_format($paidProc, 2).'</td>
							<td style="'.$pay_sty.'"></td>
							<td class="text-nowrap" style="'.$pay_sty.'">'.$paidOfDateNew.'</td>
							<td class="text-nowrap" style="'.$pay_sty.'">'.$paidOfDateTran.'</td>
							<td style="'.$pay_sty.'"></td>
							<td style="'.$pay_sty.'"></td>
							<td style="'.$pay_sty.'">&nbsp;</td>
							<td style="'.$pay_sty.'">'.$pay_batch_id.'</td>
							<td>
								<span style="'.$pay_sty.'">'.$operatorName.'</span>
								<span>'.$del_operatorName.'</span>
							</td>
						</tr>';
						$arr_all[$paidOfDateNew1][]=$arr_all_pay;
						$arr_date[]=$paidOfDateNew1;
					}
				}
			}
	
			if($_REQUEST['batch_pat_id']>0 && $encounter_id>0){

				$getPayment_batch = imw_query("SELECT * FROM manual_batch_tx_payments WHERE batch_id = '$b_id' and charge_list_detail_id='$charge_list_detail_id' and del_status=0");
				$getRows_batch_Count = imw_num_rows($getPayment_batch);
				if($getRows_batch_Count>0){
					while($getAccPayRows = imw_fetch_array($getPayment_batch)){
						$arr_all_tx_pay="";
						$id = $getAccPayRows['id'];
						$b_id = $getAccPayRows['batch_id'];
						$batch_trans_id = $getAccPayRows['batch_trans_id'];
						$pri_due_old = $getAccPayRows['pri_due'];
						$sec_due_old  = $getAccPayRows['sec_due'];
						$tri_due_old = $getAccPayRows['tri_due'];
						$pat_due_old = $getAccPayRows['pat_due'];
						$encounter_id = $getAccPayRows['encounter_id'];
						$patient_id = $getAccPayRows['patient_id'];
			
						list($year, $month, $day)=explode("-", $getAccPayRows['payment_date']);
						$payment_date = date('m-d-y',mktime(0,0,0,$month,$day,$year));
						$payment_date_for_arr = date('m-d-Y',mktime(0,0,0,$month,$day,$year));
						
						$operator_id = $getAccPayRows['operator_id'];
						$del_status = $getAccPayRows['del_status'];
						$del_date=$getAccPayRows['del_date_time'];
						$operator_name = $usr_alias_name[$operator_id];	
					$tx_entered_date = mmddyy_date($getAccPayRows['entered_date']);
					$del_operator=$usr_alias_name[$getAccPayRows['del_operator_id']];
					$deleteRows = '';
					$show_del_style= '';
					if($del_status==1){
						$show_del_style='class="hide deleted"';
					}else{
						$show_del_style="";
					}
					
					$arr_all_tx_pay .='	
					<tr '.$deleteRows.' '.$show_del_style.'>
						<td colspan="2">';
						if($del_status==0){
							$arr_all_tx_pay .='<a>&nbsp;&nbsp;</a>
												<a href="javascript:void(0);" onClick="javascript:delTxTransId(\''.$b_id.'\',\''.$encounter_id.'\',\''.$id.'\',\''.$patient_id.'\',\''.$batch_trans_id.'\')"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>';
						}else{
							$arr_all_tx_pay .= '&nbsp;';
						}	
						$arr_all_tx_pay .= '
						</td>
						<td colspan="4" style="font-weight:bold;">';
							if(($payment_date!='00-00-0000') && ($payment_date!='') && ($del_status=='1')){
								$arr_all_tx_pay .='Tx Balance Deleted ';
								$arr_all_tx_pay .=mmddyy_date($del_date).' '.$del_operator.'</td>';
							}else{
								$acc_pay_sty="";
									$arr_all_tx_pay .='Tx Balance';
							}
						$arr_all_tx_pay .= '</td>';
						$acc_pay_sty1="";
						if($del_status=='1') $acc_pay_sty1="text-decoration:line-through;color:#FF0000;";
						if($primaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right">$'.$pri_due_old.'</td>';
						if($secondaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right">$'.$sec_due_old.'</td>';
						if($tertiaryInsProviderId>0) $arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'"class="text-right">$'.$tri_due_old.'</td>';
						$arr_all_tx_pay .='
						<td style="'.$acc_pay_sty1.'" class="text-right">$'.$pat_due_old.'</td>';
						if($show_resp_party>0){
							$arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'">&nbsp;</td>';
						}
						$arr_all_tx_pay .='<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td class="text-nowrap" style="'.$acc_pay_sty1.'">'.$payment_date.'</td>
						<td class="text-nowrap" style="'.$acc_pay_sty1.'">'.$tx_entered_date.'</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">&nbsp;</td>
						<td style="'.$acc_pay_sty1.'">'.$operator_name.'</td>
	
					</tr>';
					$arr_all[$payment_date_for_arr][]=$arr_all_tx_pay;
					$arr_date[]=$payment_date_for_arr;
					}
				}
				
				//batch file payment transaction
				$deletePayment="";
				$getPayment_batch = "SELECT * FROM manual_batch_transactions
										WHERE batch_id = '$b_id' and charge_list_detaill_id='$charge_list_detail_id'
										and payment_claims !='Allowed' and payment_claims!='Tx Balance'
										and post_status!=1 and del_status=0";
				$getPayment_batchQry = imw_query($getPayment_batch);
				$getRows_batch_Count = imw_num_rows($getPayment_batchQry);
				if($getRows_batch_Count>0){
					while($getPaymentBatchRows = imw_fetch_array($getPayment_batchQry)){
						$arr_all_pay_tran=$chk_number_trans=$modified_date="";
						$trans_id = $getPaymentBatchRows['trans_id'];
						$trans_amt = $getPaymentBatchRows['trans_amt'];
						$trans_payment_claims = $getPaymentBatchRows['payment_claims'];
						$trans_payment_mode = $getPaymentBatchRows['payment_mode'];
						$trans_date = $getPaymentBatchRows['trans_date'];
						$trans_operator_id = $getPaymentBatchRows['operator_id'];
						$trans_insurance_id = $getPaymentBatchRows['insurance_id'];
						$trans_by = $getPaymentBatchRows['trans_by'];
						$creditCardType= $getPaymentBatchRows['credit_card_type'];
						$write_off_code_id= $getPaymentBatchRows['write_off_code_id'];
						$adj_code_id= $getPaymentBatchRows['adj_code_id'];
						$enc_id= $getPaymentBatchRows['encounter_id'];
						$cas_type= $getPaymentBatchRows['cas_type'];
						$cas_code= $getPaymentBatchRows['cas_code'];
						$ins_selected = $getPaymentBatchRows['ins_selected'];
						$trans_facility_id = $getPaymentBatchRows['facility_id'];
						
						$write_off_code_trans="";
						if($write_off_code_id>0){
							$write_off_code_trans=$wrt_name_arr[$write_off_code_id];
						}else if($adj_code_id>0){
							$write_off_code_trans=$adj_code_data[$adj_code_id]['a_code'];
						}
						
						$show_cas_code=show_cas_code_fun($cas_type,$cas_code);
						
						list($year, $month, $day)=explode("-",$trans_date);
						$paidOfDateNew1 = date('m-d-Y',mktime(0,0,0,$month,$day,$year));
						$paid_trans_amt=$adj_trans_amt=$other_trans_amt=""; 
						if($trans_payment_claims=="Deposit"){
							$trans_payment_claims="Deposit";
							$paid_trans_amt=numberFormat($trans_amt,2,'yes');
							$show_total_paid_arr[$ins_selected][]=$trans_amt;
						}else if($trans_payment_claims=="Interest Payment"){
							$trans_payment_claims="Interest Payment";
							$paid_trans_amt=numberFormat($trans_amt,2,'yes');
							$show_total_paid_arr[$ins_selected][]=$trans_amt;
						}else if($trans_payment_claims=="Paid"){
							$trans_payment_claims="Payment";
							$paid_trans_amt=numberFormat($trans_amt,2,'yes');
							$show_total_paid_arr[$ins_selected][]=$trans_amt;
						}else if($trans_payment_claims=="Negative Payment"){
							$paid_trans_amt=numberFormat($trans_amt,2,'yes');
							$show_total_paid_arr[$ins_selected][]=-$trans_amt;
						}else if($trans_payment_claims=="Denied"){
							$trans_payment_claims="Denial";
							$trans_payment_mode="";
						}else if($trans_payment_claims=="Deductible"){
							$trans_payment_mode="";
						}else if($trans_payment_claims=="Co-Insurance" || $trans_payment_claims=="Co-Payment"){
							$trans_payment_mode="";
							$other_trans_amt=numberFormat($trans_amt,2,'yes');
						}else{
							$trans_payment_mode="";
							$adj_trans_amt=numberFormat($trans_amt,2,'yes');
						}
						
						//-------------------- GETTING INS. CO. NAME --------------------//
						if(($trans_insurance_id!=0) && ($trans_insurance_id!="")){
							$getInsCoStr = "SELECT in_house_code,name FROM insurance_companies WHERE id = '$trans_insurance_id'";
							$getInsCoQry = imw_query($getInsCoStr);
							$getInsCoRow = imw_fetch_array($getInsCoQry);
							$insCoCode = $getInsCoRow['in_house_code'];
							$insCoName = $getInsCoRow['name'];
						}
						if((!$insCoCode) || ($insCoCode=='')){
							$insCoCode = $insCoName;
						}
						//-------------------- GETTING INS. CO. NAME --------------------//
						
						if($trans_by == 'Insurance'){
							$paidByCode = $insCoCode;
							if(strlen($paidByCode)>8){
								$paidByCode = $paidByCode;
							}
						}
						
						if($trans_by == 'Patient'){
							$paidByCode = 'Patient';
						}
						
						if($trans_by == 'Res. Party'){
							$paidByCode = 'Res. Party';
						}
						$operatorName_tran = $usr_alias_name[$trans_operator_id];
						
						if($trans_payment_mode=='Check' || $trans_payment_mode=='EFT' || $trans_payment_mode=='Money Order' || $trans_payment_mode=='VEEP' || stripos($trans_payment_mode,'Check')>0){
							$chk_number_trans = $getPaymentBatchRows['check_no'];
						}
						
						$creditCardNoTrans = strtoupper(substr($getPaymentBatchRows['credit_card_no'], 0, 2));
						if($trans_payment_mode=='Credit Card'){
							$trans_payment_mode = 'CC';
							$creditCardNoTrans = $getPaymentBatchRows['credit_card_no'];
							$expDate_cc_tran = $getPaymentBatchRows['credit_card_exp'];
							$ccNoLength_tran = strlen($creditCardNoTrans);
							if($ccNoLength_tran>3){
								$creditCardNoTrans = strrev($creditCardNoTrans);
								$creditCardNoTrans = substr($creditCardNoTrans, 0, 4);
								$creditCardNoTrans = "xx".strrev($creditCardNoTrans);
								$chk_number_trans = $creditCardType.' - '.$creditCardNoTrans;
							}
						}else{
							$expDate_cc_tran = '';
						}
															
						++$seque;
							$deleteRows_tran = '';
							$show_del_style= '';
							$deleteDate="";
							if($deleteRows_tran==1){
								$show_del_style='class="hide deleted"';
								$deleteRows = 'id="deleted_rows_id[]"';
							}else{
								$show_del_style="";
							}
		
							$pay_cols=3;
							$method_cols=$trans_desc_cols=4;
							if($ins_selected>0){
								$pay_cols=$ins_selected+$pay_cols;
								$method_cols=$method_cols-$ins_selected;
							}else{
								$pay_cols=$pay_cols+$due_col;
								$method_cols=0;
							}
							if($trans_payment_claims=="Deductible"){
								$trans_desc_cols="3";
							}
							$arr_all_pay_tran .='
							<tr '.$deleteRows_tran.'   '.$show_del_style.'>
								<td colspan="2" align="left">';
								if($deletePayment!=1){ 
									$arr_all_pay_tran .='
										<a href="javascript:editTrans(\''.$b_id.'\',\''.$trans_id.'\');" class="text_10b_purpule">
											<img src="../../library/images/edit.png" alt="Edit" border="0">
										</a>
										<a href="javascript:delTransId(\''.$b_id.'\',\''.$enc_id.'\',\''.$trans_id.'\',\''.$patient_id.'\');" class="text_10b_purpule">
											<img src="../../library/images/del.png" alt="Del" border="0">
										</a>';
									 } 
								 $arr_all_pay_tran .='</td>
								<td colspan="'.$trans_desc_cols.'"  style="font-weight:bold;">'.$trans_payment_claims.'';
								
									if($deleteDate){
										$arr_all_pay_tran .= '&nbsp;'.$deleteDate.$paidByCode;
									}else{
										if($modified_date){ 
											$arr_all_pay_tran .= ' : '.$paidByCode. ' - '. $modified_date; 
										}else{ 
											$arr_all_pay_tran .= ' : '.$paidByCode; 
										} 
									}
								$arr_all_pay_tran .='</td>';
								if($trans_payment_claims=="Deductible"){
									$arr_all_pay_tran .='<td class="text-right">'.numberFormat($trans_amt,2,'yes').'</td>';
								}
								
								$pri_den_amt=$sec_den_amt=$ter_den_amt=$pat_den_amt=$resp_den_amt="";
								if($trans_payment_claims=="Denial"){
									if($primaryInsProviderId>0){
										if($primaryInsProviderId==$trans_insurance_id){
											$pri_den_amt=$trans_amt;
										}
										$arr_all_pay_tran .='<td class="text-right">'.numberFormat($pri_den_amt,2,'no').'</td>';
									}
									if($secondaryInsProviderId>0){
										if($secondaryInsProviderId==$trans_insurance_id && $pri_den_amt<=0){
											$sec_den_amt=$trans_amt;
										}
										$arr_all_pay_tran .='<td class="text-right">'.numberFormat($sec_den_amt,2,'no').'</td>';
									}
									if($tertiaryInsProviderId>0){
										if($tertiaryInsProviderId==$trans_insurance_id && $pri_den_amt<=0 && $sec_den_amt<=0){
											$ter_den_amt=$trans_amt;
										}
										$arr_all_pay_tran .='<td class="text-right">'.numberFormat($ter_den_amt,2,'no').'</td>';
									}
									if($trans_insurance_id==0){
										if($trans_by=="Res. Party"){
											$resp_den_amt=$trans_amt;
										}else{
											$pat_den_amt=$trans_amt;
										}
									}
									
									$arr_all_pay_tran .='<td class="text-right">'.numberFormat($pat_den_amt,2,'no').'</td>';
									if($show_resp_party>0){
										$arr_all_pay_tran .='<td class="text-right">'.numberFormat($resp_den_amt,2,'no').'</td>';
									}
								}else{
									if($trans_payment_claims=="Co-Insurance" || $trans_payment_claims=="Co-Payment"){
										$arr_all_pay_tran .='<td colspan="'.$due_col.'">'.$show_cas_code.' '.$other_trans_amt.'</td>';
										$show_cas_code="";
									}else{
										$arr_all_pay_tran .='<td colspan="'.$due_col.'"></td>';
									}
								}
								$arr_all_pay_tran .='<td>
									<span style="float:right;" class="text-red pointer" '.show_tooltip($fac_data_arr[$trans_facility_id]['name']).'>'.$fac_ins_name_arr[$trans_facility_id].'</span>
									'.$trans_payment_mode.'
								</td>
								<td '.$pay_sty.'>';
									if($trans_payment_mode!='Cash'){
										$arr_all_pay_tran .= $chk_number_trans; 
									}else{
										$arr_all_pay_tran .= "-";
									}
								$arr_all_pay_tran .='</td>
								<td class="text-right">'.$paid_trans_amt.'</td>';
								$arr_all_pay_tran .='</td>
								<td></td>
								<td>'.mmddyy_date($trans_date).'</td>
								<td></td>
								<td class="text-right">'.$adj_trans_amt.'</td>
								<td></td>
								<td>'.$write_off_code_trans.$show_cas_code.'</td>
								<td></td>
								<td>'.$operatorName_tran.'</td>
							</tr>';
						$arr_all[$paidOfDateNew1][]=$arr_all_pay_tran;
						$arr_date[]=$paidOfDateNew1;
					}
				}
			
				$deletePayment="";
				$getPayment_batch = "SELECT * FROM manual_batch_creditapplied WHERE batch_id = '$b_id' and charge_list_detail_id='$charge_list_detail_id'
									 and post_status!=1 and delete_credit=0 and crAppliedTo='payment'";
				$getPayment_batchQry = imw_query($getPayment_batch);
				$getRows_batch_Count = imw_num_rows($getPayment_batchQry);
				if($getRows_batch_Count>0){
					while($getPaymentBatchRows = imw_fetch_array($getPayment_batchQry)){
						$arr_all_pay_tran=$chk_number_trans=$modified_date=$paid_trans_amt="";
						$trans_id = $getPaymentBatchRows['crAppId'];
						$trans_amt = $getPaymentBatchRows['amountApplied'];
						$trans_payment_mode = $getPaymentBatchRows['payment_mode'];
						$trans_date = $getPaymentBatchRows['dateApplied'];
						$trans_operator_id = $getPaymentBatchRows['operatorApplied'];
						$trans_by = $getPaymentBatchRows['type'];
						$creditCardType= $getPaymentBatchRows['creditCardCo'];
						$enc_id= $getPaymentBatchRows['crAppliedToEncId'];
						$ins_selected = $getPaymentBatchRows['insCompany'];
						$trans_facility_id = $getPaymentBatchRows['facility_id'];
						$ins_selected = $getPaymentBatchRows['ins_selected'];
						$trans_insurance_id = $getPaymentBatchRows['ins_case'];
						$adj_trans_amt = numberFormat($getPaymentBatchRows['amountApplied'],2,'yes');
						$trans_payment_claims="";
						if($getPaymentBatchRows['crAppliedTo']=="payment"){
							$trans_payment_claims="Refund";
						}
						
						//-------------------- GETTING INS. CO. NAME --------------------//
						if(($trans_insurance_id!=0) && ($trans_insurance_id!="")){
							$getInsCoStr = "SELECT in_house_code,name FROM insurance_companies WHERE id = '$trans_insurance_id'";
							$getInsCoQry = imw_query($getInsCoStr);
							$getInsCoRow = imw_fetch_array($getInsCoQry);
							$insCoCode = $getInsCoRow['in_house_code'];
							$insCoName = $getInsCoRow['name'];
						}
						if((!$insCoCode) || ($insCoCode=='')){
							$insCoCode = $insCoName;
						}
						//-------------------- GETTING INS. CO. NAME --------------------//
						
						if($trans_by == 'Insurance'){
							$paidByCode = $insCoCode;
							if(strlen($paidByCode)>8){
								$paidByCode = $paidByCode;
							}
						}
						
						if($trans_by == 'Patient'){
							$paidByCode = 'Patient';
						}
						
						if($trans_by == 'Res. Party'){
							$paidByCode = 'Res. Party';
						}
						$operatorName_tran = $usr_alias_name[$trans_operator_id];
						
						if($trans_payment_mode=='Check' || $trans_payment_mode=='EFT' || $trans_payment_mode=='Money Order' || $trans_payment_mode=='VEEP' || stripos($trans_payment_mode,'Check')>0){
							$chk_number_trans = $getPaymentBatchRows['checkCcNumber'];
						}
						
						$creditCardNoTrans = strtoupper(substr($getPaymentBatchRows['creditCardNo'], 0, 2));
						if($trans_payment_mode=='Credit Card'){
							$trans_payment_mode = 'CC';
							$creditCardNoTrans = $getPaymentBatchRows['creditCardNo'];
							$expDate_cc_tran = $getPaymentBatchRows['expirationDateCc'];
							$ccNoLength_tran = strlen($creditCardNoTrans);
							if($ccNoLength_tran>3){
								$creditCardNoTrans = strrev($creditCardNoTrans);
								$creditCardNoTrans = substr($creditCardNoTrans, 0, 4);
								$creditCardNoTrans = "xx".strrev($creditCardNoTrans);
								$chk_number_trans = $creditCardType.' - '.$creditCardNoTrans;
							}
						}else{
							$expDate_cc_tran = '';
						}
															
							++$seque;
							$deleteRows_tran = '';
							$show_del_style= '';
							$deleteDate="";
							if($deleteRows_tran==1){
								$show_del_style='class="hide deleted"';
								$deleteRows = 'id="deleted_rows_id[]"';
							}else{
								$show_del_style="";
							}
		
							$pay_cols=3;
							$method_cols=$trans_desc_cols=4;
							if($ins_selected>0){
								$pay_cols=$ins_selected+$pay_cols;
								$method_cols=$method_cols-$ins_selected;
							}else{
								$pay_cols=$pay_cols+$due_col;
								$method_cols=0;
							}
							$arr_all_pay_tran .='
							<tr '.$deleteRows_tran.'   '.$show_del_style.'>
								<td colspan="2" align="left">';
								if($deletePayment!=1){ 
									$arr_all_pay_tran .='
										<a href="javascript:editCreditTrans(\''.$b_id.'\',\''.$trans_id.'\');" class="text_10b_purpule">
											<img src="../../library/images/edit.png" alt="Edit" border="0">
										</a>
										<a href="javascript:delCreditTransId(\''.$b_id.'\',\''.$enc_id.'\',\''.$trans_id.'\',\''.$patient_id.'\');" class="text_10b_purpule">
											<img src="../../library/images/del.png" alt="Del" border="0">
										</a>';
									 } 
								 $arr_all_pay_tran .='</td>
								<td colspan="'.$trans_desc_cols.'"  style="font-weight:bold;">'.$trans_payment_claims.'';
								
									if($deleteDate){
										$arr_all_pay_tran .= '&nbsp;'.$deleteDate.$paidByCode;
									}else{
										if($modified_date){ 
											$arr_all_pay_tran .= ' : '.$paidByCode. ' - '. $modified_date; 
										}else{ 
											$arr_all_pay_tran .= ' : '.$paidByCode; 
										} 
									}
								$arr_all_pay_tran .='</td>';
								$arr_all_pay_tran .='<td colspan="'.$due_col.'"></td>
								<td>
									<span style="float:right;" class="text-red pointer" '.show_tooltip($fac_data_arr[$trans_facility_id]['name']).'>'.$fac_ins_name_arr[$trans_facility_id].'</span>
									'.$trans_payment_mode.'
								</td>
								<td '.$pay_sty.'>';
									if($trans_payment_mode!='Cash'){
										$arr_all_pay_tran .= $chk_number_trans; 
									}else{
										$arr_all_pay_tran .= "-";
									}
								$arr_all_pay_tran .='</td>
								<td class="text-right">'.$paid_trans_amt.'</td>';
								$arr_all_pay_tran .='</td>
								<td></td>
								<td>'.mmddyy_date($trans_date).'</td>
								<td></td>
								<td class="text-right">'.$adj_trans_amt.'</td>
								<td></td>
								<td>'.$write_off_code_trans.$show_cas_code.'</td>
								<td></td>
								<td>'.$operatorName_tran.'</td>
							</tr>';
						$arr_all[$paidOfDateNew1][]=$arr_all_pay_tran;
						$arr_date[]=$paidOfDateNew1;
					}
				}
			}
	
			$arr_date1 = array_values(array_unique($arr_date));
			array_walk($arr_date1,"ymd2ts");
			sort($arr_date1);
			array_walk($arr_date1,"ts2ymd");
			for($k=0;$k<count($arr_date1);$k++){
				$date = $arr_date1[$k];
				for($p=0;$p<count($arr_all[$date]);$p++){
					if($arr_all[$date][$p]){
						if($_REQUEST['encounter_id']>0 && $show_credit==""){
							print $arr_all[$date][$p];
						}
					}
				}
			}
		}
	if($copay>0 && $show_credit==""){
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
		$write_off_code_id_copay=$wrt_name_arr[$write_off_code_copay];
		if(($coPayNotRequired == 0)){
			if($copayPaid<=0 && array_sum($trans_copay_total_arr)<=0){
				$res_due_col=0;
				if($show_resp_party>0){ $res_due_col=1;}
	?>
				<tr>
					<td>
						<div class="checkbox">
                        	<input type="hidden" name="tot_enc_copay_<?php echo $encounter_id; ?>" id="tot_enc_copay_<?php echo $encounter_id; ?>" value="<?php echo $copay; ?>">
                            <input type="hidden" name="tot_copay_paid_<?php echo $encounter_id; ?>" id="tot_copay_paid_<?php echo $encounter_id; ?>" value="<?php echo array_sum($enc_copay_paid_arr[$encounter_id]); ?>">
                            <input type="hidden" name="copay_chl_id_<?php echo $encounter_id; ?>" id="copay_chl_id_<?php echo $encounter_id; ?>" value="<?php echo $chargeListId; ?>">
                            <input type="checkbox" name="chkbx[<?php echo $encounter_id; ?>]" id="chkbx<?php echo $encounter_id; ?>" value="true" onClick="return changeAmt(<?php echo $copay; ?>,<?php echo $encounter_id; ?>);" />
							<label for="chkbx<?php echo $encounter_id; ?>"></label>
						</div>
					</td>
					<td style="font-weight:bold;">CoPay</td>
					<td colspan="<?php echo 3+$due_col-$res_due_col; ?>">
						<select name="proc_copay_<?php echo $encounter_id; ?>" id="proc_copay_<?php echo $encounter_id; ?>" class="selectpicker" onChange="changeAmt(<?php echo $copay; ?>,<?php echo $encounter_id; ?>);">
							<?php 
								for($i=0;$i<count($copay_chld_id_arr);$i++){
									$sel_copay="";
									if($copay_apply_for_chld==$copay_chld_id_arr[$i]){
										$sel_copay="selected";
									}
							?>
								<option value="<?php echo $copay_chld_id_arr[$i]; ?>" <?php echo $sel_copay; ?>><?php echo $copay_proc_code_arr[$copay_chld_id_arr[$i]]; ?></option>
							<?php } ?>
						</select>
					</td>
                    <td>
                        <div class="input-group mb5">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                            <input type="text" name="pat_paid_<?php echo $encounter_id; ?>" id="pat_paid_<?php echo $encounter_id; ?>" class="form-control" value="" onChange="checkChkBox(<?php echo $encounter_id; ?>,'pat_paid_');">
                        </div>
                     </td>
                     <?php if($show_resp_party>0){ echo "<td>&nbsp;</td>";}?>
                     <td style="width:83px !important;">
                        <?php
							$trans_method_arr=array();
                            $trans_arr=array("CoPay");
                            $method_arr=array("Cash","Check","Credit Card","EFT","Money Order","VEEP");
                            $method_cc_arr=array("American Express","Care Credit","Discover","Master Card","Visa","Others");
                            $no_need_type_arr=array("Discount","Denied","Deductible","Write Off","Debit","Credit","Check In/Out","Tx Balance","Update Allow Amt","Patient Pre Pmts");
                            foreach($trans_arr as $trans_key=>$trans_val){
                                if(!in_array($trans_val,$no_need_type_arr)){
                                    $trans_method_arr[$trans_key][]=$trans_val;
                                    foreach($method_arr as $method_key=>$method_val){
                                        $final_method_val=array($method_val,array(),$trans_val.' - '.$method_val);
                                        $trans_method_arr[$trans_key][1][$method_key]=$final_method_val;
                                        if($method_val=="Credit Card"){
                                            foreach($method_cc_arr as $method_cc_key=>$method_cc_val){
                                                $final_method_cc_val=array($method_cc_val,array(),$trans_val.' - '.$method_val.' - '.$method_cc_val);
                                                $trans_method_arr[$trans_key][1][$method_key][1][$method_cc_key]=$final_method_cc_val;
                                            }
                                        }
                                    }
                                }else{
                                    $trans_method_arr[$trans_key]=array($trans_val,array(),$trans_val);
                                }
                            }
                        ?>
                        <div class="input-group">
                            <input type="text" name="payment_method_<?php echo $encounter_id; ?>" id="payment_method_<?php echo $encounter_id; ?>" class="form-control" value="" readonly style="width:83px !important;">
                            <?php  echo get_simple_menu($trans_method_arr,"menu_method_".$encounter_id,"payment_method_".$encounter_id,"","0",$sm_unique_id++);?>
                        </div>	
                    </td>
                    <td>
                        <input type="text" name="check_cc_no_<?php echo $encounter_id; ?>" id="check_cc_no_<?php echo $encounter_id; ?>" class="form-control" value="" style="width:75px !important;">
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                    	<div class="input-group">
							<input type="text" name="paid_date_<?php echo $encounter_id; ?>" id="paid_date_<?php echo $encounter_id; ?>" value="<?php echo $cur_date; ?>" onBlur="checkdate(this);" class="form-control date-pick"  style="width:83px !important;"/>
						</div>
                    </td>
                    <td colspan="6"></td>
				</tr>
                <tr>
					<td colspan="<?php echo 6+$due_col; ?>"></td>
                    <td>
                        <div>
                            <select name="facility_id_<?php echo $encounter_id; ?>" id="facility_id_<?php echo $encounter_id; ?>" class="form-control minimal" style="width:113px;">
                                <option value="">Pay Location</option>
                                <?php
                                    foreach($fac_data_arr as $fac_key=>$fac_val){
                                        $FacilityDetails=$fac_data_arr[$fac_key];				
                                        $id = $FacilityDetails['id'];
                                        $sel="";
                                        if($default_pay_location>0){
                                            if($default_pay_location==$id){
                                                $sel="selected";
                                            }
                                        }else if($_SESSION['login_facility']==$id){
                                            $sel="selected";
                                        }
                                        print '<option '.$sel.' value="'.$id.'">'.$FacilityDetails['name'].'</option>';
                                    }
                                ?>
                            </select>
                      </div>	
                    </td>
                    <td colspan="10"></td>
				</tr>
				<?php
			}else{
				?>
				<tr>									
					<td colspan="2">&nbsp;&nbsp;<img src="../../library/images/confirm.gif"  style="width:16px;"/></td>
					<td colspan="<?php echo 15+$due_col; ?>" style="font-weight:bold;">CoPay</td>
				</tr>
				<?php
			}
		}else{
			?>
			<tr>
				<td colspan="18" style="font-weight:bold;">
				CoPay:
				<span style="color:#FF0000;"><?php echo "$".number_format($copay, 2); ?></span>
				<?php 
				if($coPayWriteOff!='1'){ 
					?>
					<span style="font-family:Arial; font-size:12px; font-weight:bold;color:#0000FF;">NR</span>
					<?php 
				}else{ 
					?>
					<span style="font-family:Arial; font-size:12px; font-weight:bold;color:#0000FF;"><?php if($paymentStatusType) echo $paymentStatusType; else echo 'Write Off'; ?></span>
					<?php
				} 
				?>
				</td>
				<td>
					<?php echo $write_off_code_id_copay; ?>
				</td>
			</tr>
			<?php
		}	
	}
	if($_REQUEST['batch_pat_id']>0 && $encounter_id>0){
		//batch file Copay payment transaction
		$getPayment_batch = "SELECT * FROM manual_batch_transactions WHERE batch_id = '$b_id' and charge_list_detaill_id='0'
							and encounter_id='$encounter_id'
							and payment_claims ='CoPay' and post_status!=1 and del_status=0";
		$getPayment_batchQry = imw_query($getPayment_batch);
		$getRows_batch_Count = imw_num_rows($getPayment_batchQry);
		if($getRows_batch_Count>0){
			while($getPaymentBatchRows = imw_fetch_array($getPayment_batchQry)){
				$arr_all_pay_tran=$chk_number_trans=$modified_date="";
				$trans_id = $getPaymentBatchRows['trans_id'];
				$trans_amt = $getPaymentBatchRows['trans_amt'];
				$trans_payment_claims = $getPaymentBatchRows['payment_claims'];
				$trans_payment_mode = $getPaymentBatchRows['payment_mode'];
				$trans_date = $getPaymentBatchRows['trans_date'];
				$trans_operator_id = $getPaymentBatchRows['operator_id'];
				$trans_insurance_id = $getPaymentBatchRows['insurance_id'];
				$trans_by = $getPaymentBatchRows['trans_by'];
				$creditCardType= $getPaymentBatchRows['credit_card_type'];
				$write_off_code_id= $getPaymentBatchRows['write_off_code_id'];
				$adj_code_id= $getPaymentBatchRows['adj_code_id'];
				$enc_id= $getPaymentBatchRows['encounter_id'];
				$cas_type= $getPaymentBatchRows['cas_type'];
				$cas_code= $getPaymentBatchRows['cas_code'];
				$ins_selected = $getPaymentBatchRows['ins_selected'];
				
				$paidOfDateNew1 = date('m-d-Y',mktime(0,0,0,$mm,$dd,$yy));
				$trans_payment_claims="Paid";
				$paid_trans_amt=numberFormat($trans_amt,2,'yes');
				
				if($trans_by == 'Patient'){
					$paidByCode = 'Patient';
				}
				
				$operatorName_tran = $usr_alias_name[$trans_operator_id];
				
				if($trans_payment_mode=='Check' || $trans_payment_mode=='EFT' || $trans_payment_mode=='Money Order' || $trans_payment_mode=='VEEP' || stripos($trans_payment_mode,'Check')>0){
					$chk_number_trans = $getPaymentBatchRows['check_no'];
				}
				
				$creditCardNoTrans = strtoupper(substr($getPaymentBatchRows['credit_card_no'], 0, 2));
				if($trans_payment_mode=='Credit Card'){
					$trans_payment_mode = 'CC';
					$creditCardNoTrans = $getPaymentBatchRows['credit_card_no'];
					$expDate_cc_tran = $getPaymentBatchRows['credit_card_exp'];
					$ccNoLength_tran = strlen($creditCardNoTrans);
					if($ccNoLength_tran>3){
						$creditCardNoTrans = strrev($creditCardNoTrans);
						$creditCardNoTrans = substr($creditCardNoTrans, 0, 4);
						$creditCardNoTrans = "xx".strrev($creditCardNoTrans);
						$chk_number_trans = $creditCardType.' - '.$creditCardNoTrans;
					}
				}else{
					$expDate_cc_tran = '';
				}
													
				$deleteRows_tran = '';
				$show_del_style= '';
				$deleteDate="";
				if($deleteRows_tran==1){
					$show_del_style='class="hide deleted"';
					$deleteRows = 'id="deleted_rows_id[]"';
				}else{
					$show_del_style="";
				}

				$pay_cols=3;
				$method_cols=$trans_desc_cols=4;
				if($ins_selected>0){
					$pay_cols=$ins_selected+$pay_cols;
					$method_cols=$method_cols-$ins_selected;
				}else{
					$pay_cols=$pay_cols+$due_col;
					$method_cols=0;
				}
				?>
				<tr <?php echo $deleteRows_tran ; ?> <?php echo $show_del_style; ?>>
					<td colspan="2">
					<?php if($deletePayment==0){?>
						<a href="javascript:void(0);" onClick="javascript:delTransId('<?php echo $b_id; ?>','<?php echo $trans_id?>');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>
					<?php } ?>
					</td>
					<td colspan="4" style="font-weight:bold;">
						<?php
						if($deletePayment=='1'){
							echo "CoPay Payment : ".$paidByCode;
						}else{
							echo "CoPay ".$trans_payment_claims." : ".$paidByCode;
						}
						?>									
					</td>	
					<td colspan="<?php echo $due_col; ?>"></td>	
					<td><?php echo $trans_payment_mode; ?></td>
					<td><?php if($trans_payment_mode!='Cash'){ echo $chk_number_trans; }else{echo "-";} ?></td>
					<td class="text-right"><?php echo $paid_trans_amt; ?></td>
					<td></td>
					<td class="text-nowrap"><?php echo mmddyy_date($trans_date); ?></td>
					<td class="text-nowrap"></td>
					<td></td><td></td><td></td>	
                    <td></td>
					<td><?php echo $operatorName_tran; ?></td>
				</tr>
				<?php
			}
		}
	}
	if($_REQUEST['encounter_id']>0 && $show_credit==""){
		if(count($payment_chld_data_arr[0])>0){
			foreach($payment_chld_data_arr[0] as $p_key=>$p_val){
				$getCoPayPaidInfoRows=$payment_chld_data_arr[0][$p_key];		
				$payment_details_id_coPay = $getCoPayPaidInfoRows['payment_details_id'];
				$payment_id_coPay = $getCoPayPaidInfoRows['payment_id'];
				$operatorId_coPay = $getCoPayPaidInfoRows['operatorId'];
				$coPayPaymentMode = $getCoPayPaidInfoRows['payment_mode'];
				$coPaycheckNo = $getCoPayPaidInfoRows['checkNo'];
				$coPaycreditCardCo = $getCoPayPaidInfoRows['creditCardCo'];
				$coPaycreditCardNo = $getCoPayPaidInfoRows['creditCardNo'];
				$paymentClaims = $getCoPayPaidInfoRows['paymentClaims'];
				$copay_pay_batch_id = $batch_track_no[$getCoPayPaidInfoRows['batch_id']];
				$copay_unapply_type=$getCoPayPaidInfoRows['unapply_type'];
				$operatorCoPay=$usr_alias_name[$operatorId_coPay];	
				$paidBy_coPay = $getCoPayPaidInfoRows['paidBy'];
				$paidDate_coPay = $getCoPayPaidInfoRows['paidDate'];
				list($coPayYear, $coPayMon, $coPayDay) = explode("-", $paidDate_coPay);
				$paidDate_coPay = date('m-d-y',mktime(0,0,0,$coPayMon,$coPayDay,$coPayYear));
					
				$DOT_coPay = $getCoPayPaidInfoRows['transaction_date'];
				list($coPayYear1, $coPayMon1, $coPayDay1) = explode("-", $DOT_coPay);
				$DOTDate_coPay = date('m-d-y',mktime(0,0,0,$coPayMon1,$coPayDay1,$coPayYear1));
		
				$paidForProc_coPay = $getCoPayPaidInfoRows['paidForProc'];
				$deleteDate_coPay = $getCoPayPaidInfoRows['deleteDate'];
				list($deleteDate_coPayYear, $deleteDate_coPayMon, $deleteDate_coPayDay) = explode("-", $deleteDate_coPay);
				$deleteDate_coPay = date('m-d-y',mktime(0,0,0,$deleteDate_coPayMon,$deleteDate_coPayDay,$deleteDate_coPayYear));
				$deletePayment = $getCoPayPaidInfoRows['deletePayment'];
				$trans_facility_id = $getCoPayPaidInfoRows['facility_id'];
				$del_operatorCoPay="";
					if($getCoPayPaidInfoRows['del_operator_id']>0){
						$del_operatorCoPay = '- '.$usr_alias_name[$getCoPayPaidInfoRows['del_operator_id']];
					}
						$insProviderId = $getCoPayPaidInfoRows['insProviderId'];
							//-------------------- GETTING INS. CO. NAME --------------------//
							if(($insProviderId!=0) && ($insProviderId!="")){
								$insCoCode = $ins_comp_data[$insProviderId]['in_house_code'];
								$insCoName = $ins_comp_data[$insProviderId]['name'];
							}
							if((!$insCoCode) || ($insCoCode=='')){
								$insCoCode = $insCoName;
							}
							//-------------------- GETTING INS. CO. NAME --------------------//
						if($paidBy_coPay == 'Insurance'){
							$paidBy_coPay = $insCoCode;
							if(strlen($paidBy_coPay)>8){
								$paidBy_coPay = $paidBy_coPay;
							}
						}else{
							$paidBy_coPay=$paidBy_coPay;
						}
						
						if($paymentClaims=='Negative Payment'){
							if($copay_unapply_type=='pp'){
								$paidBy_coPay=$paidBy_coPay.' (Pre Payments)';
							}
							if($copay_unapply_type=='ci'){
								$paidBy_coPay=$paidBy_coPay.' (CI/CO)';
							}
						}
						
					$deleteRows = $copay_sty = $show_del_style = '';
					if($deletePayment==1){
						$show_del_style='class="hide deleted"';
					}else{
						$show_del_style="";
						$chk_ins_type="";
						$insCompany=$getCoPayPaidInfoRows['insCompany'];
						$chk_ins_type=$insCompany;
						$show_total_paid_arr[$chk_ins_type][]=$getCoPayPaidInfoRows['paidForProc']+$getCoPayPaidInfoRows['overPayment'];
					}
					if($deletePayment==1){ $copay_sty="text-decoration:line-through;color:#FF0000"; } 
				?>
				<tr <?php echo $deleteRows ; ?> <?php echo $show_del_style; ?>>
					<td colspan="2">
					<?php
					if($deletePayment==0){
						?>
						<a href="javascript:void(0);" onClick="javascript:edit_enc_trans('<?php echo $payment_details_id_coPay; ?>','<?php echo $encounter_id?>','copay_payment');">&nbsp;<img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a>
						<a href="javascript:void(0);" onClick="javascript:del_enc_trans('<?php echo $payment_details_id_coPay; ?>','<?php echo $encounter_id; ?>','copay_payment','<?php echo $payment_id_coPay; ?>');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>
						<?php
					}
					?>
					</td>
					<td colspan="4" style="font-weight:bold;">
						<?php
						if($deletePayment=='1'){
							echo "CoPay Payment : ".$paidBy_coPay;
						}else{
							echo "CoPay ".$paymentClaims." : ".$paidBy_coPay;
						}
						?>									
					</td>	
					<td colspan="<?php echo $due_col; ?>"></td>	
					<td style=" <?php echo $copay_sty; ?>">
                    <span style="float:right;" class="text-red pointer" <?php echo show_tooltip($fac_data_arr[$trans_facility_id]['name']);?>><?php echo $fac_ins_name_arr[$trans_facility_id];?></span>
					<?php echo $coPayPaymentMode; ?>
                    </td>
					<td style=" <?php echo $copay_sty; ?>"><?php if($coPayPaymentMode=='Check' || $coPayPaymentMode == 'EFT' || $coPayPaymentMode == 'Money Order' || $coPayPaymentMode == 'VEEP' || stripos($coPayPaymentMode,'Check')>0){echo $coPaycheckNo;}else{ echo $coPaycreditCardNo;} ?></td>
					<td style=" <?php echo $copay_sty; ?>"  class="text-right"><?php echo "$".number_format($paidForProc_coPay, 2); ?></td>
					<td></td>
					<td class="text-nowrap" style=" <?php echo $copay_sty; ?>"><?php echo $paidDate_coPay; ?></td>
					<td class="text-nowrap" style=" <?php echo $copay_sty; ?>"><?php echo $DOTDate_coPay; ?></td>
					<td></td><td></td><td></td>	
                    <td><?php echo $copay_pay_batch_id; ?></td>
					<td>
						<span style=" <?php echo $copay_sty; ?>"><?php echo $operatorCoPay; ?></span>
						<span><?php echo $del_operatorCoPay; ?></span>
					</td>
				</tr>
				<?php
			}
		}
		//--------------------- COPAY WROTEOFF LIST ---------------------
		foreach($payment_wrt_data[$encounter_id][0] as $wrt_key=>$wrt_val){
			$getCoPayWriteOffListRows=$payment_wrt_data[$encounter_id][0][$wrt_key];	
			$coPay_write_off_by_id = $getCoPayWriteOffListRows['write_off_by_id'];
				//$coPay_write_off_by = getData('name', 'insurance_companies', 'id', $coPay_write_off_by_id);
			if($coPay_write_off_by_id>0){
				$coPay_write_off_by = $ins_comp_name_arr[$coPay_write_off_by_id];
			}else{
				$coPay_write_off_by="Patient";
			}			
			$coPay_write_off_amount = number_format($getCoPayWriteOffListRows['write_off_amount'], 2);
			$coPay_write_off_operator_id = $getCoPayWriteOffListRows['write_off_operator_id'];
				
			//$coPay_write_off_operatorName = getData('lname', 'users', 'id', $coPay_write_off_operator_id);
			//$coPay_write_off_operatorName = substr(getData('fname', 'users', 'id', $coPay_write_off_operator_id),0,1).''.substr($coPay_write_off_operatorName,0,1);
			$coPay_write_off_operatorName=$usr_alias_name[$coPay_write_off_operator_id];
				
			$coPay_delStatus = $getCoPayWriteOffListRows['delStatus'];
			$coPay_write_off_del_date = $getCoPayWriteOffListRows['write_off_del_date'];
			$coPay_write_off_code = $getCoPayWriteOffListRows['write_off_code_id'];
			$coPay_write_off_date = mmddyy_date($getCoPayWriteOffListRows['write_off_date']);
			$coPay_writeoff_entered_date = mmddyy_date($getCoPayWriteOffListRows['entered_date']);
			/*$w_code_qry3 = imw_query("SELECT w_code FROM write_off_code 
											WHERE w_id = '$coPay_write_off_code'");							
			$w_code_row3 = imw_fetch_array($w_code_qry3);
			$coPay_write_off_code_id=$w_code_row3['w_code'];*/
			$coPay_write_off_code_id=$wrt_name_arr[$coPay_write_off_code];
				list($coPayWOYear, $coPayWOMonth, $coPayWODay) = explode("-", $coPay_write_off_del_date);
				//$coPay_write_off_del_date = $coPayWOMonth."-".$coPayWODay."-".$coPayWOYear;		
				$coPay_write_off_del_date = date('m-d-y',mktime(0,0,0,$coPayWOMonth,$coPayWODay,$coPayWOYear));						
			$deleteRows = '';
			$show_del_style= '';
			if($getCoPayWriteOffListRows['delStatus']==1){
				$show_del_style='class="hide deleted"';
				//$deleteRows = 'id="deleted_rows_id[]"';
			}else{
				$show_del_style="";
			}
			$del_coPay_write_off_operatorName="";
			if($getCoPayWriteOffListRows['del_operator_id']>0){
				$del_coPay_write_off_operatorName = '- '.$usr_alias_name[$getCoPayWriteOffListRows['del_operator_id']];
			}
			if($getCoPayWriteOffListRows['delStatus']==1){ $copay_wrt_sty="text-decoration:line-through;color:#FF0000"; } 
			?>
			<tr <?php echo $deleteRows ; ?> <?php echo $show_del_style; ?>>
				 <td colspan="2">
					 <?php if($getCoPayWriteOffListRows['delStatus']=='0'){?>
						<a href="javascript:void(0);" onClick="javascript:edit_enc_trans('<?php echo $coPay_write_off_id; ?>','<?php echo $encounter_id?>','write_off_copay');">&nbsp;<img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a>
						<a href="javascript:void(0);" onClick="javascript:del_enc_trans('<?php echo $coPay_write_off_id; ?>','<?php echo $encounter_id; ?>','write_off_copay');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>
					<?php } ?>
				 </td>
				 <td colspan="4" style="font-weight:bold;">CoPay Write Off : <?php echo $coPay_write_off_by; ?></td>	
				 <td colspan="<?php echo $due_col; ?>"></td>
				 <td></td><td></td><td></td><td></td>		
				 <td class="text-nowrap" style=" <?php echo $copay_wrt_sty; ?>"><?php echo $coPay_write_off_date; ?></td>
				 <td class="text-nowrap" style=" <?php echo $copay_wrt_sty; ?>"><?php echo $coPay_writeoff_entered_date; ?></td>  
				 <td style=" <?php echo $copay_wrt_sty; ?>"><?php echo "$".$coPay_write_off_amount; ?></td>
				 <td></td>
				 <td style=" <?php echo $copay_wrt_sty; ?>"><?php echo $coPay_write_off_code_id; ?></td>
				 <td></td>
				 <td>
					<span style=" <?php echo $copay_wrt_sty; ?>"><?php echo $coPay_write_off_operatorName; ?></span>
					<span><?php echo $del_coPay_write_off_operatorName; ?></span>
				 </td>  
			</tr>
			<?php							
		}
	}
	//--------------------- COPAY WROTEOFF LIST ---------------------	
	
	$show_grand_chrg_arr[]=array_sum($show_total_chrg_arr);
	$show_grand_approved_arr[]=array_sum($show_total_approved_arr);
	$show_grand_deduct_arr[]=array_sum($show_total_deduct_arr);
	$show_grand_paid_arr[1][]=array_sum($show_total_paid_arr[1]);
	$show_grand_paid_arr[2][]=array_sum($show_total_paid_arr[2]);
	$show_grand_paid_arr[3][]=array_sum($show_total_paid_arr[3]);
	$show_grand_paid_arr[0][]=array_sum($show_total_paid_arr[0]);
	$show_grand_final_paid_arr[]=array_sum($show_final_paid_arr);
	$show_grand_due_arr[]=array_sum($show_total_due_arr);
?>
<?php //if($_REQUEST['encounter_id']>0){?>  
    <tr style="font-weight:bold;">
        <td class="purple_bar text-right" colspan="3">Total Payments</td>
        <td class="text-right purple_bar"><?php echo numberFormat(array_sum($show_total_chrg_arr),2,'yes');?></td>
        <td class="text-right purple_bar"><?php echo numberFormat(array_sum($show_total_approved_arr),2,'yes');?></td>
        <td class="text-right purple_bar"><?php echo numberFormat(array_sum($show_total_deduct_arr),2,'yes');?></td>
        <?php if($primaryInsProviderId>0){?>
        <td class="text-right purple_bar"><?php echo numberFormat(array_sum($show_total_paid_arr[1]),2,'yes');?></td>
        <?php } ?>
        <?php if($secondaryInsProviderId>0){?>
        <td class="text-right purple_bar"><?php echo numberFormat(array_sum($show_total_paid_arr[2]),2,'yes');?></td>
        <?php } ?>
        <?php if($tertiaryInsProviderId>0){?>
        <td class="text-right purple_bar"><?php echo numberFormat(array_sum($show_total_paid_arr[3]),2,'yes');?></td>
        <?php } ?>
        <td class="text-right purple_bar"><?php echo numberFormat(array_sum($show_total_paid_arr[0]),2,'yes');?></td>
        <?php if($show_resp_party>0){?>
        <td class="text-right purple_bar"></td>
        <?php } ?>
        <td class="text-right purple_bar"></td>
        <td class="text-right purple_bar"></td>
        <td class="text-right purple_bar"><?php echo numberFormat(array_sum($show_final_paid_arr),2,'yes');?></td>
        <td class="text-right purple_bar">
        <?php 
            $show_total_due_str=0;
            $show_total_due_str=array_sum($show_total_due_arr);
            if($show_total_due_str <= 0){
                $total_bal_col="Green";
            }else{
                $total_bal_col="Red";
            }
            $total_bal_col="";
			$ngt_balance=$show_total_due_str;
        ?>
        <span style="color:<?php echo $total_bal_col;?>">
            <?php echo numberFormat($show_total_due_str,2,'yes');?>
        </span>
        </td>
        <td colspan="7">&nbsp;</td>
    </tr>
<?php //}?>      
<?php    
}
?>
<?php if($_REQUEST['encounter_id']<=0 && imw_num_rows($getEncounterQry)>0){?>  
	<tr><td colspan="18"></td></tr>
    <tr style="font-weight:bold;">
    	<td class="text-right grand-total" colspan="3">Grand Total Payments</td>
        <td class="text-right grand-total"><?php echo numberFormat(array_sum($show_grand_chrg_arr),2,'yes');?></td>
        <td class="text-right grand-total"><?php echo numberFormat(array_sum($show_grand_approved_arr),2,'yes');?></td>
        <td class="text-right grand-total"><?php echo numberFormat(array_sum($show_grand_deduct_arr),2,'yes');?></td>
        <?php if($primaryInsProviderId>0){?>
        <td class="text-right grand-total"><?php echo numberFormat(array_sum($show_grand_paid_arr[1]),2,'yes');?></td>
        <?php } ?>
        <?php if($secondaryInsProviderId>0){?>
        <td class="text-right grand-total"><?php echo numberFormat(array_sum($show_grand_paid_arr[2]),2,'yes');?></td>
        <?php } ?>
        <?php if($tertiaryInsProviderId>0){?>
        <td class="text-right grand-total"><?php echo numberFormat(array_sum($show_grand_paid_arr[3]),2,'yes');?></td>
        <?php } ?>
        <td class="text-right grand-total"><?php echo numberFormat(array_sum($show_grand_paid_arr[0]),2,'yes');?></td>
        <?php if($show_resp_party>0){?>
        <td class="text-right purple_bar"></td>
        <?php } ?>
        <td class="text-right grand-total"></td>
        <td class="text-right grand-total"></td>
        <td class="text-right grand-total"><?php echo numberFormat(array_sum($show_grand_final_paid_arr),2,'yes');?></td>
        <td class="text-right grand-total">
        <?php 
            $show_grand_due_str=array_sum($show_grand_due_arr);
			$ngt_balance=$show_grand_due_str;
        ?>
        <span>
            <?php echo numberFormat($show_grand_due_str,2,'yes');?>
        </span>
        </td>
        <td colspan="7">&nbsp;</td>
    </tr>
<?php }?>  
</table>
<?php if($_REQUEST['encounter_id']>0 && $show_credit==""){?>
    <style>.align_td_top td{vertical-align: top!important;}</style>
    <table class="table table-bordered table-striped">		
        <tr class="grythead">
			<th class="text-nowrap" >Task On Rem. Date</th>
            <th>Int. / Ext.</th>
            <th class="text-nowrap" >Notes Date</th>
            <th class="text-nowrap" >Reminder Date</th>
            <th>Task For</th>
            <th style="width:45%;">Notes</th>
            <th>Done</th>
            <th>Operator</th>
            <th>Function</th>
        </tr>
    <?php
        $getCommentsStr = "SELECT * FROM paymentscomment WHERE patient_id = '$patient_id' AND encounter_id = '$encounter_id' and c_type!='batch' order by encCommentsDate desc,encCommentsTime desc";
        $getCommentsQry = imw_query($getCommentsStr);
        while($getCommentsRows = imw_fetch_array($getCommentsQry)){
            $commentId = $getCommentsRows['commentId'];
            $commentsType = $getCommentsRows['commentsType'];
           // $encCommentsDate = $getCommentsRows['encCommentsDate'];
                //list($commentsYear, $commentsMonth, $commentsDay) = explode("-", $encCommentsDate);
                //$encCommentsDate = $commentsMonth."-".$commentsDay."-".$commentsYear;
                //$encCommentsDate = date('m-d-Y',mktime(0,0,0,$commentsMonth,$commentsDay,$commentsYear));
			if(isset($getCommentsRows['encCommentsTime']) && $getCommentsRows['encCommentsTime']!='00:00:00' && $getCommentsRows['encCommentsTime']!=''){
				$encCommentsDate = $getCommentsRows['encCommentsDate'].' '.$getCommentsRows['encCommentsTime'];
				$encCommentsDate = date('m-d-Y h:i A', strtotime($encCommentsDate));
			}else{
            	$encCommentsDate = get_date_format($getCommentsRows['encCommentsDate']); 
			}
            $encComments = core_extract_user_input($getCommentsRows['encComments']);
            $encCommentsOperatorId = $getCommentsRows['encCommentsOperatorId'];
            //---------------------- GETTING OPERATOR NAME FROM ID ----------------------//
            $operatorName=$usr_alias_name[$encCommentsOperatorId];		
            //---------------------- GETTING OPERATOR NAME FROM ID ----------------------//
            $reminder_date = get_date_format($getCommentsRows['reminder_date']);
            $task_done = $getCommentsRows['task_done'];
            $task_assign_for = $getCommentsRows['task_assign_for'];
            $checked=" ";
            $onchnge=' onChange="task_done('.trim($commentId).');" ';
            $task_done_msg=' ';
            if($task_done=='2') {
                $task_done_msg=' This note marked as done. You are not allowed to change it. ';
                $checked=" checked ";
                $onchnge=' onclick=" return false; " ';
            }
			$taskAssignForUsers = 'Not Assigned';
			$taskFor = explode(',',$getCommentsRows['task_assign_for']);
			$comm_usr_tooltip="";
			if(count($taskFor) > 0 && $getCommentsRows['task_assign_for']!=""){
				$tmpArr = array();
				foreach($taskFor as $task_userId){
					if(isset($usr_full_name[$task_userId]) && isset($usr_full_name[$task_userId])) $tmpArr[] = $usr_full_name[$task_userId];
				}
				$comm_usr_tooltip=show_tooltip(implode('<br>',$tmpArr));
				if(count($tmpArr)>1){$taskAssignForUsers = 'Multi';} else {$taskAssignForUsers = implode(';',$tmpArr);}
			}
			$task_on_reminder = 'no';
			$task_check = ' ';
			if($getCommentsRows['task_onreminder']==1){
				$task_on_reminder = 'yes';
				$task_check = ' checked="checked" ';
			}
    ?>
        <tr class="text-center align_td_top" id="CommentTr<?php echo $commentId; ?>">
			<td id="taskOnReminderTd<?php echo $commentId; ?>">
				<div class="checkbox">
					<input type="checkbox" id="task_on_reminder<?php echo $commentId; ?>" value="<?php echo $task_on_reminder;?>" <?php echo $task_check;?> name="task_on_reminder<?php echo $commentId; ?>" onClick="task_reminder_date();"/>
					<label for="task_on_reminder<?php echo $commentId; ?>">&nbsp;</label>
				</div>
            </td>
            <td id="editType<?php echo $commentId; ?>"><?php echo $commentsType; ?></td>
            <td id="commentDateTd<?php echo $commentId; ?>"><?php echo $encCommentsDate; ?></td>
            <td id="reminder_date<?php echo $commentId; ?>"><?php echo $reminder_date; ?></td>
            <td id="task_for<?php echo $commentId; ?>"><span <?php echo $comm_usr_tooltip; ?>><?php echo $taskAssignForUsers; ?></span></td>
            <td class="text-left" id="commentTd<?php echo $commentId; ?>"><?php echo nl2br($encComments); ?></td>
            <td id="taskdoneTd<?php echo $commentId; ?>">
                <?php if($task_assign_for=='') { ?>
                    N/A
                <?php } else if($task_done=='2') { ?>
                    <img src="../../library/images/confirm.gif" width="16px" />
                <?php } else { ?>
                    <div class="checkbox">
                        <input type="checkbox" title="<?php echo $task_done_msg;?>" <?php echo $onchnge;?> id="taskdone<?php echo $commentId; ?>" value="<?php echo $task_done;?>" <?php echo $checked;?> name="taskdone<?php echo $commentId; ?>"/>
                        <label for="taskdone<?php echo $commentId; ?>">&nbsp;</label>
                    </div>
                <?php } ?>
            </td>
            <td id="operName<?php echo $commentId; ?>"><?php echo $operatorName; ?></td>
            <td>
                <table>
                    <tr>
                        <td id="editTd<?php echo $commentId; ?>"><a href="javascript:void(0);" onClick="editComment('<?php echo $commentId; ?>', '<?php echo $commentsType; ?>','<?php echo $getCommentsRows['task_assign_for']; ?>','<?php echo $getCommentsRows['task_onreminder']; ?>');"><img src="../../library/images/edit.png" alt="Edit" style="border:none;"></a></td>
                        <td>
                            <?php if($_REQUEST['batch_pat_id']>0){?>
                        		<a href="javascript:void(0);" onClick="javascript:del_batch_notes('<?php echo $commentId; ?>', '<?php echo $encounter_id; ?>','comment');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>
                        	<?php }else{ ?>
                        		<a href="javascript:void(0);" onClick="javascript:del_enc_trans('<?php echo $commentId; ?>', '<?php echo $encounter_id; ?>','comment');"><img src="../../library/images/del.png" alt="Del" style="border:none; padding-left:10px;"></a>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    <?php
    }
    ?>
    </table>	
<?php }?>    	
<?php 
$dis_proc_imp=implode(',',$dis_proc_arr);
?>
<script type="text/javascript">
var operator='<?php echo $usr_alias_name[$_SESSION["authId"]]; ?>';
<?php if(count($dis_proc_arr)>0 && $_REQUEST['batch_pat_id']<=0){?>
getDiscount('<?php print $dis_proc_imp; ?>');
<?php } ?>
</script>