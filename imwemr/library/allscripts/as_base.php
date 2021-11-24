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
*/

include_once(dirname(__FILE__).'/as_exceptions.php');

abstract class as_base
{
	
	/*Unity API token*/
	private $token;
	
	/*Hold's API credentials*/
	private static $appName;
	private static $appURL;
	private static $apiUsername;
	private static $apiPassword;
	private static $ehrUsername;
	private static $ehrPassword;
	
	//private static $tokenFile;
	
	/*Writable directory's path*/
	private static $writePath;
	
	/*API call parameters container*/
	private $parameters;
	
	/*Hold current error reporting/display status for restoring on the end of object*/
	private $errorReporting;
	private $errorDisplay;
	
	protected function __construct($dailyCallsFlag=true)
	{
		
		$this->errorReporting = error_reporting();
		$this->errorDisplay = ini_get( 'display_errors' );
		
		/*error_reporting(E_ALL);
		ini_set( 'display_errors', true);
		set_error_handler( 'asErrorHandler' );*/
		
		$this->createASDataDir();
		//$this->tokenFile = $this->writePath.'/token.json';
		
		$this->loadToken();
		
		if($dailyCallsFlag)
		{
			$this->getUserAuthentication();
			$this->triggerDailyCalls();
		}
	}

	private function resetParameters()
	{
		$this->parameters = array("Action" => "", "AppUserID" => "", "Appname" => "", "PatientID" => "", "Token" => "", "Parameter1" => "", "Parameter2" => "", "Parameter3" => "", "Parameter4" => "", "Parameter5" => "", "Parameter6" => "", "Data");
	}
	
	/*
	 * Check if directory writing as communication data. Create if does not exists
	 */
	private function createASDataDir()
	{
		//$dirPath = $GLOBALS['incdir'].'/main/uploaddir/as_data';
		$dirPath = data_path().'as_data';
		
		if( !is_dir($dirPath) ){
			mkdir( $dirPath, 0755, true );
			chown( $dirPath, 'apache' );
		}
		
		$this->writePath = $dirPath;
	}

	/**
	 * Trigger "GetUserAuthentication" API call
	 * It is mandatory to be triggered if the user is directly logging in to imwemr application and is not coming from iDoc launch operation.
	 * It will triggered right after token generation.
	 * It basically bind the TW toke with the user details for further processing.
	 */
	private function getUserAuthentication()
	{
		/* Stop function execution if User is already authenticated for the session */
		if( array_key_exists('as_user_authenticated', $_SESSION) && $_SESSION['as_user_authenticated'] === true )
		{
			return;
		}

		/**
		 * Get TW Generic username and password
		 */
		$sql = "SELECT 
					`ehr_username`,
					`ehr_password`
				FROM
					`as_credentials`
				WHERE
					`id`=1";
		$resp = imw_query($sql);

		if( $resp && imw_num_rows($resp) > 0 )
		{
			$resp = imw_fetch_assoc($resp);
		}
		
		if(
			!array_key_exists('ehr_username', $resp) ||
			!array_key_exists('ehr_password', $resp) ||
			empty($resp['ehr_username']) ||
			empty($resp['ehr_password'])
		)
		{
			throw new asException( 'Data Error', 'Unable to retrieve TW Generic User details.' );
		}

		/** Make API call */
		$this->setEhrUserID($resp['ehr_username']);
		$this->prepareParameters( 'GetUserAuthentication', array($resp['ehr_password']) );
		$response = $this->makeCall( true );

		/** Check response - Stop execution and invalidate is user is not validated from Touch Works */
		if( strtolower($response->ValidUser) !== 'yes' )
		{
			$_SESSION['as_user_authenticated'] = false;

			throw new asException( 'Data Error', 'Unable to Authenticate TW Generic User Account. Please check the details' );
		}

		$_SESSION['as_user_authenticated'] = true;
	}
	
	/*
	 * Initiate Calls that require to be called once a day.
	 */
	public function triggerDailyCalls()
	{
		/*
		 * GetServerInfo Call
		*/
		/*Check last call time form log file*/
		$serverInfoLog = $this->writePath.'/serverInfoLog.txt';
		$triggerServerInfo = true;
		
		if( file_exists($serverInfoLog) )
		{
			$serverInfoLastLog = (int)file_get_contents($serverInfoLog);
			if( $serverInfoLastLog == (int)date('Ymd') )
				$triggerServerInfo = false;
		}
		
		if( $triggerServerInfo )
		{
			$this->prepareParameters( 'GetServerInfo' );
			$serverInfoData = $this->makeCall( true );
			
			/*Log Data*/
			$sql = "INSERT INTO `as_server_info_log` SET
					`timestamp` = '".date('Y-m-d- H:i:s')."',
					`info_data` = '".json_encode($serverInfoData)."',
					`license_key` = '".$serverInfoData->LicenseKey."',
					`user_id` = '".$_SESSION['authUserID']."',
					`facility_id` = '".$_SESSION['login_facility']."'";
			imw_query($sql);
			
			/*Log Call time in text file for quick access*/
			file_put_contents($serverInfoLog, date('Ymd'));
		}
	}
	
	/*
	 * Check if Token values exists in session
	 */
	private function check_session_token()
	{
		return ( isset($_SESSION['as_token']) && trim($_SESSION['as_token']) != '' && isset($_SESSION['as_token_time']) && trim($_SESSION['as_token_time']) != '' );
	}
	
	/*
	* Get Unity API Token.
	* Return stored Unity API token. Generate new if previous is expired.
	**/
	private function loadToken()
	{
		$this->loadCredentials();
		
		/*Check if Toke file exists* /
		$file = $this->tokenFile;*/
		
		$token = '';
		if( $this->check_session_token() )
		{
			/*$tokenData = json_decode( file_get_contents( $file ) );*/
			
			/*Token Saved/Last Used time*/
			$tokenTime = $_SESSION['as_token_time'];
			
			/*Current Time for comparison*/
			$cTime = time();
			
			/*Time difference in minutes*/
			$interval = $cTime - $tokenTime;
			$minutes = round( $interval / 60 );
			
			/*Generate new token if the existing one is near to expiry, else return it*/
			if ( $minutes < 18 )
				$token = $_SESSION['as_token'];
		}
		
		if ( $token== '' )
			$token = $this->generateToken();
		
		$token = trim( $token );
		
		/*Throw exception and stock execution if unable to Get token*/
		if( $token == '' )
			throw new asException( 'API Error', 'Token not generated' );
		
		$this->token = $token;
	}
	
	/*
	* Get Unity API Credentials.
	* Return stored Unity API Credentials from database.
	**/
	private function loadCredentials()
	{
		$sql = 'SELECT `appname`, `username`, `password`, `url`, `ubq_appname`, `ubq_username`, `ubq_password`, `ubq_url`, `ubq_status` FROM `as_credentials` WHERE `id`=1';
		$resp = imw_query($sql);
		if( $resp && imw_num_rows( $resp ) )
		{
			$credsData = imw_fetch_assoc( $resp );
			$ubqStatus = (bool)$credsData['ubq_status'];
			
			$creds = array();
			
			if( $ubqStatus )
			{
				$creds['appname'] = $credsData['ubq_appname'];
				$creds['url'] = $credsData['ubq_url'];
				$creds['username'] = $credsData['ubq_username'];
				$creds['password'] = $credsData['ubq_password'];
			}
			else
			{
				$creds['appname'] = $credsData['appname'];
				$creds['url'] = $credsData['url'];
				$creds['username'] = $credsData['username'];
				$creds['password'] = $credsData['password'];
			}
			
			/*All details are required*/
			foreach( $creds as $cVal )
			{
				if( trim($cVal) == '' )
					throw new asException('Call Error', 'Missiong Unity API credential. Please check.');
			}
			
			$this->appName		= $creds['appname'];
			$this->appURL		= urldecode( $creds['url'] );
			$this->apiUsername	= $creds['username'];
			$this->apiPassword	= $creds['password'];
			$this->ehrUsername	= (isset($_SESSION['as_user_id']) && $_SESSION['as_user_id'] !='')?$_SESSION['as_user_id']:'';
			/*$this->ehrPassword	= (isset($_SESSION['as_user_pass']) && $_SESSION['as_user_pass'] !='')?$_SESSION['as_user_pass']:'';*/
			
			/*Specify allscript's return type*/
			$this->appURL .= ( ( substr( $this->appURL, -1 ) == '/' ) ? '' : '/' ) . 'json/';
		}
		else
			/*API credentials are Required*/
			throw new asException('Call Error', 'Plese add Unity API credentials.');
	}

	public function setEhrUserID( $ehrUserID )
	{
		
		$ehrUserID = preg_replace('/[^\w\d]/i', '', $ehrUserID);

		if( empty($ehrUserID) )
			throw new asException( 'Call Error', 'Blank TW User ID Supplied.' );

		$this->ehrUsername = $ehrUserID;
	}

	public function getEhrUserID()
	{
		return $this->ehrUsername;
	}
	
	/*
	* Generate Unity API Token.
	* It will be used for Authorization purpose for the further API calls to Unity.
	* Store the token in session, with time of generation.
	**/
	private function generateToken()
	{
		
		$this->resetParameters();
		$this->parameters['Username'] = $this->apiUsername;
		$this->parameters['Password'] = $this->apiPassword;

		$token = $this->call( 'GetToken' );
		$token = trim($token);
		
		/*Validate token - GUID token*/
		$match = (bool)preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $token);
		if( $match === false )
		{
			throw new asException( 'API Error', 'Invalid Token Supplied from Unity.<br/>Please try again or contact Support.' );
		}
		/*End Validate token - GUID token*/
		
		/*Store token in file* /
		$tokenData = array( 'token' => $token, 'time' => time() );
		$tokenData = json_encode( $tokenData );
		file_put_contents( $file, $tokenData );
		*/
		
		/*Load Token in Session specific to the user*/
		$_SESSION['as_token'] = $token;
		$_SESSION['as_token_time'] = time();
		
		return $token;
	}
	
	/*
	 * Update token timestamp after every call made to API
	 * Unity automatically expires the token after unused duration of 20 minutes.
	 * This function is called in condtion when the token already exists in the data file
	 */
	function refreshToken()
	{
		/*$file = $this->tokenFile;
		$tokenData = file_get_contents( $file );
		$tokenData = json_decode( $tokenData );
		$tokenData->time = time();
		$tokenData = json_encode( $tokenData );
		file_put_contents( $file, $tokenData );*/
		
		/*Update Token generation time*/
		$_SESSION['as_token_time'] = time();
	}
	
	/**
	 * Prepare parameters for Unity Call
	 * @patientId = AllScripts Internal Patient Id
	 * @params = API call parameters. Maximum length is 6.
	 *  @data = Data to be pushed
	 * */
	protected function prepareParameters( $action = '', $params = array(), $pateintId = '', $data = '' )
	{
		if( $action == '' && ( $pateintId == '' || count( $params ) == 0 )  )
		{
			throw new asException( 'Call Error', 'Missing reuired data for API call.' );;
		}
		
		if ( count( $params) < 6 )
		{
			$params = array_pad( $params, 6, '' );
		}
		elseif( count( $params) > 6 )
		{
			$params = array_slice( $params, 0, 6, true );
		}
		
		$this->parameters = $this->resetParameters();
		$this->parameters['Action'] = $action;
		$this->parameters['AppUserID'] = $this->ehrUsername;
		$this->parameters['Appname'] = $this->appName;
		$this->parameters['PatientID'] = trim( $pateintId );
		$this->parameters['Token'] = $this->token;
		$this->parameters['Parameter1'] = $params[0];
		$this->parameters['Parameter2'] = $params[1];
		$this->parameters['Parameter3'] = $params[2];
		$this->parameters['Parameter4'] = $params[3];
		$this->parameters['Parameter5'] = $params[4];
		$this->parameters['Parameter6'] = $params[5];
		$this->parameters['Data'] = trim( $data );
		
	}
	
	/*
	 * Make API calls to Unity
	 **/
	protected function call( $endpoint='MagicJson')
	{
		/*Return data container*/
		$data = '';
		
		/*Operation Name*/
		if( $endpoint == 'MagicJson' )
			$operation = $this->parameters['Action'];
		
		/*Parameters are sent in json encode form*/
		$parameters = json_encode( $this->parameters );
		
		$header = array();
		$header[] = 'Content-Type: application/json'; /*Data type of the response*/
		$header[] = 'Cache-Control: no-cache'; /*Data type of the response*/
		
		/*Attach API endpoint to the service URL*/
		$callURL = $this->appURL.$endpoint;
		
		/*Log Call Details*/
		$logId = false;
		if( defined('UNITY_LOG_CALLS') && UNITY_LOG_CALLS == true )
		{
			$sqlLog = "INSERT INTO `as_api_call_log` SET
						`action`='".$this->parameters['Action']."',
						`url_endpoint`='".$callURL."',
						`parameters_sent`='".addslashes($parameters)."',
						`date_time`='".date('Y-m-d H:i:s')."',
						`facility_id`='".(isset($_SESSION['login_facility'])?$_SESSION['login_facility']:'')."',
						`user_id`='".(isset($_SESSION['authUserID'])?$_SESSION['authUserID']:'')."'";
			/*Chart Note Logs*/
			if( array_key_exists('as_log_cn_dos', $_SESSION) && array_key_exists('as_log_cn_id', $_SESSION) && $this->parameters['Action'] == 'SaveDocumentImage' )
			{
				$sqlLog .= ", `date_of_service` = '".$_SESSION['as_log_cn_dos']."', `cn_id` = '".$_SESSION['as_log_cn_id']."'";
			}
			$logResp = imw_query($sqlLog);
			if($logResp)
				$logId = imw_insert_id();
		}
		/*End Log Call Details*/
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $callURL);
		curl_setopt($ch, CURLOPT_POST, true);	/*Reset HTTP method to GET*/
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*Return the response*/
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP); /*Set protocol to HTTP if default changed*/
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); /*Prevent checking SSL certificate of peer. Though we are sending request to http. So this option is of no use.*/
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);	/*Do not fail if the HTTP error*/
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($ch, CURLOPT_HEADER, false); /*Include header in Output/Response*/
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); /*Default timeout for the request to prevent endles wait for response*/
		$data = curl_exec($ch); /*$data will hold data returned from FramesData API*/
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);	/*Get data response code*/
		/*Close curl session/connection*/
		curl_close($ch);
		
		/*Update Call response data*/
		if( $logId !== false )
		{
			$sqlLog = "UPDATE `as_api_call_log` SET
						`response_code`='".$response_code."',
						`response_data`='".addslashes($data)."'
						WHERE
						`id`='".$logId."'";
			imw_query($sqlLog);
		}
		/*End Update Call response data*/
		
		/*Break exection if API returned with non favourable response code.*/
		if( $response_code !== 200 )
			throw new asException( 'API Error', 'Service Unavailable' );
		
		if( $data !== '' && $endpoint == 'MagicJson' )
		{
			$data = json_decode( $data );
			$datakey = ( $operation === 'GetTokenValidation' ) ? 'Table' : strtolower( $operation ).'info';
			$data = $data[0];
			
			if( isset( $data->Error ) )
			{

				$data->Error = preg_replace('/.*?Magic Error\s?-?\s?\b\w*?\b:?\s\w*\b\s?-\s?/i', '', $data->Error);
				$data->Error = preg_replace('/.*?Magic Error/i', '', $data->Error);
				
				/*Chart Note Logs*/
				if( array_key_exists('as_log_cn_dos', $_SESSION) && array_key_exists('as_log_cn_id', $_SESSION) && $this->parameters['Action'] == 'SaveDocumentImage' )
				{
					$sqlLog = "UPDATE `as_api_call_log` SET
						`error_message`='".trim($data->Error)."',
						WHERE
						`id`='".$logId."'";
					imw_query($sqlLog);
				}

				throw new asException( 'API Error', trim($data->Error) );
			}
			elseif( 
				isset( $data->{$datakey}[0]->STATUS )
				&& 
				strpos($data->{$datakey}[0]->STATUS, 'Magic Error') !== false 
			)
			{
				$data->{$datakey}[0]->STATUS = preg_replace('/.*?Magic Error\s?-?\s?\b\w*?\b:?\s\w*\b\s?-\s?/i', '', $data->{$datakey}[0]->STATUS);
				$data->{$datakey}[0]->STATUS = preg_replace('/.*?Magic Error/i', '', $data->{$datakey}[0]->STATUS);

				/*Chart Note Logs*/
				if( array_key_exists('as_log_cn_dos', $_SESSION) && array_key_exists('as_log_cn_id', $_SESSION) && $this->parameters['Action'] == 'SaveDocumentImage' )
				{
					$sqlLog = "UPDATE `as_api_call_log` SET
						`error_message`='".trim($data->{$datakey}[0]->STATUS)."',
						WHERE
						`id`='".$logId."'";
					imw_query($sqlLog);
				}
				
				throw new asException( 'API Error', trim($data->{$datakey}[0]->STATUS) );
			}
			
			$data = $data->{$datakey};
			/*Update Token's Last Used time*/
			$this->refreshToken();
		}
		
		return( $data );
	}
	
	/**
	 * Make API call and return data
	 * */
	protected function makeCall( $popElem = false )
	{
		$data = $this->call();
		if( $popElem && gettype( $data ) == 'array' )
			$data = array_pop( $data );
		
		return $data;
	}
	
	/*Restore error reporting/display status & restore error handler*/
	public function __destruct()
	{
		error_reporting( $this->errorReporting );
		ini_set('display_errors', $this->errorDisplay);
		restore_error_handler();
	}
}

