<?php
/**
  MDprospects.com
  Simple PHP client class  REST API

 */

class sendApiClient
{
    const USER_KEY = 'imwemrApiUser';
    const SECRET_KEY = 'yoursecretkey';
    const USER_AGENT = 'MDprospectsAPIclient';

    const VERSION = 'v1';
	const SERVER_HOST = 'v2.domain.com/webapi';
	//const SERVER_HOST = '192.168.1.31/imwemr-Dev/imw_api/receive_calls/index.php?mode=';
	const SERVER_LOCAL = 0;


    public function get($url_string, $format="")
    {
        $format=(empty($format))?"application/x-www-form-urlencoded":$format;
		$headers = array("Accept: $format");

        $curl_session = self::construct_session($url_string, $headers);

        $http_message = self::send_request($curl_session);
        return $http_message;
    }

    public function post($url_string, $fields, $format="")
    {
        $format=(empty($format))?"application/x-www-form-urlencoded":$format;
		$headers = array("Accept: $format");

        $curl_session = self::construct_session($url_string, $headers);

        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $fields);

        $http_message = self::send_request($curl_session);
        return $http_message;
    }

    public function put($url_string, $fields)
    {
        $curl_session = self::construct_session($url_string, array());

        curl_setopt($curl_session, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $fields);

        $http_message = self::send_request($curl_session);
        return $http_message;
    }

    public function delete($url_string)
    {
        $curl_session = self::construct_session($url_string, array());

        curl_setopt($curl_session, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $http_message = self::send_request($curl_session);
        return $http_message;
    }

    private function send_request($curl_session)
    {
        $response = curl_exec($curl_session);

        curl_close($curl_session);

        return $response;
    }

    private function construct_session($url_string, $existing_headers)
    {
        $headers = array_merge(
                self::authorization_headers(), $existing_headers);
        $url = self::construct_uri($url_string);

        $curl_session = curl_init($url);

        curl_setopt($curl_session, CURLOPT_VERBOSE, false); //set to true for debug
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, 0);


        return $curl_session;
    }

    private function authorization_headers()
    {
        $time_stamp = date('YmdHis');

        $data_to_sign = self::USER_KEY.self::USER_AGENT.$time_stamp. self::SECRET_KEY;

        $signature = base64_encode(sha1($data_to_sign, true));

        $headers = array();
        $headers[] = "User-Agent: " . self::USER_AGENT;
        $headers[] = 'X-Api-Signature: '.self::USER_KEY."|$time_stamp|$signature";
        return $headers;
    }

    private function construct_uri($url_string)
    {
        
		if (self::SERVER_LOCAL)
		{
			
			$url = 'http://' . self::SERVER_HOST . '/' . self::VERSION . $url_string;
		}
		else
			$url = 'https://' . self::SERVER_HOST . '/' . self::VERSION . $url_string;

        return $url;
    }
	protected function _getIpAddress()
    {
        if (isset($_SERVER)) {
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	}
	else {
		if (getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$realip = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$realip = getenv( 'HTTP_CLIENT_IP' );
		} else {
			$realip = getenv( 'REMOTE_ADDR' );
		}
	}
	return($realip);
    }

	public function prp($data)
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	
	}
}
?>