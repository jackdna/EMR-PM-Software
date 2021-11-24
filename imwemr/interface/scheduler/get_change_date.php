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
File: get_change_date.php
Purpose: For Calendar Operation
Access Type: Direct
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
//scheduler object
$obj_scheduler = new appt_scheduler();

list($load_mn, $load_dt, $load_yr) = explode("-", $_REQUEST["load_dt"]);
$load_dt_mode = $_REQUEST["load_dt_mode"];
$inc_dec_no = $_REQUEST["inc_dec_no"];
$inc_dec_mode = $_REQUEST["inc_dec_mode"];
list($new_yr, $new_mn, $new_dt) = explode("-", $_REQUEST["load_this_date"]);

switch($load_dt_mode){
	case "this_date":
	case "new_date":
		$ts = mktime(0, 0, 0, $new_mn, $new_dt, $new_yr);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "current":
		echo date("Y-m-d;;l");
		$dd_option = date("Y-m-01");
		break;
	case "yesterday":
		$ts = mktime(0, 0, 0, $load_mn, $load_dt - 1, $load_yr);
		$dd_option = date("Y-m-01", $ts);
		echo date("Y-m-d;;l", $ts);
		break;
	case "tomorrow":
		$ts = mktime(0, 0, 0, $load_mn, $load_dt + 1, $load_yr);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "next_month":
		$load_dt = ($load_dt > 28 && ($load_mn + 1) == 2) ? $load_dt = 28 : $load_dt;
		$ts = mktime(0, 0, 0, $load_mn + 1, $load_dt, $load_yr);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "prev_month":
		$load_dt = ($load_dt > 28 && ($load_mn - 1) == 2) ? $load_dt = 28 : $load_dt;
		$ts = mktime(0, 0, 0, $load_mn - 1, $load_dt, $load_yr);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "next_year":
		$load_dt = ($load_dt > 28 && $load_mn == 2) ? $load_dt = 28 : $load_dt;
		$ts = mktime(0, 0, 0, $load_mn, $load_dt, $load_yr + 1);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "prev_year":
		$load_dt = ($load_dt > 28 && $load_mn == 2) ? $load_dt = 28 : $load_dt;
		$ts = mktime(0, 0, 0, $load_mn, $load_dt, $load_yr - 1);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "next_3_month":
		$load_dt = ($load_dt > 28 && ($load_mn + 3) == 2) ? $load_dt = 28 : $load_dt;
		$ts = mktime(0, 0, 0, $load_mn + 3, $load_dt, $load_yr);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "next_6_month":
		$load_dt = ($load_dt > 28 && ($load_mn + 6) == 2) ? $load_dt = 28 : $load_dt;
		$ts = mktime(0, 0, 0, $load_mn + 6, $load_dt, $load_yr);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "sel_date":
		$ts = mktime(0, 0, 0, $load_mn, $load_dt, $load_yr);
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
	case "go_to":
		$inc_dec_no = ($inc_dec_no == "") ? 0 : $inc_dec_no;
		switch($inc_dec_mode){			
			case "day":
				$ts = mktime(0, 0, 0, $load_mn, $load_dt + $inc_dec_no, $load_yr);
				break;
			case "week":				
				$ts = mktime(0, 0, 0, $load_mn, $load_dt, $load_yr);
				$ts = $ts + (86400 * 7 * $inc_dec_no);
				break;
			case "month":
				$load_dt = ($load_dt > 28 && ($load_mn + $inc_dec_no) == 2) ? $load_dt = 28 : $load_dt;
				$ts = mktime(0, 0, 0, $load_mn + $inc_dec_no, $load_dt, $load_yr);
				break;
			default:
				$ts = mktime(0, 0, 0, $load_mn, $load_dt, $load_yr);
				break;
		}
		echo date("Y-m-d;;l", $ts);
		$dd_option = date("Y-m-01", $ts);
		break;
}
echo ";;";
$arr_option = $obj_scheduler->generate_month_list($dd_option);
echo $arr_option[0];
?>