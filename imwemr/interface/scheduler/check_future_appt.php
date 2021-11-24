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
require_once(dirname(__FILE__).'/../../config/globals.php');

$ap_id = (isset($_REQUEST["ap_id"]) && !empty($_REQUEST["ap_id"])) ? $_REQUEST["ap_id"] : 0;

$return = "";
$patient_id = "";
if($ap_id > 0){
	$qry = "SELECT sa_app_start_date,sa_patient_id FROM schedule_appointments WHERE id = '".$ap_id."'";
	$res =imw_query($qry);
	if(imw_num_rows($res) > 0){
		
		$arr = imw_fetch_assoc($res);
		if($arr["sa_app_start_date"] <= date("Y-m-d")){
			$return = "justdoit";
		}
		$patient_id = $arr["sa_patient_id"];		
	}
}

if($patient_id != "")
{
	$req_qry = "SELECT clTeachId FROM clteach WHERE patient_id = '".$patient_id."' and schedule_cltech_chk = 'Yes'";
	$res = imw_query($req_qry);
	if(imw_num_rows($res) > 0)
	{
		$return .= '-clTeach';
	}
}

echo $return;
?>