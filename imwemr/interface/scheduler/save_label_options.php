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
$start_loop_time =  $time_from_hour.":".$time_from_mins.":00";
$end_loop_time =  $time_to_hour.":".$time_to_mins.":00";
$load_dt = $_REQUEST["load_dt"];
$arr_fac = explode(",", $_REQUEST["loca"]);
$arr_pro= explode(",", $_REQUEST["prov"]);
$label_type = $_REQUEST["label_type"];
$ap_tmp_id= $_REQUEST["ap_tmp_id"];

$label_text = $_REQUEST["proc_acro"];
$arr_template_label = explode(";", $label_text);
$arr_new_template_label = array();
if(count($arr_template_label) > 0){
	foreach($arr_template_label as $this_template_label){
		$arr_new_template_label[] = trim($this_template_label);
	}
	$label_text = implode("; ", $arr_new_template_label);
}


$label_color = $_REQUEST["label_color"];
$mode = $_REQUEST["mode"];
$del_arr = array();

for($f = 0; $f < count($arr_fac); $f++){
	//echo $arr_fac[$f]."<br>";
	for($p = 0; $p < count($arr_pro); $p++){

		//time loop starts
		$sttm = strtotime($start_loop_time);
		$edtm = strtotime($end_loop_time);
		//get labels type from schedule lbl table
		$q=imw_query("select label_type, template_label, start_time from schedule_label_tbl where sch_template_id=$ap_tmp_id");
		if(imw_num_rows($q)>0)
		{
			while($d=imw_fetch_object($q))
			{
				$template_lbl_arr[$d->start_time.":00"]['type']=$d->label_type;
				$template_lbl_arr[$d->start_time.":00"]['label']=$d->template_label;
			}
		}
		$st_time = date("H:i:00", $sttm);
		$ed_time = date("H:i:00", $edtm);
		$qry0 = "select id, l_show_text, l_type, start_time from scheduler_custom_labels 
		where provider = '".$arr_pro[$p]."' 
		and facility = '".$arr_fac[$f]."'
		and start_time >= '".$st_time."' 
		and end_time <= '".$ed_time."'
		and start_date='".$load_dt."'";
		$res0 = imw_query($qry0);
		if(imw_num_rows($res0) > 0){
			while($arr0 = imw_fetch_assoc($res0))
			{
				$del_arr[] = $arr0["id"];
				$custom_lbl_arr[$arr0["start_time"]]['type'] = $arr0["l_type"];
				$custom_lbl_arr[$arr0["start_time"]]['label'] = $arr0["l_show_text"];
			}
		}
		for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
			$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);
			
			$start_loop_time = date("H:i:00", $looptm);
			$end_loop_time = date("H:i:00", $edtm2);

			$l_type=($custom_lbl_arr[$start_loop_time]['type'])?$custom_lbl_arr[$start_loop_time]['type']:$template_lbl_arr[$start_loop_time]['type'];
			$l_before=($custom_lbl_arr[$start_loop_time])?$custom_lbl_arr[$start_loop_time]['label']:$template_lbl_arr[$start_loop_time]['label'];
			
			if($mode == "save"){
				$l_after=addslashes($label_text);
				$qry = "INSERT INTO scheduler_custom_labels set provider = '".$arr_pro[$p]."', 
				facility = '".$arr_fac[$f]."', 
				start_date = '".$load_dt."', 
				start_time = '".$start_loop_time."', 
				end_time = '".$end_loop_time."', 
				l_text = '".addslashes($label_text)."', 
				l_show_text = '".addslashes($label_text)."', 
				l_type = '".addslashes($_REQUEST["label_type"])."', 
				l_color = '".addslashes($_REQUEST["label_color"])."', 
				time_status = '".date("Y-m-d H:i:s")."',
				temp_id = '".$ap_tmp_id."'";
				imw_query($qry);
				$_REQUEST['last_custom_inserted_id']=imw_insert_id();
				$obj_scheduler->custom_lbl_log($arr_pro[$p], $arr_fac[$f], $load_dt, $start_loop_time, $end_loop_time, $_REQUEST["label_type"], $label_text, $l_before, $l_after, $ap_tmp_id, 'Label Added', imw_insert_id());
			}
			if($mode == "remove"){
				$qry = "INSERT INTO scheduler_custom_labels set provider = '".$arr_pro[$p]."', 
				facility = '".$arr_fac[$f]."', 
				start_date = '".$load_dt."', 
				start_time = '".$start_loop_time."', 
				end_time = '".$end_loop_time."', 
				l_text = '', 
				l_show_text = '', 
				l_type = '$l_type', 
				l_color = '', 
				time_status = '".date("Y-m-d H:i:s")."', 
				temp_id = '".$ap_tmp_id."'";
				imw_query($qry);
				$obj_scheduler->custom_lbl_log($arr_pro[$p], $arr_fac[$f], $load_dt, $start_loop_time, $end_loop_time, $l_type, '', $l_before, '', $ap_tmp_id, 'Label Removed', imw_insert_id());
			}
			if($mode == "default"){
				//delete only
				$l_after=$label=$template_lbl_arr[$start_loop_time]['label'];
				$l_type=$template_lbl_arr[$start_loop_time]['type'];
				$obj_scheduler->custom_lbl_log($arr_pro[$p], $arr_fac[$f], $load_dt, $start_loop_time, $end_loop_time, $l_type, $label, $l_before, $l_after, $ap_tmp_id, 'Master Labels Restored',0);
			}
		}
		//time loop ends
	}
}
if(count($del_arr) > 0){
	$delqry = "DELETE FROM scheduler_custom_labels WHERE id IN ('".implode("','", $del_arr)."')";
	imw_query($delqry);
}

//setting date format
list($yr, $mn, $dt) = explode("-", $_REQUEST["load_dt"]);
echo $_REQUEST["load_dt"];
echo "~~~~~";
echo date("l", mktime(0, 0, 0, $mn, $dt, $yr));
?>
