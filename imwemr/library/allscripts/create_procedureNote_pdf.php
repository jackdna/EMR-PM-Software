<?php

function crate_procedure_note_pdf($patientId, $cnId)
{
	$patient_id = $patientId;
	$form_id = $cnId;

	$oProcedures = new Procedures($patient_id, $form_id);
	$pdf_data = $oProcedures->print_procedure_note();
	$pdf_data = trim($pdf_data);

	$fileDir = '';

	if( !empty($pdf_data) )
	{
		$oPrinter = new Printer($patient_id, $form_id);
		$html_path = $oPrinter->print_page($pdf_data, "Print Procedures","","",1,"print_procedures");
		unset($pdf_data);

		$pathInfo = pathinfo($html_path);

		$fileDir = $pathInfo['dirname'].'/patient_'.$patient_id.'_'.date('Y_m_d_H_i_s').'.pdf';

		$params = array(
			'page'=>'1.3', 
			'op'=>'P', 
			'font_size'=>'7.5', 
			'saveOption'=>'F', 
			'name'=>$html_path, 
			'file_location'=>$html_path, 
			'pdf_name'=>$fileDir
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php?setIgnoreAuth=true');
		curl_setopt($ch, CURLOPT_POST, true);	/*Reset HTTP method to GET*/
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*Return the response*/
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP); /*Set protocol to HTTP if default changed*/
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false); /*Include header in Output/Response*/
		$data = curl_exec($ch); /*$data will hold data returned from FramesData API*/
		/*Close curl session/connection*/
		curl_close($ch);
	}

	return $fileDir;
}
