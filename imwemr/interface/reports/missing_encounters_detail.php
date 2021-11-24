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

$showCurrencySymbol = showCurrency();
$multi = 0;
$totEnc=0;
$arr=array();
$arr[]="Physician";
$arr[]="Facility";	
$arr[]="Date & Time";
$arr[]="CI Time";
$arr[]="CO Time";
$arr[]="Patient ID";
$arr[]="Procedure";
$arr[]="SuperBill";
$arr[]="EID";
$arr[]="Charges Posted";
$arr[]="Claim File & Date";
$arr[]="Deferred Claims";
fputcsv($fp,$arr, ",","\"");
$fp = fopen ($csv_file_name, 'a+');
foreach($appointmentDataArr as $sel_pro => $facilityData){
	$arrPhyTotal=array();
	$phyName = ucwords(trim($physicianNameArr[$sel_pro]));
	$csv_rep .='<tr><td class="text_b_w" width="auto" align="Left" colspan="11">Physician : '.$phyName.'</td></tr>';
	$reportData1 .='<tr><td class="text_b_w" width="auto" align="Left" colspan="7">Physician : '.$phyName.'</td></tr>';
	foreach($facilityData as $facId => $reportRes){
		$arrFacTotal=array();
		$facName=$arrFacilityNames[$facId];
		$csv_rep .='<tr><td class="text_b_w" width="auto" align="Left" colspan="11">Facility : '.$arrFacilityNames[$facId].'</td></tr>';
		$reportData1 .='<tr><td class="text_b_w" width="auto" align="Left" colspan="7">Facility : '.$arrFacilityNames[$facId].'</td></tr>';
		$tempArr =$tempArr1 = array();
		for($r=0,$okCnt=0;$r<count($reportRes);$r++,$tempR++){		
			$intThisFacilityId = $reportRes[$r]['sa_facility_id'];		
			$sa_app_startdate_tr = $reportRes[$r]['sa_app_startdate'];
			$checkInTime = '';
			$sa_app_starttime = substr($reportRes[$r]['sa_app_starttime'],0,-3);
			$sa_app_endtime = substr($reportRes[$r]['sa_app_endtime'],0,-3);
			$patNameArr = array();
			$patNameArr["LAST_NAME"] = $reportRes[$r]['lname'];
			$patNameArr["FIRST_NAME"] = $reportRes[$r]['fname'];
			$patNameArr["MIDDLE_NAME"] = $reportRes[$r]['mname'];
			$patName = changeNameFormat($patNameArr);
			
			$patPhone = core_phone_format($reportRes[$r]['phone_home']);
			
			$sa_doctor_id = $reportRes[$r]['sa_doctor_id'];
			$sa_patient_id = $reportRes[$r]['sa_patient_id'];
			$patientIdArr[] = $reportRes[$r]['sa_patient_id'];
			$proc = $reportRes[$r]['proc'];
			$sa_id = $reportRes[$r]['id'];
			
			$sa_patient_app_status_id = $reportRes[$r]['sa_patient_app_status_id'];
			
			$arrs[] = $sa_patient_app_status_id;
			$totalToDoApp++;
			$check_in_out_st = false;
			
			$enc_id = 0;
			$reportData1_part='';
			$csv_rep_part = '';
	
			$reportData1_part.='<table border="0" cellpadding="0" cellspacing="1" bgcolor="#FFF3E8">';
			$csv_rep_part.='<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFF3E8">';
				
			$apptSize=sizeof($arrPatApptSize[$sa_patient_id][$sa_app_startdate_tr]);
			unset($arrPatApptSize[$sa_patient_id][$sa_app_startdate_tr][$r]);
			$arrPatApptSize[$sa_patient_id][$sa_app_startdate_tr]=array_values($arrPatApptSize[$sa_patient_id][$sa_app_startdate_tr]);
			
			//--- GET ALL POSTED CHARGES WITH IN DATE RANGE ------
			if(count($patientChargesDataArr[$sa_patient_id][$sa_app_startdate_tr]['encounter_id'])>0){
	
				$encSize=sizeof($arrPatEncSize[$sa_patient_id][$sa_app_startdate_tr]);	
				$encKArr=array_keys($arrPatEncSize[$sa_patient_id][$sa_app_startdate_tr]);
				$piStart=$encKArr[0];
				end($arrPatEncSize[$sa_patient_id][$sa_app_startdate_tr]);
				$piEnd=key($arrPatEncSize[$sa_patient_id][$sa_app_startdate_tr]);
				if($apptSize>1){ $piEnd=$encKArr[0]; }
				if($encSize==0){ $piStart=1; $piEnd=0;} //BECAUSE LOOP SHOULD NOT RUN
	
				for($pi=$piStart; $pi<= $piEnd; $pi++){
					$testData=array();
					$enc_id =0;
					$notPostedAmt ='';
					$postedDate='';
					$superBillAmt ='N/SB';
					$submittedDate='';
					$submittedFile = '';
					$responsedFile = '';
					
					$notPostedAmt = $patientChargesDataArr[$sa_patient_id][$sa_app_startdate_tr]['not_posted_amount'][$pi];
					$totalNotPostedChares+= $notPostedAmt; //TOTAL
					if($patientChargesDataArr[$sa_patient_id][$sa_app_startdate_tr]['super_bill_encounter'][$pi]>0){
						if($patientChargesDataArr[$sa_patient_id][$sa_app_startdate_tr]['super_bill_discharge'][$pi]>0 && $patientChargesDataArr[$sa_patient_id][$sa_app_startdate_tr]['super_bill_amount'][$pi]<=0){						
							$superBillAmt = 'SB-DS<br>(Not Processed)';	// SUPER BILL FROM DISCHARGE SUMMARY
						}else{
							$superBillAmt = $patientChargesDataArr[$sa_patient_id][$sa_app_startdate_tr]['super_bill_amount'][$pi];
							$arrFacTotal['superbill']+= $superBillAmt;	// TOTAL
							if($superBillAmt>0 || $superBillAmt<0){
								$superBillAmt = $CLSReports->numberFormat($superBillAmt,2,false);
							}else{
								$superBillAmt=$showCurrencySymbol.'00.00'; 
							}
						}
					}
					$enc_id = $patientChargesDataArr[$sa_patient_id][$sa_app_startdate_tr]['encounter_id'][$pi];
	
					$submittedFile = $arrSubmittedFiles[$enc_id];
					$deferredInfo=implode(', ', $arrDeferredInfo[$enc_id]);
					$postedAmt = $postedAmount[$enc_id];
					$arrFacTotal['posted_amt']+=$postedAmt;
					
					if($superBillCharges =="N/SB") {
						$billAlign = "style=\"text-align:left\"";
					}else {
						$billAlign = "style=\"text-align:right\"";
					}					
					$testData['superBillAmt']=$superBillAmt;	
					$testData['enc_id']=$enc_id;	
					$testData['postedAmt']=$postedAmt;	
					$testData['submittedFile']=$submittedFile;	
					$testData['deferred_info']= $deferredInfo;	
					$tempArr[$sa_patient_id][$sa_app_startdate_tr][]=$testData;
					$csv_rep_part.='
					<tr bgcolor="#FFFFFF" style="height:25px">
						<td class="text_10" width="105" '.$billAlign.'>'.$superBillAmt.'&nbsp;</td>
						<td class="text_10" width="111" style="text-align:center;" >'.$enc_id.'</td>
						<td class="text_10" width="111" style="text-align:right;">'.$postedAmt.'&nbsp;</td>
						<td class="text_10" width="111" style="text-align:left;">&nbsp;'.$submittedFile.'</td>
						<td class="text_10" width="111" style="text-align:left;">&nbsp;'.$deferredInfo.'</td>
					</tr>';
					//FOR PDF
					$reportData1_part.='
					<tr bgcolor="#FFFFFF" style="height:25px">
						<td class="text_10" width="88" '.$billAlign.'>'.$superBillAmt.'&nbsp;</td>
						<td class="text_10" width="88" style="text-align:center;">'.$enc_id.'</td>
						<td class="text_10" width="88" style="text-align:right;">'.$postedAmt.'&nbsp;</td>
						<td class="text_10" width="100" style="text-align:left;">&nbsp;'.$submittedFile.'</td>
						<td class="text_10" width="90" style="text-align:left;">&nbsp;'.$deferredInfo.'</td>
					</tr>';
				unset($arrPatEncSize[$sa_patient_id][$sa_app_startdate_tr][$pi]);
				}
			}
	
	//pre();
			//--- GET SUPER BILL CHARGES -------
			$enc_id=0;
			if(count($superbillEncArr[$sa_patient_id][$sa_app_startdate_tr])>0){
				$sbSize=sizeof($arrPatSBSize[$sa_patient_id][$sa_app_startdate_tr]);	
				$sbKArr=array_keys($arrPatSBSize[$sa_patient_id][$sa_app_startdate_tr]);
				$sbStart=$sbKArr[0];
				end($arrPatSBSize[$sa_patient_id][$sa_app_startdate_tr]);
				$sbEnd=key($arrPatSBSize[$sa_patient_id][$sa_app_startdate_tr]);
				if($apptSize>1){ $sbEnd=$sbKArr[0]; }
				if($sbSize==0){ $sbStart=1; $sbEnd=0;} //BECAUSE LOOP SHOULD NOT RUN
	
				for($sb=$sbStart; $sb<= $sbEnd; $sb++){
					$testData1 =array();
					$superbillRes = '';
					$superBillCharges='';
					
					$enc_id = $superbillEncArr[$sa_patient_id][$sa_app_startdate_tr][$sb];
					if($superbillDisArr[$sa_patient_id][$sa_app_startdate_tr][$sb]>0 && $superbillDataArr[$sa_patient_id][$sa_app_startdate_tr][$sb]<=0){
						$superBillCharges = 'SB-DS<br>(Not Processed)';
					}else{
						$superbillRes = $superbillDataArr[$sa_patient_id][$sa_app_startdate_tr][$sb];
						$arrFacTotal['superbill']+= $superbillRes;	// TOTAL
						$superBillCharges = $CLSReports->numberFormat($superbillRes,2);
					}
					
					$testData1['superBillCharges']=$superBillCharges;	
					$testData1['enc_id1']=$enc_id;	
					
					$tempArr1[$sa_patient_id][$sa_app_startdate_tr][]=$testData1;
					$csv_rep_part .='
					<tr bgcolor="#FFFFFF" style="height:25px">
						<td class="text_10" width="105" style="text-align:right">'.$superBillCharges.'&nbsp;</td>
						<td class="text_10" width="111" style="text-align:center;">'.$enc_id.'</td>
						<td class="text_10" width="111" style="text-align:right;">&nbsp;</td>
						<td class="text_10" width="auto" style="text-align:right;">&nbsp;</td>
					</tr>';
					// FOR PDF
					$reportData1_part .='
					<tr bgcolor="#FFFFFF" style="height:25px">
						<td class="text_10" width="85" style="text-align:right" >'.$superBillCharges.'&nbsp;</td>
						<td class="text_10" width="85" style="text-align:center;" >'.$enc_id.'</td>
						<td class="text_10" width="85" style="text-align:left;">&nbsp;</td>
						<td class="text_10" width="100" style="text-align:left;">&nbsp;</td>
					</tr>';
				unset($arrPatSBSize[$sa_patient_id][$sa_app_startdate_tr][$sb]);								
				}
			}
			$reportData1_part .='</table>';
			$csv_rep_part .='</table>';
			
	
			$sa_app_starttime_tr = getMainAmPmTime($sa_app_starttime);
			$checkInTime = $arrCheckInData[$sa_id]['check_in'];
			$checkInTime = ($checkInTime=='')? 'N/CI' : getMainAmPmTime($checkInTime);
			$checkOutTime = $arrCheckInData[$sa_id]['check_out'];
			$checkOutTime = ($checkOutTime=='')? 'N/CO' : getMainAmPmTime($checkOutTime);

			$patName = $patName.' - '.$sa_patient_id;






if(count($tempArr[$sa_patient_id][$sa_app_startdate_tr])>0 || count($tempArr1[$sa_patient_id][$sa_app_startdate_tr])>0){
	$csvCounter=0;
	if(count($tempArr[$sa_patient_id][$sa_app_startdate_tr])>0) {
		foreach($tempArr[$sa_patient_id][$sa_app_startdate_tr] as $data){
			$csvCounter++;
			if($csvCounter==1){
				$arr=array();
				$arr[]=$phyName;
				$arr[]=$facName;
				$arr[]=$sa_app_startdate_tr.' '.$sa_app_starttime_tr;
				$arr[]=$checkInTime;
				$arr[]=$checkOutTime;
				$arr[]=$patName;
				$arr[]=$proc;
				$arr[]=	$data['superBillAmt'];
				$arr[]= $data['enc_id'];
				$arr[]= $data['postedAmt'];
				$arr[]= $data['submittedFile'];
				$arr[]= $data['deferred_info'];
				fputcsv($fp,$arr, ",","\"");
			} else {
				$arr = array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]=	$data['superBillAmt'];
				$arr[]= $data['enc_id'];
				$arr[]= $data['postedAmt'];
				$arr[]= $data['submittedFile'];
				$arr[]= $data['deferred_info'];
				fputcsv($fp,$arr, ",","\"");
			}
		}
	} 
	if(count($tempArr1[$sa_patient_id][$sa_app_startdate_tr])>0) {
		$csvCounter1=0;
		foreach($tempArr1[$sa_patient_id][$sa_app_startdate_tr] as $data1){
			$csvCounter1++;
			if($csvCounter1==1){
				$arr=array();
				$arr[]=$phyName;
				$arr[]=$facName;
				$arr[]=$sa_app_startdate_tr.' '.$sa_app_starttime_tr;
				$arr[]=$checkInTime;
				$arr[]=$checkOutTime;
				$arr[]=$patName;
				$arr[]=$proc;
				$arr[]=	$data1['superBillCharges'];
				$arr[]= $data1['enc_id1'];
				$arr[]= "";
				$arr[]= "";
				fputcsv($fp,$arr, ",","\"");
			} else {
				$arr = array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]=	$data1['superBillCharges'];
				$arr[]= $data1['enc_id1'];
				$arr[]="";
				$arr[]="";
				fputcsv($fp,$arr, ",","\"");
			}
		}
	}
	}else{
		$arr=array();
		$arr[]=$phyName;
		$arr[]=$facName;
		$arr[]=$sa_app_startdate_tr.' '.$sa_app_starttime_tr;
		$arr[]=$checkInTime;
		$arr[]=$checkOutTime;
		$arr[]=$patName;
		$arr[]=$proc;
		fputcsv($fp,$arr, ",","\"");
	}
			
		$okCnt++;
		$reportData1 .='
		<tr bgcolor="#FFFFFF" style="height:25px">
			<td class="text_10" style="width:20px" align="center">'.$okCnt.'</td>
			<td class="text_10" style="width:105px" align="center">'.$sa_app_startdate_tr.' '.$sa_app_starttime_tr.'</td>
			<td class="text_10" style="width:70px" align="center">'.$checkInTime.'</td>
			<td class="text_10" style="width:70px" align="center">'.$checkOutTime.'</td>
			<td class="text_10" style="width:130px" align="left">'.$patName.'</td>
			<td class="text_10" style="width:95px" align="left">'.$proc.'</td>
			<td class="text_10" style="width:auto" align="left">'.$reportData1_part.'</td>
		</tr>';
		//---- CSV DATA ---
		$csv_rep .='
		<tr bgcolor="#FFFFFF" style="height:25px">
			<td class="text_10" valign="top" align="center">'.$okCnt.'</td>
			<td class="text_10" valign="top" align="center">'.$sa_app_startdate_tr.' '.$sa_app_starttime_tr.'</td>
			<td class="text_10" valign="top" align="center">'.$checkInTime.'</td>
			<td class="text_10" valign="top" align="center">'.$checkOutTime.'</td>
			<td class="text_10" valign="top" align="left">'.$patName.'</td>
			<td class="text_10" valign="top" align="left">'.$proc.'</td>
			<td class="text_10" valign="top" align="left" colspan="5">'.$csv_rep_part.'</td>
		</tr>';
	}

	//FACILITY TOTAL
	$arrPhyTotal['superbill']+=($arrFacTotal['superbill']=='')? 0 : $arrFacTotal['superbill'];
	$arrPhyTotal['posted_amt']+=($arrFacTotal['posted_amt']=='')? 0 :$arrFacTotal['posted_amt'];

	$facSuperBill = $CLSReports->numberFormat($arrFacTotal['superbill'],2);
	$facPostedAmt = $CLSReports->numberFormat($arrFacTotal['posted_amt'],2);
	if($facSuperBill==''){$facSuperBill=$showCurrencySymbol.'0.00'; }
	if($facPostedAmt==''){$facPostedAmt=$showCurrencySymbol.'0.00'; }
	
	$reportData1 .='
	<tr style="height:1px">
		<td class="total-row" colspan="7"></td>
	</tr>
	<tr bgcolor="#FFFFFF" valign="top">
		<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;" colspan="6">Facility Total : </td>
		<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;">
			<table width="100%" cellspacing="1" cellpadding="0" border="0"  bgcolor="#FFF3E8">
			<tr>
				<td class="text_10b" style="text-align:right;" width="88">'.$facSuperBill.'&nbsp;</td>
				<td class="text_10b" style="text-align:right;" width="88"></td>
				<td class="text_10b" style="text-align:right;" width="88">'.$facPostedAmt.'&nbsp;</td>
				<td bgcolor="#FFFFFF"  width="100"></td>
				<td bgcolor="#FFFFFF"  width="90"></td>
			</tr>
			</table>	
		</td>
	</tr>
	<tr>
		<td class="total-row" colspan="7"></td>
	</tr>';

	//--- CSV DATA ----
	$csv_rep .='
	<tr style="height:1px">
		<td class="total-row" colspan="11"></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td class="text_10b" valign="top" style="text-align:right;" colspan="6">Facility Total : </td>
		<td class="text_10b" valign="top" style="text-align:right;">'.$facSuperBill.'&nbsp;</td>
		<td class="text_10b" valign="top" style="text-align:right;"></td>
		<td class="text_10b" valign="top" style="text-align:right;">'.$facPostedAmt.'&nbsp;</td>
		<td class="text_10b" valign="top" style="text-align:right;"></td>
		<td class="text_10b" valign="top" style="text-align:right;"></td>
	</tr>
	<tr>
		<td class="total-row" colspan="11"></td>
	</tr>';
}

	//PHYSICIAN TOTAL
	$arrGrandTotals['superbill']+=$arrPhyTotal['superbill'];
	$arrGrandTotals['posted_amt']+=$arrPhyTotal['posted_amt'];

	$phySuperBill = $CLSReports->numberFormat($arrPhyTotal['superbill'],2);
	$phyPostedAmt = $CLSReports->numberFormat($arrPhyTotal['posted_amt'],2);
	if($phySuperBill==''){$phySuperBill=$showCurrencySymbol.'0.00'; }
	if($phyPostedAmt==''){$phyPostedAmt=$showCurrencySymbol.'0.00'; }
	
	$reportData1 .='
	<tr style="height:1px">
		<td class="total-row" colspan="7"></td>
	</tr>
	<tr bgcolor="#FFFFFF" valign="top">
		<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;" colspan="6">Physician Total : </td>
		<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;">
			<table width="100%" cellspacing="1" cellpadding="0" border="0"  bgcolor="#FFF3E8">
			<tr>
				<td class="text_10b" style="text-align:right;" width="88">'.$phySuperBill.'&nbsp;</td>
				<td class="text_10b" style="text-align:right;" width="88"></td>
				<td class="text_10b" style="text-align:right;" width="88">'.$phyPostedAmt.'&nbsp;</td>
				<td bgcolor="#FFFFFF" width="100"></td>
				<td bgcolor="#FFFFFF" width="90"></td>
			</tr>
			</table>	
		</td>
	</tr>
	<tr>
		<td class="total-row" colspan="7"></td>
	</tr>';

	//--- CSV DATA ----
	$csv_rep .='
	<tr style="height:1px">
		<td class="total-row" colspan="11"></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td class="text_10b" valign="top" style="text-align:right;" colspan="6">Physician Total : </td>
		<td class="text_10b" valign="top" style="text-align:right;">'.$phySuperBill.'&nbsp;</td>
		<td class="text_10b" valign="top" style="text-align:right;"></td>
		<td class="text_10b" valign="top" style="text-align:right;">'.$phyPostedAmt.'&nbsp;</td>
		<td class="text_10b" valign="top" style="text-align:right;"></td>
		<td class="text_10b" valign="top" style="text-align:right;"></td>
	</tr>
	<tr>
		<td class="total-row" colspan="11"></td>
	</tr>';
}


if($csv_rep!=''){

	$grdSuperBill = $CLSReports->numberFormat($arrGrandTotals['superbill'],2);
	$grdPostedAmt = $CLSReports->numberFormat($arrGrandTotals['posted_amt'],2);
	if($grdSuperBill==''){$grdSuperBill=$showCurrencySymbol.'0.00'; }
	if($grdPostedAmt==''){$grdPostedAmt=$showCurrencySymbol.'0.00'; }

	$strHTML .='
	<page backtop="15mm" backbottom="5mm">
	<page_footer>	
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:300px;">&nbsp;Unapplied Superbills ('.$summary_detail.')</td>
				<td class="rptbx2" style="width:300px;">&nbsp;Date: From '.$startDate1.' To '.$endDate1.'</td>
				<td class="rptbx3" style="width:450px;">&nbsp;Created by '.$op_name.' on '.$curDate.'&nbsp;</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1">	&nbsp;Selected Group : '.$selGrp.' </td>
				<td class="rptbx2"> &nbsp;Selected Facility : '.$selFac.' </td>
				<td class="rptbx3"> &nbsp;Selected Physician : '.$selPhy.' &nbsp;&nbsp;&nbsp;Encounter Status : '.$sel_enc_status.' </td>
			</tr>	
		</table>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
			<tr>
				<td class="text_b_w" style="width:20px; text-align:center;">#</td>
				<td class="text_b_w" style="width:105px; text-align:center;">App Date & Time</td>
				<td class="text_b_w" style="width:75px; text-align:center;">CI Time</td>
				<td class="text_b_w" style="width:75px; text-align:center;">CO Time</td>
				<td class="text_b_w" style="width:120px; text-align:center;">Patient Name - ID</td>
				<td class="text_b_w" style="width:100px; text-align:center;">Procedure</td>
				<td class="text_b_w" style="width:85px; text-align:center;">SuperBill</td>
				<td class="text_b_w" style="width:85px; text-align:center;">EID</td>
				<td class="text_b_w" style="text-align:center; width:85px">Charges Posted</td>
				<td class="text_b_w" style="text-align:center; width:100px;">Claim File & Date</td>
				<td class="text_b_w" style="text-align:center; width:90px;">Deferred Claims</td>
			</tr>
		</table>
	</page_header>
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">				
	'.$reportDataHeader.'
	'.$reportData1.'
	<tr><td class="total-row" colspan="7"></td></tr>
	<tr bgcolor="#FFFFFF" valign="top">
		<td bgcolor="#FFFFFF" class="text_10b" colspan="6" style="text-align:right" >Grand Total :</td>
		<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right" >
			<table width="100%" cellspacing="1" cellpadding="0" border="0"  bgcolor="#FFF3E8">
			<tr>
				<td class="text_10b" style="text-align:right;" width="88">'.$grdSuperBill.'&nbsp;</td>
				<td class="text_10b" style="text-align:right;" width="88"></td>
				<td class="text_10b" style="text-align:right;" width="88">'.$grdPostedAmt.'&nbsp;</td>
				<td bgcolor="#FFFFFF" width="auto" width="100"></td>
				<td bgcolor="#FFFFFF" width="auto" width="90"></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td class="total-row" colspan="7"></td></tr>
	</table>
	</page>';

	$conditionChk = true;

	//--- CSV DATA ----
	$grtandSuperBill = $arrGrandTotals['superbill'];
	$grtandSuperBill=$CLSReports->numberFormat($grtandSuperBill,2);
	
	$csv_data .='
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
	<tr class="rpt_headers">
		<td class="rptbx1" style="width:342px;">&nbsp;Unapplied Superbills ('.$summary_detail.')</td>
		<td class="rptbx2" style="width:350px;">&nbsp;Date: From '.$startDate1.' To '.$endDate1.'</td>
		<td class="rptbx3" style="width:350px;">&nbsp;Created by '.$op_name.' on '.$curDate.'&nbsp;</td>
	</tr>
	<tr class="rpt_headers">
		<td class="rptbx1">	&nbsp;Selected Group : '.$selGrp.' </td>
		<td class="rptbx2"> &nbsp;Selected Facility : '.$selFac.' </td>
		<td class="rptbx3"> &nbsp;Selected Physician : '.$selPhy.' &nbsp;&nbsp;&nbsp;Encounter Status : '.$sel_enc_status.' </td>
	</tr>	
	</table>
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
	<tr>
		<td class="text_b_w" width="20" style="text-align:center">#</td>
		<td class="text_b_w" width="140" style="text-align:center">Appt Date & Time</td>
		<td class="text_b_w" width="70" style="text-align:center">CI Time</td>
		<td class="text_b_w" width="70" style="text-align:center">CO Time</td>
		<td class="text_b_w" width="220" style="text-align:center">Patient Name - ID</td>
		<td class="text_b_w" width="150" style="text-align:center">Procedure</td>
		<td class="text_b_w" width="110" style="text-align:center">SuperBill</td>
		<td class="text_b_w" width="110" style="text-align:center">EID</td>
		<td class="text_b_w" width="110" style="text-align:center";>Charges Posted</td>
		<td class="text_b_w" width="110" style="text-align:center";>Claim File & Date</td>
		<td class="text_b_w" width="110" style="text-align:center";>Deferred Claims</td>
	</tr>
	'.$csv_rep.'
	<tr><td colspan="10" height="10px" bgcolor="#ffffff"></td></tr>
	<tr><td class="total-row" colspan="11"></td></tr>
	<tr bgcolor="#FFFFFF" style="height:20px">
		<td class="text_10b" valign="top" style="text-align:right;" colspan="6" width="600">Grand Total : </td>
		<td class="text_10b" valign="top" style="text-align:right;" width="100">'.$grdSuperBill.'&nbsp;</td>
		<td class="text_10b" valign="top" style="text-align:right;" width="100"></td>
		<td class="text_10b" valign="top" style="text-align:right;" width="100">'.$grdPostedAmt.'&nbsp;</td>
		<td class="text_10b" valign="top" style="text-align:right;" width="100"></td>
		<td class="text_10b" valign="top" style="text-align:right;" width="110"></td>
	</tr>
	<tr><td class="total-row" colspan="11"></td></tr>
	</table>';
}	


$sel_pro = join(',',$sel_pro_arr);
?>