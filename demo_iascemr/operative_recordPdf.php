<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$syncExternalPdf = isset($syncExternalPdf) ? $syncExternalPdf : '';
if($syncExternalPdf!="yes") { //FROM operative_record.php
	session_start();
}
include_once("common/conDb.php");
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include_once("new_html2pdf/html2pdf.class.php");
extract($_GET);
$patient_id = $_REQUEST["patient_id"];
$pConfId = $_REQUEST["pConfId"];
if(!$pConfId) {
	//$pConfId = $_SESSION['pConfId'];
}	
include_once("new_header_print.php");
extract($_GET);
//CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
	$Operative_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$pConfId."'";
	$Operative_patientConfirm_tblRes = imw_query($Operative_patientConfirm_tblQry) or die(imw_error());
	$Operative_patientConfirm_tblRow = imw_fetch_array($Operative_patientConfirm_tblRes);
	if(!$patient_id) {
		$patient_id= $Operative_patientConfirm_tblRow['patientId'];
	}
	if(!$patient_id) {
		$patient_id = $_SESSION['patient_id'];
	}
		
	
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
//VIEW RECORD FROM DATABASE
	$ViewoperativeQry = "select opr.*, date_format(opr.signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, opt.template_name from `operativereport` opr left join operative_template opt ON (opt.template_id = opr.template_id)where opr.confirmation_id='".$pConfId."'";
	$ViewoperativeRes = imw_query($ViewoperativeQry) or die(imw_error()); 
	$ViewoperativeNumRow = imw_num_rows($ViewoperativeRes);
	$ViewoperativeRow = imw_fetch_array($ViewoperativeRes); 
	$oprativeReportId = $ViewoperativeRow["oprativeReportId"];
	$operative_surgeon_sign = $ViewoperativeRow["signature"];
	$operative_data = stripslashes($ViewoperativeRow["reportTemplate"]);
	$opReportFormStatus = $ViewoperativeRow["form_status"];
	$opReportTemplateName = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$ViewoperativeRow["template_name"])));
	
	$signSurgeon1Id = $ViewoperativeRow["signSurgeon1Id"];
	$signSurgeon1FirstName = $ViewoperativeRow["signSurgeon1FirstName"];
	$signSurgeon1MiddleName = $ViewoperativeRow["signSurgeon1MiddleName"];
	$signSurgeon1LastName = $ViewoperativeRow["signSurgeon1LastName"];
	$signSurgeon1Status = $ViewoperativeRow["signSurgeon1Status"];
	$operative_data = str_ireplace( '/surgerycenter/',$_SERVER['DOCUMENT_ROOT'].'/surgerycenter/', $operative_data);
	$operative_data = str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/',"",$operative_data);
//END VIEW RECORD FROM DATABASE

$table='';			
$table.='<page>'.$head_table."\n";	

$table.='<table style="width:700px;" cellpadding="0" cellspacing="0">
			<tr>	
				<td style="width:700px; height:3px;"></td>
			</tr>';
		
		$strip_p = (substr($operative_data,1,5) == 'table') ? '<p>' : '';
		if($operative_data!=""){
			$table.='
			<tr>
				<td style="width:700px; vertical-align:middle;" class="bgcolor bdrbtm cbold ">Operative Report</td>
			</tr>
			<tr>
				<td style="width:698px;">'.strip_tags(nl2br($operative_data),' <img> <strong> <table><tr><td><tbody><br> <span>'.$strip_p).'</td>
			</tr>';
		}
			$table.='
			<tr>
				<td style="width:700px;">';
				if($signSurgeon1LastName!="" || $signSurgeon1FirstName!=''){	
					$table.='
						<b>Surgeon:&nbsp;</b>'.$signSurgeon1LastName.', '.$signSurgeon1FirstName.'
						<br><b>Electronically Signed:&nbsp;</b>'.$ViewoperativeRow['signSurgeon1Status'].'
						<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($ViewoperativeRow['signSurgeon1DateTime']);
				}else{
					$table.='
						<b>Surgeon:&nbsp;</b>______
						<br><b>Electronically Signed:&nbsp;</b>________
						<br><b>Signature Date:&nbsp;</b>________';
				}
			$table.='
				</td>					
			</tr>';
			//START IOL SCAN UPLOAD IMAGE
			$ViewOpRoomRecordQry = "select * from `operatingroomrecords` where  confirmation_id = '".$pConfId."'";
			$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
			$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
			$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
			$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
			$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
			$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
			if($ViewOpRoomRecordNumRow>0) {
				if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){	
					if($iol_ScanUpload!=''){
						$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
						imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom.jpg');
						
						$newSize=' height="100"';
						$priImageSize=array();
						if(file_exists('html2pdfnew/oproom.jpg')) {
							$priImageSize = getimagesize('html2pdfnew/oproom.jpg');
							if($priImageSize[0] > 395 && $priImageSize[1] < 840){
								$newSize = $objManageData->imageResize(680,400,500);						
								$priImageSize[0] = 500;
							}					
							elseif($priImageSize[1] > 840){
								$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
								$priImageSize[1] = 600;
							}
							else{					
								$newSize = $priImageSize[3];
							}							
							if($priImageSize[1] > 800 ){					
								echo '<page></page>';												
							}
						}
						$table.='<tr><td style="width:700px;text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom.jpg" '.$newSize.'></td></tr>';
					}
				
					if($iol_ScanUpload2!=''){
						$bakImgResourceOproom1 = imagecreatefromstring($iol_ScanUpload2);
						imagejpeg($bakImgResourceOproom1,'html2pdfnew/oproom1.jpg');
						
						$priImageSize=array();
						if(file_exists('html2pdfnew/oproom1.jpg')) {
							$priImageSize = getimagesize('html2pdfnew/oproom1.jpg');
							$newSize = 'height="100"';
							if($priImageSize[0] > 395 && $priImageSize[1] < 840){
								$newSize = $objManageData->imageResize(680,400,500);						
								$priImageSize[0] = 500;
							}					
							elseif($priImageSize[1] > 840){
								$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
								$priImageSize[1] = 600;
							}
							else{					
								$newSize = $priImageSize[3];
							}							
							if($priImageSize[1] > 800 ){					
								echo '<page></page>';												
							}
						}
						$table.='<tr><td style="width:700px;padding-top:20px;text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom1.jpg" '.$newSize.'></td></tr>';
					}
				}
			}
$table.='</table></page>';	

$table=str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/','',$table);
$table=str_ireplace('<p style="text-align: left"></p>','',$table);
$table=str_ireplace('<p style="text-align: left">  </p>','',$table);
$table=str_ireplace('<p style="text-align: left"> </p>','',$table);
$table=str_ireplace('<p>&nbsp;</p>','',$table);
$table=str_ireplace('<p> </p>','',$table);
$table=str_ireplace('<p><strong><span style="font-size:12pt"> </span></strong></p>','',$table);
$table=str_ireplace('<p><strong><span style="font-size:12pt">&nbsp;</span></strong></p>','',$table);
$matchesArr = array();
preg_match_all('@font-family(\s*):(.*?)(\s?)("|;|$)@i', $table, $matchesArr);
if (count($matchesArr[2])>0) {
	foreach($matchesArr[0] as $matchesKey=> $matches ) {
		$matchesVal=str_ireplace('"','',$matches);
		$table=str_ireplace($matchesVal,'',$table);	
	}
}
//$table = preg_replace('/font-family.+?"/', "\"", $table);
if($syncExternalPdf=="yes") { //FROM operative_record.php
	$opExternalContent = $table;
	$updirOp = $_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles';
	$obExFolder = "inte_external";
	$obExPath = $updirOp."/".$obExFolder;
	if(!is_dir($obExPath)){		
		mkdir($obExPath,0777);
	}
	$opExternalContent=str_ireplace('../new_html2pdf/','new_html2pdf/',$opExternalContent);
	$opExternalContent=str_ireplace('../html2pdfnew/','html2pdfnew/',$opExternalContent);
	$opExternalContent=str_ireplace('../html2pdf/','html2pdf/',$opExternalContent);
	$opPdfFileName = 'opreport_'.$patient_id.'_'.$pConfId.'_'.$Operative_patientConfirm_tblRow["ascId"].'.pdf';
	$savePdfFilePath = $obExPath.'/'.$opPdfFileName;
	$op = 'P';
	try {
		$html2pdf = new Html2Pdf($op,'A4','en');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->writeHTML($opExternalContent, isset($_GET['vuehtml']));
		$html2pdf->output($savePdfFilePath,'F');
		$saveOpPdfDbPath = "pdfFiles/".$obExFolder."/".$opPdfFileName;
		$updtOpRpPdfQry = "UPDATE operativereport SET opreport_pdf_file_path = '".$saveOpPdfDbPath."', opreport_pdf_save_date_time = '".date("Y-m-d H:i:s")."', opreport_inte_sync_status = '0' WHERE confirmation_id = '".$pConfId."'";
		$updtOpRpPdfRes = imw_query($updtOpRpPdfQry) or die($updtOpRpPdfQry.imw_error());		
	} catch (Html2PdfException $e) {
		$html2pdf->clean();
		$formatter = new ExceptionFormatter($e);
		echo "Error while creating a PDF ".$formatter->getHtmlMessage();
	}
}else {
	$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
	$filePut  = fputs($fileOpen,$table);
	fclose($fileOpen);
	?>
	<body  onClick="document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.main_frmInner.hideSliders();">    	
	<form name="printAllOperative"  action="new_html2pdf/createPdf.php?op=p" method="post">
	</form>		
	
	<script language="javascript">
		function submitfn()
		{
			document.printAllOperative.submit();
		}
	</script>
	
	<script type="text/javascript">
		submitfn();
	</script>
	</body>
	</html>
<?php
}
?>