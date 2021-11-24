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

$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$op_name = ucfirst(trim($op_name_arr[1][0]));
$op_name .= ucfirst(trim($op_name_arr[0][0]));
$curDate = date('m-d-Y h:i A');

$page_header_val = <<<DATA
	<table width="100%"class="rpt rpt_table rpt_table-bordered rpt_padding">
		<tr>	
			<td align="left" class="rptbx1" style="width:229px;">Front Desk Collection Report : Summary</td>
			<td align="left" class="rptbx2" style="width:290px;">Selected Group : $group_name</td>	
			<td align="left" class="rptbx2" style="width:270px;">Selected Facility : $practice_name</td>
			<td align="left" class="rptbx3" style="width:222px;">Created by $op_name on $curDate </td>
		</tr>
		<tr>	
			<td align="left" class="rptbx1">Sel Physician : $physican_name</td>
			<td align="left" class="rptbx2" colspan="2">Sel Operator : $operator_name</td>
			<td align="left" class="rptbx3">Sel Date : $Start_date To $End_date</td>
		</tr>
	</table>
DATA;
$page_header_val2 = <<<DATA
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="700" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" class="rptbx1" width="200" style="font-size: 11px;">FD Collection Report: Summary</td>
				<td align="left" class="rptbx2" width="300" colspan="2" style="font-size: 11px;">Selected Group : $group_name</td>	
				<td align="left" class="rptbx3" width="200" style="font-size: 11px;">Created by $op_name on $curDate </td>
			</tr>
			<tr>
				<td align="left" class="rptbx1" style="font-size: 11px;">Sel Facility : $practice_name</td>
				<td align="left" class="rptbx2" style="font-size: 11px;">Sel Physician : $physician_name</td>
				<td align="left" class="rptbx2" style="font-size: 11px;">Sel Operator : $operator_name</td>
				<td align="left" class="rptbx3" style="font-size: 11px;">Sel Date : $Start_date To $End_date</td>
			</tr>
	</table>
DATA;
//--- GET PAYMENTS RECORDS --------
$paid_encounter_str = join(',',$paid_encounter_arr);
if(empty($paid_encounter_str) === false){
	$primaryProviderIdArr = array_values($primaryProviderIdArr);
	$grandTotalPayment = array();
	for($i=0;$i<count($primaryProviderIdArr);$i++){
		$primaryProviderId = (int)$primaryProviderIdArr[$i];
		$proEncounter = $paidProEncounter[$primaryProviderId];
		$pdfData2 = NULL;
		$csvData = '';
		$footerTotalDataPDF='';
		$subCashTotal = 0;
		$subCheckTotal = 0;
		$subCCTotal = 0;
		$sub_adj_amt_arr = array();
		$pat_id_arr = array();
			$gr_cashPaymentArr_copay_arr=array();
			$gr_checkPaymentArr_copay_arr=array();
			$gr_ccPaymentArr_copay_arr=array();
			
			$gr_cashPaymentArr_opt_arr=array();
			$gr_checkPaymentArr_opt_arr=array();
			$gr_ccPaymentArr_opt_arr=array();
			
			$gr_cashPaymentArr_proc_arr=array();
			$gr_checkPaymentArr_proc_arr=array();
			$gr_ccPaymentArr_proc_arr=array();
			
			$gr_payment_mode_copay_arr=array();
			$gr_payment_mode_opt_arr=array();
			$gr_payment_mode_proc_arr=array();
			
			$gr_payment_cnt_copay_arr=array();
			$gr_payment_cnt_opt_arr=array();
			$gr_payment_cnt_proc_arr=array();
			
			for($p=0;$p<count($proEncounter);$p++){
				$encounterId = $proEncounter[$p];
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
				for($m=0;$m<count($paymentIdArr);$m++){
					$paymentId = $paymentIdArr[$m];
					$paymentmode = strtolower(trim($paymentArr[$paymentId]['payment_mode']));
					$checkNo = $paymentArr[$paymentId]['checkNo'];
					$creditCardNo = $paymentArr[$paymentId]['creditCardNo'];
	
					$operatorNameArr[] = $paymentArr[$paymentId]['operatorInitial'];
					
					//--- TOTAL PAID AMOUNT --
					$paidForProc = str_replace(',','',$paymentArr[$paymentId]['paidForProc']);
					$paidForProc += str_replace(',','',$paymentArr[$paymentId]['overPayment']);
					
					//--- GET GRAND TOTAL AMOUNT ---
					$grandTotalPayment[] = $paidForProc;
					
					if(strtolower($paymentmode) == 'cash'){
						$cashPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode;
					}
					else if(strtolower($paymentmode) == 'check' || strtolower($paymentmode) == 'eft' || strtolower($paymentmode) == 'money order' || strtolower($paymentmode) == 'veep'){
						$checkPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode.' - '.substr($checkNo,-5);
					}
					else{
						$CCPaymentArr[] = $paidForProc;
						$payment_mode[] = $creditCardCo.' - '.substr($creditCardNo,0,4);
					}
					
					$cashPaymentArr_copay_arr=array();
					$checkPaymentArr_copay_arr=array();
					$ccPaymentArr_copay_arr=array();
					
					$cashPaymentArr_opt_arr=array();
					$checkPaymentArr_opt_arr=array();
					$ccPaymentArr_opt_arr=array();
					
					$cashPaymentArr_proc_arr=array();
					$checkPaymentArr_proc_arr=array();
					$ccPaymentArr_proc_arr=array();
					
					$payment_mode_copay_arr=array();
					$payment_mode_opt_arr=array();
					$payment_mode_proc_arr=array();
					
					$payment_cnt_copay_arr=array();
					$payment_cnt_opt_arr=array();
					$payment_cnt_proc_arr=array();
					
					foreach($mainPayment_dets_Arr[$encounterId] as $pay_det_id_key => $pay_det_id_val){
						$paymentId_detail=$pay_det_id_val;
						foreach($mainPayment_det_Arr[$encounterId][$paymentId_detail] as $pay_key => $pay_val){
							$main_pay_mode=$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['payment_mode'];
							
							if(strtolower($main_pay_mode) == 'cash'){
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									//if($paidForProc>0){
										$cashPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $paymentmode;
										$payment_cnt_copay_arr[] = 1;
									//}
								}
								
								if(in_array($pcld_proccode[$pay_key],$main_proc_arr) && $pcld_proccode[$pay_key]>0){
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
									//if($paidForProc>0){
										$cashPaymentArr_opt_arr[] = $paidForProc;
										$payment_mode_opt_arr[] = $paymentmode;
										$payment_cnt_opt_arr[] = 1;
									//}
								}else{
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
									//if($paidForProc>0){
										$cashPaymentArr_proc_arr[] = $paidForProc;
										$payment_mode_proc_arr[] = $paymentmode;
										$payment_cnt_proc_arr[] = 1;
									//}
								}
								
							}
							else if(strtolower($main_pay_mode) == 'check' || strtolower($main_pay_mode) == 'eft' || strtolower($main_pay_mode) == 'money order' || strtolower($main_pay_mode) == 'veep'){
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									//if($paidForProc>0){
										$checkPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $paymentmode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
										$payment_cnt_copay_arr[] = 1;
									//}
								}
								
								if(in_array($pcld_proccode[$pay_key],$main_proc_arr) && $pcld_proccode[$pay_key]>0){
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
									//if($paidForProc>0){
										$checkPaymentArr_opt_arr[] = $paidForProc;
										$payment_mode_opt_arr[] = $paymentmode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
										$payment_cnt_opt_arr[] = 1;
									//}
								}else{
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
									//if($paidForProc>0){
										$checkPaymentArr_proc_arr[] = $paidForProc;
										$payment_mode_proc_arr[] = $paymentmode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
										$payment_cnt_proc_arr[] = 1;
									//}
								}
								
							}
							else{
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									//if($paidForProc>0){
										$ccPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $paymentmode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['creditCardNo'],0,4);
										$payment_cnt_copay_arr[] = 1;
									//}
								}
								
								if(in_array($pcld_proccode[$pay_key],$main_proc_arr) && $pcld_proccode[$pay_key]>0){
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
									//if($paidForProc>0){
										$ccPaymentArr_opt_arr[] = $paidForProc;
										$payment_mode_opt_arr[] = $paymentmode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['creditCardNo'],0,4);
										$payment_cnt_opt_arr[] = 1;
									//}
								}else{
									$paidForProc=0;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
									//if($paidForProc>0){
										$ccPaymentArr_proc_arr[] = $paidForProc;
										$payment_mode_proc_arr[] = $paymentmode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['creditCardNo'],0,4);
										$payment_cnt_proc_arr[] = 1;
									//}
								}
								
							}
						}
					}
				}
				
				$gr_cashPaymentArr_copay_arr[]=array_sum($cashPaymentArr_copay_arr);
				$gr_checkPaymentArr_copay_arr[]=array_sum($checkPaymentArr_copay_arr);
				$gr_ccPaymentArr_copay_arr[]=array_sum($ccPaymentArr_copay_arr);
				
				$gr_cashPaymentArr_opt_arr[]=array_sum($cashPaymentArr_opt_arr);
				$gr_checkPaymentArr_opt_arr[]=array_sum($checkPaymentArr_opt_arr);
				$gr_ccPaymentArr_opt_arr[]=array_sum($ccPaymentArr_opt_arr);
				
				$gr_cashPaymentArr_proc_arr[]=array_sum($cashPaymentArr_proc_arr);
				$gr_checkPaymentArr_proc_arr[]=array_sum($checkPaymentArr_proc_arr);
				$gr_ccPaymentArr_proc_arr[]=array_sum($ccPaymentArr_proc_arr);
				
				$gr_payment_mode_copay_arr[]=join(', ',array_unique($payment_mode_copay_arr));
				$gr_payment_mode_opt_arr[]=join(', ',array_unique($payment_mode_opt_arr));
				$gr_payment_mode_proc_arr[]=join(', ',array_unique($payment_mode_proc_arr));
				
				$gr_payment_cnt_copay_arr[]=array_sum($payment_cnt_copay_arr);
				$gr_payment_cnt_opt_arr[]=array_sum($payment_cnt_opt_arr);
				$gr_payment_cnt_proc_arr[]=array_sum($payment_cnt_proc_arr);
				
				$cashPayment = array_sum($cashPaymentArr);
				$checkPayment = array_sum($checkPaymentArr);
				$CCPayment = array_sum($CCPaymentArr);
				
				//--- ADJUSTMENT AMOUNT -----
				$adjAmt = NULL;
				if(count($adjAmtArr[$encounterId])){
					$adjAmt = array_sum($adjAmtArr[$encounterId]);
				}
				$sub_adj_amt_arr[] = $adjAmt;
			
				$subCashTotal += $cashPayment;
				$subCheckTotal += $checkPayment;
				$subCCTotal += $CCPayment;
				
				//--- NUMBER FORMAT FOR CASH PAYMENT ----
				$cashPayment = $CLSReports->numberFormat($cashPayment, 2);
				
				//--- NUMBER FORMAT FOR CHECK PAYMENT ----
				$checkPayment = $CLSReports->numberFormat($checkPayment, 2);
				
				//--- NUMBER FORMAT FOR CC PAYMENT ----
				$CCPayment = $CLSReports->numberFormat($CCPayment, 2);
				
				//--- NUMBER FORMAT FOR ADJUSMENT PAYMENT ----
				$adjAmt = $CLSReports->numberFormat($adjAmt, 2);
				
				$paymentMode = join(', ',array_unique($payment_mode));
				$operatorNameStr = join(', ',array_unique($operatorNameArr));
				$date_of_service = $charge_listArr['date_of_service'];
				$encounter_id = $charge_listArr['encounter_id'];
				$patientName = trim($patientName).' - '.$charge_listArr['id'];
				$pat_id_arr[] = $charge_listArr['id'];

	}



				$paymentModeCopay = "";
				$cashPaymentCopay = "";
				$checkPaymentCopay = "";
				$ccPaymentCopay = "";
				$payment_cnt_copay= "";
				
				$paymentModeOpt = "";
				$cashPaymentOpt = "";
				$checkPaymentOpt = "";
				$ccPaymentOpt = "";
				$payment_cnt_opt = "";
				
				$paymentModeProc = "";
				$cashPaymentProc = "";
				$checkPaymentproc = "";
				$ccPaymentProc = "";
				$payment_cnt_proc = "";
				
				$paymentModeCopay = join(', ',array_filter(array_unique($gr_payment_mode_copay_arr)));	
				$cashPaymentCopay = $CLSReports->numberFormat(array_sum($gr_cashPaymentArr_copay_arr),2);
				$checkPaymentCopay = $CLSReports->numberFormat(array_sum($gr_checkPaymentArr_copay_arr),2);
				$ccPaymentCopay = $CLSReports->numberFormat(array_sum($gr_ccPaymentArr_copay_arr),2);
				$payment_cnt_copay= array_sum($gr_payment_cnt_copay_arr);


				$paymentModeOpt = join(', ',array_filter(array_unique($gr_payment_mode_opt_arr)));
				$cashPaymentOpt = $CLSReports->numberFormat(array_sum($gr_cashPaymentArr_opt_arr),2);
				$checkPaymentOpt = $CLSReports->numberFormat(array_sum($gr_checkPaymentArr_opt_arr),2);
				$ccPaymentOpt = $CLSReports->numberFormat(array_sum($gr_ccPaymentArr_opt_arr),2);
				$payment_cnt_opt = array_sum($gr_payment_cnt_opt_arr);
				
				$paymentModeProc = join(', ',array_filter(array_unique($gr_payment_mode_proc_arr)));
				$cashPaymentProc = $CLSReports->numberFormat(array_sum($gr_cashPaymentArr_proc_arr),2);
				$checkPaymentProc = $CLSReports->numberFormat(array_sum($gr_checkPaymentArr_proc_arr),2);
				$ccPaymentProc = $CLSReports->numberFormat(array_sum($gr_ccPaymentArr_proc_arr),2);
				$payment_cnt_proc = array_sum($gr_payment_cnt_proc_arr);

			$csvData.= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="180" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center"></td>
						<td class="text_10b" bgcolor="#FFFFFF" width="110" align="left">Copay</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="40" align="center">$payment_cnt_copay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$cashPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$checkPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$ccPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="auto"></td>
					</tr>
DATA;
			$csvData .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="180" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center"></td>
						<td class="text_10b" bgcolor="#FFFFFF" width="110" align="left">Optical</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="40" align="center">$payment_cnt_opt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$cashPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$checkPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$ccPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" width="auto" style="text-align:right"></td>
					</tr>
DATA;
		$csvData .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="180" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center"></td>
						<td class="text_10b" bgcolor="#FFFFFF" width="110" align="left">Procedures</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="40" align="center">$payment_cnt_proc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$cashPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$checkPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$ccPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" width="auto" style="text-align:right"></td>
					</tr>
DATA;


			$pdfData2 .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="90" align="left"></td>
						<td class="text_b" bgcolor="#FFFFFF" width="125" align="left">Copay</td>
						<td class="text_12" bgcolor="#FFFFFF" width="85" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="85" align="center">$payment_cnt_copay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="83">$cashPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="85">$checkPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="85">$ccPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" width="70" align="left"></td>
					</tr>
DATA;
			$pdfData2 .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_b" bgcolor="#FFFFFF" align="left">Optical</td>
						<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" align="center">$payment_cnt_opt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" >$cashPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" >$checkPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" >$ccPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" width="80" align="left"></td>
					</tr>
DATA;
		$pdfData2 .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_b" bgcolor="#FFFFFF" align="left">Procedures</td>
						<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" align="center">$payment_cnt_proc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$cashPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$checkPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$ccPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" width="80" align="left"></td>
					</tr>
DATA;
		$userName = $userNameArr[$primaryProviderId];
		$subCashTotal2 = $subCashTotal + $subCheckTotal + $subCCTotal;
		$sub_adj_amt = array_sum($sub_adj_amt_arr);
		
		$grandTotalCash[] = $subCashTotal;
		$grandTotalCheck[] = $subCheckTotal;
		$grandTotalCC[] = $subCCTotal;
		
		//--- NUMBER FORMAT FOR CASH SUB TOTAL ----
		$subCashTotal = $CLSReports->numberFormat($subCashTotal, 2);
		
		//--- NUMBER FORMAT FOR CHECK SUB TOTAL ----
		$subCheckTotal = $CLSReports->numberFormat($subCheckTotal, 2);
		
		//--- NUMBER FORMAT FOR CREDIT CARD SUB TOTAL ----
		$subCCTotal = $CLSReports->numberFormat($subCCTotal, 2);
		
		//--- NUMBER FORMAT FOR SUB TOTAL PAYMENTS ----
		$subCashTotal2 = $CLSReports->numberFormat($subCashTotal2, 2);
		
		//--- NUMBER FORMAT FOR TOTAL ADJUSTMENT ----
		$sub_adj_amt = $CLSReports->numberFormat($sub_adj_amt, 2);
		
		$footerTotalData = NULL;
		if($i == count($primaryProviderIdArr)-1 and count($grandTotalPayment) > 0){
			//--- GRNAD TOTAL PAYMENTS OF ALL PROVIDERS ----
			$grandTotalCashVal = array_sum($grandTotalCash);
			$grandTotalCheckVal = array_sum($grandTotalCheck);
			$grandTotalCCVal = array_sum($grandTotalCC);
			$TotalPayments = array_sum($grandTotalPayment);
			$grdTotalPayments=$TotalPayments;
			
			$grandTotalCashVal = $CLSReports->numberFormat($grandTotalCashVal,2);
			$grandTotalCheckVal = $CLSReports->numberFormat($grandTotalCheckVal,2);
			$grandTotalCCVal = $CLSReports->numberFormat($grandTotalCCVal,2);
			$TotalPayments = $CLSReports->numberFormat($TotalPayments,2);
			
			
			$footerTotalData .= <<<DATA
				<tr>
					<td align="center" colspan="9" bgcolor="#FFFFFF" class="text_12b"></td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_12b">&nbsp;</td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">Cash</td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">Check</td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">Credit Card</td>
					<td align="left" bgcolor="#FFFFFF" class="text_12b">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_12b">&nbsp;</td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalCashVal</td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalCheckVal</td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalCCVal</td>
					<td align="left" bgcolor="#FFFFFF" class="text_12b">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_12b">Grand Total :</td>
					<td align="left" colspan="4" bgcolor="#FFFFFF" class="text_12b">$TotalPayments</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
DATA;
			$footerTotalDataPDF .= <<<DATA
				<tr>
					<td align="center" colspan="8" bgcolor="#FFFFFF" class="text_b"></td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" colspan="2" class="text_b">&nbsp;</td>
					<td align="center" bgcolor="#FFFFFF" class="text_b">Cash</td>
					<td align="center" bgcolor="#FFFFFF" class="text_b">Check</td>
					<td align="center" bgcolor="#FFFFFF" class="text_b">Credit Card</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" colspan="2" class="text_b">&nbsp;</td>
					<td align="center" bgcolor="#FFFFFF" class="text_b">$grandTotalCashVal</td>
					<td align="center" bgcolor="#FFFFFF" class="text_b">$grandTotalCheckVal</td>
					<td align="center" bgcolor="#FFFFFF" class="text_b">$grandTotalCCVal</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="right" bgcolor="#FFFFFF" colspan="2" class="text_b">Grand Total :</td>
					<td align="left" colspan="4" bgcolor="#FFFFFF" class="text_b">$TotalPayments</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
DATA;
		}
		
		//--- PDF FILE DATA ----
		$pdfData .= <<<DATA
			<page backtop="30mm" backbottom="20mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$page_header_val2
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">
				<tr>
					<td style="text-align:right" class="text_b_w">Physician Name :</td>
					<td align="left" colspan="8" class="text_b_w">$userName</td>
				</tr>
				<tr>
					<td align="center" class="text_b_w" style="width:80px"></td>
					<td align="center" class="text_b_w" style="width:120px">Type</td>
					<td align="center" class="text_b_w" style="width:80px"></td>
					<td align="center" class="text_b_w" style="width:80px">Unit</td>
					<td align="center" class="text_b_w" style="width:80px">Cash</td>
					<td align="center" class="text_b_w" style="width:80px">Check</td>
					<td align="center" class="text_b_w" style="width:80px">Credit Card</td>
					<td align="center" class="text_b_w" style="width:80px"></td>
				</tr>				
			</table>
			</page_header>
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">
				$pdfData2
				<tr><td class="total-row" colspan="9"></td></tr>
				<tr>
					<td colspan="4" class="text_b" style="text-align:right">SubTotal :</td>
					<td style="text-align:right" class="text_b">$subCashTotal</td>
					<td style="text-align:right" class="text_b">$subCheckTotal</td>
					<td style="text-align:right" class="text_b">$subCCTotal</td>
					<td class="text_b">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
				<tr>
					<td style="text-align:right" class="text_b" colspan="4">Total :</td>
					<td align="left" colspan="4" class="text_b">$subCashTotal2</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
				$footerTotalDataPDF
			</table>
			</page>
DATA;
		
		$pat_id_count = count($pat_id_arr);		
		//--- GET CSV DATA ---
		$csvFileData .= <<<DATA
			$page_header_val
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">			
				<tr>
					<td style="text-align:right" class="text_b_w">Physician Name :</td>
					<td align="left" colspan="8" class="text_b_w">$userName</td>
				</tr>
				<tr>
					<td align="center" class="text_b_w"></td>
					<td align="center" class="text_b_w"></td>
					<td align="center" class="text_b_w">Type</td>
					<td align="center" class="text_b_w"></td>
					<td align="center" class="text_b_w">Unit</td>
					<td align="center" width="120" class="text_b_w">Cash</td>
					<td align="center" width="120" class="text_b_w">Check</td>
					<td align="center" width="120" class="text_b_w">Credit Card</td>
					<td align="center" class="text_b_w"></td>
				</tr>				
				$csvData
				<tr><td class="total-row" colspan="9"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td bgcolor="#FFFFFF" class="text_12b" colspan="2" style="text-align:right">SubTotal :</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCashTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCheckTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCCTotal</td>
					<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" class="text_12b" bgcolor="#FFFFFF">Total Claims :</td>
					<td align="left" colspan="4" bgcolor="#FFFFFF" class="text_12b">$pat_id_count</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" class="text_12b" bgcolor="#FFFFFF">Total :</td>
					<td align="left" colspan="4" bgcolor="#FFFFFF" class="text_12b">$subCashTotal2</td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
				$footerTotalData
			</table>
DATA;
		$page_header_val = NULL;
		

	}	
}

?>