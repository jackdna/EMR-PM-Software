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
FILE : DETAILPRODUCTIVITY.PHP
PURPOSE :  PRODUCTVITY PHYSICIAN DETAIL REPORT
ACCESS TYPE : INCLUDED
*/
$providerIdArr = array_keys($mainResArr);
$pdf_page_content = NULL;
$csvFileData = NULL;
$grandTotalAmtArr = array();

$chart_j = $k = $m = $n = 0; 
$chartData = array();
$chartProviderId = array();
$chartFacilityId = array();
$chartFacilityName = array();
$chartFacilityNId = array();
$chartProviderNId = array();

$colspan=12;
$colspanPdf=17;
$startColspan=6;
$startColspanPDF=5;

$tdTotal='';
$totPosted=array();
$posted_data=$not_posted_data=$re_posted_data='';

if(sizeof($mainResArr['posted_charges'])>0){
	
	foreach($mainResArr['posted_charges'] as $firstGrpId => $firstGrpData){	
		$arrFirstGrpTotal = array();
		$printFile=true;
		
		if($viewBy=='physician'){
			$firstGrpTitle='Physician';
			$firstGrpName = $providerNameArr[$firstGrpId];
			$firstGrpTitle1 = 'Physician';
		}else{
			$firstGrpTitle='Facility';
			$firstGrpTitle1='Facility';
			$firstGrpName = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$firstGrpId] : $arrAllFacilities[$firstGrpId];
		}
	
		foreach($firstGrpData as $enc_id => $encDataArr){
			$patient_id = $encDataArr[0]['patient_id'];
			
			//--- GET TOTAL AMOUNT ----		
			$totalAmtArr = array();		
			$write_off_amt_arr = array();
			$arrCPT = array();
			$arrCPT_CSV = array();
			$arrDxCodes = array();
			$arrPaidDate=array();
			$arrPaidBy=array();
			$payment_modeArr=array();
			$arrModifiers=array();
			$totalBalance =0;
			$patientPaidAmt = $crd_dbt_amt = $crdDbt = $adj_amt = $write_off_amt= $creditProcAmount=0;
			$paymentDate='';
			
			for($d=0;$d<count($encDataArr);$d++){
				$patCD=$insCD=$proc_paid=0;
				$chgDetId = $encDataArr[$d]['charge_list_detail_id'];
				$deptid=($encDataArr[$d]['departmentId']>0)? $encDataArr[$d]['departmentId']: '0';
	
				if($encDataArr[$d]['totalAmt']>0){
					$totalAmtArr[] = $encDataArr[$d]['totalAmt'];
					$arrFirstGrpTotal["totalAmt"]+= $encDataArr[$d]['totalAmt'];
				}
				
				//CREDIT/DEBIT
				//$crdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'] + $pay_crd_deb_arr[$chgDetId]['Insurance'];			
				$proc_paid=$mainEncounterPayArr[$chgDetId] + $crdDbt; 
				
				// TOTAL PAYMENT
				$patientPaidAmt+= $proc_paid;
	
				//WRITE-OFF & ADJUSTMENTS
				$write_off_amt+= $normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
				$adj_amt+= $arrAdjustmentAmt[$chgDetId];				
	
				//CREDIT - OVER PAYMENT
				$creditProcAmount+= $encDataArr[$d]['over_payment'];
	
				//BALANCE
				$balAmt=0;
				if($encDataArr[$d]["proc_balance"]>0){
					$balAmt= $encDataArr[$d]['proc_balance'];
				}else{
					if($encDataArr[$d]['over_payment']>0){
						$balAmt= $encDataArr[$d]['proc_balance'] - $encDataArr[$d]['over_payment'];
					}else{
						$balAmt= $encDataArr[$d]['proc_balance'];
					}
				}
				$totalBalance+=$balAmt;
	
				//PAID BY
				if($patPayDetArr[$chgDetId]['patPaid']){ 
					$arrPaidBy['Patient']='Patient';
				}else if($patPayDetArr[$chgDetId]['insPaid']){
					$arrPaidBy['Insurance']='Insurance';
				}

				$i=0;
				foreach($patPayDetArr[$chgDetId]['method'] as $record_id => $transDet){
					
					$payment_mode=$patPayDetArr[$chgDetId]['method'][$record_id];
					$paid_amount= $patPayDetArr[$chgDetId]['paid'][$record_id]+$crdDbt;
					$crdDbt=0; //empty to variable after assign first time.
					if($patPayDetArr[$chgDetId]['paid_date'][$record_id])$arrPaidDate[$patPayDetArr[$chgDetId]['paid_date'][$record_id]]= $patPayDetArr[$chgDetId]['paid_date'][$record_id];
	
					if(strtolower($payment_mode) == 'cash'){
						$payment_modeArr['Cash'] = 'Cash';
						$grand_total_arr['TOTAL_CASH']+= $paid_amount;
						$totPosted["TOTAL_CASH"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CASH"]+= $paid_amount;
						$grandSumTotalPaidCash += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'check'){
						//$check='Check - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$check='Check - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$check] = $check;
						$grand_total_arr['TOTAL_CHECK']+= $paid_amount;
						$totPosted["TOTAL_CHECK"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CHECK"]+= $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'money order'){
						//$mo='MO - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$mo='MO - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$mo] = $mo;
						$grand_total_arr['TOTAL_MO']+= $paid_amount;
						$totPosted["TOTAL_MO"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_MO"]+= $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'eft'){
						//$eft='EFT - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$eft='EFT - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$eft] = $eft;
						$grand_total_arr['TOTAL_EFT']+= $paid_amount;
						$totPosted["TOTAL_EFT"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_EFT"]+= $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
					}								
					else if(strtolower($payment_mode) == 'credit card'){
						$cc=$patPayDetArr[$chgDetId]['cc_type'][$record_id].' - '.substr($patPayDetArr[$chgDetId]['cc_number'][$record_id],0,4);
						$payment_modeArr[$cc] = $cc;
						$grand_total_arr['TOTAL_CC']+= $paid_amount;
						$totPosted["TOTAL_CC"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CC"]+= $paid_amount;
						$grandSumTotalPaidCC += $paid_amount;
					}else{
						$payment_modeArr['Other'] = 'Other';
						$grand_total_arr['TOTAL_OTHER']+= $paid_amount;
						$totPosted["TOTAL_OTHER"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_OTHER"]+= $paid_amount;
						$grandSumTotalPaidOther += $paid_amount;
					}
					$i++;
				}
				
				//GRAND TOTAL - TO AVOID DUPLICATE OF CHARGES, CREDIT AND BALANCE IN CASE OF GROUP BY INSURANCE CASE
				if(!$tempIsChgDetId[$chgDetId] && !$arrAlreadyAddedInCheckView[$chgDetId]){
					$grandTotalAmtArr["totalAmt"]+= $encDataArr[$d]['totalAmt'];
					$grandTotalAmtArr["creditProcAmount"]+= $encDataArr[$d]['over_payment'];
					$grandTotalAmtArr["insuranceDue"]+= $encDataArr[$d]['pri_due'] + $encDataArr[$d]['sec_due'] + $encDataArr[$d]['tri_due'];
					$grandTotalAmtArr["patientDue"]+= $encDataArr[$d]['pat_due'];
					$grandTotalAmtArr["totalBalance"]+= $balAmt;	
					$tempIsChgDetId[$chgDetId]=$chgDetId;
				}
				$totPosted["patient_count"][$patient_id]= $patient_id;
				$grandTotalAmtArr["patient_count"][$patient_id]= $patient_id;					
				
				if($reportType=='checkView'){
					//MADE FOR CHECK VIEW FILE
					$arrDeptDetails[$deptid]['PAID']+= $proc_paid;
					$arrDeptDetails[$deptid]['ADJUSTMENT']+=$arrAdjustmentAmt[$chgDetId];
					$arrDeptDetails[$deptid]['WRITEOFF']+=$normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					if(!$arrAlreadyAddedInCheckView[$chgDetId]){
						$arrDeptDetails[$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
						$arrDeptDetails[$deptid]['BALANCE']+=$balAmt;
					}
				}else{
					//DEPARTMENT ARRAY					
					$arrDeptDetails[$firstGrpId][$deptid]['PAID']+= $proc_paid;
					$arrDeptDetails[$firstGrpId][$deptid]['ADJUSTMENT']+=$arrAdjustmentAmt[$chgDetId];
					$arrDeptDetails[$firstGrpId][$deptid]['WRITEOFF']+=$normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					if(!$arrAlreadyAddedInCheckView[$chgDetId]){
						$arrDeptDetails[$firstGrpId][$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
						$arrDeptDetails[$firstGrpId][$deptid]['BALANCE']+=$balAmt;
					}
				}
			}
			$totalAmt = array_sum($totalAmtArr);
	
			$arrFirstGrpTotal["patient_count"][$patient_id]= $patient_id;
		//	$arrFirstGrpTotal["totalAmt"]+= $totalAmt;
			$arrFirstGrpTotal["pat_paid_amt"]+= $patientPaidAmt;
			$arrFirstGrpTotal["adj_amt"]+= $adj_amt;
			$arrFirstGrpTotal["write_off_amt"]+= $write_off_amt;
			$arrFirstGrpTotal["totalBalance"]+= $totalBalance;		
		}
	
		$totPosted["totalAmt"]+= $arrFirstGrpTotal["totalAmt"];
		$totPosted["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
		$totPosted["write_off_amt"]+= $arrFirstGrpTotal["write_off_amt"];
		$totPosted["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];
		$totPosted["totalBalance"]+= $arrFirstGrpTotal["totalBalance"];	
	
		//GRAND GROUP TOTAL
		$grandTotalAmtArr["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
		$grandTotalAmtArr["write_off_amt"]+= $arrFirstGrpTotal["write_off_amt"];
		$grandTotalAmtArr["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];

		$posted_part.='
		<tr>
			<td style="text-align:left; background:#FFFFFF; width:70px;" class="text_10">'.$firstGrpName.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.count($arrFirstGrpTotal["patient_count"]).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CASH"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CHECK"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_MO"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_EFT"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CC"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_OTHER"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
		</tr>';
	}

	$total_paid=$totPosted["TOTAL_CASH"]+$totPosted["TOTAL_CHECK"]+$totPosted["TOTAL_MO"]+$totPosted["TOTAL_EFT"]+$totPosted["TOTAL_CC"]+$totPosted["TOTAL_OTHER"];

	$posted_data.=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="'.$colspan.'">Posted Charges</td></tr>
	<tr>
        <td style="text-align:center; width:70px;" class="text_b_w nowrap">'.$firstGrpTitle.'</td>
		<td style="text-align:center; width:40px;" class="text_b_w nowrap">Patient#</td>
        <td style="text-align:center; width:70px" class="text_b_w">Charges</td>
        <td style="text-align:center; width:70px" class="text_b_w">Cash</td>
        <td style="text-align:center; width:70px" class="text_b_w">Check</td>
		<td style="text-align:center; width:70px" class="text_b_w">Money Order</td>
		<td style="text-align:center; width:70px" class="text_b_w">EFT</td>
		<td class="text_b_w" style="text-align:center; width:70px">Credit Cards</td>
		<td class="text_b_w" style="text-align:center; width:70px">Other</td>
        <td style="text-align:center; width:80px" class="text_b_w">Adjustment</td>
        <td style="text-align:center; width:80px" class="text_b_w">Write-Off</td>
        <td style="text-align:center; width:80px" class="text_b_w">Balance</td>
    </tr>
	'.$posted_part.'
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Posted Total:</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.count($totPosted["patient_count"]).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["totalAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["TOTAL_CASH"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["TOTAL_CHECK"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["TOTAL_MO"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["TOTAL_EFT"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["TOTAL_CC"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["TOTAL_OTHER"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["adj_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["write_off_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["totalBalance"],2).'</td>
	</tr>	
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="8">Total Paid:</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($total_paid,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
	</tr>	
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	</table>';			
}

//NOT POSTED
if(sizeof($mainResArr['not_posted_charges'])>0){
	$csvFileData=$csvFileDataPrint='';
	
	foreach($mainResArr['not_posted_charges'] as $firstGrpId => $firstGrpData){	
		$arrFirstGrpTotal = array();
		$printFile=true;
		
		if($viewBy=='physician'){
			$firstGrpTitle='Physician';
			$firstGrpName = $providerNameArr[$firstGrpId];
			$firstGrpTitle1 = 'Physician';
		}else{
			$firstGrpTitle='Facility';
			$firstGrpTitle1='Facility';
			$firstGrpName = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$firstGrpId] : $arrAllFacilities[$firstGrpId];
		}
	
		foreach($firstGrpData as $enc_id => $encDataArr){
			$patient_id = $encDataArr[0]['patient_id'];
		
			//--- GET TOTAL AMOUNT ----		
			$totalAmtArr = array();		
			$write_off_amt_arr = array();
			$arrCPT = array();
			$arrCPT_CSV = array();
			$arrDxCodes = array();
			$arrPaidDate=array();
			$arrPaidBy=array();
			$payment_modeArr=array();
			$arrModifiers=array();
			$totalBalance =0;
			$patientPaidAmt = $crd_dbt_amt = $crdDbt = $adj_amt = $write_off_amt= $creditProcAmount=0;
			$paymentDate='';
			
			for($d=0;$d<count($encDataArr);$d++){
				$patCD=$insCD=0;
				$chgDetId = $encDataArr[$d]['charge_list_detail_id'];
				$deptid=($encDataArr[$d]['departmentId']>0)? $encDataArr[$d]['departmentId']: '0';
	
				if($encDataArr[$d]['totalAmt']>0){
					$totalAmtArr[] = $encDataArr[$d]['totalAmt'];
					$arrFirstGrpTotal["totalAmt"]+= $encDataArr[$d]['totalAmt'];
				}
				
				//CREDIT/DEBIT
				//$crdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'] + $pay_crd_deb_arr[$chgDetId]['Insurance'];			
				$proc_paid=$mainEncounterPayArr[$chgDetId] + $crdDbt; 
				
				// TOTAL PAYMENT
				$patientPaidAmt+= $proc_paid;
	
				//WRITE-OFF & ADJUSTMENTS
				$write_off_amt+= $normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
				$adj_amt+= $arrAdjustmentAmt[$chgDetId];				
	
				//CREDIT - OVER PAYMENT
				$creditProcAmount+= $encDataArr[$d]['over_payment'];
	
				//BALANCE
				$balAmt=0;
				if($encDataArr[$d]["proc_balance"]>0){
					$balAmt= $encDataArr[$d]['proc_balance'];
				}else{
					if($encDataArr[$d]['over_payment']>0){
						$balAmt= $encDataArr[$d]['proc_balance'] - $encDataArr[$d]['over_payment'];
					}else{
						$balAmt= $encDataArr[$d]['proc_balance'];
					}
				}
				$totalBalance+=$balAmt;
	
				//PAID BY
				if($patPayDetArr[$chgDetId]['patPaid']){ 
					$arrPaidBy['Patient']='Patient';
				}else if($patPayDetArr[$chgDetId]['insPaid']){
					$arrPaidBy['Insurance']='Insurance';
				}
				
	
				$i=0;
				foreach($patPayDetArr[$chgDetId]['method'] as $record_id => $transDet){
					
					$payment_mode=$patPayDetArr[$chgDetId]['method'][$record_id];
					$paid_amount= $patPayDetArr[$chgDetId]['paid'][$record_id]+$crdDbt;
					$crdDbt=0; //empty to variable after assign first time.
					if($patPayDetArr[$chgDetId]['paid_date'][$record_id])$arrPaidDate[$patPayDetArr[$chgDetId]['paid_date'][$record_id]]= $patPayDetArr[$chgDetId]['paid_date'][$record_id];
	
					if(strtolower($payment_mode) == 'cash'){
						$payment_modeArr['Cash'] = 'Cash';
						$grand_total_arr['TOTAL_CASH']+= $paid_amount;
						$totNotPosted["TOTAL_CASH"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CASH"]+= $paid_amount;
						$grandSumTotalPaidCash += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'check'){
						//$check='Check - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$check='Check - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$check] = $check;
						$grand_total_arr['TOTAL_CHECK']+= $paid_amount;
						$totNotPosted["TOTAL_CHECK"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CHECK"]+= $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'money order'){
						//$mo='MO - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$mo='MO - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$mo] = $mo;
						$grand_total_arr['TOTAL_MO']+= $paid_amount;
						$totNotPosted["TOTAL_MO"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_MO"]+= $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'eft'){
						//$eft='EFT - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$eft='EFT - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$eft] = $eft;
						$grand_total_arr['TOTAL_EFT']+= $paid_amount;
						$totNotPosted["TOTAL_EFT"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_EFT"]+= $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
					}								
					else if(strtolower($payment_mode) == 'credit card'){
						$cc=$patPayDetArr[$chgDetId]['cc_type'][$record_id].' - '.substr($patPayDetArr[$chgDetId]['cc_number'][$record_id],0,4);
						$payment_modeArr[$cc] = $cc;
						$grand_total_arr['TOTAL_CC']+= $paid_amount;
						$totNotPosted["TOTAL_CC"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CC"]+= $paid_amount;
						$grandSumTotalPaidCC += $paid_amount;
					}else{
						$payment_modeArr['Other'] = 'Other';
						$grand_total_arr['TOTAL_OTHER']+= $paid_amount;
						$totNotPosted["TOTAL_OTHER"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_OTHER"]+= $paid_amount;
						$grandSumTotalPaidOther += $paid_amount;
					}
					$i++;
				}
				
				//GRAND TOTAL - TO AVOID DUPLICATE OF CHARGES, CREDIT AND BALANCE IN CASE OF GROUP BY INSURANCE CASE
				if(!$tempIsChgDetId[$chgDetId] && !$arrAlreadyAddedInCheckView[$chgDetId]){
					$grandTotalAmtArr["totalAmt"]+= $encDataArr[$d]['totalAmt'];
					$grandTotalAmtArr["creditProcAmount"]+= $encDataArr[$d]['over_payment'];
					$grandTotalAmtArr["insuranceDue"]+= $encDataArr[$d]['pri_due'] + $encDataArr[$d]['sec_due'] + $encDataArr[$d]['tri_due'];
					$grandTotalAmtArr["patientDue"]+= $encDataArr[$d]['pat_due'];
					$grandTotalAmtArr["totalBalance"]+= $balAmt;	
					$tempIsChgDetId[$chgDetId]=$chgDetId;
				}
				$grandTotalAmtArr["patient_count"][$patient_id]= $patient_id;
				$totNotPosted["patient_count"][$patient_id]= $patient_id;

				if($reportType=='checkView'){
					//DEPARTMENT ARRAY
					$arrDeptDetails[$deptid]['PAID']+= $proc_paid;
					$arrDeptDetails[$deptid]['ADJUSTMENT']+=$arrAdjustmentAmt[$chgDetId];
					$arrDeptDetails[$deptid]['WRITEOFF']+=$normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					if(!$arrAlreadyAddedInCheckView[$chgDetId]){
						$arrDeptDetails[$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
						$arrDeptDetails[$deptid]['BALANCE']+=$balAmt;
					}
				}else{
					//DEPARTMENT ARRAY
					$arrDeptDetails[$firstGrpId][$deptid]['PAID']+= $proc_paid;
					$arrDeptDetails[$firstGrpId][$deptid]['ADJUSTMENT']+=$arrAdjustmentAmt[$chgDetId];
					$arrDeptDetails[$firstGrpId][$deptid]['WRITEOFF']+=$normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					if(!$arrAlreadyAddedInCheckView[$chgDetId]){
						$arrDeptDetails[$firstGrpId][$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
						$arrDeptDetails[$firstGrpId][$deptid]['BALANCE']+=$balAmt;
					}
				}
			}
			$totalAmt = array_sum($totalAmtArr);
	
			$arrFirstGrpTotal["patient_count"][$patient_id]= $patient_id;
			$arrFirstGrpTotal["pat_paid_amt"]+= $patientPaidAmt;
			$arrFirstGrpTotal["adj_amt"]+= $adj_amt;
			$arrFirstGrpTotal["write_off_amt"]+= $write_off_amt;
			$arrFirstGrpTotal["totalBalance"]+= $totalBalance;		
		}
	
		$totNotPosted["totalAmt"]+= $arrFirstGrpTotal["totalAmt"];
		$totNotPosted["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
		$totNotPosted["write_off_amt"]+= $arrFirstGrpTotal["write_off_amt"];
		$totNotPosted["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];
		$totNotPosted["totalBalance"]+= $arrFirstGrpTotal["totalBalance"];	
	
		//GRAND GROUP TOTAL
		$grandTotalAmtArr["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
		$grandTotalAmtArr["write_off_amt"]+= $arrFirstGrpTotal["write_off_amt"];
		$grandTotalAmtArr["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];

		$not_posted_part.=
		'<tr>
			<td style="text-align:left; background:#FFFFFF; width:70px;" class="text_10">'.$firstGrpName.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.count($arrFirstGrpTotal["patient_count"]).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CASH"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CHECK"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_MO"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_EFT"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CC"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_OTHER"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
		</tr>';
		
	}

	$total_paid=$totNotPosted["TOTAL_CASH"]+$totNotPosted["TOTAL_CHECK"]+$totNotPosted["TOTAL_MO"]+$totNotPosted["TOTAL_EFT"]+$totNotPosted["TOTAL_CC"]+$totNotPosted["TOTAL_OTHER"];

	$not_posted_data=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="'.$colspan.'">Not Posted Charges</td></tr>
	<tr>
        <td style="text-align:center; width:70px;" class="text_b_w nowrap">'.$firstGrpTitle.'</td>
		<td style="text-align:center; width:40px;" class="text_b_w nowrap">Patient#</td>
        <td style="text-align:center; width:70px" class="text_b_w">Charges</td>
        <td style="text-align:center; width:70px" class="text_b_w">Cash</td>
        <td style="text-align:center; width:70px" class="text_b_w">Check</td>
		<td style="text-align:center; width:70px" class="text_b_w">Money Order</td>
		<td style="text-align:center; width:70px" class="text_b_w">EFT</td>
		<td class="text_b_w" style="text-align:center; width:70px">Credit Cards</td>
		<td style="text-align:center; width:70px" class="text_b_w">Other</td>
        <td style="text-align:center; width:80px" class="text_b_w">Adjustment</td>
        <td style="text-align:center; width:80px" class="text_b_w">Write-Off</td>
        <td style="text-align:center; width:80px" class="text_b_w">Balance</td>
    </tr>'.
	$not_posted_part.'
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Not Posted Total:</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.count($totNotPosted["patient_count"]).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["totalAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["TOTAL_CASH"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["TOTAL_CHECK"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["TOTAL_MO"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["TOTAL_EFT"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["TOTAL_CC"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["TOTAL_OTHER"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["adj_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["write_off_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["totalBalance"],2).'</td>
	</tr>	
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="8">Total Paid:</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($total_paid,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
	</tr>	
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	</table>';				
}


//RE-SUBMITTED
if(sizeof($mainResArr['re_posted_charges'])>0){
	$csvFileData=$csvFileDataPrint='';
	
	foreach($mainResArr['re_posted_charges'] as $firstGrpId => $firstGrpData){	
		$arrFirstGrpTotal = array();
		$printFile=true;
		
		if($viewBy=='physician'){
			$firstGrpTitle='Physician';
			$firstGrpName = $providerNameArr[$firstGrpId];
			$firstGrpTitle1 = 'Physician';
		}else{
			$firstGrpTitle='Facility';
			$firstGrpTitle1='Facility';
			$firstGrpName = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$firstGrpId] : $arrAllFacilities[$firstGrpId];
		}
	
		foreach($firstGrpData as $enc_id => $encDataArr){
			$patient_id = $encDataArr[0]['patient_id'];	
			
			//--- GET TOTAL AMOUNT ----		
			$totalAmtArr = array();		
			$write_off_amt_arr = array();
			$arrCPT = array();
			$arrCPT_CSV = array();
			$arrDxCodes = array();
			$arrPaidDate=array();
			$arrPaidBy=array();
			$payment_modeArr=array();
			$arrModifiers=array();
			$totalBalance =0;
			$patientPaidAmt = $crd_dbt_amt = $crdDbt = $adj_amt = $write_off_amt= $creditProcAmount=0;
			$paymentDate='';
			
			for($d=0;$d<count($encDataArr);$d++){
				$patCD=$insCD=0;
				$chgDetId = $encDataArr[$d]['charge_list_detail_id'];
				$deptid=($encDataArr[$d]['departmentId']>0)? $encDataArr[$d]['departmentId']: '0';
	
				if($encDataArr[$d]['totalAmt']>0){
					$totalAmtArr[] = $encDataArr[$d]['totalAmt'];
					$arrFirstGrpTotal["totalAmt"]+= $encDataArr[$d]['totalAmt'];
				}
				
				//CREDIT/DEBIT
				//$crdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'] + $pay_crd_deb_arr[$chgDetId]['Insurance'];			
				$proc_paid=$mainEncounterPayArr[$chgDetId] + $crdDbt; 
				
				// TOTAL PAYMENT
				$patientPaidAmt+= $proc_paid;
	
				//WRITE-OFF & ADJUSTMENTS
				$write_off_amt+= $normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
				$adj_amt+= $arrAdjustmentAmt[$chgDetId];				
	
				//CREDIT - OVER PAYMENT
				$creditProcAmount+= $encDataArr[$d]['over_payment'];
	
				//BALANCE
				$balAmt=0;
				if($encDataArr[$d]["proc_balance"]>0){
					$balAmt= $encDataArr[$d]['proc_balance'];
				}else{
					if($encDataArr[$d]['over_payment']>0){
						$balAmt= $encDataArr[$d]['proc_balance'] - $encDataArr[$d]['over_payment'];
					}else{
						$balAmt= $encDataArr[$d]['proc_balance'];
					}
				}
				$totalBalance+=$balAmt;
	
				//PAID BY
				if($patPayDetArr[$chgDetId]['patPaid']){ 
					$arrPaidBy['Patient']='Patient';
				}else if($patPayDetArr[$chgDetId]['insPaid']){
					$arrPaidBy['Insurance']='Insurance';
				}
				
				$i=0;
				foreach($patPayDetArr[$chgDetId]['method'] as $record_id => $transDet){
					
					$payment_mode=$patPayDetArr[$chgDetId]['method'][$record_id];
					$paid_amount= $patPayDetArr[$chgDetId]['paid'][$record_id]+$crdDbt;
					$crdDbt=0; //empty to variable after assign first time.
					if($patPayDetArr[$chgDetId]['paid_date'][$record_id])$arrPaidDate[$patPayDetArr[$chgDetId]['paid_date'][$record_id]]= $patPayDetArr[$chgDetId]['paid_date'][$record_id];
	
					if(strtolower($payment_mode) == 'cash'){
						$payment_modeArr['Cash'] = 'Cash';
						$grand_total_arr['TOTAL_CASH']+= $paid_amount;
						$totRePosted["TOTAL_CASH"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CASH"]+= $paid_amount;
						$grandSumTotalPaidCash += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'check'){
						//$check='Check - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$check='Check - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$check] = $check;
						$grand_total_arr['TOTAL_CHECK']+= $paid_amount;
						$totRePosted["TOTAL_CHECK"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CHECK"]+= $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'money order'){
						//$mo='MO - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$mo='MO - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$mo] = $mo;
						$grand_total_arr['TOTAL_MO']+= $paid_amount;
						$totRePosted["TOTAL_MO"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_MO"]+= $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'eft'){
						//$eft='EFT - '.substr($patPayDetArr[$chgDetId]['check_num'][$record_id],-5);
						$eft='EFT - '.$patPayDetArr[$chgDetId]['check_num'][$record_id];
						$payment_modeArr[$eft] = $eft;
						$grand_total_arr['TOTAL_EFT']+= $paid_amount;
						$totRePosted["TOTAL_EFT"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_EFT"]+= $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
					}								
					else if(strtolower($payment_mode) == 'credit card'){
						$cc=$patPayDetArr[$chgDetId]['cc_type'][$record_id].' - '.substr($patPayDetArr[$chgDetId]['cc_number'][$record_id],0,4);
						$payment_modeArr[$cc] = $cc;
						$grand_total_arr['TOTAL_CC']+= $paid_amount;
						$totRePosted["TOTAL_CC"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_CC"]+= $paid_amount;
						$grandSumTotalPaidCC += $paid_amount;
					}else{
						$payment_modeArr['Other'] = 'Other';
						$grand_total_arr['TOTAL_OTHER']+= $paid_amount;
						$totRePosted["TOTAL_OTHER"]+= $paid_amount;
						$arrFirstGrpTotal["TOTAL_OTHER"]+= $paid_amount;
						$grandSumTotalPaidOther += $paid_amount;
					}
					$i++;
				}
				
				//GRAND TOTAL - TO AVOID DUPLICATE OF CHARGES, CREDIT AND BALANCE IN CASE OF GROUP BY INSURANCE CASE
				if(!$tempIsChgDetId[$chgDetId] && !$arrAlreadyAddedInCheckView[$chgDetId]){
					$grandTotalAmtArr["totalAmt"]+= $encDataArr[$d]['totalAmt'];
					$grandTotalAmtArr["creditProcAmount"]+= $encDataArr[$d]['over_payment'];
					$grandTotalAmtArr["insuranceDue"]+= $encDataArr[$d]['pri_due'] + $encDataArr[$d]['sec_due'] + $encDataArr[$d]['tri_due'];
					$grandTotalAmtArr["patientDue"]+= $encDataArr[$d]['pat_due'];
					$grandTotalAmtArr["totalBalance"]+= $balAmt;	
					$tempIsChgDetId[$chgDetId]=$chgDetId;
				}
				$grandTotalAmtArr["patient_count"][$patient_id]= $patient_id;						
				$totRePosted["patient_count"][$patient_id]= $patient_id;

				if($reportType=='checkView'){
					//DEPARTMENT ARRAY
					$arrDeptDetails[$deptid]['PAID']+= $proc_paid;
					$arrDeptDetails[$deptid]['ADJUSTMENT']+=$arrAdjustmentAmt[$chgDetId];
					$arrDeptDetails[$deptid]['WRITEOFF']+=$normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					if(!$arrAlreadyAddedInCheckView[$chgDetId]){
						$arrDeptDetails[$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
						$arrDeptDetails[$deptid]['BALANCE']+=$balAmt;
					}
				}else{
					//DEPARTMENT ARRAY
					$arrDeptDetails[$firstGrpId][$deptid]['PAID']+= $proc_paid;
					$arrDeptDetails[$firstGrpId][$deptid]['ADJUSTMENT']+=$arrAdjustmentAmt[$chgDetId];
					$arrDeptDetails[$firstGrpId][$deptid]['WRITEOFF']+=$normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					if(!$arrAlreadyAddedInCheckView[$chgDetId]){
						$arrDeptDetails[$firstGrpId][$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
						$arrDeptDetails[$firstGrpId][$deptid]['BALANCE']+=$balAmt;
					}
				}
			}
			$totalAmt = array_sum($totalAmtArr);
	
		//	$arrFirstGrpTotal["totalAmt"]+= $totalAmt;
			$arrFirstGrpTotal["patient_count"][$patient_id]= $patient_id;
			$arrFirstGrpTotal["pat_paid_amt"]+= $patientPaidAmt;
			$arrFirstGrpTotal["adj_amt"]+= $adj_amt;
			$arrFirstGrpTotal["write_off_amt"]+= $write_off_amt;
			$arrFirstGrpTotal["totalBalance"]+= $totalBalance;		
		}
	
		$totRePosted["totalAmt"]+= $arrFirstGrpTotal["totalAmt"];
		$totRePosted["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
		$totRePosted["write_off_amt"]+= $arrFirstGrpTotal["write_off_amt"];
		$totRePosted["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];
		$totRePosted["totalBalance"]+= $arrFirstGrpTotal["totalBalance"];	
	
		//GRAND GROUP TOTAL
		$grandTotalAmtArr["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
		$grandTotalAmtArr["write_off_amt"]+= $arrFirstGrpTotal["write_off_amt"];
		$grandTotalAmtArr["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];

		$re_posted_part.='
		<tr>
			<td style="text-align:left; background:#FFFFFF;" class="text_10">'.$firstGrpName.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.count($arrFirstGrpTotal["patient_count"]).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CASH"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CHECK"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_MO"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_EFT"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_CC"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["TOTAL_OTHER"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
		</tr>';	
		
	}

	$total_paid=$totRePosted["TOTAL_CASH"]+$totRePosted["TOTAL_CHECK"]+$totRePosted["TOTAL_MO"]+$totRePosted["TOTAL_EFT"]+$totRePosted["TOTAL_CC"]+$totRePosted["TOTAL_OTHER"];

	$re_posted_data=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="'.$colspan.'">Re-submitted Charges</td></tr>
	<tr>
        <td style="text-align:center; width:70px;" class="text_b_w nowrap">'.$firstGrpTitle.'</td>
		<td style="text-align:center; width:40px;" class="text_b_w nowrap">Patient#</td>
        <td style="text-align:center; width:70px" class="text_b_w">Charges</td>
        <td style="text-align:center; width:70px" class="text_b_w">Cash</td>
        <td style="text-align:center; width:70px" class="text_b_w">Check</td>
		<td style="text-align:center; width:70px" class="text_b_w">Money Order</td>
		<td style="text-align:center; width:70px" class="text_b_w">EFT</td>
		<td class="text_b_w" style="text-align:center; width:70px">Credit Cards</td>
		<td style="text-align:center; width:70px" class="text_b_w">Other</td>
        <td style="text-align:center; width:80px" class="text_b_w">Adjustment</td>
        <td style="text-align:center; width:80px" class="text_b_w">Write-Off</td>
        <td style="text-align:center; width:80px" class="text_b_w">Balance</td>
   </tr>
   '.$re_posted_part.'
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Re-submitted Total:</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.count($totRePosted["patient_count"]).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["totalAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["TOTAL_CASH"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["TOTAL_CHECK"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["TOTAL_MO"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["TOTAL_EFT"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["TOTAL_CC"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["TOTAL_OTHER"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["adj_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["write_off_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["totalBalance"],2).'</td>
	</tr>	
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="8">Total Paid:</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($total_paid,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
	</tr>	
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	</table>';		
}

//GRAND TOTALS
if(sizeof($grand_total_arr)>0){
	
	$grand_title_part=($reportType=='checkView')? ' (Except Check, EFT and MO)' :'';
	
	$totPaid=$grand_total_arr["TOTAL_CASH"]+$grand_total_arr["TOTAL_CHECK"]+$grand_total_arr["TOTAL_MO"]+$grand_total_arr["TOTAL_EFT"]+$grand_total_arr["TOTAL_CC"]+$grand_total_arr["TOTAL_OTHER"]; 
	
	$grand_totals=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="11">Grand Totals'.$grand_title_part.'</td></tr>
	<tr>
        <td style="text-align:center; width:70px;" class="text_b_w nowrap">Patient#</td>
        <td style="text-align:center; width:80px" class="text_b_w">Charges</td>
        <td style="text-align:center; width:80px" class="text_b_w">Cash</td>
        <td style="text-align:center; width:80px" class="text_b_w">Check</td>
		<td style="text-align:center; width:80px" class="text_b_w">Money Order</td>
		<td style="text-align:center; width:80px" class="text_b_w">EFT</td>
		<td class="text_b_w" style="text-align:center; width:80px">Credit Cards</td>
		<td style="text-align:center; width:80px" class="text_b_w">Other</td>
        <td style="text-align:center; width:80px" class="text_b_w">Adjustment</td>
        <td style="text-align:center; width:80px" class="text_b_w">Write-Off</td>
        <td style="text-align:center; width:90px" class="text_b_w">Balance</td>
    </tr>
	<tr><td colspan="11" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.count($grandTotalAmtArr["patient_count"]).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["totalAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_total_arr["TOTAL_CASH"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_total_arr["TOTAL_CHECK"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_total_arr["TOTAL_MO"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_total_arr["TOTAL_EFT"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_total_arr["TOTAL_CC"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_total_arr["TOTAL_OTHER"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["adj_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["write_off_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["totalBalance"],2).'</td>
	</tr>	
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">Total Paid: </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPaid,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
	</tr>	
	<tr><td colspan="11" class="total-row"></td></tr>
	</table>';		
}




// DEPARTMENT DETAILS
if($reportType=='checkView'){
	if(sizeof($arrDeptDetails)>0){
	
		$dept_title_part=($reportType=='checkView')? ' (Including Check, EFT and MO)' :'';
		
		$ledger_data.='
		<table class="rpt_table rpt rpt_table-bordered">
		<tr id="heading_orange" ><td colspan="6">Department Summary'.$dept_title_part.'</td></tr>
		<tr>
			<td class="text_b_w" style="text-align:center">Department</td>
			<td class="text_b_w" style="text-align:center">Charges</td>
			<td class="text_b_w" style="text-align:center">Total Paid</td>
			<td class="text_b_w" style="text-align:center">Adjustment</td>
			<td class="text_b_w" style="text-align:center">Write-Off</td>
			<td class="text_b_w" style="text-align:center">Balance</td>
		</tr>';
	
	/*	foreach($arrDeptDetails as $firstGrpId => $deptdata){
			$arrSubDept=array();
			
			if($viewBy=='physician'){
				$firstGrpTitle='Physician';
				$firstGrpName = $providerNameArr[$firstGrpId];
			}else{
				$firstGrpTitle='Facility';
				$firstGrpName = $arrAllFacilities[$firstGrpId];
			}
		
			$ledger_data .='<tr><td class="text_b_w" colspan="6">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';*/
			
			foreach($arrDeptDetails as $deptid => $deptDetails){
				
				$deptName= $arrDeptNames[$deptid];
	/*			$arrSubDept['CHARGES']+=$deptDetails["CHARGES"];
				$arrSubDept['PAID']+=$deptDetails["PAID"];
				$arrSubDept['ADJUSTMENT']+=$deptDetails["ADJUSTMENT"];
				$arrSubDept['WRITEOFF']+=$deptDetails["WRITEOFF"];
				$arrSubDept['BALANCE']+=$deptDetails["BALANCE"];*/
	
				$arrTotDept['CHARGES']+=$deptDetails["CHARGES"];
				$arrTotDept['PAID']+=$deptDetails["PAID"];
				$arrTotDept['ADJUSTMENT']+=$deptDetails["ADJUSTMENT"];
				$arrTotDept['WRITEOFF']+=$deptDetails["WRITEOFF"];
				$arrTotDept['BALANCE']+=$deptDetails["BALANCE"];			
				
				$ledger_data.='
				<tr>
					<td style="text-align:left; background:#FFFFFF; width:165px;" class="text_10">'.$deptName.'</td>
					<td style="text-align:right; background:#FFFFFF; width:165px;" class="text_10">'.$CLSReports->numberFormat($deptDetails["CHARGES"],2).'</td>
					<td style="text-align:right; background:#FFFFFF; width:165px;" class="text_10">'.$CLSReports->numberFormat($deptDetails["PAID"],2).'</td>
					<td style="text-align:right; background:#FFFFFF; width:165px;" class="text_10">'.$CLSReports->numberFormat($deptDetails["ADJUSTMENT"],2).'</td>
					<td style="text-align:right; background:#FFFFFF; width:165px;" class="text_10">'.$CLSReports->numberFormat($deptDetails["WRITEOFF"],2).'</td>
					<td style="text-align:right; background:#FFFFFF; width:165px;" class="text_10">'.$CLSReports->numberFormat($deptDetails["BALANCE"],2).'</td>
				</tr>';	
			}
	
	/*		$arrTotDept['CHARGES']+=$arrSubDept["CHARGES"];
			$arrTotDept['PAID']+=$arrSubDept["PAID"];
			$arrTotDept['ADJUSTMENT']+=$arrSubDept["ADJUSTMENT"];
			$arrTotDept['WRITEOFF']+=$arrSubDept["WRITEOFF"];
			$arrTotDept['BALANCE']+=$arrSubDept["BALANCE"];
	
			$ledger_data.='
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
		}*/
		
		$ledger_data.='
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
}else{
	if(sizeof($arrDeptDetails)>0){
		
		$dept_title_part=($reportType=='checkView')? ' (Including Check, EFT and MO)' :'';
		
		$ledger_data.='
		<table class="rpt_table rpt rpt_table-bordered">
		<tr id="heading_orange" ><td colspan="6">Department Summary'.$dept_title_part.'</td></tr>
		<tr>
			<td class="text_b_w" style="text-align:center">Department</td>
			<td class="text_b_w" style="text-align:center">Charges</td>
			<td class="text_b_w" style="text-align:center">Total Paid</td>
			<td class="text_b_w" style="text-align:center">Adjustment</td>
			<td class="text_b_w" style="text-align:center">Write-Off</td>
			<td class="text_b_w" style="text-align:center">Balance</td>
		</tr>';
	
		foreach($arrDeptDetails as $firstGrpId => $deptdata){
			$arrSubDept=array();
			
			if($viewBy=='physician'){
				$firstGrpTitle='Physician';
				$firstGrpName = $providerNameArr[$firstGrpId];
			}else{
				$firstGrpTitle='Facility';
				$firstGrpName = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$firstGrpId] : $arrAllFacilities[$firstGrpId];
			}
		
			$ledger_data .='<tr><td class="text_b_w" colspan="6">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
			
			foreach($deptdata as $deptid => $deptDetails){
				
				$deptName= $arrDeptNames[$deptid];
				$arrSubDept['CHARGES']+=$deptDetails["CHARGES"];
				$arrSubDept['PAID']+=$deptDetails["PAID"];
				$arrSubDept['ADJUSTMENT']+=$deptDetails["ADJUSTMENT"];
				$arrSubDept['WRITEOFF']+=$deptDetails["WRITEOFF"];
				$arrSubDept['BALANCE']+=$deptDetails["BALANCE"];
				
				$ledger_data.='
				<tr>
					<td style=" width:165px; text-align:left; background:#FFFFFF;" class="text_10">'.$deptName.'</td>
					<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["CHARGES"],2).'</td>
					<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["PAID"],2).'</td>
					<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["ADJUSTMENT"],2).'</td>
					<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["WRITEOFF"],2).'</td>
					<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["BALANCE"],2).'</td>
				</tr>';	
			}
	
			$arrTotDept['CHARGES']+=$arrSubDept["CHARGES"];
			$arrTotDept['PAID']+=$arrSubDept["PAID"];
			$arrTotDept['ADJUSTMENT']+=$arrSubDept["ADJUSTMENT"];
			$arrTotDept['WRITEOFF']+=$arrSubDept["WRITEOFF"];
			$arrTotDept['BALANCE']+=$arrSubDept["BALANCE"];
	
			$ledger_data.='
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
		
		$ledger_data.='
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
}

//-- OPERATOR INITIAL -------
$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$opInitial = $authProviderNameArr[1][0];
$opInitial .= $authProviderNameArr[0][0];
$opInitial = strtoupper($opInitial);
$notPosted='';

$totalLabel='Total';

$page_header='
<table class="rpt_table rpt rpt_table-bordered rpt_padding">
    <tr >
        <td style="text-align:left;" class="rptbx1" width="350">Ledger (Summary)</td>
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


$totalChargesAmt=$grandTotalAmtArr['totalAmt'];
$totalPaymentAmt=$grandTotalAmtArr['pat_paid_amt'];
$totalAdjustmentAmt=$grandTotalAmtArr["write_off_amt"] + $grandTotalAmtArr["adj_amt"];

$csv_file_data=$csv_check_data;
if($reportType=='checkView'){
	if($posted_data!='' || $not_posted_data!='' || $re_posted_data!=''){
		$csv_file_data.='<br><table class="rpt_table rpt rpt_table-bordered">
		<tr id="heading_orange" ><td>Transactions Except Check, EFT and MO</td></tr></table>';
	}
}
if($reportType!='checkView'){
	$csv_file_data.=$page_header;
}
$csv_file_data.=
$posted_data.
$not_posted_data.
$re_posted_data.
$grand_totals.
$ledger_data;


//IN GRROUP BY INSURANCE CASE - REMOVING COLUMN THAT ARE NOT IN USE FOR THIS GROUPING
//$np_page_content_pdf = preg_replace('/<td class="notInPdf(.+)<\/td>/', '', $not_posted_content);

//IN GRROUP BY INSURANCE CASE - REMOVING COLUMN THAT ARE NOT IN USE FOR THIS GROUPING
if($viewBy=='insurance'){
	//$pdf_page_content = preg_replace('/<td class="notInsGroupBy(.+)<\/td>/', '', $pdf_page_content);
}
?>
