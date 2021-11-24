<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
include("discharge_detail_reportpop_export.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Dishcarge Summary Sheet - Detailed Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
</head>
<body>
<?php
if($dischargeSummarySheetCnt)
{
	if($tableDSummery && $dischargePdfConfirmationId) { //FROM export_anesthesia_chart.php
		$content = $tableDSummery; 
		$op = 'p';
		$dischargePdfFileName = $_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/'.$dateFolderPath.'/discharge_summary_'.$dischargePdfPatientName.'_'.$dischargePdfAscId.'.pdf';
		$html2pdf = new HTML2PDF($op,'A4','en');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output($dischargePdfFileName,'F');
		
	}else {
		$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
		$intBytes = fputs($fileOpen,$tableDSummery);
		//die($tableDSummery);
		fclose($fileOpen);
	?>
	
		<form name="printDischargeSheet" action="new_html2pdf/createPdf.php?op=p" method="post"></form>
		<script language="javascript">
			function submitfn()
			{
				document.printDischargeSheet.submit();
			}
			submitfn();
		</script>
<?php
	}
}
else
{
	if(!$dischargePdfConfirmationId) {
	?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	
<?php
	echo '<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><b>No Record Found</b></td>
			</tr>
		  </table>
		';
	}
}
?>
	</body>
</html>