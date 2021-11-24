// Core Main JavaScript Document

var jquery_date_format = top.global_date_format; 	//	Common Date Format for jquery DatePicker
var jquery_date_time_format = top.global_date_format+' H:i:s'; 	//	Common Date Format for jquery DateTimePicker

var ser_root = top.ser_root;
if(typeof(ser_root) == 'undefined'){
	ser_root = window.opener.top.ser_root;
}

if(typeof(top.JS_WEB_ROOT_PATH) == 'undefined'){
	top.JS_WEB_ROOT_PATH = window.opener.top.JS_WEB_ROOT_PATH;
}

var window_resize_timer;

/** Typeahead Arrays **/
var arrRefPhy = new Array();
var arrRefPhyID = new Array();
var arrRefPhyFax= new Array();


function clear_console(){
	if (typeof console._commandLineAPI !== 'undefined') {
		console.API = console._commandLineAPI;
	} else if (typeof console._inspectorCommandLineAPI !== 'undefined') {
		console.API = console._inspectorCommandLineAPI;
	} else if (typeof console.clear !== 'undefined') {
		console.API = console;
	}
	console.API.clear();
}

//This function return inner dimentions of window or passed parameter object
function innerDim(obj){
	/***************
	
	Purpose: Retuns inner dimentions of provided object (or window)
	****************/
	if(typeof(obj)=='undefined'){obj = $(window);}
	var arDim = new Array();
	arDim['h'] = obj.innerHeight();
	arDim['w'] = obj.innerWidth();
	return arDim;
}

//Initialize display of main interface window
function init_display(){
	/***************
	
	Purpose: Sets Initial Display of Main interface (page loads after user logs in)
	****************/
	innerDim1 = top.innerDim($(window));
	var diff = window.outerHeight - screen.availHeight;
	diff = (diff>0) ? diff : 0;
	hh = $('header').height();
	if($('#first_toolbar').css('display')!='none'){fth = $('#first_toolbar').height();}else{fth = 0;}
	//if($('#second_toolbar').css('display')!='none'){sth = $('#second_toolbar').height();}else{sth = 0;}
	ff	= $('footer').height();
	//console.log(hh+'+'+fth+'+'+ff);
	browser_margin	= 14;
	brows	= get_browser();
	if(brows!='ie') browser_margin = 19;
	consumed_h 	= hh+fth+ff+(browser_margin)+diff;
	remains_h	= innerDim1['h']-consumed_h;
	$('#fmain').height(remains_h);

	$( window ).resize(function() {
		clearTimeout(window_resize_timer);
	  	window_resize_timer = setTimeout(function(){top.init_display();}, 500);
	});
}

/***This function opens SWITCH USER modal and sets a session to prevent disable it on browser refresh***/
function showSwitchUserForm(){
	//Check Work View;
	if($("#curr_main_tab").val() == "Work_View"){
		if(typeof(top.fmain.chkWVB4Move) == "function" && top.fmain.chkWVB4Move("SwitchUser")){
			return false;
		}
	}
	showHidemodal('hide','div_user_settings');
	$(".modal, .modal-backdrop",top.document).hide();
	$('#div_switch_user').modal({backdrop: 'static', show:true});
	if($('#div_switch_user:not(:hidden)')) $('.modal-backdrop.fade').css('opacity', '0.95');

	$('#switch_user_tab').val($("#curr_main_tab").val());

	if(typeof(addon_su_field) !== 'undefined' && addon_su_field == 1) {
		var objPass_suU = top.sw_form.suU;
		if(objPass_suU){setTimeout(function(){objPass_suU.focus();},1000);};
	} else {
		var objPass = top.sw_form.suP;
		if(objPass){setTimeout(function(){objPass.focus();},1000);};
	}
	top.master_ajax_tunnel('ajax_handler.php?task=set_su_opened'); //security for Browswer window refresh (F5).
}

/****SWITCH USER data collection and sending for processing****/
function processSW(evkc){
	if(typeof(evkc) != 'undefined' && evkc != '13'){/**DO NOTHING*****/}
	else{
		top.show_loading_image('show', '', 'Authenticating...');
		var msg = '';
		var callback = '';
		if(typeof(addon_su_field) !== 'undefined' && addon_su_field == 1) {
			if(document.getElementById('suU').value == ""){
				document.getElementById("divErrorText").innerHTML = "Please Enter Username.";
				top.show_loading_image('hide');
				return false;
			}
		}
		if(document.getElementById('suP').value == ""){
			document.getElementById("divErrorText").innerHTML = "Please Enter Password.";
			top.show_loading_image('hide');
			return false;
		}
		if (HASH_METHOD == "MD5")hash_str=md5(document.getElementById('suP').value);
		else hash_str=Sha256.hash(document.getElementById('suP').value);
		if(hash_str.length!=32 && hash_str.length!=64){
			top.fAlert('Password encryption failed. Security exception. Can\'t proceed.');
			return false;
		}
		$('#suP').val(hash_str);
		var cc_t=dgi("tick2").innerHTML;
		top.master_ajax_tunnel('ajax_handler.php?task=processSwitchUser&do=1',top.ResultProcessSW,$('#sw_form').serialize()+'&cc_t='+cc_t);
	}
}

function ResultProcessSW(result){
	if(result=='OK') window.open('index.php','_parent','');
	else if( result == 'Incorrect Password.' || result == 'You are not allowed to login in Off Hours.' ) {
		$("#divErrorText").text(result);
		$('#suP').val('').focus();
	}
}

/***This function will trigger the actions need to perform before leaving a tab***/
function action_before_tab_change(curr_tab,curr_sub_tab,main_tab,sub_tab){
	var flg_reload = (curr_tab == "Work_View" && typeof(sub_tab)!="undefined" && (sub_tab=="procedure" || sub_tab=="sx_plan"|| sub_tab=="prescription")) ? 0 : 1 ;
	if( typeof(sub_tab)!="undefined" && sub_tab == "physician_notes"){flg_reload=0;}
	/****REMOVE BUTTONS FROM FOOTER****/
	if(flg_reload){$('#page_buttons').html('');}
	//top.$(".elchart,.elacc,.eldemo").css("display","none");

	if(main_tab!='sch_icon_li'){top.$('#appt_scheduler_status').val('unloaded');}

	//var strFunName=''; var arrArgu = new Array();
	//doSaveChangeDB(strFunName,arrArgu);
}

/***This function execute the tab change action based on tabname***/
function core_redirect_to(tab_name, redirect_url, flgNc){window.top.change_main_Selection(window.top.document.getElementById(tab_name), '', redirect_url, flgNc);}

function set_nav_class(main_tab){
	var target_elem  = $('#first_toolbar');
	var parent_arr = new Array();
	//Creating aray of all main parents in the nav
	var get_tab_parents = $(main_tab).parentsUntil().find('li.dropdown.mega-dropdown a.dropdown-toggle').each(function(id,elem){
		var parent_id = $(elem).attr('id');
		if(parent_id && typeof(parent_id) != 'undefined'){
			parent_arr.push(parent_id);
		}
	});
	parent_arr.push('user_fst_landing');

	//Getting the parent of clicked element
		var main_tab_parents = $(main_tab).parentsUntil('a').closest('li.dropdown.mega-dropdown').find('a');
		var class_name = main_tab_parents.attr('id');

	//Removing prev. added classes
	$.each(parent_arr,function(id,val){
		if(target_elem.hasClass(val)){
			target_elem.removeClass(val);
		}
	});

	//Checking if a element is clicked or not & target calling is not close pt[clear pt session]
	if(target_elem.data('calling') != 'close_pt'){
		if($('.sitenav',top.document).find('[data-clicked]').length > 0){
			if(class_name != 'Admin' && class_name != 'Reports_header' && class_name != 'Billing'){
				if($('#patient_id',top.document).val() != ''){
					target_elem.data('calling',class_name);
				}else{
					target_elem.data('calling','user_fst_landing');
				}
			}else{
				target_elem.data('calling',class_name);
			}
		}

		var nav_calling = target_elem.data('calling');

		if(nav_calling == 'user_fst_landing'){
			if(target_elem.hasClass(target_elem) === true){
				target_elem.removeClass(class_name);
			}
			if(target_elem.hasClass('user_fst_landing') === false){
				target_elem.addClass('user_fst_landing');
			}
		}else{
			if(target_elem.hasClass('user_fst_landing') === true){
				target_elem.removeClass('user_fst_landing');
			}
			if(target_elem.hasClass(target_elem) === false){
				target_elem.addClass(class_name);
			}
		}
	}else{
		$.each(parent_arr,function(id,val){
			if(target_elem.hasClass(val)){
				target_elem.removeClass(val);
			}
		});
		target_elem.addClass('user_fst_landing');
		target_elem.data('calling','user_fst_landing');
	}

}


/***This function performs the tab change action***/
function change_main_Selection(main_tab, sub_tab, redirect_url, flgNc,onload){
	var main_tab_obj = main_tab;
	var sub_tab_obj = sub_tab;

	if( ($(main_tab_obj).attr('id') == 'Documents' || $(main_tab_obj).attr('id') == 'Documents_Lab' ) && top.doc_pop ) {
		var pid = parseInt(top.document.getElementById('patient_id').value);
		var extendUrl = '';
		if( !pid) {
			if($('#appt_scheduler_status').length > 0 ) {
				if($('#appt_scheduler_status').val()=='loaded') {
					pid = $("#global_ptid",top.fmain.document).val();
				}
			}
		}
		if($(main_tab_obj).attr('id') == 'Documents_Lab') { extendUrl = '?tab_name=DocTab'; }
		var features = "location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width="+screen.availWidth+",height="+screen.availHeight;
		if(pid) { top.popup_win('../common/documents.php'+extendUrl,features); }
		show_loading_image('hide');
		return false;
	}

	//
	if(typeof(sub_tab)!="undefined" && sub_tab=="procedure" && $("#patient_id").val()=="") {
		top.fAlert("Please select a patient.");
		return false;
	}

	//To check whether a link from nav is clicked or not
	$('.sitenav').find('[data-clicked]').each(function(id,elem){
		$(elem).removeAttr('data-clicked');
	});
	$(main_tab).attr('data-clicked','yes');



	//Getting current tab value
	var curr_tab 		= $('#curr_main_tab').val();
	var curr_sub_tab	= $('#curr_sub_tab').val();
	//Execute actions which need to be performed before tab change.
	action_before_tab_change(curr_tab,curr_sub_tab,main_tab,sub_tab);

	var redirect_url = redirect_url;
	var flgNc = flgNc;
	var ansHidInsVal = top.chkHidVal("INS");
	var ansHidDemoVal = top.chkHidVal("DEMO");
	//save_medhx_tab();

	if(ansHidInsVal == "yes"){
		var ansAsk = top.askSaveOnFormChange("INS");
		if(ansAsk == true){
			var tempInterval = setInterval(function() {
				var ans = top.chkConfirmSave("","chk");
				if(ans == "yes"){
					clearInterval(tempInterval);top.chkConfirmSave("no","set");
					do_change_main_Selection(main_tab_obj, sub_tab_obj, redirect_url, flgNc,onload);
				}
			}, 10);
		}
		else{
			do_change_main_Selection(main_tab_obj, sub_tab_obj, redirect_url, flgNc,onload);
		}
	}
	else if(ansHidDemoVal == "yes"){
		var ansAsk = top.askSaveOnFormChange("DEMO");
		if(ansAsk == true){
			var tempInterval = setInterval(function() {
				var ans = top.chkConfirmSave("","chk");
				if(ans == "yes"){
					clearInterval(tempInterval);top.chkConfirmSave("no","set");
					do_change_main_Selection(main_tab_obj, sub_tab_obj, redirect_url, flgNc,onload);
				}
			}, 10);
		}
		else{
			do_change_main_Selection(main_tab_obj, sub_tab_obj, redirect_url, flgNc,onload);
		}
	}
	else if( curr_tab == 'Medical_Hx' && curr_sub_tab != "cc_history" && curr_sub_tab != "" && curr_sub_tab != "hms" && top.clinical_prev == 'false' ){
		if( $("#hid_chk_change_data_main",top.document).val() == 'yes' ){
			if( curr_sub_tab != sub_tab ){
				prevTab = curr_sub_tab;
				nextTab = ($(main_tab).attr('id') == 'Medical_Hx') ? sub_tab : '';
				top.fmain.save_form_on_tab_changed(prevTab,nextTab);
				var tempInterval = setInterval(function() {
					var ans = top.chkConfirmSave("","chk");
					if(ans == "yes"){
						clearInterval(tempInterval);
						do_change_main_Selection(main_tab_obj, sub_tab_obj, redirect_url, flgNc,onload);
					}
				}, 10);

			}
			else{
				do_change_main_Selection(main_tab_obj, sub_tab_obj, redirect_url, flgNc,onload);
			}

		}
		else{
			do_change_main_Selection(main_tab_obj, sub_tab_obj, redirect_url, flgNc,onload);
		}

	}
	else{
		do_change_main_Selection(main_tab_obj, sub_tab_obj, redirect_url, flgNc,onload);
	}
}

function do_change_main_Selection(main_tab, sub_tab, redirect_url, flgNc, onload){
	show_loading_image('show','300','Loading...');

	main_tab = (main_tab && typeof(main_tab.id)!="undefined") ? main_tab.id : "";
	if(main_tab==""){ return; }

	var prev_tab = $("#curr_main_tab").val();
	var curr_tab = main_tab;

	if(main_tab!='sch_icon_li'){top.$('#appt_scheduler_status').val('unloaded');}

	//Check Work View Tab and Changes made in chart notes
	if(prev_tab == "Work_View" && curr_tab!="Work_View"  && flgNc != "1"){
		if(typeof top.fmain.chkWVB4Move == 'function' && top.fmain.chkWVB4Move(curr_tab)){
			show_loading_image('hide');
			return;
		}
		if(typeof(onload)=="undefined" || onload==0){
			top.setPtMonitorStatus();
		}
	}

	$("#curr_main_tab").val(curr_tab);

	//Get Destination URL.
	var arr_desti_url = core_get_tab_path(main_tab,sub_tab);
	var new_fmain_url	= arr_desti_url[0];
	var new_sub_url		= arr_desti_url[1];

	if(typeof(redirect_url) != "undefined" && redirect_url != ""){
		new_fmain_url = redirect_url;
	}
	if(new_fmain_url != ''){
		//if(prev_tab!=curr_tab){
		var flg_reload = (prev_tab == "Work_View" && typeof(sub_tab)!="undefined" && (sub_tab=="procedure" || sub_tab=="sx_plan" || sub_tab=="prescription")) ? 0 : 1 ;


		if(flg_reload){$('#fmain').prop('src',new_fmain_url);}

		//}
	}
	if(new_sub_url != ''){
		window.top.popup_win(new_sub_url);
	}

	//Saving main and sub url for future use.
	$('#curr_main_tab').val(main_tab);
	$('#curr_sub_tab').val(sub_tab);
	show_loading_image('hide');

	//Adding class to navbar for header icon management
	//set_nav_class(document.getElementById(main_tab));
}

/***This function returns URL according to Main_tab and/or Sub_tab value***/
function core_get_tab_path(tab,opt2){
	if(typeof(opt2)=='undefined') var opt2 = '';
	var filename = "";
	var filename2 = "";

	switch(tab){
		case "sch_icon_li":		filename = "../scheduler/base_day_scheduler.php?v=2018-12-13";break;
		case "Work_View":
			filename = "../chart_notes/work_view.php";
			if(opt2=='consult'){filename2 = '../chart_notes/template.php';}
			else if(opt2=='procedure'){filename2 = JS_WEB_ROOT_PATH+'/interface/chart_notes/onload_wv.php?elem_action=Procedures';}
			else if(opt2=='sx_plan'){filename2 = '../chart_notes/sx_planning_sheet.php?get_date_format='+top.jquery_date_format;}
			else if(opt2=='contact_lens'){filename2 = '../chart_notes/contact_lens_worksheet_popup.php?mode=newContactLensSheet';}
			else if(opt2=='prescription'){filename2 = '../chart_notes/prescription.php';}
			else if(opt2=='physician_notes'){inter_phy_note();filename="";filename2="";}
			else if(opt2=='operative_note'){inter_operative_note();filename="";filename2="";}
			break;
		case "Tests":			filename = "../tests/index.php?showpage="+opt2;break;
		case "Patient_Info":
		case "Demographics":	filename = "../patient_info/demographics/index.php";break;

		case "Insurance":		filename = "../patient_info/insurance/index.php";break;
		case "Documents":		filename = "../patient_info/consent_forms/index.php?doc_name=signed_consent&doc_collapse=yes";break;
		case "Documents_Lab":	filename = "../chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs&doc_collapse=yes";break;
		/*
		case "PtCnstFrm":		filename = "../patient_info/consent_forms/index.php";break;
		case "PtSrgCnstFrm":	filename = "../patient_info/surgery_consent_forms/index.php";break;
		case "PtConsultLetterView":	filename = "../chart_notes/consult_letter_page.php";break;
		case "PtDocs":			filename = "../chart_notes/scan_docs/pt_docs.php";break;
		*/
		case "PtElig":			filename = "../patient_info/eligibility/index.php";break;
		case "Medical_Hx":		filename = "../Medical_history/index.php?showpage="+opt2;break;
		case "Accounting":		filename = "../accounting/superbill_charges.php";break;
		case "AccountingSB":	filename = "../accounting/superbill_charges.php";break;
		case "AccountingRC":	filename = "../accounting/review_charges.php";break;
		case "AccountingEC":	filename = "../accounting/accounting_view.php";break;
		case "AccountingPC":	filename = "../accounting/pending_charges.php";break;
		case "AccountingPR":	filename = "../accounting/recall_desc_save.php";break;

		case "AccountingRP":	filename = "../accounting/review_payments.php";break;
		case "AccountingEP":	filename = "../accounting/makePayment.php";break;
		case "AccountingPP":	filename = "../accounting/check_in_out_acc.php";break;

		case 'BillingBP':		filename = "../billing/batch_process_list.php";break;
		case 'BillingP':		filename = "../billing/paper_billing.php";break;
		case 'Billing':			filename = "../billing/electronic_billing.php";break;
		case 'BillingE':
				filename = "../billing/electronic_billing.php";
				if(opt2=='CLHReports'){filename2 = '../billing/get_batch_file_report.php';}
				break;
		case 'BillingCP':		filename = "../billing/cap_account.php";break;
		case 'BillingEU':		filename = "../billing/era_file_lists.php";break;
		case 'BillingEPP':		filename = "../billing/era_post_payments.php?send_era_id="+opt2;break;
		case 'BillingEMP':		filename = "../billing/era_manual_payments.php";break;
		case 'BillingCR':		filename = "../billing/denied_payment_correction.php";break;

		case "Optical_Tab":		filename = "../optical/index.php?showpage="+opt2;break;
		//case "Optical":		filename = "../optical/optical_screen.php";break;
		//case "OpticalClLst":	filename = "../optical/cl_order_list.php";break;
		//case "OpticalGlsOL":	filename = "../optical/todays_order_list.php";break;
		//case "OpticalGlsFrm":	filename = "../optical/optical_order_form.php";break;

		//REPORTS
		case "SchedulerReport":        	filename = "../reports/scheduler_report.php?sch_temp_id="+opt2;break;
		case "AnalyticReport":          filename = "../reports/prac_analytics_report.php?sch_temp_id="+opt2;break;
		case "FinancialsReport":        filename = "../reports/financial_index.php?sch_temp_id="+opt2;break;
		case "ComplianceReport":        filename = "../reports/compliance_index.php?sch_temp_id="+opt2;break;
		case "ReportPracticeAnalytics":	filename = "../reports/productivity.php";break;
		case "ReportCPTAnalysis":		filename = "../reports/Procedural.php";break;
		case "ReportYearly":			filename = "../reports/yearlyReports.php";break;
		case "ReportRefPhysician":		filename = "../reports/report_referring_physician.php";break;
		case "ReportNumberOfARTouches":	filename = "../reports/number_of_ar_touches.php";break;
		case "ReportChargeEntryLag":	filename = "../reports/charge_entry_lag_reject_ratio.php";break;
		case "ReportPointOfServiceCollections":	filename = "../reports/point_of_service_collections.php";break;
		//case "CCDIMPORT":				filename = "../reports/ccd/ccd_import.php?op=Existing_Patient";break;
		//case "CCDNP":					filename = "../reports/ccd/ccd_import.php?op=New_Patient";break;
		//case "CCDLI":					filename = "../reports/ccd/lab_import.php";break;
		//case "CCRIMPORT":				filename = "../reports/ccd/ccr_import.php";break;
		case "QRDA_2020":				filename = "../reports/qrda_2020/index.php";break;
		case "QRDA":					filename = "../reports/qrda/index.php";break;
		case "CQMIMPORT_2020":			filename = "../reports/cqm_import_2020/index.php";break;
		case "CQMIMPORT":				filename = "../reports/cqm_import/index.php";break;
		case "CCDEXPORT":				filename = "../reports/ccd/index.php";break;
		case "APIACCESSLOG":			filename = "../reports/api/index.php";break;
		case "APICALLLOG":				filename = "../reports/api/callLog.php";break;
		case "WVLOG":					filename = "../reports/allscripts/index.php";break;
		case "PREVIOUSHCFA":			filename = "../reports/previous_hcfa.php";break;
		case "PREVIOUSUB":			 	filename = "../reports/previous_ub.php";break;
		case "NEWSTATEMENT":			filename = "../reports/new_statement.php";break;
		case "PREVIOUSSTATEMENT":		filename = "../reports/previous_statement.php";break;
		case "STATEKY":					filename = "../reports/index_state_ky.php";break;
		case "STATETN":					filename = "../reports/index_state_tn.php";break;
		case "STATENC":					filename = "../reports/index_state_nc.php";break;
		case "STATEIL":					filename = "../reports/index_state_il.php";break;
		case "STATEPA":					filename = "../reports/index_state_pa.php";break;
		case "STATEASC":				filename = "../reports/index_state_asc.php";break;
		case "SPARCS":					filename = "../reports/index_sparcs.php";break;
		case "PtMonitor":				filename = "../reports/patient_monitor_index.php";break;
		case "DayFaceSheet":			filename = "../reports/day_facesheet_index.php";break;
		case "ApptInfo":			    filename = "../reports/app_info_index.php";break;
		case "Pt_Docs":				    filename = "../reports/pt_docs_index.php";break;
		case "SxPlanSheet":				filename = "../reports/sx_planning_sheet.php";break;
		case "eidstatus":				filename = "../reports/eid_status.php";break;
		case "eidpayments":				filename = "../reports/eid_payments.php";break;
        case "opticalCL":               filename = "../reports/optical/index.php?showpage=contactlens";break;
        case "opticalCLO":              filename = "../reports/optical/index.php?showpage=contactlensorder";break;
        case "opticalG":				filename = "../reports/optical/index.php?showpage=glasses";break;
		case "RECALLFF":				filename = "../reports/recall_fulfillment_index.php";break;
		case "DayAppts":				filename = "../reports/day_appts_index.php";break;
		case "ReminderLists":			filename = "../reports/reminder_lists_index.php";break;
		case "ReminderRecall":			filename = "../reports/reminder_recall_index.php";break;
		case "ConsultLetters":			filename = "../reports/consult_letter_report_index.php";break;
		case "clinicalRpt":             filename = "../reports/clinical/clinical_index.php";break;
		case "auto_finalize_charts_rpt":filename = "../reports/clinical/auto_finalize_charts.php";break;
		case "unbilled_tests_rpt":		filename = "../reports/clinical/unbilled_tests.php";break;
		case "un_superbilled_encounters_rpt":		filename = "../reports/clinical/unsuperbilled_encounters.php";break;
		case "patient_procedures_rpt":	filename = "../reports/clinical/patient_procedures.php";break;
		case "SchedulerReportDef":		filename = "../reports/scheduler_report_default.php";break;
		case "patientCSV":				filename = "../reports/detail_patient_report.php";break;
		case "TFLProof":				filename = "../reports/timelyFillingProof.php";break;
		case "SurgeryAppt":				filename = "../reports/surgery_report.php";break;
		case "rulesRpt":				filename = "../reports/rules_ar_report.php";break;
		case "rtaQryRpt":				filename = "../reports/rta_query_report.php";break;
		case "STATEMENTPAYMENT":		filename = "../reports/statement_payment.php";break;
		case "clinicalProductivity":  	filename = "../reports/clinical_productivity.php";break;
		case "ReportAppt":  			filename = "../reports/report_appt.php";break;
		case "ProvidersReport":  		filename = "../reports/providers_index.php";break;
		case "ProceduresReport":  		filename = "../reports/procedure_index.php";break;
		case "SurveyRpt":  				filename = "../reports/survey_index.php";break;
		case "SavedSchedules":			filename = "../reports/saved_schedules_result.php";break;
		case "ExecutedReports":  		filename = "../reports/executed_report_result.php";break;
		case "pk_interface":	  		filename = "../reports/pk_interface.php";break;
		case "press_ganey":		  		filename = "../reports/press_ganey.php";break;
		case "patient_referral":  		filename = "../reports/patient_referral.php";break;
		case "STATETX":					filename = "../reports/index_state_tx.php";break;
		case "AppointmentSurvey":		filename = "../reports/appointment_survey.php";break;
		case "rpt_pateint_ins_info":	filename = "../reports/patient_insurance_info.php";break;
		case "ReportFinancialData":		filename = "../reports/asheville_financial_index.php";break;
		case "patient_visit_info":		filename = "../reports/patient_visit_info.php";break;
		case "PT_STATUS":				filename = "../reports/pt_status.php";break;
		case "patient_registry_percentage":				filename = "../reports/patient_registry_percentage_index.php";break;
		case "label_count_report":		filename = "../reports/label_count_report.php";break;
		case "no_show_report":			filename = "../reports/no_show_report.php";break;
		case "time_utilization":		filename = "../reports/time_utilization.php";break;
		case "lbl_user_perms_rprt":		filename = "../reports/lbl_user_perms_rprt.php";break;
		case "InstitutionalEncounters":	filename = "../reports/posted_institutional_encounters.php";break;
		case "NEWSTATE":				filename = "../reports/index_new_state.php";break;
		case "monitoring_report":		filename = "../reports/clinical/monitoring_report.php";break;

		//ADMIN
		case "Admin":					filename = "../admin/groups/index.php";break;
		case "AdmGroups":				filename = "../admin/groups/index.php";break;
		case "AdmFacility":				filename = "../admin/facility/index.php";break;
		case "AdmProviders":			filename = "../admin/providers/index.php";break;
		case "AdmRefPhy":				filename = "../admin/ReferringPhysician/index.php";break;
		case "AdmCdsIntervention":		filename = "../admin/cds_intervention/index.php";break;
		case "AdmUpdox":				filename = "../admin/updox/index.php";break;
		case "AdmAllscripts":			filename = "../admin/allscripts/index.php";break;
		case "AdmDSS":					filename = "../admin/dss/index.php";break;
		case "taskRulesManager":		filename = "../admin/taskrules/rule_manager.php";break;
		case "groupPrivileges":			filename = "../admin/groupprivileges/index.php";break;
		case "changePrivileges":		filename = "../admin/groupprivileges/change_prevlgs.php";break;
		case "userMessagesFolder":		filename = "../admin/messages_folder/index.php";break;
		case "loginLogoutHours":		filename = "../admin/office_hours/index.php";break;
		case "AdmErpApi":               filename = "../admin/erp_portal/index.php";break;
		case "AdmVitalInteractions":	filename = "../admin/vital_interactions/index.php";break;
		case "AdmArWorksheetSetting":	filename = "../admin/ar/index.php";break;

		case "AdmProTemp":		filename = "../admin/scheduler_admin/procedure_template/index.php";break;
		case "AdmSchTemp":		filename = "../admin/scheduler_admin/schedule_template/index.php";break;
		case "AdmSchSett":		filename = "../admin/scheduler_admin/setting/index.php";break;
		case "AdmProSch":		filename = "../admin/scheduler_admin/provider_schedule/provider_sch.php";break;
		case "AdmSchRea":		filename = "../admin/scheduler_admin/schedule_reason/index.php";break;
		case "AdmSchStat":		filename = "../admin/scheduler_admin/schedule_status/index.php";break;
		case "AdmAvail":		filename = "../admin/scheduler_admin/available/index.php";break;
		case "AdmChnEvt":		filename = "../admin/scheduler_admin/chainevent/index.php";break;
		case "AdminDocument":	filename = "../admin/documents/index.php?showpage="+opt2;break;
		case "AdminDocumentIns":filename = "../admin/documents/index.php?showpage="+opt2+'&sub=instructions';break;
		case "AdminDocumentPresc":filename = "../admin/console/prescriptions/index.php";break;
		case "AdminDocumentVar":filename = "../admin/documents/variable_help/index.php";break;
		//case "AdmDocCnslts":	filename = "../admin/documents/consults/index.php";break;
		//case "AdmDocEdu":		filename = "../admin/documents/education/index.php";break;
		//case "AdmDocIns":		filename = "../admin/documents/instructions/index.php";break;
		//case "AdmDocOp":		filename = "../admin/documents/opnotes/index.php";break;
		//case "AdmDocRcl":		filename = "../admin/documents/recalls/index.php";break;
		//case "AdmDocPtDoc":		filename = "../admin/documents/pt_docs/index.php";break;
		//case "AdmDocStm":		filename = "../admin/documents/statements/index.php";break;
		case "AdmDocStags":		filename = "../admin/documents/smart_tags/index.php";break;
		case "AdmDocLog":		filename = "../admin/documents/logos/index.php";break;
		//case "AdmDocPnl":		filename = "../admin/documents/panels/index.php";break;
		case "AdmRA":			filename = "../admin/room_assigned/index.php";break;
		case "AdmVS":			filename = "../admin/vs/index.php";break;
		case "AdmImM":			filename = "../admin/imedic_monitor/index.php";break;
		case "AdImMC":			filename = "../admin/imedic_monitor/columns_index.php";break;
		case "AdmIMz":			filename = "../admin/immunization/index.php";break;
		case "admIopDef":		filename = "../admin/chart_notes/iop_def.php";break;
		case "AdmSM":			filename = "../admin/set_margin/index.php";break;

		case "AdmBilPol":		filename = "../admin/billing/policies/index.php";break;
		case "AdmBilFtbl":		filename = "../admin/billing/cpt_fee/index.php";break;
		case "AdmBilCpt":		filename = "../admin/billing/cpt_fee_tbl/index.php";break;
		case "AdmBilRevCode":	filename = "../admin/billing/revenue_code/revenue_code_list.php";break;
		case "AdmBilProCode":	filename = "../admin/billing/revenue_code/proc_code_list.php";break;
		case "AdmBilWrtCode":	filename = "../admin/billing/revenue_code/write_code_list.php";break;
		case "AdmBilAdjCode":	filename = "../admin/billing/revenue_code/adj_code_list.php";break;
		case "AdmBilDisCode":	filename = "../admin/billing/revenue_code/discount_code_list.php";break;
		case "AdmBilReaCode":	filename = "../admin/billing/revenue_code/reason_code_list.php";break;
		case "AdmBilPayMethod":	filename = "../admin/billing/revenue_code/payment_methods_list.php";break;
		case "AdmBilDxCode":	filename = "../admin/billing/diagnosis_code/index.php";break;
		case "AdmBilMod":		filename = "../admin/billing/modifiers/index.php";break;
		case "AdmBilPos":		filename = "../admin/billing/pos/index.php";break;
		case "AdmBilPosF":		filename = "../admin/billing/pos_facility/index.php";break;
        case "AdmBilPosFG":     filename = "../admin/pos_facility_group/index.php";break;
		case "AdmBilTos":		filename = "../admin/billing/tos/index.php";break;
		case "AdmBilDept":		filename = "../admin/billing/Department/index.php";break;
		case "AdmBilMsg":		filename = "../admin/billing/statements_Messages/index.php";break;
		case "AdmBilCases":		filename = "../admin/billing/add_insurance_case/index.php";break;
		case "AdmBilIns":		filename = "../admin/billing/add_insurance/index.php";break;
		case "AdmBilInsGrp":	filename = "../admin/billing/ins_group/index.php";break;
		case "AdmBilPoe":		filename = "../admin/billing/poe/index.php";break;
		case "AdmBilStats":		filename = "../admin/billing/account_status/index.php";break;
		case "AdmClaimStats":	filename = "../admin/billing/claim_status/index.php";break;
		case "AdmBilPhr":		filename = "../admin/billing/phrases/index.php";break;
		case "AdmBilIcd10":		filename = "../admin/billing/icd10/index.php";break;
		case "AdmSCP":			filename = "../admin/alert/listAlerts.php";break;
		case "managePOS":		filename = "../admin/pos/pos_merchant_index.php";break;
		case "AdmBilSageSFTP":	filename = "../admin/billing/sage_sftp/index.php";break;
		
		case "AdmCnslPhr":		filename = "../admin/console/managed_phrases/index.php";break;
		case "AdmCnslAP":		filename = "../admin/console/appolicy/index.php";break;
		case "AdmCNTemp":		filename = "../admin/chart_notes/chart_template.php";break;
		case "AdmCNWNL":		filename = "../admin/chart_notes/wnl.php";break;
		case "AdmCNSoC":		filename = "../admin/chart_notes/soc.php";break;
		case "AdmCNDrawing":	filename = "../admin/chart_notes/drawicon.php";break;
		//case "AdmCNBtx":		filename = "../admin/chart_notes/index.php";break;
		case "AdmCNSCPR":		filename = "../admin/chart_notes/SCPReasons.php";break;
		case "AdmCNSpec":		filename = "../admin/chart_notes/speciality.php";break;
		case "closePt":			filename = "../landing/index.php";break;
		case "AdminCFU":		filename = "../admin/chart_notes/fu_index.php";break;
		case "AdminCVST":		filename = "../admin/chart_notes/visit.php";break;
		case "AdminCTesting":	filename = "../admin/chart_notes/testing.php";break;
		case "AdmCNBtx":		filename = "../admin/chart_notes/botox.php";break;
		case "AdmCNTT":			filename = "../admin/chart_notes/test_templates.php";break;
		case "AdmCNTDiagnosis":	filename = "../admin/chart_notes/test_diagnosis.php";break;
		case "AdmCNOD":			filename = "../admin/chart_notes/chart_admn_ophth_drops.php";break;
		case "AdmClinical":		filename = "../admin/chart_notes/chart_clinical.php";break;
		case "AdmHPI":			filename = "../admin/chart_notes/chart_hpi_ext.php";break;
		case "AdmCNTST":		filename = "../admin/chart_notes/cn_tests.php";break;
		case "AdmCNLR":			filename = "../admin/chart_notes/lab_display.php";break;
		case "AdmLskOpt":		filename = "../admin/chart_notes/lasik_opts.php";break;
		case "AdmLensUsed":		filename = "../admin/chart_notes/lens_used.php";break;
		case "AdmCNPCL":		filename = "../admin/chart_notes/pt_chart_locked.php";break;
		case "AdmAP":			filename = "../admin/audit/audit_policies.php";break;
		case "AdmERP":			filename = "../admin/eRx_preferences/index.php";break;
		case "AdmIPSQ":			filename = "../admin/iportal/security_questions.php";break;
		case "AdmIPPP":			filename = "../admin/iportal/print_preferences.php";break;
		case "AdmIPS":			filename = "../admin/iportal/survey.php";break;
		case "AdmIAR":			filename = "../admin/iportal/auto_responder.php";break;
		case "AdmIPPI":			filename = "../admin/iportal/preferred_images.php";break;
		case "AdmIPSS":			filename = "../admin/iportal/survey_conf.php";break;
		case "AdmIPIS":			filename = "../admin/iportal/index.php";break;
		case "AdmO":			filename = "../admin/order_sets/Order/orderList.php";break;
		case "AdmOS":			filename = "../admin/order_sets/Orderset/index.php";break;
		case "AdmOT":			filename = "../admin/order_sets/order_templates/index.php?showpage="+opt2;break;
		case "AdmMFCF":			filename = "../admin/manage_fields/index_custom_fields.php";break;
		case "AdmMFOC":			filename = "../admin/manage_fields/index_med_hx.php?MedTab=Ocular";break;
		case "AdmMFGH":			filename = "../admin/manage_fields/index_med_hx.php?MedTab=General";break;
		case "AdmMFPF":			filename = "../admin/manage_fields/index_prac_fields.php";break;
		case "AdmMFTF":			filename = "../admin/manage_fields/index_tech_fields.php";break;
		case "AdmOPV":			filename = "../admin/optical/index_vendor.php";break;
		case "AdmOPF":			filename = "../admin/optical/index_frames.php";break;
		case "AdmOPL":			filename = "../admin/optical/index_lenses.php";break;
		case "AdmOPC":			filename = "../admin/optical/index_color.php";break;
		case "AdmOPLC":			filename = "../admin/optical/index_lens_code.php";break;
		case "AdmOPCLC":		filename = "../admin/optical/index_cl_charges.php";break;
		case "AdmOPM":			filename = "../admin/optical/index_make.php";break;
		case "AdmCnslPhr":		filename = "../admin/console/managed_phrases/index.php";break;
		case "AdmCnslRXT":		filename = "../admin/console/managed_prescription/index.php";break;
		case "AdmCnslEP":		filename = "../admin/console/managed_predefine_epost/index.php";break;
		case "AdmCnslMED":		filename = "../admin/console/Medication_type_ahead/index.php";break;
		case "AdmMedProcSet":	filename = "../admin/chart_notes/med_proc_dis_settings.php";break;
		case "AdmCnslAL":		filename = "../admin/console/admin_allergies/index.php";break;
		case "AdmCnslZC":		filename = "../admin/console/zip_codes/index.php";break;
		case "AdmCnsDenial":	filename = "../admin/billing/denial_management/index.php";break;
		case "AdmBilCcdaSFTP":	filename = "../admin/billing/ccda_sftp/index.php";break;
		
		case "AdmEraRules":		filename = "../admin/billing/era_rules/index.php";break;
		case "AdmCnslFD":		filename = "../admin/console/manage_folder/index.php";break;
		case "AdmCnslCPTGP":	filename = "../admin/console/cpt_groups/index.php";break;
		case "AdmCnslREFGP":	filename = "../admin/console/ref_groups/index.php";break;
		case "AdmCnslFACGP":	filename = "../admin/console/fac_groups/index.php";break;
		case "AdmCnslPGP":		filename = "../admin/console/prov_group/index.php";break;
		case "AdmCnslSX":		filename = "../admin/console/sx/index.php";break;
		case "AdmCnslSXP":		filename = "../admin/console/sx/sx_planning.php";break;
		case "AdmCnslHAU":		filename = "../admin/console/heard_about_us/index.php";break;
		case "AdmCnslPRO":		filename = "../admin/console/procedures/index.php";break;
		case "AdmCnslPAT":		filename = "../admin/console/pre_auth_templates/index.php";break;
		case "AdmCnslAPP":		filename = "../admin/console/appolicy/index.php";break;
		case "AdmiASCLink":		filename = "../admin/iolink_tabs/iOLinkSettings.php";break;
		case "AdmSCF":			filename = "../admin/iolink_tabs/surgery_consent_form.php";break;
		//REPORTS
		case "AdmRptSch":		filename = "../admin/reports/scheduler.php";break;
		case "AdmRptPra":		filename = "../admin/reports/prac_analytics.php";break;
		case "AdmRptFin":		filename = "../admin/reports/financials.php";break;
		//case "AdmRptCli":		filename = "../admin/reports/clinical.php";break;
		//case "AdmRptCom":		filename = "../admin/reports/compliance.php";break;
		//case "AdmRptOpt":		filename = "../admin/reports/optical.php";break;
		//case "AdmRptSta":		filename = "../admin/reports/statements.php";break;
		//case "AdmRptCol":		filename = "../admin/reports/collection.php";break;

        //IOLs(Lenses)
        case "AdmManageLenses": filename = "../admin/iols_lenses/index.php";break;
        case "AdmLCALC":		filename = "../admin/iols_lenses/lens_calc.php";break;
        case "AdmIOLUser":		filename = "../admin/iols_lenses/assign_iol_user.php";break;
        case "AdmLensPhy":      filename = "../admin/iols_lenses/providersDefined.php?pId="+opt2;break;
}
	var final = new Array();
	final[0]	= filename;
	final[1]	= filename2;
	return final;
}

/***This function opens popup; custom function to control popups if need to close all at once***/
var chldWin = '';
var chldWinCheckIn = '';
function popup_win(url,features,feat2){
	//File name fetched to put in window_name.
	var n = url.substring(url.lastIndexOf('/')+1);
	temp_n= n.split('.');
	n	  = temp_n[0];
	sc_wd=(screen.availWidth-100);
	sc_hg=(screen.availHeight-100);
	if(typeof(feat2)!="undefined" && feat2!=""){
		if(typeof(features)!="undefined" && features!=""){ n=features; }
		features = feat2;
	}
	if(typeof(features)=='undefined' || features=='' || features==null){
		features = "location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width="+sc_wd+",height="+sc_hg
	}
	//If window is not opened, then opening it.
	if(n.toLowerCase()=='fileprocessview'){
		n = n+Math.random();
		arr_opened_popups[n] = window.open(url,n,features);
	}else if(!arr_opened_popups[n] || !(arr_opened_popups[n].open) || (arr_opened_popups[n].closed == true)){
		arr_opened_popups[n] = window.open(url,n,features);
		if(n=='newPatientWindow'){
			if(typeof window.top.chk_window_opened =="undefined") {
				window.top.chk_window_opened = new Array();
			}
			window.top.chk_window_opened["chkinwin"]  = true;
		}
	}
	//START CODE FOR SITE CARE PLAN
	if(n=='index') {
		chldWin	= arr_opened_popups[n];
	}
	if(n=='new_patient_info_popup_new' || n=='newPatientWindow') {//CHECK-IN SCREEN OR NEW PATIENT POPUP
		chldWinCheckIn	= arr_opened_popups[n];
	}
	//END CODE FOR SITE CARE PLAN
	arr_opened_popups[n].focus();
}

function callChildWin(r,divSiteCareScanDoc,siteCareId){
	if((chldWin != null) && (chldWin.closed == false)) {
		chldWin.image_DIV(r,divSiteCareScanDoc,siteCareId);
	}
}
function callChildWinCheckIn(){
	if((chldWinCheckIn != null) && (chldWinCheckIn.closed == false)) {
		chldWinCheckIn.get_action('submit_form');
	}
}
/****THIS IS A MASTER AJAX FUNCTION TO ROUTE ALL AJAX (POSSIBLE) AJAX CALLS****/
function master_ajax_tunnel(url,callBack,post_params_query_string,return_data_type,call_type){
	/***************************************************/
	//url 						= server side (php) scrip url, it may include get variables (i.e. script_url.php?param1=value1&param2=value2)
	//callBack					= function to execute on call finish.
	//post_params_query_string	= if given, POST data string will be used, otherwise empty sent as post.
	//return_data_type			= the data type of returned data (i.e. xml, json,text etc.)
	//call_type					= regular or optional (regular call be executed immediately; optional call be executed only if tunnel is free)
	/***************************************************/

	//SETTING DEFAULT BEHAVIOR/VALUES
	if(typeof(url)=='undefined' || url=='' || url==null){top.fAlert('No URL provided to execute.');return false;}
	if(typeof(callBack)=='undefined') 					callBack 					= '';
	if(typeof(post_params_query_string)=='undefined') 	post_params_query_string 	= '';
	if(typeof(return_data_type)=='undefined' || return_data_type=='') 			return_data_type 			= 'text';
	if(typeof(call_type)=='undefined') 					call_type 					= 'regular';

	//CHECKING TUNNEL STATUS AND RESCHEDULING IF FOUND BUSY
	var ajax_tunnel_status = $('#ajax_tunnel_status').val();
	if(call_type=='optional' && ajax_tunnel_status=='busy'){
		setTimeout(function(){top.master_ajax_tunnel(url,callBack,post_params_query_string,return_data_type,call_type)},500);
		return;
	}

	//MAIN AJAX CALL
	show_loading_image('show','','Loading...');
	$.ajax({
		url			: url,
		type		: "POST",
		dataType	: return_data_type,
		data		: post_params_query_string,
		beforeSend: function() {
			$('#ajax_tunnel_status').val('busy');
        	//set LOADING ANIMATION PROGRAM
    	},
		success		: function(r){
			show_loading_image('hide');
			$('#ajax_tunnel_status').val('free');
			if(callBack != '') callBack(r,false);
		}
	});


}

function update_iconbar(r,ajax){
	var nickName = "";
	var phoneticName = "";
	var sch_loaded = false;
	if(typeof(top.$('#patient.srch_pt_text').get(0))!='undefined')	top.$('#patient.srch_pt_text').val('');
	//hide icon bar for scheduler
	if($('#appt_scheduler_status').val()=='loaded'){
		EraseIconbarPtInfo();
		top.$('#first_toolbar').hide();
		//reset fmain frame hight
		top.init_display();
		sch_loaded = true;
		//return;
	}
	else
	{
		top.$('#first_toolbar').show();
		EraseIconbarPtInfo();
		//reset fmain frame hight
		top.init_display();
	}

	if(typeof(ajax)=='undefined'){top.master_ajax_tunnel('ajax_handler.php?task=get_icon_bar_status',top.update_iconbar,'','json','optional');}
	else if(typeof(r)!='undefined'){//console.log(r);
		/*--PT.NAME ID, DOD, ERX, INS.FLAGS--*/ //name:~:Walker, Robert L. - 21257::~::eRx:~:e/Rx::~::DOD:~:::~::terFlag:~:::~::secFlag:~:::~::priFlag:~:::~::
		strPtName = strPtId = pt_facility_name = erx_Val = pt_image = pt_acc_status = '';
		$("li#erx",top.document).hide();
		if(r.PtNameIdeRx && r.PtNameIdeRx!='' && !sch_loaded){
			arr_ptNameDetails = r.PtNameIdeRx.split('::~::');
			for(i=0; i<arr_ptNameDetails.length;i++){
				ARRcurObj1 = arr_ptNameDetails[i].split(':~:');
				if(ARRcurObj1[0]=='name'){
					Temp_strPtName = ARRcurObj1[1]; tempArrPtname = Temp_strPtName.split(' - ');
					strPtId = tempArrPtname.pop();
					strPtName = tempArrPtname.join(' - ').trim();
				}else if(ARRcurObj1[0]=='eRx' && ARRcurObj1[1] != ''){
					erx_Val = ' <span style="color:#FF0;" class="pull-right">'+ARRcurObj1[1]+'&nbsp;&nbsp;&nbsp;</span>';
					$("li#erx",top.document).show();
				}else if(ARRcurObj1[0]=='DOD' && ARRcurObj1[1] != '' && ARRcurObj1[1] != '00-00-0000'){
					strPtName += ' DOD: '+ARRcurObj1[1];
				}else if(ARRcurObj1[0]=='Default_Facility' && ARRcurObj1[1] != ''){
					pt_facility_name = '<br />'+ARRcurObj1[1]+'';
				}else if(ARRcurObj1[0]=='pt_image' && ARRcurObj1[1] != ''){
					pt_image = ARRcurObj1[1];
				}else if(ARRcurObj1[0]=='pt_image_width' && ARRcurObj1[1] != ''){
					pt_image_width = ARRcurObj1[1];
				}else if(ARRcurObj1[0]=='pt_image_height' && ARRcurObj1[1] != ''){
					pt_image_height = ARRcurObj1[1];
				}else if(ARRcurObj1[0]=='pt_acc_status' && ARRcurObj1[1] != '' ){
					if( ARRcurObj1[1].toLowerCase() != 'active' ) {
						pt_acc_status = '<img onclick="top.get_set_pat_acc_status();" src="'+top.JS_WEB_ROOT_PATH+'/library/images/flag_account_status.png" class="pointer" style="float: right; background: white;" />';
					}
				}else if(ARRcurObj1[0]=='pri_ref_flag'){
					if(ARRcurObj1[1] != '') $("li#ref_pri",top.document).removeClass('hidden').find('i').removeClass('text-orange text-green text-red').addClass(ARRcurObj1[1]);
					else $("li#ref_pri").addClass('hidden').find('i').removeClass('text-orange text-green text-red');
				}
				else if(ARRcurObj1[0]=='sec_ref_flag'){
					if(ARRcurObj1[1] != '') $("li#ref_sec",top.document).removeClass('hidden').find('i').removeClass('text-orange text-green text-red').addClass(ARRcurObj1[1]);
					else $("li#ref_sec").addClass('hidden').find('i').removeClass('text-orange text-green text-red');
				}
				else if(ARRcurObj1[0]=='ter_ref_flag'){
					if(ARRcurObj1[1] != '') $("li#ref_ter",top.document).removeClass('hidden').find('i').removeClass('text-orange text-green text-red').addClass(ARRcurObj1[1]);
					else $("li#ref_ter").addClass('hidden').find('i').removeClass('text-orange text-green text-red');
				}else if(ARRcurObj1[0]=='nick_name'){
					if(ARRcurObj1[1].trim().length > 0){
						nickName = ARRcurObj1[1].trim();
					}else{
						nickName = "";
					}
				}else if(ARRcurObj1[0]=='phonetic_name'){
					if(ARRcurObj1[1].trim().length > 0){
						phoneticName = ARRcurObj1[1].trim();
					}else{
						phoneticName = "";
					}
				}else if(ARRcurObj1[0]=='language'){
					if(ARRcurObj1[1].trim().length > 0){
						language = ARRcurObj1[1].trim();
					}else{
						language = "";
                    }
                }
            }
		}

		//RECENT SEARCH UPDATE
		if(r.recent_search && r.recent_search != ''){
			top.$('ul#main_search_dd').html(r.recent_search);
			top.main_search_dd_behavior();
		}

		if(r.PtNameIdeRx && r.PtNameIdeRx!='' && sch_loaded){
			arr_ptNameDetails = r.PtNameIdeRx.split('::~::');
			for(i=0; i<arr_ptNameDetails.length;i++) {
				ARRcurObj1 = arr_ptNameDetails[i].split(':~:');
				if(ARRcurObj1[0]=='name'){
					Temp_strPtName = ARRcurObj1[1]; tempArrPtname = Temp_strPtName.split(' - ');
					strPtName = tempArrPtname[0];
					strPtId = tempArrPtname[1];
					break;
				}
			}
			if( strPtId ) $('#patient_id').val(strPtId);
		}

		//IF NO PATIENT ID FOUND, HIDE ALL ICONS AND PATIENT NAME ETC
		if(!sch_loaded){
			if(strPtId==''){
				EraseIconbarPtInfo();
				return;
			}else if(strPtId!=''){
				$('.nopt_noshow').show();
				$('.nopt_show').hide();
			}

			//PATIENT NAME DETAILS            James Cataract <span>68159(Toms River) e/Rx </span>
			$('#div_pt_name h2').html(strPtName+' - '+strPtId+pt_facility_name+erx_Val);
			if(nickName.length > 0 || phoneticName.length > 0  || language.length > 0){
				var nickAndPhoneticName = "";
				if(nickName.length > 0 && phoneticName.length > 0){
					nickAndPhoneticName = "Nick Name: " + nickName + "<br />" + "Phonetic Name: " + phoneticName;
				}else if(nickName.length > 0 && phoneticName.length <= 0){
					nickAndPhoneticName = "Nick Name: " + nickName;
				}else if(nickName.length <= 0 && phoneticName.length > 0){
					nickAndPhoneticName = "Phonetic Name: " + phoneticName;
				}
				if(language.length > 0){
					nickAndPhoneticName = (nickAndPhoneticName!="") ? nickAndPhoneticName+"<br />Language: " + language:"Language: " + language ;
				}
				$('#div_pt_name h2').attr("data-toggle", "tooltip");
				$('#div_pt_name h2').attr("data-html", "true");
				$('#div_pt_name h2').attr("data-placement", "bottom");
				$('#div_pt_name h2').attr("data-original-title", nickAndPhoneticName);
				$('[data-toggle="tooltip"]').tooltip('enable');
				$('[data-toggle="tooltip"]').tooltip();
			}else{
				$('#div_pt_name h2').attr("data-original-title", '');
			}
			$("li.loguser #patAccStatus",top.document).html(pt_acc_status);
			$('#patient_id').val(strPtId);
			if(pt_image!= '') {
				var ptImgObj = $('#div_pt_name figure img');
				ptImgObj.prop('src',top.JS_WEB_ROOT_PATH + '/data/' + practice_dir +pt_image);
				if(typeof(pt_image_width)!="undefined"&&typeof(pt_image_height)!="undefined"){
				ptImgObj.prop('width',pt_image_width).prop('height',pt_image_height);
				if( pt_image_height < 40 ){
					$_margin_top	= parseInt((40 - pt_image_height)  / 2);
					ptImgObj.css('margin-top',$_margin_top + 'px')
				}
				}
			}
			else $('#div_pt_name figure img').prop('src','../../library/images/username.png');
			//PATIENT SPECIFIC ALERTS

			if(r.ptSpecificAlert && parseInt(r.ptSpecificAlert) != 'NaN' &&  parseInt(r.ptSpecificAlert)>0){
				$('.stflwicon').find('.ptalerts_icon1').parent('li').find('figure').text(r.ptSpecificAlert).show();
				$('.stflwicon').find('.ptalerts_icon1').addClass('active');
			}else{
				$('.stflwicon').find('.ptalerts_icon1').parent('li').find('figure').text(r.ptSpecificAlert).show();
				$('.stflwicon').find('.ptalerts_icon1').removeClass('active');
			}

			//Patient Portal Registration
			if(r.ptPortal && r.ptPortal === true){
				var liObj = $('.stflwicon').find('li.ptcont.portal');

				if(liObj.length){
					if(liObj.hasClass('hide') == true) liObj.removeClass('hide');
				}
			}else{
				if($('.stflwicon').find('li.ptcont.portal').hasClass('hide') == false) $('.stflwicon').find('li.ptcont.portal').addClass('hide');
			}

			/*--USER'S MOD--index (13)*/
			if(r.MODtext && r.MODtext!=''){
				if($('#fac_mod_text').hasClass('mod_icon')){
					$('#fac_mod_text').removeClass('mod_icon');
					$('#fac_mod_text').addClass('mod_icon_active');
				}

				$('#div_fac_mod a#fac_mod_text').attr('data-content',r.MODtext);
				if($('#div_fac_mod').hasClass('hide') === true){
					$('#div_fac_mod').removeClass('hide');
				}

				if($('#div_fac_mod').hasClass('show') === false){
					$('#div_fac_mod').addClass('show');
				}
			}else{
				if($('#fac_mod_text').hasClass('mod_icon_active')){
					$('#fac_mod_text').removeClass('mod_icon_active');
					$('#fac_mod_text').addClass('mod_icon');
				}
				$('#div_fac_mod a#fac_mod_text').attr('data-content','No Message');
				if($('#div_fac_mod').hasClass('hide') === false){
					$('#div_fac_mod').addClass('hide');
				}

				if($('#div_fac_mod').hasClass('show') === true){
					$('#div_fac_mod').removeClass('show');
				}
			}


			if( $('#curr_main_tab').val() == 'Work_View' ) {
				$("li#patient_communication",top.document).hide();
			} else {
				$("li#patient_communication",top.document).show();
			}

			//Adjusting Iconbar according to selected tab.
			set_nav_class(document.getElementById($('#curr_main_tab').val()));
			top.init_display();
		}
	}
}

function EraseIconbarPtInfo(){
	try{
		$('#div_pt_name figure img').prop('src','../../library/images/username.png');
		$('#div_pt_name h2').html('');
		$('#mainnav [data-toggle="tooltip"]').tooltip('disable');
		$('#patient_id').val('');
		$('.nopt_noshow').hide();
		$('.nopt_show').show();
		$("li.loguser #patAccStatus",top.document).html('');
	}catch(e){
		//no alert.
	}
}

function main_search_dd_behavior(){
	$('#main_search_dd li a.noclose').click(function(){$('.dropdown-submenu > .dropdown-menu').toggle(); return false;});
	$('#main_search_dd li a').not('#main_search_dd li a.noclose').click(function(){
		var fv = $(this).text();
		var pt_id = $(this).attr('pt_id');
		if(typeof(pt_id)=='undefined'){
			if($('.ptsrchbut').is(':visible') === true){ //If Pt search is called from popover
				$('.ptsrchbut').next('.popover').find('form').find('input#findBy').attr('value',fv);
			}else{
				$('#findBy').val(fv).attr('title',fv);
			}
		}
		else{
			if($('.ptsrchbut').is(':visible') === true){ //If Pt search is called from popover
				top.document.find_patient.find_patient.patient.value=pt_id;
				top.document.find_patient.find_patient.findBy.value='';
				top.document.find_patient.find_patient.submit();
			}else{

				//Check Work View and chart changes --
				if($("#curr_main_tab").val() == "Work_View"){
					if(typeof top.fmain.chkWVB4Move == "function" &&
						top.fmain.chkWVB4Move("Search",pt_id,'')){
						return false;
					}
					//
					top.setPtMonitorStatus();
				}
				//Check Work View and chart changes --

				document.find_patient.patient.value=pt_id;
				$('#findBy').val('');
				document.find_patient.submit();
			}
		}
		$('.dropdown-submenu > .dropdown-menu').css('display','none');
	});
}

/****CHECK BEFORE POSTING PATIENT SEARCH PARAMS****/
function validSearch(){

	//Check Work View and chart changes --
	if($("#curr_main_tab").val() == "Work_View"){
		if(typeof top.fmain.chkWVB4Move == "function" &&
			top.fmain.chkWVB4Move("Search",document.find_patient.patient.value,document.find_patient.findBy.value)){

			return false;
		}
		//
		top.setPtMonitorStatus();
	}
	//Check Work View and chart changes --

	if(top.document.find_patient.patient.value=='') return false;
}

/****Function to perform action to load a patient***/
function load_patient(pid, todo, bgpriv, rp_alert){
	/*****TEMPORARY SKIPPING TODO FUNCTIONALITY******/
	 todo = '';

	var lut = top.logged_user_type;
	if(pid != ""){
		$('#patient_id').val(pid);
		if(rp_alert == "y"){
			top.core_restricted_prov_alert(pid, bgpriv);
			return false;
		}else if(todo != "" && lut != "" &&  lut != 1 && top.logged_user_type !=3){
			top.document.getElementById("findBy").value = pid;
			top.core_to_do_alert(pid);
			return false;
		}else{
			var curr_tab = top.document.getElementById("curr_main_tab").value;
			var redirect_url = top.core_get_tab_path(curr_tab);
			top.core_set_pt_session(top.fmain, pid, redirect_url[0]);
			top.document.getElementById("findBy").value = "Active";
		}
	}
}
function follow_main_att_phy(user_id)
{
	top.show_loading_image('show', '', 'Processing. Please hold a moment...');
	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/core/ajax_handler.php?task=set_res_fellow_sess&user_id='+user_id,
		type : 'POST',
		complete:function(resp){
			user_resp = resp.responseText; user_resp_arr = user_resp.split("||"); pid = '';
			if(user_resp_arr.length > 1){rf_follow_name = user_resp_arr[0]; pid = user_resp_arr[1];}
			else{rf_follow_name = user_resp_arr[0];}
			top.$('#rf_foll_name').html(rf_follow_name);
			top.$('#rf_foll_name').attr("title", rf_follow_name);
			//$("#tl_psc").show();
			if($.trim(pid) != ""){
				window.fmain.location.href = top.JS_WEB_ROOT_PATH+"/interface/core/set_session.php?set_pid="+pid;//+"&rd2=../chart_notes/main_page.php";
			}
			//reload current window to load physicin specific data
			window.location.reload();
		}
	});
}
//to set patient session
function core_set_pt_session(mydoc, pid, redirect_url,server_id,consentSubFolderId){
	var a = "";
	var b = "";
	var res = redirect_url.match(/doc_name/gi);
	if( !res ) top.close_popwin();
	a = top.JS_WEB_ROOT_PATH+"/interface/core/set_session.php?set_pid="+pid+"&rd2="+encodeURI(redirect_url);
	if(res){ a = redirect_url+ "&fromDocTab=fromDocTab"; }
	if( typeof top.patient_pop_up !== 'undefined') {top.patient_pop_up = [];}
	//c = a+b;
	$('#fmain').prop('src',a);
	top.scanOpenWindow(consentSubFolderId);
}

//TO LOAD PATIENT THEN OPEN PARTICULAR PAGE OR MODULE
function LoadPtThenModule(ptid,moduleHandlerURL){
    //To check restrict access of patient before load
    $.when(check_for_break_glass_restriction(ptid)).done(function(response){
        window.top.removeMessi();
        if(response.rp_alert=='y') {
            var patId=response.patId;
            var bgPriv=response.bgPriv;
            var rp_alert=response.rp_alert;
            top.core_restricted_prov_alert(patId, bgPriv, '');
        }else{
            window.top.focus();
            rand = Math.round(Math.random()*555555);
            window.top.core_set_pt_session(window.top.fmain, ptid, moduleHandlerURL+'&uniqueurl='+rand);
        }
    });
}

//check the restrict access and break glass functionality
function check_for_break_glass_restriction(ptid) {
    if(!ptid)return false;
    return $.ajax({
        type: "POST",
        url:top.JS_WEB_ROOT_PATH+'/interface/core/ajax_handler.php?task=check_restrict_access',
        dataType:'JSON',
        data: {ptid:ptid},
        success: function(response){ }
	});
}


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
            if(window.top.$('#curr_main_tab').val() == "Work_View"){
                if(window.top.$("#appt_scheduler_status").val() != "loaded"){
                    //close pop ups
                    if(window.top.fmain && typeof(window.top.fmain.funClosePopUpExe)!="undefined"){ window.top.fmain.funClosePopUpExe(0); }
                }
            }else{ window.top.$("#curr_main_tab").val("Work_View"); $("#Work_View").parent().parent().find("li").removeClass("active"); $("#Work_View").parent().addClass("active"); }
            load_patient(ptid);
        }
    });
}

//FUNCTION TO CLEAN PATIENT SESSION
function clean_patient_session(mode,ajax, chkwv){
	if(typeof(chkwv)=="undefined"){
		//check if current tab is work view, then close all open windows first
		if(window.top.$('#curr_main_tab').val() == "Work_View"){
			if(window.top.$("#appt_scheduler_status").val() != "loaded"){
				if(window.top.fmain && typeof(window.top.fmain.chkWVB4Close)!="undefined"){ if(window.top.fmain.chkWVB4Close()){  return; } }
			}
		}
	}
	if( typeof top.patient_pop_up !== 'undefined') {top.patient_pop_up = [];}
	top.$(".elchart").css("display","none"); //,.elacc,.eldemo
	if(typeof(ajax)=='undefined'){top.master_ajax_tunnel('ajax_handler.php?task=clean_patient_session',top.clean_patient_session);}
	else{
		/*if(mode){
			if(mode == "scheduler"){
				top.fmain.close_patient_info();
			}
		}*/
		if($('#appt_scheduler_status').val()!='loaded'){
			$('#first_toolbar').data('calling','close_pt');
			var default_url = top.core_get_tab_path('closePt');
			top.$('#fmain').prop('src',default_url[0]);
		}else{
			top.update_iconbar();
			//fucntion to reload scheduler pt info
			top.fmain.close_patient_info();
		}

		top.close_popwin();

	}
}

/****FUNCTION SHOW ALERT FOR RESTRICTED PROVIDER******/
function core_restricted_prov_alert(pid, bgPriv, showAlert, consentSubFolderId,eid){
	var msg = "<b>You are not authorized to access this patient's account.</b>";
	if(bgPriv == "y"){
		msg += "<br/><br/>If this is an emergency and you need to access the patient chart. Please fill the following to \"Break Glass\":<br><br><form name=\"frmRestrictedUserAlerts\"><div><span><B>Reason Code:</B></span><span><select id==\"rp_reason_code\" name=\"rp_reason_code\" class=\"input_text_10 form-control\" style=\"width:200px\"><option value=\"\"></option>"+$("#reason_code_options").html()+"</select></span></div><div><span><B>Comments:</B></span><span><textarea name=\"rp_reason_comments\" id=\"rp_reason_comments\" rows=\"2\" style=\"width:100%\" class=\"input_text_10 form-control\"></textarea></span></div></form><div id=\"frmRestrictedUserAlerts_err\" class=\"warning\" style=\"height:20px\"></div><div style=\"text-align:center;\"><input type=\"button\" onclick=\"top.core_break_glass_access('"+pid+"', '"+showAlert+"', '"+consentSubFolderId+"','"+eid+"')\" value=\"Break Glass\" class=\"btn btn-success\">    <input type=\"button\" onclick=\"top.removeMessi()\" value=\"Cancel\" class=\"btn btn-danger\"></div>";
		top.fancyModal(msg,'Restricted Access',"400");
		//top.fancyAlert(msg,'Restricted Access', '', top.document.getElementById("divCommonAlertMsg"), 'Break Glass', 'Cancel', "top.core_break_glass_access('"+pid+"', '"+showAlert+"', '"+consentSubFolderId+"')", "closeDialog()", true, 10, 350, "", "no", false, "", "", false);
	//	top.document.getElementById("divCommonAlertMsg").style.display = "block";
	}else{
		top.fAlert(msg);
		return false;
	}
}

/****Break glass Alert function*****/
function core_break_glass_access(pid, showAlert, consentSubFolderId,eid){//to save restricted provider alert
	var ofrm = top.document.frmRestrictedUserAlerts;
	var scheduler_loaded = document.getElementById("appt_scheduler_status").value;
	if(ofrm.rp_reason_code.value == "" || ofrm.rp_reason_comments.value == ""){
		$("#frmRestrictedUserAlerts_err").text("Please enter reason code and comments.");
		return false;
	}else{
		$.ajax({
			url: "../chart_notes/requestHandler.php?elem_formAction=ruCommentSave&patient_searched=" + pid + "&rp_reason_comments=" + document.frmRestrictedUserAlerts.rp_reason_comments.value + "&rp_reason_code=" + document.frmRestrictedUserAlerts.rp_reason_code.value+"&req_ptwo=1",
			success: function(resp){
				if(scheduler_loaded == "loaded" && $.isFunction('top.fmain.pre_load_front_desk')){
                    top.fmain.pre_load_front_desk(pid, '', showAlert);
					//top.fmain.load_front_desk(pid);
				}else{
					//var tab = top.document.getElementById("curr_main_tab").value;
					//var filename = top.core_get_tab_path(tab);
					//c = "../main/set_session.php?set_pid=" + pid + "&rd2=" + filename;
					//top.fmain.location.href = c;
					var curr_tab = top.document.getElementById("curr_main_tab").value;
					var redirect_url = top.core_get_tab_path(curr_tab);
                    if(eid=='RPT') {redirect_url[0]='../accounting/review_payments.php';}
                    if(curr_tab=='BillingEPP' || curr_tab=='BillingEMP' || curr_tab=='BillingE') {
                        var md='&md=ep';
                        if(curr_tab=='BillingE')md='';
                        url = '../billing/set_session.php?patient='+pid+'&eid='+eid+md;
                        var sc_wd=(screen.availWidth-20);
                        var sc_hg=(screen.availHeight-100);
                        top.popup_win(url,"left=0,top=0,resizeable=1,scrollbars=1,menubar=0,toolbar=0,status=0,width="+sc_wd+",height="+sc_hg);
                    }else{
                        top.core_set_pt_session(top.fmain, pid, redirect_url[0]);
                    }
				}
				//top.refresh_control_panel($("#curr_main_tab").val());
				top.document.getElementById("findBy").value = "Active";
				top.removeMessi();
				top.scanOpenWindow(consentSubFolderId);
			}
		});
	}
}

function scanOpenWindow(consentSubFolderId) {
	if(consentSubFolderId!='' && consentSubFolderId!='undefined' && !isNaN(consentSubFolderId)) {
		//top.tb_oPU['scanDocs'] =
		window.open(JS_WEB_ROOT_PATH + '/interface/chart_notes/scan_docs/index.php?folder_id='+consentSubFolderId,'scanDocs','height=700,width=1000,top=50,left=50');
	}
}


/****function show/hide loading image*******/
function show_loading_image(mode, padd_top, show_text){//TO SHOW / HIDE LOADING IMAGE
	if(mode == "show"){
		$("#div_loading_image").show();
		if(padd_top != "" && typeof(padd_top) != "undefined"){
			$("#div_loading_image").css("margin-top", padd_top+"px");
		}else{
			$("#div_loading_image").css("margin-top", "0px");
		}
		if(show_text != "" && typeof(show_text) != "undefined"){
			$("#div_loading_text").html(show_text).show();
		}
	}
	if(mode == "hide"){
		$("#div_loading_text").html("").hide();
		$("#div_loading_image").hide();
	}
}

/***This function update status in hidden fields if changes made in Tab***/
function chk_change_in_form(olddata,obj,strHidFieldName,e)
{
	e = e || event;
	try{
		characterCode = e.keyCode;
	}
	catch(err){
		characterCode = 0;
	}

	var strHidFieldLoad = "hidChk"+strHidFieldName+'Status';
	var strHidFieldName = "hidChkChange"+strHidFieldName;
	if(obj.type == "text" || obj.type == "textarea"){
		var newData = obj.value;
		//alert(newData);
		if(characterCode != 9 && characterCode != 16 ){
			if(olddata != newData && document.getElementById(strHidFieldLoad).value == 'loaded' ){
				document.getElementById(strHidFieldName).value = "yes";
			}
			else{
				if(document.getElementById(strHidFieldName).value != "yes"){
					document.getElementById(strHidFieldName).value = "no";
				}
			}
		}
	}
	else if(obj.type == "checkbox"){
		var newData = "";
		if(obj.checked == true){
			newData = "checked";
		}
		if(olddata != newData){
			document.getElementById(strHidFieldName).value = "yes";
		}
		else{
			if(document.getElementById(strHidFieldName).value != "yes"){
				document.getElementById(strHidFieldName).value = "no";
			}
		}
	}
	else if(obj.type == "radio"){
		var newData = "";
		if(obj.checked == true){
			newData = "checked";
		}
		if(olddata != newData){
			document.getElementById(strHidFieldName).value = "yes";
		}
		else{
			if(document.getElementById(strHidFieldName).value != "yes"){
				document.getElementById(strHidFieldName).value = "no";
			}
		}
	}
	else{
		document.getElementById(strHidFieldName).value = "yes";
	}
}

/***This function display patient infomation when user click on user name***/
function show_patient_info(r, ajax){
	if(typeof(ajax)=='undefined'){top.master_ajax_tunnel('ajax_handler.php?task=show_patient_info',top.show_patient_info,'','json');}
	else if(typeof(r)!='undefined'){
		var dataDetails = r;
		$.each(dataDetails,function(id,val){
			$('#patinentInfomenu #'+id+'').text(val);
			if(id.indexOf("MRN") != -1 && val!=""){
				var a = id.replace("External_MRN_","");
				$("#patinentInfomenu #div_pt_mrn").remove();
				$('#patinentInfomenu ul>li>.row').append("<div class=\"col-sm-6\" id=\"div_pt_mrn\"><div class=\"row\"><div class=\"col-sm-4\" id=\"pt_mrn_var\"><b>MRN "+a+" :</b></div><div id=\"pt_mrn\" class=\"col-sm-8\">"+val+"</div></div></div>")
			}
		});
		if($('.pt_info_menu').is(':visible')){
			$('.pt_info_menu').slideUp('slow');
		}else{
			$('.pt_info_menu').slideDown('slow');
		}
	}
	$('body').click(function(e){
		if(e.target.class != 'pt_info_menu'){
			$('.pt_info_menu').slideUp('slow');
		}
	});
}

function enlarge_pt_image(o){
	s=$(o).prop('src');
	f = s.indexOf('username.png');
	if(f>0) return;
	top.show_modal('PatientImage','Patient Image','<div class="text-center"><img src="'+s+'" style="max-width:800px; max-height:600px;"></div>','','350','modal-sg');
}

//EXECUTE LOGOUT
function logOut(){var strFunName; var arrArgu = new Array(); strFunName = "doLogOut"; top.doSaveChangeDB(strFunName,arrArgu);}

//PERFORM LOGOUT BY SAVING wv (IF OPENED)
function doLogOut(){
	//Check Work View Tab and Changes made in chart notes
	var prev_tab = $("#curr_main_tab").val();
	if(prev_tab == "Work_View"){
		if(typeof top.fmain.chkWVB4Move == 'function' && top.fmain.chkWVB4Move('Logout')){return;}
		top.setPtMonitorStatus();
	}

	top.document.getElementById("frmLogout").submit(); // logout
}

//set chart display status : patient monitor : patient Chart_Close--
function setPtMonitorStatus(){
	$.get("../chart_notes/requestHandler.php?elem_formAction=setPtMonitorStatus&stts=CHART_CLOSE", function(data){});
}

//Its checkd hidden field value for specific module.
function chkHidVal(place){
	if(place == "INS"){
		return top.$("#hidChkChangeInsTabDb").val();
	}
	else if(place == "DEMO"){
		return top.$("#hidChkChangeDemoTabDb").val();
	}
}

//If anything changed, it will ask confirmation for save
function askSaveOnFormChange(place){
	if(place == "INS"){
		if(priv_vo_pt_info=='0'){
			var ans = confirm("There is change in the information! Would you like to save and continue?");
			if(ans == true){
				var insCliamVal=top.fmain.document.getElementById("insCliamVal").value;
				var priInsRel=top.fmain.document.getElementById("i1subscriber_relationship").value;
				if(insCliamVal=="1" && priInsRel!="self"){
					fAlert('For Medicare Insurance, Relationship Should be Self','',360);
					top.show_loading_image("hide");
					return true;
				}
				document.getElementById("hidChkChangeInsTabDb").value = "no";
				top.fmain.document.getElementById("hidInsChangeOption").value = "1";
				top.fmain.saveCase(document.getElementById('hidInsCaseBtCaption').value);
				top.fmain.askSepAccount();
				return true;
			}
			else{
				document.getElementById("hidChkChangeInsTabDb").value = "no";
				return false;
			}
		}else{
			document.getElementById("hidChkChangeInsTabDb").value = "no";
			return false;
		}
	}
	else if(place == "DEMO"){
		if(priv_vo_pt_info=='0'){
			var ans = confirm("There is change in the information! Would you like to save and continue?");
			if(ans == true){
				document.getElementById("hidChkChangeDemoTabDb").value = "no";
				$("#hidDemoChangeOption",top.fmain.document).val("1");
				top.fmain.process_save();
				return true;
			}
			else{
				document.getElementById("hidChkChangeDemoTabDb").value = "no";
				return false;
			}
		}else{
			document.getElementById("hidChkChangeDemoTabDb").value = "no";
			return false;
		}
	}
}

//Save changed data if any any change occured.
function doSaveChangeDB(strFunName,arrArgu){
	var funToExe = strFunName+"(";
	funToExe += arrArgu.join(",");
	funToExe += ")";
	//alert(funToExe);
	var ansHidDemoVal = top.chkHidVal("DEMO");
	var ansHidInsVal = top.chkHidVal("INS");

	if(ansHidDemoVal == "yes"){
		var ansAsk = top.askSaveOnFormChange("DEMO");
		if(ansAsk == true){
			top.show_loading_image("hide");
			var tempInterval = setInterval(function() {
				var ans = top.chkConfirmSave("","chk");
				if(ans == "yes"){
					clearInterval(tempInterval);
					//alert(funToExe);
					(strFunName !== '') ? eval(funToExe) : '';
				}
			}, 10);
		}
		else{
			//alert(funToExe);
			(strFunName !== '') ? eval(funToExe) : '';
		}
	}
	else if(ansHidInsVal == "yes"){
		var ansAsk = top.askSaveOnFormChange("INS");
		if(ansAsk == true){
			top.show_loading_image("hide");
			var tempInterval = setInterval(function() {
				var ans = top.chkConfirmSave("","chk");
				if(ans == "yes"){
					clearInterval(tempInterval);
					//alert(funToExe);
					(strFunName !== '') ? eval(funToExe) : '';
				}
			}, 10);
		}
		else{
			//alert(funToExe);
			(strFunName !== '') ? eval(funToExe) : '';
		}
	}
	else{
		//alert(funToExe);
		(strFunName !== '') ? eval(funToExe) : '';
	}
	//ClosePopUpExe();
}

function chkConfirmSave(str,op){
	var str = str || "";
	if(op == "set"){
		document.getElementById("hidChkConfirmSave").value = str;
	}
	else if(op == "chk"){
		return document.getElementById("hidChkConfirmSave").value;
	}
}


/** This will load Ref. Physicians from xml and provide typeaheads  **/
function loadPhysicians(ele, hid_id, server_root,fax_field,call_from,hid_id_2,fax_id_2,fax_ref_name_id,fax_field_2,obj_frame){
	call_from = call_from || '';
	server_root = server_root || ser_root;
	hid_id = hid_id || "";
	hid_id_2 = hid_id_2 || "";
	fax_id_2 = fax_id_2 || "";
	fax_ref_name_id = fax_ref_name_id || "";
	fax_field_2 = fax_field_2 || "";
	obj_frame = obj_frame || top;
	obj_hid_id_2 = obj_frame.document.getElementById(hid_id_2) ? obj_frame.document.getElementById(hid_id_2) : '';
	obj_fax_id_2 = obj_frame.document.getElementById(fax_id_2) ? obj_frame.document.getElementById(fax_id_2) : '';
	obj_fax_ref_name_id = obj_frame.document.getElementById(fax_ref_name_id) ? obj_frame.document.getElementById(fax_ref_name_id) : '';

	var lastChar = server_root.substr(-1); // Selects the last character
	if (lastChar != '/') {         // If the last character is not a slash
	   server_root = server_root + '/';            // Append a slash to it.
	}

	var old_ref_val = '';
	if(hid_id!=""){
		var hidden_id = top.document.getElementById(hid_id);
		if(!hidden_id){
			hidden_id = top.fmain.document.getElementById(hid_id);
		}
		if(typeof(hidden_id) != "undefined" && hidden_id.value > 0 && ele.value != ""){
			reff_multi_detail_popup(ele, hidden_id, call_from, obj_hid_id_2, obj_fax_id_2, obj_fax_ref_name_id,'address');
			if(call_from == "WV")return;
		}
	}

	trimLname = $.trim(ele.value);
	trimLname = trimLname.replace(/[^a-zA-Z+]/g,"");
	char = trimLname.substring(1,2);
	if(char == ",")
	len = 1;
	else len = 2;
	if(trimLname.length==len || trimLname.length > len && (old_ref_val!=trimLname || old_ref_ele!=ele)){
		old_ref_ele = ele
		old_ref_val = trimLname;
		XMLname = trimLname.substring(0,2)+".xml";
		XMLname = XMLname.toLowerCase();
		loadXMLDoc(server_root+XMLname,ele,hid_id,fax_field,call_from,hid_id_2,fax_id_2,fax_ref_name_id,fax_field_2,obj_frame);
	}

}


/** This function will be used in palce of --> reff_multi_add_popup() & reff_multi_add_popup_email();
	This function is used for creating multi address or email popup
 **/
function reff_multi_detail_popup(ele,hidd,callFrom,hidd2,fax_email_ID2,fax_email_RefNameId,request){
	callFrom = callFrom || '';
	reff_id = $(hidd).val();
	//$("#div_reff_add").remove();
	if(request == 'address'){
		modal_title = 'Choose Address';
	}else{
		modal_title = 'Choose Email';
	}
	if($('#div_reff_add').length > 0){ $('#div_reff_add,.modal-backdrop').remove(); }
	if($("#div_reff_add").length <= 0 && reff_id > 0){
		ele_val = $(ele).val();
		arrName = ele_val.split(",");
		if(arrName.length < 2)return;
		if( top.fmain == 'undefined'){
			if(opener != "undefined" && opener != null && typeof(opener.top.show_loading_image)!="undefined"){ opener.top.show_loading_image('show'); }
		}
		else if(typeof(top.show_loading_image)!="undefined") { top.show_loading_image('hide'); }
		if( top.fmain == 'undefined'){
			if(opener != "undefined" && opener != null && typeof(opener.top.show_loading_image)!="undefined"){opener.top.show_loading_image('hide');}
		}

		/** Multi Address Popup **/
		var header_content = '<div id="div_reff_add_header" class="modal-header bg-primary"><button type="button" class="close" data-dismiss="modal">×</button><h4 class="modal-title">'+modal_title+'</h4></div>';
		var body_content   = '<div id="div_reff_add_content" class="modal-body" ></div>';
		var footer_content = '';

		var modal = '<div class="common_modal_wrapper">'
				modal = '<div class="modal fade" id="div_reff_add" role="dialog">';
					modal += '<div class="modal-dialog"><div class="modal-content">'+header_content+body_content+footer_content+'</div>';
				modal += '</div>';
			modal += '<div>';
		if($('#div_reff_add.modal').length == 0){
			$('body').append(modal);
		}else{
            $('#div_reff_add.modal,.modal-backdrop').remove();
            $('body').append(modal);
        }

        $('#div_reff_add #div_reff_add_content').empty();
	}else {
        $('#div_reff_add #div_reff_add_content').empty();
    }
	get_reff_details(reff_id, hidd, hidd2,fax_email_ID2,fax_email_RefNameId,request);
}

/** This function is used to get multiple address or emails of provided physician **/
function get_reff_details(reff_id, hidd, hidd_2, fax_email_id_2,fax_email_ref_name_id,request){
	if(reff_id != "" && reff_id > 0){
		hidd_id = $(hidd).length > 0 ? $(hidd).attr('id') : '';
		hidd_id2 = $(hidd_2).length > 0 ? $(hidd_2).attr('id') : '';
		fax_id2 = $(fax_email_id_2).length > 0 ? $(fax_email_id_2).attr('id') : '';
		fax_ref_name_id = $(fax_email_ref_name_id).length > 0 ? $(fax_email_ref_name_id).attr('id') : '';
		if(request == 'address'){
			var data =  "id="+reff_id+"&hidd_id="+hidd_id+"&hidd_id2="+hidd_id2+"&fax_id2="+fax_id2+"&fax_ref_name_id="+fax_ref_name_id;
		}else{
			var data = "id="+reff_id+"&hidd_id="+hidd_id+"&hidd_id2="+hidd_id2+"&email_id2="+fax_id2+"&email_ref_name_id="+fax_ref_name_id+"&req_type=email";
		}
		$.ajax({
			url:top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/reff_phy_add.php",
			type: "POST",
			data:data,
			success:function(r){
				if(r != ""){
					$(hidd).blur();
					$("#div_reff_add_content").html(r);
					if(!$(hidd).attr('val_selected')){
						if( !$("#div_reff_add").hasClass('in') ){
							$("#div_reff_add").modal({ backdrop: 'static',keyboard: false}).modal('show');
						}
					}
					if( top.fmain == 'undefined'){
						if(opener != "undefined" && opener != null && typeof(opener.top.show_loading_image)!="undefined") { opener.top.show_loading_image('hide'); }
					}
					else if(typeof(top.show_loading_image)!="undefined") { top.show_loading_image('hide'); }
				}
				if( top.fmain == 'undefined'){
					if(opener != "undefined" && opener != null && typeof(opener.top.show_loading_image)!="undefined") { opener.top.show_loading_image('hide'); }
				}
				else if(typeof(top.show_loading_image)!="undefined") { top.show_loading_image('hide'); }
			}
		});
	}
}

/** This function is used to set default address or email from multiple ones **/
function set_reff_add(reff_id, hidd_id,obj,faxno,hidd_id2,fax_id2,reffName,fax_ref_name_id){
	$("#div_reff_add_content input[type=radio]").each(function(index, element) {
        $(this).prop("checked",false);
    });
	$(obj).prop("checked",true);
	var jID = top.document.getElementById(hidd_id);
	if(!jID){
		jID = top.fmain.document.getElementById(hidd_id);
	}
	$(jID).val(reff_id);
	faxID = "#"+hidd_id2;
	$(faxID).val(reff_id);
	send_fax_number = "#"+fax_id2;

	if($(send_fax_number) && typeof($(send_fax_number).val()) != "undefined") {
		$(send_fax_number).val(faxno);
	} else {
		$(send_fax_number,top.fmain).val(faxno);
	}
	faxRefNameId = "#"+fax_ref_name_id;
	if($(faxRefNameId)) {
		$(faxRefNameId).val(reffName);
	}
	// To stop modal from poping again and again even if user has made a selection
	$(jID).attr('val_selected','1');
	$('#div_reff_add').modal('hide');
}

/** This function will read xml of the provided xml name and init. typeahead  on the provided input field **/
function loadXMLDoc(XMLname,ele,hidID,refPhyfax,callFrom,hidID2,faxID2,faxRefNameId,faxfield2,objFrame)
{
	var hid_id = top.document.getElementById(hidID);
	if(!hid_id){
		hid_id = top.fmain.document.getElementById(hidID);
	}
	objFrame = objFrame || top;
	var responseArr = '';
	$.ajax({
		url:XMLname,
		type:'POST',
		dataType:'xml',
		success:function(resp){
			if ($.trim(resp)){
				arrRefPhy = [];
				arrRefPhyID = [];
				arrRefPhyFax = [];
				arrRefPhyEmail= [];
				$(resp).find('refPhyInfo').each(function(){
					var fNameTag		 = $(this).find('refphyFName').text();
					var mNameTag 		 = $(this).find('refPhyMname').text();
					var lNameTag 		 = $(this).find('refphyLName').text();
					var titleTag 		 = $(this).find('refPhyTitle').text();
					var phyIDNameTag 	 = $(this).find('refphyId').text();
					var phyFaxTag  		 = $(this).find('refFax').text();
					var phyEmailTag  	 = $(this).find('refEmail').text();

					if(typeof(fNameTag) != "undefined"){
						fname = fNameTag;
						mname = mNameTag;
						lname = lNameTag;
						title = titleTag;
						phyID = phyIDNameTag;
						phyFax= phyFaxTag;
						phyEmail=phyEmailTag;
					}

					var name = "";
					REF_PHY_FORMAT='local';
					REF_PHY_FORMAT = (typeof(REF_PHY_FORMAT) == 'undefined')? top.REF_PHY_FORMAT : REF_PHY_FORMAT;
					if(REF_PHY_FORMAT.toLowerCase() != 'boston' || REF_PHY_FORMAT == ''){
						//if(title != '')
						//name += title+' ';
						if(lname != '')
						name += trim_multi_space(lname)+', ';
						if(fname != '')
						name += trim_multi_space(fname)+' ';
						if(mname != '')
						name += trim_multi_space(mname);
						name = name.replace(/\\/gi, "");
					}
					else{
						name = trim_multi_space(lname)+", "+trim_multi_space(fname)+" ";
						if(mname!='')
						name += trim_multi_space(mname)+" ";
						name += trim_multi_space(title);
						name = name.replace(/\\/gi, "");
					}

					var tmp =  {'name':name,'id':phyID};
					arrRefPhy.push(tmp);
					arrRefPhyID[name] = phyID;
					//arrRefPhyFax.push(phyFax);
					arrRefPhyFax[phyID] = phyFax;
					arrRefPhyEmail[phyID] = phyEmail;
				});

				if(hidID != ""  && document.getElementById(refPhyfax)){ //for consult letter fax values
					//document.getElementById(refPhyfax).value="";
					//var obj1 = new actb(document.getElementById(ele.id),arrRefPhy,"","",document.getElementById(hidID),arrRefPhyID,",",document.getElementById(refPhyfax),arrRefPhyFax,document.getElementById(faxRefNameId),arrRefPhy,document.getElementById(faxfield2),arrRefPhyFax);
				}else if(hidID != ""){
					responseArr = arrRefPhy;
				}else{
					responseArr = arrRefPhy;
				}
				$(ele).focus(function(e) {
					if(hidID != "" && hid_id.value > 0 && $(ele).val() != "")
					/**
					 * Changes By: Pankaj S.
					 * Update function params, adding objFrame with some of the param
					 */
					reff_multi_detail_popup(this,objFrame.document.getElementById(hidID),callFrom,objFrame.document.getElementById(hidID2),objFrame.document.getElementById(faxID2),objFrame.document.getElementById(faxRefNameId),'address');
				});
			}

			var autocomplete = $(ele).typeahead({scrollBar:true});
			autocomplete.data('typeahead').source = arrRefPhy;
			autocomplete.data('typeahead').updater = function(item){
					hid_id.value = arrRefPhyID[item];
					//fax
					if(typeof(refPhyfax)!="undefined" && refPhyfax!="" && objFrame.$("#"+refPhyfax).length>0){
						if(typeof(hid_id.value)!="undefined" && hid_id.value!=""){
							var tmp = (refPhyfax.indexOf("send_email_id")!=-1) ? arrRefPhyEmail[hid_id.value] : arrRefPhyFax[hid_id.value]  ;
							if(typeof(tmp)=="undefined") {tmp="";}
							 objFrame.$("#"+refPhyfax).val(tmp);
						}
					}

					//faxrefname
					if(typeof(faxRefNameId)!="undefined" && faxRefNameId!="" && objFrame.$("#"+faxRefNameId).length>0){
						if(typeof(hid_id.value)!="undefined" && hid_id.value!=""){
							var tmp = (refPhyfax.indexOf("send_email_id")!=-1) ? arrRefPhyEmail[hid_id.value] : arrRefPhyFax[hid_id.value] ;
							if(typeof(tmp)=="undefined") {tmp="";}
							 objFrame.$("#"+faxRefNameId).val(tmp);
						}
					}

					//fax2
					if(typeof(faxfield2)!="undefined" && faxfield2!="" && objFrame.$("#"+faxfield2).length>0){
						if(typeof(hid_id.value)!="undefined" && hid_id.value!=""){
							var tmp = (refPhyfax.indexOf("send_email_id")!=-1) ? arrRefPhyEmail[hid_id.value] : arrRefPhyFax[hid_id.value] ;
							if(typeof(tmp)=="undefined") {tmp="";}
							objFrame.$("#"+faxfield2).val(tmp);
						}
					}

                    //Remove multi address identifier if phy is changed
                    var hiddenRefObj = $(document.getElementById(hidID));
                    if(hiddenRefObj.length){
                        if(hiddenRefObj.attr('val_selected')) hiddenRefObj.removeAttr('val_selected');
                    }

					return item;
			};
			setTimeout(function(){
				$(ele).typeahead('lookup');
			},500);
		}
	});
}

function trim_multi_space(val) { return val.replace(/\s{2,}/g,''); }

/***************
Function: download_erx_data

Purpose: Called on click on "eRx..." icon to download erx drug and allergy info.
***************/
function download_erx_data(){
	top.show_loading_image('show', '', 'Processing. Please hold a moment...');
	$.ajax({
	  url: '../chart_notes/erx_patient_rx_history.php',
	  data: '',
	  success: function(r){
		  top.show_loading_image('hide');
		  top.show_modal('erx_data','eRx',r,'','350','modal-lg');
	  }
	});
}

/***************
Function: icon_popups

Purpose: Called on click on "eRx..." icon to download erx drug and allergy info.
***************/
function icon_popups(param,pid){
	switch(param){
		case 'change_pw':
			top.show_loading_image('show', '', 'Processing. Please hold a moment...');
			$.get( "change_password.php", function(r) {
			  btm_buttons = '<button type="button" class="btn btn-success" onClick="top.check_valid_cp()">Save</button>   ';
			  btm_buttons+= '<button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>';
			  top.show_modal('ChangePassword','Change Password',r,btm_buttons,'350','');
			  top.show_loading_image('hide');
			});
			break;
		case 'imon_settings':
			top.show_loading_image('show', '', 'Processing. Please hold a moment...');
			botButton = '<button type="button" class="btn btn-success" data-dismiss="modal" onClick="top.save_imon_settings()">Save</button>';
			$.get( "imonitor_settings.php", function(r) {
			  if(r.indexOf("patient loaded yet")>0 || r.indexOf("Patient not checked-in")>0) botButton = '<button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>';
			  top.show_modal('iMonSettings','iMedicMonitor Settings',r,botButton,'550','');
			  top.show_loading_image('hide');
			  top.stop_enter_key_on('imon_comments');
			});
			break;
		case 'test_manager':
			var width = screen.availWidth;
			var height = screen.availHeight;
			var features = 'top=0,left=0,toolbar=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+'';
			top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/test_manager/index.php',features);
			break;
		case 'pt_at_glance':
			var width = screen.availWidth;
			var height = screen.availHeight;
			var features = 'top=0,left=0,toolbar=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+'';
			if(typeof(pid)!="undefined"&&pid!=""){ q="?p_id="+pid; }else{q="";}
			top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/past_diag/chart_patient_diagnosis.php'+q,features);
			break;
		case 'phy_day_sch':
			top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/scheduler/physician_scheduler/index.php');
			break;
		case 'print_pt_summary':
			var width = (screen.availWidth - 20);
			var height = (screen.availHeight - 60);
			var features = 'top=0,left=0,toolbar=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+'';
			top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/patient_info/common/print_function.php',features);
			break;
		case 'show_pt_alert':
			top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/patient_info/demographics/patient_alert.php');
			break;
		case 'patient_problems_list':
			$.get(top.JS_WEB_ROOT_PATH + "/interface/Medical_history/problem_list/patient_problems_list_popup.php", function(r) {
				top.show_modal('PatientProblemsListPopup','Patient Problems List',r,'<button type="button" class="btn btn-success" data-dismiss="modal" onClick="saveProblemList();">Save</button>','650','');
			});
			break;
		case 'contact_lens_worksheet':
			var width = screen.availWidth;
			width= (width*98) / 100;
			//var width=690;
			var height = window.innerHeight;
			var features = 'top=0,left=0,toolbar=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+'';
			top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/contact_lens_worksheet_popup.php',features);
/*
		   var buttons = '<button type="button" class="btn btn-success" data-dismiss="modal" onClick="saveContactLens();">Save</button>';
		   buttons += '<button type="button" class="btn btn-success" data-dismiss="modal" onClick="top.icon_popups(\"contact_lens_order\");">Order</button>';
		   buttons += '<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closePopup();">Close</button>';
		   $.get(top.JS_WEB_ROOT_PATH + "/interface/chart_notes/contact_lens_worksheet_popup.php", function(r) {
			top.show_modal('ContactLensPopup', 'Contact Lens', r, buttons, '1000', '');
		   });
*/		   break;
		case 'future_sch_tests_appointment':
			var buttons = '<button type="button" class="btn btn-success" data-dismiss="modal" onClick="add_future_sch_tests_appoints(\'1\');">Save</button>';
			buttons += '<button type="button" class="btn btn-success" data-dismiss="modal" onClick="add_future_sch_tests_appoints();">Add New</button>';
			buttons += '<button type="button" class="btn btn-success" data-dismiss="modal" onClick="add_future_sch_tests_appoints(\'0\');">Close</button>';
			$.get(top.JS_WEB_ROOT_PATH + "/interface/chart_notes/future_sch_tests_appoints.php", function(r) {
				top.show_modal('ContactLensPopup', 'Future Scheduled Tests/appointments(Outside)', r, buttons, '650', '');
			});
			break;
		case 'contact_lens_order':
			/*var width=690;
			var height = window.innerHeight;
			var features = 'top=0,left=0,toolbar=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+'';
			top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/print_order.php',features);*/
			$.get(top.JS_WEB_ROOT_PATH + "/interface/chart_notes/print_order.php", function(r) {
				top.show_modal('ContactLensOrderPopup', 'Contact Lens Print Order', r, "", '1000', 'modal-lg');
			});
			break;
		case '':
			break;
	}
}

function save_imon_settings(){
	frm_data = $('#imon_settings').serialize();
	$.ajax({
	  type: "POST",
	  url: "ajax_handler.php?task=save_imon_settings",
	  data: frm_data,
	  success: function(r){/**do nothing**/}
	});
}

function refill_imon_settings(sch_id){
	rs = patient_location_rs[sch_id];
	f = document.forms.imon_settings;
	if(typeof(rs)=='object'){
		for(x in rs){
			//alert(x+' :: '+rs[x]);
			v = rs[x];
			if(x=='app_room'){f.imon_rooms.value = v;}
			else if(x=='pt_with'){
				if(v=='0'){
					$(arr_ready_elements).each(function(index, element) {
                        $(this).prop('checked',false);
                    });
				}else if(v=='1'){top.only1checkbox(arr_ready_elements,'r4doc');}
				else if(v=='2'){top.only1checkbox(arr_ready_elements,'r4tech');}
				else if(v=='3'){top.only1checkbox(arr_ready_elements,'r4test');}
				else if(v=='4'){top.only1checkbox(arr_ready_elements,'r4wr');}
				else if(v=='6'){top.only1checkbox(arr_ready_elements,'r4done');}
			}else if(x=='sch_message'){f.imon_comments.value = v;}
		}
		sch_rs = pt_checked_in_appt_today[sch_id];
		for(x in sch_rs){
			v = sch_rs[x];
			if(x=='pt_priority'){
				top.only1checkbox(arr_prior_elements,'prio'+v);
			}
		}
	}else {f.reset();f.imon_sch_id.value = sch_id;}

}

/****POST CHANGE PASSWORD FORM*******/
function process_cp_form(){
	frm_data = $('#cp').serialize();
	$.ajax({
	  type: "POST",
	  url: "ajax_handler.php?task=process_change_password",
	  data: frm_data,
	  success: function(r){
			//alert(r);/**do nothing**/
			r = JSON.parse(r);
			if(typeof(r)=='object'){
				error 		= r.error;
				errormsg	= r.errormsg;
				response	= r.response;
				if(error){
                    top.fAlert(errormsg);
                }else{
                    $('#ChangePassword').modal('hide');
                    top.fAlert(response);
                }
                document.forms.cp.reset();

			}
		}
	});
}

//Gen Health POP up Start --
var showMedReview_tmr="",reqd=0;
function showMedReview(flg){

	if(!top.fmain||!top.fmain.jQuery){return;}

	clearTimeout(showMedReview_tmr);
	showMedReview_tmr="";
	var o = top.fmain.$("#general_health");

	/*
	o.on("click", function(){ o.tooltip('hide'); $("div[role=tooltip]").remove(); o.tooltip('dispose'); o.tooltip({title:"Hello", }); });
	if(flg==1){
		var tid = o.attr("aria-describedby");
		if(typeof(tid)!="undefined" && top.fmain.$("#"+tid).length>0 && top.fmain.$("#"+tid+":visible").length>0){}else{o.tooltip('show');}
	}else {showMedReview_tmr = setTimeout(function(){o.tooltip('hide');},500);}
	*/

	if(flg==1){
		if(top.fmain.$("#dvMedRvwBy").length<=0 && reqd==0){
		top.show_loading_image('show', '', 'Processing...');	reqd=1;
		var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php";
		$.get(url,{elem_formAction:"showMedReview"},
		function(resp){
			reqd=0;
			top.fmain.$("#dvMedRvwBy").remove();
			if(resp!=""){
			//resp = resp;
			top.fmain.$("body").append(resp);

			var w = top.fmain.$("#dvMedRvwBy").outerWidth(); w = parseInt((parseInt(w)/4));
			var h = parseInt(o.outerHeight());
			var w1 = 0; //parseInt(parseInt(o.outerWidth())/2);
			if(h==0){h=50;}
			var p = o.offset();
			var q = parseInt(p.left);
			if(q==0){q = 300;}

			top.fmain.$("#dvMedRvwBy").css({"left":q+w1, "top":parseInt(p.top)+parseInt(h)+5}).show();
			}
			top.show_loading_image('hide');
		});
		}
	}else if(flg==0){
		showMedReview_tmr = setTimeout(function(){showMedReview(2);},500);
	}else if(flg==2){
		top.fmain.$("#dvMedRvwBy").remove();
	}
}
//Gen Health POP up End --

function tb_popup(obj,fpu)
{
	switch(obj.title)
	{
		case "Change Password":
		cp_offset = $(obj).offset();
		top.fancyModal('<iframe name="messiframe" id="messiframe" style="height:130px; width:350px;" frameborder=0 src="'+WRP+'/interface/main/changepassword.php"></iframe>','Change Password','','',true,obj);
		break;
		case "Patient at a glance":
			if(typeof top.fmain != "undefined"){
				if(fpu == "1"){
					var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php";
					$.get(url,{elem_formAction:"PtAtAGlancePopUp",limit_records:"1"},
					function(resp){
						resp = $.parseJSON(resp);
						$('#pgd_showpop',top.fmain.document).html(resp.data)
							.fadeIn(function(){
								if(typeof(top.fmain.setHgtGrpDiv)=='function'){top.fmain.setHgtGrpDiv();}
						});
					});
				}
				else if(fpu == "0"){
					//if(top.fmain.$('#pgd_popfun')) top.fmain.$('#pgd_showpop').hide();
				}else{
					$('#pgd_showpop',top.fmain.document).remove();
					if(typeof top.fmain.showPatientDiagnosis != "undefined"){
						top.fmain.showPatientDiagnosis();
					}else{
						popup_win(WRP+"/interface/chart_notes/past_diag/chart_patient_diagnosis.php",'winptglance','height=670,width=1260,top=0,left=0');
					}
				}
			}
		break;
		case "Glaucoma Flow sheet":
			if(typeof top.fmain != "undefined"){
				top.fmain.showOtherForms(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/chart_glucoma.php?mode=1","docGlucoma",1270,top.wn_height,1);
			}
		break;
		case "Patient Test Manager":
			if(typeof top.fmain != "undefined" && typeof top.fmain.opTests != "undefined")top.fmain.opTests();
		break;
		case "Patient General Health":
				if(typeof top.fmain.showMedList != "undefined"){ top.fmain.showMedList('PMH'); showMedReview(2); }
		break;
		case "Surgeries":
			if(typeof top.fmain != "undefined"){
				if(fpu == "1"){
					if(typeof top.fmain.showMedList != "undefined") top.fmain.showMedList('6');
					if(typeof top.fmain.SxWinObj != "undefined" && top.fmain.SxWinObj) top.fmain.SxWinObj.close();
				}else if(fpu == "0"){
					if(typeof top.fmain.hideMedList != "undefined") top.fmain.hideMedList('6');
				}else{
					top.tb_popup(obj,0);
					if(typeof(top.fmain.openMedHX) != "undefined") top.fmain.openMedHX('sxPro2',800);
				}
			}
		break;
		case "Allergies":
			if(typeof top.fmain != "undefined"){
				if(fpu == "1"){
					if(typeof top.fmain.showAllergy != "undefined") top.fmain.showAllergy(1);
				}else if(fpu == "0"){
					if(typeof top.fmain.showAllergy != "undefined") top.fmain.showAllergy();
				}else{
					if(typeof(top.fmain.openMedHX) != "undefined") top.fmain.openMedHX('allergies', '800');
				}
			}
		break;
		case "Smart Charting":
			if(typeof top.fmain != "undefined" && typeof top.fmain.getSmartChartPopUp != "undefined")
			top.fmain.getSmartChartPopUp();
		break;
		case "Physician Notes":
			if(typeof top.fmain != "undefined"){
				if(fpu == "1"){
					if(typeof top.fmain.showPhyNotes != "undefined") top.fmain.showPhyNotes(1);
				}else if(fpu == "0"){
					if(typeof top.fmain.showPhyNotes != "undefined") top.fmain.showPhyNotes();
				}else{
					top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/physician_notes.php');
				}
			}
		break;
		case "Patient All Tests":
			if(typeof top.fmain != "undefined" && typeof top.fmain.openPtPdf != "undefined")top.fmain.openPtPdf('Test');
		break;
		case "Patient All Visits":
			if(typeof top.fmain != "undefined" && typeof top.fmain.openPtPdf != "undefined")top.fmain.openPtPdf('chart');
		break;
		case "Consult Letters":
			//if(typeof top.fmain != "undefined" && typeof top.fmain.opConsult != "undefined")top.fmain.opConsult();
			if(typeof opConsult != "undefined"){opConsult();}
		break;
		case "Operative Note":
			if(typeof top.fmain != "undefined" && typeof top.fmain.opTests != "undefined")top.fmain.opTests('operativeNote');
		break;
		case "Confidential Text":
			if(typeof top.fmain != "undefined"){
				pop_height = screen.height-100;
				popup_win(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/confidential_text.php",'Confidential Text','height='+pop_height+',width=1150,top=50,left=50');
			}
			if(typeof top.fmain != "undefined" && typeof top.fmain.show_confidential_text_win != "undefined")
			top.fmain.show_confidential_text_win();
		break;
		case "Patient Providers":
			if(typeof top.fmain != "undefined" && typeof top.fmain.showPtProviders != "undefined"){
			top.fmain.showPtProviders();
			}else{
				var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/pt_providers/index.php";
				var wn = "winptpro";
				var ftr = "height=600,width=1000,top=0,left=0,scrollbars=1";
				popup_win(url,wn,ftr);
			}
		break;
		case "Patient Instruction Documents":
			pop_height = screen.height-100;
			popup_win(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/pt_instructions/index.php",'ptInst','height='+pop_height+',width=1150,top=50,left=50');
		break;
		case "Scanned Patient Documents":
			popup_win(WRP+"/interface/chart_notes/scan_docs/index.php",'scanDocs','height=700,width=1000,top=50,left=50');
		break;
		case "Quick Scan":
			var pid = 0;
			if( top.document.getElementById('patient_id')) {
				pid = parseInt(top.document.getElementById('patient_id').value);
			}
			if( !pid ) {
				top.fAlert('Please Select patient.');
				return false;
			}
			var popFeatures = "location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width=1000,height=900,left=100";
			popup_win(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/scan_documents.php?t=sch&a=iqs&sb=no','Quick Scan',popFeatures);
		break;
		case "Patient Alerts":
			popup_win(top.JS_WEB_ROOT_PATH+"/interface/patient_info/demographics/patient_alert.php",
					'alerts',
					'width=1000,height=422,left=150,top=80,toolbar=0,scrollbars=0,location=no,statusbar=0,menubar=0,resizable=0');
		break;
		case "Complete Patient Record":
			popup_win(WRP+"/interface/printing/index.php",
					'imedic_print',
					'width=560,height=750,left=150,top=80,location=0,status=1,resizable=1,left=1,top=10,scrollbars=1');
		break;
		case "eRx":
			if(typeof top.fmain != "undefined"){
				if(fpu == "1"){
					if(typeof top.fmain.showMedList != "undefined") {top.fmain.showMedList('1');}else{ showMedList('1');}
				}else if(fpu == "0"){
					if(typeof top.fmain.hideMedList != "undefined") {top.fmain.hideMedList('1');}else{ hideMedList('1');}
				}else{
					if(typeof top.fmain.open_erx != "undefined"){top.fmain.open_erx();}else{open_erx();}
				}
			}
		break;
		case "eRx Prescription":
			if(typeof top.fmain != "undefined"){
				if(fpu == "0"){
					if(typeof top.fmain.showRxDiv != "undefined"){ top.fmain.showRxDiv('none');}else{showRxDiv('none');}
				}else{
					if(typeof top.fmain.getPatientRx != "undefined"){top.fmain.getPatientRx('block');}else{getPatientRx('block');}
				}
			}
		break;
		case "Close Patient":

			if(typeof top.fmain != "undefined" && typeof top.fmain.chrtClose != "undefined"){ //checking if function exists in fmain
				var ofrm = top.fmain.document.frmMain;
				if(ofrm && ofrm.elem_masterId && ofrm.elem_masterId.value!=""){ // checking if WV form exists and field is not empty. Assuming this form and field exists in WV only
					top.fmain.chrtClose();
					return; //no further execution in this function
				}
			}

			var ansHidInsVal = top.chkHidVal("INS");
			var ansHidDemoVal = top.chkHidVal("DEMO");
			if(ansHidInsVal == "yes"){
				var ansAsk = top.askSaveOnFormChange("INS");
				if(ansAsk == true){
					var tempInterval = setInterval(function() {
						var ans = top.chkConfirmSave("","chk");
						if(ans == "yes"){
							clearInterval(tempInterval);
							doClosePatient(WRP);
						}
					}, 10);
				}
				else{
					doClosePatient(WRP);
				}
			}
			else if(ansHidDemoVal == "yes"){
				var ansAsk = top.askSaveOnFormChange("DEMO");
				if(ansAsk == true){
					var tempInterval = setInterval(function() {
						var ans = top.chkConfirmSave("","chk");
						if(ans == "yes"){
							clearInterval(tempInterval);
							doClosePatient(WRP);
						}
					}, 10);
				}
				else{
					doClosePatient(WRP);
				}
			}
			else{
				doClosePatient(WRP);
			}
		break;
		case "Patient Vital Signs":
			if(typeof top.fmain != "undefined"){
				if(fpu == "1"){
					if(typeof top.fmain.showPat_vital != "undefined")top.fmain.showPat_vital();
				}else{
					if(typeof top.fmain.add_vital != "undefined")top.fmain.add_vital();
				}
			}
		break;
		case "Patient Communication":
            if(fpu=='docs_tab'){
                load_pvc('','docs_tab');
            } else {
                load_pvc();
            }
		break;
		case "History of CPT Services":
			if(typeof top.fmain != "undefined"){
				top.show_loading_image("show");
				$.ajax({
					type: "POST",
					url: top.JS_WEB_ROOT_PATH+"/interface/chart_notes/history_cpt_services.php",
					success: function(respRes){
						var str = '<div class="col-sm-12 text-center"><input type="button" name="elem_btnPrint" id="elem_btnPrint" onClick="document.printFrmALLPDF.submit()" class="btn btn-success" value="Print" align="bottom" />   <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></div>';
						top.show_modal('div_pt_cpt_hx','History of CPT Services',respRes,str,'550','modal-lg','false');
						top.show_loading_image("hide");
					}
				});
			}
		break;
		case "Mpay":
			window.open("https://www.mpaygateway.com/mpay",'Mpay','_target').resizeTo(800,500);
		break;
		case "Retinal Flow Sheet":
			popup_win(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/retinal_flow_sheet.php",'retinal_flow_sheet','height=720,width=1260,top=0,left=0');
		break;
		case "Procedure Flow Sheet":
			popup_win(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/procedure_flow_sheet.php",'procedure_flow_sheet','top=0,left=0');
		break;
		case "Multi Upload":
			popup_win(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/pdf_split.php",'multi_upload_split','height=720,width=1260,top=0,left=0');
		break;
		case "Patient Chart Search":
			popup_win(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/pt_chart_search.php",'pt_chart_search','height=800,width=800,top=0,left=0');
		break;
		case "HIE Data":
			popup_win(top.JS_WEB_ROOT_PATH+'/hl7sys/show_hl7_decoded.php','HIE_HL7_pop','location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width=1100,height=680')
		break;
		case "MUR Checklist":
			var parWidth = parent.document.body.clientWidth;
			popup_win(top.JS_WEB_ROOT_PATH+'/interface/MU_checklist/index.php','MUR_Checklist_pop','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+',height=600,left=10,top=80');
		break;
		case "Patient Refractive Sheet":
			popup_win(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/patient_refractive_sheet.php",'patient_refractive_sheet','height=720,width=1260,top=0,left=0,resizable=1,scrollbars=1');
		break;
		case "Primary Referrals":
			popup_win(top.JS_WEB_ROOT_PATH+"/interface/patient_info/insurance/referrals.php",'Patient Referrals','height=720,width=1260,top=0,left=0');
		break;
		case "Toric Calculator":
			if(fpu=='1')
			popup_win(top.JS_WEB_ROOT_PATH+'/addons/toric/toric.php','Toric_Calc_pop','location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width=1050,height=740');
			else
			popup_win(top.JS_WEB_ROOT_PATH+'/addons/toric/index.php','Toric_img_pop','location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width=1050,height=680');
		break;
	}
}
//function to open physician conolse window from top icon bar
function physician_console(params, msgId, action){
	var thisClientHeight = $(document).height();
	var features = 'left=0,top=10,width='+(top.innerWidth-50)+',height='+(thisClientHeight-22)+',menuBar=no,scrollBars=yes,toolbar=0,resizable=yes';

	if( msgId !== undefined && parseInt(msgId) > 0  )
		document.cookie = 'console-data-id='+msgId;
	else
		document.cookie = 'console-data-id=null';

	if( action !== undefined && action != ''  )
		document.cookie = 'action='+action;
	else
		document.cookie = 'action=null';

	if(typeof(params)=='undefined'){params = '';}else{params = '#'+params;}
	var physicianconsole2 = window.open(top.JS_WEB_ROOT_PATH+'/interface/physician_console/index.php'+params,'Console2',features);
	physicianconsole2.focus();

	//$('#div_core_notifications').slideUp();
}

var show_phy_checkin_appts_flg;
function show_phy_checkin_appts(flg){
	if(typeof(flg)!="undefined"){
		if(flg==1){show_phy_checkin_appts_flg = setTimeout(function(){  show_phy_checkin_appts(); }, 500);}
		else if(flg==2){clearTimeout(show_phy_checkin_appts_flg);}
		return;
	}

	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/scheduler/physician_scheduler/checkin_list_hover.php',
		type:'POST',
		success:function(response){
			if(response.search('!~~!') > 0){
				response = response.split('!~~!');
				if(response[0] !== 'no'){
					$('#phy_checkin_appts').show();
					setTimeout(function(){
						$('#phy_checkin_appts #phy_sch_prov').html(response[0]);
						$('#phy_checkin_appts .modal-body').html(response[1]);
					}, 500);
				}else{
					$('#phy_checkin_appts').hide();
				}
			}
		}
	});

	//Hiding Modal
	$("#phy_checkin_appts .modal-content").mouseleave(function(){
		$('#phy_checkin_appts').hide();
		$('#phy_checkin_appts').load(location.href + " #phy_checkin_appts > *");
	});
}

//For setting popover for Pt. search
function set_mobile_pt_search(){
	$('.ptsrchbut').popover({
		//container: 'body',
		trigger:'click',
		html: true,
		placement: 'bottom',
		content: function () {
			//Changing Form submit function to work with popover
			var popover_html = $(this).parent('div').find('.popover_search:first');
			popover_html.find('form').removeAttr('onsubmit');
			popover_html.find('form').attr('onsubmit','return valid_popover(this)');		//Changed function as it was not available to the popover
			popover_html.find('form').find('input#patient').attr('value',$('#patient_id',top.document).val()); //Setting Patient id in the input box
			return popover_html.html();
		}
	});

	//Setting Popover width on show
	$('.ptsrchbut').on('shown.bs.popover', function () {
		top.main_search_dd_behavior();
		var popover_div = $(this).next('.popover');
		var popover_width = popover_div.width();
		var popover_position = popover_div.offset();

		var popover_full_width = parseInt(popover_width + popover_position.left);
		var popover_placement = parseInt($(window).width() - popover_full_width);
		popover_div.css('left',popover_placement);
	});
}

function valid_popover(obj){
	var patient_val = $(obj).find('#patient').val();
	if($.trim(patient_val) == ''){
		return false;
	}
}

//Consult Letters --
function opConsult(qry, obja)
{
	$("#btn_send_fax").addClass("hidden");
	if(typeof(obja)!="undefined" && obja){
		var u = $(obja).data("url")||"";
		var tr= $(obja).data("target")||"";
		if(tr == "consent_data" && u != ""){
			top.$("#consult_data_id").attr("src", ""+u);
			var fax_btn = top.$("#list_div_pcl").data("fax-btn")||"";
			var fax_log_btn = top.$("#list_div_pcl").data("fax-log-btn")||"";
			var email_btn = top.$("#list_div_pcl").data("email-btn")||"";
			if(fax_btn=="1"){	$("#sendFaxBtn").removeClass("hidden");	}
			if(fax_log_btn=="1"){	$("#send_fax_Log").removeClass("hidden");	}
			if(email_btn=="1"){	$("#sendEmailBtn").removeClass("hidden");	}
		}
		return;
	}

	top.show_loading_image('show', '', 'Processing. Please hold a moment...');
	if(typeof(qry)=="undefined"){ qry=""; }
	var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/onload_wv.php?elem_action=Pt_consult_letters"+qry;
	$.get(url, function(data){
			if(data!=""){
				if(qry!=""){
					top.$("#list_div_pcl").html(data);
					top.$("#consult_data_id").attr("src", "");
				}else{

					var xhgt = top.$(window).height();
					var xwdth = top.$(window).width();
					if($("#ptConsultLettersModal").length<=0){top.$("body").append(data);}
					else{$("#ptConsultLettersModal").replaceWith(data);}
					top.$('#ptConsultLettersModal').on('shown.bs.modal', function () {
					    top.$(this).find('.modal-dialog').css({width:'auto',
								       height:'auto',
								      'max-height':'100%'}); //margin:'30px auto',
						top.$(this).find('.modal-body').css({padding:'2px'});
						xhgt = parseInt(xhgt) - (parseInt(top.$(this).find('.modal-header').css("height")) + parseInt(top.$(this).find('.modal-footer').css("height")) + 50);
						top.$("#list_div_pcl").css({"max-height":xhgt,"overflow":"auto"});
						top.$("#consult_data_id").css({"height":xhgt});
					});
					top.$("#ptConsultLettersModal").modal("show");
				}
			}
			top.show_loading_image('hide');
		});
}
//Consult Letters --

//Phy Note --
function inter_phy_note(){
	top.show_loading_image('show', '', 'Processing. Please hold a moment...');
	var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/onload_wv.php?elem_action=physician_notes";
	$.get(url, function(data){

			if(data!=""){
				var xhgt = top.$(window).height();
				var xwdth = top.$(window).width();
				if($("#pt_physician_notesModal").length<=0){top.$("body").append(data);}
				else{$("#pt_physician_notesModal").replaceWith(data);}
				top.$('#pt_physician_notesModal').on('shown.bs.modal', function () {
				    top.$(this).find('.modal-dialog').css({width:'auto',
							       height:'auto',
							      'max-height':'100%'}); //margin:'30px auto',
					top.$(this).find('.modal-body').css({padding:'2px'});
					xhgt = (parseInt(xhgt) - (parseInt(top.$(this).find('.modal-header').css("height")) + parseInt(top.$(this).find('.modal-footer').css("height")) + 100))/2.5;
					top.$(this).find('.panel-body').css({height:xhgt+"px", "padding":"0px"});
					top.$(this).find('.modal-header').css({'cursor':'move'});
					top.$(this).draggable({'handle':'.modal-header'});

					//minimise
					top.$(this).find('span[class*="glyphicon"]').css({"font-size":"20px", "color":"purple"});

					//typeahead ---
					/*
					var autocomplete = top.$(this).find('textarea').typeahead();
					autocomplete.data('typeahead').source = tp_ah; //
					autocomplete.data('typeahead').updater = function(item){
							return item;
						};
					*/
					//*

					top.$(this).find('textarea').bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, "");};});
					//*/
					//typeahead ---

					$("#save_phy_note").bind("click", function(){
							var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/saveCharts.php";
							var strsave=$("#frm_phy_notes").serialize();
							strsave+="&savedby=ajax";
							top.show_loading_image('show', '', 'Processing. Please hold a moment...');
							$.post(url, strsave, function(data) {
									top.show_loading_image('hide');
									if(data=="0"||data=="1"){
										$("#pt_physician_notesModal").modal("hide");
										if(data==1){ $("#chart_phy_note").removeClass("hidden"); }else{ $("#chart_phy_note").addClass("hidden"); }
									}
									else{console.log(data);}
								});
						});
					//top.$("#list_div_pcl").css({"max-height":xhgt,"overflow":"auto"});
					//top.$("#consult_data_id").css({"height":xhgt});
				});
				top.$("#pt_physician_notesModal").modal({backdrop: false}).modal("show");
			}
			top.show_loading_image('hide');
		});
}
function show_phy_notes(flg){
	//***
	var chk = $('#chart_phy_note').data('content');
	var mx = parseInt(parseInt($(window).height())*75/100);
	if(typeof(chk)=="undefined" || chk==""){
		top.show_loading_image('show', '', 'Processing. Please hold a moment...');
		$.get(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php?elem_formAction=show_phy_note",function(data){
				top.show_loading_image('hide');
				top.$('#chart_phy_note').popover('destroy');
				//top.$('#chart_phy_note').attr("title","<b>Physician Notes</b>");
				top.$('#chart_phy_note').attr("data-content",""+data);
				top.$('#chart_phy_note').popover({ container:'body', title: "<b>Physician Notes</b>", content: ""+data, html: true, placement: "bottom", animation: false});
				top.$('#chart_phy_note').popover('toggle');
				chk = top.$('#chart_phy_note').attr("aria-describedby");
				$("#"+chk+" .popover-title").css({"cursor":"move"});
				$("#"+chk).draggable({'handle':'.popover-title'});
				$("#"+chk+" .popover-content").css({"height":mx+"px", "overflow":"auto", "padding":"3px"});

			});
	}else{
		$('#chart_phy_note').popover("toggle");
		chk = top.$('#chart_phy_note').attr("aria-describedby");
		$("#"+chk+" .popover-title").css({"cursor":"move"});
		$("#"+chk).draggable({'handle':'.popover-title'});
		$("#"+chk+" .popover-content").css({"height":mx+"px", "overflow":"auto", "padding":"0px"});
	}
}
//Phy Note --

//Operative Note--
function inter_operative_note(shw_all, qry){

	if(typeof(shw_all)=="undefined"){shw_all="";}
	if(typeof(qry)=="undefined"){ qry=""; }
	top.show_loading_image('show', '', 'Processing. Please hold a moment...');
	var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/onload_wv.php?elem_action=Operative_notes"+qry;
	if(typeof(shw_all)!="undefined" && shw_all!=""){ shw_all = "&show_all=1"; url=url+shw_all; }

	$.get(url, function(data){
		if(data!=""){
			if(data=="Error01"){alert("Please open patient chart note.");return;}
			if(qry!=""){
				top.$("#list_div_pon").html(data);
				top.$("#frm_operative_notes").attr("src", "");
			}else{
				var xhgt = top.$(window).height();
				var xwdth = top.$(window).width();
				if($("#opnoteModal").length<=0){top.$("body").append(data);}
				else{$("#opnoteModal").replaceWith(data);}
				$(".modal-backdrop").remove();

				top.$('#opnoteModal').on('shown.bs.modal', function () {
					    top.$(this).find('.modal-dialog').css({width:'auto',
								       height:'auto',
								      'max-height':'100%'}); //margin:'30px auto',
						xhgt = (parseInt(xhgt) - (parseInt(top.$(this).find('.modal-header').css("height")) + parseInt(top.$(this).find('.modal-footer').css("height")) + 100));
						top.$(this).find('.modal-body').css({height:xhgt+"px", "padding":"2px"});


						if(shw_all==""){
							CKEDITOR.replace( 'elem_pnData', { width:'99%', height:xhgt-150+"px" } );
						}else{
							top.$("#frm_operative_notes").css({"height":xhgt});
						}

					});
				top.$("#opnoteModal").modal("show");
			}
			//
		}
		top.show_loading_image('hide');
	});

}
//Operative Note--

//Ref Follow Phy POP up --
function show_rf_foll_pu(){
	var t = $("#rf_foll_name").html();
	t = $.trim(t);
	if(t!=""){
		if($("#res_fel_div_modal").length<=0){
			top.show_loading_image('show', '', 'Processing. Please hold a moment...');
			$.get(JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php?elem_formAction=refer_phy_modal&req_ptwo=1", function(d){
				top.show_loading_image('hide');$("body").append(d);
				top.$('#res_fel_div_modal').on('shown.bs.modal', function () {
					var xhgt = top.$(window).height(); xhgt = (parseInt(xhgt) - (parseInt(top.$(this).find('.modal-header').css("height")) + parseInt(top.$(this).find('.modal-footer').css("height")) + 100));
					top.$(this).find('.modal-body').css({height:xhgt+"px","overflow":'auto'});
				});
				$("#res_fel_div_modal").modal("show");});
		}else{
			$("#res_fel_div_modal").modal("show");
		}
	}
}
//Ref Follow Phy POP up --

function LoadAccountingView(pat_id,enc_id,page_path){
	if(page_path=="enter_payment"){
		moduleHandlerURL = '../accounting/makePayment.php?encounter_id='+enc_id+'&del_charge_list_id=0';
		LoadPtThenModule(pat_id,moduleHandlerURL);
	}else if(page_path=="enter_charges"){
		moduleHandlerURL = '../accounting/accounting_view.php?encounter_id='+enc_id+'&del_charge_list_id=0';
		LoadPtThenModule(pat_id,moduleHandlerURL);
	}
}

//pt edu alert
//ajax function to get pt edu alert
function get_pt_edu_alert(msg, closeStatus, callingWindow){
	callingWindow = callingWindow || "";
	$.ajax({
		url: JS_WEB_ROOT_PATH+"/interface/chart_notes/pt_instructions/show_pt_instructions_ajax.php?elem_formAction=get_pt_edu_alert",
		success: function(resp){
			//alert(resp);
			var arr_resp = resp.split("~~");
			if(arr_resp[0] == "yes" && arr_resp[3]){
				top.document.getElementById("divCommonAlertMsgNew").innerHTML = arr_resp[3];
				top.document.getElementById("divCommonAlertMsgNew").style.display='inline-block';

			}
			if(top.fmain && top.fmain.update_toolbar_icon){top.fmain.update_toolbar_icon();}

			if(msg){
				top.fAlert(msg);
			}
			//for chart note/test backward compatibility
			if(typeof(closeStatus) == 'undefined') {
				closeStatus = '';
			}
			if(closeStatus=='closeYes' && callingWindow != ""){
				callingWindow.close()
				//callingWindow.close();
			}
		}
	});
}

function given_pt_edu() {
	var hiddCntEdu = top.document.getElementById("hiddCntEdu").value;
	var hiddEduId = top.document.getElementById("hiddEduId").value;
	var hiddEduIdSplit = hiddEduId.split(',');
	var chbxObj='';
	var chbxVal='';
	var j=0;
	if(hiddEduIdSplit.length>0) {
		for (var i=0;i < hiddEduIdSplit.length;i++) {
			chbxObj = top.document.getElementById("chbx_pt_edu_"+hiddEduIdSplit[i]);
			if(chbxObj.checked==true) {
				if(j==0) {
					chbxVal= chbxObj.value;
				}else {
					chbxVal+=','+chbxObj.value;
				}
				j++;
			}

		}
		if(chbxVal) {
			top.show_loading_image("show");
			$.ajax({
				url: JS_WEB_ROOT_PATH+"/interface/chart_notes/pt_instructions/show_pt_instructions_ajax.php?idCommaSep="+chbxVal,
				success: function(resp){
					//alert(resp);
					//top.get_pt_edu_alert();
					if(top.fmain && top.fmain.update_toolbar_icon){top.fmain.update_toolbar_icon();}
					top.show_loading_image("hide");
					top.document.getElementById("divCommonAlertMsgNew").style.display='none';
					cancel_pt_edu_alert();//it will remove alert from current patient session
				}
			});
		}
	}
}

function cancel_pt_edu_alert(){
	$("#divCommonAlertMsgNew").hide();
	$.get(JS_WEB_ROOT_PATH+"/interface/chart_notes/pt_instructions/show_pt_instructions_ajax.php?cancel_alert=Y", function(r){if($.trim(r)=='Y'){}});
}


// End pt edu alert --

function get_set_pat_acc_status(r){
	if( typeof r == 'undefined' ) {
		$("#pt_account_status").find("div.modal-body").html('<div class="loader"></div>');
		top.master_ajax_tunnel('ajax_handler.php?task=pat_acc_status',top.get_set_pat_acc_status,'','json','optional');
	}
	else
		if( r.data ) {
			$("#pt_account_status").modal('show').find("div.modal-body").html(r.data);
		}

}

function PeriodicCheckNotification(firstCall){
	firstCall = typeof(firstCall)=='undefined' ? 'false' : 'true';
	// var height = $(window).innerHeight();
	// if(typeof(top.browser_env)!='undefined' && top.browser_env=='ipad'){
	// 	$('#fmain').height(height+110);
	// 	$('#core_main_content').height(height+110);
	// }else{
	// 	$('#fmain').height(height-100);
	// 	$('#core_main_content').height(height-100);
	// }
	$.ajax({
		type: "POST",
		url:top.JS_WEB_ROOT_PATH+'/interface/core/ajax_handler.php',
		dataType:'JSON',
		data:'task=periodic_check&from=core&params=sessheight,notifier&ptcommfirstcall='+firstCall,
		success: function(r){
			txt = r;
			if(txt != null && typeof(txt)!='undefined' && typeof(txt.notifier)!='undefined'){
				ShowCoreNotifications(txt.notifier);
				var timeoutID = window.setTimeout(function(){PeriodicCheckNotification(true);}, 120000);
			}else{var timeoutID = window.setTimeout(PeriodicCheckNotification, 120000);}
		}
	});
}

function managePhyIcon(iconCall, obj){
	if(!iconCall || typeof(iconCall) == 'undefined' || iconCall == '') iconCall = 'default' ;
	if(obj == '' || typeof(obj) !== 'object') obj = $('#notifier_icon .infoIcn');

	switch(iconCall){
		case 'blink':
			if(obj.hasClass('default') == true) obj.removeClass('default');
			if(obj.hasClass('pulse') == false) obj.addClass('pulse');
		break;

		case 'notify':
			if(obj.hasClass('pulse') == true) obj.removeClass('pulse');
			if(obj.hasClass('default') == true) obj.removeClass('default');
		break;

		case 'default':
			if(obj.hasClass('pulse') == true) obj.removeClass('pulse');
			if(obj.hasClass('default') == false) obj.addClass('default');
		break;
	}
}

function ShowCoreNotifications(flag){
	var obj = $('#notifier_icon');
	var iconObj = obj.find('.infoIcn');
	var tabToShow = 'get_messages_reminders';

	if(obj.hasClass('hide') == true) obj.removeClass('hide');

	if(flag.unread_messages_status && parseInt(flag.unread_messages_status) > 0){
		managePhyIcon('blink', iconObj);
		tabToShow = 'get_messages_reminders';
	}else if(parseInt(flag.un_consent_form_status)>0 || parseInt(flag.un_sx_consent_forms_status)>0 || parseInt(flag.un_op_notes_status)>0 || parseInt(flag.un_consult_letters_status)>0){
		managePhyIcon('notify', iconObj);
		tabToShow = 'get_forms_letters';
	}else if(parseInt(flag.unread_scan_docs_status)>0 || parseInt(flag.phy_notes)>0){
		managePhyIcon('notify', iconObj);
		tabToShow = 'get_tests_tasks';
	}else if(parseInt(flag.unread_scan_docs_status)>0 && (parseInt(flag.un_consent_form_status)==0 || parseInt(flag.un_sx_consent_forms_status)==0 || parseInt(flag.un_op_notes_status)==0 || parseInt(flag.un_consult_letters_status)==0)){
		tabToShow = 'get_tests_tasks';
	}else{
		managePhyIcon('default', iconObj);
	}

	//Binding Event
	$('#notifier_icon').unbind('click');
	$('#notifier_icon').on('click', function(){
		initBubbleModal('expand_notifications','',tabToShow);
	});

	//If modal is visible and their is any change in notifications open that block
	if($('#div_user_bubble').hasClass('show') == true && $('#div_user_bubble').data('show') == false){
		var targetElem = $('#div_user_bubble .modal-body .nav-tabs li.active a').data('id');
		if(targetElem == '' || typeof(targetElem) == 'undefined' || tabToShow == 'get_messages_reminders') targetElem = tabToShow;
		initBubbleModal('expand_notifications', targetElem, targetElem);
	}else{
		if($('#div_user_bubble').hasClass('show') == true) $('#div_user_bubble').removeClass('show');
		if($('#div_user_bubble').hasClass('hide') == false) $('#div_user_bubble').addClass('hide');
	}
}

function initBubbleModal(task, reqSection, tabToShow){
	if(reqSection == '' || typeof(reqSection) == 'undefined'){
		var validate = toggleBubble();
		if(validate == false) return false;
	}

	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/physician_console/ajax_html.php',
		data:'from=core&task='+task+'&reqSection='+reqSection,
		type:'POST',
		dataType:'JSON',
		beforeSend:function(){
			top.show_loading_image('show');
		},
		success:function(response){
			if(Object.keys(response).length){
				var strHtml = [];
				$.each(response, function(tab, tabArr){
					//Create HTML for each tab received in Response
					var htmlContent = createPhyBubbleSection(tab, tabArr, tabToShow);
					strHtml.push(htmlContent);
				});

				//If reqSection is given that means call is made for single Sections
				if(typeof(reqSection) == 'undefined' || reqSection == '') showPhyBubble(strHtml,'all');
				//Else call is made to get all the sections
				else showPhyBubble(strHtml,'single', reqSection);
			}
			return false;
		},
		complete:function(){
			top.show_loading_image('hide');

			//Binding Click events
			bubbleClickEvents();
		}
	});
}

function bubbleClickEvents(callFrom){
	$('#div_user_bubble .bubbleClose, .bubbleActions').unbind('click');
	$('#div_user_bubble a[data-toggle="tab"]').unbind('click');
	$('#div_user_bubble a[data-toggle="tab"]').unbind('dblclick');

	//Click events
	$('#div_user_bubble .bubbleClose').on('click', function(){
		toggleBubble('hide');
	});

	//Refresh the clicked section
	$('#div_user_bubble a[data-toggle="tab"]').on('click', function (e) {
		var target = $(e.target).data('id'); // activated tab
		initBubbleModal('expand_notifications', target, target);
	});

	//Open the double clicked section in Physician Console
	$('#div_user_bubble a[data-toggle="tab"]').on('dblclick', function (e) {
		var target = $(this).data('section'); // Go to Section
		top.physician_console(target);
	});

	//Messages events
	$('.bubbleActions').on('click', function(){
		doBubbleActions($(this));
	});

	//Manage Height
	var mainHeight = $('#div_user_bubble .modal-body').height();
	$('#div_user_bubble .modal-body .tab-pane.active').css({
		'height' : parseInt(mainHeight - 50),
		'max-height' :  parseInt(mainHeight - 50),
		'overflowY' : 'auto'
	});
}

//Based on case performs tasks
function showPhyBubble(htmlArr, callFrom, section){
	switch(callFrom){
		//If Modal is called first time
		case 'all':
			var htmlHeader = '';
			var htmlBody = '';
			var finalHtml = '';

			if(htmlArr.length){
				var tmpHead = '';
				var tmpBody = '';

				$.each(htmlArr, function(id, strValue){
					if(strValue['header']) tmpHead += strValue['header'];
					if(strValue['body']) tmpBody += strValue['body'];
				});

				if(tmpHead !== '') htmlHeader = '<ul class="nav nav-tabs">'+tmpHead+'</ul>';
				if(tmpBody !== '') htmlBody = '<div class="tab-content">'+tmpBody+'</div>';
			}

			if(htmlHeader !== '' && htmlBody !== ''){
				finalHtml = 	'<div class="row">';
				finalHtml +=		'<div class="col-sm-12">';
				finalHtml += 			htmlHeader;
				finalHtml +=		'</div>';
				finalHtml +=		'<div class="col-sm-12">';
				finalHtml += 			htmlBody;
				finalHtml +=		'</div>';
				finalHtml += 	'</div>';
			}

			if(finalHtml !== ''){
				$('#div_user_bubble .modal-body').html(finalHtml);
				if($('#div_user_bubble').hasClass('hide') == true) $('#div_user_bubble').removeClass('hide');
				if($('#div_user_bubble').hasClass('show') == false) $('#div_user_bubble').addClass('show');
				$('#div_user_bubble').data('show', true);
			}
		break;

		//If a section is called within the bubble
		case 'single':
			var htmlBody = '';

			if(htmlArr.length){
				var tmpBody = '';

				$.each(htmlArr, function(id, strValue){
					if(strValue['body']) tmpBody += strValue['body'];
				});

				if(tmpBody !== '') {
					$('#div_user_bubble .modal-body .nav-tabs li').removeClass('active');
					$('#div_user_bubble .modal-body .tab-content .tab-pane').removeClass('active');
					$('#div_user_bubble .modal-body .tab-content .tab-pane').removeClass('in');

					var prevObj = $('#div_user_bubble .modal-body .tab-content').find('#'+section);

					$('#div_user_bubble .modal-body .nav-tabs li[data-id="'+section+'"]').addClass('active');

					var activeClass = (prevObj.hasClass('active') == true) ? 'active' : '';
					var inClass = (prevObj.hasClass('in') == true) ? 'in' : '';
					prevObj.remove();

					$('#div_user_bubble .modal-body .tab-content').append(tmpBody);
				}
			}
		break;
	}
}

//Toggle the bubble modal
function toggleBubble(task, param){
	if(task == '' || typeof(task) == 'undefined') task = 'bubble';
	var validate = true;

	switch(task){
		case 'bubble':
			if($('#div_user_bubble').hasClass('show')){
				$('#div_user_bubble').removeClass('show');
				$('#div_user_bubble .modal-body').empty();
				if($('#div_user_bubble').hasClass('hide') == false) $('#div_user_bubble').addClass('hide');

				validate = false;
			}
		break;

		case 'msgRead':
			var msgId = param.msgid;
			var section = param.section;

			var obj = $('.msgBlock_'+msgId+' .msgRow[data-msgid='+msgId+']');
			var showVal = obj.data('show');

			if(showVal == 1){
				if(obj.hasClass('hide') == true) obj.removeClass('hide');
				if(obj.hasClass('show') == false) obj.addClass('show');
				obj.data('show', 2);
			}else{
				if(obj.hasClass('hide') == false) obj.addClass('hide');
				if(obj.hasClass('show') == true) obj.removeClass('show');
				obj.data('show', 1);
			}
		break;

		case 'hide':
			if($('#div_user_bubble').hasClass('show')){
				$('#div_user_bubble').removeClass('show');
				$('#div_user_bubble .modal-body').empty();
				if($('#div_user_bubble').hasClass('hide') == false) $('#div_user_bubble').addClass('hide');

				//validate = false;
			}
		break;
	}

	return validate;
}

//Creates Physician Bubble Sections HTML
function createPhyBubbleSection(section, secData, selSec){
	if(section == null) return ;
	var returnHtml = [];

	var headerStr = '';
	var bodyStr = '';

	switch(section){
		case 'get_messages_reminders':
			var selected = (section == selSec) ? 'active' : '';
			//Creating Header Section
			headerStr = '<li class="'+selected+'" data-id="'+section+'"><a data-toggle="tab" href="#'+section+'" data-id="'+section+'" data-section="message_reminders_opt"> <span class="glyphicon glyphicon-envelope" ></span> Messages</a></li>';

			//Creating Body Section
			bodyStr = createSectionBody(section, secData, selected);

		break;

		case 'get_forms_letters':
			var selected = (section == selSec) ? 'active' : '';
			//Creating Header Section
			headerStr = '<li class="'+selected+'" data-id="'+section+'"><a data-toggle="tab" href="#'+section+'" data-id="'+section+'" data-section="forms_letters_opt"> <span class="	glyphicon glyphicon-pencil"></span> Forms</a></li>';

			//Creating Body Section
			bodyStr = createSectionBody(section, secData, selected);
		break;

		case 'get_tests_tasks':
			var selected = (section == selSec) ? 'active' : '';
			//Creating Header Section
			headerStr = '<li class="'+selected+'" data-id="'+section+'"><a data-toggle="tab" href="#'+section+'" data-id="'+section+'" data-section="test_tasks_opt"> <span class="glyphicon glyphicon-list-alt"></span> Tasks</a></li>';

			//Creating Body Section
			bodyStr = createSectionBody(section, secData, selected);
		break;
	}

	returnHtml['header'] = headerStr;
	returnHtml['body'] = bodyStr;

	return returnHtml;
}

//Converts \n\r to <br>
function nl2br (str, is_xhtml) {
	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

//Creates Physician Bubble Body Section HTML
function createSectionBody(section, arrData, activeSec){
	if(activeSec && activeSec !== '' && typeof(activeSec) !== 'undefined') activeSec = "active in";
	var returnHtml = '';

	switch(section){
		case 'get_messages_reminders':
			if(arrData && typeof(arrData) !== 'undefined' && arrData.length){
				var strVal = '';
				$.each(arrData, function(id, val){
					strVal += '<div class="panel panel-default">';
					strVal += 	'<div class="panel-heading">';
					strVal += 		'<div class="row">';
					strVal += 			'<div class="col-sm-7">';
					strVal += 				'<h4 class="panel-title pointer" data-toggle="collapse" data-parent="#accordion'+section+'" href="#collapse'+id+'">';
					strVal += 					'<span><strong>'+val['message_sender_name']+'</strong></span>';
					strVal += 				'</h4>';
					strVal += 			'</div>';
					strVal += 			'<div class="col-sm-5 text-right">';
					strVal += 				'<span>'+val['msg_send_date']+'</span>';
					strVal += 			'</div>';
					strVal += 		'</div>';

					strVal += 	'</div>';

					strVal += 	'<div id="collapse'+id+'" class="panel-collapse collapse msgBlock_'+val['user_message_id']+' msgBlocks">';
					strVal +=		'<div class="panel-body">';
					strVal += 			'<div class="row">';

					//Gettng Patient Details
					var ptId = '';
					var ptName = '';

					if(val['patient_name'] && val['patient_name'] !== ''){
						var ptArr = val['patient_name'].split('-');
						ptName = ptArr[0];
						ptId = ptArr[1];
					}

					if(ptId !== '' && ptName !== ''){
						strVal += 				'<div class="col-sm-12">';
						strVal += 					'<label class="text_purple"><strong class="pointer bubbleActions" data-html="true" data-placement="left" data-trigger="manual" data-patient='+ptId+' data-action="getptdetails" data-section="'+section+'" data-msgid="'+val['user_message_id']+'" data-container="body">'+ptName+'</strong> - <strong data-action="loadworkview" data-patient='+ptId+' class="pointer bubbleActions" data-section="'+section+'" data-perform="true">'+ptId+'</strong></label>';
						strVal += 				'</div>';
					}

					//Get Message details
					strVal += 				'<div class="col-sm-12">';

					var msgLength = 50;
					var msgId = val['user_message_id'];
					var readStatus = val['message_read_status'];
					var senderId = val['message_sender_id'];
					var msgSubject = val['message_subject'];
					var bold = (readStatus == 0) ? 'unread' : '';
					//Filtering Message text
					var msgParts = val['message_text'].split('<table');
					val['message_text'] = msgParts[0];
					if(msgParts[1] && msgParts[1] !== ''){
						val['message_text'] += '<br><div style="text-align:right"><a href="javascript:void(0);" onclick="physician_console(2,\'ptComm_tab1\');" class="a_clr1 unBold text_small">View Complete CL Order...</a></div>';
					}

					var longMsg = '<p style="margin:0px;">'+nl2br(val['message_text'])+'</p>';
					var strippedMsg = longMsg;
					if(val['message_text'].length > msgLength){
						strippedMsg = strippedMsg.substr(0, msgLength);
					}

					strVal += 					'<div class="row">';
					strVal += 						'<div class="col-sm-9">';
					strVal += 							'<label id="" class="bubbleActions pointer '+bold+'" title="Click to mark read" data-msgid="'+msgId+'" data-action="markRead" data-section="'+section+'">'+strippedMsg+'</label>';
					strVal += 						'</div>';
					strVal += 						'<div class="col-sm-3 text-right iconBlock">';
					strVal += 							'<span class="bubbleActions glyphicon glyphicon-edit pointer" title="Quick Reply" data-msgid="'+msgId+'" data-senderid="'+senderId+'" data-patientid="'+ptId+'" data-subject="'+msgSubject+'" data-action="quickReply" data-section="'+section+'" data-perform="true"></span>';
					strVal += 							'<span class="bubbleActions glyphicon glyphicon-check pointer" title="Mark as Done" data-msgid="'+msgId+'" data-action="markDone" data-section="'+section+'"></span>';
					strVal += 						'</div>';

					//Hidden Row to show Logn text
					strVal += 						'<div class="clearfix"></div>';
					strVal += 						'<div class="col-sm-12 hide msgRow" data-msgid="'+msgId+'" data-show="1">';
					strVal += 							'<span>'+longMsg+'</span>';
					strVal += 						'</div>';


					strVal += 					'</div>';
					strVal += 				'</div>';
					strVal += 			'</div>';
					strVal +=		'</div>';
					strVal += 	'</div>';
					strVal += 	'</div>';

				});

				if(strVal !== '') returnHtml = '<div class="panel-group" id="accordion'+section+'">'+strVal+'</div>';

			}else{
				returnHtml = '<div class="alert alert-info">No Messages</div>';
			}
		break;

		case 'get_forms_letters':
			if(arrData && typeof(arrData) !== 'undefined' && arrData.length){
				var strVal = '';
				$.each(arrData, function(id, val){
					//Change Image based on Form Type
					var imgSrc = '';
					switch(val['form_type']){
						case 'consent_form':
							imgSrc = 'flag_unread_cf.png';
						break;
						case 'opnotes':
							imgSrc = 'flag_unread_opnote.png';
						break;
						case 'consult_letters':
							imgSrc = 'flag_unread_consult.png';
						break;
						case 'sx_consent_form':
							imgSrc = 'flag_unread_sxconsent.png';
						break;
					}

					strVal += '<li class="list-group-item">';
					strVal += 	'<div class="row">';
					strVal += 		'<div class="col-sm-12">';
					strVal += 			'<div class="row">';
					strVal += 				'<div class="col-sm-9">';
					strVal += 					'<label><strong>'+val['form_name']+'</strong></label>';
					strVal += 				'</div>';
					strVal += 				'<div class="col-sm-3 text-right">';
					strVal += 					'<img src="'+top.JS_WEB_ROOT_PATH+'/library/images/'+imgSrc+'"/>';
					strVal += 				'</div>';
					strVal += 			'</div>';
					strVal += 			'<div class="row">';
					strVal += 				'<div class="col-sm-6">';
					strVal += 					'<label>'+val['patient_name']+'</label>';
					strVal += 				'</div>';
					strVal += 				'<div class="col-sm-6 text-right">';
					strVal += 					'<label>'+val['chscre_on']+'</label>';
					strVal += 				'</div>';
					strVal += 			'</div>';
					strVal += 		'</div>';
					strVal += 	'</div>';
					strVal += '</li>';
				});

				if(strVal !== '') returnHtml = '<ul class="list-group">'+strVal+'</ul>';
			}else{
				returnHtml = '<div class="alert alert-info">No form/letter pending for signature.</div>';
			}
		break;

		case 'get_tests_tasks':
			if(arrData && typeof(arrData) !== 'undefined' && arrData.length){
				var strVal = '';
				$.each(arrData, function(id, val){
					if(val['folder_name'] && typeof(val['folder_name']) !== 'undefined'){
					strVal += '<li class="list-group-item">';
					strVal += 	'<div class="row">';
					strVal += 		'<div class="col-sm-6">';
					strVal += 			'<label><strong>'+val['patient_name']+'</strong></label>';
					strVal += 		'</div>';
					strVal += 		'<div class="col-sm-6 text-right">';
					strVal += 			'<label>'+val['doc_upload_date']+'</label>';
					strVal += 		'</div>';
					strVal += 	'</div>';
					strVal += 	'<div class="row">';
					strVal += 		'<div class="col-sm-12">';
					strVal += 			'<label><strong>Folder</strong> : '+val['folder_name']+'</label>';
					strVal += 		'</div>';
					if(val['doc_comments'] && val['doc_comments'] !== ''){
						strVal += 		'<div class="col-sm-12">';
						strVal += 			'<label><strong>Comments</strong> : '+val['doc_comments']+'</label>';
						strVal += 		'</div>';
					}
					strVal += 	'</div>';
					strVal += '</li>';}

				});
				if(strVal !== '') returnHtml = '<ul class="list-group">'+strVal+'</ul>';
				//section to get accounting notes
				var strVal = '';
				$.each(arrData, function(id, val){
					if(val['task_id'] && typeof(val['task_id']) !== 'undefined'){
					strVal += '<li class="list-group-item">';
					strVal += 	'<div class="row">';
					strVal += 		'<div class="col-sm-6">';
					strVal += 			'<label><strong>'+val['ptname']+'</strong></label>';
					strVal += 		'</div>';
					strVal += 		'<div class="col-sm-6 text-right">';
					strVal += 			'<label>'+val['task_date_time']+'</label>';
					strVal += 		'</div>';
					strVal += 	'</div>';
					strVal += 	'<div class="row">';
					strVal += 		'<div class="col-sm-12">';
					strVal += 			'<label><strong>Assigned by </strong> : '+val['phyname']+'</label>';
					strVal += 		'</div>';
					if(val['comment'] && val['comment'] !== ''){
						strVal += 		'<div class="col-sm-12">';
						strVal += 			'<label><strong>Comments</strong> : '+val['comment']+'</label>';
						strVal += 		'</div>';
					}
					strVal += 	'</div>';
					strVal += '</li>';}

				});

				if(strVal !== '') returnHtml += '<ul class="list-group">'+strVal+'</ul>';

			}else{
				returnHtml = '<div class="alert alert-info">No task pending for review.</div>';
			}
		break;

		default:
			returnHtml = '';
	}
	if(returnHtml !== '') returnHtml = '<div class="tab-pane fade '+activeSec+'" id="'+section+'">'+returnHtml+'</div>';
	return returnHtml;
}

//This function handles various actions performed within the bubble
function doBubbleActions(obj){
	if(!obj || typeof(obj) == 'undefined' || obj == '') return false;
	var dataArr = obj.data();

	var section = (dataArr && dataArr.section && typeof(dataArr.section) !== 'undefined') ? dataArr.section : '';
	var action = (dataArr && dataArr.action && typeof(dataArr.action) !== 'undefined') ? dataArr.action : '';

	if(section !== '' && action !== ''){

		//If performTask is provided that means perform some task and continue the ajax part
		if(dataArr.perform && typeof(dataArr.perform) !== 'undefined' && dataArr.perform !== '' && dataArr.perform == true){
			switch(section){
				case 'get_messages_reminders':
					//Perform task based on provided action
					switch(action){
						case 'quickReply':
							//Init. Quick Reply box
							showReplyBox(obj);
						break;

						case 'loadworkview':
                            //To check restrict access of patient before load
                            $.when(check_for_break_glass_restriction(dataArr.patient)).done(function(response){
                                top.removeMessi();
                                if(response.rp_alert=='y') {
                                    var patId=response.patId;
                                    var bgPriv=response.bgPriv;
                                    var rp_alert=response.rp_alert;
                                    top.core_restricted_prov_alert(patId, bgPriv, '');
                                } else {
                                    //load Patient in workview
                                    load_patient(dataArr.patient);
                                }
                            });
						break;
					}
				break;
			}
			return false;
		}

		$.ajax({
			url:top.JS_WEB_ROOT_PATH+'/interface/physician_console/ajax_html.php',
			type:'POST',
			data:{task:'performTask', params:dataArr, from:'core'},
			dataType:'JSON',
			beforeSend:function(){
				top.show_loading_image('show');
			},
			success:function(resp){
				if(Object.keys(resp).length){
					//If Their is any error
					if(resp.error && resp.error !== ''){
						top.fAlert(resp.error);
						return false;
					}

					//What to do on response for specific section
					switch(section){
						//Messages Block
						case 'get_messages_reminders':
							switch(action){
								case 'markRead':
									if(resp.success && resp.success == true){
										var obj = $('.msgBlock_'+dataArr.msgid).find('.bubbleActions');
										if(obj.hasClass('unread')) obj.removeClass('unread');

										toggleBubble('msgRead', dataArr);
									}
								break;

								case 'markDone':
									if(resp.success && resp.success == true){
										var obj = $('.msgBlock_'+dataArr.msgid).parent();
										obj.remove();

										if($('.msgBlocks').length == 0) initBubbleModal('expand_notifications', section, section);
									}
								break;

								case 'quickReply':
									if(resp.success && resp.success == true){
									 	var obj = $('#replyBox_'+dataArr.msgid);
										obj.find('.row').html('<div class="alert alert-success">Reply Sent successfully</div>');
										setTimeout(function(){
											obj.remove();
										}, 2000);
									}
								break;

								case 'getptdetails':
									if(Object.keys(resp).length > 0 && !resp.error){
										var ptName = resp.lname+', '+resp.fname+' '+resp.mname;
										var gender = resp.sex;
										var dob = resp.DOB;

										//Address Details
										var address = resp.street;
										if(resp.street2 && resp.street2 !== '') address += ', '+resp.street2;

										//City, State, Zip
										var cityZip = '';
										cityZip = resp.city;

										if(cityZip !== '') cityZip += ', '+resp.state;
										else cityZip = resp.state;

										if(cityZip !== '' && resp.postal_code && resp.postal_code !== '') cityZip += ' - '+resp.postal_code;
										else cityZip = resp.postal_code;

										if(cityZip !== '' && resp.zip_ext && resp.zip_ext !== '') cityZip += '-'+resp.zip_ext;
										if(cityZip !== '') cityZip = '<br>'+cityZip+'<br>';

										//Contact Details
										var phoneHome = '';
										var phoneWork = '';
										var phoneMobile = '';
										if(resp.phone_home && resp.phone_home !== '')  phoneHome = resp.phone_home;
										if(resp.phone_biz && resp.phone_biz !== '')  phoneWork = resp.phone_biz;
										if(resp.phone_cell && resp.phone_cell !== '')  phoneMobile = resp.phone_cell;

										//Appoitnment Details
										var apptData = 'N/A';
										if( resp.ptAppt && (resp.ptAppt.appt_dt_time && resp.ptAppt.appt_dt_time !== '') ){
											var apptDt = resp.ptAppt;
											var facName = (apptDt.facility_name && apptDt.facility_name !== '') ? apptDt.facility_name : '';
											var apptDate = (apptDt.appt_dt_time && apptDt.appt_dt_time !== '') ? apptDt.appt_dt_time : '';
											var phyName = (apptDt.phy_init_name && apptDt.phy_init_name !== '') ? apptDt.phy_init_name : '';

											apptData = phyName+' / '+apptDate+' / '+facName;
										}

										var ptImage = (resp.p_imagename && resp.p_imagename !== '') ? '<img src="'+resp.dataPath+resp.p_imagename+'" alt="'+ptName+'" >' : '';

										var htmlStr = '';
										htmlStr += '<div class="row">';
										htmlStr += 		'<div class="col-sm-3">'+ptImage+'</div>';
										htmlStr += 		'<div class="col-sm-9">';
										htmlStr += 			'<div class="row">';

										htmlStr += 				'<div class="col-sm-8"><label><strong>Name</strong> : '+ptName+'</label></div>';
										htmlStr += 				'<div class="col-sm-4 text-right text-danger"><span class="glyphicon glyphicon-remove" onClick=\'$("#showBox").remove();\'></span></div>';
										htmlStr += 				'<div class="clearfix"></div>';

										htmlStr += 				'<div class="col-sm-6"><label><strong>Gender</strong> : '+gender+'</label></div>';
										htmlStr += 				'<div class="col-sm-6"><label><strong>DOB</strong> : '+dob+'</label></div>';
										htmlStr += 				'<div class="clearfix"></div>';

										htmlStr += 				'<div class="col-sm-12"><label><strong>Address</strong> : '+address+'</label></div>';
										htmlStr += 				'<div class="clearfix"></div>';

										htmlStr += 				'<div class="col-sm-6"><label><strong>Home</strong> : '+phoneHome+'</label></div>';
										htmlStr += 				'<div class="col-sm-6"><label><strong>Work</strong> : '+phoneWork+'</label></div>';
										htmlStr += 				'<div class="clearfix"></div>';

										htmlStr += 				'<div class="col-sm-6"><label><strong>Cell</strong> : '+phoneMobile+'</label></div>';
										htmlStr += 				'<div class="col-sm-6"><label><strong>Email</strong> : '+resp.email+'</label></div>';
										htmlStr += 				'<div class="clearfix"></div>';

										htmlStr += 				'<div class="col-sm-12"><label><strong>Appt</strong> : '+apptData+'</label></div>';

										htmlStr += 			'</div>';
										htmlStr += 		'</div>';
										htmlStr += '</div>';

										var obj = $('.msgBlock_'+dataArr.msgid).find('.bubbleActions[data-patient='+resp.id+'][data-action="'+action+'"]');

										//Creating Element
										var divEle = $('<div></div>');

										divEle.attr({
											//'id' : 'showBox_'+dataArr.msgid,
											'id' : 'showBox',
											'class' : 'bubbleShowBox col-sm-3 well well-sm',
											'data-id' : dataArr.msgid,
											'data-show' : 'true'
										});

										if($('#showBox').length) $('#showBox').html(htmlStr);
										else{
											$('body').append(divEle);
											divEle.html(htmlStr);
										}

										// //Identifier to get Current Active Patient Details popup
										// var mainShowID = $('#div_user_bubble').data('msgid');
										//
										// //If empty than it's first Call
										// if(mainShowID == ''){
										// 	$('body').append(divEle);
										// 	$('#div_user_bubble').data('msgid', dataArr.msgid);
										// }else{
										// 	//If Both ID's are same than hide & remove current div and append the new one
										// 	var currentEle = $('#showBox_'+dataArr.msgid);
										// 	if(dataArr.msgid == mainShowID && currentEle.data('show')) currentEle.remove();
										// 	else{
										// 		$('body').append(divEle);
										// 		$('#div_user_bubble').data('msgid', dataArr.msgid);
										// 	}
										// }

										//Setting Position
										if($('#showBox').length){
											var objPosition = $('#div_user_bubble').position();
											$('#showBox').css({
												'top' : obj.offset().top,
												'left' : parseInt(objPosition.left - ($('#showBox').width() + 15))
											});
										}
									}
								break;
							}
						break;
					}
				}

				return false;
			},
			complete:function(){
				top.show_loading_image('hide');
			}
		});
	}


	function showReplyBox(obj){
		var dataArr = obj.data();

		//If same ID box exists remove it
		if(obj.closest('.row').find('#replyBox_'+dataArr.msgid).length){
			obj.closest('.row').find('#replyBox_'+dataArr.msgid).remove();
			return false;
		}

		//Creating Data attributes for HTML
		var objStr = [];
		if(Object.keys(dataArr).length > 0){
			$.each(dataArr, function(id, val){
				if(id !== 'perform' && val !== ''){
					var string = 'data-'+id+' = '+val;
					objStr.push(string);
				}
			});
		}

		//Creatng HTML
		var htmlStr = '';
		htmlStr += '<div class="col-sm-12" id="replyBox_'+dataArr.msgid+'">';
		htmlStr += 	'<div class="row">';
		htmlStr += 		'<div class="input-group">';
		htmlStr += 			'<textarea class="form-control custom-control" rows="2" style="resize:none"></textarea>';
		htmlStr += 			'<span class="input-group-addon btn btn-success sendReply" '+objStr.join(' ')+'>Send</span>';
		htmlStr += 		'</div>';
		htmlStr += 	'</div>';
		htmlStr += '</div>';

		obj.closest('.row').append(htmlStr);

		//Binding Events
		$("#replyBox_"+dataArr.msgid).find('.sendReply').on('click', function(){
			//Get Textarea String
			var msgText = $(this).siblings('textarea').val();
			if(!$(this).data('msgtext')) $(this).data('msgtext', msgText);

			//Perform Quick Reply ation
			doBubbleActions($(this));
		});

		return false;
	}
}

// Disable Backspace + short cut keys ----
 function keyCatcher(e){
	var event = e||window.event;
	if (event)
	{
		var flg=0;
		obj = event.srcElement;
 		if(event.keyCode == 8){
 			if(typeof(obj.type) == "undefined" || ((obj.type != 'text') && (obj.type != 'TEXTAREA') && (obj.type != 'textarea') && (obj.type != 'password'))){ //&& obj.id != 'elem_ccHx'
				flg=1;
 			}
 			else if((typeof(obj.type) != "undefined" && ((obj.type == 'textarea') || (obj.type == 'TEXTAREA')) && (obj.readOnly == true))){
				flg=1;
 			}

			if(flg==1 && $(obj).hasClass("redactor-editor")){flg=0;}

		}else if((event.keyCode == 81 || event.keyCode == 83 || event.keyCode == 69 || event.keyCode == 68)  && event.ctrlKey){ //ctrl + q
			var wv = $('#curr_main_tab').val();
			if(event.keyCode == 81){if($("#div_switch_user").css("display")!="block"){showSwitchUserForm();}flg=1;}
			else if($('#patient_id').val() != "" && wv=="Work_View"){
				if(event.keyCode == 83){$("#save").trigger("click");flg=1;}
				else if(event.keyCode == 69){
					if($("#div_pt_name").css("display")=="block"){
						$("#div_pt_name").css({"display":"none"});
						$("#save").trigger("click");
						setTimeout(function(){$("#closePt").trigger("click");},200);
						flg=1;
					}
				}else if(event.keyCode == 68){
					var flg = $("#ico_tp_sug").hasClass("hidden") ? 1 : 0;
					top.show_loading_image('show');
					$.get(top.JS_WEB_ROOT_PATH + "/interface/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=en_dis_TA&flg="+flg,	function(d){top.show_loading_image('hide');if(d==1){ $("#ico_tp_sug").addClass("hidden"); }else{$("#ico_tp_sug").removeClass("hidden");}});
					flg=1;
				}
			}
		}

		if(flg==1){
			if(event.keyCode != 68){console.log("Backspace is stopped here.");}
			  event.returnValue = false;
			  event.cancelBubble = true;
			  event.stopPropagation();
			  event.preventDefault();
		}
 	}
 }
// Disable Backspace ----

 //PVC ----
 var cnfrm = '';
function pt_comm_action(mod,del_id,con)
{
	var div = $("#pt_comm");
	var frm = $("#pat_commun_form");
	var view = $("#chk").is(':checked') ? 'view_all' : 'view_active';
	var dat = '';
	mod = mod || frm.find('#mode').val();
	del_id = del_id || '' ;
	cnfrm = con || cnfrm;
	if(mod == 'add' || mod == 'edit')
	{
		dat = frm.serialize();
		var sbj = frm.find("#pat_msg_sub").val();
		var msg = frm.find("#pat_msg_txt").val();

		if(sbj == '' ){
			top.fAlert("Please enter Subject.");
			return false;
		}
		else if(msg == '' ){
			top.fAlert("Please enter Message.");
			return false;
		}
	}
	else if(mod == 'delete')
	{
		dat = "del_id="+del_id;
	}

	if( !cnfrm && mod == 'delete' )
	{
		top.fancyConfirm("Do you really want to delete?", "", "top.pt_comm_action('"+mod+"','"+del_id+"','yes');");
		return false;
	}
	cnfrm = '';
	$inputs = div.find('input,textarea,button');
	$inputs.prop('disabled',true);

	top.show_loading_image('show');

	$.ajax({
		type: "POST",
		url: top.JS_WEB_ROOT_PATH + "/interface/chart_notes/requestHandler.php?elem_formAction=pt_comm&view="+view+"&mode="+mod,
		data: dat,
		complete:function(){
			top.show_loading_image('hide');
			$inputs.not("#el_pvc_op").prop('disabled',false);

		},
		success: function(respRes){
			//top.update_toolbar_icon();
			$("#pt_comm_data").html(respRes);
			reset_pt_comm_form();
		}
	});

}

function reset_pt_comm_form()
{
	$("#pat_msg_sub,#pat_msg_txt,#edit_id,#pat_msg_date").val('');
	$("#pat_msg_date").val(top.current_date());
	$("#approve_status").val('accept');
	$("#mode").val('add');
	$("#msg_accept").prop('checked',true);
	$("#msg_decline").prop('checked',false);

}

function pt_comm_all($_this)
{
	if( $($_this).is(':checked') )	pt_comm_action('view_all');
	else pt_comm_action('view_active');
}

function edit_pvc_message(id,subject,text,app_dec)
{
	if(typeof(id)=="undefined" || id==""){return;}
	top.show_loading_image('show');
	var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php?elem_formAction=load_pvc&eid="+id;
	$.get(url, function(data){
		top.show_loading_image('hide');
		//console.log(data);
		if(data){
			$("#edit_id").val(id);
			$("#mode").val('edit');
			subject = data.message_subject;
			$("#pat_msg_sub").val(subject);
			text = data.message_text;
			$("#pat_msg_txt").val(text);
			app_dec = data.approved;
			if(app_dec==""){app_dec="Accept";}
			$("#approve_status").val(app_dec.toLowerCase());
			if(app_dec=="Decline"){ $("#msg_decline").prop('checked', true);}
			else if(app_dec=="Accept"){$("#msg_accept").prop('checked', true);}
		}
	}, 'json');
}
function load_pvc(flg,tab){
	top.show_loading_image('show');
	if(typeof(flg)=="undefined" || $("#pt_comm").length<=0){
		if($("#pt_comm").length>0){ $("#pt_comm, .modal-backdrop").remove();}
		var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php?elem_formAction=load_pvc";
		$.get(url, function(data){
			top.show_loading_image('hide');
			if(data=="Please select Patient."){ top.fAlert(data);  return;}
			$("body").append(data);
            var dfmt='';
			if(typeof(tab)!="undefined" && tab=='docs_tab'){
                dfmt=window.top.opener.jQueryIntDateFormat;
            } else {
                dfmt=jQueryIntDateFormat;
            }
			$('#pt_comm').draggable({handle:'.modal-header'});
			$('#pt_comm').on('shown.bs.modal', function(){
				if( $("#pat_commun_form #pat_msg_date").length ==1 )
				    $("#pat_commun_form #pat_msg_date").datepicker({dateFormat:dfmt});
			    });
			$("#pt_comm").find('input,textarea').off('keyUp keyDown blur focus keyPress change click focusin focus out');

			load_pvc(1);
		});
	}else if($("#pt_comm").length>0){

		$("#pt_comm").modal('show');

	}
	top.show_loading_image('hide');
}
 //End PVC ----
function open_referrals(type) {
	type = type || 1;
	var url = '../../interface/common/referral_view.php?ref_type='+type;
	//top.fmain.location.href = url;
	top.popup_win(url);
}

// Send secure message from pt image portal icon
function send_secure_msg(callFrom){
	if(!callFrom || typeof(callFrom) == 'undefined'){
		$('#secureMsgModal').modal('show');
		$("#secureMsgModal #subject").val('');
		$("#secureMsgModal #body").val('');
		return false;
	}

	if(callFrom && callFrom == 'send_msg'){
		var subject = $("#subject").val();
		var body_txt = $("#body").val();
		var msgArr = [];

		if ($.trim(subject) == "") msgArr.push('Message Subject');
		if ($.trim(body_txt) == "") msgArr.push('Message Content');

		if(msgArr.length){
			var msgStr = 'Following fields are required : <br>';
			msgStr += msgArr.join('<br>');

			fAlert(msgStr);
			return false;
		}

		var frm_data = $('#secureMsgModal #frmForm').serialize();
        $.ajax({
            type: "POST",
            url: top.JS_WEB_ROOT_PATH+"/interface/physician_console/patient_messages.php",
            data: frm_data,
            success: function (r) {
				if(r && r == 'Message has been sent successfully'){
					fAlert('Message sent successfully');
				}else{
					fAlert('Message not send');
				}

				$('#secureMsgModal').modal('hide');
            }
        });
	}

}

//Change Role --
function change_role(o,rfrs){
	if(typeof(rfrs)=="undefined"){rfrs="";}
	var rl = o.value == "3" ? "Technician" : "Scribe";
	top.show_loading_image('show', '', 'Changing role to '+rl);
	$.get(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php?elem_formAction=change_role&req_ptwo=1&cr2="+o.value+"&pto="+rfrs, function(){ top.show_loading_image('hide'); if(rfrs=="1"){ window.location.reload(); } });
}
//Change Role --

function close_popwin(close_win){
	var tmp_popup = top.arr_opened_popups;
	if( tmp_popup ){
		var not_to_close = ['newPatientWindow', 'new_patient_info_popup_new', 'day_charges_list', 'patient_ar_aging', 'insurance_ar_aging','check_in_out_payment','ar_worksheet'];
		for(var i in tmp_popup){
			if(tmp_popup[i] && tmp_popup[i].closed == false && tmp_popup[i].name !== '' && !(not_to_close.indexOf(tmp_popup[i].name) > -1) )
			{
				if(typeof(close_win)!="undefined" && close_win!= "" && close_win!=tmp_popup[i].name){
					continue;
				}
				tmp_popup[i].close();
			}
		}
	}
}

/* UGA Finance Registration */
function apply_uga_finance(reqFrom){
	top.show_loading_image('show', '', 'Loading ...');
	$('#UGAPaitentStatus').modal('hide');
	if(reqFrom == 'demographics' || reqFrom == 'checkin') {
		$.ajax({
			url: top.JS_WEB_ROOT_PATH+"/interface/core/ajax_handler.php",
			type: 'POST',
			data: {task: 'apply_uga_finance'},
		})
		.done(function(response) {
			var resp = $.parseJSON(response);
			if(resp.status == 'success') {
				top.show_loading_image('hide');
				var w = screen.width - 100;
				window.open(resp.data,'','width='+w+',height=700,top=50,left=50,toolbar=0,location=0,statusbar=0,menubar=0');
			}
			if(resp.status == 'failed') {
				top.show_loading_image('hide');
				top.fAlert(resp.data);
			}
		});
	}
}

/* GET UGA Customer Status */
function get_uga_status(){
	top.show_loading_image('show', '', 'Loading ...');
	$.ajax({
		url: top.JS_WEB_ROOT_PATH+"/interface/core/ajax_handler.php",
		type: 'POST',
		data: {task: 'get_uga_status'},
	})
	.done(function(response) {
		// console.log(response);
		var res = $.parseJSON(response);
		top.show_loading_image('hide');
		top.show_modal('UGAPaitentStatus','UGA Finance Patient Status',res.html,res.btn,'350','modal_90');
	});
}

// Used in fancy confirm popup close
function rt_false() { return false; }

/* DSS electronic signature code for user */
function dssValidateElectronicSignature(action,id,callFrom,reqFrom){
	if(action == '' || typeof(action) == 'undefined') return false;

	if(reqFrom != 'saveChartNote')
		if(id == '' || typeof(id) == 'undefined') return false;

	var htmlStr =   '<div class="row">';
      htmlStr   +=  '<input type="hidden"  name="del_id" value="'+id+'" /> ';
      htmlStr   +=  '<div class="col-sm-12">';
      htmlStr   +=    '<div class="form-group">';
      htmlStr   +=      '<label for="">Electronic Signature Code</label>';
      htmlStr   +=      '<input type="password" name="electronicSignature" id="electronicSignature" value="" class="form-control" autocomplete="off" onselectstart="return false" onpaste="return false;" onCopy="return false" onCut="return false" />';
      htmlStr   +=    '</div>';
      htmlStr   +=  '</div>';
    htmlStr   +=  '</div>';

	var footerBtn = '<button class="btn btn-success" id="saveDSSInfo" type="button" onClick="top.dssValidateElectronicSignature(\'validate\', \''+id+'\', \''+callFrom+'\', \''+reqFrom+'\');">Validate</button><button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>';

	switch(action){
		case 'show':
			show_modal('DSSModal','Validate Electronic Signature',htmlStr,footerBtn);
		break;

		case 'validate':
			var dssModal = $('#DSSModal');
			var dss_elec_sign = dssModal.find('input[name=electronicSignature]').val();
			if(dss_elec_sign == ''){
				top.fAlert('Not allowed to be empty.');
				return false;
			}

			$.ajax({
				url: top.JS_WEB_ROOT_PATH + "/interface/core/ajax_handler.php",
				type: 'POST',
				dataType:'JSON',
				data:{
					task:'dssValidateElectronicSignature',
					electronicSignature:dss_elec_sign,
					reqFrom:reqFrom
				},
				beforeSend:function(){
					top.show_loading_image('show');
				},
				success:function(response){
					// console.log(response);
					top.show_loading_image('hide');

					if(response.status == 'success') {
						dssModal.modal('hide');
						if(reqFrom == 'saveChartNote' && response.vcode != "") {
							//alert('Success Triggerred');
							// Finalized chart note
							if ($.isFunction(top.fmain.saveMainPage)) {
								top.fmain.saveMainPage(0,1);
							}
							//alert('Success Passed');
						} else {
							if(callFrom != 'WV') {
								window.top.fmain.document.location.href = top.JS_WEB_ROOT_PATH +"/interface/Medical_history/index.php?showpage=medication&mode=delete&del_id="+id+"&vcode="+response.vcode;
							} else {
								document.location.href = top.JS_WEB_ROOT_PATH+"/interface/Medical_history/index.php?showpage=medication&callFrom=WV&subcall="+scf+"&divH="+document.getElementById("divH").value+"&mode=delete&del_id="+id+"&vcode="+response.vcode;
							}
						}
					}

					if(response.status == 'failed') {
						top.fAlert(response.msg);
						return false;
					}
				},
				complete:function(){
					top.show_loading_image('hide');
				}
			});
		break;
	}
}

// DSS Service Conneccted Option Form
function dssLoadServiceConnectedOpt(reqFrom, medId) {
	if(reqFrom == "undefined" || reqFrom == "") return false;
	top.show_loading_image('show');

	// patient problem id
	if(reqFrom == "problem_list") {
		var prob_id = top.fmain.$('#dssPatSCPopup').attr('data-scid');
		if(typeof(prob_id) !== 'undefined' && prob_id != '') {
			medId = prob_id;
		}
	}

	// Work View
	if(reqFrom == "work_view") {
		var form_id = medId;
		if(typeof(form_id) !== 'undefined' && form_id != '') {
			medId = form_id;
		}
	}

	$.ajax({
		url: top.JS_WEB_ROOT_PATH+"/interface/core/ajax_handler.php",
		type: 'POST',
		data: {
			task:'dssLoadServiceConnectedOpt',
			reqFrom:reqFrom,
			medId:medId
		},
	})
	.done(function(response) {
		top.show_loading_image('hide');
		var footer = title = '';
		switch(reqFrom) {
		  case 'header_master':
		  	modal_title = 'Patient Service Connected Eligibility';
		  	footer = '';
		    break;
		  case 'work_view':
		  	modal_title = 'Work View Patient Service Connected Eligibility';
		  	footer = '<button type="button" class="btn btn-success" onclick="top.dssProbServiceConnected()">Submit</button>';
		    break;
		  case 'medication':
		  	modal_title = 'Medication Service Connected Options';
		  	footer = '';
			// footer = '<button type="button" class="btn btn-success" onclick="dssServiceConnected()">Update</button>';
		    break;
	      case 'problem_list':
		  	modal_title = 'Patient Problems List - Service Connected Eligibility';
			footer = '<button type="button" class="btn btn-success" onclick="top.dssProbServiceConnected()">Submit</button>';
		    break;
		  default:
		    modal_title = 'Modal Title';
		    footer = '';
		}
		show_modal('isServiceConnected', modal_title, response, footer, 700, 'modal-lg');
	});
}

/* DSS Medication service connected */
function dssServiceConnected() {
	top.show_loading_image('show');
	$.ajax({
		url: top.JS_WEB_ROOT_PATH+"/interface/core/ajax_handler.php",
		type: 'POST',
		data: {
			task:'dssUpdateServiceConnected',
			formdata: $('#dss_sc_form').serialize()
		},
	})
	.done(function(response) {
		top.show_loading_image('hide');
		$("#isServiceConnected").modal('hide');
		top.fAlert(response);
	});
}

/* DSS problem list service connected */
function dssProbServiceConnected() {
	top.show_loading_image('show');
	top.fmain.$('#service_eligibility').val($('#dss_sc_form').serialize());
	top.show_loading_image('hide');
	$("#isServiceConnected").modal('hide');
}

/* UGA View in uPortal360 Button */
function view_in_uportal360(redirect_url) {
	if(typeof(redirect_url) != 'undefined' && redirect_url != '') {
		var w = screen.width - 100;
		window.open(redirect_url,'','width='+w+',height=700,top=50,left=50,toolbar=0,location=0,statusbar=0,menubar=0');
	}
}

//specify if DSS medication is service connected eligibility
function dss_value_change(obj){
    if($(obj).is(':checked')==true){$(obj).val(1);$(obj).prop('checked', true);}
    else {$(obj).val(0);$(obj).prop('checked', false);}
}

//Starts Here - (IM-6331)Switch Facility Option
$('#div_user_settings').on('hidden.bs.modal', function(){
    fac_mouseleave();
});

function fac_mousehover() {
    $('.logged_facility_text').hide();
    $('.facility_dd').show();
}
function fac_mouseleave() {
    $('.logged_facility_text').show();
    $('.facility_dd').hide();
}

function changeLoggedInFacility(obj) {
    if(typeof(obj)!=='undefined') {
        top.show_loading_image('show');

        var facility_value=$("#"+obj.id+" option:selected").val();
        var facility_text=$("#"+obj.id+" option:selected").text();
        var curr_tab=$("#curr_main_tab").val();
        $.ajax({
            url: top.JS_WEB_ROOT_PATH+"/interface/core/ajax_handler.php",
            type: 'POST',
            data: { task:'change_loggedin_facility', loggedInFacility:facility_value, curr_tab:curr_tab},
        }).done(function(response) {
            top.show_loading_image('hide');
            $('.logged_facility_text').html(facility_text);
            $('.logged_facility_text').show();
            $('.facility_dd').hide();
            window.location.reload();
        });
    }
}

//Ends Here - (IM-6331)Switch Facility Option

