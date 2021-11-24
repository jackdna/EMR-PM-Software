<?php
	$filePath=write_html(html_entity_decode($divData));
	$email_success='0';
	$pdfOp = 'P';
	$getPCIP=$_SESSION["authId"];			
	$getIP=str_ireplace(".","_",$getPCIP);
	$pdfPath=data_path()."UserId_".$_SESSION['authUserID']."/tmp/"; 
	$setNameFaxPDF="email_statement".$getIP;//FAX PDF NAME
	$filename= $pdfPath.$setNameFaxPDF.'.pdf';
	//@unlink($filename);//delete pdf file if already exist
	/*$dir = explode('/',$_SERVER['HTTP_REFERER']);
	$httpPro = $dir[0];
	$httpHost = $dir[2];
	$httpfolder = $dir[3];
	$ip = $_SERVER['REMOTE_ADDR'];*/

	$httpPro = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https:' : 'http:';

	$myHTTPAddress = $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/library/html_to_pdf/createPdf.php';
	$data1 = "";
	$curNew = curl_init();
	$urlPdfFile = $myHTTPAddress."?pdf_name=".$filename."&setIgnoreAuth=true&op=$pdfOp&saveOption=F&file_location=$filePath";

	curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
	$data1 = curl_exec($curNew);
	curl_close($curNew); 
	//===============CREATED PDF ADDRESS==================
	
	$filetype= 'PDF';
	if(!($fp = fopen($filename, "r"))){
		echo "Error opening PDF file";
		exit;
	}
	if($email_body==""){
		$email_body="Please find enclosed imwemr Account Statement.";
	}
	//convert HTML into a basic plain-text alternative body
	$mail->msgHTML($groupEmailConfig['header']."<br/>".$email_body."<br/>".$groupEmailConfig['footer']);
	//Replace the plain text body with one created manually
	$mail->AltBody = '';
	//Attach an image file
	if($filename)$mail->addAttachment($filename);

	$mail->addAddress($pt_email,$respartyName);
	
	if (!$mail->send()) {
		$failed++;
		$error.='<br>Mailer error: ' . $mail->ErrorInfo;
		$email_success='0';
	} else {
		$totalMailsSent++;
		$email_success='1';
	}
	// Clear all addresses and attachments for next loop
	$mail->clearAddresses();
	$mail->clearAttachments();
	
	unset($statementData,$statements,$statementHeader);
	$totalMails++;
?>