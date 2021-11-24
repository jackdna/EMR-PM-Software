<?php
 	ob_start();
	require_once(dirname('__FILE__')."/../../config/config.php"); 
 	set_time_limit(0);
	ini_set('memory_limit', '512M'); 
	
	use Spipu\Html2Pdf\Html2Pdf;
	use Spipu\Html2Pdf\Exception\Html2PdfException;
	use Spipu\Html2Pdf\Exception\ExceptionFormatter;

	$file_name = $_GET['file_name'];
	if(empty($file_name) == false){
		include(dirname(__FILE__).'/'.$file_name.'.html');
	}
	else{
		include(dirname(__FILE__).'/pdffile.html');
	}
	$content = ob_get_clean();
	$placeholders = array('</br>','</ br>','<br/>
	<br />',
	'<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />', 
	'<BR />
	<BR />
	<BR />
	<BR />
	<BR />', 
	'<br/>
	<br/>
	<br/>
	<br/>', 
	'<BR/>
	<BR/>
	<BR/>
	<BR/>
	<BR/>','<1', '<2', '<3', '<4','<5', '<6', '<7', '<8','<9','<0', '&nbsp;<1', '&nbsp;<2', '&nbsp;<3', '&nbsp;<4','&nbsp;<5', '&nbsp;<6', '&nbsp;<7', '&nbsp;<8','&nbsp;<9','&nbsp;<0','&trade;');
	$repLacevals = array('<br/>','<br />','','<br/>','<br/>','<br/>','<br/>','&lt;1', '&lt;2', '&lt;3', '&lt;4','&lt;5', '&lt;6', '&lt;7', '&lt;8','&lt;9','&lt;0','&nbsp;&lt;1', '&nbsp;&lt;2', '&nbsp;&lt;3', '&nbsp;&lt;4','&nbsp;&lt;5', '&nbsp;&lt;6', '&nbsp;&lt;7', '&nbsp;&lt;8','&nbsp;&lt;9','&nbsp;&lt;0','™');
	$content= str_replace($placeholders, $repLacevals, $content);
	
	
	$pageSize = "A4";
	$margins = array(5, 5, 5, 8);
	if(isset($_REQUEST['mod']) && $_REQUEST['mod']=="stock_print"){
		$pageSize = "dymo-303334";
		$margins = array(0, 0, 0, 0);
	}
	
	//$op = $_REQUEST['op'];
	$op = (!empty($_REQUEST['op'])) ? $_REQUEST['op'] : "p";
	/*if($_REQUEST['op'] == 'l' && empty($_REQUEST['op']) == false){
		$op = 'l';
	}
	if(empty($op)){
		$op = 'p';
	}*/
	$onePage = "";
	if(isset($_REQUEST['onePage']) == true && empty($_REQUEST['onePage']) == false){
		$onePage = $_REQUEST['onePage'];	
		if($_REQUEST['onePage'] == "true" || $_REQUEST['onePage'] == "1")
		$onePage = true;
	}
	try {
        $op = strtoupper($op);
        $html2pdf = new Html2Pdf($op,$pageSize,'en', $margins);
        if($onePage=="false") {$html2pdf->setTestTdInOnePage(false); }
		$html2pdf->writeHTML($content, isset($_REQUEST['vuehtml']));
        //$html2pdf->createIndex('Sommaire', 30, 12, false, true, 2, null, '10mm');
        ob_end_clean(); // fix: TCPDF ERROR - Some data has already been output
		$newFileName=$html2pdf->output('newPdf.pdf');
	} catch (Html2PdfException $e) {
		$html2pdf->clean();
		//echo "Error while creating a PDF";
		$formatter = new ExceptionFormatter($e);
		echo $formatter->getHtmlMessage();
	}
?>