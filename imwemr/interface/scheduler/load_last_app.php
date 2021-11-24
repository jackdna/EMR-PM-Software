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

$qry = "SELECT sa.id, sa.sa_app_start_date FROM schedule_appointments sa left join slot_procedures sp ON sp.id = sa.procedureid left join users u ON u.id = sa.sa_doctor_id left join facility f ON f.id = sa.sa_facility_id where sa_patient_id = '".$_REQUEST["pid"]."' and sa_patient_app_status_id IN(201) ORDER BY sa.id DESC LIMIT 1";
$res = imw_query($qry);
$arr = array(0 => "");
if(imw_num_rows($res)> 0){
	$arr = imw_fetch_assoc($res);		
}
list($yr, $mn, $dt) = explode("-", $arr["sa_app_start_date"]);
$day_name = date("l", mktime(0, 0, 0, $mn, $dt, $yr));
echo $arr["id"]."~~".$_REQUEST["pid"]."~~".$arr["sa_app_start_date"]."~~".$day_name;
?>