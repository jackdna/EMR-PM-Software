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
FILE : detail_ins_analytics.php
PURPOSE :  INSURANCE CASES DETAIL FILE 
ACCESS TYPE : Included
*/
ob_start();
$pdf_page_content = NULL;
$csv_file_data = NULL;
$csv_content =  NULL;
$loopCount = 1;
$arrTotalCount = array();

$colSpan=($cpt_check)?12:11;

$Width=1020;
if($ins_types['pri_ins_id']){
	$PatName = 200;
	$colWidth=round(($Width-($PatName))/ ($colSpan-1));
}else{
	$PatName = 150;
	$colWidth=round(($Width-($PatName))/ ($colSpan-1));
}		
$PatName.='px';
$colWidth.='px';
$Width.='px';

$pdfWidth=1000;
if($ins_types['pri_ins_id']){
	$wPdfPatName = 150;
	$pdfcolWidth=round(($pdfWidth-($wInsName))/ ($colSpan-1));
}else{
	$wPdfPatName = 150;
	$pdfcolWidth=round(($pdfWidth-($wInsName))/ ($colSpan+1));
}

$insBilledColspan = $ins_types['pri_ins_id'] ? 1 : 2;
$ptNameColSpan = ($cpt_check) ? 2 : 1;

foreach($arrResult['insurance'] as $insID=>$patArr){
		$printFile=true;
		$ins_comp_name = $arrAllInsCompanies[$insID];
		$ptCount = count($patArr);
		$enCount = 0;
		$arrEncCPT = array();
		//$pat_detail_content = '<table class="rpt_table rpt rpt_table-bordered rpt_padding" align="right">';
		//$pdf_detail_content ='<table class="rpt_table rpt rpt_table-bordered rpt_padding" align="right">';
		
		$pat_detail_content = '<tr>
			<td class="text_b_w" width="20" >&nbsp;</td>
			<td class="text_b_w" width="'.$PatName.'" colspan="'.$ptNameColSpan.'" align="left" valign="top">Patient Name</td>
			<td class="text_b_w" align="left" valign="top">Encounter ID</td>
			<td class="text_b_w" align="left" valign="top">DOS</td>
			<td class="text_b_w" align="left" valign="top">CPT Code</td>';
			if($ins_types['pri_ins_id'])
			{
		$pat_detail_content .= '<td class="text_b_w" align="left" valign="top">Pri. Billed</td>';		
			}
			else
			{
		$pat_detail_content .= '<td class="text_b_w" align="left" valign="top">Enc Chrgs</td>';
		$pat_detail_content .= '<td class="text_b_w" align="left" valign="top">Sec. Billed</td>';		
			}
			
		$pat_detail_content .= '
			<td class="text_b_w" align="left" valign="top">Contract Fee</td>
			<td class="text_b_w" align="left" valign="top">Allowed Amt</td>
			<td class="text_b_w" align="left" valign="top">Pri. Paid</td>
			<td class="text_b_w" align="left" valign="top">Sec. Paid/ Ter. Paid</td>
			<td class="text_b_w"align="left" valign="top">Ins Bal</td>
			</tr>
			';
		//$pat_detail_csv = ",Patient Name,Encounter ID, CPT Code,";
		//$pat_detail_csv .= ($ins_types['pri_ins_id'])?"Pri. Billed,":"Enc Chrgs,Sec. Billed,";
		//$pat_detail_csv .="Contract Fee, Allowed Amt, Pri. Paid, Sec. Paid, Ins Bal\n";	
		
		$pdf_detail_content = '<tr bgcolor="#FFFFFF">
								<td class="text_b_w" width="20" >&nbsp;</td>
								<td class="text_b_w" width="'.$wPdfPatName.'" colspan="'.$ptNameColSpan.'" align="left" valign="top">Patient Name</td>
								<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Encounter ID</td>
								<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">DOS</td>
								<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" style="width:'.$pdfcolWidth.';overflow:hidden;word-wrap:break-word;" valign="top">  CPT Code</td>'; 
		if($ins_types['pri_ins_id'])
		{
			$pdf_detail_content .= '<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Pri. Billed</td>';
		}
		else
		{
			$pdf_detail_content .= '<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Enc Chrgs</td>';
			$pdf_detail_content .= '<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Sec. Billed</td>';
		}

		
		$pdf_detail_content .= '
								<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Contract Fee</td>
								<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Allowed Amt.</td>
								<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Pri. Paid</td>
								<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Sec. Paid/ Ter. Paid</td>
								<td class="text_b_w" width="'.$pdfcolWidth.'" align="left" valign="top">Ins Bal</td>
								</tr>
								';
		
		$insBilled = $ins_contractFee= $ins_approvedAmt=$insBalance = $insPaid = '';
		$arrInsTotals=array();
		foreach($patArr as $patID => $arrPat){
			$enCount += count($arrPat['encounter']);
			//$pat_detail_csv.=",".str_replace(',',' ',$arrPat['name']) . ' - '.$patID.',';
			$addFirstCol=0;
			$pat_detail_content .= '<tr bgcolor="#FFFFFF">
										<td >&nbsp;</td>
										<td class="text_10" align="left" valign="top" colspan="'.$ptNameColSpan.'" style="word-wrap: break-word;" >'.$arrPat['name'] . ' - '.$patID.'</td>
										';
			$pdf_detail_content .= 	'<tr bgcolor="#FFFFFF">
										<td >&nbsp;</td>
										<td class="text_10" align="left" valign="top" colspan="'.$ptNameColSpan.'" style="word-wrap: break-word;width:'.$wPdfPatName.'">'.$arrPat['name'] . ' - '.$patID.'</td>
										';						
			$enc_detail_content = $pdf_enc_detail_content = $enc_detail_csv='';	
			$encCount = $insBalance=0;		
			foreach($arrPat['encounter'] as $encID=>$cptArr){

				$encBilled = $encBalance =$i_contractFee =$i_approvedAmt= $encPaid = '';
				$arrEncCPT[$encID] = implode(", ",$cptArr['cpt']);
				$encBilled = $cptArr['ins_billed'];
				$i_contractFee = $cptArr['ins_contract_fee'];
				$i_approvedAmt = $cptArr['ins_approved_amt'];
				$dos = $cptArr['dos'];
				$priBilled = $cptArr['ins_pri_billed'];
				$secBilled = $cptArr['ins_sec_billed'];
				$triBilled = $cptArr['ins_tri_billed'];
				$contractFee = $cptArr['contract_fee'];
				$approvedAmt = $cptArr['approved_amt'];

				$encBalance = $cptArr['ins_due'];
				$insDueBy=isset($cptArr['ins_due_by'])?" (".implode(", ",array_unique($cptArr['ins_due_by'])).") ":"";
				$priPaid = $arrPayment[$insID][$encID]['ins_pri_paid'];
				$secPaid = $arrPayment[$insID][$encID]['ins_sec_paid'];
				$triPaid = $arrPayment[$insID][$encID]['ins_tri_paid'];
				$insPaid += $priPaid+$secPaid+$triPaid;

				$arrTotalCount['patient'][$patID]= $patID;
				$arrTotalCount['encounter'][$encID]= $encID;

				$insBilled += $encBilled;
				$ins_contractFee += $contractFee;
				$ins_approvedAmt += $approvedAmt;
				$insBalance += $encBalance;
				$insPaid += $encPaid;

				$insPaidText='';
				if($secPaid>0 || $triPaid>0){
					$insPaidText= $CLSReports->numberFormat($secPaid,2,1).'/'.$CLSReports->numberFormat($triPaid,2,1);
				}
				
				if($encCount != 0){
					$enc_detail_content .= '<tr bgcolor="#FFFFFF"><td>&nbsp;</td><td colspan="'.$ptNameColSpan.'">&nbsp;</td>';
					$pdf_enc_detail_content .= '<tr bgcolor="#FFFFFF"><td>&nbsp;</td><td colspan="'.$ptNameColSpan.'">&nbsp;</td>';
				}
				
				//if($addFirstCol==1)$enc_detail_csv.=',';
				
				//$enc_detail_csv.=str_replace(',',' ',$encID).','.str_replace(',',' ',$arrEncCPT[$encID]).',';
				//$enc_detail_csv.=($ins_types['pri_ins_id'])?$priBilled.',':$priBilled.','.$secBilled.',';
				//$enc_detail_csv.=$contractFee.','.$approvedAmt.','.$priPaid.','.$secPaid.','.$insBalance;
				//$enc_detail_csv.=$insDueBy;
				
				$enc_detail_content .= '<td class="text_10" align="left" valign="top">'.$encID.'</td>
										<td class="text_10" align="left" valign="top">'.$dos.'</td>
										<td class="text_10" align="left" style="width:'.$colWidth.';overflow:hidden;word-wrap:break-word;" valign="top" valign="top">'.$arrEncCPT[$encID].'</td>';
				
				if($ins_types['pri_ins_id'])$enc_detail_content .= '<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($priBilled,2).'</td>';
				else
				{
					$enc_detail_content .= '<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($priBilled,2).'</td>';
					$enc_detail_content .= '<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($secBilled,2).'</td>';
				}
				$enc_detail_content .='
				<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($contractFee,2).'</td>
				<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($approvedAmt,2,1).'</td>
				<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($priPaid,2).'</td>';	
				$enc_detail_content .= '<td class="text_10" align="left" valign="top">'.$insPaidText.'</td>
										<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($encBalance,2);
				$enc_detail_content .= $insDueBy;
				$enc_detail_content .= '</td>';
				
				
				$pdf_enc_detail_content .='<td class="text_10" align="left" valign="top">'.$encID.'</td>
										   <td class="text_10" align="left" valign="top">'.$dos.'</td>
										<td class="text_10" align="left"  style="width:'.$colWidth.';overflow:hidden;word-wrap:break-word;" valign="top" width="'.$colWidth.'" valign="top">'.$arrEncCPT[$encID].'</td>';
				
				if($ins_types['pri_ins_id'])$pdf_enc_detail_content .= '<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($priBilled,2).'</td>';
				else
				{
					$pdf_enc_detail_content .= '<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($priBilled,2).'</td>';
					$pdf_enc_detail_content .= '<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($secBilled,2).'</td>';
				}
				$pdf_enc_detail_content .='
				<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($contractFee,2).'</td>
				<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($approvedAmt,2,1).'</td>
				<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($priPaid,2).'</td>
				';	
				$pdf_enc_detail_content .= '<td class="text_10" align="left" valign="top" width="'.$pdfcolWidth.'">'.$insPaidText.'</td>
										<td class="text_10" align="left" valign="top">'.$CLSReports->numberFormat($encBalance,2);
				$pdf_enc_detail_content .= $insDueBy;
				$pdf_enc_detail_content .= '</td>';
				
									
				if(count($arrPat['encounter'])>1){
				//$enc_detail_csv.="\n";
				$enc_detail_content .= '</tr>';
				$pdf_enc_detail_content .= '</tr>';
				}
				$addFirstCol=1;
				$encCount++;
			}
		//	$pat_detail_csv.=$enc_detail_csv;
			$pat_detail_content .= $enc_detail_content;
			$pdf_detail_content .= $pdf_enc_detail_content;
			if(count($arrPat['encounter']) == 1){
				$pat_detail_content .= '</tr>';
				$pdf_detail_content .= '</tr>';
				//$pat_detail_csv.="\n";
			}

			//INSURANCE TOTAL
			$arrInsTotals['insBalance']+= $insBalance;
		}
		//$pat_detail_content .= '<tr><td colspan="8" bgcolor="#FFFFFF">&nbsp;</td></tr>';
		//$pat_detail_content .= '</table>';
		
		//$pdf_detail_content .= '<tr><td colspan="8" bgcolor="#FFFFFF">&nbsp;</td></tr>';
		//$pdf_detail_content .= '</table>';

		$insBalance=$arrInsTotals['insBalance'];
		
		$arrTotalCount['insBilled'] += $insBilled;
		$arrTotalCount['ins_contractFee'] += $ins_contractFee;
		$arrTotalCount['ins_approvedAmt'] += $ins_approvedAmt;		
		$arrTotalCount['priBilled'] += $priBilled;
		$arrTotalCount['contractFee'] += $contractFee;
		$arrTotalCount['approvedAmt'] += $approvedAmt;
		$arrTotalCount['secBilled'] += $secBilled;
		$arrTotalCount['triBilled'] += $triBilled;
		$arrTotalCount['insBalance'] += $insBalance;
		$arrTotalCount['insPaid'] += $insPaid;				
		$strCPT = implode(", ",$arrEncCPT);
		
		//$csv_content.=$loopCount.','.$ins_comp_name.',';
		$page_content .='
						<tr bgcolor="#FFFFFF" id="">
							<td class="text_10b" valign="top">'.$loopCount.'</td>
							<td class="text_10b" valign="top"  style="width:'.$wInsName.'px; word-wrap: break-word;">'.$ins_comp_name.'</td>';
		$pdf_content .='
						<tr bgcolor="#FFFFFF">
							<td class="text_10b" valign="top">'.$loopCount.'</td>
							<td class="text_10b" valign="top" style="width:'.$wInsName.'px; word-wrap: break-word;">'.$ins_comp_name.'</td>';				
		if($cpt_check){
			//$csv_content.=substr($strCPT,0,30);
			$page_content .=' <td class="text_10b" align="left" valign="top">'.substr($strCPT,0,30);	
			if(strlen($strCPT)>30)
			//$csv_content.=".... \n";
			$page_content .='....';
			$page_content .='</td>';			
			$pdf_content .=' <td class="text_10b" align="left" style="width:'.$wCPT.'px; word-wrap: break-word;" width="140" valign="top">'.substr($strCPT,0,30);	
			if(strlen($strCPT)>30)
			$pdf_content .='....';
			$pdf_content .='</td>';			
		}
		
		//$csv_content.=$ptCount.','.$enCount.',';
		//$csv_content.=($ins_types['pri_ins_id'])?$priBilled.',':$priBilled.','.$secBilled;
		//$csv_content.=$contractFee.','.$approvedAmt.','.$priPaid.','.$secPaid.','.$insBalance."\n";
		
		//$CLSReports->numberFormat($insBilled,2).','.$CLSReports->numberFormat($insPaid,2).','.$CLSReports->numberFormat($insBalance,2)."\n";
		
		$page_content .=' <td class="text_10b" align="left" valign="top" colspan="2">'.$ptCount.'</td>
							<td class="text_10b" align="left" valign="top">'.$enCount.'</td>
							<td class="text_10b" style="text-align:center;" valign="top" colspan="'.$insBilledColspan.'">'.$CLSReports->numberFormat($insBilled,2).'</td>
							<td class="text_10b" align="left" valign="top">'.$CLSReports->numberFormat($ins_contractFee,2).'</td>
							<td class="text_10b" align="left" valign="top">'.$CLSReports->numberFormat($ins_approvedAmt,2,1).'</td>
							<td class="text_10b" style="text-align:center;" valign="top" colspan="2">'.$CLSReports->numberFormat($insPaid,2).'</td>
							<td class="text_10b"align="left" valign="top">'.$CLSReports->numberFormat($insBalance,2).'</td>
						</tr>
					';
		$pdf_content .=' <td class="text_10b" align="left" valign="top" colspan="2">'.$ptCount.'</td>
							<td class="text_10b" align="left" valign="top">'.$enCount.'</td>
							<td class="text_10b" style="text-align:center;" valign="top" colspan="'.$insBilledColspan.'">'.$CLSReports->numberFormat($insBilled,2).'</td>
							<td class="text_10b" align="left" valign="top">'.$CLSReports->numberFormat($ins_contractFee,2).'</td>
							<td class="text_10b" align="left" valign="top">'.$CLSReports->numberFormat($ins_approvedAmt,2,1).'</td>
							<td class="text_10b" style="text-align:center;" valign="top"  colspan="2">'.$CLSReports->numberFormat($insPaid,2).'</td>
							<td class="text_10b"align="left" valign="top">'.$CLSReports->numberFormat($insBalance,2).'</td>
						</tr>
					';		
		//$csv_content.=$pat_detail_csv;
		$page_content .= $pat_detail_content;
		$pdf_content .= $pdf_detail_content;
		/*$page_content .='<tr bgcolor="#FFFFFF">
				
							<td class="text_10"  align="right" colspan="'.$colSpan.'" valign="top">'.$pat_detail_content.'</td>
						</tr>
					';	
		$pdf_content .='<tr bgcolor="#FFFFFF"> 
							<td class="text_10"  align="right" colspan="'.$colSpan.'" valign="top">'.$pdf_detail_content.'</td>
						</tr>
					';			*/			
	$loopCount++;
	}

?>
<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr>
  	<td style="text-align:left;" class="rptbx1" width="25%">Insurance Analytics Report (Detail)</td>
    <td style="text-align:left;" class="rptbx2" width="25%">Selected Group: <?php echo $selgroup; ?></td>
   	<td style="text-align:left;" class="rptbx3" width="25%"><?php echo "$search: $Sdate To $Edate"; ?></td>
    <td style="text-align:left;" class="rptbx1" width="25%"><?php echo "Created by: $opInitial on $curDate";?></td>										
	</tr>
	<tr>
 		<td style="text-align:left;" class="rptbx1" width="25%">Selected Insurance Company: <?php echo $selInsurance;?></td>
		<td style="text-align:left;" class="rptbx2" width="25%">Selected CPT Code: <?php echo $selCPT;?></td>
		<td style="text-align:left;" class="rptbx3" width="25%">Selected Facility: <?php echo $selFac;?></td>
		<td style="text-align:left;" class="rptbx1" width="25%">Selected Physician: <?php echo $selPhy;?></td>										
	</tr>
	<tr>
   	<td style="text-align:left;" class="rptbx1" width="25%">Show CPT : <?php echo ($cpt_check)?'Yes':'No';;?></td>
		<td style="text-align:left;" class="rptbx2" width="25%">Billed As : <?php echo ($ins_types['pri_ins_id'])?'Pri':'Sec';?></td>
   	<td style="text-align:left;" class="rptbx3" width="25%">Selected Crediting Phy.: <?php echo $selCrPhy;?></td>
   	<td style="text-align:left;" class="rptbx1" width="25%">&nbsp;</td>
	</tr>
</table>
<?php 

/*$csv="Insurance Analytics Detail Report,Selected Group: $sel_grp, $searchFor: $Start_date To $End_date, Created by $op_name on $curDate \n";
$csv.="Selected Insurance Company: $insurance_name, Selected CPT Code: $cpt_name, Selected Facility: $facility_name, Selected Physician: $sel_phy \n";
$csv.="Show CPT :";$csv.=($cpt_check)?'Yes':'No';
$csv.=",Billed As :";$csv.=($ins_types['pri_ins_id'])?'Pri':'Sec';										
$csv.=",,\n";*/

if($cpt_check){
	$wInsName = 250;
	$wCPT = 250;
	$wPatCnt = 130;
	$wEncCnt = 130;
	$wInsBil = 150;
	$wInsPaid = 180;
	$wInsBal = 135;
	$wAllowedAmt = 100;
}else{
	$wInsName = 250;
	$wPatCnt = 130;
	$wEncCnt = 130;
	$wInsBil = 180;
	$wInsPaid = 200;
	$wInsBal = 150;
	$wAllowedAmt = 100;
}

?>
<table class="rpt_table rpt rpt_table-bordered" id="hello">
	<tr>
    	<td width="20" class="text_b_w">#</td>
    	<td class="text_b_w" width="<?php echo $wInsName;?>">Insurance</td>
        <?php if($cpt_check){?>
        <td class="text_b_w" width="<?php echo $wCPT;?>" align="left" style="width:<?php echo $wCPT;?>; word-wrap:break-word">CPT Code</td>
        <?php }?>
        <td class="text_b_w" width="<?php echo $wPatCnt;?>" align="left" colspan="2">Patient Count</td>
        <td class="text_b_w" width="<?php echo $wEncCnt;?>" align="left">Encounter Count</td>
        <td class="text_b_w" width="<?php echo $wInsBil;?>" align="center" colspan="<?php echo $insBilledColspan;?>">Ins. Billed</td>
        <td class="text_b_w" width="<?php echo $wAllowedAmt;?>" align="left">Contract Fee</td>
        <td class="text_b_w" width="<?php echo $wAllowedAmt;?>" align="left">Allowed Amt</td>
        <td class="text_b_w" width="<?php echo ($wInsPaid);?>" align="center" colspan="2">Ins. Paid</td>
        <td class="text_b_w" width="<?php echo $wInsBal;?>"align="left">Ins. Balance</td>
    </tr>
		<?php echo $page_content; ?>
    <tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
    <tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
    <tr class="text_12b">
			<td>&nbsp;</td>
    	<td class="text_10b" align="right" style="text-align:right">Total</td>
     	<?php if($cpt_check){?><td></td><?php }?>
        <td colspan="2"><?php echo count($arrTotalCount['patient']); ?></td>
        <td ><?php echo count($arrTotalCount['encounter']); ?></td>
        <td colspan="<?php echo $insBilledColspan;?>" align="center" ><?php echo $CLSReports->numberFormat($arrTotalCount['insBilled'],2); ?></td>
        <td ><?php echo $CLSReports->numberFormat($arrTotalCount['ins_contractFee'],2); ?></td>
        <td ><?php echo $CLSReports->numberFormat($arrTotalCount['ins_approvedAmt'],2,1); ?></td>
        <td colspan="2" align="center"><?php echo $CLSReports->numberFormat($arrTotalCount['insPaid'],2); ?></td>
        <td ><?php echo $CLSReports->numberFormat($arrTotalCount['insBalance'],2); ?></td>
    </tr>
    <tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
</table>
<?php
/*$csv.="#, Insurance,";
if($cpt_check){$csv.="CPT Code,"; }
$csv.="Patient Count, Encounter Count, Ins. Billed, Ins. Paid, Ins. Paid, Ins. Balance\n";
$csv.=$csv_content;
if($cpt_check){$csv.=",,,,,,,\n";}else{$csv.=",,,,,,\n";}
if($cpt_check){$csv.="Total,,,".count($arrTotalCount['patient']).",".count($arrTotalCount['encounter']).",".array_sum($arrEnc).",".$arrTotalCount['insPaid'].",".$arrTotalCount['insBalance']."\n";}else{$csv.="Total,,".count($arrTotalCount['patient']).",".count($arrTotalCount['encounter']).",".array_sum($arrEnc).",".$arrTotalCount['insPaid'].",".$arrTotalCount['insBalance']."\n";}
if($cpt_check){$csv.=",,,,,,,\n";}else{$csv.=",,,,,,\n";}*/

$csv_file_data = ob_get_clean();
ob_start();
$showPDF = 1;
?>
<!----- BEGIN PDF FILE DATA ---------------->
<page backtop="16mm" backbottom="5mm">
<page_footer>
    <table style="width: 100%;">
        <tr>
            <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
        </tr>
    </table>
</page_footer>
<page_header>
<!----- END PDF FILE DATA ------------------>
<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
	<tr>
		<td align="left" width="260" class="rpt_headers rptbx1">Insurance Analytics Report (Detail)</td>
		<td align="left" width="260" class="rpt_headers rptbx2 ">Selected Group: <?php echo $selgroup; ?></td>
		<td align="left" width="260" class="rpt_headers rptbx3"><?php echo "$search: $Sdate To $Edate"; ?></td>
		<td align="left" width="260" class="rpt_headers rptbx1"><?php echo "Created by: $opInitial on $curDate";?></td>										
	</tr>
	<tr>
   	<td align="left" class="rpt_headers rptbx1">Selected Insurance Company: <?php echo $selInsurance;?></td>
		<td align="left" class="rpt_headers rptbx2">Selected CPT Code: <?php echo $selCPT;?></td>
		<td align="left" class="rpt_headers rptbx3">Selected Facility: <?php echo $selFac;?></td>
		<td align="left" class="rpt_headers rptbx1">Selected Physician: <?php echo $selPhy;?></td>										
	</tr>
	<tr>
   	<td align="left" class="rpt_headers rptbx1">Show CPT : <?php echo ($cpt_check)?'Yes':'No';;?></td>
		<td align="left" class="rpt_headers rptbx2">Billed As : <?php echo ($ins_types['pri_ins_id'])?'Pri':'Sec';?></td>
		<td align="left" class="rpt_headers rptbx3">Selected Crediting Phy.: <?php echo $selCrPhy;?></td>
		<td align="left" class="rpt_headers rptbx1">&nbsp;</td>
	</tr>
</table>
</page_header>
<?php 
$pdfWidth=1020;
if($cpt_check){
	$wInsName = 120;
	$wCPT = 120;
	$colWidth=round(($pdfWidth-($wInsName + $wCPT))/$colSpan);
}else{
	$wInsName = 120;
	$colWidth=round(($pdfWidth-($wInsName))/$colSpan);
}

?>
<table width="100%" cellpadding="5" cellspacing="1" border="0" class="rpt_padding">
	<tr>
		<td width="20" class="text_b_w" >#</td>
		<td class="text_b_w" width="<?php echo $wInsName;?>" >Insurance</td>
		<?php if($cpt_check){?>
		<td class="text_b_w" width="<?php echo $wCPT;?>" align="left">CPT Code</td>
		<?php }?>
		<td class="text_b_w" width="<?php echo $colWidth;?>" align="left" colspan="2">Pt Cnt</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>" align="left">Enc Cnt</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>" style="text-align:center" colspan="<?php echo $insBilledColspan;?>">Ins. Billed</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>" align="left">Contract Fee</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>" align="left">Allowed Amt</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>" style="text-align:center" colspan="2">Ins. Paid</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>" align="left">Ins. Bal</td>
	</tr>
	<?php echo $pdf_content; ?>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
 	<tr bgcolor="#FFFFFF">
		<td class="text_10b" align="right" colspan="2" style="text-align:right"> Total</td>
		<?php if($cpt_check){?><td class="text_10b"></td><?php }?>
		<td class="text_10b" colspan="2"><?php echo count($arrTotalCount['patient']); ?></td>
    <td class="text_10b"><?php echo count($arrTotalCount['encounter']); ?></td>
    <td class="text_10b" colspan="<?php echo $insBilledColspan;?>" align="center" ><?php echo $CLSReports->numberFormat(array_sum($arrEnc),2); ?></td>
    <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['ins_contractFee'],2); ?></td>
    <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['ins_approvedAmt'],2,1); ?></td>
    <td class="text_10b" colspan="2" align="center"><?php echo $CLSReports->numberFormat($arrTotalCount['insPaid'],2); ?></td>
    <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insBalance'],2); ?></td>
 	</tr>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
</table>
</page>
<?php $pdf_page_content = ob_get_clean();?>
<!----- END PDF FILE DATA ------------------>