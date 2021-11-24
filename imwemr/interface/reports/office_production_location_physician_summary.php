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

//COLSPAN AND COL WIDTH
$cols=10;
$subCol=1;
$pdf_cols=10;
$pdf_subCol=1;
$groupColWidth=8;

if(empty($cpt_cat_2) === false){
    $cols+=1;
    $subCol+=1;
    $pdf_cols+=1;
    $pdf_subCol+=1;
}

$colWidth=round((100-$groupColWidth)/ ($cols-1),2);
$pdfColWidth=round((100-$groupColWidth)/ ($pdf_cols-1),2);
$groupColWidth.='%';
$providerColWidth.='%';
$patNameColWidth.='%';
$cptDescColWidth.='%';
$colWidth.='%';
$pdfColWidth.='%';
//-------------------------

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
	$data_output.="Office Production Report (Summary By Location Per Provider)".$pfx;
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
    $data_output.="Provider".$pfx;
    if(empty($cpt_cat_2) === false){
        $data_output.="CPT Category2".$pfx;
    }    
    $data_output.="Charges".$pfx;
    $data_output.="Pat. Paid".$pfx;
    $data_output.="Ins. Paid".$pfx;
	$data_output.="Total Paid".$pfx;	
    $data_output.="Adjustment".$pfx;
    $data_output.="Refund".$pfx;
	$data_output.="Pat. Due".$pfx;
    $data_output.="Ins. Due".$pfx;
	$data_output.="Balance".$pfx;
	$data_output.="\n"; 
	
	foreach($mainResArr as $firstGrpId => $firstGrpData){	
        $facilityTotalArr=array();
		$printFile=true;
        $firstGrpName = $arrAllFacilities[$firstGrpId];

        $html_part .='<tr><td class="text_b_w" colspan="'.$cols.'">'.$grpTitle.' : '.$firstGrpName.'</td></tr>';
        
        foreach($firstGrpData as $providerId => $providerData)
        {
            $physicianTotalArr=array();

            foreach($arr_cpt_cats as $cat2_key=> $cat2_name)
            {            
                $charges=$patCrdDbt=$insCrdDbt=$patPaidAmt=$insPaidAmt=$adj_amt=$refund_amt=$patientDue=$insuranceDue=$creditProcAmount=$totalBalance=0;

                foreach($providerData[$cat2_key] as $chgDetId => $encDataArr)
                {
                    $enc_id=$encDataArr['encounter_id'];
                    $provider_name=$providerNameArr[$encDataArr['primaryProviderId']];
                    $cat2_name=$arrrCPTCat2[$encDataArr['cpt_category2']];

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
                    $refund_amt+=$arrRefundAmt[$chgDetId];
                    //PAT & INS DUES
                    $patientDue+= $encDataArr['pat_due'];            
                    $insuranceDue+= $encDataArr['pri_due'] + $encDataArr['sec_due'] + $encDataArr['tri_due'];
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

                //ARRAY PHYSCIAN TOTAL 
                $physicianTotalArr["charges"]+= $charges;
                $physicianTotalArr["pat_paid_amt"]+= $patPaidAmt;
                $physicianTotalArr["ins_paid_amt"]+= $insPaidAmt;
                $physicianTotalArr["total_paid_amt"]+= $totalPaidAmt;
                $physicianTotalArr["adj_amt"]+= $adj_amt;
                $physicianTotalArr["refund_amt"]+= $refund_amt;
                $physicianTotalArr["pat_due"]+= $patientDue;
                $physicianTotalArr["ins_due"]+= $insuranceDue;
                $physicianTotalArr["balance"]+= $totalBalance;

                if(empty($cpt_cat_2) === false){
                    $tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$colWidth.'">'.$cat2_name.'</td>';
                }    

                $html_part.='
                <tr>
                    <td class="text_10" style="background:#FFFFFF; width:'.$groupColWidth.'">'.$provider_name.'</td>
                    '.$tdCPTCat2Data.'
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($charges,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($patPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($insPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($totalPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($adj_amt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($refund_amt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($patientDue,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($insuranceDue,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($totalBalance,2).'</td>
                </tr>';

                   
                //FOR CSV
                $data_output.='"'.$firstGrpName.'"'.$pfx;
                $data_output.='"'.$provider_name.'"'.$pfx;
                if(empty($cpt_cat_2) === false){
                    $data_output.='"'.$cat2_name.'"'.$pfx;
                }
                $data_output.='"'.$CLSReports->numberFormat($charges,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($patPaidAmt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($insPaidAmt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($totalPaidAmt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($adj_amt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($refund_amt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($patientDue,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($nsuranceDue,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($totalBalance,2).'"'.$pfx;
                $data_output.="\n";                
        }


            //ARRAY FACILITY TOTAL 
            $facilityTotalArr["charges"]+= $physicianTotalArr["charges"];
            $facilityTotalArr["pat_paid_amt"]+= $physicianTotalArr["pat_paid_amt"];
            $facilityTotalArr["ins_paid_amt"]+= $physicianTotalArr["ins_paid_amt"];
            $facilityTotalArr["total_paid_amt"]+=$physicianTotalArr["total_paid_amt"];
            $facilityTotalArr["adj_amt"]+= $physicianTotalArr["adj_amt"];
            $facilityTotalArr["refund_amt"]+= $physicianTotalArr["refund_amt"];
            $facilityTotalArr["pat_due"]+= $physicianTotalArr["pat_due"];
            $facilityTotalArr["ins_due"]+=$physicianTotalArr["ins_due"];
            $facilityTotalArr["balance"]+=$physicianTotalArr["balance"];

            if(empty($cpt_cat_2) === false){    
                $html_part.='
                <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
                <tr>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Physician Total:</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($physicianTotalArr['charges'],2).'</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($physicianTotalArr['pat_paid_amt'],2).'</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($physicianTotalArr['ins_paid_amt'],2).'</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($physicianTotalArr['total_paid_amt'],2).'</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physicianTotalArr['adj_amt'],2).'</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physicianTotalArr['refund_amt'],2).'</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physicianTotalArr['pat_due'],2).'</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physicianTotalArr['ins_due'],2).'</td>
                    <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physicianTotalArr['balance'],2).'</td>
                </tr>
                <tr><td class="total-row" colspan="'.$cols.'"></td></tr>';              
            }
        }
    
        //ARRAY GRAND TOTAL 
        $grandTotalAmtArr["charges"]+= $facilityTotalArr["charges"];
        $grandTotalAmtArr["approved_amt"]+= $facilityTotalArr["approved_amt"];
        $grandTotalAmtArr["pat_paid_amt"]+= $facilityTotalArr["pat_paid_amt"];
        $grandTotalAmtArr["ins_paid_amt"]+= $facilityTotalArr["ins_paid_amt"];
        $grandTotalAmtArr["total_paid_amt"]+= $facilityTotalArr["total_paid_amt"];
        $grandTotalAmtArr["adj_amt"]+= $facilityTotalArr["adj_amt"];
        $grandTotalAmtArr["refund_amt"]+= $facilityTotalArr["refund_amt"];
        $grandTotalAmtArr["pat_due"]+= $facilityTotalArr["pat_due"];
        $grandTotalAmtArr["ins_due"]+= $facilityTotalArr["ins_due"];
        $grandTotalAmtArr["balance"]+= $facilityTotalArr["balance"];

        $html_part.='
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Facility Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facilityTotalArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facilityTotalArr['pat_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facilityTotalArr['ins_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facilityTotalArr['total_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['refund_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['pat_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['ins_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>';  
    }

    $header_part='
    <table class="rpt_table rpt_table-bordered rpt_padding">
        <tr class="rpt_headers">
            <td class="rptbx1" style="text-align:left; width:33%">Office Production Report (Summary By Location per Provider)</td>
            <td class="rptbx2" style="text-align:left; width:34%">'.$dayReport.' ('.$search.') From : '.$Sdate.' To : '.$Edate.'</td>
            <td class="rptbx3" style="text-align:left; width:33%">Created by '.$opInitial.' on '.$curDate.'</td>
        </tr>
        <tr class="rpt_headers">
            <td class="rptbx1">Group : '.$selgroup.'</td>
            <td class="rptbx2">Facility : '.$selFac.'</td>
            <td class="rptbx3">Filing Phy. : '.$selPhy.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Oper.: '.$selOpr.
                '
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

    $tdCPTCat2Title='';
    if(empty($cpt_cat_2) === false)
    {
        $tdCPTCat2Title='<td class="text_b_w" style="text-align:left; width:'.$colWidth.'">CPT CAT2</td>';
    }

    //PAGE DATA
    $page_data=
    $header_part.'
    <table class="rpt_table rpt_table-bordered" style="width:100%;" >
    <tr>
        <td class="text_b_w" style="text-align:center; width:'.$groupColWidth.'">Provider</td>
        '.$tdCPTCat2Title.'
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Pat. Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Ins. Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Total Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Adjustment</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Refund</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Pat. Due</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Ins. Due</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Balance</td>
    </tr>
        '.$html_part.'
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['total_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grandTotalAmtArr['refund_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['pat_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['ins_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>        
    </table>';

    //PDF DATA    
	$pdf_data=
	'<page backtop="23mm" backbottom="5mm">
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
        <td class="text_b_w" style="width:'.$groupColWidth.'">Provider</td>
        '.$tdCPTCat2Title.'
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Charges</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Pat. Paid</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Ins. Paid</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Total Paid</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Adjustment</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Refund</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Pat Due</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Ins Due</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Balance</td>
     </tr>
	</table>
	</page_header>
	    <table style="width:100%;" class="rpt_table rpt_table-bordered">'
        .$html_part.
        '<tr><td class="total-row" colspan="'.$cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['total_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['refund_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['pat_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['ins_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>        
	    </table>
    </page>'; 

    //FOR CSV
    $data_output.='Total:'.$pfx;
    $data_output.=' '.$pfx;
    if(empty($cpt_cat_2) === false){
        $data_output.=' '.$pfx;
    }
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['total_paid_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['refund_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['pat_due'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['ins_due'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['balance'],2).'"'.$pfx;
    $data_output.="\n";  

    @fwrite($fp,$data_output);
    fclose($fp);
}
?>
