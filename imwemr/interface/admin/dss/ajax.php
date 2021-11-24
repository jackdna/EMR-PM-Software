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

/**
 * File: ajax.php
 * Purpose: Action File to save DSS credentials in the DB
 */

include_once '../../../config/globals.php';
if( !isset( $_POST['action'] ) || $_POST['action'] == '' )
{
	die( json_encode(array('message' => 'Invalid Request')) );
}

$action = $_POST['action'];
switch( $action ){
	
    case "saveCredentials":

		$accessCode	= trim( xss_rem( $_POST['accessCode'] ) );
		$verifyCode= trim( xss_rem( $_POST['verifyCode'] ) );
		$menuContext	= trim( xss_rem( $_POST['menuContext'] ) );
		$appURL	= 	urlencode( trim( $_POST['appUrl'] ) );
		$cid = (int) xss_rem( $_POST['record_id'] );
        $date = date('Y-m-d H:i:s');

        $query = '';
        $data = "
            `accessCode`= '$accessCode', 
            `verifyCode`= '$verifyCode', 
            `menuContext`= '$menuContext', 
            `url`= '$appURL' 
        ";
        $where = '';
        if(empty($cid) || $cid == '') {
            $sql = "INSERT INTO `dss_credentials` SET";
            $data .= ", `created_at` = '$date'";
        } else {
            $sql = "UPDATE `dss_credentials` SET";
            $data .= ", `updated_at` = '$date'";
            $where = "WHERE `id`=".$cid;
        }
        $query = $sql.$data.$where;
        $resp = imw_query($query);
        echo $resp;
		break;
}

?>