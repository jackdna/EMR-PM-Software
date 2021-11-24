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

class uga_base
{
	public $base_url = '';
	public $key = '';
	public $url = '';
	public $loginURL = '';
	public $ccn = '';
	public $location = '';
	public $program = '';

	public function __construct()
	{
		if(
			isUGAEnable() && 
			(defined('UGA_API_KEY') && UGA_API_KEY != '') && 
			(defined('UGA_ENV') && UGA_ENV != '') && 
			(defined('UGA_CONTACT_CUSTOMER_NUMBER') && UGA_CONTACT_CUSTOMER_NUMBER != '') && 
			(defined('UGA_LOCATION_ID') && UGA_LOCATION_ID != '') && 
			(defined('UGA_PROGRAM_NUMBER') && UGA_PROGRAM_NUMBER != '')
		) {
			if(UGA_ENV == 'STAGING'){
				$this->base_url = 'https://staging.uportal360.com';
			} elseif (UGA_ENV == 'PRODUCTION') {
				$this->base_url = 'https://uportal360.com';
			}
			$this->url = $this->base_url.'/service/rest/v1.0/';
			$this->loginURL = $this->base_url.'/logInAs';
			
			$this->key = trim(UGA_API_KEY);
			$this->ccn = trim(UGA_CONTACT_CUSTOMER_NUMBER);
			$this->location = trim(UGA_LOCATION_ID);
			$this->program = trim(UGA_PROGRAM_NUMBER);
		} else {
			throw new Exception("UGA API configuration not complete.");
		}
	}

	/**
	 * Handle all the API CURL Calls
	 * @param: api endpoint, api request method, arguments
	 */
	public function CURL($endpoint, $method, $params = '')
	{
		$apiURL = '';
		$payload = '';

		if(!empty($method) || $method !== '') {
			
			$request_headers = array();
			$request_headers[] = 'Content-Type:application/json';

			switch ($method) {
				case 'GET':
					$apiURL = $this->url.$endpoint.'?api_key='.$this->key;
					break;

				case 'POST':
					$apiURL = $this->url.$endpoint;

					switch ($endpoint) {
						case 'creditApplication':
							$reqArray = array(
								'api_key' => $this->key,
								'location_id' => $this->location,
								'program_number' => $this->program,
							);
							$params = array_merge($reqArray, $params);
							break;
						
						default:
							$params = array_merge(array('api_key' => $this->key), $params);
							break;
					}
					
					$payload = json_encode($params);
					break;
			}

			// pre($apiURL,1);
			// pre($payload,1);

			$logId = false;
			if(defined('UGA_API_LOG') && UGA_API_LOG == true){
				$sqlLog = "INSERT INTO `uga_api_log` SET
							`url_endpoint`='".$apiURL."',
							`parameters_sent`='".addslashes($payload)."',
							`created_at`='".date('Y-m-d H:i:s')."',
							`facility_id`='".(isset($_SESSION['login_facility'])?$_SESSION['login_facility']:'')."',
							`user_id`='".(isset($_SESSION['authUserID'])?$_SESSION['authUserID']:'')."'
						";
				$logResp = imw_query($sqlLog);
				if($logResp)
					$logId = imw_insert_id();
			}

			// Initiate Curl 
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiURL);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /* Return the response */
			curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_FAILONERROR, false);
			curl_setopt($ch, CURLOPT_HEADER, false); /* Include header in Output/Response */
			curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
			if($method == 'POST'){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			}
			// Execute Curl Request
			$result = curl_exec($ch);

			// Get data response code
			$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			// Close Curl Request
			curl_close($ch);

			/*Update Call response data*/
			if( $logId !== false )
			{
				$sqlLog = "UPDATE `uga_api_log` SET
							`response_code`='".$response_code."',
							`response_data`='".addslashes($result)."'
							WHERE `id`='".$logId."'";
				imw_query($sqlLog);
			}
			/*End Update Call response data*/

			/*Break execution if API returned with non favourable response code.*/
			if( $response_code !== 200 ) {
				if( $result !== '' ) {
				    $result = json_decode($result, true);
				    $erroResponse = $result['error'];
				    if(isset($erroResponse['message']) && !empty($erroResponse['message'])) {
				        $errorMessage = '<p><b>UGA API Error: '.$erroResponse['code'].' - '.$erroResponse['message'].'</b></p>';

				        $errors = $result['error']['errors'];
				        if(isset($errors) && !empty($errors)) {
				        	$errField = '';
				        	foreach ($errors as $key => $err) {
				        		if(isset($err['field']) && $err['field'] != '') {
				        			$errField = $err['field'].': ';
				        		}
					        	$errorMessage .= '<p>'.$errField.$err['message'].'</p>';
				        	}
				        }
				        throw new Exception( $errorMessage );
				    } else {
				        throw new Exception( 'UGA API Error: Service Unavailable' );
				    }
				} else {
				    throw new Exception( 'UGA API Error: Service Unavailable' );
				}
			}

			if( $result !== '' ) {
				// DECODE result data from json format
				$result = json_decode($result, true);
			}
			return $result;

		} else {
			throw new Exception("Request method not allowed to be null.");
		}
	}

	/**
     * Create Login URL
     */
    public function redirectUrl($uas_number)
    {
        return $this->loginURL.'?id='.$this->ccn.'&key='.$this->key.'&redirect=/navigation/'.$uas_number;
    }

}