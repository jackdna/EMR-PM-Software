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
}elseif($groupBy=='grpby_groups'){
	$firstGroupTitle = 'Physician Name';
	$secGroupTitle = 'Group Name';
	$subTotalTitle = 'Group Total';
}elseif($groupBy=='grpby_operators'){
	$firstGroupTitle = 'Facility Name';
	$secGroupTitle = 'Operator Name';
	$subTotalTitle = 'Operator Total';
}elseif($groupBy=='grpby_department'){
	$firstGroupTitle = 'Physician Name';
	$secGroupTitle = 'Department Name';
	$subTotalTitle = 'Department Total';
}

$show_beg_ar_col 	= $inc_opening_ar ? true : false;
$show_charges_col = $inc_summary_charges ? true : false;
$show_payment_col = $inc_payments ? true : false;
$show_adjst_col 	= $inc_adjustments ? true : false;
$show_end_ar_col	= $inc_ending_ar ? true : false;

$show_ci_co_block = $inc_ci_co_prepay ? true : false;
$show_appt_detail =	$inc_appt_detail ? true : false;
$show_appt_summary=	$inc_appt_summary ? true : false; 

if( $show_appt_detail )
{
	$firstApptGroupTitle = $firstGroupTitle;
	$secApptGroupTitle = $secGroupTitle;
	if($groupBy=='grpby_department'){
		$firstApptGroupTitle = 'Facility Name';
		$secApptGroupTitle = 'Physician Name';
	}

	if( is_array( $apptDetailsArr) && count( $apptDetailsArr) > 0 )
	{
		$appt_data .= '
		<tr id="heading_orange"><td colspan="6">&nbsp;<b>Appointment Details</b></td></tr>
		<tr>
			<td class="text_b_w" width="175">'.$firstApptGroupTitle.'</td>
			<td class="text_b_w" width="175">Patient Name</td>
			<td class="text_b_w" width="175">DOS</td>
			<td class="text_b_w" width="175">Time</td>
			<td class="text_b_w" width="175">Procedure</td>
			<td class="text_b_w" width="175">Status</td>
		</tr>';
		
		foreach($apptDetailsArr as $firstApptGroupId => $firstApptGroupData)
		{
			$firstApptGroupName = '';
			//--- GET SELECTED GROUP BY NAME ----
			if($groupBy=='grpby_physician' || $groupBy=='grpby_operators'){
				$tmp_name = $providerNameArr[$firstApptGroupId];
				if( $groupBy=='grpby_operators' && $firstApptGroupId == 0 )
					$tmp_name = 'No Operator' ;
				$firstApptGroupName = $tmp_name;
			}elseif($groupBy=='grpby_groups'){
				$firstApptGroupName = $arrAllGroups[$firstApptGroupId];
			}elseif($groupBy=='grpby_department'){
				$firstApptGroupName = $providerNameArr[$firstApptGroupId];
			}else{
				$firstApptGroupName = $fac_name_arr[$firstApptGroupId];
			}
			
			if( is_array( $firstApptGroupData) && count( $firstApptGroupData) > 0 )
			{		
				$appt_data .= '<tr><td class="text_b_w" colspan="6">'.$secApptGroupTitle.' : '.$firstApptGroupName.'</td></tr>';
				
				foreach( $firstApptGroupData as $secApptGroupData)
				{
					$secApptGroupName = '';
					if($groupBy=='grpby_physician' || $groupBy=='grpby_operators' || $groupBy=='grpby_department' ){
						$secApptGroupName = $fac_name_arr[$secApptGroupData['sec_group_id']];
					}else{
						$secApptGroupName = $providerNameArr[$secApptGroupData['sec_group_id']];				
					}	
					
					$appt_data .= '
					<tr>
						<td class="text_10">'.$secApptGroupName.'&nbsp;</td>
						<td class="text_10">'.$secApptGroupData['sa_patient_name'].'-'.$secApptGroupData['sa_patient_id'].'</td>
						<td class="text_10">'.get_date_format($secApptGroupData['sa_app_start_date']).'</td>
						<td class="text_10">'.core_time_format($secApptGroupData['sa_app_starttime']).'</td>
						<td class="text_10">'.$secApptGroupData['proc_name'].'</td>
						<td class="text_10">'.$arrApptStatus[$secApptGroupData['sa_patient_app_status_id']].'</td>
					</tr>';		
							
				}
			}
		}
	}
}
?>
<table class="rpt rpt_table rpt_table-bordered" width="1050">
	<?php print $appt_data; ?>
</table>  
  
<?php

if( $show_appt_summary )
{
	if( is_array( $apptSummaryArr) && count( $apptSummaryArr) > 0 )
	{
		$appt_summary_data .= '
		<tr id="heading_orange"><td colspan="2">&nbsp;<b>Appointment Summary</b></td></tr>
		<tr>
			<td class="text_b_w" style="width:15%;">Facility Name</td>
			<td class="text_b_w text-center" style="width:15%;">No. of Appointments</td>
		</tr>';
		
		foreach($apptSummaryArr as $tmpPhysicianId => $apptSummaryData)
		{
			if( is_array( $apptSummaryData) && count( $apptSummaryData) > 0 )
			{		
				$appt_summary_data .= '<tr><td class="text_b_w" colspan="2">Physician Name: '.$providerNameArr[$tmpPhysicianId].'</td></tr>';
				
				foreach( $apptSummaryData as $apptFacilityId => $apptSummaryCount)
				{
					;
					$appt_summary_data .= '
					<tr>
						<td class="text_10">'.$fac_name_arr[$apptFacilityId].'&nbsp;</td>
						<td class="text_10 text-center">'.(int) $apptSummaryCount.'</td>
					</tr>';		
							
				}
			}
		}
	}
}
?>
<table class="rpt rpt_table rpt_table-bordered" width="1050">
	<?php print $appt_summary_data; ?>
</table>

<?php
$colspan = 8;
if( !$show_beg_ar_col ) $colspan--;
if( !$show_charges_col ) $colspan--;
if( !$show_payment_col ) $colspan--;
if( !$show_adjst_col ) $colspan--;
if( !$show_end_ar_col ) $colspan--;


$page_content_data .= '
		<tr id="heading_orange"><td colspan="'.$colspan.'" >&nbsp;<b>Posted Payments</b></td></tr>
		<tr>
			<td class="text_b_w" style="width:18%; text-align:left;">'.$firstGroupTitle.'</td>
			'.($show_beg_ar_col ? '<td class="text_b_w" style="width:12%; text-align:right;">Beginning A/R&nbsp;</td>' : '')
			 .($show_charges_col ? '<td class="text_b_w" style="width:12%; text-align:right;">Charges</td>' : '')
			 .($show_payment_col ? '<td class="text_b_w" style="width:12%; text-align:right;">Payments</td>' : '').
			'<td class="text_b_w" style="width:11%; text-align:right;">Credit</td>
			'.($show_adjst_col ? '<td class="text_b_w" style="width:12%; text-align:right;">Adjustments</td>' : '').
			'<td class="text_b_w" style="width:11%; text-align:right;">Refunds</td>
			'.($show_end_ar_col ? '<td class="text_b_w" style="width:12%; text-align:right;">Ending A/R&nbsp;</td>' : '').'
		</tr>';
		
for($f=0;$f<count($firstGroupData);$f++){
	$firstGroupName='';
	$firstGrpTotal=array();
	$firstGroupBy = $firstGroupData[$f];
	
	//--- GET SELECTED GROUP BY NAME ----
	if($groupBy=='grpby_physician' || $groupBy=='grpby_operators'){
		$tmp_name = $providerNameArr[$firstGroupBy];
		if( $groupBy=='grpby_operators' && $firstGroupBy == 0 )
			$tmp_name = 'No Operator' ;
		$firstGroupName = $tmp_name;
	}elseif($groupBy=='grpby_groups'){
		$firstGroupName = $arrAllGroups[$firstGroupBy];
	}elseif($groupBy=='grpby_department'){
		$firstGroupName = $deptNameArr[$firstGroupBy];
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

							
			$dr_tot_beg_ar_str=$dr_tot_beg_ar_arr[$firstGroupBy][$secGroupBy];
			$dr_tot_amt_str=array_sum($dr_tot_amt_arr[$firstGroupBy][$secGroupBy]);
			$dr_tot_paid_str=array_sum($dr_tot_paid_arr[$firstGroupBy][$secGroupBy]);
			$dr_tot_credit_str=array_sum($dr_tot_credit_arr[$firstGroupBy][$secGroupBy]);
			$dr_tot_adj_str=array_sum($dr_tot_adj_arr[$firstGroupBy][$secGroupBy]);
			$dr_tot_ref_str=array_sum($dr_tot_ref_arr[$firstGroupBy][$secGroupBy]);
			$dr_tot_bal_str=$dr_tot_bal_arr[$firstGroupBy][$secGroupBy];
			
			$totChg = $dr_tot_amt_str;
			$totPay = $dr_tot_paid_str;
			$totAdj = $dr_tot_adj_str;
			
			$tmp_tot_beg_ar_str = ($CLSReports->numberFormat($dr_tot_beg_ar_str,2,'yes','','no'));
			$tmp_tot_amt_str = ($CLSReports->numberFormat($dr_tot_amt_str,2,'yes','','no'));
			$tmp_tot_paid_str = ($CLSReports->numberFormat($dr_tot_paid_str,2,'yes','','no'));
			$tmp_tot_credit_str = ($CLSReports->numberFormat($dr_tot_credit_str,2,'yes','','no'));
			$tmp_tot_adj_str = ($CLSReports->numberFormat($dr_tot_adj_str,2,'yes','','no'));
			$tmp_tot_ref_str = ($CLSReports->numberFormat($dr_tot_ref_str,2,'yes','','no'));
			$tmp_tot_bal_str = ($CLSReports->numberFormat($dr_tot_bal_str,2,'yes','','no'));
			
			if($groupBy=='grpby_physician' || $groupBy=='grpby_operators' ){
				$secGroupName = $fac_name_arr[$secGroupBy];
			}else{
				$secGroupName = $providerNameArr[$secGroupBy];				
			}			
			$globalCurrency = showCurrency();
			$page_content_data .= '
			<tr>
				<td class="text_10" style="text-align:left;">'.$secGroupName.'&nbsp;</td>
				'.($show_beg_ar_col ? '<td class="text_10 text-right" style="text-align:right">'.$tmp_tot_beg_ar_str.'&nbsp;</td>' : '')
			 	 .($show_charges_col ? '<td class="text_10 text-right" style="text-align:right">'.$tmp_tot_amt_str.'&nbsp;</td>' : '')
			 	 .($show_payment_col ? '<td class="text_10 text-right" style="text-align:right">'.$tmp_tot_paid_str.'&nbsp;</td>' : '').
				'<td class="text_10 text-right" style="text-align:right">'.$tmp_tot_credit_str.'&nbsp;</td>
				'.($show_adjst_col ? '<td class="text_10 text-right" style="text-align:right">'.$tmp_tot_adj_str.'&nbsp;</td>' : '').
				'<td class="text_10 text-right" style="text-align:right">'.$tmp_tot_ref_str.'&nbsp;</td>
				'.($show_end_ar_col ? '<td class="text_10 text-right" style="text-align:right">'.$tmp_tot_bal_str.'&nbsp;</td>' : '').'
			</tr>';	
			
			//DELETED ROW
			$delChg = $delPay = $delAdj = '';
			$delChg=$del_amounts_arr[$firstGroupBy][$secGroupBy]['CHARGES'];
			$delPay=$del_amounts_arr[$firstGroupBy][$secGroupBy]['PAYMENT'];
			$delAdj=$del_amounts_arr[$firstGroupBy][$secGroupBy]['ADJUSTMENT'];

			if(empty($delChg)==false || empty($delPay)==false || empty($delAdj)==false){
				$showTot=1;
				$page_content_data .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">Deleted Amounts :</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.''.$CLSReports->numberFormat($delChg,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.''.$CLSReports->numberFormat($delPay,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.''.$CLSReports->numberFormat($delAdj,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
				</tr>';	
				
				$totChg-=$delChg;
				$totPay-=$delPay;
				$totAdj-=$delAdj;
			}

			//PREVIOUS DEFAULT WRITE-OFF AMT
			$prev_write_off= array_sum($dr_tot_prev_writeoff_arr[$firstGroupBy][$secGroupBy]);
			if(empty($prev_write_off)==false){
				$showTot=1;
				
				$page_content_data .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Prev. Write-off :</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($prev_write_off,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
				</tr>';	
				
				$totAdj-=$prev_write_off;
			}

			if($showTot==1){
				$page_content_data.= '
				<tr><td colspan="8" class="total-row"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total :</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($dr_tot_beg_ar_str,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($totChg,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($totPay,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($dr_tot_credit_str,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($totAdj,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($dr_tot_ref_str,2,'yes','','no').'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($dr_tot_bal_str,2,'yes','','no').'&nbsp;</td>
				</tr>
				<tr><td colspan="8" class="total-row"></td></tr>';
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

	//ARRAY FOR SUBTOTALS
	$sub_tot_beg_ar_str=$CLSReports->numberFormat($firstGrpTotal['BeginingAR'],2,'yes','','no');
	$sub_tot_amt_str=$CLSReports->numberFormat($firstGrpTotal['Charges'],2,'yes','','no');
	$sub_tot_paid_str=$CLSReports->numberFormat($firstGrpTotal['Paid'],2,'yes','','no');
	$sub_tot_credit_str=$CLSReports->numberFormat($firstGrpTotal['Credit'],2,'yes','','no');
	$sub_tot_adj_str=$CLSReports->numberFormat($firstGrpTotal['Adjustment'],2,'yes','','no');
	$sub_tot_ref_str=$CLSReports->numberFormat($firstGrpTotal['Refund'],2,'yes','','no');
	$sub_tot_bal_str=$CLSReports->numberFormat($firstGrpTotal['EndingAR'],2,'yes','','no');

	$page_content_data .= '
	<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>
	<tr>
		<td class="text_10b text-right">'.$subTotalTitle.'&nbsp;</td>
		'.($show_beg_ar_col ? '<td class="text_10b text-right" style="text-align:right">'.$sub_tot_beg_ar_str.'&nbsp;</td>' : '')
		 .($show_charges_col ? '<td class="text_10b text-right" style="text-align:right">'.$sub_tot_amt_str.'&nbsp;</td>' : '')
		 .($show_payment_col ? '<td class="text_10b text-right" style="text-align:right">'.$sub_tot_paid_str.'&nbsp;</td>' : '').
		'<td class="text_10b text-right" style="text-align:right">'.$sub_tot_credit_str.'&nbsp;</td>
		'.($show_adjst_col ? '<td class="text_10b text-right" style="text-align:right">'.$sub_tot_adj_str.'&nbsp;</td>' : '').
		'<td class="text_10b text-right" style="text-align:right">'.$sub_tot_ref_str.'&nbsp;</td>
		'.($show_end_ar_col ? '<td class="text_10b text-right" style="text-align:right">'.$sub_tot_bal_str.'&nbsp;</td>' : '').'	
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
<table class="rpt rpt_table rpt_table-bordered mt5" width="1050">
	<?php print $page_content_data; ?>
    <tr><td colspan="<?php echo $colspan;?>" bgcolor="#FFFFFF">&nbsp;</td></tr>
    <tr><td class='total-row' colspan="<?php echo $colspan; ?>"></td></tr>
<?php
	$totBeg= array_sum($dr_fn_beg_ar_arr);
	$totChg= array_sum($dr_fn_tot_amt_arr);
	$totPay= array_sum($dr_fn_tot_paid_arr);
	$totCrd= array_sum($dr_fn_tot_credit_arr);
	$totAdj= array_sum($dr_fn_tot_adj_arr);
	$totRef= array_sum($dr_fn_tot_ref_arr);
	$totEnd= array_sum($dr_fn_tot_bal_arr);
	
	$disp_totBeg= ''.$CLSReports->numberFormat($totBeg,2,'yes','','no');
	$disp_totChg= ''.$CLSReports->numberFormat($totChg,2,'yes','','no');
	$disp_totPay= ''.$CLSReports->numberFormat($totPay,2,'yes','','no');
	$disp_totCrd= ''.$CLSReports->numberFormat($totCrd,2,'yes','','no');
	$disp_totAdj= ''.$CLSReports->numberFormat($totAdj,2,'yes','','no');
	$disp_totRef= ''.$CLSReports->numberFormat($totRef,2,'yes','','no');
	$disp_totEnd= ''.$CLSReports->numberFormat($totEnd,2,'yes','','no');
	
 	$totRow='
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Sub Total :</td>
		'.($show_beg_ar_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totBeg.'&nbsp;</td>' : '')
		 .($show_charges_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totChg.'&nbsp;</td>' : '')
		 .($show_payment_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totPay.'&nbsp;</td>' : '').
		'<td class="text_10b text-right" style="text-align:right">'.$disp_totCrd.'&nbsp;</td>
		'.($show_adjst_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totAdj.'&nbsp;</td>' : '').
		'<td class="text_10b text-right" style="text-align:right">'.$disp_totRef.'&nbsp;</td>
		'.($show_end_ar_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totEnd.'&nbsp;</td>' : '').'
	</tr>';
	
	if($DateRangeFor=='dos'){
		$afterDeductProcAmt = $totEnd - $balWithoutCreditCalculated;
		$totRow.='
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b text-right" colspan="'.($colspan-1).'">Procedure Balance Without Calculating Over-Payment :</td>
			<td class="text_10b text-right" style="text-align:right">'.''.$CLSReports->numberFormat($balWithoutCreditCalculated,2,'yes','','no').'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b text-right" colspan="'.($colspan-1).'">Deducting Procedure Balance :</td>
			<td class="text_10b text-right" style="text-align:right">'.''.$CLSReports->numberFormat($afterDeductProcAmt,2,'yes','','no').'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>';

	}
	
	if($DateRangeFor=='dot' || $DateRangeFor=='dor'){	
		$afterAddingchargesForNotPosted = $totChg + $chargesForNotPosted;
		$totalEndingARIncludingNotPosted  = $totEnd + $chargesForNotPosted;
		
		$disp_afterAddingchargesForNotPosted = $CLSReports->numberFormat($afterAddingchargesForNotPosted,2,'yes','','no');
		$disp_totalEndingARIncludingNotPosted= $CLSReports->numberFormat($totalEndingARIncludingNotPosted,2,'yes','','no');
		
		$totRow.='
		<tr><td colspan="'.$colspan.'">&nbsp;</td></tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Not Posted :</td>
			'.($show_beg_ar_col ? '<td class="text_10b text-right"  style="text-align:right">'.'0.00&nbsp;</td>' : '')
			 .($show_charges_col ? '<td class="text_10b text-right" style="text-align:right">'.''.$CLSReports->numberFormat($chargesForNotPosted,2,'yes','','no').'&nbsp;</td>' : '')
			 .($show_payment_col ? '<td class="text_10b text-right"  style="text-align:right">'.'0.00&nbsp;</td>' : '').
			'<td class="text_10b text-right" >'.'0.00&nbsp;</td>
			'.($show_adjst_col ? '<td class="text_10b text-right" style="text-align:right" >'.'0.00&nbsp;</td>' : '').
			'<td class="text_10b text-right"  style="text-align:right">'.'0.00&nbsp;</td>
			'.($show_end_ar_col ? '<td class="text_10b text-right"  style="text-align:right">'.'0.00&nbsp;</td>' : '').'
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total :</td>
			'.($show_beg_ar_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totBeg.'&nbsp;</td>' : '')
			 .($show_charges_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_afterAddingchargesForNotPosted.'&nbsp;</td>' : '')
			 .($show_payment_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totPay.'&nbsp;</td>' : '').
			'<td class="text_10b text-right" style="text-align:right">'.$disp_totCrd.'&nbsp;</td>
			'.($show_adjst_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totAdj.'&nbsp;</td>' : '').
			'<td class="text_10b text-right" style="text-align:right">'.$disp_totRef.'&nbsp;</td>
			'.($show_end_ar_col ? '<td class="text_10b text-right" style="text-align:right">'.$disp_totalEndingARIncludingNotPosted.'&nbsp;</td>' : '').'
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>';
	}
	echo $totRow;
?>    
    <tr><td class='total-row' colspan="8"></td></tr>
</table>

<?php

if( $show_ci_co_block ) 
{
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
			$title1= 'title="Refund : '.$CLSReports->numberFormat($arrCicoRefundFirst[$firstGroupBy],2).'"';
		}
		if($arrCicoRefundSec[$firstGroupBy]>0){
			$arrCICONotAppliedSec[$firstGroupBy]-=$arrCicoRefundSec[$firstGroupBy];
			$fontColor2='#FF0000';
			$title2= 'title="Refund : '.$CLSReports->numberFormat($arrCicoRefundSec[$firstGroupBy],2).'"';
		}
				
		$totAmt = $arrCICONotAppliedFirst[$firstGroupBy] + $arrCICONotAppliedSec[$firstGroupBy];
		$arrTot['First']+=$arrCICONotAppliedFirst[$firstGroupBy];
		$arrTot['Second']+=$arrCICONotAppliedSec[$firstGroupBy];
		
		$notAppPart.='
		<tr style="height:25px">
			<td class="text_10 white" style="text-align:left;">'.$firstGroupName.'</td>
			<td class="text_10 white" style="text-align:right; color:'.$fontColor1.'" '.$title1.'>'.$CLSReports->numberFormat($arrCICONotAppliedFirst[$firstGroupBy],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right; color:'.$fontColor2.'" '.$title2.'>'.$CLSReports->numberFormat($arrCICONotAppliedSec[$firstGroupBy],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right;">'.$CLSReports->numberFormat($totAmt,2).'&nbsp;</td>
			<td class="text_10 white"></td>
		</tr>';
	}
	
	$arrTotNotApp['First']+=$arrTot['First'];
	$arrTotNotApp['Second']+=$arrTot['Second'];
	
	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered mt5" width="1050">
		<tr id="heading_orange"><td colspan="5">&nbsp;Unapplied CI/CO Amounts</td></tr>
		<tr>
			<td class="text_b_w" width="210" style="text-align:center;">'.$firstTitle.'</td>
			<td class="text_b_w" width="210" style="text-align:center;">Before '.$Start_date.'</td>
			<td class="text_b_w" width="210" style="text-align:center;">From '.$Start_date.' To '.$End_date.'</td>	
			<td class="text_b_w" width="210" style="text-align:center;">Till '.$End_date.'</td>
			<td class="text_b_w" width="210">&nbsp;</td>
		</tr>'.
		$notAppPart.'
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Total CI/CO :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'],2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['Second'],2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'] + $arrTot['Second'],2).'&nbsp;</td>
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
			$title1= 'title="Refund : '.$CLSReports->numberFormat($arrPrePayRefundFirst[$firstGroupBy],2).'"';
		}
		if($arrPrePayRefundSec[$firstGroupBy]>0){
			$arrPrePayNotAppliedSec[$firstGroupBy]-=$arrPrePayRefundSec[$firstGroupBy];
			$fontColor2='#FF0000';
			$title2= 'title="Refund : '.$CLSReports->numberFormat($arrPrePayRefundSec[$firstGroupBy],2).'"';			
		}

		$totAmt = $arrPrePayNotAppliedFirst[$firstGroupBy] + $arrPrePayNotAppliedSec[$firstGroupBy];
		$arrTot['First']+=$arrPrePayNotAppliedFirst[$firstGroupBy];
		$arrTot['Second']+=$arrPrePayNotAppliedSec[$firstGroupBy];
				
		$notAppPart.='
		<tr style="height:25px">
			<td class="text_10 white" style="text-align:left;">'.$firstGroupName.'</td>
			<td class="text_10 white" style="text-align:right; color:'.$fontColor1.'" '.$title1.'>'.$CLSReports->numberFormat($arrPrePayNotAppliedFirst[$firstGroupBy],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right; color:'.$fontColor2.'" '.$title2.'>'.$CLSReports->numberFormat($arrPrePayNotAppliedSec[$firstGroupBy],2).'&nbsp;</td>
			<td class="text_10 white" style="text-align:right;">'.$CLSReports->numberFormat($totAmt,2).'&nbsp;</td>
			<td class="text_10 white"></td>
		</tr>';
	}
	
	$arrTotNotApp['First']+=$arrTot['First'];
	$arrTotNotApp['Second']+=$arrTot['Second'];

	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered mt5" width="1050">
		<tr id="heading_orange"><td colspan="7">&nbsp;Unapplied Pre Payment Amounts</td></tr>
		<tr>
			<td class="text_b_w" width="210" style="text-align:center;">'.$firstTitle.'</td>
			<td class="text_b_w" width="210" style="text-align:center;">Before '.$Start_date.'</td>
			<td class="text_b_w" width="210" style="text-align:center;">From '.$Start_date.' To '.$End_date.'</td>	
			<td class="text_b_w" width="210" style="text-align:center;">Till '.$End_date.'</td>
			<td class="text_b_w" width="210">&nbsp;</td>
		</tr>'.
		$notAppPart.'
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;">Total Pre Payment :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'],2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['Second'],2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'] + $arrTot['Second'],2).'&nbsp;</td>
			<td class="text_10b white"></td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';
echo $not_app_csv;
}
$not_app_csv='';
if(count($arrGrpCICONotApplied)>0 || count($arrGrpPrePayNotApplied)>0){
	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered" width="1050">
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;" width="210">Total Not Applied :</td>
			<td class="text_10b white" style="text-align:right;" width="210">'.$CLSReports->numberFormat($arrTotNotApp['First'],2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;" width="210">'.$CLSReports->numberFormat($arrTotNotApp['Second'],2).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;" width="210">'.$CLSReports->numberFormat($arrTotNotApp['First'] + $arrTotNotApp['Second'],2).'&nbsp;</td>
			<td class="text_10b white" width="210"></td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';
echo $not_app_csv;		
}

}

echo '</page>';


// REFUND - CI/CO AND PRE PAYMENTS	
if(sizeof($arrMainOthers)>0){
	$dataExists=1;
	$pdfData2 = $csvFileData2 = '';
	$arrTot = array();
	foreach($arrMainOthers as $phyId => $patIds){
		$arrSubTot=array();

		$pdfData2 .='<tr><td class="text_b_w" colspan="8" align="left">Physician Name : '.$providerNameArr[$phyId].'</td></tr>';
		$csvFileData2 .='<tr><td class="text_b_w" colspan="8" align="left">Physician Name : '.$providerNameArr[$phyId].$phyId.'</td></tr>';
	
		foreach($patIds as $patId){
			
			$patientName='';
			// CI/CO
			foreach($arrCICORefunds[$phyId][$patId] as $cicoDetId => $facId){
				
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $arrPatDetail[$cicoDetId]['lname'];
				$patient_name_arr["FIRST_NAME"] = $arrPatDetail[$cicoDetId]['fname'];
				$patient_name_arr["MIDDLE_NAME"] = $arrPatDetail[$cicoDetId]['mname'];
				$patientName = changeNameFormat($patient_name_arr);
				$patientName.= ' - '.$patId;
				
				$paidDate = $arrPatDetail[$cicoDetId]['pay_date'];
				$paidAmt =  $arrPatDetail[$cicoDetId]['pay_amt'];
				$arrSubTot['paidAmt']+= $paidAmt;
				
				foreach($arrCICORefundsDet[$cicoDetId] as $sno => $patDetails){
					$arrSubTot['refAmt']+= $patDetails['ref_amt'];

					$csvFileData2 .='<tr>
						<td class="text_10" bgcolor="#FFFFFF">'.$patientName.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center" width="100">'.$paidDate.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="left">'.$fac_name_arr[$facId].'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center">'.$patDetails['ref_date'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left">Check In/Out</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right"> '.$showCurrencySymbol.$CLSReports->numberFormat($paidAmt,2,'yes','','no').'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$showCurrencySymbol.$CLSReports->numberFormat($patDetails['ref_amt'],2,'yes','','no').'</td>
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
					$patientName.= ' - '.$patId;
				}
				
				$paidDate = $arrPatDetail[$pmtId]['pay_date'];
				$paidAmt =  $arrPatDetail[$pmtId]['pay_amt'];
				$arrSubTot['paidAmt']+= $paidAmt;
				
				foreach($arrPMTRefundsDet[$pmtId] as $sno => $patDetails){
					$arrSubTot['refAmt']+= $patDetails['ref_amt'];
					
					$csvFileData2 .='<tr>
						<td class="text_10" bgcolor="#FFFFFF" >'.$patientName.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center">'.$paidDate.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="left">'.$fac_name_arr[$facId].'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center">'.$patDetails['ref_date'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left">Pre-Payment</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right"> '.$showCurrencySymbol.$CLSReports->numberFormat($paidAmt,2,'yes','','no').'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$showCurrencySymbol.$CLSReports->numberFormat($patDetails['ref_amt'],2,'yes','','no').'</td>
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
		<tr><td class="total-row" colspan="8"></td></tr>	
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF"></td>
			<td class="text_10b" bgcolor="#FFFFFF"></td>
			<td class="text_10b" bgcolor="#FFFFFF"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Sub Total:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> </td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$showCurrencySymbol.$CLSReports->numberFormat($arrSubTot['paidAmt'],2,'yes','','no').'</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$showCurrencySymbol.$CLSReports->numberFormat($arrSubTot['refAmt'],2,'yes','','no').'</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="8"></td></tr>';			
	}

	$arrGrandTot['paidAmt']+= $arrTot['paidAmt'];
	$arrGrandTot['refAmt']+= $arrTot['refAmt'];
	
	// MAIN TOTAL
	$otherHTML=' 
	<table class="rpt rpt_table rpt_table-bordered mt5" width="1050">					
	<tr id="heading_orange"><td colspan="8">&nbsp;<b>Refunds of CI/CO and Pre-Payments</b></td></tr>
	<tr>
		<td class="text_b_w" width="210" style="text-align:left">Patient Name-ID</td>
		<td class="text_b_w" width="110" style="text-align:center">Paid Date</td>
		<td class="text_b_w" width="150" style="text-align:center">Facility</td>
		<td class="text_b_w" width="110" style="text-align:center">Refund Date</td>
		<td class="text_b_w" width="110" style="text-align:left">Type</td>
		<td class="text_b_w" width="110" style="text-align:right">Payment &nbsp;</td>
		<td class="text_b_w" width="110" style="text-align:right">Refund &nbsp;</td>
		<td class="text_b_w" width="130" style="text-align:center">Method</td>
	</tr>
	'.$csvFileData2.'
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="4">CI/CO & Pre-Payments Total:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$showCurrencySymbol.$CLSReports->numberFormat($arrTot['paidAmt'],2,'yes','','no').'</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$showCurrencySymbol.$CLSReports->numberFormat($arrTot['refAmt'],2,'yes','','no').'</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="8"></td></tr>
	</table>';

	echo $otherHTML;
}

$page_content = ob_get_contents();
ob_end_clean();
?>
