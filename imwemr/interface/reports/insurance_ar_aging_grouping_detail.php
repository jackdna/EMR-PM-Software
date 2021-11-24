<?php
$printAllDue = false;
$strQryPart = $strXML = $strLink = $slicedOut = $forDataURL = NULL;
//--- GET TOTAL COLUMN WIDTH ----
$column = ceil(($aging_to - $aging_start) / $aggingCycle);

if($All_due == true){
$column++;
}

$grandTotalArr = array();
$ptNoteArr = array();

$task_reminder_enc_arr = implode(', ', $task_reminder_enc_arr);
$getDetailsStr = "SELECT encounter_id, encComments,task_assign_for, DATE_FORMAT(reminder_date, '".get_sql_date_format()."') as reminder_date 
FROM paymentscomment 
WHERE encounter_id in ($task_reminder_enc_arr) 
$whr_due_date_chk
ORDER By commentId";
$getDetailsQry = imw_query($getDetailsStr);
while($getDetailsRow = imw_fetch_assoc($getDetailsQry)){
	$prov_rem_id = explode(',', $getDetailsRow['task_assign_for']);
	$reminder_date = ($getDetailsRow['reminder_date'] != "00-00-0000") ? $getDetailsRow['reminder_date'] : "";
	$tempProArr = array();
	foreach($prov_rem_id as $id){
		$pt_id_rem = $providerNameArr[$id];
		if(empty($pt_id_rem) == false) $tempProArr[] =  $pt_id_rem;
	}
	
	$remDate_Pro = "";
	if(count($tempProArr)){
		$remDate_Pro = "\n Assign for: \n".implode("\n", $tempProArr);
	}
	$arrEncNotes[$getDetailsRow['encounter_id']]['reminder_note']= $getDetailsRow['encComments'];
	$arrEncNotes[$getDetailsRow['encounter_id']]['reminder_date_provider']= $reminder_date.$remDate_Pro;
}

//---- GET TOTAL WIDTH FOR AUTO GENERATE TD --------
switch($column){
	case '1':
		$width = '280';
		//$pdfWidth = $colWidthAR / 2;
		$pdfWidth = $pdfHeaderWidth = $colHeaderWidthAR / 2;
	break;
	case '2':
		$width = '185';
		//$pdfWidth = $colWidthAR / 3;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 3) - 2;
	break;
	case '3':
		$width = '138';
		//$pdfWidth = $colWidthAR / 4;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 4) - 3;
	break;
	case '4':
		$width = '109';
		//$pdfWidth = $colWidthAR / 5;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 5) - 4;
	break;
	case '5':
		$width = '90';
		//$pdfWidth = $colWidthAR / 6;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 6) - 4;
	break;
	case '6':
		$width = '77';
		//$pdfWidth = $colWidthAR / 7;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 7) - 4;
	break;
	case '7':
		$width = '67';
		//$pdfWidth = $colWidthAR / 8;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 8) - 4;
	break;
}
$pdfHeaderWidth=$pdfHeaderWidth+7;
//--- TOTAL TD COLSPAN ---
$otherCols  = 2; 
if($inc_payments==1){$otherCols++;}
if($inc_adjustments==1){$otherCols++;}
$totalTd = $column + $otherCols;


$totalColTd = $totalTd - 1;
$cols1 = floor($totalTd / 3);
$cols = floor($totalTd / 2);
$cols2 = $totalTd- ($cols1+$cols1);

//Width to TDS
$totalWidth = 960;
$totaltdWidth = round(100/$totalTd,2);
$totaltdWidth.='%';
$btmcol	= $totalTd - 2;	
if($inc_payments==1){$btmcol--;}
if($inc_adjustments==1){$btmcol--;}

$pageContent = NULL;
$csvPageContent = NULL;
$self_ins_id_arr = array_keys($selfInsIdArr);

//MAKING OUTPUT DATA FOR CSV
$file_name="insurance_ar_aging_".time().".csv";
$csv_file_name= write_html("", $file_name);

//CSV FILE NAME
//$csv_file_name = "../../data/".PRACTICE_PATH."/UserId_".$_SESSION['authId']."/tmp/eid_status_".time().'.csv';
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

for($in=0;$in<count($self_ins_id_arr);$in++){

	$insuranceId = $self_ins_id_arr[$in];
	$insuranceName = $selfInsIdArr[$insuranceId];
	if (strlen($insuranceName) > 20) {
		$insuranceName = substr($insuranceName, 0, 20) . '...';
	}
	
	$contact_name = $insCompDetails[$insuranceId]['contact_name'];
	$phone = $insCompDetails[$insuranceId]['phone'];
	
	//--- INSURANCE COMPANY NAME ----
	$pageContent .= <<<DATA
			<tr>
				<td class="text_b_w" colspan="$cols2" style="">$insuranceName</td>
				<td class="text_b_w" colspan="$cols1" style="">Contact Name: $contact_name</td>
				<td class="text_b_w" colspan="$cols1" style="">Phone#: $phone</td>
			</tr>
DATA;
	
	$csvPageContent .= <<<DATA
			<tr>
				<td class="text_b_w" colspan="$cols2" style="">$insuranceName</td>
				<td class="text_b_w" colspan="$cols1" style="">Contact Name: $contact_name</td>
				<td class="text_b_w" colspan="$cols1" style="">Phone#: $phone</td>
			</tr>
DATA;

	$patientData = $mainInsIdArr[$insuranceId];
	$patient_id_arr = array_keys($patientData);	
	for($i=0;$i<count($patient_id_arr);$i++){
		$patient_id = $patient_id_arr[$i];
		$encounterDataArr = $patientData[$patient_id];		
		$encounterIdArr = array();
		$pat_age = $patient_age_arr[$patient_id];
		
		if(count($encounterDataArr) > 0){
			$encounterIdArr = array_keys($encounterDataArr);
			//---  ALL ENCOUNTERS OF A SINGLE PATIENT --------
			$pageContent1 = NULL;
			$PDFpageContent1 = NULL;
			$payments=$adjustments=0;
			for($e=0;$e<count($encounterIdArr);$e++){
				$encounter_id = $encounterIdArr[$e];
				$data_of_service = NULL;
				//--- GET ALL AGGING REPORTS OF SINGLE ENCOUNTERS FOR A PATIENT ---
				$ecounter_data = NULL;
				$totalBalance = 0;
				$posted_date = NULL;
				for($a=$aging_start;$a<=$aging_to;$a++){
					$start = $a;
					$a = $a > 0 ? $a - 1 : $a;
					//---- ENCOUNTER DATE OF SERVICE ------
					$dataArr = $encounterDataArr[$encounter_id][$start];
					if($dataArr[0]['date_of_service']){
						$data_of_service = $dataArr[0]['date_of_service'];

					}
					//---- ENCOUNTER DATE OF POSTED ------
					if($dataArr[0]['postedDate']){
						$posted_date = $dataArr[0]['postedDate'];
					}
					
					//--- GET ENCOUNTER DUE AMOUNT -----------
					$insuranceDue = 0;
					for($en=0;$en<count($dataArr);$en++){
						$insuranceDue += preg_replace('/,/','',$dataArr[$en]['insuranceDue']);
					}
					
					$grandTotalArr[$start][] = $insuranceDue;
					
					//--- PATIENT ENCOUNTER BALANCE ----					
					$totalBalance += $insuranceDue;

					//--- NUMBER FORMAT -----
					$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
					$ecounter_data .= "<td class=\"text_10\" style=\"text-align:right; width:$totaltdWidth\" >$insuranceDue</td>";
					$a += $aggingCycle;
				}
				
				if($All_due == true){
					//---- ENCOUNTER DATE OF SERVICE ------
					$dataArr = $encounterDataArr[$encounter_id][181];
					if($dataArr[0]['date_of_service']){
						//By Karan
						//$ServiceDate = $dataArr[0]['date_of_service'];
						//$dd1 = explode('-',$ServiceDate);
						//$data_of_service = date(''.phpDateFormat().'', mktime(0,0,0, date($dd1[0]), date($dd1[1]), date($dd1[2])));
						
						// By Jaswant Sir // 
						$data_of_service = $dataArr[0]['date_of_service'];
						
					}
					
					if($dataArr[0]['postedDate']){
						//By Karan
						//$postedDate = $dataArr[0]['postedDate'];
						//$dd = explode('-',$postedDate);
						//$posted_date = date(''.phpDateFormat().'', mktime(0,0,0, date($dd[0]), date($dd[1]), date($dd[2])));
						
						// By Jaswant Sir //
						$posted_date = $dataArr[0]['postedDate'];
						
					}
					//--- GET ENCOUNTER DUE AMOUNT -----------
					$insuranceDue = 0;
					for($en=0;$en<count($dataArr);$en++){
						$insuranceDue += preg_replace('/,/','',$dataArr[$en]['insuranceDue']);
					}
					
					$grandTotalArr[181][] = $insuranceDue;
					
					//--- PATIENT ENCOUNTER BALANCE ----					
					$totalBalance += $insuranceDue;

					//--- NUMBER FORMAT -----
					$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
					$ecounter_data .= <<<DATA
						<td class="text_10" style="text-align:right; width:$totaltdWidth">$insuranceDue</td>
DATA;
				}
				
				//--- NUMBER FORMAT -----
				$totalBalance = $CLSReports->numberFormat($totalBalance,2);
				$pageContent1 .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td class="text_10" style="text-align:left;">$data_of_service - $encounter_id &nbsp; ($posted_date)</td>
						<td class="text_10" style="text-align:right;" width="$totaltdWidth">$totalBalance</td>
					</tr>
DATA;

				$PDFpageContent1 .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td class="text_10" style="text-align:left;">$data_of_service - $encounter_id &nbsp; ($posted_date)</td>
						<td class="text_10" style="text-align:right;" width="$totaltdWidth">$totalBalance</td>
					</tr>
DATA;
			}
		}
		
		//--- PATIENT NAME ----
		$patientName = $patientNameArr[$patient_id];
		
		//--- PDF DEATILS DATA ----
		$pageContent .= <<<DATA
			<tr>
				<td colspan="$totalTd" class="text_10b">$patientName ($pat_age)</td>
			</tr>
			<tr>
				<td colspan="$totalTd" height="5px" class="text_10b"></td>
			</tr>
			$pageContent1	
			<tr>
				<td colspan="$totalTd" bgcolor="#000000" height="1px"></td>
			</tr>
			<tr>
				<td colspan="$totalTd" class="text_10b">&nbsp;</td>
			</tr>			
DATA;
		
		//--- CSV DEATILS DATA ----	
		$url= $GLOBALS['rootdir']."/reports/set_session.php?patient=".$patient_id."&file_name=".$result_file_name;
		$csvPageContent .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td colspan="$totalTd" class="text_10b">$patientName ($pat_age)</td>
			</tr>
			$pageContent1
			<tr bgcolor="#FFFFFF"><td colspan="$totalTd">&nbsp;</td></tr>	
DATA;
	}
}
//$a=0;
for($a=$aging_start;$a<=$aging_to;$a++){
	$start = $a;
	$a = ($a > 0) ? ($a - 1) : $a;
	$end = ($a) + $aggingCycle;
	$headerTd .= <<<DATA
		<td class="text_b_w" style="text-align:right; width:$totaltdWidth;">
			$start - $end
		</td>
DATA;
	
	//FOR CSV
	$arrTitles[]=$start." - ".$end;

	$a += $aggingCycle;
}
if($All_due == true){
	$headerTd .= <<<DATA
		<td class="text_b_w" style="text-align:right; width:$totaltdWidth;">181+</td>
DATA;
	$insDue = 0;
	if(count($grandTotalArr[181])>0){
		$insDue = array_sum($grandTotalArr[181]);
	}
	
	//FOR CSV
	$arrTitles[]="181+";
}

$firstGrpTitle=($grpby_block=='grpby_groups')? 'Group': 'Facility';

//FOR CSV
$arrFinal=array();
$arrFinal[]="A/R Aging - Insurance ".$summary_detail;
$arrFinal[]="Date (".$Start_date." - ".$End_date.")";
$arrFinal[]="Created by: ".$op_name." on ".$curDate;
fputcsv($fp,$arrFinal, ",","\"");			

$arrFinal=array();
$arrFinal[]=$firstGrpTitle;
$arrFinal[]="Patient Name-ID";
$arrFinal[]="DOB";
$arrFinal[]="DOS";
$arrFinal[]="Posted Date";
$arrFinal[]="Encounter ID";
foreach($arrTitles as $title){
	$arrFinal[]=$title;
}
$arrFinal[]="Balance";
if($inc_payments==1){
	$arrFinal[]="Payments";
}
if($inc_adjustments==1){
	$arrFinal[]="Adjustment";
}
$arrFinal[]="Insurance";
$arrFinal[]="Policy No.";
$arrFinal[]="Insurance Contact No.";
$arrFinal[]="Procedures";
if($accNotes){	
	$arrFinal[]="A/C Notes";
	$arrFinal[]="Next Follow Up Date";
}
if($inc_appt_detail==1){
	$arrFinal[]='Appointment Date Time';
	$arrFinal[]='Appointment Procedure';
}
fputcsv($fp,$arrFinal, ",","\"");	

$ins_com_id_arr = array_keys($insComIdArr);
$firstGrpTitle=($grpby_block=='grpby_groups')? 'Group': 'Facility';
	
foreach($mainInsIdArr as $firstgrpid => $arrInsData){
	$arrGroupTotal=array();

	if($grpby_block=='grpby_groups'){
		$firstGrpName=$arrAllGroups[$firstgrpid];
	}else{
		$firstGrpName=$fac_name_arr[$firstgrpid];
	}
		
	$csvPageContent.='<tr><td class="text_b_w" colspan="'.$totalTd.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
	$pageContent.='<tr><td class="text_b_w" colspan="'.$totalTd.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';

	//for($in=0;$in<count($ins_com_id_arr);$in++){
	foreach($arrInsData as $insuranceId =>$arrInsDet)
	{

		$insuranceName = $insComIdArr[$insuranceId];
		if (strlen($insuranceName) > 20) {
			$insuranceName = substr($insuranceName, 0, 20) . '...';
		}
		$contact_name = $insCompDetails[$insuranceId]['contact_name'];
		$phone = $insCompDetails[$insuranceId]['phone'];
		
		//--- INSURANCE COMPANY NAME ----
		$pageContent .= <<<DATA
				<tr>
					<td class="text_b_w" colspan="$cols2" style="">$insuranceName</td>
					<td class="text_b_w" colspan="$cols1" style="">Contact Name: $contact_name</td>
					<td class="text_b_w" colspan="$cols1" style="">Phone#: $phone</td>
				</tr>
DATA;
		
		$csvPageContent .= <<<DATA
				<tr>
					<td class="text_b_w" colspan="$cols2" style="">$insuranceName</td>
					<td class="text_b_w" colspan="$cols1" style="">Contact Name: $contact_name</td>
					<td class="text_b_w" colspan="$cols1" style="">Phone#: $phone</td>
				</tr>
DATA;

		
		//for($i=0;$i<count($patient_id_arr);$i++){
		foreach($arrInsDet as $patient_id => $encounterDataArr){

			$encounterIdArr = array();
			$patientName = $patientNameArr[$patient_id];
			$pat_age = $patient_age_arr[$patient_id];
			if(count($encounterDataArr) > 0){
				$encounterIdArr = array_keys($encounterDataArr);
				//---  ALL ENCOUNTERS OF A SINGLE PATIENT --------
				$pageContent1 = NULL;
				$PDFpageContent1 = NULL;
				$arrProcCode=array();
				//for($e=0;$e<count($encounterIdArr);$e++){
				foreach($encounterDataArr as $encounter_id => $encounterDetail){
					$arrProcCode=array();
					$arr=array();
					$data_of_service = NULL;
					//--- GET ALL AGGING REPORTS OF SINGLE ENCOUNTERS FOR A PATIENT ---
					$ecounter_data = NULL;
					$totalBalance = $payments=$adjustments=0;
					$posted_date = NULL;
					for($a=$aging_start;$a<=$aging_to;$a++){
						$start = $a;
						$a = $a > 0 ? $a - 1 : $a;
						//---- ENCOUNTER DATE OF SERVICE ------
						$dataArr = $encounterDetail[$start];
						if($dataArr[0]['date_of_service']){
							$data_of_service = $dataArr[0]['date_of_service'];
						}
						//---- ENCOUNTER DATE OF POSTED ------
						if($dataArr[0]['postedDate']){
							$posted_date = $dataArr[0]['postedDate'];
						}
						
						//--- GET ENCOUNTER DUE AMOUNT -----------
						$insuranceDue = 0;
						for($en=0;$en<count($dataArr);$en++){
							$insuranceDue += preg_replace('/,/','',$dataArr[$en]['insuranceDue']);
							
							if($dataArr[$en]['insuranceDue']>0){
								$procCode=$arrAllCPTCodes[$dataArr[$en]['procCode']];
								$arrProcCode[$procCode]= $procCode; 
							}

							//PAYMENTS
							$chgDetId=$dataArr[$en]['charge_list_detail_id'];
							if($arrPaymentAmt[$chgDetId]){
								$payments+=$arrPaymentAmt[$chgDetId];
								unset($arrPaymentAmt[$chgDetId]);
							}
							//ADJUSTMENTS
							if($arrAdjustmentAmt[$chgDetId] || $normalWriteOffAmt[$chgDetId]){
								$adjustments+=$arrAdjustmentAmt[$chgDetId]+$normalWriteOffAmt[$chgDetId];
								if($arrAdjustmentAmt[$chgDetId])unset($arrAdjustmentAmt[$chgDetId]);
								if($normalWriteOffAmt[$chgDetId])unset($normalWriteOffAmt[$chgDetId]);
							}							
						}
						
						$grandTotalArr[$start][] = $insuranceDue;

					
						
						//--- PATIENT ENCOUNTER BALANCE ----					
						$totalBalance += $insuranceDue;

						//--- NUMBER FORMAT -----
						$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
						$insuranceDue=(empty($insuranceDue)==true)? " " : $insuranceDue;
						$ecounter_data .= <<<DATA
							<td class="text_10" valign="top" style="text-align:right; width:$totaltdWidth">$insuranceDue</td>
DATA;
						//FOR CSV
						$arr[]=$insuranceDue;
						$a += $aggingCycle;
					}
					
					if($All_due == true){
						//---- ENCOUNTER DATE OF SERVICE ------
						$dataArr = $encounterDetail[181];
						if($dataArr[0]['date_of_service']){
							$data_of_service = $dataArr[0]['date_of_service'];
						}
						
						if($dataArr[0]['postedDate']){
							$posted_date = $dataArr[0]['postedDate'];
						}
						//--- GET ENCOUNTER DUE AMOUNT -----------
						$insuranceDue = 0;
						for($en=0;$en<count($dataArr);$en++){
							$insuranceDue += preg_replace('/,/','',$dataArr[$en]['insuranceDue']);
							
							if($dataArr[$en]['insuranceDue']>0){
								$procCode=$arrAllCPTCodes[$dataArr[$en]['procCode']];
								$arrProcCode[$procCode]= $procCode; 
							}

							//PAYMENTS
							$chgDetId=$dataArr[$en]['charge_list_detail_id'];
							if($arrPaymentAmt[$chgDetId]){
								$payments+=$arrPaymentAmt[$chgDetId];
								unset($arrPaymentAmt[$chgDetId]);
							}
							//ADJUSTMENTS
							if($arrAdjustmentAmt[$chgDetId] || $normalWriteOffAmt[$chgDetId]){
								$adjustments+=$arrAdjustmentAmt[$chgDetId]+$normalWriteOffAmt[$chgDetId];
								if($arrAdjustmentAmt[$chgDetId])unset($arrAdjustmentAmt[$chgDetId]);
								if($normalWriteOffAmt[$chgDetId])unset($normalWriteOffAmt[$chgDetId]);
							}	
						}
						
						$grandTotalArr[181][] = $insuranceDue;

		

						//--- PATIENT ENCOUNTER BALANCE ----					
						$totalBalance += $insuranceDue;
						
						//--- NUMBER FORMAT -----
						$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
						$ecounter_data .= <<<DATA
							<td class="text_10" valign="top" style="text-align:right; width:$totaltdWidth">$insuranceDue</td>
DATA;
						//FOR CSV
						$arr[]=$insuranceDue;
					}
					$procCode = implode(', ', $arrProcCode);
					$procCodepdf = wordwrap($procCode, 40, "<br>\n", true);
					
					$arrNote = $arrEncNotes[$encounter_id]['reminder_note'];
					$encPDFComments = wordwrap($arrNote, 40, "<br>\n", true);
					$arrDateAssinFor = $arrEncNotes[$encounter_id]['reminder_date_provider'];
					
					
					//PAYMENTS & ADJUSTMENTS
					$td_payments=$td_adjustments='';
					if($inc_payments==1){
						$total_ins_payments+=$payments;
						$arrGroupTotal['payments']+= $payments;
						$td_payments='<td class="text_10" valign="top" style="text-align:right; width:'.$totaltdWidth.'">'.$CLSReports->numberFormat($payments,2).'</td>';
						
					}
					if($inc_adjustments==1){
						$total_ins_adjustments+=$adjustments;
						$arrGroupTotal['adjustments']+= $adjustments;
						$td_adjustments='<td class="text_10" valign="top" style="text-align:right; width:'.$totaltdWidth.'">'.$CLSReports->numberFormat($adjustments,2).'</td>';
					}

					$acc_notes = ""; 
					$acc_Cols = 2;
					$acc_Cols= ($accNotes)? $acc_Cols+2: $acc_Cols;
					$acc_Cols= ($inc_appt_detail==1)? $acc_Cols+2: $acc_Cols;
					$acc_width = round(100/$acc_Cols, 2); 

					if($accNotes){	
						$acc_notes = "<td class='text_10'  valign='top' style='text-align:left; width:$acc_width%'>$encPDFComments</td>
						<td class='text_10' valign='top' style='text-align:left; width:$acc_width%'>$arrDateAssinFor</td>";
						
						$accHdr = "<td class='text_b_w' style='text-align:left; width:$acc_width%'>A/C Notes</td>
						<td class='text_b_w' style='text-align:left; width:$acc_width%'>Next Follow Up Date</td>";
						
					}
					
					//APPOINTMENT DETAILS
					$appt_details=$apptHdr='';
					$appt_date_time=$arrApptDetails[$encounter_id]['appt_date']." ".$arrApptDetails[$encounter_id]['appt_time'];
					$appt_procedure=$arrAllProcedures[$arrApptDetails[$encounter_id]['appt_procedure']];
					if($inc_appt_detail==1){
						$appt_details = "<td class='text_10'  valign='top' style='text-align:left;'>".$appt_date_time."</td>
						<td class='text_10' valign='top' style='text-align:left;'>".$appt_procedure."</td>";

						$apptHdr = "<td class='text_b_w' style='text-align:left; width:$acc_width%'>Appointent Date Time</td>
						<td class='text_b_w' style='text-align:left; width:$acc_width%'>Appointment Procedure</td>";
					}
					
					//--- NUMBER FORMAT -----
					$totalBalance = $CLSReports->numberFormat($totalBalance,2);
					$pageContent1 .= <<<DATA
						<tr bgcolor="#FFFFFF">
							<td class="text_10" valign="top" style="text-align:left; width:$totaltdWidth">$data_of_service - $encounter_id<br>($posted_date)</td>
							$ecounter_data
							<td class="text_10" valign="top" style="text-align:right; width:$totaltdWidth">$totalBalance</td>
							$td_payments
							$td_adjustments
						</tr>
						<tr>
							<td colspan="$totalTd" class="hide" data-id="$patient_id">
								<table class="rpt rpt_table rpt_table-bordered rpt_padding" >	
									<tr>
										<td class="text_b_w" style="text-align:left; width:$acc_width%">Policy No.</td>
										<td class="text_b_w" style="text-align:left; width:$acc_width%">Procedures</td>
										$accHdr
										$apptHdr
									</tr>
									<tr bgcolor="#FFFFFF">
										<td class="text_10" valign="top" style="text-align:left;">$arrEncInsPolicyNos[$encounter_id]</td>
										<td class="text_10" valign="top" style="text-align:left;">$procCodepdf</td>
										$acc_notes
										$appt_details
									</tr>
								</table>
							</td>
						</tr>
			
DATA;

					$PDFpageContent1 .= <<<DATA
						<tr>
							<td class="text_10" valign="top" style="text-align:left; width:$totaltdWidth">$data_of_service - $encounter_id<br> ($posted_date)</td>
							$ecounter_data
							<td class="text_10" valign="top" style="text-align:right; width:$totaltdWidth">$totalBalance</td>
							$td_payments
							$td_adjustments
						</tr>
						<tr>
							<td colspan="$totalTd">
								<table class="rpt rpt_table rpt_table-bordered rpt_padding">	
									<tr>
										<td class="text_b_w" style="text-align:left; width:$acc_width%">Policy No.</td>
										<td class="text_b_w" style="text-align:left; width:$acc_width%">Procedures</td>
										$accHdr
										$apptHdr
									</tr>
									<tr bgcolor="#FFFFFF">
										<td class="text_10" valign="top" style="text-align:left;">$arrEncInsPolicyNos[$encounter_id]</td>
										<td class="text_10" valign="top" style="text-align:left;">$procCodepdf</td>
										$acc_notes
										$appt_details
									</tr>
								</table>
							</td>
						</tr>					
DATA;
					//FOR CSV
					$arrPatientRows=array();
					$arrPatientRows[]=$firstGrpName;
					$arrPatientRows[]=$patientName;
					$arrPatientRows[]=$pat_age;
					$arrPatientRows[]=$data_of_service;
					$arrPatientRows[]=$posted_date;
					$arrPatientRows[]=$encounter_id;
					foreach($arr as $balAmt){
						$arrPatientRows[]=$balAmt;	
					}
					$arrPatientRows[]=$totalBalance;
					if($inc_payments==1){
						$arrPatientRows[]=$payments;
					}
					if($inc_adjustments==1){
						$arrPatientRows[]=$adjustments;
					}				
					$arrPatientRows[]=str_replace("\n", " ",$insuranceName);
					$arrPatientRows[]=str_replace("\n", " ",$arrEncInsPolicyNos[$encounter_id]);
					$arrPatientRows[]=$phone;
					$arrPatientRows[]=str_replace("\n", " ",$procCode);
					if($accNotes){	
						$arrPatientRows[]=str_replace("\n", " ",$arrNote);
						$arrPatientRows[]=$arrDateAssinFor;
					}
					if($inc_appt_detail==1){
						$arrPatientRows[]=$appt_date_time;
						$arrPatientRows[]=$appt_procedure;
					}
					fputcsv($fp,$arrPatientRows, ",","\"");
				}
			}
			
			
			//--- PDF DEATILS DATA ----
			$pageContent .= <<<DATA
				<tr>
					<td colspan="$totalTd" class="text_10b">$patientName ($pat_age)</td>
				</tr>
				<tr>
					<td colspan="$totalTd" class="text_10b" height="5px"></td>
				</tr>
				$PDFpageContent1	
				<tr>
					<td colspan="$totalTd" bgcolor="#000000" height="1px"></td>
				</tr>
				<tr>
					<td colspan="$totalTd" class="text_10b">&nbsp;</td>
				</tr>			
DATA;

			//--- CSV DEATILS DATA ----	
			$csvPageContent .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td colspan="$totalTd" class="text_10b" style="width:300px;"><a href="javascript:void(0);" onClick="loadRptPatient($patient_id)" class='text_10b_purpule'>$patientName - $pat_age</a><span class="pull-right" onclick="showDetails(this);" data-id="$patient_id"><a href="#">
					<span class="glyphicon glyphicon-chevron-down"></span></a></span></td>
				</tr>
				$pageContent1
				<tr bgcolor="#FFFFFF"><td colspan="$totalTd">&nbsp;</td></tr>	
DATA;
		}
	}
}

//--- CREATE DATA FOR HTML TO PDF FILE -----
if($pageContent){
	$arrTotDues=array();
	$arrTotDues[]="Total :";
	$arrTotDues[]="";
	$arrTotDues[]="";
	$arrTotDues[]="";
	$arrTotDues[]="";
	
	//-- GET OPERATOR NAME ----
	$op_name = strtoupper($_SESSION['authProviderName']);
	//--- GET HEADER LABELS -------	
	$totalBalance = 0;
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;
		
		$insDue = NULL;
		if(count($grandTotalArr[$start])>0){
			$insDue = array_sum($grandTotalArr[$start]);
		}

		$totalBalance += $insDue;	
		$insDue = $CLSReports->numberFormat($insDue,2);		
		
		$totalDueData .= <<<DATA
			<td class="text_10b" style="text-align:right;">
				$insDue
			</td>
DATA;

		//FOR CSV
		$arrTotDues[]=$insDue;

		$a += $aggingCycle;
	}
	
	//--- GET HEADER LBEL FOR AGGING 181+ -------- 
	if($All_due == true){
		$insDue = 0;
		if(count($grandTotalArr[181])>0){
			$insDue = array_sum($grandTotalArr[181]);
		}
		
		$totalBalance += $insDue;	
		$insDue = $CLSReports->numberFormat($insDue,2);		
		$totalDueData .= <<<DATA
			<td class="text_10b" style="text-align:right;">
				$insDue
			</td>
DATA;
		//FOR CSV
		$arrTotDues[]=$insDue;

	}
	
	//--- HEADER DATA -------
	$insProviderName = 'All Selected';
	if(trim($insuranceName) != ''){
		$insProviderName = $insComIdArr[$insuranceName];
	}

	//FOR CSV
	$totCols=$totalTd+2;
	
	if(trim($totalBalance) > 0){
		
		//--- CHANGE NUMBER FORMAT -----------
		$grandTotal = $totalBalance + $totalCollectionInsBalance;
		$balnaceAfterDeduct = $grandTotal - $totalOverPayment;	
		$totalCollectionInsBalance = $CLSReports->numberFormat($totalCollectionInsBalance,2,1);
		$totalBalance = $CLSReports->numberFormat($totalBalance,2);
		$grandTotal = $CLSReports->numberFormat($grandTotal,2);
		$totalOverPayment = $CLSReports->numberFormat($totalOverPayment,2,1);
		$balnaceAfterDeduct = $CLSReports->numberFormat($balnaceAfterDeduct,2);

		//TOTALING OF PAYMENTS AND ADJUSTMENTS
		$title_payments=$title_adjustment=$td_total_ins_payments=$td_total_other_payments=$td_grand_payments=$td_total_ins_adjustments=$td_total_other_adjustments=$td_grand_adjustments='';
		$td_blank_payments=$td_blank_adjustments='';
		if($inc_payments==1){
			$title_payments='<td class="text_b_w" style="text-align:right; width:'.$totaltdWidth.'">Payments</td>';
			$td_total_ins_payments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$CLSReports->numberFormat($total_ins_payments,2).'</td>';
			$td_grand_payments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$CLSReports->numberFormat($grandPayments,2).'</td>';
			$td_blank_payments='<td align="right" style="text-align:right;  width:'.$totaltdWidth.'" class="text_10b"></td>';
		}
		if($inc_adjustments==1){
			$title_adjustment='<td class="text_b_w" style="text-align:right; width:'.$totaltdWidth.'">Adjustment</td>';
			$td_total_ins_adjustments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$CLSReports->numberFormat($total_ins_adjustments,2).'</td>';
			$td_grand_adjustments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$CLSReports->numberFormat($grandAdjustments,2).'</td>';
			$td_blank_adjustments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b"></td>';
		}		
		
		//$totalTd1 = $totalTd + 1;
		$htmlPart='';
		if($DateRangeFor=='dot'){
			$htmlPart='
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">Credit Amount : </td>
				<td colspan="'.$btmcol.'" bgcolor="#FFFFFF"></td>
				<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$totalOverPayment.'</td>
				'.$td_blank_payments.'
				'.$td_blank_adjustments.'
			</tr>
			<tr><td class="total-row" colspan="'.$totalTd.'"></td></tr>
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">Deducting Credit from Grand total : </td>
				<td colspan="'.$btmcol.'" bgcolor="#FFFFFF"></td>
				<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$balnaceAfterDeduct.'</td>
				'.$td_blank_payments.'
				'.$td_blank_adjustments.'				
			</tr>
			<tr><td class="total-row" colspan="'.$totalTd.'"></td></tr>';

			//FOR CSV
			$arrCredits[]="Credit Amount:";
			for($i=0;$i<$totCols;$i++){
				$arrCredits[]="";
			}
			$arrCredits[]=$totalOverPayment;

			$arrCreditsDeduct[]="Deducting Credit from Grand total :";
			for($i=0;$i<$totCols;$i++){
				$arrCreditsDeduct[]="";
			}
			$arrCreditsDeduct[]=$balnaceAfterDeduct;
		}
		
		$pdfData .= <<<DATA
			$stylePDF
			<page backtop="12mm" backbottom="7mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center; width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
				<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%;" >
					<tr class="rpt_headers">
						<td class="rptbx1" style="width:33%">
							A/R Aging - Insurance $summary_detail
						</td>
						
						<td class="rptbx2" style="width:33%">
							Date ($Start_date - $End_date)
						</td>
						<td class="rptbx3" style="width:33%">
							Created by: $op_name on $curDate
						</td>
					</tr>
				</table>
				<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%;" >
					<tr>
						<td class="text_b_w" style="text-align:center; width:$totaltdWidth">Description</td>
						$headerTd
						<td class="text_b_w" style="text-align:right; width:$totaltdWidth">Balance</td>
						$title_payments
						$title_adjustment
					</tr>
				</table>
			</page_header>
			<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%;" >
				$pageContent
				<tr>
					<td class="total-row" colspan="$totalTd"></td>
				</tr>
				<tr>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Total: </td>
					$totalDueData
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$totalBalance</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Patients Under Collection: </td>
					<td colspan="$btmcol" bgcolor="#FFFFFF"></td>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$totalCollectionInsBalance</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Grand Total: </td>
					<td colspan="$btmcol" bgcolor="#FFFFFF"></td>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$grandTotal</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>
				$htmlPart
			</table>
			</page>
DATA;
		
		//--- CSV FILE DATA ----
		$csvFileContent .= <<<DATA
			$styleHTML
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
				<tr class="rpt_headers">
					<td class="rptbx1" width="350">
						A/R Aging - Insurance $summary_detail
					</td>
					
					<td class="rptbx2" width="350">
						Date ($Start_date - $End_date)
					</td>
					<td class="rptbx3" width="350">
						Created by: $op_name on $curDate
					</td>
				</tr>
			</table>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
				<tr>
					<td class="text_b_w" style="text-align:center; width:$totaltdWidth">Description</td>
					$headerTd
					<td class="text_b_w" style="text-align:right; width:$totaltdWidth">Balance</td>
					$title_payments
					$title_adjustment
				</tr>
				$csvPageContent
				<tr bgcolor="#FFFFFF"><td colspan="$totalTd">&nbsp;</td></tr>
				<tr>
					<td class="total-row" colspan="$totalTd"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" class="text_10b">Total : </td>
					$totalDueData
					<td style="text-align:right;" class="text_10b">$totalBalance</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Patients Under Collection : </td>
					<td colspan="$btmcol" class="text_10b"></td>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$totalCollectionInsBalance</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Grand Total : </td>
					<td colspan="$btmcol" class="text_10b"></td>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$grandTotal</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>
				$htmlPart
			</table>
DATA;

			//FOR CSV
			$arrTotDues[]=$totalBalance;
			if($inc_payments==1){
				$arrTotDues[]=$CLSReports->numberFormat($total_ins_payments,2);
			}
			if($inc_adjustments==1){
				$arrTotDues[]=$CLSReports->numberFormat($total_ins_adjustments,2);
			}
			fputcsv($fp,$arrTotDues, ",","\""); //TOTAL DUES
			//PAT UNDER COLLECTION
			$arrPatUnderCol=array();
			$arrPatUnderCol[]="Patients Under Collection :";
			for($i=0;$i< $totCols; $i++){
				$arrPatUnderCol[]="";
			}
			$arrPatUnderCol[]=$totalCollectionInsBalance;
			fputcsv($fp,$arrPatUnderCol, ",","\"");
			//GRAND TOTAL	
			$arrGrand=array();
			$arrGrand[]="Grand Total :";
			for($i=0;$i< $totCols; $i++){
				$arrGrand[]="";
			}
			$arrGrand[]=$grandTotal;
			if($inc_payments==1){
				$arrGrand[]=$CLSReports->numberFormat($total_ins_payments,2);
			}
			if($inc_adjustments==1){
				$arrGrand[]=$CLSReports->numberFormat($total_ins_adjustments,2);
			}			
			fputcsv($fp,$arrGrand, ",","\"");
			
			fputcsv($fp,$arrCredits, ",","\""); //CREDIT AMOUNT
			fputcsv($fp,$arrCreditsDeduct, ",","\""); //CREDIT DEDUCTING
	}
	fclose($fp);
	
	$finalContant = "<div id='html_data_div' style='overflow-y:auto'>$csvFileContent</div><br />";
	$finalContant .= "<div id='module_buttons' class='text-center ad_modal_footer'><button type=\"button\" class=\"btn btn-success\" onclick=\"opener.top.fmain.generate_pdf('l')\";>Print PDF</button>";
	if($summary_detail=='summary'){
		$finalContant .= "&nbsp;&nbsp;<button type=\"button\" class=\"btn btn-success\" onclick=\"getCSVData();\">Export CSV</button>";
	}else{
		$finalContant .= "&nbsp;&nbsp;<button type=\"button\" class=\"btn btn-success\" onclick=\"download_csv();\">Export CSV</button>";
	}
	$finalContant .= "</div><form name=\"csvDownloadForm\" id=\"csvDownloadForm\" action=\"".$GLOBALS['webroot']."/interface/reports/downloadFile.php\" method =\"post\" > 
	<input type=\"hidden\" name=\"csv_text\" id=\"csv_text\"><input type=\"hidden\" name=\"csv_file_name\" id=\"csv_file_name\" value=\"Insuarnce_AR.csv\" /></form>";
	$finalContant.="<form name=\"csvDirectDownloadForm\" id=\"csvDirectDownloadForm\" action=\"".$GLOBALS['webroot']."/interface/reports/downloadCSV.php\" method =\"post\">"; 
	$finalContant.="<input type=\"hidden\" name=\"file_format\" id=\"file_format\" value=\"csv\">";
	$finalContant.="<input type=\"hidden\" name=\"zipName\" id=\"zipName\" value=\"\">";
	$finalContant.="<input type=\"hidden\" name=\"file\" id=\"file\" value=\"".$csv_file_name."\" /></form>";
	
		$file = write_html($finalContant, "insurance_ar_aging.html");	
		$file_path = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$file);
		if($callFrom != 'scheduled'){
			echo '<div class="text-center alert alert-info">Result is populated in separate window</div>';
		}
		
		} else {
			if($callFrom != 'scheduled'){
				echo '<div class="text-center alert alert-info">No Record Found.</div>';
			}
		}
		
?>