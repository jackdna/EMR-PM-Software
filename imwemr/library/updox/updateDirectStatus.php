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

$ignoreAuth = true;
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once( dirname(__FILE__).'/updoxFax.php' );
$updoxFax=new updoxFax();
/*Allow Request only from updox*/
if($updoxFax->validateInboundIP()){
	
	$data = trim(file_get_contents("php://input"));	/*Get Request payloads*/
	
	/*Log Call Data*/
	$logDirPath = dirname(__FILE__);
	if( !is_dir($logDirPath) )
	{
		mkdir( $logDirPath, 0755, true );
		chown( $logDirPath, 'apache' );
	}
	$logFile = $logDirPath.'/log_'.date('Y_m_d').'.txt';
	$logData = $parameters;
	$logData['auth']['applicationPassword'] = '[OMITTED]';
	$logData = print_r($data, true);
	
	file_put_contents($logFile, date('Y-m-d H:i:s')."\n", FILE_APPEND);
	file_put_contents($logFile, $endpoint."\n", FILE_APPEND);
	file_put_contents($logFile, $logData."\n", FILE_APPEND);
	/*End Call Data Log*/
	
	if( $data !== '' ) {
		
		$data = json_decode($data);
		$status = imw_real_escape_string( trim($data->mdnStatus) );
		$msg_id = preg_replace("/[^0-9]/", "", $data->messageId);
		
		/*Update Fax Status in Database*/
		if( $status !== '' && $msg_id !== '' ){
			$sql = "INSERT INTO `direct_messages_log` SET `updox_message_id`='".$msg_id."', `status`='".$status."', entered_date_time='".date('Y-m-d H:i:s')."'";
			imw_query($sql);
		}
	}
}
 ?>