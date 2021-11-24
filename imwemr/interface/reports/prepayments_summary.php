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
	
	//---------BEGIN PAGE HEADERS-------------------------
	$pdf_header ='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr>	
			<td width="258" align="left" class="rptbx1">Prepayments Report (Summary)</td>	
			<td width="258" align="left" class="rptbx2" >Selected Group : '.$sel_grp.'</td>			
			<td width="258" align="left" class="rptbx3">'.$selDateC.' (From : '.$Start_date.' To : '.$End_date.')</td>
			<td width="258" align="left" class="rptbx1">Created By: '.$report_generator_name.' on '.$curDate.'</td>
		</tr>
		<tr>	
			<td align="left" class="rptbx1" nowrap>Selected Facility : '.$sel_fac.'</td>	
			<td align="left" class="rptbx2">Selected Physician : '.$sel_phy.'</td>			
			<td align="left" class="rptbx3" >Selected Operator : '.$sel_opr.'</td>
			<td align="left" class="rptbx1" >Sel Method : '.$sel_method.'</td>
		</tr>
	</table>';
	//---------END PAGE HEADERS-------------------------

	//MAKING OUTPUT DATA
	$file_name="daily_balance.csv";
	$csv_file_name= write_html("", $file_name);
	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr=array();
	$arr[]='Prepayments Report (Summary)';
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
		
	$total_cols = 6;
	$opr_col = 17;
	$tot_opr_col=$opr_col*2;
	$last_col=25;
	$w_cols = floor((100 - ($tot_opr_col+$last_col))/($total_cols-3));
	$w_cols = $w_cols."%";
	$opr_col = $opr_col."%";
	$last_col = $last_col."%";

	if(sizeof($arrPrePayData)>0){
		$arrPreTotal = array();
		$printFile = true;
		$page_data.='
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr id="">
			<td class="text_b_w" style="text-align:center; width:$opr_col;">Operator</td>
			<td class="text_b_w" style="text-align:center; width:$opr_col;">Facility</td>
			<td class="text_b_w" style="text-align:center; width:$w_cols;" >Payments</td>
			<td class="text_b_w" style="text-align:center; width:$w_cols;" >Applied</td>
			<td class="text_b_w" style="text-align:center; width:$w_cols;" >Unapplied</td>
			<td class="text_b_w" style="text-align:center; width:$last_col;"></td>
		</tr>';

		$arr=array();
		$arr[]='Pre Payments';
		fputcsv($fp,$arr, ",","\"");	
		$arr=array();
		$arr[]='Operator';
		$arr[]='Facility';
		$arr[]='Payments';
		$arr[]='Applied';
		$arr[]='Unapplied';
		fputcsv($fp,$arr, ",","\"");

		foreach($arrPrePayData as $oprId=>$arrOprData){
			foreach($arrOprData as $facId=>$arrData){
				$opr_name = ($oprId == 0)?"Not Specified":$arrAllUsers[$oprId];
				$applied = $arrData['applied'];
				$unapplied = $arrData['payment'] - $applied;
				
				if($arrData['payment_ref']>0){
					$redRowPre=';color:#FF0000" title="Refund '.numberformat($arrData['payment_ref'],2);
				}else{ $redRowPre='';}
				
				$page_data.= '
				<tr class="data">
					<td style="text-align:left; width:'.$opr_col.';">'.$opr_name.'</td>
					<td style="text-align:left; width:'.$opr_col.';">'.$arr_sch_facilities[$facId].'</td>
					<td style="text-align:right; width:'.$w_cols.$redRowPre.'" >'.numberformat($arrData['payment'],2).'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.numberformat($applied,2).'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.numberformat($unapplied,2).'</td>
					<td style="text-align:right; width:'.$last_col.';"></td>
				</tr>';
				
				$arrPreTotal['payment'][] = $arrData['payment'];
				$arrPreTotal['applied'][] = $applied;
				$arrPreTotal['unapplied'][] = $unapplied;
				
				$arr=array();
				$arr[]=$opr_name;
				$arr[]=$arr_sch_facilities[$facId];
				$arr[]=numberformat($arrData['payment'],2);
				$arr[]=numberformat($applied,2);
				$arr[]=numberformat($unapplied,2);
				fputcsv($fp,$arr, ",","\"");
			}
		}
		
		$unapplied=array_sum($arrPreTotal['unapplied']);
		$totPrePayUnapplied= $unapplied;
		
		$tot_payment_amt = numberformat(array_sum($arrPreTotal['payment']),2);
		$tot_applied_amt = numberformat(array_sum($arrPreTotal['applied']),2);
		$tot_unapplied_amt = numberformat(array_sum($arrPreTotal['unapplied']),2);
		
		$page_data.= '
		<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
		<tr class="subtotal">
			<td style="text-align:right;" colspan="2">Total:</td>
			<td style="text-align:right;">'.$tot_payment_amt.'</td>
			<td style="text-align:right;">'.$tot_applied_amt.'</td>
			<td style="text-align:right;">'.$tot_unapplied_amt.'</td>
			<td style="text-align:right;"></td>
		</tr>
		<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
		</table>';

	}
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
	$arr[]='Deleted Pre-Payments between '.$Start_date.' and '.$End_date;
	fputcsv($fp,$arr, ",","\"");	
	$arr=array();
	$arr[]='Operator';
	$arr[]='Facility';
	$arr[]='Pre-Payment';
	fputcsv($fp,$arr, ",","\"");
	
	$colspan=4;
	$last_col= 100-((intval($opr_col)*2)+intval($w_cols));
	$last_col=$last_col.'%';
	
	foreach($arrDelAmounts as $oprId => $oprData){
		foreach($oprData as $facId => $facData){
			$operator = $arrAllUsers[$oprId];
			$arrTotals['pre_payment']+= $facData['pre_payment'];
			$arrTotals['total']+= $total;
			
			$content_part .='
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$opr_col.';">'.$operator.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$opr_col.';">'.$arr_sch_facilities[$facId].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.';">'.$CLSReports->numberFormat($facData['pre_payment'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$last_col.';"></td>
			</tr>';	
			
			$arr=array();
			$arr[]=$operator;
			$arr[]=$arr_sch_facilities[$facId];
			$arr[]=$CLSReports->numberFormat($grpData['pre_payment'],2);
			fputcsv($fp,$arr, ",","\"");
		}
	}
	
	$delPostedTotal=$arrTotals['posted'];
	$delCICOTotal=$arrTotals['cico'];
	$delPrePayTotal=$arrTotals['pre_payment'];

	$page_data.=' 
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">	
	<tr id="heading_orange"><td colspan="'.$colspan.'">Deleted Pre-Payments between '.$Start_date.' and '.$End_date.'</td></tr>
	<tr id="">
		<td class="text_b_w" style="text-align:left;">Operator &nbsp;</td>
		<td class="text_b_w" style="text-align:left;">Facility</td>
		<td class="text_b_w" style="text-align:right;">Pre-Payments&nbsp;</td>
		<td class="text_b_w"style="text-align:right;">&nbsp;</td>
	</tr>'
	.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Deleted Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrTotals['pre_payment'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	</table>';	
	
	$arr=array();
	$arr[]='';
	$arr[]='';
	$arr[]='Deleted Total:';
	$arr[]=$CLSReports->numberFormat($arrTotals['pre_payment'],2);
	fputcsv($fp,$arr, ",","\"");
}


$complete_page_data = 
$pdf_header.
$page_data;
?>