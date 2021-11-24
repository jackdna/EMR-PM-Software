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
FILE : unapplied_payment_summary.php
PURPOSE : Display summary result of unapplied amounts report
ACCESS TYPE : Direct
*/
	$total_cols = 6;
	$op_col = $op_col1 ="17";
	$w_cols = $w_cols1 = floor((100 - ($op_col))/($total_cols-1));
	$op_col = $op_col1 = 100 - ( (($total_cols-1) * $w_cols));
	
	$w_cols = $w_cols."%";
	$op_col = $op_col."%";
	//--- CSV FILE HEADER INFORMATION 

	$pdf_header = <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:300px;">&nbsp;$report_tit</td>
				<td class="rptbx2" style="width:300px;">&nbsp;Selected DOP : $Start_date To $End_date</td>
				<td class="rptbx3" style="width:450px;">&nbsp;Created by $op_name on $curDate</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1">&nbsp;Selected Group : $sel_grp</td>
				<td class="rptbx2">&nbsp;Selected Facility : $sel_fac</td>
				<td class="rptbx3">&nbsp;Selected Physician : $sel_phy &nbsp;&nbsp;&nbsp;Selected Operator : $sel_opr</td>
			</tr>
		</table>
DATA;

$arrGrand = array();
if(sizeof($arrPayments)>0){
	//pre($arrAppliedAmt);
	//pre($arrPayments);
	$arrTotal = array();
	$dispGrand = 0;
	$printFile = true;
	$page_data=$pdf_header;
	$page_data.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
			<tr id="heading_orange">	
				<td align="left"  class="text_b_w"  colspan="$total_cols">CI/CO Payments and Pre Payments</td>	
			</tr>
DATA;
	$page_data.= <<<DATA
				<tr>	
					<td class="text_b_w" style="text-align:center; width:200px;">Operator</td>
					<td class="text_b_w" style="text-align:center; width:170px;">CI Payments</td>
					<td class="text_b_w" style="text-align:center; width:170px;">CO Payments</td>
					<td class="text_b_w" style="text-align:center; width:170px;">Pre Payments</td>
					<td class="text_b_w" style="text-align:center; width:170px;">Del Payment</td>
					<td class="text_b_w" style="text-align:center; width:170px;">Unapplied</td>
				</tr>				
DATA;
//MAKING OUTPUT DATA
$file_name="unappiled_payment.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr=array();
$arr[]=$report_tit;
$arr[]='Selected DOP :'.$Start_date.' To '.$End_date;
$arr[]='Created by :'.$op_name.' on '.$curDate;
fputcsv($fp,$arr, ",","\"");
$arr=array();
$arr[]='Selected Group : '.$sel_grp;
$arr[]='Selected Facility : '.$sel_fac;
$arr[]='Selected Physician : '.$sel_phy;
$arr[]='Selected Operator : '.$sel_opr;
fputcsv($fp,$arr, ",","\"");
$arr=array();
$arr[]='CI/CO Payments and Pre Payments';
fputcsv($fp,$arr, ",","\"");
$arr=array();
$arr[]='Operator';
$arr[]='CI Payments';
$arr[]='CO Payments';
$arr[]='Pre Payments';
$arr[]='Del Payment';
$arr[]='Unapplied';
fputcsv($fp,$arr, ",","\"");

	foreach($arrPayments as $opr_id=>$arrOprPayment){
		$subArr = array();
		$opr_name = $arrAllUsers[$opr_id];
		$applied_amt = 0;
		
		if($arrOprPayment['ci_payment_ref']>0)
		$ci_redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($arrOprPayment['ci_payment_ref'],2);
		else
		$ci_redRow='';
		
		if($arrOprPayment['co_payment_ref']>0)
		$co_redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($arrOprPayment['co_payment_ref'],2);
		else
		$co_redRow='';
		
		if($arrOprPayment['pre_payment_ref']>0)
		$pre_redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($arrOprPayment['pre_payment_ref'],2);
		else
		$pre_redRow='';
		
		if($arrOprPayment['del_payment_ref']>0)
		$del_redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($arrOprPayment['del_payment_ref'],2);
		else
		$del_redRow='';
		
		$arrOprPayment['ci_payment']-= $arrOprPayment['ci_payment_ref'];
		$arrOprPayment['co_payment']-= $arrOprPayment['co_payment_ref'];
		$arrOprPayment['pre_payment']-= $arrOprPayment['pre_payment_ref'];

		$unapplied_amt = $arrOprPayment['un_applied'] + $arrOprPayment['pre_un_applied'];
		
		if($opr_id == 0)
		$opr_name = "Not Specified";
		$page_data.= '<tr  class="data">
			<td style="text-align:left; width:'.$op_col.';">'.$opr_name.'</td>
			<td style="text-align:right; width:'.$w_cols.$ci_redRow.'">'.$CLSReports->numberFormat($arrOprPayment['ci_payment'],2,1).'</td>
			<td style="text-align:right; width:'.$w_cols.$co_redRow.'">'.$CLSReports->numberFormat($arrOprPayment['co_payment'],2,1).'</td>
			<td style="text-align:right; width:'.$w_cols.$pre_redRow.'">'.$CLSReports->numberFormat($arrOprPayment['pre_payment'],2,1).'</td>
			<td style="text-align:right; width:'.$w_cols.$del_redRow.'">'.$CLSReports->numberFormat($arrOprPayment['del_payment'],2).'</td>
			<td style="text-align:right; width:'.$w_cols.';">'.$CLSReports->numberFormat($unapplied_amt,2).'</td>
		</tr>';
		
		$arr=array();
		$arr[]=$opr_name;
		$arr[]=$CLSReports->numberFormat($arrOprPayment['ci_payment'],2,1);
		$arr[]=$CLSReports->numberFormat($arrOprPayment['co_payment'],2,1);
		$arr[]=$CLSReports->numberFormat($arrOprPayment['pre_payment'],2,1);
		$arr[]=$CLSReports->numberFormat($arrOprPayment['del_payment'],2,1);
		$arr[]=$CLSReports->numberFormat($unapplied_amt,2);
		fputcsv($fp,$arr, ",","\"");
		
		$arrTotal['ci_payment'][] = $arrOprPayment['ci_payment'];
		$arrTotal['co_payment'][] = $arrOprPayment['co_payment'];
		$arrTotal['pre_payment'][] = $arrOprPayment['pre_payment'];
		$arrTotal['del_payment'][] = $arrOprPayment['del_payment'];
		$arrTotal['unapplied'][] = $unapplied_amt;
	}
	$tot_ci_payment = $CLSReports->numberFormat(array_sum($arrTotal['ci_payment']),2);
	$tot_co_payment = $CLSReports->numberFormat(array_sum($arrTotal['co_payment']),2);
	$tot_pre_payment = $CLSReports->numberFormat(array_sum($arrTotal['pre_payment']),2);
	$tot_del_payment = $CLSReports->numberFormat(array_sum($arrTotal['del_payment']),2);
	$tot_unapplied = $CLSReports->numberFormat(array_sum($arrTotal['unapplied']),2);
	$page_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
					<td class="text_10b" valign="top" style="text-align:right;" >Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_ci_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_co_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_pre_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_del_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_unapplied</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
$page_data .= <<<DATA
	</table>
DATA;
		$pdf_file_content .=<<<DATA
			<page backtop="12mm" backbottom="10mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$pdf_header
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
			<tr id="heading_orange">	
				<td align="left"  class="text_10b" style="width:100%">Write-off Summary</td>	
			</tr>
			</table>
			</page_header>
			$page_data
			</page>
DATA;
}//END WRITEOFF IF
$page_data.='<br>';

$page_data = $page_data;
$pdf_data = $page_data;
?>