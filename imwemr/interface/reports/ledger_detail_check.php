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
$colspan=15;

//MAKING OUTPUT DATA
$file_name="ledger_report".time().".csv";
$csv_file_name= write_html("", $file_name);
$pfx=",";
//CSV FILE NAME
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$data_output.="Ledger (Detail)".$pfx;
$data_output.="$dayReport ($selRangeType) From : $Sdate To : $Edate".$pfx;
$data_output.="Created by $opInitial on $curDate".$pfx;
$data_output.="\n";

$data_output.="Group : $selgroup".$pfx;
$data_output.="Facility : $selFac".$pfx;
$data_output.="Phy. : $selPhy     Selected Oper.: $selOpr".$pfx;
$data_output.="\n";

$data_output.="Dept. :$selDept".$pfx;
$data_output.="Insurance : $selInsurance".$pfx;
$data_output.="Batch : $batchFiles".$pfx;
$data_output.="\n";

$data_output.="Check# : $checkNo".$pfx;
$data_output.="Find by : $selAmtCriteria      Check Amt : '.$checkAmt".$pfx;
$data_output.="Method : $selPayMethod".$pfx;
$data_output.="\n";

$data_output.="Posted Payments".$pfx;
$data_output.="\n";
@fwrite($fp,$data_output);

//--- ALL POSTED CHARGES -------
if(count($mainResCheckArr['posted_charges'])>0){
	
	$pdf_page_header = $page_header=$data_output='';
	
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

	//FOR CSV
	$data_output.="Patient".$pfx;
	$data_output.="DOS".$pfx;
	$data_output.="CPT Desc.".$pfx;
	$data_output.="CPT Desc.".$pfx;
	$data_output.="DX Codes".$pfx;
	$data_output.="Modifiers".$pfx;
	$data_output.="Date of Charge".$pfx;
	$data_output.="Charges".$pfx;
	$data_output.="DOP".$pfx;
	$data_output.="Paid By".$pfx;
	$data_output.="Tot. Paid".$pfx;
	$data_output.="Method".$pfx;
	$data_output.="Adjustment".$pfx;	
	$data_output.="Write-Off".$pfx;
	$data_output.="Balance".$pfx;
	$data_output.="\n"; 	

	foreach($mainResCheckArr['posted_charges'] as $main_groupBy_id => $groupData){
		$printFile=true;
		$arrGroupTotals=array();

		$main_grp_title='Physician';
		$main_groupBy_name=$providerNameArr[$main_groupBy_id];
		if($viewBy=='facility'){
			$main_grp_title='Facility';
			$main_groupBy_name = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$main_groupBy_id] : $arrAllFacilities[$main_groupBy_id];			
		}

		if(count($groupData)>0){
			$posted_data .='<tr><td class="text_b_w" colspan="15">'.$main_grp_title.' : '.$main_groupBy_name.'</td></tr>';

			foreach($groupData as $check_number => $data){	
				$arrCheckTotals=array();
				//$posted_data .='<tr><td class="text_b_w" colspan="14">Check No - '.substr($check_number,-5).'</td></tr>';				
				$posted_data .='<tr><td class="text_b_w" colspan="15">Check No - '.$check_number.'</td></tr>';				

				foreach($data as $encounter_id =>$encounter_data){	
					$cptCodeArr = array();
					$cptDescArr = array();
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
						$deptid=$charge_list_detail_arr['departmentId'];
						$patientName = core_name_format($charge_list_detail_arr['lname'], $charge_list_detail_arr['fname'], $charge_list_detail_arr['mname']);		
	
						$patient_id = $charge_list_detail_arr['patient_id'];			
						if(empty($patientName) == false){
							$patientName .= ' - '.$patient_id;
						}
						
						$DOS = $charge_list_detail_arr['date_of_service'];
						$postedDate = $charge_list_detail_arr['postedDate'];			
						$cptCodeArr[] = $charge_list_detail_arr['cpt4_code'];
						$cptDescArr[] = $charge_list_detail_arr['cpt_desc'];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id1']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id1']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id2']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id2']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id3']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id3']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id4']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id4']];
						$tempTotalAmt = $charge_list_detail_arr['totalAmt'];
						$totalAmt +=$tempTotalAmt;
						$balForProc = $charge_list_detail_arr['proc_balance'];
						$totalBalance += $balForProc;
						$encOverPayment+= $charge_list_detail_arr['over_payment'];
						$diagnosis_id[$charge_list_detail_arr['dx_id1']] = $charge_list_detail_arr['dx_id1'];
						$diagnosis_id[$charge_list_detail_arr['dx_id2']] = $charge_list_detail_arr['dx_id2'];
						$diagnosis_id[$charge_list_detail_arr['dx_id3']] = $charge_list_detail_arr['dx_id3'];
						$diagnosis_id[$charge_list_detail_arr['dx_id4']] = $charge_list_detail_arr['dx_id4'];
						
						$adj_amt=0;
						if($pay_location=='1' && $viewBy=='facility' && ($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment')){
							$write_off = $normalWriteOffCheckAmt[$main_groupBy_id][$check_number][$charge_list_detail_id] + $writte_off_check_arr[$main_groupBy_id][$check_number][$charge_list_detail_id];
							$write_off_amount+= $write_off;
							$adj_amt=$arrCheckAdjustmentAmt[$main_groupBy_id][$check_number][$charge_list_detail_id];
							$adj_amount+=$adj_amt;
							
							$paidDetArr=$patPayCheckDetArr[$main_groupBy_id][$check_number][$charge_list_detail_id];
							
						}else{
							$write_off = $normalWriteOffCheckAmt[$check_number][$charge_list_detail_id] + $writte_off_check_arr[$check_number][$charge_list_detail_id];
							$write_off_amount+= $write_off;
							$adj_amt=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
							$adj_amount+=$adj_amt;
							
							$paidDetArr=$patPayCheckDetArr[$check_number][$charge_list_detail_id];
						}

						$tempChargeEncForReSub[$encounter_id] = $encounter_id;
						
						//PAYMENTS
						$paid_amount=0;

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
						
						//PAID DATES
						foreach($paidDetArr['paid_date'] as $paiddate){
							$dateOfPayment[$paiddate]=$paiddate;
						}
						
						if(!$tempChgDetArr[$charge_list_detail_id]){
							//CHECK TOTAL
							$arrCheckTotals['charges']+=$tempTotalAmt;
							$arrCheckTotals['balance']+=$balForProc - $charge_list_detail_arr['over_payment'];
							//DEPARTMENT						
							$arrDeptDetails[$main_groupBy_id][$deptId]['CHARGES']+= $tempTotalAmt;
							$arrDeptDetails[$main_groupBy_id][$deptId]['BALANCE']+= $balForProc - $charge_list_detail_arr['over_payment'];
							
							$tempChgDetArr[$charge_list_detail_id]=$charge_list_detail_id;
						}
						//CHECK TOTAL
						$arrCheckTotals['paid_amt']+=$paid_amount;
						$arrCheckTotals['adj']+=$adj_amt;
						$arrCheckTotals['write_off']+=$write_off;
						//DEPARTMENT						
						$arrDeptDetails[$main_groupBy_id][$deptId]['PAID']+= $paid_amount;
						$arrDeptDetails[$main_groupBy_id][$deptId]['ADJUSTMENT']+= $adj_amt;
						$arrDeptDetails[$main_groupBy_id][$deptId]['WRITEOFF']+= $write_off;
						
						$arrAlreadyAddedInCheckView[$charge_list_detail_id]=$charge_list_detail_id;
					}
					
					sort($diagnosis_id);
					if($diagnosis_id[0] == ''){
						array_shift($diagnosis_id);
					}
					$dxCode = implode(', ',$diagnosis_id);
			
					$cptCode = implode(', ',$cptCodeArr);
					$cptDesc = implode(', ',$cptDescArr);
					$modifiers = implode(', ', $modArr); 				
					
					$totalBalance = $totalBalance - $encOverPayment;
					//$tempCheckNo = substr($check_number,-5);
					$tempCheckNo = $check_number;
					$thisCheckSize =sizeof($arrCheckNoPosted[$main_groupBy_id][$checkNo]);
					
					$paidBy = join(', ', $paidByArr);
					$methods = join(', ', $payment_modeArr);
					$dateOfPayments = join(', ', $dateOfPayment);
			
					$totPaid =0;
										
					$totalAmt = $CLSReports->numberFormat($totalAmt,2);
					$paidAmount = $CLSReports->numberFormat($paidAmount,2,1);
					if($paidAmount=='0.00' || $paidAmount==''){ $paidAmount='$0.00';}
					$write_off_amount = $CLSReports->numberFormat($write_off_amount,2);
					$totalBalance = $CLSReports->numberFormat($totalBalance,2);
					$adjAmt = $CLSReports->numberFormat($adj_amount,2);
			
					$dateOfPayments = $dateOfPayments == '00-00-00' ? '' : $dateOfPayments;
					$postedDate = $postedDate == '00-00-00' ? '' : $postedDate;
					$DOS = $DOS == '00-00-00' ? '' : $DOS;		
					
					$posted_data .='
					<tr>
						<td class="text_10" style="width:110px; background:#FFFFFF;">'.$patientName.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$DOS.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($cptCode, 7, "<br>\n", true).'</td>			
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($cptDesc, 15, "<br>\n", true).'</td>			
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($dxCode, 10, "<br>\n", true).'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$modifiers.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$postedDate.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalAmt.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$dateOfPayments.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidBy.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$paidAmount.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($methods, 8, "<br>\n", true).'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$adjAmt.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$write_off_amount.'</td>			
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalBalance.'</td>
					</tr>';

					//FOR CSV
					$data_output.='"'.$patientName.'"'.$pfx;
					$data_output.='"'.$DOS.'"'.$pfx;
					$data_output.='"'.$cptCode.'"'.$pfx;
					$data_output.='"'.$dxCode.'"'.$pfx;
					$data_output.='"'.$modifiers.'"'.$pfx;
					$data_output.='"'.$postedDate.'"'.$pfx;
					$data_output.='"'.$totalAmt.'"'.$pfx;
					$data_output.='"'.$dateOfPayments.'"'.$pfx;
					$data_output.='"'.$paidBy.'"'.$pfx;
					$data_output.='"'.$paidAmount.'"'.$pfx;
					$data_output.='"'.$methods.'"'.$pfx;
					$data_output.='"'.$adjAmt.'"'.$pfx;
					$data_output.='"'.$write_off_amount.'"'.$pfx;
					$data_output.='"'.$totalBalance.'"'.$pfx;
					$data_output.="\n";						
				}

				$arrGroupTotals['charges']+=$arrCheckTotals['charges'];
				$arrGroupTotals['paid_amt']+=$arrCheckTotals['paid_amt'];
				$arrGroupTotals['adj']+=$arrCheckTotals['adj'];
				$arrGroupTotals['write_off']+=$arrCheckTotals['write_off'];
				$arrGroupTotals['balance']+=$arrCheckTotals['balance'];
				//CHECK TOTAL
				$posted_data .='
				<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
				<tr>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">Check Total : </td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['charges'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['paid_amt'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['adj'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['write_off'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['balance'],2).'</td>
				</tr>	
				<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
			}
			
			// GROUP TOTALS
			$arrChkPostedTotal['charges']+=$arrGroupTotals['charges'];
			$arrChkPostedTotal['paid_amt']+=$arrGroupTotals['paid_amt'];
			$arrChkPostedTotal['adj']+=$arrGroupTotals['adj'];
			$arrChkPostedTotal['write_off']+=$arrGroupTotals['write_off'];
			$arrChkPostedTotal['balance']+=$arrGroupTotals['balance'];
			$posted_data .='
			<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
			<tr>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">'.$main_grp_title.' Total : </td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['charges'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['paid_amt'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['adj'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['write_off'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['balance'],2).'</td>
			</tr>	
			<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
		}
	}
	
	//--- GET GRAND TOTAL ---------
	$grand_total_chek_arr['TOTAL_CHARGES'][] = $arrChkPostedTotal['charges'];
	$grand_total_chek_arr['TOTAL_PAID'][] = $arrChkPostedTotal['paid_amt'];
	$grand_total_chek_arr['TOTAL_ADJ'][] = $arrChkPostedTotal['adj'];
	$grand_total_chek_arr['TOTAL_WRITEOFF'][] = $arrChkPostedTotal['write_off'];
	$grand_total_chek_arr['TOTAL_BALANCE'][] = $arrChkPostedTotal['balance'];

	//--- POSTED CONTENT FOR CSV FILE --
	$posted_check_data='
			<table class="rpt_table rpt rpt_table-bordered">
			<tr id="heading_orange">
				<td align="left" colspan="15">Posted Charges</td>
			</tr>
			<tr>
				<td style="text-align:center;" class="text_b_w nowrap">Patient</td>
				<td style="text-align:center;" class="text_b_w">DOS</td>
				<td style="text-align:center;" class="text_b_w">CPT</td>
				<td style="text-align:center;" class="text_b_w">CPT Desc.</td>
				<td style="text-align:center;" class="text_b_w">DX</td>
				<td style="text-align:center;" class="text_b_w">Mod.</td>
				<td style="text-align:center;" class="text_b_w">Date of Charge</td>
				<td class="text_b_w" style="text-align:center;">Charges</td>
				<td style="text-align:center;" class="text_b_w">DOP</td>
				<td style="text-align:center;" class="text_b_w">Paid By</td>
				<td style="text-align:center;" class="text_b_w">Tot. Paid</td>
				<td style="text-align:center;" class="text_b_w">Method</td>
				<td style="text-align:center;" class="text_b_w">Adjustment</td>
				<td style="text-align:center;" class="text_b_w">Write-Off</td>
				<td style="text-align:center;" class="text_b_w">Balance</td>
			</tr>
			'.$posted_data.'
			<tr>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">Posted Total : </td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["charges"],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["paid_amt"],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["adj"],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["write_off"],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkPostedTotal["balance"],2).'</td>
			</tr>
			</table>';

			//FOR CSV
			$data_output.=" ".$pfx;
			$data_output.=" ".$pfx;
			$data_output.=" ".$pfx;
			$data_output.=" ".$pfx;
			$data_output.=" ".$pfx;
			$data_output.="Posted Total:".$pfx;
			$data_output.='"'.$CLSReports->numberFormat($arrChkPostedTotal["charges"],2).'"'.$pfx;
			$data_output.=" ".$pfx;
			$data_output.=" ".$pfx;
			$data_output.='"'.$CLSReports->numberFormat($arrChkPostedTotal["paid_amt"],2).'"'.$pfx;
			$data_output.=" ".$pfx;
			$data_output.='"'.$CLSReports->numberFormat($arrChkPostedTotal["adj"],2).'"'.$pfx;
			$data_output.='"'.$CLSReports->numberFormat($arrChkPostedTotal["write_off"],2).'"'.$pfx;
			$data_output.='"'.$CLSReports->numberFormat($arrChkPostedTotal["balance"],2).'"'.$pfx;
			$data_output.="\n";	
			@fwrite($fp,$data_output);			

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
	$data_output='';
	$tempEncIds = array();

	$data_output.="Not Posted Charges".$pfx;
	$data_output.="\n"; 	
	
	$data_output.="Patient".$pfx;
	$data_output.="DOS".$pfx;
	$data_output.="CPT Desc.".$pfx;
	$data_output.="CPT Desc.".$pfx;
	$data_output.="DX Codes".$pfx;
	$data_output.="Modifiers".$pfx;
	$data_output.="Posted Date".$pfx;
	$data_output.="Charges".$pfx;
	$data_output.="DOP".$pfx;
	$data_output.="Paid By".$pfx;
	$data_output.="Tot. Paid".$pfx;
	$data_output.="Method".$pfx;
	$data_output.="Adjustment".$pfx;	
	$data_output.="Write-Off".$pfx;
	$data_output.="Balance".$pfx;
	$data_output.="\n"; 	
	
	foreach($mainResCheckArr['not_posted_charges'] as $main_groupBy_id => $groupData) {
			$printFile=true;
			$arrGroupTotals=array();
			$main_grp_title='Physician';
			$main_groupBy_name=$providerNameArr[$main_groupBy_id];
			if($viewBy=='facility'){
				$main_grp_title='Facility';
				$main_groupBy_name = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$main_groupBy_id] : $arrAllFacilities[$main_groupBy_id];
			}
			$not_posted_data .='<tr><td class="text_b_w" colspan="15">'.$main_grp_title.' : '.$main_groupBy_name.'</td></tr>';
	
			foreach($groupData as $check_number => $data){
				$arrCheckTotals=array();
				//$not_posted_data .='<tr><td class="text_b_w" colspan="14">Check No - '.substr($check_number,-5).'</td></tr>';
				$not_posted_data .='<tr><td class="text_b_w" colspan="14">Check No - '.$check_number.'</td></tr>';
		
				foreach($data as $encounter_id =>$encounter_data){
					$cptCodeArr = array();
					$cptDescArr = array();
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
						$deptid=$charge_list_detail_arr['departmentId'];
						$patientName = core_name_format($charge_list_detail_arr['lname'], $charge_list_detail_arr['fname'], $charge_list_detail_arr['mname']);		
						$patient_id = $charge_list_detail_arr['patient_id'];			
						if(empty($patientName) == false){
							$patientName .= ' - '.$patient_id;
						}
						
						$DOS = $charge_list_detail_arr['date_of_service'];
						$postedDate = $charge_list_detail_arr['postedDate'];			
						$cptCodeArr[] = $charge_list_detail_arr['cpt4_code'];
						$cptDescArr[] = $charge_list_detail_arr['cpt_desc'];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id1']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id1']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id2']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id2']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id3']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id3']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id4']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id4']];
						
						$tempTotalAmt = $charge_list_detail_arr['totalAmt'];
						$totalAmt +=$tempTotalAmt;
						$balForProc = $charge_list_detail_arr['proc_balance'];
						$totalBalance += $balForProc;
						$encOverPayment+= $charge_list_detail_arr['over_payment'];
						$diagnosis_id[$charge_list_detail_arr['dx_id1']] = $charge_list_detail_arr['dx_id1'];
						$diagnosis_id[$charge_list_detail_arr['dx_id2']] = $charge_list_detail_arr['dx_id2'];
						$diagnosis_id[$charge_list_detail_arr['dx_id3']] = $charge_list_detail_arr['dx_id3'];
						$diagnosis_id[$charge_list_detail_arr['dx_id4']] = $charge_list_detail_arr['dx_id4'];
						
						$adj_amt=0;
						if($pay_location=='1' && $viewBy=='facility' && ($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment')){
							$write_off = $normalWriteOffCheckAmt[$main_groupBy_id][$check_number][$charge_list_detail_id] + $writte_off_check_arr[$main_groupBy_id][$check_number][$charge_list_detail_id];
							$write_off_amount+= $write_off;
							$adj_amt=$arrCheckAdjustmentAmt[$main_groupBy_id][$check_number][$charge_list_detail_id];
							$adj_amount+=$adj_amt;
							$paidDetArr=$patPayCheckDetArr[$main_groupBy_id][$check_number][$charge_list_detail_id];							
						}else{
							$write_off = $normalWriteOffCheckAmt[$check_number][$charge_list_detail_id] + $writte_off_check_arr[$check_number][$charge_list_detail_id];
							$write_off_amount+= $write_off;
							$adj_amt=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
							$adj_amount+=$adj_amt;
							$paidDetArr=$patPayCheckDetArr[$check_number][$charge_list_detail_id];
						}
						
						$arrChargeDept[$charge_list_detail_id] = $deptId;
						$tempChargeEncForReSub[$encounter_id] = $encounter_id;
						
						//PAYMENTS
						$paid_amount=0;

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
						
						//PAID DATES
						foreach($paidDetArr['paid_date'] as $paiddate){
							$dateOfPayment[$paiddate]=$paiddate;
						}

						if(!$tempChgDetArr[$charge_list_detail_id]){
							//CHECK TOTAL
							$arrCheckTotals['charges']+=$tempTotalAmt;
							$arrCheckTotals['balance']+=$balForProc - $charge_list_detail_arr['over_payment'];
							//DEPARTMENT						
							$arrDeptDetails[$main_groupBy_id][$deptId]['CHARGES']+= $tempTotalAmt;
							$arrDeptDetails[$main_groupBy_id][$deptId]['BALANCE']+= $balForProc - $charge_list_detail_arr['over_payment'];
							
							$tempChgDetArr[$charge_list_detail_id]=$charge_list_detail_id;
						}
						//CHECK TOTAL
						$arrCheckTotals['paid_amt']+=$paid_amount;
						$arrCheckTotals['adj']+=$adj_amt;
						$arrCheckTotals['write_off']+=$write_off;
						//DEPARTMENT						
						$arrDeptDetails[$main_groupBy_id][$deptId]['PAID']+= $paid_amount;
						$arrDeptDetails[$main_groupBy_id][$deptId]['ADJUSTMENT']+= $adj_amt;
						$arrDeptDetails[$main_groupBy_id][$deptId]['WRITEOFF']+= $write_off;
						
						$arrAlreadyAddedInCheckView[$charge_list_detail_id]=$charge_list_detail_id;
					}

					sort($diagnosis_id);
					if($diagnosis_id[0] == ''){
						array_shift($diagnosis_id);
					}
					$dxCode = implode(', ',$diagnosis_id);
					$cptCode = implode(', ',$cptCodeArr);
					$cptDesc = implode(', ',$cptDescArr);
					$modifiers = implode(', ', $modArr); 				
					
					$totalBalance = $totalBalance - $encOverPayment;
					//$tempCheckNo = substr($check_number,-5);
					$tempCheckNo = $check_number;
					$thisCheckSize =sizeof($arrCheckNoPosted[$main_groupBy_id][$checkNo]);
					
					$paidBy = join(', ', $paidByArr);
					$methods = join(', ', $payment_modeArr);
					$dateOfPayments = join(', ', $dateOfPayment);
			

					//--- NUMBER FORMAT ---
					$totalAmt = $CLSReports->numberFormat($totalAmt,2);
					$paidAmount = $CLSReports->numberFormat($paidAmount,2,1);
					if($paidAmount=='0.00' || $paidAmount==''){ $paidAmount='$0.00';}
					$write_off_amount = $CLSReports->numberFormat($write_off_amount,2);
					$totalBalance = $CLSReports->numberFormat($totalBalance,2);
					$adjAmt = $CLSReports->numberFormat($adj_amount,2);
			
					$dateOfPayments = $dateOfPayments == '00-00-00' ? '' : $dateOfPayments;
					$postedDate = $postedDate == '00-00-00' ? '' : $postedDate;
					$DOS = $DOS == '00-00-00' ? '' : $DOS;		

					$not_posted_data .='
					<tr>
						<td class="text_10" style="width:110px; background:#FFFFFF;">'.$patientName.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$DOS.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($cptCode, 7, "<br>\n", true).'</td>			
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($cptDesc, 15, "<br>\n", true).'</td>			
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($dxCode, 10, "<br>\n", true).'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$modifiers.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$postedDate.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalAmt.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$dateOfPayments.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidBy.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$paidAmount.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($methods, 8, "<br>\n", true).'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$adjAmt.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$write_off_amount.'</td>			
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalBalance.'</td>
					</tr>';

					//FOR CSV
					$data_output.='"'.$patientName.'"'.$pfx;
					$data_output.='"'.$DOS.'"'.$pfx;
					$data_output.='"'.$cptCode.'"'.$pfx;
					$data_output.='"'.$cptDesc.'"'.$pfx;
					$data_output.='"'.$dxCode.'"'.$pfx;
					$data_output.='"'.$modifiers.'"'.$pfx;
					$data_output.='"'.$postedDate.'"'.$pfx;
					$data_output.='"'.$totalAmt.'"'.$pfx;
					$data_output.='"'.$dateOfPayments.'"'.$pfx;
					$data_output.='"'.$paidBy.'"'.$pfx;
					$data_output.='"'.$paidAmount.'"'.$pfx;
					$data_output.='"'.$methods.'"'.$pfx;
					$data_output.='"'.$adjAmt.'"'.$pfx;
					$data_output.='"'.$write_off_amount.'"'.$pfx;
					$data_output.='"'.$totalBalance.'"'.$pfx;
					$data_output.="\n";					
				}


				$arrGroupTotals['charges']+=$arrCheckTotals['charges'];
				$arrGroupTotals['paid_amt']+=$arrCheckTotals['paid_amt'];
				$arrGroupTotals['adj']+=$arrCheckTotals['adj'];
				$arrGroupTotals['write_off']+=$arrCheckTotals['write_off'];
				$arrGroupTotals['balance']+=$arrCheckTotals['balance'];
				//CHECK TOTAL
				$not_posted_data.='
				<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
				<tr>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">Check Total : </td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['charges'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['paid_amt'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['adj'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['write_off'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['balance'],2).'</td>
				</tr>	
				<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
			}

			// GROUP TOTALS
			$arrChkNotPostedTotal['charges']+=$arrGroupTotals['charges'];
			$arrChkNotPostedTotal['paid_amt']+=$arrGroupTotals['paid_amt'];
			$arrChkNotPostedTotal['adj']+=$arrGroupTotals['adj'];
			$arrChkNotPostedTotal['write_off']+=$arrGroupTotals['write_off'];
			$arrChkNotPostedTotal['balance']+=$arrGroupTotals['balance'];
			$not_posted_data .='
			<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
			<tr>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">'.$main_grp_title.' Total : </td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['charges'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['paid_amt'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['adj'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['write_off'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['balance'],2).'</td>
			</tr>	
			<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';

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
		<td align="left" colspan="15">Not Posted Charges</td>
	</tr>
	<tr>
		<td style="text-align:center;" class="text_b_w nowrap">Patient</td>
		<td style="text-align:center;" class="text_b_w">DOS</td>
		<td style="text-align:center;" class="text_b_w">CPT</td>
		<td style="text-align:center;" class="text_b_w">CPT Desc.</td>
		<td style="text-align:center;" class="text_b_w">DX</td>
		<td style="text-align:center;" class="text_b_w">Mod.</td>
		<td style="text-align:center;" class="text_b_w">Date of Charge</td>
		<td class="text_b_w" style="text-align:center;">Charges</td>
		<td style="text-align:center;" class="text_b_w">DOP</td>
		<td style="text-align:center;" class="text_b_w">Paid By</td>
		<td style="text-align:center;" class="text_b_w">Tot. Paid</td>
		<td style="text-align:center;" class="text_b_w">Method</td>
		<td style="text-align:center;" class="text_b_w">Adjustment</td>
		<td style="text-align:center;" class="text_b_w">Write-Off</td>
		<td style="text-align:center;" class="text_b_w">Balance</td>
	</tr>
	'.$not_posted_data.'
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">Not Posted Total : </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["charges"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["paid_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["adj"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["write_off"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkNotPostedTotal["balance"],2).'</td>
	</tr>
	</table>';

	//FOR CSV
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.="Not Posted Total:".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkNotPostedTotal["charges"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkNotPostedTotal["paid_amt"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkNotPostedTotal["adj"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkNotPostedTotal["write_off"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkNotPostedTotal["balance"],2).'"'.$pfx;
	$data_output.="\n";		
	@fwrite($fp,$data_output);						
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
	$data_output='';

	$data_output.="Re-submitted Charges".$pfx;
	$data_output.="\n"; 	
	
	$data_output.="Patient".$pfx;
	$data_output.="DOS".$pfx;
	$data_output.="CPT Desc.".$pfx;
	$data_output.="DX Codes".$pfx;
	$data_output.="Modifiers".$pfx;
	$data_output.="Posted Date".$pfx;
	$data_output.="Charges".$pfx;
	$data_output.="DOP".$pfx;
	$data_output.="Paid By".$pfx;
	$data_output.="Tot. Paid".$pfx;
	$data_output.="Method".$pfx;
	$data_output.="Adjustment".$pfx;	
	$data_output.="Write-Off".$pfx;
	$data_output.="Balance".$pfx;
	$data_output.="\n"; 	
	
	foreach($mainResCheckArr['re_posted_charges'] as $main_groupBy_id => $groupData) {
			$printFile=true;
			$arrGroupTotals=array();
			$main_grp_title='Physician';
			$main_groupBy_name=$providerNameArr[$main_groupBy_id];
			if($viewBy=='facility'){
				$main_grp_title='Facility';
				$main_groupBy_name = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$main_groupBy_id] : $arrAllFacilities[$main_groupBy_id];
			}
			$re_posted_data .='<tr><td class="text_b_w" colspan="15">'.$main_grp_title.' : '.$main_groupBy_name.'</td></tr>';

			foreach($groupData as $check_number => $data){
				$arrCheckTotals=array();
				//$not_posted_data .='<tr><td class="text_b_w" colspan="14">Check No - '.substr($check_number,-5).'</td></tr>';
				$not_posted_data .='<tr><td class="text_b_w" colspan="15">Check No - '.$check_number.'</td></tr>';

				foreach($data as $encounter_id =>$encounter_data){
					$cptCodeArr = array();
					$cptDescArr = array();
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
						$deptid=$charge_list_detail_arr['departmentId'];
						$patientName = core_name_format($charge_list_detail_arr['lname'], $charge_list_detail_arr['fname'], $charge_list_detail_arr['mname']);		
						$patient_id = $charge_list_detail_arr['patient_id'];			
						if(empty($patientName) == false){
							$patientName .= ' - '.$patient_id;
						}
						
						$DOS = $charge_list_detail_arr['date_of_service'];
						$postedDate = $charge_list_detail_arr['postedDate'];			
						$cptCodeArr[] = $charge_list_detail_arr['cpt4_code'];
						$cptDescArr[] = $charge_list_detail_arr['cpt_desc'];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id1']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id1']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id2']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id2']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id3']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id3']];
						$modArr[$arrAllModifiers[$charge_list_detail_arr['mod_id4']]] = $arrAllModifiers[$charge_list_detail_arr['mod_id4']];
						$tempTotalAmt = $charge_list_detail_arr['totalAmt'];
						$totalAmt +=$tempTotalAmt;
						$balForProc = $charge_list_detail_arr['proc_balance'];
						$totalBalance += $balForProc;
						$encOverPayment+= $charge_list_detail_arr['over_payment'];
						$diagnosis_id[$charge_list_detail_arr['dx_id1']] = $charge_list_detail_arr['dx_id1'];
						$diagnosis_id[$charge_list_detail_arr['dx_id2']] = $charge_list_detail_arr['dx_id2'];
						$diagnosis_id[$charge_list_detail_arr['dx_id3']] = $charge_list_detail_arr['dx_id3'];
						$diagnosis_id[$charge_list_detail_arr['dx_id4']] = $charge_list_detail_arr['dx_id4'];
						
						$adj_amt=0;
						if($pay_location=='1' && $viewBy=='facility' && ($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment')){
							$write_off = $normalWriteOffCheckAmt[$main_groupBy_id][$check_number][$charge_list_detail_id] + $writte_off_check_arr[$main_groupBy_id][$check_number][$charge_list_detail_id];
							$write_off_amount+= $write_off;
							$adj_amt=$arrCheckAdjustmentAmt[$main_groupBy_id][$check_number][$charge_list_detail_id];
							$adj_amount+=$adj_amt;
							$paidDetArr=$patPayCheckDetArr[$main_groupBy_id][$check_number][$charge_list_detail_id];							
						}else{
							$write_off = $normalWriteOffCheckAmt[$check_number][$charge_list_detail_id] + $writte_off_check_arr[$check_number][$charge_list_detail_id];
							$write_off_amount+= $write_off;
							$adj_amount+=$arrCheckAdjustmentAmt[$check_number][$charge_list_detail_id];
							$paidDetArr=$patPayCheckDetArr[$check_number][$charge_list_detail_id];	
						}
						
						$tempChargeEncForReSub[$encounter_id] = $encounter_id;
						
						//PAYMENTS
						$paid_amount=0;
						
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
						
						//PAID DATES
						foreach($paidDetArr['paid_date'] as $paiddate){
							$dateOfPayment[$paiddate]=$paiddate;
						}

						if(!$tempChgDetArr[$charge_list_detail_id]){
							//CHECK TOTAL
							$arrCheckTotals['charges']+=$tempTotalAmt;
							$arrCheckTotals['balance']+=$balForProc - $charge_list_detail_arr['over_payment'];
							//DEPARTMENT						
							$arrDeptDetails[$main_groupBy_id][$deptId]['CHARGES']+= $tempTotalAmt;
							$arrDeptDetails[$main_groupBy_id][$deptId]['BALANCE']+= $balForProc - $charge_list_detail_arr['over_payment'];
							
							$tempChgDetArr[$charge_list_detail_id]=$charge_list_detail_id;
						}
						//CHECK TOTAL
						$arrCheckTotals['paid_amt']+=$paid_amount;
						$arrCheckTotals['adj']+=$adj_amt;
						$arrCheckTotals['write_off']+=$write_off;
						//DEPARTMENT						
						$arrDeptDetails[$main_groupBy_id][$deptId]['PAID']+= $paid_amount;
						$arrDeptDetails[$main_groupBy_id][$deptId]['ADJUSTMENT']+= $adj_amt;
						$arrDeptDetails[$main_groupBy_id][$deptId]['WRITEOFF']+= $write_off;
						
						$arrAlreadyAddedInCheckView[$charge_list_detail_id]=$charge_list_detail_id;
					}
							
					sort($diagnosis_id);
					if($diagnosis_id[0] == ''){
						array_shift($diagnosis_id);
					}
					$dxCode = implode(', ',$diagnosis_id);
			
					$cptCode = implode(', ',$cptCodeArr);
					$cptDesc = implode(', ',$cptDescArr);
					$modifiers = implode(', ', $modArr); 				
					
					$totalBalance = $totalBalance - $encOverPayment;
					//$tempCheckNo = substr($check_number,-5);
					$tempCheckNo = $check_number;
					$thisCheckSize =sizeof($arrCheckNoPosted[$main_groupBy_id][$checkNo]);
					
					$paidBy = join(', ', $paidByArr);
					$methods = join(', ', $payment_modeArr);
					$dateOfPayments = join(', ', $dateOfPayment);
					
					//--- NUMBER FORMAT ---
					$totalAmt = $CLSReports->numberFormat($totalAmt,2);
					$paidAmount = $CLSReports->numberFormat($paidAmount,2,1);
					if($paidAmount=='0.00' || $paidAmount==''){ $paidAmount='$0.00';}
					$write_off_amount = $CLSReports->numberFormat($write_off_amount,2);
					$totalBalance = $CLSReports->numberFormat($totalBalance,2);
					$adjAmt = $CLSReports->numberFormat($adj_amount,2);
					$write_off_amount = $write_off_amount;
			
					$dateOfPayments = $dateOfPayments == '00-00-00' ? '' : $dateOfPayments;
					$postedDate = $postedDate == '00-00-00' ? '' : $postedDate;
					$DOS = $DOS == '00-00-00' ? '' : $DOS;		
					
					$re_posted_data .='
					<tr>
						<td class="text_10" style="width:110px; background:#FFFFFF;">'.$patientName.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$DOS.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($cptCode, 7, "<br>\n", true).'</td>			
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($cptDesc, 15, "<br>\n", true).'</td>			
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($dxCode, 10, "<br>\n", true).'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$modifiers.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$postedDate.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalAmt.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$dateOfPayments.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidBy.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$paidAmount.'</td>
						<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($methods, 8, "<br>\n", true).'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$adjAmt.'</td>
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$write_off_amount.'</td>			
						<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalBalance.'</td>
					</tr>';

					//FOR CSV
					$data_output.='"'.$patientName.'"'.$pfx;
					$data_output.='"'.$DOS.'"'.$pfx;
					$data_output.='"'.$cptCode.'"'.$pfx;
					$data_output.='"'.$cptDesc.'"'.$pfx;
					$data_output.='"'.$dxCode.'"'.$pfx;
					$data_output.='"'.$modifiers.'"'.$pfx;
					$data_output.='"'.$postedDate.'"'.$pfx;
					$data_output.='"'.$totalAmt.'"'.$pfx;
					$data_output.='"'.$dateOfPayments.'"'.$pfx;
					$data_output.='"'.$paidBy.'"'.$pfx;
					$data_output.='"'.$paidAmount.'"'.$pfx;
					$data_output.='"'.$methods.'"'.$pfx;
					$data_output.='"'.$adjAmt.'"'.$pfx;
					$data_output.='"'.$write_off_amount.'"'.$pfx;
					$data_output.='"'.$totalBalance.'"'.$pfx;
					$data_output.="\n";						
				}
			
				$arrGroupTotals['charges']+=$arrCheckTotals['charges'];
				$arrGroupTotals['paid_amt']+=$arrCheckTotals['paid_amt'];
				$arrGroupTotals['adj']+=$arrCheckTotals['adj'];
				$arrGroupTotals['write_off']+=$arrCheckTotals['write_off'];
				$arrGroupTotals['balance']+=$arrCheckTotals['balance'];
				//CHECK TOTAL
				$re_posted_data.='
				<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
				<tr>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">Check Total : </td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['charges'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['paid_amt'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['adj'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['write_off'],2).'</td>
					<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrCheckTotals['balance'],2).'</td>
				</tr>	
				<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
			}
		
			// GROUP TOTALS
			$arrChkRePostedTotal['charges']+=$arrGroupTotals['charges'];
			$arrChkRePostedTotal['paid_amt']+=$arrGroupTotals['paid_amt'];
			$arrChkRePostedTotal['adj']+=$arrGroupTotals['adj'];
			$arrChkRePostedTotal['write_off']+=$arrGroupTotals['write_off'];
			$arrChkRePostedTotal['balance']+=$arrGroupTotals['balance'];
			$re_posted_data .='
			<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
			<tr>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">'.$main_grp_title.' Total : </td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['charges'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['paid_amt'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['adj'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['write_off'],2).'</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotals['balance'],2).'</td>
			</tr>	
			<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
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
		<td align="left" colspan="15">Re-Submitted Charges</td>
	</tr>
	<tr>
		<td style="text-align:center;" class="text_b_w nowrap">Patient</td>
		<td style="text-align:center;" class="text_b_w">DOS</td>
		<td style="text-align:center;" class="text_b_w">CPT</td>
		<td style="text-align:center;" class="text_b_w">CPT Desc.</td>
		<td style="text-align:center;" class="text_b_w">DX</td>
		<td style="text-align:center;" class="text_b_w">Mod.</td>
		<td style="text-align:center;" class="text_b_w">Date of Charge</td>
		<td class="text_b_w" style="text-align:center;">Charges</td>
		<td style="text-align:center;" class="text_b_w">DOP</td>
		<td style="text-align:center;" class="text_b_w">Paid By</td>
		<td style="text-align:center;" class="text_b_w">Tot. Paid</td>
		<td style="text-align:center;" class="text_b_w">Method</td>
		<td style="text-align:center;" class="text_b_w">Adjustment</td>
		<td style="text-align:center;" class="text_b_w">Write-Off</td>
		<td style="text-align:center;" class="text_b_w">Balance</td>
	</tr>
	'.$re_posted_data.'
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">Re-Submitted Total : </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["charges"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["paid_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["adj"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["write_off"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrChkRePostedTotal["balance"],2).'</td>
	</tr>
	</table>';

	//FOR CSV
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.="Re-submitted Total:".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkRePostedTotal["charges"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkRePostedTotal["paid_amt"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkRePostedTotal["adj"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkRePostedTotal["write_off"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrChkRePostedTotal["balance"],2).'"'.$pfx;
	$data_output.="\n";		
	@fwrite($fp,$data_output);	
}

//GRAND TOTALS
if(sizeof($grand_total_chek_arr)>0){
	$data_output='';
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
	
	//FOR CSV
	$data_output.="Grand Totals (Check View)".$pfx;
	$data_output.="\n"; 
	
	$data_output.="Charges".$pfx;
	$data_output.="Check".$pfx;
	$data_output.="Money Order".$pfx;
	$data_output.="EFT".$pfx;
	$data_output.="Total Paid".$pfx;
	$data_output.="Adjustment".$pfx;
	$data_output.="Write-off".$pfx;
	$data_output.="Balance".$pfx;
	$data_output.="\n";

	$data_output.='"'.$CLSReports->numberFormat($grand_tot_charges,2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_tot_check,2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_tot_mo,2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_tot_eft,2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_tot_paid,2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_tot_adj,2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_tot_writeoff,2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_tot_balance,2).'"'.$pfx;
	$data_output.="\n";	
	@fwrite($fp,$data_output);
}

// DEPARTMENT DETAILS
$department_data='';
if(sizeof($arrCheckDeptDetails)>0){
	$data_output='';
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

	//FOR CSV
	$data_output.="Department Summary".$dept_title_part.$pfx;
	$data_output.="\n"; 
	
	$data_output.="Department".$pfx;
	$data_output.="Charges".$pfx;
	$data_output.="Total Paid".$pfx;
	$data_output.="Adjustment".$pfx;
	$data_output.="Write-Off".$pfx;
	$data_output.="Balance".$pfx;
	$data_output.="\n";
	
	foreach($arrCheckDeptDetails as $firstGrpId => $deptdata){
		$arrSubDept=array();
		
		if($viewBy=='physician'){
			$firstGrpTitle='Physician';
			$firstGrpName = $providerNameArr[$firstGrpId];
		}else{
			$firstGrpTitle='Facility';
			$main_groupBy_name = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$firstGrpId] : $arrAllFacilities[$firstGrpId];
		}

		$department_data .='<tr><td class="text_b_w" colspan="6">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';

		//FOR CSV
		$data_output.=$firstGrpTitle.' : '.$firstGrpName.$pfx;
		$data_output.="\n"; 		
		
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

			//FOR CSV
			$data_output.=$deptName.$pfx;
			$data_output.='"'.$CLSReports->numberFormat($deptDetails["CHARGES"],2).'"'.$pfx;
			$data_output.='"'.$CLSReports->numberFormat($deptDetails["PAID"],2).'"'.$pfx;
			$data_output.='"'.$CLSReports->numberFormat($deptDetails["ADJUSTMENT"],2).'"'.$pfx;
			$data_output.='"'.$CLSReports->numberFormat($deptDetails["WRITEOFF"],2).'"'.$pfx;
			$data_output.='"'.$CLSReports->numberFormat($deptDetails["BALANCE"],2).'"'.$pfx;
			$data_output.="\n";				
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
		
		//FOR CSV
		$data_output.='Sub Total'.$pfx;
		$data_output.='"'.$CLSReports->numberFormat($arrSubDept["CHARGES"],2).'"'.$pfx;
		$data_output.='"'.$CLSReports->numberFormat($arrSubDept["PAID"],2).'"'.$pfx;
		$data_output.='"'.$CLSReports->numberFormat($arrSubDept["ADJUSTMENT"],2).'"'.$pfx;
		$data_output.='"'.$CLSReports->numberFormat($arrSubDept["WRITEOFF"],2).'"'.$pfx;
		$data_output.='"'.$CLSReports->numberFormat($arrSubDept["BALANCE"],2).'"'.$pfx;
		$data_output.="\n";			
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

	//FOR CSV
	$data_output.='Total'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrTotDept["CHARGES"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrTotDept["PAID"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrTotDept["ADJUSTMENT"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrTotDept["WRITEOFF"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($arrTotDept["BALANCE"],2).'"'.$pfx;
	$data_output.="\n";	
	
	@fwrite($fp,$data_output);	
	$data_output='';
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