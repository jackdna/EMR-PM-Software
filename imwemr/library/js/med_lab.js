/*************************** common func() ***************************/

var global_arr = {};

//Setting Typeahead arr to use 
function set_typeahead_arr(){
	var url = top.JS_WEB_ROOT_PATH+"/interface/Medical_history/lab/ajax/ajax_handler.php?get_typeahead_arr=yes";
	$.ajax({
		url:url,
		type:'GET',
		dataType: 'JSON',
		success:function(response){
			var result = response;
			global_arr.provider_arr = result.provider_arr;
			global_arr.loinc_arr = result.loinc_arr;
			global_arr.loinc_val_arr = result.loinc_val_arr;
			global_arr.abnormal_flag = result.get_abnormal_flag;
			global_arr.samples = result.samples;
			global_arr.specimens = result.specimens;
		}
	});
}	

function open_window(val){
	window.open("http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c="+val+"&mainSearchCriteria.v.cs=2.16.840.1.113883.6.1&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en","Lab","height=700,width=1000,top=50,left=50,scrollbars=yes");
}


/*************************** Index Page func() ***************************/

function new_lab_order(lab_id,source){
	if($("#div_disable").hasClass('hide') === true){
		$("#div_disable").removeClass('hide');
	}
	var lab_test_id = lab_id;
	if(typeof(source)=='undefined'){
		//url = 'new_lab_order.php?lab_test_id='+lab_test_id;
		top.show_loading_image('show');
		get_modal(lab_id);
	}else if(typeof(source)=='string' && source=='hl7imported'){
		url = '../hl7stage2.php?task=Import&for=LAB&hl7mu_id='+lab_test_id;
	}
	//$('#lab_hl7_upload_div #popup_title').text('LAB ORDER');
	//$('#lab_im_ex_iframe').attr('src',url);
	//$('#lab_hl7_upload_div').show();
}


//Get modal to show
function get_modal(lab_id){
	var url = top.JS_WEB_ROOT_PATH+"/interface/Medical_history/lab/ajax/ajax_handler.php?lab_test_id="+lab_id+"&get_modal=yes";
	$.ajax({
		type:'GET',
		url:url,
		success:function(response){
			$('#modal_show_cont').html(response);
			$('#myModal').modal({
				backdrop: 'static',
				keyboard: false
			}).modal('show');
			top.show_loading_image('hide');
			//Provider Typeahead
			var phyTypeAhead = $('#order_by').typeahead({source:global_arr.provider_arr.phyName});
			phyTypeAhead.data('typeahead').updater = function(item){
				var phyId = global_arr.provider_arr.phyId[item];
				if(phyId) $('input[name=order_by_prov_id]').val(phyId);

				return item;
			};
			
			//Observation Typeahead
			$('[id^=observation_]').each(function(id,elem){
				$(elem).typeahead({source:global_arr.loinc_arr});
			});
			
			//Service Typeahead
			$('[id^=service_]').each(function(id,elem){
				$(elem).typeahead({source:global_arr.loinc_arr});
			});
			
			//Specimens Typeahead
			$('[id^=collection_type_]').each(function(id,elem){
				$(elem).typeahead({
					source:global_arr.specimens,
					onSelect: function(obj) { 
						// console.log(obj);
						$('[id^=hidden_collection_type_]').val(obj.value);
					}
				});
			});
			
			//Samples Typeahead
			$('[id^=smp_collection_type_]').each(function(id,elem){
				$(elem).typeahead({
					source:global_arr.samples,
					onSelect: function(obj) { 
						// console.log(obj);
						$('[id^=hidden_smp_collection_type_]').val(obj.value);
					}
				});
			});
			
			$('.selectpicker').selectpicker('refresh');
			$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false,validateOnBlur:true});
			
			$('#myModal').on('hide.bs.modal',function(e){
				if($("#div_disable").hasClass('hide') === false){
					$('#div_disable').addClass('hide');
				}
				window.location.href = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/index.php?showpage=lab';
				
			});
		}
	})
}

//Delete lab record
function delete_lab_rec(lab_id){
	var lab_order_val = $('#labOrder'+lab_id).val();
	var url = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/lab/ajax/ajax_handler.php?del_lab_id='+lab_id+'&labOrder='+lab_order_val;
	$.ajax({
		url:url,
		type:'GET',
		success:function(response){
			var resp = $.trim(response);
			if(resp != '' && resp > 0){
				top.alert_notification_show('Record deleted successfully');
				$('#lab_order_'+lab_id).remove();
				$("#lab_record_content").load(location.href+" #lab_record_content>*");
			}
		}
	});
}



/*************************** Modal inside func() ***************************/

//Setting Loinc value
function fet_loinc_code(field_name,pro_cont){
	var loinc_arr_js = global_arr.loinc_val_arr;
	if(field_name=="result"){
		var observation = $('#observation_'+pro_cont).val();
		if(typeof(loinc_arr_js[observation])=="undefined"){
			$('#result_loinc_'+pro_cont).val("");
		}else{
			$('#result_loinc_'+pro_cont).val("");
			if(loinc_arr_js[observation]!=null){
				$('#result_loinc_'+pro_cont).val(loinc_arr_js[observation]);
			}
		}
	}
	if(field_name=="request"){
		var observation = $('#service_'+pro_cont).val();
		if(!loinc_arr_js[observation]){
			$('#loinc_'+pro_cont).val("");
		}else{
			$('#loinc_'+pro_cont).val(loinc_arr_js[observation]);
		}
	}
}

function scroll_to_last_row(id) {
    var wtf    = $('#'+id);
    var height = wtf[0].scrollHeight;
    wtf.scrollTop(height);
}
//Observation request row add
function ob_req_addrow(){
	var row_data = '';	
	var getRows = ($(".ob_req_countrow tr").size()-1);
	i = getRows+1;
	
	row_data +='<tr id="tr_c_'+i+'">';
	row_data +='<td><input type="text" class="form-control" name="service_'+i+'" id="service_'+i+'" value="" onChange="fet_loinc_code(\'request\','+i+');"></td>';
	row_data +='<td><input type="text" class="form-control" name="loinc_'+i+'" id="loinc_'+i+'" value=""></td>';
	row_data += '<td>';
	row_data += 	'<div class="row">';
	row_data += 		'<div class="col-sm-7">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="start_date_'+i+'" id="start_date_'+i+'" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField(\'start_date_'+i+'\', \'start_time_'+i+'\')" value=""/>';
	row_data += 				'<label for="start_date_'+i+'" class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 		'<div class="col-sm-5">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="start_time_'+i+'" id="start_time_'+i+'" class="form-control" onClick="getDate_and_setToField(\'start_date_'+i+'\', \'start_time_'+i+'\')" value=""/>';
	row_data += 				'<label for="start_time_'+i+'" class="input-group-addon"><span class="glyphicon glyphicon-time"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 	'</div>';
	row_data += '</td>';
	row_data += '<td>';
	row_data += 	'<div class="row">';
	row_data += 		'<div class="col-sm-7">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="end_date_'+i+'" id="end_date_'+i+'" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField(\'end_date_'+i+'\', \'end_time_'+i+'\')" value=""/>';
	row_data += 				'<label for="end_date_'+i+'" class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 		'<div class="col-sm-5">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="end_time_'+i+'" id="end_time_'+i+'" class="form-control" onClick="getDate_and_setToField(\'end_date_'+i+'\', \'end_time_'+i+'\')" value=""/>';
	row_data += 				'<label for="end_time_'+i+'" class="input-group-addon"><span class="glyphicon glyphicon-time"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 	'</div>';
	row_data += '</td>';
	row_data +='<td><input type="text" class="form-control" name="clinical_info_'+i+'" value=""></td>';
    if(!isDssEnable) {
        row_data +='<td class="text-center pointer"><span id="removebtn" class="glyphicon glyphicon-remove pointer" onClick="obser_removerow(\'request\');"></span></td>';
    }
	row_data +='</tr>';
	
	$("#tr_c_"+getRows).after(row_data); // ADD NEW ROW
	$("#request_cont").val(i);
	$('.selectpicker').selectpicker('refresh');
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false,validateOnBlur:true});
	$('#service_'+i).typeahead({source:global_arr.loinc_arr});
	if($('#tr_c_'+i)[0]){
        scroll_to_last_row('obser_req');
	}
}


//Observation specimen row add
function spm_addrow(){
	var row_data = '';
	var getRows = ($(".spm_countrow tr").size()-1);
	y = getRows+1;
	
	row_data +='<tr id="tr_b_'+y+'">';
	row_data +='<td><input type="text" class="form-control" name="collection_type_'+y+'" id="collection_type_'+y+'" value=""><input type="hidden" class="form-control" name="hidden_collection_type_'+y+'" id="hidden_collection_type_'+y+'" value=""></td>';
	row_data += '<td>';
	row_data += 	'<div class="row">';
	row_data += 		'<div class="col-sm-7">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="collection_start_date_'+y+'" id="collection_start_date_'+y+'" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField(\'collection_start_date_'+y+'\', \'collection_start_time_'+y+'\')" value=""/>';
	row_data += 				'<label for="collection_start_date_'+y+'" class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 		'<div class="col-sm-5">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="collection_start_time_'+y+'" id="collection_start_time_'+y+'" class="form-control" onClick="getDate_and_setToField(\'collection_start_date_'+y+'\', \'collection_start_time_'+y+'\')" value=""/>';
	row_data += 				'<label for="collection_start_time_'+y+'" class="input-group-addon"><span class="glyphicon glyphicon-time"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 	'</div>';
	row_data += '</td>';
	row_data += '<td>';
	row_data += 	'<div class="row">';
	row_data += 		'<div class="col-sm-7">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="collection_end_date_'+y+'" id="collection_end_date_'+y+'" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField(\'collection_end_date_'+y+'\', \'collection_end_time_'+y+'\')" value=""/>';
	row_data += 				'<label for="collection_end_date_'+y+'" class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 		'<div class="col-sm-5">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="collection_end_time_'+y+'" id="collection_end_time_'+y+'" class="form-control" onClick="getDate_and_setToField(\'collection_end_date_'+y+'\', \'collection_end_time_'+y+'\')" value=""/>';
	row_data += 				'<label for="collection_end_time_'+y+'" class="input-group-addon"><span class="glyphicon glyphicon-time"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 	'</div>';
	row_data += '</td>';
	row_data +='<td><input type="text" class="form-control" name="collection_condition_'+y+'" value=""></td>';
	row_data +='<td><input type="text" class="form-control" name="collection_rejection_'+y+'" value=""></td>';
	row_data +='<td><input type="text" class="form-control" name="collection_comments_'+y+'" value=""></td>';
    if(!isDssEnable) {
        row_data +='<td class="text-center pointer"><span id="removebtn" class="glyphicon glyphicon-remove pointer" onClick="obser_removerow(\'spm\');" ></span></td>';
    }
	row_data +='</tr>';
	
	$("#tr_b_"+getRows).after(row_data); // ADD NEW ROW
	$("#specimen_cont").val(y);
	$('.selectpicker').selectpicker('refresh');
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false,validateOnBlur:true});
	$('#collection_type_'+y).typeahead({
		source:global_arr.specimens,
		onSelect: function(obj) { 
			// console.log(obj);
			$('#hidden_collection_type_'+y).val(obj.value);
		}
	});
	if($('#tr_b_'+y)[0]){
		scroll_to_last_row('obser_specimen_bl');
	}
}


//Observation sample row add
function smp_addrow(){
	var row_data = '';
	var getRows = ($(".smp_countrow tr").size()-1);
	y = getRows+1;
	
	row_data +='<tr id="tr_s_'+y+'">';
	row_data +='<td><input type="text" class="form-control" name="smp_collection_type_'+y+'" id="smp_collection_type_'+y+'" value=""><input type="hidden" class="form-control" name="hidden_smp_collection_type_'+y+'" id="hidden_smp_collection_type_'+y+'" value=""></td>';
	row_data += '<td>';
	row_data += 	'<div class="row">';
	row_data += 		'<div class="col-sm-7">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="sample_start_date_'+y+'" id="sample_start_date_'+y+'" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField(\'sample_start_date_'+y+'\', \'sample_start_date_'+y+'\')" value=""/>';
	row_data += 				'<label for="sample_start_date_'+y+'" class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 		'<div class="col-sm-5">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="sample_start_time_'+y+'" id="sample_start_time_'+y+'" class="form-control" onClick="getDate_and_setToField(\'sample_start_date_'+y+'\', \'sample_start_time_'+y+'\')" value=""/>';
	row_data += 				'<label for="sample_start_time_'+y+'" class="input-group-addon"><span class="glyphicon glyphicon-time"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 	'</div>';
	row_data += '</td>';
	row_data += '<td>';
	row_data += 	'<div class="row">';
	row_data += 		'<div class="col-sm-7">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="sample_end_date_'+y+'" id="sample_end_date_'+y+'" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField(\'sample_end_date_'+y+'\', \'sample_end_date_'+y+'\')" value=""/>';
	row_data += 				'<label for="sample_end_date_'+y+'" class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 		'<div class="col-sm-5">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="sample_end_time_'+y+'" id="sample_end_time_'+y+'" class="form-control" onClick="getDate_and_setToField(\'sample_end_time_'+y+'\', \'sample_end_time_'+y+'\')" value=""/>';
	row_data += 				'<label for="sample_end_time_'+y+'" class="input-group-addon"><span class="glyphicon glyphicon-time"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 	'</div>';
	row_data += '</td>';
	row_data +='<td><input type="text" class="form-control" name="sample_condition_'+y+'" value=""></td>';
	row_data +='<td><input type="text" class="form-control" name="sample_rejection_'+y+'" value=""></td>';
	row_data +='<td><input type="text" class="form-control" name="sample_comments_'+y+'" value=""></td>';
    if(!isDssEnable) {
        row_data +='<td class="text-center pointer"><span id="smp_removebtn" class="glyphicon glyphicon-remove pointer" onClick="obser_removerow(\'smp\');" ></span></td>';
    }
	row_data +='</tr>';
	
	$("#tr_s_"+getRows).after(row_data); // ADD NEW ROW
	$("#sample_cont").val(y);
	$('.selectpicker').selectpicker('refresh');
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false,validateOnBlur:true});
	$('#smp_collection_type_'+y).typeahead({
		source:global_arr.samples,
		onSelect: function(obj) { 
			// console.log(obj);
			$('#hidden_smp_collection_type_'+y).val(obj.value);
		}
	});
	if($('#tr_s_'+y)[0]){
		scroll_to_last_row('obser_sample_bl');
	}
}

//Observation result row add
function ob_res_addrow(){
	var row_data = '';
	var getRows = ($(".ob_res_countrow tr").size()-1);
	var count = (getRows+1);
	
	var abnormal_flag_arr = global_arr.abnormal_flag;
	var abnormal_drop_down = '';
	$.each(abnormal_flag_arr,function(id,val){
		abnormal_drop_down += '<option value="'+id+'">'+val+'</option>';
	});
	
	row_data += '<tr id="tr_d_'+count+'">';
	row_data +=	'<td style="width:10%"><input type="text" name="observation_'+count+'" id="observation_'+count+'" value="" onChange="fet_loinc_code(\'result\','+count+');" class="form-control"></td>';
	row_data += '<td><input type="text" class="form-control" name="result_loinc_'+count+'" id="result_loinc_'+count+'" value=""></td>';
	row_data += '<td><input type="text" class="form-control" name="result_'+count+'" value=""></td>';
	row_data += '<td><input type="text" class="form-control" name="uom_'+count+'" value=""></td>';
	row_data += '<td><input type="text" class="form-control" name="result_range_'+count+'" value=""></td>';
	row_data += '<td><select class="selectpicker" data-width="100%" data-size="5" name="abnormal_flag_'+count+'" data-title="Select"  data-container="#selectpicker_cont">'+abnormal_drop_down+'</select></td>';
	row_data += '<td><input type="text" class="form-control" name="status_'+count+'" value=""></td>';
	row_data += '<td>';
	row_data += 	'<div class="row">';
	row_data += 		'<div class="col-sm-7">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="result_date_'+count+'" id="result_date_'+count+'" class="datepicker form-control" onBlur="top.checkdate(this)" onClick="getDate_and_setToField(\'result_date_'+count+'\', \'result_time_'+count+'\')" value=""/>';
	row_data += 				'<label for="result_date_'+count+'" class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 		'<div class="col-sm-5">';
	row_data += 			'<div class="input-group">';
	row_data += 				'<input type="text" name="result_time_'+count+'" id="result_time_'+count+'" class="form-control" onClick="getDate_and_setToField(\'result_date_'+count+'\', \'result_time_'+count+'\')" value=""/>';
	row_data += 				'<label for="result_time_'+count+'" class="input-group-addon"><span class="glyphicon glyphicon-time"></span></label>';
	row_data += 			'</div>';
	row_data += 		'</div>';
	row_data += 	'</div>';
	row_data += '</td>';	
	row_data += '<td><input type="text" class="form-control" name="result_comments_'+count+'" value=""></td>';
    if(!isDssEnable) {
        row_data += '<td class="text-center"><span id="removebtn" class="glyphicon glyphicon-remove pointer" onClick="obser_removerow(\'result\')"></span></td>';
    }
	row_data += '</tr>';

	$("#tr_d_"+getRows).after(row_data); // ADD NEW ROW
	$("#result_cont").val(count);
	$('.selectpicker').selectpicker('refresh');
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false,validateOnBlur:true});
	$('#observation_'+count).typeahead({source:global_arr.loinc_arr});
	if($('#tr_d_'+count)[0]){
		scroll_to_last_row('observ_result_bl');
	}
}

//Remove observation rows
function obser_removerow(request)
{
	var obser_remove_row = '';
	if(request == 'result'){
		if($("#result_cont").val() > 1){
			obser_remove_row = ($(".ob_res_countrow tr").size()-1);
			$("#tr_d_"+obser_remove_row).remove();
		}
		$("#result_cont").val(obser_remove_row-1);
		
	}else if(request == 'request'){
		if($("#request_cont").val() > 1){
			obser_remove_row = ($(".ob_req_countrow tr").size()-1);
			$("#tr_c_"+obser_remove_row).remove();
		}
		$("#request_cont").val(obser_remove_row-1);
		
	}else if(request == 'spm'){	//Specimen
		if($("#specimen_cont").val() > 1){
			obser_remove_row = ($(".spm_countrow tr").size()-1);
			$("#tr_b_"+obser_remove_row).remove();
		}
		$("#specimen_cont").val(obser_remove_row-1);
	}else if(request == 'smp'){	//Sample
		if($("#sample_cont").val() > 1){
			obser_remove_row = ($(".smp_countrow tr").size()-1);
			$("#tr_s_"+obser_remove_row).remove();
		}
		$("#sample_cont").val(obser_remove_row-1);
	}
}

//Delete obseravtion records
function del_test(id,action,lab_test_id){
	var url = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/lab/ajax/ajax_handler.php?';
	if(id != ""){
		if(action == "request"){
			url += 'lab_test_id='+lab_test_id+'&del_request_id='+id+'&action='+action;
		}else if(action=="specimen"){
			url += 'lab_test_id='+lab_test_id+'&del_specimen_id='+id+'&action='+action;
		}else if(action=="sample"){
			url += 'lab_test_id='+lab_test_id+'&del_sample_id='+id+'&action='+action;
		}else if(action == "result"){
			url += 'lab_test_id='+lab_test_id+'&del_result_id='+id+'&action='+action;
		}
		$.ajax({
			url:url,
			type:'GET',
			success:function(response){
				if($.trim(response) > 0){
					fAlert('Record deleted successfully');
					window.location.href = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/index.php?showpage=lab';
				}
			}
		});
	} 
}






$(function(){
	set_typeahead_arr();
});

function openScan(lab_id){
window.open('../view_scan_images.php?scanOrUpload=upload&upload_from=lab&lab_id='+lab_id,'lab_scan','width=670,height=550');
}

function validateLabOrder() {
	var service = top.fmain.$('#service_1').val();
	var sample = top.fmain.$('#smp_collection_type_1').val();
	var specimen = top.fmain.$('#collection_type_1').val();

	if((service == '') || (sample == '') || (specimen == '')){
		top.fAlert('Please select Service, Sample and Specimen.');
		return false;
	} else {
		document.forms["frm_sub"].submit();
	}
	return false;
}