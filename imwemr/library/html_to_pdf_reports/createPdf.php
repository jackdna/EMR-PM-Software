<?php

/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF, utilise fpdf de Olivier PLATHEY 
 * Distribué sous la licence GPL. 
 *
 * @author		Laurent MINGUET <webmaster@spipu.net>
 * 
 * isset($_GET['vuehtml']) n'est pas obligatoire
 * il permet juste d'afficher le résultat au format HTML
 * si le paramètre 'vuehtml' est passé en paramètre _GET
 */
 	// récupération du contenu HTML
 	ob_start();
 	set_time_limit(0);
	ini_set('memory_limit', '9072M'); 

	$_GET['file_name']=$_REQUEST['file_location'];	
	$file_name = $_GET['file_name'];
	
	if(empty($file_name) == false){
		//include(dirname(__FILE__).'/'.$file_name.'.html');
		include($file_name);
	}
	else{
		include(dirname(__FILE__).'/pdffile.html');
	}
	$pdf_name_gt = 'newPdf.pdf';
	if(trim($_GET['pdf_name_gt'])!='') {
		$pdf_name_gt = $_GET['pdf_name_gt'].'.pdf';	
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
	<BR/>','<1', '<2', '<3', '<4','<5', '<6', '<7', '<8','<9','<0', '&nbsp;<1', '&nbsp;<2', '&nbsp;<3', '&nbsp;<4','&nbsp;<5', '&nbsp;<6', '&nbsp;<7', '&nbsp;<8','&nbsp;<9','&nbsp;<0');
	$repLacevals = array('<br/>','<br />','','<br/>','<br/>','<br/>','<br/>','&lt;1', '&lt;2', '&lt;3', '&lt;4','&lt;5', '&lt;6', '&lt;7', '&lt;8','&lt;9','&lt;0','&nbsp;&lt;1', '&nbsp;&lt;2', '&nbsp;&lt;3', '&nbsp;&lt;4','&nbsp;&lt;5', '&nbsp;&lt;6', '&nbsp;&lt;7', '&nbsp;&lt;8','&nbsp;&lt;9','&nbsp;&lt;0');
	$content= str_replace($placeholders, $repLacevals, $content);	
	// conversion HTML => PDF
	require_once(dirname(__FILE__).'/html2pdf.class.php');
	
	//$op = $_REQUEST['op'];
	$op = (!empty($_REQUEST['op'])) ? $_REQUEST['op'] : "p";
	/*if($_REQUEST['op'] == 'l' && empty($_REQUEST['op']) == false){
		$op = 'l';
	}
	if(empty($op)){
		$op = 'p';
	}*/
	$saveOption="F";
	$saveOption = isset($_REQUEST['saveOption']) ? $_REQUEST['saveOption'] : '';
	if(empty($saveOption)){
		$saveOption = false;
	}	
	
	$onePage = "";
	if(isset($_REQUEST['onePage']) == true && empty($_REQUEST['onePage']) == false){
		$onePage = $_REQUEST['onePage'];	
		if($_REQUEST['onePage'] == "true" || $_REQUEST['onePage'] == "1")
		$onePage = true;
	}
	$html2pdf = new HTML2PDF($op,'A4','fr');
	if($onePage=="false") {$html2pdf->setTestTdInOnePage(false); }
	$html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
	$html2pdf->Output($pdf_name_gt,$saveOption);

?>