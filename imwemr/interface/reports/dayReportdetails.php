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
FILE : DAYREPORTDETAILS.PHP
PURPOSE :  DAY REPORT RESULT REPORT
ACCESS TYPE : DIRECT
*/
//require_once(dirname(__FILE__).'/../common/functions.inc.php');
$showCurrencySymbol=showcurrency();
$multi = 0;
$curDate = date(phpDateFormat().' H:i A');
$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$op_name = $op_name_arr[1][0];
$op_name .= $op_name_arr[0][0];

//--- START LOOP FOR DISPLAY DATA -----
$sel_groups = "All";
$arrGrpSel=array();
if(empty($grp_id)==false){ $arrGrpSel = explode(',', $grp_id);}
$sel_groups = (sizeof($arrGrpSel)>1)? (sizeof($arrGroups)==sizeof($arrGrpSel))? 'All' : 'Multi' : $arrGroups[$grp_id]; 

$sel_pros = "All";
if(count($sel_pro)>0){
	if(count($sel_pro)>1){
		$sel_pros = "Multi";
	}else{
		$sel_pros = implode(", ",$sel_pro);
	}
}
$sel_facs = "All";
if(count($facilityId)>0){
	if(count($facilityId)>1){
		$sel_facs = "Multi";
	}else{
		$sel_facs = implode(", ",$facilityId);
	}
}
$csv_data = '<table class="rpt_table rpt rpt_table-bordered rpt_padding">					
				<tr>
					<td class="rptbx1" style="width:33%">Day Sheet Report</td>
					
					<td class="rptbx2" style="text-align:left; width:33%">Selected DOS : '.$sel_date.'</td>
					<td class="rptbx3" style="text-align:left; width:33%">Created by '.$op_name.' on '.$curDate.'</td>
				</tr>
				<tr>
					<td class="rptbx1" style="width:33%">Selected Group : '.$sel_grp.'</td>
					<td class="rptbx2" style="text-align:left; width:33%">Selected Facility: '.$sel_fac.'</td>
					<td class="rptbx3" style="text-align:left; width:33%">Selected Physician : '.$sel_phy.'</td>
				</tr>
			</table>';
			
if($newpage == "No"){
	$strHTML .= <<<DATA
		<page backtop="5mm" backbottom="10mm">			
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
DATA;
	$strHTML .= '<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
			<tr class="rpt_headers">
				<td class="rptbx1" align="left" width="350">Day Sheet Report</td>
				<td class="rptbx2" align="left" width="350">Selected DOS : '.$sel_date.'</td>
				<td class="rptbx3" align="left" width="350">Created by '.$op_name.' on '.$curDate.'</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1" align="left">Selected Group : '.$sel_grp.'</td>
				<td class="rptbx2" align="left">Selected Facility: '.$sel_fac.'</td>
				<td class="rptbx3" align="left">Selected Physician : '.$sel_phy.'</td>
			</tr>
			</table>';
}

$file_name="day_sheet.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');
$arr=array();
$arr[]='Day Sheet Report';
$arr[]='Selected DOS :'.$sel_date;
$arr[]='Created by :'.$op_name.' on '.$curDate;
fputcsv($fp,$arr, ",","\"");
$arr=array();
$arr[]='Selected Group :'.$sel_grp;
$arr[]='Selected Facility :'.$sel_fac;
$arr[]='Selected Physician :'.$sel_phy;
fputcsv($fp,$arr, ",","\"");

$arr=array();
$arr[]='Physician';
$arr[]='Facility';
$arr[]='Appt Time';
$arr[]='CheckIn Time';
$arr[]='CheckOut Time';
$arr[]='Patient Name - ID';
$arr[]='Procedure';
$arr[]='SuperBill';
$arr[]='Charges: Not Posted';
$arr[]='Charges: Posted';
$arr[]='Payments: Cash';
$arr[]='Payments: Check';
$arr[]='Payments: CC';
$arr[]='Payments: MO/VEEP';
$arr[]='Payments: EFT';
fputcsv($fp,$arr, ",","\"");

for($p=0;$p<count($sel_pro_arr);$p++){	
	$sel_pro = $sel_pro_arr[$p];
	$totalSchApp = 0;
	$totalNoShowApp = 0;
	$totalCheckApp = 0;
	$totalCancelApp = 0;
	$patientIdArr = array();
	$paidForProc = 0;
	$totalBalance = 0;
	$totalToDoApp = 0;
	
	//---- GET APPOINTMENT STATUS -----------
	$reportRes = $appointmentDataArr[$sel_pro];	
	//-- GET PHYSICIAN NAME ---	
	$phyName = ucwords(trim($physicianNameArr[$sel_pro]));
	//--- GET FACILITY NAME ----
	$facName = ucwords(trim($reportRes[0]['name']));
	if($facName==""){
		$facName="No";
	}
	$totalSchApp = count($reportRes);
	$reportData1 = NULL;
	$reportDataHeader = NULL;
	$reportData2 = NULL;
	$intThisFacilityId = NULL;
	$intPrevFacilityId = NULL;
	$tempR = 0;
	
	if($newpage == "Yes"){
		$reportDataHeader .= <<<DATA
			<tr>
				<td width="380" class="text_b" align="left" >
					Physician: $phyName
				</td>
				<td width="250" class="text_b" align="left" >
					Facility: $facName
				</td>
			</tr>
		</table>
		<table cellpadding="3" cellspacing="1"  class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr id="">
				<td width="20" class="text_b_w" align="center">#</td>
				<td width="60" class="text_b_w" align="center">Appt Time</td>
				<td width="60" class="text_b_w" align="center">CheckIn</td>
				<td width="60" class="text_b_w" align="center">CheckOut</td>
				<td align="left" class="text_b_w" width="165">Patient Name - ID</td>
				<td width="130" class="text_b_w" align="left">Procedure</td>
				<td width="70" class="text_b_w" align="center">SuperBill</td>
				<td style="text-align:center" class="text_b_w"  colspan="2">Charges</td>
				<td style="text-align:center" class="text_b_w" colspan="5">Payments</td>
			</tr>
			<tr id="">
				<td valign="top" class="text_b_w" align="center" colspan="2"></td>
				<td width="60" class="text_b_w" valign="top"  align="center">Time</td>
				<td width="60" class="text_b_w" valign="top"  align="center">Time</td>
				<td valign="top" class="text_b_w" align="center"></td>
				<td valign="top" class="text_b_w" align="center" colspan="2"></td>
				<td valign="top" class="text_b_w" align="center" width="60">Not Posted</td>
				<td valign="top" class="text_b_w" align="center" width="60">Posted</td>
				<td valign="top" class="text_b_w" align="center" width="50">Cash</td>
				<td valign="top" class="text_b_w" align="center" width="50">Check</td>
				<td valign="top" class="text_b_w" align="center" width="50">CC</td>
				<td valign="top" class="text_b_w" align="center" width="50">MO/VEEP</td>
				<td valign="top" class="text_b_w" align="center" width="50">EFT</td>
			</tr>
DATA;

	}
	
	//--- RESET VARIABLE FOR TOTAL AMOUNT ----
	$totalSuperBillCharges = 0;
	$totalNotPostedChares = 0;
	$totalPostedChares = 0;
	$totalCashAmount = 0;
	$totalCheckAmount = 0;
	$totalCcAmount = 0;
	$spCnt = 0;
	for($r=0;$r<count($reportRes);$r++,$tempR++){
		$sa_patient_app_status_id = $reportRes[$r]['sa_patient_app_status_id'];
		switch($sa_patient_app_status_id){
			case '201':				
			case '3':
			case '18':
			case '17':
			break;
			default:
				$spCnt++;
			break;
		}
	}
	
	$csv_rep2 = '';
	for($r=0,$okCnt=0;$r<count($reportRes);$r++,$tempR++){		
		$intThisFacilityId = $reportRes[$r]['sa_facility_id'];		
		$checkInTime = '';
		$sa_app_starttime = substr($reportRes[$r]['sa_app_starttime'],0,-3);
		$sa_app_endtime = substr($reportRes[$r]['sa_app_endtime'],0,-3);
		$patNameArr = array();
		$patNameArr["LAST_NAME"] = $reportRes[$r]['lname'];
		$patNameArr["FIRST_NAME"] = $reportRes[$r]['fname'];
		$patNameArr["MIDDLE_NAME"] = $reportRes[$r]['mname'];
		$patName = changeNameFormat($patNameArr);
		
		$sa_doctor_id = $reportRes[$r]['sa_doctor_id'];
		$sa_patient_id = $reportRes[$r]['sa_patient_id'];
		$patientIdArr[] = $reportRes[$r]['sa_patient_id'];
		$proc = $reportRes[$r]['proc'];
		$sa_id = $reportRes[$r]['id'];
	
		//--- GET CHECK IN / OUT TIME ------
		$timeRes = $schDataArr[$sa_id];
		$checkOutTime = '';
		for($ch=0;$ch<count($timeRes);$ch++){
			list($h,$m,$s) = preg_split('/:/',$timeRes[$ch]['status_time']);
			$st = 'AM';
			if($h>12){
				$h = $h -12;
				$st = 'PM';
			}
			if($h == 12) $st = 'PM';
			if($timeRes[$ch]['status'] == 11){				
				$checkOutTime = $h.':'.$m.' '.$st;
			}
			else if($timeRes[$ch]['status'] == 13){
				$checkInTime = $h.':'.$m.' '.$st;
			}
			else if($timeRes[$ch]['status'] == 6){
				$checkOutTime = 'LV';
			}
		}

		$sa_patient_app_status_id = $reportRes[$r]['sa_patient_app_status_id'];
		
		$arrs[] = $sa_patient_app_status_id;
		$totalToDoApp++;
		$check_in_out_st = false;
		switch($sa_patient_app_status_id){
			case '201':				
				$checkInTime = 'Re-Scheduled';
			break;
			case '3':
				$checkInTime = 'No Show';
			break;
			case '18':
				$checkInTime = 'Canceled';
			break;
			case '17':
				$checkInTime = 'Confirmed';
			break;
			default:
				$check_in_out_st = true;
			break;
		}
		
		$superbillRes = "";
		$notPostedAmt = "";
		$postedAmt = "";
		$cashAmt = "";
		$CheckAmt = "";
		$CCAmt = "";
		$moAmt = "";
		$eftAmt = "";
		$superBillCharges = "";
		
		if($sa_patient_app_status_id != 3 && $sa_patient_app_status_id != 18){
			$superbillRes = 0;
			$notPostedAmt = 0;
			$postedAmt = 0;
			$cashAmt = 0;
			$CheckAmt = 0;
			$CCAmt = 0;
			$moAmt = 0;
			$veepAmt = 0;
			$eftAmt = 0;
			
			//$apptKey = array_search($sa_id, $arrSchSn[$sa_patient_id]);
			//$encounter_id = $arrEncountersSn[$sa_patient_id][$apptKey];
			reset($arrEncSchId[$sa_id]);
			$encounter_id = current($arrEncSchId[$sa_id]);

//			$arrEncs = array_values($arrEncountersSn[$sa_patient_id]);
//			if(sizeof($arrPtIndex[$sa_patient_id])>0) { $arrPtIndex[$sa_patient_id] = $arrPtIndex[$sa_patient_id]+1; }
//			else{ $arrPtIndex[$sa_patient_id] = 0;  }
//			$pIndex = $arrPtIndex[$sa_patient_id];
//			if($encounter_id>0){
//				$patEncDone[$sa_patient_id][$encounter_id]=$encounter_id;
//			}

			//--- GET SUPER BILL CHARGES -------
					
			$superBillCharges = 'N/SB';
			if(count($superbillDataArr[$sa_patient_id][$encounter_id])>0){
				$superbillRes = $superbillDataArr[$sa_patient_id][$encounter_id];
				$superBillCharges = $showCurrencySymbol.number_format($superbillRes,2);
				unset($superbillDataArr[$sa_patient_id][$encounter_id]);
			}

			// && count($arrEncounters[$sa_patient_id])<=$arrPtIndex[$sa_patient_id]+1
		/*	if(count($superbillDataArr[$sa_patient_id])>0 && (sizeof($arrEncs) == $arrPtIndex[$sa_patient_id]+1)){
				if($superBillCharges=='N/SB'){
					$superbillRes = array_sum($superbillDataArr[$sa_patient_id]);
					$superBillCharges = '$'.number_format($superbillRes,2);
					unset($superbillDataArr[$sa_patient_id]);
				}
			}*/

			
			//--- GET ALL POSTED CHARGES WITH IN DATE RANGE ------

			//--- POSTED AMOUNT -----
			if($patientChargesDataArr[$sa_patient_id]['posted_amount'][$encounter_id]){
				$postedAmt = $patientChargesDataArr[$sa_patient_id]['posted_amount'][$encounter_id];
			}
			//--- NOT POSTED AMOUNT ----
			if($patientChargesDataArr[$sa_patient_id]['not_posted_amount'][$encounter_id]){
				$notPostedAmt = $patientChargesDataArr[$sa_patient_id]['not_posted_amount'][$encounter_id];
			}

			$totalNotPostedChares += $notPostedAmt;
			$notPostedAmt = numberformat($notPostedAmt,2);
			$totalPostedChares += $postedAmt;
			$postedAmt = numberformat($postedAmt,2);
					
			//---- GET TOTAL PAYMENT DETAILS ----------			
			$pay_amt_arr = array();
			$paymentArr = $paymentDataArr[$encounter_id];
			$pay_amt_arr['cashAmt'][] = array_sum($paymentArr['cash']);
			$pay_amt_arr['CheckAmt'][] = array_sum($paymentArr['check']);
			$pay_amt_arr['CCAmt'][] = array_sum($paymentArr['credit']);
			$pay_amt_arr['moAmt'][] = array_sum($paymentArr['money order']);
			$pay_amt_arr['veepAmt'][] = array_sum($paymentArr['veep']);
			$pay_amt_arr['eftAmt'][] = array_sum($paymentArr['eft']);
			
			// SET PAYMENT COLORS
			$amtColorCash=$amtColorCheck=$amtColorCC=$amtColorMO=$amtColorEFT='#000';
			$cashStar=$checkStar=$ccStar=$moStar=$eftStar='';			
			
			$cashAmt = NULL;
			if(count($pay_amt_arr['cashAmt'])>0){
				$cashAmt = array_sum($pay_amt_arr['cashAmt']);
			}			
			
			$CheckAmt = NULL;
			if(count($pay_amt_arr['CheckAmt'])>0){
				$CheckAmt = array_sum($pay_amt_arr['CheckAmt']);
			}

			$CCAmt = NULL;
			if(count($pay_amt_arr['CCAmt'])>0){
				$CCAmt = array_sum($pay_amt_arr['CCAmt']);
			}
			
			$moAmt = NULL;
			if(count($pay_amt_arr['moAmt'])>0){
				$moAmt = array_sum($pay_amt_arr['moAmt']);
			}

			$veepAmt = NULL;
			if(count($pay_amt_arr['veepAmt'])>0){
				$veepAmt = array_sum($pay_amt_arr['veepAmt']);
			}
			
			$eftAmt = NULL;
			if(count($pay_amt_arr['eftAmt'])>0){
				$eftAmt = array_sum($pay_amt_arr['eftAmt']);
			}

			//ADDING CI/CO AND PRE-PAYMENT COLLECTIONS
			//cash
			if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash']>0){
				if($cashAmt>0){ $cashStar='*'; }
				$amtColorCash='#2A2AFF';
				$cashAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash'];
				unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash']);
			}
			if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash']>0){
				if($cashAmt>0){ $cashStar='*'; }
				$amtColorCash='#FF7F55';
				$cashAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash'];
				unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash']);
			}
			//check
			if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check']>0){
				if($CheckAmt>0){ $checkStar='*'; }
				$amtColorCheck='#2A2AFF';
				$CheckAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check'];
				unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check']);
			}
			if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check']>0){
				if($CheckAmt>0){ $checkStar='*'; }
				$amtColorCheck='#FF7F55';
				$CheckAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check'];
				unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check']);
			}
			//CC
			if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card']>0){
				if($CCAmt>0){ $ccStar='*'; }
				$amtColorCC='#2A2AFF';
				$CCAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card'];
				unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card']);
			}
			if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card']>0){
				if($CCAmt>0){ $ccStar='*'; }
				$amtColorCC='#FF7F55';
				$CCAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card'];
				unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card']);
			}
			//MO
			if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order']>0){
				if($moAmt>0){ $moStar='*'; }
				$amtColorMO='#2A2AFF';
				$moAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order'];
				unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order']);
			}
			if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order']>0){
				if($moAmt>0){ $moStar='*'; }
				$amtColorMO='#FF7F55';
				$moAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order'];
				unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order']);
			}

			//VEEP
			if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep']>0){
				if($veepAmt>0){ $veepStar='*'; }
				$amtColorMO='#2A2AFF';
				$veepAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep'];
				unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep']);
			}
			if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep']>0){
				if($veepAmt>0){ $veepStar='*'; }
				$amtColorMO='#FF7F55';
				$veepAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep'];
				unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep']);
			}

			//EFT
			if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft']>0){
				if($eftAmt>0){ $eftStar='*'; }
				$amtColorEFT='#2A2AFF';
				$eftAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft'];
				unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft']);
			}
			if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft']>0){
				if($eftAmt>0){ $eftStar='*'; }
				$amtColorEFT='#FF7F55';
				$eftAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft'];
				unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft']);
			}

			$totalCashAmount += $cashAmt;
			$totalCheckAmount += $CheckAmt;
			$totalCcAmount += $CCAmt;
			$totalMoAmount += $moAmt;
			$totalVeepAmount += $veepAmt;
			$totalEftAmount += $eftAmt;

			$cashAmt = numberformat($cashAmt,2);
			$CheckAmt = numberformat($CheckAmt,2);
			$CCAmt = numberformat($CCAmt,2);
			$moAmt = numberformat($moAmt,2);
			$veepAmt = numberformat($veepAmt,2);
			$eftAmt = numberformat($eftAmt,2);
		}
		
		list($hs,$ms,$ss) = preg_split('/:/',$sa_app_starttime);
		$sts = 'AM';
		if($hs>12){
			$hs = $hs -12;
			$sts = 'PM';
		}
		if($hs == 12) $sts = 'PM';
		
		$hs = (strlen($hs) == 1) ? "0".$hs : $hs;
		$ms = (strlen($ms) == 1) ? "0".$ms : $ms;
		
		$sa_app_starttime_tr = $hs.':'.$ms.' '.$sts;
		$patName = $patName.' - '.$sa_patient_id;

		if($r > 0 && ($intThisFacilityId != $intPrevFacilityId)){
			$tempR = 0;
			$fc_name = $reportRes[$r]['name'];
			
			$reportData1 .= <<<DATA
				<tr>
					<td class="text_b_w" align="left" colspan="4">
						Physician: $phyName
					</td>
					<td class="text_b_w" align="left" colspan="10">
						Facility: $fc_name
					</td>
				</tr>
				<tr>
					<td class="text_b_w" align="center" style="width:2%">#</td>
					<td class="text_b_w" align="center" style="width:5%">Appt Time</td>
					<td class="text_b_w" align="center" style="width:7%">CheckIn</td>
					<td class="text_b_w" align="center" style="width:7%">CheckOut</td>
					<td class="text_b_w" align="left"  style="width:15%; word-wrap:break-word;">Patient Name - ID</td>
					<td class="text_b_w" align="left" style="width:8%; word-wrap:break-word;">Procedure</td>
					<td class="text_b_w" align="center" style="width:7%">SuperBill</td>
					<td class="text_b_w" style="text-align:center; width:14%" colspan="2">Charges</td>
					<td class="text_b_w" style="text-align:center;width:35%" colspan="5">Payments</td>
				</tr>
				<tr>
					<td class="text_b_w" valign="top"  align="center" colspan="2" style="width:7%"></td>
					<td class="text_b_w" valign="top"  align="center" style="width:7%">Time</td>
					<td class="text_b_w" valign="top"  align="center" style="width:7%">Time</td>
					<td class="text_b_w" valign="top"  align="center" style="width:15%"></td>
					<td class="text_b_w" valign="top"  align="center" colspan="2" style="width:15%"></td>
					<td class="text_b_w" valign="top"  align="center"  style="width:7%">Not Posted</td>
					<td class="text_b_w" valign="top"  align="center"  style="width:7%">Posted</td>
					<td class="text_b_w" valign="top"  align="center"  style="width:7%">Cash</td>
					<td class="text_b_w" valign="top"  align="center"  style="width:7%">Check</td>
					<td class="text_b_w" valign="top"  align="center"  style="width:7%">CC</td>
					<td class="text_b_w" valign="top"  align="center"  style="width:7%">MO/VEEP</td>
					<td class="text_b_w" valign="top"  align="center"  style="width:7%">EFT</td>
				</tr>
DATA;

			//--- GET CSV DATA ----
			$csv_rep .= <<<DATA
				<tr>
					<td class="text_b_w" align="left" colspan="3">
						Physician: $phyName
					</td>
					<td class="text_b_w" align="left" colspan="11">
						Facility: $fc_name
					</td>
				</tr>
				<tr>
					<td class="text_b_w" width="20"  align="center">#</td>
					<td class="text_b_w" width="60"  align="center">Appt Time</td>
					<td class="text_b_w" width="60"  align="center">CheckIn</td>
					<td class="text_b_w" width="60"  align="center">CheckOut</td>
					<td class="text_b_w"  align="left" width="165">Patient Name - ID</td>
					<td class="text_b_w" width="150"  align="left">Procedure</td>
					<td class="text_b_w" width="90"  align="center">SuperBill</td>
					<td class="text_b_w"  style="text-align:center" colspan="2">Charges</td>
					<td class="text_b_w"  style="text-align:center" colspan="5">Payments</td>
				</tr>
				<tr>
					<td class="text_b_w valignTop" align="center" colspan="2"></td>
					<td class="text_b_w valignTop" width="60"  align="center">Time</td>
					<td class="text_b_w valignTop" width="60"  align="center">Time</td>
					<td class="text_b_w valignTop" align="center"></td>
					<td class="text_b_w valignTop" align="center" colspan="2"></td>
					<td class="text_b_w valignTop" align="center" width="65">Not Posted</td>
					<td class="text_b_w valignTop" align="center" width="60">Posted</td>
					<td class="text_b_w valignTop" align="center" width="60">Cash</td>
					<td class="text_b_w valignTop" align="center" width="60">Check</td>
					<td class="text_b_w valignTop" align="center" width="60">CC</td>
					<td class="text_b_w valignTop" align="center" width="60">MO/VEEP</td>
					<td class="text_b_w valignTop" align="center" width="60">EFT</td>
				</tr>
DATA;
		}

		$tempR1 = $tempR+1;

		if($check_in_out_st == true){
			$totalSuperBillCharges += $superbillRes;
			$okCnt++;
			$moOrVeep=$br='';

			if($moAmt!='0' && $moAmt!='')$moOrVeep='MO '.$moStar.$moAmt;$br='<br>';
			if($veepAmt!='0' && $veepAmt!=''){$moOrVeep.=$br.'VEEP '.$veepStar.$veepAmt;}

			$reportData1 .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td class="valignTop text_10" align="center">$okCnt</td>
					<td class="valignTop text_10" align="center">$sa_app_starttime_tr</td>
					<td class="valignTop text_10" align="center">$checkInTime</td>
					<td class="valignTop text_10" align="center">$checkOutTime</td>
					<td class="valignTop text_10" align="left" style="width:15%; word-wrap:break-word;">$patName</td>
					<td class="valignTop text_10" align="left" style="width:7%; word-wrap:break-word;" >$proc</td>
					<td class="valignTop text_10" style="text-align:right">$superBillCharges</td>
					<td class="valignTop text_10" style="text-align:right" >$notPostedAmt</td>
					<td class="valignTop text_10" style="text-align:right" >$postedAmt</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorCash">$cashStar$cashAmt</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorCheck">$checkStar$CheckAmt</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorCC">$ccStar$CCAmt</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorMO">$moOrVeep</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorEFT">$eftStar$eftAmt</td>
				</tr>
DATA;
			//---- CSV DATA ---
			$csv_rep .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td class="valignTop text_10" width="20" align="center">$okCnt</td>
					<td class="valignTop text_10" width="60" align="center">$sa_app_starttime_tr</td>
					<td class="valignTop text_10" width="70" align="center">$checkInTime</td>
					<td class="valignTop text_10" width="70" align="center">$checkOutTime</td>
					<td class="valignTop text_10" width="165" align="left" >$patName</td>
					<td class="valignTop text_10" width="130" align="left" >$proc</td>
					<td class="valignTop text_10" width="80" align="center">$superBillCharges</td>
					<td class="valignTop text_10" width="50" style="text-align:right">$notPostedAmt</td>
					<td class="valignTop text_10" width="50" style="text-align:right">$postedAmt</td>
					<td class="valignTop text_10" width="50" style="text-align:right; color:$amtColorCash">$cashStar$cashAmt</td>
					<td class="valignTop text_10" width="50" style="text-align:right; color:$amtColorCheck">$checkStar$CheckAmt</td>
					<td class="valignTop text_10" width="50" style="text-align:right; color:$amtColorCC">$ccStar$CCAmt</td>
					<td class="valignTop text_10" width="50" style="text-align:right; color:$amtColorMO">$moOrVeep</td>
					<td class="valignTop text_10" width="50" style="text-align:right; color:$amtColorEFT">$eftStar$eftAmt</td>
				</tr>
DATA;
			
			$arr=array();
			$arr[]=$phyName;
			$arr[]=$fc_name;
			$arr[]=$sa_app_starttime_tr;
			$arr[]=$checkInTime;
			$arr[]=$checkOutTime;
			$arr[]=$patName;
			$arr[]=$proc;
			$arr[]=$superBillCharges;
			$arr[]=$notPostedAmt;
			$arr[]=$postedAmt;
			$arr[]=$cashStar.''.$cashAmt;
			$arr[]=$checkStar.''.$CheckAmt;
			$arr[]=$ccStar.''.$CCAmt;
			$arr[]=$moOrVeep;
			$arr[]=$eftStar.''.$eftAmt;
			fputcsv($fp,$arr, ",","\"");		

			//if((sizeof($arrEncounters[$sa_patient_id]) > sizeof($arrApptPats[$sa_patient_id])) && (sizeof($arrApptPats[$sa_patient_id]) == $arrPtIndex[$sa_patient_id]+1)){	
			
			if(sizeof($arrEncSchId[$sa_id])>1){
				$arrEncs = array_keys($arrEncSchId[$sa_id]);
				$childRows=makeChildRows($sa_patient_id, $sa_id, $arrEncs, $reportData1, $csv_rep, $superbillDataArr, $patientChargesDataArr, $paymentDataArr, $patEncDone[$sa_patient_id], $cioPatientPay, $prePayPatients);
				$cRows=explode('~', $childRows);
				$reportData1.=$cRows[0];
				$csv_rep.=$cRows[1];
				$totalSuperBillCharges+=$cRows[2];
				$totalNotPostedChares+=$cRows[3];
				$totalPostedChares+=$cRows[4];
				$totalCashAmount+=$cRows[5];
				$totalCheckAmount+=$cRows[6];
				$totalCcAmount+=$cRows[7];
				$totalMoAmount+=$cRows[8];
				$totalEftAmount+=$cRows[9];
				$totalVeepAmount+=$cRows[10];
			}
		}
		else{
			$spCnt++;
			$moOrVeep=$br='';
			
			if($moAmt>0)$moOrVeep='MO '.$moStar.$moAmt;$br='<br>';
			if($veepAmt>0){$moOrVeep.=$br.'VEEP '.$veepStar.$veepAmt;}

			$reportData2 .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td class="valignTop text_10" align="center">$spCnt</td>
					<td class="valignTop text_10" align="center">$sa_app_starttime_tr</td>
					<td class="valignTop text_10" align="center">$checkInTime</td>
					<td class="valignTop text_10" align="center">$checkOutTime</td>
					<td class="valignTop text_10" align="left" style="width:15%; word-wrap:break-word;">$patName</td>
					<td class="valignTop text_10" align="left" style="width:7%; word-wrap:break-word;">$proc</td>
					<td class="valignTop text_10" style="text-align:right">$superBillCharges</td>
					<td class="valignTop text_10" style="text-align:right" >$notPostedAmt</td>
					<td class="valignTop text_10" style="text-align:right" >$postedAmt</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorCash" >$cashStar$cashAmt</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorCheck" >$checkStar$CheckAmt</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorCC" >$ccStar$CCAmt</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorMO" >$moOrVeep</td>
					<td class="valignTop text_10" style="text-align:right; color:$amtColorEFT" >$eftStar$eftAmt</td>
				</tr>
DATA;
			//---- CSV DATA ---
			$csv_rep2 .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td class="valignTop text_10" width="20" style="text-align:left">$spCnt</td>
					<td class="valignTop text_10" width="60" style="text-align:left">$sa_app_starttime_tr</td>
					<td class="valignTop text_10" width="70" style="text-align:left">$checkInTime</td>
					<td class="valignTop text_10" width="70" style="text-align:left">$checkOutTime</td>
					<td class="valignTop text_10" width="165" style="text-align:left">$patName</td>
					<td class="valignTop text_10" width="130" style="text-align:left">$proc</td>
					<td class="valignTop text_10" width="90" style="text-align:right">$superBillCharges</td>
					<td class="valignTop text_10" width="60" style="text-align:right">$notPostedAmt</td>
					<td class="valignTop text_10" width="60" style="text-align:right">$postedAmt</td>
					<td class="valignTop text_10" width="60" style="text-align:right">$cashStar$cashAmt</td>
					<td class="valignTop text_10" width="60" style="text-align:right; color:$amtColorCash">$checkStar$CheckAmt</td>
					<td class="valignTop text_10" width="60" style="text-align:right; color:$amtColorCC">$ccStar$CCAmt</td>
					<td class="valignTop text_10" width="60" style="text-align:right; color:$amtColorMO">$moOrVeep</td>
					<td class="valignTop text_10" width="60" style="text-align:right; color:$amtColorEFT">$eftStar$eftAmt</td>
				</tr>
DATA;
			$arr=array();
			$arr[]=$phyName;
			$arr[]=$fc_name;
			$arr[]=$sa_app_starttime_tr;
			$arr[]=$checkInTime;
			$arr[]=$checkOutTime;
			$arr[]=$patName;
			$arr[]=$proc;
			$arr[]=$superBillCharges;
			$arr[]=$notPostedAmt;
			$arr[]=$postedAmt;
			$arr[]=$cashStar.''.$cashAmt;
			$arr[]=$checkStar.''.$CheckAmt;
			$arr[]=$ccStar.''.$CCAmt;
			$arr[]=$moOrVeep;
			$arr[]=$eftStar.''.$eftAmt;
			fputcsv($fp,$arr, ",","\"");	
			//if((sizeof($arrEncounters[$sa_patient_id]) > sizeof($arrApptPats[$sa_patient_id])) && (sizeof($arrApptPats[$sa_patient_id]) == $arrPtIndex[$sa_patient_id]+1)){	
			if(sizeof($arrEncSchId[$sa_id])>1){			
				$arrEncs = array_keys($arrEncSchId[$sa_id]);
				$childRows=makeChildRows($sa_patient_id, $sa_id, $arrEncs, $reportData1, $csv_rep, $superbillDataArr, $patientChargesDataArr, $paymentDataArr, $patEncDone[$sa_patient_id], $cioPatientPay, $prePayPatients);
				$cRows=explode('~', $childRows);
				$reportData2.=$cRows[0];
				$csv_rep2.=$cRows[1];
				$totalSuperBillCharges+=$cRows[2];
				$totalNotPostedChares+=$cRows[3];
				$totalPostedChares+=$cRows[4];
				$totalCashAmount+=$cRows[5];
				$totalCheckAmount+=$cRows[6];
				$totalCcAmount+=$cRows[7];
				$totalMoAmount+=$cRows[8];
				$totalEftAmount+=$cRows[9];
				$totalVeepAmount+=$cRows[10];
			}
		}
	
		$intPrevFacilityId = $intThisFacilityId;
		
		if($r == (count($reportRes)-1)){
			$totalMoVeep=$totalMoAmount+$totalVeepAmount;
			
			//--- CHANGE NUMBER FORMAT ----
			$totalSuperBillCharges = numberformat($totalSuperBillCharges,2);
			$totalNotPostedChares = numberformat($totalNotPostedChares,2);
			$totalPostedChares = numberformat($totalPostedChares,2);
			$totalCashAmount = numberformat($totalCashAmount,2);
			$totalCheckAmount = numberformat($totalCheckAmount,2);
			$totalCcAmount = numberformat($totalCcAmount,2);
			$totalMoVeep = numberformat($totalMoVeep,2);
			$totalEftAmount = numberformat($totalEftAmount,2);
			
			$reportData1 .= <<<DATA
				$reportData2
				<tr>
					<td style="height:2px; padding: 0px; background: #009933;" colspan="14"></td>
				</tr>
				<tr bgcolor="#FFFFFF" valign="top">
					<td class="text_10b" style="text-align:right;" colspan="6">Total : </td>
					<td class="text_10b" style="text-align:right;" >$totalSuperBillCharges</td>
					<td class="text_10b" style="text-align:right;" >$totalNotPostedChares</td>
					<td class="text_10b" style="text-align:right;" >$totalPostedChares</td>
					<td class="text_10b" style="text-align:right;" >$totalCashAmount</td>
					<td class="text_10b" style="text-align:right;" >$totalCheckAmount</td>
					<td class="text_10b" style="text-align:right;" >$totalCcAmount</td>
					<td class="text_10b" style="text-align:right;" >$totalMoVeep</td>
					<td class="text_10b" style="text-align:right;" >$totalEftAmount</td>
				</tr>
				<tr>
					<td style="height:2px; padding: 0px; background: #009933;" colspan="14"></td>
				</tr>
DATA;
			
			//--- CSV DATA ----
			$csv_rep .= <<<DATA
				$csv_rep2
				<tr>
					<td style="height:2px; padding: 0px; background: #009933;" colspan="14"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td class="text_10b" valign="top" style="text-align:right;" colspan="6">Total : </td>
					<td class="text_10b" valign="top" style="text-align:right;">$totalSuperBillCharges</td>
					<td class="text_10b" valign="top" style="text-align:right;">$totalNotPostedChares</td>
					<td class="text_10b" valign="top" style="text-align:right;">$totalPostedChares</td>
					<td class="text_10b" valign="top" style="text-align:right;">$totalCashAmount</td>
					<td class="text_10b" valign="top" style="text-align:right;">$totalCheckAmount</td>
					<td class="text_10b" valign="top" style="text-align:right;">$totalCcAmount</td>
					<td class="text_10b" valign="top" style="text-align:right;">$totalMoVeep</td>
					<td class="text_10b" valign="top" style="text-align:right;">$totalEftAmount</td>
				</tr>
				<tr>
					<td style="height:2px; padding: 0px; background: #009933;" colspan="14"></td>
				</tr>
DATA;
		}
	}
	
	if(count($reportRes)>0){
		$multi++;
		$totalBalance = numberformat($totalBalance,2); 
		$paidForProc = numberformat($paidForProc,2);

		if($newpage == "Yes"){
			$strHTML .= <<<DATA
			<page backtop="5mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
DATA;
		$strHTML .= '<table class="rpt_table rpt rpt_table-bordered" width="100%" border="0" cellpadding="0" cellspacing="0">					
			<tr>
				<td class="text_b_w" align="center" style="text-align:center" width="1065">Group Name : '.$sel_groups.'</td>
			</tr>
			</table>';
		}
	
		if($newpage == "No"){
			$strHTML .= <<<DATA
				<table class="rpt_table rpt rpt_table-bordered" style="width:100%">					
					<tr id="">
						<td align="left" colspan="5" class="text_b_w" style="width:36%">Physician: $phyName</td>
						<td align="left" colspan="9" class="text_b_w" style="width:64%">Facility: $facName</td>
					</tr>
					<tr id="">
						<td class="text_b_w" align="center" style="width:2%">#</td>
						<td class="text_b_w" align="center" style="width:5%">Appt Time</td>
						<td class="text_b_w" align="center" style="width:7%">CheckIn</td>
						<td class="text_b_w" align="center" style="width:7%">CheckOut</td>
						<td class="text_b_w" align="left"  style="width:15%">Patient Name - ID</td>
						<td class="text_b_w" align="left" style="width:8%">Procedure</td>
						<td class="text_b_w" align="center" style="width:7%">SuperBill</td>
						<td class="text_b_w" style="text-align:center; width:14%" colspan="2">Charges</td>
						<td class="text_b_w" style="text-align:center; width:35%" colspan="5">Payments</td>
					</tr>
					<tr id="">
						<td valign="top" class="text_b_w" align="center" colspan="2" style="width:7%"></td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">Time</td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">Time</td>
						<td valign="top" class="text_b_w" align="center" style="width:15%"></td>
						<td valign="top" class="text_b_w" align="center" colspan="2" style="width:15%"></td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">Not Posted</td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">Posted</td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">Cash</td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">Check</td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">CC</td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">MO/VEEP</td>
						<td valign="top" class="text_b_w" align="center" style="width:7%">EFT</td>
					</tr>
DATA;
		}
		
		$conditionChk = true;
		
		//--- PDF DATA ---
		$strHTML .= <<<DATA
				$reportDataHeader
				$reportData1
			</table>
DATA;

		//--- CSV DATA ----
		$csv_data.= <<<DATA
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%"> 					
				<tr id="">
					<td width="150" align="left" class="text_b_w" colspan="2">Physician: $phyName</td>
					<td width="814" align="left" class="text_b_w">Facility: $facName</td>
				</tr>
			</table>
			<table class="rpt_table rpt rpt_table-bordered" cellspacing="1" style="width:100%">
				<tr id="">
					<td width="20" class="text_b_w" align="center">#</td>
					<td width="60" class="text_b_w" align="center">Appt Time</td>
					<td width="65" class="text_b_w" align="center">CheckIn</td>
					<td width="65" class="text_b_w" align="center">CheckOut</td>
					<td width="190" class="text_b_w" align="left">Patient Name - ID</td>
					<td width="140" class="text_b_w" align="left">Procedure</td>
					<td width="90" class="text_b_w" align="center">SuperBill</td>
					<td style="text-align:center" class="text_b_w" colspan="2">Charges</td>
					<td style="text-align:center" class="text_b_w" colspan="5">Payments</td>
				</tr>
				<tr id="">
					<td valign="top" class="text_b_w" align="center" ></td>
					<td valign="top" class="text_b_w" align="center" ></td>
					<td valign="top" class="text_b_w" align="center">Time</td>
					<td valign="top" class="text_b_w" align="center">Time</td>
					<td valign="top" class="text_b_w" align="center" ></td>
					<td valign="top" class="text_b_w" align="center" ></td>
					<td valign="top" class="text_b_w" align="center" ></td>
					<td valign="top" class="text_b_w" align="center" width="65">Not Posted</td>
					<td valign="top" class="text_b_w" align="center" width="60">Posted</td>
					<td valign="top" class="text_b_w" align="center" width="60">Cash</td>
					<td valign="top" class="text_b_w" align="center" width="60">Check</td>
					<td valign="top" class="text_b_w" align="center" width="60">CC</td>
					<td valign="top" class="text_b_w" align="center" width="60">MO/VEEP</td>
					<td valign="top" class="text_b_w" align="center" width="60">EFT</td>
				</tr>
				$reportData1
			</table>
DATA;
		
		if($newpage == "Yes"){
			$strHTML .= '</page>';
		}

		if($printsummary == "Yes"){	//to show summary of one physician only
			$blExcludePageTags = true;
			if(count($appointmentDataArr) == 1)
			$showSummary = true;
			require(dirname(__FILE__).'/dayReportsummary.php');
		}
	}	
}
if($newpage == "No"){
	$strHTML .= '</page>';
}

//if($multi > 1){	//to show summary of all physicians (in case of more than one physicians worked)
$sel_pro = join(',',$sel_pro_arr);
$blExcludePageTags = true;
$showSummary = true;
require(dirname(__FILE__).'/dayReportsummary.php');
//}

function makeChildRows($sa_patient_id, $sa_id, $arrEncs, $reportData1, $csv_rep, $superbillDataArr, $patientChargesDataArr, $paymentDataArr, $patEncDone, $cioPatientPay, $prePayPatients){

	for($en=1; $en<sizeof($arrEncs); $en++){
	$superbillRes = 0;
	$notPostedAmt = 0;
	$postedAmt = 0;
	$cashAmt = 0;
	$CheckAmt = 0;
	$CCAmt = 0;
	$moAmt = 0;
	$veepAmt = 0;
	$eftAmt = 0;
	$encounter_id = $arrEncs[$en];


		//--- GET SUPER BILL CHARGES -------
		$superBillCharges = 'N/SB';
		if(count($superbillDataArr[$sa_patient_id][$encounter_id])>0){
			$superbillRes = $superbillDataArr[$sa_patient_id][$encounter_id];
			$superBillCharges = $showCurrencySymbol.number_format($superbillRes,2);
			$superbillDataArr[$sa_patient_id][$encounter_id]=0;
		}
/*		if(count($superbillDataArr[$sa_patient_id])>0 && (sizeof($arrEncs) == $arrPtIndex[$sa_patient_id]+1)){
				$superbillRes+= array_sum($superbillDataArr[$sa_patient_id]);
				$superBillCharges = '$'.number_format($superbillRes,2);
				unset($superbillDataArr[$sa_patient_id]);
		}*/

		 
		//--- POSTED AMOUNT -----
		if($patientChargesDataArr[$sa_patient_id]['posted_amount'][$encounter_id]){
			$postedAmt = $patientChargesDataArr[$sa_patient_id]['posted_amount'][$encounter_id];
		}
		//--- NOT POSTED AMOUNT ----
		if($patientChargesDataArr[$sa_patient_id]['not_posted_amount'][$encounter_id]){
			$notPostedAmt = $patientChargesDataArr[$sa_patient_id]['not_posted_amount'][$encounter_id];
		}
	
		$totalNotPostedChares += $notPostedAmt;
		$notPostedAmt = numberformat($notPostedAmt,2);
		$totalPostedChares += $postedAmt;
		$postedAmt = numberformat($postedAmt,2);
	
		//---- GET TOTAL PAYMENT DETAILS ----------			
		$pay_amt_arr = array();
		$paymentArr = $paymentDataArr[$encounter_id];
		$pay_amt_arr['cashAmt'][] = array_sum($paymentArr['cash']);
		$pay_amt_arr['CheckAmt'][] = array_sum($paymentArr['check']);
		$pay_amt_arr['CCAmt'][] = array_sum($paymentArr['credit']);
		$pay_amt_arr['moAmt'][] = array_sum($paymentArr['money order']);
		$pay_amt_arr['veepAmt'][] = array_sum($paymentArr['veep']);
		$pay_amt_arr['eftAmt'][] = array_sum($paymentArr['eft']);

		// SET PAYMENT COLORS
		$amtColorCash=$amtColorCheck=$amtColorCC=$amtColorMO=$amtColorEFT='#000';
		$cashStar=$checkStar=$ccStar=$moStar=$veepStar=$eftStar='';
		//------------------			
		
		$cashAmt = NULL;
		if(count($pay_amt_arr['cashAmt'])>0){
			$cashAmt = array_sum($pay_amt_arr['cashAmt']);
		}			

		$CheckAmt = NULL;
		if(count($pay_amt_arr['CheckAmt'])>0){
			$CheckAmt = array_sum($pay_amt_arr['CheckAmt']);
		}
		$CCAmt = NULL;
		if(count($pay_amt_arr['CCAmt'])>0){
			$CCAmt = array_sum($pay_amt_arr['CCAmt']);
		}
		
		$moAmt = NULL;
		if(count($pay_amt_arr['moAmt'])>0){
			$moAmt = array_sum($pay_amt_arr['moAmt']);
		}

		$veepAmt = NULL;
		if(count($pay_amt_arr['veepAmt'])>0){
			$veepAmt = array_sum($pay_amt_arr['veepAmt']);
		}
		
		$eftAmt = NULL;
		if(count($pay_amt_arr['eftAmt'])>0){
			$eftAmt = array_sum($pay_amt_arr['eftAmt']);
		}

		//ADDING CI/CO AND PRE-PAYMENT COLLECTIONS
		//cash
		if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash']>0){
			if($cashAmt>0){ $cashStar='*'; }
			$amtColorCash='#2A2AFF';
			$cashAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash'];
			unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash']);
		}
		if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash']>0){
			if($cashAmt>0){ $cashStar='*'; }
			$amtColorCash='#FF7F55';
			$cashAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash'];
			unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['cash']);
		}
		//check
		if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check']>0){
			if($CheckAmt>0){ $checkStar='*'; }
			$amtColorCheck='#2A2AFF';
			$CheckAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check'];
			unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check']);
		}
		if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check']>0){
			if($CheckAmt>0){ $checkStar='*'; }
			$amtColorCheck='#FF7F55';
			$CheckAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check'];
			unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['check']);
		}
		//CC
		if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card']>0){
			if($CCAmt>0){ $ccStar='*'; }
			$amtColorCC='#2A2AFF';
			$CCAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card'];
			unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card']);
		}
		if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card']>0){
			if($CCAmt>0){ $ccStar='*'; }
			$amtColorCC='#FF7F55';
			$CCAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card'];
			unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['credit card']);
		}
		//MO
		if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order']>0){
			if($moAmt>0){ $moStar='*'; }
			$amtColorMO='#2A2AFF';
			$moAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order'];
			unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order']);
		}
		if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order']>0){
			if($moAmt>0){ $moStar='*'; }
			$amtColorMO='#FF7F55';
			$moAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order'];
			unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['money order']);
		}
		//VEEP
		if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep']>0){
			if($veepAmt>0){ $veepStar='*'; }
			$amtColorMO='#2A2AFF';
			$veepAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep'];
			unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep']);
		}
		if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep']>0){
			if($veepAmt>0){ $veepStar='*'; }
			$amtColorMO='#FF7F55';
			$veepAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep'];
			unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['veep']);
		}

		//EFT
		if($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft']>0){
			if($eftAmt>0){ $eftStar='*'; }
			$amtColorEFT='#2A2AFF';
			$eftAmt+=$cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft'];
			unset($cioPatientPay[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft']);
		}
		if($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft']>0){
			if($eftAmt>0){ $eftStar='*'; }
			$amtColorEFT='#FF7F55';
			$eftAmt+=$prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft'];
			unset($prePayPatients[$sa_doctor_id][$intThisFacilityId][$sa_patient_id]['eft']);
		}

		$totalCashAmount += $cashAmt;
		$totalCheckAmount += $CheckAmt;
		$totalCcAmount += $CCAmt;
		$totalMoAmount += $moAmt;
		$totalVeepAmount += $veepAmt;
		$totalEftAmount += $eftAmt;

		$cashAmt = numberformat($cashAmt,2);
		$CheckAmt = numberformat($CheckAmt,2);
		$CCAmt = numberformat($CCAmt,2);
		$moAmt = numberformat($moAmt,2);
		$veepAmt = numberformat($veepAmt,2);
		$eftAmt = numberformat($eftAmt,2);		
	
		$totalSuperBillCharges += $superbillRes;
		
		$moOrVeep=$br='';
		if($moAmt>0)$moOrVeep='MO '.$moStar.$moAmt;$br='<br>';
		if($veepAmt>0)$moOrVeep=$br.'VEEP '.$veepStar.$veepAmt;
		
		//$okCnt++;
		$rptData .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td class="text_10"  valign="top" align="center"></td>
				<td class="text_10"  valign="top" align="center"></td>
				<td class="text_10"  valign="top" align="left"></td>
				<td class="text_10"  valign="top" align="center"></td>
				<td class="text_10"  valign="top" align="center"></td>
				<td class="text_10"  valign="top" align="left"></td>
				<td class="text_10"  valign="top" style="text-align:right" >$superBillCharges</td>
				<td class="text_10" valign="top" style="text-align:right" >$notPostedAmt</td>
				<td class="text_10" valign="top" style="text-align:right" >$postedAmt</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorCash" >$cashStar$cashAmt</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorCheck" >$checkStar$CheckAmt</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorCC" >$ccStar$CCAmt</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorMO" >$moOrVeep</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorEFT" >$eftStar$eftAmt</td>
			</tr>
DATA;
		//---- CSV DATA ---
		$rpt_csv_rep .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td class="text_10" width="20" valign="top" align="center"></td>
				<td class="text_10" width="60" valign="top" align="center"></td>
				<td class="text_10" width="165" valign="top" align="left"></td>
				<td class="text_10" width="70" valign="top" align="center"></td>
				<td class="text_10" width="70" valign="top" align="center"></td>
				<td class="text_10" width="130" valign="top" align="left"></td>
				<td class="text_10" width="80" valign="top" style="text-align:right" >$superBillCharges</td>
				<td class="text_10" valign="top" style="text-align:right" width="50">$notPostedAmt</td>
				<td class="text_10" valign="top" style="text-align:right" width="50">$postedAmt</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorCash" width="50">$cashStar$cashAmt</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorCheck" width="50">$checkStar$CheckAmt</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorCC" width="50">$ccStar$CCAmt</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorMO" width="50">$moOrVeep</td>
				<td class="text_10" valign="top" style="text-align:right; color:$amtColorEFT" width="50">$eftStar$eftAmt</td>
			</tr>
DATA;
	}
				

	return $rptData.'~'.$rpt_csv_rep.'~'.$totalSuperBillCharges.'~'.$totalNotPostedChares.'~'.$totalPostedChares.'~'.$totalCashAmount.'~'.$totalCheckAmount.'~'.$totalCcAmount.'~'.$totalMoAmount.'~'.$totalEftAmount.'~'.$totalVeepAmount;
	
	
}
	$tooltip='Red coloured CI/CO and Pre-Payments represents that there is refund amount deducted from these payments.';
	
	if($strHTML)
	{
	$strHTML .= '<table class="rpt_table rpt rpt_table-bordered" width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
		<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
		<tr><td style="width:20px;" class="info" style="background-color:#FFFFFF;">&nbsp;</td>
		<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
		<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
		'.$tooltip.'
		</td>
		</tr>
		</table>';
	}

	
	if($csv_data)
	{
	$csv_data .= '<table class="rpt_table rpt rpt_table-bordered" width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
			<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
			<tr><td style="width:20px;" style="background-color:#FFFFFF;">&nbsp;</td>
			<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
			<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
			'.$tooltip.'<br/>Refund amount can be view by mouse over on red coloured amount.
			</td>
			</tr>
			</table>';
	}	
	$csv_data;
	
?>