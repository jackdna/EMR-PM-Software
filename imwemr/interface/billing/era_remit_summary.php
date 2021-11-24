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
require_once("../accounting/acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../library/classes/common_function.php"); 

$electronicFilesTblId = xss_rem($_REQUEST['era_file_id'], 3);	/* Reject parameter with unwanted values - Security Fix */

$EraId = $_REQUEST['era_file_chk_id'];	
if($EraId>0){
	$sub_id_whr=" and 835_Era_Id in($EraId)";
}

$qry=imw_query("select file_name from electronicfiles_tbl where id='$electronicFilesTblId'");
$fileDetails=imw_fetch_object($qry);
$file_name = $fileDetails->file_name;

// GET FILE DETAILS
$getFileDetailsInfoStr = "SELECT * FROM era_835_details WHERE `electronicFilesTblId` = '$electronicFilesTblId' $sub_id_whr";
$getFileDetailsInfoQry = imw_query($getFileDetailsInfoStr);
while($getFileDetailsInfoRow = imw_fetch_assoc($getFileDetailsInfoQry)){
	extract($getFileDetailsInfoRow);
	$Era_835_Id = $getFileDetailsInfoRow['835_Era_Id'];
	
	$N1_payer_name_arr[$getFileDetailsInfoRow['N1_payer_name']] = $getFileDetailsInfoRow['N1_payer_name'];
	$N3_payer_address_arr[$getFileDetailsInfoRow['N1_payer_name']] = $getFileDetailsInfoRow['N3_payer_address'];
	$N4_payer_city_arr[$getFileDetailsInfoRow['N1_payer_name']] = $getFileDetailsInfoRow['N4_payer_city'];
	$N4_payer_state_arr[$getFileDetailsInfoRow['N1_payer_name']] = $getFileDetailsInfoRow['N4_payer_state'];
	$N4_payer_zip_arr[$getFileDetailsInfoRow['N1_payer_name']] = $getFileDetailsInfoRow['N4_payer_zip'];
	
	$X12_standard_exp = explode(",", $X12_standard);
	$standard=$X12_standard_exp[0];
	$ver=$X12_standard_exp[1];
	
	//GET PAYMENT INFO
	$totalAmtChk = $totalAmtChk + $provider_payment_amount;
	$providerPaymentAmount_arr[] = $provider_payment_amount;
	if(!$TRNPaymentTypeNumber){
		$TRNPaymentTypeNumber = $TRN_payment_type_number;
		$chkIssueEFTEffectiveDate_arr[get_date_format($chk_issue_EFT_Effective_date)] = get_date_format($chk_issue_EFT_Effective_date);
	}else{
		$TRNPaymentTypeNumber = $TRNPaymentTypeNumber.', '.$TRN_payment_type_number;
		$chkIssueEFTEffectiveDate_arr[get_date_format($chk_issue_EFT_Effective_date)] = get_date_format($chk_issue_EFT_Effective_date);
	}
	
	// GET PATIENT DETAILS
	$getPatDetailsStr = "SELECT ERA_patient_details_id FROM era_835_patient_details WHERE 835_Era_Id = '$Era_835_Id'";
	$getPatDetailsQry = imw_query($getPatDetailsStr);
	while($getPatDetailsRows = imw_fetch_assoc($getPatDetailsQry)){
		$ERAPatientDetailsId = $getPatDetailsRows['ERA_patient_details_id'];				
	}
	
	// TOTAL ALLOWED AMT
	$getAMTAmountStr = "SELECT sum(AMT_amount) as amtTotalAmount,sum(SVC_proc_charge) as totalBilledAmount FROM era_835_proc_details WHERE `835_Era_Id` = '$Era_835_Id'";
	$getAMTAmountQry = imw_query($getAMTAmountStr);
	$getAMTAmountRow = imw_fetch_assoc($getAMTAmountQry);
	$totalAmtAllowed_arr[] = $getAMTAmountRow['amtTotalAmount'];
	$totalBilled_arr[] = $getAMTAmountRow['totalBilledAmount'];		
	
	// TOTAL CHECK AMOUNT
	$getTotalChkAmtStr = "SELECT sum(provider_payment_amount) as paymentAmt FROM era_835_details WHERE `835_Era_Id` = '$Era_835_Id'";
	$getTotalChkAmtQry = imw_query($getTotalChkAmtStr);
	$getTotalChkAmtRow = imw_fetch_assoc($getTotalChkAmtQry);
		$paymentTotalAmt_arr[] = $getTotalChkAmtRow['paymentAmt'];


	//GET DEDUCT AMOUNT
	$getDetailCASStr = "SELECT `CAS_type`, `CAS_reason_code`, `CAS_amt` FROM era_835_proc_details WHERE `835_Era_Id` = '$Era_835_Id'";
	$getDetailCASQry = imw_query($getDetailCASStr);
	while($getDetailCASRows = imw_fetch_assoc($getDetailCASQry)){
		$CAS_amt = $getDetailCASRows['CAS_amt'];
		$CAS_type = $getDetailCASRows['CAS_type'];
		$CAS_reason_code = $getDetailCASRows['CAS_reason_code'];
		unset($CAS_typeArr);
		unset($CAS_reason_codeArr);
		unset($CAS_amtArr);
		if(strpos($CAS_type, ",")){		
			$CAS_typeArr = explode(",",$CAS_type);
			$CAS_reason_codeArr = explode(",",$CAS_reason_code);			
			$CAS_amtArr = explode(",",$CAS_amt);
			foreach($CAS_typeArr as $k => $val){
				if((trim($CAS_typeArr[$k]) == 'PR' || trim($CAS_typeArr[$k]) == 'OA') && (trim($CAS_reason_codeArr[$k]) == '1')){
					$CAS_amtTotal_arr[] = $CAS_amtArr[$k];
				}
			}
		}else{
			if(($CAS_type == 'PR' || trim($CAS_type) == 'OA') && ($CAS_reason_code == '1')){
				$CAS_amtTotal_arr[] = $CAS_amt;
			}
		}
	}		
}
$chkIssueEFTEffectiveDate="";
$chkIssueEFTEffectiveDate=implode(', ',$chkIssueEFTEffectiveDate_arr);
$deductAmt = array_sum($CAS_amtTotal_arr);
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
<page>
<table class="table_collapse cellBorder3">
	<tr bgcolor="#4684ab" height="20">
		<td class="text_b_w" style="font-size:13px; text-align:left; width:350px;">
			&nbsp;&nbsp;Remit Summary				
		</td>
		<td class="text_b_w" style="font-size:13px; text-align:center; width:350px;">
			Provider Payment Summary Report				
		</td>
		<td class="text_b_w" style="font-size:13px; text-align:right; width:350px;">
			File Name: ('.$file_name.')&nbsp;&nbsp;
		</td>
	</tr>
</table>
<table class="table_collapse cellBorder3">
	<tr style="background-color:#EAF0F7;">
		<td class="text11" align="left" style="padding:5px; background-color:#EAF0F7; width:520px;" valign="top">';
			$ni_row=0;
			foreach($N1_payer_name_arr as $n1_key=>$n1_val){
			  if($ni_row>0){$printPdf.= '<br><br>';} 
			  if($N1_payer_name_arr[$n1_key]){
				 $printPdf.= '<b>'.$N1_payer_name_arr[$n1_key].
					'</b><br>'.$N3_payer_address_arr[$n1_key].
					'<br>'.$N4_payer_city_arr[$n1_key].', '.$N4_payer_state_arr[$n1_key].' '.$N4_payer_zip_arr[$n1_key];
				$ni_row++;
			  }else{ 
				$printPdf.= '-';
			  }
			} 
$printPdf.='</td>
		<td class="text11" align="left" valign="top" style="padding:5px; background-color:#EAF0F7; width:510px">
			<b>VER :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>'.$ver.'<br>
			<b>Provider Name :&nbsp;&nbsp;&nbsp;</b>'.$N1_payee_name.'<br>
			<b>NPI # :&nbsp;</b>'.$N1_payee_id.'<br>
			<b>Check Date :&nbsp;&nbsp;&nbsp;</b>'.$chkIssueEFTEffectiveDate.'<br>
			<b>Check / EFT # :&nbsp;</b>'.$TRNPaymentTypeNumber.'
		</td>
	</tr></table>
<table class="table_collapse">
	<tr><td style="padding:5px;" colspan="2"></td></tr>
	<tr>
		<td width="250" align="left" class="text11" style="padding:5px;"><b>Billed Amount : </b></td>
		<td align="left" class="text11">'.numberFormat(array_sum($totalBilled_arr), 2).'</td>
	</tr>
	<tr height="25">
		<td align="left" class="text11" style="padding:5px;"><b>Total Reason Code Adjustment Amount : </b></td>
		<td align="left" class="text11"></td>
	</tr>
	<tr height="25">
		<td align="left" class="text11" style="padding:5px;"><b>Total Allowed Amount : </b></td>
		<td align="left" class="text11">'.numberFormat(array_sum($totalAmtAllowed_arr), 2).'</td>
	</tr>
	<tr height="25">
		<td align="left" class="text11" style="padding:5px;"><b>Total Co-Insurance Amount : </b></td>
		<td align="left" class="text11"></td>
	</tr>
	<tr height="25">
		<td align="left" class="text11" style="padding:5px;"><b>Total Deductible Amount : </b></td>
		<td align="left" class="text11">'.numberFormat($deductAmt, 2).'</td>
	</tr>
	<tr height="25">
		<td align="left" class="text11" style="padding:5px;"><b>Total Paid to Provider : </b></td>
		<td align="left" class="text11">'.numberFormat(array_sum($providerPaymentAmount_arr), 2).'</td>
	</tr>
	<tr height="25">
		<td align="left" class="text11" style="padding:5px;"><b>Total Interest Amount : </b></td>
		<td align="left" class="text11">'.numberFormat($interestAmt, 2).'</td>
	</tr>
	<tr height="25">
		<td align="left" class="text11" style="padding:5px;"><b>Total Check/EFT Amount : </b></td>
		<td align="left" class="text11">'.numberFormat(array_sum($paymentTotalAmt_arr), 2).'</td>
	</tr>
</table>';
$printPdf.='</page>';
if(trim($printPdf) != ""){
	$PdfText = $pdfCSS.$printPdf;
	$filePath=write_html($PdfText);
}
?>
<script type="text/javascript">
top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
html_to_pdf('<?php echo $filePath; ?>','l');
window.close();
</script>