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
	$total_cols = 11;
	$phy_col = $phy_col1 ="15";
	$fac_col = "25";
	$pat_col = $pat_col1 = "18";
	$w_cols = $w_cols1 = floor((100 - ($phy_col + $fac_col+$pat_col))/($total_cols-3));
	$fac_col = $fac_col1 = 100 - ( (($total_cols-3) * $w_cols) + $phy_col + $pat_col);
	
	//$w_cols = $w_cols."%";
	//$phy_col = $phy_col."%";
	//$fac_col = $fac_col."%";
	$pat_col = $pat_col."%";

	$w_cols = "70px";
	$phy_col = "140px";
	$fac_col = "210px";
    $pat_col = "170px";
	$refundTitle='';
	//if($dtRangeFor=='dos')$refundTitle='Refund';
	
	//--- CSV FILE HEADER INFORMATION 

	$pdf_header = <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
            <tr class="rpt_headers">
                <td class="rptbx1" style="width:260px;">&nbsp;Adjustment Report - Detail</td>	
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
$arr[]="Adjustment Report (Detail)";
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
	
	$arr[]="Patient Name";
	$arr[]="DOS";
	$arr[]="DOT";
	$arr[]="Physician";
	$arr[]="Facility";
	$arr[]="Charges";
	$arr[]="Payment";
	$arr[]="Write-Off";
	$arr[]=$refundTitle;
	$arr[]="Check#";
	$arr[]="Balance";
	
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');
	
	$write_data_title.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr>	
				<td class="text_b_w" style="width:1050px;" colspan="$total_cols">Write-off Detail</td>	
			</tr>
DATA;

	$arrCodeId = array_keys($arrWriteOffCode['code']);

    $writeOffOperators = array_keys($arrWriteOffCode['woff_operator']);
	foreach($arrCodeId as $writeCodeId){
		
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
					<td class="text_b_w" style="text-align:center; width:$pat_col;">Patient Name</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">DOS</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">DOT</td>
					<td class="text_b_w" style="text-align:center; width:$phy_col;">Physician</td>
					<td class="text_b_w" style="text-align:center; width:$fac_col;">Facility</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Charges</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Payment</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Write-Off</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">$refundTitle</td>
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
				foreach($arrPatData as $patient_id=>$arrEncData){
					$pat_name = $arrPatient[$patient_id];
					foreach($arrEncData as $enc_id=>$arrchldData){
						$dos = $arrWriteOffCode[$writeCodeId][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['dos'];
						$write_off_amt = $arrWriteOffCode[$writeCodeId][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['write_off'];
						$write_off_dot = $arrWriteOffCode[$writeCodeId][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['write_off_dot'];

						foreach($arrchldData as $chld=>$arrChld){
                           if(!in_array($chld.'~~'.$writeCodeId, $chld_wofarr)) {
                                $chld_wofarr[]=$chld.'~~'.$writeCodeId;
                                $arrAmount['charges'][$enc_id][$chld] = $arrWriteOffCode['charge_list_detail_id'][$chld]['charges'];
                                $arrAmount['balance'][$enc_id][$chld] = $arrWriteOffCode['charge_list_detail_id'][$chld]['balance'];
                                $arrAmount['payment'][$enc_id][$chld] = $arrWriteOffCode['charge_list_detail_id'][$chld]['payment'];
                            
                                if(is_numeric($chld))
                                $dos = $arrWriteOffCode['charge_list_detail_id'][$chld]['dos'];
								$dot = $arrWriteOffCode['charge_list_detail_id'][$chld]['write_off_date1'];

                                foreach($pay_crd_deb_chld_arr[$chld] as $key=>$cre_deb_amt){
                                    if(!isset($pay_crd_deb_chld_arr[$chld][$key]['applied'])){
                                        $arrAmount['payment'][$enc_id][$chld] += $cre_deb_amt;
                                        $arrAmount['balance'][$enc_id][$chld] -= $cre_deb_amt;
                                        $pay_crd_deb_chld_arr[$chld][$key]['applied'] = 'yes';
                                    }
                                }
                           }
						}
						
						$charges_amt = array_sum($arrAmount['charges'][$enc_id]);
						$balance_amt = array_sum($arrAmount['balance'][$enc_id]);
						$payment_amt = array_sum($arrAmount['payment'][$enc_id]);
						
						$subArr['charges'][] = $charges_amt;
						$subArr['balance'][] = $balance_amt;
						$subArr['payment'][] = $payment_amt;
						$subArr['write_off'][] = $write_off_amt;

						$write_off_amt = $CLSReports->numberFormat($write_off_amt,2);
						$charges_amt = $CLSReports->numberFormat($charges_amt,2);
						$balance_amt = $CLSReports->numberFormat($balance_amt,2);
						$payment_amt = $CLSReports->numberFormat($payment_amt,2);
						$write_data.= <<<DATA
							<tr>
								<td class="text_10" valign="top" style="text-align:left; width:$pat_col; word-wrap:break-word;">$pat_name</td>
								<td class="text_10" valign="top" style="text-align:center; width:$w_cols; word-wrap:break-word;">$dos</td>
								<td class="text_10" valign="top" style="text-align:center; width:$w_cols; word-wrap:break-word;">$write_off_dot</td>
								<td class="text_10" valign="top" style="text-align:left; width:$phy_col; word-wrap:break-word;">$prov_name</td>
								<td class="text_10" valign="top" style="text-align:left; width:$fac_col; word-wrap:break-word;">$fac_name</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$charges_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$payment_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$write_off_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;"></td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;"></td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$balance_amt</td>
							</tr>
DATA;
							//FOR CSV
							$arr=array();
							$arr[]=$pat_name;
							$arr[]=$dos;
							$arr[]=$write_off_dot;
							$arr[]=$prov_name;
							$arr[]=$fac_name;
							$arr[]=$charges_amt;
							$arr[]=$payment_amt;
							$arr[]=$write_off_amt;
							$arr[]="";
							$arr[]="";
							$arr[]=$balance_amt;
							fputcsv($fp,$arr, ",","\"");	
						}
					}
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
		
		$sub_write_off_amt = $CLSReports->numberFormat($sub_write_off_amt,2);
		$sub_charges_amt = $CLSReports->numberFormat($sub_charges_amt,2);
		$sub_balance_amt = $CLSReports->numberFormat($sub_balance_amt,2);
		$sub_payment_amt = $CLSReports->numberFormat($sub_payment_amt,2);
			$write_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Sub Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_write_off_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
	}
	$tot_write_off_amt = array_sum($arrTotal['write_off']);
	$tot_charges_amt = array_sum($arrTotal['charges']);
	$tot_balance_amt = array_sum($arrTotal['balance']);
	$tot_payment_amt = array_sum($arrTotal['payment']);
		
	$arrGrand['write_off'][] = $tot_write_off_amt;
	$arrGrand['charges'][] = $tot_charges_amt;
	$arrGrand['balance'][] = $tot_balance_amt;
	$arrGrand['payment'][] = $tot_payment_amt;
			
	$tot_write_off_amt = $CLSReports->numberFormat($tot_write_off_amt,2);
	$tot_charges_amt = $CLSReports->numberFormat($tot_charges_amt,2);
	$tot_balance_amt = $CLSReports->numberFormat($tot_balance_amt,2);
	$tot_payment_amt = $CLSReports->numberFormat($tot_payment_amt,2);
	$write_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_write_off_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="Write Off Code Total:";
				$arr[]=$tot_charges_amt;
				$arr[]=$tot_payment_amt;
				$arr[]=$tot_write_off_amt;
				$arr[]="";
				$arr[]="";
				$arr[]=$tot_balance_amt;
				fputcsv($fp,$arr, ",","\"");
	$write_data .= <<<DATA
	</table>
DATA;

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
		<td class="text_b_w" style="width:1070px;">Write-off Detail</td>	
	</tr>
	</table>
	</page_header>
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
	$write_data
	</page>
DATA;

	$write_data= $write_data_title.$write_data;
}//END WRITEOFF IF
$write_data.='<br>';
// DISCOUNT CODE
if(sizeof($arrDisData)>0){
	$arrTotal = array();
	$dispGrand = 1;
	$printFile = true;
	$dis_data_title.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr>	
				<td align="left" class="text_b_w"  style="width:1050px;" colspan="$total_cols">Discount Detail</td>	
			</tr>
DATA;
		//FOR CSV
		$arr=array();
		$arr[]="Discount Detail";
		fputcsv($fp,$arr, ",","\"");
		
		//FOR CSV
		$arr=array();
		$arr[]="Patient Name";
		$arr[]="DOS";
		$arr[]="DOT";
		$arr[]="Physician";
		$arr[]="Facility";
		$arr[]="Charges";
		$arr[]="Payment";
		$arr[]="Discount";
		$arr[]=$refundTitle;
		$arr[]="";
		$arr[]="Balance";
		fputcsv($fp,$arr, ",","\"");
		
		
    $writeOffOperators = array_keys($arrDisData['woff_operator']);
	$arrCodeId = array_keys($arrDisData['code']);
	foreach($arrCodeId as $code_id){
		
		$subArr = array();
		$code_name = $dis_off_code_arr[$code_id];
		if($code_id == 0)
		$code_name = "Not Specified";
		
		$dis_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">Code - $code_name</td>
		</tr>
DATA;
		//FACILITY
		$dis_data.= <<<DATA
				<tr>
					<td class="text_b_w" style="text-align:center; width:$pat_col;">Patient Name</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">DOS</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">DOT</td>
					<td class="text_b_w" style="text-align:center; width:$phy_col;">Physician</td>
					<td class="text_b_w" style="text-align:center; width:$fac_col;">Facility</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Charges</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Payment</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Discount</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">$refundTitle</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;"></td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Balance</td>
				</tr>				
DATA;
        foreach($writeOffOperators as $writeOffOperatorId){
            $woperator_name = $operater_name[$writeOffOperatorId];
            if($writeOffOperatorId == 0)
            $woperator_name = "Not Specified";
            if(empty($arrDisData[$code_id][$writeOffOperatorId])) {continue; }
            $dis_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">Operator - $woperator_name</td>
		</tr>
DATA;
		foreach($arrDisData[$code_id][$writeOffOperatorId] as $providerId=>$arrFacData){
			$prov_name = $arrProvider[$providerId];
			foreach($arrFacData as $facilityId=>$arrPatData)
			{
				$arrAmount = array();
				$fac_name = ($billing_location=='1')? $arrSchFacilites[$facilityId] : $arrFacility[$facilityId];
				foreach($arrPatData as $patient_id=>$arrEncData){
					$pat_name = $arrPatient[$patient_id];
					
					foreach($arrEncData as $enc_id=>$arrchldData){
						$dos = $arrDisData[$code_id][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['dos'];
						$dis_amt = $arrDisData[$code_id][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['dis'];
						
						$write_off_dot = $arrDisData[$code_id][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['write_off_dot'];
												
						foreach($arrchldData as $chld=>$arrChld){
                            if(!in_array($chld.'~~'.$writeCodeId, $chld_disarr)) {
                                $chld_disarr[]=$chld.'~~'.$writeCodeId;
                                $arrAmount['charges'][$enc_id][$chld] = $arrDisData['charge_list_detail_id'][$chld]['charges'];
                                $arrAmount['balance'][$enc_id][$chld] = $arrDisData['charge_list_detail_id'][$chld]['balance'];
                                $arrAmount['payment'][$enc_id][$chld] = $arrDisData['charge_list_detail_id'][$chld]['payment'];

                                if(is_numeric($chld))
                                $dos = $arrDisData['charge_list_detail_id'][$chld]['dos'];
                                foreach($pay_crd_deb_chld_arr[$chld] as $key=>$cre_deb_amt){
                                    if(!isset($pay_crd_deb_chld_arr[$chld][$key]['applied'])){
                                        $arrAmount['payment'][$enc_id][$chld] += $cre_deb_amt;
                                        $arrAmount['balance'][$enc_id][$chld] -= $cre_deb_amt;
                                        $pay_crd_deb_chld_arr[$chld][$key]['applied'] = 'yes';
                                    }
                                }
                            }
						}
						
						$subArr['charges'][] = array_sum($arrAmount['charges'][$enc_id]);
						$subArr['balance'][] =  array_sum($arrAmount['balance'][$enc_id]);
						$subArr['payment'][] = array_sum($arrAmount['payment'][$enc_id]);
						$subArr['dis'][] = $dis_amt;
						
						$dis_amt = $CLSReports->numberFormat($dis_amt,2);
						$charges_amt = $CLSReports->numberFormat(array_sum($arrAmount['charges'][$enc_id]),2);
						$balance_amt = $CLSReports->numberFormat(array_sum($arrAmount['balance'][$enc_id]),2);
						$payment_amt = $CLSReports->numberFormat(array_sum($arrAmount['payment'][$enc_id]),2);
						$dis_data.= <<<DATA
							<tr>
								<td class="text_10" valign="top" style="text-align:left; width:$pat_col; word-wrap:break-word;">$pat_name</td>
								<td class="text_10" valign="top" style="text-align:center; width:$w_cols; word-wrap:break-word;">$dos</td>
								<td class="text_10" valign="top" style="text-align:center; width:$w_cols; word-wrap:break-word;">$write_off_dot</td>
								<td class="text_10" valign="top" style="text-align:left; width:$phy_col; word-wrap:break-word;">$prov_name</td>
								<td class="text_10" valign="top" style="text-align:left; width:$fac_col; word-wrap:break-word;">$fac_name</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$charges_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$payment_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$dis_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;"></td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;"></td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$balance_amt</td>
							</tr>
DATA;
							//FOR CSV
							$arr=array();
							$arr[]=$pat_name;
							$arr[]=$dos;
							$arr[]=$write_off_dot;
							$arr[]=$prov_name;
							$arr[]=$fac_name;
							$arr[]=$charges_amt;
							$arr[]=$payment_amt;
							$arr[]=$dis_amt;
							$arr[]="";
							$arr[]="";
							$arr[]=$balance_amt;
							fputcsv($fp,$arr, ",","\"");
						}
					}
				}
			}
        }
		$arrTotal['dis'][] = array_sum($subArr['dis']);
		$arrTotal['charges'][] = array_sum($subArr['charges']);
		$arrTotal['balance'][] = array_sum($subArr['balance']);
		$arrTotal['payment'][] = array_sum($subArr['payment']);
		
		$sub_dis_amt = $CLSReports->numberFormat(array_sum($subArr['dis']),2);
		$sub_charges_amt = $CLSReports->numberFormat(array_sum($subArr['charges']),2);
		$sub_balance_amt = $CLSReports->numberFormat( array_sum($subArr['balance']),2);
		$sub_payment_amt = $CLSReports->numberFormat( array_sum($subArr['payment']),2);
			$dis_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Sub Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_dis_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
	}
	$arrGrand['write_off'][] = array_sum($arrTotal['dis']);
	$arrGrand['charges'][] = array_sum($arrTotal['charges']);
	$arrGrand['balance'][] = array_sum($arrTotal['balance']);
	$arrGrand['payment'][] = array_sum($arrTotal['payment']);
		
	$tot_dis_amt = $CLSReports->numberFormat(array_sum($arrTotal['dis']),2);
	$tot_charges_amt = $CLSReports->numberFormat( array_sum($arrTotal['charges']),2);
	$tot_balance_amt = $CLSReports->numberFormat(array_sum($arrTotal['balance']),2);
	$tot_payment_amt = $CLSReports->numberFormat(array_sum($arrTotal['payment']),2);
	$dis_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_dis_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr></table>	
DATA;
				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="Discount Code Total:";
				$arr[]=$tot_charges_amt;
				$arr[]=$tot_payment_amt;
				$arr[]=$tot_dis_amt;
				$arr[]="";
				$arr[]="";
				$arr[]=$tot_balance_amt;
				fputcsv($fp,$arr, ",","\"");	
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
			<td align="left" class="text_b_w"  style="width:1070px;">Discount Detail</td>	
		</tr>
		</table>
		</page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
		$dis_data
		</page>
DATA;

	$dis_data= $dis_data_title.$dis_data;
	
}//END DISCOUNT IF

// ADJUST CODE
if(sizeof($arrAdjData)>0){
	$arrTotal = array();
	$dispGrand = 1;
	$printFile = true;
	$adj_data_title.= <<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
			<tr>	
				<td align="left" class="text_b_w" style="width:1050px;" colspan="$total_cols">Adjustment Detail</td>	
			</tr>
DATA;
	
				//FOR CSV
				$arr=array();
				$arr[]="Adjustment Detail";
				fputcsv($fp,$arr, ",","\"");	
				
				$arr=array();
				$arr[]="Patient Name";
				$arr[]="DOS";
				$arr[]="DOT";
				$arr[]="Physician";
				$arr[]="Facility";
				$arr[]="Charges";
				$arr[]="Payment";
				$arr[]="Adjustment";
				$arr[]="Refund";
				$arr[]="Check#";
				$arr[]="Balance";
				fputcsv($fp,$arr, ",","\"");	
	
    $writeOffOperators = array_keys($arrAdjData['woff_operator']);
	$arrCodeId = array_keys($arrAdjData['code']);
	foreach($arrCodeId as $code_id){
		$subArr = array();
		$code_name = $adj_off_code_arr[$code_id];
		if($tempResCodeArr[$code_id]!=""){
			$reason_code = 'Reason Code - '.$tempResCodeArr[$code_id];
		}
		if($code_id == 0){
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
					<td class="text_b_w" style="text-align:center; width:$pat_col;">Patient Name</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">DOS</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">DOT</td>
					<td class="text_b_w" style="text-align:center; width:$phy_col;">Physician</td>
					<td class="text_b_w" style="text-align:center; width:$fac_col;">Facility</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Charges</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Payment</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Adjustment</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Refund</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Check#</td>
					<td class="text_b_w" style="text-align:center; width:$w_cols;">Balance</td>
				</tr>				
DATA;
        foreach($writeOffOperators as $writeOffOperatorId){
            $woperator_name = $operater_name[$writeOffOperatorId];
            if($writeOffOperatorId == 0)
            $woperator_name = "Not Specified";
            if(empty($arrAdjData[$code_id][$writeOffOperatorId])) {continue; }
            $adj_data.= <<<DATA
		<tr>
			<td class="text_b_w" align="left" colspan="$total_cols">Operator - $woperator_name</td>
		</tr>
DATA;
		foreach($arrAdjData[$code_id][$writeOffOperatorId] as $providerId=>$arrFacData){
			$prov_name = $arrProvider[$providerId];
			foreach($arrFacData as $facilityId=>$arrPatData)
			{
				$arrAmount = array();
				$fac_name = ($billing_location=='1')? $arrSchFacilites[$facilityId] : $arrFacility[$facilityId];
				foreach($arrPatData as $patient_id=>$arrEncData){
					$pat_name = $arrPatient[$patient_id];
					foreach($arrEncData as $enc_id=>$arrchldData){
						$checkNos='';
						$dos = $arrAdjData[$code_id][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['dos'];
						$adj_amt = $arrAdjData[$code_id][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['adj'];
						$refund_amt = $arrAdjData[$code_id][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['refund'];
						$write_off_dot = $arrAdjData[$code_id][$writeOffOperatorId][$providerId][$facilityId][$patient_id][$enc_id]['write_off_dot'];


						
						//check number
						if(sizeof($arrAdjData[$paymentCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['check_no'])>0){
							$checkNos=implode(', ',$arrAdjData[$paymentCodeId][$writeOperatorId][$providerId][$facilityId][$patientId][$encId]['check_no']);
						}
						
						foreach($arrchldData as $chld=>$arrChld){
                            if(!in_array($chld.'~~'.$writeCodeId, $chld_arr)) {
                                $chld_arr[]=$chld.'~~'.$writeCodeId;
                                $arrAmount['charges'][$enc_id][$chld] = $arrAdjData['charge_list_detail_id'][$chld]['charges'];
                                $arrAmount['balance'][$enc_id][$chld] = $arrAdjData['charge_list_detail_id'][$chld]['balance'];
                                $arrAmount['payment'][$enc_id][$chld] = $arrAdjData['charge_list_detail_id'][$chld]['payment'];
                            
                                if(is_numeric($chld))
                                $dos = $arrAdjData['charge_list_detail_id'][$chld]['dos'];
								
                                foreach($pay_crd_deb_chld_arr[$chld] as $key=>$cre_deb_amt){
                                    if(!isset($pay_crd_deb_chld_arr[$chld][$key]['applied'])){
                                        $arrAmount['payment'][$enc_id][$chld] += $cre_deb_amt;
                                        $arrAmount['balance'][$enc_id][$chld] -= $cre_deb_amt;
                                        $pay_crd_deb_chld_arr[$chld][$key]['applied'] = 'yes';
                                    }
                                }

								if($enc_id==1948158){
									echo $chld.' - '.$dos.'-'.$adj_amt.'<br>';	
								}	
								
                            }
							if($write_off_dot == ""){
								$write_off_dot = $arrAdjData['write_off_dot'][$chld];
							}
						}
						
						$subArr['charges'][] = array_sum($arrAmount['charges'][$enc_id]);
						$subArr['balance'][] = array_sum($arrAmount['balance'][$enc_id]);
						$subArr['payment'][] = array_sum($arrAmount['payment'][$enc_id]);
						$subArr['adj'][] = $adj_amt;
						$subArr['refund'][] = $refund_amt;

						

						
						$adj_amt = $CLSReports->numberFormat($adj_amt,2);
						$refund_amt = $CLSReports->numberFormat($refund_amt,2);
						$charges_amt = $CLSReports->numberFormat(array_sum($arrAmount['charges'][$enc_id]),2);
						$balance_amt = $CLSReports->numberFormat(array_sum($arrAmount['balance'][$enc_id]),2);
						$payment_amt = $CLSReports->numberFormat(array_sum($arrAmount['payment'][$enc_id]),2);
						$adj_data.= <<<DATA
							<tr>
								<td class="text_10" valign="top" style="text-align:left; width:$pat_col; word-wrap:break-word;">$pat_name</td>
								<td class="text_10" valign="top" style="text-align:center; width:$w_cols; word-wrap:break-word;">$dos</td>
								<td class="text_10" valign="top" style="text-align:center; width:$w_cols; word-wrap:break-word;">$write_off_dot</td>
								<td class="text_10" valign="top" style="text-align:left; width:$phy_col; word-wrap:break-word;">$prov_name</td>
								<td class="text_10" valign="top" style="text-align:left; width:$fac_col; word-wrap:break-word;">$fac_name</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$charges_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$payment_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$adj_amt</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$refund_amt</td>
								<td class="text_10" valign="top" style="text-align:center; width:$w_cols; word-wrap:break-word;">$checkNos</td>
								<td class="text_10" valign="top" style="text-align:right; width:$w_cols; word-wrap:break-word;">$balance_amt</td>
							</tr>
DATA;
							//FOR CSV
							$arr=array();
							$arr[]=$pat_name;
							$arr[]=$dos;
							$arr[]=$write_off_dot;
							$arr[]=$prov_name;
							$arr[]=$fac_name;
							$arr[]=$charges_amt;
							$arr[]=$payment_amt;
							$arr[]=$adj_amt;
							$arr[]=$refund_amt;
							$arr[]=$checkNos;
							$arr[]=$balance_amt;
							fputcsv($fp,$arr, ",","\"");	
						}
					}
				}
			}
        }
		$arrTotal['adj'][] = array_sum($subArr['adj']);
		$arrTotal['refund'][] = array_sum($subArr['refund']);
		$arrTotal['charges'][] = array_sum($subArr['charges']);
		$arrTotal['balance'][] = array_sum($subArr['balance']);
		$arrTotal['payment'][] = array_sum($subArr['payment']);
		
		$sub_adj_amt = $CLSReports->numberFormat(array_sum($subArr['adj']),2);
		$sub_refund_amt = $CLSReports->numberFormat(array_sum($subArr['refund']),2);
		$sub_charges_amt = $CLSReports->numberFormat(array_sum($subArr['charges']),2);
		$sub_balance_amt = $CLSReports->numberFormat(array_sum($subArr['balance']),2);
		$sub_payment_amt = $CLSReports->numberFormat(array_sum($subArr['payment']),2);
			$adj_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Sub Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_adj_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_refund_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$sub_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>		
DATA;
	}
	$arrGrand['write_off'][] = array_sum($arrTotal['adj']);
	$arrGrand['refund'][] = array_sum($arrTotal['refund']);
	$arrGrand['charges'][] = array_sum($arrTotal['charges']);
	$arrGrand['balance'][] = array_sum($arrTotal['balance']);
	$arrGrand['payment'][] = array_sum($arrTotal['payment']);
		
	$tot_adj_amt = $CLSReports->numberFormat(array_sum($arrTotal['adj']),2);
	$tot_refund_amt = $CLSReports->numberFormat(array_sum($arrTotal['refund']),2);
	$tot_charges_amt = $CLSReports->numberFormat(array_sum($arrTotal['charges']),2);
	$tot_balance_amt = $CLSReports->numberFormat(array_sum($arrTotal['balance']),2);
	$tot_payment_amt = $CLSReports->numberFormat(array_sum($arrTotal['payment']),2);
	$adj_data.= <<<DATA
				<tr><td class="total-row"  colspan="$total_cols"></td></tr>
				<tr>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
                    <td class="text_10b" valign="top"></td>
					<td class="text_10b" valign="top" style="text-align:right;">Total:</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_charges_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_payment_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_adj_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_refund_amt</td>
					<td class="text_10b" valign="top" style="text-align:right; "></td>
					<td class="text_10b" valign="top" style="text-align:right; ">$tot_balance_amt</td>
				</tr>
				<tr><td class="total-row"  colspan="$total_cols"></td></tr></table>	
DATA;
				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="Adjust Code Total:";
				$arr[]=$tot_charges_amt;
				$arr[]=$tot_payment_amt;
				$arr[]=$tot_adj_amt;
				$arr[]=$tot_refund_amt;
				$arr[]=$tot_balance_amt;
				fputcsv($fp,$arr, ",","\"");	

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
			<td class="text_b_w" style="width:1070px;">Adjustment Detail</td>	
		</tr>
		</table>
		</page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
		$adj_data
		</page>
DATA;

	$adj_data= $adj_data_title.$adj_data;

}//END ADJUST IF


	if($dispGrand==1){
		$grd_adj_amt = $CLSReports->numberFormat(array_sum($arrGrand['write_off']),2);
		$grd_refund_amt = $CLSReports->numberFormat(array_sum($arrGrand['refund']),2);
		$grd_charges_amt = $CLSReports->numberFormat(array_sum($arrGrand['charges']),2);
		$grd_balance_amt = $CLSReports->numberFormat(array_sum($arrGrand['balance']),2);
		$grd_payment_amt = $CLSReports->numberFormat(array_sum($arrGrand['payment']),2);
		$first_col = $phy_col1 + $fac_col1 + $pat_col1 + $w_cols;
		$first_col = $first_col."%";
		$grand_data.= <<<DATA
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
				<tr><td class="text_b_w" colspan="$total_cols" style="width:1060px;text-align:left;">Grand Totals</td></tr>
				<tr><td colspan="$total_cols" class="total-row"></td></tr>
                <tr>
                    <td style="text-align:right; width:$pat_col;"></td>
					<td style="text-align:right; width:$w_cols;"></td>
					<td style="text-align:right; width:$phy_col;"></td>
					<td class="text_10b" style="text-align:right; width:$fac_col;">Grand Total:</td>
					<td class="text_10b" style="text-align:right; width:$w_cols;">$grd_charges_amt</td>
					<td class="text_10b" style="text-align:right; width:$w_cols;">$grd_payment_amt</td>
					<td class="text_10b" style="text-align:right; width:$w_cols;">$grd_adj_amt</td>
					<td class="text_10b" style="text-align:right; width:$w_cols;">$grd_refund_amt</td>
					<td class="text_10b" style="text-align:right; width:$w_cols;"></td>
					<td class="text_10b" style="text-align:right; width:$w_cols;">$grd_balance_amt</td>
                </tr>
				<tr><td colspan="$total_cols" class="total-row"></td></tr>
				</table>
DATA;
				//FOR CSV
				$arr=array();
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="Grand Total:";
				$arr[]=$grd_charges_amt;
				$arr[]=$grd_payment_amt;
				$arr[]=$grd_adj_amt;
				$arr[]=$grd_refund_amt;
				$arr[]="";
				$arr[]=$grd_balance_amt;
				fputcsv($fp,$arr, ",","\"");	
	}
}
fclose($fp);	
$page_data = 
	$pdf_header.
	$write_data.
	$dis_data.
	$adj_data.
	$grand_data;
	
$pdf_file_content.=	$grand_data;
?>