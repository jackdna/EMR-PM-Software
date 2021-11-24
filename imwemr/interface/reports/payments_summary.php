<?php

$dataExists=false;
$firstGroupTitle = 'Physician';
$subTotalTitle = 'Physician Total';
$naFirstGroupTitle='Physician';
$naSubTotalTitle='Physician Total';

//SET GLOBAL CURRENCY
$showCurrencySymbol = showcurrency();

$firstGroupTitle = 'Physician';
$subTotalTitle = 'Physician Total';
$naFirstGroupTitle='Physician';
$naSubTotalTitle='Physician Total';

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

$colsRemoved= (empty($pay_method)==false) ? 4 : 0;


if($groupBy=='department'){
	//SET COL WIDTHS
	$fac_col='';
	$colspan= 11 - $colsRemoved;
	$colWidth="9%";
	$total_cols = 10 - $colsRemoved;
	if($showFacCol){
		$colspan= 12 - $colsRemoved;
		$total_cols=11 - $colsRemoved;
	}
	if(empty($pay_method)==false){
		$secColspan= $colspan - 6;
	}else{
		$secColspan= $colspan - 3;
	}
	
	$last_col=0;
	if(empty($pay_method)==false){ 	
		$last_col = 40;	
		$total_cols=$total_cols-1;
	}
	$first_col = 16;
	$w_cols = (100 - ($first_col + $last_col)) / $total_cols;

	$gd_first_col = $first_col;
	if($showFacCol){
		$gd_first_col += $w_cols;
	}
	
	$gd_col1 = $gd_first_col."%";
	$gd_col2 = $w_cols."%";
	$gd_col3 = $w_cols."%";
	$gd_col4 = $w_cols."%";
	$gd_col5 = $w_cols."%";
	$gd_col6 = $w_cols."%";
	$gd_col7 = ($w_cols*3)."%";
	$gd_col_opt= $last_col."%";

	$first_col = $first_col."%";
	$last_col = $last_col."%";
	$w_cols = $w_cols."%";
	//-------------------------

	foreach($arrDepartmentView as $phy_id => $phy_data){
		$dataExists=true;
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
				$arr_dept_tot=array();
				$content_part.='<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Posted Payments</td></tr>';
				
				foreach($postedData as $dept_id => $grpData){
					
					$dept_name = $dept_name_arr[$dept_id];
					
					$rowTot=0;
					$firstGroupName='';
					$firstGrpTotal=array();
					
					$arr_dept_tot['cash']+=	$grpData['cash'];
					$arr_dept_tot['check']+= $grpData['check'];
					$arr_dept_tot['credit_card']+=	$grpData['credit_card'];
					$arr_dept_tot['eft']+=	$grpData['eft'];
					$arr_dept_tot['money_order']+=	$grpData['money_order'];
					$arr_dept_tot['veep']+=	$grpData['veep'];
					$arr_dept_tot['other']+=	$grpData['other'];
					$arr_dept_tot['by_patient']+=	$grpData['byPatient'];
					$arr_dept_tot['by_insurance']+=	$grpData['byInsurance'];

					//FOR GRAND TOTALS
					$arrPatPayTot['cash']+=	$grpData['cash'];
					$arrPatPayTot['check']+= $grpData['check'];
					$arrPatPayTot['credit_card']+=	$grpData['credit_card'];
					$arrPatPayTot['eft']+=	$grpData['eft'];
					$arrPatPayTot['money_order']+=	$grpData['money_order'];
					$arrPatPayTot['veep']+=	$grpData['veep'];
					$arrPatPayTot['other']+=	$grpData['other'];
					$arrPatPayTot['by_patient']+=	$grpData['byPatient'];
					$arrPatPayTot['by_insurance']+=	$grpData['byInsurance'];
					
					$rowTot = $grpData['byPatient'] + $grpData['byInsurance'];
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$dept_name.'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['cash'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['check'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')					
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['credit_card'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')					
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['eft'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')					
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['money_order'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')					
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['veep'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')					
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['other'],2,1).'&nbsp;</td>';
						$content_part .='
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['byPatient'],2,1).'&nbsp;</td>					
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['byInsurance'],2,1).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
						if(empty($pay_method)==false)
						$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part .='
					</tr>';					
				}
				
				$facGrpTotal['cash']+=	$arr_dept_tot['cash'];
				$facGrpTotal['check']+= $arr_dept_tot['check'];
				$facGrpTotal['credit_card']+=	$arr_dept_tot['credit_card'];
				$facGrpTotal['eft']+=	$arr_dept_tot['eft'];
				$facGrpTotal['money_order']+=	$arr_dept_tot['money_order'];
				$facGrpTotal['veep']+=	$arr_dept_tot['veep'];
				$facGrpTotal['other']+=	$arr_dept_tot['other'];
				$facGrpTotal['by_patient']+=	$arr_dept_tot['by_patient'];
				$facGrpTotal['by_insurance']+=	$arr_dept_tot['by_insurance'];	
	
				//FACILITY TOTAL
				$rowTot = $arr_dept_tot['by_patient'] + $arr_dept_tot['by_insurance'];
				$postedBlockTot+= $rowTot;
				
				$content_part .= '
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Departments Total:</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')					
					$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')					
					$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')					
					$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')					
					$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['other'],2,1).'&nbsp;</td>';
					$content_part .='
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['by_patient'],2,1).'&nbsp;</td>					
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arr_dept_tot['by_insurance'],2,1).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part .='
				</tr>';	
				
			}unset($postedData);
			
			//CI/CO
			if(sizeof($cicoData)>0){
				$rowTot=0;
				$firstGroupName='';
				$firstGrpTotal=array();
				$facilityName = $posFacilityArr[$facId];

				$rowTot = $cicoData['cash'] + $cicoData['check'] + $cicoData['credit_card'] + $cicoData['eft'] + $cicoData['money_order'] + $cicoData['veep'] + $cicoData['other'];
				$cicoBlockTot+= $rowTot;
								
				$facGrpTotal['cash']+=	$cicoData['cash'];
				$facGrpTotal['check']+= $cicoData['check'];
				$facGrpTotal['credit_card']+=	$cicoData['credit_card'];
				$facGrpTotal['eft']+=	$cicoData['eft'];
				$facGrpTotal['veep']+=	$cicoData['veep'];
				$facGrpTotal['other']+=	$cicoData['other'];
				$facGrpTotal['money_order']+=	$cicoData['money_order'];
				$facGrpTotal['by_patient']+=$rowTot;

				//FOR GRAND TOTALS
				$arrCICOPayTot['cash']+=	$cicoData['cash'];
				$arrCICOPayTot['check']+= $cicoData['check'];
				$arrCICOPayTot['credit_card']+=	$cicoData['credit_card'];
				$arrCICOPayTot['eft']+=	$cicoData['eft'];
				$arrCICOPayTot['money_order']+=	$cicoData['money_order'];
				$arrCICOPayTot['veep']+=	$cicoData['veep'];
				$arrCICOPayTot['other']+=	$cicoData['other'];
				$arrCICOPayTot['by_patient']+=	$rowTot;
				
				$content_part .= '
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">CI/CO Payments</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoData['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoData['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoData['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoData['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoData['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoData['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoData['other'],2,1).'&nbsp;</td>';
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part.='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part .='
				</tr>';	

			}

			//PRE-PAYMENT
			if(sizeof($prepaidData)>0){
				$rowTot=0;
				$facilityName = $posFacilityArr[$facId];

				$rowTot = $prepaidData['cash'] + $prepaidData['check'] + $prepaidData['credit_card'] + $prepaidData['eft'] + $prepaidData['money_order'] + $prepaidData['veep'];
				$prePayBlockTot+= $rowTot;
								
				$facGrpTotal['cash']+=	$prepaidData['cash'];
				$facGrpTotal['check']+= $prepaidData['check'];
				$facGrpTotal['credit_card']+=	$prepaidData['credit_card'];
				$facGrpTotal['eft']+=	$prepaidData['eft'];
				$facGrpTotal['money_order']+=	$prepaidData['money_order'];
				$facGrpTotal['veep']+=	$prepaidData['veep'];
				$facGrpTotal['other']+=	$prepaidData['other'];
				$facGrpTotal['by_patient']+=$rowTot;

				//FOR GRAND TOTALS
				$arrPatPayTot['cash']+=	$prepaidData['cash'];
				$arrPatPayTot['check']+= $prepaidData['check'];
				$arrPatPayTot['credit_card']+=	$prepaidData['credit_card'];
				$arrPatPayTot['eft']+=	$prepaidData['eft'];
				$arrPatPayTot['money_order']+=	$prepaidData['money_order'];
				$arrPatPayTot['veep']+=	$prepaidData['veep'];
				$arrPatPayTot['other']+=	$prepaidData['other'];
				$arrPatPayTot['by_patient']+=	$rowTot;
				
				$redRow=($prepaidData['is_ref']>0)?';color:#FF0000':'';
				
				//POPUP TILES
				$cash_title=($prepaidData['cash_is_ref']=='cash' && $prepaidData['cash_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$prepaidData['cash_ref_amt'].' Refund"':'"';
				$check_title=($prepaidData['check_is_ref']=='check' && $prepaidData['check_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$prepaidData['check_ref_amt'].' Refund"':'"';
				$cc_title=($prepaidData['credit_card_is_ref']=='credit_card' && $prepaidData['credit_card_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$prepaidData['credit_card_ref_amt'].' Refund"':'"';	
				$eft_title=($prepaidData['eft_is_ref']=='eft' && $prepaidData['eft_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$prepaidData['eft_ref_amt'].' Refund"':'"';			
				$mo_title=($prepaidData['money_order_is_ref']=='money_order' && $prepaidData['money_order_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$prepaidData['money_order_ref_amt'].' Refund"':'"';			
				$veep_title=($prepaidData['veep_is_ref']=='veep' && $prepaidData['veep_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$prepaidData['veep_ref_amt'].' Refund"':'"';
				
				$content_part .= '
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word">Pre-Payments</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$cash_title.'">'.$CLSReports->numberFormat($prepaidData['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$check_title.'">'.$CLSReports->numberFormat($prepaidData['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$cc_title.'">'.$CLSReports->numberFormat($prepaidData['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$eft_title.'">'.$CLSReports->numberFormat($prepaidData['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$mo_title.'">'.$CLSReports->numberFormat($prepaidData['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$veep_title.'">'.$CLSReports->numberFormat($prepaidData['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$veep_title.'">'.$CLSReports->numberFormat($prepaidData['other'],2,1).'&nbsp;</td>';
					$content_part .='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part .='
				</tr>';	
									
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

			$rowTot = $facGrpTotal['cash'] + $facGrpTotal['check'] + $facGrpTotal['credit_card'] + $facGrpTotal['eft'] + $facGrpTotal['money_order'] + $facGrpTotal['veep'];			

			//FACILITY TOTALS
			$content_part .= '
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Facility Total:</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')					
				$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')					
				$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')					
				$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')					
				$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')				
				$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['other'],2,1).'&nbsp;</td>';
				$content_part .='
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['by_patient'],2,1).'&nbsp;</td>					
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($facGrpTotal['by_insurance'],2,1).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$content_part .='
			</tr>';						
			
		}

		$rowTot=0;
		$rowTot=$phyGrpTotal['cash'] + $phyGrpTotal['check'] + $phyGrpTotal['credit_card'] + $phyGrpTotal['eft'] + $phyGrpTotal['money_order'] + $phyGrpTotal['veep'] + $phyGrpTotal['other'];			
		
		//PHYSICIAN TOTAL
		$content_part .= '
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Physician Total:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')					
			$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')					
			$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')					
			$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')					
			$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')					
			$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['other'],2,1).'&nbsp;</td>';
			$content_part .='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['by_patient'],2,1).'&nbsp;</td>					
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($phyGrpTotal['by_insurance'],2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
		$content_part .='
		</tr>';			
		
	}
	
	$patient_html='<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:center;">Department / Facility</td>
		'.$fac_col;
		if(empty($pay_method)==true || $pay_method=='cash')
		$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Credit Card&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Money Order&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">VEEP&nbsp;</td>';
		if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
		$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">OTHER&nbsp;</td>';
		$patient_html .='
		<td class="text_b_w" style="text-align:center;" colspan="2">Payments By</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$patient_html .='<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>';
		$patient_html .='
	</tr>
	<tr id="">
		<td class="text_b_w" style="text-align:right;" colspan="'.$secColspan.'"></td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Patient&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Insurance&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>';
		if(empty($pay_method)==false)
		$patient_html .='<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>';
	$patient_html .='
	</tr>'
	.$content_part.
	'</table>';
	

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
	$content_part= $patient_html='';
	if(sizeof($arrPostedPay)>0){
	
		//SET COL WIDTHS
		$fac_col='';
		$colspan= 11 - $colsRemoved;
		$colWidth="9%";
		$total_cols = 10 - $colsRemoved;
		if($showFacCol || $pay_location=='1'){
			$colspan= 12 - $colsRemoved;
			$total_cols=11 - $colsRemoved;
		}
		if(empty($pay_method)==false){	
			$secColspan= $colspan - 6;
		}else{
			$secColspan= $colspan - 3;
		}
		$last_col=0;
		if(empty($pay_method)==false){ 	
			$last_col = 40;	
			$total_cols=$total_cols-1;
		}
		$first_col = 17;
		$w_cols = (100 - ($first_col + $last_col)) / $total_cols;
	
		$gd_first_col = $first_col;
		if($showFacCol || $pay_location=='1'){
			$gd_first_col += $w_cols;
		}
		
		$gd_col1 = $gd_first_col."%";
		$gd_col2 = $w_cols."%";
		$gd_col3 = $w_cols."%";
		$gd_col4 = $w_cols."%";
		$gd_col5 = $w_cols."%";
		$gd_col6 = $w_cols."%";
		$gd_col7 = ($w_cols*3)."%";
		$gd_col_opt= $last_col."%";
	
		$first_col = $first_col."%";
		$last_col = $last_col."%";
		$w_cols = $w_cols."%";
		//-------------------------
		
		
		$dataExists=true;
		if($showFacCol || $pay_location=='1'){
			foreach($arrPostedPay as $grpId => $grpDataArr){
				foreach($grpDataArr as $facID=>$grpData){
					$rowTot=0;
					$firstGroupName='';
					$firstGrpTotal=array();
					$secGroupName = ($pay_location=='1')? $arr_sch_facilities[$facID] : $posFacilityArr[$facID];
					
					if($pay_location=='1'){
						$firstGroupName = $arr_sch_facilities[$grpId];
						$secGroupName=$providerNameArr[$facID];
					}else{
						if($groupBy=='physician' || $groupBy=='operator'){
							$firstGroupName = $providerNameArr[$grpId];
						}else{
							$firstGroupName = $dept_name_arr[$grpId];
						}
					}
					
					$arrPatPayTot['cash']+=	$grpData['cash'];
					$arrPatPayTot['check']+= $grpData['check'];
					$arrPatPayTot['credit_card']+=	$grpData['credit_card'];
					$arrPatPayTot['eft']+=	$grpData['eft'];
					$arrPatPayTot['money_order']+=	$grpData['money_order'];
					$arrPatPayTot['veep']+=	$grpData['veep'];
					$arrPatPayTot['other']+=	$grpData['other'];
					$arrPatPayTot['by_patient']+=	$grpData['byPatient'];
					$arrPatPayTot['by_insurance']+=	$grpData['byInsurance'];
					
					//$arrPatPayTot['cc_types'][]
					
					$rowTot = $grpData['byPatient'] + $grpData['byInsurance'];
					
					$content_part .='
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$w_cols.'">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$w_cols.'">'.$secGroupName.'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['cash'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['check'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['credit_card'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['eft'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['money_order'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['veep'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['other'],2,1).'&nbsp;</td>';
						$content_part .='
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['byPatient'],2,1).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['byInsurance'],2,1).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
						if(empty($pay_method)==false)
						$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part .='
					</tr>';	
				}
			}
			
		}else{
			foreach($arrPostedPay as $grpId => $grpData){
					$rowTot=0;
					$firstGroupName='';
					$firstGrpTotal=array();
					if($groupBy=='physician' || $groupBy=='operator'){
						$firstGroupName = $providerNameArr[$grpId];
					}else{
						$firstGroupName = $dept_name_arr[$grpId];
					}
					
					$arrPatPayTot['cash']+=	$grpData['cash'];
					$arrPatPayTot['check']+= $grpData['check'];
					$arrPatPayTot['credit_card']+=	$grpData['credit_card'];
					$arrPatPayTot['eft']+=	$grpData['eft'];
					$arrPatPayTot['money_order']+=	$grpData['money_order'];
					$arrPatPayTot['veep']+=	$grpData['veep'];
					$arrPatPayTot['other']+=	$grpData['other'];
					$arrPatPayTot['by_patient']+=	$grpData['byPatient'];
					$arrPatPayTot['by_insurance']+=	$grpData['byInsurance'];
					
					$rowTot = $grpData['byPatient'] + $grpData['byInsurance'];
					
					$content_part .= '
					<tr>
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:left; width:'.$w_cols.'">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['cash'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['check'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')					
						$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['credit_card'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')					
						$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['eft'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')					
						$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['money_order'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')					
						$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['veep'],2,1).'&nbsp;</td>';
						if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')				
						$content_part .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['other'],2,1).'&nbsp;</td>';
						$content_part .='
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['byPatient'],2,1).'&nbsp;</td>					
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['byInsurance'],2,1).'&nbsp;</td>
						<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
						if(empty($pay_method)==false)
						$content_part .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part .='
					</tr>';	
				}
		}
		
		$postedBlockTot = $arrPatPayTot['by_patient'] + $arrPatPayTot['by_insurance'];
		
		// TOTAL
	
	
		if($pay_location=='1'){
			$firstGroupTitle='Facility';
			$secGroupTitle=($groupBy=='operator')? 'Operator' : 'Physician';
			$fac_col=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">'.$secGroupTitle.'</td>';
		}else if($showFacCol){
			$fac_col=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
		}
	
		$patient_html .=' 
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Posted Payments</td></tr>
		<tr id="">
			<td class="text_b_w" style="width:'.$first_col.'; text-align:center;">'.$firstGroupTitle.'</td>
			'.$fac_col;
			if(empty($pay_method)==true || $pay_method=='cash')
			$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Cash&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Check&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Credit Card&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">EFT&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Money Order&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">VEEP&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$patient_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">OTHER&nbsp;</td>';
			$patient_html .='
			<td class="text_b_w" style="text-align:center;" colspan="2">Payments By</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>';
			if(empty($pay_method)==false)
			$patient_html .='<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>';
			$patient_html .='
		</tr>
		<tr id="">
			<td class="text_b_w" style="text-align:right;" colspan="'.$secColspan.'"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Patient&nbsp;</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Insurance&nbsp;</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>';
			if(empty($pay_method)==false)
			$patient_html .='<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>';
		$patient_html .='
		</tr>'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" >Posted Payments Total&nbsp;:</td>';
			if($showFacCol || $pay_location=='1'){
			$patient_html .=' <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>';
			}
			if(empty($pay_method)==true || $pay_method=='cash')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['other'],2,1).'&nbsp;</td>';
			$patient_html .='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['by_patient'],2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['by_insurance'],2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($postedBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$patient_html .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
		$patient_html .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		
		if(sizeof($arrPostedCCTypeAmts)>0){
			$ccColspan=$colspan-2;
			$totCCType=0;
			foreach($arrPostedCCTypeAmts as $ccType=> $amt){
				$totCCType+=$amt;
				$patient_html.='<tr>';
					$patient_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
					$patient_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($amt,2,1).'&nbsp;</td>';
					$patient_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
				$patient_html.='</tr>';	
	
				$arrGrandCCTypes[$ccType]+=$amt;
			}
			$patient_html.='<tr>';
				$patient_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total of CC Types&nbsp;:</td>';
				$patient_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
				$patient_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$patient_html.='</tr>';	
	
			$patient_html.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
			
		}
		$patient_html .='</table>';		
	}
	
	
	// UNAPPLIED CI/CO PAYMENTS
	$arrCICOPayTot=array();
	$content_part= $cico_html = '';
	if(sizeof($arrCICONotApplied)>0){
		$dataExists=true;
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
				foreach($grpDataArr as $facId => $grpData){
				$rowTot=0;
				$firstGroupName='';
				$firstGrpTotal=array();
				$firstGroupName = $providerNameArr[$grpId];
					
				$secGroupName = ($pay_location=='1') ? $arr_sch_facilities[$facId] : $posFacilityArr[$facId];

				if($pay_location=='1'){
					$firstGroupName = $arr_sch_facilities[$grpId];
					$secGroupName=$providerNameArr[$facId];
				}else{
					if($groupBy=='physician' || $groupBy=='operator'){
						$firstGroupName = $providerNameArr[$grpId];
					}else{
						$firstGroupName = $dept_name_arr[$grpId];
					}
				}
				
				$arrCICOPayTot['cash']+=	$grpData['cash'];
				$arrCICOPayTot['check']+= $grpData['check'];
				$arrCICOPayTot['credit_card']+=	$grpData['credit_card'];
				$arrCICOPayTot['eft']+=	$grpData['eft'];
				$arrCICOPayTot['veep']+=	$grpData['veep'];
				$arrCICOPayTot['money_order']+=	$grpData['money_order'];
				$arrCICOPayTot['other']+=	$grpData['other'];
				
				$rowTot = $grpData['cash'] + $grpData['check'] + $grpData['credit_card'] + $grpData['eft'] + $grpData['money_order'] + $grpData['veep'] + $grpData['other'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word; width:'.$w_cols.'">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word; width:'.$w_cols.'">'.$secGroupName.'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grpData['other'],2,1).'&nbsp;</td>';
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part.='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part .='
				</tr>';			
				}
			}
		}
		else{
			foreach($arrCICONotApplied as $grpId => $grpData){
				$rowTot=0;
				$firstGroupName='';
				$firstGrpTotal=array();
				$firstGroupName = $providerNameArr[$grpId];
					
				if($groupBy=='facility'){
					$firstGroupName = ($pay_location=='1') ? $arr_sch_facilities[$grpId] : $posFacilityArr[$grpId];
				}		
				
				$arrCICOPayTot['cash']+=	$grpData['cash'];
				$arrCICOPayTot['check']+= $grpData['check'];
				$arrCICOPayTot['credit_card']+=	$grpData['credit_card'];
				$arrCICOPayTot['eft']+=	$grpData['eft'];
				$arrCICOPayTot['veep']+=	$grpData['veep'];
				$arrCICOPayTot['money_order']+=	$grpData['money_order'];
				$arrCICOPayTot['other']+=	$grpData['other'];
				
				$rowTot = $grpData['cash'] + $grpData['check'] + $grpData['credit_card'] + $grpData['eft'] + $grpData['money_order'] + $grpData['veep'] + $grpData['other'];
	
				//POPUP TITLES
				$cash_title=($grpData['cash_is_ref']=='cash' && $grpData['cash_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['cash_ref_amt'].' Refund"':'"';			
				$check_title=($grpData['check_is_ref']=='check' && $grpData['check_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['check_ref_amt'].' Refund"':'"';
				$cc_title=($grpData['credit_card_is_ref']=='credit_card' && $grpData['credit_card_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['credit_card_ref_amt'].' Refund"':'"';			
				$eft_title=($grpData['eft_is_ref']=='eft' && $grpData['eft_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['eft_ref_amt'].' Refund"':'"';			
				$mo_title=($grpData['money_order_is_ref']=='money_order' && $grpData['money_order_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['money_order_ref_amt'].' Refund"':'"';			
				$veep_title=($grpData['veep_is_ref']=='veep' && $grpData['veep_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['veep_ref_amt'].' Refund"':'"';
				$other_title=($grpData['other_is_ref']=='other' && $grpData['veep_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['other_ref_amt'].' Refund"':'"';					
	
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$cash_title.'">'.$CLSReports->numberFormat($grpData['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$check_title.'">'.$CLSReports->numberFormat($grpData['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$cc_title.'">'.$CLSReports->numberFormat($grpData['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$eft_title.'">'.$CLSReports->numberFormat($grpData['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$mo_title.'">'.$CLSReports->numberFormat($grpData['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$veep_title.'">'.$CLSReports->numberFormat($grpData['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$other_title.'">'.$CLSReports->numberFormat($grpData['other'],2,1).'&nbsp;</td>';
					$content_part .='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part .='
				</tr>';			
			}
		}
		
		$cicoBlockTot = $arrCICOPayTot['cash'] + $arrCICOPayTot['check'] + $arrCICOPayTot['credit_card'] + $arrCICOPayTot['eft'] + $arrCICOPayTot['money_order'] + $arrCICOPayTot['veep'] + $arrCICOPayTot['other'];
	
		// TOTAL
		$facCol='';
		$colspan=11 - $colsRemoved;
		$secColspan=1;
		$colWidth='9%';
		$total_cols = 10  - $colsRemoved;
		if($showFacCol || $pay_location=='1'){
			$colspan = 12 - $colsRemoved;
			$secColspan=2;
			$colWidth='9%';
			$total_cols = 11 - $colsRemoved;
		}
		$last_col=0;
		if(empty($pay_method)==false){ 	
			$last_col = 40;	
			$total_cols=$total_cols-1;
		}
		
		$first_col = "16";
		$w_cols = (100 - ($first_col+$last_col)) /$total_cols;
		
		$gd_first_col = $first_col;
		if($showFacCol || $pay_location=='1'){
			$gd_first_col += $w_cols;
		}
		$gd_col1 = $gd_first_col."%";
		$gd_col2 = $w_cols."%";
		$gd_col3 = $w_cols."%";
		$gd_col4 = $w_cols."%";
		$gd_col5 = $w_cols."%";
		$gd_col6 = $w_cols."%";
		$gd_col7 = $w_cols."%";
		$gd_col_opt= $last_col."%";
		
		$w_cols = $w_cols."%";
		$first_col = $first_col."%";
		$last_col = $last_col."%";
	

		if($pay_location=='1'){
			$naFirstGroupTitle='Facility';
			$secGroupTitle=($groupBy=='operator')? 'Operator' : 'Physician';
			$cico_fac=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">'.$secGroupTitle.'</td>';
		}else if($showFacCol){
			$cico_fac=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
		}

		$cico_html .=' 
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Unapplied CI/CO Payments</td></tr>
		<tr id="">
			<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">'.$naFirstGroupTitle.'&nbsp;</td>
			'.$cico_fac;
			if(empty($pay_method)==true || $pay_method=='cash')
			$cico_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Cash&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$cico_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Check&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$cico_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Credit Card&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$cico_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">EFT&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$cico_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Money Order&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$cico_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">VEEP&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$cico_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">OTHER&nbsp;</td>';
			$cico_html .='
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>';
			if(empty($pay_method)==false)
			$cico_html .='<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>';
			$cico_html .='
		</tr>'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$secColspan.'">CI/CO Payments Total&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrCICOPayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrCICOPayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrCICOPayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrCICOPayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrCICOPayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrCICOPayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrCICOPayTot['other'],2,1).'&nbsp;</td>';
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($cicoBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$cico_html .='<td class="text_10b" bgcolor="#FFFFFF"></td>';
			$cico_html .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		
		if(sizeof($arrCIOCCTypeAmts)>0){
			$ccColspan=$colspan-2;
			$totCCType=0;
			foreach($arrCIOCCTypeAmts as $ccType=> $amt){
				$totCCType+=$amt;
				$cico_html.='<tr>';
					$cico_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
					$cico_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($amt,2,1).'&nbsp;</td>';
					$cico_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
				$cico_html.='</tr>';
	
				$arrGrandCCTypes[$ccType]+=$amt;				
			}
			$cico_html.='<tr>';
				$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total of CC Types&nbsp;:</td>';
				$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
				$cico_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$cico_html.='</tr>';	
	
			$cico_html.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}
		
		$cico_html .='</table>';	
	}
	
	// UNAPPLIED PRE PAYMENTS
	$arrPrePayTot=array();
	$totCCType='';
	$content_part= $pre_pay_html = '';
	
	if(sizeof($arrPrePayNotApplied)>0){
		$dataExists=true;
		
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
			foreach($grpDataArr as $facId => $grpData){
				$rowTot=0;
				$firstGroupName='';
				$firstGrpTotal=array();
				$firstGroupName = $providerNameArr[$grpId];
				$secGroupName = ($pay_location=='1') ? $arr_sch_facilities[$facId] : $posFacilityArr[$facId];

				if($pay_location=='1'){
					$firstGroupName = $arr_sch_facilities[$grpId];
					$secGroupName=$providerNameArr[$facId];
				}else{
					if($groupBy=='physician' || $groupBy=='operator'){
						$firstGroupName = $providerNameArr[$grpId];
					}else{
						$firstGroupName = $dept_name_arr[$grpId];
					}
				}
				
				$arrPrePayTot['cash']+=	$grpData['cash'];
				$arrPrePayTot['check']+= $grpData['check'];
				$arrPrePayTot['credit_card']+=	$grpData['credit_card'];
				$arrPrePayTot['eft']+=	$grpData['eft'];
				$arrPrePayTot['money_order']+=	$grpData['money_order'];
				$arrPrePayTot['veep']+=	$grpData['veep'];
				$arrPrePayTot['other']+=	$grpData['other'];
				
				$redRow=($grpData['is_ref']>0)?';color:#FF0000':'';
				
				$rowTot = $grpData['cash'] + $grpData['check'] + $grpData['credit_card'] + $grpData['eft'] + $grpData['money_order'] + $grpData['veep'];
	
				//POPUP TILES
				$cash_title=($grpData['cash_is_ref']=='cash' && $grpData['cash_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['cash_ref_amt'].' Refund"':'"';
				$check_title=($grpData['check_is_ref']=='check' && $grpData['check_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['check_ref_amt'].' Refund"':'"';
				$cc_title=($grpData['credit_card_is_ref']=='credit_card' && $grpData['credit_card_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['credit_card_ref_amt'].' Refund"':'"';	
				$eft_title=($grpData['eft_is_ref']=='eft' && $grpData['eft_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['eft_ref_amt'].' Refund"':'"';			
				$mo_title=($grpData['money_order_is_ref']=='money_order' && $grpData['money_order_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['money_order_ref_amt'].' Refund"':'"';			
				$veep_title=($grpData['veep_is_ref']=='veep' && $grpData['veep_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['veep_ref_amt'].' Refund"':'"';
				$other_title=($grpData['other_is_ref']=='veep' && $grpData['other_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['other_ref_amt'].' Refund"':'"';
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word; width:'.$w_cols.'">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word; width:'.$w_cols.'">'.$secGroupName.'</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$cash_title.'">'.$CLSReports->numberFormat($grpData['cash'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$check_title.'">'.$CLSReports->numberFormat($grpData['check'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$cc_title.'">'.$CLSReports->numberFormat($grpData['credit_card'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$eft_title.'">'.$CLSReports->numberFormat($grpData['eft'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$mo_title.'">'.$CLSReports->numberFormat($grpData['money_order'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$veep_title.'">'.$CLSReports->numberFormat($grpData['veep'],2,1).'&nbsp;</td>';
					if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$veep_title.'">'.$CLSReports->numberFormat($grpData['other'],2,1).'&nbsp;</td>';
					$content_part .='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
					$content_part .='
				</tr>';			
			}
		}
		}else{
		foreach($arrPrePayNotApplied as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
				
			if($groupBy=='facility'){
				$firstGroupName = ($pay_location=='1') ? $arr_sch_facilities[$grpId] : $posFacilityArr[$grpId];
			}		
			
			$arrPrePayTot['cash']+=	$grpData['cash'];
			$arrPrePayTot['check']+= $grpData['check'];
			$arrPrePayTot['credit_card']+=	$grpData['credit_card'];
			$arrPrePayTot['eft']+=	$grpData['eft'];
			$arrPrePayTot['money_order']+=	$grpData['money_order'];
			$arrPrePayTot['veep']+=	$grpData['veep'];
			$arrPrePayTot['other']+=	$grpData['other'];
			
			$rowTot = $grpData['cash'] + $grpData['check'] + $grpData['credit_card'] + $grpData['eft'] + $grpData['money_order'] + $grpData['veep'] + $grpData['other'];
	
			//POPUP TITLES
			$cash_title=($grpData['cash_is_ref']=='cash' && $grpData['cash_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['cash_ref_amt'].' Refund"':'"';		
			$check_title=($grpData['check_is_ref']=='check' && $grpData['check_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['check_ref_amt'].' Refund"':'"';
			$cc_title=($grpData['credit_card_is_ref']=='credit_card' && $grpData['credit_card_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['credit_card_ref_amt'].' Refund"':'"';
			$eft_title=($grpData['eft_is_ref']=='eft' && $grpData['eft_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['eft_ref_amt'].' Refund"':'"';
			$mo_title=($grpData['money_order_is_ref']=='money_order' && $grpData['money_order_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['money_order_ref_amt'].' Refund"':'"';
			$veep_title=($grpData['veep_is_ref']=='veep' && $grpData['veep_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['veep_ref_amt'].' Refund"':'"';
			$other_title=($grpData['other_is_ref']=='other' && $grpData['other_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['other_ref_amt'].' Refund"':'"';
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word; width:'.$w_cols.'">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$cash_title.'">'.$CLSReports->numberFormat($grpData['cash'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')			
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$check_title.'">'.$CLSReports->numberFormat($grpData['check'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')			
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$cc_title.'">'.$CLSReports->numberFormat($grpData['credit_card'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')			
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$eft_title.'">'.$CLSReports->numberFormat($grpData['eft'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')			
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$mo_title.'">'.$CLSReports->numberFormat($grpData['money_order'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')			
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$veep_title.'">'.$CLSReports->numberFormat($grpData['veep'],2,1).'&nbsp;</td>';
				if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')			
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right '.$veep_title.'">'.$CLSReports->numberFormat($grpData['other'],2,1).'&nbsp;</td>';
				$content_part .='
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($rowTot,2,1).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part .='
			</tr>';			
		}
		}
		
		$prePayBlockTot = $arrPrePayTot['cash'] + $arrPrePayTot['check'] + $arrPrePayTot['credit_card'] + $arrPrePayTot['eft'] + $arrPrePayTot['money_order'] + $arrPrePayTot['veep'] + $arrPrePayTot['other'];
		
		// TOTAL
		$facCol='';
		$colspan=11 - $colsRemoved;
		$secColspan=1;
		$colWidth='9%';
		$total_cols = 10 - $colsRemoved;
		if($showFacCol || $pay_location=='1'){
			$colspan = 12 - $colsRemoved;
			$secColspan=2;
			$colWidth='9%';
			$total_cols = 11 - $colsRemoved;;
		}
		$last_col=0;
		if(empty($pay_method)==false){ 	
			$last_col = 40;	
			$total_cols=$total_cols-1;
		}
			
		$first_col = "16";
		$w_cols = (100 - ($first_col+$last_col)) /$total_cols;
		
		$gd_first_col = $first_col;
		if($showFacCol || $pay_location=='1'){
			$gd_first_col +=$w_cols;
		}
		$gd_col1 = $gd_first_col."%";
		$gd_col2 = $w_cols."%";
		$gd_col3 = $w_cols."%";
		$gd_col4 = $w_cols."%";
		$gd_col5 = $w_cols."%";
		$gd_col6 = $w_cols."%";
		$gd_col7 = $w_cols."%";
		$gd_col_opt= $last_col."%";
		
		$w_cols = $w_cols."%";
		$first_col = $first_col."%";
		$last_col = $last_col."%";
		
		if($pay_location=='1'){
			$naFirstGroupTitle='Facility';
			$secGroupTitle=($groupBy=='operator')? 'Operator' : 'Physician';
			$facCol=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">'.$secGroupTitle.'</td>';
		}else if($showFacCol){
			$facCol=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
		}

		$pre_pay_html .=' 
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">	
		<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Unapplied Pre-Payments</td></tr>
		<tr id="">
			<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">'.$naFirstGroupTitle.'</td>
			'.$facCol;
			if(empty($pay_method)==true || $pay_method=='cash')
			$pre_pay_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Cash&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$pre_pay_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Check&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$pre_pay_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Credit Card&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$pre_pay_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">EFT&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$pre_pay_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Money Order&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$pre_pay_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">VEEP&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$pre_pay_html .='<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">OTHER&nbsp;</td>';
			$pre_pay_html .='
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;"></td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>';
			if(empty($pay_method)==false)
			$pre_pay_html .='<td class="text_b_w" style="width:'.$last_col.';"></td>';
			$pre_pay_html .='
		</tr>'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$secColspan.'">Pre-Payments Total&nbsp;:</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['cash'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['check'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['credit_card'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['eft'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['money_order'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['veep'],2,1).'&nbsp;</td>';
			if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrPrePayTot['other'],2,1).'&nbsp;</td>';
			$pre_pay_html .='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($prePayBlockTot,2,1).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$pre_pay_html .='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		
		if(sizeof($arrPrePayCCTypeAmts)>0){
			$ccColspan=$colspan-2;
			$totCCType=0;
			foreach($arrPrePayCCTypeAmts as $ccType=> $amt){
				$totCCType+=$amt;
				$pre_pay_html.='<tr>';
					$pre_pay_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
					$pre_pay_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($amt,2,1).'&nbsp;</td>';
					$pre_pay_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
				$pre_pay_html.='</tr>';	
	
				$arrGrandCCTypes[$ccType]+=$amt;			
			}
			$pre_pay_html.='<tr>';
				$pre_pay_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total of CC Types&nbsp;:</td>';
				$pre_pay_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
				$pre_pay_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$pre_pay_html.='</tr>';	
	
			$pre_pay_html.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}	
		$pre_pay_html .='</table>';		
	}
}

//DELETED RECORDS
$delDataExists=false;
if(sizeof($delSortedGroup)>0){
	$dataExists=true;
	$delDataExists=true;
	$content_part='';
	$arrTotals=array();
	if($showFacCol || $pay_location=='1'){
		foreach($arrDelAmounts as $grpId => $grpDataArr){
			foreach($grpDataArr as $facId => $grpData){
				$rowTot=0;
				$firstGroupName='';
				$firstGrpTotal=array();
				$firstGroupName = $providerNameArr[$grpId];
				$secGroupName = $posFacilityArr[$facId];

				if($pay_location=='1'){
					$firstGroupName = $arr_sch_facilities[$grpId];
					$secGroupName=$providerNameArr[$facId];
				}else{
					if($groupBy=='physician' || $groupBy=='operator'){
						$firstGroupName = $providerNameArr[$grpId];
					}else{
						$firstGroupName = $dept_name_arr[$grpId];
					}
				}

				$total= $grpData['posted']+$grpData['cico']+$grpData['pre_payment']; 
				$arrTotals['posted']+= $grpData['posted'];
				$arrTotals['cico']+= $grpData['cico'];
				$arrTotals['pre_payment']+= $grpData['pre_payment'];
				$arrTotals['total']+= $total;
				
				$content_part.= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word">'.$secGroupName.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['posted'],2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['cico'],2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['pre_payment'],2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($total,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				</tr>';			
			}
		}
	}else{
		foreach($arrDelAmounts as $grpId => $grpData){
			$firstGroupName = $providerNameArr[$grpId];
			$total= $grpData['posted']+$grpData['cico']+$grpData['pre_payment']; 
			$arrTotals['posted']+= $grpData['posted'];
			$arrTotals['cico']+= $grpData['cico'];
			$arrTotals['pre_payment']+= $grpData['pre_payment'];
			$arrTotals['total']+= $total;
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['posted'],2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['cico'],2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['pre_payment'],2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($total,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>';			
		}
	}
	
	// TOTAL
	$facCol='';
	$colspan=6;
	$colWidth='9%';
	$firstColSpan=1;
	if($showFacCol || $pay_location=='1'){
		$colspan+=1;
		$firstColSpan=2;
	}
	$total_cols=$colspan-2;
	$first_col = "16";
	$last_col="20";
	$w_cols = floor((100 - ($first_col+$last_col)) / $total_cols);
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);
		
	$grand_first_col = 	$first_col;
	$grand_w_cols = $w_cols;
	
	$first_col = $first_col."%";
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";

	if($pay_location=='1'){
		$naFirstGroupTitle='Facility';
		$secGroupTitle='Physician/Operator';
		$facCol=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">'.$secGroupTitle.'</td>';
	}else if($showFacCol){
		$naFirstGroupTitle='Physician/Operator';
		$facCol=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
	}	
	
	$delPostedTotal=$arrTotals['posted'];
	$delCICOTotal=$arrTotals['cico'];
	$delPrePayTotal=$arrTotals['pre_payment'];

	$deleted_html.=' 
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">	
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Deleted Payments</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">'.$naFirstGroupTitle.' &nbsp;</td>
		'.$facCol.'
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Posted Payments&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">CI/CO Payments&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Pre-Payments&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>
		<td class="text_b_w"style="width:'.$last_col.'; text-align:right;">&nbsp;</td>
	</tr>'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$firstColSpan.'">Deleted Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['posted'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['cico'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['pre_payment'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['total'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	</table>';		
}


if($dataExists==true){
	$title=($delDataExists==true) ? 'Total Payments' : 'Grand Total';
	$by_patient=$by_insurance='';
	
	$cash= $arrPatPayTot['cash'] + $arrInsPayTot['cash'] + $arrCICOPayTot['cash'] + $arrPrePayTot['cash'];
	$check= $arrPatPayTot['check'] + $arrInsPayTot['check'] + $arrCICOPayTot['check'] + $arrPrePayTot['check'];
	$credit_card= $arrPatPayTot['credit_card'] + $arrInsPayTot['credit_card'] + $arrCICOPayTot['credit_card'] + $arrPrePayTot['credit_card'];
	$eft= $arrPatPayTot['eft'] + $arrInsPayTot['eft'] + $arrCICOPayTot['eft'] + $arrPrePayTot['eft'];
	$money_order= $arrPatPayTot['money_order'] + $arrInsPayTot['money_order'] + $arrCICOPayTot['money_order'] + $arrPrePayTot['money_order'];
	$veep= $arrPatPayTot['veep'] + $arrInsPayTot['veep'] + $arrCICOPayTot['veep'] + $arrPrePayTot['veep'];
	$other= $arrPatPayTot['other'] + $arrInsPayTot['other'] + $arrCICOPayTot['other'] + $arrPrePayTot['other'];
	if($groupBy=='department'){
		$by_patient= $arrPatPayTot['by_patient'] + $arrCICOPayTot['by_patient'] + $arrPrePayTot['by_patient'];
		$by_insurance= $arrPatPayTot['by_insurance'] + $arrCICOPayTot['by_insurance'] + $arrPrePayTot['by_insurance'];
	}
	
	$grandTot = $cash + $check + $credit_card + $eft + $money_order + $veep + $other;
	
	//FINAL HTML
	$grandCCType='';
	$colspan=11 -  $colsRemoved;
	$colWidth="9%";
	$firstColWidth="16%";
	$pdfColWidth="143px";
	$pdfColWidth="12%";
	//$pdfFirstColWidth="160px";
	$pdfFirstColWidth="15%";
	if($showFacCol || $pay_location=='1'){
		$colWidth="9%";
		$firstColWidth="16%";
		$pdfColWidth="125px";
		$pdfColWidth="12%";
		$pdfFirstColWidth="290px";
	}
	
	//GRAND IF CC TYPE AMOUNTS EXIST
	if(sizeof($arrGrandCCTypes)>0){
		$ccColspan=$colspan-2;
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
			$grandCCType.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Grand Total of CC Types&nbsp;:</td>';
			$grandCCType.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCCType,2,1).'&nbsp;</td>';
			$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$grandCCType.='</tr>';	

		$grandCCType.='<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
	}		
		
	$grand_html=' 
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">	
	<tr><td bgcolor="#FFFFFF" colspan="'.$colspan.'"></td></tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col1.'; text-align:right;">'.$title.'&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($cash,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($check,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($credit_card,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($eft,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($money_order,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($veep,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($other,2,1).'&nbsp;</td>';
		$grand_html.='
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($by_patient,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($by_insurance,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($grandTot,2,1).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col_opt.'; text-align:right;"></td>';
		$grand_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
	'.$grandCCType.'
	</table>';		

	$grand_pdf=' 
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">	
	<tr><td colspan="'.$colspan.'" style="width:100%"></td></tr>
	<tr><td bgcolor="#FFFFFF" colspan="'.$colspan.'"></td></tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col1.'; text-align:right;">'.$title.'&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($cash,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($check,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($credit_card,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($eft,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($money_order,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($veep,2,1).'&nbsp;</td>';
		if(empty($pay_method)==true && $pay_method!='cash' && $pay_method!='check' && $pay_method!='credit card' && $pay_method!='eft' && $pay_method!='money order' && $pay_method!='veep')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($other,2,1).'&nbsp;</td>';
		$grand_pdf.='
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($by_patient,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($by_insurance,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$CLSReports->numberFormat($grandTot,2,1).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col_opt.';">&nbsp;</td>';
		$grand_pdf.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	'.$grandCCType.'	
	</table>';		
}

//IF DELETED EXIST THEN DISPLAY BELOW GRAND TOTALS
if($delDataExists==true){
	
	$totalCollected=$postedBlockTot + $cicoBlockTot + $prePayBlockTot; 
	$totalDeleted=$delPostedTotal + $delCICOTotal + $delPrePayTotal; 
	$finalCollected = $totalCollected - $totalDeleted;
	
	$firstCol=$grand_first_col;
	if($showFacCol || $pay_location=='1'){
		$firstCol=$grand_first_col+$grand_w_cols;
	}
	$lastCol=100 - ($firstCol+$grand_w_cols).'%';
	$firstCol.='%';
	$grand_w_cols.='%';
	
	//FINAL HTML
	$grand_html1=' 
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">	
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
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Collected</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totalCollected,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted Posted Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($delPostedTotal,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted CI/CO Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($delCICOTotal,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted Pre-Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($delPrePayTotal,2,1).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Deleted</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totalDeleted,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Final Collected</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($finalCollected,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	</table>';	
}


//MANUALLY APPLIED RECORDS
if(sizeof($arrManuallyApplied)>0){
	$dataExists=true;
	$delDataExists=true;
	$content_part='';
	$arrTotals=array();
	
	foreach($arrManuallyApplied as $grpId => $grpData){
		$firstGroupName = $providerNameArr[$grpId];
		$total= $grpData['cico']+$grpData['pre_payment']; 
		$arrTotals['cico']+= $grpData['cico'];
		$arrTotals['pre_payment']+= $grpData['pre_payment'];
		$arrTotals['total']+= $total;
		
		$content_part .= '
		<tr>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['cico'],2,1).'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['pre_payment'],2,1).'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($total,2,1).'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>';			
	}
	
	// TOTAL
	$facCol='';
	$colspan=5;
	$colWidth='9%';
	$firstColSpan=1;
	if($showFacCol){
		$colspan+=1;
		$firstColSpan=2;
	}
	$total_cols=$colspan-2;
	$first_col = "16";
	$last_col="20";
	$w_cols = floor((100 - ($first_col+$last_col)) / $total_cols);
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);
		
	$grand_first_col = 	$first_col;
	$grand_w_cols = $w_cols;
	
	$first_col = $first_col."%";
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	
	if($showFacCol){
		$facCol = '<td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
	}

	$delCICOTotal=$arrTotals['cico'];
	$delPrePayTotal=$arrTotals['pre_payment'];

	$manually_applied_html.=' 
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">	
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Manually Applied Amounts (Between '.$Start_date.' and '.$End_date.')</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">Physician/Operator &nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">CI/CO Payments&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Pre-Payments&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>
		<td class="text_b_w" style="width:'.$last_col.'; text-align:right;">&nbsp;</td>
	</tr>'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$firstColSpan.'">Manually Applied Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['cico'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['pre_payment'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['total'],2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	</table>';		
}

$page_content=
$patient_html.
$cico_html.
$pre_pay_html.
$grand_html.
$deleted_html.
$grand_html1.
$manually_applied_html
;

$pdf_content=
$patient_html.
$cico_html.
$pre_pay_html.
$grand_pdf.
$deleted_html.
$grand_html1.
$manually_applied_html
;
?>
