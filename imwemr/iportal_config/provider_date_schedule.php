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

$ignoreAuth=true;
include_once("../config/globals.php");
if($_REQUEST['IPORTAL_REQUEST']!=(md5(constant("IPORTAL_SERVER")))){
	die("[Error]:401 Unauthorized Access ");
}
include_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_schedule_functions.php");

/*$selectedFacility = "1";
$selectedDate = "2015-12-15";
$selectedProvider = "97";
$selectedProcedure = "97";*/

$selectedFacility = $_REQUEST['facility'];
$selectedDate = $_REQUEST['date'];
$selectedProvider = $_REQUEST['provider'];
$selectedProcedure = $_REQUEST['procedure'];
$week_status=$_REQUEST['week'];
$arr_dates=array();
$arr_dates[]=$selectedDate;
if($week_status==1){
	for($d=1;$d<=7;$d++){
		$arr_dates[]=date("Y-m-d",strtotime("+".$d." day", strtotime($selectedDate)));	
	}
}
$slots = array(); /*Available time slots*/

//db object
$obj_db = $GLOBALS["adodb"]["db"];

//scheduler object
$obj_scheduler = new appt_scheduler();

foreach($arr_dates as $date_val){
	$selectedDate=$date_val;
	$selectedDate1 = str_replace("-","_", $selectedDate);
	list($calYearNumber,$calMonthNumber,$calDateNumber)=explode("-",$selectedDate);
$defaultTimeSlot = constant('DEFAULT_TIME_SLOT')*60;	//Default time slot seconds

$qryGetSP = "select spa.times as procTime, spb.id as procId from slot_procedures spa, slot_procedures spb 
where (spb.id) = ('".$selectedProcedure."') and spa.doctor_id = '0' and spb.proc_time = spa.id 
ORDER BY spb.id ASC LIMIT 1";
$procDuration = "";
$rsGetSP = imw_query($qryGetSP);
if($rsGetSP && imw_num_rows($rsGetSP)>0){
	$rsGetSP = imw_fetch_assoc($rsGetSP);
	$intAppTimeDuration = (int)$rsGetSP['procTime'];
	$procDuration = $intAppTimeDuration * 60;
}

$defaultTimeSlot1 = ($procDuration!="")?$procDuration:$defaultTimeSlot;
$defaultTimeSlot = $defaultTimeSlot1;


/*error_reporting(E_ERROR);
ini_set('display_errors', 1);*/
	
	$arr_prov_sch = $obj_scheduler->get_provider_schedules($selectedDate, array($selectedProvider));
	$arr_sch_tmp_id = array();
	$arr_tmp_fac_rel = array();
	$arr_avail_admin_slots=array();
	for($i = 0; $i < count($arr_prov_sch); $i++){
		if($arr_prov_sch[$i]['iportal_enable_slot']==""){continue;}
		//if($arr_prov_sch[$i]["facility"]!=$selectedFacility){continue;}
		$arr_avail_admin_slots[]=$arr_prov_sch[$i]['iportal_enable_slot'];
		$arr_sch_tmp_id[] = $arr_prov_sch[$i]["sch_tmp_id"];
		$arr_tmp_fac_rel[$arr_prov_sch[$i]["sch_tmp_id"]] = $arr_prov_sch[$i]["facility"];
		$slots[$selectedDate1]["facility"]=$arr_prov_sch[$i]["facility"];
	}
	$str_sch_tmp_id = join("','", $arr_sch_tmp_id);
	/*$arr_prov_sch = $arr_prov_sch[0];*/

	if(trim($str_sch_tmp_id) != ""){
		$str_sch_tmp_id = "'".$str_sch_tmp_id."'";
	}
	//getting timings for office
	$int_this_fac_id = 0;
	$arr_templates = array();
	if(trim($str_sch_tmp_id) != ""){
		$arr_all_tmp = array();
		$str_tmp = "select id, morning_start_time, morning_end_time from schedule_templates where id in (".$str_sch_tmp_id.") order by id";
		$res_tmp = get_array_records_query($str_tmp);
		if( count($res_tmp) > 0 ){
			$arr_templates = $res_tmp;
		}
	}
	
	foreach($arr_templates as $key=>$arr_tmp){ /*Loop templates for provider on same at same facility*/
		//$arr_tmp = (count($arr_templates)>0)?$arr_templates[0]:array();
		if(count($arr_tmp)>0){
			$templateId = $arr_tmp['id'];
			$startTime = $arr_tmp['morning_start_time'];
			$endTime = $arr_tmp['morning_end_time'];
			
			// Blocked time slots
			$mainBlockSlots = array();
			$resBlock = imw_query("SELECT * FROM block_times where provider = '".$selectedProvider."' and time_status in ('block', 'lock') 
			and start_date = '".$selectedDate."'") ;
			if($resBlock && imw_num_rows($resBlock)>0){
				$i = 1;
				while($row = imw_fetch_assoc($resBlock)){
					$mainBlockSlots[$i]['start'] = strtotime($row['start_time']);
					$mainBlockSlots[$i]['end'] = strtotime($row['end_time']);
					$i++;
				}
			}
			
			$lbl = imw_query("SELECT `start_time`, `end_time` FROM `schedule_label_tbl` WHERE `sch_template_id`='".$templateId."' AND `label_type`='Lunch'");
			$lunch_time = array();
			if($lbl && imw_num_rows($lbl)>0){
				$i = 1;
				while($row = imw_fetch_assoc($lbl)){
					$lunch_time[$i]['start'] = strtotime($row['start_time']);
					$lunch_time[$i]['end'] = strtotime($row['end_time']);
					$i++;
				}
			}
			
			/*print_r($lunch_time);*/
			//$slots['diff'] = $defaultTimeSlot1; /*Duration of time slot available*/
			//$slots['templates'][$key]['id'] = $templateId;
			$slots[$selectedDate1]['template_id'] = $templateId;
	
			$sql = "SELECT `id`, `sa_app_starttime`, `sa_app_endtime`, `sa_app_duration`, `sa_app_start_date`, `sa_app_end_date` FROM `schedule_appointments` WHERE `sa_doctor_id`='".$selectedProvider."' AND `sa_patient_app_status_id`NOT IN('18','203') AND `sa_facility_id`='".$selectedFacility."' AND `sa_app_start_date`='".$selectedDate."' AND `sch_template_id`='".$templateId."'";
			$data = imw_query($sql);
	
			$bookings = array();
			if($data && imw_num_rows($data)>0){
				$i = 1;
				while($row = imw_fetch_assoc($data)){
					$bookings[$i]['start'] = strtotime($row['sa_app_starttime']);
					$bookings[$i]['end'] = strtotime($row['sa_app_endtime']);
					$i++;
				}
			}
			/*======= GET ENABLE TIME SLOTS=========================== */
			$arr_slots_avaible=array();
			foreach($arr_avail_admin_slots as $arr_values){
				$arr_slots_avaible[]=unserialize(html_entity_decode(urldecode($arr_values)));	
			}
			$i=1;
			foreach($arr_slots_avaible as $slot_array){
				
				foreach($slot_array as $slot){
					
					foreach($bookings as $booking){ /*Skip already booked time slots*/
						if($booking['start']<=$slot['start'] && $booking['end']>=$slot['end']){
							continue(2);
						}
						elseif($booking['start']>$slot['start'] && $booking['start']<$slot['end']){
							continue(2);
						}
						elseif($booking['end']>$slot['start'] && $booking['end']<$slot['end']){
							continue(2);
						}
					}
					
					if(count($lunch_time)>0){ /*Skip Lunch time slots*/
						foreach($lunch_time as $lt){
							if($lt['start']<=$slot['start'] && $lt['end']>=$slot['end']){
								continue(2);
							}
							elseif($lt['start']>$slot['start'] && $lt['start']<$slot['end']){
								continue(2);
							}
							elseif($lt['end']>$slot['start'] && $lt['end']<$slot['end']){
								continue(2);
							}
						}
					}
					
					if(count($mainBlockSlots)>0){ /*Skip blocked time slots*/
						foreach($mainBlockSlots as $slt){
							if($slt['start']<=$slot['start'] && $slt['end']>=$slot['end']){
								continue(2);
							}
							elseif($slt['start']>$slot['start'] && $slt['start']<$slot['end']){
								continue(2);
							}
							elseif($slt['end']>$slot['start'] && $slt['end']<$slot['end']){
								continue(2);
							}
						}
					}
					
					//if($slotEnd>$end){$flag=false;}
					//else{
						
						$elemId = $calYearNumber."_".(int)$calMonthNumber."_".(int)$calDateNumber."_".date("h_i_a",$slot['start']);
						$slots[$selectedDate1]['slots'][$i] = $elemId;
						
						//$slots['templates'][$key]['slots'][$i]['start'] = date("h:i A",$slotStart);
						//$slots['templates'][$key]['slots'][$i]['end'] = date("h:i A",$slotEnd);
					//}
					$i++;
				}
			}
	
			/*print_r($bookings);
			print $startTime."\n\n".$endTime."\n\n\n";*/
			
			/*$start = strtotime($startTime);
			$end = strtotime($endTime);
	
			$flag = true;
	
			$slotEnd = $start;
			$i=1;
			do{
				$slotStart = $slotEnd;
				$slotEnd = strtotime("+".$defaultTimeSlot." seconds", $slotStart);
	
				foreach($bookings as $booking){ /*Skip already booked time slots* /
					if($booking['start']<=$slotStart && $booking['end']>=$slotEnd){
						continue(2);
					}
					elseif($booking['start']>$slotStart && $booking['start']<$slotEnd){
						continue(2);
					}
					elseif($booking['end']>$slotStart && $booking['end']<$slotEnd){
						continue(2);
					}
				}
				
				if(count($lunch_time)>0){ /*Skip Lunch time slots* /
					foreach($lunch_time as $lt){
						if($lt['start']<=$slotStart && $lt['end']>=$slotEnd){
							continue(2);
						}
						elseif($lt['start']>$slotStart && $lt['start']<$slotEnd){
							continue(2);
						}
						elseif($lt['end']>$slotStart && $lt['end']<$slotEnd){
							continue(2);
						}
					}
				}
				
				
	
				if($slotEnd>$end){$flag=false;}
				else{
					
					$elemId = $calYearNumber."_".(int)$calMonthNumber."_".(int)$calDateNumber."_".date("h_i_a",$slotStart);
					$slots[$selectedDate1]['slots'][$i] = $elemId;
					
					//$slots['templates'][$key]['slots'][$i]['start'] = date("h:i A",$slotStart);
					//$slots['templates'][$key]['slots'][$i]['end'] = date("h:i A",$slotEnd);
				}
				$i++;
			}while($flag);
			*/
			
		}
	}
}
	print json_encode($slots);
?>