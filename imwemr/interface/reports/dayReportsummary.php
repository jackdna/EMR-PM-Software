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
FILE : DAYREPORTSUMMERY.PHP
PURPOSE :  DAILY REPORT SUMMERY RESULT
ACCESS TYPE : INCLUDED
*/

//require_once(dirname(__FILE__).'/../common/functions.inc.php');

if(trim($_REQUEST['Submit']) != ''){
	list($m,$d,$y) = preg_split('/-/',$sel_date);

	$curDate = date('m-d-Y H:i A');
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = $op_name_arr[1][0];
	$op_name .= $op_name_arr[0][0];
		
	$sel_pro_id_arr = preg_split('/,/',$sel_pro);

	//---- SUMMARY END OF DAY REPORT -------
	$totalSchApp = 0;
	$totalToDoApp = 0;
	$totalNoShowApp = 0;
	$totalCheckApp = 0;
	$totalCancelApp = 0;
	$totalCharges = 0;
	$paidForProcArr = array();
	$arrPtIndex = array();
	$patEncDone = array();
	
	//--- GET PHYSICIAN NAME ----
	$phyName = $CLSReports->report_display_selected($sel_pro,'physician','4');
	//--- START LOOP FOR DISPLAY DATA ----
	for($sm=0;$sm<count($sel_pro_id_arr);$sm++){
		$sel_pro_id = $sel_pro_id_arr[$sm];
		//---- GET SELECTED DATE APPOINTMENT STATUS -----------
		$reportRes = $appointmentDataArr[$sel_pro_id];		
		$totalSchApp += count($reportRes);
		for($sp=0;$sp<count($reportRes);$sp++){
			$intThisFacilityId = $reportRes[$sp]['sa_facility_id'];
			$sa_patient_app_status_id = $reportRes[$sp]['sa_patient_app_status_id'];
			$sa_id = $reportRes[$sp]['id'];			
			switch($sa_patient_app_status_id){
				case '201':
					$totalToDoApp++;
				break;
				case '3':
					$totalNoShowApp++;
				break;
				case '18':
					$totalCancelApp++;
				break;
				default:
					$totalCheckApp++;
				break;
			}
			$sa_patient_id = $reportRes[$sp]['sa_patient_id'];

			if($sa_patient_app_status_id != 3 && $sa_patient_app_status_id != 18){

				//$apptKey = array_search($sa_id, $arrSchSn[$sa_patient_id]);
				//$encounter_id = $arrEncountersSn[$sa_patient_id][$apptKey];
				reset($arrEncSchId[$sa_id]);
				$encounter_id = current($arrEncSchId[$sa_id]);

/*				$arrEncs = array_values($arrEncounters[$sa_patient_id]);
				if(sizeof($arrPtIndex[$sa_patient_id])>0) { $arrPtIndex[$sa_patient_id] = $arrPtIndex[$sa_patient_id]+1; }
				else{ $arrPtIndex[$sa_patient_id] = 0;  }
				$pIndex = $arrPtIndex[$sa_patient_id];
				if($encounter_id>0){
					$patEncDone[$sa_patient_id][$encounter_id]=$encounter_id;
				}				
*/
				//--- POSTED AMOUNTS -----
				if(count($patientChargesDataArr[$sa_patient_id]['posted_amount'])>0){
					$totalCharges += $patientChargesDataArr[$sa_patient_id]['posted_amount'][$encounter_id];
				}
				//--- NOT POSTED AMOUNT ----
				if(count($patientChargesDataArr[$sa_patient_id]['not_posted_amount'])>0){
					$totalCharges += $patientChargesDataArr[$sa_patient_id]['not_posted_amount'][$encounter_id];
				}

				//--- GET TOTAL PAYMENTS -----				
				$encounter_id_arr = $patientChargesDataArr[$sa_patient_id]['encounter_id'][$encounter_id];
				$pay_amt_arr = array();
				for($s=0;$s<count($encounter_id_arr);$s++){
					//$encounter_id = $encounter_id_arr[$s];
					$paymentArr = $paymentDataArr[$encounter_id];
					$paidForProcArr[] = array_sum($paymentArr['cash']);
					$paidForProcArr[] = array_sum($paymentArr['check']);
					$paidForProcArr[] = array_sum($paymentArr['credit']);
					$paidForProcArr[] = array_sum($paymentArr['money order']);
					$paidForProcArr[] = array_sum($paymentArr['veep']);
					$paidForProcArr[] = array_sum($paymentArr['eft']);
				}

				if(sizeof($arrEncSchId)>1){	
					$arrEncs = array_keys($arrEncSchId[$sa_id]);				
					for($en=1; $en<sizeof($arrEncs); $en++){
						$encounter_id = $arrEncs[$en];

						//--- POSTED AMOUNTS -----
						if(count($patientChargesDataArr[$sa_patient_id]['posted_amount'])>0){
							$totalCharges += $patientChargesDataArr[$sa_patient_id]['posted_amount'][$encounter_id];
						}
						//--- NOT POSTED AMOUNT ----
						if(count($patientChargesDataArr[$sa_patient_id]['not_posted_amount'])>0){
							$totalCharges += $patientChargesDataArr[$sa_patient_id]['not_posted_amount'][$encounter_id];
						}
						
						$paymentArr = $paymentDataArr[$encounter_id];
						$paidForProcArr[] = array_sum($paymentArr['cash']);
						$paidForProcArr[] = array_sum($paymentArr['check']);
						$paidForProcArr[] = array_sum($paymentArr['credit']);
						$paidForProcArr[] = array_sum($paymentArr['money order']);
						$paidForProcArr[] = array_sum($paymentArr['veep']);
						$paidForProcArr[] = array_sum($paymentArr['eft']);
					}
				}
			}
		}		
	}
	
	$paidForProcArr[] = $arrDepTots['PAT_DEPOSIT'];
	$paidForProcArr[] = $arrCIOTots['CIO_AMT'];
	
	if($totalSchApp>0){
		if($blExcludePageTags == false){
			$strHTML .= <<<DATA
				<page backtop="10mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table width="550" border="0" cellpadding="1" cellspacing="0">
						<tr>
							<td class="text_b_w" width="200" align="left">
								Day Sheet report / $reportProcess
							</td>
							<td class="text_b_w" width="100" align="center">
								$sel_date
							</td>
						</tr>
						<tr>
							<td class="text_b_w" style="text-align:left;" colspan="2">Physician : $phyName</td>
						</tr>
						<tr>
							<td class="text_b_w" style="text-align:left;" colspan="2">Facility : $facility</td>
						</tr>						
					</table>
				</page_header>
DATA;
			$arr=array();
			$arr[]='Day Sheet report / '.$reportProcess;
			$arr[]='Physician : '.$phyName;
			$arr[]=$sel_date;
			$arr[]='Created by '.$op_name.' on '.$curDate;
			fputcsv($fp,$arr, ",","\"");	
			//--- CSV FILE DATA
			$csv_data .= <<<DATA
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt_table rpt rpt_table-bordered">
					<tr>
						<td class="text_b_w" width="250" align="left">
							Day Sheet report / $reportProcess
						</td>
						<td class="text_b_w" width="auto" align="left">
							Physician : $phyName
						</td>
						<td class="text_b_w" width="150" align="center">
							$sel_date
						</td>
						<td class="text_b_w" width="280" style="text-align:right">
							Created by $op_name on $curDate
						</td>						
					</tr>						
				</table>
DATA;
		}
		
		if($blExcludePageTags == true){
			$strHTML .= <<<DATA
				<table width="100%" cellspacing="1" style="width:100%"class="rpt_table rpt rpt_table-bordered">
					<tr id="">
						<td style="width:40%" align="left" class="text_b_w">
							Day Sheet report / Summary
						</td>
						<td align="left"style="width:30%" class="text_b_w">
							$phyName
						</td>
						<td style="width:30%" align="center" class="text_b_w">
							$sel_date
						</td>
					</tr>
				</table>
DATA;
			$arr=array();
			$arr[]='Day Sheet report / Summary';
			$arr[]= $phyName;
			$arr[]=$sel_date;
			fputcsv($fp,$arr, ",","\"");

			//--- CSV FILE DATA
			$csv_data .= <<<DATA
				<table width="100%" cellspacing="1" style="width:100%" class="rpt_table rpt rpt_table-bordered">
					<tr id="">
						<td width="350" align="left" class="text_b_w">
							Day Sheet report / Summary
						</td>
						<td align="left" width="auto" class="text_b_w">
							$phyName
						</td>
						<td width="150" align="center" class="text_b_w">
							$sel_date
						</td>
					</tr>
				</table>
DATA;
		}
		$conditionChk = true;
		
		//---  CHANGE NUMBER FORMAT ----
		$totalCharges = numberformat($totalCharges,2);
		$paidForProc = NULL;
		if(count($paidForProcArr)>0){
			$paidForProc = array_sum($paidForProcArr);
		}
		$totalPostedPayment  =$paidForProc;
		$paidForProc = numberformat($paidForProc,2);
		
		$strHTML .= <<<DATA
			<table align="left" class="rpt_table rpt rpt_table-bordered" style="width:100%" cellspacing="2">
				<tr>
					<td class="text_10b"  align="left" style="width:30%"> Total # of Patient scheduled</td>
					<td class="text_10b"  align="left" style="width:70%">$totalSchApp</td>
				</tr>
				<tr>
					<td class="text_10b" align="left">Total # of Patient Re-scheduled</td>
					<td class="text_10b" align="left">$totalToDoApp</td>
				</tr>
				<tr>
					<td class="text_10b" align="left">Total # of Patient No Show</td>
					<td class="text_10b" align="left">$totalNoShowApp</td>
				</tr>
				<tr>
					<td class="text_10b" align="left">Total # of Patient Checked</td>
					<td class="text_10b" align="left">$totalCheckApp</td>
				</tr>
				<tr>
					<td class="text_10b" align="left">Total # of Patient Canceled</td>
					<td class="text_10b" align="left">$totalCancelApp</td>
				</tr>
				<tr>
					<td class="text_10b" align="left">Total Charges</td>
					<td class="text_10b" align="left">$totalCharges</td>
				</tr>
				<tr>
					<td class="text_10b" align="left">Total Payments</td>
					<td class="text_10b" align="left">$paidForProc</td>
				</tr>
			</table>
DATA;

			$arr=array();
			$arr[]='Total # of Patient scheduled';
			$arr[]=$totalSchApp;
			fputcsv($fp,$arr, ",","\"");
			$arr=array();
			$arr[]='Total # of Patient Re-scheduled';
			$arr[]=$totalToDoApp;
			fputcsv($fp,$arr, ",","\"");
			$arr=array();
			$arr[]='Total # of Patient No Show';
			$arr[]=$totalNoShowApp;
			fputcsv($fp,$arr, ",","\"");
			$arr=array();
			$arr[]='Total # of Patient Checked';
			$arr[]=$totalCheckApp;
			fputcsv($fp,$arr, ",","\"");
			$arr=array();
			$arr[]='Total # of Patient Canceled';
			$arr[]=$totalCancelApp;
			fputcsv($fp,$arr, ",","\"");
			$arr=array();
			$arr[]='Total Charges';
			$arr[]=$totalCharges;
			fputcsv($fp,$arr, ",","\"");
			$arr=array();
			$arr[]='Total Payments';
			$arr[]=$paidForProc;
			fputcsv($fp,$arr, ",","\"");	

			//--- CSV FILE DATA
			$csv_data .=<<<DATA
				<table width="100%" class="rpt_table rpt rpt_table-bordered" style="width:100%" cellspacing="1">
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" width="250" align="left">Total # of Patient scheduled</td>
						<td class="text_10b" width="auto" align="left">$totalSchApp</td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" align="left">Total # of Patient Re-scheduled</td>
						<td class="text_10b" align="left">$totalToDoApp</td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" align="left">Total # of Patient No Show</td>
						<td class="text_10b" align="left">$totalNoShowApp</td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" align="left">Total # of Patient Checked</td>
						<td class="text_10b" align="left">$totalCheckApp</td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" align="left">Total # of Patient Canceled</td>
						<td class="text_10b" align="left">$totalCancelApp</td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" align="left">Total Charges</td>
						<td class="text_10b" align="left">$totalCharges</td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" align="left">Total Payments</td>
						<td class="text_10b" align="left">$paidForProc</td>
					</tr>
				</table>
DATA;

		if($blExcludePageTags == false){
			$strHTML .= '</page>';
		}
	}
	
	//CI/CO AND PRE PAYMENT TOTAL BLOCK
	$totalCollectAmt = $arrCIOTots['CIO_AMT'] + $arrDepTots['PAT_DEPOSIT'];
	if($totalPostedPayment>0){
		$totalPostedPayment = $totalPostedPayment - $totalCollectAmt;
	}
	$totalAppliedAmt = $arrCIOTots['APPLIED_AMT'] + $arrDepTots['APPLIED_AMT'];
	$grandCollected = $arrCIOTots['CIO_AMT'] + $arrDepTots['PAT_DEPOSIT'] + $totalPostedPayment;
	$grandApplied = $arrCIOTots['APPLIED_AMT'] + $arrDepTots['APPLIED_AMT'] + $totalPostedPayment;
	
	if($arrDepTots['PAT_DEPOSIT_REF']>0)
	{
		$redRow=';color:#FF0000" title="Refund '.numberformat($arrDepTots['PAT_DEPOSIT_REF'],2);
	}else $redRow='';
	
	if($arrCIOTots['CIO_AMT_REF']>0)
	{
		$redRowCC=';color:#FF0000" title="Refund '.numberformat($arrCIOTots['CIO_AMT_REF'],2);
	}else $redRowCC='';
	
	$csv_data.='<br>
		<table cellspacing="1" class="rpt_table rpt rpt_table-bordered" style="width:100%;">
		<tr id="heading_orange"><td colspan="4">CI/CO and Pre Payments of '.$sel_date.'</td></tr>
		<tr id=""><td class="text_b_w" style="text-align:left;"></td>
			<td style="text-align:center;" class="text_b_w">Total Collected</td>
			<td style="text-align:center;" class="text_b_w">Applied for Selected Date</td>
		</tr>		
		<tr>
			<td class="text_10b white" style="text-align:right; width:252px;">CI/CO Payments&nbsp;</td>
			<td class="text_10 white" style="text-align:right; width:252px'.$redRowCC.'">'.numberformat($arrCIOTots['CIO_AMT'],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right; width:252px;">'.numberformat($arrCIOTots['APPLIED_AMT'],2).'&nbsp;</td>
		</tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Pre Payments&nbsp;</td>
			<td class="text_10 white" style="text-align:right'.$redRow.'">'.numberformat($arrDepTots['PAT_DEPOSIT'],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right">'.numberformat($arrDepTots['APPLIED_AMT'],2).'&nbsp;</td>
		</tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Total &nbsp;:</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($totalCollectAmt,2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($totalAppliedAmt,2).'&nbsp;</td>
		</tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Total Posted Payments&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($totalPostedPayment,2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($totalPostedPayment,2).'&nbsp;</td>
		</tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Grand Total &nbsp;:</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($grandCollected,2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($grandApplied,2).'&nbsp;</td>
		</tr>
		</table>';
	

	$strHTML.='<br>
		<table width="100%" cellspacing="2" class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr id="heading_orange" style="height:10px"><td colspan="4">CI/CO and Pre Payments of '.$sel_date.'</td></tr>
		<tr id=""><td style="text-align:left;width:25%;" class="text_b_w"></td>
			<td style="text-align:center;width:25%;" class="text_b_w">Total Collected</td>
			<td style="text-align:center;width:25%;" class="text_b_w">Applied for Selected Date</td>
			<td style="text-align:center;width:25%;" class="text_b_w"></td>
		</tr>		
		<tr>
			<td class="text_10b white" style="text-align:right; width:25%;">CI/CO Payments&nbsp;</td>
			<td class="text_10 white" style="text-align:right; width:25%'.$redRowCC.'">'.numberformat($arrCIOTots['CIO_AMT'],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right; width:25%;">'.numberformat($arrCIOTots['APPLIED_AMT'],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right; width:25%;"></td>
		</tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Pre Payments&nbsp;</td>
			<td class="text_10 white" style="text-align:right'.$redRow.'">'.numberformat($arrDepTots['PAT_DEPOSIT'],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right">'.numberformat($arrDepTots['APPLIED_AMT'],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right;"></td>
		</tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Total &nbsp;:</td>
			<td class="text_10b white" style="text-align:right">'.numberformat($totalCollectAmt,2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($totalAppliedAmt,2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right;"></td>
		</tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Total Posted Payments&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($totalPostedPayment,2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($totalPostedPayment,2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right;"></td>
		</tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Grand Total &nbsp;:</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($grandCollected,2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.numberformat($grandApplied,2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right;"></td>
		</tr>		</table>';
	
		
		
		$arr=array();
		$arr[]='CI/CO and Pre Payments of '.$sel_date;
		fputcsv($fp,$arr, ",","\"");	
		$arr=array();
		$arr[]='';
		$arr[]='Total Collected';
		$arr[]='Applied for Selected Date';
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]='';
		$arr[]='CI/CO Payments';
		$arr[]=numberformat($arrCIOTots['CIO_AMT'],2);
		$arr[]=numberformat($arrCIOTots['APPLIED_AMT'],2);
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]='';
		$arr[]='Pre Payments';
		$arr[]=numberformat($arrDepTots['PAT_DEPOSIT'],2);
		$arr[]=numberformat($arrDepTots['APPLIED_AMT'],2);
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]='';
		$arr[]='Total';
		$arr[]=numberformat($totalCollectAmt,2);
		$arr[]=numberformat($totalAppliedAmt,2);
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]='';
		$arr[]='Total Posted Payments';
		$arr[]=numberformat($totalPostedPayment,2);
		$arr[]=numberformat($totalPostedPayment,2);
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]='';
		$arr[]='Grand Total';
		$arr[]=numberformat($grandCollected,2);
		$arr[]=numberformat($grandApplied,2);
		fputcsv($fp,$arr, ",","\"");
}
	
	if($showSummary){	
		if($reportProcess != 'summary'){		
				$csv_data.='<br>
				<table width="100%" class="rpt_table rpt rpt_table-bordered" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8">
					<tr><td class="white"></td>
						<td style="width:5px; height:5px;" bgcolor="#2A2AFF"></td>
						<td class="white" style="padding-left:20px; color:#2A2AFF">Color Represents CI/CO payments.</td>
					</tr>
					<tr><td class="white" style="width:20px"></td>
						<td style="width:5px; height:5px; background-color:#FF7F55" bgcolor="#FF7F55"></td>
						<td class="white" style="padding-left:20px; color:#FF7F55">Color Represents pre-payment amount.</td>
					</tr>
					<tr><td class="white"></td>
						<td colspan="2"><strong>*</strong> Represents combination of payments with CI/CO or pre payments or both payments.</td>
					</tr>
				</table>';	
				
					
			}
		if($reportProcess != 'summary'){
				$strHTML.='<br>
				<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" class="rpt_table rpt rpt_table-bordered" >
					<tr><td class="info"></td>
						<td style="width:2px;" height="5px;" bgcolor="#2A2AFF"></td>
						<td class="info" style="padding-left:20px; color:#2A2AFF">Color represents CI/CO payments.</td>
					</tr>
					<tr><td class="info" style="width:20px"></td>
						<td style="width:2px; height:5px; background-color:#FF7F55" bgcolor="#FF7F55"></td>
						<td class="info" style="padding-left:20px; color:#FF7F55">Color represents pre-payment amount.</td>
					</tr>
					<tr><td class="info"></td>
						<td colspan="2" class="info" ><strong>*</strong> Represents combination of payments with CI/CO or pre payments or combination both payments.</td>
					</tr>
				</table>';		
		}
	}
?>