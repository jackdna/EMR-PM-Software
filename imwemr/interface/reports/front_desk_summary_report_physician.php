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
FILE : FRONTDESKSUMMARYREPORTPHISICIAN.PHP
PURPOSE :  PAYMENTS AND POSTED CHARGES SUMMARY REPORT
ACCESS TYPE : INCLUDED
*/
function sortByOrder($a, $b) {
    if ($a['facCharges'] == $b['facCharges']) return 0;
	 return ($a['facCharges'] < $b['facCharges']) ? -1 : 1;
}

$grdtotalPaymentCount = 0;
$grdtotalPatientCash = 0;
$grdtotalPatientCheck = 0;
$grdtotalInsCheck = 0;
$grdtotalPatientCredit = 0;
$grdtotalPatientEFT = 0;
$grdtotalPatientMoneyOrder = 0;
$grdtotalPostedTotalAmt = 0;
$grdtotalPostCnt = 0;
$paidAmount = 0;

//--POSTED CHARGES AND PAYMENTS
if(sizeof($mainArr)>0){
$pdfData2 = NULL;
	foreach($mainArr as $firstGrpId => $secGrpArr){	
		$subQty ='';
		$subChargesTotal=0;
		$subCashTotal = 0;
		$subCheckTotal = 0;
		$subCCTotal = 0;
		$sub_adj_amt_arr = array();
		$pat_id_arr = array();		
	
		$firstGrpName = $userNameArr[$firstGrpId];
		if($firstGrpId=='') { $firstGrpName = 'No Operator';  }

		$pdfData2.='<tr><td class="text_b_w" style="text-align:left" colspan="9">Operator : '.$firstGrpName.'</td></tr>';
	
		foreach($secGrpArr as $secGrpId => $arrEncounters){
			$facilityId = $facilityIdKeys[$f];
			$facilityName = $posFacilityArr[$facilityId];
	
			$facCharges='';
			$facQty= '';
			$facCashTotal= '';
			$facCheckTotal= '';
			$facCCTotal = '';
			$facEftTotal= '';
			$facMoneyOrderTotal= '';
	
			$secGrpTitle='Physician';
			$secGrpName=$userNameArr[$secGrpId];
			if($groupBy=='facility'){
				$secGrpTitle='Facility';
				$secGrpName=$posFacilityArr[$secGrpId];
			}else if($groupBy=='department'){
				$secGrpTitle='Department';
				$secGrpName=$arrDepartments[$secGrpId];
			}
	
			if($orderByTemp=='Charges')
			{	
				$col++;
				$orderByTempArr[$col]['title']='<tr><td class="text_b_w" style="text-align:left" colspan="9">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
			}
			else
			$pdfData2.='<tr><td class="text_b_w" style="text-align:left" colspan="9">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
			
			foreach($arrEncounters as $encounterId){
				$charges='';
				$patientName='';
				$cashPaymentArr = array();
				$checkPaymentArr = array();
				$CCPaymentArr = array();
				$operatorNameArr = array();
				$eftPaymentArr=array();
				$moneyOrderPaymentArr=array();	
				$cashPayment = $checkPatPayment =$checkInsPayment= $CCPayment = $eftPayment =	$moneyOrderPayment = $adjAmt='';				
				
				//CHARGES
				if($mainPostedEncounterArr[$firstGrpId][$secGrpId][$encounterId]){
					foreach($mainPostedEncounterArr[$firstGrpId][$secGrpId][$encounterId] as $chgDetId => $detailsArr){
						$phyId = $detailsArr['primaryProviderId'];
						$facId = $detailsArr['facility_id'];
						$charges+=$detailsArr['totalAmount'];
					}
				}
				
				//PAYMENTS
				if($mainPayment_Arr[$encounterId][$firstGrpId]){
					$charge_listArr = $mainPaymentArr[$encounterId]['charge_list'];
					$pat_name_arr = array();
					if(empty($patientName)==true){
						$pat_name_arr['LAST_NAME'] = $charge_listArr['lname'];
						$pat_name_arr['FIRST_NAME'] = $charge_listArr['fname'];
						$pat_name_arr['MIDDLE_NAME'] = $charge_listArr['mname'];
						$patientName = changeNameFormat($pat_name_arr);
						$patientName.=' - '.$charge_listArr['id'];
					}
					$date_of_service = $charge_listArr['date_of_service'];
					$phyId = $charge_listArr['primaryProviderId'];
					$facId = $charge_listArr['facility_id'];
					
					//--- PAYMENT DETAILS -------
					$payment_mode = array();
					$payment_cnt = count($mainPayment_Arr[$encounterId][$firstGrpId]);
					$cashPaymentArr = array();
					$checkPaymentArr = array();
					$CCPaymentArr = array();
					$operatorNameArr = array();
					$eftPaymentArr=array();
					$moneyOrderPaymentArr=array();
					
					foreach($mainPayment_Arr[$encounterId][$firstGrpId] as $sno => $paymentArr){					
						$paymentmode = strtolower(trim($paymentArr['payment_mode']));
						$paidBy = strtolower($paymentArr['paid_by']);
						
						//--- TOTAL PAID AMOUNT --
						$paidForProc = str_replace(',','',$paymentArr['paidForProc']);
						$paidForProc += str_replace(',','',$paymentArr['overPayment']);
						if($paymentArr['paymentClaims'] == 'Negative Payment'){
							$paidForProc = '-'.$paidForProc;
						}

						//--- GET GRAND TOTAL AMOUNT ---
						$grandTotalPayment[] = $paidForProc;
						
						if($paymentmode == 'cash'){
							$cashPaymentArr[] = $paidForProc;
						}
						else if($paymentmode == 'check'){
							$checkPaymentArr[$paidBy][] = $paidForProc;
						}
						else if($paymentmode=='eft')
						{
							$eftPaymentArr[]=$paidForProc;
						}
						else if($paymentmode== 'money order')
						{
							$moneyOrderPaymentArr[] = $paidForProc;
						}
						else{
							$CCPaymentArr[] = $paidForProc;
						}
					}
					
					$cashPayment = array_sum($cashPaymentArr);
					$checkPatPayment = array_sum($checkPaymentArr['patient']);
					$checkInsPayment = array_sum($checkPaymentArr['insurance']);
					$CCPayment = array_sum($CCPaymentArr);
					$eftPayment = array_sum($eftPaymentArr);
					$moneyOrderPayment = array_sum($moneyOrderPaymentArr);
			
				
					// FACILITY TOTALS
					$facQty += $payment_cnt;
					$facCashTotal += $cashPayment;
					$facCheckPatTotal += $checkPatPayment;
					$facCheckInsTotal += $checkInsPayment;
					$facCCTotal += $CCPayment;
					$facEftTotal += $eftPayment;
					$facMoneyOrderTotal += $moneyOrderPayment;
					$facAdjAmt += $adjAmt;
					
					$subQty+= $payment_cnt;
					$subCashTotal += $cashPayment;
					$subCheckPatTotal += $checkPatPayment;
					$subCheckInsTotal += $checkInsPayment;
					$subCCTotal += $CCPayment;
					$subEftTotal += $eftPayment;
					$subMoneyOrderTotal += $moneyOrderPayment;
					
				}
				
				//CREDIT/DEBIT IF ANY
				if($pay_crd_deb_arr[$firstGrpId][$encounterId]){
					$charge_listArr = $mainPaymentArr[$encounterId]['charge_list'];
					$pat_name_arr = array();
					if(empty($patientName)==true){
						$pat_name_arr['LAST_NAME'] = $charge_listArr['lname'];
						$pat_name_arr['FIRST_NAME'] = $charge_listArr['fname'];
						$pat_name_arr['MIDDLE_NAME'] = $charge_listArr['mname'];
						$patientName = changeNameFormat($pat_name_arr);
						$patientName.=' - '.$charge_listArr['id'];
					}
					$date_of_service = $charge_listArr['date_of_service'];
					$phyId = $charge_listArr['primaryProviderId'];
					$facId = $charge_listArr['facility_id'];
					
					$crdDbtAmt = array_sum($pay_crd_deb_arr[$firstGrpId][$encounterId]);
					$subCashTotal+= $crdDbtAmt;
					$grandTotalPayment[] = $crdDbtAmt;
				}	


				$firstColTitle='Facility';
				$firstColName=$posFacilityArr[$facId];
				if($groupBy=='facility'){
					$firstColTitle='Physician';
					$firstColName=$userNameArr[$phyId];
				}else if($groupBy=='department'){
					$firstColTitle='Physician';
					$firstColName=$userNameArr[$phyId];
				}	

				$facCharges+= $charges;
				$subChargesTotal+= $charges;
			}

						
			// SECOND GROUP TOTALS
			
			$facCashTotal = numberFormat($facCashTotal, 2);
			$facCheckPatTotal = numberFormat($facCheckPatTotal, 2);
			$facCheckInsTotal = numberFormat($facCheckInsTotal, 2);
			$facCCTotal = numberFormat($facCCTotal, 2);
			$facEftTotal= numberFormat($facEftTotal,2);
			$facMoneyOrderTotal= numberFormat($facMoneyOrderTotal,2);
			$facAdjAmt = numberFormat($facAdjAmt, 2);
	
			if($orderByTemp=='Charges')
			{	
				$orderByTempArr[$col]["firstColName"]=$firstColName;
				$orderByTempArr[$col]["facCharges"]=$facCharges;
				$orderByTempArr[$col]["facQty"]=$facQty;
				$orderByTempArr[$col]["facCashTotal"]=$facCashTotal;
				$orderByTempArr[$col]["facCheckPatTotal"]=$facCheckPatTotal;
				$orderByTempArr[$col]["facCheckInsTotal"]=$facCheckInsTotal;
				$orderByTempArr[$col]["facCCTotal"]=$facCCTotal;
				$orderByTempArr[$col]["facEftTotal"]=$facEftTotal;
				$orderByTempArr[$col]["facMoneyOrderTotal"]=$facMoneyOrderTotal;
				
				
			}
			else
			{
				$facCharges = numberFormat($facCharges, 2);
				$pdfData2.= '
				<tr>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:left; width:116px">'.$firstColName.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$facCharges.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$facQty.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$facCashTotal.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$facCheckPatTotal.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$facCheckInsTotal.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$facCCTotal.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$facEftTotal.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:110px">'.$facMoneyOrderTotal.'</td>
				</tr>';
			}
			
		}
		
		if($orderByTemp=='Charges')
		{
			usort($orderByTempArr,'sortByOrder');
							
			for($i=0;$i<=sizeof($orderByTempArr)-1;$i++)
			{
				$pdfData2.= $orderByTempArr[$i]["title"];
				
				$facCharges = numberFormat($orderByTempArr[$i]["facCharges"], 2);
				$pdfData2.= '
				<tr>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:left; width:116px">'.$orderByTempArr[$i]["firstColName"].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$facCharges.'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$orderByTempArr[$i]["facQty"].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$orderByTempArr[$i]["facCashTotal"].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$orderByTempArr[$i]["facCheckPatTotal"].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$orderByTempArr[$i]["facCheckInsTotal"].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$orderByTempArr[$i]["facCCTotal"].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:116px">'.$orderByTempArr[$i]["facEftTotal"].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right; width:110px">'.$orderByTempArr[$i]["facMoneyOrderTotal"].'</td>
				</tr>';
			}
		}
		
		
		//FIRST GROUP TOTALS
		$pat_id_count = count($pat_id_arr);		
		$subCashTotal2 = $subCashTotal + $subCheckPatTotal + $subCheckInsTotal + $subCCTotal + $subEftTotal + $subMoneyOrderTotal;
		//$sub_adj_amt = array_sum($sub_adj_amt_arr);

		$arrMainTotal['charges']+=	$subChargesTotal;
		$arrMainTotal['qty']+=	$subQty;
		$arrMainTotal['cash']+=	$subCashTotal;
		$arrMainTotal['check_pat']+= $subCheckPatTotal;
		$arrMainTotal['check_ins']+= $subCheckInsTotal;
		$arrMainTotal['cc']+= $subCCTotal;
		$arrMainTotal['eft']+=	$subEftTotal;
		$arrMainTotal['money_order']+= $subMoneyOrderTotal;
			
		$subChargesTotal = numberFormat($subChargesTotal, 2);
		$subCashTotal = numberFormat($subCashTotal, 2);
		$subCheckPatTotal = numberFormat($subCheckPatTotal, 2);
		$subCheckInsTotal = numberFormat($subCheckInsTotal, 2);
		$subCCTotal = numberFormat($subCCTotal, 2);
		$subCashTotal2 = numberFormat($subCashTotal2, 2);
		$subEftTotal= numberFormat($subEftTotal,2);
		$subMoneyOrderTotal= numberFormat($subMoneyOrderTotal,2);
		//$sub_adj_amt = numberFormat($sub_adj_amt, 2);

		$pdfData2.= <<<DATA
		<tr><td class="total-row" colspan="9"></td></tr>
		<tr>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">Operator Total:</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subChargesTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subQty</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subCashTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subCheckPatTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subCheckInsTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subCCTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subEftTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subMoneyOrderTotal</td>
		</tr>
		<tr><td class="total-row" colspan="9"></td></tr>
DATA;
		
		}	

		//MAIN TOTAL
		$footerTotalData = NULL;
		//--- GRNAD TOTAL PAYMENTS OF ALL PROVIDERS ----
		$TotalPayments = array_sum($grandTotalPayment);
		$TotalPayments = numberFormat($TotalPayments,2);

		//--- PDF FILE DATA ----
		$pdfData .='
		<page backtop="20mm" backbottom="9mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>'.
		$fileHeaderData.'
		<table width="100%" border="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr id="heading_orange">
				<td align="left" colspan="2">Posted Charges & Payments</td>
				<td align="center" class="text_b_w" colspan="7">Payments</td>
			</tr>
			<tr>
				<td align="center" width="116" class="text_b_w">'.$firstColTitle.'</td>
				<td align="center" width="116" class="text_b_w">Charges</td>
				<td align="center" width="116" class="text_b_w">Trans#</td>
				<td align="center" width="116" class="text_b_w">Cash</td>
				<td align="center" width="116" class="text_b_w">Pt. Check</td>
				<td align="center" width="116" class="text_b_w">Ins. Check</td>
				<td align="center" width="116" class="text_b_w">CC</td>
				<td align="center" width="116" class="text_b_w">EFT</td>
				<td align="center" width="110" class="text_b_w">MO</td>					
			</tr>				
		</table>
		</page_header>
		<table width="100%" border="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
			'.$pdfData2.'
			<tr><td class="total-row" colspan="9"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Total Posted :</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['charges'],2).'</td>
				<td style="text-align:right" class="text_10b">'.$arrMainTotal['qty'].'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['cash'],2).'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['check_pat'],2).'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['check_ins'],2).'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['cc'],2).'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['eft'],2).'</td>					
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['money_order'],2).'</td>
			</tr>
			<tr><td class="total-row" colspan="9"></td></tr>
			<tr>
				<td align="right" bgcolor="#FFFFFF" class="text_10b">Total Payments :</td>
				<td align="right" bgcolor="#FFFFFF" class="text_10b">'.$TotalPayments.'</td>
				<td align="right" bgcolor="#FFFFFF" class="text_10b" colspan="7"></td>
			</tr>
			<tr><td class="total-row" colspan="9"></td></tr>			
		</table>
		</page>';

		
		//--- GET CSV DATA ---
		$csvFileData.=
		$page_header_val.'
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">				
			<tr id="heading_orange">
				<td class="text_b_w" style="text-align:left;font-weight:bold" colspan="2">Posted Charges & Payments</td>
				<td colspan="7" class="text_b_w" style="text-align:center; font-weight:bold">Payments</td>					
			</tr>
			<tr>
				<td width="116" class="text_b_w" style="text-align:center">'.$firstColTitle.'</td>
				<td width="116"class="text_b_w" style="text-align:center">Charges</td>
				<td width="116" class="text_b_w" style="text-align:center">Trans#</td>
				<td width="116" class="text_b_w" style="text-align:center">Cash</td>
				<td width="116" class="text_b_w" style="text-align:center">Pat. Check</td>
				<td width="116" class="text_b_w" style="text-align:center">Ins. Check</td>
				<td width="116" class="text_b_w" style="text-align:center">CC</td>
				<td width="116" class="text_b_w" style="text-align:center">EFT</td>
				<td width="110" class="text_b_w" style="text-align:center">MO</td>					
			</tr>				
			'.$pdfData2.'
			<tr><td class="total-row" colspan="9"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Total Posted :</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['charges'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.$arrMainTotal['qty'].'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['cash'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['check_pat'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['check_ins'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['cc'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['eft'],2).'</td>					
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['money_order'],2).'</td>
			</tr>
			<tr><td class="total-row" colspan="9"></td></tr>
			<tr>
				<td align="right" bgcolor="#FFFFFF" class="text_10b">Total Payments :</td>
				<td align="right" bgcolor="#FFFFFF" class="text_10b">'.$TotalPayments.'</td>
				<td align="right" bgcolor="#FFFFFF" class="text_10b" colspan="7"></td>
			</tr>
			<tr><td class="total-row" colspan="9"></td></tr>
		</table>';
		$page_header_val = NULL;
}



$pdf_part='';
$grdRePaymentCount= '';
$grdRePatientCash= '';
$grdRePatientCheck= '';
$grdReInsCheck= '';
$grdRePatientCredit= '';
$grdRePatientEFT= '';
$grdRePatientMoneyOrder= '';
$grdRePostedTotalAmt= '';
$grdRePostCnt= '';
//--- NOT SUBMITTED CHARGES ---
$grdtotalPaymentCount = 0;
$grdtotalPatientCash = 0;
$grdtotalPatientCheck = 0;
$grdtotalInsCheck = 0;
$grdtotalPatientCredit = 0;
$grdtotalPatientEFT = 0;
$grdtotalPatientMoneyOrder = 0;
$grdtotalPostedTotalAmt = 0;
$grdtotalPostCnt = 0;
$grdtotalPaidAmt = 0;

if(count($mainPaymentArr) >0 || count($submitedArr) >0 || count($notSubmitedArr)>0){
	
	$totalPatientCash = $maintotalPatientCash;
	$totalPatientCash += $maintotalPatientCheck;
	$totalPatientCash += $maintotalInsCheck;
	$totalPatientCash += $maintotalPatientCredit;	
	$totalPatientCash += $maintotalPatientEFT;
	$totalPatientCash += $maintotalPatientMoneyOrder;
	
	$subtotalPatientCash = numberFormat($maintotalPatientCash, 2);
	$subtotalPatientCheck = numberFormat($maintotalPatientCheck, 2);
	$subtotalInsCheck = numberFormat($maintotalInsCheck, 2);
	$subtotalPatientCredit = numberFormat($maintotalPatientCredit, 2);
	$subtotalPostedTotalAmt = numberFormat($maintotalPostedTotalAmt, 2);
	$subtotalPatientEFT=numberFormat($maintotalPatientEFT,2);
	$subtotalPatientMoneyOrder=numberFormat($maintotalPatientMoneyOrder,2);
	$totalPatientCash = numberFormat($totalPatientCash, 2);

	$subtotalPostCnt = $maintotalPostCnt > 0 ? $maintotalPostCnt : NULL;
	$subtotalPaymentCount = $maintotalPaymentCount > 0 ? $maintotalPaymentCount : NULL;
}


//OPERATOR DATA
$csvFileData.=$oper_data;
$pdfData.=$oper_data;
//- END OPER DATA
	
?>