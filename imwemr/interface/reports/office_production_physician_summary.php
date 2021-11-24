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
$cols=14;
$firstColWidth=9;
$perColWidth=5;

$colWidth=round((100-($firstColWidth+ ($perColWidth*3)))/ ($cols-4),2);
$firstColWidth.='%';
$perColWidth.='%';
$colWidth.='%';
//-------------------------

$grpTitle = 'Provider';
if($viewBy=='grpby_crediting_physician'){
    $grpTitle= 'Crediting Provider';
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
	$data_output.="Office Production Report (".$grpTitle." Summary)".$pfx;
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
    $data_output.="Charges".$pfx;
	$data_output.="Individual ".$grpTitle." Charges %".$pfx;
    $data_output.="Service Charges".$pfx;
    $data_output.="Material Charges".$pfx;
    $data_output.="Other Charges".$pfx;
	$data_output.="Adjustment".$pfx;
    $data_output.="Net Charges".$pfx;
    $data_output.="Individual ".$grpTitle." Net Charges %".$pfx;
    $data_output.="Credit".$pfx;
    $data_output.="Refund".$pfx;
    $data_output.="Net Credit".$pfx;
	$data_output.="Individual ".$grpTitle." Net Credit %".$pfx;	
	$data_output.="Net AR Diff.".$pfx;
    $data_output.="\n"; 
        
    $arrTotals=array();
    foreach($mainResArr as $firstGrpId => $firstGrpData){	
        $printFile = true;
        $firstGrpName = $providerNameArr[$firstGrpId];
        
        $chargesPercentage= round(($firstGrpData['charges']*100)/$mainResTotalCharges);
        $netCharges= $firstGrpData['charges']-$arrAdjustmentAmt[$firstGrpId]; 
        $netChargesPercentage= round(($netCharges*100)/$totalNetCharges); 
        $netCredit=$arrPaymentAmt[$firstGrpId]-$arrRefundAmt[$firstGrpId];
        $netCreditPercentage= round(($netCredit*100)/$totalNetCredit); 
        $netArDiff=$netCharges-$netCredit;
        
        $html_part.='<tr>
            <td class="text_10" style="background:#FFFFFF; width:'.$firstColWidth.'">'.$firstGrpName.'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($firstGrpData['charges'],2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$perColWidth.'">'.$chargesPercentage.'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($firstGrpData['charges_service'],2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($firstGrpData['charges_material'],2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($firstGrpData['charges_other'],2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($arrAdjustmentAmt[$firstGrpId],2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($netCharges,2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$perColWidth.'">'.$netChargesPercentage.'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($arrPaymentAmt[$firstGrpId],2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($arrRefundAmt[$firstGrpId],2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($netCredit,2).'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$perColWidth.'">'.$netCreditPercentage.'</td>
            <td class="text_10" style="background:#FFFFFF; text-align:right; width:'.$colWidth.'">'.$CLSReports->numberFormat($netArDiff,2).'</td>
        </tr>';

        //TOTALS
        $arrTotals['charges']+=$firstGrpData['charges'];
        $arrTotals['charges_service']+=$firstGrpData['charges_service'];
        $arrTotals['charges_material']+=$firstGrpData['charges_material'];
        $arrTotals['charges_other']+=$firstGrpData['charges_other'];
        $arrTotals['adjustment']+=$arrAdjustmentAmt[$firstGrpId];
        $arrTotals['net_charges']+=$netCharges;
        $arrTotals['payments']+=$arrPaymentAmt[$firstGrpId];
        $arrTotals['refund']+=$arrRefundAmt[$firstGrpId];
        $arrTotals['net_credit']+=$netCredit;
        $arrTotals['net_ar_diff']+=$netArDiff;

        //FOR CSV
        $data_output.='"'.$firstGrpName.'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($firstGrpData['charges'],2).'"'.$pfx;
        $data_output.='"'.$chargesPercentage.'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($firstGrpData['charges_service'],2).'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($firstGrpData['charges_material'],2).'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($firstGrpData['charges_other'],2).'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($arrAdjustmentAmt[$firstGrpId],2).'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($netCharges,2).'"'.$pfx;
        $data_output.='"'.$netChargesPercentage.'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($arrPaymentAmt[$firstGrpId],2).'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($arrRefundAmt[$firstGrpId],2).'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($netCredit,2).'"'.$pfx;
        $data_output.='"'.$netCreditPercentage.'"'.$pfx;
        $data_output.='"'.$CLSReports->numberFormat($netArDiff,2).'"'.$pfx;
        $data_output.="\n";                 
    }	

    $html_part.='
    <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
    <tr>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Total:</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrTotals['charges'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrTotals['charges_service'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrTotals['charges_material'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTotals['charges_other'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($arrTotals['adjustment'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['net_charges'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['payments'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['refund'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['net_credit'],2).'</td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>
        <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($arrTotals['net_ar_diff'],2).'</td>
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
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$perColWidth.';">Individual '.$grpTitle.' Charges %</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Service Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Material Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Other Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Adjustment</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Net Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$perColWidth.';">Individual '.$grpTitle.' Net Charges %</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Credit</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Refund</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Net Credit</td>
        <td class="text_b_w" style="text-align:center; width:'.$perColWidth.';">Individual '.$grpTitle.' Net Credit %</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Net AR Diff.</td>
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
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$perColWidth.';">Individual '.$grpTitle.' Charges %</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Service Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Material Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Other Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Adjustment</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Net Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$perColWidth.';">Individual '.$grpTitle.' Net Charges %</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Credit</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Refund</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Net Credit</td>
        <td class="text_b_w" style="text-align:center; width:'.$perColWidth.';">Individual '.$grpTitle.' Net Credit %</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.';">Net AR Diff.</td>
     </tr>
	</table>
	</page_header>
	    <table style="width:100%;" class="rpt_table rpt_table-bordered">'
	    .$html_part.
	    '</table>
    </page>';   

    //FOR CSV
    $data_output.='Total:'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['charges'],2).'"'.$pfx;
    $data_output.=' '.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['charges_service'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['charges_material'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['charges_other'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['adjustment'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['net_charges'],2).'"'.$pfx;
    $data_output.=' '.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['payments'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['refund'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['net_credit'],2).'"'.$pfx;
    $data_output.=' '.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($arrTotals['net_ar_diff'],2).'"'.$pfx;
    $data_output.="\n"; 

    @fwrite($fp,$data_output);
    fclose($fp);    
}


?>