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
require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
/*files to show urgent message popup */
require_once(dirname(__FILE__).'/../../library/classes/msgConsole.php');
require_once(dirname(__FILE__).'/../../library/classes/landing_page.php');
$landPhyObj = new landing_physician;
/*files to show urgent message popup ends here */

if(!defined('DOC_POPUP')) define('DOC_POPUP',true);
$app_base			= new app_base();
$user_info_console	= $app_base->get_user_info_console();
$this_provider_tabs	= $app_base->get_provider_tabs();
$main_tab_names		= $app_base->language->get_vocabulary("core_module", "main_tabs");
$reason_code_options = $app_base->get_rp_reason_codes();
$DEFAULT_TAB 		= '';
$LOAD_THIS_TAB 		= '';
foreach($this_provider_tabs as $tabName=>$statusDefaultARR){
	if(is_array($statusDefaultARR) && strtolower($statusDefaultARR['default'])=='yes'){
		$DEFAULT_TAB = $tabName;
	}else if(!is_array($statusDefaultARR) && strtolower($tabName)=='load_this_tab' && !empty($statusDefaultARR)){
		$LOAD_THIS_TAB 	= $statusDefaultARR;
	}
}
if(empty($LOAD_THIS_TAB)) $LOAD_THIS_TAB = $DEFAULT_TAB;

$_SESSION['task_alert_shown'] = false;
$show_task_alerts = false;
if( !isset($app_base->session['task_alert_shown'])) {
	$taskAlertData = task_alerts();
	$show_task_alerts = (is_array($taskAlertData) && count($taskAlertData) > 0 ) ? true :  false;
}

$admin_docs_tab_arr=array("priv_admn_docs_Collection","priv_admn_docs_Consent","priv_admn_docs_Consult","priv_admn_docs_Education","priv_admn_docs_Instructions",
						"priv_admn_docs_Logos","priv_admn_docs_Op_Notes","priv_admn_docs_Package","priv_admn_docs_Panels","priv_admn_docs_Prescriptions",
						"priv_admn_docs_Pt_Docs","priv_admn_docs_Recalls","priv_admn_docs_Scan_Upload_Folders","priv_set_margin","priv_admn_docs_Smart_Tags",
						"priv_admn_docs_Statements");
$admin_docs_tab_priv=false;
foreach($admin_docs_tab_arr as $admn_tab) {
    if(core_check_privilege(array($admn_tab)) == true){
        $admin_docs_tab_priv=true;
    }
}

$pos_table_exists_sql="SELECT table_name
    FROM information_schema.TABLES
    WHERE (TABLE_SCHEMA = '".constant('IMEDIC_IDOC')."')
    AND TABLE_NAME IN ('tsys_merchant','tsys_device_details')";
$pos_table_exists_rs=imw_query($pos_table_exists_sql);

$login_facility=$_SESSION['login_facility'];
$pos_device=false;
if($pos_table_exists_rs && imw_num_rows($pos_table_exists_rs)>0){
    $devices_sql="Select tsys_device_details.id from tsys_device_details
                  JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id
                  WHERE device_status=0
                  AND tsys_device_details.facility_id='".$login_facility."'
                  AND merchant_status=0
                  ";
    $resp = imw_query($devices_sql);
    if($resp && imw_num_rows($resp)>0){
        $pos_device=true;
    }
}

$facArr = get_facility_details();
$enable_hp = $facArr['enable_hp'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>imwemr</title>
    <link rel="icon" type="image/png" href="<?php echo $GLOBALS['webroot']; ?>/favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="<?php echo $GLOBALS['webroot']; ?>/favicon-32x32.png" sizes="32x32">
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css?<?php echo filemtime('../../library/css/common.css');?>" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css?<?php echo filemtime('../../library/messi/messi.css');?>" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
    <?php if($app_base->default_product == "imwemr") { ?>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
    <?php } ?>
    <style type="text/css">#div_user_settings{
		top:5%;
		right:70%;
		outline: none;
	}
	body {
		overflow:hidden !important;
	}
	#div_pt_name figure img{max-height:40px;max-width:45px;}
	#chart_status, .elchart{display:none;}
	#chart_status{ font-size:18px; margin-bottom:0px;padding:4px;vertical-align: middle;} #btn_ncf{font-weight:bold; vertical-align: middle;}
	#divMultiRefPhy .modal-dialog{margin:0;}
	#vis_ptphy .patient{margin:0px 0 0 0;padding:0px 5px;}
	#vis_ptphy{color:#fff; padding-right:10px; }

	#infoIns{white-space: nowrap;}
	#infoIns label{margin-bottom:0;}

	#vis_ptphy .patient[id*=btnref]{padding:0px 2px 0px 2px;margin:0px;width:100%;display:block;}

	#vis_ptphy .tooltip-inner{ text-align: left; max-width:500px; padding:5px 10px; }
	#el_chartp3_ig ul li, #el_charttemp_ig ul li, #lichartp3dd  ul li ul li,  #lichart_status ul li {display:block; float:left;width:100%;}
	ul.ul_pt_forms{
		display: block;
		list-style-type: none;
		-webkit-margin-before: 0em;
		-webkit-margin-after: 0em;
		-webkit-margin-start: 0px;
		-webkit-margin-end: 0px;
		-webkit-padding-start: 0px;
	}
	.ftvertxt, #tick2 { font-family:'robotobold'; font-weight:600;font-size:14px; color:#5c2a79;}
	ul.ul_pt_forms li{display: block;}
	.elchart .input-group-btn .btn{padding:1px 2px!important;}

	#lichartp3dd a { color: #333; text-decoration: none; }
	.elchart .dropdown-header{ padding:5px; font-weight:bold; }
	.userdos{text-align:center;}
	.userdos span{padding:0px 2px 0px 2px;}
	#chartdos{font-weight:bold; font-size:17px!important;width:98px;height:20px!important;background-color:transparent;color:white;border-color:transparent;padding:1px!important;}
	#dd_wv_tab ul li{ white-space: nowrap; }
	#dd_wv_tab{width:300px;}
	.od{color:#0000ff;}
	.os{color:#008000;}
	.ou{color:#a200cc;}
	table.borderless{margin-bottom:0px;}
	table.borderless>tbody>tr>td{ border-top: 0;padding:0px 3px 0px 0px!important;}
	table.borderless .btn-link{padding:1px;}
	#chart_phy_note, #ico_tp_sug{position:absolute;top:0px;right:0px;}
	#chart_phy_note span{color:yellow;}
	#ico_tp_sug{left:0px;right:auto;color:red;}
	.ui-datepicker,.ui-autocomplete{z-index:99999!important;}
	.to_show_more{cursor:pointer; float:right;}

	#patAccStatus { position:absolute;bottom:1px; right:1px;}
    #fake_iframe{z-index:-1;top:0px;background:transparent;position:absolute;width:100%;height:100%;border:transparent;}
	#icoPtLock{ position: absolute; margin:0px 15px; color:yellow;  padding:2px; cursor:pointer; /*border:1px solid black; background-color:yellow;*/}
	#lichart_status .dropdown-menu, #licharttmpdd .dropdown-menu{overflow:auto;width:auto;min-width:50px; max-height:450px;}
	#li_rc{ color:white; }
	#btnPtForms.highlight{ background-color: #ffa500; }
	.doc_exists a{ background-color: #ffa500 !important; }
	.doc_exists a:hover{ background-color: #e7e7e7 !important; }
	#div_loading_image{z-index:99999!important; }
	#main_toolbar .hght_lm{ height:38px; }

	#btnPtForms .sm-vw{ display:inline-block; }
	#btnPtForms .lg-vw{ display:none; }
	.tlastara .flagopt .glyphicon { font-size:10px !important; }

	#vis_ptphy span.refphy
	{
	    display:inline-block;
	    max-width:40px;
	    white-space: nowrap;
	    overflow: hidden;
	    text-overflow: ellipsis;
	}
	.stflwicon ul li { margin: 0px; }
	@media (min-width: 1900px) {
		.tlastara .flagopt .glyphicon { font-size:20px !important; }
		#btnPtForms .lg-vw{ display:inline-block; }
		#btnPtForms .sm-vw{ display:none; }
		#vis_ptphy span.refphy{max-width: 90px;}
	}

	</style>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/html5shiv.min.js"></script>
      <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
	window.onunload=function(){top.close_popwin();top.master_ajax_tunnel('ajax_handler.php?task=set_pt_monitor_pt_close');}
	var urgent_msg_alerts	= "";
	var logged_user_type 	= "<?php echo $app_base->logged_user_type;?>";
	var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
	var HASH_METHOD				= "<?php echo $app_base->hash_method;?>";
	var priv_vo_clinical 	= "<?php echo $app_base->session['sess_privileges']['priv_vo_clinical'];?>";
	var priv_vo_pt_info 	= "<?php echo $app_base->session['sess_privileges']['priv_vo_pt_info'];?>";
	var priv_vo_acc 			= "<?php echo $app_base->session['sess_privileges']['priv_vo_acc'];?>";
	var phone_format			= "<?php echo $GLOBALS['phone_format'];?>";
	var phone_min_length	= "<?php echo inter_phone_length(); ?>" ;
	var ssn_format				= "<?php echo inter_ssn_format(); ?>" ;
	var ssn_reg_exp_js		= "<?php echo inter_ssn_reg_exp_js(); ?>" ;
	var ssn_length				= "<?php echo inter_ssn_length(); ?>" ;
	var zip_length				=	"<?php echo inter_zip_length(); ?>" ;
	var zip_ext						=	"<?php echo inter_zip_ext(); ?>" ;
	var state_label				=	"<?php echo inter_state_label(); ?>" ;
	var state_val					=	"<?php echo inter_state_val(); ?>" ;
	var state_length			=	"<?php echo inter_state_length(); ?>";
	var int_country				=	"<?php echo inter_country(); ?>" ;
	var ser_root 					= "<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/xml/refphy/" ?>";
	var REF_PHY_FORMAT		= "<?php echo $GLOBALS['REF_PHY_FORMAT']; ?>";
	var inter_date_format	= "<?php echo inter_date_format(); ?>";
	var claim_status_request_js='<?php echo constant('CLAIM_STATUS_REQUEST'); ?>';
	var practice_dir	 		=	"<?php echo constant('PRACTICE_PATH'); ?>";
	var clinical_prev			=	"<?php echo (core_check_privilege(array("priv_vo_clinical")) ? 'true' : 'false'); ?>";
	var providerId = "<?php echo $_SESSION['authId']; ?>";
	var doc_pop = "<?php echo constant('DOC_POPUP');?>";
	var zipLabel = "<?php getZipPostalLabel(); ?>";
	var hashOrNo = "<?php getHashOrNo(); ?>";
	var global_date_format = "<?php echo phpDateFormat(); ?>";
	var jQueryIntDateFormat = "<?php echo jQueryIntDateFormat(); ?>";
    var addon_su_field = <?php echo (isset($GLOBALS['ADDON_SU_FIELD']) && $GLOBALS['ADDON_SU_FIELD'] === true) ? 1 : 0; ?>;
    var currentTimeZone = "<?php echo date_default_timezone_get(); ?>";
	/*--AUTO SESSION TIMEOUT SCRIPT START--*/
	var auto_sess_timeout = <?php echo $GLOBALS["session_timeout"];?>;
	if(auto_sess_timeout){
		var auto_sess_timeout_warning_time = auto_sess_timeout > 60 ? auto_sess_timeout - 60 : auto_sess_timeout - 30;
		var auto_sess_timeout_warning_time = auto_sess_timeout_warning_time*1000; //in mili-seconds for setInterval
		//alert('timeout='+auto_sess_timeout+', Warning on='+auto_sess_timeout_warning_time);
		try{clearInterval(auto_sess_timer);}catch(e){console.log(e.message);}
		var auto_sess_timer = setTimeout(warn_sess_timeout, auto_sess_timeout_warning_time);
		var warn_sess_timeout_do = 1;
		function warn_sess_timeout(){
			$.ajax({
				url:top.JS_WEB_ROOT_PATH+'/interface/core/sess_timeout_runtime.php?do='+warn_sess_timeout_do+'&timeout_in='+auto_sess_timeout+'&recall_in='+auto_sess_timeout_warning_time,
				type:"json",
				success:function(resp){
					console.log(resp);
					r = JSON.parse(resp);
					if(typeof(r)=="object"){
						rt	= r.respType;
						switch(rt){
							case 'if':{
								showSeconds = r.showSeconds;
								dialogBox("Session TimeOut Warning","<span style='font-size:14px;'><b>You will be logged out in <span id='logout_seconds' style='color:#f00'>"+showSeconds+"</span> Seconds.</b></span>","Keep Session Active","Logout Now","window.top.do_sess_alive();","window.top.logOut();",false,true,false,false,500,"",true,"","",false);
								log_sec_timer = setInterval(fun_sec_timer,1000);
								break;
							}
							case 'else':{
								auto_sess_timeout				= r.timeoutInVal;
								auto_sess_timeout_warning_time	= r.recallIn;
								//auto_sess_timeout_warning_time	= r.recallFun;
								//warn_sess_timeout_do 			= 2;
								try{clearInterval(auto_sess_timer);}catch(e){console.log(e.message);}
								var auto_sess_timer = setTimeout(warn_sess_timeout, r.recallFun);
								break;
							}
						}
					}
				}
			});
		}
	}//end of if(sess_timeout)

	function do_sess_alive(){//to keep session alive till next cycle.
		clearInterval(log_sec_timer);
		$.ajax({
				url:top.JS_WEB_ROOT_PATH+'/interface/core/sess_timeout_runtime.php?do=2',
				success:function(resp){
					try{clearInterval(auto_sess_timer);}catch(e){console.log(e.message);}
					auto_sess_timer = setInterval(warn_sess_timeout,auto_sess_timeout_warning_time);
				}
		});
		closeDialog();
	}

	var log_sec_timer;
	function fun_sec_timer(){
		try{clearInterval(auto_sess_timer);}catch(e){console.log(e.message);}
		try{
			sec_start = parseInt(dgi("logout_seconds").innerHTML);
			var sec_time = sec_start - 1;
			dgi("logout_seconds").innerHTML = sec_time;
		}catch(e){console.log(e.message);}
		if(sec_time<1){clearInterval(log_sec_timer); window.top.logOut();}
	}
	/*--AUTO SESSION TIMEOUT SCRIPT END--*/
	</script>
</head>
<body>
<!-- Alert & Notification Div -->
<div id="div_alert_notifications"><span class="notification_span"></span></div>
<div id="divCommonAlertMsgNew" class="panel panel-success" style="display:none; position:absolute;z-Index:802; background-color:#FFFFFF;top:20%;left:40%;"></div>


<input type="hidden" name="curr_main_tab" id="curr_main_tab" value="<?php echo $DEFAULT_TAB;?>">
<input type="hidden" name="curr_sub_tab" id="curr_sub_tab" value="">
<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $app_base->session['patient'];?>">
<input type="hidden" name="ajax_tunnel_status" id="ajax_tunnel_status" value="free">
<input type="hidden" name="appt_scheduler_status" id="appt_scheduler_status" value="">
<input type="hidden" name="hidChkChangeInsTabDb" id="hidChkChangeInsTabDb" value="no">
<input type="hidden" name="hidChkChangeDemoTabDb" id="hidChkChangeDemoTabDb" value="no">
<input type="hidden" name="hidChkDemoTabDbStatus" id="hidChkDemoTabDbStatus" value="">
<input type="hidden" name="hidChkInsTabDbStatus" id="hidChkInsTabDbStatus" value="">
<input type="hidden" name="hidInsCaseBtCaption" id="hidInsCaseBtCaption" value="Update Case">
<input type="hidden" name="hidChkConfirmSave" id="hidChkConfirmSave" value="no">
<!-- Moved from Medical History Page to Top -->
<input type="hidden" name="medical_tab_change" id="medical_tab_change" value=""/>
<input type="hidden" name="hid_chk_change_data_main" id="hid_chk_change_data_main" value="no">
<header>
    <div class="container-fluid topheader">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-8 usrlogin">
                <ul>
                    <li class="profimglo"><a href="#" style="margin-right:15px;">
                            <img id="provider_image" src="<?php echo $user_info_console['user_image'];?>" alt="" ondblclick="showSwitchUserForm();" onclick="showHidemodal('show','div_user_settings',false,false)" style="display:none;" /></a>
						<div class="phyday"><img src="<?php echo $GLOBALS['webroot']; ?>/library/images/user_calendar.png" title="Physician Day Scheduler" onClick="top.icon_popups('phy_day_sch');" onmouseover="top.show_phy_checkin_appts(1);" onmouseout="top.show_phy_checkin_appts(2);"  /></div>
					</li>
                    <li>
                    	<?php if($app_base->default_product == "imwemr"){?>
                            <a href="#" onClick="physician_console();" class="text_10b_purple" title="<?php echo $user_info_console['console_title'];?>">
                                <small><?php echo $user_info_console['console_text'];?></small>
                            </a>
                    	<?php } else if($app_base->default_product == "imwemr"){?>
                        &nbsp;<b><?php echo $user_info_console['console_text'];?></b>
                        <?php }?>
                        <span id="rf_foll_name"><?php echo $user_info_console['res_fellow_info'];?></span>
                    </li>
					<li onClick="top.icon_popups('imon_settings');" class="profimglo"><a href="#" class="top_icon imontico" title="iMedicMonitor Settings"></a></li>
					<li id="sch_icon_li" onClick="change_main_Selection(this);" class="profimglo"><a href="#" class="top_icon scheduler_icon" title="Scheduler"></a></li>
                </ul>
            </div>
            <div class="col-lg-7 col-md-9 col-sm-9 col-xs-4 sitenav">
            	<nav class="navbar navbar-default">
                    <div class="navbar-header">
                    	<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".js-navbar-collapse">	<span class="sr-only">Toggle navigation</span>
                    	<span class="icon-bar"></span>
                    	<span class="icon-bar"></span>
                    	<span class="icon-bar"></span>
                   		</button>
                    </div>

                	<!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse js-navbar-collapse">
                        <ul class="nav navbar-nav">
                           <!-- Tab - Work View-->
                          <?php if( $this_provider_tabs['Work_View']['status'] == 'on' || in_array( 'WORKVIEW',$GLOBALS['IMW_TAB_SETTINGS']) ) { ?>
                            <li class="dropdown mega-dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" id="Work_View" onClick="change_main_Selection(this);">WorkView</a>
                            	<ul id="dd_wv_tab" class="dropdown-menu mega-dropdown-menu row" style="z-index:99999;">
                                	<li class="dropdown-header">WORKVIEW</li>
                                    <li class="col-sm-6">
                                        <ul>
                                            <li><a href="#" id="Work_View" onClick="change_main_Selection(this);" class="">Chart Note</a></li>
                                            <!--<li><a href="#" id="Work_View" onClick="return false; change_main_Selection(this,'consult');" >Consult Letter</a></li>-->
                                            <li><a href="#" id="Work_View" onClick="change_main_Selection(this,'contact_lens');" >Contact Lens</a></li>
											<!--<li><a href="#" id="Work_View" onClick="return false;change_main_Selection(this,'prescription');" >Prescription</a></li>
											<li><a href="#" id="Work_View" onClick="return false;change_main_Selection(this,'upload_image');" >Upload Image</a></li>-->
											<li><a href="#" id="Work_View" onClick="change_main_Selection(this,'physician_notes');" >Physician Notes</a></li>
											<?php if(core_check_privilege(array("priv_edit_prescriptions")) == true){ ?>
											<li><a href="#" id="Work_View" onClick="change_main_Selection(this,'prescription');" >Prescription</a></li>
										<?php } ?>
                                        </ul>
                                    </li>
                                    <li class="col-sm-6">
                                        <ul>
                                            <li><a href="#" id="Work_View" onClick="change_main_Selection(this,'procedure');" >Procedures</a></li>
                                            <li><a href="#" id="Work_View" onClick="change_main_Selection(this,'sx_plan');" >Sx Planning Sheet</a></li>

											<!--<li><a href="#" id="Work_View" onClick="change_main_Selection(this,'operative_note');" >Operative Note</a></li>-->
											<li><a href="#" onclick="top.tb_popup(this);" title="Patient Instruction Documents" >Patient Instruction</a></li>
				                        </ul>
                                    </li>
                                    <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                </ul>
                            </li>
                        	<?php } ?>

													<!-- Tab -  Tests -->
													<?php if( $this_provider_tabs['Tests']['status'] == 'on' ) { ?>
							<li class="dropdown mega-dropdown">
                            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" id="Tests" onClick="change_main_Selection(this);">Tests</a>
                            	<ul class="dropdown-menu mega-dropdown-menu row text-nowrap" style="width:500px !important; z-index: 99999;">
                                	<li class="dropdown-header">TESTS</li>
                                    <?php $app_base->tests_tab_submenu();?>
                                    <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                </ul>
                            </li>

                        	<?php } ?>

													<!-- Tab - Medical Hx -->
													<?php if( $this_provider_tabs['Medical_Hx']['status'] == 'on' || in_array( 'MEDICAL_HX',$GLOBALS['IMW_TAB_SETTINGS']) ) { ?>
							<li class="dropdown mega-dropdown">
                            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" id="Medical_Hx" onClick="change_main_Selection(this,'ocular');">Medical Hx</a>
                            	<ul class="dropdown-menu mega-dropdown-menu row text-nowrap" style="width:380px; z-index: 99999;">
                                	<li class="dropdown-header">MEDICAL HISTORY</li>
                                    <li class="col-sm-4">
                                        <ul>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'ocular');" data-elem="medhx_ocu" class="medhx_ocu"><a href="#">Ocular</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'general_health');" data-elem="medhx_gen" class="medhx_gen"><a href="#">General Health</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'medication');" data-elem="medhx_med" class="medhx_med"><a href="#">Medications</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'sx_procedures');" data-elem="medhx_proc" class="medhx_proc"><a href="#">Sx/Procedures</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'allergies');" data-elem="medhx_allergies" class="medhx_allergies"><a href="#">Allergies</a></li>
                                        </ul>
                                    </li>
                                    <li class="col-sm-4">
                                        <ul>
											<li id="Medical_Hx" onClick="change_main_Selection(this,'immunizations');" data-elem="medhx_imm" class="medhx_imm"><a href="#">Immunizations</a></li>
											<li id="Medical_Hx" onClick="change_main_Selection(this,'cc_history');"><a href="#">CC &amp; Hx</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'vs');"><a href="#">Vital Signs</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'order_sets');"><a href="#">Order Sets</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'problem_list');"><a href="#">Problem List</a></li>
                                    	</ul>
                                    </li>
                                    <li class="col-sm-4">
                                        <ul>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'hms');"><a href="#">PHMS</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'lab');"><a href="#">LAB</a></li>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'radiology');"><a href="#">RAD</a></li>
                                            <?php if( $enable_hp )	{ ?>
                                            <li id="Medical_Hx" onClick="change_main_Selection(this,'hp');"><a href="#">H&amp;P</a></li>
                                            <?php } ?>
                                    	</ul>
                                    </li>
                                    <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                </ul>
                            </li>

													<?php } ?>

													<!-- Tab - Patient Info -->
													<?php if( $this_provider_tabs['Patient_Info']['status'] == 'on' ) { ?>

							<li class="dropdown mega-dropdown">
                            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" id="Patient_Info" onClick="change_main_Selection(this);">Patient Info</a>
                            	<ul class="dropdown-menu mega-dropdown-menu row" style="width:380px!important; z-index: 99999;">
                                	<li class="dropdown-header">PATIENT INFORMATION</li>
                                    <li class="col-sm-6 ">
                                        <ul>
                                            <li><a href="#" id="Demographics" onClick="change_main_Selection(this);">Demographics</a></li>
                                            <li><a href="#" id="Insurance" onClick="change_main_Selection(this);">Insurance</a></li>

                                        </ul>
                                    </li>
                                    <li class="col-sm-6 ">
                                        <ul>
                                            <li><a href="#" id="PtElig" onClick="change_main_Selection(this)">Eligibility</a></li>
											<li><a href="#" id="AccountingPR" onClick="change_main_Selection(this);">Patient Recall</a></li>
                                        </ul>
                                    </li>
                                    <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                </ul>
                            </li>
                        	<?php } ?>

													<!-- Tab - Documents -->
                                    <?php if ($this_provider_tabs["Docs"]["status"] == 'on') { ?>
                            <li class="dropdown mega-dropdown document_exists">
                            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" id="Documents" onClick="change_main_Selection(this);">Docs</a>
                            	<input type="hidden" name="Documents_Lab" id="Documents_Lab">
                            </li>
                                    <?php } ?>
                        	<!-- Tab - Accounting -->
													<?php if( $this_provider_tabs['Accounting']['status'] == 'on' ) { ?>
                            <li class="dropdown mega-dropdown" style="z-index:999999;">
                            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" id="Accounting" onClick="change_main_Selection(this);">Accounting</a>
                                <ul class="dropdown-menu mega-dropdown-menu row" style="width:400px;z-index:99999;">
                                	<li class="col-sm-6">
                                        <ul>
                                        	<li class="dropdown-header">CHARGES</li>
                                            <li><a href="#" id="AccountingSB" onClick="change_main_Selection(this);">Unprocessed Superbills</a></li>
                                            <li><a href="#" id="AccountingRC" onClick="change_main_Selection(this);">Charges Ledger</a></li>
                                            <li><a href="#" id="AccountingEC" onClick="change_main_Selection(this);">Service Charges</a></li>
                                            <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                        </ul>
                                    </li>
                                    <li class="col-sm-6">
                                        <ul>
                                        	<li class="dropdown-header">PAYMENTS</li>
                                            <li><a href="#" id="AccountingRP" onClick="change_main_Selection(this);">Payments Ledger</a></li>
                                            <li><a href="#" id="AccountingEP" onClick="change_main_Selection(this);">Service Payments</a></li>
                                            <li><a href="#" id="AccountingPP" onClick="change_main_Selection(this);">CI/CO Prepayments</a></li>
                                            <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                        </ul>
                                    </li>
                                    <li class="col-sm-12 text-center">
                                    	 <ul><li class="divider"></li></ul>
                                        <div style="z-index:999999;">
                                    	<button class="btn btn-info" onClick="era_file_fun();">ERA</button>
                                        <button class="btn btn-info" onClick="claim_file_fun();">Claims</button>
                                        <button class="btn btn-info" onClick="statement_file_fun();">Statements</button>
                                        <button class="btn btn-info" onClick="popup_win('../../interface/accounting/accountingAPSearch.php','width=900px,height=450px,resizable=1,scrollbars=1');">A&P </button>
                                        <button class="btn btn-info" onClick="popup_win('../accounting/ac_notes.php','width=1200px,height=650px,resizable=1,scrollbars=1,left=200');">Notes</button>
                                        </div>
                                         <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                    </li>
                                </ul>
                            </li>
													<?php } ?>

                          <!-- Tab - Billing -->
                         	<?php if( $this_provider_tabs['Billing']['status'] == 'on' || core_check_privilege(array("priv_bi_day_chrg_rept")) == true) {
                                $bclass=" pull-right ";
                                if( $this_provider_tabs['Billing']['status'] != 'on' && core_check_privilege(array("priv_bi_day_chrg_rept")) == true) {
                                    $bclass=" ";
                                } ?>
                            <li class="dropdown mega-dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" data-hover="dropdown" id="Billing" <?php if($this_provider_tabs['Billing']['status'] == 'on') { ?> onClick="change_main_Selection(this);"<?php } ?>>Billing</a>
                                <ul class="dropdown-menu mega-dropdown-menu row" style="width:450px;z-index: 99999;">
                                	<li class="dropdown-header">
                                        <?php if(core_check_privilege(array("priv_bi_day_chrg_rept")) == true){ ?>
                                            <span class="<?php echo $bclass;?> text_purple pointer" onClick="day_charges();">
                                                DAY CHARGES
                                            </span>
                                        <?php } ?>
                                        <?php if($this_provider_tabs['Billing']['status'] == 'on') { ?>
                                            <span>BILLING</span>
                                        <?php } ?>
                                    </li>
                                    <?php if($this_provider_tabs['Billing']['status'] == 'on') { ?>
                                        <li class="col-sm-6 ">
                                            <ul>
                                                <li><a href="#" id="BillingP" onClick="change_main_Selection(this);">Paper</a></li>
                                                <li><a href="#" id="BillingE" onClick="change_main_Selection(this);">Electronic</a></li>
                                                <li><a href="#" id="BillingE" onClick="change_main_Selection(this,'CLHReports');">Clearing House Reports</a></li>
                                                <li><a href="#" id="BillingCP" onClick="change_main_Selection(this);">Capitation</a></li>
                                            </ul>
                                        </li>
                                        <li class="col-sm-6 ">
                                            <ul>
                                                <li><a href="#" id="BillingBP" onClick="change_main_Selection(this);">Batch Processing</a></li>
                                                <li><a href="#" id="BillingEU" onClick="change_main_Selection(this);">Upload ERA</a></li>
                                                <li><a href="#" id="BillingEPP" onClick="change_main_Selection(this);">ERA Post Payments</a></li>
                                                <li><a href="#" id="BillingEMP" onClick="change_main_Selection(this);">ERA Manual Payments</a></li>
                                                <li><a href="#" id="BillingCR" onClick="change_main_Selection(this);">ERA Claim rejections</a></li>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                </ul>
                            </li>
													<?php } ?>

                        	<!-- Tab - Optical -->
                       		<?php if( $this_provider_tabs['Optical']['status'] == 'on' ) { ?>
                            <li class="dropdown mega-dropdown">
                            	<a href="#" id="Optical" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">Optical</a>
                            	<ul class="dropdown-menu mega-dropdown-menu row" style="width:250px;z-index: 99999;">
                                	<li class="dropdown-header">OPTICAL</li>
                                    <li class="col-sm-12 ">
                                        <ul>
                                            <li><a href="#" id="Optical_Tab" onClick="change_main_Selection(this,'');">Contact Lens</a></li>
                                            <li><a href="#" id="Optical_Tab" onClick="change_main_Selection(this,'cl_order_list');">CL List</a></li>
                                            <li><a href="#" id="Optical_Tab" onClick="change_main_Selection(this,'todays_order_list')">Glasses Order List</a></li>
                                            <li><a href="#" id="Optical_Tab" onClick="change_main_Selection(this,'optical_order_form')">Glasses Order Form</a></li>
                                        </ul>
                                    </li>
                                    <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
                                </ul>
                            </li>
													<?php } ?>

													<!-- Tab - Reports -->
													<?php if( $this_provider_tabs['Reports']['status'] == 'on' ) { ?>
							<li class="dropdown mega-dropdown adminmne">
                            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" id="Reports_header" onClick="change_main_Selection(this);">Reports</a>
                            	<ul class="dropdown-menu mega-dropdown-menu row" style="width:800px;z-index: 99999;">
                                <div class="row adminmenu">
									<div class="col-sm-3">
										<ul class="nav nav-tabs  admleft radmleft" role="tablist">
											<?php if (core_check_privilege(array("priv_sc_scheduler")) == true) { ?>
											<li role="presentation" class=" "><a href="#rep_scheduler" id="SchedulerReport" aria-controls="other" role="tab" data-toggle="tab" onClick="change_main_Selection(this);" class="pointer">Scheduler</a></li>
                                            <?php } if (core_check_privilege(array("priv_report_practice_analytics")) == true) { ?>
											<li role="presentation"><a href="#analytic" id="AnalyticReport" aria-controls="other" role="tab" data-toggle="tab" onClick="change_main_Selection(this);" class="pointer">Practice Analytics</a></li>
                                            <?php } if (core_check_privilege(array("priv_report_financials")) == true) { ?>
											<li role="presentation"><a href="#financials" aria-controls="documents" role="tab" data-toggle="tab">Financials</a></li>
                                            <?php } if (core_check_privilege(array("priv_report_compliance")) == true) { ?>
											<li role="presentation"><a href="#compliance" aria-controls="orders" role="tab" data-toggle="tab">Compliance</a></li>
                                            <?php }
											if(core_check_privilege(array("priv_cl_ccd")) == true){
											?>
                                            	<li role="presentation"><a href="#ccd" aria-controls="orders" role="tab" data-toggle="tab">CCD</a></li>
											<?php
											}
											//if(core_check_privilege(array("priv_admin")) == true || core_check_privilege(array("priv_api_access")) == true || core_check_privilege(array("priv_report_api_access")) == true){
											if(core_check_privilege(array("priv_api_access")) == true || core_check_privilege(array("priv_report_api_access")) == true){
											?>
												<li role="presentation"><a href="#api" aria-controls="orders" role="tab" data-toggle="tab">API</a></li>
											<?php
											}
											if( is_allscripts('enabled') === true )
											{
											?>
												<li role="presentation"><a href="#allscripts" aria-controls="orders" role="tab" data-toggle="tab">Allscripts</a></li>

                                            <?php  } if (core_check_privilege(array("priv_report_State")) == true) { ?>
											<li role="presentation"><a href="#state" aria-controls="orders" role="tab" data-toggle="tab">State</a></li>
                                            <?php } if (core_check_privilege(array("priv_report_optical")) == true) { ?>
											<li role="presentation"><a href="#opticals" aria-controls="opticals" role="tab" data-toggle="tab">Optical</a></li>
                                            <?php } if (core_check_privilege(array("priv_sc_house_calls")) == true) { ?>
											<li role="presentation"><a href="#reminders " aria-controls="reminders" role="tab" data-toggle="tab">Reminders</a></li>
                                            <?php } if (core_check_privilege(array("priv_cl_clinical")) == true) { ?>
											<li role="presentation"><a href="#clinical_rpt " aria-controls="clinical_rpt" role="tab" data-toggle="tab">Clinical</a></li>
                                            <?php } if (core_check_privilege(array("priv_report_Rules")) == true) { ?>
											<li role="presentation"><a href="#rules_rpt " aria-controls="rules_rpt" role="tab" data-toggle="tab">Rules</a></li>
                                            <?php } if (core_check_privilege(array("priv_report_iPortal")) == true) { ?>
											<li role="presentation" style="text-transform:none;"><a href="#iportal_rpt " aria-controls="iportal_rpt" role="tab" data-toggle="tab">iPortal</a></li>
                                            <?php } ?>
									  </ul>
									</div>
									<div class="col-sm-9">
											<div class="tab-content adsubmenu ">
												<div role="tabpanel" class="tab-pane  in active" id="rep_scheduler">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_sc_scheduler")) == true) { ?>
														<?php
															$scheduler_reports = "";
															$scheduler_name = "";
															$scheduler_query = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` = 'scheduler' and `delete_status` = 0");
															if(imw_num_rows($scheduler_query) > 0){
																while($scheduler_row = imw_fetch_assoc($scheduler_query)){
																	$scheduler_id = $scheduler_row['id'];
																	$scheduler_name = $scheduler_row['template_name'];

                                                                        $report_priv_name = str_replace(array(" ", "/","-"), "_", $scheduler_name);
                                                                        $report_priv_name= strtolower($report_priv_name.$scheduler_id);
                                                                        if (core_check_privilege(array('priv_report_'.$report_priv_name)) == true || $scheduler_row['default_report']==0) {
																	$scheduler_reports .= '<li><a href="#" id="SchedulerReport" onClick="change_main_Selection(this,'.$scheduler_id.');">'.$scheduler_name.'</a></li>';
																}
															}
                                                                }
															echo $scheduler_reports;
														?>
                                                            <?php if (core_check_privilege(array("priv_report_Patient_Monitor")) == true) { ?>
														<li><a href="#" id="PtMonitor" onClick="change_main_Selection(this);">Patient Monitor</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Day_Face_Sheet")) == true) { ?>
														<li><a href="#" id="DayFaceSheet" onClick="change_main_Selection(this);">Day Face Sheet</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Appointment_Report")) == true) { ?>
														<li><a href="#" id="ReportAppt" onClick="change_main_Selection(this);">Appointment Report</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Appointment_information")) == true) { ?>
														<li><a href="#" id="ApptInfo" onClick="change_main_Selection(this);">Appointment information</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Patient_Document")) == true) { ?>
														<li><a href="#" id="Pt_Docs" onClick="change_main_Selection(this);">Patient Document</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Sx_Planning_Sheet")) == true) { ?>
														<li><a href="#" id="SxPlanSheet" onClick="change_main_Selection(this);">Sx Planning Sheet</a></li>
                                                            <?php } if (core_check_privilege(array("priv_sc_recall_fulfillment")) == true) { ?>
														<li><a href="#" id="RECALLFF" onClick="change_main_Selection(this);">Recall Fulfillment</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Consult_Letters")) == true) { ?>
														<li><a href="#" id="ConsultLetters" onClick="change_main_Selection(this);">Consult Letters</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Scheduler_Report")) == true) { ?>
														<li><a href="#" id="SchedulerReportDef" onClick="change_main_Selection(this);">Scheduler Report</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Patients_CSV_Export")) == true) { ?>
														<li><a href="#" id="patientCSV" onClick="change_main_Selection(this);">Patients CSV Export</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Surgery_Appointments")) == true) { ?>
														<li><a href="#" id="SurgeryAppt" onClick="change_main_Selection(this);">Surgery Appointments</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_RTA_Query")) == true) { ?>
														<li><a href="#" id="rtaQryRpt" onClick="change_main_Selection(this);">RTA Query</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Clinical_Productivity")) == true) { ?>
														<li><a href="#" id="clinicalProductivity" onClick="change_main_Selection(this);">Clinical Productivity</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Providers_Report")) == true) { ?>
														<li><a href="#" id="ProvidersReport" onClick="change_main_Selection(this);">Providers Report</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Procedures_Report")) == true) { ?>
														<li><a href="#" id="ProceduresReport" onClick="change_main_Selection(this);">Procedures Report</a></li>
                                                            <?php } if (strtolower($GLOBALS["LOCAL_SERVER"])=='boston' || @$temp_for_all_report_permission=='yes') { ?>
                                                        <li><a href="#" id="pk_interface" onClick="change_main_Selection(this);">PK Interface</a></li>
                                                        <li><a href="#" id="press_ganey" onClick="change_main_Selection(this);">Press Ganey</a></li>
                                                        	<?php } ?>
                                                        <li><a href="#" id="patient_referral" onClick="change_main_Selection(this);">Patient Referral</a></li>
														<li><a href="#" id="AppointmentSurvey" onClick="change_main_Selection(this);">Appointment Survey</a></li>
                                                        <li><a href="#" id="rpt_pateint_ins_info" onClick="change_main_Selection(this);">Patient Insurance Info</a></li>
														<?php if (strtolower($GLOBALS["LOCAL_SERVER"])=='lehigh' || @$temp_for_all_report_permission=='yes') { ?>
                                                        <li><a href="#" id="patient_visit_info" onClick="change_main_Selection(this);">Patient Visit Info</a></li>
                                                        <?php } ?>
														 <li><a href="#" id="patient_registry_percentage" onClick="change_main_Selection(this);">Patient Registry Fields</a></li>
														 <li><a href="#" id="label_count_report" onClick="change_main_Selection(this);">Label Count Report</a></li>
														 <li><a href="#" id="no_show_report" onClick="change_main_Selection(this);">Workable No Show Report</a></li>
														 <li><a href="#" id="time_utilization" onClick="change_main_Selection(this);">Time Utilization</a></li>
														<?php } ?>
														<li><a href="#" id="lbl_user_perms_rprt" onClick="change_main_Selection(this);">Users Permissions Report</a></li>
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="analytic">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_report_practice_analytics")) == true) { ?>
														<?php
															$analytic_report = $report_names = "";
															$analytics_query = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` = 'practice_analytic' and `delete_status` = 0");
															if(imw_num_rows($analytics_query) > 0){
																while($analytic_row = imw_fetch_assoc($analytics_query)){
																	$analytic_id = $analytic_row['id'];
																	$report_names = $analytic_row['template_name'];

                                                                        $report_priv_name = str_replace(array(" ", "/","-"), "_", $report_names);
                                                                        $report_priv_name= strtolower($report_priv_name.$analytic_id);
                                                                        if (core_check_privilege(array('priv_report_'.$report_priv_name)) == true || $analytic_row['default_report']==0) {
																	$analytic_report .= '<li><a href="#" id="AnalyticReport" onClick="change_main_Selection(this,'.$analytic_id.');">'.$report_names.'</a></li>';
																}
															}
                                                                }
															echo $analytic_report;
														?>
                                                        <?php } ?>
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="financials">
                                                    <?php if (core_check_privilege(array("priv_report_financials")) == true) { ?>
													<?php
																$report_title ="";
																$report_name = "";
																$subTitle = "";
																$arr_title=array();
																$arr_default_report=array();
																$financial_query = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` = 'financial' And `report_sub_type` in ('daily' , 'analytics', 'account_receivable', 'claims','pt_collections') and `delete_status` = 0");
																if(imw_num_rows($financial_query) > 0){
																	while($financial_row = imw_fetch_assoc($financial_query)){
																		$id2 = $financial_row['id'];

																		$report_type = trim($financial_row['report_sub_type']);
																		$arr_title[$report_type][$id2]=$financial_row['template_name'];
																		$arr_default_report[$id2]=$financial_row['default_report'];
																	}
																}
																$report_title ="";
																$report_name = "";
																$subTitle = "";

																$counter = 0;
																$rep_str = '<div class="col-sm-6"><ul>';
																foreach($arr_title as $title => $arr_temp_name){
																	if($counter > 1){
																		$rep_str .= '</ul></div><div class="col-sm-6"><ul>';
																	}

																	if($title == 'daily'){ $subTitle = 'Daily'; }
																	if($title == 'analytics'){ $subTitle = 'Analytic'; }
																	if($title == 'claims'){ $subTitle = 'Claims'; }
																	if($title == 'account_receivable'){ $subTitle = 'Account Receivable'; }
																	if($title == 'pt_collections'){ $subTitle = 'PT Collections'; }
																	$report_title = '<li><a href="#"><b>'.$subTitle.'</b></a></li><li class="divider" role="separator"></li>';
																	foreach($arr_temp_name as $id2 =>$report_name){

                                                                        $report_priv_name = str_replace(array(" ", "/","-"), "_", $report_name);
                                                                        $report_priv_name= strtolower($report_priv_name.$id2);
                                                                        if (core_check_privilege(array('priv_report_'.$report_priv_name)) == true || $arr_default_report[$id2]==0) {
                                                                            $report_title .= '<li><a href="#" id="FinancialsReport" onClick="change_main_Selection(this,'.$id2.');">'.$report_name.'</a></li>';
                                                                        }
																	}

																	if($title == 'analytics'){
																		if(strtolower($GLOBALS["LOCAL_SERVER"])=='bennett' || @$temp_for_all_report_permission=='yes'){
																			$report_title .= '<li><a href="#" id="ReportNumberOfARTouches" onClick="change_main_Selection(this);">Number of AR Touches</a></li>';
																			$report_title .= '<li><a href="#" id="ReportPointOfServiceCollections" onClick="change_main_Selection(this);">Point of Service Collections</a></li>';
																			$report_title .= '<li><a href="#" id="ReportChargeEntryLag" onClick="change_main_Selection(this);">Charge Entry Lag - Reject Ratio</a></li>';
																		}
																		$report_title .= '<li><a href="#" id="ReportFinancialData" onClick="change_main_Selection(this);">Financial Data Report</a></li>';
																		if (core_check_privilege(array('priv_Ins_Enc')) == true) {
																		$report_title .= '<li><a href="#" id="InstitutionalEncounters" onClick="change_main_Selection(this);">Institutional Encounters</a></li>';
																		}
																	}

																	$rep_str .= $report_title;
																	$counter++;
																}

																$rep_str .= '</ul>';
																$rep_str .= '<ul>';
                                                                    if (core_check_privilege(array("priv_prev_hcfa")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="PREVIOUSHCFA" onClick="change_main_Selection(this);">Previous HCFA</a></li>';
																		$rep_str .= '<li><a href="#" id="PREVIOUSUB" onClick="change_main_Selection(this);">Previous UB04</a></li>';
                                                                    } if (core_check_privilege(array("priv_report_eid_status")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="eidstatus" onClick="change_main_Selection(this);">EID Status</a></li>';
                                                                    } if (core_check_privilege(array("priv_report_EID_Payments")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="eidpayments" onClick="change_main_Selection(this);">EID Payments</a></li>';
                                                                    } if (core_check_privilege(array("priv_tfl_proof")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="TFLProof" onClick="change_main_Selection(this);">TFL Proof</a></li>';
                                                                    } if (core_check_privilege(array("priv_pt_status")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="PT_STATUS" onClick="change_main_Selection(this);">PT Status</a></li>';
                                                                    }
																$rep_str .= '</ul>';
                                                                $rep_str .= '<ul>';
                                                                    if (core_check_privilege(array("priv_new_statements")) == true || core_check_privilege(array("priv_prev_statements")) == true || core_check_privilege(array("priv_statements_pay")) == true) {
                                                                        $rep_str .= '<li><a href="#"><b>Statements</b></a></li><li class="divider" role="separator"></li>';
                                                                    } if (core_check_privilege(array("priv_new_statements")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="NEWSTATEMENT" onClick="change_main_Selection(this);">New Statement</a></li>';
                                                                    } if (core_check_privilege(array("priv_prev_statements")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="PREVIOUSSTATEMENT" onClick="change_main_Selection(this);">Previous Statement</a></li>';
                                                                    } if (core_check_privilege(array("priv_statements_pay")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="STATEMENTPAYMENT" onClick="change_main_Selection(this);">Statement Payments</a></li>';
                                                                    }
                                                                $rep_str .= '</ul>';
                                                                $rep_str .= '<ul>';
                                                                    if (core_check_privilege(array("priv_saved_scheduled")) == true || core_check_privilege(array("priv_executed_report")) == true) {
                                                                        $rep_str .= '<li><a href="#"><b>Scheduled Reports</b></a></li><li class="divider" role="separator"></li>';
                                                                    } if (core_check_privilege(array("priv_saved_scheduled")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="SavedSchedules" onClick="change_main_Selection(this);">Saved Schedules</a></li>';
                                                                    } if (core_check_privilege(array("priv_executed_report")) == true) {
                                                                        $rep_str .= '<li><a href="#" id="ExecutedReports" onClick="change_main_Selection(this);">Executed Reports</a></li>';
                                                                    }
                                                                $rep_str .= '</ul>';
																$rep_str .= '</div>';
																echo $rep_str;
															?>
                                                        <?php } ?>
												</div>
												<div role="tabpanel" class="tab-pane " id="compliance">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_report_compliance")) == true) { ?>
														<?php
															$compliance_report = "";
															$compliance_name = "";
															$compliance_query = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` ='compliance' and `delete_status` = 0");
															if(imw_num_rows($compliance_query) > 0){
																while($compliance_row = imw_fetch_assoc($compliance_query)){
																	$compliance_id = $compliance_row['id'];
																	$compliance_name = $compliance_row['template_name'];
                                                                       //if($compliance_name=='2020 MIPS PI') continue;
                                                                        $report_priv_name = str_replace(array(" ", "/","-"), "_", $compliance_name);
                                                                        $report_priv_name= strtolower($report_priv_name.$compliance_id);
                                                                        if (core_check_privilege(array('priv_report_'.$report_priv_name)) == true || $compliance_row['default_report']==0) {
                                                                            if($compliance_name=='Transition ACI 2018'){$compliance_name='Transitional ACI (Promoting Interoperability) 2018';}
                                                                            if($compliance_name=='ACI 2018'){$compliance_name='ACI (Promoting Interoperability) 2018';}
																			if($compliance_name=='ACI 2019'){$compliance_name='2019 MIPS PI';}
																	$compliance_report .= '<li><a href="#" id="ComplianceReport" onClick="change_main_Selection(this,'.$compliance_id.');">'.$compliance_name.'</a></li>';
																}
															}
                                                                }
															echo $compliance_report;
														?>
                                                            <?php if (core_check_privilege(array("priv_report_QRDA")) == true) { ?>
                                                        <li><a href="#" id="QRDA_2020" onClick="change_main_Selection(this);">2020 QRDA Export</a></li>
														<li><a href="#" id="QRDA" onClick="change_main_Selection(this);">2019 QRDA Export</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_CQM_Import")) == true) { ?>
														<li><a href="#" id="CQMIMPORT_2020" onClick="change_main_Selection(this);">2020 QRDA-1 Import</a></li>
														<li><a href="#" id="CQMIMPORT" onClick="change_main_Selection(this);">CQM Import</a></li>
                                                            <?php } ?>
                                                        <?php } ?>
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="ccd">
													<ul>
														<?php if($_SESSION['sess_privileges']['priv_ccd_export']=="1" || core_check_privilege(array("priv_admin")) == true || core_check_privilege(array("priv_cl_ccd")) == true){ ?>
                                                            <?php if (core_check_privilege(array("priv_ccd_export")) == true) { ?>
                                                        <li><a href="#" id="CCDEXPORT" onClick="change_main_Selection(this);">CCD Export</a></li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="api">
													<ul>
                                                        <?php if(core_check_privilege(array("priv_api_access")) == true || core_check_privilege(array("priv_report_api_access")) == true){ ?>
                                                            <?php if (core_check_privilege(array("priv_report_Access_Log")) == true || core_check_privilege(array("priv_api_access")) == true) { ?>
                                                        <li><a href="#" id="APIACCESSLOG" onClick="change_main_Selection(this);">Access Log</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Call_Log")) == true || core_check_privilege(array("priv_api_access")) == true) { ?>
														<li><a href="#" id="APICALLLOG" onClick="change_main_Selection(this);">Call Log</a></li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="allscripts">
													<ul>
                                                        <?php if( is_allscripts('enabled') === true ) { ?>
                                                        <li><a href="#" id="WVLOG" onClick="change_main_Selection(this);">Workview API Call Log</a></li>
                                                        <?php } ?>
                                                    </ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="state">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_report_State")) == true) { ?>
                                                            <?php if (core_check_privilege(array("priv_report_KY_State_Report")) == true) { ?>
                                                        <li><a href="#" id="STATEKY" onClick="change_main_Selection(this);">KY State Report</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_TN_State_Report")) == true) { ?>
														<li><a href="#" id="STATETN" onClick="change_main_Selection(this);">TN State Report</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_NC_State_Report")) == true) { ?>
                                                        <li><a href="#" id="STATENC" onClick="change_main_Selection(this);">NC State Report</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_IL_State_Report")) == true) { ?>
                                                        <li><a href="#" id="STATEIL" onClick="change_main_Selection(this);">IL State Report</a></li>
														<?php } if (core_check_privilege(array("priv_report_PA_State_Report")) == true) { ?>
                                                        <li><a href="#" id="STATEPA" onClick="change_main_Selection(this);">PA State Report</a></li>
														<?php } if (core_check_privilege(array("priv_report_TX_State_Report")) ==true){ ?>
															<li><a href="#" id="STATETX" onClick="change_main_Selection(this);">TX State Report</a></li>
														<?php } if (core_check_privilege(array("priv_report_ASC_State_Report")) ==true){ ?>
															<li><a href="#" id="STATEASC" onClick="change_main_Selection(this);">ASC State Report</a></li>
														<?php } if (core_check_privilege(array("priv_report_new_state_report")) ==true){ ?>
															<li><a href="#" id="NEWSTATE" onClick="change_main_Selection(this);">New State Report</a></li>
														<?php } if (core_check_privilege(array("priv_report_SPARCS_Report")) ==true){ ?>
															<li><a href="#" id="SPARCS" onClick="change_main_Selection(this);">SPARCS Report</a></li>	
                                                       <?php } } ?>

                                                    </ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="opticals">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_report_optical")) == true) { ?>
                                                            <?php if (core_check_privilege(array("priv_cn_reports")) == true) { ?>
                                                        <li><a href="#" id="opticalCL" onClick="change_main_Selection(this);">Contact Lens Report</a></li>
                                                            <?php } if (core_check_privilege(array("priv_contact_lens")) == true) { ?>
                                                        <li><a href="#" id="opticalCLO" onClick="change_main_Selection(this);">Contact Lens Orders</a></li>
                                                            <?php } if (core_check_privilege(array("priv_glasses")) == true) { ?>
														<li><a href="#" id="opticalG" onClick="change_main_Selection(this);">Glasses</a></li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="reminders">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_sc_house_calls")) == true) { ?>
                                                            <?php if (core_check_privilege(array("priv_dat_appts")) == true) { ?>
                                                        <li><a href="#" id="DayAppts" onClick="change_main_Selection(this);">Day Appts</a></li>
                                                            <?php } if (core_check_privilege(array("priv_recalls")) == true) { ?>
                                                        <li><a href="#" id="ReminderRecall" onClick="change_main_Selection(this);">Recalls</a></li>
                                                            <?php } if (core_check_privilege(array("priv_reminder_lists")) == true) { ?>
                                                        <li><a href="#" id="ReminderLists" onClick="change_main_Selection(this);">Reminder Lists</a></li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="clinical_rpt">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_cl_clinical")) == true) { ?>
                                                            <?php if (core_check_privilege(array("priv_report_Clinical_Report")) == true) { ?>
                                                        <li><a href="#" id="clinicalRpt" onClick="change_main_Selection(this);">Clinical Report</a></li>
                                                            <?php } if (core_check_privilege(array("priv_report_Auto_Finalize_Charts_Report")) == true) { ?>
                                                        <li><a href="#" id="auto_finalize_charts_rpt" onClick="change_main_Selection(this);">Auto Finalize Charts Report</a></li>
                                                        <?php }?>
                                                        <li><a href="#" id="unbilled_tests_rpt" onClick="change_main_Selection(this);">Unbilled Tests</a></li>

														<?php
															$clinical_reports='';
															$clinical_rs = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` ='clinical' and `delete_status`=0");
															while($clinical_res = imw_fetch_assoc($clinical_rs)){
																$clinical_id = $clinical_res['id'];
																$clinical_name = $clinical_res['template_name'];
																$report_priv_name = str_replace(array(" ", "/","-"), "_", $clinical_name);
																$name_id=strtolower($report_priv_name.'_rpt');
																$report_priv_name= strtolower($report_priv_name.$clinical_id);
																if (core_check_privilege(array('priv_report_'.$report_priv_name)) == true || $clinical_res['default_report']==0) {
																	$clinical_reports.= '<li><a href="#" id="'.$name_id.'" onClick="change_main_Selection(this);">'.$clinical_name.'</a></li>';
																}
															}
															echo $clinical_reports;
														?>
                                                        <?php } ?>
														<li><a href="#" id="monitoring_report" onClick="change_main_Selection(this);">Monitoring Report</a></li>
                                                    </ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="rules_rpt">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_report_Rules")) == true) { ?>
                                                            <?php if (core_check_privilege(array("priv_report_A_R_Aging_Rules")) == true) { ?>
                                                        <li><a href="#" id="rulesRpt" onClick="change_main_Selection(this);">A/R Aging Rules</a></li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </ul>
												</div>
												<div role="tabpanel" class="tab-pane " id="iportal_rpt">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_report_iPortal")) == true) { ?>
                                                            <?php if (core_check_privilege(array("priv_report_Survey")) == true) { ?>
                                                        <li><a href="#" id="SurveyRpt" onClick="change_main_Selection(this);">Survey</a></li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </ul>
												</div>
											</div>
										</div>
									</div>
                                    <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
								</ul>
							</li>
													<?php } ?>

													<!-- Tab - Settings -->
													<?php if( $this_provider_tabs['Admin']['status'] == 'on' ) { ?>
							<li class="dropdown mega-dropdown adminmne">
                            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" id="Admin" onClick="change_main_Selection(this);">Settings</a>
                                            <ul class="dropdown-menu mega-dropdown-menu row" style="width:800px;z-index: 999999;">
                                <div class="row adminmenu">
									<div class="col-sm-3">
                                        <ul class="nav nav-tabs  admleft sadmleft" role="tablist">
                                            <?php if (core_check_privilege(array("priv_admin")) == true) { ?>
                                                <li role="presentation" class=""><a href="#admin" aria-controls="admin" role="tab" data-toggle="tab">Admin</a></li>
                                            <?php } if (core_check_privilege(array("priv_admin_billing")) == true) { ?>
                                                <li role="presentation"><a href="#billing" aria-controls="billing" role="tab" data-toggle="tab" >Billing</a></li>
                                            <?php } if (core_check_privilege(array("priv_admin_clinical")) == true) { ?>
                                                <li role="presentation"><a href="#chartNotes" aria-controls="chartNotes " role="tab" data-toggle="tab">Clinical </a></li>
                                            <?php } if ($admin_docs_tab_priv == true) { ?>
                                                <li role="presentation"><a href="#documents" aria-controls="documents" role="tab" data-toggle="tab">Documents</a></li>
                                            <?php } if (core_check_privilege(array("priv_iOLink")) == true) { ?>
                                                <li role="presentation" style="text-transform:none;"><a href="#IASC" aria-controls="IASC" role="tab" data-toggle="tab">iASC Link</a></li>
                                            <?php } if (core_check_privilege(array("priv_iMedicMonitor")) == true) { ?>
                                                <li role="presentation" style="text-transform:none;"><a href="#imed_monitor_div" aria-controls="other" role="tab" data-toggle="tab">iMedic Monitor</a></li>
                                            <?php } if (core_check_privilege(array("priv_iportal")) == true) { ?>
                                                <li role="presentation" style="text-transform:none;"><a href="#iPortal" aria-controls="iPortal" role="tab" data-toggle="tab">iPortal</a></li>
                                            <?php } if (core_check_privilege(array("priv_manage_fields")) == true) { ?>
                                                <li role="presentation"><a href="#managefields" aria-controls="managefields" role="tab" data-toggle="tab">Manage Fields</a></li>
                                            <?php } if (core_check_privilege(array("priv_Admin_Optical")) == true) { ?>
                                                <li role="presentation"><a href="#optical" aria-controls="optical" role="tab" data-toggle="tab">Optical</a></li>
                                            <?php } if (core_check_privilege(array("priv_Admin_Reports")) == true) { ?>
                                                <li role="presentation"><a href="#Reports" aria-controls="Reports" role="tab" data-toggle="tab">Reports</a></li>
                                            <?php } if (core_check_privilege(array("priv_admin_scheduler")) == true) { ?>
                                                <li role="presentation"><a href="#scheduler" aria-controls="scheduler" role="tab" data-toggle="tab">Scheduler</a></li>
                                            <?php } if (core_check_privilege(array("priv_iols")) == true) { ?>
                                                <li role="presentation"><a href="#iol_lenses" aria-controls="iol_lenses" role="tab" data-toggle="tab">IOLs(Lenses)</a></li>
                                            <?php } ?>
                                        </ul>
									</div>
									<div class="col-sm-9">
										<div class="tab-content adsubmenu ">
										<div role="tabpanel" class="tab-pane  in active" id="admin">
                                            <ul>
                                                <?php if (core_check_privilege(array("priv_admin")) == true) { ?>
                                                    <?php if (core_check_privilege(array("priv_group")) == true) { ?>
                                                        <li><a href="#" id="AdmGroups" onClick="change_main_Selection(this)">Business Unit</a></li>
                                                    <?php } if (core_check_privilege(array("priv_facility")) == true) { ?>
                                                        <li><a href="#" id="AdmFacility" onclick="change_main_Selection(this)">Facilities</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admin_Heard_About_Us")) == true) { ?>
                                                        <li><a href="#" id="AdmCnslHAU" onClick="change_main_Selection(this)">Heard About Us</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admin_Provider_Groups")) == true) { ?>
                                                        <li><a href="#" id="AdmCnslPGP" onClick="change_main_Selection(this)">Provider Groups</a></li>
                                                    <?php } if (core_check_privilege(array("priv_ref_physician")) == true) { ?>
                                                        <li><a href="#" id="AdmRefPhy" onClick="change_main_Selection(this)">Ref. Physician</a></li>
                                                    <?php } if(core_check_privilege(array("priv_provider_management")) == true){ ?>
                                                        <li><a href="#" id="AdmProviders" onClick="change_main_Selection(this)">Users</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admin_Updox")) == true) { ?>
                                                        <?php if (is_updox('admin')) { ?>
                                                            <li><a href="#" id="AdmUpdox" onClick="change_main_Selection(this)">Updox</a></li>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <?php
                                                    //All Scripts - Touchworks
                                                        if(is_allscripts('enabled')){
                                                        ?>
                                                        <li><a href="#" id="AdmAllscripts" onClick="change_main_Selection(this)">Allscripts</a></li>
                                                            <?php
                                                        }
                                                        ?>

                                                    <?php
                                             	       // DSS
                                                        if(isDssEnable()){
                                                    ?>
                                                        <li><a href="#" id="AdmDSS" onClick="change_main_Selection(this)">DSS</a></li>
                                                    <?php } ?>
                                                    <?php
                                                    if (core_check_privilege(array("priv_grp_prvlgs")) == true) { echo '<li><a href="#" id="groupPrivileges" onClick="change_main_Selection(this)">Group Privileges</a></li>';}
                                                    if (core_check_privilege(array("priv_chng_prvlgs")) == true) { echo '<li><a href="#" id="changePrivileges" onClick="change_main_Selection(this)">Change Privileges</a></li>';}
                                                    if (core_check_privilege(array("priv_rules_mngr")) == true) { echo '<li><a href="#" id="taskRulesManager" onClick="change_main_Selection(this)">Rules Manager</a></li>'; }
                                                    ?>
                                                    <?php if (core_check_privilege(array("priv_Office_Hours_Settings")) == true) { ?>
                                                        <li><a href="#" id="loginLogoutHours" onClick="change_main_Selection(this)">Office Hours Settings</a></li>
                                                    <?php }
														if (core_check_privilege(array("priv_vital_interactions")) == true)
														{
														if( defined('VITAL_HOST'))
														{
                                                    ?>
														  <li><a href="#" id="AdmVitalInteractions" onClick="change_main_Selection(this)">Vital Interactions</a></li>
                                                    <?php
														}
														}if (core_check_privilege(array("priv_ar_worksheet_setting")) == true)
														{
                                                    ?>
														  <li><a href="#" id="AdmArWorksheetSetting" onClick="change_main_Selection(this)">AR Worksheet</a></li>
                                                    <?php
														}
													?>
                                                <?php }?>
                                            </ul>
										</div>
										<div role="tabpanel" class="tab-pane " id="iol_lenses">
                                            <ul>
                                                <?php if (core_check_privilege(array("priv_iols")) == true) { ?>
                                                    <?php if (core_check_privilege(array("priv_admn_iols_Manage_Lenses")) == true) { ?>
                                                        <li><a href="#" id="AdmManageLenses" onClick="change_main_Selection(this)">Manage Lenses</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_iols_Lens_Calculators")) == true) { ?>
                                                        <li><a href="#" id="AdmLCALC" onClick="change_main_Selection(this)">Lens Calculators</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_iols_IOL_Users_Lens")) == true) { ?>
                                                        <li><a href="#" id="AdmIOLUser" onClick="change_main_Selection(this)">IOL Users Lens</a></li>
                                                    <?php } ?>
                                                    <?php
                                                    // GETTING PHYSICIAN
                                                    /* $gehysicianStr = "SELECT * FROM users WHERE user_type = '1' AND delete_status = '0'";
                                                      $gehysicianQry = imw_query($gehysicianStr);
                                                      if(imw_num_rows($gehysicianQry) > 0){
                                                      while($gehysicianRows = imw_fetch_assoc($gehysicianQry)){
                                                      ++$count;
                                                      $phyNames = $gehysicianRows['username'];
                                                      $phyId = $gehysicianRows['id'];
                                                      if($count==1){
                                                      $currTab = $phyId;
                                                      }
                                                      $phyNamesArr[$phyId] = $phyNames;
                                                      }
                                                      }
                                                      $phyNameRows=ceil((imw_num_rows($gehysicianQry)/12));
                                                      if(count($phyNamesArr)>0){
                                                      foreach($phyNamesArr as $phyId => $phyName){
                                                      ++$c;
                                                      if(empty($phyName) == false){
                                                      ?>
                                                      <li><a href="#" id="AdmLensPhy" onClick="change_main_Selection(this,<?php echo $phyId; ?>);"><?php echo ucwords($phyName); ?></a></li>
                                                      <?php
                                                      }
                                                      }
                                                      } */
                                                    ?>
                                                <?php } ?>
                                            </ul>
										</div>
										<div role="tabpanel" class="tab-pane " id="scheduler">
											<ul>
                                                <?php if (core_check_privilege(array("priv_admin_scheduler")) == true) { ?>
                                                    <?php if (core_check_privilege(array("priv_admn_sch_Available")) == true) { ?>
                                                        <li><a href="#" id="AdmAvail" onClick="change_main_Selection(this)">Available</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_sch_Chain_Event")) == true) { ?>
                                                        <li><a href="#" id="AdmChnEvt" onClick="change_main_Selection(this)">Chain Event</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_sch_Procedure_Templates")) == true) { ?>
                                                        <li><a href="#"  id="AdmProTemp" onClick="change_main_Selection(this)">Procedure Templates</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_sch_Provider_Schedule")) == true) { ?>
                                                        <li><a href="#" id="AdmProSch" onClick="change_main_Selection(this)">Provider Schedule</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_sch_Schedule_Reasons")) == true) { ?>
                                                        <li><a href="#" id="AdmSchRea" onClick="change_main_Selection(this)">Schedule Reasons</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_sch_Schedule_Status")) == true) { ?>
                                                        <li><a href="#" id="AdmSchStat" onClick="change_main_Selection(this)">Schedule Status</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_sch_Schedule_Templates")) == true) { ?>
                                                        <li><a href="#" id="AdmSchTemp" onClick="change_main_Selection(this)">Schedule Templates</a></li>
                                                    <?php } //if (core_check_privilege(array("priv_admn_sch_Setting")) == true) { ?>
                                                        <li><a href="#" id="AdmSchSett" onClick="change_main_Selection(this)">Setting</a></li>
                                                    <?php //} ?>
                                                <?php } ?>
											</ul>
										</div>
										<div role="tabpanel" class="tab-pane " id="documents">
											<ul>
                                                <?php if ($admin_docs_tab_priv == true) { ?>
                                                    <?php if (core_check_privilege(array("priv_admn_docs_Collection")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'collection')">Collection</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Consent")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'consent')">Consent</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Consult")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'consult')">Consult</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Education")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'education')">Education</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Instructions")) == true) { ?>
                                                        <li><a href="#" id="AdminDocumentIns" onClick="change_main_Selection(this, 'education')">Instructions</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Logos")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onclick="change_main_Selection(this, 'logos')">Logos</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Op_Notes")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'op_notes')">Op Notes</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Package")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'package')">Package</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Panels")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'panels')">Panels</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Prescriptions")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'prescriptions')">Prescriptions</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Pt_Docs")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'pt_docs')">Pt. Docs</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Recalls")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onclick="change_main_Selection(this, 'recall')">Recalls</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Scan_Upload_Folders")) == true) { ?>
                                                        <li><a href="#" id="AdmCnslFD" onClick="change_main_Selection(this)">Scan/Upload Folders</a></li>
                                                    <?php } if (core_check_privilege(array("priv_set_margin")) == true) { ?>
                                                        <li><a href="#" id="AdmSM" onClick="change_main_Selection(this)">Set Margin</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Smart_Tags")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'smart_tags')">Smart Tags</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Statements")) == true) { ?>
                                                        <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'statements')">Statements</a></li>
                                                    <?php } if (core_check_privilege(array("priv_admn_docs_Statements")) == true) { ?>
														<li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'variable_help')">Variable Help</a></li>
													<?php } ?>
												<?php } ?>
											</ul>
										</div>
										<div role="tabpanel" class="tab-pane  " id="billing">
                                            <?php if (core_check_privilege(array("priv_admin_billing")) == true) { ?>
											<div class="row">
												<div class="col-sm-6">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_billing_Adjustment_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmBilAdjCode" onClick="change_main_Selection(this)">Adjustment Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Cases")) == true) { ?>
                                                            <li><a href="#" id="AdmBilCases" onClick="change_main_Selection(this)">Cases</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_CL_Charges")) == true) { ?>
                                                            <li><a href="#" id="AdmOPCLC" onClick="change_main_Selection(this)">CL Charges</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_CPT")) == true) { ?>
                                                            <li><a href="#" id="AdmBilCpt" onClick="change_main_Selection(this)">CPT</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Department")) == true) { ?>
                                                            <li><a href="#" id="AdmBilDept" onClick="change_main_Selection(this)">Department</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Discount_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmBilDisCode" onClick="change_main_Selection(this)">Discount Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Dx_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmBilDxCode" onClick="change_main_Selection(this)">Dx Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Fee_Table")) == true) { ?>
                                                            <li><a href="#" id="AdmBilFtbl" onClick="change_main_Selection(this)">Fee Table</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_ICD_10")) == true) { ?>
                                                            <li><a href="#" id="AdmBilIcd10" onClick="change_main_Selection(this)">ICD-10</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Insurance")) == true) { ?>
                                                            <li><a href="#" id="AdmBilIns" onClick="change_main_Selection(this)">Insurance</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Insurance_Groups")) == true) { ?>
                                                            <li><a href="#" id="AdmBilInsGrp" onClick="change_main_Selection(this)">Insurance Groups</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Messages")) == true) { ?>
                                                            <li><a href="#" id="AdmBilMsg" onClick="change_main_Selection(this)">Messages</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Modifiers")) == true) { ?>
                                                            <li><a href="#" id="AdmBilMod" onClick="change_main_Selection(this)">Modifiers</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Phrases")) == true) { ?>
                                                            <li><a href="#" id="AdmBilPhr" onClick="change_main_Selection(this)">Phrases</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Payment_Methods")) == true) { ?>
                                                        	<li><a href="#" id="AdmBilPayMethod" onClick="change_main_Selection(this)">Payment Methods</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Manage_POS")) == true) { ?>
                                                        	<li><a href="#" id="managePOS" onClick="change_main_Selection(this)">Manage POS</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_sage_sftp_cred")) == true) { ?>
                                                        	<li><a href="#" id="AdmBilSageSFTP" onClick="change_main_Selection(this)">Sage SFTP Credentials</a></li>
                                                        <?php } ?>
													</ul>
												</div>
												<div class="col-sm-6">
													<ul>
                                                        <?php if (core_check_privilege(array("priv_billing_POE")) == true) { ?>
                                                            <li><a href="#" id="AdmBilPoe" onClick="change_main_Selection(this)">POE</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Policies")) == true) { ?>
                                                            <li><a href="#" id="AdmBilPol" onclick="change_main_Selection(this)">Policies</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_POS_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmBilPos" onClick="change_main_Selection(this)">POS Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_POS_Facilities")) == true) { ?>
                                                            <li><a href="#" id="AdmBilPosF" onClick="change_main_Selection(this)">POS Facilities</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Pre_Auth_Templates")) == true) { ?>
                                                            <li><a href="#" id="AdmCnslPAT" onClick="change_main_Selection(this)">Pre Auth Templates</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Proc_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmBilProCode" onClick="change_main_Selection(this)">Proc Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Reason_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmBilReaCode" onClick="change_main_Selection(this)">Reason Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Revenue_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmBilRevCode" onClick="change_main_Selection(this)">Revenue Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Status")) == true) { ?>
                                                            <li><a href="#" id="AdmBilStats" onClick="change_main_Selection(this)">Status</a></li>
                                                        <?php }if (core_check_privilege(array("priv_billing_Status")) == true) { ?>
                                                            <li><a href="#" id="AdmClaimStats" onClick="change_main_Selection(this)">Claim Status</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Test_CPT_Preference")) == true) { ?>
                                                            <li><a href="#" id="AdmCNTST" onClick="change_main_Selection(this)">Test CPT Preference</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Type_Of_Service")) == true) { ?>
                                                            <li><a href="#" id="AdmBilTos" onClick="change_main_Selection(this)">TOS (Type of Service)</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Write_Off_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmBilWrtCode" onClick="change_main_Selection(this)">Write Off Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Zip_Codes")) == true) { ?>
                                                            <li><a href="#" id="AdmCnslZC" onClick="change_main_Selection(this)">Zip Codes</a></li>
                                                        <?php } if (core_check_privilege(array("priv_billing_Denial_Mgmt")) == true) { ?>
                                                            <li><a href="#" id="AdmCnsDenial" onClick="change_main_Selection(this)">Denial Management</a></li>
						<!--<li><a href="#" id="AdmEraRules" onClick="change_main_Selection(this)">ERA Rules</a></li>-->
                                                        <?php } ?>
                                                        <?php if( isPosFacGroupEnabled() ) { ?>
                                                            <li><a href="#" id="AdmBilPosFG" onClick="change_main_Selection(this)">POS Facilities Group</a></li>
														<?php } if (core_check_privilege(array("priv_billing_ccda_sftp_cred")) == true) { ?>
														<li><a href="#" id="AdmBilCcdaSFTP" onClick="change_main_Selection(this)">CCDA SFTP Credentials</a></li>		
                                                        <?php } ?>
													</ul>
												</div>
                                            </div><?php } ?>
                                        </div>
	<div role="tabpanel" class="tab-pane " id="chartNotes">
        <?php if (core_check_privilege(array("priv_admin_clinical")) == true) { ?>
		<div class="row">
			<div class="col-sm-6">
                <ul>
                    <?php if (core_check_privilege(array("priv_admn_clinical_Allergies")) == true) { ?>
                        <li><a href="#" id="AdmCnslAL" onClick="change_main_Selection(this)">Allergies</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_AP_Policies")) == true) { ?>
                        <li><a href="#" id="AdmCnslAPP" onClick="change_main_Selection(this)">AP Policies</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Botox")) == true) { ?>
                        <li><a href="#" id="AdmCNBtx" onClick="change_main_Selection(this)">Botox</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Exam_Extensions")) == true) { ?>
                        <li><a href="#" id="AdmClinical" onClick="change_main_Selection(this)">Clinical Exam Extensions</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Custom_HPI")) == true) { ?>
                        <li><a href="#" id="AdmHPI" onClick="change_main_Selection(this)">Custom HPI</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Drawings")) == true) { ?>
                        <li><a href="#" id="AdmCNDrawing" onClick="change_main_Selection(this)">Drawings</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Epost")) == true) { ?>
                        <li><a href="#" id="AdmCnslEP" onClick="change_main_Selection(this)">Epost</a></li>
                    <?php } if (core_check_privilege(array("priv_erx_preferences")) == true) { ?>
                        <li><a href="#" id="AdmERP" onClick="change_main_Selection(this)">eRx Preferences</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_FU")) == true) { ?>
                        <li><a href="#" id="AdminCFU" onclick="change_main_Selection(this)">F/U</a></li>
                    <?php } if (core_check_privilege(array("priv_immunization")) == true) { ?>
                        <li><a href="#" id="AdmIMz" onClick="change_main_Selection(this)">Immunization</a></li>
                    <?php } if (core_check_privilege(array("priv_admin")) == true) { ?>
												<li><a href="#" id="admIopDef" onClick="change_main_Selection(this)">IOP Default</a></li>
										<?php } if (core_check_privilege(array("priv_admn_clinical_Labs_Rad")) == true) { ?>
                        <li><a href="#" id="AdmCNLR" onClick="change_main_Selection(this)">Labs/Rad</a></li>
		    <li><a href="#" id="AdmLskOpt" onClick="change_main_Selection(this)">Lasik Options</a></li>
		    <li><a href="#" id="AdmLensUsed" onClick="change_main_Selection(this)">Lens Used</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Med")) == true) { ?>
                        <li><a href="#" id="AdmCnslMED" onClick="change_main_Selection(this)">Med.</a></li>
		    <li><a href="#" id="AdmMedProcSet" onClick="change_main_Selection(this)">Medical History > Procedures</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Ophth_Drops")) == true) { ?>
                        <li><a href="#" id="AdmCNOD" onClick="change_main_Selection(this)">Ophth. Drops</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Order")) == true) { ?>
                        <li><a href="#" id="AdmO" onClick="change_main_Selection(this)">Order</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Order_Sets")) == true) { ?>
                        <li><a href="#" id="AdmOS" onClick="change_main_Selection(this)">Order Sets</a></li>
                    <?php } ?>
                </ul>
			</div>
			<div class="col-sm-6">
				<ul>
                    <?php if (core_check_privilege(array("priv_admn_clinical_Order_Templates")) == true) { ?>
                        <li><a href="#" id="AdmOT" onClick="change_main_Selection(this, 'order_temp')">Order Templates </a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Phrases")) == true) { ?>
                        <li><a href="#" id="AdmCnslPhr" onClick="change_main_Selection(this)">Phrases</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Procedures")) == true) { ?>
                        <li><a href="#" id="AdmCnslPRO" onClick="change_main_Selection(this)">Procedures</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Pt_Chart_Locked")) == true) { ?>
                        <li><a href="#" id="AdmCNPCL" onClick="change_main_Selection(this)">Pt Chart Locked</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Rx_Template")) == true) { ?>
                        <li><a href="#" id="AdmCnslRXT" onClick="change_main_Selection(this)">Rx Template</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_SCP_Reasons")) == true) { ?>
                        <li><a href="#" id="AdmCNSCPR" onClick="change_main_Selection(this)">SCP Reasons</a></li>
                    <?php } if (core_check_privilege(array("priv_admin_scp")) == true) { ?>
                        <li><a href="#" id="AdmSCP" onClick="change_main_Selection(this)">Site Care Plan</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Specialty")) == true) { ?>
                        <li><a href="#" id="AdmCNSpec" onClick="change_main_Selection(this)">Specialty</a></li>
                    <?php } ?>
		<li><a href="#" id="AdmCNSoC" onClick="change_main_Selection(this)">Standards of Care</a></li>
		<?php if (core_check_privilege(array("priv_admn_clinical_Sx")) == true) { ?>
                        <li><a href="#" id="AdmCnslSX" onClick="change_main_Selection(this)">Sx</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Sx_Planning")) == true) { ?>
                        <li><a href="#" id="AdmCnslSXP" onClick="change_main_Selection(this)">Sx Planning</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Template")) == true) { ?>
                        <li><a href="#" id="AdmCNTemp" onClick="change_main_Selection(this)">Template</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Test_Template")) == true) { ?>
                        <li><a href="#" id="AdmCNTT" onClick="change_main_Selection(this)">Test Templates</a></li>
		    <li><a href="#" id="AdmCNTDiagnosis" onClick="change_main_Selection(this)">Test Diagnosis</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_Visit")) == true) { ?>
		    <li><a href="#" id="AdminCTesting" onClick="change_main_Selection(this)">Testing</a></li>
                        <li><a href="#" id="AdminCVST" onClick="change_main_Selection(this)">Visit</a></li>
                    <?php } if (core_check_privilege(array("priv_vs")) == true) { ?>
                        <li><a href="#" id="AdmVS" onClick="change_main_Selection(this)">VS</a></li>
                    <?php } if (core_check_privilege(array("priv_admn_clinical_WNL")) == true) { ?>
                        <li><a href="#" id="AdmCNWNL" onClick="change_main_Selection(this)">WNL</a></li>
                    <?php } ?>

				</ul>
			</div>
		</div>
        <?php } ?>
	</div>
	<div role="tabpanel" class="tab-pane " id="iPortal">
		<ul>
            <?php if (core_check_privilege(array("priv_iportal")) == true) { ?>
                <?php if (core_check_privilege(array("priv_admn_ipl_Auto_Responder")) == true) { ?>
                    <li><a href="#" id="AdmIAR" onClick="change_main_Selection(this)">Auto Responder</a></li>
                <?php } if (core_check_privilege(array("priv_admn_ipl_iPortal_Settings")) == true) { ?>
                    <li style="text-transform:none;"><a href="#" id="AdmIPIS" onClick="change_main_Selection(this)">iPortal Settings</a></li>
                <?php } if (core_check_privilege(array("priv_admn_ipl_Preferred_Images")) == true) { ?>
                    <li><a href="#" id="AdmIPPI" onClick="change_main_Selection(this)">Preferred Images</a></li>
                <?php } if (core_check_privilege(array("priv_admn_ipl_Print_Preferences")) == true) { ?>
                    <li><a href="#" id="AdmIPPP" onClick="change_main_Selection(this)">Print Preferences</a></li>
                <?php } if (core_check_privilege(array("priv_admn_ipl_Security_Questions")) == true) { ?>
                    <li><a href="#" id="AdmIPSQ" onClick="change_main_Selection(this)">Security Questions</a></li>
                <?php } if (core_check_privilege(array("priv_admn_ipl_Set_Survey")) == true) { ?>
                    <li><a href="#" id="AdmIPSS" onClick="change_main_Selection(this)">Set Survey</a></li>
                <?php } if (core_check_privilege(array("priv_admn_ipl_Survey")) == true) { ?>
                    <li><a href="#" id="AdmIPS" onClick="change_main_Selection(this)">Survey</a></li>
                <?php } if ( isset($GLOBALS['ERP_API_PATIENT_PORTAL']) && $GLOBALS['ERP_API_PATIENT_PORTAL'] == 1
                        && core_check_privilege(array("priv_admn_erp_portal")) == true) { ?>
                    <li><a href="#" id="AdmErpApi" onClick="change_main_Selection(this)">Eye Reach Patient Portal</a></li>
                <?php } ?>
            <?php } ?>
		</ul>
	</div>
	<div role="tabpanel" class="tab-pane " id="managefields">
		<ul>
            <?php if (core_check_privilege(array("priv_manage_fields")) == true) { ?>
                <?php if (core_check_privilege(array("priv_admn_mcf_Custom_Fields")) == true) { ?>
                    <li><a href="#" id="AdmMFCF" onClick="change_main_Selection(this)">Custom Fields</a></li>
                <?php } if (core_check_privilege(array("priv_admn_mcf_General_Health_Questns")) == true) { ?>
                    <li><a href="#" id="AdmMFGH" onClick="change_main_Selection(this)">General Health Questions</a></li>
                <?php } if (core_check_privilege(array("priv_admn_mcf_Ocular_Questions")) == true) { ?>
                    <li><a href="#" id="AdmMFOC" onclick="change_main_Selection(this)">Ocular Questions</a></li>
                <?php } if (core_check_privilege(array("priv_admn_mcf_Practice_Fields")) == true) { ?>
                    <li><a href="#" id="AdmMFPF" onClick="change_main_Selection(this)">Practice Fields</a></li>
                <?php } if (core_check_privilege(array("priv_admn_mcf_Tech_Fields")) == true) { ?>
                    <li><a href="#" id="AdmMFTF" onClick="change_main_Selection(this)">Tech. Fields</a></li>
                <?php } ?>
                <li><a href="#" id="userMessagesFolder" onClick="change_main_Selection(this)">Messages Folder</a></li>
            <?php } ?>
		</ul>
	</div>
	<div role="tabpanel" class="tab-pane  " id="optical">
		<ul>
            <?php if (core_check_privilege(array("priv_Admin_Optical")) == true) { ?>
                <?php if (core_check_privilege(array("priv_admn_optical_Frames")) == true) { ?>
                    <li><a href="#" id="AdmOPF" onClick="change_main_Selection(this)">Frames</a></li>
                <?php } if (core_check_privilege(array("priv_admn_optical_Lenses")) == true) { ?>
                    <li><a href="#" id="AdmOPL" onClick="change_main_Selection(this)">Lenses</a></li>
                <?php } if (core_check_privilege(array("priv_admn_optical_Vendor")) == true) { ?>
                    <li><a href="#" id="AdmOPV" onClick="change_main_Selection(this)">Vendor</a></li>
                <?php } if (core_check_privilege(array("priv_admn_optical_Contact_Lens")) == true) { ?>
                    <li><a href="#" id="AdmOPC" onClick="change_main_Selection(this)"><b>Contact Lens</b></a></li>
                <?php } ?>
                <li class="divider"></li>
                <?php if (core_check_privilege(array("priv_admn_optical_Color")) == true) { ?>
                    <li><a href="#" id="AdmOPC" onClick="change_main_Selection(this)">Color</a></li>
                <?php } if (core_check_privilege(array("priv_admn_optical_Lens_Codes")) == true) { ?>
                    <li><a href="#" id="AdmOPLC" onClick="change_main_Selection(this)">Lens Codes</a></li>
                <?php } if (core_check_privilege(array("priv_admn_optical_Make")) == true) { ?>
                    <li><a href="#" id="AdmOPM" onClick="change_main_Selection(this)">Make</a></li>
                <?php } ?>
            <?php } ?>
		</ul>
	</div>
	<div role="tabpanel" class="tab-pane " id="IASC">
		<ul>
            <?php if (core_check_privilege(array("priv_iOLink")) == true) { ?>
                <?php if (core_check_privilege(array("priv_admn_iasc_iASC_Link_Settings")) == true) { ?>
                    <li style="text-transform:none;"><a href="#" id="AdmiASCLink" onClick="change_main_Selection(this)">iASC Link Settings</a></li>
                <?php } if (core_check_privilege(array("priv_admn_iasc_Surgery_Consent_Form")) == true) { ?>
                    <li><a href="#" id="AdminDocument" onClick="change_main_Selection(this, 'surgery_consent')">Surgery Consent Form</a></li>
                <?php } ?>
            <?php } ?>
		</ul>
	</div>
	<!-- <div role="tabpanel" class="tab-pane fade" id="Consol">
		<div class="row">
			<div class="col-sm-6">
				<ul></ul>
			</div>
			<div class="col-sm-6">
				<ul></ul>
			</div>
		</div>
	</div> -->
	<div role="tabpanel" class="tab-pane " id="Reports">
		<ul>
            <?php if (core_check_privilege(array("priv_Admin_Reports")) == true) { ?>
                <?php if (core_check_privilege(array("priv_admn_reports_Audit_Policies")) == true) { ?>
                    <li><a href="#" id="AdmAP" onClick="change_main_Selection(this)">Audit Policies</a></li>
                    <!--<li><a href="#" id="AdmRptCli" onClick="change_main_Selection(this)">Clinical</a></li>-->
                    <!--<li><a href="#" id="AdmRptCol" onClick="change_main_Selection(this)">Collection</a></li>-->
                <?php } if (core_check_privilege(array("priv_admn_reports_Compliance")) == true) { ?>
                    <li><a href="#" id="AdmRptCom" onClick="change_main_Selection(this)">Compliance</a></li>
                <?php } if (core_check_privilege(array("priv_admn_reports_CPT_Groups")) == true) { ?>
                    <li><a href="#" id="AdmCnslCPTGP" onClick="change_main_Selection(this)">CPT Groups</a></li>
                <?php } if (core_check_privilege(array("priv_admn_reports_Fac_Groups")) == true) { ?>
                    <li><a href="#" id="AdmCnslFACGP" onClick="change_main_Selection(this)">Fac. Groups</a></li>
                <?php } if (core_check_privilege(array("priv_admn_reports_Financials")) == true) { ?>
                    <li><a href="#" id="AdmRptFin" onClick="change_main_Selection(this)">Financials</a></li>
                    <!--<li><a href="#" id="AdmRptOpt" onClick="change_main_Selection(this)">Optical</a></li>-->
                <?php } if (core_check_privilege(array("priv_admn_reports_Practice_Analytic")) == true) { ?>
                    <li><a href="#" id="AdmRptPra" onClick="change_main_Selection(this)">Practice Analytic</a></li>
                <?php } if (core_check_privilege(array("priv_admn_reports_Ref_Groups")) == true) { ?>
                    <li><a href="#" id="AdmCnslREFGP" onClick="change_main_Selection(this)">Ref. Groups</a></li>
                <?php } if (core_check_privilege(array("priv_admn_reports_Scheduler")) == true) { ?>
                    <li><a href="#" id="AdmRptSch" onClick="change_main_Selection(this)">Scheduler</a></li>
                <?php } ?>
            <?php } ?>
			<!--<li><a href="#" id="AdmRptSta" onClick="change_main_Selection(this)">Statements</a></li>-->
		</ul>
	</div>
	 <div role="tabpanel" class="tab-pane " id="imed_monitor_div">
		<ul>
            <?php if (core_check_privilege(array("priv_iMedicMonitor")) == true) { ?>
                <?php if (core_check_privilege(array("priv_admn_imm_iMedic_Monitor")) == true) { ?>
                    <li style="text-transform:none;"><a href="#" id="AdmImM" onClick="change_main_Selection(this)">iMedic Monitor</a></li>
                <?php } if (core_check_privilege(array("priv_room_assign")) == true) { ?>
                    <li><a href="#" id="AdmRA" onClick="change_main_Selection(this)">Room Assign</a></li>
                <?php } if (core_check_privilege(array("priv_Manage_Columns")) == true) { ?>
                    <li><a href="#" id="AdImMC" onClick="change_main_Selection(this)">Manage Columns</a></li>
                <?php } ?>
            <?php } ?>
		</ul>
	</div>
</div>

	</div>


	</div>

	                         		<iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
								</ul>
                            </li>
													<?php } ?>
						</ul>
					</div><!-- /.navbar-collapse -->
				</nav>
			</div>

                <div class="col-lg-3 col-md-1  col-sm-12 col-xs-12" id="search_patient" style="z-index:999999;">
					<div class="popover_search hidden-md">
						<form border="0" method="get" target="fmain" name="find_patient" action="patient_select.php" onsubmit="return validSearch();">
							<div class="form-inline text-nowrap">
								<input type="text" class="form-control srch_pt_text" name="patient" id="patient" placeholder="Search patient..." value="<?php echo isset($_SESSION['patient']) ? $_SESSION['patient'] : ''; ?>">
								<input type="text" id="findBy" name="findBy"  value="Active"  class="form-control active_dd" readonly>
								<div class="dropdown">
									<a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-primary" data-target="#"><span class="caret"></span></a>
									<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu" id="main_search_dd">

									</ul>
								</div>
								<button type="submit" class="btn tsearch"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                                <button id="notifier_icon" class="btn btn-success hide" aria-hidden="true" type="button">
                                    <span class="glyphicon glyphicon-info-sign infoIcn default"></span>
                                </button>
							</div>
						</form>
				    </div>
					<a class="btn btn-default ptsrchbut  hidden-sm hidden-lg" href="#" role="button"  onClick="set_mobile_pt_search(this);">Pt. Search <span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
                </div>
                <iframe id="fake_iframe"  allowtransparency="true" src="about:blank"></iframe>
        </div>
    </div>
</header>
<div class="usersection user_fst_landing" id="first_toolbar" data-calling="user_fst_landing">
  <div id="main_toolbar">
    <div class="row">
      <div class="col-sm-5" >
        <ul>
		  <li class="elacc demograph title_name">
            <!-- ACC -->
            <ul>
              <li ><span class="actpagname" id="acc_page_name"></span></li>
            </ul>
            <!-- ACC -->
          </li>
          <li class="elacc pull-right">
            <!-- ACC -->
            <ul>
              <li class="ciopamt" id="acc_ci_unpost_pay_id" onClick="top.fmain.check_in_div('show');"></li>
              <li class="ciopamt" id="acc_pp_unpost_pay_id" onClick="top.fmain.patient_pre_payments_div('show');"></li>
              <li class="aalst" id="acc_accept_assignment_div"></li>
              <li class="aalst" id="no_balance_bill" style="color:blue; font-weight:bold; padding-left:10px;"></li>
              <li class="acc_ins_active"></li>
            </ul>
            <!-- ACC -->
          </li>
          <!-- Role Change -->
	<?php if($_SESSION["logged_user_type"]=="13" || $_SESSION["logged_user_type"]=="3"){

		if(!isset($_SESSION["user_role"]) || empty($_SESSION["user_role"])){
			$el_usr_role=$_SESSION["logged_user_type"];
		}else{
			$el_usr_role=$_SESSION["user_role"];
		}

	?>
	<li id="li_rc" >

		<div class="form-inline">
		<h4><label class="">Role as</label>

		<div class="radio">
			<input type="radio" name="el_usr_role" id="el_usr_role_tech" value="3" <?php if($el_usr_role=="3"){ echo "CHECKED"; } ?> onclick="change_role(this)" ><label for="el_usr_role_tech">Technician</label>
		</div>

		<div class="radio">
			<input type="radio" name="el_usr_role" id="el_usr_role_scribe" value="13" <?php if($el_usr_role=="13"){ echo "CHECKED"; } ?> onclick="change_role(this)" ><label for="el_usr_role_scribe">Scribe</label>
		</div>

		</h4>
		</div>

		<?php //echo $_SESSION["logged_user_type"]." - ".$_SESSION["user_role"]; ?>

	</li>
	<?php } ?>
	<!-- Role Change -->
          <!-- wv -->

          <li id="licharttmpdd" class="elchart chartcomdrop">
            <div class="input-group" id="el_charttemp_ig" >
              <input type="text" id="el_charttemp" class="form-control" aria-label="Template" placeholder="Template" readonly>
              <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right"></ul>
              </div>
              <!-- /btn-group -->
            </div>
            <!-- /input-group -->
          </li>
	<li id="lichartp3dd" class="elchart chartcomdrop ">
		<div class="input-group" id="el_chartp3_ig" >
			<input type="text" id="el_chartp3" class="form-control" aria-label="No Defect" placeholder="No Defect" readonly value="">
			<div id="ddmenu_p3" class="input-group-btn">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span></button>
				<!-- p3 -->
				<ul  class="dropdown-menu" >
					<li><a  href="#">Poor-view</a></li>
					<li><a  href="#">Phthisis</a></li>
					<li><a  href="#">Prosthesis</a></li>
					<li><a  href="#">No Defect</a></li>
					<div class="dropdown-divider"></div>
					<li class="dropdown-submenu open">
						<a  href="#" class="">Eye <span class="caret"></span></a>
						<ul  class="dropdown-menu" >
							<li><a  href="#">OU</a></li>
							<li><a  href="#">OD</a></li>
							<li><a  href="#">OS</a></li>
						</ul>
					</li>
				</ul>
				<!-- p3 -->
			</div>
		</div>
	</li>
	<!--<li id="lichartneurodd" class="elchart chartcomdrop"></li>-->
	<li class="elchart chartcomdrop" id="lichart_status"  >
		<span class="glyphicon glyphicon-lock hidden" id="icoPtLock" alt="Lock" title="Chart notes of this patient are locked. Please contact administrator." ></span>
		<label id="chart_status" class="label"></label>
		<div class="btn-group">
		<button type="button" id="btn_new_chart" class="btn btn-info dropdown-toggle btn-sm hidden" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">New</button>
		<ul class="dropdown-menu dropdown-menu-right"></ul>
		</div>
	</li>
<?php if(isDssEnable()) { ?>
	<style>
		.Work_View #dssServiceConnectedButton { display: inline-block !important; }
		.Medical_Hx #dssServiceConnectedButton a { line-height: 35px; }
	</style>
	<li id="dssServiceConnectedButton" style="background: #63327f;padding: 3px 5px 4px; vertical-align:bottom;display: none;">
		<a href="#" onclick="top.dssLoadServiceConnectedOpt('header_master')" title="DSS Service Connected Eligibility" style="color: #fff;background: #63327f;display: block;">Service Connected</a>
	</li>
<?php } ?>

	<li class="userdos pull-right" >
		<div class="elchart"  >
		<span>DOS</span>
		<div id="chartdosdiv"><input id="chartdos" value="" class="form-control" readonly></div>
		<a href="javascript:void(0);" id="chart_phy_note"  data-t1rigger="focus" html="true" placement="bottom" animation="false" onclick="show_phy_notes()"><span  class="glyphicon glyphicon-flag " title="Physician note" ></span></a>
		<span  class="glyphicon glyphicon-ban-circle <?php if(empty($_SESSION["disable_typeahead"])){ echo " hidden"; } ?>" title="Typeahead is disabled" id="ico_tp_sug" ></span>
		</div>
          </li>
          <!-- wv -->
        </ul>
      </div>

      <div class="col-sm-7">
      	<div class="row">
      <div class="col-sm-4">
        <div class="username" id="acc_page_middle">
			<div class="usrname1" >
				<ul>
					<li class="loguser">
						<div id="div_pt_name" class="nopt_noshow">
							<span id="patAccStatus"></span>
							<span class="glyphicon glyphicon-remove link_cursor" id="closePt" title="Close Patient" onClick="clean_patient_session();"></span>
							<figure>
								<img id="pt_img_tmb" src="<?php echo $GLOBALS['webroot']; ?>/library/images/username.png" class="link_cursor" onClick="enlarge_pt_image(this);"/>
								<div >
									<ul class="stflwicon" >
										<li class="ptcont" onClick="top.icon_popups('show_pt_alert');">
											<a href="#" onclick="show_pt_alert();" class="main_iconbar_icon ptalerts_icon1 hide" title="Patient Alerts"></a>
											<figure>0</figure>
										</li>
										<li class="ptcont portal hide" onClick="send_secure_msg();">
											<a href="#" onclick="send_secure_msg();" class="main_iconbar_icon hide" title="Patient Alerts"></a>
											<figure>P</figure>
										</li>
									</ul>
								</div>
							</figure>
							<h2 onClick="show_patient_info();" style="cursor:pointer; line-height: 20px;">&nbsp;</h2>
						</div>
						<div class="nopt_show">
							<h2>&nbsp;</h2>
						</div>
					</li>
				</ul>
			</div>
			<div class="clearfix"></div>

        </div>
      </div>
      <div class="col-sm-8 hght_lm" >
        <ul>
          <li id="vis_ptphy">
		<div class="row elchart text-center">
			<ul>
				<li>
					<input type="button" id="btnref_pcp" class="patient" onClick="top.fmain.showMultiPhy(1, 4);" value="PCP" />
					<span id="spanDetailPCP" class="refphy pointer" data-toggle="tooltip" data-html="true" data-placement="bottom"></span> <span id="spanMorePCP" class="to_show_more hidden" ></span>
				</li>
				<li>
					<input type="button" id="btnref_rp" class="patient" onClick="top.fmain.showMultiPhy(1, 1);" value="RP" />
					<span id="spanDetailRefPhy" class="refphy pointer" data-toggle="tooltip" data-html="true" data-placement="bottom"></span> <span id="spanMoreRefPhy" class="to_show_more hidden" ></span>
				</li>
				<li>
					<input type="button" id="btnref_cm" class="patient" onClick="top.fmain.showMultiPhy(1, 2);" value="CM" />
					<span id="spanDetailCoMangPhy" class="refphy pointer" data-toggle="tooltip" data-html="true" data-placement="bottom"></span> <span id="spanMoreCoMangPhy" class="to_show_more hidden" ></span>
				</li>
				<li>
					<ul class="ul_pt_forms">
					<li class="patienthov">
					<button id="btnPtForms" class="patient hvr-rectangle-out text-nowrap"><span class="lg-vw">Patient</span><span class="sm-vw">Pt.</span> Forms</button>
					</li><li>
					<span id="infoIns" class="refphy pointer" data-toggle="tooltip" data-html="true" data-placement="bottom"></span>
					</li>
					</ul>
				</li>
				<li>
					<button type="button" id="btn_ncf" class="btn btn-info  btn-xs" onclick="top.window.fmain.set_new_carry()">NCF</button>
				</li>
			</ul>
		</div>
          </li>
          <li class="elacc"><!-- ACC -->
            <div class="form-inline acontrht " id="acc_icon">
              <ul>
                <li class="acttopbut">
                  <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-default btn-xs" id="era_id" onClick="top.fmain.era_file_fun();">ERA </button>
                   <!-- <button type="button" class="btn btn-default btn-xs" id="btn_clm_status" onClick="top.fmain.claim_file_fun('clm_status');" style="display:none;">Claim Status Request</button>-->
                    <button type="button" class="btn btn-default btn-xs" id="claim_id" onClick="top.fmain.claim_file_fun();">Claims </button>
                    <button type="button" class="btn btn-default btn-xs" id="statement" onClick="top.fmain.statement_file_fun();">Statements </button>
                    <button type="button" class="btn btn-default btn-xs" id="AP" oncontextmenu="top.fmain.show_ap_div();return false;" onClick="popup_win('../../interface/accounting/accountingAPSearch.php','width=900px,height=450px,resizable=1,scrollbars=1');">A&P </button>
                    <button type="button" class="btn btn-default btn-xs" id="notes" onClick="popup_win('../accounting/ac_notes.php','width=1200px,height=650px,resizable=1,scrollbars=1,left=200');">Notes</button>
                    <?php if($pos_device){ ?>
                        <button type="button" class="btn btn-default btn-xs" id="pos_log" onClick="popup_win('../accounting/pos/patient_pos_transaction_log.php','width=1200px,height=650px,resizable=1,scrollbars=1,left=200');">POS Log</button>
                    <?php } ?>
                  </div>
                </li>
                <li><a href="#" onClick="top.fmain.ins_hx();"><span class="inshx">ins<br>
                  hx</span></a></li>
                <li class="acc_pat_dob"></li>
              </ul>
            </div>
            <!-- ACC -->
         </li><li class="stflwicon demo_btn">
            <!-- Demographics -->
            <div class="form-inline stflwicon eldemo" id="demo_iconbar">
              <ul class="text-right">
                <li>
                  <button class="btn btn-primary" type="button" onClick="<?php if(core_check_privilege(array("priv_vo_pt_info")) == true){ ?> view_only_acc_call(1); return false; <?php }else{ ?>javascript:top.clean_patient_session(); top.newPatient_info_main('');<?php } ?>">Add New</button>
                </li>
					<li class="stflwicon">
						<ul class="nopt_noshow">
						  <li class="ptcont" onClick="top.fmain.get_phssi_data();"><a href="#" class="main_iconbar_icon demograph_icons" title="Public Health Syndromic Surveillance Data" style="background-image:url('<?php echo $GLOBALS['webroot'] ?>/library/images/Public-Health-Syndromic-Surveillance-Data.png')"></a>
							<figure style="display: none;"></figure>
						  </li>
						  <?php
							//if(empty($_SESSION['patient']) == false){
								if(core_check_privilege(array("priv_pt_Override")) == true){ ?>
									<li id="MergePatients" onClick="top.fmain.showMergePatients();"><a href="#" class="main_iconbar_icon demograph_icons" title="Merge Patients" style="background-image:url('<?php echo $GLOBALS['webroot'] ?>/library/images/Header-Merge-Patients.png')"></a></li>
								<?php   }?>
								<li id="patientProvider" onClick="top.fmain.showPtProviders();"><a href="#" class="main_iconbar_icon demograph_icons" title="Patient Providers" style="background-image:url('<?php echo $GLOBALS['webroot'] ?>/library/images/Header-Patient-Providers.png')"></a></li>
							<?php
							//}
							?>

							<?php if(isUGAEnable()) { ?>
								<li class="UGAIcon" onClick="top.get_uga_status();"><a href="#" class="main_iconbar_icon demograph_icons" title="UGA Finance Patient Status" style="background-image:url('<?php echo $GLOBALS['webroot'] ?>/library/images/uga_icon_set.png')"></a></li>
							<?php } ?>
						</ul>
					</li>
              </ul>
            </div>
          </li>
          <li class="stflwicon tlastara">
            <ul class="nopt_noshow">
							<li id="ref_pri" class="flagopt pointer hidden" onClick="top.open_referrals(1);" title="Primary Referrals" ><i class="glyphicon glyphicon-flag font-30"></i><span>Pr</span></li>
							<li id="ref_sec" class="flagopt pointer hidden" onClick="top.open_referrals(2);" title="Secondary Referrals"><i class="glyphicon glyphicon-flag font-30"></i><span>Sr</span></li>
							<li id="ref_ter" class="flagopt pointer hidden" onClick="top.open_referrals(3);" title="Tertiary Referrals"><i class="glyphicon glyphicon-flag font-30"></i><span>Tr</span></li>

				<li id="erx"><a href="#" onclick="top.popup_win('../chart_notes/erx_patient_selection.php');"><img id="img_erx" src="<?php echo $GLOBALS['webroot'];?>/library/images/space.gif" alt="" title="Erx"></a></li>

            	<!-- PAtient Verbal Communication Icon -->
            	<li id="patient_communication"><a href="#" onclick="top.tb_popup(this);" title="Patient Communication" class="main_iconbar_icon pd0"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/Patient-Communicationicon.png" alt="" title="Pt. Communication" width="34" /></a></li>
             <?php if(constant('ZEISS_FORUM')=='YES'){
				 $zeiss_forum_status = get_Zeiss_details();
				 if($zeiss_forum_status!=''){
			  ?>
              <li onClick="top.ZeissViewer(<?php echo $zeiss_forum_status;?>);"><a href="#" class="main_iconbar_icon zess_icon" title="Zeiss"></a></li>
              <?php }}?>
              <li onClick="top.icon_popups('print_pt_summary');"><a href="#" class="main_iconbar_icon print_icon" title="Print Patient Summary"></a></li>
			  <li id="div_fac_mod" class="show">
				<a href="#" id="fac_mod_text" class="text-nowrap mod_icon main_iconbar_icon" data-placement="left" data-html="true" data-toggle="popover" data-trigger="hover" data-content="Click anywhere in the document to close this popover" data-container="body">
				</a>
			  </li>
            </ul>
          </li>
        </ul>
      </div>
				</div>
			</div>
    </div>
  </div>
</div>


<div class="pt_info_menu userdrop col-sm-4 col-sm-offset-4" id="patinentInfomenu" style="display: none;position:absolute;">
	<ul style="list-style:none;">
		<li>
			<div class="row">
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4"><b>DOB :</b></div>
						<div id="DOB" class="col-sm-8"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4"><b>SS :</b></div>
						<div id="ss" class="col-sm-8"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4"><b>Gender :</b></div>
						<div id="sex" class="col-sm-8"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-4"><b>Home Ph. :</b></div>
						<div id="phone_home" class="col-sm-8"></div>
					</div>
				</div>
			</div>
		</li>
	</ul>
</div>

<div class="clearfix"></div>
<div class="maincontent">
	<iframe src="<?php if(!empty($LOAD_THIS_TAB) && !empty($_SESSION['patient'])){echo 'about:blank';}else{echo '../landing/index.php';}?>" width="100%" name="fmain" id="fmain" frameborder="0" style="z-index:0; height:<?php echo $app_base->sess_height-370;?>px"></iframe>
</div>
<div class="clearfix"></div>
<footer>
    <div class="container-fluid">
        <div class="row">
        	<div class="col-sm-3">
				<?php
					$pg_btn_str = '';
					$pg_btn_arr= array(
						'priv_pt_icon_imm' => array('img' => 'ft_icon.png', 'title' => 'iMedicMonitor', 'app_name' => 'imedicmonitor'),
						'priv_pt_icon_optical' => array('img' => 'ft_icon2.png', 'title' => 'Optical', 'app_name' => 'optical'),
                        'priv_pt_icon_iasclink' => array('img' => 'ft_icon3.png', 'title' => 'iASCLink', 'app_name' => 'iasclink'),
						'priv_financial_dashboard' => array('img' => 'ft_icon4.png', 'title' => 'Financial Dashboard', 'app_name' => 'financial_dashboard'),
						'priv_pt_icon_support' => array('img' => 'ft_icon5.png', 'title' => 'Support', 'app_name' => 'support'),
						'priv_ar_worksheet' => array('img' => 'ar_dollar_icon.png', 'title' => 'AR Worksheet', 'app_name' => 'ar_worksheet')
					);
					foreach($pg_btn_arr as $key => $val){
						if(isset($_SESSION['sess_privileges'][$key]) && empty($_SESSION['sess_privileges'][$key]) == false && $_SESSION['sess_privileges'][$key] == 1){
							switch($key){
								case 'priv_pt_icon_optical':
									if(empty($GLOBALS['optical_directory_name']) == false){
										$pg_btn_str .= '&nbsp;<img data-toggle="tooltip" data-app-tab="app_btn" data-title="'.$val['title'].'" class="pointer" src="'.$GLOBALS['webroot'].'/library/images/'.$val['img'].'" data-app="'.$val['app_name'].'" />';
									}
								break;
								case 'priv_pt_icon_iasclink':
									if(empty($GLOBALS['iasclink_directory_name']) == false){
										$pg_btn_str .= '&nbsp;<img data-toggle="tooltip" data-app-tab="app_btn" data-title="'.$val['title'].'" class="pointer" src="'.$GLOBALS['webroot'].'/library/images/'.$val['img'].'" data-app="'.$val['app_name'].'" />';
									}
								break;

								case 'priv_pt_icon_support':
									//if(empty($_SESSION['sess_privileges']['priv_admin']) == false && $_SESSION['sess_privileges']['priv_admin'] == 1){
										$pg_btn_str .= '&nbsp;<img data-toggle="tooltip" data-app-tab="app_btn" data-title="'.$val['title'].'" class="pointer" src="'.$GLOBALS['webroot'].'/library/images/'.$val['img'].'" data-app="'.$val['app_name'].'" />';
									//}
								break;

								case 'priv_ar_worksheet':
									$pg_btn_str .= '&nbsp;<img data-toggle="tooltip" data-app-tab="app_btn" data-title="'.$val['title'].'" class="pointer" src="'.$GLOBALS['webroot'].'/library/images/'.$val['img'].'" data-app="'.$val['app_name'].'" />';
								break;

								default:
									$pg_btn_str .= '&nbsp;<img data-toggle="tooltip" data-app-tab="app_btn" data-title="'.$val['title'].'"  class="pointer" src="'.$GLOBALS['webroot'].'/library/images/'.$val['img'].'" data-app="'.$val['app_name'].'" />';
							}
						}
					}

					if(is_updox('fax')){
						$pg_btn_str .= '&nbsp;<img data-toggle="tooltip" data-app-tab="app_btn" data-title="Received Fax" class="pointer" src="'.$GLOBALS['webroot'].'/library/images/ft_icon6.png" data-app="received_fax" />';
					}
					echo $pg_btn_str;
				?>
            </div>
            <div class="col-sm-7 text-center" id="page_buttons"></div>
            <div class="col-sm-2 ftvertxt">
            	<?php echo date(phpDateFormat());?> <span id="tick2" class="text11"></span> |
            	<a href="#" onClick="srd()" class="a_clr1" title="Version Release Document"><?php echo $app_base->product_version;?></a>
            </div>
        </div>
    </div>
</footer>
<!-- Modal (USER SETTINGS) -->
<div id="div_user_settings" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <!-- Modal content-->
    <div class="modal-content">
    <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">User Settings</h4>
    </div>
    <div class="modal-body usersetting">
        <div class="row">
            <div class="col-sm-6 bordbot bordrht"><img src="../../library/images/change_profile_img.jpg" alt="" class="link_cursor" onClick="scanPatientImage('<?php echo $_SESSION['authId']; ?>');"/><br>Change Profile image</div>
            <div class="col-sm-6 bordbot"><img src="../../library/images/switch_user.jpg" alt="Switch User" data-dismiss="modal" onClick="showSwitchUserForm();" class="link_cursor"/><br>Switch User</div>
            <div class="col-sm-6 bordrht"><img src="../../library/images/change_password.jpg" alt="Change Password" data-dismiss="modal" onClick="icon_popups('change_pw');" class="link_cursor"/><br>Change Password</div>
            <div class="col-sm-6"><img src="../../library/images/logout.jpg" alt="Logout" data-dismiss="modal" onClick="window.top.logOut();" class="link_cursor"/><br>Logout</div>
            <div class="col-sm-12 text-left form-inline" style="text-transform: none;"><strong>Logged in Facility: </strong>
                <span class="logged_facility_text text_purple link_cursor" onmouseover="fac_mousehover();"  ><?php print $app_base->get_loggedin_facility_name(); ?></span>
                <span class="facility_dd" style="display:none;"><select class="form-control minimal"  name="l_facility" id="l_facility" onchange="changeLoggedInFacility(this);"><?php echo $ObjSecurity->getFacilityOptions(); ?></select></span>
            </div>
        </div>
		<form name="frmLogout" id="frmLogout" action="logout.php" method="post" style="margin:0px;">
            <input type="hidden" id="pg" name="pg" value="app-logout" />
            <input  type="hidden"  id="findByShow" name="findByShow" value="" />
        </form>
    </div>
    </div>

  </div>
</div>

<!-- Modal (SWITCH USER)-->
<div id="div_switch_user" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title">Switch User</h4>
      </div>
      <div class="modal-body" id="sw_us_body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="text text-warning" id="divErrorText"></div>
                    <form method="post" autocomplete="off" action="<?php echo $GLOBALS['webroot'];?>/interface/core/ajax_handler.php?task=processSwitchUser" name="sw_form" id="sw_form" onSubmit="return false;">
                        <input type="hidden" name="pg-switch" id="pg-switch" value="switch-user">
                        <input type="hidden" name="switch_user_tab" id="switch_user_tab" value="">
                        <?php if(isset($GLOBALS["ADDON_SU_FIELD"]) && $GLOBALS["ADDON_SU_FIELD"] === true): ?>
							<div class="form-group">
	                            <label for="suU">Username</label>
	                            <input type="text" name="suU" id="suU" class="form-control" maxlength="25" value="">
	                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="email">Password</label>
                            <input type="password" name="suP" id="suP" size="20" maxlength="100" class="form-control">
                            <input type="hidden" name="l_facility" value="<?php echo $app_base->login_facility;?>" />
                        </div>
                        <div class="form-group text-center">
                            <input type="button" class="btn btn-success" id="login" name="login" value="Login" onClick="processSW();">
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-warning text-center"><b>Please Note: "Login with your own Credentials only."</b></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--
	Start
 	Patient Account Status
-->
<div id="pt_account_status" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h4 class="modal-title" id="modal_title">&nbsp;Patient Next Action Status</h4>
     	</div>
			<div class="modal-body" style="min-height:450px;max-height:450px; overflow: hidden; overflow-y: auto;"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
  </div>
</div>
<!--
	End
 	Patient Account Status
-->

<!-- Phy day sch. modal -->
<div class="common_wrapper" >
	<div id="phy_checkin_appts" class="modal fade in" role="dialog" style="top:13%!important">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<div class="row">
						<div class="col-sm-6">
							<h4 class="header-title">Check - In Patients</h4>
						</div>
						<div class="col-sm-6 text-right">
							<div class="row">
								<div class="col-sm-10" id="phy_sch_prov"></div>
								<div class="col-sm-2">
									<button type="button" class="close" onClick="$('#phy_checkin_appts').hide().trigger('mouseLeave');">&times;</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12 text-center">
							<div class="process_loader"></div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger" type="button" onClick="$('#phy_checkin_appts').hide().trigger('mouseLeave');">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

        <div class="div_popup" id="div_version_release_doc"></div>

<!-- Modal (USER SETTINGS) -->
<div id="div_user_bubble" class="modal hide" role="dialog" data-show="false" data-msgid="">
  <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close bubbleClose" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title">User Messages / Forms / Tasks </h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="common_modal_wrapper">
    <!-- Modal -->
    <div id="secureMsgModal" class="modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="divHeader"> Send Patient Secure Message thru iPortal </h4>
                </div>
                <div class="modal-body ptmessapopup">
                    <div id="loader" style="position:absolute"></div>
                    <div id="divForm"></div>
                    <form name="frmForm" id="frmForm">
						<input type="hidden" value="<?php echo isset($_SESSION['patient']) ? $_SESSION['patient'] : ''; ?>" name="patient_id" id="patient_id">
                        <div class="row sendptmsg">
                            <div class="col-sm-12">
                                <div class="clearfix"></div>
                                <div class="row pt10">
                                    <div class="col-sm-3">Subject</div>
                                    <div class="col-sm-9"><input type="text" id="subject" name="subject" value="" class="form-control" /></div>
                                </div>
                                 <div class="clearfix"></div>
                                <div class="row pt10">
                                    <div class="col-sm-3">Message</div>
                                    <div class="col-sm-9"><textarea class="form-control" id="body" name="body" rows="5"></textarea></div>
                                </div>
                                 <div class="clearfix"></div>
                                <div class="row pt10">
                                    <div class="col-sm-3">&nbsp;</div>
                                    <div class="col-sm-9">
                                        <div class="checkbox">
                                            <input id="message_urgent" type="checkbox" name="message_urgent" value="1" class="chk_record">
                                            <label for="message_urgent">Urgent Message</label>
                                        </div>
                                    </div>
                                </div>
                                 <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-6">
                                <div id="divPtDemo"><div id="pat_details_td"></div></div>
                            </div>
                             <div class="clearfix"></div>
                        </div>
                        <input type="hidden" name="sync_type"  id="sync_type"  value="send_mail" />
                        <input type="hidden" name="reply_of" id="reply_of" />
                        <input type="hidden" name="filter_type" id="filter_type" value="<?php echo isset($filter_type) ? $filter_type : '';?>"/>
                    </form>
                </div>
                <div class="modal-footer" id="divBtnCont">
                    <button name="send_message" id="send_message" type="button" class="btn btn-primary" onClick="send_secure_msg('send_msg');">Send Message</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal (URGENT MESSAGES) -->
<?php
	$u_msg=$landPhyObj->top_five_messages('urgent');
	if($u_msg){
?>
<div class="common_modal_wrapper">
<div id="urgent_msg_alerts" class="modal" role="dialog" data-show="false" >
  <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close bubbleClose" data-dismiss="modal" >&times;</button>
				<h4 class="modal-title"><i class="glyphicon glyphicon-envelope mt5"></i> You have a urgent message</h4>
            </div>
            <div class="modal-body" style="max-height:550px;overflow:hidden; overflow-y:auto;">
            	<?php
					echo $u_msg;
            	?>
            </div>

        		<div id="module_buttons" class="modal-footer ad_modal_footer">
        			<div class="col-sm-12">
        				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        			</div>
        		</div>
        </div>
    </div>
</div>
</div>
	<script>var urgent_msg_alerts=true;</script>
<?php } ?>
<!-- End ModalURGENT MESSAGES -->

<!-- Modal (TASK ALERTS) -->
<div class="common_modal_wrapper">
<div id="task_alerts" class="modal" role="dialog" data-show="false" >
  <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close bubbleClose" data-dismiss="modal" >&times;</button>
							<h4 class="modal-title"><i class="glyphicon glyphicon-bell mt5"></i>&nbsp;Task alert(s) for today</h4>
            </div>
            <div class="modal-body" style="max-height:550px;overflow:hidden; overflow-y:auto;">
            	<?php
								$thtml = '';
								if(is_array($taskAlertData) && count($taskAlertData) > 0 ) {
									$thtml .= '<table class="table table-striped table-bordered">';
									$thtml .= '<thead class="header">';
									$thtml .= '<tr class="grythead">';
									$thtml .= '<th>Patient Name - ID</th>';
									$thtml .= '<th>Encounter Id</th>';
									$thtml .= '<th>Task</th>';
									$thtml .= '</tr>';
									$thtml .= '</thead>';
									$thtml .= '<tbody>';
									foreach($taskAlertData as $_data) {

										$thtml .= '<tr>';
										$thtml .= '<td>'.$_data['patient_name'].' - '.$_data['patientid'].'</td>';
										$thtml .= '<td>'.$_data['enc_id'].'</td>';
										$thtml .= '<td>'.$_data['task'].'</td>';
										$thtml .= '</tr>';
									}
									$thtml .= '</tbody>';
									$thtml .= '</table>';
								}
								echo $thtml;
            	?>
            </div>

        		<div id="module_buttons" class="modal-footer ad_modal_footer">
        			<div class="col-sm-12">
        				<button type="button" class="btn btn-success" onClick="top.dismissTaskAlert();">Ok</button>
        				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        			</div>
        		</div>
        </div>
    </div>
</div>
</div>
<!-- End Modal Task Alert -->

<div id="div_loading_image" class="text-center">
	<div class="loading_container">
		<div class="process_loader"></div>
    	<div id="div_loading_text" class="text-info">Please wait, while system is getting ready for you...</div>
	</div>
</div>
<div id="reason_code_options" class="hidden" ><?php echo $reason_code_options;  ?></div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-dropdownhover.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<!--<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.okayNav.min.js"></script>-->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js?<?php echo filemtime('../../library/js/common.js');?>"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js?<?php echo filemtime('../../library/messi/messi.js');?>"></script>
<!--<script src="<?php echo $GLOBALS['webroot']; ?>/library/interface/views/core_base.js"></script>-->
<!--<script src="<?php echo $GLOBALS['webroot']; ?>/library/interface/views/core_title_bar.js"></script>-->
<!--<script src="<?php echo $GLOBALS['webroot']; ?>/library/interface/chart_notes/js/buttons.js" type="text/javascript"></script>-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/md5.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/js_crypto_sha256.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/core_main.js?<?php echo filemtime('../../library/js/core_main.js');?>"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/buttons.js??<?php echo filemtime('../../library/js/buttons.js');?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_providers.js"></script>
<script type="text/javascript">
	var arr_opened_popups = new Array();
	var show_task_alert = <?php echo json_encode($show_task_alerts);?>;
	<?php
	if(isset($_SESSION["switch_user_tab"]) && trim($_SESSION["switch_user_tab"])!=''){
		$this_provider_tabs['LOAD_THIS_TAB'] = $_SESSION["switch_user_tab"];
		$_SESSION["switch_user_tab"] = '';
		unset($_SESSION["switch_user_tab"]);
	}
	if($this_provider_tabs['LOAD_THIS_TAB'] != ''){echo '/*top.change_main_Selection(document.getElementById("'.$this_provider_tabs['LOAD_THIS_TAB'].'"),""); */top.$("#'.$this_provider_tabs['LOAD_THIS_TAB'].'").triggerHandler("click"); ';} ?>
	<?php if (isset($app_base->session['sess_user_switched']) && $app_base->session['sess_user_switched'] == "no"){echo 'top.showSwitchUserForm();';}?>

	$(function(){
		$('img[data-app-tab=app_btn]').on('click',function(){
			var data_elem = $(this).data();
			if(data_elem.app != 'support'){
				if(data_elem.app == 'received_fax'){
					top.popup_win('<?php echo $GLOBALS['webroot']; ?>/interface/chart_notes/inbound_fax.php','height=760,width=1050,top=50,left=50');
				}
				else if(data_elem.app == 'ar_worksheet'){
					var f = "scrollbars=1,location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width="+(screen.availWidth-10)+",height="+(screen.availHeight-60);
					top.popup_win('<?php echo $GLOBALS['webroot']; ?>/interface/ar/index.php','ar_worksheet',f);
				}
				else
				{
					top.popup_win('<?php echo $GLOBALS['webroot']; ?>/interface/core/multi_login.php?app_name='+data_elem.app);
				}
			}else{
				var f = "scrollbars=1,location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width="+(screen.availWidth-100)+",height="+(screen.availHeight-100);
				top.popup_win('https://yoursupportportal.yourdomain.com/login',f);
			}
		});
	});

    function srd(){

		/*
		//return;
		$('#div_version_release_doc').load('release_doc.php?rand='+Math.random(),'',function(){
            $('#div_version_release_docs').modal('toggle');alert($('#div_version_release_doc'));
		});
		*/
		o = $('#div_version_release_doc');
		top.show_loading_image('show');
		o.load('release_doc.php?rand='+Math.random(),'',function(){
			ww = $(window).width();
			dw = o.width();
			o.css("position","absolute");
			o.css("top", Math.max(0, (($(window).height() - o.outerHeight()) / 2) + $(window).scrollTop()) + "px");
			o.css("left", Math.max(0, (($(window).width() - o.outerWidth()) / 2) + $(window).scrollLeft()) + "px");
			o.fadeIn();
		});


    }

	$(document).ready(function(e){
		$(document).on("keyup","#suU,#suP",function(k){
			if($(this).val().trim()!='' && k.keyCode=='13'){
				processSW(k.keyCode);
			}
		});
		<?php if(isset($_GET['loadPtHash'])){ ?>
		try{
			top.close_popwin();
			window.top.$('#curr_main_tab').val('Work_View');
			top.LoadWorkView('<?php echo $_GET['loadPtHash'];?>');
		}catch(e){

		}
		<?php }?>
		var providerImage = 'Provider_' + providerId + ".jpg";
		$.ajax({ url: "../admin/providers/webcam/getSessionPicResized.php?pId=" + providerId + "&pName="+providerImage+"&tWidth=35&tHeight=35",success: function(resp){
			if(resp == "")
			{
				$("#provider_image").width(nWidth + "px");
    			$("#provider_image").height(nHeight + "px");
    			//$("#provider_image").attr('src', JS_WEB_ROOT_PATH + "/library/images/demo_user_pic.jpg");
			}
			else
			{
    			var arr_resp = resp.split("~");
    			var nWidth = arr_resp[0];
    			var nHeight = arr_resp[1];
    			$("#provider_image").width(nWidth + "px");
    			$("#provider_image").height(nHeight + "px");
			}
			$("#provider_image").show();
		}});

        function checkdocsexists() {
            $.ajax({
                url:top.JS_WEB_ROOT_PATH+'/interface/core/ajax_handler.php?task=check_doc_exists',
                success: function(r){
                    $('.document_exists').removeClass('doc_exists');
                    $('.document_exists').removeClass('doc_not_exists');
                    $('.document_exists').addClass(r);
                }
            });
        }

		$('[data-toggle="tooltip"]').tooltip();
		//Taken action each time main frame URL changes
		$('#fmain').load(function(){
            checkdocsexists();
			update_iconbar(); //reload iconbar status.
			//Setting Popover on pt load
			//Get popover id
			var popover_id = $(".ptsrchbut").attr('aria-describedby');
			//If popover id exists then hide popover
			if(popover_id != '' || typeof(popover_id) !== 'undefined'){
				$(".ptsrchbut").popover('hide');
			}
			//Check nav changes on frame change
			set_nav_class(document.getElementById($('#curr_main_tab').val()));

			//disable backspace in frames
			this.contentWindow.document.onkeydown = keyCatcher;
		});

		//disable backspace in parent window
		document.onkeydown = keyCatcher;

		$("#rf_foll_name").bind('click', function(){show_rf_foll_pu();});
		<?php if($user_info_console['res_fellow_info']=="- Attending" && $app_base->logged_user_type!=3){ ?>
		$("#rf_foll_name").trigger("click");
		<?php } ?>

		top.update_iconbar();
		top.init_display();
		top.show_clock();

		show_loading_image('hide');
		$('[data-toggle="popover"]').popover();

		$("body").on('shown.bs.modal','#div_switch_user',function(){
			if( $(".modal-backdrop").length > 0 ){
				$(".modal-backdrop").css({'opacity':'0.95'});
			}

		});

		if( show_task_alert) {
			$("#task_alerts").modal('show');

		}

		//For setting popover for Pt. search
		set_mobile_pt_search();

        //Trigger Ajax call to check notifications
        var timeoutID = window.setTimeout(PeriodicCheckNotification, 7000);
    });
	var WebRoot = '<?php echo $GLOBALS['webroot']; ?>';

<?php
if(isset($_SESSION["remote_opt_loc_id"]) && $_SESSION["remote_opt_loc_id"]>0 && $_SESSION['opt_enc_id']>0){
	$opt_enc_id=$_SESSION['opt_enc_id'];
	$_SESSION['opt_enc_id']='';?>
		var opt_pat_id='<?php echo $_SESSION['patient']; ?>';
		var opt_enc_id='<?php echo $opt_enc_id; ?>';
		LoadAccountingView(opt_pat_id,opt_enc_id,'enter_charges');
<?php }else if(isset($_GET['md']) && trim($_GET['md']) != ''){
	if($_GET['md'] == 'rpt'){?>
		var send_url = "../accounting/accountingTabs.php?flagSetPid=true&tab=reviewPayments";
		core_redirect_to("Accounting", send_url);
<?php }else if(substr($_GET['md'],0,6) == 'rpt_ac'){?>
		// ADDED BY JASWANT FOR REPORTs
		var filename = "../accounting/accountingTabs.php";
		var send_url = "../billing/billing_session.php?patient=<?php echo $_GET['ptid']?>&rd2="+filename+"&front=yes&encounter=<?php echo substr($_GET['md'],0,6);?>";
		core_redirect_to("Accounting", send_url);
<?php }else if($_GET['md'] == 'eb'){?>
		var send_url = "../accounting/accounting_view.php?encounter_id=<?php echo $_SESSION['encounter_id'];?>";
		top.core_redirect_to("Accounting",send_url);
<?php }else if($_GET['md'] == 'ep'){?>
		var send_url = "../accounting/makePayment.php?encounter_id=<?php echo $_SESSION['encounter_id'];?>";
		top.core_redirect_to("Accounting",send_url);
<?php }
}else{?>

//do_change_main_Selection("<?php echo $LOAD_THIS_TAB;?>");
<?php }?>
</script>

<script>
$('.nav li a').hover(function (e) {
    e.preventDefault();
    var currentTab = $(this).attr('href');

    if($(this).parents('.adsubmenu').find('.tab-pane').hasClass('active')) {
        $(currentTab).show();
    } else {
        $('.tab-pane').hide();
    }

    if($(this).attr('id') === 'Admin') {
        $("ul.sadmleft li:first-child").addClass(' first_tab ');
        var tab = $('.first_tab').siblings('li.active').find('a').attr('href');
        if(tab) {
            $(tab).addClass(' active ').show();
        } else {
            $('li.first_tab').addClass(' active ');
            var showtab = $('.first_tab').find('a').attr('href');
            $(showtab).addClass(' active ').show();
            //$('#admin').addClass(' active ').show();
        }
    }
    if($(this).attr('id') === 'Reports_header') {
        $("ul.radmleft li:first-child").addClass(' rfirst_tab ');
        var rtab = $('.rfirst_tab').siblings('li.active').find('a').attr('href');
        if(rtab) {
            $(rtab).addClass(' active ').show();
        } else {
            $('li.rfirst_tab').addClass(' active ');
            var rshowtab = $('.rfirst_tab').find('a').attr('href');
            $(rshowtab).addClass(' active ').show();
            //$('#rep_scheduler').addClass(' active ').show();
        }
    }

    $(currentTab).addClass(' active ');
    $(currentTab).show();
    $(this).tab('show');
});

$('.nav li a').click(function (e) {
    return true;
});
/*show urgent message popup*/
if( urgent_msg_alerts) {
	$("#urgent_msg_alerts").modal('show');
}

function addScrollBar(){
	$('.tab-pane').each(function(){
		var innerWindowH=window.innerHeight;
		if($(this).attr('id')=='financials' || $(this).attr('id')=='rep_scheduler'){
			$(this).css('height', innerWindowH-100);
			$(this).css('overflow-y', 'scroll');
		}
	});
}

$(window).load(function(){
    <?php if(isset($_SESSION['ask_for_reason_patient']) && $_SESSION['ask_for_reason_patient']!=''){ ?>
        $("#patient").val('<?php echo $_SESSION["ask_for_reason_patient"];?>');
        $('form[name=find_patient]').submit();
    <?php } unset($_SESSION['ask_for_reason_patient']); ?>
});

$(document).ready(function(){
	addScrollBar();
});

$(window).resize(function(){
	addScrollBar();
});
</script>
</body>
</html>
