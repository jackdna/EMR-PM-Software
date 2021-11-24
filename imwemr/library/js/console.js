/* This function called on loading console */
browser = get_browser();
var pt_win = '';
var current_tab='';
function onload_fun(formHeight) {
	wh = parseInt($(window).height());
	ww = parseInt($(window).width());
	console_ht = wh - 15;
	$('div#console_details').height(console_ht).width(ww - 220);
	$('div#console_head_bar').height(30);
	if (typeof (formHeight) != 'undefined') {
		if (typeof (formHeight) != 'number') {
			formHeight = 250;
		}
		formHeight = formHeight - 70;
		$('div#console_form').height(formHeight);
		if (browser == "ie" || "")
			console_ht = console_ht - (formHeight + 155);
		else if (browser == "chrome" || browser == "safari")
			console_ht = console_ht - (formHeight + 155);
	} else {
		if (browser == "ie" || "")
			console_ht -= 90;
		else if (browser == "chrome" || browser == "safari")
			console_ht -= 90;
	}
	$('div#console_data').height(console_ht);

	$('body').on('shown.bs.modal', '#mrmsg_popup',function(){
		//if (!($('.modal.in').length)) {
			$('.modal-dialog').css({
				top: 0,
				left: 0
			});

			$('.modal-header').css('cursor', 'move');
		//}

		$('#mrmsg_popup .modal-dialog').draggable({
			handle: ".modal-header",
		});
	});

}
function get_browser() {
	browser = '';
	if (navigator.userAgent.indexOf("MSIE") != -1 || !!navigator.userAgent.match(/Trident\/7\./)) {
		browser = "ie";
	} else if (typeof (window.mozilla) == "object") {
		browser = "mozilla";
	} else if (typeof (window.chrome) == "object") {
		browser = "chrome";
	} else if (navigator.userAgent.indexOf("Safari") != -1) {
		browser = "safari";
	}
	return browser;
}
function console_setup()
{
	$('.phylft li').unbind('click');
	$('.phylft li').click(liClick);
	hash = window.location.hash;
	activateByHash(hash);
}

function liClick() {
	$('.phylft li').removeClass('active');
	$(this).addClass('active');
	$(document).prop('title', 'Physician Console :: ' + $(this).text());
	//load_link_data($(this).attr('id'));
}

function activateByHash(hash) {
	switch (hash) {
		case '#ptComm_tab1':
			$('#message_reminders_opt').trigger('click');
			break;
		case '#ptComm_tab2':
			$('#forms_letters_opt').trigger('click');
			break;
		case '#ptComm_tab3':
			$('#test_tasks_opt').trigger('click');
			break;
		case '#message_reminders_opt':
			$('#message_reminders_opt').trigger('click');
			break;
		default:
			if (hash == '#' || hash == '') {
				$('#message_reminders_opt').trigger('click');
			} else {
				//hash = hash.replace(/[^\w#]+/g, "");
				hash = decodeURIComponent(encodeURIComponent(hash));
				$(hash).trigger('click');
			}
			break;
	}
}

function load_link_data(section, filter) {
	$('#console_data').html('<div class="doing"></div>');
	hide_console_form();
	window.location.hash = section;
	switch (section) {
		case 'import_ccda_opt':{
			switch_con_data_header('import_ccda_header');
			show_import_ccda_interface();
			break;
		}
		case 'message_reminders_opt':
		{
			switch_con_data_header('message_reminder');
			load_messages();
			break;
		}
		case 'smart_phrases':
		{
			switch_con_data_header('phy_con_smart_phrases');
			load_smart_phrases();
			break;
		}
		case 'test_tasks_opt':
		{
			switch_con_data_header('phy_con_tasks_header');
			load_tests_tasks(filter);
			break;
		}
		case 'rule_tasks_opt':
		{
			switch_con_data_header('phy_con_rule_tasks_header');
			//do_action('rule_tasks');
            load_rules_tasks();
			break;
		}
		case 'a_p_policies_opt':
		{
			switch_con_data_header('phy_con_ap_policies_header');
			load_ap_policies();
			break;
		}
		case 'forms_letters_opt':
		{
			switch_con_data_header('phy_con_forms_letters_header');
			load_forms_letters();
			break;
		}
		case 'unfinalized_patients_opt':
		{
			switch_con_data_header('phy_con_unfinalized_pt_header');
			load_unfinalized_patient();
			break;
		}
		case 'orders_set_opt':
		{
			switch_con_data_header('phy_con_order_header');
			load_orders_order_set();
			break;
		}
		case 'resp_person_opt':
		{
			switch_con_data_header('phy_con_resp_person');
			load_responsible_person();
			break;
		}
		case 'erx_inbox_opt':
		{
			switch_con_data_header('phy_con_erx');
			load_erx_inbox();
			break;
		}
		case 'completed_tasks':
		{
			switch_con_data_header('phy_con_completed_tasks');
			load_completed_tasks(filter);
			break;
		}
		case 'patient_notify_opt':
		{
			switch_con_data_header('phy_con_patient_notify');
			if(!filter || typeof(filter) == 'undefined' || filter == '') filter = 0;
			load_patient_notify(filter);
			break;
		}
		case 'patient_messages':
		{
			switch_con_data_header('phy_con_patient_messages');
			load_patient_messages();
			break;
		}
		case 'wnl_charttemplate':
		{
			switch_con_data_header('phy_con_wnl_charttemplate');
			var t = (typeof (gcti) != "undefined") ? gcti : "";
			load_wnl_charttemplate(t);
			break;
		}
		case 'direct_messages':
		{
			switch_con_data_header('phy_con_direct_messages');
			load_direct_messages(1, 'id', 'desc', 'direct_msg_inbox');
			break;
		}
	}
}

/*-----MESSAGING------*/
function load_messages_sort() {
	var sopt = $("#el_sort_val").data("val");
	if(typeof(sopt)=="undefined" || sopt=="" || sopt=="DESC"){sopt = "ASC";}else{sopt = "DESC";}
	$("#el_sort_val").data("val", sopt);
	sort=$("#el_sort_msg").val();
	if(typeof(sort)=="undefined" || sort==""){$("#el_sort_msg").val("Sender");}
	load_messages();
}
function load_messages(page_no, per_page, filter) {
	if (typeof (page_no) == "undefined") {
		var page_no = "";
	}
	if (typeof (per_page) == "undefined") {
		var per_page = "";
	}
	var filter_chk = "msg_my_inbox";
	if (typeof (filter) == "undefined") {
		if($('#message_reminder .newmessag').data('filter')){
			filter_chk = $('#message_reminder .newmessag').data('filter');
		}
	} else if (filter) {
		filter_chk = filter;
	}

	var sort="";
	sort=$("#el_sort_msg").val();
	if(typeof(sort)!="undefined" && sort!=""){
		var sopt = $("#el_sort_val").data("val");
		if(typeof(sopt)=="undefined" || sopt==""){sopt = "ASC";}
		sort = sort+"_"+sopt;
	}

	current_tab=filter_chk;

	show_console_form('send_msg_frm.php', 315);
	$.ajax({
		url: 'ajax_html.php?from=console&task=ptcomm&filter=' + filter_chk + '&page_no=' + page_no + '&per_page=' + per_page + '&sort='+sort,
		type: 'POST',
		success: function (resultData)
		{
			var data_arr = resultData.split('~~::~~');
			resultData = data_arr[0];
			if (data_arr[1])
			{
				//show buttons in header
				$("#phy_con_msg_header_buttons_container").html(data_arr[1]);
			}
			set_result_data(resultData);
		},
		complete: function ()
		{
			var msgId = getConsoleDataId('console-data-id');
			if (msgId != 'null')
				$('#msg_' + msgId).trigger('click');
		}
	});
}
function open_msg_block(msgid, th, ptid) {

	$('.tr_msg_details').hide();
	$('#open_msg_tr_' + msgid).show();


	msg_subject = $('h2', $(th)).text();

	msg_pat_detail = $('.ptName', $(th)).text();
	msg_pat_detail_arr = msg_pat_detail.split(' - ');

	$('#patientId', window.frames.console_form.document).val(msg_pat_detail_arr[1]);
	$('#txt_patient_name', window.frames.console_form.document).val(msg_pat_detail_arr[0]);
	$('#message_subject', window.frames.console_form.document).val(msg_subject);


	if (typeof (window.frames.console_form.load_ptcomm_ptinfo) != "undefined")
		window.frames.console_form.load_ptcomm_ptinfo(ptid);

	msgMarkRead(msgid);
}

function blankReplyForm()
{
	$('#patientId', window.frames.console_form.document).val('');
	$('#txt_patient_name', window.frames.console_form.document).val('');
	$('#message_subject', window.frames.console_form.document).val('');
	$('#message_text', window.frames.console_form.document).val('');

	if (typeof (window.frames.console_form.load_ptcomm_ptinfo) != "undefined")
		window.frames.console_form.load_ptcomm_ptinfo(0);
}


function loadPtMessgDetail(pt_msgid, obj, ptid, sent_li)
{
	pt_msgid = parseInt(pt_msgid);
	if (pt_msgid <= 0)
		return false;

	var formData = {from: 'console', task: 'load_patient_messages', msgId: pt_msgid, sent_li: sent_li};

	if (sent_li == 0)
	{
		$.ajax({
			url: 'ajax_html.php?from=console&task=set_unread_pt_msg&pt_msgid=' + pt_msgid,
			type: 'POST',
			complete: function (resp) {}
		});
	}

	$.ajax({
		url: top.URL + '/interface/physician_console/ajax_html.php?filter=getPtMessageDetails',
		method: 'POST',
		data: formData,
		success: function (data)
		{
			$('#ptmessageData').html(data);
			//blankReplyForm();
		},
		complete: function ()
		{
			$('.messageList>li').removeClass('activeMsg');
			$(obj).addClass('activeMsg');
			//msgMarkRead(pt_msgid);
			$(obj).removeClass('unredBold');
			reloadFunction();

			//open_msg_block(pt_msgid, obj, ptid);
		}
	});
}
function loadPtNotificationDetail(obj, ptid, sent_li, rec_id,tbl, cancel_req_id)
{
	var formData = {from: 'console', task: 'load_patient_messages', msgId: ptid, sent_li: sent_li, rec_id:rec_id, tbl:tbl, cancel_req_id:cancel_req_id};

	$.ajax({
		url: top.URL + '/interface/physician_console/ajax_html.php?filter=getPtMessageDetails',
		method: 'POST',
		data: formData,
		success: function (data)
		{
			$('#ptmessageData').html(data);
		},
		complete: function ()
		{
			$('.messageList>li').removeClass('activeMsg');
			$(obj).addClass('activeMsg');
			//msgMarkRead(pt_msgid);
			$(obj).removeClass('unredBold');
			reloadFunction();
		}
	});
}
//function open_pt_msg_block(pt_msgid,th,ptid,sent_tr){
//	var dis_row_result = $('#open_pt_msg_tr_'+pt_msgid).css('display');
//	if(dis_row_result == "none")
//	{
//		$('.tr_pt_msg_details').hide();
//		$('#open_pt_msg_tr_'+pt_msgid).css({"display":"table-row"});
//	}
//	else
//	{
//		$('.tr_pt_msg_details').hide();
//		$('#open_pt_msg_tr_'+pt_msgid).css({"display":"none"});
//	}
//	$(th).removeClass('text12b');
//	if($('.icoreadunread',$(th)).hasClass('unread')){$('.icoreadunread',$(th)).removeClass('unread').addClass('read');}
//	if(sent_tr == 0)
//	{
//		$.ajax({
//			url:'ajax_html.php?from=console&task=set_unread_pt_msg&pt_msgid='+pt_msgid,
//			type:'POST',
//			complete: function(resp){}
//		});
//	}
//	load_ptcomm_ptinfo(ptid);
//}

function open_msg(pt_msgid, th, ptid, sent_tr, is_read, sender_id) {
	var dis_row_result = $('#open_pt_msg_tr_' + pt_msgid).css('display');
	if (dis_row_result == "none")
	{
		$('#open_pt_msg_tr_' + pt_msgid).css({"display": "block"});
	} else
	{
		$('#open_pt_msg_tr_' + pt_msgid).css({"display": "none"});
	}
	$(th).removeClass('text12b');
	if ($('.icoreadunread', $(th)).hasClass('unread')) {
		$('.icoreadunread', $(th)).removeClass('unread').addClass('read');
	}
	if (sent_tr == 0)
	{
		$.ajax({
			url: 'ajax_html.php?from=console&task=set_unread_pt_msg&sender_id=' + sender_id + '&pt_msgid=' + pt_msgid,
			type: 'POST',
			complete: function (resp) {
				if (is_read == 0) {
					jID = "#div_total_unread_" + sender_id;
					total_unread = $(jID).html();
					$(jID).html(resp.responseText);
				}
			}
		});
	}
}

function load_pt_info_frm(ptid)
{
	//window.frames['console_form'].load_ptcomm_ptinfo(ptid);
	load_ptcomm_ptinfo(ptid);
}

/*-----TASKS/TESTS-----*/
function load_tests_tasks(filter)
{
	var filter = (typeof (filter) != "undefined" && filter != "") ? filter : "tests";
	$.ajax({
		url: 'ajax_html.php?from=console&task=tests_tasks&filter=' + filter,
		type: 'POST',
		success: function (resultData)
		{
			var data_arr = resultData.split('~~::~~');
			resultData = data_arr[0];
			if (data_arr[1])
			{
				//show buttons in header
				$("#phy_con_tasks_header_buttons_container").html(data_arr[1]);
			}
			set_result_data(resultData);
			if( $("#console_data_list table.sortable").length > 0 )
				sorttable.makeSortable($("table.sortable")[0]);
		},
		complete: function ()
		{
			$("#" + filter).siblings('li').removeClass('newmessag');
			$("#" + filter).addClass('newmessag');
		}
	});
}
function del_selected_tasks() {
	var del_id = '';
	$('.chk_record').each(function () {
		if ($(this).attr('checked') == 'checked' || $(this).attr('checked') == true || $(this).prop('checked') == true) {
			if (del_id == '') {
				del_id += $(this).val();
			} else {
				del_id += ',' + $(this).val();
			}
		}
	});
	$.ajax({
		url: 'ajax_html.php?from=console&task=tests_tasks&filter=del_scan_tasks&del_tasks_id=' + del_id,
		type: 'POST',
		success: function (r) {
			if (r == 'taskdeleted') {
				load_link_data('test_tasks_opt');
			}
		}
	});
}
function save_selected_scan_tasks() {
	var formData = $('#task_form').serialize();
	$.ajax({
		url: 'save_scan_tasks.php',
		type: 'POST',
		data: formData,
		success: function (r) {
			if (r == 'tasksaved') {
				load_link_data('test_tasks_opt', 'scan_upload');
			}
		}
	});
}

/*-----SMART PHRASES------*/
function load_smart_phrases()
{
	//show_console_form('smart_phrase_frm.php',95);
	$.ajax({
		url: 'ajax_html.php?from=console&task=smart_phrases',
		type: 'POST',
		success: function (resultData)
		{
			set_result_data(resultData);
		}
	});
}
function del_smart_phrase(del_id)
{
	result_action = confirm('Are you sure you want to delete this Phrase');
	if (result_action == true)
	{
		$.ajax({
			url: 'ajax_html_console.php?from=console&task=smart_phrases&del_id=' + del_id,
			type: 'POST',
			success: function (resultData)
			{
				load_link_data('smart_phrases');
			}
		});
	}
}

/*-----A & P POLICIES------*/
function load_ap_policies()
{
	//show_console_form('console_to_do.php',287);
	$.ajax({
		url: 'ajax_html.php?from=console&task=ap_policies',
		type: 'POST',
		success: function (resultData)
		{
			//$('#console_data_list').html(resultData);
			set_result_data(resultData);
			//console_ht_pd = ($('#console_data_list').height() - 10) / 3;
			//$('#ap_pol_prov').height(console_ht_pd);
			//$('#console_comm').height(console_ht_pd);
			//$('#console_dynamic').height(console_ht_pd);
		}
	});
}

/*-----Unfinalized Patients------*/

function load_unfinalized_patient()
{
	var sall=($('#show_all_patients').prop('checked')==true) ? 1 : 0 ;
	$('#show_all_patients').prop("disabled", true);
	$.ajax({
		url: 'ajax_html.php?from=console&task=unfinalized_patients&sall='+sall,
		type: 'POST',
		success: function (resultData)
		{
			var data_arr = resultData.split('~~::~~');
			resultData = data_arr[0];
			if (data_arr[1])
			{
				//show buttons in header
				$("#phy_con_unfinalized_pt_buttons_container").html(data_arr[1]);
			}


			set_result_data(resultData);
			if(sall==1)
			{
				elem = $('#show_all_patients');
				show_all_unfinal_pat(elem);
			}
			$('#show_all_patients').prop("disabled", false);
			$('.chk_sel_all').unbind('click');
			$('.chk_sel_all').click(select_all_checkbox);
			//$('.chk_record').click(rem_checked_attr);
			if( $("#data_show_by_default table.sortable").length > 0 ){ sorttable.makeSortable($("#data_show_by_default table.sortable")[0]); }
			if( $("#show_on_chk table.sortable").length > 0 ){ sorttable.makeSortable($("#show_on_chk table.sortable")[0]); }
		}
	});
}

/*-----FORMS & LETTERS------*/
function load_forms_letters()
{
	$.ajax({
		url: 'ajax_html.php?from=console&task=forms_letters&filter=consent_forms',
		type: 'POST',
		success: function (resultData)
		{
			//var data_arr = resultData.split('~~::~~');
			//resultData = data_arr[0];
			//if (data_arr[1])
			//{
			//	//show buttons in header
			//	$("#phy_con_forms_letters_buttons_container").html(data_arr[1]);
			//}

			set_result_data(resultData);
		}
	});
}

/*-------RESPONSIBLE PERSON---------*/
function load_responsible_person(page_no, per_page, filter) {
	if (typeof (page_no) == "undefined") {
		var page_no = "";
	}
	if (typeof (per_page) == "undefined") {
		var per_page = "";
	}

	$.ajax({
		url: 'ajax_html.php?from=console&task=resp_person&page_no=' + page_no + '&per_page=' + per_page,
		type: 'POST',
		success: function (resultData)
		{
			//
			var data_arr = resultData.split('~~::~~');
			resultData = data_arr[0];
			if (data_arr[1])
			{
				//show buttons in header
				$("#phy_con_resp_person_buttons_container").html(data_arr[1]);
			}
			set_result_data(resultData);
			if( $("#console_data_list table.sortable").length > 0 )
				sorttable.makeSortable($("table.sortable")[0]);
		}
	});
}

/*----Orders/Order Set-----------------*/
function load_orders_order_set()
{
	$.ajax({
		url: 'ajax_html.php?from=console&task=order_sets',
		type: 'POST',
		success: function (resultData)
		{
			set_result_data(resultData);
		}
	});
}

/*---------ERX INBOX---------*/

function load_erx_inbox()
{
	$.ajax({
		url: 'ajax_html.php?from=console&task=load_erx',
		type: 'POST',
		success: function (resultData)
		{
			set_result_data(resultData);
		}
	});
}
/*---- Wnl/Chart Template -----------------*/
function load_wnl_charttemplate(cti)
{
	//
	if (typeof (cti) == "undefined") {
		cti = "";
	}

	$.ajax({
		url: 'ajax_html.php?from=console&task=wnl_charttemplate&cti=' + cti,
		type: 'POST',
		success: function (resultData)
		{
			set_result_data(resultData);
		}
	});
}

/**********IMPORT CCDA INTERFACE********/
function show_import_ccda_interface(){
	$.ajax({
		url: 'ajax_html.php?from=console&task=import_ccda_interface',
		type: 'POST',
		success: function (resultData)
		{
			set_result_data(resultData);
		}
	});
}


function mark_erx_inbox_link()
{
	$.ajax({
		url: 'ajax_html.php?from=console&task=load_erx',
		type: 'POST',
		success: function (resultData)
		{//a=window.open();a.document.write(resultData);
			if (resultData == '<div class="alignCenter warning text12b">No record found.</div>') {
				$('#erx_inbox_opt').removeClass('unread').addClass('read');
			} else {
				$('#erx_inbox_opt').removeClass('read').addClass('unread');
			}
		}
	});
}

/*-------LOAD COMPLETED TASKS-------*/

function load_completed_tasks(elem)
{
	var sel_opt = "";

	if (elem) {
		sel_opt = elem;
	} else {
		sel_opt = $('.comp_tasks_opt').attr('id');
	}

	//if($("#comp_tasks_opt")){
	//sel_opt=$("input:radio[name ='comp_tasks_opt']:checked").attr('id');
	//sel_opt=$("input[type='radio'].comp_tasks_opt:checked").attr('id');
	//}top.fAlert(sel_opt);
	$.ajax({
		url: 'ajax_html.php?from=console&task=completed_tasks&sub_task=' + sel_opt,
		type: 'POST',
		success: function (resultData)
		{
			var data_arr = resultData.split('~~::~~');
			resultData = data_arr[0];
			if (data_arr[1])
			{
				//show buttons in header
				$("#phy_con_completed_tasks_buttons_container").html(data_arr[1]);
			}
			set_result_data(resultData);
		},
		complete: function ()
		{
			$("#" + elem).siblings('li').removeClass('comp_tasks_opt');
			$("#" + elem).addClass('comp_tasks_opt');
		}
	});
}

/*--------PATIENT NOTIFY------------*/
function load_patient_notify(filterVal)
{
	if(!filterVal) filterVal = $('#patientNotifyFilter').val();
	if(filterVal == '' || typeof(filterVal) == 'undefined') filterVal = 0;

	$.ajax({
		url: 'ajax_html.php?from=console&task=load_patient_notify&filter='+filterVal,
		type: 'POST',
		success: function (resultData)
		{
			set_result_data(resultData);
			$('#patientNotifyFilter').val(filterVal);
		}
	});
}

function load_patient_messages(page, sort_by, sort_order, filter)
{
	$("#directLoader").html("<div class='doing'></div>");
	var page = page || '1';
	var sort_by = sort_by || 'pt_msg_id';
	var sort_order = sort_order || 'DESC';
	var filter = filter || 'load_pt_msg_inbox';

    if(typeof($("#filter_type").val())!= "undefined" && $("#filter_type").val()!=''){
       filter = $("#filter_type").val();
    }
	//show_console_form('send_patient_msg_frm.php',240);
	$.ajax({
		url: 'ajax_html.php?from=console&task=load_patient_messages&filter=' + filter + '&page=' + page + '&sort_by=' + sort_by + '&sort_order=' + sort_order,
		type: 'POST',
		success: function (resultData)
		{
			var data_arr = resultData.split('~~::~~');
			resultData = data_arr[0];
			if (data_arr[1])
			{
				//show buttons in header
				$("#phy_con_patient_messages_buttons_container").html(data_arr[1]);
			} else {
				$("#phy_con_patient_messages_buttons_container").html('');
			}

			set_result_data(resultData);

			setTimeout(function () {
				sort_data(sort_by, sort_order, filter, load_patient_messages);
			}, 2000);
		},
		complete: function ()
		{
			$("#" + filter).siblings('li').removeClass('newmessag');
			$("#" + filter).addClass('newmessag');
		}
	});
}

/*--------- Direct Messages ----------*/
function load_direct_messages(page, sort_by, sort_order, filter, userId)
{
	$("#directLoader").html("<div class='doing'></div>");

	page = page || '1';
	sort_by = sort_by || 'id';
	sort_order = sort_order || 'desc';
	filter = filter || '';
	userId = userId || '';

    if(userId=='') {
        $('[id^=user_prev_id_]').each( function(key,obj) {
            obj = $(obj);
            if(obj.closest('li.active').length>0) {
                var elem=obj.closest('li.active');
                var anchrData = elem.find('a:first-child');
                if(anchrData.length){
                    userId = anchrData.data('prev_user_id');
                }
            }
        });
    }

	$.ajax({
		url: 'ajax_html.php?from=console&task=load_direct_messages&filter=' + filter + '&page=' + page + '&sort_by=' + sort_by + '&sort_order=' + sort_order+'&userId='+userId,
		type: 'POST',
		success: function (resultData)
		{
			/*var data_arr = resultData.split('~~::~~');
			resultData = data_arr[0];
			if (data_arr[1])
			{
				//show buttons in header
				$("#phy_con_direct_messages_buttons_container").html(data_arr[1]);
			}*/
			set_result_data(resultData);
			chkForUnreadMsg();
			//sort_data(sort_by, sort_order, filter, load_direct_messages);
		},
		complete: function ()
		{
			hideloaderFlag = true;
			var msgId = getConsoleDataId('console-data-id');
			if (msgId != 'null')
			{
				$('#direct_' + msgId).trigger('click');

				/*Trigger Landing Page Action*/
				var action = getConsoleDataId('action');
				if (action != 'null')
				{
					var actionElement = $('#' + action + 'Btn_' + msgId);
					if (actionElement.length > 0)
						$(actionElement).trigger('click');
				}
			}
			if(filter=='direct_msg_sent')$('#DM_type').html('[Sent]');
			else $('#DM_type').html('[Inbox]');
		}
	});
}
function sort_data(sort_by, sort_order, filter, func) {
	filter = filter || '';
	tdID = '';
	switch (sort_by) {
		case "from_email":
			text = "FROM";
			break;
		case "to_email":
		case "receiver_id":
			text = "TO";
			break;
		case "msg_subject":
		case "subject":
			text = "SUBJECT";
			break;
		case "msg_date_time":
		case "direct_datetime_f":
		case "local_datetime":
			text = "DATE";
			break;
		case "MID":
			text = "MID";
			break;
		default:
			text = "FROM";
			break;
	}
	if (sort_order == "asc") {
		tdID = sort_by;
		document.getElementById(tdID).innerHTML = text + ' <image src=\"../../library/images/s_asc.png\" valign="middle"> ';
		next_sort_order = "desc";
	} else if (sort_order == "desc") {
		tdID = sort_by;
		document.getElementById(tdID).innerHTML = text + ' <image src=\"../../library/images/s_desc.png\" valign="middle"> ';
		next_sort_order = "asc";
	}
	//top.fAlert(sort_by+"::"+sort_order+"::"+text)

	jspanID = "#" + tdID;
	$(jspanID).removeAttr('onClick');
	$(jspanID).bind('click', function () {
		eval(func(1, sort_by, next_sort_order, filter));
	})
}
function open_erx(patientId) {
	var parentWid = parent.document.body.clientWidth;
	window.open('../chart_notes/erx_patient_selection.php?patientFromSheduler=' + patientId, 'erx_window_new', 'resizable=1,width=' + parentWid + ',height=' + screen.height + ',scrollbars=1');
}

function open_authorize() {
	var parentWid = parent.document.body.clientWidth;
	window.open('erx_authorize.php', 'erx_authorize', 'resizable=1,width=' + parentWid + ',height=' + screen.height + ',scrollbars=1');
}

function printWindow() {
	window.focus();
	window.print();
}

/*print Unfinalized patient*/
function printUnf()
{
	if( $('#data_show_by_default').css('display') === 'none' )
		$('#show_on_chk').printElementContent();
	else
		$('#data_show_by_default').printElementContent();
}

/*----expanding pt detalis----*/
function load_pt_more_details() {
	$('#icon_pt_detail_expand').hide();
	$('#div_pt_details_msg').html('<div class="doing"></div>');
	$.ajax({
		url: 'ajax_html.php?from=console&task=pt_more_details',
		type: 'POST',
		success: function (r)
		{
			$('#div_pt_details_msg').html(r);
		}
	});
	//result div.
	$('#div_pt_details_msg').show();
}

function hide_pt_more_details() {
	$('#div_pt_details_msg').html('');
	$('#div_pt_details_msg').hide();
	$('#icon_pt_detail_expand').show();
}

//COMMON FUNCTION BELOW. TO RETRIVE DATA FOR ANY BLOCK WRITE CODE ABOVE THIS LINE.
function select_all_checkbox() {
	if ($(this).attr('checked') == 'checked' || $(this).attr('checked') == true || $(this).prop('checked') == true) {
		$('.chk_record').prop('checked', true);
		$('.chk_record').attr({'checked': 'checked'});
		$(this).parent().parent().addClass('selected');
	} else {
		$('.chk_record').prop('checked', false);
		$('.chk_record').removeAttr('checked');
		$(this).parent().parent().removeClass('selected');
	}
}
function select_all_checkbox1(ths) {
	if ($(ths).attr('checked') == 'checked' || $(ths).attr('checked') == true || $(ths).prop('checked') == true) {
		$('.chk_record', $(ths).parent().parent().parent().parent()).attr({'checked': 'checked'});
		$(ths).parent().parent().addClass('selected');
        $('.move_to_folder').prop('disabled', false);
	} else {
		$('.chk_record', $(ths).parent().parent().parent().parent()).removeAttr('checked');
		$(ths).parent().parent().removeClass('selected');
        $('.move_to_folder').prop('disabled', true);
	}
}

function enable_saveto(elem) {
    if ($(elem).attr('checked') == 'checked' || $(elem).attr('checked') == true || $(elem).prop('checked') == true) {
		$('.move_to_folder').prop('disabled', false);
	} else {
		$('.move_to_folder').prop('disabled', true);
	}
}

function save_to_folder(obj) {
    var folder_id = $(obj).val();
    var folder_name = $(obj).find(':selected').attr('data-folder_name');

    elem = current_tab;

    var chkVal = [];
    var chk_values = '';
    $('.chk_record').each(function(id, ele){
        var value = $(ele).val();
        if ($(ele).attr('checked') == 'checked' || $(ele).attr('checked') == true || $(ele).prop('checked') == true) {
            if(value && typeof(value) !== 'undefined') chkVal.push(value);
        }
	});
    if(chkVal.length){
        chk_values = chkVal.join(',');
    }

    $.ajax({
		url: 'ajax_html.php?from=console&task=save_to_folder&msg_id=' + chk_values + '&folder_id=' + folder_id,
		type: 'POST',
		success: function (r)
		{
            if(r>0){
                top.fAlert('Message saved to '+folder_name);
                do_action('ptcomm', elem);
            }
		}

	});
}


function rem_checked_attr() {
	if ($(this).attr('checked') == 'checked') {
		$(this).removeAttr('checked');
	}
}

function switch_con_data_header(idToShow) {
	$('#sectionTitle .section_heading').hide();
	$('#' + idToShow).show();

	//first_chk_box=$('#'+idToShow+' input[type="radio"]').attr('id');
	//$('#'+first_chk_box).prop('checked', true);
}


//function switch_con_data_header(idToShow){
//
//	$.ajax({
//		url: top.URL+'/interface/physician_console/templates/'+idToShow+'.html',
//		method: 'GET',
//		success: function(data){
//			data = replaceVariables(data);
//			$('#sectionTitle').html(data);
//		}
//	});
//
//}

function replaceVariables(data)
{
	var regex = new RegExp(Object.keys(mapObj).join("|"), "gi");

	data = data.replace(regex, function (matched) {
		return mapObj[matched];
	});
	return data;
}

var shownForm_detail = new Array();
function show_console_form(src, height)
{
	onload_fun(height);
	$('#console_form').prop('src', '');
	$('#console_form').prop('src', src);
	$('#console_form').css({'height': height});
	$('#console_form').show();
	shownForm_detail['src'] = src;
	shownForm_detail['height'] = src;
}

function hide_console_form() {
	shownForm_detail['src'] = '';
	shownForm_detail['height'] = '';
	$('#console_form').prop('src', '');
	$('#console_form').css({'height': 0});
	$('#console_form').hide();
	onload_fun();
}

function set_result_data(resultData)
{
	$('#console_data_list').html(resultData);//inserting result HMTL.
	//$('#messageData').html('');

	$('.chk_sel_all').unbind('click');
	$('.chk_sel_all').click(select_all_checkbox);
	//$('.chk_record').click(rem_checked_attr);
	$('tr td.stoptrclick').click(function (e) {
		e.stopPropagation();
	});

	reloadFunction();

	check_console_data_markers();
}

function loadReplyForm(params)
{
	if (params == 'undefined')
		params = '';
	sendNewMessage(params);

}

function do_action(block, th, obj, page) {
	page = page || '1';
	$('#console_data').html('<div class="doing"></div>');
	if ($(th).length > 0) {
		elemId = $(th).prop('id');
	} else {
		elemId = th;
	}
	if (typeof (elemId) == "undefined") {
		elemId = th;
	}
	//assign it to page variable
	current_tab=th;
	$(obj).data('filter',th);
	varurl = 'ajax_html.php?from=console&task=' + block + '&filter=' + elemId + '&page=' + page;

	$.ajax({
		url: varurl,
		type: 'POST',
		success: function (resultData)
		{
			var data_arr = resultData.split('~~::~~');
			resultData = data_arr[0];
			if (block == 'load_patient_messages')
			{
				if (data_arr[1])
				{
					//show buttons in header
					$("#phy_con_patient_messages_buttons_container").html(data_arr[1]);
				} else {
					$("#phy_con_patient_messages_buttons_container").html('');
				}
			} else
			{
				if (data_arr[1])
				{
					//show buttons in header
					$("#phy_con_msg_header_buttons_container").html(data_arr[1]);
				}
				if(block == 'load_direct_messages')
				{
					if(th=='direct_msg_inbox')$('#DM_type').html('[Inbox]');
					else $('#DM_type').html('[Sent]');
				}
			}
			set_result_data(resultData);
			if( $("#console_data_list table.sortable").length > 0 )
				sorttable.makeSortable($("table.sortable")[0]);
			jele = "#" + elemId;
			$(jele).prop('checked', true);
		},
		complete: function ()
		{
			//console.log(obj);
            if($(obj).hasClass('more_folders')) {
                $(obj).parents('li').siblings('li').removeClass('newmessag');
            } else {
                $(obj).siblings('li').children('ul').find('li').removeClass('newmessag');
            }
			$(obj).siblings('li').removeClass('newmessag');
			$(obj).addClass('newmessag');
			$("#facility_name").val('');
		}
	});
}


function loadMessageDetails(messageId, obj, ptId)
{

	messageId = parseInt(messageId);
	if (messageId <= 0)
		return false;

	var formData = {from: 'console', task: 'getMessageDetails', msgId: messageId};

	$.ajax({
		url: top.URL + '/interface/physician_console/ajax_html.php',
		method: 'POST',
		data: formData,
		success: function (data)
		{
			$('#messageData').html(data);
			//blankReplyForm();
		},
		complete: function ()
		{
			$('.messageList>li').removeClass('activeMsg');
			$(obj).addClass('activeMsg');
			msgMarkRead(messageId);
			$(obj).removeClass('unredBold');
			reloadFunction();

			/*Trigger Landing Page Action*/
			var action = getConsoleDataId('action');
			if (action != 'null')
			{
				var actionElement = $('#' + action + 'Btn_' + messageId);
				if (actionElement.length > 0)
					$(actionElement).trigger('click');
			}
		}
	});
}

/*------FUNCTIONS FROM OLD CONSOLE------*/
function LoadWorkView(ptid) {
    $.when(window.opener.top.check_for_break_glass_restriction(ptid)).done(function(response){
        window.opener.top.removeMessi();
        if(response.rp_alert=='y') {
            var patId=response.patId;
            var bgPriv=response.bgPriv;
            var rp_alert=response.rp_alert;
            window.opener.top.core_restricted_prov_alert(patId, bgPriv, '');
        }else{
            window.opener.top.focus();
            rand = Math.round(Math.random() * 555555);
            window.opener.top.core_set_pt_session(window.opener.top.fmain, ptid, '../chart_notes/work_view.php?activateTab=Work_View&uniqueurl=' + rand);
        }
    });
}

function loadPtThenTEST(pt_id,test_table,test_id){
	moduleHandlerURL = '../tests/module_handler.php?param='+test_id+'~'+test_table;
	window.opener.top.LoadPtThenModule(pt_id,moduleHandlerURL);

}

function update_toolbar() {
	window.opener.top.update_toolbar_icon();
}

function update_link_status(id, cls1, cls2) {//top.fAlert(id+', '+cls1+', '+cls2);
	if (cls1 != '') {
		$(id).removeClass(cls1);
	}
	if (cls2 != '') {
		$(id).addClass(cls2);
	}

	if (!$('#div_opt_scans').hasClass('unread_marker') && !$('#div_opt_tests').hasClass('unread_marker')) {
		$('#test_tasks_opt').removeClass('unread').addClass('read');
	}
	if (!$('#div_opt_consent').hasClass('unread_marker') && !$('#div_opt_sxconsent').hasClass('unread_marker') && !$('#div_opt_opnote').hasClass('unread_marker') && !$('#div_opt_consult').hasClass('unread_marker')) {
		$('#forms_letters_opt').removeClass('unread').addClass('read');
	}

}

function check_console_data_markers() {
	var text = $('#console_data').html();
	text = $(text).text();
	switch (text) {
		case 'No Consent Form pending for signature.':
			update_link_status('#div_opt_consent', 'unread_marker', '');
			break;

		case 'No Surgery Consent Form pending for signature.':
			update_link_status('#div_opt_sxconsent', 'unread_marker', '');
			break;

		case 'No OPNote pending for signature.':
			update_link_status('#div_opt_opnote', 'unread_marker', '');
			break;

		case 'No Consult Letter pending for signature.':
			update_link_status('#div_opt_consult', 'unread_marker', '');
			break;

		case 'No Scan/Upload task pending.':
			update_link_status('#div_opt_scans', 'unread_marker', '');
			break;

		case 'No Test pending.':
			update_link_status('#div_opt_tests', 'unread_marker', '');
			break;
	}
	//this is pending here
	//top.opener.top.update_toolbar_icon();
}

function large(id, Ext) {
	if (!Ext) {
		Ext = 'pdf'
	}
	var url = 'show_image.php?id=' + id + '&ext=' + Ext + '&scn_task=review&hide_close_btn=true';
	$.ajax({
		url: url,
		method: 'POST',
		success: function (rdata) {
			var imghtml = "";
			imghtml += '<div id="image_popup_' + id + '" class="modal common_modal_wrapper"><div class="modal-dialog modal-lg model_width"><div class="modal-content">';
			imghtml += '<div class="modal-header bg-primary"><button type="button" class="close" data-dismiss="modal">x</button><h4 class="modal-title">Tasks</h4></div>';
			imghtml += '<div class="modal-body">';
			imghtml += rdata;
			if ($('#comment_' + id).get(0)) {
				var comment = $('#comment_' + id).text();
				imghtml += '<div class="mainwhtbox"><textarea rows="3" style="width:100%; vertical-align:text-top;" name="comment_' + id + '" id="comment_' + id + '" class="input_text_10 body_c">' + comment + '</textarea><input type="hidden" name="hidd_comment[' + id + ']" id="hidd_comment' + id + '" value="' + comment + '" ></div>';
			}

			imghtml += '</div>';
			imghtml += '<div class="modal-footer" style="text-align:center;"><button onClick="SaveEditor(' + id + ');" data-dismiss="modal" class="btn btn-success" value="Done" >Done</button>';
			imghtml += '<button onClick="hideEditor();" class="btn btn-danger" data-dismiss="modal" value="Cancel" >Cancel</button></div>';

			imghtml += '</div></div>';
			if ($('#image_popup_' + id).length == 0) {
				$(imghtml).insertAfter($("#task_form"));
			}
			$('#image_popup_' + id).modal({show: 'true'});
		}
	});
	//getEditor(id,url);
}


function getEditor(id, url) {
	$('.div_editor').width($(window).width() - 220);
	$('.div_editor').height($(window).height());
	$('.div_editor').css('top', ($(window).scrollTop()));
	$('#editor_iframe').css('width', $(window).width() - 220);
	$('#editor_iframe').css('height', ($('.div_editor').height() - 250) + 'px');
	$('#editor_iframe').prop('src', url);
	if (!$('#comment_' + id).get(0)) {
		var comment = $('#cmnt_td_' + id).text();
		var htmlText = '<textarea style="width:180px; height:20px; vertical-align:text-top;" name="comment_' + id + '" id="comment_' + id + '" class="input_text_10 body_c">' + comment + '</textarea><input type="hidden" name="hidd_comment[' + id + ']" id="hidd_comment' + id + '" value="' + comment + '" >';
		$('#cmnt_td_' + id).html(htmlText);
	}
	var original_text = $('#comment_' + id).val();
	$('#div_editor_textarea').val(original_text);
	$('#original_id').val(id)
	$('.div_editor').show();
}

function hideEditor() {
	//$('.div_editor').hide();
	load_link_data('test_tasks_opt', 'scan_upload');
}

function SaveEditor(id) {
	var newval = $('.mainwhtbox').find('#comment_' + id).val();
	$('#task_form').find('#comment_' + id).val(newval);
	$('#message_id_' + id).prop('checked', true);

	save_selected_scan_tasks();
}

function openEditor(section, holdId, ptId) {
	wh = parseInt($(window).height());
	ww = parseInt($(window).width());
	window.open('editor.php?case=' + section + '&doc_id=' + holdId + '&patient_id=' + ptId + '&height=' + (wh - 170) + '&width=' + (ww - 50), '', 'width=' + (ww - 50) + ',height=' + (wh - 100));
}
/*-------END OF FUNCTION FROM OLD CONSOLE----*/

/* This function deletes the messages */
function del_msg(msg_id, del_type, folder_id)
{
	elem = current_tab;
	//cr = confirm('Are you sure you want to delete the selected message(s)');
	//if (cr == true)
	//{
		$.ajax({
			url: 'ajax_html.php?from=console&task=del_msg&msg_id=' + msg_id + '&del_type=' + del_type+'&folder_id='+folder_id,
			type: 'POST',
			success: function (r)
			{
				alertShow('Message(s) deleted successfully');
				//top.fAlert('Message(s) deleted successfully');
				do_action('ptcomm', elem);
			}
		});
	//}
}

function un_del_msg(msg_id, del_type)
{
	elem = current_tab;
	//cr = confirm('Are you sure you want to Undelete the selected message(s)');
	//if (cr == true)
	//{
		$.ajax({
			url: 'ajax_html.php?from=console&task=un_del_msg&msg_id=' + msg_id + '&del_type=' + del_type,
			type: 'POST',
			success: function (r)
			{
				alertShow('Message(s) Undeleted successfully')
				//top.fAlert('Message(s) Undeleted successfully');
				do_action('ptcomm', elem);
			}
		});
	//}
}

function alter_msg_flag_status(msg_id, th)
{
	obj = $('span', $(th));
	if (obj.hasClass('unflagged')) {
		obj.removeClass('unflagged').addClass('flagged');
	} else {
		obj.removeClass('flagged').addClass('unflagged');
	}
	$.ajax({
		url: 'ajax_form_actions.php?from=console&task=alter_msg_flag_status&msg_id=' + msg_id,
		type: 'POST',
		success: function (r)
		{
			//top.fAlert(r+'status altered');
		}
	});
}

function alter_pt_msg_flag_status(msg_id, th)
{
	obj = $('span', $(th));
	if (obj.hasClass('unflagged')) {
		obj.removeClass('unflagged').addClass('flagged');
	} else {
		obj.removeClass('flagged').addClass('unflagged');
	}
	$.ajax({
		url: 'ajax_form_actions.php?from=console&task=alter_pt_msg_flag_status&pt_msg_id=' + msg_id,
		type: 'POST',
		success: function (r)
		{
			//top.fAlert(r+'status altered');
		}
	});
}

/* This function sets the messages mark as unread */
function mark_as_unread(msg_id)
{
	elem = $('input[type="radio"]:checked', $('.msg_filters'));
	$.ajax({
		url: 'ajax_html.php?from=console&task=mark_as_unread&msg_id=' + msg_id,
		type: 'POST',
		success: function ()
		{
			top.fAlert('Message marked as unread');
			do_action('ptcomm', elem);
		}
	});
}
/* This function sets the messages status completed */
function msg_completed(msg_id, operator_id)
{
	$('tr#open_msg_tr_' + msg_id).prev().remove();
	$('tr#open_msg_tr_' + msg_id).remove();
	msgMarkDone(msg_id);
}
/* This function sets the patient messages status completed */
function pt_msg_completed(pt_msgid, operator_id, sender_id)
{
	$('tr#open_pt_msg_tr_' + pt_msgid).prev().remove();
	$('tr#open_pt_msg_tr_' + pt_msgid).remove();

	$('div#open_pt_msg_tr_' + pt_msgid).prev().remove();
	$('div#open_pt_msg_tr_' + pt_msgid).remove();
	$.ajax({
		url: 'ajax_html.php?from=console&task=set_msg_done&pt_msgid=' + pt_msgid,
		type: 'POST',
		complete: function (resp) {
			jID = "#div_total_msg_" + sender_id;
			total_msg = $(jID).html();
			$(jID).html(total_msg - 1);

			load_link_data('patient_messages');
		}
	});
}

/* This function used for filter the patient messages by their ID */

function searchPtMsgById()
{
	var pt_id = $("#search_pt_msg_id").val();
	if (!isNaN(pt_id))
	{
		var task = '';
		$('#console_data').html('<div class="doing"></div>');
		if ($("#load_pt_msg_inbox").attr("checked") == "checked" || $("#load_pt_msg_inbox").attr("checked") == true || $("#load_pt_msg_inbox").prop("checked") == true)
		{
			task = 'load_pt_msg_inbox';
		} else
		{
			task = 'load_pt_msg_sent';
		}
		$.ajax({
			url: 'ajax_html.php?from=console&task=load_patient_messages&filter=' + task + '&filter_pt_id=' + pt_id,
			type: 'POST',
			success: function (resultData)
			{
				set_result_data(resultData);
			},
			complete: function ()
			{
				$('#load_pt_msg_inbox').siblings('li').removeClass('newmessag');
				$('#load_pt_msg_inbox').addClass('newmessag');
			}
		});
	} else
	{
		top.fAlert("Please search by Patient Id Only");
	}

	return false;
}


function searchFac(facId){
	if (!isNaN(facId)){
		var task = '';
		$('#console_data').html('<div class="doing"></div>');

		var task = 'load_pt_msg_inbox';
		if($('#phy_con_patient_messages .newmessag').length){
			var objId = $('#phy_con_patient_messages .newmessag').attr('id');
			if(objId == 'load_pt_msg_sent') task = 'load_pt_msg_sent';
		}
		$.ajax({
			url: 'ajax_html.php?from=console&task=load_patient_messages&filter=' + task + '&filter_pt_fac=' + facId,
			type: 'POST',
			success: function (resultData)
			{
				set_result_data(resultData);
			}
		});
	}
}


function deleteTests(act){
	if ($.trim(act) != ""){
		var chk_values = [];

		$('#console_data .chk_record:checked').each(function(id, ele){
			var value = $(ele).val();
			if(value && typeof(value) !== 'undefined') chk_values.push(value);
		});

		if(chk_values.length == 0){
			top.fAlert('Please select a record to proceed');
			return false;
		}
	}
}

/* This function deletes the messages for selecting records from checkboxes/grid/multiple selection */
function del_messages(act,folder_id)
{
	if ($.trim(act) != "")
	{
		chk_values = '';
		chk_flag = 0;
		$('.messageList input[type="checkbox"]', $('#console_data_list')).each(function ()
		{
			if ($(this).attr('checked') == 'checked' || $(this).attr('checked') == true || $(this).prop('checked') == true)
			{
				if (chk_flag == 1) {
					chk_values += ',';
				}
				chk_values += $(this).val();
				chk_flag = 1;
			}
		});
		if (chk_values == '')
		{
			var validate = false;

			var chkVal = [];
			//Added this case for Deleting Test/Tasks
			$('#console_data .chk_record:checked').each(function(id, ele){
                var value = $(ele).val();
                if($(ele).data('section') && typeof($(ele).data('section'))!=='undefined') {
                    value = $(ele).val()+'||'+$(ele).data('section');
                }
				if(value && typeof(value) !== 'undefined') chkVal.push(value);
			});

			if(chkVal.length){
				validate = true;
				chk_values = chkVal.join(',');
			}

			if(validate === false){
				top.fAlert('Please select a record to proceed');
				return false;
			}
		}

		if(typeof(folder_id) == 'undefined' || !folder_id || folder_id == 0) folder_id = false;

        if(folder_id) {
            del_msg(chk_values, 0, folder_id);
        }
		switch (act)
		{
			case 'sent':
				del_msg(chk_values, 1);
				break;
			case 'inbox':
				del_msg(chk_values, 0);
				break;
			case 'future_alerts':
				del_msg(chk_values, 2);
				break;
			case 'consent':
				$.ajax({
					url: 'ajax_html.php?from=console&task=consent_sign_selected&chs_ids=' + chk_values,
					type: 'POST',
					success: function (r)
					{
						top.fAlert('Selected Forms have been signed successfully');
						elem = $('#consent_forms');
						do_action('forms_letters', elem)
					}
				});
				break;
			case 'sx_consent':
				$.ajax({
					url: 'ajax_html.php?from=console&task=sx_consent_sign_selected&chs_ids=' + chk_values,
					type: 'POST',
					success: function (r)
					{
						top.fAlert('Selected Forms have been signed successfully');
						elem = $('#sx_consent_forms');
						do_action('forms_letters', elem)
					}
				});
				break;
			case 'op_notes':
				$.ajax({
					url: 'ajax_html.php?from=console&task=op_notes_sign_selected&chs_ids=' + chk_values,
					type: 'POST',
					success: function (r)
					{
						top.fAlert('Selected Forms have been signed successfully');
						elem = $('#op_notes');
						do_action('forms_letters', elem)
					}
				});
				break;
			case 'consult_letters':
				$.ajax({
					url: 'ajax_html.php?from=console&task=consult_letters_sign_selected&chs_ids=' + chk_values,
					type: 'POST',
					success: function (r)
					{
						top.fAlert('Selected Forms have been signed successfully');
						elem = $('#consult_letters');
						do_action('forms_letters', elem)
					}
				});
				break;
			case 'tests':
				fancyConfirm('The selected patient(s)will be removed from the Test/tasks list.', 'Confirm', 'del_tests(\''+chk_values+'\')');
				//del_tests(chk_values);
				break;
			case 'save_tests':
				save_tests(chk_values);
				break;
			case 'del_pt_messages':
				del_pt_messages(chk_values);
				break;
			case 'delete_approvals':
				del_approvals(chk_values);
				break;

			case 'complete_task':
				$.ajax({
					url: 'ajax_html.php?from=console&task=complete_task&chs_ids=' + chk_values,
					type: 'POST',
					dataType:'JSON',
					success: function (r)
					{
						if(r > 0){
							top.fAlert('Task marked done Successfully');
							elem = $('#acc_notes');
							do_action('tests_tasks', elem)
						}
						else{
							top.fAlert('Unable to mark task done. Please try again.');
							return false;
						}
					}
				});
			break;
			case 'complete_rule_task':
				$.ajax({
					url: 'ajax_html.php?from=console&task=tm_complete_rule_task&chs_ids=' + chk_values,
					type: 'POST',
					dataType:'JSON',
					success: function (r)
					{
						if(r > 0){
							top.fAlert('Task marked done Successfully');
							elem = $('#rule_tasks');
							do_action('rule_tasks', elem);
						}
						else{
							top.fAlert('Unable to mark task done. Please try again.');
							return false;
						}
					}
				});
			break;
		}
	}
}

function un_del_messages(act)
{
	if ($.trim(act) != "")
	{
		chk_values = '';
		chk_flag = 0;
		$('.messageList input[type="checkbox"]', $('#console_data_list')).each(function ()
		{
			if ($(this).attr('checked') == 'checked' || $(this).attr('checked') == true || $(this).prop('checked') == true)
			{
				if (chk_flag == 1) {
					chk_values += ',';
				}
				chk_values += $(this).val();
				chk_flag = 1;
			}
		});
		if (chk_values == '')
		{
			top.fAlert('Please select a record to proceed');
			return false;
		}
		switch (act)
		{
			case 'deleted_messages':
				un_del_msg(chk_values, 1);
				break;
		}
	}
}
function del_approvals(chk_values)
{
	$.ajax({
		url: 'ajax_html.php?from=console&task=del_approvals&chs_ids=' + chk_values,
		type: 'POST',
		success: function (r)
		{
			top.fAlert('Selected Notifications have been deleted');
			if ($("#load_pt_msg_inbox").attr("checked") == "checked" || $("#load_pt_msg_inbox").prop("checked") == true)
			{
				do_action("load_patient_messages", $("#load_pt_msg_inbox"));
			} else if ($("#load_pt_msg_sent").attr("checked") == "checked" || $("#load_pt_msg_sent").prop("checked") == true)
			{
				do_action("load_patient_messages", $("#load_pt_msg_sent"));
			} else
			{
				do_action("load_patient_messages", $("#pt_changes_approval"));
			}
		}
	});
}

function del_pt_messages(chk_values)
{
	$.ajax({
		url: 'ajax_html.php?from=console&task=del_pt_messages&chs_ids=' + chk_values,
		type: 'POST',
		success: function (r)
		{
			top.fAlert('Selected Patient Messages have been deleted');
			if ($("#load_pt_msg_inbox").attr("checked") == "checked" || $("#load_pt_msg_inbox").prop("checked") == true)
			{
				do_action("load_patient_messages", $("#load_pt_msg_inbox"));
			} else
			{
				do_action("load_patient_messages", $("#load_pt_msg_sent"));
			}
		}
	});
}

function load_top_pt_msgs()
{
	if ($("#load_pt_msg_inbox").attr("checked") == "checked" || $("#load_pt_msg_inbox").prop("checked") == true)
	{
		do_action("load_patient_messages", $("#load_pt_msg_inbox"));
	} else
	{
		do_action("load_patient_messages", $("#load_pt_msg_sent"));
	}
}

function del_unfinalized_pats(flgdel)
{
	//refresh list
	if (flgdel == 0) {
		load_link_data('unfinalized_patients_opt');
		return;
	}

	chkbx_id = '';
	var selectedValues = new Array;

	if( $('#data_show_by_default').css('display') === 'none' )
	{
		chkbx_id = '#show_on_chk .chk_record_hidden:checkbox:checked';
	}
	else
	{
		chkbx_id = '#data_show_by_default .chk_record:checkbox:checked';
	}

	var selected = $(chkbx_id).map(function () {
		if( $(this).val())
		return $(this).val();
	}).get();


	if( selected.length === 0 )
	{
		top.fAlert('Please select a record to proceed');
		return false;
	}

	fancyConfirm('The selected patient(s) will be removed from the Un-finalized list.', 'Unfinalized Patient', 'del_unfinalized_pats_action(\''+chkbx_id+'\')');
}
function del_unfinalized_pats_action( selector )
{
	//console.log(status);

	if( typeof(selector) !== undefined && $.trim(selector) != '' )
	{
		var selected = $(selector).map(function () {
			if( $(this).val())
			return $(this).val();
		}).get();

		if( selected.length > 0 )
		{
			var postData = {from:'console', task:'del_unf_pats', pat_ids:selected, flgdel:1};

			$.ajax({
				url: 'ajax_html.php',
				data: postData,
				type: 'POST',
				success: function ()
				{
					fAlert('Selected Unfinalized chart note(s) are deleted successfully.');
					load_link_data('unfinalized_patients_opt');
				}
			});
		}

	}
}


/*--this function used for deleting the phrase--*/
function del_phrase(confirm)
{
	confirm = (confirm !== undefined && confirm === true) ? true : false;

	var selected = $('.smart_phrase_chkbx:checked').map(function () {
		return $(this).val();
	}).get();

	if (selected.length === 0)
	{
		top.fAlert('Please select the phrase to delete.');
		return;
	}

	if (!confirm)
		top.fancyConfirm('Are you sure want to delete the selected phrases(s)?', 'del_phrase(true)');
	else
	{
		var formData = {from: 'console', task: 'del_phrase', phrase_id: selected};

		$.ajax({
			url: 'ajax_html.php',
			type: 'POST',
			data: formData,
			success: function (r)
			{
				top.fAlert('Phrase(s) deleted successfully');
				load_smart_phrases();
				//load_link_data('smart_phrases');
			}
		});
	}
}

function show_all_unfinal_pat(ths)
{
	if ($(ths).attr('checked') == 'checked' || $(ths).attr('checked') == true || $(ths).prop('checked') == true)
	{
		$('#show_on_chk').css({'display': 'block'});
		$('#data_show_by_default').css({'display': 'none'});
	} else
	{
		$('#show_on_chk').css({'display': 'none'});
		$('#data_show_by_default').css({'display': 'block'});
	}
}

function del_resp_person()
{
	chk_values_phy_task = '';
	chk_values_msg = '';
	chk_values_order_sets = '';
	chk_flag_phy = 0;
	chk_flag_msg = 0;
	chk_flag_order_set = 0;
	$('tbody input[type="checkbox"]', $('#resp-person')).each(function ()
	{
		if ($(this).attr('checked') == 'checked' || $(this).attr('checked') == true || $(this).prop('checked') == true)
		{
			elem_val = $(this).val();
			elem_val_arr = elem_val.split('-');
			if (elem_val_arr[0] == 'phy_todo_task') {
				if (chk_flag_phy == 1) {
					chk_values_phy_task += ',';
				}
				chk_values_phy_task += elem_val_arr[1];
				chk_flag_phy = 1;
			} else if (elem_val_arr[0] == 'user_messages') {
				if (chk_flag_msg == 1) {
					chk_values_msg += ',';
				}
				chk_values_msg += elem_val_arr[1];
				chk_flag_msg = 1;
			} else if (elem_val_arr[0] == 'order_set_associate_chart_notes') {
				if (chk_flag_msg == 1) {
					chk_values_order_sets += ',';
				}
				chk_values_order_sets += elem_val_arr[1];
				chk_flag_order_set = 1;
			}

		}
	});
	if (chk_flag_phy == 0 && chk_flag_msg == 0 && chk_flag_order_set == 0)
	{
		fAlert('Please select a record to proceed');
		return false;
	}
	cr = confirm('Are you sure you want to delete the selected Record(s)');
	if (cr == true)
	{
		$.ajax({
			url: 'ajax_html.php?from=console&task=del_resp_person&phy_ids=' + chk_values_phy_task + '&msg_ids=' + chk_values_msg + '&order_set_ids=' + chk_values_order_sets,
			type: 'POST',
			success: function (r)
			{
				fAlert('Responsible person are deleted successfully');
				load_link_data('resp_person_opt');
			}
		});
	}
}

function del_tests(chk_values)
{
	//cr = confirm('The selected patient(s)will be removed from the Test/tasks list');
	//if (cr == true)
	//{
		$.ajax({
			url: 'ajax_html.php?from=console&task=del_tests&del_tests_vals=' + chk_values,
			type: 'POST',
			success: function (r)
			{
				top.fAlert('Selected records have been deleted successfully');
				elem_id = 'tests';
				do_action('tests_tasks', elem_id);
			}
		});
	//}
}

function save_tests(chk_values)
{
	$.ajax({
		url: 'ajax_html.php?from=console&task=save_tests&save_tests_vals=' + chk_values,
		type: 'POST',
		success: function (r)
		{
			top.fAlert('Tests have been saved successfully');
			elem_id = 'tests';
			do_action('tests_tasks', elem_id);
		}
	});
}

function del_completed_tasks()
{
	chk_values = '';
	chk_flag = 0;
	$('tbody input[type="checkbox"]', $('#comp-tasks')).each(function ()
	{
		if ($(this).attr('checked') == 'checked' || $(this).attr('checked') == true || $(this).prop('checked') == true)
		{
			if (chk_flag == 1) {
				chk_values += ',';
			}
			chk_values += $(this).val();
			chk_flag = 1;
		}
	});

	if (chk_flag == 0)
	{
		fAlert('Please select a record to proceed');
		return false;
	}

	cr = confirm('Are you sure you want to delete the selected Record(s)');
	if (cr == true)
	{
		chk_values_arr = chk_values.split(',');
		chk_values_vf = '';
		vf_flag = 0;
		chk_values_nfa = '';
		nfa_flag = 0;
		chk_values_oct = '';
		oct_flag = 0;
		chk_values_oct_rnfl = '';
		oct_rnfl_flag = 0;

		chk_values_vf_gl = '';
		vf_gl_flag = 0;

		chk_values_pachy = '';
		pachy_flag = 0;
		chk_values_ivfa = '';
		ivfa_flag = 0;
		chk_values_disc = '';
		disc_flag = 0;
		chk_values_disc_external = '';
		disc_external_flag = 0;
		chk_values_topography = '';
		topography_flag = 0;

		chk_values_test_gdx = '';
		test_gdx_flag = 0;

		chk_values_surgical_tbl = '';
		surgical_tbl_flag = 0;

		chk_values_test_labs = '';
		test_labs_flag = 0;

		chk_values_test_other = '';
		test_other_flag = 0;

		chk_values_test_bscan = '';
		test_bscan_flag = 0;

		chk_values_icg = '';
		icg_flag = 0;

		chk_values_test_cellcnt = '';
		test_cellcnt_flag = 0;

		chk_values_user_messages = '';
		user_messages_flag = 0;
		chk_values_phy_todo_task = '';
		phy_to_do_task_flag = 0;
		chk_values_orders = '';
		orders_flag = 0;

		for (i = 0; i < chk_values_arr.length; i++)
		{
			elem_val_arr = chk_values_arr[i].split('-');
			act_val = $.trim(elem_val_arr[0]);
			switch (act_val)
			{
				case 'vf':
					if (vf_flag == 1) {
						chk_values_vf += ',';
					}
					chk_values_vf += elem_val_arr[1];
					vf_flag = 1;
					break;
				case 'vf_gl':
					if (vf_gl_flag == 1) {
						chk_values_vf_gl += ',';
					}
					chk_values_vf_gl += elem_val_arr[1];
					vf_gl_flag = 1;
					break;
				case 'nfa':
					if (nfa_flag == 1) {
						chk_values_nfa += ',';
					}
					chk_values_nfa += elem_val_arr[1];
					nfa_flag = 1;
					break;
				case 'oct':
					if (oct_flag == 1) {
						chk_values_oct += ',';
					}
					chk_values_oct += elem_val_arr[1];
					oct_flag = 1;
					break;
				case 'oct_rnfl':
					if (oct_rnfl_flag == 1) {
						chk_values_oct_rnfl += ',';
					}
					chk_values_oct_rnfl += elem_val_arr[1];
					oct_rnfl_flag = 1;
					break;
				case 'pachy':
					if (pachy_flag == 1) {
						chk_values_pachy += ',';
					}
					chk_values_pachy += elem_val_arr[1];
					pachy_flag = 1;
					break;
				case 'ivfa':
					if (ivfa_flag == 1) {
						chk_values_ivfa += ',';
					}
					chk_values_ivfa += elem_val_arr[1];
					ivfa_flag = 1;
					break;
				case 'disc':
					if (disc_flag == 1) {
						chk_values_disc += ',';
					}
					chk_values_disc += elem_val_arr[1];
					disc_flag = 1;
					break;
				case 'disc_external':
					if (disc_external_flag == 1) {
						chk_values_disc_external += ',';
					}
					chk_values_disc_external += elem_val_arr[1];
					disc_external_flag = 1;
					break;
				case 'topography':
					if (topography_flag == 1) {
						chk_values_topography += ',';
					}
					chk_values_topography += elem_val_arr[1];
					topography_flag = 1;
					break;
				case 'test_gdx':
					if (test_gdx_flag == 1) {
						chk_values_test_gdx += ',';
					}
					chk_values_test_gdx += elem_val_arr[1];
					test_gdx_flag = 1;
					break;
				case 'surgical_tbl':
					if (surgical_tbl_flag == 1) {
						chk_values_surgical_tbl += ',';
					}
					chk_values_surgical_tbl += elem_val_arr[1];
					surgical_tbl_flag = 1;
					break;
				case 'test_labs':
					if (test_labs_flag == 1) {
						chk_values_test_labs += ',';
					}
					chk_values_test_labs += elem_val_arr[1];
					test_labs_flag = 1;
					break;
				case 'test_other':
					if (test_other_flag == 1) {
						chk_values_test_other += ',';
					}
					chk_values_test_other += elem_val_arr[1];
					test_other_flag = 1;
					break;
				case 'test_bscan':
					if (test_bscan_flag == 1) {
						chk_values_test_bscan += ',';
					}
					chk_values_test_bscan += elem_val_arr[1];
					test_bscan_flag = 1;
					break;
				case 'test_cellcnt':
					if (test_cellcnt_flag == 1) {
						chk_values_test_cellcnt += ',';
					}
					chk_values_test_cellcnt += elem_val_arr[1];
					test_cellcnt_flag = 1;
					break;
				case 'icg':
					if (icg_flag == 1) {
						chk_values_icg += ',';
					}
					chk_values_icg += elem_val_arr[1];
					icg_flag = 1;
					break;
				case 'user_messages':
					if (user_messages_flag == 1) {
						chk_values_user_messages += ',';
					}
					chk_values_user_messages += elem_val_arr[1];
					user_messages_flag = 1;
					break;
				case 'phy_todo_task':
					if (phy_to_do_task_flag == 1) {
						chk_values_phy_todo_task += ',';
					}
					chk_values_phy_todo_task += elem_val_arr[1];
					phy_to_do_task_flag = 1;
					break;
				default:
					if (orders_flag == 1) {
						chk_values_orders += ',';
					}
					chk_values_orders += elem_val_arr[1];
					orders_flag = 1;
					break;
			}
		}
		comp_tasks_vars = 'chk_values_vf=' + chk_values_vf + '&chk_values_nfa=' + chk_values_nfa + '&chk_values_oct=' + chk_values_oct + '&chk_values_oct_rnfl=' + chk_values_oct_rnfl + '&chk_values_vf_gl=' + chk_values_vf_gl + '&chk_values_pachy=' + chk_values_pachy + '&chk_values_ivfa=' + chk_values_ivfa + '&chk_values_disc=' + chk_values_disc + '&chk_values_disc_external=' + chk_values_disc_external + '&chk_values_topography=' + chk_values_topography + '&chk_values_surgical_tbl=' + chk_values_surgical_tbl + '&chk_values_test_labs=' + chk_values_test_labs + '&chk_values_test_cellcnt=' + chk_values_test_cellcnt + '&chk_values_test_other=' + chk_values_test_other + '&chk_values_test_bscan=' + chk_values_test_bscan + '&chk_values_icg=' + chk_values_icg + '&chk_values_test_gdx=' + chk_values_test_gdx + '&chk_values_user_messages=' + chk_values_user_messages + '&chk_values_phy_todo_task=' + chk_values_phy_todo_task + '&chk_values_orders=' + chk_values_orders;
		$.ajax({
			url: 'ajax_html.php?from=console&task=del_completed_tasks',
			type: 'POST',
			data: comp_tasks_vars,
			success: function (r)
			{
				fAlert('Selected Completed tasks have been deleted successfully');
				load_link_data('completed_tasks');
			}
		});
	}
}

function printWindowResposiblePerson() {
	window.open('responsible_task_print.php', 'print', '' + screen.height + ',' + screen.width + ',scrollbars=1,resizable=1');
}

function frm_sub()
{
	data_send = $('#order_frm').serialize();
	$.ajax({
		url: 'ajax_html.php?from=console&task=order_sets',
		type: 'POST',
		data: data_send,
		success: function (resultData)
		{
			top.fAlert('Order status updated successfully');
			set_result_data(resultData);
		}
	});
}


function pat_notify_frm_submit()
{
	data_send = $('#pat_notify_frm').serialize();
	var filterVal ='';
	if(!filterVal) filterVal = $('#patientNotifyFilter').val();
	if(filterVal == '' || typeof(filterVal) == 'undefined') filterVal = 0;
	$.ajax({
		url: 'ajax_html.php?from=console&task=load_patient_notify&filter='+filterVal,
		type: 'POST',
		data: data_send,
		success: function (resultData)
		{
			set_result_data(resultData);
		}
	});
}
function print_data(task) {
	//window.open('console_pdf.php?task='+task,'Print_Data');
	top.fAlert("this function is removed");
}

function open_next_row(ths)
{
	var flag = $(ths).next().hasClass('show1');

	$('.tr_pt_msg_details').removeClass('show1').addClass('hide');

	if (!flag)
		$(ths).next().removeClass('hide').addClass('show1');
}

function approve_operation(row_id, ths, tbl, cancel_req_id)
{
	cancel_req_id = cancel_req_id || '';
	ths_parent = $(ths).parent();
	if (row_id != "" && parseInt(row_id))
	{
		ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');
	}
	$.ajax({
		url: 'handle_pt_changes.php',
		data: 'sel_op=approve&row_id=' + row_id+'&tbl='+tbl+'&cancel_req_id='+cancel_req_id,
		type: 'POST',
		complete: function (respData)
		{
			color = '#090';
			var resp_val = respData.responseText;

			//START CODE FOR CANCEL REQUEST
			if(cancel_req_id!='' && tbl!='iportal_pghd_reqs') {
				var resp = jQuery.parseJSON(respData.responseText);
				if(resp.status == 'success') {
					resp_val = resp.approval;
				}else {
					color = '#F00';
					resp_val = resp.msg;
				}
			}
			//END CODE FOR CANCEL REQUEST
			if(respData && resp_val && resp_val=="Approved" ){
				ths_parent.html('<div style="color:'+color+';font-weight:bold;">Approved</div>');
			}
		}
	}).fail(function() {
    alert( "Error: could not connect!" );
  });
}

function disapprove_operation(row_id, ths,tbl, cancel_req_id)
{
	ths_parent = $(ths).parent();
	if (row_id != "" && parseInt(row_id))
	{
		ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');
	}
	$.ajax({
		url: 'handle_pt_changes.php',
		data: 'sel_op=decline&row_id=' + row_id+'&tbl='+tbl+'&cancel_req_id='+cancel_req_id,
		type: 'POST',
		complete: function (respData)
		{
			resultData = respData.responseText;
			var resp_val = respData.responseText;

			//START CODE FOR CANCEL REQUEST
			if(cancel_req_id!='' && tbl!='iportal_pghd_reqs') {
				var resp = jQuery.parseJSON(respData.responseText);
				if(resp.status == 'success') {
					resp_val = resp.approval;
				}else {
					resp_val = resp.msg;
				}
			}
			//END CODE FOR CANCEL REQUEST

			if(respData && resp_val && resp_val=="Declined" ){
				ths_parent.html('<div style="color:#F00;font-weight:bold;">Declined</div>');
			}
		}
	}).fail(function() {
    alert( "Error: could not connect!" );
  });
}
function sync_direct() {
	$('#console_data').html('<div class="doing"></div>');
	$.ajax({
		url: 'sync_direct.php',
		data: 'sync_type=inbox',
		type: 'POST',
		beforeSend: function(){
			hideloaderFlag = false;
		},
		complete: function (respData)
		{
			resultData = respData.responseText;
			load_direct_messages();
			$("#direct_msg_sent").removeAttr("checked");
			$("#direct_msg_inbox").prop("checked", true);
		}
	});
}

function make_unbold_direct_msg(direct_msg_id, ths)
{
	if (direct_msg_id == "") {
		return false;
	}
	$.ajax({
		url: 'ajax_html.php?from=console&task=set_read_direct_msg',
		type: 'POST',
		data: 'direct_msg_id=' + direct_msg_id,
		complete: function (resp) {
			$(ths).removeClass('bold');
			chkForUnreadMsg(true);
		}
	})
}
function submit_direct(mode, msg) {
	frm_data = $('#frmDirect').serialize();
	var filter_val = $('#frmDirect').find('input[name^=filter]').val();
	if (document.getElementById('mode').value == "delete") {
		if (!msg) {
			is_chk_checked = false;
			$('.chk_record').each(function (index, element) {
				if ($(this).attr('checked') == true || $(this).prop('checked') == true) {
					is_chk_checked = true
				}
			});
			if (is_chk_checked) {
				fancyConfirm("Do you want to delete the record(s)", "", "submit_direct('" + filter_val + "',true);");
				return;
			} else {
				top.fAlert("Please select any record to delete");
				return;
			}
		}
	}
	$.ajax({
		type: "POST",
		url: "direct_messages.php",
		data: frm_data,
		success: function (d) {
			do_action('load_direct_messages', mode);
		}
	});
}
function new_direct(pt_id) {
	//window.open("direct_messages_console.php",'Send Direct Message','width=950,height=400,resizable')
	$('body').on('show.bs.modal','#divContainer',function(){
		document.frmForm.reset();
		$("#reply_of").val('');
		$("#to_email").val('');
		$("#from_email_send").val('');
		$("#subject").val('');
		$("#body").val('');
	});
	$("#divContainer").modal('show');

	if(pt_id == ''){
		$('body').on('hide.bs.modal','#divContainer',function(){
			document.frmForm.reset();
			$('#patientId').val('');
			if($("#attchPtDocModal").length>0){
				$("#attchPtDocModal").remove();
			}
		});

		$('body').on('show.bs.modal','#divContainer',function(){
			document.frmForm.reset();
		});
	}
	load_ptcomm_ptinfo(pt_id);
}
function load_ptcomm_ptinfo(ptid) {
	$.ajax({
		url: URL + '/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&ptid=' + ptid,
		type: 'POST',
		success: function (r) {
			if($('#tdPatDOS').length>0)$('#tdPatDOS').html('');
			$('#pat_details_td').html(r);
			set_direct_email_typeahead($('#divContainer #to_email'));
		}
	});
}

function set_direct_email_typeahead(obj){
	$.ajax({
		url: URL + '/interface/physician_console/ajax_html.php?from=console&task=get_direct_email',
		type: 'POST',
		dataType:'JSON',
		success: function (r) {
			if(r.length && obj.length>0){
				var autocomplete = $(obj).typeahead();
				autocomplete.data('typeahead').source = r;
			}
		}
	});
}
function new_pt_message() {
	//window.open("direct_messages_console.php",'Send Direct Message','width=950,height=400,resizable')
	$("#reply_of").val('');
	$("#txt_patient_name").val('');
	$("#patientId").val('');
	$("#subject").val('');
	$("#body").val('');
	$("#divContainer").unbind('shown.bs.modal');
	$("#divContainer").unbind('hide.bs.modal');
	$("#divContainer").modal('show').on('shown.bs.modal', function(){
		if($('#divContainer .userForRow').hasClass('hide') == false) $('#divContainer .userForRow').addClass('hide');
		setForwardOption();
	});
	$("#divContainer").on('hide.bs.modal', function(){
		$("#upldModal, #attchPtDocModal").hide().data( 'bs.modal', null ).remove();
	});
	$("#upldModal, #attchPtDocModal").hide().data( 'bs.modal', null ).remove();
}

function addslashes(string) {
    return string.replace(/\\/g, '\\\\').
        replace(/\u0008/g, '\\b').
        replace(/\t/g, '\\t').
        replace(/\n/g, '\\n').
        replace(/\f/g, '\\f').
        replace(/\r/g, '\\r').
        replace(/'/g, '\\\'').
        replace(/"/g, '\\"');
}


function get_xml_section(count,content,file_name,pt_details,zip_name,direct_attach_id,patient_suggestion,comm_name,pt_appt_details){
	var section_str = '';
	var content_str = '';
	var pt_str = '';
	//XML Block
	if(content != ''){
		var iframe_height = (screen.availHeight - 450);
		content_str += '<div class="col-sm-12 pt10" id="xml_content_'+count+'">';
		content_str += '<iframe name="iframe_ccda" id="iframe_ccda_'+count+'" frameborder="0" width="100%" height="'+iframe_height+'px" src="cda_viewer.php?ccda_file='+content+'&force_ccd_viewer=yes&fileToShow='+file_name+'"></iframe>';
		/*	content_str += '<script>var iframe_ccda = document.getElementById("iframe_ccda_'+count+'");var iframe_ccda = iframe_ccda.contentDocument || iframe_ccda.contentWindow.document;iframe_ccda.write("'+addslashes(content)+'");</script>'; */
		content_str += '</div>';
	}

	//Pt. Block
	var pt_id = '';
	var pt_sch_id = '';
	var pt_name = '';
	var pt_file_name = '';
	if(pt_details != ''){
		pt_name = pt_details.lname+', '+pt_details.fname+' '+pt_details.mname;
		pt_id = pt_details.id;
		pt_sch_id = pt_details.sch_id;
		if(pt_details.lname != '' && pt_details.fname != ''){
			pt_name = pt_name+' - '+pt_id;
		}
	}

	var pt_appt_info = '';
	if(pt_appt_details!= null )
	{
		pt_appt_info = pt_appt_details;
	}

	//Pt. Suggestion Block
	var more_link = '';
	var pt_suggest_id_arr = new Array;

	if(patient_suggestion.length > 4){
		more_link = '<span class="text_purple pointer pull-right" onclick="$(\'#div_ccda_main\').find(\'#pt_block_'+count+'\').find(\'#txt_patient_name\').attr(\'data-called-one\',\'yes\');searchPatient(\'\',\''+comm_name+'\',\'Active\');">Manual Search</span>';
	}else if(!patient_suggestion.length){
		more_link = '<span class="text_purple pointer pull-right" onclick="create_patient_ccda(\''+zip_name+'\',\''+file_name+'\',\''+direct_attach_id+'\');">Create Patient</span>';
	}
	var pt_suggest_str = '<div id="pt_suggest_'+count+'" class="adminbox col-sm-12 mt10"><div class="head"><div class="row"><div class="col-sm-3"><span>Auto Matched Patients</span></div><div class="col-sm-9">'+more_link+'</div></div></div><div class="row"><div class="alert alert-warning"> <strong>No results for automatch patients, please search manually.</strong></div></div></div>';
	if(patient_suggestion.length){
		pt_suggest_str = '';
		var counter = 1;
		$.each(patient_suggestion,function(id,val){
			if(counter <=4){
				var checked = '';
				if(val['id'] != pt_id){
					pt_suggest_id_arr.push(val['id']);
				}
				if(val['id'] == pt_id){
					checked = 'checked';
				}
				var panel_class = 'info';
				if(val['id'] == pt_id){
					panel_class = 'success';
				}
				pt_suggest_str += '<div class="col-sm-3">';
					pt_suggest_str += '<div class="panel panel-'+panel_class+'">';
						pt_suggest_str += '<div class="panel-heading">';
							pt_suggest_str += '<div class="radio radio-inline">';
								pt_suggest_str += '<input type="radio" id="suggested_pt_'+id+'" name="suggested_xml_pt" data-id="'+val['id']+'" data-fname="'+val['fname']+'" data-lname="'+val['lname']+'" data-sex="'+val['sex']+'" data-dob="'+val['DOB']+'" data-zip="'+val['postal_code']+'" data-parent="pt_block_'+count+'" data-appt="suggested_pt_appt_'+id+'" '+checked+' onclick="set_sel_pt_values(this);">';
								pt_suggest_str += '<label for="suggested_pt_'+id+'">'+val['lname']+', '+val['fname']+' '+val['mname']+' - '+val['id']+'</label>';
							pt_suggest_str += '</div>';
						pt_suggest_str += '</div>';
						pt_suggest_str += '<div class="panel-body">';
								pt_suggest_str += '<div class="row">';
								if(val['sex'] && val['sex']!== null)
									pt_suggest_str += '<div><strong>Sex</strong> : '+val['sex'];
									pt_suggest_str += '<div class="form-group" style="width:150px;float:right;">';
									pt_suggest_str += 'Appointments<br/>';
									pt_suggest_str += '<select name="txt_appt_info" id="suggested_pt_appt_'+id+'" name="suggested_xml_pt_appt" class="form-control minimal" onchange="$(\'#suggested_pt_'+id+'\').prop(\'checked\',true);set_sel_pt_values($(\'#suggested_pt_'+id+'\'));">';
									pt_suggest_str += '<option value="0"></option>';
									$(pt_appt_info[val['id']]).each(function(index, element) {
										element_arr = element.split(':~:');
										pt_suggest_str += '<option value="'+element_arr[1]+'">'+element_arr[0]+'</option>';
                                    });

									pt_suggest_str += '</select>';
									pt_suggest_str += '</div>';
									pt_suggest_str += '</div>';
								pt_suggest_str += '</p>';



							if(val['DOB'] && val['DOB']!== null) pt_suggest_str += '<p><strong>DOB</strong> : '+val['DOB']+'</p>';
							if(val['postal_code'] && val['postal_code']!== null) pt_suggest_str += '<p><strong>Zip</strong> : '+val['postal_code']+'</p>';
							pt_suggest_str += '</div>';
						pt_suggest_str += '</div>';
					pt_suggest_str += '</div>';
				pt_suggest_str += '</div>';
			}
			counter++;
		});
		if(pt_suggest_str != ''){
			pt_suggest_str = '<div id="pt_suggest_'+count+'" class="adminbox col-sm-12 mt10"><div class="head"><div class="row"><div class="col-sm-3"><span>Auto Matched Patients</span></div><div class="col-sm-9">'+more_link+'</div></div></div><div class="row"><div class="row">'+pt_suggest_str+'</div></div></div>';
			//pt_suggest_str = '<div id="pt_suggest_'+count+'" class="adminbox col-sm-12 mt10"><div class="head"><span>Auto Matched Patients</span></div><div class="row">'+pt_suggest_str+'</div></div>';
		}
	}

	var disabled_val = '';
	if(pt_details.show_chk){
		disabled_val = 'disabled';
	}

	pt_str += '<div class="col-sm-12" id="pt_block_'+count+'">';
		pt_str += '<div class="row">';
			pt_str += '<div class="col-sm-2">';
				pt_str += '<label for="txt_patient_name">Patient Name</label>';
				pt_str += '<div class="input-group">';
					pt_str += '<input type="hidden" id="pt_file_zip" name="pt_file_zip" value="'+zip_name+'">';
					pt_str += '<input type="hidden" id="pt_file_name" name="pt_file_name" value="'+file_name+'">';
					pt_str += '<input type="hidden" id="pt_sel_id" name="patient_id" value="'+pt_id+'">';
					pt_str += '<input type="hidden" id="pt_sel_sch_id" name="patient_sch_id" value="'+pt_sch_id+'">';
					pt_str += '<input type="hidden" id="direct_attach_id" name="direct_attach_id" value="'+direct_attach_id+'">';
					pt_str += '<input type="text" id="txt_patient_name" name="txt_patient_name" onKeyPress="{if (event.keyCode==13)return searchPatient(this)}" value="'+pt_name+'" class="form-control" placeholder="Search Patient...." '+disabled_val+'/>';
					pt_str += '<label class="input-group-addon" onclick="chk_patient($(\'#pt_block_'+count+' #txt_patient_name\'));searchPatient($(\'#pt_block_'+count+' #txt_patient_name\'));">';
						pt_str += '<span class="glyphicon glyphicon-search"></span>';
					pt_str += '</label>';
				pt_str += '</div>';
			pt_str += '</div>';
			if(pt_details.show_chk){
				pt_str += '<div class="col-sm-6 text-center">';
					pt_str += '<div class="pt5"> </div>';
					pt_str += '<div class="col-sm-4 form-group" style="width:350px; border :0px solid #000;">';
					pt_str += '<table class="table table-collapse"><tr><td style="border:0px;">Appointment: </td>';
					pt_str += '<td style="border:0px;"><select name="txt_appt_info" id="asssigned_pt_appt_id" name="asssigned_pt_appt_id" class="form-control minimal pull-right" onchange="" style="width:150px;"><option value="0"></option>';
					$(pt_appt_info[pt_id]).each(function(index, element) {
						element_arr = element.split(':~:');
						pt_appt_info_selected = '';
						if(typeof(pt_sch_id)!='undefined' && pt_sch_id==element_arr[1]) pt_appt_info_selected = ' selected';
						pt_str += '<option value="'+element_arr[1]+'"'+pt_appt_info_selected+'>'+element_arr[0]+'</option>';
					});

					pt_str += '</select></td>';
					pt_str += '<td style="border:0px;"><input type="button" value="Save" class="btn btn-success" onclick="move_doc_to_pt(this,\'save\');" data-parent="#pt_block_'+count+'"></td></tr></table>';
					pt_str += '</div>';

					pt_str += '<label><span class="glyphicon glyphicon-ok" style="color:#60cc60"></span> Moved to Pt. Docs</label>';
				pt_str += '</div>';

				pt_str += '<div class="col-sm-1">';
					pt_str += '<div class="pt5"> </div>';
					pt_str += '<button class="btn btn-primary" type="button" onclick="move_doc_to_pt(this,\'import\');" data-parent="#pt_block_'+count+'">Import</button>';
				pt_str += '</div>';
			}else{
				if(disabled_val != 'disabled'){
					pt_str += '<div class="col-sm-2">';
						pt_str += '<label> </label>';
						pt_str += '<div class="form-group">';
							pt_str += '<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient(this)}" class="form-control minimal">';
								pt_str += '<option value="Active">Active</option>';
								pt_str += '<option value="Inactive">Inactive</option>';
								pt_str += '<option value="Deceased">Deceased</option>';
								pt_str += '<option value="Resp.LN">Resp.LN</option>';
								pt_str += '<option value="Ins.Policy">Ins.Policy</option>';
							pt_str += '</select>';
						pt_str += '</div>';
					pt_str += '</div>';
				}

				pt_str += '<div class="col-sm-2">';
					pt_str += '<div class="pt5"> </div>';
					pt_str += '<div class="checkbox checkbox-inline">';
						pt_str += '<input type="checkbox" id="ccda_entry_status_'+count+'" name="ccda_entry_status" checked/>';
						pt_str += '<label for="ccda_entry_status_'+count+'">Move to Pt. Docs</label>';
					pt_str += '</div>';
				pt_str += '</div>';

				pt_str += '<div class="col-sm-1">';
					pt_str += '<div class="pt5"> </div>';
					pt_str += '<button class="btn btn-success" type="button" onclick="move_doc_to_pt(this,\'save\');" data-parent="#pt_block_'+count+'">Save</button>';
				pt_str += '</div>';

				pt_str += '<div class="col-sm-1">';
					pt_str += '<div class="pt5"> </div>';
					pt_str += '<button class="btn btn-primary" type="button" onclick="move_doc_to_pt(this,\'import\');" data-parent="#pt_block_'+count+'">Import</button>';
				pt_str += '</div>';
			}

			pt_str += '<div class="col-sm-1">';
				pt_str += '<div class="pt5"> </div>';
				pt_str += '<button class="btn btn-primary" type="button" onclick="print_this_frame(this);" data-parent="#xml_content_'+count+'">Print</button>';
			pt_str += '</div>';

		pt_str += '</div>';
	pt_str += '</div>';

	//Full section
	section_str += '<div id="xml_sec_'+count+'" class="row">';
		if(pt_str.length){
			section_str += pt_str;
		}

		if(!pt_details.show_chk){
			if(pt_suggest_str.length){
				section_str += pt_suggest_str;
			}
		}

		if(content_str.length){
			section_str += content_str;
		}

	section_str += '</div>';
	return section_str;
}

function create_patient_ccda(zip_name,file_name,direct_attach_id){
	var form_data = 'filter=get_ccda_patient&zip_name='+zip_name+'&file_name='+file_name;
	$.ajax({
		url:"direct_messages.php",
		type:'POST',
		data:form_data,
		dataType:'JSON',
		beforeSend:function(){
			$("#directLoader").show();
		},
		success:function(response){
			if(response.error){
				fAlert(response.error);
			}else{
				//console.log(response);
				var pt_data = response.patientData;
				var mapped_fields_arr = response.FieldsMappedArr;
				var zipName = response.zip_name;
				var fileName = response.file_name;
				var main_pt_str = '<div class="col-sm-12"></div>';
				var pt_str = '';
				$.each(pt_data,function(id,val){
					if(id.length){
						$.each(val,function(dt_key,dt_val){
							if(dt_val != ''){
								var id_val = dt_key.replace('','_');
								pt_str += '<div class="col-sm-3">';
									pt_str += '<div class="form-group">';
										pt_str += '<label for="'+id_val+'">'+dt_key+'</label>';
										pt_str += '<input class="form-control" type="text" id="'+id_val+'" name="'+mapped_fields_arr[dt_key]+'" value="'+dt_val+'" disabled/>';
										pt_str += '<input type="hidden" name="'+mapped_fields_arr[dt_key]+'" value="'+dt_val+'"/>';
									pt_str += '</div>';
								pt_str += '</div>';
							}
						});

						/* main_pt_str += '<div class="adminbox col-sm-12 mt10">';
							main_pt_str += '<div class="head">';
								main_pt_str += '<span>'+id+'</span>';
							main_pt_str += '</div>';
							var pt_str = '';
							$.each(val,function(dt_key,dt_val){
								if(dt_val != ''){
									var id_val = dt_key.replace('','_');
									pt_str += '<div class="col-sm-4">';
										pt_str += '<div class="form-group">';
											pt_str += '<label for="'+id_val+'">'+dt_key+'</label>';
											pt_str += '<input class="form-control" type="text" id="'+id_val+'" name="'+mapped_fields_arr[dt_key]+'" value="'+dt_val+'" disabled/>';
										pt_str += '</div>';
									pt_str += '</div>';
								}
							});
							main_pt_str += '<div class="pd5">';
								main_pt_str += pt_str;
							main_pt_str += '</div>';
						main_pt_str += '</div>'; */
					}
				});
				//console.log(pt_str);
				var pt_show_str = '<div class="row"><div class="adminbox col-sm-12"><div class="head">Do you want to create patient with following details ?</div><form name="pt_create_form" id="pt_create_form" method="POST"><input type="hidden" name="zip_name" value="'+zipName+'"/><input type="hidden" name="file_name" value="'+fileName+'"/><input type="hidden" name="direct_attach_id" value="'+direct_attach_id+'" />'+pt_str+'</form></div></div>';
				var footer_content = '<div class="row"><div class="col-sm-12"><button class="btn btn-success" onclick="create_patient($(\'#pt_create_div\'));" type="button">Confirm</button><button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></div></div>';
				show_modal('pt_create_div','Create Patient',pt_show_str,footer_content,'600','modal_70');
			}
			return false;
		},
		complete:function(){
			$("#directLoader").hide();
		}
	});
}

function launch_view_uploaded_ccda(checkxsl){
	if(typeof(checkxsl)=='undefined'){checkxsl = ''; modaltitle = 'View C-CDA Document';}else{modaltitle='View XML Document';}
	xml_path = $('#xml_file_path').val();
	xml_path = '/'+xml_path.replace('/users/','');
	var iframe_height = (screen.availHeight - 320);
	content_str = '<div class="col-sm-12 pt10">';
	content_str += '<iframe name="iframe_ccda" id="iframe_ccda" frameborder="0" width="100%" height="'+iframe_height+'px" src="cda_viewer.php?ccda_file='+xml_path+'&check_xsl='+checkxsl+'"></iframe>';
	content_str += '</div>';
	var footer_content = '<div class="row"><div class="col-sm-12"><button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></div></div>';
	top.show_modal('view_ccda',modaltitle,content_str,footer_content,'600','modal_90');
}


function create_patient(obj){
	var form_data = $(obj).find('#pt_create_form').serialize()+'&filter=create_new_patient';
	//console.log(form_data);
	$.ajax({
		url:"direct_messages.php",
		type:'POST',
		data:form_data,
		dataType:'JSON',
		beforeSend:function(){
			$("#directLoader").show();
		},
		success:function(response){
			if(response.error){
				fAlert(response.error);
				return false;
			}

			if(response.count > 0){


				$('#pt_create_div').modal('hide');
				if(response.zip_name!=''){
					view_ccda(response.zip_name,response.direct_attach_id,false);
					fAlert('Patient created successfully.');
				}else if(response.file_name != ''){
					view_ccda(response.file_name,response.direct_attach_id, false, false);
					fAlert('Patient created successfully.');
				}else{
					load_link_data('import_ccda_opt');
					do_pt_doc_then_reconcile(response.patient_id,'/users'+response.file_name);
				}
			}
		},
		complete:function(){
			$("#directLoader").hide();
		}
	})
}

function do_pt_doc_then_reconcile(patient_id,file_name){
	var newWin = window.open('import_ccda.php?pt_id='+patient_id+'&xml_file='+encodeURI(file_name)+'','Import CCDA','resizable=yes,scrollbars=yes,width='+(screen.availWidth - 100)+',height='+(screen.availHeight - 100)+'');
}

function move_doc_to_pt(obj,import_val){
	var validate = true;
	var pt_id = '';
	var pt_sch_id = '';
	var direct_attach_id = '';
	var zip_name = '';
	var file_name = '';
	var pt_name = '';
	var msg = '';
	var move_ccda = 0;

	var prnt_elem = $(obj).data('parent');
	prnt_elem = $(prnt_elem);

	if(prnt_elem.length){
		//Direct attach id
		if(prnt_elem.find('#direct_attach_id').val() != ''){
			direct_attach_id = prnt_elem.find('#direct_attach_id').val();
		}

		//Pt. ID
		if(prnt_elem.find('#pt_sel_id').val() != ''){
			pt_id = prnt_elem.find('#pt_sel_id').val();
		}

		//Pt. appointment ID
		if(prnt_elem.find('#pt_sel_sch_id').val() != ''){
			pt_sch_id = prnt_elem.find('#pt_sel_sch_id').val();
		}

		//Pt. Name
		if(prnt_elem.find('#txt_patient_name').val() != ''){
			pt_name = prnt_elem.find('#txt_patient_name').val();
		}

		//Zip name
		if(prnt_elem.find('#pt_file_zip').val() != ''){
			zip_name = prnt_elem.find('#pt_file_zip').val();
		}

		//File name
		if(prnt_elem.find('#pt_file_name').val() != ''){
			file_name = prnt_elem.find('#pt_file_name').val();
		}

		//Move to Pt. Docs
		if(prnt_elem.find('[name^=ccda_entry_status]').length){
			if(prnt_elem.find('[name^=ccda_entry_status]').is(':checked') == true){
				move_ccda = 1;
			}
		}

		//Pt. details validate
		if(pt_name == ''){
			msg = 'Patient Name is required';
		}
	}

	if(msg.length){
		fAlert(msg);
		return false;
	}

	if(pt_id == '' || file_name == ''){
		fAlert('Error in receving details. Try reloading the page');
		return false;
	}else{
		if(!import_val){

		}else{
			manage_ccda_doc(direct_attach_id,pt_id,file_name,zip_name,'ccda_docs_entry',move_ccda,pt_sch_id,import_val);
		}
	}
}

function manage_ccda_import(direct_attach_id,pt_id,file_name,zip_name,form_data){
	var url = URL+'/interface/physician_console/import_ccda.php';
	var frm_data = 'upload_file=yes&direct_attach_id='+direct_attach_id+'&pt_id='+pt_id+'&file_name='+file_name+'&zip_name='+zip_name+'&chk_ccda=yes';
	if(form_data){
		frm_data = form_data;
	}
	$.ajax({
		url:url,
		data:frm_data,
		type:'GET',
		dataType:'JSON',
		success:function(response){
			//{"save_ccda":"yes","xml_id":",129","upload_done":"yes"}
			if(response.chk_ccda){
				form_data = 'upload_file=yes&direct_attach_id='+direct_attach_id+'&pt_id='+pt_id+'&file_name='+file_name+'&zip_name='+zip_name+'&save_ccda=yes';
				if(response.error){
					fancyConfirm(response.error+'<span class="pt10">Do you still want to continue ?</span>','manage_ccda_import("'+direct_attach_id+'","'+pt_id+'","'+file_name+'","'+zip_name+'","'+form_data+'")');
				}else{
					manage_ccda_import(direct_attach_id,pt_id,file_name,zip_name,form_data);
				}
			}

			if(response.save_ccda){
				var xml_id = response.xml_id.split(',');
				xml_id = xml_id.join(',');
				zip_name = zip_name.replace('/users/','/');
				//view_ccda(zip_name,direct_attach_id,true);
				var newWin = window.open('import_ccda.php?pt_id='+pt_id+'&xml_id='+xml_id+'&file_name='+file_name+'&direct_message_id='+direct_attach_id,'Import CCDA','resizable=yes,scrollbars=yes,width='+(screen.availWidth - 100)+',height='+(screen.availHeight - 100)+'');

			}
		}
	});
}

function manage_ccda_doc(direct_attach_id,pt_id,file_name,zip_name,filter,move_ccda,pt_sch_id,import_val){
	//filter=ccda_docs_entry
	if($('#asssigned_pt_appt_id').get(0)) pt_sch_id = $('#asssigned_pt_appt_id').val();
	var form_data = 'pt_id='+pt_id+'&pt_sch_id='+pt_sch_id+'&ccda_file='+file_name+'&zip_file='+zip_name+'&direct_attach_id_new='+direct_attach_id+'&move_ccda='+move_ccda;
	$.ajax({
		url:"direct_messages.php?filter="+filter,
		type:'POST',
		data:form_data,
		dataType:'JSON',
		beforeSend:function(){
			$("#directLoader").show();
		},
		success:function(response){
			if(pt_win != ''){
				pt_win.close();
			}
			if(filter == 'chk_exist_ccda'){
				if(response.chk_exist == false){
					manage_ccda_doc(direct_attach_id,pt_id,file_name,zip_name,'ccda_docs_entry',move_ccda,pt_sch_id,import_val);
				}else{
					fancyConfirm('Requested CCDA already exists in Pt. Docs for <b>'+response.pt_name+'</b><br /> Do you want to overwrite previous CCDA ?','manage_ccda_doc("'+direct_attach_id+'","'+pt_id+'","'+file_name+'","'+zip_name+'","ccda_docs_entry",'+move_ccda+');');
					return false;
				}
			}

			if(typeof(import_val)=='undefined' || import_val != 'save'){
				if(response.error){
					fAlert(response.error);
					return false;
				}else{
					form_data = 'upload_file=yes&direct_attach_id='+direct_attach_id+'&pt_id='+pt_id+'&file_name='+file_name+'&zip_name='+zip_name+'&save_ccda=yes';
					manage_ccda_import(direct_attach_id,pt_id,file_name,zip_name,form_data);
					return true;
					/* fAlert('CCDA moved successfully');
					$('#div_ccda_main').modal('hide');
					return false; */
				}
			}else if(typeof(import_val)!='undefined' && import_val == 'save'){
				top.fAlert('Record updated.');
				view_ccda(zip_name.substr(6),direct_attach_id,null,null,false);
			}

		},
		complete:function(){
			$("#directLoader").hide();
		}
	});
}

function set_xml_modal_values(id,name){
	var prnt_elem = $('#div_ccda_main').find('[data-called-one]');
	if(prnt_elem.length){
		prnt_elem.val(name+' - '+id);
		prnt_elem.siblings('#pt_sel_id').val(id);
	}else{
		$('#div_ccda_main').find('#txt_patient_name:first').val(name+' - '+id);
		$('#div_ccda_main').find('#pt_sel_id:first').val(id);
	}

	if(pt_win != ''){
		pt_win.close();
	}
}

// Clean the unzip directory
function cleanUnZipDirectory(){
	$.ajax({
		type: "GET",
		url: URL+"/library/cda_viewer/index.php?cleanDirectory=1",
		success: function(resp){

		}
	})
}

function view_ccda(ccda_file,direct_id,refreshh,attach_id,askCon){
	if(typeof(askCon)=='undefined') askCon=true;
	if(typeof(attach_id)=='undefined') attach_id = '';
	if(askCon){
		// Clean the unzip directory
		cleanUnZipDirectory();

		$('#xml_file_path').val(ccda_file);
		YsAction = "top.view_ccda('"+ccda_file+"','"+direct_id+"',"+refreshh+",'"+attach_id+"',false)";
		NoAction = "top.launch_view_uploaded_ccda('check_xsl')";
		fancyConfirm('<div class="text-center">Do you want to open this XML file with C-CDA viewer? <br><small>Pressing <b>NO</b> will open it with plain XML viewer.</small></div>', 'Please select', YsAction, NoAction);
		return;
	}

	$("#directLoader").html("<div class='doing'></div>");
	$("#directLoader").show();
	$.ajax({
		url: "direct_messages.php?filter=view_ccda&ccda_file="+ccda_file+'&direct_attach_id='+direct_id+'&attach_id='+attach_id,
		type: "POST",
		success: function(r){
			r = $.trim(r);
			if($.trim(r) != '' || r.length){
				var result_arr = r.split('^^^^!!^^^^!!^^^^');	//Each Section
				var iframe_str = new Array;
				$.each(result_arr,function(id,val){
					if(val != ''){
						var content = '';
						var file_name = '';
						var pt_details = '';
						var zip_name = '';
						var direct_attach_id = '';
						var pt_appt_details = '';

						var val_arr = val.split('^^!!^^!!^^');	//Values of each section
						if(val_arr[0]){
							content = val_arr[0];
						}

						if(val_arr[1]){
							file_name = val_arr[1];
						}

						if(val_arr[2]){
							pt_details = JSON.parse(val_arr[2]);
						}

						if(val_arr[3]){
							zip_name = val_arr[3];
						}

						if(val_arr[4]){
							direct_attach_id = val_arr[4];
						}

						if(val_arr[5]){
							patient_suggestion = JSON.parse(val_arr[5]);
						}

						if(val_arr[6]){
							comm_name = val_arr[6];
						}

						if(val_arr[7]){
							pt_appt_details = jQuery.parseJSON(val_arr[7]); //PATIENT APPOINTMENT INFORMATION
						}

						var sec_content = get_xml_section(id,content,file_name,pt_details,zip_name,direct_attach_id,patient_suggestion,comm_name,pt_appt_details);
						iframe_str.push(sec_content); //Whole section
					}
				});

				if(iframe_str.length){
					iframe_str = iframe_str.join('');
					if(refreshh){
						$("#div_ccda_main .modal-body").html(iframe_str);
					}else{
						$("#div_ccda_main .modal-body").html(iframe_str);
						$("#div_ccda_main").modal('show');
						$("#directLoader").hide();
					}
				}else{
					fAlert('No content found !');
					//iframe_str = '<div class="row"><div class="col-sm-12 alert alert-info text-center"><strong>No content found !</strong></div></div>';
				}
			}else{
				fAlert('No content found !');
			}

			$('body').on('hide.bs.modal','#div_ccda_main',function(){
				$("#div_ccda_main .modal-body").html('');
			});
			if($('[name=suggested_xml_pt]:checked').length){
				var elem = $('[name^=suggested_xml_pt]:checked');
				set_sel_pt_values(elem);
			}
			return false;
		}
	});
}

function view_attachment(url,a_id,m_id,sec){
	h = window.innerHeight;
	if(h){
		h = h-150;
		w = window.innerWidth - 100;
	}
	else {
		h=500;
		w=1000;
	}
	if(w>1300) w=1300;
	if(h>1000) h=1000;
	top.fancyModal('<iframe frameborder="0" src="attachment_viewer.php?full_path='+encodeURI(url)+'&a_id='+a_id+'&m_id='+m_id+'&h='+h+'&sec='+sec+'" style="width:'+w+'px; height:'+h+'px;"></iframe><div class="clearfix"></div><div class="text-center"><input type="button" value="Close" class="btn btn-danger" onclick="top.removeMessi();" /></div>','Attachment Viewer',w+'px',h+'px');
}

function set_sel_pt_values(elem){
	var obj = $(elem);
	var data_arr = obj.data();
	var pt_name = data_arr['lname']+','+data_arr['fname']+' - '+data_arr['id'];
	$('#'+data_arr['parent']).find('#pt_sel_id').val(data_arr['id']);
	$('#'+data_arr['parent']).find('input[name=txt_patient_name]').val(pt_name);

	if(typeof(data_arr['appt'])!='undefined'){
		var pt_appt_element_id = data_arr['appt'];
		if($('#'+pt_appt_element_id).get(0)){
			$('#'+data_arr['parent']).find('#pt_sel_sch_id').val($('#'+pt_appt_element_id).val());
		}
	}

}

function pt_docs(id, file) {

	$.ajax({
		url: "direct_messages.php?filter=ccda_docs_entry&patient_id=" + id + "&ccda_file=" + file,
		type: "POST",
		success: function (r) {
			if (r) {
				fancyAlert('CCDA saved in Pt. Docs', '', '', document.getElementById("divCommonAlertMsg"));
			} else {
				fancyAlert('CCDA not saved.', '', '', document.getElementById("divCommonAlertMsg"));
			}
		}
	});
}

function loadWnl(val) {
	if (val == "") {
		top.fAlert("Please select chart template .");
		return;
	}
	$("#dv_wnl_ct").append("<div style=\"position:absolute; top:100px; left: 50%; line-height:30px;padding:5px; background-color:red; color:white; font-weight:bold;border:1px solid black;\">Loading ! Please wait.. </div>");
	load_wnl_charttemplate(val);//chart template id
}

//type ahead fu
function cn_ta_fu() {
	var ta7 = function (ota7) {
		$(ota7).autocomplete({
			source: "common/requestHandler.php?elem_formAction=TypeAhead&mode=FUVisit",
			minLength: 1,
			autoFocus: true,
			focus: function (event, ui) {
				return false;
			},
			select: function (event, ui) {
				if (ui.item.value != "") {
					this.value = "" + ui.item.value;
				}
			}
		});
	};
	$("#listFU input[type=text][id*=elem_followUpVistType_]").bind("focus", function (event) {
		if (!$(this).hasClass("ui-autocomplete-input")) {
			ta7(this);
		}
		;
	});
}



function changeOther(obj, val) {
	if ((obj.value == 'Other') || (typeof val != 'undefined')) {
		val = (typeof val == 'undefined') ? "" : val;

		var idOthr = obj.id.replace(/elem_followUpVistType/g, "elem_followUpVistTypeOther");
		//var idObjDd = obj.id.replace(/elem_followUpVistType/g,"sp_followUpVistTypeMenu");
		var idOthrIcon = obj.id.replace(/elem_followUpVistType/g, "sp_fu_vis_other");

		var oOthr = gebi(idOthr);
		//var oObjDd = gebi(idObjDd);
		var oOthrIcon = gebi(idOthrIcon);
		if (oOthr && oOthrIcon) {
			oOthr.value = "1";
			obj.value = "";
			obj.focus();
			//oObjDd.style.visibility = "hidden";
			oOthrIcon.style.visibility = "visible";
		}
	}
}

//Follow up
//Add
function fu_add(flg) {
	var otbl = $("#listFU");
	var len = otbl.children().length;
	var iter = $("#listFU").attr("data-cntrfu");
	iter = parseInt(iter) + 1;

	//Coords --
	var vCords1 = $("#listFU").attr("data-fuCntr1");
	var vCords2 = $("#listFU").attr("data-fuCntr2");

	//FU Pro DD --
	var fuProDD = "";
	if ($("#listFU li select[id*='elem_fuProName_']").length > 0) {
		fuProDD = "<select name=\"elem_fuProName[]\" id=\"elem_fuProName_" + iter + "\" " +
				" onmouseover=\"if(this.selectedIndex)this.title=this.options[this.selectedIndex].text+'-'+this.value;\" >";
		fuProDD += $("#listFU li select[id*='elem_fuProName_']").eq(0).html();
		fuProDD += "</select> ";
	}

	var numMenu = menu1.text();
	numMenu = numMenu.replace(/#dynID#/g, iter);

	var visitMenu = menu2.text();
	visitMenu = visitMenu.replace(/#dynID#/g, iter);
	//FU Pro DD --

	var str = "<div class=\"row pt10\"><div class=\"col-sm-3\">" +
			"<div class='input-group'><input type=\"text\" class=\"form-control\" name=\"elem_followUpNumber[]\" id=\"elem_followUpNumber_" + iter + "\" value=\"\"" +
			" onchange=\"fu_refineNum(this)\" >" + numMenu + "</div></div>" +
			//""+getSimpleMenuJs("elem_followUpNumber_"+iter,"menu_FuNum",imgPath,1,0,"divWorkView",vCords1,1)+" "+
			//Select of Time
			"<div class=\"col-sm-3\"><select name=\"elem_followUp[]\" id=\"elem_followUp_" + iter + "\" onchange=\"fu_move(this)\" class=\"form-control minimal\">" +
			"<option value=\"\"></option>" +
			"<option value=\"Days\" >Days</option>" +
			"<option value=\"Weeks\" >Weeks</option>" +
			"<option value=\"Months\" >Months</option>" +
			"<option value=\"Year\" >Year</option>" +
			"</select> </div>" +
			"<div class=\"col-sm-3\"><div class='input-group'><input type=\"text\" name=\"elem_followUpVistType[]\" id=\"elem_followUpVistType_" + iter + "\" value=\"\" onchange=\"changeOther(this);\" class=\"form-control\">" +
			visitMenu + "</div>" +
//""+getSimpleMenuJs("elem_followUpVistType_"+iter,"menu_FuOptions",imgPath,0,0,"divWorkView",vCords2)+" "+
			"<input type=\"hidden\" name=\"elem_followUpVistTypeOther[]\" id=\"elem_followUpVistTypeOther_" + iter + "\" value=\"\"></div>" +
			fuProDD +
			"<div class=\"col-sm-3 pdl_10\"><span class=\"pdl_10 pt5 spnFuDel glyphicon glyphicon-remove link_cursor\" title=\"Remove F/U\" onclick=\"fu_del('" + iter + "');\"></span></div>" +
			"</div>";
	otbl.append(str);
	otbl.attr("data-cntrfu", iter);

	//FU Pro DD: --
	if (fuProDD != "") {
		var tmp = alwaysDocFU;
		if (typeof (alwaysDocFU) == 'undefined' || alwaysDocFU == '') {
			if (typeof (ssFollowPhy) != "undefined" && ssFollowPhy != "") {
				tmp = ssFollowPhy;
			} else if ($(":input[name*=elem_physicianId][value!=''][type!=text]").length > 0) {
				tmp = "" + $(":input[name*=elem_physicianId][value!=''][type!=text]").val();
			}
		}
		$("#elem_fuProName_" + iter).val(tmp);
	}
	//FU Pro DD --

	//Fu visit
	cn_ta_fu();

	if (flg == 1) {
		return iter;
	}

}

function fu_del(itr) {

	if (itr > 1) {
		$("#listFU>div:has(#elem_followUp_" + itr + ")").remove();
	} else {
		$("#listFU>div:has(#elem_followUp_" + itr + ") :input").val("");
	}

	/*
	 var tbl = gebi("tblFU");

	 if(typeof itr != "undefined"){
	 var id = "td6_fuId_"+itr;
	 }else{
	 var id = this.id;
	 }

	 var iter = ""+id.replace(/td6_fuId_/g,"");
	 var orows = tbl.rows;
	 var len = orows.length;

	 for(var i=0;i<len;i++){
	 if(orows[i].id == "tr_fuId_"+iter){
	 iter = i;
	 break;
	 }
	 }

	 //top.fAlert("CHK1: "+iter);

	 if(iter > 0){
	 //top.fAlert("CHK: "+iter);
	 iter = parseInt(iter);
	 tbl.deleteRow(iter);
	 }
	 */

}

function fu_refineNum(obj) {
	var arr = obj.value.split(",");
	var len = arr.length;
	var str = "";
	var tmp = "";
	var flgMv = 0;
	if (len > 1) {
		tmp = arr[0];
		if (tmp == "-") {
			if (arr[1].indexOf("-") == -1)
				str = arr[1] + tmp;
		} else {
			var ar2 = arr[1].split("-");
			if (typeof ar2[1] == "string")
				ar2[1] = myTrim(ar2[1]);
			if (ar2.length == 2 && typeof ar2[1] != "undefined" && ar2[1] == "") {
				str = arr[1] + tmp;
				flgMv = 1;
			} else {
				str = tmp;
			}
		}
	} else {
		str = obj.value;
	}

	obj.value = "" + myTrim(str);

	//Enable Days, Weeks, Month and Year drop down.
	var oSel = null;
	if (obj.id) {
		oSel = gebi(obj.id.replace(/elem_followUpNumber_/, "elem_followUp_"));
		if (oSel) {
			oSel.disabled = false;
		}
	}

	if (Date.parse(obj.value) && !$.isNumeric(obj.value)) {
		oSel.disabled = true;
	} else {
		oSel.disabled = false;
	}

	//Calendar
	if (obj.value.indexOf("Calendar") != -1) {
		obj.value = "";

		var date_global_format = 'm-d-Y';
		$("#"+ obj.id).datetimepicker({
			timepicker: false,
			format: date_global_format,
			formatDate: 'Y-m-d',
			scrollInput: false,
		});
		$("#"+ obj.id).datetimepicker('show');

		//Disable Days, Weeks, Month and Year drop down.
		if (oSel) {
			oSel.disabled = true;
		}
		//Open Calender Pop up
		//newWindow("" + obj.id);
		return false;
	} else {
		$("#"+ obj.id).datetimepicker('destroy');
	}
	//

	//Move
	if (flgMv == 1) {
		fu_move(obj);
	}
}

function fu_move(obj) {
	var id = obj.id;
	var t = null;
	if (id.indexOf("elem_followUpNumber") != -1) {
		t = gebi(id.replace(/elem_followUpNumber/, "elem_followUp"));
	} else if (id.indexOf("elem_followUp") != -1) {
		t = gebi(id.replace(/elem_followUp/, "elem_followUpVistType"));
	}
	if ((obj.value != "") && (t != null)) {
		t.focus();
	}
}


/*window.onresize = function(e)
 {
 var _wh	=	window.innerHeight;
 var _ch	=	document.getElementById('console_head_bar').clientHeight;
 var _cd	=	document.getElementById('console_data');
 _cd.style.height	=	(_wh - _ch)+'px' ;

 }*/



//////////////////////////////////////
//functions derived from core_base.js
//////////////////////////////////////



function sendReply(msgId, sendTo, subject, pId) {
	msgText = $('#reply_msg_' + msgId).val();
	var_url = "ajax_form_actions.php?task=quickreply&from=core&sendTo=" + sendTo + "&msgText=" + escape(msgText) + "&replyOf=" + msgId + "&subject=" + subject + "&msg_pid=" + pId;
//To=	sent_to_groups
	ajaxStarted();
	$('#reply_form_' + msgId + ' table').hide();
	$.ajax({
		type: "POST",
		url: var_url,
		success: function (r) {
			if (r == 'success') {
				$('#reply_form_' + msgId + ' div.replySent').fadeIn();
				$('#reply_form_' + msgId + ' div.replySent').delay(2000).fadeOut('slow');
				$('#reply_msg_' + msgId).val('');
				msgMarkRead(msgId);
			}
			ajaxDone();
		}
	});
}

var old_long_ptmsg = new Object();
function extend_read(currObj, msgId) {
	if (currObj != old_long_ptmsg) {
		$('.ptmsg .msg_text .msg_text_long').hide();
		$('.ptmsg .msg_text .msg_text_short').show();
	}
	if ($(currObj).find('.msg_text_long').css('display') == 'none') {
		$(currObj).find('.msg_text_short').hide();
		$(currObj).find('.msg_text_long').show();
		old_long_ptmsg = currObj;
		$('#ptmsg_' + msgId).removeClass('unread');
		msgMarkRead(msgId);
	} else {
		$(currObj).find('.msg_text_short').show();
		$(currObj).find('.msg_text_long').hide();
	}
}

function msgMarkRead(msgId) {
	ajaxStarted();
	var params = {task: 'markRead', from: 'core', msg_id: msgId};
	var_url = "ajax_form_actions.php";
	$.ajax({
		type: "POST",
		url: var_url,
		data: params,
		success: function (r) {
			if (r == 'success') {
				//ptcomm_loatTab(document.getElementById('ptComm_tab1'),false);//re-loading data.
			}
		}
	});
}

function msgMarkDone(msgId) {
	$('#ptmsg_' + msgId).remove();
	ajaxStarted();
	var_url = "ajax_form_actions.php?task=markDone&from=core&msg_id=" + msgId;
	$.ajax({
		type: "POST",
		url: var_url,
		success: function (r) {
			/*if(r=='success'){
			 //ptcomm_loatTab(document.getElementById('ptComm_tab1'),false);//re-loading data.
			 }*/
			ajaxDone();

		}
	});
    $('[data-toggle="tooltip"]').tooltip();
}

function ptcomm_loatTab(tab, loadingimg) {
	tab = $(tab).attr('id');
	if (typeof (loadingimg) == 'undefined' || loadingimg != false) {
		$('#tags_data_div').html('<span class="doing"></span>');
	}
	ajaxStarted();
	$.ajax({
		type: "POST",
		url: "ajax_html.php?from=core&task=" + tab,
		success: function (r) {
			$('#tags_data_div').html('');
			$('#tags_data_div').html(r);
			if (tab == 'ptComm_tab1') {
				init_messages_tab();
			}
			ajaxDone();
		}
	});
}

function ajaxStarted() {
	/*$('#div_core_notifications, .ptmsg, div.ptCommBlocks, div.ptCommBlocks .tab_text, .btn_style2, .msg_icon, .form_icon, .test_icon').each(function(){
	 $(this).addClass('cursor_wait');
	 })*/
}

function ajaxDone() {/*
 $('#div_core_notifications, .ptmsg, div.ptCommBlocks, div.ptCommBlocks .tab_text, .btn_style2, .msg_icon, .form_icon, .test_icon').each(function(){
 $(this).removeClass('cursor_wait');
 })		*/
}

function show_ptComm_info_div(pos_x, pos_y, html_content)
{
	$('#ptComm_ptDetail_div').html(html_content);
	$('#ptComm_ptDetail_div').css({'margin-left': pos_x + 100, 'margin-top': pos_y, 'display': 'block'});
	$('#ptComm_ptDetail_div').draggable();
}

function show_pt_info(ths, pt_id)
{
	pt_id = $.trim(pt_id);
	if (pt_id != "")
	{
		evt = window.event;
		pos_x = evt.x;
		pos_y = evt.y;
		show_ptComm_info_div(pos_x, pos_y, '<span class="doing"></span>');
		ajaxStarted();
		$.ajax({
			type: "POST",
			url: "ajax_html.php?from=core&task=pt_details_ajax&ptid=" + pt_id,
			success: function (r) {
				show_ptComm_info_div(pos_x, pos_y, r);
				ajaxDone();
			}
		});
	}
}

function save_wnl()
{
	$("#dv_wnl_ct").append("<div style=\"position:absolute; top:100px; left: 50%; line-height:30px;padding:5px; background-color:red; color:white; font-weight:bold;border:1px solid black;\">Saving Data ! Please wait.. </div>");
	document.frm_wnl.submit();
}


function searchPatient(ths,pt_name,pt_type){
	if(ths && typeof(ths) == 'object'){
		$('#div_ccda_container').find('[data-called-one="yes"]').removeAttr('data-called-one');
		if(!$(ths).attr('data-called-one')){
			$(ths).attr('data-called-one','yes');
		}
	}

	var parent_sib = $(ths).parent().siblings('input');
	if(parent_sib.length){
		var elem = parent_sib.val();
		if(elem != ''){
			return false;
		}
	}

	var name = document.getElementById("txt_patient_name").value;
	if(pt_name){
		name = pt_name;
	}
	var findBy = document.getElementById("txt_findBy").value;
	if(pt_type){
		findBy = pt_type;
	}
	var msg = "";
	if(name == ""){
		msg = "Please Fill Name For Search.\n";
	}
	if(findBy == ""){
		msg += "Please Select Field For Search.";
	}
	if(msg){
		top.fAlert(msg);
	}
	else{
		var validate = true;
		if(name.indexOf('-') != -1){
			name = name.replace(' ','');
			name = name.split('-');
			name = name[0]
			validate = false;
		}

		if(validate){
			if(isNaN(name)){
				pt_win = window.open("../../interface/scheduler/search_patient_popup.php?sel_by="+findBy+"&txt_for="+name+"&btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
			}
			else{
				if(pt_name){
					getPatientName(name,ths);
				}else{
					getPatientName(name);
				}
			}
		}
	}
	return false;
}

function getPatientName(id,obj){
	$.ajax({
		type: "POST",
		url: URL+"/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
		dataType:'JSON',
		success: function(r){
			if(r.id){
				if(obj){
					set_xml_modal_values(r.id,r.pt_name);
				}else{
					$("#txt_patient_name").val(r.pt_name);
					$("#patientId").val(r.id);
					load_ptcomm_ptinfo(r.id);
				}
			}else{
				fAlert("Patient not exists");
				$("#txt_patient_name").val('');
				return false;
			}
		}
	});
}

function getPatientNameManually(id){
	$.ajax({
		type: "POST",
		url: "ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
		dataType:'JSON',
		success: function(r){
			if(r.id){
				pt_name = r.lname+', '+r.fname;
				//$("#txt_patient_name").val(pt_name);
				//$("#patientId").val(r.id);
				physician_console2(r.id,pt_name)

			}else{
				fAlert("Patient not exists");
				$("#txt_patient_name").val('');
				return false;
			}
		}
	});
}

function searchPatientManually(ths,pt_name,pt_type){
	var parent_sib = $(ths).parent().siblings('input');
	if(parent_sib.length){
		var elem = parent_sib.val();
		if(elem != ''){
			return false;
		}
	}

	var name = document.getElementById("txt_patient_name").value;
	if(pt_name){
		name = pt_name;
	}
	var findBy = document.getElementById("txt_findBy").value;
	if(pt_type){
		findBy = pt_type;
	}
	var msg = "";
	if(name == ""){
		msg = "Please Fill Name For Search.\n";
	}
	if(findBy == ""){
		msg += "Please Select Field For Search.";
	}
	if(msg){
		top.fAlert(msg);
	}
	else{
		var validate = true;
		if(name.indexOf('-') != -1){
			name = name.replace(' ','');
			name = name.split('-');
			name = name[0]
			validate = false;
		}

		if(validate){
			if(isNaN(name)){
				pt_win = window.open("../../interface/scheduler/search_patient_popup.php?btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
			}
			else{
				if(pt_name){
					getPatientNameManually(name,ths);
				}else{
					getPatientNameManually(name);
				}
			}
		}
	}
	return false;
}


function chk_patient(obj) {
	if (obj.value == "") {
		document.getElementById('patientId').value = "";
	}
}

//previous name was getvalue
function physician_console(id,name){
	//If call is from xml modal
	if($('#div_ccda_main').is(':visible')){
		set_xml_modal_values(id,name);
		return false;
	}

	if(document.getElementById("patientId").value != id)
	{
		$("#pt_msg_id").val("");
	}

	document.getElementById("txt_patient_name").value = name;
	document.getElementById("patientId").value = id;
	load_ptcomm_ptinfo(id);
}

//2nd version for manual search in IMPORT C-CDA
function physician_console2(id,name){
	document.getElementById("txt_patient_name").value = name;
	document.getElementById("patientId").value = id;
	$('#btn_proceed').prop({'disabled':false,'title':''});
}

var xmlHttpGetDOS;
function GetXmlHttpObject()
{
	var objXMLHttp = null
	if (window.XMLHttpRequest)
	{
		objXMLHttp = new XMLHttpRequest()
	} else if (window.ActiveXObject)
	{
		objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP")
	}
	return objXMLHttp;
}

function reloadFunction()
{
	$(".mCustomScrollbar").each(function(index, obj){

		var elem = $(obj);
		//Setting element height
		var offset_dim = elem.offset();
		var final_height = parseInt(elem.outerHeight() + offset_dim.top);
		var window_dim = {};
		window_dim.width = screen.availWidth;
		if($('#direct_messages').length > 0) {
            dot = $('#direct_messages').offset();
            window_dim.height = parseInt(dot.top + 25);
        } else {
            window_dim.height = parseInt();
        }

		if(final_height > window_dim.height){
			var diff = parseInt(final_height - (window_dim.height));
			final_height = parseInt(final_height - (diff + 150));
		}else{
			final_height = (window_dim.height - offset_dim.top);
		}
		$(obj).css('height', (final_height)+'px');
	});

	$(".mCustomScrollbar").mCustomScrollbar({
        mouseWheel:{ scrollAmount: 380 },
		callbacks:
		{
			onInit: function ()
			{
				var elem = $(this);
				if (elem.is('.mCS_no_scrollbar.dynamicRightPadding'))
					elem.addClass('pdr_10');
				else
					elem.removeClass('pdr_10');
			},
			onOverflowYNone: function ()
			{
				if ($(this).is('.mCS_no_scrollbar.dynamicRightPadding'))
					$(this).addClass('pdr_10');
				else
					$(this).removeClass('pdr_10');
			}
		}
	});

	$('.selectpicker').selectpicker();
	$('[data-toggle="tooltip"]').tooltip();
	onload_fun();
}

function sendNewMessage(params) {
	if (params == 'undefined')
		params = '';
	var url = 'send_msg_frm.php';
	$.ajax({
		url: url,
		method: 'GET',
		data: params,
		success: function (rdata) {
			$('.replymrmsg_popup').html(rdata);

			$('#mrmsg_popup').modal({
				backdrop: false,
				show: true
			});
			// $('body').on('hidden.bs.modal', '#mrmsg_popup',function(){
			// 	$('#mrmsg_popup .modal-dialog').draggable( "destroy" );
			// });

			//$('#mrmsg_popup').modal({show: 'true'});
			reloadFunction();
		}
	});
}

function getConsoleDataId(c_name)
{
	c_name = (c_name === undefined || c_name === '') ? null : c_name;

	var c_data = null;

	if (opener)
	{
		if (opener.document.cookie.length > 0 && c_name != null)
		{
			c_start = opener.document.cookie.indexOf(c_name + "=");
			if (c_start != -1) {
				c_start = c_start + c_name.length + 1;
				c_end = opener.document.cookie.indexOf(";", c_start);
				if (c_end == -1) {
					c_end = opener.document.cookie.length;
				}
				c_data = unescape(opener.document.cookie.substring(c_start, c_end));
			}
		}
		opener.document.cookie = c_name + '=null';
	}

	return c_data;
}

function edit_phrase(recordId)
{
	if (accessEdit === undefined || !accessEdit)
	{
		fAlert('You do not have rights to add or edit the record(s).');
		return false;
	}

	var phraseData = '';
	var phraseDataId = '';

	recordId = (recordId === undefined) ? 0 : parseInt(recordId);

	if (recordId === 0)
	{
		$('#phraseModal h4.modal-title').text('Add Smart Phrase');
	} else
	{
		$('#phraseModal h4.modal-title').text('Edit Smart Phrase');

		phraseData = $('#phraseRow_' + recordId + ' > .phraseText').text();
		phraseDataId = recordId;
	}

	var modalDataArea = $('#phraseModal textarea#phrase_data');
	var modalDataId = $('#phraseModal input#phrase_data_id');

	$(modalDataArea).val(phraseData);
	$(modalDataId).val(phraseDataId);

	$('#phraseModal').modal("show").on('hide.bs.modal', function () {
		$(modalDataArea).val('');
		$(modalDataId).val('');
	});
}

function submitPhrase()
{
	var phraseData = $.trim($('#phrase_data').val());
	var phraseDataId = $('#phrase_data_id').val();

	if (phraseData === '')
	{
		fAlert('Please enter value for phrase.');
		return false;
	}

	formData = {from: 'console', task: 'smart_phrases_edit', phraseData: phraseData, phraseDataId: phraseDataId};

	$.ajax({
		url: top.URL + '/interface/physician_console/ajax_html.php?filter=getPtMessageDetails',
		method: 'POST',
		data: formData,
		success: function (resp)
		{
			if (resp == '')
				fAlert('Error in saving Data');
			else if (resp == 'duplicate')
				fAlert('Record already Exists');
			else
				fAlert('Record Saved Successfully');

			load_smart_phrases();
		},
		complete: function ()
		{
			$('#phraseModal').modal("hide");
		}
	});
}

function print_this_frame(ths){
	var parent_elem = $(ths).data('parent');
	$(parent_elem).find('iframe').get(0).contentWindow.print();
}

function delPtDetails(){
	var modal = $("#mrmsg_popup");
	if( modal.length > 0 ){
		modal.find('input#patientId,input#txt_patient_name').val('');
		modal.find('div#pat_details_td').html('');
	}
}

$.fn.printElementContent = function()
{
	var baseStyle0 = $('html link');
	var baseStyle1 = $('html style');

	/*PopUp window to print action*/
	var mywindow = window.open('', 'PRINT', 'height='+(window.outerHeight-140)+',width='+(window.outerWidth-140));

    mywindow.document.write('<html><head><title>' + document.title  + '</title>');

	/*Add Style Sheet*/
	$(baseStyle0).each(function(index, obj){
		mywindow.document.write('<link href="'+$(obj).attr('href')+'" rel="stylesheet" type="text/css" />');
	});

	/*Add On Page Style*/
	$(baseStyle1).each(function(index, obj){
		mywindow.document.write('<style>'+$(obj).html()+'</style>');
	});

    mywindow.document.write('</head><body>');
    mywindow.document.write($(this).html());
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

	$(mywindow).load(function(){
		mywindow.print();
		mywindow.close();
	});
}

function EnableDisable(curChk,puppetObjID){
	ChkStatus = $(curChk).prop('checked');
	if(ChkStatus) $('#'+puppetObjID).prop('disabled',false);
	else $('#'+puppetObjID).prop('disabled',true);
}

function checkCCDUpload(){
	var ccdFileValue = document.getElementById("ccdFile").value;
	while (ccdFileValue.indexOf("\\") != -1){
		ccdFileValue = ccdFileValue.slice(ccdFileValue.indexOf("\\") + 1);
		//alert(ccdFileValue);
	}
	while (ccdFileValue.indexOf(".") != -1){
		ccdFileValue = ccdFileValue.slice(ccdFileValue.indexOf(".") + 1);
	}
	var ext = ccdFileValue.toLowerCase();
	var arrccdFileValue = ccdFileValue.split('.');
	if(ext == 'xml'){
		if(document.getElementById('cbkEncrip').checked == true){
			if(document.getElementById('txtENCKey').value != ''){
				var val = document.getElementById('txtENCKey').value;
				if(val.length != 16){
					top.fAlert("Please Enter AES Key of 16 character.");
					return false;
				}
			}
			else{
				top.fAlert("Please Enter AES Key.");
				return false;
			}
		}
		return true;
		//document.import_CCD.submit();
	}else{
		top.fAlert('Please select a Proper CCD Document (XML file).');
		return false;
	}
}

function check_validation_status(af_name,clickedObj,OtherObj){
	var done = false;
	$.ajax({
		url: 'cda_validator.php?ccda_file='+af_name,
		type: 'GET',
		success:function(r){
			done=true;
			//a=window.open();a.document.write(r);
			//{"ccdaDocumentType":"CCD","objectiveProvided":"C-CDA_IG_Plus_Vocab","serviceError":false,"serviceErrorMessage":null,"TotalErrors":3}
			if(r!='file'){
				r = JSON.parse(r);
			}else{
				$(OtherObj).hide();
				$(clickedObj).show();
				$(clickedObj).prop('disabled',true);
				top.view_ccd_validation_details(af_name,clickedObj);
				//$(clickedObj).html('VALIDATE').prop({'title':'Click to validate this clinical document.','disabled':false});
				setTimeout(function(){$(clickedObj).html('VALIDATE').prop({'title':'Click to validate this clinical document.','disabled':false}).removeClass('button-success');$(OtherObj).hide();$(clickedObj).show();},5000);
				return;
			}
			if(typeof(r)=='object'){
				if(r.serviceError > 0){
					$(clickedObj).hide();
					$(OtherObj).html('XML Structure Error!').addClass('button-warning');
					$(OtherObj).prop({'title':'Click Again to view Details.','disabled':false});
					$(OtherObj).show();
				}else if(r.TotalErrors > 0){
					$(clickedObj).hide();
					$(OtherObj).html(r.TotalErrors+' Validation Error(s)!').addClass('button-warning');
					$(OtherObj).prop({'title':'Click Again to view Details.','disabled':false});
					$(OtherObj).show();
				}else if(r.TotalErrors == 0){
					$(clickedObj).hide();
					$(OtherObj).html('<span class="fa fa-check"></span> NO Error!').addClass('button-success').prop('disabled',false);
					setTimeout(function(){$(clickedObj).html('VALIDATE').prop({'title':'Click to validate this clinical document.','disabled':false}).removeClass('button-success');$(OtherObj).hide();$(clickedObj).show();},5000);
				}
				return;
			}else{
				alert('Data structure Error!');return;
			}
			//$('#div_validation_status').html(r);
		},
		complete:function(){
			if(!done){
				$(clickedObj).html('VALIDATE: Try Again!').prop({'title':'Request not completed successfully.','disabled':true}).removeClass('button-warning');
				$(clickedObj).show();$(OtherObj).hide();
				setTimeout(function(){$(clickedObj).html('VALIDATE').prop({'title':'Click to validate this clinical document.','disabled':false});$(OtherObj).hide();$(clickedObj).show();},5000);
			}
		},beforeSend: function(){
		     v = $(clickedObj).html();
			 l = top.URL+'/library/images/loading.gif';
			 v = '<img src="'+l+'" height="20" width="20" align="top" /> '+v;
			 $(clickedObj).html(v);
 			 $(clickedObj).prop({'title':'Processing. Please wait...','disabled':true});
	    }
	});
}

function view_ccd_validation_details(af_name,clickedObj,OtherObj){
	//top.$('#view_ccda').modal('hide');
	h = top.window.innerHeight;
	if(h){	h = h-150; 			w = top.window.innerWidth - 100;}
	else {	h=500;				w=1000;}
	if(h>1000) h=1000;			if(w>1200) w=1200;
	top.fancyModal('<iframe frameborder="0" src="cda_validator_details.php?ccda_file='+encodeURI(af_name)+'" style="width:100%; height:'+h+'px;"></iframe><div class="clearfix"></div><div class="text-center"><input type="button" value="Close" class="btn btn-danger" onclick="top.removeMessi();" /></div>','Validation Details',w+'px',h+'px');
	setTimeout(function(){$(OtherObj).html('VALIDATE').prop({'title':'Click to validate this clinical document.','disabled':false});$(OtherObj).show();$(clickedObj).hide();},1000);
}

function show_selected_prescription(o){
	v = o.value;
	$('.prescriptions_rows').addClass('hide');
	$('.npi'+v).removeClass('hide');
	if($('.npi'+v).get(0)) $('#div_print_btn').show(); else $('#div_print_btn').hide();
}

function alertShow(m)
{
	o1 = $('#div_alert_notifications');
	o2 = $('#div_alert_notifications').find('.notification_span').html(m);
	o1.fadeIn('slow');
	ACT = 2;//Auto Close Time (seconds).
	t = setInterval(function(){if(ACT>0){ACT--;}else{clearInterval(t);o1.fadeOut('slow');o2.html('');}},1000);

}

function setForwardOption(callFrom){
	if(!callFrom || typeof(callFrom) == 'undefined') callFrom = '1';

	var ptRowObj = $('#divContainer .patientForRow:first');
	var userRowObj = $('#divContainer .userForRow:first');

	$('input[name=forwardType]').each(function(id, elem){
		if($(elem).data('forward') == 'false') $(elem).prop('disabled', true);
		else $(elem).prop('disabled', false);
	});

	if($('input[name=forwardType][value="'+callFrom+'"]').prop('disabled') == true) return false;

	switch(callFrom){
		//For Patient
		case '1':
			$('input[name=forwardType][value=1]').prop('checked', true);
			if(ptRowObj.length){
				//First Chk patient Fields
				if(ptRowObj.find('a.searchButton').hasClass('hide') == true) ptRowObj.find('a.searchButton').removeClass('hide');
				if(ptRowObj.find('select[name=txt_findBy]').prop('disabled') == true) ptRowObj.find('select[name=txt_findBy]').prop('disabled', false);
				if(ptRowObj.find('input[name=txt_patient_name]').prop('disabled') == true) ptRowObj.find('input[name=txt_patient_name]').prop('disabled', false);
				if($('#pat_details_td').find('.glyphicon-remove').hasClass('hide') == true) $('#pat_details_td').find('.glyphicon-remove').removeClass('hide');
			}

			if(userRowObj.length){
				//then Chk user Fields
				if(userRowObj.find('#sent_to_groups').prop('disabled') == false) userRowObj.find('#sent_to_groups').prop('disabled', true).selectpicker('refresh');
			}
		break;

		//For User
		case '2':
			$('input[name=forwardType][value=2]').prop('checked', true);
			if(ptRowObj.length){
				//First Chk patient Fields
				if(ptRowObj.find('a.searchButton').hasClass('hide') == false) ptRowObj.find('a.searchButton').addClass('hide');
				if(ptRowObj.find('select[name=txt_findBy]').prop('disabled') == false) ptRowObj.find('select[name=txt_findBy]').prop('disabled', true);
				if(ptRowObj.find('input[name=txt_patient_name]').prop('disabled') == false) ptRowObj.find('input[name=txt_patient_name]').prop('disabled', true);
				if($('#pat_details_td').find('.glyphicon-remove').hasClass('hide') == false) $('#pat_details_td').find('.glyphicon-remove').addClass('hide');
			}

			if(userRowObj.length){
				//then Chk user Fields
				if(userRowObj.find('#sent_to_groups').prop('disabled') == true) userRowObj.find('#sent_to_groups').prop('disabled', false).selectpicker('refresh');
			}
		break;
	}
}


function load_rules_tasks(page_no, per_page, reminder_filter) {
	if (typeof (page_no) == "undefined") {
		var page_no = "";
	}
	if (typeof (per_page) == "undefined") {
		var per_page = "";
	}
	if (typeof (reminder_filter) == "undefined") {
		var reminder_filter = "";
	}

	$.ajax({
		url: 'ajax_html.php?from=console&task=rule_tasks&page_no=' + page_no + '&per_page=' + per_page +'&reminder_filter='+reminder_filter,
		type: 'POST',
		success: function (resultData)
		{
			set_result_data(resultData);

            $('#filter_rem_date').datetimepicker({
                timepicker: false,
                format: window.opener.global_date_format,
                formatDate: 'Y-m-d',
                scrollInput: false
            }).change(function() {
                var date_value='';
                if($(this).val()){ date_value=$(this).val();}
                load_rules_tasks('','',date_value);
            });
		}
	});

}

//Checks and manages the unread count for the direct messages tabs
function chkForUnreadMsg(reduceCount){
	var parentElem = $('#console_data_list').find('ul.nav-tabs:first-child');
	var sumArr = 0;

	//Reduce one count from the active tab read counter
	if(reduceCount){
		var liElem = parentElem.find('li.active');
		if(liElem.length){
			var currentCounterLi = liElem.find('.readCount:first-child');
			if(currentCounterLi.length){
				var currentCount = parseInt(currentCounterLi.text());
				var newCount = currentCount;
				if(currentCount && currentCount > 0) newCount -= 1
				if(newCount <= 0) newCount = 0;

				currentCounterLi.text(newCount);
			}
		}
	}


	if(parentElem.length){
		parentElem.find('.readCount').each(function(id, elem){
			var Ele = $(elem);
			var currentCount = parseInt(Ele.text());
			if(currentCount) sumArr += currentCount;
		});
	}

	var sideBarElem = $('.phylft #direct_messages');
	if(sideBarElem.length){
		if(sumArr > 0){
			if(sideBarElem.hasClass('unread') == false) sideBarElem.addClass('unread');
		}else{
			if(sideBarElem.hasClass('unread') == true) sideBarElem.removeClass('unread');
		}
	}
}

//Print patient messages
//IM-3576:- Need to print out patient communications that come into IMW.
function print_pt_messages_div(divName) {

    if(typeof($('#'+divName))!='undefined' && $('#'+divName).length>0 && $('#'+divName).html()!='' && $('#'+divName).html()!='undefined'){
        var msg_id= $('#'+divName).data('msg_id');
        var patientId= $('#'+divName).data('patientid');

        var location= $('#pt_msg_location').val();
        top.html_to_pdf(location,'p');
    } else {
        top.fAlert('Please select message to print.');
        return false;
    }
}


function reconcile_operation(row_id, ths, tbl, req_id)
{

	req_id = req_id || '';
	ths_parent = $(ths).parent();
	if (row_id != "" && parseInt(row_id))
	{
		//ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');
	}
	//"&tbl="+tbl+
	window.open("reconcile_portal_patients.php?sel_op=reconcile&row_id="+row_id+"&req_id="+req_id,"mywindow","width=1100,height=600,scrollbars=yes");
}


function cancel_reconcile(row_id,loc) {
	if (!row_id)
		return false;

	loc = loc || '';

	$.ajax({
		url: 'reconcile_portal_patients.php',
		data: 'action=cancel_reconcile&row_id=' + row_id,
		type: 'POST',
		dataType: 'JSON',
		success: function (resultData)
		{
			if (resultData) {
				do_action("load_patient_messages", "pt_changes_approval");
			}
		}
	});
}


function loadPortalReconciledPt(obj,row_id,tbl) {
	if (!row_id)
		return false;

	$.ajax({
		url: 'reconcile_portal_patients.php',
		data: 'action=get_details&row_id=' + row_id,
		type: 'POST',
		dataType: 'JSON',
		success: function (resultData)
		{
			$('#ptmessageData').html(resultData);
		},
		complete: function ()
		{
			$('.messageList>li').removeClass('activeMsg');
			$(obj).addClass('activeMsg');
			$(obj).removeClass('unredBold');
			reloadFunction();
		}
	});

}

function load_mdl_upld(sec){
		$("#div_loading_image").show();
		var secnm = typeof(sec)!="undefined" && sec=="1" ? "pt_msg" : "drct_msg";

		var u = URL + "/interface/physician_console/ajax_html.php?from=console&task=load_direct_messages&get_upld_mdl=1&sec="+secnm;
		$.get(u, function(d){
				if(d){
					$("#div_loading_image").hide();
					$("body").append(d);
					if($("#upldModal").length>0){
						$("#upldModal").find("button.start").hide();
						$("#upldModal").modal(); //{show: true}
					}
				}
		});
}

function download_ccda(path){
	window.location.href = 'download_file.php?file_name='+up_dir_path+'/users'+path;
}

function show_list_pt_docs(flg){
		var pt_id = $.trim($("#divContainer #patientId").val());
		var pt_nm = $.trim($("#divContainer #txt_patient_name").val());
		if(pt_id==""||pt_nm==""){ alert("Please select Patient!"); return ; }
		$("#div_loading_image").show();
		var url = URL + "/interface/physician_console/ajax_html.php?from=console&task=load_patient_messages&filter=load_pt_msg_inbox&get_mdl_list_pt_docs=1&patientId="+pt_id+"&flg="+flg;
		$.get(url, function(d){
			if(d){
				$("#div_loading_image").hide();
				$("body").append(d);
				if($("#attchPtDocModal").length>0){
					$("#attchPtDocModal #dvptattch").css({"max-height":"400px", "overflow":"auto"});
					$("#attchPtDocModal").modal(); //{show: true}
				}
			}
		});
}

function load_mdl_atch(flg){

	if(flg==2 || flg==3){
	var pt_id = $.trim($("#divContainer #patientId").val());
	var pt_nm = $.trim($("#divContainer #txt_patient_name").val());
	if(pt_id==""||pt_nm==""){ alert("Please select Patient!"); return ; }
	}

	//check and load
	if($("#attchPtDocModal").length>0 && $("#attchPtDocModal :checked").length>0){
		$("#attchPtDocModal").modal({show: true});
		return ;
	}else if($("#upldModal").length>0 && $("#upldModal .files .template-upload").length>0){
		$("#upldModal").modal({show: true});
		return ;
	}

	$("#upldModal, #attchPtDocModal").hide().data( 'bs.modal', null ).remove();
	if(flg==1){ load_mdl_upld(1); }
	else if(flg==2 || flg==3){ show_list_pt_docs(flg); }
}

var ar_attch_pt_files;
function attch_pt_files(){
	ar_attch_pt_files=[];
	var pt_id = $.trim($("#divContainer #patientId").val());
	var pt_nm = $.trim($("#divContainer #txt_patient_name").val());
	if(pt_id==""||pt_nm==""){ alert("Please select Patient!"); return ; }

	var frm_data = "", flg="";
	if($('#frm_pt_docs_atch').length>0 && $('#frm_pt_docs_atch :checked').length>0){
		var atc_data = $('#frm_pt_docs_atch').serialize();
		frm_data += (typeof(atc_data) != "undefined" && atc_data!="") ? "&"+atc_data : "";
	}else{
		alert("Please select patient doc(s).");
		return;
	}

	var prms = "from=console&task=load_patient_messages&filter=load_pt_msg_inbox&attch_pt_files=1&patientId="+pt_id+"&flg="+flg;
	prms += frm_data;
	$("#div_loading_image").show();
	var url = URL + "/interface/physician_console/ajax_html.php";
	$.post(url, prms, function(d){
		if(d){
			$("#div_loading_image").hide();
			if(typeof(d.error)!="undefined" && d.error!=""){
				alert(d.error);
			}else{
				ar_attch_pt_files = d;
				$("#attchPtDocModal .close").trigger("click");
			}
		}
	}, "json");
}
