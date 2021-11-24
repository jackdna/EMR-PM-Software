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
FILE : payments_summary.php
PURPOSE :  PAYMENTS SUMMARY VIEW
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
	$naFirstGroupTitle='Group';
	$naSubTotalTitle='Group Total';
}
$colsRemoved= (empty($pay_method)==false) ? 4 : 0;
// POSTED PAYMENTS
$arrPatPayTot=array();
$content_part= $patient_html='';
if(sizeof($arrPostedPay)>0){
	$dataExists=true;
	if($showFacCol){
		foreach($arrPostedPay as $grpId => $grpDataArr){
			foreach($grpDataArr as $facID=>$grpData){
				$rowTot=0;
				$firstGroupName='';
				$firstGrpTotal=array();
				if($groupBy=='physician' || $groupBy=='operator'){
					$firstGroupName = $providerNameArr[$grpId];
				}elseif($groupBy=='facility'){
					$firstGroupName = $posFacilityArr[$grpId];
				}elseif($groupBy=='groups'){
					$firstGroupName = $group_id_arr[$grpId];
				}else{
					$firstGroupName = $dept_name_arr[$grpId];
				}
				
				$arrPatPayTot['cash']+=	$grpData['cash'];
				$arrPatPayTot['check']+= $grpData['check'];
				$arrPatPayTot['credit_card']+=	$grpData['credit_card'];
				$arrPatPayTot['eft']+=	$grpData['eft'];
				$arrPatPayTot['money_order']+=	$grpData['money_order'];
				$arrPatPayTot['veep']+=	$grpData['veep'];
				$arrPatPayTot['by_patient']+=	$grpData['byPatient'];
				$arrPatPayTot['by_insurance']+=	$grpData['byInsurance'];
				
				//$arrPatPayTot['cc_types'][]
				
				$rowTot = $grpData['byPatient'] + $grpData['byInsurance'];
				
				$content_part .='
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$firstGroupName.'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$facilityName.'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['cash'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['check'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['credit_card'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['eft'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['money_order'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['veep'],2).'&nbsp;</td>';
					$content_part .='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['byPatient'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['byInsurance'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
				}elseif($groupBy=='facility'){
					$firstGroupName = $posFacilityArr[$grpId];
				}elseif($groupBy=='groups'){
					$firstGroupName = $group_id_arr[$grpId];
				}else{
					$firstGroupName = $dept_name_arr[$grpId];
				}
				$arrPatPayTot['cash']+=	$grpData['cash'];
				$arrPatPayTot['check']+= $grpData['check'];
				$arrPatPayTot['credit_card']+=	$grpData['credit_card'];
				$arrPatPayTot['eft']+=	$grpData['eft'];
				$arrPatPayTot['money_order']+=	$grpData['money_order'];
				$arrPatPayTot['veep']+=	$grpData['veep'];
				$arrPatPayTot['by_patient']+=	$grpData['byPatient'];
				$arrPatPayTot['by_insurance']+=	$grpData['byInsurance'];
				
				$rowTot = $grpData['byPatient'] + $grpData['byInsurance'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$firstGroupName.'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='cash')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['cash'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='check')
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['check'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='credit card')					
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['credit_card'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='eft')					
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['eft'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='money order')					
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['money_order'],2).'&nbsp;</td>';
					if(empty($pay_method)==true || $pay_method=='veep')					
					$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['veep'],2).'&nbsp;</td>';
					$content_part .='
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['byPatient'],2).'&nbsp;</td>					
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['byInsurance'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
					if(empty($pay_method)==false)
					$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part .='
				</tr>';	
			}
	}
	
	$postedBlockTot = $arrPatPayTot['by_patient'] + $arrPatPayTot['by_insurance'];
	
	// TOTAL
	$fac_col='';
	$colspan= 10 - $colsRemoved;
	$colWidth="9%";
	$total_cols = 9 - $colsRemoved;
	if($showFacCol){
		$colspan= 11 - $colsRemoved;
		$total_cols=10 - $colsRemoved;
	}
	if(empty($pay_method)==false){	
		$secColspan= $colspan - 4;
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

	if($showFacCol){
		$fac_col=' <td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
	}

	$patient_html .=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td class="text_b_w" colspan="'.$colspan.'">Posted Payments</td></tr>
	<tr>
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;" class="text_b_w">'.$firstGroupTitle.'</td>
		'.$fac_col;
		if(empty($pay_method)==true || $pay_method=='cash')
		$patient_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$patient_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$patient_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Credit Card&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$patient_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$patient_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Money Order&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$patient_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">VEEP&nbsp;</td>';
		$patient_html .='
		<td style="text-align:center;" colspan="2" class="text_b_w">Payments By</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$patient_html .='<td style="width:'.$last_col.';">&nbsp;</td>';
		$patient_html .='
	</tr>
	<tr>
		<td style="text-align:right;" colspan="'.$secColspan.'" class="text_b_w"></td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">Patient&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:center;" class="text_b_w">Insurance&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w"></td>';
		if(empty($pay_method)==false)
		$patient_html .='<td style="width:'.$last_col.';">&nbsp;</td>';
	$patient_html .='
	</tr>'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" >Posted Payments Total&nbsp;:</td>';
		if($showFacCol){
		$patient_html .=' <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>';
		}
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
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	
	if(sizeof($arrPostedCCTypeAmts)>0){
		$ccColspan=$colspan-2;
		$totCCType=0;
		foreach($arrPostedCCTypeAmts as $ccType=> $amt){
			$totCCType+=$amt;
			$patient_html.='<tr>';
				$patient_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
				$patient_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($amt,2).'&nbsp;</td>';
				$patient_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$patient_html.='</tr>';	

			$arrGrandCCTypes[$ccType]+=$amt;
		}
		$patient_html.='<tr>';
			$patient_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total of CC Types&nbsp;:</td>';
			$patient_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totCCType,2).'&nbsp;</td>';
			$patient_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$patient_html.='</tr>';	

		$patient_html.='<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
		
	}
	$patient_html .='</table>';		
}


// UNAPPLIED CI/CO PAYMENTS
$arrCICOPayTot=array();
$content_part= $cico_html = '';
if(sizeof($arrCICONotApplied)>0){
	$dataExists=true;
	if($showFacCol){
		foreach($arrCICONotApplied as $grpId => $grpDataArr){
			foreach($grpDataArr as $facId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
			$facilityName = $posFacilityArr[$facId];
			$arrCICOPayTot['cash']+=	$grpData['cash'];
			$arrCICOPayTot['check']+= $grpData['check'];
			$arrCICOPayTot['credit_card']+=	$grpData['credit_card'];
			$arrCICOPayTot['eft']+=	$grpData['eft'];
			$arrCICOPayTot['veep']+=	$grpData['veep'];
			$arrCICOPayTot['money_order']+=	$grpData['money_order'];
			
			$rowTot = $grpData['cash'] + $grpData['check'] + $grpData['credit_card'] + $grpData['eft'] + $grpData['money_order'] + $grpData['veep'];
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$facilityName.'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['cash'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['check'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['credit_card'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['eft'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['money_order'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grpData['veep'],2).'&nbsp;</td>';
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
			if($groupBy=='physician' || $groupBy=='operator'){
				$firstGroupName = $providerNameArr[$grpId];
			}elseif($groupBy=='facility'){
				$firstGroupName = $posFacilityArr[$grpId];
			}elseif($groupBy=='groups'){
				$firstGroupName = $group_id_arr[$grpId];
			}else{
				$firstGroupName = $dept_name_arr[$grpId];
			}
			
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
			
			
			
			
			
			$arrCICOPayTot['cash']+=	$grpData['cash'];
			$arrCICOPayTot['check']+= $grpData['check'];
			$arrCICOPayTot['credit_card']+=	$grpData['credit_card'];
			$arrCICOPayTot['eft']+=	$grpData['eft'];
			$arrCICOPayTot['veep']+=	$grpData['veep'];
			$arrCICOPayTot['money_order']+=	$grpData['money_order'];
			
			$rowTot = $grpData['cash'] + $grpData['check'] + $grpData['credit_card'] + $grpData['eft'] + $grpData['money_order'] + $grpData['veep'];

			//POPUP TITLES
			$cash_title=($grpData['cash_is_ref']=='cash' && $grpData['cash_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['cash_ref_amt'].' Refund"':'"';			
			$check_title=($grpData['check_is_ref']=='check' && $grpData['check_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['check_ref_amt'].' Refund"':'"';
			$cc_title=($grpData['credit_card_is_ref']=='credit_card' && $grpData['credit_card_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['credit_card_ref_amt'].' Refund"':'"';			
			$eft_title=($grpData['eft_is_ref']=='eft' && $grpData['eft_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['eft_ref_amt'].' Refund"':'"';			
			$mo_title=($grpData['money_order_is_ref']=='money_order' && $grpData['money_order_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['money_order_ref_amt'].' Refund"':'"';			
			$veep_title=($grpData['veep_is_ref']=='veep' && $grpData['veep_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['veep_ref_amt'].' Refund"':'"';			

			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$cash_title.'>'.$showCurrencySymbol.number_format($grpData['cash'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$check_title.'>'.$showCurrencySymbol.number_format($grpData['check'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$cc_title.'>'.$showCurrencySymbol.number_format($grpData['credit_card'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$eft_title.'>'.$showCurrencySymbol.number_format($grpData['eft'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$mo_title.'>'.$showCurrencySymbol.number_format($grpData['money_order'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$veep_title.'>'.$showCurrencySymbol.number_format($grpData['veep'],2).'&nbsp;</td>';
				$content_part .='
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
				if(empty($pay_method)==false)
				$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
				$content_part .='
			</tr>';			
		}
	}
	
	$cicoBlockTot = $arrCICOPayTot['cash'] + $arrCICOPayTot['check'] + $arrCICOPayTot['credit_card'] + $arrCICOPayTot['eft'] + $arrCICOPayTot['money_order'] + $arrCICOPayTot['veep'];

	// TOTAL
	$facCol='';
	$colspan=10 - $colsRemoved;
	$secColspan=1;
	$colWidth='9%';
	$total_cols = 9  - $colsRemoved;
	if($showFacCol){
		$colspan = 11 - $colsRemoved;
		$secColspan=2;
		$colWidth='9%';
		$total_cols = 10 - $colsRemoved;
	}
	$last_col=0;
	if(empty($pay_method)==false){ 	
		$last_col = 40;	
		$total_cols=$total_cols-1;
	}
	
	$first_col = "16";
	$w_cols = (100 - ($first_col+$last_col)) /$total_cols;

	//$first_col = 100 - ($total_cols * $w_cols);

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
	$gd_col7 = $w_cols."%";
	$gd_col_opt= $last_col."%";
	
	$w_cols = $w_cols."%";
	$first_col = $first_col."%";
	$last_col = $last_col."%";

	if($showFacCol){
		$cico_fac='<td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
	}
	
	$cico_html .=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td class="text_b_w" colspan="'.$colspan.'">Unapplied CI/CO Payments</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">'.$naFirstGroupTitle.'&nbsp;</td>
		'.$cico_fac;
		if(empty($pay_method)==true || $pay_method=='cash')
		$cico_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$cico_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$cico_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Credit Card&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$cico_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$cico_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Money Order&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$cico_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">VEEP&nbsp;</td>';
		$cico_html .='
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w"></td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w"></td>
		<td  style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$cico_html .='<td  style="width:'.$last_col.';" class="text_b_w">&nbsp;</td>';
		$cico_html .='
	</tr>'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$secColspan.'">CI/CO Payments Total&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($arrCICOPayTot['cash'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($arrCICOPayTot['check'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($arrCICOPayTot['credit_card'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($arrCICOPayTot['eft'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($arrCICOPayTot['money_order'],2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($arrCICOPayTot['veep'],2).'&nbsp;</td>';
		$cico_html .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($cicoBlockTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$cico_html .='<td class="text_10b" bgcolor="#FFFFFF"></td>';
		$cico_html .='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	
	if(sizeof($arrCIOCCTypeAmts)>0){
		$ccColspan=$colspan-2;
		$totCCType=0;
		foreach($arrCIOCCTypeAmts as $ccType=> $amt){
			$totCCType+=$amt;
			$cico_html.='<tr>';
				$cico_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
				$cico_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($amt,2).'&nbsp;</td>';
				$cico_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$cico_html.='</tr>';

			$arrGrandCCTypes[$ccType]+=$amt;				
		}
		$cico_html.='<tr>';
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total of CC Types&nbsp;:</td>';
			$cico_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totCCType,2).'&nbsp;</td>';
			$cico_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$cico_html.='</tr>';	

		$cico_html.='<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	}
	
	$cico_html .='</table>';	
}

// UNAPPLIED PRE PAYMENTS
$arrPrePayTot=array();
$totCCType='';
$content_part= $pre_pay_html = '';
if(sizeof($arrPrePayNotApplied)>0){
	$dataExists=true;
	
	if($showFacCol){
	foreach($arrPrePayNotApplied as $grpId => $grpDataArr){
		foreach($grpDataArr as $facId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $providerNameArr[$grpId];
			$facilityName = $posFacilityArr[$facId];
			
			$arrPrePayTot['cash']+=	$grpData['cash'];
			$arrPrePayTot['check']+= $grpData['check'];
			$arrPrePayTot['credit_card']+=	$grpData['credit_card'];
			$arrPrePayTot['eft']+=	$grpData['eft'];
			$arrPrePayTot['money_order']+=	$grpData['money_order'];
			$arrPrePayTot['veep']+=	$grpData['veep'];
			
			$redRow=($grpData['is_ref']>0)?';color:#FF0000':'';
			
			$rowTot = $grpData['cash'] + $grpData['check'] + $grpData['credit_card'] + $grpData['eft'] + $grpData['money_order'] + $grpData['veep'];

			//POPUP TILES
			$cash_title=($grpData['cash_is_ref']=='cash' && $grpData['cash_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['cash_ref_amt'].' Refund"':'"';
			$check_title=($grpData['check_is_ref']=='check' && $grpData['check_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['check_ref_amt'].' Refund"':'"';
			$cc_title=($grpData['credit_card_is_ref']=='credit_card' && $grpData['credit_card_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['credit_card_ref_amt'].' Refund"':'"';	
			$eft_title=($grpData['eft_is_ref']=='eft' && $grpData['eft_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['eft_ref_amt'].' Refund"':'"';			
			$mo_title=($grpData['money_order_is_ref']=='money_order' && $grpData['money_order_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['money_order_ref_amt'].' Refund"':'"';			
			$veep_title=($grpData['veep_is_ref']=='veep' && $grpData['veep_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['veep_ref_amt'].' Refund"':'"';
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word">'.$firstGroupName.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word">'.$facilityName.'</td>';
				if(empty($pay_method)==true || $pay_method=='cash')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$cash_title.'>'.$showCurrencySymbol.number_format($grpData['cash'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='check')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$check_title.'>'.$showCurrencySymbol.number_format($grpData['check'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='credit card')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$cc_title.'>'.$showCurrencySymbol.number_format($grpData['credit_card'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='eft')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$eft_title.'>'.$showCurrencySymbol.number_format($grpData['eft'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='money order')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$mo_title.'>'.$showCurrencySymbol.number_format($grpData['money_order'],2).'&nbsp;</td>';
				if(empty($pay_method)==true || $pay_method=='veep')
				$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$veep_title.'>'.$showCurrencySymbol.number_format($grpData['veep'],2).'&nbsp;</td>';
				$content_part .='
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
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
		if($groupBy=='physician' || $groupBy=='operator'){
			$firstGroupName = $providerNameArr[$grpId];
		}elseif($groupBy=='facility'){
			$firstGroupName = $posFacilityArr[$grpId];
		}elseif($groupBy=='groups'){
			$firstGroupName = $group_id_arr[$grpId];
		}else{
			$firstGroupName = $dept_name_arr[$grpId];
		}
			
		$arrPrePayTot['cash']+=	$grpData['cash'];
		$arrPrePayTot['check']+= $grpData['check'];
		$arrPrePayTot['credit_card']+=	$grpData['credit_card'];
		$arrPrePayTot['eft']+=	$grpData['eft'];
		$arrPrePayTot['money_order']+=	$grpData['money_order'];
		$arrPrePayTot['veep']+=	$grpData['veep'];
		
		$rowTot = $grpData['cash'] + $grpData['check'] + $grpData['credit_card'] + $grpData['eft'] + $grpData['money_order'] + $grpData['veep'];

		//POPUP TITLES
		$cash_title=($grpData['cash_is_ref']=='cash' && $grpData['cash_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['cash_ref_amt'].' Refund"':'"';		
		$check_title=($grpData['check_is_ref']=='check' && $grpData['check_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['check_ref_amt'].' Refund"':'"';
		$cc_title=($grpData['credit_card_is_ref']=='credit_card' && $grpData['credit_card_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['credit_card_ref_amt'].' Refund"':'"';
		$eft_title=($grpData['eft_is_ref']=='eft' && $grpData['eft_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['eft_ref_amt'].' Refund"':'"';
		$mo_title=($grpData['money_order_is_ref']=='money_order' && $grpData['money_order_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['money_order_ref_amt'].' Refund"':'"';
		$veep_title=($grpData['veep_is_ref']=='veep' && $grpData['veep_ref_amt']>0)?';color:#FF0000"  title="'.$showCurrencySymbol.$grpData['veep_ref_amt'].' Refund"':'"';
		
		$content_part .= '
		<tr>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='cash')
			$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$cash_title.'>'.$showCurrencySymbol.number_format($grpData['cash'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='check')			
			$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$check_title.'>'.$showCurrencySymbol.number_format($grpData['check'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='credit card')			
			$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$cc_title.'>'.$showCurrencySymbol.number_format($grpData['credit_card'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='eft')			
			$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$eft_title.'>'.$showCurrencySymbol.number_format($grpData['eft'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='money order')			
			$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$mo_title.'>'.$showCurrencySymbol.number_format($grpData['money_order'],2).'&nbsp;</td>';
			if(empty($pay_method)==true || $pay_method=='veep')			
			$content_part .='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right" '.$veep_title.'>'.$showCurrencySymbol.number_format($grpData['veep'],2).'&nbsp;</td>';
			$content_part .='
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($rowTot,2).'&nbsp;</td>';
			if(empty($pay_method)==false)
			$content_part .='<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>';
			$content_part .='
		</tr>';			
	}
	}
	
	$prePayBlockTot = $arrPrePayTot['cash'] + $arrPrePayTot['check'] + $arrPrePayTot['credit_card'] + $arrPrePayTot['eft'] + $arrPrePayTot['money_order'] + $arrPrePayTot['veep'];
	
	// TOTAL
	$facCol='';
	$colspan=10 - $colsRemoved;
	$secColspan=1;
	$colWidth='9%';
	$total_cols = 9 - $colsRemoved;
	if($showFacCol){
		$colspan = 11 - $colsRemoved;
		$secColspan=2;
		$colWidth='9%';
		$total_cols = 10 - $colsRemoved;;
	}
	$last_col=0;
	if(empty($pay_method)==false){ 	
		$last_col = 40;	
		$total_cols=$total_cols-1;
	}
		
	$first_col = "16";
	$w_cols = (100 - ($first_col+$last_col)) /$total_cols;
	
	$gd_first_col = $first_col;
	if($showFacCol){
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
	
	if($showFacCol){
		$facCol = '<td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
	}
	
	$pre_pay_html .=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td class="text_b_w" colspan="'.$colspan.'">Unapplied Pre-Payments</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">Operator &nbsp;</td>
		'.$facCol;
		if(empty($pay_method)==true || $pay_method=='cash')
		$pre_pay_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Cash&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$pre_pay_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Check&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$pre_pay_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Credit Card&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$pre_pay_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">EFT&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$pre_pay_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Money Order&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$pre_pay_html .='<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">VEEP&nbsp;</td>';
		$pre_pay_html .='
		<td style="width:'.$w_cols.'; text-align:right;"></td>
		<td style="width:'.$w_cols.'; text-align:right;"></td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Total&nbsp;</td>';
		if(empty($pay_method)==false)
		$pre_pay_html .='<td style="width:'.$last_col.';" class="text_b_w"></td>';
		$pre_pay_html .='
	</tr>'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$secColspan.'" class="text_b_w">Pre-Payments Total&nbsp;:</td>';
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
		$pre_pay_html .='<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>';
		$pre_pay_html .='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	
	if(sizeof($arrPrePayCCTypeAmts)>0){
		$ccColspan=$colspan-2;
		$totCCType=0;
		foreach($arrPrePayCCTypeAmts as $ccType=> $amt){
			$totCCType+=$amt;
			$pre_pay_html.='<tr>';
				$pre_pay_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$ccType.'&nbsp;:</td>';
				$pre_pay_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($amt,2).'&nbsp;</td>';
				$pre_pay_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
			$pre_pay_html.='</tr>';	

			$arrGrandCCTypes[$ccType]+=$amt;			
		}
		$pre_pay_html.='<tr>';
			$pre_pay_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" class="text_b_w">Total of CC Types&nbsp;:</td>';
			$pre_pay_html.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($totCCType,2).'&nbsp;</td>';
			$pre_pay_html.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$ccColspan.'"></td>';
		$pre_pay_html.='</tr>';	

		$pre_pay_html.='<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>';
	}	
	$pre_pay_html .='</table>';		
}

//DELETED RECORDS
$delDataExists=false;
if(sizeof($delSortedGroup)>0){
	$dataExists=true;
	$delDataExists=true;
	$content_part='';
	$arrTotals=array();
	if($showFacCol){
		foreach($arrDelAmounts as $grpId => $grpDataArr){
			foreach($grpDataArr as $facId => $grpData){
				$rowTot=0;
				$firstGroupName='';
				$firstGrpTotal=array();
				$firstGroupName = $providerNameArr[$grpId];
				$facilityName = $posFacilityArr[$facId];

				$total= $grpData['posted']+$grpData['cico']+$grpData['pre_payment']; 
				$arrTotals['posted']+= $grpData['posted'];
				$arrTotals['cico']+= $grpData['cico'];
				$arrTotals['pre_payment']+= $grpData['pre_payment'];
				$arrTotals['total']+= $total;
				
				$content_part.= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word">'.$firstGroupName.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word">'.$facilityName.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($grpData['posted'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($grpData['cico'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($grpData['pre_payment'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($total,2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				</tr>';			
			}
		}
	}else{
		foreach($arrDelAmounts as $grpId => $grpData){
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
			$total= $grpData['posted']+$grpData['cico']+$grpData['pre_payment']; 
			$arrTotals['posted']+= $grpData['posted'];
			$arrTotals['cico']+= $grpData['cico'];
			$arrTotals['pre_payment']+= $grpData['pre_payment'];
			$arrTotals['total']+= $total;
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($grpData['posted'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($grpData['cico'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($grpData['pre_payment'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($total,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>';			
		}
	}
	
	// TOTAL
	$facCol='';
	$colspan=6;
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

	$delPostedTotal=$arrTotals['posted'];
	$delCICOTotal=$arrTotals['cico'];
	$delPrePayTotal=$arrTotals['pre_payment'];

	$deleted_html.=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td class="text_b_w" colspan="'.$colspan.'">Deleted Payments (Between '.$Start_date.' and '.$End_date.')</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;" class="text_b_w">'.$firstGroupTitle.' &nbsp;</td>
		'.$facCol.'
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Posted Payments&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">CI/CO Payments&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Pre-Payments&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:right;" class="text_b_w">Total&nbsp;</td>
		<td style="width:'.$last_col.'; text-align:right;" class="text_b_w">&nbsp;</td>
	</tr>'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$firstColSpan.'">Deleted Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrTotals['posted'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrTotals['cico'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrTotals['pre_payment'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrTotals['total'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	</table>';		
}


if($dataExists==true){
	$title=($delDataExists==true) ? 'Total Payments' : 'Grand Total';

	$cash= $arrPatPayTot['cash'] + $arrInsPayTot['cash'] + $arrCICOPayTot['cash'] + $arrPrePayTot['cash'];
	$check= $arrPatPayTot['check'] + $arrInsPayTot['check'] + $arrCICOPayTot['check'] + $arrPrePayTot['check'];
	$credit_card= $arrPatPayTot['credit_card'] + $arrInsPayTot['credit_card'] + $arrCICOPayTot['credit_card'] + $arrPrePayTot['credit_card'];
	$eft= $arrPatPayTot['eft'] + $arrInsPayTot['eft'] + $arrCICOPayTot['eft'] + $arrPrePayTot['eft'];
	$money_order= $arrPatPayTot['money_order'] + $arrInsPayTot['money_order'] + $arrCICOPayTot['money_order'] + $arrPrePayTot['money_order'];
	$veep= $arrPatPayTot['veep'] + $arrInsPayTot['veep'] + $arrCICOPayTot['veep'] + $arrPrePayTot['veep'];
	
	$grandTot = $cash + $check + $credit_card + $eft + $money_order + $veep;
	
	//FINAL HTML
	$grandCCType='';
	$colspan=10 -  $colsRemoved;
	$colWidth="9%";
	$firstColWidth="16%";
	$pdfColWidth="143px";
	$pdfColWidth="12%";
	//$pdfFirstColWidth="160px";
	$pdfFirstColWidth="15%";
	if($showFacCol){
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
		
	$grand_html=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td bgcolor="#FFFFFF" colspan="'.$colspan.'"></td></tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col1.'; text-align:right;">'.$title.'&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($cash,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($check,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($credit_card,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($eft,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($money_order,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($veep,2).'&nbsp;</td>';
		$grand_html.='
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($grandTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_html.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col_opt.'; text-align:right;"></td>';
		$grand_html.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	'.$grandCCType.'
	</table>';		

	$grand_pdf=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td colspan="'.$colspan.'" style="width:100%"></td></tr>
	<tr><td bgcolor="#FFFFFF" colspan="'.$colspan.'"></td></tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col1.'; text-align:right;">'.$title.'&nbsp;:</td>';
		if(empty($pay_method)==true || $pay_method=='cash')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($cash,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='check')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($check,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='credit card')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($credit_card,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='eft')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($eft,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='money order')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($money_order,2).'&nbsp;</td>';
		if(empty($pay_method)==true || $pay_method=='veep')
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($veep,2).'&nbsp;</td>';
		$grand_pdf.='
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col2.'; text-align:right;">'.$showCurrencySymbol.number_format($grandTot,2).'&nbsp;</td>';
		if(empty($pay_method)==false)
		$grand_pdf.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$gd_col_opt.';">&nbsp;</td>';
		$grand_pdf.='
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	'.$grandCCType.'	
	</table>';		
}

//IF DELETED EXIST THEN DISPLAY BELOW GRAND TOTALS
if($delDataExists==true){
	
	$totalCollected=$postedBlockTot + $cicoBlockTot + $prePayBlockTot; 
	$totalDeleted=$delPostedTotal + $delCICOTotal + $delPrePayTotal; 
	$finalCollected = $totalCollected - $totalDeleted;
	
	$firstCol=$grand_first_col;
	if($showFacCol){
		$firstCol=$grand_first_col+$grand_w_cols;
	}
	$lastCol=100 - ($firstCol+$grand_w_cols).'%';
	$firstCol.='%';
	$grand_w_cols.='%';
	
	//FINAL HTML
	$grand_html1=' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td class="text_b_w" colspan="3">Grand Collection</td></tr>
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
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted Posted Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($delPostedTotal,2).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted CI/CO Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($delCICOTotal,2).'&nbsp;</td>
		<td class="text_10" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Deleted Pre-Payments</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($delPrePayTotal,2).'&nbsp;</td>
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


//MANUALLY APPLIED RECORDS
if(sizeof($arrManuallyApplied)>0){
	$dataExists=true;
	$delDataExists=true;
	$content_part='';
	$arrTotals=array();
	
	foreach($arrManuallyApplied as $grpId => $grpData){
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

		$total= $grpData['cico']+$grpData['pre_payment']; 
		$arrTotals['cico']+= $grpData['cico'];
		$arrTotals['pre_payment']+= $grpData['pre_payment'];
		$arrTotals['total']+= $total;
		
		$content_part .= '
		<tr>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($grpData['cico'],2).'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($grpData['pre_payment'],2).'&nbsp;</td>
			<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.number_format($total,2).'&nbsp;</td>
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
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr><td class="text_b_w" colspan="'.$colspan.'">Manually Applied Amounts (Between '.$Start_date.' and '.$End_date.')</td></tr>
	<tr>
		<td style="width:'.$first_col.'; text-align:left;">'.$firstGroupName.' &nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:right;">CI/CO Payments&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:right;">Pre-Payments&nbsp;</td>
		<td style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>
		<td style="width:'.$last_col.'; text-align:right;">&nbsp;</td>
	</tr>'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$firstColSpan.'">Manually Applied Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrTotals['cico'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrTotals['pre_payment'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($arrTotals['total'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;"colspan="'.$colspan.'"></td></tr>
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
