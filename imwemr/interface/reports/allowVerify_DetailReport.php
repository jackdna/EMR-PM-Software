<?php
$page_header_val = '
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%" bgcolor="#FFF3E8">
		<tr class="rpt_headers">
			<td align="left" class="rptbx1" width="350">Allowable Verify Detail Report </td>
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
						<td class="text_b_w" colspan="8" align="left">Physician Name : '.$provider_name_arr[$provider_id].'</td>
					</tr>';
		$csvFileData2 .='<tr>
						<td class="text_b_w" colspan="8" align="left">Physician Name : '.$provider_name_arr[$provider_id].'</td>
					</tr>';			
		$totalAmount_sub_arr=array();	
		$approvedAmt_sub_arr=array();	
		$totalCptAmount_sub_arr=array();		

		for($j=0;$j<count($overPaymentDataArr);$j++){
			$countRes++;
			$totalAmount= $cptAmt = $approvedAmt = 0;
			$pdfData2_part = $csvFileData2_part='';
			$firstProcAmt = $firstProcCPTAmt= $firstProcApprvAmt = $firstProcCPTCode='';
			
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
			$patientName .= " - ".$detailsArr['patient_id'];

			
			for($k=0; $k<sizeof($encounterDataArr); $k++){
				$totalAmount+= $encounterDataArr[$k]['totalAmount'];
				$cptAmt+= $encounterDataArr[$k]['cpt_fee'];
				$approvedAmt+= $encounterDataArr[$k]['approvedAmt'];
				$cptCode = $encounterDataArr[$k]['cpt4_code'];
				
				if($k==0){
					$firstProcAmt = $totalAmount;
					$firstProcCPTAmt= $cptAmt;
					$firstProcApprvAmt = $approvedAmt;
					$firstProcCPTCode= $cptCode;					
				}else{
					//--- PDF FILE DATA ----
					$pdfData2_part .='<tr>
								<td class="text_12" bgcolor="#FFFFFF" ></td>
								<td class="text_12" bgcolor="#FFFFFF" align="center" ></td>
								<td class="text_12" bgcolor="#FFFFFF" align="center"></td>
								<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
								<td class="text_12" bgcolor="#FFFFFF" align="center">'.$cptCode.'</td>
								<td class="text_12" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat($encounterDataArr[$k]['totalAmount'],2).'</td>
								<td class="text_12" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat($encounterDataArr[$k]['cpt_fee'],2).'</td>
								<td class="text_12" bgcolor="#FFFFFF" style="text-align:right;padding-right:20px;"> '.numberformat($encounterDataArr[$k]['approvedAmt'],2).'</td>
							</tr>';
					$csvFileData2_part .='<tr>
								<td class="text_12" bgcolor="#FFFFFF" width="200"></td>
								<td class="text_12" bgcolor="#FFFFFF" align="center" width="100"></td>
								<td class="text_12" bgcolor="#FFFFFF" width="70" align="center"></td>
								<td class="text_12" bgcolor="#FFFFFF" width="100" align="left"></td>
								<td class="text_12" bgcolor="#FFFFFF" width="110" align="center">'.$cptCode.'</td>
								<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.numberformat($encounterDataArr[$k]['totalAmount'],2).'</td>
								<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.numberformat($encounterDataArr[$k]['cpt_fee'],2).'</td>
								<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> '.numberformat($encounterDataArr[$k]['approvedAmt'],2).'</td>
							</tr>';					
				}
			}

			//--- PDF FILE DATA ----
			$pdfData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
						<td class="text_12" bgcolor="#FFFFFF" align="center" width="100">'.$date_of_service.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$encounter_id.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="150" align="left">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$firstProcCPTCode.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="133" style="text-align:right"> '.numberformat($firstProcAmt,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="133" style="text-align:right"> '.numberformat($firstProcCPTAmt,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="133" style="text-align:right;> '.numberformat($firstProcApprvAmt,2).'</td>
					</tr>'.$pdfData2_part;
			$csvFileData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
						<td class="text_12" bgcolor="#FFFFFF" align="center" width="100">'.$date_of_service.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="70" align="center">'.$encounter_id.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="110" align="center">'.$firstProcCPTCode.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.numberformat($firstProcAmt,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.numberformat($firstProcCPTAmt,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> '.numberformat($firstProcApprvAmt,2).'</td>
					</tr>'.$csvFileData2_part;	
			
			
			$totalAmount_sub_arr[]=$totalAmount;
			$totalCptAmount_sub_arr[]=$cptAmt;
			$approvedAmt_sub_arr[]=$approvedAmt;
			
			$totalAmount_arr[]=$totalAmount;
			$totalCptAmount_arr[]=$cptAmt;
			$approvedAmt_arr[]=$approvedAmt;
		}
			$pdfData2 .='
					<tr><td bgcolor="#FFFFFF" colspan="8"></td></tr>
					<tr>
						<td class="text_b" bgcolor="#FFFFFF" colspan="4"></td>
						<td class="text_b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($totalAmount_sub_arr),2).'</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($totalCptAmount_sub_arr),2).'</td>
						<td class="text_b" bgcolor="#FFFFFF" style="text-align:right;padding-right:20px;"> '.numberformat(array_sum($approvedAmt_sub_arr),2).'</td>
					</tr>
					<tr><td bgcolor="#FFFFFF" colspan="8"></td></tr>
					';
			$csvFileData2 .='
			<tr><td class="total-row" colspan="8"></td></tr>	
			<tr>
						<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
						<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Sub Total:</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.numberformat(array_sum($totalAmount_sub_arr),2).'</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.numberformat(array_sum($totalCptAmount_sub_arr),2).'</td>
						<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> '.numberformat(array_sum($approvedAmt_sub_arr),2).'</td>
					</tr>
					<tr><td class="total-row" colspan="8"></td></tr>';		
	}
}

$pdfData = '
	<page backtop="15mm" backbottom="5mm">
	<page_footer>
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%"">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>		
	<page_header>
		'.$page_header_val.'
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%" bgcolor="#FFF3E8">						
			<tr>
				<td class="text_b_w" width="200" style="text-align:center">Patient Name-ID</td>
				<td class="text_b_w" width="100" style="text-align:center">DOS</td>
				<td class="text_b_w" width="100" style="text-align:center">E.ID</td>
				<td class="text_b_w" width="150" style="text-align:center">POS</td>
				<td class="text_b_w" width="100" style="text-align:center">CPT Code</td>
				<td class="text_b_w" width="133" style="text-align:center">Charges</td>
				<td class="text_b_w" width="133" style="text-align:center">Fee Table</td>
				<td class="text_b_w" width="133" style="text-align:center">ERA Allowable</td>
			</tr>
		</table>
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">
		'.$pdfData2.'
		<tr>
			<td class="text_12" bgcolor="#FFFFFF" colspan="4"></td>
			<td class="text_b" bgcolor="#FFFFFF" align="right">Grand Total:</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($totalAmount_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right"> '.numberformat(array_sum($totalCptAmount_arr),2).'</td>
			<td class="text_b" bgcolor="#FFFFFF" style="text-align:right;padding-right:20px;"> '.numberformat(array_sum($approvedAmt_arr),2).'</td>
		</tr>
		<tr><td bgcolor="#FFFFFF" colspan="8"></td></tr>
		'.$footerTotalData.'
	</table>
	</page>';

$csvFileData = $page_header_val.'
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%" bgcolor="#FFF3E8">					
		<tr>
			<td class="text_b_w" width="180" style="text-align:center">Patient Name-ID</td>
			<td class="text_b_w" width="90" style="text-align:center">DOS</td>
			<td class="text_b_w" width="70" style="text-align:center">E.ID</td>
			<td class="text_b_w" width="90" style="text-align:center">POS</td>
			<td class="text_b_w" width="90" style="text-align:center">CPT Code</td>
			<td class="text_b_w" width="140" style="text-align:center">Charges</td>
			<td class="text_b_w" width="140" style="text-align:center">Fee Table</td>
			<td class="text_b_w" width="140" style="text-align:center">ERA Allowable</td>
		</tr>
		'.$csvFileData2.'
		<tr>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Grand Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.numberformat(array_sum($totalAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.numberformat(array_sum($totalCptAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:20px;"> '.numberformat(array_sum($approvedAmt_arr),2).'</td>
		</tr>'.
		$footerTotalData.'
	</table>';
?>