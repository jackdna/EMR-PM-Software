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

$grandTotalAmtArr = array();

//COLSPAN AND COL WIDTH
$cols=8;
$subCol=1;
$groupColWidth=10;

$colWidth=round((100-$groupColWidth)/ ($cols-1),2);

$groupColWidth.='%';
$colWidth.='%';
//-------------------------

//MAKE TITLES FOR CSV FILE
$grpTitle = 'Location';


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
	$data_output.="Office Production Report (By Location per CPT Category Summary)".$pfx;
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
    $data_output.="CPT Category".$pfx;
    $data_output.="Charges".$pfx;
    $data_output.="Ins Payment".$pfx;
    $data_output.="Pat Payment".$pfx;
    $data_output.="Total Payment".$pfx;
    $data_output.="Adjustment".$pfx;
    $data_output.="Refund".$pfx;
    $data_output.="Balance".$pfx;
	$data_output.="\n"; 
    
    foreach($mainResArr as $firstGrpId => $firstGrpData){	
        $printFile=true;
        $facTotalAmtArr=array();
        $firstGrpName = $arrAllFacilities[$firstGrpId];

        $html_part .='<tr><td class="text_b_w" colspan="'.$cols.'">'.$grpTitle.' : '.$firstGrpName.'</td></tr>';

        foreach($firstGrpData as $cptCatId => $cptCatData)
        {
            $cptCatName = $arrAllCptCats[$cptCatId];
            $units=$charges=$patCrdDbt=$insCrdDbt=$patPaidAmt=$insPaidAmt=$totalPaidAmt=$adj_amt=$creditProcAmount=$totalBalance=$refund_amt=0;

            foreach($cptCatData as $chgDetId => $encDataArr)
            {

                $cptCode = $encDataArr['cpt_prac_code'];
                $cpt_desc = $encDataArr['cpt_desc'];
                
                $units+=$encDataArr['units'];
                //CHARGES    
                $charges+=$encDataArr['totalAmt'];
                //CREDIT/DEBIT
                $patCrdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'];			
                $insCrdDbt= $pay_crd_deb_arr[$chgDetId]['Insurance'];
                //PAYMENTS
                $patPaidAmt+=$patPayDetArr[$chgDetId]['patPaid'] + $patCrdDbt;
                $insPaidAmt+= $patPayDetArr[$chgDetId]['insPaid'] + $insCrdDbt;
                //WRITE-OFF & ADJUSTMENTS
                $adj_amt+= $normalWriteOffAmt[$chgDetId] + $arrAdjustmentAmtDet[$chgDetId];
                //REFUND
                $refund_amt+= $arrRefundAmt[$chgDetId];
                //CREDIT - OVER PAYMENT
                $creditProcAmount= $encDataArr['over_payment'];

                //BALANCE
                $balAmt=0;
                if($encDataArr["proc_balance"]>0){
                    $balAmt= $encDataArr['proc_balance'];
                }else{
                    if($encDataArr['over_payment']>0){
                        $balAmt= $encDataArr['proc_balance'] - $encDataArr['over_payment'];
                    }else{
                        $balAmt= $encDataArr['proc_balance'];
                    }
                }
                $totalBalance+=$balAmt;
            }

            $totalPaidAmt=$patPaidAmt+$insPaidAmt;

            //ARRAY LOCATION TOTAL 
            $facTotalAmtArr["charges"]+= $charges;
            $facTotalAmtArr["ins_paid"]+= $insPaidAmt;
            $facTotalAmtArr["pat_paid"]+= $patPaidAmt;
            $facTotalAmtArr["total_paid"]+= $totalPaidAmt;
            $facTotalAmtArr["adj_amt"]+= $adj_amt;
            $facTotalAmtArr["refund_amt"]+= $refund_amt;
            $facTotalAmtArr["balance"]+= $totalBalance;

            $html_part.='
            <tr>
                <td class="text_10" style="background:#FFFFFF; width:'.$groupColWidth.'">'.$cptCatName.'</td>
                <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($charges,2).'</td>
                <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($insPaidAmt,2).'</td>
                <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($patPaidAmt,2).'</td>
                <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($totalPaidAmt,2).'</td>
                <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($adj_amt,2).'</td>
                <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($refund_amt,2).'</td>
                <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($totalBalance,2).'</td>
            </tr>';

            //FOR CSV
            $data_output.='"'.$firstGrpName.'"'.$pfx;
            $data_output.='"'.$cptCatName.'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($charges,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($insPaidAmt,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($patPaidAmt,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($totalPaidAmt,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($adj_amt,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($refund_amt,2).'"'.$pfx;
            $data_output.='"'.$CLSReports->numberFormat($totalBalance,2).'"'.$pfx;
            $data_output.="\n";                  

        }
        //ARRAY GRAND TOTAL 
        $grandTotalAmtArr["charges"]+= $facTotalAmtArr["charges"];
        $grandTotalAmtArr["ins_paid"]+= $facTotalAmtArr["ins_paid"];
        $grandTotalAmtArr["pat_paid"]+= $facTotalAmtArr["pat_paid"];
        $grandTotalAmtArr["total_paid"]+= $facTotalAmtArr["total_paid"];
        $grandTotalAmtArr["adj_amt"]+= $facTotalAmtArr["adj_amt"];
        $grandTotalAmtArr["refund_amt"]+= $facTotalAmtArr["refund_amt"];
        $grandTotalAmtArr["balance"]+= $facTotalAmtArr["balance"];
        
        $html_part.='
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Location Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facTotalAmtArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facTotalAmtArr['ins_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facTotalAmtArr['pat_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facTotalAmtArr['total_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facTotalAmtArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facTotalAmtArr['refund_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facTotalAmtArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>';      
    }

    $header_part='
    <table class="rpt_table rpt_table-bordered rpt_padding">
        <tr class="rpt_headers">
            <td class="rptbx1" style="text-align:left; width:33%">Office Production Rpt (By Location per CPT Category Summary)</td>
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
        <td class="text_b_w" style="text-align:center; width:'.$groupColWidth.'">CPT Category</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Ins Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Pat Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Total Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Adjustment</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Refund</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Balance</td>
    </tr>
        '.$html_part.'
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['total_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['refund_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>        
    </table>';

    //PDF DATA    
	$pdf_data=
	'<page backtop="25mm" backbottom="5mm">
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
        <td class="text_b_w" style="text-align:center; width:'.$groupColWidth.'">CPT Category</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Ins Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Pat Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Total Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Adjustment</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Refund</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Balance</td>
     </tr>
	</table>
	</page_header>
	    <table style="width:100%;" class="rpt_table rpt_table-bordered">'
        .$html_part.
        '<tr><td class="total-row" colspan="'.$cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['total_paid'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['refund_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>        
	    </table>
    </page>'; 

    //FOR CSV
    $data_output.=' '.$pfx;
    $data_output.='Total: '.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['total_paid'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['refund_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['balance'],2).'"'.$pfx;
    $data_output.="\n";

    @fwrite($fp,$data_output);
    fclose($fp);
}


?>
