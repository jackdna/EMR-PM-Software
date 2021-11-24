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
	if(isset($_REQUEST['setIgnoreAuth']) && $_REQUEST['setIgnoreAuth'] != '') { $ignoreAuth = true; }
	
	//------GLOBAL FILE INCLUSION-------
	include_once('../../config/globals.php');

	/**
	 * HTML TO PDF LIBRARY FILES CALLED
	 */
	use Spipu\Html2Pdf\Html2Pdf;
	use Spipu\Html2Pdf\Exception\Html2PdfException;
	use Spipu\Html2Pdf\Exception\ExceptionFormatter;
	
	ob_start();
 	set_time_limit(0);
	ini_set('memory_limit', '512M'); 
	
	//------HTML FILE LOCATION-------
	$file_location = $_REQUEST['file_location'];
	
	if(empty($file_location) == false)
	{
		//--TO STOP TRAVERSAL PATH---
		$htmlPath = $file_location . (stristr($file_location,'.html') === FALSE ? '.html' : '');
		
		if(!file_exists($htmlPath))
		{
			//------WRITE AND SAVE HTML FILE-------
			include_once(dirname(__FILE__).'/../classes/SaveFile.php');
			
			$oSaveFile = new SaveFile($_SESSION["authId"],1);
			$htmlPath = $oSaveFile->get_print_file_path($htmlPath); // HTML FILE LOCATION
			
			if(!file_exists($htmlPath)){exit("Error: File not found!");}	
		}
		else
		{
			//------BELOW WORK ADDED CONDITIONALLY DUE TO ISSUE FOUND IN CASE OF TESTS PRINTING
			$htmlPath = realpath($htmlPath);
			
			//------TO CHECK - IS EXTENSION = php_fileinfo.dll ENABLE OR NOT
			if( !check_phpExt() ) return false;
			
			if(strtolower(mime_content_type($htmlPath))=='text/html' || strtolower(mime_content_type($htmlPath))=='text/x-asm')
			{
				//---DO NOTHING--------
			}else
			{
				die("Only valid html content file is allowed");
			}
		}
	}
	else
	{
		$htmlPath = 'pdffile.html';
	}
	
	include($htmlPath);
	
	$pdf_name_gt = 'new_pdf.pdf'; //PDF DEFAULT NAME
	
	if(trim($_REQUEST['pdf_name'])!='')
	{
		$pdf_name_gt = $_REQUEST['pdf_name']. (stristr($_REQUEST['pdf_name'],'.pdf') === FALSE ? '.pdf' : '');	
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
	
	/*
	$content= utf8_decode($content);
	conversion HTML => PDF
	$op = $_REQUEST['op']; 
	if($_REQUEST['op'] == 'l' && empty($_REQUEST['op']) == false)
	{
		$op = 'l';
	}
	if(empty($op))
	{
		$op = 'p';
	}
	*/
	
	//------PAGE ORIENTATION SETTING------
	$op = (!empty($_REQUEST['op'])) ? $_REQUEST['op'] : "p";
	
	//CONDITION ADDED TO CORRECT THE PARAMETER FOR SEND FAX CASES TO SAVE PDF FILES AT PARTICULAR LOCATION
	if(!empty($_REQUEST['saveOption']) && $_REQUEST['saveOption'] == 'fax'){ $_REQUEST['saveOption'] = 'F';}
	
    $image_from = isset($_REQUEST['image_from']) ? $_REQUEST['image_from'] : false; //FOR TEST PRINTING
	$saveOption = isset($_REQUEST['saveOption']) ? $_REQUEST['saveOption'] : 'I';	
	
	if(isset($_REQUEST['testIds']) && $_REQUEST['testIds']!="")
	{
        $saveOption=false;
    }
	if(isset($_REQUEST['mergePDF']) && $_REQUEST['mergePDF']!="" && ($saveOption ==false || $image_from))
	{
		$saveOption ='F';
		
		$getPCIP=$_SESSION["authId"];			
		$getIP=str_ireplace(".","_",$getPCIP);
		$pdf_name_gt=data_path()."UserId_".$_SESSION['authUserID']."/tmp/".$pdf_name_gt; 
	}
	
	$onePage = "false";
	if(isset($_REQUEST['onePage']) == true && empty($_REQUEST['onePage']) === false)
	{
		if($_REQUEST['onePage'] == "true" || $_REQUEST['onePage'] == "1")
		{
			$onePage = "true";
		}
	}
	try
	{
        $op = strtoupper($op);
        $html2pdf = new Html2Pdf($op,'A4','en');
        
		if($onePage=="false") {$html2pdf->setTestTdInOnePage(false); }
        
		$html2pdf->writeHTML($content, isset($_REQUEST['vuehtml']));
        
		ob_end_clean(); // FIX: TCPDF ERROR - SOME DATA HAS BEEN ALREADY OUTPUT
		
		$newFileName=$html2pdf->output($pdf_name_gt,$saveOption);
		
		/*OLD CODE FOR REFERENCE PURPOSES
		$op = strtoupper($op);
        $html2pdf = new Html2Pdf($op,'A4','en');
        if($onePage=="false") {$html2pdf->setTestTdInOnePage(false); }
        //$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->writeHTML($content, isset($_REQUEST['vuehtml']));
        //$html2pdf->createIndex('Sommaire', 30, 12, false, true, 2, null, '10mm');
        ob_end_clean(); // FIX: TCPDF ERROR - SOME DATA HAS BEEN ALREADY OUTPUT
		$newFileName=$html2pdf->output($pdf_name_gt,$saveOption);
		*/
	}
	catch(Html2PdfException $e)
	{
		$html2pdf->clean();
		//echo "Error while creating a PDF";
		$formatter = new ExceptionFormatter($e);
		echo $formatter->getHtmlMessage();
	}

	if(isset($_REQUEST['mergePDF']) && $_REQUEST['mergePDF']!="")
	{
        if($image_from) 
		{
            echo "<script>location.href='".$GLOBALS['webroot']."/interface/patient_info/complete_pt_rec/merge_pdf.php?testIds=".$_REQUEST['testIds']."&patient_pdf=".$pdf_name_gt."&ptmailname=".$_REQUEST['ptmailname']."&ptmailid=".$_REQUEST['ptmailid']."&image_from=".$image_from."'</script>";
        }
		else
		{
			echo "<script>location.href='".$GLOBALS['webroot']."/interface/patient_info/complete_pt_rec/merge_pdf.php?testIds=".$_REQUEST['testIds']."&patient_pdf=".$pdf_name_gt."&ptmailname=".$_REQUEST['ptmailname']."&ptmailid=".$_REQUEST['ptmailid']."&iportal=".$_REQUEST['iportal']."&txtFaxRecipent=".$_REQUEST['txtFaxRecipent']."&txtFaxNo=".$_REQUEST['txtFaxNo']."&sendFaxFromCPR=".$_REQUEST['sendFaxFromCPR']."'</script>";
		}
	}

	/**
	 * PASSWORD PROTECT FUNCTION
	 */
	if(isset($_REQUEST["encPassword"]) && $_REQUEST["encPassword"] != "")
	{
		if(!isset($_REQUEST['mode']))
		{
			$_REQUEST['mode'] = 'New';
		}
		
		$name = $pdf_name_gt;
		
		function pdfEncrypt ($origFile, $password, $destFile)
		{ 
			/**
			 * PDF ENCRYPTION CODE STARTS HERE
			 */
			
			include(dirname(__FILE__).'/fpdi/FPDI_Protection.php');
			
			$pdf =new FPDI_Protection(); 
			$pdf->FPDF('P', 'in'); 
			
			//---CALCULATE THE NUMBER OF PAGES FROM ORIGINAL DOCUMENT
			$pagecount = $pdf->setSourceFile($origFile); 
			
			//---COPY ALL PAGES FROM OLD UNPROTECTED PDF IN NEW ONE
			for ($loop = 1; $loop <= $pagecount; $loop++) { 
				$tplidx = $pdf->importPage($loop); 
				$pdf->addPage(); 
				$pdf->useTemplate($tplidx); 
			} 
	 
			/**
			 * PROTECT THE NEW PDF FILE AND ALLOW NO PRINTING, COPY etc.
			 * LEAVE ONLY READING ALLOWED
			 *
			 */
			$pdf->SetProtection(array('print'), $password); 
			$pdf->Output($destFile, 'D'); 
			
			return $destFile; 
		}
		
		$password =$_REQUEST["encPassword"];
		
		if($_REQUEST["mode"]=='Old')
		{
			$file_location = explode('/',$file_location);
			
			array_pop($file_location);
			
			$file_location = join("/",$file_location);
			
			$origFile = $file_location.$name; 
			
			$destFile =$name; 
		}
		if($_REQUEST["mode"]=='New')
		{
			$origFile=$name; 
			$destFile=$name; 
		}
		
		pdfEncrypt($origFile, $password, $destFile );
			
			
		echo "
			<html>
				<head>
					<title>Please wait... Your download will start shortly.</title>
				
				</head>
				<body style='font-family:\"verdana\"; font-size:12px; color:#000000; font-weight:bold;'>
				<center>
					You will be prompted for download file. Save or open file as per your need. After download, please close this window by clicking close button below:
					<br>
					<input type='button' name='buttonclose' onClick='javaScript:window.close();' value='Close'>
				</center>
				</body>
				<script>
						location.href=".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?mode=New&name=".$origFile."&encPassword=".$password."';
				</script>
			</html>
			";	 
		/*	$password = $_POST["encPassword"];   
			$origFile = $name; 
			$destFile =$name;; 
			pdfEncrypt($origFile, $password, $destFile ); 
		*/
	}

	?>
<script>
	//window.close();
</script>
<?php
/*
Saving Physical copy of PDF 
	$save_location_extract = explode('/',$htmlPath);
	if(strpos($htmlPath, '.html') !== false) {
		array_pop($save_location_extract);
	}
	$save_location = implode('/',$save_location_extract).'/';
	$html2pdf->Output(''.$save_location.$pdf_name_gt.'.pdf', 'F');
*/
?>