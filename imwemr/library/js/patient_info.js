// Global Javascript variable for Demographics
var count=0;
var grid_id = 0;
var search_data = [];
var search_header_html = '<h4 class="modal-title col-xs-4 col-sm-5 col-md-3" id="modal_title">Select Patient</h4><span class="col-xs-6 col-md-4 input-group"><input type="text" id="sp_ajax" class="form-control col-xs-5" title="Search Patient (by last name) " placeholder="Search Patient (by last name)" /><label class="input-group-addon btn" for="sp_ajax"><span class="glyphicon glyphicon-search"></span></label></span>';

var myvar = top.fmain;

/*
* Function : xhr_ajax 
* Purpose - To handle Ajax Request and Response
*						This function will use all data attributes
*						to send params in ajax request 	
* Params -
*	r:	
* $_this - Holds this object from which event Occured 
* c : Boolean true|false whether to get current field value
* f : file name to which ajax request send must be in patient info/ajax folder
*/
function xhr_ajax(r,$_this,c,f)
{ 
	f = f || 'demographics/ajax_handler';
	if(typeof c !== 'boolean') { c = false; }
	
	if(typeof $_this == 'object')
	{ 
		var p = $_this.data();
		var d = '';
		$.each(p,function(i,v){ if(i !== 'prevVal') { d	+= '&' + i + '=' + v;} });
		if(c) d += '&val='+$_this.val();
		d = d.substr(1);
		
		var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/"+f+".php?"+d;
		var callback = top.xhr_ajax;
		if(top.fmain) callback = top.fmain.xhr_ajax;
		top.master_ajax_tunnel(url,callback,'','json');
		top.show_loading_image('hide');
	}
	else if(typeof r !== 'undefined')
	{ 
		if(r.action === 'search_patient')
		{ 
				grid_id = r.grid; search_data = r.data;
				$("#search_patient_result #sp_ajax").data('grid',r.grid);
				var ht = ['Name','ID','Address','Phone'];
				if(r.grid == 0) ht = ['Name','SS','DOB','ID'];
				var html = '';
				
					html	+=	'<table class="table table-bordered table-hover table-striped scroll release-table">'
					html	+=	'<thead class="header">';
					html	+=	'<tr class="grythead">';	
					html	+=	'<th class="col-xs-3">'+ht[0]+'</th>';
					html	+=	'<th class="col-xs-2">'+ht[1]+'</th>';
					html	+=	'<th class="col-xs-4">'+ht[2]+'</th>';
					html	+=	'<th class="col-xs-3">'+ht[3]+'</th>';
					html	+=	'</tr>';
					html	+=	'</thead>';
					html	+=	'<tbody>';
					
				if(r.pdata.length > 0 )
				{ 
					var g = (r.grid > 0) ? 'family' : 'resp';
					var k = (r.iKey > 0) ? 'data-i-key="'+r.iKey+'"' : '';
					for(i in r.pdata) {
						
						var f1 = (r.grid > 0) ? r.pdata[i].name 	: r.pdata[i].name;
						var f2 = (r.grid > 0) ? r.pdata[i].id 		: r.pdata[i].ss;
						var f3 = (r.grid > 0) ? r.pdata[i].address: r.pdata[i].dob;
						var f4 = (r.grid > 0) ? r.pdata[i].phone 	: r.pdata[i].id;
						
						html	+=	'<tr >';
						html	+=	'<td data-label="'+ht[0]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f1+'</a></td>';
						html	+=	'<td data-label="'+ht[1]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f2+'</a></td>';
						html	+=	'<td data-label="'+ht[2]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f3+'</a></td>';
						html	+=	'<td data-label="'+ht[3]+'"><a data-grid="'+g+'" data-row="'+r.pdata[i].id+'" '+k+'>'+f4+'</a></td>';
						html	+=	'</tr>';
					}
				}
				else
				{
					html += '<tr><td colspan="4" class="bg-warning">No Patient Found.</td></tr>'
				}
				html	+=	'</tbody>';
				html	+=	'</table>';
				
				$("#search_patient_result .modal-body").html(html);
				
		}
		
		else if(r.action === 'search_physician')
		{ 
			$("#search_physician_result #phy_ajax").data('text-box',r.text_box).data('id-box',r.id_box).val('');
			$("#search_physician_result .modal-body").html(r.html);
		}
		
		else if (r.action === 'show_patient_access_log')
		{
				var html = '';
				html	+=	'<table class="table table-bordered table-hover table-striped scroll release-table">'
				html	+=	'<thead class="header">';
				html	+=	'<tr class="grythead">';	
				html	+=	'<th class="col-xs-4">Action</th>';
				html	+=	'<th class="col-xs-4">Description</th>';
				html	+=	'<th class="col-xs-4">Access Time</th>';
				html	+=	'</tr>';
				html	+=	'</thead>';
				html	+=	'<tbody>';
					
				if(r.data.length > 0 )
				{ 
					for(i in r.data)
					{
						html	+=	'<tr >';
						html	+=	'<td data-label="Action">'+r.data[i].action+'</td>';
						html	+=	'<td data-label="Description">'+r.data[i].desc+'</td>';
						html	+=	'<td data-label="Access Time">'+r.data[i].time+'</td>';
						html	+=	'</tr>';
					}
				}
				else
				{
					html += '<tr><td colspan="3" class="text-center">No Record Found.</td></tr>'
				}
				html	+=	'</tbody>';
				html	+=	'</table>';
				
				
				show_modal('access_log_modal',r.title,html,'',350,'modal-md',false);
			
		}
		
		else if ( r.action === 'login_history' )
		{
			var html = '';
				html	+=	'<table class="table table-bordered table-hover table-striped scroll release-table">'
				html	+=	'<thead class="header">';
				html	+=	'<tr class="grythead">';	
				html	+=	'<th class="col-xs-4">#</th>';
				html	+=	'<th class="col-xs-4">Login Date</th>';
				html	+=	'<th class="col-xs-4">Login Time</th>';
				html	+=	'</tr>';
				html	+=	'</thead>';
				html	+=	'<tbody>';
					
				if(r.data.length > 0 )
				{ 
					for(i in r.data)
					{
						html	+=	'<tr >';
						html	+=	'<td data-label="Action">'+r.data[i].counter+'</td>';
						html	+=	'<td data-label="Description">'+r.data[i].date+'</td>';
						html	+=	'<td data-label="Access Time">'+r.data[i].time+'</td>';
						html	+=	'</tr>';
					}
				}
				else
				{
					html += '<tr><td colspan="3" class="text-center">No Record Found.</td></tr>'
				}
				html	+=	'</tbody>';
				html	+=	'</table>';
				
				
				show_modal('login_history_modal',r.title,html,'',350,'modal-md',false);
			
		}
		
		else if( r.action === 'temp_key_generate')
		{
			var response = $.trim(r.response);
			if(response == 'no_priv')
			{
				$("#pt_override_div").modal('show');
				$("#user_password").focus();
				$("#done_btn_pt_override").attr('data-temp-key-size',r.tempKeySize);
			}
			else if(response == 'user_has_no_priv'){
				top.fAlert("Incorrect Password",'',$("#user_password"));
			}
			else if(response=='user_incorrect')
			{
				top.fAlert("Incorrect Password",'',$("#user_password"));
			}
			else
			{
				$('#temp_key').val(response);
				$("#pt_override_div").modal('hide');
				$("#user_password").val("");
				$("#temp_key_chk_val").prop("checked",false);
			}
			
		}
		
		else if(r.action === 'demographics_history')
		{
			show_modal('demographics_history',r.title,r.html,'',400,'modal-lg',false);
		}
		
		else if(r.action === 'validate_form')
		{ 
			var responseText = r.response;
			
			var msg_code_str = '';
			var matched_ssn_pt = '';
			
			var p_obj 	= myvar.$("#pass1");
			var cp_obj	= myvar.$("#pass2"); 
			var rp_obj	=	myvar.$("#elem_physicianName");	
			var pp_obj	=	myvar.$("#primaryCarePhy");	
			var ss_obj	=	myvar.$("#ss");	
			var ss_obj1	=	myvar.$("#ss1");	
			var un_obj1	=	myvar.$("#usernm");	
			
			if(responseText !== "")
			{
				var arrAJAXResp = responseText.split("~~");
				
				//Referring Physician
				if(rp_obj)
				{		
					var refPhyNameNew = rp_obj.val().trim();
					if(refPhyNameNew !== "")
					{								
						var arrPhyNameNewFull = refPhyNameNew.split("; ");
						var arrPhyNameNew = arrPhyNameNewFull[0].split(",");
						if(arrPhyNameNew.length < 2 || arrPhyNameNew.length > 2){
							msg_code_str += "6,";
						}
					}								
				}
				
				//Referring Physician
				if(pp_obj)
				{			
					if(pp_obj.val() !== ""){								
						var primaryPhyNameNew = trim(pp_obj.val());
						var arrPriPhyNameNewFull = primaryPhyNameNew.split("; ");
						var arrPriPhyNameNew = arrPriPhyNameNewFull[0].split(",");
						if(arrPriPhyNameNew.length < 2 || arrPriPhyNameNew.length > 2){
							msg_code_str += "12,";
						}
					}								
				}
				
				//ssn format check
				if(ss_obj.val() !== "")
				{
					var ssn_format = validate_ssn_format(ss_obj.val());
					if(ssn_format == false){
						msg_code_str += "2,";
					}
				}

				//ssn format for resp party check
				if(ss_obj1.val() != "")
				{
					var ssn_format = validate_ssn_format(ss_obj1.val());
					if(ssn_format == false){
						msg_code_str += "11,";
					}
				}
				
				//unique ssn			
				if(arrAJAXResp[0] == "1")
				{
					msg_code_str += "3,";
					ss_obj.trigger('change');
					matched_ssn_pt = arrAJAXResp[2];
				}
				
				//unique login id
				if(arrAJAXResp[1] == "1"){
					//msg_code_str += "4,";
					//un_obj1.trigger('change');
				}
				
				//responsible party alert
				if(resSsnNumber !='' && arrAJAXResp[3] == "1"){
					document.getElementById("hid_resp_party_sel_our_sys").value = "yes";
				}
				
				//confirm pass
				
				if(p_obj){ 
					if(p_obj.val() !== cp_obj.val() ){	
						msg_code_str += "1,";
						p_obj.trigger('change')
						cp_obj.trigger('change')
					}
				}
				
				if(msg_code_str != "")
				{
					var arr_func = [];
					arr_func[0] = "return false";
					arr_func[1] = "";
					window.top.show_loading_image("hide");
					window.top.fmain.pi_show_alert("alert", msg_code_str, arr_msg, arr_focus, arr_func, null, '', matched_ssn_pt);
				}
				else
				{
					var mandatory_fields = window.top.fmain.getMandatoryMsg();
					var advisory_fields = window.top.fmain.showPracMendAlert();
					
					var str_msg = "";
					var str_focus = "";
					for(i = 0; i < arr_msg.length; i++){
						str_msg += arr_msg[i]+"__";
					}
					for(i = 0; i < arr_focus.length; i++){
						str_focus += arr_focus[i]+"__";
					}
					
					if(mandatory_fields != true)
					{
						var arr_func = new Array();
						arr_func[0] = "window.top.show_loading_image('hide');";
						arr_func[1] = "window.top.show_loading_image('hide');";
						window.top.fmain.pi_show_alert("mandatory", mandatory_fields, arr_msg, arr_focus, arr_func, "", 400);
					}
					else if(advisory_fields != true)
					{
						var arr_func = [];
						arr_func[0] = "window.top.show_loading_image('show',250, 'Please wait..');window.top.fmain.ask_for_after_save_actions('"+escape(str_msg)+"', '"+escape(str_focus)+"')";
						arr_func[1] = "window.top.show_loading_image('hide');";
						window.top.fmain.pi_show_alert("confirm", advisory_fields, arr_msg, arr_focus, arr_func, "", 400);
					}
					else{
						window.top.fmain.ask_for_after_save_actions('','');
						//window.top.fmain.process_save();
					}
				}
			}
		}
		
		// insurance ajax response
		else if(r.action === 'insCompsAnchors')
		{
			var j = r.data;
			arr_r =	j.split("||~***~||");
			top.fmain.$('#priInsCompData').html(arr_r[0]);
			top.fmain.$('#secInsCompData').html(arr_r[1]);
			top.fmain.$('#terInsCompData').html(arr_r[2]);
		}
		
		else if(r.action === 'ins_comp_practice_code')
		{
			$("#tool_tip_div").html(r.data).show('fast');
		}
		
		else if(r.action === 'ins_eligibility')
		{
			var strResp = r.data;
			var arrResp = strResp.split("~~");
			if(arrResp[0] == "1" || arrResp[0] == 1)
			{
				var alertResp = "";
				if(arrResp[1] != ""){
					alertResp += "Patient Eligibility Or Benefit Information Status :"+arrResp[1]+"\n";
				}
				if(arrResp[2] != ""){
					alertResp += "With Insurance Type Code :"+arrResp[2]+"\n \n";
				}
				if(alertResp != "")
				{
					if(arrResp[3] == "A")
					{
					//	document.getElementById('imgEligibility').src = "../../../images/eligibility_green.png";
					}
					else if(arrResp[3] == "INA")
					{
					//	document.getElementById('imgEligibility').src = "../../../images/eligibility_red.png";
					}
					//document.getElementById('imgEligibility').title = alertResp;
					var elId = parseInt(arrResp[4]);
					var strShowMsg = arrResp[5];
					if((elId > 0) && (strShowMsg) == "yes")
					{
						alertResp += "Would you like to set Co-Pay, Deductible and Co-Insurance!\n"
					}
					if((elId > 0) && (strShowMsg) == "yes")
					{
						if(confirm(alertResp) == true)
						{
							var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/eligibility/eligibility_report.php?set_rte_amt=yes&id='+elId;
							window.open(url,'setAmountRTE','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,left=10,top=10');
						}
					}
					else{
						top.fAlert(alertResp);
					}
				}
			}
			else if(arrResp[0] == "2" || arrResp[0] == 2)
			{
				if(arrResp[1] != "")
				{
					top.fAlert(arrResp[1]);
					document.getElementById('imgEligibility').src = "../../../images/eligibility_red.png";
					document.getElementById('imgEligibility').title = arrResp[1];
				}
			}
			else
			{
				top.fAlert(arrResp[0]);
			}
		}
		
		else if(r.action === 'ins_history')
		{
			$("#ins_history_modal .modal-body").html(r.html);
			$("#ins_history_modal").modal('show');
			set_modal_height('ins_history_modal');
		}
		
		else if(r.action === 'check_exist_ins')
		{
			if(r.is_exist)
			{
				top.fAlert('Please expire previous '+r.ins_type+' insurance company.');
			}
			else
			{
				$("#copy_ins_submit_txt").val('Submit');
				top.show_loading_image('show');
				$("#copy_ins_form").submit();
			}
			
		}
		
	}
}

/*
* Function : chk_change 
* Purpose - To Detect change in Prev and current value
* Params -	
* olddata - Holds Previous Values saved in DB
* newData - Holds Current displaying value in Input/Select Box
*/
function chk_change(olddata,newData,e)
{
	e = event ? event : Event;
	var character_code = e.which ? e.which : e.keyCode;
	if(character_code!== 9 && character_code !== 16 ){
		if(olddata !== newData){
			change_flag = true;
		}else{
			if(change_flag !== true){
				change_flag = false;
			}
		}
	}	
}


/*
* Function : search_patient 
* Purpose - to search for patient in Family Info || Responsible Party
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function search_patient($_this)
{
		var v = $_this.val().trim();
		if(v)
		{
			if(!$("#search_patient_result").hasClass('in'))
			{
				$("#search_patient_result").modal();	
			}
			$("#search_patient_result .modal-body").html('<div class="loader"></div>');	
			xhr_ajax('',$_this,true,$_this.attr('data-action'));
		}
		
		if($_this.attr('id') !== 'sp_ajax' && !v)
		{
			top.fAlert('Please enter last name to precede search');
		}
	
}

/*
* Function : fill_grid_info 
* Purpose - to fill fields info from search result
* Params -	
* id - hold grid id | t = type either for family||responsible party 
*/	
function fill_grid_info(id,t,call_from)
{		
		call_from = call_from || 1;
		if(id == '0' || typeof id == 'undefined') return;
		
		var d = search_data[id];
		
		if(t == 'family')
		{
			// Chk Mobile Field is missing -chkMobileTableFamilyInformation_
			var flds = Array('fname_table_family_information','mname_table_family_information','lname_table_family_information','street1_table_family_information','street2_table_family_information','code_table_family_information','city_table_family_information','state_table_family_information','email_table_family_information','phone_home_table_family_information','phone_work_table_family_information','phone_cell_table_family_information');
			
			$("#"+flds[0]+grid_id).val(d.fname);
			$("#"+flds[1]+grid_id).val(d.mname);
			$("#"+flds[2]+grid_id).val(d.lname);
			$("#"+flds[3]+grid_id).val(d.street);
			$("#"+flds[4]+grid_id).val(d.street2);
			$("#"+flds[5]+grid_id).val(d.postal_code);
			$("#"+flds[6]+grid_id).val(d.city);
			$("#"+flds[7]+grid_id).val(d.state);
			$("#"+flds[8]+grid_id).val(d.email);
			$("#"+flds[9]+grid_id).val(d.phone_home);
			$("#"+flds[10]+grid_id).val(d.phone_biz);
			$("#"+flds[11]+grid_id).val(d.phone_cell);
			//$("#"+flds[12]+grid_id).val(d.chk_mobile);
			
			var grid_obj = $("#table_family_information_"+grid_id);
		}
		else
		{
			if(top.fmain.insuranceCaseFrm)
			{
				popUpRelationValue(call_from,d)
				var grid_obj = $("#insPolicy"+call_from+"_table");
			}
			else
			{
				$("#title1").val(d.title);
				$("#fname1").val(d.fname);
				$("#mname1").val(d.mname);
				$("#lname1").val(d.lname);
				$("#suffix1").val(d.suffix);
				$("#status1").val(d.status);
				$("#dob1").val(d.DOB);
				$("#sex1").val(d.sex);
				$("#street1").val(d.street);
				$("#street_emp").val(d.street2);
				$("#rcode").val(d.postal_code);
				$("#rcity").val(d.city);
				$("#rstate").val(d.state);	
				$("#ss1").val(d.ss);	
				$("#phone_home1").val(d.phone_home);	
				$("#phone_biz1").val(d.phone_biz);	
				$("#phone_cell1").val(d.phone_cell);	
				$("#hid_resp_party_sel_our_sys").val('yes');	
				
				$("#title1.selectpicker,#status1.selectpicker").selectpicker('refresh')
					
				var grid_obj = $("#resp_container");
			}
		}
		
		$("#search_patient_result .modal-body").html('<div class="loader"></div>');
		$("#search_patient_result").modal('hide');
		grid_obj.find('input[type="text"],select.minimal,select.selectpicker').triggerHandler('change');
			
}

/*
* Function : search_physician 
* Purpose - to search for Reffering || Primary Care || Co Managed Physicians
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function search_physician($_this)
{
	var id = $_this.attr('id');
	if( id === 'phy_ajax')
	{
		$_this.data('search-by',$("#search_by").val())
	}
	var v = $.trim($_this.val());
	if(v)
	{
		if(!$("#search_physician_result").hasClass('in'))
		{
			$("#search_physician_result").modal();
			$("#search_physician_result").find('#phy_ajax').val('');
		}
		$("#search_physician_result .modal-body").html('<div class="loader"></div>');	
		xhr_ajax('',$_this,true,$_this.data('action'));
	}
	
	else
	{
		top.fAlert('Please enter some text for search');
	}
		
}

function save_data(e)
{
	var characterCode //literal character code will be stored in this variable
	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e
		characterCode = e.which //character code is contained in NN4's which property
	}
	else{
		e = event
		characterCode = e.keyCode //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		if(change_flag==true){
			//top.fmain.front.validSaves();
		}
		else{
			chkCall();				
		}
	}
	else{
		return true 
	}	
}

/*
* Function : do_date_check 
* Purpose - To check From date is greater from To Date
* Params -	
* from - Holds From date field object
* to - Holds To date field object
*/
function do_date_check(from, to)
{	
	if(validate_date(to) && validate_date(from) )
	{
		if (from.value != "" && to.value != "")
		{ 
			if (parse_date(from.value, top.jquery_date_format) >= parse_date(to.value, top.jquery_date_format)) {	
				return true;
			}
			else 
			{	
				if (from.value == "" || to.value == ""){ }
				else{ 
					to.value="";
					top.fAlert("Date of birth can not be greater than current date.");
					return false;
				}
			}
		}
	}
}

/*
* Function : parse_date 
* Purpose - Date Parser according to given format
* Params -	
* input - Holds Date String
* format - Holds Date format in which date will be parsed
*					 IF Not Defined Then Default will be Used from Top 
*/
function parse_date(input, format)
{ 
	if(input)
	{
 		format = format || top.jquery_date_format;
  	var parts = input.match(/(\d+)/g),  
 		i = 0, fmt = {};
		// extract date-part indexes from the format 
  	format.replace(/(Y|d|m)/g, function(part) { fmt[part] = i++; }); 
 		return new Date(parts[fmt['Y']], parts[fmt['m']]-1, parts[fmt['d']]); 
	}
	return;
}


function getPosition(e)
{
	e = window.event;
	var cursor = {x:0, y:0};
	var de = document.documentElement;
	var b = document.body;
	cursor.x = e.clientX + 
		(de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
	cursor.y = e.clientY + 
		(de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	return cursor;
}

var newzip_code;
//To get city and state on the basis of zipcode in add new patient
function zip_vs_state(zip_code,page){
		if((page=="add_patient") || (page=="edit_patient"))	{
			if(document.demographics_edit_form.elem_patientStatus.value != "Active") return;			
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChanged)
		}else if(page=="occupation"){
			if(document.demographics_edit_form.elem_patientStatus.value != "Active") return;
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateoccupation)
		}else if(page=="resp_party"){
			if(document.demographics_edit_form.elem_patientStatus.value != "Active") return;
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateresp_party)
		}else if(page=="add_facility")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChanged)
		}else if(page=="primary")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstate_primary)
		}else if(page=="secondary")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstate_secondary)
		}else if(page=="tertiary")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstate_tertiary)
		}else if(page=="new_patient_popup")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChanged)
		}
		else if(page=="RefferringPhysician"){
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedRef)
		}
		else if(page=="add_insurance")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			newzip_code = zip_code;
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedIns)
		}
		else if(page=="PosFacility")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedPosFacility)
		}
		else if(page=="PosFacilityGroup")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedPosFacilityGroup)
		}
		else if(page=="add_groups")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedAdd_groups)
		}
		else if(page=="add_rem_groups")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.zipstateChangedAdd_groups_rem)
		}
		else if(page=="policy")	{
			var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
			url=url+"?zipcode="+zip_code
			top.master_ajax_tunnel(url,top.fmain.setPolicyState)
		}
}

var family_index = '';
function zip_vs_state_family_state(zip_code, num_id)
{
	if(zip_code == '') { return false; }
	var url=top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php";			
	url=url+"?zipcode="+zip_code
	family_index = num_id;
	top.master_ajax_tunnel(url,top.fmain.setfamilyZipCode)
}

function setfamilyZipCode(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city_table_family_information" + family_index).value=trim(val[0]);
		document.getElementById("state_table_family_information" + family_index).value=trim(val[1]);
		$("#city_table_family_information"+family_index+",#state_table_family_information"+family_index).trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code_table_family_information"+family_index+",#city_table_family_information"+family_index+",#state_table_family_information"+family_index+"").val('');
		$("#code_table_family_information"+family_index).trigger('change').focus();	$("#code_table_family_information"+family_index+",#city_table_family_information"+family_index+",#state_table_family_information"+family_index+"").trigger('change');
	}
	family_index = '';// Reset family index
}

function setPolicyState(result){
	if(result){
		var val=result.split("-");
		document.getElementById("City").value=trim(val[0]);
		document.getElementById("State").value=trim(val[1]);
		$("#City,#State").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#Zip,#City,#State").val('');
		$("#Zip").trigger('change').focus();
		$("#Zip,#City,#State").trigger('change');
	}
}

function zipstateChangedAdd_groups(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city").value=trim(val[0]);
		document.getElementById("state").value=trim(val[1]);
		$("#city,#state").trigger('change');
        if(document.getElementById("state").value=='TX') {
            if($('#THCICSubmitterId_col').length>0) {
                $('#THCICSubmitterId').val('');
                $('#THCICSubmitterId_col').show();
            }
        } else {
            if($('#THCICSubmitterId_col').length>0) {
                $('#THCICSubmitterId').val('');
                $('#THCICSubmitterId_col').hide();
            }
        }
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code,#city,#state").val('');
		$("#code").trigger('blur').focus();
		$("#city,#state").trigger('change');
	}
}

function zipstateChangedAdd_groups_rem(result){
	if(result){
		var val=result.split("-");
		document.getElementById("rem_city").value=trim(val[0]);
		document.getElementById("rem_state").value=trim(val[1]);
		d$("#rem_city,#rem_state").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#rem_zip,#rem_city,#rem_state").val('');
		$("#rem_zip").trigger('blur').focus();
		$("#rem_city,#rem_state").trigger('change');
	}
}

function zipstateChangedRef(result){
	if(result){
		var val=result.split("-");
		document.getElementById("rcity").value=trim(val[0]);
		document.getElementById("rstate").value=trim(val[1]);
		$("#rcity,#rstate").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#rcode,#rcity,#rstate").val('');
		$("#rcode").trigger('change').focus();
		$("#rcode,#rcity,#rstate").trigger('change');
	}
}

function zipstateChangedPosFacility(result){
	if(result){
		var val=result.split("-");
		document.getElementById("pos_facility_city").value=trim(val[0]);
		document.getElementById("pos_facility_state").value=trim(val[1]);
		$("#pos_facility_city,#pos_facility_state").trigger('change');
        if(document.getElementById("pos_facility_state").value=='TX') {
            if($('#THCICID_col').length>0) {
                $('#thcic_id').val('');
                $('#THCICID_col').show();
            }
        } else {
            if($('#THCICID_col').length>0) {
                $('#thcic_id').val('');
                $('#THCICID_col').hide();
            }
        }
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#pos_facility_zip,#pos_facility_city,#pos_facility_state").val('');
		$("#pos_facility_zip").trigger('change').focus();
		$("#pos_facility_zip,#pos_facility_city,#pos_facility_state").trigger('change');
	}	
}

function zipstateChangedIns(result){
	if(result){
		var val=result.split("-");
		document.getElementById("City").value=trim(val[0]);
		document.getElementById("State").value=trim(val[1]);
		$("#City,#State").trigger('change');
	}else{
		//window.open('../../common/addZipCode.php?code='+newzip_code,'mywindow','width=800,height=100');				
	}
}

function zipstateresp_party(result){
	if(result){
		var val=result.split("-");
		document.getElementById("rcity").value=trim(val[0]);
		document.getElementById("rstate").value=trim(val[1]);
		$("#rcity,#rstate").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#rcode,#rcity,#rstate").val('');
		$("#rcode").trigger('change').focus();
		$("#rcode,#rcity,#rstate").trigger('change');
		
	}
}

function zipstateoccupation(result){
	if(result){
		var val=result.split("-");
		document.getElementById("ecity").value=trim(val[0]);
		document.getElementById("estate").value=trim(val[1]);
		$("#ecity,#estate").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#ecode,#ecity,#estate").val('');
		$("#ecode").trigger('change').focus();
		$("#ecode,#ecity,#estate").trigger('change');
	}
}

function zipstate_primary(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city1").value=trim(val[0]);
		document.getElementById("state1").value=trim(val[1]);
		$("#city1,#state1").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code1,#city1,#state1").val('');
		$("#code1").trigger('change').focus();
		$("#code1,#city1,#state1").trigger('change');
	}
}

function zipstate_secondary(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city2").value=trim(val[0]);
		document.getElementById("state2").value=trim(val[1]);
		$("#city2,#state2").trigger('change');
	}else	{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code2,#city2,#state2").val('');
		$("#code2").trigger('change').focus();
		$("#code2,#city2,#state2").trigger('change');
	}
}

function zipstate_tertiary(result){
	if(result){
		var val=result.split("-");
		document.getElementById("city3").value=trim(val[0]);
		document.getElementById("state3").value=trim(val[1]);
		$("#city3,#state3").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code3,#city3,#state3").val('');
		$("#code3").trigger('change').focus();
		$("#code3,#city3,#state3").trigger('change');
	}
}

function zipstateChanged(result){ 
	if(result){
		var val=result.split("-");
		document.getElementById("city").value=trim(val[0]);
		document.getElementById("state").value=trim(val[1]);
		$("#city,#state").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#code").trigger('change');
		$("#city,#state").val('').trigger('change');
		$("#code").trigger('change');
	}
} 

//end here

/*
* Function : call_functions 
* Purpose - Call On click of save button
* Params -
*	val :	Holds Sub Tab name
*/
function call_functions(val)
{
		switch(val)
		{
			case 'demographic_save':
				//required fields
				var reqd_fields = top.fmain.validate_reqd_fields();
				
				if(reqd_fields != true){
					var arr_func = [];
					arr_func[0] = "return false";
					arr_func[1] = "";
					var alertType = "alert";
					if(reqd_fields == '5,') {alertType = "confirm";	arr_func[0] = "top.fmain.call_functions(\'demographic_save_proceed\')";}
					top.fmain.pi_show_alert(alertType, reqd_fields, arr_msg, arr_focus, arr_func);
					return false;
				}
				
				//validate filled fields
				top.fmain.validate_filled_fields();			
			break;
			
			case 'demographic_save_proceed':
			 	//validate filled fields 
				top.fmain.validate_filled_fields();			
			break;
			
			case 'insurance_save':
				//top.resetHidVal();
				var result = top.fmain.showPracMendAlertInsurence();
			break;
		}
}

// Demographics Save && Validation Functions	
function validate_reqd_fields()
{	
	var msg_code_str = "";
	if(myvar.document.getElementById("fname").value == "" || myvar.document.getElementById("lname").value == ""){
		msg_code_str += "0,";
	}
	
	//responsible party
	var patient_age = parseInt(myvar.document.getElementById("patient_age").innerHTML);
	if(patient_age != 0 && patient_age < 18 && myvar.document.getElementById("fname1").value == ""){
		msg_code_str += "5,";
	}
    
	//family info
	var ans = myvar.validateFamilyInfo();
	if(ans == false){
		msg_code_str += "10,";
	}
		
	if(msg_code_str != ""){
		return msg_code_str;
	}
	return true;
}

function pi_show_alert(mode, msg_str, arr_msg, arr_focus, arr_func, response_mode, height_adjustment, optional_val)
{	
	
	var msg_to_show = "Please fill the following fields correctly:<br><br>";
	var set_focus_to = "";
	var focus_set = false;

	if(mode == "mandatory"){
		msg_to_show = "Following fields are mandatory:<br><br>";
	}
	if(mode == "confirm"){
		msg_to_show = "You have not filled the following fields:<br><br>";
	}
	
	var default_width = 375;
	if(response_mode == "multi"){
		msg_to_show = "Please confirm the following actions:<br><br>";
		default_width = 675;
	}
	
	if(msg_str.substring(0,2) == "!!"){
		msg_to_show += msg_str.substring(2);
	}else{
		var arr_show_msg = msg_str.split(",");
		for(i = 0; i < arr_show_msg.length-1; i++){
			if(focus_set == false){
				if(arr_focus[arr_show_msg[i]] != ""){
					set_focus_to = arr_focus[arr_show_msg[i]];
					focus_set = true;
				}				
			}
			
			if(response_mode == "multi"){
				msg_to_show += "<div class=\"col-sm-12\">"+arr_msg[arr_show_msg[i]] + "</div><div class=\"col-sm-2\"><div class=\"radio\"><input type=\"radio\" name=\"r"+arr_show_msg[i]+"\" id=\"r"+arr_show_msg[i]+"_yes\" checked onclick=\"window.top.fmain.set_after_save_actions('"+arr_show_msg[i]+"', '1');\" /><label for=\"r"+arr_show_msg[i]+"_yes\">Yes</label></div></div><div class=\"col-sm-2\"><div class=\"radio\"><input type=\"radio\" name=\"r"+arr_show_msg[i]+"\" id=\"r"+arr_show_msg[i]+"_no\" onclick=\"window.top.fmain.set_after_save_actions('"+arr_show_msg[i]+"', 0);\" /><label for=\"r"+arr_show_msg[i]+"_no\">No</label></div></div><div class=\"clearfix\"></div>";
			}else{
				msg_to_show += arr_msg[arr_show_msg[i]] + "<br>";
				if(set_focus_to=='ss'){
					msg_to_show += optional_val+'<br><br>';
				}
			}
		}
	}
	
	if(mode == "mandatory"){
		if(set_focus_to != ""){
			window.top.fAlert(msg_to_show);
		}else{
			window.top.fAlert(msg_to_show);
		}
	}
	else if(mode == "confirm"){
		msg_to_show += "<br>Do you wish to continue?";
		if(set_focus_to != ""){
			window.top.fancyConfirm(msg_to_show, "", arr_func[0], "window.top.fmain.document.getElementById('"+set_focus_to+"').focus(); "+arr_func[1]);
		}else{
			window.top.fancyConfirm(msg_to_show, "", arr_func[0], arr_func[1]);
		}
	}
	else{
		if(response_mode == ""){
			msg_to_show += "<br>Click OK Button to continue saving.";
		}
		if(set_focus_to != ""){
			if( set_focus_to == 'ss') {
				msg_to_show = '<div style="max-height:500px; overflow:auto;">'+msg_to_show+'</div>';
			}
			window.top.fAlert(msg_to_show, "", "window.top.fmain.document.getElementById('"+set_focus_to+"').focus();");//+arr_func[0]
		}else{
			window.top.fAlert(msg_to_show, "", arr_func[0],700);
		}
	}
}

function validate_filled_fields(resp_cred)
{
    resp_cred = resp_cred || '';

	if(isERPPortalEnabled){
		if( (myvar.document.getElementById("fname1").value != "" || myvar.document.getElementById("lname1").value != "") && resp_cred=='' ){
			var msg='';
			var set_focus_to='erp_resp_username';
			if(document.getElementById("erp_resp_username").value == ""){
				msg=" - Without Username and password the Representative account cannot be created on Patient Portal. ";
				set_focus_to="erp_resp_username";
			}else if(document.getElementById("erp_resp_passwd").value == ""){
				msg=" - Without Username and password the Representative account cannot be created on Patient Portal. ";
				set_focus_to="erp_resp_passwd";
			}
			if(document.getElementById("erp_hidd_passwd").value == ""){
				var new_pass = document.getElementById("erp_resp_passwd").value;
				var confirm_pass = document.getElementById("erp_resp_cpasswd").value;
				if(new_pass != confirm_pass){
					if(confirm_pass==''){
						msg=" - Confirm Representative Password is required. ";
						set_focus_to="erp_resp_cpasswd";
					} else {
						msg=" - Confirm Password does not matches with Password. ";
						set_focus_to="erp_resp_cpasswd";
					}	
				}
			}

			var arr_func = [];
			arr_func[0] = "return false";
			arr_func[1] = "";
			
			if(msg!='') {
				window.top.fancyConfirm(msg, "",  "top.fmain.validate_filled_fields(\'1\')",  "window.top.fmain.document.getElementById('"+set_focus_to+"').focus(); "+arr_func[1]);
				return false;
			}
		}
    }
    
	var userName, ssnNumber;
	userName = myvar.demographics_edit_form.usernm.value;
	ssnNumber = myvar.demographics_edit_form.ss.value;
	resSsnNumber = myvar.demographics_edit_form.ss1.value;
	
	var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/ajax_validation.php?action=validate_form";
	url = url + "&userName=" + userName + "&ssnNumber=" + ssnNumber + "&resSsnNumber=" + resSsnNumber;
	
	var ptStatusObj = $("#elem_patientStatus");
	var pt_status = ptStatusObj.val();
	var prev_pt_status = ptStatusObj.data('prev-val');
	if( prev_pt_status != 'Deceased' && pt_status == 'Deceased' ) {
		msg = "Patient status changed to deceased.<br>All future appointments will be canceled.";
		//window.top.fancyConfirm(msg, "", "top.fmain.onChangePtStatus('"+url+"');", false);
		window.top.fAlert(msg,'',"top.fmain.onChangePtStatus('"+url+"');",'','','Ok',true);
	}
	else {
		top.master_ajax_tunnel(url,top.fmain.xhr_ajax,'','json');
	}
	
}

function onChangePtStatus(url){

	if( typeof url == 'undefined') return false;

	top.master_ajax_tunnel(url,top.fmain.xhr_ajax,'','json');

}
function validateFamilyInfo()
{ 
	for(var a=0; a<25; a++)
	{
		if(document.getElementById("family_information_relatives"+a))
		{
			if((document.getElementById("family_information_relatives"+a).value != "") && (document.getElementById("fname_table_family_information"+a).value == "" || document.getElementById("lname_table_family_information"+a).value == ""))
			{
				if(document.getElementById("fname_table_family_information"+a).value == "")
				{
					familyMemberTextBoxFName = "fname_table_family_information"+a;
				}
				else if(document.getElementById("lname_table_family_information"+a).value == "")
				{
					familyMemberTextBoxLName = "lname_table_family_information"+a;	
				}
				return false;
			}
		}
	}
	return true;
}

function validate_ssn_format(ssn)
{
	var not_valid = false;
	not_valid = validate_ssn(ssn);
	if(not_valid != false){
		myvar.$("#ss").trigger('change');
	}
	return not_valid;
}

function showPracMendAlert()
{ 
 	var msg = "";
	var alertActive = false;
	$.each(mandatory,function(i,v){
		if(typeof i === 'string') {
			var obj = $("#"+i);
			if(obj.length)
			{
				var t_msg = typeof(vocabulary[i]) == 'undefined' ? '' : vocabulary[i];
				t_msg = t_msg.replace(/\\n/g,''); 	
				if(obj.val() === '' && v == '1' ) {
					if(i == "race" || i == "ethnicity" || i == "language")
						msg = msg + '<span class="red-txt">'+t_msg+'</span><br>';
					else
						msg = msg + t_msg +'<br>';
					alertActive = true;
				}
			}
		}
	});
	
	if(alertActive == true){
		return "!!"+msg;
	}
	return true;
}

function getMandatoryMsg()
{ 
	var msg = "";
	var alertActive = false;
	$.each(mandatory,function(i,v){
		if(typeof i === 'string') {
			var obj = $("#" + i);
			if(obj.length) {
				var t_msg = typeof(vocabulary[i]) == 'undefined' ? '' : vocabulary[i];
				t_msg = t_msg.replace(/\\n/g,'');	
				if((obj.val() === '' || obj.val() == null) && v == '2' ) {
					msg = msg + t_msg + '<br>';
					alertActive = true;
				}
			}
		}
	});
			
	if(alertActive == true){
		return "!!"+msg;
	}
	return true;
}

function ask_for_after_save_actions(str_msg, str_focus)
{
	var msg_code_str = "";
	var str_msg = unescape(str_msg);
	var str_focus = unescape(str_focus);
	
	var arr_new_msg = (str_msg) ? str_msg.split("__") : arr_msg;
	var arr_new_focus = (str_focus) ? str_focus.split("__") : arr_focus;
	
	//zip code does not exits
	var zipCodeStatus = myvar.document.getElementById("zipCodeStatus");
	var zipCode = myvar.document.getElementById("postal_code");				
	if(zipCodeStatus.value == 'NA'){
		msg_code_str += "7,";
		myvar.document.getElementById("zipCodeStatus").value = "NA";
	}
	
	//erx registration
	var erx_entry = myvar.demographics_edit_form.erx_entry.value;
	var Allow_erx_medicare = myvar.demographics_edit_form.Allow_erx_medicare.value;
	if(Allow_erx_medicare == 'Yes' && erx_entry == 0){
		msg_code_str += "8,";
		myvar.demographics_edit_form.erx_entry.value = 1;
		myvar.demographics_edit_form.chkErxAsk.value = 1;
	}
	
	//new a/c for resp party
	if( myvar.document.getElementById("hid_resp_party_sel_our_sys")){
		if((myvar.document.getElementById("hid_resp_party_sel_our_sys").value == "no" || myvar.document.getElementById("hid_resp_party_sel_our_sys").value == "") && myvar.document.getElementById('fname1').value != "" && myvar.document.getElementById('lname1').value != ""){																										
			msg_code_str += "9,";
			myvar.document.getElementById("hid_create_acc_resp_party").value = "yes";
		}
	}
	
	if(msg_code_str != ""){
		var arr_func = new Array();
		arr_func[0] = "window.top.fmain.process_save();";
		arr_func[1] = "";
		window.top.fmain.pi_show_alert("alert", msg_code_str, arr_new_msg, arr_new_focus, arr_func, "multi");
		return false;
	}
	window.top.fmain.process_save();
}

function set_after_save_actions(msg_index, val)
{
	if(msg_index == "7"){
		if(val == "1") { val = "NA"; } else { val = "NotOk"; }		
		if(typeof(window.top.int_country) != "undefined" && window.top.int_country == "UK")val = "OK";
		myvar.document.getElementById("zipCodeStatus").value = val;
	}
	if(msg_index == "8"){
		if(val == "1") { val = "1"; } else { val = "0"; }		
		myvar.demographics_edit_form.erx_entry.value = val;
		myvar.demographics_edit_form.chkErxAsk.value = val;
	}
	if(msg_index == "9"){
		if(val == "1") { val = "yes"; } else { val = "no"; }		
		myvar.document.getElementById("hid_create_acc_resp_party").value = val;
	}
}

//to submit form
function process_save(){
	window.top.fmain.demographics_edit_form.submit();
	window.top.show_loading_image("show", "150", "");
}

//Opens Public Health Syndromic Surveillance Data window
function get_phssi_data(){
	var parWidth = parseInt($(window).width() - 500);
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/patient_info/common/pt_phss_info.php','ptInfoPHSSDHL7Export','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width='+parWidth+',height=600,left=10,top=100');
}

//Opens merge patient window
function showMergePatients(){		
	var parWidth = parseInt($(window).width() - 500);		
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/patient_info/common/merge_patient.php','MergePatients','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+'px,height=800px,left=10,top=80');
}

//Opens Medical History Tab Scan Upload And Print actions
function showPtProviders(){		
	var parWidth = parent.document.body.clientWidth;	
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/chart_notes/pt_providers/index.php','patientProvider','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+',height=600,left=10,top=80');
}

//Opens complete patient record print window
function openPrint(){
	var parWidth = parent.document.body.clientWidth;		
	top.popup_win(top.JS_WEB_ROOT_PATH+"/interface/patient_info/common/print_function.php?call_from=demo",'imedic_print','location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width='+parWidth+',height=680');
}

// End Demographics Save && Validation Functions

function zipstateChangedPosFacilityGroup(result){
	if(result){
		var val=result.split("-");
		document.getElementById("fac_group_city").value=trim(val[0]);
		document.getElementById("fac_group_state").value=trim(val[1]);
		$("#fac_group_city,#fac_group_state").trigger('change');
	}else{
		top.fAlert("Please enter correct "+top.zipLabel);
		$("#fac_group_zip,#fac_group_city,#fac_group_state").val('');
		$("#fac_group_zip").trigger('change').focus();
		$("#fac_group_zip,#fac_group_city,#fac_group_state").trigger('change');
	}	
}
