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

//setting date format
list($yr, $mn, $dt) = explode("-", $_REQUEST["load_dt"]);
//echo $mn."/".$dt."/".$yr."~~~~~";
echo get_date_format($_REQUEST["load_dt"],'','','',"/")."~~~~~";
$prov_specific = array();
$qry_sch = "select sp.acronym, sp.proc, sa.sa_doctor_id, sa.procedureid, doc.fname, doc.lname, doc.mname from schedule_appointments sa left join slot_procedures sp on sp.id = sa.procedureid left join slot_procedures sp2 on sp2.id = sp.proc_time left join schedule_status st on st.id = sa.sa_patient_app_status_id left join users doc on doc.id = sa.sa_doctor_id where sa_facility_id IN (".$_REQUEST["sel_fac"].") and sa_doctor_id IN (".$_REQUEST["sel_pro"].") and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) and '".$_REQUEST["load_dt"]."' between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime, sa.sa_app_time desc";
$res_sch = imw_query($qry_sch);
if(imw_num_rows($res_sch) > 0){
	while($arr_sch_raw = imw_fetch_assoc($res_sch))
	{
		$prov_specific[$arr_sch_raw["sa_doctor_id"]][] = $arr_sch_raw;
	}
}
$arr_sel_pro = explode(",", $_REQUEST["sel_pro"]);
$proc_arr = array();
for($l = 0; $l < count($arr_sel_pro); $l++){
	$arr_sch = $prov_specific[$arr_sel_pro[$l]];
	//print_r($arr_sch);
	$duplicate_proc_arr = array();
	for($p = 0; $p < count($arr_sch); $p++){
		if(!in_array($arr_sch[$p]["procedureid"], $duplicate_proc_arr)){
			$proc_arr[$arr_sel_pro[$l]][$arr_sch[$p]["procedureid"]]["doc"] = core_name_format($arr_sch[$p]["lname"], $arr_sch[$p]["fname"], $arr_sch[$p]["mname"]);
			$proc_arr[$arr_sel_pro[$l]][$arr_sch[$p]["procedureid"]]["proc"] = ($arr_sch[$p]["acronym"] != "") ? $arr_sch[$p]["acronym"] : $arr_sch[$p]["proc"];
			$proc_arr[$arr_sel_pro[$l]][$arr_sch[$p]["procedureid"]]["cnt"] = 1;
			array_push($duplicate_proc_arr, $arr_sch[$p]["procedureid"]);
		}else{
			$proc_arr[$arr_sel_pro[$l]][$arr_sch[$p]["procedureid"]]["cnt"]++;	
		}
	}
}
$str_return ='';
if(count($proc_arr) > 0){
	foreach($proc_arr as $k => $v){
		$cnt = 0;
		$str_return .="<div id=\"docDiv\" class=\"fl\">";
		if(is_array($v) && count($v) > 0){

			foreach($v as $detail){
				if($cnt == 0){
					$str_return .= "<div class=\"sc_line\"></div><div class=\"fl ml5\" style=\"width:185px;text-align:left\"><hr><br><div class=\"sc_title_font\">".$detail["doc"]."</div><br><hr><br><br>";		
				}
				$cnt = $cnt + $detail["cnt"];
				$str_return .= "<div class=\"fl sc_detail_font\" style=\"width:130px;\">".$detail["proc"]."</div><div class=\"fl sc_detail_font\" style=\"width:50px;\">".$detail["cnt"]."</div><br>";
			}
		}
		$str_return .= "<hr><br><div class=\"fl sc_title_font\" style=\"width:130px;\">Total</div><div class=\"fl sc_title_font\" style=\"width:50px;\">".$cnt."</div><br><hr><br></div>";
	}
}else{
	$str_return .= "
<div style=\"width:100%;\"><br><br><br><br>No Appointments</div>
";
}
echo $str_return;
echo "~~~~~";
?>