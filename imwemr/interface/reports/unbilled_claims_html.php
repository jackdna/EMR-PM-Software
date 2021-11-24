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
//--- START FACILITY LOOP ----
$dataExists=false;

$firstGroupTitle = 'Physician';
$secGroupTitle = 'Facility';

if($groupBy=='facility'){
	$firstGroupTitle = 'Facility';
	$secGroupTitle = 'Physician';
}

$showCurrencySymbol = showCurrency();

if(sizeof($arrResultData)>0){
	$grandCharges='';
	$dataExists=true;
	
	if($process=='Summary'){
		foreach($arrResultData as $firstGrpId => $firstGrpData){
			$firstGroupName='';
			$firstGrpTotal='';
			
			if($groupBy=='physician'){
				$firstGroupName = $providerNameArr[$firstGrpId];
			}else{
				$firstGroupName = $posFacilityArr[$firstGrpId];			
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="4">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
			
			foreach($firstGrpData as $secGrpId => $secGrpData){
				$secGroupName='';
				
				if($groupBy=='physician'){
					$secGroupName = $posFacilityArr[$secGrpId];
				}else{
					$secGroupName = $providerNameArr[$secGrpId];				
				}
				$firstGrpTotal+= $secGrpData['charges'];

				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:24%">&nbsp;'.$secGroupName.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:18%">'.$CLSReports->numberFormat($secGrpData['charges'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:58%"></td>
				</tr>';	
			}
			
			$grandCharges+=	$firstGrpTotal;
			// SUB TOTAL
			$content_part.=' 
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>';
		}
		
		// TOTAL
		$page_content .=' 
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">
		<tr>
			<td class="text_b_w" style="text-align:left;">&nbsp;'.$secGroupTitle.'</td>
			<td class="text_b_w" style="text-align:right;">Charges&nbsp;</td>
			<td class="text_b_w" style="text-align:right;"></td>
		</tr>'
		.$content_part.'
		<tr><td style="height:2px; background:green;" colspan="4"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Charges&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height:2px; background:green;" colspan="4"></td></tr>
		</table>';	
		
		//PDF
		$pdfHeader='<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">
			<tr>
				<td class="text_b_w" style="width:24%; text-align:left;">&nbsp;'.$secGroupTitle.'</td>
				<td class="text_b_w" style="width:18%; text-align:right;">Charges&nbsp;</td>
				<td class="text_b_w" style="width:58%; text-align:right;"></td>
			</tr>
		</table>';
		$pdf_content .=' 
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">'
		.$content_part.'
		<tr><td style="height:2px; background:green;" colspan="4"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Charges&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height:1px; background:green;" colspan="4"></td></tr>
		</table>';	
				
	}else{
		// DETAILS
		foreach($arrResultData as $firstGrpId => $firstGrpData){
			$firstGroupName='';
			$firstGrpTotal='';
			
			if($groupBy=='physician'){
				$firstGroupName = $providerNameArr[$firstGrpId];
			}else{
				$firstGroupName = $posFacilityArr[$firstGrpId];			
			}
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="6">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
			
			foreach($firstGrpData as $secGrpId => $secGrpData){
				$secGroupName='';
				$secGrpTotal='';
				
				if($groupBy=='physician'){
					$secGroupName = $posFacilityArr[$secGrpId];
				}else{
					$secGroupName = $providerNameArr[$secGrpId];				
				}
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="6">&nbsp;'.$secGroupTitle.' - '.$secGroupName.'</td></tr>';			
				
				foreach($secGrpData as $eid => $grpDetail){
					$pName = explode('~', $grpDetail['pat_name']);
					
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $pName[3];
					$patient_name_arr["FIRST_NAME"] = $pName[1];
					$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
					$patient_name = changeNameFormat($patient_name_arr);
					$patient_name.= ' - '.$pName[0];
					
					$ins_plan_name= $arrEncPlanNames[$grpDetail['case_type_id']];
					$primaryInsuranceCoId = $ins_comp_arr[$grpDetail['primaryInsuranceCoId']];
					$secGrpTotal+= $grpDetail['charges'];
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:20%">&nbsp;'.$patient_name.'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:10%">'.$eid.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:10%">'.$grpDetail['dos'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:20%">'.$primaryInsuranceCoId.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:20%">'.$ins_plan_name.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:20%">'.$CLSReports->numberFormat($grpDetail['charges'],2).'&nbsp;</td>
						
					</tr>';	
				}
				
				$firstGrpTotal+=	$secGrpTotal;
				// SUB TOTAL
				$content_part.=' 
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">'.$secGroupTitle.' Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($secGrpTotal,2).'&nbsp;</td>
				</tr>';		
			}
			
			$grandCharges+=	$firstGrpTotal;
			// SUB TOTAL
			$content_part.=' 
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($firstGrpTotal,2).'&nbsp;</td>
			</tr>';		
		}
		
		// TOTAL
		$page_content .=' 
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr>
			<td class="text_b_w" style="text-align:left;">&nbsp;Patient Name-Id</td>
			<td class="text_b_w" style="text-align:center;">Enc. Id</td>
			<td class="text_b_w" style="text-align:center;">DOS</td>
			<td class="text_b_w" style="text-align:center;">Primary Ins.</td>
			<td class="text_b_w" style="text-align:center;">Plan Name</td>
			<td class="text_b_w" style="text-align:right;">Charges&nbsp;</td>
		</tr>'
		.$content_part.'
		<tr><td style="height:2px; background:green;" colspan="6"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">Total Charges&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grandCharges,2).'&nbsp;</td>
		</tr>
		<tr><td style="height:2px; background:green;" colspan="6"></td></tr>
		</table>';	
		
		//PDF
		$pdfHeader='<table class="rpt_table rpt_table-bordered" style="width:100%;">
			<tr>
				<td class="text_b_w" style="width:20%; text-align:left;">&nbsp;Patient Name-Id</td>
				<td class="text_b_w" style="width:10%; text-align:center;">Enc. Id</td>
				<td class="text_b_w" style="width:10%; text-align:center;">DOS</td>
				<td class="text_b_w" style="width:20%; text-align:center;">Primary Ins.</td>
				<td class="text_b_w" style="width:20%; text-align:center;">Plan Name</td>
				<td class="text_b_w" style="width:20%; text-align:right;">Charges&nbsp;</td>
				
			</tr>
		</table>';
		$pdf_content .=' 
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">'
		.$content_part.'
		<tr><td style="height:2px; background:green;" colspan="6"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">Total Charges&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($grandCharges,2).'&nbsp;</td>
		</tr>
		<tr><td style="height:1px; background:green;" colspan="6"></td></tr>
		</table>';	
	}
}

?>
