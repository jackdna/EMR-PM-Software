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
 * Purpose: Routes for ContactLens API calls.
*/

$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
	$where = $startDate = $endDate = '';
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
});

//Get Last Contact Lens Rx
$this->get('/getLastRx', function($request, $response, $service,$app) use(&$patientId){
	
	$contactObj = new IMW\CONTACTLENS($app->dbh, $service, $patientId);
	
	//It contains the all default data required for contact lens
	$arrMasterDt = $contactObj->masterData;
	
	//Last CL Worksheet ID
	$clId = (isset($arrMasterDt['clId']) && empty($arrMasterDt['clId']) == false) ? $arrMasterDt['clId'] : '';
	
	//Get Last Rx Record
	$lastData = $contactObj->getLastRx($clId);
	
	$returnData = array('LastRx' => 'No Rx Found');
	if($lastData && count($lastData) > 0) $returnData = array('LastRx' => $lastData);
	
	return json_encode($returnData);
});

//Get Past Contact Lens Orders
$this->get('/getOrders', function($request, $response, $service,$app) use(&$patientId){
	
	$contactObj = new IMW\CONTACTLENS($app->dbh, $service, $patientId);
	$orderArr = array();
	
	if($request->__isset('startDate') && trim($request->__get('startDate')) !== '' ){
		$service->validateParam('startDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
	}
	
	if($request->__isset('endDate') && trim($request->__get('endDate')) !== '' ){
		$service->validateParam('endDate', 'Please provide valid end date.')->notNull()->isDate();
		
		$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
		
		if(trim($request->__get('startDate')) == ''){
			
			$service->validateParam('startDate', 'Please provide valid start date also.')->notNull()->isDate();
		}
	}
	
	if( empty($startDate) === false && empty($endDate) === true )
	{
		$endDate = $startDate;
	}
	
	$orderArr = $contactObj->getPtOrders($patientId, $startDate, $endDate);
	
	$returnData = array('Orders' => 'No Orders Found');
	if($orderArr && count($orderArr) > 0) $returnData = array('Orders' => $orderArr);
	
	return json_encode($returnData);
});

//Get available manufacturers 
$this->get('/getBrands', function($request, $response, $service,$app) use(&$patientId){
	$contactObj = new IMW\CONTACTLENS($app->dbh, $service, $patientId);
	$returnData = $arrData = $disposeData = array();
	
	//It contains the all default data required for contact lens
	$arrMasterDt = $contactObj->masterData;
	$searchword = '';
	
	if(count($arrMasterDt['ManufacturersData']) > 0){
		foreach($arrMasterDt['ManufacturersData'] as $key => &$val){
			$tmpArr = array();
			$tmpArr['ID'] = $key;
			$tmpArr['Brand'] = $val['brand'];
			$tmpArr['Manufacturer'] = $val['manufacturer'];
			$tmpArr['Type'] = $val['type'];
			
			$arrData[$tmpArr['Brand']] = $tmpArr;
		}
	}
	
	if($request->__isset('Brand') && trim($request->__get('Brand')) != ''){
		$service->validateParam('Brand', 'Please provide a valid name.')->notNull();
		$searchword = trim(strtolower($request->__get('Brand')));
	}
	
	$matches = array_filter($arrMasterDt['arrData'], function($var) use ($searchword) { return preg_match("/\b$searchword\b/i", $var); });
	if(count($matches) > 0) $arrData = $matches;
	
	print_r($arrData);
	die();
	
	foreach($arrData as $key => &$val){
		$tmpArr = array();
		
		$tmpArr['ID'] = $key;
		$tmpArr['Name'] = $val;
		
		$disposeData[] = $tmpArr;
	}
	
	$returnData = array('Disposable' => 'No disposables Found');
	if(count($disposeData) > 0) $returnData = array('Disposable' => $disposeData);
	
	return json_encode($returnData);
});

//Get available disposable values
$this->get('/getDisposable', function($request, $response, $service,$app) use(&$patientId){
	$contactObj = new IMW\CONTACTLENS($app->dbh, $service, $patientId);
	$returnData = $arrData = $disposeData = array();
	
	//It contains the all default data required for contact lens
	$arrMasterDt = $contactObj->masterData;
	$searchword = '';
	
	if($request->__isset('Name') && trim($request->__get('Name')) != ''){
		$service->validateParam('Name', 'Please provide a valid name.')->notNull();
		$searchword = trim(strtolower($request->__get('Name')));
	}
	
	$matches = array_filter($arrMasterDt['DisposableData'], function($var) use ($searchword) { return preg_match("/\b$searchword\b/i", $var); });
	
	$arrData = $arrMasterDt['DisposableData'];
	if(count($matches) > 0) $arrData = $matches;
	
	foreach($arrData as $key => &$val){
		$tmpArr = array();
		
		$tmpArr['ID'] = $key;
		$tmpArr['Name'] = $val;
		
		$disposeData[] = $tmpArr;
	}
	
	$returnData = array('Disposable' => 'No disposables Found');
	if(count($disposeData) > 0) $returnData = array('Disposable' => $disposeData);
	
	return json_encode($returnData);
});

//Get available Lens packages values
$this->get('/getLensPackages', function($request, $response, $service,$app) use(&$patientId){
	$contactObj = new IMW\CONTACTLENS($app->dbh, $service, $patientId);
	$returnData = $arrData = $packageDt = array();
	
	//It contains the all default data required for contact lens
	$arrMasterDt = $contactObj->masterData;
	$disposeId = '';
	
	if($request->__isset('DisposableID') && trim($request->__get('DisposableID')) != ''){
		$service->validateParam('DisposableID', 'Please provide a valid disposable id.')->notNull()->isInt()->isDisposable($app);
		$disposeId = trim($request->__get('DisposableID'));
	}
	
	//Change Array to the selected Disposable ID or get all data
	$arrData = $arrMasterDt['PackageData'];
	if(empty($disposeId) == false){
		$arrData = $arrMasterDt['PackageData'][$disposeId];
	}
	
	if(count($arrData) > 0){
		foreach($arrData as $key => &$val){
			$catName = $arrMasterDt['DisposableData'][$key];
			if(is_array($val) && count($val) > 0){
				foreach($val as $valK => &$valV){
					$tmpArr = array();
					$tmpArr['ID'] = $valK;
					$tmpArr['DisposableId'] = $key;
					$tmpArr['Name'] = $valV;
					
					$packageDt[ucfirst($catName)][] = $tmpArr;
				}
			}else{
				$tmpArr = array();
				$tmpArr['ID'] = $key;
				$tmpArr['DisposableId'] = $disposeId;
				$tmpArr['Name'] = $val;
					
				$packageDt[ucfirst($arrMasterDt['DisposableData'][$disposeId])][] = $tmpArr;
			}
		}
	}
	
	$returnData = array('Package' => 'No packages Found');
	if(count($packageDt) > 0) $returnData = array('Package' => $packageDt);
	
	return json_encode($returnData);
});

//Get supplies values
$this->get('/getSupplies', function($request, $response, $service,$app) use(&$patientId){
	$contactObj = new IMW\CONTACTLENS($app->dbh, $service, $patientId);
	$returnData = $arrData = $suppliesArr = array();
	
	//It contains the all default data required for contact lens
	$arrMasterDt = $contactObj->masterData;
	$arrData = $arrMasterDt['SupplyData'];
	
	if(count($arrData) > 0){
		foreach($arrData as $key => &$val){
			$tmpArr = array();
			$tmpArr['ID'] = $key;
			$tmpArr['Name'] = $val;
			
			$suppliesArr[] = $tmpArr;
		}
	}
	
	$returnData = array('Supplies' => 'No supplies Found');
	if(count($suppliesArr) > 0) $returnData = array('Supplies' => $suppliesArr);
	
	return json_encode($returnData);
});

//Calculate Boxes
$this->get('/calculateBoxes', function($request, $response, $service,$app) use(&$patientId){
	$contactObj = new IMW\CONTACTLENS($app->dbh, $service, $patientId);
	$disposeId = $lensPkgId = $suppliesId = '';
	
	//Disposal ID
	$service->validateParam('disposalId', 'Please provide a valid disposable id.')->notNull()->isInt()->isDisposable($app);
	$disposeId = trim($request->__get('disposalId'));
	
	//Lens package ID
	$service->validateParam('lensPkgId', 'Please provide a valid lens package id.')->notNull()->isInt()->isLensPkg($app);
	$lensPkgId = trim($request->__get('lensPkgId'));
	
	//Supplies ID
	$service->validateParam('suppliesId', 'Please provide a valid supplies id.')->notNull()->isInt()->isSupplyPkg($app);
	$suppliesId = trim($request->__get('suppliesId'));
	
	$boxStaus = $contactObj->calculateBoxes($disposeId, $lensPkgId, $suppliesId);
	
	$returnData = array('Boxes' => $boxStaus);
	
	return json_encode($returnData);
});

//Contact Lens Order
$this->post('/orderContactLens', function($request, $response, $service,$app) use(&$patientId){
	$contactObj = new IMW\CONTACTLENS($app->dbh, $service, $patientId);
	$disposeId = $lensPkgId = $suppliesId = $Boxes = $shipType = '';
	$shipAddress = $contactObj->masterData['patientAddress'];
	$site = (trim($request->__get('Site')) == 1 || trim($request->__get('Site')) == 2) ? trim($request->__get('Site')) : 0;
	
	//Disposal ID
	$service->validateParam('disposalId', 'Please provide a valid disposable id.')->notNull()->isInt()->isDisposable($app);
	$disposeId = trim($request->__get('disposalId'));
	
	//Lens package ID
	$service->validateParam('lensPkgId', 'Please provide a valid lens package id.')->notNull()->isInt()->isLensPkg($app);
	$lensPkgId = trim($request->__get('lensPkgId'));
	
	//Supplies ID
	$service->validateParam('suppliesId', 'Please provide a valid supplies id.')->notNull()->isInt()->isSupplyPkg($app);
	$suppliesId = trim($request->__get('suppliesId'));
	
	//Number of boxes
	$service->validateParam('Boxes', 'Please provide a valid order box number.')->notNull()->isInt();
	$Boxes = trim($request->__get('Boxes'));
	
	//Validating Box count is correct or not
	$boxStaus = $contactObj->calculateBoxes($disposeId, $lensPkgId, $suppliesId);
	
	if(empty($boxStaus) == false && $boxStaus != $Boxes){
		$response->append(' Invalid Box number.');
		$this->abort(400);
	}
	
	//Ship Type -- Pickup Or Deliver to Home
	$shipType = (trim($request->__get('shipType')) == 1 || trim($request->__get('shipType')) == 2) ? trim($request->__get('shipType')) : '';
	
	if(empty($shipType)){
		$service->validateParam('shipType', 'Please provide a valid value for Shipping Address.')->notNull()->isInt();
	}
	
	//If shipType == Delivery, Then change shipAddress to custom address
	if($shipType == 2 && trim($request->__get('shipAddress')) != ''){
		$shipAddress = trim($request->__get('shipAddress'));
	}elseif($shipType == 1){
		//If shipType == Pickup, Then change shipAddress to Facility Address
		$shipAddress = trim($contactObj->masterData['facilityAddress']);
	}
	
	$comments = $request->__get('comments');
	
	$orderStatus = $contactObj->orderContactLens($site, $disposeId, $lensPkgId, $suppliesId, $Boxes, $shipType, $shipAddress, $comments);
	
	die;
});

$this->respond(function($request, $response, $service) use(&$patientId) {

});