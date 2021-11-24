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
//ob_start();
//$providerIdArr = array_keys($mainResArr);

$pdf_page_content = NULL;
$csvFileData = NULL;
$grandTotalAmtArr = array();

$j = $k = 0; $chartData = array();
$chartProviderId = array();
$chartFacilityName = array();
$chartFacilityId = array();

//COLSPAN AND COL WIDTH
$cols=(empty($cpt_cat_2) === false)? 11 :10;
$firstColWidth=9;
$colWidth=round((100-($firstColWidth))/ ($cols-1),2);
$firstColWidth.='%';
$colWidth.='%';
//-------------------------

$grpTitle = 'Provider';
if($viewBy=='grpby_crediting_physician'){
    $grpTitle= 'Crediting Provider';
}else if($viewBy=='grpby_location'){
    $grpTitle= 'Location';
}

$tdCPTCat2Title='';
//IF CAT2 NOT SELECTED THEN AVOID EXTRA LOOPING.
if(empty($cpt_cat_2) === false){
	$arr_cpt_cat_2=explode(',', $cpt_cat_2);
	foreach($arr_cpt_cat_2 as $cat2){
		if($cat2==1){
			$arr_cpt_cats[1]='Service';
		}
		if($cat2==2){
			$arr_cpt_cats[2]='Material';
		}		
	}
	$colspan=10;
	$subColspan=9;
}
else{
	$arr_cpt_cats=array('0'=>'Other');
}


if(sizeof($mainResArr)>0)
{
	//MAKING OUTPUT DATA
	$file_name="office_production_".time().".csv";
	$csv_file_name= write_html("", $file_name);
	$pfx=",";
	//CSV FILE NAME
	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');
	$data_output.="Office Production Report (".$grpTitle." Detail)".$pfx;
	$data_output.="$dayReport ($search) From : $Sdate To : $Edate"." Time: $hourFromL-$hourToL".$pfx;
	$data_output.="Created by $opInitial on $curDate".$pfx;
	$data_output.="Group : $selgroup".$pfx;
	$data_output.="\n";
	
	$data_output.="Facility : $selFac".$pfx;
	$data_output.="Filing Phy. : $selPhy".$pfx;
	$data_output.="Selected Oper. : $selOpr".$pfx;
	$data_output.="Credit Phy. : $selCrPhy".$pfx;
	$data_output.="\n";
	
	$data_output.="Insurance : $selInsurance".$pfx;
	$data_output.="Ins. Type : $selInsType".$pfx;
	$data_output.="CPT : $selCPT".$pfx;
	$data_output.="ICD10 : $selDX10".$pfx;
	$data_output.="\n";
	
    $data_output.=$grpTitle.$pfx;
    if(empty($cpt_cat_2) === false){
        $data_output.="CPT Category2".$pfx;
    }    
    $data_output.="Charges".$pfx;
    $data_output.="Ins. Paid".$pfx;
    $data_output.="Pat. Paid".$pfx;
	$data_output.="Total Paid".$pfx;	
    $data_output.="Adjustment".$pfx;
    $data_output.="Refund".$pfx;
    $data_output.="Ins. Due".$pfx;
    $data_output.="Pat. Due".$pfx;
	$data_output.="Balance".$pfx;
    $data_output.="\n"; 
     

    $arrTotals=array();
    foreach($mainResArr as $firstGrpId => $firstGrpData)
    {	

        foreach($arr_cpt_cats as $cat2_key=> $cat2_name)
        {	

            $printFile = true;
            if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician'){    
                $firstGrpName = $providerNameArr[$firstGrpId];
            }else if($viewBy=='grpby_location'){
                $firstGrpName = $arrAllFacilities[$firstGrpId];
            }
            
            //PAYMENTS
            $insPaid=$patPayDetArr[$firstGrpId][$cat2_key]['insPaid']+$pay_crd_deb_arr_summary[$firstGrpId][$cat2_key]['Insurance'];
            $patPaid=$patPayDetArr[$firstGrpId][$cat2_key]['patPaid']+$pay_crd_deb_arr_summary[$firstGrpId][$cat2_key]['Patient'];
            $totalPaid=$insPaid+$patPaid;

            $tdCPTCat2Data='';
			if(empty($cpt_cat_2) === false)
			{
				$tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$cat2_name.'</td>';
			}
            
            $html_part.='<tr>
                <td class="text_10" style="background:#FFFFFF; width:'.$firstColWidth.'">'.$firstGrpName.'</td>
                '.$tdCPTCat2Data.'
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($firstGrpData[$cat2_key]['charges'],2).'</td>
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($insPaid,2).'</td>
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($patPaid,2).'</td>
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($totalPaid,2).'</td>
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($arrAdjustmentAmt[$firstGrpId][$cat2_key],2).'</td>
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($arrRefundAmt[$firstGrpId][$cat2_key],2).'</td>
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($firstGrpData[$cat2_key]['ins_due'],2).'</td>
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($firstGrpData[$cat2_key]['pat_due'],2).'</td>
                <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($firstGrpData[$cat2_key]['balance'],2).'</td>
            </tr>';

            //TOTALS
            $arrTotals['charges']+=$firstGrpData[$cat2_key]['charges'];
            $arrTotals['ins_paid']+=$insPaid;
            $arrTotals['pat_paid']+=$patPaid;
            $arrTotals['total_paid']+=$totalPaid;
            $arrTotals['adjustment']+=$arrAdjustmentAmt[$firstGrpId][$cat2_key];
            $arrTotals['refund']+=$arrRefundAmt[$firstGrpId][$cat2_key];
            $arrTotals['pat_due']+=$firstGrpData[$cat2_key]['pat_due'];
            $arrTotals['ins_due']+=$firstGrpData[$cat2_key]['ins_due'];
            $arrTotals['balance']+=$firstGrpData[$cat2_key]['balance'];

            //FOR CSV
            $data_output.='"'.$firstGrpName.'"'.$pfx;
            if(empty($cpt_cat_2) === false){
                $data_output.='"'.$cat2_name.'"'.$pfx;
            }
            $data_output.='"'.$CLSReports->numberFormat($firstGrpData[$cat2_key]['charges'],2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($insPaid,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($patPaid,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($totalPaid,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($arrAdjustmentAmt[$firstGrpId][$cat2_key],2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($arrRefundAmt[$firstGrpId][$cat2_key],2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($firstGrpData[$cat2_key]['ins_due'],2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($firstGrpData[$cat2_key]['pat_due'],2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($firstGrpData[$cat2_key]['balance'],2).'"'.$pfx;
            $data_output.="\n";
        }
    }	

    $tdCPTCat2Data='';
    if(empty($cpt_cat_2) === false)
    {
        $tdCPTCat2Data='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>';
        $tdCPTCat2Title='<td class="text_b_w" style="text-align:left; width:'.$colWidth.'%">CPT CAT2</td>';
    }

    $html_part.='
    <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
    <tr>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Total:</td>
        '.$tdCPTCat2Data.'
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrTotals['charges'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrTotals['ins_paid'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrTotals['pat_paid'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTotals['total_paid'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTotals['adjustment'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['refund'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['ins_due'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['pat_due'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['balance'],2).'</td>
    </tr>
    <tr><td class="total-row" colspan="'.$cols.'"></td></tr>';		


    $totalLabel='Total';
    $showTotalAgain=0;
    $total_charges=$grand_total_amt2;
    $total_balance=$grand_total_balance2;

    $header_part='
    <table class="rpt_table rpt_table-bordered rpt_padding">
        <tr class="rpt_headers">
            <td class="rptbx1" style="text-align:left; width:33%">Office Production Report ('.$grpTitle.' Summary)</td>
            <td class="rptbx2" style="text-align:left; width:34%">'.$dayReport.' ('.$search.') From : '.$Sdate.' To : '.$Edate.'</td>
            <td class="rptbx3" style="text-align:left; width:33%">Created by '.$opInitial.' on '.$curDate.'</td>
        </tr>
        <tr class="rpt_headers">
            <td class="rptbx1">Group : '.$selgroup.'</td>
            <td class="rptbx2">Facility : '.$selFac.'</td>
            <td class="rptbx3">Filing Phy. : '.$selPhy.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Oper.: '.$selOpr.'
            </td>
        </tr>
        <tr class="rpt_headers">
            <td class="rptbx1">Credit Phy. : '.$selCrPhy.'</td>
            <td class="rptbx2">Insurance : '.$selInsurance.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Ins. Type : '.$selInsType.'
            </td>
            <td class="rptbx3">ICD10 : '.$selDX10.'</td>
        </tr>
        <tr class="rpt_headers">
            <td class="rptbx1">CPT : '.$selCPT.'</td>
            <td class="rptbx2">CPT Cat2 : '.$selCptCat2.'</td>
            <td class="rptbx3"></td>
        </tr>
    </table>';

    //PAGE DATA
    $page_data=
    $header_part.'
    <table class="rpt_table rpt_table-bordered" style="width:100%;" >
        <tr>
        <td class="text_b_w" style="text-align:center; width:'.$firstColWidth.';">'.$grpTitle.'</td>
        '.$tdCPTCat2Title.'
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Ins Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Pat Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Total Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Adjustment</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Refund</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Ins Due</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Pat Due</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Balance</td>
        </tr>
        '.$html_part.'
    </table>';

    //PDF DATA    
	$pdf_data=
	'<page backtop="35mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>'
	.$header_part.
	'<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%;" >
        <tr>
        <td class="text_b_w" style="text-align:center; width:'.$firstColWidth.';">'.$grpTitle.'</td>
        '.$tdCPTCat2Title.'
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Ins Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Pat Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Total Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Adjustment</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Refund</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Ins Due</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Pat Due</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Balance</td>
        </tr>
	</table>
	</page_header>
	    <table style="width:100%;" class="rpt_table rpt_table-bordered">'
	    .$html_part.
	    '</table>
    </page>';   

    //FOR CSV
    $data_output.='Total:'.$pfx;
    if(empty($cpt_cat_2) === false){
        $data_output.=' '.$pfx;
    }
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['charges'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['ins_paid'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['pat_paid'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['total_paid'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['adjustment'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['refund'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['ins_due'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['pat_due'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['balance'],2).'"'.$pfx;
    $data_output.="\n";

    @fwrite($fp,$data_output);
    fclose($fp);
}


?>