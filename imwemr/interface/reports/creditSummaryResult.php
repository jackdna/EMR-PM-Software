<?php
$page_header_val = '
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">Credit Report Summary</td>
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
						<td class="text_b_w" colspan="4" align="left">Physician Name : '.$provider_name_arr[$provider_id].'</td>
					</tr>';
		$csvFileData2 .='<tr>
						<td class="text_b_w" colspan="4" align="left">Physician Name : '.$provider_name_arr[$provider_id].'</td>
					</tr>';			
		$totalAmount_sub_arr=array();	
		$overPayment_sub_arr=array();	
		$totalPayAmount_sub_arr=array();		

		for($j=0;$j<count($overPaymentDataArr);$j++){
			$countRes++;
			$totalAmount= $amtPaid = $overPayment = 0;
			$cptCode='';			
			$arrCPTCode=array();
			
			$facility_id = $overPaymentDataArr[$j];
			$facilityDataArr = $ovr_pay_arr[$provider_id][$facility_id];
			$facility_name = $posFacilityArr[$facility_id];

			$totalAmount= $facilityDataArr['totalAmount'];
			$amtPaid= $facilityDataArr['paidForProc'];
			$overPayment= $facilityDataArr['overPaymentForProc'];

			$totalAmount_sub_arr[]=$totalAmount;
			$overPayment_sub_arr[]=$overPayment;
			$totalPayAmount_sub_arr[]=$amtPaid;
			
			$totalAmount_arr[]=$totalAmount;
			$overPayment_arr[]=$overPayment;
			$totalPayAmount_arr[]=$amtPaid;
			
			//--- PDF FILE DATA ----
			$pdfData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="262" align="left">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="255" style="text-align:right"> '.$currency.number_format($totalAmount,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="255" style="text-align:right"> '.$currency.number_format($amtPaid,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="262" style="text-align:right;"> '.$currency.number_format($overPayment,2).'</td>
					</tr>';
			$csvFileData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="262" align="left">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.$currency.number_format($totalAmount,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.$currency.number_format($amtPaid,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="262" style="text-align:right"> '.$currency.number_format($overPayment,2).'</td>
					</tr>';		
		}
			$pdfData2 .='
					<tr><td bgcolor="#FFFFFF" colspan="4"></td></tr>
					<tr>
						<td class="text_b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($totalAmount_sub_arr),2).'</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($totalPayAmount_sub_arr),2).'</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($overPayment_sub_arr),2).'</td>
					</tr>
					<tr><td bgcolor="#FFFFFF" colspan="4"></td></tr>
					';
			$csvFileData2 .='
			<tr><td style="height: 2px;padding: 0px;background: #009933;" colspan="4"></td></tr>	
			<tr>
						<td class="text_12b" bgcolor="#FFFFFF" width="262" align="right">Sub Total:</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.$currency.number_format(array_sum($totalAmount_sub_arr),2).'</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.$currency.number_format(array_sum($totalPayAmount_sub_arr),2).'</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="262" style="text-align:right"> '.$currency.number_format(array_sum($overPayment_sub_arr),2).'</td>
					</tr>
					<tr><td style="height: 2px;padding: 0px;background: #009933;" colspan="4"></td></tr>';
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
		<table class="rpt rpt_table rpt_table-bordered" width="100%" bgcolor="#FFF3E8">					
			<tr>
				<td class="text_b_w" width="262" style="text-align:center">Facility</td>
				<td class="text_b_w" width="255" style="text-align:center">Total Charges</td>
				<td class="text_b_w" width="255" style="text-align:center">Total Payment</td>
				<td class="text_b_w" width="262" style="text-align:center">Credit Amount</td>
			</tr>
		</table>
	</page_header>
	<table class="rpt rpt_table rpt_table-bordered" width="100%" bgcolor="#FFF3E8">
		'.$pdfData2.'
		<tr>
			<td class="text_b" bgcolor="#FFFFFF" align="right">Final Total:</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($totalAmount_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($totalPayAmount_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.$currency.number_format(array_sum($overPayment_arr),2).'</td>
		</tr>
		<tr><td bgcolor="#FFFFFF" colspan="4"></td></tr>
		'.$footerTotalData.'
	</table>
	</page>';

$csvFileData = $page_header_val.'
		<table class="rpt rpt_table rpt_table-bordered" width="100%" bgcolor="#FFF3E8">					
		<tr>
			<td class="text_b_w" width="262" style="text-align:right">Facility</td>
			<td class="text_b_w" width="263" style="text-align:right">Total Charges</td>
			<td class="text_b_w" width="263" style="text-align:right">Total Payment</td>
			<td class="text_b_w" width="262" style="text-align:right">Credit Amount</td>
		</tr>
		'.$csvFileData2.'
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" width="262" align="right">Final Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.$currency.number_format(array_sum($totalAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.$currency.number_format(array_sum($totalPayAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="262" style="text-align:right"> '.$currency.number_format(array_sum($overPayment_arr),2).'</td>
		</tr>'.
		$footerTotalData.'
	</table>';
?>