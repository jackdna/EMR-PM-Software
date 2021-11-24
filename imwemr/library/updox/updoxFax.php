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
class updoxFax{
	
	/*URL to be used for the API call*/
	protected $apiURL = '';
	protected $account_id = '';
	protected $applicationId = '';
	private $fax_name = '';
	private $fax_number = '';
	protected $fax_status_url = '';
	protected $auth = array();
	
	/*Constructor to fetch Account credentials from DB*/
	public function __construct(){
		global $protocol, $phpServerIP;
		
		$sql = 'SELECT `account_id`, `fax_no`, `fax_name` FROM `updox_credentials`';
		$resp = imw_query($sql) or die($sql.imw_error());
		
		if( $resp && imw_num_rows($resp) > 0 ){
			$data = imw_fetch_assoc($resp);
			$this->account_id = $data['account_id'];
			$this->fax_name = $data['fax_name'];
			$this->fax_number = $data['fax_no'];
		}
		
		if( defined('UPDOX_FAX_STATUS') && UPDOX_FAX_STATUS === true )
			$this->fax_status_url =  $protocol.$phpServerIP.$GLOBALS['webroot'].'/library/updox/updateFaxStatus.php';
		
		/*Set Updox api endpoint to be used*/
		if( defined('UPDOX_PRODUCTION') && UPDOX_PRODUCTION === true )
			$this->apiURL = 'https://myupdox.com/api/io';
		else
			$this->apiURL = 'https://updoxqa.com/api/io';
			
		$this->prepareAuth();
	}
	
	private function prepareAuth()
	{
		$this->applicationId = UPDOX_APP_ID;
		
		$this->auth = array('applicationId'=>UPDOX_APP_ID, 'applicationPassword'=>UPDOX_APP_PASSWORD, 'accountId'=>$this->account_id, 'userId'=>'');
		
		$sql = 'SELECT updox_user_id FROM users where id="'.$_SESSION['authId'].'"';
		$resp = imw_query($sql) or die($sql.imw_error());
		if(imw_num_rows($resp)>0) {
			$data = imw_fetch_assoc($resp);
			if(trim($data['updox_user_id'])) {
				$this->auth['userId'] = $data['updox_user_id'];
			}
		}
	}
	
	
	/**
	 * Send fax message
	 * @name = recipient name
	 * @number = recipient number
	 * @pdfData = base64 encoded pdf data
	 * */
	public function sendFax( $name, $number, $pdfData )
	{
		$data = array('auth'=>$this->auth);
		
		$data['toName'] = $name;
		$data['toFaxNumber'] = $number;
		$data['fromName'] = $this->fax_name;
		$data['fromFaxNumber'] = $this->fax_number;
		$data['faxContent'] = $pdfData;
		$data['callbackUrl'] = $this->fax_status_url;//pre($data);
		
		$resp = $this->call('FaxOemSend', $data);
		return $resp;
	}

	/**
	 * Bulk Fax sending
	 * @name = recipient name
	 * @number = recipient number
	 * @pdfData = base64 encoded pdf data
	 * */
	public function sendFaxMulti( $numbers = array(), $pdfData )
	{
		$data = array('auth'=>$this->auth);
		
		$data['recipientFaxNumbers'] = $numbers;
		$data['fromName'] = $this->fax_name;
		$data['fromFaxNumber'] = $this->fax_number;
		$data['faxContent'] = $pdfData;

		$resp = $this->call('FaxBulkSend', $data);
		return $resp;
	}
	
	public function providerSync( $providerData = array() )
	{

		$data = array('auth'=>$this->auth);
		$data = array_merge($data,$providerData);

		$resp = $this->call('CalendarsSync', $data);
		return $resp;
	}

	public function patientSync( $demographicData )
	{

		$data = array('auth'=>$this->auth);
		$data['patients'] = $demographicData;

		$resp = $this->call('PatientsSync', $data);
		return $resp;
	}
	
	public function apptSync( $apptData )
	{

		$data = array('auth'=>$this->auth);
		$data['appointments'] = $apptData;

		$resp = $this->call('AppointmentsSync', $data);
		return $resp;
	}
	
	/**
	 * Get fax message
	 * @faxId = Updox fax Id to fetch PDF data
	 * */
	public function getFaxPDF( $faxId )
	{
		$data = array('auth'=>$this->auth);
		
		$data['faxId'] = $faxId;
		
		$resp = $this->call('FaxItemGetPdf', $data);
		return $resp;
	}

	public function pingAccount()
	{
		$data = array('auth'=>$this->auth);

		$resp = $this->call( 'pingWithAccountAuth', $data, true );
		return $resp;
	}
	
	/**
	 * Make API calls to updox
	 * @endpoint = Updox api endpoint to be called
	 * @parameters = data to be posted in the call
	 **/
	protected function call( $endpoint, $parameters, $rawResponse = false)
	{
		/*Return data container*/
		$resp = array('status'=>'', 'message'=>'');
		
		/*Parameters are sent in json encode form*/
		$parameters = json_encode( $parameters );
		
		$header = array();
		$header[] = 'Content-Type: application/json'; /*Data type of the response*/
		
		/*Attach API endpoint to the service URL*/
		$callURL = $this->apiURL.'/'.$endpoint;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $callURL);
		curl_setopt($ch, CURLOPT_POST, true);	/*Reset HTTP method to GET*/
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*Return the response*/
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS); /*Set protocol to HTTP if default changed*/
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); /*Do not check SSL certificate of peer. This is required to ensure communication security.*/
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);	/*Do not fail if the HTTP error. It is being handled explicitly*/
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($ch, CURLOPT_HEADER, false); /*Do not Include header in Output/Response*/
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); /*Default timeout for the request to prevent endles wait for connection*/
		$data = curl_exec($ch); /*$data will hold data returned from UPDOX API*/
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);	/*Get data response code*/
		/*Close curl session/connection*/
		curl_close($ch);
		$respLog = $data;
		if( $rawResponse )
		{
			return json_decode($data);
		}
		
		/*Break exection if API returned with non favourable response code.*/
		if( $response_code !== 200 || $data === '' ) {
			$resp['status'] = 'failed';
			$resp['statusCode'] = $response_code;
			$resp['message'] = 'Unknown Error Occoured';
		} else {
			$data = json_decode($data);
			
			if( $data->responseCode === 2000 && strtolower($data->responseMessage) == 'ok' && (bool)$data->successful === true && (!$data->statuses || (bool)$data->statuses[0]->success === true )) {
				$resp['status'] = 'success';

				if( $endpoint !== 'MdnStatusList' )
				{
					unset($data->successful, $data->responseMessage, $data->responseCode);
				}
				
				$resp['data'] = $data;

			}
			else {
				$resp['status'] = 'failed';
				$resp['statusCode'] = trim($data->responseCode);
				$resp['message'] = trim($data->responseMessage);
				if($data->statuses[0]->message) {
					$resp['messageStatuses'] = trim($data->statuses[0]->message);	
				}
			}
		}
		$dtTm 		= date("Y-m-d H:i:s");
		$method 	= 'POST';
		$respStatus = $resp['status'];
		$qryUpdoxLog= "INSERT INTO updox_log (request_type,request_url,request_data,request_date_time,response_data,response_date_time,response_status,operator_id)
						VALUES('".$method."', '".$callURL."', '".$parameters."', '".$dtTm."', '".$respLog."', '".$dtTm."', '".$respStatus."', '".$_SESSION['authId']."')";
		$resUpdoxLog = imw_query($qryUpdoxLog);	
		return( $resp );
	}
	
	public function validateInboundIP()
	{
		/**
		 * Return with success, if Updox client IP range validation is disabled
		 */
		if( 
			defined('UPDOX_IP_RANGE_VALIDATION') &&
			UPDOX_IP_RANGE_VALIDATION === false
		)
		{
			return true;
		}


		/*
		list of ip list sent by updox
		198.167.186.196 - .206 =>Production
		74.219.154.208 - .223
		74.143.39.18 - .25
		24.106.152.134 - .137
		66.11.23.210 - .225
        198.167.186.225 - .238 =>QA
		*/
		$ip_list=array('74.219.154.208', '74.219.154.209', '74.219.154.210', '74.219.154.211', '198.167.186.196', '198.167.186.197', '198.167.186.198', '198.167.186.199', '198.167.186.200', '198.167.186.201', '198.167.186.202', '198.167.186.203', '198.167.186.204', '198.167.186.205', '198.167.186.206', '74.219.154.223', '74.143.39.18', '74.143.39.19', '74.143.39.20', '74.143.39.21', '74.143.39.22', '74.143.39.23', '74.143.39.24', '74.143.39.25', '24.106.152.134', '24.106.152.135', '24.106.152.136', '24.106.152.137', '198.167.186.225', '198.167.186.226', '198.167.186.227', '198.167.186.228', '198.167.186.229', '198.167.186.230', '198.167.186.231', '198.167.186.232', '198.167.186.233', '198.167.186.234', '198.167.186.235', '198.167.186.236', '198.167.186.237', '198.167.186.238', '66.11.23.223', '66.11.23.224', '66.11.23.225');

		$approvedIPs=array_combine($ip_list,$ip_list);

		$incoming_ip=$this->getClientIP();

		return ($approvedIPs[$incoming_ip])?true:false;
	}

	public function getClientIP()
	{       
		$returnServer = $_SERVER['REMOTE_ADDR'];

		//Checking Apache Headers for HCCS servers
		$apacheReqheaders = apache_request_headers();
		if(array_key_exists('X-Forwarded-For', $apacheReqheaders)){
			$returnServer = $apacheReqheaders["X-Forwarded-For"];  
		}

		return $returnServer;
	}
	
	/**
	* Expose the Updox Account Id for the Practice in context
	*/
	public function accountId()
	{
		return $this->account_id;
	}

	/**
	 * List Status updates using Message Id
	 */
	public function listMdnStatuses( $messageId, $userId )
	{
		/**
		 * Updox authentication data
		 */
		$this->auth['userId'] = $userId;
		$data = array('auth'=>$this->auth);
		
		$data['messageId'] = $messageId;
		
		$resp = $this->call('MdnStatusList', $data);
		return $resp;
	}
}
