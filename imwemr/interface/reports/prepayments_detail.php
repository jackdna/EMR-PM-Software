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
$selDateC = strtoupper($DateRangeFor);



$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$op_name = $op_name_arr[1][0];
$op_name .= $op_name_arr[0][0];
$op_name = strtoupper($op_name);
$curDate = date('H:i A');
//---------BEGIN CALCULATE COLUMN WIDTHS----------------
$total_cols = 11;
$notes_col = "12";
$date_col = $date_col1 = "8";
$w_cols = $w_cols1 = floor((100 - ($notes_col+$date_col))/($total_cols-2));
$notes_col = $notes_col1 = 100 - ((($total_cols-2) * $w_cols) + $date_col);

$w_cols = $w_cols."%";
$notes_col = $notes_col."%";
$date_col = $date_col."%";
//---------END CALCULATE COLUMN WIDTHS----------------

//---------BEGIN PAGE HEADERS-------------------------
$pdf_header ='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr>	
			<td width="258" align="left" class=" rptbx1">Prepayments Report (Detail)</td>	
			<td width="258" align="left" class=" rptbx2">Selected Group : '.$sel_grp.'</td>			
			<td width="258" align="left" class=" rptbx3">'.$selDateC.' (From : '.$Start_date.' To : '.$End_date.')</td>
			<td width="258" align="left" class=" rptbx1">Created By: '.$report_generator_name.' on '.$curDate.'</td>
		</tr>
		<tr>	
			<td align="left" class="rptbx1" nowrap>Selected Facility : '.$sel_fac.'</td>	
			<td align="left" class="rptbx2">Selected Physician : '.$sel_phy.'</td>			
			<td align="left" class="rptbx3" >Selected Operator : '.$sel_opr.'</td>
			<td align="left" class="rptbx1" >Sel Method : '.$sel_method.'</td>
		</tr>
	</table>';


//---------END PAGE HEADERS-------------------------

$file_name="prepayments_detail.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr=array();
$arr[]='Prepayments Report (Detail)';
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
if(sizeof($arrPatPayDetail)>0){
	$arrTotal = array();
	$arrCIOTotal = array();
	$arrPreTotal = array();
	$printFile = true;
	$page_data.='<table class="rpt_table rpt rpt_table-bordered" style="width:100%">';
			
	$page_data.= '
	<tr id="">
		<td class="text_b_w" style="text-align:center; width:'.$date_col.';">Entered Date</td>
		<td class="text_b_w" style="text-align:center; width:'.$date_col.';">Payment Date</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Operator</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Location</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Account#</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Patient Name</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Method</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Payment</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Applied</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Unapplied</td>
		<td class="text_b_w" style="text-align:center; width:'.$notes_col.';" >Notes</td>
	</tr>';

	$arr=array();
	$arr[]='Entered Date';
	$arr[]='Payment Date';
	$arr[]='Operator';
	$arr[]='Location';
	$arr[]='Account#';
	$arr[]='Patient Name';
	$arr[]='Method';
	$arr[]='Payment';
	$arr[]='Applied';
	$arr[]='Unapplied';
	$arr[]='Pre Payments: Unapplied';
	$arr[]='Notes';
	fputcsv($fp,$arr, ",","\"");			
			
	foreach($arrPatPayDetail as $id=>$arrData){
		
		$pre_applied = $arrData['applied'] + $arrPrePayAppliedId[$id];
		$pre_unapplied= $arrData['payment'] - $pre_applied;
		
		if($arrData['payment_ref']>0){
			$redRowPre=';color:#FF0000" title="Refund '.numberformat($arrData['payment_ref'],2);
		}else $redRowPre='';

		$page_data.= '
		<tr class="data">
			<td style="text-align:left; width:'.$date_col.'; word-wrap:break-word;">'.$arrData['entered_date'].'</td>
			<td style="text-align:left; width:'.$date_col.'; word-wrap:break-word;">'.$arrData['payment_date'].'</td>
			<td style="text-align:left; width:'.$w_cols.'; word-wrap:break-word;">'.$arrAllUsers[$arrData['operator']].'</td>
			<td style="text-align:left; width:'.$w_cols.'; word-wrap:break-word;">'.$arr_sch_facilities[$arrData['location']].'</td>
			<td style="text-align:left; width:'.$w_cols.'; word-wrap:break-word;">'.$arrData['pat_id'].'</td>
			<td style="text-align:left; width:'.$w_cols.'; word-wrap:break-word;">'.$arrData['pat_name'].'</td>
			<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;" >'.$arrData['payment_method'].'</td>
			<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word'.$redRowPre.'">'.$CLSReports->numberformat($arrData['payment'],2).'</td>
			<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.$CLSReports->numberformat($pre_applied,2).'</td>
			<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.$CLSReports->numberformat(($pre_unapplied),2).'</td>
			<td style="text-align:left; width:'.$notes_col.'; word-wrap:break-word;">'.$arrData['comment'].'</td>
		</tr>';
		
		
		$arr=array();
		$arr[]=$arrData['entered_date'];
		$arr[]=$arrData['payment_date'];
		$arr[]=$arrAllUsers[$arrData['operator']];
		$arr[]=$allFacArr[$arrData['location']];
		$arr[]=$arrData['pat_id'];
		$arr[]=$arrData['pat_name'];
		$arr[]=$arrData['payment_method'];
		$arr[]=$CLSReports->numberformat($arrData['payment'],2);
		$arr[]=$CLSReports->numberformat($pre_applied,2);
		$arr[]=$CLSReports->numberformat(($pre_unapplied),2);
		$arr[]=$arrData['comment'];
		fputcsv($fp,$arr, ",","\"");
		
		$strArr = array();
		foreach($arrHtml as $key => $val){
			for($i = 0; $i<=count($val);$i++){
				$method = $val[$i]['method'];
				$pay = $val[$i]['pay'];
				if(empty($method) && empty($pay)) continue;
				$strArr[$i][$key] = array('method' => $method, 'pay' => $pay);
			}
		}
		
		$strHtml = '';
		if(count($strArr) > 0){
			foreach($strArr as $obj){
				$arr=array();
				$arr[]=$arrPatient[$patId];
				$arr[]=$DOT;
				$checkInHtml = $preHtml = $postHtml = '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;" ></td><td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;" >$0.00</td>';
				$strHtml .= '<tr class="data">';
				$strHtml .= '<td style="text-align:left; width:'.$w_cols.'; word-wrap:break-word;">'.$arrPatient[$patId].'</td>';
				$strHtml .= '<td style="text-align:left; width:'.$date_col.'; word-wrap:break-word;">'.$DOT.'</td>';
				$strHtml .= '<td style="text-align:left; width:'.$w_cols.'; word-wrap:break-word;"></td>';
				$arr[]="";
				if($obj['CheckIn']){
					$checkInHtml = '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['CheckIn']['method'],6, "<br />\n", true).'</td><td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['CheckIn']['pay'],2).'</td>';
					
				}
				$strHtml .= $checkInHtml;
				$strHtml .= '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">$0.00</td>';
				$strHtml .= '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">$0.00</td>';
				if($obj['PrePay']){
					$preHtml = '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['PrePay']['method'],6, "<br />\n", true).'</td><td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['PrePay']['pay'],2).'</td>';
					
				}
				$strHtml .= $preHtml;
				$strHtml .= '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">$0.00</td>';
				$strHtml .= '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">$0.00</td>';
				if($obj['PostPay']){
					$postHtml = '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['PostPay']['method'],6, "<br />\n", true).'</td><td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['PostPay']['pay'],2).'</td>';
					
				}
				$strHtml .= $postHtml;
				$strHtml .= '<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;"></td>';
				$strHtml .= '</tr>';
				
				if($obj['CheckIn']){
					$arr[]=$obj['CheckIn']['method'];
					$arr[]=numberformat($obj['CheckIn']['pay'],2);
				}else{
					$arr[]="";
					$arr[]="$0.00";
				}
				$arr[]="$0.00";
				$arr[]="$0.00";
				if($obj['PrePay']){
					$arr[]=$obj['PrePay']['method'];
					$arr[]=numberformat($obj['PrePay']['pay'],2);
				}else{
					$arr[]="";
					$arr[]="$0.00";
				}
				$arr[]="$0.00";
				$arr[]="$0.00";
				if($obj['PostPay']){
					$arr[]=$obj['PostPay']['method'];
					$arr[]=numberformat($obj['PostPay']['pay'],2);
				}else{
					$arr[]="";
					$arr[]="$0.00";
				}
				fputcsv($fp,$arr, ",","\"");
			}
		}
		
		if(empty($strHtml) == false) $page_data .= $strHtml;
		
		$arrPreTotal['payment']+= $arrData['payment'];
		$arrPreTotal['applied']+= $pre_applied;
		$arrPreTotal['unapplied']+= $pre_unapplied;
	}

	
	$arr=array();
	$arr[]=' ';
	$arr[]=' ';
	$arr[]=' ';
	$arr[]=' ';
	$arr[]=' ';
	$arr[]=' ';
	$arr[]='Total';
	$arr[]=$CLSReports->numberformat($arrPreTotal['payment'],2);
	$arr[]=$CLSReports->numberformat($arrPreTotal['applied'],2);
	$arr[]=$CLSReports->numberformat($arrPreTotal['unapplied'],2);
	fputcsv($fp,$arr, ",","\"");
				
	$page_data.= '
	<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
	<tr class="subtotal">
		<td style="text-align:right;" colspan="7">Total:</td>
		<td style="text-align:right;">'.$CLSReports->numberformat($arrPreTotal['payment'],2).'</td>
		<td style="text-align:right;">'.$CLSReports->numberformat($arrPreTotal['applied'],2).'</td>
		<td style="text-align:right;">'.$CLSReports->numberformat($arrPreTotal['unapplied'],2).'</td>
		<td style="text-align:right;">&nbsp;</td>
	</tr>	
	<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
	</table>';
}



$postpaymhdtd = '';
if($DateRangeFor=='dor'){
	$postpaymhdtd = 'After '.$End_date;
}

//DELETED PRE_PAYMENTS
$content_part= $deleted_html='';
$arrDelPatPayTot=array();
if(sizeof($arrDelPrePayAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$colspan= 7;

	$total_cols = 7;
	$first_col = "10";
	$last_col = "21";
	$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
	
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);

	$grand_first_col=$first_col;
	$grand_w_cols=$w_cols;	
	
	$first_col = $first_col.'%';
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	$arr=array();
	$arr[]='Deleted Pre-Payments Payments '.$postpaymhdtd;
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]='Patient Name-Id';
	$arr[]=' ';
	$arr[]='DOT';
	$arr[]='Deleted Amount';
	$arr[]='Notes';
	fputcsv($fp,$arr, ",","\"");
	
	foreach($arrDelPrePayAmounts as $eid => $grpDetail){
			$rowTot=0;
		
			$pName = explode('~', $grpDetail['pat_name']);
			$patient_name_arr = array();
			$patient_name_arr["LAST_NAME"] = $pName[3];
			$patient_name_arr["FIRST_NAME"] = $pName[1];
			$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
			$patient_name = changeNameFormat($patient_name_arr);
			$patient_name.= ' - '.$pName[0];
			
			$arrDelPatPayTot['del_amount']+=$grpDetail['del_amount'];
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['deleted_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$arrAllUsers[$grpDetail['operator']].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$last_col.'">'.$grpDetail['comment'].'</td>
			</tr>';	
				
			$arr=array();
			$arr[]=$patient_name;
			$arr[]=' ';
			$arr[]=$grpDetail['entered_date'];
			$arr[]=$CLSReports->numberFormat($grpDetail['del_amount'],2);
			$arr[]=$grpDetail['comment'];
			fputcsv($fp,$arr, ",","\"");
			
		}
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
	<tr id="heading_orange"><td class="text_b_w" colspan="'.$colspan.'">Deleted Pre-Payments between '.$Start_date.' and '.$End_date.'</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Enetered Date</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Payment Date</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Deleted Date</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Operator</td>
		<td class="text_b_w" style="width:'.$first_col.'; text-align:center;">Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Deleted Amount</td>
		<td class="text_b_w" style="width:'.$last_col.'; text-align:center;">Notes</td>
	</tr>';

	
	$delPrePayBlockTotal = $arrDelPatPayTot['del_amount'];
	
	//PAGE HTML
	$page_data .=
	$header. 
	$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="5">Deleted Pre-Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>';

	$arr=array();
	$arr[]=' ';
	$arr[]=' ';
	$arr[]='Deleted Pre-Payments Total:';
	$arr[]=$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2);
	$arr[]=' ';
	fputcsv($fp,$arr, ",","\"");	
}



$complete_page_data = 
$pdf_header.
$page_data;
?>