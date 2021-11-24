<?php
$dataExists=false;

$prePayDateLabel='DOT';
if($DateRangeFor=='dop'){
	$prePayDateLabel='DOP';
}

$arrPatPayTot=array();
$arrPostedTransCount=array();
$main_part=$content_part= $patient_html='';

$arrModuleTypes=array('posted'=>'Posted','cico'=>'CICO','pre_pay'=>'Pre-Payment');

if(sizeof($arrMainData)>0){
	$dataExists=true;
	$colspan= 11;

	foreach($arrMainData as $paid_mode => $paidModeData){
		
		foreach($paidModeData as $dot => $dateData){
			//POSTED and CI/CO and PRE-PAYMENTS
			foreach($arrModuleTypes as $key => $type_name){
				foreach($dateData[$key] as $eid => $grpDetail){
					$facid=$grpDetail['facility'];
					$facility_name = ($pay_location=='1')? $arr_sch_facilities[$facid] : $posFacilityArr[$facid];
					$arrModeTotal[$paid_mode]+=$grpDetail['payment'];
					$arrModeCount[$paid_mode]+=1;
					$arrModuleTotals[$key]+=$grpDetail['payment'];
					$eid=($key=='posted')? $eid: '';
					
					$main_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:6%">'.$dot.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:6%">'.$grpDetail['dop'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:6%">'.$eid.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:12%">'.$providerNameArr[$grpDetail['physician']].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:12%">'.$facility_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:12$">'.$providerNameArr[$grpDetail['operator']].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:15%">'.$grpDetail['pat_name'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:6%">'.$type_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:6%">'.ucfirst($grpDetail['paid_by']).'</td>					
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:8%">'.ucwords($paid_mode).'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:10%">'.$CLSReports->numberFormat($grpDetail['payment'],2,1).'</td>
					</tr>';			
				}
			}
		}
			
		//PAYMENT MODE TOTAL
		$main_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="10">'.$paid_mode.' Total :</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrModeTotal[$paid_mode],2,1).'&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';	
	}
	
	//TOTAL
	$all_total=array_sum($arrModeTotal);
	$main_part.=' 
	<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="10">Total :</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($all_total,2,1).'&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';			


	$main_header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">	
	<tr id="">
		<td class="text_b_w" style="width:6%; text-align:center;">DOT</td>
		<td class="text_b_w" style="width:6%; text-align:center;">DOP</td>
		<td class="text_b_w" style="width:6%; text-align:center;">Encounter Id</td>
		<td class="text_b_w" style="width:12%; text-align:center;">Physician</td>
		<td class="text_b_w" style="width:12%; text-align:center;">Facility</td>
		<td class="text_b_w" style="width:12%; text-align:center;">Operator</td>
		<td class="text_b_w" style="width:15%; text-align:left;">Patient Name-Id</td>
		<td class="text_b_w" style="width:6%; text-align:center;">Type</td>
		<td class="text_b_w" style="width:6%; text-align:center;">Paid By</td>
		<td class="text_b_w" style="width:8%; text-align:center;">Method</td>
		<td class="text_b_w" style="width:10%; text-align:center;">Payment</td>
	</tr>
	</table>';
	
	//PAGE HTML
 	$totalPostedCount=$arrPostedTransCount['cash']+$arrPostedTransCount['check']+$arrPostedTransCount['credit_card']+$arrPostedTransCount['eft']+$arrPostedTransCount['money_order']+$arrPostedTransCount['veep']+$arrPostedTransCount['other'];
	
	$patient_html .=
	$main_header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$main_part.'
	</table>';
		
	//MAIN PDF HTML
	$patient_html_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table class="text_b_w" style="width: 100%;">
			<tr>
				<td class="text_b_w" style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$main_header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$main_part.'
	</table>
	</page>';			
}

//DELETED RECORDS
$content_part= $deleted_html='';
$delDataExists=false;
if(sizeof($arrDelAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$colspan= 7;

	foreach($arrDelAmounts as $del_date => $grpData){
		//POSTED and CICO and PRE-PAYMENTS
		foreach($arrModuleTypes as $key => $type_name){
			foreach($grpData[$key] as $eid => $grpDetail){
				$delTotals[$key]+=$grpDetail['del_amount'];
				$eid=($key=='posted')? $eid: '';
				$arrDelModuleTotals[$key]+=$grpDetail['del_amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:10%">'.$del_date.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:10%">'.$grpDetail['entered_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:10%">'.$eid.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:20%">'.$providerNameArr[$grpDetail['del_opr']].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:25%">'.$grpDetail['pat_name'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:10%">'.$type_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:15%">'.$CLSReports->numberFormat($grpDetail['del_amount'],2,1).'&nbsp;</td>
				</tr>';			
			}
		}
	}	
	$content_part.=' 
	<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="6">Voided Total :</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($delTotals),2,1).'&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';

	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">		
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Voided Payments</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:10%; text-align:center;">Deleted Date</td>
		<td class="text_b_w" style="width:10%; text-align:center;">DOT</td>
		<td class="text_b_w" style="width:10%; text-align:center;">Enc. Id</td>
		<td class="text_b_w" style="width:20%; text-align:center;">Deleted By</td>
		<td class="text_b_w" style="width:25%; text-align:center;">Patient Name-Id</td>
		<td class="text_b_w" style="width:10%; text-align:center;">Type</td>
		<td class="text_b_w" style="width:15%; text-align:center;">Deleted Payment</td>
	</tr>
	</table>';

	//PAGE HTML
	$del_payments_html.=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$content_part.'
	</table>';
	
	//PDF HTML
	$del_payments_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table class="text_b_w" style="width: 100%;">
			<tr>
				<td class="text_b_w" style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$content_part.'
	</table>
	</page>';	
	
}


//PAYMENTS TOTAL
if($dataExists==true){
	$title= ($delDataExists==true) ? 'Payments Total' : 'Grand Total';

	$grandTot = $arrModeTotal['Cash'] + $arrModeTotal['Check'] + $arrModeTotal['Credit Card'] + $arrModeTotal['EFT'] + $arrModeTotal['Money Order'] + $arrModeTotal['VEEP'] + $arrModeTotal['Other'];
	
	$grandCCType='';

	//IF GRAND CC TYPE AMOUNTS EXIST
	if(sizeof($arrCCModeAmts)>0){
		$ccColspan= $colspan-2;
		$totCCType=0;
		
		$grandCCType.='<tr id=""><td class="text_10" colspan="9"></td></tr>';
		$grandCCType.='<tr id=""><td class="text_b_w" colspan="9">Credit Card Payments</td></tr>';
				
		foreach($arrCCModeAmts as $ccType=> $amt){
			$totCCType+=$amt;
			$grandCCType.='<tr>';
				$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
				$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($amt,2,1).'&nbsp;</td>';
				$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="7"></td>';
			$grandCCType.='</tr>';	
		}
		$grandCCType.='<tr>';
			$grandCCType.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Grand Total of CC Types&nbsp;:</td>';
			$grandCCType.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
			$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="7"></td>';
		$grandCCType.='</tr>';	

		$grandCCType.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="9"></td></tr>';
	}	
	
	//TOTAL COUNTS
	$totCount = $arrModeCount['Cash'] + $arrModeCount['Check'] + $arrModeCount['Credit Card'] + $arrModeCount['EFT'] + $arrModeCount['Money Order'] + $arrModeCount['VEEP'] + $arrModeCount['Other'];	
		
	//FINAL HTML
	$grand_html=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	<tr id=""><td class="text_b_w" colspan="9">'.$title.'</td></tr>
	<tr id="">
		<td class="text_b_w" style="text-align:center;width:20%;" ></td>
		<td class="text_b_w" style="text-align:right;width:10%;">Cash&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:10%;">Check&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:10%;">Credit Card&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:10%;">EFT&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:10%;">MO&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:10%;">VEEP&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:10%;">OTHER&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:10%;">Total&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="9"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrModeTotal['Cash'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrModeTotal['Check'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrModeTotal['Credit Card'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrModeTotal['EFT'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrModeTotal['Money Order'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrModeTotal['VEEP'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrModeTotal['Other'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($grandTot,2,1).'&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="9"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Total Transactions Count: </td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrModeCount['Cash'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrModeCount['Check'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrModeCount['Credit Card'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrModeCount['EFT'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrModeCount['Money Order'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrModeCount['VEEP'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrModeCount['Other'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$totCount.'&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="9"></td></tr>
	'.$grandCCType.'
	</table>';	

}

//IF DELETED EXIST THEN GRAND TOTAL
$grand_html1='';
if($delDataExists==true){
	$wCols=$grand_w_cols*2;
	$firstCol=$grand_first_col+$wCols;
	$lastCol=100 -($firstCol + $grand_w_cols).'%';
	$firstCol.='%';
	$grand_w_cols.='%';
	
	$totalCollected= $arrModuleTotals['posted'] + $arrModuleTotals['cico'] + $arrModuleTotals['pre_pay'];
	$totalDeleted= $arrDelModuleTotals['posted'] + $arrDelModuleTotals['cico'] + $arrDelModuleTotals['pre_pay'];
	$finalCollected= $totalCollected - $totalDeleted;
	
	//FINAL HTML
	$grand_html1=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	<tr id=""><td class="text_b_w" colspan="3">Grand Collection</td></tr>
	<tr id="">
		<td class="text_b_w" style="text-align:center;width:20%;"></td>
		<td class="text_b_w" style="text-align:right;width:10%">Payments&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:70%">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Posted Payments:</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrModuleTotals['posted'],2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">CI/CO Payments:</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrModuleTotals['cico'],2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Pre-Payments:</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrModuleTotals['pre_pay'],2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Total Collected:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totalCollected,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Voided Posted Payments:</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrDelModuleTotals['posted'],2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Voided CI/CO Payments:</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrDelModuleTotals['cico'],2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Voided Pre-Payments:</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrDelModuleTotals['pre_pay'],2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Total Voided:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totalDeleted,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Final Collected:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($finalCollected,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
	</table>';		
}

//MANUALLY APPLIED
$content_part= $applied_html='';
$totalCICOManuallyApplied=0;
$arrAppliedPatPayTot=array();
if(sizeof($arrManuallyData)>0){
	$colspan= 5;
	
	foreach($arrManuallyData as $dot => $dateData){
		foreach($arrModuleTypes as $key => $type_name){
			foreach($dateData[$key] as $id => $grpDetail){		
	
				$arrAppliedPatPayTot['applied_amt']+=$grpDetail['applied_amt'];

				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:10%">'.$dot.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:25%">'.$grpDetail['pat_name'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:10%">'.$type_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:20%">'.$CLSReports->numberFormat($grpDetail['applied_amt'],2,1).'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:35%"></td>
				</tr>';			
			}
		}
	}
	

	$totalCICOManuallyApplied=$arrAppliedPatPayTot['applied_amt'];
	
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">		
	<tr id=""><td class="text_b_w" colspan="5">Manually Applied Amounts</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:10%; text-align:center;">DOT</td>
		<td class="text_b_w" style="width:25%; text-align:left;">Patient Name-Id</td>
		<td class="text_b_w" style="width:10%; text-align:center;">Type</td>
		<td class="text_b_w" style="width:20%; text-align:right;">Applied Amount</td>
		<td class="text_b_w" style="width:35%;">&nbsp;</td>
	</tr>
	</table>';

	
	//HTML
	$manually_applied_html .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="5"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Manually Applied Amounts:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrAppliedPatPayTot['applied_amt'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="5"></td></tr>	
	</table>';
}



$page_content=
$patient_html.
$grand_html.
$del_payments_html.
$grand_html1.
$manually_applied_html
;

$pdf_content=
$patient_html_PDF.
$grand_html.
$del_payments_PDF.
$grand_html1.
$manually_applied_html
;
?>
