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
ob_start();


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

$colspan=16;
$colspanPdf=12;
$startColspan=5;
$startColspanPDF=4;

$tdTotal='';
/*if($registered_fac=='1'){
	$colspan=20;
	$tdTotal = '<td width="auto" class="text_10" style="text-align:left; background:#FFFFFF;"></td>';
	$startColspan='6';
	$startColspanPDF='4';

	if($viewBy=='insurance'){
		$colspan=15;
	}	
}
*/

//INCLUDE/EXCLUDE COLUMNS
if($inc_payments=='')$colspan=$colspan-4;
if($inc_adjustments=='')$colspan=$colspan-2;
if($inc_summary_charges=='')$colspan=$colspan-1;

$pdf_col = $colspan - 1;
$col_width = 900/$pdf_col; 
$col_width = $col_width.'px;';


$normal_available=0;
foreach($mainResArr as $firstGrpId => $firstGrpData){
	$normal_available=1;		
	$arrFirstGrpTotal = array();
	
	if($viewBy=='physician' || $viewBy=='operator'){
		$firstGrpTitle=($viewBy=='physician')? 'Filing Physician' : 'Operator';
		$firstGrpName = $providerNameArr[$firstGrpId];
	}else if($viewBy=='department'){
		$firstGrpTitle='Department';
		$firstGrpName = $arrDeptNames[$firstGrpId];
	}else if($viewBy=='groups'){
		$firstGrpTitle='Group';
		$firstGrpName = $arrAllGroups[$firstGrpId];
	}else if($viewBy=='insurance'){
		$firstGrpTitle='Insurance';
		$firstGrpName = $arrAllInsCompanies[$firstGrpId];
	}elseif($viewBy=='procedure'){
		$firstGrpTitle='Procedure';
		$firstGrpName = $arrAllCPTCodes[$firstGrpId];
	}elseif($viewBy=='ins_group'){
		$firstGrpTitle='Ins Group';
		$firstGrpName = $arrAllInsGroups[$firstGrpId];
	}else{
		$firstGrpTitle='Facility';
		$firstGrpName = $arrAllFacilities[$firstGrpId];
	}

	$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		$csvFileDataPrint .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
	
		foreach($firstGrpData as $secGrpId => $secGrpData){
			$subTotalAmtArr = array();
			$arrSecGrpTotal = array();
	
			if($viewBy=='physician' || $viewBy=='operator'){
				$secGrpTitle='Facility';
				$secGrpName = $arrAllFacilities[$secGrpId];
			}else{
				$secGrpTitle='Filing Physician';
				$secGrpName = $providerNameArr[$secGrpId];
			}
			$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
			$csvFileDataPrint .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
	
			foreach($secGrpData as $enc_id => $encDataArr){

				$patient_name = core_name_format($encDataArr[0]['lname'], $encDataArr[0]['fname'], $encDataArr[0]['mname']);		
				$patient_id = $encDataArr[0]['patient_id'];
				$date_of_service = $encDataArr[0]['date_of_service'];
				$default_facility = $encDataArr[0]['default_facility'];
				$primaryInsName  = $arrAllInsCompanies[$encDataArr[0]['pri_ins_id']];
				$secondaryInsName = $arrAllInsCompanies[$encDataArr[0]['sec_ins_id']];
				
				if($default_facility<=0 || $default_facility==''){
					$default_facility = $arrHomeFacOfPatients[$patient_id];
				}
	
				//--- GET TOTAL AMOUNT ----		
				$totalAmtArr = array();		
				$write_off_amt_arr = array();
				$arrCPT = array();
				$arrCPT_CSV = array();
				$arrDxCodes = array();
				$totalBalance =$insuranceDue = $patientDue = $patPaidAmt= $insPaidAmt= $priPaidAmt= $secPaidAmt= 0;
				$patientPaidAmt = $crd_dbt_amt = $patCrdDbt = $insCrdDbt = $adj_amt = $write_off_amt= $creditProcAmount=0;
				$lastPaidDOT='';
				
				for($d=0;$d<count($encDataArr);$d++){
					$patCD=$insCD=0;
					$chgDetId = $encDataArr[$d]['charge_list_detail_id'];
	
					if($encDataArr[$d]['totalAmt']>0){
						$totalAmtArr[] = $encDataArr[$d]['totalAmt'];
						$subTotalAmtArr["totalAmt"][] = $encDataArr[$d]['totalAmt'];
					}
					
					//PAT & INS DUES
					$insuranceDue+= $encDataArr[$d]['pri_due'] + $encDataArr[$d]['sec_due'] + $encDataArr[$d]['tri_due'];
					$patientDue+= $encDataArr[$d]['pat_due'];
					
					if($viewBy=='insurance'){
						if(!$tempIsChgDetId2[$firstGrpId][$chgDetId]){ //SOME ENCOUNTER HAS SAME PRIMARY AND SEC INS SO TO AVOID DUPLICATE OF AMOUNTS THIS CHECK ADDED. 
							//CREDIT/DEBIT
							$patCrdDbt= 0;			
							$insCrdDbt= $pay_crd_deb_arr[$firstGrpId][$chgDetId];
							
							// TOTAL PAYMENT
							$patientPaidAmt+= $mainEncounterPayArr[$firstGrpId][$chgDetId] + $insCrdDbt;

							//WRITE-OFF & ADJUSTMENTS
							$write_off_amt+= $normalWriteOffAmt[$firstGrpId][$chgDetId] + $writte_off_arr[$firstGrpId][$chgDetId];
							$adj_amt+= $arrAdjustmentAmt[$firstGrpId][$chgDetId];				
							
							unset($arrOtherGrandTotal['charges'][$chgDetId]);
							unset($arrOtherGrandTotal['ins_due'][$chgDetId]);
							unset($arrOtherGrandTotal['pat_due'][$chgDetId]);
							unset($arrOtherGrandTotal['over_payment'][$chgDetId]);
							unset($arrOtherGrandTotal['balance'][$chgDetId]);
							$tempIsChgDetId2[$firstGrpId][$chgDetId]=$chgDetId;
						}
					}else{
						//CREDIT/DEBIT
						$patCrdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'];			
						$insCrdDbt= $pay_crd_deb_arr[$chgDetId]['Insurance'];
						
						// TOTAL PAYMENT
						$patientPaidAmt+= $mainEncounterPayArr[$chgDetId] + ($patCrdDbt + $insCrdDbt);

						//WRITE-OFF & ADJUSTMENTS
						$write_off_amt+= $normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
						$adj_amt+= $arrAdjustmentAmt[$chgDetId];				
					}

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

					if($viewBy!='insurance'){
						// PATIENT PAID
						$patPaidAmt+=$patPayDetArr[$chgDetId]['patPaid'] + $patCrdDbt;
						// INSURANCE PAID
						$insPaidAmt+= $patPayDetArr[$chgDetId]['insPaid'] + $insCrdDbt;
						// PRI INSURANCE PAID
						if($patPayDetArr[$chgDetId]['priPaid']>0){
							$priPaidAmt+= $patPayDetArr[$chgDetId]['priPaid'] + $insCrdDbt;
							$insCrdDbt=0;
						}
						// SEC+TER INSURANCE PAID
						$secPaidAmt+= $patPayDetArr[$chgDetId]['secPaid'] + $patPayDetArr[$chgDetId]['terPaid'] + $insCrdDbt;
					}
					
					//LAST PAID DOT
					if($patPayDetArr[$chgDetId]['lastDOT']!=null && $patPayDetArr[$chgDetId]['lastDOT']!='' && $patPayDetArr[$chgDetId]['lastDOT']!='00-00-0000'){
						$lastPaidDOT= $patPayDetArr[$chgDetId]['lastDOT'];
					}

					$cptCode = $encDataArr[$d]['cpt_prac_code'];
					$arrCPT_CSV[$cptCode]=$cptCode;
					if(strlen($cptCode)>7){ $cptCode = substr($cptCode, 0, 7).'..';}
					$arrCPT[] = $cptCode;
					if($encDataArr[$d]['dx_id1']!=''){ $arrDxCodes[$encDataArr[$d]['dx_id1']]=$encDataArr[$d]['dx_id1'];}
					if($encDataArr[$d]['dx_id2']!=''){ $arrDxCodes[$encDataArr[$d]['dx_id2']]=$encDataArr[$d]['dx_id2'];}
					if($encDataArr[$d]['dx_id3']!=''){ $arrDxCodes[$encDataArr[$d]['dx_id3']]=$encDataArr[$d]['dx_id3'];}
					if($encDataArr[$d]['dx_id4']!=''){ $arrDxCodes[$encDataArr[$d]['dx_id4']]=$encDataArr[$d]['dx_id4'];}
					
					//GRAND TOTAL - TO AVOID DUPLICATE OF CHARGES, CREDIT AND BALANCE IN CASE OF GROUP BY INSURANCE CASE
					if(!$tempIsChgDetId[$chgDetId]){
						$grandTotalAmtArr["totalAmt"]+= $encDataArr[$d]['totalAmt'];
						$grandTotalAmtArr["creditProcAmount"]+= $encDataArr[$d]['over_payment'];
						$grandTotalAmtArr["insuranceDue"]+= $encDataArr[$d]['pri_due'] + $encDataArr[$d]['sec_due'] + $encDataArr[$d]['tri_due'];
						$grandTotalAmtArr["patientDue"]+= $encDataArr[$d]['pat_due'];
						$grandTotalAmtArr["totalBalance"]+= $balAmt;	
						$tempIsChgDetId[$chgDetId]=$chgDetId;
					}					
				}
				$totalAmt = array_sum($totalAmtArr);

				//if($DateRangeFor=='date_of_service'){
				//	$adj_amt= $arrAdjustmentAmt[$enc_id];				
				//}
	
				$insuranceDue = ($insuranceDue<0) ? 0 : $insuranceDue; 
				$subTotalAmtArr["insuranceDue"][] = $insuranceDue;
				$patientDue = ($patientDue<0) ? 0 : $patientDue;
				$patientDue = ($totalBalance<=0) ? 0 : $patientDue;
				
				$subTotalAmtArr["patientDue"][] = $patientDue;
				$subTotalAmtArr["totalBalance"][] = $totalBalance;

				$subTotalAmtArr["pat_paid_amt"][] = $patientPaidAmt;
				$subTotalAmtArr["patPaidAmt"][] = $patPaidAmt;
				$subTotalAmtArr["insPaidAmt"][] = $insPaidAmt;
				$subTotalAmtArr["priPaidAmt"][] = $priPaidAmt;
				$subTotalAmtArr["secPaidAmt"][] = $secPaidAmt;
				$subTotalAmtArr["creditProcAmount"][] = $creditProcAmount;
				$subTotalAmtArr["adj_amt"][] = $adj_amt;
				$subTotalAmtArr["write_off_amt"][] = $write_off_amt;


		
				//--- CHANGE NUMBER FORMAT FOR ENCOUNTER ---
				$totalAmt = $CLSReports->numberFormat($totalAmt,2);
				$patientPaidAmt = $CLSReports->numberFormat($patientPaidAmt,2);
				$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
				$patientDue = $CLSReports->numberFormat($patientDue,2);		
				$creditProcAmount = $CLSReports->numberFormat($creditProcAmount,2);		
				$adj_amt = $CLSReports->numberFormat($adj_amt,2);
				$write_off_amt = $CLSReports->numberFormat($write_off_amt,2);
				$totalBalance = $CLSReports->numberFormat($totalBalance,2);
				$patPaidAmt = $CLSReports->numberFormat($patPaidAmt,2);
				$insPaidAmt = $CLSReports->numberFormat($insPaidAmt,2);
				$priPaidAmt = $CLSReports->numberFormat($priPaidAmt,2);
				$secPaidAmt = $CLSReports->numberFormat($secPaidAmt,2);
				
				$strCPT = implode(', ', $arrCPT);
				$strCPT_CSV = implode(', ', $arrCPT_CSV);
				$strDxCodes = implode(', ', $arrDxCodes);
			
			$tdPart=$tdPartPDF=$encTDPDF=$subTotBlankTD='';	
			$encTDPDF='<td width="60" class="text_10" style="background:#FFFFFF;">'.$enc_id.'</td>';
			if($registered_fac=='1'){
				//$tdPart='<td class="text_10" style="background:#FFFFFF;">'.$arrAllFacilities[$default_facility].'</td>';
				//$tdPartPDF='<td width="60" class="text_10" style="background:#FFFFFF;">'.$arrAllFacilities[$default_facility].'</td>';
				//$subTotBlankTD='<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>';
				//$encTDPDF='';
			}

			$chargesTDs=$paymentTDs=$adjustmentTDs='';
			if($inc_summary_charges==1){
				$chargesTDs='
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$totalAmt.'</td>';
			}
			if($inc_payments==1){
				$paymentTDs='
				<td class="notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">'.$patPaidAmt.'</td>
				<td class="notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">'.$priPaidAmt.'</td>
				<td class="notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">'.$secPaidAmt.'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$patientPaidAmt.'</td>';
			}
			if($inc_adjustments==1){
				$adjustmentTDs='
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$write_off_amt.'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$adj_amt.'</td>';
			}			

			$csvFileData.='
			<tr>
				<td class="text_10" style="background:#FFFFFF;">'.$patient_name.' - '.$patient_id.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$date_of_service.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$lastPaidDOT.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$enc_id.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$strCPT_CSV.'</td>

				<!--<td class="text_10" style="background:#FFFFFF;">'.$strDxCodes.'</td>
				<td class="notInsGroupBy text_10" style="background:#FFFFFF;">'.$primaryInsName.'</td>
				<td class="notInsGroupBy text_10" style="background:#FFFFFF;">'.$secondaryInsName.'</td>-->
				'.$chargesTDs.'
				'.$paymentTDs.'
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$creditProcAmount.'</td>
				'.$adjustmentTDs.'
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$insuranceDue.'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$patientDue.'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$totalBalance.'</td>
			</tr>';

			$csvFileDataPrint .= <<<DATA
			<tr>
				<td class="text_10" style="background:#FFFFFF;">$patient_name - $patient_id</td>
				<td class="text_10" style="background:#FFFFFF;">$date_of_service</td>
				<td class="text_10" style="background:#FFFFFF;">$lastPaidDOT</td>
				<td class="text_10" style="background:#FFFFFF;">$enc_id</td>
				<td class="text_10" style="background:#FFFFFF; width:$col_width">$strCPT_CSV</td>
				$chargesTDs
				$paymentTDs
				<td class="text_10" style="text-align:right; background:#FFFFFF;">$creditProcAmount</td>
				$adjustmentTDs
				<td class="text_10" style="text-align:right; background:#FFFFFF;">$insuranceDue</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">$patientDue</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">$totalBalance</td>
			</tr>
DATA;
		}

			// SECOND GROUP TOTAL
			$sub_total_amt = array_sum($subTotalAmtArr['totalAmt']);
			$sub_pat_paid_amt = array_sum($subTotalAmtArr['pat_paid_amt']);
			$sub_ins_due = array_sum($subTotalAmtArr['insuranceDue']);
			$sub_patient_due = array_sum($subTotalAmtArr['patientDue']);
			$sub_credit_amt = array_sum($subTotalAmtArr['creditProcAmount']);
			$sub_write_off_amt = array_sum($subTotalAmtArr['write_off_amt']);
			$sub_adj_amt = array_sum($subTotalAmtArr['adj_amt']);
			$sub_total_balance = array_sum($subTotalAmtArr['totalBalance']);
			$sub_patPaidAmt = array_sum($subTotalAmtArr['patPaidAmt']);
			$sub_insPaidAmt = array_sum($subTotalAmtArr['insPaidAmt']);
			$sub_priPaidAmt = array_sum($subTotalAmtArr['priPaidAmt']);
			$sub_secPaidAmt = array_sum($subTotalAmtArr['secPaidAmt']);
	
	
			//--- GET GRAND TOTAL AMIUNT ---
			$arrFirstGrpTotal["totalAmt"]+= $sub_total_amt;
			$arrFirstGrpTotal["pat_paid_amt"]+= $sub_pat_paid_amt;
			$arrFirstGrpTotal["insuranceDue"]+= $sub_ins_due;
			$arrFirstGrpTotal["patientDue"]+= $sub_patient_due;
			$arrFirstGrpTotal["creditProcAmount"]+= $sub_credit_amt;
			$arrFirstGrpTotal["write_off_amt"]+= $sub_write_off_amt;
			$arrFirstGrpTotal["adj_amt"]+= $sub_adj_amt;
			$arrFirstGrpTotal["totalBalance"]+= $sub_total_balance;			
			$arrFirstGrpTotal["patPaidAmt"]+= $sub_patPaidAmt;
			$arrFirstGrpTotal["insPaidAmt"]+= $sub_insPaidAmt;
			$arrFirstGrpTotal["priPaidAmt"]+= $sub_priPaidAmt;
			$arrFirstGrpTotal["secPaidAmt"]+= $sub_secPaidAmt;
	
			// SECOND GROUP TOTAL	
			$sub_total_amt = $CLSReports->numberFormat($sub_total_amt,2);
			$sub_pat_paid_amt = $CLSReports->numberFormat($sub_pat_paid_amt,2);
			$sub_ins_due = $CLSReports->numberFormat($sub_ins_due,2);
			$sub_patient_due = $CLSReports->numberFormat($sub_patient_due,2);			
			$sub_credit_amt = $CLSReports->numberFormat($sub_credit_amt,2);			
			$sub_write_off_amt = $CLSReports->numberFormat($sub_write_off_amt,2);
			$sub_adj_amt = $CLSReports->numberFormat($sub_adj_amt,2);
			$sub_total_balance = $CLSReports->numberFormat($sub_total_balance,2);
			$sub_patPaidAmt = $CLSReports->numberFormat($sub_patPaidAmt,2);
			$sub_insPaidAmt = $CLSReports->numberFormat($sub_insPaidAmt,2);
			$sub_priPaidAmt = $CLSReports->numberFormat($sub_priPaidAmt,2);
			$sub_secPaidAmt = $CLSReports->numberFormat($sub_secPaidAmt,2);

		$chargesTDs=$paymentTDs=$adjustmentTDs='';
		if($inc_summary_charges==1){
			$chargesTDs='
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_total_amt.'</td>';
		}
		if($inc_payments==1){
			$paymentTDs='
			<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$sub_patPaidAmt.'</td>
			<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$sub_priPaidAmt.'</td>
			<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$sub_secPaidAmt.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_pat_paid_amt.'</td>';
		}
		if($inc_adjustments==1){
			$adjustmentTDs='
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_write_off_amt.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_adj_amt.'</td>';
		}			
						
		$csvFileData .='
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">'.$secGrpTitle.' Total : </td>
			'.$chargesTDs.'
			'.$paymentTDs.'
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_credit_amt.'</td>
			'.$adjustmentTDs.'
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_ins_due.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_patient_due.'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_total_balance.'</td>
		</tr>	
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
		

		$csvFileDataPrint .= <<<DATA
		<tr><td colspan="$colspan" class="total-row"></td></tr>
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="$startColspan">$secGrpTitle Total : </td>
			$chargesTDs
			$paymentTDs
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_credit_amt</td>
			$adjustmentTDs
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_ins_due</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_patient_due</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_balance</td>
		</tr>	
		<tr><td colspan="$colspan" class="total-row"></td></tr>
DATA;
	} // END SECOND GROUP
	
	
	// FIRST GROUP TOTAL
	//$grandTotalAmtArr["totalAmt"]+= $arrFirstGrpTotal["totalAmt"];
	$grandTotalAmtArr["patPaidAmt"]+= $arrFirstGrpTotal["patPaidAmt"];
	$grandTotalAmtArr["insPaidAmt"]+= $arrFirstGrpTotal["insPaidAmt"];
	$grandTotalAmtArr["priPaidAmt"]+= $arrFirstGrpTotal["priPaidAmt"];
	$grandTotalAmtArr["secPaidAmt"]+= $arrFirstGrpTotal["secPaidAmt"];
	$grandTotalAmtArr["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
	//$grandTotalAmtArr["creditProcAmount"]+= $arrFirstGrpTotal["creditProcAmount"];
	$grandTotalAmtArr["write_off_amt"]+= $arrFirstGrpTotal["write_off_amt"];
	$grandTotalAmtArr["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];
	//$grandTotalAmtArr["insuranceDue"]+= $arrFirstGrpTotal["insuranceDue"];
	//$grandTotalAmtArr["patientDue"]+= $arrFirstGrpTotal["patientDue"];
	//$grandTotalAmtArr["totalBalance"]+= $arrFirstGrpTotal["totalBalance"];	


	$chargesTDs=$paymentTDs=$adjustmentTDs='';
	if($inc_summary_charges==1){
		$chargesTDs='
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>';
	}
	if($inc_payments==1){
		$paymentTDs='
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($arrFirstGrpTotal["patPaidAmt"],2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($arrFirstGrpTotal["priPaidAmt"],2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($arrFirstGrpTotal["secPaidAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2).'</td>';
	}
	if($inc_adjustments==1){
		$adjustmentTDs='
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>';
	}		

	$csvFileData .='
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">'.$firstGrpTitle.' Total : </td>
		'.$chargesTDs.'
		'.$paymentTDs.'
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["creditProcAmount"],2).'</td>
		'.$adjustmentTDs.'
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["insuranceDue"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["patientDue"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
		'.$tdTotal.'
	</tr>	
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';


	$csvFileDataPrint .='
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">'.$firstGrpTitle.' Total : </td>
		'.$chargesTDs.'
		'.$paymentTDs.'
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["creditProcAmount"],2).'</td>
		'.$adjustmentTDs.'
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["insuranceDue"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["patientDue"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
		'.$tdTotal.'
	</tr>	
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
}		
//-- OPERATOR INITIAL -------
$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$opInitial = $authProviderNameArr[1][0];
$opInitial .= $authProviderNameArr[0][0];
$opInitial = strtoupper($opInitial);
$printFile = true;
$notPosted='';


$totalLabel='Total';
$showTotalAgain=0;
$totalCharges=$grandTotalAmtArr["totalAmt"];
$totalBalance=$grandTotalAmtArr["totalBalance"];

if($chargesForNotPosted>0){
	$showTotalAgain=1;
	$totalLabel='Sub Total';
	
	$totalCharges= $grandTotalAmtArr["totalAmt"] + $chargesForNotPosted;
	$totalBalance= $grandTotalAmtArr["totalBalance"] + $chargesForNotPosted;

	$chargesTDs=$paymentTDs=$adjustmentTDs='';
	if($inc_summary_charges==1){
		$chargesTDs='
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($chargesForNotPosted,2).'</td>';
	}
	if($inc_payments==1){
		$paymentTDs='
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
        <td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>';
	}
	if($inc_adjustments==1){
		$adjustmentTDs='
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>';
	}		
	
	$notPostedCSV='
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
  <tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">Not Posted Amount : </td>
        '.$chargesTDs.'
		'.$paymentTDs.'
        <td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
    	'.$adjustmentTDs.'
	    <td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($chargesForNotPosted,2).'</td>
        '.$tdTotal.'
    </tr>	
    <tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
	
	$notPostedPrint='
	<tr><td colspan="'.$colspanPdf.'" class="total-row"></td></tr>
	<tr>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspanPDF.'">Not Posted Amount :</td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($chargesForNotPosted,2).'</td>
		<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;"></td>
		<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;"></td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td width="65" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($chargesForNotPosted,2).'</td>
		'.$subTotBlankTD.'
	</tr>	
	<tr><td colspan="'.$colspanPdf.'" class="total-row"></td></tr>';
}

$otherTotals='';
$otherPayments=$otherWriteOff=$otherAdj=$otherCharges=$otherInsDue=$otherPatDue=$otherOverPayment=$otherBalance=0;


if($showTotalAgain==1){

	$chargesTDs=$paymentTDs=$adjustmentTDs='';
	if($inc_summary_charges==1){
		$chargesTDs='
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totalCharges+$otherCharges,2).'</td>';
	}
	if($inc_payments==1){
		$paymentTDs='
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr["patPaidAmt"],2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr["priPaidAmt"],2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr["secPaidAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["pat_paid_amt"]+$otherPayments,2).'</td>';
	}
	if($inc_adjustments==1){
		$adjustmentTDs='
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["write_off_amt"]+$otherWriteOff,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["adj_amt"]+$otherAdj,2).'</td>';
	}		
		
    $totalsRowCSV=
	$notPostedCSV
	.$otherTotals
    .'<tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">Total : </td>
		'.$chargesTDs.'
		'.$paymentTDs.'
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["creditProcAmount"]+$otherOverPayment,2).'</td>
		'.$adjustmentTDs.'
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["insuranceDue"]+$otherInsDue,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["patientDue"]+$otherPatDue,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totalBalance+$otherBalance,2).'</td>
        '.$tdTotal.'
    </tr>	
    <tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';	
	
	 $totalsRowPrint=
	 $notPostedPrint
	 .$otherTotalsPrint
	 .'<tr>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspanPDF.'">Total :</td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totalCharges+$otherCharges,2).'</td>
		<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr["patPaidAmt"],2).'</td>
		<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr["insPaidAmt"],2).'</td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["pat_paid_amt"]+$otherPayments,2).'</td>
		<td width="65" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["creditProcAmount"]+$otherOverPayment,2).'</td>
		<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["write_off_amt"]+$otherWriteOff,2).'</td>
		<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["adj_amt"]+$otherAdj,2).'</td>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["insuranceDue"]+$otherInsDue,2).'</td>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["patientDue"]+$otherPatDue,2).'</td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totalBalance+$otherBalance,2).'</td>
		'.$subTotBlankTD.'
	</tr>	
	<tr><td colspan="'.$colspanPdf.'" class="total-row"></td></tr>';
}
$firstGroupTitle = 'Facility Name';
$secGroupTitle = 'Physician Name';
$subTotalTitle = 'Physician Total';
$groupBy = $viewBy;
if($groupBy=='facility'){
	$firstGroupTitle = 'Physician Name';
	$secGroupTitle = 'Facility Name';
	$subTotalTitle = 'Facility Total';
}elseif($groupBy=='groups'){
	$firstGroupTitle = 'Physician Name';
	$secGroupTitle = 'Group Name';
	$subTotalTitle = 'Group Total';
}elseif($groupBy=='operator'){
	$firstGroupTitle = 'Facility Name';
	$secGroupTitle = 'Operator Name';
	$subTotalTitle = 'Operator Total';
}elseif($groupBy=='department'){
	$firstGroupTitle = 'Physician Name';
	$secGroupTitle = 'Department Name';
	$subTotalTitle = 'Department Total';
}
$show_appt =	$inc_appt_detail ? true : false;
if($show_appt)
{
	$firstApptGroupTitle = $firstGroupTitle;
	$secApptGroupTitle = $secGroupTitle;
	if($groupBy=='department'){
		$firstApptGroupTitle = 'Facility Name';
		$secApptGroupTitle = 'Physician Name';
	}

	if( is_array( $apptDetailsArr) && count( $apptDetailsArr) > 0 )
	{
		$appt_data .= '
		<tr id="heading_orange"><td colspan="6">&nbsp;<b>Appointment Details</b></td></tr>
		<tr>
			<td class="text_b_w" style="width:15%;">'.$firstApptGroupTitle.'</td>
			<td class="text_b_w" style="width:20%;">Patient Name</td>
			<td class="text_b_w" style="width:15%;">DOS</td>
			<td class="text_b_w" style="width:15%;">Time</td>
			<td class="text_b_w" style="width:20%;">Procedure</td>
			<td class="text_b_w" style="width:15%;">Status</td>
		</tr>';
		
		foreach($apptDetailsArr as $firstApptGroupId => $firstApptGroupData)
		{
			$firstApptGroupName = '';
			//--- GET SELECTED GROUP BY NAME ----
			if($groupBy=='physician' || $groupBy=='operator'){
			$tmp_name = $providerNameArr[$firstApptGroupId];
			if( $groupBy=='operator' && $firstApptGroupId == 0 )
				$tmp_name = 'No Operator' ;
			$firstApptGroupName = $tmp_name;
			}elseif($groupBy=='groups'){
				$firstApptGroupName = $arrAllGroups[$firstApptGroupId];
			}elseif($groupBy=='department'){
				$firstApptGroupName = $providerNameArr[$firstApptGroupId];
			}else{
				$firstApptGroupName = $arrAllFacilities[$firstApptGroupId];
			}
			
			if( is_array( $firstApptGroupData) && count( $firstApptGroupData) > 0 )
			{		
				$appt_data .= '<tr><td class="text_b_w" colspan="6">'.$secApptGroupTitle.' : '.$firstApptGroupName.'</td></tr>';
				
				foreach( $firstApptGroupData as $secApptGroupData)
				{	
					$secApptGroupName = '';
					if($groupBy=='physician' || $groupBy=='operator' || $groupBy=='department' ){
						$secApptGroupName = $arrAllFacilities[$secApptGroupData['sec_group_id']];
					}else{
						$secApptGroupName = $providerNameArr[$secApptGroupData['sec_group_id']];				
					}	
					
					$appt_data .= '
					<tr>
						<td class="text_10">'.$secApptGroupName.'&nbsp;</td>
						<td class="text_10">'.$secApptGroupData['sa_patient_name'].'-'.$secApptGroupData['sa_patient_id'].'</td>
						<td class="text_10">'.get_date_format($secApptGroupData['sa_app_start_date']).'</td>
						<td class="text_10">'.core_time_format($secApptGroupData['sa_app_starttime']).'</td>
						<td class="text_10">'.$secApptGroupData['proc_name'].'</td>
						<td class="text_10">'.$arrApptStatus[$secApptGroupData['sa_patient_app_status_id']].'</td>
					</tr>';		
							
				}
			}
		}
	}
}

if($normal_available==1 || count($arrVoidPay)>0){
?>
<table class="rpt_table rpt rpt_table-bordered rpt_padding">
    <tr >
        <td style="text-align:left;" class="rptbx1" width="33%">Financial - <?php echo $dbtemp_name;?> Report (Detail)</td>
        <td style="text-align:left;" class="rptbx2" width="34%"><?php print "$dayReport ($search) From : $Sdate To : $Edate"; ?></td>
        <td style="text-align:left;" class="rptbx3" width="33%"><?php print " Created by $opInitial on $curDate"; ?></td>
    </tr>
    <tr>
        <td class="rptbx1">Group : <?php echo $selgroup; ?></td>
        <td class="rptbx2">Facility : <?php  echo $selFac; ?></td>
        <td class="rptbx3">Filing Phy. : <?php echo $selPhy; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Selected Oper.: <?php echo $selOpr; ?>        
        </td>
    </tr>
    <tr>
        <td class="rptbx1">Credit Phy. : <?php echo $selCrPhy; ?></td>
        <td class="rptbx2">Insurance : <?php  echo $selInsurance; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Ins. Type : <?php  echo $selInsType; ?>
        </td>
        <td class="rptbx3">CPT : <?php echo $selCPT; ?></td>
    </tr>
    <tr>
        <td class="rptbx1">ICD9 : <?php echo $selDX; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        ICD10 : <?php echo $selDX10; ?>
        </td>
        <td class="rptbx2">Modifiers : <?php  echo $selModifiers; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       		Adj. Code : <?php  echo $selAdjCode; ?>
        </td>
        <td class="rptbx3">Write-off Code : <?php echo $selWriteoff; ?></td>
    </tr>
</table>
<?php
}

if($normal_available==1){ 
$totalChargesAmt=$grandTotalAmtArr['totalAmt'] + $chargesForNotPosted;
$totalPaymentAmt=$grandTotalAmtArr['pat_paid_amt'];
$totalAdjustmentAmt=$grandTotalAmtArr["write_off_amt"] + $grandTotalAmtArr["adj_amt"];

$chargesTDs=$paymentTDs=$adjustmentTDs='';
$grd_chargesTDs=$grd_paymentTDs=$grd_adjustmentTDs='';
if($inc_summary_charges==1){
	$chargesTDs='
	<td class="text_b_w" style="text-align:center; width:80px">Charges</td>';

	$grd_chargesTDs='
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr['totalAmt'],2).'</td>';
}
if($inc_payments==1){
	$paymentTDs='
	<td class="notInsGroupBy text_b_w" style="text-align:center; width:70px">Pat. Paid</td>
	<td class="notInsGroupBy text_b_w" style="text-align:center; width:70px">Pri. Paid</td>
	<td class="notInsGroupBy text_b_w" style="text-align:center; width:70px">Sec. Paid</td>
	<td style="text-align:center; width:80px" class="text_b_w">Tot. Paid</td>';

	$grd_paymentTDs='
	<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr['patPaidAmt'],2).'</td>
	<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr['priPaidAmt'],2).'</td>
	<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr['secPaidAmt'],2).'</td>
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2).'</td>';
}
if($inc_adjustments==1){
	$adjustmentTDs='
	<td style="text-align:center; width:100px" class="text_b_w">Write-Off</td>
	<td style="text-align:center; width:100px" class="text_b_w">Adjustment</td>';

	$grd_adjustmentTDs='
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr['write_off_amt'],2).'</td>
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'</td>';
}		
?>
<table class="rpt_table rpt rpt_table-bordered">
    <tr>
        <td style="text-align:center; width:140px;" class="text_b_w nowrap">Patient Name</td>
        <td style="text-align:center; width:100px" class="text_b_w">DOS</td>
        <td style="text-align:center; width:100px" class="text_b_w">DOT</td>
        <td style="text-align:center; width:60px" class="text_b_w">Enc. #</td>
        <td style="text-align:center; width:60px" class="text_b_w">CPT</td>    
        <?php echo $chargesTDs.$paymentTDs;?>
        <td style="text-align:center; width:90px" class="text_b_w">Credit</td>
		<?php echo $adjustmentTDs;?>        
        <td style="text-align:center; width:80px" class="text_b_w">Ins. Due</td>
        <td style="text-align:center; width:90px" class="text_b_w">Pat. Due</td>
        <td style="text-align:center; width:100px" class="text_b_w">Balance</td>
    </tr>
    <?php print $csvFileData; ?>
	<tr><td colspan="<?php echo $colspan;?>" class="total-row"></td></tr>
    <tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="<?php echo $startColspan;?>"><?php echo $totalLabel;?> : </td>
        <?php echo $grd_chargesTDs.$grd_paymentTDs;?>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['creditProcAmount'],2); ?></td>
		<?php echo $grd_adjustmentTDs;?>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['insuranceDue'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['patientDue'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['totalBalance'],2); ?></td>
    </tr>	
    <tr><td colspan="<?php echo $colspan;?>" class="total-row"></td></tr>
    <?php echo $totalsRowCSV;?>
</table>
<table class="rpt rpt_table rpt_table-bordered" width="100%">
	<?php print $appt_data; ?>
</table>  

<?php 
}

$cico_prepay_exist=0;
if($inc_ci_co_prepay==1){
	//UNAPPLIED CI/CO AMOUNTS
	if(sizeof($arrCICONotApplied)>0){
		$tot_cico=0;
		$cico_prepay_exist=1;
		foreach($arrCICONotApplied as $firstGrpId => $firstGrpData){
			$subtot_cico=0;
			if($viewBy=='physician' || $viewBy=='operator'  || $viewBy=='groups' || $viewBy=='department'){
				$firstGrpTitle=($viewBy=='physician')? 'Physician' : 'Operator';
				$firstGrpName = $providerNameArr[$firstGrpId];
			}else{
				$firstGrpTitle='Facility';
				$firstGrpName = $arrAllFacilities[$firstGrpId];
			}
		
			$cico_data.='<tr><td class="text_b_w" colspan="4">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
			
			foreach($firstGrpData as $secGrpId => $secGrpData){

				if($viewBy=='physician' || $viewBy=='operator'  || $viewBy=='groups' || $viewBy=='department'){
					$firstGrpTitle='Facility';
					$secGrpName = $arrAllFacilities[$secGrpId];
				}else{
					$secGrpTitle='Physician';
					$secGrpName = $providerNameArr[$secGrpId];
				}

				$cico_data.='<tr><td class="text_b_w" colspan="4">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';				
				
				foreach($secGrpData as $payid => $grpDetail){	

					$pName = explode('~', $grpDetail['pat_name']);

					$patient_name = core_name_format($pName[3], $pName[1], $pName[2]);
					$patient_name.= ' - '.$pName[0];
		
					$subtot_cico+=$grpDetail['amount'];
					
					//POPUP TITLES
					$refund_title=($grpDetail['ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';			
					
					$cico_data.='
					<tr>
						<td class="text_10" style="background:#FFFFFF;">'.$patient_name.'</td>
						<td class="text_10" style="background:#FFFFFF;">'.$grpDetail['paid_date'].'</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;" '.$refund_title.'>'.$CLSReports->numberFormat($grpDetail['amount'],2).'</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;"></td>
					</tr>';
				}
				
			}
	
			$tot_cico+=$subtot_cico;
			$cico_data.='
			<tr>
				<td class="text_10b" style="background:#FFFFFF; text-align:right" colspan="2">'.$secGrpTitle.' Total:</td>
				<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($subtot_cico,2).'</td>
				<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
			</tr>';
		}
	
		$cico_html.='
		<table  style="width:100%" class="rpt_table rpt_table-bordered">
		<tr id="heading_orange"><td colspan="4">Unapplied CI/CO Payments</td></tr>
		<tr>
			<td style="text-align:center; width:300px;" class="text_b_w" width="200px">Patient Name-ID</td>
			<td style="text-align:center; width:200px" class="text_b_w" width="80px">Date</td>
			<td style="text-align:center; width:200px" class="text_b_w" width="150px">Payment</td>
			<td style="text-align:center; width:auto;" class="text_b_w" width="auto"></td>
		</tr>
		'.$cico_data.'	
		<tr>
			<td class="text_10b" style="background:#FFFFFF; text-align:right" colspan="2">CI/CO Total:</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($tot_cico,2).'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		</tr>
		</table>';
		echo $cico_html;
	}
	
	//UNAPPLIED PRE-PAYMENTS AMOUNTS
	if(sizeof($arrPrePayNotApplied)>0){
		$tot_prepay=0;
		$cico_prepay_exist=1;
		foreach($arrPrePayNotApplied as $firstGrpId => $firstGrpData){
			$subtot_prepay=0;
			if($viewBy=='physician' || $viewBy=='operator'  || $viewBy=='groups' || $viewBy=='department'){
				$firstGrpTitle=($viewBy=='physician')? 'Physician' : 'Operator';
				$firstGrpName = $providerNameArr[$firstGrpId];
			}else{
				$firstGrpTitle='Facility';
				$firstGrpName = $arrAllFacilities[$firstGrpId];
			}
		
			$prepay_data.='<tr><td class="text_b_w" colspan="4">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
			
			foreach($firstGrpData as $secGrpId => $secGrpData){
	
				if($viewBy=='physician' || $viewBy=='operator'  || $viewBy=='groups' || $viewBy=='department'){
					$firstGrpTitle='Facility';
					$secGrpName = $arrAllFacilities[$secGrpId];
				}else{
					$secGrpTitle='Physician';
					$secGrpName = $providerNameArr[$secGrpId];
				}
		
				$prepay_data.='<tr><td class="text_b_w" colspan="4">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';					
	
				foreach($secGrpData as $payid => $grpDetail){	
					$pName = explode('~', $grpDetail['pat_name']);
					$patient_name = core_name_format($pName[3], $pName[1], $pName[2]);
					$patient_name.= ' - '.$pName[0];
		
					$subtot_prepay+=$grpDetail['amount'];
					
					//POPUP TITLES
					$refund_title=($grpDetail['ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';			
					
					$prepay_data.='
					<tr>
						<td class="text_10" style="background:#FFFFFF;">'.$patient_name.'</td>
						<td class="text_10" style="background:#FFFFFF;">'.$grpDetail['entered_date'].'</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;" '.$refund_title.'>'.$CLSReports->numberFormat($grpDetail['amount'],2).'</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;"></td>
					</tr>';
				}
				
			}
	
			$tot_prepay+=$subtot_prepay;
			$prepay_data.='
			<tr>
				<td class="text_10b" style="background:#FFFFFF; text-align:right" colspan="2">'.$secGrpTitle.' Total:</td>
				<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($subtot_prepay,2).'</td>
				<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
			</tr>';
		}
	
		$prepay_html.='
		<table  style="width:100%" class="rpt_table rpt_table-bordered">
		<tr id="heading_orange"><td colspan="4">Unapplied Pre-payments</td></tr>
		<tr>
			<td style="text-align:center; width:300px;" class="text_b_w" width="200px">Patient Name-ID</td>
			<td style="text-align:center; width:200px" class="text_b_w" width="80px">Date</td>
			<td style="text-align:center; width:200px" class="text_b_w" width="150px">Payments</td>
			<td style="text-align:center; width:auto;" class="text_b_w" width="auto"></td>
		</tr>
		'.$prepay_data.'	
		<tr>
			<td class="text_10b" style="background:#FFFFFF;text-align:right" colspan="2">Pre-payment Total:</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($tot_prepay,2).'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		</tr>
		</table>';
		echo $prepay_html;
	}
}


//-----------DELETED CHARGES-------------
$del_csv = $del_csv_html = $del_pdf_html = '';
$del_csv='';
if(count($arrVoidPay)>0){
	$total_cols = 15;
	$pat_col =  "8";
	$fac_col = $fac_col1 = "10";
	$w_cols = $w_cols1 = floor((100 - ($pat_col+$fac_col))/($total_cols-2));
	$pat_col = $pat_col1 = 100 - ( (($total_cols-2) * $w_cols) + $fac_col);
	$w_cols = $w_cols."%";
	$pat_col = $pat_col."%";
	$fac_col = $fac_col."%";
	
	$fristGrpArr = array_keys($arrVoidPay);
	$arrGrandTotal = array();
	foreach($fristGrpArr as $firstID){
		$arrFirstTotal = array();
		if($viewBy=='physician'){
			$firstTitle='Physician';
			$firstGrpName = $providerNameArr[$firstID];
		}elseif($viewBy=='groups'){	
			$firstTitle='Group';
			$firstGrpName = $arrAllGroups[$firstID];
		}else{
			$firstTitle='Facility';
			$firstGrpName = $arrAllFacilities[$firstID];
		}
		$del_csv.='<tr><td class="text_b_w" align="left" colspan="'.$total_cols.'">'.$firstTitle.' : '.$firstGrpName.'</td></tr>';
		
		$secGrpArr = array_keys($arrVoidPay[$firstID]);
		$arrPaymentCntFirst = $arrPatCheckFirst = $arrCheckFirst = $arrCashPFirst = $arrEFTFirst = $arrMoneyOrderFirst = $arrVEEPFirst = $arrCCPFirst = $arrAdjFirst = '';
		foreach($secGrpArr as $secID){
			$arrSecTotal = array();
			if($viewBy=='physician'){
				$secTitle='Facility';
				$secGrpName = $arrAllFacilities[$secID];
			}else{
				$secTitle='Physician';
				$secGrpName = $providerNameArr[$secID];
			}

			$encArr = $arrVoidPay[$firstID][$secID]['detail'];
			//$del_csv.='<tr><td class="text_b_w" align="left" colspan="'.$total_cols.'">'.$secTitle.' : '.$secGrpName.'</td></tr>';
			foreach($encArr as $encID=>$arrData){
				$strCPT = '';
				$delOprArr = array_unique(array_values($arrData['opr']));
				$delOprNameArr = array();
				foreach($delOprArr as $oprId){
					$delOprNameArr[] = $userNameTwoCharArr[$oprId];
				}
				$delOprName = join(", ",array_unique($delOprNameArr));
				
				$strCPT= implode(', ', $arrData['proc_code']);

			$del_csv .= '
			<tr>
				<td class="text_12" bgcolor="#FFFFFF" align="left" style="width:'.$pat_col.'">'.$arrData['pt_name'].' - '.$arrData['pt_id'].'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="left" style="width:'.$fac_col.'">'.$secGrpName.'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="center" style="width:'.$w_cols.'">'.$arrData['dos'].'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="center" style="width:'.$w_cols.'">'.$encID.'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="left" style="width:'.$w_cols.'">'.$strCPT.'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="right" style="width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['charges'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['cash'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['pt_check'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['ins_check'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['CC'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['EFT'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['MO'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['VEEP'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;width:'.$w_cols.'">'.$CLSReports->numberFormat($arrData['adjustments'],2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:left;width:'.$w_cols.'">&nbsp;'.$delOprName.'</td>
			</tr>';
			$arrFirstTotal['charges'] += $arrData['charges'];
			$arrFirstTotal['cash'] += $arrData['cash'];
			$arrFirstTotal['pt_check'] += $arrData['pt_check'];
			$arrFirstTotal['ins_check'] += $arrData['ins_check'];
			$arrFirstTotal['CC'] += $arrData['CC'];
			$arrFirstTotal['EFT'] += $arrData['EFT'];
			$arrFirstTotal['MO'] += $arrData['MO'];	
			$arrFirstTotal['VEEP'] += $arrData['VEEP'];	
			$arrFirstTotal['adjustments'] += $arrData['adjustments'];
			}
		}
		//FIRST GROUP TOTAL		
		$del_csv .= '
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right; width:'.($pat_col1+$fac_col1+$w_cols1+$w_cols1).'%;" colspan="5" >('.$firstGrpName.') '.$firstTitle.' Total: </td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['charges'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['cash'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['pt_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['ins_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['CC'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['EFT'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['MO'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['VEEP'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['adjustments'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:left">&nbsp;</td>
			</tr>
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			';
			$del_csv .= '
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="5">Total Voided Payment: </td>
				<td bgcolor="#FFFFFF"  style="text-align:right" > </td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['cash'] + $arrFirstTotal['pt_check']+$arrFirstTotal['ins_check']+$arrFirstTotal['CC']+$arrFirstTotal['EFT']+$arrFirstTotal['MO']+$arrFirstTotal['VEEP'],2).'</td>
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="8"></td>
					
			</tr>
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			';
			$arrGrandTotal['charges'] += $arrFirstTotal['charges'];
			$arrGrandTotal['cash'] += $arrFirstTotal['cash'];
			$arrGrandTotal['pt_check'] += $arrFirstTotal['pt_check'];
			$arrGrandTotal['ins_check'] += $arrFirstTotal['ins_check'];
			$arrGrandTotal['CC'] += $arrFirstTotal['CC'];
			$arrGrandTotal['EFT'] += $arrFirstTotal['EFT'];
			$arrGrandTotal['MO'] += $arrFirstTotal['MO'];
			$arrGrandTotal['VEEP'] += $arrFirstTotal['VEEP'];
			$arrGrandTotal['adjustments'] += $arrFirstTotal['adjustments'];		

	}
	
	$arrTotDelPayment=$arrGrandTotal['cash'] + $arrGrandTotal['pt_check']+$arrGrandTotal['ins_check']+$arrGrandTotal['CC']+$arrGrandTotal['EFT']+$arrGrandTotal['MO']+$arrGrandTotal['VEEP'];
	$totalDeletedCharges=$arrGrandTotal['charges'];
	$totalDeletedPayments=$arrTotDelPayment;
	$totalDeletedAdjustments=$arrGrandTotal['adjustments'];
	
	$del_csv .= '
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="5">Grand Total: </td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['charges'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['cash'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['pt_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['ins_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['CC'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['EFT'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['MO'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['VEEP'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['adjustments'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:left">&nbsp;</td>
			</tr>
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			';

	$del_csv .= '
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="5">Total Voided Payment: </td>
				<td bgcolor="#FFFFFF"  style="text-align:right" > </td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['cash'] + $arrGrandTotal['pt_check']+$arrGrandTotal['ins_check']+$arrGrandTotal['CC']+$arrGrandTotal['EFT']+$arrGrandTotal['MO']+$arrGrandTotal['VEEP'],2).'</td>
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="8"></td>
					
			</tr>
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			';
	
		$del_paid_csv .='
		<table style="width:100%" class="rpt_table rpt_table-bordered">
			<tr id="heading_orange"><td colspan="'.$total_cols.'">Voided Records</td></tr>
			<tr>
				<td align="center" class="text_b_w" style="width:'.$pat_col.'">Patient Name-ID</td>
				<td align="center" class="text_b_w" style="width:'.$fac_col.'">Facility</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">DOS</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">E.ID</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">CPT</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">Charges</td>
				<td width="400" class="text_b_w" colspan="7" style="text-align:center; width:'.(7*$w_cols1).'%">Payments</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">Write-off & Adj</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">Del Opr</td>
			</tr>
			<tr>
				<td align="center" class="text_b_w" colspan="6" style="width:'.($pat_col1+$fac_col1+($w_cols1*4)).'%"></td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">Cash</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">Pt Check</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">Ins Check</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">CC</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">EFT</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">MO</td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'">VEEP</td>					
				<td align="center" class="text_b_w" style="width:'.$w_cols.'"></td>
				<td align="center" class="text_b_w" style="width:'.$w_cols.'"></td>
			</tr>
			'.
			$del_csv.'
		</table>';	
	
	$del_paid_pdf .= '.
	<page backtop="9mm" backbottom="5mm">
	<page_footer>
	<table style="width: 100%;">
		<tr>
			<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
		</tr>
	</table>
	</page_footer>'.
	$del_paid_csv.
'</page>';

echo $del_paid_csv;
}


//GRAND TOTAL IF NORMAL AND DELETED AVAILABLE
if($normal_available==1 && (empty($del_paid_csv)==false || $cico_prepay_exist==1)){
	$totalChargesAmt+=$otherCharges;
	$totalPaymentAmt+=$otherPayments;
	$totalAdjustmentAmt+=$otherWriteOff+$otherAdj;
	
	$grandCharges=$totalChargesAmt-$totalDeletedCharges;
	$grandPayments=($totalPaymentAmt+$tot_cico+$tot_prepay)-$totalDeletedPayments;
	$grandAdjustments=$totalAdjustmentAmt-$totalDeletedAdjustments;
	
	$grand_totals_csv= '
	<table class="rpt_table rpt rpt_table-bordered">
	<tr id="heading_orange"><td colspan="5">Grand Totals</td></tr>
	<tr>
		<td class="text_b_w" style="width:15%"></td>
		<td class="text_b_w" style="width:15%; text-align:center">Charges</td>
		<td class="text_b_w" style="width:15%; text-align:center">Payments</td>
		<td class="text_b_w" style="width:15%; text-align:center">Write-off & Adj</td>
		<td class="text_b_w" style="width:auto"></td>
	</tr>
	<tr class="text_12b">
		<td bgcolor="#FFFFFF" style="text-align:right">Total : </td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalChargesAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalPaymentAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalAdjustmentAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right"></td>
	</tr>';
	if($tot_cico>0){
		$grand_totals_csv.='
		<tr class="text_12b">
			<td bgcolor="#FFFFFF" style="text-align:right">Unapplied CI/CO : </td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tot_cico,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
		</tr>';
	}
	if($tot_prepay>0){
		$grand_totals_csv.='
		<tr class="text_12b">
			<td bgcolor="#FFFFFF" style="text-align:right">Unapplied Pre-payments : </td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tot_prepay,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
		</tr>';
	}
	$grand_totals_csv.='
	<tr class="text_12b">
		<td bgcolor="#FFFFFF" style="text-align:right">Total Deleted : </td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedCharges,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedPayments,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedAdjustments,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right"></td>
	</tr>
	<tr><td colspan="5" class="total-row"></td></tr>
	<tr class="text_12b">
		<td bgcolor="#FFFFFF"  style="text-align:right">Grand Total : </td>
		<td bgcolor="#FFFFFF"  style="text-align:right">'.$CLSReports->numberFormat($grandCharges,2,1).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandPayments,2,1).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandAdjustments,2,1).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right"></td>
	</tr>
	<tr><td colspan="5" class="total-row"></td></tr>
	</table>';	

	$grand_totals_pdf='
	<table width="1050" class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr id="heading_orange">
		<td colspan="4">Grand Totals</td>
	</tr>
	<tr>
		<td class="text_b_w" width="262"></td>
		<td class="text_b_w" width="262" style=" text-align:center">Charges</td>
		<td class="text_b_w" width="262" style="text-align:center">Payments</td>
		<td class="text_b_w" width="262" style="text-align:center">Write-off & Adj</td>
	</tr>
	<tr class="text_12b">
		<td bgcolor="#FFFFFF" style="text-align:right">Total : </td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalChargesAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalPaymentAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalAdjustmentAmt,2).'</td>
	</tr>';
	if($tot_cico>0){
		$grand_totals_pdf.='
		<tr class="text_12b">
			<td bgcolor="#FFFFFF" style="text-align:right">Unapplied CI/CO : </td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tot_cico,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
		</tr>';
	}
	if($tot_prepay>0){
		$grand_totals_pdf.='
		<tr class="text_12b">
			<td bgcolor="#FFFFFF" style="text-align:right">Unapplied Pre-payments : </td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tot_prepay,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
		</tr>';
	}
	$grand_totals_pdf.='	
	<tr class="text_12b">
		<td bgcolor="#FFFFFF" style="text-align:right">Total Deleted : </td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedCharges,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedPayments,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedAdjustments,2).'</td>
	</tr>
	<tr><td colspan="4" class="total-row"></td></tr>
	<tr class="text_12b">
		<td bgcolor="#FFFFFF"  style="text-align:right">Grand Total : </td>
		<td bgcolor="#FFFFFF"  style="text-align:right">'.$CLSReports->numberFormat($grandCharges,2,1).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandPayments,2,1).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandAdjustments,2,1).'</td>
	</tr>
	<tr><td colspan="4" class="total-row"></td></tr>
	</table>';	
		
	echo $grand_totals_csv;
}

$csv_file_data = ob_get_contents();
ob_end_clean();
//--- PDF FILE DATA ---
ob_start();

$lblHomeFacTD=$totBlankTD='';
$lblEncTD='<td style="text-align:center;" width="50" class="text_b_w">Enc. #</td>';
if($registered_fac=='1'){
	//$lblEncTD='';
	//$lblHomeFacTD='<td style="text-align:center;" width="55" class="text_b_w">Home Fac</td>';
	//$totBlankTD='<td style="text-align:center;" class="text_10b"></td>';
}
$chargesTDsPdf=$paymentTDsPdf=$adjustmentTDsPdf='';
$grd_chargesTDs=$grd_paymentTDs=$grd_adjustmentTDs='';
if($inc_summary_charges==1){
	$chargesTDsPdf='
	<td class="text_b_w" style="text-align:center; width:'.$col_width.'">Charges</td>';

	$grd_chargesTDs='
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr['totalAmt'],2).'</td>';
}
if($inc_payments==1){
	$paymentTDsPdf='
	<td class="notInsGroupBy text_b_w" style="text-align:center; width:'.$col_width.'">Pat. Paid</td>
	<td class="notInsGroupBy text_b_w" style="text-align:center; width:'.$col_width.'">Pri. Paid</td>
	<td class="notInsGroupBy text_b_w" style="text-align:center; width:'.$col_width.'">Sec. Paid</td>
	<td style="text-align:center; width:'.$col_width.'" class="text_b_w">Tot. Paid</td>';

	$grd_paymentTDs='
	<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr['patPaidAmt'],2).'</td>
	<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr['priPaidAmt'],2).'</td>
	<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr['secPaidAmt'],2).'</td>
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2).'</td>';
}
if($inc_adjustments==1){
	$adjustmentTDsPdf='
	<td style="text-align:center; width:'.$col_width.'" class="text_b_w">Write-Off</td>
	<td style="text-align:center; width:'.$col_width.'" class="text_b_w">Adjustment</td>';

	$grd_adjustmentTDs='
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr['write_off_amt'],2).'</td>
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'</td>';
}	
?>
<page backtop="20mm" backbottom="5mm">
<page_footer>
    <table style="width: 100%;">
        <tr>
            <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
        </tr>
    </table>
</page_footer>
<page_header>	
    <table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
		<tr class="rpt_headers">	
			<td align="left" class="rptbx1" width="350"><?php print "$dbtemp_name"; ?> (Detail)</td>	
			<td align="left" class="rptbx2" width="350"><?php print "$dayReport ($search) From : $Sdate To : $Edate".""; ?></td>
			<td align="left" class="rptbx3" width="350"><?php print "Created by $opInitial on $curDate"; ?></td>
		</tr>
		<tr class="rpt_headers">
			<td align="left" class="rptbx1" width="350">Group : <?php echo $selgroup; ?></td>
			<td align="left" class="rptbx2" width="350">Facility : <?php  echo $selFac; ?></td>
			<td align="left" class="rptbx3" width="350">Filing Phy. : <?php echo $selPhy; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Oper.: <?php echo $selOpr; ?>
			</td>
        </tr>
		<tr class="rpt_headers">
			<td align="left" class="rptbx1" width="350">Credit Phy. : <?php echo $selCrPhy; ?></td>
			<td align="left" class="rptbx2" width="350">Insurance : <?php  echo $selInsurance; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Ins. Type : <?php  echo $selInsType; ?>
			</td>
			<td align="left" class="rptbx3" width="350">CPT : <?php echo $selCPT; ?></td>
        </tr>
        <tr class="rpt_headers">
			<td align="left" class="rptbx1" width="350">ICD9: <?php echo $selDX; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			ICD10 : <?php echo $selDX10; ?>
			</td>
			<td align="left" class="rptbx2" width="350">Modifiers : <?php  echo $selModifiers; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Adj. Code : <?php  echo $selAdjCode; ?>
			</td>
			<td align="left" class="rptbx3" width="350">Write-off Code : <?php echo $selWriteoff; ?></td>
        </tr>
    </table>
</page_header>
<table width="100%" class="rpt_table rpt rpt_table-bordered">
    <tr>
        <td style="text-align:center; width:110px" class="text_b_w nowrap">Patient Name</td>
			<td style="text-align:center; width:<?php echo $col_width; ?>" class="text_b_w">DOS</td>
			<td style="text-align:center; width:<?php echo $col_width; ?>" class="text_b_w">DOT</td>
			<td style="text-align:center; width:<?php echo $col_width; ?>" class="text_b_w">Enc. #</td>
			<td style="text-align:center; width:<?php echo $col_width; ?>" class="text_b_w">CPT</td>    
			<?php echo $chargesTDsPdf.$paymentTDsPdf;?>
			<td style="text-align:center; width:<?php echo $col_width; ?>" class="text_b_w">Credit</td>
			<?php echo $adjustmentTDsPdf;?>        
			<td style="text-align:center; width:<?php echo $col_width; ?>" class="text_b_w">Ins. Due</td>
			<td style="text-align:center; width:<?php echo $col_width; ?>" class="text_b_w">Pat. Due</td>
			<td style="text-align:center; width:<?php echo $col_width; ?>" class="text_b_w">Balance</td>
    </tr>
    <?php print $csvFileDataPrint; ?>
	<tr><td colspan="<?php echo $colspan;?>" class="total-row"></td></tr>
    <tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="<?php echo $startColspan;?>"><?php echo $totalLabel;?> : </td>
        <?php echo $grd_chargesTDs.$grd_paymentTDs;?>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['creditProcAmount'],2); ?></td>
		<?php echo $grd_adjustmentTDs;?>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['insuranceDue'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['patientDue'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['totalBalance'],2); ?></td>
    </tr>	
    <tr><td colspan="<?php echo $colspan;?>" class="total-row"></td></tr>
    <?php echo $totalsRowCSV;?>
</table>
<?php 
echo $cico_html;
echo $prepay_html;
echo $del_pdf_html; 
echo $del_paid_csv;
echo $grand_totals_pdf;
?>

</page>
<?php
$pdf_page_content = ob_get_contents();
ob_end_clean();

//IN GRROUP BY INSURANCE CASE - REMOVING COLUMN THAT ARE NOT IN USE FOR THIS GROUPING
$np_page_content_pdf = preg_replace('/<td class="notInPdf(.+)<\/td>/', '', $not_posted_content);

//IN GRROUP BY INSURANCE CASE - REMOVING COLUMN THAT ARE NOT IN USE FOR THIS GROUPING
if($viewBy=='insurance'){
	$pdf_page_content = preg_replace('/<td class="notInsGroupBy(.+)<\/td>/', '', $pdf_page_content);
}
?>
