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
/*
FILE : LEDGER_DETAIL_CHECK.PHP
PURPOSE : LEDGER REPORT IN DETAILS
ACCESS TYPE : INCLUDED
*/
$totalChargeAmt = 0;
$sumTotalPaid = 0;
$sumCoPayWriteOff = 0;
$sumTotalBalance = 0;
$grand_total_chek_arr = array();
$arrAllCheckEncs = array();

$grandSumTotalPaidCash =0;
$grandSumTotalPaidCheck =0;
$grandSumTotalPaidCC =0;
$not_posted_data = '';
$tempEncIds=array();

$colspan=6;
//--- ALL POSTED CHARGES -------
if(count($mainResCheckArr['posted_charges'])>0){
	
	$pdf_page_header = $page_header;
	
	$posted_data = '';
	$patientCnt = 0;
	$sub_adj_amt_arr = array();
	$sub_writeoff_amt_arr = array();
	$arrCheckNo =array();
	$checkCount=1;
	$arrChkTotalAmt = 0;
	$arrChkPaidAmount = 0;
	$arrChk_write_off_amount = 0;
	$arrChkTotalBalance = 0;
	$arrChkAdjAmt = 0;	
	$arrChkWriteoffAmt = 0;	
	$oldCheckNo='';

	foreach($mainResCheckArr['posted_charges'] as $check_number => $data){	
		$paymentModeTitleArr=array();
		$arrCheckTotals=array();
			
		foreach($data as $encounter_id =>$encounter_data){	
			$printFile=true;
			$cptCodeArr = array();
			$diagnosis_id = array();
			$modArr = array();
			$paidByArr = array();
			$dateOfPayment = array();
			$payment_modeArr=array();
			$write_off_amount = 0;
			$adj_amount = 0;
			$totalBalance = 0;
			$totalAmt = 0;
			$encOverPayment = 0;
			$paidAmount = 0;

			foreach($encounter_data as $charge_list_detail_arr){
				$charge_list_detail_id = $charge_list_detail_arr['charge_list_detail_id'];
				$deptId=$charge_list_detail_arr['departmentId'];
				$tempTotalAmt = $charge_list_detail_arr['totalAmt'];
				$totalAmt +=$tempTotalAmt;
				$balForProc = $charge_list_detail_arr['proc_balance'];
				$totalBalance += $balForProc;
				$encOverPayment+= $charge_list_detail_arr['over_payment'];
				$write_off = $normalWriteOffCheckAmt[$check_number][$charge_list_detail_id] + $writte_off_check_arr[$check_number][$charge_list_detail_id];
				$write_off_amount+= $write_off;
				
				$adj_amount+=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				$tempChargeEncForReSub[$encounter_id] = $encounter_id;

				//PAYMENTS
				$paid_amount=0;
				$paidDetArr=$patPayCheckDetArr[$check_number][$charge_list_detail_id];

				$paid_amount=array_sum($paidDetArr['paid']);
				$paidAmount+=$paid_amount;
				
				//PAID BY
				if($paidDetArr['patPaid']>0){
					$paidByArr['Patient'] = 'Patient';
				}
				else if($paidDetArr['insPaid']>0){
					$insProviderId=$paidDetArr['insComp'];
					$paidByArr[ucfirst($arrAllInsCompanies[$insProviderId])] = ucfirst($arrAllInsCompanies[$insProviderId]);
				}
				else{
					$paidByArr['R.Party'] = 'R.Party';
				}
				
				//CHECK PAYMENT MODE
				foreach($paidDetArr['method'] as $payment_mode){
					if(strtolower($payment_mode) == 'check'){
						//$mode='Check - '.substr($check_number,-5);
						$mode='Check - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_CHECK'][] = $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
						$paymentModeTitleArr['Check']='Check';
					}
					else if(strtolower($payment_mode) == 'money order'){
						//$mode='MO - '.substr($check_number,-5);
						$mode='MO - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_MO'][] = $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
						$paymentModeTitleArr['Money Order']='Money Order';
					}
					else if(strtolower($payment_mode) == 'eft'){
						//$mode='EFT - '.substr($check_number,-5);
						$mode='EFT - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_EFT'][] = $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
						$paymentModeTitleArr['EFT']='EFT';
					}						
				}
				
				if(!$tempChgDetArr[$charge_list_detail_id]){
					//CHECK TOTAL
					$arrCheckTotals['charges']+=$tempTotalAmt;
					$arrCheckTotals['balance']+=$balForProc - $charge_list_detail_arr['over_payment'];
					//DEPARTMENT						
					$arrDeptDetails[$deptId]['CHARGES']+= $tempTotalAmt;
					$arrDeptDetails[$deptId]['BALANCE']+= $balForProc - $charge_list_detail_arr['over_payment'];
					
					$tempChgDetArr[$charge_list_detail_id]=$charge_list_detail_id;
				}
				//CHECK TOTAL
				$arrCheckTotals['paid_amt']+=$paid_amount;
				$arrCheckTotals['adj']+=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				$arrCheckTotals['write_off']+=$write_off;
				//DEPARTMENT						
				$arrDeptDetails[$deptId]['PAID']+= $paid_amount;
				$arrDeptDetails[$deptId]['ADJUSTMENT']+= $arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				$arrDeptDetails[$deptId]['WRITEOFF']+= $write_off;

				$arrAlreadyAddedInCheckView[$charge_list_detail_id]=$charge_list_detail_id;
			}
			
			$totalBalance = $totalBalance - $encOverPayment;
			//$tempCheckNo = substr($check_number,-5);
			$tempCheckNo = $check_number;
			$thisCheckSize =sizeof($arrCheckNoPosted[$main_groupBy_id][$checkNo]);
			
			$totPaid =0;
		}

		$arrChkPostedTotal['charges']+=$arrCheckTotals['charges'];
		$arrChkPostedTotal['paid_amt']+=$arrCheckTotals['paid_amt'];
		$arrChkPostedTotal['adj']+=$arrCheckTotals['adj'];
		$arrChkPostedTotal['write_off']+=$arrCheckTotals['write_off'];
		$arrChkPostedTotal['balance']+=$arrCheckTotals['balance'];

		$paymentModeTitleStr=(sizeof($paymentModeTitleArr)>0)? implode(', ',$paymentModeTitleArr): 'Check';
		//$check_title=$paymentModeTitleStr.' - '.substr($check_number,-5);
		$check_title=$paymentModeTitleStr.' - '.$check_number;

		$posted_data .='
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$check_title.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['charges'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['paid_amt'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['adj'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['write_off'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['balance'],2).'</td>
		</tr>';
	}
			
	//CHECK POSTED TOTAL
	$grand_total_chek_arr['TOTAL_CHARGES'][] = $arrChkPostedTotal['charges'];
	$grand_total_chek_arr['TOTAL_PAID'][] = $arrChkPostedTotal['paid_amt'];
	$grand_total_chek_arr['TOTAL_ADJ'][] = $arrChkPostedTotal['adj'];
	$grand_total_chek_arr['TOTAL_WRITEOFF'][] = $arrChkPostedTotal['write_off'];
	$grand_total_chek_arr['TOTAL_BALANCE'][] = $arrChkPostedTotal['balance'];

	$posted_check_data='
	<table class="rpt_table rpt rpt_table-bordered">
	<tr id="heading_orange">
		<td align="left" colspan="'.$colspan.'">Posted Charges</td>
	</tr>
	<tr>
		<td style="text-align:center;" class="text_b_w nowrap">Check#</td>
		<td style="text-align:center;" class="text_b_w">Charges</td>
		<td style="text-align:center;" class="text_b_w">Paid</td>
		<td style="text-align:center;" class="text_b_w">Adjustment</td>
		<td style="text-align:center;" class="text_b_w">Write-Off</td>
		<td style="text-align:center;" class="text_b_w">Balance</td>
	</tr>
	'.$posted_data.'
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Posted Total : </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["charges"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["paid_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["adj"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["write_off"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["balance"],2).'</td>
	</tr>
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	</table>';

	$postedPatientCnt = $patientCnt;
	$postedTotalChargeAmt = $totalChargeAmt;
	$postedSumTotalPaid = $sumTotalPaid;
	$postedSumCoPayWriteOff = $sumCoPayWriteOff;
	$posted_sub_adj_amt = $sub_adj_amt;
	$posted_sub_writeoff_amt = $sub_writeoff_amt;
	$postedSumTotalBalance = $sumTotalBalance;
}

//--- NOT POSTED CHARGES --------
$posted_charges = array();
$tempEncIds=array();
$tempChgDetArr=array();
$totalChargeAmt=0;
$sumTotalPaid=0;
$grandSumTotalPaidCheck=$grandSumTotalPaidMo=$grandSumTotalPaidEft=0;
if(count($mainResCheckArr['not_posted_charges'])>0){
	$not_posted_data = '';
	$patientCnt = 0;
	$sub_adj_amt_arr = array();
	$sub_writeoff_amt_arr = array();
	$arrCheckNo =array();
	$checkCount=1;
	$arrChkTotalAmt = 0;
	$arrChkPaidAmount = 0;
	$arrChk_write_off_amount = 0;
	$arrChkTotalBalance = 0;
	$arrChkAdjAmt = 0;	
	$oldCheckNo='';
	$tempEncIds = array();
	
	foreach($mainResCheckArr['not_posted_charges'] as $check_number => $data){
		$printFile=true;
		$arrCheckTotals=array();
		$paymentModeTitleArr=array();
					
		foreach($data as $encounter_id =>$encounter_data){
			$cptCodeArr = array();
			$diagnosis_id = array();
			$modArr=array();
			$payment_modeArr=array();
			$paidByArr=array();
			$dateOfPayment=array();
			$write_off_amount = 0;
			$adj_amount = 0;
			$totalBalance = 0;
			$totalAmt = 0;
			$encOverPayment=0;
	
			foreach($encounter_data as $charge_list_detail_arr){
				$charge_list_detail_id = $charge_list_detail_arr['charge_list_detail_id'];
				$deptId=$charge_list_detail_arr['departmentId'];
				$tempTotalAmt = $charge_list_detail_arr['totalAmt'];
				$totalAmt +=$tempTotalAmt;
				$balForProc = $charge_list_detail_arr['proc_balance'];
				$totalBalance += $balForProc;
				$encOverPayment+= $charge_list_detail_arr['over_payment'];
				
				$write_off = $normalWriteOffCheckAmt[$check_number][$charge_list_detail_id] + $writte_off_check_arr[$check_number][$charge_list_detail_id];
				$write_off_amount+= $write_off;
				$adj_amount+=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				
				$arrChargeDept[$charge_list_detail_id] = $deptId;
				$tempChargeEncForReSub[$encounter_id] = $encounter_id;
				
				//PAYMENTS
				$paid_amount=0;
				$paidDetArr=$patPayCheckDetArr[$check_number][$charge_list_detail_id];

				$paid_amount=array_sum($paidDetArr['paid']);
				$paidAmount+=$paid_amount;
				
				//PAID BY
				if($paidDetArr['patPaid']>0){
					$paidByArr['Patient'] = 'Patient';
				}
				else if($paidDetArr['insPaid']>0){
					$insProviderId=$paidDetArr['insComp'];
					$paidByArr[ucfirst($arrAllInsCompanies[$insProviderId])] = ucfirst($arrAllInsCompanies[$insProviderId]);
				}
				else{
					$paidByArr['R.Party'] = 'R.Party';
				}
				
				//CHECK PAYMENT MODE
				foreach($paidDetArr['method'] as $payment_mode){
					if(strtolower($payment_mode) == 'check'){
						//$mode='Check - '.substr($check_number,-5);
						$mode='Check - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_CHECK'][] = $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
						$paymentModeTitleArr['Check']='Check';
					}
					else if(strtolower($payment_mode) == 'money order'){
						//$mode='MO - '.substr($check_number,-5);
						$mode='MO - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_MO'][] = $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
						$paymentModeTitleArr['Money Order']='Money Order';
					}
					else if(strtolower($payment_mode) == 'eft'){
						//$mode='EFT - '.substr($check_number,-5);
						$mode='EFT - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_EFT'][] = $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
						$paymentModeTitleArr['EFT']='EFT';
					}						
				}
				
				if(!$tempChgDetArr[$charge_list_detail_id]){
					//CHECK TOTAL
					$arrCheckTotals['charges']+=$tempTotalAmt;
					$arrCheckTotals['balance']+=$balForProc - $charge_list_detail_arr['over_payment'];
					//DEPARTMENT						
					$arrDeptDetails[$deptId]['CHARGES']+= $tempTotalAmt;
					$arrDeptDetails[$deptId]['BALANCE']+= $balForProc - $charge_list_detail_arr['over_payment'];
					
					$tempChgDetArr[$charge_list_detail_id]=$charge_list_detail_id;
				}
				//CHECK TOTAL
				$arrCheckTotals['paid_amt']+=$paid_amount;
				$arrCheckTotals['adj']+=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				$arrCheckTotals['write_off']+=$write_off;
				//DEPARTMENT						
				$arrDeptDetails[$deptId]['PAID']+= $paid_amount;
				$arrDeptDetails[$deptId]['ADJUSTMENT']+= $arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				$arrDeptDetails[$deptId]['WRITEOFF']+= $write_off;
								
				$arrAlreadyAddedInCheckView[$charge_list_detail_id]=$charge_list_detail_id;
			}

			$totalBalance = $totalBalance - $encOverPayment;
			//$tempCheckNo = substr($check_number,-5);
			$tempCheckNo = $check_number;
			$thisCheckSize =sizeof($arrCheckNoPosted[$main_groupBy_id][$checkNo]);
			
		}


		$arrChkNotPostedTotal['charges']+=$arrCheckTotals['charges'];
		$arrChkNotPostedTotal['paid_amt']+=$arrCheckTotals['paid_amt'];
		$arrChkNotPostedTotal['adj']+=$arrCheckTotals['adj'];
		$arrChkNotPostedTotal['write_off']+=$arrCheckTotals['write_off'];
		$arrChkNotPostedTotal['balance']+=$arrCheckTotals['balance'];

		$paymentModeTitleStr=(sizeof($paymentModeTitleArr)>0)? implode(', ',$paymentModeTitleArr): 'Check';
		//$check_title=$paymentModeTitleStr.' - '.substr($check_number,-5);
		$check_title=$paymentModeTitleStr.' - '.$check_number;
		
		$not_posted_data .='
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$check_title.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['charges'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['paid_amt'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['adj'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['write_off'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['balance'],2).'</td>
		</tr>';
	}

	//GET GRAND TOTAL
	$grand_total_chek_arr['TOTAL_CHARGES'][] = $arrChkNotPostedTotal['charges'];
	$grand_total_chek_arr['TOTAL_PAID'][] = $arrChkNotPostedTotal['paid_amt'];
	$grand_total_chek_arr['TOTAL_ADJ'][] = $arrChkNotPostedTotal['adj'];
	$grand_total_chek_arr['TOTAL_WRITEOFF'][] = $arrChkNotPostedTotal['write_off'];
	$grand_total_chek_arr['TOTAL_BALANCE'][] = $arrChkNotPostedTotal['balance'];

	//NOT POSTED CONTENT
	$not_posted_check_data='
	<table class="rpt_table rpt rpt_table-bordered">
	<tr id="heading_orange">
		<td align="left" colspan="'.$colspan.'">Not Posted Charges</td>
	</tr>
	<tr>
		<td style="text-align:center;" class="text_b_w nowrap">Check#</td>
		<td style="text-align:center;" class="text_b_w">Charges</td>
		<td style="text-align:center;" class="text_b_w">Paid</td>
		<td style="text-align:center;" class="text_b_w">Adjustment</td>
		<td style="text-align:center;" class="text_b_w">Write-Off</td>
		<td style="text-align:center;" class="text_b_w">Balance</td>
	</tr>
	'.$not_posted_data.'
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Not Posted Total : </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["charges"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["paid_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["adj"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["write_off"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["balance"],2).'</td>
	</tr>
	</table>';
}


//RE SUBMITTED CHARGES
$totalChargeAmt =0;
$tempEncIds=array();
$tempChgDetArr=array();
$grandSumTotalPaidCheck=$grandSumTotalPaidMo=$grandSumTotalPaidEft=0;
if(count($mainResCheckArr['re_posted_charges'])>0){
	$sequences = 1;
	$total_paid_amount_arr = array();
	$totalPostedAmount = 0;
	$sub_adj_amt_arr = array();
	$sub_writeoff_amt_arr = array();
	$re_posted_data = '';
	$checkCount=1;

	$arrChkTotalAmt = 0;
	$arrChkPaidAmount = 0;
	$arrChk_write_off_amount = 0;
	$arrChkTotalBalance = 0;
	$arrChkAdjAmt = 0;	
	$arrChkBalAmt = 0;	
	$adjAmt=0;
	$oldCheckNo='';

	foreach($mainResCheckArr['re_posted_charges'] as $check_number => $data){
		$printFile=true;
		$arrCheckTotals=array();
		$paymentModeTitleArr=array();

		foreach($data as $encounter_id =>$encounter_data){
			$cptCodeArr = array();
			$diagnosis_id = array();
			$modArr=array();
			$payment_modeArr=array();
			$paidByArr=array();
			$dateOfPayment=array();
			$write_off_amount = 0;
			$adj_amount=0;
			$totalBalance = 0;
			$totalAmt = 0;
			$encOverPayment=0;
			$postedAmount=0;
			
			foreach($encounter_data as $charge_list_detail_arr){
				$tempTotalAmt=0;
				$charge_list_detail_id = $charge_list_detail_arr['charge_list_detail_id'];
				$deptId=$charge_list_detail_arr['departmentId'];
				$tempTotalAmt = $charge_list_detail_arr['totalAmt'];
				$totalAmt +=$tempTotalAmt;
				$balForProc = $charge_list_detail_arr['proc_balance'];
				$totalBalance += $balForProc;
				$encOverPayment+= $charge_list_detail_arr['over_payment'];

				$write_off = $normalWriteOffCheckAmt[$check_number][$charge_list_detail_id] + $writte_off_check_arr[$check_number][$charge_list_detail_id];
				$write_off_amount+= $write_off;
				$adj_amount+=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				
				$tempChargeEncForReSub[$encounter_id] = $encounter_id;
				
				//PAYMENTS
				$paid_amount=0;
				$paidDetArr=$patPayCheckDetArr[$check_number][$charge_list_detail_id];

				$paid_amount=array_sum($paidDetArr['paid']);
				$paidAmount+=$paid_amount;
				
				//PAID BY
				if($paidDetArr['patPaid']>0){
					$paidByArr['Patient'] = 'Patient';
				}
				else if($paidDetArr['insPaid']>0){
					$insProviderId=$paidDetArr['insComp'];
					$paidByArr[ucfirst($arrAllInsCompanies[$insProviderId])] = ucfirst($arrAllInsCompanies[$insProviderId]);
				}
				else{
					$paidByArr['R.Party'] = 'R.Party';
				}
				
				//CHECK PAYMENT MODE
				foreach($paidDetArr['method'] as $payment_mode){
					if(strtolower($payment_mode) == 'check'){
						//$mode='Check - '.substr($check_number,-5);
						$mode='Check - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_CHECK'][] = $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
						$paymentModeTitleArr['Check']='Check';
					}
					else if(strtolower($payment_mode) == 'money order'){
						//$mode='MO - '.substr($check_number,-5);
						$mode='MO - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_MO'][] = $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
						$paymentModeTitleArr['Money Order']='Money Order';
					}
					else if(strtolower($payment_mode) == 'eft'){
						//$mode='EFT - '.substr($check_number,-5);
						$mode='EFT - '.$check_number;
						$payment_modeArr[$mode] = $mode;
						$grand_total_chek_arr['TOTAL_EFT'][] = $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
						$paymentModeTitleArr['EFT']='EFT';
					}						
				}
				
				if(!$tempChgDetArr[$charge_list_detail_id]){
					//CHECK TOTAL
					$arrCheckTotals['charges']+=$tempTotalAmt;
					$arrCheckTotals['balance']+=$balForProc - $charge_list_detail_arr['over_payment'];
					//DEPARTMENT						
					$arrDeptDetails[$deptId]['CHARGES']+= $tempTotalAmt;
					$arrDeptDetails[$deptId]['BALANCE']+= $balForProc - $charge_list_detail_arr['over_payment'];
					
					$tempChgDetArr[$charge_list_detail_id]=$charge_list_detail_id;
				}
				//CHECK TOTAL
				$arrCheckTotals['paid_amt']+=$paid_amount;
				$arrCheckTotals['adj']+=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				$arrCheckTotals['write_off']+=$write_off;
				//DEPARTMENT						
				$arrDeptDetails[$deptId]['PAID']+= $paid_amount;
				$arrDeptDetails[$deptId]['ADJUSTMENT']+= $arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
				$arrDeptDetails[$deptId]['WRITEOFF']+= $write_off;
								
				$arrAlreadyAddedInCheckView[$charge_list_detail_id]=$charge_list_detail_id;
			}
					
			$totalBalance = $totalBalance - $encOverPayment;
			//$tempCheckNo = substr($check_number,-5);
			$tempCheckNo = $check_number;
			$thisCheckSize =sizeof($arrCheckNoPosted[$main_groupBy_id][$checkNo]);
			
		}
		
		// GROUP TOTALS
		$arrChkRePostedTotal['charges']+=$arrCheckTotals['charges'];
		$arrChkRePostedTotal['paid_amt']+=$arrCheckTotals['paid_amt'];
		$arrChkRePostedTotal['adj']+=$arrCheckTotals['adj'];
		$arrChkRePostedTotal['write_off']+=$arrCheckTotals['write_off'];
		$arrChkRePostedTotal['balance']+=$arrCheckTotals['balance'];
	
		$paymentModeTitleStr=(sizeof($paymentModeTitleArr)>0)? implode(', ',$paymentModeTitleArr): 'Check';
		//$check_title=$paymentModeTitleStr.' - '.substr($check_number,-5);
		$check_title=$paymentModeTitleStr.' - '.$check_number;
		
		$re_posted_data .='
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$check_title.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['charges'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['paid_amt'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['adj'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['write_off'],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrCheckTotals['balance'],2).'</td>
		</tr>';
	}	
	
	//GET GRAND TOTAL
	$grand_total_chek_arr['TOTAL_CHARGES'][] = $arrChkRePostedTotal['charges'];
	$grand_total_chek_arr['TOTAL_PAID'][] = $arrChkRePostedTotal['paid_amt'];
	$grand_total_chek_arr['TOTAL_ADJ'][] = $arrChkRePostedTotal['adj'];
	$grand_total_chek_arr['TOTAL_WRITEOFF'][] = $arrChkRePostedTotal['write_off'];
	$grand_total_chek_arr['TOTAL_BALANCE'][] = $arrChkRePostedTotal['balance'];

	//RE-SUBMITTED CONTENT
	$re_posted_check_data='
	<table class="rpt_table rpt rpt_table-bordered">
	<tr id="heading_orange">
		<td align="left" colspan="'.$colspan.'">Re-Submitted Charges</td>
	</tr>
	<tr>
		<td style="text-align:center;" class="text_b_w nowrap">Check#</td>
		<td style="text-align:center;" class="text_b_w">Charges</td>
		<td style="text-align:center;" class="text_b_w">Paid</td>
		<td style="text-align:center;" class="text_b_w">Adjustment</td>
		<td style="text-align:center;" class="text_b_w">Write-Off</td>
		<td style="text-align:center;" class="text_b_w">Balance</td>
	</tr>
	'.$re_posted_data.'
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Re-Submitted Total : </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["charges"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["paid_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["adj"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["write_off"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["balance"],2).'</td>
	</tr>
	</table>';
}

//GRAND TOTALS
if(sizeof($grand_total_chek_arr)>0){
	$grand_tot_charges=array_sum($grand_total_chek_arr['TOTAL_CHARGES']);
	$grand_tot_check=array_sum($grand_total_chek_arr['TOTAL_CHECK']);
	$grand_tot_mo=array_sum($grand_total_chek_arr['TOTAL_MO']);
	$grand_tot_eft=array_sum($grand_total_chek_arr['TOTAL_EFT']);
	$grand_tot_paid=array_sum($grand_total_chek_arr['TOTAL_PAID']);
	$grand_tot_adj=array_sum($grand_total_chek_arr['TOTAL_ADJ']);
	$grand_tot_writeoff=array_sum($grand_total_chek_arr['TOTAL_WRITEOFF']);
	$grand_tot_balance=array_sum($grand_total_chek_arr['TOTAL_BALANCE']);
	
	$totPaid=$grand_tot_check+$grand_tot_mo+$grand_tot_eft; 
	
	$grand_totals_check=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="8">Grand Totals (Check View)</td></tr>
	<tr>
        <td style="text-align:center; width:90px" class="text_b_w">Charges</td>
        <td style="text-align:center; width:90px" class="text_b_w">Check</td>
		<td style="text-align:center; width:90px" class="text_b_w">Money Order</td>
		<td style="text-align:center; width:90px" class="text_b_w">EFT</td>
		<td style="text-align:center; width:90px" class="text_b_w">Total Paid</td>
		<td style="text-align:center; width:90px" class="text_b_w">Adjustment</td>
		<td style="text-align:center; width:90px" class="text_b_w">Write-off</td>
        <td style="text-align:center; width:90px" class="text_b_w">Balance</td>
    </tr>
	<tr><td colspan="8" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_tot_charges,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_tot_check,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_tot_mo,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_tot_eft,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_tot_paid,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_tot_adj,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_tot_writeoff,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_tot_balance,2).'</td>
	</tr>	
	<tr><td colspan="8" class="total-row"></td></tr>
	</table>';		
}

// DEPARTMENT DETAILS
$department_data='';
if(sizeof($arrCheckDeptDetails)>0){
	$department_data.='
	<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="6">Department Summary</td></tr>
	<tr>
		<td class="text_b_w" style="text-align:center">Department</td>
		<td class="text_b_w" style="text-align:center">Charges</td>
		<td class="text_b_w" style="text-align:center">Total Paid</td>
		<td class="text_b_w" style="text-align:center">Adjustment</td>
		<td class="text_b_w" style="text-align:center">Write-Off</td>
		<td class="text_b_w" style="text-align:center">Balance</td>
	</tr>';

	foreach($arrCheckDeptDetails as $firstGrpId => $deptdata){
		$arrSubDept=array();
		
		if($viewBy=='physician'){
			$firstGrpTitle='Physician';
			$firstGrpName = $providerNameArr[$firstGrpId];
		}else{
			$firstGrpTitle='Facility';
			$firstGrpName = $arrAllFacilities[$firstGrpId];
		}
	
		$department_data .='<tr><td class="text_b_w" colspan="6">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		
		foreach($deptdata as $deptid => $deptDetails){
			
			$deptName= $arrDeptNames[$deptid];
			$arrSubDept['CHARGES']+=array_sum($deptDetails["CHARGES"]);
			$arrSubDept['PAID']+=array_sum($deptDetails["PAID"]);
			$arrSubDept['ADJUSTMENT']+=array_sum($deptDetails["ADJUSTMENT"]);
			$arrSubDept['WRITEOFF']+=array_sum($deptDetails["WRITEOFF"]);
			$arrSubDept['BALANCE']+=array_sum($deptDetails["BALANCE"]);

			$department_data.='
			<tr>
				<td style=" width:165px; text-align:left; background:#FFFFFF;" class="text_10">'.$deptName.'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat(array_sum($deptDetails["CHARGES"]),2).'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat(array_sum($deptDetails["PAID"]),2).'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat(array_sum($deptDetails["ADJUSTMENT"]),2).'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat(array_sum($deptDetails["WRITEOFF"]),2).'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat(array_sum($deptDetails["BALANCE"]),2).'</td>
			</tr>';	
		}

		$arrTotDept['CHARGES']+=$arrSubDept["CHARGES"];
		$arrTotDept['PAID']+=$arrSubDept["PAID"];
		$arrTotDept['ADJUSTMENT']+=$arrSubDept["ADJUSTMENT"];
		$arrTotDept['WRITEOFF']+=$arrSubDept["WRITEOFF"];
		$arrTotDept['BALANCE']+=$arrSubDept["BALANCE"];

		$department_data.='
		<tr><td colspan="6" class="total-row"></td></tr>
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">Sub Total:</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrSubDept["CHARGES"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrSubDept["PAID"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrSubDept["ADJUSTMENT"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrSubDept["WRITEOFF"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrSubDept["BALANCE"],2).'</td>
		</tr>
		<tr><td colspan="6" class="total-row"></td></tr>';			
	}
	
	$department_data.='
	<tr><td colspan="6" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Total:</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrTotDept["CHARGES"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrTotDept["PAID"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrTotDept["ADJUSTMENT"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrTotDept["WRITEOFF"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrTotDept["BALANCE"],2).'</td>
	</tr>
	<tr><td colspan="6" class="total-row"></td></tr>
	</table>';		
}

$page_header='
<table class="rpt_table rpt rpt_table-bordered rpt_padding">
    <tr >
        <td style="text-align:left;" class="rptbx1" width="350">Ledger (Detail)</td>
        <td style="text-align:left;" class="rptbx2" width="345">'.$dayReport.' ('.$selRangeType.') From : '.$Sdate.' To : '.$Edate.'</td>
        <td style="text-align:left;" class="rptbx3" width="345">Created by '.$opInitial.' on '.$curDate.'</td>
    </tr>
    <tr>
        <td class="rptbx1">Group : '.$selgroup.'</td>
        <td class="rptbx2">Facility : '.$selFac.'&nbsp;&nbsp;&nbsp;&nbsp;Phy. : '.$selPhy.'</td>
        <td class="rptbx3">Cr. Phy. : '.$selCrPhy.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Oper.: '.$selOpr.'</td>
    </tr>
    <tr>
        <td class="rptbx1">Dept. :'.$selDept.'</td>
        <td class="rptbx2">Insurance : '.$selInsurance.'
        </td>
        <td class="rptbx3">Batch : '.$batchFiles.'</td>
    </tr>
    <tr>
        <td class="rptbx1">Check# : '.$checkNo.'
        </td>
        <td class="rptbx2">Find by : '.$selAmtCriteria.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       		Check Amt : '.$checkAmt.'
        </td>
        <td class="rptbx3">Method : '.$selPayMethod.'</td>
    </tr>
</table>';

$csv_check_data=
$page_header.
$posted_check_data.
$not_posted_check_data.
$re_posted_check_data.
$grand_totals_check;

?>