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
FILE : unworked_ar_html.php
PURPOSE : Display results of Unworked A/R report
ACCESS TYPE : Direct
*/

$dataExists=false;

$firstGroupTitle = 'Physician';
$secGroupTitle = 'Facility';

if($grpby_block=='grpby_facility'){	
	$firstGroupTitle = 'Facility';
	$secGroupTitle = 'Physician';
}

// SET GLOBAL CURRENCY
$showCurrencySymbol = showCurrency();

if(sizeof($arrResultData)>0){
	$grandCharges='';
	$grandBalance='';
	$dataExists=true;
	
	if($summary_detail=='summary'){
		foreach($arrResultData as $firstGrpId => $secGrpData){
			$firstGroupName='';
			if($grpby_block=='grpby_physician'){
				$firstGroupName = $providerNameArr[$firstGrpId];
			}else{
				$firstGroupName = $posFacilityArr[$firstGrpId];			
			}
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:24%">&nbsp;'.$firstGroupName.'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:18%">'.''.$CLSReports->numberFormat($secGrpData['charges'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:58%"></td>
			</tr>';	
			$grandCharges+=	$secGrpData['charges'];
		} 
		
		
		// TOTAL
		$page_content .=' 
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr>
			<td class="text_b_w" style="text-align:left;">&nbsp;'.$firstGroupTitle.'</td>
			<td class="text_b_w" style="text-align:right;">Charges&nbsp;</td>
			<td class="text_b_w" style="text-align:right;"></td>
		</tr>'
		.$content_part.'
		<tr><td style="height: 2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Charges : </td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height: 2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
		</table>';	
		
		//PDF
		$pdfHeader='<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">
			<tr>
				<td class="text_b_w" style="width:24%; text-align:left;">&nbsp;'.$firstGroupTitle.'</td>
				<td class="text_b_w" style="width:18%; text-align:right;">Charges&nbsp;</td>
				<td class="text_b_w" style="width:58%; text-align:right;"></td>
			</tr>
		</table>';
		$pdf_content .=' 
		<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">'
		.$content_part.'
		<tr><td style="height: 2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Charges : </td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height: 2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
		</table>';	
				
	}else{
		// DETAILS
		foreach($arrResultData as $firstGrpId => $firstGrpData){
			$firstGroupName='';
			$firstGrpTotal='';
			$firstGrpBal='';
			
			if($grpby_block=='grpby_physician'){
				$firstGroupName = $providerNameArr[$firstGrpId];
			}else{
				$firstGroupName = $posFacilityArr[$firstGrpId];			
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="8">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
			$content_part_pdf.='<tr><td class="text_b_w" style="text-align:left;" colspan="7">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
			

			foreach($firstGrpData as $eid => $grpDetail){
				$pName = explode('~', $grpDetail['pat_name']);
				
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				
				$firstGrpTotal+= $grpDetail['charges'];
				$firstGrpBal+= $grpDetail['balance'];
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;'.$patient_name.'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$grpDetail['dos'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$eid.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">'.$insurance_data_arr[$grpDetail['insid']].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center;">'.$grpDetail['aging'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($grpDetail['charges'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($grpDetail['balance'],2).'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;">&nbsp;<a href="javascript:void();" class="text_10b_purpule" onclick="javascript:claim_file_fun('.$eid.','.$pName[0].');">Claim Status</a></td>
				</tr>';	

				$content_part_pdf .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:32%">&nbsp;'.$patient_name.'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:12%">'.$grpDetail['dos'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:10%">'.$eid.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:15%">'.$insurance_data_arr[$grpDetail['insid']].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:8%">'.$grpDetail['aging'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:11%">'.''.$CLSReports->numberFormat($grpDetail['charges'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:12%">'.''.$CLSReports->numberFormat($grpDetail['balance'],2).'</td>
				</tr>';	
			}
			
			$grandCharges+=	$firstGrpTotal;
			$grandBalance+=	$firstGrpBal;
			// SUB TOTAL
			$content_part.=' 
			<tr><td class="total-row" colspan="8"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">'.$firstGroupTitle.' Total : </td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($firstGrpTotal,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($firstGrpBal,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>
			<tr><td class="total-row" colspan="8"></td></tr>';	
			
			$content_part_pdf.=' 
			<tr><td class="total-row" colspan="7"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">'.$firstGroupTitle.' Total : </td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($firstGrpTotal,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($firstGrpBal,2).'&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="7"></td></tr>';			
		}
		
		// TOTAL
		$page_content .=' 
		<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr>
			<td class="text_b_w" style="width:18%; text-align:left;">&nbsp;Patient Name-Id</td>
				<td class="text_b_w" style="width:12%; text-align:center;">DOS</td>
				<td class="text_b_w" style="width:12%; text-align:center;">Encounter</td>
				<td class="text_b_w" style="width:12%; text-align:left;">Insurance</td>
				<td class="text_b_w" style="width:8%; text-align:center;">Aging</td>
				<td class="text_b_w" style="width:12%; text-align:right;">Charges&nbsp;</td>
				<td class="text_b_w" style="width:12%; text-align:right;">Balance</td>
				<td class="text_b_w" style="width:12%; text-align:left;">Claims Check</td>
		</tr>'
		.$content_part.'
		<tr><td style="height: 2px; padding: 0px; background: #009933;" colspan="8"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">Total Charges : </td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($grandBalance,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height: 2px; padding: 0px; background: #009933;" colspan="8"></td></tr>
		</table>';	
		
		//PDF
		$pdfHeader='<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">
			<tr>
				<td class="text_b_w" style="width:32%; text-align:left;">&nbsp;Patient Name-Id</td>
				<td class="text_b_w" style="width:12%; text-align:center;">DOS</td>
				<td class="text_b_w" style="width:10%; text-align:center;">Encounter</td>
				<td class="text_b_w" style="width:15%; text-align:left;">Insurance</td>
				<td class="text_b_w" style="width:8%; text-align:center;">Aging</td>
				<td class="text_b_w" style="width:11%; text-align:right;">Charges&nbsp;</td>
				<td class="text_b_w" style="width:12%; text-align:right;">Balance</td>
			</tr>
		</table>';
		$pdf_content .=' 
		<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">'
		.$content_part_pdf.'
		<tr><td style="height: 2px; padding: 0px; background: #009933;" colspan="7"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">Total Charges : </td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.''.$CLSReports->numberFormat($grandBalance,2).'&nbsp;</td>
		</tr>
		<tr><td style="height: 2px; padding: 0px; background: #009933;" colspan="7"></td></tr>
		</table>';	
	}
}
?>