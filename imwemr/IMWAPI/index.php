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
 * Access Type: Direct
 * Purpose: Support iDoc API Calls using Klein REST api router.
*/

require_once(dirname(__FILE__).'/commonRoutes.php');
ini_set('max_execution_time', 3000);
/*Handle Token Generation*/
$router->get('/getToken', function($request, $response, $service, $app) use($router){
	
	$hash = $app->passwordhash;
	
	$service->validateParam('userId', 'Please provide valid username')->notNull();
	$service->validateParam('password', 'Please provide password')->notNull();
	$service->validateParam('userType', 'Please provide a valid user type')->notNull()->isInt()->isLen(1, 3);
	
	//User Type
		// 1- iDoc User
		// 2,3 - iPortal Patient or Auth. Representative
	
	$userType   = $app->dbh->imw_escape_string( $request->__get('userType') );
	$userId	    = $app->dbh->imw_escape_string( $request->__get('userId') );
	$password   = $app->dbh->imw_escape_string( $request->__get('password') );
	$password   = $hash($password, $userType );

	$apiUserId = 0;
	$getFields = $tableName = '';
	
	//Setting token Id based on the userType
	switch($userType){
		//If user is from iDoc
		case 1:
			
			$getFields = '`id`, `locked`, `access_pri`';
			$tableName = 'users';
			
		break;
		
		case 2:
		case 3:
			
			$getFields = '`id`';
			$tableName = 'patient_data';
			
		break;
	}
	
	$sql = 'SELECT 
				'.$getFields.'
			FROM
				`'.$tableName.'`
			WHERE
				`username`= BINARY \''.$userId.'\'
			AND `password`= BINARY \''.$password.'\'';
	$resp = $app->dbh->imw_query($sql);
	
	
	if( $resp && $app->dbh->imw_num_rows($resp) === 1 )
	{
		$data = $app->dbh->imw_fetch_assoc($resp);
		
		//iDoc User
		if($userType == 1){
			$access_arr = unserialize(html_entity_decode(trim($data["access_pri"])));
		
			if( (int)$data['locked'] !== 0 )
			{
				$response->code(401);
				$response->body('User Locked.');
				$router->skipNext();
			}
			elseif( !isset($access_arr['priv_api_access']) || ( isset($access_arr['priv_api_access']) && $access_arr['priv_api_access'] == 0 ) )
			{
				$response->code(403);
				$response->body('User unauthorized.');
				$router->skipNext();
			}
		}elseif($userType == 'fmh'){
			if( (int)$data['locked'] !== 0 )
			{
				$response->code(401);
				$response->body('User Locked.');
				$router->skipNext();
			}
		}
		
		$apiUserId = (int)$data['id'];
	}
	else
	{
		$response->code(403);
		$response->body('Invalid Credentials.');
		$router->skipNext();
	}
	
	/*Generate Token and return to the User*/
	$token = $request->ip().$request->__get('userId').time();
	$token = hash('sha256', $token);
	
	/*Log Token in DB*/
	
	$timeStamp = time();
	$createDateTime = date('Y-m-d H:i:s', $timeStamp);
	$expireDateTime = date('Y-m-d H:i:s', strtotime('+24 hours', $timeStamp) );

	$sql = 'INSERT INTO `fmh_api_token_log`
			SET
				`token`=\''.$token.'\',
				`user_id`='.$apiUserId.',
				`usertype`='.$userType.',
				`create_date_time`=\''.$createDateTime.'\',
				`expire_date_time`=\''.$expireDateTime.'\'';
	$resp = $app->dbh->imw_query($sql);
	
	if( $resp )
	{
		$tokenId = $app->dbh->imw_insert_id();
		$request->__set('TokenId', $tokenId);
	}
	
	$response->append($token);
	$router->skipNext();
});


/*Verify Token*/
$router->respond('*', function($request, $response, $service, $app) use($router) {
	
	$service->validateParam('accessToken', 'No / Invalid access token provided.')->isAlnum()->notNull()->isLen(64, 64);
	
	$accessToken = $app->dbh->imw_escape_string( $request->__get('accessToken') );
	
	$sql = 'SELECT `id`, `expire_date_time`, `usertype`, `user_id`
			FROM
				`fmh_api_token_log`
			WHERE
				`token`=\''.$accessToken.'\'';
	$resp = $app->dbh->imw_query($sql);
	$tokenId = 0;
	$userType = 0;
	$usrId = 0;
	
	if( $resp && $app->dbh->imw_num_rows($resp) === 1 )
	{
		$tokenData = $app->dbh->imw_fetch_assoc($resp);
		
		$tokenExpireDateTime = strtotime($tokenData['expire_date_time']);
		
		//Valid User type or not
		$userType = (int) $tokenData['usertype'];
		
		$usrId = (int) $tokenData['user_id'];
		if($userType == 2 || $userType == 3)
		$service->validate($usrId, 'Please provide valid Patient ID.')->isPatient($app);
		
		if( $tokenExpireDateTime < time() )
		{
			$response->append('Invalid Token.');
			$router->abort(401);
		}
		$tokenId = (int)$tokenData['id'];
	}
	else
	{
		$response->append('Token does not exists.');
		$router->abort(401);
	}
	
	$request->__set('TokenId', $tokenId);
	$request->__set('userType', $userType);
	
	//If user is patient then he/she cant view the details of any other patient id as user id is set the same in every call
	$ptId = (($userType == 2 || $userType == 3)) ? $usrId : '';
	if(empty($ptId) == false){
		$request->__set('patientId', $ptId);
	}
});


/*Patient Search*/
$router->get('/searchPatient', function($request, $response, $service, $app) use($router, $converToString){
	//$service->validateParam('lname', 'Please provide valid lastname.')->isAlnum()->notNull();
	
	if($request->__isset('dob') && trim($request->__get('dob')) !== '' )
		$service->validateParam('dob', 'Please provide valid Date of Birth.')->notNull()->isDate();
	
	if($request->__isset('id') && trim($request->__get('id')) !== '' )
		$service->validateParam('id', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app, true);
	
	$firstName	= $app->dbh->imw_escape_string( $request->__get('fname') );
	$lastName	= $app->dbh->imw_escape_string( $request->__get('lname') );
	$dob		= $app->dbh->imw_escape_string( $request->__get('dob') );
	$searchId	= $app->dbh->imw_escape_string( $request->__get('id') );
	$maxRecords	= (int)$app->dbh->imw_escape_string( $request->__get('maxRecords' ) );
	
	$searchArr = array();
	if( $lastName !== '' ) $searchArr['lname'] = $lastName;
	if( $firstName !== '' ) $searchArr['fname'] = $firstName;
	if( $dob !== '' ) 	$searchArr['DOB'] = $dob;
	if( $searchId !== '' ) 	$searchArr['id'] = $searchId;
	
	if(count($searchArr) == 0){
		$response->append('No value provided to search');
		$router->abort(403);
	}
	
	$sqlArr = array();
	if(count($searchArr) > 0){
		foreach($searchArr as $key => &$val){
			$str = '';
			if(strtolower($key) == 'dob'){
				$str = '`'.$key.'` = \''.$val.'\'';
			}
			elseif(strtolower($key) == 'id'){
				$str = '`id` = \''.$val.'\' || `External_MRN_4` = \''.$val.'\'';
			}else{
				$str = '`'.$key.'` LIKE \'%'.$val.'%\'';
			}
			if(empty($val) == false) array_push($sqlArr, $str);
		}
	}
	
	if(count($sqlArr) > 0){
		$whereStr = implode(' AND ', $sqlArr);
		if(empty($whereStr) == false) $where = 'WHERE '.$whereStr;
	}
	
	/* if( $lastName !== '' )
		$where = ' WHERE `lname` LIKE \'%'.$lastName.'%\'';
	
	if( $firstName !== '' )
		$where .= ' AND `fname` LIKE \'%'.$firstName.'%\'';
	if( $dob !== '' )
		$where .= ' AND `DOB` = \''.$dob.'\''; */
	
	$limit = ' LIMIT '.( ($maxRecords > 0) ? $maxRecords : 100 );
	
	$sql = 'SELECT
				`id` AS \'ID\',
				`External_MRN_4` AS \'ExternalID\',
				`fname` AS \'FirstName\',
				`lname` AS \'LastName\',
				`mname` AS \'MiddleName\',
				`suffix` AS \'Suffix\',
				`DOB` AS \'DateOfBirth\',
				`ss` AS \'SSN\',
				`sex` AS \'Sex\',
				`phone_home` AS \'HomePhone\',
				`phone_biz` AS \'BusinessPhone\',
				`phone_cell` AS \'CellPhone\',
				`email` AS \'Email\'
			FROM
				`patient_data`
			'.$where.$limit;
	$resp = $app->dbh->imw_query($sql);
	
	$returnData = array();
	
	if( $resp && $app->dbh->imw_num_rows($resp) > 0 )
	{
		while( $row = $app->dbh->imw_fetch_assoc($resp) )
		{
			$row['SSN'] = filter_var($row['SSN'], FILTER_SANITIZE_NUMBER_INT);
			$row['DateOfBirth'] = filter_var($row['DateOfBirth'], FILTER_SANITIZE_NUMBER_INT);
			$row['HomePhone'] = filter_var($row['HomePhone'], FILTER_SANITIZE_NUMBER_INT);
			$row['BusinessPhone'] = filter_var($row['BusinessPhone'], FILTER_SANITIZE_NUMBER_INT);
			$row['CellPhone'] = filter_var($row['CellPhone'], FILTER_SANITIZE_NUMBER_INT);
			$row['ExternalID'] = filter_var($row['ExternalID'], FILTER_SANITIZE_NUMBER_INT);
			
			//$row['DateOfBirth'] = '0000-00-00';
			
			if( (double) preg_replace('/[^0-9]/', '', $row['DateOfBirth']) <= 0 ){
				$row['DateOfBirth'] = null;
			}
			
			/* Patient multi address */
			$pt_add_qry = "
				SELECT 
					street as Street1,
					street2 as Street2,
					city as City,
					state as State,
					postal_code as Zip
				FROM patient_multi_address 
				WHERE patient_id = ".$row['ID']." AND del_status = 0";
				
				$respAddres = $app->dbh->imw_query($pt_add_qry);
				
				if($respAddres && $app->dbh->imw_num_rows($respAddres) > 0){
					while($rowAddress = $app->dbh->imw_fetch_assoc($respAddres)){
						
						$row['Address'][] = $rowAddress;
					}
					
					$app->dbh->imw_free_result($respAddres);
					unset($respAddres);
				}
			
			array_push($returnData, $row);
		}
	}
	else
	{
		$returnData = array('status' => 'No match found.');
	}
	
	array_walk_recursive($returnData, $converToString);
	
	return json_encode($returnData);
});

/* Registration Status */
$router->get('/getRegistrationStatus', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$patient_id = '';
	
	// Validating Values //
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patient_id	= $request->__get('patientId');
	if(empty($patient_id) == true)
	{
		$response->append('Invalid Patient ID.');
		$router->abort(400);
	}
	$qry = "SELECT 
				id as patientId,
				fname as FirstName,
				lname as LastName,
				dob as DOB,				
				fmh_pt_status as Status				
			FROM patient_data
			WHERE 
				id = ".$patient_id."";
	$res = $app->dbh->imw_query($qry);			
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			array_push($returnData, $row);
		}
		$app->dbh->imw_free_result($res);
	}else{
		$returnData = array('status' => 'No patients found.');
	}	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Update Pt. registration status */
$router->post('/updateRegistrationStatus', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$status = $patientId = '';
	
	/* Validating Values */
	$service->validateParam('status', 'Please provide valid status.')->notNull()->isAlpha();
	$status	= filter_var($request->__get('status'), FILTER_SANITIZE_STRING);
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt()->isPatient($app);
	$patientId	= filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	$sql_qry = "SELECT 
					fmh_pt_status,id 
				FROM patient_data
				WHERE
					id = ".$patientId."";
	
	$res = $app->dbh->imw_query($sql_qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		$row = $app->dbh->imw_fetch_assoc($res);
		$update_qry = 'UPDATE patient_data SET fmh_pt_status = \''.$status.'\' WHERE id = '.$row['id'].'';
		$up_qry = $app->dbh->imw_query($update_qry);
		if($up_qry){
			$returnData = array('status' => 'Status updated');
		}
		$app->dbh->imw_free_result($res);
	}else{
		$returnData = array('status' => 'No patients found.');
	}		
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});


/*Demographics Data*/
$router->with('/getDemographics', __DIR__ .'/demographicRoutes.php');
$router->with('/updateDemographics', __DIR__ .'/demographicRoutes.php');


/*List modified Patients*/
$router->get('/getPatientsModified', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$startTime = $endTime = $time_qry = '';
	/* Validating Values */
	
	//Date
	$service->validateParam('startDate', 'Please provide valid start date format.')->notNull()->isDate();
	$service->validateParam('endDate', 'Please provide valid end date format.')->notNull()->isDate();
	
	//Time
	if($request->__isset('startTime') && trim($request->__get('startTime')) !== '' ){
		$service->validateParam('startTime', 'Please provide valid start time format.')->notNull()->isTime();
		$startTime = $app->dbh->imw_escape_string( $request->__get('startTime') );
	}
	
	if($request->__isset('endTime') && trim($request->__get('endTime')) !== '' ){
		$service->validateParam('startTime', 'Please provide valid start time.')->notNull()->isTime();
		
		$service->validateParam('endTime', 'Please provide valid end time format.')->notNull()->isTime();
		$endTime	= $app->dbh->imw_escape_string( $request->__get('endTime') );
	}
	
	if(empty($endTime) == true) $endTime = $startTime;
	
	if(empty($startTime) == false && empty($endTime) == false){
		$time_qry = '(DATE_FORMAT(timestamp,"%H:%i") BETWEEN "'.$startTime.'" AND "'.$endTime.'")';
	}
	
	/* Retriving values */
	$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
	$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
	
	if(empty($startTime) == false && empty($endTime) == false){
		$time_qry = ' AND '.$time_qry;
	}
	
	$sql = $app->dbh->imw_query("SELECT id,fname,lname,External_MRN_4 AS ExternalID, FROM patient_data WHERE (DATE(timestamp) BETWEEN '$startDate' AND '$endDate') ".$time_qry." ORDER BY timestamp DESC");
	if($sql && $app->dbh->imw_num_rows($sql) > 0){
		while($row = $app->dbh->imw_fetch_assoc($sql)){
			$tmp_arr = array();
			$tmp_arr['PatientId'] = filter_var($row['id'], FILTER_SANITIZE_NUMBER_INT);
			$tmp_arr['ExternalID'] = filter_var($row['ExternalID'], FILTER_SANITIZE_NUMBER_INT);
			$tmp_arr['FirstName'] = filter_var($row['fname'], FILTER_SANITIZE_STRING);
			$tmp_arr['LastName'] = filter_var($row['lname'], FILTER_SANITIZE_STRING);
			
			array_push($returnData, $tmp_arr);
		}
		$app->dbh->imw_free_result($sql);
	}else{
		$returnData = array('status' => 'No match found.');
	}
	array_walk_recursive($returnData, $converToString);
	
	return json_encode($returnData);
});

/*Providers List*/
$router->get('/getProvidersList', function($request, $response, $service, $app) use($router, $converToString){
	//Get scheduler enabled provider only
	$schedulerOnly = $where = '';
	if($request->__isset('schedulerOnly') && trim($request->__get('schedulerOnly')) !== '' ){
		$service->validateParam('schedulerOnly', 'Please provide a valid value to continue.')->notNull()->isInt();
		$schedulerOnly = $app->dbh->imw_escape_string( $request->__get('schedulerOnly') );
		
		switch($schedulerOnly){
			case 0:
				$schedulerOnly = '';
			break;
			case 1:
				continue;
			break;
			
			default:
				$response->append('Please provide a valid value');
				$router->abort(401);
			
		}
	}
	
	if(empty($schedulerOnly) == false) $where = 'AND Enable_Scheduler = 1';
	
	$returnData = array();
	$qry = $app->dbh->imw_query("SELECT 
				id as ProviderId,
				fname as FirstName,
				mname as MiddleName,
				lname as LastName,
				user_npi as NPI				
			FROM users
			WHERE 
				user_type = 1 
			AND delete_status = 0
			AND superuser = 'no'
			".$where."
			ORDER BY lname ASC");
	if($qry && $app->dbh->imw_num_rows($qry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($qry)){
			array_push($returnData, $row);
		}
		$app->dbh->imw_free_result($qry);
	}else{
		$returnData = array('status' => 'No provider found.');
	}	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Procedures List */
$router->get('/getProcedureList', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = $responseArr = $portalArr = array();
	
	//Get Portal enabled procedures
	$sqlpreProc = $app->dbh->imw_query("select appt_type_name from iportal_req_appt_type limit 0,1");
	if($sqlpreProc && $app->dbh->imw_num_rows($sqlpreProc) > 0){
		$rowFetch = $app->dbh->imw_fetch_assoc($sqlpreProc);
		if(unserialize(html_entity_decode($rowFetch['appt_type_name'])) !== false){
			$tmpData = unserialize(html_entity_decode($rowFetch['appt_type_name']));
			if(count($tmpData) > 0 ){
				foreach($tmpData as $str){
					list($id,$val)=explode('~|~',$str);
					$tmpArr = array();
					$tmpArr['ID'] = $id;
					$tmpArr['ProdecureName'] = $val;
					
					array_push($portalArr, $tmpArr);
				}
			}
		}
	}
	
	$qry = $app->dbh->imw_query("
			SELECT 
				id as ID,
				proc as ProdecureName,
				acronym as ProdecureAlias			
			FROM slot_procedures
			WHERE 
				active_status = 'yes'
			ORDER BY proc ASC");
	
	if($qry && $app->dbh->imw_num_rows($qry) > 0){
		
		while($row = $app->dbh->imw_fetch_assoc($qry)){
			
			$row['ProdecureName'] = trim($row['ProdecureName']);
			if( empty($row['ProdecureName']) == false)
			{
				array_push($responseArr, $row);
			}
		}
		$app->dbh->imw_free_result($qry);
	}
	else{
		$responseArr = array('status' => 'No Procedure found.');
	}
	
	if(!isset($responseArr['status']) && count($responseArr) > 0) $returnData['Procedures'] = $responseArr;
	if(count($portalArr) > 0) $returnData['PortalProcedures'] = $portalArr;
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Appointment Status */
$router->get('/getAppointmentsList', function($request, $response, $service, $app) use($router, $converToString){
	$patientId = $startDate = $endDate = $where = $startTime = $endTime = $time_qry = '';
	$valid_start_date = $valid_end_date = false;
	$returnData = $statusArr = array();
	
	/* Appointment status array */
	$statQry = $res = $app->dbh->imw_query("SELECT id, status_name FROM schedule_status");
	if($statQry && $app->dbh->imw_num_rows($statQry) > 0){
		while($rowFetch = $app->dbh->imw_fetch_assoc($statQry)){
			$statusArr[$rowFetch['id']] = $rowFetch['status_name'];
		}
	}
	
	if(!isset($statusArr[0])) $statusArr[0] = 'Created / Restore';
	
	/* Validating Values */
	$patientId	= $request->__get('patientId');
	$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
	$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
	
	if(empty($patientId) == false){
		
		$service->validateParam('patientId', 'Please provide valid patient id.')->isInt()->isPatient($app);
		
		$patientId	= $request->__get('patientId');
		
		$where .= "sch_apt.sa_patient_id = '".$patientId."'";
		
	}
	
	if(empty($startDate) == false){
		$service->validateParam('startDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
		
		if(empty($endDate) == true){
			
			$service->validateParam('endDate', 'Please provide valid end date also.')->notNull()->isDate();
			
		}
	}
	
	if(empty($endDate) == false){
		$service->validateParam('endDate', 'Please provide valid end date.')->notNull()->isDate();
		
		$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
		
		if(empty($startDate) == true){
			
			$service->validateParam('startDate', 'Please provide valid start date also.')->notNull()->isDate();
			
		}
	}
	
	//Time
	$startTime = $app->dbh->imw_escape_string($request->__get('startTime'));
	if($request->__isset('startTime') && empty($startTime) == false){
		$service->validate(urldecode($startTime), 'Please provide valid start time format.')->notNull()->isTime();
		//$service->validateParam('startTime', 'Please provide valid start time format.')->notNull()->isTime();
		$startTime = $app->dbh->imw_escape_string( $request->__get('startTime') );
	}
	
	$endTime = $app->dbh->imw_escape_string($request->__get('endTime'));
	if($request->__isset('endTime') && empty($endTime) == false){
		$startTime = $app->dbh->imw_escape_string($request->__get('startTime'));
		$service->validate(urldecode($startTime), 'Please provide valid start time format.')->notNull()->isTime();
		//$service->validateParam('startTime', 'Please provide valid start time.')->notNull()->isTime();
		
		$service->validate(urldecode($endTime), 'Please provide valid end time format.')->notNull()->isTime();
		//$service->validateParam('endTime', 'Please provide valid end time format.')->notNull()->isTime();
		$endTime	= $app->dbh->imw_escape_string( $request->__get('endTime') );
	}
	
	if(empty($endTime) == true) $endTime = $startTime;
	
	if(empty($startTime) == false && empty($endTime) == false){
		$time_qry = '(DATE_FORMAT(ps.status_time,"%H:%i") BETWEEN "'.urldecode($startTime).'" AND "'.urldecode($endTime).'")';
	}
	
	
	if(empty($endDate) == false && empty($startDate) == false){
		
		if(empty($where) == false) $where .= ' AND ';
		
		$where .= "ps.status_date between '".$startDate."' and '".$endDate."'"; 
		if(empty($time_qry) == false) $where = $where.' AND ';
	}
	
	/* Retriving Values */
	$qry = "
		SELECT 
			sch_apt.id as appointmentId,
			sch_apt.sa_app_start_date as appointmentDate,
			TIME(sch_apt.sa_app_starttime) as appointmentTime,
			ps.status as appointmentStatus,
			sch_apt.procedureid as procedureId,
			slt_proc.proc as procedureName,
			sch_apt.sa_doctor_id as physicianId,
			usrs.fname as physicianFirstName,
			usrs.lname as physicianLastName,
			sch_apt.sa_facility_id as FacilityId,
			fac.name as locationName,
			sch_apt.sa_patient_id as patientId,
			pt_data.fname as patientFirstName,
			pt_data.mname as patientMiddleName,
			pt_data.lname as patientLastName,
			DATE_FORMAT(sch_apt.sa_app_time, '%Y-%m-%d') as lastModifiedDate
		FROM
			schedule_appointments sch_apt
			LEFT JOIN slot_procedures slt_proc ON (sch_apt.procedureid = slt_proc.id)
			LEFT JOIN users usrs ON (sch_apt.sa_doctor_id = usrs.id)
			LEFT JOIN patient_data pt_data ON (sch_apt.sa_patient_id = pt_data.id)
			LEFT JOIN facility fac ON (sch_apt.sa_facility_id = fac.id)
			LEFT JOIN previous_status ps ON sch_apt.id=ps.sch_id
		WHERE 
			".$where.$time_qry."
		ORDER BY sch_apt.sa_app_start_date DESC
	";
	$res = $app->dbh->imw_query($qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		$tmpArr = array();
		while($row = $app->dbh->imw_fetch_assoc($res)){
			$row['appointmentStatus'] = $statusArr[$row['appointmentStatus']];
			$tmpArr[$row['appointmentId']][] = $row;
		}
		
		$tmpArr = array_filter($tmpArr);
		if(count($tmpArr) > 0){
			foreach($tmpArr as $key => $objVal){
				$tmpArrval = (is_array($objVal) && count($objVal) > 0) ? end(array_filter($objVal)) : '';
				if(empty($tmpArrval) == false) array_push($returnData, $tmpArrval);
			}
		}
		$app->dbh->imw_free_result($res);
	}else{
		
		$returnData = array('status' => 'No Appointments found.');
	}
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Get Patient Message  */
$router->get('/getPatientMessages', function($request, $response, $service, $app) use($router, $converToString){
	$where = '';$returnData = array();
	
	/* Get & Validate Values */
	$service->validateParam('senderType', 'Please provide valid sender type.')->notNull()->isInt();
	$sender_type = (int)$request->__get('senderType');
	
	$service->validateParam('patientId', 'Please provide valid patient id.')->notNull()->isInt()->isPatient($app);
	$patientId = (int)$request->__get('patientId');
	
	
	if( $request->__isset('physicianId') && trim($request->__get('physicianId')) != '')
		$service->validateParam('physicianId', 'Please provide valid physician id.')->isInt()->notZero();
	
	$physicianId = (int)$request->__get('physicianId');
	
	
	$date = $request->__get('date');
	if(empty($date) == false){
		$service->validateParam('date', 'Please provide valid date.')->isDate();
		$where .= " AND DATE(msg_date_time) = '".$date."' ";
	}
	
	/* Retriving Values */
	switch($sender_type){
		case 1:		// Receiver --> Patient && Sender --> Physician/User
			$where .= "AND receiver_id = ".$patientId." AND sender_id != 0 ";
			if(empty($physicianId) == false){
				$where .= "AND sender_id = ".$physicianId."  ";
			}	
		break;
		
		case 2:		// Receiver --> Physician/User  && Sender --> Patient
			$where .= "AND sender_id = ".$patientId." ";
			if(empty($physicianId) == false){
				$where .= "AND receiver_id = ".$physicianId." ";
			}
		break;
	}
	
	$qry = "
		SELECT 
			pt_msg_id as messageId,
			sender_id as senderId,
			receiver_id as receiverId,
			communication_type as senderType,
			DATE(msg_date_time) as date,
			msg_subject as subject,
			msg_data as messageData,
			is_appt
		FROM
			patient_messages
		WHERE 
			del_status_by_pt = 0 AND 
			communication_type = ".$sender_type." AND
			is_done = 0 ".$where."
	";
	$res = $app->dbh->imw_query($qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			if(isset($row['is_appt']) && $row['is_appt'] == 1){$row['AppointmentRequest'] = $row['is_appt'];unset($row['is_appt']);}
			else unset($row['is_appt']);
			
			$tmpArr = array();
			if(isset($row['messageData']) && empty($row['messageData']) == false && $row['AppointmentRequest'] == 1){
				$row['messageData'] = str_ireplace('<br>', '<br />', $row['messageData']);
				$arrData = explode('<br />', $row['messageData']);
				$arrData = array_filter($arrData);
				
				if(count($arrData) > 0){
					foreach($arrData as $obj){
						$string = trim(strip_tags($obj));
						$arrDt = explode('-', $string, 2);
						if(empty($arrDt[0]) == false && isset($arrDt[0])) $tmpArr[str_replace(' ','',trim($arrDt[0]))] = trim($arrDt[1]);
					}
				}
				
				if(count($tmpArr) > 0) $row['messageData'] = $tmpArr;
			}
			
			array_push($returnData, $row);
		}
		$app->dbh->imw_free_result($res);
	}else{
		$returnData = array('status' => 'No Messages found.');
	}
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Send Patient Messages */
$router->post('/sendPatientMessage', function($request, $response, $service, $app) use($router, $converToString){
	$db_sender_id = $db_receiver_id = '';$returnData = array();
	$physicianId = '';
	
	/* Get & Validate Values */
	$service->validateParam('patientId', 'Please provide valid patient id.')->notNull()->isInt()->isPatient($app);
	$patientId = (int)$request->__get('patientId');
	
	$service->validateParam('senderType', 'Please provide valid sender type.')->notNull()->isInt();
	$sender_type = (int)$request->__get('senderType');
	
	if($request->__isset('physicianId') && trim($request->__get('physicianId')) != ''){
		$service->validateParam('physicianId', 'Please provide valid physician id.')->notNull()->isInt()->notZero();
		$physicianId = (int)$request->__get('physicianId');
	}
	
	if(empty($physicianId)){
		//Getting Patient Provider ID 
		$chkQry = $app->dbh->imw_query("SELECT patient_data.providerID as provider_id FROM patient_data, users WHERE patient_data.id = '".$patientId."' and users.id = patient_data.providerID and users.delete_status!=1");
		if($app->dbh->imw_num_rows($chkQry) > 0){
			$fetchId = $app->dbh->imw_fetch_assoc($chkQry);
			$physicianId = $fetchId['provider_id'];
		}
	}
	
	$service->validateParam('subject', 'Please provide a subject for message')->notNull();
	$msgSubject = $request->__get('subject');
	
	$service->validateParam('data', 'Please provide content for message')->notNull();
	$msgData = $request->__get('data');
	
	if(empty($physicianId)) $physicianId = 1;
	
	/* Setting Values */
	switch($sender_type){
		case 1:		// Receiver --> Patient && Sender --> Physician/User
			$db_sender_id = $physicianId;
			$db_receiver_id = $patientId;	
		break;
		
		case 2:		// Receiver --> Physician/User  && Sender --> Patient
			$db_sender_id = $patientId;
			$db_receiver_id = $physicianId;
		break;
	}
	
	$qry = "
		INSERT INTO patient_messages SET
			`sender_id` = ".$db_sender_id.",
			`receiver_id` = ".$db_receiver_id.",
			`communication_type` = ".$sender_type.",
			`msg_subject` = '".$msgSubject."',
			`msg_data` = '".$msgData."',
			`delivery_date` = '".date('Y-m-d')."'
	";
	$res = $app->dbh->imw_query($qry);
	if($res && $app->dbh->imw_affected_rows() > 0){
		$insert_id = $app->dbh->imw_insert_id();
		$returnData = array('msgId' => $insert_id);
	}else{
		$returnData = array('status' => 'Unable to send message.');
	}
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Get Visit DOS */
$router->get('/getVisitDOS', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$patientId = $limit = '';
	
	/* Validating Values */
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->notNull()->isInt()->isPatient($app);
	$patientId	= filter_var($request->__get('patientId'), FILTER_SANITIZE_NUMBER_INT);
	
	/* Not implemented yet
	if( $request->__isset('maxRecords') ){
		$service->validateParam('maxRecords', 'Please provide valid limit')->notNull()->isInt();
		$limit = filter_var($request->__get('maxRecords'), FILTER_SANITIZE_NUMBER_INT);
	}
	
	if(empty($limit) == false){
		$limit = ' LIMIT '.$limit;
	} */
	
	$sql_qry = "SELECT 
					date_of_service as DOS	
				FROM chart_master_table
				WHERE
					patient_id = ".$patientId." ORDER BY date_of_service DESC".$limit;			
	$res = $app->dbh->imw_query($sql_qry);
	if($res && $app->dbh->imw_num_rows($res) > 0){
		while($row = $app->dbh->imw_fetch_assoc($res)){
			array_push($returnData,$row['DOS']);
		}
		$app->dbh->imw_free_result($res);
	}else{
		$returnData = array('status' => 'No DOS found.');
	}		
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Care Team Members */
$router->get('/getCareTeamMembers', function($request, $response, $service, $app) use($router, $converToString){
	$where = $startDate = $endDate = $dos = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	if($request->__isset('startDate') && trim($request->__get('startDate')) !== '' ){
		$service->validateParam('startDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('startDate') );
		/* if($request->__isset('endDate') == false){
			
			$service->validateParam('endDate', 'Please provide valid end date also.')->notNull()->isDate();
			
		} */
	}
	
	if($request->__isset('endDate') && trim($request->__get('endDate')) !== '' ){
		$service->validateParam('endDate', 'Please provide valid end date.')->notNull()->isDate();
		$endDate	= $app->dbh->imw_escape_string( $request->__get('endDate') );
		if($request->__isset('startDate') == false){
			
			$service->validateParam('startDate', 'Please provide valid start date also.')->notNull()->isDate();
		}
	}
	
	if( empty($startDate) === false && empty($endDate) === true )
	{
		$endDate = $startDate;
	}
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(date_of_service) BETWEEN '".$startDate."' AND '".$endDate."' ";
	}	
	
	
	$dbh = $app->dbh;
	/* Get Providers IDS for the Chart note */
	$providerIds = array();
	$providersGroups = array();

	$sql = "SELECT `provIds` FROM `chart_master_table` WHERE `patient_id`='".$patientId."' AND `provIds`!='' ".$where;
	$resp = $dbh->imw_query($sql);
	if( $resp && $dbh->imw_num_rows($resp) > 0 )
	{
		while( $row = $dbh->imw_fetch_assoc($resp) )
		{
			$providerIds = array_merge($providerIds,  explode(',', $row['provIds']));
		}
		
		//$providerIds = array_map(array($this,'convertToInt'), $providerIds);
		
		$providerIds = array_map('trim', $providerIds);
		
		if(count($providerIds) > 0){
			$providerIds = array_filter($providerIds);	
			$providerIds = array_unique($providerIds);
		}
		
		if( is_array($providerIds) && count($providerIds) > 0 )
		{
			$sql = 'SELECT `user_group_id`, `id` FROM `users` WHERE `id` IN('.implode(',', $providerIds).')';
			$resp = $dbh->imw_query($sql);
			if( $resp && $dbh->imw_num_rows($resp)>0 )
			{
				while( $row = $dbh->imw_fetch_assoc($resp) )
				{
					$providersGroups[$row['id']] = (int)$row['user_group_id'];
				}
			}
		}
	}
	
	$sql_patient = "SELECT * FROM patient_data WHERE id = '".$patientId."' LIMIT 0,1";
	$result_patient = $dbh->imw_query($sql_patient);
	$row_patient 	= $dbh->imw_fetch_assoc($result_patient);
	$providerID = $row_patient['providerID'];
	
	$tempProviderID = false;
	$tempTechnicianID = false;
	
	if( count($providerIds) > 0)
	{
		foreach($providersGroups as $groupKey=>$groupVal)
		{
			if( $groupVal === 2 && $tempProviderID === false )
			{
				$tempProviderID = (int)$groupKey;
			}
			elseif( $groupVal === 5 && $tempTechnicianID === false )
			{
				$tempTechnicianID = (int)$groupKey;
			}
		}
		
		if( $tempProviderID !== false && $tempProviderID > 0)
		{
			$providerID = $tempProviderID;
		}
	}
	//Primiary Physician
	$qry_provider = "SELECT 
						id as ID,
						fname as FirstName,
						mname as MiddleName,
						lname as LastName,
						'Primary Physician' AS 'Role',
						default_facility as DefaultFacility
					FROM users WHERE id = '".$providerID."'";  // PRIMARY PHYSICIAN
	$res_provider = $dbh->imw_query($qry_provider);
	
	$tmp_arr = $return_arr = array();
	if($dbh->imw_num_rows($res_provider) > 0 && $res_provider){
		$row_provider = $dbh->imw_fetch_assoc($res_provider);
		$default_facility = (empty($row_provider['DefaultFacility']) == false) ? $row_provider['DefaultFacility'] : 1;
		
		if($default_facility > 0){
			$qry_facility = "select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where id = '".$default_facility."'";
		}
		else{
			$qry_facility = "select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where facility_type = '1'";
		}
		
		
		$res_facility = $dbh->imw_query($qry_facility);
		if($res_facility && $dbh->imw_num_rows($res_facility) > 0){
			$row_facility = $dbh->imw_fetch_assoc($res_facility);
		}else{
			$sql = $dbh->imw_query("select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where facility_type = '1'");
			if($sql && $dbh->imw_num_rows($sql) > 0){
				$row_facility = $dbh->imw_fetch_assoc($sql);
			}
		}
		
		$tmp_arr['ID'] = $row_provider['ID'];
		$tmp_arr['FirstName'] = $row_provider['FirstName'];
		$tmp_arr['MiddleName'] = $row_provider['MiddleName'];
		$tmp_arr['LastName'] = $row_provider['LastName'];
		$tmp_arr['Role'] = $row_provider['Role'];
		if(count($row_facility) > 0){
			$tmp_arr['Address'] = $row_facility;	
		}
		$return_arr[] = $tmp_arr;
		unset($tmp_arr);
	}
	
	$qry_reff = "SELECT 
					physician_Reffer_id as ID,
					FirstName,
					MiddleName,
					LastName,
					NPI as NPI,
					'Primary Care Physician' AS 'Role',
					Address1 as Add_Address1,
					Address2 as Add_Address2,
					ZipCode as Add_ZipCode,
					City as Add_City,
					State as Add_State,
					physician_phone as Add_Phone,
					physician_fax as Add_Fax,
					physician_email as Add_Email,
					direct_email as Add_DirectEmail 
				FROM refferphysician WHERE physician_Reffer_id = '".$row_patient['primary_care_phy_id']."'"; // PCP PHYSICIAN
	$res_reff = $dbh->imw_query($qry_reff);
	$pcp_physician_arr = array();
	if($dbh->imw_num_rows($res_reff) > 0){
		while($row_reff = $dbh->imw_fetch_assoc($res_reff)){
			foreach($row_reff as $key => $val){
				if(strpos($key, 'Add_') !== false){
					$key = str_replace('Add_','',$key);
					$tmp_arr['Address'][$key] = $val;
				}else{
					$tmp_arr[$key] = $val;
				}
			}
		}
		array_filter($tmp_arr);
		$return_arr[] = $tmp_arr;
		unset($tmp_arr);
	}
	
	//Nurse
	$qry_provider = "SELECT 
						id as ID,
						fname as FirstName,
						mname as MiddleName,
						lname as LastName,
						default_facility as DefaultFacility
					FROM users WHERE id = '".(($tempTechnicianID!==false)?$tempTechnicianID:$row_patient['assigned_nurse'])."'";  // PRIMARY PHYSICIAN
	$res_provider = $dbh->imw_query($qry_provider);
	
	if($dbh->imw_num_rows($res_provider) > 0 && $res_provider){
		$row_provider = $dbh->imw_fetch_assoc($res_provider);
		$default_facility = (empty($row_provider['DefaultFacility']) == false) ? $row_provider['DefaultFacility'] : 1;
		
		if($default_facility > 0){
			$qry_facility = "select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where id = '".$default_facility."'";
		}
		else{
			$qry_facility = "select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where facility_type = '1'";
		}
		
		
		$res_facility = $dbh->imw_query($qry_facility);
		if($res_facility && $dbh->imw_num_rows($res_facility) > 0){
			$row_facility = $dbh->imw_fetch_assoc($res_facility);
		}else{
			$sql = $dbh->imw_query("select name as Name,phone as Phone,street as Street,city as City,state as State,postal_code as ZipCode from facility where facility_type = '1'");
			if($sql && $dbh->imw_num_rows($sql) > 0){
				$row_facility = $dbh->imw_fetch_assoc($sql);
			}
		}
		
		$tmp_arr['ID'] = $row_provider['ID'];
		$tmp_arr['FirstName'] = $row_provider['FirstName'];
		$tmp_arr['MiddleName'] = $row_provider['MiddleName'];
		$tmp_arr['LastName'] = $row_provider['LastName'];
		$tmp_arr['Role'] = 'Nurse';
		if(count($row_facility) > 0){
			$tmp_arr['Address'] = $row_facility;	
		}
		$return_arr[] = $tmp_arr;
		unset($tmp_arr);
	} 

	array_walk_recursive($return_arr, $converToString);
	return json_encode($return_arr);
});


/* Facility List */
$router->get('/getFacility', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$qry = $app->dbh->imw_query("
		Select 
			id as ID,
			(
				CASE 
					WHEN facility.facility_type = '1' THEN 'HQ'
					ELSE 'Location'
				END
			) AS FacilityType,
			facility.name as FacilityName, 
			CONCAT_WS(',', street, city, state, postal_code, facility.zip_ext) as 'Location',
			phone as PhoneNumber
		FROM 
			facility 
			LEFT JOIN groups_new ON(groups_new.gro_id = facility.default_group and groups_new.del_status='0')
		");
	if($qry && $app->dbh->imw_num_rows($qry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($qry)){
			array_push($returnData, $row);
		}
		$app->dbh->imw_free_result($qry);
	}else{
		$returnData = array('status' => 'No provider found.');
	}	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Patient Languages List */
$router->get('/getLanguages', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$qry = $app->dbh->imw_query("
		Select 
			id as ID,
			name as Name,
			iso_639_2 as ISOCode
		FROM 
			pt_languages
		WHERE	
			del_status = 0
	");
	if($qry && $app->dbh->imw_num_rows($qry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($qry)){
			array_push($returnData, $row);
		}
		$app->dbh->imw_free_result($qry);
	}else{
		$returnData = array('status' => 'No provider found.');
	}	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Request Appointment */
$router->post('/requestAppointment', function($request, $response, $service, $app) use($router, $converToString){
	$patientId = $physicianId = $facilityId = $ApptReasonId = $ApptReason = $AppointmentInformation = $AppointmentDate = $AppointmentTime = $phyName = $SelFacility = '';
	
	/* Default Variables */
	$reqApptPg = 1;
	$apptReqSend = 1;
	$templd = '';
	
	/* Validating Variables */
	
	//Patient ID
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	//Physician ID
	$service->validateParam('physicianId', 'Please provide valid Physician ID.')->isInt()->notNull()->isPhysician($app);
	$physicianId	= (int)$request->__get('physicianId');
	
	//Facility ID
	$service->validateParam('facilityId', 'Please provide valid Facility ID.')->isInt()->notNull()->isFacility($app);
	$facilityId	= (int)$request->__get('facilityId');
	
	//Appointment Reason
	$service->validateParam('AppointmentReason', 'Please provide valid Procedure ID.')->isInt()->notNull()->isProcedure($app);
	$ApptReasonId	= (int)$request->__get('AppointmentReason');
	
	//Appointment Date
	$service->validateParam('AppointmentDate', 'Please provide valid appointment date.')->notNull()->isDate();
	$AppointmentDate	= $request->__get('AppointmentDate');
	
	//Appointment Time
	$service->validateParam('AppointmentTime', 'Please provide valid appointment time.')->notNull()->isTime();
	$AppointmentTime	= $request->__get('AppointmentTime');
	
	//Appointment Information
	$AppointmentInformation = $request->__get('AppointmentInformation');
	
	
	//Requesting Appointment
		//Physician name
		$doc_qry = $app->dbh->imw_query("select fname,lname,mname,id from users WHERE id = '".$physicianId."'");
		if($app->dbh->imw_num_rows($doc_qry) > 0){
			$doc_data = $app->dbh->imw_fetch_assoc($doc_qry);
			if($doc_data["mname"] != "") { $mname = " ".$doc_data["mname"].". "; }
			$phyName = $doc_data["lname"].", ".$doc_data["fname"].$mname;
		}
		
		//Procedure name
		$procQry = $app->dbh->imw_query('SELECT proc from slot_procedures where id = "'.$ApptReasonId.'"');
		if($app->dbh->imw_num_rows($procQry) > 0){
			$Procdata = $app->dbh->imw_fetch_assoc($procQry);
			$ApptReason = $Procdata['proc'];
		}
		
		//Facility name
		$facQry = $app->dbh->imw_query('SELECT name from facility where id = "'.$facilityId.'"');
		if($app->dbh->imw_num_rows($facQry) > 0){
			$facdata = $app->dbh->imw_fetch_assoc($facQry);
			$SelFacility = $facdata['name'];
		}
		
		//Patient name
		$ptEmail = $ptPhone = $ptAdd = $ptName = '';
		$ptQry = $app->dbh->imw_query('SELECT fname,mname,lname,phone_home,email,street,street2 from patient_data where id = "'.$patientId.'"');
		if($app->dbh->imw_num_rows($ptQry) > 0){
			$ptData = $app->dbh->imw_fetch_assoc($ptQry);
			$ptEmail = $ptData['email'];
			$ptPhone = $ptData['phone_home'];
			$ptAdd = $ptData['street'].', '.$ptData['street2'];
			$ptName = $ptData['lname'].', '.$ptData['fname'].' '.$ptData['mname'];
		}
		
		$returnData = array('status' => 'Request not send');
		$msg_data = "<b> Patient Name </b> - ".addslashes($ptName)."<br />";
		$msg_data .= "<b> Email </b> - ".addslashes($ptEmail)."<br />";
		$msg_data .= "<b> Phone </b> - ".addslashes($ptPhone)."<br />";
		$msg_data .= "<b> Address </b> - ".addslashes($ptAdd)."<br />";
		$msg_data .= "<b> Physician Name </b> - ".$phyName."<br />";
		$msg_data .= "<b> Selected Facility </b> - ".addslashes($SelFacility)."<br />";
		$msg_data .= "<b> Appointment Reason </b> - ".addslashes($ApptReason)."<br />";
		$msg_data .= "<b> Appointment Date </b> - ".$AppointmentDate."<br />";
		$msg_data .= "<b> Appointment Time </b> - ".$AppointmentTime."<br />";
		$msg_data .= "<b> Additional Information </b> - ".addslashes($AppointmentInformation)."<br />";
		$msg_subject = "Patient - Appointment Request ";
		if($apptReqSend == 1){
			
			//Checking if a request is already pending or not
			$chkRequest = $app->dbh->imw_query('SELECT pt_msg_id from patient_messages where is_appt = 1 AND communication_type = 2 AND sender_id = '.$patientId.' AND is_done = 0 AND del_status = 0');
			if($chkRequest && $app->dbh->imw_num_rows($chkRequest) > 0){
				$response->append('An appointment request is already pending for approval.');
				$router->abort(400);
			}
			
			$req_qry = $app->dbh->imw_query("INSERT INTO patient_messages SET is_appt = 1, receiver_id = '".$physicianId."', sender_id = '".$patientId."', communication_type = 2, msg_subject = '".addslashes($msg_subject)."', msg_data = '".$msg_data."'");
			if($req_qry){
				$returnData = array('status' => 'Request Sent.');
			}
		}
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
	
});


/* Facility List */
$router->get('/getRelationship', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$chkQry = imw_query('SELECT id as ID, relation as Relation FROM patient_relations where del_status = 0');
	if(imw_num_rows($chkQry) > 0){
		while($row = imw_fetch_assoc($chkQry)){
			array_push($returnData, $row);
		}
	}
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Insurance Companies List */
$router->get('/getInsuranceCompany', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$chkQry = imw_query('
		SELECT 
			id as ID,
			name as InsuranceCompany,
			in_house_code as InHouseCode,
			contact_address as ContactAddress
		FROM 
			insurance_companies 
		where 
			ins_del_status = 0'
	);
	
	if(imw_num_rows($chkQry) > 0){
		while($row = imw_fetch_assoc($chkQry)){
			array_push($returnData, $row);
		}
	}
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Allergy List */
$router->get('/getAllergy', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$chkQry = imw_query('
		SELECT 
			`allergies_id` as ID,
			`allergie_name` as Name,
			`alias` as Alias,
			`recall_code` as RecallCode,
			`procedure` as \'Procedure\',
			`description` as Description
		FROM 
			allergies_data'	
	);
	
	if(imw_num_rows($chkQry) > 0){
		while($row = imw_fetch_assoc($chkQry)){
			array_push($returnData, $row);
		}
	}
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Medications List */
$router->get('/getMedications', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$chkQry = imw_query('
		SELECT 
			`id` as ID,
			`medicine_name` as Name,
			`ocular` as Type,
			`ccda_code` as CCDACode
		FROM 
			medicine_data
		WHERE
			del_status = 0
	');
	
	if(imw_num_rows($chkQry) > 0){
		while($row = imw_fetch_assoc($chkQry)){
			$row['Type'] = ($row['Type'] == 0) ? 'Systemic' : 'Ocular';
			array_push($returnData, $row);
		}
	}
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Med Route List */
$router->get('/getMedRoutes', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$chkQry = imw_query('
		SELECT 
			id as ID,
			LOWER(route_name) as Route,
			code as Code
		from 
			route_codes
	');
	
	if(imw_num_rows($chkQry) > 0){
		while($row = imw_fetch_assoc($chkQry)){
			$row['Route'] = (isset($row['Route']) && empty($row['Route']) == false) ? ucfirst($row['Route']) : '';
			array_push($returnData, $row);
		}
	}
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});


/* Surgeries List */
$router->get('/getSurgeries', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$chkQry = imw_query('
		Select distinct(title) as Title,id as ID from lists_admin where type in (5,6) and delete_status=0  group by trim(title) order by trim(title)
	');
	
	if(imw_num_rows($chkQry) > 0){
		while($row = imw_fetch_assoc($chkQry)){
			$tmpArr = array();
			$tmpArr['ID'] = $row['ID'];
			$tmpArr['Title'] = $row['Title'];
			array_push($returnData, $tmpArr);
		}
	}
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Referring Physician List */
$router->get('/getReferringPhysicians', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$chkQry = imw_query('
		SELECT 
			physician_Reffer_id as ID,
			if(MiddleName!="",(concat(LastName,", ",FirstName," ",MiddleName)),(concat(LastName,", ",FirstName))) as RefPhysician 
		from 
			refferphysician 
		WHERE  
			FirstName!="" and LastName!=""
	');
	
	if(imw_num_rows($chkQry) > 0){
		while($row = imw_fetch_assoc($chkQry)){
			array_push($returnData, $row);
		}
	}
	
	array_walk_recursive($returnData, $converToString);
	return json_encode($returnData);
});

/* Patient Statements */
$router->get('/getPatientStatements', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$where = $startDate = $endDate = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
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
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(created_data) BETWEEN '".$startDate."' AND '".$endDate."' ";
	}
	
	//Get Patient Statement Results 
	$chkStatement = $app->dbh->imw_query('
		SELECT 
			previous_statement_id as ID,
			created_date as CreatedDate,
			CONCAT(" '.show_currency().'", statement_balance) as Balance
		FROM 
			previous_statement 
		WHERE 
			statement_acc_status=1 and patient_id = '.$patientId.'
			'.$where.'
		ORDER BY created_date desc
	');
	
	if($app->dbh->imw_num_rows($chkStatement) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkStatement)){
			array_push($returnData, $row);
		}
	}
	
	if(count($returnData) == 0) $returnData = 'No Statements found';
	
	return json_encode($returnData);
});

/* Patient Education Material */
$router->get('/getPatientEducationMaterial', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$where = $startDate = $endDate = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
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
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(date_time) BETWEEN '".$startDate."' AND '".$endDate."' ";
	}
	
	//Get Patient Statement Results 
	$chkStatement = $app->dbh->imw_query('
		SELECT 
			doc_id as ID,
			name as Name,
			TIME(date_time) as Time,
			DATE(date_time) as Date
		FROM 
			document_patient_rel 
		WHERE 
			p_id = '.$patientId.' AND
			status = "0" AND 
			doc_id != "0"
			'.$where.'
			ORDER BY date_time DESC
	');
	
	if($app->dbh->imw_num_rows($chkStatement) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkStatement)){
			$returnData[$row['Date']][] = $row;
		}
	}
	
	if(count($returnData) == 0) $returnData = 'No Material found';
	
	return json_encode($returnData);
});

/* Get PGHD */
$router->get('/getPGHD', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$where = $startDate = $endDate = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
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
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(reqDateTime) BETWEEN '".$startDate."' AND '".$endDate."' ";
	}
	
	//Get Patient Statement Results 
	$chkPGHD = $app->dbh->imw_query('
		SELECT 
			new_val_lbl as HealthInformation,
			DATE(reqDateTime) as Date,
			TIME(reqDateTime) as Time,
			if(is_approved="1","Approved","Pending") as Status 
		FROM 
			iportal_req_changes 
		WHERE 
			tb_name = "user_messages" AND 
			col_name = "PGHD" AND 
			pt_id='.$patientId.' AND 
			del_status = "0"
			'.$where.'
		ORDER BY id DESC
	');
	
	if($app->dbh->imw_num_rows($chkPGHD) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkPGHD)){
			$returnData[$row['Date']][] = $row;
		}
	}
	
	if(count($returnData) == 0) $returnData = 'No Record found';
	
	return json_encode($returnData);
});

/* Request / Update PGHD */
$router->post('/updatePGHD', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	
	/* print_r($router); */
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	$service->validateParam('healthInformation', 'Please provide valid input for Health Information.')->notNull();
	$healthInfo = (string)$request->__get('healthInformation');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	try
    {
		/*Parameters to be saved for this API Call*/
		$parameters = $app->saveParameters->pghd;
		$router->saveField($parameters);
    }
    catch (Exception $e)
    {
		$response->append($e->getMessage());
		$this->abort(503);
    }
    
	return json_encode('Request Saved. Data is pending for approval.');
});


/* Print Patient Statements */
$router->get('/getStatement', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = $stateData = array();
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	$service->validateParam('StatementId', 'Please provide valid Statement ID.')->notNull();
	$statementId = (string)$request->__get('StatementId');
	
	$multipleValues	= $app->multipleValues;	//Int. Lazy Multiple values service
	$statementId = $multipleValues($statementId, $service);
	
	$chkId = $app->dbh->imw_query('SELECT previous_statement_id,statement_data,statement_txt_data FROM previous_statement_detail WHERE previous_statement_id IN ('.$statementId.')');
	if($app->dbh->imw_num_rows($chkId) > 0){
		$debugging = true;$counter = 0;
		while($row = $app->dbh->imw_fetch_assoc($chkId)){
			//Creating PDF
			$pdfString	= $app->pdfString;	//Int. Lazy PDF service
			
			$b64Doc		= $pdfString(htmlspecialchars_decode($row['statement_data']));
			$stateData[$row['previous_statement_id']] = $b64Doc;
		}
	}else{
		$response->append('Please provide valid value for StatementId.');
		$router->abort(401);
	}
	
	if(count($stateData) == 0) $stateData = 'No Statements found';
	
	return json_encode($stateData);
});


/* Print Patient Education Material */
$router->get('/getEducationMaterial', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	$service->validateParam('MaterialId', 'Please provide valid Material ID.')->notNull();
	$materialId = (string)$request->__get('MaterialId');
	
	$multipleValues	= $app->multipleValues;	//Int. Lazy Multiple values service
	$materialId = $multipleValues($materialId, $service);
	
	$chkId = $app->dbh->imw_query('
		SELECT 
			doc_id,
			description,
			upload_doc_type,
			if(upload_doc_file_path != "",upload_doc_file_path ,scan_doc_file_path) as upload_doc_file_path
		FROM 
			document_patient_rel 
		WHERE 
			doc_id IN ('.$materialId.') AND p_id = '.$patientId.'
	');
	
	if($app->dbh->imw_num_rows($chkId) > 0){
		$debugging = true;$counter = 0;
		while($row = $app->dbh->imw_fetch_assoc($chkId)){
			$consent_data = $row['description'];
			
			//If content is there
			if($consent_data != ""){
				$inputVal = explode('<input',$consent_data);
				$consent_data = $inputVal[0];
				for($i=1;$i<count($inputVal);$i++){
					$pos = strpos($inputVal[$i],'value=\"');
					$str = substr($inputVal[$i],$pos+7);
					$pos1 = strpos($str,'\"');
					$inputVals = substr($str,0,$pos1);
					$pos2 = strpos($str,'>');
					$lastVal = substr($str,$pos2+1);
					$consent_data .= $inputVals.' '.$lastVal;
				}
				
				//Creating PDF
				$pdfString	= $app->pdfString;	//Int. Lazy PDF service
				$b64Doc		= $pdfString($consent_data);
				
			}elseif($row['upload_doc_type'] == "pdf" && $row['upload_doc_file_path'] != ""){
				$pracPath = data_path();
				$filePath = $pracPath.$row['upload_doc_file_path'];
				$b64Doc = base64_encode(file_get_contents($filePath));
			}
			
			$returnData[$row['doc_id']] = $b64Doc;
		}
	}else{
		$response->append('Please provide valid value for Material Id.');
		$router->abort(401);
	}
	
	if(count($returnData) == 0) $returnData = 'No Material found';
	
	return json_encode($returnData);
});

/* Get Pending Approvals */
$router->get('/getApprovalStatus', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	$where = $startDate = $endDate = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
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
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(reqDateTime) BETWEEN '".$startDate."' AND '".$endDate."' ";
	}
	
	//Get Patient Statement Results 
	$chkPGHD = $app->dbh->imw_query('
		SELECT 
			id as ID,
			tb_name as ReffId,
			title_msg as Title,
			col_lbl as ChangedField,
			old_val as OldVal,
			new_val as NewVal,
			action as ActionType,
			(
				CASE 
					WHEN is_approved = "1" THEN "Approved"
					WHEN is_approved = "2" THEN "Declined"
					ELSE "Pending for approval"
				END
			) AS Status,
			DATE(reqDateTime) as Date,
			TIME(reqDateTime) as Time,
			new_val_arr,
			col_pri_id
		FROM 
			iportal_req_changes 
		WHERE 
			pt_id = '.$patientId.' and 
			del_status = 0
			'.$where.'	
		ORDER BY id DESC
	');
	
	if($app->dbh->imw_num_rows($chkPGHD) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkPGHD)){
			$str = sha1(md5('reffId~~~~'));
			$encodeStr = base64_encode($row['ReffId'].'~~~~'.$row['col_pri_id']);
			$row['ReffId'] = $str.$encodeStr;
			
			//If ChangedField is empty that means its a new record
			if(empty($row['ChangedField'])) $row['NewVal'] = $row['new_val_arr'];
			unset($row['new_val_arr']);
			
			$returnData[$row['Date']][] = $row;
		}
	}
	
	if(count($returnData) == 0) $returnData = 'No Record found';
	
	return json_encode($returnData);
});

/* Update/Delete Pending Approvals */
$router->post('/updateApprovalStatus', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = array();
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	$service->validateParam('ApprovalId', 'Please provide valid Approval ID.')->notNull();
	$materialId = (string)$request->__get('ApprovalId');
	
	$multipleValues	= $app->multipleValues;	//Int. Lazy Multiple values service
	$ApprovalId = $multipleValues($materialId, $service);
	
	$chkQry = $app->dbh->imw_query('select id from iportal_req_changes WHERE id IN ('.$ApprovalId.') AND del_status = 0');
	if($app->dbh->imw_num_rows($chkQry) > 0){
		$chkId = $app->dbh->imw_query('UPDATE iportal_req_changes SET del_status = 1 WHERE id IN ('.$ApprovalId.')');
		if($chkId){
			$returnData = 'Approval list updated successfully.';
		}else{
			$response->append('Unable to process the request.');
			$router->abort(401);
		}
	}else{
		$response->append('Please provide valid value for ApprovalId.');
		$router->abort(401);
	}
	
	return json_encode($returnData);
});

/* Get Patient Signed Consent */
$router->get('/getPatientSignedConsent', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = $sigConsentArr = $sigPackageArr = array();
	$where = $startDate = $endDate = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
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
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(form_created_date) BETWEEN '".$startDate."' AND '".$endDate."' ";
	}
	
	//Type of result
	$resultType = 0;
	if($request->__isset('resultType') && trim($request->__get('resultType')) != ''){
		$service->validateParam('resultType', 'Please provide valid result type.')->notNull()->isInt();
		$resultType = $request->__get('resultType');
	}
	
	//Get Signed Consent Forms
	$chkSignedConsent = $app->dbh->imw_query("
		SELECT 
			form_information_id as ID,
			consent_form_name as ConsentName,
			DATE(form_created_date) as ConsentDate, 
			TIME(form_created_date) as ConsentTime 
		FROM 
			patient_consent_form_information 
		WHERE 
			patient_id = '".$patientId."' and 
			package_category_id = '0' and 
			movedToTrash = 0 
			".$where."
		ORDER BY 
			form_created_date DESC
	");
		
	if($app->dbh->imw_num_rows($chkSignedConsent) > 0){
		while($rowSig = $app->dbh->imw_fetch_assoc($chkSignedConsent)){
			$sigConsentArr[$rowSig['ConsentDate']][] = $rowSig;	
		}
	}	
	
	//Get Signed Packages
	$chkSignedPackage = $app->dbh->imw_query("
		SELECT 
			distinct DATE(form_created_date) as formCreatedDate, 
		from 
			patient_consent_form_information 
		where 
			patient_id = '".$patientId."' AND 
			movedToTrash = 0 AND 
			package_category_id != '0' 
			".$where."
		ORDER BY 
			form_created_date desc
	");
	
	if($app->dbh->imw_num_rows($chkSignedPackage) > 0){
		while($rowSigPack = $app->dbh->imw_fetch_assoc($chkSignedPackage)){
			//Getting Consent packages details
			$getPackages = $app->dbh->imw_query("
				SELECT 
					DISTINCT pcfi.package_category_id,cp.package_category_name 
				FROM 
					patient_consent_form_information pcfi 
					LEFT JOIN consent_package cp ON (cp.package_category_id = pcfi.package_category_id)
				WHERE 
					pcfi.patient_id='".$patientId."' 
					AND DATE(pcfi.form_created_date) = '".$rowSigPack['formCreatedDate']."' 
					AND pcfi.movedToTrash = '0' 
					AND pcfi.package_category_id!='0'
				ORDER BY pcfi.form_created_date desc
			");
			
			if($app->dbh->imw_num_rows($getPackages) > 0){
				while($rowPackages = $app->dbh->imw_fetch_assoc($getPackages)){
					//Get consent in packages
					$chkPackages = $app->dbh->imw_query("
						SELECT 
							form_information_id as ID,
							consent_form_name as ConsentName,
							DATE(form_created_date) as ConsentTime
						FROM 
							patient_consent_form_information 
						WHERE 
							patient_id='".$patientId."'
							AND DATE(form_created_date) = '".$rowSigPack['formCreatedDate']."' 
							AND movedToTrash = '0' 
							AND package_category_id!='0' 
							AND package_category_id='".$rowPackages['package_category_id']."' 
						ORDER BY form_created_date DESC 
					");
					
					if($app->dbh->imw_num_rows($chkPackages) > 0){
						while($rowSigPackages = $app->dbh->imw_fetch_assoc($chkPackages)){
							$sigPackageArr[$rowSigPack['formCreatedDate']][$rowPackages['package_category_name']][] = $rowSigPackages;
						}
					}
				}
			}
		}
	}
	
	if(count($sigConsentArr) == 0) $sigConsentArr = 'No consent found.';
	if(count($sigPackageArr) == 0) $sigPackageArr = 'No consent found.';
	
	switch(trim($resultType)){
		case 1:
			//Only return signed consent
			if(count($sigConsentArr) > 0)
				$returnData = array('SignedConsent' => $sigConsentArr);
		break;
		
		case 2:
			//Only return signed package consent
			if(count($sigPackageArr) > 0)
				$returnData = array('SignedPackageConsent' => $sigPackageArr);
		break;
		
		default:
			//Return Both Signed consent and signed packages consent
			$returnData = array('SignedConsent' => $sigConsentArr, 'SignedPackageConsent' => $sigPackageArr);
	}
	
	return json_encode($returnData);
});

/* Get Patient Consent */
$router->get('/getConsent', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = $ConsentData = array();
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	$service->validateParam('ConsentId', 'Please provide valid Consent ID.')->notNull();
	$ConsentId = (string)$request->__get('ConsentId');
	
	$multipleValues	= $app->multipleValues;	//Int. Lazy Multiple values service
	$ConsentId = $multipleValues($ConsentId, $service);
	
	$dataPath = data_path();
	
	$chkId = $app->dbh->imw_query('
		SELECT 
			ptcf.form_information_id as id,
			ptcf.consent_form_content_data as consentData,
			ptcf.package_category_id as consentPackageId,
			ptcf.consent_form_id as consentId,
			csf.cat_id as catId
		FROM 
			patient_consent_form_information ptcf
			LEFT JOIN consent_form csf ON (csf.consent_form_id = ptcf.consent_form_id)
		WHERE 
			form_information_id IN ('.$ConsentId.')');
	if($app->dbh->imw_num_rows($chkId) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkId)){
			$sigArr = array();
			
			$consentData = html_entity_decode($row['consentData']);	//consent_form_content_data
			$consentPackageId = $row['consentPackageId'];			//consent_package_category_id
			$consentCatId = $row['catId'];
			
			//Getting Signatures
			$chkSig = $app->dbh->imw_query("
				SELECT 
					signature_image_path,
					signature_count 
				FROM 
					consent_form_signature 
				WHERE 
					form_information_id = '".$row['id']."' and 
					patient_id = '".$patientId."'
				and consent_form_id = '".$row['consentId']."' and signature_status = 'Active' order by signature_count
			");
			
			if($app->dbh->imw_num_rows($chkSig) > 0){
				while($rowSig = $app->dbh->imw_fetch_assoc($chkSig)){
					$sigArr[] = $rowSig;
				}
			}
			
			//If data is not empty
			if(empty($consentData) == false){
				$consentFinal = '';
				
				//Replacing Values
				$replaceVariables	= $app->replaceVariables;	//Int. Lazy Replace Custom form variables with values
				$consentData = $replaceVariables($consentData, 'Input');
				
				//Replacing Signatures
				$sig_arr = explode('{SIGNATURE}',$consentData);
				if(count($sig_arr)>0){
					foreach($sig_arr as $key=> $sigVal){
						$content=$sig_arr[$key];
						if($arrSig[$key]['signature_count']){
							$imgName=explode("/",$arrSig[$key]['signature_image_path']);
							if(end($imgName)){
								$path="/".$GLOBALS['iDoc_dir']."/interface/SigPlus_images/".end($imgName);
								$sig_patient="<img src='".$path."'  width='150' height='73'>";
								$content.=$sig_patient;
							}
						}	
						$consentFinal.= $content;
					}
				}
				$consentData = $consentFinal;
				
				preg_match_all('/(src)=("[^"]*")/i',$consentData,$matches); 
				$arr_find_src = $arr_repl_src = array();
				foreach($matches[2] as $key){
					$file_Name=str_ireplace('"','',$key);
					$file_Name=end(explode("/",$file_Name));
					$arr_find_src[]=$file_Name;
					$arr_repl_src[]=rawurlencode($file_Name);
					
				}
				$consentData = str_ireplace($arr_find_src,$arr_repl_src,html_entity_decode(stripslashes($consentData)));
			
				//Creating PDF
				$pdfString	= $app->pdfString;	//Int. Lazy PDF service
			
				$b64Doc		= $pdfString($consentData);
				$returnData[$row['id']] = $b64Doc;
			}
		}
	}else{
		$response->append('Please provide valid value for ConsentId.');
		$router->abort(401);
	}
	
	if(count($returnData) == 0) $returnData = 'No Consents found';
	
	return json_encode($returnData);
});

/* Patient Access Logs */
$router->get('/getPatientAccessLogs', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = $accLogArr = $loginLogArr = array();
	$where = $startDate = $endDate = '';
	$resultType = 0;
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	if($request->__isset('resultType') && trim($request->__get('resultType')) !== '' ){
		$service->validateParam('resultType', 'Please provide valid resultType.')->notNull()->isInt();
		
		$resultType	= $app->dbh->imw_escape_string( $request->__get('resultType') );
	}
	
	//Time
	$startTime = $app->dbh->imw_escape_string($request->__get('startTime'));
	if($request->__isset('startTime') && empty($startTime) == false){
		$service->validateParam('startTime', 'Please provide valid start time format.')->notNull()->isTime();
		$startTime = $app->dbh->imw_escape_string( $request->__get('startTime') );
	}
	
	$endTime = $app->dbh->imw_escape_string($request->__get('endTime'));
	if($request->__isset('endTime') && empty($endTime) == false){
		$service->validateParam('startTime', 'Please provide valid start time.')->notNull()->isTime();
		
		$service->validateParam('endTime', 'Please provide valid end time format.')->notNull()->isTime();
		$endTime	= $app->dbh->imw_escape_string( $request->__get('endTime') );
	}
	
	if(empty($endTime) == true) $endTime = $startTime;
	
	if(empty($startTime) == false && empty($endTime) == false){
		$time_qry = '( TIME(logtime) BETWEEN "'.$startTime.'" AND "'.$endTime.'" )';
	}
	
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
	
	if(empty($startDate) == false && empty($endDate) == false){
		$where = " AND DATE(logtime) BETWEEN '".$startDate."' AND '".$endDate."' ";
	}
	if(empty($time_qry) == false) $where = $where.' AND ';
	
	
	//Get Access Log
	$chkQry = $app->dbh->imw_query("
		SELECT 
			u_action as Action,
			`desc` as Description,
			DATE(logtime) as LogDate,
			TIME(logtime) as LogTime
		FROM 
			pt_and_rp_logs 
		WHERE 
			patient_id = ".$patientId." AND 
			pt_rp_id = 0 
			".$where.$time_qry."
		ORDER BY 
			DATE(logtime), TIME(logtime) DESC
	");
	
	if($chkQry && $app->dbh->imw_num_rows($chkQry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkQry)){
			$tmpArr = array();
			$tmpArr['Time'] = $row['LogTime'];
			$tmpArr['Action'] = $row['Action'];
			$tmpArr['Description'] = $row['Description'];
			$accLogArr[$row['LogDate']][] = $tmpArr;
		}
	}
	
	
	//Get Login Hx Log
	$where = str_ireplace('logtime', 'logindatetime', $where);
	$time_qry = str_ireplace('logtime', 'logindatetime', $time_qry);
	$chkQry = $app->dbh->imw_query("
		SELECT 
			DATE(logindatetime) as Date, 
			TIME(logindatetime) as Time 
		FROM 
			patient_loginhistory 
		WHERE 
			patient_id = '".$patientId."' and 
			pt_rp_id = 0 
			".$where.$time_qry."
		order by id DESC
	");
	
	if($chkQry && $app->dbh->imw_num_rows($chkQry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($chkQry)){
			$tmpArr = array();
			$tmpArr['Time'] = $row['Time'];
			$loginLogArr[$row['Date']][] = $tmpArr;
		}
	}
	
	if(count($accLogArr) == 0) $accLogArr = 'No record found.';
	if(count($loginLogArr) == 0) $loginLogArr = 'No record found.';
	
	switch(trim($resultType)){
		case 1:
			//Only return access logs
			if(count($accLogArr) > 0)
				$returnData = array('AccessLog' => $accLogArr);
		break;
		
		case 2:
			//Only return login logs
			if(count($loginLogArr) > 0)
				$returnData = array('LoginLog' => $loginLogArr);
		break;
		
		default:
			//Return Both access logs and login logs
			$returnData = array('AccessLog' => $accLogArr, 'LoginLog' => $loginLogArr);
	}
	
	return json_encode($returnData);
	
	
});	

/* Return data string images */
$router->get('/getDataImages', function($request, $response, $service, $app) use($router, $converToString){
	$returnData = $pathArr = array();
	$phyId = $ptImgPath = '';
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	//Get Physician ID 
	$chkPhyQry = $app->dbh->imw_query('
		SELECT 
			patient_data.providerID as provider_id
		FROM 
			patient_data, users 
		WHERE 
			patient_data.id = "'.$patientId.'" and 
			users.id = patient_data.providerID and 
			users.delete_status!=1
	');
	if($chkPhyQry && $app->dbh->imw_num_rows($chkPhyQry) > 0){
		$rowPhy = $app->dbh->imw_fetch_assoc($chkPhyQry);
		$phyId = (isset($rowPhy['provider_id']) && empty($rowPhy['provider_id']) == false) ? $rowPhy['provider_id'] : '';
	}
	
	//Get Patient image path
	$chkPtImgQry = $app->dbh->imw_query('SELECT p_imagename FROM patient_data WHERE id = '.$patientId.'');
	
	if($chkPtImgQry && $app->dbh->imw_num_rows($chkPtImgQry) > 0){
		$rowPt = $app->dbh->imw_fetch_assoc($chkPtImgQry);
		$ptImgPath = (isset($rowPt['p_imagename']) && empty($rowPt['p_imagename']) == false) ? $rowPt['p_imagename'] : '';
	}
	
	//Physician Image
	if(empty($phyId) == false){
		$pathArr['PhysicianImg'] = data_path().'UserId_'.$phyId.'/profile_img/Provider_'.$phyId.'.jpg';
	}
	
	//Patient Image
	if(empty($ptImgPath) == false){
		$str = explode('/',$ptImgPath);
		array_shift($str);
		$pathArr['PatientImg'] = data_path().implode('/',$str);
	}
	
	if(count($pathArr) > 0){
		foreach($pathArr as $key => &$val){
			if(file_exists($val)){
				$tmpArr = array();
				
				$type = pathinfo($val, PATHINFO_EXTENSION);
				$data = file_get_contents($val);
				//$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
				$base64 = base64_encode($data);
				
				$tmpArr['Type'] = $type;
				$tmpArr['Data'] = $base64;
				$returnData[$key] = $tmpArr;
			}
		}
	}
	
	$responseArr = array('DataImages' => 'No Images Found');
	if(count($returnData) > 0) $responseArr = array('DataImages' => $returnData);
	
	return json_encode($responseArr);
});

/* Get Upload Patient documents */
$router->post('/uploadPtDocument', function($request, $response, $service, $app) use($router, $converToString){
	$catId = $catName = $folderId = $createUploadFolder = $createFolder = $createScFolder = $content = $path = $DBPath = $docId = '';
	
	$fileName = 'IMW_API_'.strtotime("now").'.pdf';
	$folderName = 'ROS';
	
	$createdDate = date('Y-m-d H:i');
	if($request->__isset('date') && trim($request->__get('date')) != ''){
		$service->validateParam('date', 'Please provide valid date.')->isDate();
		$createdDate = $request->__get('date');
	}
	
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= $request->__get('patientId');
	
	
	$service->validateParam('docData', 'Please provide a valid content to continue.')->notNull();
	$content	= $request->__get('docData');
	
	if(empty($content) == false){
		$content = base64_decode($content);
	}
	
	//Check if API folder category exists or not
	/* $chkCat = $app->dbh->imw_query(' SELECT cat_id, category_name FROM main_template_category WHERE delete_status = 0 AND LOWER(category_name) = "import-api" ');
	if($app->dbh->imw_num_rows($chkCat) > 0){
		$rowCat = $app->dbh->imw_fetch_assoc($chkCat);
		$catId = $rowCat['cat_id'];
		$catName = $rowCat['category_name'];
	}else{
		$insertCat = $app->dbh->imw_query('INSERT main_template_category SET category_name = "Import-API", template_name = "pt_docs", delete_status = 0');
		if($insertCat){
			$insertCat = $app->dbh->imw_insert_id();
			$chkCat = $app->dbh->imw_query(' SELECT cat_id, category_name FROM main_template_category WHERE delete_status = 0 AND cat_id = '.$insertCat.' ');
			if($app->dbh->imw_num_rows($chkCat) > 0){
				$rowCat = $app->dbh->imw_fetch_assoc($chkCat);
				$catId = $rowCat['cat_id'];
				$catName = $rowCat['category_name'];
			}
		}
	}
	echo $catId.'-----'.$catName; */
	
	//Setting Upload Path
	$uploadDir = data_path();
	
	//Get Category
	$chkCatQry = "SELECT folder_categories_id from ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE folder_name = '".$folderName."' ";
	$chkCatRes = $app->dbh->imw_query($chkCatQry) or die($app->dbh->imw_error());
	if($app->dbh->imw_num_rows($chkCatRes)>0) {
		$chkCatRow = $app->dbh->imw_fetch_assoc($chkCatRes);
		$folderId = $chkCatRow["folder_categories_id"];
	}else{ 
		$insCatQry = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".folder_categories SET folder_name = '".$folderName."', folder_status = 'active' ";
		$insCatRes = $app->dbh->imw_query($insCatQry)or die($app->dbh->imw_error());
		$folderId = $app->dbh->imw_insert_id();
	}
	
	$createFolder = $uploadDir."/PatientId_".$patientId;
	$createScFolder = $uploadDir."/PatientId_".$patientId."/Folder";
	$createUploadFolder = $uploadDir."/PatientId_".$patientId."/Folder/id_".$folderId;
	
	if(empty($folderId) == false && empty($patientId) == false ){
		if(is_dir($createFolder)){
			$folderMainStatus=1;
		}else{
			mkdir($createFolder);	
			$folderMainStatus = 1;
		}
		if(is_dir($createScFolder)){
			$subFolderStatus=1;
		}else{
			mkdir($createScFolder);			
			$subFolderStatus=1;
		}
		if(is_dir($createUploadFolder)){
			$scanUploadFolder=1;	
		}else{
			mkdir($createUploadFolder);
			$scanUploadFolder=1;		
		}
		
		if($folderMainStatus == 1 && $subFolderStatus == 1 && $scanUploadFolder == 1){
			$path = $createUploadFolder.'/'.$fileName;
			$DBPath = str_ireplace($uploadDir, '', $path);
		}
		
		if(empty($path) == false){
			$fileInfo = pathinfo($path);
			
			//If directory exists
			if(is_dir($fileInfo['dirname'])){
				
				//Create file
				$myfile = fopen($path, "w");
				if($myfile){
					fwrite($myfile, $content);
				}else{
					$response->append('Unable to write the request.');
					$router->abort(400);
				}
				fclose($myfile);
				
				//If file exists
				if(file_exists($path)){
					
					//Insert it in DB
					$chkDb = $app->dbh->imw_query("
						SELECT 
							scan_doc_id 
						FROM 
							".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
						WHERE  
							patient_id='".$patientId."' AND 
							doc_title='".addslashes($fileInfo['basename'])."' "
					);
					
					if($chkDb && $app->dbh->imw_num_rows($chkDb) == 0){
						$qryInsertScanDocs="INSERT INTO ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set 
											folder_categories_id='".$folderId."',patient_id='".$patientId."',
											doc_title='".addslashes($fileInfo['basename'])."',upload_date='".$createdDate."',
											doc_type='".$fileInfo['extension']."',doc_upload_type='upload', 
											pdf_url='".addslashes($DBPath)."',upload_operator_id='1',
											upload_docs_date='".$createdDate."',file_path='".addslashes($DBPath)."'";	
						$resInsertScanDocs = $app->dbh->imw_query($qryInsertScanDocs);
						$docId = $app->dbh->imw_insert_id();
					}	
				}
			}else{
				$response->append('Unable to handle the request.');
				$router->abort(400);
			}
		}else{
			$response->append('Server location undefined.');
			$router->abort(400);
		}
		
		$returnData = array('UploadDocument' => $docId);
		if(empty($docId)) $returnData = array('UploadDocument' => 'No document found');
		
		return json_encode($returnData);
	}
});

/* Appointment Custom Status */
$router->get('/getAppointmentStatus', function($request, $response, $service, $app) use($router, $converToString){
		$statType = '';
		$apptStatArr = $returnData = array();
		$allowedStatus = array(0, 1, 2);
		
		$colTypeArr = array(0 => 'Mandatory Status', 1 => 'System Status', 2 => 'Custom Status');
		$statusArr = array(0 => 'Active', 1 => 'Inactive');
		
		if($request->__isset('statusType') && $request->__get('statusType') != ''){
			$service->validateParam('statusType', 'Please provide a valid status type to continue.')->notNull()->isInt();
			$statType	= $request->__get('statusType');
			
			if(!in_array($statType, $allowedStatus)){
				$response->append('Please provide a valid status type to continue.');
				$router->abort(400);
			}
		}
	
		if(!in_array($statType, $allowedStatus) || empty($statType)) $statType = implode('", "', $allowedStatus);
		$chkQry = $app->dbh->imw_query('SELECT 
				id as StatusID,
				status_name as StatusName,
				alias as StatusAlias,
				status as Status,
				col_type as colType
			FROM schedule_status WHERE col_type IN ("'.$statType.'") ORDER BY id ASC'
		);
		
		if($chkQry && $app->dbh->imw_num_rows($chkQry) > 0){
			while($rowFetch = $app->dbh->imw_fetch_assoc($chkQry)){
				$rowFetch['Status'] = (isset($statusArr[$rowFetch['Status']])) ? $statusArr[$rowFetch['Status']] : '';
				$rowFetch['colType'] = (isset($colTypeArr[$rowFetch['colType']])) ? $colTypeArr[$rowFetch['colType']] : '';
				if(!is_array($apptStatArr[$rowFetch['colType']])) $apptStatArr[$rowFetch['colType']] = array();
				
				array_push($apptStatArr[$rowFetch['colType']], $rowFetch);
			}
		}
		
		ksort($apptStatArr);
		
		$returnData = $apptStatArr;
		if(count($apptStatArr) == 0) $returnData = array('No Status found');
		
		return json_encode($returnData);
});

/* Appointment Custom Status */
$router->get('/modifyAppointmentStatus', function($request, $response, $service, $app) use($router, $converToString){
	$statType = $statusId = $statusNm = $statusAlias = $msg = $qryStatus = '';
	$enableType = 0;
	$allowedStatus = array(1, 2);
	$tmpArr = array();
	
	//Validating Variables
	
	if($request->__isset('statusId') && trim($request->__get('statusId')) != ''){
		$service->validateParam('statusId', 'Please provide a valid status id to continue.')->notNull()->isInt()->isApptStat($app);
		$statusId	= $request->__get('statusId');
	}
	
	//if($request->__isset('statusType') && trim($request->__get('statusType')) != ''){
		$service->validateParam('statusType', 'Please provide a valid status type to continue.')->notNull()->isInt();
		$statType	= $request->__get('statusType');
		
		if(!in_array($statType, $allowedStatus)){
			$response->append('Please provide a valid status type to continue.');
			$router->abort(400);
		}
	//}
	
	if($request->__isset('statusNm') && trim($request->__get('statusNm')) != '' && trim($request->__get('statusId')) == ''){
		$service->validateParam('statusNm', 'Please provide a unique status name to continue.')->notNull()->isUniqueApptStatNm($app);
		$statusNm	= $request->__get('statusNm');
	}
	
	if($request->__isset('enableType') && trim($request->__get('enableType')) != ''){
		$service->validateParam('enableType', 'Please provide a valid enable status to continue.')->isInt();
		
		if(!in_array($request->__get('enableType'), array(1, 2))){
			$response->append('Please provide a valid enable status to continue.');
			$router->abort(400);
		}
		
		$enableType	= $request->__get('enableType');
		
		if($enableType == 2) $enableType = 1;
		else $enableType = 0;
	}
	
	$statusAlias	= $request->__get('statusAlias');
	
	//If Empty than new record only for Custom status
	if(empty($statusId) && $statType == 2){
		if(empty($statusNm) == true){
			$response->append('Please provide a unique status name to continue.');
			$router->abort(400);
		}
		
		if(empty($statusNm) == false) $tmpArr['status_name'] = $statusNm;
		if(empty($statusAlias) == false) $tmpArr['alias'] = $statusAlias;
		if($enableType !== '') $tmpArr['status'] = $enableType;
		if(empty($statType) == false) $tmpArr['col_type'] = $statType;
		$tmpArr['added_datetime'] = date('Y-m-d H:i a');
		
		$qryStatus = AddRecords($tmpArr, 'schedule_status');
		
		if($qryStatus && empty($qryStatus) == false) $msg = 'Appointment Status added Successfully';
		else $msg = 'Unable to add appointment status';
		
	}elseif ( empty($statusId) == false && in_array($statType, $allowedStatus) ) {
		//Check DB first
		$sqlQry = imw_query('SELECT * FROM schedule_status WHERE id = '.$statusId.' AND col_type = '.$statType.' ');		
		if($sqlQry && imw_num_rows($sqlQry) > 0){
			switch($statType){
				case 1:
					if($enableType !== '') $tmpArr['status'] = $enableType;
				break;
				
				case 2:
					if($enableType !== '') $tmpArr['status'] = $enableType;
					if(empty($statusNm) == false) $tmpArr['status_name'] = $statusNm;
					if(empty($statusAlias) == false) $tmpArr['alias'] = $statusAlias;
				break;
			}
			
			if(count($tmpArr) > 0){
				if(empty($statType) == false) $tmpArr['col_type'] = $statType;
				$tmpArr['modify_datetime'] = date('Y-m-d H:i a');
			}
			
			$qryStatus = UpdateRecords($statusId,'id',$tmpArr,'schedule_status');
			
			if($qryStatus && empty($qryStatus) == false) $msg = 'Appointment Status updated Successfully';
			else $msg = 'Unable to update appointment status';
		}else{
			$response->append('Invalid Status ID or Status Type.');
			$router->abort(400);
		}
	}else{
		$response->append('Invalid Values provided.');
		$router->abort(400);
	}
	
	return json_encode($msg);
});

/*Clinical Data*/
$router->with('/getClinicalData', __DIR__ .'/clinicalRoutes.php');
$router->with('/updateClinicalData', __DIR__ .'/clinicalRoutes.php');

/* Generate CCDA for the requested patient */
$router->with('/getCCDA', __DIR__ .'/ccdaGenerate.php');

/* Book appointment for the requested patient */
$router->with('/patientAppointment', __DIR__ .'/manageAppointment.php');

/* Contact lens information for the requested patient */
$router->with('/patientContactLens', __DIR__ .'/manageContactLens.php');

/* Optical information */
$router->with('/optical', __DIR__ .'/opticalRoutes.php');


/* Review of System data for the requested patient */
$router->with('/getROS', __DIR__ .'/reviewSystem.php');
$response = $router->dispatch();

?>