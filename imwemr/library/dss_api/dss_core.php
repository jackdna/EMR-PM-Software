<?php

class Dss_core
{
	
	/*Unity API token*/
	private $token = false;
	
	/*Hold's API credentials*/
	private static $apiUrl;
	private static $accessCode;
	private static $verifyCode;
	private static $menuContext;
	public $reqObj = array();

	
	public function __construct($req=array())
	{
        $this->reqObj=$req;
        if(isset($_SESSION['dss_vista']) && is_array($_SESSION['dss_vista']) && empty($_SESSION['dss_vista'])==false ) {
            $this->reqObj=$_SESSION['dss_vista'];
        }
        
        // $this->apiUrl='http://172.31.255.6:3020/api/';
        if(!isset($this->apiUrl) || empty($this->apiUrl))
            $this->loadCredentials();
		
        $this->validateToken();
	}

	private function checkAuth()
	{
		 return ( isset($_SESSION['dss_token']) && trim($_SESSION['dss_token']) != '' && isset($_SESSION['dss_loginDUZ']) && trim($_SESSION['dss_loginDUZ']) != '' && isset($_SESSION['dss_token_time']) && trim($_SESSION['dss_token_time']) != '' );
	}
	
	private function validateToken()
	{
		// $this->loadCredentials();
		$token = '';
		if( $this->checkAuth() )
		{
			$tokenTime = $_SESSION['dss_token_time'];
			$cTime = time();
			
			/*Time difference in minutes*/
			$interval = $cTime - $tokenTime;
			$minutes = round( $interval / 60 );
			
			/*Generate new token if the existing token is near to expire*/
			if ( $minutes < 8 )
				$token = $_SESSION['dss_token'];
		}
		
		if ( $token== '' ) {
			$token = $this->auth();
        }
		$token = trim( $token );

		if( $token == '' )
			throw new Exception( 'Token not generated' );
		
		$this->token = $token;
	}
	

	private function loadCredentials()
	{
        $sql = 'SELECT `accessCode`, `verifyCode`, `menuContext`, `url` FROM `dss_credentials` WHERE `id`=1';
        $resp = imw_query($sql);
        if( $resp && imw_num_rows( $resp ) )
        {
            $credsData = imw_fetch_assoc( $resp );
            $creds['accessCode'] = $credsData['accessCode'];
            $creds['verifyCode'] = $credsData['verifyCode'];
            $creds['menuContext'] = $credsData['menuContext'];
            $creds['url'] = $credsData['url'];
        
            /*All details are required*/
            foreach( $creds as $cVal )
            {
                if( trim($cVal) == '' )
                    throw new Exception('Call Error: Missiong DSS credential. Please check.');
            }

            // $this->accessCode="prog999-";
            // $this->verifyCode="prog1234-";
            // $this->menuContext="VEJD PCE RECORD MANAGER";

            $this->accessCode  = $creds['accessCode'];
            $this->apiUrl      = urldecode($creds['url']);
            $this->verifyCode  = $creds['verifyCode'];
            $this->menuContext = $creds['menuContext'];
            if(empty($this->reqObj)==false) {
                $_SESSION['dss_vista']=$this->reqObj;

                $this->accessCode  = $this->reqObj['accessCode'];
                $this->verifyCode  = $this->reqObj['verifyCode'];
            }
        }
        else
            /*API credentials are Required*/
            throw new Exception('Call Error: Please add DSS credentials.');
	}


	public function auth()
	{
        // $this->loadCredentials();
        
		$params = array();
        $params['accessCode'] = $this->accessCode;
        $params['verifyCode'] = $this->verifyCode;
        $params['menuContext'] = $this->menuContext;

		$result = $this->CURL($params);
        
        if($result && isset($result['authFailed']) && $result['authFailed']==false){
            $token = $result['token'];
            $duz = $result['soLogin']['loginDUZ'];
            $_SESSION['dss_token'] = $token;
            $_SESSION['dss_token_time'] = time();
            $_SESSION['dss_loginDUZ'] = $duz;
            $_SESSION['dss_location'] = '530';
        } else if($result['authFailed']==true) {
            $msg=$result['soLogin']['errorText'];
            $this->unsetSessionToken();
            throw new Exception($msg);
        } else if($result['statusCode']==400) {
            $msg=$result['message'];
            $this->unsetSessionToken();
            throw new Exception($msg);
        } else {
            $this->unsetSessionToken();
            throw new Exception('No data returned');
        }
		
        if($token) {
            $this->token = $token;
        }
        
		return $this->token;
	}
    
    private function unsetSessionToken() 
    {
        $this->token='';
        unset($_SESSION['dss_token']);
        unset($_SESSION['dss_token_time']);
        unset($_SESSION['dss_loginDUZ']);
        unset($_SESSION['dss_vista']);
        unset($_SESSION['dss_location']);
    }
    
	public function logout()
	{
		$params = array();
		$result = $this->CURL($params,'logout','GET');
        if($result && isset($result['resultCode']) && $result['resultCode']==0 && isset($result['message']) && strtolower($result['resultCode'])=='logout complete.'){
            $this->unsetSessionToken();
        }
        return $result;
	}

    
    // Common CURL Request fucntion 
    public function CURL($params,$endpoint='auth',$curl_req_method='POST')
    {
        $payload = json_encode($params);

        $request_headers = array();
        $request_headers[] = 'Accept:application/json';
        $request_headers[] = 'Content-Type:application/json';
        if($endpoint!='auth'){
            $request_headers[] = 'Authorization:Bearer '.$this->token;
        }

        // Reset Curl message before each request
        $this->reset_curl_msg();

        // API End Point
        $url = $this->apiUrl.$endpoint;

        $logId = false;
        if(defined('DSSAPILOG') && DSSAPILOG == true){
            $sqlLog = "INSERT INTO `dss_api_log` SET
                        `url_endpoint`='".$url."',
                        `parameters_sent`='".addslashes($payload)."',
                        `created_at`='".date('Y-m-d H:i:s')."',
                        `facility_id`='".(isset($_SESSION['login_facility'])?$_SESSION['login_facility']:'')."',
                        `user_id`='".(isset($_SESSION['authUserID'])?$_SESSION['authUserID']:'')."'
                    ";
            $logResp = imw_query($sqlLog);
            if($logResp)
                $logId = imw_insert_id();
        }

        try {
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
            if($endpoint!='logout'){
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }
            // Execute Curl Request
            $result = curl_exec($ch);

            // Get data response code
            $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                // If error then set curl message
                $this->set_curl_msg($ch);
            }

            // Close Curl Request
            curl_close($ch);

            /*Update Call response data*/
            if( $logId !== false )
            {
                $sqlLog = "UPDATE `dss_api_log` SET
                            `response_code`='".$response_code."',
                            `response_data`='".addslashes($result)."'
                            WHERE `id`='".$logId."'";
                imw_query($sqlLog);
            }
            /*End Update Call response data*/

            /*Break exection if API returned with non favourable response code.*/
            if( $response_code !== 200 ) {
                $this->unsetSessionToken();
                throw new Exception( 'API Error: Service Unavailable' );
            }

            if( $result !== '' )
            {
                // DECODE result data from json format
                $result = json_decode($result, true);
            }
        } catch(Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }
    
    
    private function set_curl_msg($curl) 
    {

        $this->curl_error_no = curl_errno($curl);
        $this->curl_error_msg = curl_error($curl);
    }

    private function reset_curl_msg() 
    {

        $this->curl_error_no = false;
        $this->curl_error_msg = false;
    }

    private function handle_error($msg) 
    {

        if (trim($msg))
            die($msg);
    }
    
    //Convert Fileman Date format to Display Date format
    public function ConvertVistaDTtoDisplayDT($filemanDate) {
        if ( $filemanDate == '' )
			throw new Exception( 'Blank date string Supplied.' );
		
		$params = array();
        $params['dateTime'] = $filemanDate;

		$result = $this->CURL($params,'DSIHTE/MISC_ConvertVistaDTtoDisplayDT');
		return $result;
        
    }
    
    //Convert Display date format to Fileman date format
    public function ConvertDisplayDateTimeToVistADateTime( $params=array() ) {
        if( empty($params) )
            throw new Exception( 'Empty array supplied to ConvertDisplayDateTimeToVistADateTime.' );

		$result = $this->CURL($params,'DSIHTE/MISC_ConvertDisplayDateTimeToVistADateTime');
		return $result[0];
        
    }
    
    /*
     * [
        {
          "delphi": "43092",
          "fileman": "3171223",
          "external": "Dec 23, 2017",
          "mumps": "64640,0",
          "hl7": "20171223"
        }
      ]
     */
    //Convert Display date format to Fileman date format
    public function MISC_DSICDateConvert( $Dateformat ) {
        if ( $Dateformat == '' )
			throw new Exception( 'Blank date string Supplied.' );
        
        $params = array();
        $params['inVal'] = $Dateformat;
        $params['inType'] = "F";
        $params['outType'] = "A";
        $params['outFmt'] = "1";
        $params['timeFmt'] = "M";

		$result = $this->CURL($params,'DSIHTE/MISC_DSICDateConvert');
		return $result[0];
        
    }
    
    /**
     * Validate User ESignature
     */
    public function validateESignature($duz, $esign)
    {
        if( empty($duz) && empty($esign) )
            throw new Exception("Duz and ESignature not allowed to be empty.");

        $params = array();
        $params['duz'] = $duz;
        $params['sig'] = $esign;
        $result = $this->CURL($params, 'DSIHTE/CPRS_ValidateESignature');

        return $result[0]['code'];
    }
	
    /**
     * Convert datetime (fileman to Y-m-d H:i:s) 
     */
    public function filemanToBase($n) {
        $date = '';
        if (strpos($n, ".") !== false) {
            $dt = explode('.', $n);
           
            if( strlen($dt[0]) > 7 || strlen($dt[1]) > 6 ) {
                throw new Exception('Not a valid fileman format');
            }

            // Date
            $yy = substr($dt[0], 0,3) + 1700;
            $mm = substr($dt[0], 3,2);
            $dd = substr($dt[0], 5,2);

            if(checkdate($mm, $dd, $yy) !== false) {
                $d = $yy.'-'.$mm.'-'.$dd;
            } else {
                throw new Exception('Error DSS: Not a valid date ('.$yy.'-'.$mm.'-'.$dd.')');
            }

            // Time
            if(strlen($dt[1]) == 4) {
                $h = substr($dt[1], 0,2);
                $i = substr($dt[1], 2,2);
                $tm = $h.':'.$i;

                if (preg_match('/^\d{2}:\d{2}$/', $tm)) {
                    if (preg_match("/(2[0-3]|[0][0-9]|1[0-9]):([0-5][0-9])/", $tm)) {
                        $time = $tm.':00';
                    } else {
                        throw new Exception('Not a valid time');
                    }
                } else {
                    throw new Exception('Not a valid time');
                }
            } else {
                $h = substr($dt[1], 0,2);
                $i = substr($dt[1], 2,2);
                $s = substr($dt[1], 4,2);
                $tm = $h.':'.$i.':'.$s;

                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $tm)) {
                    if (preg_match("/(2[0-3]|[0][0-9]|1[0-9]):([0-5][0-9]):([0-5][0-9])/", $tm)) {
                        $time = $tm;
                    } else {
                        throw new Exception('Not a valid time');
                    }
                } else {
                    throw new Exception('Not a valid time');
                }
            }
            $date = $d.' '.$time;
        } else {
            // Date
            $yy = substr($n, 0,3) + 1700;
            $mm = substr($n, 3,2);
            $dd = substr($n, 5,2);

            if(checkdate($mm, $dd, $yy) !== false) {
                $date = $yy.'-'.$mm.'-'.$dd;
            } else {
                throw new Exception('Error DSS: Not a valid date ('.$yy.'-'.$mm.'-'.$dd.')');
            }
        }
        return $date;
    }

    /**
     *
     */
    public function convertToFileman($date) {
        $time = '';
        if (strpos($date, " ") !== false) {
            $d = explode(' ', $date);
            $time = '.'.str_ireplace(':', '', $d[1]);
        } else {
            $d[0] = $date;
        }
        $dt = explode('-', $d[0]);
        $yy = $dt[0] - 1700;
        $mm = $dt[1];
        $dd = $dt[2];
        $fDate = $yy.$mm.$dd;

        return $fDate.$time;
    }
}

