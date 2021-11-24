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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');

//require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_cl_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_ac_functions.php");
//require_once($GLOBALS['fileroot']."/library/classes/appt_cn_functions.php");

$max_schedules_per_slide=constant('MAX_SCHEDULE_PER_SLIDE');
$max_schedules_per_slide=(!$max_schedules_per_slide)?5:$max_schedules_per_slide;

//scheduler object
$obj_scheduler = new appt_scheduler();
//$obj_contactlens = new appt_contactlens();
$obj_accounting = new appt_accounting();
//$obj_chartnotes = new appt_chartnotes($_SESSION['authId'], $_SESSION["patient"]);

//this function is stopped since R6 - due to slow query and time cosuming job
//$obj_scheduler->set_auto_status();

//getting copay policy - user in nav_bar.php and fd_search_patient.php
$arr_billing_policies = $obj_accounting->get_billing_policies();
if($arr_billing_policies["cpt_print_details"] == "yes"){
	if($arr_billing_policies["show_check_in"] > 0){
		$show_payment_box_chk_in = "check in";
	}else{
		$show_payment_box_chk_in = "";
	}
	if($arr_billing_policies["show_check_out"] > 0){
		$show_payment_box_chk_out = "check out";
	}else{
		$show_payment_box_chk_out = "";
	}
}
if($arr_billing_policies["ci_demographics"] == 1){
	$show_ci_demographics="yes";
}else{
	$show_ci_demographics="no";
}

//getting date
$load_date = date("m-d-Y");
if(isset($_REQUEST["sel_date"]) && !empty($_REQUEST["sel_date"])){
	$load_date = $_REQUEST["sel_date"];
}elseif(isset($_SESSION['last_sch_date']) && !empty($_SESSION['last_sch_date']))
{
	$load_date = date("m-d-Y", strtotime($_SESSION['last_sch_date']));
}
list($this_m, $dt, $y) = preg_split('/-/', $load_date);

$db_load_date = date("Y-m-d", mktime(0, 0, 0, $this_m, $dt, $y));
$ts_load_date = mktime(0, 0, 0, $this_m, $dt, $y);
$strGetDayName = date("l", mktime(0, 0, 0, $this_m, $dt, $y));

//populating minth list
$arr_month_list = $obj_scheduler->generate_month_list($db_load_date);
$month_data = $arr_month_list[0];

//admin access privilege ?
$strPrivilegeCheck = (core_check_privilege(array("priv_admin")) == false) ? 0 : 1;

//load patient from session in front desk
if(isset($_REQUEST["pat_id"]) && !empty($_REQUEST["pat_id"])){
	if(isset($_REQUEST["pat_id"]))
	$pat_id = $_REQUEST["pat_id"];
}else{
	if(isset($_SESSION["patient"]))
	$pat_id = $_SESSION["patient"];
}

//product specific settings
$styleCommon = ""; 
if(DEFAULT_PRODUCT == "imwemr"){ 
	$styleCommon = "style=\"display:none;\"";  
}

if(isset($_REQUEST["sel_fac"]) && $_REQUEST["sel_fac"] != ""){
	$arr_sess_facs = array();
	$arr_sess_facs[] = $_REQUEST["sel_fac"];
	$sel_fac = $_REQUEST["sel_fac"];
}
elseif(isset($_SESSION['sess_sch_sel_facs']) && !empty($_SESSION['sess_sch_sel_facs'])){
	$arr_sess_facs = $_SESSION['sess_sch_sel_facs'];
}
else
{
	//get scheduler cached data for facility
	$fac_db_cache = $obj_scheduler->get_session_cache("FAC_SELECT", $_SESSION["authId"]); 
	if($fac_db_cache !== false){
		$arr_sess_facs = implode(",", $fac_db_cache);
	}else if(empty($arr_sess_facs) && $_SESSION["authId"]) { //IM-6581:- Default location ends here
        $qry = "SELECT default_facility, sch_facilities FROM users WHERE id = '".$_SESSION["authId"]."'";
        $res1 = imw_query($qry);
        if($res1 && imw_num_rows($res1) > 0){
            $arr_usr =  imw_fetch_assoc($res1);
            $sch_facilities = explode(";", $arr_usr["sch_facilities"]);
            if(in_array($arr_usr['default_facility'], $sch_facilities)){
                $arr_sess_facs=$arr_usr['default_facility'];
            }
        }
    } else{
		$arr_sess_facs = "";
	}
}
if(isset($_REQUEST["sel_prov"]) && $_REQUEST["sel_prov"] != ""){
	$arr_sess_prov = array();
	$arr_sess_prov[] = $_REQUEST["sel_prov"];
	$sel_prov = $_REQUEST["sel_prov"];
}
elseif(isset($_SESSION['sess_sch_sel_prov']) && !empty($_SESSION['sess_sch_sel_prov'])){
	$arr_sess_prov = $_SESSION['sess_sch_sel_prov'];
}else{
	//get scheduler cached data for provider
	$pro_db_cache = $obj_scheduler->get_session_cache("PRO_SELECT", $_SESSION["authId"]); 
	if($pro_db_cache !== false){
		$arr_sess_prov = implode(",", $pro_db_cache);
		if(isset($pro_db_cache[0]))
		$week_pro=$pro_db_cache[0];
		else
		$week_pro=false;
		
		$obj_scheduler->save_session_cache("WEEKLY_PROV", $week_pro, $_SESSION["authId"]);
	}else{
		$arr_sess_prov = "";
	}
}

/*iPortal patient registration alert*/
$div_front_layer_zindex = 6000;
$iportal_ch_demographics = '';
if(isset($_SESSION['IPORTAL_DEMOGRAPHICS']))
	$iportal_demo=$_SESSION['IPORTAL_DEMOGRAPHICS'];
else
	$iportal_demo=0;

if((core_check_privilege(array('priv_pt_fdsk'),'all')==true || core_check_privilege(array('priv_pt_clinical'),'all')==true) && $iportal_demo==0){
	if(isset($pid))
		$iportal_ch_demographics0 = iportal_demographics_changes_list($pid);
	else
		$iportal_ch_demographics0=false;
	
	$iportal_ch_demographics = $iportal_ch_demographics0[0];
	$hidden_approveIds = $iportal_ch_demographics0[1];
	if(empty($iportal_ch_demographics)==false){
		$_SESSION['IPORTAL_DEMOGRAPHICS']=1;
	}
}

$iportal_cl_orders = '';
if(core_check_privilege(array('priv_pt_fdsk'),'all')==true && isset($_SESSION['IPORTAL_CL_DISPLAYED'])==false){
	$iportal_cl_orders = iportal_cl_orders_list();
	if(empty($iportal_cl_orders)==false){
		$_SESSION['IPORTAL_CL_DISPLAYED']=1;
	}
}

$patientDeceased = is_deceased($pat_id);
$getField = get_copay_field('checkin_on_done');
$checkin_on_done = $getField['checkin_on_done'] ? 1 : '';
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>imwemr :: Appointment Scheduler</title>
		
		<meta name="viewport" content="width=device-width, maximum-scale=0.8" />
		<meta charset="UTF-8" />
       	<!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css">
    	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-dropdownhover.min.css">

     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.mCustomScrollbar.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/normalize.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css">
		<link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-colorpicker.css">
        <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css?version=<?php echo fileatime('../../library/css/schedulemain.css');?>">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-multiselect.css">
     	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css?version=<?php echo fileatime('../../library/css/common.css');?>">
		<link rel='stylesheet' href='<?php echo $GLOBALS['webroot'];?>/library/js/grid_color/spectrum.css' />
        <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
            <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
        <?php } ?>
    	<script>
			//variable being used in function moved in sc_script.js
			var JS_SCHEDULER_VERSION = "<?php echo SCHEDULER_VERSION;?>";
			var get271Report_hight='<?php echo $_SESSION['wn_height'] - 140; ?>';
			var approve_operation_dt = "<?php echo $_SERVER['PHP_SELF']; ?>";
			var WEB_ROOT='<?php echo $GLOBALS['webroot']?>';
			var date_format = '<?php echo $GLOBALS['date_format']; ?>';
			var MAX_SCHEDULE_PER_SLIDE='<?php echo $max_schedules_per_slide; ?>';
			var hidden_approveIds = '<?php echo $hidden_approveIds; ?>';
			var MD_API = '<?php echo (sizeof($GLOBALS["API_PROCEDURES"])>0)?'On':'';?>';
		</script>
   					
        <script>ser_root = "<?php echo $GLOBALS['webroot']?>/data/<?php echo constant('PRACTICE_PATH');?>/xml/refphy";</script>
	</head>
	<body>
		<input type="hidden" id="global_time_slot" name="global_time_slot" value="<?php echo DEFAULT_TIME_SLOT;?>" />
		<input type="hidden" id="global_admin" name="global_admin" value="<?php echo $strPrivilegeCheck;?>" />
		<input type="hidden" id="CHECKIN_ON_DONE" name="CHECKIN_ON_DONE" value="<?php echo $checkin_on_done;?>" />
		
		<!-- base hidden fields for storing date -->
		<input type="hidden" id="global_date" name="global_date" value="<?php echo $dt; ?>"/>
		<input type="hidden" id="global_month" name="global_month" value="<?php echo $this_m; ?>"/>
		<input type="hidden" id="global_year" name="global_year" value="<?php echo $y; ?>"/>
		<input type="hidden" id="global_appttempproc" name="global_appttempproc" value=""/>
	
		<!-- base hidden fields for patient -->
		<input type="hidden" id="global_ptid" name="global_ptid" value="<?php echo (isset($pat_id))?$pat_id:0; ?>"/>
		<input type="hidden" id="global_ptfname" name="global_ptfname" value=""/>
		<input type="hidden" id="global_ptmname" name="global_ptmname" value=""/>
		<input type="hidden" id="global_ptlname" name="global_ptlnames" value=""/>
		<input type="hidden" id="global_ptemr" name="global_ptemr" value=""/>

		<!-- base hidden fields for appointment -->
		<input type="hidden" id="global_apptid" name="global_apptid" value=""/>
		<input type="hidden" id="global_apptact" name="global_apptact" value=""/>
		<input type="hidden" id="global_apptactreason" name="global_apptactreason" value=""/>
		<input type="hidden" id="global_apptstid" name="global_apptstid" value=""/>
		<input type="hidden" id="global_apptsttm" name="global_apptsttm" value=""/>
		<input type="hidden" id="global_apptdoc" name="global_apptdoc" value=""/>
		<input type="hidden" id="global_apptfac" name="global_apptfac" value=""/>
		<input type="hidden" id="global_apptpro" name="global_apptpro" value=""/>
		<input type="hidden" id="global_apptsecpro" name="global_apptsecpro" value=""/>
		<input type="hidden" id="global_apptterpro" name="global_apptterpro" value=""/>                
		<input type="hidden" id="global_apptstdt" name="global_apptstdt" value=""/>

		<!-- appt context menu pt id and appt id -->
		<input type="hidden" id="global_context_ptid" name="global_context_ptid" value=""/>
		<input type="hidden" id="global_context_apptid" name="global_context_apptid" value=""/>
		<input type="hidden" id="global_context_apptsttm" name="global_context_apptsttm" value=""/>
		<input type="hidden" id="global_context_apptdoc" name="global_context_apptdoc" value=""/>
		<input type="hidden" id="global_context_apptfac" name="global_context_apptfac" value=""/>
		<input type="hidden" id="global_context_apptstdt" name="global_context_apptstdt" value=""/>
		<input type="hidden" id="global_context_apptlbty" name="global_context_apptlbty" value=""/>
		<input type="hidden" id="global_context_apptlbtx" name="global_context_apptlbtx" value=""/>
		<input type="hidden" id="global_context_apptlbcl" name="global_context_apptlbcl" value=""/>
        <input type="hidden" id="global_iolink_connection_settings_id" name="global_iolink_connection_settings_id" value=""/>
        <input type="hidden" id="global_iolink_mode" name="global_iolink_mode" value=""/>
        <input type="hidden" id="global_iolink_ocular_hx_form_id" name="global_iolink_ocular_hx_form_id" value=""/>
        <input type="hidden" id="global_replace_lbl" name="global_replace_lbl" value=""/>
        
		<!-- slot context menu pt id and appt id -->
		<input type="hidden" id="global_context_slsttm" name="global_context_slsttm" value=""/>
		<input type="hidden" id="global_context_sldoc" name="global_context_sldoc" value=""/>
		<input type="hidden" id="global_context_slfac" name="global_context_slfac" value=""/>
		<input type="hidden" id="global_context_slstdt" name="global_context_slstdt" value=""/>

		<!-- calendar context menu date -->
		<input type="hidden" id="global_context_caldt" name="global_context_caldt" value=""/>

		<!-- base hidden fields for accounting -->
		<input type="hidden" id="global_apptinsid" name="global_apptinsid" value=""/>

		<!-- base hidden fields for contact lens -->
		<input type="hidden" id="global_recsupply" name="global_recsupply" value=""/>
		<input type="hidden" id="global_recordid" name="global_recordid" value=""/>
		<input type="hidden" id="global_ordsupply" name="global_ordsupply" value=""/>
		<input type="hidden" id="global_orderid" name="global_orderid" value=""/>
		<input type="hidden" id="global_trialtype" name="global_trialtype" value=""/>
		<input type="hidden" id="global_trialcost" name="global_trialcost" value=""/>
		<input type="hidden" id="global_fnlclrxid" name="global_fnlclrxid" value=""/>

		<!-- base hidden fields for accounting -->
		<input type="hidden" id="show_payment_box_chk_out" name="show_payment_box_chk_out" value="<?php echo $show_payment_box_chk_out;?>" />
		<input type="hidden" id="show_payment_box_chk_in" name="show_payment_box_chk_in" value="<?php echo $show_payment_box_chk_in;?>" />
        <input type="hidden" id="show_ci_demographics" name="show_ci_demographics" value="<?php echo $show_ci_demographics;?>" />
		
		<!-- base hidden fields for adding new prov schedule -->
		<input type="hidden" id="prov_sch_add_type" name="prov_sch_add_type" value="" />

		<!-- base hidden fields for highlighting prov schedules -->
		<input type="hidden" id="loaded_cls" name="loaded_cls" value="" />
		<input type="hidden" id="loaded_cls_obj" name="loaded_cls_obj" value="" />
		<input type="hidden" id="loaded_first_month" name="loaded_first_month" value="<?php echo $y."-".$this_m."-01";?>" />
		
		<!-- init time and proc fields for rescheduling -->
        <input type="hidden" id="init_date_rs" name="init_date_rs" value="" />
		<input type="hidden" id="init_st_time_rs" name="init_st_time_rs" value="" />
		<input type="hidden" id="init_et_time_rs" name="init_et_time_rs" value="" />
		<input type="hidden" id="init_acronym_rs" name="init_acronym_rs" value="" />
        <input type="hidden" id="init_prov_id" name="init_prov_id" value="" />
        <input type="hidden" id="init_fac_id" name="init_fac_id" value="" />
		<input type="hidden" id="hash_method" name="hast_method" value="<?php echo constant('HASH_METHOD'); ?>">
        
        <!-- hidden field to store global settings -->
        <input type="hidden" id="ENABLE_SCHEDULER_RAIL_CHECK" name="ENABLE_SCHEDULER_RAIL_CHECK" value="<?php echo constant('ENABLE_SCHEDULER_RAIL_CHECK'); ?>"/>
        
        <!-- field to store clicked slot template id -->
        <input type="hidden" id="global_context_appt_tmp_id" name="global_context_appt_tmp_id" value=""/>
        
        
        <div class="clearfix"></div>
        <div class="container-fluid green_bg">
            <div class="schedfltara">
	            <div class="row">
                	<!--physician search top bar search start here-->
                    <?php  require_once("base_day_scheduler_top.php"); ?>
                    <!--physician search top bar search ends here-->
                </div>
            </div>
        </div>
		<div class="clearfix"></div>
        <div class="scheduleara">
            <div class="container-fluid">
            	<div class="row">
                    <!-- Navigation Bar -->
                    <?php require_once("nav_bar.php"); ?>
                </div>
        		<div class="clearfix"></div>
                <div class="row">
					<div class="col-lg-5 col-md-12 col-sm-12 ">
                    	<!--left scheduler will be here-->
                        <div class="schedulelft" id="sch_left_portion">
							<div class="row cland" id="month_h">
								<?php require_once("cal_month_view.php"); ?>
                            </div>
        					<div class="clearfix"></div>
                            <div id="month_h_disable"></div>
        					<div class="clearfix"></div>
                            <div id="front_desk_container">
								<!-- calendar and front desk -->	
                                <?php require_once("fd_search_patient.php"); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-sm-12 schedulerht" id="day_save">
                        <!--right scheduler will be here-->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- a&p -->
		<!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="AssesmentDiv" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Assesment and Plan</h4>
                    </div>
                    <div class="modal-body">
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!--modal wrapper class end here -->
        
        <!-- add new provider schedule -->
    	<!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="div_add_prov_form" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                   		<button type="button" class="close" data-dismiss="modal">&times;</button>
                    	<h4 class="modal-title">
                            <span id="setTimeTitle">Add New Provider Schedule </span>
                            [<span id="anps_day_name"><?php echo $this_m."-".$dt."-".$y; ?></span>]
                        </h4>
                    </div>
                    <div class="modal-body">
                            <div class="form-group">
                            	<div id="div_add_sch_detail">
									<?php require_once("add_prov_sch_form.php"); ?>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer" style="overflow:visible">
                    	<button type="button" class="btn btn-success" onClick="save_provider_schedule()">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
		</div>
        <!--modal wrapper class end here -->
        
         <?php
        //appt hx detail
		$body_cont='<div class="form-group">
						<div id="div_app_hx_detail">Loading....
						</div>
					</div>';
		$footer_cont='';
		show_modal('div_app_hx', 'Appointment History', $body_cont, $footer_cont, 700, 'modal_90');
		?>
        
         <!-- ADD to TODO LIST div -->
    	<!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="todo_div" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                   		<button type="button" class="close" data-dismiss="modal">&times;</button>
                    	<h4 class="modal-title">
                            <span>Send to Re-Schedule </span>
                            [<span id="todo_date"></span>]
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="todo_content">
                         </div>
                    </div>
                    <div class="modal-footer" style="overflow:visible">
                    	<button type="button" class="btn btn-success" onClick="save_todo();">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
		</div>
        <!--modal wrapper class end here -->
        
        
        <!-- scheduler warning -->
		<div id="msgDiv_scheduler_overlay"></div>
         <!-- Modal -->
        <div id="msgDiv_scheduler" style="display: none">
            <div class="msg-modal-dialog">
            <!-- Modal content-->
                <div class="msg-modal-content">
                    <div class="msg-modal-header bg-primary">
                        <button type="button" class="close" onClick="hide_custom_modal();">&times;</button>
                        <h4 class="msg-modal-title" id="msgTitle"></h4>
                    </div>
                    <div class="msg-modal-body" id="msgBody">
                    	
                    </div>
                    <div class="msg-modal-footer" id="msgFooter">
                        <button type="button" class="btn btn-danger" onClick="hide_custom_modal();">Close</button>
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
                                <div id="print_options_caption" class="fl"> - Print Options</div>
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
        
		<!-- block / lock options div -->
        <!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="block_lock_div" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                    	<h4 class="modal-title">
                            <div class="row" id="block_lock_div-handle">
                            	<div class="col-sm-4 text-left">Block / Un-Block</div>
                            	<div class="col-sm-4 text-center" id="blk_lk_date"></div>
                            	<div class="col-sm-4 text-right">Lock / Un-Lock</div>
                            </div>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="blk_lk_content"></div>
                    </div>
                    <div class="modal-footer" style="overflow:visible" id="block_lock_div_footer"></div>
                </div>
            </div>
        </div>
		</div>
        <!--modal wrapper class end here -->
		
        <!-- label options div -->
	    <!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="label_opt_div" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                    	<button type="button" class="close" data-dismiss="modal">&times;</button>
                    	<h4 class="modal-title">
                            <div id="label_opt_div-handle" class="text-left">
                            Add / Edit Label(s)
                            </div>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="label_opt_content"></div>
                    </div>
                    <div class="modal-footer" style="overflow:visible" id="label_opt_footer"></div>
                </div>
            </div>
        </div>
		</div>
        <!--modal wrapper class end here -->
        
		<!-- Set Appointments Via Double click on the empty slot. -->
        <!--modal wrapper class is being used to control modal design-->
        <div class="common_modal_wrapper">
         <!-- Modal -->
        <div id="set_appt_div_slot_dc" class="modal fade" role="dialog">
            <div class="modal-dialog">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"> 
                            <div class="row">
                                <div class="col-sm-6" id="set_appt_div_slot_dc-handle">Set Appointment on <span id="sadc_appt_tm_view"></span></div>
                                <div class="col-sm-6" id="load_set_appt_data_dc"></div>
                            </div>
                        </h4>
                    </div>
                    <div class="modal-body" id="load_set_appt_data_dc_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Patient</label>
                                    <div class="row">
                                        <div class="col-sm-5"><input type="text" id="sadc_txt_patient_name" name="sadc_txt_patient_name" onKeyPress="{if (event.keyCode==13)return searchPatient();}" value="" class="form-control"/></div>
                                        <div class="col-sm-5"><select name="sadc_txt_findBy" id="sadc_txt_findBy" onChange="searchPatient2(this)" onkeypress="{if (event.keyCode==13)return searchPatient();}" class="form-control minimal selecicon">
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                                <option value="Deceased">Deceased</option> 
                                                <option value="Resp.LN">Resp.LN</option> 
                                                <option value="Ins.Policy">Ins.Policy</option>    
                                            </select></div>
                                        <div class="col-sm-2"><button type="button" value="save" class="btn btn-success" name="save_butt" onClick="searchPatient()" onKeyPress="{if (event.keyCode==13)return searchPatient();}" >Search</button></div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 5px;">
							<div class="col-sm-4 set_appt_pri">
								<div class="col-sm-12">
									<label>Primary</label>
									<select name="sadc_sel_proc_id" id="sadc_sel_proc_id" onChange="show_test(this.value,'<?php echo $_REQUEST["sch_id"];?>');"  onmouseover="javascript:show_proc_fullname(this.value);" class="form-control minimal selecicon" title="">
										<option value="">-Reason-</option>
										<?php
											list($list_of_procs, $arr_proc_names, $user_proc) = $obj_scheduler->load_procedures();
											echo $list_of_procs;
										?>
									</select>
								</div>
								<div class="col-sm-12">
									<label>Site</label>
									<select name="sadc_site_pri" id="sadc_site_pri" class="form-control minimal selecicon">
										<option value=""></option>
										<?php 
										$options=$obj_scheduler->eye_site();
										foreach($options as $k=>$v){
											echo "<option value=\"$k\">$v</option>";
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-sm-4 set_appt_sec">
								<div class="col-sm-12">
									<label>Secondary</label>
									<select name="sadc_sec_sel_proc_id" id="sadc_sec_sel_proc_id" onChange="show_test(this.value,'<?php echo $_REQUEST["sch_id"];?>');" class="form-control minimal selecicon" onmouseover="javascript:show_proc_fullname(this.value);" title="">
										<option value="">-Reason-</option>
										<?php
											echo $list_of_procs;
										?>
									</select>
								</div>
								<div class="col-sm-12">
									<label>Site</label>
									<select name="sadc_site_sec" id="sadc_site_sec" class="form-control minimal selecicon">
										<option value=""></option>
										<?php 
										$options=$obj_scheduler->eye_site();
										foreach($options as $k=>$v){
											echo "<option value=\"$k\">$v</option>";
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-sm-4 set_appt_ter">
								<div class="col-sm-12">
									<label>Tertiary</label>
									<select name="sadc_ter_sel_proc_id" id="sadc_ter_sel_proc_id" onChange="show_test(this.value,'<?php echo $_REQUEST["sch_id"];?>');" class="form-control minimal selecicon" onmouseover="javascript:show_proc_fullname(this.value);" title="">
										<option value="">-Reason-</option>
										<?php
											echo $list_of_procs;
										?>
									</select>
								</div>
								<div class="col-sm-12">
									<label>Site</label>
									<select name="sadc_site_ter" id="sadc_site_ter" class="form-control minimal selecicon">
										<option value=""></option>
										<?php 
										$options=$obj_scheduler->eye_site();
										foreach($options as $k=>$v){
											echo "<option value=\"$k\">$v</option>";
										}
										?>
									</select>
								</div>
							</div>
                  		</div>
                    </div>
                    <div class="modal-footer" id="msgFooter">
                    	<button type="button" class="btn btn-success" onClick="javascript:add_appt_bydcon_slot();" value="Add Appt">Add Appt</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!--modal wrapper class end here -->
        
        <!-- ---------------- old html starts here------------------ -->
        		
		<!-- for showing highlighted prov schedules -->
		<div id="show_highlighted_prov_sch" style="border:1px solid #000000;background-color:#ffffff;display:none;text-align:left;padding:5px;"></div>

		<!-- Requreid for Day Summary -->
		<input type="hidden" id="hid_prov_count" name="hid_prov_count" value="">

		<!-- drag add / resch appt -->
		<div class="tool_tip" id="appt_drag" style="display:none; cursor:move; font-weight:bold;"></div>
		
		

		<!-- Div to Show / Hide Content -->
		<div id="global_msg_stack" style="display:none;z-index:<?php echo $div_front_layer_zindex;?>;position:absolute;left:25px;top:100px;"></div>
		
		<div id="div_add_prov_button" style="display:none;z-index:<?php echo $div_front_layer_zindex;?>;">
			<input type="button" name="add_prov_sch" value="Add Provider Schedule" onClick="javascript:open_add_schedule_option();" onMouseOut="javascript:display_block_none('div_add_prov_button', 'none');" class="nav_highlight_button" />
		</div>
		
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="<?php echo $GLOBALS['webroot']?>/library/js/html5shiv.min.js"></script>
          <script src="<?php echo $GLOBALS['webroot']?>/library/js/respond.min.js"></script>
        <![endif]-->
        <!--cache file for js files-->
     	<script type="text/javascript" src="js_scheduler.php?version=<?php echo fileatime('../../library/js/sc_script.js');?>"></script>
        <?php if (constant('HASH_METHOD') == "MD5"){?>
		<script language="javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/md5.js"></script>
        <?php }else{?>
        <script language="javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/js_crypto_sha256.js"></script>
        <?php }?>
        
		<script type="text/javascript">
			/*
			Purpose: on load actions
			*/
			var patientDeceased = <?php echo json_encode($patientDeceased); ?>;
			var pd_alert = "Appointment(s) not allowed for deceased patients.";
			$(document).ready( function() {
			
				//for front desk buttons
				$('[data-toggle="tooltip"]').tooltip(); 
				
				$('body').on('click','#main_search_dd li a:lt(11)',function(){
					var fv = $(this).text();
					if(typeof(fv)!='undefined' && fv!='Advance') 
					{
						$('#findBy').val(fv);
						$('#findByShow').val(fv);
					}
					
				});
				
				$('body').on('hide.bs.modal','#editable_area', function () {
					var obj = $("#elem_patientStatus");
					var len = obj.length;
					var prev_status = obj.data('prev-val');
					var pt_status = obj.val();

					if( pt_status != prev_status ) {
						obj.val(prev_status);
						obj.trigger('change');
					}
				});

				$('body').on('click','#main_search_dd li a:gt(11)',function(){
					var fv = $(this).text();
					var pt_id = $(this).attr('pt_id');
					if(typeof(pt_id)=='undefined') 
					{
						$('#findBy').val(fv).attr('title',fv);
					}
					else{
							$("#hd_patient_id").val(pt_id);
							$("#txt_patient_app_name").val(fv);
							//$('#findBy').val('');
							$('#findBy').val(fv).attr('title',fv);
							pre_load_front_desk(pt_id);
					}
					$('.dropdown-submenu > .dropdown-menu').css('display','none');
				});
				
				top.fmain.show_iportal_changes_alert_data('registered_patients');
			
				//PATIENT PORTAL CL ORDERS
				top.fmain.show_iportal_changes_alert_data('cl_order');
				
                //PATIENT PORTAL PAYMENTS
				top.fmain.show_iportal_changes_alert_data('iportal_payments');
                
				//loading scheduler sub pages
				fresh_load('<?php print $db_load_date; ?>', '<?php print $pat_id; ?>', '<?php echo $arr_sess_facs;?>', '<?php echo $arr_sess_prov;?>');
				
				$('#facilities').on('hide.bs.select', function() {
				  fac_change_load('day'); // or $(this).val()
				});
				
				$('#sel_pro_month').on('hide.bs.select', function() {
				  pro_change_load('day'); // or $(this).val()
				});
				
				collect_labels_by_provider();			
				
				$("#next_available_slot_div").modal('hide');
				$("#run_label").click(function(){
					var rad=$(".sch_timing_radio:checked").val();
					var sel_label=selectedValuesStr("sel_all_labels");
					var chain_event=$("#chain_event").val();
					var sel_phy=selectedValuesStr("provider_label");
					msg="";
					if(!chain_event && !sel_label)
					{
						sel_label="Slot without labels~~NA";		
					}
					
					if(sel_label=="" && !chain_event){
						msg+="&nbsp;&bull;&nbsp;Please select the label";
					}
					if(sel_phy==""){
						if(msg!=""){msg+="<br>";}
						msg+="&nbsp;&bull;&nbsp;Please select the physician";	
					}if(msg){
						top.fAlert(msg);return false;
					}
					get_avaiable_slot(sel_label,'',rad);
					
				});

				$('body').on('change blur','input[id^="frontAddressStreet"]',function(event){ 
					var v = $(this).val();
					var c = capitalize_letter(v);
					$(this).val(c);
				});

				
			//====================================================================================//	
			//=======================Schedule Template Drop Down Work=============================//		
				var str_template='<?php echo addslashes($obj_scheduler->load_sch_templates("OPTIONS"));?>';
				$('#show_emergency_templates').click(function(){
					var str_template_opt_first='<option value="new">New Template</option>';
					if($(this).prop("checked")==true){
						$('#anps_sel_tmp').html(str_template_opt_first+str_template);
					}else if($(this).prop("checked")==false){
						var opt_without_emer=$('#sel_tmp_options').html();
						$('#anps_sel_tmp').html(str_template_opt_first+opt_without_emer);
					}
					
				});
			//====================================================================================//	
			});
			
			function core_pat_check(val,obj){
				if(val == ''){
					obj.value = '';
				}
			}
		</script> 
        <!-- CODE TO SHOW THE INSURANCE ALERT INTO FRONT DESK ONLOAD --->
		<?php
        $ins_alert = "";
		if(trim($_SESSION['patient'])){
        $qry = imw_query("select insurance_companies.name,insurance_companies.attn from 
                insurance_data join insurance_companies
                on insurance_companies.id = insurance_data.provider
                where 
                insurance_data.pid = '".$_SESSION['patient']."'
                and insurance_data.provider > 0
                and insurance_data.actInsComp ='1' 
                and (insurance_companies.frontdesk_desc='1' or insurance_companies.billing_desc='1')
                order by insurance_data.actInsComp desc");
        if(imw_num_rows($qry)>0){
            while($row_ins=imw_fetch_assoc($qry)){
                $ins_alert .= '&bull; ' . $row_ins['name'].' - '.$row_ins['attn'].'<br />';
            }
        }
		}
        ?>
        <script type="text/javascript">
            var ins_alert = "<?php echo (isset($ins_alert))?$ins_alert:''; ?>";
            if(ins_alert!=''){
                top.fAlert(ins_alert,"Insurance Description");
            }
        </script>
        <!--End of Front Desk Insurance Alert Work -->
	</body>
</html>