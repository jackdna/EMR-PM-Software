// imedicmonitornew JavaScript Document
function initDisplay(arDim, page) {
	var h = arDim['h'];
	var w = arDim['w'];
	$('#page_bottom_bar').css({'top': (h - 55), 'width': w - 10, 'display': 'block'});/*SETTING POSITION AND WIDTH OF FOOTER*/
	if (page == 'main') {
		ldivH = Math.round((h - fixObjH) / 3, 0);
		rdivH1 = Math.round((ldivH * 2), 0);
		rdivH2 = Math.round((h - fixObjH) - rdivH1, 0);
		$(arrLeftDivs).each(function () {
			$('#' + this).css({'height': ldivH - 6});
		});
		$('#box_waitingpt').css({'height': rdivH1 - 5, 'float': 'right'});
		$('#box_wr').css({'height': rdivH2 - 5});
		$(allObj).each(function (i) {
			$('#' + allObj[i]).show().css({'width': '49.6%'});
		});
		TableScroller();
	} else {
		$('#btn_close').css({'position': 'absolute', 'left': ((w / 2) - 4) - ($('#btn_close').width() / 2)});
	}
	bigbox_height = rdivH1 - 5;
}

function initDisplayRoomView(arDim, page) {
	var h = arDim['h'];
	var w = arDim['w'];
	$('#page_bottom_bar').css({'top': (h - 55), 'width': w - 10, 'display': 'block'});/*SETTING POSITION AND WIDTH OF FOOTER*/
	if (page == 'main') {
		ldivH = Math.round((h - fixObjH) / 3, 0);
		rdivH1 = Math.round((ldivH * 2), 0);
		rdivH2 = Math.round((h - fixObjH) - rdivH1, 0);
		$(arrLeftDivs).each(function () {
			$('#' + this).css({'height': ldivH - 10});
		});
		$('#box_waitingpt').css({'height': rdivH1 - 5});
		$('#box_waitingpt_left').css({'height': rdivH1 - 5});
		$('#box_wr').css({'height': rdivH2 - 10});
		$(allObj).each(function (i) {
			if (allObj[i] == 'box_waitingpt') {
				$('#' + allObj[i]).show().css({'width': '32%'});
				$('.div_group_container').height($('#box_waitingpt').height() - 20);
			} else if (allObj[i] == 'box_waitingpt_left') {
				$('#' + allObj[i]).show().css({'width': '67%'});
			} else {
				$('#' + allObj[i]).show().css({'width': '49.6%'});
			}
		});
		//alert($('#box_waitingpt_left').css('height'));
		TableScroller();
	} else {
		$('#btn_close').css({'position': 'absolute', 'left': ((w / 2) - 4) - ($('#btn_close').width() / 2)});
	}
	bigbox_height = rdivH1 - 5;
}
function innerDim() {
	var arDim = new Array();
	arDim['h'] = $(window).innerHeight() - 4;
	arDim['w'] = $(window).innerWidth();
	return arDim;
}
function BigMe(o, from) {
	if (typeof (from) == 'undefined')
		from = false;
	arDim = innerDim();
	if (!boxBigged) {
		/*****MAKING ALL BOXES HIDDEN******/
		currObj = $(o).parent().parent('div').attr('id');
		$(allObj).each(function (i) {
			if (allObj[i] != currObj) {
				if (currObj != 'box_wr' && currObj != 'box_schp')
					$('#' + allObj[i]).not('#box_wr, #box_schp').hide();
				else
					$('#' + allObj[i]).hide();
			}
		});

		/*****MAKING CURRENT CLICKED BOX BIGGER AND CHANGING ICON CLASS*******/
		if (currObj != 'box_wr' && currObj != 'box_schp')
			$('#' + currObj).css({'width': (arDim['w'] - 12), 'height': bigbox_height});//,'height':((arDim['h']-10)-fixObjH)
		else
			$('#' + currObj).css({'width': (arDim['w'] - 12), 'height': ((arDim['h'] - 10) - fixObjH)});

		/*****SWITCHING CLASS OF ICON******/
		$('#' + currObj).children('.section_header').children('span').removeClass('icon20_expand').addClass('icon20_collapse');

		/*****ARRANGING ROOM GROUP WIDTH (ROOM VIEW SPECIFIC)*****/
		if (from == 'roomview') {
			//$('.div_group_container').height($('#box_waitingpt').height());
			div_grp_length = $('.div_group').length;
			div_grp_width = '99%';
			if (div_grp_length == 2)
				div_grp_width = '48.5%';
			else if (div_grp_length == 3)
				div_grp_width = '31.8%';
			else if (div_grp_length > 3)
				div_grp_width = '24.2%';
			$('.div_group').css({'width': div_grp_width});
		}
		boxBigged = true;
		TableScroller($('#' + currObj));
	} else {
		boxBigged = false;
		if (from == 'roomview') {
			initDisplayRoomView(arDim, 'main');
			div_grp_length = $('.div_group').length;
			div_grp_width = '99%';
			if (div_grp_length == 2)
				div_grp_width = '48.5%';
			else if (div_grp_length >= 3)
				div_grp_width = '32.4%';
			$('.div_group').css({'width': div_grp_width});
		} else
			initDisplay(arDim, 'main');
		$(o).removeClass('icon20_collapse').addClass('icon20_expand');
	}
}

function TableScroller(o) {
	if (o) {
		//$('.tableCon').height(o.height()-21);
	} else {
		$('.tableCon').each(function () {
			o = $(this).parent('div');
			$(this).height(o.height() - 21);
		});
	}
}

function show_clock() {
	var C = new Date();
	var h = C.getHours();
	var m = C.getMinutes();
	var s = C.getSeconds();
	var dn = "PM";
	if (h < 12)
		dn = "AM";
	if (h > 12)
		h = h - 12;
	if (h == 0)
		h = 12;
	if (m <= 9)
		m = "0" + m;
	if (s <= 9)
		s = "0" + s;
	var tm = h + ":" + m + ":" + s + " " + dn;
	$("#div_now").text(tm);
	setTimeout("show_clock()", 1000);
}
String.prototype.trim = function () {
	return this.replace(/^\s+|\s+$/g, "");
}

function stristr(haystack, needle, bool) {
	var pos = 0;
	haystack += '';
	pos = haystack.toLowerCase().indexOf((needle + '').toLowerCase());
	if (pos == -1) {
		return false;
	} else {
		if (bool) {
			return haystack.substr(0, pos);
		} else {
			return true; //haystack.slice(pos);
		}
	}
}

function fancyModal(html, title, w, h, closeBtn, po, ModalMode) {//positionObject=po;
	if (typeof (title) != "string")
		title = null;
	if (typeof (w) != "string")
		w = '';
	if (typeof (h) != "string")
		h = '';
	if (typeof (closeBtn) == "undefined")
		closeBtn = true;
	if (typeof (po) != 'undefined') {
		of = $(po).offset();
	} else
		po = false;
	if (typeof (ModalMode) != 'boolean')
		ModalMode = true;
	if (po) {
		new top.Messi(html, {'title': title, modal: ModalMode, 'width': w, 'height': h, 'closeButton': closeBtn, center: false, viewport: {top: of.top + 25, left: of.left + 25}});
	} else {
		new top.Messi(html, {'title': title, modal: ModalMode, 'width': w, 'height': h, 'closeButton': closeBtn});
	}
}
function removeMessi() {
	window.top.$('.messi-modal,.messi').remove();
}

function showPAG(pt_id) {
	hw = innerDim();
	if (parseInt(hw['w']) >= 1200)
		wid = 1150;
	else
		wid = parseInt(hw['w']) - 50;
	if (parseInt(hw['h']) >= 700)
		hei = 650;
	else
		hei = parseInt(hw['h']) - 50;

	top.fancyModal('<iframe name="messimodal" id="messimodal" src="../../interface/chart_notes/past_diag/chart_patient_diagnosis.php?p_id=' + pt_id + '&cameFrom=imedicmonitor" frameborder=0 style="width:' + wid + 'px; height:' + hei + 'px;"></iframe>');
}

function switchView(v) {
	if (v == 'room')
		window.location.href = 'main_rooms.php?dd_fac_id=' + document.getElementById('sel_facility').value + '&dd_prov_id=' + $("#active_pro").val() + '&view_mode=' + $("#view_mode").val();
	else
		window.location.href = 'main.php?dd_fac_id=' + document.getElementById('sel_facility').value + '&dd_prov_id=' + $("#hidd_sel_provider").val() + '&view_mode=' + $("#view_mode").val();
}

function room_priority_change(task, sch_id, room) {
	if (typeof (room) == 'undefined')
		room = '';
	//alert(task+'::'+sch_id+' :: '+room);

	url = "change_pt_room_priority.php?sch_id=" + sch_id + "&task=" + escape(task) + "&room=" + escape(room);
	$.ajax({type: "GET", url: url, success: function (r) {//alert(url+"\n\n"+r);

			if (typeof (page_loader2) != 'undefined') {
				clearInterval(page_loader2);
				delete page_loader2;
			}
			getSchData(true);
		}
	});

}

function set_refresh_mode(m) {
	if (typeof (m) == 'undefined')
		m = $('#refresh_mode').val();
	if (m == 'toogle') {
		if (m == 'manual')
			m = 'auto';
		else if (m == 'auto')
			m = 'manual';
	}
	o = $('#btn_refresh_mode');
	if ((typeof (m) != 'undefined' && m.toLowerCase() == 'manual') || o.hasClass('active')) {
		if (o.hasClass('active'))
			o.removeClass('active');
		$('#refresh_mode').val('manual');
		o.val('Manual');
	} else {
		if (!o.hasClass('active'))
			o.addClass('active');
		$('#refresh_mode').val('auto');
		o.val('Auto');
		getSchData();
	}
}

function set_view_mode(m) {
	if (typeof (m) == 'undefined')
		m = $('#view_mode').val();
	//if(m=='toogle'){
	if (m == 'public')
		m = 'private';
	else if (m == 'private')
		m = 'public';
	//}
	o = $('#btn_view_mode');
	if ((typeof (m) != 'undefined' && m.toLowerCase() == 'public') || o.hasClass('active')) {
		if (o.hasClass('active'))
			o.removeClass('active');
		$('#view_mode').val('public');
		o.val('Public');
	} else {
		if (!o.hasClass('active'))
			o.addClass('active');
		$('#view_mode').val('private');
		o.val('Private');
		getSchData();
	}
}


//Code from script.js file

/*  GETTING SCHEDULER DATA  */
function getSchData(forceRefresh1) {
	if (typeof (page_loader2) == 'number')
		clearInterval(page_loader2);
	if (typeof (forceRefresh1) == 'undefined')
		forceRefresh1 = false;
	//console.log('Flow Status: '+monitor_flow_status+' :: Pageloader='+typeof(page_loader2)+' :: forceRefresh='+forceRefresh1);
	monitor_flow_status = 'moving';
	var fac = $("#sel_facility").val();
	if (baseName == 'main_rooms.php') {
		var prov = $("#hidd_sel_provider").val();
	} else {
		var prov = $("#active_pro").val();
	}
	var view_mode = $('#view_mode').val();
	var timezone = jstz.determine();
	local_tz = timezone.name();
	if (fac != "") {
		url_var = "appts.php?prov=" + prov + "&local_tz=" + encodeURI(local_tz);
		$.ajax({
			type: "POST",
			url: url_var,
			dataType: "xml",
			success: function (xml) {
				//alert(xml);
				var sno = 1;
				var snochin = 1;
				var snotech = 1;
				var snophy = 1;
				var snowaitp = 1;
				var str_chk_in = "";
				var prev_prov = "0";
				var waiting_ready_for_doc_pts_val = '';
				var ready_for_doc_pts_val = '';
				var scheduled_pts_val = '';
				var checked_in_pts_val = '';
				var waiting_tech_active_list_val = '';
				var tech_active_list_val = '';
				var waiting_patients_val = '';
				var room_wise_pts_arr = new Array();
				var room_wise_priority = new Array();
				var room_wise_procedure = new Array();
				/*
				 $("#ready_for_doc_pts tbody").html("");
				 $("#scheduled_pts tbody").html("");
				 $("#checked_in_pts tbody").html("");
				 $("#tech_active_list tbody").html("");
				 $("#waiting_patients tbody").html("");
				 */
				if (typeof (allTimer) == 'number') {
					clearInterval(allTimer);
				}
				$(xml).find("pt").each(function () {
					sch_id = $(this).find("id").text();
					appt_status = $(this).find("st").text();
					pt_id = $(this).find("pid").text();
					pt_name = $(this).find("name").text();
					if (view_mode != 'private' && pt_name != '') {
						tmp_pt_name_arr = pt_name.split(' ');
						lname = tmp_pt_name_arr['0'];
						if (typeof (tmp_pt_name_arr['2']) != 'undefined')
							fname = tmp_pt_name_arr['2'];
						else if (typeof (tmp_pt_name_arr['1']) != 'undefined')
							fname = tmp_pt_name_arr['1'];
						else
							fname = '';
						pt_name = lname.substr(0, 1) + '. ' + fname;
						$('.private').hide();
					} else {
						$('.private').show();
					}
					doc_id = $(this).find("doc").text();
					doc_name = $(this).find("docnm").text();
					sch_oprid = $(this).find("opr").text();
					sch_oprname = $(this).find("oprnm").text();
					sch_fac = $(this).find("fac").text();
					pt_priority = $(this).find("pt_priority").text();
					sch_msg = $(this).find("msg").text();
					sch_st_time = $(this).find("tm").text();
					sch_proc = $(this).find("proc").text();
					sch_ci_time = $(this).find("ci").text();
					sch_ci_time1 = $(this).find("ci_picktime").text();
					sch_co_time = $(this).find("co").text();
					doc_msg = $(this).find("doctor_mess").text();
					tech_click = $(this).find("tech_click").text();
					chart_opened = $(this).find("chart_opened").text();
					ready4DrId = $(this).find("ready4DrId").text();
					moved2Tech = $(this).find("moved2Tech").text();
					Sent2DrBy = $(this).find("opidSent2Dr").text();
					Sent2TechBy = $(this).find("opidSent2Dr").text();
					sent2SC = $(this).find("sent2SC").text();
					pt_with = $(this).find("pt_with").text();
					//if operator type == Resident or Fellow then set it equivalent to Technician.
					if (pt_with == '11' || pt_with == '19' || pt_with == '13') {
						pt_with = 3;
					}
					room_no = $(this).find("room_no").text();
					room = $(this).find("room").text();
					roomop = $(this).find("roomop").text();
					roomopnm = $(this).find("roomopnm").text();
					roomoptype = $(this).find("roomoptype").text();
					roomopcolor = $(this).find("roomopcolor").text();
					if (roomopcolor.length == 7)
						roomopcolor = roomopcolor.substr(1, 6);
					waitStartFrm = $(this).find("locCurrTime").text();
					waitStartFrm1 = $(this).find("locCurrTime1").text();
					waitingPt = $(this).find("waitingPt").text();
					waitingIcon = $(this).find("waitingPtIcon").text();
					waitingLong = $(this).find("waiting_4long").text();
					dilated_time = $(this).find("dilated_time").text();
					ready_for_status_text = $(this).find("ready_for_status_text").text();
					ready_for_status_color = $(this).find("ready_for_status_color").text();
					ready_for_status_text_color = $(this).find("ready_for_status_text_color").text();

					//coloring
					var color_class = "";
					var ReadyForClass = "";
					if (chart_opened == "yes" && tech_click > 0) {
						//	color_class = "green_bg";
					}
					if (waitingLong == "1" && ready4DrId == "0") {
						//	color_class = "red_bg";
					}
					if (ready_for_status_text != '') {
						ready_for_status_text = '<div class="ready4box" style="background-color:#' + ready_for_status_color + '; color:#' + ready_for_status_text_color + '">' + ready_for_status_text + '</div>';
					}


					if (prov != '' && prov != '0' && prov == doc_id && fac == sch_fac && baseName != 'main_rooms.php') {
						if (sch_ci_time == 'N/A') {
							sch_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
							scheduled_pts_val += '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + sno + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span></td><td>' + sch_st_time + '</td><td>';
							if (appt_status == '3') {
								scheduled_pts_val += '<font color="#ff0000"><b title="No Show"><i>NS</i></b></font> ';
							}
							scheduled_pts_val += pt_name + ' - ' + pt_id + '</td><td class="private">' + sch_proc + '</td><td>' + doc_name + '</td><td class="message_div private">' + sch_msg_text + '&nbsp;</td></tr>';
							sno++;
						}
						if (appt_status == '13' && pt_with != '6') {//patient is checked-in.
							if ((room == "" || roomoptype == "") && waitingPt != "Y" && sch_ci_time != "N/A" && (pt_with == '' || pt_with == '0')) {
								checked_in_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
								checked_in_pts_val += '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + snochin + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span><span class="pickTime hide">' + sch_ci_time1 + '</span></td><td class="">' + sch_ci_time + '<br>(' + sch_st_time + ')' + '</td><td class="pt_nameid_td" id="nametd' + sch_id + '">' + pt_name + ' - ' + pt_id + '</td><td class="private">' + sch_proc + '</td><td>' + doc_name + '</td><td class="message_div private">' + checked_in_msg_text + '&nbsp;</td><td class="showTime private">&nbsp;</td></tr>';
								snochin++;
								str_chk_in += pt_name + ' - ' + pt_id + "; ";
							} else if ((waitingPt != "Y" && room != "" && (roomoptype == "Physician" || roomoptype == "Test") && pt_with != '2' && pt_with != '3' && pt_with != '4' && pt_with != '5') || pt_with == '1') {
								if (pt_with == '1') {
									ReadyForClass = ' icon16_readyforP';
								} else {
									ReadyForClass = '';
								}
								if (pt_with == '6') {
									DoneMarked = '<span class="icon20 icon20_done"></span>';
								} else {
									DoneMarked = '';
								}
								ready4doc_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
								str_doc_pt_val = '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + snophy + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span><span class="pickTime hide">' + sch_ci_time1 + '</span></td><td class="' + ReadyForClass + '">' + sch_ci_time + '<br>(' + sch_st_time + ')' + '</td><td class="pt_nameid_td" id="nametd' + sch_id + '">' + pt_name + ' - ' + pt_id + DoneMarked + '<span class="span_priority">' + pt_priority + '</span></td><td class="private">' + sch_proc + '</td><td>' + room + ready_for_status_text + '&nbsp;</td><td>' + doc_name + '</td><td>' + roomopnm + '</td><td class="message_div private">' + ready4doc_msg_text + '&nbsp;</td><td class="showTime private"></td></tr>';
								if (pt_with == '1') {
									waiting_ready_for_doc_pts_val += str_doc_pt_val;
								} else {
									ready_for_doc_pts_val += str_doc_pt_val;
								}
								str_chk_in += pt_name + ' - ' + pt_id + "; ";
								snophy++;
							} else if ((waitingPt != "Y" && room != "" && roomoptype == "Technician" && pt_with != '3' && pt_with != '4' && pt_with != '5') || pt_with == '2') {
								if (pt_with == '2') {
									ReadyForClass = ' icon16_readyforT';
								} else {
									ReadyForClass = '';
								}
								if (pt_with == '6') {
									DoneMarked = '<span class="icon20 icon20_done"></span>';
								} else {
									DoneMarked = '';
								}
								techactive_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
								str_tech_pt_val = '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + snotech + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span><span class="pickTime hide">' + sch_ci_time1 + '</span></td><td class="' + ReadyForClass + '">' + sch_ci_time + '<br>(' + sch_st_time + ')' + '</td><td class="pt_nameid_td" id="nametd' + sch_id + '">' + pt_name + ' - ' + pt_id + DoneMarked + '<span class="span_priority">' + pt_priority + '</span></td><td class="private">' + sch_proc + '</td><td>' + room + ready_for_status_text + '&nbsp;</td><td>' + doc_name + '</td><td>' + roomopnm + '</td><td class="message_div private">' + techactive_msg_text + '&nbsp;</td><td class="showTime private">&nbsp;</td></tr>';
								if (pt_with == '2') {
									waiting_tech_active_list_val += str_tech_pt_val;
								} else {
									tech_active_list_val += str_tech_pt_val;
								}
								str_chk_in += pt_name + ' - ' + pt_id + "; ";
								snotech++;
							} else if (waitingPt == "Y" || pt_with == '4' || pt_with == '3') {
								if (waitingIcon == "icon16_dilation") {
									dilationTime = '<span style="display:none" class="pickDTime">' + dilated_time + '</span>';
									dilationElapsed = '<span class="greenText"></span>';
								} else {
									dilationTime = '<span style="display:none" class="pickDTime"></span>';
									dilationElapsed = '';
								}
								if (pt_with == '3') {
									ReadyForClass = ' icon16_readyfor';
								} else {
									ReadyForClass = '';
								}
								waitingpt_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
								waiting_patients_val += '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + snowaitp + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span><span class="pickTime hide">' + sch_ci_time1 + '</span></td><td>' + dilationTime + ready_for_status_text + '<span class="icon16 ' + waitingIcon + '"></span><span class="showDTime">' + dilationElapsed + '</span></td><td>' + sch_st_time + '</td><td class="' + ReadyForClass + '">' + sch_ci_time + '</td><td class="pt_nameid_td" id="nametd' + sch_id + '">' + pt_name + ' - ' + pt_id + '<span class="span_priority">' + pt_priority + '</span></td><td class="private">' + sch_proc + '</td><td>' + doc_name + '&nbsp;</td><td>' + roomopnm + '</td><td class="message_div private">' + waitingpt_msg_text + '&nbsp;</td><td class="showTime private">&nbsp;</td></tr>';
								str_chk_in += pt_name + ' - ' + pt_id + "; ";
								snowaitp++;
							}
						}
					} else if ((prov == '' || baseName == 'main_rooms.php') && fac != '' && fac != '0' && fac == sch_fac) {
						if (sch_ci_time == 'N/A') {
							if ((prov != '' && prov != '0' && prov == doc_id && baseName == 'main_rooms.php') || ((prov == '' || prov == '0') && (baseName == 'main_rooms.php' || baseName == 'main.php'))) {
								sch_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
								scheduled_pts_val += '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + sno + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span></td><td>' + sch_st_time + '</td><td>';
								if (appt_status == '3') {
									scheduled_pts_val += '<font color="#ff0000"><b title="No Show"><i>NS</i></b></font> ';
								}
								scheduled_pts_val += pt_name + ' - ' + pt_id + '</td><td class="private">' + sch_proc + '</td><td>' + doc_name + '</td><td class="message_div private">' + sch_msg_text + '&nbsp;</td></tr>';
								sno++;
							}
						}
						if (appt_status == '13' && pt_with != '6') {//patient is checked-in.
							if ((room == "" || (roomoptype == "" && baseName != 'main_rooms.php') || (room == "N/A" && baseName == 'main_rooms.php')) && waitingPt != "Y" && sch_ci_time != "N/A" && (pt_with == '' || pt_with == '0')) {
								if ((prov != '' && prov != '0' && prov == doc_id && baseName == 'main_rooms.php') || ((prov == '' || prov == '0') && (baseName == 'main_rooms.php' || baseName == 'main.php'))) {
									if (pt_priority == '' || pt_priority == '0') {
										span_priority_html = '';
									} else {
										span_priority_html = '<span class="span_priority">' + pt_priority + '</span>';
									}
									checked_in_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
									checked_in_pts_val += '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + snochin + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span></span><span class="pickTime hide">' + sch_ci_time1 + '</span></td><td>' + sch_ci_time + '<br>(' + sch_st_time + ')' + '</td><td class="pt_nameid_td" id="nametd' + sch_id + '">' + pt_name + ' - ' + pt_id + span_priority_html + '</td><td class="private">' + sch_proc + '</td><td>' + doc_name + '</td><td class="message_div private">' + checked_in_msg_text + '&nbsp;</td><td class="showTime private">&nbsp;</td></tr>';
									snochin++;
									str_chk_in += pt_name + ' - ' + pt_id + "; ";
								}
							} else if (((waitingPt != "Y" && room != "" && (roomoptype == "Physician" || roomoptype == "Test") && pt_with != '2' && pt_with != '3' && pt_with != '4' && pt_with != '5') || pt_with == '1') && baseName != 'main_rooms.php') {
								if (pt_with == '1') {
									ReadyForClass = ' icon16_readyforP';
								} else {
									ReadyForClass = '';
								}
								if (pt_with == '6') {
									DoneMarked = '<span class="icon20 icon20_done"></span>';
								} else {
									DoneMarked = '';
								}

								ready4doc_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
								str_doc_pt_val = '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + snophy + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span><span class="pickTime hide">' + sch_ci_time1 + '</span></td><td class="' + ReadyForClass + '">' + sch_ci_time + '<br>(' + sch_st_time + ')' + '</td><td class="pt_nameid_td" id="nametd' + sch_id + '">' + pt_name + ' - ' + pt_id + DoneMarked + '<span class="span_priority">' + pt_priority + '</span></td><td class="private">' + sch_proc + '</td><td>' + room + '<br>' + ready_for_status_text + '</td><td>' + doc_name + '</td><td>' + roomopnm + '</td><td class="message_div private">' + ready4doc_msg_text + '&nbsp;</td><td class="showTime private"></td></tr>';
								if (pt_with == '1') {
									waiting_ready_for_doc_pts_val += str_doc_pt_val;
								} else {
									ready_for_doc_pts_val += str_doc_pt_val;
								}
								str_chk_in += pt_name + ' - ' + pt_id + "; ";
								snophy++;
							} else if (((waitingPt != "Y" && room != "" && roomoptype == "Technician" && pt_with != '3' && pt_with != '4' && pt_with != '5') || pt_with == '2') && baseName != 'main_rooms.php') {
								if (pt_with == '2') {
									ReadyForClass = ' icon16_readyforT';
								} else {
									ReadyForClass = '';
								}
								if (pt_with == '6') {
									DoneMarked = '<span class="icon20 icon20_done"></span>';
								} else {
									DoneMarked = '';
								}

								techactive_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
								str_tech_pt_val = '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + snotech + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span><span class="pickTime hide">' + sch_ci_time1 + '</span></td><td class="' + ReadyForClass + '">' + sch_ci_time + '<br>(' + sch_st_time + ')' + '</td><td class="pt_nameid_td" id="nametd' + sch_id + '">' + pt_name + ' - ' + pt_id + DoneMarked + '<span class="span_priority">' + pt_priority + '</span></td><td class="private">' + sch_proc + '</td><td>' + room + '<br>' + ready_for_status_text + '</td><td>' + doc_name + '</td><td>' + roomopnm + '</td><td class="message_div private">' + techactive_msg_text + '&nbsp;</td><td class="showTime private">&nbsp;</td></tr>';
								if (pt_with == '2') {
									waiting_tech_active_list_val += str_tech_pt_val;
								} else {
									tech_active_list_val += str_tech_pt_val;
								}
								str_chk_in += pt_name + ' - ' + pt_id + "; ";
								snotech++;
							} else if ((waitingPt == "Y" || pt_with == '4' || pt_with == '3')) {
								if (waitingIcon == "icon16_dilation") {
									dilationTime = '<span style="display:none" class="pickDTime">' + dilated_time + '</span>';
									dilationElapsed = '<span class="greenText"></span>';
								} else {
									dilationTime = '<span style="display:none" class="pickDTime"></span>';
									dilationElapsed = '';
								}
								if (pt_with == '3') {
									ReadyForClass = ' icon16_readyfor';
								} else {
									ReadyForClass = '';
								}
								waitingpt_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;

								if (baseName != 'main_rooms.php') {
									waiting_patients_val += '<tr sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td>' + snowaitp + '<span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span><span class="pickTime hide">' + sch_ci_time1 + '</span></td><td>' + dilationTime + ready_for_status_text + '<span class="icon16 ' + waitingIcon + '"></span><span class="showDTime">' + dilationElapsed + '</span></td><td>' + sch_st_time + '</td><td class="' + ReadyForClass + '">' + sch_ci_time + '</td><td class="pt_nameid_td" id="nametd' + sch_id + '">' + pt_name + ' - ' + pt_id + '<span class="span_priority">' + pt_priority + '</span></td><td class="private">' + sch_proc + '</td><td>' + doc_name + '&nbsp;</td><td>' + roomopnm + '</td><td class="message_div private">' + waitingpt_msg_text + '&nbsp;</td><td class="showTime private">&nbsp;</td></tr>';
								} else if (baseName == 'main_rooms.php') {
									wr_appt_data_html = '';
									wr_appt_data_html += '<div class="ApptDetails section hide"><div class="section_header"><img src="../../library/images/icon_close.png" style="width:15px;" class="fr">Appointment Details</div><table>';

									wr_appt_data_html += '<tr><td style="width:130px;">Appt. Time</td><td style="width:auto;">' + sch_st_time + '</td></tr>';
									wr_appt_data_html += '<tr><td style="width:130px;">Check-in Time</td><td style="width:auto;">' + sch_ci_time + '</td></tr>';
									wr_appt_data_html += '<tr><td>Technician</td><td>' + roomopnm + '</td></tr>';
									wr_appt_data_html += '<tr><td>Pt. At a Glance</td><td><span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span></td></tr>';
									if (waitingpt_msg_text != '') {
										wr_appt_data_html += '<tr><td vAlign="top">Appt. Comments</td><td>' + waitingpt_msg_text + '</td></tr>';
									}
									wr_appt_data_html += '</table></div>';

									waiting_patients_val += '<tr sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td class="pt_nameid_td" id="nametd' + sch_id + '"><img src="../../library/images/icon_message.png" class="fr msg_icon">' + pt_name + ' - ' + pt_id + ready_for_status_text + '<span class="span_priority">' + pt_priority + '</span></td><td class="private"><span class="pickTime hide">' + sch_ci_time1 + '</span>' + sch_proc + wr_appt_data_html + '</td><td class="showTime private">&nbsp;</td></tr>';
								}
								str_chk_in += pt_name + ' - ' + pt_id + "; ";
								snowaitp++;
							} else if (baseName == 'main_rooms.php' && room_no != '' && room_no != '0') {
								if (waitingIcon == "icon16_dilation") {
									dilationTime = '<span style="display:none" class="pickDTime">' + dilated_time + '</span>';
									dilationElapsed = '<span class="greenText"></span>';
								} else {
									dilationTime = '<span style="display:none" class="pickDTime"></span>';
									dilationElapsed = '';
								}
								waitingpt_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;
								pt_status_icon = '';
								ReadyForClass = '';
								DoneMarked = '';

								if (pt_with == '6') {
									pt_status_icon = '<span class="icon20 icon20_done"></span>';
								} else if (pt_with == '1') {
									pt_status_icon = '<span class="icon16 icon16_readyforP"></span>';
								} else if (pt_with == '2') {
									pt_status_icon = '<span class="icon16 icon16_readyforT"></span>';
								} else if (pt_with == '3') {
									pt_status_icon = '<span class="icon16 icon16_readyfor"></span>';
								} else if (waitingPt == "Y" && waitingIcon == "icon16_dilation") {
									pt_status_icon = '<span class="icon16 ' + waitingIcon + '"></span>';
								}

								//If Patient Name is lengthy than cell width.
								ptNameVal = pt_name + ' - ' + pt_id;
								ptNameCellWidth = $('.pt_name_td').width();
								if ((ptNameVal.length * 9) > ptNameCellWidth) { //removing patient ID
									ptNameVal = pt_name;
								}
								if ((ptNameVal.length * 9) > ptNameCellWidth) { //trimming Patient Name if still its lengthy.
									RequiredPtNameLen = Math.ceil(ptNameCellWidth / 9) - 3
									ptNameVal = pt_name.substr(0, RequiredPtNameLen) + '...';
								}
								ready4doc_msg_text = (doc_msg == '' || doc_msg == 'N/A') ? sch_msg : doc_msg;

								//CHECK HERE IF FOR A ROOM this is first patient or second.
								room_appt_move_up = '';
								if (typeof (room_wise_pts_arr[room_no]) == 'undefined' || room_wise_pts_arr[room_no] == '') {
									//this room is currently empty; this is the first record
									if (pt_priority == '0')
										room_wise_priority[room_no] = pt_priority;
									else if (pt_priority != '0')
										room_wise_priority[room_no] = pt_priority;

									if (sch_proc.length > 12) {
										span_room_proc_val = '<span title="' + sch_proc + '">' + sch_proc.substr(0, 12) + '..</span>';
									} else {
										span_room_proc_val = '<span title="' + sch_proc + '">' + sch_proc + '</span>';
									}
									room_wise_procedure[room_no] = span_room_proc_val;
								} else if (typeof (room_wise_pts_arr[room_no]) != 'undefined' && room_wise_pts_arr[room_no] != '') {
									//this is the second or third record for this room
									room_appt_move_up = '<img src="../css/images/icon_moveup.png" class="fr moveup_icon" style="margin-right:5px;" room="' + room + '" title="Move Up">';
								}

								room_data_html = '<tr class="' + color_class + '" sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td class="pt_nameid_td" id="nametd' + sch_id + '" style="width:auto; cursor:pointer;"><img src="../../library/images/icon_message.png" class="fr msg_icon">' + room_appt_move_up + pt_status_icon + '<span class="span_ptname_text">' + ptNameVal + ready_for_status_text + '</span><span class="pickTime hide">' + sch_ci_time1 + '</span>';

								room_data_html += '<div class="ApptDetails section hide"><div class="section_header"><img src="../../library/images/icon_close.png" style="width:15px;" class="fr">Appointment Details</div><table>';

								room_data_html += '<tr><td style="width:130px;">Appt. Time</td><td style="width:auto;">' + sch_st_time + '</td></tr>';
								room_data_html += '<tr><td style="width:130px;">Check-in Time</td><td style="width:auto;">' + sch_ci_time + '</td></tr>';
								room_data_html += '<tr><td>Technician</td><td>' + roomopnm + '</td></tr>';
								room_data_html += '<tr><td>Pt. At a Glance</td><td><span class="icon20 icon20_pag" onclick="showPAG(\'' + pt_id + '\')" title="Patient At a Glance"></span></td></tr>';
								/*if(ready4doc_msg_text!=''){
								 room_data_html += '<tr><td vAlign="top">Appt. Comments</td><td>'+ready4doc_msg_text+'</td></tr>';
								 }*/
								room_data_html += '</table></div>';
								room_data_html += '</td><td class="showTime private">&nbsp;</td></tr>';
								if (ready4doc_msg_text != '') {
									room_data_html += '<tr sch_id="' + sch_id + '" doc_id="' + doc_id + '"><td colspan="2" class="message_div">' + ready4doc_msg_text + '</td></tr>';
								}

								room_wise_pts_arr[room_no] += room_data_html;
								str_chk_in += pt_name + ' - ' + pt_id + "; ";

							}
						}
					}
					prev_prov = doc_id;
				});
				/*
				 if(sno == 1){
				 $("#header_1_"+prev_prov).html("");
				 }
				 if(snotech == 1){
				 $("#header_2_"+prev_prov).html("");
				 }
				 if(snophy == 1){
				 $("#header_3_"+prev_prov).html("");
				 }*/
				if (str_chk_in == "") {
					str_chk_in = "N/A";
				}
				$("#scroll_bar marquee").html('<b>CHECK IN: </b>' + str_chk_in);
				$("#checked_in_pts tbody").html(checked_in_pts_val);
				$("#scheduled_pts tbody").html(scheduled_pts_val);

				if (baseName == 'main_rooms.php') {
					$("#waiting_patients tbody").html(waiting_patients_val);
					//$("#waiting_patients").tablesorter();

					if ($('#refresh_mode').val() == 'auto' || forceRefresh1) {
						$('.div_room .tableCon tbody').html('');
						for (x in room_wise_pts_arr) {
							$('.priority').hide();
							$('#room' + x + ' .tableCon tbody').html(room_wise_pts_arr[x]);
							//console.log('A='+$('#room'+x+' div.tableCon').css('min-height'));
							tmp_tblcon_hgt = ($('#room' + x + ' div.tableCon tbody').height() + 10) + 'px';
							$('#room' + x + ' div.tableCon').css({'min-height': tmp_tblcon_hgt});
							//	console.log('B='+$('#room'+x+' div.tableCon').css('min-height'));
						}
					}

					$('.span_priority').each(function (index, element) {
						if ($(this).text() == '' || $(this).text() == '0') {
							$(this).hide();
							$(this).text('');
						} else {
							$(this).text('Priority ' + $(this).text());
							if ($(this).text() == 'Priority 1') {
								$(this).css({'background-color': '#f00'});
							} else if ($(this).text() == 'Priority 2' || $(this).text() == 'Priority 3') {
								$(this).css({'background-color': '#0c0'});
							}
							$(this).show();
						}

					});

					if ($('#refresh_mode').val() == 'auto' || forceRefresh1) {
						for (x in room_wise_priority) {
							if (room_wise_priority[x] != '0') {
								//if($('#room'+x+' .section_header .priority').text().indexOf('Priority')== -1) 
								$('#room' + x + ' .section_header .priority').text('Priority ' + room_wise_priority[x]);
								if (room_wise_priority[x] == '2' || room_wise_priority[x] == '3') {
									$('#room' + x + ' .section_header .priority').css({'background-color': '#0c0'});
								} else if (room_wise_priority[x] == '1') {
									$('#room' + x + ' .section_header .priority').css({'background-color': '#f00'});
								}

								$('#room' + x + ' .section_header .priority').show();
							} else {
								$('#room' + x + ' .section_header .priority').hide();
							}
						}

						//room_wise_procedure
						$('.span_room_proc').hide();
						for (x in room_wise_procedure) {
							if (room_wise_procedure[x] != '') {
								$('#room' + x + ' .section_header .span_room_proc').html('&nbsp;&nbsp;' + room_wise_procedure[x] + '&nbsp;&nbsp;');
								$('#room' + x + ' .section_header .span_room_proc').show();
							} else {
								$('#room' + x + ' .section_header .span_room_proc').hide();
							}
						}
					}

					// Show  context menu when Room is clicked
					attach_new_context_menu(".pt_nameid_td");

					// ATTACHING APPOINTMENT DETAIL EVENTS.
					$('.div_room .tableCon tbody tr td .msg_icon, #waiting_patients tbody tr td .msg_icon').click(function (e) {
						e.stopPropagation();
					});
					$('.div_room .tableCon tbody tr td .msg_icon, #waiting_patients tbody tr td .msg_icon').dblclick(function (e) {
						e.stopPropagation();
						editMessages($(this).parent('td'), 'nomsg');
					});
					$('.div_room .tableCon tbody tr td .msg_icon, #waiting_patients tbody tr td .msg_icon').mouseover(function (e) {
						//$('.ApptDetails').not($(this).find('.ApptDetails')).hide();
						t = $(this).parent().parent();
						to = t.offset();
						tApptD = t.find('.ApptDetails');
						tApptD.find('table').css({width: '350px'});
						if (tApptD.css('display') == 'none') {
							tApptD.css({position: 'absolute', top: (to.top + 20), left: (to.left + 15), border: '5px solid #ccc'});
						}
						tApptD.show();
						e.stopPropagation();
						$('.ApptDetails').not(tApptD).hide();

						//BiNDING ESC KEY to hide appt details
						$(document).bind("keydown", function (e) {
							if (e.keyCode == 27) {
								$('.ApptDetails').hide();
								$(document).unbind("keydown");
							}
						});
					});
					$('.ApptDetails').click(function (e) {
						e.stopPropagation();
						$('.ApptDetails').hide();
					});

					$('.div_room .tableCon tbody tr').mouseup(function (e) {
						$('.ApptDetails').hide();
					});

					// ATTACHING APPOINTMENT MOVE-UP EVENTS
					$('.div_room .tableCon tbody tr td .moveup_icon').mousemove(function (e) {
						$('.ApptDetails').hide();
					});
					$('.div_room .tableCon tbody tr td .moveup_icon').click(function (e) {
						e.stopPropagation();

						mvup_tr = $(this).parent().parent();
						room = $(this).attr('room');
						room_priority_change('moveup', mvup_tr.attr('sch_id'), room);
					});

				} else {
					//console.log($('#refresh_mode').val() + ' :: Force='+ forceRefresh1);
					if ($('#refresh_mode').val() == 'auto' || forceRefresh1) {
						$("#ready_for_doc_pts tbody").html(ready_for_doc_pts_val + waiting_ready_for_doc_pts_val);
						$("#tech_active_list tbody").html(tech_active_list_val + waiting_tech_active_list_val);
						//$("#ready_for_doc_pts").tablesorter();
						//$("#tech_active_list").tablesorter();
						$("#waiting_patients tbody").html(waiting_patients_val);
						//$("#waiting_patients").tablesorter();
						$('.span_priority').each(function (index, element) {
							if ($(this).text() == '' || $(this).text() == '0') {
								$(this).hide();
								$(this).text('');
							} else {
								$(this).text('Priority ' + $(this).text());
								if ($(this).text() == 'Priority 1') {
									$(this).css({'background-color': '#f00'});
								} else if ($(this).text() == 'Priority 2' || $(this).text() == 'Priority 3') {
									$(this).css({'background-color': '#0c0'});
								}
								$(this).show();
							}

						});
					} else {
						$('.span_priority').each(function (index, element) {
							if ($(this).text() == '' || $(this).text() == '0') {
								$(this).hide();
								$(this).text('');
							} else {
								if ($(this).text().indexOf('Priority') == -1)
									$(this).text('Priority ' + $(this).text());
								if ($(this).text() == 'Priority 1') {
									$(this).css({'background-color': '#f00'});
								} else if ($(this).text() == 'Priority 2' || $(this).text() == 'Priority 3') {
									$(this).css({'background-color': '#0c0'});
								}
								$(this).show();
							}
						});
					}
					attach_new_context_menu(".pt_nameid_td");

				}
				if (view_mode != 'private') {
					$('.private').hide();
				} else {
					$('.private').show();
				}

				/*****making drag visible on patient name click****/
				$('#tech_active_list tbody tr td.pt_nameid_td, #ready_for_doc_pts tbody tr td.pt_nameid_td, #waiting_patients tbody tr td.pt_nameid_td,#checked_in_pts tbody tr td.pt_nameid_td, .div_room .tableCon tbody tr td.pt_nameid_td').click(function (e) {
					//	if($('#refresh_mode').val()!='auto'){
					//		alert('This functionality works in "AUTO" mode only.');
					//	}else{		
					$('#tech_active_list tbody tr td.pt_nameid_td, #ready_for_doc_pts tbody tr td.pt_nameid_td, #waiting_patients tbody tr td.pt_nameid_td,#checked_in_pts tbody tr td.pt_nameid_td, .div_room .tableCon tbody tr td.pt_nameid_td').unbind('click');
					e.stopPropagation();
					$('.ApptDetails').hide();// hiding if any appt details div is appeared.
					this_source_tr = $(this).parent();
					this_tr_width = this_source_tr.parent().width();
					this_tr_sch_id = this_source_tr.attr('sch_id');

					pause_monitor_flow(1); // to pause the flow.

					html_Val = $(this).parent().parent().parent().parent().find('thead').html();
					html_Val += '<tr>' + $(this).parent().html() + '</tr>';
					$('#record_drag_container').html(html_Val);
					this_source_tr.css('opacity', '0.4');

					$(document).bind("mousemove", function (e) {
						var $this = $("#record_drag_container");
						$this.offset({top: e.pageY + 20, left: e.pageX + 10});
						$this.css({'display': 'block', 'width': this_tr_width});

					});

					/*****BINDING CLICK EVENT ON POSSIBLE DESTINATION HEADERS FOR THIS DURATION ONLY****/
					$('#box_waitingpt, #box_phy_tat, #box_tech_al,.div_room').click(function () {
						//$('#room'+x+' .tableCon tbody')
						$(document).unbind("mousemove");
						$(document).unbind("keydown");
						this_dest_obj_id = $(this).attr('id');
						switch (this_dest_obj_id) {
							case 'box_waitingpt':
								room_priority_change('task_4', this_tr_sch_id);
								break;
							case 'box_phy_tat':
								room_priority_change('task_1', this_tr_sch_id);
								break;
							case 'box_tech_al':
								room_priority_change('task_2', this_tr_sch_id);
								break;
							default:
								room_priority_change('room_' + this_dest_obj_id.replace('room', ''), this_tr_sch_id);
								break;
						}
						$("#record_drag_container").html('').css('display', 'none');
						this_source_tr = '';
						this_tr_width = '';
						this_tr_sch_id = '';
						$(this).unbind('click');
					});

					$(document).bind("keydown", function (e) {
						if (e.keyCode == 27) {
							$("#record_drag_container").html('').css('display', 'none');
							$(document).unbind("mousemove");
							$(document).unbind("keydown");
							//if(this_source_tr) this_source_tr.css('opacity','1');
							this_source_tr = '';
							this_tr_width = '';
							this_tr_sch_id = '';
							getSchData(true); // to resume the flow.
						}
					});
					//}

				});


				$('.pickDTime').parent("td").dblclick(function () {
					editDilTimer(this);
				});
				$('.message_div').dblclick(function () {
					editMessages(this);
				});
				allTimer = setInterval("StartTimers()", 100);
				forceRefresh1 = false;
				/***SCHEDULING FUNCTION TO CALL ITSELF AGAIN***/
				page_loader2 = setTimeout("getSchData()", 10000);
			}
		});
	}
}

var monitor_flow_status = 'moving';
function pause_monitor_flow(forcePause) {
	if (typeof (forcePause) == 'undefined')
		forcePause = 0;
	if ($('#refresh_mode').val() == 'auto') {
		if ((typeof (page_loader2) != 'undefined' && monitor_flow_status == 'moving') || forcePause == 1) {
			clearInterval(page_loader2);
			monitor_flow_status = 'paused';
		} else {
		}
	}
}

/* LOADING PATIENTS  */
function load_pts(fac, prov) {
	if (!fac)
		fac = "";
	if (!prov)
		prov = "";
	if (fac != "") {
		if (fac == "get") {
			fac = $("#sel_facility").val();
			//	strUser = $('#active_pro').val();
			//	if(strUser=='Select Physician'){strUser='';}
			//	$('.tddocname').html(strUser);
		}
		if (prov == "get") {
			if (baseName == 'main_rooms.php') {
				prov = $("#sel_profiles").val();
			} else {
				prov = $("#active_pro").val();
			}
		}
		$('#active_pro').val(prov);
		getSchData(fac, prov);
	} else {
		//clean up all sections and close checkout popup
		$("#scheduled_pts tbody").html("");
		$("#checked_in_pts tbody").html("");
		if (baseName == 'main_rooms.php') {
			$("#div_room tableCon tbody").html("");
		} else {
			$("#ready_for_doc_pts tbody").html("");
			$("#tech_active_list tbody").html("");
			$("#waiting_patients tbody").html("");
		}
	}
}

/* OPENING POPUP FOR CHECKED-OUT PATIENTS  */
function open_co_pop() {
	coptwin = window.open("co_done_popup.php", "coptwin", "height=" + ($(window).height() / 2) + ",width=" + $(window).width() + ",location=no,status=no,top=50");
}

/*  EDITING DILATION TIMER ON DOUBLE CLICK  */
function editDilTimer(obj) {
	hideDilTimerForm();
	var sch_id = $(obj).parent().attr('sch_id');
	$('#hidd_dt_sch_id').val(sch_id);
	offset = $(obj).offset();
	$('.dilTimerEditor').css('top', offset.top + 40);
	$('.dilTimerEditor').css('left', offset.left);
	var ar_val = $(obj).find('.showDTime').text().split(":");
	val = ar_val[0];
	$('.dilTimerEditor #text_timerMinute').val(val);
	$('.dilTimerEditor').show();
	$('.dilTimerEditor #text_timerMinute').focus();
}
function hideDilTimerForm() {
	$('#hidd_dt_sch_id').val('');
	$('.dilTimerEditor #text_timerMinute').val('');
	$('.dilTimerEditor').hide();
}
function saveDilTimer() {
	sch_id = $('#hidd_dt_sch_id').val();
	text = $('.dilTimerEditor #text_timerMinute').val();
	url = "edit_dilation_timer.php?sch_id=" + sch_id + "&newtime=" + parseInt(text.trim());
	//alert(url);
	$.ajax({type: "POST", url: url, success: function (r) {
			//	alert(r);
			hideDilTimerForm();
			clearInterval(page_loader2);
			delete page_loader2;
			getSchData();
		}
	});
}

/*  EDITING MESSAGES ON DOUBLE CLICK  */
function editMessages(obj, msgType) {
	if (typeof (msgType) == 'undefined')
		msgType = '';
	hideEditMessages();
	var sch_id = $(obj).parent().attr('sch_id');
	$('#hidd_sch_id').val(sch_id);
	offset = $(obj).offset();
	internalDim = innerDim();
	newL = parseInt(offset.left + parseInt($('.msgEditor').width()));
	if (newL > internalDim['w']) {
		newL = (internalDim['w'] - parseInt($('.msgEditor').width())) - 15;
	} else {
		newL = offset.left;
	}
	//alert(newL+' :: '+internalDim['w']);
	$('.msgEditor').css('top', offset.top + 20);
	$('.msgEditor').css('left', newL);
	//alert("a"+$(obj).text()+"a");

	if ($(obj).text() == "&nbsp;") {
		$(obj).text("");
	}
	if ($(obj).text() == $(obj).html()) {
		$('.msgEditor textarea').val($(obj).text());
	} else {
		$('.msgEditor textarea').val('');
	}
	$('.msgEditor').show();
	$('.msgEditor textarea').focus();
}
function hideEditMessages() {
	$('#hidd_sch_id').val('');
	$('.msgEditor textarea').val('');
	$('.msgEditor').hide();
}
function saveMessages() {
	sch_id = $('#hidd_sch_id').val();
	text = $('.msgEditor textarea').val();
	url = "edit_messages.php?sch_id=" + sch_id + "&msgtxt=" + escape(text.trim());
	//alert(url);
	$.ajax({type: "POST", url: url, success: function (r) {
			//alert(r);
			hideEditMessages();
			clearInterval(page_loader2);
			delete page_loader2;
			getSchData();
		}
	});
}
function convertMS(ms) {
	var d, h, m, s;
	s = Math.floor(ms / 1000);
	m = Math.floor(s / 60);
	s = s % 60;
	h = Math.floor(m / 60);
	m = m % 60;
	d = Math.floor(h / 24);
	h = h % 24;
	return {d: d, h: h, m: m, s: s};
}
;
function date_diff(date1) {
	if (date1 == "")
		return;
	date1 = date1.trim();
	dateArr = date1.split(" ");
	dDate = dateArr[0];
	dDateArr = dDate.split("-");

	tTime = dateArr[1];
	if (typeof (tTime) == 'undefined') {
		tTimeArr = dDate.split(":");
	} else {
		tTimeArr = tTime.split(':');
	}

	date1 = Date.UTC(dDateArr[0], dDateArr[1] - 1, dDateArr[2], tTimeArr[0], tTimeArr[1], tTimeArr[2]);

	newcT = new Date();
	dY = newcT.getFullYear();
	dM = newcT.getMonth();
	dD = newcT.getDate();
	cH = newcT.getHours();
	cM = newcT.getMinutes();
	cS = newcT.getSeconds();
	cMS = newcT.getMilliseconds();
	date2 = Date.UTC(dY, dM, dD, cH, cM, cS);

	date_diff_var = (date2 - date1);
	objDate = convertMS(date_diff_var);
	return objDate;
}
function StartTimers() {
	/*	$("#scheduled_pts tbody").html("");
	 $("#checked_in_pts tbody").html("");
	 $("#ready_for_doc_pts tbody").html("");
	 $("#tech_active_list tbody").html("");
	 $("#waiting_patients tbody").html("");*/
	var arr_Boxes = new Array('#checked_in_pts tbody tr', '#ready_for_doc_pts tbody tr', '#tech_active_list tbody tr', '#waiting_patients tbody tr', '.room_records tr');
	//var arr_Boxes = new Array('#ready_for_doc_pts tbody tr');
	$(arr_Boxes).each(function (i) {
		$(arr_Boxes[i]).each(function () {
			oT = $(this).find("td.pickTime, span.pickTime").text(); //original time value of event.
			if (oT != '') {
				objDate = date_diff(oT);
				//console.log(objDate);
				oH = typeof (objDate.h) != 'undefined' ? objDate.h : objDate['h'];
				oM = typeof (objDate.m) != 'undefined' ? objDate.m : objDate['m'];
				oS = typeof (objDate.s) != 'undefined' ? objDate.s : objDate['s'];
				dH = oH;
				dM = oM;
				var addClass = 'greenText';
				if (dH != '00') {
					addClass = 'redText';
				} else if (dM >= 30 && dH == '00') {
					addClass = 'orangeText';
				} else if (dM < 30) {
					addClass = 'greenText';
				}
				if (typeof (objDate) != 'undefined') {
					objDate.h = pad2(oH);
					objDate.m = pad2(oM);
					objDate.s = pad2(oS);
					timeString = "<span class='" + addClass + "'>" + oH + ":" + oM + ":" + oS + "</span>";

					if (timeString.search('-') >= 0)
						$(this).find("td.showTime").html('');
					else
						$(this).find("td.showTime").html(timeString);
				}
			}
		});
	});
	StartIconTimers();
}
function pad2(number) {
	if (number < 10)
		return number = '0' + number;
	else
		return number;
}
function StartIconTimers() {
	$('#waiting_patients tbody tr').each(function () {
		oT = $(this).find("span.pickDTime").text(); //original time value of event.
		oT = oT.trim();
		objDate = date_diff(oT);
		if (typeof (objDate) == 'undefined')
			return;
		dH = objDate.h;
		dM = objDate.m;
		dS = objDate.s;

		if (dH <= 0) {
			if (dM <= parseInt(default_dilation_timer)) {
				dM = parseInt(default_dilation_timer) - dM;
				dS = 60 - dS;

			} else {
				dM = "0";
				dS = "0";
			}
		} else {
			dM = "0";
			dS = "0";
		}
		var addClass = 'greenText';
		if (dH == '00') {
			if (dM >= 10) {
				addClass = 'greenText';
			} else if (dM >= 5) {
				addClass = 'orangeText';
			} else if (dM >= 0) {
				addClass = 'redText';
			}
		} else {
			addClass = 'redText';
		}
		dM = pad2(dM);
		dS = pad2(dS);
		timeString = '<span class="' + addClass + '">' + dM + ':' + dS + '</span>';
		$(this).find("span.showDTime").html(timeString);
	});
}