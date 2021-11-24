var IAmNotActive = false;
var IAmBusyNow = false;
var time;

window.onfocus = function() {
	IAmNotActive = false;
	getSchData();
};

window.onblur = function(){
	if(IMM_ALWAYS_REFRESH!='YES'){
		IAmNotActive = true;
		if(typeof(page_loader2)=='number') clearInterval(page_loader2);
	}	
};

/*  GETTING SCHEDULER DATA  */
function getSchData(){
	if(IAmNotActive || IAmBusyNow) return; //DON'T EXECUTE FURTHER IF WINDOW IS NOT ACTIVE or AJAX is busy.
	if(typeof(page_loader2)=='number') clearInterval(page_loader2);
	//console.log('Flow Status: '+monitor_flow_status+' :: Pageloader='+typeof(page_loader2)+' :: forceRefresh='+forceRefresh1);
	var fac = $("#sel_facility").val();
	var prov = $("#active_pro").val();
	var sort_by = $("#sort_by").val();
	var sort_order = $("#sort_order").val();
	var chk_showchkout = $("#chk_showchkout").prop('checked') ? true : false;
	var timezone = jstz.determine();
	local_tz = timezone.name();
	if(fac != ""){
		url_var = "extended_view_data.php?fac="+fac+"&prov="+prov+"&sort_by="+sort_by+"&sort_order="+sort_order+"&local_tz="+encodeURI(local_tz);
		IAmBusyNow = true;
		$.ajax({
			type: "POST",
			url: url_var,
			dataType: "text",
			success: function(apptData){
						//$("#active_patients tbody").html('<tr><td colspan=21>'+apptData+'</td><tr>');
					//	return;
						$('#processing_image').hide();
						apptDataRs = JSON.parse(apptData);
						var sno = 1;
						var str_chk_in = "";
						var Html='';
						var prev_prov = "0";
						var room_wise_pts_arr = new Array();
						var room_wise_priority= new Array();
						var room_wise_procedure = new Array();
						/*
						if(typeof(allTimer)=='number'){
							clearInterval(allTimer);	
						}
						*/
						
						/*****START HERE MAIN LOOP OF EXECUTION OF JSON DATA OBJECT***/
						for(schId in apptDataRs){
							row = apptDataRs[schId];
							schId = schId.substring(3);
							if(!chk_showchkout && row.co_time!='') continue;
							
							appt_status_id	= (typeof(row.appt_status_id)=='string' && row.appt_status_id!=null) ? row.appt_status_id : '';
							if(typeof(IMM_LIST_NOSHOW_APPTS)!='undefined' && IMM_LIST_NOSHOW_APPTS==0 && appt_status_id == '3') continue;
							arrival_time	= (typeof(row.arrival_time)=='string' && row.arrival_time!=null && row.arrival_time!='') ? row.arrival_time : '--';
							checkin_time	= (typeof(row.checkin_time)=='string' && row.checkin_time!=null && row.checkin_time!='') ? row.checkin_time : '--';
							if(checkin_time!='--'){
								str_chk_in += row.patient_name+';&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							}
							
							fd_time 		= (typeof(row.fd_time)=='string' && row.fd_time!=null) ? row.fd_time : '--';
							room_name		= (typeof(row.room_name)=='string' && row.room_name!=null) ? row.room_name : 'N/A';
							
							tech_name		= (typeof(row.tech_name)=='string' && row.tech_name!=null) ? row.tech_name : '';
							tech_start_time	= (typeof(row.tech_start_time)=='string' && row.tech_start_time!=null) ? row.tech_start_time : '';
							tech_stop_time	= (typeof(row.tech_stop_time)=='string' && row.tech_stop_time!=null) ? row.tech_stop_time : '';
							tech_total		= (typeof(row.tech_total)=='string' && row.tech_total!=null) ? row.tech_total : '';
							tech_room		= (typeof(row.tech_room)=='string' && row.tech_room!=null) ? row.tech_room : 'N/A';

							phy_name		= (typeof(row.phy_name)=='string' && row.phy_name!=null) ? row.phy_name : '';
							phy_start_time	= (typeof(row.phy_start_time)=='string' && row.phy_start_time!=null) ? row.phy_start_time : '';
							phy_stop_time	= (typeof(row.phy_stop_time)=='string' && row.phy_stop_time!=null) ? row.phy_stop_time : '';
							phy_total		= (typeof(row.doc_total)=='string' && row.doc_total!=null) ? row.doc_total : '';
							phy_room		= (typeof(row.phy_room)=='string' && row.phy_room!=null) ? row.phy_room : 'N/A';
							
							wait_total		= (typeof(row.wait_total)=='string' && row.wait_total!=null) ? row.wait_total : '';
							dilation_time	= (typeof(row.dilation_time)=='string' && row.dilation_time!=null) ? row.dilation_time : '';
							dilation_complete = (typeof(row.dilation_complete)=='string' && row.dilation_complete!=null) ? row.dilation_complete : 'N';
							dilation_timer	= '';
							if((typeof(row.co_time)!='string' || row.co_time=='') && dilation_complete=='N'){
								dilation_timer	= (typeof(row.dilation_timer)=='string' && row.dilation_timer!=null) ? row.dilation_timer : '';
							}else if((typeof(row.co_time)=='string' && row.co_time!='') || dilation_complete=='Y'){
								dilation_time += (typeof(row.dilation_timer)=='string' && row.dilation_timer!=null) ? '<br>'+row.dilation_timer : '';
								dilation_timer = '';
							}
							if(dilation_timer!=''){
								dilation_time = '<div>'+dilation_time+'<span id="pickDTime'+schId+'" class="hide">'+dilation_timer+'</span></div>';
								dilation_time += '<span id="elapsedDTime'+schId+'"></span>';
							}
							
							pt_name_noshow_prefix = '';
							if(appt_status_id == '3'){pt_name_noshow_prefix = '<font color="#ff0000"><b title="No Show"><i>NS</i></b></font> ';}

							Html += '<tr class="extended_row" sch_id="'+schId+'" doc_id="'+row.appt_doctor_id+'">';
							Html += '<td>'+sno+'</td>';
							if(Cols2Show[1]==1)
							Html += '<td class="pt_nameid_td" id="nametd'+schId+'">'+pt_name_noshow_prefix+row.patient_name+'</td>';//+' - '+row.patient_id
							if(Cols2Show[2]==1)
							Html += '<td>'+row.appt_reason+'</td>';
							if(Cols2Show[3]==1)
							Html += '<td title="'+row.appt_doctor_name+'">'+row.appt_doctor_initials+'</td>';
							if(Cols2Show[4]==1)
							Html += '<td>'+row.appt_time+'</td>';
							if(Cols2Show[5]==1)
							Html += '<td>'+arrival_time+'</td>';
							if(Cols2Show[6]==1)
							Html += '<td>'+checkin_time+'</td>';
							if(Cols2Show[7]==1)
							Html += '<td>'+fd_time+'</td>';
							if(Cols2Show[8]==1)
							Html += '<td>'+row.fsheet+'</td>';
							if(Cols2Show[9]==1)
							Html += '<td>'+row.arrival_2now+'</td>';
							if(Cols2Show[10]==1)
							Html += '<td>'+row.checkin_2now+'</td>';
							if(Cols2Show[11]==1)
							Html += '<td>'+tech_name+'<br>'+tech_start_time+'</td>';
							if(Cols2Show[12]==1)
							Html += '<td>'+tech_room+'</td>';
							if(Cols2Show[13]==1)
							Html += '<td>'+tech_stop_time+'</td>';
							if(Cols2Show[14]==1)
							Html += '<td>'+tech_total+'</td>';
							if(Cols2Show[15]==1)
							Html += '<td>'+dilation_time+'</td>';
							if(Cols2Show[16]==1)
							Html += '<td>'+wait_total+'</td>';
							if(Cols2Show[17]==1)
							Html += '<td>'+phy_name+'<br>'+phy_start_time+'</td>';
							if(Cols2Show[18]==1)
							Html += '<td>'+phy_room+'</td>';
							if(Cols2Show[19]==1)
							Html += '<td>'+phy_stop_time+'</td>';
							if(Cols2Show[20]==1)
							Html += '<td>'+phy_total+'</td>';
							if(Cols2Show[21]==1)
							Html += '<td>'+row.co_time+'<br>'+row.pt_time_full+'</td>';
							Html += '</tr>';
							sno++;
						}						
						/****END OF MAIN LOOP OF EXECUTION OF JSON DATA OBJECT***/						
												
						if(str_chk_in == ""){
							str_chk_in = "N/A";
						}
						$("#scroll_bar marquee").html('<b>CHECK IN: </b>'+str_chk_in);
						$("#active_patients tbody").html(Html);
					//	$("#active_patients").append('<div>'+apptData+'</div>');
						startWaitingTimers();
						//return;
						
						{
							//console.log($('#refresh_mode').val() + ' :: Force='+ forceRefresh1);
							if($('#refresh_mode').val()=='auto'){
								$("#ready_for_doc_pts tbody").html(ready_for_doc_pts_val+waiting_ready_for_doc_pts_val);
								$("#tech_active_list tbody").html(tech_active_list_val+waiting_tech_active_list_val);
								//$("#ready_for_doc_pts").tablesorter();
								//$("#tech_active_list").tablesorter();
								$("#waiting_patients tbody").html(waiting_patients_val);	
								//$("#waiting_patients").tablesorter();
								$('.span_priority').each(function(index, element) {
									if($(this).text()=='' || $(this).text()=='0'){$(this).hide();$(this).text('');}
									else{
											$(this).text('Priority '+$(this).text());
											if($(this).text()=='Priority 1'){
												$(this).css({'background-color':'#f00'});
											}else if($(this).text()=='Priority 2' || $(this).text()=='Priority 3'){
												$(this).css({'background-color':'#0c0'});
											}
											$(this).show();
									}
									
								});
							}
							attach_new_context_menu(".pt_nameid_td");

						}
						
						/*
						$('.message_div').dblclick(function() {
							editMessages(this);
						});
						allTimer = setInterval("StartTimers()",100);
						forceRefresh1 = false;
						*/
						if(typeof(IMM_AUTO_REFRESH_MODE)!='undefined' && IMM_AUTO_REFRESH_MODE==1){
							page_loader2 = setTimeout("getSchData()",default_refresh_interval);
						}
					//	console.log(default_refresh_interval);
						
						IAmBusyNow = false;
					}
	   });
	}	
}

function startWaitingTimers(){
	$('.extended_row').each(function(index, element) {
        schId = $(this).attr('sch_id');
		
		//----NORMAL WAIT TIMERS--
		pickT = 'pickTime'+schId;
		showT = 'elapsedTime'+schId;
		pickV = $('#'+pickT).text();
		if(pickV!=''){
			pickTArr1 = pickV.split(' ');
			pickTArr1 = pickTArr1[1];
			pickTArr2 = pickTArr1.split(':');
			elapseMe({hr : pickTArr2[0], min : pickTArr2[1], sec: pickTArr2[2],targetId:showT, update : true, show: "hhmmss" });
		}
		
		//----DILATION TIMER--
		pickDT = 'pickDTime'+schId;
		showDT = 'elapsedDTime'+schId;
		pickDV = $('#'+pickDT).text();
		if(pickDV!=''){
			pickDTArr1 = pickDV.split(' ');
			pickDTArr1 = pickDTArr1[1];
			pickDTArr2 = pickDTArr1.split(':');
			elapseMe({hr : pickDTArr2[0], min : pickDTArr2[1], sec: pickDTArr2[2],targetId:showDT, update : true, show: "hhmmss" });
		}
		
		
		
    });
	
	
}

var monitor_flow_status = 'moving';
function pause_monitor_flow(forcePause){
	if(typeof(forcePause)=='undefined') forcePause = 0;
	if($('#refresh_mode').val()=='auto'){
		if((typeof(page_loader2)!='undefined' && monitor_flow_status=='moving') || forcePause == 1){
			clearInterval(page_loader2);
			monitor_flow_status = 'paused';
		}else{
		}
	}
}

/* LOADING PATIENTS  */
function load_pts(fac, prov){
	if(!fac) fac = "";
	if(typeof(prov)=='undefined') prov = "";
	if(fac != ""){
		if(fac == "get"){
			fac = $("#sel_facility").val();
		}else{
			$('#active_fac').val(fac);
		}
		if(prov == "get"){
			prov = $("#active_pro").val();
		}else{
			$('#active_pro').val(prov);
		}
		$('#processing_image').show();
		getSchData(fac, prov);
	}else{
		$("#active_patients tbody").html("");
	}
}

/* OPENING POPUP FOR CHECKED-OUT PATIENTS  */
function open_co_pop(){
	coptwin = window.open("co_done_popup.php","coptwin","height="+($(window).height()/2)+",width="+$(window).width()+",location=no,status=no,top=50");
}

/*  EDITING DILATION TIMER ON DOUBLE CLICK  */
function editDilTimer(obj){
	hideDilTimerForm();
	var sch_id = $(obj).parent().attr('sch_id');
	$('#hidd_dt_sch_id').val(sch_id);
	offset = $(obj).offset();
	$('.dilTimerEditor').css('top',offset.top+40);
	$('.dilTimerEditor').css('left',offset.left);
	var ar_val = $(obj).find('.showDTime').text().split(":");
	val = ar_val[0];
	$('.dilTimerEditor #text_timerMinute').val(val);
	$('.dilTimerEditor').show();
	$('.dilTimerEditor #text_timerMinute').focus();
}
function hideDilTimerForm(){
	$('#hidd_dt_sch_id').val('');
	$('.dilTimerEditor #text_timerMinute').val('');
	$('.dilTimerEditor').hide();
}
function saveDilTimer(){
	sch_id = $('#hidd_dt_sch_id').val();
	text = $('.dilTimerEditor #text_timerMinute').val();
	url = "edit_dilation_timer.php?sch_id="+sch_id+"&newtime="+parseInt(text.trim());
	//alert(url);
	$.ajax({type:"POST",url:url,success:function(r){
		//	alert(r);
			hideDilTimerForm();
			clearInterval(page_loader2);
			delete page_loader2;
			getSchData();
		}
	});
}

/*  EDITING MESSAGES ON DOUBLE CLICK  */
function editMessages(obj,msgType){
	if(typeof(msgType)=='undefined') msgType='';
	hideEditMessages();
	var sch_id = $(obj).parent().attr('sch_id');
	$('#hidd_sch_id').val(sch_id);
	offset = $(obj).offset();
	internalDim = innerDim();
	newL = parseInt(offset.left+parseInt($('.msgEditor').width()));
	if(newL > internalDim['w']){
		newL = (internalDim['w']-parseInt($('.msgEditor').width()))-15;
	}else{
		newL = offset.left;
	}
	//alert(newL+' :: '+internalDim['w']);
	$('.msgEditor').css('top',offset.top+20);
	$('.msgEditor').css('left',newL);
	//alert("a"+$(obj).text()+"a");
	
	if($(obj).text() == "&nbsp;"){
		$(obj).text("");
	}
	ot = $(obj).text();
	ot = ot.trim();
//	if($(obj).text()==$(obj).html()){
		$('.msgEditor textarea').val(ot);
//	}else{$('.msgEditor textarea').val('');}
	$('.msgEditor').show();
	$('.msgEditor textarea').focus();
}
function hideEditMessages(){
	$('#hidd_sch_id').val('');
	$('.msgEditor textarea').val('');
	$('.msgEditor').hide();
}
function saveMessages(){
	sch_id = $('#hidd_sch_id').val();
	text = $('.msgEditor textarea').val();
	url = "edit_messages.php?sch_id="+sch_id+"&msgtxt="+escape(text.trim());
	//alert(url);
	$.ajax({type:"POST",url:url,success:function(r){
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
	return { d: d, h: h, m: m, s: s };
};
function date_diff(date1){
	if(date1 == "")return;
	date1 = date1.trim();
	dateArr = date1.split(" ");
	dDate = dateArr[0];
	dDateArr = dDate.split("-");
	
	tTime = dateArr[1];
	if(typeof(tTime)=='undefined') {tTimeArr = dDate.split(":");}
	else{tTimeArr = tTime.split(':');}
	
	date1 = Date.UTC(dDateArr[0],dDateArr[1]-1,dDateArr[2],tTimeArr[0],tTimeArr[1],tTimeArr[2]);
	
	newcT = new Date();
	dY = newcT.getFullYear();
	dM = newcT.getMonth();
	dD = newcT.getDate();
	cH = newcT.getHours();
	cM = newcT.getMinutes();
	cS = newcT.getSeconds();
	cMS = newcT.getMilliseconds();
	date2 = Date.UTC(dY,dM,dD,cH,cM,cS);
	
	date_diff_var = (date2 - date1);
	objDate = convertMS(date_diff_var);
	return objDate;
}
function StartTimers(){
/*	$("#scheduled_pts tbody").html("");
	$("#checked_in_pts tbody").html("");
	$("#ready_for_doc_pts tbody").html("");
	$("#tech_active_list tbody").html("");
	$("#waiting_patients tbody").html("");*/
	var arr_Boxes = new Array('#checked_in_pts tbody tr', '#ready_for_doc_pts tbody tr', '#tech_active_list tbody tr', '#waiting_patients tbody tr', '.room_records tr');
	//var arr_Boxes = new Array('#ready_for_doc_pts tbody tr');
	$(arr_Boxes).each(function(i){
		$(arr_Boxes[i]).each(function(){
			oT = $(this).find("td.pickTime, span.pickTime").text(); //original time value of event.
			if(oT != ''){
				objDate = date_diff(oT);
				//console.log(objDate);
				oH = typeof(objDate.h)!='undefined' ? objDate.h : objDate['h'];
				oM = typeof(objDate.m)!='undefined' ? objDate.m : objDate['m'];
				oS = typeof(objDate.s)!='undefined' ? objDate.s : objDate['s'];
				dH = oH; dM = oM;
				var addClass = 'greenText';
				if(dH != '00'){addClass='redText';}
				else if(dM>=30 && dH=='00'){addClass='orangeText';}
				else if(dM<30){addClass='greenText';}
				if(typeof(objDate)!='undefined'){
					objDate.h = pad2(oH);
					objDate.m = pad2(oM);
					objDate.s = pad2(oS);
					timeString = "<span class='"+addClass+"'>"+pad2(oH)+":"+pad2(oM)+":"+pad2(oS)+"</span>";
					
					if(timeString.search('-') >= 0) $(this).find("td.showTime").html('');
					else $(this).find("td.showTime").html(timeString);
				}
			}
		});
   });
   StartIconTimers();
}
function pad2(number) {
	if(number < 10) {return number = '0'+number;}
	else {return number;}
}
function StartIconTimers(){
	$('#waiting_patients tbody tr').each(function(){
		oT = $(this).find("span.pickDTime").text(); //original time value of event.
		oT = oT.trim();
		objDate = date_diff(oT);
		if(typeof(objDate)=='undefined') return;
		dH = objDate.h; dM = objDate.m; dS = objDate.s;
		
		if(dH<=0){
			if(dM <= parseInt(default_dilation_timer)){
				dM = parseInt(default_dilation_timer) - dM;
				dS = 60 - dS;
				
			}else{
				dM = "0";
				dS = "0";
			}
		}else{
			dM = "0";
			dS = "0";
		}
		if(parseInt(dS)>59) dS = '59';
		
		var addClass = 'greenText';
		if(dH == '00'){
			if(dM>=10){addClass='greenText';}
			else if(dM>=5){addClass='orangeText';}
			else if(dM>=0){addClass='redText';}
		}else{
			addClass='redText';
		}
		dM = pad2(dM);
		dS = pad2(dS);
		timeString = '<span class="'+addClass+'">'+dM+':'+dS+'</span>';
		$(this).find("span.showDTime").html(timeString);
	});
}

function imon_sortby(t,colName){
	$('span.ui-icon').remove();
	$('#sort_by').val(colName);
	if($('#sort_order').val()==''){
		$('#sort_order').val('asc');
		$(t).append('<span class="ui-icon ui-icon-arrow-1-s">A</span>');
	}else if($('#sort_order').val()=='asc'){
		$('#sort_order').val('desc');
		$(t).append('<span class="ui-icon ui-icon-arrow-1-n">V</span>');
	}else if($('#sort_order').val()=='desc'){
		$('#sort_order').val('');
		if($('#sort_order').val()=='') $('#sort_by').val('');
	}
	$('#processing_image').show();
	getSchData();	
}