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
 * Purpose: Routes for generating CCDA
*/

$this->respond(array('GET', 'POST'), '*', function($request, $response, $service, $app) {
	$returnData = '';
	$patientId = $dos = $startDate = $endDate = '';
	
	/* Validating Values */
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt()->isPatient($app);
	$patientId = filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	if($request->__isset('dateOfService') && trim($request->__get('dateOfService')) !== '' ){
		$service->validateParam('dateOfService', 'Please provide valid date.')->notNull()->isDate();
		$dos = $app->dbh->imw_escape_string( $request->__get('dateOfService') );
		$startDate = $endDate = '';
	}else{
		if($request->__isset('startDate') && trim($request->__get('startDate')) !== '' ){
			$service->validateParam('startDate', 'Please provide valid start date.')->notNull()->isDate();
			
			$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
			$service->__set('startDate', $startDate);
			
			if($request->__isset('endDate') == false){
				
				$service->validateParam('endDate', 'Please provide valid end date also.')->notNull()->isDate();
				
			}
		}
		
		if($request->__isset('endDate') && trim($request->__get('endDate')) !== '' ){
			$service->validateParam('endDate', 'Please provide valid end date.')->notNull()->isDate();
			
			$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
			$service->__set('endDate', $endDate);
			
			if($request->__isset('startDate') == false){
				
				$service->validateParam('startDate', 'Please provide valid start date also.')->notNull()->isDate();
			}
		}
	}
	
	if(empty($dos) && empty($startDate) && empty($endDate)){
		$response->append('Please provide either a specific date(Date of Service) or a date range');
		$this->abort(422);
	}
	
	$token = $app->dbh->imw_escape_string($request->__get('accessToken'));
	$ccda_obj = new IMW\CCDA($app->dbh, $patientId, $dos, $startDate, $endDate, $token);
	
	if($ccda_obj->form_id == 'no_dos'){	//Check if DOS is valid or not
		$response->append('Please provide a valid dateOfService');
		$this->abort(422);	
	}
	$ccda_data = $ccda_obj->get_ccda();
	if(isset($ccda_data['status']) && empty($ccda_data['status']) == false){
		$response->append($ccda_data['status']);
		$this->abort(422);
	}
	
	if(isset($ccda_data['xml']) && empty($ccda_data['xml']) == false){
		$returnData = $ccda_data['xml'];
	}
	
	$request->__set('responseFormat', 'xml');
	
	$domDoc = new DOMDocument;
	libxml_use_internal_errors(false);
	$domDoc->preserveWhiteSpace = false;
	$domDoc->formatOutput = true;
	$domDoc->load( $returnData );
	
	$xml = $domDoc->saveXML();
	file_put_contents($returnData, $xml);
	
	$response->file($returnData);
});
 
//Hack to Accept blank  subCategory/
$this->respond(array('GET', 'POST'), '', function(){});
