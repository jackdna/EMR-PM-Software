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
/*
Purpose: Get latest encounters list
Access Type: Direct
*/
require_once(dirname(__FILE__).'/../../config/globals.php');

//schedule_appointments
$pid = $_REQUEST['pid'];
$app_id = $_REQUEST['app_id'];

//check in superbill base on appt Id
$enco_id="";
$encoQry = "SELECT encounterId  FROM superbill WHERE sch_app_id ='".$app_id."' AND patientId='".$pid."' AND del_status='0' and merged_with='0' ";
$encoSql = imw_query($encoQry);
if(imw_num_rows($encoSql)>0){
	$enco_result= imw_fetch_array($encoSql);
	$enco_id = $enco_result["encounterId"];
}
if(empty($enco_id)){
	//to get encounter latest id DOS
	$encoQry = "SELECT sb.encounterId FROM schedule_appointments sa LEFT JOIN superbill sb ON sa.sa_app_start_date = sb.dateOfService  where sa.sa_patient_id = sb.patientId AND sa.sa_patient_id=".$pid." AND sa.id=".$app_id." and merged_with='0' ORDER BY sb.dateOfService DESC, sb.timeSuperBill DESC LIMIT 0,1";
	$encoSql = imw_query($encoQry);
	if(imw_num_rows($encoSql)>0){
		$enco_result= imw_fetch_array($encoSql);
		$enco_id = $enco_result["encounterId"];
	}else{
		$enco_id = "";
	}
}
echo $enco_id;
?>