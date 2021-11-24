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
FILE: writeoffReportSummary.php
PURPOSE: Display summary result of adjustment report
ACCESS TYPE: Indirect
*/

$dispGrand=0;
$writeoff_data = array(1);
if($flag_data_exist>0){
	
	//--- GET PAGE HEADER ----
	$arrCheckDetailId=array();
	$pdf_file_content='';
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = $op_name_arr[1][0];
	$op_name .= $op_name_arr[0][0];
	$op_name = strtoupper($op_name);
	$total_cols = 7;
	$phy_col = $phy_col1 ="17";
	$fac_col = "25";
	$w_cols = $w_cols1 = floor((100 - ($phy_col + $fac_col))/($total_cols-2));
	$fac_col = $fac_col1 = 100 - ( (($total_cols-2) * $w_cols) + $phy_col);
	
//	$w_cols = $w_cols."%";
//	$phy_col = $phy_col."%";
//	$fac_col = $fac_col."%";
	$w_cols = "120px";
	$phy_col = "215px";
	$fac_col = "215px";
	//--- CSV FILE HEADER INFORMATION 

	$pdf_header = <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr class="rpt_headers">
                <td class="rptbx1" style="width:260px;">&nbsp;Adjustment Report - Summary</td>	
				<td class="rptbx2" style="width:250px;">&nbsp;Selected Group : $selGrpDisp</td>			
				<td class="rptbx3" style="width:260px;">&nbsp;From : $Start_date To : $End_date</td>
				<td class="rptbx1" style="width:250px;">&nbsp;Created by $op_name on $curDate</td>
			</tr>
			<tr class="rpt_headers">	
				<td class="rptbx1" style="width:260px;">&nbsp;Selected Facility : $selFacDisp</td>	
				<td class="rptbx2" style="width:250px;">&nbsp;Selected Physician : $selPhyDisp</td>			
				<td class="rptbx3" style="width:260px;">&nbsp;Selected Adj. Code : $selWriteoffDisp</td>
				<td class="rptbx1" style="width:250px;">&nbsp;Selected Operator : $sel_opr</td>
			</tr>
		</table>
DATA;

//MAKING OUTPUT DATA
$file_name="adjustment_report.csv";
$csv_file_name= write_html("", $file_name);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');
$arr=array();
$arr[]="Adjustment Report (Summary)";
$arr[]="Selected Group :" .$selGrpDisp;
$arr[]="From :" .$Start_date." To :" .$End_date;
$arr[]="Created by" .$op_name." on" .$curDate;
fputcsv($fp,$arr, ",","\"");
// WRITE OFF CODE
$arrGrand = array();
if(sizeof($arrWriteOffCode)>0){
	$arrTotal = array();
	$dispGrand = 1;
	$printFile = true;
	
	$arr=array();
	$arr[]="Selected Facility :" .$selFacDisp;
	$arr[]="Selected Physician :" .$selPhyDisp;
	$arr[]="Selected Adj. Code :" .$selWriteoffDisp;
	$arr[]="Selected Operator :" .$sel_opr;
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]="Physician";
	$arr[]="Facility";
	$arr[]="Charges";
	$arr[]="Payment";
	$arr[]="Write-Off";
	$arr[]="";
	$arr[]="Balance";
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');
	
	$write_data_page_title= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px">
			<tr>
				<td class="text_b_w" style="width:1050px;" colspan="$total_cols">Write-off Summary</td>
			</tr>
DATA;

	$writeofCode = array_keys($arrWriteOffCode['code']);
    $writeOffOperators = array_keys($arrWriteOffCode['woff_operator']);
	foreach($writeofCode as $writeCodeId){
		
		$subArr = array();
		$code_name = $write_off_code_arr[$writeCodeId];
		if($writeCodeId == 0)
		$code_name = "Not Specified";
		
		$write_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">Code - $code_name</td>
		</tr>
DATA;
		//FACILITY
		$write_data.= <<<DATA
				<tr>
					<td class="text_b_w" style="text-align:center; width:$phy_col;">Physician</td>
					<td class="text_b_w" style="text-align:center; width:$fac_col;">Facility</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Charges</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Payment</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Write-Off</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;"></td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Balance</td>
				</tr>				
DATA;
        foreach($writeOffOperators as $writeOffOperatorId){
		$woperator_name = $operater_name[$writeOffOperatorId];
        if($writeOffOperatorId == 0)
        $woperator_name = "Not Specified";
        if(empty($arrWriteOffCode[$writeCodeId][$writeOffOperatorId])) {continue; }
        $write_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">Operator - $woperator_name</td>
		</tr>
DATA;
        
		foreach($arrWriteOffCode[$writeCodeId][$writeOffOperatorId] as $providerId=>$arrFacData){
			$prov_name = $arrProvider[$providerId];
			foreach($arrFacData as $facilityId=>$arrPatData)
			{
				$arrAmount = array();
				$fac_name = ($billing_location=='1')? $arrSchFacilites[$facilityId] : $arrFacility[$facilityId];
				$write_off_amt = $arrFacData[$facilityId]['write_off'];
				
				foreach($arrPatData as $patient_id=>$arrEncData){
					foreach($arrEncData as $enc_id=>$arrchldData){
						foreach($arrchldData as $chld=>$arrChld){
                            if(!in_array($chld.'~~'.$writeCodeId, $chld_wofarr)) {
                                $chld_wofarr[]=$chld.'~~'.$writeCodeId;
                                $arrAmount['charges'][$chld] = $arrWriteOffCode['charge_list_detail_id'][$chld]['charges'];
                                $arrAmount['balance'][$chld] = $arrWriteOffCode['charge_list_detail_id'][$chld]['balance'];
                                $arrAmount['payment'][$chld] = $arrWriteOffCode['charge_list_detail_id'][$chld]['payment'];
                            }
						}
						
					}
				}
				//echo "writeCodeId=".$writeCodeId."<br>";
				//pre($arrPatData);
				//echo $charges_amt."<br>";
			//FACILITY TOTALS
			$charges_amt = array_sum($arrAmount['charges']);
			$balance_amt = array_sum($arrAmount['balance']);
			$payment_amt = array_sum($arrAmount['payment']);
			
			$subArr['charges'][] = $charges_amt;
			$subArr['balance'][] = $balance_amt;
			$subArr['payment'][] = $payment_amt;
			$subArr['write_off'][] = $write_off_amt;
			
			$charges_amt = $CLSReports->numberFormat($charges_amt,2);
			$balance_amt = $CLSReports->numberFormat($balance_amt,2);
			$payment_amt = $CLSReports->numberFormat($payment_amt,2);
			$write_off_amt = $CLSReports->numberFormat($write_off_amt,2);
			
			$subArr['changes'][] = 
			$write_data.= <<<DATA
				<tr bgcolor="#ffffff">
					<td class="text_10" valign="top" style="text-align:left; width:$phy_col; word-wrap:break-word;">$prov_name</td>
					<td class="text_10" valign="top" style="text-align:left; width:$fac_col; word-wrap:break-word;">$fac_name</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$charges_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$payment_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$write_off_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;"></td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$balance_amt</td>
				</tr>
DATA;
				//FOR CSV
				$arr=array();
				$arr[]=$prov_name;
				$arr[]=$fac_name;
				$arr[]=$charges_amt;
				$arr[]=$payment_amt;
				$arr[]=$write_off_amt;
				$arr[]="";
				$arr[]=$balance_amt;
				fputcsv($fp,$arr, ",","\"");
			}
		}
    }
		$sub_write_off_amt = array_sum($subArr['write_off']);
		$sub_charges_amt = array_sum($subArr['charges']);
		$sub_balance_amt = array_sum($subArr['balance']);
		$sub_payment_amt = array_sum($subArr['payment']);
		
		$arrTotal['write_off'][] = $sub_write_off_amt;
		$arrTotal['charges'][] = $sub_charges_amt;
		$arrTotal['balance'][] = $sub_balance_amt;
		$arrTotal['payment'][] = $sub_payment_amt;
		
		$arrGrand['write_off'][] = $sub_write_off_amt;
		$arrGrand['charges'][] = $sub_charges_amt;
		$arrGrand['balance'][] = $sub_balance_amt;
		$arrGrand['payment'][] = $sub_payment_amt;
		
		$sub_write_off_amt = $CLSReports->numberFormat($sub_write_off_amt,2);
		$sub_charges_amt = $CLSReports->numberFormat($sub_charges_amt,2);
		$sub_balance_amt = $CLSReports->numberFormat($sub_balance_amt,2);
		$sub_payment_amt = $CLSReports->numberFormat($sub_payment_amt,2);
			$write_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Sub Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_write_off_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
	}
	$tot_write_off_amt = $CLSReports->numberFormat(array_sum($arrTotal['write_off']),2);
	$tot_charges_amt = $CLSReports->numberFormat(array_sum($arrTotal['charges']),2);
	$tot_balance_amt = $CLSReports->numberFormat(array_sum($arrTotal['balance']),2);
	$tot_payment_amt = $CLSReports->numberFormat(array_sum($arrTotal['payment']),2);
	$write_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_write_off_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="Total:";
				$arr[]=$tot_charges_amt;
				$arr[]=$tot_payment_amt;
				$arr[]=$tot_write_off_amt;
				$arr[]="";
				$arr[]=$tot_balance_amt;
				fputcsv($fp,$arr, ",","\"");

$write_data .= <<<DATA
	</table>
DATA;
		//PDF HTML
		$pdf_file_content .=<<<DATA
			<page backtop="12mm" backbottom="10mm">
			<page_footer>
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$pdf_header
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
                <tr class="rpt_headers">
                    <td class="text_b_w" style="width:1070px;">Write-off Summary</td>	
                </tr>
			</table>
			</page_header>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			$write_data
			</page>
DATA;

	//PAGE HTML
	$write_data= $write_data_page_title.$write_data;

}//END WRITEOFF IF
$write_data.='<br>';
	
// DISCOUNT CODE
if(sizeof($arrDisData)>0){
	$arrTotal = array();
	$dispGrand = 1;
	$printFile = true;
	$dis_data_page_title.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr>	
				<td align="left" class="text_b_w"  style="width:1050px;" colspan="$total_cols">Discount Summary</td>	
			</tr>
DATA;
		
		//FOR CSV
		$arr=array();
		$arr[]="Discount Summary";
		fputcsv($fp,$arr, ",","\"");
		
		$arr=array();
		$arr[]="Physician";
		$arr[]="Facility";
		$arr[]="Charges";
		$arr[]="Payment";
		$arr[]="Discount";
		$arr[]="";
		$arr[]="Balance";
		fputcsv($fp,$arr, ",","\"");

	$arrCode = array_keys($arrDisData['code']);
    $writeOffOperators = array_keys($arrDisData['woff_operator']);
	foreach($arrCode as $codeId){
		
		$subArr = array();
		$code_name = $dis_off_code_arr[$codeId];
		if($codeId == 0)
		$code_name = "Not Specified";
		
		$dis_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">Code - $code_name</td>
		</tr>
DATA;
		//FACILITY
		$dis_data.= <<<DATA
				<tr>
					<td class="text_b_w" style="text-align:center; width:$phy_col;">Physician</td>
					<td class="text_b_w" style="text-align:center; width:$fac_col;">Facility</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Charges</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Payment</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Discount</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;"></td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Balance</td>
				</tr>				
DATA;
        foreach($writeOffOperators as $writeOffOperatorId){
            $woperator_name = $operater_name[$writeOffOperatorId];
            if($writeOffOperatorId == 0)
            $woperator_name = "Not Specified";
            if(empty($arrDisData[$codeId][$writeOffOperatorId])) {continue; }
            $dis_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">Operator - $woperator_name</td>
		</tr>
DATA;
		foreach($arrDisData[$codeId][$writeOffOperatorId] as $providerId=>$arrFacData){
			$prov_name = $arrProvider[$providerId];
			foreach($arrFacData as $facilityId=>$arrPatData)
			{
				$arrAmount = array();
				$fac_name = ($billing_location=='1')? $arrSchFacilites[$facilityId] : $arrFacility[$facilityId];
				$dis_amt = $arrFacData[$facilityId]['dis'];
				
				foreach($arrPatData as $patient_id=>$arrEncData){
					foreach($arrEncData as $enc_id=>$arrchldData){
						foreach($arrchldData as $chld=>$arrChld){
                            if(!in_array($chld.'~~'.$writeCodeId, $chld_disarr)) {
                                $chld_disarr[]=$chld.'~~'.$writeCodeId;
                                $arrAmount['charges'][$chld] = $arrDisData['charge_list_detail_id'][$chld]['charges'];
                                $arrAmount['balance'][$chld] = $arrDisData['charge_list_detail_id'][$chld]['balance'];
                                $arrAmount['payment'][$chld] = $arrDisData['charge_list_detail_id'][$chld]['payment'];
							
                                //-----ADD SUBTRACT CREDIT DEBIT AMOUNT FROM PAYMENTS AND BALANCE-----
                                foreach($pay_crd_deb_chld_arr[$chld] as $key=>$cre_deb_amt){
                                    if(!isset($pay_crd_deb_chld_arr[$chld][$key]['applied'])){
                                        $arrAmount['payment'][$chld] += $cre_deb_amt;
                                        $arrAmount['balance'][$chld] -= $cre_deb_amt;
                                        $pay_crd_deb_chld_arr[$chld][$key]['applied'] = 'yes';
                                    }
                                }
                            }
						}
					}
				}
			//FACILITY TOTALS
			$charges_amt = array_sum($arrAmount['charges']);
			$balance_amt = array_sum($arrAmount['balance']);
			$payment_amt = array_sum($arrAmount['payment']);
			$subArr['charges'][] = $charges_amt;
			$subArr['balance'][] = $balance_amt;
			$subArr['payment'][] = $payment_amt;
			$subArr['dis'][] = $dis_amt;
			
			$charges_amt = $CLSReports->numberFormat($charges_amt,2);
			$balance_amt = $CLSReports->numberFormat($balance_amt,2);
			$payment_amt = $CLSReports->numberFormat($payment_amt,2);
			$dis_amt = $CLSReports->numberFormat($dis_amt,2);
			
			$dis_data.= <<<DATA
				<tr bgcolor="#ffffff">
					<td class="text_10" valign="top" style="text-align:left; width:$phy_col; word-wrap:break-word;">$prov_name</td>
					<td class="text_10" valign="top" style="text-align:left; width:$fac_col; word-wrap:break-word;">$fac_name</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$charges_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$payment_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$dis_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;"></td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$balance_amt</td>
				</tr>
DATA;
				//FOR CSV
				$arr=array();
				$arr[]=$prov_name;
				$arr[]=$fac_name;
				$arr[]=$charges_amt;
				$arr[]=$payment_amt;
				$arr[]=$dis_amt;
				$arr[]="";
				$arr[]=$balance_amt;
				fputcsv($fp,$arr, ",","\"");
			}
		}
        }
		$sub_dis_amt = array_sum($subArr['dis']);
		$sub_charges_amt = array_sum($subArr['charges']);
		$sub_balance_amt = array_sum($subArr['balance']);
		$sub_payment_amt = array_sum($subArr['payment']);
		
		$arrGrand['write_off'][] = $sub_dis_amt;
		$arrGrand['charges'][] = $sub_charges_amt;
		$arrGrand['balance'][] = $sub_balance_amt;
		$arrGrand['payment'][] = $sub_payment_amt;
		
		$arrTotal['dis'][] = $sub_dis_amt;
		$arrTotal['charges'][] = $sub_charges_amt;
		$arrTotal['balance'][] = $sub_balance_amt;
		$arrTotal['payment'][] = $sub_payment_amt;
		
		$sub_dis_amt = $CLSReports->numberFormat($sub_dis_amt,2);
		$sub_charges_amt = $CLSReports->numberFormat($sub_charges_amt,2);
		$sub_balance_amt = $CLSReports->numberFormat($sub_balance_amt,2);
		$sub_payment_amt = $CLSReports->numberFormat($sub_payment_amt,2);
			$dis_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Sub Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_dis_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
	}
	$tot_dis_amt = $CLSReports->numberFormat(array_sum($arrTotal['dis']),2);
	$tot_charges_amt = $CLSReports->numberFormat(array_sum($arrTotal['charges']),2);
	$tot_balance_amt = $CLSReports->numberFormat(array_sum($arrTotal['balance']),2);
	$tot_payment_amt = $CLSReports->numberFormat(array_sum($arrTotal['payment']),2);
	$dis_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_dis_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="Total:";
				$arr[]=$tot_charges_amt;
				$arr[]=$tot_payment_amt;
				$arr[]=$tot_dis_amt;
				$arr[]="";
				$arr[]=$tot_balance_amt;
				fputcsv($fp,$arr, ",","\"");
$dis_data .= <<<DATA
	</table>
DATA;
		//PDF HTML
		$pdf_file_content .=<<<DATA
			<page backtop="12mm" backbottom="10mm">
			<page_footer>
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$pdf_header
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr class="rpt_headers">
				<td align="left" class="text_b_w"  style="width:1070px;">Discount Summary</td>	
			</tr>
			</table>
			</page_header>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1070px" >
			$dis_data
			</page>
DATA;

	//PAGE HTML
	$dis_data= $dis_data_page_title.$dis_data;
	
}//END DISCOUNT IF


// ADJUST CODE
if(sizeof($arrAdjData)>0){
	$arrTotal = array();
	$dispGrand = 1;
	$printFile = true;
	$adj_data_page_title.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr>	
				<td align="left" class="text_b_w" style="width:1050px;" colspan="$total_cols">Adjustment Summary</td>	
			</tr>
DATA;
	
				//FOR CSV
				$arr=array();
				$arr[]="Adjustment Summary";
				fputcsv($fp,$arr, ",","\"");
				
				$arr=array();
				$arr[]="Physician";
				$arr[]="Facility";
				$arr[]="Charges";
				$arr[]="Payment";
				$arr[]="Adjustment";
				$arr[]="Refund";
				$arr[]="Balance";
				fputcsv($fp,$arr, ",","\"");
	
    $writeOffOperators = array_keys($arrAdjData['woff_operator']);
	$arrCode = array_keys($arrAdjData['code']);
	foreach($arrCode as $codeId){
		$code_name = $adj_off_code_arr[$codeId];
		if($tempResCodeArr[$codeId]!=""){
			$reason_code = 'Reason Code - '.$tempResCodeArr[$codeId];
		}
		if($codeId == 0){
			$code_name = "Code - Not Specified";
		}else {
			$code_name = 'Code - '.$code_name;
		}
		$adj_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">$code_name&nbsp;&nbsp;&nbsp;&nbsp;$reason_code</td>
		</tr>
DATA;
		//FACILITY
		$adj_data.= <<<DATA
				<tr>
					<td class="text_b_w" style="text-align:center; width:$phy_col;">Physician</td>
					<td class="text_b_w" style="text-align:center; width:$fac_col;">Facility</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Charges</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Payment</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Adjustment</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Refund</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Balance</td>
				</tr>				
DATA;
        foreach($writeOffOperators as $writeOffOperatorId){
            $woperator_name = $operater_name[$writeOffOperatorId];
            if($writeOffOperatorId == 0)
            $woperator_name = "Not Specified";
            if(empty($arrAdjData[$codeId][$writeOffOperatorId])) {continue; }
            $adj_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">Operator - $woperator_name</td>
		</tr>
DATA;
		foreach($arrAdjData[$codeId][$writeOffOperatorId] as $providerId=>$arrFacData){
			$prov_name = $arrProvider[$providerId];
			foreach($arrFacData as $facilityId=>$arrPatData)
			{
				$arrAmount = array();
				$fac_name = ($billing_location=='1')? $arrSchFacilites[$facilityId] : $arrFacility[$facilityId];
				$adj_amt = $arrFacData[$facilityId]['adj'];
				$refund_amt = $arrFacData[$facilityId]['refund'];
				
				foreach($arrPatData as $patient_id=>$arrEncData){
					foreach($arrEncData as $enc_id=>$arrchldData){
						foreach($arrchldData as $chld=>$arrChld){
                            if(!in_array($chld.'~~'.$writeCodeId, $chld_arr)) {
                                $chld_arr[]=$chld.'~~'.$writeCodeId;
                                $arrAmount['charges'][$chld] = $arrAdjData['charge_list_detail_id'][$chld]['charges'];
                                $arrAmount['balance'][$chld] = $arrAdjData['charge_list_detail_id'][$chld]['balance'];
                                $arrAmount['payment'][$chld] = $arrAdjData['charge_list_detail_id'][$chld]['payment'];
							
                                //-----ADD SUBTRACT CREDIT DEBIT AMOUNT FROM PAYMENTS AND BALANCE-----
                                foreach($pay_crd_deb_chld_arr[$chld] as $key=>$cre_deb_amt){
                                    if(!isset($pay_crd_deb_chld_arr[$chld][$key]['applied'])){
                                        $arrAmount['payment'][$chld] += $cre_deb_amt;
                                        $arrAmount['balance'][$chld] -= $cre_deb_amt;
                                        $pay_crd_deb_chld_arr[$chld][$key]['applied'] = 'yes';
                                    }
                                }
                            }
						}
					}
				}
			//FACILITY TOTALS
			$charges_amt = array_sum($arrAmount['charges']);
			$balance_amt = array_sum($arrAmount['balance']);
			$payment_amt = array_sum($arrAmount['payment']);
			
			$subArr['charges'][] = $charges_amt;
			$subArr['balance'][] = $balance_amt;
			$subArr['payment'][] = $payment_amt;
			$subArr['adj'][] = $adj_amt;
			$subArr['refund'][] = $refund_amt;
			
			$charges_amt = $CLSReports->numberFormat($charges_amt,2);
			$balance_amt = $CLSReports->numberFormat($balance_amt,2);
			$payment_amt = $CLSReports->numberFormat($payment_amt,2);
			$adj_amt = $CLSReports->numberFormat($adj_amt,2);
			$refund_amt = $CLSReports->numberFormat($refund_amt,2);
			
			$adj_data.= <<<DATA
				<tr bgcolor="#ffffff">
					<td class="text_10" valign="top" style="text-align:left; width:$phy_col; word-wrap:break-word;">$prov_name</td>
					<td class="text_10" valign="top" style="text-align:left; width:$fac_col; word-wrap:break-word;">$fac_name</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$charges_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$payment_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$adj_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$refund_amt</td>
					<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$balance_amt</td>
				</tr>
DATA;
			//FOR CSV
				$arr=array();
				$arr[]=$prov_name;
				$arr[]=$fac_name;
				$arr[]=$charges_amt;
				$arr[]=$payment_amt;
				$arr[]=$adj_amt;
				$arr[]=$refund_amt;
				$arr[]=$balance_amt;
				fputcsv($fp,$arr, ",","\"");
			}
		}
        }
		$sub_adj_amt = array_sum($subArr['adj']);
		$sub_refund_amt = array_sum($subArr['refund']);
		$sub_charges_amt = array_sum($subArr['charges']);
		$sub_balance_amt = array_sum($subArr['balance']);
		$sub_payment_amt = array_sum($subArr['payment']);
		
		$arrGrand['write_off'][] = $sub_adj_amt;
		$arrGrand['refund'][] = $sub_refund_amt;
		$arrGrand['charges'][] = $sub_charges_amt;
		$arrGrand['balance'][] = $sub_balance_amt;
		$arrGrand['payment'][] = $sub_payment_amt;
		
		$arrTotal['adj'][] = $sub_adj_amt;
		$arrTotal['refund'][] = $sub_refund_amt;
		$arrTotal['charges'][] = $sub_charges_amt;
		$arrTotal['balance'][] = $sub_balance_amt;
		$arrTotal['payment'][] = $sub_payment_amt;
		
		$sub_adj_amt = $CLSReports->numberFormat($sub_adj_amt,2);
		$sub_refund_amt = $CLSReports->numberFormat($sub_refund_amt,2);		
		$sub_charges_amt = $CLSReports->numberFormat($sub_charges_amt,2);
		$sub_balance_amt = $CLSReports->numberFormat($sub_balance_amt,2);
		$sub_payment_amt = $CLSReports->numberFormat($sub_payment_amt,2);
		
			$adj_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Sub Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_adj_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_refund_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
	}
	$tot_adj_amt = $CLSReports->numberFormat(array_sum($arrTotal['adj']),2);
	$tot_refund_amt = $CLSReports->numberFormat(array_sum($arrTotal['refund']),2);
	$tot_charges_amt = $CLSReports->numberFormat(array_sum($arrTotal['charges']),2);
	$tot_balance_amt = $CLSReports->numberFormat(array_sum($arrTotal['balance']),2);
	$tot_payment_amt = $CLSReports->numberFormat(array_sum($arrTotal['payment']),2);
	$adj_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr bgcolor="#ffffff">
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_adj_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_refund_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="Total:";
				$arr[]=$tot_charges_amt;
				$arr[]=$tot_payment_amt;
				$arr[]=$tot_adj_amt;
				$arr[]=$tot_refund_amt;
				$arr[]=$tot_balance_amt;
				fputcsv($fp,$arr, ",","\"");
$adj_data .= <<<DATA
	</table>
DATA;
		//PDF DATA
		$pdf_file_content .=<<<DATA
			<page backtop="12mm" backbottom="10mm">
			<page_footer>
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$pdf_header
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
                <tr class="rpt_headers">
                    <td class="text_b_w" style="width:1070px;">Adjustment Summary</td>	
                </tr>
			</table>
			</page_header>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			$adj_data
			</page>
DATA;

$adj_data= $adj_data_page_title.$adj_data;
}//END ADJUST IF

}

if($dispGrand==1){
$grd_adj_amt = $CLSReports->numberFormat(array_sum($arrGrand['write_off']),2);
$grd_refund_amt = $CLSReports->numberFormat(array_sum($arrGrand['refund']),2);
$grd_charges_amt = $CLSReports->numberFormat(array_sum($arrGrand['charges']),2);
$grd_balance_amt = $CLSReports->numberFormat(array_sum($arrGrand['balance']),2);
$grd_payment_amt = $CLSReports->numberFormat(array_sum($arrGrand['payment']),2);
$first_col = $phy_col1 + $fac_col1;
$first_col = $first_col."%";
$grand_data.= <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
		<tr id=""><td class="text_b_w" colspan="$total_cols" style="width:1060px;text-align:left;">Grand Totals</td></tr>
		<tr><td colspan="$total_cols" class="total-row"></td></tr>
        <tr>
            <td class="text_10b" style="text-align:right; width:$phy_col;"></td>
            <td class="text_10b" style="text-align:right; width:$fac_col;">Grand Total:</td>
            <td class="text_10b" style="text-align:right; width:$w_cols;">$grd_charges_amt</td>
            <td class="text_10b" style="text-align:right; width:$w_cols;">$grd_payment_amt</td>
            <td class="text_10b" style="text-align:right; width:$w_cols;">$grd_adj_amt</td>
            <td class="text_10b" style="text-align:right; width:$w_cols;">$grd_refund_amt</td>
            <td class="text_10b" style="text-align:right; width:$w_cols;">$grd_balance_amt</td>
		</tr>
		<tr><td colspan="$total_cols" class="total-row"></td></tr>
		</table>
DATA;
		//FOR CSV
		$arr=array();
		$arr[]="";
		$arr[]="Grand Total:";
		$arr[]=$grd_charges_amt;
		$arr[]=$grd_payment_amt;
		$arr[]=$grd_adj_amt;
		$arr[]=$grd_refund_amt;
		$arr[]=$grd_balance_amt;
		fputcsv($fp,$arr, ",","\"");
}
fclose($fp);
$page_data=
	$pdf_header. 
	$write_data.
	$dis_data.
	$adj_data.
	$grand_data;
	
$pdf_file_content.= $grand_data;

//TRASHING VARIABLES
$pdf_header=$write_data=$dis_data=$adj_data=$grand_data='';
?>