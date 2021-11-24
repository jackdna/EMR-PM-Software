<?php
function sortByOrder($a, $b) {
    if ($a['charges'] == $b['charges']) return 0;
	 return ($a['charges'] < $b['charges']) ? -1 : 1;
}


$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$op_name = ucfirst(trim($op_name_arr[1][0]));
$op_name .= ucfirst(trim($op_name_arr[0][0]));
$curDate = date($phpDateFormat.' h:i A');

//--- GET DEPARTMENT DETAILS -------
$dep = imw_query("select DepartmentId, DepartmentCode from department_tbl");
$deprtQryRes =  array();
$departMentArr = array();
while($qryRes  =imw_fetch_array($dep)){
	$deprtQryRes[] = $qryRes;
}
for($d=0;$d<count($deprtQryRes);$d++){
	$DepartmentId = $deprtQryRes[$d]['DepartmentId'];
	$DepartmentCode = $deprtQryRes[$d]['DepartmentCode'];
	$departMentArr[$DepartmentId] = $DepartmentCode;
}


//PAYMENTS & CHARGES BLOCK
$grandTotalPayment = array();
$tempCheckEncIds=array();
$arrMainTotal=array();
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

		$pdfData2.='<tr><td class="text_b_w" style="text-align:left" colspan="12">Operator : '.$firstGrpName.'</td></tr>';
	
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
	
			$pdfData2.='<tr><td class="text_b_w" style="text-align:left" colspan="12">'.$secGrpTitle.' : '.$secGrpName.'</td></tr>';
			
			$tempSortingArr = array();
	
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
						$pat_name_arr = array();
						if(empty($patientName)==true){
							$pat_name_arr['LAST_NAME'] = $detailsArr['lname'];
							$pat_name_arr['FIRST_NAME'] = $detailsArr['fname'];
							$pat_name_arr['MIDDLE_NAME'] = $detailsArr['mname'];
							$patientName = changeNameFormat($pat_name_arr);
							$patientName.= " - ".$detailsArr['id'];
						}
						$date_of_service = $detailsArr['date_of_service'];
						$phyId = $detailsArr['primaryProviderId'];
						$facId = $detailsArr['facility_id'];
						$charges+=$detailsArr['totalAmount'];
					}
					unset($mainPostedEncounterArr[$firstGrpId][$secGrpId][$encounterId]);// TO PREVENT FROM DUPLICATION
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
					$cashPayment+= $crdDbtAmt;
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
				
				
				$cashPayment = numberFormat($cashPayment, 2,'yes');
				$checkPatPayment = numberFormat($checkPatPayment, 2,'yes');
				$checkInsPayment =  numberFormat($checkInsPayment, 2,'yes');
				$CCPayment =  numberFormat($CCPayment, 2,'yes');
				$adjAmt =  numberFormat($adjAmt, 2);
				$eftPayment=  numberFormat($eftPayment,2,'yes');
				$moneyOrderPayment=  numberFormat($moneyOrderPayment,2,'yes');
				
				$cashPayment = ($cashPayment=='') ? '$0.00' : $cashPayment;
				$checkPatPayment = ($checkPatPayment=='') ? '$0.00' : $checkPatPayment;
				$checkInsPayment = ($checkInsPayment=='') ? '$0.00' : $checkInsPayment;
				$CCPayment = ($CCPayment=='') ? '$0.00' : $CCPayment;
				$eftPayment = ($eftPayment=='') ? '$0.00' : $eftPayment;
				$moneyOrderPayment = ($moneyOrderPayment=='') ? '$0.00' : $moneyOrderPayment;
				
				if($orderByTemp=='Charges')
				{	
					$col++;
					$orderByTempArr[$col]["firstColName"]=$firstColName;
					$orderByTempArr[$col]["patientName"]=$patientName;
					$orderByTempArr[$col]["date_of_service"]=$date_of_service;
					$orderByTempArr[$col]["encounterId"]=$encounterId;
					$orderByTempArr[$col]["charges"]=$charges;
					$orderByTempArr[$col]["payment_cnt"]=$payment_cnt;
					$orderByTempArr[$col]["cashPayment"]=$cashPayment;
					$orderByTempArr[$col]["checkPatPayment"]=$checkPatPayment;
					$orderByTempArr[$col]["checkInsPayment"]=$checkInsPayment;
					$orderByTempArr[$col]["CCPayment"]=$CCPayment;
					$orderByTempArr[$col]["eftPayment"]=$eftPayment;
					$orderByTempArr[$col]["moneyOrderPayment"]=$moneyOrderPayment;
					
					
				}
				else
				{
					$charges = numberFormat($charges, 2,'yes');
					
					if(!isset($tempSortingArr[$patientName]))
						$tempSortingArr[$patientName] = array();
					
					$sortingDataArr = array();
					$sortingDataArr['firstColName'] = $firstColName;
					$sortingDataArr['patientName'] = $patientName;
					$sortingDataArr['date_of_service'] = $date_of_service;
					$sortingDataArr['encounterId'] = $encounterId;
					$sortingDataArr['charges'] = $charges;
					$sortingDataArr['payment_cnt'] = $payment_cnt;
					$sortingDataArr['cashPayment'] = $cashPayment;
					$sortingDataArr['checkPatPayment'] = $checkPatPayment;
					$sortingDataArr['checkInsPayment'] = $checkInsPayment;
					$sortingDataArr['CCPayment'] = $CCPayment;
					$sortingDataArr['eftPayment'] = $eftPayment;
					$sortingDataArr['moneyOrderPayment'] = $moneyOrderPayment;
					array_push($tempSortingArr[$patientName], $sortingDataArr);
				}
			}
			
			ksort($tempSortingArr);
			
			if( count($tempSortingArr) > 0 ){
				foreach($tempSortingArr as $sortPtName=>$sortdataVals){
					foreach($sortdataVals as $sortvals){
						//for detail mode
						$pdfData2 .= '
						<tr>
							<td class="text_10" bgcolor="#FFFFFF" width="85" align="left">'.$sortvals['firstColName'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="135" align="left">'.$sortvals['patientName'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$sortvals['date_of_service'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$sortvals['encounterId'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$sortvals['charges'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$sortvals['payment_cnt'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$sortvals['cashPayment'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$sortvals['checkPatPayment'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$sortvals['checkInsPayment'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$sortvals['CCPayment'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$sortvals['eftPayment'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$sortvals['moneyOrderPayment'].'</td>
						</tr>';
					}
				}
			}
			
			if($orderByTemp=='Charges')
			{
				usort($orderByTempArr,'sortByOrder');
								
				for($i=0;$i<=sizeof($orderByTempArr)-1;$i++)
				{
					$charges = numberFormat($orderByTempArr[$i]["charges"], 2,'yes');
					$pdfData2 .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" width="85" align="left">'.$orderByTempArr[$i]["firstColName"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="135" align="left">'.$orderByTempArr[$i]["patientName"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$orderByTempArr[$i]["date_of_service"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$orderByTempArr[$i]["encounterId"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$charges.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$orderByTempArr[$i]["payment_cnt"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$orderByTempArr[$i]["cashPayment"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$orderByTempArr[$i]["checkPatPayment"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$orderByTempArr[$i]["checkInsPayment"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$orderByTempArr[$i]["CCPayment"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$orderByTempArr[$i]["eftPayment"].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" width="80">'.$orderByTempArr[$i]["moneyOrderPayment"].'</td>
					</tr>';
				}
				
				unset($orderByTempArr);
			}
			
			// SECOND GROUP TOTALS
			$facCharges = numberFormat($facCharges, 2);
			$facCashTotal = numberFormat($facCashTotal, 2);
			$facCheckPatTotal = numberFormat($facCheckPatTotal, 2);
			$facCheckInsTotal = numberFormat($facCheckInsTotal, 2);
			$facCCTotal = numberFormat($facCCTotal, 2);
			$facEftTotal= numberFormat($facEftTotal,2);
			$facMoneyOrderTotal= numberFormat($facMoneyOrderTotal,2);
			$facAdjAmt = numberFormat($facAdjAmt, 2);
	
			$pdfData2.= <<<DATA
			<tr><td class="total-row" colspan="12"></td></tr>
			<tr>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right" colspan="4">$secGrpTitle Total:</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$facCharges</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$facQty</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$facCashTotal</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$facCheckPatTotal</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$facCheckInsTotal</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$facCCTotal</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$facEftTotal</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$facMoneyOrderTotal</td>
			</tr>
			<tr><td class="total-row" colspan="12"></td></tr>
DATA;
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
		
		$pdfData2.= <<<DATA
		<tr><td class="total-row" colspan="12"></td></tr>
		<tr>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right" colspan="4">Operator Total:</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subChargesTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subQty</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subCashTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subCheckPatTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subCheckInsTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subCCTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subEftTotal</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">$subMoneyOrderTotal</td>
		</tr>
		<tr><td class="total-row" colspan="12"></td></tr>
DATA;
		
		}	

		//MAIN TOTAL
		$footerTotalData = NULL;
		//--- GRNAD TOTAL PAYMENTS OF ALL PROVIDERS ----
		
		$TotalPayments=$arrMainTotal['cash'] + $arrMainTotal['check_pat'] + $arrMainTotal['check_ins'] + $arrMainTotal['cc'] + $arrMainTotal['eft'] + $arrMainTotal['money_order'];	
		$TotalPayments = numberFormat($TotalPayments - array_sum($total_refund_arr),2);

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
			<tr>
				<td align="left" colspan="5" class="text_b_w" style="font-weight:bold">Posted Charges & Payments</td>
				<td align="center" colspan="7" class="text_b_w" style="font-weight:bold">Payments</td>
			</tr>
			<tr>
				<td align="center" width="85" class="text_b_w">'.$firstColTitle.'</td>
				<td align="center" width="135" class="text_b_w">Patient Name-ID</td>
				<td align="center" width="80" class="text_b_w">DOS</td>
				<td align="center" width="80" class="text_b_w">E.ID</td>
				<td align="center" width="80" class="text_b_w">Charges</td>
				<td align="center" width="80" class="text_b_w">Trans#</td>
				<td align="center" width="80" class="text_b_w">Cash</td>
				<td align="center" width="80" class="text_b_w">Pt. Check</td>
				<td align="center" width="80" class="text_b_w">Ins. Check</td>
				<td align="center" width="80" class="text_b_w">CC</td>
				<td align="center" width="80" class="text_b_w">EFT</td>
				<td align="center" width="80" class="text_b_w">MO</td>					
			</tr>				
		</table>
		</page_header>
		<table width="100%" border="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
			'.$pdfData2.'
			<tr><td class="total-row" colspan="12"></td></tr>
			<tr>
				<td colspan="4" class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Total Posted :</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['charges'],2).'</td>
				<td style="text-align:right" class="text_10b">'.$arrMainTotal['qty'].'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['cash'],2).'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['check_pat'],2).'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['check_ins'],2).'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['cc'],2).'</td>
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['eft'],2).'</td>					
				<td style="text-align:right" class="text_10b">'.numberFormat($arrMainTotal['money_order'],2).'</td>
			</tr>
			<tr><td class="total-row" colspan="12"></td></tr>
			<tr>
				<td bgcolor="#FFFFFF" class="text_10b" colspan="4" style="text-align:right">Total Payments :</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$TotalPayments.'</td>
				<td align="right" bgcolor="#FFFFFF" class="text_10b" colspan="7"></td>
			</tr>
			<tr><td class="total-row" colspan="12"></td></tr>			
		</table>
		</page>';

		
		//--- GET CSV DATA ---
		$csvFileData.=
		$page_header_val.'
		<table width="100%" border="0" cellpadding="1" cellspacing="1" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">				
			<tr id="heading_orange">
				<td colspan="5" style="font-weight:bold; text-align:left">Posted Charges & Payments</td>
				<td colspan="7" style="font-weight:bold; text-align:center">Payments</td>					
			</tr>
			<tr>
				<td class="text_b_w" style="text-align:center">'.$firstColTitle.'</td>
				<td class="text_b_w" style="text-align:center">Patient Name-ID</td>
				<td class="text_b_w" style="text-align:center">DOS</td>
				<td class="text_b_w" style="text-align:center">E.ID</td>
				<td class="text_b_w" style="text-align:center">Charges</td>
				<td class="text_b_w" style="text-align:center">Trans#</td>
				<td class="text_b_w" style="text-align:center">Cash</td>
				<td class="text_b_w" style="text-align:center">Pat. Check</td>
				<td class="text_b_w" style="text-align:center">Ins. Check</td>
				<td class="text_b_w" style="text-align:center">CC</td>
				<td class="text_b_w" style="text-align:center">EFT</td>
				<td class="text_b_w" style="text-align:center">MO</td>					
			</tr>				
			'.$pdfData2.'
			<tr><td class="total-row" colspan="12"></td></tr>
			<tr>
				<td colspan="4" class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Total Posted :</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['charges'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.$arrMainTotal['qty'].'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['cash'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['check_pat'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['check_ins'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['cc'],2).'</td>
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['eft'],2).'</td>					
				<td style="text-align:right" bgcolor="#FFFFFF" class="text_10b">'.numberFormat($arrMainTotal['money_order'],2).'</td>
			</tr>
			<tr><td class="total-row" colspan="12"></td></tr>
			<tr>
				<td bgcolor="#FFFFFF" class="text_10b" colspan="4" style="text-align:right">Total Payments :</td>
				<td align="right" bgcolor="#FFFFFF" class="text_10b">'.$TotalPayments.'</td>
				<td align="right" bgcolor="#FFFFFF" class="text_10b" colspan="7"></td>
			</tr>
			<tr><td class="total-row" colspan="12"></td></tr>
		</table>';
		$page_header_val = NULL;
}

 
//PRE PAYMENTS BLOCK
if(sizeof($arrDepPatientData)>0){
	$prePayTotal=$preNotApplyTot=0;
	$pre_page_data.='<table width="100%" cellspacing="1" cellpadding="1" border="0" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">';
	$pre_page_data.='<tr id="heading_orange"><td style="text-align:left" colspan="5">&nbsp;Patient Pre Payments</td></tr>';

	$prePDFHead.='<table width="100%" border="0" class="rpt rpt_table rpt_table-bordered rpt_padding" class="table">';
	$prePDFHead.= '<tr id="heading_orange"><td style="text-align:left;" colspan="5">&nbsp;Patient Pre Payments</td></tr>';	

	$pre_page_data.=
	'<tr>
		<td class="text_b_w" style="width:205px; text-align:center;">Patient Name-ID</td>
		<td class="text_b_w" style="width:205px; text-align:center;">Payment Date</td>
		<td class="text_b_w" style="width:205px; text-align:center;">Pre Payment</td>
		<td class="text_b_w" style="width:205px; text-align:center;">Method</td>
		<td class="text_b_w" style="width:205px; text-align:center;">Unapplied Amount</td>
	</tr>';							
	$prePDFHead .= '
	<tr>
		<td class="text_b_w" style="text-align:center; width:205px">Patient Name-ID</td>
		<td class="text_b_w" style="text-align:center; width:205px">Payment Date</td>
		<td class="text_b_w" style="text-align:center; width:205px">Pre Payment</td>
		<td class="text_b_w" style="text-align:center; width:205px">Method</td>
		<td class="text_b_w" style="text-align:center; width:205px">Unapplied Amount</td>
	</tr>
	</table>';	
	
	$preStrHTML='<table width="100%" border="0" class="rpt rpt_table rpt_table-bordered rpt_padding">';						
	
	foreach($arrDepPatientData as $operId => $operData){
		$arrSubTot=array();
		$pre_page_data.='<tr><td class="text_b_w alignLeft" colspan="5">'.$userNameArr[$operId].'</td></tr>';
		$preStrHTML.='<tr><td class="text_b_w alignLeft" colspan="6">'.$userNameArr[$operId].'</td></tr>';		
		
		foreach($operData as $id => $patData){
			$preCash+=$patData['CASH'];
			$preCheck+=$patData['CHECK'];
			$preCC+=$patData['CC'];
			$preMO+=$patData['MO'];
			$prePayTotal+=$patData['PAT_DEPOSIT']-$patData['PAT_DEPOSIT_REF'];
			$preNotApplyTot+=$patData['NOT_APPLIED_AMT'];
			$dopsit=$patData['PAT_DEPOSIT']-$patData['PAT_DEPOSIT_REF'];
			$arrSubTot['pat_deposit']+=$dopsit;
			$arrSubTot['not_applied']+=	$patData['NOT_APPLIED_AMT'];
			
			$redRowPP=($patData['PAT_DEPOSIT_REF'])?';color:#FF0000" title="$'.$patData['PAT_DEPOSIT_REF'].' Refund':'';
			
			$pre_page_data.='
			<tr style="height:25px">
				<td class="text_10 alignLeft white" style="width:205px;">&nbsp;'.$patData['PATNAME'].'</td>
				<td class="text_10 alignLeft white" style="width:205px; text-align:center">&nbsp;'.$patData['PAID_DATE'].'</td>
				<td class="text_10 white" style="width:205px; text-align:right'.$redRowPP.'">'.numberFormat($dopsit,2,1).'&nbsp;</td>
				<td class="text_10 white" style="width:205px; text-align:center;">&nbsp;'.$patData['PAT_DEPOSIT_MODE'].'</td>
				<td class="text_10 white" style="width:205px; text-align:right;">'.numberFormat($patData['NOT_APPLIED_AMT'],2).'&nbsp;</td>
			</tr>';
			$preStrHTML.='
			<tr style="height:25px">
				<td class="text_10 alignLeft white" style="width:205px;">&nbsp;'.$patData['PATNAME'].'</td>
				<td class="text_10 alignLeft white" style="width:205px; text-align:center">&nbsp;'.$patData['PAID_DATE'].'</td>
				<td class="text_10 white" style="width:205px; text-align:right'.$redRowPP.'">'.numberFormat($dopsit,2,1).'&nbsp;</td>
				<td class="text_10 white" style="width:205px; text-align:center;">&nbsp;'.$patData['PAT_DEPOSIT_MODE'].'</td>
				<td class="text_10 white" style="width:205px; text-align:right;">'.numberFormat($patData['NOT_APPLIED_AMT'],2).'&nbsp;</td>
			</tr>';
		}
		//OPERATOR TOTAL
		$pre_page_data.='
		<tr><td class="total-row" colspan="5"></td></tr>
		<tr style="height:25px">
			<td class="text_10b white" style="text-align:right;" colspan="2">Operator Total : </td>
			<td class="text_10b white" style="text-align:right">'.numberFormat($arrSubTot['pat_deposit'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;"></td>
			<td class="text_10b white" style="text-align:right;">'.numberFormat($arrSubTot['not_applied'],2).'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="5"></td></tr>';
		$preStrHTML.='
		<tr><td class="total-row" colspan="5"></td></tr>
		<tr style="height:25px">
			<td class="text_10b white" style="text-align:right;" colspan="2">Operator Total : </td>
			<td class="text_10b white" style="text-align:right">'.numberFormat($arrSubTot['pat_deposit'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;"></td>
			<td class="text_10b white" style="text-align:right;">'.numberFormat($arrSubTot['not_applied'],2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;"></td>
		</tr>
		<tr><td class="total-row" colspan="5"></td></tr>';
	}

	$pre_page_data.='
	<tr><td class="total-row" colspan="5"></td></tr>
	<tr style="height:30px">
		<td class="text_10b white" style="text-align:right; width:205px;"></td>
		<td class="text_10b white" style="text-align:right; width:205px;">Total :</td>
		<td class="text_10b white" style="text-align:right; width:205px;">'.numberFormat($prePayTotal,2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:205px;"></td>
		<td class="text_10b white" style="text-align:right; width:205px;">'.numberFormat($preNotApplyTot,2).'&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="5"></td></tr>';
					
	$preStrHTML.='
	<tr><td class="total-row" colspan="5"></td></tr>
	<tr style="height:30px">
		<td class="text_10b white" style="text-align:right; width:205px;"></td>
		<td class="text_10b white" style="text-align:right; width:205px;">Total :</td>
		<td class="text_10b white" style="text-align:right; width:205px;">'.numberFormat($prePayTotal,2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:205px;"></td>
		<td class="text_10b white" style="text-align:right; width:205px;">'.numberFormat($preNotApplyTot,2).'&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="5"></td></tr>';					
	$pre_page_data.='</table>';
	$preStrHTML.='</table>';

	$pre_page_data.='
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8">
	<tr>
		<td class="text_10b white" style="text-align:right; width:80px;"></td>
		<td class="text_10b white" style="text-align:right; width:120px;">Cash&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:120px;">Check&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:120px;">Credit Card&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:120px">Money Order&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:150px">Total Payment&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="6"></td></tr>
	<tr style="height:30px">
		<td class="text_10b white" style="text-align:right;"></td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($arrDepPayments['CASH'],2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($arrDepPayments['CHECK'],2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($arrDepPayments['CC'],2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($arrDepPayments['MO'],2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($prePayTotal,2,1).'&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="6"></td></tr>
	</table>';
	
	$preStrHTML.='
	<table width="100%" border="0" class="table">
	<tr>
		<td class="text_10b white" style="text-align:right; width:170px;">Cash&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:150px;">Check&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:150px;">Credit Card&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:150px">Money Order&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:150px">Total Payment&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:232px;"></td>		
	</tr>
	<tr><td class="total-row" colspan="6"></td></tr>
	<tr style="height:30px">
		<td class="text_10b white" style="text-align:right;">'.numberFormat($arrDepPayments['CASH'],2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($arrDepPayments['CHECK'],2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($arrDepPayments['CC'],2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($arrDepPayments['MO'],2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;">'.numberFormat($prePayTotal,2,1).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right;"></td>
	</tr>
	<tr><td class="total-row" colspan="6"></td></tr>
	</table>';	
	
	$pre_pdf='
	<page backtop="20mm" backbottom="9mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>	
	<page_header>	
	'.$fileHeaderData
	 .$prePDFHead.'
	</page_header>
	'.$preStrHTML.'
	</page>';
	

//	$pre_page_data.=$pre_del_page_data;
//	$preStrHTML.=$preDelStrHTML;
	$csvFileData.=$pre_page_data;
	$pdfData.=$pre_pdf;
}
//-- END PRE PAYMENTS

$pdf_part ='';
?>