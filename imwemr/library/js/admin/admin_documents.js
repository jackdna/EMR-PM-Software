js_array = js_php_arr;

//------------------------------------- Common functions
function selectTemplate(id, name, tab){
	switch(tab){
		case 'collection':
			$('#templateName').val(name);
		break;
		
		case 'consent':
			$('#consent_form_name').val(name);
			$('#consent_form_id').val(id);
		break;
	}
	$('#edit_id').val(id);
	top.show_loading_image('show');
	$('#perform_action').val('update');
	document.template_form.submit();
}

function reset_page(){
	var sub_tab = '';
	if(js_array.cur_tab == 'instructions'){
		js_array.cur_tab = 'education';
		sub_tab = '&sub=instructions';
	} 
	window.location.href = js_array.base_path+'/index.php?showpage='+js_array.cur_tab+sub_tab;
}

//Validating fields
function checkFields(section){
	switch(section){
		//Collection Forms
		case 'collection':
		//Consult Forms
		case 'consult':
		//Education Forms
		case 'education':
		//Instruction Forms
		case 'instructions':
		//Op notes Forms
		case 'op_notes':
		//Recall letter
		case 'recall':
			if($('#templateName').val() == ''){
				fAlert ('Please Enter template name to save.');
				top.show_loading_image('hide');
				if($('#templateName').hasClass('mandatory') == false){
					$('#templateName').addClass('mandatory');
				}
				$('#templateName').focus();
				return false;
			}
		break;
		
		//Consent Forms
		case 'surgery_consent':
		case 'consent':
			var msg = '';
			var validate = true;
			if($('#consent_form_name').val() == ''){
				msg = '&bull; Consent form name is required.<br>';
				if($('#consent_form_name').hasClass('mandatory') == false){
					$('#consent_form_name').addClass('mandatory');
				}
				validate = false;
			}else{
				if($('#consent_form_name').hasClass('mandatory')){
					$('#consent_form_name').removeClass('mandatory');
				}
			}
			
			if($('#consent_cat').val() == ''){
				msg += '&bull; Category is required.<br>';
				$('#consent_cat').selectpicker('setStyle', 'btn-default', 'remove').selectpicker('refresh');
				$('#consent_cat').selectpicker('setStyle', 'btn-warning', 'add').selectpicker('refresh');
				validate = false;
			}else{
				$('#consent_cat').selectpicker('setStyle', 'btn-warning', 'remove').selectpicker('refresh');
				$('#consent_cat').selectpicker('setStyle', 'btn-default', 'add').selectpicker('refresh');
			}
			
			if(msg.length > 0 && validate == false){
				fAlert(msg);
				return false;
			}
		break;
		
		case 'package':
			var msg = '';
			var validate = true;
			if($('#package_category_name').val() == ''){
				msg = '&bull; Package name is required.<br>';
				if($('#package_category_name').hasClass('mandatory') == false){
					$('#package_category_name').addClass('mandatory');
				}
				validate = false;
			}else{
				if($('#package_category_name').hasClass('mandatory')){
					$('#package_category_name').removeClass('mandatory');
				}
			}
			
			if(msg.length > 0 && validate == false){
				fAlert(msg);
				return false;
			}
		break;
		
		case 'pt_docs':
			var msg = '';
			var validate = true;
			
			if($('#templateName').val() == ''){
				fAlert ('Please Enter template name to save.');
				top.show_loading_image('hide');
				if($('#templateName').hasClass('mandatory') == false){
					$('#templateName').addClass('mandatory');
				}
				$('#templateName').focus();
				return false;
			}
			
			if($('#pt_docs_category').val() == ''){
				msg += '&bull; Category is required.<br>';
				$('#pt_docs_category').selectpicker('setStyle', 'btn-default', 'remove').selectpicker('refresh');
				$('#pt_docs_category').selectpicker('setStyle', 'btn-warning', 'add').selectpicker('refresh');
				validate = false;
			}else{
				$('#pt_docs_category').selectpicker('setStyle', 'btn-warning', 'remove').selectpicker('refresh');
				$('#pt_docs_category').selectpicker('setStyle', 'btn-default', 'add').selectpicker('refresh');
			}
			
			if(msg.length > 0 && validate == false){
				fAlert(msg);
				return false;
			}
		break;
		
		case 'order_temp':
			var msg = '';
			var validate = true;
			
			if($('#template_name').val() == ''){
				fAlert ('Please Enter template name to save.');
				top.show_loading_image('hide');
				if($('#template_name').hasClass('mandatory') == false){
					$('#template_name').addClass('mandatory');
				}
				$('#template_name').focus();
				return false;
			}
			
			if($('#order_type_id').val() == ''){
				msg += '&bull; Category is required.<br>';
				$('#order_type_id').selectpicker('setStyle', 'btn-default', 'remove').selectpicker('refresh');
				$('#order_type_id').selectpicker('setStyle', 'btn-warning', 'add').selectpicker('refresh');
				validate = false;
			}else{
				$('#order_type_id').selectpicker('setStyle', 'btn-warning', 'remove').selectpicker('refresh');
				$('#order_type_id').selectpicker('setStyle', 'btn-default', 'add').selectpicker('refresh');
			}
			
			if(msg.length > 0 && validate == false){
				fAlert(msg);
				return false;
			}
		break;
		
		//Prescriptions
		case 'prescriptions':
			if($('#prescriptionType').val() == ''){
				fAlert ('Please select a template to save.');
				top.show_loading_image('hide');
				/* if($('#prescriptionType').hasClass('mandatory') == false){
					$('#prescriptionType').addClass('mandatory');
				} */
				$('#prescriptionType').focus();
				return false;
			}
		break;

	}
	$("#saveBtn").val('submit');
	return true;
}

//This is used to add and update data
function perform_action(submit){
	if(checkFields(js_array.cur_tab)){
		var base_url = js_array.base_path+'/index.php?showpage='+js_array.cur_tab;
		for ( instance in CKEDITOR.instances ) {
			CKEDITOR.instances[instance].updateElement();
		}
		if( $("#doc_from").val() == 'scanDoc')
		{
			top.show_loading_image('show');
			browser = get_browser();
			if(browser == "ie")
				upload(document.template_form);
			else
				document.template_form.submit();
			
			return false;	
		}
		
		if(submit){
			top.show_loading_image('show');
			document.template_form.submit();
		}else{
			var form_data = $("#template_form").serialize();
			$.ajax({
				url:js_array.ajax_url,
				data:form_data,
				type:'POST',
				dataType:'JSON',
				beforeSend:function(){
					top.show_loading_image('show');
				},
				success:function(response){
					top.alert_notification_show(response.status);
					if(response.action.length){
						switch(response.action){
							case 'add':
							case 'delete':
								window.location.href = base_url;
							break;
							
							case 'update':
								window.location.href = base_url+'&edit_id='+response.edit_id;
							break;
						}
					}
				},
				complete:function(){
					top.show_loading_image('hide');
				}
			});
		}
	}
}

//Deleting Templates
function delTemplate(id,msg){
	if(typeof(msg)!='boolean'){msg = true;}
	if(msg){
		top.fancyConfirm("Are you sure to delete the template?","","top.fmain.delTemplate('"+id+"',false)")
	}else{
		$.ajax({
			url:js_array.ajax_url,
			data:'delId='+id+'&current_tab='+js_array.cur_tab,
			dataType:'JSON',
			beforeSend:function(){
				top.show_loading_image('show');
			},
			success:function(response){
				top.alert_notification_show(response);
				window.location.href = js_array.base_path+'/index.php?showpage='+js_array.cur_tab;
			},
			complete:function(){
				top.show_loading_image('hide');
			}
		});
	}
}
//Returns footer btn array
function get_footer_btns(section){
	var return_arr = '';
	section = (section != '') ? section : js_array.cur_tab;
	switch(section){
		case 'collection':
		case 'consult':
		case 'op_notes':
		case 'recall':
		case 'order_temp':
		case 'panels':
			var save_btn_str = js_array.cur_tab+'_detail';
			var reset_btn_str = js_array.cur_tab+'_reset';
			return_arr = [[save_btn_str,"Save","top.fmain.perform_action();"],[reset_btn_str,"Cancel","top.fmain.reset_page();"]];
		break;
		
		case 'education':
		case 'instructions':
			return_arr = [["document_submit","Save","top.fmain.perform_action(true);"],["document_new","Add New","top.fmain.reset_page();"]];
		break;
		case 'prescriptions':
			return_arr = [["document_submit","Save","top.fmain.perform_action();"],["document_new","Cancel","top.fmain.reset_page();"]];
		break;
		case 'consent':
			return_arr = new Array;
			if(!js_array.old_ver_id){	//Show save button if old form is not slected
				return_arr.push(["get_consent_detail","Save","top.fmain.perform_action();"]);
			}
			return_arr.push(["consent_manage_category","Manage Categories","top.fmain.$('#cat_show_modal').modal('show');"]);
			return_arr.push(["new_consent_letters","Cancel","top.fmain.reset_page();"]);
		break;
		
		case 'package':
			return_arr = [["add_new","Add New","top.fmain.get_package_modal();"],["dx_cat_del","Delete","top.fmain.manage_packages('delete');"]];
		break;
		
		case 'pt_docs':
			return_arr = [['Save_btn_doc',"Save","top.fmain.perform_action();"],["add_new","Cancel","top.fmain.reset_page();"],["consent_manage_category","Manage Categories","top.fmain.$('#cat_show_modal').modal('show');"]];
		break;
		
		case 'statements':
			return_arr = [["document_submit","Save","top.fmain.perform_action(true);"]];
		break;
		
		case 'smart_tags':
			return_arr = [["save_smartSubTag","Save Tag Options","top.fmain.save_sub_tags('sub');"]];
		break;
		
		case 'surgery_consent':
			return_arr = new Array;
			if(!js_array.old_ver_id){	//Show save button if old form is not slected
				return_arr.push(["get_consent_detail","Save","top.fmain.perform_action();"]);
			}
			return_arr.push(["consent_manage_category","Manage Categories","top.fmain.$('#cat_show_modal').modal('show');"]);
			return_arr.push(["new_consent_letters","Cancel","top.fmain.reset_page();"]);
		break;
	}
	
	return return_arr;
}

//Returns textarea name depending on cur tab
function get_textarea_nm(){
	var return_val = 'content';
	switch(js_array.cur_tab){
		case 'package':
		case 'smart_tags':
		case 'logos':
			return_val = '';
		break;	
		case 'panels':
			return_val = ['leftpanel','header','footer'];
		break;
	}
	return return_val;
}

//Set height of panels
function set_elem_height(){
	var prnt_elem = $('.whtbox');
	var prnt_dimensions = {};
	prnt_dimensions.height = (prnt_elem.height() - 30);

	//Left Panel sizing	
	if($('.lft_pnl').length){
		$('.lft_pnl').css({
			'max-height':(prnt_dimensions.height),
			'oveflowY':'auto',
			'overflowX':'hidden'
		});
	}
	
	//Right Panel Sizing
	if($('.rght_pnl').length){
		var right_target = $('.rght_pnl').find('textarea.ckeditor_textarea').not('.extra_textarea');
		var right_dimensions = right_target.offset();
		
		//For Panels only
		if($('.extra_textarea').length){
			var editor_height = parseInt((prnt_dimensions.height - 147) - right_dimensions.top);
			CKEDITOR.config.height = editor_height;
			
			
			var height = parseInt((prnt_dimensions.height - 120)/$('.extra_textarea').length);
			$('.extra_textarea').each(function(id,elem){
				var txt_nm = $(elem).attr('id');
				var editor_txt = CKEDITOR.instances[''+txt_nm+''];
				//console.log(editor_txt);
				editor_txt.config.height = height - 70;
			});
		}else{ // For every other section
			var editor_height = parseInt((prnt_dimensions.height - 70) - right_dimensions.top);
			CKEDITOR.config.height = editor_height;
		}
	}
}

//Set sort order
function set_order(){
	var sort_order = $('#sort_order').val();
	var sort_time = $('#sort_order').data('first-time');
	
	var sort_first_time = '';
	if(sort_time == 'yes'){
		sort_first_time = 'no';
	}
	
	var sort_value = 'ASC';
	if(sort_order == 'ASC'){
		sort_value = 'DESC';
	}
	
	window.location.href = js_array.base_path+'/index.php?showpage='+js_array.cur_tab+'&sort_by='+sort_value+'&sort_first_time='+sort_first_time;
}

//This function is used to trigger default functions
function set_init(load){
	//To trigger functions for certain tab
	switch(load){
		case 'document':		//Will be loaded on document ready
			switch(js_array.cur_tab){
				case 'consent':
					check_yreview($('#consent_cat'));
				break;
				
				case 'education':
				case 'instructions':
					education_on_load();
				break;
				case 'logos':
					logos_on_load();
				break;
				case 'smart_tags':
					if(js_array.sel_smart_tag_id){
						if($('#page_buttons #save_smartSubTag',top.document).hasClass('hide') == true){
							$('#page_buttons #save_smartSubTag',top.document).removeClass('hide');
						}
						$('#main_tag').find('span[data-id="'+js_array.sel_smart_tag_id+'"]:first').trigger('click');
					}else{
						if($('#page_buttons #save_smartSubTag',top.document).hasClass('hide') == false){
							$('#page_buttons #save_smartSubTag',top.document).addClass('hide');
						}
					}
				break;
			}
		break;
		
		case 'window':			//Will be loaded on window load
			switch(js_array.cur_tab){
				case 'smart_tags':
					set_tag_height(); 		//Setting height of divs in Documents > Smart Tags
				break;
				
				case 'logos':
					set_logo_elem();		//Setting preview height on window load in Documents > Logos
				break;
				
				default:
				set_elem_height();		//Setting max height of left and right panels
			}
		break;
	}
	
}

//This function is used to show preview of template
function get_template_preview(){
	var myinstances = [];
	var content_data = '';
	var template_header = $('[data-preview-template]').val();
	var left_panel = '';
	var header_panel = '';
	var footer_panel = '';
	var preview_str = '';
	var billing_server = js_array.billing_server;
	
	
	//Editor data
	for(var i in CKEDITOR.instances) {
		//myinstances[CKEDITOR.instances[i].name] =  CKEDITOR.instances[i].getData(); 
		content_data = CKEDITOR.instances[i].getData();
	}
	
	//If panel key exists in js php array
		// which is in Documents > Consults [ Now ]
	if(js_array.temp_panels){
		if($('#header_chk').prop('checked') == true){
			header_panel = js_array.temp_panels.header+'<br /><br />'; 
		}
		
		if($('#footer_chk').prop('checked') == true){
			footer_panel = '<br /><br />'+js_array.temp_panels.footer;
		}

		if($('#leftpanel_chk').prop('checked') == true){
			left_panel = js_array.temp_panels.leftpanel; 
			//Switching left panel acc. to global billing server
			switch(js_array.billing_server){
				case 'patel':
					content_data = '<table style="width:100%;"><tr><td valign="top" style="width:80%;">'+content_data+'</td><td valign="top" style="width:20%;">'+left_panel+'</td></tr></table>';
				break;	
				
				default:
					content_data = '<table style="width:100%;"><tr><td valign="top" style="width:20%">'+left_panel+'</td><td valign="top" style="width:80%;">'+content_data+'</td></tr></table>';
			}
		}
	}
	
	//Str to be shown
	preview_str = header_panel+content_data+footer_panel;
	
	if(content_data.length){
		show_modal('template_preview_modal',template_header,preview_str,'','','modal-lg');
	}
}


//------------------------------------- Consent functions
function check_yreview(obj){
	var value = $(obj).find('option:selected').text();
	if($.inArray(value,js_array.consent_cat_arr) != -1){
		if($('#td_yreview').hasClass('hide')){
			$('#td_yreview').removeClass('hide');
		}
	}else{
		if($('#td_yreview').hasClass('hide') == false){
			$('#td_yreview').addClass('hide');
		}
		$('#yreview').attr('checked', false);
	}
}


//------------------------------------- Package functions
function get_package_modal(pac_id){
	var frm_objects = ['package_category_id','package_category_name','package_consent_form'];
	var modal_header = 'Add New Record';
	if(pac_id){
		modal_header = 'Edit Record';
		var pac_data = js_array.package_data[pac_id];
		$.each(frm_objects,function(id,val){
			if(val == 'package_consent_form'){
				var pack_consent_arr = pac_data[val].split(',');
				$('#'+val).selectpicker('val',pack_consent_arr);
			}else{
				$('#'+val).val(pac_data[val]);
			}
			
		});
	}else{
		$.each(frm_objects,function(id,val){
			$('#'+val).val('');
		});
	}
	$('.selectpicker').selectpicker('refresh');
	$('#package_show_modal .modal-content .modal-title').text(modal_header);
	$('#package_show_modal').modal('show');
}

function manage_packages(mode,msg){
	var url = js_array.ajax_url+'?current_tab='+js_array.cur_tab+'&pac_mode='+mode;
	switch(mode){
		case 'update':
			if(checkFields(js_array.cur_tab)){
				//Consent form value
				var selected_forms = $('#package_consent_form').val();
				var package_name = $('#package_category_name').val();
				var package_id = $('#package_category_id').val();
				var frm_data = 'package_consent_form='+selected_forms+'&package_category_name='+package_name+'&package_category_id='+package_id+'';	
				$.ajax({
					url:url,
					data:frm_data,
					type:'POST',
					dataType:'JSON',
					beforeSend:function(){
						top.show_loading_image('show');
					},
					success:function(response){
						top.alert_notification_show(response.status);
						if(response.action.length){
							$('#package_show_modal').modal('hide');
						}
					},
					complete:function(){
						top.show_loading_image('hide');
					}
				});
			}
			
		break;
		
		case 'delete':
			var selected_ids = new Array;
			$('.chk_sel').each(function(id,elem){
				if($(elem).is(':checked')){
					selected_ids.push($(elem).val());
				}
			});
			
			if(selected_ids.length == 0){
				fAlert('Please select a record to continue');
				return false;
			}else{
				if(!msg){
					top.fancyConfirm("Are you sure to delete the selected records?","","top.fmain.manage_packages('delete',true)");
				}else{
					var del_ids = selected_ids.join(',');
					var frm_data = 'delIds='+del_ids;
					$.ajax({
						url:url,
						data:frm_data,
						type:'POST',
						dataType:'JSON',
						beforeSend:function(){
							top.show_loading_image('show');
						},
						success:function(response){
							top.alert_notification_show(response.status);
							window.location.href = js_array.base_path+'/index.php?showpage='+js_array.cur_tab;
						},
						complete:function(){
							top.show_loading_image('hide');
						}
					});
				}
			}
		break;
	}
}

//------------------------------------- Education functions
//Runs on education tab load to set required values
function education_on_load(){
	
	//To set which div to show on load
	education_show_elem($('#doc_from'));
	
	//On file field change
	$(document).on('change', ':file', function() {
		var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [numFiles, label]);
	});

	// On file change event
	$(document).ready( function() {
		$(':file').on('fileselect', function(event, numFiles, label) {
			var input = $(this).parents('.input-group').find(':text'),
			log = numFiles > 1 ? numFiles + ' files selected' : label;

			if( input.length ) {
				input.val(log);
			}
		});
	});
}

function education_show_elem(obj){
	if($(obj).length){
		if($("#divWriteId").length && $("#divScnDocId").length && $("#uploadDocId").length){
			var cur_val = $(obj).val();
			switch(cur_val){
				case 'writeDoc':
					if($("#divWriteId").hasClass('hide') == true){
						$("#divWriteId").removeClass('hide');
					}
					
					if($("#divScnDocId").hasClass('hide') == false){
						$("#csxi,#dwtcontrolContainer").hide();$("#divScnDocId").addClass('hide');
					}
					
					if($("#uploadDocId").hasClass('hide') == false){
						$("#uploadDocId").addClass('hide');
					}
				break;
				
				case 'scanDoc':
					if($("#divScnDocId").hasClass('hide') == true){
						$("#csxi,#dwtcontrolContainer").show();$("#divScnDocId").removeClass('hide');
						autoLoad = true;
						LoadControl();
					}
					
					if($("#divWriteId").hasClass('hide') == false){
						$("#divWriteId").addClass('hide');
					}
					
					if($("#uploadDocId").hasClass('hide') == false){
						$("#uploadDocId").addClass('hide');
					}
				break;
				
				case 'uploadDoc':
					if($("#uploadDocId").hasClass('hide') == true){
						$("#uploadDocId").removeClass('hide');
					}
					
					if($("#divScnDocId").hasClass('hide') == false){
						$("#csxi,#dwtcontrolContainer").hide();$("#divScnDocId").addClass('hide');
					}
					
					if($("#divWriteId").hasClass('hide') == false){
						$("#divWriteId").addClass('hide');
					}
				break;
			}
		}
	}
}

//------------------------------------- Smart tag functions
//Get sub category of clicked tag
function get_sub_tag(obj,call_from){
	var data = $(obj).data();
	var tag_data = '';
	if(js_array.smart_tag_dt){
		tag_data = js_array.smart_tag_dt;
	}
	var tag_arr = tag_data[data.tag];
	var counter = 1;
	$('#main_cat_sub').val(data.id);
	var sub_str = '<div class="row">';
	if(tag_arr){
		$.each(tag_arr,function(id,val){	
			sub_str += '<div class="col-sm-3 sub_tag_rw">';
				sub_str += '<div class="row">';
					sub_str += '<div class="col-sm-11">';
						sub_str += '<input type="text" class="form-control" data-id="'+val['id']+'" data-tag="'+val['tagname']+'" value="'+val['tagname']+'" data-cat="'+val['under']+'">';
					sub_str += '</div>';
					
					sub_str += '<div class="col-sm-1">';
						sub_str += '<span class="glyphicon glyphicon-remove pointer" onclick="delete_tag(this,true);" data-id="'+val['id']+'" data-cat="'+val['under']+'"></span>';
					sub_str += '</div>';
				sub_str += '</div>';
			sub_str += '</div>';
			if(counter % 4 == 0){
				sub_str += '</div><div class="row pt10">';
			}
			counter++;
		});
	}else{
		sub_str	= '<div class="no_rec_cls"><div class="col-sm-12 text-center">No Record</div></div>';
	}
	
	
	var target_elem = $('#sub_tag');
	
	//Setting update value
	$('#tagname').val(data.tag);
	$('#edit_id').val(data.id);
	$('#tag_ac_btn').text('Update');
	
	if(!call_from){
		//Sub title
		target_elem.find('.head').find('span:first').text('Sub tags for : '+data.tag);	

		//Show add button
		if(target_elem.find('.add_row').hasClass('hide')){
			target_elem.find('.add_row').removeClass('hide');
		}
		
		target_elem.find('#sub_tag_content').html(sub_str);
		
		if($('#page_buttons #save_smartSubTag',top.document).hasClass('hide') == true){
			$('#page_buttons #save_smartSubTag',top.document).removeClass('hide');
		}
	}
}

//Add new tag field
function add_sub_field(){
	var sub_str = '';
	sub_str += '<div class="col-sm-3 sub_tag_rw">';
		sub_str += '<div class="row">';
			sub_str += '<div class="col-sm-11">';
				sub_str += '<input type="text" class="form-control" data-id="0" data-tag="" data-cat="0">';
			sub_str += '</div>';
			
			sub_str += '<div class="col-sm-1">';
				sub_str += '<span class="glyphicon glyphicon-remove pointer" onclick="delete_tag(this);" data-id="" data-cat=""></span>';
			sub_str += '</div>';
		sub_str += '</div>';
	sub_str += '</div>';
	
	if($('.no_rec_cls').length){
		$('.no_rec_cls').remove();
	}
	
	//Finding last row and adding field to it
	var div_count = new Array;
	var target_elem = $('#sub_tag #sub_tag_content > .row:last');
	var target_length = target_elem.find('.sub_tag_rw').length;
	if(target_elem.length){
		if(target_length < 4){
			target_elem.append(sub_str);
		}else if(target_length == 0 || target_length == 4){
			var nw_str = '<div class="row pt10">'+sub_str+'</div>';
			$('#sub_tag #sub_tag_content').append(nw_str);
		}
	}else{
		var nw_str = '<div class="row pt10">'+sub_str+'</div>';
			$('#sub_tag #sub_tag_content').html(nw_str);
	}
	var btn_ar = [["save_smartSubTag","Save Tag Options","top.fmain.save_sub_tags('sub');"]];
	top.btn_show("ADMN",btn_ar);
}


//Save sub tags 
function save_sub_tags(call_from){
	switch(call_from){
		case 'sub':
			var input_field = new Array;
			var a = 0;
			var cat_id = 0;
			$('#sub_tag #sub_tag_content').find('input[type=text]').each(function(id,elem){
				var elem_data = $(elem).data();
				if($(elem).val() != ''){
					elem_data.tag = $(elem).val();
					input_field.push(elem_data);
				}
			});
			if(input_field.length == 0){
				fAlert('Please enter atleast one tag name to continue');
				return false;
			}
			
			if(input_field.length > 0){
				var input_data = JSON.stringify(input_field);
				var main_cat_id = $('#main_cat_sub').val();
				var form_data = 'main_cat='+main_cat_id+'&input_data='+input_data+'&call_from=sub_tag&action=saveUpdate&current_tab='+js_array.cur_tab;
			}
		break;
		
		case 'main':
			var elem_target = $('#tagname');
			if(elem_target.val() != ''){
				var tag_nm = elem_target.val();
				var cat_id = $('#edit_id').val();
				var form_data = 'tag_name='+tag_nm+'&cat_id='+cat_id+'&call_from=main_tag&action=saveUpdate&current_tab='+js_array.cur_tab;
			}else{
				fAlert('Please enter a name to continue');
				return false;
			}
		break;
	}
	
	$.ajax({
		url:js_array.ajax_url,
		type:'POST',
		data:form_data,
		dataType:'JSON',
		beforeSend:function(){
			top.show_loading_image('show');
		},
		success:function(response){
			if(response.counter > 0){
				top.alert_notification_show(response.msg);
				if(response.main_cat_id){
					window.location.href = js_array.base_path+"/index.php?smart_tag_id="+main_cat_id+"&showpage="+js_array.cur_tab;
				}else{
					window.location.href = js_array.base_path+"/index.php?showpage="+js_array.cur_tab;
				}
			}
		},
		complete:function(){
			top.show_loading_image('hide');
		}
	});
}

//Deleting smart tag
function delete_tag(ths,msg,cat_id){
	var id = ths;
	if(typeof(ths) == 'object'){
		id = $(ths).data('id');
		cat_id = $(ths).data('cat');
	}
	if(id != ''){
		if(msg){
			top.fancyConfirm("Are you sure, you want to delete this Variable?","","window.top.fmain.delete_tag('"+id+"',false,'"+cat_id+"')")
		}else{
			top.show_loading_image('show',200);		
				var url = js_array.ajax_url+'?delId='+id+'&current_tab='+js_array.cur_tab;
			$.ajax({
				url:js_array.ajax_url+'?delId='+id+'&current_tab='+js_array.cur_tab,
				type:'POST',
				dataType:'JSON',
				success:function(response){
					top.alert_notification_show(response);
					if(cat_id > 0){	//If Sub tag is deleted
						window.location.href = js_array.base_path+"/index.php?smart_tag_id="+cat_id+"&showpage="+js_array.cur_tab;
					}else{			//If Main tag is deleted
						window.location.href = js_array.base_path+"/index.php?showpage="+js_array.cur_tab;
					}
				},
				complete:function(){
					top.show_loading_image('hide');	
				}
			});
		}
	}else{
		var cls_length = '';
		$(ths).parentsUntil('#sub_tag_content').each(function(id,elem){ //Gets parent till main div id
			if($(elem).hasClass('pt10')){	//If its parent row
				cls_length = $(elem).find('.sub_tag_rw').length;
				if(cls_length == 1){ //If only one block is there --> remove full row
					$(elem).remove();
					$('#sub_tag_content').html('<div class="no_rec_cls"><div class="col-sm-12 text-center">No Record</div></div>');
					top.btn_show("ADMN",[]);
				}else{				// else remove only block
					$(ths).closest('.sub_tag_rw').remove();
				} 
				return false;
			}
		});
	}
}

//Seting height of divs in Documents > Smart Tags
function set_tag_height(){
	var prnt_height = ($('.whtbox').height() / 2);
	$('#main_tag > .row').css({
		'height':((prnt_height - 100)),
		'max-height':((prnt_height - 100)),
		'overflowY':'auto'
	});
	
	$('#sub_tag > .tblBg').css({
		'height':(prnt_height),
		'max-height':(prnt_height),
		'overflowY':'auto'
	});
}

//------------------------------------- Logos functions
function logos_on_load(){
	$('#n').focus();
	$('#template_form').submit(function(){
		if($('#n').val()==''){
			top.fAlert('Please enter a unique value for Logo Variable Caption.','',window.top.fmain.template_form.n);
			return false;  
		}else if($('#f').val()=='' && $('#edit_id').val()==''){
			top.fAlert('Please select image file to upload.','',window.top.fmain.template_form.f);
			return false; 
		}	  
	});
	$('.saved_img').attr('title','Click for full size preview');
	$('.saved_img').click(function(){
		src = $(this).attr('src');
		show_modal('img_modal','Logo Preview','<div class="col-sm-12"><img src="'+src+'"></div>','','450','modal-lg');
	});
	$('.variable_caption').click(function() {
		t = $(this).text();
		i = $(this).parent().siblings('img').attr('src');
		id = $(this).attr('record_id');
		$('#n').val(t);
		$('#edit_id').val(id);
		$('#preview_div').html('<img id="prv_img">');
		$('#prv_img').attr('src',i);
		$('#previous_img').attr('value',i);
		document.template_form.submit.value = 'UPDATE';
	});
}

function set_logo_elem(){
	$('#preview_div').css({
		'height':($('.whtbox').height() - 100),
	});
}

function checkImg(files,ths){   //Function to check whether the provided image is valid or not
	var value = $(ths).val();
	var formVal = $('#submit').val();
	if(value == ''){
			if(formVal == 'UPDATE'){  //This is for update case 
				if(value == ''){
					var previous_src = $('#previous_img').attr('value');
					if(previous_src != ''){
						$('#prv_img').attr('src',previous_src);
					}else{
						$("#preview_div").html(no_preview);	
					}
				}else{
					var src = $('#prv_img').attr('src');
					if(src == ''){
						$("#preview_div").html(no_preview);	
					}	
				}
			}else{
				$("#preview_div").html(no_preview);
			}
	}else{
		var validImg = '';
		var file = files[0];
		var type = file.type;
		switch (type) { //Checking the extension of image
			case 'image/jpeg':
				validImg = true;
				break;
			case 'image/jpg':
				validImg = true;
				break;	
			case 'image/gif':
				validImg = true;
				break;
			default: 
				validImg = false;
		}
		
		if(validImg == false){
			top.fancyAlert('Sorry, only JPG, JPEG &amp; GIF files are allowed.');
			$(ths).val('');
			$(ths).focus();
			var previous_src = $('#previous_img').attr('value');
			if(formVal == 'UPDATE'){ //This is for update case 
				if(value == ''){
					if(previous_src != ''){
						$('#prv_img').attr('src',previous_src);
					}else{
						$("#preview_div").html(no_preview);	
					}
				}else{
					$('#prv_img').attr('src',previous_src);
				}
			}else{
				$("#preview_div").html(no_preview);
			}
		}else{
			
			$('#preview_div').html('<img id="prv_img">');
			var reader = new FileReader();
			reader.onload = function (e) {
				// get loaded data and render thumbnail
				$("#prv_img").attr('src',e.target.result);
			};
			// read the image file as a data URL.
			reader.readAsDataURL(ths.files[0]);
		}
	}	
}

function resetImg(){
	$("#preview_div").html(no_preview);
	document.getElementById("template_form").reset();
}


//------------------------------------- Category functions
//Sets values of selected category in the form
function set_category(obj){
	var target_elem = $(obj).find('span');
	var target_data = target_elem.data();
	
	$('#add_edt_cat').find('.head span').text('Edit Category');
	$('#perform_btn').text('Update');
	
	$('#category_edit_id').val(target_data.id);
	
	if(target_data.name && target_data.name != ''){
		$('#manage_cat_name').val(target_data.name);
	}
	
	if(target_data.iportal && target_data.iportal > 0){
		$('#cat_iportal').prop('checked',true);
		$('#cat_iportal').val(target_data.iportal);
	}else{
		$('#cat_iportal').prop('checked',false);
	}
	
	if(target_data.check && target_data.check > 0){
		$('#cat_check_in').prop('checked',true);
		$('#cat_check_in').val(target_data.check);
	}else{
		$('#cat_check_in').prop('checked',false);
		$('#cat_check_in').val('');
	}
	
	if($('#reset_btn').hasClass('hide') == true){
		$('#reset_btn').removeClass('hide');	
	}
}

//Perform action on category
function edit_category(){
	if($('#manage_cat_name').val() == ''){
		fAlert('Please enter category name.');
		return false;
	}else{
		var form_data = $('#category_management').serialize()+'&modal_category=yes';
		$.ajax({
			url:js_array.ajax_url+'?current_tab='+js_array.cur_tab,
			type:'POST',
			data:form_data,
			dataType:'JSON',
			beforeSend:function(){
				top.show_loading_image('show');
			},
			success:function(response){
				top.alert_notification_show(response);
				$("#manage_categories").load(location.href + " #manage_categories > *");
				reset_modal();
			},
			complete:function(){
				top.show_loading_image('hide');
			}
		})
	}
}

//Checking category delete checkbox and deleting checked ones
function check_delete_stat(status,msg){
	if(!status){
		if($('#manage_categories').find('[type=checkbox]').is(':checked')){
			if($('#modal_del_btn').hasClass('hide') == true){
				$('#modal_del_btn').removeClass('hide');
			}
		}else{
			if($('#modal_del_btn').hasClass('hide') == false){
				$('#modal_del_btn').addClass('hide');
			}
		}
	}else{
		var str_ids = new Array;
		var cat_names = new Array;
		$('#manage_categories').find('[type=checkbox]').each(function(id,elem){
			if($(elem).is(':checked')){
				str_ids.push($(elem).val());
				
				var cat_name = $(elem).parent().parent().next('td').find('span:first').text();
				cat_names.push(cat_name);
			}
		});
		str_ids = str_ids.join(',');
		cat_names = cat_names.join(',<br />');
		
		var alert_msg = '';
		if(cat_names.length){
			alert_msg = 'Are you sure to delete the following categories ?<br /><small>'+cat_names+'</small>';
		}
		
		if(!msg){
			top.fancyConfirm(alert_msg,"Please Confirm","top.fmain.check_delete_stat('this',true)");
		}else{
			$.ajax({
				url:js_array.ajax_url+'?current_tab='+js_array.cur_tab,
				data:'modal_del=yes&modal_category=yes&ids='+str_ids,
				dataType:'JSON',
				type:'POST',
				success:function(response){
					if(response){
						top.alert_notification_show('Record Deleted successfully');
						$("#manage_categories").load(location.href + " #manage_categories > *");
						reset_modal();
					}
				}
			});
		}
	}
}

//Reset category form
function reset_modal(){
	$('#category_edit_id').val('');
	$('#add_edt_cat').find('.head span').text('ADD NEW CATEGORY');
	document.category_management.reset();
	$('#perform_btn').text('Add');
	if($('#reset_btn').hasClass('hide') == false){
		$('#reset_btn').addClass('hide');	
	}
	
	if($('#modal_del_btn').hasClass('hide') == false){
		$('#modal_del_btn').addClass('hide');	
	}
	$('#manage_categories').find('[type=checkbox]').prop('checked',false);
}


$(function(){
	$('.collapse').on('show.bs.collapse', function(){
		$(this).prev('.panel').find(".glyphicon-menu-right").removeClass("glyphicon-menu-right").addClass("glyphicon-menu-down");
	}).on('hide.bs.collapse', function(){
		$(this).prev('.panel').find(".glyphicon-menu-down").removeClass("glyphicon-menu-down").addClass("glyphicon-menu-right");
	});
	
	$('.modal:not(#dxModal)').on('hide.bs.modal',function(){
		window.location.href = js_array.base_path+"/index.php?showpage="+js_array.cur_tab;
	});
});

//Bind Logo replace code with all ck editor instances
function bindLogoReplace(editor){
	editor.on('instanceReady', function(ev) {
		var content = ev.editor.getData();
		$.each(js_array.logo_urls,function(id,val){
			var img_url = '<img src="'+js_array.logo_urls[id]+'" />';
			//if(content.search(id) >= 0){
				content = content.replace(id, img_url);
			//}
		});
		ev.editor.setData(content); 
		
		
		ev.editor.on('afterPaste', function(evt) {
			var content = ev.editor.getData();
			$.each(js_array.logo_urls,function(id,val){
				var img_url = '<img src="'+js_array.logo_urls[id]+'" />';
				//if(content.search(id) >= 0){
					content = content.replace(id, img_url);
				//}
			});
			ev.editor.setData(content); 
		});
	});
}

$(document).ready(function(){
	var editor = '';
	var txt_area_nm = get_textarea_nm();
	
	if(txt_area_nm.length){
		if(typeof(txt_area_nm)!= 'object'){
			CKEDITOR.replace( ''+txt_area_nm+'', { width:'100%'} );
			editor = CKEDITOR.instances[''+txt_area_nm+''];
			bindLogoReplace(editor);
		} else if(typeof(txt_area_nm)== 'object'){
			$.each(txt_area_nm,function(id,val){
				CKEDITOR.replace( ''+val+'', { width:'100%'} );
				editor = CKEDITOR.instances[''+val+''];
				bindLogoReplace(editor);
			});		
		}
	}
	
	$('.variable_list li span').mouseover(function (){
		var text = $(this).text();
		var $this = $(this);
		var $input = $('<input type=text readonly>');
		$input.prop('value', text);
		$input.appendTo($this.parent());
		$input.focus();
		$input.select();
		$this.hide();
		$input.focusout(function(){
			$this.show();
			$input.remove();
		});
	});
	
	set_init('document');	//Trigger functions defined in it at document rerady
	
	set_header_title(js_array.header_title);
	var ar = get_footer_btns(js_array.cur_tab);
	top.btn_show("ADMN",ar);
	check_checkboxes();
	$('[data-toggle="tooltip"]').tooltip(); 
	
	$('body').on('shown.bs.modal','#cat_show_modal',function(){
		top.fmain.set_modal_height('cat_show_modal');
	});
	
	$('body').on('shown.bs.modal','#template_preview_modal',function(){
		top.fmain.set_modal_height('template_preview_modal');
	});
	
	top.show_loading_image('hide');
});

function setCheckSingleAction(obj1,obj2){
	if($("#"+obj1)){
		$("#"+obj1).attr("checked",true);
	}
	if($("#"+obj2)){
		$("#"+obj2).attr("checked",false);
	}
}

//To show multiple Dx codes in modal
function getDxValues(obj){
	var dataArr = $(obj).data();
	var optStr = '';
	var optStrSelected = '';
	
	var modal = $(dataArr.modal);
	
	if(js_array.icd10Arr){
		$.each(JSON.parse(js_array.icd10Arr), function(id,val){
			if(val != '') optStr += '<option value="'+val+'">'+val+'</option>';
		});	
	}

	if(js_array.icd10ArrSelected){
		$.each(JSON.parse(js_array.icd10ArrSelected), function(id,val){
			if(val != '') optStrSelected += '<option value="'+val+'">'+val+'</option>';
		});	
	}
	
	if(optStr.trim()){
		modal.find('#sourceEle').html(optStr);
		if(optStrSelected != '' && optStrSelected.trim()) {
			modal.find('#targetEle').html(optStrSelected);
		}
		modal.modal('show');
	}
	
	modal.on('show.bs.modal', function(){
		// $('#targetEle option').remove();
		$('#sourceEle option').prop('disabled', false);
	});
	
	console.log(dataArr.element);

	$('#HideDxModal').data('element', dataArr.element);
}

//To move values from target element to source element
function moveValues(obj){
	var dataArr = $(obj).data();
	var sourceDiv = '';
	var targetDiv = '';
	
	var size = dataArr.size;
	switch(size){
		case 1:
			switch(dataArr.direction){
				case 'target':
					targetDiv = $('#targetEle');
					sourceDiv = $('#sourceEle');
					
					var optStr = '';
					sourceDiv.find('option:selected').each(function(id, elem){
						var value = $(elem).val();
						optStr += '<option value="'+value+'" selected>'+value+'</option>';
						$(elem).prop('disabled', true);	
					});	
					if(optStr){
						targetDiv.append(optStr);
					}
					
				break;
				
				case 'source':
					targetDiv = $('#sourceEle');
					sourceDiv = $('#targetEle');
					
					sourceDiv.find('option:selected').each(function(id, elem){
						var value = $(elem).val();
						targetDiv.find('option[value^="'+value+'"]').prop('disabled', false);
						$(elem).remove();
					});
					
				break;	
			}
			
		break;
		
		default:
			switch(dataArr.direction){
				case 'target':
					targetDiv = $('#targetEle');
					sourceDiv = $('#sourceEle');
					
					var optStr = '';
					sourceDiv.find('option').each(function(id, elem){
						var value = $(elem).val();
						optStr += '<option value="'+value+'" selected>'+value+'</option>';
						$(elem).prop('disabled', true);	
					});	
					if(optStr) targetDiv.append(optStr);
					
				break;
				
				case 'source':
					targetDiv = $('#sourceEle');
					sourceDiv = $('#targetEle');
					
					sourceDiv.find('option').each(function(id, elem){
						var value = $(elem).val();
						targetDiv.find('option[value^="'+value+'"]').prop('disabled', false);
						$(elem).remove();
					});
				break;	
			}
	}
}

//Hides the Dx Modal and append values to the 
function hideDxModal(obj){
	var mainElem = $($(obj).data('element'));
	
	var valArr = new Array;
	var codeStr = '';
	
	// $('#targetEle option:selected').each(function(id, elem){
	$('#targetEle option').each(function(id, elem){
		var value = $(elem).val();
		valArr.push(value);
	});
	
	if(valArr.length) codeStr = valArr.join('\r\n');
	// if(mainElem.val() != '') codeStr = '\r\n'+codeStr;
	// if(codeStr.length) mainElem.append(codeStr);
	if(codeStr.length) mainElem.text(codeStr);
	
	$('#dxModal').modal('hide');
}


$(window).load(function(){
	set_init('window');
});