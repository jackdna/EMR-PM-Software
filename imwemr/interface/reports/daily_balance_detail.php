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

function getRowHtml($arrData = array()){
	if(count($arrData) == 0) return '';
	$returnArr = array();
	$i=0;	
	foreach($arrData as $key => $arrDetail){	
		$methodName = $arrDetail['method'];
		$reference_no = $arrDetail['reference_no'];
		$methodPay = $arrDetail['pay'];
		$applied = $arrDetail['applied'];
		$returnArr[$i] = array('method' => $methodName, 'reference_no'=>$reference_no, 'pay' => $methodPay, 'applied'=>$applied);
		$i++;
	}
	return $returnArr;
}
if($hasData>0){
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = $op_name_arr[1][0];
	$op_name .= $op_name_arr[0][0];
	$op_name = strtoupper($op_name);
	$curDate = date('H:i A');
	//---------BEGIN CALCULATE COLUMN WIDTHS----------------
	$total_cols = 17;
	$pat_col = "10";
	$date_col = $date_col1 = "7";
	$w_cols = $w_cols1 = floor((100 - ($pat_col+$date_col))/($total_cols-2));
	$pat_col = $pat_col1 = 100 - ((($total_cols-2) * $w_cols) + $date_col);

	$w_cols = $w_cols."%";
	$pat_col = $pat_col."%";
	$date_col = $date_col."%";

	$total_cols_pdf = 14;
	$pat_col_pdf = 10;
	$date_col_pdf = $date_col1_pdf = 7;	
	$w_cols_pdf = $w_cols1_pdf = floor((100 - ($pat_col_pdf+$date_col_pdf))/($total_cols_pdf-2));
	$pat_col_pdf = $pat_col1_pdf = 100 - ((($total_cols_pdf-2) * $w_cols_pdf) + $date_col_pdf);
	
	$w_cols_pdf = $w_cols_pdf."%";
	$pat_col_pdf = $pat_col_pdf."%";
	$date_col_pdf = $date_col_pdf."%";
	//---------END CALCULATE COLUMN WIDTHS----------------
	
	//---------BEGIN PAGE HEADERS-------------------------
	$pdf_header = <<<DATA
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>	
				<td width="258" align="left" class=" rptbx1">Daily Balance Report (Detail)</td>	
				<td width="258" align="left" class=" rptbx2">Selected Group : $sel_grp</td>			
				<td width="258" align="left" class=" rptbx3">$selDateC (From : $Start_date To : $End_date)</td>
				<td width="258" align="left" class=" rptbx1">Created By: $report_generator_name on $curDate</td>
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

$file_name="daily_balance.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr=array();
$arr[]='Daily Balance Report (Detail)';
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
	//pre($arrPatPayDetail['66670']);
	$arrTotal = array();
	$arrCIOTotal = array();
	$arrPreTotal = array();
	$printFile = true;

	$main_block_page.='
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
	<tr id="">	
		<td align="left"  class="text_b_w"  colspan="'.$total_cols.'">CI/CO , Pre Payments and Posted Payments</td>	
	</tr>
	<tr id="">
		<td class="text_b_w" style="text-align:center; width:'.$pat_col.';">Patient Name - ID</td>
		<td class="text_b_w" style="text-align:center; width:'.$date_col.';">DOT</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Desc</td>
		<td class="text_b_w" style="text-align:center;" colspan="5" >CI/CO</td>
		<td class="text_b_w" style="text-align:center;" colspan="5" >Pre Payments</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" colspan="3">Posted Pymt</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Opr</td>
	</tr>	
	<tr id="">
		<td class="text_b_w" style="text-align:center; width:'.$pat_col.';"></td>
		<td class="text_b_w" style="text-align:center; width:'.$date_col.';"></td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" ></td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Method</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Reference#</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Payment</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Applied</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Unappl</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Method</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Reference#</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Payment</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Applied</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Unappl</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Method</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Reference#</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Payment</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" ></td>
	</tr>';

	$main_block_pdf.='
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
	<tr id="">	
		<td align="left"  class="text_b_w"  colspan="'.$total_cols_pdf.'">CI/CO , Pre Payments and Posted Payments</td>	
	</tr>
	<tr id="">
		<td class="text_b_w" style="text-align:center; width:'.$pat_col_pdf.';">Patient Name - ID</td>
		<td class="text_b_w" style="text-align:center; width:'.$date_col_pdf.';">DOT</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';">Desc</td>
		<td class="text_b_w" style="text-align:center;" colspan="4" >CI/CO</td>
		<td class="text_b_w" style="text-align:center;" colspan="4" >Pre Payments</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" colspan="2">Posted Pymt</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';">Opr</td>
	</tr>	
	<tr id="">
		<td class="text_b_w" style="text-align:center; width:'.$pat_col_pdf.';"></td>
		<td class="text_b_w" style="text-align:center; width:'.$date_col_pdf.';"></td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" ></td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Method</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Payment</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Applied</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Unappl</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Method</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Payment</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Applied</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Unappl</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Method</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" >Payment</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols_pdf.';" ></td>
	</tr>';		

$arr=array();
$arr[]='CI/CO and Posted Payments';
fputcsv($fp,$arr, ",","\"");	
$arr=array();
$arr[]='Patient Name - ID';
$arr[]='DOT';
$arr[]='Desc';
$arr[]='CI/CO: Method';
$arr[]='CI/CO: Reference#';
$arr[]='CI/CO: Payment';
$arr[]='CI/CO: Applied';
$arr[]='CI/CO: Unapplied';
$arr[]='Pre Payments: Method';
$arr[]='Pre Payments: Reference#';
$arr[]='Pre Payments: Payment';
$arr[]='Pre Payments: Applied';
$arr[]='Pre Payments: Unapplied';
$arr[]='Posted Method';
$arr[]='Posted Reference#';
$arr[]='Posted Payments';
$arr[]='Opr';
fputcsv($fp,$arr, ",","\"");			
	
	foreach($arrPatPayDetail as $patId=>$arrDOT){
		foreach($arrDOT as $DOT=>$arrOpr){
			foreach($arrOpr as $oprId=>$arrData){
				preg_match_all('/(?<=\,\s|^)[a-z0-9]/i', $arrAllUsers[$oprId], $matches);
				
				/*$pre_applied = $arrData['pre_applied'];
				foreach($arrData['pre_pay_id'] as $pre_pay_id){
					foreach($arrPrePayAppliedId[$pre_pay_id] as $pre_app_pay){
					$pre_applied += $pre_app_pay;
					}
				}*/
				
			//	pre($arrData['PAYMENT_ROW']);
				$arrHtml = array();
				foreach($arrData['PAYMENT_ROW'] as $paykey => $payVal){
					if($paykey=='CheckIn'){
					//	pre($payVal);
					}
					switch($paykey){
						case 'CheckIn':
							$arrHtml[$paykey] = getRowHtml($payVal);
						break;
						case 'PrePay':
							$arrHtml[$paykey] = getRowHtml($payVal);
						break;
						case 'PostPay':
							$arrHtml[$paykey] = getRowHtml($payVal);
						break;
					}
				}

				$cio_unapplied= $arrHtml['CheckIn'][0]['pay'] - $arrHtml['CheckIn'][0]['applied'];
				$pre_unapplied= $arrHtml['PrePay'][0]['pay'] - $arrHtml['PrePay'][0]['applied'];
				
				//MAKING METHOD STRINGS FOR PDF
				$check_in_method_pdf=$arrHtml['CheckIn'][0]['method'];
				$pre_method_pdf=$arrHtml['PrePay'][0]['method'];
				$post_method_pdf=$arrHtml['PostPay'][0]['method'];
				if($arrHtml['CheckIn'][0]['reference_no']!=''){
					$check_in_method_pdf=$arrHtml['CheckIn'][0]['method'].'- '.$arrHtml['CheckIn'][0]['reference_no'];	
				}
				if($arrHtml['PrePay'][0]['reference_no']!=''){
					$pre_method_pdf=$arrHtml['PrePay'][0]['method'].'- '.$arrHtml['PrePay'][0]['reference_no'];	
				}				
				if($arrHtml['PostPay'][0]['reference_no']!=''){
					$post_method_pdf=$arrHtml['PostPay'][0]['method'].'- '.$arrHtml['PostPay'][0]['reference_no'];	
				}

				
				if($arrData['cio_payment_ref']>0)
				{
					$redRow=';color:#FF0000" title="Refund '.numberformat($arrData['cio_payment_ref'],2);
				}else $redRow='';
				
				if($arrData['pre_payment_ref']>0)
				{
					$redRowPre=';color:#FF0000" title="Refund '.numberformat($arrData['pre_payment_ref'],2);
				}else $redRowPre='';


				$main_block_page.= '
				<tr class="data">
					<td style="text-align:left; width:'.$pat_col.'; word-wrap:break-word;">'.$arrPatient[$patId].'</td>
					<td style="text-align:left; width:'.$date_col.'; word-wrap:break-word;">'.$DOT.'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.substr($cioFields[$arrData['cio_item_id']],0,11).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($arrHtml['CheckIn'][0]['method'], 7, "<br />\n", true).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($arrHtml['CheckIn'][0]['reference_no'], 10, "<br />\n", true).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word'.$redRow.'" >'.numberformat($arrHtml['CheckIn'][0]['pay'],2).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;" >'.numberformat($arrHtml['CheckIn'][0]['applied'],2).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat(($cio_unapplied),2).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;" >'.wordwrap($arrHtml['PrePay'][0]['method'],7, "<br />\n", true).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($arrHtml['PrePay'][0]['reference_no'], 10, "<br />\n", true).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word'.$redRowPre.'">'.numberformat($arrHtml['PrePay'][0]['pay'],2).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($arrHtml['PrePay'][0]['applied'],2).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat(($pre_unapplied),2).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;" >'.wordwrap($arrHtml['PostPay'][0]['method'],7, "<br />\n", true).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($arrHtml['PostPay'][0]['reference_no'], 10, "<br />\n", true).'</td>
					<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($arrHtml['PostPay'][0]['pay'],2).'</td>
					<td style="text-align:left; width:'.$w_cols.'; word-wrap:break-word;">'.ucwords(implode('', array_reverse($matches[0]))).'</td>
				</tr>';

				$main_block_pdf.= '
				<tr class="data">
					<td style="text-align:left; width:'.$pat_col_pdf.'; word-wrap:break-word;">'.$arrPatient[$patId].'</td>
					<td style="text-align:left; width:'.$date_col_pdf.'; word-wrap:break-word;">'.$DOT.'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.substr($cioFields[$arrData['cio_item_id']],0,11).'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.$check_in_method_pdf.'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word'.$redRow.'" >'.numberformat($arrHtml['CheckIn'][0]['pay'],2).'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;" >'.numberformat($arrHtml['CheckIn'][0]['applied'],2).'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.numberformat(($cio_unapplied),2).'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;" >'.$pre_method_pdf.'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word'.$redRowPre.'">'.numberformat($arrHtml['PrePay'][0]['pay'],2).'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.numberformat($arrHtml['PrePay'][0]['applied'],2).'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.numberformat(($pre_unapplied),2).'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;" >'.$post_method_pdf.'</td>
					<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.numberformat($arrHtml['PostPay'][0]['pay'],2).'</td>
					<td style="text-align:left; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.ucwords(implode('', array_reverse($matches[0]))).'</td>
				</tr>';				
				
				
				$arr=array();
				$arr[]=$arrPatient[$patId];
				$arr[]=$DOT;
				$arr[]=substr($cioFields[$arrData['cio_item_id']],0,11);
				$arr[]=$arrHtml['CheckIn'][0]['method'];
				$arr[]=$arrHtml['CheckIn'][0]['reference_no'];
				$arr[]=numberformat($arrHtml['CheckIn'][0]['pay'],2);
				$arr[]=numberformat($arrHtml['CheckIn'][0]['applied'],2);
				$arr[]=numberformat(($cio_unapplied),2);
				$arr[]=$arrHtml['PrePay'][0]['method'];
				$arr[]=$arrHtml['PrePay'][0]['reference_no'];
				$arr[]=numberformat($arrHtml['PrePay'][0]['pay'],2);
				$arr[]=numberformat($arrHtml['PrePay'][0]['applied'],2);
				$arr[]=numberformat(($pre_unapplied),2);
				$arr[]=$arrHtml['PostPay'][0]['method'];
				$arr[]=$arrHtml['PostPay'][0]['reference_no'];
				$arr[]=numberformat($arrHtml['PostPay'][0]['pay'],2);
				$arr[]=ucwords(implode('', array_reverse($matches[0])));
				fputcsv($fp,$arr, ",","\"");
				
				if(count($arrHtml['CheckIn']) > 0) unset($arrHtml['CheckIn'][0]);
				if(count($arrHtml['PrePay']) > 0) unset($arrHtml['PrePay'][0]);
				if(count($arrHtml['PostPay']) > 0) unset($arrHtml['PostPay'][0]);
				
				$strArr = array();
				foreach($arrHtml as $key => $val){
					for($i = 0; $i<=count($val);$i++){
						$method = $val[$i]['method'];
						$reference_no = $val[$i]['reference_no'];
						$pay = $val[$i]['pay'];
						$applied = $val[$i]['applied'];
						if(empty($method) && empty($pay)) continue;
						$strArr[$i][$key] = array('method' => $method, 'reference_no'=>$reference_no, 'pay' => $pay, 'applied'=>$applied);
					}
				}
				
				$strHtml = $strPDF='';
				if(count($strArr) > 0){
					foreach($strArr as $obj){

						//MAKING METHOD STRINGS FOR PDF
						$check_in_method_pdf=$obj['CheckIn']['method'];
						$pre_method_pdf=$obj['PrePay']['method'];
						$post_method_pdf=$obj['PostPay']['method'];
						if($obj['CheckIn']['reference_no']!=''){
							$check_in_method_pdf=$obj['CheckIn']['method'].'-'.$obj['CheckIn']['reference_no'];	
						}
						if($obj['PrePay']['reference_no']!=''){
							$pre_method_pdf=$obj['PrePay']['method'].'-'.$obj['PrePay']['reference_no'];	
						}				
						if($obj['PostPay']['reference_no']!=''){
							$post_method_pdf=$obj['PostPay']['method'].'-'.$obj['PostPay']['reference_no'];	
						}

						//PAGE DATA
						$strHtml .= '<tr class="data">
						<td style="text-align:left; width:'.$pat_col.'; word-wrap:break-word;">'.$arrPatient[$patId].'</td>
						<td style="text-align:left; width:'.$date_col.'; word-wrap:break-word;">'.$DOT.'</td>
						<td style="text-align:left; width:'.$w_cols.'; word-wrap:break-word;"></td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['CheckIn']['method'],7, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['CheckIn']['reference_no'],10, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['CheckIn']['pay'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['CheckIn']['applied'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['CheckIn']['pay']-$obj['CheckIn']['applied'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['PrePay']['method'],7, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['PrePay']['reference_no'],10, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['PrePay']['pay'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['PrePay']['applied'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['PrePay']['pay']-$obj['PrePay']['applied'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['PostPay']['method'],7, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.wordwrap($obj['PostPay']['reference_no'],10, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['PostPay']['pay'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;"></td>
						</tr>';


						//PDF DATA
						$strPDF .= '<tr class="data">
						<td style="text-align:left; width:'.$pat_col_pdf.'; word-wrap:break-word;">'.$arrPatient[$patId].'</td>
						<td style="text-align:left; width:'.$date_col_pdf.'; word-wrap:break-word;">'.$DOT.'</td>
						<td style="text-align:left; width:'.$w_cols_pdf.'; word-wrap:break-word;"></td>
						<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.wordwrap($check_in_method_pdf,7, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.numberformat($obj['CheckIn']['pay'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['CheckIn']['applied'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['CheckIn']['pay']-$obj['CheckIn']['applied'],2).'</td>
						<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.wordwrap($pre_method_pdf,7, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.numberformat($obj['PrePay']['pay'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['PrePay']['applied'],2).'</td>
						<td style="text-align:right; width:'.$w_cols.'; word-wrap:break-word;">'.numberformat($obj['PrePay']['pay']-$obj['PrePay']['applied'],2).'</td>						
						<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.wordwrap($post_method_pdf,7, "<br />\n", true).'</td>
						<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;">'.numberformat($obj['PostPay']['pay'],2).'</td>
						<td style="text-align:right; width:'.$w_cols_pdf.'; word-wrap:break-word;"></td>
						</tr>';						
						
						//CSV DATA
						$arr=array();
						$arr[]=$arrPatient[$patId];
						$arr[]=$DOT;
						$arr[]="";
						$arr[]=$obj['CheckIn']['method'];
						$arr[]=$obj['CheckIn']['reference_no'];
						$arr[]=numberformat($obj['CheckIn']['pay'],2);
						$arr[]=numberformat($obj['CheckIn']['applied'],2);
						$arr[]=numberformat($obj['CheckIn']['pay']-$obj['CheckIn']['applied'],2);
						$arr[]=$obj['PrePay']['method'];
						$arr[]=$obj['PrePay']['reference_no'];
						$arr[]=numberformat($obj['PrePay']['pay'],2);
						$arr[]=numberformat($obj['PrePay']['applied'],2);
						$arr[]=numberformat($obj['PrePay']['pay']-$obj['PrePay']['applied'],2);
						$arr[]=$obj['PostPay']['method'];
						$arr[]=$obj['PostPay']['reference_no'];
						$arr[]=numberformat($obj['PostPay']['pay'],2);
						$arr[]="";
						fputcsv($fp,$arr, ",","\"");
					}
				}
				
				if(empty($strHtml) == false) $main_block_page .= $strHtml;
				if(empty($strPDF) == false) $main_block_pdf .= $strPDF;
				
				$arrTotal['cio_payment'][] = $arrData['cio_payment'];
				$arrTotal['cio_applied'][] = $arrData['cio_applied'];
				$arrTotal['pre_payment'][] = $arrData['pre_payment'];
				$arrTotal['pre_applied'][] = $pre_applied;
				$arrTotal['posted'][] = $arrData['posted'];
				
				$arrCIOTotal['payment'][] = $arrData['cio_payment'];
				$arrCIOTotal['applied'][] = $arrData['cio_applied'];
				$arrCIOTotal['unapplied'][] = $cio_unapplied;
				$arrCIOTotal['posted'][] = $arrData['posted'];
				
				$arrPreTotal['payment'][] = $arrData['pre_payment'];
				$arrPreTotal['applied'][] = $pre_applied;
				$arrPreTotal['unapplied'][] = $pre_unapplied;
				
			}
		}
	}
	$tot_payment_amt = numberformat(array_sum($arrCIOTotal['payment']),2);
	$tot_applied_amt = numberformat(array_sum($arrCIOTotal['applied']),2);
	$tot_unapplied_amt = numberformat(array_sum($arrCIOTotal['unapplied']),2);
	$tot_posted_amt = numberformat(array_sum($arrCIOTotal['posted']),2);
	
	$arr=array();
	$arr[]='';
	$arr[]='';
	$arr[]='';
	$arr[]='';
	$arr[]='Total';
	$arr[]=numberformat(array_sum($arrTotal['cio_payment']),2);
	$arr[]=numberformat(array_sum($arrTotal['cio_applied']),2);
	$arr[]=numberformat(array_sum($arrTotal['cio_payment']) - array_sum($arrTotal['cio_applied']),2);
	$arr[]='';
	$arr[]='';
	$arr[]=numberformat(array_sum($arrTotal['pre_payment']),2);
	$arr[]=numberformat(array_sum($arrTotal['pre_applied']),2);
	$arr[]=numberformat(array_sum($arrTotal['pre_payment']) - array_sum($arrTotal['pre_applied']),2);
	$arr[]='';
	$arr[]='';
	$arr[]=numberformat(array_sum($arrTotal['posted']),2);
	$arr[]='';
	fputcsv($fp,$arr, ",","\"");
				
	$main_block_page.= '
	<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
	<tr class="subtotal">
		<td style="text-align:right;" colspan="5">Total</td>
		<td style="text-align:right; width:'.$w_cols.';" >'.numberformat(array_sum($arrTotal['cio_payment']),2).'</td>
		<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['cio_applied']),2).'</td>
		<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['cio_payment']) - array_sum($arrTotal['cio_applied']),2).'</td>
		<td style="text-align:right; width:'.$w_cols.';"></td>
		<td style="text-align:right; width:'.$w_cols.';"></td>
		<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['pre_payment']),2).'</td>
		<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['pre_applied']),2).'</td>
		<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['pre_payment']) - array_sum($arrTotal['pre_applied']),2).'</td>
		<td style="text-align:right; width:'.$w_cols.';"></td>
		<td style="text-align:right; width:'.$w_cols.';"></td>
		<td style="text-align:right; width:'.$w_cols.';">'.numberformat(array_sum($arrTotal['posted']),2).'</td>
		<td style="text-align:right; width:'.$w_cols.';">&nbsp;</td>
	</tr>	
	<tr><td colspan="'.$total_cols.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
	</table>';

	$main_block_pdf.= '
	<tr><td colspan="'.$total_cols_pdf.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
	<tr class="subtotal">
		<td style="text-align:right;" colspan="4">Total</td>
		<td style="text-align:right; width:'.$w_cols_pdf.';" >'.numberformat(array_sum($arrTotal['cio_payment']),2).'</td>
		<td style="text-align:right; width:'.$w_cols_pdf.';">'.numberformat(array_sum($arrTotal['cio_applied']),2).'</td>
		<td style="text-align:right; width:'.$w_cols_pdf.';">'.numberformat(array_sum($arrTotal['cio_payment']) - array_sum($arrTotal['cio_applied']),2).'</td>
		<td style="text-align:right; width:'.$w_cols_pdf.';"></td>
		<td style="text-align:right; width:'.$w_cols_pdf.';">'.numberformat(array_sum($arrTotal['pre_payment']),2).'</td>
		<td style="text-align:right; width:'.$w_cols_pdf.';">'.numberformat(array_sum($arrTotal['pre_applied']),2).'</td>
		<td style="text-align:right; width:'.$w_cols_pdf.';">'.numberformat(array_sum($arrTotal['pre_payment']) - array_sum($arrTotal['pre_applied']),2).'</td>
		<td style="text-align:right; width:'.$w_cols_pdf.';"></td>
		<td style="text-align:right; width:'.$w_cols_pdf.';">'.numberformat(array_sum($arrTotal['posted']),2).'</td>
		<td style="text-align:right; width:'.$w_cols_pdf.';">&nbsp;</td>
	</tr>	
	<tr><td colspan="'.$total_cols_pdf.'" style="height:2px; padding: 0px; background: #009933;"></td></tr>
	</table>';	
}
//---------END DISPLAY CI/CO AND POSTED PAYMENTS BLOCK-------------------------

$page_data='<br>';
}

//REFUND AMOUNTS (POSTED)
$totPostedRefundAmt=0;
if(count($arrPostedRefundData)){ 
	$csv_data_part='';
	$dataExists=1;
	
	$arr=array();
	$arr[]='Refunds of Posted Payments (All refund amounts done between selected date range.)';
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]='Patient Name-ID';
	$arr[]='DOS';
	$arr[]='E.ID';
	$arr[]='CPT Code';
	$arr[]='Refund Date';
	$arr[]='Refund';
	$arr[]='Method';
	$arr[]='Opr';
	fputcsv($fp,$arr, ",","\"");
		
	foreach($arrPostedRefundData as $encounter_id =>$encData){
		foreach($encData as $detailsArr){	
		
			$amtPaid = $refundAmt = 0;
			$cptCodes='';

			$date_of_service = $detailsArr['date_of_service'];
			$patientName = core_name_format($detailsArr['lname'], $detailsArr['fname'], $detailsArr['mname']);
			$opr_applied=$detailsArr[''];
			
			$patientName .= " - ".$detailsArr['patient_id'];
			$cptCode = $detailsArr['cpt_code'];
			$refundDate = $detailsArr['dateApplied'];
			
			$refundAmt = $detailsArr['amountApplied'];
			$refundMethod = $detailsArr['payment_mode'];
			
			$totPostedRefundAmt+=$refundAmt;

			//--- PDF FILE DATA ----
			$csv_data_part .='<tr>
				<td class="text_10" bgcolor="#FFFFFF">'.$patientName.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="center">'.$date_of_service.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="center">'.$encounter_id.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="left">'.$cptCode.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="center">'.$refundDate.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($refundAmt,2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$refundMethod.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$userNameTwoCharArr[$detailsArr['operatorApplied']].'</td>
			</tr>';
			
			$arr=array();
			$arr[]=$patientName;
			$arr[]=$date_of_service;
			$arr[]=$encounter_id;
			$arr[]=$cptCode;
			$arr[]=$refundDate;
			$arr[]=$CLSReports->numberFormat($refundAmt,2);
			$arr[]=$refundMethod;
			$arr[]=$userNameTwoCharArr[$detailsArr['operatorApplied']];
			fputcsv($fp,$arr, ",","\"");
		}
	}
	$arr=array();
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]='Total';
	$arr[]=$CLSReports->numberFormat($totPostedRefundAmt,2);
	$arr[]="";
	$arr[]="";
	fputcsv($fp,$arr, ",","\"");
	//MAIN TOTAL
	$page_data.='
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >		
	<tr id="heading_orange"><td class="text_b_w" colspan="8">Refunds of Posted Payments (All refund amounts done between selected date range.)</td></tr>
	<tr>
		<td class="text_b_w" style="text-align:left; width:200px">Patient Name-ID</td>
		<td class="text_b_w" style="text-align:center; width:120px">DOS</td>
		<td class="text_b_w" style="text-align:center; width:120px">E.ID</td>
		<td class="text_b_w" style="text-align:center; width:120px">CPT Code</td>
		<td class="text_b_w" style="text-align:center; width:120px">Refund Date</td>
		<td class="text_b_w" style="text-align:right; width:120px">Refund &nbsp;</td>
		<td class="text_b_w" style="text-align:center; width:120px">Method</td>
		<td class="text_b_w" style="text-align:center; width:120px">Opr</td>
	</tr>
	'.$csv_data_part.'
	<tr><td colspan="8" class="total-row"></td></tr>
	<tr>
		<td class="text_12" bgcolor="#FFFFFF"></td>
		<td class="text_12" bgcolor="#FFFFFF"></td>
		<td class="text_12" bgcolor="#FFFFFF"></td>
		<td class="text_12" bgcolor="#FFFFFF"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right">Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($totPostedRefundAmt,2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
		<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td colspan="8" class="total-row"></td></tr>
	</table>';
}
//REFUND AMOUNTS (CI/CO and PRE-PAYMENTS)
if(sizeof($arrRefundOthers)>0){
	$dataExists=1;
	$pdfData2 = $csvFileData2 = '';
	$arrTot = array();
	$arr=array();
	$arr[]='Refunds - CI/CO and Pre-Payment (Refunds done between selected date range but payments collected before date range.)';
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]='Patient Name-ID';
	$arr[]='Paid DOS';
	$arr[]='Refund Date';
	$arr[]='Type';
	$arr[]='Payment';
	$arr[]='Refund';
	$arr[]='Method';
	$arr[]='Opr';
	fputcsv($fp,$arr, ",","\"");	
	
	foreach($arrRefundOthers as $patId){
		$patientName='';
		// CI/CO
		foreach($arrCICORefunds[$patId] as $cicoDetId => $detData){
			
			$patientName = core_name_format($detData['lname'], $detData['fname'], $detData['mname']);
			$patientName.= ' - '.$patId;
			
			$paidDate = $detData['pay_date'];
			$paidAmt =  $detData['pay_amt'];
			$arrTot['paidAmt']+= $paidAmt;
			
			foreach($arrCICORefundsDet[$cicoDetId] as $sno => $patDetails){
				$arrTot['refAmt']+= $patDetails['ref_amt'];
				
				//PAGE
				$csvFileData2 .='<tr>
				<td class="text_10" bgcolor="#FFFFFF">'.$patientName.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="center">'.$paidDate.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="center">'.$patDetails['ref_date'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left">Check In/Out</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($paidAmt,2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$patDetails['method'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$userNameTwoCharArr[$patDetails['entered_by']].'</td>
				</tr>';	
				
				$arr=array();
				$arr[]=$patientName;
				$arr[]=$paidDate;
				$arr[]=$patDetails['ref_date'];
				$arr[]='Check In/Out';
				$arr[]=$CLSReports->numberFormat($paidAmt,2);
				$arr[]=$CLSReports->numberFormat($patDetails['ref_amt'],2);
				$arr[]=$patDetails['method'];
				$arr[]=$userNameTwoCharArr[$patDetails['entered_by']];
				fputcsv($fp,$arr, ",","\"");	
			}
		}
		unset($arrCICORefunds[$patId]);

		// PRE-PAYMENTS
		foreach($arrPMTRefunds[$patId] as $pmtId => $detData){
			
			$patientName = core_name_format($detData['lname'], $detData['fname'], $detData['mname']);
			$patientName.= ' - '.$patId;
			
			$paidDate = $detData['pay_date'];
			$paidAmt =  $detData['pay_amt'];
			$arrTot['paidAmt']+= $paidAmt;
			
			foreach($arrPMTRefundsDet[$pmtId] as $sno => $patDetails){
				$arrTot['refAmt']+= $patDetails['ref_amt'];
				
				//PAGE
				$csvFileData2 .='<tr>
					<td class="text_10" bgcolor="#FFFFFF">'.$patientName.'</td>
					<td class="text_10" bgcolor="#FFFFFF" align="center">'.$paidDate.'</td>
					<td class="text_10" bgcolor="#FFFFFF" align="center">'.$patDetails['ref_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left">Pre-Payment</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($paidAmt,2).'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($patDetails['ref_amt'],2).'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$patDetails['method'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$userNameTwoCharArr[$patDetails['entered_by']].'</td>
				</tr>';
				$arr=array();
				$arr[]=$patientName;
				$arr[]=$paidDate;
				$arr[]=$patDetails['ref_date'];
				$arr[]='Pre-Payment';
				$arr[]=$CLSReports->numberFormat($paidAmt,2);
				$arr[]=$CLSReports->numberFormat($patDetails['ref_amt'],2);
				$arr[]=$patDetails['method'];
				$arr[]=$userNameTwoCharArr[$patDetails['entered_by']];
				fputcsv($fp,$arr, ",","\"");	
			}
		}
		unset($arrPMTRefunds[$patId]);
	}
		
	$totOtherRefundAmt= $arrTot['refAmt'];
	
	$arr=array();
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]='Total';
	$arr[]=$CLSReports->numberFormat($arrTot['paidAmt'],2);
	$arr[]=$CLSReports->numberFormat($arrTot['refAmt'],2);
	$arr[]="";
	$arr[]="";
	fputcsv($fp,$arr, ",","\"");
	
	// MAIN TOTAL
	$page_data.=' 
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
	<tr id="heading_orange"><td class="text_b_w" colspan="8">Refunds - CI/CO and Pre-Payment (Refunds done between selected date range but payments collected before date range.)</td></tr>
	<tr>
		<td class="text_b_w" style="text-align:left; width:200px">Patient Name-ID</td>
		<td class="text_b_w" style="text-align:center; width:120px">Paid Date</td>
		<td class="text_b_w" style="text-align:center; width:120px">Refund Date</td>
		<td class="text_b_w" style="text-align:left; width:120px">Type</td>
		<td class="text_b_w" style="text-align:right; width:120px">Payment &nbsp;</td>
		<td class="text_b_w" style="text-align:right; width:120px">Refund &nbsp;</td>
		<td class="text_b_w" style="text-align:center; width:120px">Method</td>
		<td class="text_b_w" style="text-align:center; width:120px">Opr</td>
	</tr>
	'.$csvFileData2.'
	<tr><td class="total-row" colspan="8"></td></tr>
	<tr>
		<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right"></td>
		<td class="text_12b" bgcolor="#FFFFFF" align="right">Total:</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTot['paidAmt'],2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF" style="text-align:right;padding-right:10px;"> '.$CLSReports->numberFormat($arrTot['refAmt'],2).'</td>
		<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
		<td class="text_12b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td class="total-row" colspan="8"></td></tr>
	</table>';
}	


$postpaymhdtd = '';
if($DateRangeFor=='dor'){
	$postpaymhdtd = 'After '.$End_date;
}

//DELETED POSTED RECORDS
$content_part= $deleted_html='';
$delDataExists=false;
$delPostedBlockTotal=0;
if(sizeof($arrDelPostedAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$colspan= 5;

	$total_cols = 3;
	$first_col = "16";
	$last_col = "40";
	$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
	
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);

	$grand_first_col=$first_col;
	$grand_w_cols=$w_cols;	
	
	$first_col = $first_col.'%';
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	
	$arr=array();
	$arr[]='Deleted Posted Payments '.$postpaymhdtd;
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]='Patient Name-Id';
	$arr[]='Enc. Id';
	$arr[]='DOS';
	$arr[]='Deleted Amount';
	fputcsv($fp,$arr, ",","\"");
	
	foreach($arrDelPostedAmounts as $grpId => $grpData){
		$rowTot=0;
		$firstGroupName='';
		$firstGrpTotal=array();
		$firstGroupName = $arrAllUsers[$grpId];

		$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Physician: '.$firstGroupName.'</td></tr>';
	
		foreach($grpData as $eid => $grpDetail){
			$pName = explode('~', $grpDetail['pat_name']);
			$patient_name_arr = array();
			$patient_name_arr["LAST_NAME"] = $pName[3];
			$patient_name_arr["FIRST_NAME"] = $pName[1];
			$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
			$patient_name = changeNameFormat($patient_name_arr);
			$patient_name.= ' - '.$pName[0];
			
			$firstGrpTotal['del_amount']+=	$grpDetail['del_amount'];
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$eid.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['dos'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
			</tr>';	
			
			$arr=array();
			$arr[]=$patient_name;
			$arr[]=$eid;
			$arr[]=$grpDetail['dos'];
			$arr[]=$CLSReports->numberFormat($grpDetail['del_amount'],2);
			fputcsv($fp,$arr, ",","\"");
		}
	
		$arrPostedPayTot['del_amount']+=$firstGrpTotal['del_amount'];
		
		$content_part.=' 
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">&nbsp;</td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
	}

	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">		
	<tr id="heading_orange"><td colspan="'.$colspan.'">Deleted Posted Payments '.$postpaymhdtd.'</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Enc. Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOS</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Deleted Amount</td>
		<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
	</tr>
	</table>';

	$delPostedBlockTotal = $arrPostedPayTot['del_amount'];

	//PAGE HTML
	$page_data .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted Posted Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPostedPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>';
	
	//PDF HTML
	$del_posted_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table class="text_b_w" style="width: 100%;">
			<tr>
				<td class="text_b_w" style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted Posted Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>
	</page>';	
	
}

//DELETED CI/CO
$content_part= $deleted_html='';
$arrDelPatPayTot=array();
if(sizeof($arrDelCICOAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$colspan= 5;

	$total_cols = 3;
	$first_col = "16";
	$last_col = "40";
	$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
	
	$first_col = 100 - (($total_cols * $w_cols) + $last_col);
	
	$grand_first_col=$first_col;
	$grand_w_cols=$w_cols;	
	
	$first_col = $first_col.'%';
	$w_cols = $w_cols."%";
	$last_col = $last_col."%";
	
	$arr=array();
	$arr[]='Deleted CI/CO Payments '.$postpaymhdtd;
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]='Patient Name-Id';
	$arr[]='';
	$arr[]='Paid Date';
	$arr[]='Deleted Amount';
	fputcsv($fp,$arr, ",","\"");
	
	foreach($arrDelCICOAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $arrAllUsers[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Physician - '.$firstGroupName.'</td></tr>';
		
			foreach($grpData as $eid => $grpDetail){
				$pName = explode('~', $grpDetail['pat_name']);
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				
				$firstGrpTotal['del_amount']+=	$grpDetail['del_amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';	
				$arr=array();
				$arr[]=$patient_name;
				$arr[]='';
				$arr[]=$grpDetail['paid_date'];
				$arr[]=$CLSReports->numberFormat($grpDetail['del_amount'],2);
				fputcsv($fp,$arr, ",","\"");

				
			}
		
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';

		}
	
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">		
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Deleted CI/CO Payments '.$postpaymhdtd.'</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">Paid Date</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Deleted Amount</td>
		<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
	</tr>
	</table>';

	$delCICOBlockTotal = $arrDelPatPayTot['del_amount'];
		
	//PAGE HTML
	$page_data .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted CI/CO Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>';
	
	//PDF HTML
	$del_cico_PDF.='
	<page backtop="24mm" backbottom="10mm">			
	<page_footer>
		<table class="text_b_w" style="width: 100%;">
			<tr>
				<td class="text_b_w" style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$mainHeaderPDF
	.$header.'
	</page_header>
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted CI/CO Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>
	</page>';
}

//DELETED PRE_PAYMENTS
$content_part= $deleted_html='';
$arrDelPatPayTot=array();
if(sizeof($arrDelPrePayAmounts)>0){
	$dataExists=true;
	$delDataExists=true;
	$colspan= 5;

	$total_cols = 3;
	$first_col = "16";
	$last_col = "40";
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
	$arr[]='';
	$arr[]='DOT';
	$arr[]='Deleted Amount';
	fputcsv($fp,$arr, ",","\"");
	
	foreach($arrDelPrePayAmounts as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $arrAllUsers[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Physician - '.$firstGroupName.'</td></tr>';
		
			foreach($grpData as $eid => $grpDetail){
				$pName = explode('~', $grpDetail['pat_name']);
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				
				$firstGrpTotal['del_amount']+=	$grpDetail['del_amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.$CLSReports->numberFormat($grpDetail['del_amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';	
					
				$arr=array();
				$arr[]=$patient_name;
				$arr[]='';
				$arr[]=$grpDetail['entered_date'];
				$arr[]=$CLSReports->numberFormat($grpDetail['del_amount'],2);
				fputcsv($fp,$arr, ",","\"");
				
			}
		
			$arrDelPatPayTot['del_amount']+=	$firstGrpTotal['del_amount'];
		
			$content_part.=' 
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal['del_amount'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td style="height:2px; padding: 0px; background: #009933;"  colspan="'.$colspan.'"></td></tr>';
		}
	// TOTAL
	// HEADER
	$header='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">		
	<tr id=""><td class="text_b_w" colspan="'.$colspan.'">Deleted Pre-Payments '.$postpaymhdtd.'</td></tr>
	<tr id="">
		<td class="text_b_w" style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">&nbsp;</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:center;">DOT</td>
		<td class="text_b_w" style="width:'.$w_cols.'; text-align:right;">Deleted Amount</td>
		<td class="text_b_w" style="width:'.$last_col.';">&nbsp;</td>
	</tr>
	</table>';

	
	$delPrePayBlockTotal = $arrDelPatPayTot['del_amount'];
	
	//PAGE HTML
	$page_data .=
	$header.' 
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
	'.$content_part.'
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>
	<tr>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;" colspan="3">Deleted Pre-Payments Total&nbsp;:</td>
		<td class="text_10b" bgcolor="#FFFFFF" class="text_b_w" style="text-align:right;">'.$CLSReports->numberFormat($arrDelPatPayTot['del_amount'],2).'&nbsp;</td>
		<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="'.$colspan.'"></td></tr>	
	</table>';
}


if($hasData == 1){
	$total_cols = 5;
	$first_col = "17";
	$w_cols = $w_cols1 = floor((100 - ($first_col))/($total_cols-1));
	$first_col = 100 - ( (($total_cols-1) * $w_cols));
	
	$w_cols = $w_cols."%";
	$first_col = $first_col."%";

	//REFUND AMOUNTS
	$tot_ref_amounts= $totPostedRefundAmt+$totOtherRefundAmt; 

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
	$grd_date_applied = numberformat(($arrAppAmtInDate['cio_payment'] + $arrAppAmtInDate['pre_payment'] + array_sum($arrCIOTotal['posted'])) - $delPostedBlockTotal, 2);
	$grd_collec_applied = numberformat(array_sum($arrCIOTotal['applied']) + array_sum($arrPreTotal['applied']) + array_sum($arrCIOTotal['posted']), 2);
	$grd_collec_unapplied = numberformat(array_sum($arrCIOTotal['unapplied']) + array_sum($arrPreTotal['unapplied']),2);
	
	$tot_ref_amounts=numberformat($tot_ref_amounts,2);
	

	//PAYMENT BREAKDOWN
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
			$ccTypeRows .= '<td class="text_10b" style="width:'.$first_col.'; text-align:right">'.$CCType.'	</td>';
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
	if($delPostedBlockTotal>0 || $delPostedBlockTotal<0){
		$totDelPayHTML='
		<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
		<tr class="data">	
			<td class="text_10b" style="width:$first_col; text-align:right">Deleted Posted Payments</td>	
			<td class="text_10b" style="width:$w_cols; text-align:right"></td>	
			<td class="text_10b" style="width:$w_cols; text-align:right">'.numberformat($delPostedBlockTotal,2).'</td>	
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
				<td class="text_b_w" style="width:$first_col; text-align:right">Grand Totals</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_payment</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_date_applied</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_collec_applied</td>	
				<td class="text_10b" style="width:$w_cols; text-align:right">$grd_collec_unapplied</td>	
			</tr>
			<tr><td colspan="$total_cols" style="height:2px; padding: 0px; background: #009933;"></td></tr>
			<tr id="heading_orange">	
				<td align="left" colspan="$total_cols">Payment Breakdown</td>	
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
				<td align="left"  colspan="'.$total_cols.'">Manual applied amounts extracted from "Applied for Date Range" column</td>	
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
		<tr id="heading_orange"><td colspan="'.$colspan.'">CI/CO Manually Applied Amounts</td></tr>
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
				$patient_name = $arrPatient[$grpDetail['pat_name']];
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
		<tr id="heading_orange"><td colspan="'.$colspan.'">Pre-Payments Manually Applied Amounts</td></tr>
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
$main_block_page.
$page_data.
$manually_applied_cico_html.
$manually_applied_pre_pay_html;
?>