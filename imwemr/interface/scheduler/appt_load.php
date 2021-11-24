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

$max_schedules_per_slide=constant('MAX_SCHEDULE_PER_SLIDE');
$max_schedules_per_slide=(!$max_schedules_per_slide)?5:$max_schedules_per_slide;

if($max_schedules_per_slide<$_REQUEST['max_slide_user'])
{$max_schedules_per_slide=$_REQUEST['max_slide_user'];}
	
//scheduler object
$obj_scheduler = new appt_scheduler();

//checking admin previleges
$admin_priv = (core_check_privilege(array("priv_admin")) == true) ? 1 : 0;
$sch_overrider_privilege=(core_check_privilege(array("priv_Sch_Override")) == false) ? 0 : 1;
$sch_lockblock_privilege=(core_check_privilege(array("priv_sch_lock_block")) == false) ? 0 : 1;

// ARRAY USED TO DISPLAY HOURS 	
$time_array=array("12 AM","01 AM","02 AM","03 AM","04 AM","05 AM","06 AM","07 AM","08 AM","09 AM","10 AM","11 AM","12 PM","01 PM","02 PM","03 PM","04 PM","05 PM","06 PM","07 PM","08 PM","09 PM","10 PM","11 PM" );

//getting and setting dates
$working_day_dt = (isset($_GET['dt']) && !empty($_GET['dt'])) ? $_GET['dt'] : date("Y-m-d");

$arr_selected_prov = explode(",", $_REQUEST["prov"]);
$obj_scheduler->cache_prov_working_hrs($working_day_dt, $arr_selected_prov);

//getting cached values if any in cache files
/*if($obj_scheduler->scan_template_cache($working_day_dt, "file") === false){
	$obj_scheduler->cache_working_hours($working_day_dt);
}*/

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
	
	if(trim($working_day_dt) == trim($target_dat) && in_array($arr_scroll['SCROLL_PROV_ID'],$arr_selected_prov))
	{
		$tim_cur = $arr_scroll["SCROLL_TIME"];	
	}
	else
	{
		$tim_cur = date('H:i:00');
	}	
}
else
{
	$tim_cur = date('H:i:00');
}
$arr_xml = $obj_scheduler->read_prov_working_hrs($working_day_dt, $arr_selected_prov);
if($arr_xml === false){
	echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:40%;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">Office is closed.</div>____CLOSED____0____';
	die();
}
$start_time = "00:00";
if(is_array($arr_xml) && count($arr_xml) > 0){
	foreach($arr_xml as $k => $v){
		if($k != "dt" && isset($v["slots"])){
			//$start_time = $v["slots"]['timing'];
			foreach($v["slots"] as $sl_id => $sl_detail){
				$arr_sl_id = explode("-", $sl_id);
				$start_time = $arr_sl_id[0];
				break;
			}
			break;
		}
	}
}
$start_time_arr = explode(':',$start_time);
$hr1 = $start_time_arr[0];
$mn1 = $start_time_arr[1];
$arr_selected_fac =$arr_selected_fac_tmp = array();
if(isset($_REQUEST["loca"]) && !empty($_REQUEST["loca"])){

	if(isset($_REQUEST["prov"]) && !empty($_REQUEST["prov"])){
		
		if(isset($_SESSION['sess_sch_sel_facs']) && !empty($_SESSION['sess_sch_sel_facs'])){
			if($_SESSION['sess_sch_sel_facs'] != $_REQUEST["loca"]){
				$_SESSION['sess_sch_sel_facs'] = $_REQUEST["loca"];
				$obj_scheduler->save_session_cache("FAC_SELECT", $_REQUEST["loca"], $_SESSION["authId"]);
			}
		}else{
			$_SESSION['sess_sch_sel_facs'] = $_REQUEST["loca"];
		}

		if(isset($_SESSION['sess_sch_sel_prov']) && !empty($_SESSION['sess_sch_sel_prov'])){
			if($_SESSION['sess_sch_sel_prov'] != $_REQUEST["prov"]){
				$_SESSION['sess_sch_sel_prov'] = $_REQUEST["prov"];
				$week_pro_arr=explode(',',$_REQUEST["prov"]);
				$week_pro=$week_pro_arr[0];
				$obj_scheduler->save_session_cache("PRO_SELECT", $_REQUEST["prov"], $_SESSION["authId"]);
				$obj_scheduler->save_session_cache("WEEKLY_PROV", $week_pro, $_SESSION["authId"]);
				unset($_SESSION['sess_sch_week_prov']);
			}
		}else{
			$_SESSION['sess_sch_sel_prov'] = $_REQUEST["prov"];
			unset($_SESSION['sess_sch_week_prov']);
		}
		$arr_selected_fac_tmp = explode(",", $_REQUEST["loca"]);
		foreach($arr_selected_fac_tmp as $fac)$arr_selected_fac[trim($fac)]=trim($fac);
		unset($arr_selected_prov);
		//$arr_selected_prov = explode(",", $_REQUEST["prov"]);
		//$arr_selected_prov = array_unique($arr_selected_prov);
		if($_REQUEST["prov"]){
			//set ordering 
			$order_key=1;
			$qOrder=imw_query("SELECT id FROM `users` where id IN ($_REQUEST[prov]) ORDER BY `sch_index` ASC, lname asc");
			while($dOrder=imw_fetch_assoc($qOrder))
			{
				$arr_selected_prov[]=$dOrder['id'];
				$display_order[$dOrder['id']]=$order_key++;
			}
		}
		$prov_sch_sel_load = $prov_sch_rem_load = array();
		/* Loop Code for the selection of first 3,4,5 physicians */
		if(count($arr_selected_prov)>0)
		{
			$arr_sch_prov = $obj_scheduler->get_provider_schedules($working_day_dt,$arr_selected_prov);
			//re order providers display order
			$arr_sch_prov_tmp=array();
			$int_tmp_order=100;
			foreach($arr_sch_prov as $key=>$sch_arr)
			{
				$pid=$sch_arr['provider'];
				$key_order=($display_order[$pid])?$display_order[$pid]:$int_tmp_order++;
				$arr_sch_prov_tmp[$key_order][]=$sch_arr;
			}
			ksort($arr_sch_prov_tmp);
			$arr_sch_prov=$arr_sch_prov_tmp;
			foreach($arr_sch_prov as $result_sch_prov_master)
			{
				foreach($result_sch_prov_master as $result_sch_prov){
					if(trim($result_sch_prov["sch_tmp_id"])!="" && $arr_selected_fac[trim($result_sch_prov['facility'])])
					{
						$pro_id=$result_sch_prov['provider'];
						if(count($prov_sch_sel_load)<$max_schedules_per_slide)
						{
							$prov_sch_sel_load[$pro_id] = $pro_id;
							//$prov_sch_sel_load = array_unique($prov_sch_sel_load);						
						}
						else
						{
							$prov_sch_rem_load[$pro_id] = $pro_id;
							//$prov_sch_rem_load = array_unique($prov_sch_rem_load);						
						}
					}				
				}
			}
		}
		$total_prov_req = count($prov_sch_sel_load)+count($prov_sch_rem_load);
		$prov_sch_rem_load_str = implode(',',$prov_sch_rem_load);
		$prov_sch_sel_load_str = implode(',',$prov_sch_sel_load);
		
		if(count($prov_sch_sel_load)>$max_schedules_per_slide-1)
		{
			$arr_selected_prov = $prov_sch_sel_load;	
		}
		list($str_header, $str_time_pane, $str_appt_slots, $str_proc_summary, $total_prov, $arr_widths, $arr_not_processed, $arr_processed) = $obj_scheduler->write_html_content($arr_xml, $arr_selected_fac, $arr_selected_prov, $admin_priv, $time_array, "main", $sch_overrider_privilege);
		$total_prov_available=$total_prov;
		list($column_width, $innerContainer, $scroll_width, $div_width, $total_prov) = $arr_widths;
		if($total_prov_req > $max_schedules_per_slide)
		{
			//$scroll_width = ($total_prov_req*210);
			$total_prov = $total_prov_req;	
		}		
		if((count($arr_processed) == 0 && count($arr_not_processed) > 0) || ($total_prov == 0)){
			echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:40%;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">Office is closed.</div>____CLOSED____0____';
			die();
		}
	}else{
		echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:40%;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">No Provider is selected.</div>____NOPROVIDER____0____';
		die();
	}

}else{
	echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:40%;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">No Facility is selected.</div>____NOFACILITY____0____';
	die();
}
?>
<form name="getdate" action=""><input type="hidden" name="target_dat2" id="target_dat2" value="<?php echo $target_dat;?>"><input type="hidden" name="tim_cur2" id="tim_cur2" value="<?php echo $tim_cur;?>"><input type="hidden" name="scroll_tim_limit3" id="scroll_tim_limit3" value="<?php echo $hr1;?>"><input type="hidden" name="scroll_tim_limit4" id="scroll_tim_limit4" value="<?php echo $mn1;?>"></form>
<form name="open_block" method="post" action=""><input type="hidden" id="re_schedule_provider" name="re_schedule_provider" value=""><input type="hidden"id="b_fac" name="b_fac" value=""><input type="hidden" id="b_pro" name="b_pro" value=""><input type="hidden" id="b_tm" name="b_tm" value=""><input type="hidden" id="b_dt" name="b_dt" value=""><input type="hidden" id="b_mode" name="b_mode" value=""><input type="hidden" id="right_click_patient_id" name="right_click_patient_id" value=""></form>
<form name="frm_day_appt_print" method="get" action="../reports/day_appointment_report_print.php" target="_blank"><input type="hidden" id="from_date" name="from_date" value=""><input type="hidden" id="comboFac" name="comboFac" value=""><input type="hidden" id="comboProvider" name="comboProvider[]" value="">
<input type="hidden" id="submitted" name="submitted" value="yes"><input type="hidden" id="submitted_from_scheduler" name="submitted_from_scheduler" value="yes"><input type="hidden" id="selMidDay" name="selMidDay" value="full"><input type="hidden" id="include_pat_Add" name="include_pat_Add" value="0"><div id="exc_div"></div></form>
<input type="hidden" id="prov_sch_rem_load" name="prov_sch_rem_load" value="<?php echo $prov_sch_rem_load_str; ?>" /><input type="hidden" id="prov_sch_sel_load" name="prov_sch_sel_load" value="<?php echo $prov_sch_sel_load_str; ?>" /><input type="hidden" id="current_slide" name="current_slide" value="1" /><div id="wn20" style="width:100%"><div id="expand_collapse_icon" onclick="expand_shorten_sch();"><img src="../../library/images/expand.png" title="Expand View"></div>
<div id="re_schedule_menu" style="float:left;"><div id="re_schedule_menu_1" onmouseout="hide_div('none');" onmouseover="hide_div('block');"><A HREF='javascript:rechedule_provider();' ONMOUSEOVER='Highlight(this);' ONMOUSEOUT='Highlight(this);'>Re-Schedule All</A><BR><A HREF='javascript:add_notes();' ONMOUSEOVER='Highlight(this);' ONMOUSEOUT='Highlight(this);'>Add Notes</A><BR></div></div>
<div id="hold2"><div id="wn2"><div id="lyr2"><div id="lr5"><?php 
echo '<div id="slide_1_header">';
echo $str_header;
echo '</div>';
//generate empty slides as per providers selected
$total_slides=ceil($total_prov/3);
for($sl=2;$sl<=$total_slides;$sl++)
{echo'<div id="slide_'.$sl.'_header" style="display:none"></div>';}
?>
</div></div></div></div>
<div style="clear:both;"></div>
<div id="mn_1" style="float:left;width:100%;height:<?php echo $_SESSION["wn_height"] - 354;?>px;">
<div id="mn1_1" style="width:100%; height:<?php echo $_SESSION["wn_height"] - 357;?>px; overflow:scroll; overflow-x:hidden;float:left;">
<div id="mnlyr1_1" style="float:left;width:100%;">
<div id="hold_1"><div id="wn_1"><div id="lyr1_1"><div id="lr4"><?php echo $str_time_pane;?></div></div></div></div>
<div id="hold"><div id="wn"><div id="lyr1">
<div class="optmnu" id="ContextMenu"><div class="scmnuop" id="ContextMenu_1"><ul><?php
$status_arr=$obj_scheduler->schedule_status();
foreach($status_arr as $key=>$status)
{
	if($key==202){echo'<li><a href="javascript:void(0);" onclick="javascript:drag_name(\'get\', \'get\', \'reschedule\', this.event);">'.$status.'</a></li>';}
	elseif($key==18){echo'<li><a href="javascript:change_status(\''.$key.'\');">Cancel</a></li>';}
	elseif($key==203){/*presently we are hiding delete option for scheduler as it used in to do reschedule or waiting list*/ }
	else{echo'<li><a href="javascript:change_status(\''.$key.'\');">'.$status.'</a></li>';}
}
?>
</ul><ul><li><a HREF="javascript:change_status(101);">Scheduled for Surgery</a></li><li><a HREF="javascript:change_status(100);">Waiting for Surgery</a></li>
</ul><!--iolink sync starts--><?php
if($enableIolink == true){
	//$userType = getUserType($_SESSION["authId"]);
	//if($userType==6){
	if(core_check_privilege(array("priv_iOLink")) == true){																			
		$qryGetIOlinkSettings = "select iolink_admin_id as iolinkAdminId, 
								iolink_id as iolinkId,
								iolink_url as iolinkUrl,
								iolink_url_username as iolinkUrlUsername,
								iolink_url_password as iolinkUrlPassword,
								iolink_practice_name as iolinkUrlPracName
								FROM iolink_connection_settings
								WHERE del_status != 'yes' ORDER BY iolink_id ";
		$rsGetIOlinkSettings = imw_query($qryGetIOlinkSettings);	
		if(imw_num_rows($rsGetIOlinkSettings)>0) {										
		?><ul id="iolink_connection_span_id"><?php	
			while($rowGetIOlinkSettings = imw_fetch_assoc($rsGetIOlinkSettings)) {
				$iolinkUrlPracName = $rowGetIOlinkSettings["iolinkUrlPracName"];
				$iolinkId = $rowGetIOlinkSettings["iolinkId"];
				$iolinkUrlPracNameShow="";
				if($iolinkUrlPracName) { $iolinkUrlPracNameShow = " - ".$iolinkUrlPracName;}
		?><div class="flulink"><li><a href="javascript:iolink_sync_ocular('send','<?php echo $iolinkId;?>');">Send to iASC Link <?php echo $iolinkUrlPracNameShow;?></a></li></div>
		<?php }	?></ul>
<ul id="iolink_re_connection_span_id" style="display:none;">
	<li><a href="javascript:iolink_sync_ocular('remove',document.getElementById('global_iolink_connection_settings_id').value);"><span id="iolink_remove_connection_id">Remove from iASC Link</span></a></li>
	<li><a href="javascript:iolink_sync_ocular('resync',document.getElementById('global_iolink_connection_settings_id').value);"> <span id="iolink_resyncro_connection_id">Resynchronise with iASC Link</span></a></li>
</ul><?php
		}
	}
}
?></div><div class="clearfix"></div><div class="schbot"><ul>
<?php
if(($admin_priv == 1 || $sch_overrider_privilege == 1)){
?><li onClick="load_label_options();" class="pointer"><img src="../../library/images/scficon1.png" alt=""/> Add/Edit Label</li>
<?php }if($sch_lockblock_privilege == 1){?>
<li onClick="blk_lk_options('get', 'block');" class="pointer"><img src="../../library/images/scficon2.png" alt=""/> Block Time</li>
<?php }?>
<li onClick="return print_pt_key();" class="pointer"><img src="../../library/images/scficon3.png" alt=""/> Pt. Portal Key</li></ul></div>
</div>



<?php if($admin_priv == 1 || $sch_overrider_privilege == 1 || $sch_lockblock_privilege == 1){ ?>
<div class="optmnu" id="ContextMenu_blk">
<?php if($admin_priv == 1 || $sch_overrider_privilege == 1){ ?>
<div class="scmnuop" id="ContextMenu_1_blk"><ul>
<div class="flulink"><li><a id="ContextMenu_1_blk_open" href="javascript:open_add_schedule_option('appt_scheduler');" style="display:block;"> Open Time </a></li></div></ul></div>
<div class="clearfix"></div>
<?php }?>

<div class="schbot"><ul>
<li id="ContextMenu_1_blk_label" onClick="load_label_options();" class="pointer"><img src="../../library/images/scficon1.png" alt=""/> Add/Edit Label</li>
<?php if($sch_lockblock_privilege == 1){?>
<li id="ContextMenu_1_blk_block" onClick="blk_lk_options('get', 'block');" class="pointer"><img src="../../library/images/scficon2.png" alt=""/> Block Time</li>
<?php }else{echo'<li id="ContextMenu_1_blk_block">&nbsp;</li>';}
if(($admin_priv == 1 || $sch_overrider_privilege == 1)){ ?>
<li id="ContextMenu_1_blk_remove_label" onClick="return remove_labels_by_slot();" class="pointer"><img src="../../library/images/delete.png" alt=""/> Remove Label</li>
<?php }?>
</ul></div>
</div>


<?php  } if($sch_lockblock_privilege == 1){ 	
?><div class="optmnu" id="ContextMenu_blk_only"><div class="scmnuop" id="ContextMenu_1_blk_only"><ul><div class="flulink"><li><a id="ContextMenu_1_blk_open" href="javascript:blk_lk_options('get', 'unblock');" style="display:block;"> Unblock Time </a></li></div></ul></div><div class="clearfix"></div></div><?php
}
?>

<div id="lr1"><div id="appt_slots_cont" style="margin-left:50px;width:100%;"><div id="slide_1"><?php
 echo $str_appt_slots;?></div><?php
//generate empty slides as per providers selected
$total_slides=ceil($total_prov/$max_schedules_per_slide);
for($sl=2;$sl<=$total_slides;$sl++)
{
echo'<div id="slide_'.$sl.'" style="display:none">';
echo'<strong>Loading ...</strong>';
echo'</div>';
}
?></div></div></div></div></div></div></div></div></div>____LOADED____<?php echo $total_prov."____".$str_proc_summary."____".$total_prov_available;?>