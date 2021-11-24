<?php
$page_header_val = '
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">
		<tr class="rpt_headers">
			<td align="left" class="rptbx1" width="350">Allowable Verify Summary Report </td>
			<td align="left" class="rptbx2" width="350">From '.$Start_date.' To '.$End_date.'</td>
			<td align="left" class="rptbx3" width="350">Created by '.$op_name.' on '.$curDate.' </td>
		</tr>
		<tr class="rpt_headers">	
			<td align="left" class="rptbx1">Selected Group: '.$groupSelected.'</td>
			<td align="left" class="rptbx2">Selected Facility : '.$facilitySelected.'</td>
			<td align="left" class="rptbx3">Physician : '.$physicianSelected.'&nbsp;&nbsp;&nbsp;&nbsp;Cr. Phy.: '.$crphysicianSelected.'</td>
		</tr>
	</table>';


//--- CREATE HTML ----------
if(count($main_provider_arr)){ 
	$countRes=0;
	$totalAmount_arr=array();
	$overPayment_arr=array();
	$totalCptAmount_arr=array();
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
		$approvedAmt_sub_arr=array();	
		$totalCptAmount_sub_arr=array();		

		for($j=0;$j<count($overPaymentDataArr);$j++){
			$countRes++;
			$totalAmount= $cptAmt = $approvedAmt = 0;
			$pdfData2_part = $csvFileData2_part='';
			
			$facility_id = $overPaymentDataArr[$j];
			$facilityDataArr = $ovr_pay_arr[$provider_id][$facility_id];
			$facility_name = $posFacilityArr[$facility_id];

			$totalAmount= $facilityDataArr['totalAmount'];
			$cptAmt= $facilityDataArr['fee'];
			$approvedAmt= $facilityDataArr['approvedAmt'];

			//--- PDF FILE DATA ----
			$pdfData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="262" align="center">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.numberformat($totalAmount,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.numberformat($cptAmt,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="262" style="text-align:right;"> '.numberformat($approvedAmt,2).'</td>
					</tr>';
			$csvFileData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="262" align="center">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.numberformat($totalAmount,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.numberformat($cptAmt,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="262" style="text-align:right"> '.numberformat($approvedAmt,2).'</td>
					</tr>';					
			
			$totalAmount_sub_arr[]=$totalAmount;
			$totalCptAmount_sub_arr[]=$cptAmt;
			$approvedAmt_sub_arr[]=$approvedAmt;
			
			$totalAmount_arr[]=$totalAmount;
			$totalCptAmount_arr[]=$cptAmt;
			$approvedAmt_arr[]=$approvedAmt;
		}
			$pdfData2 .='
					<tr><td bgcolor="#FFFFFF" colspan="4"></td></tr>
					<tr>
						<td class="text_b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($totalAmount_sub_arr),2).'</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($totalCptAmount_sub_arr),2).'</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($approvedAmt_sub_arr),2).'</td>
					</tr>
					<tr><td bgcolor="#FFFFFF" colspan="4"></td></tr>
					';
			$csvFileData2 .='
			<tr><td class="total-row" colspan="4"></td></tr>	
			<tr>
						<td class="text_12b" bgcolor="#FFFFFF" width="262" align="right">Sub Total:</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.numberformat(array_sum($totalAmount_sub_arr),2).'</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.numberformat(array_sum($totalCptAmount_sub_arr),2).'</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="262" style="text-align:right"> '.numberformat(array_sum($approvedAmt_sub_arr),2).'</td>
					</tr>
					<tr><td class="total-row" colspan="4"></td></tr>';		
	}
}

$pdfData = '
	<page backtop="15mm" backbottom="5mm">
	<page_footer>
		<table class="rpt_table rpt rpt_table-bordered" width="1050">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>		
	<page_header>
		'.$page_header_val.'
		<table width="1050" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">						
			<tr>
				<td class="text_b_w" width="262" style="text-align:center">Facility</td>
				<td class="text_b_w" width="263" style="text-align:center">Charges</td>
				<td class="text_b_w" width="263" style="text-align:center">Fee Table</td>
				<td class="text_b_w" width="262" style="text-align:center">ERA Allowable</td>
			</tr>
		</table>
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered" width="1050">
		'.$pdfData2.'
		<tr>
			<td class="text_b" bgcolor="#FFFFFF" align="right">Grand Total:</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($totalAmount_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($totalCptAmount_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($approvedAmt_arr),2).'</td>
		</tr>
		<tr><td bgcolor="#FFFFFF" colspan="4"></td></tr>
		'.$footerTotalData.'
	</table>
	</page>';

$csvFileData = $page_header_val.'
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%" bgcolor="#FFF3E8">					
		<tr>
			<td class="text_b_w" width="262" style="text-align:center">Facility</td>
			<td class="text_b_w" width="263" style="text-align:center">Charges</td>
			<td class="text_b_w" width="263" style="text-align:center">Fee Table</td>
			<td class="text_b_w" width="262" style="text-align:center">ERA Allowable</td>
		</tr>
		'.$csvFileData2.'
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" width="262" align="right">Grand Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.numberformat(array_sum($totalAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="263" style="text-align:right"> '.numberformat(array_sum($totalCptAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="262" style="text-align:right"> '.numberformat(array_sum($approvedAmt_arr),2).'</td>
		</tr>'.
		$footerTotalData.'
	</table>';
?>