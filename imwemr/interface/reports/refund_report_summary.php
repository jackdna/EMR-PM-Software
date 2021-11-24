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
FILE : refundReportsUMMARY.php
PURPOSE : REFUND REPORT SUMMARY VIEW
ACCESS TYPE : INCLUDED
*/

$colTitle='Physician';
if($view_by=='facility')$colTitle='Facility';


$page_header = '
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
        <tr class="rpt_headers">
            <td class="rptbx1" style="width:260px;">&nbsp;Refund Report : Summary</td>	
            <td class="rptbx2" style="width:250px;">&nbsp;Selected DOT : '.$Start_date.' To '.$End_date.'</td>			
            <td class="rptbx3" style="width:260px;">&nbsp;Pat : '.$pat_nam_srh.'</td>
            <td class="rptbx1" style="width:250px;">&nbsp;Created by '.$op_name.' on '.$curDate.'</td>
        </tr>
        <tr class="rpt_headers">	
            <td class="rptbx1" style="width:260px;">&nbsp;Selected Group : '.$group_name.'</td>	
            <td class="rptbx2" style="width:250px;">&nbsp;Selected Facility : '.$practice_name.'</td>			
            <td class="rptbx3" style="width:260px;">&nbsp;Selected Physician : '.$physician_name.'</td>
            <td class="rptbx1" style="width:250px;">&nbsp;Selected Operator : '.$operator_name.'</td>
        </tr>
	</table>
	';
//MAKING OUTPUT DATA
$file_name="refund_report.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');
$arr=array();
$arr[]='Refund Report : Summary';
$arr[]='Selected DOT : '.$Start_date.' To '.$End_date;
$arr[]='Pat : '.$pat_nam_srh;
$arr[]='Created by: '.$op_name.' on '.$curDate;
fputcsv($fp,$arr, ",","\"");

$arr=array();
$arr[]='Selected Group : '.$group_name;
$arr[]='Selected Facility : '.$practice_name;
$arr[]='Selected Physician : '.$physician_name;
$arr[]='Selected Operator : '.$operator_name;
fputcsv($fp,$arr, ",","\"");
//--- CREATE HTML ----------
if($view_by=='fac_n_oper'){

	$total_cols = 5;
	$phy_col = $phy_col1 ="25";
	$w_cols = $w_cols1 = floor((100 - ($phy_col))/($total_cols-1));
	$phy_col = $phy_col1 = 100 - ( (($total_cols-1) * $w_cols));
	
	$w_cols.='%';
	$phy_col.='%';
	
	$arr=array();
	$arr[]="Facility";
	$arr[]="Operator";
	$arr[]="Charges";
	$arr[]="Payments";
	$arr[]="Refund";
	fputcsv($fp,$arr, ",","\"");
	if(count($arrRefundData)>0){ 
		$page_data.='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" style="width:100%">
		<tr>
			<td class="text_b_w" style="text-align:center; width:'.$phy_col.';">Facility</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Operator</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Charges</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Payments</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Refund</td>
		</tr>';
		$arrTotal = array();
		foreach($arrRefundData as $firstGrpId=>$firstGrpData){
			foreach($firstGrpData as $secGrpId=>$arrData){
				$first_grp_name = $posFacilityArr[$firstGrpId];
				$sec_grp_name = $userNameArr[$secGrpId];
				
				$charges = $CLSReports->numberFormat($arrData['charges'],2);
				$payment = $CLSReports->numberFormat($arrData['payment'],2);
				$refund = $CLSReports->numberFormat($arrData['refund'],2);
				$page_data.='
				<tr class="data">
					<td style="text-align:left; width:'.$phy_col.';">'.$first_grp_name.'</td>
					<td style="text-align:left; width:'.$w_cols.';">'.$sec_grp_name.'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.$charges.'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.$payment.'</td>
					<td style="text-align:right; width:'.$w_cols.';">'.$refund.'</td>
				</tr>';
						
				$arrTotal['charges'][] = $arrData['charges'];
				$arrTotal['payment'][] = $arrData['payment'];
				$arrTotal['refund'][] = $arrData['refund'];
				
				$arr=array();
				$arr[]=$first_grp_name;
				$arr[]=$sec_grp_name;
				$arr[]=$charges;
				$arr[]=$payment;
				$arr[]=$refund;
				fputcsv($fp,$arr, ",","\"");
				
				
			}
		}
		$tot_charges = $CLSReports->numberFormat(array_sum($arrTotal['charges']),2);
		$tot_payment = $CLSReports->numberFormat(array_sum($arrTotal['payment']),2);
		$tot_refund = $CLSReports->numberFormat(array_sum($arrTotal['refund']),2);
		$page_data.='
		<tr class="subtotal">
			<td style="text-align:right; width:'.$phy_col.';"></td>
			<td style="text-align:right; width:'.$w_cols.';">Total</td>
			<td style="text-align:right; width:'.$w_cols.';">'.$tot_charges.'</td>
			<td style="text-align:right; width:'.$w_cols.';">'.$tot_payment.'</td>
			<td style="text-align:right; width:'.$w_cols.';">'.$tot_refund.'</td>
		</tr>
		</table>';
	}
	$pdfData ='
	<page backtop="9mm" backbottom="10mm">
	<page_footer>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>
		'.$page_header.'
	</page_header>
		'.$page_data.'
	</page>';	
	
}else{

	$total_cols = 4;
	$phy_col = $phy_col1 ="25";
	$w_cols = $w_cols1 = floor((100 - ($phy_col))/($total_cols-1));
	$phy_col = $phy_col1 = 100 - ( (($total_cols-1) * $w_cols));
	
	$w_cols.='%';
	$phy_col.='%';

	if(count($arrRefundData)>0){ 
		$page_data.='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" style="width:100%">
		<tr>
			<td class="text_b_w" style="text-align:center; width:$phy_col;">'.$colTitle.'</td>
			<td class="text_b_w" style="text-align:center; width:$w_cols;">Charges</td>
			<td class="text_b_w" style="text-align:center; width:$w_cols;">Payments</td>
			<td class="text_b_w" style="text-align:center; width:$w_cols;">Refund</td>
		</tr>';
		
		$arr=array();
		$arr[]=$colTitle;
		$arr[]="Charges";
		$arr[]="Payments";
		$arr[]="Refund";
		fputcsv($fp,$arr, ",","\"");
		
		$arrTotal = array();
		foreach($arrRefundData as $firstGrpId=>$arrData){
			$first_grp_name = $userNameArr[$firstGrpId];
			if($view_by=='facility'){
				$first_grp_name = $posFacilityArr[$firstGrpId];
			}
			$charges = $CLSReports->numberFormat($arrData['charges'],2);
			$payment = $CLSReports->numberFormat($arrData['payment'],2);
			$refund = $CLSReports->numberFormat($arrData['refund'],2);
			$page_data.='
			<tr class="data">
				<td style="text-align:left; width:'.$phy_col.';">'.$first_grp_name.'</td>
				<td style="text-align:right; width:$w_cols;">'.$charges.'</td>
				<td style="text-align:right; width:$w_cols;">'.$payment.'</td>
				<td style="text-align:right; width:$w_cols;">'.$refund.'</td>
			</tr>';
					
			$arr=array();
			$arr[]=$first_grp_name;
			$arr[]=$charges;
			$arr[]=$payment;
			$arr[]=$refund;
			fputcsv($fp,$arr, ",","\"");			
					
		$arrTotal['charges'][] = $arrData['charges'];
		$arrTotal['payment'][] = $arrData['payment'];
		$arrTotal['refund'][] = $arrData['refund'];
			
		}
		$tot_charges = $CLSReports->numberFormat(array_sum($arrTotal['charges']),2);
		$tot_payment = $CLSReports->numberFormat(array_sum($arrTotal['payment']),2);
		$tot_refund = $CLSReports->numberFormat(array_sum($arrTotal['refund']),2);
		$page_data.='
		<tr class="subtotal">
			<td style="text-align:right; width:'.$phy_col.';">Total</td>
			<td style="text-align:right; width:'.$w_cols.';">'.$tot_charges.'</td>
			<td style="text-align:right; width:'.$w_cols.';">'.$tot_payment.'</td>
			<td style="text-align:right; width:'.$w_cols.';">'.$tot_refund.'</td>
		</tr>
		</table>';
	}
	$pdfData ='
	<page backtop="9mm" backbottom="10mm">
	<page_footer>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>
		'.$page_header.'
	</page_header>
		'.$page_data.'
	</page>';
}
$csvFileData = $page_header.$page_data;
?>