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
/*
FILE : summaryProductivity.php
PURPOSE : Display summary result of Practice Analytic report
ACCESS TYPE : Indirect
*/

$isData=false;

ob_start();
$providerIdArr = array_keys($mainResArr);
$pdf_page_content = NULL;
$csvFileData = NULL;
$grandTotalAmtArr = array();

$j = $k = 0; $chartData = array();
$chartProviderId = array();
$chartFacilityName = array();
$chartFacilityId = array();

$cols=13;
$cWidth1='84';
$cWidth2='82';
if($DateRangeFor=='date_of_service'){ 
	$cols=15; 
	$cWidth1='80';
	$cWidth2='67';
}

if($viewBy=='insurance'){
	$cols-=3;	
}
if($viewBy=='physician' || $viewBy=='operator'){
	$colTitle='Facility';
}else{
	$colTitle='Physician';
}	

foreach($mainResArr as $firstGrpId => $firstGrpData){	
		$isData=true;
		$fac_detail_data = NULL;
		$sub_pro_total_arr = array();
	
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
	
		$csvFileData.='<tr><td class="text_b_w" colspan="'.$cols.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		
		foreach($firstGrpData as $secGrpId => $secGrpData){	
	
			$pro_sub_amt_arr = array();
			$units_arr = array();
			$encounter_arr=array();
	
			foreach($secGrpData as $enc_id => $enc_detail_arr){	
	
				$patDue = $insDue = '';
				$totBalance= $patCrdDbt= $insCrdDbt= $creditProcAmount= $write_off_amt= $adj_amt=$patPaid=$insPaid=$priPaid=$secPaid=$totalPaid=0;
				$encounter_arr[$enc_id]=$enc_id;
				$sub_pro_total_arr["enc_count"][$enc_id] = $enc_id;
				$sub_pro_total_arr["enc_count"][$enc_id] = $enc_id;
				$grandTotalAmtArr["enc_count"][$enc_id] = $enc_id;
	
				$submitted = $enc_detail_arr[0]["submitted"];
				$first_posted_date = $enc_detail_arr[0]["first_posted_date"];
	
				//--- GET ENCOUNTER CREDIT AND WRITE OFF AMOUNT ----
				for($d=0;$d<count($enc_detail_arr);$d++){
					$chgDetId = $enc_detail_arr[$d]['charge_list_detail_id'];
					
					if($enc_detail_arr[$d]["totalAmt"]>0){
						$pro_sub_amt_arr["totalAmt"][] = $enc_detail_arr[$d]["totalAmt"];
					}
	
					//PAT & INS DUES
					$patDue+= $enc_detail_arr[$d]["pat_due"];
					$insDue+= $enc_detail_arr[$d]['pri_due'] + $enc_detail_arr[$d]['sec_due'] + $enc_detail_arr[$d]['tri_due'];
					
					if($viewBy=='insurance'){
						if(!$tempIsChgDetId2[$firstGrpId][$chgDetId]){ //SOME ENCOUNTER HAS SAME PRIMARY AND SEC INS SO TO AVOID DUPLICATE OF AMOUNTS THIS CHECK ADDED. 
							//CREDIT/DEBIT
							$insCrdDbt= array_sum($pay_crd_deb_arr[$firstGrpId][$chgDetId]);
			
							//PAYMENTS
							$totalPaid+=array_sum($mainEncounterPayArr[$firstGrpId][$chgDetId]) + $insCrdDbt;
						
							//WRITE-OFF & ADJUSTEMENT
							$write_off_amt+= $normalWriteOffAmt[$firstGrpId][$chgDetId] + array_sum($writte_off_arr[$firstGrpId][$chgDetId]);
							$adj_amt+= $arrAdjustmentAmt[$firstGrpId][$chgDetId];				
	
							unset($arrOtherGrandTotal['charges'][$chgDetId]);
							unset($arrOtherGrandTotal['ins_due'][$chgDetId]);
							unset($arrOtherGrandTotal['pat_due'][$chgDetId]);
							unset($arrOtherGrandTotal['over_payment'][$chgDetId]);
							unset($arrOtherGrandTotal['balance'][$chgDetId]);
							unset($arrOtherGrandTotal['units'][$chgDetId]);
							unset($arrOtherGrandTotal['enc_count'][$enc_id]);
							$tempIsChgDetId2[$firstGrpId][$chgDetId]=$chgDetId;
						}
					}else{
						//CREDIT/DEBIT
						$patCrdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'];
						$insCrdDbt= $pay_crd_deb_arr[$chgDetId]['Insurance'];
		
						//PAYMENTS
						$patPaid+=$patPayDetArr[$chgDetId]["patPaid"] + $patCrdDbt;
						$insPaid+=$patPayDetArr[$chgDetId]["insPaid"] + $insCrdDbt;
						$totalPaid+=$mainEncounterPayArr[$chgDetId] + $patCrdDbt + $insCrdDbt;
						// PRI INSURANCE PAID
						if($patPayDetArr[$chgDetId]['priPaid']>0){
							$priPaid+= $patPayDetArr[$chgDetId]['priPaid'] + $insCrdDbt;
							$insCrdDbt=0;
						}
						// SEC+TER INSURANCE PAID
						$secPaid+= $patPayDetArr[$chgDetId]['secPaid'] + $patPayDetArr[$chgDetId]['terPaid'] + $insCrdDbt;

						//WRITE-OFF & ADJUSTEMENT
						$write_off_amt+= $normalWriteOffAmt[$chgDetId] + $writte_off_arr[$chgDetId];
						$adj_amt+= $arrAdjustmentAmt[$chgDetId];						
					}
					
					//CREDIT - OVER PAYMENT
					if($enc_detail_arr[$d]['over_payment']>0){
						$creditProcAmount+= $enc_detail_arr[$d]['over_payment'];
					}
										
					//BALANCE
					$balAmt=0;
					if($enc_detail_arr[$d]["proc_balance"]>0){
						$balAmt= $enc_detail_arr[$d]['proc_balance'];
					}else{
						if($enc_detail_arr[$d]['over_payment']>0){
							$balAmt= $enc_detail_arr[$d]['proc_balance'] - $enc_detail_arr[$d]['over_payment'];
						}else{
							$balAmt= $enc_detail_arr[$d]['proc_balance'];
						}
					}
					$totBalance+=$balAmt;
					
					$units_arr[] = $enc_detail_arr[$d]['units'];

					//GRAND TOTAL - TO AVOID DUPLICATE OF CHARGES, CREDIT AND BALANCE IN CASE OF GROUP BY INSURANCE CASE
					if(!$tempIsChgDetId[$chgDetId]){
						$grandTotalAmtArr["units"][] = $enc_detail_arr[$d]['units'];
						$grandTotalAmtArr["totalAmt"][]= $enc_detail_arr[$d]['totalAmt'];
						$grandTotalAmtArr["creditProcAmount"][]= $enc_detail_arr[$d]['over_payment'];
						$grandTotalAmtArr["insuranceDue"][]= $enc_detail_arr[$d]['pri_due'] + $enc_detail_arr[$d]['sec_due'] + $enc_detail_arr[$d]['tri_due'];
						$grandTotalAmtArr["patientDue"][]= $enc_detail_arr[$d]['pat_due'];
						$grandTotalAmtArr["totalBalance"][]= $balAmt;	
						$tempIsChgDetId[$chgDetId]=$chgDetId;
					}	

				}
	
				//if($DateRangeFor=='date_of_service'){
				//	$adj_amt= $arrAdjustmentAmt[$enc_id];				
				//}

				$pro_sub_amt_arr["totalBalance"][] = $totBalance;
				$patDue = ($patDue<=0) ? 0 : $patDue; 
				$pro_sub_amt_arr["patientDue"][] = $patDue;
				$pro_sub_amt_arr["insuranceDue"][] = $insDue;
				$pro_sub_amt_arr["patPaidAmt"][] = $patPaid;
				$pro_sub_amt_arr["insPaidAmt"][] = $insPaid;
				$pro_sub_amt_arr["priPaidAmt"][] = $priPaid;
				$pro_sub_amt_arr["secPaidAmt"][] = $secPaid;
				$pro_sub_amt_arr["patientPaidAmt"][] = $totalPaid;						
	
				$pro_sub_amt_arr["creditProcAmount"][] = $creditProcAmount;
				$pro_sub_amt_arr["adj_amt"][] = $adj_amt;
				$pro_sub_amt_arr["write_off"][] = $write_off_amt;
				
			} // END ENCOUNTERS
			
			//--- TOTAL AMOUNT UNDER SINGLE PROVIDER ----
			$totalAmt = array_sum($pro_sub_amt_arr["totalAmt"]);		
			$patientPaidAmt = NULL;
			if(count($pro_sub_amt_arr["patientPaidAmt"])>0){
				$patientPaidAmt = array_sum($pro_sub_amt_arr["patientPaidAmt"]);
			}
			
			$units = array_sum($units_arr);
			$encounters=count($encounter_arr);
			$insuranceDue = array_sum($pro_sub_amt_arr["insuranceDue"]);
			$patientDue = array_sum($pro_sub_amt_arr["patientDue"]);		
			$creditProcAmount = array_sum($pro_sub_amt_arr["creditProcAmount"]);
			$write_off_amt = array_sum($pro_sub_amt_arr["write_off"]);
			$adj_amt = array_sum($pro_sub_amt_arr["adj_amt"]);
			$totalBalance = array_sum($pro_sub_amt_arr["totalBalance"]);
			$patPaidAmt = array_sum($pro_sub_amt_arr["patPaidAmt"]);
			$insPaidAmt = array_sum($pro_sub_amt_arr["insPaidAmt"]);
			$priPaidAmt = array_sum($pro_sub_amt_arr["priPaidAmt"]);
			$secPaidAmt = array_sum($pro_sub_amt_arr["secPaidAmt"]);
	
			if($viewBy=='physician' || $viewBy=='operator'){
				$secGrpTitle='Facility';
				$secGrpName = $arrAllFacilities[$secGrpId];
			}else{
				$secGrpTitle='Physician';
				$secGrpName = $providerNameArr[$secGrpId];
			}

			//--- GET SUB TOTAL AMOUNT FOR ALL PROVIDER UNDER SINGLE FACILITY ---
			$sub_pro_total_arr["units"][] = $units;
			$sub_pro_total_arr["totalAmt"][] = $totalAmt;
			$sub_pro_total_arr["patientPaidAmt"][] = $patientPaidAmt;
			$sub_pro_total_arr["insuranceDue"][] = $insuranceDue;
			$sub_pro_total_arr["patientDue"][] = $patientDue;
			$sub_pro_total_arr["creditProcAmount"][] = $creditProcAmount;
			$sub_pro_total_arr["write_off_amt"][] = $write_off_amt;
			$sub_pro_total_arr["adj_amt"][] = $adj_amt;
			$sub_pro_total_arr["totalBalance"][] = $totalBalance;
			$sub_pro_total_arr["patPaidAmt"][] = $patPaidAmt;
			$sub_pro_total_arr["insPaidAmt"][] = $insPaidAmt;
			$sub_pro_total_arr["priPaidAmt"][] = $priPaidAmt;
			$sub_pro_total_arr["secPaidAmt"][] = $secPaidAmt;
			
			$ratioCols='';
			if($DateRangeFor=='date_of_service'){
				$subGrossRatio=round(($patientPaidAmt*100) / $totalAmt,2);
				$subNetRatio=round((($patientPaidAmt+$write_off_amt+$adj_amt)*100) / $totalAmt,2);
				$ratioCols='
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$subGrossRatio.'%</td>
				<td class="tempcol text_10" style="text-align:right; background:#FFFFFF;">'.$subNetRatio.'%</td>';
			}		
			
			$csvFileData.='
			<tr>
				<td class="text_10" style="background:#FFFFFF; width:100px">'.$secGrpName.'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$units.'/'.$encounters.'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($totalAmt,2).'</td>
				<td class="notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($patPaidAmt,2).'</td>
				<td class="tempInsPaid notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($insPaidAmt,2).'</td>
				<td class="tempcol notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($priPaidAmt,2).'</td>
				<td class="tempcol notInsGroupBy text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($secPaidAmt,2).'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($patientPaidAmt,2).'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($creditProcAmount,2).'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($write_off_amt,2).'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($adj_amt,2).'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($insuranceDue,2).'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($patientDue,2).'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($totalBalance,2).'</td>
			</tr>';
		}
		
		//--- DISPLAY SUB TOTAL DATA --------
		$subGrossRatio=$subNetRatio=0;
		$subTotalUnits = array_sum($sub_pro_total_arr["units"]);
		$subTotalEncs = count($sub_pro_total_arr["enc_count"]);
		$subTotalAmt = array_sum($sub_pro_total_arr["totalAmt"]);
		$subPatientPaidAmt = array_sum($sub_pro_total_arr["patientPaidAmt"]);
		$subInsuranceDue = array_sum($sub_pro_total_arr["insuranceDue"]);
		$subPatientDue = array_sum($sub_pro_total_arr["patientDue"]);
		$subCreditProcAmount = array_sum($sub_pro_total_arr["creditProcAmount"]);
		$subWrite_off_amt = array_sum($sub_pro_total_arr["write_off_amt"]);
		$sub_adj_amt = array_sum($sub_pro_total_arr["adj_amt"]);
		$subTotalBalance = array_sum($sub_pro_total_arr["totalBalance"]);
		$subPatPaidAmt = array_sum($sub_pro_total_arr["patPaidAmt"]);
		$subInsPaidAmt = array_sum($sub_pro_total_arr["insPaidAmt"]);
		$subPriPaidAmt = array_sum($sub_pro_total_arr["priPaidAmt"]);
		$subSecPaidAmt = array_sum($sub_pro_total_arr["secPaidAmt"]);
		$ratioCols='';
		if($DateRangeFor=='date_of_service'){
			$subGrossRatio=round(($subPatientPaidAmt*100) / $subTotalAmt,2);
			$subNetRatio=round((($subPatientPaidAmt+$subWrite_off_amt+$sub_adj_amt)*100) / $subTotalAmt,2);
			$ratioCols='
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subGrossRatio.'%</td>
			<td class="tempcol text_10b" style="text-align:right; background:#FFFFFF;">'.$subNetRatio.'%</td>';
		}
		
		//--- GET SUB TOTAL AMOUNT FOR ALL PROVIDER UNDER SINGLE FACILITY ---
		//$grandTotalAmtArr["units"][] = $subTotalUnits;
		//$grandTotalAmtArr["totalAmt"][] = $subTotalAmt;
		$grandTotalAmtArr["patientPaidAmt"][] = $subPatientPaidAmt;
		//$grandTotalAmtArr["insuranceDue"][] = $subInsuranceDue;
		//$grandTotalAmtArr["patientDue"][] = $subPatientDue;
		//$grandTotalAmtArr["creditProcAmount"][] = $subCreditProcAmount;
		$grandTotalAmtArr["write_off_amt"][] = $subWrite_off_amt;
		$grandTotalAmtArr["adj_amt"][] = $sub_adj_amt;
		//$grandTotalAmtArr["totalBalance"][] = $subTotalBalance;
		$grandTotalAmtArr["patPaidAmt"][] = $subPatPaidAmt;
		$grandTotalAmtArr["insPaidAmt"][] = $subInsPaidAmt;
		$grandTotalAmtArr["priPaidAmt"][] = $subPriPaidAmt;
		$grandTotalAmtArr["secPaidAmt"][] = $subSecPaidAmt;
	
		//--- NUMBER FORMAT FOR SUB TOTAL AMOUNTS ----
		$subTotalAmt = $CLSReports->numberFormat($subTotalAmt,2);
		$subPatientPaidAmt = $CLSReports->numberFormat($subPatientPaidAmt,2);
		$subInsuranceDue = $CLSReports->numberFormat($subInsuranceDue,2);
		$subPatientDue = $CLSReports->numberFormat($subPatientDue,2);		
		$subCreditProcAmount = $CLSReports->numberFormat($subCreditProcAmount,2);		
		$subWrite_off_amt = $CLSReports->numberFormat($subWrite_off_amt,2);
		$sub_adj_amt = $CLSReports->numberFormat($sub_adj_amt,2);
		$subTotalBalance = $CLSReports->numberFormat($subTotalBalance,2);
		$subPatPaidAmt = $CLSReports->numberFormat($subPatPaidAmt,2);
		$subInsPaidAmt = $CLSReports->numberFormat($subInsPaidAmt,2);
		$subPriPaidAmt = $CLSReports->numberFormat($subPriPaidAmt,2);
		$subSecPaidAmt = $CLSReports->numberFormat($subSecPaidAmt,2);
	
		$csvFileData.='
		<tr><td colspan="'.$cols.'" class="total-row"></td></tr>
		<tr>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$firstGrpTitle.' Total :</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subTotalUnits.'/'.$subTotalEncs.'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subTotalAmt.'</td>
			<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$subPatPaidAmt.'</td>
			<td class="tempInsPaid notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$subInsPaidAmt.'</td>
			<td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$subPriPaidAmt.'</td>
			<td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$subSecPaidAmt.'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subPatientPaidAmt.'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subCreditProcAmount.'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subWrite_off_amt.'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$sub_adj_amt.'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subInsuranceDue.'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subPatientDue.'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$subTotalBalance.'</td>
		</tr>
		<tr><td colspan="'.$cols.'" class="total-row"></td></tr>';
	}	

//--- GRAND TOTAL AMOUNT ----
$grand_total_units = array_sum($grandTotalAmtArr['units']);
$grand_total_encs = count($grandTotalAmtArr['enc_count']);
$grand_total_amt = array_sum($grandTotalAmtArr['totalAmt']);
$grand_pat_paid_amt = array_sum($grandTotalAmtArr['patientPaidAmt']);
$grand_ins_due = array_sum($grandTotalAmtArr['insuranceDue']);
$grand_patient_due = array_sum($grandTotalAmtArr['patientDue']);
$grand_credit_amt = array_sum($grandTotalAmtArr['creditProcAmount']);
$grand_write_off_amt = array_sum($grandTotalAmtArr['write_off_amt']);
$grand_adj_amt = array_sum($grandTotalAmtArr['adj_amt']);		
$grand_total_balance = array_sum($grandTotalAmtArr['totalBalance']);
$grand_patPaidAmt = array_sum($grandTotalAmtArr['patPaidAmt']);
$grand_insPaidAmt = array_sum($grandTotalAmtArr['insPaidAmt']);
$grand_priPaidAmt = array_sum($grandTotalAmtArr['priPaidAmt']);
$grand_secPaidAmt = array_sum($grandTotalAmtArr['secPaidAmt']);
$ratioCols='';

$grandTotalChargesAmt=$grand_total_amt + $chargesForNotPosted;
$grandTotalPaidAmt=$grand_pat_paid_amt;
$grandTotalAdjustmentAmt=$grand_write_off_amt + $grand_adj_amt;

if($DateRangeFor=='date_of_service'){
	$grandGrossRatio=round(($grand_pat_paid_amt*100) / $grand_total_amt,2);
	$grandNetRatio=round((($grand_pat_paid_amt+$grand_write_off_amt+$grand_adj_amt)*100) / $grand_total_amt,2);
	$ratioColsTitle='
    <td style="text-align:center; width:60px" class="text_b_w">Gross Coll. Ratio</td>
    <td style="text-align:center; width:60px" class="text_b_w">Net Coll. Ratio</td>';
	$ratioCols='
	<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$grandGrossRatio.'%</td>
	<td class="tempcol text_10b" style="text-align:right; background:#FFFFFF;" >'.$grandNetRatio.'%</td>';
	
	$ratioColsPDFTitle='
	<td style="text-align:right; width:50px" class="text_b_w">G. Coll. Ratio</td>
	<td class="tempcol text_b_w" style="text-align:right; width:60px" >Net Coll. Ratio</td>';
	$ratioColsPDF='
	<td style="height:1px; width:60px"></td>
	<td class="tempcol" style="height:1px; width:60px"></td>';
	
	
}

$grand_total_amt2= $grand_total_amt;
$grand_total_balance2= $grand_total_balance;

//--- CHANGE NUMBER FORMAT FOR GRAND TOTAL AMOUNT ---
/*$grand_total_amt = $CLSReports->numberFormat($grand_total_amt,2);
$grand_pat_paid_amt = $CLSReports->numberFormat($grand_pat_paid_amt,2);
$grand_ins_due = $CLSReports->numberFormat($grand_ins_due,2);
$grand_patient_due = $CLSReports->numberFormat($grand_patient_due,2);			
$grand_credit_amt = $CLSReports->numberFormat($grand_credit_amt,2);			
$grand_write_off_amt = $CLSReports->numberFormat($grand_write_off_amt,2);
$grand_adj_amt = $CLSReports->numberFormat($grand_adj_amt,2);
$grand_total_balance = $CLSReports->numberFormat($grand_total_balance,2);
$grand_patPaidAmt = $CLSReports->numberFormat($grand_patPaidAmt,2);
$grand_insPaidAmt = $CLSReports->numberFormat($grand_insPaidAmt,2);
$grand_priPaidAmt = $CLSReports->numberFormat($grand_priPaidAmt,2);
$grand_secPaidAmt = $CLSReports->numberFormat($grand_secPaidAmt,2);*/

//-- OPERATOR INITIAL -------
$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$opInitial = $authProviderNameArr[1][0];
$opInitial .= $authProviderNameArr[0][0];
$opInitial = strtoupper($opInitial);
$printFile = true;


//-----------DELETED CHARGES-------------
$del_csv = $del_csv_html = $del_pdf_html ='';
if(count($arrVoidPay)>0){
	$isData=true;
	$total_cols = 10;
	$phy_col =  "16";
	$w_cols = $w_cols1 = floor((100 - ($phy_col))/($total_cols-1));
	$phy_col = $phy_col1 = 100 - ( (($total_cols-1) * $w_cols));
	//$w_cols-=1;
	//$phy_col-=1;
	$w_cols = $w_cols."%";
	$phy_col = $phy_col."%";

	$fristGrpArr = array_keys($arrVoidPay);
	$arrDelTotal = array();
	foreach($fristGrpArr as $firstID){
		$arrFirstTotal = $arrPhyTotal = array();
		if($viewBy=='physician'){
			$firstTitle='Physician';
			$firstGrpName = $providerNameArr[$firstID];
		}else{
			$firstTitle='Facility';
			$firstGrpName = $arrAllFacilities[$firstID];
		}
		//$phyName = $providerNameArr[$phyID];
		$del_csv.=
			'<tr>
				<td class="text_b_w" align="left" colspan="'.$total_cols.'">'.$firstTitle.' : '.$firstGrpName.'</td>
			</tr>';

		foreach($arrVoidPay[$firstID] as $secID => $grpCharges){
			if($viewBy=='physician'){
				$secTitle='Facility';
				$secGrpName = $arrAllFacilities[$secID];
			}else{
				$secTitle='Physician';
				$secGrpName = $providerNameArr[$secID];
			}
			//$facName = $arrAllFacilities[$facID];
			//$delTotPhyChg += $grpCharges;
			$del_csv.='<tr>
				<td class="text_10" bgcolor="#FFFFFF" align="left">'.$secGrpName.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['charges'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['cash'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['pt_check'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['ins_check'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['CC'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['EFT'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['MO'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['VEEP'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['adjustments'],2).'</td>
			</tr>';
			$arrFirstTotal['charges'] += $grpCharges['charges'];
			$arrFirstTotal['cash'] += $grpCharges['cash'];
			$arrFirstTotal['pt_check'] += $grpCharges['pt_check'];
			$arrFirstTotal['ins_check'] += $grpCharges['ins_check'];
			$arrFirstTotal['CC'] += $grpCharges['CC'];
			$arrFirstTotal['EFT'] += $grpCharges['EFT'];
			$arrFirstTotal['MO'] += $grpCharges['MO'];
			$arrFirstTotal['VEEP'] += $grpCharges['VEEP'];
			$arrFirstTotal['adjustments'] += $grpCharges['adjustments'];
		}
		
		$del_csv.= '
			<tr><td class="total-row" colspan="'.$total_cols.'"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  align="right">'.$firstTitle.' Total: </td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['charges'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['cash'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['pt_check'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['ins_check'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['CC'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['EFT'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['MO'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['VEEP'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['adjustments'],2).'</td>
			</tr>
			<tr><td class="total-row" colspan="'.$total_cols.'"></td></tr>
			';
			$arrDelTotal['charges'] += $arrFirstTotal['charges'];
			$arrDelTotal['cash'] += $arrFirstTotal['cash'];
			$arrDelTotal['pt_check'] += $arrFirstTotal['pt_check'];
			$arrDelTotal['ins_check'] += $arrFirstTotal['ins_check'];
			$arrDelTotal['CC'] += $arrFirstTotal['CC'];
			$arrDelTotal['EFT'] += $arrFirstTotal['EFT'];
			$arrDelTotal['MO'] += $arrFirstTotal['MO'];
			$arrDelTotal['VEEP'] += $arrFirstTotal['VEEP'];
			$arrDelTotal['adjustments'] += $arrFirstTotal['adjustments'];
	}

	$totDelPayment=$arrDelTotal['cash'] + $arrDelTotal['pt_check']+$arrDelTotal['ins_check']+$arrDelTotal['CC']+$arrDelTotal['EFT']+$arrDelTotal['MO']+$arrDelTotal['VEEP'];
	$totalDeletedCharges=$arrDelTotal['charges'];
	$totalDeletedPayments=$totDelPayment;
	$totalDeletedAdjustments=$arrDelTotal['adjustments'];
	
$del_csv.= '<tr><td class="total-row" colspan="'.$total_cols.'"></td></tr>
			<tr class="text_12b">
			<td bgcolor="#FFFFFF"  align="right">Total: </td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['charges'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['cash'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['pt_check'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['ins_check'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['CC'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['EFT'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['MO'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['VEEP'],2).'</td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['adjustments'],2).'</td>
			</tr>
			<tr><td class="total-row" colspan="'.$total_cols.'"></td></tr>
			';
$del_csv .= '
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right" >Total Voided Payment: </td>
				<td bgcolor="#FFFFFF"  style="text-align:right" > </td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totDelPayment,2).'</td>
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="7"></td>
			</tr>
			<tr><td  colspan="'.$total_cols.'" class="total-row"></td></tr>
			';			

	$del_csv_html .='
	<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr id="heading_orange"><td colspan="'.$total_cols.'">Voided Records</td></tr>
		<tr>
			<td class="text_b_w" align="center" style="width:'.$phy_col.'">Facility</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">Charges</td>
			<td class="text_b_w" align="center" style="text-align:center;width:'.($w_cols1*7).'%" colspan="7">Payments</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">Write-off & Adj</td>
		</tr>
		<tr>
			<td class="text_b_w" align="center" style="width:'.$phy_col.'"></td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'"></td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">Cash</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">Pt Check</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">Ins Check</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">CC</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">EFT</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">MO</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'">VEEP</td>
			<td class="text_b_w" align="center" style="width:'.$w_cols.'"></td>
		</tr>
		'.
		$del_csv.'
	</table>';

}


$otherPayments=$otherWriteOff=$otherAdj=$otherCharges=$otherInsDue=$otherPatDue=$otherOverPayment=$otherBalance=0;
if($viewBy=='insurance'){
	$otherUnits= array_sum($arrOtherGrandTotal['units']);
	$otherEnc= count($arrOtherGrandTotal['enc_count']);
	$otherPayments= $arrOtherGrandTotal['payments'];
	$otherWriteOff= $arrOtherGrandTotal['write-off'];
	$otherAdj= $arrOtherGrandTotal['adj'];
	$otherCharges= array_sum($arrOtherGrandTotal['charges']);
	$otherInsDue= array_sum($arrOtherGrandTotal['ins_due']);
	$otherPatDue= array_sum($arrOtherGrandTotal['pat_due']);
	$otherOverPayment= array_sum($arrOtherGrandTotal['over_payment']);
	$otherBalance= array_sum($arrOtherGrandTotal['balance']);
}

$cico_prepay_exist=0;
if($inc_ci_co_prepay==1){
	//UNAPPLIED CI/CO AMOUNTS
	if(sizeof($arrCICONotApplied)>0){
		$tot_cico=0;
		$cico_prepay_exist=1;
		foreach($arrCICONotApplied as $firstGrpId => $firstGrpData){
			$subtot_cico=0;
			if($viewBy=='physician' || $viewBy=='operator'  || $viewBy=='groups' || $viewBy=='department' || $viewBy=='procedure' || $viewBy=='ins_group'){
				$firstGrpTitle=($viewBy=='physician')? 'Physician' : 'Operator';
				$firstGrpName = $providerNameArr[$firstGrpId];
			}else{
				$firstGrpTitle='Facility';
				$firstGrpName = $arrAllFacilities[$firstGrpId];
			}
		
			$cico_data.='<tr><td class="text_b_w" colspan="3">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
			
			foreach($firstGrpData as $secGrpId => $secGrpData){
	
				if($viewBy=='facility'){
					$secGrpTitle='Facility';
					$secGrpName = $providerNameArr[$secGrpId];
				}else{
					$secGrpTitle=($viewBy=='operator')? 'Operator' : 'Physician';
					$secGrpName = $arrAllFacilities[$secGrpId];
				}			
	
				$subtot_cico+=$secGrpData['amount'];
				
				//POPUP TITLES
				$refund_title=($secGrpData['ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$secGrpData['ref_amt'].' Refund"':'"';			
				
				$cico_data.='
				<tr>
					<td class="text_10" style="background:#FFFFFF;">'.$secGrpName.'</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;" '.$refund_title.'>'.$CLSReports->numberFormat($secGrpData['amount'],2).'</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;"></td>
				</tr>';
				
			}
	
			$tot_cico+=$subtot_cico;
			$cico_data.='
			<tr>
				<td class="text_10b" style="background:#FFFFFF; text-align:right">'.$secGrpTitle.' Total:</td>
				<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($subtot_cico,2).'</td>
				<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
			</tr>';
		}
	
		$cico_html.='
		<table  style="width:100%" class="rpt_table rpt_table-bordered">
		<tr id="heading_orange"><td colspan="3">Unapplied CI/CO Payments</td></tr>
		<tr>
			<td style="text-align:center; width:300px;" class="text_b_w" width="200px">'.$colTitle.'</td>
			<td style="text-align:center; width:200px" class="text_b_w" width="150px">Payments</td>
			<td style="text-align:center; width:auto;" class="text_b_w" width="auto"></td>
		</tr>
		'.$cico_data.'	
		<tr>
			<td class="text_10b" style="background:#FFFFFF; text-align:right">CI/CO Total:</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($tot_cico,2).'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		</tr>
		</table>';
	}
	
	//UNAPPLIED PRE-PAYMENTS AMOUNTS
	if(sizeof($arrPrePayNotApplied)>0){
		$tot_prepay=0;
		$cico_prepay_exist=1;
		foreach($arrPrePayNotApplied as $firstGrpId => $firstGrpData){
			$subtot_prepay=0;
			if($viewBy=='physician' || $viewBy=='operator'  || $viewBy=='groups' || $viewBy=='department' || $viewBy=='procedure' || $viewBy=='ins_group'){
				$firstGrpTitle=($viewBy=='physician')? 'Physician' : 'Operator';
				$firstGrpName = $providerNameArr[$firstGrpId];
			}else{
				$firstGrpTitle='Facility';
				$firstGrpName = $arrAllFacilities[$firstGrpId];
			}
		
			$prepay_data.='<tr><td class="text_b_w" colspan="3">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
			
			foreach($firstGrpData as $secGrpId => $secGrpData){
	
				if($viewBy=='facility'){
					$secGrpTitle='Facility';
					$secGrpName = $providerNameArr[$secGrpId];
				}else{
					$secGrpTitle=($viewBy=='operator')? 'Operator' : 'Physician';
					$secGrpName = $arrAllFacilities[$secGrpId];
				}
					
				$subtot_prepay+=$secGrpData['amount'];
				
				//POPUP TITLES
				$refund_title=($secGrpData['ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$secGrpData['ref_amt'].' Refund"':'"';			
				
				$prepay_data.='
				<tr>
					<td class="text_10" style="background:#FFFFFF;">'.$secGrpName.'</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;" '.$refund_title.'>'.$CLSReports->numberFormat($secGrpData['amount'],2).'</td>
					<td class="text_10" style="text-align:right; background:#FFFFFF;"></td>
				</tr>';
				
			}
	
			$tot_prepay+=$subtot_prepay;
			$prepay_data.='
			<tr>
				<td class="text_10b" style="background:#FFFFFF; text-align:right">'.$secGrpTitle.' Total:</td>
				<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($subtot_prepay,2).'</td>
				<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
			</tr>';
		}
	
		$prepay_html.='
		<table  style="width:100%" class="rpt_table rpt_table-bordered">
		<tr id="heading_orange"><td colspan="3">Unapplied Pre-payments</td></tr>
		<tr>
			<td style="text-align:center; width:300px;" class="text_b_w" width="200px">'.$colTitle.'</td>
			<td style="text-align:center; width:200px" class="text_b_w" width="150px">Payments</td>
			<td style="text-align:center; width:auto;" class="text_b_w" width="auto"></td>
		</tr>
		'.$prepay_data.'	
		<tr>
			<td class="text_10b" style="background:#FFFFFF;text-align:right">Pre-payment Total:</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($tot_prepay,2).'</td>
			<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		</tr>
		</table>';
	}
}

//GRAND TOTAL IF NORMAL AND DELETED AVAILABLE
if(sizeof($mainResArr)>0 && (empty($del_csv_html)==false || $cico_prepay_exist==1)){
	$grandTotalChargesAmt+=$otherCharges;
	$grandTotalPaidAmt+=$otherPayments;
	$grandTotalAdjustmentAmt+=$otherWriteOff+$otherAdj;
	
	$grandCharges=$grandTotalChargesAmt-$totalDeletedCharges;
	$grandPayments=($grandTotalPaidAmt+$tot_cico+$tot_prepay)-$totalDeletedPayments;
	$grandAdjustments=$grandTotalAdjustmentAmt-$totalDeletedAdjustments;
	
	$grand_totals_csv= '
	<table style="width:100%" class="rpt_table rpt_table-bordered">
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
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalChargesAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalPaidAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAdjustmentAmt,2).'</td>
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
		<td bgcolor="#FFFFFF"  style="text-align:right"></td>
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
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalChargesAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalPaidAmt,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAdjustmentAmt,2).'</td>
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

}

$totalLabel='Total';
$showTotalAgain=0;
$total_charges=$grand_total_amt2;
$total_balance=$grand_total_balance2;

if($chargesForNotPosted>0){
	$showTotalAgain=1;
	$totalLabel='Sub Total';

	$total_charges+=$chargesForNotPosted;
	$total_balance+=$chargesForNotPosted;

	$notPosted='
	<tr><td colspan="'.$cols.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Not Posted Amounts :</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($chargesForNotPosted,2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="tempInsPaid notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b"></td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($chargesForNotPosted,2).'</td>
	</tr>
	<tr><td colspan="'.$cols.'" class="total-row"></td></tr>';
}	

//IF GROUP BY INSURANCE THEN SHOW OTHER AMOUNTS RATHER THAN INSURANCE
$otherTotals='';
if($viewBy=='insurance'){
	$showTotalAgain=1;
	$totalLabel='Sub Total';

	$otherRatioCols='';
	if($DateRangeFor=='date_of_service'){
		$otherGrossRatio=round(($otherPayments*100) / $otherCharges,2);
		$otherNetRatio=round((($otherPayments+$otherWriteOff+$otherAdj)*100) / $otherCharges,2);
		$otherRatioCols='
		<td class="text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="tempcol text_10b" style="text-align:right; background:#FFFFFF;"></td>';
	}	

	$otherTotals='
	<tr><td colspan="'.$cols.'" class="total-row"></td></tr>
	<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Other Totals :</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$otherUnits.'/'.$otherEnc.'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherCharges,2).'</td>
		<td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="tempInsPaid notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"></td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($otherPayments,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($otherOverPayment,2).'</td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($otherWriteOff,2).'</td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($otherAdj,2).'</td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($otherInsDue,2).'</td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($otherPatDue,2).'</td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($otherBalance,2).'</td>
	</tr>
	<tr><td colspan="'.$cols.'" class="total-row"></td></tr>';	
}

if($showTotalAgain==1){
	
	$ratioCols2='';
	if($DateRangeFor=='date_of_service'){
		$totGrossRatio=round((($grand_pat_paid_amt + $otherPayments)*100) / ($total_charges + $otherCharges),2);
		$totNetRatio=round((($grand_pat_paid_amt+$otherPayments+$grand_write_off_amt+$otherWriteOff+$grand_adj_amt+$otherAdj)*100) / ($total_charges + $otherCharges),2);
		$ratioCols2='
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$totGrossRatio.'%</td>
		<td class="tempcol text_10b" style="text-align:right; background:#FFFFFF;">'.$totNetRatio.'%</td>';
	}

	$sumOfUnits=$grand_total_units+$otherUnits;
	$sumOfEncs=$grand_total_encs+$otherEnc;
			
	$totalsRow=
	$notPosted
	.$otherTotals
	.'<tr>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">Total :</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$sumOfUnits.'/'.$sumOfEncs.'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($total_charges + $otherCharges,2).'</td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grand_patPaidAmt,2).'</td>
        <td class="tempInsPaid notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grand_insPaidAmt,2).'</td>
		<td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grand_priPaidAmt,2).'</td>
		<td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($grand_secPaidAmt,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_pat_paid_amt + $otherPayments,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_credit_amt + $otherOverPayment,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_write_off_amt + $otherWriteOff ,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_adj_amt + $otherAdj,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_ins_due + $otherInsDue,2).'</td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($grand_patient_due + $otherPatDue,2).'</td>
		<td style="text-align:right; background:#FFFFFF;" class="text_10b">'.$CLSReports->numberFormat($total_balance + $otherBalance,2).'</td>
	</tr>
	<tr><td colspan="'.$cols.'" class="total-row"></td></tr>';	
}


$show_appt = $inc_appt_detail ? true : false;
if( $show_appt )
{
	if( is_array( $apptSummaryArr) && count( $apptSummaryArr) > 0 )
	{
		$isData=true;
		$appt_summary_data .= '
		<tr id="heading_orange"><td colspan="2">&nbsp;<b>Appointment Summary</b></td></tr>
		<tr>
			<td class="text_b_w" style="width:15%;">Facility Name</td>
			<td class="text_b_w text-center" style="width:15%;">No. of Appointments</td>
		</tr>';
		
		foreach($apptSummaryArr as $tmpPhysicianId => $apptSummaryData)
		{
			if( is_array( $apptSummaryData) && count( $apptSummaryData) > 0 )
			{		
				$appt_summary_data .= '<tr><td class="text_b_w" colspan="6">Physician Name: '.$providerNameArr[$tmpPhysicianId].'</td></tr>';
				
				foreach( $apptSummaryData as $apptFacilityId => $apptSummaryCount)
				{
					;
					$appt_summary_data .= '
					<tr>
						<td class="text_10">'.$arrAllFacilities[$apptFacilityId].'&nbsp;</td>
						<td class="text_10 text-center">'.(int) $apptSummaryCount.'</td>
					</tr>';		
				}
			}
		}
	}
}



if($isData==true){
?>

<table class="rpt_table rpt_table-bordered rpt_padding">
    <tr class="rpt_headers">
		<td class="rptbx1" style="text-align:left;" width="33%"><?php print "$dbtemp_name"; ?> (Summary)</td>
        <td class="rptbx2" style="text-align:left;" width="34%"><?php print "$dayReport ($search) From : $Sdate To : $Edate"; ?></td>
        <td class="rptbx3" style="text-align:left;" width="33%"><?php print "Created by $opInitial on $curDate"; ?></td>
    </tr>
    <tr class="rpt_headers">
    	<td class="rptbx1">Group : <?php echo $selgroup; ?></td>
        <td class="rptbx2">Facility : <?php  echo $selFac; ?></td>
        <td class="rptbx3">Filing Phy. : <?php echo $selPhy; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Oper.: <?php echo $selOpr; ?>        
        </td>
    </tr>
    <tr class="rpt_headers">
    	<td class="rptbx1">Credit Phy. : <?php echo $selCrPhy; ?></td>
        <td class="rptbx2">Insurance : <?php  echo $selInsurance; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	        Ins. Type : <?php  echo $selInsType; ?>
        </td>
        <td class="rptbx3">CPT : <?php echo $selCPT; ?></td>
    </tr>
    <tr class="rpt_headers">
        <td class="rptbx1">ICD9 : <?php echo $selDX; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        ICD10 : <?php echo $selDX10; ?>
        </td>
        <td class="rptbx2">Modifiers : <?php  echo $selModifiers; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       		Adj. Code : <?php  echo $selAdjCode; ?>
        </td>
        <td class="rptbx3">Write-off Code : <?php echo $selWriteoff; ?></td>
    </tr>
</table>
<?php if(count($providerIdArr)>0){?>
<table class="rpt_table rpt_table-bordered">
    <tr>
        <td style="text-align:center; width:auto;" class="text_b_w"><?php echo $colTitle;?></td>
		<td style="text-align:center; width:40px" class="text_b_w">Proc./Enc</td>
        <td style="text-align:center; width:100px" class="text_b_w">Total Charges</td>
		<td class="notInsGroupBy text_b_w" style="text-align:center; width:100px">Pat. Paid</td>
        <td class="tempInsPaid notInsGroupBy text_b_w" style="text-align:center; width:100px">Ins. Paid</td>
        <td class="tempcol notInsGroupBy text_b_w" style="text-align:center; width:100px" >Pri. Paid</td>
        <td class="tempcol notInsGroupBy text_b_w" style="text-align:center; width:100px" >Sec. Paid</td>
        <td style="text-align:center; width:100px" class="text_b_w">Total Payments</td>
        <td style="text-align:center; width:100px" class="text_b_w">Credit</td>
        <td style="text-align:center; width:100px" class="text_b_w">Write-Off</td>
        <td style="text-align:center; width:100px" class="text_b_w">Adjustment</td>
        <td style="text-align:center; width:100px" class="text_b_w">Ins. Due</td>
        <td style="text-align:center; width:100px" class="text_b_w">Pat. Due</td>
        <td style="text-align:center; width:100px" class="text_b_w">Balance</td>
    </tr>
    <?php print $csvFileData; ?>    
    <tr>
        <td colspan="<?php echo $cols;?>" class="total-row"></td>
    </tr>
    <tr>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php echo $totalLabel;?> : </td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $grand_total_units.'/'.$grand_total_encs; ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grand_total_amt,2); ?></td>
        <td class="notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grand_patPaidAmt,2); ?></td>
        <td class="tempInsPaid notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grand_insPaidAmt,2); ?></td>
        <td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grand_priPaidAmt,2); ?></td>
        <td class="tempcol notInsGroupBy text_10b" style="text-align:right; background:#FFFFFF;"><?php print $CLSReports->numberFormat($grand_secPaidAmt,2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grand_pat_paid_amt,2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grand_credit_amt,2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grand_write_off_amt,2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grand_adj_amt,2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grand_ins_due,2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grand_patient_due,2); ?></td>
        <td style="text-align:right; background:#FFFFFF;" class="text_10b"><?php print $CLSReports->numberFormat($grand_total_balance,2); ?></td>
    </tr>	

    <tr>
        <td colspan="<?php echo $cols;?>" class="total-row"></td>
    </tr>
    <?php echo $totalsRow;?>
</table>
<table class="rpt rpt_table rpt_table-bordered" width="100%">
	<?php print $appt_summary_data; ?>
</table>
<?php } 
}?>
<?php
echo $cico_html;
echo $prepay_html;
echo $del_csv_html;
echo $del_paid_csv;
echo $grand_totals_csv;

$csv_file_data = ob_get_contents();
ob_end_clean();

$csv_file_data =  preg_replace('#<td class="tempInsPaid(.*?)</td>#', '', $csv_file_data);
if($viewBy=='insurance'){
	$csv_file_data =  preg_replace('#<td class="notInsGroupBy(.*?)</td>#', '', $csv_file_data);
	$csv_file_data =  preg_replace('#<td class="tempInsPaid notInsGroupBy(.*?)</td>#', '', $csv_file_data);
	$csv_file_data =  preg_replace('#<td class="tempcol notInsGroupBy(.*?)</td>#', '', $csv_file_data);
}

//--- GET PDF FILE DATA ---
ob_start();

if($isData==true){
?>
<page backtop="23mm" backbottom="5mm">
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
			<td align="left" class="rptbx1" width="350"><?php print "$dbtemp_name"; ?> (Summary)</td>	
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
<?php if(count($providerIdArr)>0){ ?>    
    <table width="100%" cellpadding="1" cellspacing="1" border="0"  bgcolor="#FFF3E8">        
        <tr>
            <td style="text-align:center; width:100px" class="text_b_w"><?php echo $colTitle;?></td>
            <td style="text-align:center; width:86px" class="text_b_w">Proc./Enc</td>
            <td style="text-align:center; width:86px" class="text_b_w">Total Charges</td>
			<td class="notInsGroupBy text_b_w" style="text-align:center; width:86px">Pat. Paid</td>
            <td class="notInsGroupBy text_b_w" style="text-align:center; width:86px">Ins. Paid</td>
            <td style="text-align:center; width:86px" class="text_b_w">Total Payments</td>
    		<td style="text-align:right; width:86px" class="text_b_w">Credit</td>
            <td style="text-align:right; width:86px" class="text_b_w">Write-Off</td>
            <td style="text-align:right; width:86px" class="text_b_w">Adjustment</td>
            <td style="text-align:right; width:80px" class="text_b_w">Ins. Due</td>
            <td style="text-align:right; width:80px" class="text_b_w">Pat. Due</td>
            <td style="text-align:right; width:80px" class="text_b_w">Balance</td>
        </tr>
    </table>
</page_header>
	<table width="100%" cellpadding="0" cellspacing="0" border="0"  bgcolor="#FFF3E8">        
        <tr>
            <td style="height:1px;width:100px"></td>
			<td style="height:1px; width:86px"></td>
            <td style="height:1px; width:86px"></td>
            <td class="notInsGroupBy" style="height:1px; width:86px"></td>
            <td class="notInsGroupBy" style="height:1px; width:86px"></td>
			<td style="height:1px; width:86px"></td>
            <td style="height:1px; width:86px"></td>
            <td style="height:1px; width:86px"></td>
            <td style="height:1px; width:86px"></td>
            <td style="height:1px; width:86px"></td>
            <td style="height:1px; width:86px"></td>
            <td style="height:1px; width:86px"></td>
        </tr>
        <?php print $csvFileData; ?>
        <tr>
            <td colspan="<?php echo $cols;?>" class="total-row"></td>
        </tr>
        <tr>
            <td style="text-align:right;" class="text_10b"><?php echo $totalLabel;?> : </td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_total_units.'/'.$grand_total_encs; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_total_amt; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_patPaidAmt; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_insPaidAmt; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_pat_paid_amt; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_credit_amt; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_write_off_amt; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_adj_amt; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_ins_due; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_patient_due; ?></td>
            <td style="text-align:right;" class="text_10b"><?php print $grand_total_balance; ?></td>
        </tr>	
        <tr>
            <td colspan="<?php echo $cols;?>" class="total-row"></td>
        </tr>
        <?php  echo $totalsRow;?>
    </table>
</page>
<?php } 
}?>
<?php
echo $del_pdf_html;
echo $del_paid_pdf;
echo $del_csv_html;
echo $grand_totals_pdf;

$pdf_page_content = ob_get_contents();

//===============NET/GROSS => NET COLUMN FROM REMOVED FROM PDF PRINTING ONLY==============
$pdf_page_content =  preg_replace('#<td class="tempcol(.*?)</td>#', '', $pdf_page_content);
if($viewBy=='insurance'){
	$pdf_page_content =  preg_replace('#<td class="notInsGroupBy(.*?)</td>#', '', $pdf_page_content);
	$pdf_page_content =  preg_replace('#<td class="tempInsPaid notInsGroupBy(.*?)</td>#', '', $pdf_page_content);
	$pdf_page_content =  preg_replace('#<td class="tempcol notInsGroupBy(.*?)</td>#', '', $pdf_page_content);
}

ob_end_clean();
?>