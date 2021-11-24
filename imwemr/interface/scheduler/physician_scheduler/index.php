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

require_once('../../../config/globals.php');
include_once($GLOBALS['srcdir'].'/classes/common_function.php');
include_once($GLOBALS['srcdir'].'/classes/scheduler/appt_schedule_functions.php');

$appt_fun_obj = New appt_scheduler;
$library_path = $GLOBALS['webroot'].'/library';

$pt_id = $_SESSION["patient"];

if(inter_date_format() == "mm-dd-yyyy"){
	$dt_temp = (isset($_GET['dt']) && !empty($_GET['dt'])) ? $_GET['dt'] : get_date_format(date('Y-m-d'));
	list($dt_temp_m, $dt_temp_d, $dt_temp_y) = explode('-', $dt_temp);
	$sch_date = $dt_temp_m."/".$dt_temp_d."/".$dt_temp_y;
	$working_day_dt_new = $dt_temp_y."-".$dt_temp_m."-".$dt_temp_d;
}else if(inter_date_format() == "dd-mm-yyyy"){
	$dt_temp = (isset($_GET['dt']) && !empty($_GET['dt'])) ? $_GET['dt'] : get_date_format(date('Y-m-d'));
	list($dt_temp_d, $dt_temp_m, $dt_temp_y) = explode('-', $dt_temp);
	$sch_date = $dt_temp_d."/".$dt_temp_m."/".$dt_temp_y;
	$working_day_dt_new = $dt_temp_y."-".$dt_temp_m."-".$dt_temp_d;
}

$dt_day = date("l", mktime(0, 0, 0, $dt_temp_m, $dt_temp_d, $dt_temp_y));
$dt_selected = date('Y-m-d', mktime(0, 0, 0, $dt_temp_m, $dt_temp_d, $dt_temp_y));

$current_ts = time();

$res_fellow_sess = trim($_SESSION['res_fellow_sess']);
if($res_fellow_sess != "" && isset($res_fellow_sess)){
	$int_prov = $res_fellow_sess;	
}
else{
	$int_prov = (isset($_SESSION["authId"]) && $_SESSION["authId"] != "") ? $_SESSION["authId"] : 0;	
}

$user_name_arr = getUserDetails($int_prov," lname, fname, mname ");
$user_name_show = core_name_format($user_name_arr['lname'],$user_name_arr['fname'],$user_name_arr['mname']);

$int_fac = 0;

if(isset($_GET['loca']) && $_GET['loca'] != "" && $_GET['loca'] != "undefined"){
	$int_fac = $_GET['loca'];
}else{
	$arr_fac_timings = array();
	$arr_schedules = $appt_fun_obj->get_provider_schedules($dt_selected, array($int_prov));
	if(is_array($arr_schedules) && count($arr_schedules) > 0){
		foreach($arr_schedules as $v){
			//getting template timings
			$qry = imw_query("SELECT morning_start_time, morning_end_time FROM schedule_templates WHERE id = '".$v["sch_tmp_id"]."'");
			if(imw_num_rows($qry) > 0){
				while($arr = imw_fetch_array($qry)){
					//start time stamp
					$arr_st = explode(":", $arr["morning_start_time"]);
					$ts_st = mktime($arr_st[0], $arr_st[1], $arr_st[2], $dt_temp_m, $dt_temp_d, $dt_temp_y);

					//end time stamp
					$arr_ed = explode(":", $arr["morning_end_time"]);
					$ts_ed = mktime($arr_ed[0], $arr_ed[1], $arr_ed[2], $dt_temp_m, $dt_temp_d, $dt_temp_y);

					$arr_fac_timings[$v["facility"]]["start"] = $ts_st;
					$arr_fac_timings[$v["facility"]]["end"] = $ts_ed;
				}
			}
		}
	}
	
	$fac_found = false;
	if(count($arr_fac_timings) > 0){
		foreach($arr_fac_timings as $k => $v){
				$int_fac = $k;
				$fac_found = true;
				break;
		}
	}
	
	if($fac_found == false){
		//getting default facility of the provider
		$sel_fac_prov = "select default_facility from users where id = '".$int_prov."'";
		$res_fac_prov = imw_query($sel_fac_prov);
		if(imw_num_rows($res_fac_prov) > 0){
			$arr_fac_prov = imw_fetch_array($res_fac_prov);	
			$int_fac = $arr_fac_prov[0]["default_facility"];
		}else{
			//getting hq facility
			$sel_fac_hq = "select id from facility where facility_type = 1";
			$res_fac_hq = imw_query($sel_fac_hq);
			if(imw_num_rows($res_fac_hq) > 0){
				$arr_fac_hq = imw_fetch_array($res_fac_hq);
				$int_fac = $arr_fac_hq[0]["id"];
			}
		}
	}
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Physician Day Scheduler</title>
		<!-- Bootstrap -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/schedulemain.css"/>
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]--> 
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<style>
			.process_loader {
				border: 16px solid #f3f3f3;
				border-radius: 50%;
				border-top: 16px solid #3498db;
				width: 80px;
				height: 80px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
				display: inline-block;
			}
			.modal h4{margin-top:5px}
			td.pointer{color:purple;font-weight:bold}
		</style>
	</head>
	<body>
		<div class="mainwhtbox phy_day_sch">
			<input type="hidden" id="global_time_slot" name="global_time_slot" value="<?php echo DEFAULT_TIME_SLOT;?>" />
			<input type="hidden" id="dt" name="dt" value="<?php echo $dt_temp;?>">
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-6">
							<div class="input-group">
								<select name='loca' title="Choose Facility" id='loca' class="selectpicker" onChange="javascript:get_phy_scheduler(document.getElementById('dt').value, this.value)" data-width="100%" data-size="5">
									<?php
									if((int)$_SESSION["login_facility"]){$int_fac =(int)$_SESSION["login_facility"];}
									$vquery_cf = "select id, name from facility order by name";												
									$vsql_cf = imw_query($vquery_cf);
									while($vrsf = imw_fetch_array($vsql_cf)){
										$sel='';
										if($int_fac == $vrsf["id"]){
											$sel='selected';
										}
										echo "<option value='".$vrsf["id"]."' ".$sel.">".$vrsf["name"]."</option>";
									}
									?>
								</select>
								<label for="" class="input-group-addon pointer" id="calendar_elem">
									<span class="glyphicon glyphicon-calendar"></span>	
								</label>	
							</div>
						</div>	
						<div class="col-xs-5 col-xs-offset-1 pull-right">
							<div class="input-group">
								<input type="text" class="form-control" value="<?php echo $user_name_show; ?>" disabled>
								<label for="" class="input-group-addon pointer" onClick="refresh_sch()">
									<span class="glyphicon glyphicon-refresh link_cursor"></span>	
								</label>	
							</div>
						</div>	
					</div>	
				</div>
				<div class="col-xs-12 pt10">
					<div class="row purple_bar">
						<input type="hidden" id="load_dt" name="load_dt" value="<?php echo $sch_date;?>">
						<div id="show_day" class="col-xs-4">
							<?php echo $dt_day.", ".$dt_temp;?>
							
						</div>
						<div class="col-xs-7 text-center">
							<div class="row">
								<div class="col-xs-5">
									<span id="dt_tm"></span>	
								</div>
								<div class="col-xs-7">
									<span id="ci_total"></span>	
								</div>	
							</div>
						</div>
						<div class="col-xs-1 text-right">
							<button class="btn btn-xs btn-primary" onClick="show_pt_notes()">To Do</button>
						</div>	
					</div>	
				</div>
				<div class="col-xs-12">
					<div class="row" style="height:324px;overflow-y:auto">
						<table class="table table-striped table-hover table-bordered" style="margin-bottom: 0px!important">
							<thead>
								<tr class="grythead">
									<th>Appt. Time</th>
									<th>Proc - Patient Name</th>
									<th>Room</th>
									<th>Priority</th>
								</tr>
							</thead>
							<tbody id="day_checked_in"></tbody>	
						</table>	
					</div>	
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12 text-center purple_bar">
							<span id="show_prov_name"></span>	
						</div><br clear="all">

						<div id="day_save"></div>	
					</div>	
				</div>	
			</div>	
		</div>
		
		<div id="patient_notes_div" class="col-xs-12" style="display: none"></div>
		<!-- patient add notes modal -->
		<div class="common_wrapper">
			<div id="pt_add_notes" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<div class="row">
								<div class="col-xs-6">
									<h4>Add Comments/Reminder</h4>
								</div>	
								<div class="col-xs-5 text-right">
									<span><h4 id="dt_val"></h4></span>
								</div>	
								<div class="col-xs-1 text-right">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
								</div>	
							</div>
						</div>
						<div class="modal-body">
							<div class="row">
								<!-- Header -->
								<div class="col-xs-12 ">
									<div class="row purple_bar">
										<div class="col-xs-12 text-center">
											<label id="pt_name_val"></label>	
										</div>
									</div>		
								</div>
								<div class="col-xs-12 pt10">
									<div class="row">
										<input type="hidden" id="pat_id" name="pat_id" value="" />
										<input type="hidden" id="prov_id" name="prov_id" value="" />
										<input type="hidden" id="load_dt" name="load_dt" value="" />
										<textarea id="new_pat_note" name="new_pat_note" class="form-control" rows="2" style="resize:none"></textarea>
									</div>
								</div>	
							</div>
						</div>
						<div class="modal-footer">
							<div class="row">
								<div class="col-xs-12 text-center">
									<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-success" onclick="javascript:save_patient_notes('','','');">Save</button>	
								</div>	
							</div>
						</div>
					</div>
				</div>
			</div>	
		</div>
		
	</body>
	<script>
		function updateTime() {
			var serverTime = new Date();
			var hours = serverTime.getHours();
			var minutes = serverTime.getMinutes();
			var seconds = serverTime.getSeconds();
			var dn='PM';
			if (hours < 12)
				dn='AM';
			if (hours > 12)
				hours = hours-12;
			if (hours == 0)
				hours = 12;
			if (minutes <= 9)
				minutes = '0'+minutes;
			if (seconds <= 9)
				seconds = '0'+seconds;
			var ctime = hours+':'+minutes+':'+seconds+' '+dn;
			$('#dt_tm').html(ctime);
		}
		
		function refresh_sch(){
			get_phy_scheduler($('#dt').val(), $('#loca').val());
		}

		
		function get_phy_scheduler(dt, loca){
			var new_dt = dt.replace(/-/g,"/");	
			$('#load_dt').val(new_dt);
			
			$("#show_day").html(getTheDay(new_dt)+", "+new_dt);
			$("#closed_header").hide();
			$("#checked_in_patients").show();
			$("#day_save").addClass('text-center').html('<div class="process_loader text-center"></div>');
			$("#day_checked_in").html("<tr><td colspan='4' class='text-success text-center'>Loading appointments...Please wait.</td></tr>");
			$.ajax({
				url: "process.php?dt="+dt+"&loca="+loca,
				method: "GET",
				success: function(response){
					arr_response = response.split("____");
					if(arr_response[1] == "CLOSED"){
						$("#day_checked_in").html("<tr><td colspan='4' class='text-muted text-center'>Office is Closed.</td></tr>");
						$("#day_save").html("<div class='row'><div class='col-xs-12 text-muted'>No Appointments found.</div></div>");
						$("#show_prov_name").html('');
						$("#ci_total").html('');
					}else{
						$("#day_save").html(arr_response[0]);
						$("#day_checked_in").html(arr_response[1]);
						$("#show_prov_name").html(arr_response[2]);
						$("#ci_total").html(arr_response[3]);
						//image_replace();
						var day_st_tm = "";
						if($("#scroll_tim_limit3").get(0)){
							day_st_tm = parseInt($("#scroll_tim_limit3").val());
						}

						if(day_st_tm != ""){
							var last_logged_time = "";
							if($("#tim_cur2").get(0)){
								last_logged_time = $("#tim_cur2").val();
							}
							if(last_logged_time != ""){
								var arr_last_logged_time = last_logged_time.split(":");
								var last_logged_st_time = parseInt(arr_last_logged_time[0],10);
								if(day_st_tm < last_logged_st_time){
		
									last_logged_st_time = last_logged_st_time - day_st_tm;
									var global_time_slot = $("#global_time_slot").val();
									var slot_height = (12 * (global_time_slot / 5)) + 30;
		
									var scroll_px = (60 / global_time_slot) * last_logged_st_time * slot_height;
									//taking minutes into consideration
									var last_logged_st_min = parseInt(arr_last_logged_time[1],10);
									var min_slots = Math.ceil(last_logged_st_min / global_time_slot);
									scroll_px += (min_slots * slot_height); 
									// top offset setting							
									var top_min_gap = parseInt($('#scroll_tim_limit4').val(),10);
									if(top_min_gap > 0){						
										var top_min_gap_offset_min = 60 - top_min_gap;
										var top_min_gap_offset_val = Math.ceil(top_min_gap_offset_min/global_time_slot);
										scroll_px -= (top_min_gap_offset_val * slot_height);
									}
								}
							}
						}	
					}
				}
			});
		}
		
		function getTheDay(aText){
			myDays = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
			myDate=new Date(eval('"'+aText+'"'));
			return myDays[myDate.getDay()];
		}
		
		function show_pt_notes(){
			var load_dt = $('#load_dt').val();
			var cur_patid = '<?php echo $pt_id; ?>';
			window.opener.top.popup_win('patient_notes.php?load_dt='+load_dt+"&cur_patid="+cur_patid);
		}
		
		function addPatientNotes(pat_id, pat_name, prov_id, load_dt){		
			if(window.event.button==2){
				var formattedDate = new Date(load_dt);
				var d = formattedDate.getDate();
				var m =  formattedDate.getMonth();
				m += 1;  // JavaScript months are 0-11
				var y = formattedDate.getFullYear();
				
				if(m < 10){
					m = '0'+m;
				}
				
				if(d < 10){
					d = '0'+d;
				}
				var new_date = m + "-" + d + "-" + y;
				
				$('#pt_add_notes #dt_val').html(new_date);
				$('#pt_add_notes #pt_name_val').html(pat_name);
				
				$('#pt_add_notes #load_dt').val(new_date);
				$('#pt_add_notes #pat_id').val(pat_id);
				$('#pt_add_notes #prov_id').val(prov_id);
				
				$('#pt_add_notes').modal({
					backdrop: 'static',
					keyboard: false
				});
				$('#pt_add_notes').modal('show');
				document.oncontextmenu = false;
			}
		}
		
		function showWorkViewWindow(ptid){
            //To check restrict access of patient before load
            $.when(window.opener.top.check_for_break_glass_restriction(ptid)).done(function(response){
                top.removeMessi();
                if(response.rp_alert=='y') {
                    var patId=response.patId;
                    var bgPriv=response.bgPriv;
                    var rp_alert=response.rp_alert;
                    window.opener.top.core_restricted_prov_alert(patId, bgPriv, '');
                }else{
                    window.opener.top.focus();
                    rand = Math.round(Math.random()*555555);
                    window.opener.top.core_set_pt_session(window.opener.top.fmain, ptid, '../chart_notes/work_view.php?&activateTab=Work_View&uniqueurl='+rand);
                    window.close();
                }
            });
		}
		
		function save_patient_notes(mode, pageMode, id){
			var pat_id = $("#pt_add_notes #pat_id").val();
			var prov_id = $("#pt_add_notes #prov_id").val();
			var load_dt = $("#pt_add_notes #load_dt").val();
			var notes = $("#pt_add_notes #new_pat_note").val();
			$.ajax({ 
				url: "patient_notes.php?ajax_request=yes&id="+id+"&pat_id="+pat_id+"&prov_id="+prov_id+"&load_dt="+load_dt+"&notes="+escape(notes),
				success: function(resp){
					if($.trim(resp) > 0 && $.trim(resp) != ''){
						$('#pt_add_notes').modal('hide');
					}
				}
			});
		}
		
		function init_showPatientDiagnosisWindow(pid){
			window.opener.top.popup_win(window.opener.top.JS_WEB_ROOT_PATH+'/interface/chart_notes/past_diag/chart_patient_diagnosis.php?p_id='+pid,"PatientDiagnosisWindow","width=1200,height=600");
		}
		
		
		$(function(){
			updateTime();
			setInterval(updateTime, 1000);
		});
		
		$(document).ready(function(){
			window.resizeTo(600,810);
			get_phy_scheduler("<?php echo $dt_temp;?>", $("#loca").val());
			$('#calendar_elem').datetimepicker({timepicker:false,format:window.opener.top.jquery_date_format,formatDate:window.opener.top.jquery_date_format,autoclose: true,scrollInput:false,onChangeDateTime:function(r,$input){ var dt_val = $input.val();get_phy_scheduler(dt_val, $("#loca").val()); }});
		});
	</script>	
</html>	