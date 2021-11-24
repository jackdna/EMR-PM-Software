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
require_once(dirname(__FILE__).'/../../library/classes/admin/scheduler_admin_func.php');

$anps_sel_tmp = $_REQUEST["anps_sel_tmp"];
$cal_sel_date = $_REQUEST["cal_sel_date"];
$start_hour = $_REQUEST["start_hour"];
$start_min = $_REQUEST["start_min"];
$start_time = $_REQUEST["start_time"];
$end_hour = $_REQUEST["end_hour"];
$end_min = $_REQUEST["end_min"];
$end_time = $_REQUEST["end_time"];
$anps_sel_pro = $_REQUEST["anps_sel_pro"];
$anps_sel_fac = $_REQUEST["anps_sel_fac"];
$comments = $_REQUEST["comm"];

$arr_cal_sel_date = explode("-", $cal_sel_date);
$dt = ((int)$arr_cal_sel_date[2] < 10) ? "0".(int)$arr_cal_sel_date[2] : (int)$arr_cal_sel_date[2];
$mn = ((int)$arr_cal_sel_date[1] < 10) ? "0".(int)$arr_cal_sel_date[1] : (int)$arr_cal_sel_date[1];
$yr = $arr_cal_sel_date[0];
$date_to_save = $yr."-".$mn."-".$dt;
$ts = mktime(0, 0, 0, $mn, $dt, $yr);
$day_name = date("l", $ts);

//deleint cahce
//$dir = realpath('../scheduler_common/load_xml');	
$dir =$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml";		
$op = opendir($dir);
while($file = readdir($op)){
	$extn = substr($file,-3);
	if(strtolower($extn) == 'sch'){
		$fileDate = explode('-',$file);		
		if(@strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]) == strtotime($date_to_save)){
			//echo $dir.'/'.$file;
			unlink($dir.'/'.$file);
		}		
	}
}
//setting time format
if((int)$start_hour == 12){
	$start_hour = ($start_time == "PM") ? 12 : $start_hour;
}else{
	$start_hour = ($start_time == "PM") ? (int)$start_hour + 12 : $start_hour;
}
if((int)$end_hour == 12){
	$end_hour = ($end_time == "PM") ? 12 : $end_hour;
}else{
	$end_hour = ($end_time == "PM") ? (int)$end_hour + 12 : $end_hour;
}

$date_status = $start_time.",".$end_time;

$morning_start_time = $start_hour.':'.$start_min.':00';
$morning_end_time = $end_hour.':'.$end_min.':00';

//calculating number of minutes of working time
$minute = 0;
$working_minutes = '';
if($start_hour != $end_hour)
{
	for($o = $start_hour; $o < $end_hour; $o++){
		$minute++;
	}
	$working_minutes = ($minute * 60) ;
}
$working_minutes += $end_min;

$template_type = (isset($_REQUEST["template_type"]) && !empty($_REQUEST["template_type"])) ? $_REQUEST["template_type"] : "USER";

//inserting new template
if($anps_sel_tmp == "new"){
	$qry = "insert into schedule_templates set morning_start_time = '".$morning_start_time."', morning_end_time = '".$morning_end_time."',date_status  = '".$date_status."', check_true = 'true', schedule_name = 'Emergency', template_type = '".$template_type."' ";
	imw_query($qry);
	$anps_sel_tmp = imw_insert_id();
	$scheduleName = 'Emergency'.$anps_sel_tmp;
	$qry = "update schedule_templates set schedule_name = '".$scheduleName."' where id = '".$anps_sel_tmp."'";
	imw_query($qry);
	
	// INSERT SCHEDULE SLOTS IN schedule_label_tbl TABLE
	$start_slot_hr = $start_hour;
	for($w = $start_min; $w < $working_minutes; $w = $w + DEFAULT_TIME_SLOT){									
	
		if($w == $start_min) { $start_slot_mn = $start_min; }
		//adjusting start time
		if($start_slot_mn >= 60){
			$start_slot_hr++;
			$start_slot_mn = $start_slot_mn - 60;
		}
		
		//calculating end time
		$end_slot_hr = $start_slot_hr;
		$end_slot_mn = $start_slot_mn + DEFAULT_TIME_SLOT;
	
		//SET FORMAT
		$start_slot_hr = $start_slot_hr < 10 ? '0'.(int)$start_slot_hr : $start_slot_hr;
		$start_slot_mn = $start_slot_mn < 10 ? '0'.(int)$start_slot_mn : $start_slot_mn;
		$end_slot_hr = $end_slot_hr < 10 ? '0'.(int)$end_slot_hr : $end_slot_hr;
		$end_slot_mn = $end_slot_mn < 10 ? '0'.(int)$end_slot_mn : $end_slot_mn;
		
		$start_time = $start_slot_hr.":".$start_slot_mn;
		$end_time = $end_slot_hr.":".$end_slot_mn;

		//INSERT QUERY
		$qry = "Insert into schedule_label_tbl set sch_template_id=".$anps_sel_tmp.", start_time = '".$start_time."', end_time = '".$end_time."', 
		template_label = '".$comments."', date_time = '".date('Y-m-d H:i:s')."', check_true = 'false', lunch = 'No', reserved = 'No', procedures='',procedures_id=0";
		imw_query($qry);

		//adjusting end time
		if($end_slot_mn >= 60){
			$end_slot_hr++;
			$end_slot_mn = $end_slot_mn - 60;
			$start_slot_hr = $end_slot_hr;
		}
		$start_slot_mn = $end_slot_mn;
	}
}

$weekDays = date('N', $ts);
$week = ceil($dt / 7);
if($anps_sel_pro && $anps_sel_fac)
{
	$qry = "select id from provider_schedule_tmp where provider = '".$anps_sel_pro."' and  facility = '".$anps_sel_fac."'and sch_tmp_id = '".$anps_sel_tmp."' and week".$week."='".$weekDays."' and today_date = '".$date_to_save."' and del_status = 1";
	$res=imw_query($qry);

	$qry_exec = " provider='".$anps_sel_pro."', week".$week."='".$weekDays."', facility='".$anps_sel_fac."', sch_tmp_id='".$anps_sel_tmp."', today_date='".$date_to_save."', status='no', del_status = 0, delete_row = 'no' ";
	if(imw_num_rows($res) > 0){
		$arr = imw_fetch_assoc($res);
		$qry = "update provider_schedule_tmp set ".$qry_exec." where id = '".$arr["id"]."'";
	}else{
		$qry = "insert into provider_schedule_tmp set ".$qry_exec;
	}
	imw_query($qry);
	tmp_log('Added', 'Schedule added from frontdesk', $anps_sel_pro, $anps_sel_fac, $anps_sel_tmp, $date_to_save, "week$week /".$weekDays, 'no');
}
echo $date_to_save."~".$day_name;
?>