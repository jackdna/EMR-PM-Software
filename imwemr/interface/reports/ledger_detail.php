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

$colspan=22;
$colspanPdf=17;
$startColspan=6;
$startColspanPDF=5;

$tdTotal='';
$totPosted=array();
$posted_data=$not_posted_data=$re_posted_data='';
$data_output='';

if($reportType!='checkView'){
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
}
	
if(sizeof($mainResArr['posted_charges'])>0){
	$data_output='';

	$data_output.="Patient".$pfx;
	$data_output.="DOS".$pfx;
	$data_output.="CPT".$pfx;
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
	
		$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		$csvFileDataPrint .='<tr><td class="text_b_w" colspan="'.$colspanPdf.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
	
		foreach($firstGrpData as $enc_id => $encDataArr){
	
			$patient_name = core_name_format($encDataArr[0]['lname'], $encDataArr[0]['fname'], $encDataArr[0]['mname']);		
			$patient_id = $encDataArr[0]['patient_id'];
			$date_of_service = $encDataArr[0]['date_of_service'];
			$first_posted_date = ($encDataArr[0]['first_posted_date_formatted']=='00-00-0000') ? '' : $encDataArr[0]['first_posted_date_formatted'];

			//--- GET TOTAL AMOUNT ----		
			$totalAmtArr = array();		
			$write_off_amt_arr = array();
			$arrCPT = array();
			$arrCPTDesc = array();
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
				$deptid=$encDataArr[$d]['departmentId'];
	
				if($encDataArr[$d]['totalAmt']>0){
					$totalAmtArr[] = $encDataArr[$d]['totalAmt'];
					$arrFirstGrpTotal["totalAmt"]+= $encDataArr[$d]['totalAmt'];
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

				
				$adjustment=$write_off=0;
				if($pay_location=='1' && $viewBy=='facility' && ($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment')){
					//CREDIT/DEBIT
					//$crdDbt= $pay_crd_deb_arr[$firstGrpId][$chgDetId]['Patient'] + $pay_crd_deb_arr[$firstGrpId][$chgDetId]['Insurance'];			
					$proc_paid=$mainEncounterPayArr[$firstGrpId][$chgDetId] + $crdDbt; 
					//WRITE-OFF & ADJUSTMENTS
					$write_off= $normalWriteOffAmt[$firstGrpId][$chgDetId] + $writte_off_arr[$firstGrpId][$chgDetId];
					$write_off_amt+=$write_off;
					$adjustment=$arrAdjustmentAmt[$firstGrpId][$chgDetId];
					$adj_amt+= $adjustment;
					
					$paidDetArr= $patPayDetArr[$firstGrpId][$chgDetId];

				}else{
					//CREDIT/DEBIT
					//$crdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'] + $pay_crd_deb_arr[$chgDetId]['Insurance'];			
					$proc_paid=$mainEncounterPayArr[$chgDetId] + $crdDbt; 
					//WRITE-OFF & ADJUSTMENTS
					$write_off= $normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					$write_off_amt+=$write_off;
					$adjustment= $arrAdjustmentAmt[$chgDetId];				
					$adj_amt+=$adjustment;
					
					$paidDetArr= $patPayDetArr[$chgDetId];
				}
				
				// TOTAL PAYMENT
				$patientPaidAmt+= $proc_paid;
	
	
				//PAID BY
				if($paidDetArr['patPaid']){ 
					$arrPaidBy['Patient']='Patient';
				}else if($paidDetArr['insPaid']){
					$arrPaidBy['Insurance']='Insurance';
				}
				
	
				$i=0;
				foreach($paidDetArr['method'] as $record_id => $transDet){
					$payment_mode=$paidDetArr['method'][$record_id];
					$paid_amount=$paidDetArr['paid'][$record_id]+$crdDbt;
					$crdDbt=0; //empty to variable after assign first time.

					if($paidDetArr['paid_date'][$record_id])$arrPaidDate[$paidDetArr['paid_date'][$record_id]]= $paidDetArr['paid_date'][$record_id];
					
					if($payment_mode == 'cash'){
						$payment_modeArr['Cash'] = 'Cash';
						$grand_total_arr['TOTAL_CASH']+= $paid_amount;
						$grandSumTotalPaidCash += $paid_amount;
					}
					else if($payment_mode == 'check'){
						//$check='Check - '.substr($paidDetArr['check_num'][$record_id],-5);
						$check='Check - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$check] = $check;
						$grand_total_arr['TOTAL_CHECK']+= $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
					}
					else if($payment_mode== 'money order'){
						//$mo='MO - '.substr($paidDetArr['check_num'][$record_id],-5);
						$mo='MO - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$mo] = $mo;
						$grand_total_arr['TOTAL_MO']+= $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
					}
					else if($payment_mode == 'eft'){
						//$eft='EFT - '.substr($paidDetArr['check_num'][$record_id],-5);
						$eft='EFT - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$eft] = $eft;
						$grand_total_arr['TOTAL_EFT']+= $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
					}								
					else if($payment_mode == 'credit card'){
						$cc=$paidDetArr['cc_type'][$record_id].' - '.substr($paidDetArr['cc_number'][$record_id],0,4);
						$payment_modeArr[$cc] = $cc;
						$grand_total_arr['TOTAL_CC']+= $paid_amount;
						$grandSumTotalPaidCC += $paid_amount;
					}else{
						$payment_modeArr['Other'] = 'Other';
						$grand_total_arr['TOTAL_OTHER']+= $paid_amount;
						$grandSumTotalPaidOther += $paid_amount;						
					}
					$i++;
				}
				
				$cptCode = $encDataArr[$d]['cpt_prac_code'];
				$arrCPT_CSV[$cptCode]=$cptCode;
				if(strlen($cptCode)>7){ $cptCode = substr($cptCode, 0, 7).'..';}
				$arrCPT[] = $cptCode;
				$cptCodeDesc = $encDataArr[$d]['cpt_desc'];
				$arrCPTDesc[] = $cptCodeDesc;
				if($encDataArr[$d]['dx_id1']!='' && $encDataArr[$d]['dx_id1']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id1']]=$encDataArr[$d]['dx_id1'];}
				if($encDataArr[$d]['dx_id2']!='' && $encDataArr[$d]['dx_id2']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id2']]=$encDataArr[$d]['dx_id2'];}
				if($encDataArr[$d]['dx_id3']!='' && $encDataArr[$d]['dx_id3']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id3']]=$encDataArr[$d]['dx_id3'];}
				if($encDataArr[$d]['dx_id4']!='' && $encDataArr[$d]['dx_id4']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id4']]=$encDataArr[$d]['dx_id4'];}
				if($encDataArr[$d]['dx_id5']!='' && $encDataArr[$d]['dx_id5']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id5']]=$encDataArr[$d]['dx_id5'];}
				if($encDataArr[$d]['dx_id6']!='' && $encDataArr[$d]['dx_id6']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id6']]=$encDataArr[$d]['dx_id6'];}
				if($encDataArr[$d]['dx_id7']!='' && $encDataArr[$d]['dx_id7']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id7']]=$encDataArr[$d]['dx_id7'];}
				if($encDataArr[$d]['dx_id8']!='' && $encDataArr[$d]['dx_id8']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id8']]=$encDataArr[$d]['dx_id8'];}
				if($encDataArr[$d]['dx_id9']!='' && $encDataArr[$d]['dx_id9']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id9']]=$encDataArr[$d]['dx_id9'];}
				if($encDataArr[$d]['dx_id10']!='' && $encDataArr[$d]['dx_id10']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id10']]=$encDataArr[$d]['dx_id10'];}
				if($encDataArr[$d]['dx_id11']!='' && $encDataArr[$d]['dx_id11']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id11']]=$encDataArr[$d]['dx_id11'];}
				if($encDataArr[$d]['dx_id12']!='' && $encDataArr[$d]['dx_id12']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id12']]=$encDataArr[$d]['dx_id12'];}
	
				
				if(empty($encDataArr[$d]['mod_id1'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id1']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id2'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id2']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id3'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id3']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id4'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id4']]; $arrModifiers[$mod_code]=$mod_code;}
				
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
				
				//DEPARTMENT ARRAY
				$arrDeptDetails[$firstGrpId][$deptid]['PAID']+= $proc_paid;
				$arrDeptDetails[$firstGrpId][$deptid]['ADJUSTMENT']+=$adjustment;
				$arrDeptDetails[$firstGrpId][$deptid]['WRITEOFF']+=$write_off;
				if(!$arrAlreadyAddedInCheckView[$chgDetId]){
					$arrDeptDetails[$firstGrpId][$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
					$arrDeptDetails[$firstGrpId][$deptid]['BALANCE']+=$balAmt;
				}
			}
			$totalAmt = array_sum($totalAmtArr);
	
		//	$arrFirstGrpTotal["totalAmt"]+= $totalAmt;
			$arrFirstGrpTotal["pat_paid_amt"]+= $patientPaidAmt;
			$arrFirstGrpTotal["adj_amt"]+= $adj_amt;
			$arrFirstGrpTotal["write_off_amt"]+= $write_off_amt;
			$arrFirstGrpTotal["totalBalance"]+= $totalBalance;		
	
			//--- CHANGE NUMBER FORMAT FOR ENCOUNTER ---
			$totalAmt = $CLSReports->numberFormat($totalAmt,2);
			$patientPaidAmt = $CLSReports->numberFormat($patientPaidAmt,2);
			$adj_amt = $CLSReports->numberFormat($adj_amt,2);
			$write_off_amt = $CLSReports->numberFormat($write_off_amt,2);
			$totalBalance = $CLSReports->numberFormat($totalBalance,2);
			
			$strCPT = implode(', ', $arrCPT);
			$strCPT_CSV = implode(', ', $arrCPT_CSV);
			$strDxCodes = implode(', ', $arrDxCodes);
			$strModifiers = implode(', ', $arrModifiers);
			$strCPTDesc = implode(', ', $arrCPTDesc);
			
			$tdPart=$tdPartPDF=$encTDPDF=$subTotBlankTD='';	 
	
			$paidDate=(sizeof($arrPaidDate)>0) ? implode(', ', $arrPaidDate) : '';
			$paidBy=(sizeof($arrPaidBy)>0) ? implode(', ', $arrPaidBy) : '';
			$paymentMethod=(sizeof($payment_modeArr)>0) ? implode(', ', $payment_modeArr) : '';
	
	
			$tmpFacilityName = '';
			$tmpFacilityName = $firstGrpName;
			
			$csvFileData .='
			<tr>
				<td class="text_10" style="width:110px; background:#FFFFFF;">'.$patient_name.' - '.$patient_id.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$date_of_service.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strCPT_CSV, 7, "<br>\n", true).'</td>			
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strCPTDesc, 15, "<br>\n", true).'</td>			
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strDxCodes, 10, "<br>\n", true).'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$strModifiers.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$first_posted_date.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalAmt.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidDate.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidBy.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$patientPaidAmt.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($paymentMethod, 8, "<br>\n", true).'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$adj_amt.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$write_off_amt.'</td>			
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalBalance.'</td>
			</tr>';

			//FOR CSV
			$data_output.='"'.$patient_name.' - '.$patient_id.'"'.$pfx;
			$data_output.='"'.$date_of_service.'"'.$pfx;
			$data_output.='"'.$strCPT_CSV.'"'.$pfx;
			$data_output.='"'.$strCPTDesc.'"'.$pfx;
			$data_output.='"'.$strDxCodes.'"'.$pfx;
			$data_output.='"'.$strModifiers.'"'.$pfx;
			$data_output.='"'.$first_posted_date.'"'.$pfx;
			$data_output.='"'.$totalAmt.'"'.$pfx;
			$data_output.='"'.$paidDate.'"'.$pfx;
			$data_output.='"'.$paidBy.'"'.$pfx;
			$data_output.='"'.$patientPaidAmt.'"'.$pfx;
			$data_output.='"'.$paymentMethod.'"'.$pfx;
			$data_output.='"'.$adj_amt.'"'.$pfx;
			$data_output.='"'.$write_off_amt.'"'.$pfx;
			$data_output.='"'.$totalBalance.'"'.$pfx;
			$data_output.="\n";			
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

		$csvFileData .='
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">'.$firstGrpTitle.' Total : </td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
		</tr>	
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
	
	/*	
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
		</tr>	
		<tr>
			<td colspan="'.$colspanPdf.'" class="total-row"></td>
		</tr>';*/
	}


	$posted_data=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="'.$colspan.'">Posted Charges</td></tr>
	<tr>
        <td style="text-align:center;" class="text_b_w nowrap">Patient</td>
        <td style="text-align:center;" class="text_b_w">DOS</td>
        <td style="text-align:center;" class="text_b_w">CPT</td>
        <td style="text-align:center;" class="text_b_w">CPT Desc.</td>
        <td style="text-align:center;" class="text_b_w">DX</td>
		<td style="text-align:center;" class="text_b_w">Mod.</td>
		<td style="text-align:center;" class="text_b_w">Posted Date</td>
        <td class="text_b_w" style="text-align:center;">Charges</td>
		<td style="text-align:center;" class="text_b_w">DOP</td>
		<td style="text-align:center;" class="text_b_w">Paid By</td>
        <td style="text-align:center;" class="text_b_w">Tot. Paid</td>
		<td style="text-align:center;" class="text_b_w">Method</td>
        <td style="text-align:center;" class="text_b_w">Adjustment</td>
        <td style="text-align:center;" class="text_b_w">Write-Off</td>
        <td style="text-align:center;" class="text_b_w">Balance</td>
    </tr>
    '.$csvFileData.'
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">Posted Total : </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["totalAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["pat_paid_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["adj_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["write_off_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totPosted["totalBalance"],2).'</td>
	</tr>	
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	</table>';	
	
	//FOR CSV
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.="Posted Total:".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totPosted["totalAmt"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totPosted["pat_paid_amt"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totPosted["adj_amt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totPosted["write_off_amt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totPosted["totalBalance"],2).'"'.$pfx;
	$data_output.="\n";	
	
	@fwrite($fp,$data_output);
}

//NOT POSTED
if(sizeof($mainResArr['not_posted_charges'])>0){
	$csvFileData=$csvFileDataPrint=$data_output='';

	$data_output.="Not Posted Charges".$pfx;
	$data_output.="\n"; 	
	
	$data_output.="Patient".$pfx;
	$data_output.="DOS".$pfx;
	$data_output.="CPT".$pfx;
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
	
		$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		$csvFileDataPrint .='<tr><td class="text_b_w" colspan="'.$colspanPdf.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		foreach($firstGrpData as $enc_id => $encDataArr){
	
			$patient_name = core_name_format($encDataArr[0]['lname'], $encDataArr[0]['fname'], $encDataArr[0]['mname']);		
			$patient_id = $encDataArr[0]['patient_id'];
			$date_of_service = $encDataArr[0]['date_of_service'];
			$charges_date = ($encDataArr[0]['entered_date']=='00-00-0000') ? '' : $encDataArr[0]['entered_date'];
			
			//--- GET TOTAL AMOUNT ----		
			$totalAmtArr = array();		
			$write_off_amt_arr = array();
			$arrCPT = array();
			$arrCPTDesc = array();
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
	
				if($encDataArr[$d]['totalAmt']>0){
					$totalAmtArr[] = $encDataArr[$d]['totalAmt'];
					$arrFirstGrpTotal["totalAmt"]+= $encDataArr[$d]['totalAmt'];
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


				$adjustment=$write_off=0;
				if($pay_location=='1' && $viewBy=='facility' && ($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment')){
					//CREDIT/DEBIT
					//$crdDbt= $pay_crd_deb_arr[$firstGrpId][$chgDetId]['Patient'] + $pay_crd_deb_arr[$firstGrpId][$chgDetId]['Insurance'];			
					$proc_paid=$mainEncounterPayArr[$firstGrpId][$chgDetId] + $crdDbt; 
					//WRITE-OFF & ADJUSTMENTS
					$write_off+= $normalWriteOffAmt[$firstGrpId][$chgDetId] + $writte_off_arr[$firstGrpId][$chgDetId];
					$write_off_amt+=$write_off;
					$adjustment= $arrAdjustmentAmt[$firstGrpId][$chgDetId];				
					$adj_amt+=$adjustment;
					
					$paidDetArr=$patPayDetArr[$firstGrpId][$chgDetId];

				}else{
					//CREDIT/DEBIT
					//$crdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'] + $pay_crd_deb_arr[$chgDetId]['Insurance'];			
					$proc_paid=$mainEncounterPayArr[$chgDetId] + $crdDbt; 
					//WRITE-OFF & ADJUSTMENTS
					$write_off+= $normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					$write_off_amt+=$write_off;
					$adjustment= $arrAdjustmentAmt[$chgDetId];				
					$adj_amt+=$adjustment;
					
					$paidDetArr=$patPayDetArr[$chgDetId];
				}
				
				
				// TOTAL PAYMENT
				$patientPaidAmt+= $proc_paid;
	
	
				//PAID BY
				if($paidDetArr['patPaid']){ 
					$arrPaidBy['Patient']='Patient';
				}else if($paidDetArr['insPaid']){
					$arrPaidBy['Insurance']='Insurance';
				}
				
	
				$i=0;
				foreach($paidDetArr['method'] as $record_id => $transDet){
					$payment_mode=$paidDetArr['method'][$record_id];
					$paid_amount=$paidDetArr['paid'][$record_id]+$crdDbt;
					
					$crdDbt=0; //empty to variable after assign first time.
					if($paidDetArr['paid_date'][$record_id])$arrPaidDate[$paidDetArr['paid_date'][$record_id]]= $paidDetArr['paid_date'][$record_id];
	
					if(strtolower($payment_mode) == 'cash'){
						$payment_modeArr['Cash'] = 'Cash';
						$grand_total_arr['TOTAL_CASH']+= $paid_amount;
						$grandSumTotalPaidCash += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'check'){
						//$check='Check - '.substr($paidDetArr['check_num'][$record_id],-5);
						$check='Check - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$check] = $check;
						$grand_total_arr['TOTAL_CHECK']+= $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'money order'){
						//$mo='MO - '.substr($paidDetArr['check_num'][$record_id],-5);
						$mo='MO - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$mo] = $mo;
						$grand_total_arr['TOTAL_MO']+= $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'eft'){
						//$eft='EFT - '.substr($paidDetArr['check_num'][$record_id],-5);
						$eft='EFT - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$eft] = $eft;
						$grand_total_arr['TOTAL_EFT']+= $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
					}								
					else if(strtolower($payment_mode) == 'credit card'){
						$cc=$paidDetArr['cc_type'][$record_id].' - '.substr($paidDetArr['cc_number'][$record_id],0,4);
						$payment_modeArr[$cc] = $cc;
						$grand_total_arr['TOTAL_CC']+= $paid_amount;
						$grandSumTotalPaidCC += $paid_amount;
					}else{
						$payment_modeArr['Other'] = 'Other';
						$grand_total_arr['TOTAL_OTHER']+= $paid_amount;
						$grandSumTotalPaidOther += $paid_amount;						
					}
					$i++;
				}
				
				$cptCode = $encDataArr[$d]['cpt_prac_code'];
				$arrCPT_CSV[$cptCode]=$cptCode;
				if(strlen($cptCode)>7){ $cptCode = substr($cptCode, 0, 7).'..';}
				$arrCPT[] = $cptCode;
				$cptCodeDesc = $encDataArr[$d]['cpt_desc'];
				$arrCptDesc[] = $cptCodeDesc;
				if($encDataArr[$d]['dx_id1']!='' && $encDataArr[$d]['dx_id1']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id1']]=$encDataArr[$d]['dx_id1'];}
				if($encDataArr[$d]['dx_id2']!='' && $encDataArr[$d]['dx_id2']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id2']]=$encDataArr[$d]['dx_id2'];}
				if($encDataArr[$d]['dx_id3']!='' && $encDataArr[$d]['dx_id3']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id3']]=$encDataArr[$d]['dx_id3'];}
				if($encDataArr[$d]['dx_id4']!='' && $encDataArr[$d]['dx_id4']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id4']]=$encDataArr[$d]['dx_id4'];}
				if($encDataArr[$d]['dx_id5']!='' && $encDataArr[$d]['dx_id5']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id5']]=$encDataArr[$d]['dx_id5'];}
				if($encDataArr[$d]['dx_id6']!='' && $encDataArr[$d]['dx_id6']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id6']]=$encDataArr[$d]['dx_id6'];}
				if($encDataArr[$d]['dx_id7']!='' && $encDataArr[$d]['dx_id7']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id7']]=$encDataArr[$d]['dx_id7'];}
				if($encDataArr[$d]['dx_id8']!='' && $encDataArr[$d]['dx_id8']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id8']]=$encDataArr[$d]['dx_id8'];}
				if($encDataArr[$d]['dx_id9']!='' && $encDataArr[$d]['dx_id9']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id9']]=$encDataArr[$d]['dx_id9'];}
				if($encDataArr[$d]['dx_id10']!='' && $encDataArr[$d]['dx_id10']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id10']]=$encDataArr[$d]['dx_id10'];}
				if($encDataArr[$d]['dx_id11']!='' && $encDataArr[$d]['dx_id11']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id11']]=$encDataArr[$d]['dx_id11'];}
				if($encDataArr[$d]['dx_id12']!='' && $encDataArr[$d]['dx_id12']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id12']]=$encDataArr[$d]['dx_id12'];}

				if(empty($encDataArr[$d]['mod_id1'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id1']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id2'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id2']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id3'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id3']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id4'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id4']]; $arrModifiers[$mod_code]=$mod_code;}
				
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

				//DEPARTMENT ARRAY
				$arrDeptDetails[$firstGrpId][$deptid]['PAID']+= $proc_paid;
				$arrDeptDetails[$firstGrpId][$deptid]['ADJUSTMENT']+=$adjustment;
				$arrDeptDetails[$firstGrpId][$deptid]['WRITEOFF']+=$write_off;
				if(!$arrAlreadyAddedInCheckView[$chgDetId]){
					$arrDeptDetails[$firstGrpId][$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
					$arrDeptDetails[$firstGrpId][$deptid]['BALANCE']+=$balAmt;
				}				
			}
			$totalAmt = array_sum($totalAmtArr);
	
		//	$arrFirstGrpTotal["totalAmt"]+= $totalAmt;
			$arrFirstGrpTotal["pat_paid_amt"]+= $patientPaidAmt;
			$arrFirstGrpTotal["adj_amt"]+= $adj_amt;
			$arrFirstGrpTotal["write_off_amt"]+= $write_off_amt;
			$arrFirstGrpTotal["totalBalance"]+= $totalBalance;		
	
			//--- CHANGE NUMBER FORMAT FOR ENCOUNTER ---
			$totalAmt = $CLSReports->numberFormat($totalAmt,2);
			$patientPaidAmt = $CLSReports->numberFormat($patientPaidAmt,2);
			$adj_amt = $CLSReports->numberFormat($adj_amt,2);
			$write_off_amt = $CLSReports->numberFormat($write_off_amt,2);
			$totalBalance = $CLSReports->numberFormat($totalBalance,2);
			
			$strCPT = implode(', ', $arrCPT);
			$strCPT_CSV = implode(', ', $arrCPT_CSV);
			$strDxCodes = implode(', ', $arrDxCodes);
			$strModifiers = implode(', ', $arrModifiers);
			$strCptDesc = implode(', ', $arrCptDesc);
			
			$tdPart=$tdPartPDF=$encTDPDF=$subTotBlankTD='';	 
	
			$paidDate=(sizeof($arrPaidDate)>0) ? implode(', ', $arrPaidDate) : '';
			$paidBy=(sizeof($arrPaidBy)>0) ? implode(', ', $arrPaidBy) : '';
			$paymentMethod=(sizeof($payment_modeArr)>0) ? implode(', ', $payment_modeArr) : '';
	
	
			$tmpFacilityName = '';
			$tmpFacilityName = $firstGrpName;
			
			$csvFileData .='
			<tr>
				<td class="text_10" style="width:110px; background:#FFFFFF;">'.$patient_name.' - '.$patient_id.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$date_of_service.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strCPT_CSV, 8, "<br>\n", true).'</td>			
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strCptDesc, 15, "<br>\n", true).'</td>			
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strDxCodes, 10, "<br>\n", true).'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$strModifiers.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$charges_date.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalAmt.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidDate.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidBy.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$patientPaidAmt.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($paymentMethod, 8, "<br>\n", true).'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$adj_amt.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$write_off_amt.'</td>			
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalBalance.'</td>
			</tr>';

			//FOR CSV
			$data_output.='"'.$patient_name.' - '.$patient_id.'"'.$pfx;
			$data_output.='"'.$date_of_service.'"'.$pfx;
			$data_output.='"'.$strCPT_CSV.'"'.$pfx;
			$data_output.='"'.$strCptDesc.'"'.$pfx;
			$data_output.='"'.$strDxCodes.'"'.$pfx;
			$data_output.='"'.$strModifiers.'"'.$pfx;
			$data_output.='"'.$charges_date.'"'.$pfx;
			$data_output.='"'.$totalAmt.'"'.$pfx;
			$data_output.='"'.$paidDate.'"'.$pfx;
			$data_output.='"'.$paidBy.'"'.$pfx;
			$data_output.='"'.$patientPaidAmt.'"'.$pfx;
			$data_output.='"'.$paymentMethod.'"'.$pfx;
			$data_output.='"'.$adj_amt.'"'.$pfx;
			$data_output.='"'.$write_off_amt.'"'.$pfx;
			$data_output.='"'.$totalBalance.'"'.$pfx;
			$data_output.="\n";				
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

		$csvFileData .='
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">'.$firstGrpTitle.' Total : </td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
		</tr>	
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
	
	/*	
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
		</tr>	
		<tr>
			<td colspan="'.$colspanPdf.'" class="total-row"></td>
		</tr>';*/
	}


	$not_posted_data=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="'.$colspan.'">Not Posted Charges</td></tr>
	<tr>
        <td style="text-align:center;" class="text_b_w nowrap">Patient</td>
        <td style="text-align:center;" class="text_b_w">DOS</td>
        <td style="text-align:center;" class="text_b_w">CPT</td>
        <td style="text-align:center;" class="text_b_w">CPT Desc.</td>
        <td style="text-align:center;" class="text_b_w">DX</td>
		<td style="text-align:center;" class="text_b_w">Mod.</td>
		<td style="text-align:center;" class="text_b_w">Posted Date</td>
        <td class="text_b_w" style="text-align:center;">Charges</td>
		<td style="text-align:center;" class="text_b_w">DOP</td>
		<td style="text-align:center;" class="text_b_w">Paid By</td>
        <td style="text-align:center;" class="text_b_w">Tot. Paid</td>
		<td style="text-align:center;" class="text_b_w">Method</td>
        <td style="text-align:center;" class="text_b_w">Adjustment</td>
        <td style="text-align:center;" class="text_b_w">Write-Off</td>
        <td style="text-align:center;" class="text_b_w">Balance</td>
    </tr>
    '.$csvFileData.'
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">Not Posted Total : </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["totalAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["pat_paid_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["adj_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["write_off_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totNotPosted["totalBalance"],2).'</td>
	</tr>	
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	</table>';		

	//FOR CSV
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.="Not Posted Total:".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totNotPosted["totalAmt"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totNotPosted["pat_paid_amt"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totNotPosted["adj_amt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totNotPosted["write_off_amt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totNotPosted["totalBalance"],2).'"'.$pfx;
	$data_output.="\n";		
	
	@fwrite($fp,$data_output);
}

//RE-SUBMITTED
if(sizeof($mainResArr['re_posted_charges'])>0){
	$csvFileData=$csvFileDataPrint=$data_output='';

	$data_output.="Re-submitted Charges".$pfx;
	$data_output.="\n"; 	
	
	$data_output.="Patient".$pfx;
	$data_output.="DOS".$pfx;
	$data_output.="CPT".$pfx;
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
	
		$csvFileData .='<tr><td class="text_b_w" colspan="'.$colspan.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		$csvFileDataPrint .='<tr><td class="text_b_w" colspan="'.$colspanPdf.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
	
		foreach($firstGrpData as $enc_id => $encDataArr){
	
			$patient_name = core_name_format($encDataArr[0]['lname'], $encDataArr[0]['fname'], $encDataArr[0]['mname']);		
			$patient_id = $encDataArr[0]['patient_id'];
			$date_of_service = $encDataArr[0]['date_of_service'];
			$first_posted_date = ($encDataArr[0]['first_posted_date_formatted']=='00-00-0000') ? '' : $encDataArr[0]['first_posted_date_formatted'];
			
			//--- GET TOTAL AMOUNT ----		
			$totalAmtArr = array();		
			$write_off_amt_arr = array();
			$arrCPT = array();
			$arrCPTDesc = array();
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
	
				if($encDataArr[$d]['totalAmt']>0){
					$totalAmtArr[] = $encDataArr[$d]['totalAmt'];
					$arrFirstGrpTotal["totalAmt"]+= $encDataArr[$d]['totalAmt'];
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
				
				$adjustment=$write_off=0;
				if($pay_location=='1' && $viewBy=='facility' && ($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment')){
					//CREDIT/DEBIT
					//$crdDbt= $pay_crd_deb_arr[$firstGrpId][$chgDetId]['Patient'] + $pay_crd_deb_arr[$firstGrpId][$chgDetId]['Insurance'];			
					$proc_paid=$mainEncounterPayArr[$firstGrpId][$chgDetId] + $crdDbt; 
					//WRITE-OFF & ADJUSTMENTS
					$write_off+= $normalWriteOffAmt[$firstGrpId][$chgDetId] + $writte_off_arr[$firstGrpId][$chgDetId];
					$write_off_amt+=$write_off;
					$adjustment= $arrAdjustmentAmt[$firstGrpId][$chgDetId];				
					$adj_amt+=$adjustment;
					
					$paidDetArr=$patPayDetArr[$firstGrpId][$chgDetId];

				}else{
					//CREDIT/DEBIT
					//$crdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'] + $pay_crd_deb_arr[$chgDetId]['Insurance'];			
					$proc_paid=$mainEncounterPayArr[$chgDetId] + $crdDbt; 
					//WRITE-OFF & ADJUSTMENTS
					$write_off+= $normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
					$write_off_amt+=$write_off;
					$adjustment= $arrAdjustmentAmt[$chgDetId];				
					$adj_amt+=$adjustment;
					
					$paidDetArr=$patPayDetArr[$chgDetId];
				}
				
				// TOTAL PAYMENT
				$patientPaidAmt+= $proc_paid;
	
	
				//PAID BY
				if($paidDetArr['patPaid']){ 
					$arrPaidBy['Patient']='Patient';
				}else if($paidDetArr['insPaid']){
					$arrPaidBy['Insurance']='Insurance';
				}
				
	
				$i=0;
				foreach($paidDetArr['method'] as $record_id => $transDet){
					
					$payment_mode=$paidDetArr['method'][$record_id];
					$paid_amount=$paidDetArr['paid'][$record_id]+$crdDbt;
					$crdDbt=0; //empty to variable after assign first time.
					if($paidDetArr['paid_date'][$record_id])$arrPaidDate[$paidDetArr['paid_date'][$record_id]]= $paidDetArr['paid_date'][$record_id];
	
					if(strtolower($payment_mode) == 'cash'){
						$payment_modeArr['Cash'] = 'Cash';
						$grand_total_arr['TOTAL_CASH']+= $paid_amount;
						$grandSumTotalPaidCash += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'check'){
						//$check='Check - '.substr($paidDetArr['check_num'][$record_id],-5);
						$check='Check - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$check] = $check;
						$grand_total_arr['TOTAL_CHECK']+= $paid_amount;
						$grandSumTotalPaidCheck += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'money order'){
						//$mo='MO - '.substr($paidDetArr['check_num'][$record_id],-5);
						$mo='MO - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$mo] = $mo;
						$grand_total_arr['TOTAL_MO']+= $paid_amount;
						$grandSumTotalPaidMo += $paid_amount;
					}
					else if(strtolower($payment_mode) == 'eft'){
						//$eft='EFT - '.substr($paidDetArr['check_num'][$record_id],-5);
						$eft='EFT - '.$paidDetArr['check_num'][$record_id];
						$payment_modeArr[$eft] = $eft;
						$grand_total_arr['TOTAL_EFT']+= $paid_amount;
						$grandSumTotalPaidEft += $paid_amount;
					}								
					else if(strtolower($payment_mode) == 'credit card'){
						$cc=$paidDetArr['cc_type'][$record_id].' - '.substr($paidDetArr['cc_number'][$record_id],0,4);
						$payment_modeArr[$cc] = $cc;
						$grand_total_arr['TOTAL_CC']+= $paid_amount;
						$grandSumTotalPaidCC += $paid_amount;
					}else{
						$payment_modeArr['Other'] = 'Other';
						$grand_total_arr['TOTAL_OTHER']+= $paid_amount;
						$grandSumTotalPaidOther += $paid_amount;						
					}
					$i++;
				}
				
				$cptCode = $encDataArr[$d]['cpt_prac_code'];
				$arrCPT_CSV[$cptCode]=$cptCode;
				if(strlen($cptCode)>7){ $cptCode = substr($cptCode, 0, 7).'..';}
				$arrCPT[] = $cptCode;
				
				$cptCodeDesc = $encDataArr[$d]['cpt_desc'];
				$arrCPTDesc[] = $cptCodeDesc;
				if($encDataArr[$d]['dx_id1']!='' && $encDataArr[$d]['dx_id1']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id1']]=$encDataArr[$d]['dx_id1'];}
				if($encDataArr[$d]['dx_id2']!='' && $encDataArr[$d]['dx_id2']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id2']]=$encDataArr[$d]['dx_id2'];}
				if($encDataArr[$d]['dx_id3']!='' && $encDataArr[$d]['dx_id3']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id3']]=$encDataArr[$d]['dx_id3'];}
				if($encDataArr[$d]['dx_id4']!='' && $encDataArr[$d]['dx_id4']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id4']]=$encDataArr[$d]['dx_id4'];}
				if($encDataArr[$d]['dx_id5']!='' && $encDataArr[$d]['dx_id5']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id5']]=$encDataArr[$d]['dx_id5'];}
				if($encDataArr[$d]['dx_id6']!='' && $encDataArr[$d]['dx_id6']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id6']]=$encDataArr[$d]['dx_id6'];}
				if($encDataArr[$d]['dx_id7']!='' && $encDataArr[$d]['dx_id7']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id7']]=$encDataArr[$d]['dx_id7'];}
				if($encDataArr[$d]['dx_id8']!='' && $encDataArr[$d]['dx_id8']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id8']]=$encDataArr[$d]['dx_id8'];}
				if($encDataArr[$d]['dx_id9']!='' && $encDataArr[$d]['dx_id9']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id9']]=$encDataArr[$d]['dx_id9'];}
				if($encDataArr[$d]['dx_id10']!='' && $encDataArr[$d]['dx_id10']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id10']]=$encDataArr[$d]['dx_id10'];}
				if($encDataArr[$d]['dx_id11']!='' && $encDataArr[$d]['dx_id11']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id11']]=$encDataArr[$d]['dx_id11'];}
				if($encDataArr[$d]['dx_id12']!='' && $encDataArr[$d]['dx_id12']!='0'){ $arrDxCodes[$encDataArr[$d]['dx_id12']]=$encDataArr[$d]['dx_id12'];}

				if(empty($encDataArr[$d]['mod_id1'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id1']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id2'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id2']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id3'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id3']]; $arrModifiers[$mod_code]=$mod_code;}
				if(empty($encDataArr[$d]['mod_id4'])===false){ $mod_code=$arrAllModifiers[$encDataArr[$d]['mod_id4']]; $arrModifiers[$mod_code]=$mod_code;}
				
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

				//DEPARTMENT ARRAY
				$arrDeptDetails[$firstGrpId][$deptid]['PAID']+= $proc_paid;
				$arrDeptDetails[$firstGrpId][$deptid]['ADJUSTMENT']+=$adjustment;
				$arrDeptDetails[$firstGrpId][$deptid]['WRITEOFF']+=$write_off;
				if(!$arrAlreadyAddedInCheckView[$chgDetId]){
					$arrDeptDetails[$firstGrpId][$deptid]['CHARGES']+=$encDataArr[$d]['totalAmt'];
					$arrDeptDetails[$firstGrpId][$deptid]['BALANCE']+=$balAmt;
				}				
			}
			$totalAmt = array_sum($totalAmtArr);
	
		//	$arrFirstGrpTotal["totalAmt"]+= $totalAmt;
			$arrFirstGrpTotal["pat_paid_amt"]+= $patientPaidAmt;
			$arrFirstGrpTotal["adj_amt"]+= $adj_amt;
			$arrFirstGrpTotal["write_off_amt"]+= $write_off_amt;
			$arrFirstGrpTotal["totalBalance"]+= $totalBalance;		
	
			//--- CHANGE NUMBER FORMAT FOR ENCOUNTER ---
			$totalAmt = $CLSReports->numberFormat($totalAmt,2);
			$patientPaidAmt = $CLSReports->numberFormat($patientPaidAmt,2);
			$adj_amt = $CLSReports->numberFormat($adj_amt,2);
			$write_off_amt = $CLSReports->numberFormat($write_off_amt,2);
			$totalBalance = $CLSReports->numberFormat($totalBalance,2);
			
			$strCPT = implode(', ', $arrCPT);
			$strCPT_CSV = implode(', ', $arrCPT_CSV);
			$strDxCodes = implode(', ', $arrDxCodes);
			$strModifiers = implode(', ', $arrModifiers);
			$strCodeDesc = implode(', ', $arrCPTDesc);
			
			$tdPart=$tdPartPDF=$encTDPDF=$subTotBlankTD='';	 
	
			$paidDate=(sizeof($arrPaidDate)>0) ? implode(', ', $arrPaidDate) : '';
			$paidBy=(sizeof($arrPaidBy)>0) ? implode(', ', $arrPaidBy) : '';
			$paymentMethod=(sizeof($payment_modeArr)>0) ? implode(', ', $payment_modeArr) : '';
	
	
			$tmpFacilityName = '';
			$tmpFacilityName = $firstGrpName;
			
			$csvFileData .='
			<tr>
				<td class="text_10" style="width:110px; background:#FFFFFF;">'.$patient_name.' - '.$patient_id.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$date_of_service.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strCPT_CSV, 8, "<br>\n", true).'</td>			
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strCodeDesc, 15, "<br>\n", true).'</td>			
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($strDxCodes, 10, "<br>\n", true).'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$strModifiers.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$first_posted_date.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalAmt.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidDate.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.$paidBy.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$patientPaidAmt.'</td>
				<td class="text_10" style="width:60px; background:#FFFFFF;">'.wordwrap($paymentMethod, 8, "<br>\n", true).'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$adj_amt.'</td>
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$write_off_amt.'</td>			
				<td class="text_10" style="width:60px; text-align:right; background:#FFFFFF;">'.$totalBalance.'</td>
			</tr>';

			//FOR CSV
			$data_output.='"'.$patient_name.' - '.$patient_id.'"'.$pfx;
			$data_output.='"'.$date_of_service.'"'.$pfx;
			$data_output.='"'.$strCPT_CSV.'"'.$pfx;
			$data_output.='"'.$strCodeDesc.'"'.$pfx;
			$data_output.='"'.$strDxCodes.'"'.$pfx;
			$data_output.='"'.$strModifiers.'"'.$pfx;
			$data_output.='"'.$first_posted_date.'"'.$pfx;
			$data_output.='"'.$totalAmt.'"'.$pfx;
			$data_output.='"'.$paidDate.'"'.$pfx;
			$data_output.='"'.$paidBy.'"'.$pfx;
			$data_output.='"'.$patientPaidAmt.'"'.$pfx;
			$data_output.='"'.$paymentMethod.'"'.$pfx;
			$data_output.='"'.$adj_amt.'"'.$pfx;
			$data_output.='"'.$write_off_amt.'"'.$pfx;
			$data_output.='"'.$totalBalance.'"'.$pfx;
			$data_output.="\n";					
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

		$csvFileData .='
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
		<tr>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">'.$firstGrpTitle.' Total : </td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["write_off_amt"],2).'</td>
			<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2).'</td>
		</tr>	
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
	
	/*	
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
		</tr>	
		<tr>
			<td colspan="'.$colspanPdf.'" class="total-row"></td>
		</tr>';*/
	}


	$re_posted_data=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="'.$colspan.'">Re-submitted Charges</td></tr>
	<tr>
        <td style="text-align:center;" class="text_b_w nowrap">Patient</td>
        <td style="text-align:center;" class="text_b_w">DOS</td>
        <td style="text-align:center;" class="text_b_w">CPT</td>
        <td style="text-align:center;" class="text_b_w">CPT Desc.</td>
        <td style="text-align:center;" class="text_b_w">DX</td>
		<td style="text-align:center;" class="text_b_w">Mod.</td>
		<td style="text-align:center;" class="text_b_w">Posted Date</td>
        <td class="text_b_w" style="text-align:center;">Charges</td>
		<td style="text-align:center;" class="text_b_w">DOP</td>
		<td style="text-align:center;" class="text_b_w">Paid By</td>
        <td style="text-align:center;" class="text_b_w">Tot. Paid</td>
		<td style="text-align:center;" class="text_b_w">Method</td>
        <td style="text-align:center;" class="text_b_w">Adjustment</td>
        <td style="text-align:center;" class="text_b_w">Write-Off</td>
        <td style="text-align:center;" class="text_b_w">Balance</td>
    </tr>
    '.$csvFileData.'
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b" colspan="'.$startColspan.'">Re-submitted Total : </td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["totalAmt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["pat_paid_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["adj_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["write_off_amt"],2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($totRePosted["totalBalance"],2).'</td>
	</tr>	
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	</table>';	
	
	//FOR CSV
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.="Re-submitted Total:".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totRePosted["totalAmt"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totRePosted["pat_paid_amt"],2).'"'.$pfx;
	$data_output.=" ".$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totRePosted["adj_amt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totRePosted["write_off_amt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($totRePosted["totalBalance"],2).'"'.$pfx;
	$data_output.="\n";		
	
	@fwrite($fp,$data_output);
}

//GRAND TOTALS
if(sizeof($grand_total_arr)>0){
	$data_output='';
	$grand_title_part=($reportType=='checkView')? ' (Except Check, EFT and MO)' :'';
	
	$totPaid=$grand_total_arr["TOTAL_CASH"]+$grand_total_arr["TOTAL_CHECK"]+$grand_total_arr["TOTAL_MO"]+$grand_total_arr["TOTAL_EFT"]+$grand_total_arr["TOTAL_CC"]+$grand_total_arr["TOTAL_OTHER"]; 
	
	$grand_totals=
	'<table class="rpt_table rpt rpt_table-bordered">
    <tr id="heading_orange" ><td colspan="11">Grand Totals'.$grand_title_part.'</td></tr>
	<tr>
        <td style="text-align:center; width:81px;" class="text_b_w nowrap">Patient#</td>
        <td style="text-align:center; width:81px" class="text_b_w">Charges</td>
        <td style="text-align:center; width:81px" class="text_b_w">Cash</td>
        <td style="text-align:center; width:81px" class="text_b_w">Check</td>
		<td style="text-align:center; width:81px" class="text_b_w">Money Order</td>
		<td style="text-align:center; width:81px" class="text_b_w">EFT</td>
		<td class="text_b_w" style="text-align:center; width:81px">Credit Cards</td>
		<td style="text-align:center; width:81px" class="text_b_w">Other</td>
		<td style="text-align:center; width:81px" class="text_b_w">Adjustment</td>
		<td style="text-align:center; width:81px" class="text_b_w">Write-off</td>
        <td style="text-align:center; width:81px" class="text_b_w">Balance</td>
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

	//FOR CSV
	$data_output.="Grand Totals.$grand_title_part".$pfx;
	$data_output.="\n"; 
	
	$data_output.="Patient#".$pfx;
	$data_output.="Charges".$pfx;
	$data_output.="Cash".$pfx;
	$data_output.="Check".$pfx;
	$data_output.="Money Order".$pfx;
	$data_output.="EFT".$pfx;
	$data_output.="Credit Cards".$pfx;
	$data_output.="Other".$pfx;
	$data_output.="Adjustment".$pfx;
	$data_output.="Write-off".$pfx;
	$data_output.="Balance".$pfx;
	$data_output.="\n";

	$data_output.=count($grandTotalAmtArr["patient_count"]).$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr["totalAmt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_total_arr["TOTAL_CASH"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_total_arr["TOTAL_CHECK"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_total_arr["TOTAL_MO"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_total_arr["TOTAL_EFT"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_total_arr["TOTAL_CC"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grand_total_arr["TOTAL_OTHER"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr["adj_amt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr["write_off_amt"],2).'"'.$pfx;
	$data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr["totalBalance"],2).'"'.$pfx;
	$data_output.="\n";	
	@fwrite($fp,$data_output);
}


// DEPARTMENT DETAILS
if(sizeof($arrDeptDetails)>0){
	$data_output='';
	$dept_title_part=($reportType=='checkView')? ' (Including Check, EFT and MO)' :'';
	
	$department_data.='
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
	
	foreach($arrDeptDetails as $firstGrpId => $deptdata){
		$arrSubDept=array();
		
		if($viewBy=='physician'){
			$firstGrpTitle='Physician';
			$firstGrpName = $providerNameArr[$firstGrpId];
		}else{
			$firstGrpTitle='Facility';
			$firstGrpName = ($pay_location=='1' || $billing_location=='1') ? $arrSchFacilites[$firstGrpId] : $arrAllFacilities[$firstGrpId];
		}
	
		$department_data .='<tr><td class="text_b_w" colspan="6">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		
		//FOR CSV
		$data_output.=$firstGrpTitle.' : '.$firstGrpName.$pfx;
		$data_output.="\n"; 
		
		foreach($deptdata as $deptid => $deptDetails){
			
			$deptName= $arrDeptNames[$deptid];
			$arrSubDept['CHARGES']+=$deptDetails["CHARGES"];
			$arrSubDept['PAID']+=$deptDetails["PAID"];
			$arrSubDept['ADJUSTMENT']+=$deptDetails["ADJUSTMENT"];
			$arrSubDept['WRITEOFF']+=$deptDetails["WRITEOFF"];
			$arrSubDept['BALANCE']+=$deptDetails["BALANCE"];
			
			$department_data.='
			<tr>
				<td style=" width:165px; text-align:left; background:#FFFFFF;" class="text_10">'.$deptName.'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["CHARGES"],2).'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["PAID"],2).'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["ADJUSTMENT"],2).'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["WRITEOFF"],2).'</td>
				<td style=" width:165px; text-align:right; background:#FFFFFF;" class="text_10">'.$CLSReports->numberFormat($deptDetails["BALANCE"],2).'</td>
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


$totalChargesAmt=$grandTotalAmtArr['totalAmt'];
$totalPaymentAmt=$grandTotalAmtArr['pat_paid_amt'];
$totalAdjustmentAmt=$grandTotalAmtArr["write_off_amt"] + $grandTotalAmtArr["adj_amt"];

$csv_file_data=$csv_check_data;
if($reportType=='checkView'){
	if($posted_data!='' || $not_posted_data!='' || $re_posted_data!=''){
		$csv_file_data.='<br><table class="rpt_table rpt rpt_table-bordered">
		<tr id="heading_orange" ><td colspan="10">Transactions Except Check, EFT and MO</td></tr></table>';
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
$department_data;


//IN GRROUP BY INSURANCE CASE - REMOVING COLUMN THAT ARE NOT IN USE FOR THIS GROUPING
//$np_page_content_pdf = preg_replace('/<td class="notInPdf(.+)<\/td>/', '', $not_posted_content);

//IN GRROUP BY INSURANCE CASE - REMOVING COLUMN THAT ARE NOT IN USE FOR THIS GROUPING
if($viewBy=='insurance'){
	//$pdf_page_content = preg_replace('/<td class="notInsGroupBy(.+)<\/td>/', '', $pdf_page_content);
}
?>
