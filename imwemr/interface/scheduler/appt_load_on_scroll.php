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

//checking admin previleges
$admin_priv = (core_check_privilege(array("priv_admin")) == true) ? 1 : 0;
$sch_overrider_privilege=(core_check_privilege(array("priv_Sch_Override")) == false) ? 0 : 1;

// ARRAY USED TO DISPLAY HOURS 	
$time_array=array("12 AM","01 AM","02 AM","03 AM","04 AM","05 AM","06 AM","07 AM","08 AM","09 AM","10 AM","11 AM","12 PM","01 PM","02 PM","03 PM","04 PM","05 PM","06 PM","07 PM","08 PM","09 PM","10 PM","11 PM" );

//getting and setting dates
$working_day_dt = (isset($_GET['dt']) && !empty($_GET['dt'])) ? $_GET['dt'] : date("Y-m-d");

$arr_selected_prov = explode(",", $_REQUEST["prov"]);

//to set scroll settings
$target_dat = "";
$tim_cur = "";
if(isset($_REQUEST["appt_id"]) && !empty($_REQUEST["appt_id"])){
	$arr_scroll = $obj_scheduler->get_scroll_settings($_SESSION["authId"], $_REQUEST["appt_id"]);
}else{
	$arr_scroll = $obj_scheduler->get_scroll_settings($_SESSION["authId"]);
}
if($arr_scroll !== false){
	$target_dat = $arr_scroll["SCROLL_DATE"];
	$tim_cur = $arr_scroll["SCROLL_TIME"];
}

$arr_xml = $obj_scheduler->read_prov_working_hrs($working_day_dt, $arr_selected_prov);
$obj_scheduler->schScrollWidthFlag = 1;
if($arr_xml === false){
	echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:775px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">Office is closed.</div>____CLOSED____0____';
	die();
}

$start_time = "00:00";
if(is_array($arr_xml) && count($arr_xml) > 0){
	foreach($arr_xml as $k => $v){
		if($k != "dt" && isset($v["slots"])){
			foreach($v["slots"] as $sl_id => $sl_detail){
				$arr_sl_id = explode("-", $sl_id);
				$start_time = $arr_sl_id[0];
				break;
			}
			break;
		}
	}
}
$hr1 = substr($start_time, 0, 2);

$arr_selected_fac = array();
if(isset($_REQUEST["loca"]) && !empty($_REQUEST["loca"])){

	if(isset($_REQUEST["prov"]) && !empty($_REQUEST["prov"])){

		$arr_selected_fac = explode(",", $_REQUEST["loca"]);
		$arr_selected_prov = explode(",", $_REQUEST["prov"]);
		list($str_header, $str_time_pane, $str_appt_slots, $str_proc_summary, $total_prov, $arr_widths, $arr_not_processed, $arr_processed) = $obj_scheduler->write_html_content($arr_xml, $arr_selected_fac, $arr_selected_prov, $admin_priv, $time_array, "main", $sch_overrider_privilege);
		echo $str_header.'____'.$str_appt_slots;
	}
}
?>