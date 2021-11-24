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
ob_start();
//--- START FACILITY LOOP ----
$firstGroupData = array_keys($checkInDataArr);
$page_content_data = NULL;
$total_charges_grand_arr = array();
$total_posted_grand_arr = array();

$firstGroupTitle = 'Facility Name';
$secGroupTitle = 'Physician Name';
$subTotalTitle = 'Physician Total';
if($groupBy=='grpby_facility'){
	$firstGroupTitle = 'Physician Name';
	$secGroupTitle = 'Facility Name';
	$subTotalTitle = 'Facility Total';
}

$colspan=9;
$subColspan=8;

$tdCPTCat2Title='';
//IF CAT2 NOT SELECTED THEN AVOID EXTRA LOOPING.
if(empty($str_cpt_cat_2) === false){
	$cpt_cat_2=explode(',', $str_cpt_cat_2);
	foreach($cpt_cat_2 as $cat2){
		if($cat2==1){
			$arr_cpt_cats[1]='Service';
		}
		if($cat2==2){
			$arr_cpt_cats[2]='Material';
		}		
	}
	$colspan=10;
	$subColspan=9;
}
else{
	$arr_cpt_cats=array('0'=>'All');
}

$firstColWidth=13;
$secColWidth=13;
$colWidth=(100-($firstColWidth+$secColWidth))/ ($colspan-2);

if(empty($str_cpt_cat_2) === false)
{
	$tdCPTCat2Title='<td class="text_b_w" style="text-align:left; width:'.$colWidth.'%">CPT CAT2</td>';
}

$page_content_data .= '
		<tr>
			<td class="text_b_w" style="text-align:left; width:'.$firstColWidth.'%">'.$firstGroupTitle.'</td>
			<td class="text_b_w" style="text-align:left; width:'.$secColWidth.'%">'.$secGroupTitle.'</td>
			'.$tdCPTCat2Title.'
			<td class="text_b_w" style="text-align:right; width:'.$colWidth.'%">Beginning A/R&nbsp;</td>
			<td class="text_b_w" style="text-align:right; width:'.$colWidth.'%">Charges</td>
			<td class="text_b_w" style="text-align:right; width:'.$colWidth.'%">Payments</td>
			<td class="text_b_w" style="text-align:right; width:'.$colWidth.'%">Credit</td>
			<td class="text_b_w" style="text-align:right; width:'.$colWidth.'%">Adjustments</td>
			<td class="text_b_w" style="text-align:right; width:'.$colWidth.'%">Refunds</td>
			<td class="text_b_w" style="text-align:right; width:'.$colWidth.'%">Ending A/R&nbsp;</td>
		</tr>';
		
for($f=0;$f<count($firstGroupData);$f++){
	$firstGroupName='';
	$firstGrpTotal=array();
	$firstGroupBy = $firstGroupData[$f];
	
	//--- GET FACILITY NAME ----
	if($groupBy=='grpby_physician'){
		$firstGroupName = $providerNameArr[$firstGroupBy];
	}else{
		$firstGroupName = $fac_name_arr[$firstGroupBy];
	}
	
	$page_content_data .= '
		<tr>
			<td class="text_b_w" colspan="'.$colspan.'">'.$secGroupTitle.' : '.$firstGroupName.'</td>
		</tr>';
	
	//--- GET PROVIDER DATA UNDER FACILITY ---
	$secGroupData = array_keys($checkInDataArr[$firstGroupBy]);
	
	for($p=0;$p<count($secGroupData);$p++){
		$showTot=0;
		$secGroupName='';
		$secGroupBy=$secGroupData[$p];

		foreach($arr_cpt_cats as $cat2_key=> $cat2_name){		
		
			$dr_tot_beg_ar_str=$dr_tot_beg_ar_arr[$firstGroupBy][$secGroupBy][$cat2_key];
			$dr_tot_amt_str=array_sum($dr_tot_amt_arr[$firstGroupBy][$secGroupBy][$cat2_key]);
			$dr_tot_paid_str=array_sum($dr_tot_paid_arr[$firstGroupBy][$secGroupBy][$cat2_key]);
			$dr_tot_credit_str=array_sum($dr_tot_credit_arr[$firstGroupBy][$secGroupBy][$cat2_key]);
			$dr_tot_adj_str=array_sum($dr_tot_adj_arr[$firstGroupBy][$secGroupBy][$cat2_key]);
			$dr_tot_ref_str=array_sum($dr_tot_ref_arr[$firstGroupBy][$secGroupBy][$cat2_key]);
			$dr_tot_bal_str=$dr_tot_bal_arr[$firstGroupBy][$secGroupBy][$cat2_key];

			$totChg = $dr_tot_amt_str;
			$totPay = $dr_tot_paid_str;
			$totAdj = $dr_tot_adj_str;
			
			if($groupBy=='grpby_physician'){
				$secGroupName = $fac_name_arr[$secGroupBy];
			}else{
				$secGroupName = $providerNameArr[$secGroupBy];				
			}			
			$globalCurrency = showCurrency();

			$tdCPTCat2Data='';
			if(empty($str_cpt_cat_2) === false)
			{
				$tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$cat2_name.'</td>';
			}
			
			$page_content_data .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.wordwrap($secGroupName, 15, "<br>\n", true).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>
				'.$tdCPTCat2Data.'
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_beg_ar_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_amt_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_paid_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_credit_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_adj_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_ref_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_bal_str,2,1).'&nbsp;</td>
			</tr>';	
			
			//DELETED ROW
			$delChg = $delPay = $delAdj = '';
			$delChg=$del_amounts_arr[$firstGroupBy][$secGroupBy][$cat2_key]['CHARGES'];
			$delPay=$del_amounts_arr[$firstGroupBy][$secGroupBy][$cat2_key]['PAYMENT'];
			$delAdj=$del_amounts_arr[$firstGroupBy][$secGroupBy][$cat2_key]['ADJUSTMENT'];

			if(empty($delChg)==false || empty($delPay)==false || empty($delAdj)==false){
				$showTot=1;
				$tdCPTCat2Data='';
				
				if(empty($str_cpt_cat_2) === false)
				{	
					$tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF"></td>';
				}
				
				$page_content_data .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					'.$tdCPTCat2Data.'
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">Deleted Amounts :</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.$CLSReports->numberFormat($delChg,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.$CLSReports->numberFormat($delPay,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.$CLSReports->numberFormat($delAdj,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
				</tr>';	
				
				$totChg-=$delChg;
				$totPay-=$delPay;
				$totAdj-=$delAdj;
			}

			//PREVIOUS DEFAULT WRITE-OFF AMT
			$prev_write_off= array_sum($dr_tot_prev_writeoff_arr[$firstGroupBy][$secGroupBy][$cat2_key]);
			if(empty($prev_write_off)==false){
				$showTot=1;
				$tdCPTCat2Data='';
				
				if(empty($str_cpt_cat_2) === false)
				{
					$tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF"></td>';
				}
				
				$page_content_data .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					'.$tdCPTCat2Data.'
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Prev. Write-off :</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($prev_write_off,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
				</tr>';	
				
				$totAdj-=$prev_write_off;
			}

			if($showTot==1){
				$tdCPTCat2Data='';
				
				if(empty($str_cpt_cat_2) === false)
				{
					$tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF"></td>';
				}
				
				$page_content_data.= '
				<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
					'.$tdCPTCat2Data.'
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total :</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_beg_ar_str,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totChg,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totPay,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_credit_str,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totAdj,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_ref_str,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_bal_str,2,1).'&nbsp;</td>
				</tr>
				<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
			}
				
			//ARRAY FOR SUBTOTALS
			$firstGrpTotal['BeginingAR']+=$dr_tot_beg_ar_str;
			$firstGrpTotal['Charges']+=$totChg;
			$firstGrpTotal['Paid']+=$totPay;
			$firstGrpTotal['Credit']+=$dr_tot_credit_str;
			$firstGrpTotal['Adjustment']+=$totAdj;
			$firstGrpTotal['Refund']+=$dr_tot_ref_str;
			$firstGrpTotal['EndingAR']+=$dr_tot_bal_str;			
		}
	}

	//ARRAY FOR SUBTOTALS
	$sub_tot_beg_ar_str=numberformat($firstGrpTotal['BeginingAR'],2,1);
	$sub_tot_amt_str=numberformat($firstGrpTotal['Charges'],2,1);
	$sub_tot_paid_str=numberformat($firstGrpTotal['Paid'],2,1);
	$sub_tot_credit_str=numberformat($firstGrpTotal['Credit'],2,1);
	$sub_tot_adj_str=numberformat($firstGrpTotal['Adjustment'],2,1);
	$sub_tot_ref_str=numberformat($firstGrpTotal['Refund'],2,1);
	$sub_tot_bal_str=numberformat($firstGrpTotal['EndingAR'],2,1);

	$tdCPTCat2Data='';
	if(empty($str_cpt_cat_2) === false)
	{
		$tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF"></td>';
	}
	
	$page_content_data .= '
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		'.$tdCPTCat2Data.'
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$subTotalTitle.'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$sub_tot_beg_ar_str.'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$sub_tot_amt_str.'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$sub_tot_paid_str.'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$sub_tot_credit_str.'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$sub_tot_adj_str.'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$sub_tot_ref_str.'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$sub_tot_bal_str.'&nbsp;</td>
	</tr>
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	';	

	//ARRAY FOR GRAND TOTALS
	$dr_fn_beg_ar_arr[]=$firstGrpTotal['BeginingAR'];
	$dr_fn_tot_amt_arr[]=$firstGrpTotal['Charges'];
	$dr_fn_tot_paid_arr[]=$firstGrpTotal['Paid'];
	$dr_fn_tot_credit_arr[]=$firstGrpTotal['Credit'];
	$dr_fn_tot_adj_arr[]=$firstGrpTotal['Adjustment'];
	$dr_fn_tot_ref_arr[]=$firstGrpTotal['Refund'];
	$dr_fn_tot_bal_arr[]=$firstGrpTotal['EndingAR'];
}

?>
<table style="width:100%" class="rpt_table rpt_table-bordered">
	<?php print $page_content_data; ?>
    <tr><td colspan="<?php echo $colspan;?>" bgcolor="#FFFFFF">&nbsp;</td></tr>
    <tr><td class='total-row' colspan="<?php echo $colspan;?>"></td></tr>
<?php
	$totBeg= array_sum($dr_fn_beg_ar_arr);
	$totChg= array_sum($dr_fn_tot_amt_arr);
	$totPay= array_sum($dr_fn_tot_paid_arr);
	$totCrd= array_sum($dr_fn_tot_credit_arr);
	$totAdj= array_sum($dr_fn_tot_adj_arr);
	$totRef= array_sum($dr_fn_tot_ref_arr);
	$totEnd= array_sum($dr_fn_tot_bal_arr);

	$tdCPTCat2Data='';
	if(empty($str_cpt_cat_2) === false)
	{
		$tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF"></td>';
	}
	
    $totRow='
	<tr>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		'.$tdCPTCat2Data.'
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Sub Total :</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totBeg,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totChg,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totPay,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCrd,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totAdj,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totRef,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totEnd,2,1).'&nbsp;</td>
    </tr>';
	
	if($DateRangeFor=='dos'){
		$afterDeductProcAmt = $totEnd - $balWithoutCreditCalculated;
		$totRow.='
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$subColspan.'">Procedure Balance Without Calculating Over-Payment :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($balWithoutCreditCalculated,2,1).'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$subColspan.'">Deducting Procedure Balance :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($afterDeductProcAmt,2,1).'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>';

	}
	
	if($DateRangeFor=='dot'){	
		$afterAddingchargesForNotPosted = $totChg + $chargesForNotPosted;
		$totalEndingARIncludingNotPosted  = $totEnd + $chargesForNotPosted;

		$tdCPTCat2Data='';
		if(empty($str_cpt_cat_2) === false)
		{
			$tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF"></td>';
		}
		
		$totRow.='
		<tr><td bgcolor="#FFFFFF" colspan="'.$colspan.'">&nbsp;</td></tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			'.$tdCPTCat2Data.'
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Not Posted :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($chargesForNotPosted,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			'.$tdCPTCat2Data.'
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totBeg,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($afterAddingchargesForNotPosted,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totPay,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCrd,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totAdj,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totRef,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totalEndingARIncludingNotPosted,2,1).'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>';
	}
	echo $totRow;
?>    
    <tr><td class='total-row' colspan="<?php echo $colspan;?>"></td></tr>
</table>

<?php
// CI/CO NOT APPLIED
$notAppPart='';
$arrTot=array();
$arrTotNotApp=array();
if(count($arrGrpCICONotApplied)>0){
	foreach($arrGrpCICONotApplied as $firstGroupBy){
		$firstGroupName='';
		if($groupBy=='grpby_physician'){
			$firstTitle='Physician';
			$firstGroupName = $providerNameArr[$firstGroupBy];
		}else{
			$firstTitle='Facility';
			$firstGroupName = $fac_name_arr[$firstGroupBy];
		}

		//REFUNDS
		$fontColor1= $fontColor2='#000';
		$title1 = $title2='';
		if($arrCicoRefundFirst[$firstGroupBy]>0){
			$arrCICONotAppliedFirst[$firstGroupBy]-=$arrCicoRefundFirst[$firstGroupBy];
			$fontColor1='#FF0000';
			$title1= 'title="Refund : '.$CLSReports->numberFormat($arrCicoRefundFirst[$firstGroupBy],2,1).'"';
		}
		if($arrCicoRefundSec[$firstGroupBy]>0){
			$arrCICONotAppliedSec[$firstGroupBy]-=$arrCicoRefundSec[$firstGroupBy];
			$fontColor2='#FF0000';
			$title2= 'title="Refund : '.$CLSReports->numberFormat($arrCicoRefundSec[$firstGroupBy],2,1).'"';
		}
				
		$totAmt = $arrCICONotAppliedFirst[$firstGroupBy] + $arrCICONotAppliedSec[$firstGroupBy];
		$arrTot['First']+=$arrCICONotAppliedFirst[$firstGroupBy];
		$arrTot['Second']+=$arrCICONotAppliedSec[$firstGroupBy];
		
		$notAppPart.='
		<tr style="height:25px">
			<td class="text_10 white" style="text-align:left;">'.$firstGroupName.'</td>
			<td class="text_10 white" style="text-align:right; color:'.$fontColor1.'" '.$title1.'>'.$CLSReports->numberFormat($arrCICONotAppliedFirst[$firstGroupBy],2,1).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right; color:'.$fontColor2.'" '.$title2.'>'.$CLSReports->numberFormat($arrCICONotAppliedSec[$firstGroupBy],2,1).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right;">'.$CLSReports->numberFormat($totAmt,2,1).'&nbsp;</td>
			<td class="text_10 white"></td>
		</tr>';
	}
	
	$arrTotNotApp['First']+=$arrTot['First'];
	$arrTotNotApp['Second']+=$arrTot['Second'];
	
	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered" width="100%">
		<tr id="heading_orange"><td style="text-align:left; width:100%" colspan="5">&nbsp;Unapplied CI/CO Amounts</td></tr>
		<tr>
			<td class="text_b_w" width="205" style="text-align:center;">'.$firstTitle.'</td>
			<td class="text_b_w" width="205" style="text-align:center;">Before '.$Start_date.'</td>
			<td class="text_b_w" width="205" style="text-align:center;">From '.$Start_date.' To '.$End_date.'</td>	
			<td class="text_b_w" width="205" style="text-align:center;">Till '.$End_date.'</td>
			<td class="text_b_w" width="205" >&nbsp;</td>
		</tr>'.
		$notAppPart.'
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Total CI/CO :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'] + $arrTot['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white">&nbsp;</td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';
echo $not_app_csv;
}

// PRE PAYMENT NOT APPLIED
$notAppPart='';
$arrTot=array();
if(count($arrGrpPrePayNotApplied)>0){
	foreach($arrGrpPrePayNotApplied as $firstGroupBy){
		$firstGroupName='';
		if($groupBy=='grpby_physician'){
			$firstTitle='Physician';
			$firstGroupName = $providerNameArr[$firstGroupBy];
		}else{
			$firstTitle='Facility';
			$firstGroupName = $fac_name_arr[$firstGroupBy];
		}

		//REFUNDS
		$fontColor1= $fontColor2='#000';
		$title1 = $title2='';
		if($arrPrePayRefundFirst[$firstGroupBy]>0){
			$arrPrePayNotAppliedFirst[$firstGroupBy]-=$arrPrePayRefundFirst[$firstGroupBy];
			$fontColor1='#FF0000';
			$title1= 'title="Refund : '.$CLSReports->numberFormat($arrPrePayRefundFirst[$firstGroupBy],2,1).'"';
		}
		if($arrPrePayRefundSec[$firstGroupBy]>0){
			$arrPrePayNotAppliedSec[$firstGroupBy]-=$arrPrePayRefundSec[$firstGroupBy];
			$fontColor2='#FF0000';
			$title2= 'title="Refund : '.$CLSReports->numberFormat($arrPrePayRefundSec[$firstGroupBy],2,1).'"';			
		}

		$totAmt = $arrPrePayNotAppliedFirst[$firstGroupBy] + $arrPrePayNotAppliedSec[$firstGroupBy];
		$arrTot['First']+=$arrPrePayNotAppliedFirst[$firstGroupBy];
		$arrTot['Second']+=$arrPrePayNotAppliedSec[$firstGroupBy];
				
		$notAppPart.='
		<tr style="height:25px">
			<td class="text_10 white" style="text-align:left;">'.$firstGroupName.'</td>
			<td class="text_10 white" style="text-align:right; color:'.$fontColor1.'" '.$title1.'>'.$CLSReports->numberFormat($arrPrePayNotAppliedFirst[$firstGroupBy],2,1).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right; color:'.$fontColor2.'" '.$title2.'>'.$CLSReports->numberFormat($arrPrePayNotAppliedSec[$firstGroupBy],2,1).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right;">'.$CLSReports->numberFormat($totAmt,2,1).'&nbsp;</td>
			<td class="text_10 white"></td>
		</tr>';
	}
	
	$arrTotNotApp['First']+=$arrTot['First'];
	$arrTotNotApp['Second']+=$arrTot['Second'];

	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered" width="100%">
		<tr id="heading_orange"><td style="text-align:left;" colspan="5">&nbsp;Unapplied Pre Payment Amounts</td></tr>
		<tr>
			<td class="text_b_w" width="205" style="text-align:center;">'.$firstTitle.'</td>
			<td class="text_b_w" width="205" style="text-align:center;">Before '.$Start_date.'</td>
			<td class="text_b_w" width="205" style="text-align:center;">From '.$Start_date.' To '.$End_date.'</td>	
			<td class="text_b_w" width="205" style="text-align:center;">Till '.$End_date.'</td>
			<td class="text_b_w" width="205" >&nbsp;</td>
		</tr>'.
		$notAppPart.'
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Total Pre Payment :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'] + $arrTot['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white"></td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';
echo $not_app_csv;
}
$not_app_csv='';
if(count($arrGrpCICONotApplied)>0 || count($arrGrpPrePayNotApplied)>0){
	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered" width="100%">
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" width="205" style="text-align:right;">Total Not Applied :</td>
			<td class="text_10b white" width="205" style="text-align:right;">'.$CLSReports->numberFormat($arrTotNotApp['First'],2,1).'&nbsp;</td>
			<td class="text_10b white" width="205" style="text-align:right;">'.$CLSReports->numberFormat($arrTotNotApp['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" width="205" style="text-align:right;">'.$CLSReports->numberFormat($arrTotNotApp['First'] + $arrTotNotApp['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" width="205"></td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';
echo $not_app_csv;		
}

echo '</page>';


// REFUND - CI/CO AND PRE PAYMENTS	
if(sizeof($arrMainOthers)>0){
	$dataExists=1;
	$pdfData2 = $csvFileData2 = '';
	$arrTot = array();
	foreach($arrMainOthers as $phyId => $patIds){
		$arrSubTot=array();

		$pdfData2 .='<tr><td class="text_b_w" colspan="9" align="left">Physician Name : '.$providerNameArr[$phyId].'</td></tr>';
		$csvFileData2 .='<tr><td class="text_b_w" colspan="9" align="left">Physician Name : '.$providerNameArr[$phyId].'</td></tr>';
	
		foreach($patIds as $patId){
			
			$patientName='';
			// CI/CO
			foreach($arrCICORefunds[$phyId][$patId] as $cicoDetId => $facId){
				
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $arrPatDetail[$cicoDetId]['lname'];
				$patient_name_arr["FIRST_NAME"] = $arrPatDetail[$cicoDetId]['fname'];
				$patient_name_arr["MIDDLE_NAME"] = $arrPatDetail[$cicoDetId]['mname'];
				$patientName = changeNameFormat($patient_name_arr);
				//$patientName.= ' - '.$patId;
				
				$paidDate = $arrPatDetail[$cicoDetId]['pay_date'];
				$paidAmt =  $arrPatDetail[$cicoDetId]['pay_amt'];
				$arrSubTot['paidAmt']+= $paidAmt;
				
				foreach($arrCICORefundsDet[$cicoDetId] as $sno => $patDetails){
					$arrSubTot['refAmt']+= $patDetails['ref_amt'];

					$csvFileData2 .='<tr>
						<td class="text_10" bgcolor="#FFFFFF">'.$patientName.'</td>
						<td class="text_10" bgcolor="#FFFFFF">'.$patId.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center" width="100">'.$paidDate.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="left">'.$fac_name_arr[$facId].'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center">'.$patDetails['ref_date'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left">Check In/Out</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($paidAmt,2,1).'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2,1).'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$patDetails['method'].'</td>
					</tr>';	
				}
			}
			unset($arrCICORefunds[$phyId][$patId]);

			// PRE-PAYMENTS
			foreach($arrPMTRefunds[$phyId][$patId] as $pmtId => $facId){
				
				if($patientName==''){
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $arrPatDetail[$pmtId]['lname'];
					$patient_name_arr["FIRST_NAME"] = $arrPatDetail[$pmtId]['fname'];
					$patient_name_arr["MIDDLE_NAME"] = $arrPatDetail[$pmtId]['mname'];
					$patientName = changeNameFormat($patient_name_arr);
					//$patientName.= ' - '.$patId;
				}
				
				$paidDate = $arrPatDetail[$pmtId]['pay_date'];
				$paidAmt =  $arrPatDetail[$pmtId]['pay_amt'];
				$arrSubTot['paidAmt']+= $paidAmt;
				
				foreach($arrPMTRefundsDet[$pmtId] as $sno => $patDetails){
					$arrSubTot['refAmt']+= $patDetails['ref_amt'];
					
					$csvFileData2 .='<tr>
						<td class="text_10" bgcolor="#FFFFFF" >'.$patientName.'</td>
						<td class="text_10" bgcolor="#FFFFFF" >'.$patId.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center">'.$paidDate.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="left">'.$fac_name_arr[$facId].'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center">'.$patDetails['ref_date'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left">Pre-Payment</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($paidAmt,2,1).'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2,1).'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$patDetails['method'].'</td>
					</tr>';	
				}
			}
			unset($arrPMTRefunds[$phyId][$patId]);
		}
		
		$arrTot['paidAmt']+= $arrSubTot['paidAmt'];
		$arrTot['refAmt']+= $arrSubTot['refAmt'];
		
		//SUB TOTAL
		$csvFileData2 .='
		<tr><td class="total-row" colspan="9"></td></tr>	
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF"></td>
			<td class="text_10b" bgcolor="#FFFFFF"></td>
			<td class="text_10b" bgcolor="#FFFFFF"></td>
			<td class="text_10b" bgcolor="#FFFFFF"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Sub Total:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> </td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrSubTot['paidAmt'],2,1).'</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrSubTot['refAmt'],2,1).'</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="9"></td></tr>';			
	}

	$arrGrandTot['paidAmt']+= $arrTot['paidAmt'];
	$arrGrandTot['refAmt']+= $arrTot['refAmt'];
	
	// MAIN TOTAL
	$otherHTML=' 
	<table class="rpt rpt_table rpt_table-bordered" width="100%">					
	<tr id="heading_orange"><td colspan="9">&nbsp;Refunds of CI/CO and Pre-Payments</td></tr>
	<tr>
		<td class="text_b_w" width="120" style="text-align:left">Patient Name</td>
		<td class="text_b_w" width="100" style="text-align:left">Patient-ID</td>
		<td class="text_b_w" width="100" style="text-align:center">Paid Date</td>
		<td class="text_b_w" width="140" style="text-align:center">Facility</td>
		<td class="text_b_w" width="100" style="text-align:center">Refund Date</td>
		<td class="text_b_w" width="100" style="text-align:left">Type</td>
		<td class="text_b_w" width="100" style="text-align:right">Payment &nbsp;</td>
		<td class="text_b_w" width="100" style="text-align:right">Refund &nbsp;</td>
		<td class="text_b_w" width="100" style="text-align:center">Method</td>
	</tr>
	'.$csvFileData2.'
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">CI/CO & Pre-Payments Total:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTot['paidAmt'],2,1).'</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrTot['refAmt'],2,1).'</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="9"></td></tr>
	</table>';

	echo $otherHTML;
}



$page_content = ob_get_contents();
ob_end_clean();
?>
