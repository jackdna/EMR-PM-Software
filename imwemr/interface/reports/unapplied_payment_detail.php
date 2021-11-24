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
FILE : unapplied_payment_detail.php
PURPOSE : Display detail result of unapplied amounts report
ACCESS TYPE : Direct
*/
	$total_cols = 6;
	$op_col = $op_col1 ="17";
	$w_cols = $w_cols1 = floor((100 - ($op_col))/($total_cols-1));
	$op_col = $op_col1 = 100 - ( (($total_cols-1) * $w_cols));

	$w_cols = $w_cols."%";
	$op_col = $op_col."%";

	//PDF
	$pageWidth=675;
	$pdf_op_col = 170;
	$pdf_w_cols = floor(($pageWidth - ($pdf_op_col))/($total_cols-1));
	$pdf_op_col =  $pageWidth - ($pdf_w_cols * ($total_cols-1));

	$pdf_w_cols = $pdf_w_cols."px";
	$pdf_op_col = $pdf_op_col."px";

	//--- CSV FILE HEADER INFORMATION 

	$pdf_header = <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:300;">&nbsp;$report_tit</td>
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
//MAKING OUTPUT DATA
$file_name="unappiled_payment.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr_csv=array();
$arr_csv[]=$report_tit;
$arr_csv[]='Selected DOP :'.$Start_date.' To '.$End_date;
$arr_csv[]='Created by :'.$op_name.' on '.$curDate;
fputcsv($fp,$arr_csv, ",","\"");
$arr_csv=array();
$arr_csv[]='Selected Group : '.$sel_grp;
$arr_csv[]='Selected Facility : '.$sel_fac;
$arr_csv[]='Selected Physician : '.$sel_phy;
$arr_csv[]='Selected Operator : '.$sel_opr;
fputcsv($fp,$arr_csv, ",","\"");
$arrGrand = array();
if(sizeof($arrPayments)>0){
	//pre($arrAppliedAmt);
	//pre($arrPayments);
	//pre($arrDelPayments);
	$arrGrdTotal = array();
	$printFile = true;
	$page_data=$pdf_header;
	$page_data.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">
			<tr id="heading_orange">	
				<td align="left"  class="text_10b"  colspan="$total_cols">CI/CO Payments and Pre Payments</td>	
			</tr>
DATA;
	$pdf_data=$pdf_header;
	$pdf_data.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">
			<tr id="heading_orange">	
				<td align="left"  class="text_b_w"  colspan="$total_cols">CI/CO Payments and Pre Payments</td>	
			</tr>
DATA;
	$arr_csv=array();
	$arr_csv[]='CI/CO Payments and Pre Payments';
	fputcsv($fp,$arr_csv, ",","\"");
	$arr_csv=array();
	$arr_csv[]='Operator';
	$arr_csv[]='Pt Name - ID';
	$arr_csv[]='Payment Date';
	$arr_csv[]='CI/CO Field';
	$arr_csv[]='CI/CO Payments';
	$arr_csv[]='Pre Payment';
	$arr_csv[]='Unapplied';
	fputcsv($fp,$arr_csv, ",","\"");
	
	foreach($arrPayments as $opr_id=>$arrOprPayment){
		$arrOprPayment['unapplied'] = 0;
		$arrTotal = array();
		$opr_name = ($opr_id==0)?"Not Specified":$arrAllUsers[$opr_id];
		$page_data.= <<<DATA
				<tr>
					<td class="text_b_w" style="text-align:left;" colspan="$total_cols">Operator: $opr_name </td>
				</tr>				
DATA;
		$pdf_data.= <<<DATA
				<tr>
					<td class="text_b_w" style="text-align:left;" colspan="$total_cols">Operator: $opr_name </td>
				</tr>				
DATA;

		$page_data.= <<<DATA
				<tr>
					<td class="text_10b" style="text-align:left; width:200px;">Pt Name - ID </td>
					<td class="text_10b" style="text-align:center; width:160px;">Payment Date</td>
					<td class="text_10b" style="text-align:center; width:160px;">CI/CO Field</td>
					<td class="text_10b" style="text-align:right; width:160px;">CI/CO Payments</td>
					<td class="text_10b" style="text-align:right; width:160px;">Pre Payment</td>
					<td class="text_10b" style="text-align:right; width:160px;">Unapplied</td>
				</tr>				
DATA;
		$pdf_data.= <<<DATA
				<tr>
					<td class="text_10b" style="text-align:left; width:200px;">Pt Name - ID </td>
					<td class="text_10b" style="text-align:center; width:150px;">Payment Date</td>
					<td class="text_10b" style="text-align:center; width:150px;">CI/CO Field</td>
					<td class="text_10b" style="text-align:right; width:150px;">CI/CO Payments</td>
					<td class="text_10b" style="text-align:right; width:150px;">Pre Payment</td>
					<td class="text_10b" style="text-align:right; width:170px;">Unapplied</td>
				</tr>				
DATA;
		foreach($arrOprPayment['patient'] as $patId=>$arrPatDetail){//pre($arrPatDetail);
			foreach($arrPatDetail['payments'] as $date=>$arrPayment){
					$pre_pay_amt = $pre_un_applied = $applied_cio_amt = $unapplied_amt = $pre_refund=0;
					$pre_pay_dis = 1;

					
					foreach($arrPayment['cio_payment'] as $arr){
					
						$arr['payment']-= $arr['refund'];
						$pre_pay_amt-= $pre_refund;
						
						if($arr['refund']>0)
							$cio_redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($arr['refund'],2);
						else 
							$cio_redRow='';
						

						if($pre_refund>0)
							 $pre_redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($pre_refund,2);
						else 
							$pre_redRow='';
						
						$cio_field_id = $arr['cio_field_id'];
						$cio_field = $cioFields[$cio_field_id];
						$unapplied_amt = $arr['un_applied'] + $pre_un_applied;
												
						$subArr = array();
						
						$arr_csv=array();
						$arr_csv[]=$opr_name;
						$arr_csv[]=$arrPatDetail['pat_detail'].' - '.$patId;
						$arr_csv[]=$date;
						$arr_csv[]=$cio_field;
						$arr_csv[]=$CLSReports->numberFormat($arr['payment'],2,1);
						
						
						$page_data.= '<tr class="data">
						<td style="text-align:left; width:'.$op_col.';">'.$arrPatDetail['pat_detail'].' - '.$patId.'</td>
						<td style="text-align:center; width:'.$w_cols.';">'.$date.'</td>
						<td style="text-align:center; width:'.$w_cols.';">'.$cio_field.'</td>
						<td style="text-align:right; width:'.$w_cols.$cio_redRow.'">'.$CLSReports->numberFormat($arr['payment'],2,1).'</td>
						<td style="text-align:right; width:'.$w_cols.$pre_redRow.'">';

						$pdf_data.= '<tr class="data">
						<td style="text-align:left; width:'.$pdf_op_col.';">'.$arrPatDetail['pat_detail'].' - '.$patId.'</td>
						<td style="text-align:center; width:'.$pdf_w_cols.';">'.$date.'</td>
						<td style="text-align:center; width:'.$pdf_w_cols.';">'.$cio_field.'</td>
						<td style="text-align:right; width:'.$pdf_w_cols.$cio_redRow.'">'.$CLSReports->numberFormat($arr['payment'],2,1).'</td>
						<td style="text-align:right; width:'.$pdf_w_cols.$pre_redRow.'">';

						if($pre_pay_dis)
						{
							$page_data.= $CLSReports->numberFormat($pre_pay_amt,2,1);
							$pdf_data.= $CLSReports->numberFormat($pre_pay_amt,2,1);
							$arr_csv[]=$CLSReports->numberFormat($pre_pay_amt,2,1);
                        }else{$arr_csv[]='';}
						$page_data.= '</td>
								<td style="text-align:right; width:'.$pdf_w_cols.';">'.$CLSReports->numberFormat($unapplied_amt,2).'</td>
								</tr>';
								$arr_csv[]=$CLSReports->numberFormat($unapplied_amt,2,1);
						$pdf_data.= '</td>
									<td style="text-align:right; width:'.$pdf_w_cols.';">'.$CLSReports->numberFormat($unapplied_amt,2).'</td>
								</tr>';
						fputcsv($fp,$arr_csv, ",","\"");
						$arrTotal['cio_payment'][] = $arr['payment'];
						if($pre_pay_dis)
						$arrTotal['pre_payment'][] = $pre_pay_amt;
						$arrTotal['unapplied'][] = $unapplied_amt;
						$pre_pay_dis = 0;
						$pre_un_applied=0;
						$pre_pay_amt=0;
						$pre_refund=0;
					}
                    
                    foreach($arrPayment['pre_payment'] as $arr){
						$pre_pay_amt= $arr['pre_payment'];
						$pre_un_applied= $arr['un_applied'];
						$pre_refund= $arr['refund'];
                        
                        
						$pre_pay_amt-= $pre_refund;

						if($pre_refund>0)
						$pre_redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($pre_refund,2);
						else 
						$pre_redRow='';
						
						$page_data.= '<tr class="data">
									<td style="text-align:left; width:'.$op_col.';">'.$arrPatDetail['pat_detail'].' - '.$patId.'</td>
									<td style="text-align:center; width:'.$w_cols.';">'.$date.'</td>
									<td style="text-align:center; width:'.$w_cols.';"></td>
									<td style="text-align:center; width:'.$w_cols.';"></td>
									<td style="text-align:right; width:'.$w_cols.$pre_redRow.'">';
						$pdf_data.= '<tr class="data">
									<td style="text-align:left; width:'.$pdf_op_col.';">'.$arrPatDetail['pat_detail'].' - '.$patId.'</td>
									<td style="text-align:center; width:'.$pdf_w_cols.';">'.$date.'</td>
									<td style="text-align:center; width:'.$pdf_w_cols.';"></td>
									<td style="text-align:center; width:'.$pdf_w_cols.';"></td>
									<td style="text-align:right; width:'.$pdf_w_cols.$pre_redRow.'">';

						$page_data.= $CLSReports->numberFormat($pre_pay_amt,2,1);
						$pdf_data.= $CLSReports->numberFormat($pre_pay_amt,2,1);
						
						$page_data.= '</td>
									<td style="text-align:right; width:'.$pdf_w_cols.';">'.$CLSReports->numberFormat($pre_un_applied,2).'</td>
								</tr>';
						$pdf_data.= '</td>
									<td style="text-align:right; width:'.$pdf_w_cols.';">'.$CLSReports->numberFormat($pre_un_applied,2).'</td>
								</tr>';
						
						$arr_csv=array();
						$arr_csv[]=$opr_name;
						$arr_csv[]=$arrPatDetail['pat_detail'].' - '.$patId;
						$arr_csv[]=$date;
						$arr_csv[]='';
						$arr_csv[]='';
						$arr_csv[]=$CLSReports->numberFormat($pre_pay_amt,2,1);
						$arr_csv[]=$CLSReports->numberFormat($pre_un_applied,2);
						fputcsv($fp,$arr_csv, ",","\"");
						
						$arrTotal['pre_payment'][] = $pre_pay_amt;		
						$arrTotal['unapplied'][] = $pre_un_applied;		
						$pre_pay_amt=0;
						$pre_un_applied=0;
						$pre_refund=0;
					}
			}	
			
		
		}
		$tot_cio_payment = $CLSReports->numberFormat(array_sum($arrTotal['cio_payment']),2);
		$tot_pre_payment = $CLSReports->numberFormat(array_sum($arrTotal['pre_payment']),2);
		$tot_unapplied = $CLSReports->numberFormat(array_sum($arrTotal['unapplied']),2);
		$page_data.= <<<DATA
					<tr><td class="total-row"  colspan="$total_cols"></td></tr>
					<tr bgcolor="#ffffff">
						<td class="text_10b" valign="top" style="text-align:right;" colspan="3">Total:</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_cio_payment</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_pre_payment</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_unapplied</td>
					</tr>
					<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
		$pdf_data.= <<<DATA
					<tr><td class="total-row"  colspan="$total_cols"></td></tr>
					<tr bgcolor="#ffffff">
						<td class="text_10b" valign="top" style="text-align:right;" colspan="3">Total:</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_cio_payment</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_pre_payment</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_unapplied</td>
					</tr>
					<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;

		$arrGrdTotal['cio_payment'][] = array_sum($arrTotal['cio_payment']);
		$arrGrdTotal['pre_payment'][] = array_sum($arrTotal['pre_payment']);
		$arrGrdTotal['unapplied'][] = array_sum($arrTotal['unapplied']);
	}
	$grd_cio_payment = $CLSReports->numberFormat(array_sum($arrGrdTotal['cio_payment']),2);
	$grd_pre_payment = $CLSReports->numberFormat(array_sum($arrGrdTotal['pre_payment']),2);
	$grd_unapplied = $CLSReports->numberFormat(array_sum($arrGrdTotal['unapplied']),2);
	$page_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
					<td class="text_10b" valign="top" style="text-align:right;" colspan="3">Grand Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_cio_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_pre_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_unapplied</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
	$pdf_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
					<td class="text_10b" valign="top" style="text-align:right;" colspan="3">Grand Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_cio_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_pre_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_unapplied</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;

$page_data .= <<<DATA
	</table>
DATA;
$pdf_data .= <<<DATA
	</table>
DATA;
}//END WRITEOFF IF
$page_data.='<br>';


if(sizeof($arrDelPayments)>0){
	$arrGrdTotal = array();
	$printFile = true;
	$page_data.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
			<tr id="heading_orange">	
				<td align="left"  class="text_10b"  colspan="$total_cols">Deleted Payments</td>	
			</tr>
DATA;

	$pdf_data1.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
			<tr id="heading_orange">	
				<td align="left"  class="text_10b"  colspan="$total_cols">Deleted Payments</td>	
			</tr>
DATA;
	$arr_csv=array();
	$arr_csv[]='Deleted Payments';
	fputcsv($fp,$arr_csv, ",","\"");
	foreach($arrDelPayments as $opr_id=>$arrOprPayment){
		$arrOprPayment['unapplied'] = 0;
		$arrTotal = array();
		$opr_name = ($opr_id==0)?"Not Specified":$arrAllUsers[$opr_id];
		$page_data.= <<<DATA
				<tr>
					<td style="text-align:left;" class="text_10b" colspan="$total_cols">Operator: $opr_name </td>
				</tr>				
DATA;
		$pdf_data1.= <<<DATA
				<tr>
					<td style="text-align:left;" class="text_10b" colspan="$total_cols">Operator: $opr_name </td>
				</tr>				
DATA;
			
		$arr_csv=array();
		$arr_csv[]='Operator';
		$arr_csv[]='Pt Name - ID';
		$arr_csv[]='Payment Date';
		$arr_csv[]='Deleted Date';
		$arr_csv[]='CI/CO Field';
		$arr_csv[]='CI/CO Payments';
		$arr_csv[]='Pre Payment';
		fputcsv($fp,$arr_csv, ",","\"");
	
		$page_data.= <<<DATA
				<tr>
					<td class="text_10b" style="text-align:center; width:$op_col;">Pt Name - ID </td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">Payment Date</td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">Deleted Date</td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">CI/CO Field</td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">CI/CO Payments</td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">Pre Payment</td>
				</tr>				
DATA;
		$pdf_data1.= <<<DATA
				<tr>
					<td class="text_10b" style="text-align:center; width:$op_col;">Pt Name - ID </td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">Payment Date</td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">Deleted Date</td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">CI/CO Field</td>
					<td class="text_10b" style="text-align:center; width:$w_cols;">CI/CO Payments</td>
					<td class="text_10b"  style="text-align:center; width:$w_cols;">Pre Payment</td>
				</tr>				
DATA;
		foreach($arrOprPayment['patient'] as $patId=>$arrPatDetail){//pre($arrPatDetail);
			foreach($arrPatDetail['payments'] as $date=>$arrPayment){
					$pre_pay_amt = $applied_prepay_amt = $applied_cio_amt = $unapplied_amt = 0;
					$pre_pay_dis = 1;
					foreach($arrPayment['pre_payment'] as $arr){
						$pre_pay_amt += $arr['pre_payment'];
						$applied_prepay_amt += $arrAppliedAmt['pre_payment'][$arr['payment_id']];
					}
					foreach($arrPayment['cio_payment'] as $arr){
						$cio_field_id = $arr['cio_field_id'];
						$cio_field = $cioFields[$cio_field_id];
						$applied_cio_amt = $arrAppliedAmt['cio_payment'][$arr['check_in_out_detail_id']];
						if($pre_pay_dis)
						$unapplied_amt = ($pre_pay_amt + $arr['cio_payment']) - ($applied_cio_amt + $applied_prepay_amt);
						else
						$unapplied_amt = $arr['cio_payment'] - $applied_cio_amt;
						$subArr = array();
						$arr_csv=array();
						$arr_csv[]=$opr_name;
						$arr_csv[]=$arrPatDetail['pat_detail'].' - '.$patId;
						$arr_csv[]=$date;
						$arr_csv[]=$arr['del_date'];
						$arr_csv[]=$cio_field;
						$arr_csv[]=$CLSReports->numberFormat($arr['cio_payment'],2);
						$page_data.= '<tr class="data">
									<td style="text-align:left;">'.$arrPatDetail['pat_detail'].' - '.$patId.'</td>
									<td style="text-align:center;">'.$date.'</td>
									<td style="text-align:center;">'.$arr['del_date'].'</td>
									<td style="text-align:center;">'.$cio_field.'</td>
									<td style="text-align:right;">'.$CLSReports->numberFormat($arr['cio_payment'],2).'</td>
									<td style="text-align:right;">';
						$pdf_data1.= '<tr class="data">
									<td style="text-align:left;">'.$arrPatDetail['pat_detail'].' - '.$patId.'</td>
									<td style="text-align:center;">'.$date.'</td>
									<td style="text-align:center;">'.$arr['del_date'].'</td>
									<td style="text-align:center;">'.$cio_field.'</td>
									<td style="text-align:right;">'.$CLSReports->numberFormat($arr['cio_payment'],2).'</td>
									<td style="text-align:right;">';									
						if($pre_pay_dis)
						$page_data.= $CLSReports->numberFormat($pre_pay_amt,2);
						$pdf_data1.= $CLSReports->numberFormat($pre_pay_amt,2);
						$page_data.= '</td>
								</tr>';
						$pdf_data1.= '</td>
								</tr>';
						$arr_csv[]=$CLSReports->numberFormat($pre_pay_amt,2);
						fputcsv($fp,$arr_csv, ",","\"");
						$arrTotal['cio_payment'][] = $arr['cio_payment'];
						if($pre_pay_dis)
						$arrTotal['pre_payment'][] = $pre_pay_amt;
						$pre_pay_dis = 0;
					}
					if(count($arrPayment['cio_payment'])<=0){
						$unapplied_amt = ($pre_pay_amt) - ($applied_prepay_amt);
						$page_data.= '<tr class="data">
									<td style="text-align:left;">'.$arrPatDetail['pat_detail'].' - '.$patId.'</td>
									<td style="text-align:center;">'.$date.'</td>
									<td style="text-align:center;">'.$arr['del_date'].'</td>
									<td style="text-align:center;"></td>
									<td style="text-align:center;"></td>
									<td style="text-align:right;">';
						$pdf_data1.= '<tr class="data">
									<td style="text-align:left;">'.$arrPatDetail['pat_detail'].' - '.$patId.'</td>
									<td style="text-align:center;">'.$date.'</td>
									<td style="text-align:center;">'.$arr['del_date'].'</td>
									<td style="text-align:center;"></td>
									<td style="text-align:center;"></td>
									<td style="text-align:right;">';									
						$page_data.= $CLSReports->numberFormat($pre_pay_amt,2);
						$pdf_data1.= $CLSReports->numberFormat($pre_pay_amt,2);
						$page_data.= '</td>
								</tr>';
						$pdf_data1.= '</td>
								</tr>';								
						$arrTotal['pre_payment'][] = $pre_pay_amt;		
						
					}
			}	
			
		
		}
		$tot_cio_payment = $CLSReports->numberFormat(array_sum($arrTotal['cio_payment']),2);
		$tot_pre_payment = $CLSReports->numberFormat(array_sum($arrTotal['pre_payment']),2);
		$page_data.= <<<DATA
					<tr><td class="total-row"  colspan="$total_cols"></td></tr>
					<tr bgcolor="#ffffff">
						<td class="text_10b" valign="top" style="text-align:right;" colspan="4">Total:</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_cio_payment</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_pre_payment</td>
					</tr>
					<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
		$pdf_data1.= <<<DATA
					<tr><td class="total-row"  colspan="$total_cols"></td></tr>
					<tr bgcolor="#ffffff">
						<td class="text_10b" valign="top" style="text-align:right;" colspan="4">Total:</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_cio_payment</td>
						<td class="text_10b" valign="top" style="text-align:right; ">$tot_pre_payment</td>
					</tr>
					<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
		$arrGrdTotal['cio_payment'][] = array_sum($arrTotal['cio_payment']);
		$arrGrdTotal['pre_payment'][] = array_sum($arrTotal['pre_payment']);
		$arrGrdTotal['unapplied'][] = array_sum($arrTotal['unapplied']);
	}
	$grd_cio_payment = $CLSReports->numberFormat(array_sum($arrGrdTotal['cio_payment']),2);
	$grd_pre_payment = $CLSReports->numberFormat(array_sum($arrGrdTotal['pre_payment']),2);
	$grd_unapplied = $CLSReports->numberFormat(array_sum($arrGrdTotal['unapplied']),2);
	$page_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
					<td class="text_10b" valign="top" style="text-align:right;" colspan="4">Grand Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_cio_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_pre_payment</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
	$pdf_data1.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
					<td class="text_10b" valign="top" style="text-align:right;" colspan="4">Grand Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_cio_payment</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$grd_pre_payment</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
$page_data .= <<<DATA
	</table>
DATA;
$pdf_data1 .= <<<DATA
	</table>
DATA;

}//END WRITEOFF IF
$page_data.='<br>';

$page_data = $page_data;
$pdf_data.= $pdf_data1;
?>
