<?php
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 *
 * Convertisseur HTML => PDF, utilise fpdf de Olivier PLATHEY
 * Distribué sous la licence GPL.
 *
 * @author		Laurent MINGUET <webmaster@spipu.net>
 *
 * isset($_REQUEST['vuehtml']) n'est pas obligatoire
 * il permet juste d'afficher le résultat au format HTML
 * si le paramètre 'vuehtml' est passé en paramètre _REQUEST
 */
 	// récupération du contenu HTML

	/**
	 * HTML TO PDF LIBRARY FILES CALLED
	 */

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

function create_pdf($htmlPath="",$pdf_name_gt="",$op="" ){
	$ret="";
	ob_start();
 	set_time_limit(0);
	ini_set('memory_limit', '512M');

	//------HTML FILE LOCATION-------
	if(!isset($htmlPath) || empty($htmlPath))
	{
		$htmlPath = dirname(__FILE__).'/pdffile.html';
	}

	include($htmlPath);

	if(!isset($pdf_name_gt) || empty($pdf_name_gt)){
		$pdf_name_gt = dirname(__FILE__).'/new_pdf.pdf'; //PDF DEFAULT NAME
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
	<BR/>','<1', '<2', '<3', '<4','<5', '<6', '<7', '<8','<9','<0', '&nbsp;<1', '&nbsp;<2', '&nbsp;<3', '&nbsp;<4','&nbsp;<5', '&nbsp;<6', '&nbsp;<7', '&nbsp;<8','&nbsp;<9','&nbsp;<0','Â','<meta charset="utf-8" />','<br<br>/>');

	$repLacevals = array('<br/>','<br />','','<br/>','<br/>','<br/>','<br/>','&lt;1', '&lt;2', '&lt;3', '&lt;4','&lt;5', '&lt;6', '&lt;7', '&lt;8','&lt;9','&lt;0','&nbsp;&lt;1', '&nbsp;&lt;2', '&nbsp;&lt;3', '&nbsp;&lt;4','&nbsp;&lt;5', '&nbsp;&lt;6', '&nbsp;&lt;7', '&nbsp;&lt;8','&nbsp;&lt;9','&nbsp;&lt;0','','','<br/>');

	$content= str_replace($placeholders, $repLacevals, $content);

	//------PAGE ORIENTATION SETTING------
	$op = (!empty($op)) ? $op : "p";

	try
	{
        $op = strtoupper($op);
        $html2pdf = new Html2Pdf($op,'A4','en');

				$html2pdf->writeHTML($content, isset($_REQUEST['vuehtml']));

				ob_end_clean(); // FIX: TCPDF ERROR - SOME DATA HAS BEEN ALREADY OUTPUT
				$saveOption = 'F';

				$html2pdf->output($pdf_name_gt,$saveOption);

				//delete html files
				if(file_exists($htmlPath)){
						unlink($htmlPath);
				}
	}
	catch(Html2PdfException $e)
	{
		$html2pdf->clean();
		//echo "Error while creating a PDF";
		$formatter = new ExceptionFormatter($e);
		$ret = "Error: ".$formatter->getHtmlMessage();
	}
	return $ret;
}

?>
