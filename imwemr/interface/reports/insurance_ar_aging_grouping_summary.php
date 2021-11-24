<?php
$printAllDue = false;
		$m = 0;
		//--- GET TOTAL COLUMN WIDTH ----
		$column = ceil(($aging_to - $aging_start) / $aggingCycle);
		if($All_due == true){
			$column++;
		}

//---- GET TOTAL WIDTH FOR AUTO GENERATE TD --------
switch($column){
	case '1':
		$width = floor(310/$column);
		$hdWidth = $width;
		$headerWidth = 238;
	break;
	case '2':
		$width = floor(395/$column);
		$hdWidth = $width + 10;		
		$headerWidth = 238;
	break;
	case '3':
		$width = floor(435/$column);
		$hdWidth = $width + 7;
		$headerWidth = 240;
	break;
	case '4':
		$width = floor(450/$column);
		$hdWidth = $width + 5;
		$headerWidth = 240;
	break;
	case '5':
		$width = floor(470/$column);
		$hdWidth = $width;
		$headerWidth = 241;
	break;
	case '6':
		$width = floor(470/$column);
		$hdWidth = $width;
		$headerWidth = 241;
	break;
	case '7':
		$width = floor(470/$column);
		$hdWidth = $width;
		$headerWidth = 244;
	break;
}
//$width.='px';
//--- TOTAL TD COLSPAN ---
$totalTd = 1 + $column;
if($inc_payments=='1'){	$totalTd++;	}
if($inc_adjustments=='1'){	$totalTd++;	}
//---- GET ALL DUE AMOUNT FOR SELF PAY INSURANCE COMPANY -------
$grandTotalArr = array();

if(count($selfInsIdArr) > 0){
	$self_ins_id_arr = array_keys($selfInsIdArr);
	$csvPageContent = $pageContent = NULL;
	for($i=0;$i<count($self_ins_id_arr);$i++){
		$ins_id = $self_ins_id_arr[$i];
		$ins_name = $selfInsIdArr[$ins_id];
		$total_ins_due = 0;
		$insuarnceDueData = NULL;
		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;
			$insDueAmt = 0;
			$insDataArr = $mainInsIdArr[$ins_id][$start];
			for($d=0;$d<count($insDataArr);$d++){
				$insDueAmt += preg_replace('/,/','',$insDataArr[$d]['insuranceDue']);
			}
			
			$grandTotalArr[$start][] = $insDueAmt;
			$total_ins_due += $insDueAmt;
			
			//--- NUMBER FORMAT ----------
			$insDueAmt = $CLSReports->numberFormat($insDueAmt,2);
			$insuarnceDueData .= <<<DATA
				<td style="text-align:right;" width="$width" class="text_10">$insDueAmt</td>
DATA;
			$a += $aggingCycle;
		}
		
		if($All_due == true){
			$insDataArr = $mainInsIdArr[$ins_id][181];
			for($d=0;$d<count($insDataArr);$d++){
				$insDueAmt += preg_replace('/,/','',$insDataArr[$d]['insuranceDue']);
			}
			
			$grandTotalArr[181][] = $insDueAmt;
			$total_ins_due += $insDueAmt;
			
			//--- NUMBER FORMAT ----------
			$insDueAmt = $CLSReports->numberFormat($insDueAmt,2);
			$insuarnceDueData .= <<<DATA
				<td style="text-align:right;" width="$width" class="text_10">$insDueAmt</td>
DATA;
		}
		
		
		$ins_name_pdf=wordwrap($ins_name, 15, "<br>", true);

		//--- NUMBER FORMAT ----		
		$total_ins_due = $CLSReports->numberFormat($total_ins_due,2);
		$totalTd1 = $totalTd + 1;
		$pageContent .= <<<DATA
			<tr>
				<td align="left" width="100" class="text_10">$ins_name_pdf</td>
				$insuarnceDueData
				<td align="right" width="$width" class="text_10">$total_ins_due</td>
			</tr>
			<tr>
				<td height="1px" bgcolor="#000000" colspan="$totalTd1"></td>
			</tr>
DATA;

		$csvPageContent .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td style="text-align:left;" class="text_10">$ins_name</td>
				$insuarnceDueData
				<td style="text-align:right;" class="text_10">$total_ins_due</td>
			</tr>
DATA;
	}
}

//---- GET ALL INSURANCE COMPANIES DATA -------
if(count($insComIdArr) > 0){
	$totalTd1 = $totalTd + 1;
	$firstGrpTitle=($grpby_block=='grpby_groups')? 'Group': 'Facility';
	
	foreach($mainInsIdArr as $firstgrpid => $arrInsData){
		$arrGroupTotal=array();

		if($grpby_block=='grpby_groups'){
			$firstGrpName=$arrAllGroups[$firstgrpid];
		}else{
			$firstGrpName=$fac_name_arr[$firstgrpid];
		}
			
		$csvPageContent.='<tr><td class="text_b_w" colspan="'.$totalTd1.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		$pageContent.='<tr><td class="text_b_w" colspan="'.$totalTd1.'">'.$firstGrpTitle.' : '.$firstGrpName.'</td></tr>';
		
		foreach($arrInsData as $ins_id =>$arrInsDet){	
	
			$ins_name = $insComIdArr[$ins_id];
			$total_ins_due = $payments=$adjustments=0;
			$insuarnceDueData = NULL;
			for($a=$aging_start;$a<=$aging_to;$a++){
				$start = $a;
				$a = $a > 0 ? $a - 1 : $a;
				$insDueAmt = 0;
				$insDataArr = $arrInsDet[$start];
				for($d=0;$d<count($insDataArr);$d++){
					$insDueAmt += preg_replace('/,/','',$insDataArr[$d]['insuranceDue']);

					$chgDetId=$insDataArr[$d]['charge_list_detail_id'];
					//PAYMENTS
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
				
				$grandTotalArr[$start][] = $insDueAmt;
				$arrGroupTotal[$start]+= $insDueAmt;
				$total_ins_due += $insDueAmt;
				
				//--- NUMBER FORMAT ----------
				$insDueAmt = $CLSReports->numberFormat($insDueAmt,2);
				$insuarnceDueData .='<td style="text-align:right;" width="'.$width.'" class="text_10">'.$insDueAmt.'</td>';
				$a+= $aggingCycle;
			}
		
			if($All_due == true){
				$insDataArr = $arrInsDet[181];
				$insDueAmt = 0;
				for($d=0;$d<count($insDataArr);$d++){
					$insDueAmt += preg_replace('/,/','',$insDataArr[$d]['insuranceDue']);
					
					$chgDetId=$insDataArr[$d]['charge_list_detail_id'];
					//PAYMENTS
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
				$grandTotalArr[181][] = $insDueAmt;
				$arrGroupTotal[181]+= $insDueAmt;
				$total_ins_due += $insDueAmt;
				
				//--- NUMBER FORMAT ----------
				$insDueAmt = $CLSReports->numberFormat($insDueAmt,2);
				$insuarnceDueData .='<td style="text-align:right;" width="'.$width.'" class="text_10">'.$insDueAmt.'</td>';
			}

			$ins_name2 = explode(" ",$ins_name);
			$ins_name3 = "";
			foreach($ins_name2 as $ins_val){  
					$val_len = strlen($ins_val);
					if($val_len>18){
					$loop_len = ceil($val_len/18);
					$lp_start = 0;
					for($lp_i=0;$lp_i<$loop_len;$lp_i++){ 
					$ins_name3.= substr($ins_val,$lp_start,18).' ';
						$lp_start+=18; 
					}
				}else{ 
					$ins_name3.=$ins_val.' ';
				}
			}
			if($ins_name3)$ins_name=$ins_name3; 
			
			//--- NUMBER FORMAT ----		
			$total_ins_due = $CLSReports->numberFormat($total_ins_due,2);
			$Tds = $totalTd-1;
			$Tds= ($inc_payments==1)? $Tds-1: $Tds;
			$Tds= ($inc_adjustments==1)? $Tds-1: $Tds;
			

			$ins_name_pdf=wordwrap($ins_name, 15, "<br>", true);


			//--- PDF DETAIL DATA FOR INSURANCE DUE ---
			$td_payments=$td_adjustments='';
			if($inc_payments==1){
				$total_ins_payments+=$payments;
				$arrGroupTotal['payments']+= $payments;
				$td_payments='<td style="text-align:right;" width="'.$width.'" class="text_10">'.$CLSReports->numberFormat($payments,2).'</td>';
			}
			if($inc_adjustments==1){
				$total_ins_adjustments+=$adjustments;
				$arrGroupTotal['adjustments']+= $adjustments;
				$td_adjustments='<td style="text-align:right;" width="'.$width.'" class="text_10">'.$CLSReports->numberFormat($adjustments,2).'</td>';
			}

			$pageContent.='
			<tr>
				<td colspan="'.$totalTd1.'">&nbsp;</td>
			</tr>
			<tr>
				<td align="left" width="80" class="text_10">'.$ins_name_pdf.'</td>
				'.$insuarnceDueData.'
				<td align="right" width="'.$width.'" class="text_10">'.$total_ins_due.'</td>
				'.$td_payments.'
				'.$td_adjustments.'
				</tr>
			<tr>
				<td height="1px" bgcolor="#000000" colspan="'.$totalTd1.'"></td>
			</tr>';

			//--- CSV DETAIL DATA FOR INSURANCE DUE ---
			$csvPageContent .='
				<tr bgcolor="#FFFFFF">
					<td style="text-align:left;" class="text_10">'.$ins_name.'</td>
					'.$insuarnceDueData.'
					<td style="text-align:right;" class="text_10">'.$total_ins_due.'</td>
					'.$td_payments.'
					'.$td_adjustments.'
				</tr>';
		}

		//GROUP TOTALS	
		$total_group=0;

		//FOR PAGE HTML
		$csvPageContent.='<tr><td class="total-row" colspan="'.$totalTd1.'"></td></tr>
		<tr bgcolor="#FFFFFF">
		<td style="text-align:right;" class="text_10b">'.$firstGrpTitle.' Total :</td>';
		
		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;

			$csvPageContent.='<td style="text-align:right;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotal[$start],2).'</td>';
			
			$total_group+=$arrGroupTotal[$start];
			$a+= $aggingCycle;
		}
		if($All_due == true){
			$csvPageContent.='<td style="text-align:right;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotal[181],2).'</td>';
			$total_group+=$arrGroupTotal[181];
		}
		$csvPageContent.='<td style="text-align:right;" class="text_10b">'.$CLSReports->numberFormat($total_group,2).'</td>';

		if($inc_payments==1){
			$csvPageContent.='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotal['payments'],2).'</td>';
		}
		if($inc_adjustments==1){
			$csvPageContent.='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotal['adjustments'],2).'</td>';
		}
		$csvPageContent.='</tr>
		<tr><td class="total-row" colspan="'.$totalTd1.'"></td></tr>';		

		//FOR PDF	
		$pageContent.='<tr><td class="total-row" colspan="'.$totalTd1.'"></td></tr>
		<tr bgcolor="#FFFFFF">
		<td style="text-align:right;" class="text_10b">'.$firstGrpTitle.' Total :</td>';
		
		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;

			$pageContent.='<td style="text-align:right;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotal[$start],2).'</td>';
			
			$a+= $aggingCycle;
		}
		if($All_due == true){
			$pageContent.='<td style="text-align:right;" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotal[181],2).'</td>';
		}
		$pageContent.='<td style="text-align:right;" class="text_10b">'.$CLSReports->numberFormat($total_group,2).'</td>';

		if($inc_payments==1){
			$pageContent.='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotal['payments'],2).'</td>';
		}
		if($inc_adjustments==1){
			$pageContent.='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($arrGroupTotal['adjustments'],2).'</td>';
		}
		$pageContent.='</tr>
		<tr><td class="total-row" colspan="'.$totalTd1.'"></td></tr>';		
	}
}


//--- CREATE DATA FOR HTML FILE -----
if($pageContent != ''){
	//-- GET OPERATOR NAME ----
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$patDueWdOutIns = $patientDueData = $totalDueData = NULL;
	
	$grand_total_arr = array();
	//--- GET HEADER LABELS -------		
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;
		$headerTd .= <<<DATA
			<td class="text_b_w" width="$hdWidth" style="text-align:right;">
				$start - $end
			</td>
DATA;
		
		$insDue = NULL;
		if(count($grandTotalArr[$start])>0){			
			$insDue = array_sum($grandTotalArr[$start]);
			// CHART	
			$chartFacility[$m][1] = $start."-".$end;
			$chartFacility[$m][2] = $insDue;			

		}
		$grand_total_arr[$start][] = $insDue;
		
		//--- GET PATIENT DUE AMOUNT WITHOUT INSURANCE CARRIER ---
		$pat_ins_due_amt = NULL;
		if(count($patBalArr[$a])>0){
			$pat_ins_due_amt = array_sum($patBalArr[$a]);
		}	
		$grand_total_arr[$start][] = $pat_ins_due_amt;	
		
		$pat_ins_due_amt = $CLSReports->numberFormat($pat_ins_due_amt,2);
		$patDueWdOutIns .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$pat_ins_due_amt
			</td>
DATA;
		
		$totalBalance += $insDue;	
		$insDue = $CLSReports->numberFormat($insDue,2);
		$totalDueData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$insDue
			</td>
DATA;
		//--- PATIENT DUE AMOUNT -----
		$patDue = NULL;		
		if(count($grandPatientDueArr[$start])>0){
			$patDue = array_sum($grandPatientDueArr[$start]);
			// CHART
			$chartFacility[$m][3]=$patDue;
		}
		
		$patientDueAmountArr[] = $patDue;
		$patDue = $CLSReports->numberFormat($patDue,2);
		
		$patientDueData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$patDue
			</td>
DATA;
		
		$a += $aggingCycle;
		$m++;
	}
	
	//--- GET DATA FOR AGGING 181+ -------- 
	if($All_due == true){
		$headerTd .= <<<DATA
			<td class="text_b_w" width="$width" style="text-align:right;">181+</td>
DATA;
		
		$insDue = NULL;
		// CHART
		$chartFacility[$m][1] = '181+';
		
		if(count($grandTotalArr[181])>0){
			$insDue = array_sum($grandTotalArr[181]);
			// CHART
			$chartFacility[$m][2] = $insDue;	
		}
		$grand_total_arr[181][] = $insDue;

		$totalBalance += $insDue;	
		$insDue = $CLSReports->numberFormat($insDue,2);		
		$totalDueData .= <<<DATA
			<td class="text_10b" style="text-align:right;">
				$insDue
			</td>
DATA;
		
		//--- GET PATIENT DUE AMOUNT WITH OUT INSURANCE CARRIER ---
		$pat_ins_due_amt = NULL;
		if(count($patBalArr[181])>0){
			$pat_ins_due_amt = array_sum($patBalArr[181]);
		}
		$grand_total_arr[181][] = $pat_ins_due_amt;		
		
		$pat_ins_due_amt = $CLSReports->numberFormat($pat_ins_due_amt,2);
		$patDueWdOutIns .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$pat_ins_due_amt
			</td>
DATA;

		//--- PATIENT DUE AMOUNT -----
		$patDue = NULL;		
		if(count($grandPatientDueArr[181])>0){
			$patDue = array_sum($grandPatientDueArr[181]);
			
			// CHART
			$chartFacility[$m][3] = $patDue;	
		}
		$patientDueAmountArr[] = $patDue;
		$patDue = $CLSReports->numberFormat($patDue,2);
		
		$patientDueData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$patDue
			</td>
DATA;
	}
	
	//--- GET GRAND TOTAL AMOUNT ---
	$grandTotalKeys = array_keys($grand_total_arr);
	$grandTdTotalData = NULL;
	$totalGrandDueAmtArr = array();
	for($g=0;$g<count($grandTotalKeys);$g++){
		$keyid = $grandTotalKeys[$g];
		$grandTdTotal = NULL;
		if(count($grand_total_arr[$keyid])>0){
			$grandTdTotal = array_sum($grand_total_arr[$keyid]);
		}
		$totalGrandDueAmtArr[] = $grandTdTotal;
		$grandTdTotal = $CLSReports->numberFormat($grandTdTotal,2);
		$grandTdTotalData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$grandTdTotal
			</td>
DATA;
	}
	
	$totalGrandDueAmt = array_sum($totalGrandDueAmtArr);
	$grandTotal = $totalGrandDueAmt + $totalCollectionBalance; 
	$balnaceAfterDeduct = $grandTotal - $totalOverPayment;	
	$totalCollectionBalance = $CLSReports->numberFormat($totalCollectionBalance,2,1);
	$grandTotal = $CLSReports->numberFormat($grandTotal,2);
	$totalGrandDueAmt = $CLSReports->numberFormat($totalGrandDueAmt,2);
	$totalOverPayment = $CLSReports->numberFormat($totalOverPayment,2,1);
	$balnaceAfterDeduct = $CLSReports->numberFormat($balnaceAfterDeduct,2);

	//TOTALING OF PAYMENTS AND ADJUSTMENTS
	$total_other_payments=array_sum($arrPaymentAmt);
	$total_other_adjustments=array_sum($arrAdjustmentAmt);
	$grandPayments=	$total_ins_payments+$total_other_payments;
	$grandAdjustments=	$total_ins_adjustments+$total_other_adjustments;
	
	$td_total_ins_payments=$td_total_other_payments=$td_grand_payments=$td_total_ins_adjustments=$td_total_other_adjustments=$td_grand_adjustments='';
	$td_blank_payments=$td_blank_adjustments='';
	if($inc_payments==1){
		$td_total_ins_payments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($total_ins_payments,2).'</td>';
		$td_total_other_payments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($total_other_payments,2).'</td>';
		$td_grand_payments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($grandPayments,2).'</td>';
		$td_blank_payments='<td align="right" width="'.$width.'" class="text_10b"></td>';
	}
	if($inc_adjustments==1){
		$td_total_ins_adjustments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($total_ins_adjustments,2).'</td>';
		$td_total_other_adjustments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($total_other_adjustments,2).'</td>';
		$td_grand_adjustments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($grandAdjustments,2).'</td>';
		$td_blank_adjustments='<td align="right" width="'.$width.'" class="text_10b"></td>';
	}

	//--- HEADER DATA -------
	$insProviderName = 'All Selected';
	if(trim($insuranceName) != ''){
		$insProviderName = $insComIdArr[$insuranceName];
	}

	$patientDueAmount = array_sum($patientDueAmountArr);
	
	$patientDueAmount = $CLSReports->numberFormat($patientDueAmount,2);
	if(trim($totalBalance) > 0 || $patientDueAmount > 0){		
		//--- CHANGE NUMBER FORMAT -----------
		$totalBalance = $CLSReports->numberFormat($totalBalance,2);
		$createdWidth = $headerWidth;// + 200;
		//--- GET PDF DATA -----

		$htmlPart='';
		//if($DateRangeFor=='dot'){
			$htmlPart='
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right;" width="100" class="text_10b">Credit Amount :</td>
				<td bgcolor="#FFFFFF" colspan="'.$Tds.'"></td>
				<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$totalOverPayment.'</td>
				'.$td_blank_payments.'
				'.$td_blank_adjustments.'
			</tr>
			<tr><td class="total-row" colspan="'.$totalTd1.'"></td></tr>				
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right;" width="100" class="text_10b">Deducting Credit from Grand Total :</td>
				<td bgcolor="#FFFFFF" colspan="'.$Tds.'"></td>
				<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$balnaceAfterDeduct.'</td>
				'.$td_blank_payments.'
				'.$td_blank_adjustments.'
			</tr>
			<tr><td class="total-row" colspan="'.$totalTd1.'"></td></tr>';
		//}
		
		$title_payments=$title_adjustment='';
		if($inc_payments==1){
			$title_payments='<td class="text_b_w" width="$width" align="right">Payments</td>';
		}
		if($inc_adjustments==1){
			$title_adjustment='<td class="text_b_w" width="$width" align="right">Adjustment</td>';
		}		

		$pdfData .= <<<DATA
			$stylePDF
			<page backtop="12mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="750">
					<tr class="rpt_headers">
						<td class="rptbx1" width="250">
							A/R Aging - Insurance $summary_detail
						</td>
						
						<td class="rptbx2" width="250">
							Date ($Start_date - $End_date)
						</td>
						<td class="rptbx3" width="250">
							Created by: $op_name on $curDate
						</td>
					</tr>
				</table>
				<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="750">
					<tr>
						<td class="text_b_w" width="100" align="center">Description</td>
						$headerTd
						<td class="text_b_w" width="$width" align="right">Balance</td>
						$title_payments
						$title_adjustment
					</tr>
				</table>
			</page_header>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="750">
				$pageContent
				<tr><td colspan="$totalTd1">&nbsp;</td></tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr>
					<td align="right" width="100" class="text_10b">Ins. due : </td>
					$totalDueData
					<td align="right" width="$width" class="text_10b">$totalBalance</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr>
					<td align="right" width="100" class="text_10b">Pt. due : </td>
					$patDueWdOutIns
					<td align="right" width="$width" class="text_10b">$totalPatBalAmt</td>
					$td_total_other_payments
					$td_total_other_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr>
					<td align="right" width="100" class="text_10b">Grand Total : </td>
					$grandTdTotalData
					<td align="right" width="$width" class="text_10b">$totalGrandDueAmt</td>
					$td_grand_payments
					$td_grand_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>	
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Patients Under Collection :</td>
					<td bgcolor="#FFFFFF" colspan="$Tds"></td>
					<td style="text-align:right;" width="$width" class="text_10b">$totalCollectionBalance</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd1"></td></tr>				
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Grand Total :</td>
					<td bgcolor="#FFFFFF" colspan="$Tds"></td>
					<td style="text-align:right;" width="$width" class="text_10b">$grandTotal</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd1"></td></tr>				
				$htmlPart
			</table>
			</page>
DATA;
	
		//--- GET CSV DATA -----
		$csvFileContent .= <<<DATA
			$styleHTML
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" >
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
					<td class="text_b_w" width="auto" style="text-align:center;">Description</td>
					$headerTd
					<td class="text_b_w" width="$width" style="text-align:right;">Balance</td>
					$title_payments
					$title_adjustment
				</tr>
				$csvPageContent
				<tr><td bgcolor="#FFFFFF" colspan="$totalTd1">&nbsp;</td></tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" class="text_10b">Ins. due : </td>
					$totalDueData
					<td style="text-align:right;" class="text_10b">$totalBalance</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Pt. due : </td>
					$patDueWdOutIns
					<td style="text-align:right;" width="$width" class="text_10b">$totalPatBalAmt</td>
					$td_total_other_payments
					$td_total_other_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Total : </td>
					$grandTdTotalData
					<td style="text-align:right;" width="$width" class="text_10b">$totalGrandDueAmt</td>
					$td_grand_payments
					$td_grand_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>				
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Patients Under Collection :</td>
					<td bgcolor="#FFFFFF" colspan="$Tds"></td>
					<td style="text-align:right;" width="$width" class="text_10b">$totalCollectionBalance</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd1"></td></tr>				
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Grand Total :</td>
					<td bgcolor="#FFFFFF" colspan="$Tds"></td>
					<td style="text-align:right;" width="$width" class="text_10b">$grandTotal</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>				
				$htmlPart
			</table>
DATA;
		}
		$finalContant = "<div id='html_data_div' style='overflow-y:auto'>$csvFileContent</div><br />";
		$finalContant .= "<div id='module_buttons' class='text-center ad_modal_footer'><button type=\"button\" class=\"btn btn-success\" onclick=\"opener.top.fmain.generate_pdf('p')\";>Print PDF</button>&nbsp;&nbsp;<button type=\"button\" class=\"btn btn-success\" onclick=\"getCSVData();\">Export CSV</button></div><form name=\"csvDownloadForm\" id=\"csvDownloadForm\" action=\"".$GLOBALS['webroot']."/interface/reports/downloadFile.php\" method =\"post\" > 
			<input type=\"hidden\" name=\"csv_text\" id=\"csv_text\">	
			<input type=\"hidden\" name=\"csv_file_name\" id=\"csv_file_name\" value=\"Insurance_AR.csv\" />
		</form>";
		$file = write_html($finalContant, "Insurance_ar_aging.html");	
		$file_path = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$file);
			echo '<div class="text-center alert alert-info">Result is populated in separate window</div>';	
	} else {
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
	
?>