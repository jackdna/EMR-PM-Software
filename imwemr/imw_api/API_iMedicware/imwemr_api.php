<?php
/**
  imwemr
  API call for imwemr
 */
class iMedicApiClient
{
    const USER_KEY = 'MDprospectsAPIclient';
    //const SECRET_KEY = '2d4e0d5dac5b29a46dbccbb5f28baf42'; //Test2
	//const SECRET_KEY = '5dec902e98f495ec04beb4442b10c175';	//Test5
	//const SECRET_KEY = 'd8eeea26938f2756774045b48e9362d2';	//Test
	const SECRET_KEY = '2a0c85c4489ed198a05953e09ea73a16';	//Demo Server
	//const SECRET_KEY = 'd2ae343dbc964f63800d69125ab18a88';	//Test10
	
    const USER_AGENT = 'iMedicWareApiclient';
	//const SERVER_HOST = 'demo.domain.com/cartereye/imw_api/receive_calls/index.php?mode=';
	//const SERVER_HOST = 'demo.domain.com/tylock/imw_api/receive_calls/index.php?mode=';
	//const SERVER_HOST = 'demo.domain.net/nse/imwemr/imw_api/receive_calls/index.php?mode=';
	//const SERVER_HOST = 'demo.domain.com/imw_api/receive_calls/index.php?mode=';
	const SERVER_HOST = 'imwemr.com/demo-r8/imw_api/receive_calls/index.php?mode=';
	
	const SERVER_LOCAL = 1;

    public function post($url_string, $fields, $format="")
    {
        $format=(empty($format))?"application/x-www-form-urlencoded":$format;
		$headers = array("Accept: $format");
		
        $data_to_sign = self::USER_KEY.self::USER_AGENT.self::SECRET_KEY;
        $secure_code = md5($data_to_sign);
		$url_string.='&sec_code='.$secure_code;

        //MAKE HEADERS AND CURL
		$headers = array_merge($this->authorization_headers(), $headers);
        $url = $this->construct_uri($url_string);
//echo $url; exit;
        $curl_session = curl_init($url);

        curl_setopt($curl_session, CURLOPT_VERBOSE, false); //set to true for debug
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, 0);


        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $fields);

        //EXECUTE CURL
		$responseData= $this->send_request($curl_session);

        return $responseData;
    }

    private function send_request($curl_session)
    {
        $response = curl_exec($curl_session);

        curl_close($curl_session);
        return $response;
    }

    private function authorization_headers()
    {
        $data_to_sign = self::USER_KEY.self::USER_AGENT.self::SECRET_KEY;
        $secure_code = md5($data_to_sign);
		
        $headers = array();
        $headers[] = "User-Agent: " . self::USER_AGENT;
        $headers[] = 'X-Key: '.self::USER_KEY;
		$headers[] = 'sec_code: '.$secure_code;
        return $headers;
    }

    private function construct_uri($url_string)
    {
		if (self::SERVER_LOCAL)
		{
			$url = 'http://' . self::SERVER_HOST . $url_string;
		}
		else
			$url = 'https://' . self::SERVER_HOST . $url_string;



 $url = 'https://' . self::SERVER_HOST . $url_string;
        return $url;
    }

	public function pre($data)
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
}
?>