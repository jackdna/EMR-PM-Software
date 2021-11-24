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
FILE : missing_encounter_summary.php
PURPOSE :  MISSING ENCOUNTER REPORT SUMMARRY VIEW
ACCESS TYPE : INCLUDED
*/
if($_POST['form_submitted']){
	list($m,$d,$y) = preg_split('/-/',$sel_date);

	$sel_pro_id_arr = preg_split('/,/',$sel_pro);
	$arr=array();
	$arr[]="Physician";
	$arr[]="Facility";
	$arr[]="Total Patient Seen";
	$arr[]="Total Superbill: Created";
	$arr[]="Total Superbill: Not Created";
	$arr[]="Total Superbill: Not Applied";
	fputcsv($fp,$arr, ",","\"");

	//---- SUMMARY END OF DAY REPORT -------
	$totalSchApp = 0;
	$paidForProcArr = array();
	//--- START LOOP FOR DISPLAY DATA ----
	$csv_part=$pdf_part='';
	foreach($appointmentDataArr as $sel_pro_id => $facilityData){
		foreach($facilityData as $facId){

			$seen = count($arrSummarySeen[$sel_pro_id][$facId]);
			$sbCreated = count($superbillCreate[$sel_pro_id][$facId]);
			$sbNotCreated = count($superbillNotCreate[$sel_pro_id][$facId]);
			$sbProcessed = count($superbillNotProc[$sel_pro_id][$facId]);
			
			$arrSub['seen']+= $seen;
			$arrSub['created']+= $sbCreated;
			$arrSub['not_created']+= $sbNotCreated;
			$arrSub['processed']+= $sbProcessed;

			$csv_part.='
			<tr bgcolor="#FFFFFF" style="height:25px">
				<td class="text_10" style="text-align:left;" >&nbsp;'.ucwords(trim($physicianNameArr[$sel_pro_id])).'</td>
				<td class="text_10" style="text-align:left;">&nbsp;'.$arrFacilityNames[$facId].'</td>
				<td class="text_10" style="text-align:right;">'.$seen.'&nbsp;</td>
				<td class="text_10" style="text-align:right;">'.$sbCreated.'&nbsp;</td>
				<td class="text_10" style="text-align:right;">'.$sbNotCreated.'&nbsp;</td>
				<td class="text_10" style="text-align:right;">'.$sbProcessed.'&nbsp;</td>
			</tr>';
			$pdf_part.='
			<tr bgcolor="#FFFFFF" style="height:25px">
				<td class="text_10" style="text-align:left; width:175px" >&nbsp;'.ucwords(trim($physicianNameArr[$sel_pro_id])).'</td>
				<td class="text_10" style="text-align:left; width:175px">&nbsp;'.$arrFacilityNames[$facId].'</td>
				<td class="text_10" style="text-align:right; width:175px">'.$seen.'&nbsp;</td>
				<td class="text_10" style="text-align:right; width:175px">'.$sbCreated.'&nbsp;</td>
				<td class="text_10" style="text-align:right; width:175px">'.$sbNotCreated.'&nbsp;</td>
				<td class="text_10" style="text-align:right; width:175px">'.$sbProcessed.'&nbsp;</td>
			</tr>';
		
			$arr=array();
			$arr[]=ucwords(trim($physicianNameArr[$sel_pro_id]));
			$arr[]=$arrFacilityNames[$facId];
			$arr[]=$seen;
			$arr[]=$sbCreated;
			$arr[]=$sbNotCreated;
			$arr[]=$sbProcessed;
			fputcsv($fp,$arr, ",","\"");
		}
	}
	$csv_part.='
	<tr bgcolor="#FFFFFF" style="height:25px">
		<td class="text_10b" style="text-align:right;" colspan="2">Total :</td>
		<td class="text_10b" style="text-align:right;">'.$arrSub['seen'].'&nbsp;</td>
		<td class="text_10b" style="text-align:right;">'.$arrSub['created'].'&nbsp;</td>
		<td class="text_10b" style="text-align:right;">'.$arrSub['not_created'].'&nbsp;</td>
		<td class="text_10b" style="text-align:right;">'.$arrSub['processed'].'&nbsp;</td>
	</tr>';
	$pdf_part.='
	<tr bgcolor="#FFFFFF" style="height:25px">
		<td class="text_10b" style="text-align:right;" colspan="2">Total :</td>
		<td class="text_10b" style="text-align:right;">'.$arrSub['seen'].'&nbsp;</td>
		<td class="text_10b" style="text-align:right;">'.$arrSub['created'].'&nbsp;</td>
		<td class="text_10b" style="text-align:right;">'.$arrSub['not_created'].'&nbsp;</td>
		<td class="text_10b" style="text-align:right;">'.$arrSub['processed'].'&nbsp;</td>
	</tr>';	


	
	if($csv_part!=''){
		$strHTML .= <<<DATA
			<page backtop="20mm" backbottom="5mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" >
					<tr class="rpt_headers">
						<td class="rptbx1" style="width:342px;">&nbsp;Unapplied Superbills ($summary_detail)</td>
						<td class="rptbx2" style="width:350px;">&nbsp;Date: From $startDate1 To '.$endDate1</td>
						<td class="rptbx3" style="width:350px;">&nbsp;Created by $op_name on $curDate &nbsp;</td>
					</tr>
					<tr class="rpt_headers">
						<td class="rptbx1">	&nbsp;Selected Group : $selGrp </td>
						<td class="rptbx2"> &nbsp;Selected Facility : $selFac </td>
						<td class="rptbx3"> &nbsp;Selected Physician : $selPhy &nbsp;&nbsp;&nbsp;Encounter Status : $sel_enc_status </td>
					</tr>	
				</table>								
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" >						
					<tr>
						<td class="text_b_w" style="text-align:center">Physician</td>
						<td class="text_b_w" style="text-align:center">Facility</td>
						<td class="text_b_w" style="text-align:center">Total Patient Seen</td>
						<td class="text_b_w" colspan="3" style="text-align:center">Total Superbill</td>
					</tr>
					<tr>
						<td class="text_b_w" style="width:175px; text-align:center"></td>
						<td class="text_b_w" style="width:175px; text-align:center"></td>
						<td class="text_b_w" style="width:175px; text-align:center"></td>
						<td class="text_b_w" style="width:175px; text-align:center">Created</td>
						<td class="text_b_w" style="width:175px; text-align:center">Not Created</td>
						<td class="text_b_w" style="width:175px; text-align:center">Not Applied</td>
					</tr>					
				</table>
			</page_header>
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				$pdf_part
				</table>
			</page>
DATA;
		
		//--- CSV FILE DATA
		$csv_data .='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:300px;">&nbsp;Unapplied Superbills ('.$summary_detail.')</td>
				<td class="rptbx2" style="width:300px;">&nbsp;Date: From '.$startDate1.' To '.$endDate1.'</td>
				<td class="rptbx3" style="width:450px;">&nbsp;Created by '.$op_name.' on '.$curDate.'&nbsp;</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1">	&nbsp;Selected Group : '.$selGrp.' </td>
				<td class="rptbx2"> &nbsp;Selected Facility : '.$selFac.' </td>
				<td class="rptbx3"> &nbsp;Selected Physician : '.$selPhy.' &nbsp;&nbsp;&nbsp;Encounter Status : '.$sel_enc_status.' </td>
			</tr>	
		</table>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
			<tr>
				<td class="text_b_w" style="text-align:center">Physician</td>
				<td class="text_b_w" style="text-align:center">Facility</td>
				<td class="text_b_w" style="text-align:center">Total Patient Seen</td>
				<td class="text_b_w" colspan="3" style="text-align:center">Total Superbill</td>
			</tr>
			<tr>
				<td class="text_b_w" width="250px" align="center"></td>
				<td class="text_b_w" width="250px" align="center"></td>
				<td class="text_b_w" width="auto" align="center"></td>
				<td class="text_b_w alignCenter" width="180px" style="text-align:center">Created</td>
				<td class="text_b_w" align="center" width="180px" style="text-align:center">Not Created</td>
				<td class="text_b_w" align="center" width="180px" style="text-align:center">Not Applied</td>
			</tr>
			'.$csv_part.'
		</table>';				

$conditionChk = true;
	}

}
?>