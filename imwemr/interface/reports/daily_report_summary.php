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
FILE : DAILY_BALANCE_SUMMERY.PHP
PURPOSE :  DAILY BALANCE SUMMERY REPORT
ACCESS TYPE : DIRECT
*/

if($hasData>0){
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = $op_name_arr[1][0];
	$op_name .= $op_name_arr[0][0];
	$op_name = strtoupper($op_name);
	$curDate = date(phpDateFormat().' h:i A');
	//---------BEGIN CALCULATE COLUMN WIDTHS----------------
	$total_cols = 6;
	$phy_col = $phy_col1 ="17";
	$fac_col = "25";
	$w_cols = $w_cols1 = floor((100 - ($phy_col + $fac_col))/($total_cols-2));
	$fac_col = $fac_col1 = 100 - ( (($total_cols-2) * $w_cols) + $phy_col);
	
	$w_cols = $w_cols."%";
	$phy_col = $phy_col."%";
	$fac_col = $fac_col."%";
	//---------END CALCULATE COLUMN WIDTHS----------------
	
	//---------BEGIN PAGE HEADERS-------------------------
	$pdf_header = <<<DATA
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>	
				<td width="25%" align="left" class="rptbx1" nowrap style="width:25%"> $dbtemp_name (Summary)</td>	
				<td width="25%" align="left" class="rptbx2" style="width:25%">Selected Group : $sel_grp</td>			
				<td width="25%" align="left" class="rptbx3" style="width:25%">DOT (From : $Start_date To : $End_date)</td>
				<td width="25%" align="left" class="rptbx1" style="width:25%">Created By: $report_generator_name on $curDate</td>
			</tr>
			<tr>	
				<td align="left" class="rptbx1" nowrap>Selected Facility : $sel_fac</td>	
				<td align="left" class="rptbx2">Selected Physician : $sel_phy</td>			
				<td align="left" class="rptbx3" >Selected Operator : $sel_opr</td>
				<td align="left" class="rptbx1" ></td>
			</tr>
		</table>
DATA;
	//---------END PAGE HEADERS-------------------------
	
	
//---------BEGIN DISPLAY CI/CO AND POSTED PAYMENTS BLOCK-------------------------	
if(sizeof($arrPayData)>0){
	$arrCIOTotal = array();
	$printFile = true;
	$page_data.= <<<DATA
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr id="heading_orange">	
				<td align="left"  class="text_b_w" colspan="$total_cols">CI/CO and Posted Payments</td>	
			</tr>
DATA;
	$page_data.= <<<DATA
				<tr id="">
					<td class="text_b_w" style="text-align:center; width:$phy_col;">Physician</td>
					<td class="text_b_w" style="text-align:center; width:$fac_col;">Facility</td>
					<td class="text_b_w" style="text-align:center; " colspan=3>CI/CO Payments</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Posted Payments</td>
				</tr>	
				<tr id="">
					<td class="text_b_w" style="text-align:center; width:$phy_col;"></td>
					<td class="text_b_w" style="text-align:center; width:$fac_col;"></td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Payments</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Applied</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Unapplied</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;"></td>
				</tr>			
DATA;

	foreach($arrPayData as $phyId=>$arrFacData){
		$phy_name = ($phyId == 0)?"Not Specified":$arrAllUsers[$phyId];
		foreach($arrFacData as $facId=>$arrData){
		
		if($arrData['payment_ref']>0)
		{
			$redRowCC=';color:#FF0000" title="Refund '.numberformat($arrData['payment_ref'],2);
		}else $redRowCC='';
		
			$fac_name = ($facId == 0)?"Not Specified":$allFacArr[$facId];
			$unapplied = $arrData['payment'] - $arrData['applied'];
			$page_data.= '
				<tr class="data">
					<td style="text-align:left; width:'.$phy_col.';">'.$phy_name.'</td>
					<td style="text-align:left; width:'.$fac_col.';">'.$fac_name.'</td>
					<td style="text-align:right; width:'.$w_cols.$redRowCC.'" >'.numberformat($arrData['payment'],2).'</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.numberformat($arrData['applied'],2).'</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.numberformat($unapplied,2).'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.numberformat($arrData['posted'],2).'</td>
				</tr>			
			';
			$arrCIOTotal['payment'][] = $arrData['payment'];
			$arrCIOTotal['applied'][] = $arrData['applied'];
			$arrCIOTotal['unapplied'][] = $unapplied;
			$arrCIOTotal['posted'][] = $arrData['posted'];
		}
	}
	
	$unapplied= array_sum($arrCIOTotal['unapplied']);
	$totCICOUnapplied= $unapplied;
	
	$tot_payment_amt = numberformat(array_sum($arrCIOTotal['payment']),2);
	$tot_applied_amt = numberformat(array_sum($arrCIOTotal['applied']),2);
	$tot_unapplied_amt = numberformat($unapplied,2);
	$tot_posted_amt = numberformat(array_sum($arrCIOTotal['posted']),2);
	
	$page_data.= '
				<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
				<tr class="subtotal">
					<td style="text-align:right; width:'.($phy_col + $fac_col).';" colspan="2">Total</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.$tot_payment_amt.'</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.$tot_applied_amt.'</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.$tot_unapplied_amt.'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.$tot_posted_amt.'</td>
				</tr>	
				<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>		
			';
$page_data .= <<<DATA
	</table>
DATA;
		
}
//---------END DISPLAY CI/CO AND POSTED PAYMENTS BLOCK-------------------------

//---------BEGIN DISPLAY PRE PAYMENTS BLOCK-------------------------	

	//---------BEGIN CALCULATE COLUMN WIDTHS----------------
	$total_cols = 4;
	$opr_col = "17";
	$w_cols = floor((100 - ($opr_col))/($total_cols-1));
	$opr_col =  100 - ( (($total_cols-1) * $w_cols));
	
	$w_cols = $w_cols."%";
	$opr_col = $opr_col."%";
	//---------END CALCULATE COLUMN WIDTHS----------------
if(sizeof($arrPrePayData)>0){
	$arrPreTotal = array();
	$printFile = true;
	$page_data.= <<<DATA
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr id="heading_orange">	
				<td align="left" class="text_b_w" colspan="$total_cols">Pre Payments</td>	
			</tr>
DATA;
	$page_data.= <<<DATA
				<tr id="">
					<td class="text_b_w" style="text-align:center; width:$opr_col;">Operator</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Payments</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Applied</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Unapplied</td>
				</tr>			
DATA;
	foreach($arrPrePayData as $oprId=>$arrData){
		$opr_name = ($oprId == 0)?"Not Specified":$arrAllUsers[$oprId];
		$applied = $arrData['applied'];
		foreach($arrData['pre_pay_id'] as $pre_pay_id){
			//$applied += $arrPrePayAppliedId[$pre_pay_id];
			foreach($arrPrePayAppliedId[$pre_pay_id] as $pre_app_pay){
			$applied += $pre_app_pay;
			}
		}
		$unapplied = $arrData['payment'] - $applied;
		
		
		
		if($arrData['payment_ref']>0)
		{
			$redRowPre=';color:#FF0000" title="Refund '.numberformat($arrData['payment_ref'],2);
		}else $redRowPre='';
		
			$page_data.= '
				<tr class="data">
					<td style="text-align:left; width:'.$opr_col.';">'.$opr_name.'</td>
					<td style="text-align:right; width:'.$w_cols.$redRowPre.'" >'.numberformat($arrData['payment'],2).'</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.numberformat($applied,2).'</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.numberformat($unapplied,2).'</td>
				</tr>			
			';
			$arrPreTotal['payment'][] = $arrData['payment'];
			$arrPreTotal['applied'][] = $applied;
			$arrPreTotal['unapplied'][] = $unapplied;
	}
	
	$unapplied=array_sum($arrPreTotal['unapplied']);
	$totPrePayUnapplied= $unapplied;
	
	$tot_payment_amt = numberformat(array_sum($arrPreTotal['payment']),2);
	$tot_applied_amt = numberformat($unapplied,2);
	$tot_unapplied_amt = numberformat(array_sum($arrPreTotal['unapplied']),2);
	
	$page_data.= '
				<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
				<tr class="subtotal">
					<td style="text-align:right; width:'.($opr_col).';">Total</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.$tot_payment_amt.'</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.$tot_applied_amt.'</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.$tot_unapplied_amt.'</td>
				</tr>
				<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>			
			';
$page_data .= <<<DATA
	</table>
DATA;
		
}
//---------END DISPLAY PRE PAYMENTSPAYMENTS -------------------------
$page_data.='<br>';
}
if($hasData == 1){
	//pre($arrAppAmtInDate);
	//---------BEGIN CALCULATE COLUMN WIDTHS----------------
	$total_cols = 5;
	$first_col = "17";
	$w_cols = $w_cols1 = floor((100 - ($first_col))/($total_cols-1));
	$first_col = 100 - ( (($total_cols-1) * $w_cols));
	
	$w_cols = $w_cols."%";
	$first_col = $first_col."%";
	//---------END CALCULATE COLUMN WIDTHS----------------
	$tot_cio_payment = numberformat(array_sum($arrCIOTotal['payment']),2);
	$tot_cio_date_applied = numberformat($arrAppAmtInDate['cio_payment'],2);
	$tot_cio_collec_applied = numberformat(array_sum($arrCIOTotal['applied']),2);
	$tot_cio_collec_unapplied = numberformat(array_sum($arrCIOTotal['unapplied']),2);
	
	
	$tot_pre_payment = numberformat(array_sum($arrPreTotal['payment']),2);
	$tot_pre_date_applied = numberformat($arrAppAmtInDate['pre_payment'],2);
	$tot_pre_collec_applied = numberformat(array_sum($arrPreTotal['applied']),2);
	$tot_pre_collec_unapplied = numberformat(array_sum($arrPreTotal['unapplied']),2);
	
	$tot_payment = numberformat(array_sum($arrCIOTotal['payment']) + array_sum($arrPreTotal['payment']),2);
	$tot_date_applied = numberformat($arrAppAmtInDate['cio_payment'] + $arrAppAmtInDate['pre_payment'],2);
	$tot_collec_applied = numberformat(array_sum($arrCIOTotal['applied']) + array_sum($arrPreTotal['applied']),2);
	$tot_collec_unapplied = numberformat(array_sum($arrCIOTotal['unapplied']) + array_sum($arrPreTotal['unapplied']),2);
	
	$tot_post_payment = numberformat(array_sum($arrCIOTotal['posted']),2);
	
	$grd_payment = numberformat(array_sum($arrCIOTotal['payment']) + array_sum($arrPreTotal['payment']) + array_sum($arrCIOTotal['posted']),2);
	$grd_date_applied = numberformat($arrAppAmtInDate['cio_payment'] + $arrAppAmtInDate['pre_payment'] + array_sum($arrCIOTotal['posted']),2);
	$grd_collec_applied = numberformat(array_sum($arrCIOTotal['applied']) + array_sum($arrPreTotal['applied']) + array_sum($arrCIOTotal['posted']),2);
	$grd_collec_unapplied = numberformat(array_sum($arrCIOTotal['unapplied']) + array_sum($arrPreTotal['unapplied']),2);
	
	//PAYMENT BREAKDOWN
	$cash=numberformat($arrPayBreakdown['CASH'],2);
	$check=numberformat($arrPayBreakdown['CHECK'],2);
	$cc=numberformat($arrPayBreakdown['CREDIT CARD'],2);
	$eft=numberformat($arrPayBreakdown['EFT'],2);
	$mo=numberformat($arrPayBreakdown['MONEY ORDER'],2);
	$veep=numberformat($arrPayBreakdown['VEEP'],2);
	$totalPaidBreakdown=$arrPayBreakdown['CASH']+$arrPayBreakdown['CHECK']+$arrPayBreakdown['CREDIT CARD']+$arrPayBreakdown['EFT']+$arrPayBreakdown['MONEY ORDER']+$arrPayBreakdown['VEEP'];
	$totalPaidBreakdown=numberformat($totalPaidBreakdown,2);
	
	$page_data.= <<<DATA
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr id="heading_orange">	
				<td align="left"  class="text_b_w"  colspan="$total_cols">Grand Totals</td>	
			</tr>
			<tr id="">	
				<td class="text_10b text_b_w" style="width:$first_col"></td>	
				<td class="text_10b text_b_w" style="width:$w_cols; text-align:center">Total Collected</td>	
				<td class="text_10b text_b_w" style="width:$w_cols; text-align:center">Applied for Date Range</td>
				<td class="text_10b text_b_w" style="width:$w_cols; text-align:center">Applied from Collected</td>	
				<td class="text_10b text_b_w" style="width:$w_cols; text-align:center">Unapplied from Collected</td>	
			</tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">CI/CO Payments</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$tot_cio_payment</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$tot_cio_date_applied</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$tot_cio_collec_applied</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$tot_cio_collec_unapplied</td>					
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Pre Payments</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$tot_pre_payment</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$tot_pre_date_applied</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$tot_pre_collec_applied</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$tot_pre_collec_unapplied</td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Total</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$tot_payment</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$tot_date_applied</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$tot_collec_applied</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$tot_collec_unapplied</td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Total Posted Payments</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$tot_post_payment</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$tot_post_payment</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$tot_post_payment</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right"></td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Grand Totals</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_payment</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_date_applied</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_collec_applied</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_collec_unapplied</td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr id="heading_orange">	
				<td align="left"  class="text_b_w"  colspan="$total_cols">Payment Breakdown</td>	
			</tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Cash</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$cash</td>	
				<td class="text_10" style="width:$w_cols; text-align:right" colspan="3"></td>	
			</tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Check</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$check</td>	
				<td class="text_10" style="width:$w_cols; text-align:right" colspan="3"></td>	
			</tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Credit Card</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$cc</td>	
				<td class="text_10" style="width:$w_cols; text-align:right" colspan="3"></td>	
			</tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">EFT</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$eft</td>	
				<td class="text_10" style="width:$w_cols; text-align:right" colspan="3"></td>	
			</tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Money Order</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$mo</td>	
				<td class="text_10" style="width:$w_cols; text-align:right" colspan="3"></td>	
			</tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">VEEP</td>	
				<td class="text_10" style="width:$w_cols; text-align:right">$veep</td>	
				<td class="text_10" style="width:$w_cols; text-align:right" colspan="3"></td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Grand Total</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$totalPaidBreakdown</td>	
				<td class="text_10" style="width:$w_cols; text-align:right" colspan="3"></td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			</table>
DATA;


	//MANUALLY APPLIED RECORDS
	if(sizeof($arrManuallyApplied)>0){
		$dataExists=true;
		$delDataExists=true;
		$content_part='';
		$arrTotals=array();
		
		foreach($arrManuallyApplied as $grpId => $grpData){
			$firstGroupName = $arrAllUsers[$grpId];
			$total= $grpData['cico']+$grpData['pre_payment']; 
			$arrTotals['cico']+= $grpData['cico'];
			$arrTotals['pre_payment']+= $grpData['pre_payment'];
			$arrTotals['total']+= $total;
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.numberformat($grpData['cico'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.numberformat($grpData['pre_payment'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.numberformat($total,2).'&nbsp;</td>
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
		$first_col = "20";
		$last_col="40";
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
		<tr id="heading_orange"><td class="text_b_w" colspan="'.$colspan.'">Manually Applied Amounts</td></tr>
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
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberformat($arrTotals['cico'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberformat($arrTotals['pre_payment'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberformat($arrTotals['total'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		</table>';		
	}
}

$complete_page_data = 
$pdf_header.
$page_data.
$manually_applied_html;
?>