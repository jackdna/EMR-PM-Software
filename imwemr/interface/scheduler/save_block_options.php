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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_label_functions.php');
//scheduler object
$obj_scheduler = new appt_scheduler();
$obj_label=new appt_label();

//getting variables
if($ap1 == 2 && $time_from_hour != 12){
	$time_from_hour +=  12;
}
if($ap2 == 2 && $time_to_hour != 12){
	$time_to_hour +=  12;
}
$start_loop_time =  $time_from_hour.":".$time_from_mins.":00";
$end_loop_time =  $time_to_hour.":".$time_to_mins.":00";
$load_dt = $_REQUEST["load_dt"];
$arr_fac = explode(",", $_REQUEST["loca"]);
$arr_pro= explode(",", $_REQUEST["prov"]);
$ap_tmp_id= $_REQUEST["ap_tmp_id"];
$block_mode = $_REQUEST["block_mode"];
$del_arr = array();

$start_loop_time_str = strtotime($start_loop_time);
$end_loop_time_str = strtotime($end_loop_time);

for($f = 0; $f < count($arr_fac); $f++){
	//echo $arr_fac[$f]."<br>";
	for($p = 0; $p < count($arr_pro); $p++){
		
		//clear cache
		//$file_name = "../scheduler_common/load_xml/".$load_dt."-".$arr_pro[$p].".sch";
		$file_name =$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml/".$load_dt."-".$arr_pro[$p].".sch";
		if(file_exists($file_name)){
			unlink($file_name);
		}
		
		$reqQry = "select * from block_times where provider = '".$arr_pro[$p]."' and facility = '".$arr_fac[$f]."' and (('".$start_loop_time."' >= start_time and '".$start_loop_time."' < end_time) or ('".$end_loop_time."' > start_time and '".$end_loop_time."' <= end_time) or ('".$end_loop_time."' = start_time and '".$end_loop_time."' = end_time) or ('".$start_loop_time."' < start_time and '".$end_loop_time."' > end_time)) and start_date = '".$load_dt."'";	
		$blocked_data_obj = imw_query($reqQry);

		while($bl_data = imw_fetch_assoc($blocked_data_obj))
		{
			$ust = ''; $uet = '';
			if($start_loop_time_str > strtotime($bl_data['start_time']))
			{
				$ust = $bl_data['start_time'];
				$uet = $start_loop_time;	
				$insQry = "INSERT INTO block_times set provider = '".$arr_pro[$p]."', 
				facility = '".$arr_fac[$f]."', 
				start_date = '".$load_dt."', 
				start_time = '".$ust."', 
				end_time = '".$uet."', 
				b_desc = '".$bl_data["b_desc"]."', 
				time_status = '".$bl_data["time_status"]."',
				temp_id = '".$ap_tmp_id."'";
				imw_query($insQry);
			}
			
			if($end_loop_time_str < strtotime($bl_data['end_time']))
			{
				$ust = $end_loop_time;
				$uet = $bl_data['end_time']; 
				$insQry = "INSERT INTO block_times set provider = '".$arr_pro[$p]."', 
				facility = '".$arr_fac[$f]."', 
				start_date = '".$load_dt."', 
				start_time = '".$ust."', 
				end_time = '".$uet."', 
				b_desc = '".$bl_data["b_desc"]."', 
				time_status = '".$bl_data["time_status"]."',
				temp_id = '".$ap_tmp_id."'";
				imw_query($insQry);				
			}
			
			$del_arr[] = $bl_data['id'];			
		}
		
		if($block_mode == "block" || $block_mode == "lock"){
			$qry = "INSERT INTO block_times set provider = '".$arr_pro[$p]."', 
			facility = '".$arr_fac[$f]."', 
			start_date = '".$load_dt."', 
			start_time = '".$start_loop_time."', 
			end_time = '".$end_loop_time."', 
			b_desc = '".addslashes($_REQUEST["comments"])."', 
			time_status = '".$block_mode."',
			temp_id = '".$ap_tmp_id."'";
			imw_query($qry);
		}
	}
}
if(count($del_arr) > 0){
	$delqry = "DELETE FROM block_times WHERE id IN ('".implode("','", $del_arr)."')";
	imw_query($delqry);
}

if($block_mode == "block"){ //do not execute in case of locking
	//--- Appointments Move To To-Do -------
	$qry = "select id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, sa_doctor_id, sa_facility_id from schedule_appointments where sa_doctor_id in (".$_REQUEST["prov"].") and sa_facility_id in (".$_REQUEST["loca"].") and sa_app_starttime  >= '".$start_loop_time."' and sa_app_starttime  < '".$end_loop_time."' and '".$load_dt."' between sa_app_start_date and sa_app_end_date and sa_patient_app_status_id NOT IN (203,201,18,19,20)";
	$res = imw_query($qry);
	if(imw_num_rows($res) > 0){
		while($arr=imw_fetch_assoc($res)){
			//release labels code start here
			$qry3=imw_query("select id, labels_replaced, label_group from scheduler_custom_labels where start_date = '".$arr["sa_app_start_date"]."' and (start_time BETWEEN '$arr[sa_app_starttime]' AND  '$arr[sa_app_endtime]')  and provider = '".$arr["sa_doctor_id"]."' and facility = '".$arr["sa_facility_id"]."'");
			while($res3=imw_fetch_assoc($qry3)){
				$lbl_replaced_array = array();
				$lbl_replaced_array = explode('::',$res3['labels_replaced']);
				foreach($lbl_replaced_array as $lbl_replaced_entity)
				{
					$lbl_replaced_entity = trim($lbl_replaced_entity);
					if($lbl_replaced_entity!="")
					{
							list($lbl_replaced_appt_id,$get_lbl_replaced) = explode(':',$lbl_replaced_entity); 
							$get_lbl_replaced = trim($get_lbl_replaced);
						//echo "checking $arr[id] == $lbl_replaced_appt_id \n";
							if($arr['id'] == $lbl_replaced_appt_id)
							{

								$target_replace = '::'.$arr['id'].':'.$get_lbl_replaced;
								$rsc_update_qry = 'UPDATE scheduler_custom_labels SET l_show_text = concat(TRIM(l_show_text),if(TRIM(l_show_text)="","'.$get_lbl_replaced.'","; '.$get_lbl_replaced.'")), labels_replaced = replace(labels_replaced,"'.$target_replace.'","") WHERE id ='.$res3['id'];
								imw_query($rsc_update_qry);
								break;

							}							

					}
				}
			}
			//release labels code ends here
			//logging this action in previous status table
			$obj_scheduler->logApptChangedStatus($arr["id"], "", "", "", "201", "", "", $_SESSION['authUser'], "Blocked Time.", "", false);
			//updating schedule appointments details
			$obj_scheduler->updateScheduleApptDetails($arr["id"], "", "", "", "201", "", "", $_SESSION['authUser'], "Blocked Time.", "", false);
		}
	}
}elseif($block_mode == "open" && $_REQUEST["comments"]=='Blocked'){ //do not execute in case of locking
	//--- Appointments Move To To-Do -------
	$qry = "select id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, sa_doctor_id, sa_facility_id, sch_template_id, CONCAT(sa_app_start_date,'-',sa_doctor_id,'.sch') as sch_file_name from schedule_appointments where sa_doctor_id in (".$_REQUEST["prov"].") and sa_facility_id in (".$_REQUEST["loca"].") and sa_app_starttime  >= '".$start_loop_time."' and sa_app_starttime  < '".$end_loop_time."' and '".$load_dt."' between sa_app_start_date and sa_app_end_date and sa_patient_app_status_id IN (201)";
	$res = imw_query($qry);
	if(imw_num_rows($res) > 0){
		while($arr=imw_fetch_assoc($res)){
			if(!$arr["sch_template_id"])
			{	
				//==To collect all providers schedule for restore appointment dates ====///
				$providers_all_schedule=get_chache_file_provider_schedule(array($arr["sch_file_name"]));
				//get template id from provider schedule
				$arr["sch_template_id"]=$providers_all_schedule[$arr["sa_doctor_id"]][$arr["sa_facility_id"]][$arr["sa_app_start_date"]][$arr["sa_app_starttime"]];
				//update it in appointment table
				imw_query("UPDATE schedule_appointments SET sch_template_id = '".$arr["sch_template_id"]."' WHERE id = '".$arr['id']."'");
			}
			//logging this action in previous status table
			$obj_scheduler->logApptChangedStatus($arr["id"], "", "", "", "0", "", "", $_SESSION['authUser'], "Blocked Time Removed.", "", false);
			//updating schedule appointments details
			$obj_scheduler->updateScheduleApptDetails($arr["id"], "", "", "", "0", "", "", $_SESSION['authUser'], "Blocked Time Removed.", "", false);
			//replace labels
			$obj_label->replaceLabel($arr["sa_app_starttime"], $arr["sa_app_endtime"], $arr["sa_app_start_date"], $arr["sa_doctor_id"], $arr["sa_facility_id"], $arr["id"],$arr["sch_template_id"]);
		}
	}
}

//setting date format
list($yr, $mn, $dt) = explode("-", $_REQUEST["load_dt"]);
echo $_REQUEST["load_dt"];
echo "~~~~~";
echo date("l", mktime(0, 0, 0, $mn, $dt, $yr));
?>