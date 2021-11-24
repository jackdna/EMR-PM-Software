<?php
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
if(count($paidSubmitEncounterArr)>0){
	//--- ALL PAYMENT DETAILS -----
	$facilityArrKeys = array_keys($paidSubmitEncounterArr);
	for($p=0; $p<sizeof($facilityArrKeys); $p++){
		$facilityId = $facilityArrKeys[$p];
		$facilityName = $posFacilityArr[$facilityId];

		$pdf_part.=
			'<tr>
				<td class="text_b_w" align="left" colspan="10">Facility : '.$facilityName.'</td>
			</tr>';

		$providerArrKeys = array_keys($paidSubmitEncounterArr[$facilityId]);
		
		$subtotalPaymentCount= '';
		$subtotalPatientCash= '';
		$subtotalPatientCheck= '';
		$subtotalInsCheck= '';
		$subtotalPatientCredit= '';
		$subtotalPatientEFT= '';
		$subtotalPatientMoneyOrder= '';
		$subtotalPostedTotalAmt='';
		$subtotalPostCnt='';
		
		for($f=0; $f<sizeof($providerArrKeys); $f++){
			$providerId = $providerArrKeys[$f];
			$providerName = $userNameArr[$providerId];
			$encounterArrKeys = array_keys($paidSubmitEncounterArr[$facilityId][$providerId]);

			$paymentCnt = 0;
			$submitPatCheckArr = array();
			$submitCheckArr = array();
			$submitCashArr = array();
			$submitCCArr = array();
			$submitEFTArr=array();
			$sumbitMoneyOrderArr=array();
				
			for($e=0; $e<sizeof($encounterArrKeys); $e++){
				$encounter_id = $encounterArrKeys[$e];

				$enconuterPayment = $mainPaymentArr[$encounter_id]['payment'];
				$enconuterPaymentArr = $mainPaymentArr[$encounter_id]['payment']['payment_id'];
				$paymentCnt+= count($enconuterPaymentArr);

				for($d=0;$d<count($enconuterPaymentArr);$d++){
					$encounter = $enconuterPaymentArr[$d];
					$paidForProc = str_replace(',','',$enconuterPayment[$encounter]['paidForProc']);
					$overPayment = str_replace(',','',$enconuterPayment[$encounter]['overPayment']);
					$t_paid_amount = $paidForProc + $overPayment;

					if($enconuterPayment[$encounter]['paymentClaims'] == 'Negative Payment'){
						$t_paid_amount = '-'.$t_paid_amount;
					}
					 
					$paid_by = strtolower(trim($enconuterPayment[$encounter]['paid_by']));
					$payment_mode = strtolower(trim($enconuterPayment[$encounter]['payment_mode']));
					//echo $payment_mode.'<br/>';
					if($payment_mode == 'check'){
						if($paid_by == 'patient'){
							$submitPatCheckArr[] = $t_paid_amount;
						}
						else{
							$submitCheckArr[] = $t_paid_amount;
						}
					}
					else if($payment_mode == 'cash'){
						$submitCashArr[] = $t_paid_amount;
					}
					else if($payment_mode == 'eft')
					{
						$submitEFTArr[]=$t_paid_amount;
					}
					else if($payment_mode == 'money order')
					{
						$sumbitMoneyOrderArr[]=$t_paid_amount;
					}
					else{
						$submitCCArr[] = $t_paid_amount;
					}
				}				
			}

			$submitPatCheck = array_sum($submitPatCheckArr);
			$submitCheck = array_sum($submitCheckArr);
			$submitCash = array_sum($submitCashArr);
			$submitCC = array_sum($submitCCArr);
			$submitEFT=array_sum($submitEFTArr);
			$submitMoneyOrder=array_sum($sumbitMoneyOrderArr);	
			//---- SUB TOTAL -----
			$subtotalPaymentCount+= $paymentCnt;
			$subtotalPatientCash+= $submitCash;
			$subtotalPatientCheck+= $submitPatCheck;
			$subtotalInsCheck+= $submitCheck;
			$subtotalPatientCredit+= $submitCC;
			$subtotalPatientEFT+= $submitEFT;
			$subtotalPatientMoneyOrder+= $submitMoneyOrder;
			
			$submitCash = numberFormat($submitCash, 2);
			$submitPatCheck = numberFormat($submitPatCheck, 2);
			$submitCheck = numberFormat($submitCheck, 2);
			$submitCC = numberFormat($submitCC, 2);
			$submitEFT= numberFormat($submitEFT,2);
			$submitMoneyOrder= numberFormat($submitMoneyOrder,2);
			$paymentCnt = $paymentCnt > 0 ? $paymentCnt : NULL;
			
			//--- ALL POSTED CHARGES -------
			$submitAmtArr = array();
			$submittedData = $submitedArr[$facilityId][$providerId];
			for($s=0;$s<count($submittedData);$s++){
				$submitAmtArr[] = $submittedData[$s]['totalAmount'];
				$dept_charge_list_id_arr[] = $submittedData[$s]['charge_list_id'];
				$departmentEncounterArr[] = $submittedData[$s]['encounter_id'];
			}
			
			$submitAmt = array_sum($submitAmtArr);
			$subtotalPostedTotalAmt+= $submitAmt;
			$submitAmt = numberFormat($submitAmt, 2);
			
			$submit_cnt = count($submittedData) > 0 ? count($submittedData) : NULL;
			$subtotalPostCnt += count($submittedData);	
			if(strlen($providerName)>22){
				$providerName=substr($providerName,0, 21).'..';
			}

			$pdf_part.='<tr>
				<td align="left" class="text_12" bgcolor="#FFFFFF">'.$providerName.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$submit_cnt.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$submitAmt.'</td>					
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$paymentCnt.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$submitCash.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$submitPatCheck.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$submitCheck.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$submitCC.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$submitEFT.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$submitMoneyOrder.'</td>			
			</tr>';
		}

		// GRAND TOTAL ARRAY
		$grdtotalPaymentCount+= $subtotalPaymentCount;
		$grdtotalPatientCash+= $subtotalPatientCash;
		$grdtotalPatientCheck+= $subtotalPatientCheck;
		$grdtotalInsCheck+= $subtotalInsCheck;
		$grdtotalPatientCredit+= $subtotalPatientCredit;
		$grdtotalPatientEFT+= $subtotalPatientEFT;
		$grdtotalPatientMoneyOrder+= $subtotalPatientMoneyOrder;
		$grdtotalPostedTotalAmt+= $subtotalPostedTotalAmt;
		$grdtotalPostCnt+= $subtotalPostCnt;		
		$paidAmount = $subtotalPatientCash + $subtotalPatientCheck + $subtotalInsCheck + $subtotalPatientCredit + $subtotalPatientEFT + $subtotalPatientMoneyOrder;

		$submitCash = numberFormat($subtotalPatientCash, 2);
		$submitPatCheck = numberFormat($subtotalPatientCheck, 2);
		$submitCheck = numberFormat($subtotalInsCheck, 2);
		$submitCC = numberFormat($subtotalPatientCredit, 2);
		$submitEFT= numberFormat($subtotalPatientEFT,2);
		$submitMoneyOrder= numberFormat($subtotalPatientMoneyOrder,2);
		$subtotalPostedTotalAmt= numberFormat($subtotalPostedTotalAmt,2);
		$paymentCnt = $subtotalPaymentCount > 0 ? $subtotalPaymentCount : NULL;		
		$paidAmount= numberFormat($paidAmount,2);
		
		$pdf_part.='<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Sub Total :</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$subtotalPostCnt.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$subtotalPostedTotalAmt.'</td>					
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$paymentCnt.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitCash.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitPatCheck.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitCheck.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitCC.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitEFT.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitMoneyOrder.'</td>			
		</tr>';
		$pdf_part.='<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Totals :</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF" colspan="9">'.$paidAmount.'</td>
		</tr>';		
	}

	// MAIN TOTAL ARRAY
	$maintotalPaymentCount+= $grdtotalPaymentCount;
	$maintotalPatientCash+= $grdtotalPatientCash;
	$maintotalPatientCheck+= $grdtotalPatientCheck;
	$maintotalInsCheck+= $grdtotalInsCheck;
	$maintotalPatientCredit+= $grdtotalPatientCredit;
	$maintotalPatientEFT+= $grdtotalPatientEFT;
	$maintotalPatientMoneyOrder+= $grdtotalPatientMoneyOrder;
	$maintotalPostedTotalAmt+= $grdtotalPostedTotalAmt;
	$maintotalPostCnt+= $grdtotalPostCnt;	
	$totalPaidAmount = $grdtotalPatientCash + $grdtotalPatientCheck + $grdtotalInsCheck + $grdtotalPatientCredit + $grdtotalPatientEFT + $grdtotalPatientMoneyOrder;
	
	// GRAND TOTALS	
	$submitCash = numberFormat($grdtotalPatientCash, 2);
	$submitPatCheck = numberFormat($grdtotalPatientCheck, 2);
	$submitCheck = numberFormat($grdtotalInsCheck, 2);
	$submitCC = numberFormat($grdtotalPatientCredit, 2);
	$submitEFT= numberFormat($grdtotalPatientEFT,2);
	$submitMoneyOrder= numberFormat($grdtotalPatientMoneyOrder,2);
	$subtotalPostedTotalAmt= numberFormat($grdtotalPostedTotalAmt,2);
	$paymentCnt = $grdtotalPaymentCount > 0 ? $grdtotalPaymentCount : NULL;		
	$totalPaidAmount= numberFormat($totalPaidAmount,2);

	$pdfData .= <<<DATA
		<page backtop="9mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		$fileHeaderData
		<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">
		<tr>
			<td colspan="10" class="text_b_w" width="700"><b>Total Posted Charges & Payments</b></td>
		</tr>
		<tr>
			<td class="text_b_w" style="text-align:center">Description</td>
			<td class="text_b_w" colspan="2" style="text-align:center">Charges Posted</td>
			<td class="text_b_w" colspan="7" style="text-align:center">Payment</td>
		</tr>
		<tr>
			<td class="text_b_w" style="text-align:center" style="width:105px">Physician</td>
			<td class="text_b_w" style="text-align:center" style="width:30px">Qty.</td>
			<td class="text_b_w" style="text-align:center" style="width:75px">Charges</td>					
			<td class="text_b_w" style="text-align:center" style="width:30px">Qty.</td>
			<td class="text_b_w" style="text-align:center" style="width:70px">Cash</td>
			<td class="text_b_w" style="text-align:center" style="width:75px">Pt. Check</td>
			<td class="text_b_w" style="text-align:center" style="width:75px">Ins. Check</td>
			<td class="text_b_w" style="text-align:center" style="width:70px">Credit Card</td>
			<td class="text_b_w" style="text-align:center" style="width:70px">EFT</td>
			<td class="text_b_w" style="text-align:center" style="width:70px">Money Order</td>			
		</tr>
		$pdf_part
		<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Total Posted :</td>
			<td align="center" class="text_12b" bgcolor="#FFFFFF">$grdtotalPostCnt</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$subtotalPostedTotalAmt</td>					
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$paymentCnt</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitCash</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitPatCheck</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitCheck</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitCC</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitEFT</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitMoneyOrder</td>			
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
		<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Grand Total:</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF"></td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$subtotalPostedTotalAmt</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF" colspan="6">$totaPaidAmount</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$totaPaidAmount</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
		</table>
		</page>	
DATA;

	//--- GET DATA FOR CSV FILE ----
	$csvFileData .= <<<DATA
			<tr>
				<td colspan="10" class="text_b_w" width="700"><b>Total Posted Charges & Payments</b></td>
			</tr>
			<tr>
				<td class="text_b_w" style="text-align:center">Description</td>
				<td class="text_b_w" colspan="2" style="text-align:center">Charges Posted</td>
				<td class="text_b_w" colspan="7" style="text-align:center">Payment</td>
			</tr>
			<tr>
				<td class="text_b_w" style="text-align:center; width:150px;">Physician</td>
				<td class="text_b_w" style="text-align:center; width:40px;">Qty.</td>
				<td class="text_b_w" style="text-align:center; width:100px;">Charges</td>					
				<td class="text_b_w" style="text-align:center; width:40px;">Qty.</td>
				<td class="text_b_w" style="text-align:center; width:90px;">Cash</td>
				<td class="text_b_w" style="text-align:center; width:90px;">Pt. Check</td>
				<td class="text_b_w" style="text-align:center; width:90px;">Ins. Check</td>
				<td class="text_b_w" style="text-align:center; width:90px;">Credit Card</td>
				<td class="text_b_w" style="text-align:center; width:90px;">EFT</td>
				<td class="text_b_w" style="text-align:center; width:90px;">Money Order</td>
			</tr>
			$pdf_part
			<tr>
				<td align="right" bgcolor="#FFFFFF" class="text_12b">Total Posted :</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPostCnt</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$subtotalPostedTotalAmt</td>					
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$paymentCnt</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitCash</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitPatCheck</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitCheck</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitCC</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitEFT</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$submitMoneyOrder</td>			
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>				
			<tr>
				<td align="right" bgcolor="#FFFFFF" class="text_12b">Grand Total :</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF"></td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$subtotalPostedTotalAmt</td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF" colspan="6"></td>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">$totalPaidAmount</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>				
DATA;
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
if(count($notSubmitedArr) > 0 || count($paidNotSubmitEncounterArr) > 0){
	//--- ALL PAYMENTS DETAILS -----
	$facilityArrKeys = array_keys($paidNotSubmitEncounterArr);
	for($p=0; $p<sizeof($facilityArrKeys); $p++){
		$facilityId = $facilityArrKeys[$p];
		$facilityName = $posFacilityArr[$facilityId];

		$pdf_part.=
			'<tr>
				<td class="text_b_w" align="left" colspan="10">Facility : '.$facilityName.'</td>
			</tr>';

		$providerArrKeys = array_keys($paidNotSubmitEncounterArr[$facilityId]);

		$subtotalPaymentCount= '';
		$subtotalPatientCash= '';
		$subtotalPatientCheck= '';
		$subtotalInsCheck= '';
		$subtotalPatientCredit= '';
		$subtotalPatientEFT= '';
		$subtotalPatientMoneyOrder= '';
		$subtotalPostedTotalAmt='';
		$subtotalPostCnt='';

		for($f=0; $f<sizeof($providerArrKeys); $f++){
			$providerId = $providerArrKeys[$f];
			$providerName = $userNameArr[$providerId];
			$encounterArrKeys = array_keys($paidNotSubmitEncounterArr[$facilityId][$providerId]);		

			$paymentCnt = 0;
			$notsubmitPatCheckArr = array();
			$notsubmitCheckArr = array();
			$notsubmitCashArr = array();
			$notsubmitCCArr = array();
			$notsubmitEFTArr=array();
			$notsumbitMoneyOrderArr=array();	

			for($e=0; $e<sizeof($encounterArrKeys); $e++){
				$encounter_id = $encounterArrKeys[$e];
				
				$enconuterPayment = $mainPaymentArr[$encounter_id]['payment'];
				$enconuterPaymentArr = $mainPaymentArr[$encounter_id]['payment']['payment_id'];
				$paymentCnt += count($enconuterPaymentArr);
				for($d=0;$d<count($enconuterPaymentArr);$d++){
					$encounter = $enconuterPaymentArr[$d];
					$paidForProc = str_replace(',','',$enconuterPayment[$encounter]['paidForProc']);
					$overPayment = str_replace(',','',$enconuterPayment[$encounter]['overPayment']);
					$paid_by = strtolower(trim($enconuterPayment[$encounter]['paid_by']));
					$payment_mode = strtolower(trim($enconuterPayment[$encounter]['payment_mode']));
					
					$t_paid_amount = $paidForProc + $overPayment;
					if($enconuterPayment[$encounter]['paymentClaims'] == 'Negative Payment'){
						$t_paid_amount = '-'.$t_paid_amount;
					}
					
					if($payment_mode == 'check'){
						if($paid_by == 'patient'){
							$notsubmitPatCheckArr[] = $t_paid_amount;
						}
						else{
							$notsubmitCheckArr[] = $t_paid_amount;
						}
					}
					else if($payment_mode == 'cash'){
						$notsubmitCashArr[] = $t_paid_amount;
					}
					else if($payment_mode == 'eft')
					{
						$notsubmitEFTArr[]=$t_paid_amount;
					}
					else if($payment_mode == 'money order')
					{
						$notsumbitMoneyOrderArr[]=$t_paid_amount;
					}				
					else{
						$notsubmitCCArr[] = $t_paid_amount;
					}
				}		
			}
			$notsubmitPatCheck = array_sum($notsubmitPatCheckArr);
			$notsubmitCheck = array_sum($notsubmitCheckArr);
			$notsubmitCash = array_sum($notsubmitCashArr);
			$notsubmitCC = array_sum($notsubmitCCArr);
			$notsubmitEFT=array_sum($notsubmitEFTArr);
			$notsubmitMoneyOrder=array_sum($notsumbitMoneyOrderArr);	
			
			//---- SUB TOTAL -----
			$subtotalPaymentCount += $paymentCnt;
			$subtotalPatientCash += $notsubmitCash;
			$subtotalPatientCheck += $notsubmitPatCheck;
			$subtotalInsCheck += $notsubmitCheck;
			$subtotalPatientCredit += $notsubmitCC;
			$subtotalPatientEFT+= $notsubmitEFT;
			$subtotalPatientMoneyOrder+= $notsubmitMoneyOrder;	
			$subtotalPostedTotalAmt += $notSubmitAmt;
			
			
			
			
			$paymentCnt = $paymentCnt > 0 ? $paymentCnt : NULL;

			//--- ALL POSTED CHARGES -------
			$notSubmitAmt = 0;
			$notSubData  =$notSubmitedArr[$facilityId][$providerId];
			for($s=0;$s<count($notSubData);$s++){
				$notSubmitAmt += $notSubData[$s]['totalAmount'];
				$dept_charge_list_id_arr[] = $notSubData[$s]['charge_list_id'];
				$departmentEncounterArr[] = $notSubData[$s]['encounter_id'];
			}
			$subtotalPostedTotalAmt += $notSubmitAmt;
			
			$not_submit_cnt = count($notSubData) > 0 ? count($notSubData) : NULL;
			$subtotalPostCnt += $not_submit_cnt;
				
			$notsubmitPatCheck = numberFormat($notsubmitPatCheck, 2);
			$notsubmitCheck = numberFormat($notsubmitCheck, 2);
			$notsubmitCC = numberFormat($notsubmitCC, 2);
			$notsubmitEFT= numberFormat($notsubmitEFT,2);
			$notsubmitCash = numberFormat($notsubmitCash, 2);
			$notsubmitMoneyOrder= numberFormat($notsubmitMoneyOrder,2);		
			$notSubmitAmt = numberFormat($notSubmitAmt, 2);	
			if(strlen($providerName)>22){
				$providerName=substr($providerName,0, 21).'..';
			}
			
			$pdf_part.='<tr>
				<td align="left" class="text_12" bgcolor="#FFFFFF">'.$providerName.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$not_submit_cnt.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$notSubmitAmt.'</td>					
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$paymentCnt.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$notsubmitCash.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$notsubmitPatCheck.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$notsubmitCheck.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$notsubmitCC.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$notsubmitEFT.'</td>
				<td align="right" class="text_12" bgcolor="#FFFFFF">'.$notsubmitMoneyOrder.'</td>			
			</tr>';	
		}
		$paidAmount = $subtotalPatientCash + $subtotalPatientCheck + $subtotalInsCheck + $subtotalPatientCredit + $subtotalPatientEFT + $subtotalPatientMoneyOrder;
		
		// GRAND TOTAL ARRAY
		$grdtotalPaymentCount+= $subtotalPaymentCount;
		$grdtotalPatientCash+= $subtotalPatientCash;
		$grdtotalPatientCheck+= $subtotalPatientCheck;
		$grdtotalInsCheck+= $subtotalInsCheck;
		$grdtotalPatientCredit+= $subtotalPatientCredit;
		$grdtotalPatientEFT+= $subtotalPatientEFT;
		$grdtotalPatientMoneyOrder+= $subtotalPatientMoneyOrder;
		$grdtotalPostedTotalAmt+= $subtotalPostedTotalAmt;
		$grdtotalPostCnt+= $subtotalPostCnt;
		$grdtotalPaidAmt += $subtotalPatientCash + $subtotalPatientCheck + $subtotalInsCheck + $subtotalPatientCredit + $subtotalPatientEFT + $subtotalPatientMoneyOrder;

		$grdtotalNotPostedTotalAmt += $subtotalPostedTotalAmt;
		
		//----------SUB TOTAL BLOCK FOR NOT POSTED ENCOUNTERS----------------
		$submitCash = numberFormat($subtotalPatientCash, 2);
		$submitPatCheck = numberFormat($subtotalPatientCheck, 2);
		$submitCheck = numberFormat($subtotalInsCheck, 2);
		$submitCC = numberFormat($subtotalPatientCredit, 2);
		$submitEFT= numberFormat($subtotalPatientEFT,2);
		$submitMoneyOrder= numberFormat($subtotalPatientMoneyOrder,2);
		$subtotalPostedTotalAmt= numberFormat($subtotalPostedTotalAmt,2);
		$paymentCnt = $subtotalPaymentCount > 0 ? $subtotalPaymentCount : NULL;		
		
		$grdNottotalPaidAmount += $paidAmount;
		$paidAmount= numberFormat($paidAmount,2);
		
		$pdf_part.='<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Sub Total :</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$subtotalPostCnt.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$subtotalPostedTotalAmt.'</td>					
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$paymentCnt.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitCash.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitPatCheck.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitCheck.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitCC.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitEFT.'</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">'.$submitMoneyOrder.'</td>			
		</tr>';
		$pdf_part.='<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Totals :</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF" colspan="9">'.$paidAmount.'</td>
		</tr>';
	}
	// MAIN TOTAL ARRAY
	///$maintotalPaymentCount+= $grdtotalPaymentCount;
	//$maintotalPatientCash+= $grdtotalPatientCash;
	//$maintotalPatientCheck+= $grdtotalPatientCheck;
	//$maintotalInsCheck+= $grdtotalInsCheck;
	//$maintotalPatientCredit+= $grdtotalPatientCredit;
	//$maintotalPatientEFT+= $grdtotalPatientEFT;
	//$maintotalPatientMoneyOrder+= $grdtotalPatientMoneyOrder;
	$maintotalPostedTotalAmt+= $grdtotalPostedTotalAmt;
	//$maintotalPostCnt+= $grdtotalPostCnt;
	
	$totalPaidAmount = $grdtotalPatientCash + $grdtotalPatientCheck + $grdtotalInsCheck + $grdtotalPatientCredit + $grdtotalPatientEFT + $grdtotalPatientMoneyOrder;
	// GRAND TOTALS	
	$submitCash = numberFormat($grdtotalPatientCash, 2);
	$submitPatCheck = numberFormat($grdtotalPatientCheck, 2);
	$submitCheck = numberFormat($grdtotalInsCheck, 2);
	$submitCC = numberFormat($grdtotalPatientCredit, 2);
	$submitEFT= numberFormat($grdtotalPatientEFT,2);
	$submitMoneyOrder= numberFormat($grdtotalPatientMoneyOrder,2);
	$subtotalPostedTotalAmt= numberFormat($grdtotalPostedTotalAmt,2);
	$paymentCnt = $grdtotalPaymentCount > 0 ? $grdtotalPaymentCount : NULL;		
	$totalPaidAmount= numberFormat($totalPaidAmount,2);
	
	//----------GRAND TOTAL BLOCK FOR NOT POSTED ENCOUNTERS----------------
	//$grdtotalPaymentCount = numberFormat($grdtotalPaymentCount, 2);
	$grdtotalPatientCash = numberFormat($grdtotalPatientCash, 2);
	$grdtotalPatientCheck = numberFormat($grdtotalPatientCheck, 2);
	$grdtotalInsCheck = numberFormat($grdtotalInsCheck, 2);
	$grdtotalPatientCredit = numberFormat($grdtotalPatientCredit, 2);
	$grdtotalPatientEFT = numberFormat($grdtotalPatientEFT, 2);
	$grdtotalPatientMoneyOrder = numberFormat($grdtotalPatientMoneyOrder, 2);
	$grdtotalPostedTotalAmt = numberFormat($grdtotalPostedTotalAmt, 2);
	//$grdtotalPostCnt = numberFormat($grdtotalPostCnt, 2);
	$grdtotalPaidAmt = numberFormat($grdtotalPaidAmt, 2);
		
	$pdfData .= <<<DATA
		<page backtop="9mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		$fileHeaderData
		<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">	
		<tr>
			<td colspan="10" class="text_b_w" width="700"><b>Charges Entered But Not Posted</b></td>
		</tr>
		<tr>
			<td class="text_b_w" style="text-align:center">Description</td>
			<td class="text_b_w" colspan="2" style="text-align:center">Charges Not Posted</td>
			<td class="text_b_w" colspan="7" style="text-align:center">Payment</td>
		</tr>
		<tr>
			<td class="text_b_w" style="text-align:center" style="width:105px">Physician</td>
			<td class="text_b_w" style="text-align:center" style="width:30px">Qty.</td>
			<td class="text_b_w" style="text-align:center" style="width:75px">Charges</td>					
			<td class="text_b_w" style="text-align:center" style="width:30px">Qty.</td>
			<td class="text_b_w" style="text-align:center" style="width:70px">Cash</td>
			<td class="text_b_w" style="text-align:center" style="width:75px">Pt. Check</td>
			<td class="text_b_w" style="text-align:center" style="width:75px">Ins. Check</td>
			<td class="text_b_w" style="text-align:center" style="width:70px">Credit Card</td>
			<td class="text_b_w" style="text-align:center" style="width:70px">EFT</td>
			<td class="text_b_w" style="text-align:center" style="width:70px">Money Order</td>			
		</tr>
		$pdf_part
		<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Total Posted :</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPostCnt</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPostedTotalAmt</td>					
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPaymentCount</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientCash</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientCheck</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalInsCheck</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientCredit</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientEFT</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientMoneyOrder</td>			
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
		<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Grand Total :</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF"></td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPostedTotalAmt</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF" colspan="6"></td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPaidAmt</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
		</table>
		</page>
DATA;

	//--- GET CSV DATA --
	$csvFileData .= <<<DATA
		<tr>
			<td colspan="10" class="text_b_w" width="700"><b>Charges Entered But Not Posted</b></td>
		</tr>
		<tr>
			<td class="text_b_w" style="text-align:center">Description</td>
			<td class="text_b_w" colspan="2" style="text-align:center">Charges Not Posted</td>
			<td class="text_b_w" colspan="7" style="text-align:center">Payment</td>
		</tr>
		<tr>
			<td class="text_b_w" style="text-align:center; width:150px;">Physician</td>
			<td class="text_b_w" style="text-align:center; width:40px;">Qty.</td>
			<td class="text_b_w" style="text-align:center; width:100px;">Charges</td>					
			<td class="text_b_w" style="text-align:center; width:40px;">Qty.</td>
			<td class="text_b_w" style="text-align:center; width:90px;">Cash</td>
			<td class="text_b_w" style="text-align:center; width:90px;">Pt. Check</td>
			<td class="text_b_w" style="text-align:center; width:90px;">Ins. Check</td>
			<td class="text_b_w" style="text-align:center; width:90px;">Credit Card</td>
			<td class="text_b_w" style="text-align:center; width:90px;">EFT</td>
			<td class="text_b_w" style="text-align:center; width:90px;">Money Order</td>
		</tr>
		$pdf_part
		<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Total :</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPostCnt</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPostedTotalAmt</td>					
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPaymentCount</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientCash</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientCheck</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalInsCheck</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientCredit</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientEFT</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPatientMoneyOrder</td>			
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
		<tr>
			<td align="right" bgcolor="#FFFFFF" class="text_12b">Grand Total :</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF"></td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPostedTotalAmt</td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF" colspan="6"></td>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">$grdtotalPaidAmt</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
DATA;
}


//--- ALL RE SUBMITTED CHARGES -----
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

if(count($mainPaymentArr) >0 || count($submitedArr) >0 || count($notSubmitedArr)>0 ){
	
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

//--- REFUND BLOCK ----------
if(count($refund_arr)){ 
	$countRes=0;
	$totalAmount_arr=array();
	$totalPayAmount_arr=array();
	$totalRefundAmt_arr=array();
	$overPayment_arr=array();
	$totalPayAmount_arr=array();
	$pdfDataRef2='';
	foreach($refund_arr as $facility_id => $phyData){
		$pdfDataRef2 .='<tr>
						<td class="text_b_w" colspan="4" align="left">Facility : '.$posFacilityArr[$facility_id].'</td>
					</tr>';
		$csvFileData2 .='<tr>
						<td class="text_b_w" colspan="4" align="left">Facility : '.$posFacilityArr[$facility_id].'</td>
					</tr>';			
		$totalAmount_sub_arr=array();	
		$totalRefundAmt_sub_arr=array();	
		$totalPayAmount_sub_arr=array();		

		foreach($phyData as $physician_id => $refundData){
			$countRes++;
			$totalAmount= $amtPaid = $refundAmt = 0;
			$cptCodes='';
			
			$physician_name = $userNameArr[$physician_id];
			$totalAmount= $refundData['totalAmount'];
			$amtPaid= $refundData['paidForProc'];
			$refundAmt = $refundData['refundAmt'];
			
			$totalAmount_sub_arr[]=$totalAmount;
			$totalRefundAmt_sub_arr[]=$refundAmt;
			$totalPayAmount_sub_arr[]=$amtPaid;
			
			$totalAmount_arr[]=$totalAmount;
			$totalRefundAmt_arr[]=$refundAmt;
			$totalPayAmount_arr[]=$amtPaid;
			
			//--- PDF FILE DATA ----
			$pdfDataRef2 .='<tr>
			<td class="text_12" bgcolor="#FFFFFF" width="185" align="left">'.$physician_name.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="175" style="text-align:right"> $'.number_format($totalAmount,2).'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="175" style="text-align:right"> $'.number_format($amtPaid,2).'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="175" style="text-align:right;padding-right:20px;"> $'.number_format($refundAmt,2).'</td>
			</tr>';
			$csvFileData2 .='<tr>
			<td bgcolor="#FFFFFF" width="100" align="left" class="text_12" >'.$physician_name.'</td>
			<td bgcolor="#FFFFFF" width="120" style="text-align:right" class="text_12" >$'.number_format($totalAmount,2).'</td>
			<td bgcolor="#FFFFFF" width="120" style="text-align:right" class="text_12" >$'.number_format($amtPaid,2).'</td>
			<td bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;" class="text_12" >$'.number_format($refundAmt,2).'</td>
			</tr>';		
		}
		$pdfDataRef2 .='
		<tr><td bgcolor="#FFFFFF" colspan="4"></td></tr>
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$'.number_format(array_sum($totalAmount_sub_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$'.number_format(array_sum($totalPayAmount_sub_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;padding-right:20px;">$'.number_format(array_sum($totalRefundAmt_sub_arr),2).'</td>
		</tr>
		<tr><td bgcolor="#FFFFFF" colspan="4"></td></tr>';
		$csvFileData2 .='
		<tr><td class="total-row" colspan="4"></td></tr>	
		<tr>
		<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Sub Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format(array_sum($totalAmount_sub_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format(array_sum($totalPayAmount_sub_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> $'.number_format(array_sum($totalRefundAmt_sub_arr),2).'</td>
		</tr>
		<tr><td class="total-row" colspan="4"></td></tr>';
	}
	$pdfData.= '
	'.$fileHeaderData.'
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">						
	<tr><td colspan="4" class="text_b_w"><strong>Refund Amounts</strong></td></tr>
	<tr>
		<td class="text_b_w" width="185" style="text-align:center">Physician</td>
		<td class="text_b_w" width="180" style="text-align:center">Charges</td>
		<td class="text_b_w" width="180" style="text-align:center">Paid Amount</td>
		<td class="text_b_w" width="185" style="text-align:center">Refund Amount</td>
	</tr>
	</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
	'.$pdfDataRef2.'
	<tr><td class="total-row" colspan="4"></td></tr>
	<tr>
		<td class="text_12b" bgcolor="#FFFFFF" align="right">Grand Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"> $'.number_format(array_sum($totalAmount_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"> $'.number_format(array_sum($totalPayAmount_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;padding-right:20px;"> $'.number_format(array_sum($totalRefundAmt_arr),2).'</td>
	</tr>
	<tr><td class="total-row" colspan="4"></td></tr>
	</table>';

	$csvFileData.= $fileHeaderData.'
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">					
	<tr><td colspan="4" class="text_b_w"><strong>Refund Amounts</strong></td></tr>
	<tr>
		<td class="text_b_w" width="90" style="text-align:center">Physician</td>
		<td class="text_b_w" width="140" style="text-align:center">Charges</td>
		<td class="text_b_w" width="140" style="text-align:center">Paid Amount</td>
		<td class="text_b_w" width="140" style="text-align:center">Refund Amount</td>
	</tr>
	'.$csvFileData2.'
	<tr><td class="total-row" colspan="4"></td></tr>
	<tr>
		<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Grand Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format(array_sum($totalAmount_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format(array_sum($totalPayAmount_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> $'.number_format(array_sum($totalRefundAmt_arr),2).'</td>
	</tr>
	<tr><td class="total-row" colspan="4"></td></tr>
	</table>';
}
//END REFUND BLOCK	

//OPERATOR DATA
$csvFileData.=$oper_data;
//- END OPER DATA

?>