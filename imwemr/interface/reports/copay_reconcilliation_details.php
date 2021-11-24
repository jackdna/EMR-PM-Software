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
FILE : COPAY_RECONCILLATION_DETAILS.PHP
PURPOSE :  COPAY DETAIL REPORT
ACCESS TYPE : DIRECT
*/
//--- START FACILITY LOOP ----
$dataExists=false;

$showCurrencySymbol = showCurrency();

//pre(array_unique($grpoperator_ids));die();
$operater_name = array();
$sel_opr_rs = imw_query("select fname,lname,id from users");
while ($row_opr = imw_fetch_array($sel_opr_rs)) {
    $id = $row_opr['id'];
    if($row_opr['lname'] == "" && $row_opr['fname'] == "") {
        $operater_name[$row_opr['id']] = "Not Defined";
    } else {
        $operater_name[$row_opr['id']] = core_name_format($row_opr['lname'], $row_opr['fname'], $row_opr['mname']);
    }
}
// COLLECTED COPAY
unset($cioData);

//MAKING OUTPUT DATA
$file_name="copay_reconciliation_report.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');
$arr=array();
$arr[]="Copay Reconciliation Report";
$arr[]="Appt Date: ".$Start_date.' To '.$End_date;
$arr[]="Operator: ".$OperatorSelected;
$arr[]="Created by: ".$op_name.' on '.$curDate;
fputcsv($fp,$arr, ",","\"");
$content_part='';
$totInsCopay= $totCopayCollected =0;

if($copay_not_collected==0)
{
	if(sizeof($arrFinalData)>0){
		$arr=array();
		$arr[]="Copay Collected Records";
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]="Operator";
		$arr[]="Appt Date";				
		$arr[]="Appt Time";
		$arr[]="CI Time";
		$arr[]="CO Time";
		$arr[]="Patient Name - ID";
		$arr[]="Pri Ins/Copay";
		$arr[]="Sec Ins/Copay";
		$arr[]="Copay Collected";
		$arr[]="Payment Mode";
		$arr[]="CI Op";
		$arr[]="CO Op";
		fputcsv($fp,$arr, ",","\"");
		$dataExists=true;
		$case_ids=array();
		$grpoperator_ids=array_unique($grpoperator_ids);
		foreach($grpoperator_ids as $id) {
			(!empty($arrFinalData[$id])) ? $content_part .= '<tr><td class="text_b_w" colspan="11">&nbsp;Operator - '.$operater_name[$id].'</td></tr>':'';
			foreach($arrFinalData[$id] as $schId => $cioData){
				$rowTot=0;
				
				foreach($cioData as $pid => $cioDetail){
					$pName = explode('~', $cioDetail['pat_name']);

					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					
					if(!in_array($cioDetail['case_id'],$case_ids)) {
						$case_ids[] = $cioDetail['case_id'];
						$totPriInsCopay+=$cioDetail['pri_ins_copay'];
						$totSecInsCopay+=$cioDetail['sec_ins_copay'];
						$totCopayCollected+=$cioDetail['copay_collected'];
					}
					
					$fontColor = '#000';
					$title='';
					if($arrCicoRefund[$schId]>0){
						$cioDetail['copay_collected']-= $arrCicoRefund[$schId];
						$fontColor = '#FF0000';
						$title = 'title="Refund : '.number_format($arrCicoRefund[$schId],2).'"';
						$totCopayCollected-= $arrCicoRefund[$schId];
					}

					$content_part .= '
					<tr>
						<td class="text_10">'.$cioDetail['created_date'].'</td>
						<td class="text_10">'.getMainAmPmTime($cioDetail['sa_app_starttime']).'</td>
						<td class="text_10">'.getMainAmPmTime($cioDetail['ci_created_time']).'</td>
						<td class="text_10">'.getMainAmPmTime($cioDetail['co_created_time']).'</td>
						<td class="text_10">&nbsp;'.$patient_name.'</td>
						<td class="text_10" style="text-align:right;">'.$cioDetail['pri_ins_comp'].$showCurrencySymbol.number_format($cioDetail['pri_ins_copay'],2).'&nbsp;</td>
						<td class="text_10" style="text-align:right;">'.$cioDetail['sec_ins_comp'].$showCurrencySymbol.number_format($cioDetail['sec_ins_copay'],2).'&nbsp;</td>
						<td class="text_10" style="text-align:right;">'.$showCurrencySymbol.number_format($cioDetail['copay_collected'],2).'&nbsp;</td>
						<td class="text_10">'.$cioDetail['payment_method'].'</td>
						<td class="text_10">&nbsp;'.$providerNameArr[$cioDetail['checkin_by']].'</td>
						<td class="text_10">&nbsp;'.$providerNameArr[$cioDetail['checkout_by']].'</td>
					</tr>';	
					
					$arr=array();
					$arr[]=$operater_name[$id];
					$arr[]=$cioDetail['created_date'];				
					$arr[]=getMainAmPmTime($cioDetail['sa_app_starttime']);
					$arr[]=getMainAmPmTime($cioDetail['ci_created_time']);
					$arr[]=getMainAmPmTime($cioDetail['co_created_time']);
					$arr[]=$patient_name;
					$arr[]=$cioDetail['pri_ins_comp'].$showCurrencySymbol.number_format($cioDetail['pri_ins_copay'],2);
					$arr[]=$cioDetail['sec_ins_comp'].$showCurrencySymbol.number_format($cioDetail['sec_ins_copay'],2);
					$arr[]=$showCurrencySymbol.number_format($cioDetail['copay_collected'],2);
					$arr[]=$cioDetail['payment_method'];
					$arr[]=$providerNameArr[$cioDetail['checkin_by']];
					$arr[]=$providerNameArr[$cioDetail['checkout_by']];
					fputcsv($fp,$arr, ",","\"");
					
				}
			}
		}
		// TOTAL
		$collected_html .=' 
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
		<tr id="heading_orange"><td class="text_b_w" colspan="11">Copay Collected Records</td></tr>
		<tr>
			<td class="text_b_w" style="width:80px;">Appt Date</td>
			<td class="text_b_w" style="width:80px;">Appt Time</td>
			<td class="text_b_w" style="width:80px;">CI Time</td>
			<td class="text_b_w" style="width:80px;">CO Time</td>
			<td class="text_b_w" style="width:150px;">&nbsp;Patient Name - Id</td>
			<td class="text_b_w" style="width:80px;text-align:right;">Pri Ins/Copay&nbsp;</td>
			<td class="text_b_w" style="width:80px;text-align:right;">Sec Ins/Copay&nbsp;</td>
			<td class="text_b_w" style="width:100px;text-align:right;">Copay Collected&nbsp;</td>
			<td class="text_b_w" style="width:90px;">&nbsp;Payment Mode</td>
			<td class="text_b_w" style="width:50px;">&nbsp;CI Op</td>
			<td class="text_b_w" style="width:50px;">&nbsp;CO Op</td>
		</tr>'
		.$content_part.'
		<tr><td style="height:1px; background:green;" colspan="11"></td></tr>
		<tr>
			<td class="text_10b">&nbsp;</td>
			<td class="text_10b">&nbsp;</td>
			<td class="text_10b">&nbsp;</td>
			<td class="text_10b">&nbsp;</td>
			<td class="text_10b" style="text-align:right;">Total&nbsp;:</td>
			<td class="text_10b" style="text-align:right;">'.$showCurrencySymbol.number_format($totPriInsCopay,2).'&nbsp;</td>
			<td class="text_10b" style="text-align:right;">'.$showCurrencySymbol.number_format($totSecInsCopay,2).'&nbsp;</td>
			<td class="text_10b" style="text-align:right;">'.$showCurrencySymbol.number_format($totCopayCollected,2).'&nbsp;</td>
			<td class="text_10b" colspan="2">&nbsp;</td>
		</tr>
		<tr><td style="height:1px; background:green;" colspan="11"></td></tr>
		</table>';		
	}
}

// NOT COLLECTED COPAY
unset($cioData);
$content_part='';
$totInsCopay= 0;
$totPriInsCopay= 0;
$totSecInsCopay= 0;
if(sizeof($arrFinalDataNotCollected)>0){
	$dataExists=true;
    $case_ids=array();
	$arr=array();
	$arr[]="Copay Not Collected Records";
	fputcsv($fp,$arr, ",","\"");
	$arr=array();
	$arr[]="Operator";
	$arr[]="Appt Date";				
	$arr[]="Appt Time";
	$arr[]="CI Time";
	$arr[]="CO Time";
	$arr[]="Patient Name - ID";
	$arr[]="Pri Ins/Copay";
	$arr[]="Sec Ins/Copay";
	$arr[]="Copay Collected";
	$arr[]="CI Op";
	$arr[]="CO Op";
	fputcsv($fp,$arr, ",","\"");
	$grpoperator_ids=array_unique($grpoperator_ids);
    foreach($grpoperator_ids as $id) {
        (!empty($arrFinalDataNotCollected[$id])) ? $content_part .= '<tr><td class="text_b_w" colspan="10">&nbsp;Operator - '.$operater_name[$id].'</td></tr>':'';
        foreach($arrFinalDataNotCollected[$id] as $schId => $cioData){
            $rowTot=0;
            
            foreach($cioData as $pid => $cioDetail){
                $pName = explode('~', $cioDetail['pat_name']);

                $patient_name_arr = array();
                $patient_name_arr["LAST_NAME"] = $pName[3];
                $patient_name_arr["FIRST_NAME"] = $pName[1];
                $patient_name_arr["MIDDLE_NAME"] = $pName[2];		
                $patient_name = changeNameFormat($patient_name_arr);
                $patient_name.= ' - '.$pName[0];
                if(!in_array($cioDetail['case_id'],$case_ids)) {
                    $case_ids[] = $cioDetail['case_id'];
                    $totPriInsCopay+=$cioDetail['pri_ins_copay'];
                    $totSecInsCopay+=$cioDetail['sec_ins_copay'];
                    $totInsCopay+=$cioDetail['ins_copay'];
                }
                $content_part .= '
                <tr>
                    <td class="text_10">'.$cioDetail['created_date'].'</td>
                    <td class="text_10">'.getMainAmPmTime($cioDetail['sa_app_starttime']).'</td>
                    <td class="text_10">'.getMainAmPmTime($cioDetail['ci_created_time']).'</td>
                    <td class="text_10">'.getMainAmPmTime($cioDetail['co_created_time']).'</td>
                    <td class="text_10">&nbsp;'.$patient_name.'</td>
                    <td class="text_10" style="text-align:right;">'.$cioDetail['pri_ins_comp'].$showCurrencySymbol.number_format($cioDetail['pri_ins_copay'],2).'&nbsp;</td>
                    <td class="text_10" style="text-align:right;">'.$cioDetail['sec_ins_comp'].$showCurrencySymbol.number_format($cioDetail['sec_ins_copay'],2).'&nbsp;</td>
                    <td class="text_10">&nbsp;'.$providerNameArr[$cioDetail['checkin_by']].'</td>
                    <td class="text_10">&nbsp;'.$providerNameArr[$cioDetail['checkout_by']].'</td>
                </tr>';
				$arr=array();
				$arr[]=$operater_name[$id];
				$arr[]=$cioDetail['created_date'];				
				$arr[]=getMainAmPmTime($cioDetail['sa_app_starttime']);
				$arr[]=getMainAmPmTime($cioDetail['ci_created_time']);
				$arr[]=getMainAmPmTime($cioDetail['co_created_time']);
				$arr[]=$patient_name;
				$arr[]=$cioDetail['pri_ins_comp'].$showCurrencySymbol.number_format($cioDetail['pri_ins_copay'],2);
				$arr[]=$cioDetail['sec_ins_comp'].$showCurrencySymbol.number_format($cioDetail['sec_ins_copay'],2);
				$arr[]=$showCurrencySymbol.number_format($cioDetail['copay_collected'],2);
				$arr[]=$providerNameArr[$cioDetail['checkin_by']];
				$arr[]=$providerNameArr[$cioDetail['checkout_by']];
				fputcsv($fp,$arr, ",","\"");
            }
        }
    }
	
	// TOTAL
	$not_collected_html .=' 
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
	<tr id="heading_orange"><td class="text_b_w" colspan="9">Copay Not Collected Records</td></tr>
	<tr>
		<td class="text_b_w" style="width:90px;">Appt Date</td>
		<td class="text_b_w" style="width:90px;">Appt Time</td>
		<td class="text_b_w" style="width:90px;">CI Time</td>
		<td class="text_b_w" style="width:90px;">CO Time</td>
		<td class="text_b_w" style="width:190px;">&nbsp;Patient Name - Id</td>
		<td class="text_b_w" style="width:120px;text-align:right;">Pri Ins/Copay&nbsp;</td>
		<td class="text_b_w" style="width:120px;text-align:right;">Sec Ins/Copay&nbsp;</td>
		<td class="text_b_w" style="width:80px;">&nbsp;CI Op</td>
		<td class="text_b_w" style="width:80px;">&nbsp;CO Op</td>
	</tr>'
	.$content_part.'
	<tr><td style="height:1px; background:green;" colspan="9"></td></tr>
	<tr>
        <td class="text_10b">&nbsp;</td>
        <td class="text_10b">&nbsp;</td>
        <td class="text_10b">&nbsp;</td>
        <td class="text_10b">&nbsp;</td>
		<td class="text_10b" style="text-align:right;">Total&nbsp;:</td>
		<td class="text_10b" style="text-align:right;">'.$showCurrencySymbol.number_format($totPriInsCopay,2).'&nbsp;</td>
		<td class="text_10b" style="text-align:right;">'.$showCurrencySymbol.number_format($totSecInsCopay,2).'&nbsp;</td>
		<td class="text_10b" style="text-align:right;" colspan="2">&nbsp;</td>
	</tr>
	<tr><td style="height:1px; background:green;" colspan="9"></td></tr>
	</table>';		
}

// NO COPAY, SELF PAY, NO CHECK IN/OUT, CANCELLED, NO SHOW
unset($cioData);
$content_part='';
$totInsCopay= 0;
if($copay_not_collected==0 || empty($str_exclude_procedures)===false || empty($str_exclude_status)===false)
{
	if(sizeof($arrFinalOtherData)>0){
		$arr=array();
		$arr[]="Other Status Records";
		fputcsv($fp,$arr, ",","\"");
		$arr=array();
		$arr[]="Operator";
		$arr[]="Appt Date";				
		$arr[]="Appt Time";
		$arr[]="Patient Name - ID";
		$arr[]="Status";
		fputcsv($fp,$arr, ",","\"");
		$case_ids=array();
		$dataExists=true;
		$grpoperator_ids=array_unique($grpoperator_ids);
		foreach($grpoperator_ids as $id) {
			(!empty($arrFinalOtherData[$id])) ? $content_part .= '<tr><td class="text_b_w" colspan="10">&nbsp;Operator - '.$operater_name[$id].'</td></tr>':'';
			foreach($arrFinalOtherData[$id] as $schId => $cioData){
				$rowTot=0;

				foreach($cioData as $pid => $cioDetail){
					$pName = explode('~', $cioDetail['pat_name']);

					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					if(!in_array($cioDetail['case_id'],$case_ids)) {
						$case_ids[] = $cioDetail['case_id'];
						$totInsCopay+=$cioDetail['ins_copay'];
					}
					$content_part .= '
					<tr>
						<td class="text_10">'.$cioDetail['created_date'].'</td>
						<td class="text_10">'.getMainAmPmTime($cioDetail['sa_app_starttime']).'</td>
						<td class="text_10">&nbsp;'.$patient_name.'</td>
						<td class="text_10">'.$cioDetail['status'].'</td>
						<td class="text_10"></td>
					</tr>';	
					$arr=array();
					$arr[]=$operater_name[$id];
					$arr[]=$cioDetail['created_date'];				
					$arr[]=getMainAmPmTime($cioDetail['sa_app_starttime']);
					$arr[]=$patient_name;
					$arr[]=$cioDetail['status'];
					fputcsv($fp,$arr, ",","\"");	
				}
			}
		}
		// TOTAL
		$others_collected_html .=' 
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
		<tr id="heading_orange"><td class="text_b_w" colspan="5">Other Status Records</td></tr>	
		<tr>
			<td class="text_b_w" style="width:200px;">Appt Date</td>
			<td class="text_b_w" style="width:200px;">Appt Time</td>
			<td class="text_b_w" style="width:250px;">&nbsp;Patient Name-Id</td>
			<td class="text_b_w" style="width:200px;">Status</td>
			<td class="text_b_w" style="width:160px;"></td>
		</tr>'
		.$content_part.'
		</table>';		
	}
}
fclose($fp);
$page_content= 
	$collected_html.
	$not_collected_html.
	$others_collected_html;
?>
