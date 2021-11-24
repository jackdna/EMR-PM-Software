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
ini_set("memory_limit","3072M");
set_time_limit(0); 
$without_pat="yes";  
require_once("../accounting/acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../library/classes/common_function.php"); 

$tday = date('m-d-Y');
$ERA_FILE_id = xss_rem($_REQUEST['ERA_FILE_id'], 3);	/* Reject parameter with unwanted values - Security Fix */

$ERA_FILE_CHK_id = $_REQUEST['ERA_FILE_CHK_id'];
$send_era_pat_id = $_REQUEST['send_era_pat_id'];
$era_manual_payments = $_REQUEST['era_manual_payments'];
$html_file_name = 'postPaymentERA_'.$_SESSION['authId'];
$send_era_pat_id_exp = explode(',',$_REQUEST['send_era_pat_id']);
$hic_on="";
if(in_array(strtolower($billing_global_server_name), array('brian'))){
	$hic_on=" / HIC";
}
function era_reason_amt($case_type,$case_reason,$case_amt,$era_835_proc_id){
	if(strpos($case_type, ",")){
		$CAS_typeArr = explode(", ",$case_type);
		$CAS_amtArr = explode(", ",$case_amt);
		$CAS_reason_codeArr = explode(", ", $case_reason);
		$set_cas_type_arr=array();
		$old_cas_type="";												
		foreach($CAS_typeArr as $cas_key => $cas_type){	
			if($cas_key>0){
				$comma_br=", ";
			}
			$cas_reason = $cas_reason.$comma_br.$CAS_typeArr[$cas_key].' '.$CAS_reason_codeArr[$cas_key];
			$cas_amount = $cas_amount.$comma_br.numberFormat($CAS_amtArr[$cas_key],2);
			
			if(trim($CAS_typeArr[$cas_key])>0){
				$set_cas_type_arr[100]=$old_cas_type;
			}else{
				$set_cas_type_arr[]=$CAS_typeArr[$cas_key];
				$old_cas_type=$CAS_typeArr[$cas_key];
			}
			
			if(($CAS_typeArr[$cas_key] == 'PR' || $CAS_typeArr[$cas_key] == 'OA') && ($CAS_reason_codeArr[$cas_key] == '1')){	
				$deduct_amount = $deduct_amount+$CAS_amtArr[$cas_key];
			}
			if($CAS_typeArr[$cas_key] == 'PR' && ($CAS_reason_codeArr[$cas_key] == '2' || $CAS_reason_codeArr[$cas_key] == '3')){
				$pat_res_amount = $pat_res_amount+$CAS_amtArr[$cas_key];
			}
			if(($CAS_typeArr[$cas_key] == 'CO' || $CAS_typeArr[$cas_key] == 'PI' || $CAS_typeArr[$cas_key] == 'OA') && ($CAS_reason_codeArr[$cas_key] == '42' || $CAS_reason_codeArr[$cas_key] == '45' || $CAS_reason_codeArr[$cas_key] == '59' || $CAS_reason_codeArr[$cas_key] == '237' || $CAS_reason_codeArr[$cas_key] == '150' || $CAS_reason_codeArr[$cas_key] == 'B10' || $CAS_reason_codeArr[$cas_key] == '104' || $CAS_reason_codeArr[$cas_key] == '144' || $CAS_reason_codeArr[$cas_key] == '131' || $CAS_reason_codeArr[$cas_key] == '187' || $CAS_reason_codeArr[$cas_key] == '35' || $CAS_reason_codeArr[$cas_key] == '97' || $CAS_reason_codeArr[$cas_key] == '15' || $CAS_reason_codeArr[$cas_key] == '96')){
				$writeoff_amount = $writeoff_amount + $CAS_amtArr[$cas_key];
			}
		}
	}else{
		$cas_reason = $case_type.' '.$case_reason;
		$cas_amount = numberFormat($case_amt,2);
		if(($case_type == 'PR' || $case_type == 'OA') && ($case_reason == '1')){
			$deduct_amount = $case_amt;
		}
		if($case_type == 'PR' && ($case_reason == '2' || $case_reason == '3')){
			$pat_res_amount = $case_amt;
		}
		if(($case_type == 'CO' || $case_type == 'PI' || $case_type == 'OA') && ($case_reason == '42' || $case_reason == '45' || $case_reason == '59' || $case_reason == '237' || $case_reason == '150' || $case_reason == 'B10' || $case_reason == '104' || $case_reason == '144' || $case_reason == '131' || $case_reason == '187' || $case_reason == '35' || $case_reason == '97' || $case_reason == '15' || $case_reason == '96')){
			$writeoff_amount = $case_amt;
		}
	}
	
	$ret_arr['era_writeoff']=$writeoff_amount;
	$ret_arr['era_deduct']=$deduct_amount;
	$ret_arr['era_reason']=$cas_reason;
	$ret_arr['era_amount']=$cas_amount;
	$ret_arr['era_pat_res_amount']=$pat_res_amount;
	return $ret_arr;
}

$sel_cas_qry=imw_query("select * from cas_reason_code");
while($sel_cas_row=imw_fetch_array($sel_cas_qry)){
	$all_cas_arr[$sel_cas_row['cas_code']]=$sel_cas_row['cas_desc'];
}

$qry = imw_query("select cpt_fee_id,cpt_cat_id,cpt4_code,cpt_prac_code from cpt_fee_tbl where delete_status='0'");
while($fet_cpt = imw_fetch_array($qry)){
	$cpt_fee_tbl_arr[$fet_cpt['cpt_fee_id']] = $fet_cpt;
}

$qry=imw_query("select modifiers_id,mod_prac_code from modifiers_tbl");
while($row=imw_fetch_array($qry)){
	$mod_prac_id_arr[$row['mod_prac_code']]=$row['modifiers_id'];
}

if($send_era_pat_id_exp[0]>0){
	$era_pat_whr=" and c.ERA_patient_details_id in(".$send_era_pat_id.")";
	$era_pat_f_whr=" and era_835_nm1_details.ERA_patient_details_id in(".$send_era_pat_id.")";
	$manual_whr_chk=" and era_835_details.835_Era_Id='".$_REQUEST['send_era_chk_id']."'";
}

if($era_manual_payments!=""){
	$qry=imw_query("select group_concat(DISTINCT(era_835_details.835_Era_Id)) as manual_835_Era_Id,group_concat(DISTINCT(era_835_proc_details.ERA_patient_details_id)) as manual_835_pt_Id from era_835_proc_details join era_835_details on era_835_proc_details.835_Era_Id=era_835_details.835_Era_Id 
	where era_835_details.electronicFilesTblId in($ERA_FILE_id) and era_835_proc_details.postedStatus='Not Posted' $manual_whr_chk");
	$row=imw_fetch_array($qry);
	$manual_835_Era_Id_whr=" and a.835_Era_Id in(".$row['manual_835_Era_Id'].")";
	$whr_proc_chk=" and d.postedStatus='Not Posted'";
	$manual_835_pt_Id_whr=" and era_835_patient_details.ERA_patient_details_id in(".$row['manual_835_pt_Id'].")";
}

$qry=imw_query("select id,file_name,file_temp_name,post_status,modified_date,file_contents from electronicfiles_tbl where id='$ERA_FILE_id'");
$fileDetails=imw_fetch_object($qry);
$file_name = $fileDetails->file_name;


if($ERA_FILE_CHK_id>0){
	$era_chk_whr=" and a.835_Era_Id = '".$ERA_FILE_CHK_id."'";
}
	$getDetailsCountStr = "SELECT a.`835_Era_Id` AS era_Id,
							a.provider_payment_amount,
							a.TRN_payment_type_number,
							b.CLP_total_claim_charge,
							b.ERA_patient_details_id,
							b.CLP_claim_patient_res_amt,
							b.CLP_claim_payment_amount,
							c.ERA_patient_details_id,
							c.NM1_type,
							d.CAS_amt,
							d.SVC_proc_charge,
							d.SVC_provider_pay_amt
							FROM era_835_details a,
							era_835_patient_details b,
							era_835_nm1_details c,
							era_835_proc_details d
							WHERE a.electronicFilesTblId = $ERA_FILE_id
							AND a.`835_Era_Id` = b.`835_Era_Id`
							AND c.ERA_patient_details_id = b.ERA_patient_details_id
							AND c.NM1_type='QC'
							AND d.ERA_patient_details_id = b.ERA_patient_details_id
							$era_pat_whr $era_chk_whr $manual_835_Era_Id_whr $whr_proc_chk
							GROUP BY d.ERA_patient_details_id
							ORDER BY a.835_Era_Id";
	$getDetailsCountQry = imw_query($getDetailsCountStr);
	$countPaymentRows = imw_num_rows($getDetailsCountQry);
	while($getDetailsCountRows = imw_fetch_array($getDetailsCountQry)){
		$era_Id = $getDetailsCountRows['era_Id'];
		$eraIdArr[$era_Id] = $era_Id;
		$SVC_proc_charge = $getDetailsCountRows['SVC_proc_charge'];
		$SVC_provider_pay_Amount = $getDetailsCountRows['SVC_provider_pay_amt'];
		$provider_payment_amount = $getDetailsCountRows['provider_payment_amount'];
		if($newMatch != $era_Id){		
			if($provider_payment_amount){
				$overAllPayAmt = $overAllPayAmt + $provider_payment_amount;
			}
		$newMatch = $era_Id;
		}
		$TRN_payment_type_number = $getDetailsCountRows['TRN_payment_type_number'];		
		$provider_payment_amount = $getDetailsCountRows['provider_payment_amount'];
		if($provider_payment_amount)
			$providerPaymentAmount = $provider_payment_amount;
		$CAS_amt = $getDetailsCountRows['CAS_amt'];
		if(strpos($CAS_amt, ", ")){
			$CAS_amtArr = explode(", ", $CAS_amt);
			$CAS_amtNow = $CAS_amtNow + $CAS_amtArr[0];
		}else{
			$CAS_amtNow = $CAS_amtNow + $CAS_amt;
		}
		
		if($getDetailsCountRows['CLP_claim_payment_amount']==0){
			++$unApplied;
			$unAppliedCLPClaimCharge = $getDetailsCountRows['CLP_total_claim_charge'];
				$totalunAppliedCharge = $totalunAppliedCharge + $unAppliedCLPClaimCharge;
			$unAppliedCLPClaimPayment = $getDetailsCountRows['CLP_claim_payment_amount'];
				$totalUnAppliedPayment = $totalUnAppliedPayment + $unAppliedCLPClaimPayment;				
		}else{
			++$applied;
			$appliedCLPClaimCharge = $getDetailsCountRows['CLP_total_claim_charge'];
				$totalAppliedCharge = $totalAppliedCharge + $appliedCLPClaimCharge;
			$appliedCLPClaimPayment = $getDetailsCountRows['CLP_claim_payment_amount'];
				$totalAppliedPayment = $totalAppliedPayment + $appliedCLPClaimPayment;
				
		}
	}
	// COUNT ROWS
	$totalPaidAmount = 0;
	$getPaidAmtDetailsStr = "SELECT
							sum(b.CLP_claim_payment_amount),
							sum(d.SVC_proc_charge),
							sum(d.SVC_provider_pay_amt)
							FROM era_835_details a,
							era_835_patient_details b,
							era_835_nm1_details c,
							era_835_proc_details d
							WHERE a.electronicFilesTblId = '$electronicFilesTblId'
							AND a.`835_Era_Id` = b.`835_Era_Id`
							AND c.ERA_patient_details_id = b.ERA_patient_details_id
							AND c.NM1_type='QC'
							AND d.ERA_patient_details_id = b.ERA_patient_details_id
							$era_pat_whr $era_chk_whr $manual_835_Era_Id_whr $whr_proc_chk";
	if($chkNumberDetails){
		$getPaidAmtDetailsStr .= " AND a.TRN_payment_type_number = '$chkNumberDetails'";
	}
	$getPaidAmtDetailsQry = imw_query($getPaidAmtDetailsStr);
	$getPaidAmtDetailsRows = imw_fetch_array($getPaidAmtDetailsQry);
		$billedAmount = $getPaidAmtDetailsRows[1];
		$totalPaidAmount = $getPaidAmtDetailsRows[2];
		
	//GET DEDUCTIBLE AMOUNT
	if(count($eraIdArr)>0){
		foreach($eraIdArr as $key => $value){
			$getTotalDeductAmtStr = "SELECT d.CAS_amt, d.CAS_type, d.CAS_reason_code
									FROM `era_835_proc_details` as d
									WHERE d.835_Era_Id = '$key' $whr_proc_chk";
			$getTotalDeductAmtQry = imw_query($getTotalDeductAmtStr);
			while($getTotalDeductAmtRows = imw_fetch_assoc($getTotalDeductAmtQry)){
				$CAS_amt = $getTotalDeductAmtRows['CAS_amt'];
				$CAS_type = $getTotalDeductAmtRows['CAS_type'];
				$CAS_reason_code = $getTotalDeductAmtRows['CAS_reason_code'];
				unset($CAS_typeArr);
				unset($CAS_reason_codeArr);
				unset($CAS_amtArr);
				if(strpos($CAS_type, ",")){		
					$CAS_typeArr = explode(",",$CAS_type);
					$CAS_reason_codeArr = explode(",",$CAS_reason_code);			
					$CAS_amtArr = explode(",",$CAS_amt);
					foreach($CAS_typeArr as $k => $val){
						if((trim($CAS_typeArr[$k]) == 'PR') && ($CAS_reason_codeArr[$k] == '1')){
							$CAS_amtTotal = $CAS_amtTotal + $CAS_amtArr[$k];
						}
						if((trim($CAS_typeArr[$k]) == 'CO' || trim($CAS_typeArr[$k]) == 'OA'  || trim($CAS_typeArr[$k]) == 'PI') && ($CAS_reason_codeArr[$k] == '42' || $CAS_reason_codeArr[$k] == '45' || $CAS_reason_codeArr[$k] == '59' || $CAS_reason_codeArr[$k] == '237' || $CAS_reason_codeArr[$k] == '150' || $CAS_reason_codeArr[$k] == 'B10' || $CAS_reason_codeArr[$k] == '104' || $CAS_reason_codeArr[$k] == '144' || $CAS_reason_codeArr[$k] == '131' || $CAS_reason_codeArr[$k] == '187')){
							$writeOFFAmt = $writeOFFAmt + $CAS_amtArr[$k];
						}
					}
				}else{
					if((trim($CAS_type) == 'PR') && ($CAS_reason_code == '1')){
						$CAS_amtTotal = $CAS_amtTotal + $CAS_amt;
					}
					if((trim($CAS_type == 'CO') || trim($CAS_type == 'PI') || trim($CAS_type == 'OA')) && ($CAS_reason_code == '42' || $CAS_reason_code == '45' || $CAS_reason_code == '59' || $CAS_reason_code == '150' || $CAS_reason_code == 'B10' || $CAS_reason_code == '104' || $CAS_reason_code == '144' || $CAS_reason_code == '131' || $CAS_reason_code == '187')){
						$writeOFFAmt = $writeOFFAmt + $CAS_amt;
					}
				}
			}
			$deductAmt = $CAS_amtTotal;
		}
	}
//------------------------------------------------------------------------------------------------
			$getDetailsExistsOrNotStr = "SELECT a.* FROM era_835_details as a
										WHERE a.electronicFilesTblId = '$ERA_FILE_id' $era_chk_whr $manual_835_Era_Id_whr";
			$getDetailsExistsOrNotQry = imw_query($getDetailsExistsOrNotStr);
			while($getDetailsExistsOrNotRow = imw_fetch_array($getDetailsExistsOrNotQry)){
				$Era_835_Id = $getDetailsExistsOrNotRow['835_Era_Id'];
				extract($getDetailsExistsOrNotRow);
				$getPatDetailsQry = imw_query("SELECT * FROM `era_835_patient_details` WHERE 835_Era_Id = $Era_835_Id");
				if(imw_num_rows($getPatDetailsQry)){
					while($getPatDetailsRows = imw_fetch_assoc($getPatDetailsQry)){
						$ERAPatientDetailsId[] = $getPatDetailsRows['ERA_patient_details_id'];
					}			
				}
			}
			foreach($ERAPatientDetailsId as $key => $patDetailsId){
				$getCountStatusStr = "SELECT postedStatus FROM era_835_proc_details WHERE 
										ERA_patient_details_id = '$patDetailsId'
										AND postedStatus = 'Not Posted'
										GROUP BY ERA_patient_details_id";
				$getCountStatusQry = imw_query($getCountStatusStr);
				$getCount = imw_num_rows($getCountStatusQry);
				$getTotalCount = $getTotalCount + $getCount;
			}
			$notPostedAre = $getTotalCount;
			$postedAre = count($ERAPatientDetailsId) - $notPostedAre;
//------------------------------------------------------------------------------------------------
?>
<?php
$printPdf='';
$pdfCSS='
<style>
	.cellBorder3 td{border:1px solid #FFE2C6;}
	.bg4{background-color:#4684AB; color:#FFFFFF;}
	.text11b{font-family:Arial, Helvetica, sans-serif;font-size:11px; font-weight:bold;}
	.text11{font-family:Arial, Helvetica, sans-serif;font-size:11px;}
	.pl5{padding-left:5px}
	.mt5{margin-top:5px;}
	.text_10b{font-family:Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold;}
	.text_10{font-family:Arial, Helvetica, sans-serif; font-size:10px; color:#333333;}
	.text_b_w{
		font-size:11px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		color:#FFFFFF;
		background-color:#4684ab;
	}
</style>';

$printPdf=$pdfCSS.'
<page backtop="10.5mm" backbottom="5mm">
<page_footer>
    <table style="width: 100%;">
        <tr>
            <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
        </tr>
    </table>
</page_footer>
<page_header>
<table class="table_collapse cellBorder3">
	<tr bgcolor="#4684ab" height="20">
		<td class="text_b_w" colspan="11" style="font-size:13px; text-align:left;">
			Claim Details File Name: ('.$file_name.')					
		</td>
		<td class="text_b_w" colspan="4" style="font-size:13px; text-align:right;">
			<strong>(P = Posted, NP = Not Posted)</strong>&nbsp;
		</td>
	</tr>
	<tr>
		<td style="width:5px;" class="text11b bg4">&nbsp;</td>
		<td class="text11b bg4" style="width:140px; text-align:center">Name</td>
		<td class="text11b bg4" style="width:40px; text-align:center">EId</td>
		<td class="text11b bg4" style="width:130px; text-align:center">ICN '.$hic_on.'</td>
		<td class="text11b bg4" style="width:40px; text-align:center">DOS</td>
		<td class="text11b bg4" style="width:60px; text-align:center">CPT</td>
		<td class="text11b bg4" style="width:60px; text-align:center">Charges</td>
		<td class="text11b bg4" style="width:60px; text-align:center">Allowed</td>
		<td class="text11b bg4" style="width:50px; text-align:center">Deduct</td>
		<td class="text11b bg4" style="width:70px; text-align:center">Paid</td>
		<td class="text11b bg4" style="width:50px; text-align:center">Pt Res</td>
		<td class="text11b bg4" style="width:50px; text-align:center">CAS</td>
		<td class="text11b bg4" style="width:50px; text-align:center">Amount</td>
		<td class="text11b bg4" style="width:60px; text-align:center">Rem&nbsp;Code</td>
		<td class="text11b bg4" style="width:60px; text-align:center">Processed&nbsp;By</td>
	</tr>
</table>
</page_header>
	<table class="table_collapse cellBorder3">
	<tr>
		<td style="width:5px;" class="text11b"></td>
		<td class="text11b" style=" text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
		<td class="text11b" style="text-align:center"></td>
	</tr>';

				if($ERA_FILE_id!=''){
					$enc_level_adj_arr=array();	
					$i = 0;
					$electronicFilesTblId = $ERA_FILE_id;
					$getDetailsExistsOrNotStr = "SELECT a.* FROM era_835_details as a
													WHERE a.electronicFilesTblId = '$electronicFilesTblId' $era_chk_whr $manual_835_Era_Id_whr";
					$getDetailsExistsOrNotQry = imw_query($getDetailsExistsOrNotStr);
					while($getDetailsExistsOrNotRow = imw_fetch_array($getDetailsExistsOrNotQry)){
						$Era_835_Id = $getDetailsExistsOrNotRow['835_Era_Id'];
						$TRN_trace_numbers = $getDetailsExistsOrNotRow['TRN_trace_numbers'];
						$TRN_payment_type_number = $getDetailsExistsOrNotRow['TRN_payment_type_number'];
						$TRN_orignating_company_id = $getDetailsExistsOrNotRow['TRN_orignating_company_id'];
						$REF_receiver_reference_id = $getDetailsExistsOrNotRow['REF_receiver_reference_id'];
						$DTM_production_date = $getDetailsExistsOrNotRow['DTM_production_date'];
						$BPRSegment = $getDetailsExistsOrNotRow['BPR_segment'];
						$N1_payer_name = $getDetailsExistsOrNotRow['N1_payer_name'];
						$N3_payer_address = $getDetailsExistsOrNotRow['N3_payer_address'];
						$N4_payer_city = $getDetailsExistsOrNotRow['N4_payer_city'];
						$N4_payer_state = $getDetailsExistsOrNotRow['N4_payer_state'];
						$N4_payer_zip = $getDetailsExistsOrNotRow['N4_payer_zip'];
						$REF_provider_ref_id = $getDetailsExistsOrNotRow['REF_provider_ref_id'];
						$PER_payer_office_name = $getDetailsExistsOrNotRow['PER_payer_office_name'];
						$PER_payer_office_phone = $getDetailsExistsOrNotRow['PER_payer_office_phone'];
						$N1_payee_name = $getDetailsExistsOrNotRow['N1_payee_name'];
						$N3_payee_address = $getDetailsExistsOrNotRow['N3_payee_address'];
						$N4_payee_city = $getDetailsExistsOrNotRow['N4_payee_city'];
						$N4_payee_state = $getDetailsExistsOrNotRow['N4_payee_state'];
						$N4_payee_zip = $getDetailsExistsOrNotRow['N4_payee_zip'];
						$provider_payment_amount_chk = $getDetailsExistsOrNotRow['provider_payment_amount'];	
						//------------- GET 835_Era DETAILS
						$getPatientDetailsStr = "SELECT era_835_patient_details.* FROM era_835_patient_details 
												join era_835_nm1_details on era_835_nm1_details.ERA_patient_details_id=era_835_patient_details.ERA_patient_details_id
												WHERE era_835_patient_details.835_Era_Id = '$Era_835_Id'
												$era_pat_f_whr $manual_835_pt_Id_whr
												group by era_835_patient_details.ERA_patient_details_id
												ORDER BY era_835_nm1_details.NM1_last_name";
						$getPatientDetailsQry = imw_query($getPatientDetailsStr);
						
						while($getPatientDetailsRows = imw_fetch_array($getPatientDetailsQry)){
							$ERA_patient_details_id = $getPatientDetailsRows['ERA_patient_details_id'];
							$CLP_claim_submitter_id = $getPatientDetailsRows['CLP_claim_submitter_id'];
							$CLP_claim_status = $getPatientDetailsRows['CLP_claim_status'];
							$CLP_total_claim_charge = $getPatientDetailsRows['CLP_total_claim_charge'];
							$CLP_claim_payment_amount = $getPatientDetailsRows['CLP_claim_payment_amount'];
							$CLP_claim_patient_res_amt = $getPatientDetailsRows['CLP_claim_patient_res_amt'];
							$CLP_payer_claim_control_number = $getPatientDetailsRows['CLP_payer_claim_control_number'];
							$DTM_qualifier = $getPatientDetailsRows['DTM_qualifier'];
							$DTM_date = $getPatientDetailsRows['DTM_date'];
							$icn=$CLP_payer_claim_control_number;
							
							if($CLP_claim_status == 1 || $CLP_claim_status == 19){
								$processed_by =  'Primary';
							}else if($CLP_claim_status == 2 || $CLP_claim_status == 20){
								$processed_by =  'Secondary';
							}else if($CLP_claim_status == 3 || $CLP_claim_status == 21){
								$processed_by =  'Tertiary';
							}else if($CLP_claim_status == 4){
								$processed_by = 'Denied';
							}else{
								$processed_by = '-';
							}
							
							$secondaryInsName=$proc_SVC_proc_charge_chq=$proc_AMT_amount_chq=$proc_deductAmount_chq=$proc_SVC_provider_pay_amt_chq=$proc_pat_res_chq="";
							$getNM1DetailsStr = "SELECT * FROM era_835_nm1_details 
												WHERE ERA_patient_details_id = '$ERA_patient_details_id'
												ORDER BY NM1_last_name";
							$getNM1DetailsQry = imw_query($getNM1DetailsStr);
							while($getNM1DetailsRow = imw_fetch_array($getNM1DetailsQry)){
								$NM1_ERA_patient_details_id = $getNM1DetailsRow['ERA_patient_details_id'];
								$NM1_type = $getNM1DetailsRow['NM1_type'];
								$NM1_last_name = stripslashes($getNM1DetailsRow['NM1_last_name']);
								$NM1_first_name = stripslashes($getNM1DetailsRow['NM1_first_name']);
								$NM1_method_code_stru = $getNM1DetailsRow['NM1_method_code_stru'];
								$NM1_patient_id = $getNM1DetailsRow['NM1_patient_id'];
									if($NM1_type=='QC'){
										$getProcDetailStr = "SELECT d.* FROM era_835_proc_details as d
															WHERE d.ERA_patient_details_id = '$NM1_ERA_patient_details_id' $whr_proc_chk";
										$getProcDetailQry = imw_query($getProcDetailStr);
										$counyPayments = imw_num_rows($getProcDetailQry);
										$SVC_provider_pay_amt = 0;
										while($getProcDetailRows = imw_fetch_array($getProcDetailQry)){
											$SVC_proc_code = $getProcDetailRows['SVC_proc_code'];
											$SVC_mod_code = $getProcDetailRows['SVC_mod_code'];
											$procPostedStatus = $getProcDetailRows['postedStatus'];
											$AMT_amount = $getProcDetailRows['AMT_amount'];
											$rem_code = $getProcDetailRows['rem_code'];
											unset($modArr);
											if(strpos($SVC_mod_code, ",")){
												$modArr = explode(", ", $SVC_mod_code);
											}else{
												$modArr[0] = $SVC_mod_code; 
											}
											$SVC_proc_charge = $getProcDetailRows['SVC_proc_charge'];
											$SVC_provider_pay_amt = $getProcDetailRows['SVC_provider_pay_amt'];
											
											$REF_prov_identifier = $getProcDetailRows['REF_prov_identifier'];
											$encounter_id = substr($REF_prov_identifier, 0, strpos($REF_prov_identifier, 'MCR'));
											
											$unit = $getProcDetailRows['unit'];
											$units_service_paid = $getProcDetailRows['units_service_paid'];
											$SVC_proc_unit = $getProcDetailRows['SVC_proc_unit'];
											$DTM_type = $getProcDetailRows['DTM_type'];
											$DTM_date = $getProcDetailRows['DTM_date'];
											if($getProcDetailRows['DTM_type']==''){
												if($getPatientDetailsRows['DTM_qualifier']=="232"){
													$DTM_type = $getPatientDetailsRows['DTM_qualifier'];
													$DTM_date = $getPatientDetailsRows['DTM_date'];
												}
											}
											$DOS = $DTM_date;
												list($DTMyy, $DTMmm, $DTMdd) = explode("-", $DTM_date);
												$DTM_date = $DTMmm.'-'.$DTMdd.'-'.substr($DTMyy,2);
											$CAS_type = $getProcDetailRows['CAS_type'];
											$CAS_reason_code = $getProcDetailRows['CAS_reason_code'];
											$CAS_amt = $getProcDetailRows['CAS_amt'];
											
											//ICN Print
											$REF_prov_identifier = $getProcDetailRows['REF_prov_identifier'];
											$REF_prov_identifier = @preg_replace('/\s+/','',$REF_prov_identifier);
											
											$tsucPos = strpos($REF_prov_identifier, '_TSUC_');
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
											
											if(strpos($tsuc_identifier, ",")){
												$posOf =  strpos($tsuc_identifier, ",");
												$tsucIdentifier = substr($tsuc_identifier,0, $posOf);  
											}else{
												$tsucIdentifier = $tsuc_identifier;
											}
											
											if($CLP_claim_submitter_id!="" && !$encounter_id){
									
												$cpt_fee_id_arr=array();
												foreach($cpt_fee_tbl_arr as $cpt_key => $cpt_val){
													if(strtolower($cpt_fee_tbl_arr[$cpt_key]['cpt4_code'])==strtolower($SVC_proc_code)){
														$cpt_fee_id_arr[$cpt_fee_tbl_arr[$cpt_key]['cpt_fee_id']]=$cpt_fee_tbl_arr[$cpt_key]['cpt_fee_id'];
													}
												}							
												$cpt_fee_id=implode(',',$cpt_fee_id_arr);			
												
												$enc_base_dos_arr=array();
												$mod_id1=$mod_id2="";
												if($modArr[0]!=""){
													$mod_id1=$mod_prac_id_arr[$modArr[0]];
												}
												if($mod_arr[1]!=""){
													$mod_id2=$mod_prac_id_arr[$modArr[1]];
												}
																					
												$chl_qry = "select pcl.charge_list_id,pcl.encounter_id,pcld.approvedAmt,pcld.totalAmount,pcld.charge_list_detail_id
															from patient_charge_list pcl join patient_charge_list_details pcld on pcl.charge_list_id=pcld.charge_list_id
															where pcld.del_status='0' and pcl.date_of_service='$DOS' AND pcld.procCode in($cpt_fee_id)
															and pcld.patient_id ='".$CLP_claim_submitter_id."'";
															
												if($mod_id1>0 && $mod_id2>0){
													$chl_qry.=" AND ((pcld.modifier_id1 = '$mod_id1' and pcld.modifier_id2 = '$mod_id2') 
																or (pcld.modifier_id1 = '$mod_id2' and pcld.modifier_id2 = '$mod_id1'))";
												}else{
													if($mod_id1>0){
														$chl_qry.=" AND pcld.modifier_id1 = '$mod_id1'";
													}
													if($mod_id2>0){
														$chl_qry.=" AND pcld.modifier_id2 = '$mod_id2'";
													}		
													if($mod_id1<=0 && $mod_id2<=0){
														$chl_qry.=" AND pcld.modifier_id1 = '' and pcld.modifier_id2 = ''";
													}	
												}
												$chl_sql = imw_query($chl_qry);
												$chl_cont=imw_num_rows($chl_sql);
												while($chl_row = imw_fetch_assoc($chl_sql)){
													$charge_list_id = $chl_row['charge_list_id'];
													$encounterId = $chl_row['encounter_id'];
													$enc_base_dos_arr[$chl_row['encounter_id']]=$chl_row['encounter_id'];
													if($chl_cont==1){
														$encounter_id = $chl_row['encounter_id'];
													}
												}
												if(count($enc_base_dos_arr)==1 && $encounter_id<=0){
													$encounter_id = $encounterId;
												}
											}
											
											//ICN Print
											$CASTypeCode = '';
											unset($CAS_amtArr);
											//DEDUCT AMOUNT
											$deductAmount = 0;
											if(strpos($CAS_type, ",")){
												$CAS_typeArr = explode(",",$CAS_type);
												$CAS_reason_codeArr = explode(",",$CAS_reason_code);			
												$CAS_amtArr = explode(",",$CAS_amt);
												$deductAmount = 0;
												foreach($CAS_typeArr as $k => $val){
													if((trim($CAS_typeArr[$k]) == 'PR' || trim($CAS_typeArr[$k]) == 'OA') && (trim($CAS_reason_codeArr[$k]) == '1')){
														$deductAmount = $deductAmount + $CAS_amtArr[$k];
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
												if((trim($CAS_type) == 'PR' || trim($CAS_type) == 'OA') && (trim($CAS_reason_code) == '1')){
													$deductAmount = $CAS_amt;
												}else{
												//	$deductAmount = '0.00';
												}
												$CASTypeCode = $CAS_type.' '.$CAS_reason_code;
												$CAS_amtArr[0] = $CAS_amt;
											}
											$cas_code_arr[]=$CASTypeCode;
											unset($CASAmount);
											foreach($CAS_amtArr as $key => $value){
												$CASAmount[$key] = numberFormat(trim($value),2,'yes');
											}
											$CASAmount = implode(', ', $CASAmount);
											if($CASAmount=="$"){
												$CASAmount="";
											}
											//DEDUCT AMOUNT
											if(($chkNumberDetails) && ($chkNumberDetails!=$TRN_payment_type_number)){
												continue;
												
											}
											
											
											$deduct_amount=$writeoff_amount=$pat_res_amount=0;
											$era_ret_cas_arr=era_reason_amt($getProcDetailRows['CAS_type'],$getProcDetailRows['CAS_reason_code'],$getProcDetailRows['CAS_amt'],$getProcDetailRows['835_Era_proc_Id']);
											$writeoff_amount=$era_ret_cas_arr['era_writeoff'];
											$deduct_amount=$era_ret_cas_arr['era_deduct'];
											$pat_res_amount=$era_ret_cas_arr['era_pat_res_amount'];
											
											//-------------------- POST CHARGES MATCH BASIS ON ACC ID AND UNITS.
											if($matchCase=="Unit"){
												$getChargeListDetailsStr = "SELECT * FROM 
																			patient_charge_list a,
																			patient_charge_list_details b
																			WHERE a.patient_id = '$CLP_claim_submitter_id'
																			AND a.charge_list_id = b.charge_list_id
																			AND b.units = '$unit'
																			AND a.date_of_service = '$DOS'";
											}
											//-------------------- POST CHARGES MATCH BASIS ON ACC ID AND UNITS.
											$Total_SVC_proc_charge = $Total_SVC_proc_charge + $SVC_proc_charge;
											$Total_SVC_proc_charge_chq = $Total_SVC_proc_charge_chq + $SVC_proc_charge;
											$Total_AMT_amount = $Total_AMT_amount + $AMT_amount;
											$Total_AMT_amount_chq = $Total_AMT_amount_chq + $AMT_amount;
											$Total_deductAmount = $Total_deductAmount + $deductAmount;
											$Total_deductAmount_chq = $Total_deductAmount_chq + $deductAmount;
											$Total_pat_res_amount = $Total_pat_res_amount + $pat_res_amount;
											$Total_pat_res_chq = $Total_pat_res_chq + $pat_res_amount;
											
											$proc_SVC_proc_charge_chq= $proc_SVC_proc_charge_chq + $SVC_proc_charge;
											$proc_AMT_amount_chq=$proc_AMT_amount_chq + $AMT_amount;
											$proc_deductAmount_chq=$proc_deductAmount_chq + $deductAmount;
											$proc_pat_res_chq=$proc_pat_res_chq + $pat_res_amount;
											
											
											$getClpCasStr = "SELECT * FROM era835clpcas WHERE 
												era835Id = '$Era_835_Id'
												AND ERAPatientdetailsId = '$ERA_patient_details_id'";
											$getClpCasQry = imw_query($getClpCasStr);
											if(imw_num_rows($getClpCasQry)>0){
												while($getClpCasRows = imw_fetch_array($getClpCasQry)){
													$enc_level_adj_arr[$getClpCasRows['clpCasId']] = $getClpCasRows['casReasonAmt'];
												}
												if($counyPayments==1){
													$SVC_provider_pay_amt=$CLP_claim_payment_amount;
												}
											}
												$Total_SVC_provider_pay_amt = $Total_SVC_provider_pay_amt + $SVC_provider_pay_amt;
												$Total_SVC_provider_pay_amt_chq = $Total_SVC_provider_pay_amt_chq + $SVC_provider_pay_amt;
												$proc_SVC_provider_pay_amt_chq=$proc_SVC_provider_pay_amt_chq + $SVC_provider_pay_amt;
												if($TRN_payment_type_number!=$checkNo) {
													if($i != 0 && $send_era_pat_id_exp[0]<=0){										
														   $Total_SVC_proc_charge_chq = $SVC_proc_charge;
														   $Total_AMT_amount_chq = $AMT_amount;
														   $Total_deductAmount_chq = $deductAmount;
														   $Total_SVC_provider_pay_amt_chq = $SVC_provider_pay_amt;
														   $Total_pat_res_chq = $pat_res_amount;
												   	}
                                                    $providerId = explode(",", $getDetailsExistsOrNotRow['REF_payee_add_info']);
													$printPdf.='<tr height="20" style="background-color:#EAF0F7;">
														<td class="text11" align="left" colspan="7" style="padding:5px; background-color:#EAF0F7;">
															<b>'.$N1_payer_name.'</b><br>'.$N3_payer_address.'<br>'.$N4_payer_city.', '.$N4_payer_state.' '.$N4_payer_zip;
															if($getDetailsExistsOrNotRow['N3_payee_address']){
																$printPdf.='<br><br><b>'.$getDetailsExistsOrNotRow['N3_payee_address'].'</b><br>'.$getDetailsExistsOrNotRow['N4_payee_city'].', '.$getDetailsExistsOrNotRow['N4_payee_state'].' '.$getDetailsExistsOrNotRow['N4_payee_zip'];
															}
													$printPdf.='</td>
														<td class="text11" align="left" valign="top" colspan="8" style="padding:5px; background-color:#EAF0F7;">
															<b>Provider # :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>'.$providerId[1].'<br>
															<b>Check Date :&nbsp;&nbsp;&nbsp;&nbsp;</b>
															'.get_date_format($getDetailsExistsOrNotRow['chk_issue_EFT_Effective_date']).'<br>
															<b>Amount :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>'.numberFormat($provider_payment_amount_chk,2).'<br>
															<b>Check / EFT # :&nbsp;</b>'.$TRN_payment_type_number.'
														</td>
													</tr>';
												}
												
												$pdfPosted=($procPostedStatus=='Posted')? 'P' : 'NP';
												$pdfCLMClaim=($tempId!=$CLP_claim_submitter_id)? $NM1_last_name.', '.$NM1_first_name.' - '.$CLP_claim_submitter_id : "&nbsp;";
												$pdfEID=($encounter_id!='' && $encounter_id>0)? $encounter_id : '-';
												$pdfSVCCode=($SVC_proc_code)? $SVC_proc_code : '&nbsp;';
												$pdfSVCCharge=($SVC_proc_charge!='') ? numberFormat($SVC_proc_charge, 2): '&nbsp;';
												$pdfAMTAmount=($AMT_amount!='') ? numberFormat($AMT_amount, 2): '&nbsp;';
												
												$era_p_np_arr['status'][$ERA_patient_details_id]=$procPostedStatus;
												$era_p_np_arr['charges'][$ERA_patient_details_id]=$era_p_np_arr['charges'][$ERA_patient_details_id]+$getProcDetailRows['SVC_proc_charge'];
												$era_p_np_arr['paid'][$ERA_patient_details_id]=$era_p_np_arr['paid'][$ERA_patient_details_id]+$getProcDetailRows['SVC_provider_pay_amt'];
												$era_p_np_arr['deduct'][$ERA_patient_details_id]=$era_p_np_arr['deduct'][$ERA_patient_details_id]+$deductAmount;
												$era_p_np_arr['writeoff'][$ERA_patient_details_id]=$era_p_np_arr['writeoff'][$ERA_patient_details_id]+$writeoff_amount;
												
												//$icn=substr($icn,0,15);
												$printPdf.='<tr style="padding:5px 0px;">
												<td align="left" class="text_10" valign="top">'.$pdfPosted.'</td>
												<td align="left" class="text_10" style="width:140px;" valign="top">'.$pdfCLMClaim.'</td>
												<td align="center" class="text_10" style="width:40px;" valign="top">'.$pdfEID.'</td>
												<td align="left" class="text_10" style="width:130px;" valign="top">'.substr($icn,0,20);
												if($hic_on!=""){
													$printPdf.=' / '.$NM1_patient_id;
												}
												$printPdf.='</td>
												<td align="center" class="nowrap text_10" style="width:40px;" valign="top">'.$DTM_date.'</td>
												<td align="center" class="text_10" style="width:60px;" valign="top">'.$pdfSVCCode.'</td>
												<td style="text-align:right; width:60px;" class="text_10" valign="top">'.$pdfSVCCharge.'</td>
												<td style="text-align:right; width:60px;" class="text_10" valign="top">'.$pdfAMTAmount.'</td>
												<td style="text-align:right; width:50px;" class="text_10" valign="top">'.numberFormat($deductAmount, 2).'</td>
												<td style="text-align:right; width:70px;" class="text_10" valign="top">'.numberFormat($SVC_provider_pay_amt, 2).'</td>
												<td style="text-align:right; width:50px;" class="text_10" valign="top">'.numberFormat($pat_res_amount, 2).'</td>
                                                <td align="center" class="text_10" style="width:50px;" valign="top">'.$CASTypeCode.'</td>
												<td align="center" class="text_10" style="width:50px;" valign="top">'.$CASAmount.'</td>
                                                <td align="center" class="text_10" style="width:64px;" valign="top">'.$rem_code.'</td>
												<td class="text_10" style="text-align:left; width:64px;" valign="top">'.$processed_by.'</td>
											</tr>';
											$tempId = $CLP_claim_submitter_id;
											$checkNo = $TRN_payment_type_number;
										}
									}
									
									if($NM1_type=='TT'){
										$secondaryInsName = stripslashes($getNM1DetailsRow['NM1_last_name']);
									}
								}
								$printPdf.='
								<tr>
								<td colspan="4" class="text11b" style="text-align:left;padding-left:5px; ">'.$secondaryInsName.'</td>
								<td colspan="2" class="text11b" style="text-align:right;padding-right:5px; ">CPT Total :</td>
								<td style="text-align:right" class="text11b">'.numberFormat(trim($proc_SVC_proc_charge_chq),2,'yes').'</td>
								<td style="text-align:right" class="text11b">'.numberFormat(trim($proc_AMT_amount_chq),2,'yes').'</td>
								<td style="text-align:right" class="text11b">'.numberFormat(trim($proc_deductAmount_chq),2,'yes').'</td>
								<td style="text-align:right" class="text11b">'.numberFormat(trim($proc_SVC_provider_pay_amt_chq),2,'yes').'</td>
								<td style="text-align:right" class="text11b">'.numberFormat(trim($proc_pat_res_chq),2,'yes').'</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								</tr>';
							}
							$i++;
							if(imw_num_rows($getPatientDetailsQry)>0 && $send_era_pat_id_exp[0]<=0){
								$printPdf.='
								 <tr>
									<td colspan="6" class="text11b" style="text-align:right;padding-right:5px; ">Check/EFT Total : </td>
									<td style="text-align:right" class="text11b">$'.number_format($Total_SVC_proc_charge_chq,2).'</td>
									<td style="text-align:right" class="text11b">$'.number_format($Total_AMT_amount_chq,2).'</td>
									<td style="text-align:right" class="text11b">$'.number_format($Total_deductAmount_chq,2).'</td>
									<td style="text-align:right" class="text11b">$'.number_format($Total_SVC_provider_pay_amt_chq,2).'</td>
									<td style="text-align:right" class="text11b">$'.number_format($Total_pat_res_chq,2).'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>';
							}
						}
					}else{
						$printPdf.='
						<tr>
							<td colspan="15" style="text-align:center" class="text_10b">No Record Found!</td>
						</tr>';
					}
					
					if($send_era_pat_id_exp[0]<=0){
						$file_data=$fileDetails->file_contents;
						$plb_exp_data=explode('~',$file_data);
						$plb_amt_arr=array();
						for($j=0;$j<count($plb_exp_data);$j++){
							if(strstr($plb_exp_data[$j],'PLB')){
								$plb_exp_final_data=explode('*',$plb_exp_data[$j]);
								for($k=0;$k<count($plb_exp_final_data);$k++){
									if($k>2){
										$plb_res_code_arr=explode(':',$plb_exp_final_data[$k]);
										$plb_amt_arr[]=$plb_exp_final_data[$k+1];
										$k++;
									}
								}
							}
						}
					}
                 	if(count($plb_amt_arr)>0 || count($enc_level_adj_arr)>0){
						$printPdf.='<tr height="20">
							<td align="right" style="padding-right:5px; text-align:right;" colspan="6" class="text11b">Total :</td>
							<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_SVC_proc_charge),2,'yes').'</td>
							<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_AMT_amount),2,'yes').'</td>
							<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_deductAmount),2,'yes').'</td>
							<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_SVC_provider_pay_amt),2,'yes').'</td>
							<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_pat_res_amount),2,'yes').'</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>';
                	}
					if(count($plb_amt_arr)>0){
						$printPdf.='<tr height="20">
							<td align="right" style="padding-right:5px; text-align:right;" colspan="6" class="text11b">PLB Amount:</td>
							<td style="text-align:right;" class="text11b"></td>
							<td style="text-align:right;" class="text11b"></td>
							<td style="text-align:right;" class="text11b"></td>
							<td style="text-align:right;" class="text11b">'.numberFormat(trim(array_sum($plb_amt_arr)),2,'yes').'</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>';
					}
					if(count($enc_level_adj_arr)>0){
						$printPdf.='<tr height="20">
							<td align="right" style="padding-right:5px; text-align:right;" colspan="6" class="text11b">Encounters Level Adjustment Amount:</td>
							<td style="text-align:right;" class="text_10b"></td>
							<td style="text-align:right;" class="text_10b"></td>
							<td style="text-align:right;" class="text_10b"></td>
							<td style="text-align:right;" class="text11b">'.numberFormat(trim(array_sum($enc_level_adj_arr)),2,'yes').'</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>';
					}

				$Total_SVC_provider_pay_amt=$Total_SVC_provider_pay_amt-array_sum($plb_amt_arr)-array_sum($enc_level_adj_arr);
                $printPdf.='<tr height="20">
					<td align="right" style="padding-right:5px; text-align:right;" colspan="6" class="text11b">Grand Total :</td>
                    <td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_SVC_proc_charge),2,'yes').'</td>
					<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_AMT_amount),2,'yes').'</td>
					<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_deductAmount),2,'yes').'</td>
					<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_SVC_provider_pay_amt),2,'yes').'</td>
					<td style="text-align:right;" class="text11b">'.numberFormat(trim($Total_pat_res_amount),2,'yes').'</td>
                    <td></td>
                    <td></td>
					<td></td>
					<td></td>
				</tr></table>';

                if(count($plb_amt_arr)>0 && $send_era_pat_id_exp[0]<=0){
                 $printPdf.='
                        <table border="1" cellpadding="0" cellspacing="2" style="background-color:#EEE4D0" width="100%">
                            <tr class="text_b" height="25" bgcolor="#4684ab">
                                <td class="text_b_w" style="width:100px; text-align:center">PLB</td>
                                <td class="text_b_w" style="width:189px; text-align:center">Provider Name</td>
                                <td class="text_b_w" style="width:189px; text-align:center">Patient Name</td>
                                <td class="text_b_w" style="width:189px; text-align:center">Encounter Id</td>
                                <td class="text_b_w" style="width:189px; text-align:center">Reason&nbsp;Code</td>
                                <td class="text_b_w" style="width:189px; text-align:center">Amount</td>
                            </tr>';
                                $plb_srn=0;
								for($j=0;$j<count($plb_exp_data);$j++){
									if(strstr($plb_exp_data[$j],'PLB')){
										$plb_srn++;
										$final_plb_code_arr=array();
										$plb_exp_final_data=explode('*',$plb_exp_data[$j]);
										//print_r($plb_exp_final_data);
										for($k=0;$k<count($plb_exp_final_data);$k++){
											if($k>2){
												$plb_res_code_arr=explode(':',$plb_exp_final_data[$k]);
												$final_plb_code_arr['reason_code'][]=$plb_res_code_arr[0];
												if(count($plb_res_code_arr)>1){
													$final_plb_code_arr['clp_control_number'][trim($plb_res_code_arr[1])]=trim($plb_res_code_arr[1]);
												}
												$final_plb_code_arr['reason_amount'][]=numberFormat(trim($plb_exp_final_data[$k+1]),2,'yes');
												$k++;
											}
										}
										$ein_num=$plb_exp_final_data[1];
										$usr_qry=imw_query("select fname,mname,lname from users where user_npi='$ein_num'");
										$usr_row=imw_fetch_array($usr_qry);
										$physician_name_arr = array();		
										$physician_name_arr["LAST_NAME"] = $usr_row['lname'];
										$physician_name_arr["FIRST_NAME"] = $usr_row['fname'];
										$physician_name_arr["MIDDLE_NAME"] = $usr_row['mname'];
										if(imw_num_rows($usr_qry)==0){
											$ref_usr_qry=imw_query("select FirstName,MiddleName,LastName from refferphysician where NPI='$ein_num'");
											$ref_usr_row=imw_fetch_array($ref_usr_qry);
											$physician_name_arr["LAST_NAME"] = $ref_usr_row['LastName'];
											$physician_name_arr["FIRST_NAME"] = $ref_usr_row['FirstName'];
											$physician_name_arr["MIDDLE_NAME"] = $ref_usr_row['MiddleName'];
										}
										$physician_name = changeNameFormat($physician_name_arr);
										$patientName_frm_arr=array();
										$Encounter_frm_arr=array();
										foreach($final_plb_code_arr['clp_control_number'] as $val){
											$final_val="";
											$final_val_arr=explode('/',$val);
											if(count($final_val_arr)>1){
												$final_val_arr=explode(' ',$final_val_arr[1]);
											}else{
												$final_val_arr=explode(' ',$val);
											}
											$final_val=str_replace("DEFER","",$final_val_arr[0]);
											$clp_qry=imw_query("select CLP_claim_submitter_id,ERA_patient_details_id from era_835_patient_details where CLP_payer_claim_control_number like '$final_val%'");
											if(imw_num_rows($clp_qry)>0){
												while($clp_row=imw_fetch_array($clp_qry)){
													$CLP_patient_id=$clp_row['CLP_claim_submitter_id'];
													$CLP_ERA_patient_details_id=$clp_row['ERA_patient_details_id'];
													$pat_qry=imw_query("select fname,lname from patient_data where id='$CLP_patient_id'");
													$pat_row=imw_fetch_array($pat_qry);
													if(imw_num_rows($pat_qry)>0){
														$plb_patient_arr[$CLP_patient_id]=$CLP_patient_id;
														$fname_frm = $pat_row['fname'];
														$lname_frm = $pat_row['lname'];
														$mname_frm = $pat_row['mname'];
														$patientName_frm_arr[$CLP_patient_id] = ucwords(trim($lname_frm.", ".$fname_frm)).' - '. $CLP_patient_id;
														$getPatProcDetailsStr = "SELECT REF_prov_identifier FROM era_835_proc_details WHERE `ERA_patient_details_id` = '$CLP_ERA_patient_details_id'";
														$getPatProcDetailsQry = imw_query($getPatProcDetailsStr);				
														while($getPatProcDetailsRow = imw_fetch_array($getPatProcDetailsQry)){
															$REF_prov_identifier_exp=explode('MCR',$getPatProcDetailsRow['REF_prov_identifier']);
															$Encounter_frm_arr[$REF_prov_identifier_exp[0]]=$REF_prov_identifier_exp[0];
														}
													}
												}
											}else{
												$CLP_patient_id=$final_val_arr[1];
												$plb_DTM_date=$final_val_arr[0];
												$pat_qry=imw_query("select fname,lname from patient_data where id='$CLP_patient_id'");
												$pat_row=imw_fetch_array($pat_qry);
												if(imw_num_rows($pat_qry)>0 && strlen($plb_DTM_date)==8){
													$plb_patient_arr[$CLP_patient_id]=$CLP_patient_id;
													$fname_frm = $pat_row['fname'];
													$lname_frm = $pat_row['lname'];
													$mname_frm = $pat_row['mname'];
													$patientName_frm_arr[$CLP_patient_id] = ucwords(trim($lname_frm.", ".$fname_frm)).' - '. $CLP_patient_id;
													$getPatProcDetailsStr = "SELECT era_835_proc_details.REF_prov_identifier FROM era_835_proc_details join era_835_patient_details 
													on era_835_proc_details.ERA_patient_details_id=era_835_patient_details.ERA_patient_details_id 
													WHERE era_835_patient_details.CLP_claim_submitter_id = '$CLP_patient_id' and REPLACE(era_835_proc_details.DTM_date,'-','')='$plb_DTM_date' limit 0,1";
													$getPatProcDetailsQry = imw_query($getPatProcDetailsStr);				
													while($getPatProcDetailsRow = imw_fetch_array($getPatProcDetailsQry)){
														$REF_prov_identifier_exp=explode('MCR',$getPatProcDetailsRow['REF_prov_identifier']);
														$Encounter_frm_arr[$REF_prov_identifier_exp[0]]=$REF_prov_identifier_exp[0];
													}
												}
											}
										} 

									$printPdf.='<tr>
										<td align="center" class="text_10" valign="top" style="background:#FFF">'.$plb_srn.'</td>
										<td align="center" class="text_10" valign="top" style="background:#FFF">'.$physician_name.'</td>
										<td align="center" class="text_10" valign="top" style="background:#FFF">'.implode('<br> ',$patientName_frm_arr).'</td>
										<td align="center" class="text_10" valign="top" style="background:#FFF">'.implode('<br> ',$Encounter_frm_arr).'</td>
										<td align="center" class="text_10" valign="top" style="background:#FFF">'.implode(', ',$final_plb_code_arr['reason_code']).'</td>
										<td align="center" class="text_10" valign="top" style="background:#FFF">'.implode(', ',$final_plb_code_arr['reason_amount']).'</td>
									</tr>';
									}
								}
								if($plb_srn==0){
								$printPdf.='
								 <tr>
									<td class="text_10b" style="text-align:center; background:#FFF" colspan="6">No PLB Record Found.</td>
								</tr>';
							}
                     $printPdf.='</table>';
                }
			if($send_era_pat_id_exp[0]<=0){	
				$printPdf.='	
				<table border="1" cellpadding="0" cellspacing="2" style="background-color:#EEE4D0" width="100%">
					<tr class="text_b" height="25">
						<td class="text_b_w" style="padding-left:5px; width:100px; text-align:center">Status</td>
						<td class="text_b_w" style="width:188px; text-align:center"># of Patients</td>
						<td class="text_b_w" style="width:188px; text-align:center">Amount</td>
						<td class="text_b_w" style="width:108px; text-align:center">Paid</td>
						<td class="text_b_w" style="width:188px; text-align:center">Write Off</td>
						<td class="text_b_w" style="width:188px; text-align:center">Deductible</td>
					</tr>';
					
					foreach($era_p_np_arr['status'] as $p_np_key=>$p_np_val){
						$st=$era_p_np_arr['status'][$p_np_key];
						$era_sum['status'][$st][]=$era_p_np_arr['status'][$p_np_key];
						$era_sum['charges'][$st][]=$era_p_np_arr['charges'][$p_np_key];
						$era_sum['paid'][$st][]=$era_p_np_arr['paid'][$p_np_key];
						$era_sum['writeoff'][$st][]=$era_p_np_arr['writeoff'][$p_np_key];
						$era_sum['deduct'][$st][]=$era_p_np_arr['deduct'][$p_np_key];
					}
					
					$pdfARE=$pdfApplied=$pdfPayment=$pdfWriteOff=$deductAmt=$pdfARENot=$pdfAppliedNot=$pdfPaymentNot=$pdfWriteOffNot=$deductAmtNot="";
					
					$pdfARE=count($era_sum['status']['Posted']);
					$pdfApplied=numberFormat(array_sum($era_sum['charges']['Posted']),2);
                    $pdfPayment=numberFormat(array_sum($era_sum['paid']['Posted']),2);
					$pdfWriteOff=numberFormat(array_sum($era_sum['writeoff']['Posted']),2);
                    $deductAmt=numberFormat(array_sum($era_sum['deduct']['Posted']),2);
                    
                    $pdfARENot=count($era_sum['status']['Not Posted']);
					$pdfAppliedNot=numberFormat(array_sum($era_sum['charges']['Not Posted']),2);
                    $pdfPaymentNot=numberFormat(array_sum($era_sum['paid']['Not Posted']),2);
					$pdfWriteOffNot=numberFormat(array_sum($era_sum['writeoff']['Not Posted']),2);
                    $deductAmtNot=numberFormat(array_sum($era_sum['deduct']['Not Posted']),2);
					
					$printPdf.='<tr height="20">
						<td class="text11b" style="padding-left:5px;background:#FFF; text-align:left">Applied</td>
						<td class="text11b" style="background:#FFF; text-align:center">'.$pdfARE.'</td>
						<td class="text11b" style="padding-right:15px; background:#FFF; text-align:right">'.$pdfApplied.'</td>
						<td class="text11b" style="padding-right:15px; background:#FFF; text-align:right">'.$pdfPayment.'</td>
						<td class="text11b" style="padding-right:15px; background:#FFF; text-align:right">'.$pdfWriteOff.'</td>
						<td class="text11b" style="background:#FFF; text-align:right">'.$deductAmt.'</td>
					</tr>
					<tr height="20">
						<td class="text11b" style="padding-left:5px; background:#FFF; text-align:left">Not Applied</td>
						<td class="text11b" style="background:#FFF; text-align:center">'.$pdfARENot.'</td>
						<td class="text11b" style="padding-right:15px; background:#FFF; text-align:right">'.$pdfAppliedNot.'</td>
						<td class="text11b" style="padding-right:15px; background:#FFF; text-align:right">'.$pdfPaymentNot.'</td>
						<td class="text11b" style="padding-right:15px; background:#FFF; text-align:right">'.$pdfWriteOffNot.'</td>
						<td class="text11b" style="background:#FFF; text-align:right">'.$deductAmtNot.'</td>
					</tr>
			  </table>
			  <div style="padding-top:25px;">&nbsp;</div>
			  <table border="1" cellpadding="0" cellspacing="2" style="background-color:#EEE4D0" width="100%">
					
					<tr class="text_b" height="25">
						<td class="text_b_w" colspan="2" style="padding-left:5px; text-align:center; width:700px;">CAS Code Details</td>
					</tr>
					<tr class="text_b" height="25">
						<td class="text_b_w" style="padding-left:5px; text-align:center;">CAS Code</td>
						<td class="text_b_w" style="padding-left:5px; text-align:center">CAS Description</td>
					</tr>';
					//print_r($cas_code_arr);
					for($j=0;$j<=count($cas_code_arr);$j++){
						$cas_code_exp=explode(', ',$cas_code_arr[$j]);
						//print_r($cas_code_exp);
						for($k=0;$k<=count($cas_code_exp);$k++){
							if(trim($cas_code_exp[$k])!=""){
								$cas_code_exp2=explode(' ',trim($cas_code_exp[$k]));
								$cas_siz=count($cas_code_exp2)-1;
								//print_r($cas_code_exp2);
								$final_cas_disc_arr[trim($cas_code_exp[$k])]=$all_cas_arr[$cas_code_exp2[$cas_siz]];
							}
							
						}
					}
					//print_r($final_cas_disc_arr);
					//exit();
					foreach($final_cas_disc_arr as $cas_key => $cas_val){
					$printPdf.='<tr height="20">
						<td class="text11b" style="padding-left:5px;background:#FFF; text-align:left">'.$cas_key.'</td>
						<td class="text11b" style="background:#FFF; text-align:left">'.$cas_val.'</td>
					</tr>';
					}
			  $printPdf.='</table>';
			}
$printPdf.='</page>';
if(trim($printPdf) != ""){
	$PdfText = $pdfCSS.$printPdf;
	$filePath=write_html($PdfText);
}
?>
<script type="text/javascript">
top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
html_to_pdf('<?php echo $filePath; ?>','l','','','','html_to_pdf_reports');
window.close();
</script>