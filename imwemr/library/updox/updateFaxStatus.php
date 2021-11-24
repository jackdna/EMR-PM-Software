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
	if( $data !== '' ) {
		
		$data = json_decode($data);
		$status = imw_real_escape_string( trim($data->status) );
		$fax_id = preg_replace("/[^0-9]/", "", $data->faxId);
		
		/*Update Fax Status in Database*/
		if( $status !== '' && $fax_id !== '' ){
			$sql = "UPDATE `send_fax_log_tbl` SET `updox_status`='".$status."' WHERE `updox_id`='".$fax_id."'";
			imw_query($sql);
		}
        
        // IM-5479:- Enhancement #23 - Tasking of failed outbound faxes
        if( $status !== '' && $fax_id !== '' && strtolower($status) !== 'success' ){
            require_once(dirname(__FILE__)."/../../interface/common/assign_new_task.php");
            outbound_fax_status_task($data,$fax_id,$status);
        }
	}
}
?>