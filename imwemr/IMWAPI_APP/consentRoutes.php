<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 * index.php
 * Access Type: InClude
 * Purpose: Routes for Consent
*/

$patientId = 0;
	
/*Validate Patient ID*/
$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
});

/* API To Get Consent Forms */
$this->respond(array('POST','GET'), '/getConsentPackage', function($request, $response, $service, $app) use(&$patientId){
	
	$b64Doc = '';
	$consentId = (int)$request->__get('consentId');
	$packageId = (int)$request->__get('packageId');
	$consent_obj = new IMW\Consent($app->dbh,1);
	// Call To function which will get Raw Html Data to print //
	$contents = $consent_obj->getConsentForm($patientId,$consentId,$packageId);
	$contents = htmlspecialchars_decode($contents);
	
	$contents = str_ireplace('</br>','<br />',$contents);
	$contents = stripslashes(html_entity_decode($contents,true));
	$contents = str_ireplace('�','',$contents);
	echo $contents; die();
	
	//$b64Doc = base64_encode($contents);
	//$data = fopen('test_2.html','w');
	//fwrite($data, base64_decode($b64Doc));
	//$response = array('getConsentPackage' => $b64Doc);
	//return json_encode($response);
	
});
/* API To Save Consent Froms */
$this->respond(array('POST','GET'), '/saveConsentPackage', function($request, $response, $service, $app) use(&$patientId){
	
	$b64Doc = '';
	$consentId = (int)$request->__get('consentId');
	$type = $request->__get('type');
	$consent_obj = new IMW\Consent($app->dbh,1);
	
	$contents = $consent_obj->save_patient_consent_form($type,$patientId,$consentId);
	return json_encode($contents);
});

/* API To Save Sign Of Consent */
$this->respond(array('POST','GET'), '/saveConsentSign', function($request, $response, $service, $app) use(&$patientId){
	
	$b64Doc = '';
	$consentId = (int)$request->__get('consentId');
	$type = $request->__get('type');
	$signImg = $request->__get('signImg');
	$consent_obj = new IMW\Consent($app->dbh,1);
	
	$contents = $consent_obj->save_patient_consent_form($type,$patientId,$consentId,$signImg);
	return json_encode($contents);
});

/* API To Print All Consent Form Of The Package */
$this->respond(array('POST','GET'), '/printConsentPackage', function($request, $response, $service, $app) use(&$patientId){
	
	$b64Doc = '';
	$packageId = (int)$request->__get('packageId');
	$consent_obj = new IMW\Consent($app->dbh,1);
	// Call To Function To Get Raw Html Data To Print //
	$contents = $consent_obj->printPackage($patientId,$packageId);
	$contents = htmlspecialchars_decode($contents);
	
	$contents = str_ireplace('</br>','<br />',$contents);
	$contents = stripslashes(html_entity_decode($contents,true));
	// To Replace Img Src Path With Pintable PDF //
	$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
	$contents = str_ireplace('src="','src="'.$protocol.$_SERVER['SERVER_NAME'],$contents);
	$contents = str_ireplace("src='","src='".$protocol.$_SERVER['SERVER_NAME'],$contents);
	$contents = str_ireplace('�','',$contents);
	
	$html2pdf = new HTML2PDF('P','A4','en');
	$html2pdf->setTestTdInOnePage(true);
	$html2pdf->WriteHTML($contents);
	
	$b64Doc = $html2pdf->Output('', 'S');
	$b64Doc = base64_encode($b64Doc);
	//$data = fopen('test.pdf','w');
	//fwrite($data, base64_decode($b64Doc));
	$response = array('printConsentPackage' => $b64Doc);
	return json_encode($response);
	
});

/* API To Print Summary */
$this->respond(array('POST','GET'), '/printSummary', function($request, $response, $service, $app) use(&$patientId){
	
	$link['link'] = '';
	$b64Doc = '';
	$formId = $request->__get('formId');
	$sumary = $request->__get('sumary')=='yes' ? 'y' : 'n';
	$visit = $request->__get('visit')=='yes' ? 'y' : 'n';
	
	if($formId!='' && !empty($formId)){
		$formId = str_replace(",","|-|",$formId);
		$url = $GLOBALS['php_server']."/interface/patient_info/complete_pt_rec/print_iportal_chart.php?form_id=".$formId."&patient=".$patientId."&c_sumary=".$sumary."&c_visit=".$visit;
		
		// Code to hit the url to put HTML data into file //
		$curNew = curl_init();
		curl_setopt($curNew,CURLOPT_URL,$url);
		curl_setopt($curNew, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curNew, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true);
		$result = curl_exec($curNew);
		
		// Code to get HTML data from file //
		$path = $GLOBALS['php_server']."/library/html_to_pdf/pdffile.html";
		$summaryData = file_get_contents($path);
		
		$html2pdf = new HTML2PDF('P','A4','en');
		$html2pdf->setTestTdInOnePage(true);
		$html2pdf->WriteHTML($summaryData);
		
		$b64Doc = $html2pdf->Output('', 'S');
		$b64Doc = base64_encode($b64Doc);
	}
	//$file = fopen('test.pdf','w');
	//fwrite($file, base64_decode($b64Doc));
	$returnArr = array("printSummary"=>$b64Doc);
	return json_encode($returnArr);
});

// Hack to Accept blank  subCategory //
$this->respond(array('GET'), '', function(){});

$this->respond(function($request, $response, $service) use(&$patientId) {

});