<?php

$dataExists=false;
$firstGroupTitle = 'Physician';
$subTotalTitle = 'Physician Total';
$naFirstGroupTitle='Physician';
$naSubTotalTitle='Physician Total';

//SET GLOBAL CURRENCY
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

}else if($groupBy=='department'){
	$firstGroupTitle = 'Department';
	$subTotalTitle = 'Department Total';
}

$prePayDateLabel='DOT';
if($DateRangeFor=='dop'){
	$prePayDateLabel='DOP';
}

$colsRemoved= (empty($pay_method)==false) ? 4 : 0;

if($groupBy=='department'){
	$dataExists=true;
	$colspan= 14;
	$colspan-=$colsRemoved;
	$total_cols = 12 - $colsRemoved;

	if(empty($pay_method)==false){	
		$secColspan= 3 ;
	}else{
		$secColspan= $colspan - 3;
	}
	$last_col=0;
	if(empty($pay_method)==false){ 	
		$last_col = 30;	
		$total_cols=$total_cols-1;
	}
	
	$first_col = 16;
	$w_cols = (100 - ($first_col + $last_col)) /$total_cols;
	
	$gd_first_col = $first_col+($w_cols*2);
	$gd_col1 = $gd_first_col."%";
	$gd_col2 = $w_cols."%";
	$gd_col3 = $w_cols."%";
	$gd_col4 = $w_cols."%";
	$gd_col5 = $w_cols."%";
	$gd_col6 = $w_cols."%";
	$gd_col7 = ($w_cols*3)."%";
	$gd_col_opt= $last_col."%";
	
	$w_cols = $w_cols."%";
	$first_col = $first_col."%";
	$last_col = $last_col."%";	
	//-----------------------------

	foreach($arrDepartmentView as $phy_id => $phy_data){
		$phyGrpTotal=array();
		$firstGroupName = $providerNameArr[$phy_id];		
		$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">Physician - '.$firstGroupName.'</td></tr>';
		
		foreach($phy_data as $fac_id => $fac_data){

			$facilityName = ($pay_location=='1')? $arr_sch_facilities[$fac_id] : $posFacilityArr[$fac_id];
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">Facility - '.$facilityName.'</td></tr>';

			$facGrpTotal=array();
			$postedData=$fac_data['posted'];
			$cicoData=$fac_data['cico'];
			$prepaidData=$fac_data['pre_paid'];

			//POSTED DATA
			if(sizeof($postedData)>0){
				$postedGrpTotal=array();
				$content_part.='<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Posted Payments</td></tr>';
				
				foreach($postedData as $dept_id => $dept_data){
					$arr_dept_tot=array();
					$dept_name = $dept_name_arr[$dept_id];
					$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">Department - '.$dept_name.'</td></tr>';					
					
					foreach($dept_data as $eid => $grpDetail){
						$rowTot=0;
						$pName = explode('~', $grpDetail['pat_name']);
						
						$patient_name_arr = array();
						$patient_name_arr["LAST_NAME"] = $pName[3];
						$patient_name_arr["FIRST_NAME"] = $pName[1];
						$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
						$patient_name = changeNameFormat($patient_name_arr);
						$patient_name.= ' - '.$pName[0];
						$reference_nos=(sizeof($grpDetail['reference_no'])>0) ? $grpDetail['reference_no']:'';

						
						$arr_dept_tot['cash']+=	$grpDetail['cash'];
						$arr_dept_tot['check']+= $grpDetail['check'];
						$arr_dept_tot['credit_card']+=	$grpDetail['credit_card'];
						$arr_dept_tot['eft']+=	$grpDetail['eft'];
						$arr_dept_tot['money_order']+=	$grpDetail['money_order'];
						$arr_dept_tot['veep']+=	$grpDetail['veep'];
						$arr_dept_tot['other']+=	$grpDetail['other'];
						$arr_dept_tot['by_patient']+=	$grpDetail['byPatient'];
						$arr_dept_tot['by_insurance']+=	$grpDetail['byInsurance'];
						
						//TRANSACTION COUNTS
						if($grpDetail['cash']>0)$arrPostedTransCount['cash']+=1;
						if($grpDetail['check']>0)$arrPostedTransCount['check']+=1;
						if($grpDetail['credit_card']>0)$arrPostedTransCount['credit_card']+=1;
						if($grpDetail['money_order']>0)$arrPostedTransCount['money_order']+=1;
						if($grpDetail['eft']>0)$arrPostedTransCount['eft']+=1;
						if($grpDetail['veep']>0)$arrPostedTransCount['veep']+=1;
						if($grpDetail['other']>0)$arrPostedTransCount['other']+=1;

						//FOR GRAND TOTALS
						$arrPatPayTot['cash']+=	$grpDetail['cash'];
						$arrPatPayTot['check']+= $grpDetail['check'];
						$arrPatPayTot['credit_card']+=	$grpDetail['credit_card'];
						$arrPatPayTot['eft']+=	$grpDetail['eft'];
						$arrPatPayTot['money_order']+=	$grpDetail['money_order'];
						$arrPatPayTot['veep']+=	$grpDetail['veep'];
						$arrPatPayTot['other']+=	$grpDetail['other'];
						$arrPatPayTot['by_patient']+=	$grpDetail['byPatient'];
						$arrPatPayTot['by_insurance']+=	$grpDetail['byInsurance'];				
						
						$rowTot = $grpDetail['byPatient'] + $grpDetail['byInsurance'];
						
						$content_part .= '
						<tr>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
							<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
							if(empty($pay_method)==true || $pay_method=='cash')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='check')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='credit card')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='eft')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='money order')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='veep')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' &&$pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
							$content_part .='
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['byPatient'],2,1).'&nbsp;</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['byInsurance'],2,1).'&nbsp;</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
							if(empty($pay_method)==false)
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>';
							$content_part .='
						</tr>';							
					}

					$postedGrpTotal['cash']+=	$arr_dept_tot['cash'];
					$postedGrpTotal['check']+= $arr_dept_tot['check'];
					$postedGrpTotal['credit_card']+=	$arr_dept_tot['credit_card'];
					$postedGrpTotal['eft']+=	$arr_dept_tot['eft'];
					$postedGrpTotal['money_order']+=	$arr_dept_tot['money_order'];
					$postedGrpTotal['veep']+=	$arr_dept_tot['veep'];
					$postedGrpTotal['other']+=	$arr_dept_tot['other'];
					$postedGrpTotal['by_patient']+=	$arr_dept_tot['by_patient'];
					$postedGrpTotal['by_insurance']+=	$arr_dept_tot['by_insurance'];

					//DEPARTMENT TOTAL
					$subTot = $arr_dept_tot['by_patient'] + $arr_dept_tot['by_insurance'];
					$content_part.=' 
					<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
					<tr>
						<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Department Total :</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['cash'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['check'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')
						$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['credit_card'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')
						$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['eft'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')
						$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['money_order'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')
						$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['veep'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
						$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['other'],2,1).'&nbsp;</td>';
						$content_part.='
						<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['by_patient'],2,1).'&nbsp;</td>
						<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['by_insurance'],2,1).'&nbsp;</td>
						<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
						if(empty($pay_method)==false)
						$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
						$content_part.='
					</tr>
					<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
					';					
				}

				$facGrpTotal['cash']+=	$postedGrpTotal['cash'];
				$facGrpTotal['check']+= $postedGrpTotal['check'];
				$facGrpTotal['credit_card']+=	$postedGrpTotal['credit_card'];
				$facGrpTotal['eft']+=	$postedGrpTotal['eft'];
				$facGrpTotal['money_order']+=	$postedGrpTotal['money_order'];
				$facGrpTotal['veep']+=	$postedGrpTotal['veep'];
				$facGrpTotal['other']+=	$postedGrpTotal['other'];
				$facGrpTotal['by_patient']+=	$postedGrpTotal['by_patient'];
				$facGrpTotal['by_insurance']+=	$postedGrpTotal['by_insurance'];

				//POSTED TOTAL
				$subTot = $postedGrpTotal['by_patient'] + $postedGrpTotal['by_insurance'];
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Posted Total :</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['other'],2,1).'&nbsp;</td>';	
					$content_part.='
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['by_patient'],2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedGrpTotal['by_insurance'],2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part.='
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				';					
			}
			unset($postedData);
			
			//CI/CO
			if(sizeof($cicoData)>0){
				$cicoGrpTotal=array();
				$content_part.='<tr id=""><td class="text_b_w" colspan="'.$colspan.'">CI/CO Payments</td></tr>';
					
				foreach($cicoData as $eid => $grpDetail){
					$rowTot=0;
					$pName = explode('~', $grpDetail['pat_name']);
					
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					$reference_nos=($grpDetail['reference_no']!='')?$grpDetail['reference_no']:'';
					
					$cicoGrpTotal['cash']+= $grpDetail['cash'];
					$cicoGrpTotal['check']+= $grpDetail['check'];
					$cicoGrpTotal['credit_card']+=	$grpDetail['credit_card'];
					$cicoGrpTotal['eft']+=	$grpDetail['eft'];
					$cicoGrpTotal['veep']+=	$grpDetail['veep'];
					$cicoGrpTotal['other']+=	$grpDetail['other'];
					$cicoGrpTotal['money_order']+=	$grpDetail['money_order'];
	
					//TRANSACTION COUNTS
					if($grpDetail['cash']>0)$arrCICOTransCount['cash']+=1;
					if($grpDetail['check']>0)$arrCICOTransCount['check']+=1;
					if($grpDetail['credit_card']>0)$arrCICOTransCount['credit_card']+=1;
					if($grpDetail['money_order']>0)$arrCICOTransCount['money_order']+=1;
					if($grpDetail['eft']>0)$arrCICOTransCount['eft']+=1;
					if($grpDetail['veep']>0)$arrCICOTransCount['veep']+=1;
					if($grpDetail['other']>0)$arrCICOTransCount['other']+=1;

					$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'] + $grpDetail['other'];

					//FOR GRAND TOTALS
					$arrCICOPayTot['cash']+=	$grpDetail['cash'];
					$arrCICOPayTot['check']+= $grpDetail['check'];
					$arrCICOPayTot['credit_card']+=	$grpDetail['credit_card'];
					$arrCICOPayTot['eft']+=	$grpDetail['eft'];
					$arrCICOPayTot['money_order']+=	$grpDetail['money_order'];
					$arrCICOPayTot['veep']+=	$grpDetail['veep'];
					$arrCICOPayTot['other']+=	$grpDetail['other'];
					$arrCICOPayTot['by_patient']+=	$rowTot;					

					$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
					
					//POPUP TITLES
					$cash_title=($grpDetail['is_ref']=='cash' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$check_title=($grpDetail['is_ref']=='check' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$cc_title=($grpDetail['is_ref']=='credit_card' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$eft_title=($grpDetail['is_ref']=='eft' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$mo_title=($grpDetail['is_ref']=='money_order' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$veep_title=($grpDetail['is_ref']=='veep' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$other_title=($grpDetail['is_ref']=='other' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
						<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cash_title.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$check_title.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cc_title.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$eft_title.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$mo_title.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$veep_title.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$other_title.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
	
						$content_part.='
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
						if(empty($pay_method)==false)
						$content_part.='
						<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'"></td>';
						$content_part.='
					</tr>';	
				}				

				$subTot = $cicoGrpTotal['cash']+$cicoGrpTotal['check']+$cicoGrpTotal['credit_card']+$cicoGrpTotal['eft']+$cicoGrpTotal['veep']+$cicoGrpTotal['money_order']+$cicoGrpTotal['other'];
				
				$facGrpTotal['cash']+= $cicoGrpTotal['cash'];
				$facGrpTotal['check']+= $cicoGrpTotal['check'];
				$facGrpTotal['credit_card']+=	$cicoGrpTotal['credit_card'];
				$facGrpTotal['eft']+=	$cicoGrpTotal['eft'];
				$facGrpTotal['veep']+=	$cicoGrpTotal['veep'];
				$facGrpTotal['other']+=	$cicoGrpTotal['other'];
				$facGrpTotal['money_order']+=	$cicoGrpTotal['money_order'];
				$facGrpTotal['by_patient']+=	$subTot;

				//CICO TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">CI/CO Total :</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoGrpTotal['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoGrpTotal['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoGrpTotal['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoGrpTotal['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoGrpTotal['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoGrpTotal['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoGrpTotal['other'],2,1).'&nbsp;</td>';
					$content_part.='
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part.='
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				';					
			}

			//PRE-PAYMENT
			if(sizeof($prepaidData)>0){
				$prepaidGrpTotal=array();
				$content_part.='<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Pre-Payments</td></tr>';
				
				foreach($prepaidData as $id => $grpDetail){
					$rowTot=0;
					$pName = explode('~', $grpDetail['pat_name']);
					
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					$reference_nos=($grpDetail['reference_no']!='')?$grpDetail['reference_no']:'';
					
					$prepaidGrpTotal['cash']+= $grpDetail['cash'];
					$prepaidGrpTotal['check']+= $grpDetail['check'];
					$prepaidGrpTotal['credit_card']+=	$grpDetail['credit_card'];
					$prepaidGrpTotal['eft']+=	$grpDetail['eft'];
					$prepaidGrpTotal['money_order']+=	$grpDetail['money_order'];
					$prepaidGrpTotal['veep']+=	$grpDetail['veep'];
					$prepaidGrpTotal['other']+=	$grpDetail['other'];
	
					//TRANSACTION COUNTS
					if($grpDetail['cash']>0)$arrPrePayTransCount['cash']+=1;
					if($grpDetail['check']>0)$arrPrePayTransCount['check']+=1;
					if($grpDetail['credit_card']>0)$arrPrePayTransCount['credit_card']+=1;
					if($grpDetail['money_order']>0)$arrPrePayTransCount['money_order']+=1;
					if($grpDetail['eft']>0)$arrPrePayTransCount['eft']+=1;
					if($grpDetail['veep']>0)$arrPrePayTransCount['veep']+=1;
					if($grpDetail['other']>0)$arrPrePayTransCount['other']+=1;

					$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'] + $grpDetail['other'];		
					
					//FOR GRAND TOTALS
					$arrPatPayTot['cash']+=	$grpDetail['cash'];
					$arrPatPayTot['check']+= $grpDetail['check'];
					$arrPatPayTot['credit_card']+=	$grpDetail['credit_card'];
					$arrPatPayTot['eft']+=	$grpDetail['eft'];
					$arrPatPayTot['money_order']+=	$grpDetail['money_order'];
					$arrPatPayTot['veep']+=	$grpDetail['veep'];
					$arrPatPayTot['other']+=	$grpDetail['other'];
					$arrPatPayTot['by_patient']+=	$rowTot;

					$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
	
					//POPUP TITLES
					$cash_title=($grpDetail['is_ref']=='cash')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$check_title=($grpDetail['is_ref']=='check')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$cc_title=($grpDetail['is_ref']=='credit_card')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$eft_title=($grpDetail['is_ref']=='eft')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$mo_title=($grpDetail['is_ref']=='money_order')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$veep_title=($grpDetail['is_ref']=='veep')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$other_title=($grpDetail['is_ref']=='other')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
						<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cash_title.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$check_title.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cc_title.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$eft_title.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$mo_title.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$veep_title.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$other_title.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
						$content_part .='
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
						if(empty($pay_method)==false)
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'"></td>';
						$content_part .='
					</tr>';		
				}
				
				$subTot = $prepaidGrpTotal['cash']+$prepaidGrpTotal['check']+$prepaidGrpTotal['credit_card']+$prepaidGrpTotal['eft']+$prepaidGrpTotal['veep']+$prepaidGrpTotal['money_order'];

				$facGrpTotal['cash']+= $prepaidGrpTotal['cash'];
				$facGrpTotal['check']+= $prepaidGrpTotal['check'];
				$facGrpTotal['credit_card']+=	$prepaidGrpTotal['credit_card'];
				$facGrpTotal['eft']+=	$prepaidGrpTotal['eft'];
				$facGrpTotal['veep']+=	$prepaidGrpTotal['veep'];
				$facGrpTotal['other']+=	$prepaidGrpTotal['other'];
				$facGrpTotal['money_order']+=	$prepaidGrpTotal['money_order'];
				$facGrpTotal['by_patient']+=	$subTot;

				//PRE-PAYMENTS TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Pre-Payments Total :</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prepaidGrpTotal['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prepaidGrpTotal['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prepaidGrpTotal['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prepaidGrpTotal['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prepaidGrpTotal['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prepaidGrpTotal['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prepaidGrpTotal['other'],2,1).'&nbsp;</td>';
					$content_part.='
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part.='
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				';						
			}	

			$phyGrpTotal['cash']+=	$facGrpTotal['cash'];
			$phyGrpTotal['check']+= $facGrpTotal['check'];
			$phyGrpTotal['credit_card']+=	$facGrpTotal['credit_card'];
			$phyGrpTotal['eft']+=	$facGrpTotal['eft'];
			$phyGrpTotal['money_order']+=	$facGrpTotal['money_order'];
			$phyGrpTotal['veep']+=	$facGrpTotal['veep'];
			$phyGrpTotal['other']+=	$facGrpTotal['other'];
			$phyGrpTotal['by_patient']+=	$facGrpTotal['by_patient'];
			$phyGrpTotal['by_insurance']+=	$facGrpTotal['by_insurance'];	

			$subTot = $facGrpTotal['cash'] + $facGrpTotal['check'] + $facGrpTotal['credit_card'] + $facGrpTotal['eft'] + $facGrpTotal['money_order'] + $facGrpTotal['veep'] + $facGrpTotal['other'];			

			//FACILITY TOTALS
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Facility Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['other'],2,1).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['by_patient'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['by_insurance'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';					
		}

		$subTot=0;
		$subTot = $phyGrpTotal['cash'] + $phyGrpTotal['check'] + $phyGrpTotal['credit_card'] + $phyGrpTotal['eft'] + $phyGrpTotal['money_order'] + $phyGrpTotal['veep'] + $phyGrpTotal['other'];			
		
		//PHYSICIAN TOTAL
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Physician Total :</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['other'],2,1).'&nbsp;</td>';
			$content_part.='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['by_patient'],2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['by_insurance'],2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$content_part.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		';			
	}	

	// TOTAL
	// HEADER
	$header='
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Enc. Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOS</td>
		<td class="notInPDF text_b_w" style="width:'.$w_cols.'; text-align:center;">Reference No</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">CC&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">MO&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">VEEP&nbsp;</td>';
		if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
		$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">OTHER&nbsp;</td>';
		$header.='
		<td class="text_b_w" style="width:'.($w_cols*2).'%; text-align:center;" colspan="2">Payments By</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$header.='<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>';
		$header.='
	</tr>
	<tr id="">
		<td class="text_b_w" style="text-align:center;" colspan="'.$secColspan.'"></td>
		<td class="text_b_w" style="text-align:center;">Patient</td>
		<td class="text_b_w" style="text-align:center;">Insurance</td>
		<td class="text_b_w" style="text-align:right;"></td>';
		if(empty($pay_method)==false)
		$header .='<td class="text_b_w">&nbsp;</td>';
		$header.='
	</tr>
	</table>';

	//PAGE HTML
	$patient_html .=
	$header.' 
	<table style="width:100%" class="rpt_table rpt_table-bordered">
	'.$content_part.'
	</table>';
	
	//PDF HTML
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
	.$header.'
	</page_header>
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	</table>
	</page>';

	//CC TYPES FOR POSTED GRAND TOTALS
	if(sizeof($arrPostedCCTypeAmts)>0){
		$totCCType=0;
		foreach($arrPostedCCTypeAmts as $ccType=> $amt){
			$arrGrandCCTypes[$ccType]+=$amt;
		}
	}										
	//CC TYPES FOR CI/CO GRAND TOTALS
	if(sizeof($arrCIOCCTypeAmts)>0){
		$totCCType=0;
		foreach($arrCIOCCTypeAmts as $ccType=> $amt){
			$arrGrandCCTypes[$ccType]+=$amt;
		}
	}					
	//CC TYPES FOR PRE-PAYMENTS GRAND TOTALS
	if(sizeof($arrPrePayCCTypeAmts)>0){
		$totCCType=0;
		foreach($arrPrePayCCTypeAmts as $ccType=> $amt){
			$arrGrandCCTypes[$ccType]+=$amt;
		}
	}	
	
}else{
	// POSTED PAYMENTS
	$arrPatPayTot=array();
	$arrPostedTransCount=array();
	$content_part= $patient_html='';
	if(sizeof($arrPostedPay)>0){
		$dataExists=true;
		$colspan= 14;
		$colspan-=$colsRemoved;
		$total_cols = 12 - $colsRemoved;
	
		if(empty($pay_method)==false){	
			$secColspan= 3 ;
		}else{
			$secColspan= $colspan - 3;
		}
	
		$last_col=0;
		if(empty($pay_method)==false){ 	
			$last_col = 30;	
			$total_cols=$total_cols-1;
		}
		
		$first_col = 16;
		$w_cols = (100 - ($first_col + $last_col)) /$total_cols;
		
		$gd_first_col = $first_col+($w_cols*2);
		$gd_col1 = $gd_first_col."%";
		$gd_col2 = $w_cols."%";
		$gd_col3 = $w_cols."%";
		$gd_col4 = $w_cols."%";
		$gd_col5 = $w_cols."%";
		$gd_col6 = $w_cols."%";
		$gd_col7 = ($w_cols*3)."%";
		$gd_col_opt= $last_col."%";
		
		$w_cols = $w_cols."%";
		$first_col = $first_col."%";
		$last_col = $last_col."%";
	
	
		if($showFacCol || $pay_location=='1'){
			foreach($arrPostedPay as $grpId => $grpDataArr){
				$firstGrpTotal=array();
				$firstGroupName='';
				
				if($pay_location=='1'){
					$firstGroupName = $arr_sch_facilities[$grpId];
					$firstGroupTitle = 'Facility';
					$subTotalTitle = 'Facility Total';
					$naFirstGroupTitle='Facility';
					$naSubTotalTitle='Facility Total';
				}else{
					if($groupBy=='physician' || $groupBy=='operator'){
						$firstGroupName = $providerNameArr[$grpId];
					}else{
						$firstGroupName = $dept_name_arr[$grpId];
					}
				}
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
	
				foreach($grpDataArr as $facId => $grpData){
					$facGrpTotal = array();
					
					if($pay_location=='1'){
						$secGroupName = $providerNameArr[$facId];
						$secGroupTitle= ($groupBy=='operator') ? 'Operator' : 'Physician';
					}else{
						$secGroupName = ($pay_location=='1')? $arr_sch_facilities[$facId] : $posFacilityArr[$facId];
						$secGroupTitle= 'Facility';
					}
					$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$secGroupTitle.' - '.$secGroupName.'</td></tr>';
				
					foreach($grpData as $eid => $grpDetail){
						$rowTot=0;
						$pName = explode('~', $grpDetail['pat_name']);
						
						$patient_name_arr = array();
						$patient_name_arr["LAST_NAME"] = $pName[3];
						$patient_name_arr["FIRST_NAME"] = $pName[1];
						$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
						$patient_name = changeNameFormat($patient_name_arr);
						$patient_name.= ' - '.$pName[0];
						$reference_nos=(sizeof($grpDetail['reference_no'])>0)? implode(', ',$grpDetail['reference_no']):'';
						
						$facGrpTotal['cash']+=	$grpDetail['cash'];
						$facGrpTotal['check']+= $grpDetail['check'];
						$facGrpTotal['credit_card']+=	$grpDetail['credit_card'];
						$facGrpTotal['eft']+=	$grpDetail['eft'];
						$facGrpTotal['money_order']+=	$grpDetail['money_order'];
						$facGrpTotal['veep']+=	$grpDetail['veep'];
						$facGrpTotal['other']+=	$grpDetail['other'];
						$facGrpTotal['by_patient']+=$grpDetail['byPatient'];
						$facGrpTotal['by_insurance']+=$grpDetail['byInsurance'];
						
						//TRANSACTION COUNTS
						if($grpDetail['cash']>0)$arrPostedTransCount['cash']+=1;
						if($grpDetail['check']>0)$arrPostedTransCount['check']+=1;
						if($grpDetail['credit_card']>0)$arrPostedTransCount['credit_card']+=1;
						if($grpDetail['money_order']>0)$arrPostedTransCount['money_order']+=1;
						if($grpDetail['eft']>0)$arrPostedTransCount['eft']+=1;
						if($grpDetail['veep']>0)$arrPostedTransCount['veep']+=1;
						if($grpDetail['other']>0)$arrPostedTransCount['other']+=1;
			
			
						$rowTot = $grpDetail['byPatient'] + $grpDetail['byInsurance'];
						
						$content_part .= '
						<tr>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
							<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
							if(empty($pay_method)==true || $pay_method=='cash')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='check')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='credit card')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='eft')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='money order')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true || $pay_method=='veep')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
							if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
							$content_part .='
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['byPatient'],2,1).'&nbsp;</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['byInsurance'],2,1).'&nbsp;</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
							if(empty($pay_method)==false)
							$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>';
							$content_part .='
						</tr>';			
					}
			
				//FACILITY TOTAL
				$firstGrpTotal['cash']+=	$facGrpTotal['cash'];
				$firstGrpTotal['check']+= $facGrpTotal['check'];
				$firstGrpTotal['credit_card']+=	$facGrpTotal['credit_card'];
				$firstGrpTotal['eft']+=	$facGrpTotal['eft'];
				$firstGrpTotal['money_order']+=	$facGrpTotal['money_order'];
				$firstGrpTotal['veep']+=	$facGrpTotal['veep'];
				$firstGrpTotal['other']+=	$facGrpTotal['other'];
				$firstGrpTotal['by_patient']+=	$facGrpTotal['by_patient'];
				$firstGrpTotal['by_insurance']+=	$facGrpTotal['by_insurance'];
				
				// SUB TOTAL
				$subTot = $facGrpTotal['by_patient'] + $facGrpTotal['by_insurance'];
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$secGroupTitle.' Total :</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['other'],2,1).'&nbsp;</td>';
					$content_part.='
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['by_patient'],2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['by_insurance'],2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part.='
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				';
			}
			//PHYSICIAN TOTAL
			$arrPatPayTot['cash']+=	$firstGrpTotal['cash'];
			$arrPatPayTot['check']+= $firstGrpTotal['check'];
			$arrPatPayTot['credit_card']+=	$firstGrpTotal['credit_card'];
			$arrPatPayTot['eft']+=	$firstGrpTotal['eft'];
			$arrPatPayTot['money_order']+=	$firstGrpTotal['money_order'];
			$arrPatPayTot['veep']+=	$firstGrpTotal['veep'];
			$arrPatPayTot['other']+=	$firstGrpTotal['other'];
			$arrPatPayTot['by_patient']+=	$firstGrpTotal['by_patient'];
			$arrPatPayTot['by_insurance']+=	$firstGrpTotal['by_insurance'];
			
			// SUB TOTAL
			$subTot1 = $firstGrpTotal['by_patient'] + $firstGrpTotal['by_insurance'];
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$firstGroupTitle.' Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['other'],2,1).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['by_patient'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['by_insurance'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot1,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';
		}
		}else{
	
			foreach($arrPostedPay as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			
			if($groupBy=='facility'){
				$firstGroupName = ($pay_location=='1')? $arr_sch_facilities[$grpId] : $posFacilityArr[$grpId];
			}else if($groupBy=='physician' || $groupBy=='operator'){
				$firstGroupName = $providerNameArr[$grpId];
			}else{
				$firstGroupName = $dept_name_arr[$grpId];
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
		
			foreach($grpData as $eid => $grpDetail){
				$pName = explode('~', $grpDetail['pat_name']);
				
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				$reference_nos=(sizeof($grpDetail['reference_no'])>0)? implode(', ',$grpDetail['reference_no']):'';
				
				$firstGrpTotal['cash']+=	$grpDetail['cash'];
				$firstGrpTotal['check']+= $grpDetail['check'];
				$firstGrpTotal['credit_card']+=	$grpDetail['credit_card'];
				$firstGrpTotal['eft']+=	$grpDetail['eft'];
				$firstGrpTotal['money_order']+=	$grpDetail['money_order'];
				$firstGrpTotal['veep']+=	$grpDetail['veep'];
				$firstGrpTotal['other']+=	$grpDetail['other'];
				$firstGrpTotal['by_patient']+=$grpDetail['byPatient'];
				$firstGrpTotal['by_insurance']+=$grpDetail['byInsurance'];
				
				//TRANSACTION COUNTS
				if($grpDetail['cash']>0)$arrPostedTransCount['cash']+=1;
				if($grpDetail['check']>0)$arrPostedTransCount['check']+=1;
				if($grpDetail['credit_card']>0)$arrPostedTransCount['credit_card']+=1;
				if($grpDetail['money_order']>0)$arrPostedTransCount['money_order']+=1;
				if($grpDetail['eft']>0)$arrPostedTransCount['eft']+=1;
				if($grpDetail['veep']>0)$arrPostedTransCount['veep']+=1;
				if($grpDetail['other']>0)$arrPostedTransCount['other']+=1;
				
				
				$rowTot = $grpDetail['byPatient'] + $grpDetail['byInsurance'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
					<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
					$content_part.='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['byPatient'],2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['byInsurance'],2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>';
					$content_part.='
				</tr>';			
			}
			
			
			$arrPatPayTot['cash']+=	$firstGrpTotal['cash'];
			$arrPatPayTot['check']+= $firstGrpTotal['check'];
			$arrPatPayTot['credit_card']+=	$firstGrpTotal['credit_card'];
			$arrPatPayTot['eft']+=	$firstGrpTotal['eft'];
			$arrPatPayTot['money_order']+=	$firstGrpTotal['money_order'];
			$arrPatPayTot['veep']+=	$firstGrpTotal['veep'];
			$arrPatPayTot['other']+=	$firstGrpTotal['other'];
			$arrPatPayTot['by_patient']+=	$firstGrpTotal['by_patient'];
			$arrPatPayTot['by_insurance']+=	$firstGrpTotal['by_insurance'];
			
			// SUB TOTAL
			$subTot = $firstGrpTotal['by_patient'] + $firstGrpTotal['by_insurance'];
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$firstGroupTitle.' Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['other'],2,1).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['by_patient'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['by_insurance'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';
		}
				
		}
		
		$postedBlockTot = $arrPatPayTot['by_patient'] + $arrPatPayTot['by_insurance'];
		
	
		// TOTAL
		// HEADER
		$header='
		<table style="width:100%" class="rpt_table rpt_table-bordered">	
		<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Posted Payments</td></tr>
		<tr id="">
			<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Enc. Id</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOS</td>
			<td class="notInPDF text_b_w" style="width:'.$w_cols.'; text-align:center;">Reference No</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Cash&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Check&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">CC&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">EFT&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">MO&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">VEEP&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">OTHER&nbsp;</td>';
			$header.='
			<td class="text_b_w" style="width:'.($w_cols*2).'%; text-align:center;" colspan="2">Payments By</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>';
			if(empty($pay_method)==false)
			$header.='<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>';
			$header.='
		</tr>
		<tr id="">
			<td class="text_b_w" style="text-align:center;" colspan="'.$secColspan.'"></td>
			<td class="text_b_w" style="text-align:center;">Patient</td>
			<td class="text_b_w" style="text-align:center;">Insurance</td>
			<td class="text_b_w" style="text-align:right;"></td>';
			if(empty($pay_method)==false)
			$header .='<td class="text_b_w">&nbsp;</td>';
			$header.='
		</tr>
		</table>';
	
		//CC TYPE AMOUNTS
		if(sizeof($arrPostedCCTypeAmts)>0){
			$ccColspan=11 - $colsRemoved;
			$totCCType=0;
			foreach($arrPostedCCTypeAmts as $ccType=> $amt){
				$totCCType+=$amt;
				$cctype_html.='<tr>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($amt,2,1).'&nbsp;</td>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
				$cctype_html.='</tr>';	
	
				$arrGrandCCTypes[$ccType]+=$amt;
			}
			$cctype_html.='<tr>';
				$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Total of CC Types&nbsp;:</td>';
				$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$cctype_html.='</tr>';	
	
			$cctype_html.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}
			
		//PAGE HTML
		$totalPostedCount=$arrPostedTransCount['cash']+$arrPostedTransCount['check']+$arrPostedTransCount['credit_card']+$arrPostedTransCount['eft']+$arrPostedTransCount['money_order']+$arrPostedTransCount['veep']+$arrPostedTransCount['other'];
		$patient_html .=
		$header.' 
		<table style="width:100%" class="rpt_table rpt_table-bordered">	
		'.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Posted Payments Total&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['other'],2,1).'&nbsp;</td>';
			$patient_html .='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['by_patient'],2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['by_insurance'],2,1).'&nbsp;</td>		
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$patient_html .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['cash'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['check'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['credit_card'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['eft'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['money_order'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['veep'].'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['other'].'&nbsp;</td>';
			$patient_html .='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['by_patient'].'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['by_insurance'].'&nbsp;</td>		
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$totalPostedCount.'&nbsp;</td>';
			if(empty($pay_method)==false)
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$patient_html .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		'.$cctype_html.'
		</table>';
		
		//PDF HTML
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
		.$header.'
		</page_header>
		<table style="width:100%" class="rpt_table rpt_table-bordered">	
		'.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Posted Payments Total&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['other'],2,1).'&nbsp;</td>';
			$patient_html_PDF.='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['by_patient'],2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['by_insurance'],2,1).'&nbsp;</td>		
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($postedBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$patient_html_PDF.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['cash'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['check'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['credit_card'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['eft'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['money_order'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['veep'].'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['other'].'&nbsp;</td>';
			$patient_html_PDF .='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['by_patient'].'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPostedTransCount['by_insurance'].'&nbsp;</td>		
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$totalPostedCount.'&nbsp;</td>';
			if(empty($pay_method)==false)
			$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$patient_html_PDF .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		'.$cctype_html.'
		</table>
		</page>';	
		

	}
	
	// UNAPPLIED CI/CO PAYMENTS
	$arrCICOPayTot=array();
	$arrCICOTransCount=array();
	$content_part= $cico_html = '';
	if(sizeof($arrCICONotApplied)>0){
		$dataExists=true;
		$colspan= 14;
		$colspan-=$colsRemoved;
		$total_cols = 12 - $colsRemoved;
	
		$last_col=0;
		if(empty($pay_method)==false){ 	
			$last_col = 30;	
			$total_cols=$total_cols-2;
		}
		
		$first_col = 16;
		$w_cols = (100 - ($first_col + $last_col)) /$total_cols;
		
		$gd_first_col = $first_col+($w_cols*2);
		$gd_col1 = $gd_first_col."%";
		$gd_col2 = $w_cols."%";
		$gd_col3 = $w_cols."%";
		$gd_col4 = $w_cols."%";
		$gd_col5 = $w_cols."%";
		$gd_col6 = $w_cols."%";
		$gd_col7 = ($w_cols*3)."%";
		$gd_col_opt= $last_col."%";
		
		$w_cols = $w_cols."%";
		$first_col = $first_col."%";
		$last_col = $last_col."%";
	
		if($showFacCol || $pay_location=='1'){
		$facKey = array_keys($arrCICONotApplied);
		$tempFacArr = array();
		foreach($facKey as $facKeyName){
			$facNm = ($pay_location=='1') ? $arr_sch_facilities[$facKeyName] : $posFacilityArr[$facKeyName];
			$tempFacArr[$facKeyName] = $facNm;
		}
		asort($tempFacArr);
		$facTempArrIndex = array_keys($tempFacArr);
		$tmpArr = array();
		foreach($facTempArrIndex as $index => $facId){
			$tmpArr[$facId] = $arrCICONotApplied[$facId];
		}	
		foreach($tmpArr as $grpId => $grpDataArr){
			$firstGroupName='';
			$firstGrpTotal=array();
			
			if($pay_location=='1'){
				$firstGroupName = $arr_sch_facilities[$facId];
				$naFirstGroupTitle='Facility';
			}else{
				$firstGroupName = $providerNameArr[$grpId];
			}
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$naFirstGroupTitle.' - '.$firstGroupName.'</td></tr>';
	
			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal=array();

				if($pay_location=='1'){
					$secGroupName = $providerNameArr[$facId];
					$naSecGroupTitle= ($groupBy=='operator') ? 'Operator' : 'Physician';
				}else{
					$secGroupName = ($pay_location=='1')? $arr_sch_facilities[$facId] : $posFacilityArr[$facId];
					$naSecGroupTitle='Facility';
				}
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$naSecGroupTitle.' - '.$secGroupName.'</td></tr>';
				
				foreach($grpData as $eid => $grpDetail){
					$rowTot=0;
					$pName = explode('~', $grpDetail['pat_name']);
					
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					$reference_nos=($grpDetail['reference_no']!='')?$grpDetail['reference_no']: '';
					
					$facGrpTotal['cash']+= $grpDetail['cash'];
					$facGrpTotal['check']+= $grpDetail['check'];
					$facGrpTotal['credit_card']+=	$grpDetail['credit_card'];
					$facGrpTotal['eft']+=	$grpDetail['eft'];
					$facGrpTotal['veep']+=	$grpDetail['veep'];
					$facGrpTotal['money_order']+=	$grpDetail['money_order'];
					$facGrpTotal['other']+=	$grpDetail['other'];
	
					//TRANSACTION COUNTS
					if($grpDetail['cash']>0)$arrCICOTransCount['cash']+=1;
					if($grpDetail['check']>0)$arrCICOTransCount['check']+=1;
					if($grpDetail['credit_card']>0)$arrCICOTransCount['credit_card']+=1;
					if($grpDetail['money_order']>0)$arrCICOTransCount['money_order']+=1;
					if($grpDetail['eft']>0)$arrCICOTransCount['eft']+=1;
					if($grpDetail['veep']>0)$arrCICOTransCount['veep']+=1;
					if($grpDetail['other']>0)$arrCICOTransCount['other']+=1;
					
				
					$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'] + $grpDetail['other'];
					$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
					
					//POPUP TITLES
					$cash_title=($grpDetail['is_ref']=='cash' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$check_title=($grpDetail['is_ref']=='check' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$cc_title=($grpDetail['is_ref']=='credit_card' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$eft_title=($grpDetail['is_ref']=='eft' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$mo_title=($grpDetail['is_ref']=='money_order' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$veep_title=($grpDetail['is_ref']=='veep' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$other_title=($grpDetail['is_ref']=='other' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
						<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cash_title.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$check_title.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cc_title.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$eft_title.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$mo_title.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$veep_title.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
						$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$veep_title.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
	
						$content_part.='
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
						if(empty($pay_method)==false)
						$content_part.='
						<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'"></td>';
						$content_part.='
					</tr>';	
				}
			
			
			$firstGrpTotal['cash']+=	$facGrpTotal['cash'];
			$firstGrpTotal['check']+= $facGrpTotal['check'];
			$firstGrpTotal['credit_card']+=	$facGrpTotal['credit_card'];
			$firstGrpTotal['eft']+=	$facGrpTotal['eft'];
			$firstGrpTotal['money_order']+=	$facGrpTotal['money_order'];
			$firstGrpTotal['veep']+=	$facGrpTotal['veep'];
			$firstGrpTotal['other']+=	$facGrpTotal['other'];
			// FACILITY TOTAL
			$subTot = $facGrpTotal['cash'] + $facGrpTotal['check'] + $facGrpTotal['credit_card'] + $facGrpTotal['eft'] + $facGrpTotal['money_order'] + $facGrpTotal['veep'] + $facGrpTotal['other'];
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$naSecGroupTitle.' Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['other'],2,1).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';			
			}
			
		$arrCICOPayTot['cash']+=	$firstGrpTotal['cash'];
		$arrCICOPayTot['check']+= $firstGrpTotal['check'];
		$arrCICOPayTot['credit_card']+=	$firstGrpTotal['credit_card'];
		$arrCICOPayTot['eft']+=	$firstGrpTotal['eft'];
		$arrCICOPayTot['money_order']+=	$firstGrpTotal['money_order'];
		$arrCICOPayTot['veep']+=	$firstGrpTotal['veep'];
		$arrCICOPayTot['other']+=	$firstGrpTotal['other'];
		
		$subTot1 = $firstGrpTotal['cash'] + $firstGrpTotal['check'] + $firstGrpTotal['credit_card'] + $firstGrpTotal['eft'] + $firstGrpTotal['money_order'] + $firstGrpTotal['veep'] + $firstGrpTotal['other'];
		// FIRST GROUP TOTAL
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$naFirstGroupTitle.' Total :</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['other'],2,1).'&nbsp;</td>';
			$content_part.='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot1,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>';
			$content_part.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		';			
		}
		}else{
			foreach($arrCICONotApplied as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
				
			if($groupBy=='facility'){
				$firstGroupName = ($pay_location=='1')? $arr_sch_facilities[$grpId] : $posFacilityArr[$grpId];				
			}		
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$naFirstGroupTitle.' - '.$firstGroupName.'</td></tr>';
			
			foreach($grpData as $eid => $grpDetail){
				$pName = explode('~', $grpDetail['pat_name']);
				
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				$reference_nos=($grpDetail['reference_no']!='')?$grpDetail['reference_no']: '';
				
				$firstGrpTotal['cash']+= $grpDetail['cash'];
				$firstGrpTotal['check']+= $grpDetail['check'];
				$firstGrpTotal['credit_card']+=	$grpDetail['credit_card'];
				$firstGrpTotal['eft']+=	$grpDetail['eft'];
				$firstGrpTotal['money_order']+=	$grpDetail['money_order'];
				$firstGrpTotal['veep']+=	$grpDetail['veep'];
				$firstGrpTotal['other']+=	$grpDetail['other'];
	
				//TRANSACTION COUNTS
				if($grpDetail['cash']>0)$arrCICOTransCount['cash']+=1;
				if($grpDetail['check']>0)$arrCICOTransCount['check']+=1;
				if($grpDetail['credit_card']>0)$arrCICOTransCount['credit_card']+=1;
				if($grpDetail['money_order']>0)$arrCICOTransCount['money_order']+=1;
				if($grpDetail['eft']>0)$arrCICOTransCount['eft']+=1;
				if($grpDetail['veep']>0)$arrCICOTransCount['veep']+=1;
				if($grpDetail['other']>0)$arrCICOTransCount['other']+=1;
			
				$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
				$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'] + $grpDetail['other'];
				
				//POPUP TITLES
				$cash_title=($grpDetail['is_ref']=='cash' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$check_title=($grpDetail['is_ref']=='check' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$cc_title=($grpDetail['is_ref']=='credit_card' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$eft_title=($grpDetail['is_ref']=='eft' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$mo_title=($grpDetail['is_ref']=='money_order' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$veep_title=($grpDetail['is_ref']=='veep' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$other_title=($grpDetail['is_ref']=='other' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
							
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
					<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cash_title.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$check_title.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cc_title.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$eft_title.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$mo_title.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$veep_title.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$veep_title.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
					$content_part .='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'"></td>';
					$content_part .='
				</tr>';	
			}
			$arrCICOPayTot['cash']+=	$firstGrpTotal['cash'];
			$arrCICOPayTot['check']+= $firstGrpTotal['check'];
			$arrCICOPayTot['credit_card']+=	$firstGrpTotal['credit_card'];
			$arrCICOPayTot['eft']+=	$firstGrpTotal['eft'];
			$arrCICOPayTot['money_order']+=	$firstGrpTotal['money_order'];
			$arrCICOPayTot['veep']+=	$firstGrpTotal['veep'];
			$arrCICOPayTot['other']+=	$firstGrpTotal['other'];
			
			// SUB TOTAL
			$subTot = $firstGrpTotal['cash'] + $firstGrpTotal['check'] + $firstGrpTotal['credit_card'] + $firstGrpTotal['eft'] + $firstGrpTotal['money_order'] + $firstGrpTotal['veep'] + $firstGrpTotal['other'];
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$naFirstGroupTitle.' Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['other'],2,1).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';			
		}
		}
		
		$cicoBlockTot = $arrCICOPayTot['cash'] + $arrCICOPayTot['check'] + $arrCICOPayTot['credit_card'] + $arrCICOPayTot['eft'] + $arrCICOPayTot['money_order'] + $arrCICOPayTot['veep'] + $arrCICOPayTot['other'];
		
		
		// TOTAL
		// HEADER
		$header='';
		$header='
		<table style="width:100%" class="rpt_table rpt_table-bordered">	
		<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Unapplied CI/CO Payments</td></tr>
		<tr id="">
			<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;"></td>
			<td  class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Paid Date</td>
			<td class="notInPDF text_b_w" style="width:'.$w_cols.'; text-align:center;">Reference No</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Cash&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Check&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Credit Card&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">EFT&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">MO&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">VEEP&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">OTHER&nbsp;</td>';
			$header.='
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>';
			if(empty($pay_method)==false)
			$header.='<td class="text_b_w" style="width:'.$last_col.'; text-align:right;"></td>';
			$header.='
		</tr>
		</table>';
	
		$cctype_html='';
		//CC TYPE AMOUNTS
		if(sizeof($arrCIOCCTypeAmts)>0){
			$ccColspan=11 - $colsRemoved;
			$totCCType=0;
			foreach($arrCIOCCTypeAmts as $ccType=> $amt){
				$totCCType+=$amt;
				$cctype_html.='<tr>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($amt,2,1).'&nbsp;</td>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
				$cctype_html.='</tr>';
	
				$arrGrandCCTypes[$ccType]+=$amt;				
			}
			$cctype_html.='<tr>';
				$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Total of CC Types&nbsp;:</td>';
				$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$cctype_html.='</tr>';	
	
			$cctype_html.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}
	
		
		// PAGE HTML
		$totalCICOCount=$arrCICOTransCount['cash']+$arrCICOTransCount['check']+$arrCICOTransCount['money_order']+$arrCICOTransCount['credit_card']+$arrCICOTransCount['eft']+$arrCICOTransCount['veep']+$arrCICOTransCount['other'];
		$cico_html.=
		$header.
		'<table style="width:100%" class="rpt_table rpt_table-bordered">'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">CI/CO Payments Total&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['other'],2,1).'&nbsp;</td>';
			$cico_html.='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$cico_html.='<td bgcolor="#FFFFFF" class="text_b_w" style="width:'.$last_col.'; text-align:right;"></td>';
			$cico_html.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['cash'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['check'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['credit_card'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['eft'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['money_order'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['veep'].'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['other'].'&nbsp;</td>';
			$cico_html.='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$totalCICOCount.'&nbsp;</td>';
			if(empty($pay_method)==false)
			$cico_html.='<td bgcolor="#FFFFFF" class="text_b_w" style="width:'.$last_col.'; text-align:right;"></td>';
			$cico_html.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		'.$cctype_html.'
		</table>';	
		
		//PDF HTML
		$cico_html_PDF.='
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
		<table style="width:100%" class="rpt_table rpt_table-bordered">'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">CI/CO Payments Total&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrCICOPayTot['other'],2,1).'&nbsp;</td>';
			$cico_html_PDF.='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($cicoBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>';
			$cico_html_PDF.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['cash'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['check'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['credit_card'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['eft'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['money_order'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['veep'].'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrCICOTransCount['other'].'&nbsp;</td>';
			$cico_html_PDF.='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$totalCICOCount.'&nbsp;</td>';
			if(empty($pay_method)==false)
			$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>';
			$cico_html_PDF.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		'.$cctype_html.'
		</table>
		</page>';	
	}

	// UNAPPLIED PRE PAYMENTS
	$arrPrePayTot=array();
	$arrPrePayTransCount=array();
	$content_part= $pre_pay_html = '';
	if(sizeof($arrPrePayNotApplied)>0){
		$dataExists=true;
		$colspan= 14;
		$colspan-=$colsRemoved;
		$total_cols = 12 - $colsRemoved;
	
		$last_col=0;
		if(empty($pay_method)==false){ 	
			$last_col = 30;	
			$total_cols=$total_cols-2;
		}
		
		$first_col = 16;
		$w_cols = (100 - ($first_col + $last_col)) /$total_cols;
		
		$gd_first_col = $first_col+($w_cols*2);
		$gd_col1 = $gd_first_col."%";
		$gd_col2 = $w_cols."%";
		$gd_col3 = $w_cols."%";
		$gd_col4 = $w_cols."%";
		$gd_col5 = $w_cols."%";
		$gd_col6 = $w_cols."%";
		$gd_col7 = ($w_cols*3)."%";
		$gd_col_opt= $last_col."%";
		
		$w_cols = $w_cols."%";
		$first_col = $first_col."%";
		$last_col = $last_col."%";
	
		if($showFacCol || $pay_location=='1'){
		$facKey = array_keys($arrPrePayNotApplied);
		$tempFacArr = array();
		foreach($facKey as $facKeyName){
			$facNm = ($pay_location=='1') ? $arr_sch_facilities[$facKeyName] : $posFacilityArr[$facKeyName];
			$tempFacArr[$facKeyName] = $facNm;
		}
		asort($tempFacArr);
		$facTempArrIndex = array_keys($tempFacArr);
		$tmpArr = array();
		foreach($facTempArrIndex as $index => $facId){
			$tmpArr[$facId] = $arrPrePayNotApplied[$facId];
		}	
		foreach($tmpArr as $grpId => $grpDataArr){
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
			if($groupBy=='facility'){
				$firstGroupName = ($pay_location=='1')? $arr_sch_facilities[$grpId] : $posFacilityArr[$grpId];				
			}	

			if($pay_location=='1'){
				$firstGroupName =$arr_sch_facilities[$grpId];
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$naFirstGroupTitle.' - '.$firstGroupName.'</td></tr>';
		
			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal=array();
				
				if($pay_location=='1'){
					$secGroupName = $providerNameArr[$facId];
					$naSecGroupTitle=($groupBy=='operator')? 'Operator': 'Physician';
				}else{
					$secGroupName = ($pay_location=='1')? $arr_sch_facilities[$facId] : $posFacilityArr[$facId];
					$naSecGroupTitle='Facility';
				}
					
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$naSecGroupTitle.' - '.$secGroupName.'</td></tr>';
				
				foreach($grpData as $eid => $grpDetail){
					$rowTot=0;
					$pName = explode('~', $grpDetail['pat_name']);
					
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					$reference_nos=($grpDetail['reference_no']!='')?$grpDetail['reference_no']:'';
					
					$facGrpTotal['cash']+= $grpDetail['cash'];
					$facGrpTotal['check']+= $grpDetail['check'];
					$facGrpTotal['credit_card']+=	$grpDetail['credit_card'];
					$facGrpTotal['eft']+=	$grpDetail['eft'];
					$facGrpTotal['money_order']+=	$grpDetail['money_order'];
					$facGrpTotal['veep']+=	$grpDetail['veep'];
					$facGrpTotal['other']+=	$grpDetail['other'];
	
					//TRANSACTION COUNTS
					if($grpDetail['cash']>0)$arrPrePayTransCount['cash']+=1;
					if($grpDetail['check']>0)$arrPrePayTransCount['check']+=1;
					if($grpDetail['credit_card']>0)$arrPrePayTransCount['credit_card']+=1;
					if($grpDetail['money_order']>0)$arrPrePayTransCount['money_order']+=1;
					if($grpDetail['eft']>0)$arrPrePayTransCount['eft']+=1;
					if($grpDetail['veep']>0)$arrPrePayTransCount['veep']+=1;
					if($grpDetail['other']>0)$arrPrePayTransCount['other']+=1;
				
					$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'] + $grpDetail['other'];		
					$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
	
					//POPUP TITLES
					$cash_title=($grpDetail['is_ref']=='cash')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$check_title=($grpDetail['is_ref']=='check')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$cc_title=($grpDetail['is_ref']=='credit_card')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$eft_title=($grpDetail['is_ref']=='eft')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$mo_title=($grpDetail['is_ref']=='money_order')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$veep_title=($grpDetail['is_ref']=='veep')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					$other_title=($grpDetail['is_ref']=='other')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
						<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cash_title.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$check_title.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cc_title.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$eft_title.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$mo_title.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$veep_title.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$other_title.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
						$content_part .='
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
						if(empty($pay_method)==false)
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'"></td>';
						$content_part .='
					</tr>';		
				}
				$firstGrpTotal['cash']+=	$facGrpTotal['cash'];
				$firstGrpTotal['check']+= $facGrpTotal['check'];
				$firstGrpTotal['credit_card']+=	$facGrpTotal['credit_card'];
				$firstGrpTotal['eft']+=	$facGrpTotal['eft'];
				$firstGrpTotal['money_order']+=	$facGrpTotal['money_order'];
				$firstGrpTotal['veep']+=	$facGrpTotal['veep'];
				$firstGrpTotal['other']+=	$facGrpTotal['other'];
				
				// Facility TOTAL
				$subTot = $facGrpTotal['cash'] + $facGrpTotal['check'] + $facGrpTotal['credit_card'] + $facGrpTotal['eft'] + $facGrpTotal['money_order'] + $facGrpTotal['veep'] + $facGrpTotal['other'];
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$naSecGroupTitle.' Total :</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['other'],2,1).'&nbsp;</td>';
					$content_part.='
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part.='
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				';				
			}
			
			$arrPrePayTot['cash']+=	$firstGrpTotal['cash'];
			$arrPrePayTot['check']+= $firstGrpTotal['check'];
			$arrPrePayTot['credit_card']+=	$firstGrpTotal['credit_card'];
			$arrPrePayTot['eft']+=	$firstGrpTotal['eft'];
			$arrPrePayTot['money_order']+=	$firstGrpTotal['money_order'];
			$arrPrePayTot['veep']+=	$firstGrpTotal['veep'];
			$arrPrePayTot['other']+=	$firstGrpTotal['other'];
			
			// FIRST GROUP TOTAL
			$subTot = $firstGrpTotal['cash'] + $firstGrpTotal['check'] + $firstGrpTotal['credit_card'] + $firstGrpTotal['eft'] + $firstGrpTotal['money_order'] + $firstGrpTotal['veep'] + $firstGrpTotal['other'];
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$naFirstGroupTitle.' Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['other'],2,1).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';				
		}
		}else{
		foreach($arrPrePayNotApplied as $grpId => $grpData){
		
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
				
			if($groupBy=='facility'){
				$firstGroupName = ($pay_location=='1')? $arr_sch_facilities[$grpId] : $posFacilityArr[$grpId];
			}		
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$naFirstGroupTitle.' - '.$firstGroupName.'</td></tr>';
			
			foreach($grpData as $eid => $grpDetail){
				$pName = explode('~', $grpDetail['pat_name']);
				
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];	
					
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				$reference_nos=($grpDetail['reference_no']!='')?$grpDetail['reference_no']:'';
				
				$firstGrpTotal['cash']+= $grpDetail['cash'];
				$firstGrpTotal['check']+= $grpDetail['check'];
				$firstGrpTotal['credit_card']+=	$grpDetail['credit_card'];
				$firstGrpTotal['eft']+=	$grpDetail['eft'];
				$firstGrpTotal['money_order']+=	$grpDetail['money_order'];
				$firstGrpTotal['veep']+=	$grpDetail['veep'];
				$firstGrpTotal['other']+=	$grpDetail['other'];
	
				//TRANSACTION COUNTS
				if($grpDetail['cash']>0)$arrPrePayTransCount['cash']+=1;
				if($grpDetail['check']>0)$arrPrePayTransCount['check']+=1;
				if($grpDetail['credit_card']>0)$arrPrePayTransCount['credit_card']+=1;
				if($grpDetail['money_order']>0)$arrPrePayTransCount['money_order']+=1;
				if($grpDetail['eft']>0)$arrPrePayTransCount['eft']+=1;
				if($grpDetail['veep']>0)$arrPrePayTransCount['veep']+=1;
				if($grpDetail['other']>0)$arrPrePayTransCount['other']+=1;
			
				$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
				
				$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'] + $grpDetail['other'];		
	
				//POPUP TITLES
				$cash_title=($grpDetail['is_ref']=='cash')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$check_title=($grpDetail['is_ref']=='check')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$cc_title=($grpDetail['is_ref']=='credit_card')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$eft_title=($grpDetail['is_ref']=='eft')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$mo_title=($grpDetail['is_ref']=='money_order')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$veep_title=($grpDetail['is_ref']=='veep')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$other_title=($grpDetail['is_ref']=='other')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
					<td class="notInPDF text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$reference_nos.'</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cash_title.'">'.$CLSReports->numberFormat($grpDetail['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$check_title.'">'.$CLSReports->numberFormat($grpDetail['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$cc_title.'">'.$CLSReports->numberFormat($grpDetail['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$eft_title.'">'.$CLSReports->numberFormat($grpDetail['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$mo_title.'">'.$CLSReports->numberFormat($grpDetail['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$veep_title.'">'.$CLSReports->numberFormat($grpDetail['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.' '.$other_title.'">'.$CLSReports->numberFormat($grpDetail['other'],2,1).'&nbsp;</td>';
					$content_part .='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'"></td>';
					$content_part .='
				</tr>';		
			}
			$arrPrePayTot['cash']+=	$firstGrpTotal['cash'];
			$arrPrePayTot['check']+= $firstGrpTotal['check'];
			$arrPrePayTot['credit_card']+=	$firstGrpTotal['credit_card'];
			$arrPrePayTot['eft']+=	$firstGrpTotal['eft'];
			$arrPrePayTot['money_order']+=	$firstGrpTotal['money_order'];
			$arrPrePayTot['other']+=	$firstGrpTotal['other'];
			
			// SUB TOTAL
			$subTot = $firstGrpTotal['cash'] + $firstGrpTotal['check'] + $firstGrpTotal['credit_card'] + $firstGrpTotal['eft'] + $firstGrpTotal['money_order'] + $firstGrpTotal['veep'] + $firstGrpTotal['other'];
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">'.$naFirstGroupTitle.' Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['other'],2,1).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($subTot,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF"></td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';				
		}
		}
		
		$prePayBlockTot = $arrPrePayTot['cash'] + $arrPrePayTot['check'] + $arrPrePayTot['credit_card'] + $arrPrePayTot['eft'] + $arrPrePayTot['money_order'] + $arrPrePayTot['veep'] + $arrPrePayTot['other'];
		
	
		
		// TOTAL
		// HEADER
		$header='';
		$header='
		<table style="width:100%" class="rpt_table rpt_table-bordered">	
		<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Unapplied Pre-Payments</td></tr>
		<tr id="">
			<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">'.$prePayDateLabel.'</td>
			<td class="notInPDF text_b_w" style="width:'.$w_cols.'; text-align:center;">Reference No</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Cash&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Check&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Credit Card&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">EFT&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">MO&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">VEEP&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$header.='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">OTHER&nbsp;</td>';
			$header.='
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>';
			if(empty($pay_method)==false)
			$header.='<td class="text_b_w" style="width:'.$last_col.';"></td>';
			$header.='
		</tr>
		</table>';
	
		$cctype_html='';
		//CC TYPE AMOUNTS
		if(sizeof($arrPrePayCCTypeAmts)>0){
			$ccColspan=10 - $colsRemoved;
			$totCCType=0;
			foreach($arrPrePayCCTypeAmts as $ccType=> $amt){
				$totCCType+=$amt;
				$cctype_html.='<tr>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($amt,2,1).'&nbsp;</td>';
					$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
				$cctype_html.='</tr>';
	
				$arrGrandCCTypes[$ccType]+=$amt;				
			}
			$cctype_html.='<tr>';
				$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Total of CC Types&nbsp;:</td>';
				$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$cctype_html.='</tr>';	
	
			$cctype_html.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}
		
		//PAGE
		$totalPrePayCount=$arrPrePayTransCount['cash']+$arrPrePayTransCount['check']+$arrPrePayTransCount['credit_card']+$arrPrePayTransCount['money_order']+$arrPrePayTransCount['eft']+$arrPrePayTransCount['veep']+$arrPrePayTransCount['other'];
		$pre_pay_html .=
		$header.' 
		<table style="width:100%" class="rpt_table rpt_table-bordered">'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Pre-Payments Total&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['other'],2,1).'&nbsp;</td>';
			$pre_pay_html .='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prePayBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF"></td>';
			$pre_pay_html .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['cash'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['check'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['credit_card'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['eft'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['money_order'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['veep'].'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['other'].'&nbsp;</td>';
			$pre_pay_html .='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$totalPrePayCount.'&nbsp;</td>';
			if(empty($pay_method)==false)
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF"></td>';
			$pre_pay_html .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		'.$cctype_html.'
		</table>';		
	
		//PDF HTML
		$pre_pay_html_PDF.=' 
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
		<table style="width:100%" class="rpt_table rpt_table-bordered">	
		'.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Pre-Payments Total&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money card')
			$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['other'],2,1).'&nbsp;</td>';
			$pre_pay_html_PDF.='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($prePayBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>';
			$pre_pay_html_PDF.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['cash'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['check'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['credit_card'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['eft'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['money_order'].'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['veep'].'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$arrPrePayTransCount['other'].'&nbsp;</td>';
			$pre_pay_html_PDF .='
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$totalPrePayCount.'&nbsp;</td>';
			if(empty($pay_method)==false)
			$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF"></td>';
			$pre_pay_html_PDF .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		'.$cctype_html.'	
		</table>
		</page>';	
	}
}
//DELETED RECORDS
//POSTED RECORDS
$content_part= $deleted_html='';
$delDataExists=false;
if(sizeof($arrDelPostedAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$colspan= 5;

	$total_cols = 3;
	$first_col = "16";
	$last_col = "40";
	$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
	
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);

	$grand_first_col=$first_col;
	$grand_w_cols=$w_cols;	
	
	$first_col = $first_col.'%';
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	
	if($showFacCol || $pay_location=='1'){
		foreach($arrDelPostedAmounts as $grpId => $grpDataArr){
			$firstGrpTotal=array();
			$firstGroupName='';
			
			if($pay_location=='1'){
				$firstGroupName = $arr_sch_facilities[$grpId];
				$firstGroupTitle='Facility';
			}else{
				$firstGroupName = $providerNameArr[$grpId];
				$firstGroupTitle='Physician';
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';

			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal = array();

				if($pay_location=='1'){
					$secGroupName = $providerNameArr[$facId];
					$secGroupTitle= ($groupBy=='operator')? 'Operator' : 'Physician';					
				}else{
					$secGroupName = ($pay_location=='1')? $arr_sch_facilities[$facId] : $posFacilityArr[$facId];	
					$secGroupTitle= 'Facility';
				}
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$secGroupTitle.' - '.$secGroupName.'</td></tr>';
			
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
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2,1).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>
					</tr>';			
				}
		
				//FACILITY TOTAL
				$firstGrpTotal['del_amount']+=	$facGrpTotal['del_amount'];
			
				// SUB TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$secGroupTitle.' Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['del_amount'],2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				';
			}
			
			//PHYSICIAN TOTAL
			$arrPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
			
			// SUB TOTAL
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';
		}
	}else{
		foreach($arrDelPostedAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
		
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
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
		}
	}
	
	// TOTAL
	// HEADER
	$header='
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Deleted Posted Payments</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Enc. Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOS</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Deleted Amount</td>
		<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
	</tr>
	</table>';

	$delPostedBlockTotal = $arrPatPayTot['del_amount'];
		
	//PAGE HTML
	$del_posted_html .=
	$header.' 
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted Posted Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['del_amount'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>';
	
	//PDF HTML
	$del_posted_PDF.='
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
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted Posted Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['del_amount'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>
	</page>';	
	
}

//DELETED CI/CO
$content_part= $deleted_html='';
$arrDelPatPayTot=array();
if(sizeof($arrDelCICOAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$colspan= 5;

	$total_cols = 3;
	$first_col = "16";
	$last_col = "40";
	$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
	
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);
	
	$grand_first_col=$first_col;
	$grand_w_cols=$w_cols;	
	
	$first_col = $first_col.'%';
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	
	if($showFacCol || $pay_location=='1'){
		foreach($arrDelCICOAmounts as $grpId => $grpDataArr){
			$firstGrpTotal=array();
			$firstGroupName='';

			if($pay_location=='1'){	
				$firstGroupName = $arr_sch_facilities[$grpId];
				$firstGroupTitle='Facility';
			}else{
				$firstGroupName = $providerNameArr[$grpId];
				$firstGroupTitle='Physician';
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';

			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal = array();

				if($pay_location=='1'){	
					$secGroupName = $providerNameArr[$facId];
					$secGroupTitle= ($groupBy=='operator')? 'Operator' : 'Physician';
				}else{
					$secGroupName = ($pay_location=='1')? $arr_sch_facilities[$facId] : $posFacilityArr[$facId];	
					$secGroupTitle='Facility';
				}
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$secGroupTitle.' - '.$secGroupName.'</td></tr>';
			
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
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2,1).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>
					</tr>';			
				}
		
				//FACILITY TOTAL
				$firstGrpTotal['del_amount']+=	$facGrpTotal['del_amount'];
			
				// SUB TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$secGroupTitle.' Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['del_amount'],2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				';
			}
			
			//PHYSICIAN TOTAL
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
			
			// SUB TOTAL
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';
		}
	}else{
		foreach($arrDelCICOAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
		
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
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
		}
	}
	
	// TOTAL
	// HEADER
	$header='
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Deleted CI/CO Payments</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Paid Date</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Deleted Amount</td>
		<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
	</tr>
	</table>';

	$delCICOBlockTotal = $arrDelPatPayTot['del_amount'];
		
	//PAGE HTML
	$del_cico_html .=
	$header.' 
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted CI/CO Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>';
	
	//PDF HTML
	$del_cico_PDF.='
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
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted CI/CO Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>
	</page>';	
	
}

//DELETED PRE_PAYMENTS
$content_part= $deleted_html='';
$arrDelPatPayTot=array();
if(sizeof($arrDelPrePayAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$colspan= 5;

	$total_cols = 3;
	$first_col = "16";
	$last_col = "40";
	$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
	
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);

	$grand_first_col=$first_col;
	$grand_w_cols=$w_cols;	
	
	$first_col = $first_col.'%';
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	
	if($showFacCol || $pay_location=='1'){
		foreach($arrDelPrePayAmounts as $grpId => $grpDataArr){
			$firstGrpTotal=array();
			$firstGroupName='';
			
			if($pay_location=='1'){
				$firstGroupTitle='Facility';
				$firstGroupName = $arr_sch_facilities[$grpId];
			}else{
				$firstGroupTitle='Physician';
				$firstGroupName = $providerNameArr[$grpId];
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';

			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal = array();

				if($pay_location=='1'){
					$secGroupName = $providerNameArr[$facId];
					$secGroupTitle=($groupBy=='operator')? 'Operator' : 'Physician';
				}else{
					$secGroupName = ($pay_location=='1')? $arr_sch_facilities[$facId] : $posFacilityArr[$facId];
					$secGroupTitle='Facility';
				}
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$secGroupTitle.' - '.$secGroupName.'</td></tr>';
			
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
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2,1).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>
					</tr>';			
				}
		
				//FACILITY TOTAL
				$firstGrpTotal['del_amount']+=	$facGrpTotal['del_amount'];
			
				// SUB TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$secGroupTitle.' Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['del_amount'],2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
				';
			}
			
			//PHYSICIAN TOTAL
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
			
			// SUB TOTAL
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			';
		}
	}else{
		foreach($arrDelPrePayAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
		
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
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
		}
	}
	
	// TOTAL
	// HEADER
	$header='
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Deleted Pre-Payments</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOT</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Deleted Amount</td>
		<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
	</tr>
	</table>';

	
	$delPrePayBlockTotal = $arrDelPatPayTot['del_amount'];
	
	//PAGE HTML
	$del_pre_pay_html .=
	$header.' 
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted Pre-Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>';
	
	//PDF HTML
	$del_pre_pay_PDF.='
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
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted Pre-Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>
	</page>';	
	
}

//PAYMENTS TOTAL
if($dataExists==true){
	$title= ($delDataExists==true) ? 'Payments Total' : 'Grand Total';

	$cash= $arrPatPayTot['cash'] + $arrInsPayTot['cash'] + $arrCICOPayTot['cash'] + $arrPrePayTot['cash'];
	$check= $arrPatPayTot['check'] + $arrInsPayTot['check'] + $arrCICOPayTot['check'] + $arrPrePayTot['check'];
	$credit_card= $arrPatPayTot['credit_card'] + $arrInsPayTot['credit_card'] + $arrCICOPayTot['credit_card'] + $arrPrePayTot['credit_card'];
	$eft= $arrPatPayTot['eft'] + $arrInsPayTot['eft'] + $arrCICOPayTot['eft'] + $arrPrePayTot['eft'];
	$money_order= $arrPatPayTot['money_order'] + $arrInsPayTot['money_order'] + $arrCICOPayTot['money_order'] + $arrPrePayTot['money_order'];
	$veep= $arrPatPayTot['veep'] + $arrInsPayTot['veep'] + $arrCICOPayTot['veep'] + $arrPrePayTot['veep'];
	$other= $arrPatPayTot['other'] + $arrInsPayTot['other'] + $arrCICOPayTot['other'] + $arrPrePayTot['other'];
	
	$grandTot = $cash + $check + $credit_card + $eft + $money_order + $veep + $other;
	
	$grandCCType='';
	$colspan=9 - $colsRemoved;	
	$total_cols = "6";
	$first_col = "16";
	$w_cols = floor((100 - $first_col)/$total_cols);

	$first_col = 100 - ($total_cols * $w_cols);
	$w_cols = $w_cols."%";
	$first_col = $first_col."%";

	//IF GRAND CC TYPE AMOUNTS EXIST
	if(sizeof($arrGrandCCTypes)>0){
		$ccColspan= $colspan-2;
		$totCCType=0;
		foreach($arrGrandCCTypes as $ccType=> $amt){
			$totCCType+=$amt;
			$grandCCType.='<tr>';
				$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
				$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($amt,2,1).'&nbsp;</td>';
				$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$grandCCType.='</tr>';	
		}
		$grandCCType.='<tr>';
			$grandCCType.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Grand Total of CC Types&nbsp;:</td>';
			$grandCCType.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
			$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$grandCCType.='</tr>';	

		$grandCCType.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
	}	
	
	//TOTAL COUNTS
	$totCashCnt= $arrPostedTransCount['cash']+$arrCICOTransCount['cash']+$arrPrePayTransCount['cash'];
	$totCheckCnt= $arrPostedTransCount['check']+$arrCICOTransCount['check']+$arrPrePayTransCount['check'];
	$totMOCnt= $arrPostedTransCount['money_order']+$arrCICOTransCount['money_order']+$arrPrePayTransCount['money_order'];
	$totEFTCnt= $arrPostedTransCount['eft']+$arrCICOTransCount['eft']+$arrPrePayTransCount['eft'];
	$totCCCnt= $arrPostedTransCount['credit_card']+$arrCICOTransCount['credit_card']+$arrPrePayTransCount['credit_card'];
	$totVeepCnt= $arrPostedTransCount['veep']+$arrCICOTransCount['veep']+$arrPrePayTransCount['veep'];
	$totOtherCnt= $arrPostedTransCount['other']+$arrCICOTransCount['other']+$arrPrePayTransCount['other'];
	$totCount= $totCashCnt+$totCheckCnt+$totMOCnt+$totEFTCnt+$totCCCnt+$totVeepCnt+$totOtherCnt;
		
	//FINAL HTML
	$grand_html=' 
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">'.$title.'</td></tr>
	<tr id="">
		<td class="text_b_w" style="text-align:center;width:'.$gd_col1.';" ></td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col2.';">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col3.';">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col4.';">Credit Card&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col5.';">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col6.';">MO&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col6.';">VEEP&nbsp;</td>';
		if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col6.';">OTHER&nbsp;</td>';
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col7.';">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_html.='<td class="text_b_w" style="text-align:right;width:'.$gd_col_opt.';">&nbsp;</td>';
		$grand_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col1.'; text-align:right;"></td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($cash,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col3.'; text-align:right;">'.$CLSReports->numberFormat($check,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col4.'; text-align:right;">'.$CLSReports->numberFormat($credit_card,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col5.'; text-align:right;">'.$CLSReports->numberFormat($eft,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col6.'; text-align:right;">'.$CLSReports->numberFormat($money_order,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col6.'; text-align:right;">'.$CLSReports->numberFormat($veep,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col6.'; text-align:right;">'.$CLSReports->numberFormat($other,2,1).'&nbsp;</td>';
		$grand_html.='
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col7.'; text-align:right;">'.$CLSReports->numberFormat($grandTot,2,1).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col_opt.';">&nbsp;</td>';
		$grand_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col1.'; text-align:right;">Total Transactions Count: </td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col2.'; text-align:right;">'.$totCashCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col3.'; text-align:right;">'.$totCheckCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col4.'; text-align:right;">'.$totCCCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col5.'; text-align:right;">'.$totEFTCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col6.'; text-align:right;">'.$totMOCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col6.'; text-align:right;">'.$totVeepCnt.'&nbsp;</td>';
		if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col6.'; text-align:right;">'.$totOtherCnt.'&nbsp;</td>';
		$grand_html.='
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col7.'; text-align:right;">'.$totCount.'&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="width:'.$gd_col_opt.';">&nbsp;</td>';
		$grand_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
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
	
	$totalCollected= $postedBlockTot + $cicoBlockTot + $prePayBlockTot;
	$totalDeleted= $delPostedBlockTotal + $delCICOBlockTotal + $delPrePayBlockTotal;
	$finalCollected= $totalCollected - $totalDeleted;
	
	//FINAL HTML
	$grand_html1=' 
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	<tr id=""><td class="text_b_w" colspan="3">Grand Collection</td></tr>
	<tr id="">
		<td class="text_b_w" style="text-align:center;width:'.$firstCol.';"></td>
		<td class="text_b_w" style="text-align:right;width:'.$grand_w_cols.'">Payments&nbsp;</td>
		<td class="text_b_w" style="text-align:right;width:'.$lastCol.'">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Posted Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($postedBlockTot,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">CI/CO Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoBlockTot,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Pre-Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($prePayBlockTot,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Total Collected</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totalCollected,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted Posted Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($delPostedBlockTotal,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted CI/CO Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($delCICOBlockTotal,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted Pre-Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($delPrePayBlockTotal,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Total Deleted</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($totalDeleted,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">Final Collected</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($finalCollected,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	</table>';		
}

//CI/CO MANUALLY APPLIED
$content_part= $applied_html='';
$totalCICOManuallyApplied=0;
$arrAppliedPatPayTot=array();
if(sizeof($arrCICOManuallyPaid)>0){
	$colspan= 5;

	$total_cols = 3;
	$first_col = "16";
	$last_col = "40";
	$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
	
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);

	$grand_first_col=$first_col;
	$grand_w_cols=$w_cols;	
	
	$first_col = $first_col.'%';
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	
	foreach($arrCICOManuallyPaid as $grpId => $grpData){
		$rowTot=0;
		$firstGroupName='';
		$firstGrpTotal=array();
		$firstGroupName = $providerNameArr[$grpId];

		$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
	
		foreach($grpData as $eid => $grpDetail){
			$pName = explode('~', $grpDetail['pat_name']);
			$patient_name_arr = array();
			$patient_name_arr["LAST_NAME"] = $pName[3];
			$patient_name_arr["FIRST_NAME"] = $pName[1];
			$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
			$patient_name = changeNameFormat($patient_name_arr);
			$patient_name.= ' - '.$pName[0];
			
			$firstGrpTotal['applied_amt']+=	$grpDetail['applied_amt'];
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['applied_amt'],2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
			</tr>';			
		}
	
		$arrAppliedPatPayTot['applied_amt']+=	$firstGrpTotal['applied_amt'];
	
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['applied_amt'],2,1).'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
	}

	$totalCICOManuallyApplied=$arrAppliedPatPayTot['applied_amt'];
	
	// TOTAL
	// HEADER
	$header='
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">CI/CO Manually Applied Amounts</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOT</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Applied Amount</td>
		<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
	</tr>
	</table>';

	
	//HTML
	$manually_applied_cico_html .=
	$header.' 
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">CI/CO Manually Applied Amounts&nbsp;:</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrAppliedPatPayTot['applied_amt'],2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>';
}

//PRE-PAYMENT MANUALLY APPLIED
$content_part= $applied_html='';
$arrAppliedPatPayTot=array();
if(sizeof($arrPrePayManuallyApplied)>0){
	$colspan= 5;

	$total_cols = 3;
	$first_col = "16";
	$last_col = "40";
	$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
	
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);

	$grand_first_col=$first_col;
	$grand_w_cols=$w_cols;	
	
	$first_col = $first_col.'%';
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	
	foreach($arrPrePayManuallyApplied as $grpId => $grpData){
		$rowTot=0;
		$firstGroupName='';
		$firstGrpTotal=array();
		$firstGroupName = $providerNameArr[$grpId];

		$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
	
		foreach($grpData as $eid => $grpDetail){
			$pName = explode('~', $grpDetail['pat_name']);
			$patient_name_arr = array();
			$patient_name_arr["LAST_NAME"] = $pName[3];
			$patient_name_arr["FIRST_NAME"] = $pName[1];
			$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
			$patient_name = changeNameFormat($patient_name_arr);
			$patient_name.= ' - '.$pName[0];
			
			$firstGrpTotal['applied_amt']+=	$grpDetail['applied_amt'];
			
			$content_part .= '
			<tr>
				<td class="text_10 text_b_w" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
				<td class="text_10 text_b_w" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
				<td class="text_10 text_b_w" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
				<td class="text_10 text_b_w" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['applied_amt'],2,1).'&nbsp;</td>
				<td class="text_10 text_b_w" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
			</tr>';			
		}
	
		$arrAppliedPatPayTot['applied_amt']+=	$firstGrpTotal['applied_amt'];
	
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['applied_amt'],2,1).'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
	}

	
	// TOTAL
	// HEADER
	$header='
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Pre-Payments Manually Applied Amounts</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOT</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Applied Amount</td>
		<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
	</tr>
	</table>';


	//HTML
	$manually_applied_pre_pay_html .=
	$header.' 
	<table style="width:100%" class="rpt_table rpt_table-bordered">	
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Pre-Payments Manually Applied Amounts&nbsp;:</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrAppliedPatPayTot['applied_amt'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';

	//IF MANUALLY APPLIED CI/CO ALSO EXIST THEN MAKE TOTAL OF BOTH
	if($totalCICOManuallyApplied>0){
		$tot= $totalCICOManuallyApplied + $arrAppliedPatPayTot['applied_amt'];
		$manually_applied_pre_pay_html.='
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Total Manually Applied Amounts&nbsp;:</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($tot,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
	}
	$manually_applied_pre_pay_html.='</table>';
}

//REMOVE REFERRAL COLUMN FROM PDF HTML
//POSTED
$patient_html_PDF = preg_replace('/<td class="notInPDF.*?>(.*?)<\/td>/', '', $patient_html_PDF);
$patient_html_PDF = preg_replace('/colspan="4"/', 'colspan="3"', $patient_html_PDF);
$patient_html_PDF = preg_replace('/colspan="14"/', 'colspan="13"', $patient_html_PDF);
//CI/CO
$cico_html_PDF = preg_replace('/<td class="notInPDF.*?>(.*?)<\/td>/', '', $cico_html_PDF);
$cico_html_PDF = preg_replace('/colspan="4"/', 'colspan="3"', $cico_html_PDF);
$cico_html_PDF = preg_replace('/colspan="14"/', 'colspan="13"', $cico_html_PDF);
//PRE-PAYMENT
$pre_pay_html_PDF = preg_replace('/<td class="notInPDF.*?>(.*?)<\/td>/', '', $pre_pay_html_PDF);
$pre_pay_html_PDF = preg_replace('/colspan="4"/', 'colspan="3"', $pre_pay_html_PDF);
$pre_pay_html_PDF = preg_replace('/colspan="14"/', 'colspan="13"', $pre_pay_html_PDF);

$page_content=
$patient_html.
$cico_html.
$pre_pay_html.
$grand_html.
$del_posted_html.
$del_cico_html.
$del_pre_pay_html.
$grand_html1.
$manually_applied_cico_html.
$manually_applied_pre_pay_html
;

$pdf_content=
$patient_html_PDF.
$cico_html_PDF.
$pre_pay_html_PDF.
$grand_html.
$del_posted_PDF.
$del_cico_PDF.
$del_pre_pay_PDF.
$grand_html1.
$manually_applied_cico_html.
$manually_applied_pre_pay_html
;
?>
