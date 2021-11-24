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

// Currently, We are store only the request parameters to our API Log file.

$ignoreAuth = true;
require_once("../../config/globals.php");

if(isDssEnable() === false) {
    echo '<script>alert("Missing DSS Settings")</script>';
}

if($json = json_decode(file_get_contents("php://input"), true)) {
    $data = $json;
} else {
    $data = $_REQUEST;
}

$headers = getallheaders();

file_put_contents(data_path().'dss_auth_log.txt', "API Call on dated: ".date('Y-m-d H:i:s')."\n\nHeader Data\n".print_r($headers, true)."\nRequest Data\n".print_r($data, true)."\n--------------------------------\n\n", FILE_APPEND);


// New Login
$token = '';
if(empty($token) || $token == '' || $token == false) {
    include_once( dirname(__FILE__).'/../login/dss_login_alt.php' );
} else {
    include_once( dirname(__FILE__).'/../login/index.php' );
}
?>