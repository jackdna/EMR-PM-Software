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
 * Access Type: Include
 * Purpose: Common Routes to be called for Each API call.
*/

error_reporting(0);
ini_set('display_errors', 0);

require_once(dirname(__FILE__).'/library/autoload.php');
include_once($GLOBALS['fileroot'].'/library/html_to_pdf/html2pdf.class.php');
include_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_schedule_functions.php");

/*Initialize API router class*/
$router = new IMW\IMWAPI();

/*Register Lazy Service for Database Handler*/
$router->respond(function ($request, $response, $service, $app) {
    $app->register('dbh', function() {
		$dbh = new IMW\DBH();
		return $dbh;
    });
    
    $app->register('saveParameters', function() {
		$dbh = new IMW\GetSaveParmeters();
		return $dbh;
    });
	
	$app->register('pdfString', function() {
		return function($content = '', $orientation = 'P', $pgSize = 'A4', $lang = 'en', $output = 'S', $fileName = '')
	    {
			if(empty($content)) return '';
			
			$html2pdf = new HTML2PDF($orientation, $pgSize, $lang);
			$html2pdf->setTestTdInOnePage(false);
			$html2pdf->WriteHTML($content);
			
			$b64Doc = $html2pdf->Output($fileName, $output);
			$b64Doc = base64_encode($b64Doc);
			
			//$six_digit_random_number = mt_rand(100000, 999999);
			//$fileName = 'imw_api_'.$six_digit_random_number.'.pdf';
			//file_put_contents($fileName, base64_decode($b64Doc));
			
			return $b64Doc;
		};
    });
    	
    $app->register('passwordhash', function() {

	    return function($pass, $userType)
	    {
		    $hashMethid = strtoupper(HASH_METHOD);
		    $userType =  (int)trim($userType);

		    if( $userType === 2 || $userType === 3 )
		    {
			    $hashMethid = 'SHA1';
		    }
		    elseif( $userType === 1 && $hashMethid === 'SHA1' )
		    {
			    $hashMethid = 'SHA2';
		    }

		    if(!empty($pass)){

			    if($hashMethid)
			    {
				    if($hashMethid=='MD5' && !is_valid_md5($pass))
				    {
					    return md5($pass);
				    }
				    elseif($hashMethid=='SHA1')
				    {
					    return hash('sha1',$pass);
				    }
				    elseif( $hashMethid=='SHA2' && !is_valid_sha256($pass))
				    {
					    return hash('sha256',$pass);
				    }
				    else{
					    return $pass;
				    }
			    }
		    }
		    return '';
	    };
    });
    
    $service->addValidator('date', function ($str) {
	    return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $str);
    });

    $service->addValidator('time', function ($str) {
	    //return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $str);		// With seconds
	    return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $str);		// Without seconds
    });

    $service->addValidator('zero', function ($str) {
	    return ((int)$str === 0);
    });

    $service->addValidator('bool', function ($str) {

    	$str = trim($str);

    	if( $str == 'true' || $str == 'false' )
    		return true;
    	else
    		return false;
    });

    //Check Patient
    $service->addValidator('patient', function ($str, $app, $isExternal = false) {
		$sql = 'SELECT id FROM `patient_data` WHERE `id`='.$str;
		
		if($isExternal === true)
	    $sql = 'SELECT id FROM `patient_data` WHERE `id`='.$str.' || `External_MRN_4` = '.$str.'';

	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });

    //Check Physician
    $service->addValidator('physician', function ($str, $app, $callFrom = '') {
		
		$callCondition = '';
		if(empty($callFrom) == false){
			switch(strtolower($callFrom)){
				case 'scheduler':
					//If validator is called from scheduler
					$callCondition = ' AND Enable_Scheduler = "1" ';
				break;
			}
		}
	    
		$sql = 'SELECT id FROM users WHERE user_type = 1 AND delete_status = 0 '.$callCondition.' AND id ='.$str;

	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });

    //Check Facility
    $service->addValidator('facility', function ($str, $app) {

	    $sql = 'Select id FROM facility WHERE id = '.$str;

	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });	

    //Check Procedure
    $service->addValidator('procedure', function ($str, $app) {

	    $sql = 'SELECT id as ID FROM slot_procedures WHERE active_status = "yes" AND id = '.$str;

	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });	

	//Check Guarantor
    $service->addValidator('guarantor', function ($str, $klein) {
		$app  = $klein->app();
	    $sql = 'SELECT id as ID FROM resp_party WHERE id = '.$str;

	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });

	//Check Family
    $service->addValidator('family', function ($str, $klein) {
		$app  = $klein->app();
		$sql = 'SELECT id as ID FROM patient_family_info WHERE id = '.$str;

	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });

	//Check Patient Insurance
    $service->addValidator('insurance', function ($str, $klein) {
		
		$app  = $klein->app();
		$request = $klein->request();
		
		$sql = '
			SELECT 
				ins_dt.id as ID 
			FROM 
				insurance_data ins_dt 
				LEFT JOIN insurance_case ins_case ON (ins_case.ins_caseid = ins_dt.ins_caseid) AND LOWER(ins_case.case_status) = "open" AND ins_case.del_status = 0
				LEFT JOIN insurance_case_types ins_types ON (ins_types.case_id = ins_case.ins_case_type) AND (ins_types.normal = 1 || ins_types.vision = 1)
			WHERE 
				LOWER(ins_dt.type) IN ("primary", "secondary") AND
				ins_case.patient_id = '.$request->__get('patientId').' AND
				ins_dt.id = '.$str;
				
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });	
	
	//Check Insurance Company
    $service->addValidator('insurancecompany', function ($str, $klein) {
		
		$app  = $klein->app();
		$request = $klein->request();
		
		$sql = '
		SELECT 
			id
		FROM 
			insurance_companies 
		where 
			ins_del_status = 0 AND
			id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });	
	
	//Check Patient Allergy
    $service->addValidator('ptallergy', function ($str, $klein) {
		
		$app  = $klein->app();
		$request = $klein->request();
		
		$ptId = $request->__get('patientId');
		
		$sql = '
		SELECT 
			id
		FROM 
			lists 
		where 
			type in (3,7) and
			LOWER(allergy_status) = "active" AND
			pid = "'.$ptId.'" AND
			id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	//Check Allergy
    $service->addValidator('allergy', function ($str, $klein) {
		
		$app  = $klein->app();
		
		$sql = '
			SELECT 
				allergies_id
			FROM 
				allergies_data 
			WHERE 
				allergies_id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	//Check Patient Medications
    $service->addValidator('ptmedication', function ($str, $klein) {
		
		$app  = $klein->app();
		$request = $klein->request();
		
		$ptId = $request->__get('patientId');
		
		$sql = '
		SELECT 
			id
		FROM 
			lists 
		where 
			type in (1,4) and
			LOWER(allergy_status) = "active" AND
			pid = "'.$ptId.'" AND
			id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	//Check Medications
    $service->addValidator('medication', function ($str, $klein) {
		
		$app  = $klein->app();
		
		$sql = '
			SELECT 
				id
			FROM 
				medicine_data 
			WHERE 
				del_status = 0 AND
				id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	//Check Patient Surgery
    $service->addValidator('ptsurgery', function ($str, $klein) {
		
		$app  = $klein->app();
		$request = $klein->request();
		
		$ptId = $request->__get('patientId');
		
		$sql = '
		SELECT 
			id
		FROM 
			lists 
		where 
			type in (5,6) and
			LOWER(allergy_status) != "deleted" AND
			pid = "'.$ptId.'" AND
			id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	//Check Surgery
    $service->addValidator('sxprocedure', function ($str, $klein) {
		
		$app  = $klein->app();
		
		$sql = '
			SELECT 
				id
			FROM 
				lists_admin 
			WHERE 
				delete_status = 0 AND
				type in (5,6) AND
				id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	//Check If provided id is a referring physician
    $service->addValidator('refphysician', function ($str, $klein) {
		
		$app  = $klein->app();
		$arrRefPhy = array();
		
		//Get All Referring Physicians arr
		$sql = 'SELECT physician_Reffer_id FROM refferphysician WHERE FirstName!="" and LastName!=""'; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) > 0 )
	    {
		    while($rowRefPhy = $app->dbh->imw_fetch_assoc($resp)){
				$arrRefPhy[] = $rowRefPhy['id'];
			}
	    }
		
		if(count($arrRefPhy) > 0){
			if(in_array($str,$arrRefPhy)){
				return true;
			}
		}else{
			return false;
		}

	    return true;
    });
	
	/*Validate Length of Array*/
	$service->addValidator('arrayLength', function ($arr, $min, $max = null) {
		
		if( !is_array($arr) )
		{
			return false;
		}
		
		$len = count($arr);
		return null === $max ? $len === $min : $len >= $min && $len <= $max;
    });
	
	/* Validate Time slot id */
	$service->addValidator('timeslot', function ($str = '', $klein = '') {
		if(empty($str) || empty($klein)) return false;
		$serv = $klein->service();
		
		$date = date('Y-m-d', $str);
		$serv->validate($date, 'Please provide a valid time slot id.')->isDate();
		
		return true;
    });
	
	/* Validate Disposable id */
	$service->addValidator('disposable', function ($str = '', $klein = '') {
		if(empty($str) || empty($klein)) return false;
		$app  = $klein->app();
		$sql = 'Select id, cat_name FROM in_contact_cat WHERE del_status = 0 AND id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	/* Validate Lens Package id */
	$service->addValidator('lenspkg', function ($str = '', $klein = '') {
		if(empty($str) || empty($klein)) return false;
		$app  = $klein->app();
		$sql = 'Select id FROM in_options WHERE opt_type = 5 AND module_id = 3 AND del_status = 0 AND id = '.$str; 
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	/* Validate Supply id */
	$service->addValidator('supplypkg', function ($str = '', $klein = '') {
		if(empty($str) || empty($klein)) return false;
		$app  = $klein->app();
		$sql = 'Select id FROM in_supply WHERE del_status = 0 AND id = '.$str;
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });
	
	
	/* Validate Supply id */
	$service->addValidator('medroute', function ($str = '', $klein = '') {
		if(empty($str) || empty($klein)) return false;
		$app  = $klein->app();
		$sql = 'SELECT id from route_codes WHERE id = '.$str;
			
	    $resp  = $app->dbh->imw_query($sql);

	    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
	    {
		    return false;
	    }

	    return true;
    });	
	/* Validate Appointment custom stats */
	$service->addValidator('apptstat', function ($str = '', $app = '') {
		if(empty($str) || empty($app)) return false;
		$sql = 'SELECT id from schedule_status WHERE col_type != 0 AND id = '.$str;
			
    $resp  = $app->dbh->imw_query($sql);

    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
    {
	    return false;
    }

    return true;
  });	
	/* Validate Appointment custom unique stats name */
	$service->addValidator('uniqueapptstatnm', function ($str = '', $app = '') {
		if(empty($str) || empty($app)) return false;
		$sql = 'SELECT id from schedule_status WHERE (LOWER(status_name) = "'.strtolower($str).'" || LOWER(alias) = "'.strtolower($str).'")';
			
    $resp  = $app->dbh->imw_query($sql);

    if( !$resp || $app->dbh->imw_num_rows($resp) === 0 )
    {
	    return true;
    }

    return false;
  });	
	
	$app->register('multipleValues', function() {
		return function($str = '', $serv)
	    {
			if(empty($str)) return $str;
			
			//Provided String is a single value or multiple values
			if (strpos($str, ',') !== false) {
				$valArr = array();
				$valArr = explode(',', $str);
				
				//Filtering Array
				$valArr = array_filter($valArr);
				$valArr = array_unique($valArr);
				
				//If count is greater than 5 - Abort the call
				$serv->validate($valArr, 'Please provide a total of 5 Id\'s only.')->isArrayLength(1, 5);
				
				//Checking if all ID's are valid or not
				$str = implode(',', $valArr);
			}
			
			return $str;
		};
    });
	
	//To replace all form variables with their respective values
	//Arguments -->	
		//$content ==> (String)To replace variables in 
		//$options ==> (String)Comma separated string containing the case you want to be replace
	
	$app->register('replaceVariables', function(){
		return function($content = '', $options = ''){
			if(empty($options) || empty($content)) return $content;
			$optArr = array();
			
			switch(strtolower($options)){
				case 'input':
					$inputVal = explode('<input',$content);
					$content = $inputVal[0];
					for($i=1;$i<count($inputVal);$i++){
						$pos = strpos($inputVal[$i],'value=\"');
						$str = substr($inputVal[$i],$pos+7);
						$pos1 = strpos($str,'\"');
						$inputVals = substr($str,0,$pos1);
						$pos2 = strpos($str,'>');
						$lastVal = substr($str,$pos2+1);
						$content .= $inputVals.' '.$lastVal;
					}
				break;	
			}
			return $content;
		};
	});
	
	
});


/*Log Request Data*/
$router->respond('*', function($request, $response, $service, $app) use($router) {
	
	$request = $router->request();
	$service = $router->service();
	
	/*Set Variables from Request Payload - Fix*/
	$payloads = trim( $request->body() );
	
	if( $payloads !== '' )
	{
		$payloads = json_decode($payloads, true);
		foreach($payloads as $key=>$value)
		{
			$request->__set($key, $value);
		}
	}
	
	/*Request Path Called*/
	$path = $app->dbh->imw_escape_string($request->uri());
	
	/*Request Method Called*/
	$method = $app->dbh->imw_escape_string($request->method());
	
	/*Request Headers*/
	$headers = json_encode($request->headers()->all());
	$headers = addslashes($headers);
	
	/*Client Browser*/
	$userAgent = $app->dbh->imw_escape_string($request->userAgent());
	
	/*Client IP Address*/
	$ip = $request->ip();
	$service->validate($ip, 'Error in Request.')->isip();
	$ip = $app->dbh->imw_escape_string($ip);
	
	/*All parameters passed in Request*/
	$parameters = json_encode($request->params());
	$parameters = addslashes($parameters);
	
	/*Calling Date-Time*/
	$callDateTime = date('Y-m-d H:i:s');
	
	$sql = "INSERT INTO `fmh_api_call_log`
			SET
				`path` = '".$path."',
				`method` = '".$method."',
				`headers` = '".$headers."',
				`user_agent` = '".$userAgent."',
				`ip` = '".$ip."',
				`parameters` = '".$parameters."',
				`call_date_time` = '".$callDateTime."'";
	
	$resp  = $app->dbh->imw_query($sql);
	if( !$resp )
	{
		$router->response()->body('Internal Connection Error.');
		$router->abort(503);
	}
	else
		$request->__set('logId', $app->dbh->imw_insert_id());
});


/*Log Response Data and Code in DB*/
$router->afterDispatch(function() use($router){
	
	$request = $router->request();
	$response = $router->response();
	$app = $router->app();
	
	$responseCode = $response->code();
	$responseData = $app->dbh->imw_escape_string($response->body());
	
	$logId = (int)$request->__get('logId');
	$tokenId = (int)$request->__get('TokenId');
	
	/*Response Date Time*/
	$responseDateTime = date('Y-m-d H:i:s');
	
	$sql = "UPDATE `fmh_api_call_log`
			SET
				`response_code` = '".$responseCode."',
				`response` = '".$responseData."',
				`response_date_time` = '".$responseDateTime."',
				`token_id` = ".$tokenId."
			WHERE
				`id`=".$logId;
	
	$resp  = $app->dbh->imw_query($sql);
	if( !$resp )
		$router->abort(503);
});


/*Actions to be taken in Case of Error or Exceptions*/
$router->onError(function($router, $error_message, $ExceptionType, $ExceptionObject){
	
	$ExceptionType = explode('\\', $ExceptionType);
	$ExceptionType = array_pop($ExceptionType);
	
	/*Set Response Code*/
	switch( $ExceptionType )
	{
		case 'ValidationException':
			$responseCode = 400;
			break;
		default:
			$responseCode = 503;
	}
	$router->response()->code($responseCode);
	$router->response()->body($error_message);
});

/*Handle HTTP Errors, mainly abort Actions*/
$router->onHttpError(function($code) use ($router) {
	
	$response = $router->response();
	
	if( $code === 404 )
		$response->body('API endpoint does not eists!');
	/*elseif( $code !== 200)
		$response->body('Unknows HTTP Error Occoured!');*/
});

/*TypeCast variable to String*/
$converToString = function(&$item){
	$item = (string)$item;
};