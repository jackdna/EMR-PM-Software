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
 * Purpose: Routes for Print Prescription.
*/

$patientId = 0;
	
/*Validate Patient ID*/
$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
	//error_reporting(E_ERROR);
	//ini_set('display_errors', 1);
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
});

/* Print Prescription Glasses */
$this->respond(array('POST','GET'), '/printGlass', function($request, $response, $service, $app) use(&$patientId){
	$b64Doc = '';
	$glassPrint_obj = new IMW\PRESCRIPTION($app->dbh,1);
	// Call To function which will get Raw Html Data to print //
	$contents = $glassPrint_obj->glassPrint($patientId);
	$contents = htmlspecialchars_decode($contents);
	$contents = str_ireplace('</br>','<br />',$contents);
	$contents = stripslashes(html_entity_decode($contents,true));
	$contents = str_ireplace('�','',$contents);

	$html2pdf = new HTML2PDF('P','A4','en');
	$html2pdf->setTestTdInOnePage(true);
	$html2pdf->WriteHTML($contents);
	
	$b64Doc = $html2pdf->Output('', 'S');
	$b64Doc = base64_encode($b64Doc);
	//$data = fopen('test.pdf','w');
	//fwrite($data, base64_decode($b64Doc));
	$response = array('PrintGlass' => $b64Doc);
	return json_encode($response);
	
});

/* Get Contact Lens Order */
$this->respond(array('POST','GET'), '/printContactLens', function($request, $response, $service, $app) use(&$patientId){
	
	$b64Doc = '';
	$contactLens_obj = new IMW\PRESCRIPTION($app->dbh,1);
	// Call To function which will get Raw Html Data to print //
	$contents = $contactLens_obj->printContactLens($patientId);
	$contents = htmlspecialchars_decode($contents);
	$contents = str_ireplace('</br>','<br />',$contents);
	$contents = stripslashes(html_entity_decode($contents,true));
	$contents = str_ireplace('�','',$contents);

	$html2pdf = new HTML2PDF('P','A4','en');
	$html2pdf->setTestTdInOnePage(true);
	$html2pdf->WriteHTML($contents);
	
	$b64Doc = $html2pdf->Output('', 'S');
	$b64Doc = base64_encode($b64Doc);
	//$data = fopen('test.pdf','w');
	//fwrite($data, base64_decode($b64Doc));
	$response = array('PrintContactLens' => $b64Doc);
	return json_encode($response);
	
});

// Hack to Accept blank  subCategory //
$this->respond(array('GET'), '', function(){});

$this->respond(function($request, $response, $service) use(&$patientId) {

});