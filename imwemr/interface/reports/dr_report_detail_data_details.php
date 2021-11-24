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
$firstTotalTitle = 'Physician Total';
$secTotalTitle = 'Facility Total';
if($groupBy=='grpby_facility'){
	$firstGroupTitle = 'Physician Name';
	$secGroupTitle = 'Facility Name';
	$firstTotalTitle = 'Facility Total';
	$secTotalTitle = 'Physician Total';
}

$page_content_data.= '
<tr>
	<td class="text_b_w" width="115" style="text-align:left;">'.$firstGroupTitle.'</td>
	<td class="text_b_w" width="115" style="text-align:left;">'.$secGroupTitle.'</td>
	<td class="text_b_w" width="70" style="text-align:right;">Patient Name-ID</td>
	<td class="text_b_w" width="70" style="text-align:right;">Encounter ID</td>
	<td class="text_b_w" width="70" style="text-align:right;">DOS</td>
	<td class="text_b_w" width="70" style="text-align:right;">Beginning A/R&nbsp;</td>
	<td class="text_b_w" width="70" style="text-align:right;">Charges</td>
	<td class="text_b_w" width="70" style="text-align:right;">Payments</td>
	<td class="text_b_w" width="70" style="text-align:right;">Credit</td>
	<td class="text_b_w" width="70" style="text-align:right;">Adjustments</td>
	<td class="text_b_w" width="70" style="text-align:right;">Refunds</td>
	<td class="text_b_w" width="75" style="text-align:right;">Ending A/R&nbsp;</td>
</tr>';

//MAKING OUTPUT DATA
$file_name="provider_ar_".time().".csv";
$csv_file_name= write_html("", $file_name);

//CSV FILE NAME
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

//FOR CSV
$arr=array();
$arr[]=$dbtemp_name.' '.$summary_detail.' Report';
$arr[]=$dateModeSelected.' ('.$Start_date.' - '.$End_date.')';
$arr[]='Created by: '.$op_name.' on '.$curDate;
$arr[]='Selected Groups: '.$groupSelected;
$arr[]='Selected Facility : '.$facilitySelected;
$arr[]='Selected Physician : '.$doctorSelected;
fputcsv($fp,$arr, ",","\"");	

$arr=array();
$arr[]=$firstGroupTitle;
$arr[]=$secGroupTitle;
$arr[]="Patient Name-ID";
$arr[]="Encounter ID";
$arr[]="DOS";
$arr[]="Beginning A/R";
$arr[]="Charges";
$arr[]="Payments";
$arr[]="Credit";
$arr[]="Adjustments";
$arr[]="Refunds";
$arr[]="Ending A/R";
fputcsv($fp,$arr, ",","\"");	
		
		
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
			<td class="text_b_w" colspan="12">'.$secGroupTitle.' : '.$firstGroupName.'</td>
		</tr>';

	
	//--- GET PROVIDER DATA UNDER FACILITY ---
	$secGroupData = array_keys($checkInDataArr[$firstGroupBy]);
	
	for($p=0;$p<count($secGroupData);$p++){
		$showTot=0;
		$secGrpTotal=array();
		$secGroupName='';
		$secGroupBy=$secGroupData[$p];

		foreach($dr_tot_enc_details[$firstGroupBy][$secGroupBy] as $eid => $encDetail){

			$patient_name = core_name_format($encDetail['lname'], $encDetail['fname'], $encDetail['mname']);		
			$patient_name.=' - '.$encDetail['patient_id'];
					
			$dr_tot_beg_ar_str=$dr_tot_beg_ar_arr[$firstGroupBy][$secGroupBy][$eid];
			$dr_tot_amt_str=$dr_tot_amt_arr[$firstGroupBy][$secGroupBy][$eid];
			$dr_tot_paid_str=$dr_tot_paid_arr[$firstGroupBy][$secGroupBy][$eid];
			$dr_tot_credit_str=$dr_tot_credit_arr[$firstGroupBy][$secGroupBy][$eid];
			$dr_tot_adj_str=$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][$eid];
			$dr_tot_ref_str=$dr_tot_ref_arr[$firstGroupBy][$secGroupBy][$eid];
			$dr_tot_bal_str=$dr_tot_bal_arr[$firstGroupBy][$secGroupBy][$eid];
	
			$totChg = $dr_tot_amt_str;
			$totPay = $dr_tot_paid_str;
			$totAdj = $dr_tot_adj_str;
			
			if($groupBy=='grpby_physician'){
				$secGroupName = $fac_name_arr[$secGroupBy];
			}else{
				$secGroupName = $providerNameArr[$secGroupBy];				
			}			
			if($dr_tot_beg_ar_str !=0 || $dr_tot_amt_str !=0 || $dr_tot_paid_str !=0 || $dr_tot_credit_str !=0 || $dr_tot_adj_str !=0 ||$dr_tot_ref_str !=0 || $dr_tot_bal_str !=0){
			$page_content_data .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.wordwrap($secGroupName, 15, "<br>\n", true).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.wordwrap($firstGroupName, 15, "<br>\n", true).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$patient_name.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$eid.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$encDetail['date_of_service'].'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_beg_ar_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_amt_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_paid_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_credit_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_adj_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_ref_str,2,1).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_bal_str,2,1).'&nbsp;</td>
			</tr>';	

			//FOR CSV
			$arr=array();
			$arr[]=$secGroupName;
			$arr[]=$firstGroupName;
			$arr[]=$patient_name;
			$arr[]=$eid;
			$arr[]=$encDetail['date_of_service'];
			$arr[]=$CLSReports->numberFormat($dr_tot_beg_ar_str,2,1);
			$arr[]=$CLSReports->numberFormat($dr_tot_amt_str,2,1);
			$arr[]=$CLSReports->numberFormat($dr_tot_paid_str,2,1);
			$arr[]=$CLSReports->numberFormat($dr_tot_credit_str,2,1);
			$arr[]=$CLSReports->numberFormat($dr_tot_adj_str,2,1);
			$arr[]=$CLSReports->numberFormat($dr_tot_ref_str,2,1);
			$arr[]=$CLSReports->numberFormat($dr_tot_bal_str,2,1);
			fputcsv($fp,$arr, ",","\"");				
			}
			//DELETED ROW
			$delChg = $delPay = $delAdj = '';
			$delChg=$del_amounts_arr[$firstGroupBy][$secGroupBy][$eid]['CHARGES'];
			$delPay=$del_amounts_arr[$firstGroupBy][$secGroupBy][$eid]['PAYMENT'];
			$delAdj=$del_amounts_arr[$firstGroupBy][$secGroupBy][$eid]['ADJUSTMENT'];
	
			if(empty($delChg)==false || empty($delPay)==false || empty($delAdj)==false){
				$showTot=1;
				$page_content_data .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">Deleted Amounts :</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.$CLSReports->numberFormat($delChg,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.$CLSReports->numberFormat($delPay,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; color:#CC0000">'.$CLSReports->numberFormat($delAdj,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
				</tr>';	

				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="Deleted Amounts :";
				$arr[]="";
				$arr[]=$CLSReports->numberFormat($delChg,2,1);
				$arr[]=$CLSReports->numberFormat($delPay,2,1);
				$arr[]="";
				$arr[]=$CLSReports->numberFormat($delAdj,2,1);
				$arr[]="";
				$arr[]="";
				fputcsv($fp,$arr, ",","\"");					
				
				$totChg-=$delChg;
				$totPay-=$delPay;
				$totAdj-=$delAdj;
			}
	
			//PREVIOUS DEFAULT WRITE-OFF AMT
			$prev_write_off= $dr_tot_prev_writeoff_arr[$firstGroupBy][$secGroupBy][$eid];
			if(empty($prev_write_off)==false){
				$showTot=1;
				
				$page_content_data .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">Prev. Write-off :</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($prev_write_off,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
					<td class="text_10" bgcolor="#FFFFFF"></td>
				</tr>';	

				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="Prev. Write-off :";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]=$CLSReports->numberFormat($prev_write_off,2,1);
				$arr[]="";
				$arr[]="";
				fputcsv($fp,$arr, ",","\"");	
								
				$totAdj-=$prev_write_off;
			}
	
			if($showTot==1){
				if($dr_tot_beg_ar_str !=0 || $totChg !=0 || $totPay !=0 || $dr_tot_credit_str !=0 || $totAdj !=0 ||$dr_tot_ref_str !=0 || $dr_tot_bal_str !=0){
				$page_content_data.= '
				<tr><td colspan="12" class="total-row"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total :</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_beg_ar_str,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totChg,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totPay,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_credit_str,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totAdj,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_ref_str,2,1).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($dr_tot_bal_str,2,1).'&nbsp;</td>
				</tr>
				<tr><td colspan="12" class="total-row"></td></tr>';

				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="Total :";
				$arr[]=$CLSReports->numberFormat($dr_tot_beg_ar_str,2,1);
				$arr[]=$CLSReports->numberFormat($totChg,2,1);
				$arr[]=$CLSReports->numberFormat($totPay,2,1);
				$arr[]=$CLSReports->numberFormat($dr_tot_credit_str,2,1);
				$arr[]=$CLSReports->numberFormat($totAdj,2,1);
				$arr[]=$CLSReports->numberFormat($dr_tot_ref_str,2,1);
				$arr[]=$CLSReports->numberFormat($dr_tot_bal_str,2,1);
				fputcsv($fp,$arr, ",","\"");					
			}
			}
			//ARRAY FOR SUBTOTALS
			$secGrpTotal['BeginingAR']+=$dr_tot_beg_ar_str;
			$secGrpTotal['Charges']+=$totChg;
			$secGrpTotal['Paid']+=$totPay;
			$secGrpTotal['Credit']+=$dr_tot_credit_str;
			$secGrpTotal['Adjustment']+=$totAdj;
			$secGrpTotal['Refund']+=$dr_tot_ref_str;
			$secGrpTotal['EndingAR']+=$dr_tot_bal_str;
		}

		//ARRAY FOR SUBTOTALS
		$sub_tot_beg_ar_str=$secGrpTotal['BeginingAR'];
		$sub_tot_amt_str=$secGrpTotal['Charges'];
		$sub_tot_paid_str=$secGrpTotal['Paid'];
		$sub_tot_credit_str=$secGrpTotal['Credit'];
		$sub_tot_adj_str=$secGrpTotal['Adjustment'];
		$sub_tot_ref_str=$secGrpTotal['Refund'];
		$sub_tot_bal_str=$secGrpTotal['EndingAR'];
		
		//FIRST GROUP TOTALS
		$firstGrpTotal['BeginingAR']+=$secGrpTotal['BeginingAR'];
		$firstGrpTotal['Charges']+=$secGrpTotal['Charges'];
		$firstGrpTotal['Paid']+=$secGrpTotal['Paid'];
		$firstGrpTotal['Credit']+=$secGrpTotal['Credit'];
		$firstGrpTotal['Adjustment']+=$secGrpTotal['Adjustment'];
		$firstGrpTotal['Refund']+=$secGrpTotal['Refund'];
		$firstGrpTotal['EndingAR']+=$secGrpTotal['EndingAR'];

	
		$page_content_data .= '
		<tr><td colspan="12" class="total-row"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$secTotalTitle.'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_beg_ar_str,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_amt_str,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_paid_str,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_credit_str,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_adj_str,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_ref_str,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_bal_str,2,1).'&nbsp;</td>
		</tr>
		<tr><td colspan="12" class="total-row"></td></tr>
		';	

  		//FOR CSV
		$arr=array();
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="Total :";
		$arr[]=$CLSReports->numberFormat($dr_tot_beg_ar_str,2,1);
		$arr[]=$CLSReports->numberFormat($totChg,2,1);
		$arr[]=$CLSReports->numberFormat($totPay,2,1);
		$arr[]=$CLSReports->numberFormat($dr_tot_credit_str,2,1);
		$arr[]=$CLSReports->numberFormat($totAdj,2,1);
		$arr[]=$CLSReports->numberFormat($dr_tot_ref_str,2,1);
		$arr[]=$CLSReports->numberFormat($dr_tot_bal_str,2,1);
		fputcsv($fp,$arr, ",","\"");					
	}

	//ARRAY FOR SUBTOTALS
	$sub_tot_beg_ar_str=$firstGrpTotal['BeginingAR'];
	$sub_tot_amt_str=$firstGrpTotal['Charges'];
	$sub_tot_paid_str=$firstGrpTotal['Paid'];
	$sub_tot_credit_str=$firstGrpTotal['Credit'];
	$sub_tot_adj_str=$firstGrpTotal['Adjustment'];
	$sub_tot_ref_str=$firstGrpTotal['Refund'];
	$sub_tot_bal_str=$firstGrpTotal['EndingAR'];

	$page_content_data .= '
	<tr><td colspan="12" class="total-row"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$firstTotalTitle.'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_beg_ar_str,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_amt_str,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_paid_str,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_credit_str,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_adj_str,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_ref_str,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($sub_tot_bal_str,2,1).'&nbsp;</td>
	</tr>
	<tr><td colspan="12" class="total-row"></td></tr>
	';	

	//FOR CSV
	$arr=array();
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]=$firstTotalTitle;
	$arr[]=$CLSReports->numberFormat($sub_tot_beg_ar_str,2,1);
	$arr[]=$CLSReports->numberFormat($sub_tot_amt_str,2,1);
	$arr[]=$CLSReports->numberFormat($sub_tot_paid_str,2,1);
	$arr[]=$CLSReports->numberFormat($sub_tot_credit_str,2,1);
	$arr[]=$CLSReports->numberFormat($sub_tot_adj_str,2,1);
	$arr[]=$CLSReports->numberFormat($sub_tot_ref_str,2,1);
	$arr[]=$CLSReports->numberFormat($sub_tot_bal_str,2,1);
	fputcsv($fp,$arr, ",","\"");		

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
<table class="rpt rpt_table rpt_table-bordered" width="1050">
	<?php print $page_content_data; ?>
    <tr><td colspan="12" bgcolor="#FFFFFF">&nbsp;</td></tr>
    <tr><td class='total-row' colspan="12"></td></tr>
<?php
	$totBeg= array_sum($dr_fn_beg_ar_arr);
	$totChg= array_sum($dr_fn_tot_amt_arr);
	$totPay= array_sum($dr_fn_tot_paid_arr);
	$totCrd= array_sum($dr_fn_tot_credit_arr);
	$totAdj= array_sum($dr_fn_tot_adj_arr);
	$totRef= array_sum($dr_fn_tot_ref_arr);
	$totEnd= array_sum($dr_fn_tot_bal_arr);
	
    $totRow='
	<tr>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Sub Total :</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totBeg,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totChg,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totPay,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCrd,2,1).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totAdj,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totRef,2,1).'&nbsp;</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totEnd,2,1).'&nbsp;</td>
    </tr>';

	//FOR CSV
	$arr=array();
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="Sub Total :";
	$arr[]=$CLSReports->numberFormat($totBeg,2,1);
	$arr[]=$CLSReports->numberFormat($totChg,2,1);
	$arr[]=$CLSReports->numberFormat($totPay,2,1);
	$arr[]=$CLSReports->numberFormat($totCrd,2,1);
	$arr[]=$CLSReports->numberFormat($totAdj,2,1);
	$arr[]=$CLSReports->numberFormat($totRef,2,1);
	$arr[]=$CLSReports->numberFormat($totEnd,2,1);
	fputcsv($fp,$arr, ",","\"");	
	
	if($DateRangeFor=='dos'){
		$afterDeductProcAmt = $totEnd - $balWithoutCreditCalculated;
		$totRow.='
		<tr><td class="total-row" colspan="12"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="11">Procedure Balance Without Calculating Over-Payment :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($balWithoutCreditCalculated,2,1).'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="12"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="11">Deducting Procedure Balance :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($afterDeductProcAmt,2,1).'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="12"></td></tr>';

		//FOR CSV
		$arr=array();
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="Procedure Balance Without Calculating Over-Payment :";
		$arr[]=$CLSReports->numberFormat($balWithoutCreditCalculated,2,1);
		fputcsv($fp,$arr, ",","\"");			

		$arr=array();
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="Deducting Procedure Balance :";
		$arr[]=$CLSReports->numberFormat($afterDeductProcAmt,2,1);
		fputcsv($fp,$arr, ",","\"");			

	}
	
	if($DateRangeFor=='dot'){	
		$afterAddingchargesForNotPosted = $totChg + $chargesForNotPosted;
		$totalEndingARIncludingNotPosted  = $totEnd + $chargesForNotPosted;
		$totRow.='
		<tr><td bgcolor="#FFFFFF" colspan="12">&nbsp;</td></tr>
		<tr><td class="total-row" colspan="12"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Not Posted :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($chargesForNotPosted,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">$0.00&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="12"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total :</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totBeg,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($afterAddingchargesForNotPosted,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totPay,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCrd,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totAdj,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totRef,2,1).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totalEndingARIncludingNotPosted,2,1).'&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="12"></td></tr>';

		//FOR CSV
		$arr=array();
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="Not Posted :";
		$arr[]="$0.00";
		$arr[]=$CLSReports->numberFormat($chargesForNotPosted,2,1);
		$arr[]="$0.00";
		$arr[]="$0.00";
		$arr[]="$0.00";
		$arr[]="$0.00";
		$arr[]="$0.00";
		fputcsv($fp,$arr, ",","\"");

		//FOR CSV
		$arr=array();
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="";
		$arr[]="Total :";
		$arr[]=$CLSReports->numberFormat($totBeg,2,1);
		$arr[]=$CLSReports->numberFormat($afterAddingchargesForNotPosted,2,1);
		$arr[]=$CLSReports->numberFormat($totPay,2,1);
		$arr[]=$CLSReports->numberFormat($totCrd,2,1);
		$arr[]=$CLSReports->numberFormat($totAdj,2,1);
		$arr[]=$CLSReports->numberFormat($totRef,2,1);
		$arr[]=$CLSReports->numberFormat($totalEndingARIncludingNotPosted,2,1);
		fputcsv($fp,$arr, ",","\"");				
	}
	echo $totRow;
?>    
    <tr><td class='total-row' colspan="12"></td></tr>
</table>

<?php
// CI/CO NOT APPLIED
$notAppPart='';
$arrTot=array();
$arrTotNotApp=array();
if(count($arrGrpCICONotApplied)>0){
	
	//FOR CSV
	$arr=array();
	$arr[]=""; //blank line
	fputcsv($fp,$arr, ",","\"");	
	$arr=array();
	$arr[]="Unapplied CI/CO Amounts";
	fputcsv($fp,$arr, ",","\"");	
	
	$arr=array();
	$arr[]="Patient Name";
	$arr[]="Padid Date";
	$arr[]="Before ".$Start_date;
	$arr[]="From ".$Start_date." To ".$End_date;
	$arr[]="Till ".$End_date;
	fputcsv($fp,$arr, ",","\"");	
		
	foreach($arrGrpCICONotApplied as $firstGroupBy => $firstgrpData){
		$arrSubTot=array();
		$firstGroupName='';
		if($groupBy=='grpby_physician'){
			$firstTitle='Physician';
			$firstGroupName = $providerNameArr[$firstGroupBy];
		}else{
			$firstTitle='Facility';
			$firstGroupName = $fac_name_arr[$firstGroupBy];
		}
		$notAppPart.= '<tr><td class="text_b_w" colspan="12">'.$firstTitle.' : '.$firstGroupName.'</td></tr>';
		
		foreach($firstgrpData as $payment_id){
			$transDet = $arrGrpCICONotAppliedDet[$firstGroupBy][$payment_id];
			$created_on= $transDet['created_on'];
			list($patient_id,$fname,$mname,$lname)=explode('~', $transDet['patient']);
			$patient_name = core_name_format($lname, $fname, $mname);
			$patient_name.=' - '.$patient_id;
			
			//REFUNDS
			$fontColor1= $fontColor2='#000';
			$title1 = $title2='';
			if($arrCicoRefundFirst[$firstGroupBy][$payment_id]>0){
				$arrCICONotAppliedFirst[$firstGroupBy][$payment_id]-=$arrCicoRefundFirst[$firstGroupBy][$payment_id];
				$fontColor1='#FF0000';
				$title1= 'title="Refund : '.$CLSReports->numberFormat($arrCicoRefundFirst[$firstGroupBy][$payment_id],2,1).'"';
			}
			if($arrCicoRefundSec[$firstGroupBy][$payment_id]>0){
				$arrCICONotAppliedSec[$firstGroupBy][$payment_id]-=$arrCicoRefundSec[$firstGroupBy][$payment_id];
				$fontColor2='#FF0000';
				$title2= 'title="Refund : '.$CLSReports->numberFormat($arrCicoRefundSec[$firstGroupBy][$payment_id],2,1).'"';
			}
					
			$totAmt = $arrCICONotAppliedFirst[$firstGroupBy][$payment_id] + $arrCICONotAppliedSec[$firstGroupBy][$payment_id];
			$arrSubTot['First']+=$arrCICONotAppliedFirst[$firstGroupBy][$payment_id];
			$arrSubTot['Second']+=$arrCICONotAppliedSec[$firstGroupBy][$payment_id];

			$notAppPart.='
			<tr style="height:25px">
				<td class="text_10 white" style="text-align:left;">'.$patient_name.'</td>
				<td class="text_10 white" style="text-align:left;">'.$created_on.'</td>
				<td class="text_10 white" style="text-align:right; color:'.$fontColor1.'" '.$title1.'>'.$CLSReports->numberFormat($arrCICONotAppliedFirst[$firstGroupBy][$payment_id],2,1).'&nbsp;</td>
				<td class="text_10 white" style="text-align:right; color:'.$fontColor2.'" '.$title2.'>'.$CLSReports->numberFormat($arrCICONotAppliedSec[$firstGroupBy][$payment_id],2,1).'&nbsp;</td>
				<td class="text_10 white" style="text-align:right;">'.$CLSReports->numberFormat($totAmt,2,1).'&nbsp;</td>
			</tr>';

			//FOR CSV
			$arr=array();
			$arr[]=$patient_name;
			$arr[]=$created_on;
			$arr[]=$CLSReports->numberFormat($arrCICONotAppliedFirst[$firstGroupBy][$payment_id],2,1);
			$arr[]=$CLSReports->numberFormat($arrCICONotAppliedSec[$firstGroupBy][$payment_id],2,1);
			$arr[]=$CLSReports->numberFormat($totAmt,2,1);
			fputcsv($fp,$arr, ",","\"");	
						
		}

		$arrTot['First']+=$arrSubTot['First'];
		$arrTot['Second']+=$arrSubTot['Second'];

		$notAppPart.='
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;" colspan="2">'.$firstTitle.' Total :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrSubTot['First'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrSubTot['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrSubTot['First'] + $arrSubTot['Second'],2,1).'&nbsp;</td>
		</tr>';
	}
	
	$arrTotNotApp['First']+=$arrTot['First'];
	$arrTotNotApp['Second']+=$arrTot['Second'];
	
	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered" width="100%">
		<tr id="heading_orange"><td style="text-align:left; width:100%" colspan="5">&nbsp;Unapplied CI/CO Amounts</td></tr>
		<tr>
			<td class="text_b_w" width="210" style="text-align:center;">Patient Name - ID</td>
			<td class="text_b_w" width="210" style="text-align:center;">Paid Date</td>
			<td class="text_b_w" width="210" style="text-align:center;">Before '.$Start_date.'</td>
			<td class="text_b_w" width="210" style="text-align:center;">From '.$Start_date.' To '.$End_date.'</td>	
			<td class="text_b_w" width="210" style="text-align:center;">Till '.$End_date.'</td>
		</tr>'.
		$notAppPart.'
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;" colspan="2">Total CI/CO :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'] + $arrTot['Second'],2,1).'&nbsp;</td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';

	//FOR CSV
	$arr=array();
	$arr[]="";
	$arr[]="Total CI/CO :";
	$arr[]=$CLSReports->numberFormat($arrTot['First'],2,1);
	$arr[]=$CLSReports->numberFormat($arrTot['Second'],2,1);
	$arr[]=$CLSReports->numberFormat($arrTot['First'] + $arrTot['Second'],2,1,2,1);
	fputcsv($fp,$arr, ",","\"");	
	
echo $not_app_csv;
}

// PRE PAYMENT NOT APPLIED
$notAppPart='';
$arrTot=array();
if(count($arrGrpPrePayNotApplied)>0){

	//FOR CSV
	$arr=array();
	$arr[]=""; //blank line
	fputcsv($fp,$arr, ",","\"");	
	$arr=array();
	$arr[]="Unapplied Pre Payment Amounts";
	fputcsv($fp,$arr, ",","\"");	
	
	$arr=array();
	$arr[]="Patient Name";
	$arr[]="Padid Date";
	$arr[]="Before ".$Start_date;
	$arr[]="From ".$Start_date." To ".$End_date;
	$arr[]="Till ".$End_date;
	fputcsv($fp,$arr, ",","\"");	
		
	foreach($arrGrpPrePayNotApplied as $firstGroupBy => $firstGrpData){
		$arrSubTot=array();
		$firstGroupName='';
		if($groupBy=='grpby_physician'){
			$firstTitle='Physician';
			$firstGroupName = $providerNameArr[$firstGroupBy];
		}else{
			$firstTitle='Facility';
			$firstGroupName = $fac_name_arr[$firstGroupBy];
		}		
		$notAppPart.= '<tr><td class="text_b_w" colspan="12">'.$firstTitle.' : '.$firstGroupName.'</td></tr>';
		
		foreach($firstGrpData as $id){
			$transDet = $arrGrpPrePayNotAppliedDet[$firstGroupBy][$id];
			$entered_date= $transDet['entered_date'];
			list($patient_id,$fname,$mname,$lname)=explode('~', $transDet['patient']);
			$patient_name = core_name_format($lname, $fname, $mname);
			$patient_name.=' - '.$patient_id;

			//REFUNDS
			$fontColor1= $fontColor2='#000';
			$title1 = $title2='';
			if($arrPrePayRefundFirst[$firstGroupBy][$id]>0){
				$arrPrePayNotAppliedFirst[$firstGroupBy][$id]-=$arrPrePayRefundFirst[$firstGroupBy][$id];
				$fontColor1='#FF0000';
				$title1= 'title="Refund : '.$CLSReports->numberFormat($arrPrePayRefundFirst[$firstGroupBy][$id],2,1).'"';
			}
			if($arrPrePayRefundSec[$firstGroupBy][$id]>0){
				$arrPrePayNotAppliedSec[$firstGroupBy][$id]-=$arrPrePayRefundSec[$firstGroupBy][$id];
				$fontColor2='#FF0000';
				$title2= 'title="Refund : '.$CLSReports->numberFormat($arrPrePayRefundSec[$firstGroupBy][$id],2,1).'"';			
			}
	
			$totAmt = $arrPrePayNotAppliedFirst[$firstGroupBy][$id] + $arrPrePayNotAppliedSec[$firstGroupBy][$id];
			$arrSubTot['First']+=$arrPrePayNotAppliedFirst[$firstGroupBy][$id];
			$arrSubTot['Second']+=$arrPrePayNotAppliedSec[$firstGroupBy][$id];
					
			$notAppPart.='
			<tr style="height:25px">
				<td class="text_10 white" style="text-align:left;">'.$patient_name.'</td>
				<td class="text_10 white" style="text-align:left;">'.$entered_date.'</td>
				<td class="text_10 white" style="text-align:right; color:'.$fontColor1.'" '.$title1.'>'.$CLSReports->numberFormat($arrPrePayNotAppliedFirst[$firstGroupBy][$id],2,1).'&nbsp;</td>
				<td class="text_10 white" style="text-align:right; color:'.$fontColor2.'" '.$title2.'>'.$CLSReports->numberFormat($arrPrePayNotAppliedSec[$firstGroupBy][$id],2,1).'&nbsp;</td>
				<td class="text_10 white" style="text-align:right;">'.$CLSReports->numberFormat($totAmt,2,1).'&nbsp;</td>
			</tr>';

			//FOR CSV
			$arr=array();
			$arr[]=$patient_name;
			$arr[]=$entered_date;
			$arr[]=$CLSReports->numberFormat($arrPrePayNotAppliedFirst[$firstGroupBy][$id],2,1);
			$arr[]=$CLSReports->numberFormat($arrPrePayNotAppliedSec[$firstGroupBy][$id],2,1);
			$arr[]=$CLSReports->numberFormat($totAmt,2,1);
			fputcsv($fp,$arr, ",","\"");				
		}

		$arrTot['First']+=$arrSubTot['First'];
		$arrTot['Second']+=$arrSubTot['Second'];

		$notAppPart.='
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;" colspan="2">'.$firstTitle.' Total :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrSubTot['First'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrSubTot['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrSubTot['First'] + $arrSubTot['Second'],2,1).'&nbsp;</td>
		</tr>';
	}
	
	$arrTotNotApp['First']+=$arrTot['First'];
	$arrTotNotApp['Second']+=$arrTot['Second'];

	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered" width="100%">
		<tr id="heading_orange"><td style="text-align:left;" colspan="7">&nbsp;Unapplied Pre Payment Amounts</td></tr>
		<tr>
			<td class="text_b_w" width="210" style="text-align:center;">Patient name-ID</td>
			<td class="text_b_w" width="210" style="text-align:center;">Paid Date</td>
			<td class="text_b_w" width="210" style="text-align:center;">Before '.$Start_date.'</td>
			<td class="text_b_w" width="210" style="text-align:center;">From '.$Start_date.' To '.$End_date.'</td>	
			<td class="text_b_w" width="210" style="text-align:center;">Till '.$End_date.'</td>
		</tr>'.
		$notAppPart.'
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" style="text-align:right;" colspan="2">Total Pre Payment :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($arrTot['First'] + $arrTot['Second'],2,1).'&nbsp;</td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';

	//FOR CSV
	$arr=array();
	$arr[]="";
	$arr[]="Total Pre Payment :";
	$arr[]=$CLSReports->numberFormat($arrTot['First'],2,1);
	$arr[]=$CLSReports->numberFormat($arrTot['Second'],2,1);
	$arr[]=$CLSReports->numberFormat($arrTot['First'] + $arrTot['Second'],2,1);
	fputcsv($fp,$arr, ",","\"");		

echo $not_app_csv;
}

$not_app_csv='';
if(count($arrGrpCICONotApplied)>0 || count($arrGrpPrePayNotApplied)>0){
	$not_app_csv='
	<table class="rpt rpt_table rpt_table-bordered" width="100%">
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_10b white" width="210" style="text-align:right;" colspan="2">Total Not Applied :</td>
			<td class="text_10b white" width="210" style="text-align:right;">'.$CLSReports->numberFormat($arrTotNotApp['First'],2,1).'&nbsp;</td>
			<td class="text_10b white" width="210" style="text-align:right;">'.$CLSReports->numberFormat($arrTotNotApp['Second'],2,1).'&nbsp;</td>
			<td class="text_10b white" width="210" style="text-align:right;">'.$CLSReports->numberFormat($arrTotNotApp['First'] + $arrTotNotApp['Second'],2,1).'&nbsp;</td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';
	
	//FOR CSV
	$arr=array();
	$arr[]="";
	$arr[]="Total Not Applied :";
	$arr[]=$CLSReports->numberFormat($arrTotNotApp['First'],2,1);
	$arr[]=$CLSReports->numberFormat($arrTotNotApp['Second'],2,1);
	$arr[]=$CLSReports->numberFormat($arrTotNotApp['First'] + $arrTotNotApp['Second'],2,1);
	fputcsv($fp,$arr, ",","\"");	
		
echo $not_app_csv;		
}

echo '</page>';


// REFUND - CI/CO AND PRE PAYMENTS	
if(sizeof($arrMainOthers)>0){
	$dataExists=1;
	$pdfData2 = $csvFileData2 = '';
	$arrTot = array();

	//FOR CSV
	$arr=array();
	$arr[]="";
	fputcsv($fp,$arr, ",","\"");	
	$arr=array();
	$arr[]="Refunds of CI/CO and Pre-Payments";
	fputcsv($fp,$arr, ",","\"");	

	$arr=array();
	$arr[]="Patient Name";
	$arr[]="Patient-ID";
	$arr[]="Paid Date";
	$arr[]="Facility";
	$arr[]="Refund Date";
	$arr[]="Type";
	$arr[]="Payment";
	$arr[]="Refund";
	$arr[]="Method";
	fputcsv($fp,$arr, ",","\"");	
		
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

					//FOR CSV
					$arr=array();
					$arr[]=$patientName;
					$arr[]=$patId;
					$arr[]=$paidDate;
					$arr[]=$fac_name_arr[$facId];
					$arr[]=$patDetails['ref_date'];
					$arr[]="Check In/Out";
					$arr[]=$CLSReports->numberFormat($paidAmt,2,1);
					$arr[]=$CLSReports->numberFormat($patDetails['ref_amt'],2,1);
					$arr[]=$patDetails['method'];
					fputcsv($fp,$arr, ",","\"");						
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

					//FOR CSV
					$arr=array();
					$arr[]=$patientName;
					$arr[]=$patId;
					$arr[]=$paidDate;
					$arr[]=$fac_name_arr[$facId];
					$arr[]=$patDetails['ref_date'];
					$arr[]="Pre-Payment";
					$arr[]=$CLSReports->numberFormat($paidAmt,2,1);
					$arr[]=$CLSReports->numberFormat($patDetails['ref_amt'],2,1);
					$arr[]=$patDetails['method'];
					fputcsv($fp,$arr, ",","\"");						
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
		<td class="text_b_w" width="100" style="text-align:left">Patient Name</td>
		<td class="text_b_w" width="100" style="text-align:left">Patient-ID</td>
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
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">CI/CO & Pre-Payments Total:</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTot['paidAmt'],2,1).'</td>
		<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrTot['refAmt'],2,1).'</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="9"></td></tr>
	</table>';

	//FOR CSV
	$arr=array();
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="CI/CO & Pre-Payments Total:";
	$arr[]=$CLSReports->numberFormat($arrTot['paidAmt'],2,1);
	$arr[]=$CLSReports->numberFormat($arrTot['refAmt'],2,1);
	$arr[]="";
	fputcsv($fp,$arr, ",","\"");		

	echo $otherHTML;
}



$page_content = ob_get_contents();
ob_end_clean();
?>
