<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF, utilise fpdf de Olivier PLATHEY 
 * Distribu� sous la licence GPL. 
 *
 * @author		Laurent MINGUET <webmaster@spipu.net>
 * 
 * isset($_GET['vuehtml']) n'est pas obligatoire
 * il permet juste d'afficher le r�sultat au format HTML
 * si le param�tre 'vuehtml' est pass� en param�tre _GET
 */
 	// r�cup�ration du contenu HTML
	
	set_time_limit(0);
	$original_memory = (int) ini_get('memory_limit');
	
	if($original_memory < 1024){
		ini_set('memory_limit', '1024M'); 		
	}
	include_once("../common/conDb.php");
	
	use Spipu\Html2Pdf\Html2Pdf;
	use Spipu\Html2Pdf\Exception\Html2PdfException;
	use Spipu\Html2Pdf\Exception\ExceptionFormatter;
	
	ob_start();
	$htmlFileName = isset($_REQUEST['htmlFileName']) ? $_REQUEST['htmlFileName'] : 'pdffile';
	$htmlFileName = str_ireplace('.html','',$htmlFileName);
	include(dirname(__FILE__).'/'.$htmlFileName.'.html');
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
	<BR/>','<1', '<2', '<3', '<4','<5', '<6', '<7', '<8','<9','<0', '&nbsp;<1', '&nbsp;<2', '&nbsp;<3', '&nbsp;<4','&nbsp;<5', '&nbsp;<6', '&nbsp;<7', '&nbsp;<8','&nbsp;<9','&nbsp;<0','Â','<meta charset="utf-8" />','&rsquo;');
	
	$repLacevals = array('<br/>','<br />','','<br/>','<br/>','<br/>','<br/>','&lt;1', '&lt;2', '&lt;3', '&lt;4','&lt;5', '&lt;6', '&lt;7', '&lt;8','&lt;9','&lt;0','&nbsp;&lt;1', '&nbsp;&lt;2', '&nbsp;&lt;3', '&nbsp;&lt;4','&nbsp;&lt;5', '&nbsp;&lt;6', '&nbsp;&lt;7', '&nbsp;&lt;8','&nbsp;&lt;9','&nbsp;&lt;0','','','&#39;');
	
	$content= str_replace($placeholders, $repLacevals, $content);	
	
	// conversion HTML => PDF
	//require_once(dirname(__FILE__).'/html2pdf.class.php');
	$op = $_REQUEST['op'];
	if(empty($op)){
		$op = 'p';
	}
	$onePage = isset($_REQUEST['onePage']) ? $_REQUEST['onePage'] : '';	
	$saveOption = isset($_REQUEST['saveOption']) ? $_REQUEST['saveOption'] : 'I';	
	/*
	$html2pdf = new HTML2PDF($op,'A4','en');
	if($onePage=="false") {$html2pdf->setTestTdInOnePage(false); }
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
	*/
	try {
        $op = strtoupper($op);
        $html2pdf = new Html2Pdf($op,'A4','en');
        //if($onePage=="false") {$html2pdf->setTestTdInOnePage(false); }
        //$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->writeHTML($content, isset($_REQUEST['vuehtml']));
        //$html2pdf->createIndex('Sommaire', 30, 12, false, true, 2, null, '10mm');
        ob_end_clean(); // fix: TCPDF ERROR - Some data has already been output
		//$newFileName=$html2pdf->output('newPdf.pdf',$saveOption);

		$html2pdf->output('newPdf.pdf',$saveOption);

	} catch (Html2PdfException $e) {
		$html2pdf->clean();
		//echo "Error while creating a PDF";
		$formatter = new ExceptionFormatter($e);
		echo $formatter->getHtmlMessage();
	}
	
	
?>