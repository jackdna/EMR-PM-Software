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

	$ap_sttm = $_REQUEST['ap_sttm'];
	list($hh,$mm,$ss)=explode(':',$ap_sttm);
	$ap_endtm=date('H:i:s', mktime($hh,($mm+DEFAULT_TIME_SLOT),$ss));
	$ap_doc = $_REQUEST['ap_doc'];
	$ap_fac = $_REQUEST['ap_fac'];
	$ap_lbty = $_REQUEST['ap_lbty'];
	$ap_lbtx = trim($_REQUEST['ap_lbtx']);
	$ap_lbcl = $_REQUEST['ap_lbcl'];
	$load_dt = $_REQUEST['load_dt'];
	$ap_tmp_id = $_REQUEST['ap_tmp_id'];
	$replace_lbl = trim($_REQUEST['replace_lbl']);
	
	$qry = "SELECT id, l_text, l_show_text FROM scheduler_custom_labels WHERE start_date = '".$load_dt."' and provider = '".$ap_doc."' and facility = '".$ap_fac."' and start_time = '".$ap_sttm."' order by id DESC LIMIT 1";
	$qry_obj = imw_query($qry);
	$sch_lbl_data = imw_fetch_assoc($qry_obj);	
	if(imw_num_rows($qry_obj) == 0)
	{
		$insert_qry = "INSERT INTO scheduler_custom_labels set provider = '".$ap_doc."', 
		facility = '".$ap_fac."', 
		start_date = '".$load_dt."', 
		start_time = '".$ap_sttm."', 
		end_time = ADDTIME('".$ap_sttm."','00:".DEFAULT_TIME_SLOT.":00'), 
		l_text = '".$ap_lbtx."', 
		l_show_text = '".$ap_lbtx."', 
		l_type = '".$ap_lbty."', 
		l_color = '".$ap_lbcl."', 
		time_status = '".date('Y-m-d H:i:s')."', 
		temp_id = '".$ap_tmp_id."', 
		system_action = '0' ";
		
		imw_query($insert_qry);

		$sch_lbl_data = array();
		$sch_lbl_data['id'] = imw_insert_id();
		$sch_lbl_data['l_text'] = $ap_lbtx;
		$sch_lbl_data['l_show_text'] = $ap_lbtx;
	}
	//remove from all label field
	$l_text = $sch_lbl_data['l_text'];
	$l_text_arr = explode(';',$l_text);
	$l_text_replace_arr = array();
	foreach($l_text_arr as $l_text_replace)
	{
		$l_text_replace = trim($l_text_replace);
		if($l_text_replace != $replace_lbl)
		{
			$l_text_replace_arr[] = $l_text_replace;
		}
	}
	$l_text_replace_arr_txt = '';
	if(count($l_text_replace_arr) > 0)
	{
		$l_text_replace_arr_txt = implode('; ',$l_text_replace_arr);	
	}
	//remove from show label field
	$l_show_text = $l_before = $sch_lbl_data['l_show_text'];
	$l_show_text_arr = explode(';',$l_show_text);
	$l_show_text_replace_arr = array();
	foreach($l_show_text_arr as $l_show_text_replace)
	{
		$l_show_text_replace = trim($l_show_text_replace);
		if($l_show_text_replace != $replace_lbl)
		{
			$l_show_text_replace_arr[] = $l_show_text_replace;
		}
	}
	$l_show_text_replace_arr_txt = '';
	if(count($l_show_text_replace_arr) > 0)
	{
		$l_show_text_replace_arr_txt = $l_after= implode('; ',$l_show_text_replace_arr);	
	}

	$update_qry = "UPDATE scheduler_custom_labels SET l_text = '".$l_text_replace_arr_txt."', l_show_text = '".$l_show_text_replace_arr_txt."' WHERE id = '".$sch_lbl_data['id']."'";
	imw_query($update_qry);
	if(imw_affected_rows() > 0)
	{
		$obj_scheduler->custom_lbl_log($ap_doc, $ap_fac, $load_dt, $ap_sttm, $ap_endtm, $ap_lbty, $replace_lbl, $l_before, $l_after, $ap_tmp_id, 'Label Removed', $sch_lbl_data['id']);
		list($yr, $mn, $dt) = explode("-", $load_dt);
		echo date("l", mktime(0, 0, 0, $mn, $dt, $yr));			
	}
	else
	{
		echo 'notdone';	
	}
?>