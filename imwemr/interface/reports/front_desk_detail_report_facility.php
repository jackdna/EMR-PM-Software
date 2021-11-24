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
FILE : FRONTDESKDETAILREPORTFACILITY.PHP
PURPOSE :  GET PATIENT RECORDS
ACCESS TYPE : DIRECT
*/
$paid_encounter_str = join(',',$paid_encounter_arr);
if(empty($paid_encounter_str) === false){
	$facIdArr = array_keys($facilityIdArr);
	$grandTotalPayment = array();
	for($i=0;$i<count($facIdArr);$i++){
		$facilityId = (int)$facIdArr[$i];
		$providerIdKeys = array_keys($facilityIdArr[$facilityId]);
		$facilityName = $posFacilityArr[$facilityId];

		$pdfData2 = NULL;
		$subCashTotal = 0;
		$subCheckTotal = 0;
		$subCCTotal = 0;
		$sub_adj_amt_arr = array();
		$pat_id_arr = array();		
		
		$pdfData2.= <<<DATA
		<tr>
			<td class="text_b_w" style="text-align:left" colspan="12">Facility : $facilityName</td>
		</tr>
DATA;
		for($f=0; $f<sizeof($providerIdKeys); $f++){
			$primaryProviderId = $providerIdKeys[$f];
			$userName = $userNameArr[$primaryProviderId];

			$facQty= '';
			$facCashTotal= '';
			$facCheckTotal= '';
			$facCCTotal = '';
			$facEftTotal= '';
			$facMoneyOrderTotal= '';
			$facAdjAmt= '';			

			$pdfData2.= <<<DATA
			<tr>
				<td class="text_b_w" style="text-align:left" colspan="12">Physician : $userName</td>
			</tr>
DATA;
			$proEncounter = $paidProEncounter[$facilityId][$primaryProviderId];

			foreach($proEncounter as $encounterId){
				$charge_listArr = $mainPaymentArr[$encounterId]['charge_list'];
				$pat_name_arr = array();
				$pat_name_arr['LAST_NAME'] = $charge_listArr['lname'];
				$pat_name_arr['FIRST_NAME'] = $charge_listArr['fname'];
				$pat_name_arr['MIDDLE_NAME'] = $charge_listArr['mname'];
				$patientName = changeNameFormat($pat_name_arr);
				
				//--- PAYMENT DETAILS -------
				$paymentArr = $mainPaymentArr[$encounterId]['payment'];
				$paymentIdArr = $paymentArr['payment_id'];
				$payment_mode = array();
				$payment_cnt = count($paymentIdArr);
				$cashPaymentArr = array();
				$checkPaymentArr = array();
				$CCPaymentArr = array();
				$operatorNameArr = array();
				$eftPaymentArr=array();
				$moneyOrderPaymentArr=array();
				for($m=0;$m<count($paymentIdArr);$m++){
					$paymentId = $paymentIdArr[$m];
					$paymentmode = strtolower(trim($paymentArr[$paymentId]['payment_mode']));
					$checkNo = $paymentArr[$paymentId]['checkNo'];
					$creditCardNo = $paymentArr[$paymentId]['creditCardNo'];
					$creditCardCo = $paymentArr[$paymentId]['creditCardCo'];
	
					$operatorNameArr[] = $paymentArr[$paymentId]['operatorInitial'];
					
					//--- TOTAL PAID AMOUNT --
					$paidForProc = str_replace(',','',$paymentArr[$paymentId]['paidForProc']);
					$paidForProc += str_replace(',','',$paymentArr[$paymentId]['overPayment']);
					if($paymentArr[$paymentId]['paymentClaims'] == 'Negative Payment'){
						$paidForProc = '-'.$paidForProc;
					}
					
					//--- GET GRAND TOTAL AMOUNT ---
					$grandTotalPayment[] = $paidForProc;
					
					if($paymentmode == 'cash'){
						$cashPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode;
					}
					else if($paymentmode == 'check'){
						$checkPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode.' - '.substr($checkNo,-5);
					}
					else if($paymentmode=='eft')
					{
						$eftPaymentArr[]=$paidForProc;
						$payment_mode[] = $paymentmode;
					}
					else if($paymentmode== 'money order')
					{
						$moneyOrderPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode;
					}
					else{
						$CCPaymentArr[] = $paidForProc;
						$payment_mode[] = $creditCardCo.' - '.substr($creditCardNo,0,4);
					}
				}
				
				$cashPayment = array_sum($cashPaymentArr);
				$checkPayment = array_sum($checkPaymentArr);
				$CCPayment = array_sum($CCPaymentArr);
				$eftPayment = array_sum($eftPaymentArr);
				$moneyOrderPayment = array_sum($moneyOrderPaymentArr);
				
				//--- ADJUSTMENT AMOUNT -----
				$adjAmt = NULL;
				if($arrAdjustmentAmt[$encounterId]){
					$adjAmt = $arrAdjustmentAmt[$encounterId];
				}
				$sub_adj_amt_arr[] = $adjAmt;
			
				// PHYSICIAN TOTALS
				$phyQty += $payment_cnt;
				$phyCashTotal += $cashPayment;
				$phyCheckTotal += $checkPayment;
				$phyCCTotal += $CCPayment;
				$phyEftTotal += $eftPayment;
				$phyMoneyOrderTotal += $moneyOrderPayment;
				$phyAdjAmt += $adjAmt;
				
				$subCashTotal += $cashPayment;
				$subCheckTotal += $checkPayment;
				$subCCTotal += $CCPayment;
				$subEftTotal += $eftPayment;
				$subMoneyOrderTotal += $moneyOrderPayment;
				
				$cashPayment = numberFormat($cashPayment, 2,'yes');
				$checkPayment = numberFormat($checkPayment, 2,'yes');
				$CCPayment = numberFormat($CCPayment, 2,'yes');
				$adjAmt = numberFormat($adjAmt, 2);
				$eftPayment= numberFormat($eftPayment,2,'yes');
				$moneyOrderPayment= numberFormat($moneyOrderPayment,2,'yes');
				
				$paymentMode = join(', ',array_unique($payment_mode));
				$operatorNameStr = join(', ',array_unique($operatorNameArr));
				$date_of_service = $charge_listArr['date_of_service'];
				$encounter_id = $charge_listArr['encounter_id'];
				$patientName = trim($patientName).' - '.$charge_listArr['id'];
				$pat_id_arr[] = $charge_listArr['id'];
				
				$cashPayment = ($cashPayment=='') ? '$0.00' : $cashPayment;
				$checkPayment = ($checkPayment=='') ? '$0.00' : $checkPayment;
				$CCPayment = ($CCPayment=='') ? '$0.00' : $CCPayment;
				$eftPayment = ($eftPayment=='') ? '$0.00' : $eftPayment;
				$moneyOrderPayment = ($moneyOrderPayment=='') ? '$0.00' : $moneyOrderPayment;
				
				$pdfData2 .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="170" align="left">$patientName</td>
						<td class="text_12" bgcolor="#FFFFFF" width="90" align="center">$date_of_service</td>
						<td class="text_12" bgcolor="#FFFFFF" width="70" align="center">$encounter_id</td>
						<td class="text_12" bgcolor="#FFFFFF" width="80" align="left">$paymentMode</td>
						<td class="text_12" bgcolor="#FFFFFF" width="30" align="center">$payment_cnt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="90">$cashPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="90">$checkPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="90">$CCPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="90">$eftPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="90">$moneyOrderPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$adjAmt</td>
						<td class="text_12" bgcolor="#FFFFFF" width="45" style="text-align:right">$operatorNameStr</td>
					</tr>
DATA;
			}
			// PHYSICIAN TOTALS DATA
			$phyCashTotal = numberFormat($phyCashTotal, 2);
			$phyCheckTotal = numberFormat($phyCheckTotal, 2);
			$phyCCTotal = numberFormat($phyCCTotal, 2);
			$phyEftTotal= numberFormat($phyEftTotal,2);
			$phyMoneyOrderTotal= numberFormat($phyMoneyOrderTotal,2);
			$phyAdjAmt = numberFormat($phyAdjAmt, 2);

			$pdfData2.= <<<DATA
				<tr>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:right" colspan="4">Physician Total:</td>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:center">$phyQty</td>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:right">$phyCashTotal</td>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:right">$phyCheckTotal</td>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:right">$phyCCTotal</td>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:right">$phyEftTotal</td>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:right">$phyMoneyOrderTotal</td>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:right">$phyAdjAmt</td>
					<td bgcolor="#FFFFFF" class="text_12b" style="text-align:right"></td>
				</tr>
DATA;
		}
		
		$pat_id_count = count($pat_id_arr);		

		$userName = $userNameArr[$primaryProviderId];
		$subCashTotal2 = $subCashTotal + $subCheckTotal + $subCCTotal + $subEftTotal + $subMoneyOrderTotal;
		$sub_adj_amt = array_sum($sub_adj_amt_arr);

		$subCashTotal = numberFormat($subCashTotal, 2);
		$subCheckTotal = numberFormat($subCheckTotal, 2);
		$subCCTotal = numberFormat($subCCTotal, 2);
		$subCashTotal2 = numberFormat($subCashTotal2, 2);
		$subEftTotal= numberFormat($subEftTotal,2);
		$subMoneyOrderTotal= numberFormat($subMoneyOrderTotal,2);
		$sub_adj_amt = numberFormat($sub_adj_amt, 2);

		$footerTotalData = NULL;
		if($i == count($facilityIdArr)-1 and count($grandTotalPayment) > 0){
			//--- GRNAD TOTAL PAYMENTS OF ALL PROVIDERS ----
			$TotalPayments = array_sum($grandTotalPayment);
			$TotalPayments = numberFormat($TotalPayments,2);
			
			$footerTotalData .= <<<DATA
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_12b">Total Payments :</td>
					<td align="left" colspan="7" bgcolor="#FFFFFF" class="text_12b">$TotalPayments</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
DATA;
		}
		
		//--- PDF FILE DATA ----
		$pdfData .= <<<DATA
			<page backtop="19mm" backbottom="10mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$page_header_val
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
				<tr>
					<td width="100%" align="left" colspan="12" class="text_b_w">Payments</td>
				</tr>
				<tr>
					<td align="center" width="170" class="text_b_w">Patient Name-ID</td>
					<td align="center" width="80" class="text_b_w">DOS</td>
					<td align="center" width="60" class="text_b_w">E.ID</td>
					<td align="center" width="80" class="text_b_w">Method</td>
					<td align="center" width="30" class="text_b_w">Qty.</td>
					<td align="center" width="440" class="text_b_w" colspan="5">Payments</td>
					<td align="center" width="100" class="text_b_w">Adjustment</td>
					<td align="center" class="text_b_w">Opr</td>
				</tr>
				<tr>
					<td align="center" class="text_b_w" colspan="5"></td>
					<td align="center" width="90" class="text_b_w">Cash</td>
					<td align="center" width="90" class="text_b_w">Check</td>
					<td align="center" width="90" class="text_b_w">Credit Card</td>
					<td align="center" width="90" class="text_b_w">EFT</td>
					<td align="center" width="90" class="text_b_w">Money Order</td>					
					<td align="center" class="text_b_w"></td>
					<td align="center" class="text_b_w"></td>
				</tr>				
			</table>
			</page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
				$pdfData2
				<tr>
					<td colspan="5" class="text_12b" style="text-align:right">SubTotal :</td>
					<td style="text-align:right" class="text_12b">$subCashTotal</td>
					<td style="text-align:right" class="text_12b">$subCheckTotal</td>
					<td style="text-align:right" class="text_12b">$subCCTotal</td>
					<td style="text-align:right" class="text_12b">$subEftTotal</td>
					<td style="text-align:right" class="text_12b">$subMoneyOrderTotal</td>					
					<td style="text-align:right" class="text_12b">$sub_adj_amt</td>
					<td class="text_12b">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:right" class="text_12b" colspan="5">Total Claims :</td>
					<td align="left" colspan="7" class="text_12b">$pat_id_count</td>
				</tr>				
				<tr>
					<td style="text-align:right" class="text_12b" colspan="5">Total :</td>
					<td align="left" colspan="7" class="text_12b">$subCashTotal2</td>
				</tr>
				$footerTotalData
			</table>
			</page>
DATA;
		
		//--- GET CSV DATA ---
		$csvFileData .= <<<DATA
			$page_header_val
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">				
				<tr>
					<td width="100%" align="left" colspan="12" class="text_b_w">Payments</td>
				</tr>
				<tr>
					<td align="center" width="180" class="text_b_w">Patient Name-ID</td>
					<td align="center" width="100" class="text_b_w">DOS</td>
					<td align="center" width="100" class="text_b_w">E.ID</td>
					<td align="center" width="100" class="text_b_w">Method</td>
					<td align="center" width="30" class="text_b_w">Qty.</td>
					<td width="600" class="text_b_w" colspan="5" style="text-align:center">Payments</td>					
					<td align="center" width="100" class="text_b_w">Adjustment</td>
					<td align="center" width="30" class="text_b_w">Opr</td>
				</tr>
				<tr>
					<td align="center" class="text_b_w" colspan="5"></td>
					<td align="center" width="120" class="text_b_w">Cash</td>
					<td align="center" width="120" class="text_b_w">Check</td>
					<td align="center" width="120" class="text_b_w">Credit Card</td>
					<td align="center" width="120" class="text_b_w">EFT</td>
					<td align="center" width="120" class="text_b_w">Money Order</td>					
					<td align="center" class="text_b_w" colspan="2"></td>
				</tr>				
				$pdfData2
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td bgcolor="#FFFFFF" class="text_12b" colspan="2" style="text-align:right">SubTotal :</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCashTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCheckTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCCTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subEftTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subMoneyOrderTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$sub_adj_amt</td>
					<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" class="text_12b" bgcolor="#FFFFFF">Total Claims :</td>
					<td align="left" colspan="7" bgcolor="#FFFFFF" class="text_12b">$pat_id_count</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" class="text_12b" bgcolor="#FFFFFF">Total :</td>
					<td align="left" colspan="7" bgcolor="#FFFFFF" class="text_12b">$subCashTotal2</td>
				</tr>
				$footerTotalData
			</table>
DATA;
		$page_header_val = NULL;
	}	
}

//--- GET ALL POSTED RECORDS ----------
if(count($mainPostedArr)){ 
	$mainPostedArr = array_values($mainPostedArr);
	$totalPostedChargesArr = array();
	for($i=0;$i<count($mainPostedArr);$i++){
		$pdfData2 = NULL;
		$facilityId = (int)$mainPostedArr[$i];
		$facilityName = $posFacilityArr[$facilityId];
		$providerArr = $mainPostedEncounterArr[$facilityId];
		$providerArrKyes = array_keys($mainPostedEncounterArr[$facilityId]);
		$totalCharges = 0;
		$totalChargesPhyArrSum = array();
		$pdfData2.= <<<DATA
			<tr>
				<td class="text_b_w" align="left" colspan="11">Facility : $facilityName</td>
			</tr>
DATA;
		$totalClaims = 0;
		for($f=0; $f<sizeof($providerArrKyes); $f++){
			$primaryProviderId =  $providerArrKyes[$f];
			$userName = $userNameArr[$primaryProviderId];
			$facilityChgIdArr = $providerArr[$primaryProviderId];
			$chargeListIdStr = join(',',$facilityChgIdArr);

			$pdfData2.= <<<DATA
				<tr>
					<td class="text_b_w" align="left" colspan="11">Physician : $userName</td>
				</tr>
DATA;
			
			$query = "select patient_charge_list_details.charge_list_id,
					patient_charge_list_details.charge_list_detail_id, patient_charge_list_details.totalAmount,
					patient_charge_list_details.diagnosis_id1, patient_charge_list_details.diagnosis_id2,
					patient_charge_list_details.diagnosis_id3, patient_charge_list_details.diagnosis_id4,
					patient_charge_list_details.posFacilityId, cpt_fee_tbl.cpt4_code,cpt_fee_tbl.cpt_desc,
					cpt_fee_tbl.departmentId, patient_charge_list_details.charge_list_id,
					DATE_FORMAT(trans_del_date, '%Y-%m-%d') as 'trans_del_date', del_status 
					from patient_charge_list_details 
					join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
					where patient_charge_list_details.charge_list_id in($chargeListIdStr) 
					AND patient_charge_list_details.posted_status='1'
					";
				
			$chargeListArr = array();
			$chargeListDetailArr = array();	
			while($qryRes  =imw_fetch_array($query)){
				$detailArr[] = $qryRes;
			}
			for($j=0;$j<count($detailArr);$j++){
				if(($detailArr[$j]['del_status']=='0') || ($detailArr[$j]['del_status']=='1' && $detailArr[$j]['trans_del_date']>$endDate)){
					$charge_list_id = $detailArr[$j]['charge_list_id'];
					$charge_list_detail_id = $detailArr[$j]['charge_list_detail_id'];
					$chargeListDetailArr[$charge_list_id][] = $charge_list_detail_id;
					$chargeListArr[$charge_list_detail_id] = $detailArr[$j];
				}
			}
			$patien_cnt = 0;
			$subCharge = 0;
			for($p=0;$p<count($facilityChgIdArr);$p++){
				$charge_list_id = $facilityChgIdArr[$p];
				
				$detailsArr = $mainPostedFacilityArr[$facilityId][$primaryProviderId][$charge_list_id];
				$pat_name_arr = array();
				$pat_name_arr['LAST_NAME'] = $detailsArr['lname'];
				$pat_name_arr['FIRST_NAME'] = $detailsArr['fname'];
				$pat_name_arr['MIDDLE_NAME'] = $detailsArr['mname'];
							
				$patientName = changeNameFormat($pat_name_arr);
				$patientName .= " - ".$detailsArr['id'];
				
				$first_posted_opr_id = $detailsArr['first_posted_opr_id'];			
				$date_of_service = $detailsArr['date_of_service'];
				$encounter_ids = $detailsArr['encounter_id'];
				$patientNameChk = false;
				$detailArr = $chargeListDetailArr[$charge_list_id];
				
				if($first_posted_opr_id_old !=$first_posted_opr_id && $p>0){
					$subCharge = numberFormat($subCharge,2);
					$pdfData2 .= <<<DATA
						<tr>							
							<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right" colspan="9">Sub Total :</td>
							<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$subCharge</td>
							<td class="text_12b" bgcolor="#FFFFFF"></td>
						</tr>
DATA;
				$subCharge = 0;
				}
				$first_posted_opr_id_old =$first_posted_opr_id;
				
				for($d=0;$d<count($detailArr);$d++){
					if($patientNameChk == true){
						$patientName = NULL;
						$countRes = NULL;
					}
					else{
						$patien_cnt++;
						$totalClaims++;
						$countRes = $patien_cnt;
					}
					
					$detailId = $detailArr[$d];
					$detailIdArr = $chargeListArr[$detailId];
					$cpt_desc = $detailIdArr['cpt_desc'];
					$cpt4_code = $detailIdArr['cpt4_code'];
					
					$diagnosisArr = array();
					if(empty($detailIdArr['diagnosis_id1']) === false){
						$diagnosisArr[] = $detailIdArr['diagnosis_id1'];
					}
					if(empty($detailIdArr['diagnosis_id2']) === false){
						$diagnosisArr[] = $detailIdArr['diagnosis_id2'];
					}
					if(empty($detailIdArr['diagnosis_id3']) === false){
						$diagnosisArr[] = $detailIdArr['diagnosis_id3'];
					}
					if(empty($detailIdArr['diagnosis_id4']) === false){
						$diagnosisArr[] = $detailIdArr['diagnosis_id4'];
					}
					
					$diagnosis = join(', ',$diagnosisArr);
					$facility_name = $detailIdArr['facilityPracCode'];
					$departmentId = $detailIdArr['departmentId'];
					$DepartmentCode = $departMentArr[$departmentId];
					$posFacilityId = $detailIdArr['posFacilityId'];
					$facility_name = $posFacilityArr[$posFacilityId];
					$subCharge += $detailIdArr['totalAmount'];
					$totalCharges += $detailIdArr['totalAmount'];
					$totalChargesPhyArrSum[] = $detailIdArr['totalAmount'];
					$totalPostedChargesArr[] = $detailIdArr['totalAmount'];
					$totalAmount = numberFormat($detailIdArr['totalAmount'],2);
					$oprName = $userNameTwoCharArr[$first_posted_opr_id];
					
					$pdfData2 .= <<<DATA
						<tr>
							<td class="text_12" bgcolor="#FFFFFF" width="30" align="center">$countRes</td>
							<td class="text_12" bgcolor="#FFFFFF" width="180">$patientName</td>
							<td class="text_12" bgcolor="#FFFFFF" align="left" width="100">$date_of_service</td>
							<td class="text_12" bgcolor="#FFFFFF" width="70">$encounter_ids</td>
							<td class="text_12" bgcolor="#FFFFFF" width="90">$cpt4_code</td>
							<td class="text_12" bgcolor="#FFFFFF" width="130" align="left">$cpt_desc</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$diagnosis</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$facility_name</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$DepartmentCode</td>
							<td class="text_12" bgcolor="#FFFFFF" width="110" style="text-align:right">$totalAmount</td>
							<td class="text_12" bgcolor="#FFFFFF" width="30" style="text-align:center">$oprName</td>
						</tr>
DATA;
	
					$patientNameChk = true;
				}
				if($p == count($facilityChgIdArr)-1){
					$subCharge = numberFormat($subCharge,2);
					$pdfData2 .= <<<DATA
						<tr>							
							<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right" colspan="9">Sub Total :</td>
							<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$subCharge</td>
							<td class="text_12b" bgcolor="#FFFFFF"></td>
						</tr>
DATA;
				$subCharge = 0;
				}
			}
			
			$totalCharges = numberFormat($totalCharges, 2);
			$totalChargesPhyArrSumVal = numberFormat(array_sum($totalChargesPhyArrSum), 2);
			$footerTotalData = NULL;
			if($i == count($mainPostedArr)-1){
				$totalPostedCharges = numberFormat(array_sum($totalPostedChargesArr),2);
				$footerTotalData .= <<<DATA
					<tr><td class="total-row" colspan="11"></td></tr>
					<tr>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" colspan="2" style="text-align:right">Total Posted Amount:</td>
						<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$totalPostedCharges</td>
						<td class="text_12b" bgcolor="#FFFFFF"></td>
					</tr>
					<tr><td class="total-row" colspan="11"></td></tr>
DATA;
			}
		}
		//--- PDF FILE DATA ----
		$pdfData .= <<<DATA
			<page backtop="17mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>		
			<page_header>
				$page_header_val
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt rpt_table rpt_table-bordered rpt_padding">					
					<tr>
						<td class="text_b_w" bgcolor="#FFFFFF" colspan="11">Total Posted Charges</td>
					</tr>
					<tr>
						<td class="text_b_w" width="20" align="center">Qty</td>
						<td class="text_b_w" width="170" align="center">Patient Name-ID</td>
						<td class="text_b_w" width="90" align="center">DOS</td>
						<td class="text_b_w" width="70" align="center">E.ID</td>
						<td class="text_b_w" width="90" align="center">CPT</td>
						<td class="text_b_w" width="140" align="center">Description</td>
						<td class="text_b_w" width="100" align="center">DX Code</td>
						<td class="text_b_w" width="90" align="center">POS</td>
						<td class="text_b_w" width="90" align="center">DPT.</td>
						<td class="text_b_w" width="110" align="center">Charges</td>
						<td class="text_b_w" width="30" align="center">Opr</td>
					</tr>
				</table>
			</page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
				$pdfData2
				<tr>
					<td class="text_12b" colspan="9" style="text-align:right">Total Claims : </td>
					<td class="text_12b" style="text-align:right">$totalClaims</td>
					<td class="text_12b"></td>
				</tr>				
				<tr>
					<td class="text_12b" colspan="9" style="text-align:right">Total :</td>
					<td class="text_12b" style="text-align:right">$totalChargesPhyArrSumVal</td>
					<td class="text_12b"></td>
				</tr>
				$footerTotalData
			</table>
			</page>
DATA;
		//--- GET CSV DATA ---
		$csvFileData .= <<<DATA
			$page_header_val
			<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">					
				<tr>
					<td class="text_b_w" colspan="11">Total Posted Charges</td>
				</tr>
				<tr>
					<td class="text_b_w" width="20" align="center">Qty</td>
					<td class="text_b_w" width="180" align="center">Patient Name-ID</td>
					<td class="text_b_w" width="90" align="center">DOS</td>
					<td class="text_b_w" width="70" align="center">E.ID</td>
					<td class="text_b_w" width="90" align="center">CPT</td>
					<td class="text_b_w" width="140" align="center">Description</td>
					<td class="text_b_w" width="100" align="center">DX Code</td>
					<td class="text_b_w" width="90" align="center">POS</td>
					<td class="text_b_w" width="90" align="center">DPT.</td>
					<td class="text_b_w" width="110" align="center">Charges</td>
					<td class="text_b_w" width="30" align="center">Opr</td>
				</tr>
				$pdfData2
				<tr>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">Total Claims:</td>
					<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$totalClaims</td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
				</tr>
				<tr>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">Total :</td>
					<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$totalChargesPhyArrSumVal</td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
				</tr>
				$footerTotalData
			</table>
DATA;
		$page_header_val = NULL;
	}
}

//--- GET ALL NOT POSTED CHARGES ----------
$mainNotPostedArr = array_values($mainNotPostedArr);
$totalPostedChargesArr = array();
if(count($mainNotPostedArr)>0){
	for($p=0;$p<count($mainNotPostedArr);$p++){
		$totalCharges = 0;
		$pdfData2 = NULL;
		$facilityId = (int)$mainNotPostedArr[$p];
		$facilityName = $posFacilityArr[$facilityId];
		$providerArr = $mainNotPostedEncounterArr[$facilityId];
		$providerArrKyes = array_keys($mainNotPostedEncounterArr[$facilityId]);

		$pdfData2.= <<<DATA
			<tr>
				<td class="text_b_w" align="left" colspan="11">Facility : $facilityName</td>
			</tr>
DATA;
		
		for($f=0; $f<sizeof($providerArrKyes); $f++){
			
			$primaryProviderId =  $providerArrKyes[$f];
			$userName = $userNameArr[$primaryProviderId];
			$facilityChgIdArr = $providerArr[$primaryProviderId];
			$chargeListIdStr = join(',',$facilityChgIdArr);

			$pdfData2.= <<<DATA
				<tr>
					<td class="text_b_w" align="left" colspan="11">Physician : $userName</td>
				</tr>
DATA;
		
			$query = "select patient_charge_list_details.charge_list_id,
					patient_charge_list_details.charge_list_detail_id, patient_charge_list_details.totalAmount,
					patient_charge_list_details.diagnosis_id1, patient_charge_list_details.diagnosis_id2,
					patient_charge_list_details.diagnosis_id3, patient_charge_list_details.diagnosis_id4,
					patient_charge_list_details.posFacilityId, cpt_fee_tbl.cpt4_code,cpt_fee_tbl.cpt_desc,
					cpt_fee_tbl.departmentId, patient_charge_list_details.charge_list_id,
					DATE_FORMAT(trans_del_date, '%Y-%m-%d') as 'trans_del_date', del_status 
					from patient_charge_list_details 
					join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
					where patient_charge_list_details.charge_list_id in($chargeListIdStr)";
			$chargeListArr = array();
			$chargeListDetailArr = array();
			while($qryRes  =imw_fetch_array($query)){
				$detailArr[] = $qryRes;
			}
			for($j=0;$j<count($detailArr);$j++){
				if(($detailArr[$j]['del_status']=='0') || ($detailArr[$j]['del_status']=='1' && $detailArr[$j]['trans_del_date']>$endDate)){
					$charge_list_id = $detailArr[$j]['charge_list_id'];
					$charge_list_detail_id = $detailArr[$j]['charge_list_detail_id'];
					$chargeListDetailArr[$charge_list_id][] = $charge_list_detail_id;
					$chargeListArr[$charge_list_detail_id] = $detailArr[$j];
				}
			}
			$subCharge = 0;
			$patien_cnt = 0;
			for($c=0;$c<count($facilityChgIdArr);$c++){
				$charge_list_id = (int)$facilityChgIdArr[$c];
				$detailsArr = $mainNotPostedFacilityArr[$facilityId][$primaryProviderId][$charge_list_id];
				$pat_name_arr = array();
				$pat_name_arr['LAST_NAME'] = $detailsArr['lname'];
				$pat_name_arr['FIRST_NAME'] = $detailsArr['fname'];
				$pat_name_arr['MIDDLE_NAME'] = $detailsArr['mname'];
				$patientName = $objDataManage->__changeNameFormat($pat_name_arr);
				
				$patientName.=" - ".$detailsArr['id'];
				
				$operatorId = $detailsArr['operator_id'];			
				$date_of_service = $detailsArr['date_of_service'];
				$encounter_ids = $detailsArr['encounter_id'];
				$patientNameChk = false;
				$detailArr = $chargeListDetailArr[$charge_list_id];

				if($operatorId_old!=$operatorId && $c>0){
					$subCharge = numberFormat($subCharge,2);
					$pdfData2 .= <<<DATA
						<tr>
							<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">Sub Total</td>
							<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$subCharge</td>
							<td class="text_12b" bgcolor="#FFFFFF"></td>
						</tr>
DATA;
				$subCharge = 0;
				}
				$operatorId_old=$operatorId;				
				
				for($d=0;$d<count($detailArr);$d++){
					if($patientNameChk == true){
						$patientName = NULL;
						$countRes = NULL;
					}
					else{
						$patien_cnt++;
						$countRes = $patien_cnt;
					}
					$detailId = $detailArr[$d];
					$detailIdArr = $chargeListArr[$detailId];
					$cpt_desc = $detailIdArr['cpt_desc'];
					$cpt4_code = $detailIdArr['cpt4_code'];
					$diagnosisArr = array();
					if($detailIdArr['diagnosis_id1']){
						$diagnosisArr[] = $detailIdArr['diagnosis_id1'];
					}
					if($detailIdArr['diagnosis_id2']){
						$diagnosisArr[] = $detailIdArr['diagnosis_id2'];
					}
					if($detailIdArr['diagnosis_id3']){
						$diagnosisArr[] = $detailIdArr['diagnosis_id3'];
					}
					if($detailIdArr['diagnosis_id4']){
						$diagnosisArr[] = $detailIdArr['diagnosis_id4'];
					}
					$diagnosis = join(', ',$diagnosisArr);
					$facility_name = $detailIdArr['facilityPracCode'];
					$departmentId = $detailIdArr['departmentId'];
					$DepartmentCode = $departMentArr[$departmentId];
					$posFacilityId = $detailIdArr['posFacilityId'];
					$facility_name = $posFacilityArr[$posFacilityId];
					$subCharge += $detailIdArr['totalAmount'];
					$totalCharges += $detailIdArr['totalAmount'];
					$totalPostedChargesArr[] = $detailIdArr['totalAmount'];
					$totalAmount = numberFormat($detailIdArr['totalAmount'],2);
					$oprName = $userNameTwoCharArr[$operatorId];
					
					$pdfData2 .= <<<DATA
						<tr>
							<td class="text_12" bgcolor="#FFFFFF" width="30" align="center">$countRes</td>
							<td class="text_12" bgcolor="#FFFFFF" width="180">$patientName</td>
							<td class="text_12" bgcolor="#FFFFFF" align="left" width="100">$date_of_service</td>
							<td class="text_12" bgcolor="#FFFFFF" width="70">$encounter_ids</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100">$cpt4_code</td>
							<td class="text_12" bgcolor="#FFFFFF" width="130" align="left">$cpt_desc</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$diagnosis</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$facility_name</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$DepartmentCode</td>
							<td class="text_12" bgcolor="#FFFFFF" width="110" style="text-align:right">$totalAmount</td>
							<td class="text_12" bgcolor="#FFFFFF" width="30" style="text-align:center">$oprName</td>
						</tr>
DATA;
	
					$patientNameChk = true;
				}

				if($c == count($facilityChgIdArr)-1){
					$subCharge = numberFormat($subCharge,2);
					$userName = $userNameArr[$operatorId_old];
					$pdfData2 .= <<<DATA
						<tr>
							<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right" colspan="9">Sub Total</td>
							<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$subCharge</td>
							<td class="text_12b" bgcolor="#FFFFFF"></td>
						</tr>
DATA;
				$subCharge = 0;
				}				
			}
					
			$footerTotalData = NULL;
			if($p == count($mainNotPostedArr)-1){
				$totalPostedCharges = numberFormat(array_sum($totalPostedChargesArr),2);
				$footerTotalData .= <<<DATA
					<tr>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">Total Amount: </td>
						<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$totalPostedCharges</td>
						<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					</tr>
DATA;
			}
		}
		//--- PDF FILE DATA ----
		$totalCharges = numberFormat($totalCharges,2);
		$pdfData .= <<<DATA
			<page backtop="17mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>		
			<page_header>
				$page_header_val
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt rpt_table rpt_table-bordered rpt_padding">					
					<tr>
						<td class="text_b_w" colspan="11">Charges Entered But Not Posted</td>
					</tr>
					<tr>
						<td class="text_b_w" style="text-align:right" colspan="2">Physician Name :</td>
						<td class="text_b_w" align="left" colspan="9">$userName</td>
					</tr>
					<tr>
						<td class="text_b_w" width="20" align="center">Qty</td>
						<td class="text_b_w" width="170" align="center">Patient Name-ID</td>
						<td class="text_b_w" width="90" align="center">DOS</td>
						<td class="text_b_w" width="70" align="center">E.ID</td>
						<td class="text_b_w" width="90" align="center">CPT</td>
						<td class="text_b_w" width="140" align="center">Description</td>
						<td class="text_b_w" width="100" align="center">DX Code</td>
						<td class="text_b_w" width="90" align="center">POS</td>
						<td class="text_b_w" width="90" align="center">DPT.</td>
						<td class="text_b_w" width="110" align="center">Charges</td>
						<td class="text_b_w" width="30" align="center">Opr</td>
					</tr>
				</table>
			</page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
				$pdfData2
				<tr>
					<td class="text_12b" bgcolor="#FFFFFF" colspan="9" style="text-align:right">Total :</td>
					<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$totalCharges</td>
					<td class="text_12b" bgcolor="#FFFFFF"></td>
				</tr>
				$footerTotalData
			</table>
			</page>
DATA;

		//--- GET CSV DATA ----
		$csvFileData .= <<<DATA
			$page_header_val
			<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">					
				<tr>
					<td class="text_b_w" colspan="11">Charges Entered But Not Posted</td>
				</tr>
				<tr>
					<td class="text_b_w" style="text-align:right" colspan="2">Physician Name :</td>
					<td class="text_b_w" align="left" colspan="9">$userName</td>
				</tr>
				<tr>
					<td class="text_b_w" width="20" align="center">Qty</td>
					<td class="text_b_w" width="180" align="center">Patient Name-ID</td>
					<td class="text_b_w" width="90" align="center">DOS</td>
					<td class="text_b_w" width="70" align="center">E.ID</td>
					<td class="text_b_w" width="90" align="center">CPT</td>
					<td class="text_b_w" width="140" align="center">Description</td>
					<td class="text_b_w" width="100" align="center">DX Code</td>
					<td class="text_b_w" width="90" align="center">POS</td>
					<td class="text_b_w" width="90" align="center">DPT.</td>
					<td class="text_b_w" width="110" align="center">Charges</td>
					<td class="text_b_w" width="30" align="center">Opr</td>
				</tr>
				$pdfData2
				<tr>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
					<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">Total :</td>
					<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">$totalCharges</td>
					<td class="text_12b" bgcolor="#FFFFFF" align="center"></td>
				</tr>
				$footerTotalData
			</table>
DATA;
		$page_header_val = NULL;
	}
}



//--- REFUND BLOCK ----------
if(count($refund_arr)){ 
	$countRes=0;
	$totalAmount_arr=array();
	$totalPayAmount_arr=array();
	$totalRefundAmt_arr=array();
	$overPayment_arr=array();
	$totalPayAmount_arr=array();
	foreach($refund_arr as $facility_id => $phyData){
		$refundPDF .='<tr>
						<td class="text_b_w" colspan="9" align="left">Facility : '.$posFacilityArr[$facility_id].'</td>
					</tr>';
		$refundCSV .='<tr>
						<td class="text_b_w" colspan="9" align="left">Facility : '.$posFacilityArr[$facility_id].'</td>
					</tr>';			
		$totalAmount_sub_arr=array();	
		$totalRefundAmt_sub_arr=array();	
		$totalPayAmount_sub_arr=array();		

		foreach($phyData as $encounter_id => $refundData){
			$countRes++;
			$totalAmount= $amtPaid = $refundAmt = 0;
			$cptCodes='';
			
			$date_of_service = $refundData[0]['date_of_service'];
			$provider_id = $refundData[0]['primaryProviderId'];
			$phy_name = $userNameArr[$provider_id];

			$pat_name_arr = array();
			$pat_name_arr['LAST_NAME'] = $refundData[0]['lname'];
			$pat_name_arr['FIRST_NAME'] = $refundData[0]['fname'];
			$pat_name_arr['MIDDLE_NAME'] = $refundData[0]['mname'];			
			$patientName = $objDataManage->__changeNameFormat($pat_name_arr);
			$patientName .= " - ".$refundData[0]['patient_id'];
			$cptCodes = implode(', ',$arrRefundCPT[$encounter_id]);
			$refundDate = $arrRefundDate[$encounter_id];
			
			for($k=0; $k<sizeof($refundData); $k++){
				$totalAmount+= $refundData[$k]['totalAmount'];
				$amtPaid+= $refundData[$k]['paidForProc'];
			}
			$refundAmt = $arrRefundAmt[$encounter_id];
			
			$totalAmount_sub_arr[]=$totalAmount;
			$totalRefundAmt_sub_arr[]=$refundAmt;
			$totalPayAmount_sub_arr[]=$amtPaid;
			
			$totalAmount_arr[]=$totalAmount;
			$totalRefundAmt_arr[]=$refundAmt;
			$totalPayAmount_arr[]=$amtPaid;
			
			//--- PDF FILE DATA ----
			$refundPDF .='<tr>
			<td class="text_12" bgcolor="#FFFFFF" width="232">'.$patientName.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="center" width="90">'.$date_of_service.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$encounter_id.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="110" align="left">'.$phy_name.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="110" align="left">'.$cptCodes.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="80" align="center">'.$refundDate.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="95" style="text-align:right"> $'.number_format($totalAmount,2).'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="95" style="text-align:right"> $'.number_format($amtPaid,2).'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="100" style="text-align:right;padding-right:20px;"> $'.number_format($refundAmt,2).'</td>
			</tr>';
			$refundCSV .='<tr>
			<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="center" width="80">'.$date_of_service.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="70" align="center">'.$encounter_id.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="140" align="left">'.$phy_name.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$cptCodes.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="80" align="center">'.$refundDate.'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format($totalAmount,2).'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format($amtPaid,2).'</td>
			<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> $'.number_format($refundAmt,2).'</td>
			</tr>';		
		}
		$refundPDF .='
		<tr><td bgcolor="#FFFFFF" colspan="9"></td></tr>
		<tr>
			<td class="text_b" bgcolor="#FFFFFF" colspan="5"></td>
			<td class="text_b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> $'.number_format(array_sum($totalAmount_sub_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> $'.number_format(array_sum($totalPayAmount_sub_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right;padding-right:20px;"> $'.number_format(array_sum($totalRefundAmt_sub_arr),2).'</td>
		</tr>
		<tr><td bgcolor="#FFFFFF" colspan="9"></td></tr>';
		$refundCSV .='
		<tr><td class="total-row" colspan="9"></td></tr>	
		<tr>
		<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Sub Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format(array_sum($totalAmount_sub_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format(array_sum($totalPayAmount_sub_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> $'.number_format(array_sum($totalRefundAmt_sub_arr),2).'</td>
		</tr>
		<tr><td class="total-row" colspan="9"></td></tr>';		
	}
	$pdfData.= '
	<page backtop="15mm" backbottom="5mm">
	<page_footer>
	<table style="width: 100%;">
	<tr>
		<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
	</tr>
	</table>
	</page_footer>		
	<page_header>
	'.$page_header_val.'
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">						
	<tr><td colspan="9" class="text_b_w"><strong>Refund Amounts</strong></td></tr>
	<tr>
		<td class="text_b_w" width="232" style="text-align:center">Patient Name-ID</td>
		<td class="text_b_w" width="80" style="text-align:center">DOS</td>
		<td class="text_b_w" width="90" style="text-align:center">E.ID</td>
		<td class="text_b_w" width="110" style="text-align:center">Physician</td>
		<td class="text_b_w" width="110" style="text-align:center">CPT Code</td>
		<td class="text_b_w" width="80" style="text-align:center">Refund Date</td>
		<td class="text_b_w" width="100" style="text-align:center">Charges</td>
		<td class="text_b_w" width="100" style="text-align:center">Paid Amount</td>
		<td class="text_b_w" width="100" style="text-align:center">Refund Amount</td>
	</tr>
	</table>
	</page_header>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
		'.$refundPDF.'
		<tr><td class="total-row" colspan="9"></td></tr>
		<tr>
		<td class="text_12" bgcolor="#FFFFFF" colspan="5"></td>
		<td class="text_b" bgcolor="#FFFFFF" align="right">Grand Total:</td>
		<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> $'.number_format(array_sum($totalAmount_arr),2).'</td>
		<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> $'.number_format(array_sum($totalPayAmount_arr),2).'</td>
		<td class="text_b" bgcolor="#FFFFFF" style="text-align:right;padding-right:20px;"> $'.number_format(array_sum($totalRefundAmt_arr),2).'</td>
		</tr>
		<tr><td class="total-row" colspan="9"></td></tr>
	</table>
	</page>';

	$csvFileData.= $page_header_val.'
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" class="rpt rpt_table rpt_table-bordered rpt_padding">					
	<tr><td colspan="9" class="text_b_w"><strong>Refund Amounts</strong></td></tr>
	<tr>
		<td class="text_b_w" width="180" style="text-align:center">Patient Name-ID</td>
		<td class="text_b_w" width="90" style="text-align:center">DOS</td>
		<td class="text_b_w" width="70" style="text-align:center">E.ID</td>
		<td class="text_b_w" width="90" style="text-align:center">Physician</td>
		<td class="text_b_w" width="90" style="text-align:center">CPT Code</td>
		<td class="text_b_w" width="90" style="text-align:center">Refund Date</td>
		<td class="text_b_w" width="140" style="text-align:center">Charges</td>
		<td class="text_b_w" width="140" style="text-align:center">Paid Amount</td>
		<td class="text_b_w" width="140" style="text-align:center">Refund Amount</td>
	</tr>
	'.$refundCSV.'
	<tr><td class="total-row" colspan="9"></td></tr>
	<tr>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Grand Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format(array_sum($totalAmount_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> $'.number_format(array_sum($totalPayAmount_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> $'.number_format(array_sum($totalRefundAmt_arr),2).'</td>
	</tr>
	<tr><td class="total-row" colspan="9"></td></tr>
	</table>';
}
//END REFUND BLOCK	

//OPERATOR DATA
//$csvFileData.=$oper_data;
//- END OPER DATA

$pdf_part ='';
?>