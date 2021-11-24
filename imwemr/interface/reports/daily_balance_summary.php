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
$selDateC = strtoupper($DateRangeFor);
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
				<td width="258" align="left" class="rptbx1">Daily Balance Report (Summary)</td>	
				<td width="258" align="left" class="rptbx2" >Selected Group : $sel_grp</td>			
				<td width="258" align="left" class="rptbx3">$selDateC (From : $Start_date To : $End_date)</td>
				<td width="258" align="left" class="rptbx1">Created By: $report_generator_name on $curDate</td>
			</tr>
			<tr>	
				<td align="left" class="rptbx1" nowrap>Selected Facility : $sel_fac</td>	
				<td align="left" class="rptbx2">Selected Physician : $sel_phy</td>			
				<td align="left" class="rptbx3" >Selected Operator : $sel_opr</td>
				<td align="left" class="rptbx1" >Sel Method : $sel_method</td>
			</tr>
		</table>
DATA;
	//---------END PAGE HEADERS-------------------------

//MAKING OUTPUT DATA
$file_name="daily_balance.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr=array();
$arr[]='Daily Balance Report (Summary)';
$arr[]='Selected Group : '.$sel_grp;
$arr[]= $selDateC.' (From : '.$Start_date.' To : '.$End_date.')';
$arr[]='Created by :'.$report_generator_name.' on '.$curDate;
fputcsv($fp,$arr, ",","\"");
$arr=array();
$arr[]='Selected Facility : '.$sel_fac;
$arr[]='Selected Physician : '.$sel_phy;
$arr[]='Selected Operator : '.$sel_opr;
$arr[]='Selected Method : '.$sel_method;
fputcsv($fp,$arr, ",","\"");	
	
//---------BEGIN DISPLAY CI/CO AND POSTED PAYMENTS BLOCK-------------------------	
if(sizeof($arrPayData)>0){
	$arrCIOTotal = array();
	$printFile = true;
	$page_data.= <<<DATA
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr>	
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

$arr=array();
$arr[]='CI/CO and Posted Payments';
fputcsv($fp,$arr, ",","\"");	
$arr=array();
$arr[]='Physician';
$arr[]='Facility';
$arr[]='CI/CO: Payments';
$arr[]='CI/CO: Applied';
$arr[]='CI/CO: Unapplied';
$arr[]='Posted Payments';
fputcsv($fp,$arr, ",","\"");	
	foreach($arrPayData as $phyId=>$arrFacData){
		$phy_name = ($phyId == 0)?"Not Specified":$arrAllUsers[$phyId];
		foreach($arrFacData as $facId=>$arrData){
		
		if($arrData['payment_ref']>0)
		{
			$redRowCC=';color:#FF0000" title="Refund '.numberformat($arrData['payment_ref'],2);
		}else $redRowCC='';
		
			$fac_name = ($facId == 0)?"Not Specified":$arr_sch_facilities[$facId];
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
			
			$arr=array();
			$arr[]=$phy_name;
			$arr[]=$fac_name;
			$arr[]=numberformat($arrData['payment'],2);
			$arr[]=numberformat($arrData['applied'],2);
			$arr[]=numberformat($unapplied,2);
			$arr[]=numberformat($arrData['posted'],2);
			fputcsv($fp,$arr, ",","\"");
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
				<td align="left" colspan="$total_cols">Pre Payments</td>	
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
$arr=array();
$arr[]='Pre Payments';
fputcsv($fp,$arr, ",","\"");	
$arr=array();
$arr[]='Operator';
$arr[]='Payments';
$arr[]='Applied';
$arr[]='Unapplied';
fputcsv($fp,$arr, ",","\"");
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
			$arr=array();
			$arr[]=$opr_name;
			$arr[]=numberformat($arrData['payment'],2);
			$arr[]=numberformat($applied,2);
			$arr[]=numberformat($unapplied,2);
			fputcsv($fp,$arr, ",","\"");
	}
	
	$unapplied=array_sum($arrPreTotal['unapplied']);
	$totPrePayUnapplied= $unapplied;
	
	$tot_payment_amt = numberformat(array_sum($arrPreTotal['payment']),2);
	$tot_applied_amt = numberformat(array_sum($arrPreTotal['applied']),2);
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


//REFUND AMOUNTS
$tot_ref_amounts=0;
if(sizeof($arrRefundAmounts)>0){
	$csv_data_part='';
	$dataExists=1;
	$arrRefTots=array();
	$arr=array();
	$arr[]='Refund Amounts';
	fputcsv($fp,$arr, ",","\"");	
	$arr=array();
	$arr[]='Operator';
	$arr[]='Posted Refunds';
	$arr[]='CI/CO Refunds';
	$arr[]='Pre-Payment Refunds';
	$arr[]='Total';
	fputcsv($fp,$arr, ",","\"");
	foreach($arrRefundAmounts as $opr_id =>$detailsArr){
		$posted_amt=$cico_amt=$pre_amt=0;
		
		$posted_amt=($detailsArr['posted']>0)? $detailsArr['posted']: 0;
		$cico_amt=($detailsArr['cico']>0)? $detailsArr['cico']: 0;
		$pre_amt=($detailsArr['pre_paid']>0)? $detailsArr['pre_paid']: 0;
		
		$total=$posted_amt+$cico_amt+$pre_amt;
		
		$arrRefTots['posted']+=$posted_amt;
		$arrRefTots['cico']+=$cico_amt;
		$arrRefTots['pre_paid']+=$pre_amt;
		$arrRefTots['total']+=$total;
		
		$csv_data_part .='<tr>
			<td class="text_10" bgcolor="#FFFFFF" width="162 style="text-align:left">'.$arrAllUsers[$opr_id].'</td>
			<td class="text_10" bgcolor="#FFFFFF" width="85" style="text-align:right">'.$CLSReports->numberFormat($posted_amt,2).'</td>
			<td class="text_10" bgcolor="#FFFFFF" width="70" style="text-align:right">'.$CLSReports->numberFormat($cico_amt,2).'</td>
			<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right">'.$CLSReports->numberFormat($pre_amt,2).'</td>
			<td class="text_10" bgcolor="#FFFFFF" width="80" style="text-align:right">'.$CLSReports->numberFormat($total,2).'</td>
		</tr>';
		$arr=array();
		$arr[]=$arrAllUsers[$opr_id];
		$arr[]=$CLSReports->numberFormat($posted_amt,2);
		$arr[]=$CLSReports->numberFormat($cico_amt,2);
		$arr[]=$CLSReports->numberFormat($pre_amt,2);
		$arr[]=$CLSReports->numberFormat($total,2);
		fputcsv($fp,$arr, ",","\"");
	}

	$tot_ref_amounts=$arrRefTots['total'];
	
	$page_data.='
	<table style="width:100%" class="rpt_table rpt_table-bordered">
	<tr id="heading_orange"><td class="text_b_w" colspan="8">Refund Amounts</td></tr>
	<tr>
		<td class="text_b_w" width="25%" style="text-align:left">Operator</td>
		<td class="text_b_w" width="18%" style="text-align:center">Posted Refunds</td>
		<td class="text_b_w" width="18%" style="text-align:center">CI/CO Refunds</td>
		<td class="text_b_w" width="18%" style="text-align:center">Pre-Payment Refunds</td>
		<td class="text_b_w" width="18%" style="text-align:center">Total</td>
	</tr>
	'.$csv_data_part.'
	<tr><td colspan="5" class="total-row"></td></tr>
	<tr>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right">Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrRefTots['posted'],2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrRefTots['cico'],2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrRefTots['pre_paid'],2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrRefTots['total'],2).'</td>
	</tr>
	<tr><td colspan="5" class="total-row"></td></tr>
	</table>';	
}

//DELETED RECORDS
$delDataExists=false;
$delPostedTotal=0;
if(sizeof($arrDelAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$content_part='';
	$arrTotals=array();
	$arr=array();
	$arr[]='Deleted Payments After '.$End_date;
	fputcsv($fp,$arr, ",","\"");	
	$arr=array();
	$arr[]='Physician/Operator';
	if($showFacCol){
		$arr[]='Facility';
	}
	$arr[]='Posted Payments';
	$arr[]='CI/CO Payments';
	$arr[]='Pre-Payment';
	$arr[]='Total';
	fputcsv($fp,$arr, ",","\"");
	foreach($arrDelAmounts as $grpId => $grpData){
			$firstGroupName = $arrAllUsers[$grpId];
			$total= $grpData['posted']+$grpData['cico']+$grpData['pre_payment']; 
			$arrTotals['posted']+= $grpData['posted'];
			$arrTotals['cico']+= $grpData['cico'];
			$arrTotals['pre_payment']+= $grpData['pre_payment'];
			$arrTotals['total']+= $total;
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['posted'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['cico'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grpData['pre_payment'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($total,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>';	
			$arr=array();
			$arr[]=$firstGroupName;
			$arr[]=$CLSReports->numberFormat($grpData['posted'],2);
			$arr[]=$CLSReports->numberFormat($grpData['cico'],2);
			$arr[]=$CLSReports->numberFormat($grpData['pre_payment'],2);
			$arr[]=$CLSReports->numberFormat($total,2);
			fputcsv($fp,$arr, ",","\"");
		}
	
	// TOTAL
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

	$page_data.=' 
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">	
	<tr id="heading_orange"><td colspan="'.$colspan.'">Deleted Payments After '.$End_date.'</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">Physician/Operator &nbsp;</td>
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
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['posted'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['cico'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['pre_payment'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['total'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	</table>';	
	
	$arr=array();
	$arr[]='Deleted Total:';
	$arr[]=$CLSReports->numberFormat($arrTotals['posted'],2);
	$arr[]=$CLSReports->numberFormat($arrTotals['cico'],2);
	$arr[]=$CLSReports->numberFormat($arrTotals['pre_payment'],2);
	$arr[]=$CLSReports->numberFormat($arrTotals['total'],2);
	fputcsv($fp,$arr, ",","\"");
	
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
	
	$grd_payment = numberformat((array_sum($arrCIOTotal['payment']) + array_sum($arrPreTotal['payment']) + array_sum($arrCIOTotal['posted']))-$tot_ref_amounts,2);
	$grd_date_applied = numberformat(($arrAppAmtInDate['cio_payment'] + $arrAppAmtInDate['pre_payment'] + array_sum($arrCIOTotal['posted']))-$delPostedTotal, 2);
	$grd_collec_applied = numberformat(array_sum($arrCIOTotal['applied']) + array_sum($arrPreTotal['applied']) + array_sum($arrCIOTotal['posted']),2);
	$grd_collec_unapplied = numberformat(array_sum($arrCIOTotal['unapplied']) + array_sum($arrPreTotal['unapplied']),2);
	
	$tot_ref_amounts=numberformat($tot_ref_amounts,2);
	
	//PAYMENT BREAKDOWN
	$tempArrPay = array();
	foreach($arrPayBreakdown as $payMeth => $payVal){
		$tempArrPay[$payMeth] = $payVal;
	}
	$payVals = array_sum(array_values($tempArrPay));
	$payment_method_data = "";
	foreach($tempArrPay as $payMothod => $paymentval){
		$payMothod = ucwords(strtolower($payMothod));
		$paymentval = numberformat($paymentval,2);
		$payment_method_data .="<tr class=\"data\">	
			<td class=\"text_10b\" style=\"width:$first_col; text-align:right\">$payMothod</td>	
			<td class=\"text_10\" style=\"width:$w_cols; text-align:right\">$paymentval</td>	
			<td class=\"text_10\" style=\"width:$w_cols; text-align:right\" colspan=\"3\"></td>	
		</tr>";
	}
	
	/* $cash=numberformat($arrPayBreakdown['CASH'],2);
	$check=numberformat($arrPayBreakdown['CHECK'],2);
	$cc=numberformat($arrPayBreakdown['CREDIT CARD'],2);
	$eft=numberformat($arrPayBreakdown['EFT'],2);
	$mo=numberformat($arrPayBreakdown['MONEY ORDER'],2);
	$veep=numberformat($arrPayBreakdown['VEEP'],2);
	$totalPaidBreakdown=$arrPayBreakdown['CASH']+$arrPayBreakdown['CHECK']+$arrPayBreakdown['CREDIT CARD']+$arrPayBreakdown['EFT']+$arrPayBreakdown['MONEY ORDER']+$arrPayBreakdown['VEEP']; */
	$totalPaidBreakdown=numberformat($payVals,2);
	
	// CC Payment BreakDown
	$ccTypeRows = '';
	if( is_array($arrCCPayBreakdown) && count($arrCCPayBreakdown) > 0 ) {
		$tmpCCTotal = $tmpCCPayment = $counter = 0;
		$ccTypeRows .= '<tr id="heading_orange"><td align="left" colspan="'.$total_cols.'">Payment Breakdown (CC Type) </td></tr>';
		
		foreach($arrCCPayBreakdown as $CCType => $CCPayment ){ $counter++;
			$tmpCCPayment = numberformat($CCPayment,2);
			$tmpCCTotal += $CCPayment;
			$CCType=str_replace('CC-','',$CCType);
			$ccTypeRows .= '<tr class="data">';
			$ccTypeRows .= '<td class="text_10b" style="width:'.$first_col.'; text-align:right">'.$CCType.'</td>';
			$ccTypeRows .= '<td class="text_10" style="width:'.$w_cols.'; text-align:right">'.$tmpCCPayment.'</td>';
			$ccTypeRows .= '<td class="text_10" style="width:'.$w_cols.'; text-align:right" colspan="3"></td>';
			$ccTypeRows .= '</tr>';
		}
		
		$tmpCCTotal = numberformat($tmpCCTotal,2);
		$ccTypeRows .= '<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>';
		$ccTypeRows .= '<tr class="data">';
		$ccTypeRows .= '<td class="text_10b" style="width:'.$first_col.'; text-align:right">Grand Total	</td>';
		$ccTypeRows .= '<td class="text_10" style="width:'.$w_cols.'; text-align:right">'.$tmpCCTotal.'</td>';
		$ccTypeRows .= '<td class="text_10" style="width:'.$w_cols.'; text-align:right" colspan="3"></td>';
		$ccTypeRows .= '</tr>';
		$ccTypeRows .= '<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>';
	}

	//DELETED POSTED PAYMENTS TOTAL
	$totDelPayHTML='';
	if($delPostedTotal>0 || $delPostedTotal<0){
		$totDelPayHTML='
		<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
		<tr class="data">	
			<td class="text_10b" style="width:$first_col; text-align:right">Deleted Posted Payments</td>	
			<td class="text_10b" style="width:$w_cols; text-align:right"></td>	
			<td class="text_10b" style="width:$w_cols; text-align:right">'.numberformat($delPostedTotal,2).'</td>	
			<td class="text_10b" style="width:$w_cols; text-align:right"></td>	
			<td class="text_10b" style="width:$w_cols; text-align:right"></td>	
		</tr>';
	}
		
	$page_data.= <<<DATA
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr id="heading_orange">	
				<td align="left"  colspan="$total_cols">Grand Totals</td>	
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
				<td class="text_10b" style="width:$first_col; text-align:right">Refund Amounts</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$tot_ref_amounts</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right"></td>	
				<td class="text_10b" style="width:$w_cols; text-align:right"></td>	
				<td class="text_10b" style="width:$w_cols; text-align:right"></td>	
			</tr>
			$totDelPayHTML
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
				<td align="left"  colspan="$total_cols">Payment Breakdown</td>	
			</tr>
			$payment_method_data
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr class="data">	
				<td class="text_10b" style="width:$first_col; text-align:right">Grand Total</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$totalPaidBreakdown</td>	
				<td class="text_10" style="width:$w_cols; text-align:right" colspan="3"></td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			$ccTypeRows
			</table>
DATA;

	
	//MANUALLY APPLIED RECORDS EXTRACTED FROM "Applied for Date Range" column
	if(sizeof($arrManualAppliedAmts)>0){
		$totManApplied=$arrManualAppliedAmts['cio_payment']+$arrManualAppliedAmts['pre_payment'];
		$page_data.='
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr id="heading_orange">	
				<td align="left"  class="text_b_w"  colspan="'.$total_cols.'">Manual applied amounts extracted from "Applied for Date Range" column</td>	
			</tr>
			<tr id="">	
				<td class="text_10b text_b_w" style="width:'.$first_col.'"></td>	
				<td class="text_10b text_b_w" style="width:'.$w_cols.'; text-align:center"></td>	
				<td class="text_10b text_b_w" style="width:'.$w_cols.'; text-align:center">Manual Applied Amount</td>
				<td class="text_10b text_b_w" style="width:'.$w_cols.'; text-align:center"></td>	
				<td class="text_10b text_b_w" style="width:'.$w_cols.'; text-align:center"></td>	
			</tr>
			<tr class="data">	
				<td class="text_10b" style="width:'.$first_col.'; text-align:right">CI/CO</td>	
				<td class="text_10" style="width:'.$w_cols.'; text-align:right"></td>	
				<td class="text_10" style="width:'.$w_cols.'; text-align:right">'.numberformat($arrManualAppliedAmts['cio_payment'],2).'</td>	
				<td class="text_10" style="width:'.$w_cols.'; text-align:right"></td>	
				<td class="text_10" style="width:'.$w_cols.'; text-align:right"></td>					
			</tr>
			<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr class="data">	
				<td class="text_10b" style="width:'.$first_col.'; text-align:right">Pre Payments</td>	
				<td class="text_10" style="width:'.$w_cols.'; text-align:right"></td>	
				<td class="text_10" style="width:'.$w_cols.'; text-align:right">'.numberformat($arrManualAppliedAmts['pre_payment'],2).'</td>	
				<td class="text_10" style="width:'.$w_cols.'; text-align:right"></td>	
				<td class="text_10" style="width:'.$w_cols.'; text-align:right"></td>	
			</tr>
			<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr class="data">	
				<td class="text_10b" style="width:'.$first_col.'; text-align:right">Total Manual Applied</td>	
				<td class="text_10b" style="width:'.$w_cols.'; text-align:right"></td>	
				<td class="text_10b" style="width:'.$w_cols.'; text-align:right">'.numberformat($totManApplied,2).'</td>	
				<td class="text_10b" style="width:'.$w_cols.'; text-align:right"></td>	
				<td class="text_10b" style="width:'.$w_cols.'; text-align:right"></td>	
			</tr>
			<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			</table>';
	}
	
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