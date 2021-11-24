<?php

class ERP_portal_core
{

	/*Unity API token*/
	private $token = false;

	/*Hold's API credentials*/
	private static $apiUrl;
	public static $accountId;
	private static $accountNumber;
	private static $synchronizationUserName;
	private static $synchronizationPassword;
	private static $erp_cookie_name;
	private $skip_exception;

	public function __construct($req=array())
	{
		if(isset($GLOBALS["ERP_API_PATIENT_PORTAL_URL"]) && $GLOBALS["ERP_API_PATIENT_PORTAL_URL"]==1) {
			$this->apiUrl='https://api.eyereachpatients.com/';
		} else {
			$this->apiUrl='https://preproductionapi.eyereachpatients.com/';
		}

        /* Set cookie name */
        $this->erp_cookie_name="erpAccessToken";

        $this->validateToken();

				$this->skip_exception=(isset($req["skip_exception"])) ? $req["skip_exception"] : 0;
	}

    private function set_token($token)
    {
        $this->token = $token;
    }

    public function get_token()
    {
        return $this->token;
    }

    /*
     * Check whether token is set in session or not.
     */
	private function checkAuth()
	{
        return ( isset($_COOKIE[$this->erp_cookie_name]) && trim($_COOKIE[$this->erp_cookie_name]) != '' );
	}

    /*
     * Validate Session token is active or expired. if expired then make a call to generate token
     */
	private function validateToken()
	{
        $this->loadCredentials();
		$token = '';

        try
        {
            if( $this->checkAuth() )
            {
                $token = $_COOKIE[$this->erp_cookie_name];
            }

            if ( $token== '' ) {
                /* If token is empty generate new token. */
                $token = $this->generateToken();
            }
            $token = trim( $token );

            if( $token == '' ) {
                throw new Exception( 'API Error: Token not generated.' );
            }
		}
        catch(Exception $e) {
            $this->handle_error($e);
        }

        if($token) {
            $this->set_token($token);
        }

	}


    /*
     * Load the synchronization credentials from database and store in variables
     */
	private function loadCredentials()
	{
        try
        {
            $sql = 'SELECT `account_id`,`account_number`,`synchronization_username`,`synchronization_password` FROM `erp_api_credentials` WHERE `id`=1';
            $resp = imw_query($sql);
            if( $resp && imw_num_rows( $resp ) == 1 )
            {
                $credsData = imw_fetch_assoc( $resp );
                $creds['account_id'] = $credsData['account_id'];
                $creds['accountNumber'] = $credsData['account_number'];
                $creds['synchronizationUserName'] = $credsData['synchronization_username'];
                $creds['synchronizationPassword'] = $credsData['synchronization_password'];

                /*All details are required
                 through exception if username or password not entered in admin */
                if( trim($creds['synchronizationUserName']) == '' || trim($creds['synchronizationPassword']) == '' ) {
                    throw new Exception('Call Error: Missiong ERP Synchronization credentials. Please check.');
                }
                $this->account_id  = $creds['account_id'];
                $this->accountNumber = $creds['accountNumber'];
                $this->synchronizationUserName  = $creds['synchronizationUserName'];
                $this->synchronizationPassword  = $creds['synchronizationPassword'];
            }
            else {
                /*API credentials are Required*/
                throw new Exception('Call Error: Please add ERP Synchronization credentials.');
            }
        }
        catch(Exception $e) {
            $this->handle_error($e);
        }
	}

    /*
     * Make a curl call to generate token for further API call.
     */
	private function generateToken()
	{
        /* Create parameters to generate token*/
		$params = array();
        $params['grant_type'] = 'password';
        $params['username'] = $this->synchronizationUserName;
        $params['password'] = $this->synchronizationPassword;

		$result = $this->CURL($params);
        try
        {
            if($result && isset($result['access_token']) && $result['access_token']!=''){
                $token = $result['access_token'];
                $erp_token_time = (time() + $result['expires_in'])-3600;
                setcookie($this->erp_cookie_name,$token,$erp_token_time,"/");
            } else if(isset($result['error']) && $result['error']!='') {
                $msg=$result['error'];
                $this->unsetSessionToken();
                throw new Exception($msg);
            } else {
                $this->unsetSessionToken();
                throw new Exception('API Error: No data returned.');
            }

            if($token) {
                $this->set_token($token);
            }
        }
        catch(Exception $e) {
            $this->handle_error($e);
        }

		return $this->get_token();
	}

    /*
     * Unset the token in session
     */
    private function unsetSessionToken()
    {
        $this->token='';
        $erp_token_time=time()-3600;
        setcookie($this->erp_cookie_name,'',$erp_token_time,"/");
    }


    /*
     * Common Curl request function for All API call for ERP Portal API
     */
    public function CURL($params,$endpoint='token',$curl_req_method='POST')
    {
        $request_headers = array();
        if($endpoint=='token'){
            $payload = http_build_query($params);

            $request_headers[] = 'Content-Type:application/x-www-form-urlencoded';
        } else {
            $payload = json_encode($params);

            $request_headers[] = 'Accept:application/json';
            $request_headers[] = 'Content-Type:application/json';
            $request_headers[] = 'Authorization:Bearer '.$this->token;
        }

        // API End Point
        $url = $this->apiUrl.$endpoint;

        $date_time = date('Y-m-d H:i:s');
        /*save the request log in erp_api_log*/
        $log_req_qry = "INSERT INTO erp_api_log (request_header,request_type,request_url,request_data,request_date_time,operator_id)
                        VALUES ('".json_encode($request_headers)."','".$curl_req_method."','".$url."','".$payload."','".$date_time."','".$_SESSION['authId']."') ";
        $log_req_sql = imw_query($log_req_qry);
        $log_req_id = imw_insert_id();

        try
        {
            // Initiate Curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $curl_req_method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /* Return the response */
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_FAILONERROR, false);
            curl_setopt($ch, CURLOPT_HEADER, false); /* Include header in Output/Response */
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            // Execute Curl Request
            $result = curl_exec($ch);
			// Get data response code
            $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if($result=='' && $response_code == 200) {
				$result = "OK";
			}

            // Update response for each executed request by imwemr
            $log_res_qry = "UPDATE erp_api_log SET response_data = '".$result."', response_date_time = '".date('Y-m-d H:i:s')."' WHERE id = ".$log_req_id." ";
            $log_res_sql = imw_query($log_res_qry);


            if (curl_errno($ch)) {
                // If error then set curl message
                $this->handle_error($ch);
            }

            // Close Curl Request
            curl_close($ch);

            if( $result !== '' )
            {
                // DECODE result data from json format
                $result = json_decode($result, true);
            }

						/*Break exection if API returned with non favourable response code.*/
						/*
						if( $response_code !== 200 && $result['message']) {
								if(!empty($this->skip_exception)){} //stop b
								else
								{
                	throw new Exception( $result['message'] );
								}
            }
						*/

        }
        catch(Exception $e) {
            $this->handle_error($e->getMessage());
        }

        return $result;
    }


    /*
     * Handle all exception through as error in the class.
     */
    private function handle_error($msg)
    {
        if (trim($msg))
            return $msg;
    }


}
