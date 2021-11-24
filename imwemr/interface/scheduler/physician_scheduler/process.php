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

/* include_once("../../globals.php");
require_once("../../Billing/billing_globals.php");
include_once("../appt_schedule_functions.php"); */

require_once('../../../config/globals.php');
include_once($GLOBALS['srcdir'].'/classes/common_function.php');
include_once($GLOBALS['srcdir'].'/classes/scheduler/appt_schedule_functions.php');

$arrPat =array();

//scheduler object
$obj_scheduler = new appt_scheduler();

//checking admin previleges
$admin_priv = (core_check_privilege(array("priv_admin")) == true) ? 1 : 0;

// ARRAY USED TO DISPLAY HOURS 	
$time_array = array("12 AM","01 AM","02 AM","03 AM","04 AM","05 AM","06 AM","07 AM","08 AM","09 AM","10 AM","11 AM","12 PM","01 PM","02 PM","03 PM","04 PM","05 PM","06 PM","07 PM","08 PM","09 PM","10 PM","11 PM" );

//getting and setting dates
if(inter_date_format() == "mm-dd-yyyy"){
	$working_day_dt_temp = (isset($_GET['dt']) && !empty($_GET['dt'])) ? $_GET['dt'] : get_date_format(date("Y-m-d"));
	list($tm, $td, $ty) = explode("-", $working_day_dt_temp);
	$working_day_dt = $ty."-".$tm."-".$td;
}else if(inter_date_format() == "dd-mm-yyyy"){
	$working_day_dt_temp = (isset($_GET['dt']) && !empty($_GET['dt'])) ? $_GET['dt'] : get_date_format(date("Y-m-d"));
	list($td, $tm, $ty) = explode("-", $working_day_dt_temp);
	$working_day_dt = $ty."-".$tm."-".$td;
}

$res_fellow_sess = trim($_SESSION['res_fellow_sess']);
if($res_fellow_sess != "" && isset($res_fellow_sess))
{
	$int_prov = $res_fellow_sess;	
}
else
{
	$int_prov = (isset($_SESSION["authId"]) && $_SESSION["authId"] != "") ? $_SESSION["authId"] : 0;	
}

$int_fac = 0;
if(isset($_GET['loca']) && $_GET['loca'] != ""){
	$int_fac = $_GET['loca'];
}else{
	//getting default facility of the provider
	$sel_fac_prov = "select default_facility from users where id = '".$int_prov."'";
	$res_fac_prov = imw_query($sel_fac_prov);
	if(imw_num_rows($res_fac_prov) > 0){
		$arr_fac_prov = imw_fetch_array($res_fac_prov);	
		$int_fac = $arr_fac_prov["default_facility"];
	}else{
		//getting hq facility
		$sel_fac_hq = "select id from facility where facility_type = 1";
		$res_fac_hq = imw_query($sel_fac_hq);
		if(imw_num_rows($res_fac_hq) > 0){
			$arr_fac_hq = imw_fetch_array($res_fac_hq);	
			pre($arr_fac_hq);
			$int_fac = $arr_fac_hq["id"];
		}
	}
}

if($billing_global_server_name != 'miramar'){
$arr_selected_prov = explode(",", $int_prov);
$obj_scheduler->cache_prov_working_hrs($working_day_dt, $arr_selected_prov);


//to set scroll settings
$target_dat = "";
$tim_cur = "";
$tim_cur = date('H:i:00');

$arr_xml = $obj_scheduler->read_prov_working_hrs($working_day_dt, $arr_selected_prov);

if($arr_xml === false){
	echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:125px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">Office is closed.</div>____CLOSED____0____';
	die();
}

$start_time = "00:00";
if(is_array($arr_xml) && count($arr_xml) > 0){
	foreach($arr_xml as $k => $v){
		if($k != "dt" && isset($v["slots"])){
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

if($int_fac != 0){
	list($str_header, $str_time_pane, $str_appt_slots, $str_proc_summary, $total_prov, $arr_widths, $arr_not_processed, $arr_processed) = $obj_scheduler->write_html_content($arr_xml, array($int_fac), array($int_prov), $admin_priv, $time_array, "physician");

	list($column_width, $innercontainer, $scroll_width, $div_width, $total_prov) = $arr_widths;
	if((count($arr_processed) == 0 && count($arr_not_processed) > 0) || ($total_prov == 0)){
		echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:125px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">Office is closed.</div>____CLOSED____0____';
		die();
	}
}else{
	echo '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:125px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">No Facility is selected.</div>____CLOSED____0____';
	die();
}
?>

<div id="wn20">	
	<div id="mn_1" style="height:278px; ">
		<div id="mn1_1" style="width:100%;height:265px; overflow-x:hidden; overflow:scroll;float:left;">
			<div id="mnlyr1_1" style="width: 98%">
				<div id="hold_1" style="width:48px;">
					<div id="wn_1">
						<div id="lyr1_1">
							<div id="lr4" style="border-right:1px solid #999999;border-left:1px solid #999999;" class="setArrow">						
								<?php echo $str_time_pane;?>
							</div>								
						</div>
					</div>
				</div>
				<div id="hold" >  
					<div id="wn">
						<div id="lyr1">							
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
</div><?php
}
else
{
	$status_arr='';
	$status_arr[0]='Created';
	$qSt=imw_query("select * from schedule_status order by id");
	while($st=imw_fetch_object($qSt))
	{
		$status_arr[$st->id]=$st->status_name;
	}
	
	//get left off patients
	$qLeft= "select id FROM schedule_appointments where sa_facility_id IN ($int_fac) and sa_doctor_id = '$int_prov' and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20,11,13,3) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and '$working_day_dt' between sa_app_start_date and sa_app_end_date order by sa_app_starttime asc";
	$qLeft1=imw_query($qLeft)or die(imw_error());
	$left_off_appts=imw_num_rows($qLeft1);

	$query= "select id, sa_patient_id,sa_patient_name,sa_patient_app_status_id, TIME_FORMAT(sa_app_endtime, '%h:%i %p') as sa_app_endtime, sa_app_starttime as time_start_from, TIME_FORMAT(sa_app_starttime, '%h:%i %p') as sa_app_starttime FROM schedule_appointments
	WHERE sa_facility_id IN ($int_fac) 
	AND sa_doctor_id = '$int_prov' 
	AND sa_test_id = 0 
	AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
	AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) 
	AND '$working_day_dt' between sa_app_start_date and sa_app_end_date 
	ORDER BY time_start_from ASC";
	
	//miramar specific provide schedule preview
	$q=imw_query($query)or die(imw_error());
	
	?>
    <div id="wn20">	<div style="width:100%;">
    	<table class="table_collapse section_header" border="1" width="100%"><tbody><tr>
            <td style="width:28%; text-align:left">Appt. Time</td>
            <td style="width:20%; text-align:left">Status</td>
            <td style="width:52%; text-align:left">Patient Name</td>
        </tr></tbody></table>
        </div>
	<div id="mn_1">
    	
		<div id="mn1_1" style="width:100%;height:265px; overflow-x:hidden; overflow:scroll;">
			<div id="mnlyr1_1" style="width:100%">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
                <?php while($data=imw_fetch_object($q)){
					$total_appts++;
					//skip CO patient
					if($data->sa_patient_app_status_id=='11')continue;
					//skip DONE mark patients
					$checkPtIsDone=imw_query("select patient_location_id from patient_location where sch_id=$data->id and pt_with=6 ORDER BY patient_location_id desc limit 0,1")or die(imw_error().' 1');
					if(imw_num_rows($checkPtIsDone)!=0)continue;//skipp this record
					//skip Finalized Chart patients
					$checkPtIsFinalized=imw_query("select id from chart_master_table where patient_id='$data->sa_patient_id' and date_of_service='$working_day_dt' and finalize=1")or die(imw_error().' 2');
					if(imw_num_rows($checkPtIsFinalized)!=0)continue;//skip this record
					
					$time='';
					$time=$data->sa_app_starttime.'-'.$data->sa_app_endtime;
                	echo'<tr class="row">
                    	<td style="width:30%; text-align:left">'.$time.'</td>
                        <td style="width:20%; text-align:left">'.$status_arr[$data->sa_patient_app_status_id].'</td>
                        <td style="width:50%; text-align:left; cursor:pointer" onClick="showPAG(\''.$data->sa_patient_id.'\')" class="a_clr1 ">'.$data->sa_patient_name.' - '.$data->sa_patient_id.'</td>
                    </tr>';
                }// while for fetching recirds here
				if($total_appts)
				{
					$str_header="Appointments 
								&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
								Total: $total_appts 
								&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
								Remaining: $left_off_appts";
				}
				?>
                </table>
			</div>
		</div>
		<br clear="all">
	</div>
</div><?php	
}

//get total checked in patients
$qCheckCI=imw_query("select id from schedule_appointments where sa_facility_id IN ($int_fac) and sa_doctor_id = '$int_prov' and sa_test_id = 0 and sa_patient_app_status_id =13 and '$working_day_dt' between sa_app_start_date and sa_app_end_date order by sa_app_starttime asc");
if(imw_num_rows($qCheckCI) > 0)
{
	$CI_total ="Checked-In Patient: ".imw_num_rows($qCheckCI);
}
?>____<?php echo $obj_scheduler->getCheckedInPtList($int_fac, $working_day_dt, $int_prov);?>____<?php echo strip_tags($str_header);?>____<?php echo $CI_total;?>