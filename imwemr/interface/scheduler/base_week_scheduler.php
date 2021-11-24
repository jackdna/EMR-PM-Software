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

//require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_cl_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_ac_functions.php");
//require_once($GLOBALS['fileroot']."/library/classes/appt_cn_functions.php");

//scheduler object
$obj_scheduler = new appt_scheduler();
//$obj_contactlens = new appt_contactlens();
$obj_accounting = new appt_accounting();
//$obj_chartnotes = new appt_chartnotes($_SESSION['authId'], $_SESSION["patient"]);

//getting date
$load_date = date("m-d-Y");
if(isset($_REQUEST["sel_date"]) && !empty($_REQUEST["sel_date"])){
	$load_date = $_REQUEST["sel_date"];
}
list($m, $dt, $y) = preg_split('/-/', $load_date);

//if jumped
if(isset($_REQUEST["op_typ"]) && !empty($_REQUEST["op_typ"])){
	if(isset($_REQUEST["jmpto"]) && (int)$_REQUEST["jmpto"] > 0){
		if($_REQUEST["op_typ"] == "day"){
			$load_dt_ts_jmp = mktime(0, 0, 0, $m, $dt + $_REQUEST["jmpto"], $y);
			$load_date = date("m-d-Y", $load_dt_ts_jmp);
		}else if($_REQUEST["op_typ"] == "week"){
			$load_dt_ts_jmp = mktime(0, 0, 0, $m, $dt + ($_REQUEST["jmpto"] * 7), $y);
			$load_date = date("m-d-Y", $load_dt_ts_jmp);	
		}else if($_REQUEST["op_typ"] == "month"){
			$load_dt_ts_jmp = mktime(0, 0, 0, $m + $_REQUEST["jmpto"], $dt, $y);
			$load_date = date("m-d-Y", $load_dt_ts_jmp);
		}
	}
	//re getting month day and year
	list($m, $dt, $y) = preg_split('/-/', $load_date);
}

$load_dt_ts = mktime(0, 0, 0, $m, $dt, $y);
$db_load_date = date("Y-m-d", $load_dt_ts);

//getting next 3 mon and 6 mon dates
$load_dt_ts_3 = mktime(0, 0, 0, $m + 3, $dt, $y);
$load_date_3 = date("m-d-Y", $load_dt_ts_3);

$load_dt_ts_6 = mktime(0, 0, 0, $m + 6, $dt, $y);
$load_date_6 = date("m-d-Y", $load_dt_ts_6);
	
if(isset($_SESSION['sess_sch_sel_facs']) && !empty($_SESSION['sess_sch_sel_facs'])){
	$arr_sess_facs = explode(',',$_SESSION['sess_sch_sel_facs']);
}else{
	$fac_db_cache = $obj_scheduler->get_session_cache("FAC_SELECT", $_SESSION["authId"]); 
	if($fac_db_cache !== false){
		$arr_sess_facs =$fac_db_cache;
	}else{
		$arr_sess_facs = "";
		$arr_default_facs = array();
		$arr_facs_temp = $obj_scheduler->load_facilities($_SESSION["authId"], "ARRAY");
		for($fc_cnt = 0; $fc_cnt < count($arr_facs_temp); $fc_cnt++){
			$arr_default_facs[] = $arr_facs_temp[$fc_cnt]["id"];
		}
		$arr_sess_facs = $arr_default_facs;			
	}		
}	

if(isset($_REQUEST["sel_pro_month"]) && !empty($_REQUEST["sel_pro_month"])){
	if($_SESSION['sess_sch_week_prov'] != $_REQUEST["sel_pro_month"]){
		$_SESSION['sess_sch_week_prov'] = $_REQUEST["sel_pro_month"];//explode(",", $_REQUEST["sel_pro"]);
		$arr_sess_prov = explode(",", $_REQUEST["sel_pro_month"]);
		$obj_scheduler->save_session_cache("WEEKLY_PROV", $_REQUEST["sel_pro_month"], $_SESSION["authId"]);
	}
}else{
	if(isset($_SESSION['sess_sch_week_prov']) && !empty($_SESSION['sess_sch_week_prov'])){
		$arr_sess_prov = explode(",", $_SESSION['sess_sch_week_prov']);
	}else{
		$arr_default_prov = array();
		$pro_db_cache = $obj_scheduler->get_session_cache("WEEKLY_PROV", $_SESSION["authId"]); 
		if($pro_db_cache !== false){
			$arr_sess_prov = $pro_db_cache;
		}else{
			$arr_sess_prov = "";
		}
		$arr_sess_prov = array_unique($arr_sess_prov);
	}
}

//populating week list
$arr_week_list = $obj_scheduler->generate_week_list($db_load_date);

//getting copay policy - user in nav_bar.php and fd_search_patient.php
$arr_billing_policies = $obj_accounting->get_billing_policies();
if($arr_billing_policies[0]["cpt_print_details"] == "yes"){
	if($arr_billing_policies[0]["show_check_in"] > 0){
		$show_payment_box_chk_in = "check in";
	}else{
		$show_payment_box_chk_in = "";
	}
	if($arr_billing_policies[0]["show_check_out"] > 0){
		$show_payment_box_chk_out = "check out";
	}else{
		$show_payment_box_chk_out = "";
	}
}
if($arr_billing_policies[0]["ci_demographics"] == 1){
	$show_ci_demographics="yes";
}else{
	$show_ci_demographics="no";
}
unset($arr_sess_prov['c_content']);
if(count($arr_sess_prov)==0 || !$arr_sess_prov || $arr_sess_prov==""){
		$arr_sess_prov=explode(',',$_SESSION['sess_sch_sel_prov']);
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>imwemr :: Weekly Appointments Viewer</title>
		
		<meta name="viewport" content="width=device-width, maximum-scale=0.8" />
		<meta charset="UTF-8" />		
        
		<!-- Bootstrap -->
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css">
    	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">

     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css"><!--
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/style.css">-->
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-colorpicker.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css?version=<?php echo fileatime('../../library/css/schedulemain.css');?>">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-multiselect.css">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css?version=<?php echo fileatime('../../library/css/common.css');?>">     
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="<?php echo $GLOBALS['webroot']?>/library/js/html5shiv.min.js"></script>
          <script src="<?php echo $GLOBALS['webroot']?>/library/js/respond.min.js"></script>
        <![endif]-->
        
        <script type="text/javascript" src="js_week_scheduler.php"></script>
        
		<script>
		//	$(function() {
//				$( "#msgDiv" ).draggable({ handle: "div.msgDiv-handle" });				
//			});
		</script>

	</head>
	<body onkeypress="keyPressHandler(this);">
		<!-- base hidden fields for storing date -->
		<input type="hidden" id="global_date" name="global_date" value="<?php echo $dt; ?>"/>
		<input type="hidden" id="global_month" name="global_month" value="<?php echo $m; ?>"/>
		<input type="hidden" id="global_year" name="global_year" value="<?php echo $y; ?>"/>

		<!-- appt context menu pt id and appt id -->
		<input type="hidden" id="global_context_ptid" name="global_context_ptid" value=""/>
		<input type="hidden" id="global_context_apptid" name="global_context_apptid" value=""/>
		
		<input type="hidden" id="global_context_apptsttm" name="global_context_apptsttm" value=""/>
		<input type="hidden" id="global_context_apptdoc" name="global_context_apptdoc" value=""/>
		<input type="hidden" id="global_context_apptfac" name="global_context_apptfac" value=""/>
		<input type="hidden" id="global_context_apptstdt" name="global_context_apptstdt" value=""/>
		<input type="hidden" id="global_iolink_connection_settings_id" name="global_iolink_connection_settings_id" value=""/>
		<input type="hidden" id="global_apptactreason" name="global_apptactreason" value=""/>

		<!-- slot context menu pt id and appt id -->
		<input type="hidden" id="global_context_slsttm" name="global_context_slsttm" value=""/>
		<input type="hidden" id="global_context_sldoc" name="global_context_sldoc" value=""/>
		<input type="hidden" id="global_context_slfac" name="global_context_slfac" value=""/>
		<input type="hidden" id="global_context_slstdt" name="global_context_slstdt" value=""/>

		<input type="hidden" id="global_ptid" name="global_ptid" value="<?php echo $pat_id; ?>"/>        
        <input type="hidden" id="sel_pat_name" name="sel_pat_name" value=""/>        
        <input type="hidden" id="sel_proc_id" name="sel_proc_id" value=""/>        

		<!-- base hidden fields for appointment -->
		<input type="hidden" id="global_apptid" name="global_apptid" value=""/>
		<input type="hidden" id="global_apptact" name="global_apptact" value="addnew"/>
        
        <input type="hidden" id="ctrl_weekly" name="ctrl_weekly" value="weekly"/>

		<!-- base hidden fields for accounting -->
		<input type="hidden" id="show_payment_box_chk_out" name="show_payment_box_chk_out" value="<?php echo $show_payment_box_chk_out;?>" />
		<input type="hidden" id="show_payment_box_chk_in" name="show_payment_box_chk_in" value="<?php echo $show_payment_box_chk_in;?>" />
		<input type="hidden" id="show_ci_demographics" name="show_ci_demographics" value="<?php echo $show_ci_demographics;?>" />
		<!-- scheduler warning -->
		<!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="msgDiv" class="modal fade" role="dialog">
            <div class="modal-dialog">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="msgTitle"></h4>
                    </div>
                    <div class="modal-body" id="msgBody">
                    	
                    </div>
                    <div class="modal-footer" id="msgFooter">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!--modal wrapper class end here -->
        
		<!-- print options div -->
        <!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="day_print_options_div" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                   		<button type="button" class="close" data-dismiss="modal">&times;</button>
                    	<h4 class="modal-title">
                            <div id="day_print_options_div-handle" class="text-left">
                                <div class="fl" id="print_options_date"></div>
                                <div class="fl"> - Print Options</div>
                            </div>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="print_options_content">
                         </div>
                    </div>
                    <div class="modal-footer" style="overflow:visible" id="day_print_options_footer"></div>
                </div>
            </div>
        </div>
		</div>
        <!--modal wrapper class end here -->
        
		<!-- day proc summary div -->
        <!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="day_proc_summ_div" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                   		<button type="button" class="close" data-dismiss="modal">&times;</button>
                    	<h4 class="modal-title">
                            <div id="day_proc_summ_div-handle" class="text-left">
                                <div class="fl" id="day_proc_summ_date"></div>
                                <div class="fl"> - Day Summary</div>
                            </div>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="baseContentDiv" style="width:100%; height:100%;background-color:#ffffff; overflow-y:scroll;overflow-x:hidden;">
                            <div id="day_proc_summ_content"></div>
                        </div>
                    </div>
                    <div class="modal-footer" style="overflow:visible" id="day_print_options_footer"></div>
                </div>
            </div>
        </div>
		</div>
        <!--modal wrapper class end here -->
		
			
		<!-- provider notes -->
        <!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="provider_notes_div" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                   		<button type="button" class="close" data-dismiss="modal">&times;</button>
                    	<h4 class="modal-title">
                            <div id="provider_notes_div_header" class="text-left">
                            </div>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="provider_notes_div_content"></div>
                    </div>
                    <div class="modal-footer" style="overflow:visible" id="provider_notes_div_footer"></div>
                </div>
            </div>
        </div>
		</div>
        <!--modal wrapper class end here -->
        

		<div style="width:100%;height:<?php echo $_SESSION["wn_height"] - 242;?>px;margin:0px;">
        	<!-- Navigation Bar -->
            <div class="container-fluid scheduleara scheduleara_weekly green_bg">
                 <div class="schedfltara">
                    <div class="row">
                    	<div class="col-sm-5 form-inline schflt">
                            <div class="form-group multiselect">
                                <label for="sel_pro_month">Physician</label>
                                <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-size="10" id="sel_pro_month" name="sel_pro_month[]" data-max-options="1">
                                <?php echo $obj_scheduler->load_providers("OPTIONS", $sel_prov);?>
                                </select>
                            </div>                               
                            <div class="form-group multiselect">
                                <label for="exampleInputEmail2">Facility</label>
                                <select class="selectpicker minimal selecicon" multiple data-done-button="true" data-actions-box="true" data-size="10" id="facilities" name="facilities[]">
                                    <?php echo $obj_scheduler->load_facilities($_SESSION["authId"], "OPTIONS", $sel_fac);?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-sm-3 datesched_wk">
                                    <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('day');">1</a> 
                                    <a href="javascript:void(0);" class="active">7</a> 
                                    <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('month');">31</a>
                                </div>
                                <div class="col-sm-5 text-center calndmonth">
                                    <ul> 
                                        
                                        <li>
                                            <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('week', '<?php echo $arr_week_list[2];?>');" title="Previous Week">
                                                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/lft1.png" alt="Previous Week" title="Previous Week"/>
                                            </a>
                                        </li>
                                        
                                        <li id="sel_month_year_container">
                                            <select class="form-control minimal selecicon" id="sel_week" name="sel_week" onchange="javascript:toggle_sch_type('week', this.value);">
                                                <?php echo $arr_week_list[0];?>
                                            </select>
                                        </li> 
                                        
                                        <li>
                                            <a href="javascript:void(0);" onClick="javascript:toggle_sch_type('week', '<?php echo $arr_week_list[1];?>');" title="Next Week">
                                                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/rtarrow.png" alt="Next Week" title="Next Week"/>
                                            </a>
                                        </li> 
                                        <li>
                                            <label style="margin-bottom:0px">
                                                <input type="text" id="dt" name="dt" value="<?php echo $load_date;?>" style="z-index:0; position:absolute; top:-40px">
                                                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/calendar1.png" id="dt_img" style="width: 30px">
                                            </label>
                                        </li>
                                    </ul>
                            </div>
                                <div class="col-sm-4 period text-right">
                                    <div class="btn-group" role="group" aria-label="...">
                                      <button type="button" class="btn btn-default" name="today_button" id="today_button" value="Today" onClick="javascript:toggle_sch_type('week', '<?php echo date("m-d-Y");?>');">Today</button>
                                      <button type="button" class="btn btn-default" name="3_mon" id="3_mon" value="3 Mon" onClick="javascript:toggle_sch_type('week', '<?php echo $load_date_3;?>');">3 Mon</button>
                                      <button type="button" class="btn btn-default" name="6_mon" id="6_mon" value="6 Mon" onClick="javascript:toggle_sch_type('week', '<?php echo $load_date_6;?>');">6 Mon</button>
                                      
                                     <!-- <div class="fl pr5 tp_scroll_ctl_bin">
                                <input type="button" value="»" id="rightArrow" title="Scroll Right" class="tp_scroll_ctl">
                            </div>-->
                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <!-- ----------------------------------------------------------------------------------- -->
			<!-- Navigation Bar -->
			<!--<div class="title_bar fl nav_container">
				
				
				<div class="fr">
					
					<form name="frm_jump_to" action="" method="get">
						<input type="hidden" id="sel_date" name="sel_date" value="<?php echo $load_date;?>">
					<div class="fl" style="padding-top:2px;">Jump To : <input type="text" size="5" name="jmpto" id="jmpto" style="width:25px;" value=""></div>
					<div class="fl pl5" style="padding-top:3px;">
						<select name="op_typ" id="op_typ">
							<option value="day">Days</option>
							<option value="week">Weeks</option>
							<option value="month">Months</option>
						</select>
					</div>
					<div class="fl pl5" style="padding-top:2px;"><input type="submit" id="go" value="Go" class="nav_button"></div>
					</form>
					<div class="fl pl5 mr5" style="right:2px;"><input type="button" value="&nbsp;" onClick="javascript:toggle_sch_type('week', '<?php echo $load_date;?>');" class="sc_refresh_button" title="Reload Scheduler"></div>		
				</div>
			</div>
			<div style="clear:both;"></div>-->
			<!-- calendar and front desk -->
			<div id="week_save" class="fl" style="background-color:#FFF;overflow-x: auto;height: <?php echo $_SESSION["wn_height"] - 280;?>px;"></div>
            
            <!-- drag add / resch appt -->
            <div class="tool_tip" id="appt_drag" style="display:none; cursor:move; font-weight:bold;"></div>
           
		</div>
		<script>
			/*
			Purpose: on load actions
			Author: ravi, prabh
			*/
			$(document).ready( function() {
				
				var date_global_format = top.jquery_date_format;
				$('#dt').datetimepicker({
					timepicker:false,
					format:date_global_format,
					formatDate:'Y-m-d',
					scrollInput:false
				}).change(function(){ 
						var dt_val = $("#dt").val();
						dt_val = top.getDateFormat(dt_val,'mm-dd-yyyy');
						toggle_sch_type('week', dt_val);
					});
				
				
				$('#facilities').on('hide.bs.select', function() {
				  fac_change_load('week'); // or $(this).val()
				});
				
				$('#sel_pro_month').on('hide.bs.select', function() {
				  pro_change_load('week'); // or $(this).val()
				});	 
			});
			
			load_week_scheduler('<?php echo implode(",", $arr_sess_facs);?>', '<?php echo $arr_sess_prov[0];?>');
			//load_week_appt_schedule();
		</script>
	</body>
</html>