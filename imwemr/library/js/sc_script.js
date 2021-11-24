

/*SCHEDULER VERSION 1.1.0 SCRIPTS*/

function dgi(id){return document.getElementById(id);}
function dgn(name){return document.getElementsByName(name);}


//loading images
var edit_appt_img;
var add_appt_img;
var hid_appt_img;
var datesRange;
var proc_lbls_arr;
var owl='';
// global patient change controller for primary phy. selection
var gl_pt_ch_ct='';
var pri_phy_chk_flag=1;

var cur_first_month = '';

var ga_ap_id='';
var ga_st_type='';
var ga_sel_date='';
var ga_sel_fac='';
var ga_pt_id='';

var firstAvail_keepOrg=1;
/*
Function: preloader
Purpose: to pre load images in memoryy
Author: AA
*/
function preloader() {
	rel_url = "../../";
	if(typeof(top.JS_WEB_ROOT_PATH)!='undefined') rel_url = top.JS_WEB_ROOT_PATH+"/";
	//edit appt image
	edit_appt_img = new Image();
	edit_appt_img.src = rel_url+"library/images/b_edit.png";
	edit_appt_img.id = "TestImage";
	
	//add appt image
	add_appt_img = new Image();
	add_appt_img.src=rel_url+"library/images/add_appoint.gif";

	//hidden appt image
	hid_appt_img = new Image();
	hid_appt_img.src=rel_url+"library/images/grippy.gif";
}

//setting image paths
//preloader();

/*
Function: image_replace
Purpose: to render images on interface
Author: AA
*/
function image_replace(){

	var obj_edit_appt_img = document.getElementsByName("edit_appt_img");
	if(edit_appt_img){
		for(var i = 0; i < obj_edit_appt_img.length; i++){
			obj_edit_appt_img[i].src = edit_appt_img.src;
		}
	}

	var obj_add_appt_img = document.getElementsByName("addImage");
	if(add_appt_img){
		for(var i = 0; i < obj_add_appt_img.length; i++){
			obj_add_appt_img[i].src = add_appt_img.src;
		}
	}

	var obj_hid_appt_img = document.getElementsByName("grippy");
	if(hid_appt_img){
		for(var i = 0; i < obj_hid_appt_img.length; i++){
			obj_hid_appt_img[i].src = hid_appt_img.src;
		}
	}
}

/*
Function: fac_change_load
Purpose: Combined actions to be performed when loading scheduler base on change in facility
Author: AA
*/
function fac_change_load(mode){
	var sel_date = get_selected_date();
	var arr_sel_date = sel_date.split("-"); //ymd
	sel_date = arr_sel_date[1]+"-"+arr_sel_date[2]+"-"+arr_sel_date[0];
	day_sel_date = arr_sel_date[0]+"-"+arr_sel_date[1]+"-"+arr_sel_date[2];
	
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();	
	if(mode == "day"){
		//alert("load day appt shedule based on these facilities...");
		$.ajax({
			url: "get_day_name.php?load_dt="+day_sel_date,
			success: function(day_name){
				//loading scheduler
				load_calendar(day_sel_date, day_name, '', false);
				datesRange='';
				collect_labels_by_provider();						
								
			}
		});
	}else if(mode == "week"){
		load_week_appt_schedule();
		//top.fmain.document.location.href = "base_week_scheduler.php?sel_date="+sel_date+"&sel_fac="+sel_fac+"&sel_pro="+sel_pro;
	}else if(mode == "month"){
		top.fmain.document.location.href = "base_month_scheduler.php?sel_date="+sel_date+"&sel_fac="+sel_fac+"&sel_pro="+sel_pro;
	}
}

/*
Function: pro_change_load
Purpose: Combined actions to be performed when loading scheduler base on change in provider
Author: AA
*/
function pro_change_load(mode){	
	var sel_date = get_selected_date();

	var arr_sel_date = sel_date.split("-"); //ymd
	sel_date = arr_sel_date[1]+"-"+arr_sel_date[2]+"-"+arr_sel_date[0];
	day_sel_date = arr_sel_date[0]+"-"+arr_sel_date[1]+"-"+arr_sel_date[2];
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	if(mode == "day"){
		//alert("load day appt shedule based on these providers...");
		$.ajax({
			url: "get_day_name.php?load_dt="+day_sel_date,
			success: function(day_name){
				//loading scheduler
				load_calendar(day_sel_date, day_name, '', false);
				datesRange='';
				collect_labels_by_provider();						
			}
		});
	}else if(mode == "week"){
		load_week_appt_schedule();
		//top.fmain.document.location.href = "base_week_scheduler.php?sel_date="+sel_date+"&sel_fac="+sel_fac+"&sel_pro="+sel_pro;
	}else if(mode == "month"){
		top.fmain.document.location.href = "base_month_scheduler.php?sel_date="+sel_date+"&sel_fac="+sel_fac+"&sel_pro="+sel_pro;
	}	
}

function load_week_appt_schedule(selected_sess_facs, selected_sess_prov){
	if(!selected_sess_prov) selected_sess_prov = "";
	//alert(selected_sess_prov);
	//show loading image
	top.show_loading_image("show");
	
	var sel_date = get_selected_date();
	if(selected_sess_prov != ""){
		var sel_pro_month = selected_sess_prov;
	}else{
		var sel_pro_month = selectedValuesStr("sel_pro_month");
	}
	if(selected_sess_facs){
		var facilities = selected_sess_facs;
	}else{
		var facilities = selectedValuesStr("facilities");
	}	
	//alert("appt_week_load.php?dt="+sel_date+"&sel_pro_month="+sel_pro_month+"&facilities="+facilities);

	$.ajax({
		url: "appt_week_load.php?dt="+sel_date+"&sel_pro_month="+sel_pro_month+"&facilities="+facilities,
		success: function(resp){
			//alert(resp);
			var arr_resp = resp.split("~~~~~");
			//loading week scheduler
			$("#scroll_controls").hide();
			document.getElementById("week_save").innerHTML = arr_resp[0];
			//alert(arr_resp[1]);
			if(arr_resp[1]){
			$("#scroll_controls").css('display','inline-block');
				
				var dateObj = new Date();
				var cur_hr = dateObj.getHours();
				
				image_replace();
			}
			//show loading image
			top.show_loading_image("hide");
			//change main div width for horizental scrolling
			$('#week_save').css({
					'width':parseInt(window.screen.availWidth)-20
				});
		}
	});
}

function to_do(JS_SCHEDULER_VERSION){
	var parentWid = parent.document.body.clientWidth;
	var parenthei = parent.document.body.clientHeight;
	var file_path='../'+JS_SCHEDULER_VERSION+'/to_do_first_avai.php';
	if($("#global_apptact").val() == "reschedule")
	{
		hide_tool_tip();		
		change_status('201');
		file_path='../'+JS_SCHEDULER_VERSION+'/to_do.php';
	}
	window.open(file_path,'to_do','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width='+parseInt(parentWid-50)+'px,height=780px,left=10px,top=100px');			
}

function appt_cancel_portal(JS_SCHEDULER_VERSION){
	var parentWid = parent.document.body.clientWidth;
	var parenthei = parent.document.body.clientHeight;
	var file_path='../'+JS_SCHEDULER_VERSION+'/appt_cancel_portal.php?apptload=1';
	/*
	if($("#global_apptact").val() == "reschedule")
	{
		//hide_tool_tip();		
		//change_status('201');
		//file_path='../'+JS_SCHEDULER_VERSION+'/appt_cancel_portal.php';
	}
	*/
	window.open(file_path,'appt_cancel_portal','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width='+parseInt(parentWid-50)+'px,height=780px,left=10px,top=100px');			
}

function change_date(mode, load_this_date){
	if(!mode) mode = "current";
	if(!load_this_date) load_this_date = "";	
	
	$("#month_h_disable").css("display", "block");

	var sel_date = get_selected_date();
	var arr_sel_date = sel_date.split("-"); //ymd
	sel_date = arr_sel_date[1]+"-"+arr_sel_date[2]+"-"+arr_sel_date[0];

	if(mode == "this_date"){
		//alert(load_this_date);
		var arr_temp = load_this_date.split("|");
		//alert(arr_temp[0]);
		var arr_load_this_dt = arr_temp[0].split("-");		
		var load_this_dt = arr_sel_date[1];
		//alert(load_this_dt);
		if(arr_sel_date[2] > 28 && arr_load_this_dt[0] == 2){
			load_this_dt = 28;
		}		
		load_this_date = arr_load_this_dt[2]+"-"+arr_load_this_dt[0]+"-"+load_this_dt;
		//alert(load_this_date);
	}

	var inc_dec_no = document.getElementById("jmpto").value;
	var inc_dec_mode = document.getElementById("op_typ").value;
	//alert("get_change_date.php?load_dt="+sel_date+"&load_dt_mode="+mode+"&inc_dec_no="+inc_dec_no+"&inc_dec_mode="+inc_dec_mode+"&load_this_date="+load_this_date);
	$.ajax({
		url: "get_change_date.php?load_dt="+sel_date+"&load_dt_mode="+mode+"&inc_dec_no="+inc_dec_no+"&inc_dec_mode="+inc_dec_mode+"&load_this_date="+load_this_date,
		success: function(resp){
			
			var arr_resp = resp.split(";;");
			var load_dt = arr_resp[0];
			var load_day = arr_resp[1];
			
			var dd_option = arr_resp[2];
			
			//if(document.getElementById("sel_month_year_container")){
			//	document.getElementById("sel_month_year_container").innerHTML = "<select id=\"sel_month_year\" name=\"sel_month_year\" onChange=\"change_date('this_date', this.value);\" >"+dd_option+"</select>";
			//}
			
			//loading scheduler
			//alert(load_dt+", "+load_day);
			load_calendar(load_dt, load_day, '', false);
		}
	});
}

/*
Function: fresh_load
Purpose: Combined actions to be performed when loading scheduler base
Author: AA
*/
function fresh_load(load_dt, pt_id, selected_sess_facs, selected_sess_prov){
	
	if(!load_dt) load_dt = "";
	if(!pt_id) pt_id = "";
	if(!selected_sess_facs) selected_sess_facs = "";
	if(!selected_sess_prov) selected_sess_prov = "";
	
	if(load_dt != ""){

		//scheduler has been opened
		top.$('#appt_scheduler_status').val('loaded');
		
		//setting patient id if any
		document.getElementById("global_ptid").value = pt_id;

		//selecting facility from session, if any
		set_facilities(selected_sess_facs);

		//selecting providers from session, if any
		set_providers(selected_sess_prov);

		//hide buttons
		//top.btn_show();--------------------commented

		//refresh title bar
		//top.refresh_control_panel("Patient_Info",pt_id);--------------------commented

		//todo button
		if(top.document.getElementById("tl_2to")){
			top.document.getElementById("tl_2to").style.display = 'block';
		}

		$.ajax({
			url: "get_day_name.php?load_dt="+load_dt,
			success: function(day_name){
				//hide loading image
				top.show_loading_image("hide");

				//loading scheduler
				load_calendar(load_dt, day_name);
			}
		});
	}else{
		top.fAlert("Invalid date.");
		return false;
	}
}

/*
Function: get_selected_facilities
Purpose: to get selectd facilites by the user
Author: AA
*/
function get_selected_facilities(){
	if($("#facilities").val()){
		return ($("#facilities").val()).join(",");
	}else return false;
}

/*
Function: set_facilities
Purpose: to select facilites
Author: AA
*/
function set_facilities(selected_sess_facs){
	if(!selected_sess_facs) selected_sess_facs = "";
	var selectbox = document.getElementById('facilities');
	if(selected_sess_facs != ""){
		var arr_selected_sess_facs = selected_sess_facs.split(",");
		for ( var i = 0, l = selectbox.options.length, o; i < l; i++ ){ 
			o = selectbox.options[i];
			var bl_fac_add = false;
			for(var j = 0; j < arr_selected_sess_facs.length; j++){
				if(o.value == arr_selected_sess_facs[j]){
					bl_fac_add = true;
					break;
				}
			}
			if(bl_fac_add == true){
				o.selected = true;
			}
		}
	}else{
		for ( var i = 0, l = selectbox.options.length, o; i < l; i++ ){  
			o = selectbox.options[i];  
			o.selected = true;
		}
	}
}

/*
Function: get_selected_providers
Purpose: to get selectd providers by the user
Author: AA
*/
function get_selected_providers(){
	return selectedValuesStr("sel_pro_month");
}

/*
Function: get_selected_providers
Purpose: to get selectd providers by the user
Author: AA
*/
function selectedValuesStr(div_id){
	if($("#"+div_id).val()){
	return ($("#"+div_id).val()).join(",");
	}else return false;
}

/*
Function: set_providers
Purpose: to select providers
Author: AA
*/
function set_providers(selected_sess_prov){
	var selectbox2 = document.getElementById('sel_pro_month');
	var selectbox_l = document.getElementById('provider_label');
	if(selected_sess_prov != ""){
		var arr_selected_sess_prov = selected_sess_prov.split(",");
		$("#sel_pro_month option").each(function(id,elem){
			var value = $(elem).val();
			if(value.length > 0 || typeof(value) != 'undefined'){
				if($.inArray(value,arr_selected_sess_prov)!=-1){
					$(elem).prop('selected',true);
				}
			}
		});
		//$("#sel_pro_month").selectpicker("val",array);
		$("#sel_pro_month").selectpicker("refresh");

		/*for(var j = 0; j < arr_selected_sess_prov.length; j++){
			$('select[id=sel_pro_month]').val(arr_selected_sess_prov[j]);
			$("#sel_pro_month").selectpicker("refresh");
		}*/
	
	}else if(selected_sess_prov == "-1"){
		//null
	}else{
		for ( var i = 0, l = selectbox2.options.length, o; i < l; i++ ){  
			o = selectbox2.options[i]; 
			ol=selectbox_l.options[i]; 
			o.selected = true;
			ol.selected = true;
		}
	}
	$("#sel_pro_month").selectpicker("refresh");
}

/*
Function: get_selected_date
Purpose: to get selectd date by the user
Author: AA
*/
function get_selected_date(){
	var dt = document.getElementById("global_date").value;
	var mn = document.getElementById("global_month").value;
	var yr = document.getElementById("global_year").value;	
	
	//adjustment for feb month
	var adj_dt = ((yr % 4) == 0) ? 29 : 28;
	if(dt > adj_dt && mn == 2){
		dt = adj_dt;
	}
	return yr+"-"+mn+"-"+dt;
}

/*
Function: set_date
Purpose: to set global date 
Author: AA
*/
function set_date(yr, mn, dt){
	document.getElementById("global_year").value = yr;
	document.getElementById("global_month").value = mn;
	document.getElementById("global_date").value = dt;	
}

function get_dt_div_obj_nm(dt, mn){
	var returnval = "dtblk-fl-cl_hili-curr_"+parseInt(dt)+"_"+parseInt(mn);
	if($("#"+returnval).get(0)){
		return returnval; 
	}
	var returnval = "dtblk-fl-cl_s_d-curr_"+parseInt(dt)+"_"+parseInt(mn);
	if($("#"+returnval).get(0)){
		return returnval; 
	}
	var returnval = "dtblk-fl-cl_d_d-curr_"+parseInt(dt)+"_"+parseInt(mn);
	if($("#"+returnval).get(0)){
		return returnval;
	}
}

/*
Function: load_calendar
Purpose: to load calendar
Author: AA
Arguments: load_dt - Y-m-d format
*/
function load_calendar(load_dt, day_name, load_appt, showAlert, int_appt){	
	if(!load_appt) load_appt = "";
	if(!int_appt) int_appt = "";
	//alert(int_appt);
	if(typeof(showAlert) == "undefined") showAlert = true;
	
	//show loading image
	top.show_loading_image("show");
	
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	
	//getting month
	var str_sel_date = $("#sel_month_year").val()
	var arr_sel_date = str_sel_date.split("-");
	
	//getting date to deselect
	var str_hg_date = get_selected_date();
	var arr_hg_date = str_hg_date.split("-");
	var hl_box_id = get_dt_div_obj_nm(arr_hg_date[2], arr_hg_date[1]);
	
	if(hl_box_id){
		var arr_hl_box_id = hl_box_id.split("-");
		if(document.getElementById(hl_box_id)){
			document.getElementById(hl_box_id).className = arr_hl_box_id[1]+" "+arr_hl_box_id[2];
		}
	}

	//setting date
	var arr_date = load_dt.split("-");

	//getting selectd month
	var curr_month_val = $("#loaded_first_month").val();
	var arr_curr_month_val = curr_month_val.split("-");
	var curr_month = parseInt(arr_curr_month_val[1]);
	var curr_year = parseInt(arr_curr_month_val[0]);

	set_date(arr_date[0], arr_date[1], arr_date[2]);
	//alert("get_cal_month_view.php?sel_dat="+load_dt+"&sel_pro="+sel_pro+"&sel_fac="+sel_fac+"&curr_month="+curr_month+"&curr_year="+curr_year);
	$.ajax({
		url: "get_cal_month_view.php?int_appt="+int_appt+"&sel_dat="+load_dt+"&sel_pro="+sel_pro+"&sel_fac="+sel_fac+"&curr_month="+curr_month+"&curr_year="+curr_year,
		success: function(resp){
			//alert(resp);
			var arr_resp = resp.split("~~~~~");

			if($.trim(arr_resp[0]) != "nonono"){
				//alert("here");
				document.getElementById("month_h").innerHTML = arr_resp[0];
				$("#loaded_first_month").val(arr_resp[1]);
			}else{
				//alert("there");
			}
			
			//loading nav bar
			load_navigation_bar(day_name);
			
			//highlighting selected date
			var hl_box_id = get_dt_div_obj_nm(arr_date[2], arr_date[1]);
			//var hl_box_id = "dtblk_curr_"+parseInt(arr_date[2])+"_"+parseInt(arr_date[1]);
			if(document.getElementById(hl_box_id)){
				document.getElementById(hl_box_id).className = "fl cl_hili";
			}

			//highlighting date on based on provider schedules
			var arr_sel_pro = "";
			if(sel_pro){arr_sel_pro =sel_pro.split(",");}
			
			//adding new funtion to exclude testing providers -------------------
			
			// code for selection of dates by single provider
			if(arr_sel_pro.length == 1){
				if(arr_sel_pro[0] != ""){
					highlight_provider_schedules(arr_sel_pro[0]);
				}else{
					reset_highlighted_schedules();
				}
			}else{
				var uri='validateProviders.php?p='+sel_pro;
			
			$.ajax({
					url:uri,
					complete:function(respData){
					no_of_providers= respData.responseText;
					var no_of_providers_arr=no_of_providers.split(',');
			
					if(no_of_providers_arr.length == 2)
					{
						if(no_of_providers_arr[0] != "" && no_of_providers_arr[1] != "")
						{
							highlight_provider_schedules(no_of_providers_arr);
						}else{
							reset_highlighted_schedules();
						}
					}
					else
					{
						reset_highlighted_schedules();
					}
				}
			});	
			}
			
			
			
			
			//-------------------------------------------------------------------
	
            // code for selection of dates by single provider
			
			//--- THIS CODE IS COMMENTED AO APPLY NEW SINGLE + ONE OR MANY TESTING PROVIDERS-------
			
			/*if(arr_sel_pro.length == 1)
			{
				if(arr_sel_pro[0] != "")
				{
					highlight_provider_schedules(arr_sel_pro[0]);
				}else{
					reset_highlighted_schedules();
				}
			}
			else
			{
				reset_highlighted_schedules();
			}*/
			//--------------------------------------------------------------------------------------
			
			
			// code for selection of dates by multiple provider
           /*                                    
			if(arr_sel_pro.length > 0){                                                    
                                                    highlight_provider_schedules(arr_sel_pro);
                                                           
			}else{
				reset_highlighted_schedules();
			}
			*/
			if(load_appt == ""){
				//loading appt schedule
				load_appt_schedule(load_dt, day_name, '', '', showAlert);
			}else{
				top.show_loading_image("hide");
			}

		}
	});
        
}

function show_schedule_details(obj_name, e){
	var sch_details = $("#"+obj_name).html();
	
	if(!e) e = window.event || event;
	else e = e || window.event || event;
	
	eve_obj=e;
	
	if(sch_details != ""){
		$("#show_highlighted_prov_sch").html(sch_details);
		display_block_none("show_highlighted_prov_sch", "block");
		//document.getElementById("show_highlighted_prov_sch").style.width = 90;
		document.getElementById("show_highlighted_prov_sch").style.position = 'absolute';
		document.getElementById("show_highlighted_prov_sch").style.display = 'block';
		document.getElementById("show_highlighted_prov_sch").style.zIndex = 999;
		document.getElementById("show_highlighted_prov_sch").style.pixelLeft = eve_obj.clientX + 25;		
		document.getElementById("show_highlighted_prov_sch").style.pixelTop = eve_obj.clientY + 25;
		
		var bro_ver=navigator.userAgent.toLowerCase();
		//if browser is crhome or firfox or safari then we need to placement issue
		if(bro_ver.search("chrome")>1 || bro_ver.search("firefox")>1){
			$("#show_highlighted_prov_sch").css({"display":"inline-block",top: eve_obj.clientY+25, left: eve_obj.clientX+25});
			
		}
		
		
	}else{
		hide_schedule_details();
	}
}

function hide_schedule_details(){
	$("#show_highlighted_prov_sch").html("");
	display_block_none("show_highlighted_prov_sch", "none");
}

function reset_highlighted_schedules(){
	var working_day_dt = get_selected_date();
	var arr_w_dt = working_day_dt.split("-");
	var loaded_month = $("#loaded_first_month").val();
	
	var uri = "reset_highlighted_schedules.php?loaded_month="+loaded_month;
	//alert(uri);

	$.ajax({
		url: uri,
		success: function(resp){
			
			var arr_resp = resp.split(":~:~:");

			for(divs = 0; divs < arr_resp.length - 1; divs++){
				var str_this_div = arr_resp[divs];
				var arr_this_div = str_this_div.split("~~~");
				var arr_t_dt = arr_this_div[2].split("_");
				//alert(arr_this_div[0]+ " " +arr_this_div[1] + " " + arr_this_div[2] + " " + arr_this_div[3]);
				//alert(parseInt(arr_w_dt[0])+" == "+parseInt(arr_t_dt[1])+" && "+parseInt(arr_w_dt[1])+" == "+parseInt(arr_t_dt[2]));
				if(parseInt(arr_w_dt[2]) == parseInt(arr_t_dt[1]) && parseInt(arr_w_dt[1]) == parseInt(arr_t_dt[2])){
					//alert("thenga");
				}else{
					if($("#"+arr_this_div[0]).get(0)){
						//alert(arr_this_div[0]);
						document.getElementById(arr_this_div[0]).className = "fl cl_s_d";
					}
					if($("#"+arr_this_div[1]).get(0)){
						//alert(arr_this_div[1]);
						document.getElementById(arr_this_div[1]).className = "fl cl_d_d";
					}
					
					//checking last month dates
					var last_normal = arr_this_div[0].replace("curr", "last");
					//checking for default class
					var last_normal_plus = last_normal.replace("cl_s_d", "cl_p_d");
					
					if($("#"+last_normal_plus).get(0)){
						document.getElementById(last_normal_plus).className = "fl cl_d_d";
					}
				}
				
				//overwrite color if we do have facility color value
				if($("#"+arr_this_div[0]).get(0))
				$("#"+arr_this_div[0]).css("background-color", '');
				
				if($("#"+arr_this_div[1]).get(0))
				$("#"+arr_this_div[1]).css("background-color", '');
				
				if($("#"+last_normal_plus).get(0))
				$("#"+last_normal_plus).css("background-color", '');
						
				document.getElementById(arr_this_div[2]).innerHTML = "";
			}
			$("#month_h_disable").css("display", "none");
            highLightDatesByLabels();    		                                
		}
	});
}

/*
Function: highlight_provider_schedules
Purpose: to highlight dates with purple color based on provider schdules
Author: AA
*/
function highlight_provider_schedules(sel_pro){
	
	var working_day_dt = get_selected_date();
	var arr_w_dt = working_day_dt.split("-");
	var loca = get_selected_facilities();
	var loaded_month = $("#loaded_first_month").val();
	var uri = "highlight_provider_schedules.php?working_day_dt="+working_day_dt+"&loca="+loca+"&prov_id="+sel_pro+"&loaded_month="+loaded_month;
	
	$.ajax({
		url: uri,
		success: function(resp){
			var arr_resp = resp.split(":~:~:");
			
			if(arr_resp.length>1)
			{
			for(divs = 0; divs < arr_resp.length - 1; divs++){
				var str_this_div = arr_resp[divs];
				var arr_this_div = str_this_div.split("~~~");
				var arr_t_dt = arr_this_div[2].split("_");
				//alert(arr_this_div[0]+ " " +arr_this_div[1] + " " + arr_this_div[2] + " " + arr_this_div[3]);
				//alert(parseInt(arr_w_dt[0])+" == "+parseInt(arr_t_dt[1])+" && "+parseInt(arr_w_dt[1])+" == "+parseInt(arr_t_dt[2]));
				if(parseInt(arr_w_dt[2]) == parseInt(arr_t_dt[1]) && parseInt(arr_w_dt[1]) == parseInt(arr_t_dt[2])){
					//alert("thenga");
				}else{
                                    
					var detail_div_name = "";
					if($("#"+arr_this_div[0]).get(0)){
						//alert(arr_this_div[0]);
						document.getElementById(arr_this_div[0]).className = "fl cl_s_d";
						detail_div_name = arr_this_div[2];
					}
					if($("#"+arr_this_div[1]).get(0)){
						//alert(arr_this_div[1]);
						document.getElementById(arr_this_div[1]).className = "fl cl_d_d";
						detail_div_name = arr_this_div[2];
					}

					//checking last month dates
					var last_normal = arr_this_div[0].replace("curr", "last");
					var last_special = arr_this_div[1].replace("curr", "last");
					
					//checking for default class for last month
					var last_normal_plus = last_normal.replace("cl_s_d", "cl_p_d");
					
					if($("#"+last_normal_plus).get(0)){
						document.getElementById(last_normal_plus).className = "fl cl_d_d";//overwrite color if we do have facility color value
						if(arr_this_div[5])
						{
							$("#"+last_normal_plus).css("background-color", arr_this_div[5]);
						}
					}
						
					if($("#"+last_normal).get(0)){
						//alert(arr_this_div[0]);
						document.getElementById(arr_this_div[0]).className = "fl cl_s_d";
						detail_div_name = arr_this_div[2].replace("curr", "last");
					}
					if($("#"+last_special).get(0)){
						//alert(arr_this_div[1]);
						document.getElementById(arr_this_div[1]).className = "fl cl_d_d";
						detail_div_name = arr_this_div[2].replace("curr", "last");
					}
	
					//checking next month dates
					var next_normal = arr_this_div[0].replace("curr", "next");
					var next_special = arr_this_div[1].replace("curr", "next");
					if($("#"+next_normal).get(0)){
						//alert(arr_this_div[0]);
						document.getElementById(next_normal).className = "fl cl_s_d";
						if(arr_this_div[5])
						{
							$("#"+next_normal).css("background-color", arr_this_div[5]);
						}
						detail_div_name = arr_this_div[2].replace("curr", "next");
					}
					if($("#"+next_special).get(0)){
						//alert(arr_this_div[1]);
						document.getElementById(next_special).className = "fl cl_d_d";
						if(arr_this_div[5])
						{
							$("#"+next_special).css("background-color", arr_this_div[5]);
						}
						detail_div_name = arr_this_div[2].replace("curr", "next");
					}
                                        
				}
				if(detail_div_name != "")
				$('#'+detail_div_name).html('');
					// document.getElementById(detail_div_name).innerHTML = "";
				
				//overwrite color if we do have facility color value
				if(arr_this_div[5])
				{
					if($("#"+arr_this_div[0]).get(0))$("#"+arr_this_div[0]).css("background-color", arr_this_div[5]);
					if($("#"+arr_this_div[1]).get(0))$("#"+arr_this_div[1]).css("background-color", arr_this_div[5]);
					if($("#"+arr_this_div[2]).get(0))$("#"+arr_this_div[2]).css("background-color", arr_this_div[5]);
					
				}
			}
			
			for(divs = 0; divs < arr_resp.length - 1; divs++){
				var str_this_div = arr_resp[divs];
				var arr_this_div = str_this_div.split("~~~");
				var arr_t_dt = arr_this_div[2].split("_");
				var show_ob_alert = arr_this_div[4];
				if(arr_this_div[4] == "default"){
					var set_class = "fl cl_h_d";
				}else if(arr_this_div[4] == "exceed_appt"){
					var set_class ="fl cl_exceed_appt_d";
				}else{
					var set_class = "fl cl_a_d";
				}
				//alert(arr_this_div[0]+ " " +arr_this_div[1] + " " + arr_this_div[2] + " " + arr_this_div[3] + " " + arr_this_div[4]);
				//alert(parseInt(arr_w_dt[0])+" == "+parseInt(arr_t_dt[1])+" && "+parseInt(arr_w_dt[1])+" == "+parseInt(arr_t_dt[2]));
				if(parseInt(arr_w_dt[2]) == parseInt(arr_t_dt[1]) && parseInt(arr_w_dt[1]) == parseInt(arr_t_dt[2])){
					//alert("thenga");
				}else{
					if($("#"+arr_this_div[0]).get(0) && arr_this_div[3] != ""){
						document.getElementById(arr_this_div[0]).className = set_class;                                                                                                
					}
					if($("#"+arr_this_div[1]).get(0) && arr_this_div[3] != ""){
						document.getElementById(arr_this_div[1]).className = set_class;
					}
					
					//-------------------------------------------------------------------
					//checking last month dates - Highlight previous month dates
					var last_normal = arr_this_div[0].replace("curr", "last");
					//checking for default class
					var last_normal_plus = last_normal.replace("cl_s_d", "cl_p_d");
					
					if($("#"+last_normal_plus).get(0) && arr_this_div[3] != ""){
						document.getElementById(last_normal_plus).className = set_class;
					}
					//-------------------------------------------------------------------
					
					//overwrite color if we do have facility color value
					if(arr_this_div[5])
					{
						if($("#"+arr_this_div[0]).get(0))
						{
							$("#"+arr_this_div[0]).css("background-color", arr_this_div[5]);
						}
					}
					
				}
				document.getElementById(arr_this_div[2]).innerHTML = arr_this_div[3];
				
				
			}
                                                                        
			$("#month_h_disable").css("display", "none");

			//hLDatesByLbl(); commented on 5 sep as front end interface related to this is already removed.
			}
			else
			{
				reset_highlighted_schedules();	
			}
			/*
			use this code if the slow speed issue come
			cur_first_mnth_in_sel = $('.cl_m_h:first').html();		
			if(cur_first_month == '' || cur_first_mnth_in_sel != cur_first_month)
			{
				cur_first_month = cur_first_mnth_in_sel;
				hLDatesByLbl();  				                                 				
			}
			else
			{
				highLightDatesByLabels();
			}			
			// call function highLightDatesByLabels() only in these cases in which the month is not modified
			*/
		}
                
	});
}

function highlight_date(obj_name, cls_name,e){
        /*
	if(document.getElementById(obj_name)){
		//alert("here");
		var get_date_from_name = obj_name.split("-");
		var day_mon = get_date_from_name[3].split("_");
		var load_dt = day_mon[1];
		var load_mn = day_mon[2];

		var loaded_dt = get_selected_date();
		var arr_loaded_dt = loaded_dt.split("-");

		//alert(get_date_from_name+" "+day_mon+" "+load_dt+" == "+arr_loaded_dt[2]+" && "+load_mn+" == "+arr_loaded_dt[1]);
		
		if(parseInt(load_dt) == parseInt(arr_loaded_dt[2]) && parseInt(load_mn) == parseInt(arr_loaded_dt[1])){
			//thenga
		}else{
			//alert(document.getElementById(obj_name).className);
			if(document.getElementById("loaded_cls").value != ""){// && obj_name == document.getElementById("loaded_cls_obj").value){
				//alert("OBJECT NAME: "+obj_name+"; CLASS TO SET: "+document.getElementById("loaded_cls").value+"; EXISTING CLASS: "+document.getElementById(obj_name).className+"; STATUS: good girl");
				document.getElementById(obj_name).className = document.getElementById("loaded_cls").value;
				document.getElementById("loaded_cls").value = "";
			}else{
				//alert("OBJECT NAME: "+obj_name+"; CLASS TO SET: "+document.getElementById("loaded_cls").value+"; EXISTING CLASS: "+document.getElementById(obj_name).className+"; STATUS: bad girl");
				//alert(document.getElementById(obj_name).className);
				if(document.getElementById(obj_name).className == "fl cl_h_d" || document.getElementById(obj_name).className == "fl cl_a_d"){
					document.getElementById("loaded_cls").value = document.getElementById(obj_name).className;
					//document.getElementById("loaded_cls_obj").value = obj_name;
				}
				document.getElementById(obj_name).className = cls_name;
			}
		}
	}
    */
   
}

/*
Function: load_appt_schedule
Purpose: to load appt templates
Author: AA
*/
function load_appt_schedule(load_dt, day_name, appt_id, load_fd, showAlert){
	
	var elemObjAvail = $('#sch_left_portion').parent().css('display');
	var max_slides=1
	if(elemObjAvail=='block'){max_slides=MAX_SCHEDULE_PER_SLIDE;}
	else{max_slides=9;}
	
	if(!appt_id) appt_id = "";
	if(!load_fd) load_fd = "";
	if(typeof(showAlert) == "undefined") showAlert = true;

	//alert(load_dt+", "+day_name+", "+appt_id+", "+load_fd);
	
	//show loading image
	top.show_loading_image("show");
	

	//loading image in template section
	//document.getElementById("day_save").innerHTML = '<div class="sc_appt_loader sc_title_font">Loading...</div>';

	//tapping vars
	if(!appt_id) var appt_id = "";

	if(load_dt){
		var arr_load_dt = load_dt.split("-");
		set_date(arr_load_dt[0], arr_load_dt[1], arr_load_dt[2]);
	}else{
		load_dt = get_selected_date();
	}

	var arr_load_dt = load_dt.split("-");

	//getting selected facilities & providers
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	
	//setting date in navigation bar
	if(date_format == "mm-dd-yyyy")
	$("#DayNameId").html(day_name+", "+arr_load_dt[1]+"-"+arr_load_dt[2]+"-"+arr_load_dt[0]);
	else if(date_format == "dd-mm-yyyy")
	$("#DayNameId").html(day_name+", "+arr_load_dt[2]+"-"+arr_load_dt[1]+"-"+arr_load_dt[0]);
	else//show default
	$("#DayNameId").html(day_name+", "+arr_load_dt[1]+"-"+arr_load_dt[2]+"-"+arr_load_dt[0]);
	
	//document.getElementById("DayNameId").innerHTML = day_name+', '+arr_load_dt[2]+'-'+arr_load_dt[1]+'-'+arr_load_dt[0];
	var appt_load_url = "appt_load.php?loca="+sel_fac+"&dt="+load_dt+"&prov="+sel_pro+"&appt_id="+appt_id+"&max_slide_user="+max_slides+"&sid="+Math.random();
		
	$.ajax({
		url: appt_load_url,
		success: function(resp){
			//alert(resp);
			var arr_response = resp.split("____");

			//loading front desk
			if(load_fd == ""){
				pre_load_front_desk('', '', showAlert);	
			}

			//hide loading image
			top.show_loading_image("hide");
			
			//hiding provider notes if shonw
			hide_provider_notes();
			
			document.getElementById("day_save").innerHTML = arr_response[0];


			var office_status = arr_response[1];
			$("#hid_prov_count").val(arr_response[2]);
			//document.getElementById("div_summary_common").innerHTML = arr_response[3];
			
			if(office_status == "NOPROVIDER" || office_status == "NOFACILITY" || office_status == "CLOSED"){
				$("#Print_Day_Appt_Link").hide();
				$("#Day_Summary_Link").hide();
				$("#Day_Block").hide();
				
				// auto mode change from expand to collapse/shorten.
				//$('#sch_left_portion').css({'display':'inline-block'});
				$('#sch_left_portion').parent().css({'display':'block'});
				sch_expand_mode = 0;
			}else{
				$('#Print_Day_Appt_Link').show();
				$('#Day_Summary_Link').show();
				$("#Day_Block").show();
				//if($("#global_admin").val() == "1"){
					//$('#Day_Block').css({'display':'inline-block'});
				//}
				$("#scroll_controls").css('display','inline-block');
				
				var dateObj = new Date();
				var cur_hr = dateObj.getHours();
				//enable disable scroll buttons
				manage_slide_buttons();
				//set number of schedule to show per scroll according to physician schedule coming
				if(arr_response[4]==1)
				{
					item1=1;
					item2=1;
					item3=1;	
				}else if(arr_response[4]==2)
				{
					item1=1;
					item2=2;
					item3=2;	
				}else if(arr_response[4]>=3)
				{
					item1=1;
					item2=2;
					item3=3;	
				}
				/*//initialize new scroll object using OWL script
				if(arr_response[4]>1)//initialize only in case of more than one provider
				{
				owl = $('.owl-carousel');
				  owl.owlCarousel({
					loop:true,
					margin:0,
					navText:false,
					nav:false,
					dots:false,
					responsive: {
					  0: {
						items: item1
					  },
					  600: {
						items: item2
					  },
					  1000: {
						items: item3
					  }
					}
				  })
				}else
				{
					//remove owl class from scheduler
					$("#owl-carousel_div").removeClass('owl-carousel');	
					//disable button for owl scrolling
					$("#scroll_control1").attr("disabled",true);
					$("#scroll_control2").attr("disabled",true);
				}
				  */

				//day start time
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
						//alert(day_st_tm + " > " + last_logged_st_time);
						if(day_st_tm < last_logged_st_time){

							last_logged_st_time = last_logged_st_time - day_st_tm;
							var global_time_slot = $("#global_time_slot").val();
							var slot_height = (11 * (global_time_slot / 5))*2;
							
							if(global_time_slot==5)slot_height+=10;
							else if(global_time_slot==15)slot_height-=18;
							else if(global_time_slot==30)slot_height-=40;
							
							var scroll_px = (60 / global_time_slot) * last_logged_st_time * slot_height;

							//taking minutes into consideration
							var last_logged_st_min = parseInt(arr_last_logged_time[1],10);
							var min_slots = Math.ceil(last_logged_st_min / global_time_slot);
							scroll_px += (min_slots * slot_height); 
							
							// top offset setting							
							var top_min_gap = parseInt($('#scroll_tim_limit4').val(),10);
							if(top_min_gap > 0)
							{						
								var top_min_gap_offset_min = 60 - top_min_gap;
								var top_min_gap_offset_val = Math.ceil(top_min_gap_offset_min/global_time_slot);
								scroll_px -= (top_min_gap_offset_val * slot_height);
							}
							//alert(scroll_px);
							var t = setTimeout("setMaxY('"+scroll_px+"')", 2);
						}
					}
				}
				
				//show appt edit images
				image_replace();

				prov_sch_sel_load_str = $('#prov_sch_sel_load').val();
				if(sch_expand_mode == 1)
				{	
					$('#sch_left_portion').parent().css({'display':'none'});
					
				}
			}                       
		}
	});
}

//to set the scroll parameters
function setMaxY(mY){
	if(document.getElementById('mn1_1')){
		abcTest = document.getElementById('mn1_1');
		abcTest.scrollTop = mY;
	}
	return true;
}

/* REMOTE SYNC - FUNCTIONS */
function set_parent_on_master(pid, appt_id){
	top.show_loading_image("show");
	$.ajax({
		url: '../../remote_sync/patient_parent_set.php?mode=set_parent_server&patient='+pid,
		dataType: 'text',
		complete: function(r){
			parent_id = r;//alert(r.responseText);return;
			if(parent_id != "" && parent_id != 0){				
				top.show_loading_image("hide");
				chk_pt_access_before_load_fd(pid, appt_id);
			}
		}
	});
}
function get_parent_data_frm_master(pid, appt_id){
	top.show_loading_image("show");
	$.ajax({
		url: '../../remote_sync/patient_parent_set.php?mode=get_parent_data_frm_master&patient='+pid,
		dataType: 'text',
		complete: function(r){
			parent_id = r;//alert(r.responseText);return;
			if(parent_id != "" && parent_id != 0){				
				top.show_loading_image("hide");
				chk_pt_access_before_load_fd(pid, appt_id);
			}
		}
	});
}
function load_rm_patient(pid,parent_id,appt_id){
	if(parent_id == "" || parent_id == "0"){
		top.fancyConfirm("Patient master server not defined or incorrect. Do you want to set current server as parent","", "window.top.fmain.set_parent_on_master('"+pid+"', '"+appt_id+"')","window.top.fmain.get_parent_data_frm_master('"+pid+"', '"+appt_id+"')")
	}
}						

/*
Function: pre_load_front_desk
Purpose: to perform pre load tasks before loading front desk like restricted provider access
Author: AA
*/
var local_pat_data_rm = '';
function pre_load_front_desk(pat_id, appt_id, showAlert){
	local_pat_data_rm = '';	
	if(!pat_id) pat_id = "";
	if(!appt_id) appt_id = "";
	if(typeof(showAlert) == "undefined") showAlert = true;
	if( typeof top.patient_pop_up !== 'undefined') {top.patient_pop_up = [];}
	
	var loaded_pat_id = $("#global_ptid").val();
	if(loaded_pat_id != "" && pat_id == loaded_pat_id){
		showAlert = false;
	}
	if( pat_id && loaded_pat_id && pat_id != loaded_pat_id ) {
		top.close_popwin();
	}
	if(pat_id == ""){
		var pat_id = document.getElementById("global_ptid").value;
	}

	if(appt_id == "" && document.getElementById("global_apptact").value == "reschedule"){
		var appt_id = document.getElementById("global_apptid").value;
	}
	
	top.show_loading_image("show");	
	
	/*if(gl_remote_sync_status == 1 && pat_id != "")
	{
		$.ajax({
			url : "local_pt_exists.php?pat_id="+pat_id+"&rn="+Math.random(),
			success:function(resp)
			{
				resp = $.trim(resp);
				resp = $.parseJSON(resp);
				var resp_act = resp.load_action;
				if(resp_act == "not_parent_server")
				{
					top.show_loading_image("hide");
					$('#ContextMenu').css({'display':'none'});	
					load_rm_patient(resp.pt_data_arr.pid,0,appt_id);									
				}
				else if(resp_act == "not_found")
				{
					top.fAlert('Remote Patient can not be pulled');
					top.show_loading_image("hide");
					$('#ContextMenu').css({'display':'none'});					
				}
				else
				{
					local_pat_data_rm = resp.pt_data_arr;
					chk_pt_access_before_load_fd(pat_id, appt_id, showAlert);				
				}
			}
		});
	}
	else
	{*/	
		chk_pt_access_before_load_fd(pat_id, appt_id, showAlert);
	/*}*/
}

/*
Function: check the patient access is restricted or not before loading this patient in to the front desk
*/
var prevent_dupli_pt_access_fun = 0;
function chk_pt_access_before_load_fd(pat_id, appt_id, showAlert)
{
	if(isNaN(pat_id) == false && pat_id>0 && prevent_dupli_pt_access_fun!=pat_id){
		$("#fd_pt_controls").html('<div style="height:29px">Updating Controls...</div>');
		prevent_dupli_pt_access_fun = pat_id;
		//check for restricted access
        var lname_val = pat_id;
        var findValArr = document.getElementById("findByShow").value.split(':');
        if(isNaN(findValArr[0])){
            var findBy = findValArr[0];
        }else{
            var findBy = findValArr[2];	
        }
        if(isNaN(lname_val) == true){
            if((lname_val[0].toLowerCase() == "e") && (parseInt(lname_val.substring(1, lname_val.length)) > 0)){
                lname_val = lname_val.substring(1, lname_val.length);
                findBy = "External MRN";
            }
        }
    
        $.ajax({
			url: 'chk_patient_exists.php',
			type: 'POST',
			data: 'pid='+lname_val+'&findBy='+findBy,
			success: function(resultData)
			{
                prevent_dupli_pt_access_fun = 0;
                
                if(resultData.length > 1) resultData = JSON.parse(resultData);

                if(resultData.hasOwnProperty('askForReason')==true)
                {
                    top.show_loading_image("hide");	
                    var patId = resultData.patId;
					var bgPriv = resultData.bgPriv;
                    TestOnMenu();
					top.core_restricted_prov_alert(patId, bgPriv,'','');
                    return false;
                }
                else if(resultData == 'n')
				{
					top.fAlert('Patient not found');	
				}
				else
				{
					pid = eval(resultData);
					load_front_desk(pid, appt_id, showAlert);
				}
            }
        });
        
        //There is no handling done for the below code in "core/index.php" It never passes the if condition and enters the else case.
        //So commented the code to implement the check-restricted-access code in scheduler
        /*
		$.ajax({
			url: "../../interface/core/index.php?pg=check-restricted-access&p_id=" + pat_id + "&resp_type=ajax",
			success: function(resp){
				prevent_dupli_pt_access_fun = 0;
				var arr_resp = resp.split("~~~");

				top.show_loading_image("hide");
				top.close_popwin();
				if(arr_resp[0] == "y"){
					top.core_restricted_prov_alert(arr_resp[1], arr_resp[2], showAlert);
				}else{
					load_front_desk(pat_id, appt_id, showAlert);
				}
			}
		});	
        
        */
	}
}

function update_recent_pt_list(recent_search)
{
	$('ul#main_search_dd').html(recent_search);
}
/*
Function: load_recent_pt_search
Purpose: to update recently searched patients list in front desk and top search panel
Author: AA
*/
function load_recent_pt_search(pat_id){
	/*$.ajax({
		url: "app_get_recent_search.php?pat_id="+pat_id,
		success: function(resp){
			var arr_resp = resp.split("~~~~~~~~~~"); //10 times
			if(top.document.getElementById('homeDropDown')){
				top.document.getElementById('homeDropDown').innerHTML = arr_resp[0];				
			}
			if(top.fmain.document.getElementById('homeDropDownSCH')){
				temp_old_val = $('#txt_patient_app_name').val();
				top.fmain.document.getElementById('homeDropDownSCH').innerHTML = arr_resp[1];
				temp_old_val = $('#txt_patient_app_name').val(temp_old_val);
			}
		}
	});*/
}

function submitFrontInsuraceForm(){
	$('#frmFrontdeskInsurance').unbind('submit');
	$('#frmFrontdeskInsurance').bind('submit',save_insurance);
	$('#frmFrontdeskInsurance').trigger('submit');
}

function save_insurance()
{
	top.show_loading_image("show");
	serialize_data = $(this).serialize();
	$.ajax({
		url: 'insurance_active_case.php',
		type: 'POST',
		data: serialize_data,
		complete : function(respData)
		{
			resultData = respData.responseText;						
			var ap_ins_case_id = ($("#choose_prevcase").length !== 0) ? $("#choose_prevcase").val() : "";
			var pat_id = $('#global_ptid').val();		
			var appt_id = $("#global_apptid").val();
			selected_dt = $('#global_year').val()+'-'+$('#global_month').val()+'-'+$('#global_date').val();
			if($.trim(appt_id)!="" && typeof(appt_id) != "undefined")
			{
				get_copay(pat_id,ap_ins_case_id,appt_id,selected_dt);
			}			
			top.show_loading_image("hide");			
		}
	});
	return false;
}

function reset_pt_data(){
	//collection flag in front desk base file
	$("#collection_flag_space").html('');
	$("#collection_flag_space").css("display","none");
	$("#todo_flag_space").css("display","none");
	$("#AssesmentDiv").css("display","none");
	
	$("#patient_photo_container").html('<img src="../../library/images/ptimage.png" alt=""/> ');	
	//top.document.getElementById("divPtDemographicAlert").style.display = "none";
	//top.document.getElementById("divPtSpecificAlert").style.display = "none";
	document.getElementById("global_ptid").value = "";
}

/*
Function: load_front_desk
Purpose: to load patient in front desk
Author: AA
*/
function load_front_desk(pat_id, sch_id, showAlert){
	if(!pat_id) pat_id = "";
	if(!sch_id) sch_id = "";
	if(typeof(showAlert) == "undefined") showAlert = true;
	//loading front desk
	if(pat_id != ""){
		hidePatientImage();
		//show loading image
		top.show_loading_image("show");
		
		//reset
		reset_pt_data();
		
		//hide if any msg opened for previously loaded patient
		hide_msg_stack();
		
		//hiding add appt trail
		//display_block_none("imageDiv1", "none");

		//hiding appt history & recalls related buttons in front desk base layer
	
		//display_block_none("frontdesk2", "none");
		display_block_none("frontdesk3", "none");

		if(document.getElementById("fd_base_controls")){
			display_block_none("fd_base_controls", "none");
		}

		var sel_date = get_selected_date();
		
		if($("#global_apptid").val() == ""){
			var force_comment = $("#txt_comments").val();
			var force_proc = $("#sel_proc_id").val();
			var force_proc2 = $("#sec_sel_proc_id").val();
			var force_proc3 = $("#ter_sel_proc_id").val();
			var force_pri_site = $("#pri_eye_site").val();
			var force_sec_site = $("#sec_eye_site").val();
			var force_ter_site = $("#ter_eye_site").val();
		}else{
			var force_comment = "";
			var force_proc = "";
			var force_proc2 = "";
			var force_proc3 = "";			
			var force_pri_site = "";
		}
		
		//loading front desk
		var frontdk_url = "frontDeskPatient.php?pat_id="+pat_id+"&sch_id="+sch_id+"&sel_date="+sel_date+"&showAlert="+showAlert+"&force_comment="+force_comment+"&force_proc="+force_proc+"&force_proc2="+force_proc2+"&force_proc3="+force_proc3+"&force_pri_site="+force_pri_site+"&force_sec_site="+force_sec_site+"&force_ter_site="+force_ter_site;
			
		$.ajax({ 
			url: frontdk_url, 
			success: function(resp){
				if(is_remote_server() == true)
				{
					resp = json_resp_handle(resp); 
				}				
				var arr_resp = resp.split("~~~~~~~~~~");

				$("#getImageCross").css("display", "inline-block");
				$("#fd_scan_links").css("display", "inline-block");
				
				//loading content
				document.getElementById("frontdesk").innerHTML = arr_resp[0];							

				$("#global_ptid").val(pat_id);
				if(arr_resp[14] == "-1"){
					arr_resp[14] = "";
				}

				top.fmain.patientDeceased = (arr_resp[38] == 'Deceased') ? true : false;
				
				$("#global_apptid").val(arr_resp[14]);
				$("#global_apptpro").val(arr_resp[19]);
				$('#global_apptsecpro').val(arr_resp[33]);
				$('#global_apptterpro').val(arr_resp[34]);
				$("#global_context_apptid").val(arr_resp[14]);
				$("#global_ptfname").val(arr_resp[1]);
				$("#global_ptmname").val(arr_resp[2]);
				$("#global_ptlname").val(arr_resp[3]);
				$("#global_ptemr").val(arr_resp[4]);
				
				var nickName = arr_resp[39].trim();
				var phoneticName = arr_resp[40].trim();
				var language = arr_resp[41].trim(); 
				
				var Xbtn="<span class=\"top_pt_close\" onclick=\"top.clean_patient_session('scheduler');\" class=\"link_cursor\" title=\"Close Patient\">X</span>";
				//show patient name in top bar
				$("#show_pt_name").html(arr_resp[3]+', '+arr_resp[1]+' '+arr_resp[2]+'-'+pat_id+' '+Xbtn);
				if(nickName.length > 0 || phoneticName.length > 0 || language.length > 0){
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

					$("#show_pt_name").attr("data-toggle", "tooltip");
					$("#show_pt_name").attr("data-html", "true");
					$("#show_pt_name").attr("data-placement", "bottom");
					$("#show_pt_name").attr("data-original-title", nickAndPhoneticName);
					$('[data-toggle="tooltip"]').tooltip();
				}else{
					$("#show_pt_name").attr("data-original-title", '');
				}
				//update recent patient
				update_recent_pt_list(arr_resp[37]);
				
				var ins_case_id = arr_resp[5];
				
				/*-----UPDATING RECENT PATIENTS LIST--*/
				if(typeof(top.update_iconbar)=='function') {top.update_iconbar(); }
				
				$.ajax({ 
					url: "insurance_active_case.php?current_caseids="+ins_case_id, 
					success: function(resp2){
						document.getElementById("load_pt_insurance").innerHTML = resp2;
						if($("#choose_prevcase").val()==0){
							load_insurance(0);
						}
						var tmp_appt_id = parseInt($("#global_context_apptid").val());
						if( !tmp_appt_id ) { chk_referral('load_front_desk'); chk_verif_sheet('load_front_desk'); }
						
						$.ajax({ 
							url: "load_appt_hx.php?pid="+pat_id, 
							success: function(resp3){							
								get_copay(pat_id, ins_case_id, arr_resp[14], arr_resp[20]);
								document.getElementById("load_pt_appointments").innerHTML = resp3;
								$('[data-toggle="tooltip"]').tooltip(); 
							}
						});

					}
				});					
				
				//showing collection flag //arr_resp[1] - collection flag status //arr_resp[2] - collection date sent if any
				if(arr_resp[12] == true){
					if(arr_resp[13] != ""){
						document.getElementById("collection_flag_space").innerHTML = " <img src=\"../../library/images/flag_red_collection.png\" title=\""+arr_resp[13]+"\">";
					}else{
						document.getElementById("collection_flag_space").innerHTML = " <img src=\"../../library/images/flag_red_collection.png\">";
					}
					document.getElementById("collection_flag_space").style.display = "block";
				}
				
				//loading pt image
				var pt_photo_path = arr_resp[6];
				//alert(pt_photo_path);
				if(pt_photo_path != ""){
					$.ajax({ 
						url: "patient_photos.php?path="+pt_photo_path, 
						success: function(ph_resp){
							//alert(ph_resp);
							if(ph_resp != ""){
								//var img = $(ph_resp);
								//var height = $(img).css('height');
								//var width = $(img).css('width');
								//$("#patient_photo_container").css({display:'inline-block', height:height, width:width});
								$("#patient_photo_container").html(ph_resp);
								//$("#patient_photo_container").css("display", "block");
							}else
							{
								$("#patient_photo_container").html('<img src="../../library/images/ptimage.png" alt=""/> ');	
							}
						}
					});
				}else
				{
					$("#patient_photo_container").html('<img src="../../library/images/ptimage.png" alt=""/> ');	
				}

				//show todo flag
				var appt_status = parseInt(arr_resp[7]);
				if(appt_status ==  201){
					document.getElementById("todo_flag_space").style.display = "block";
				}
				
				//show pt demographic alert
				var pt_dg_alert = arr_resp[8];
				if(typeof(top.patient_pop_up)=="undefined"){ top.patient_pop_up=new Array(); }
				if(pt_dg_alert != "" && (jQuery.inArray("divPtDemographicAlertSC", top.patient_pop_up) == "-1")){
					//pt_alert_div += "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_dg_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript:top.fmain.close_alert_box('divPtDemographicAlert');\" /></div>";
					top.fAlert('<div style="max-height:450px;overflow-y:scroll;">'+pt_dg_alert+'</div>', 'imwemr - Patient Notes');
					top.patient_pop_up.push('divPtDemographicAlertSC');
					//alert_box("imwemr - Patient Info Alert", pt_dg_alert, 300, "", 500, 150, "divPtDemographicAlert", false, false);
					
				}
				//remove previous pt alert content
				$( "form",top.fmain.document ).remove(".class_chart_alerts_patient_specific");
				//show pt specific alert
				var pt_sp_alert = arr_resp[9];				
				pt_sp_alert_arr = pt_sp_alert.split('^^^');
				if(pt_sp_alert_arr[0] != ""){
					var pt_sp_alert_div = "";
					pt_sp_alert_div += "<form class=\"class_chart_alerts_patient_specific\" name=\"chart_alerts_patient_specific\" action=\""+top.JS_WEB_ROOT_PATH+"/interface/patient_info/alerts_reason_save.php\" target=\"chart_alerts_patient_specific\" method=\"post\">";
					pt_sp_alert_div += "<div id=\"patSpesificDivAlert\" onmouseover=\"drag_div_move(this, event)\" onMouseDown=\"drag_div_move(this, event)\"; style=\"display:block;  z-index:2000; top:200px; width:400px; left:550px; position:absolute;cursor:move;\" class=\"confirmTable3 panel panel-success\">";							
					pt_sp_alert_div += "<div class=\"boxhead panel-heading\">imwemr - Pt. Alerts</div>";
					var strTemp = top.JS_WEB_ROOT_PATH+"/library/images/confirmYesNo.gif";
					pt_sp_alert_div += "<div clasws=\"panel-body\" style=\"max-height:300px; overflow:hidden; overflow-y:auto;\"><div class=\"row pt10\">";
					pt_sp_alert_div += "<div class=\"col-sm-2 text-center\"><img src=\""+strTemp+"\" alt=\"Confirm\"></div>";
					pt_sp_alert_div += "<div id=\"patientAlertMsg\" class=\"col-sm-10\"><p>"+pt_sp_alert_arr[0]+"</p></div></div>";
					pt_sp_alert_div += "</div>";
					pt_sp_alert_div += "<div class=\"panel-footer text-center\" id=\"module_buttons\">";
					pt_sp_alert_div += "<input type=\"button\" id=\"patAlertDisable\" name=\"patAlertDisable\" value=\"OK\" class=\"btn btn-success\" onClick=\"acknowledged('1', this.form); this.form.submit();\" > ";
					pt_sp_alert_div += "<input type=\"button\" value=\"Remove\" name=\"patAlertAcknowledged\" id=\"patAlertAcknowledged\" class=\"btn btn-danger\" onClick=\"javascript: acknowledged('', this.form); this.form.submit();\" >";			
					pt_sp_alert_div += "</div>";
					pt_sp_alert_div += "</div>";								
					pt_sp_alert_div += "<input type=\"hidden\" name=\"patientSpecificFrm\" value=\"SCH\">";
					pt_sp_alert_div += "<input type=\"hidden\" id=\"disablePatAlertThisSession\" name=\"disablePatAlertThisSession\" >";
					pt_sp_alert_div += "<input type=\"hidden\" name=\"cancel_pt_alert\" id=\"cancel_pt_alert\" value=\""+pt_sp_alert_arr[1]+"\">";
					pt_sp_alert_div += "</form>";
					pt_sp_alert_div += "<iframe name=\"chart_alerts_patient_specific\" src=\"\" style=\"display:block;\" frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";
					
					/*pt_sp_alert = "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_sp_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><form name=\"chart_alerts_patient_specific\" target=\"chart_alerts_patient_specific\" action=\"../patient_info/common/alerts_reason_save.php\" method=\"post\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript: top.document.getElementById('disablePatAlertThisSession').value = 'yes'; this.form.submit();top.fmain.close_alert_box('divPtSpecificAlert');\" /> <input type=\"button\" class=\"dff_button\" value=\"Remove\" onclick=\"this.form.submit();top.fmain.close_alert_box('divPtSpecificAlert');\" /><input type=\"hidden\" name=\"patientSpecificFrm\" value=\"SCH\"><input type=\"hidden\" id=\"disablePatAlertThisSession\" name=\"disablePatAlertThisSession\" ></form></div><iframe name=\"chart_alerts_patient_specific\"  src=\"\" style=\"display:block;\" frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";
					alert_box("imwemr - Pt. Alerts", pt_sp_alert, 300, "", 550, 200, "divPtSpecificAlert", false, false);			*/
					$("body",top.fmain.document).append(pt_sp_alert_div);
					
				}

				//show pt specific alert
				var pt_poe_alert = arr_resp[11];
				if(pt_poe_alert != ""){					
					//pt_poe_alert = "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_poe_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript:top.fmain.close_alert_box('divPtPOEAlert');\" /></div>";
					//alert_box("imwemr - POE Alert", pt_poe_alert, 300, "", 600, 250, "divPtPOEAlert", false, true);
					//alert(pt_poe_alert);
				}
				if($("#poeModal").length>0 && !$("#poeModal").hasClass("hidden")){$("#poeModal").modal('show');}

				//to do flag
				var pt_to_do = arr_resp[10];
				if(pt_to_do == true){
					document.getElementById("todo_flag_space").style.display = "block";
				}
				
				//Collection Alert
				var coll_alert = arr_resp[30];
				if(coll_alert == 1){
					top.fAlert("Patient account status is <font color='#ff0000'><b>"+arr_resp[35]+"</b></font>.");
				}
				
				//first available Alert
				if($.trim(arr_resp[36])!=''){
					top.fAlert(arr_resp[36],'First Available time slot found');
				}
				//updating recently searched patients list in front desk and top search panel
				//load_recent_pt_search(pat_id);

				//reload title bar
				//top.refresh_control_panel("Patient_Info",pat_id);

				//display to do button
				if(top.document.getElementById("tl_2to")){
					top.document.getElementById("tl_2to").style.display = 'block';
				}
				
				//hiding loading image
				top.show_loading_image("hide");

				// CALL FUNCTION TO DISPLAY MESSAGE OF SELECTED PROCEDURE 
				if(document.getElementById('sel_proc_id').value!='') {
					getProcMessage(document.getElementById('sel_fd_provider').value,document.getElementById('sel_proc_id').value);	
				}
				//---------------
				////Eligibility
				if(document.getElementById("div_fd_el_links")){
					var strElLinkInnerHTML = arr_resp[31];
					if(strElLinkInnerHTML != "undefined"){
						$("#div_fd_el_links").css("display","inline-block");
						//alert(strElLinkInnerHTML);
						document.getElementById("div_fd_el_links").innerHTML = strElLinkInnerHTML;
					}
				}
				
				if(document.getElementById('rte_info')){
					var rte_icon = arr_resp[32];
					if(strElLinkInnerHTML != "undefined"){
						$("#rte_info").css("display","inline-block");
						//alert(strElLinkInnerHTML);
						document.getElementById("rte_info").innerHTML = rte_icon;
					}
					
				}
				
				//Setting Pt. Alert notification counter
				set_pt_allert_notification_counter();
			}
		});
	}
}

function set_pt_allert_notification_counter(){
	$.ajax({
		url:'get_pt_alert.php',
		type:'POST',
		success:function(response){
			if($.trim(response) != ''){
				$('.pt_alert_container').not('.portal').html(response);
			}
		}
	});
}

function getRealTimeEligibilityApp(insRecId, askElFrom, strRootDir, schId, strAppDate, intClentWinH){
	if(strRootDir != ""){	
		askElFrom = askElFrom || 0;
		
		//top.show_loading_image("show", 100, "Please wait while Real Time Eligibility is processing");
		top.show_loading_image("show", 100);
		$.ajax({ 
			url: strRootDir +'/patient_info/ajax/make_270_edi.php?action=ins_eligibility&insRecId='+insRecId+'&askElFrom='+askElFrom+'&schId='+schId+'&strAppDate='+strAppDate, 
			success: function(responseText){
				res = JSON.parse(responseText);
				var strResp=res.data;
				var arrResp = strResp.split("~~");
				if(arrResp[0] == "1" || arrResp[0] == 1){
					var alertResp = "";
					if(arrResp[1] != ""){
						if(arrResp[3] == "A"){
							alertResp += "Patient Eligibility Or Benefit Information Status :<label style='color:green;'>"+arrResp[1]+"</label><br>";
						}
						else if(arrResp[3] == "INA"){
							alertResp += "Patient Eligibility Or Benefit Information Status :<label style='color:red;'>"+arrResp[1]+"</label><br>";
						}
						else{
							alertResp += "Patient Eligibility Or Benefit Information Status :<label style='color:black;'>"+arrResp[1]+"</label><br>";
						}
					}
					if(arrResp[2] != ""){
						alertResp += "With Insurance Type Code :"+arrResp[2]+"<br><br>";
					}
					if(alertResp != ""){
						if(arrResp[3] == "A"){
							document.getElementById('imgEligibility').src = "../../library/images/eligibility_green.png";
						}
						else if(arrResp[3] == "INA"){
							document.getElementById('imgEligibility').src = "../../library/images/eligibility_red.png";
						}
						document.getElementById('imgEligibility').title = alertResp;
						//alert(alertResp);
						
						var elId = parseInt(arrResp[4]);
						var strShowMsg = arrResp[5];
						if((elId > 0) && (strShowMsg) == "yes"){
							alertResp += "Would you like to set Co-Pay, Deductible and Co-Insurance!<br>"
						}
						if((elId > 0) && (strShowMsg) == "yes"){
							top.fancyConfirm(alertResp,"","window.top.fmain.send_request('"+strRootDir+"','"+elId+"','"+intClentWinH+"')" );
						}
						else{
							top.fAlert(alertResp);
						}
						
						var schedule_id = $("#global_context_apptid").val();
						var sa_date = get_selected_date();						
						$.ajax({
							url: "get_day_name.php?load_dt="+sa_date,
							success: function(day_name){
								//load_appt_schedule(sa_date, day_name, schedule_id, '', false);
								load_calendar(sa_date, day_name, '', '', schedule_id);
								top.show_loading_image("hide");
								
							}
						});
					}
				}
				else if(arrResp[0] == "2" || arrResp[0] == 2){						
					if(arrResp[1] != ""){
						document.getElementById('imgEligibility').src = "../../library/images/eligibility_red.png";
						document.getElementById('imgEligibility').title = arrResp[1];
						//alert(arrResp[1]);
						var schedule_id = $("#global_context_apptid").val();
						var sa_date = get_selected_date();						
						$.ajax({
							url: "get_day_name.php?load_dt="+sa_date,
							success: function(day_name){
								//load_appt_schedule(sa_date, day_name, schedule_id, '', false);
								load_calendar(sa_date, day_name, '', '', schedule_id);
								top.show_loading_image("hide");
							}
						});
					}
				}
				else{
					//document.write(arrResp[0]);
					top.fAlert(arrResp[0]);
					var schedule_id = $("#global_context_apptid").val();
					var sa_date = get_selected_date();						
					$.ajax({
						url: "get_day_name.php?load_dt="+sa_date,
						success: function(day_name){
							//load_appt_schedule(sa_date, day_name, schedule_id, '', false);
							load_calendar(sa_date, day_name, '', '', schedule_id);
							top.show_loading_image("hide");
						}
					});
				}
				top.show_loading_image("hide");
			}
		});
		
	}
}

function send_request(strRootDir, elId, intClentWinH){
	var urlAmount = strRootDir + '/patient_info/eligibility/eligibility_report.php?set_rte_amt=yes&id='+elId;
	var h = intClentWinH;
	window.open(urlAmount,'setAmountRTE','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
	
}

function load_insurance(ins_case_id){
	$.ajax({ 
		url: "insurance_active_case.php?current_caseids="+ins_case_id, 
		success: function(resp2){
			document.getElementById("load_pt_insurance").innerHTML = resp2;
			chk_referral('load_insurance');
			chk_verif_sheet('load_insurance');
			var pat_id = $("#global_ptid").val();			
			get_copay(pat_id, ins_case_id);
		}
	});
}

function load_last_app(){
	var pat_id = $("#global_ptid").val();
	$.ajax({ 
		url: "load_last_app.php?pid="+pat_id, 
		success: function(resp){
			var sch_arr = resp.split("~~");
			if(sch_arr[0] != "" && sch_arr[1] != "" && sch_arr[2] != ""){
				//load_calendar(sch_arr[2], sch_arr[3], 'nonono');				
				pre_load_front_desk(sch_arr[1], sch_arr[0], false);	
				//load_appt_schedule(sch_arr[2], sch_arr[3], '', 'nonono')
			}
		}
	});
}

function showAssesmentList(mode,dated){
	if(mode == 1){
		$.ajax({
			url: "../accounting/accountingAPResult.php?scheduler_call=yes&dat_id="+dated,
			success: function(resp){
				var response = resp.split('<body>');
				if(response[1]){
					response = response[1].replace('</body></html>','');
					response = response.replace('msgDiv','msgDivReplaced');
					resp = response;
				}
				
				$('#AssesmentDiv .modal-body').html(resp);
				$('#AssesmentDiv').modal('show');
			}
		});	
	}else if(mode == 0){
		$('#AssesmentDiv .modal-body').html('');
		$('#AssesmentDiv').modal('hide');
	}
}

function fd_scan_patient_image(){
	var webcam_window = window.open("../patient_info/demographics/webcam/flash.php",'webcam_window_popup','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=650,height=600,left=150,top=60');		
}

function close_patient_info(){
	reset_pt_data();
	$.ajax({
		url: "pre_fd_search_patient.php",
		success: function(resp){
			$("#front_desk_container").html(resp);
		}
	});
	$("#show_pt_name").html('');
}

function scan_licence(){
	var scan_window = window.open("../patient_info/demographics/scan_licence.php#scan_license",'scan_window_popup','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=650,height=600,left=150,top=60');
		
}

/*
Function: load_navigation_bar
Purpose: to change values in nav bar
Author: AA
*/
function load_navigation_bar(day_name){
	var sel_dat = get_selected_date();
	var arr_dat = sel_dat.split("-");

	var month_arr = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
	var selBoxOptCount = document.getElementById("sel_month_year").options;
	var selBoxCheck = false;
	for(i=0;i<selBoxOptCount.length;i++){
		if(selBoxOptCount[i].value == arr_dat[1]+"-01-"+arr_dat[0]+"|"){
			selBoxOptCount.selectedIndex = i;
			selBoxCheck = true;
		}
	}
	if(selBoxCheck == false){
		var optn = document.createElement("OPTION");
		var arr_index = arr_dat[1] - 1;
		optn.text = month_arr[arr_index]+" "+arr_dat[0];
		optn.value = arr_dat[1]+"-01-"+arr_dat[0]+"|";
		selBoxOptCount.add(optn);
		selBoxOptCount.selectedIndex = i;
	}
	if(date_format == "mm-dd-yyyy")
	document.getElementById("DayNameId").innerHTML = day_name+", "+arr_dat[1]+"-"+arr_dat[2]+"-"+arr_dat[0];
	else if(date_format == "dd-mm-yyyy")
	document.getElementById("DayNameId").innerHTML = day_name+", "+arr_dat[2]+"-"+arr_dat[1]+"-"+arr_dat[0];
}
/*
Function: display_block_none
Purpose: to show / hide a particular html element
Author: AA
Arguments: object id and action - block / none
*/
function display_block_none(obj_id, action){
	document.getElementById(obj_id).style.display = action;
}

/*
Function: show_msg_in_stack
Purpose: to show a particular message in the message stack
Author: AA
Arguments: msg content
*/
var global_stack_msg_identifier = 0;
var global_stack_msg_count = 0;
function show_msg_in_stack(msg){
	var left = global_stack_msg_identifier * 25;
	var top = global_stack_msg_identifier * 25;
	var this_msg = "<div id=\"msg"+global_stack_msg_identifier+"\" style=\"display:block;position:absolute;background-color:#FFFFFF;left:"+left+"px;top:"+top+"px;text-align:center;\">"+msg+"<br><br><input type=\"button\" value=\"OK\" onclick=\"javscript:hide_msg_in_stack('msg"+global_stack_msg_identifier+"');\" /></div>";
	document.getElementById("global_msg_stack").innerHTML = document.getElementById("global_msg_stack").innerHTML + this_msg;
	display_block_none("global_msg_stack", "inline-block");
	global_stack_msg_identifier++;
	global_stack_msg_count++;
}

/*
Function: hide_msg_in_stack
Purpose: to hide a particular message in the message stack
Author: AA
Arguments: hide div obj
*/
function hide_msg_in_stack(obj_id){
	display_block_none(obj_id, "none");
	global_stack_msg_count--;
	if(global_stack_msg_count == 0){
		hide_msg_stack();
	}
}

/*
Function: hide_msg_stack
Purpose: to hide the whole msg stack
Author: AA
*/
function hide_msg_stack(){
	document.getElementById("global_msg_stack").innerHTML = "";
	display_block_none("global_msg_stack", "none");
	global_stack_msg_identifier = 0;
	global_stack_msg_count = 0;
}


function show_cal_context_menu(obj_name, sel_y_m_d, e){
	//alert(sel_y_m_d);
	if(document.getElementById("global_admin").value == "1"){
			
		//this code commented because it haulting process in safari
		//$("#"+obj_name).mouseup(function(event) {
				
			//alert(event.which);
			switch (event.which) //WhichButton(event)
			{
				case 3:	
					document.oncontextmenu = function(){ return false; };
					document.getElementById("global_context_caldt").value = sel_y_m_d;
					display_block_none("div_add_prov_button", "block");
					document.getElementById("div_add_prov_button").style.width = 90;
					document.getElementById("div_add_prov_button").style.position = 'absolute';
					document.getElementById("div_add_prov_button").style.display = 'block';
					document.getElementById("div_add_prov_button").style.pixelLeft = event.clientX;		
					document.getElementById("div_add_prov_button").style.pixelTop = event.clientY;
					
					var bro_ver=navigator.userAgent.toLowerCase();
					//if browser is crhome or firfox or safari then we need to placement issue
					if(bro_ver.search("chrome")>1 || bro_ver.search("firefox")>1){
						$("#div_add_prov_button").css({"display":"inline-block",top: event.clientY, left: event.clientX});
					}
				break;
				default:
					display_block_none("div_add_prov_button", "none");
			}
		//});
	}
}

function open_add_schedule_option(mode){
	if(!mode) mode = "";
    $('#div_add_prov_form').modal('show');
	TestOnMenu();

	//setting this date
	if(mode == "appt_scheduler"){
		
		var ap_sttm = $("#global_context_slsttm").val();
		var global_time_slot = $("#global_time_slot").val();
		//alert(ap_sttm+" "+global_time_slot);
		var arr_ap_sttm = ap_sttm.split(":");
		var ap_hr = parseInt(arr_ap_sttm[0]);
		var ap_mn = parseInt(arr_ap_sttm[1]);
		var ap_sc = parseInt(arr_ap_sttm[2]);

		var ap_doc = $("#global_context_sldoc").val();
		$("#anps_sel_pro").val(ap_doc);

		//alert(ap_hr+" "+ap_mn+" "+ap_dt);
		
		var set_st_ampm = "AM";
		var set_st_hr = ap_hr;
		if(set_st_hr > 12){
			set_st_hr = set_st_hr - 12;	
			set_st_ampm = "PM";
		}
		if(set_st_hr == 12){
			set_st_ampm = "PM";
		}
		if(parseInt(set_st_hr) < 10){
			set_st_hr = "0" + parseInt(set_st_hr);
		}
		var set_st_mn = ap_mn;
		if(parseInt(set_st_mn) < 10){
			set_st_mn = "0" + parseInt(set_st_mn);
		}
		
		var set_ed_ampm = "AM";
		var set_ed_mn = ap_mn + 60;
		var set_ed_hr = set_st_hr;
		if(set_ed_mn >= 60){	//assuming slot will be never be more that  1hr
			set_ed_mn = set_ed_mn - 60;
			set_ed_hr = parseInt(set_ed_hr) + 1;
		}
		if(parseInt(set_ed_hr) >= 12){
			set_ed_ampm = "PM";
		}
		if(parseInt(set_ed_hr) > 12){
			set_ed_hr = set_ed_hr - 12;
		}
		if(parseInt(set_ed_hr) < 10){
			set_ed_hr = "0" + parseInt(set_ed_hr);
		}
		if(parseInt(set_ed_mn) < 10){
			set_ed_mn = "0" + parseInt(set_ed_mn);
		}
		
		//set title
		$("#setTimeTitle").html("Open Physician Schedule");
		//setting time
		//alert(set_st_hr+" "+set_st_mn+" "+set_st_ampm+" "+set_ed_hr+" "+set_ed_mn+" "+set_ed_ampm);
		$("#start_hour").val(set_st_hr);
		$("#start_min").val(set_st_mn);
		$("#start_time").val(set_st_ampm);

		//$("#end_hour").val(set_ed_hr);
		//$("#end_min").val(set_ed_mn);
		//$("#end_time").val(set_ed_ampm);
		
		var cal_sel_date = get_selected_date();
		var arr_cal_date = cal_sel_date.split("-");
		$("#anps_day_name").html(arr_cal_date[1]+"-"+arr_cal_date[2]+"-"+arr_cal_date[0]);
		
		document.getElementById('show_tmp_option').style.display = 'none';
		//document.getElementById('temp_dd').style.display = 'none';
		//document.getElementById('temp_text').style.display = 'inline-block';
		document.getElementById('commentDiv').style.display = 'inline-block';
		document.getElementById('prov_sch_add_type').value = "SYSTEM";
		
	}else{

		$("#start_hour").val("");
		$("#start_min").val("");
		$("#start_time").val("");

		$("#end_hour").val("");
		$("#end_min").val("");
		$("#end_time").val("");

		$("#setTimeTitle").html("Add New Provider Schedule");
		var cal_sel_date = document.getElementById("global_context_caldt").value;
		var arr_cal_date = cal_sel_date.split("-");
		$("#anps_day_name").html(arr_cal_date[1]+"-"+arr_cal_date[2]+"-"+arr_cal_date[0]);
		
		document.getElementById('show_tmp_option').style.display = 'inline-block';
		document.getElementById('temp_dd').style.display = 'inline-block';
		document.getElementById('temp_text').style.display = 'none';
		document.getElementById('commentDiv').style.display = 'none';
		//document.getElementById('prov_sch_add_type').value = "USER";
		document.getElementById('prov_sch_add_type').value = "SYSTEM";
	}
	
	//document.getElementById("div_add_prov_form").style.position = 'absolute';
	//document.getElementById("div_add_prov_form").style.display = 'block';
	//document.getElementById("div_add_prov_form").style.pixelLeft = 100;		
	//document.getElementById("div_add_prov_form").style.pixelTop = 130;
	//display_block_none("div_add_prov_button", "none");
}

function load_template_timings(tmp_id){
	if(!tmp_id) tmp_id = "new";
	if(tmp_id == "new"){
		$("#start_hour").val("");
		$("#start_min").val("");
		$("#start_time").val("");

		$("#end_hour").val("");
		$("#end_min").val("");
		$("#end_time").val("");
	}else{
		$.ajax({
			url: "get_template_timings.php?tmp_id="+tmp_id,
			success: function(resp){
				//alert(resp);
				if(resp == ""){
					$("#start_hour").val("");
					$("#start_min").val("");
					$("#start_time").val("");

					$("#end_hour").val("");
					$("#end_min").val("");
					$("#end_time").val("");
				}else{
					var arr_resp = resp.split("~");

					$("#start_hour").val(arr_resp[0]);
					$("#start_min").val(arr_resp[1]);
					$("#start_time").val(arr_resp[2]);

					$("#end_hour").val(arr_resp[3]);
					$("#end_min").val(arr_resp[4]);
					$("#end_time").val(arr_resp[5]);
				}
			}
		});
	}
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function save_provider_schedule(){

	var err = "";
	
	if($("#anps_sel_fac").val() == ""){
		err += " - Facility\n";
	}
	if($("#anps_sel_pro").val() == ""){
		err += " - Provider\n";
	}
	if($("#start_hour").val() == "" || $("#start_min").val() == "" || $("#start_time").val() == ""){
		err += " - Start Time\n";
	}
	if($("#end_hour").val() == "" || $("#end_min").val() == "" || $("#end_time").val() == ""){
		err += " - End Time\n";
	}
	
	if(err != ""){
		err = "Please provide input for the following:\n\n" + err;
		top.fAlert(err);
		return false;
	}else{
		top.show_loading_image("show");
		//var cal_sel_date = document.getElementById("global_context_caldt").value;
		//alert($("#anps_day_name").html());
		var cal_sel_date_tmp = $("#anps_day_name").html();
		var cal_sel_date_arr = cal_sel_date_tmp.split("-");
		var cal_sel_date = cal_sel_date_arr[2]+"-"+cal_sel_date_arr[0]+"-"+cal_sel_date_arr[1];
		//alert(cal_sel_date);
		//return false;

		var anps_sel_fac = document.getElementById("anps_sel_fac").value;
		var anps_sel_pro = document.getElementById("anps_sel_pro").value;
		var anps_sel_tmp = document.getElementById("anps_sel_tmp").value;

		var start_hour = document.getElementById("start_hour").value;
		var start_min = document.getElementById("start_min").value;
		var start_time = document.getElementById("start_time").value;

		var end_hour = document.getElementById("end_hour").value;
		var end_min = document.getElementById("end_min").value;
		var end_time = document.getElementById("end_time").value;
		
		var comm = document.getElementById("comments").value;
		var template_type = document.getElementById('prov_sch_add_type').value;
		//alert("save_prov_sch.php?anps_sel_tmp="+anps_sel_tmp+"&cal_sel_date="+cal_sel_date+"&start_hour="+start_hour+"&start_min="+start_min+"&start_time="+start_time+"&end_hour="+end_hour+"&end_min="+end_min+"&end_time="+end_time+"&anps_sel_pro="+anps_sel_pro+"&anps_sel_fac="+anps_sel_fac);
		$.ajax({
			url: "save_prov_sch.php?anps_sel_tmp="+anps_sel_tmp+"&cal_sel_date="+cal_sel_date+"&start_hour="+start_hour+"&start_min="+start_min+"&start_time="+start_time+"&end_hour="+end_hour+"&end_min="+end_min+"&end_time="+end_time+"&anps_sel_pro="+anps_sel_pro+"&anps_sel_fac="+anps_sel_fac+"&comm="+escape(comm)+"&template_type="+template_type,
			success: function(resp){	
				//document.write(resp);
				var arr_resp = resp.split("~");
				top.show_loading_image("hide");
				load_calendar(arr_resp[0], arr_resp[1], '', false);
				$('#div_add_prov_form').modal('hide');
			}
		});
	}
}

function checkBoxMultiValChk(){	
	
	var str_multi = "";
	var obj_multi = document.getElementsByName("facilities[]");
	for(i = 0; i < obj_multi.length; i++){
		if(obj_multi[i].checked == true){
			str_multi += obj_multi[i].value + ",";
		}
	}

	if(str_multi != ""){
		str_multi = str_multi.substr(0,str_multi.length-1);

		//setting cookie
		var name = 'facility';
				
		var date = new Date();
		date.setTime(date.getTime()+(1*24*60*60*1000));
		
		var expires = "; expires="+date.toGMTString();
		document.cookie = name+"="+str_multi+expires+"; path=/";
		
		//reloading templates
		var vmonth = document.getElementById("theMonth").value;
		var vyear = document.getElementById("theYear").value;
		var dt = document.getElementById("theDate").value;
		var strDayName = document.getElementById("strDayName").value;
		var dtVal = vyear+"-"+vmonth+"-"+dt;
		
		var prov = "";
		var selectbox = document.getElementsByName('sel_pro_month[]');
		if(selectbox.length == 1){
			var selectbox = document.getElementById('sel_pro_month');
			for ( var i = 0, l = selectbox.options.length, o; i < l; i++ ){  
				o = selectbox.options[i];
				if(o.selected == true){
					if(prov == ""){
						prov = o.value;
					}else{
						prov = o.value + "," + prov;
					}
				}
			}
		}else{
			for(i = 0; i < selectbox.length; i++){
				if(selectbox[i].checked == true){
					if(prov == ""){
						prov = selectbox[i].value;
					}else{
						prov = selectbox[i].value + "," + prov;
					}
				}
			}
		}
		
		var arr_prov = prov.split(",");
		var int_multi_p = "";
		//var obj_multi_p = document.getElementsByName("sel_pro_month[]");
		//alert(obj_multi_p);
		if(arr_prov.length == 1){
			int_multi_p = arr_prov[0];			
			//alert(int_multi_p);
			//var sel_pro_monthTEMP=document.getElementById("sel_pro_month").value;
			changeDay_physician(int_multi_p,vmonth,dt,vyear,'');
		}else{		
			//loadScheduler(dtVal,strDayName);
			see_sel_month();
		}
	}else{
		document.getElementById("day_save").innerHTML = '<div style="text-align:center;padding-top:13px;position:absolute;top:200px;left:775px;width:250px;height:50px;border:2px solid #ffffff;color:#ff0000" class="text_10b">No Facility has been selected.</div>';
	}
}

/*
Function: toggle_sch_type
Purpose: to swtich between day, week , month schedulers
Author: AA
*/
function toggle_sch_type(mode, set_dt){
	
	if(!set_dt) set_dt = "";
	
	if(set_dt != ""){
		var arr_st_ed = set_dt.split("|");
		var arr_dt_st = arr_st_ed[0].split("-");
		var dt = arr_dt_st[1];
		var mn = arr_dt_st[0];
		var yr = arr_dt_st[2];
	}else{
		var dt = $("#global_date").val();
		var mn = $("#global_month").val();
		var yr = $("#global_year").val();
	}
	top.show_loading_image("show");
	
	if(mode == "week"){		
		top.fmain.document.location.href = "base_week_scheduler.php?sel_date="+mn+"-"+dt+"-"+yr;
	}else if(mode == "month"){
		top.fmain.document.location.href = "base_month_scheduler.php?sel_date="+mn+"-"+dt+"-"+yr;
	}else if(mode == "day"){
		top.fmain.document.location.href = "base_day_scheduler.php?sel_date="+mn+"-"+dt+"-"+yr;
	}
}

/**WEEK SCHEDULER VIEWER*/
function load_week_scheduler(selected_sess_facs, selected_sess_prov){
	//selecting facility from session, if any
	set_facilities(selected_sess_facs);
	$("#facilities").selectpicker("refresh");
	//selecting providers from session, if any
	set_providers(selected_sess_prov);
	
	load_week_appt_schedule(selected_sess_facs, selected_sess_prov);
}

function initScrollLayer(){	
/*
	 wndo0 = new dw_scrollObj('wn_0', 'lyr1_0');			
	 dw_scrollObj.GeckoTableBugFix('wn_0');

	 wndo1 = new dw_scrollObj('wn_1', 'lyr1_1');			
	 dw_scrollObj.GeckoTableBugFix('wn_1');
	
	 wndo2 = new dw_scrollObj('wn_2', 'lyr1_2');			
	 dw_scrollObj.GeckoTableBugFix('wn_2');	
	
	 wndo3 = new dw_scrollObj('wn_3', 'lyr1_3');			
	 dw_scrollObj.GeckoTableBugFix('wn_3');
	 
	 wndo4 = new dw_scrollObj('wn_4', 'lyr1_4');			
	 dw_scrollObj.GeckoTableBugFix('wn_4');	
	 
	 wndo5 = new dw_scrollObj('wn_5', 'lyr1_5');			
	 dw_scrollObj.GeckoTableBugFix('wn_5');	
	 
	 wndo6 = new dw_scrollObj('wn_6', 'lyr1_6');			
	 dw_scrollObj.GeckoTableBugFix('wn_6');
	 */
}

/**MONTH SCHEDULER VIEWER*/
function load_month_scheduler(selected_sess_facs, selected_sess_prov){
	top.show_loading_image("hide");
	//selecting facility from session, if any
	set_facilities(selected_sess_facs);
	$("#facilities").selectpicker("refresh");
	//selecting providers from session, if any
	set_providers(selected_sess_prov);
}

function searchPatientInFrontDesk(obj){
	var patientdetails = obj.value.split(':');
	if(isNaN(patientdetails[0]) == false){
		document.getElementById("txt_patient_app_name").value = patientdetails[1];
		document.getElementById("hd_patient_id").value = patientdetails[0];
		pre_load_front_desk(patientdetails[0],'','');	
	}
}

function selPatient_frontdesk(){
	if(typeof(top.update_iconbar)!='undefined') top.update_iconbar();
	var lname_val = document.getElementById("txt_patient_app_name").value;
	var findValArr = document.getElementById("findByShow").value.split(':');
	if(isNaN(findValArr[0])){
		var findBy = findValArr[0];
	}else{
		var findBy = findValArr[2];	
	}
	if(isNaN(lname_val) == true){
		if((lname_val[0].toLowerCase() == "e") && (parseInt(lname_val.substring(1, lname_val.length)) > 0)){
			lname_val = lname_val.substring(1, lname_val.length);
			findBy = "External MRN";
		}
	}
	if(isNaN(lname_val) || (isNaN(lname_val)==false && findBy=='Ins.Policy')){
		window.open("search_patient_popup.php?sel_by="+findBy+"&txt_for="+lname_val+"&btn_sub=Search&call_from=scheduler","PatientWindow","width=800,height=500,top=420,left=150,scrollbars=yes");
	}else{
		$.ajax({
			url: 'chk_patient_exists.php',
			type: 'POST',
			data: 'pid='+lname_val+'&findBy='+findBy,
			success: function(resultData)
			{
                if(resultData.length > 1) resultData = JSON.parse(resultData);
                
                if(resultData.hasOwnProperty('askForReason')==true)
                {
                    var patId = resultData.patId;
					var bgPriv = resultData.bgPriv;
					if( findBy=='External MRN' )
					{
						document.getElementById( "findByShow" ).value = 'Active';
						document.getElementById( "txt_patient_app_name" ).value = patId;
					}
					top.core_restricted_prov_alert(patId, bgPriv,'','');
                }
                else if(resultData == 'n')
				{
					top.fAlert('Patient not found');	
				}
				else
				{
					pid = eval(resultData);
					if( findBy=='External MRN' )
					{
						document.getElementById( "findByShow" ).value = 'Active';
						document.getElementById( "txt_patient_app_name" ).value = pid;
					}
					pre_load_front_desk(pid);
				}				
			}
		});		
	}
	return false;
}

function patient_erx_registration(){	
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	window.open("load_patient_erx_registration.php?sel_date="+sel_date+"&facility_id="+sel_fac,'erx_reg','width=500,height=300');	
}

function send_to_forum(intClentWinH){
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	var urlFile = "forum_send_to.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date;
	var h = intClentWinH;
	top.popup_win(urlFile,'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
	return;
}

function pre_auth(intClentWinH){
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	var h = parseInt(intClentWinH-100);
	var urlFile = "pre_auth_send.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date+"&height="+h;
	top.popup_win(urlFile,'width=1200px,height='+h+'px,toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,left=10,top=10');
	return;
}


function realtime_medicare_eligibility(intClentWinH){
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	var urlRTEFile = "realtime_eligibility_All.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date;
	var h = intClentWinH;
	window.open(urlRTEFile,'allAPPRTE','toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
	return;
	/*if(confirm("Eligibility Checking process may take several minutes.\nAre you sure to start the process?")){
		top.show_loading_image("show");

		var sel_date = get_selected_date();
		var sel_fac = get_selected_facilities();
		var sel_pro = get_selected_providers();

		$.ajax({
			url: "realtime_medicare_eligibility.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date,
			success: function(resp){
				//document.getElementById("txt_comments").value = resp;
				//return;
				if(typeof(resp) != "undefined"){
					var arrResp = resp.split("-");					
					if((arrResp[0] == 1) || (arrResp[0] == 2) || (arrResp[0] == 3)){
						top.show_loading_image("hide");
						
						//loading scheduler
						//load_calendar(day_sel_date, arrResp[2], '', false);
						alert(arrResp[1]);
						return false;
					}
				}
				var schedule_id = $("#global_context_apptid").val();
				var sa_date = get_selected_date();						
				$.ajax({
					url: "get_day_name.php?load_dt="+sa_date,
					success: function(day_name){
						load_appt_schedule(sa_date, day_name, schedule_id, '', false);
						top.show_loading_image("hide");
					}
				});
				//top.show_loading_image("hide");
				//loading scheduler
				//load_calendar(day_sel_date, arrResp[2], '', false);
			}
		});
	}*/
}
function realtime_eligibility_file(intClentWinH){
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var sel_pro = get_selected_providers();
	var urlRTEFile = "realtime_eligibility_file.php?phyId="+sel_pro+"&facId="+sel_fac+"&dated="+sel_date;
	var h = intClentWinH;
	window.open(urlRTEFile,'winRTEFile','toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}

function print_day_appt_report(div_name, mode){
	if(document.getElementById(div_name).style.display == "none"){
		document.getElementById(div_name).style.display = "block";
	}else{
		document.getElementById(div_name).style.display = "none";
	}
	if(mode){
		var window_count = document.getElementById("hid_prov_count").value;
		var window_new_width = window_count * 200;
		if(window_new_width > 1200){
			window_new_width = 1200;
			document.getElementById(div_name).style.overflow = "auto";
		}
		document.getElementById(div_name).style.width = window_new_width+"px";
		document.getElementById(div_name).style.right = "20px";
	}
}

function print_day_appt_report_process(providerID, rep_fac, eff_date, selMidDay, div_name){
	if(providerID == "get"){
		var prov = get_selected_providers();
		providerID = prov;
	}

	if(rep_fac == "get"){
		var loca = get_selected_facilities();
		rep_fac = loca;
	}

	if(eff_date == "get"){
		var sel_date = get_selected_date();
		var arr_sel_date = sel_date.split("-"); //ymd
		eff_date = arr_sel_date[1]+"-"+arr_sel_date[2]+"-"+arr_sel_date[0]; //mdy
	}
	
	document.getElementById("eff_date").value = eff_date;
	document.getElementById("rep_fac").value = rep_fac;
	document.getElementById("providerID").value = providerID;
	document.getElementById("selMidDay").value = selMidDay;

	if(div_name != ""){
		print_day_appt_report(div_name);
	}
	document.frm_day_appt_print.submit();
}

function newPatient_info(id, sch_id, mode){
	if(!sch_id) sch_id = "";
	if(!mode) mode = "";
	var win_height = screen.height;
	if(id>0){
		if(document.getElementById("show_ci_demographics").value == "yes"){
			top.core_set_pt_session(top.fmain, id, '../patient_info/demographics/index.php');
		}else{
			top.popup_win("../scheduler/common/new_patient_info_popup_new.php?source=scheduler&mode="+mode+"&search=true&ci_pid="+id+"&sch_id="+sch_id+"&sel_date="+ga_sel_date+"&frm_status=show_check_in&popheight="+win_height, "width=1200,scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");
		}
	}else{
			top.popup_win("../scheduler/common/new_patient_info_popup_new.php?source=scheduler&mode="+mode+"&search=true&ci_pid="+id+"&sch_id="+sch_id+"&frm_status=show_check_in&popheight="+win_height,  "width=1200,scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");
	}
}

function superbill_info(id) {
	
	var current_caseId_sch = dgi("choose_prevcase").value;	
	var app_id = $(global_apptid).val();
	//alert(app_id);
	$.ajax({
		url: "get_latest_encounter.php?pid="+id+"&app_id="+app_id,
		success: function(resp){
			//alert(resp);
			if(resp==''){
				top.fAlert("No SuperBill is associated with this DOS.");
			}else {
				//../chart_notes/requestHandler.php?elem_formAction=SuperBill_Print&e_id="+resp+"&neww=1
				top.popup_win("../chart_notes/requestHandler.php?elem_formAction=SuperBill_Print&e_id="+resp+"&neww=1",'width=1170,height=630,top=10,left=10,scrollbars=yes,resizable=yes');	
			}
		}
	});
}

function showClSupplyOrderFromFrontDesk() {
	var SupplyUrl="../chart_notes/print_order.php?callFrom=clSupply";
	top.popup_win(SupplyUrl,"width=1090,scrollbars=1,height=690,top=2,left=0");
}

function contactLensDispense(){
	$.ajax({
		url: "getCldispenseDetails.php?",
		success: function(resp){
			if(resp > 0){
				var DispUrl="../chart_notes/cl_dispense.php?print_order_id="+resp;
				window.open(DispUrl,"ClDispOrderWindow","width=1060,scrollbars=0,height=370,top=2,left=0");
				//redirectToEnterCharges(pid);
			}else{
				top.fAlert("Sorry no encounter exist for this Recieved Supply.");
			}
		}
	});
}

function open_erx(id){
	var parentWid = parent.document.body.clientWidth;
	var parenthei = parent.document.body.clientHeight;
	var url="../chart_notes/erx_patient_selection.php?patientFromSheduler="+id;
	window.open(url,'erx_window','scrollbars=1,resizable=1,width='+parentWid+',height='+parenthei+'');
}

function openChartNotes(){
	//top.refresh_control_panel("Work_View");
	//top.fmain.location.href="../chart_notes/home.php";
	top.core_redirect_to("Work_View", "../chart_notes/main_page.php");
}

function descrip(pt_id, sch_id){
	var locs = get_selected_facilities();		
	url="recall_desc_save.php?patient_id="+pt_id+"&sch_id="+sch_id+"&loc="+locs;
	//top.popup_win(url,'repeata',''dependent=yes,toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=800px,height=350px,left=30px,top=50px'');
	window.open(url,'repeata','dependent=yes,toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=1200px,height=504px,left=30px,top=50px');
}

function get_Maketoday(varStr) {
	
}
function printFaceSheetFromFrontDesk(ptDocTempId,apptId) {
	var PrintUrl="../../interface/patient_info/common/process_pt_print_req.php?dont_print_medical=1&"+"print_form_id="+"&chart_nopro=3&face_sheet_scan=1&from=frontDesk&patient_info[]=face_sheet&apptId="+apptId;
	if(ptDocTempId) {
		var PrintUrl="../../interface/chart_notes/scan_docs/load_pt_docs.php?temp_id="+ptDocTempId+"&mode=facesheet&apptId="+apptId;
	}
	top.popup_win(PrintUrl,'printPatientFaceSheetWindow',"width=1050,resizable=yes,scrollbars=0,height=750,top=2,left=0");
}

function change_status_weekly(st_type, pt_id, ap_id){
	if(!pt_id) pt_id = $("#global_context_ptid").val();
	if(!ap_id) ap_id = $("#global_context_apptid").val();
	//alert(pt_id);
	$("#global_context_ptid").val(pt_id);
	$("#global_context_apptid").val(ap_id);
	TestOnMenu();
	switch(st_type){
		case "201":

			break;
		case "18":
			
			break;
		case "17":

			break;
		case "13":
			newPatient_info(pt_id, ap_id, "weekly");
			break;
		case "11":
			$.ajax({
				url: "check_future_appt.php?ap_id="+ap_id,
				success: function(resp){alert('result received : '+resp);
					if(resp == "justdoit"){
						if(document.getElementById("show_payment_box_chk_out").value == "check out"){
						/*	var check_in_out_payment = top.popup_win("common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pt_id+"&sch_id="+ap_id,"width=1200,scrollbars=0,height=500,top=100,left=10");
							check_in_out_payment.focus();  */
							top.popup_win("common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pt_id+"&sch_id="+ap_id,"width=1200,scrollbars=0,height=500,top=100,left=10");
						}
					}else{
						top.fAlert('A patient cannot be checked out for a future appointment.');
						/*
						var pt_co_alert = "A patient cannot be checked out for a future appointment.";
						var checkout_msg = "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_co_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript:top.fmain.close_alert_box('divPtCheckoutAlert');\" /></div>";
						alert_box("imwemr - Check-out", checkout_msg, 300, "", 500, 150, "divPtCheckoutAlert", false, false);*/
						return false;
					}
				}
			});
			break;
	}
	
	var sel_fac = get_selected_facilities();
	//alert(sch_id+" "+st_type+" "+sel_date+" "+sel_fac+" "+pat_id);
	if(st_type == "18"){
		//loading reasons
		$.ajax({
			url: "load_reasons.php?pt_id="+pt_id+"&ap_id="+ap_id,
			success: function(resp){
				var arr_resp = resp.split("~~~~~");
				var title = "Cancellation Reason";
				
				var msg ='';
				msg ='<div class="row">';
				msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
				msg +='</div>';
				
				msg +='<div class="row">';
				msg +='<div class="col-sm-12">';
				msg +='<div class="form-group">';
				msg +='<label for="">Reason</label>';
				msg +='<select id="cancellation_reason" name="cancellation_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
				msg +='</div>';
				msg +='</div>';
				msg +='</div>';
					

				var btn1 = 'OK';
				var btn2 = 'Cancel';

				var misc = "DONOTASKPASSWORD";
				
				var func1 = 'save_cancellation_reason::::weekly';
				var func2 = 'hideConfirmYesNo';
				
				scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
			}
		});
	}else{
		getchange_status_weekly(ap_id, st_type, sel_fac, pt_id);
	}
}
function getchange_status_weekly(schedule_id, chg_to, loca, id){ 
	//alert("getchangedstatus.php?loca="+loca+"&sch_id="+schedule_id+"&chg_to="+chg_to+"&dt=notrequied&patId="+id+"&sid="+Math.random());

	var reason = $("#global_apptactreason").val();

	$.ajax({
		url: "getchangedstatus.php?loca="+loca+"&sch_id="+schedule_id+"&chg_to="+chg_to+"&reason="+reason+"&dt=notrequied&patId="+id+"&sid="+Math.random(),
		success: function(resp){			
			$("#global_apptactreason").val("");
			ids = resp.split("-");
			if(ids[1] == "11"){
				if(document.getElementById("show_payment_box_chk_out").value != "check out"){
					//top.core_redirect_to("Accounting", "../accounting/accountingTabs.php");
					top.change_main_Selection(top.document.getElementById('AccountingSB'));
				}else{
					load_week_appt_schedule();
				}
			}else{
				load_week_appt_schedule();
			}
		}
	});
}

function change_status_to_do(st_type){

	pt_id = $("#global_context_ptid").val();
	ap_id = $("#global_context_apptid").val();
	//alert(pt_id);
	$("#global_context_ptid").val(pt_id);
	$("#global_context_apptid").val(ap_id);	
	TestOnMenu();
	//alert($("#global_context_ptid").val(pt_id));
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
     //alert('Desc : '+pt_id+' '+ap_id+' '+' '+sel_date+' '+sel_fac); 
	//alert(sch_id+" "+st_type+" "+sel_date+" "+sel_fac+" "+pat_id);
	
	getchange_status(ap_id, st_type, sel_date, sel_fac, pt_id);
	
	save_first_available_reason_todo();
}

function change_status(st_type, pt_id, ap_id){
	if(!pt_id) pt_id = $("#global_context_ptid").val();
	if(!ap_id) ap_id = $("#global_context_apptid").val();
	//alert(pt_id);
	$("#global_context_ptid").val(pt_id);
	$("#global_context_apptid").val(ap_id);	
	TestOnMenu();
	switch(st_type){
		case "201":

			break;
		case "18":
			
			break;
		case "17":

			break;
		case "13":
			//newPatient_info(pt_id, ap_id);
			break;
		case "11":
			$.ajax({
				url: "check_future_appt.php?ap_id="+ap_id,
				success: function(resp){
					resultresp = resp;
					resultarr = resultresp.split('-');
					resp = resultarr[0];
					if(resp == "justdoit"){
						
						ga_ap_id=ap_id;
						ga_st_type=st_type;
						ga_sel_date=sel_date;
						ga_sel_fac=sel_fac;
						ga_pt_id=pt_id;
						
						$.ajax({
							url: 'get_ap_ids_by_patient.php',
							type: "POST",
							data: 'pt_id='+pt_id+'&sel_date='+sel_date+'&typ=CO',
							success: function(resp){
								appt_ids_result=$.parseJSON(resp);
								if(appt_ids_result.length > 1)
								{
									var msg="Is Check-Out be applied to the other appointments of the same patient";
									var trueFun="applyCOtrue('"+appt_ids_result +"','"+st_type+"')";
									var falseFun="applyCOFalse('"+st_type+"')";
									top.fancyConfirm(msg,"", "window.top.fmain."+trueFun,"window.top.fmain."+falseFun);
								}
								else 
								{
									ga_ap_id=ap_id;
									if(document.getElementById("show_payment_box_chk_out").value == "check out")
									{
										top.popup_win("common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pt_id+"&sch_id="+ap_id,"width=1305,scrollbars=0,height=600,top=100,left=10,resizable=yes");
									}
									getchange_status(ga_ap_id, st_type, sel_date, sel_fac, pt_id);
								}
							}
						});
						
						
						
						if(typeof resultarr[1] == "string" && resultarr[1] == "clTeach")
						{
							top.fAlert("Schedule CL teach Appointment");	
						}
						
					}else{
						var pt_co_alert = "A patient cannot be checked out for a future appointment.";
						top.fAlert(pt_co_alert);
						/*var checkout_msg = "<textarea onclick=\"javascript:return false;\" style=\"width:275px;height:75px;\" readonly=\"readonly\">"+pt_co_alert+"</textarea><div style=\"margin-top:10px;width:300px;text-align:center\"><input type=\"button\" class=\"dff_button\" value=\"OK\" onclick=\"javascript:top.fmain.close_alert_box('divPtCheckoutAlert');\" /></div>";
						alert_box("imwemr - Check-out", checkout_msg, 300, "", 500, 150, "divPtCheckoutAlert", false, false);*/
						return false;
					}
				}
			});
			break;
	}
	
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
                //alert('Desc : '+pt_id+' '+ap_id+' '+' '+sel_date+' '+sel_fac); 
	//alert(sch_id+" "+st_type+" "+sel_date+" "+sel_fac+" "+pat_id);
	if(st_type == "18"){
		//loading reasons
		var lr_url = "load_reasons.php?pt_id="+pt_id+"&ap_id="+ap_id+"&cancel="+1;
		$.ajax({
			url: lr_url,
			success: function(resp){
				var arr_resp = resp.split("~~~~~");
				var title = "Cancellation Reason";
				var msg ='';
				msg ='<div class="row">';
				msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
				msg +='</div>';
				
				msg +='<div class="row">';
				msg +='<div class="col-sm-12">';
				msg +='<div class="form-group">';
				msg +='<label for="">Reason</label>';
				msg +='<select id="cancellation_reason" name="cancellation_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
				msg +='</div>';
				msg +='</div>';
				msg +='</div>';
				
				msg +=arr_resp[2];
				
				var btn1 = 'OK';
				var btn2 = 'Cancel';

				var misc = "DONOTASKPASSWORD";
				
				var func1 = 'save_cancellation_reason';
				var func2 = 'hideConfirmYesNo';
				
				scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
			}
		});
	}
	else if(st_type == "271"){
		
		//loading reasons
		var lr_url = "load_reasons.php?pt_id="+pt_id+"&ap_id="+ap_id+"&ctype=first_available&cancel="+1;
				
		$.ajax({
			url: lr_url,
			success: function(resp){
				if(is_remote_server() == true)
				{
					resp = json_resp_handle(resp);				
				}				
				var arr_resp = resp.split("~~~~~");
				var title = "First Available";
				
				var msg ='';
				msg ='<div class="row">';
				msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
				msg +='</div>';
				
				msg +='<div class="row">';
				msg +='<div class="col-sm-12">';
				msg +='<div class="form-group">';
				msg +='<label for="">Reason</label>';
				msg +='<select id="cancellation_reason" name="cancellation_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
				msg +='</div>';
				msg +='</div>';
				msg +='</div>';
				
				msg +=arr_resp[2];

				var btn1 = 'OK';
				var btn2 = 'Cancel';

				var misc = "DONOTASKPASSWORD";
				
				var func1 = 'save_first_available_reason';
				var func2 = 'hideConfirmYesNo';
				
				scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
			}
		});
	}else if(st_type != "11")
	{
		if(st_type=="13")
		{
			ga_ap_id=ap_id;
			ga_st_type=st_type;
			ga_sel_date=sel_date;
			ga_sel_fac=sel_fac;
			ga_pt_id=pt_id;
			$.ajax({url:'get_ap_ids_by_patient.php',type:'POST',data:'pt_id='+pt_id+'&sel_date='+sel_date, complete:get_ap_ids_by_patient_and_sel_date});
		}
		else
		{
			getchange_status(ap_id, st_type, sel_date, sel_fac, pt_id);
		}
		
	}
	
	if(MD_API=='On'){
		//CODE ADDED TO SEND INFO TO API :
		var sel_proc_name=$('#sel_proc_id option:selected').text();	
		$.ajax({
			url: 'sending_info_to_api.php',
			type: "POST",
			data: 'pt_id='+pt_id+'&ap_id='+ap_id+'&st_type='+st_type+'&sel_proc_name='+sel_proc_name,
			success: function(resp){
			}
		});	
	}
}

function applyCOtrue(ga_ap_id, st_type)
{
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	var ap_id = $("#global_context_apptid").val();
	if(document.getElementById("show_payment_box_chk_out").value == "check out")
	{
		top.popup_win("../scheduler/common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pat_id+"&sch_id="+ap_id,"width=1305,scrollbars=0,height=600,top=100,left=10,resizable=yes");
	}
	getchange_status(ga_ap_id, st_type, sel_date, sel_fac, pat_id);
}
function applyCOFalse(st_type)
{
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	var ap_id = $("#global_context_apptid").val();
	if(document.getElementById("show_payment_box_chk_out").value == "check out")
	{
		top.popup_win("../scheduler/common/check_in_out_payment.php?search=true&frm_status=show_check_out&ci_pid="+pat_id+"&sch_id="+ap_id,"width=1305,scrollbars=0,height=600,top=100,left=10,resizable=yes");
	}
	getchange_status(ap_id, st_type, sel_date, sel_fac, pat_id);
}

function get_ap_ids_by_patient_and_sel_date(respData)
{
    appt_ids_result=$.parseJSON(respData.responseText);
    if(appt_ids_result.length > 1)
	{
		var msg="Is Check-in be applied to the other appointments of the same patient";
		var trueFun="applyCItrue('"+appt_ids_result +"')";
		var falseFun="applyCIFalse()";
		top.fancyConfirm(msg,"", "window.top.fmain."+trueFun,"window.top.fmain."+falseFun);
	}
	else
	{
		newPatient_info(ga_pt_id, ga_ap_id);
		if(document.getElementById("show_ci_demographics").value == "yes" || !$("#CHECKIN_ON_DONE").val() )
		{
			getchange_status(ga_ap_id, ga_st_type, ga_sel_date, ga_sel_fac, ga_pt_id);
		}
	}
}

function applyCItrue(appt_ids_result)
{
	newPatient_info(ga_pt_id, ga_ap_id);
	if(document.getElementById("show_ci_demographics").value == "yes" || !$("#CHECKIN_ON_DONE").val())
	{
		getchange_status(appt_ids_result, ga_st_type, ga_sel_date, ga_sel_fac, ga_pt_id);
	}
}
function applyCIFalse()
{
	newPatient_info(ga_pt_id, ga_ap_id);
	if(document.getElementById("show_ci_demographics").value == "yes" || !$("#CHECKIN_ON_DONE").val())
	{
		getchange_status(ga_ap_id, ga_st_type, ga_sel_date, ga_sel_fac, ga_pt_id);
	}
}

function saveFirstAvailable(keep_orignal,schedule_id, sel_month, sel_week, sel_time)
{
	//saving first available request
	var reason = $("#global_apptactreason").val();
	
	var send_uri = "saveFirstAvailable.php?keep_orignal="+keep_orignal+"&sel_month="+sel_month+"&sel_week="+sel_week+"&sel_time="+sel_time+"&sch_id="+schedule_id+"&reason="+reason+"&sid="+Math.random();
	if(is_remote_server() == true)
	{
		sever_id_val = get_server_id();
		rN = Math.random();
		reqTaskArray = {"sch":{"req_mode":"change_appt_status","server_id":sever_id_val,"keep_orignal":keep_orignal,"sel_month":sel_month,"sel_week":sel_week,"sel_time":sel_time,"sch_id":schedule_id,"reason":reason,"sid":rN}};
		send_uri = gl_api_addr+"?taskArray="+ju_encode_reqArr(reqTaskArray);
	}	
	$.ajax({
		url: send_uri,
		success: function(resp){
			$("#global_apptactreason").val("");
			if(is_remote_server() == true)
			{
				resp = json_resp_handle(resp);				
			}				
		}
	});	
}


function getchange_status(schedule_id, chg_to, dt, loca, id){ 
               	
	//alert("getchangedstatus.php?loca="+loca+"&sch_id="+schedule_id+"&chg_to="+chg_to+"&dt="+dt+"&patId="+id+"&sid="+Math.random());
	var reason = $("#global_apptactreason").val();
	var send_uri = "getchangedstatus.php?loca="+loca+"&sch_id="+schedule_id+"&chg_to="+chg_to+"&dt="+dt+"&patId="+id+"&reason="+reason+"&keepOrg="+firstAvail_keepOrg+"&sid="+Math.random();
		
	$.ajax({
		url: send_uri,
		success: function(resp){
			$("#global_apptactreason").val("");
			if(is_remote_server() == true)
			{
				resp = json_resp_handle(resp);				
			}				
			ids = resp.split("-");
			var curDate = ids[3]+'-'+ids[4]+'-'+ids[5];
			if(ids[1] == "11"){
				if(document.getElementById("show_payment_box_chk_out").value != "check out"){
					//top.core_redirect_to("Accounting", "../accounting/accountingTabs.php");
					top.change_main_Selection(top.document.getElementById('AccountingSB'));
				}else{
					pre_load_front_desk(ids[0], ids[2], false);	
					load_appt_schedule(curDate, ids[6], '', "nonono");
				}
			}else{
				pre_load_front_desk(ids[0], ids[2], false);	
				load_appt_schedule(curDate, ids[6], '', "nonono");
			}
		}
	});
}

function showPateintOtherTxtBox(obj1, obj2, val, otherPatientStatusVal){
	var obj1Id = obj1;
	var obj2Id = obj2;
	var allowVal = val;
	var arrAllowVal = allowVal.split('-');
	for(var a=0; a < arrAllowVal.length; a++ ){
		if(document.getElementById(obj1Id).value == arrAllowVal[a]){
			//document.getElementById(obj2Id).style.display = 'block';
			//var statusTdData = "<input name=\"otherPatientStatus\" id=\"otherPatientStatus\" style=\"display:block;\" class=\"form-control\" value=\""+otherPatientStatusVal+"\" />";
			$("#otherPatientStatus").val(otherPatientStatusVal);
			$("#otherPatientStatus").css('display','inline-block');
			//document.getElementById('tdOtherPatientStatus').innerHTML = statusTdData;
			a = arrAllowVal.length;
		}else{
			//document.getElementById(obj2Id).style.display = 'none';
			//var statusTdData = "<input name=\"otherPatientStatus\" id=\"otherPatientStatus\" style=\"display:none;\" value=\""+otherPatientStatusVal+"\"  class=\"form-control\"/>";
			//document.getElementById('tdOtherPatientStatus').innerHTML = statusTdData;
			//document.getElementById('tdOtherPatientStatus').style.display = 'inline-block';
			
			
			$("#otherPatientStatus").val(otherPatientStatusVal);
			$("#otherPatientStatus").css('display','none');
			$("#tdOtherPatientStatus").css('display','inline-block');
		}
	}
	if(document.getElementById(obj1Id).value == 'Deceased'){
		$("#dod_patient_td").css('display','inline-block');
		$("#tdOtherPatientStatus").css('display','none');
	}else{
		$("#dod_patient").val('');
		$("#dod_patient_td").css('display','none');
	}
}

function showEditAddress(mode){
	if(mode == "open"){
		//document.getElementById("display_area").style.display = "none";
		//document.getElementById("editable_area").style.display = "block";
		$('#editable_area').modal('show');
	}
	if(mode == "close"){
		//document.getElementById("display_area").style.display = "block";
		//document.getElementById("editable_area").style.display = "none";
		$('#editable_area').modal('hide');
	}
}

function before_save_changes(pt_id, ap_id,fac_type){

	if(!pt_id) pt_id = "";
	if(!ap_id) ap_id = "";
	if( pt_id ) {
		
		if( $("#elem_patientStatus").length > 0 )
		{
			var pt_status = $("#elem_patientStatus").val();
			var prev_pt_status = $("#elem_patientStatus").data('prev-val');
			if( pt_status == 'Deceased' && prev_pt_status != 'Deceased' ) {
				msg = "Patient status changed to deceased.<br>All future appointments will be canceled.";
				window.top.fAlert(msg,'',"top.fmain.save_changes('"+pt_id+"', '"+ap_id+"','"+fac_type+"')",'','','Ok',true);
			}
			else {
				save_changes(pt_id, ap_id,fac_type)	
			}
		}
		else {
			save_changes(pt_id, ap_id,fac_type)
		}
	}
}
/*
Function: save_changes
Purpose: to save_changes from front desk
Author: AA
*/
function save_changes(pt_id, ap_id,fac_type){
	if(!pt_id) pt_id = "";
	if(!ap_id) ap_id = "";
	
	if(pt_id != ""){
		//patient specific data
		var pt_doctor_id = ($("#sel_fd_provider").length !== 0) ? $("#sel_fd_provider").val() : "";
		
		var pt_pcp_phy_id = ($("#pcp_id").length !== 0) ? $("#pcp_id").val() : "";
		var pt_pcp_phy = ($("#pcp_name").length !== 0) ? escape($("#pcp_name").val()) : "";
		var hidd_pt_pcp_phy = ($("#hidd_pcp_name").length !== 0) ? escape($("#hidd_pcp_name").val()) : "";

		var pt_ref_phy_id = ($("#front_primary_care_id").length !== 0) ? $("#front_primary_care_id").val() : "";
		var pt_ref_phy = ($("#front_primary_care_name").length !== 0) ? escape($("#front_primary_care_name").val()) : "";
		var hidd_pt_ref_phy = ($("#hidd_front_primary_care_name").length !== 0) ? escape($("#hidd_front_primary_care_name").val()) : "";
		
		var pt_street1 = ($("#frontAddressStreet").length !== 0) ? escape($("#frontAddressStreet").val()) : "";
		var pt_street2 = ($("#frontAddressStreet2").length !== 0) ? escape($("#frontAddressStreet2").val()) : "";
		var pt_city = ($("#frontAddressCity").length !== 0) ? escape($("#frontAddressCity").val()) : "";
		var pt_state = ($("#frontAddressState").length !== 0) ? escape($("#frontAddressState").val()) : "";
		var pt_zip = ($("#frontAddressZip").length !== 0) ? escape($("#frontAddressZip").val()) : "";
		var pt_zip_ext = ($("#frontAddressZip_ext").length !== 0) ? escape($("#frontAddressZip_ext").val()) : "";

		var pt_email = ($("#email").length !== 0) ? escape($("#email").val()) : "";
		var pt_photo_ref = ($("#photo_ref").is(":checked")==true) ? 1 : 0;

		var pt_home_ph = ($("#phone_home").length !== 0) ? escape($("#phone_home").val()) : "";
		var pt_work_ph = ($("#phone_biz").length !== 0) ? escape($("#phone_biz").val()) : "";
		var pt_cell_ph = ($("#phone_cell").length !== 0) ? escape($("#phone_cell").val()) : "";
		
		//hidden fields regarding demographics change log/HX entry
		var hidd_prev_pt_street1 = ($("#hidd_prev_frontAddressStreet").length !== 0) ? escape($("#hidd_prev_frontAddressStreet").val()) : "";
		var hidd_prev_pt_street2 = ($("#hidd_prev_frontAddressStreet2").length !== 0) ? escape($("#hidd_prev_frontAddressStreet2").val()) : "";
		var hidd_prev_pt_city = ($("#hidd_prev_frontAddressCity").length !== 0) ? escape($("#hidd_prev_frontAddressCity").val()) : "";
		var hidd_prev_pt_state = ($("#hidd_prev_frontAddressState").length !== 0) ? escape($("#hidd_prev_frontAddressState").val()) : "";
		var hidd_prev_pt_zip = ($("#hidd_prev_frontAddressZip").length !== 0) ? escape($("#hidd_prev_frontAddressZip").val()) : "";
		var hidd_prev_pt_zip_ext = ($("#hidd_prev_frontAddressZip_ext").length !== 0) ? escape($("#hidd_prev_frontAddressZip_ext").val()) : "";
		var hidd_prev_pt_email = ($("#hidd_prev_email").length !== 0) ? escape($("#hidd_prev_email").val()) : "";
		var hidd_prev_pt_home_ph = ($("#hidd_prev_phone_home").length !== 0) ? escape($("#hidd_prev_phone_home").val()) : "";
		var hidd_prev_pt_work_ph = ($("#hidd_prev_phone_biz").length !== 0) ? escape($("#hidd_prev_phone_biz").val()) : "";
		var hidd_prev_pt_cell_ph = ($("#hidd_prev_phone_cell").length !== 0) ? escape($("#hidd_prev_phone_cell").val()) : "";
		
		var hidd_prev="&hidd_prev_pt_street1="+ hidd_prev_pt_street1 +"&hidd_prev_pt_street2="+ hidd_prev_pt_street2 +"&hidd_prev_pt_city="+ hidd_prev_pt_city +"&hidd_prev_pt_state="+ hidd_prev_pt_state +"&hidd_prev_pt_zip="+ hidd_prev_pt_zip +"&hidd_prev_pt_zip_ext="+ hidd_prev_pt_zip_ext +"&hidd_prev_pt_email="+ hidd_prev_pt_email +"&hidd_prev_pt_home_ph="+ hidd_prev_pt_home_ph +"&hidd_prev_pt_work_ph="+ hidd_prev_pt_work_ph +"&hidd_prev_pt_cell_ph="+ hidd_prev_pt_cell_ph;
		
		var pt_status = ($("#elem_patientStatus").length !== 0) ? $("#elem_patientStatus").val() : "";
		var pt_other_status = ($("#otherPatientStatus").length !== 0) ? escape($("#otherPatientStatus").val()) : "";
		var pt_dod_patient = ($("#dod_patient").length !== 0) ? escape($("#dod_patient").val()) : "";

		//appointment specific data
		if(ap_id != ""){
			var ap_routine_exam = ($("#chkRoutineExam").length !== 0) ? $("#chkRoutineExam").val() : "";
			var ap_ins_case_id = ($("#choose_prevcase").length !== 0) ? $("#choose_prevcase").val() : "";
			var ap_notes = ($("#txt_comments").length !== 0) ? encodeURIComponent($("#txt_comments").val()) : "";
			
			var pri_eye_site = ($("#pri_eye_site").length !== 0) ? $("#pri_eye_site").val() : "";
			var sec_eye_site = ($("#sec_eye_site").length !== 0) ? $("#sec_eye_site").val() : "";
			var ter_eye_site = ($("#ter_eye_site").length !== 0) ? $("#ter_eye_site").val() : "";
			
			var ap_pickup_time = ($("#pick_up_time").length !== 0) ? escape($("#pick_up_time").val()) : "";
			var ap_arrival_time = ($("#arrival_time").length !== 0) ? escape($("#arrival_time").val()) : "";
			var ap_procedure = ($("#sel_proc_id").length !== 0) ? $("#sel_proc_id").val() : "";
			var sec_ap_procedure = ($("#sec_sel_proc_id").length !== 0) ? $("#sec_sel_proc_id").val() : "";
			var ter_ap_procedure = ($("#ter_sel_proc_id").length !== 0) ? $("#ter_sel_proc_id").val() : "";
		}
		var facility_type_provider = ($("#facility_type_provider").length !== 0) ? escape($("#facility_type_provider").val()) : "";
		if(typeof(fac_type)!='undefined' && fac_type!='' && fac_type!='0' && facility_type_provider==""){
			top.fAlert("Please select Surgeon","",$("#facility_type_provider").focus());
			top.show_loading_image("hide");
			return false;
		}
		if(typeof(fac_type)!='undefined' && (fac_type=='' || fac_type=='0')){
		facility_type_provider = "";
		}
		var appt_duration = ($("#appt_duration").length !== 0) ? escape($("#appt_duration").val()) : "";
		var chk_prev_slot_val = ($("#chk_prev_slot_val").length !== 0) ? escape($("#chk_prev_slot_val").val()) : "";
		var slot_time_changed=0;var add_appt_duration="";
		
		add_appt_duration="&appt_duration="+appt_duration;
		
		var referral = ($("input[type=checkbox]#sa_ref_management").is(":checked") ? 1 : 0);
		var ref_management = "&pt_referral="+referral;
		if(chk_prev_slot_val!='' && appt_duration!=''){
			if(chk_prev_slot_val!=appt_duration){slot_time_changed=1;}
		}
		
        var sa_verification = ($("input[type=checkbox]#sa_verification_req").is(":checked") ? 1 : 0);
        var verification_req = "&pt_verification="+sa_verification;
		
		var send_uri = "save_changes.php?save_type=save&pt_id="+pt_id+"&ap_id="+ap_id+"&pt_doctor_id="+pt_doctor_id+"&pt_pcp_phy_id="+pt_pcp_phy_id+"&pt_pcp_phy="+pt_pcp_phy+"&pt_ref_phy_id="+pt_ref_phy_id+"&pt_ref_phy="+pt_ref_phy+"&hidd_ref="+hidd_pt_ref_phy+"&hidd_pcp="+hidd_pt_pcp_phy+"&pt_street1="+pt_street1+"&pt_street2="+pt_street2+"&pt_city="+pt_city+"&pt_state="+pt_state+"&pt_zip="+pt_zip+"&pt_zip_ext="+pt_zip_ext+"&pt_email="+pt_email+"&pt_photo_ref="+pt_photo_ref+"&pt_home_ph="+pt_home_ph+"&pt_work_ph="+pt_work_ph+"&pt_cell_ph="+pt_cell_ph+"&pt_status="+pt_status+"&pt_other_status="+pt_other_status+"&pt_dod_patient="+pt_dod_patient+"&ap_routine_exam="+ap_routine_exam+"&ap_ins_case_id="+ap_ins_case_id+"&ap_notes="+ap_notes+"&pri_eye_site="+pri_eye_site+"&sec_eye_site="+sec_eye_site+"&ter_eye_site="+ter_eye_site+"&ap_pickup_time="+ap_pickup_time+"&ap_arrival_time="+ap_arrival_time+"&ap_procedure="+ap_procedure+"&sec_ap_procedure="+sec_ap_procedure+"&ter_ap_procedure="+ter_ap_procedure+"&facility_type_provider="+facility_type_provider+add_appt_duration+ref_management+verification_req+hidd_prev;
		//return false;
		$.ajax({
			url: send_uri,
			type: "POST",
			success: function(resp){
				var arr_resp = resp.split("~");
				if(arr_resp[0] == "save"){
					if($('#editable_area').hasClass('in'))
					{
						$('#editable_area').on('hidden.bs.modal', function () {
							reload_on_save(pt_id, ap_id, false, ap_procedure, sec_ap_procedure, ter_ap_procedure, slot_time_changed, arr_resp);
						});
						$('#editable_area').modal('hide');//hide pt info edit div
					}else {reload_on_save(pt_id, ap_id, false, ap_procedure, sec_ap_procedure, ter_ap_procedure, slot_time_changed, arr_resp);}
				}

				if( arr_resp[3] > 0 ) { 
					if( arr_resp[2]) {
						load_appt_schedule(arr_resp[2], arr_resp[1], ap_id, false);
					}
				}

			}
		});
	}
}
function reload_on_save(pt_id, ap_id, fal, ap_procedure, sec_ap_procedure, ter_ap_procedure, slot_time_changed, arr_resp)
{
	pre_load_front_desk(pt_id, ap_id, fal); 
	if($("#global_apptsecpro").val() == 0) {$("#global_apptsecpro").val("");} 
	if($("#global_apptterpro").val() == 0) {$("#global_apptterpro").val("");}

	if((ap_procedure != "" && $("#global_apptpro").val() != "" && $("#global_apptpro").val() != ap_procedure) || ($("#global_apptsecpro").val() != sec_ap_procedure) || ($("#global_apptterpro").val() != ter_ap_procedure) || slot_time_changed==1){
		load_appt_schedule(arr_resp[2], arr_resp[1], "", "", false);
	}
}
function drag_name(ap_id, pt_id, mode, e){

	if( check_deceased() ) return false;

	if(!ap_id) ap_id = "";
	if(!pt_id) pt_id = "";
	if(!e) e = window.event;
	
	if(ap_id == "get") TestOnMenu();
	if(pt_id == "get") pt_id = $("#global_context_ptid").val();
	if(ap_id == "get") ap_id = $("#global_context_apptid").val();	

	$("#global_ptid").val(pt_id);
	$("#global_apptid").val(ap_id);
	$("#global_apptact").val(mode);

	$("#appt_drag").addClass("sc_title_font");
	$("#appt_drag").css("backgroundColor", "");			
	$("#appt_drag").width("320");
	$("#appt_drag").css("top", e.clientY);
	$("#appt_drag").css("left", e.clientX);
	
	var sel_proc_id = $("#sel_proc_id").val();
	var proc_lbls=$("#sel_proc_id").find('option:selected').attr('data-labels');
	if(proc_lbls)
	{
		proc_lbls_arr = proc_lbls.split('~:~');
	}
	var sec_sel_proc_id = $('#sec_sel_proc_id').val();
	var ter_sel_proc_id = $('#ter_sel_proc_id').val();
	
	var send_uri = "schedule_new_tooltip.php?tool_sch_id="+ap_id+"&pate_id="+pt_id+"&sel_proc_idR="+sel_proc_id+"&sec_sel_proc_id="+sec_sel_proc_id+"&ter_sel_proc_id="+ter_sel_proc_id;
		
	$.ajax({
		url: send_uri,
		type: "GET",
		success: function(resp){
			if(is_remote_server() == true)
			{
				resp = json_resp_handle(resp);				
			}			
			$("#appt_drag").html(resp);
			$("#appt_drag").css("display", "block");
			//document.attachEvent('onmousemove', move_trail);
			$(document).bind("mousemove", move_trail);
		}
	});
}

function move_trail(){
	window.status = window.event.clientX + "," + window.event.clientY
	$("#appt_drag").css("top", window.event.clientY - 5);
	$("#appt_drag").css("left", window.event.clientX + 20);
}

function keyPressHandler(){
	if(event.keyCode==27){
		$(document).unbind("mousemove", move_trail);
		$("#global_apptact").val('');
		hide_tool_tip();
	}
}
//jquery function to handle ESCAPE key pressed because above function does work in IE only
$(document).keydown(function(e) {
    if (e.keyCode == 27) {
		try{
		   $(document).unbind("mousemove", move_trail);
			$("#global_apptact").val('');
			hide_tool_tip();
		}catch(e){//do nothing
		}
    }
});
function hide_tool_tip(){
	$("#appt_drag").css("display", "none");
}

function pop_menu_time(ap_fac, ap_doc, ap_sttm, ap_stdt, mode, ap_lbty, ap_lbtx, ap_lbcl, ap_tmp_id){
	//alert(ap_lbty+", "+ap_lbtx+", "+ap_lbcl);
	if(mode == "open" || mode == "on"){
		document.getElementById("ContextMenu_1_blk_block").style.display = "inline-block";
		document.getElementById("ContextMenu_1_blk_open").style.display = "none";
		document.getElementById("ContextMenu_1_blk_label").style.display = "inline-block";
	}else if(mode == "off"){
		document.getElementById("ContextMenu_1_blk_block").style.display = "none";
		document.getElementById("ContextMenu_1_blk_open").style.display = "inline-block";
		document.getElementById("ContextMenu_1_blk_label").style.display = "none";
	}else{
		document.getElementById("ContextMenu_1_blk_block").style.display = "inline-block";
		document.getElementById("ContextMenu_1_blk_open").style.display = "inline-block";
		document.getElementById("ContextMenu_1_blk_label").style.display = "inline-block";
	}
	
	if($.trim(ap_lbty) != "" && ap_lbtx != "")
	{
		$("#ContextMenu_1_blk_remove_label").css({'display':'inline-block'});
	}
	else
	{
		$("#ContextMenu_1_blk_remove_label").css({'display':'none'});				
	}
	
	$("#global_context_slsttm").val(ap_sttm);
	$("#global_context_sldoc").val(ap_doc);
	$("#global_context_slfac").val(ap_fac);
	$("#global_context_slstdt").val(ap_stdt);
	$("#global_context_apptlbty").val(ap_lbty);
	$("#global_context_apptlbtx").val(ap_lbtx);
	$("#global_context_apptlbcl").val(ap_lbcl);
	$("#global_context_appt_tmp_id").val(ap_tmp_id);
	
	if (window.event.button == 2){
		var evt = window.event || event;

		var rqOffset = 0;
		if(document.getElementById('mn1_1'))
		{
			rqOffset = document.getElementById('mn1_1').getBoundingClientRect().left;	
		}		
		var left_offset = Math.abs(parseInt($("#lyr1").css('left')));
		var cony = (evt.clientX + left_offset) - rqOffset;
		var maxy = (evt.clientY + $("#mn1_1").scrollTop()) - 110;			

		//================Scroll issue in case of Safari and Mac===================//
		if(parseInt(jQuery.browser.version)==534 || (parseInt(jQuery.browser.version)==537) || (parseInt(jQuery.browser.version)==600) || (parseInt(jQuery.browser.version)==601)){
			var scroll_hei=$("#mn1_1").scrollTop();
			if(parseInt(scroll_hei)>0){
				scroll_hei=parseInt(scroll_hei);
			}
			var topheight=parseInt(maxy+scroll_hei);
			var elemObjAvail = $('#sch_left_portion').css('display');
			if(elemObjAvail=='block'){
			cony=parseInt(cony-580);
			}
			maxy=parseInt(topheight-120);
		}
		//code to check if pop menu going out of window width
		var posCheck=parseInt(window.innerWidth)-parseInt($("#mn1_1").width());
		posCheck=parseInt(posCheck)+340+parseInt(cony);

		if(posCheck>window.innerWidth){
			cony-=(parseInt(posCheck)-parseInt(window.innerWidth))+20;
		}
		//=========================================================================//
		if(mode == "block"){
			TestOnMenu();			
			ToggleContext_2_blk_only(cony, maxy);
		}else{
			TestOnMenu();
			ToggleContext_2_blk(cony, maxy);
		}
		document.oncontextmenu = blank_function;
	}else{
		TestOnMenu();
	}
}

function set_replace_label(req_lbl)
{
	$("#global_replace_lbl").val(req_lbl);	
}

function ToggleContext_2_blk(cony,maxy){		
	document.getElementById("ContextMenu_blk").style.position = 'absolute';
	document.getElementById("ContextMenu_blk").style.display = 'block';
	document.getElementById("ContextMenu_blk").style.pixelLeft = cony;		
	document.getElementById("ContextMenu_blk").style.pixelTop = maxy;
	IsOn = true;		
	window.event.returnValue = false;	
	var bro_ver=navigator.userAgent.toLowerCase();
	if(bro_ver.search("chrome")>1){
		$("#ContextMenu_blk").css({"display":"inline-block",top: maxy, left: cony});
	}			
}

function ToggleContext_2_blk_only(cony,maxy){	
	//document.getElementById("ContextMenu_blk_only").style.width = "100px";		
	document.getElementById("ContextMenu_blk_only").style.position = 'absolute';
	document.getElementById("ContextMenu_blk_only").style.display = 'block';
	document.getElementById("ContextMenu_blk_only").style.pixelLeft = cony;		
	document.getElementById("ContextMenu_blk_only").style.pixelTop = maxy;
	
	var bro_ver=navigator.userAgent.toLowerCase();
	if(bro_ver.search("chrome")>1){
		$("#ContextMenu_blk_only").css({"display":"inline-block",top: maxy, left: cony});
	}	
	IsOn = true;		
	window.event.returnValue = false;
}

function todo_options(ap_doc, ap_fac, mode){
	if (window.event.button == 2){
		TestOnMenu();
		var load_dt = get_selected_date();
		$.ajax({ 
			url: "todo_options.php?load_dt="+load_dt+"&ap_doc="+ap_doc+"&ap_fac="+ap_fac,
			success: function(resp){
				var arr_resp = resp.split("~~~~~");
				
				$("#todo_div").modal("show");
				$("#todo_content").html(arr_resp[1]);
				$("#todo_date").html(arr_resp[0]);
				$("#blk_lk_loca").selectpicker("refresh");
			}
		});
		document.oncontextmenu = blank_function;
	}else {
		TestOnMenu();
	}
}

function save_todo(){		
		var loca = selectedValuesStr("blk_lk_loca");
		if(loca=='')
		{
			top.fAlert('Select any facility to proceed')
			return false;
		}else {

			var load_dt = get_selected_date();
	
			
			var phy_id = $("#phy_id").val();
			var time_from_hour = $("#todo_time_from_hour").val();
			var time_from_mins = $("#todo_time_from_mins").val();
			var ap1 = $("#todo_ap1").val();
			var time_to_hour = $("#todo_time_to_hour").val();
			var time_to_mins = $("#todo_time_to_mins").val();
			var ap2 = $("#todo_ap2").val();
			var send_uri = "save_todo.php?load_dt="+load_dt+"&phy_id="+phy_id+"&loca="+loca+"&time_from_hour="+time_from_hour+"&time_from_mins="+time_from_mins+"&ap1="+ap1+"&time_to_hour="+time_to_hour+"&time_to_mins="+time_to_mins+"&ap2="+ap2;
			//alert(send_uri);
			$.ajax({
				url: send_uri,
				success: function(resp){
					var arr_resp = resp.split("~~~~~");
					load_appt_schedule(arr_resp[0], arr_resp[1], "", "nonono", false); //dt and dayname in response
				}
			});
		}
}

function change_time(chg_to){	
	var sl_sttm = $("#global_context_slsttm").val();
	var sl_doc = $("#global_context_sldoc").val();
	var sl_fac = $("#global_context_slfac").val();
	var sl_stdt = $("#global_context_slstdt").val();
	
	//alert(sl_sttm+" "+sl_doc+" "+sl_fac+" "+sl_stdt);	
	TestOnMenu();
	var win_change_time = window.open("change_time.php?sl_doc="+sl_doc+"&sl_fac="+sl_fac+"&sl_sttm="+sl_sttm+"&sl_stdt="+sl_stdt+'&act='+chg_to, "block_open_time", "toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=400,height=220,left=200,top=120");
	win_change_time.focus();
}

function load_appt_hx(){
	//document.getElementById("frontdesk3").style.display = "none";

	var pat_id = $("#global_ptid").val();
	top.show_loading_image("show");
	$.ajax({
		url: "common/appointment_status.php?pat_id="+pat_id+"&mode=tiny",
		type: "GET",
		success: function(resp){			
			//change title
			$("#frontdesk_mdl .modal-title").html('Appointment History');			
			//add body html
			$("#frontdesk_mdl .modal-body").html(resp);			
			//show button in footer
			$("#frontdesk_mdl .modal-footer #print_app").css("display","inline-block");
			//show modal
			$("#frontdesk_mdl").modal('show');
			top.show_loading_image("hide");
		}
	});
}

function close_patient_appoitment(){
	var pat_id = $("#global_ptid").val();
	var sch_id = $("#global_apptid").val();
	pre_load_front_desk(pat_id, sch_id, false);	
}

function openPrintWindow(){
	var pat_id = $("#global_ptid").val();
	url="print-appointment-history.php?pat_id="+pat_id;
	window.open(url,'printAppt','dependent=yes,toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=200,left=30,top=10');
}

function pop_menu(ap_id, ap_fac, ap_doc, ap_sttm, ap_stdt, pt_id, mode, ap_lbty, ap_lbtx, ap_lbcl, ap_iolink_csi, ap_iolink_practice,ap_iolink_ocfi,askforreason){
	//alert(ap_lbty+", "+ap_lbtx+", "+ap_lbcl);
	if(!mode) mode = "";
	if(!askforreason) askforreason = false;
	$("#global_context_ptid").val(pt_id);
	$("#global_context_apptid").val(ap_id);
	$("#global_context_apptsttm").val(ap_sttm);
	$("#global_context_apptdoc").val(ap_doc);
	$("#global_context_apptfac").val(ap_fac);
	$("#global_context_apptstdt").val(ap_stdt);

	$("#global_context_slsttm").val(ap_sttm);
	$("#global_context_sldoc").val(ap_doc);
	$("#global_context_slfac").val(ap_fac);
	$("#global_context_slstdt").val(ap_stdt);
	$("#global_context_apptlbty").val(ap_lbty);
	$("#global_context_apptlbtx").val(ap_lbtx);
	$("#global_context_apptlbcl").val(ap_lbcl);
	$("#global_iolink_connection_settings_id").val(ap_iolink_csi);
	$("#global_iolink_ocular_hx_form_id").val(ap_iolink_ocfi);

	$("#iolink_connection_span_id").css("display", "inline-block");
	$("#iolink_re_connection_span_id").css("display", "none");
	if(ap_iolink_csi!=0 && ap_iolink_csi!='') {
		$("#iolink_connection_span_id").css("display", "none");
		$("#iolink_re_connection_span_id").css("display", "inline-block");
		$("#iolink_remove_connection_id").text("Remove from iASC Link - "+ap_iolink_practice);
		$("#iolink_resyncro_connection_id").text("Resynchronise with iASC Link - "+ap_iolink_practice);
	}
	if(window.event.button==2){
		if(mode == "weekly"){
			
		}else{
			if($("#global_ptid").val() == pt_id && $("#global_apptid").val() == ap_id){
                if(askforreason==true){pre_load_front_desk(pt_id, ap_id,'');TestOnMenu();return false};
			}else{
				pre_load_front_desk(pt_id, ap_id,'');
                if(askforreason==true){TestOnMenu();return false};
			}
		}
		evt=window.event || event;
	
		//if(parseInt(jQuery.browser.version) >= 10)
//		{
//			var cony = evt.x;
//			var maxy = evt.y;			
//		}
//		else
//		{
			var rqOffset = 0;
			if(document.getElementById('mn1_1'))
			{
				rqOffset = document.getElementById('mn1_1').getBoundingClientRect().left;	
			}		
			var left_offset = Math.abs(parseInt($("#lyr1").css('left')));
			var cony = (evt.clientX + left_offset) - rqOffset;
			var maxy = (evt.clientY + $("#mn1_1").scrollTop()) - 110;		
		//}
					
		TestOnMenu();
		ToggleContext("ContextMenu", cony, maxy);	
		document.oncontextmenu = blank_function;
		// Stop an event to further propagate.
		evt=window.event;
		target = (evt.currentTarget) ? evt.currentTarget : evt.srcElement;
		
		evt.cancelBubble=true;
		evt.returnValue=false;
	}else{
		TestOnMenu();
	}
}

function set_init_timings(start_time, end_time, acronym, prov_id, fac_id)
{
	init_rs_date = $('#global_year').val()+'-'+ $('#global_month').val()+'-'+$('#global_date').val();
	$('#init_date_rs').attr({'value':init_rs_date});
	$('#init_st_time_rs').attr({'value':start_time});
	$('#init_et_time_rs').attr({'value':end_time});
	$('#init_acronym_rs').attr({'value':acronym});
	$('#init_prov_id').attr({'value':prov_id});
	$('#init_fac_id').attr({'value':fac_id});
}

function TestOnMenu(){		
	if($("#ContextMenu").get(0)){
		$("#ContextMenu").css("display", "none");
	}
	if($("#ContextMenu_blk").get(0)){
		$("#ContextMenu_blk").css("display", "none");
	}
	if($("#ContextMenu_opn").get(0)){
		$("#ContextMenu_opn").css("display", "none");
	}
	if($("#ContextMenu_blk_only").get(0)){
		$("#ContextMenu_blk_only").css("display", "none");
	}
	//if($("#todo_div").get(0)){
		$("#todo_div").modal("hide");
	//}

}

function ToggleContext(menu_name, cony, maxy){
	if(is_remote_server() == true){ if(cony == ""){ cony = 0;} cony = parseInt(cony,10); cony -= 70; }
	//document.getElementById(menu_name).style.width = "130px";		
	document.getElementById(menu_name).style.position = 'absolute';
	document.getElementById(menu_name).style.display = 'block';
	//================Scroll issue in case of Safari and Mac===================//
	if(parseInt(jQuery.browser.version)==534 || (parseInt(jQuery.browser.version)==537) || (parseInt(jQuery.browser.version)==600) || (parseInt(jQuery.browser.version)==601)){
		var scroll_hei=$("#mn1_1").scrollTop();
		if(parseInt(scroll_hei)>0){
			scroll_hei=parseInt(scroll_hei);
		}
		var topheight=parseInt(maxy+scroll_hei);
		var elemObjAvail = $('#sch_left_portion').css('display');
		if(elemObjAvail=='block'){
			cony=parseInt(cony-580);
		}
		maxy=parseInt(topheight-120);
	}
	
	//code to check if pop menu going out of window width
	var posCheck=parseInt(window.innerWidth)-parseInt($("#mn1_1").width());
	posCheck=parseInt(posCheck)+340+parseInt(cony);
	
	if(posCheck>window.innerWidth){
		cony-=(parseInt(posCheck)-parseInt(window.innerWidth))+20;
	}
		
	//=========================================================================//
	document.getElementById(menu_name).style.pixelLeft = cony;
	document.getElementById(menu_name).style.pixelTop = maxy;
	if((window.event.y) > 210){
		document.getElementById(menu_name).style.pixelTop = document.getElementById(menu_name).style.pixelTop - 80;			
	}
	var bro_ver=navigator.userAgent.toLowerCase();
	if(bro_ver.search("chrome")>1){
		$("#"+menu_name).css({"display":"inline-block",top: maxy, left: cony});
	}
	IsOn = true;		
	window.event.returnValue = false;
}

function blank_function(){
	return false;
}
var var_procedure_limit='';
var var_times_from='';
var var_eff_date_add='';
var var_loc='';
var var_ro1='';
var var_sch_tmp_id='';
var var_procedure='';
var var_label_type='';
var var_label_group='';
var var_is_valid_proc='';
var var_targetLabel='';
var var_fac_type_provider='';

function sch_drag_id(times_from, eff_date_add, loc, pro1, sch_tmp_id, procedure, label_type, is_valid_proc, targetLabel, procedure_limit, fac_type_provider, label_group){
	
	var_times_from=times_from;
	var_eff_date_add=eff_date_add;
	var_loc=loc;
	var_pro1=pro1;
	var_sch_tmp_id=sch_tmp_id;
	var_procedure=procedure;
	var_label_type=label_type;
	var_label_group=label_group;
	var_is_valid_proc=is_valid_proc;
	var_targetLabel=targetLabel;
	var_procedure_limit=procedure_limit;
	var_fac_type_provider=fac_type_provider;
	
	if(!procedure) procedure = "";
	if(!label_type) label_type = "";
	if(!is_valid_proc) is_valid_proc = "no";
	var sel_label='';
	var n=0;
	if(targetLabel)
	{
		n=targetLabel.indexOf(";");
	}
	if($('#ENABLE_SCHEDULER_RAIL_CHECK').val()==1)
	{
		
		if(targetLabel && label_type=='Procedure')
		{
			if(procedure=='')
			{
				if(n==-1)
				{
					procedure=targetLabel;
					is_valid_proc='yes';
				}else
				{
					procedure=targetLabel;
				}
			}
		}else if(targetLabel && procedure=='')
		{
			if(proc_lbls_arr)
			{
				//if we do have only on available label
				if(n==-1)
				{
					//if we unable to find matching label then forcefully replace first encountered label
					//amendment made on 4 july 16, on arun request
					procedure=targetLabel;	
				}
				else
				{
				
					var labelArr=targetLabel.split('; ');
					//check is there any matching procedure in selected slot and procedure
					for (var i = 0; i < labelArr.length; i++) {
						 if(labelArr[i])
						 {
							 for (var n = 0; n < proc_lbls_arr.length; n++) 
							 {
								if(proc_lbls_arr[n].toLowerCase()==labelArr[i].toLowerCase())
								{
									sel_label=proc_lbls_arr[n];
									break;
								}
							}
						}if(sel_label)break;
					}
					//if we unable to find matching label then forcefully replace first encountered label
					//amendment made on 4 july 16, on arun request
					if(!sel_label)sel_label=labelArr[0];
					if(!procedure)procedure=sel_label;
				}
			}
		}
		//alert(procedure);return false;
		if(n==-1 || n==0 || label_type!='Procedure')
		{
			$("#global_appttempproc").val(procedure);
		}
	}
	else
	{
		if(procedure=='')
		{
			if(n==-1)
			{
				procedure=targetLabel;
				is_valid_proc='yes';
			}else
			{
				procedure=targetLabel;
			}
		}
		$("#global_appttempproc").val(procedure);	
	}
	var save_type = $("#global_apptact").val();

	$.ajax({
		url: "match_enforced_proc.php?selected_proc="+$("#sel_proc_id").val()+"&landing_proc="+procedure+"&label_type="+label_type+"&label_group="+var_label_group,
		success: function(resp){
			if(resp == "no"){
				top.fAlert("Procedures do not match. This slot is reserved for "+procedure+".");
				return false;
			}			
			else if((resp == "yes" || resp == "schovrtrue" ) && save_type != ""){
				if(resp == "schovrtrue")
				{
					if($('#ENABLE_SCHEDULER_RAIL_CHECK').val()==1)
					{
						//if we have single procedure then move on, otherwise return to choose a label
						if(n==-1 || n==0)
						{
							var askMsg='This appointment replace its target label(s). Do you want to continue ?';
							top.fancyConfirm(askMsg,'','window.top.fmain.replaceLblConfirm(1)','window.top.fmain.replaceLblConfirm(2)');
							/*var cResult= confirm(askMsg);
							if(cResult==false)
							{
								return false;
							}*/
						}
						else
						{
							top.fAlert('No matching procedure found.');
							$(document).unbind("mousemove", move_trail);
							hide_tool_tip();
							return false;	
						}
					}
					else
					{
						var askMsg= 'This appointment replace its target label(s). Do you want to continue ?';
						top.fancyConfirm(askMsg,'','window.top.fmain.replaceLblConfirm(1)','window.top.fmain.replaceLblConfirm(2)');
					}
				}else{
				//call add appt function for default processing
				replaceLblConfirm(1);
				}
			}
		}
	});
}

function replaceLblConfirm(response)
{
	if(response==1)
	{
		var procedure_limit=var_procedure_limit;
		var times_from=var_times_from;
		var eff_date_add=var_eff_date_add;
		var loc=var_loc;
		var pro1=var_pro1;
		var sch_tmp_id=var_sch_tmp_id;
		var procedure=var_procedure;
		var label_type=var_label_type;
		var label_group=var_label_group;
		var is_valid_proc=var_is_valid_proc;
		var targetLabel=var_targetLabel;
		var fac_type_provider=var_fac_type_provider;
		
		var save_type = $("#global_apptact").val();
		
		$("#global_apptstid").val(sch_tmp_id);
		$("#global_apptsttm").val(times_from);
		$("#global_apptdoc").val(pro1);
		$("#global_apptfac").val(loc);
		$("#global_apptstdt").val(eff_date_add);

		//patient id
		var pat_id = $("#global_ptid").val();					//setting patient id
		var ap_id = $("#global_apptid").val();

		//document.detachEvent("onmousemove", move_trail);
		$(document).unbind("mousemove", move_trail);
		hide_tool_tip();
		//alert(is_valid_proc);
		//procedure id

		if($("#sel_proc_id").val() == "" && (procedure == "" || is_valid_proc == "no")){				
			$("#sel_proc_id").focus();
			top.fAlert("Please select Procedure.");
			return false;
		}else{
			var proc_id = $("#sel_proc_id").val();				//setting procedure id
		}
		if(fac_type_provider=="1"){
			if($("#facility_type_provider").val()==""){
				top.fAlert("Please select Surgeon","",$("#facility_type_provider").focus());
				top.show_loading_image("hide");
				return false;
			}
		}else{
			$("#facility_type_provider").val("");
		}
		if(pat_id != "" && pro1 != ""){
			var url_validity = "check_appt_validity.php?st_date=" + eff_date_add + "&pat_id=" + pat_id + "&st_time=" + times_from + "&sl_pro=" + proc_id + "&pro_id=" + pro1 + "&template_id=" + sch_tmp_id + "&fac_id=" + loc + "&querytype=" + save_type +"&procedure_limit="+ procedure_limit;		
			$.ajax({
				url: url_validity,
				success: function(resp){
					arr_resp = resp.split("~~~");
					if(arr_resp[0] == "y"){	
						btn1 = 'Yes';
						btn2 = 'No';

						func1 = 'validate_or_password';
						func2 = 'hideConfirmYesNo';

						if(arr_resp[1] == "y"){
							misc = "ASKPASSWORD";
							title = 'Admin Override Required!';
						}else{
							misc = "DONOTASKPASSWORD";
							title = 'Warning!';
						}
						//alert(title+", "+arr_resp[2]+", "+btn1+", "+btn2+", "+func1+", "+func2+", "+misc);
						scheduler_warning_disp(title, arr_resp[2], btn1, btn2, func1, func2, misc);
					}else{
						if(save_type == "reschedule"){
							//loading reasons
							var lr_url = "load_reasons.php?pt_id="+pat_id+"&ap_id="+ap_id;
							$.ajax({
								url: lr_url,
								success: function(resp){											
									var arr_resp = resp.split("~~~~~");
									var title = "Reschedule Reason";

									var msg ='';
									msg ='<div class="row">';
									msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
									msg +='</div>';

									msg +='<div class="row">';
									msg +='<div class="col-sm-12">';
									msg +='<div class="form-group">';
									msg +='<label for="">Reason:</label>';
									msg +='<select id="reschedule_reason" name="reschedule_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
									msg +='</div>';
									msg +='</div>';
									msg +='</div>';

									var btn1 = 'OK';
									var btn2 = 'Cancel';

									var misc = "DONOTASKPASSWORD";

									var func1 = 'save_reschedule_reason';
									var func2 = 'hideConfirmYesNo';

									scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
								}
							});
						}else{
							drag_drop();
						}
						return true;
					}
				}
			});
		}
	}
	else{
		hideConfirmYesNo();
	}
}

function save_reschedule_reason(){
	var reason = $("#reschedule_reason").val();
	if(reason == ""){
		top.fAlert("Please select a reason to continue.");
		return false;
	}else{
		if(reason == "Other"){
			reason = escape($("#OtherReason").val());
		}
		$("#global_apptactreason").val(reason);
		//alert($("#global_apptactreason").val());
		drag_drop();
		open_todo();			
	}
}

function save_cancellation_reason(mode){
	if(!mode) mode = "";
	var reason = $("#cancellation_reason").val();
	var fa_available_ch = $("#fa_available_rd").is(":checked");
	if(reason == "" && fa_available_ch == false){
		top.fAlert("Please select a reason to continue.");
		return false;
	}else{
		if(reason == "Other"){
			reason = escape($("#OtherReason").val());
		}
		$("#global_apptactreason").val(reason);
		var sel_date = get_selected_date();
		var sel_fac = get_selected_facilities();
		var pat_id = $("#global_context_ptid").val();
		var sch_id = $("#global_context_apptid").val();
		
		if(mode == "weekly"){
			getchange_status_weekly(sch_id, '18', sel_date, sel_fac, pat_id);
		}else{
			var send_status_code_val = '18';
			if(fa_available_ch == true)
			{
				send_status_code_val = '271';
			}
			//do confirm about to cancel same day future appt for patient in canes of cancel only
			if(send_status_code_val=='18')
			{
				$.ajax({
					url: 'get_ap_ids_by_patient.php',
					type: "POST",
					data: 'pt_id='+pat_id+'&sel_date='+sel_date+'&typ=cancel',
					success: function(resp){
						appt_ids_result=$.parseJSON(resp);
						if(appt_ids_result.length > 1)
						{
							var msg="Cancel – All other appointments for the day";
							var trueFun="applyCancelTrue('"+appt_ids_result +"','"+send_status_code_val +"')";
							var falseFun="applyCancelFalse('"+send_status_code_val +"')";
							top.fancyConfirm(msg,"", "window.top.fmain."+trueFun,"window.top.fmain."+falseFun);
						}
						else 
						{
							getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
						}
					}
				});
			}
			else 
			{
				getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
			}
			hideConfirmYesNo();
			open_todo();
		}
		hideConfirmYesNo();
	}
}

function applyCancelTrue(appt_ids_result, send_status_code_val)
{
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	
	getchange_status(appt_ids_result, send_status_code_val, sel_date, sel_fac, pat_id);
	hideConfirmYesNo();
	open_todo();
}

function applyCancelFalse(send_status_code_val)
{
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	var sch_id = $("#global_context_apptid").val();
	
	getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
	hideConfirmYesNo();
	open_todo();
}

function save_first_available_reason_todo(mode)
{
	if(!mode) mode = "";
	var keep_orignal = false;
	
	var sch_id = $("#global_context_apptid").val();
	
	if(mode == "weekly"){
		//this function is onhold will work after sorting else condition  ------------- NOTE
		//getchange_status_weekly(sch_id, '18', sel_date, sel_fac, pat_id);
	}else{
		//function to save first available criterian in new table
		saveFirstAvailable(1,sch_id, '', '', '');
		
	}
	
}

function save_first_available_reason(mode)
{
	if(!mode) mode = "";
	var reason = $("#cancellation_reason").val();//getting reason from list box
	var keep_orignal = $("#keep_sa").is(":checked");
	
	if(reason == "Other"){
		reason = escape($("#OtherReason").val());
	}
	$("#global_apptactreason").val(reason);
	var sel_month= $("#month").val()
	var sel_week= $("#week").val()
	var sel_time= $("#time").val()
	
	var sel_date = get_selected_date();
	var sel_fac = get_selected_facilities();
	var pat_id = $("#global_context_ptid").val();
	var sch_id = $("#global_context_apptid").val();
	
	if(mode == "weekly"){
		//this function is onhold will work after sorting else condition  ------------- NOTE
		//getchange_status_weekly(sch_id, '18', sel_date, sel_fac, pat_id);
	}else{
		var send_status_code_val = '271';
		//cal this function only if user have not selected keep present appointment option
		if(keep_orignal==false)
		{
			firstAvail_keepOrg=1;
			getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
			//function to save first available criterian in new table
			saveFirstAvailable(1,sch_id, sel_month, sel_week, sel_time);
			hideConfirmYesNo();
			open_todo();
		}
		else
		{
			firstAvail_keepOrg=0;
			getchange_status(sch_id, send_status_code_val, sel_date, sel_fac, pat_id);
			//function to save first available criterian in new table
			saveFirstAvailable(0,sch_id, sel_month, sel_week, sel_time);
			hideConfirmYesNo();
			open_todo();
		}
	}
	hideConfirmYesNo();
	
}
function show_hide_other(reason){
	if(reason == "Other"){
		display_block_none("OtherReasonContainer", "block");
		document.getElementById("OtherReasonContainer").focus();
	}else{
		display_block_none("OtherReasonContainer", "none");
	}
}

function scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc){
	var arrfunc1 = func1.split("::::");
	func1 = arrfunc1[0];
	var arg1 = arrfunc1[1];
	if(arg1 == "") arg1 = "1";
	$("#msgTitle").html("<div id=\"msgDiv-handle\">"+title+"</div>");
	var text_msg=msg+"<div class=\"clearfix\"></div><div class=\"sc_line\"></div><div id=\"OtherReasonContainer\" style=\"display:none;\"><input type=\"text\" id=\"OtherReason\" name=\"OtherReason\" class=\"form-control\" placeholder=\"Other Reason\"></div>";
	
	if(misc == "ASKPASSWORD"){
		text_msg += "<div class=\"clearfix\"></div><div>Admin Password: </div><div><input type=\"password\" id=\"AdminPass\" name=\"AdminPass\" class=\"form-control\"></div>";
	}
	
	$("#msgBody").html(text_msg);
	
	var btns= "<button type=\"button\" class=\"btn btn-success\" value=\""+btn1+"\"onClick=\"window."+func1+"('"+arg1+"')\">"+btn1+"</button>";
	btns+="<button type=\"button\" class=\"btn btn-danger\" value=\""+btn2+"\"onClick=\"window."+func2+"('-1')\">"+btn2+"</button>";
	
	$("#msgFooter").html(btns);
	//$("#msgDiv_scheduler").modal('show');
	show_custom_modal();
}

function show_custom_modal()
{
	$("#msgDiv_scheduler").draggable();
	$("#msgDiv_scheduler").show();
	$("#msgDiv_scheduler_overlay").show();
}

function hide_custom_modal()
{
	$("#msgDiv_scheduler").hide();
	$("#msgDiv_scheduler_overlay").hide();
}
function validate_or_password(mode){
	if(window.opener == "undefined")
	{
		top.show_loading_image("show");	
	}	
	//patient id
	var pat_id = $("#global_ptid").val();					//setting patient id
	var ap_id = $("#global_apptid").val();

	if(dgi("AdminPass")){
		if(dgi("AdminPass").value == ""){
			top.fAlert("Please enter password.");
			return false;
		}else{
			var hashMehtod=dgi("hash_method").value;
			if(hashMehtod=="MD5"){
				dgi("AdminPass").value=md5(dgi("AdminPass").value);
			}else{
				dgi("AdminPass").value=Sha256.hash(dgi("AdminPass").value);
			}
			url_pass_check = "check_or_password.php?AdminPass=" + dgi("AdminPass").value;
			$.ajax({ 
				url: url_pass_check,
				success: function(resp){
					//alert(resp);
					if(resp == "grant_access"){
						drag_drop();
						return true;
					}else if(resp == "revoke_access"){
						dgi("AdminPass").value = "";
						top.fAlert("Incorrect Password.");
					}
				}
			});
		}
	}else{
		var save_type = $("#global_apptact").val();
		if(save_type == "reschedule"){
			//loading reasons
			lr_url = "load_reasons.php?pt_id="+pat_id+"&ap_id="+ap_id;
			$.ajax({
				url: lr_url,
				success: function(resp){				
					var arr_resp = resp.split("~~~~~");
					var title = "Reschedule Reason";
					
					var msg ='';
					msg ='<div class="row">';
					msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
					msg +='</div>';
					
					msg +='<div class="row">';
					msg +='<div class="col-sm-12">';
					msg +='<div class="form-group">';
					msg +='<label for="">Reason:</label>';
					msg +='<select id="reschedule_reason" name="reschedule_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
					msg +='</div>';
					msg +='</div>';
					msg +='</div>';
					
					
					var btn1 = 'OK';
					var btn2 = 'Cancel';

					var misc = "DONOTASKPASSWORD";
					
					var func2 = 'hideConfirmYesNo';

					if(mode=='week') {	
						var func1 = 'save_reschedule_reason_weekly';
						scheduler_warning_disp_weekly(title, msg, btn1, btn2, func1, func2, misc);
					}else {
						var func1 = 'save_reschedule_reason';
						scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
					}
				}
			});
		}else{
			if(mode=='week') {
				addApptWeek();
			}else {
				drag_drop();
			}
		}
		return true;
	}
}

function hideConfirmYesNo(){
	
	$("#msgTitle").html('');
	$("#msgBody").html('');
	$("#msgFooter").html('');
	//$("#msgDiv_scheduler").hide();
	hide_custom_modal();
	$(document).unbind("onmousemove", move_trail);
	hide_tool_tip();
	$("#global_apptact").val('');
	top.show_loading_image("hide");	
}

function drag_drop(){
	var pt_id = $("#global_ptid").val();
	var ap_id = $("#global_apptid").val();
	var mode = $("#global_apptact").val();
	var tmp_id = $("#global_apptstid").val();
	var start_time = $("#global_apptsttm").val();
	var doctor_id = $("#global_apptdoc").val();
	var facility_id = $("#global_apptfac").val();
	var start_date = $("#global_apptstdt").val();
	var tempproc = encodeURIComponent($("#global_appttempproc").val());	

	var pt_fname =  $("#global_ptfname").val();
	var pt_lname =  $("#global_ptlname").val();
	var pt_mname =  $("#global_ptmname").val();
	var pt_emr = $("#global_ptemr").val();
	
	var init_date_rs = $('#init_date_rs').val();
	var init_st_time_rs=$('#init_st_time_rs').val();
	var init_et_time_rs=$('#init_et_time_rs').val();
	var init_acronym_rs=$('#init_acronym_rs').val();
	var init_provider_id = $('#init_prov_id').val();
	var init_fac_id = $('#init_fac_id').val();		
	
	var ap_act_reason = ($("#global_apptactreason").length !== 0) ? escape($("#global_apptactreason").val()) : "";
	//alert(ap_act_reason);
	
	if(pt_id != ""){
		//patient specific data
		var pt_doctor_id = ($("#sel_fd_provider").length !== 0) ? $("#sel_fd_provider").val() : "";
		var pt_pcp_phy_id = ($("#pcp_id").length !== 0) ? $("#pcp_id").val() : "";
		var pt_pcp_phy = ($("#pcp_name").length !== 0) ? escape($("#pcp_name").val()) : "";
		var pt_ref_phy_id = ($("#front_primary_care_id").length !== 0) ? $("#front_primary_care_id").val() : "";
		var pt_ref_phy = ($("#front_primary_care_name").length !== 0) ? escape($("#front_primary_care_name").val()) : "";
		var pt_street1 = ($("#frontAddressStreet").length !== 0) ? escape($("#frontAddressStreet").val()) : "";
		var pt_street2 = ($("#frontAddressStreet2").length !== 0) ? escape($("#frontAddressStreet2").val()) : "";
		var pt_city = ($("#frontAddressCity").length !== 0) ? escape($("#frontAddressCity").val()) : "";
		var pt_state = ($("#frontAddressState").length !== 0) ? escape($("#frontAddressState").val()) : "";
		var pt_zip = ($("#frontAddressZip").length !== 0) ? escape($("#frontAddressZip").val()) : "";
		var pt_zip_ext = ($("#frontAddressZip_ext").length !== 0) ? escape($("#frontAddressZip_ext").val()) : "";
		var pt_email = ($("#email").length !== 0) ? escape($("#email").val()) : "";
		var pt_photo_ref = ($("#photo_ref").is(":checked")==true) ? 1 : 0;
		var pt_home_ph = ($("#phone_home").length !== 0) ? escape($("#phone_home").val()) : "";
		var pt_work_ph = ($("#phone_biz").length !== 0) ? escape($("#phone_biz").val()) : "";
		var pt_cell_ph = ($("#phone_cell").length !== 0) ? escape($("#phone_cell").val()) : "";

		var pt_status = ($("#elem_patientStatus").length !== 0) ? $("#elem_patientStatus").val() : "";
		var pt_other_status = ($("#otherPatientStatus").length !== 0) ? escape($("#otherPatientStatus").val()) : "";
		var pt_dod_patient = ($("#dod_patient").length !== 0) ? escape($("#dod_patient").val()) : "";

		//appointment specific data
		var ap_routine_exam = ($("#chkRoutineExam").length !== 0) ? $("#chkRoutineExam").val() : "";
		var ap_ins_case_id = ($("#choose_prevcase").length !== 0) ? $("#choose_prevcase").val() : "";
		var ap_notes = ($("#txt_comments").length !== 0) ? encodeURIComponent($("#txt_comments").val()) : "";
		var ap_pickup_time = ($("#pick_up_time").length !== 0) ? escape($("#pick_up_time").val()) : "";
		var ap_arrival_time = ($("#arrival_time").length !== 0) ? escape($("#arrival_time").val()) : "";
		
		var ap_procedure = ($("#sel_proc_id").length !== 0) ? $("#sel_proc_id").val() : "";
		var sec_ap_procedure = ($("#sec_sel_proc_id").length !== 0) ? $("#sec_sel_proc_id").val() : "";
		var ter_ap_procedure = ($("#ter_sel_proc_id").length !== 0) ? $("#ter_sel_proc_id").val() : "";	
		
		var pri_eye_site = ($("#pri_eye_site").length !== 0) ? $("#pri_eye_site").val() : "";
		var sec_eye_site = ($("#sec_eye_site").length !== 0) ? $("#sec_eye_site").val() : "";
		var ter_eye_site = ($("#ter_eye_site").length !== 0) ? $("#ter_eye_site").val() : "";
		
		var facility_type_provider = ($("#facility_type_provider").length !== 0) ? $("#facility_type_provider").val() : "";

		var referral = ($("input[type=checkbox]#sa_ref_management").is(":checked") ? 1 : 0);
		var ref_management = "&pt_referral="+referral;
        
        var sa_verification = ($("input[type=checkbox]#sa_verification_req").is(":checked") ? 1 : 0);
        var verification_req = "&pt_verification="+sa_verification;
		
			var send_uri = "save_changes.php?save_type="+mode+"&pt_id="+pt_id+"&ap_id="+ap_id+"&pt_doctor_id="+pt_doctor_id+"&pt_pcp_phy_id="+pt_pcp_phy_id+"&pt_pcp_phy="+pt_pcp_phy+"&pt_ref_phy_id="+pt_ref_phy_id+"&pt_ref_phy="+pt_ref_phy+"&pt_street1="+pt_street1+"&pt_street2="+pt_street2+"&pt_city="+pt_city+"&pt_state="+pt_state+"&pt_zip="+pt_zip+"&pt_zip_ext="+pt_zip_ext+"&pt_email="+pt_email+"&pt_photo_ref="+pt_photo_ref+"&pt_home_ph="+pt_home_ph+"&pt_work_ph="+pt_work_ph+"&pt_cell_ph="+pt_cell_ph+"&pt_status="+pt_status+"&pt_other_status="+pt_other_status+"&pt_dod_patient="+pt_dod_patient+"&ap_routine_exam="+ap_routine_exam+"&ap_ins_case_id="+ap_ins_case_id+"&ap_notes="+ap_notes+"&pri_eye_site="+pri_eye_site+"&sec_eye_site="+sec_eye_site+"&ter_eye_site="+ter_eye_site+"&ap_pickup_time="+ap_pickup_time+"&ap_arrival_time="+ap_arrival_time+"&ap_procedure="+ap_procedure+"&start_date="+start_date+"&start_time="+start_time+"&doctor_id="+doctor_id+"&facility_id="+facility_id+"&tmp_id="+tmp_id+"&pt_fname="+pt_fname+"&pt_mname="+pt_mname+"&pt_lname="+pt_lname+"&pt_emr="+pt_emr+"&ap_act_reason="+ap_act_reason+"&tempproc="+tempproc+'&init_st_time_rs='+init_st_time_rs+'&init_et_time_rs='+init_et_time_rs+'&init_acronym_rs='+init_acronym_rs+'&init_provider_id='+init_provider_id+'&init_fac_id='+init_fac_id+'&sec_ap_procedure='+sec_ap_procedure+'&ter_ap_procedure='+ter_ap_procedure+'&init_date_rs='+init_date_rs+"&facility_type_provider="+facility_type_provider+ref_management+verification_req;
		$.ajax({
			url: send_uri,
			type: "POST",
			success: function(resp){
				//alert(resp);
				//return false;
				if(is_remote_server() == true)
				{
					resp = json_resp_handle(resp);				
				}
				var arr_resp = resp.split("~");
				if(arr_resp[0] == "save"){
					pre_load_front_desk(pt_id, ap_id, false);
				}
				if(arr_resp[0] == "addnew" || arr_resp[0] == "reschedule"){
					var arr_start_date = start_date.split("-");
					var new_start_date = arr_start_date[2]+"-"+arr_start_date[0]+"-"+arr_start_date[1];					
					load_calendar(new_start_date, arr_resp[1], '', false);					
				}
				$("#global_apptact").val("");
				$("#global_apptactreason").val("");
				$("#global_apptstid").val("");
				$("#global_apptsttm").val("");
				$("#global_apptdoc").val("");
				$("#global_apptfac").val("");
				$("#global_apptstdt").val("");
				$("#global_appttempproc").val("");
				$('#init_fac_id').val("");
				$('#init_prov_id').val("");
				hideConfirmYesNo();
			}
		});
	}
}

function popUpMe(intId, patId, server_data){
	//window.open("common/appt_hx_popup.php?schId="+intId+"&patId="+patId,"AppointmentHxDetails","width=700,height=400,top=175,left=125,resizable=yes");			
	var url = WEB_ROOT+"/interface/scheduler/common/appt_hx_popup.php?schId="+intId+"&patId="+patId;
	$('#div_app_hx').modal('show');
	top.master_ajax_tunnel(url,popUpMe_callBack);
}

function popUpMe_callBack(reponse,etc)
{
	$('#div_app_hx_detail').html(reponse);
}
//cal write



function printContactRx(method, workSheetId, opnr_status){
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	if(opnr_status == 1)
	{
		winPrintMr = window.open('../../chart_notes/print_patient_contact_lenses.php?printType=2&method='+method+'&workSheetId='+workSheetId,'printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');		
	}
	else
	{
		winPrintMr = window.open('../chart_notes/print_patient_contact_lenses.php?printType=2&method='+method+'&workSheetId='+workSheetId,'printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');		
	}
}

function printMr(value, opnr_status){
	var givenMrValue = value;
	var pr = "";
	pr = (pr == "") ? "0" : "1";
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	if(opnr_status == 1)
	{
		winPrintMr = window.open('../../chart_notes/requestHandler.php?printType=1&elem_formAction=print_mr&givenMr='+givenMrValue,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');		
	}
	else
	{
		winPrintMr = window.open('../chart_notes/requestHandler.php?printType=1&elem_formAction=print_mr&givenMr='+givenMrValue,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');		
	}
}

function get_copay(pat_id, casid, appt_id, appt_date){
	if(!casid) casid = "";
	if(!appt_id) appt_id = "";
	if(!appt_id) appt_date = "";
	if(casid != "" && casid != 0){

		send_uri = "common/get_copay_refrral.php?pat_id="+pat_id+"&case_typeid="+casid+"&appt_id="+appt_id+"&appt_date="+appt_date;
		
		$.ajax({
			url: send_uri,
			type: "GET",
			success: function(resp){
				var res = resp.split("~");
				if(res[0] == 1){
					if(res[1] != 0){
						document.getElementById("cpays").innerHTML = res[1];
					}else{
						document.getElementById("cpays").innerHTML = "$0.00";
					}
					if(res[7] == "1" || res[7] == 1){
						if(document.getElementById("RoutineExamVisionCase")){
							document.getElementById("RoutineExamVisionCase").style.display = "block";
						}
					}else{
						if(document.getElementById("RoutineExamVisionCase")){
							document.getElementById("RoutineExamVisionCase").style.display = "none";
						}
					}
				}else{
					document.getElementById("cpays").innerHTML = "$0.00";
				}
				if(res[8]!=""){
					get_accept_assignment(res[8]);
				}
			}
		});
	}else{
		get_accept_assignment(0);
	}
}

function show_test(procid,sch_id){

		if(sch_id=='' || sch_id==0)
		{
			if(procid != ""){
				document.getElementById('txt_comments').disabled=false;
				document.getElementById('txt_comments').value='';
			} else {
				document.getElementById('txt_comments').value= 'Appointment Comment';
				document.getElementById('txt_comments').disabled=true;
			}
		}
		if(procid != ""){
			var provider_id;
			if(document.getElementById("sel_fd_provider")){
					provider_id =document.getElementById("sel_fd_provider").value;
					getProcMessage(provider_id,procid);
			}
		}
}

function getProcMessage(provider_id,proc_id){

	send_uri = "common/getproceduretime.php?pro_id="+provider_id+"&proc_id="+proc_id;
	$.ajax({
		url: send_uri,
		type: "GET",
		success: function(resp){
			var res = resp.split("~");
			var notes= res[1];
			if(notes!='') {
				top.fAlert(notes);
			}
		}
	});
}


function image_DIV(imageSrc, div){	
	if(imageSrc){
		getPatientImage();
	}
}

function getPatientImage(){
	var patId = $("#global_ptid").val();
	$.ajax({ 
		url: "patient_photos.php?pat_id="+patId,
		success: function(resp){
			if(resp != ""){
				if (document.getElementById('patient_photo_container')){
					//var img = $(resp);
					//var height = $(img).css('height');
					//var width = $(img).css('width');
					//$("#patient_photo_container").css({display:'inline-block', height:height, width:width});
					$("#patient_photo_container").html(resp);
				}
				else{
				$("#patient_photo_container").html('<img src="../../library/images/ptimage.png" alt=""/> ');
				}
			}
		}
	});	
}

function hidePatientImage(){
	if (document.getElementById('patient_photo_container')){
		document.getElementById('patient_photo_container').style.display = 'none';
	}
}


/*
Name:		mk_appt
Purpose:	to Make Appt Based upon the follow up set in latest chart note for this patient
Author:		AA
*/
function mk_appt(pt_id){
	if(!pt_id) pt_id = "";

	if(pt_id != ""){
		//alert("mk_appt.php?pt_id="+pt_id);
		$.ajax({ 
			url: "mk_appt.php?pt_id="+pt_id,
			success: function(resp){
				if(resp != "no_response"){					
					var arr_resp = resp.split("||||");				
					set_date(arr_resp[0]);
					if($("#txt_comments").get(0) && arr_resp[2].trim() != ""){
						$("#txt_comments").val('');
						$("#txt_comments").val(arr_resp[2]);
					}
					if($("#sel_proc_id").get(0)){
						$("#sel_proc_id").val(arr_resp[3]);
					}
					load_calendar(arr_resp[0], arr_resp[1], 'nonono', false);
					load_appt_schedule(arr_resp[0], arr_resp[1], '', 'nonono', false);
				}else{
					top.fAlert("No Follow Up added for the patient.");
				}
			}
		});
	}else{
		top.fAlert("Please select patient.");
		return false;
	}
}

/*PT DEMOGRAPHICS ALERTS*/
function patient_note_alert(title,msg,btn1,btn2,func,showCancel,showImage,misc)
{
	//
	
	  text = '<div id="divCon_pt_alert" style="position:relative; z-index:1000; left:450px;">';
			 
	  text += '<table align="center" width="400px" border=0 cellpadding=2 cellspacing=0 class="confirmTable3" style="position:absolute;top:0px;left:0px;z-index:10;">';
	  text += '<tr><td height="25" class="text_b_w" colspan="2" >';		  		  
	  text += title;
	  text += '</td></tr>';
	
	  text += '<tr class="confirmBackground"><Td colspan="2" class="text_10b">';
	  
	  if((typeof showImage == "undefined") || (showImage != 0))
	  {
		//text += '<img src="../../library/images/stop.gif" alt="stop">';
	  }
	   
	  text += '</td>';
	  text += '</tr>';
	  text += '<tr  class="confirmBackground">';
	 
	  text += '<td colspan="2" valign="middle" class="text_10b" align="center">';
	  text += msg;
	 
	  text += '</td></tr>';
	  text += '<tr  height="25"  class="confirmBackground"><td class="confirmBackground"  colspan="2" ><center>';
	  text += '<input type="button" value="'+btn1+'" onClick="window.'+func+'(1)" class=\"dff_button\" id="okbut1" onMouseOver="button_over(\'okbut1\')" onMouseOut="button_over(\'okbut1\', \'\')">';
	 
	  if((typeof showCancel == "undefined") || (showCancel != 0))
	  {
		text += '<input type="button" value="Cancel" onClick="window.'+func+'(-1)" class=\"dff_button\" id="okbut2" onMouseOver="button_over(\'okbut2\')" onMouseOut="button_over(\'okbut2\', \'\')">';
	  }
	  text += '</center></td></tr></table>';		 
	  text += '</div>';		  
	  
	  if (document.getElementById('msgDiv2')) 
	  {
		 mDiv = document.getElementById('msgDiv2');
		 mDiv.innerHTML = text;
		 mDiv.style.visibility = 'visible';
	  }	
	  
}// end of function
function alertReasonsShow_pt_alert(strVal){
	alertReasonsHide_pt_alert();
	var objDiv=document.getElementById("msgDiv2");
	if(objDiv && strVal==1){		
		objDiv.style.display="block";
	}
}
function alertReasonsHide_pt_alert(){

	var objDiv=document.getElementById("msgDiv2");
	if(objDiv){		
		objDiv.style.display="none";
	}
}
function show_pop_up_pt_alert(msg){
	var title = "Patient Alerts";
	var showCancel = 0;
	var showImage = 1;
	var btn1 = "OK";
	var btn2 = "Cancel";
	var func = "alertReasonsHide_pt_alert";	
	var oPDiv = patient_note_alert(title,msg,btn1,btn2,func,showCancel,showImage,0);
	if(oPDiv){
		oPDiv.style.left = "350px";
	}
}

function alert_box(title, content, w, h, l, t, divName, showClose, showMask){	
	if(top.dgi(divName)){
		if(typeof(w) == "undefined"){ w = "300px"; }else{ w = parseInt(w) + "px"; }
		if(typeof(h) == "undefined"){ h = "auto"; }else{ h = parseInt(h) + "px"; }
		if(typeof(l) == "undefined"){ l = "100px"; }else{ l = parseInt(l) + "px";}
		if(typeof(t) == "undefined"){ t = "100px"; }else{ t = parseInt(t) + "px";}
		if(typeof(showClose) == "undefined"){ showClose = true; }	
		if(showClose){title = '<span class="closeBtn" onClick="close_alert_box('+divName+');"></span>'+title;}
		
		var text = "";
		text += '<div class="section" style="cursor:pointer;width:'+w+';">';
			text += '<div id="'+divName+'-handle" class="section_header">'+title+'</div>';
			text += '<div style="margin-top:0px;text-align:left;height:'+h+';overflow:auto;overflow-x:hidden;background-color:#FFFFFF;" class="mt10 padd10">'+content+'</div>';
		text += '</div>';
		top.dgi(divName).innerHTML = text;
		top.dgi(divName).style.width = w;
		//top.dgi(divName).style.height = h;
		top.dgi(divName).style.left = l;
		top.dgi(divName).style.top = t;
		top.dgi(divName).style.display = "block";
	}
}

function close_alert_box(divName){
	top.dgi(divName).innerHTML = "";
	top.pop_up_handler(divName);
	top.dgi(divName).style.display = "none";
}

function show_provider_notes(prov_id, load_dt){
	$.ajax({ 
		url: "provider_notes.php?prov_id="+prov_id+"&load_dt="+load_dt,
		success: function(resp){
			var arr_resp = resp.split("~~~~~");
			$("#provider_notes_div_header").html(arr_resp[0]);
			$("#provider_notes_div_content").html(arr_resp[1]);
			$("#provider_notes_div_footer").html(arr_resp[2]);
			$("#provider_notes_div").modal("show");
		}
	});
}
function reload_provider_notes(prov_id, load_dt){
	
	$("#provider_notes_div_content").html("Loading...");
	$("#provider_notes_div_footer").html("");
	$.ajax({ 
		url: "provider_notes.php?prov_id="+prov_id+"&load_dt="+load_dt,
		success: function(resp){
			var arr_resp = resp.split("~~~~~");
			$("#provider_notes_div_header").html(arr_resp[0]);
			$("#provider_notes_div_content").html(arr_resp[1]);
			$("#provider_notes_div_footer").html(arr_resp[2]);
		}
	});
}

function hide_provider_notes(){
	$("#provider_notes_div_header").html("");
	$("#provider_notes_div_content").html("");
	$("#provider_notes_div_footer").html("");
	$("#provider_notes_div").modal("hide");
}

function save_provider_notes(load_dt){
	if(!load_dt) load_dt = "";
	var act_id = $("#new_prov_note_act").val();
	var prov_id = $("#new_prov_note_id").val();
	var notes = $("#new_prov_note").val();
	if($.trim(notes))
	{
		if(load_dt == ""){
			var note_date = get_selected_date();
		}else{
			var note_date = load_dt;
		}
		$.ajax({ 
			url: "save_provider_notes.php?prov_id="+prov_id+"&note_date="+note_date+"&act_id="+act_id+"&notes="+encodeURIComponent(notes),
			success: function(resp){
				//hide_provider_notes();
				//show_provider_notes(prov_id, note_date);
				reload_provider_notes(prov_id, note_date);
				var notes_cnt_name = "sticky_"+prov_id+"_"+load_dt;
				$("#"+notes_cnt_name).html($.trim(resp));
			}
		});
	}
}

function edit_provider_notes(note_id){
	var note_div_name = "existing_notes"+note_id;
	var notes = $("#"+note_div_name).html();
	$("#new_prov_note_act").val(note_id);
	$("#new_prov_note").val($.trim(notes));
}

function new_provider_notes(){
	$("#new_prov_note_act").val("0");
	$("#new_prov_note").val("");
}

function delete_provider_notes(note_id, load_dt){
	if(!load_dt) load_dt = "";
	var prov_id = $("#new_prov_note_id").val();
	if(load_dt == ""){
		var note_date = get_selected_date();
	}else{
		var note_date = load_dt;
	}
	$.ajax({ 
		url: "delete_provider_notes.php?prov_id="+prov_id+"&note_date="+note_date+"&act_id="+note_id,
		success: function(resp){
			//hide_provider_notes();
			//show_provider_notes(prov_id, note_date);
			reload_provider_notes(prov_id, note_date);
			var notes_cnt_name = "sticky_"+prov_id+"_"+load_dt;
			$("#"+notes_cnt_name).html(resp);
		}
	});
}

function changeTimings(ids, load_dt)
{
	$.ajax({ 
		url: "load_block_timings.php?ids="+ids+"&load_dt="+load_dt,
		success: function(resp){
			$("#timeContainer").html(resp);
		}
	});
}

function blk_lk_options(mode, act_type){
	if(!mode) mode = "";
	TestOnMenu();
	if(mode == "get"){
		var ap_sttm = $("#global_context_slsttm").val();
		var ap_doc = $("#global_context_sldoc").val();
		var ap_fac = $("#global_context_slfac").val();
	}
	var load_dt = get_selected_date();
	$.ajax({ 
		url: "load_block_options.php?load_dt="+load_dt+"&mode="+mode+"&ap_sttm="+ap_sttm+"&ap_doc="+ap_doc+"&ap_fac="+ap_fac+"&act_type="+act_type+"&sid="+Math.random(),
		success: function(resp){
			var arr_resp = resp.split("~~~~~");
			
			$("#block_lock_div").modal("show");
			$("#blk_lk_content").html(arr_resp[1]);

			if(act_type == "block"){
				swap_block_unblock('block');
			}
			if(act_type == "unblock"){
				swap_block_unblock('none');
			}
			
			$("#blk_lk_date").html(arr_resp[0]);
			$("#block_lock_div_footer").html(arr_resp[2]);
			
			//refresh select picker
			$("#blk_lk_loca").selectpicker("refresh");
			$("#blk_lk_prov").selectpicker("refresh");

			/*var dd_pro = new Array();
			dd_pro["listHeight"] = 300;
			dd_pro["noneSelected"] = "Select All";
			$("#blk_lk_prov").multiSelect(dd_pro, function(){ changeTimings(selectedValuesStr("blk_lk_prov"), load_dt)});
			
			var dd_fac = new Array();
			dd_fac["listHeight"] = 300;
			dd_fac["noneSelected"] = "Select All";
			$("#blk_lk_loca").multiSelect(dd_fac);*/			
		}
	});
}

//add / edit labels - starts here
//ajax div swap by amit - starts here
var selectedList;
var availableList;
function populateProcLabel(){
	var strReturn = document.getElementById("tempSelectedCache").value;   
	var arrReturn = strReturn.split("~:~");
	var strLen = arrReturn.length;
	strReturn = "";
	for(i = 0; i < strLen-1; i++){
		var arrTemp = arrReturn[i].split("~~~");
		strReturn += arrTemp[1]+"; ";
	} 
	var strLength = parseInt(strReturn.length)-2;
	strReturn = strReturn.substring(0,strLength);
	//document.frm_proc_time.template_label.value=strReturn;
	document.getElementById('proc_acro').value=strReturn;
	//document.frm_proc_time.chkLunch.checked=false; 
	//document.frm_proc_time.chkReserved.checked=false;
}
function createListObjects(){
	availableList = document.getElementById("availableOptions");
	selectedList = document.getElementById("selectedOptions");
}
	 
function setSize(list1,list2){
	list1.size = getSize(list1);
	list2.size = getSize(list2);
}

function selectNone(list1,list2){
	list1.selectedIndex = -1;
	list2.selectedIndex = -1;
	addIndex = -1;
	selIndex = -1;
}

function getSize(list){
	var len = list.childNodes.length;
	var nsLen = 0;
	for(i=0; i<len; i++){
		if(list.childNodes.item(i).nodeType==1)
		nsLen++;
	}
	if(nsLen<2)
	return 2;
	else
	return nsLen;
}     

function refreshProcList(strSelectType,strMode){

	var dir="../admin/scheduler_admin/schedule_template/";
	if(strSelectType == "custom"){
		var strAttribs = document.getElementById("tempSelectedCache").value;
		var url_dt = dir+"proc_list.php?strSelectType="+strSelectType+"&strAttribs="+strAttribs;                                
	}else{
		var url_dt = dir+"proc_list.php?strSelectType="+strSelectType;
	}   
	$("#loading_img").css("display","block");
	$.ajax({ 
		url: url_dt,
		success: function(resp){
			var arrResponse = resp.split("[{(^)}]");
			
			if(strSelectType == "available"){
				$("#divAvailableOptions").html(arrResponse[0]);
				$("#divSelectedOptions").html(arrResponse[1]);
				
			}else if(strSelectType == "custom"){
				$("#divAvailableOptions").html(arrResponse[0]);
				$("#divSelectedOptions").html(arrResponse[1]);
				
				if(arrResponse[2] == 1){
					 document.getElementById('addall').disabled = false;
					 document.getElementById('addsel').disabled = false;
				}else{
					 document.getElementById('addall').disabled = true;
					 document.getElementById('addsel').disabled = true;
				}
				
				if(arrResponse[3] == 1){    
					 document.getElementById('remall').disabled = false;
					 document.getElementById('remsel').disabled = false;
				}else{
					 document.getElementById('remall').disabled = true;
					 document.getElementById('remsel').disabled = true;
				}
	
			}else{
				$("#divAvailableOptions").html(arrResponse[1]);
				$("#divSelectedOptions").html(arrResponse[0]);
				
				var selectedList = document.getElementById("selectedOptions");
				//alert(selectedList.length);
				var strReturn = "";
				for(i = 0; i < selectedList.length; i++){               
					strReturn += selectedList.options.item(i).value+",";
				}
				
				
				document.getElementById("tempSelectedCache").value=strReturn;
			}    
			populateProcLabel();
			if(strMode == "lunch"){
				document.getElementById("proc_acro").value='lunch'; document.getElementById("chkLunch").checked=true; document.getElementById("chkReserved").checked=false;
				//template_label
			}
			
			if(strMode == "Reserved"){
				document.getElementById("proc_acro").value='Reserved'; document.getElementById("chkLunch").checked=false; document.getElementById("chkReserved").checked=true;
				//template_label
			}            
			document.getElementById("loading_img").style.display = "none";
					
		}
	});
	
}

function delAll(strMode){
	document.getElementById("tempSelectedCache").value="";
	refreshProcList("available", strMode);
	selectedList.options.length = 0;
	selectNone(selectedList,availableList);
	setSize(selectedList,availableList);
	document.getElementById('addall').disabled = false;
	document.getElementById('addsel').disabled = false;
	document.getElementById('remall').disabled = true;
	document.getElementById('remsel').disabled = true;
}

function addAll(){
	document.getElementById("tempSelectedCache").value="";
	refreshProcList("selectedOptions");
	availableList.options.length = 0; 
	selectNone(selectedList,availableList);
	setSize(selectedList,availableList);
	document.getElementById('addall').disabled = true;
	document.getElementById('addsel').disabled = true;
	document.getElementById('remall').disabled = false;
	document.getElementById('remsel').disabled = false;
}


function delAttribute(){
	var strToSendAttrib = ""; 
	var selectedList = document.getElementById("selectedOptions");
	var selIndex = selectedList.selectedIndex;
	if(selIndex < 0){
		top.fAlert("Please select some procedure(s) to continue.");
		return;
	}
	var arrRefinedSelection = new Array();
	var j = 0;
	var existingValue = document.getElementById("tempSelectedCache").value;
	var arrExistingValue = existingValue.split(",");
	
	for(i = 0; i < selectedList.length; i++){
		blRemove = "";
		if(selectedList.options.item(i).selected == true){             
			var blRemove = selectedList.options.item(i).value;
			for(z = 0; z < arrExistingValue.length-1; z++){
				if(arrExistingValue[z] == selectedList.options.item(i).value){
					arrExistingValue[z] = "";            
				}                    
			}                
		}
	}
	
	for(i = 0; i < arrExistingValue.length ; i++){
		if(arrExistingValue[i] != "undefined" && arrExistingValue[i] != "")
			strToSendAttrib += arrExistingValue[i]+"~:~";
	}
	
	if(strToSendAttrib == ""){
		delAll();
	}else{
		document.getElementById("tempSelectedCache").value = strToSendAttrib;
		refreshProcList("custom");
	}
}

function addAttribute(){
	var strToSendAttrib = "";
	var availableList = document.getElementById("availableOptions");
	var addIndex = availableList.selectedIndex;
	if(addIndex < 0){
		top.fAlert("Please select some procedure(s) to continue.");
		return;
	}

	for(i = availableList.length-1; i >= 0 ; i--){
		if(availableList.options.item(i).selected == true){
			strToSendAttrib += availableList.options.item(i).value+",";
		}
	}
	
	document.getElementById("tempSelectedCache").value += strToSendAttrib;
	//refreshProcList("custom");
}

function set_reset_options(mode){
	/*if(mode == "Lunch"){
		document.getElementById("template_label").value = "Lunch";
		document.getElementById("select_acro").style.display = "none";
		document.getElementById("input_acro").style.display = "block";
	}else if(mode == "Reserved"){
		document.getElementById("template_label").value = "Reserved";
		document.getElementById("select_acro").style.display = "none";
		document.getElementById("input_acro").style.display = "block";
	}else if(mode == "Information"){
		document.getElementById("template_label").value = "";
		document.getElementById("select_acro").style.display = "none";
		document.getElementById("input_acro").style.display = "block";
	}else if(mode == "Procedure"){
		document.getElementById("template_label").value = "";
		document.getElementById("select_acro").style.display = "block";
		document.getElementById("input_acro").style.display = "none";
	}*/
	
	$("#proc_acro").prop("readonly", false);
	if(mode == "Lunch"){
			document.getElementById("proc_acro").value = "Lunch";
			document.getElementById("show_proc_options").style.display = "none";
			$("#proc_acro").prop("readonly", true);
		}else if(mode == "Reserved"){
			document.getElementById("proc_acro").value = "Reserved";
			document.getElementById("show_proc_options").style.display = "none";
		}else if(mode == "Information"){
			document.getElementById("proc_acro").value = "";
			document.getElementById("show_proc_options").style.display = "block";
		}else if(mode == "Procedure"){
			document.getElementById("proc_acro").value = "";
			document.getElementById("show_proc_options").style.display = "block";
		}
}

function load_label_options(){
	TestOnMenu();
	var ap_sttm = $("#global_context_slsttm").val();
	var ap_doc = $("#global_context_sldoc").val();
	var ap_fac = $("#global_context_slfac").val();
	var ap_lbty = $("#global_context_apptlbty").val();
	var ap_lbtx = $("#global_context_apptlbtx").val();
	var ap_lbcl = $("#global_context_apptlbcl").val();
	var ap_tmp_id = $("#global_context_appt_tmp_id").val();
	var load_dt = get_selected_date();
	var send_uri = "load_label_options.php?load_dt="+load_dt+"&ap_sttm="+ap_sttm+"&ap_doc="+ap_doc+"&ap_fac="+ap_fac+"&ap_lbty="+escape(ap_lbty)+"&ap_lbtx="+escape(ap_lbtx)+"&ap_lbcl="+escape(ap_lbcl)+"&ap_tmp_id="+escape(ap_tmp_id);
	//alert(send_uri);
	$.ajax({ 
		url: send_uri,
		success: function(resp){
			var arr_resp = resp.split("~~~~~");
			
			$("#label_opt_div").modal("show");
			$("#label_opt_date").html(arr_resp[0]);
			$("#label_opt_content").html(arr_resp[1]);
			$("#label_opt_footer").html(arr_resp[2]);	
			/*var dd_pro = new Array();
			dd_pro["listHeight"] = 300;
			dd_pro["noneSelected"] = "Select All";
			$("#label_opt_prov").multiSelect(dd_pro, function(){ changeTimings(selectedValuesStr("label_opt_prov"), load_dt)});
			
			var dd_fac = new Array();
			dd_fac["listHeight"] = 300;
			dd_fac["noneSelected"] = "Select All";
			$("#label_opt_loca").multiSelect(dd_fac);	
			
			$("#proc_acro").multiSelect({noneSelected:'Select All'})*/;
			//refresh multiselect dropdown options
			//$("#label_opt_loca").selectpicker("refresh");
			//$("#label_opt_prov").selectpicker("refresh");
			$("#availableOptions").selectpicker("refresh");
			
		/*	var colorPickObj = $('.bfh-colorpicker');
			if(colorPickObj.data('bfhcolorpicker')){
				console.log(colorPickObj, 'Initialized');
				colorPickObj.setValue = 'transparent';
			}else{
				var newPicker = colorPickObj.colorpicker();
				newPicker.setValue = 'transparent';
				console.log(newPicker, 'New Initialized');
			}*/
			load_color_picker("#FFF");
			
		}
	});
}

function remove_labels_by_slot()
{
	TestOnMenu();
	var ap_sttm = $("#global_context_slsttm").val();
	var ap_doc = $("#global_context_sldoc").val();
	var ap_fac = $("#global_context_slfac").val();
	var ap_lbty = encodeURIComponent($("#global_context_apptlbty").val());
	var ap_lbtx = encodeURIComponent($("#global_context_apptlbtx").val());
	var ap_lbcl = encodeURIComponent($("#global_context_apptlbcl").val());
	var ap_tmp_id = encodeURIComponent($("#global_context_appt_tmp_id").val());
	var replace_lbl = encodeURIComponent($("#global_replace_lbl").val());
	var load_dt = get_selected_date();

	var rm_lbl_url = "remove_labels_by_slot.php?ap_sttm="+ap_sttm+"&ap_doc="+ap_doc+"&ap_fac="+ap_fac+"&ap_lbty="+ap_lbty+"&ap_lbtx="+ap_lbtx+"&ap_lbcl="+ap_lbcl+"&load_dt="+load_dt+"&replace_lbl="+replace_lbl+"&ap_tmp_id="+escape(ap_tmp_id);	
	$.ajax({
			url:rm_lbl_url,
			success : function(resp)
			{
				if(resp != "notdone")
				{
					load_appt_schedule(load_dt, resp, '', "nonono");
				}
			}
		});
}

function save_label_options(mode){
	if(!mode) mode = "";

	var err = "";
	if($("#label_time_to_hour").val() == "" || $("#label_time_to_mins").val() == "" || $("#label_ap2").val() == ""){
		err += " - End Time\n";
	}
	if(err != ""){
		err = "Please provide input for the following:\n\n" + err;
		top.fAlert(err);
		return false;
	}else{
		
		//show loading image
		top.show_loading_image("show");

		var load_dt = get_selected_date();
		
		var prov = $("#label_opt_prov").val();
		var loca = $("#label_opt_loca").val();

		var time_from_hour = $("#label_time_from_hour").val();
		var time_from_mins = $("#label_time_from_mins").val();
		var ap1 = $("#label_ap1").val();

		var time_to_hour = $("#label_time_to_hour").val();
		var time_to_mins = $("#label_time_to_mins").val();
		var ap2 = $("#label_ap2").val();

		var label_type = $("#label_type").val();
		var label_text = $("#template_label").val();
		var proc_acro = $("#proc_acro").val();
		var label_color = $("#label_color").val();
		var ap_tmp_id = $("#global_context_appt_tmp_id").val();
			
		var send_uri = "save_label_options.php?proc_acro="+proc_acro+"&load_dt="+load_dt+"&prov="+prov+"&loca="+loca+"&time_from_hour="+time_from_hour+"&time_from_mins="+time_from_mins+"&ap1="+ap1+"&time_to_hour="+time_to_hour+"&time_to_mins="+time_to_mins+"&ap2="+ap2+"&label_type="+escape(label_type)+"&label_text="+escape(label_text)+"&label_color="+escape(label_color)+"&mode="+mode+"&ap_tmp_id="+ap_tmp_id;
		$.ajax({
			url: send_uri,
			success: function(resp){
				//alert(resp);
				var arr_resp = resp.split("~~~~~");
				//document.write(resp);
				//return false;
				$("#label_opt_div").modal('hide');

				//show loading image
				top.show_loading_image("hide");
				load_appt_schedule(arr_resp[0], arr_resp[1], "", "nonono", false); //dt and dayname in response
			}
		});
	}
}
//add/edit labels - ends here

function swap_block_unblock(action,elemValue){
 if(elemValue =='locked')
 {
	display_block_none('comments_section', action);
	display_block_none('block_warning', 'none');
	$('#blk_lk_comment').val('Locked');
 }else {
	display_block_none('comments_section', action);
	display_block_none('block_warning', action);
	$('#blk_lk_comment').val('Blocked');	
 }
}

function chgBlockTime(ids)
{
	top.fAlert(ids);
}
	


function save_blk_lk(){
	
	var err = "";
	if($("#block_time_to_hour").val() == "" || $("#block_time_to_mins").val() == "" || $("#block_ap2").val() == ""){
		err += " - End Time<br />";
	}
	if(err != ""){
		err = "Please provide input for the following:<br /><br />" + err;
		top.fAlert(err);
		return false;
	}else{

		//show loading image
		top.show_loading_image("show");

		var load_dt = get_selected_date();

		var block_mode = "";
		var blk_lk_act = document.getElementsByName("blk_lk_act");
		for(act = 0; act < blk_lk_act.length; act++){
			if(blk_lk_act[act].checked == true){
				if(blk_lk_act[act].id == "blk_lk_act_block"){
					block_mode = "block"
				}
				if(blk_lk_act[act].id == "blk_lk_act_unblock"){
					block_mode = "open"
				}
				if(blk_lk_act[act].id == "lk_act_block"){
					block_mode = "lock"
				}
				if(blk_lk_act[act].id == "lk_act_unblock"){
					block_mode = "unlock"
				}
			}
		}
		if(block_mode != ""){
			var prov = selectedValuesStr("blk_lk_prov");
			var loca = selectedValuesStr("blk_lk_loca");

			var time_from_hour = $("#block_time_from_hour").val();
			var time_from_mins = $("#block_time_from_mins").val();
			var ap1 = $("#block_ap1").val();

			var time_to_hour = $("#block_time_to_hour").val();
			var time_to_mins = $("#block_time_to_mins").val();
			var ap2 = $("#block_ap2").val();
			var ap_tmp_id = $("#global_context_appt_tmp_id").val();

			var comments = $("#blk_lk_comment").val();
			
			var send_uri = "save_block_options.php?load_dt="+load_dt+"&block_mode="+block_mode+"&prov="+prov+"&loca="+loca+"&time_from_hour="+time_from_hour+"&time_from_mins="+time_from_mins+"&ap1="+ap1+"&time_to_hour="+time_to_hour+"&time_to_mins="+time_to_mins+"&ap2="+ap2+"&comments="+escape(comments)+"&ap_tmp_id="+ap_tmp_id;
			//alert(send_uri);
			$.ajax({
				url: send_uri,
				success: function(resp){
					//alert(resp);
					var arr_resp = resp.split("~~~~~");
					$("#block_lock_div").modal("hide");

					//show loading image
					top.show_loading_image("hide");

					load_appt_schedule(arr_resp[0], arr_resp[1], "", "nonono", false); //dt and dayname in response
				}
			});
		}
	}
}

function day_print_options(sel_pro, load_dt, level){
	if(!sel_pro) sel_pro = "";
	if(!load_dt) load_dt = "";
	
	if(load_dt == ""){
		var load_dt = get_selected_date();
	}
	
	var selProCombo = get_selected_providers();
	var selFacCombo = get_selected_facilities();
	
	$.ajax({ 
		url: "load_print_options.php?load_dt="+load_dt+"&sel_pro="+sel_pro+"&level="+level+"&selProCombo="+selProCombo+"&selFacCombo="+selFacCombo,
		success: function(resp){			
			var arr_resp = resp.split("~~~~~");
			
			$("#day_print_options_div").modal("show");
			$("#print_options_content").html(arr_resp[1]);
			var arr_exclusion= ["Patient DOB","Phone","Procedure","Comments","Appt Made","CoPay","Pt. Prv Bal"];
			var exclusion="<span class='a_clr1' style='margin-left:80px;float:right;font-weight:bold;cursor:pointer;	 font-size:13px;' id='excl_link' onClick='$(\"#exc_div\").show(\"slide\");'>Exclude</span>";
			var exculsion_ele;
			exculsion_ele="<div class='section_header'>Exclusion<span class='fr' onClick='$(\"#exc_div\").hide(\"slide\");'><img src='../../library/images/close14.png'> </span></div><table class='section' style='width:100%'>";
			exculsion_ele+="<tr>";
			var label_str='';
			for(var e=0;e<arr_exclusion.length;e++){
				
				label_str=arr_exclusion[e].replace('_');
				exculsion_ele+="<td><div class='checkbox'><input id='chkbox"+e+"' name='excusion_chkbox[]' type='checkbox' value='"+arr_exclusion[e]+"' checked='checked'><label for='chkbox"+e+"'>"+label_str+"</label></div></td>";
				if(e==3){exculsion_ele+='</tr><tr>';}
				
			}
			exculsion_ele+="</tr>";
			exculsion_ele+='</table>';
			$("#exc_div").html(exculsion_ele);
			if(level == 1){
				$("#print_options_caption").html(" - Print Options All"+exclusion);
			}
			else if(level == 2){
				$("#print_options_caption").html(" - Print Options"+exclusion);
			}
			$("#print_options_date").html(arr_resp[0]);
			//add buttons
			$("#day_print_options_footer").html(arr_resp[2]);
			//refresh select picker
			$("#print_loca").selectpicker("refresh");
			$("#print_prov").selectpicker("refresh");
		}
	});
}

function day_print_process(load_dt){
	if(!load_dt) load_dt = "";
	var prov = selectedValuesStr("print_prov");
	if(prov === false) prov = '';
	
	var loca = selectedValuesStr("print_loca");
	if(loca === false) loca = '';
	
	if(load_dt == ""){
		var sel_date = get_selected_date();
	}else{
		var sel_date = load_dt;
	}
	var arr_sel_date = sel_date.split("-"); //ymd
	var eff_date = getDateFormat(sel_date);
	var selMidDay = "";
	var selMidDay_act = document.getElementsByName("print_act");
	for(act = 0; act < selMidDay_act.length; act++){
		if(selMidDay_act[act].checked == true){
			if(selMidDay_act[act].id == "print_fullday"){
				selMidDay = "full"
			}
			if(selMidDay_act[act].id == "print_morning"){
				selMidDay = "morning"
			}
			if(selMidDay_act[act].id == "print_evening"){
				selMidDay = "afternoon"
			}
		}
	}
	
	if(document.getElementById("from_date"))
		document.getElementById("from_date").value = eff_date;
	if(document.getElementById("comboFac"))
		document.getElementById("comboFac").value = loca;
	if(document.getElementById("comboProvider"))
		document.getElementById("comboProvider").value = prov;
	if(document.getElementById("selMidDay"))
		document.getElementById("selMidDay").value = selMidDay;

	document.frm_day_appt_print.submit();
	$("#day_print_options_div").modal("hide");
	$("#exc_div").hide();
}	


function day_proc_summary(sel_pro, load_dt){
	if(!sel_pro) sel_pro = "";
	if(!load_dt) load_dt = "";
	if(load_dt == ""){
		var load_dt = get_selected_date();
	}
	if(sel_pro == ""){
		sel_pro = get_selected_providers();
	}
	var sel_fac = get_selected_facilities();
	
	$.ajax({ 
		url: "load_day_summary.php?load_dt="+load_dt+"&sel_pro="+sel_pro+"&sel_fac="+sel_fac,
		success: function(resp){
			//alert(resp);
			var arr_resp = resp.split("~~~~~");

			var arr_resp1 = resp.split("<div id=\"docDiv\"");
			var noOfDoctors =(arr_resp1.length) - 1;
			if(noOfDoctors ==0) { noOfDoctors=1; }
			
			var width = parseInt(noOfDoctors) * 200;
			
			if(width > 800){
				var scrollWidth= width;
				width = 800;
				$("#baseContentDiv").css("overflow-x", "scroll");
				$("#day_proc_summ_content").css("width", scrollWidth+"px");
			}

			$("#day_proc_summ_div").modal("show");			

			$("#baseContentDiv").css("width", width+"px");			

			$("#day_proc_summ_content").html(arr_resp[1]);
			
			$("#day_proc_summ_date").html(arr_resp[0]);		
		}
	});
}


// Function Uses - Function used for CL-Sply Button on Frontdesk,
// function called when Contact Lens Order Submitted at Popup.
function redirectToEnterCharges(pid){
	//var send_url ="../accounting/accountingTabs.php?flagSetPid=true&tab=enterCharges";
	//top.core_redirect_to("Accounting", send_url);
	top.change_main_Selection(top.document.getElementById('AccountingEC'));
}



// WEEKLY ADD APPOINTMENT 
function add_appt_weekly(pr_id, pat_id, fac_id, appt_from, eff_date, temp_id)
{
	if($('#global_apptact').val() == 'reschedule') {
		$("#appt_drag").css("display", "none");
	}
	var url = "add_appt_weekly.php?pr_id="+pr_id+"&fac_id="+fac_id+"&pat_id="+pat_id+"&appt_from="+appt_from+"&eff_date="+eff_date+"&temp_id="+temp_id;
	window.open(url,'addApptWeek','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=600,height=260,left=20,top=100');
}


function send_values_weekly(times_from, eff_date_add, loc, pro1, sch_tmp_id){

	var save_type = $("#global_apptact").val();

	if(save_type != ""){
		//$("#global_apptstid").val(sch_tmp_id);
		//$("#global_apptsttm").val(times_from);
		//$("#global_apptdoc").val(pro1);
		//$("#global_apptfac").val(loc);
		//$("#global_apptstdt").val(eff_date_add);

		//patient id
		var pat_id = $("#pat_id").val();					//setting patient id
		var ap_id = $("#global_apptid").val();

		//document.detachEvent("onmousemove", move_trail);
		$(document).unbind("mousemove", move_trail);
		hide_tool_tip();

		//procedure id
		if($("#sel_proc_id").val() == ""){				
			$("#sel_proc_id").focus();
			top.fAlert("Please select Procedure.");
			return false;
		}else{
			var proc_id = $("#sel_proc_id").val();				//setting procedure id
		}

		if(pat_id != "" && pro1 != ""){
			
			var url_validity = "check_appt_validity.php?st_date=" + eff_date_add + "&pat_id=" + pat_id + "&st_time=" + times_from + "&sl_pro=" + proc_id + "&pro_id=" + pro1 + "&template_id=" + sch_tmp_id + "&fac_id=" + loc + "&querytype=" + save_type;		
			//alert(url_validity);
			
			$.ajax({
				url: url_validity,
				success: function(resp){				
					
					arr_resp = resp.split("~~~");
					if(arr_resp[0] == "y"){	
						
						btn1 = 'Yes';
						btn2 = 'No';

						func1 = 'validate_or_password';
						func2 = 'hideConfirmYesNo';
						
						if(arr_resp[1] == "y"){
							misc = "ASKPASSWORD";
							title = 'Admin Override Required!';
						}else{
							misc = "DONOTASKPASSWORD";
							title = 'Warning!';
						}

						//alert(title+", "+arr_resp[2]+", "+btn1+", "+btn2+", "+func1+", "+func2+", "+misc);
						scheduler_warning_disp_weekly(title, arr_resp[2], btn1, btn2, func1, func2, misc);
					}else{
						if(save_type == "reschedule"){
							//loading reasons							
							$.ajax({
								url: "load_reasons.php?pt_id="+pat_id+"&ap_id="+ap_id,
								success: function(resp){									
									var arr_resp = resp.split("~~~~~");
									var title = "Reschedule Reason";
									
									var msg ='';
									msg ='<div class="row">';
									msg +='<div class="col-sm-12"><strong>'+arr_resp[0]+'</strong></div>';
									msg +='</div>';
									
									msg +='<div class="row">';
									msg +='<div class="col-sm-12">';
									msg +='<div class="form-group">';
									msg +='<label for="">Reason:</label>';
									msg +='<select id="reschedule_reason" name="reschedule_reason" onchange="javascript:show_hide_other(this.value);" class="form-control minimal"><option value="">Please select a reason</option>'+arr_resp[1]+'</select>';
									msg +='</div>';
									msg +='</div>';
									msg +='</div>';

									var btn1 = 'OK';
									var btn2 = 'Cancel';

									var misc = "DONOTASKPASSWORD";
									
									var func1 = 'save_reschedule_reason_weekly';
									var func2 = 'hideConfirmYesNo';

									scheduler_warning_disp_weekly(title, msg, btn1, btn2, func1, func2, misc);
								}
							});
						}else{
							addApptWeek();
						}
						return true;
					}
				}
			});
		}
	}
}

function scheduler_warning_disp_weekly(title, msg, btn1, btn2, func1, func2, misc){

	text = "<div id=\"msgDiv-handle\" class=\"fl section_header\" style=\"width:395px\">"+title+"</div><div class=\"sc_line\" style=\"text-align:left;\">"+msg+"</div>";
	if(misc == "ASKPASSWORD"){
		text += "<div class=\"sc_line\"></div><div class=\"fl\" style=\"text-align:left;\">Admin Password: </div><div class=\"fl\" style=\"text-align:left;\"><input type=\"password\" id=\"AdminPass\" name=\"AdminPass\"></div>";
	}
	text += "<div class=\"sc_line\"></div><div class=\"fl\" style=\"margin-left:145px;text-align:right;\"><input type=\"button\" style=\"display:block;\" value=\""+btn1+"\" onClick=\"window."+func1+"('week')\" class=\"dff_button\"/></div><div class=\"fl\" style=\"width:195px;text-align:left;\"><input type=\"button\" value=\""+btn2+"\" onClick=\"window."+func2+"(-1)\" class=\"dff_button\"/><br><br></div>";			  
	
	document.getElementById('msgDiv').innerHTML = text;
	document.getElementById('msgDiv').style.display = 'block';
}


function addApptWeek(){
//self.close();
	var pt_id = $("#pat_id").val();
	var ap_id = $("#global_apptid").val();
	var mode = $("#global_apptact").val();
	var tmp_id = $("#global_tempid").val();
	var start_time = $("#global_apptsttm").val();
	var doctor_id = $("#global_apptdoc").val();
	var facility_id = $("#global_apptfac").val();
	var start_date = $("#global_apptstdt").val();

	if(pt_id != ""){
		//patient specific data
		
		//appointment specific data
		var ap_notes = ($("#txt_comments").length !== 0) ? escape($("#txt_comments").val()) : "";
		var ap_procedure = ($("#sel_proc_id").length !== 0) ? $("#sel_proc_id").val() : "";

		var send_uri = "save_appt_weekly.php?save_type="+mode+"&pt_id="+pt_id+"&ap_id="+ap_id+"&ap_notes="+ap_notes+"&ap_procedure="+ap_procedure+"&start_date="+start_date+"&start_time="+start_time+"&doctor_id="+doctor_id+"&facility_id="+facility_id+"&tmp_id="+tmp_id;

		//alert(send_uri);

		$.ajax({
			url: send_uri,
			type: "POST",
			success: function(resp){
				var arr_resp = resp.split("~");
				if(arr_resp[0] == "save"){
					pre_load_front_desk(pt_id, ap_id, false);
				}
				if(arr_resp[0] == "addnew" || arr_resp[0] == "reschedule"){
//					var arr_start_date = start_date.split("-");
//					var new_start_date = arr_start_date[2]+"-"+arr_start_date[0]+"-"+arr_start_date[1];					
					hideConfirmYesNo();
					self.close();
					window.opener.$('#global_apptactreason').val('');
					//window.opener.$('#global_ptid').val('');
					window.opener.$('#sel_pat_name').val('');
					window.opener.$('#sel_proc_id').val('');
					window.opener.$('#global_apptid').val('');
					window.opener.$('#global_apptact').val('addnew');
					
					window.opener.load_week_appt_schedule();					
				}
			}
		});
	
	}
}

function drag_name_weekly(ap_id, pt_id, sel_pat_name, sel_proc_id, mode, e){

	if(!ap_id) ap_id = "";
	if(!pt_id) pt_id = "";
	if(!e) e = window.event;
	
	if(ap_id == "get") TestOnMenu();
	if(pt_id == "get") pt_id = $("#global_context_ptid").val();
	if(ap_id == "get") ap_id = $("#global_context_apptid").val();	

	$("#global_ptid").val(pt_id);
	$("#sel_proc_id").val(sel_proc_id);	
	$("#global_apptid").val(ap_id);
	$("#global_apptact").val(mode);
	$("#sel_pat_name").val(sel_pat_name);
	

	$("#appt_drag").addClass("sc_title_font");
	$("#appt_drag").css("backgroundColor", "");			
	$("#appt_drag").width("320");
	$("#appt_drag").css("top", e.clientY);
	$("#appt_drag").css("left", e.clientX);
	
	
	
	var send_uri = "schedule_new_tooltip.php?tool_sch_id="+ap_id+"&pate_id="+pt_id+"&sel_proc_idR="+sel_proc_id;
	$.ajax({
		url: send_uri,
		type: "GET",
		success: function(resp){
			$("#appt_drag").html(resp);
			$("#appt_drag").css("display", "block");
			//document.attachEvent('onmousemove', move_trail);
			$(document).bind("mousemove", move_trail);
		}
	});
}


function save_reschedule_reason_weekly(){
	var reason = $("#reschedule_reason").val();
	if(reason == ""){
		top.fAlert("Please select a reason to continue.");
		return false;
	}else{
		$("#global_apptactreason").val(reason);
		//alert($("#global_apptactreason").val());
		addApptWeek();
	}
}

/*ref phy and pcp popup window*/
function searchPhysicianWindow(){
	search_val =  $('#front_primary_care_name').val();
	search_val = $.trim(search_val);
	target_search_val = '';
	if(search_val!="")
	{
		search_val_arr = search_val.split(',');
		if(search_val_arr[0]!="" && typeof search_val_arr[0] != "undefined")
		{
			last_name_val = $.trim(search_val_arr[0]); 
			last_mid_name_arr = last_name_val.split(' ');			
			target_search_val = $.trim(last_mid_name_arr[1]); 	
			if(target_search_val == "" || typeof target_search_val == "undefined")
			{
				target_search_val = last_mid_name_arr[0];			
			}
		}
		else
		{
			target_search_val = $.trim(search_val_arr[0]); 	
		}
	}

	if(target_search_val != '')
	{
		window.open("../admin/users/searchPhysician.php?btn_sub=search&sel_by=LastName&txt_for="+target_search_val,"window1","width=800,height=500,scrollbars=yes, status=1");	
	}
	else
	{
		window.open("../admin/users/searchPhysician.php","window1","width=800,height=500,scrollbars=yes, status=1");	
	}
	
}
function searchPCPWindow(){
	search_val = $('#pcp_name').val();
	search_val = $.trim(search_val);
	target_search_val = '';
	if(search_val!="")
	{
		search_val_arr = search_val.split(',');
		if(search_val_arr[0]!="" && typeof search_val_arr[0] != "undefined")
		{
			last_name_val = $.trim(search_val_arr[0]); 
			last_mid_name_arr = last_name_val.split(' ');			
			target_search_val = $.trim(last_mid_name_arr[1]); 	
			if(target_search_val == "" || typeof target_search_val == "undefined")
			{
				target_search_val = last_mid_name_arr[0];			
			}
		}
		else
		{
			target_search_val = $.trim(search_val_arr[0]); 	
		}
	}	
	
	if(target_search_val != '')
	{
		window.open("../admin/users/searchPCP.php?btn_sub=search&sel_by=LastName&txt_for="+target_search_val,"window2","width=800,height=500,scrollbars=yes, status=1");
	}
	else
	{
		window.open("../admin/users/searchPCP.php","window2","width=800,height=500,scrollbars=yes, status=1");
	}
}
function get_phy_name_from_search(strVal,id){
	//document.getElementById('front_primary_care_id').value = id;
	//document.getElementById('front_primary_care_name').value = strVal;
	console.log('function from common js file with same name is in usr');
	
}
function get_pcp_name_from_search(strVal,id){
	document.getElementById('pcp_id').value = id;
	document.getElementById('pcp_name').value = strVal;
	
}

//function to sync iolink
//var current_form_id=0;
function funChbxOcHx(obj) {
	obj.checked=true;
	var cur_obj=obj.checked;
	$(".chbx_ochx").each(function(index, element) {
        this.checked=false;
    });
	obj.checked=cur_obj;
	//current_form_id=obj.value;
	$("#global_iolink_ocular_hx_form_id").val(obj.value);
}
function iolink_sync_ocular(mode,iolink_connection_setting_id){
	var schedule_id = $("#global_context_apptid").val();
	var iolink_ocular_hx_form_id = $("#global_iolink_ocular_hx_form_id").val();
	$("#global_iolink_mode").val(mode);
	$("#global_iolink_connection_settings_id").val(iolink_connection_setting_id);
	if((mode=="resync" && iolink_ocular_hx_form_id!=0) || mode=="remove") {
		iolink_sync();
		return;	
	}
	var title = "Select DOS For Ocular History";
	
	$.ajax({
			url: "ocular_hx_dos_ajax.php?ap_id="+schedule_id,
			success: function(resp){
					if(resp) {
						var arr_resp = resp.split(",");
						var arr_respNew = new Array();
						var i="";
						var newVal = "";
						var checkedVal = "";
						var len = arr_resp.length;
						if(len>0) {
							newVal+='<table>';
							for(i=0;i<len;i++) {
								arr_respNew = arr_resp[i].split("~~");
								checkedVal="";
								if(i==0) { 
									checkedVal='checked';
									$("#global_iolink_ocular_hx_form_id").val(arr_respNew[0]); 
								}
								
								newVal+='<tr><td style="padding-left:10px;"><input type="checkbox" class="chbx_ochx" name="chbx_ochx'+i+'" id="chbx_ochx'+i+'" value="'+arr_respNew[0]+'" '+checkedVal+' onClick="funChbxOcHx(this)"></td><td>'+arr_respNew[1]+'</td></tr>';
								if(len==1) {
									$("#global_iolink_ocular_hx_form_id").val(arr_respNew[0]);
								}
							}
							newVal+='</table>';
							
						}
					
						var msg = "<div style=\"text-align:center;\">"+newVal+"</div><div class=\"ml10\"></div>";
						var btn1 = 'OK';
						var btn2 = 'Close';
					
						var misc = "";
						
						var func1 = 'iolink_sync';
						var func2 = 'hideConfirmYesNo';
						
						if(len==0 || len==1) {
							iolink_sync();
						}else {
							scheduler_warning_disp(title, msg, btn1, btn2, func1, func2, misc);
						}
						//iolink_sync(mode,iolink_connection_setting_id);
					}else {
						iolink_sync();	
					}
				}
		});
}

function iolink_sync(){
	top.show_loading_image("show");
	
	//$("#msgDiv_scheduler").hide();
	hide_custom_modal();
	var mode=$("#global_iolink_mode").val();
	var iolink_connection_setting_id=$("#global_iolink_connection_settings_id").val();
	var iolink_ocular_hx_form_id = $("#global_iolink_ocular_hx_form_id").val();
	var facility_type_provider = $("#facility_type_provider").val();
	var schedule_id = $("#global_context_apptid").val();
	var sa_date = get_selected_date();

	var send_uri = "common/iolink_sync.php?mode="+mode+"&sch_id="+schedule_id+"&sa_date="+sa_date+"&iolink_connection_setting_id="+iolink_connection_setting_id+"&iolink_ocular_hx_form_id="+iolink_ocular_hx_form_id+'&facility_type_provider='+facility_type_provider;
	$.ajax({
		url: send_uri,
		type: "GET",
		success: function(resp){
			top.fAlert(resp);//alert(resp);
			$.ajax({
				url: "get_day_name.php?load_dt="+sa_date,
				success: function(day_name){
					top.show_loading_image("hide");
					load_appt_schedule(sa_date, day_name, schedule_id, '', false);
				}
			});
		}
	});
	TestOnMenu();
}
function showIolinkPdf(patentId){ 
	var parWidth = parent.document.body.clientWidth-100;
	window.open('iolink_pdf_page.php?patentId='+patentId,'iolinkPdf','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+',height=550,left=30,top=100');
}

function show_more_procs(div_id){
	document.getElementById("more_options_"+div_id).style.display = "block";
//	document.getElementById("more_options_"+div_id).style.zIndex = 999;
	//$("#more_options_"+div_id).css("display", "block");
}

function hide_more_procs(div_id){
	document.getElementById("more_options_"+div_id).style.display = "none";
	//$("#more_options_"+div_id).css("display", "none");
}

function show_proc_fullname(proc_id){
	var div_name = "proc" + proc_id;
	var new_title = $("#"+div_name).html();
	$("#sel_proc_id").attr("title", new_title);
}

function get_Report(rte_id){
			var h = window.outerHeight-70;
			window.open('../patient_info/eligibility/eligibility_report.php?id='+rte_id,'eligibility_report','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}
function show_rte_div(id){
	$.ajax({
				url: "show_rte_detail.php?rte_id="+id,
				success: function(data){
					$('#rte_information').html(data);
					$('#rte_information').show();
				}
			}); 
}
function hide_rte_div(){
	$('#rte_information').hide();
}

var scroll_response_flag = 0;
var common_ch_color = 1;
var sch_expand_mode = 0;

function manage_slide_buttons()
{
	elemObjAvail = $('#sch_left_portion').parent().css('display');
	
	$("#scroll_control1").attr('disabled',true);//previous
	$("#scroll_control2").attr('disabled',true);//next
	
	var total_provider=$("#hid_prov_count").val();
	var max_slides=1
	if(elemObjAvail=='block'){max_slides=MAX_SCHEDULE_PER_SLIDE;}
	else{max_slides=9;}
	
	var total_slides=Math.ceil(total_provider / max_slides);
	var current_slide=$("#current_slide").val();
	
	if(current_slide>1)$("#scroll_control1").attr('disabled',false);//previous
	else $("#scroll_control1").attr('disabled',true);//previous
	 
	if(current_slide<total_slides)$("#scroll_control2").attr('disabled',false);//next
	else if(current_slide==total_slides)$("#scroll_control2").attr('disabled',true);//next
}

function get_slide(slide)
{
	var current_slide=$("#current_slide").val();
	if(slide=='next')next_slide=parseInt(current_slide)+1;
	else next_slide=parseInt(current_slide)-1;
	//update current slide 
	$("#current_slide").val(next_slide);
	//update slide buttons 
	manage_slide_buttons();
	return next_slide;
}

function load_sch_on_scroll(slide)
{
	var total_provider=$("#hid_prov_count").val();
	var elemObjAvail = $('#sch_left_portion').parent().css('display');
	var max_slides=1
	if(elemObjAvail=='block'){max_slides=MAX_SCHEDULE_PER_SLIDE;}
	else{max_slides=9;}
	var total_slides=Math.floor(total_provider / max_slides);
	var current_slide=$("#current_slide").val();
	top.show_loading_image("show");
	//hide curent slide
	$("#slide_"+current_slide).css('display','none');
	$("#slide_"+current_slide+"_header").css('display','none');
	//get next slide to show
	var next_slide=get_slide(slide);
	$("#slide_"+next_slide).css('display','block');
	$("#slide_"+next_slide+"_header").css('display','block');
	if($("#slide_"+next_slide).html()=='<strong>Loading ...</strong>')
	{
		active_providers = $('#prov_sch_rem_load').val();
		if($.trim(active_providers) != "")
		{
			remain_providers_arr = new Array();
			remain_providers_str = '';
			req_active_providers_arr = new Array();
			req_active_providers_str = '';		
			
			active_providers_arr = active_providers.split(',');
			apal = active_providers_arr.length;
			if(apal > max_slides)
			{
				for(i=0;i<max_slides;i++)
				{
					req_active_providers_arr[i] = active_providers_arr[i];	
				}
				req_active_providers_str = req_active_providers_arr.join(',');
				
				remain_providers_str = remain_providers_arr.join(',');			
				for(i=max_slides;i<apal;i++)
				{
					j = i-max_slides;
					remain_providers_arr[j] = active_providers_arr[i];	
				}
				remain_providers_str = remain_providers_arr.join(',');
			}
			else
			{
				req_active_providers_str = active_providers_arr.join(',');
			}
			
			if($.trim(req_active_providers_str)!="")
			{
				prov_sch_sel_load_val = $('#prov_sch_sel_load').val();
				prov_sch_sel_load_val += ','+req_active_providers_str;	
				$('#prov_sch_sel_load').attr({'value':prov_sch_sel_load_val});
			}
			
			$('#prov_sch_rem_load').attr({'value':remain_providers_str});
			
			load_appt_schedule_on_scroll(req_active_providers_str, '','','','','', "slide_"+next_slide);
		}
	}else
	{
		top.show_loading_image("hide");	
	}
}


/*
Function: load_appt_schedule_on_scroll
Purpose: to load appt templates on scroll
*/

function load_appt_schedule_on_scroll(load_prov, load_dt, day_name, appt_id, load_fd, showAlert, slide){
	if(!appt_id) appt_id = "";
	if(!load_fd) load_fd = "";
	if(typeof(showAlert) == "undefined") showAlert = true;	

	if(!appt_id) var appt_id = "";

	if(load_dt){
		var arr_load_dt = load_dt.split("-");
		set_date(arr_load_dt[0], arr_load_dt[1], arr_load_dt[2]);
	}else{
		load_dt = get_selected_date();
	}

	var arr_load_dt = load_dt.split("-");

	//getting selected facilities & providers
	var sel_fac = get_selected_facilities();
	var sel_pro = load_prov;
	
	scroll_response_flag = 1;
	scrollTopPos = $('#mn1_1').scrollTop();
	scrollTopPos += 80;
	/*ld = document.createElement('div');
	ld.setAttribute('class','fl sch_scroll_loading');
	ld.style.marginTop = scrollTopPos+"px";
	ld.style.marginLeft = "20px";
	ld.innerHTML = '<div class="sc_appt_loader_common">Loading...</div>';
	document.getElementById('appt_slots_cont').appendChild(ld);*/
	
	$.ajax({
		url: "appt_load_on_scroll.php?loca="+sel_fac+"&dt="+load_dt+"&prov="+sel_pro+"&appt_id="+appt_id+"&sid="+Math.random(),
		success: function(resp){
			//$('.sch_scroll_loading',$('#appt_slots_cont')).remove();
			var arr_response = resp.split("____");
			
			//var sh = document.createElement('span');
			//sh.innerHTML = arr_response[0];
			//document.getElementById('lr5').appendChild(sh);
			$("#"+slide+"_header").html(arr_response[0]);
			
			//var s = document.createElement('span');
			//s.innerHTML = arr_response[1];
			//document.getElementById('appt_slots_cont').appendChild(s);
			$("#"+slide).html(arr_response[1]);
			
			scroll_response_flag = 0;
			result_color = 333333 + common_ch_color;
			common_ch_color++;
			document.getElementById('lyr1').style.color = '#'+result_color;	
			top.show_loading_image("hide");
		}
		
	});
	
}

/*
 * Purpose : Collect labels by provider
 */


getMonthNoFromName={'January':'01','Jan':'01','February':'02','Feb':'02','March':'03','Mar':'03','April':'04','Apr':'04','May':'05','June':'06','Jun':'06','July':'07','Jul':'07','August':'08','Aug':'08','September':'09','Sep':'09','October':'10','Oct':'10','November':'11','Nov':'11','December':'12','Dec':'12'};

datesRange='';
label_options='';
label_options_loaded=0;

function collect_labels_by_provider()
{	
	/*var providers_arr='';
	providers=get_selected_providers();
	if(providers){providers_arr=providers.split(',');}
	if(providers_arr.length!=1)
	{
		//$('#sel_pro_labels').parent().parent().css({'visibility':'hidden'});
		return false;
	}
	if(label_options_loaded==0)
	{
		$.ajax({
			url:'get_labels_by_provider_avail_dts.php',
			complete:function(respData)
			{
				label_options=respData.responseText;
				label_options_loaded=1;
				if(label_options!='')
				{
					//$('#sel_pro_labels').html(label_options);

					var dd_pro = new Array();
					dd_pro["listHeight"] = 300;
					dd_pro["noneSelected"] = "Select Appt Type";
					dd_pro["onMouseOut"] = function(){//$("#sel_pro_labels").multiSelectOptionsHide();hLDatesByLbl();
					};

					//$('#sel_pro_labels').multiSelect(dd_pro);
					//$('#sel_pro_labels').parent().parent().css({'visibility':'visible'});				
				}
				else
				{
					//$('#sel_pro_labels').parent().parent().css({'visibility':'hidden'});				
				}
			}
		});	
	}
	else
	{
		if(label_options!='')
		{
			//$('#sel_pro_labels').html(label_options);

			var dd_pro = new Array();
			dd_pro["listHeight"] = 300;
			dd_pro["noneSelected"] = "Select Appt Type";
			dd_pro["onMouseOut"] = function(){
				//$("#sel_pro_labels").multiSelectOptionsHide();hLDatesByLbl();
				};

			//$('#sel_pro_labels').multiSelect(dd_pro);
			//$('#sel_pro_labels').parent().parent().css({'visibility':'visible'});				
		}
		else
		{
			//$('#sel_pro_labels').parent().parent().css({'visibility':'hidden'});				
		}
	}	*/
}

function selPrimaryPhy()
{
    patient_id=$('#global_ptid').attr('value');
    patient_id=eval(patient_id);
    primary_phy_id=eval($('#sel_fd_provider').attr('value'));
    if(patient_id!="" && typeof primary_phy_id!="undefined")
        {			
			$('INPUT',$('#sel_pro_month').parent()).each(function()
			{
				cur_input_val= $(this).attr('value');
				if(cur_input_val==primary_phy_id)
					{
						$('input:checked',$('#sel_pro_month').parent()).attr({'checked':''}).parent().removeClass('checked');
						$(this).attr('checked','checked');
						if($(this).parent().hasClass('checked')!=true)
							{
								$(this).parent().addClass('checked');
								$('#sel_pro_month span').html($(this).parent().text());																
							}
						pro_change_load('day');	
					}
			}); 
        }
		
		return false;
}

/*
 * Purpose : Highlight dates by label or labels selected
 */


function hLDatesByLbl()
{
    datesRange='';
	provider_dates_avail=new Array();
    top.show_loading_image("show");

    facilities1=get_selected_facilities();
    providers=get_selected_providers();
	providers_arr=providers.split(',');
    sel_date=get_selected_date();
    
    labels=get_selected_labels();
	var cur_date_val='';
    pInd=0;
    context_obj='';
	if($.trim(labels)!="" && providers_arr.length==1)
	{	$('.cl_m_h').each(function(ind)
		{
			month_year_html=$.trim($(this).html());
			month_year_arr=month_year_html.split('&nbsp;');
	
			$('.cl_h_d',$(this).parent()).each(function()
			{
				cur_date_val=$.trim($(this).html());
				if (cur_date_val.toLowerCase().indexOf("<br>") == 0)
				{
					if(cur_date_val<10)
					{
						cur_date_val="0"+cur_date_val;
					}
					provider_dates_avail[pInd]=month_year_arr[1]+'-'+getMonthNoFromName[month_year_arr[0]]+'-'+cur_date_val;
		
					pInd++;
				}
			});
	
		}); 
		reqData='selected_date='+sel_date+'&provider_dates='+provider_dates_avail+'&provider_id='+providers+'&selected_facilities='+facilities1+'&labels='+labels;
		
		$.ajax({url:'get_hl_dates_by_label.php',type:'POST',data:reqData,complete:highLightDatesAct});
	}   
	else
	{
		$('div',$('.cl_m_h').parent()).removeClass('l_s_ds');
		top.show_loading_image("hide");
	}
}

function get_selected_labels()
{
   return selectedValuesStr("sel_pro_labels");
}

function highLightDatesAct(respData)
{
    //alert(respData.responseText); return false;
    datesRange=respData.responseText;
    //alert(datesRange); return false;
    datesRange=$.parseJSON(datesRange);   
    //alert(datesRange);return false;
    highLightDatesByLabels();
}

function highLightDatesByLabels()
{
    var providers=get_selected_providers();
	var providers_arr='';
	if(providers){providers_arr=providers.split(',');}
	
	var cur_date_val=0;
	$('div',$('.cl_m_h').parent()).removeClass('l_s_ds');

    if(datesRange!="" && datesRange!=null)
        {
            selected_date=get_selected_date();
			if(providers_arr.length==1)//earlier condition 
			{
				$('.cl_m_h').each(function(ind)
					{
						month_year_html=$.trim($(this).html());
						month_year_arr=month_year_html.split(' ');
	
						$('.cl_h_d',$(this).parent()).each(function()
						{
							cur_date_val=eval($.trim($(this).html()));
							if($.contains(cur_date_val,'<br>')==true)
							{
								cur_date_val_arr=cur_date_val.split('<br>');									
								
								if($.trim(cur_date_val[1])!="")
	
								{
									month_name_str=$('.cl_m_f',$(this)).html();
									if($.trim(month_name_str)!="")
									{
										//month_no=getMonthNoFromName[month_name_str];													
									}
									else
									{
										cur_date_val=cur_date_val_arr[0];	
									}
								}
								
							}
							if(cur_date_val<10)
							{
								cur_date_val="0"+cur_date_val;
							}
							cl_h_d="'"+month_year_arr[1]+'-'+getMonthNoFromName[month_year_arr[0]]+'-'+cur_date_val+"'";
							//alert(cl_h_d+'|'+datesRange);
							if($.inArray(cl_h_d,datesRange)!=-1)
							{
								if(cl_h_d==selected_date)
								{
									$(this).addClass('cl_hili');
								}
								else
								{
									if($(this).hasClass('l_s_ds')!=true)
									{
										$(this).addClass('l_s_ds');
									}
								}
							}
						});
	
					});  
			}
			else
			{
				$('.cl_m_h').each(function(ind)
				{
					month_year_html=$.trim($(this).html());
					month_year_arr=month_year_html.split(' ');
					//alert($(this).parent().html()); return false;
					$('.cl_p_d,.cl_s_d,.cl_d_d,.cl_hili',$(this).parent()).each(function()
					{
						cur_date_val=$.trim($(this).html());	
						month_no=$.trim(getMonthNoFromName[month_year_arr[0]]);		
						
						if($.contains(cur_date_val,'<br>')==true)
						{
							cur_date_val_arr=cur_date_val.split('<br>');									
							
							if($.trim(cur_date_val[1])!="")

							{
								month_name_str=$('.cl_m_f',$(this)).html();
								if($.trim(month_name_str)!="")
								{
									//month_no=getMonthNoFromName[month_name_str];													
								}
								else
								{
									cur_date_val=cur_date_val_arr[0];	
								}
							}
							
						}
						
						if(cur_date_val<10)
							{
								cur_date_val="0"+cur_date_val;
							}
						cl_h_d="'"+month_year_arr[1]+'-'+month_no+'-'+cur_date_val+"'";
						//alert(cl_h_d+'|'+datesRange);
						if($.inArray(cl_h_d,datesRange)!=-1)
							{
								if(cl_h_d==selected_date)
									{
										$(this).addClass('cl_hili');
									}
									else
									{
											if($(this).hasClass('l_s_ds')!=true)
												{
													$(this).addClass('l_s_ds');
												}
									}
							}
					});

				});  				
			}
        }
		
		top.show_loading_image("hide");
}

function setPriPhyOnPatientChange()
{
	if($('#global_ptid').val()!="" && eval(gl_pt_ch_ct)!=eval($('#global_ptid').val()))
	{		
		gl_pt_ch_ct=$('#global_ptid').val();	
		//selPrimaryPhy() is for set the primary phy. autmatically from the selected providers.
		selPrimaryPhy();					
	}
}
function PriPhyFlagSet()
{
	pri_phy_chk_flag=1;
}

// OR stands for Operating Room
function add_or_record(provider_id,facility_id,date_or,ths)
{
	assign_or = ths.value;
	if(provider_id!="" && facility_id!="" && date_or!="")
	{
		top.show_loading_image("show");
		$.ajax(
		{
			url : 'operating_room_allocation.php',
			type : "POST",
			data : 'provider_id='+provider_id+'&facility_id='+facility_id+'&date_or='+date_or+'&assign_or='+assign_or,
			complete : function(resultData)
			{
				top.show_loading_image("hide");
			}
		});			
	}
}

function EnableSaveButton()
{
	top.fmain.document.getElementById("btnsaveInsurance").disabled=false;
}

function connectToRemote(server_id)
{
	window.open("remoteConnect.php?server_c="+server_id,'','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=650,height=100,left=150,top=60')
}

function getToolTip(id,providerRCOId){
	if(id>0){
		var url="../patient_info/insurance/insuranceResult.php?dofrom=acc_reviewpt&id="+id+"&providerRCOId="+providerRCOId;
		$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					document.getElementById('ins_show_div').innerHTML = resp;
				}
		});
		var curPos = getPositionCoords();
		$('#ins_show_div').fadeIn();
		
		document.getElementById('ins_show_div').style.pixelTop = curPos.y;
		document.getElementById('ins_show_div').style.pixelLeft = curPos.x+10;
		var bro_ver=navigator.userAgent.toLowerCase();
		//if browser is crhome or firfox or safari then we need to placement issue
		if(bro_ver.search("chrome")>1 || bro_ver.search("firefox")>1){
			$("#ins_show_div").css({"display":"inline-block",top: parseInt(curPos.y-50), left: curPos.x+10});
			
		}
	}else{
		$('#ins_show_div').fadeOut()
	}
}

function hideToolTip()
{
	$('#ins_show_div').fadeOut()	
}

function getPositionCoords(e) {
	if(!e) e = window.event || event;
	else e = e || window.event || event;
	
	var cursor = {x:0, y:0};
	var de = document.documentElement;
	var b = document.body;
	cursor.x = e.clientX + 
		(de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
	cursor.y = e.clientY + 
		(de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	//cursor.x = e.clientX;
	//cursor.y = e.clientY;
	return cursor;
}

function expand_shorten_sch()
{
	elemObjAvail = $('#sch_left_portion').parent().css('display');
	//remove any binded appt or reschedule appt action
	$(document).unbind("mousemove", move_trail);
	$("#global_apptact").val('');
	hide_tool_tip();
	
	if(elemObjAvail == 'block')
	{
		$('#sch_left_portion').parent().css({'display':'none'});
		$('#day_save').removeClass('col-lg-7');
		$('#day_save').addClass('col-lg-12');
		
		//$('#wn20,#mn1_1').css({'width':'100%'});
		//$('#hold2,#wn2').css({'width':'97%'});
		//rq_width = $('#hold').width();
		//$('#hold,#wn').width(rq_width+570);
		sch_expand_mode = 1;
		/*		
		wndo9 = new dw_scrollObj('mn1_1', 'mnlyr1_1');			
		lyr_1=document.getElementById('mnlyr1_1');
		dw_scrollObj.GeckoTableBugFix('mn1_1');	
		
		wndo = new dw_scrollObj('wn', 'lyr1');			
		dw_scrollObj.GeckoTableBugFix('wn');
		lyr=document.getElementById('lyr1');
				
		wndo1 = new dw_scrollObj('wn_1', 'lyr1_1');			
		dw_scrollObj.GeckoTableBugFix('wn_1');	
				 
		wndo3 = new dw_scrollObj('wn2', 'lyr2');			
		dw_scrollObj.GeckoTableBugFix('wn2');
				 
		wndo2 = new dw_scrollObj('ContextMenu', 'ContextMenu_1');			
		dw_scrollObj.GeckoTableBugFix('ContextMenu');		
		*/
	}
	else
	{
		$('#sch_left_portion').parent().css({'display':'block'});
		$('#day_save').addClass('col-lg-7');
		$('#day_save').removeClass('col-lg-12');
		//$('#wn20,#mn1_1').css({'width':'100%'});
		//$('#hold2,#wn2').css({'width':'97%'});
		//rq_width = $('#hold').width();
		//$('#hold,#wn').width(rq_width-570);
		
		sch_expand_mode = 0;		
		/*		
		wndo9 = new dw_scrollObj('mn1_1', 'mnlyr1_1');			
		lyr_1=document.getElementById('mnlyr1_1');
		dw_scrollObj.GeckoTableBugFix('mn1_1');	
		
		wndo = new dw_scrollObj('wn', 'lyr1');			
		dw_scrollObj.GeckoTableBugFix('wn');
		lyr=document.getElementById('lyr1');
				
		wndo1 = new dw_scrollObj('wn_1', 'lyr1_1');
		dw_scrollObj.GeckoTableBugFix('wn_1');	
				 
		wndo3 = new dw_scrollObj('wn2', 'lyr2');			
		dw_scrollObj.GeckoTableBugFix('wn2');
				 
		wndo2 = new dw_scrollObj('ContextMenu', 'ContextMenu_1');			
		dw_scrollObj.GeckoTableBugFix('ContextMenu');				
		*/
	}
		/*dw_scrollObj.resetPos('mn1_1');
		dw_scrollObj.resetPos('wn');
		dw_scrollObj.resetPos('wn_1');
		dw_scrollObj.resetPos('wn2');
		
		dw_scrollObj.setArrParams = [];
		dw_scrollObj.masterArr('mn1_1', 'mnlyr1_1')
		dw_scrollObj.masterArr('wn', 'lyr1');
		dw_scrollObj.masterArr('wn_1', 'lyr1_1');
		dw_scrollObj.masterArr('wn2', 'lyr2');	*/
	
		/*get_sch_width_on_scroll();
	
	result_color = 333333 + common_ch_color;
	common_ch_color++;
	document.getElementById('lyr1').style.color = '#'+result_color;	*/
	 pro_change_load('day');
}

function stopEventsinSch(e)
{
	if (!e)
	{
		var e = window.event;
		e.cancelBubble = true;
	}
	if (e.stopPropagation) e.stopPropagation();
}

var sadc_times_from = '';
var sadc_sch_date = '';
var sadc_fac_id = '';
var sadc_provider_id = '';
var sadc_temp_id = '';
var sadc_label_type = '';
var sadc_status = '';
var sadc_user_type = '';
var sadc_is_group_label = '';
var sadc_un_val = '';
function load_set_appt(times_from,sch_date,fac_id,provider_id,temp_id,un_val,label_type,status,user_type,is_group_label)
{
	//holding add appt on double click on time slot
	pat_name = '';
	pat_fname = $('#global_ptfname').val();
	if($.trim(pat_fname) != "")
	{
		pat_lname = $('#global_ptlname').val();
		pat_mname = $('#global_ptmname').val() != "" ? " "+$('#global_ptmname').val() : "";
		pat_name = pat_lname+pat_mname+", "+pat_fname;
	}
	
	times_from_arr = times_from.split(':');
	day_pattern = 'AM';
	if(times_from_arr[0] == 12) {day_pattern = 'PM';}
	if(times_from_arr[0] > 12) {day_pattern = 'PM'; times_from_arr[0] -= 12;}
	
	$("#sadc_procedure_site").val("");
	$("#sadc_sel_proc_id").val("");
	$("#sadc_sec_sel_proc_id").val("");
	$("#sadc_ter_sel_proc_id").val("");
		
	$('#sadc_txt_patient_name').val(pat_name);
	$('#sadc_appt_tm_view').html(times_from_arr[0]+':'+times_from_arr[1]+" "+day_pattern);
	$('#set_appt_div_slot_dc').modal("show");

	if(sadc_times_from != times_from || (un_val && sadc_un_val!=un_val))
	{
		sadc_times_from = times_from;
		sadc_label_type = label_type;
		sadc_status = status;
		sadc_user_type = user_type;
 		sadc_is_group_label = is_group_label;
		sadc_un_val = un_val;	
	}
	sadc_sch_date = sch_date;
	sadc_fac_id = fac_id;
	sadc_provider_id = provider_id;
	sadc_temp_id = temp_id;
}

function add_appt_bydcon_slot()
{
	if( check_deceased() ) return false;

	$("#global_apptact").val("addnew");
	ap_id = ''; $("#global_apptid").val(ap_id);
	$("#pri_eye_site").val($('#sadc_site_pri').val());
	$("#sec_eye_site").val($('#sadc_site_sec').val());
	$("#ter_eye_site").val($('#sadc_site_ter').val());
	
	$("#procedure_site").val($('#sadc_site_pri').val());
	$("#sel_proc_id").val($('#sadc_sel_proc_id').val());
	$("#sec_sel_proc_id").val($('#sadc_sec_sel_proc_id').val());
	$("#ter_sel_proc_id").val($('#sadc_ter_sel_proc_id').val());
	if(!$('#global_ptfname').val())
	{
		top.fAlert('Please select an Patient to add appointment');
		return false;	
	}
	top.fmain.sch_drag_id(sadc_times_from, sadc_sch_date, sadc_fac_id, sadc_provider_id, sadc_temp_id, sadc_un_val, sadc_label_type, sadc_status, '', '-1', sadc_user_type, sadc_is_group_label);
	$("#set_appt_div_slot_dc").modal("hide");
	top.show_loading_image("show");
}

function searchPatient(){
	var name = document.getElementById("sadc_txt_patient_name").value;
	var findBy = document.getElementById("sadc_txt_findBy").value;
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
		if(isNaN(name)){
			//window.open("../scheduler_v1_1_1/search_patient_popup.php?btn_enter="+findBy+"&btn_sub="+name,"mywindow","width=800,height=500,scrollbars=yes");
			window.open("search_patient_popup.php?sel_by="+findBy+"&txt_for="+name+"&btn_sub=Search&call_from=scheduler","mywindow","width=800,height=500,scrollbars=yes");
		}
		else{
			$.ajax({
				url: 'chk_patient_exists.php',
				type: 'POST',
				data: 'pid='+name+'&findBy='+findBy,
				success: function(resultData)
				{
					if(resultData == 'n')
					{
						top.fAlert('Patient not found');	
					}
					else
					{
						pid = eval(resultData);
						pre_load_front_desk(pid,'','');	
					}				
				}
			});						
		}
	}
	return false;
}

function searchPatient2(obj){
	var patientdetails = obj.value.split(':');
	if(isNaN(patientdetails[0]) == false){
		document.getElementById("sadc_patientId").value = patientdetails[0];
		document.getElementById("sadc_txt_patient_name").value = patientdetails[1];
	}
}

//Print Vision PC --
function print_vision_pc_1(form_id){
	if(form_id == "undefined" || typeof form_id == "undefined")
	{
		form_id = 0;	
	}
	var str="";
	if(top.JS_WEB_ROOT_PATH){
		str = top.JS_WEB_ROOT_PATH;	
	}else if(opener && opener.top.JS_WEB_ROOT_PATH){
		str = opener.top.JS_WEB_ROOT_PATH;
	}
	
	if(str!=""){str+='/interface/main/';}
	
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;				
	//window.open(str+'print_patient_pc.php?printType=1&print_form_id='+form_id,'printPatientPC','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	window.open('../chart_notes/requestHandler.php?printType=1&elem_formAction=print_pc&print_form_id='+form_id,'printPatientPC','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	
}
//Print Vision PC --

/*-----  Remote Server Functions  -----*/

function is_remote_server()
{
	var return_bool = false;
	return return_bool;
}

function get_server_id()
{
	return $('#sel_server').val();
}

function change_server(ths)
{
	server_val = ths.value;
	if(server_val == 0)
	{
		top.changeSrcFun(top.fmain,'../scheduler_v1_1_1/base_day_scheduler.php','1','Scheduler');
	}
	else
	{
		get_prov_fac_drop_down(server_val);		
	}
}

function json_resp_handle(resp)
{
	respData = $.parseJSON(resp);	
	if($.trim(respData.sch.error) != "")
	{
		top.fAlert('Remote Connection Error - '+respData.sch.error);	
		return '';
	}
	return $.trim(respData.sch.data);
}

function ju_encode_reqArr(reqTaskArray)
{
	return encodeURIComponent(JSON.stringify(reqTaskArray));	
}

function get_prov_fac_drop_down(server_val)
{
	var data_sender = "server_id="+server_val;
	top.show_loading_image("show");	
	$.ajax({
		url : 'get_prov_fac_drop_down.php',
		type: 'POST',
		data: data_sender,
		complete : function(resp)
		{
			resultData = resp.responseText;
			resultData_arr = resultData.split('___*___');
			var fac_data = resultData_arr[0];
			var prov_data = resultData_arr[1]; 
			$('#facilities_cnt').html(fac_data);
			$('#sel_pro_month_cnt').html(prov_data);

			var dd_fac = new Array();
			dd_fac["listHeight"] = 300;
			dd_fac["noneSelected"] = "Select All";
			dd_fac["onMouseOut"] = function(){$("#facilities").multiSelectOptionsHide();fac_change_load('day');};
			$("#facilities").multiSelect(dd_fac);			

			if(server_val == 0)
			{
				var dd_pro = new Array();
				dd_pro["listHeight"] = 300;
				dd_pro["noneSelected"] = "Select All";
				dd_pro["onMouseOut"] = function(){$("#sel_pro_month").multiSelectOptionsHide();pro_change_load('day');};
				$("#sel_pro_month").multiSelect(dd_pro);				
			}
			fac_change_load('day');
			top.show_loading_image("hide");				
		}
	});
}

/*-------- Remote Server Code Ends here  ---------*/

function pt_deposits_fun(){
	top.popup_win("common/patient_pre_payment.php","width=1580,scrollbars=0,height=700,top=100,left=5");
}
function get_appt_hx_for_sort()
{
	var json_appthx_date_arr = {};
	$(".remote_apptHx_row",$("#appt_hx_sort")).each(function()
	{
		var date_val = $(".remote_apptHX_date",$(this)).val();
		//alert(date_val);
		var date_val_arr = date_val.split(',');
		var year_val = parseInt(date_val_arr[0],10);
		var month_val = parseInt(date_val_arr[1],10);
		var day_val = parseInt(date_val_arr[2],10);
		var hour_val = parseInt(date_val_arr[3],10);
		var minutes_val = parseInt(date_val_arr[4],10); 
		
		var date_val_extract = new Date(year_val,month_val,day_val,hour_val,minutes_val,0).getTime();		
		json_appthx_date_arr[date_val_extract] = $(this)[0].outerHTML;		
	});
	var sort_init_arr = new Array();
	var ix_ind = 0;
	for(x in json_appthx_date_arr)
	{
		sort_init_arr[ix_ind] = x; 	
		ix_ind++;
	}
	sort_init_arr.sort(function(a,b){return b-a});
	
	var result_json_appthx_arr = {};
	for(var xn=0;xn<sort_init_arr.length;xn++)
	{
		var date_val_extract = sort_init_arr[xn];

		result_json_appthx_arr[date_val_extract] = json_appthx_date_arr[date_val_extract];
	}	
	var result_appthx_str = "";
	for(xs in result_json_appthx_arr)
	{
		result_appthx_str += result_json_appthx_arr[xs];
	}
	var apptHxHeader = '<tr height="20" bgcolor="#4684ab"><td width="19%" class="text_b_w" align="left" nowrap="nowrap">Date Time</td><td width="19%" class="text_b_w" align="left">Provider</td><td width="15%" class="text_b_w" align="left">Location</td><td width="21%" class="text_b_w" align="left">Procedure</td><td width="20%" class="text_b_w" align="left">Comments</td></tr>';
	result_appthx_str = "<table>"+apptHxHeader+result_appthx_str+"</table>";
	return result_appthx_str;	
}
function get_avaiable_slot(selected_labels,act,sch_timing,c_date,pid){
	if(selected_labels=="" || typeof(selected_labels)=="undefined"){
		selected_labels=selectedValuesStr("sel_all_labels");
	}
	
	var event_id=$("#chain_event").val();
	if(!event_id && !selected_labels || event_id)
	{
		selected_labels="Slot without labels~~NA";		
	}
	var action_c="";
	var	get_current_provider=selectedValuesStr("provider_label");
	//var	get_current_provider=$("#provider_label").val();
	if(selected_labels && get_current_provider){
		var get_current_date=get_selected_date();
		var	get_current_facility=selectedValuesStr("facilities_label");
		if($('#current_avail_date').val()==""){
			$('#current_avail_date').val(get_current_date);
		}
		if(act){
			var curr_d=$('#current_avail_date').val()
			var date_c=curr_d.split("-");
			var dd=mm=yy=new_date="";
			yy=date_c[0];mm=date_c[1];dd=date_c[2];
			if(act=="next"){
				mm=parseInt(mm)+1;
				if(mm>12){
					mm=1;
					yy=parseInt(yy)+1;
				}		
			}
			if(act=="prev"){
				mm=parseInt(mm)-1;
				if(mm==0){
					mm=12;
					yy=parseInt(yy)-1;
				}
			}
			
			get_current_date=yy+"-"+mm+"-01";
			$('#current_avail_date').val(get_current_date);
		}
		if($('#current_avail_date').val()){
			get_current_date=$('#current_avail_date').val();
		}
		var patient_p="";
		if(pid){
			patient_p="&pat_id="+pid;
		}
		if(sch_timing=="" || typeof sch_timing == "undefined"){
			var sch_timing=$(".sch_timing_radio:checked").val();
		}
		
		var fac_conc='';
		if(get_current_facility){fac_conc='&facility_sel='+get_current_facility}
		var sel_day_option='';
		var day_option=selectedValuesStr("days_of_week");
		if(day_option){sel_day_option='&days_sel='+day_option;}
		//$("#next_available_slot").html("<img src='../../library/images/sch-loader.gif'>");
		var file_url = WEB_ROOT+"/interface/scheduler/";
			file_url+='ajax_next_appointment.php?current_date='+get_current_date+'&current_provider='+get_current_provider+"&sel_label="+ encodeURIComponent(selected_labels)+action_c+fac_conc+"&event_id="+ event_id +"&sch_timing="+ sch_timing+patient_p+sel_day_option+ "&random_string="+Math.random();
		
		top.master_ajax_tunnel(file_url,get_avaiable_slot_callBack);
		/*$.ajax({
			url:file_url,
			complete:function(respData){
				if(respData.responseText){
					$("#next_available_slot").html(respData.responseText);
					var d=respData.responseText;
					$('#next_available_slot_div').modal('show');
				}
			}
		});*/	
	}
}

//cal back function for get_avaiable_slot
function get_avaiable_slot_callBack(response, etc)
{
	if(response){
		$("#next_available_slot").html(response);
		$('#next_available_slot_div').modal('show');
	}		
}
function add_appointment_next_sch(time_from,eff_date_add,fac_id,provider_id,tmp_id,proc,label_t,valid_proc,p_date,cday,obj, label){
	//this condition is removed because of it when we add appt from next available without procedure it does given us "choose procedure first" alert
	// && $("#sel_proc_id").val()
	if($("#global_ptid").val()){
		if(!$("#sel_proc_id").val())
		{
			top.fAlert('Please select Procedure.');
		}
		else if( check_deceased() ) { return false; }
		else
		{
			var ap_id=0;
			var mode ='addnew';
			var pt__id=$("#global_ptid").val();
			var ap_id=$("#global_apptid").val();
			var mode = $("#global_apptact").val();
			if(mode=='reschedule' && (ap_id!='' || typeof(ap_id)!='undefined'))
			{
				//do nothing
			}else
			{
				$("#global_apptact").val('');
				$("#global_apptact").val('');
				ap_id='';mode='addnew';
			}
			drag_name(ap_id, pt__id, mode);
			sch_drag_id(time_from,eff_date_add,fac_id,provider_id,tmp_id,proc,label_t,valid_proc,label,'-1');
		}
	}else{
		top.fAlert('Please select patient first');
		load_calendar(p_date, cday,'nonono');
		load_appt_schedule(p_date, cday,'nonono');
		setTimeout(function(){$('#'+obj).focus()},2500);
	}
}

function add_appointment_next_sch_multi(procedures,appt_count,start_date,facility_id,valid_proc,p_date,cday,obj){
	if($("#global_ptid").val()){
		
		var val_string="";
		var found_val=0;
		//check times~:~template~:~label type~:~provider~:~procedure
		var pro_arr=procedures.split(',');
		 for(drop_down=0;drop_down<appt_count;drop_down++)
		 {
			var sel_string= $("#timing_"+pro_arr[drop_down]).val();
			if(typeof(sel_string)!='undefined')
			{
				if(val_string)
				val_string=val_string+'~::~'+sel_string;
				else
				val_string=sel_string; 
				found_val++;
			}

		 }
		 
		 if(found_val<appt_count)
		 {
			 top.fAlert('Please select Appt Time for each procedure')
			 return false;
		 }
		 var pt__id=$("#global_ptid").val();
		 drag_name('', pt__id, 'addnew');
		 //for(drop_down=1;drop_down<=appt_count;drop_down++)
		 //{
			//var val_string= $("#timing_"+drop_down).val();
//			var val_arr=val_string.split("~:~");
//			start_time=val_arr[0];
//			tmp_id=val_arr[1];
//			label_t=val_arr[2];
//			doctor_id=val_arr[3];
//			tempproc=val_arr[4];

			start_time='';
			tmp_id='';
			label_t='';
			doctor_id='';
			tempproc='';
			//alert(time_from,eff_date_add,fac_id,provider_id,tmp_id,label_t,valid_proc);
			//sch_drag_id(time_from,eff_date_add,fac_id,provider_id,tmp_id,label_t,valid_proc,'','-1');
		
			var pt_id = $("#global_ptid").val();
			var ap_id = $("#global_apptid").val();
			var mode = $("#global_apptact").val();
		
			var pt_fname =  $("#global_ptfname").val();
			var pt_lname =  $("#global_ptlname").val();
			var pt_mname =  $("#global_ptmname").val();
			var pt_emr = $("#global_ptemr").val();
			
			var init_date_rs = $('#init_date_rs').val();
			var init_st_time_rs=$('#init_st_time_rs').val();
			var init_et_time_rs=$('#init_et_time_rs').val();
			var init_acronym_rs=$('#init_acronym_rs').val();
			var init_provider_id = $('#init_prov_id').val();
			var init_fac_id = $('#init_fac_id').val();		
			
			var ap_act_reason = ($("#global_apptactreason").length !== 0) ? escape($("#global_apptactreason").val()) : "";
			//alert(ap_act_reason);
			
			if(pt_id != ""){
				//patient specific data
				var pt_doctor_id = ($("#sel_fd_provider").length !== 0) ? $("#sel_fd_provider").val() : "";
				
				var pt_pcp_phy_id = ($("#pcp_id").length !== 0) ? $("#pcp_id").val() : "";
				var pt_pcp_phy = ($("#pcp_name").length !== 0) ? escape($("#pcp_name").val()) : "";
		
				var pt_ref_phy_id = ($("#front_primary_care_id").length !== 0) ? $("#front_primary_care_id").val() : "";
				var pt_ref_phy = ($("#front_primary_care_name").length !== 0) ? escape($("#front_primary_care_name").val()) : "";
		
				var pt_street1 = ($("#frontAddressStreet").length !== 0) ? escape($("#frontAddressStreet").val()) : "";
				var pt_street2 = ($("#frontAddressStreet2").length !== 0) ? escape($("#frontAddressStreet2").val()) : "";
				var pt_city = ($("#frontAddressCity").length !== 0) ? escape($("#frontAddressCity").val()) : "";
				var pt_state = ($("#frontAddressState").length !== 0) ? escape($("#frontAddressState").val()) : "";
				var pt_zip = ($("#frontAddressZip").length !== 0) ? escape($("#frontAddressZip").val()) : "";
				var pt_zip_ext = ($("#frontAddressZip_ext").length !== 0) ? escape($("#frontAddressZip_ext").val()) : "";
		
				var pt_email = ($("#email").length !== 0) ? escape($("#email").val()) : "";
				var pt_photo_ref = ($("#photo_ref").is(":checked")==true) ? 1 : 0;
		
				var pt_home_ph = ($("#phone_home").length !== 0) ? escape($("#phone_home").val()) : "";
				var pt_work_ph = ($("#phone_biz").length !== 0) ? escape($("#phone_biz").val()) : "";
				var pt_cell_ph = ($("#phone_cell").length !== 0) ? escape($("#phone_cell").val()) : "";
		
				var pt_status = ($("#elem_patientStatus").length !== 0) ? $("#elem_patientStatus").val() : "";
				var pt_other_status = ($("#otherPatientStatus").length !== 0) ? escape($("#otherPatientStatus").val()) : "";
				var pt_dod_patient = ($("#dod_patient").length !== 0) ? escape($("#dod_patient").val()) : "";
		
				//appointment specific data
				var ap_routine_exam = ($("#chkRoutineExam").length !== 0) ? $("#chkRoutineExam").val() : "";
				var ap_ins_case_id = ($("#choose_prevcase").length !== 0) ? $("#choose_prevcase").val() : "";
				var ap_notes = ($("#txt_comments").length !== 0) ? encodeURIComponent($("#txt_comments").val()) : "";
				var ap_pickup_time = ($("#pick_up_time").length !== 0) ? escape($("#pick_up_time").val()) : "";
				var ap_arrival_time = ($("#arrival_time").length !== 0) ? escape($("#arrival_time").val()) : "";
				//var ap_procedure = ($("#sel_proc_id").length !== 0) ? $("#sel_proc_id").val() : "";
				var ap_procedure = tempproc;
				var sec_ap_procedure = ($("#sec_sel_proc_id").length !== 0) ? $("#sec_sel_proc_id").val() : "";
				var ter_ap_procedure = ($("#ter_sel_proc_id").length !== 0) ? $("#ter_sel_proc_id").val() : "";	
				
				var pri_eye_site = ($("#pri_eye_site").length !== 0) ? $("#pri_eye_site").val() : "";
				var sec_eye_site = ($("#sec_eye_site").length !== 0) ? $("#sec_eye_site").val() : "";
				var ter_eye_site = ($("#ter_eye_site").length !== 0) ? $("#ter_eye_site").val() : "";
				
				var facility_type_provider = ($("#facility_type_provider").length !== 0) ? $("#facility_type_provider").val() : "";				
				var referral = ($("input[type=checkbox]#sa_ref_management").is(":checked") ? 1 : 0);
				var ref_management = "&pt_referral="+referral;
                
                var sa_verification = ($("input[type=checkbox]#sa_verification_req").is(":checked") ? 1 : 0);
                var verification_req = "&pt_verification="+sa_verification;
				
				var send_uri = "save_changes.php?save_type="+mode+"&pt_id="+pt_id+"&ap_id="+ap_id+"&pt_doctor_id="+pt_doctor_id+"&pt_pcp_phy_id="+pt_pcp_phy_id+"&pt_pcp_phy="+pt_pcp_phy+"&pt_ref_phy_id="+pt_ref_phy_id+"&pt_ref_phy="+pt_ref_phy+"&pt_street1="+pt_street1+"&pt_street2="+pt_street2+"&pt_city="+pt_city+"&pt_state="+pt_state+"&pt_zip="+pt_zip+"&pt_zip_ext="+pt_zip_ext+"&pt_email="+pt_email+"&pt_photo_ref="+pt_photo_ref+"&pt_home_ph="+pt_home_ph+"&pt_work_ph="+pt_work_ph+"&pt_cell_ph="+pt_cell_ph+"&pt_status="+pt_status+"&pt_other_status="+pt_other_status+"&pt_dod_patient="+pt_dod_patient+"&ap_routine_exam="+ap_routine_exam+"&ap_ins_case_id="+ap_ins_case_id+"&ap_notes="+ap_notes+"&pri_eye_site="+pri_eye_site+"&sec_eye_site="+sec_eye_site+"&ter_eye_site="+ter_eye_site+"&ap_pickup_time="+ap_pickup_time+"&ap_arrival_time="+ap_arrival_time+"&ap_procedure="+ap_procedure+"&start_date="+start_date+"&start_time="+start_time+"&doctor_id="+doctor_id+"&facility_id="+facility_id+"&tmp_id="+tmp_id+"&pt_fname="+pt_fname+"&pt_mname="+pt_mname+"&pt_lname="+pt_lname+"&pt_emr="+pt_emr+"&ap_act_reason="+ap_act_reason+"&tempproc="+tempproc+'&init_st_time_rs='+init_st_time_rs+'&init_et_time_rs='+init_et_time_rs+'&init_acronym_rs='+init_acronym_rs+'&init_provider_id='+init_provider_id+'&init_fac_id='+init_fac_id+'&sec_ap_procedure='+sec_ap_procedure+'&ter_ap_procedure='+ter_ap_procedure+'&init_date_rs='+init_date_rs+'&multi_sel_string='+val_string+'&facility_type_provider='+facility_type_provider+ref_management+verification_req;
			//alert(send_uri);false;
				$.ajax({
					url: send_uri,
					type: "POST",
					success: function(resp){
						//alert(resp);
						//return false;
						if(is_remote_server() == true)
						{
							resp = json_resp_handle(resp);				
						}
						var arr_resp = resp.split("~");
						if(arr_resp[0] == "save"){
							pre_load_front_desk(pt_id, ap_id, false);	
						}
						/*if(arr_resp[0] == "addnew" || arr_resp[0] == "reschedule"){
							var arr_start_date = start_date.split("-");
							var new_start_date = arr_start_date[2]+"-"+arr_start_date[0]+"-"+arr_start_date[1];					
							load_calendar(new_start_date, arr_resp[1], '', false);					
						}*/
						$("#global_apptact").val("");
						$("#global_apptactreason").val("");
						$("#global_apptstid").val("");
						$("#global_apptsttm").val("");
						$("#global_apptdoc").val("");
						$("#global_apptfac").val("");
						$("#global_apptstdt").val("");
						$("#global_appttempproc").val("");
						$('#init_fac_id').val("");
						$('#init_prov_id').val("");
						hideConfirmYesNo();
					}
				});
			}


		// }
		 
		 load_calendar(p_date, cday, '', false);	
	}else{
		top.fAlert('Please select patient first');
		load_calendar(p_date, cday,'nonono');
		load_appt_schedule(p_date, cday,'nonono');
		setTimeout(function(){$('#'+obj).focus()},2500);
	}
}

var label_load=false;
var facility_load=false; 
function collect_labels(){
	var pid=$("#global_ptid").val();

	if( check_deceased() ) return false;

	$("#next_available_slot_div").modal('show');
	//================Facility Label Options======================//
	var provier_id=facility_id=fac_pro="";
	var label_options='';var proc_s=opslot=false;
	var label_p;
	$.ajax({
		url:'ajax_get_pat_fac.php?pat_id='+pid,
		complete:function(respData){
			fac_pro=respData.responseText;
			if(fac_pro){
				var arr_facpro=fac_pro.split("~||~");
				provier_id=arr_facpro[0];
				facility_id=arr_facpro[1];
			}
			var d_pro=selectedValuesStr("sel_pro_month");
			var d_fac=selectedValuesStr("facilities");
			
			
			if(provier_id){
				//$("#provider_label").val(provier_id);
				
				var arr_selected_prov = provier_id.split(",");
				$("#provider_label option").each(function(id,elem){
					var value = $(elem).val();
					if(value.length > 0 || typeof(value) != 'undefined'){
						if($.inArray(value,arr_selected_prov)!=-1){
							$(elem).prop('selected',true);
						}
					}
				});
				
			}else{
				//provier_id_get=d_pro.split(",");
				//$("#provider_label").val(provier_id_get[0]);
				
				var arr_selected_prov = d_pro.split(",");
				$("#provider_label option").each(function(id,elem){
					var value = $(elem).val();
					if(value.length > 0 || typeof(value) != 'undefined'){
						if($.inArray(value,arr_selected_prov)!=-1){
							$(elem).prop('selected',true);
						}
					}
				});
				
			}
			
			$("#provider_label").selectpicker("refresh");
			
			if(facility_id==""){
				facility_id=d_fac;
			}
			var facility_option=$("#facility_options").html();
			$('#facilities_label').append(label_options);
						
			var facility_op=document.getElementById("facilities_label");
			var l;
			l = facility_op.options.length;
			
			var arr_selected_sess_facs= '';
			if(facility_id){
				arr_selected_sess_facs= facility_id.split(",");
			}
			var bl_fac_add;
			for(var t=0;t<(l);t++){ 
				o = facility_op.options[t];
				
				for(var j = 0; j < arr_selected_sess_facs.length; j++){
					//o.selected = false;
					bl_fac_add = false;
					if(o.value == arr_selected_sess_facs[j]){
						bl_fac_add = true;
							break;
					}
				}
				if(bl_fac_add==true){
					o.selected = true;
				}
			}
			
			$("#facilities_label").selectpicker("refresh");
			/*var dd_pro = new Array();
			dd_pro["listHeight"] = 300;
			dd_pro["noneSelected"] = "Select Provider";
			dd_pro["onMouseOut"] = function(){$("#provider_label").multiSelectOptionsHide();}
			$("#provider_label").multiSelect(dd_pro);	
			
			var dd_fac = new Array();
			dd_fac["listHeight"] = 300;
			dd_fac["noneSelected"] = "Select Facilities";
			dd_fac["onMouseOut"] = function(){$("#facilities_label").multiSelectOptionsHide();}
			$("#facilities_label").multiSelect(dd_fac);	
		*/
			label_p=$("#sel_proc_id option:selected").text();
			if(label_p && label_p!='-Reason-'){
				get_avaiable_slot(label_p,'','','',pid);
				proc_s=true;
			}
			if(proc_s==false){
				get_avaiable_slot('Slot without labels','','all_day','',pid);opslot=true;
			}
			facility_load=false;
			if($('select#sel_all_labels option').length==0){
				$.ajax({
					url:'get_labels_by_provider_avail_dts.php',
					complete:function(respData){
						label_options=respData.responseText;
						if(label_options){
							label_options=label_options.replace("Slot without labels-NA","Open Time Slot");
							if(opslot==true){
								label_options=label_options.replace('value="Slot without labels~~NA"','value="Slot without labels~~NA" Selected');
							} 
							if(proc_s==true && proc_s!="-Procedure-"){
									label_options=label_options.replace('value="'+label_p+'~~Procedure"','value="'+label_p+'~~Procedure" Selected');
							}
							$('#sel_all_labels').append(label_options);
							$("#sel_all_labels").selectpicker("refresh");
							/*var dd_label = new Array();
							dd_label["listHeight"] = 300;
							dd_label["noneSelected"] = "Select Labels";
							dd_label["onMouseOut"] = function(){$("#sel_all_labels").multiSelectOptionsHide();};
							$('#sel_all_labels').multiSelect(dd_label);
							$('#sel_all_labels').css({"width":"180px"});*/
						}
					}
				});
			}
			$("#next_available_slot").html("<div>Please select the label</div>");
			$("#div_curr_month").html("");
		}
	})			
}

function get_current_month(month_val,provier_id,facility_id){
	var pid=$("#global_ptid").val();
 	if(month_val){
		$("#div_curr_month").html(month_val);
	}
}

//function created to sort out mouse button click detect in new version browser
 function WhichButton (event) 
 {
		// all browsers except IE before version 9
	if ('which' in event) {
		switch (event.which) {
		case 1:
			return 0;
			break;
		case 2:
			return 1;
			break;
		case 3:
			return 2;
			break;
		}
	}
	else {
			// Internet Explorer before version 9
		if ('button' in event) {
			var buttons = "";
			if (event.button & 1) {
				return 0;
			}
			if (event.button & 2) {
				if (buttons == "") {
					return 2;
				}
				else {
					return 2;
				}
			}
			if (event.button & 4) {
				if (buttons == "") {
					return 1;
				}
				else {
					return 1;
				}
			}
			
		}
	}
}


function open_todo(){
	var record_exist;
	$.ajax({
			url:'to_do_first_avai.php?check_rows_todo=y',
			complete:function(respData){
			record_exist=respData.responseText;
		}
	});
	
	if(record_exist==2){
		var file_path='to_do_first_avai.php';
		var parentWid = parent.document.body.clientWidth;
		var parenthei = parent.document.body.clientHeight;
		window.open(file_path,'to_do','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width='+parentWid+',height='+parenthei+',left=10,top=100');
	}
}
function getWks(load_wk)
{
	if(load_wk){
	$.ajax({
		url: "to_do_first_avai.php?load_wk="+load_wk,
		success: function(resp){
			if(resp){
				//document.getElementById('patientSearcchResponse').style.display='block';
				//document.getElementById('day').innerHTML=resp;	
				//$("#day").html(resp);	
				$("#week").html(resp);
			}			
		}
	});
	}
}
function print_pt_key(){
	TestOnMenu();
	var pt_id = $("#global_context_ptid").val();
	window.open("print_pt_key.php?patient_id="+pt_id,"Print_PT_KEY","width=900","height=700","left=250","scrollbars=yes","resizable=yes");	
}
function get_accept_assignment(priInsStr){
	//"AA – Courtesy Billing"
	//"NAA - Courtesy Billing"
	//"NAA - No Courtesy Billing"
	$('#accept_assignment_div').html("AA");
	$('#accept_assignment_div').attr('title', 'Accept Assignment');
	if(priInsStr!=0){
		var val_arr = priInsStr.split("-|S|-");
		if(val_arr[1]==1){
			 $('#accept_assignment_div').html("NAA - CB");
			 $('#accept_assignment_div').attr('title', 'NAA - Courtesy Billing');
		}else if(val_arr[1]==2){
			 $('#accept_assignment_div').html("NAA - No CB");
			 $('#accept_assignment_div').attr('title', 'NAA - No Courtesy Billing');
		}
	}
}

function hide_div(val){
	document.getElementById("re_schedule_menu").style.display = val;
}

function Highlight(Object)
{
	if(!MouseOn)
	{
		TempColor = Object.style.color;
		Object.style.color = '#9F5000';
		Object.style.textDecoration = 'underline';
		MouseOn = true;
	}
	else
	{
		Object.style.color = TempColor;
		Object.style.textDecoration = 'none';
		MouseOn = false;
	}
}

 
function get271Report(id){
	var h = get271Report_hight;
	top.popup_win('../patient_info/eligibility/eligibility_report.php?id='+id,'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}

function save_new_prov_sch(){
	var msg = '';
	if($("#anps_sel_fac").val() == "" && msg == ""){
		msg = "- Please select Facility.";
	}
	if($("#anps_sel_pro").val() == "" && msg == ""){
		msg = "- Please select Provider name.";
	}
	if(($("#ansp_start_hour").val() == "" || $("#ansp_start_min").val() == "") && msg == ""){
		msg = '- Please select Template start time.';
	}
	if(($("#ansp_end_hour").val() == "" || $("#ansp_end_min").val() == "") && msg == ""){
		msg = '- Please select Template end time.';
	}
	if(msg){
		alert(msg);
	}else{
		EnableDisable(1);
		document.frm_dump_month12.submit();
	}
}
//patient search related functions
function setShowFindByVal(){
	document.getElementById("findByShow").value = document.getElementById("findBy").value;
}
function setSearchParameters(){
	searchPatientInFrontDesk(document.getElementById("findBy"));
}
function setDefaultShowFindByVal(){
	document.getElementById("findByShow").value = "Active";
}
//function related patient action taken from iportal-  approval
function approve_operation(row_id,ths){
	var dt = approve_operation_dt;
	ths_parent = $(ths).parent();	
	if(row_id != "" && parseInt(row_id)){
		ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
	}	
	$.ajax({
		url:'../../iportal_config/handle_pt_registration.php',
		data:'sel_op=approve&row_id='+row_id,
		type:'POST',
		complete:function(respData){
			var resp = jQuery.parseJSON(respData.responseText);
			if(resp.status=="success"){
				ths_parent.html('<div style="color:#090;font-weight:bold;">Approved<br />Patient Id: '+resp.pt_id+'</div>');				
			}
			else if(resp.status=="error"){
				ths_parent.html('<div style="color:#CC0000;font-weight:bold;">Error while approving</div>');				
			}
		}
	});
}
function disapprove_operation(row_id,ths){
	ths_parent = $(ths).parent();
	if(row_id != "" && parseInt(row_id)){
		ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
	}	
	$.ajax({
		url:'../../iportal_config/handle_pt_registration.php',
		data:'sel_op=decline&row_id='+row_id,
		type:'POST',
		complete:function(respData){
			var resp = jQuery.parseJSON(respData.responseText);
			if(resp.status=="success"){
				ths_parent.html('<div style="color:#F00;font-weight:bold;">Declined</div>');
			}
		}
	});		
}
function approve_all_operation(indx) {
	var all_id = window.top.$('input#hidd_iportal_approve').val();
	if(typeof(all_id)=="undefined"){all_id=window.top.fmain.hidden_approveIds;}
	var all_id_arr = new Array();
	if(all_id) {
		all_id_arr = all_id.split(',');
		row_id = all_id_arr[indx];
		if(all_id_arr.length>indx) {
			if(row_id != "" && parseInt(row_id)){
				if($('#iportal_approve_'+row_id).text() !="Declined")
				$('#iportal_approve_'+row_id).html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
			}
			$.ajax({
				url:'../../iportal_config/handle_pt_registration.php',
				data:'sel_op=approve&row_id='+row_id,
				type:'POST',
				complete:function(respData){
					var resp = jQuery.parseJSON(respData.responseText);							
					if(resp.status=="success"){
						$('#iportal_approve_'+row_id).html('<div style="color:#090;font-weight:bold;">Approved<br />Patient Id: '+resp.pt_id+'</div>');		
						approve_all_operation(parseInt(indx)+1);
					}
					else if(resp.status=="error"){
						$('#iportal_approve_'+row_id).html('<div style="color:#CC0000;font-weight:bold;"> Error while approving.</div>');				
					}
				}
			});	
		}
		//if(all_id_arr.length == indx)
		//location.reload();		
	}
}

function approve_cl_operation(all_id, mode) {
	if(mode=='approve_all'){
		var all_id = window.top.$('input#hidd_iportal_cl_approve').val();
		if(typeof(all_id)=="undefined"){all_id=window.top.fmain.hidd_iportal_cl_approve;}
	}
	
	var resultLabel='';
	if(mode=='approve' || mode=='approve_all'){resultLabel='Approved';}else{ resultLabel='Declinded';}

	var all_id_arr = new Array();
	if(all_id || mode=='approve' || mode=='decline') {
		if(mode=='approve_all'){
			all_id_arr = all_id.split(',');
		}else{
			all_id_arr[0]=all_id;
		}

		if(all_id_arr.length>0) {
			for(x in all_id_arr){
				orderNum=all_id_arr[x];
				if(top.$('#iportal_cl_approve_'+orderNum).text() !="Declined" && top.$('#iportal_cl_approve_'+orderNum).text()!="Approved")
				top.$('#iportal_cl_approve_'+orderNum).html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
			}
			$.ajax({
				url:'../../iportal_config/approve_cl_orders.php',
				data:'mode='+mode+'&orderNum='+all_id,
				type:'POST',
				success:function(respData){
					resp = jQuery.parseJSON(respData);
					arrResult1=resp.arrResult;
					
					for(x in arrResult1){
						if(arrResult1[x]=='success'){
							top.$('#iportal_cl_approve_'+x).html('<div style="color:#090;font-weight:bold;">Order '+resultLabel+'</div>');		
						}else if(arrResult1[x]=="error"){
							top.$('#iportal_cl_approve_'+x).html('<div style="color:#CC0000;font-weight:bold;"> Error </div>');
						}
						
					}
				}
			});	
		}
	}
}	

// Multi Level Dropdown [Simple Menu]
function set_val_text_sch(ths,menuId,elemId, valTxt){
	if(ths === '' || typeof(ths) === 'undefined'){}else{
		var elem_val = $(ths).parent().find('input').val();
		if(elem_val != '' || typeof(elem_val) != 'undefined'){
			$('#'+elemId+'').val(elem_val).change();
			$('#'+menuId+'').dropdown('toggle');
			//change display value
			if(valTxt && valTxt!='Clear')
			$('#'+menuId+'').html("<a href=\"javascript:void(0)\" class='status_icon '>"+valTxt+"</a>");	
			else
			$('#'+menuId+'').html("<img src='../../library/images/eyeicon1.png' width='20' height='13' alt='Site' title='Site' class='pointer'/>");
		}
	}
}

function ref_management(){
	var url = top.JS_WEB_ROOT_PATH + "/interface/scheduler/referral/index.php";
	top.fmain.location = url;
}

function chk_referral(call_from) {
	
	if( typeof call_from !== 'string') return false;
	
	var tmp_appt_id = parseInt($("#global_context_apptid").val());
	
	var chk = false;
	if( call_from =='proc_sel') {
		var obj1 = $("#sel_proc_id option:selected",top.fmain.document);
		var obj2 = tmp_appt_id ? '' : $("#tmp_provider_id0",top.fmain.document);
	}
	else if( call_from == 'load_front_desk' || call_from == 'load_insurance' ) {
		var obj1 = $("#tmp_provider_id0",top.fmain.document);
		var obj2 = tmp_appt_id ? '' : $("#sel_proc_id option:selected",top.fmain.document);
	}
	if(obj1.length > 0) { if( obj1.data('referral') ) { chk = true;	}}
	
	if( !chk ) {
		if(typeof obj2 == 'object' && obj2.length > 0) {
			if( obj2.data('referral') ) { chk = true;	} 
		}
	}
	$("input[type='checkbox']#sa_ref_management").prop('checked',chk);
	
}

function verification_sheet(){
	var url = top.JS_WEB_ROOT_PATH + "/interface/scheduler/verification/index.php?height:650px";
	top.fmain.location = url;
}

function chk_verif_sheet(call_from) {
	if( typeof call_from !== 'string') return false;
	
	var tmp_appt_id = parseInt($("#global_context_apptid").val());
	var chk = false;
	if( call_from =='proc_sel') {
		var obj1 = $("#sel_proc_id option:selected",top.fmain.document);
		var obj2 = tmp_appt_id ? '' : $("#tmp_provider_id0",top.fmain.document);
	}
	else if( call_from == 'load_front_desk' ) {
		var obj1 = $("#tmp_provider_id0",top.fmain.document);
		var obj2 = tmp_appt_id ? '' : $("#sel_proc_id option:selected",top.fmain.document);
	}

    if(typeof(obj1)!='undefined' && obj1.length > 0) { if( obj1.data('verification') ) { chk = true;	}}
	
	if( !chk ) {
		if(typeof obj2 == 'object' && obj2.length > 0) {
			if( obj2.data('verification') ) { chk = true;	} 
		}
	}
	$("input[type='checkbox']#sa_verification_req").prop('checked',chk);
	
}

function view_only_pt_call(mode){
	top.fAlert("You do not have permission to perform this action.");
	if(mode == 1){
		return false;
	}
}
//need library/js/grid_color/spectrum.js and library/js/grid_color/spectrum.css
function load_color_picker(color)
{
	$(".grid_color_picker").spectrum({
	color: color,
	showInput: true,
	className: "full-spectrum",
	showInitial: true,
	showPalette: true,
	showSelectionPalette: true,
	showAlpha: true,
	maxPaletteSize: 10,
	preferredFormat: "hex",
	localStorageKey: "spectrum.demo",
	move: function (color) {
		//if($.isFunction(updateBorders)) updateBorders(color);
	},
	show: function () {

	},
	beforeShow: function () {

	},
	hide: function (color) {
		//if($.isFunction(updateBorders)) updateBorders(color);
	},

	palette: [
		["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)", /*"rgb(153, 153, 153)","rgb(183, 183, 183)",*/
		"rgb(204, 204, 204)", "rgb(217, 217, 217)", /*"rgb(239, 239, 239)", "rgb(243, 243, 243)",*/ "rgb(255, 255, 255)"],
		["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
		"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
		["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
		"rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
		"rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
		"rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
		"rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
		"rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
		"rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
		"rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
		/*"rgb(133, 32, 12)", "rgb(153, 0, 0)", "rgb(180, 95, 6)", "rgb(191, 144, 0)", "rgb(56, 118, 29)",
		"rgb(19, 79, 92)", "rgb(17, 85, 204)", "rgb(11, 83, 148)", "rgb(53, 28, 117)", "rgb(116, 27, 71)",*/
		"rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
		"rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
	]
});
}

function check_deceased(){

	if( typeof top.fmain.patientDeceased !== 'undefined' ) {
		if( top.fmain.patientDeceased ) {
			var msg = (top.fmain.pd_alert !== 'undefined' && top.fmain.pd_alert !== '') ? top.fmain.pd_alert : 'Not allowed';
			top.fAlert(msg);
			return true;
		}
	}
	return false;
}

function loadInsHx()
{
	var _modal = $("#InsuranceHx",top.document);
	if( _modal.length > 0 )
	{
		_modal.modal('show');
	}
	else 
	{
		$.ajax({
			url: "../../interface/patient_info/ajax/insurance/act_exp_open_insu_case.php"
		})
		.done(function(resp){
			resp = JSON.parse(resp);
			if(typeof(resp.html)!= "undefined"){
				show_modal('InsuranceHx','Patient All Insurance History', resp.html,'','','modal-lg');
				set_modal_height('InsuranceHx');
			}	
		});
	}

}

function show_scanned(obj,id,val,type){
	var img_src = $(obj).data('src');
	var img_type = $(obj).data('type');
	if( img_type == 'pdf')
		var modal_src = '<object style="width:100%; min-height:500px;" type="application/pdf" data="'+img_src+'"></object>';
	else
		var modal_src = '<img src="'+img_src+'" style="max-width:100%; width:auto; height:auto;" >';
	
	var modal_content = '<div class="row"><div class="col-sm-12">'+modal_src+'</div></div>';
	var modal_footer = '<div class="row"><div class="col-sm-12 text-center"><button class="btn btn-danger" data-dismiss="modal">Close</button></div></div>';
	show_modal('ins_scan_modal','Insurance Scan Documents',modal_content,modal_footer,'400','modal-lg');
	set_modal_height('ins_scan_modal');
	//window.open('show_scan_img_acc.php?id='+id+'&val='+val+'&type='+type,'scan','');
}

//function related appointment action taken from iportal-  approval
function approve_disapprove_appt(row_id,ths,operation_status){
	ths_parent = $(ths).parent().parent();	
	
	if(row_id != "" && parseInt(row_id)){
		ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
	}	
	$.ajax({
		url:'../../interface/scheduler/appt_cancel_portal_handle.php',
		data:'sel_op='+operation_status+'&row_id='+row_id,
		type:'POST',
		complete:function(respData){
			var resp = jQuery.parseJSON(respData.responseText);
			if(resp.status=="success"){
				var color = '#FF0000';
				if(resp.approval == 'Approved') {
					color = '#090';
				}	
				ths_parent.html('<div style="color:'+color+';font-weight:bold;">'+resp.approval+'<br />Patient Id: '+resp.pt_id+' '+resp.msg+'</div>');				
				$('#cbk'+row_id).prop("disabled", true);
			}
			else if(resp.status=="error"){
				ths_parent.html('<div style="color:#CC0000;font-weight:bold;">Error while approving/declining '+resp.msg+'</div>');				
			}
		}
	});
}

function approve_disapprove_all_appt(operation_status,indx,sel){
	
	cbkObj =  top.document.getElementsByName('cbkPrev');
	var row_id = sub_obj = '';
	var row_id_sub = '';
	var ths = '';
	var sel = sel || false;
	var row_id_arr = new Array();
	for(var a = 0; a < cbkObj.length; a++){
		sub_obj = cbkObj.item(a);
		if(sub_obj.checked == true && sub_obj.disabled==false) {
			row_id_sub = sub_obj.getAttribute('data-id');
			row_id_arr.push(row_id_sub);
			sel=true;
		}
	}
	
	row_id = row_id_arr[indx];
	if(row_id_arr.length>indx) {
		ths = $('#btn_approve_'+row_id);
		ths_parent = $(ths).parent().parent();	
		if(row_id != "" && parseInt(row_id)){
			ths_parent.html('<div style="color:#5a9dec;font-weight:bold;">Processing..</div>');	
		}	
		$.ajax({
			url:'../../interface/scheduler/appt_cancel_portal_handle.php',
			data:'sel_op='+operation_status+'&row_id='+row_id,
			type:'POST',
			complete:function(respData){
				var resp = jQuery.parseJSON(respData.responseText);
				if(resp.status=="success"){
					var color = '#FF0000';
					if(resp.approval == 'Approved') {
						color = '#090';
					}	
					ths_parent.html('<div style="color:'+color+';font-weight:bold;">'+resp.approval+'<br />Patient Id: '+resp.pt_id+' '+resp.msg+'</div>');				
					$('#cbk'+row_id).prop("disabled", true);
					
					approve_disapprove_all_appt(operation_status,indx,sel)
				}
				else if(resp.status=="error"){
					ths_parent.html('<div style="color:#CC0000;font-weight:bold;">Error while approving/declining '+resp.msg+'</div>');				
				}
			}
		});
	}
	if(sel==false) {
		top.fAlert("Please select appointment's request(s) from Portal");
	}
}

function pull_cancel_request(apptload) {
	top.show_loading_image("show");
	$.ajax({
		url:'../../interface/scheduler/appt_cancel_portal_handle.php',
		data:'pull_cancel_appt=yes',
		type:'POST',
		complete:function(respData){
			top.show_loading_image("hide");
			var resp = respData.responseText;
			var vl = '';
			//window.top.location.reload();
			if(resp.search('success')>=0 || apptload=='1') { vl = "window.top.location = '../../interface/scheduler/appt_cancel_portal.php';";/*vl = 'window.top.location.reload();'*/}
			top.fAlert(resp,'',vl);
		}
	});
}

function launch_telemedicine()
{
	var parentWidth = parent.document.body.clientWidth;
	var parentheight = parent.document.body.clientHeight;

	top.show_loading_image("show");
	$.ajax({
		url: top.JS_WEB_ROOT_PATH+'/interface/scheduler/telemedicine_url.php',
		type:'GET',
		dataType: 'json',
		success: function(resp)
		{
			if( typeof resp.ssoUrl !== 'undefined' )
			{
				window.open(resp.ssoUrl, 'telemedicine', 'width='+parseInt(parentWidth-50)+'px,height='+parentheight+'px,');
			}
			else if( typeof resp.message !== 'undefined' )
			{
				top.fAlert(resp.message,'SSO Token Error');
			}
			else
			{
				top.fAlert('Unable to launch Updox telemedicine','Error');
			}
			
		},
		complete: function()
		{
			top.show_loading_image("hide");
		}
	});
}