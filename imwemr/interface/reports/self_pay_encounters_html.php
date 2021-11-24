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

$firstGroupTitle = 'POS Facility';
$secGroupTitle = 'Physician';

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
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="3">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
			
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
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:18%">'.$showCurrencySymbol.number_format($secGrpData['charges'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:58%"></td>
				</tr>';	
			}
			
			$grandCharges+=	$firstGrpTotal;
			// SUB TOTAL
			$content_part.=' 
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>';		
		}
		
		// TOTAL
		$page_content .=' 
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">
		<tr>
			<td class="text_b_w" style="text-align:left;">&nbsp;'.$secGroupTitle.'</td>
			<td class="text_b_w" style="text-align:right;">Charges&nbsp;</td>
			<td class="text_b_w" style="text-align:right;"></td>
		</tr>'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Charges&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
		</table>';	
		
		//PDF
		$pdfHeader='<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">
			<tr>
				<td class="text_b_w" style="width:24%; text-align:left;">&nbsp;'.$secGroupTitle.'</td>
				<td class="text_b_w" style="width:18%; text-align:right;">Charges&nbsp;</td>
				<td class="text_b_w" style="width:58%; text-align:right;"></td>
			</tr>
		</table>';
		$pdf_content .=' 
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total Charges&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="3"></td></tr>
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
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="5">&nbsp;'.$firstGroupTitle.' - '.$firstGroupName.'</td></tr>';
			
			foreach($firstGrpData as $secGrpId => $secGrpData){
				$secGroupName='';
				$secGrpTotal='';
				
				if($groupBy=='physician'){
					$secGroupName = $posFacilityArr[$secGrpId];
				}else{
					$secGroupName = $providerNameArr[$secGrpId];				
				}
				$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="5">&nbsp;'.$secGroupTitle.' - '.$secGroupName.'</td></tr>';			
				
				foreach($secGrpData as $eid => $grpDetail){
					$pName = explode('~', $grpDetail['pat_name']);
					$patient_name = core_name_format($pName[3], $pName[1], $pName[2]);	
					$patient_name.= ' - '.$pName[0];
					
					$secGrpTotal+= $grpDetail['charges'];
				
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:24%">&nbsp;'.$patient_name.'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:12%">'.$eid.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:12%">'.$grpDetail['dos'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:18%">'.$showCurrencySymbol.number_format($grpDetail['charges'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:34%"></td>
					</tr>';	
				}
				
				$firstGrpTotal+=	$secGrpTotal;
				// SUB TOTAL
				$content_part.=' 
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$secGroupTitle.' Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($secGrpTotal,2).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
				</tr>';		
			}
			
			$grandCharges+=	$firstGrpTotal;
			// SUB TOTAL
			$content_part.=' 
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($firstGrpTotal,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>';		
		}
		
		// TOTAL
		$page_content .=' 
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">
		<tr>
			<td class="text_b_w" style="text-align:left;">&nbsp;Patient Name-Id</td>
			<td class="text_b_w" style="text-align:center;">Enc. Id</td>
			<td class="text_b_w" style="text-align:center;">DOS</td>
			<td class="text_b_w" style="text-align:right;">Charges&nbsp;</td>
			<td class="text_b_w" style="text-align:right;"></td>
		</tr>'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="5"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Total Charges&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="5"></td></tr>
		</table>';	
		
		//PDF
		$pdfHeader='<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">
			<tr>
				<td class="text_b_w" style="width:24%; text-align:left;">&nbsp;Patient Name-Id</td>
				<td class="text_b_w" style="width:12%; text-align:center;">Enc. Id</td>
				<td class="text_b_w" style="width:12%; text-align:center;">DOS</td>
				<td class="text_b_w" style="width:18%; text-align:right;">Charges&nbsp;</td>
				<td class="text_b_w" style="width:34%; text-align:right;"></td>
			</tr>
		</table>';
		$pdf_content .=' 
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">'
		.$content_part.'
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="5"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Total Charges&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$showCurrencySymbol.number_format($grandCharges,2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td style="height:2px; padding: 0px; background: #009933;" colspan="5"></td></tr>
		</table>';	
	}
}

?>
