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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

//scheduler object
$obj_scheduler = new appt_scheduler();

//getting variables
if($ap1 == 2 && $time_from_hour != 12){
	$time_from_hour +=  12;
}
if($ap2 == 2 && $time_to_hour != 12){
	$time_to_hour +=  12;
}
$start_loop_time = $time_from_hour.":".$_REQUEST['time_from_mins'].":00";
$end_loop_time =  $time_to_hour.":".$_REQUEST['time_to_mins'].":00";
$load_dt = $_REQUEST["load_dt"];
$fac_ids = $_REQUEST["loca"];
$phy_id= $_REQUEST["phy_id"];

$del_arr = array();
//clear cache
$order_file=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml/".$load_dt."-".$phy_id.".sch";
if(file_exists($file_name)){
	unlink($file_name);
}

//--- Appointments Move To To-Do -------
$qry = "select id, sa_patient_id, sa_app_start_date from schedule_appointments where sa_doctor_id = ".$phy_id." and sa_facility_id IN (".$fac_ids.") and sa_app_starttime  >= '".$start_loop_time."' and sa_app_starttime  < '".$end_loop_time."' and '".$load_dt."' between sa_app_start_date and sa_app_end_date";
$res = imw_query($qry);
if(imw_num_rows($res) > 0){
	while($tmpData=imw_fetch_assoc($res))
	{
		$arr[]=$tmpData;	
	}
	for($sa = 0; $sa < count($arr); $sa++){
		//logging this action in previous status table
		$obj_scheduler->logApptChangedStatus($arr[$sa]["id"], "", "", "", "201", "", "", $_SESSION['authUser'], "Blocked Time.", "", false);
		//updating schedule appointments details
		$obj_scheduler->updateScheduleApptDetails($arr[$sa]["id"], "", "", "", "201", "", "", $_SESSION['authUser'], "Blocked Time.", "", false);
	}
}

//setting date format
list($yr, $mn, $dt) = explode("-", $_REQUEST["load_dt"]);
echo $_REQUEST["load_dt"];
echo "~~~~~";
echo date("l", mktime(0, 0, 0, $mn, $dt, $yr));
?>