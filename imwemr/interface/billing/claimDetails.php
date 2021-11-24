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

?><?php
/*
File: claimDetails.php
Purpose: Patient Claim Paid details.
Access Type: Direct Access
*/
include_once(dirname(__FILE__)."/../../config/globals.php"); 
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php"); 
$SVCId = $_REQUEST['SVCId'];
$idPat = $_REQUEST['idPat'];
$eFileId = $_REQUEST['eFileId'];
$patientArr = $_REQUEST['patientArr'];
if($patientArr){
	$patientArr = explode(",", $patientArr);
}
if($idPat){
	$idPatArr = explode(",",$idPat);
	$idPat = implode("','",$idPatArr);
}
//GETTING FILE DETAILS
	$qry = imw_query("select file_name from electronicfiles_tbl where id='".$eFileId."'");
	$EFileMainFile = imw_fetch_assoc($qry);
//GETTING FILE DETAILS
?>
<html>
<head>
</head>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/interface/themes/default/css_billing.php" type="text/css" media="screen">
<script type="text/javascript">
	window.focus();
	function showPaymentFn(eId){
		if(isNaN(eId)){
			alert('The encounter does not exists!')
		}else{		
			//opener.changeEncounterTab(eId);
			var eFID="";
			var filename = "../accounting/accountingTabs.php";
			var send_url = "ERA_session.php?eId="+eId+"&rd2="+filename+"&front=yes&eFID="+eFID;
			opener.top.core_redirect_to("Accounting", send_url);
		}
	}
</script>
<body class="body_c" topmargin="0" leftmargin="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr bgcolor="#EAF0F7" height="25">
		<td colspan="3" align="left" class="text_10" style="text-decoration:underline;font-size:14px;color:#0000FF;"><b>File Name&nbsp;:&nbsp;</b><?php echo $EFileMainFile['file_name']; ?></td>
	</tr>
	<tr>
		<td colspan="3" align="center" class="text_10b" style="font-size:16px;text-decoration:underline;">View Charges<hr color="#B1C0D6"></td>
	</tr>
</table>
	<?php
	if(!$patientArr){
	//----------------------- GETTING DETAILS
	$getDetailsStr = "SELECT * FROM era_835_details a,
							era_835_patient_details b,
							era_835_proc_details c,
							era_835_nm1_details d
							WHERE a.`electronicFilesTblId` = '$eFileId'
							AND a.`835_Era_Id` = b.`835_Era_Id`
							AND b.ERA_patient_details_id = c.ERA_patient_details_id
							AND b.ERA_patient_details_id in('$idPat')
							AND d.ERA_patient_details_id = b.ERA_patient_details_id
							AND d.NM1_type = 'QC'";
	$getDetailsExistsOrNotQry = imw_query($getDetailsStr);
	$getDetailsQry = imw_query($getDetailsStr);
	$getDetailsExistsOrNotRows = imw_fetch_array($getDetailsExistsOrNotQry);
		$chk_issue_EFT_Effective_date = $getDetailsExistsOrNotRows['chk_issue_EFT_Effective_date'];
		$provider_payment_amount = $getDetailsExistsOrNotRows['provider_payment_amount'];
			list($dateYY, $dateMM, $dateDD) = explode("-", $chk_issue_EFT_Effective_date);
			$chk_issue_EFT_Effective_date = $dateMM."-".$dateDD."-".$dateYY;
		$pId = $getDetailsExistsOrNotRows['CLP_claim_submitter_id'];
		$TRN_payment_type_number = $getDetailsExistsOrNotRows['TRN_payment_type_number'];
		$N1_payer_name = $getDetailsExistsOrNotRows['N1_payer_name'];
		$N3_payer_address = $getDetailsExistsOrNotRows['N3_payer_address'];
		$N4_payer_city = $getDetailsExistsOrNotRows['N4_payer_city'];
		$N4_payer_state = $getDetailsExistsOrNotRows['N4_payer_state'];
		$N4_payer_zip = $getDetailsExistsOrNotRows['N4_payer_zip'];
		$N1_payee_name = $getDetailsExistsOrNotRows['N1_payee_name'];
		$N1_payee_id = $getDetailsExistsOrNotRows['N1_payee_id'];
		$N3_payee_address = $getDetailsExistsOrNotRows['N3_payee_address'];
		$N4_payee_city = $getDetailsExistsOrNotRows['N4_payee_city'];
		$N4_payee_state = $getDetailsExistsOrNotRows['N4_payee_state'];
		$N4_payee_zip = $getDetailsExistsOrNotRows['N4_payee_zip'];
		$REF_payee_add_info = $getDetailsExistsOrNotRows['REF_payee_add_info'];
		$REFPayeeAddInfo = explode(",", $REF_payee_add_info);
		$NM1_last_name = stripslashes($getDetailsExistsOrNotRows['NM1_last_name']);
		$NM1_first_name = stripslashes($getDetailsExistsOrNotRows['NM1_first_name']);
		$patientName = $NM1_last_name.', '.$NM1_first_name;
		$icn = $getDetailsExistsOrNotRows['CLP_payer_claim_control_number'];
		$HIC = $getDetailsExistsOrNotRows['NM1_patient_id'];
		$PER_payer_office_name = $getDetailsExistsOrNotRows['PER_payer_office_name'];
		$CLP_claim_payment_amount =  $getDetailsExistsOrNotRows['CLP_claim_payment_amount'];
		$Era_Id835 = $getDetailsExistsOrNotRows['835_Era_Id'];
		?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">		
		<?php
		if($N1_payer_name){
			?>
			<tr>		
				<td width="272" align="left" class="text_10" style="padding-left:10px;">
					<b><?php echo $N1_payer_name; ?></b><br><?php echo $N3_payer_address; ?><br><?php echo $N4_payer_city.', '.$N4_payer_state.' '.$N4_payer_zip; ?>
					
				</td>
				<td width="62">&nbsp;</td>
				<td colspan="2" align="left" valign="top" class="text_10b">MEDICAL REMITTANCE NOTICE</td>
				<td width="30" align="left"><img style="cursor:pointer;" src="../../images/cancel.gif" title="Close" onClick="window.close();"></td>
				<td width="55" align="left"><img style="cursor:pointer;" src="../../images/printer.gif" title="Print" onClick="window.print();"></td>
			</tr>
			<?php
		}
		?>
		<tr height="2">
			<td colspan="6"></td>
		</tr>
		<?php		
		//if($PER_payer_office_name){
			//$PER_payer_office_name = str_replace(',', '', $PER_payer_office_name);
			?>
			<tr>
				<td rowspan="4" align="left" class="text_10" style="padding-left:10px;">
					<?php echo '<b>'.$N3_payee_address.'</b><br>'.$N4_payee_city.', '.$N4_payee_state.' '.$N4_payee_zip; ?>
				</td>
				<td width="62">&nbsp;</td>
				<td width="103" align="left" class="text_10b">Provider #:</td>
				<td width="107" align="left" class="text_10"><?php echo $REFPayeeAddInfo[1]; ?></td>
				<td align="left" class="text_10">&nbsp;</td>
				<td align="left" class="text_10">&nbsp;</td>
			</tr>
			<?php
		//}
		?>
		<tr>
			<td width="62">&nbsp;</td>
			<td align="left" class="text_10b">Date:</td>
			<td align="left" class="text_10"><?php echo $chk_issue_EFT_Effective_date; ?></td>
		    <td align="left" class="text_10">&nbsp;</td>
		    <td align="left" class="text_10">&nbsp;</td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td align="left" class="text_10b">Amount:</td>
		  <td align="left" class="text_10"><?php echo $provider_payment_amount; ?></td>
		  <td align="left" class="text_10">&nbsp;</td>
		  <td align="left" class="text_10">&nbsp;</td>
		  </tr>
		<tr>
			<td width="62">&nbsp;</td>
			<td align="left" class="text_10b">Check / EFT #:</td>
			<td align="left" class="text_10"><?php echo $TRN_payment_type_number; ?></td>
		    <td align="left" class="text_10">&nbsp;</td>
		    <td align="left" class="text_10">&nbsp;</td>
		</tr>
		<tr height="20">
			<td colspan="6" align="left">
				<table class="table_collapse cellBorder3" bgcolor="#EAF0F7">
					<?php
					unset($CAS_typeArr);
					unset($CAS_reason_codeArr);
					unset($CAS_amtArr);
					$totalDeductAmount = 0;
					while($getDetailsRows = imw_fetch_array($getDetailsQry)){
						$MOA_qualifier = $getDetailsRows['MOA_qualifier'];
						$method_of_code_stu = $getDetailsRows['method_of_code_stu'];
						$Interchange_sender_id = $getDetailsRows['Interchange_sender_id'];
						$id_qualifier = $getDetailsRows['id_qualifier'];
						$submitter_id = $getDetailsRows['submitter_id'];
						$interchange_date_time = $getDetailsRows['interchange_date_time'];
						$standard_Identifier_control_code = $getDetailsRows['standard_Identifier_control_code'];
						$version_number_of_segments = $getDetailsRows['version_number_of_segments'];
						$interchange_sender_no = $getDetailsRows['interchange_sender_no'];
						$interchange_sender_control_no = $getDetailsRows['interchange_sender_control_no'];
						$usage_indicator = $getDetailsRows['usage_indicator'];
						$receiver_code = $getDetailsRows['receiver_code'];
						$submitter_code = $getDetailsRows['submitter_code'];
						$batch_date_time = $getDetailsRows['batch_date_time'];
						$unique_control_identifier = $getDetailsRows['unique_control_identifier'];
						$X12_standard = $getDetailsRows['X12_standard'];
						$Identifying_control_number835 = $getDetailsRows['835_Identifying_control_number'];
						$BPR_segment = $getDetailsRows['BPR_segment'];
						$action_code = $getDetailsRows['action_code'];
						$provider_payment_amount = $getDetailsRows['provider_payment_amount'];
						$flag_code = $getDetailsRows['flag_code'];
						$payment_method_code = $getDetailsRows['payment_method_code'];
						$sender_bank_acc = $getDetailsRows['sender_bank_acc'];
						$TRN_trace_numbers = $getDetailsRows['TRN_trace_numbers'];	
						$TRN_orignating_company_id = $getDetailsRows['TRN_orignating_company_id'];
						$REF_receiver_reference_id = $getDetailsRows['REF_receiver_reference_id'];
						$DTM_production_date = $getDetailsRows['DTM_production_date'];
						$REF_provider_ref_id = $getDetailsRows['REF_provider_ref_id'];
						$contact_fn_code = $getDetailsRows['contact_fn_code'];
						$PER_payer_office_name = $getDetailsRows['PER_payer_office_name'];
						$PER_payer_office_phone = $getDetailsRows['PER_payer_office_phone'];
						//------------- PROC. DETAILS
						//$Era835_proc_Id = $getDetailsRows['835_Era_proc_Id'];
						$SVC_proc_code = $getDetailsRows['SVC_proc_code'];
						$SVC_mod_code = $getDetailsRows['SVC_mod_code'];
						if(!$SVC_mod_code){ $SVC_mod_code = 'N/A'; }
						$unit = $getDetailsRows['unit'];
						$SVC_proc_charge = $getDetailsRows['SVC_proc_charge'];
							$totalBilled = $totalBilled + $SVC_proc_charge;
						$SVC_provider_pay_amt = $getDetailsRows['SVC_provider_pay_amt'];
						$units_service_paid = $getDetailsRows['units_service_paid'];
						$SVC_proc_unit = $getDetailsRows['SVC_proc_unit'];
						$DTM_date = $getDetailsRows['DTM_date'];
						$CAS_amt = $getDetailsRows['CAS_amt'];
						$CAS_type = $getDetailsRows['CAS_type'];
						$CAS_reason_code = $getDetailsRows['CAS_reason_code'];
						$rem_code = $getDetailsRows['rem_code'];
						$CASTypeCode = '';
						//DEDUCT AMOUNT
						$deductAmount = '0.00';
						$pt_co_ins = '0.00';
						if(strpos($CAS_type, ",")){
							$CAS_typeArr = explode(",",$CAS_type);
							$CAS_reason_codeArr = explode(",",$CAS_reason_code);
							$CAS_amtArr = explode(",",$CAS_amt);
							foreach($CAS_typeArr as $k => $val){
								if((trim($CAS_typeArr[$k]) == 'PR') && (trim($CAS_reason_codeArr[$k]) == '1')){
									$deductAmount = $CAS_amtArr[$k];
								}
								if((trim($CAS_typeArr[$k]) == 'PR') && (trim($CAS_reason_codeArr[$k]) == '2')){
									$pt_co_ins = $CAS_amtArr[$k];
								}
								if((trim($CAS_typeArr[$k]) == 'PR') && (trim($CAS_reason_codeArr[$k]) == '3')){
									$pt_co_ins = $CAS_amtArr[$k];
								}
							}
							foreach($CAS_typeArr as $key => $valueType){
								if($CASTypeCode == ''){
									$CASTypeCode = $CAS_typeArr[$key].' '.$CAS_reason_codeArr[$key];
								}else{
									$CASTypeCode = $CASTypeCode.', '.$CAS_typeArr[$key].' '.$CAS_reason_codeArr[$key];
								}
							}
						}else{
							if((trim($CAS_type) == 'PR') && (trim($CAS_reason_code) == '1')){
								$deductAmount = $CAS_amt;
							}
							if((trim($CAS_type) == 'PR') && (trim($CAS_reason_code) == '2')){
								$pt_co_ins = $CAS_amt;
							}
							if((trim($CAS_type) == 'PR') && (trim($CAS_reason_code) == '3')){
								$pt_co_ins = $CAS_amt;
							}
							$CASTypeCode = $CAS_type.' '.$CAS_reason_code;
						}
						
						//DEDUCT AMOUNT
						$totalDeductAmount = $totalDeductAmount + $deductAmount;

						if(strpos($CAS_amt, ", ")){
							$CAS_amtArr = explode(", ", $CAS_amt);
							/*$newBalanceShow = '$'.number_format($CAS_amtArr[0], 2).', $'.number_format($CAS_amtArr[1], 2);
							$newBalance = $CAS_amtArr[0] + $CAS_amtArr[1];
							if($CAS_amtArr[2]!=""){
								$newBalanceShow = '$'.number_format($CAS_amtArr[0], 2).', $'.number_format($CAS_amtArr[1], 2).', $'.number_format($CAS_amtArr[2], 2);
								$newBalance = $CAS_amtArr[0] + $CAS_amtArr[1] + $CAS_amtArr[2];
							}*/
							$newBalanceShow=0;
							$newBalance=0;
							foreach($CAS_amtArr as $key => $valueType){
								if($newBalanceShow == ''){
									$newBalanceShow = '$'.number_format($CAS_amtArr[$key],2);
									//$newBalance = $newBalance+$CAS_amtArr[$key];
								}else{
									$newBalanceShow = $newBalanceShow.', $'.number_format($CAS_amtArr[$key],2);;
									//$newBalance = $newBalance+$CAS_amtArr[$key];
								}
							}
							//$newBalance = number_format($newBalance, 2);
							$approvedAmount = $SVC_proc_charge - $adjustAmt;
						}else{
							//$newBalance = number_format($CAS_amt, 2);
							$newBalanceShow = '$'.number_format($CAS_amt, 2);
							$approvedAmount = 0;
						}
						$ERA_patient_details_id = $getDetailsRows['ERA_patient_details_id'];
						$getClpCasStr = "SELECT * FROM era835clpcas WHERE 
										era835Id = '$Era_Id835'
										AND ERAPatientdetailsId = '$ERA_patient_details_id'";
						$getClpCasQry = imw_query($getClpCasStr);
						if(imw_num_rows($getClpCasQry)>0){
							while($getClpCasRows = imw_fetch_array($getClpCasQry)){
								$enc_level_adj_arr[$getClpCasRows['clpCasId']] = $getClpCasRows['casReasonAmt'];
							}
						}
						$chk_cont_proc_num=0;
						if(imw_num_rows($getClpCasQry)){
							$getDetailsOfStr_num = imw_query("SELECT * FROM era_835_proc_details
							WHERE `ERA_patient_details_id` = '$ERA_patient_details_id'");
							$chk_cont_proc_num=imw_num_rows($getDetailsOfStr_num);
						}
						if($chk_cont_proc_num==1){
							$SVC_provider_pay_amt=$CLP_claim_payment_amount;
						}
						$newBalance = $SVC_proc_charge - $deductAmount - $SVC_provider_pay_amt;
						$newBalance = number_format($newBalance, 2);
						$totalApprovedAmount = $totalApprovedAmount + $approvedAmount;
						$totalAdjustAmt = $totalAdjustAmt + $adjustAmt;
						$totalPaid = $totalPaid + $SVC_provider_pay_amt;
						$totalDue = $totalDue + str_replace(',','',$newBalance);
						
						$AMT_amount = $getDetailsRows['AMT_amount'];
						//------------- PROC. DETAILS
						$REF_prov_identifier = $getDetailsRows['REF_prov_identifier'];
							$REFProvId = explode(",", $REF_prov_identifier);							
						$serviceDate = $getDetailsRows['DTM_date'];
						if($getDetailsRows['DTM_type']==''){
							$getDTMProvStr = "SELECT DTM_qualifier,DTM_date FROM era_835_patient_details WHERE ERA_patient_details_id = '$ERA_patient_details_id' and DTM_qualifier='232'";
							$getDTMProvQry = imw_query($getDTMProvStr);
							if(imw_num_rows($getDTMProvQry)>0){
								$getDTMProvRow = imw_fetch_array($getDTMProvQry);
								$DTM_type = $getDTMProvRow['DTM_qualifier'];
								$serviceDate = $getDTMProvRow['DTM_date'];
							}	
						}
							list($DTMyy, $DTMmm, $DTMdd) = explode("-", $serviceDate);
							$serviceDate = $DTMmm.'-'.$DTMdd.'-'.$DTMyy;
						// GETTING RENDPROV
						
						$CLP_claim_status = $getDetailsRows['CLP_claim_status'];
						if($CLP_claim_status == 1){
							$processedBy = 'Primary Insurance';
						}else if($CLP_claim_status == 2){
							$processedBy = 'Secondary Insurance';
						}else if($CLP_claim_status == 3){
							$processedBy = 'Tertiary Insurance';
						}else if($CLP_claim_status == 4){
							$processedBy = 'Denied';
						}else{
							$processedBy = 'No Processed By information exists.';
						}
							
							$getRenProvStr = "SELECT NM1_patient_id FROM era_835_nm1_details
												WHERE ERA_patient_details_id = '$ERA_patient_details_id'
												AND NM1_type = '82'";
							$getRenProvQry = imw_query($getRenProvStr);
							$getRenProvRow = imw_fetch_array($getRenProvQry);
								$RENDPROV = $getRenProvRow['NM1_patient_id'];
						// GETTING RENDPROV
						
						//CAS REASON CODE
							$CAS_type = $getDetailsRows['CAS_type'];
							$CAS_reason_code = $CAS_type.' '.$getDetailsRows['CAS_reason_code'];
						//CAS REASON CODE
						
						//ENCOUNTER
						$REF_prov_identifier = $getDetailsRows['REF_prov_identifier'];
						$REF_prov_identifier = @preg_replace('/\s+/','',$REF_prov_identifier);
						if(isset($billing_global_tsuc_separator)){
							$tsucPos = strpos($REF_prov_identifier, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator);
						}else{
							$tsucPos = strpos($REF_prov_identifier, '_TSUC_');
						}
						if($tsucPos){
							$tsucId = $tsucPos+6;
						}else{
							$tsucPos = strpos($REF_prov_identifier, 'TSUC');
							if($tsucPos)
								$tsucId = $tsucPos+4;
						}
						if($tsucPos){												
							$tsuc_identifier = substr($REF_prov_identifier, $tsucId);	
						}
						$encounter_id = substr($REF_prov_identifier, 0, strpos($REF_prov_identifier, "MCR"));
						//ENCOUNTER
						
						if($SVC_proc_code == '92015'){
							//$AMT_amount = 0;
						}
						$totalAllowedAmt = $totalAllowedAmt + $AMT_amount;	
						$total_pt_co_ins = $total_pt_co_ins + $pt_co_ins;					
						if($ERA_patient_details_id!=$showHeader){
							if(count($idPatArr)>1){
							?>
                            <tr height="10" bgcolor="#ffffff"><td colspan="13" align="left" class="text_10"></td></tr>
                            <?php } ?>
							<tr height="25" bgcolor="#B1C0D6">
								<td colspan="13" align="left">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="27%" align="left" class="text_10">&nbsp;<b>Patient:</b>&nbsp;&nbsp;&nbsp;<?php echo $patientName; ?></td>
											<td width="14%" align="left" class="text_10">&nbsp;<b>HIC #:</b>&nbsp;<?php echo $HIC; ?></td>
											<td width="23%" align="left" class="text_10" nowrap>&nbsp;<b>ICN #:</b>&nbsp;<?php echo $icn; ?></td>
											<td width="22%" align="left" class="text_10">&nbsp;<b>Account #:</b>&nbsp;<?php echo $pId; ?></td>
											<td width="15%" align="left" class="text_10">&nbsp;<b>Reason : </b><?php echo $MOA_qualifier; ?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr height="20" bgcolor="#B1C0D6">
								<td colspan="13" align="left" class="text_10"><b>&nbsp;Processed By : </b><?php echo $processedBy; ?></td>
							</tr>
							<tr height="25" bgcolor="#B1C0D6">
								<td width="80" align="center" style="text-align:center;" class="text_10b">REND&nbsp;PROV</td>
								<td width="75" align="center" style="text-align:center;" class="text_10b">Sevice&nbsp;Date</td>
								<td width="30" align="center" style="text-align:center;" class="text_10b">POS</td>
								<td width="60" align="center" style="text-align:center;" class="text_10b">Proc&nbsp;Code</td>
								<td width="55" align="center" style="text-align:center;" class="text_10b">Modifiers</td>
								<td width="61" align="center" style="text-align:center;" class="text_10b">Charges</td>
								<td width="51" align="center" style="text-align:center;" class="text_10b">Allowed</td>
								<td width="44" align="center" style="text-align:center;" class="text_10b">Deduct</td>
								<td width="50" align="center" style="text-align:center;" class="text_10b">Paid</td>
                                <td width="70" align="center" style="text-align:center;" class="text_10b">Pt Res</td>
								<td width="48" align="center"  style="text-align:center;"class="text_10b">Amount</td>
								<td align="center" class="text_10b" style="width:auto; text-align:center;">CAS</td>
                                <td width="48" align="center" class="text_10b" style="text-align:center;">Rem&nbsp;Code</td>
							</tr>
							<?php
							$showHeader = $ERA_patient_details_id;
						}
						$tot_paid_amt_arr[]=$SVC_provider_pay_amt;
						//GET SECONDARY OR NOT
						$getSecondaryStr = "SELECT NM1_last_name FROM era_835_nm1_details
											WHERE ERA_patient_details_id = '$ERA_patient_details_id'
											AND NM1_type = 'TT'";
						$getSecondaryQry = imw_query($getSecondaryStr);
						$getSecondaryRow = imw_fetch_array($getSecondaryQry);
							$secondaryInsName = stripslashes($getSecondaryRow['NM1_last_name']);
						if(strlen($REFProvId[1])>5)
							$REFProvId[1] = substr($REFProvId[1], 0, 4).'...';
						?>						
						<tr height="20">
							<td align="center" class="text_10"><?php echo $RENDPROV; ?></td>
							<td align="center" class="text_10">
								<a class="text_10" href="javascript:showPaymentFn('<?php echo $encounter_id; ?>');">
									<?php echo $serviceDate; ?>
								</a>
							</td>
							<td align="center" class="text_10"><?php if($REFProvId[1]) echo $REFProvId[1]; else echo 'N/A'; ?></td>
							<td align="center" class="text_10"><?php echo $SVC_proc_code; ?></td>
							<td align="center" class="text_10"><?php echo $SVC_mod_code; ?></td>
							<td align="right" class="text_10" style="padding-right:2px;text-align:right;"><?php if($SVC_proc_charge) echo '$'.number_format($SVC_proc_charge, 2); else echo '-'; ?></td>
							<td align="right" class="text_10" style="padding-right:2px;text-align:right;"><?php if($AMT_amount) echo '$'.number_format($AMT_amount, 2); else echo '-'; ?></td>
							<td align="right" class="text_10" style="padding-right:2px;text-align:right;"><?php if($deductAmount) echo '$'.number_format($deductAmount, 2); else echo '-'; ?></td>
							<td align="right" class="text_10" style="text-align:right;"><?php if($SVC_provider_pay_amt) echo '$'.number_format($SVC_provider_pay_amt, 2); else echo '-'; ?></td>
							<td align="right" class="text_10" style="padding-right:2px;text-align:right;"><?php if($pt_co_ins) echo '$'.number_format($pt_co_ins,2); else echo '-'; ?></td>
                            <td align="right" class="text_10" style="padding-right:2px;"><?php if($newBalanceShow) echo $newBalanceShow; else echo '-'; ?></td>
						    <td align="right" class="text_10" style="padding-left:2px;" nowrap><?php echo $CASTypeCode; //$CAS_reason_code; ?></td>
                            <td align="right" class="text_10" style="padding-left:2px;"><?php echo $rem_code; ?></td>
						</tr>
						<?php
					}
					?>
                   
                     <?php if(count($enc_level_adj_arr)>0){?>
                     <tr height="25">
						<td colspan="4" align="left" class="text_10b"><?php if($secondaryInsName) echo $secondaryInsName; else echo '&nbsp;'; ?></td>
						<td align="right" class="text_10b" style="padding-right:5px; text-align:right;">Sub Total:</td>
						<td align="right" class="text_10b" style="padding-right:2px;text-align:right;"><?php echo '$'.number_format($totalBilled, 2); ?></td>
						<td align="right" class="text_10b" style="padding-right:2px;text-align:right;"><?php echo '$'.number_format($totalAllowedAmt, 2); ?></td>
						<td align="right" class="text_10b" style="padding-right:2px;text-align:right;"><?php echo '$'.number_format($totalDeductAmount, 2); //$totalAdjustAmt?></td>
                        <td align="right" class="text_10b" style="text-align:right;"><?php echo '$'.number_format(array_sum($tot_paid_amt_arr), 2); ?></td>
                        <td align="right" class="text_10b" style="padding-right:2px;text-align:right;"><?php echo '$'.number_format($total_pt_co_ins, 2); ?></td>
                        <td align="right" class="text_10b" style="padding-right:2px;">&nbsp;<?php //echo '$'.number_format($totalDue, 2); ?></td>
					    <td align="right" class="text_10b" style="padding-right:2px;">&nbsp;</td>
                        <td align="right" class="text_10b" style="padding-right:2px;">&nbsp;</td>
					</tr>
                    <tr height="20">
                        <td align="right" style="padding-right:5px; text-align:right;" colspan="5" class="text_10b">Encounters Level Adjustment Amount:</td>
                        <td style="text-align:right;" class="text_10b"></td>
                        <td style="text-align:right;" class="text_10b"></td>
                        <td style="text-align:right;" class="text_10b"></td>
                        <td style="text-align:right;" class="text_10b"><?php echo numberFormat(trim(array_sum($enc_level_adj_arr)),2,'yes'); ?></td>
                        <td></td>
                    </tr>
                    <?php } ?>
					<tr height="25">
						<td colspan="4" align="left" class="text_10b"><?php if($secondaryInsName) echo $secondaryInsName; else echo '&nbsp;'; ?></td>
						<td align="right" class="text_10b" style="padding-right:5px; text-align:right;">Grand Total:</td>
						<td align="right" class="text_10b" style="padding-right:2px;text-align:right;"><?php echo '$'.number_format($totalBilled, 2); ?></td>
						<td align="right" class="text_10b" style="padding-right:2px;text-align:right;"><?php echo '$'.number_format($totalAllowedAmt, 2); ?></td>
						<td align="right" class="text_10b" style="padding-right:2px;text-align:right;"><?php echo '$'.number_format($totalDeductAmount, 2); //$totalAdjustAmt?></td>
                        <td align="right" class="text_10b" style="text-align:right;">
                        <?php
							$grand_CLP_claim_payment_amount=array_sum($tot_paid_amt_arr)-array_sum($enc_level_adj_arr);
						?>
						<?php echo numberFormat($grand_CLP_claim_payment_amount,2,'yes'); ?>
                        </td>
                        <td align="right" class="text_10b" style="padding-right:2px;text-align:right;"><?php echo '$'.number_format($total_pt_co_ins, 2); ?></td>
                        <td align="right" class="text_10b" style="padding-right:2px;">&nbsp;<?php //echo '$'.number_format($totalDue, 2); ?></td>
					    <td align="right" class="text_10b" style="padding-right:2px;">&nbsp;</td>
                        <td align="right" class="text_10b" style="padding-right:2px;">&nbsp;</td>
					</tr>
			  </table>
			</td>
		</tr>
		</table>
		<?php
		}else{
			?>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<?php
			foreach($patientArr as $key => $patientAccId){
				$totalAllowedAmt = 0;
				if($repeatPatient == $patientAccId){
					continue;
				}				
				//echo $eFileId;
				$getAccDetailsStr = "SELECT *
									FROM `era_835_patient_details` a,
									era_835_details b
									WHERE a.`CLP_claim_submitter_id` = '$patientAccId'
									AND a.`835_Era_Id` = b.`835_Era_Id`
									and b.electronicFilesTblId='$eFileId'";
				$getAccDetailsQry = imw_query($getAccDetailsStr);
				while($getAccDetailsRows = imw_fetch_assoc($getAccDetailsQry)){
					$repeatPatient = $patientAccId;
					$Era_Id835 = $getAccDetailsRows['835_Era_Id'];
					extract($getAccDetailsRows);					
					$REFPayeeAddInfo = explode(",", $REF_payee_add_info);
					list($dateYY, $dateMM, $dateDD) = explode("-", $chk_issue_EFT_Effective_date);
					$chk_issue_EFT_Effective_date = $dateMM."-".$dateDD."-".$dateYY;
					//if($tempPayer!=$N1_payer_name)				
					++$noHead;
					if($noHead<2){	
						?>
						<tr>
							<td width="296" align="left" class="text_10"><?php echo '<b>'.$N1_payer_name.'</b><br>'.$N3_payer_address.'<br>'.$N4_payer_city.', '.$N4_payer_state.' '.$N4_payer_zip; ?></td>
							<td></td>
							<td width="233" valign="top" align="left" class="text_10b"><?php echo "MEDICAL REMITTANCE NOTICE"; ?></td>
						    <td width="97" valign="top" align="right" class="text_10b">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><img style="cursor:pointer;" src="../../images/cancel.gif" title="Close" onClick="window.close();"></td>
										<td width="5"></td>
										<td><img style="cursor:pointer;" src="../../images/printer.gif" title="Print" onClick="window.print();"></td>
									</tr>
								</table>								
							</td>
						</tr>
						<tr>
							<td height="25" colspan="4" align="right"></td>
						</tr>
						<tr>
							<td align="left" class="text_10"><?php echo '<b>'.$N3_payee_address.'</b><br>'.$N4_payee_city.', '.$N4_payee_state.' '.$N4_payee_zip; ?></td>
							<td width="46"></td>
							<td colspan="2" align="left" valign="top" class="text_10"><b>Provider #:</b><?php echo $REFPayeeAddInfo[1].'<br><b>Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>'.$chk_issue_EFT_Effective_date; ?></td>
						</tr>
						<tr>
							<td height="15" colspan="4"></td>
						</tr>
						<?php
						$tempPayer=$N1_payer_name;
					}
				}
				?>
				<tr>
					<td height="25" colspan="4">
						<table class="table_collapse cellBorder3" bgcolor="#EAF0F7">
							<?php
							$getNamePatStr = "SELECT a.NM1_last_name,a.NM1_first_name,a.NM1_patient_id,
												b.CLP_payer_claim_control_number
												FROM era_835_nm1_details a,era_835_patient_details b
												WHERE a.ERA_patient_details_id = '$ERA_patient_details_id'
												and a.ERA_patient_details_id = b.ERA_patient_details_id
												AND a.NM1_type = 'QC'";
							$getNamePatQry = imw_query($getNamePatStr);
							$getNamePatRow = imw_fetch_array($getNamePatQry);
								$NM1_patient_id = $getNamePatRow['NM1_patient_id'];
								$NM1_last_name = stripslashes($getNamePatRow['NM1_last_name']);
								$NM1_first_name = stripslashes($getNamePatRow['NM1_first_name']);
								$icn = $getNamePatRow['CLP_payer_claim_control_number'];
							?>
							<tr height="25" bgcolor="#B1C0D6" valign="middle"><!--DFB6A4 FEDC8B-->
								<td colspan="13" align="left">
									<table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="320" align="left" class="text_10" style="padding-left:10px;"><b>Patient:</b> <?php echo $NM1_last_name.", ".$NM1_first_name; ?></td>
											<td width="193" align="left" class="text_10" style="padding-left:10px;"><b>HIC #:</b> <?php echo $NM1_patient_id; ?></td>
											<td width="233" align="left" class="text_10"><b>ICN #:</b> <?php echo $icn; ?></td>
											<td width="202" align="left" class="text_10" style="padding-left:5px;"><b>Account #:</b> <?php echo $patientAccId; ?></td>
											<td width="311" align="left" class="text_10"><b>Reason : </b><?php echo $MOA_qualifier; ?></td>
										</tr>
								  </table>
								</td>
							</tr>					
							<tr height="20" bgcolor="#B1C0D6"><!---->
								<td width="75" align="center" class="text_10b">Rend Prov</td>
								<td width="93" align="center" class="text_10b">Sevice Date</td>
								<td width="30" align="center" class="text_10b">POS</td>
								<td width="68" align="center" class="text_10b">Proc&nbsp;Code</td>
								<td width="58" align="center" class="text_10b">Modifiers</td>
								<td width="60" align="center" class="text_10b">Charges</td>
								<td width="60" align="center" class="text_10b">Allowed</td>
								<td width="44" align="center" class="text_10b">Deduct</td>
								<td width="37" align="center" class="text_10b">Paid</td>
                                <td width="70" align="center" class="text_10b">Pt Res</td>
								<td width="48" align="center" class="text_10b">Amount</td>
							    <td width="65" align="center" class="text_10b">CAS</td>
                                <td width="65" align="center" class="text_10b">Rem Code</td>
							</tr>
							<?php
							$totalPatCharges = 0;
							$totalApprovedAmount = 0;
							$totalDeductAmount = 0;
							$totalPayAmt = 0;
							$totalNewBalance = 0;
							
							//GET PATIENT DETAILS
							$getClaimDeailsStr = "SELECT * FROM 
												era_835_details a,
												era_835_patient_details b,
												era_835_nm1_details c,
												era_835_proc_details d
												WHERE a.electronicFilesTblId = '$eFileId'
												AND b.`835_Era_Id` = a.`835_Era_Id`
												AND b.ERA_patient_details_id = c.ERA_patient_details_id
												AND c.NM1_type = 'QC'
												AND d.`835_Era_Id` = a.`835_Era_Id`
												AND b.ERA_patient_details_id = d.ERA_patient_details_id
												AND b.CLP_claim_submitter_id = '$patientAccId'";
							$getClaimDeailsQry = imw_query($getClaimDeailsStr);
							if(imw_num_rows($getClaimDeailsQry)>0){
								while($getClaimDeailsRows = imw_fetch_assoc($getClaimDeailsQry)){
									$ERAPatientDetailsId = $getClaimDeailsRows['ERA_patient_details_id'];
										//GET SECONDARY INS NAME
										$getSecondaryStr = "SELECT NM1_last_name FROM era_835_nm1_details
															WHERE ERA_patient_details_id = '$ERAPatientDetailsId'
															AND NM1_type = 'TT'";
										$getSecondaryQry = imw_query($getSecondaryStr);
										$getSecondaryRow = imw_fetch_array($getSecondaryQry);
											$secondaryInsName = stripslashes($getSecondaryRow['NM1_last_name']);
										//GET SECONDARY INS NAME
									$CAS_reason_code =  @preg_replace('/\s+/','', $getClaimDeailsRows['CAS_reason_code']);
									$CAS_type = @preg_replace('/\s+/','', $getClaimDeailsRows['CAS_type']);
										$CASReasonCode = $CAS_type.' - '.$CAS_reason_code;
										
									$Era_proc_Id835 = $getClaimDeailsRows['835_Era_proc_Id'];
									$DTM_date = $getClaimDeailsRows['DTM_date'];
									
									if($getClaimDeailsRows['DTM_type']==''){
										$getDTMProvStr = "SELECT DTM_qualifier,DTM_date FROM era_835_patient_details WHERE ERA_patient_details_id = '$ERAPatientDetailsId' and DTM_qualifier='232'";
										$getDTMProvQry = imw_query($getDTMProvStr);
										if(imw_num_rows($getDTMProvQry)>0){
											$getDTMProvRow = imw_fetch_array($getDTMProvQry);
											$DTM_type = $getDTMProvRow['DTM_qualifier'];
											$DTM_date = $getDTMProvRow['DTM_date'];
										}
									}
										list($DTM_dateYY, $DTM_dateMM, $DTM_dateDD) = explode("-", $DTM_date);
										$DTM_date = $DTM_dateMM.'-'.$DTM_dateDD.'-'.$DTM_dateYY;
									
									$SVC_proc_charge = $getClaimDeailsRows['SVC_proc_charge'];
									$SVC_provider_pay_amt = $getClaimDeailsRows['SVC_provider_pay_amt'];
									$REF_prov_identifier = $getClaimDeailsRows['REF_prov_identifier'];
									
									$encounter_id = substr($REF_prov_identifier, 0, strpos($REF_prov_identifier, "MCR"));
									
									$REFProvId = explode(",", $REF_prov_identifier);
									$REFProvIdLen = strlen($REFProvId[1]);
									if($REFProvIdLen>5){
										$REF_prov_identifier = substr($REFProvId[1], 0, 5).'...';
									}
									$SVC_proc_code = $getClaimDeailsRows['SVC_proc_code'];
									$SVC_mod_code = $getClaimDeailsRows['SVC_mod_code'];
									if(!$SVC_mod_code){ $SVC_mod_code = 'N/A'; }
									$AMT_amount = $getClaimDeailsRows['AMT_amount'];
									$CAS_amt = $getClaimDeailsRows['CAS_amt'];
									$CAS_type = $getClaimDeailsRows['CAS_type'];
									$CAS_reason_code = $getClaimDeailsRows['CAS_reason_code'];
									$rem_code = $getClaimDeailsRows['rem_code'];
									
									$REF_prov_identifier = $getClaimDeailsRows['REF_prov_identifier'];
									$REF_prov_identifier = @preg_replace('/\s+/','',$REF_prov_identifier);
									if(isset($billing_global_tsuc_separator)){
										$tsucPos=strpos($REF_prov_identifier, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator);	
									}else{
										$tsucPos=strpos($REF_prov_identifier, '_TSUC_');
									}
									if($tsucPos){
										$tsucId = $tsucPos+6;
									}else{
										$tsucPos = strpos($REF_prov_identifier, 'TSUC');
										if($tsucPos)
											$tsucId = $tsucPos+4;
									}
									if($tsucPos){												
										$tsuc_identifier = substr($REF_prov_identifier, $tsucId);	
									}
									$icn = $getPatientDetailsRows['CLP_payer_claim_control_number'];
									//DEDUCT AMOUNT
									$CASTypeCode = '';
									$deductAmount = '0.00';
									$pt_co_ins = '0.00';
									if(strpos($CAS_type, ",")){
										$CAS_typeArr = explode(",",$CAS_type);
										$CAS_reason_codeArr = explode(",",$CAS_reason_code);			
										$CAS_amtArr = explode(",",$CAS_amt);
										foreach($CAS_typeArr as $k => $val){
											if((trim($CAS_typeArr[$k]) == 'PR') && (trim($CAS_reason_codeArr[$k]) == '1')){
												$deductAmount = $CAS_amtArr[$k];
											}
											if((trim($CAS_typeArr[$k]) == 'PR') && (trim($CAS_reason_codeArr[$k]) == '2')){
												$pt_co_ins = $CAS_amtArr[$k];
											}
										}
										foreach($CAS_typeArr as $key => $valueType){
											if($CASTypeCode == ''){
												$CASTypeCode = $CAS_typeArr[$key].' '.$CAS_reason_codeArr[$key];
											}else{
												$CASTypeCode = $CASTypeCode.', '.$CAS_typeArr[$key].' '.$CAS_reason_codeArr[$key];
											}
										}
									}else{
										if((trim($CAS_type) == 'PR') && (trim($CAS_reason_code) == '1')){
											$deductAmount = $CAS_amt;
										}
										
										if((trim($CAS_type) == 'PR') && (trim($CAS_reason_code) == '2')){
											$pt_co_ins = $CAS_amt;
										}
										$CASTypeCode = $CAS_type.' '.$CAS_reason_code;
									}
									//DEDUCT AMOUNT
									$totalDeductAmount = $totalDeductAmount + $deductAmount;
									$total_pt_co_ins = $total_pt_co_ins+$pt_co_ins;
									if(strpos($CAS_amt, ", ")){
										$CAS_amtArr = explode(", ", $CAS_amt);
										/*$newBalanceShow = '$'.number_format($CAS_amtArr[0], 2).', $'.number_format($CAS_amtArr[1], 2);	
										$newBalance = $CAS_amtArr[0] + $CAS_amtArr[1];
										if($CAS_amtArr[2]!=""){
											$newBalanceShow = '$'.number_format($CAS_amtArr[0], 2).', $'.number_format($CAS_amtArr[1], 2).', $'.number_format($CAS_amtArr[2], 2);
											$newBalance = $CAS_amtArr[0] + $CAS_amtArr[1] + $CAS_amtArr[2];
										}*/									
										$newBalanceShow=0;
										$newBalance=0;
										foreach($CAS_amtArr as $key => $valueType){
											if($newBalanceShow == ''){
												$newBalanceShow = '$'.number_format($CAS_amtArr[$key],2);
												//$newBalance = $newBalance+$CAS_amtArr[$key];
											}else{
												$newBalanceShow = $newBalanceShow.', $'.number_format($CAS_amtArr[$key],2);;
												//$newBalance = $newBalance+$CAS_amtArr[$key];
											}
										}
										//$newBalance = number_format($newBalance, 2);
										$approvedAmount = $SVC_proc_charge - $adjustAmt;
										
									}else{
										//$newBalance = number_format($CAS_amt, 2);
										$newBalanceShow = '$'.number_format($CAS_amt, 2);
										$approvedAmount = 0;
									}
									$getClpCasStr = "SELECT * FROM era835clpcas WHERE 
										era835Id = '$Era_Id835'
										AND ERAPatientdetailsId = '$ERAPatientDetailsId'";
									$getClpCasQry = imw_query($getClpCasStr);
									$chk_cont_proc_num=0;
									if(imw_num_rows($getClpCasQry)){
										$getDetailsOfStr_num = imw_query("SELECT * FROM era_835_proc_details
										WHERE `ERA_patient_details_id` = '$ERAPatientDetailsId'");
										$chk_cont_proc_num=imw_num_rows($getDetailsOfStr_num);
									}
									if($chk_cont_proc_num==1){
										$SVC_provider_pay_amt=$CLP_claim_payment_amount;
									}
									$newBalance = $SVC_proc_charge - $deductAmount - $SVC_provider_pay_amt;
									$newBalance = number_format($newBalance, 2);
									$totalPatCharges = $totalPatCharges + $SVC_proc_charge;
									$totalPayAmt = $totalPayAmt + $SVC_provider_pay_amt;
									//$totalNewBalance = $totalNewBalance + str_replace(',','',$newBalance);
									$totalAdjustAmt = $totalAdjustAmt + $adjustAmt;
									// GETTING RENDPROV
									
									
									$ERA_patient_details_id = $getClaimDeailsRows['ERA_patient_details_id'];
										$getRenProvStr = "SELECT NM1_patient_id FROM era_835_nm1_details
															WHERE ERA_patient_details_id = '$ERAPatientDetailsId'
															AND NM1_type = '82'";
										$getRenProvQry = imw_query($getRenProvStr);
										$getRenProvRow = imw_fetch_array($getRenProvQry);
											$RENDPROV = $getRenProvRow['NM1_patient_id'];
									// GETTING RENDPROV
									
								
								if($SVC_proc_code == '92015'){
									//$AMT_amount = 0;
								}
								$totalAllowedAmt = $totalAllowedAmt + $AMT_amount;
								if(strlen($REFProvId[1])>5){
									$REFProvId[1] = substr($REFProvId[1], 0, 4).'...';
								}
								$CLP_claim_payment_amount =  $getClaimDeailsRows['CLP_claim_payment_amount'];
								
									?>
									<tr height="20">
										<td width="65" align="center" class="text_10"><?php echo $RENDPROV;//$REFProvId[2]; ?></td>
										<td width="93" align="center" class="text_10">
											<a class="text_10" href="javascript:showPaymentFn('<?php echo $encounter_id; ?>');">
												<?php echo $DTM_date; ?>
											</a>
										</td>
										<td align="center" class="text_10"><?php if($REFProvId[1]) echo $REFProvId[1]; else echo 'N/A'; ?></td>
										<td align="center" class="text_10"><?php echo $SVC_proc_code; ?></td>
										<td align="center" class="text_10"><?php if($SVC_mod_code) echo $SVC_mod_code; else echo "N/A"; ?></td>
										<td align="right" class="text_10" style="padding-right:2px;"><?php if($SVC_proc_charge) echo '$'.number_format($SVC_proc_charge, 2); else echo '-'; ?></td>
										<td align="right" class="text_10" style="padding-right:2px;"><?php if($AMT_amount) echo '$'.number_format($AMT_amount, 2); else echo '-'; ?></td>
										<td align="right" class="text_10" style="padding-right:2px;"><?php if($deductAmount) echo '$'.number_format($deductAmount, 2); else echo '-'; ?></td>
										<td align="right" class="text_10" style="padding-right:2px;"><?php if($SVC_provider_pay_amt) echo '$'.number_format($SVC_provider_pay_amt, 2); else echo '-'; ?></td>
										<td align="right" class="text_10" style="padding-right:2px;"><?php if($pt_co_ins) echo '$'.number_format($pt_co_ins, 2); else echo '-'; ?></td>
                                        <td align="right" class="text_10" style="padding-right:2px;"><?php echo $newBalanceShow; ?></td>
									    <td align="left" class="text_10" style="padding-left:2px;" nowrap><?php echo $CASTypeCode; ?></td>
                                         <td align="left" class="text_10" style="padding-left:2px;" nowrap><?php echo $rem_code; ?></td>
									</tr>
									<?php
								}
							}
							?>
							<tr height="20">
								<td colspan="3" align="left" class="text_10b"><?php echo $secondaryInsName; ?></td>
								<td colspan="2" align="right" class="text_10b">
									Total Billed:
								</td>
								<td align="right" class="text_10b" style="padding-right:5px;"><?php echo '$'.number_format($totalPatCharges, 2); ?></td>
								<td align="right" class="text_10b" style="padding-right:5px;"><?php echo '$'.number_format($totalAllowedAmt, 2); ?></td>
								<td align="right" class="text_10b" style="padding-right:5px;"><?php echo '$'.number_format($totalDeductAmount, 2); ?></td>
                            	<td align="right" class="text_10b" style="padding-right:5px;"><?php echo '$'.number_format($CLP_claim_payment_amount, 2); ?></td>
                                <td align="right" class="text_10b" style="padding-right:5px;"><?php echo '$'.number_format($total_pt_co_ins, 2); ?></td>
                                <td align="right" class="text_10b" style="padding-right:5px;">&nbsp;<?php //echo '$'.number_format($totalNewBalance, 2); ?></td>
							    <td align="right" class="text_10b" style="padding-right:5px;">&nbsp;</td>
                                 <td align="right" class="text_10b" style="padding-right:5px;">&nbsp;</td>
							</tr>
					  </table>
					</td>
				</tr>
			</table>
			<?php
		}			
	}
	?>
</body>
</html>
<?php
if(strpos($tsuc_identifier, ",")){
	$posOf =  strpos($tsuc_identifier, ",");
	$tsucIdentifier = substr($tsuc_identifier,0, $posOf);  
}else{
	$tsucIdentifier = $tsuc_identifier;
}
?>
