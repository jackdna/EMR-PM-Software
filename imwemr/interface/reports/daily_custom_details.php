<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
FILE : payments_details.php
PURPOSE :  PAYMENTS DETAIL VIEW
ACCESS TYPE : INCLUDED
*/
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

}else if($groupBy=='department'){
	$firstGroupTitle = 'Department';
	$subTotalTitle = 'Department Total';
}else if($groupBy=='groups'){
	$firstGroupTitle = 'Group';
	$subTotalTitle = 'Group Total';
}

$prePayDateLabel='DOT';
if($DateRangeFor=='dop'){
	$prePayDateLabel='DOP';
}

$colsRemoved= (empty($pay_method)==false) ? 4 : 0;

// POSTED PAYMENTS
$arrPatPayTot=array();
$arrPostedTransCount=array();
$content_part= $patient_html='';
if(sizeof($arrPostedPay)>0){
	$dataExists=true;
	$colspan= 13;
	$colspan-=$colsRemoved;
	$total_cols = 12 - $colsRemoved;

	if(empty($pay_method)==false){	
		$secColspan= 4 ;
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
	
	$gd_first_col = $first_col+($w_cols*3);
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


	if($showFacCol){
		foreach($arrPostedPay as $grpId => $grpDataArr){
			$firstGrpTotal=array();
			$firstGroupName='';
			
			if($groupBy=='physician' || $groupBy=='operator'){
				$firstGroupName = $providerNameArr[$grpId];
			}else{
				$firstGroupName = $dept_name_arr[$grpId];
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';

			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal = array();

				$facilityName = $posFacilityArr[$facId];
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Facility - '.$facilityName.'</td></tr>';
			
				foreach($grpData as $eid => $grpDetail){
					$rowTot=0;
					$pName = explode('~', $grpDetail['pat_name']);
					
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					
					$facGrpTotal['cash']+=	$grpDetail['cash'];
					$facGrpTotal['check']+= $grpDetail['check'];
					$facGrpTotal['credit_card']+=	$grpDetail['credit_card'];
					$facGrpTotal['eft']+=	$grpDetail['eft'];
					$facGrpTotal['money_order']+=	$grpDetail['money_order'];
					$facGrpTotal['veep']+=	$grpDetail['veep'];
					$facGrpTotal['by_patient']+=$grpDetail['byPatient'];
					$facGrpTotal['by_insurance']+=$grpDetail['byInsurance'];
					
					//TRANSACTION COUNTS
					if($grpDetail['cash']>0)$arrPostedTransCount['cash']+=1;
					if($grpDetail['check']>0)$arrPostedTransCount['check']+=1;
					if($grpDetail['credit_card']>0)$arrPostedTransCount['credit_card']+=1;
					if($grpDetail['money_order']>0)$arrPostedTransCount['money_order']+=1;
					if($grpDetail['eft']>0)$arrPostedTransCount['eft']+=1;
					if($grpDetail['veep']>0)$arrPostedTransCount['veep']+=1;
		
		
					$rowTot = $grpDetail['byPatient'] + $grpDetail['byInsurance'];
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dor'].'</td>';
						if(empty($pay_method)==true || $pay_method=='cash')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['cash'],2).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='check')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['check'],2).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='credit card')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['credit_card'],2).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='eft')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['eft'],2).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='money order')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['money_order'],2).'&nbsp;</td>';
						if(empty($pay_method)==true || $pay_method=='veep')
						$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['veep'],2).'&nbsp;</td>';
						$content_part .='
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['byPatient'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['byInsurance'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
			$firstGrpTotal['by_patient']+=	$facGrpTotal['by_patient'];
			$firstGrpTotal['by_insurance']+=	$facGrpTotal['by_insurance'];
			
			// SUB TOTAL
			$subTot = $facGrpTotal['by_patient'] + $facGrpTotal['by_insurance'];
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">Facility Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['cash'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['check'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['credit_card'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['eft'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['money_order'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['veep'],2).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['by_patient'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['by_insurance'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot,2).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			';
		}
		//PHYSICIAN TOTAL
		$arrPatPayTot['cash']+=	$firstGrpTotal['cash'];
		$arrPatPayTot['check']+= $firstGrpTotal['check'];
		$arrPatPayTot['credit_card']+=	$firstGrpTotal['credit_card'];
		$arrPatPayTot['eft']+=	$firstGrpTotal['eft'];
		$arrPatPayTot['money_order']+=	$firstGrpTotal['money_order'];
		$arrPatPayTot['veep']+=	$firstGrpTotal['veep'];
		$arrPatPayTot['by_patient']+=	$firstGrpTotal['by_patient'];
		$arrPatPayTot['by_insurance']+=	$firstGrpTotal['by_insurance'];
		
		// SUB TOTAL
		$subTot1 = $firstGrpTotal['by_patient'] + $firstGrpTotal['by_insurance'];
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">'.$firstGroupTitle.' Total :</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['cash'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['check'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['credit_card'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['eft'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['money_order'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['veep'],2).'&nbsp;</td>';
			$content_part.='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['by_patient'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['by_insurance'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot1,2).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$content_part.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		';
	}
	}else{
		foreach($arrPostedPay as $grpId => $grpData){
		$rowTot=0;
		$firstGroupName='';
		$firstGrpTotal=array();
		
		
		if($groupBy=='facility'){
			$firstGroupName = $posFacilityArr[$grpId];
		}else if($groupBy=='physician' || $groupBy=='operator'){
			$firstGroupName = $providerNameArr[$grpId];
		}else if($groupBy=='groups'){
			$firstGroupName = $group_id_arr[$grpId];
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
			
			$firstGrpTotal['cash']+=	$grpDetail['cash'];
			$firstGrpTotal['check']+= $grpDetail['check'];
			$firstGrpTotal['credit_card']+=	$grpDetail['credit_card'];
			$firstGrpTotal['eft']+=	$grpDetail['eft'];
			$firstGrpTotal['money_order']+=	$grpDetail['money_order'];
			$firstGrpTotal['veep']+=	$grpDetail['veep'];
			$firstGrpTotal['by_patient']+=$grpDetail['byPatient'];
			$firstGrpTotal['by_insurance']+=$grpDetail['byInsurance'];
			
			//TRANSACTION COUNTS
			if($grpDetail['cash']>0)$arrPostedTransCount['cash']+=1;
			if($grpDetail['check']>0)$arrPostedTransCount['check']+=1;
			if($grpDetail['credit_card']>0)$arrPostedTransCount['credit_card']+=1;
			if($grpDetail['money_order']>0)$arrPostedTransCount['money_order']+=1;
			if($grpDetail['eft']>0)$arrPostedTransCount['eft']+=1;
			if($grpDetail['veep']>0)$arrPostedTransCount['veep']+=1;
			
			
			$rowTot = $grpDetail['byPatient'] + $grpDetail['byInsurance'];
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dor'].'</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['cash'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['check'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['credit_card'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['eft'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['money_order'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['veep'],2).'&nbsp;</td>';
				$content_part.='
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['byPatient'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['byInsurance'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
		$arrPatPayTot['by_patient']+=	$firstGrpTotal['by_patient'];
		$arrPatPayTot['by_insurance']+=	$firstGrpTotal['by_insurance'];
		
		// SUB TOTAL
		$subTot = $firstGrpTotal['by_patient'] + $firstGrpTotal['by_insurance'];
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">'.$firstGroupTitle.' Total :</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['cash'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['check'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['credit_card'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['eft'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['money_order'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['veep'],2).'&nbsp;</td>';
			$content_part.='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['by_patient'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['by_insurance'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot,2).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$content_part.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		';
	}
	}
	
	$postedBlockTot = $arrPatPayTot['by_patient'] + $arrPatPayTot['by_insurance'];
	

	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">	
	<tr><td class="text_b_w" colspan="'.$colspan.'">Posted Payments</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">&nbsp;Patient Name-Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">Enc. Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">DOS</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">DOR</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">CC&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">MO&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">VEEP&nbsp;</td>';
		$header.='
		<td style="width:'.($w_cols*2).'%; text-align:center;" colspan="2" class="text_b_w">Payments By</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$header.='<td style="width:'.$last_col.';" class="text_b_w">&nbsp;</td>';
		$header.='
	</tr>
	<tr>
		<td style="text-align:center;" colspan="'.$secColspan.'" class="text_b_w"></td>
		<td style="text-align:center;" class="text_b_w">Patient</td>
		<td style="text-align:center;" class="text_b_w">Insurance</td>
		<td style="text-align:right;" class="text_b_w"></td>';
		if(empty($pay_method)==false)
		$header .='<td>&nbsp;</td>';
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
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($amt,2).'&nbsp;</td>';
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$cctype_html.='</tr>';	

			$arrGrandCCTypes[$ccType]+=$amt;
		}
		$cctype_html.='<tr>';
			$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total of CC Types&nbsp;:</td>';
			$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totCCType,2).'&nbsp;</td>';
			$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$cctype_html.='</tr>';	

		$cctype_html.='<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	}
		
	//PAGE HTML
	$totalPostedCount=$arrPostedTransCount['cash']+$arrPostedTransCount['check']+$arrPostedTransCount['credit_card']+$arrPostedTransCount['eft']+$arrPostedTransCount['money_order']+$arrPostedTransCount['veep'];
	$patient_html .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">Posted Payments Total&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['cash'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['check'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['credit_card'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['eft'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['money_order'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['veep'],2).'&nbsp;</td>';
		$patient_html .='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['by_patient'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['by_insurance'],2).'&nbsp;</td>		
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($postedBlockTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
		$patient_html .='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">Transactions Count&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['cash'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['check'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['credit_card'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['eft'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['money_order'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['veep'].'&nbsp;</td>';
		$patient_html .='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['by_patient'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['by_insurance'].'&nbsp;</td>		
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$totalPostedCount.'&nbsp;</td>';
		if(empty($pay_method)==false)
		$patient_html .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
		$patient_html .='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	'.$cctype_html.'
	</table>';
	
	//PDF HTML
	$patient_html_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">Posted Payments Total&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['cash'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['check'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['credit_card'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['eft'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['money_order'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['veep'],2).'&nbsp;</td>';
		$patient_html_PDF.='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['by_patient'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['by_insurance'],2).'&nbsp;</td>		
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($postedBlockTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$patient_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
		$patient_html_PDF.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">Transactions Count&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['cash'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['check'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['credit_card'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['eft'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['money_order'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['veep'].'&nbsp;</td>';
		$patient_html_PDF .='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['by_patient'].'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPostedTransCount['by_insurance'].'&nbsp;</td>		
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$totalPostedCount.'&nbsp;</td>';
		if(empty($pay_method)==false)
		$patient_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
		$patient_html_PDF .='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
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
	$colspan= 13;
	$colspan-=$colsRemoved;
	$total_cols = 12 - $colsRemoved;

	$last_col=0;
	if(empty($pay_method)==false){ 	
		$last_col = 30;	
		$total_cols=$total_cols-1;
	}
	
	$first_col = 16;
	$w_cols = (100 - ($first_col + $last_col)) /$total_cols;
	
	$gd_first_col = $first_col+($w_cols*3);
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

	if($showFacCol){
	foreach($arrCICONotApplied as $grpId => $grpDataArr){
		$firstGroupName='';
		$firstGrpTotal=array();
		$firstGroupName = $providerNameArr[$grpId];

		$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$naFirstGroupTitle.' - '.$firstGroupName.'</td></tr>';

		foreach($grpDataArr as $facId => $grpData){
			$facGrpTotal=array();
			$facilityName = $posFacilityArr[$facId];
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Facility - '.$facilityName.'</td></tr>';
			
			foreach($grpData as $eid => $grpDetail){
				$rowTot=0;
				$pName = explode('~', $grpDetail['pat_name']);
				
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				
				$facGrpTotal['cash']+= $grpDetail['cash'];
				$facGrpTotal['check']+= $grpDetail['check'];
				$facGrpTotal['credit_card']+=	$grpDetail['credit_card'];
				$facGrpTotal['eft']+=	$grpDetail['eft'];
				$facGrpTotal['veep']+=	$grpDetail['veep'];
				$facGrpTotal['money_order']+=	$grpDetail['money_order'];

				//TRANSACTION COUNTS
				if($grpDetail['cash']>0)$arrCICOTransCount['cash']+=1;
				if($grpDetail['check']>0)$arrCICOTransCount['check']+=1;
				if($grpDetail['credit_card']>0)$arrCICOTransCount['credit_card']+=1;
				if($grpDetail['money_order']>0)$arrCICOTransCount['money_order']+=1;
				if($grpDetail['eft']>0)$arrCICOTransCount['eft']+=1;
				if($grpDetail['veep']>0)$arrCICOTransCount['veep']+=1;
				
			
				$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'];
				$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
				
				//POPUP TITLES
				$cash_title=($grpDetail['is_ref']=='cash' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$check_title=($grpDetail['is_ref']=='check' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$cc_title=($grpDetail['is_ref']=='credit_card' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$eft_title=($grpDetail['is_ref']=='eft' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$mo_title=($grpDetail['is_ref']=='money_order' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$veep_title=($grpDetail['is_ref']=='veep' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$cash_title.'>'.$showCurrencySymbol.number_format($grpDetail['cash'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$check_title.'>'.$showCurrencySymbol.number_format($grpDetail['check'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$cc_title.'>'.$showCurrencySymbol.number_format($grpDetail['credit_card'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$eft_title.'>'.$showCurrencySymbol.number_format($grpDetail['eft'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$mo_title.'>'.$showCurrencySymbol.number_format($grpDetail['money_order'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$veep_title.'>'.$showCurrencySymbol.number_format($grpDetail['veep'],2).'&nbsp;</td>';

					$content_part.='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
		// FACILITY TOTAL
		$subTot = $facGrpTotal['cash'] + $facGrpTotal['check'] + $facGrpTotal['credit_card'] + $facGrpTotal['eft'] + $facGrpTotal['money_order'] + $facGrpTotal['veep'];
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">Fcility Total :</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['cash'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['check'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['credit_card'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['eft'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['money_order'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['veep'],2).'&nbsp;</td>';
			$content_part.='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot,2).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$content_part.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		';			
		}
		
	$arrCICOPayTot['cash']+=	$firstGrpTotal['cash'];
	$arrCICOPayTot['check']+= $firstGrpTotal['check'];
	$arrCICOPayTot['credit_card']+=	$firstGrpTotal['credit_card'];
	$arrCICOPayTot['eft']+=	$firstGrpTotal['eft'];
	$arrCICOPayTot['money_order']+=	$firstGrpTotal['money_order'];
	$arrCICOPayTot['veep']+=	$firstGrpTotal['veep'];
	
	$subTot1 = $firstGrpTotal['cash'] + $firstGrpTotal['check'] + $firstGrpTotal['credit_card'] + $firstGrpTotal['eft'] + $firstGrpTotal['money_order'] + $firstGrpTotal['veep'];
	// FIRST GROUP TOTAL
	$content_part.=' 
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">'.$naFirstGroupTitle.' Total :</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['cash'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['check'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['credit_card'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['eft'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['money_order'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['veep'],2).'&nbsp;</td>';
		$content_part.='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot1,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>';
		$content_part.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	';			
	}
	}else{
		foreach($arrCICONotApplied as $grpId => $grpData){
		$rowTot=0;
		$firstGroupName='';
		$firstGrpTotal=array();
		$firstGroupName = $providerNameArr[$grpId];
			
		if($groupBy=='facility'){
			$firstGroupName = $posFacilityArr[$grpId];
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
			
			$firstGrpTotal['cash']+= $grpDetail['cash'];
			$firstGrpTotal['check']+= $grpDetail['check'];
			$firstGrpTotal['credit_card']+=	$grpDetail['credit_card'];
			$firstGrpTotal['eft']+=	$grpDetail['eft'];
			$firstGrpTotal['money_order']+=	$grpDetail['money_order'];
			$firstGrpTotal['veep']+=	$grpDetail['veep'];

			//TRANSACTION COUNTS
			if($grpDetail['cash']>0)$arrCICOTransCount['cash']+=1;
			if($grpDetail['check']>0)$arrCICOTransCount['check']+=1;
			if($grpDetail['credit_card']>0)$arrCICOTransCount['credit_card']+=1;
			if($grpDetail['money_order']>0)$arrCICOTransCount['money_order']+=1;
			if($grpDetail['eft']>0)$arrCICOTransCount['eft']+=1;
			if($grpDetail['veep']>0)$arrCICOTransCount['veep']+=1;
		
			$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
			$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'];
			
			//POPUP TITLES
			$cash_title=($grpDetail['is_ref']=='cash' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$check_title=($grpDetail['is_ref']=='check' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$cc_title=($grpDetail['is_ref']=='credit_card' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$eft_title=($grpDetail['is_ref']=='eft' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$mo_title=($grpDetail['is_ref']=='money_order' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$veep_title=($grpDetail['is_ref']=='veep' && $grpDetail['ref_amt']>0)?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
						
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$cash_title.'>'.$showCurrencySymbol.number_format($grpDetail['cash'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$check_title.'>'.$showCurrencySymbol.number_format($grpDetail['check'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$cc_title.'>'.$showCurrencySymbol.number_format($grpDetail['credit_card'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$eft_title.'>'.$showCurrencySymbol.number_format($grpDetail['eft'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$mo_title.'>'.$showCurrencySymbol.number_format($grpDetail['money_order'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$veep_title.'>'.$showCurrencySymbol.number_format($grpDetail['veep'],2).'&nbsp;</td>';
				$content_part .='
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
		
		// SUB TOTAL
		$subTot = $firstGrpTotal['cash'] + $firstGrpTotal['check'] + $firstGrpTotal['credit_card'] + $firstGrpTotal['eft'] + $firstGrpTotal['money_order'] + $firstGrpTotal['veep'];
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">'.$naFirstGroupTitle.' Total :</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['cash'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['check'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['credit_card'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['eft'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['money_order'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['veep'],2).'&nbsp;</td>';
			$content_part.='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot,2).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>';
			$content_part.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		';			
	}
	}
	
	$cicoBlockTot = $arrCICOPayTot['cash'] + $arrCICOPayTot['check'] + $arrCICOPayTot['credit_card'] + $arrCICOPayTot['eft'] + $arrCICOPayTot['money_order'] + $arrCICOPayTot['veep'];
	
	
	// TOTAL
	// HEADER
	$header='';
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td colspan="'.$colspan.'" class="text_b_w">Unapplied CI/CO Payments</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">&nbsp;Patient Name-Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w"></td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">Paid Date</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w"></td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Credit Card&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">MO&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">VEEP&nbsp;</td>';
		$header.='
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w"></td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w"></td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$header.='<td style="width:'.$last_col.'; text-align:right;" class="text_b_w"></td>';
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
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($amt,2).'&nbsp;</td>';
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$cctype_html.='</tr>';

			$arrGrandCCTypes[$ccType]+=$amt;				
		}
		$cctype_html.='<tr>';
			$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total of CC Types&nbsp;:</td>';
			$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totCCType,2).'&nbsp;</td>';
			$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$cctype_html.='</tr>';	

		$cctype_html.='<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	}

	
	// PAGE HTML
	$totalCICOCount=$arrCICOTransCount['cash']+$arrCICOTransCount['check']+$arrCICOTransCount['money_order']+$arrCICOTransCount['credit_card']+$arrCICOTransCount['eft']+$arrCICOTransCount['veep'];
	$cico_html.=
	$header.
	'<table class="rpt_table rpt rpt_table-bordered rpt_padding">'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">CI/CO Payments Total&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['cash'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['check'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['credit_card'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['eft'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['money_order'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['veep'],2).'&nbsp;</td>';
		$cico_html.='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($cicoBlockTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$cico_html.='<td bgcolor="#FFFFFF" style="width:'.$last_col.'; text-align:right;"></td>';
		$cico_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">Transactions Count&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['cash'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['check'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['credit_card'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['eft'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['money_order'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['veep'].'&nbsp;</td>';
		$cico_html.='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$totalCICOCount.'&nbsp;</td>';
		if(empty($pay_method)==false)
		$cico_html.='<td bgcolor="#FFFFFF" style="width:'.$last_col.'; text-align:right;"></td>';
		$cico_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	'.$cctype_html.'
	</table>';	
	
	//PDF HTML
	$cico_html_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4" class="text_b_w">CI/CO Payments Total&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['cash'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['check'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['credit_card'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['eft'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['money_order'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrCICOPayTot['veep'],2).'&nbsp;</td>';
		$cico_html_PDF.='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($cicoBlockTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>';
		$cico_html_PDF.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['cash'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['check'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['credit_card'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['eft'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['money_order'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrCICOTransCount['veep'].'&nbsp;</td>';
		$cico_html_PDF.='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$totalCICOCount.'&nbsp;</td>';
		if(empty($pay_method)==false)
		$cico_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>';
		$cico_html_PDF.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
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
	$colspan= 13;
	$colspan-=$colsRemoved;
	$total_cols = 12 - $colsRemoved;

	$last_col=0;
	if(empty($pay_method)==false){ 	
		$last_col = 30;	
		$total_cols=$total_cols-1;
	}
	
	$first_col = 16;
	$w_cols = (100 - ($first_col + $last_col)) /$total_cols;
	
	$gd_first_col = $first_col+($w_cols*3);
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

	if($showFacCol){
	foreach($arrPrePayNotApplied as $grpId => $grpDataArr){
		$firstGroupName='';
		$firstGrpTotal=array();
		$firstGroupName = $providerNameArr[$grpId];

		if($groupBy=='facility'){
			$firstGroupName = $posFacilityArr[$grpId];
		}	
		$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$naFirstGroupTitle.' - '.$firstGroupName.'</td></tr>';
	
		foreach($grpDataArr as $facId => $grpData){
			$facGrpTotal=array();
			$facilityName = $posFacilityArr[$facId];
				
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Facility - '.$facilityName.'</td></tr>';
			
			foreach($grpData as $eid => $grpDetail){
				$rowTot=0;
				$pName = explode('~', $grpDetail['pat_name']);
				
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				
				$facGrpTotal['cash']+= $grpDetail['cash'];
				$facGrpTotal['check']+= $grpDetail['check'];
				$facGrpTotal['credit_card']+=	$grpDetail['credit_card'];
				$facGrpTotal['eft']+=	$grpDetail['eft'];
				$facGrpTotal['money_order']+=	$grpDetail['money_order'];
				$facGrpTotal['veep']+=	$grpDetail['veep'];

				//TRANSACTION COUNTS
				if($grpDetail['cash']>0)$arrPrePayTransCount['cash']+=1;
				if($grpDetail['check']>0)$arrPrePayTransCount['check']+=1;
				if($grpDetail['credit_card']>0)$arrPrePayTransCount['credit_card']+=1;
				if($grpDetail['money_order']>0)$arrPrePayTransCount['money_order']+=1;
				if($grpDetail['eft']>0)$arrPrePayTransCount['eft']+=1;
				if($grpDetail['veep']>0)$arrPrePayTransCount['veep']+=1;
			
				$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'];		
				$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';

				//POPUP TITLES
				$cash_title=($grpDetail['is_ref']=='cash')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$check_title=($grpDetail['is_ref']=='check')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$cc_title=($grpDetail['is_ref']=='credit_card')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$eft_title=($grpDetail['is_ref']=='eft')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$mo_title=($grpDetail['is_ref']=='money_order')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				$veep_title=($grpDetail['is_ref']=='veep')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dor'].'</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$cash_title.'>'.$showCurrencySymbol.number_format($grpDetail['cash'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$check_title.'>'.$showCurrencySymbol.number_format($grpDetail['check'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$cc_title.'>'.$showCurrencySymbol.number_format($grpDetail['credit_card'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$eft_title.'>'.$showCurrencySymbol.number_format($grpDetail['eft'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$mo_title.'>'.$showCurrencySymbol.number_format($grpDetail['money_order'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$veep_title.'>'.$showCurrencySymbol.number_format($grpDetail['veep'],2).'&nbsp;</td>';
					$content_part .='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
			
			// Facility TOTAL
			$subTot = $facGrpTotal['cash'] + $facGrpTotal['check'] + $facGrpTotal['credit_card'] + $facGrpTotal['eft'] + $facGrpTotal['money_order'] + $facGrpTotal['veep'];
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">Facility Total :</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['cash'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['check'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['credit_card'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['eft'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['money_order'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['veep'],2).'&nbsp;</td>';
				$content_part.='
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot,2).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part.='
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			';				
		}
		
		$arrPrePayTot['cash']+=	$firstGrpTotal['cash'];
		$arrPrePayTot['check']+= $firstGrpTotal['check'];
		$arrPrePayTot['credit_card']+=	$firstGrpTotal['credit_card'];
		$arrPrePayTot['eft']+=	$firstGrpTotal['eft'];
		$arrPrePayTot['money_order']+=	$firstGrpTotal['money_order'];
		$arrPrePayTot['veep']+=	$firstGrpTotal['veep'];
		
		// FIRST GROUP TOTAL
		$subTot = $firstGrpTotal['cash'] + $firstGrpTotal['check'] + $firstGrpTotal['credit_card'] + $firstGrpTotal['eft'] + $firstGrpTotal['money_order'] + $firstGrpTotal['veep'];
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">'.$naFirstGroupTitle.' Total :</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['cash'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['check'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['credit_card'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['eft'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['money_order'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['veep'],2).'&nbsp;</td>';
			$content_part.='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot,2).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
			$content_part.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		';				
	}
	}else{
	foreach($arrPrePayNotApplied as $grpId => $grpData){
	
		$rowTot=0;
		$firstGroupName='';
		$firstGrpTotal=array();
		$firstGroupName = $providerNameArr[$grpId];
			
		if($groupBy=='facility'){
			$firstGroupName = $posFacilityArr[$grpId];
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
			
			$firstGrpTotal['cash']+= $grpDetail['cash'];
			$firstGrpTotal['check']+= $grpDetail['check'];
			$firstGrpTotal['credit_card']+=	$grpDetail['credit_card'];
			$firstGrpTotal['eft']+=	$grpDetail['eft'];
			$firstGrpTotal['money_order']+=	$grpDetail['money_order'];
			$firstGrpTotal['veep']+=	$grpDetail['veep'];

			//TRANSACTION COUNTS
			if($grpDetail['cash']>0)$arrPrePayTransCount['cash']+=1;
			if($grpDetail['check']>0)$arrPrePayTransCount['check']+=1;
			if($grpDetail['credit_card']>0)$arrPrePayTransCount['credit_card']+=1;
			if($grpDetail['money_order']>0)$arrPrePayTransCount['money_order']+=1;
			if($grpDetail['eft']>0)$arrPrePayTransCount['eft']+=1;
			if($grpDetail['veep']>0)$arrPrePayTransCount['veep']+=1;
		
			$redRow=($grpDetail['is_ref'])?';color:#FF0000':'';
			
			$rowTot = $grpDetail['cash'] + $grpDetail['check'] + $grpDetail['credit_card'] + $grpDetail['eft'] + $grpDetail['money_order'] + $grpDetail['veep'];		

			//POPUP TITLES
			$cash_title=($grpDetail['is_ref']=='cash')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$check_title=($grpDetail['is_ref']=='check')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$cc_title=($grpDetail['is_ref']=='credit_card')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$eft_title=($grpDetail['is_ref']=='eft')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$mo_title=($grpDetail['is_ref']=='money_order')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			$veep_title=($grpDetail['is_ref']=='veep')?$redRow.'"  title="'.$showCurrencySymbol.$grpDetail['ref_amt'].' Refund"':'"';
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dor'].'</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$cash_title.'>'.$showCurrencySymbol.number_format($grpDetail['cash'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$check_title.'>'.$showCurrencySymbol.number_format($grpDetail['check'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$cc_title.'>'.$showCurrencySymbol.number_format($grpDetail['credit_card'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$eft_title.'>'.$showCurrencySymbol.number_format($grpDetail['eft'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$mo_title.'>'.$showCurrencySymbol.number_format($grpDetail['money_order'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'" '.$veep_title.'>'.$showCurrencySymbol.number_format($grpDetail['veep'],2).'&nbsp;</td>';
				$content_part .='
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
		
		// SUB TOTAL
		$subTot = $firstGrpTotal['cash'] + $firstGrpTotal['check'] + $firstGrpTotal['credit_card'] + $firstGrpTotal['eft'] + $firstGrpTotal['money_order'] + $firstGrpTotal['veep'];
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">'.$naFirstGroupTitle.' Total :</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['cash'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['check'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['credit_card'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['eft'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['money_order'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['veep'],2).'&nbsp;</td>';
			$content_part.='
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($subTot,2).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part.='<td class="text_10b" bgcolor="#FFFFFF"></td>';
			$content_part.='
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		';				
	}
	}
	
	$prePayBlockTot = $arrPrePayTot['cash'] + $arrPrePayTot['check'] + $arrPrePayTot['credit_card'] + $arrPrePayTot['eft'] + $arrPrePayTot['money_order'] + $arrPrePayTot['veep'];
	

	
	// TOTAL
	// HEADER
	$header='';
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr class="text_b_w"><td colspan="'.$colspan.'">Unapplied Pre-Payments</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">&nbsp;Patient Name-Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w"></td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">DOT</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">DOR</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Credit Card&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">MO&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$header.='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">VEEP&nbsp;</td>';
		$header.='
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w"></td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w"></td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$header.='<td style="width:'.$last_col.';" class="text_b_w"></td>';
		$header.='
	</tr>
	</table>';

	$cctype_html='';
	//CC TYPE AMOUNTS
	if(sizeof($arrPrePayCCTypeAmts)>0){
		$ccColspan=11 - $colsRemoved;
		$totCCType=0;
		foreach($arrPrePayCCTypeAmts as $ccType=> $amt){
			$totCCType+=$amt;
			$cctype_html.='<tr>';
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($amt,2).'&nbsp;</td>';
				$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$cctype_html.='</tr>';

			$arrGrandCCTypes[$ccType]+=$amt;				
		}
		$cctype_html.='<tr>';
			$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total of CC Types&nbsp;:</td>';
			$cctype_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totCCType,2).'&nbsp;</td>';
			$cctype_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$cctype_html.='</tr>';	

		$cctype_html.='<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	}
	
	//PAGE
	$totalPrePayCount=$arrPrePayTransCount['cash']+$arrPrePayTransCount['check']+$arrPrePayTransCount['credit_card']+$arrPrePayTransCount['money_order']+$arrPrePayTransCount['eft']+$arrPrePayTransCount['veep'];
	$pre_pay_html .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">Pre-Payments Total&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['cash'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['check'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['credit_card'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['eft'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['money_order'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['veep'],2).'&nbsp;</td>';
		$pre_pay_html .='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($prePayBlockTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF"></td>';
		$pre_pay_html .='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['cash'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['check'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['credit_card'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['eft'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['money_order'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['veep'].'&nbsp;</td>';
		$pre_pay_html .='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$totalPrePayCount.'&nbsp;</td>';
		if(empty($pay_method)==false)
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF"></td>';
		$pre_pay_html .='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	'.$cctype_html.'
	</table>';		

	//PDF HTML
	$pre_pay_html_PDF.=' 
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">Pre-Payments Total&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['cash'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['check'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['credit_card'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['eft'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money card')
		$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['money_order'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPrePayTot['veep'],2).'&nbsp;</td>';
		$pre_pay_html_PDF.='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($prePayBlockTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$pre_pay_html_PDF.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>';
		$pre_pay_html_PDF.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">Transactions Count&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['cash'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['check'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['credit_card'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['eft'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['money_order'].'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPrePayTransCount['veep'].'&nbsp;</td>';
		$pre_pay_html_PDF .='
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$totalPrePayCount.'&nbsp;</td>';
		if(empty($pay_method)==false)
		$pre_pay_html_PDF .='<td class="text_10b" bgcolor="#FFFFFF"></td>';
		$pre_pay_html_PDF .='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	'.$cctype_html.'	
	</table>
	</page>';	
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
	
	if($showFacCol){
		foreach($arrDelPostedAmounts as $grpId => $grpDataArr){
			$firstGrpTotal=array();
			$firstGroupName='';
			$firstGroupName = $providerNameArr[$grpId];
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';

			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal = array();

				$facilityName = $posFacilityArr[$facId];
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Facility - '.$facilityName.'</td></tr>';
			
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
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['del_amount'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>
					</tr>';			
				}
		
				//FACILITY TOTAL
				$firstGrpTotal['del_amount']+=	$facGrpTotal['del_amount'];
			
				// SUB TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Facility Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['del_amount'],2).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
				';
			}
			
			//PHYSICIAN TOTAL
			$arrPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
			
			// SUB TOTAL
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['del_amount'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			';
		}
	}else{
		foreach($arrDelPostedAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			
			if($groupBy=='facility'){
				$firstGroupName = $posFacilityArr[$grpId];
			}else if($groupBy=='physician' || $groupBy=='operator'){
				$firstGroupName = $providerNameArr[$grpId];
			}else if($groupBy=='groups'){
				$firstGroupName = $group_id_arr[$grpId];
			}else{
				$firstGroupName = $providerNameArr[$grpId];
				$firstGroupTitle = 'Physician';
				$subTotalTitle = 'Physician Total';
				$naFirstGroupTitle='Physician';
				$naSubTotalTitle='Physician Total';
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
				
				$firstGrpTotal['del_amount']+=	$grpDetail['del_amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['del_amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['del_amount'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}
	}
	
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">		
	<tr><td colspan="'.$colspan.'" class="text_b_w">Deleted Posted Payments</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">&nbsp;Patient Name-Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">Enc. Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">DOS</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Deleted Amount</td>
		<td style="width:'.$last_col.';" class="text_b_w">&nbsp;</td>
	</tr>
	</table>';

	$delPostedBlockTotal = $arrPatPayTot['del_amount'];
		
	//PAGE HTML
	$del_posted_html .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Deleted Posted Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
	</table>';
	
	//PDF HTML
	$del_posted_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Deleted Posted Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
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
	
	if($showFacCol){
		foreach($arrDelCICOAmounts as $grpId => $grpDataArr){
			$firstGrpTotal=array();
			$firstGroupName='';
			$firstGroupName = $providerNameArr[$grpId];
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';

			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal = array();

				$facilityName = $posFacilityArr[$facId];
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Facility - '.$facilityName.'</td></tr>';
			
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
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['del_amount'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>
					</tr>';			
				}
		
				//FACILITY TOTAL
				$firstGrpTotal['del_amount']+=	$facGrpTotal['del_amount'];
			
				// SUB TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Facility Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['del_amount'],2).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
				';
			}
			
			//PHYSICIAN TOTAL
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
			
			// SUB TOTAL
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['del_amount'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			';
		}
	}else{
		foreach($arrDelCICOAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			if($groupBy=='facility'){
				$firstGroupName = $posFacilityArr[$grpId];
			}else if($groupBy=='physician' || $groupBy=='operator'){
				$firstGroupName = $providerNameArr[$grpId];
			}else if($groupBy=='groups'){
				$firstGroupName = $group_id_arr[$grpId];
			}else{
				$firstGroupName = $providerNameArr[$grpId];
				$firstGroupTitle = 'Physician';
				$subTotalTitle = 'Physician Total';
				$naFirstGroupTitle='Physician';
				$naSubTotalTitle='Physician Total';
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
				
				$firstGrpTotal['del_amount']+=	$grpDetail['del_amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['del_amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['del_amount'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}
	}
	
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td colspan="'.$colspan.'" class="text_b_w">Deleted CI/CO Payments</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">&nbsp;Patient Name-Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">Paid Date</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Deleted Amount</td>
		<td style="width:'.$last_col.';" class="text_b_w">&nbsp;</td>
	</tr>
	</table>';

	$delCICOBlockTotal = $arrDelPatPayTot['del_amount'];
		
	//PAGE HTML
	$del_cico_html .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Deleted CI/CO Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrDelPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
	</table>';
	
	//PDF HTML
	$del_cico_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Deleted CI/CO Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrDelPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
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
	
	if($showFacCol){
		foreach($arrDelPrePayAmounts as $grpId => $grpDataArr){
			$firstGrpTotal=array();
			$firstGroupName='';
			$firstGroupName = $providerNameArr[$grpId];
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';

			foreach($grpDataArr as $facId => $grpData){
				$facGrpTotal = array();

				$facilityName = $posFacilityArr[$facId];
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Facility - '.$facilityName.'</td></tr>';
			
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
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['del_amount'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="width:'.$last_col.'">&nbsp;</td>
					</tr>';			
				}
		
				//FACILITY TOTAL
				$firstGrpTotal['del_amount']+=	$facGrpTotal['del_amount'];
			
				// SUB TOTAL
				$content_part.=' 
				<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Facility Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($facGrpTotal['del_amount'],2).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
				';
			}
			
			//PHYSICIAN TOTAL
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
			
			// SUB TOTAL
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['del_amount'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			';
		}
	}else{
		foreach($arrDelPrePayAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			if($groupBy=='facility'){
				$firstGroupName = $posFacilityArr[$grpId];
			}else if($groupBy=='physician' || $groupBy=='operator'){
				$firstGroupName = $providerNameArr[$grpId];
			}else if($groupBy=='groups'){
				$firstGroupName = $group_id_arr[$grpId];
			}else{
				$firstGroupName = $providerNameArr[$grpId];
				$firstGroupTitle = 'Physician';
				$subTotalTitle = 'Physician Total';
				$naFirstGroupTitle='Physician';
				$naSubTotalTitle='Physician Total';
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
				
				$firstGrpTotal['del_amount']+=	$grpDetail['del_amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['del_amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['del_amount'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}
	}
	
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td colspan="'.$colspan.'" class="text_b_w">Deleted Pre-Payments</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">&nbsp;Patient Name-Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">DOT</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Deleted Amount</td>
		<td style="width:'.$last_col.';" class="text_b_w">&nbsp;</td>
	</tr>
	</table>';

	
	$delPrePayBlockTotal = $arrDelPatPayTot['del_amount'];
	
	//PAGE HTML
	$del_pre_pay_html .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Deleted Pre-Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrDelPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
	</table>';
	
	//PDF HTML
	$del_pre_pay_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Deleted Pre-Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrDelPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
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
	
	$grandTot = $cash + $check + $credit_card + $eft + $money_order + $veep;
	
	$grandCCType='';
	$colspan=8 - $colsRemoved;	
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
				$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($amt,2).'&nbsp;</td>';
				$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$grandCCType.='</tr>';	
		}
		$grandCCType.='<tr>';
			$grandCCType.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Grand Total of CC Types&nbsp;:</td>';
			$grandCCType.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totCCType,2).'&nbsp;</td>';
			$grandCCType.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$grandCCType.='</tr>';	

		$grandCCType.='<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	}	
	
	//TOTAL COUNTS
	$totCashCnt= $arrPostedTransCount['cash']+$arrCICOTransCount['cash']+$arrPrePayTransCount['cash'];
	$totCheckCnt= $arrPostedTransCount['check']+$arrCICOTransCount['check']+$arrPrePayTransCount['check'];
	$totMOCnt= $arrPostedTransCount['money_order']+$arrCICOTransCount['money_order']+$arrPrePayTransCount['money_order'];
	$totEFTCnt= $arrPostedTransCount['eft']+$arrCICOTransCount['eft']+$arrPrePayTransCount['eft'];
	$totCCCnt= $arrPostedTransCount['credit_card']+$arrCICOTransCount['credit_card']+$arrPrePayTransCount['credit_card'];
	$totVeepCnt= $arrPostedTransCount['veep']+$arrCICOTransCount['veep']+$arrPrePayTransCount['veep'];
	$totCount= $totCashCnt+$totCheckCnt+$totMOCnt+$totEFTCnt+$totCCCnt+$totVeepCnt;
		
	//FINAL HTML
	$grand_html=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td colspan="'.$colspan.'" class="text_b_w">'.$title.'</td></tr>
	<tr>
		<td style="text-align:center;width:'.$gd_col1.';" class="text_b_w"></td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_html.='<td style="text-align:right;width:'.$gd_col2.';" class="text_b_w">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_html.='<td style="text-align:right;width:'.$gd_col3.';" class="text_b_w">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_html.='<td style="text-align:right;width:'.$gd_col4.';" class="text_b_w">Credit Card&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_html.='<td style="text-align:right;width:'.$gd_col5.';" class="text_b_w">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_html.='<td style="text-align:right;width:'.$gd_col6.';" class="text_b_w">MO&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_html.='<td style="text-align:right;width:'.$gd_col6.';" class="text_b_w">VEEP&nbsp;</td>';
		$grand_html.='<td style="text-align:right;width:'.$gd_col7.';" class="text_b_w">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_html.='<td style="text-align:right;width:'.$gd_col_opt.';" class="text_b_w">&nbsp;</td>';
		$grand_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col1.'; text-align:right;"></td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($cash,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col3.'; text-align:right;">'.$showCurrencySymbol.number_format($check,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col4.'; text-align:right;">'.$showCurrencySymbol.number_format($credit_card,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col5.'; text-align:right;">'.$showCurrencySymbol.number_format($eft,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col6.'; text-align:right;">'.$showCurrencySymbol.number_format($money_order,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col6.'; text-align:right;">'.$showCurrencySymbol.number_format($veep,2).'&nbsp;</td>';
		$grand_html.='
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col7.'; text-align:right;">'.$showCurrencySymbol.number_format($grandTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col_opt.';">&nbsp;</td>';
		$grand_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col1.'; text-align:right;">Total Transactions Count: </td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$totCashCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col3.'; text-align:right;">'.$totCheckCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col4.'; text-align:right;">'.$totCCCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col5.'; text-align:right;">'.$totEFTCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col6.'; text-align:right;">'.$totMOCnt.'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col6.'; text-align:right;">'.$totVeepCnt.'&nbsp;</td>';
		$grand_html.='
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col7.'; text-align:right;">'.$totCount.'&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col_opt.';">&nbsp;</td>';
		$grand_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
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
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td colspan="3" class="text_b_w">Grand Collection</td></tr>
	<tr>
		<td style="text-align:center;width:'.$firstCol.';"></td>
		<td style="text-align:right;width:'.$grand_w_cols.'">Payments&nbsp;</td>
		<td style="text-align:right;width:'.$lastCol.'">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Posted Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($postedBlockTot,2).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">CI/CO Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($cicoBlockTot,2).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Pre-Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($prePayBlockTot,2).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Collected</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totalCollected,2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted Posted Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($delPostedBlockTotal,2).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted CI/CO Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($delCICOBlockTotal,2).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted Pre-Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($delPrePayBlockTotal,2).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Deleted</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totalDeleted,2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Final Collected</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($finalCollected,2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
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
		if($groupBy=='facility'){
			$firstGroupName = $posFacilityArr[$grpId];
		}else if($groupBy=='physician' || $groupBy=='operator'){
			$firstGroupName = $providerNameArr[$grpId];
		}else if($groupBy=='groups'){
			$firstGroupName = $group_id_arr[$grpId];
		}else{
			$firstGroupName = $providerNameArr[$grpId];
			$firstGroupTitle = 'Physician';
			$subTotalTitle = 'Physician Total';
			$naFirstGroupTitle='Physician';
			$naSubTotalTitle='Physician Total';
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
			
			$firstGrpTotal['applied_amt']+=	$grpDetail['applied_amt'];
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['applied_amt'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
			</tr>';			
		}
	
		$arrAppliedPatPayTot['applied_amt']+=	$firstGrpTotal['applied_amt'];
	
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['applied_amt'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
	}

	$totalCICOManuallyApplied=$arrAppliedPatPayTot['applied_amt'];
	
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td colspan="'.$colspan.'">CI/CO Manually Applied Amounts</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">&nbsp;Patient Name-Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">DOT</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Applied Amount</td>
		<td style="width:'.$last_col.';" class="text_b_w">&nbsp;</td>
	</tr>
	</table>';

	
	//HTML
	$manually_applied_cico_html .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">CI/CO Manually Applied Amounts&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrAppliedPatPayTot['applied_amt'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>	
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
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$showCurrencySymbol.number_format($grpDetail['applied_amt'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
			</tr>';			
		}
	
		$arrAppliedPatPayTot['applied_amt']+=	$firstGrpTotal['applied_amt'];
	
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal['applied_amt'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
	}

	
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td colspan="'.$colspan.'" class="text_b_w">Pre-Payments Manually Applied Amounts</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">&nbsp;Patient Name-Id</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">DOT</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Applied Amount</td>
		<td style="width:'.$last_col.';" class="text_b_w">&nbsp;</td>
	</tr>
	</table>';


	//HTML
	$manually_applied_pre_pay_html .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Pre-Payments Manually Applied Amounts&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrAppliedPatPayTot['applied_amt'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';

	//IF MANUALLY APPLIED CI/CO ALSO EXIST THEN MAKE TOTAL OF BOTH
	if($totalCICOManuallyApplied>0){
		$tot= $totalCICOManuallyApplied + $arrAppliedPatPayTot['applied_amt'];
		$manually_applied_pre_pay_html.='
		<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Total Manually Applied Amounts&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($tot,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	}
	$manually_applied_pre_pay_html.='</table>';
}

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
