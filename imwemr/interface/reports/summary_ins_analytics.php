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
FILE : summary_ins_analytics.php
PURPOSE :  INSURANCE CASES SUMMARY REPORT DATA
ACCESS TYPE : INCLUDED
*/
ob_start();
$pdf_page_content = NULL;
$csv_file_data = NULL;
$loopCount = 1;
$arrTotalCount = array();

$colSpan=($ins_types['pri_ins_id'])?10:11;
$colSpan=($cpt_check)?$colSpan+1:$colSpan;

$pageWidth=100;
$colCountWidth=8;

if($cpt_check){
	$wInsName = 15;$wCPT = 15;
	$w= $pageWidth - ($wInsName+ $wCPT +($colCountWidth*2) +2);
	$colWidth=$w/($colSpan-5);
}else{
	$wInsName = 15;
	$w= $pageWidth - ($wInsName+ ($colCountWidth*2)+2);
	$colWidth=$w/($colSpan-4);
}

foreach($arrResult['insurance'] as $insID=>$patArr){
	$printFile=true;
	$ins_comp_name = $arrAllInsCompanies[$insID];
	$ptCount = count($patArr);
	$enCount = '';
	$insBilled = $insBalance = $insPriBilled=$insPriBalance=$insTriBilled=$insTriBalance=$insSecBilled=$insSecBalance='';
	$insContractFee=$insApprovedAmt='';
	$insPaid =$insPriPaid= $insSecPaid=$insTriPaid='';
	$arrEncCPT = array();
	foreach($patArr as $patID => $arrPat){//pre($encArr);
		$enCount += count($arrPat['encounter']);
		foreach($arrPat['encounter'] as $encID=>$cptArr){
			$arrEncCPT[$encID] = implode(", ",$cptArr['cpt']);
			$insBilled += $cptArr['ins_billed'];
			$insBalance += $cptArr['ins_due'];
			
			$insPriBilled += $cptArr['ins_pri_billed'];
			$insPriBalance += $cptArr['ins_pri_due'];
			
			$insSecBilled += $cptArr['ins_sec_billed'];
			$insSecBalance += $cptArr['ins_sec_due'];
			
			$insTriBilled += $cptArr['ins_tri_billed'];
			$insTriBalance += $cptArr['ins_tri_due'];
			
			$insContractFee+= $cptArr['contract_fee'];
			$insApprovedAmt+= $cptArr['approved_amt'];
			
			$insPriPaid += $arrPayment[$insID][$encID]['ins_pri_paid'];unset($arrPayment[$insID][$encID]['ins_pri_paid']);
			$insSecPaid += $arrPayment[$insID][$encID]['ins_sec_paid'];unset($arrPayment[$insID][$encID]['ins_sec_paid']);
			$insTriPaid += $arrPayment[$insID][$encID]['ins_tri_paid'];unset($arrPayment[$insID][$encID]['ins_tri_paid']);
			
			$insPaid += $arrPayment[$insID][$encID]['ins_paid'];
			
			$arrTotalCount['patient'][$patID]= $patID;
			$arrTotalCount['encounter'][$encID]= $encID;
		}
	}
	//primary
	$arrTotalCount['insPriBilled'] += $insPriBilled;
	//Secondary
	$arrTotalCount['insSecBilled'] += $insSecBilled;
	//tritary
	$arrTotalCount['insTriBilled'] += $insTriBilled;
	//pri+sec+tri
	$arrTotalCount['insBilled'] += $insBilled;
	$arrTotalCount['insBalance'] += $insBalance;
	
	$arrTotalCount['insPriPaid'] += $insPriPaid;
	$arrTotalCount['insSecPaid'] += $insSecPaid;
	$arrTotalCount['insTriPaid'] += $insTriPaid;
	
	$arrTotalCount['insPaid'] += $insPaid;
	$arrTotalCount['insContractFee'] += $insContractFee;	
	$arrTotalCount['insApprovedAmt'] += $insApprovedAmt;

	$secPaidText='';
	if($insSecPaid>0 || $insTriPaid>0){
		$secPaidText= $CLSReports->numberFormat($insSecPaid,2,1).'/ '.$CLSReports->numberFormat($insTriPaid,2,1);
	}
	
	$strCPT = implode(", ",$arrEncCPT);
	$page_content .='
					<tr bgcolor="#FFFFFF">
						<td class="text_10" >'.$loopCount.'</td>
						<td class="text_10" style="word-wrap: break-word;" width="130">'.$ins_comp_name.'</td>';
	if($cpt_check){
		$page_content .=' <td class="text_10"  align="left" style="word-wrap: break-word;" width="80" >'.substr($strCPT,0,100);
		if(strlen($strCPT)>100)
		$page_content .='....';
		$page_content .='</td>';	
	}
	$page_content .=' <td class="text_10" align="left">'.$ptCount.'</td>
						<td class="text_10" align="left">'.$enCount.'</td>';
						
	if($ins_types['pri_ins_id'])
	{
		$page_content .='<td class="text_10" align="left">'.$CLSReports->numberFormat($insPriBilled,2).'</td>';
	}
	else
	{
		$page_content .='<td class="text_10" align="left">'.$CLSReports->numberFormat($insPriBilled,2).'</td>';
		$page_content .='<td class="text_10" align="left">'.$CLSReports->numberFormat($insSecBilled,2).'</td>';
	}
	$page_content .='
					 <td class="text_10" align="left">'.$CLSReports->numberFormat($insContractFee,2).'</td>
					 <td class="text_10" align="left">'.$CLSReports->numberFormat($insApprovedAmt,2).'</td>
					 <td class="text_10" align="left">'.$CLSReports->numberFormat($insPriPaid,2).'</td>
					 <td class="text_10" align="left" width="'.$colWidth.'">'.$secPaidText.'</td>
					 <td class="text_10" align="left">'.$CLSReports->numberFormat($insBalance,2).'</td>';
	$page_content .='</tr>';
$loopCount++;
}

?>

<table class="rpt_table rpt rpt_table-bordered rpt_padding">
	<tr>
  	<td style="text-align:left;" class="rptbx1" width="25%">Insurance Analytics Report (Summary)</td>
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

<table class="rpt_table rpt rpt_table-bordered">
	<tr>
		<td style="width:2%" class="text_b_w">#</td>
		<td class="text_b_w" style="width:<?php echo $wInsName;?>%" style="word-wrap: break-word;">Insurance</td>
			<?php if($cpt_check){?>
			<td class="text_b_w" style="width:<?php echo $wCPT;?>%" align="left" style="word-wrap: break-word;">CPT Code</td>
			<?php }?>
			<td class="text_b_w" style="width:<?php echo $colCountWidth;?>%" align="left">Patient Count</td>
			<td class="text_b_w" style="width:<?php echo $colCountWidth;?>%" align="left">Encounter Count</td>
			<?php if($ins_types['pri_ins_id']){?>
			<td class="text_b_w" style="width:<?php echo $colWidth;?>%" align="left">Pri. Billed</td>
			<?php }else{?>
			<td class="text_b_w" style="width:<?php echo $colWidth;?>%" align="left">Enc Chrgs</td> 
			<td class="text_b_w" style="width:<?php echo $colWidth;?>%" align="left">Sec. Billed</td>
			<?php }?>
			<td class="text_b_w" style="width:<?php echo $colWidth;?>%" align="left">Contract Fee</td>
			<td class="text_b_w" style="width:<?php echo $colWidth;?>%" align="left">Allowed Amt</td>
			<td class="text_b_w" style="width:<?php echo $colWidth;?>%" align="left">Pri. Paid</td>
			<td class="text_b_w" style="width:<?php echo $colWidth;?>%" align="left">Sec. Paid/ Ter. Paid</td>
			<td class="text_b_w" style="width:<?php echo $colWidth;?>%" align="left">Total Ins. Bal.</td>
	</tr>
	<?php echo $page_content; ?>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
	<tr bgcolor="#FFFFFF">
  		<td align="right">&nbsp; </td>
			<td align="right" style="font-weight:bold"> Total</td>
			<?php if($cpt_check){?><td class="text_10b"></td><?php }?>
			<td class="text_10b"><?php echo count($arrTotalCount['patient']); ?></td>
      <td class="text_10b"><?php echo count($arrTotalCount['encounter']); ?></td>
      <?php if($ins_types['pri_ins_id']){?>
      	<td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insPriBilled'],2); ?></td>
      <?php }else{?>
				<td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insPriBilled'],2); ?></td>
				<td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insSecBilled'],2); ?></td>
      <?php }?>
     	<td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insContractFee'],2); ?></td>
      <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insApprovedAmt'],2); ?></td>
      <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insPriPaid'],2); ?></td>
      <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insSecPaid'],2,1).'/ '.$CLSReports->numberFormat($arrTotalCount['insTriPaid'],2,1); ?></td>
    	<td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insBalance'],2);?></td>
	</tr>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
</table>
<?php 
$csv_file_data = ob_get_clean();

$pdfWidth=1020;
$colCountWidth=80;
if($cpt_check){
	$wInsName = 120; $wCPT = 120;
	$w= $pdfWidth - ($wInsName+ $wCPT +($colCountWidth*2) +20);
	$colWidth=round($w/($colSpan-5));
}else{
	$wInsName = 120;
	$w= $pdfWidth - ($wInsName+ ($colCountWidth*2)+20);
	$colWidth=round($w/($colSpan-4));
}
ob_start();
?>
<!----- BEGIN PDF FILE DATA ---------------->
<page backtop="15mm" backbottom="5mm">
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

<table width="100%" cellpadding="5" cellspacing="1" border="0" class="rpt_padding">
	<tr>
		<td width="20" class="text_b_w">#</td>
		<td class="text_b_w" width="<?php echo $wInsName;?>" style="word-wrap: break-word;">Insurance</td>
		<?php if($cpt_check){?>
		<td class="text_b_w" width="<?php echo $wCPT;?>" align="left" style="word-wrap: break-word;">CPT Code</td>
		<?php }?>
		<td class="text_b_w" width="<?php echo $colCountWidth;?>" align="left">Pt Cnt</td>
		<td class="text_b_w" width="<?php echo $colCountWidth;?>" align="left">Enc Cnt</td>
		<?php if($ins_types['pri_ins_id']){?>
		<td class="text_b_w" width="<?php echo $colWidth;?>"  align="left">Pri. Billed</td>
		<?php }else{?>
		<td class="text_b_w" width="<?php echo $colWidth;?>"  align="left">Enc Chrgs</td> 
		<td class="text_b_w" width="<?php echo $colWidth;?>" align="left">Sec. Billed</td>
		<?php }?>
		<td class="text_b_w" width="<?php echo $colWidth;?>"  align="left">Contract Fee</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>"  align="left">Allowed Amt</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>"  align="left">Pri. Paid</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>"  align="left">Sec. Paid/ Ter. Paid</td>
		<td class="text_b_w" width="<?php echo $colWidth;?>"  align="left">Total Ins. Bal.</td>
	</tr>
	<?php echo $page_content; ?>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
	<tr bgcolor="#FFFFFF">
		<td class="text_10b" align="right" colspan="2"> Total</td>
		<?php if($cpt_check){?><td class="text_10b"></td><?php }?>
		<td class="text_10b"><?php echo count($arrTotalCount['patient']); ?></td>
    <td class="text_10b"><?php echo count($arrTotalCount['encounter']); ?></td>
    <?php if($ins_types['pri_ins_id']){?>
    	<td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insPriBilled'],2); ?></td>
		<?php }else{?>
    	<td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insPriBilled'],2); ?></td>
      <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insSecBilled'],2); ?></td>
		<?php }?>
    <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insContractFee'],2); ?></td>
    <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insApprovedAmt'],2); ?></td>
    <td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insPriPaid'],2); ?></td>
    <td class="text_10b" width="<?php echo $colWidth;?>"><?php echo $CLSReports->numberFormat($arrTotalCount['insSecPaid'],2).'/ '.$CLSReports->numberFormat($arrTotalCount['insTriPaid'],2); ?></td>
		<td class="text_10b"><?php echo $CLSReports->numberFormat($arrTotalCount['insBalance'],2); ?></td>
	</tr>
	<tr><td class="total-row" colspan="<?php echo $colSpan;?>"></td></tr>
</table>
</page>
<?php $pdf_page_content = ob_get_clean();
?>
<!----- END PDF FILE DATA ------------------>