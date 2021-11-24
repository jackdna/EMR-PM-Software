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
include_once("../../globals.php");
include("../appt_schedule_functions.php");
$obj_scheduler = new appt_scheduler();*/

require_once('../../../config/globals.php');
include_once($GLOBALS['srcdir'].'/classes/common_function.php');
include_once($GLOBALS['srcdir'].'/classes/scheduler/appt_schedule_functions.php');

$obj_scheduler = new appt_scheduler();
$working_day_dt_temp = date("m-d-Y");
list($tm, $td, $ty) = explode("-", $working_day_dt_temp);
$working_day_dt = $ty."-".$tm."-".$td;

$res_fellow_sess = trim($_SESSION['res_fellow_sess']);
$user_name_show = '';
if($res_fellow_sess != "" && isset($res_fellow_sess))
{
	$u_id = $res_fellow_sess;	
}
else
{
	$u_id = $_SESSION["authId"];
}

$user_name_arr = getUserDetails($u_id," lname, fname, mname ");
$user_name_show = core_name_format($user_name_arr['lname'],$user_name_arr['fname'],$user_name_arr['mname']);

$dt_selected = $working_day_dt;

$arrP = $obj_scheduler->getReminderPtList($u_id);

$strHTML = "";
$sel_sch = "select 
			sa.id, sa.sa_app_start_date, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as sa_app_starttime, TIME_FORMAT(sa.sa_app_endtime, '%h:%i %p') as sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id,sa.procedure_site, 
			sa.EMR, sa.checked, sa.pt_priority, 
			sp.proc_color, sp.acronym, sp.proc,
			st.status_name, st.status_icon, 
			sp2.times,sa.iolinkPatientId,sa.iolinkPatientWtId 
			from schedule_appointments sa 
			left join slot_procedures sp on sp.id = sa.procedureid 
			left join slot_procedures sp2 on sp2.id = sp.proc_time 
			left join schedule_status st on st.id = sa.sa_patient_app_status_id 
			where sa_doctor_id = '".$u_id."' and sa_test_id=0 and sa_patient_app_status_id IN (13) and '".$dt_selected."' between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime";
$res_sch = imw_query($sel_sch);
if($res_sch && imw_num_rows($res_sch)>0){
		$strHTML = '<h4>'.$user_name_show.'</h4>';	
		$strHTML .= '!~~!';	
		$strHTML .= '
			<div class="row">
				<table class="table table-bordered table-striped table-hover">
					<tr class="grythead">
						<th>Appt. Time</th>
						<th>Patient ID - Name</th>
						<th>Appt. Type</th>
						<th>Room#</th><th>Priority</th>
					</tr>';
					while($checkin_pat = imw_fetch_assoc($res_sch))
					{
						if($billing_global_server_name == 'miramar'){
						//skip DONE mark patients
						$checkPtIsDone=imw_query("select patient_location_id from patient_location where sch_id='$checkin_pat[id]' and pt_with=6 ORDER BY patient_location_id desc limit 0,1")or die(imw_error().' 1');
						if(imw_num_rows($checkPtIsDone)!=0)continue;//skipp this record
						//skip Finalized Chart patients
						$checkPtIsFinalized=imw_query("select id from chart_master_table where patient_id='$checkin_pat[sa_patient_id]' and date_of_service='$checkin_pat[sa_app_start_date]' and finalize=1")or die(imw_error().' 2');
						if(imw_num_rows($checkPtIsFinalized)!=0)continue;//skip this record
						}
						/*----Waiting icon status get----*/
						$room_qry = "SELECT ready4DrId, pt_with,app_room FROM patient_location WHERE patientId = '".$checkin_pat["sa_patient_id"]."' AND  cur_date = '".$checkin_pat["sa_app_start_date"]."' ORDER BY patient_location_id DESC LIMIT 1";
						$room_res =imw_query($room_qry);
						$room_arr = array();
						if(imw_num_rows($room_res) == 1){
							$room_arr = imw_fetch_assoc($room_res);
						}
						$flag = '';
						if($room_arr["ready4DrId"] > 0 || $room_arr['pt_with'] == 1)
						{
							$flag = "<div style=\"margin-right:5px;cursor:pointer;float:left;display:block;width:14px;\" title=\"".$room_arr[0]['doctor_mess']."\"><img src=\"../../library/images/status3.png\" title=\"Ready for Doctor\" /></div>";
						}
						
						$priority_arr = array(0=>'Normal',1=>'Priority 1',2=>'Priority 2',3=>'Priority 3');
						$checkin_pat["pt_priority"] = $priority_arr[$checkin_pat["pt_priority"]];
						
						$strHTML .= '<tr sch_id="'.$checkin_pat["id"].'">
										<td>'.$flag.' '.$checkin_pat['sa_app_starttime'].' - '.$checkin_pat['sa_app_endtime'].'</td>
										<td><a class="a_clr1" style="cursor:pointer;" onClick="LoadWorkView('.$checkin_pat['sa_patient_id'].')">'.$checkin_pat['sa_patient_id'].' - '.$checkin_pat['sa_patient_name'].'</a></td>
										<td class="chkin_room_cell">'.$checkin_pat["proc"].'</td>
										<td class="chkin_room_cell">'.$room_arr["app_room"].'</td>
										<td class="chkin_prio_cell">'.$checkin_pat["pt_priority"].'</td>
									</tr>';
					}
		$str_html .= '			
				</table>
			</div>';
}else{
	$strHTML = 'no';
}

$function = "
<script>
	function LoadWorkView(ptid){
        //To check restrict access of patient before load
        $.when(check_for_break_glass_restriction(ptid)).done(function(response){
            top.removeMessi();
            if(response.rp_alert=='y') {
                var patId=response.patId;
                var bgPriv=response.bgPriv;
                var rp_alert=response.rp_alert;
                top.core_restricted_prov_alert(patId, bgPriv, '');
            }else{
                top.focus();			
                rand = Math.round(Math.random()*555555);			
                top.core_set_pt_session('', ptid, '../chart_notes/work_view.php?&activateTab=Work_View&uniqueurl='+rand);			
                $('#phy_checkin_appts').modal('hide');	
            }
        });	
	}
</script>";

echo $strHTML.$function;
?>