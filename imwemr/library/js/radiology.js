
// JS Functions to be used in Medical Hx -> Radiology Tab

	function addNewRow(cnt){
		$('#lab_display_table').css('opacity','0.4');
		var preCnt = cnt;
		
		var imgObj = $("#add_row_"+cnt);
		imgObj.attr('title','Delete Row');
		imgObj.attr('class','glyphicon glyphicon-remove');
		imgObj.removeAttr('onclick');
		imgObj.attr('onclick','removeTableRow(\'\',\''+preCnt+'\')');
		imgObj.attr('id','');
		
		cnt++;
		
		var row_val = '<div class="row">';
		
		//Test name Field
		row_val += '<div class="col-sm-2">';	
		row_val += 	'<label for="rad_name'+cnt+'">Test Name</label>';
		row_val += 	'<input type="text" id="rad_name'+cnt+'" tabindex="'+cnt+'" name="rad_name'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value=""  class="form-control">';
		row_val += '</div>';
		
		// Type Dropdown
		row_val += '<div class="col-sm-2" id="rad_type_td_'+cnt+'" >';
		row_val += 	'<label for="rad_type'+cnt+'">Type</label>';	
		row_val += 	'<select id="rad_type'+cnt+'" tabindex="'+cnt+'" name="rad_type'+cnt+'" class="selectpicker" data-width="100%" data-size="5" onChange="chk_change(\'\',this,event); getOtherType(this,\''+cnt+'\');">"'+global_js_var.DOM_RAD_TYPE+'"</select>';
		row_val += '</div>';
		
		// Type Other Field
		row_val += '<div class="col-sm-2 hide" id="rad_type_td_other_'+cnt+'">';
		row_val += 	'<label for="rad_type_other'+cnt+'">Other</label>';
		row_val += 	'<div class="input-group">';
		row_val +=		'<input type="text" id="rad_type_other'+cnt+'" tabindex="'+cnt+'" name="rad_type_other'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control"  onBlur="getOtherType(this,\''+cnt+'\');">';
		row_val += 		'<label for="" class="input-group-addon" onClick="toggle_other_dropdown(\'rad_type_td_other_'+cnt+'','rad_type_td_'+cnt+'\')">';	
		row_val +=			'<span class="glyphicon glyphicon glyphicon-arrow-left"></span>';
		row_val += 		'</label>'
		row_val += 	'</div>';
		row_val += '</div>'
		
		//Loinc Text Field
		row_val += '<div class="col-sm-2">';	
		row_val += 	'<label for="rad_loinc'+cnt+'">LOINC</label>';
		row_val += 	'<input type="text" id="rad_loinc'+cnt+'" tabindex="'+cnt+'" name="rad_loinc'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control" >';
		row_val += '</div>';	
		
		//Contact Name Field
		row_val += '<div class="col-sm-2">';
		row_val += 	'<label for="rad_fac_name'+cnt+'">Contact Name</label>'
		row_val += 	'<input type="text" id="rad_fac_name'+cnt+'" tabindex="'+cnt+'" name="rad_fac_name'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control">';
		row_val += '</div>';
		
		//Contact Address Field
		row_val += '<div class="col-sm-2">';
		row_val += 	'<label for="rad_address'+cnt+'">Contact Address</label>';
		row_val += 	'<textarea id="rad_address'+cnt+'" tabindex="'+cnt+'" name="rad_address'+cnt+'" class="form-control" rows="1" onKeyUp="chk_change(\'\',this,event);"></textarea>';	
		row_val += '</div>';
		
		//Indication Field
		row_val += '<div class="col-sm-2">';
		row_val += 		'<div class="row">';
		row_val += 			'<div class="col-sm-10">';
		row_val += 				'<label for="rad_indication'+cnt+'">Indication</label>';	
		row_val += 	'<input type="text" id="rad_indication'+cnt+'" tabindex="'+cnt+'" name="rad_indication'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control">';	
		row_val += 			'</div>';
		row_val += 			'<div class="col-sm-2">';	
		row_val += 			'</div>';	
		row_val += 		'</div>';
		row_val += '</div>';
		
		//Result Field
		row_val += '</div>'; 
		row_val += '<div class="row pt10">';
		row_val += 	'<div class="col-sm-2">';
		row_val += 		'<label for="rad_results'+cnt+'">Results</label>';
		row_val += 		'<input type="text" id="rad_results'+cnt+'" tabindex="'+cnt+'" name="rad_results'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control">';
		row_val += 	'</div>';
		
		//Order Date
		row_val += '<div class="col-sm-1">';
		row_val += 	'<label for="rad_order_date'+cnt+'">Order Date</label>';
		row_val +=	'<div class="input-group">';
		row_val +=		'<input type="text" id="rad_order_date'+cnt+'" tabindex="'+cnt+'" name="rad_order_date'+cnt+'" onKeyUp="chk_change(\'\',this,event);" onChange="chk_change(\'\',this,event);" value="" maxlength="10" class="datepicker form-control order_dt_rad" title="<?php echo $date_format; ?>">';	
		row_val += 		'<label for="rad_order_date'+cnt+'" class="input-group-addon">';
		row_val += 			'<span class="glyphicon glyphicon-calendar"></span>';
		row_val += 		'</label>';
		row_val += '</div>';
		row_val += '</div>';
		
		//Order Time
		row_val += 	'<div class="col-sm-1">';
		row_val += 		'<label for="rad_order_time'+cnt+'">Order Time</label>';
		row_val += 		'<input type="text" id="rad_order_time'+cnt+'" tabindex="'+cnt+'" name="rad_order_time'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control">';
		row_val += 	'</div>';
		
		//Result Date
		row_val += '<div class="col-sm-1">';
		row_val += 	'<label for="rad_results_date'+cnt+'">Result Date</label>';
		row_val +=	'<div class="input-group">';
		row_val +=		'<input type="text" id="rad_results_date'+cnt+'" tabindex="'+cnt+'" name="rad_results_date'+cnt+'" onKeyUp="chk_change(\'\',this,event);" onChange="chk_change(\'\',this,event);" value="" maxlength="10" class="datepicker form-control order_dt_rad" title="<?php echo $date_format; ?>">';	
		row_val += 		'<label for="rad_results_date'+cnt+'" class="input-group-addon">';
		row_val += 			'<span class="glyphicon glyphicon-calendar"></span>';
		row_val += 		'</label>';
		row_val += '</div>';	
		row_val += '</div>';
			
		//Result Time
		row_val += 	'<div class="col-sm-1">';
		row_val += 		'<label for="rad_results_time'+cnt+'">Result Time</label>';
		row_val += 		'<input type="text" id="rad_results_time'+cnt+'" tabindex="'+cnt+'" name="rad_results_time'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control">';
		row_val += 	'</div>';
		
		//Status Dropdown
		row_val += '<div class="col-sm-2">';
		row_val += '<div class="row">';
		row_val += '<div class="col-sm-5">';
		row_val += 	'<label for="rad_status'+cnt+'">Status</label>';
		row_val += 	'<select name="rad_status'+cnt+'" id="rad_status'+cnt+'" tabindex="'+cnt+'" onChange="chk_change(\'\',this,event);" class="selectpicker" data-width="100%" data-title="Select"><option value="1">Ordered</option><option value="2">Completed</option></select>';
		row_val += '</div>';
		row_val += '<div class="col-sm-7">';
		row_val += 	'<label for="rad_snowmed'+cnt+'">SNOWMED CT</label>';
		row_val += 	'<input type="text" id="rad_snowmed'+cnt+'" tabindex="'+cnt+'" name="rad_snowmed'+cnt+'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control">';
		row_val += '</div>';
		row_val += '</div>';
		row_val += '</div>';
		
		//Instruction Field
		row_val += '<div class="col-sm-2">';
		row_val += 	'<label for="rad_instuctions'+cnt+'">Instructions</label>';
		row_val += 	'<textarea id="rad_instuctions'+cnt+'" tabindex="'+cnt+'" name="rad_instuctions'+cnt+'" rows="1"  class="form-control" onKeyUp="chk_change(\'\',this,event);"></textarea>';
		row_val += '</div>';	
		
		row_val += '<div class="col-sm-2">';
		row_val += 	'<div class="row">';
		
		//Order By Dropdown
		row_val += 		'<div class="col-sm-7">';	
		row_val += 			'<label for="rad_order_by'+cnt+'">Order By</label>';
		row_val += 			'<select name="rad_order_by'+cnt+'" id="rad_order_by'+cnt+'" tabindex="'+cnt+'" onChange="chk_change(\'\',this,event);" class="selectpicker" data-width="100%" data-size="5">'+phy_opt+'</select>'
		row_val += 		'</div>';
			
		row_val += 	'<div class="col-sm-3 text-center">';		
		row_val += 	'<label for="refusal'+cnt+'">Refusal</label>';	
		row_val +=  '<div class="checkbox">';
		row_val +=  '<input type="checkbox" class="checkbox" name="refusal'+cnt+'" id="refusal'+cnt+'" value="1" tabindex="'+cnt+'" onChange="check_refusal('+cnt+');">';
		row_val +=  '<label for="refusal'+cnt+'"></label>';
		row_val +=  '</div>';
		row_val += 	'</div>';
		
		//Add row icon	
		row_val += 		'<div class="col-sm-2 text-center">';
		row_val += 			'<label class="blank_label">&nbsp;</label>';
		row_val += 			'<span id="add_row_'+cnt+'" class="glyphicon glyphicon-plus pointer" onClick="addNewRow(\''+cnt+'\');" ></span>';
		row_val += 		'</div>';
		row_val += 	'</div>';
		row_val += '</div>';
		
		row_val += '</div>';
			
		var new_row = "<div id='rad_tr"+cnt+"' class='col-sm-12 whitebox bordbx'>" + row_val + "</div>";
		$('#lab_display_table').css('opacity','1');
		$("#lab_display_table").last().append(new_row);
		
		$('.selectpicker').selectpicker('refresh');
		$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
		
		$('#rad_name'+cnt).typeahead({source:[global_js_var.typeahead_title_arr]});
		$("#rad_name"+cnt).focus();
		$("#last_cnt").val(cnt);
	}
	
	function deleteRow(id,cnt){
		if(id != ''){
			window.top.show_loading_image('show');
			var webroot = global_js_var.global_path;
			var url = webroot+"/interface/Medical_history/index.php?showpage=radiology&mode=delete&del_id="+id;
			$.ajax({
				type:'POST',
				url:url,
				success:function(response){
					if(response && response != 0){
						window.top.show_loading_image('hide');
						$('#rad_fac_name'+cnt).val('');
						$('#rad_name'+cnt).val('');
						$('#rad_tr'+cnt).fadeOut('fast');
						return false;
					}
				}
			});
		}
	}
	
	function removeTableRow(id,cnt){
		if(id == ''){
			$('#rad_fac_name'+cnt).val('');
			$('#rad_name'+cnt).val('');
			$('#rad_tr'+cnt).fadeOut('fast');
		}else if(id !='' && typeof(id) != 'undefined'){
			top.fancyConfirm(global_js_var.alert_arr.delete,"",'top.fmain.deleteRow('+id+','+cnt+')','');
		}
	}
	
	function getOtherType(obj,cnt){
		var dropDis = 'inline';
		var txtDis = 'none';
		var obj = $(obj);
		if(obj.val() == 'Other'){
			$('#rad_type_other'+cnt).val('');
			$('#rad_type_td_'+cnt).addClass('hide');
			if($('#rad_type_td_other_'+cnt).hasClass('hide')){
				$('#rad_type_td_other_'+cnt).removeClass('hide');
			}
			$('#rad_type_other'+cnt).focus();
		}else if(obj.id == 'rad_type_other'+cnt){
			var txtVal = $('#rad_type_other'+cnt).val();			
			if(txtVal != ''){
				var dropDis = 'none';
				var txtDis = 'inline';
			}
			else{
				$('#rad_type'+cnt).val('').selectpicker('refresh');
			}
			if($('#rad_type_td_'+cnt).hasClass('hide') === false){
				$('#rad_type_td_'+cnt).addClass('hide');
			}
			$('#rad_type_td_other_'+cnt).css('display',txtDis);	
		}
	}
	
	function toggle_other_dropdown(other_div_id,main_dropdown_id){
		var other_obj = $('#'+other_div_id+'');
		var main_drop_obj = $('#'+main_dropdown_id+'');
		
		if(other_obj.is(':visible') === true){
			other_obj.addClass('hide');
			other_obj.css('display','');
		}
		
		main_drop_obj.val('').selectpicker('refresh');
		if(main_drop_obj.hasClass('hide') === true || main_drop_obj.is(':visible') === true){
			main_drop_obj.removeClass('hide');
		}
	}
	
	function form_submit(){
		document.save_rad_test_form.action = 'index.php?showpage=radiology';
		document.save_rad_test_form.submit();
	}
	
	//btns --- 
	top.btn_show("RAD");
	
	top.show_loading_image('hide');
	
	function openScan(lab_id){
		top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/Scan/view_scan_images.php?scanOrUpload=upload&upload_from=radiology&lab_id='+lab_id);
	}
	
	$("body").on('change', '.checkbox',function() {
	if(this.checked) {
		var row_id =   this.id;
			$("#myModal").modal('show');
			$("#rowID").val(row_id);	
		}
	});
	function check_refusal(id){
		$("#refusal_row").val(id);
		$('#refusal_reason').val($("#refusal_reason"+id).val());
		$('#refusal_snomed').val($("#refusal_snomed"+id).val());
	}
	function check_refusal_values(){
		var ID = $("#refusal_row").val();
		var refusal_reason	= $('#refusal_reason').val();
		var refusal_snomed	= $('#refusal_snomed').val();
		if(refusal_snomed != "" && ID != ""){
			$("#refusal"+ID).val(1);
			$("#refusal_reason"+ID).val(refusal_reason); 
			$("#refusal_snomed"+ID).val(refusal_snomed); 
		}
		$("#myModal").modal('hide');
	}