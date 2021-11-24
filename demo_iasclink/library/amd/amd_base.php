<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

/*
 * File: amd_base.php
 * Coded in PHP7
 * Purpose: Base Class for Advanced MD API Calls
 * Access Type: Include
*/
include_once(dirname(__FILE__).'/amd_exceptions.php');

abstract class amd_base
{
	/*Hold's API credentials*/
	protected $appURL, $webServerURL, $userContext;
	private static $apiUsername, $apiPassword, $apiOfficekey, $serverEndPoint, $appName;
	
	/*Writable directory's path*/
	private static $writePath, $tokenFile, $cookieFile;
	
	/*API call parameters container*/
	private $parameters;
	
	/*Hold current error reporting/display status for restoring on the end of object*/
	private $errorReporting, $sc_db_name, $errorDisplay;
	
	public $hc_sex = array('M' => 'Male', 'F' => 'Female', 'U' => 'Unknown');
	public $hc_ins = array('1'=>'Primary', '2'=>'SECONDARY', '3'=>'TERTIARY', '4'=>'WORKERS', '5'=>'AUTO', '6'=>'DME' );
	public $hc_marital = array('1'=>'single', '2'=>'married', '3'=>'divorced', '4'=>'separated', '5'=>'widowed', '6'=>'UNKNOWN');
	public $hc_rel = array('1'=>'self', '2'=>'Spouse', '3'=>'Child:No Fin Responsibility', '4'=>'Other');
	public $api_log;
	
	protected function __construct()
	{
		$this->errorReporting = error_reporting();
		$this->errorDisplay = ini_get( 'display_errors' );
		
		error_reporting();
		ini_set( 'display_errors', false);
		//set_error_handler( 'amdErrorHandler' );
		
		$this->writePath = dirname(__FILE__).'/data/';
		$this->tokenFile = 'token_'.date('Ymd').'.json';
		$this->cookieFile = 'cookies.txt';
		$this->serverEndPoint = '/xmlrpc/processrequest.aspx';
		
		$this->init();
		
		/*New Batch will be created once a day.*/
		$this->crateBatch();
		
		/*Update Anesthesia Fee Details Once a Day*/
		$this->updateAnesthesiaFeeDetails();
	}
	
	/*
	 * Initialize/Login API
	 **/
	protected function init()
	{
		$this->amd_credentials();
		
		if( $this->verify_login() ) return;
		
		$this->amd_login();	
		
	}
	
	protected function verify_login()
	{
		$file = $this->writePath . $this->tokenFile;
		//$cookie_file = $this->writePath . $this->cookieFile;
		
		if( !file_exists($file) ) return false;
		
		$data = file_get_contents($file);
		$data = json_decode($data);
		
		if( !$data->Results->usercontext ) return false;
		
		$this->appURL = $this->webServerURL = $data->Results->usercontext->{'@webserver'} . $this->serverEndPoint;
		$this->userContext = $data->Results->usercontext;
		
		return true;
	}
	
	protected function amd_login()
	{
		
		$this->parameters = array('ppmdmsg' => array( "@action" => "login",
																									"@class" => "login",
																									"@msgtime" => date('m/d/Y h:i:s A'),
																									"@username" => $this->apiUsername,
																									"@psw" => $this->apiPassword,
																									"@officecode" => $this->apiOfficekey,
																									"@appname" => $this->appName ));
		
		$data = self::CURL($this->appURL, $this->parameters);
		//self::print_resp($data);
		$data = json_decode($data);
		//file_put_contents($this->writePath.'debugtoken.txt','resp_imw-: '.$dg, FILE_APPEND);
		if( $data->PPMDResults->Results->{'@success'} )
		{
			/* Write Login Response in file */
			$tokenData = $data->PPMDResults;
			$tokenData->timestamp = date('Y-m-d h:i:s A');
			
			$this->appURL = $this->webServerURL = $tokenData->Results->usercontext->{'@webserver'} . $this->serverEndPoint;
			$this->userContext = $tokenData->Results->usercontext;
			
			$tokenData = json_encode( $tokenData );
			
			file_put_contents( $this->writePath . $this->tokenFile, $tokenData );
			
			//file_put_contents( $this->writePath . $this->cookieFile, 'usercontext='.$this->userContext );
		}
		else
		{
			if(isset($data->PPMDResults->Error)) {
				$this->appURL = $data->PPMDResults->Results->usercontext->{'@webserver'} . $this->serverEndPoint;
				$this->amd_login();	
			}
		}
	}
	
	/* Load API Login credentials */
	protected function amd_credentials()
	{
		$this->appName = 'imwemr';
		$this->appURL = 'https://partnerlogin.domain.com/practicemanager/xmlrpc/processrequest.aspx';
		
		// SandBox
		// $this->apiUsername = 'IMWFUL';
		// $this->apiPassword = 'hjp()567@';
		// $this->apiOfficekey = '991370';
	
		if(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'SCC_INDIANA') {
			// SCC Indiana
			$this->apiUsername = 'IME0524';
			$this->apiPassword = 'kWw_^FKe8#';
			$this->apiOfficekey = '136252';
		} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'INTE') {
			// Integrity
			$this->apiUsername = 'IME0917';
			$this->apiPassword = 'Nc9r9RmnBY';
			$this->apiOfficekey = '991115';
		} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'TOMOKA') {
			// Tomoka Eye
			$this->apiUsername = 'ODBC0723';
			$this->apiPassword = 'wkUaJg8EaS';
			$this->apiOfficekey = '142490';
		} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'NWES') {
			// North West Credentials
			$this->apiUsername = 'MED427';
			$this->apiPassword = 'hjp()567';
			$this->apiOfficekey = '134546';
		} elseif(isset($GLOBALS['LOCAL_SERVER']) && $GLOBALS['LOCAL_SERVER'] == 'INTEGRITY_DEMO') {
			// North West Credentials
			$this->apiUsername = 'INTEGRITY';
			$this->apiPassword = 'givenjanA1!';
			$this->apiOfficekey = '995460';
		}
	}
	
	/* Common CURL Calling Function */
	protected static function CURL($url, $params = array())
	{
		$url = trim($url);
		if( !$url) die('unable to process');
		
		/*Parameters are sent in json encode form*/
		$params = json_encode($params);
		/*
		file_put_contents('data/debugcurl.txt', '-URL-', FILE_APPEND);
		file_put_contents('data/debugcurl.txt', $url, FILE_APPEND);
		file_put_contents('data/debugcurl.txt', '-PARAMS-', FILE_APPEND);
		file_put_contents('data/debugcurl.txt', print_r($params, true), FILE_APPEND);
		*/
		$header = array();
		$header[] = 'Content-Type: application/json'; /*Data type of the response*/
		$header[] = 'Accept: application/json'; /*Data Accept type of the response*/
		$header[] = 'Content-Length: ' . strlen($params);/*Length of content in request*/
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*Return the response*/
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, false); 
		curl_setopt($ch, CURLOPT_HEADER, false); /*Include header in Output/Response*/
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		
		/*$allowCookies = true;
		if( $allowCookies )
		{
			//curl_setopt($ch, CURLOPT_COOKIE, 'token='.self::$token.'; ');
			curl_setopt($ch, CURLOPT_COOKIEJAR, 'data/cookies.txt');
			curl_setopt($ch, CURLOPT_COOKIEFILE, 'data/cookies.txt');

		}*/
		
		$data = curl_exec($ch); 
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		curl_close($ch);
		/*
		file_put_contents('data/debugcurl.txt', '-DATA-', FILE_APPEND);
		file_put_contents('data/debugcurl.txt', print_r($data, true), FILE_APPEND);
		file_put_contents('data/debugcurl.txt', 'Response Code: '.$response_code, FILE_APPEND);
		*/
		return $data;
	}
	
	protected static function print_resp($data)
	{
		$data = json_decode($data);
		echo '<pre>';print_r($data);echo '</pre>';
	}
	
	public function trim_arr(&$data)
	{
		foreach($data as $key => $val)
		{
			$data[$key] = (is_array($val)) ? $this->trim_arr($val) : trim($val);
		}
		return $data;
	}
	
	/*Restore error reporting/display status & restore error handler*/
	public function __destruct()
	{
		error_reporting( $this->errorReporting );
		ini_set('display_errors', $this->errorDisplay);
		restore_error_handler();
	}
	
	/* function to get provider id */
	public function get_provider($pro_name,$ex_pro_id)
	{
		if($ex_pro_id!='')
		{
			$strUQry = "SELECT usersId, fname, lname, mname FROM users WHERE FIND_IN_SET('".$ex_pro_id."',amd_user_id) AND LOWER(deleteStatus) != 'yes'";
			$rsUData = imw_query($strUQry);
		}
		if(imw_num_rows($rsUData)==0)
		{
			list($lname,$fname)=explode(',',$pro_name);
			list($fname, $mname)=explode(' ',$fname); 
			//search by name
			$strUQry = "SELECT usersId, fname, lname, mname FROM users WHERE LOWER(fname) = '".trim(strtolower($fname))."'
						AND LOWER(lname) = '".trim(strtolower($lname))."'
						AND LOWER(deleteStatus) != 'yes'";
			$rsUData = imw_query($strUQry);
		}
		
		if(imw_num_rows($rsUData) >= 1){
			$arrUProviderId = imw_fetch_assoc($rsUData);
			$intUProviderId = $arrUProviderId['usersId'];
			
		}
		return $intUProviderId;
	}
	
	
	protected function get_operator($db){
		if($db=='scemr')
			$q = "SELECT usersId AS 'id', loginName AS 'username' FROM users WHERE LOWER(fname) = 'AMD' AND LOWER(lname) = 'AMD' LIMIT 0,1";
		else
			$q = "SELECT id,username FROM users WHERE LOWER(fname) = 'AMD' AND LOWER(lname) = 'AMD' LIMIT 0,1";
			
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			 $this->operator_id = $rs['id'];
			 $this->operator_username = $rs['username'];
		}else{
			return false;
		}
	}
	
	protected function get_facility($ext_fac_id)
	{
		if(!trim($ext_fac_id))return false;
		$strQry = "SELECT fac_id AS 'id' FROM facility_tbl WHERE FIND_IN_SET('".trim($ext_fac_id)."',external_id) ORDER BY fac_id LIMIT 0,1";
		$rsData = imw_query($strQry);
		if($rsData && imw_num_rows($rsData) == 1){
			//$response .= "Facility with External ID ".$intAthenaFacilityId." Found."."\n";
			$arrFacilityId = imw_fetch_array($rsData, imw_ASSOC);
			$intFacilityId = $arrFacilityId['id'];
		}else{
			return false;
		}
		return $intFacilityId;
	}
	
	protected function get_procedure($visit_type){
		//checking procedure if not added adding it
		
		$strPQry = "SELECT procedureId FROM procedures WHERE LOWER(name) = '".addslashes(strtolower(trim($visit_type)))."' AND LOWER(`del_status`)!= 'yes'";
		$rsPData = imw_query($strPQry);
		
		if(imw_num_rows($rsPData) > 0){
			$arrProcId = imw_fetch_array($rsPData, imw_ASSOC);
			$intProcId = $arrProcId['procedureId'];
		}else{

			// check the visit type with procedure alias
			$strPAQry = "SELECT procedureId FROM procedures WHERE LOWER(procedureAlias) = '".addslashes(strtolower(trim($visit_type)))."' AND LOWER(`del_status`)!= 'yes'";
			$rsPAData = imw_query($strPAQry);
			
			if(imw_num_rows($rsPAData) > 0){
				$arrProcId = imw_fetch_array($rsPAData, imw_ASSOC);
				$intProcId = $arrProcId['procedureId'];
			}else{

			$strPQry = "SELECT procedureId FROM procedures WHERE LOWER(name) = 'import' AND LOWER(`del_status`)!= 'yes'";
			$rsPData = imw_query($strPQry);
			if(imw_num_rows($rsPData) > 0){
				$arrProcId = imw_fetch_array($rsPData, imw_ASSOC);
				$intProcId = $arrProcId['procedureId'];
			}
			else
			{
				/*Fetch Procedure category Id*/
			 	$sqlProcCat = 'SELECT `proceduresCategoryId` FROM `procedurescategory` WHERE  LOWER(`name`)="procedure" AND LOWER(`del_status`)!="yes"';
				$sqlProcCat = imw_query($sqlProcCat);
				
				$procCategoryId = false;
				if($sqlProcCat && imw_num_rows($sqlProcCat)>0){
					$procCategoryId = imw_fetch_assoc($sqlProcCat);
					$procCategoryId = $procCategoryId['proceduresCategoryId'];
				}
				
				if($procCategoryId){
				 	$sqlProcAdd = 'INSERT INTO `procedures` SET `name`="Import", `catId`='.$procCategoryId;
					imw_query($sqlProcAdd);
					$intProcId = imw_insert_id();
				}	
			}

			}
		}
		return $intProcId.'~:~'.addslashes(trim($visit_type));
	}
	
	protected function get_sync_time()
	{
		
		//$syncFile = self::$writePath.'visit_sync.txt';
		$syncFile = 'data/visit_sync.txt';
		if(file_exists($syncFile) )
		{
			return file_get_contents($syncFile);	
		}else return '';
	}
	
	
	protected function set_sync_time($sync_date_time)
	{
		
		//$syncFile = self::$writePath.'visit_sync.txt';
		$syncFile = 'data/visit_sync.txt';
		file_put_contents($syncFile,$sync_date_time);	
		
	}
	
	
	protected function write_log($log)
	{
		//$logFile = self::$writePath.'log_'.date('Ymd').'.txt';
		$logFile = 'data/log_'.date('Ymd').'.txt';
		file_put_contents($logFile,$log,FILE_APPEND);	
	}
	
	/*Create New Batch for Posting Charges in Advanced MD*/
	private function crateBatch()
	{
		/*Check if batch alerady exists*/
		$sql = "SELECT `batch_id` FROM `amd_batch_log` WHERE `creating_date`='".date("Y-m-d")."'";
		$resp = imw_query($sql);
		
		if( $resp && imw_num_rows($resp)>0 )
			return true;
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "newbatch",
													"@class" => "batches",
													"@msgtime" => date('m/d/Y h:i:s A'),
													)
								  );
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		
		if(isset($result->PPMDResults->Error->Fault))
			throw new amdException( 'API Error', "Unable to careate batch in Advanced MD" );
		
		$result = $result->PPMDResults->Results->batchlist->batch;
		$result = (is_array($result))?$result[0]:$result;
		
		$sql = "INSERT INTO `amd_batch_log` SET `batch_id`='".$result->{'@id'}."', `creating_date`='".date("Y-m-d")."', `batch_data`='".json_encode($result)."'";
		imw_query($sql);
	}
	
	/*Get Charge Details - Mainly used for Anesthesia CPT Codes*/
	private function getChargeDetails( $procId, $pos = '24', $tos = '07' )
	{
		$procId = trim($procId);
		
		if( $procId == '' )
			throw new amdException( 'Call Error', "CPT Code not supplied for geeting fee detail." );
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "getfees",
													"@class" => "chargeentry",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@chargeschedid" => "feesch21092",
													"@dos" => "06/23/2017",
													"proccode" => array(
																		 "@id" => $procId,
																		 "@pos" => $pos,
																		 "@tos" => $tos
																		 )
													)
								  );
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		if(isset($result->PPMDResults->Error->Fault))
			throw new amdException( 'API Error', $result->PPMDResults->Error->Fault->detail->description );
		elseif( !isset($result->PPMDResults->Results->proccodelist->proccode) )
			throw new amdException( 'API Error', "Unable to locate Charge : ".$procId." in Advanced MD." );
		
		return $result->PPMDResults->Results->proccodelist->proccode;
	}
	
	/*Search Procedure Code in Advanced MD*/
	public function searchProcCode($procCode=''){
		
		$procCode = trim($procCode);
		
		if( $procCode == '' )
			throw new amdException( 'Call Error', "Blank Procedure Code Supplied." );
		
		$this->parameters = array( 'ppmdmsg' => array(
													"usercontext" => $this->userContext,
													"@nocookie" => '1',
													"@action" => "lookupproccode",
													"@class" => "api",
													"@msgtime" => date('m/d/Y h:i:s A'),
													"@code" => (string)$procCode
													)
								  );
		
		$result = self::CURL($this->appURL, $this->parameters);
		$result = json_decode($result);
		
		if(isset($result->PPMDResults->Error))
			throw new amdException( 'API Error', $result->PPMDResults->Error->Fault->detail->description );
		elseif( !isset($result->PPMDResults->Results->proccodelist->proccode) )
			throw new amdException( 'API Error', "No result returned for Procedure Code: ".$procCode." from Advanced MD." );
		
		return $result->PPMDResults->Results->proccodelist->proccode;
	}
	
	/*Get CPT Code Fee Deatails*/
	public function updateAnesthesiaFeeDetails()
	{
		/*Check if Data anlready updated for the Day*/
		$syncLogFile = $this->writePath.'/anesChargesSyncLog.txt';
		if( file_exists($syncLogFile) )
		{
			$oldDate = trim(file_get_contents($syncLogFile));
			$currentDate = date('Ymd', time());;
			
			if( $oldDate!='' && $currentDate == $oldDate )
				return true;
		}
		
		$amdProcIds = array();
		$procedureCodes = array();
		/*List All Anesthesia CPT Codes*/
		$sql = "SELECT `pc`.`codePractice`, `am`.`amd_id`, `am`.`id` FROM `procedures` `pc` LEFT JOIN `amd_codes` `am` ON(`pc`.`codePractice` = `am`.`code` AND `am`.`code_type`=1) WHERE `pc`.`catId`=21 AND LOWER(`pc`.`del_status`)!='yes' AND `pc`.`codePractice`!=''";
		$resp = imw_query($sql);
		
		if( $resp && imw_num_rows($resp) > 0 )
		{
			while( $row = imw_fetch_assoc($resp) ){
				if( is_null($row['amd_id']) )
					array_push($procedureCodes, $row['codePractice']);
				else
				{
					$amdProcIds[$row['amd_id']] = $row['id'];
				}
			}
		}
		
		/*Get AMD ID of Procedure code if it is missing*/
		foreach( $procedureCodes as $procCode )
		{
			try{
				/*Get Data from Advanced MD*/
				$amdProcCode = $this->searchProcCode($procCode);
				$code = (is_object($amdProcCode))?$amdProcCode:array_pop($amdProcCode);
				do{
					$sqlAdd = "INSERT INTO `amd_codes` SET `code`='".$code->{'@code'}."', `amd_id`='".$code->{'@id'}."', `code_type`='1', `amd_object`='".json_encode($code)."'";
					imw_query($sqlAdd);
					
					if( $procCode == $code->{'@code'} )
						$amdProcIds[$code->{'@id'}] = imw_insert_id();
					
				}while( is_array($amdProcCode) && $code = array_pop($amdProcCode) );
			}
			catch(amdException $e)
			{}
		}
		
		/*Get CPT Fee Detail from Advanced MD and save in IMW*/
		foreach( $amdProcIds as $procCode=>$uid )
		{
			try{
				$chargeData = $this->getChargeDetails($procCode);
				/*Update Data in DB*/
				if( isset($chargeData->{'@id'}) && $chargeData->{'@id'} == $procCode )
				{
					$cptFee = (float)$chargeData->{'@fee'};
					$cptAllowed = (float)$chargeData->{'@allowable'};
					$cptUnit = (float)$chargeData->{'@units'};
					
					$sql = "UPDATE `amd_codes` SET `amd_fee`='".$cptFee."', `amd_allowed`='".$cptAllowed."', `amd_units`='".$cptUnit."' WHERE `id`='".$uid."'";
					imw_query($sql);
				}
			}
			catch(amdException $e)
			{}
		}
		file_put_contents($syncLogFile, date('Ymd'));
	}
}
