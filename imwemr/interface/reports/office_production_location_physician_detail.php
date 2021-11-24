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
$cols=17;
$subCol=8;
$pdf_cols=14;
$pdf_subCol=7;
$groupColWidth=8;
$providerColWidth=8;
$patNameColWidth=8;
$cptDescColWidth=8;

if(empty($cpt_cat_2) === false){
    $cols+=1;
    $subCol+=1;
    $pdf_cols+=1;
    $pdf_subCol+=1;
}

$colWidth=round((100-($groupColWidth+$providerColWidth+$patNameColWidth+$cptDescColWidth))/ ($cols-4),2);
$pdfColWidth=round((100-($providerColWidth+$patNameColWidth))/ ($pdf_cols-2),2);
$groupColWidth.='%';
$providerColWidth.='%';
$patNameColWidth.='%';
$cptDescColWidth.='%';
$colWidth.='%';
$pdfColWidth.='%';
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
	$data_output.="Office Production Report (Detail By Location Per Provider)".$pfx;
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
    $data_output.="Patient ID".$pfx;
	$data_output.="Patient Name".$pfx;
    $data_output.="Encounter#".$pfx;
    $data_output.="DOS".$pfx;
    $data_output.="Insurance".$pfx;
	$data_output.="CPT".$pfx;
    $data_output.="CPT Description".$pfx;
    $data_output.="Charges".$pfx;
    $data_output.="Allowed Amount".$pfx;
    $data_output.="Pat. Paid".$pfx;
    $data_output.="Ins. Paid".$pfx;
	$data_output.="Total Paid".$pfx;	
	$data_output.="Adjustment".$pfx;
	$data_output.="Pat. Due".$pfx;
    $data_output.="Ins. Due".$pfx;
	$data_output.="Balance".$pfx;
	$data_output.="\n"; 
	
	foreach($mainResArr as $firstGrpId => $firstGrpData){	
        $facilityTotalArr=array();
		$printFile=true;
        $firstGrpName = $arrAllFacilities[$firstGrpId];

        $html_part .='<tr><td class="text_b_w" colspan="'.$cols.'">'.$grpTitle.' : '.$firstGrpName.'</td></tr>';
        $pdf_part .='<tr><td class="text_b_w" colspan="'.$pdf_cols.'">'.$grpTitle.' : '.$firstGrpName.'</td></tr>';
        
        foreach($firstGrpData as $providerId => $providerData)
        {
            $physcianTotalArr=array();
            foreach($providerData as $chgDetId => $encDataArr)
            {
                $enc_id=$encDataArr['encounter_id'];
                $patient_name = core_name_format($encDataArr['lname'], $encDataArr['fname'], $encDataArr['mname']);		
                $patient_id = $encDataArr['patient_id'];
                $date_of_service = $encDataArr['date_of_service'];
                $cptCode = $encDataArr['cpt_prac_code'];
                $cpt_desc = $encDataArr['cpt_desc'];
                $priInsName=$arrAllInsCompanies[$encDataArr['pri_ins_id']];
                $provider_name=$providerNameArr[$encDataArr['primaryProviderId']];

                //CHARGES    
                $charges=$encDataArr['totalAmt'];
                $approved_amt=$encDataArr['approved_amt'];
                //CREDIT/DEBIT
                $patCrdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'];			
                $insCrdDbt= $pay_crd_deb_arr[$chgDetId]['Insurance'];
                //PAYMENTS
                $patPaidAmt=$patPayDetArr[$chgDetId]['patPaid'] + $patCrdDbt;
                $insPaidAmt= $patPayDetArr[$chgDetId]['insPaid'] + $insCrdDbt;
                $totalPaidAmt= $patPaidAmt+$insPaidAmt;
                //WRITE-OFF & ADJUSTMENTS
                $adj_amt= $normalWriteOffAmt[$chgDetId] + $arrAdjustmentAmtDet[$chgDetId];
                //PAT & INS DUES
                $patientDue= $encDataArr['pat_due'];            
                $insuranceDue= $encDataArr['pri_due'] + $encDataArr['sec_due'] + $encDataArr['tri_due'];
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
                $totalBalance=$balAmt;

                //ARRAY PHYSCIAN TOTAL 
                $physcianTotalArr["charges"]+= $charges;
                $physcianTotalArr["approved_amt"]+= $approved_amt;
                $physcianTotalArr["pat_paid_amt"]+= $patPaidAmt;
                $physcianTotalArr["ins_paid_amt"]+= $insPaidAmt;
                $physcianTotalArr["total_paid_amt"]+= $totalPaidAmt;
                $physcianTotalArr["adj_amt"]+= $adj_amt;
                $physcianTotalArr["pat_due"]+= $patientDue;
                $physcianTotalArr["ins_due"]+= $insuranceDue;
                $physcianTotalArr["balance"]+= $totalBalance;


                $cat2_name=$arrrCPTCat2[$encDataArr['cpt_category2']];
                if(empty($cpt_cat_2) === false){
                    $tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$colWidth.'">'.$cat2_name.'</td>';
                    $pdf_tdCPTCat2Data='<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$pdfColWidth.'">'.$cat2_name.'</td>';
                }    

                $html_part.='
                <tr>
                    <td class="text_10" style="background:#FFFFFF; width:'.$providerColWidth.'">'.$provider_name.'</td>
                    '.$tdCPTCat2Data.'
                    <td class="text_10" style="background:#FFFFFF; width:'.$colWidth.'">'.$patient_id.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$patNameColWidth.'">'.$patient_name.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$colWidth.'">'.$enc_id.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$colWidth.'">'.$date_of_service.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$colWidth.'">'.$priInsName.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$colWidth.'">'.$cptCode.'</td>
                    <td class="notInPDF text_10" style="background:#FFFFFF; width:'.$cptDescColWidth.'">'.$cpt_desc.'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($charges,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($approved_amt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($patPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($insPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($totalPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($adj_amt,2).'</td>
                    <td class="notInPDF text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($patientDue,2).'</td>
                    <td class="notInPDF text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($insuranceDue,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$colWidth.'">'.$CLSReports->numberFormat($totalBalance,2).'</td>
                </tr>';

                $pdf_part.='
                <tr>
                    <td class="text_10" style="background:#FFFFFF; width:'.$providerColWidth.'">'.$provider_name.'</td>
                    '.$pdf_tdCPTCat2Data.'
                    <td class="text_10" style="background:#FFFFFF; width:'.$pdfColWidth.'">'.$patient_id.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$patNameColWidth.'">'.$patient_name.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$pdfColWidth.'">'.$enc_id.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$pdfColWidth.'">'.$date_of_service.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$pdfColWidth.'">'.$priInsName.'</td>
                    <td class="text_10" style="background:#FFFFFF; width:'.$pdfColWidth.'">'.$cptCode.'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$pdfColWidth.'">'.$CLSReports->numberFormat($charges,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$pdfColWidth.'">'.$CLSReports->numberFormat($approved_amt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$pdfColWidth.'">'.$CLSReports->numberFormat($patPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$pdfColWidth.'">'.$CLSReports->numberFormat($insPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$pdfColWidth.'">'.$CLSReports->numberFormat($totalPaidAmt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$pdfColWidth.'">'.$CLSReports->numberFormat($adj_amt,2).'</td>
                    <td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$pdfColWidth.'">'.$CLSReports->numberFormat($totalBalance,2).'</td>
                </tr>';

                //FOR CSV
                $data_output.='"'.$firstGrpName.'"'.$pfx;
                $data_output.='"'.$provider_name.'"'.$pfx;
                if(empty($cpt_cat_2) === false){
                    $data_output.='"'.$cat2_name.'"'.$pfx;
                }
                $data_output.='"'.$patient_id.'"'.$pfx;
                $data_output.='"'.$patient_name.'"'.$pfx;
                $data_output.='"'.$enc_id.'"'.$pfx;
                $data_output.='"'.$date_of_service.'"'.$pfx;
                $data_output.='"'.$priInsName.'"'.$pfx;            
                $data_output.='"'.$cptCode.'"'.$pfx;
                $data_output.='"'.$cpt_desc.'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($charges,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($approved_amt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($patPaidAmt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($insPaidAmt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($totalPaidAmt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($adj_amt,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($patientDue,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($nsuranceDue,2).'"'.$pfx;
                $data_output.='"'.$CLSReports->numberFormat($totalBalance,2).'"'.$pfx;
                $data_output.="\n";
            }

            //ARRAY FACILITY TOTAL 
            $facilityTotalArr["charges"]+= $physcianTotalArr["charges"];
            $facilityTotalArr["approved_amt"]+= $physcianTotalArr["approved_amt"];
            $facilityTotalArr["pat_paid_amt"]+= $physcianTotalArr["pat_paid_amt"];
            $facilityTotalArr["ins_paid_amt"]+= $physcianTotalArr["ins_paid_amt"];
            $facilityTotalArr["total_paid_amt"]+= $physcianTotalArr["total_paid_amt"];
            $facilityTotalArr["adj_amt"]+= $physcianTotalArr["adj_amt"];
            $facilityTotalArr["pat_due"]+= $physcianTotalArr["pat_due"];
            $facilityTotalArr["ins_due"]+= $physcianTotalArr["ins_due"];
            $facilityTotalArr["balance"]+= $physcianTotalArr["balance"];

            $html_part.='
            <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
            <tr>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Physician Total:</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($physcianTotalArr['charges'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($physcianTotalArr['approved_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($physcianTotalArr['pat_paid_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($physcianTotalArr['ins_paid_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($physcianTotalArr['total_paid_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physcianTotalArr['adj_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physcianTotalArr['pat_due'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physcianTotalArr['ins_due'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physcianTotalArr['balance'],2).'</td>
            </tr>
            <tr><td class="total-row" colspan="'.$cols.'"></td></tr>';

            $pdf_part.=    
            '<tr><td class="total-row" colspan="'.$pdf_cols.'"></td></tr>
            <tr>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$pdf_subCol.'">Physician Total:</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($physcianTotalArr['charges'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($physcianTotalArr['approved_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($physcianTotalArr['pat_paid_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($physcianTotalArr['ins_paid_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($physcianTotalArr['total_paid_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physcianTotalArr['adj_amt'],2).'</td>
                <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($physcianTotalArr['balance'],2).'</td>
            </tr>
            <tr><td class="total-row" colspan="'.$pdf_cols.'"></td></tr>';              
        }
        
        //ARRAY GRAND TOTAL 
        $grandTotalAmtArr["charges"]+= $facilityTotalArr["charges"];
        $grandTotalAmtArr["approved_amt"]+= $facilityTotalArr["approved_amt"];
        $grandTotalAmtArr["pat_paid_amt"]+= $facilityTotalArr["pat_paid_amt"];
        $grandTotalAmtArr["ins_paid_amt"]+= $facilityTotalArr["ins_paid_amt"];
        $grandTotalAmtArr["total_paid_amt"]+= $facilityTotalArr["total_paid_amt"];
        $grandTotalAmtArr["adj_amt"]+= $facilityTotalArr["adj_amt"];
        $grandTotalAmtArr["pat_due"]+= $facilityTotalArr["pat_due"];
        $grandTotalAmtArr["ins_due"]+= $facilityTotalArr["ins_due"];
        $grandTotalAmtArr["balance"]+= $facilityTotalArr["balance"];

        $html_part.='
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Facility Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facilityTotalArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facilityTotalArr['approved_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facilityTotalArr['pat_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facilityTotalArr['ins_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facilityTotalArr['total_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['pat_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['ins_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>';  

        $pdf_part.=    
        '<tr><td class="total-row" colspan="'.$pdf_cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$pdf_subCol.'">Facility Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facilityTotalArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facilityTotalArr['approved_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($facilityTotalArr['pat_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facilityTotalArr['ins_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($facilityTotalArr['total_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($facilityTotalArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$pdf_cols.'"></td></tr>';             
      
    }

    $header_part='
    <table class="rpt_table rpt_table-bordered rpt_padding">
        <tr class="rpt_headers">
            <td class="rptbx1" style="text-align:left; width:33%">Office Production Report (Detail By Location per Provider)</td>
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

    $tdCPTCat2Title=$tdCPTCat2Data=$pdf_tdCPTCat2Title='';
    if(empty($cpt_cat_2) === false)
    {
        $tdCPTCat2Data='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"></td>';
        $tdCPTCat2Title='<td class="text_b_w" style="text-align:left; width:'.$colWidth.'%">CPT CAT2</td>';
        $pdf_tdCPTCat2Title='<td class="text_b_w" style="text-align:left; width:'.$pdfColWidth.'%">CPT CAT2</td>';
    }

    //PAGE DATA
    $page_data=
    $header_part.'
    <table class="rpt_table rpt_table-bordered" style="width:100%;" >
    <tr>
        <td class="text_b_w" style="text-align:center; width:'.$providerColWidth.'">Provider</td>
        '.$tdCPTCat2Title.'
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Patient ID</td>
        <td class="text_b_w" style="text-align:center; width:'.$patNameColWidth.'">Patient Name</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Enc. ID</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">DOS</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Ins. Name</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">CPT</td>
        <td class="text_b_w" style="text-align:center; width:'.$cptDescColWidth.'">CPT Desc</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Charges</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Allowed Amount</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Pat. Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Ins. Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Total Paid</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Adjustment</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Pat. Due</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Ins. Due</td>
        <td class="text_b_w" style="text-align:center; width:'.$colWidth.'">Balance</td>
    </tr>
        '.$html_part.'
        <tr><td class="total-row" colspan="'.$cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$subCol.'">Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['approved_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['total_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['pat_due'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['ins_due'],2).'</td>
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
        <td class="text_b_w" style="width:'.$providerColWidth.'">Provider</td>
        '.$pdf_tdCPTCat2Title.'
        <td class="text_b_w" style="width:'.$pdfColWidth.'">Patient ID</td>
        <td class="text_b_w" style="width:'.$patNameColWidth.'">Patient Name</td>
        <td class="text_b_w" style="width:'.$pdfColWidth.'">Enc. ID</td>
        <td class="text_b_w" style="width:'.$pdfColWidth.'">DOS</td>
        <td class="text_b_w" style="width:'.$pdfColWidth.'">Ins. Name</td>
        <td class="text_b_w" style="width:'.$pdfColWidth.'">CPT</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Charges</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Allowed Amount</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Pat. Paid</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Ins. Paid</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Total Paid</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Adjustment</td>
        <td class="text_b_w" style="text-align:right; width:'.$pdfColWidth.'">Balance</td>
     </tr>
	</table>
	</page_header>
	    <table style="width:100%;" class="rpt_table rpt_table-bordered">'
        .$pdf_part.
        '<tr><td class="total-row" colspan="'.$pdf_cols.'"></td></tr>
        <tr>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right" colspan="'.$pdf_subCol.'">Total:</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['approved_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right"> '.$CLSReports->numberFormat($grandTotalAmtArr['total_paid_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'</td>
            <td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"> '.$CLSReports->numberFormat($grandTotalAmtArr['balance'],2).'</td>
        </tr>
        <tr><td class="total-row" colspan="'.$pdf_cols.'"></td></tr>        
	    </table>
    </page>'; 

    //FOR CSV
    $data_output.=' '.$pfx;
    $data_output.=' '.$pfx;
    if(empty($cpt_cat_2) === false){
        $data_output.=' '.$pfx;
    }
    $data_output.=' '.$pfx;
    $data_output.=' '.$pfx;
    $data_output.=' '.$pfx;
    $data_output.=' '.$pfx;
    $data_output.=' '.$pfx;
    $data_output.=' '.$pfx;
    $data_output.='Total:'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['charges'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['approved_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['pat_paid_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['ins_paid_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['total_paid_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['adj_amt'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['pat_due'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['ins_due'],2).'"'.$pfx;
    $data_output.='"'.$CLSReports->numberFormat($grandTotalAmtArr['balance'],2).'"'.$pfx;
    $data_output.="\n";

    @fwrite($fp,$data_output);
    fclose($fp);
}


?>
