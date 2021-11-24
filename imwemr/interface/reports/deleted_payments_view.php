<?php
$dataExists=false;
$firstGroupTitle = 'Physician';
$subTotalTitle = 'Physician Total';
$naFirstGroupTitle='Physician';
$naSubTotalTitle='Physician Total';

//SET GLOBAL CURRENCY
$showCurrencySymbol = showCurrency();
if($groupBy=='operator'){
	$firstGroupTitle = 'Operator';
	$subTotalTitle = 'Operator Total';
	$naFirstGroupTitle='Operator';
	$naSubTotalTitle='Operator Total';
	
}else if($groupBy=='facility'){
	$firstGroupTitle = 'Facility';
	$subTotalTitle = 'Facility Total';
	$naFirstGroupTitle='Facility';
	$naSubTotalTitle='Facility Total';

}

//DELETED RECORDS
//POSTED RECORDS
$content_part= $deleted_html='';
$delDataExists=false;
if(sizeof($arrDelPostedAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	if($showFacCol){
		foreach($arrDelPostedAmounts as $grpId => $grpDataArr){
			$firstGrpTotal=array();
			$firstGroupName='';
			$firstGroupName = $providerNameArr[$grpId];
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="7">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';

			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal = array();

				$facilityName = $posFacilityArr[$facId];
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="7">&nbsp;Facility - '.$facilityName.'</td></tr>';
			
				foreach($grpData as $eid => $grpDetail){
					
					$rowTot=0;
					$pName = explode('~', $grpDetail['pat_name']);
					
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					
					$facGrpTotal['del_amount']+=	$grpDetail['del_amount'];
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$patient_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$eid.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$grpDetail['cpt'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$grpDetail['dos'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$grpDetail['delDate'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$grpDetail['opr'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpDetail['del_amount'],2).'&nbsp;</td>
					</tr>';
				}
		
				//FACILITY TOTAL
				$firstGrpTotal['del_amount']+=	$facGrpTotal['del_amount'];
			
				// SUB TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="7"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="6">Facility Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['del_amount'],2).'&nbsp;</td>
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="7"></td></tr>';
			}
			
			//PHYSICIAN TOTAL
			$arrPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
			
			// SUB TOTAL
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="7"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="6">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['del_amount'],2).'&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="7"></td></tr>';
		}
	}else{
		foreach($arrDelPostedAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="7">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
		
			foreach($grpData as $eid => $grpDetail){
				$pName = explode('~', $grpDetail['pat_name']);
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				
				$firstGrpTotal['del_amount']+=	$grpDetail['del_amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$eid.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$grpDetail['cpt'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$grpDetail['dos'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$grpDetail['delDate'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$grpDetail['opr'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpDetail['del_amount'],2).'&nbsp;</td>
				</tr>';			
			}
		
			$arrPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="7"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="6">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['del_amount'],2).'&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="7"></td></tr>';
		}
	}
	
	// TOTAL HEADER
	$header='';
	$delPostedBlockTotal = $arrPatPayTot['del_amount'];
	//PAGE HTML
	$del_posted_html .=
	$header.' 
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
	<tr id="heading_orange">
		<td style="width:210px; text-align:left;">&nbsp;Patient Name-Id</td>
		<td style="width:130px; text-align:center;">Encounter Id</td>
		<td style="width:130px; text-align:center;">Procedure</td>
		<td style="width:130px; text-align:center;">DOS</td>
		<td style="width:130px; text-align:center;">Delete Date </td>
		<td style="width:130px; text-align:center;">Delete by Opreator</td>
		<td style="width:130px;; text-align:right;">Deleted Amount</td>
	</tr>
		
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="7"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="6">Deleted Posted Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['del_amount'],2).'&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="7"></td></tr>	
	</table>';
}
$page_content = $del_posted_html;
?>
