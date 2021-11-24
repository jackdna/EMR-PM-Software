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

$colspan=22;
$colspanPdf=17;
$startColspan=11;
$startColspanPDF=5;

if($viewBy=='insurance'){
	$colspan=14;
	$colspanPdf=13;
	$startColspan=6;
}


$tdTotal='';
if($home_facility=='1'){
	$colspan=20;
	$tdTotal = '<td width="auto" class="text_10" style="text-align:left; background:#FFFFFF;"></td>';
	$startColspan='6';
	$startColspanPDF='4';

	if($viewBy=='insurance'){
		$colspan=15;
	}	
}


if($revenue_by_dept=='1'){
	foreach($mainResArr as $firstGrpId => $firstGrpData){	
		$arrFirstGrpTotal = array();
		$printFile=true;
		
		if($viewBy=='physician' || $viewBy=='operator'){
			$firstGrpTitle=($viewBy=='physician')? 'Filing Physician' : 'Operator';
			$firstGrpName = $providerNameArr[$firstGrpId];
			$firstGrpTitle1 = 'Physician';
		}elseif($viewBy=='procedure'){
			$firstGrpTitle='Procedure';
			$firstGrpName = $arrAllCPTCodes[$firstGrpId];
			$firstGrpTitle1 = 'Facility';
		}else{
			$firstGrpTitle='Facility';
			$firstGrpTitle1 = 'Facility';
			$firstGrpName = $arrAllFacilities[$firstGrpId];
		}
		$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		$csvFileDataPrint .='<tr><td class="text_b_w" colspan="15">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
	
		foreach($firstGrpData as $secGrpId => $secGrpData){
			$subTotalAmtArr = array();
			$arrSecGrpTotal = array();
	
			if($viewBy=='physician' || $viewBy=='operator'){
				$secGrpTitle='Facility';
				$secGrpTitle1='Facility';
				$secGrpName = $arrAllFacilities[$secGrpId];
			}else{
				$secGrpTitle='Filing Physician';
				$secGrpTitle1='Physician';
				$secGrpName = $providerNameArr[$secGrpId];
			}
			$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
			$csvFileDataPrint .='<tr><td class="text_b_w" colspan="15">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
	
			
			foreach($secGrpData as $thirdGrpId => $encData){
				
				//IF "Revenue By Dept" SELECTED
				if($revenue_by_dept=='1'){
					$subTotalAmtArr = array();
					$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">Department : '.$arrDeptNames[$thirdGrpId].'</td></tr>';
					$csvFileDataPrint .='<tr><td class="text_b_w" colspan="15">Department : '.$arrDeptNames[$thirdGrpId].'</td></tr>';
				}
			
				foreach($encData as $enc_id => $encDataArr){
					$submitted = $encDataArr[0]["submitted"];
					$first_posted_date = $encDataArr[0]["first_posted_date"];
					$patient_name = core_name_format($encDataArr[0]['lname'], $encDataArr[0]['fname'], $encDataArr[0]['mname']);
					
					$patient_id = $encDataArr[0]['patient_id'];
					$date_of_service = $encDataArr[0]['date_of_service'];
					$default_facility = $encDataArr[0]['default_facility'];
					$primaryInsName  = $arrAllInsCompanies[$encDataArr[0]['primaryInsuranceCoId']];
					$secondaryInsName = $arrAllInsCompanies[$encDataArr[0]['secondaryInsuranceCoId']];
					
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
						
						//CREDIT/DEBIT
						$patCrdDbt= array_sum($pay_crd_deb_arr[$chgDetId]['Patient']);			
						$insCrdDbt= array_sum($pay_crd_deb_arr[$chgDetId]['Insurance']);
						
						//CREDIT - OVER PAYMENT
						$creditProcAmount+= $encDataArr[$d]['overPaymentForProc'];
						
						//WRITE-OFF & ADJUSTMENTS
						$write_off_amt+= $normalWriteOffAmt[$chgDetId] + array_sum($writte_off_arr[$chgDetId]);
						//if($DateRangeFor!='date_of_service'){
						$adj_amt+= $arrAdjustmentAmt[$chgDetId];				
						//}
		
						//BALANCE
						if($encDataArr[$d]["newBalance"]>0){
							$totalBalance+= $encDataArr[$d]['newBalance'];
						}else{
							if($encDataArr[$d]['overPaymentForProc']>0){
								$totalBalance-= $encDataArr[$d]['overPaymentForProc'];
							}else{
								$totalBalance+= $encDataArr[$d]['newBalance'];
							}
						}				
	
						// TOTAL PAYMENT
						$patientPaidAmt+= array_sum($mainEncounterPayArr[$chgDetId]) + ($patCrdDbt + $insCrdDbt);			
						// PATIENT PAID
						$patPaidAmt+= array_sum($patPayDetArr[$chgDetId]['patPaid']);
						// INSURANCE PAID
						$insPaidAmt+= array_sum($patPayDetArr[$chgDetId]['insPaid']) + $insCrdDbt;
						// PRI INSURANCE PAID
						if($patPayDetArr[$chgDetId]['priPaid']>0){
							$priPaidAmt+= array_sum($patPayDetArr[$chgDetId]['priPaid']) + $insCrdDbt;
							$insCrdDbt=0;
						}
						// SEC+TER INSURANCE PAID
						$secPaidAmt+= array_sum($patPayDetArr[$chgDetId]['secPaid']) + array_sum($patPayDetArr[$chgDetId]['terPaid']) + $insCrdDbt;
	
						//LAST PAID DOT
						if($patPayDetArr[$chgDetId]['lastDOT']!=null && $patPayDetArr[$chgDetId]['lastDOT']!='' && $patPayDetArr[$chgDetId]['lastDOT']!='00-00-0000'){
							$lastPaidDOT= $patPayDetArr[$chgDetId]['lastDOT'];
						}
	
						$cptCode = $encDataArr[$d]['cpt_prac_code'];
						$cpt4Code = $encDataArr[$d]['cpt4_code'];
						$arrCPT_CSV[$cptCode]=$cptCode;
						if(strlen($cptCode)>7){ $cptCode = substr($cptCode, 0, 7).'..';}
						$arrCPT[] = $cptCode;
						if($encDataArr[$d]['diagnosis_id1']!=''){ $arrDxCodes[$encDataArr[$d]['diagnosis_id1']]=$encDataArr[$d]['diagnosis_id1'];}
						if($encDataArr[$d]['diagnosis_id2']!=''){ $arrDxCodes[$encDataArr[$d]['diagnosis_id2']]=$encDataArr[$d]['diagnosis_id2'];}
						if($encDataArr[$d]['diagnosis_id3']!=''){ $arrDxCodes[$encDataArr[$d]['diagnosis_id3']]=$encDataArr[$d]['diagnosis_id3'];}
						if($encDataArr[$d]['diagnosis_id4']!=''){ $arrDxCodes[$encDataArr[$d]['diagnosis_id4']]=$encDataArr[$d]['diagnosis_id4'];}
					//}
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
					
					$startColspanPDF='5';
					$tdPart=$tdPartPDF=$encTDPDF=$subTotBlankTD='';	
					$encTDPDF='<td width="60" class="text_10" style="background:#FFFFFF;">'.$enc_id.'</td>';
					if($home_facility=='1'){
						$tdPart='<td class="text_10" style="background:#FFFFFF;">'.$arrAllFacilities[$default_facility].'</td>';
						$tdPartPDF='<td width="60" class="text_10" style="background:#FFFFFF;">'.$arrAllFacilities[$default_facility].'</td>';
						$subTotBlankTD='<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>';
						$encTDPDF='';
						$startColspanPDF='4';
					}
					
					$csvFileData .= <<<DATA
					<tr>
						<td class="text_10" style="background:#FFFFFF;">$patient_name</td>
						<td class="text_10" style="background:#FFFFFF;">$patient_id</td>
						<td class="text_10" style="background:#FFFFFF;">$firstGrpName</td>
						<td class="text_10" style="background:#FFFFFF;">$secGrpName</td>
						<td class="text_10" style="background:#FFFFFF;">$date_of_service</td>
						<td class="text_10" style="background:#FFFFFF;">$lastPaidDOT</td>
						<td class="text_10" style="background:#FFFFFF;">$enc_id</td>
						<td class="text_10" style="background:#FFFFFF;">$strCPT_CSV</td>
						<td class="text_10" style="background:#FFFFFF;">$strDxCodes</td>
						<td class="text_10" style="background:#FFFFFF;">$primaryInsName</td>
						<td class="text_10" style="background:#FFFFFF;">$secondaryInsName</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$totalAmt</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$patPaidAmt</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$priPaidAmt</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$secPaidAmt</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$patientPaidAmt</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$creditProcAmount</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$write_off_amt</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$adj_amt</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$insuranceDue</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$patientDue</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">$totalBalance</td>
						$tdPart
					</tr>
DATA;
	
					$csvFileDataPrint .= <<<DATA
					<tr>
						<td width="70" class="text_10" style="background:#FFFFFF;">$patient_name - $patient_id</td>
						<td width="40" class="text_10" style="background:#FFFFFF;">$date_of_service</td>
						$encTDPDF
						<td width="50" class="text_10" style="background:#FFFFFF;">$strCPT</td>
						<td width="50" class="text_10" style="background:#FFFFFF;">$strDxCodes</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$totalAmt</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$patPaidAmt</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$insPaidAmt</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$patientPaidAmt</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$creditProcAmount</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$write_off_amt</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$adj_amt</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$insuranceDue</td>
						<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$patientDue</td>
						<td width="70" class="text_10" style="text-align:right; background:#FFFFFF;">$totalBalance</td>
						$tdPartPDF
					</tr>
DATA;
				}
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
	
				//IF "Revenue By Dept" SELECTED
				if($revenue_by_dept=='1'){
								
					//SECOND GRAND TOTAL AMOUNT
					$arrSecGrpTotal["totalAmt"]+= $sub_total_amt;
					$arrSecGrpTotal["pat_paid_amt"]+= $sub_pat_paid_amt;
					$arrSecGrpTotal["insuranceDue"]+= $sub_ins_due;
					$arrSecGrpTotal["patientDue"]+= $sub_patient_due;
					$arrSecGrpTotal["creditProcAmount"]+= $sub_credit_amt;
					$arrSecGrpTotal["write_off_amt"]+= $sub_write_off_amt;
					$arrSecGrpTotal["adj_amt"]+= $sub_adj_amt;
					$arrSecGrpTotal["totalBalance"]+= $sub_total_balance;			
					$arrSecGrpTotal["patPaidAmt"]+= $sub_patPaidAmt;
					$arrSecGrpTotal["insPaidAmt"]+= $sub_insPaidAmt;
					$arrSecGrpTotal["priPaidAmt"]+= $sub_priPaidAmt;
					$arrSecGrpTotal["secPaidAmt"]+= $sub_secPaidAmt;
	
					//--- CHANGE NUMBER FORMAT FOR SUB TOTAL AMOUNT ---
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
	
					$csvFileData .= '
					<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
					<tr>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="7">Department Total : </td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_total_amt.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_patPaidAmt.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_priPaidAmt.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_secPaidAmt.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_pat_paid_amt.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_credit_amt.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_write_off_amt.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_adj_amt.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_ins_due.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_patient_due.'</td>
						<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_total_balance.'</td>
						'.$tdTotal.'
					</tr>	
					<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';			
					
					$csvFileDataPrint .='
					<tr><td colspan="15" class="total-row"></td></tr>
					<tr>
						<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspanPDF.'">Department Total : </td>
						<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_total_amt.'</td>
						<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_patPaidAmt.'</td>
						<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_insPaidAmt.'</td>
						<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_pat_paid_amt.'</td>
						<td width="65" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_credit_amt.'</td>
						<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_write_off_amt.'</td>
						<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_adj_amt.'</td>
						<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_ins_due.'</td>
						<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_patient_due.'</td>
						<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sub_total_balance.'</td>
						'.$subTotBlankTD.'
					</tr>	
					<tr><td colspan="15" class="total-row"></td></tr>';
				}
			}//DEPT IF END
	
	
	
			if($revenue_by_dept=='1'){
				// SECOND GROUP TOTAL
				$sub_total_amt = $arrSecGrpTotal['totalAmt'];
				$sub_pat_paid_amt = $arrSecGrpTotal['pat_paid_amt'];
				$sub_ins_due = $arrSecGrpTotal['insuranceDue'];
				$sub_patient_due = $arrSecGrpTotal['patientDue'];
				$sub_credit_amt = $arrSecGrpTotal['creditProcAmount'];
				$sub_write_off_amt = $arrSecGrpTotal['write_off_amt'];
				$sub_adj_amt = $arrSecGrpTotal['adj_amt'];
				$sub_total_balance = $arrSecGrpTotal['totalBalance'];
				$sub_patPaidAmt = $arrSecGrpTotal['patPaidAmt'];
				$sub_insPaidAmt = $arrSecGrpTotal['insPaidAmt'];			
				$sub_priPaidAmt = $arrSecGrpTotal['priPaidAmt'];
				$sub_secPaidAmt = $arrSecGrpTotal['secPaidAmt'];
			}
	
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
							
			$csvFileData .= <<<DATA
			<tr><td colspan="$colspan" class="total-row"></td></tr>
			<tr>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="8">$secGrpTitle Total : </td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_patPaidAmt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_priPaidAmt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_secPaidAmt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_pat_paid_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_credit_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_write_off_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_adj_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_ins_due</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_patient_due</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_balance</td>
				$tdTotal
			</tr>	
			<tr><td colspan="$colspan" class="total-row"></td></tr>
DATA;
	
			$csvFileDataPrint .= <<<DATA
			<tr><td colspan="15" class="total-row"></td></tr>
			<tr>
				<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="$startColspanPDF">$secGrpTitle Total : </td>
				<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_amt</td>
				<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_patPaidAmt</td>
				<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_insPaidAmt</td>
				<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_pat_paid_amt</td>
				<td width="65" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_credit_amt</td>
				<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_write_off_amt</td>
				<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_adj_amt</td>
				<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_ins_due</td>
				<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_patient_due</td>
				<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_balance</td>
				$subTotBlankTD
			</tr>	
			<tr><td colspan="15" class="total-row"></td></tr>
DATA;
		} // END SECOND GROUP
		
		
		// FIRST GROUP TOTAL
		$grandTotalAmtArr["totalAmt"]+= $arrFirstGrpTotal["totalAmt"];
		$grandTotalAmtArr["patPaidAmt"]+= $arrFirstGrpTotal["patPaidAmt"];
		$grandTotalAmtArr["insPaidAmt"]+= $arrFirstGrpTotal["insPaidAmt"];
		$grandTotalAmtArr["priPaidAmt"]+= $arrFirstGrpTotal["priPaidAmt"];
		$grandTotalAmtArr["secPaidAmt"]+= $arrFirstGrpTotal["secPaidAmt"];
		$grandTotalAmtArr["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
		$grandTotalAmtArr["creditProcAmount"]+= $arrFirstGrpTotal["creditProcAmount"];
		$grandTotalAmtArr["write_off_amt"]+= $arrFirstGrpTotal["write_off_amt"];
		$grandTotalAmtArr["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];
		$grandTotalAmtArr["insuranceDue"]+= $arrFirstGrpTotal["insuranceDue"];
		$grandTotalAmtArr["patientDue"]+= $arrFirstGrpTotal["patientDue"];
		$grandTotalAmtArr["totalBalance"]+= $arrFirstGrpTotal["totalBalance"];	
		
		$csvFileData .='
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="8">'.$firstGrpTitle.' Total : </td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["patPaidAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["priPaidAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["secPaidAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["creditProcAmount"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["insuranceDue"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["patientDue"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
			'.$tdTotal.'
		</tr>	
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
	
	
		$csvFileDataPrint .='
		<tr>
			<td colspan="15" class="total-row"></td>
		</tr>
		<tr>
			<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspanPDF.'">'.$firstGrpTitle.' Total : </td>
			<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["patPaidAmt"],2).'</td>
			<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["insPaidAmt"],2).'</td>
			<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2).'</td>
			<td width="65" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["creditProcAmount"],2).'</td>
			<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["insuranceDue"],2).'</td>
			<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["patientDue"],2).'</td>
			<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
			'.$subTotBlankTD.'
		</tr>	
		<tr>
			<td colspan="15" class="total-row"></td>
		</tr>';
	}
}else{
	
	 //WITHOUT GROUP BY DEPARTMENT
	foreach($mainResArr as $firstGrpId => $firstGrpData){	
		$arrFirstGrpTotal = array();
		$printFile=true;
		
		if($viewBy=='physician' || $viewBy=='operator'){
			$firstGrpTitle=($viewBy=='physician')? 'Filing Physician' : 'Operator';
			$firstGrpName = $providerNameArr[$firstGrpId];
			$firstGrpTitle1 = 'Physician';
		}else if($viewBy=='insurance'){
			$firstGrpTitle='Insurance';
			$firstGrpName = $arrAllInsCompanies[$firstGrpId];
		}else if($viewBy=='procedure'){
			$firstGrpTitle='Procedure';
			$firstGrpTitle1= 'Facility';
			$firstGrpName = $arrAllCPTCodes[$firstGrpId];
		}else{
			$firstGrpTitle='Facility';
			$firstGrpTitle1='Facility';
			$firstGrpName = $arrAllFacilities[$firstGrpId];
		}

		$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		$csvFileDataPrint .='<tr><td class="text_b_w" colspan="'.$colspanPdf.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
	
		foreach($firstGrpData as $secGrpId => $secGrpData){
			$subTotalAmtArr = array();
			$arrSecGrpTotal = array();
	
			if($viewBy=='physician' || $viewBy=='operator'){
				$secGrpTitle='Facility';
				$secGrpTitle1='Facility';
				$secGrpName = $arrAllFacilities[$secGrpId];
			}else{
				$secGrpTitle='Filing Physician';
				$secGrpTitle1='Physician';
				$secGrpName = $providerNameArr[$secGrpId];
			}
			$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
			$csvFileDataPrint .='<tr><td class="text_b_w" colspan="'.$colspanPdf.'">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
	
			foreach($secGrpData as $chgDetId => $encDataArr){
				$enc_id = $encDataArr[0]['encounter_id'];
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
					//$chgDetId = $encDataArr[$d]['charge_list_detail_id'];
	
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
//echo $mainEncounterPayArr[$chgDetId].'<br>';				
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
						$patPaidAmt+=$patPayDetArr[$chgDetId]['patPaid'];
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
					$cpt4Code = $encDataArr[$d]['cpt4_code'];
				/* 	$arrCPT_CSV[$cptCode]=$cptCode;
					if(strlen($cptCode)>7){ $cptCode = substr($cptCode, 0, 7).'..';}
					$arrCPT[] = $cptCode; */
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
				//}
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
				if($home_facility=='1'){
					$tdPart='<td class="text_10" style="background:#FFFFFF;">'.$arrAllFacilities[$default_facility].'</td>';
					$tdPartPDF='<td width="60" class="text_10" style="background:#FFFFFF;">'.$arrAllFacilities[$default_facility].'</td>';
					$subTotBlankTD='<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b"></td>';
					$encTDPDF='';
				}
				$tmpFacilityName = '';	
				$tmpFacilityName = ($viewBy=='procedure' ? $arrAllFacilities[$encDataArr[0]['pos_facility_id']] : $firstGrpName);
				$csvFileData .= <<<DATA
				<tr>
					<td class="text_10" style="background:#FFFFFF;">$patient_name</td>
					<td class="text_10" style="background:#FFFFFF;">$patient_id</td>
					<td class="text_10" style="background:#FFFFFF;">$tmpFacilityName</td>
					<td class="text_10" style="background:#FFFFFF;">$secGrpName</td>
					<td class="text_10" style="background:#FFFFFF;">$date_of_service</td>
					<td class="text_10" style="background:#FFFFFF;">$lastPaidDOT</td>
					<td class="text_10" style="background:#FFFFFF;">$enc_id</td>
					<td class="text_10" style="background:#FFFFFF;">$cptCode ($cpt4Code)</td>
					<td class="text_10" style="background:#FFFFFF;">$strDxCodes</td>
					<td class="notInsGroupBy text_10" style="background:#FFFFFF;">$primaryInsName</td>
					<td class="notInsGroupBy text_10" style="background:#FFFFFF;">$secondaryInsName</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;">$totalAmt</td>
					<td class="notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">$patPaidAmt</td>
					<td class="notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">$priPaidAmt</td>
					<td class="notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">$secPaidAmt</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;">$patientPaidAmt</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;">$creditProcAmount</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;">$write_off_amt</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;">$adj_amt</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;">$insuranceDue</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;">$patientDue</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;">$totalBalance</td>
					$tdPart
				</tr>
DATA;

				$csvFileDataPrint .= <<<DATA
				<tr>
					<td width="70" class="text_10" style="background:#FFFFFF;">$patient_name - $patient_id</td>
					<td width="40" class="text_10" style="background:#FFFFFF;">$date_of_service</td>
					$encTDPDF
					<td width="50" class="text_10" style="background:#FFFFFF;">$cptCode</td>
					<td width="50" class="text_10" style="background:#FFFFFF;">$strDxCodes</td>
					<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$totalAmt</td>
					<td class="notInsGroupBy text_10" width="65" style="text-align:right; background:#FFFFFF;">$patPaidAmt</td>
					<td class="notInsGroupBy text_10" width="65" style="text-align:right; background:#FFFFFF;">$insPaidAmt</td>
					<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$patientPaidAmt</td>
					<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$creditProcAmount</td>
					<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$write_off_amt</td>
					<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$adj_amt</td>
					<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$insuranceDue</td>
					<td width="65" class="text_10" style="text-align:right; background:#FFFFFF;">$patientDue</td>
					<td width="70" class="text_10" style="text-align:right; background:#FFFFFF;">$totalBalance</td>
					$tdPartPDF
				</tr>
DATA;
			}
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
							
			$csvFileData .= <<<DATA
			<tr><td colspan="$colspan" class="total-row"></td></tr>
			<tr>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="$startColspan">$secGrpTitle Total : </td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_amt</td>
				<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">$sub_patPaidAmt</td>
				<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">$sub_priPaidAmt</td>
				<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">$sub_secPaidAmt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_pat_paid_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_credit_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_write_off_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_adj_amt</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_ins_due</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_patient_due</td>
				<td style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_balance</td>
				$tdTotal
			</tr>	
			<tr><td colspan="$colspan" class="total-row"></td></tr>
DATA;
	
			$csvFileDataPrint .= <<<DATA
			<tr><td colspan="$colspanPdf" class="total-row"></td></tr>
			<tr>
				<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="$startColspanPDF">$secGrpTitle Total : </td>
				<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_amt</td>
				<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;">$sub_patPaidAmt</td>
				<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;">$sub_insPaidAmt</td>
				<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_pat_paid_amt</td>
				<td width="65" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_credit_amt</td>
				<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_write_off_amt</td>
				<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_adj_amt</td>
				<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_ins_due</td>
				<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_patient_due</td>
				<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">$sub_total_balance</td>
				$subTotBlankTD
			</tr>	
			<tr><td colspan="$colspanPdf" class="total-row"></td></tr>
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
		
		$csvFileData .='
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">'.$firstGrpTitle.' Total : </td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($arrFirstGrpTotal["patPaidAmt"],2).'</td>
			<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($arrFirstGrpTotal["priPaidAmt"],2).'</td>
			<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($arrFirstGrpTotal["secPaidAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["creditProcAmount"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["insuranceDue"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["patientDue"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
			'.$tdTotal.'
		</tr>	
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
	
	
		$csvFileDataPrint .='
		<tr>
			<td colspan="'.$colspanPdf.'" class="total-row"></td>
		</tr>
		<tr>
			<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspanPDF.'">'.$firstGrpTitle.' Total : </td>
			<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($arrFirstGrpTotal["patPaidAmt"],2).'</td>
			<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($arrFirstGrpTotal["insPaidAmt"],2).'</td>
			<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2).'</td>
			<td width="65" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["creditProcAmount"],2).'</td>
			<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["insuranceDue"],2).'</td>
			<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["patientDue"],2).'</td>
			<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
			'.$subTotBlankTD.'
		</tr>	
		<tr>
			<td colspan="'.$colspanPdf.'" class="total-row"></td>
		</tr>';
	}		
}

//-- OPERATOR INITIAL -------
$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$opInitial = $authProviderNameArr[1][0];
$opInitial .= $authProviderNameArr[0][0];
$opInitial = strtoupper($opInitial);
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
	
	$notPostedCSV='
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
  <tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">Not Posted Amount : </td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($chargesForNotPosted,2).'</td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
        <td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
        <td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
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
if($viewBy=='insurance'){
	$showTotalAgain=1;
	$totalLabel='Sub Total';
	
	$otherPayments= $arrOtherGrandTotal['payments'];
	$otherWriteOff= $arrOtherGrandTotal['write-off'];
	$otherAdj= $arrOtherGrandTotal['adj'];
	$otherCharges= array_sum($arrOtherGrandTotal['charges']);
	$otherInsDue= array_sum($arrOtherGrandTotal['ins_due']);
	$otherPatDue= array_sum($arrOtherGrandTotal['pat_due']);
	$otherOverPayment= array_sum($arrOtherGrandTotal['over_payment']);
	$otherBalance= array_sum($arrOtherGrandTotal['balance']);

	$otherTotals='
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
    <tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">Other Totals : </td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherCharges,2).'</td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
        <td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($otherPayments,2).'</td>
        <td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($otherOverPayment,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherWriteOff,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherAdj,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otheriInsDue,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherPatDue,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherBalance,2).'</td>
        '.$tdTotal.'
    </tr>	
    <tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';	

	$otherTotalsPrint='
	<tr><td colspan="'.$colspanPdf.'" class="total-row"></td></tr>
	<tr>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspanPDF.'">Other Totals :</td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherCharges,2).'</td>
		<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;"></td>
		<td class="notInsGroupBy text_10b" width="60" style="text-align:right; background:#FFFFFF;"></td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherPayments,2).'</td>
		<td width="65" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherOverPayment,2).'</td>
		<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherWriteOff,2).'</td>
		<td width="75" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherAdj,2).'</td>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otheriInsDue,2).'</td>
		<td width="60" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherPatDue,2).'</td>
		<td width="70" style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherBalance,2).'</td>
		'.$subTotBlankTD.'
	</tr>	
	<tr><td colspan="'.$colspanPdf.'" class="total-row"></td></tr>';	
	
}

if($showTotalAgain==1){
    $totalsRowCSV=
	$notPostedCSV
	.$otherTotals
    .'<tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">Total : </td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totalCharges+$otherCharges,2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr["patPaidAmt"],2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr["priPaidAmt"],2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grandTotalAmtArr["secPaidAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["pat_paid_amt"]+$otherPayments,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["creditProcAmount"]+$otherOverPayment,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["write_off_amt"]+$otherWriteOff,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grandTotalAmtArr["adj_amt"]+$otherAdj,2).'</td>
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


?>
<table class="rpt_table rpt rpt_table-bordered rpt_padding">
    <tr >
        <td style="text-align:left;" class="rptbx1" width="33%">&nbsp;Practice Analytics Report (Detail)</td>
        <td style="text-align:left;" class="rptbx2" width="34%"><?php print "$dayReport ($search) From : $Sdate To : $Edate"." Time: $hourFromL-$hourToL"; ?></td>
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
<?php if(count($providerIdArr)>0){ 
$totalChargesAmt=$grandTotalAmtArr['totalAmt'] + $chargesForNotPosted;
$totalPaymentAmt=$grandTotalAmtArr['pat_paid_amt'];
$totalAdjustmentAmt=$grandTotalAmtArr["write_off_amt"] + $grandTotalAmtArr["adj_amt"];
?>
<table class="rpt_table rpt rpt_table-bordered">
    <tr>
        <td style="text-align:center; width:70px;" class="text_b_w nowrap">Patient</td>
        <td style="text-align:center; width:50px;" class="text_b_w nowrap">ID</td>
		<td style="text-align:center; width:100px" class="text_b_w"><?php echo $firstGrpTitle1; ?></td>
		<td style="text-align:center; width:100px" class="text_b_w"><?php echo $secGrpTitle1; ?></td>
        <td style="text-align:center; width:100px" class="text_b_w">DOS</td>
        <td style="text-align:center; width:100px" class="text_b_w">DOT</td>
        <td style="text-align:center; width:60px" class="text_b_w">Enc. #</td>
        <td style="text-align:center; width:60px" class="text_b_w">CPT</td>
        <td style="text-align:center; width:60px" class="text_b_w">DX</td>
        <td class="notInsGroupBy text_b_w" style="text-align:center; width:60px">Primary Insurance</td>
        <td class="notInsGroupBy text_b_w" style="text-align:center; width:60px">Secondary Insurance</td>
        <td class="text_b_w" style="text-align:center; width:80px">Charges</td>
        <td class="notInsGroupBy text_b_w" style="text-align:center; width:70px">Pat. Paid</td>
        <td class="notInsGroupBy text_b_w" style="text-align:center; width:70px">Pri. Paid</td>
        <td class="notInsGroupBy text_b_w" style="text-align:center; width:70px">Sec. Paid</td>
        <td style="text-align:center; width:80px" class="text_b_w">Tot. Paid</td>
        <td style="text-align:center; width:90px" class="text_b_w">Credit</td>
        <td style="text-align:center; width:100px" class="text_b_w">Write-Off</td>
        <td style="text-align:center; width:100px" class="text_b_w">Adjustment</td>
        <td style="text-align:center; width:80px" class="text_b_w">Ins. Due</td>
        <td style="text-align:center; width:90px" class="text_b_w">Pat. Due</td>
        <td style="text-align:center; width:100px" class="text_b_w">Balance</td>
        <?php if($home_facility=='1'){
	      echo '<td style="text-align:center; width:100px" class="text_b_w">Home Facility</td>';
        }?>
    </tr>
    <?php print $csvFileData; ?>
	<tr><td colspan="<?php echo $colspan;?>" class="total-row"></td></tr>
    <tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="<?php echo $startColspan;?>"><?php echo $totalLabel;?> : </td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['totalAmt'],2); ?></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grandTotalAmtArr['patPaidAmt'],2); ?></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grandTotalAmtArr['priPaidAmt'],2); ?></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grandTotalAmtArr['secPaidAmt'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['creditProcAmount'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['write_off_amt'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['insuranceDue'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['patientDue'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['totalBalance'],2); ?></td>
        <?php echo $tdTotal;?>
    </tr>	
    <tr><td colspan="<?php echo $colspan;?>" class="total-row"></td></tr>
    <?php echo $totalsRowCSV;?>
</table>
<?php } ?>
<?php

//-----------DELETED CHARGES-------------
$del_csv = $del_csv_html = $del_pdf_html = '';
$del_csv='';
if(count($arrVoidPay)>0){
	$printFile=true;
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
	$del_paid_csv .= '
	'.$page_header_val.'
	<table class="rpt_table rpt rpt_table-bordered">
	<tr id="heading_orange"><td colspan="'.$total_cols.'">Voided Payments</td></tr>
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
		</tr>'.	
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
if(sizeof($mainResArr)>0 && empty($del_paid_csv) == false){
	$totalChargesAmt+=$otherCharges;
	$totalPaymentAmt+=$otherPayments;
	$totalAdjustmentAmt+=$otherWriteOff+$otherAdj;
	
	$grandCharges=$totalChargesAmt-$totalDeletedCharges;
	$grandPayments=$totalPaymentAmt-$totalDeletedPayments;
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
	</tr>
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
	<table class="rpt_table rpt rpt_table-bordered">
	<tr id="heading_orange"><td colspan="4">Grand Totals</td></tr>
	<tr>
		<td class="text_b_w" style="width:15%"></td>
		<td class="text_b_w" style="width:15%; text-align:center">Charges</td>
		<td class="text_b_w" style="width:15%; text-align:center">Payments</td>
		<td class="text_b_w" style="width:15%; text-align:center">Write-off & Adj</td>
	</tr>
	<tr class="text_12b">
		<td bgcolor="#FFFFFF" style="text-align:right">Total : </td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalChargesAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalPaymentAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalAdjustmentAmt,2).'</td>
	</tr>
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

//IN GRROUP BY INSURANCE CASE - REMOVING COLUMN THAT ARE NOT IN USE FOR THIS GROUPING
if($viewBy=='insurance'){
	$csv_file_data = preg_replace('/<td class="notInsGroupBy(.+)<\/td>/', '', $csv_file_data);
}

//--- PDF FILE DATA ---
ob_start();

$lblHomeFacTD=$totBlankTD='';
$lblEncTD='<td style="text-align:center;" width="50" class="text_b_w">Enc. #</td>';
if($home_facility=='1'){
	$lblEncTD='';
	$lblHomeFacTD='<td style="text-align:center;" width="55" class="text_b_w">Home Fac</td>';
	$totBlankTD='<td style="text-align:center;" class="text_10b"></td>';
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
        <tr>
            <td style="text-align:left;" class="rpt_headers rptbx1" width="342">Practice Analytics Report (Detail)</td>
            <td style="text-align:left;" class="rpt_headers rptbx2" width="350"><?php print "$dayReport ($search) From : $Sdate To : $Edate"." Time: $hourFromL-$hourToL"; ?></td>
            <td style="text-align:left;" class="rpt_headers rptbx3" width="350"><?php print "Created by $opInitial on $curDate"; ?></td>
        </tr>
        <tr>
            <td class="rpt_headers rptbx1">Group : <?php echo $selgroup; ?></td>
            <td class="rpt_headers rptbx2">Facility : <?php  echo $selFac; ?></td>
            <td class="rpt_headers rptbx3">Filing Phy. : <?php echo $selPhy; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	            Oper.: <?php echo $selOpr; ?>            
            </td>
        </tr>
        <tr>
            <td class="rpt_headers rptbx1">Credit Phy. : <?php echo $selCrPhy; ?></td>
            <td class="rpt_headers rptbx2">Insurance : <?php  echo $selInsurance; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Ins. Type : <?php  echo $selInsType; ?>
            </td>
            <td class="rpt_headers rptbx3">CPT : <?php echo $selCPT; ?></td>
        </tr>
        <tr>
            <td class="rpt_headers rptbx1">ICD9 : <?php echo $selDX; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ICD10 : <?php echo $selDX10; ?>
            </td>
            <td class="rpt_headers rptbx2">Modifiers : <?php  echo $selModifiers; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Adj. Code : <?php  echo $selAdjCode; ?>
            </td>
            <td class="rpt_headers rptbx3">Write-off Code : <?php echo $selWriteoff; ?></td>
        </tr>
    </table>
</page_header>    
    <table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8">
       <thead>        
        <tr>
            <td style="text-align:center;" width="80" class="text_b_w">Patient-ID</td>
						<td style="text-align:center;" width="50" class="text_b_w">DOS</td>
            <?php echo $lblEncTD;?>
            <td style="text-align:center;" width="65" class="text_b_w">CPT</td>
            <td style="text-align:center;" width="65" class="text_b_w">DX</td>
            <td style="text-align:center;" width="65" class="text_b_w">Charges</td>
            <td class="notInsGroupBy text_b_w" style="text-align:right;" width="65">Pat. Paid</td>
            <td class="notInsGroupBy text_b_w" style="text-align:right;" width="65">Ins. Paid</td>
            <td style="text-align:center;" width="65" class="text_b_w">Tot. Paid</td>
            <td style="text-align:right;" width="65" class="text_b_w">Credit</td>
            <td style="text-align:right;" width="65" class="text_b_w">Write-Off</td>
            <td style="text-align:right;" width="65" class="text_b_w">Adjustment</td>
            <td style="text-align:right;" width="65" class="text_b_w">Ins. Due</td>
            <td style="text-align:right;" width="65" class="text_b_w">Pat. Due</td>
            <td style="text-align:right;" width="65" class="text_b_w">Balance</td>
            <?php echo $lblHomeFacTD;?>
        </tr>
			</thead>        
    	<tbody>
    <?php print $csvFileDataPrint; ?>    
    <tr><td colspan="<?php echo $colspanPdf;?>" class="total-row"></td></tr>
    <tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="<?php echo $startColspanPDF;?>"><?php echo $totalLabel;?> : </td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['totalAmt'],2); ?></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grandTotalAmtArr['patPaidAmt'],2); ?></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grandTotalAmtArr['insPaidAmt'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['creditProcAmount'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['write_off_amt'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['insuranceDue'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['patientDue'],2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grandTotalAmtArr['totalBalance'],2); ?></td>
        <?php echo $totBlankTD;?>
    </tr>	
    <tr>
        <td colspan="<?php echo $colspanPdf;?>" class="total-row"></td>
    </tr>
    <?php echo $totalsRowPrint;?>
			</tbody>
</table>
<?php 
echo $del_pdf_html; 
echo $del_paid_pdf;
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
