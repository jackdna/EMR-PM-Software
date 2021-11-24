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

//include_once ('../../../../config/globals.php');

class Dss_api {
    
    private $api_url = false;

    function __construct() {
        $this->api_url='http://localhost:3003/api/';

        Dss_api::validateToken();
    }


    public function validateToken() {
            $this->auth();
    }

    //Authentication
    public function auth() {
        $params = array();
        $params['accessCode'] = "prog999-";
        $params['verifyCode'] = "prog1234-";
        $params['menuContext'] = "VEJD PCE RECORD MANAGER";

        //$result = $this->CURL($params);
        $result='{
            "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzb1NldHVwIjp7InNlcnZlck5hbWUiOiJERVZNSUFQTDAzVjA0Iiwidm9sdW1lIjoiUk9VIiwiVUNJIjoiVkFIIiwiZGV2aWNlIjoiLy8uL251bDoxODg4IiwiYXR0ZW1wdENvdW50ZXIiOiIzIiwic2tpcFNpZ25PbiI6ZmFsc2UsImRvbWFpbk5hbWUiOiJTTUEuRk8tQUxCQU5ZLk1FRC5WQS5HT1YiLCJpc1Byb2R1Y3Rpb24iOmZhbHNlfSwic29Mb2dpbiI6eyJsb2dpbkRVWiI6IjEwMDAwMDAwMDM0IiwiYXV0aE1ldGhvZCI6IkFWIiwiYWNjb3VudExvY2tlZCI6ZmFsc2UsImF1dGhGYWlsZWQiOmZhbHNlLCJjdmNGbGFnIjpmYWxzZSwiZXJyb3JUZXh0IjoiIiwicGFybTQiOiIwIiwic3J2TXNnIjoiIiwic3J2TXNnVXNlZnVsIjpmYWxzZSwic2Vzc2lvblRpbWVvdXQiOjM2MDAwMDB9LCJkaXZpc2lvbkxpc3QiOlt7ImRFSU4iOiI1MDAiLCJkTmFtZSI6IlZBTUMgQUxCQU5ZIiwiZElkIjoiNTAwIn0seyJkRUlOIjoiMTcwMDciLCJkTmFtZSI6IkFMQi1QUlJUUCIsImRJZCI6IjUwMFBBIn1dLCJzb1VzZXJJbmZvIjp7InVEVVoiOiIxMDAwMDAwMDAzNCIsInVOYW1lIjoiUk9JU1RBRkYsQ0hJRUYgTyIsInVTdGFuZGFyZE5hbWUiOiJDaGllZiBPIFJvaXN0YWZmIFRIRSBNQU4iLCJ1RGl2aXNpb24iOiI1MDBeVkFNQyBBTEJBTlleNTAwIiwidVRpdGxlIjoiQ09NUFVURVIgU1BFQ0lBTElTVCIsInVTZXJ2aWNlU2VjdGlvbiI6IklORk9STUFUSU9OIFNZU1RFTVMgQ0VOVEVSIiwidUxhbmd1YWdlIjoiMSIsInVEVGltZSI6Ijk5OTk5IiwidVZQSUQiOiIifSwiYXV0aGVudGljYXRlZCI6dHJ1ZSwic2lkIjoiNTA5YTA3MmYtNTQwYi00ZDU3LWJiYjItNWVlZTZjN2Y4ZTEwIiwic2VydmVySWQiOiJbZGVmYXVsdF0iLCJpYXQiOjE1NDM0NjYyNzksImV4cCI6MTU0MzQ4NDI3OSwiaXNzIjoiVlNPQTpTZXJ2ZXphIn0.Oql794WG9t_HV6d0pVT8Ze9zBu8hl81t-Euc2FJqiOY",
            "authFailed": false,
            "soLogin": {
              "loginDUZ": "10000000034",
              "authMethod": "AV",
              "accountLocked": false,
              "authFailed": false,
              "cvcFlag": false,
              "errorText": "",
              "parm4": "0",
              "srvMsg": "",
              "srvMsgUseful": false,
              "sessionTimeout": 3600000
            }
          }';
        $result = json_decode($result, true);
pre($result); die;
        $response = array();
        
        if ($result) {
            if(isset($result['authFailed']) && $result['authFailed']==true){
                $response['errorText']=$result['soLogin']['errorText'];
            } else if(isset($result['authFailed']) && $result['authFailed']==false) {
                $response['token']=$result['token'];
                $response['authFailed']=$result['authFailed'];
                $response['errorText']=$result['token']['errorText'];
                $response['sessionTimeout']=$result['soLogin']['sessionTimeout'];
            }
            
        } else {

            if ($this->curl_error_no) {

                $error_msg = 'Error - ' . $this->curl_error_no . ' - ' . $this->curl_error_msg;
                $this->handle_error($error_msg);
            } else {
                throw new Exception('No data returned');
            }
        }
    }

    // Common CURL Request fucntion 
    private function CURL($params) {

        $payload = json_encode($params);

        $request_headers = array();
        $request_headers[] = 'Accept:application/json';
        $request_headers[] = 'Content-Type:application/json';

        // Reset Curl message before each request
        $this->reset_curl_msg();

        // API End Point
        $url = $this->api_url.'auth';

        // Initiate Curl 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
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


        // DECODE result data from json format
        $result = json_decode($result, true);

        if (curl_errno($ch)) {
            // If error then set curl message
            $this->set_curl_msg($ch);
        }

        // Close Curl Request
        curl_close($ch);

        return $result;
    }

    private function set_curl_msg($curl) {

        $this->curl_error_no = curl_errno($curl);
        $this->curl_error_msg = curl_error($curl);
    }

    private function reset_curl_msg() {

        $this->curl_error_no = false;
        $this->curl_error_msg = false;
    }

    private function handle_error($msg) {

        if (trim($msg))
            die($msg);
    }

}


//END CLASS
?>