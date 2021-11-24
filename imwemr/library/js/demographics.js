// Demographics JS File
// Code Written By : Gurpreet Singh

var arr_msg = [];
arr_msg[0] 	= vocabulary.first_last_name;
arr_msg[1] 	= vocabulary.pass_confirm;
arr_msg[2] 	= vocabulary.invalid_ssn;
arr_msg[3] 	= vocabulary.unique_ssn;
arr_msg[4] 	= vocabulary.login_already_exist;
arr_msg[5] 	= vocabulary.patient_not_adult;
arr_msg[6] 	= vocabulary.format_ref_phy;
arr_msg[7] 	= vocabulary.zip_not_exist;
arr_msg[8] 	= vocabulary.pt_not_reg_erx;
arr_msg[9] 	= vocabulary.create_acc_grantor;
arr_msg[10] = vocabulary.family_member_not_enter;
arr_msg[11] = vocabulary.invalid_resp_party_ssn;
arr_msg[12] = vocabulary.format_primary_care_phy;

var arr_focus = [];
arr_focus[0] = 'fname';
arr_focus[1] = 'pass2';
arr_focus[2] = 'ss';
arr_focus[3] = 'ss';
arr_focus[4] = 'usernm';
arr_focus[5] = 'fname1';
arr_focus[6] = 'elem_physicianName';
arr_focus[7] = '';
arr_focus[8] = '';
arr_focus[9] = '';
arr_focus[10] = '';
for(var b=0; b<25; b++){
	if(myvar.document.getElementById("fname_table_family_information"+b)) {
		if(myvar.document.getElementById("fname_table_family_information"+b).value == ""){
			arr_focus[10] = "fname_table_family_information"+b;
			break;
		}else if(myvar.document.getElementById("lname_table_family_information"+b).value == ""){								
			arr_focus[10] = "lname_table_family_information"+b;
			break;	
		}
	}
}
arr_focus[11] = 'ss1';

/* 
* Function add_new_address
* Purpose - To add Multiple Address Grids in All Communication grid
* Params = callFrom 
*/
function add_new_address(callFrom)
{
	callFrom = callFrom || '';
	i = $('input[name^="street["]').length;
	html	='';
	html += '<div class="col-xs-12 pt-box"><div class="row grid-box" tabindex="0">';
	html += '<div id="div_address'+i+'">';
	// Header 
	html	+=	'<div class="">';
	html	+=	'<div class="col-sm-12">';
	html	+=	'<h2 class="head"> ';
	html	+=	'<div class="radio radio-inline">';
	html	+=	'<input type="radio" name="all_communication" id="all_communication'+i+'" autocomplete="off" value="'+i+'" />';
	html	+=	'<label for="all_communication'+i+'" >All Communication</label>';
	html	+=	'</div>';
	html	+=	'<span id="address_close'+i+'" title="Delete Address" onClick="$(\'#div_address'+i+'\').remove();" class="pull-right margin-top-20 pointer"><i class="glyphicon glyphicon-remove"></i></span>';
	html	+=	'</h2>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	
	html	+=	'<div class="col-xs-12 ">';
	// Street 1
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="street_'+i+'">Street 1</label>';
	html	+=	'<input name="street['+i+']" id="street_'+i+'" type="text" class="form-control" value="" />';
	html	+=	'</div>';
	
	// Street 2
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="street2_'+i+'">Street 2</label>';
	html	+=	'<input name="street2['+i+']" id="street2_'+i+'" type="text" class="form-control" value="" />';
	html	+=	'</div>';
	
	// Zip Code
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="code'+i+'">'+top.zipLabel+'</label>';
	
	html	+=	'<div class="row">';
	html	+=	'<div class="col-xs-'+(top.zip_ext ? '6' : '12')+'">';
	html	+=	'<input name="postal_code['+i+']" type="text" class="form-control" id="code'+i+'" onBlur="zip_vs_state_R6(this,document.getElementsByName(\'city['+i+']\'),document.getElementsByName(\'state['+i+']\'),document.getElementsByName(\'country_code['+i+']\'),document.getElementsByName(\'county['+i+']\'));" value="" maxlength="'+top.zip_length+'" />';
	html	+=	'</div>';
	
	if(top.zip_ext)
	{
		html	+=	'<div class="col-xs-6">';
		html	+=	'<input name="zip_ext['+i+']" type="text" id="zip_ext_'+i+'" value="" class="form-control" maxlength="4">';
		html	+=	'</div>';
	}
	html	+=	'</div>';
	html	+=	'</div>';
	
	//City
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="city_'+i+'">City</label><br>';
	html += '<input name="city['+i+']" type="text" id="city_'+i+'" value="" class="form-control" >';
	html	+=	'</div>';
	
	//State
	var StateLabel = top.state_label;
	StateLabel = StateLabel.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    return letter.toUpperCase();
	});
											
	html	+=	'<div class="col-xs-2">';
	html	+=	'<label for="state_'+i+'">'+StateLabel+'</label><br>';
	html += '<input name="state['+i+']" type="text" maxlength="'+top.state_length+'" id="state_'+i+'" value="" class="form-control" />';
	html	+=	'</div>';
	
	//County
	html	+=	'<div class="col-xs-6">';
	html	+=	'<label for="county_'+i+'">County</label><br>';
	html	+=	'<input name="county['+i+']" type="text" class="form-control" id="county_'+i+'" value="" />';
	html	+=	'</div>';
	
	
	//Country
	html	+=	'<div class="col-xs-4">';
	html	+=	'<label for="country_code_'+i+'">Country</label><br>';
	html	+=	'<input name="country_code['+i+']" type="text" class="form-control" id="country_code_'+i+'" value="'+top.int_country+'" >';
	html	+=	'</div>';
	
	html	+=	'<div class="clearfix mb5"></div>';
	
	html	+=	'</div>';		
	html	+=	'</div></div>';
	var cont = $('#address_grid');
	cont.append(html);
	//cont.animate({ scrollTop: cont.prop("scrollHeight")}, 1000);
}

/* 
* Function del_address
* Purpose- To Delete address grid in ALL Communication Section
* Params - 
*/
function del_address(cnt,add_id,r)
{
	if(typeof r === 'undefined'){
		top.fancyConfirm("Are you sure, you want to delete ?", '','top.fmain.del_address('+cnt+','+add_id+','+true+')',false);
		return false;	
	} 
	
	else
	{
		jId = "#div_address"+cnt;
		$(jId).remove();
		ids = $("#address_del_id").val();
		$("#address_del_id").val(ids+","+add_id);
	}
	
	
	
}

/*
* Function : add_family_info_row
* Purpose - Add New Family Info Grid 
* Params -
* 	grid_id - Last Inserted Grid ID 
* 	rows : total count 
* 	delete_msg : Confirmation Message before deletion 
* 	state_label : Label for state fields
*/

function add_family_info_row(grid_id,rows,delete_msg,state_label)
{
		var pre_cnt = rows;
		state_label = state_label || 'State';
		$("#imgRowTd"+pre_cnt).html('<br><span id="imgDeleteRow'+pre_cnt+'" class="pull-right pointer" title="Delete Family Information" onClick="delete_family_info(\''+grid_id+'\',\''+pre_cnt+'\',\''+delete_msg+'\');"><i class="glyphicon glyphicon-remove"></i></span>');
		
		rows++;
		var altClass= (rows%2 === 0) ? ' alternate' : '';
		
		var html = '';
		html += '<div id="table_family_information_' + rows + '" class="family-grid margin-top-10 pt-box '+altClass+' "  >';
		html += '<div id="family_info_name_table_' + rows + '" class="grid-box" tabindex="0">';
		// Row 1 -->
		html += '<div class="col-xs-12 ">';
		
		// Relative -->
    html += '<div class="col-xs-3" >';
		html += '<label>Relative</label>';
		html += '<br>';
		html += '<select class="form-control minimal" data-width="100%" name="family_information_relatives' + rows + '" id="family_information_relatives' + rows + '" data-tab-num="' + rows + '" title="Relative" data-header="Relative" data-container="#familySelectContainer">';
		var arrFamily = Array("","Brother","Daughter","Father","Mother","Sister","Son","Spouse","Other");
		for(var i=0; i < arrFamily.length;i++){
			opval = arrFamily[i];
			html += '<option value="'+opval+'" '+(opval ? '' : 'selected')+'>'+opval+' </option>';
		}
		html += '</select>';
		
		html 	+= '<div id="family_rel_other_box_'+rows+'" class="hidden">';
		html 	+= '<div class="input-group ">';
		html 	+= '<input type="text" class="form-control" id="family_information_relatives_other_txt'+rows+'" name="family_information_relatives_other_txt' + rows + '" value="" >';
		html 	+= '<label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackFamilyInformation' + rows + '" data-tab-name="family_information_relatives" data-tab-num="'+rows+'">';
		html 	+= '<span class="glyphicon glyphicon-arrow-left"></span>';
		html 	+= '</label>';
		html 	+= '</div>';
		html 	+= '</div>';
		
		html += '</div>';
		
		/* Title */
		html += '<div class="col-xs-3"	>';
		html += '<label>Title</label>';
		html += '<br>';
		html += '<select name="title_table_family_information' + rows + '" id="title_table_family_information' + rows + '" class="form-control minimal" data-width="100%" data-header="Title" title="Title">';
		html += '<option value="" selected> </option>';
		html += '<option value="Mr." >Mr.</option>';
		html += '<option value="Mrs.">Mrs.</option>';
		html += '<option value="Ms.">Ms.</option>';
		html += '<option value="Miss">Miss</option>';
		html += '<option value="Master">Master</option>';
		html += '<option value="Prof.">Prof.</option>';
		html += '<option value="Dr.">Dr.</option>';
		html += '</select>';
		html += '</div>';
		
		html += '<div class="col-xs-6" id="imgRowTd' + rows + '">';
		html += '</div>';
											
		html += '</div>';
		
    //Row 2
		
    html += '<div class="col-xs-12 ">';
		
		// First Name 
		html += '<div class="col-xs-4 "	>';
		html += '<label>First Name</label>';
		html += '<br>';
		html += '<input type="text" name="fname_table_family_information' + rows + '" id="fname_table_family_information' + rows + '" class="form-control" value="" />';
		html += '</div>';
		
		// Middle Name 
		html += '<div class="col-xs-4"	>';
		html += '<label>Middle Name</label>';
		html += '<br>';
		html += '<input type="text" name="mname_table_family_information' + rows + '" id="mname_table_family_information' + rows + '" class="form-control" value="" />';
		html += '</div>';
                      
    // Last Name 
		html += '<div class="col-xs-4"	>';
		html += '<label>Last Name</label>';
		html += '<br>';
		html += '<input type="text" name="lname_table_family_information' + rows + '" id="lname_table_family_information' + rows + '" class="form-control" value="" data-action="search_patient" data-grid="' + rows + '" data-fld="Active"  />';
		html += '</div>';
		
		html += '</div>';
			
    // Row 3  
		html += '<div class="col-xs-12 ">';
		
    // Suffix 
		html += '<div class="col-xs-2 "	>';
		html += '<label>Suffix</label>';
		html += '<br>';
		html += '<input type="text" name="suffix_table_family_information' + rows + '" id="suffix_table_family_information' + rows + '" class="form-control" value="" />';
		html += '</div>';
		
		// Relaese HIPAA Info 
		html += '<div class="col-xs-5 form-inline"	>';
		html += '<br>';
		html += '<div class="checkbox">';
		html += '<input type="checkbox" class="form-control" id="chkHippaFamilyInformation_' + rows + '" name="chkHippaFamilyInformation_' + rows + '" value="1" >';
		html += '<label for="chkHippaFamilyInformation_'+ rows +'"><span class="text-red">Relase HIPAA Info</span></label>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		
		// Row 4 
		html += '<div class="col-xs-12 ">';
		
		// Street1 
		html += '<div class="col-xs-5"	>';
		html += '<label>Street 1</label>';
		html += '<br>';
		html += '<input name="street1_table_family_information' + rows + '" id="street1_table_family_information' + rows + '"  type="text" class="form-control" value="" />';
		html += '</div>';
		
		// Street2 
		html += '<div class="col-xs-4"	>';
		html += '<label>Street 2</label>';
		html += '<br>';
		html += '<input name="street2_table_family_information' + rows + '" id="street2_table_family_information' + rows + '" type="text" class="form-control" value="" />';
		html += '</div>';
		
		var col 	= (top.zip_ext)	?	7 : 12;
		var col2	=	(top.zip_ext)	?	4 : 0;	
		
		// Zip Code 
		html += '<div class="col-xs-3"	>';
		html += '<label>'+top.zipLabel+'</label>';
		html += '<br>';
		html += '<div class="col-xs-12">';
		html += '<div class="row">';
		
		html += '<div class="col-xs-'+col+'" >';
		
		html += '<input name="postal_code_table_family_information' + rows + '" type="text" class="form-control" id="code_table_family_information' + rows + '" onChange="zip_vs_state_family_state(this.value,\'' + rows + '\'); " value=""  maxlength="'+top.zip_length+'" size="'+top.zip_length+'" >';
		html += '</div>';
		
		if(top.zip_ext)
		{
     	html += '<div class="col-xs-1 text-center padding_0"><b>-</b></div>';
			html += '<div class="col-xs-'+col2+'">';
			html += '<input name="zip_ext_table_family_information' + rows + '" type="text" class="form-control" id="zip_ext_table_family_information' + rows + '" value="" maxlength="4">';
			html += '</div>';
		}
		
		html += '</div>';
		
		html += '</div>';
		
		html += '</div>';
		
		html += '</div>';
		
    // Row 5 
    html += '<div class="col-xs-12 ">';
		
		// City 
		html += '<div class="col-xs-3"	>';
		html += '<label>City</label>';
		html += '<br>';
		html += '<input name="city_table_family_information' + rows + '" type="text" class="form-control" id="city_table_family_information' + rows + '" value="" />';
		html += '</div>';
		
		// State 
		html += '<div class="col-xs-2"	>';
		html += '<label>'+state_label+'</label>';
		html += '<br>';
		html += '<input name="state_table_family_information' + rows + '" type="text" maxlength="2" class="form-control" id="state_table_family_information' + rows + '" value="" />';
		html += '</div>';
		
		// Email ID 
		html += '<div class="col-xs-7"	>';
		html += '<label>Email-Id</label>';
		html += '<br>';
		html += '<input name="email_table_family_information' + rows + '" id="email_table_family_information' + rows + '" type="text" class="form-control" value="">';
		html += '</div>';
		
		html += '</div>';
		
		
    // Row 6 
    html += '<div class="col-xs-12 ">';
		
		// Home Phone 
		html += '<div class="col-xs-4" >';
		html += '<label>Home Phone '+top.hashOrNo+'</label>';
		html += '<br>';
		html += '<input name="phone_home_table_family_information' + rows + '" id="phone_home_table_family_information' + rows + '" type="text" class="form-control" maxlength="'+top.phone_min_length+'" value="" />';
		html += '</div>';
		
		
    // Work Phone 
		html += '<div class="col-xs-4" >';
		html += '<label>Work Phone '+top.hashOrNo+'</label>';
		html += '<br>';
		html += '<input name="phone_work_table_family_information' + rows + '" id="phone_work_table_family_information' + rows + '" type="text" class="form-control" maxlength="'+top.phone_min_length+'" value="" />';
		html += '</div>';
		
		// Mobile Phone 
		
		html += '<div class="col-xs-4" >';
		html += '<label>Mobile Phone '+top.hashOrNo+'</label>';
		html += '<br>';
		html += '<input name="phone_cell_table_family_information' + rows + '" id="phone_cell_table_family_information' + rows + '" type="text"  class="form-control" maxlength="'+top.phone_min_length+'" value="" />';
		html += '</div>';
		
                    
    html += '</div>';
		
          		
   	html += '</div>';
		html += '</div>';
		
		// End -->
		
		// Add More Button 
		var AddMoreButton	=	'';
		AddMoreButton	=	'<span id="imgAddNewRow'+rows+'" title="Add More" onClick="add_family_info_row(\'\',\''+rows+'\',\''+delete_msg+'\',\''+state_label+'\');" class="pull-right pointer"><i class="glyphicon glyphicon-plus"></i></span>';
		
		var tbl = $("#patient_family_table");
		tbl.last().append(html);
		dgi("last_family_inf_cnt").value = rows;
		//$("select.selectpicker").selectpicker('refresh');
		$("#ImageAddRow").html(AddMoreButton);
		tbl.animate({ scrollTop: tbl.prop("scrollHeight")}, 1000);
		/*--updating jquery table highliting for newly generated data--*/
		/*$('.div_table').click(function(){
			$('.div_table').removeClass('bg3');
			$(this).addClass('bg3');
		});*/
	}

/*
* Function : delete_family_info
* Purpose - To Delete Family Info Grid
* Params - 
* 	family_info_id : Id With which information saved in DB
* 	row_id : Grid Index Generated while create new
* 	confirmMsg : Message text Before Deletion
*/
function delete_family_info(family_info_id,row_id,confirmMsg)
{
		if(family_info_id){
			var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/delete_family_info.php?id="+family_info_id;
			top.fancyConfirm(confirmMsg,"", "window.top.show_loading_image('show');master_ajax_tunnel('"+url+"',top.fmain.handle_ajax_response,'','json');","window.top.show_loading_image('hide');")
		}
		else{
			$("#table_family_information_"+row_id).fadeOut('fast');
		}
}

/*
* Function : handle_ajax_response
* Purpose - To handle response send by Ajax tunnel method
* 					Called From delete_family_info ^| METHOD			
* Params - r : holds JSON ENCODED information
*/

function handle_ajax_response(r)
{
	if(r.success == true)
	{
		var m = vocabulary[r.msg_key]; 
		top.fmain.location.reload(true);
		top.alert_notification_show(m);
	}
	return;
}

/*
* Function : set_release_information
* Purpose - To Set Name, Phone and Relationship info in 
*						Release Grid While checked Release Info	Checkbox 
*						in Responsible Part and Family Information Grid	
* Params - num : holds row no to set values in input/select
*/

function set_release_information(num)
{
	/*
		when you checked the "Release Hipaa information "checkbox"in Family Information then name, home phone and relation of patient
		goes to the "Reminder Choices"
	*/
	
	var name_id_array = new Array("relInfoName1", "relInfoName2", "relInfoName3", "relInfoName4");
	var phone_id_array = new Array("relInfoPhone1", "relInfoPhone2", "relInfoPhone3", "relInfoPhone4");
	var rel_id_array = new Array("relInfoReletion1", "relInfoReletion2", "relInfoReletion3", "relInfoReletion4");
	var rel_other_id_array = new Array("otherRelInfoReletion1", "otherRelInfoReletion2", "otherRelInfoReletion3", "otherRelInfoReletion4");
		
	if(num == "resp"){
		if(document.getElementById("chkHippaRelResp").checked == true){
			var family_fname_object = document.getElementById("fname1");
			var family_lname_object = document.getElementById("lname1");
			var family_phone_object = document.getElementById("phone_home1");
			var family_relation_ship_object = document.getElementById("relation1");
			var family_relation_ship_other_object = document.getElementById("oth");
			var name = '';
			if(family_lname_object.value){
				name = family_lname_object.value;			
			}
			if(family_fname_object.value){
				name += ", "+family_fname_object.value;			
			}

			var phone = family_phone_object.value;
			var relationship = family_relation_ship_object.value;
			var relationship_other = family_relation_ship_other_object.value;
			
			if(name !== "" && name !== 'undefined')
			{
				var blHaveInGrid = false;
				for(var a=0;a<4;a++){
					txtName = document.getElementById(name_id_array[a]).value;
					if(txtName.toLowerCase() == name.toLowerCase() ){
						document.getElementById(phone_id_array[a]).value = phone;
						document.getElementById(rel_id_array[a]).value = relationship;
						document.getElementById(rel_other_id_array[a]).value = relationship_other;
						$("#"+rel_id_array[a]).trigger('change');//.selectpicker('refresh');	
						blHaveInGrid = true;	
						a = 4;
						
					}
				}
				if(blHaveInGrid == false)
				{
					for(var a=0;a<4;a++){
						if(document.getElementById(name_id_array[a]).value == ""){
							document.getElementById(name_id_array[a]).value = name;
							document.getElementById(phone_id_array[a]).value = phone;
							document.getElementById(rel_id_array[a]).value = relationship;
							document.getElementById(rel_other_id_array[a]).value = relationship_other;
							$("#"+rel_id_array[a]).trigger('change');//.selectpicker('refresh');	
							
							a = 4;
						}
					}						
				}
				
			}
		}
		else if(document.getElementById("chkHippaRelResp").checked == false){
			var family_fname_object = document.getElementById("fname1");
			var family_lname_object = document.getElementById("lname1");
			var family_phone_object = document.getElementById("phone_home1");
			var family_relation_ship_object = document.getElementById("relation1");
			var family_relation_ship_other_object = document.getElementById("oth");
			
			var name = '';
			if(family_lname_object.value){
				name = family_lname_object.value;			
			}
			if(family_fname_object.value){
				name += ", "+family_fname_object.value;			
			}

			var phone = family_phone_object.value;
			var relationship = family_relation_ship_object.value;
			var relationship_other = family_relation_ship_other_object.value;
			for(var a=0;a<4;a++){
				
				txtName = document.getElementById(name_id_array[a]).value;
				if(txtName.toLowerCase() == name.toLowerCase()){
					document.getElementById(name_id_array[a]).value = '';
					document.getElementById(phone_id_array[a]).value = '';
					document.getElementById(rel_id_array[a]).value = '';				
					document.getElementById(rel_other_id_array[a]).value = '';
					$("#"+rel_id_array[a]).trigger('change');	//.selectpicker('refresh')
					a = 4;
					
				}
			}				
		}		
	
	}
	else{
		num = parseInt(num);
		i = num;
		if(num > 4) { return false; }
		
		if(document.getElementById("chkHippaFamilyInformation_"+num).checked){
			var family_fname_object = document.getElementById("fname_table_family_information"+num);
			var family_lname_object = document.getElementById("lname_table_family_information"+num);
			var family_phone_object = document.getElementById("phone_home_table_family_information"+num);
			var family_relation_ship_object = document.getElementById("family_information_relatives"+num);
			var family_relation_ship_other_object = document.getElementById("family_information_relatives_other_txt"+num);
			var name = '';
			if(family_lname_object.value){
				name = family_lname_object.value;			
			}
			if(family_fname_object.value){
				name += ", "+family_fname_object.value;			
			}

			var phone = family_phone_object.value;
			var relationship = family_relation_ship_object.value;
			var relationship_other = family_relation_ship_other_object.value;
			
			if(name != "" && name != 'undefined')
			{
				var blHaveInGrid = false;
				for(var a=0;a<4;a++){
					if(document.getElementById(name_id_array[a]).value == name && document.getElementById(phone_id_array[a]).value == phone && document.getElementById(rel_id_array[a]).value == relationship){
						blHaveInGrid = true;	
						a = 4;
					}
				}
				if(blHaveInGrid == false){
					document.getElementById(name_id_array[i]).value = name;
					document.getElementById(phone_id_array[i]).value = phone;
					document.getElementById(rel_id_array[i]).value = relationship;				
					document.getElementById(rel_other_id_array[i]).value = relationship_other;
					$("#"+rel_id_array[i]).trigger('change');//.selectpicker('refresh');	
				}
				
			}
		}//end of main if
		else if(!document.getElementById("chkHippaFamilyInformation_"+num).checked){
			document.getElementById(name_id_array[i]).value = '';
			document.getElementById(phone_id_array[i]).value = '';
			document.getElementById(rel_id_array[i]).value = '';				
			document.getElementById(rel_other_id_array[i]).value = '';
			$("#"+rel_id_array[i]).trigger('change');//.selectpicker('refresh');	
		}//end of main else.
	}

}
	
/*
* Function : collect_source
* Purpose - To collect typehead option for selected option
* Params - _key : holds key info to for which typehead to collect
*/	
function collect_source(_key) 
{
	if(_key !== '') {
		type_head_source = suggestions_ha[_key];
	} else { type_head_source = []; }
}

/*
* Function : switch_advisory_class
* Purpose - To set field as advisory if fields is empty
* Params - o: holds this object for which event has occured
*/
function switch_advisory_class(o)
{
	var c = 'advisory';
	var m = $(o); if(!m){ return;} var n = m.attr('name'); var v = m.val();
	if(v && m.hasClass('advisory-chk')) { m.removeClass(c); } else { m.addClass(c);}
}

/*
* Function : switch_advisory_class_s 
* Purpose - To set field as advisory if not selected
*						specialy for select with selectpicker class							
* Params -	o: holds this object for which event has occured
*/
function switch_advisory_class_s(o)
{
	var c = 'advisory';
	var m = $(o); if(!m){ return;} var n = m.attr('name'); var v = m.val();
	if(v && m.hasClass('advisory-chk')) { m.selectpicker('setStyle', c, 'remove'); }
	else { m.selectpicker('setStyle', c, 'add'); }
}

/*
* Function : switch_mandatory_class
* Purpose - To set field as mandatory if fields is empty
* Params - o: holds this object for which event has occured
*/
function switch_mandatory_class(o)
{
	var c = 'mandatory';
	var m = $(o); if(!m){ return;} var n = m.attr('name'); var v = m.val();
	if(v && m.hasClass('mandatory-chk')) { m.removeClass(c); } else { m.addClass(c);}
}

/*
* Function : switch_mandatory_class 
* Purpose - To set field as mandatory if not selected
*						specialy for select with selectpicker class							
* Params -	o: holds this object for which event has occured
*/
function switch_mandatory_class_s(o)
{
	var c = 'mandatory';
	var m = $(o); if(!m){ return;} var n = m.attr('name'); var v = m.val();
	if(v && m.hasClass('mandatory-chk')) { m.selectpicker('setStyle', c, 'remove'); }
	else { m.selectpicker('setStyle', c, 'add'); }
}

/*
* Function : swap_combo_other 
* Purpose - To Change Appearance of fields If Option is selected as 'Others'
* Params -	
* showIDArr - Holds Array of HTML Tags ID which will appear
* hideIDArr - Holds Array of HTML Tags ID which will hidden
* comboObj	- Holds this object of Tag from which event occured
*/
function swap_combo_other(showIDArr,hideIDArr,comboObj)
{
	if((typeof(comboObj) != 'object') || (comboObj.value == 'Other') || (comboObj.value == 'Others')){
		if(hideIDArr){ 
			$(hideIDArr).each(function(){
				if($('#'+this).hasClass('selectpicker')) $('#'+this).selectpicker('hide'); 
				else $('#'+this).removeClass('inline').addClass('hidden'); 	
			});
		} 
		$(showIDArr).each(function(){ 
			if($('#'+this).hasClass('selectpicker')) 
				$('#'+this).val('').selectpicker('show').selectpicker('refresh').trigger('change'); 
			else $('#'+this).removeClass('hidden').addClass('inline'); 
		});
	}
}


/*
* Function : str_exists 
* Purpose - To check if substring exists in given string
* Params -	
* str - Holds string from which search
* search_string - Holds string to search
*/
function str_exists(str,search_string)
{
	return (str.indexOf(search_string) === -1 ) ? false : true;
}

function get_operator_name_date()
{
	$('#chkNotesScheduler, #chkNotesChartNotes, #chkNotesAccounting, #chkNotesOptical').prop('checked',true);
	var t = current_date(top.jquery_date_format,2) + ' ' + operator + ': \n' + $("#patient_notes").val();
	$("#patient_notes").val(t);
	set_caret_position("patient_notes", 13);
}

function remove_operator_name_date()
{
	var str_text = $("#patient_notes").val();
	var match_string = current_date(top.jquery_date_format,2) + ' ' + operator + ': \n';
	if(str_text == match_string )	$("#patient_notes").val('');
	
	if(str_text != "" && str_text != match_string) 
		$("#chkNotesScheduler").prop('checked',true);
	else
		$("#chkNotesScheduler").prop('checked',false);
		
}
			
/*
* Function : set_heard_type 
* Purpose - To set typehead in textarea according to selected 
*						value in HEARD ABOUT US Field	
* Params -	obj: this object of HEARD ABOUT US Field	
*/
function set_heard_type(obj)
{
	var orignalVal 	= obj.val();	
	var arrOrignalVal = orignalVal.split("-");
	
	collect_source('');
	
	if(arrOrignalVal.length > 1){
		var val = arrOrignalVal[1];
		val = val.replace(/[0-9]/,'num');
		if(val !== 'Dr.')
		{
			repObj = val.replace(/\s/g,'_');
			try{
				collect_source(repObj);
			}catch(e){
				collect_source('');
			}
		}
	}
	
}

/*
* Function : set_patient_ac_status 
* Purpose - 
* Params -
*	$_this - Holds this object from which event Occured 
*/
var other_val ='';
function set_patient_ac_status($_this)
{
	var set_status = 0; 
	var src = $_this.data('source'); var a = $_this.data('action');
	var v = $_this.val();
	
	if(src == 'btn')
	{
		if($('#other_status').val() == ''){
			top.fAlert("Please fill other status value in text field.");
			return false;
		}else{
			set_status = 1;
		}
	}
	else
	{
		
		if(v == 'other') {
			$_this.addClass('hidden').removeClass('inline');//selectpicker('hide');
			$('#otherStsDiv').removeClass('hidden').addClass('inline');
		} else {
			set_status=1;
			$_this.addClass('inline').removeClass('hidden');//selectpicker('show');
			$('#other_status').val('');
			$('#otherStsDiv').removeClass('inline').addClass('hidden');
		}
	}
	
	if(set_status == 1)
	{
		other_val = $('#other_status').val();
		var old_status = $('#oldStatus').val();
		var selected_text=$("#account_status option:selected").text();
		var url = top.JS_WEB_ROOT_PATH+'/interface/accounting/setPatAccountStatus.php?';
		var d = 'acId=' + v + '&otherVal=' + other_val + "&selectedText=" + selected_text + "&oldStatus=" + old_status
		url = url + d;
		top.master_ajax_tunnel(url,top.fmain.patient_ac_status_handler);
        
        //Assign task according to rule manager
        top.check_rule_manager(v, 'pt_account_status');
	}
}

/*
* Function : patient_ac_status_handler 
* Purpose - Handle Ajax Response After Setting PAtient Account Status
* Params -
*	r - Holds return date from ajax call
*/
function patient_ac_status_handler(r)
{
	var retVals= r.split('~~~');
	if(retVals[0]==1)
	{
		top.alert_notification_show("Patient Account Status is saved successfully.");
	}
	if(other_val!='')
	{
		var selected_text = $('#other_status').val();
		$('#other_status').val('');
		$('#otherStsDiv').removeClass('inline').addClass('hidden');
		$('#account_status').addClass('inline').removeClass('hidden').html(retVals[1]);// .selectpicker('show') .selectpicker('refresh');
	}
	var is_active = 'no';
	if(selected_text)
	{
		if(selected_text.toLowerCase() == 'active'){ is_active='yes'; } 
	}
	//top.changePatNameColor(is_active);
	top.update_iconbar();
}
/*
* Function : xhr_ajax_delete 
* Purpose - To handle Delete request using Ajax
*						This function will use all data attributes
*						to send params in ajax request 	
* Params -	
* $_this - Holds this object from which event Occured 
*/
function xhr_ajax_delete($_this)
{ 
	var p = $_this.data();
	var d = '';
	$.each(p,function(i,v){ d	+= '&' + i + '=' + v; });
	d = d.substr(1);
	var c_msg = "Are you sure, you want to delete ?";
	var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/ajax_save_update.php?"+d;
	top.fancyConfirm(c_msg,"", "window.top.show_loading_image('show');master_ajax_tunnel('"+url+"',top.fmain.xhr_ajax_delete_handler,'','json');","window.top.show_loading_image('hide');")
}

/*
* Function : xhr_ajax_delete_handler 
* Purpose - Handler/CallBack Method of xhr_ajax_delete METHOD
*						It handles the response returned by ajax request
* Params -	
* $_this - Holds this object from which event Occured 
*/

function xhr_ajax_delete_handler(r)
{
	if(r.success)
	{
		if(r.action == 'delete_resp_party')
		{
			$('#resp_container').find('input[type="text"],input[type="hidden"]').val('');
			$('#resp_container').find('input[type="checkbox"]').prop('checked',false);
			$('#resp_container').find('select').val('');//.selectpicker('refresh');
			$('#btn_del_resp_party,#viewText,#viewTexticon,#btn_del_rli,#btn_view_rli').hide();
		}
		else if(r.action == 'delete_resp_license')
		{
			$('#resp_container').find('#btn_del_rli,#btn_view_rli').remove();
		}
		top.alert_notification_show(r.msg);
	}
	else
	{
		top.fAlert("Unable to delete record");
	}	
	
	return;
}


/*
* Function : show_log 
* Purpose - to view Patient Access Log, login hostory
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function show_log($_this)
{
	xhr_ajax('',$_this);
}

/*
* Function : delete_call_timming 
* Purpose - to Reset Reminder Choice Values
* Params -	
* num - Holds row number in reminder choice 
*				box to reset the value
*/
function delete_call_timming(num)
{
	if(num>0){
		$('#hourFrom'+num+', #hourTo'+num).val('');
		$('#minFrom'+num+', #minTo'+num).val('00');
	}
}
	
/*
* Function : generate_activation_key 
* Purpose - Generate Activation Key in Patient Portal
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function generate_activation_key($_this)
{
	var d	=	$_this.data();
	var user_pass	=	$("#user_password").val();
	$_this.data('user-pass',user_pass);
	if(d.tempKeyChk == 1 || d.respUserName)
	{
		var c_msg	=	'Reset Temporary Key, will reduce count from MU Report until this key is given to Patient. Are you sure?';
		top.fancyConfirm(c_msg,"", "top.fmain.gen_temp_key('"+d.tempKeySize+"','1');","");
	}
	else{
		gen_temp_key(d.tempKeySize,'0'); 
	}
	return;
}

function gen_temp_key(tempKeySize,regen_key)
{
	$("#usernm,#pass1,#pass2").val('');
	var obj = $(".activation-key");
	obj.data('regen-key',regen_key); 
	xhr_ajax('',obj);
}

/*
* Function : collect_changes 
* Purpose - Combined function for all input/Select/Textarea tags 
*						If any change occured in any field
* Params -	
* $_this - Holds this object from which event Occured 
*/	
function collect_changes($_this)
{
	_this = $_this[0];
	var p_val = $_this.attr('data-prev-val');
	var c_val = $_this.val();
	top.chk_change_in_form(p_val,_this,'DemoTabDb',event);
	chk_change(p_val,c_val);
}

function add_lang_code(code)
{
	if( code ) $("#lang_code").val(code);
}

/*
* Section Below is used for on load event as below:
* To bind Event with HTML Fields
* To trigger default event of HTML Fields
* To Attach Datepicker/Selectpicker Scripts
*/
$(function(){
	var noChange = ['scanner','created_by_name','reg_date','temp_key','usernm','fakeField'];
	$('body').on('keyup','#heardAbtDesc',function(event){
			_this =	$(this)[0]; $_this = $(this);
			var old_val = $("#elem_heardAbtUs").attr('data-desc');
			collect_changes($(this));
	});
	$('[data-toggle="popover"]').popover();
	//.pt-box input, .pt-box select, .pt-box textarea, .pt-box button[type=button], 
	$("body").on("focus", ".pt-box .grid-box", function(){
		$(this).closest('.pt-box').css({'background-color':'#ffffcc'});
	});
	
	$("body").on("blur", ".pt-box .grid-box", function(){
		$(".pt-box").css({'background':'transparent'})
	});
	
	$('body').on('keyup change keyupSwitch','input.mandatory-chk',function(e){ switch_mandatory_class($(this)[0]); });
	$('body').on('keyup change keyupSwitch','input.advisory-chk',function(e){ switch_advisory_class($(this)[0]); });
	
	$('body').on('change','select.mandatory-chk',function(e){
		if($(this).hasClass('selectpicker'))
			switch_mandatory_class_s($(this)[0]);
		else
			switch_mandatory_class($(this)[0]);
	});
	$('body').on('change','select.advisory-chk',function(e){
		if($(this).hasClass('selectpicker'))
			switch_advisory_class_s($(this)[0]);
		else
			switch_advisory_class($(this)[0]);
	});
	
	$('body').on('keyup blur','input[type="text"]',function(event){ if( $.inArray($(this).attr('id'),noChange) >=0 ){ return false; } collect_changes($(this));  });
	
	$("body").on('focusin','input[type="text"],textarea',function(event){ if( $.inArray($(this).attr('id'),noChange) >= 0){ return false; } get_focus_obj($(this)[0]); });
	
	$('body').on('change blur','#fname, #fname1, #mname, #mname1,  #lname, #lname1, #nick_name, #suffix, #suffix1, #birth_name, #maiden_fname, #maiden_mname, #maiden_lname, #contact_relationship, input[id^="street"], input[id^="street2"], #interpretter, #ename, #occupation, #estreet, input[id^="fname_table_family_information"], input[id^="mname_table_family_information"], input[id^="lname_table_family_information"], input[id^="street1_table_family_information"], input[id^="street2_table_family_information"] ',function(event){ 
		//console.log($(this).attr('id'));
		var v = $(this).val();
		var c = capitalize_letter(v);
		$(this).val(c);
	});
	
	$("body").on('keyup','#heardAbtSearch',function(){
		$("#heardAbtSearchId").val('');
	})
	$('body').on('change','#phone_home,#phone_biz, #phone_cell, input[id^="relInfoPhone"], #phone_home1, #phone_biz1, #phone_cell1, input[id^="phone_home_table_family_information"],input[id^="phone_work_table_family_information"],input[id^="phone_cell_table_family_information"]',function(event){ var c = 'form-control'; if($(this).hasClass('mandatory-chk')) { c = c + ' mandatory-chk mandatory'; } if($(this).hasClass('advisory-chk')) { c = c + ' advisory-chk advisory'; } if( $(this).val() ) { set_phone_format($(this)[0],phone_format,'','',c);} });
	
	$('body').on('paste','#phone_home,#phone_biz, #phone_cell, input[id^="relInfoPhone"], #phone_home1, #phone_biz1, #phone_cell1, input[id^="phone_home_table_family_information"],input[id^="phone_work_table_family_information"],input[id^="phone_cell_table_family_information"]',function(event){ 
		var pasteData = '';
		if( typeof event.originalEvent.clipboardData !== 'undefined' )
			pasteData = event.originalEvent.clipboardData.getData('text');
		else 
			pasteData = window.clipboardData.getData('text');
			
		pasteData = pasteData.replace(new RegExp('-','g'), '');
		$(this).val(pasteData).trigger('change');
	});
	
	$('body').on('blur change','#dob1',function(event){ 
		top.checkdate($(this)[0]);
		do_date_check($("#from_date_byram1")[0],$(this)[0]); 
	});
	
	$('body').on('change','#ss,#ss1',function(event){ validate_ssn($(this)[0]); });
	
	$('body').on('keyup','#code,#rcode,input[id^="code_table_family_information"]',function(event){ validate_zip($(this)[0]); });
	
	$('body').on('keyup','#state,#estate,#rstate,input[id^="state_table_family_information"]',function(event){ 
		checkIfAlphabet(event,$(this).attr('id')); 
	});
	
	$('body').on('keypress','input[type="text"]',function(event){ save_data(event) });
	
	$('body').on('change','input[type="checkbox"],#phone_home1',function(event){ 
		collect_changes($(this));	
		var n = $(this).attr('id');
		if(n === 'chkHippaRelResp' || n === 'phone_home1') { set_release_information('resp'); }
		else if(n === 'hipaa_voice') { display_hide_timmings(); }
		else if(str_exists(n,'chkHippaFamilyInformation')) { var s = n.split('_'); set_release_information(s[1]);  }
	});
	
	$('body').on('change','select',function(){
		_this = $(this)[0]; $_this= $(this);
		var n = $_this.attr('name');
		if(n)
		{
			if(n === 'relation1') { swap_combo_other(Array('relation1_oth'),Array('relation1'),_this); }
			else if(n === 'ado_option') { swap_combo_other(Array('ado_other_box'),Array('ado_option'),_this); }
			else if(n === 'emerRelation') { swap_combo_other(Array('relation_other_box'),Array('emerRelation'),_this); }
			else if(n === 'language') { swap_combo_other(Array('otherLanguageBox'),Array('language'),_this); add_lang_code($_this.find('option:selected').data('code'))  }
			else if(n === 'sexual_orientation') { swap_combo_other(Array('otherSORBox'),Array('sexual_orientation'),_this);  }
			else if(n === 'gender_identity') { swap_combo_other(Array('otherGIBox'),Array('gender_identity'),_this);  }
			else if(str_exists(n,'family_information_relatives')) { var t = $_this.attr('data-tab-num'); swap_combo_other(Array('family_rel_other_box_'+t),Array(n),_this); }
			else if(str_exists(n,'relInfoReletion')) { var t = $_this.attr('data-tab-num'); swap_combo_other(Array('otherRelInfoBox'+t),Array(n),_this); }
			else if( n === 'elem_heardAbtUs')
			{
				swap_combo_other(Array('otherHeardAboutBox'),Array('elem_heardAbtUs'),_this);
				var heardAbtVal = $("#elem_heardAbtUs").val();
				
				if(heardAbtVal !== '') {
					
					if( heardAbtVal !== 'Other' ) {
						var tmpArr = heardAbtVal.split("-");
						heardAbtVal = tmpArr[1].trim();
					}
					
					if($.inArray(heardAbtVal,heardAboutSearch ) !== -1 ) {
						if( heardAbtVal == 'Doctor') {
								$("#heardAbtSearch").attr('onkeyup',"top.loadPhysicians(this,'heardAbtSearchId')")
																		.attr('onfocus',"top.loadPhysicians(this,'heardAbtSearchId')")
																		.removeAttr('onKeydown');				
						}
						else {
							$("#heardAbtSearch").removeAttr('onkeyup onkeyup')
																	.attr('onKeydown','if( event.keyCode == 13) { searchHeardAbout(); }');	
						}
						$("#tdHeardAboutSearch").removeClass('hidden').addClass('inline');
						$("#heardAbtDesc").removeClass('inline').addClass('hidden');
					}
					else {
						$("#heardAbtDesc").removeClass('hidden').addClass('inline');
						$("#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
						set_heard_type($_this);
						$('#heardAbtDesc').typeahead({source:type_head_source});
					}
				}
				else {
					$("#heardAbtDesc,#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
				}
				
			}
			else if(n === 'elem_patientStatus')
			{
				var v = $_this.val();
				var s_arr = h_arr = [];
				if(v === 'Transferred' || v === 'Moved' || v === 'Other' )
				{
					s_arr = Array('tdOtherPatientStatus'); h_arr	=	Array('dod_patient_td');
				}
				else if(v === "Deceased")
				{
					s_arr = Array('dod_patient_td'); h_arr	=	Array('tdOtherPatientStatus');	
				}
				else
				{
					h_arr	=	Array('dod_patient_td','tdOtherPatientStatus');
				}
				swap_combo_other(s_arr,h_arr);		
			}
			collect_changes($(this));
		}
	});
	
	$('body').on('click','.back_other',function(){
		_this = $(this)[0]; $_this= $(this);
		var n = $_this.attr('data-tab-name'); 
		if(n === 'relation1') { swap_combo_other(Array('relation1'),Array('relation1_oth')); }
		else if(n === 'ado_option') { swap_combo_other(Array('ado_option'),Array('ado_other_box')); }
		else if(n === 'emerRelation') { swap_combo_other(Array('emerRelation'), Array('relation_other_box')); }
		else if(n === 'language') { swap_combo_other(Array('language'),Array('otherLanguageBox')); }
		else if(n === 'sexual_orientation') { swap_combo_other(Array('sexual_orientation'),Array('otherSORBox')); }
		else if(n === 'gender_identity') { swap_combo_other(Array('gender_identity'),Array('otherGIBox')); }
		else if(n === 'family_information_relatives') { var t = $_this.attr('data-tab-num'); swap_combo_other(Array(n+t),Array('family_rel_other_box_'+t)); }
		else if(n === 'relInfoReletion') { var t = $_this.attr('data-tab-num'); swap_combo_other(Array(n+t),Array('otherRelInfoBox'+t)); }
		else if( n === 'elem_heardAbtUs')
		{ 
			swap_combo_other(Array('elem_heardAbtUs'),Array('otherHeardAboutBox'));	
			$("#heardAbtDesc,#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
			set_heard_type($_this);
			$('#heardAbtDesc').typeahead({source:type_head_source});	
		}
		else if( n === 'account_status')
		{ 
			swap_combo_other(Array('account_status'),Array('otherStsDiv'));	
		}
		
		
	});
	
	$("body").on('click','#btn_del_rli,#btn_del_resp_party',function(){ xhr_ajax_delete($(this)); });
	
	$("body").on('click','#add_new_address',function(e){ add_new_address(); });
	
	$("body").on('blur','#lname1,input[id^="lname_table_family_information"]',function(e){ $('#sp_ajax').val($(this).val()); search_patient($(this)); });
	
	$("body").on('click','#sp_ajax_btn',function(e){ search_patient($('#sp_ajax')); });
	
	$("body").on('keyup','#sp_ajax',function(e){ if(e.keyCode == 13 ) { search_patient($('#sp_ajax')); } });
	
	$("body").on('click keyup','.search_physician',function(event){ 
		if( $(this).attr('id') == 'phy_ajax' && event.keyCode !== 13) {
			return false;
		}
		var d = $(this).data('source'); var o = $("#"+d); search_physician(o); 
	});
	
	$("body").on('click','a[data-click="pick_physician"]',function(e){ 
		var d = $(this).data(); $("#"+d.idBox).val(d.refId); $("#"+d.textBox).val(d.name);$("#"+d.textBox).removeClass('red-font');
		$("#phy_ajax").val('');
		$("#search_physician_result .modal-body").html('<div class="loader"></div>');
		$("#search_physician_result").modal('hide');
	});
	
	$("body").on('focus','#patient_notes',function(e){ get_operator_name_date(); });
	
	$("body").on('blur','#patient_notes',function(e){ remove_operator_name_date(); });
	
	$("body").on('click','[data-grid="family"], [data-grid="resp"]',function(e){
			var i = $(this).attr('data-row'); var t = $(this).attr('data-grid'); fill_grid_info(i,t);
	});
	
	$("body").on('click','.show_log',function(){ show_log($(this)); });
	
	$("body").on('click','.activation-key',function(){ generate_activation_key($(this)); });
	
	$("body").on('click','#done_btn_pt_override',function(){ gen_temp_key($(this).attr('data-temp-key-size'),'1'); });
	
	$("body").on('click','#demographics_hx',function(){ xhr_ajax('',$(this),false,'demographics_history'); });
	
	$("body").on('change','#account_status',function(){ set_patient_ac_status($(this)); });
	
	$("body").on('click','#btnSetStatus',function(){ set_patient_ac_status($(this)); });
	
	$("body").on('click','.physician_del',function(){  });
	
	$("body").on('click','.physician_add',function(){ });
	
	$("body").on('keypress','#elem_physicianName,#primaryCarePhy,#co_man_phy',function(e){ make_id_empty($(this).data('id-box')); });
	
	var _selectors = document.querySelectorAll('#elem_physicianName,#primaryCarePhy,#co_man_phy');
	for(var i = 0; i < _selectors.length; i++) {
			_selectors[i].addEventListener('keyup', function(event){
				var _this = $(this)[0];
				if( _this.hasAttribute('data-content') ) {
					if( _this.value == '' || _this.getAttribute('data-prev-val') !== _this.value ) {
						_this.setAttribute('data-content','');
						$(this).popover('destroy');
					}
					else {
						$(this).popover('hide');
					}
				}
			});
	}

	$("body").on('blur','#elem_physicianName,#primaryCarePhy,#co_man_phy',function(e){ refine_data($(this)[0]); });
	
	// Binding Date Picker with fields  
	setTimeout(function(){$('#dod_patient, #dob, #dob1').datetimepicker({timepicker:false,format:top.jquery_date_format,maxDate:new Date(),autoclose: true, scrollInput:false,onChangeDateTime:function(r,$input){ if($input[0].id == "dob"){get_age($input[0].defaultValue,$input[0].id,'patient_age','patient_age_month');}}});}, 200);
	// Binding Select Picker to select Tags having class selectpicker
	$('.selectpicker').selectpicker();
	// Add Mandatory Class to fields 
	$.each(mandatory_fld,function(i,v){ 
		$('#'+v).addClass('mandatory-chk');
		if(v == 'language') { $('#otherLanguage').addClass('mandatory-chk'); }
		else if(v == 'email') { $('#ptDemoEmail').addClass('mandatory-chk'); }
	});
		// Add Advisory Class to fields 
		$.each(advisory_fld,function(i,v){ 
			$('#'+v).addClass('advisory-chk');
			if(v == 'language') { $('#otherLanguage').addClass('advisory-chk'); }
			else if(v == 'email') { $('#ptDemoEmail').addClass('advisory-chk'); }
		});
	
	// Triggering events on window load
	$('select.mandatory-chk, select.advisory-chk, #elem_patientStatus, select[id^=relInfoReletion], #relation1, select[id^=family_information_relatives], #emerRelation,#sexual_orientation,#gender_identity').trigger('change');
	$('input.mandatory-chk,input.advisory-chk').trigger('keyupSwitch');
	// Set Default value to no || value changed to yes
	// by triggering event above for select and input tags 
	$("#hidChkChangeDemoTabDb",top.document).val('no');
	$("#hidChkDemoTabDbStatus",top.document).val('loaded');
 
	top.btn_show("DEMO");
	
	var r_modal = false;
	var e_modal = false;
	var l_modal = false;
	var i_modal = false;
	$("body").on('click','.load_modal',function(e){
		e.preventDefault();
		var t = $(this).data('modal');	
		var chk = false;
		if( t == 'race_modal') chk = r_modal; 
		else if( t == 'ethnicity_modal') chk = e_modal;
		else if( t == 'language_modal') chk = l_modal;
		else if( t == 'interpreter_modal') chk = i_modal;
		
		if(chk) {
			$("#" + t).modal('show');
			return false;	
		}
		
		var u = top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/demographics/ajax_handler.php';
		var p = {action:t};
		$.ajax({
				url:u,
				type:'post',
				data:p,
				dataType:'json',
				beforeSend:function(){
					$("#" + t).modal('show');
				},
				success:function(r){
					var d = r.data;
					$("#" + t + " .modal-body").html(d);
					if( t == 'race_modal') r_modal = true
					else if( t == 'ethnicity_modal') e_modal = true;
					else if( t == 'language_modal') l_modal = true;
					else if( t == 'interpreter_modal') i_modal = true;
				}
		});
		
	});
    
	$('body').on('show.bs.modal','#pt_account_status',function(){
		var btn_array = [];
		top.fmain.set_modal_btns('pt_account_status .modal-footer:first',btn_array);
	});
	$('body').on('show.bs.modal','#reminderChoice',function(){
		var btn_array = [];
		top.fmain.set_modal_btns('reminderChoice .modal-footer:first',btn_array);
	});
	$('body').on('show.bs.modal','#emergencyContact',function(){
		var btn_array = [];
		top.fmain.set_modal_btns('emergencyContact .modal-footer:first',btn_array);
	});
	
	//Start Temp Key generation By Default
	if($("#temp_key").val()=="" && $("#usernm").val()=="") {
		$(".activation-key").trigger('click');
	}
	//top.btn_show("DEMO");
});

// function for display current age in Years and months 
function get_age(fromdate, dob_id, yearBox, monthBox)
{
	if(dob_id != ''){
		var val_date = $('#'+dob_id+'').val();
		fromdate = val_date;
	}
	todate = new Date();
	var age= [], fromdate= new Date(fromdate),
	y= [todate.getFullYear(), fromdate.getFullYear()],
  ydiff= y[0]-y[1],
  m= [todate.getMonth(), fromdate.getMonth()],
  mdiff= m[0]-m[1],
  d= [todate.getDate(), fromdate.getDate()],
  ddiff= d[0]-d[1];
	
	if(mdiff < 0 || (mdiff=== 0 && ddiff<0))
		--ydiff;
	
	if(mdiff<0 || (mdiff === 0 && ddiff<0 ))
		mdiff += 12;
		
  if(ddiff<0)
	{
		fromdate.setMonth(m[1]+1, 0);
		ddiff= fromdate.getDate()-d[1]+d[0];
		--mdiff;
	}
	
	age.push(ydiff);
  age.push(mdiff);
	age.push(ddiff);
	
	$('#'+yearBox+'').html(age[0]);
	$('#'+monthBox+'').html(age[1]);
}

function checkIfAlphabet(e, ctrlId){
	txtVal ='';
	var txtVal = $("#"+ctrlId).val();
	if(txtVal!=''){
		var unicode= e.keyCode? e.keyCode : e.charCode;
		if(typeof unicode !== 'undefined'){
			if(!(unicode>=65 && unicode<=90) && !(unicode==8 || unicode==46 || unicode==13 || unicode==9
			 || unicode==16 || unicode==17 || unicode==18 || unicode==37 || unicode==38 || unicode==39 || unicode==40 || unicode==32)){
				top.fAlert("Only Character values accepted");
				var rightVal = txtVal.substr(0, parseInt(txtVal.length)-1);
				$("#"+ctrlId).val(rightVal);
				return false;
			}
		}
	}
}

function make_id_empty(odjId)
{
	if($("#"+odjId)) {
		if($("#"+odjId).val() !== "") {
			$("#"+odjId).val('');
		}
	}
}

function refine_data(obj)
{
	var data = obj.value;
	data = data.replace('"','"');
	obj.value = data;

	if( obj.hasAttribute('data-content') ) {
		if( obj.value == '' ) {
			obj.setAttribute('data-content','');
			$(obj).popover('destroy');
		}
	}
}	
		
var ModalID			=	"";
var txtFieldArr	=	"";
var hiddFieldTxt	=	"";
function show_multi_phy(op, phyType)
{
			op = op || 0;
			phyType = phyType || 0;
			
			var pTypeStr		=	"";
			var pTypeHidStr		=	"";
				
			if(phyType == 1)
			{
					ModalID			=	"referringPhysician";
					txtFieldArr		=	"txtRefPhyArr[]";
					pTypeStr		=	"strRefPhy";
					pTypeHidStr		=	"strRefPhyHid";
					hiddFieldTxt	=	"hidRefPhy";
			}
			else if(phyType == 2)
			{
					ModalID			=	"coManagedPhysician";
					txtFieldArr		=	"txtCoPhyArr[]";
					pTypeStr		=	"strCoPhy";
					pTypeHidStr		=	"strCoPhyHid";
					hiddFieldTxt	=	"hidCoPhy";
			}
			else if(phyType == 4)
			{
					ModalID			=	"primaryCarePhysician";
					txtFieldArr		=	"txtPCPDemoArr[]";
					pTypeStr		=	"strPCPDemoPhy";
					pTypeHidStr		=	"strPCPDemoHid";
					hiddFieldTxt	=	"hidPCPDemo";
			}
				
			if(op == 1)
			{
				var arrPhy 		= new Array();
				var arrPhyHid 	= new Array();
				var strPhy 		= "";
				var strPhyHid 	= "";
				
				if(document.getElementsByName(txtFieldArr))
				{
						var objPhyArr = document.getElementsByName(txtFieldArr);
						for(var i = 0; i < objPhyArr.length; i++){
							var objPhyArrID = objPhyArr[i].id;
							var arrPhyArrID = objPhyArrID.split("-");
							var hidPhyArrID = "hidPhyArr-" + arrPhyArrID[1];
							if((document.getElementById(objPhyArrID)) && (document.getElementById(hidPhyArrID))){
								arrPhy[i] = document.getElementById(objPhyArrID).value;
								arrPhyHid[i] = document.getElementById(hidPhyArrID).value;
							}							
						}
						if(arrPhy.length > 0)
						{
							strPhy = arrPhy.join("!~#~!");
							strPhyHid = arrPhyHid.join("!~#~!");
						}
				}
				
				var d = 'mode=get&phyType='+phyType+'&'+pTypeStr+'='+strPhy+'&'+pTypeHidStr+'='+strPhyHid;
				var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
				
				top.master_ajax_tunnel(url,top.fmain.show_multi_phy_handler);
				
			}
			else if(op == 0){
				if($("#tat_table"))
					$("#tat_table").hide();	
				$("#"+ModalID).modal('hide');
			}
			else if(op == 2){
				var selectedEffect = "blind";
				if(phyType == 1){
					var strTxtRefPhyArr = "";
					var strHidRefPhyArrID = "";	
					var strHidRefPhyIdID = "";				
					if(document.getElementsByName("txtRefPhyArr[]")){
						var objRefPhyArr = document.getElementsByName("txtRefPhyArr[]");
						for(var i = 0; i < objRefPhyArr.length; i++){
							var objRefPhyArrID = objRefPhyArr[i].id;
							var arrRefPhyArrID = objRefPhyArrID.split("-");
							var hidRefPhyArrID = "hidRefPhyArr-" + arrRefPhyArrID[1];
							var hidRefPhyIdID = "hidRefPhyId" + arrRefPhyArrID[1];
							if((document.getElementById(objRefPhyArrID)) && (document.getElementById(hidRefPhyArrID))){
								strTxtRefPhyArr += document.getElementById(objRefPhyArrID).value + "!$@$!";
								strHidRefPhyArrID += document.getElementById(hidRefPhyArrID).value + "!$@$!";
								if(document.getElementById(hidRefPhyIdID)){
									strHidRefPhyIdID += document.getElementById(hidRefPhyIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeleteRefPhyVal = document.getElementById("hidDeleteRefPhy").value;
					
					var d = 'mode=save&phyType='+phyType+'&strTxtRefPhyArr='+strTxtRefPhyArr+'&strHidRefPhyIdID='+strHidRefPhyIdID+'&strHidRefPhyArrID='+strHidRefPhyArrID+'&hidDeleteRefPhyVal='+hidDeleteRefPhyVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler);
				}
				else if(phyType == 2){
					var strTxtCoPhyArr = "";
					var strHidCoPhyArrID = "";
					var strHidCoPhyIdID = "";
					if(document.getElementsByName("txtCoPhyArr[]")){
						var objCoPhyArr = document.getElementsByName("txtCoPhyArr[]");
						for(var i = 0; i < objCoPhyArr.length; i++){
							var objCoPhyArrID = objCoPhyArr[i].id;
							var arrCoPhyArrID = objCoPhyArrID.split("-");
							var hidCoPhyArrID = "hidCoPhyArr-" + arrCoPhyArrID[1];
							var hidCoPhyIdID = "hidCoPhyId" + arrCoPhyArrID[1];
							if((document.getElementById(objCoPhyArrID)) && document.getElementById(hidCoPhyArrID)){
								strTxtCoPhyArr += document.getElementById(objCoPhyArrID).value + "!$@$!";
								strHidCoPhyArrID += document.getElementById(hidCoPhyArrID).value + "!$@$!";
								if(document.getElementById(hidCoPhyIdID)){
									strHidCoPhyIdID += document.getElementById(hidCoPhyIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeleteCoPhyVal = document.getElementById("hidDeleteCoPhy").value;
					
					var d = "mode=save&phyType="+phyType+"&strTxtCoPhyArr="+strTxtCoPhyArr+"&strHidCoPhyIdID="+strHidCoPhyIdID+"&strHidCoPhyArrID="+strHidCoPhyArrID+"&hidDeleteCoPhyVal="+hidDeleteCoPhyVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler_2);
				}
				else if(phyType == 4){
					var strTxtPCPDemoArr = "";
					var strHidPCPDemoArrID = "";
					var strHidPCPDemoIdID = "";
					if(document.getElementsByName("txtPCPDemoArr[]")){
						var objPCPDemoArr = document.getElementsByName("txtPCPDemoArr[]");
						for(var i = 0; i < objPCPDemoArr.length; i++){
							var objPCPDemoArrID = objPCPDemoArr[i].id;
							var arrPCPDemoArrID = objPCPDemoArrID.split("-");
							var hidPCPDemoArrID = "hidPCPDemoArr-" + arrPCPDemoArrID[1];
							var hidPCPDemoIdID = "hidPCPDemoId" + arrPCPDemoArrID[1];
							if((document.getElementById(objPCPDemoArrID)) && document.getElementById(hidPCPDemoArrID)){
								strTxtPCPDemoArr += document.getElementById(objPCPDemoArrID).value + "!$@$!";
								strHidPCPDemoArrID += document.getElementById(hidPCPDemoArrID).value + "!$@$!";
								if(document.getElementById(hidPCPDemoIdID)){
									strHidPCPDemoIdID += document.getElementById(hidPCPDemoIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeletePCPDemoVal = document.getElementById("hidDeletePCPDemo").value;
					
					var d = "mode=save&phyType="+phyType+"&strTxtPCPDemoArr="+strTxtPCPDemoArr+"&strHidPCPDemoArrID="+strHidPCPDemoArrID+"&strHidPCPDemoIdID="+strHidPCPDemoIdID+"&hidDeletePCPDemoVal="+hidDeletePCPDemoVal;
					var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,top.fmain.show_multi_phy_save_handler_4);
					
				}
			}
		}
		
function show_multi_phy_handler(respRes)
{
		var arrResp = respRes.split("!~-1-~!");
		var arrTemp = arrResp[1].split("~-~");
		var phyName = new Array();
		for(var a = 0; a <= arrTemp.length; a++){
			phyName[a] = arrTemp[a];
		}
		arrTemp = arrResp[2].split("~-~");
		var phyNameID = new Array();
		for(var a = 0; a <= arrTemp.length; a++){
			phyNameID[a] = arrTemp[a];
		}
		
		$("#"+ModalID).html(arrResp[0]);
		
		if(document.getElementsByName(txtFieldArr)){
			var objPhyArr = document.getElementsByName(txtFieldArr);
			for(var i = 0; i < objPhyArr.length; i++){
				var objPhyArrID = objPhyArr[i].id;
				var arrPhyArrID = objPhyArrID.split("-");
				var hidPhyArrID = hiddFieldTxt + "Arr-" + arrPhyArrID[1];
				if((document.getElementById(objPhyArrID)) && (document.getElementById(hidPhyArrID))){
				}							
			}
		}
		
		$("#"+ModalID).modal('toggle');

}

function show_multi_phy_save_handler(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("elem_physicianName").className = "form-control";
		document.getElementById("elem_physicianName").value = arrRespRes[1];
		document.getElementById("pcare").value = arrRespRes[2];
		document.getElementById("elem_physicianName").setAttribute('data-content',arrRespRes[3]);
		$("#"+ModalID).modal('hide');
	}
}

function show_multi_phy_save_handler_2(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("co_man_phy").className = "form-control";
		document.getElementById("co_man_phy").value = arrRespRes[1];
		document.getElementById("co_man_phy_id").value = arrRespRes[2];
		document.getElementById("co_man_phy").setAttribute('data-content',arrRespRes[3]);
		//$("#divMultiCoPhy").hide(selectedEffect,"", 500);
		$("#"+ModalID).modal('hide');
	}		
}

function show_multi_phy_save_handler_4(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("primaryCarePhy").className = "form-control";
		document.getElementById("primaryCarePhy").value = arrRespRes[1];
		document.getElementById("pCarePhy").value = arrRespRes[2];
		//$("#divMultiPCPDemo").hide(selectedEffect,"", 500);
		document.getElementById("primaryCarePhy").setAttribute('data-content',arrRespRes[3]);
		$("#"+ModalID).modal('hide');
	}
}
		
function add_phy_row(add_image_id, del_image_id, intCounter, phyType)
{
			var objDelImg = $("#"+del_image_id);
			var objAddImg = $("#"+add_image_id);
			
			if(objAddImg){ objAddImg.addClass('hidden') }			
			if(objDelImg){ objDelImg.removeClass('hidden');	}
			
			var intCounterTemp = parseInt(intCounter) + 1;
			var divTrTag = document.createElement("div");
			divTrTag.id = "divTR" + "-" + phyType + "-" + intCounterTemp;
			divTrTag.className = "col-xs-12 margin-top-5";
			//divTrTag.style.marginBottom = "5px";
			
			var divTDTag1 = document.createElement("div");
			divTDTag1.className = "col-xs-2 text-center";
			divTDTag1.innerHTML = intCounterTemp;			
			divTrTag.appendChild(divTDTag1);
			
			var divTDTag2 = document.createElement("div");
			divTDTag2.className = "col-xs-9";
			
			if(phyType == 1){
				var txtId = "txtRefPhyArr-"+intCounterTemp;
			}
			else if(phyType == 2){
				var txtId = "txtCoPhyArr-"+intCounterTemp;
			}
			else if(phyType == 4){
				var txtId = "txtPCPDemoArr-"+intCounterTemp;
			}
			var txtBox = document.createElement("input");
			txtBox.type = "text";
			if(phyType == 1){
				txtBox.name = "txtRefPhyArr[]";
			}
			else if(phyType == 2){
				txtBox.name = "txtCoPhyArr[]";
			}
			else if(phyType == 4){
				txtBox.name = "txtPCPDemoArr[]";
			}
			txtBox.id = txtId;
			txtBox.value = "";
			txtBox.className = "form-control";
			
			if(phyType == 1){
				var hidId = "hidRefPhyArr-"+intCounterTemp;
			}
			else if(phyType == 2){
				var hidId = "hidCoPhyArr-"+intCounterTemp;
			}
			else if(phyType == 4){
				var hidId = "hidPCPDemoArr-"+intCounterTemp;
			}
			//
			txtBox.setAttribute('onKeyup',"top.loadPhysicians(this,'"+hidId+"');");
			txtBox.setAttribute('onFocus',"top.loadPhysicians(this,'"+hidId+"');");
			
			
			var hidBox = document.createElement("input");
			hidBox.type = "hidden";
			if(phyType == 1){
				hidBox.name = "hidRefPhyArr[]";
			}
			else if(phyType == 2){
				hidBox.name = "hidCoPhyArr[]";
			}
			else if(phyType == 4){
				hidBox.name = "hidPCPDemoArr[]";
			}
			divTDTag2.appendChild(txtBox);
			hidBox.id = hidId;
			hidBox.value = "";
			divTDTag2.appendChild(hidBox);
			divTrTag.appendChild(divTDTag2);
			
			var divTDTag3 = document.createElement("div");
			divTDTag3.className = "col-xs-1";
			var imgDelId = "imgDel" + "-" + phyType + "-" + intCounterTemp;
			var imgAddId = "imgAdd" + "-" + phyType + "-" + intCounterTemp;
			var strImgHTML = "<span id=\""+imgDelId+"\" name=\""+imgDelId+"\" class=\"pointer hidden\" onClick=\"del_phy_row('"+imgDelId+"','"+intCounterTemp+"', '','"+phyType+"');\" ><i class=\"glyphicon glyphicon-remove\"></i></span>";
			//var strImgHTML = "<img src=\""+top.JS_WEB_ROOT_PATH+"/library/images/close_small.png\" id=\""+imgDelId+"\" name=\""+imgDelId+"\" class=\"physician_del hidden\" onClick=\"del_phy_row('"+imgDelId+"','"+intCounterTemp+"', '','"+phyType+"');\" />";
			strImgHTML += "<span id=\""+imgAddId+"\" name=\""+imgAddId+"\" onClick=\"add_phy_row('"+imgAddId+"','"+imgDelId+"', '"+intCounterTemp+"', '"+phyType+"');\" class=\"pointer\" ><i class=\"glyphicon glyphicon-plus\"></i></span>"
			//strImgHTML += "<img src=\""+top.JS_WEB_ROOT_PATH+"/library/images/add_small.png\" id=\""+imgAddId+"\" name=\""+imgAddId+"\" onClick=\"add_phy_row('"+imgAddId+"','"+imgDelId+"', '"+intCounterTemp+"', '"+phyType+"');\" class=\"physician_add\" />"
			divTDTag3.innerHTML = strImgHTML;
			
			divTrTag.appendChild(divTDTag3);
			if(phyType == 1){
				document.getElementById("divMultiPhyInner1").appendChild(divTrTag);
			}
			else if(phyType == 2){
				document.getElementById("divMultiPhyInner2").appendChild(divTrTag);
			}
			else if(phyType == 4){
				document.getElementById("divMultiPhyInner4").appendChild(divTrTag);
			}
			//txtBox.addEventListener("keyup",function(){loadPhysicians(txtBox,hidId,'','','popup')});
			//txtBox.addEventListener("focus",function(){loadPhysicians(txtBox,hidId,'','','popup')});
			document.getElementById(txtId).focus();
		}
		
function del_phy_row(del_image_id, intCounter, intPhyIdDB, phyType)
{
			var objDelImg = $("#"+del_image_id);
			
			intPhyIdDB = intPhyIdDB || 0;
			//var divTrTag = "divTR" + intCounter;
			var divTrTag = "divTR" + "-" + phyType + "-" + intCounter
			if((intPhyIdDB > 0) && phyType == 1){				
				document.getElementById("hidDeleteRefPhy").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			else if((intPhyIdDB > 0) && phyType == 2){				
				document.getElementById("hidDeleteCoPhy").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			else if((intPhyIdDB > 0) && phyType == 4){				
				document.getElementById("hidDeletePCPDemo").value += intPhyIdDB+"~~"+intCounter+'-';
			}
			if(document.getElementById(divTrTag)){
				var divType = "divMultiPhyInner" + phyType;
				var objMainDiv = document.getElementById(divType);
				objMainDiv.removeChild(document.getElementById(divTrTag));
			}
		}
		
function search_email2(event)
{
	if(event.keyCode == 64){
		if(document.getElementById('ptDemoEmail').value.length> 3)
		{
			document.getElementById('valDiv').style.display='block';
			document.getElementById('valDiv').style.visibility='visible';
		}
	}
}		

function display_hide_timmings()
{
	if($("#hipaa_voice").is(':checked'))
	{
		$('#trVoiceTimmings').removeClass('hidden').addClass('show');
	}
	else
	{
		$('#trVoiceTimmings').removeClass('show').addClass('hidden');
	}
}

function scan_patient_image()
{
	var features = "toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=630,left=150,top=60";
	var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/demographics/webcam/flash.php";
	var wname = "lic";
	top.popup_win(url,wname,features);
}

function image_DIV(imageSrc,adiv)
{
	if(imageSrc)
	{
		var tmpImg = imageSrc;
		imageSrc = top.JS_WEB_ROOT_PATH + '/data/'+ top.practice_dir +'/'+imageSrc;
		if(adiv == "ptImage")
		{
			$("#ptImageDiv").html('<img src="'+imageSrc+'" >');
			$("#ptImage td").html('<img src="'+imageSrc+'" >');
			$("#div_pt_name img#pt_img_tmb",window.top.document).attr('src',imageSrc);
			/*document.getElementById('ptImageDiv').onclick=function() {
				$('#ptImage').show();
			};*/
		}
		else if(adiv == "ptLic")
		{
			var tmpArr = imageSrc.split('/');
			var lKey = tmpArr.length-1;
			tmpArr[lKey] = 'thumbnail/'+tmpArr[lKey];
			var thumbSrc = tmpArr.join('/');
			var html = '<span><img src="'+thumbSrc+'" /><span class="layer" data-toggle="modal" data-target="#imageLicense"></span></span>';
			dgi("ptLicDiv").innerHTML = html;
			$("#imageLicense .modal-body").html('<img src="'+imageSrc+'">');
		}
		else if(adiv == "respLic")
		{
			var tmpArr = imageSrc.split('/');
			var lKey = tmpArr.length-1;
			tmpArr[lKey] = 'thumbnail/'+tmpArr[lKey];
			var thumbSrc = tmpArr.join('/');
			var html = '<span><img src="'+thumbSrc+'" /><span class="layer" data-toggle="modal" data-target="#resp_party_license"></span></span>';
			dgi("respLicDiv").innerHTML = html;
			dgi('resp_license_image').value = tmpImg;
			$("#resp_party_license .modal-body").html('<img src="'+imageSrc+'">');
		}
	}
}

function scan_licence(pid,type)
{
	if( typeof type === 'undefined') type = '';
	var features = "toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1";
	var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/demographics/scan_licence.php"+(type?'?type='+type:'');
	var wname = type+"lic";
	sc_wd=(screen.availWidth-100);
	sc_hg=(screen.availHeight-100);
	features = "location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width="+sc_wd+",height="+sc_hg
	top.popup_win(url,wname,features);
}

function get271Report(id){
		var h = "<?php echo $_SESSION['wn_height'] - 140; ?>";
		window.open('../eligibility/eligibility_report.php?id='+id,'eligibility_report','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
}

function ado_scan_fun(show,formName,scan_id)
{									
	scan_id = scan_id || 0;
	var w = 950 ;
	var h = 660 ;
	var l = parseInt((screen.availWidth - w ) / 2);
	var t = parseInt((screen.availHeight - h ) / 2);
	
	var features = 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width='+w+',height='+h+',left='+l+',top='+t;
	ado_scan_obj = window.open(top.JS_WEB_ROOT_PATH + '/library/classes/scan_ptinfo_medhx_images.php?formName='+formName+"&edit_id="+scan_id+""+'&show='+show,'PtMed'+show, features);
	ado_scan_obj.focus();
}
				 	
function showpdf(id,pdf,image_form){
	if( (typeof id != "undefined") && (id != "") ){
		
		var w = 950 ;
		var h = 700 ;
		var l = parseInt((screen.availWidth - w ) / 2);
		var t = parseInt((screen.availHeight - h ) / 2);
	
		pdf = pdf || '';
		image_form = image_form || '';
		var n = "scan_"+id;
		var url = top.JS_WEB_ROOT_PATH + "/interface/chart_notes/logoImg.php?from=scanImage&scan_id="+id+"&headery="+pdf+"&image_form="+image_form;
		var v = window.open(url,"","width="+w+",height="+h+",resizable=1,scrollbars=1,top="+t+",left="+l+"");				
		v.focus;
	}
}

function addExtra(_this)
{
	var isChecked = $(_this).is(':checked') ? true : false;
	var fldName = $(_this).data('object-id');
	var v = $(_this).val();
	var fldObj = $("#"+fldName);
	
	var tmpObj = fldObj.find('option[value="'+v+'"]');
	
	if( tmpObj.length > 0 ) {
		
		var isCommon = tmpObj.data('common');
		
		if( isCommon == '0' )	tmpObj.remove();
		else tmpObj.prop('selected',isChecked);
	
	} else {
		if( fldName == 'race')
			$("#"+fldName + " option:eq(-1)").before($('<option></option>').val(v).text(v).data('common','0').prop('selected',isChecked));
		else
			$("#"+fldName + " option:eq(-2)").before($('<option></option>').val(v).text(v).data('common','0').prop('selected',isChecked));
	}
	
	fldObj.selectpicker('refresh');
}

function addLanguage(_this)
{
	var isChecked = $(_this).is(':checked') ? true : false;
	
	$("#language_modal [type=checkbox]").prop('checked',false);
	$(_this).prop('checked',isChecked);
	
	var fldName = $(_this).data('object-id');
	var code = $(_this).data('code-name');
	var v = $(_this).val();
	var fldObj = $("#"+fldName);
	
	var c_value = fldObj.val();
	if( c_value == 'Other')	$("#imgBackLanguage").trigger('click');
	
	var tmpObj = fldObj.find('option[value="'+v+'"]');
	
	if( tmpObj.length > 0 ) {
		
		var isCommon = tmpObj.data('common');
		
		if( isCommon == '0' )	tmpObj.remove();
		else tmpObj.prop('selected',isChecked);
	
	} else {
			$("#"+fldName + " option:eq(-2)").before('<option value="'+v+'" data-common="0" data-code="'+code+'" '+(isChecked ? 'selected' : '')+' >'+v+'</option>');
	}
	
	//fldObj.selectpicker('refresh').trigger('change');
	fldObj.trigger('change');
}
function addInterpreter(_this)
{
	var isChecked = $(_this).is(':checked') ? true : false;
	
	$("#interpreter_modal [type=checkbox]").prop('checked',false);
	$(_this).prop('checked',isChecked);
	
	var fldName = $(_this).data('object-id');
	var code = $(_this).data('code-name');
	var v = $(_this).val();
	var fldObj = $("#"+fldName);
	
	var c_value = fldObj.val();
	
	var tmpObj = fldObj.find('option[value="'+v+'"]');
	
	if( tmpObj.length > 0 ) {
		
		var isCommon = tmpObj.data('common');
		
		if( isCommon == '0' )	tmpObj.remove();
		else tmpObj.prop('selected',isChecked);
	
	} else {
			$("#"+fldName + " option:eq(-2)").before('<option value="'+v+'" data-common="0" data-code="'+code+'" '+(isChecked ? 'selected' : '')+' >'+v+'</option>');
	}
	
	//fldObj.selectpicker('refresh').trigger('change');
	fldObj.trigger('change');
}

//Auto fill the Responsible party/guarantor address if patient's age is below 18 years
$('#resp_container').on('click', function(){
    $("#fname1").bind( "focus blur keyup",function(){ autofillRespParty(); });
    $("#lname1").bind( "focus blur keyup",function(){ autofillRespParty(); }); 
});

function autofillRespParty() {
    if(resp_party_arr && resp_party_arr!='' && resp_party_arr!='null' ) {
        if( ($("#fname1").val()!='' && $("#lname1").val()!='') && $("#street1").val()=='' && $("#rcode").val()=='' && $("#rcity").val()=='' && $("#rstate").val()=='') {
            var d=JSON.parse(resp_party_arr);
            $("#street1").val(d.resp_ptStreet);
            $("#street_emp").val(d.resp_ptStreet2);
            $("#rcode").val(d.resp_ptPostalCode);
            $("#rzip_ext").val(d.resp_ptzip_ext);
            $("#rcity").val(d.resp_ptCity);
            $("#rstate").val(d.resp_ptState);	
        }
    }
}