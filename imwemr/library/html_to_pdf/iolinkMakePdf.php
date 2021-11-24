<?php 
$ignoreAuth = true;

include_once("../../config/globals.php");
include_once("../classes/admin/documents/encoding.php");
$library_path = $GLOBALS['webroot'].'/library';
$savePdfFilePath = data_path().'iOLink/';

//include_once("html2pdf.class.php");
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

$onePage=($_REQUEST['onePage'])?$_REQUEST['onePage']:false;

$flName = "pdffile.html";
if($_REQUEST['name']) {
	$flName = $_REQUEST['name'].'.html';	
}
$fp = fopen($savePdfFilePath.$flName,"r");
$strContent = fread($fp, filesize($savePdfFilePath.$flName));
$strContent = Encoding::fixUTF8($strContent);
fclose($fp);

$savePdfFileName = $savePdfFilePath.'pdfFile.pdf';
if(trim($_REQUEST['pdf_name'])!='') {
	$savePdfFileName = $_REQUEST['pdf_name']. (stristr($_REQUEST['pdf_name'],'.pdf') === FALSE ? '.pdf' : '');	
}

try {
	$op = ($_REQUEST['op'])?$_REQUEST['op']:'p';
	$op = strtoupper($op);
	$html2pdf = new Html2Pdf($op,'A4','en');
	$html2pdf->setTestTdInOnePage($onePage);
	$html2pdf->writeHTML(utf8_decode(html_entity_decode($strContent)), isset($_GET['vuehtml']));
	$newFileName=$html2pdf->output($savePdfFileName,'F');
} catch (Html2PdfException $e) {
	$html2pdf->clean();
	$formatter = new ExceptionFormatter($e);
	echo $formatter->getHtmlMessage();
}

if (isset($_REQUEST["copyPathIolink"]) && !empty($_REQUEST["copyPathIolink"])){	
	$dest_path = urldecode($_REQUEST["copyPathIolink"]);		
	$dest_path = str_replace("&amp;","",$dest_path);	
	file_put_contents($dest_path,file_get_contents($savePdfFileName));	
}

if($_REQUEST['images']){
	$imagesArr = explode(',',$_REQUEST['images']);
	for($i=0;$i<count($imagesArr);$i++){
		@unlink($imagesArr[$i]); 
	}
}
?>