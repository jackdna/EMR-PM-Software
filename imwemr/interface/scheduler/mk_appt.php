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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_cn_functions.php');

//class objects
$obj_scheduler = new appt_scheduler();
$obj_chartnotes = new appt_chartnotes($_SESSION['authId'], $_REQUEST["pt_id"]);

//echo($_REQUEST["pt_id"]);
$follow_up_numeric = "";
$follow_up_string = "";
$follow_up_comments = "";
$follow_up_proc = "";

$qry = "SELECT cap.followup, cap.follow_up, cap.follow_up_numeric_value, cap.plan_notes FROM `chart_assessment_plans` as cap LEFT JOIN chart_master_table cmt ON cmt.id = cap.form_id LEFT JOIN chart_left_cc_history clch ON clch.form_id = cap.form_id WHERE cap.patient_id = '".$_REQUEST["pt_id"]."' ORDER BY cmt.date_of_service DESC,cmt.id DESC LIMIT 0,1";

$res = imw_query($qry);
if(imw_num_rows($res) > 0){
	$arr = imw_fetch_assoc($res);
	
	$followup = $arr["followup"];
	
	//backward compatibilty
	if(!empty($arr["followup"])){
		list($len_arrFu, $arrFu) = $obj_chartnotes->read_fu_xml($arr["followup"]);
		if(count($arrFu) > 0){
			foreach($arrFu as $val){
				$follow_up_numeric = $val["number"];
				$follow_up_string = $val["time"];
				$follow_up_proc = $val["visit_type"];
				break;
			}
		}
	}else if($arr["follow_up_numeric_value"]) {
		$follow_up_numeric = $arr["follow_up_numeric_value"];
		$follow_up_string = $arr["follow_up"];
		$follow_up_proc = "";
	}
	$follow_up_comments = $arr["plan_notes"];
}

$date_value = "";
list($tdt, $tmn, $tyr) = explode("-", date("d-m-Y"));

if($follow_up_string != ""){
	$plus_extra_day=1;
	switch($follow_up_string){
		case "Days":
			$date_value = date("Y-m-d", mktime(0, 0, 0, $tmn, (int)$tdt + (int)$follow_up_numeric + (int)$plus_extra_day, $tyr));
			break;
		case "Weeks":
			$date_value = date("Y-m-d", mktime(0, 0, 0, $tmn, (int)$tdt + ((int)$follow_up_numeric * 7), $tyr));
			break;
		case "Months":
			$date_value = date("Y-m-d", mktime(0, 0, 0, (int)$tmn + (int)$follow_up_numeric, $tdt+ (int)$plus_extra_day, $tyr));
			break;
		case "Year":
			$date_value = date("Y-m-d", mktime(0, 0, 0, $tmn, $tdt, $tyr + (int)$follow_up_numeric));
			break;
	}
	if($follow_up_proc != ""){
		$qry = "SELECT id FROM slot_procedures WHERE proc = '".$follow_up_proc."'";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			echo $date_value."||||".date("l", strtotime($date_value))."||||".$follow_up_comments."||||".$arr["id"];
		}else{
			echo $date_value."||||".date("l", strtotime($date_value))."||||".$follow_up_proc."; ".$follow_up_comments."||||";
		}
	}else{
		echo $date_value."||||".date("l", strtotime($date_value))."||||".$follow_up_comments."||||";
	}
}else{
	echo "no_response";
}
?>