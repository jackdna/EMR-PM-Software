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

// ARRAY USED TO DISPLAY HOURS 	
$time_array=array("12 AM","01 AM","02 AM","03 AM","04 AM","05 AM","06 AM","07 AM","08 AM","09 AM","10 AM","11 AM","12 PM","01 PM","02 PM","03 PM","04 PM","05 PM","06 PM","07 PM","08 PM","09 PM","10 PM","11 PM" );

//getting and setting dates
$working_day_dt = (isset($_GET['dt']) && !empty($_GET['dt'])) ? $_GET['dt'] : date("Y-m-d");

//echo "ab".$_REQUEST["sel_pro_month"]."ab";
$arr_selected_prov = explode(",", $_REQUEST["sel_pro_month"]);

$obj_scheduler->cache_prov_working_hrs_weekly($working_day_dt, $arr_selected_prov);
$arr_xml = $obj_scheduler->read_prov_working_hrs_weekly($working_day_dt, $arr_selected_prov);

//print "<pre><textarea>"; print_r($arr_xml); print "</textarea>";
//die;
if($arr_xml === false){
	echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:775px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">Office is closed.</div>';
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
$hr1 = substr($start_time, 0, 2);

$arr_selected_fac = array();
if(isset($_REQUEST["facilities"]) && !empty($_REQUEST["facilities"])){
	if(isset($_REQUEST["sel_pro_month"]) && !empty($_REQUEST["sel_pro_month"])){
		
		
		/*if(isset($_SESSION['sess_sch_sel_facs']) && !empty($_SESSION['sess_sch_sel_facs'])){
			if($_SESSION['sess_sch_sel_facs'] != $_REQUEST["facilities"]){
				$_SESSION['sess_sch_sel_facs'] = $_REQUEST["facilities"];
				$obj_scheduler->save_session_cache("FAC_SELECT", $_REQUEST["facilities"], $_SESSION["authId"]);
			}
		}else{
			$_SESSION['sess_sch_sel_facs'] = $_REQUEST["facilities"];
		}*/

		if(isset($_SESSION['sess_sch_week_prov']) && !empty($_SESSION['sess_sch_week_prov'])){
			if($_SESSION['sess_sch_week_prov'] != $_REQUEST["sel_pro_month"]){
				$_SESSION['sess_sch_week_prov'] = $_REQUEST["sel_pro_month"];
				$obj_scheduler->save_session_cache("WEEKLY_PROV", $_REQUEST["sel_pro_month"], $_SESSION["authId"]);
				$obj_scheduler->save_session_cache("PRO_SELECT", $_REQUEST["sel_pro_month"], $_SESSION["authId"]);
				unset($_SESSION['sess_sch_sel_prov']);
			}
		}else{
			$_SESSION['sess_sch_week_prov'] = $_REQUEST["sel_pro_month"];
			$obj_scheduler->save_session_cache("PRO_SELECT", $_REQUEST["sel_pro_month"], $_SESSION["authId"]);
			$_SESSION['sess_sch_sel_prov']= $_REQUEST["sel_pro_month"];		
		}

		$obj_scheduler->save_session_cache("FAC_SELECT", $_REQUEST["facilities"], $_SESSION["authId"]);
		unset($_SESSION['sess_sch_sel_facs']);
		$arr_selected_fac = explode(",", $_REQUEST["facilities"]);
		$arr_selected_prov = explode(",", $_REQUEST["sel_pro_month"]);

		list($str_header, $str_time_pane, $str_appt_slots, $str_proc_summary, $total_prov, $arr_widths, $arr_not_processed, $arr_processed) = $obj_scheduler->write_html_content_weekly($arr_xml, $arr_selected_fac, $arr_selected_prov, $admin_priv, $time_array);
		list($column_width, $scroll_width, $div_width, $total_prov) = $arr_widths;
		if(count($arr_processed) == 0 && count($arr_not_processed) > 0){
			echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:775px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">Office is closed.</div>';
			die();
		}
	}else{
		echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:500px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">No Provider is selected.</div>';
		die();
	}

}else{
	echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:500px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">No Facility is selected.</div>';
	die();
}
?>
<form name="frm_day_appt_print" method="get" action="../reports/day_appointment_report_print.php" target="_blank">
	<input type="hidden" id="from_date" name="from_date" value="">
	<input type="hidden" id="comboFac" name="comboFac" value="">
	<input type="hidden" id="comboProvider" name="comboProvider[]" value="">
	<input type="hidden" id="submitted" name="submitted" value="yes">
	<input type="hidden" id="submitted_from_scheduler" name="submitted_from_scheduler" value="yes">
	<input type="hidden" id="selMidDay" name="selMidDay" value="full">
	<input type="hidden" id="include_pat_Add" name="include_pat_Add" value="0">
</form>
<div id="wn20" style="width:1755px">	
	<!-- <div style="height:24px;border-left:#999999 solid 1px;float:left;background-color:#ece9d8"><img src="../../images/space.gif" width="48px" height="0px" /></div>
	 -->
	 <div id="hold2" style="height:24px;padding-left:60px;background:#7a7a7a">
		<div id="wn2" style="height:24px;">
			<div id="lyr2">
				<div id="lr5"><!-- style="width:2000px; left:0"-->
					<?php echo $str_header; ?>
				</div>
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>
	<div id="mn_1" style="width:100%">
		<div id="mn1_1" style="height:<?php echo $_SESSION["wn_height"] - 310;?>px; overflow:auto; background:#FFF; width:100%">
			<div id="mnlyr1_1" style="float:left;width:100%;">
				<div id="hold_1" style="width:56px; float:left">
					<div id="wn_1">
						<div id="lyr1_1">
							<div id="lr4" style="border-right:1px solid #999999;border-left:1px solid #999999;">						
								<?php echo $str_time_pane;?>
							</div>								
						</div>
					</div>
				</div>
				<div id="hold" >  
					<div id="wn" >
						<div id="lyr1">
							<div id="ContextMenu" class="ContextMenuStyle">
								<div id="ContextMenu_1" class="ContextMenuSubStyle">	
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('18');">Cancel</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('13');">Check-in</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('11');">Check-out</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('2');">Chart Pulled</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('17');">Confirm</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('6');">Left Without Visit</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('3');">No-Show</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('201');">Send to Re-Schedule List</A><BR>	
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('0');">Restore</A><BR>				
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('200');">Room # assignment</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly('7');">Insurance/Financial Issue</A><BR>									
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly(100);">Waiting for Surgery</A><BR>
									<A STYLE='color:#666666;' HREF="javascript:change_status_weekly(101);">Scheduled for Surgery</A><BR>
								</div>
							</div>
							<div id="lr1">
								<div style="margin-left:50px;border-right: 1px solid #999999;border-left: 1px solid #999999;">
									<?php echo $str_appt_slots;?>									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>~~~~~Processed