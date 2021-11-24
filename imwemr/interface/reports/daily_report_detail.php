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
if($hasData>0){
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = $op_name_arr[1][0];
	$op_name .= $op_name_arr[0][0];
	$op_name = strtoupper($op_name);
	$curDate = date('H:i A');
	//---------BEGIN CALCULATE COLUMN WIDTHS----------------
	
	$total_cols = 7;
	$clspn = "colspan='3'";
	if($inc_appt == 1){
		$total_cols = 8;	
		$appt_detailsTD = '<td class="text_b_w" style="text-align:center; width:'.$date_col.';">Appt Date/Time</td>';
		$appt_detailsRes = '<td style="text-align:left; width:'.$date_col.'; word-wrap:break-word;">'.$arrPtApptDetail['date'].' '.$arrPtApptDetail['time'].'</td>';
		$appt_detailsTD1 = '<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" ></td>';
		$clspn = "colspan='4'";
	}
	
	$pat_col = "15";
	$date_col = $date_col1 = "10";
	$w_cols = $w_cols1 = floor((100 - ($pat_col+$date_col))/($total_cols-2));
	$pat_col = $pat_col1 = 100 - ((($total_cols-2) * $w_cols) + $date_col);
	
	$w_cols = $w_cols."%";
	$pat_col = $pat_col."%";
	$date_col = $date_col."%";
	
	
	//---------END CALCULATE COLUMN WIDTHS----------------
	
	//---------BEGIN PAGE HEADERS-------------------------
	$pdf_header = <<<DATA
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>	
				<td width="25%" align="left" class=" rptbx1" nowrap style="width:25%">$dbtemp_name (Detail)</td>	
				<td width="25%" align="left" class=" rptbx2" style="width:25%">Selected Group : $sel_grp</td>			
				<td width="25%" align="left" class=" rptbx3" style="width:25%">DOT (From : $Start_date To : $End_date)</td>
				<td width="25%" align="left" class=" rptbx1" style="width:25%">Created By: $report_generator_name on $curDate</td>
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
if(sizeof($arrPatPayDetail)>0){
	//pre($arrPatPayDetail['66670']);
	$arrTotal = array();
	$arrCIOTotal = array();
	$arrPreTotal = array();
	$printFile = true;
	$page_data.= <<<DATA
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr id="heading_orange">	
				<td align="left"  class="text_b_w"  colspan="$total_cols">CI/CO , Pre Payments and Posted Payments</td>	
			</tr>
DATA;
	$page_data.= '
				<tr id="">
					<td class="text_b_w" style="text-align:center; width:'.$pat_col.';">Patient Name - ID</td>
					<td class="text_b_w" style="text-align:center; width:'.$date_col.';">DOT</td>
					'.$appt_detailsTD.'
					<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Desc</td>
					<td class="text_b_w" style="text-align:center; width:'.($w_cols1*3).'%;" colspan="3" >CI/CO</td>
					<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Posted Pymt</td>
				</tr>	
				<tr id="">
					<td class="text_b_w" style="text-align:center; width:'.$pat_col.';"></td>
					<td class="text_b_w" style="text-align:center; width:'.$date_col.';"></td>
					<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" ></td>
					<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Payment</td>
					<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Applied</td>
					<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Unappl</td>
					<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" ></td>
					'.$appt_detailsTD1.'
				</tr>			
			';
	foreach($arrPatPayDetail as $patId=>$arrDOT){
		if(empty($patId) == false){
			$firstTitle='Physician';
			if($providerNameArr[$patId]){
				$firstGrpByName = $providerNameArr[$patId];
			}
			
			if($_REQUEST['registered_fac']==1){
				$facname = $arrAllPosFacilities[$patId];
			}else{
				$facname = $arrAllFacilities[$patId];	
			}
			if($grpby_block=='grpby_facility'){
				$firstTitle='Facility';
				$firstGrpByName = $facname;
				if(empty($firstGrpByName) == true){
					$firstGrpByName = 'No Facility';
				}
			}
			if($grpby_block=='grpby_operators'){
				$firstTitle='Operator';
				$firstGrpByName= $providerNameArr[$patId];
			}
			if($grpby_block=='grpby_groups'){
				$firstTitle='Group';
				$firstGrpByName= $groupNameArr[$patId];
				if(empty($firstGrpByName) == true){
					$firstGrpByName = 'No Group';
				}
			}
			$page_data.='<tr>
				<td colspan='.$total_cols.' class="text_b_w" style="text-align:left;word-wrap:break-word;">'.$firstTitle.' : '.$firstGrpByName.'</td>
				</tr>';
			
			foreach($arrDOT as $DOT=>$arrOpr){
				foreach($arrOpr as $oprId=>$arrData){
					preg_match_all('/(?<=\,\s|^)[a-z0-9]/i', $arrAllUsers[$oprId], $matches);
					
					$pre_applied = $arrData['pre_applied'];
					foreach($arrData['pre_pay_id'] as $pre_pay_id){
						foreach($arrPrePayAppliedId[$pre_pay_id] as $pre_app_pay){
						$pre_applied += $pre_app_pay;
						}
					}
					
					$cio_unapplied= $arrData['cio_payment'] - $arrData['cio_applied'];
					$pre_unapplied= $arrData['pre_payment'] - $pre_applied;
					
					$arr_pt_id = $arrData['pt_id'];
					if($arrData['cio_payment_ref']>0)
					{
						$redRow=';color:#FF0000" title="Refund '.numberformat($arrData['cio_payment_ref'],2);
					}else $redRow='';
					
					if($arrData['pre_payment_ref']>0)
					{
						$redRowPre=';color:#FF0000" title="Refund '.numberformat($arrData['pre_payment_ref'],2);
					}else $redRowPre='';
					$page_data.= '
						<tr class="data">
							<td style="text-align:left; width:'.$pat_col.'; word-wrap:break-word;">'.$arrPatient[$arr_pt_id].'</td>
							<td style="text-align:left; width:'.$date_col.'; word-wrap:break-word;">'.$DOT.'</td>
							'.$appt_detailsRes.'
							<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;" >'.substr($cioFields[$arrData['cio_item_id']],0,11).'</td>
							<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word'.$redRow.'" >'.numberformat($arrData['cio_payment'],2).'</td>
							<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;" >'.numberformat($arrData['cio_applied'],2).'</td>
							<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat(($cio_unapplied),2).'</td>
							<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($arrData['posted'],2).'</td>
						</tr>			
					';
					$arrTotal['cio_payment'][] = $arrData['cio_payment'];
					$arrTotal['cio_applied'][] = $arrData['cio_applied'];
					$arrTotal['pre_payment'][] = $arrData['pre_payment'];
					$arrTotal['pre_applied'][] = $pre_applied;
					$arrTotal['posted'][] = $arrData['posted'];
					
					$arrCIOTotal['payment'][] = $arrData['cio_payment'];
					$arrCIOTotal['applied'][] = $arrData['cio_applied'];
					$arrCIOTotal['unapplied'][] = $cio_unapplied;
					$arrCIOTotal['posted'][] = $arrData['posted'];
					
				}
			}
		}
	}
	$tot_payment_amt = numberformat(array_sum($arrCIOTotal['payment']),2);
	$tot_applied_amt = numberformat(array_sum($arrCIOTotal['applied']),2);
	$tot_unapplied_amt = numberformat(array_sum($arrCIOTotal['unapplied']),2);
	$tot_posted_amt = numberformat(array_sum($arrCIOTotal['posted']),2);
	
	
				
	$page_data.= '
				<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
				<tr class="subtotal">
					<td style="text-align:right; width:'.($pat_col1 + $w_cols1 +$date_col1).'%;" '.$clspn.'>Total</td>
					<td style="text-align:right; width:'.$w_cols.';" >'.numberformat(array_sum($arrTotal['cio_payment']),2).'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['cio_applied']),2).'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['cio_payment']) - array_sum($arrTotal['cio_applied']),2).'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['posted']),2).'</td>
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
if(sizeof($arrPrePayDetail)>0){
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
					<td class="text_b_w" style="text-align:center; width:$opr_col;">Patient Name - ID</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Payments</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Applied</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;" >Unapplied</td>
				</tr>			
DATA;
	foreach($arrPrePayDetail as $oprId=>$arrData){
		
		
		
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
					<td style="text-align:left; width:'.$opr_col.';">'.$arrPatient[$oprId].'</td>
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
	$grd_collec_applied = numberformat(array_sum($arrCIOTotal['unapplied']) + array_sum($arrPreTotal['unapplied']) + array_sum($arrCIOTotal['posted']), 2);
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
				<td class="text_10b text_b_w"" style="width:$first_col"></td>	
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
				<td class="text_b_w" style="width:$first_col; text-align:right">Grand Totals</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_payment</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_date_applied</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_collec_applied</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_collec_unapplied</td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr id="heading_orange">	
				<td align="left" class="text_b_w" colspan="$total_cols">Payment Breakdown</td>	
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


	//CI/CO MANUALLY APPLIED
	$content_part= $applied_html='';
	$totalCICOManuallyApplied=0;
	$arrAppliedPatPayTot=array();
	if(sizeof($arrCICOManuallyPaid)>0){
		$colspan= 4;
	
		$total_cols = 2;
		$first_col = "22";
		$last_col = "53";
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
			$firstGroupName = $arrAllUsers[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Operator - '.$firstGroupName.'</td></tr>';
		
			foreach($grpData as $pid => $patData){
				foreach($patData as $payment_id => $paymentDetail){

					$patient_name=  $arrPatient[$pid];
					$firstGrpTotal['applied_amt']+=	$paymentDetail['applied_amt'];
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$paymentDetail['paid_date'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.numberformat($paymentDetail['applied_amt'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
					</tr>';
				}
			}
		
			$arrAppliedPatPayTot['applied_amt']+=	$firstGrpTotal['applied_amt'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Operator Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberformat($firstGrpTotal['applied_amt'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
		}
	
		$totalCICOManuallyApplied=$arrAppliedPatPayTot['applied_amt'];
		
		// TOTAL
		// HEADER
		$header='
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr id="heading_orange"><td class="text_b_w" colspan="'.$colspan.'">CI/CO Manually Applied Amounts</td></tr>
		<tr id="">
			<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOT</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Applied Amount</td>
			<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
		</tr>
		</table>';
	
		
		//HTML
		$manually_applied_cico_html .=
		$header.' 
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		'.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">CI/CO Manually Applied Amounts&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberformat($arrAppliedPatPayTot['applied_amt'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
		</table>';
	}

	//PRE-PAYMENT MANUALLY APPLIED
	$content_part= $applied_html='';
	$arrAppliedPatPayTot=array();
	if(sizeof($arrPrePayManuallyApplied)>0){
		$colspan= 4;
	
		$total_cols = 2;
		$first_col = "22";
		$last_col = "53";
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
			$firstGroupName = $arrAllUsers[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Operator - '.$firstGroupName.'</td></tr>';
		
			foreach($grpData as $eid => $grpDetail){

				$patient_name=  $arrPatient[$pid];
				$firstGrpTotal['applied_amt']+=	$grpDetail['applied_amt'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.numberformat($grpDetail['applied_amt'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrAppliedPatPayTot['applied_amt']+=	$firstGrpTotal['applied_amt'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberformat($firstGrpTotal['applied_amt'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
		}
	
		
		// TOTAL
		// HEADER
		$header='
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">		
		<tr id="heading_orange"><td class="text_b_w" colspan="'.$colspan.'">Pre-Payments Manually Applied Amounts</td></tr>
		<tr id="">
			<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOT</td>
			<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Applied Amount</td>
			<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
		</tr>
		</table>';
	
	
		//HTML
		$manually_applied_pre_pay_html .=
		$header.' 
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		'.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Pre-Payments Manually Applied Amounts&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberformat($arrAppliedPatPayTot['applied_amt'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
	
		//IF MANUALLY APPLIED CI/CO ALSO EXIST THEN MAKE TOTAL OF BOTH
		if($totalCICOManuallyApplied>0){
			$tot= $totalCICOManuallyApplied + $arrAppliedPatPayTot['applied_amt'];
			$manually_applied_pre_pay_html.='
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Total Manually Applied Amounts&nbsp;:</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberformat($tot,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>';
		}
		$manually_applied_pre_pay_html.='</table>';
	}
}
$complete_page_data = 
$pdf_header.
$page_data.
$manually_applied_cico_html.
$manually_applied_pre_pay_html;
?>