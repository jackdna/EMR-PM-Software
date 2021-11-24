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

$showCurrencySymbol= showCurrency();
$SqlDateFormat  = get_sql_date_format(); 
if($reportType == 'encounter')
{
$page_header_val = <<<DATA
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" class="rptbx1" style="width:229px;">Front Desk Collection Report : Detail</td>
				<td align="left" class="rptbx2" style="width:290px;">Selected Group : $group_name</td>	
				<td align="left" class="rptbx2" style="width:270px;">Selected Facility : $practice_name</td>
				<td align="left" class="rptbx3" style="width:222px;">Created by $op_name on $curDate </td>
			</tr>
			<tr>	
				<td align="left" class="rptbx1">Selected Physician : $physican_name</td>
				<td align="left" class="rptbx2"  colspan="2">Selected Operator : $operator_name</td>
				<td align="left" class="rptbx3">Selected Date : $Start_date To $End_date</td>
			</tr>
	</table>
DATA;
$page_header_val2 = <<<DATA
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="720" bgcolor="#FFF3E8">
		<tr>	
			<td align="left" class="rptbx1" width="200" style="font-size: 11px;">FD Collection Report: Detail</td>
			<td align="left" class="rptbx2" width="320" colspan="2" style="font-size: 11px;">Selected Group : $group_name</td>	
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
		$csvData ='';
		$footerTotalDataPDF='';
		$subCashTotal = 0;
		$subCheckTotal = 0;
		$subCCTotal = 0;
		$subMoTotal = 0;
		$subEftTotal = 0;
		$subVeepTotal = 0;
		$sub_adj_amt_arr = array();
		$pat_id_arr = array();
		
			
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
				$moPaymentArr = array();
				$eftPaymentArr = array();
				$veepPaymentArr = array();
				$operatorNameArr = array();
				for($m=0;$m<count($paymentIdArr);$m++){
					$paymentId = $paymentIdArr[$m];
					$checkNo = $paymentArr[$paymentId]['checkNo'];
					$creditCardNo = $paymentArr[$paymentId]['creditCardNo'];
	
					$operatorNameArr[] = $paymentArr[$paymentId]['operatorInitial'];
					
					//--- TOTAL PAID AMOUNT --
					$paidForProc = str_replace(',','',$paymentArr[$paymentId]['paidForProc']);
					$paidForProc += str_replace(',','',$paymentArr[$paymentId]['overPayment']);
					//if($paymentArr[$paymentId]['paymentClaims'] == 'Negative Payment'){
						//$paidForProc = '-'.$paidForProc;
					//}
					
					//--- GET GRAND TOTAL AMOUNT ---
					$grandTotalPayment[] = $paidForProc;
					
					if(strtolower(trim($paymentArr[$paymentId]['payment_mode']))=="cash"){
						$paymentmode = "Cash";
						$cashPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode;
					}
					else if(strtolower(trim($paymentArr[$paymentId]['payment_mode']))=="check"){
						$paymentmode = "Check";
						$checkPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode.' - '.substr($checkNo,-5);
					}
					else if(strtolower(trim($paymentArr[$paymentId]['payment_mode']))=="credit card"){
						$paymentmode = "CC";
						$CCPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode.' - '.substr($creditCardNo,0,4);
					}
					else if(strtolower(trim($paymentArr[$paymentId]['payment_mode']))=="eft"){
						$paymentmode = "EFT";
						$eftPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode.' - '.substr($checkNo,-5);
					}
					else if(strtolower(trim($paymentArr[$paymentId]['payment_mode']))=="money order"){
						$paymentmode = "MO";
						$moPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode.' - '.substr($checkNo,-5);
					}
					else if(strtolower(trim($paymentArr[$paymentId]['payment_mode']))=="veep"){
						$paymentmode = "VEEP";
						$veepPaymentArr[] = $paidForProc;
						$payment_mode[] = $paymentmode.' - '.substr($checkNo,-5);
					}
					
					$cashPaymentArr_copay_arr=array();
					$checkPaymentArr_copay_arr=array();
					$ccPaymentArr_copay_arr=array();
					$moPaymentArr_copay_arr=array();
					$eftPaymentArr_copay_arr=array();
					$veepPaymentArr_copay_arr=array();
					
					$cashPaymentArr_opt_arr=array();
					$checkPaymentArr_opt_arr=array();
					$ccPaymentArr_opt_arr=array();
					$moPaymentArr_opt_arr=array();
					$eftPaymentArr_opt_arr=array();
					$veepPaymentArr_opt_arr=array();
					
					$cashPaymentArr_proc_arr=array();
					$checkPaymentArr_proc_arr=array();
					$ccPaymentArr_proc_arr=array();
					$moPaymentArr_proc_arr=array();
					$eftPaymentArr_proc_arr=array();
					$veepPaymentArr_proc_arr=array();
					
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
							$copay=0;
							if(strtolower($main_pay_mode) == 'cash'){
								$main_pay_mode = "Cash";
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$copay=1;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									if($paidForProc!=0){
										$cashPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $main_pay_mode;
										$payment_cnt_copay_arr[] = 1;
									}
								}

								if($copay==0){
									if(in_array($pcld_proccode[$pay_key],$main_proc_arr)){
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$cashPaymentArr_opt_arr[] = $paidForProc;
											$payment_mode_opt_arr[] = $main_pay_mode;
											$payment_cnt_opt_arr[] = 1;
										}
									}else{
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$cashPaymentArr_proc_arr[] = $paidForProc;
											$payment_mode_proc_arr[] = $main_pay_mode;
											$payment_cnt_proc_arr[] = 1;
										}
									}
								}
							}
							else if(strtolower($main_pay_mode) == 'check'){
								$main_pay_mode = "Check";
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$copay=1;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									if($paidForProc!=0){
										$checkPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
										$payment_cnt_copay_arr[] = 1;
									}
								}
								
								if($copay==0){
									if(in_array($pcld_proccode[$pay_key],$main_proc_arr)){
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$checkPaymentArr_opt_arr[] = $paidForProc;
											$payment_mode_opt_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
											$payment_cnt_opt_arr[] = 1;
										}
									}else{
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$checkPaymentArr_proc_arr[] = $paidForProc;
											$payment_mode_proc_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
											$payment_cnt_proc_arr[] = 1;
										}
									}
								}
							}
							else if(strtolower($main_pay_mode) == 'money order'){
								$main_pay_mode = "MO";
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$copay=1;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									if($paidForProc!=0){
										$moPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
										$payment_cnt_copay_arr[] = 1;
									}
								}
								
								if($copay==0){
									if(in_array($pcld_proccode[$pay_key],$main_proc_arr)){
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$moPaymentArr_opt_arr[] = $paidForProc;
											$payment_mode_opt_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
											$payment_cnt_opt_arr[] = 1;
										}
									}else{
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$moPaymentArr_proc_arr[] = $paidForProc;
											$payment_mode_proc_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
											$payment_cnt_proc_arr[] = 1;
										}
									}
								}
							}
							else if(strtolower($main_pay_mode) == 'eft'){
								$main_pay_mode = "EFT";
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$copay=1;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									if($paidForProc!=0){
										$eftPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
										$payment_cnt_copay_arr[] = 1;
									}
								}
								
								if($copay==0){
									if(in_array($pcld_proccode[$pay_key],$main_proc_arr)){
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$eftPaymentArr_opt_arr[] = $paidForProc;
											$payment_mode_opt_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
											$payment_cnt_opt_arr[] = 1;
										}
									}else{
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$eftPaymentArr_proc_arr[] = $paidForProc;
											$payment_mode_proc_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
											$payment_cnt_proc_arr[] = 1;
										}
									}
								}
							}
							else if(strtolower($main_pay_mode) == 'veep'){
								$main_pay_mode = "VEEP";
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$copay=1;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									if($paidForProc!=0){
										$veepPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
										$payment_cnt_copay_arr[] = 1;
									}
								}
								
								if($copay==0){
									if(in_array($pcld_proccode[$pay_key],$main_proc_arr)){
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$veepPaymentArr_opt_arr[] = $paidForProc;
											$payment_mode_opt_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
											$payment_cnt_opt_arr[] = 1;
										}
									}else{
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$veepPaymentArr_proc_arr[] = $paidForProc;
											$payment_mode_proc_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['checkNo'],-5);
											$payment_cnt_proc_arr[] = 1;
										}
									}
								}
							}							
							else{
								$main_pay_mode = "CC";
								if($mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']){
									$paidForProc=0;
									$copay=1;
									$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['paidForProc']);
									$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][0]['overPayment']);
									if($paidForProc!=0){
										$ccPaymentArr_copay_arr[] = $paidForProc;
										$payment_mode_copay_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['creditCardNo'],0,4);
										$payment_cnt_copay_arr[] = 1;
									}
								}
								
								if($copay==0){
									if(in_array($pcld_proccode[$pay_key],$main_proc_arr)){
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$ccPaymentArr_opt_arr[] = $paidForProc;
											$payment_mode_opt_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['creditCardNo'],0,4);
											$payment_cnt_opt_arr[] = 1;
										}
									}else{
										$paidForProc=0;
										$paidForProc = str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['paidForProc']);
										$paidForProc += str_replace(',','',$mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['overPayment']);
										if($paidForProc!=0){
											$ccPaymentArr_proc_arr[] = $paidForProc;
											$payment_mode_proc_arr[] = $main_pay_mode.' - '.substr($mainPayment_det_Arr[$encounterId][$paymentId_detail][$pay_key]['creditCardNo'],0,4);
											$payment_cnt_proc_arr[] = 1;
										}
									}
								}
							}
						}
					}
				}
				
				$cashPayment = array_sum($cashPaymentArr);
				$checkPayment = array_sum($checkPaymentArr);
				$CCPayment = array_sum($CCPaymentArr);
				$moPayment = array_sum($moPaymentArr);
				$eftPayment = array_sum($eftPaymentArr);
				$veepPayment = array_sum($veepPaymentArr);
				//--- ADJUSTMENT AMOUNT -----
				$adjAmt = NULL;
				if(count($adjAmtArr[$encounterId])){
					$adjAmt = array_sum($adjAmtArr[$encounterId]);
				}
				$sub_adj_amt_arr[] = $adjAmt;
			
				$subCashTotal += $cashPayment;
				$subCheckTotal += $checkPayment;
				$subCCTotal += $CCPayment;
				$subMoTotal += $moPayment;
				$subEftTotal += $eftPayment;
				$subVeepTotal += $veepPayment;
				
				$grandTotalCash[] 	= $cashPayment;
				$grandTotalCheck[] 	= $checkPayment;
				$grandTotalCC[] 	= $CCPayment;
				$grandTotalMo[] 	= $moPayment;
				$grandTotalEft[] 	= $eftPayment;
				$grandTotalVeep[] 	= $veepPayment;
								
				//--- NUMBER FORMAT FOR CASH PAYMENT ----
				$cashPayment = $CLSReports->numberFormat($cashPayment, 2);
				
				//--- NUMBER FORMAT FOR CHECK PAYMENT ----
				$checkPayment = $CLSReports->numberFormat($checkPayment, 2);
				
				//--- NUMBER FORMAT FOR CC PAYMENT ----
				$CCPayment = $CLSReports->numberFormat($CCPayment, 2);
				
				//--- NUMBER FORMAT FOR MONERY ORDER PAYMENT ----
				$moPayment = $CLSReports->numberFormat($moPayment, 2);

				//--- NUMBER FORMAT FOR EFT PAYMENT ----
				$eftPayment = $CLSReports->numberFormat($eftPayment, 2);

				//--- NUMBER FORMAT FOR VEEP PAYMENT ----
				$veepPayment = $CLSReports->numberFormat($veepPayment, 2);
				
				//--- NUMBER FORMAT FOR ADJUSMENT PAYMENT ----
				$adjAmt = $CLSReports->numberFormat($adjAmt, 2);
				

				$paymentModeCopay = "";	
				$cashPaymentCopay = "";	
				$checkPaymentCopay = "";	
				$ccPaymentCopay = "";
				$moPaymentCopay = "";
				$eftPaymentCopay = "";	
				$veepPaymentCopay = "";	
				$payment_cnt_copay= "";	
				
				$paymentModeOpt = "";	
				$cashPaymentOpt = "";
				$checkPaymentOpt = "";
				$ccPaymentOpt = "";
				$moPaymentOpt = "";
				$eftPaymentOpt = "";
				$veepPaymentOpt = "";
				$payment_cnt_opt = "";
				
				$paymentModeProc = "";
				$cashPaymentProc = "";
				$checkPaymentProc = "";
				$ccPaymentProc = "";
				$moPaymentProc = "";
				$eftPaymentProc = "";
				$veepPaymentProc = "";
				$payment_cnt_proc = "";
				
				
				$paymentMode = join(', ',array_filter(array_unique($payment_mode)));
				$operatorNameStr = join(', ',array_unique($operatorNameArr));
				$date_of_service = $charge_listArr['date_of_service'];
				$encounter_id = $charge_listArr['encounter_id'];
				$patientName = trim($patientName).' - '.$charge_listArr['id'];
				$pat_id_arr[] = $charge_listArr['id'];

				$paymentModeCopay = join(',<br>',array_filter(array_unique($payment_mode_copay_arr)));	
				$cashPaymentCopay = $CLSReports->numberFormat(array_sum($cashPaymentArr_copay_arr),2);
				$checkPaymentCopay = $CLSReports->numberFormat(array_sum($checkPaymentArr_copay_arr),2);
				$ccPaymentCopay = $CLSReports->numberFormat(array_sum($ccPaymentArr_copay_arr),2);
				$moPaymentCopay = $CLSReports->numberFormat(array_sum($moPaymentArr_copay_arr),2);
				$eftPaymentCopay = $CLSReports->numberFormat(array_sum($eftPaymentArr_copay_arr),2);
				$veepPaymentCopay = $CLSReports->numberFormat(array_sum($veepPaymentArr_copay_arr),2);
				$payment_cnt_copay= array_sum($payment_cnt_copay_arr);
				
				$paymentModeOpt = join(',<br>',array_filter(array_unique($payment_mode_opt_arr)));	
				$cashPaymentOpt = $CLSReports->numberFormat(array_sum($cashPaymentArr_opt_arr),2);
				$checkPaymentOpt = $CLSReports->numberFormat(array_sum($checkPaymentArr_opt_arr),2);
				$ccPaymentOpt = $CLSReports->numberFormat(array_sum($ccPaymentArr_opt_arr),2);
				$moPaymentOpt = $CLSReports->numberFormat(array_sum($moPaymentArr_opt_arr),2);
				$eftPaymentOpt = $CLSReports->numberFormat(array_sum($eftPaymentArr_opt_arr),2);
				$veepPaymentOpt = $CLSReports->numberFormat(array_sum($veepPaymentArr_opt_arr),2);
				$payment_cnt_opt = array_sum($payment_cnt_opt_arr);
				
				$paymentModeProc = join(',<br>',array_filter(array_unique($payment_mode_proc_arr)));	
				$cashPaymentProc = $CLSReports->numberFormat(array_sum($cashPaymentArr_proc_arr),2);
				$checkPaymentProc = $CLSReports->numberFormat(array_sum($checkPaymentArr_proc_arr),2);
				$ccPaymentProc = $CLSReports->numberFormat(array_sum($ccPaymentArr_proc_arr),2);
				$moPaymentProc = $CLSReports->numberFormat(array_sum($moPaymentArr_proc_arr),2);
				$eftPaymentProc = $CLSReports->numberFormat(array_sum($eftPaymentArr_proc_arr),2);
				$veepPaymentProc = $CLSReports->numberFormat(array_sum($veepPaymentArr_proc_arr),2);
				$payment_cnt_proc = array_sum($payment_cnt_proc_arr);
				
	
		
				$csvData .= <<<DATA
					<tr style="height:30px;">
						<td class="text_10b" bgcolor="#FFFFFF" width="180" align="left" valign="top">$patientName</td>
						<td class="text_10b" bgcolor="#FFFFFF" width="100" align="center" valign="top">$date_of_service</td>
						<td class="text_10b" bgcolor="#FFFFFF" width="110" align="center" valign="top">$encounter_id</td>
						<td class="text_10b" bgcolor="#FFFFFF" width="100" align="left" valign="top">$paymentMode</td>
						<td class="text_10b" bgcolor="#FFFFFF" width="40" style="text-align:center" valign="top">$payment_cnt</td>
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" width="100" valign="top">$cashPayment</td>
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" width="100" valign="top">$checkPayment</td>
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" width="100" valign="top">$CCPayment</td>
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" width="100" valign="top">$moPayment</td>
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" width="100" valign="top">$eftPayment</td>
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" width="100" valign="top">$veepPayment</td>
						<td class="text_10b" bgcolor="#FFFFFF" width="45" style="text-align:left" valign="top">$operatorNameStr</td>
					</tr>
DATA;
			$csvData .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="180" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="110" align="left">Copay</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$paymentModeCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" width="40" align="center">$payment_cnt_copay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$cashPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$checkPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$ccPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$moPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$eftPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$veepPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" width="45" style="text-align:right"></td>
					</tr>
DATA;
		$csvData .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="180" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="110" align="left">Optical</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$paymentModeOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" width="40" align="center">$payment_cnt_opt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$cashPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$checkPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$ccPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$moPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$eftPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$veepPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" width="45" style="text-align:right"></td>
					</tr>
DATA;
		$csvData .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="180" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center"></td>
						<td class="text_12" bgcolor="#FFFFFF" width="110" align="left">Procedures</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">$paymentModeProc</td>
						<td class="text_12" bgcolor="#FFFFFF" width="40" align="center">$payment_cnt_proc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$cashPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$checkPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$ccPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$moPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$eftPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="100">$veepPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" width="45" style="text-align:right"></td>
					</tr>
DATA;
		
				$pdfData2 .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="150" align="left" valign="top">$patientName</td>
						<td class="text_12" bgcolor="#FFFFFF" width="80" align="center" valign="top">$date_of_service</td>
						<td class="text_12" bgcolor="#FFFFFF" width="59" align="left" valign="top">$encounter_id</td>
						<td class="text_12" bgcolor="#FFFFFF" width="50" align="left" valign="top">$paymentMode</td>
						<td class="text_12" bgcolor="#FFFFFF" width="20" style="text-align:right;" valign="top">$payment_cnt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="60" valign="top">$cashPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="50" valign="top">$checkPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="60" valign="top">$CCPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="55" valign="top">$moPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="50" valign="top">$eftPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right" width="50" valign="top">$veepPayment</td>
						<td class="text_12" bgcolor="#FFFFFF" width="45" style="text-align:left" valign="top">$operatorNameStr</td>
					</tr>
DATA;
			$pdfData2 .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12" bgcolor="#FFFFFF" align="left">Copay</td>
						<td class="text_12" bgcolor="#FFFFFF" align="left">$paymentModeCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" align="right">$payment_cnt_copay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$cashPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$checkPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$ccPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$moPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$eftPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$veepPaymentCopay</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right"></td>
					</tr>
DATA;
		$pdfData2 .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12" bgcolor="#FFFFFF" align="left">Optical</td>
						<td class="text_12" bgcolor="#FFFFFF" align="left">$paymentModeOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" align="right">$payment_cnt_opt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$cashPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$checkPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$ccPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$moPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$eftPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$veepPaymentOpt</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right"></td>
					</tr>
DATA;
		$pdfData2 .= <<<DATA
					<tr>
						<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
						<td class="text_12" bgcolor="#FFFFFF" align="center"></td>
						<td class="text_12" bgcolor="#FFFFFF" align="left">Procedures</td>
						<td class="text_12" bgcolor="#FFFFFF" align="left">$paymentModeProc</td>
						<td class="text_12" bgcolor="#FFFFFF" align="right">$payment_cnt_proc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$cashPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$checkPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$ccPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$moPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$eftPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">$veepPaymentProc</td>
						<td class="text_12" bgcolor="#FFFFFF" style="text-align:right"></td>
					</tr>
DATA;

	}

		$userName = $userNameArr[$primaryProviderId];
		$subCashTotal2 = $subCashTotal + $subCheckTotal + $subCCTotal + $subMoTotal + $subEftTotal + $subVeepTotal;
		$sub_adj_amt = array_sum($sub_adj_amt_arr);
		
		//--- NUMBER FORMAT FOR CASH SUB TOTAL ----
		$subCashTotal = $CLSReports->numberFormat($subCashTotal, 2);
		
		//--- NUMBER FORMAT FOR CHECK SUB TOTAL ----
		$subCheckTotal = $CLSReports->numberFormat($subCheckTotal, 2);
		
		//--- NUMBER FORMAT FOR CREDIT CARD SUB TOTAL ----
		$subCCTotal = $CLSReports->numberFormat($subCCTotal, 2);

		//--- NUMBER FORMAT FOR MONEY ORDER SUB TOTAL ----
		$subMoTotal = $CLSReports->numberFormat($subMoTotal, 2);

		//--- NUMBER FORMAT FOR EFT SUB TOTAL ----
		$subEftTotal = $CLSReports->numberFormat($subEftTotal, 2);

		//--- NUMBER FORMAT FOR VEEP SUB TOTAL ----
		$subVeepTotal = $CLSReports->numberFormat($subVeepTotal, 2);
		
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
			$grandTotalMoVal = array_sum($grandTotalMo);
			$grandTotalEftVal = array_sum($grandTotalEft);
			$grandTotalVeepVal = array_sum($grandTotalVeep);
			$TotalPayments = array_sum($grandTotalPayment);
			$grdTotalPayments=$TotalPayments;
			
			$grandTotalCashVal = $CLSReports->numberFormat($grandTotalCashVal,2);
			$grandTotalCheckVal = $CLSReports->numberFormat($grandTotalCheckVal,2);
			$grandTotalCCVal = $CLSReports->numberFormat($grandTotalCCVal,2);
			$grandTotalMoVal = $CLSReports->numberFormat($grandTotalMoVal,2);
			$grandTotalEftVal = $CLSReports->numberFormat($grandTotalEftVal,2);
			$grandTotalVeepVal = $CLSReports->numberFormat($grandTotalVeepVal,2);
			$TotalPayments = $CLSReports->numberFormat($TotalPayments,2);

			
			$footerTotalData .= <<<DATA
				<tr>
					<td align="center" colspan="12" bgcolor="#FFFFFF" class="text_12b">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">Cash</td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">Check</td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">Credit Card</td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">Money Order</td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">EFT</td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b">VEEP</td>
					<td align="left" bgcolor="#FFFFFF" class="text_12b">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_12b"></td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalCashVal</td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalCheckVal</td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalCCVal</td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalMoVal</td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalEftVal</td>
					<td align="right" bgcolor="#FFFFFF" class="text_12b">$grandTotalVeepVal</td>
					<td align="left" bgcolor="#FFFFFF" class="text_12b">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_12b">Total Payments:</td>
					<td align="left" colspan="7" bgcolor="#FFFFFF" class="text_12b">$TotalPayments</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
DATA;
			$footerTotalDataPDF .= <<<DATA
				<tr>
					<td align="center" colspan="12" bgcolor="#FFFFFF" class="text_12b">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_b">&nbsp;</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">Cash</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">Check</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">Credit Card</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">Money Order</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">EFT</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">Veep</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">&nbsp;</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_b">&nbsp;</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">$grandTotalCashVal</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">$grandTotalCheckVal</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">$grandTotalCCVal</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">$grandTotalMoVal</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">$grandTotalEftVal</td>
					<td align="left" bgcolor="#FFFFFF" class="text_b">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_b"></td>
					<td style="text-align:right" colspan="2" bgcolor= "#FFFFFF" class="text_b">Payments Total :</td>
					<td align="left" colspan="7" bgcolor="#FFFFFF" class="text_b">$TotalPayments</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
DATA;
		}
		$pat_id_count = count($pat_id_arr);	
		//--- PDF FILE DATA ----
		$pdfData .= <<<DATA
			<page backtop="30mm" backbottom="10mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$page_header_val2
			<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8">
				<tr>
					<td style="text-align:left" class="text_b_w" colspan="12">Physician Name : $userName</td>
				</tr>
				<tr>
					<td align="center" width="160" class="text_b_w">Patient Name-ID</td>
					<td align="center" width="60" class="text_b_w">DOS</td>
					<td align="center" width="55" class="text_b_w">E.ID</td>
					<td align="center" width="70" class="text_b_w">Method</td>
					<td align="center" width="20" class="text_b_w">Qty.</td>
					<td class="text_b_w" align="center" colspan="6">Payments</td>
					<td align="center" width="45" class="text_b_w">Opr</td>
				</tr>
				<tr>
					<td align="center" class="text_b_w" colspan="5"></td>
					<td align="center" width="50" class="text_b_w">Cash</td>
					<td align="center" width="50" class="text_b_w">Check</td>
					<td align="center" width="50" class="text_b_w">Credit Card</td>
					<td align="center" width="50" class="text_b_w">Money Order</td>
					<td align="center" width="50" class="text_b_w">EFT</td>
					<td align="center" width="50" class="text_b_w">VEEP</td>
					<td align="center" class="text_b_w"></td>
				</tr>				
			</table>
			</page_header>
			<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8">
				$pdfData2
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td colspan="5" class="text_b" style="text-align:right">Sub Total :</td>
					<td style="text-align:right" class="text_b">$subCashTotal</td>
					<td style="text-align:right" class="text_b">$subCheckTotal</td>
					<td style="text-align:right" class="text_b">$subCCTotal</td>
					<td style="text-align:right" class="text_b">$subMoTotal</td>
					<td style="text-align:right" class="text_b">$subEftTotal</td>
					<td style="text-align:right" class="text_b">$subVeepTotal</td>
					<td class="text_b">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td style="text-align:right" class="text_b"  colspan="5">Total Claims :</td>
					<td align="left" colspan="7" class="text_b">$pat_id_count</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td style="text-align:right" class="text_b" colspan="5">Total :</td>
					<td align="left" colspan="7" class="text_b">$subCashTotal2</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
				$footerTotalDataPDF
			</table>
			</page>
DATA;
		
			
		//--- GET CSV DATA ---
		$csvFileData .= <<<DATA
			$page_header_val
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">				
				<tr>
					<td width="100%" align="left" colspan="12" class="text_b_w">Payments</td>
				</tr>
				<tr>
					<td style="text-align:left" class="text_b_w" colspan="12">Physician Name : $userName</td>
				</tr>
				<tr>
					<td align="center" width="180" class="text_b_w">Patient Name-ID</td>
					<td align="center" width="100" class="text_b_w">DOS</td>
					<td align="center" width="100" class="text_b_w">E.ID</td>
					<td align="center" width="100" class="text_b_w">Method</td>
					<td align="center" width="30" class="text_b_w">Qty.</td>
					<td class="text_b_w" style="text-align:center" colspan="6">Payments</td>					
					<td align="center" width="30" class="text_b_w">Opr</td>
				</tr>
				<tr>
					<td align="center" class="text_b_w"></td>
					<td align="center" class="text_b_w"></td>
					<td align="center" class="text_b_w"></td>
					<td align="center" class="text_b_w"></td>
					<td align="center" class="text_b_w"></td>
					<td align="center" width="120" class="text_b_w">Cash</td>
					<td align="center" width="120" class="text_b_w">Check</td>
					<td align="center" width="120" class="text_b_w">Credit Card</td>
					<td align="center" width="120" class="text_b_w">Money Order</td>
					<td align="center" width="120" class="text_b_w">EFT</td>
					<td align="center" width="120" class="text_b_w">VEEP</td>
					<td align="center" class="text_b_w"></td>
				</tr>				
				$csvData
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td bgcolor="#FFFFFF" class="text_12b" colspan="2" style="text-align:right">Sub Total :</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCashTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCheckTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subCCTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subMoTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subEftTotal</td>
					<td style="text-align:right" bgcolor="#FFFFFF" class="text_12b">$subVeepTotal</td>
					<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" class="text_12b" bgcolor="#FFFFFF">Total Claims :</td>
					<td align="left" colspan="7" bgcolor="#FFFFFF" class="text_12b">$pat_id_count</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
				<tr>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td align="center" bgcolor="#FFFFFF" class="text_12b"></td>
					<td style="text-align:right" colspan="2" class="text_12b" bgcolor="#FFFFFF">Total :</td>
					<td align="left" colspan="7" bgcolor="#FFFFFF" class="text_12b">$subCashTotal2</td>
				</tr>
				<tr><td class="total-row" colspan="12"></td></tr>
				$footerTotalData
			</table>
DATA;
		$page_header_val = NULL;
	}
}
}else{

	//	-----	CHECK IN/OUT PAYMENTS   ----------
	
	//--- GET ALL PAYMENTS HISTORY OF A PATIENT ---
		$pay_query ="Select schedule_appointments.sa_facility_id, schedule_appointments.sa_doctor_id, patient_data.fname,patient_data.mname, patient_data.lname,
		check_in_out_payment.*, DATE_FORMAT(check_in_out_payment.created_on, '".$SqlDateFormat."') as created_on, check_in_out_payment_details.*,check_in_out_payment_details.id as cioPaydetID, 
		DATE_FORMAT(check_in_out_payment.delete_date, '".$SqlDateFormat."') as deleteDate, DATE_FORMAT(check_in_out_payment_details.delete_date, '".$SqlDateFormat."') as deleteDateDet,
	 	check_in_out_payment_details.delete_time as deleteTimeDet, check_in_out_payment_details.delete_operator_id as delete_operator_id_det, check_in_out_payment.created_by  
		from check_in_out_payment 
		join schedule_appointments on check_in_out_payment.sch_id = schedule_appointments.id 
		join patient_data on patient_data.id = check_in_out_payment.patient_id
		join check_in_out_payment_details on 
		check_in_out_payment.payment_id = check_in_out_payment_details.payment_id
		where schedule_appointments.sa_doctor_id IN($Physician) 
		and created_on between '$startDate' and '$endDate'  
		and patient_data.lname != '' AND check_in_out_payment.total_payment>0";
	if($strSelFac){
		$pay_query .=" and schedule_appointments.sa_facility_id IN($strSelFac)";
	}
	if($operatorIdN){
		$pay_query .=" and check_in_out_payment.created_by IN($operatorIdN)";
	}
	
	$pay_query.=" ORDER BY $sortByPat_comm $sortByDot_ci_comm schedule_appointments.sa_facility_id, schedule_appointments.sa_doctor_id, check_in_out_payment.payment_id desc, patient_data.lname, patient_data.fname";

	$paymentDetIdArr =array();
	$grdTotCopay  =array();
	$grdTotOptical  =array();	
	$operatorArr=array();
	$pay_query_rs = imw_query($pay_query);
	$payQryResSize = sizeof($pay_query_rs);
	while($payQryRes = imw_fetch_assoc($pay_query_rs)){
	
		##########################################################
		#query to get refund detail for current ci/co payments if any
		##########################################################
		$refundAmt=0;
		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id = '".$payQryRes['cioPaydetID']."'")or die(imw_error().'_471');
		$rsRef=imw_fetch_array($qryRef);
		$refundAmt=$rsRef['ref_amt'];
		
		imw_free_result($qryRef);
		
		$printFile = true;
		$facilityId=$payQryRes['sa_facility_id'];
		$doctorId=$payQryRes['sa_doctor_id'];
		$patientId=$payQryRes['patient_id'];
		$paymentId=$payQryRes['payment_id'];
		$created_by=$payQryRes['created_by'];

		$paymentDetIdArr[$payQryRes['id']] = $payQryRes['id']; 

		if($payQryRes['del_status']=='0' && $payQryRes['status']=='0'){
			$facilityArr[$facilityId] = $allFacilityArr[$facilityId];
			$doctorArr[$facilityId][$doctorId] = $userNameArr[$doctorId];
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['payment_id'] = $paymentId;
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['pat_name'] = $payQryRes['lname'].' '.$payQryRes['fname'].'-'.$patientId;
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['pay_date'] = $payQryRes['created_on']; 
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['payment_type'] = $payQryRes['payment_type']; 
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['total_payment'] = $payQryRes['total_payment'];
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['operator_name'] = $created_by;
			
			$patientDet[$patientId][$paymentId][] = $payQryRes;

			$patientAmtArr[$paymentId]['item_payment'][] = $payQryRes['item_payment']-$refundAmt;
			$patientAmtArr[$paymentId]['item_payment_ref'][] = $refundAmt;
			
			//creating arry to maintain refund value
			$patientAmtArr[$payQryRes['cioPaydetID']]['item_refund']= $refundAmt;
			
			$totPatientAmtArr[$paymentId]['total_payment'] = array_sum($patientAmtArr[$paymentId]['item_payment']);
			$totPatientAmtArr[$paymentId]['total_payment_ref'] = array_sum($patientAmtArr[$paymentId]['item_payment_ref']);
	
			// GRAND TOTAL ARRAY FOR COPAY AND OPTICAL
			if(in_array($payQryRes['item_id'], $arrCopayIds)){
				$grdTotCopay[] = $payQryRes['item_payment']-$refundAmt;
			}
			if(in_array($payQryRes['item_id'], $arrOpticalIds)){
				$grdTotOptical[] = $payQryRes['item_payment']-$refundAmt;
			}
		}else{
			// DELETED RECORDS
			$delFacilityArr[$facilityId] = $allFacilityArr[$facilityId];
			$delDoctorArr[$facilityId][$doctorId] = $userNameArr[$doctorId];
			
			$delPaymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['payment_id'] = $paymentId;
			$delPaymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['pat_name'] = $payQryRes['lname'].' '.$payQryRes['fname'].'-'.$patientId;
			$delPaymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['pay_date'] = $payQryRes['created_on']; 
			$delPaymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['payment_type'] = $payQryRes['payment_type']; 
			$delPaymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['total_payment'] = $payQryRes['total_payment'];
			
			$delPatientDet[$patientId][$paymentId][] = $payQryRes;
			
			$delPatientAmtArr[$paymentId]['item_payment'][] = $payQryRes['item_payment']-$refundAmt;
			$delTotPatientAmtArr[$paymentId]['total_payment'] = array_sum($delPatientAmtArr[$paymentId]['item_payment']);
			
			$delPatientAmtArr[$paymentId]['item_payment_ref'][] = $refundAmt;
			$delTotPatientAmtArr[$paymentId]['total_payment_ref'] = array_sum($delPatientAmtArr[$paymentId]['item_payment_ref']);
	
			// GRAND TOTAL ARRAY FOR COPAY AND OPTICAL
			if(in_array($payQryRes['item_id'], $arrCopayIds)){
				$delGrdTotCopay[] = $payQryRes['item_payment']-$refundAmt;
			}
			if(in_array($payQryRes['item_id'], $arrOpticalIds)){
				$delGrdTotOptical[] = $payQryRes['item_payment']-$refundAmt;
			}	
		}
	}
	
	
//	echo "<pre>"; print_r($delTotPatientAmtArr);

	// GET PROC_CODE and PAID AMT
	$appliedDetArr = array();
	$paymentDetIdStr = implode(",", $paymentDetIdArr);
	$procQry = "Select check_in_out_payment_post.check_in_out_payment_id, check_in_out_payment_post.check_in_out_payment_detail_id, check_in_out_payment_details.status, 
	patient_charge_list_details.charge_list_detail_id ,patient_charge_list_details.procCode, cpt_fee_tbl.cpt4_code, patient_charges_detail_payment_info.paidForProc, patient_charges_detail_payment_info.deletePayment , patient_charges_detail_payment_info.unapply  
	FROM check_in_out_payment_post 
	LEFT JOIN check_in_out_payment_details ON check_in_out_payment_details.id = check_in_out_payment_post.check_in_out_payment_detail_id    
	LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_detail_id = check_in_out_payment_post.charge_list_detail_id 
	LEFT JOIN patient_charges_detail_payment_info ON patient_charges_detail_payment_info.payment_id = check_in_out_payment_post.acc_payment_id  
	LEFT JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode 
	WHERE check_in_out_payment_post.check_in_out_payment_detail_id IN(".$paymentDetIdStr.")";
	
	$procQryRs	= imw_query($procQry);
	while($procQryRes = imw_fetch_assoc($procQryRs)){
		$check_in_out_id = $procQryRes['check_in_out_payment_id'];
		$check_in_out_det_id = $procQryRes['check_in_out_payment_detail_id'];
		
		if($procQryRes['deletePayment']=='0' && $procQryRes['status']=='0' && $procQryRes['unapply']=='0'){
			$appliedDetArr[$check_in_out_id][$check_in_out_det_id]['cpt4_code'][] = $procQryRes['cpt4_code'];
			$appliedDetArr[$check_in_out_id][$check_in_out_det_id]['paidForProc'][] = $procQryRes['paidForProc'];
	
			$tempArr[$check_in_out_id][] = $procQryRes['paidForProc'];
			$totAppliedDetArr[$check_in_out_id] = array_sum($tempArr[$check_in_out_id]);
		}else{
			// DELETED RECORDS
			$delAppliedDetArr[$check_in_out_id][$check_in_out_det_id]['cpt4_code'][] = $procQryRes['cpt4_code'];
			$delAppliedDetArr[$check_in_out_id][$check_in_out_det_id]['paidForProc'][] = $procQryRes['paidForProc'];
			
			$delTempArr[$check_in_out_id][] = $procQryRes['paidForProc'];	
			$delTotAppliedDetArr[$check_in_out_id] = array_sum($delTempArr[$check_in_out_id]);
		}
	}
	

//echo "<pre>"; print_r($delAppliedDetArr);

	//--- HEADER FOR PDF FILE ---
	$fileHeaderData = <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr>	
				<td align="left" class="rptbx1" style="width:229px;">Front Desk Collection Report : Detail</td>
				<td align="left" class="rptbx2" style="width:290px;">Selected Group : $group_name</td>	
				<td align="left" class="rptbx3" style="width:270px;">Selected Facility : $practice_name</td>
				<td align="left" class="rptbx3" style="width:222px;">Created by $op_name on $curDate </td>
			</tr>
			<tr>	
				<td align="left" class="rptbx1">Selected Physician : $physican_name</td>
				<td align="left" class="rptbx2">Selected Operator : $operator_name</td>
				<td align="left" class="rptbx3" colspan="2">Selected Date : $Start_date To $End_date</td>
			</tr>
	</table>
DATA;
$fileHeaderData2 = <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="720" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" class="rptbx1" width="200" style="font-size: 11px;">FD Collection Report: Detail</td>
				<td align="left" class="rptbx2" width="320" colspan="2" style="font-size: 11px;">Selected Group : $group_name</td>	
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
	$csvFileData = $fileHeaderData;
	
	$grdTotCash =array();
	$grdTotCheck =array();
	$grdTotCC =array();
	$grdTotMo =array();
	$grdTotEft =array();
	
	foreach($facilityArr as $facId => $facName){
		if(sizeof($paymentDetArr[$facId])<=0){ break; }

		$csvFileData.= <<<DATA
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>	
				<td align="left" class="text_b_w">Facility : $facName</td>
			</tr>
		</table>
DATA;
		$pdfData2.= <<<DATA
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>	
				<td align="left" class="text_b_w" style="width:755px;">Facility : $facName</td>
			</tr>
		</table>
DATA;
		foreach($doctorArr[$facId] as $docId => $docName){

			if(sizeof($paymentDetArr[$facId][$docId])<=0){ break; }
			$operator_name=$operatorArr[$facId][$docId];
			$docTotPayAmtArr = array();
			$docTotAppAmtArr = array();
			$docTotBalAmtArr = array();			
			$csvFileData.= <<<DATA
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
				<tr>	
					<td align="left" colspan="11" class="text_b_w">Physician : $docName</td>
				</tr>
				<tr class="text_b_w">	
					<td align="center" style="width:250px">Patient Name - ID</td>
					<td align="center" style="width:150px">Payment Date</td>
					<td align="center" style="width:100px">Payment</td>
					<td align="center" style="width:100px">Applied</td>
					<td align="center" style="width:100px">Balance</td>
					<td align="center" style="width:80px">Cash</td>
					<td align="center" style="width:80px">Check</td>
					<td align="center" style="width:80px">CC</td>
					<td align="center" style="width:80px">MO</td>
					<td align="center" style="width:80px">EFT</td>
				</tr>
DATA;
			$pdfData2.= <<<DATA
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
				<tr>	
					<td align="left" colspan="10" class="text_b_w">Physician : $docName</td>
				</tr>
				<tr class="text_b_w">	
					<td align="center" class="text_b_w" style="width:175px">Patient Name - ID</td>
					<td align="center" class="text_b_w" style="width:85px">Payment Date</td>
					<td align="center" class="text_b_w" style="width:55px">Payment</td>
					<td align="center" class="text_b_w" style="width:55px">Applied</td>
					<td align="center" class="text_b_w" style="width:55px">Balance</td>
					<td align="center" class="text_b_w" style="width:55px">Cash</td>
					<td align="center" class="text_b_w" style="width:55px">Check</td>
					<td align="center" class="text_b_w" style="width:55px">CC</td>
					<td align="center" class="text_b_w" style="width:55px">MO</td>
					<td align="center" class="text_b_w" style="width:55px">EFT</td>
				</tr>
DATA;

			$i=0;
			$arrSubTot=array();
			foreach($paymentDetArr[$facId][$docId] as $patID => $patDetails){
				
				//echo "<pre>"; print_r($patDet);
				
				foreach($patDetails as $paymentID => $patDet){
					$patName =$patDet['pat_name'];
					$payDate =$patDet['pay_date'];
					$paymentID = $patDet['payment_id'];
					$oprName = $userNameArr[$patDet['operator_name']];
					$totPatPayment = $CLSReports->numberFormat($totPatientAmtArr[$paymentID]['total_payment'],2);
					$totPatPayment = ($totPatPayment=='')? $showCurrencySymbol."00.00": $totPatPayment;
					$totAppliedAmt = $CLSReports->numberFormat($totAppliedDetArr[$paymentID],2);				
					$totAppliedAmt = ($totAppliedAmt=='')? $showCurrencySymbol."00.00": $totAppliedAmt;
					$totBalAmt = $CLSReports->numberFormat($totPatientAmtArr[$paymentID]['total_payment'] - $totAppliedDetArr[$paymentID],2);				
					$totBalAmt = ($totBalAmt=='')? $showCurrencySymbol."00.00": $totBalAmt;
					
					$csvFileData.= <<<DATA
						<tr style="height:20px;">	
							<td align="left" style="width:250px" class="text11b" bgcolor="#FFFFFF">$patName</td>
							<td align="left" style="width:150px" class="text11b" bgcolor="#FFFFFF">$payDate</td>
							<td align="right" style="width:100px" class="text11b" bgcolor="#FFFFFF">$totPatPayment</td>
							<td align="right" style="width:100px" class="text11b" bgcolor="#FFFFFF">$totAppliedAmt</td>
							<td align="right" style="width:100px" class="text11b" bgcolor="#FFFFFF">$totBalAmt</td>
							<td align="right" style="width:80px" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" style="width:80px" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" style="width:80px" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" style="width:80px" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" style="width:80px" class="text11b" bgcolor="#FFFFFF"></td>
							
						</tr>
DATA;
					$pdfData2.= <<<DATA
						<tr style="height:20px;">	
							<td align="left" class="text_12" bgcolor="#FFFFFF">$patName</td>
							<td align="left" class="text_12" bgcolor="#FFFFFF">$payDate</td>
							<td align="right" class="text_12" bgcolor="#FFFFFF">$totPatPayment</td>
							<td align="right" class="text_12" bgcolor="#FFFFFF">$totAppliedAmt</td>
							<td align="right" class="text_12" bgcolor="#FFFFFF">$totBalAmt</td>
							<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
							<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
							<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
							<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
							<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
						</tr>
DATA;
	
					$chkin = $chkout =0;
	
					foreach($patientDet[$patID][$paymentID] as $pId => $payDetail){
						$cash = $check = $cc = $mo =$eft = '';
						$paymentType = strtoupper($payDetail['payment_type']);
						$paymentMethod = strtoupper($payDetail['payment_method']);
						$payId = $payDetail['payment_id'];
						
						$refundAmt=0;
						$refundAmt=$patientAmtArr[$payDetail['cioPaydetID']]['item_refund'];
						//$refundAmt=$payDetail['item_payment_ref'];
						
						$paymentDetId = $payDetail['id'];
						$payTypeName = $allINOutFields[$payDetail['item_id']];
						$pdf_payTypeName=$payTypeName;						
						if(strlen($pdf_payTypeName)>18){
							$pdf_payTypeName=substr($pdf_payTypeName,0, 16).'..';
						}
						$payAmt = $CLSReports->numberFormat($payDetail['item_payment']-$refundAmt,2);
						$payAmt = ($payAmt=='')? $showCurrencySymbol."00.00": $payAmt;
						
						$totItemApply = array_sum($appliedDetArr[$payId][$paymentDetId]['paidForProc']);
						$totalItemApply = $CLSReports->numberFormat($totItemApply,2);
						$totalItemApply = ($totalItemApply=='')? $showCurrencySymbol."00.00": $totalItemApply;
						$totBal =($payDetail['item_payment']-$refundAmt) - $totItemApply;
						if($totItemApply < $payDetail['item_payment']-$refundAmt) {
							$totalItemBal = $CLSReports->numberFormat($totBal,2);
						}else{
							$totalItemBal = $showCurrencySymbol."00.00";
						}

						
						// SET CASH, CHECK , CC, MONEY ORDER, EFT ARAYS FOR GRAND TOTAL
						if($paymentMethod =='CASH'){
							$grdTotCash[] =  $payDetail['item_payment']-$refundAmt; 
							$cash= $CLSReports->numberFormat($payDetail['item_payment']-$refundAmt,2);
							$arrSubTot['cash']+=$payDetail['item_payment']-$refundAmt;
						}
						if($paymentMethod =='CHECK'){
							$grdTotCheck[] =  $payDetail['item_payment']-$refundAmt; 						
							$check= $CLSReports->numberFormat($payDetail['item_payment']-$refundAmt,2);
							$arrSubTot['check']+=$payDetail['item_payment']-$refundAmt;
						}
						if($paymentMethod =='CREDIT CARD'){
							$grdTotCC[] =  $payDetail['item_payment']-$refundAmt; 						
							$cc= $CLSReports->numberFormat($payDetail['item_payment']-$refundAmt,2);
							$arrSubTot['cc']+=$payDetail['item_payment']-$refundAmt;
						}
						if($paymentMethod =='MONEY ORDER'){
							$grdTotMo[] =  $payDetail['item_payment']-$refundAmt;
							$mo= $CLSReports->numberFormat($payDetail['item_payment']-$refundAmt,2);
							$arrSubTot['mo']+=$payDetail['item_payment']-$refundAmt;
						}
						if($paymentMethod =='EFT'){
							$grdTotEft[] =  $payDetail['item_payment']-$refundAmt;
							$eft= $CLSReports->numberFormat($payDetail['item_payment']-$refundAmt,2);
							$arrSubTot['eft']+=$payDetail['item_payment']-$refundAmt;
						}
	
						// Sub Total Array 
						$subTotPayAmtArr[$payId][] = $payDetail['item_payment']-$refundAmt;
						$subTotBalAmtArr[$payId][] = $totBal;
										
						if(($paymentType=='CHECKIN' && $chkin==0) || ($paymentType=='CHECKOUT' && $chkout==0)){
							$csvFileData.= <<<DATA
								<tr>	
									<td align="right" class="text11b" bgcolor="#FFFFFF">$paymentType</td>
									<td align="left" style="padding-left:10px;" colspan="10" class="text11" bgcolor="#FFFFFF"><b>Operator Name:</b> $oprName</td>
								</tr>
DATA;
							$pdfData2.= <<<DATA
								<tr>	
									<td align="right" class="text_12" bgcolor="#FFFFFF">$paymentType</td>
									<td align="left" style="padding-left:10px;" colspan="9" class="text_12" bgcolor="#FFFFFF"><b>Operator Name:</b> $oprName</td>
								</tr>
DATA;
							if($paymentType=='CHECKIN'){ $chkin=1;}
							if($paymentType=='CHECKOUT'){ $chkout=1;}
							}
							
							if($refundAmt>0)
							$redRow='style="color:#FF0000" title="Refund '. $CLSReports->numberFormat($refundAmt).'"';
							else
							$redRow='';
					
						$csvFileData.= <<<DATA
							<tr>	
								<td align="left" class="text" bgcolor="#FFFFFF"></td>
								<td align="left" class="text" bgcolor="#FFFFFF">$payTypeName</td>
								<td align="right" class="text" bgcolor="#FFFFFF" $redRow>$payAmt</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$totalItemApply</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$totalItemBal</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$cash</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$check</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$cc</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$mo</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$eft</td>
							</tr>
DATA;
						$pdfData2.= <<<DATA
							<tr>	
								<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
								<td align="left" class="text_12" bgcolor="#FFFFFF">$pdf_payTypeName</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF" $redRow>$payAmt</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$totalItemApply</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$totalItemBal</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$cash</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$check</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$cc</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$mo</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$eft</td>
							</tr>
DATA;
	
	
						$appliedSize = sizeof($appliedDetArr[$payId][$paymentDetId]['cpt4_code']);
						if($appliedSize>0){
							
							for($j=0; $j<$appliedSize; $j++){
								$item_bal= '';
								$cpt4_code 	= $appliedDetArr[$payId][$paymentDetId]['cpt4_code'][$j];
								$appAmt = $appliedDetArr[$payId][$paymentDetId]['paidForProc'][$j];
								$appliedAmt = $CLSReports->numberFormat($appAmt,2);
								
								//SUB TOTAL ARRAY
								$subTotAppAmtArr[$payId][] = $appAmt;
								
								$csvFileData.= <<<DATA
									<tr>	
										<td align="right" colspan="2" class="text" bgcolor="#FFFFFF">$cpt4_code</td>
										<td align="right" class="text" bgcolor="#FFFFFF"></td>
										<td align="right" class="text" bgcolor="#FFFFFF">$appliedAmt</td>
										<td align="left" class="text" bgcolor="#FFFFFF"></td>
										<td align="left" class="text" bgcolor="#FFFFFF"></td>
										<td align="left" class="text" bgcolor="#FFFFFF"></td>
										<td align="left" class="text" bgcolor="#FFFFFF"></td>
										<td align="left" class="text" bgcolor="#FFFFFF"></td>
										<td align="left" class="text" bgcolor="#FFFFFF"></td>
									</tr>
DATA;
								$pdfData2.= <<<DATA
									<tr>	
										<td align="right" colspan="2" class="text_12" bgcolor="#FFFFFF">$cpt4_code</td>
										<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
										<td align="right" class="text_12" bgcolor="#FFFFFF">$appliedAmt</td>
										<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
										<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
										<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
										<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
										<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
										<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
									</tr>
DATA;
	
							}
						}
					}
	
					// DISPLAY SUB TOTAL FOR PATIENT
					$TotPayAmt = array_sum($subTotPayAmtArr[$payId]);
					$TotAppAmt = array_sum($subTotAppAmtArr[$payId]);
					$TotBalAmt = array_sum($subTotBalAmtArr[$payId]);
					
	/*				$subTotPayAmt = $CLSReports->numberFormat($TotPayAmt,2);
					$subTotAppAmt = $CLSReports->numberFormat($TotAppAmt,2);
					$subTotBalAmt = $CLSReports->numberFormat($TotBalAmt,2);
	*/				
					//ARRAY FOR DOCTOR TOTALS
					$docTotPayAmtArr[$docId][] = $TotPayAmt;
					$docTotAppAmtArr[$docId][] = $TotAppAmt;
					$docTotBalAmtArr[$docId][] = $TotBalAmt;
					
				$i++;
				}
			}
			// DOCTOR TOTALS 
			$dTotPayAmt = array_sum($docTotPayAmtArr[$docId]);
			$dTotAppAmt = array_sum($docTotAppAmtArr[$docId]);
			$dTotBalAmt = array_sum($docTotBalAmtArr[$docId]);
			
			$docTotPayAmt = $CLSReports->numberFormat($dTotPayAmt,2);
			$docTotAppAmt = $CLSReports->numberFormat($dTotAppAmt,2);
			$docTotBalAmt = $CLSReports->numberFormat($dTotBalAmt,2);
			$docTotCash = $CLSReports->numberFormat($arrSubTot['cash'],2);
			$docTotCheck = $CLSReports->numberFormat($arrSubTot['check'],2);
			$docTotCC = $CLSReports->numberFormat($arrSubTot['cc'],2);
			$docTotMO = $CLSReports->numberFormat($arrSubTot['mo'],2);
			$docTotEFT = $CLSReports->numberFormat($arrSubTot['eft'],2);
			

			// GRAND TOTALS ARRAY
			$grdTotPayAmtArr[] = $dTotPayAmt;
			$grdTotAppAmtArr[] = $dTotAppAmt;
			$grdTotBalAmtArr[] = $dTotBalAmt;
			$arrGrdTot['cash']+= $arrSubTot['cash'];
			$arrGrdTot['check']+= $arrSubTot['check'];
			$arrGrdTot['cc']+= $arrSubTot['cc'];
			$arrGrdTot['mo']+= $arrSubTot['mo'];
			$arrGrdTot['eft']+= $arrSubTot['eft'];		

			$csvFileData.= <<<DATA
				<tr><td class="total-row" colspan="10"></td></tr>
				<tr style="height:20px">	
					<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">Sub Total&nbsp;</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotPayAmt</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotAppAmt</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotBalAmt</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotCash</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotCheck</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotCC</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotMO</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotEFT</td>
				</tr>
				<tr><td class="total-row" colspan="10"></td></tr>
				</table>
DATA;
			$pdfData2.= <<<DATA
				<tr><td class="total-row" colspan="10"></td></tr>
				<tr style="height:20px">	
					<td align="right" class="text_b" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">Sub Total&nbsp;</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotPayAmt</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotAppAmt</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotBalAmt</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotCash</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotCheck</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotCC</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotMO</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotEFT</td>
				</tr>
				<tr><td class="total-row" colspan="10"></td></tr>
				</table>
DATA;

		} // END DOCTOR
	}
	
	// GRAND TOTALS
	if(sizeof($paymentDetArr)>0){
		$grdTotPayAmt = array_sum($grdTotPayAmtArr);
		$grdAmt = $grdTotPayAmt;
		$grdTotPayAmt = $CLSReports->numberFormat($grdTotPayAmt,2);
		$grdTotAppAmt = $CLSReports->numberFormat(array_sum($grdTotAppAmtArr),2);
		$grdTotBalAmt = $CLSReports->numberFormat(array_sum($grdTotBalAmtArr),2);
		
		$grdCash = array_sum($grdTotCash);	
		$grdTotCashAmt = $CLSReports->numberFormat($grdCash,2);
		$grdTotCashAmt =($grdTotCashAmt=='')? $showCurrencySymbol."00.00" : $grdTotCashAmt;		
		$grdCheck = array_sum($grdTotCheck);	
		$grdTotCheckAmt = $CLSReports->numberFormat($grdCheck,2);
		$grdTotCheckAmt =($grdTotCheckAmt=='')? $showCurrencySymbol."00.00" : $grdTotCheckAmt;	
		$grdCC = array_sum($grdTotCC);		
		$grdTotCCAmt = $CLSReports->numberFormat($grdCC,2);
		$grdTotCCAmt =($grdTotCCAmt=='')? $showCurrencySymbol."00.00" : $grdTotCCAmt;		
		$grdMo = array_sum($grdTotMo);	
		$grdTotMoAmt = $CLSReports->numberFormat($grdMo,2);
		$grdTotMoAmt =($grdTotMoAmt=='')? $showCurrencySymbol."00.00" : $grdTotMoAmt;	
		$grdEft = array_sum($grdTotEft);	
		$grdTotEftAmt = $CLSReports->numberFormat($grdEft,2);
		$grdTotEftAmt =($grdTotEftAmt=='')? $showCurrencySymbol."00.00" : $grdTotEftAmt;	
		
		
		$grdTotAmtType = $grdCash + $grdCheck + $grdCC + $grdMo + $grdEft; 
		$grdTotAmtType = $CLSReports->numberFormat($grdTotAmtType,2);
		$grdTotAmtType =($grdTotAmtType=='')? $showCurrencySymbol."00.00" : $grdTotAmtType;		
	
		$acctAmt = 0;
		$grdTotCopayAmt = array_sum($grdTotCopay);
		$acctAmt = $grdTotCopayAmt;
		$grdTotCopayAmt = $CLSReports->numberFormat($grdTotCopayAmt,2);
		$grdTotCopayAmt =($grdTotCopayAmt=='')? $showCurrencySymbol."00.00" : $grdTotCopayAmt;		
	
		$grdTotOpticalAmt = array_sum($grdTotOptical);
		$acctAmt+= $grdTotOpticalAmt;
		$grdTotOpticalAmt = $CLSReports->numberFormat($grdTotOpticalAmt,2);
		$grdTotOpticalAmt =($grdTotOpticalAmt=='')? $showCurrencySymbol."00.00" : $grdTotOpticalAmt;		
		
		$grdProc = $grdAmt - $acctAmt;
		$grdTotProcAmt = $CLSReports->numberFormat($grdProc,2);
		$grdTotProcAmt =($grdTotProcAmt=='')? $showCurrencySymbol."00.00" : $grdTotProcAmt;		
		
		$grdMethod = $acctAmt + $grdProc;
		$grdTotAmtMethod = $CLSReports->numberFormat($grdMethod,2);
		$grdTotAmtMethod =($grdTotAmtMethod=='')? $showCurrencySymbol."00.00" : $grdTotAmtMethod;		
	
		$grdTotCash=$CLSReports->numberFormat($arrGrdTot['cash'],2);
		$grdTotCheck=$CLSReports->numberFormat($arrGrdTot['check'],2);
		$grdTotCC=$CLSReports->numberFormat($arrGrdTot['cc'],2);
		$grdTotMO=$CLSReports->numberFormat($arrGrdTot['mo'],2);
		$grdTotEFT=$CLSReports->numberFormat($arrGrdTot['eft'],2);
		
		$csvFileData.= <<<DATA
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" colspan="10" class="text10" ></td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr style="height:20px">	
				<td align="right" class="text11b" style="width:150px" bgcolor="#FFFFFF"></td>
				<td align="right" class="text11b" style="width:250px" bgcolor="#FFFFFF">Grand Total&nbsp;</td>
				<td align="right" class="text11b" style="width:100px" bgcolor="#FFFFFF">$grdTotPayAmt</td>
				<td align="right" class="text11b" style="width:100px" bgcolor="#FFFFFF">$grdTotAppAmt</td>
				<td align="right" class="text11b" style="width:100px" bgcolor="#FFFFFF">$grdTotBalAmt</td>
				<td align="right" class="text11b" style="width:80px" bgcolor="#FFFFFF">$grdTotCash</td>
				<td align="right" class="text11b" style="width:80px" bgcolor="#FFFFFF">$grdTotCheck</td>
				<td align="right" class="text11b" style="width:80px" bgcolor="#FFFFFF">$grdTotCC</td>
				<td align="right" class="text11b" style="width:80px" bgcolor="#FFFFFF">$grdTotMO</td>
				<td align="right" class="text11b" style="width:80px" bgcolor="#FFFFFF">$grdTotEFT</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
	
			<tr style="height:30px"><td colspan="10" bgcolor="#FFFFFF"></td></tr>
			<tr>	
				<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>
				<td align="right" class="text11b" bgcolor="#FFFFFF" >Copay</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF" >$grdTotCopayAmt</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF" colspan="7"></td>
			</tr>
			<tr>
				<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>	
				<td align="right" class="text11b" bgcolor="#FFFFFF" >Optical</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF" >$grdTotOpticalAmt</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF" colspan="7"></td>
			</tr>
			<tr>
				<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>	
				<td align="right" class="text11b" bgcolor="#FFFFFF" >Procedures</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF" >$grdTotProcAmt</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF" colspan="7"></td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr style="height:20px">
				<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>	
				<td align="right" class="text11b" bgcolor="#FFFFFF">Grand Total&nbsp;</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF">$grdTotAmtMethod</td>
				<td align="left"  class="text11b" bgcolor="#FFFFFF" colspan="7"></td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
			
		</table>
DATA;
		$pdfData2.= <<<DATA
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr style="height:20px">	
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:150px"></td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:80px;">Grand Total&nbsp;</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:59px;">$grdTotPayAmt</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:59px;">$grdTotAppAmt</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:59px;">$grdTotBalAmt</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:59px;">$grdTotCash</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:59px;">$grdTotCheck</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:59px;">$grdTotCC</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:59px;">$grdTotMO</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:59px;">$grdTotEFT</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr ><td colspan="10" bgcolor="#FFFFFF" style="height:20px"></td></tr>
			<tr>	
				<td align="right" class="text_b" ></td>
				<td align="right" class="text_b" >Copay</td>
				<td align="right" class="text_b" >$grdTotCopayAmt</td>
				<td align="right" class="text_b" colspan="7"></td>
			</tr>
			<tr>	
				<td align="right" class="text_b" ></td>
				<td align="right" class="text_b" >Optical</td>
				<td align="right" class="text_b" >$grdTotOpticalAmt</td>
				<td align="right" class="text_b" colspan="7"></td>
			</tr>
			<tr>	
				<td align="right" class="text_b" ></td>
				<td align="right" class="text_b" >Procedures</td>
				<td align="right" class="text_b" >$grdTotProcAmt</td>
				<td align="right" class="text_b" colspan="7"></td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr style="height:20px" >	
				<td align="right" class="text_b" ></td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">Grand Total&nbsp;</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">$grdTotAmtMethod</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF" colspan="7"></td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
		</table>
DATA;
	
			$pdfData.= <<<DATA
				<page backtop="9mm" backbottom="10mm">
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
				$fileHeaderData2
				</page_header>
				$pdfData2
				</page>
DATA;
	}

// DELETED CI/CO RECORDS
if(sizeof($delFacilityArr)>0){
$fileHeaderData = $delcsvFileData = $delpdfData2 ='';
$subTotPayAmtArr = array();
$subTotBalAmtArr = array();
$subTotAppAmtArr = array();
$grdTotPayAmtArr = array();
$grdTotAppAmtArr = array();
$grdTotBalAmtArr = array();


	//--- HEADER FOR PDF FILE ---
	$fileHeaderData = <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr>	
				<td align="left" class="text_b_w" colspan="4" style="text-align:center">Deleted Records</td>
			</tr>
			<tr>	
				<td align="left" class="rptbx1" style="width:229px;">Front Desk Collection Report : Detail</td>
				<td align="left" class="rptbx2" style="width:290px;">Selected Group : $group_name</td>	
				<td align="left" class="rptbx3" style="width:270px;">Selected Facility : $practice_name</td>
				<td align="left" class="rptbx3" style="width:222px;">Created by $op_name on $curDate </td>
			</tr>
			<tr>	
				<td align="left" class="rptbx1">Selected Physician : $physican_name</td>
				<td align="left" class="rptbx2">Selected Operator : $operator_name</td>
				<td align="left" class="rptbx3" colspan="2">Selected Date : $Start_date To $End_date</td>
			</tr>
	</table>
DATA;
	$fileHeaderData2 = <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr>	
				<td align="left" class="text_b_w" colspan="4" style="text-align:center">Deleted Records</td>
			</tr>
			
			
			
			
			
			<tr>	
				<td align="left" class="rptbx1" style="background-color:#1f759f; color:#FFF; text-align:left; width:200px;">FD Collection Report : Detail</td>
				<td align="left" class="rptbx2" style="background-color:#1f759f; color:#FFF; text-align:left; width:310px;" colspan="2">Selected Group : $group_name</td>	
				<td align="left" class="rptbx3" style="background-color:#1f759f; color:#FFF; text-align:left; width:200px;">Created by $op_name on $curDate </td>
			</tr>
			<tr>
				<td align="left" class="rptbx1" style="background-color:#1f759f; color:#FFF; text-align:left;">Sel Facility : $practice_name</td>
				<td align="left" class="rptbx2" style="background-color:#1f759f; color:#FFF; text-align:left;">Sel Physician : $physician_name</td>
				<td align="left" class="rptbx2" style="background-color:#1f759f; color:#FFF; text-align:left;">Sel Operator : $operator_name</td>
				<td align="left" class="rptbx3" style="background-color:#1f759f; color:#FFF; text-align:left;">Sel Date : $Start_date To $End_date</td>
			</tr>
		</table>
DATA;
	
	$grdTotCash =array();
	$grdTotCheck =array();
	$grdTotCC =array();
	$grdTotMo =array();
	$grdTotEft =array();
	
	foreach($delFacilityArr as $facId => $facName){
		if(sizeof($delPaymentDetArr[$facId])<=0){ break; }

		$delcsvFileData.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr>	
				<td align="left" class="text_b_w">Facility : $facName</td>
			</tr>
		</table>
DATA;
		$delpdfData2.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr>	
				<td align="left" class="text_b_w" style="width:755px;">Facility : $facName</td>
			</tr>
		</table>
DATA;
		foreach($delDoctorArr[$facId] as $docId => $docName){

			if(sizeof($delPaymentDetArr[$facId][$docId])<=0){ break; }

			$docTotPayAmtArr = array();
			$docTotAppAmtArr = array();
			$docTotBalAmtArr = array();			
			$delcsvFileData.= <<<DATA
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
				<tr>	
					<td align="left" colspan="9" class="text_b_w">Physician : $docName</td>
				</tr>
				<tr class="text_b_w">	
					<td align="center" style="width:250px">Patient Name - ID</td>
					<td align="center" style="width:150px">Payment Date</td>
					<td align="center" style="width:150px">Payment</td>
					<td align="center" style="width:150px">Applied</td>
					<td align="center" style="width:150px">Balance</td>
					<td align="center" style="width:100px">Deleted Date</td>
					<td align="center" style="width:100px">Deleted Time</td>
					<td align="center" style="width:100px">Deleted By</td>
					<td align="center" style="width:auto"></td>
				</tr>
DATA;
			$delpdfData2.= <<<DATA
			<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
				<tr>	
					<td align="left" colspan="8" class="text_b_w">Physician : $docName</td>
				</tr>
				<tr class="text_b_w">	
					<td align="center" class="text_b_w" style="width:175px">Patient Name - ID</td>
					<td align="center" class="text_b_w" style="width:85px">Payment Date</td>
					<td align="center" class="text_b_w" style="width:70px">Payment</td>
					<td align="center" class="text_b_w" style="width:70px">Applied</td>
					<td align="center" class="text_b_w" style="width:70px">Balance</td>
					<td align="center" class="text_b_w" style="width:60px">Del. Date</td>
					<td align="center" class="text_b_w" style="width:60px">Del. Time</td>
					<td align="center" class="text_b_w" style="width:110px">Deleted By</td>
				</tr>
DATA;

			$i=0;
			foreach($delPaymentDetArr[$facId][$docId] as $patID => $patDetails){
				
				//echo "<pre>"; print_r($patDet);
				foreach($patDetails as $paymentID => $patDet){
					$patName =$patDet['pat_name'];
					$payDate =$patDet['pay_date'];
					$paymentID = $patDet['payment_id'];
					$totPatPayment = $CLSReports->numberFormat($delTotPatientAmtArr[$paymentID]['total_payment'],2);
					$totPatPayment = ($totPatPayment=='')? $showCurrencySymbol."00.00": $totPatPayment;
					$totAppliedAmt = $CLSReports->numberFormat($delTotAppliedDetArr[$paymentID],2);				
					$totAppliedAmt = ($totAppliedAmt=='')? $showCurrencySymbol."00.00": $totAppliedAmt;
					$totBalAmt = $CLSReports->numberFormat($delTotPatientAmtArr[$paymentID]['total_payment'] - $delTotAppliedDetArr[$paymentID],2);
					$totBalAmt = ($totBalAmt=='')? $showCurrencySymbol."00.00": $totBalAmt;

					$delcsvFileData.= <<<DATA
						<tr style="height:20px;">	
							<td align="left" style="width:250px" class="text11b" bgcolor="#FFFFFF">$patName</td>
							<td align="left" style="width:150px" class="text11b" bgcolor="#FFFFFF">$payDate</td>
							<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF">$totPatPayment</td>
							<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF">$totAppliedAmt</td>
							<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF">$totBalAmt</td>
							<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" style="width:auto" class="text11b" bgcolor="#FFFFFF"></td>
						</tr>
DATA;
					$delpdfData2.= <<<DATA
						<tr style="height:20px;">	
							<td align="left" class="text11b" bgcolor="#FFFFFF">$patName</td>
							<td align="left" class="text11b" bgcolor="#FFFFFF">$payDate</td>
							<td align="right" class="text11b" bgcolor="#FFFFFF">$totPatPayment</td>
							<td align="right" class="text11b" bgcolor="#FFFFFF">$totAppliedAmt</td>
							<td align="right" class="text11b" bgcolor="#FFFFFF">$totBalAmt</td>
							<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
							<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
						</tr>
DATA;
	
					$chkin = $chkout =0;
	
	
					foreach($delPatientDet[$patID][$paymentID] as $pId => $payDetail){
						$paymentType = strtoupper($payDetail['payment_type']);
						$paymentMethod = strtoupper($payDetail['payment_method']);
						$deletedDate = ($payDetail['deleteDateDet']!='00-00-0000') ? $payDetail['deleteDateDet'] : '';
						$deletedTime = ($payDetail['deleteTimeDet']!='00:00:00') ? getMainAmPmTime($payDetail['deleteTimeDet']) : '';
						$deletedBy = $userNameArr[$payDetail['delete_operator_id_det']];
						
						$payId = $payDetail['payment_id'];
						$paymentDetId = $payDetail['id'];
						$payTypeName = $allINOutFields[$payDetail['item_id']];
						$pdf_payTypeName = $payTypeName;
						if(strlen($pdf_payTypeName)>18){
							$pdf_payTypeName=substr($pdf_payTypeName, 0, 16).'..';
						}
						$payAmt = $CLSReports->numberFormat($payDetail['item_payment'],2);
						$payAmt = ($payAmt=='')? $showCurrencySymbol."00.00": $payAmt;
						
						$totItemApply = array_sum($delAppliedDetArr[$payId][$paymentDetId]['paidForProc']);
						$totalItemApply = $CLSReports->numberFormat($totItemApply,2);
						$totalItemApply = ($totalItemApply=='')? $showCurrencySymbol."00.00": $totalItemApply;
						$totBal =$payDetail['item_payment'] - $totItemApply;
						if($totItemApply < $payDetail['item_payment']) {
							$totalItemBal = $CLSReports->numberFormat($totBal,2);
						}else{
							$totalItemBal = $showCurrencySymbol."00.00";
						}
						
						// SET CASH, CHECK , CC, MONEY ORDER, EFT ARAYS FOR GRAND TOTAL
						if($paymentMethod =='CASH'){
							$grdTotCash[] =  $payDetail['item_payment']; 						
						}
						if($paymentMethod =='CHECK'){
							$grdTotCheck[] =  $payDetail['item_payment']; 						
						}
						if($paymentMethod =='CREDIT CARD'){
							$grdTotCC[] =  $payDetail['item_payment']; 						
						}
						if($paymentMethod =='MONEY ORDER'){
							$grdTotMo[] =  $payDetail['item_payment']; 						
						}
						if($paymentMethod =='EFT'){
							$grdTotEft[] =  $payDetail['item_payment']; 						
						}
	
						// Sub Total Array 
						$subTotPayAmtArr[$payId][] = $payDetail['item_payment'];
						$subTotBalAmtArr[$payId][] = $totBal;
										
						if(($paymentType=='CHECKIN' && $chkin==0) || ($paymentType=='CHECKOUT' && $chkout==0)){
							$delcsvFileData.= <<<DATA
								<tr>	
									<td align="right" class="text11b" bgcolor="#FFFFFF">$paymentType</td>
									<td align="left" colspan="8" class="text" bgcolor="#FFFFFF"></td>
								</tr>
DATA;
							$delpdfData2.= <<<DATA
								<tr>	
									<td align="right" class="text_12" bgcolor="#FFFFFF">$paymentType</td>
									<td align="left" colspan="7" class="text_12" bgcolor="#FFFFFF"></td>
								</tr>
DATA;
							if($paymentType=='CHECKIN'){ $chkin=1;}
							if($paymentType=='CHECKOUT'){ $chkout=1;}
							}
						
						$delcsvFileData.= <<<DATA
							<tr>	
								<td align="left" class="text" bgcolor="#FFFFFF"></td>
								<td align="left" class="text" bgcolor="#FFFFFF">$payTypeName</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$payAmt</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$totalItemApply</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$totalItemBal</td>
								<td align="center" class="text" bgcolor="#FFFFFF">$deletedDate</td>
								<td align="center" class="text" bgcolor="#FFFFFF">$deletedTime</td>
								<td align="center" class="text" bgcolor="#FFFFFF">$deletedBy</td>
								<td align="left" class="text" bgcolor="#FFFFFF"></td>
							</tr>
DATA;
						$delpdfData2.= <<<DATA
							<tr>	
								<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
								<td align="left" class="text_12" bgcolor="#FFFFFF">$pdf_payTypeName</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$payAmt</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$totalItemApply</td>
								<td align="right" class="text_12" bgcolor="#FFFFFF">$totalItemBal</td>
								<td align="center" class="text_12" bgcolor="#FFFFFF">$deletedDate</td>
								<td align="center" class="text_12" bgcolor="#FFFFFF">$deletedTime</td>
								<td align="center" class="text_12" bgcolor="#FFFFFF">$deletedBy</td>
							</tr>
DATA;
	
	
						$appliedSize = sizeof($delAppliedDetArr[$payId][$paymentDetId]['cpt4_code']);
						if($appliedSize>0){
							
							for($j=0; $j<$appliedSize; $j++){
								$item_bal= '';
								$cpt4_code 	= $delAppliedDetArr[$payId][$paymentDetId]['cpt4_code'][$j];
								$appAmt = $delAppliedDetArr[$payId][$paymentDetId]['paidForProc'][$j];
								$appliedAmt = $CLSReports->numberFormat($appAmt,2);
								
								//SUB TOTAL ARRAY
								$subTotAppAmtArr[$payId][] = $appAmt;
								
								$delcsvFileData.= <<<DATA
									<tr>	
										<td align="right" colspan="2" class="text" bgcolor="#FFFFFF">$cpt4_code</td>
										<td align="right" class="text" bgcolor="#FFFFFF"></td>
										<td align="right" class="text" bgcolor="#FFFFFF">$appliedAmt</td>
										<td align="left" class="text" colspan="5" bgcolor="#FFFFFF"></td>
									</tr>
DATA;
								$delpdfData2.= <<<DATA
									<tr>	
										<td align="right" colspan="2" class="text_12" bgcolor="#FFFFFF">$cpt4_code</td>
										<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
										<td align="right" class="text_12" bgcolor="#FFFFFF">$appliedAmt</td>
										<td align="left" class="text_12" colspan="4" bgcolor="#FFFFFF"></td>
									</tr>
DATA;
	
							}
						}
					}
	
					// DISPLAY SUB TOTAL FOR PATIENT
					$TotPayAmt = array_sum($subTotPayAmtArr[$payId]);
					$TotAppAmt = array_sum($subTotAppAmtArr[$payId]);
					$TotBalAmt = array_sum($subTotBalAmtArr[$payId]);
					
					//ARRAY FOR DOCTOR TOTALS
					$docTotPayAmtArr[$docId][] = $TotPayAmt;
					$docTotAppAmtArr[$docId][] = $TotAppAmt;
					$docTotBalAmtArr[$docId][] = $TotBalAmt;
					
				$i++;
				}
			}
			// DOCTOR TOTALS 
			$dTotPayAmt = array_sum($docTotPayAmtArr[$docId]);
			$dTotAppAmt = array_sum($docTotAppAmtArr[$docId]);
			$dTotBalAmt = array_sum($docTotBalAmtArr[$docId]);
			
			$docTotPayAmt = $CLSReports->numberFormat($dTotPayAmt,2);
			$docTotAppAmt = $CLSReports->numberFormat($dTotAppAmt,2);
			$docTotBalAmt = $CLSReports->numberFormat($dTotBalAmt,2);

			// GRAND TOTALS ARRAY
			$grdTotPayAmtArr[] = $dTotPayAmt;
			$grdTotAppAmtArr[] = $dTotAppAmt;
			$grdTotBalAmtArr[] = $dTotBalAmt;

			$delcsvFileData.= <<<DATA
				<tr><td class="total-row" colspan="9"></td></tr>
				<tr style="height:20px">	
					<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">Sub Total&nbsp;</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotPayAmt</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotAppAmt</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotBalAmt</td>
					<td align="left" class="text11b" bgcolor="#FFFFFF" colspan="4"></td>
				</tr>
				<tr><td class="total-row" colspan="9"></td></tr>
				</table>
DATA;
			$delpdfData2.= <<<DATA
				<tr><td class="total-row" colspan="8"></td></tr>
				<tr style="height:20px">	
					<td align="right" class="text_b" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">Sub Total&nbsp;</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotPayAmt</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotAppAmt</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotBalAmt</td>
					<td bgcolor="#FFFFFF" colspan="3"></td>
				</tr>
				<tr><td class="total-row" colspan="8"></td></tr>
				</table>
DATA;

		} // END DOCTOR
	}
	// GRAND TOTALS
	$grdTotPayAmt = array_sum($grdTotPayAmtArr);
	$grdAmt = $grdTotPayAmt;
	$grdTotPayAmt = $CLSReports->numberFormat($grdTotPayAmt,2);
	$grdTotAppAmt = $CLSReports->numberFormat(array_sum($grdTotAppAmtArr),2);
	$grdTotBalAmt = $CLSReports->numberFormat(array_sum($grdTotBalAmtArr),2);
	
	$grdCash = array_sum($grdTotCash);	
	$grdTotCashAmt = $CLSReports->numberFormat($grdCash,2);
	$grdTotCashAmt =($grdTotCashAmt=='')? $showCurrencySymbol."00.00" : $grdTotCashAmt;		
	$grdCheck = array_sum($grdTotCheck);	
	$grdTotCheckAmt = $CLSReports->numberFormat($grdCheck,2);
	$grdTotCheckAmt =($grdTotCheckAmt=='')? $showCurrencySymbol."00.00" : $grdTotCheckAmt;	
	$grdCC = array_sum($grdTotCC);		
	$grdTotCCAmt = $CLSReports->numberFormat($grdCC,2);
	$grdTotCCAmt =($grdTotCCAmt=='')? $showCurrencySymbol."00.00" : $grdTotCCAmt;		
	$grdMo = array_sum($grdTotMo);	
	$grdTotMoAmt = $CLSReports->numberFormat($grdMo,2);
	$grdTotMoAmt =($grdTotMoAmt=='')? $showCurrencySymbol."00.00" : $grdTotMoAmt;	
	$grdEft = array_sum($grdTotEft);	
	$grdTotEftAmt = $CLSReports->numberFormat($grdEft,2);
	$grdTotEftAmt =($grdTotEftAmt=='')? $showCurrencySymbol."00.00" : $grdTotEftAmt;	
	
	
	$grdTotAmtType = $grdCash + $grdCheck + $grdCC + $grdMo + $grdEft; 
	$grdTotAmtType = $CLSReports->numberFormat($grdTotAmtType,2);
	$grdTotAmtType =($grdTotAmtType=='')? $showCurrencySymbol."00.00" : $grdTotAmtType;		

	$acctAmt = 0;
	$grdTotCopayAmt = array_sum($grdTotCopay);
	$acctAmt = $grdTotCopayAmt;
	$grdTotCopayAmt = $CLSReports->numberFormat($grdTotCopayAmt,2);
	$grdTotCopayAmt =($grdTotCopayAmt=='')? $showCurrencySymbol."00.00" : $grdTotCopayAmt;		

	$grdTotOpticalAmt = array_sum($grdTotOptical);
	$acctAmt+= $grdTotOpticalAmt;
	$grdTotOpticalAmt = $CLSReports->numberFormat($grdTotOpticalAmt,2);
	$grdTotOpticalAmt =($grdTotOpticalAmt=='')? $showCurrencySymbol."00.00" : $grdTotOpticalAmt;		
	
	$grdProc = $grdAmt - $acctAmt;
	$grdTotProcAmt = $CLSReports->numberFormat($grdProc,2);
	$grdTotProcAmt =($grdTotProcAmt=='')? $showCurrencySymbol."00.00" : $grdTotProcAmt;		
	
	$grdMethod = $acctAmt + $grdProc;
	$grdTotAmtMethod = $CLSReports->numberFormat($grdMethod,2);
	$grdTotAmtMethod =($grdTotAmtMethod=='')? $showCurrencySymbol."00.00" : $grdTotAmtMethod;		

	$delcsvFileData.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr>	
			<td align="left" colspan="9" class="text10" ></td>
		</tr>
		<tr><td class="total-row" colspan="9"></td></tr>
		<tr style="height:20px">	
			<td align="right" class="text11b" style="width:140px" bgcolor="#FFFFFF"></td>
			<td align="right" class="text11b" style="width:233px" bgcolor="#FFFFFF">(Deleted Records) Grand Total&nbsp;</td>
			<td align="right" class="text11b" style="width:140px" bgcolor="#FFFFFF">$grdTotPayAmt</td>
			<td align="right" class="text11b" style="width:140px" bgcolor="#FFFFFF">$grdTotAppAmt</td>
			<td align="right" class="text11b" style="width:140px" bgcolor="#FFFFFF">$grdTotBalAmt</td>
			<td align="left" class="text11b" bgcolor="#FFFFFF" colspan="4"></td>
		</tr>
		<tr><td class="total-row" colspan="9"></td></tr>
	</table>
DATA;

	$csvFileData.='
	<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
	<tr style="height:20px">	
		<td align="left" colspan="9" class="text10" bgcolor="#FFFFFF" ></td>
	</tr></table>'.
	$fileHeaderData.$delcsvFileData;
	
	$delpdfData2.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr>	
			<td align="left" colspan="8" class="text10" ></td>
		</tr>
		<tr><td class="total-row" colspan="8"></td></tr>
		<tr style="height:20px">
			<td align="right" style="width:75px;" class="text_b" bgcolor="#FFFFFF"></td>	
			<td align="right" style="width:175px;" class="text_b" bgcolor="#FFFFFF">(Deleted Records) Grand Total&nbsp;</td>
			<td align="right" style="width:70px;" class="text_b" bgcolor="#FFFFFF">$grdTotPayAmt</td>
			<td align="right" style="width:75px;" class="text_b" bgcolor="#FFFFFF">$grdTotAppAmt</td>
			<td align="right" style="width:75px;" class="text_b" bgcolor="#FFFFFF">$grdTotBalAmt</td>
			<td style="width:65px;" class="text_b" bgcolor="#FFFFFF">&nbsp;</td>
			<td style="width:65px;" class="text_b" bgcolor="#FFFFFF">&nbsp;</td>
			<td style="width:110px;" class="text_b" bgcolor="#FFFFFF">&nbsp;</td>
			
		</tr>
		<tr><td class="total-row" colspan="8"></td></tr>
	</table>
DATA;

		$pdfData.= <<<DATA
			<page backtop="19mm" backbottom="10mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$fileHeaderData2
			</page_header>
			$delpdfData2
			</page>
DATA;
} // END DELETED CI/CO

}
?>