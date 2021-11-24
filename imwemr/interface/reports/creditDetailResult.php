<?php
$page_header_val = '
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">Credit Report Detail</td>
					<td class="rptbx2" style="width:350px;">DOS From '.$Start_date.' To '.$End_date.'</td>
					<td class="rptbx3" style="width:350px;">Created by: '.$op_name.' on '.$curDate.'</td>
				</tr>
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">Selected Group:'.$groupSelected.'</td>
					<td class="rptbx2" style="width:350px;">Selected Facility: '.$practice_name.'</td>
					<td class="rptbx3" style="width:350px;">Physician: '.$physician_name.'&nbsp;&nbsp;&nbsp;&nbsp;Cr. Phy.: '.$crphysicianSelected.'</td>
				</tr>
		</table>';
		
//--- GET ALL POSTED RECORDS ----------
if(count($main_provider_arr)){ 
	$countRes=0;
	$totalAmount_arr=array();
	$overPayment_arr=array();
	$totalPayAmount_arr=array();
	for($i=0;$i<count($main_provider_arr);$i++){
		$provider_id=$main_provider_arr[$i];
		$overPaymentDataArr = array_keys($ovr_pay_arr[$provider_id]);
		$pdfData2 .='<tr>
						<td class="text_b_w" colspan="9" align="left">Physician Name : '.$provider_name_arr[$provider_id].'</td>
					</tr>';
		$csvFileData2 .='<tr>
						<td class="text_b_w" colspan="9" align="left">Physician Name : '.$provider_name_arr[$provider_id].'</td>
					</tr>';			
		$totalAmount_sub_arr=array();	
		$overPayment_sub_arr=array();	
		$totalPayAmount_sub_arr=array();		

		for($j=0;$j<count($overPaymentDataArr);$j++){
			$countRes++;
			$totalAmount= $amtPaid = $overPayment = 0;
			$cptCode='';			
			$arrCPTCode=array();
			
			$encounter_id = $overPaymentDataArr[$j];
			$encounterDataArr = $ovr_pay_arr[$provider_id][$encounter_id];
			$detailsArr=$encounterDataArr[0];
			
			$date_of_service = $detailsArr['date_of_service'];
			$posFacilityId = $detailsArr['facility_id'];
			$facility_name = $posFacilityArr[$posFacilityId];

			$pat_name_arr = array();
			$pat_name_arr['LAST_NAME'] = $detailsArr['lname'];
			$pat_name_arr['FIRST_NAME'] = $detailsArr['fname'];
			$pat_name_arr['MIDDLE_NAME'] = $detailsArr['mname'];			
			$patientName = changeNameFormat($pat_name_arr);
			$patientName .= " - ".$detailsArr['id'];
			
			for($k=0; $k<sizeof($encounterDataArr); $k++){
				$totalAmount+= $encounterDataArr[$k]['totalAmount'];
				$amtPaid+= $encounterDataArr[$k]['paidForProc'];
				if($encounterDataArr[$k]['overPaymentForProc']!='0' && $encounterDataArr[$k]['overPaymentForProc']!='0.00'){
					$overPayment+= $encounterDataArr[$k]['overPaymentForProc'];
					$arrCPTCode[] = $encounterDataArr[$k]['cpt4_code'];
				}
			}
			$cptCode=implode(',', $arrCPTCode);
			
			$totalAmount_sub_arr[]=$totalAmount;
			$overPayment_sub_arr[]=$overPayment;
			$totalPayAmount_sub_arr[]=$amtPaid;
			
			$totalAmount_arr[]=$totalAmount;
			$overPayment_arr[]=$overPayment;
			$totalPayAmount_arr[]=$amtPaid;
			
			//--- PDF FILE DATA ----
			$pdfData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="20" align="center">'.$countRes.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="190">'.$patientName.'</td>
						<td class="text_12" bgcolor="#FFFFFF" align="left" width="80">'.$date_of_service.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="80">'.$encounter_id.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" align="left">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" align="left">'.wordwrap($cptCode, 6, "<br>\n", true).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" style="text-align:right"> '.$currency.number_format($totalAmount,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" style="text-align:right"> '.$currency.number_format($amtPaid,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" style="text-align:right;"> '.$currency.number_format($overPayment,2).'</td>
					</tr>';
			$csvFileData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="20" align="center">'.$countRes.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="190">'.$patientName.'</td>
						<td class="text_12" bgcolor="#FFFFFF" align="left" width="118">'.$date_of_service.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118">'.$encounter_id.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" align="left">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" align="left">'.$cptCode.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" style="text-align:right"> '.$currency.number_format($totalAmount,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" style="text-align:right"> '.$currency.number_format($amtPaid,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="118" style="text-align:right;"> '.$currency.number_format($overPayment,2).'</td>
					</tr>';		
		}
			$pdfData2 .='
					<tr><td bgcolor="#FFFFFF" colspan="9"></td></tr>
					<tr><td bgcolor="#FFFFFF" colspan="6"></td><td style="height: 2px;padding: 0px;background: #009933;" colspan="3"></td></tr>	
					<tr>
						<td class="text_b" bgcolor="#FFFFFF" colspan="5"></td>
						<td class="text_b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($totalAmount_sub_arr),2).'</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($totalPayAmount_sub_arr),2).'</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right;"> '.$currency.number_format(array_sum($overPayment_sub_arr),2).'</td>
					</tr>
					<tr><td bgcolor="#FFFFFF" colspan="9"></td></tr>
					<tr><td bgcolor="#FFFFFF" colspan="6"></td><td style="height: 2px;padding: 0px;background: #009933;" colspan="3"></td></tr>	
					';
			$csvFileData2 .='
			<tr><td style="height: 2px;padding: 0px;background: #009933;" colspan="9"></td></tr>	
			<tr>
						<td class="text_12b" bgcolor="#FFFFFF" width="20"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="190"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="118"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="118"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="118"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="118" align="right">Sub Total:</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="118" style="text-align:right"> '.$currency.number_format(array_sum($totalAmount_sub_arr),2).'</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="118" style="text-align:right"> '.$currency.number_format(array_sum($totalPayAmount_sub_arr),2).'</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="118" style="text-align:right;"> '.$currency.number_format(array_sum($overPayment_sub_arr),2).'</td>
					</tr>
					<tr><td style="height: 2px;padding: 0px;background: #009933;" colspan="9"></td></tr>';		
	}
}

$pdfData = '
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
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">						
			<tr>
				<td class="text_b_w" width="20" align="center">S.No</td>
				<td class="text_b_w" width="190" align="center">Patient Name-ID</td>
				<td class="text_b_w" width="80" align="center">DOS</td>
				<td class="text_b_w" width="80" align="center">E.ID</td>
				<td class="text_b_w" width="118" align="center">POS</td>
				<td class="text_b_w" width="118" align="center">CPT Code</td>
				<td class="text_b_w" width="118" align="center">Total Charges</td>
				<td class="text_b_w" width="118" align="center">Total Payment</td>
				<td class="text_b_w" width="118" align="center">Credit Amount</td>
			</tr>
		</table>
	</page_header>
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
		'.$pdfData2.'
		<tr>
			<td class="text_12" bgcolor="#FFFFFF" colspan="5"></td>
			<td class="text_b" bgcolor="#FFFFFF" align="right">Final Total:</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($totalAmount_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($totalPayAmount_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right;"> '.$currency.number_format(array_sum($overPayment_arr),2).'</td>
		</tr>
		<tr><td bgcolor="#FFFFFF" colspan="9"></td></tr>
		<tr><td bgcolor="#FFFFFF" colspan="6"></td><td style="height: 1px;padding: 0px;background: #009933;" colspan="3"></td></tr>
		'.$footerTotalData.'
	</table>
	</page>';

$csvFileData = $page_header_val.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">					
		<tr>
			<td class="text_b_w" width="20" align="center">S.No</td>
			<td class="text_b_w" width="190" align="center">Patient Name-ID</td>
			<td class="text_b_w" width="118" align="center">DOS</td>
			<td class="text_b_w" width="118" align="center">E.ID</td>
			<td class="text_b_w" width="118" align="center">POS</td>
			<td class="text_b_w" width="118" align="center">CPT Code</td>
			<td class="text_b_w" width="118" align="center">Total Charges</td>
			<td class="text_b_w" width="118" align="center">Total Payment</td>
			<td class="text_b_w" width="118" align="center">Credit Amount</td>
		</tr>
		'.$csvFileData2.'
		<tr>
			<td class="text_12" bgcolor="#FFFFFF" width="20"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="190"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="118"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="118"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="118"></td>
			<td class="text_12b" bgcolor="#FFFFFF" width="118" align="right">Final Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="118" style="text-align:right;"> '.$currency.number_format(array_sum($totalAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="118" style="text-align:right;"> '.$currency.number_format(array_sum($totalPayAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="118" style="text-align:right;"> '.$currency.number_format(array_sum($overPayment_arr),2).'</td>
		</tr>'.
		$footerTotalData.'
	</table>';
?>