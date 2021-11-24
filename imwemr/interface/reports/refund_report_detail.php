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
FILE : refundReportDetail.php
PURPOSE : REFUND REPORT DETAIL VIEW
ACCESS TYPE : INCLUDED
*/


$dataExists=0;
$arrGrandTot=array();

$showCurrencySymbol = showCurrency();

$page_header_val = '
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
        <tr class="rpt_headers">
            <td class="rptbx1" style="width:260px;">&nbsp;Refund Report : Detail</td>	
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
	</table>';
$file_name="refund_report.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');
$arr=array();	
$arr[]='Refund Report : Detail';
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

//POSTED AMOUNTS
if($view_by=='fac_n_oper'){
	
	if(count($ovr_pay_arr)){ 
		$dataExists=1;
		$countRes=0;
		$totalAmount_arr=array();
		$overPayment_arr=array();
		$totalPayAmount_arr=array();
		
		$arr=array();
		$arr[]="Posted Payment Refunds";
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]="Operator";
		$arr[]="Patient Name-ID";
		$arr[]="DOS";
		$arr[]="E.ID";
		$arr[]="Facility";
		$arr[]="CPT Code";
		$arr[]="Refund Date";
		$arr[]="Charges";
		$arr[]="Payment";
		$arr[]="Refund";
		$arr[]="Method";
		fputcsv($fp,$arr, ",","\"");
		
		foreach($ovr_pay_arr as $first_grp_id =>$first_grp_data){
			$sec_grp_totals=array();			
			$first_grp_title='Facility';
			$first_grp_name=$posFacilityArr[$first_grp_id];
			
			$pdfData2 .='<tr><td class="text_b_w" colspan="10" align="left">'.$first_grp_title.' : '.$first_grp_name.'</td></tr>';
			$csvFileData2 .='<tr><td class="text_b_w" colspan="10" align="left">'.$first_grp_title.' : '.$first_grp_name.'</td></tr>';			
			
			
			foreach($first_grp_data as $sec_grp_id =>$sec_grp_data){		
				$totalAmount_sub_arr=array();	
				$totalRefundAmt_sub_arr=array();	
				$totalPayAmount_sub_arr=array();		
				$sec_grp_name=$userNameArr[$sec_grp_id];
				
				$pdfData2 .='<tr><td class="text_b_w" colspan="10" align="left">Operator : '.$sec_grp_name.'</td></tr>';
				$csvFileData2 .='<tr><td class="text_b_w" colspan="10" align="left">Operator : '.$sec_grp_name.'</td></tr>';			
				foreach($sec_grp_data as $encounter_id =>$encounterDataArr){	
					$countRes++;
					$totalAmount= $amtPaid = $refundAmt = 0;
					$cptCodes='';
					
					$detailsArr=$encounterDataArr[0];
					
					$date_of_service = $detailsArr['date_of_service'];
					$posFacilityId = $detailsArr['facility_id'];
					$facility_name = $posFacilityArr[$posFacilityId];
					$patientName = core_name_format($detailsArr['lname'], $detailsArr['fname'], $detailsArr['mname']);
					$patientName .= " - ".$detailsArr['patient_id'];
					
					$arrCptCodes=array();
					$refundAmt=0;
					for($k=0; $k<sizeof($encounterDataArr); $k++){
						$chgdetid=$encounterDataArr[$k]['charge_list_detail_id'];
						$totalAmount+= $encounterDataArr[$k]['totalAmount'];
						$amtPaid+= $encounterDataArr[$k]['paidForProc'];
						
						$cpt_code_name=$arrRefundCPT[$chgdetid][$sec_grp_id];
						$arrCptCodes[$cpt_code_name]=$cpt_code_name;
						$refundDate = $arrRefundDate[$chgdetid][$sec_grp_id];
						$refundAmt+= $arrRefundAmt[$chgdetid][$sec_grp_id];
						$refundMethod = $arrRefundMethod[$chgdetid][$sec_grp_id];
					}
					
					$cptCodes = (sizeof($arrCptCodes)>0)? implode(', ',$arrCptCodes): '';
					
					$totalAmount_sub_arr[]=$totalAmount;
					$totalRefundAmt_sub_arr[]=$refundAmt;
					$totalPayAmount_sub_arr[]=$amtPaid;
								
					//--- PDF FILE DATA ----
					$pdfData2 .='<tr>
						<td class="text_10" bgcolor="#FFFFFF" width="162">'.$patientName.'</td>
						<td class="text_10" bgcolor="#FFFFFF" align="center" width="85">'.$date_of_service.'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="70" align="center">'.$encounter_id.'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="105" align="left">'.$facility_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="100" align="left">'.$cptCodes.'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$refundDate.'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right"> '.$CLSReports->numberFormat($totalAmount,2).'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right"> '.$CLSReports->numberFormat($amtPaid,2).'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($refundAmt,2).'</td>
						<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:center;">'.$refundMethod.'</td>
					</tr>';
					$csvFileData2 .='<tr>
						<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
						<td class="text_12" bgcolor="#FFFFFF" align="center" width="100">'.$date_of_service.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="70" align="center">'.$encounter_id.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$facility_name.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$cptCodes.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$refundDate.'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($totalAmount,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($amtPaid,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($refundAmt,2).'</td>
						<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:center;">'.$refundMethod.'</td>
					</tr>';	
					
					$arr=array();
					$arr[]=$sec_grp_name;
					$arr[]=$patientName;
					$arr[]=$date_of_service;
					$arr[]=$encounter_id;
					$arr[]=$facility_name;
					$arr[]=$cptCodes;
					$arr[]=$refundDate;
					$arr[]=$CLSReports->numberFormat($totalAmount,2);
					$arr[]=$CLSReports->numberFormat($amtPaid,2);
					$arr[]=$CLSReports->numberFormat($refundAmt,2);
					$arr[]=$refundMethod;
					fputcsv($fp,$arr, ",","\"");	
				}
				
				$sec_grp_totals['charges']+=array_sum($totalAmount_sub_arr);
				$sec_grp_totals['paidAmt']+=array_sum($totalPayAmount_sub_arr);
				$sec_grp_totals['refAmt']+=array_sum($totalRefundAmt_sub_arr);
				
				//OPERATOR TOTALS
				$pdfData2 .='
				<tr><td class="total-row" colspan="10"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" colspan="5"></td>
					<td class="text_10b" bgcolor="#FFFFFF" align="right">Operator Total:</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalAmount_sub_arr),2).'</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalPayAmount_sub_arr),2).'</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat(array_sum($totalRefundAmt_sub_arr),2).'</td>
					<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="10"></td></tr>';
				
				$csvFileData2 .='
				<tr><td class="total-row" colspan="10"></td></tr>	
				<tr>
					<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
					<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
					<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
					<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
					<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
					<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Operator Total:</td>
					<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalAmount_sub_arr),2).'</td>
					<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalPayAmount_sub_arr),2).'</td>
					<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat(array_sum($totalRefundAmt_sub_arr),2).'</td>
					<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="10"></td></tr>';					
			}
			

			$totalAmount_arr[]=$sec_grp_totals['charges'];
			$totalPayAmount_arr[]=$sec_grp_totals['paidAmt'];
			$totalRefundAmt_arr[]=$sec_grp_totals['refAmt'];

			//FACILITY TOTALS
			$pdfData2 .='
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" colspan="5"></td>
				<td class="text_10b" bgcolor="#FFFFFF" align="right">Facility Total:</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($sec_grp_totals['charges'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($sec_grp_totals['paidAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($sec_grp_totals['refAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>';
			
			$csvFileData2 .='
			<tr><td class="total-row" colspan="10"></td></tr>	
			<tr>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Facility Total:</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($sec_grp_totals['charges'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($sec_grp_totals['paidAmt'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($sec_grp_totals['refAmt'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>';	
		}
	
		$arrGrandTot['charges']+= array_sum($totalAmount_arr);
		$arrGrandTot['paidAmt']+= array_sum($totalPayAmount_arr);
		$arrGrandTot['refAmt']+= array_sum($totalRefundAmt_arr);
		
		//MAIN TOTAL
		$postedHTML ='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >				
		<tr><td class="text_b_w" colspan="10">Posted Payment Refunds</td></tr>
		<tr>
			<td class="text_b_w" width="200" style="text-align:left">Patient Name-ID</td>
			<td class="text_b_w" width="100" style="text-align:center">DOS</td>
			<td class="text_b_w" width="70" style="text-align:center">E.ID</td>
			<td class="text_b_w" width="90" style="text-align:center">Facility</td>
			<td class="text_b_w" width="90" style="text-align:center">CPT Code</td>
			<td class="text_b_w" width="90" style="text-align:center">Refund Date</td>
			<td class="text_b_w" width="120" style="text-align:right">Charges &nbsp;</td>
			<td class="text_b_w" width="120" style="text-align:right">Payment &nbsp;</td>
			<td class="text_b_w" width="120" style="text-align:right">Refund &nbsp;</td>
			<td class="text_b_w" width="120" style="text-align:center">Method</td>
		</tr>
		'.$csvFileData2.'
		<tr>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Posted Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalPayAmount_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat(array_sum($totalRefundAmt_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		</table>';
		
		$postedPDF= '
		<page backtop="20mm" backbottom="5mm">
		<page_footer>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>		
		<page_header>
			'.$page_header_val.'
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
				<tr><td class="text_b_w" colspan="10">Posted Payment Refunds</td></tr>
				<tr>
					<td class="text_b_w" width="162" style="text-align:left">Patient Name-ID</td>
					<td class="text_b_w" width="85" style="text-align:center">DOS</td>
					<td class="text_b_w" width="70" style="text-align:center">E.ID</td>
					<td class="text_b_w" width="105" style="text-align:center">Facility</td>
					<td class="text_b_w" width="100" style="text-align:center">CPT Code</td>
					<td class="text_b_w" width="80" style="text-align:center">Refund Date</td>
					<td class="text_b_w" width="100" style="text-align:right">Charges &nbsp;</td>
					<td class="text_b_w" width="100" style="text-align:right">Payment &nbsp;</td>
					<td class="text_b_w" width="120" style="text-align:right">Refund &nbsp;</td>
					<td class="text_b_w" width="100" style="text-align:center">Method</td>
				</tr>
			</table>
		</page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			'.$pdfData2.'
			<tr>
				<td class="text_12" bgcolor="#FFFFFF" colspan="5"></td>
				<td class="text_10b" bgcolor="#FFFFFF" align="right">Posted Total:</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalAmount_arr),2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalPayAmount_arr),2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat(array_sum($totalRefundAmt_arr),2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
		</table>
		</page>';
	}
	
}else{
	
	if(count($main_provider_arr)){ 
	$dataExists=1;
	$countRes=0;
	$totalAmount_arr=array();
	$overPayment_arr=array();
	$totalPayAmount_arr=array();
	
	$arr=array();
	$arr[]="Posted Payment Refunds";
	fputcsv($fp,$arr, ",","\"");
	$arr=array();
	$first_grp_title='Physician';
	if($view_by=='facility'){
		$first_grp_title='Facility';
	}
	$arr[]=$first_grp_title;
	$arr[]="Patient Name-ID";
	$arr[]="DOS";
	$arr[]="E.ID";
	$arr[]="Facility";
	$arr[]="CPT Code";
	$arr[]="Refund Date";
	$arr[]="Charges";
	$arr[]="Payment";
	$arr[]="Refund";
	$arr[]="Method";
	fputcsv($fp,$arr, ",","\"");
	
	foreach($main_provider_arr as $first_grp_id){
		$first_grp_name=$userNameArr[$first_grp_id];
		$first_grp_title='Physician';
		if($view_by=='facility'){
			$first_grp_name=$posFacilityArr[$first_grp_id];
			$first_grp_title='Facility';
		}
		
		$pdfData2 .='<tr>
						<td class="text_b_w" colspan="10" align="left">'.$first_grp_title.' : '.$first_grp_name.'</td>
					</tr>';
		$csvFileData2 .='<tr>
						<td class="text_b_w" colspan="10" align="left">'.$first_grp_title.' : '.$first_grp_name.'</td>
					</tr>';			
		$totalAmount_sub_arr=array();	
		$totalRefundAmt_sub_arr=array();	
		$totalPayAmount_sub_arr=array();		

		foreach($ovr_pay_arr[$first_grp_id] as $encounter_id =>$encounterDataArr){	
			$countRes++;
			$totalAmount= $amtPaid = $refundAmt = 0;
			$cptCodes='';
			
			$detailsArr=$encounterDataArr[0];
			
			$date_of_service = $detailsArr['date_of_service'];
			$posFacilityId = $detailsArr['facility_id'];
			$facility_name = $posFacilityArr[$posFacilityId];
			$patientName = core_name_format($detailsArr['lname'], $detailsArr['fname'], $detailsArr['mname']);
			
			$patientName .= " - ".$detailsArr['patient_id'];
			$cptCodes = implode(', ',$arrRefundCPT[$encounter_id]);
			$refundDate = $arrRefundDate[$encounter_id];
			
			for($k=0; $k<sizeof($encounterDataArr); $k++){
				$totalAmount+= $encounterDataArr[$k]['totalAmount'];
				$amtPaid+= $encounterDataArr[$k]['paidForProc'];
			}
			$refundAmt = $arrRefundAmt[$encounter_id];
			$refundMethod = $arrRefundMethod[$encounter_id];
			$totalAmount_sub_arr[]=$totalAmount;
			$totalRefundAmt_sub_arr[]=$refundAmt;
			$totalPayAmount_sub_arr[]=$amtPaid;
			
			$totalAmount_arr[]=$totalAmount;
			$totalRefundAmt_arr[]=$refundAmt;
			$totalPayAmount_arr[]=$amtPaid;
			
			//--- PDF FILE DATA ----
			$pdfData2 .='<tr>
				<td class="text_10" bgcolor="#FFFFFF" width="162">'.$patientName.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="center" width="85">'.$date_of_service.'</td>
				<td class="text_10" bgcolor="#FFFFFF" width="70" align="center">'.$encounter_id.'</td>
				<td class="text_10" bgcolor="#FFFFFF" width="105" align="left">'.$facility_name.'</td>
				<td class="text_10" bgcolor="#FFFFFF" width="100" align="left">'.$cptCodes.'</td>
				<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$refundDate.'</td>
				<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right"> '.$CLSReports->numberFormat($totalAmount,2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right"> '.$CLSReports->numberFormat($amtPaid,2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($refundAmt,2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:center;">'.$refundMethod.'</td>
			</tr>';
			$csvFileData2 .='<tr>
				<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="center" width="100">'.$date_of_service.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="70" align="center">'.$encounter_id.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$facility_name.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$cptCodes.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$refundDate.'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($totalAmount,2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($amtPaid,2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($refundAmt,2).'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:center;">'.$refundMethod.'</td>
			</tr>';

			
			$arr=array();
			$arr[]=$first_grp_name;
			$arr[]=$patientName;
			$arr[]=$date_of_service;
			$arr[]=$encounter_id;
			$arr[]=$facility_name;
			$arr[]=$cptCodes;
			$arr[]=$refundDate;
			$arr[]=$CLSReports->numberFormat($totalAmount,2);
			$arr[]=$CLSReports->numberFormat($amtPaid,2);
			$arr[]=$CLSReports->numberFormat($refundAmt,2);
			$arr[]=$refundMethod;
			fputcsv($fp,$arr, ",","\"");
			
		}
		
		$pdfData2 .='
		<tr><td class="total-row" colspan="10"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" colspan="5"></td>
			<td class="text_10b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalAmount_sub_arr),2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalPayAmount_sub_arr),2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat(array_sum($totalRefundAmt_sub_arr),2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>';
		
		$csvFileData2 .='
		<tr><td class="total-row" colspan="10"></td></tr>	
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
			<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Sub Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalAmount_sub_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalPayAmount_sub_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat(array_sum($totalRefundAmt_sub_arr),2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>';	
	}

	$arrGrandTot['charges']+= array_sum($totalAmount_arr);
	$arrGrandTot['paidAmt']+= array_sum($totalPayAmount_arr);
	$arrGrandTot['refAmt']+= array_sum($totalRefundAmt_arr);
	
	//MAIN TOTAL
	$postedHTML ='
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >				
	<tr><td class="text_b_w" colspan="10">Posted Payment Refunds</td></tr>
	<tr>
		<td class="text_b_w" width="200" style="text-align:left">Patient Name-ID</td>
		<td class="text_b_w" width="100" style="text-align:center">DOS</td>
		<td class="text_b_w" width="70" style="text-align:center">E.ID</td>
		<td class="text_b_w" width="90" style="text-align:center">Facility</td>
		<td class="text_b_w" width="90" style="text-align:center">CPT Code</td>
		<td class="text_b_w" width="90" style="text-align:center">Refund Date</td>
		<td class="text_b_w" width="120" style="text-align:right">Charges &nbsp;</td>
		<td class="text_b_w" width="120" style="text-align:right">Payment &nbsp;</td>
		<td class="text_b_w" width="120" style="text-align:right">Refund &nbsp;</td>
		<td class="text_b_w" width="120" style="text-align:center">Method</td>
	</tr>
	'.$csvFileData2.'
	<tr>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12" bgcolor="#FFFFFF" width="70"></td>
		<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Posted Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalAmount_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalPayAmount_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat(array_sum($totalRefundAmt_arr),2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	</table>';
	
	$postedPDF= '
	<page backtop="20mm" backbottom="5mm">
	<page_footer>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>		
	<page_header>
		'.$page_header_val.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr><td class="text_b_w" colspan="10">Posted Payment Refunds</td></tr>
			<tr>
				<td class="text_b_w" width="162" style="text-align:left">Patient Name-ID</td>
				<td class="text_b_w" width="85" style="text-align:center">DOS</td>
				<td class="text_b_w" width="70" style="text-align:center">E.ID</td>
				<td class="text_b_w" width="105" style="text-align:center">Facility</td>
				<td class="text_b_w" width="100" style="text-align:center">CPT Code</td>
				<td class="text_b_w" width="80" style="text-align:center">Refund Date</td>
				<td class="text_b_w" width="100" style="text-align:right">Charges &nbsp;</td>
				<td class="text_b_w" width="100" style="text-align:right">Payment &nbsp;</td>
				<td class="text_b_w" width="120" style="text-align:right">Refund &nbsp;</td>
				<td class="text_b_w" width="100" style="text-align:center">Method</td>
			</tr>
		</table>
	</page_header>
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
		'.$pdfData2.'
		<tr>
			<td class="text_12" bgcolor="#FFFFFF" colspan="5"></td>
			<td class="text_10b" bgcolor="#FFFFFF" align="right">Posted Total:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalAmount_arr),2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat(array_sum($totalPayAmount_arr),2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat(array_sum($totalRefundAmt_arr),2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
	</table>
	</page>';
	}

}


// CI/CO AND PRE PAYMENTS	
if($view_by=='fac_n_oper'){
	if(sizeof($arrMainOthers)>0){
	$arr=array();
	$arr[]="CI/CO and Pre-Payment Refunds";
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]="Facility";
	$arr[]="Operator";
	$arr[]="Patient Name-ID";
	$arr[]="Paid Date";
	$arr[]="Facility";
	$arr[]="Refund Date";
	$arr[]="Type";
	$arr[]="Payment";
	$arr[]="Refund";
	$arr[]="Method";
	fputcsv($fp,$arr, ",","\"");
	
		$dataExists=1;
		$pdfData2 = $csvFileData2 = '';
		$arrTot = array();
		foreach($arrMainOthers as $first_grp_id => $first_grp_data){
			$arrSubTot=array();
			$pdfData2 .='<tr><td class="text_b_w" colspan="10" align="left">Facility : '.$posFacilityArr[$first_grp_id].'</td></tr>';
			$csvFileData2 .='<tr><td class="text_b_w" colspan="10" align="left">Facility : '.$posFacilityArr[$first_grp_id].'</td></tr>';
	
			foreach($first_grp_data as $sec_grp_id => $patIds){
				$sec_grp_total=array();
				$pdfData2 .='<tr><td class="text_b_w" colspan="10" align="left">Operator : '.$userNameArr[$sec_grp_id].'</td></tr>';
				$csvFileData2 .='<tr><td class="text_b_w" colspan="10" align="left">Operator : '.$userNameArr[$sec_grp_id].'</td></tr>';
	
				foreach($patIds as $patId){
			
				$patientName='';
				// CI/CO
				foreach($arrCICORefunds[$first_grp_id][$sec_grp_id][$patId] as $cicoDetId => $facId){

					$patientName=core_name_format($arrPatDetail[$cicoDetId]['lname'], $arrPatDetail[$cicoDetId]['fname'], $arrPatDetail[$cicoDetId]['mname']);
					$patientName.= ' - '.$patId;
					
					$paidDate = $arrPatDetail[$cicoDetId]['pay_date'];
					$paidAmt =  $arrPatDetail[$cicoDetId][$sec_grp_id]['pay_amt'];
					
					if(!$tempArrCICOId[$cicoDetId]){
						$arrSubTot['paidAmt']+= $paidAmt;
						$sec_grp_total['paidAmt']+= $paidAmt;
						$tempArrCICOId[$cicoDetId]=$cicoDetId;
					}
					
					foreach($arrCICORefundsDet[$cicoDetId][$sec_grp_id] as $sno => $patDetails){

						$arrSubTot['refAmt']+= $patDetails['ref_amt'];
						$sec_grp_total['refAmt']+= $patDetails['ref_amt'];
						
						//PDF
						$pdfData2 .='<tr>
							<td class="text_10" bgcolor="#FFFFFF" width="162">'.$patientName.'</td>
							<td class="text_10" bgcolor="#FFFFFF" align="center" width="85">'.$paidDate.'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="70" align="center"></td>
							<td class="text_10" bgcolor="#FFFFFF" width="105" align="left">'.$posFacilityArr[$facId].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" align="left"></td>
							<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$patDetails['ref_date'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:left">Check In/Out</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right">'.$CLSReports->numberFormat($paidAmt,2).'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:center;">'.$patDetails['method'].'</td>
						</tr>';
						//PAGE
						$csvFileData2 .='<tr>
							<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
							<td class="text_12" bgcolor="#FFFFFF" align="center" width="100">'.$paidDate.'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="70" align="center"></td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$posFacilityArr[$facId].'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left"></td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$patDetails['ref_date'].'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:left">Check In/Out</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($paidAmt,2).'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:center;">'.$patDetails['method'].'</td>
						</tr>';	
					}
				}
				unset($arrCICORefunds[$first_grp_id][$sec_grp_id]);
	
				// PRE-PAYMENTS
				foreach($arrPMTRefunds[$first_grp_id][$sec_grp_id][$patId] as $pmtId => $facId){
					
					if($patientName==''){
						$patientName=core_name_format($arrPatDetail[$pmtId]['lname'], $arrPatDetail[$pmtId]['fname'], $arrPatDetail[$pmtId]['mname']);
						$patientName.= ' - '.$patId;
					}
					
					$paidDate = $arrPatDetail[$pmtId]['pay_date'];
					$paidAmt =  $arrPatDetail[$pmtId][$sec_grp_id]['pay_amt'];

					if(!$tempArrPrePayId[$pmtId]){					
						$arrSubTot['paidAmt']+= $paidAmt;
						$sec_grp_total['paidAmt']+= $paidAmt;
						$tempArrPrePayId[$pmtId]=$pmtId;
					}
					
					foreach($arrPMTRefundsDet[$pmtId][$sec_grp_id] as $sno => $patDetails){
						$arrSubTot['refAmt']+= $patDetails['ref_amt'];
						$sec_grp_total['refAmt']+= $patDetails['ref_amt'];
						
						//PDF
						$pdfData2 .='<tr>
							<td class="text_10" bgcolor="#FFFFFF" width="162">'.$patientName.'</td>
							<td class="text_10" bgcolor="#FFFFFF" align="center" width="85">'.$paidDate.'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="70" align="center"></td>
							<td class="text_10" bgcolor="#FFFFFF" width="105" align="left">'.$posFacilityArr[$facId].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" align="left"></td>
							<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$patDetails['ref_date'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:left">Pre-Payment</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right">'.$CLSReports->numberFormat($paidAmt,2).'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:center;">'.$patDetails['method'].'</td>
						</tr>';
						//PAGE
						$csvFileData2 .='<tr>
							<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
							<td class="text_12" bgcolor="#FFFFFF" align="center" width="100">'.$paidDate.'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="70" align="center"></td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$posFacilityArr[$facId].'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left"></td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$patDetails['ref_date'].'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:left">Pre-Payment</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($paidAmt,2).'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:center;">'.$patDetails['method'].'</td>
						</tr>';	
					}
				}
				unset($arrPMTRefunds[$phyId][$patId]);
			}
			
			//OPERATOR TOTAL
			$pdfData2 .='
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" colspan="5"></td>
				<td class="text_10b" bgcolor="#FFFFFF" align="right">Operator Total:</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($sec_grp_total['paidAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($sec_grp_total['refAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>';
			
			$csvFileData2 .='
			<tr><td class="total-row" colspan="10"></td></tr>	
			<tr>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Operator Total:</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> </td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right">'.$CLSReports->numberFormat($sec_grp_total['paidAmt'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($sec_grp_total['refAmt'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>';			
			
			}
			
			$arrTot['paidAmt']+= $arrSubTot['paidAmt'];
			$arrTot['refAmt']+= $arrSubTot['refAmt'];
			
			//SUB TOTAL
			$pdfData2 .='
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" colspan="5"></td>
				<td class="text_10b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrSubTot['paidAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrSubTot['refAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>';
			
			$csvFileData2 .='
			<tr><td class="total-row" colspan="10"></td></tr>	
			<tr>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Sub Total:</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> </td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right">'.$CLSReports->numberFormat($arrSubTot['paidAmt'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrSubTot['refAmt'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>';			
		}
	
		$arrGrandTot['paidAmt']+= $arrTot['paidAmt'];
		$arrGrandTot['refAmt']+= $arrTot['refAmt'];
		
		// MAIN TOTAL
		$otherHTML=' 
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
		<tr><td class="text_b_w" colspan="10">CI/CO and Pre-Payment Refunds</td></tr>
		<tr>
			<td class="text_b_w" width="180" style="text-align:left">Patient Name-ID</td>
			<td class="text_b_w" width="90" style="text-align:center">Paid Date</td>
			<td class="text_b_w" width="70" style="text-align:center"></td>
			<td class="text_b_w" width="90" style="text-align:center">Facility</td>
			<td class="text_b_w" width="90" style="text-align:center"></td>
			<td class="text_b_w" width="90" style="text-align:center">Refund Date</td>
			<td class="text_b_w" width="140" style="text-align:left">Type</td>
			<td class="text_b_w" width="140" style="text-align:right">Payment &nbsp;</td>
			<td class="text_b_w" width="140" style="text-align:right">Refund &nbsp;</td>
			<td class="text_b_w" width="140" style="text-align:center">Method</td>
		</tr>
		'.$csvFileData2.'
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">CI/CO & Pre-Payments Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTot['paidAmt'],2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrTot['refAmt'],2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
		</table>';
			
		$otherPDF= '
		<page backtop="20mm" backbottom="5mm">
		<page_footer>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>		
		<page_header>
			'.$page_header_val.'
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
				<tr><td class="text_b_w" colspan="10">CI/CO and Pre-Payment Refunds</td></tr>
				<tr>
					<td class="text_b_w" width="162" style="text-align:left">Patient Name-ID</td>
					<td class="text_b_w" width="85" style="text-align:center">Paid Date</td>
					<td class="text_b_w" width="70" style="text-align:center"></td>
					<td class="text_b_w" width="105" style="text-align:center">Facility</td>
					<td class="text_b_w" width="100" style="text-align:center"></td>
					<td class="text_b_w" width="80" style="text-align:center">Refund Date</td>
					<td class="text_b_w" width="100" style="text-align:left">Type</td>
					<td class="text_b_w" width="100" style="text-align:right">Payment &nbsp;</td>
					<td class="text_b_w" width="120" style="text-align:right">Refund &nbsp;</td>
					<td class="text_b_w" width="100" style="text-align:center">Method</td>
				</tr>
			</table>
		</page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			'.$pdfData2.'
			<tr>
				<td class="text_12" bgcolor="#FFFFFF"  style="text-align:right" colspan="6">CI/CO & Pre-Payment Total : </td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTot['paidAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrTot['refAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
		</table>
		</page>';
	}
	

}else{
	if(sizeof($arrMainOthers)>0){
		$dataExists=1;
		$pdfData2 = $csvFileData2 = '';
		$arrTot = array();
		$arr=array();
		$arr[]="CI/CO and Pre-Payment Refunds";
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]="Physician Name";
		$arr[]="Patient Name-ID";
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
	
			$pdfData2 .='<tr><td class="text_b_w" colspan="10" align="left">Physician Name : '.$userNameArr[$phyId].'</td></tr>';
			$csvFileData2 .='<tr><td class="text_b_w" colspan="10" align="left">Physician Name : '.$userNameArr[$phyId].'</td></tr>';
		
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
						
						//PDF
						$pdfData2 .='<tr>
							<td class="text_10" bgcolor="#FFFFFF" width="162">'.$patientName.'</td>
							<td class="text_10" bgcolor="#FFFFFF" align="center" width="85">'.$paidDate.'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="70" align="center"></td>
							<td class="text_10" bgcolor="#FFFFFF" width="105" align="left">'.$posFacilityArr[$facId].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" align="left"></td>
							<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$patDetails['ref_date'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:left">Check In/Out</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right">'.$CLSReports->numberFormat($paidAmt,2).'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:center;">'.$patDetails['method'].'</td>
						</tr>';
						//PAGE
						$csvFileData2 .='<tr>
							<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
							<td class="text_12" bgcolor="#FFFFFF" align="center" width="100">'.$paidDate.'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="70" align="center"></td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$posFacilityArr[$facId].'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left"></td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$patDetails['ref_date'].'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:left">Check In/Out</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($paidAmt,2).'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:center;">'.$patDetails['method'].'</td>
						</tr>';	
						
						$arr=array();
						$arr[]=$userNameArr[$phyId];
						$arr[]=$patientName;
						$arr[]=$paidDate;
						$arr[]=$$posFacilityArr[$facId];
						$arr[]=$patDetails['ref_date'];
						$arr[]="Check In/Out";
						$arr[]=$CLSReports->numberFormat($paidAmt,2);
						$arr[]=$CLSReports->numberFormat($patDetails['ref_amt'],2);
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
						$patientName.= ' - '.$patId;
					}
					
					$paidDate = $arrPatDetail[$pmtId]['pay_date'];
					$paidAmt =  $arrPatDetail[$pmtId]['pay_amt'];
					$arrSubTot['paidAmt']+= $paidAmt;
					
					foreach($arrPMTRefundsDet[$pmtId] as $sno => $patDetails){
						$arrSubTot['refAmt']+= $patDetails['ref_amt'];
						
						//PDF
						$pdfData2 .='<tr>
							<td class="text_10" bgcolor="#FFFFFF" width="162">'.$patientName.'</td>
							<td class="text_10" bgcolor="#FFFFFF" align="center" width="85">'.$paidDate.'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="70" align="center"></td>
							<td class="text_10" bgcolor="#FFFFFF" width="105" align="left">'.$posFacilityArr[$facId].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" align="left"></td>
							<td class="text_10" bgcolor="#FFFFFF" width="80" align="center">'.$patDetails['ref_date'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:left">Pre-Payment</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right">'.$CLSReports->numberFormat($paidAmt,2).'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
							<td class="text_10" bgcolor="#FFFFFF" width="100" style="text-align:center;">'.$patDetails['method'].'</td>
						</tr>';
						//PAGE
						$csvFileData2 .='<tr>
							<td class="text_12" bgcolor="#FFFFFF" width="200">'.$patientName.'</td>
							<td class="text_12" bgcolor="#FFFFFF" align="center" width="100">'.$paidDate.'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="70" align="center"></td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left">'.$posFacilityArr[$facId].'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="left"></td>
							<td class="text_12" bgcolor="#FFFFFF" width="100" align="center">'.$patDetails['ref_date'].'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:left">Pre-Payment</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right"> '.$CLSReports->numberFormat($paidAmt,2).'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
							<td class="text_12" bgcolor="#FFFFFF" width="120" style="text-align:center;">'.$patDetails['method'].'</td>
						</tr>';	
						
						$arr=array();
						$arr[]=$userNameArr[$phyId];
						$arr[]=$patientName;
						$arr[]=$paidDate;
						$arr[]=$$posFacilityArr[$facId];
						$arr[]=$patDetails['ref_date'];
						$arr[]="Pre-Payment";
						$arr[]=$CLSReports->numberFormat($paidAmt,2);
						$arr[]=$CLSReports->numberFormat($patDetails['ref_amt'],2);
						$arr[]=$patDetails['method'];
						fputcsv($fp,$arr, ",","\"");
					}
				}
				unset($arrPMTRefunds[$phyId][$patId]);
			}
			
			$arrTot['paidAmt']+= $arrSubTot['paidAmt'];
			$arrTot['refAmt']+= $arrSubTot['refAmt'];
			
			//SUB TOTAL
			$pdfData2 .='
			<tr><td class="total-row" colspan="10"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" colspan="5"></td>
				<td class="text_10b" bgcolor="#FFFFFF" align="right">Sub Total:</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrSubTot['paidAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrSubTot['refAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>';
			
			$csvFileData2 .='
			<tr><td class="total-row" colspan="10"></td></tr>	
			<tr>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="70"></td>
				<td class="text_12b" bgcolor="#FFFFFF" width="100" align="right">Sub Total:</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right"> </td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right">'.$CLSReports->numberFormat($arrSubTot['paidAmt'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrSubTot['refAmt'],2).'</td>
				<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>';			
		}
	
		$arrGrandTot['paidAmt']+= $arrTot['paidAmt'];
		$arrGrandTot['refAmt']+= $arrTot['refAmt'];
		
		// MAIN TOTAL
		$otherHTML=' 
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
		<tr><td class="text_b_w" colspan="10">CI/CO and Pre-Payment Refunds</td></tr>
		<tr>
			<td class="text_b_w" width="180" style="text-align:left">Patient Name-ID</td>
			<td class="text_b_w" width="90" style="text-align:center">Paid Date</td>
			<td class="text_b_w" width="70" style="text-align:center"></td>
			<td class="text_b_w" width="90" style="text-align:center">Facility</td>
			<td class="text_b_w" width="90" style="text-align:center"></td>
			<td class="text_b_w" width="90" style="text-align:center">Refund Date</td>
			<td class="text_b_w" width="140" style="text-align:left">Type</td>
			<td class="text_b_w" width="140" style="text-align:right">Payment &nbsp;</td>
			<td class="text_b_w" width="140" style="text-align:right">Refund &nbsp;</td>
			<td class="text_b_w" width="140" style="text-align:center">Method</td>
		</tr>
		'.$csvFileData2.'
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">CI/CO & Pre-Payments Total:</td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"></td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTot['paidAmt'],2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrTot['refAmt'],2).'</td>
			<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
		</table>';
			
		$otherPDF= '
		<page backtop="20mm" backbottom="5mm">
		<page_footer>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>		
		<page_header>
			'.$page_header_val.'
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
				<tr><td class="text_b_w" colspan="10">CI/CO and Pre-Payment Refunds</td></tr>
				<tr>
					<td class="text_b_w" width="162" style="text-align:left">Patient Name-ID</td>
					<td class="text_b_w" width="85" style="text-align:center">Paid Date</td>
					<td class="text_b_w" width="70" style="text-align:center"></td>
					<td class="text_b_w" width="105" style="text-align:center">Facility</td>
					<td class="text_b_w" width="100" style="text-align:center"></td>
					<td class="text_b_w" width="80" style="text-align:center">Refund Date</td>
					<td class="text_b_w" width="100" style="text-align:left">Type</td>
					<td class="text_b_w" width="100" style="text-align:right">Payment &nbsp;</td>
					<td class="text_b_w" width="120" style="text-align:right">Refund &nbsp;</td>
					<td class="text_b_w" width="100" style="text-align:center">Method</td>
				</tr>
			</table>
		</page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			'.$pdfData2.'
			<tr>
				<td class="text_12" bgcolor="#FFFFFF"  style="text-align:right" colspan="6">CI/CO & Pre-Payment Total : </td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTot['paidAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrTot['refAmt'],2).'</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="10"></td></tr>
		</table>
		</page>';
	}	
}


//GRAND TOTAL
if($dataExists==1){

	//PAGE
	$csvFileData = 
	$page_header_val.
	$postedHTML.
	$otherHTML.
	'<br>
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
	<tr>
        <td class="text_b_w" bgcolor="#FFFFFF" width="200"></td>
        <td class="text_b_w" bgcolor="#FFFFFF" align="center" width="100"></td>
        <td class="text_b_w" bgcolor="#FFFFFF" width="70" align="center"></td>
        <td class="text_b_w" bgcolor="#FFFFFF" width="100" align="left"></td>
        <td class="text_b_w" bgcolor="#FFFFFF" width="100" align="left"></td>
        <td class="text_b_w" bgcolor="#FFFFFF" width="100" align="center"></td>
        <td class="text_b_w" bgcolor="#FFFFFF" width="120" style="text-align:right">Charges &nbsp;</td>
        <td class="text_b_w" bgcolor="#FFFFFF" width="120" style="text-align:right">Payment &nbsp;</td>
        <td class="text_b_w" bgcolor="#FFFFFF" width="120" style="text-align:right;padding-right:10px;">Refund &nbsp;</td>
        <td class="text_b_w" bgcolor="#FFFFFF" width="120" style="text-align:center;"></td>
	</tr>
	<tr><td class="total-row" colspan="10"></td></tr>	
	<tr>
		<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right">Grand Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrGrandTot['charges'],2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrGrandTot['paidAmt'],2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrGrandTot['refAmt'],2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="10"></td></tr>
	</table>';
		
	//PDF
	$pdfData =
	$postedPDF.
	$otherPDF.
	'<table style="border-collapse:separate; border:none; width:100%"  bgcolor="#FFF3E8">
		<tr>
			<td class="text_b_w" width="162" style="text-align:left"></td>
			<td class="text_b_w" width="85" style="text-align:center"></td>
			<td class="text_b_w" width="70" style="text-align:center"></td>
			<td class="text_b_w" width="105" style="text-align:center"></td>
			<td class="text_b_w" width="100" style="text-align:center"></td>
			<td class="text_b_w" width="80" style="text-align:center"></td>
			<td class="text_b_w" width="100" style="text-align:right">Charges &nbsp;</td>
			<td class="text_b_w" width="100" style="text-align:right">Payment &nbsp;</td>
			<td class="text_b_w" width="120" style="text-align:right">Refund &nbsp;</td>
			<td class="text_b_w" width="100" style="text-align:center"></td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:left"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center"></td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center">Grand Total:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrGrandTot['charges'],2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrGrandTot['paidAmt'],2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrGrandTot['refAmt'],2).'</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="10"></td></tr>
	</table>';
}
?>