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
Purpose: Mark status of procedure templates - Active, Deactive
Access Type: Direct
*/

include_once("../../../../config/globals.php");

$mode = $_REQUEST['mode'];
if($mode!="del"){
	$mode = ($mode == "yes") ? "no" : "yes";
}
$pro_id = $_REQUEST['pro_id'];
if(isset($_REQUEST['pro_id'])){
	$counter  = 0;
	$pro_id = explode(',',$_REQUEST['pro_id']);
	foreach($pro_id as $key => $val){
		$updQry = "UPDATE slot_procedures SET active_status = '".$mode."' WHERE id = '".$val."' OR procedureId = '".$val."'";
		imw_query($updQry);
		$counter = $counter + imw_affected_rows();
	}
}
//$updQry = "UPDATE slot_procedures SET active_status = '".$mode."' WHERE id = '".$pro_id."'";

//---
/*
 * ERP PATIENT PORTAL CLINICAL SUMMARY API WORK STARTS HERE
 * PATIENT_SUMMARY IS API MAIN FILE
 * isERPPortalEnabled IS FUNCTION CALLED FROM common_functions.php TO CHECK ERP ACCOUNT ENABLE OR DISABLE
 */
$erp_error=array();
if(isERPPortalEnabled())
{
	try {
		include_once($GLOBALS['srcdir'].'/erp_portal/appointments.php');
		$oAppointments = new Appointments();
		$oAppointments->addUpdateAppointmentRequestReasons($pro_id);
	} catch(Exception $e) {
		$erp_error[]='Unable to connect to ERP Portal';
	}
}
//--

echo $counter;
?>
