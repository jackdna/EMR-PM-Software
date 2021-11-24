var old_unit7val = '';
var bmi_drop_down = '';
var bp_drop_down = '';

// BP data dropdown
$.each(global_js_arr.bp_drop_data,function(id,val){
	bp_drop_down += '<option value="'+val+'">'+val+'</option>';
});

// BMI data dropdown
$.each(global_js_arr.bmi_drop_data,function(id,val){
	bmi_drop_down += '<option value="'+val+'">'+val+'</option>';
});



function open_window(val){
	window.open("http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c="+val+"&mainSearchCriteria.v.cs=2.16.840.1.113883.6.1&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en","Lab","height=700,width=1000,top=50,left=50,scrollbars=yes");
}

function calculateBmi(row_index){
	var height 			= $("#new_range7_"+row_index).val();
	var height_unit		= $("#new_unit7_"+row_index).val();
	if(height_unit!='inch'){ 
		height		= convertUnit(height,height_unit,'inch');
	}
	var weight 			= $("#new_range8_"+row_index).val();
	var weight_unit		= $("#new_unit8_"+row_index).val();
	if(weight_unit!='lbs'){ 
		weight			= convertUnit(weight,weight_unit,'lbs',row_index);
	}
	if(weight > 0 && height > 0){	
		var finalBmi = weight/(height/100*height/100);  
		finalBmi = cal_bmi(weight, height);
		$("#new_range9_"+row_index).val(finalBmi);
	}	
}

function cal_bmi(lbs, ins)
{
   h2 = ins * ins;
   bmi = lbs/h2 * 703
   f_bmi = Math.floor(bmi);
   diff  = bmi - f_bmi;
   diff = diff * 10;
   diff = Math.round(diff);

   if (diff == 10)    // Need to bump up the whole thing instead
   {
      f_bmi += 1;
      diff   = 0;
   }
   bmi = f_bmi + "." + diff;
   return bmi;
}

function update_old_unit(val){
	old_unit7val = val;
}

function convertUnit(val,fromUnit,ToUnit){
	converted_value = ''; 
	if(val!=''){ 
		if(fromUnit=='lbs' && ToUnit=='kg'){
			converted_value = (val * 0.4536);
		}else if(fromUnit=='kg' && ToUnit=='lbs'){
			converted_value = (val * 2.2046);			
		}else if(fromUnit=='cm' && ToUnit=='m'){
			converted_value = (val/100);			
		}else if(fromUnit=='cm' && ToUnit=='inch'){
			converted_value = (val * 0.3937);			
		}else if(fromUnit=='m' && ToUnit=='cm'){
			converted_value = (val * 100);			
		}else if(fromUnit=='m' && ToUnit=='inch'){
			converted_value = (val * 39.37);			
		}else if(fromUnit=='inch' && ToUnit=='cm'){
			converted_value = (val/0.3937);			
		}else if(fromUnit=='inch' && ToUnit=='m'){
			converted_value = (val/39.37);			
		}
	}
	return converted_value;	
}

function convert_height_weight(row_index,new_unit){ 
	calculateBmi(row_index);
	old_unit7val = new_unit;
}

function isNumeric(row_index,range_index){
	var isNotnumber = $('#new_range'+range_index+'_'+row_index).val();
	if(isNaN(isNotnumber)){ 
		fAlert('Please enter the numeric value only');
		$('#new_range'+range_index+'_'+row_index).val('');
	}	
}

function show_next(cnt){
	var pre_cnt = cnt;
	
	var imgObj = $("#add_row_"+cnt);
	imgObj.attr('title','Delete Row');
	imgObj.attr('class','glyphicon glyphicon-remove');
	imgObj.removeAttr('onclick');
	imgObj.attr('onclick','removeTableRow(\'\',\''+pre_cnt+'\')');
	imgObj.attr('id','');
	cnt++;
	
	var td_class = (cnt%2) == 0 ? 'bgcolor' : '';
	var cnt1 = 1;
	var td_val = '';		
		td_val += '<td>';
		td_val += 	'<div class="input-group">';
		td_val += 		'<input type="text" class="datepicker form-control vs_dt" name="new_range_dat'+cnt+'" id="new_range_dat'+cnt+'" onKeyUp="top.fmain.chk_change(this.value,this,event)" onChange="top.fmain.chk_change(this.value,this,event)">';
		td_val += 		'<label for="new_range_dat'+cnt+'" class="input-group-addon">';
		td_val += 			'<span class="glyphicon glyphicon-calendar"></span>';
		td_val += 		'</label>';
		td_val += 	'</div>';
		td_val += '</td>';
		
	if(!global_js_arr.vital_dis_array['BP_SYS'] || !global_js_arr.vital_dis_array['BP_DIS']){
		td_val += '<td>';
		td_val += 	'<div class="row">';
		td_val += 		'<div class="col-sm-5">';	
		td_val += 	'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onblur="isNumeric(\''+cnt+'\',\''+cnt1+'\');" onKeyUp="top.fmain.chk_change(\'\',this,event);  focus_next('+cnt+',\'new\');"  class="form-control" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 	'<input type="hidden" name="new_unit'+cnt1+'_'+cnt+'" id="new_unit'+cnt1+'_'+cnt+'"  value="mmHg">';
		td_val += 		'</div>';
		cnt1++;
		td_val += 		'<div class="col-sm-2">/</div>';
		td_val += 		'<div class="col-sm-5">';	
		td_val += 	'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onblur="isNumeric(\''+cnt+'\',\''+cnt1+'\');" focus_next('+cnt+',\'new\');" onKeyUp="top.fmain.chk_change(\'\',this,event);" class="form-control" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 	'<input type="hidden" name="new_unit'+cnt1+'_'+cnt+'" id="new_unit'+cnt1+'_'+cnt+'" value="mmHg">';
		td_val += 		'</div>';		
		td_val += 	'</div>';
		td_val += '</td>';
	}
	cnt1++;
	if(global_js_arr.vital_dis_array['BP_SYS'] && global_js_arr.vital_dis_array['BP_DIS']){
		cnt1++;
	}
	
	if(!global_js_arr.vital_dis_array['PULSE']){
		td_val += '<td>';
		td_val += 	'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onblur="isNumeric(\''+cnt+'\',\''+cnt1+'\');" class="form-control" onKeyUp="top.fmain.chk_change(\'\',this,event);" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 	'<input type="hidden" name="new_unit'+cnt1+'_'+cnt+'" id="new_unit'+cnt1+'_'+cnt+'" value="beats/minute">';
		td_val += '</td>';
	}
	cnt1++;
	
	if(!global_js_arr.vital_dis_array['RESP']){
		td_val += '<td>';
		td_val += 	'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onblur="isNumeric(\''+cnt+'\',\''+cnt1+'\');" class="form-control"  onKeyUp="top.fmain.chk_change(\'\',this,event);" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 	'<input type="hidden" name="new_unit'+cnt1+'_'+cnt+'" id="new_unit'+cnt1+'_'+cnt+'" value="breaths/minute">';
		td_val += '</td>';
	}
	cnt1++;
	
	if(!global_js_arr.vital_dis_array['O2SAT']){
		td_val += '<td>';
		td_val += 	'<div class="input-group">';
		td_val +=		'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onblur="isNumeric(\''+cnt+'\',\''+cnt1+'\');" class="form-control" onKeyUp="top.fmain.chk_change(\'\',this,event);" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 		'<label for="new_range'+cnt1+'_'+cnt+'" class="input-group-addon">';
		td_val += 			'<span><strong>%</strong></span>';
		td_val += 		'</label>';
		td_val += 	'</div>';
		td_val += 	'<input type="hidden" name="new_unit'+cnt1+'_'+cnt+'" id="new_unit'+cnt1+'_'+cnt+'" value="ml/l">';
		td_val += '</td>';
	}
	cnt1++;
	
	if(!global_js_arr.vital_dis_array['TEMP']){
		td_val += '<td>';
		td_val += 	'<div class="row">';
		td_val += 		'<div class="col-sm-6">';
		td_val += 			'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onblur="isNumeric(\''+cnt+'\',\''+cnt1+'\');" onKeyUp="top.fmain.chk_change(\'\',this,event);" class="form-control" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 		'</div>';
		td_val += 		'<div class="col-sm-6">';
		td_val += 			'<div class="row">';
		td_val += 				'<select name="new_unit'+cnt1+'_'+cnt+'" id="new_unit'+cnt1+'_'+cnt+'" class="minimal form-control" onChange="top.fmain.chk_change(\'\',this,event);"><option value="°f" >&deg;f</option><option value="°c" >&deg;c</option></select>';
		td_val += 			'</div>';
		td_val += 		'</div>';
		td_val += 	'</div>';
		td_val += '</td>';
	}
	cnt1++;
	
	//BP Field 
	td_val += '<td>';
	td_val += 	'<select class="form-control minimal selecicon" name="new_bp_type'+cnt+'" id="new_bp_type'+cnt+'">';
	td_val += 		bp_drop_down;
	td_val += 	'</select>';
	td_val += '</td>';
	cnt1++;
	
	
	if(!global_js_arr.vital_dis_array['HEIGHT']){
		td_val += '<td>';
		td_val += 	'<div class="row">';
		td_val += 		'<div class="col-sm-6">';
		td_val += 			'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onblur="isNumeric(\''+cnt+'\',\''+cnt1+'\');" onKeyUp="top.fmain.chk_change(\'\',this,event);" class="form-control" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 		'</div>';
		td_val += 		'<div class="col-sm-6">';
		td_val += 			'<div class="row">';
		td_val += 				'<select name="new_unit'+cnt1+'_'+cnt+'" class="minimal form-control" onChange="top.fmain.chk_change(\'\',this,event);convert_height_weight(\''+cnt+'\',this.value); calculateBmi(\''+cnt+'\');" onclick="update_old_unit(this.value)" onfocus="update_old_unit(this.value)" id="new_unit'+cnt1+'_'+cnt+'"><option value="inch" >inch</option><option value="m" >m</option><option value="cm">cm</option></select>';
		td_val += 			'</div>';
		td_val += 		'</div>';
		td_val += 	'</div>';
		td_val += '</td>';
	}
	cnt1++;
	
	if(!global_js_arr.vital_dis_array['WIEGHT']){
		td_val += '<td>';
		td_val += 	'<div class="row">';
		td_val += 		'<div class="col-sm-6">';
		td_val += 			'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onblur="isNumeric(\''+cnt+'\',\''+cnt1+'\');" onKeyUp="top.fmain.chk_change(\'\',this,event);" class="form-control" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 		'</div>';
		td_val += 		'<div class="col-sm-6">';
		td_val += 			'<div class="row">';
		td_val += 				'<select class="minimal form-control" name="new_unit'+cnt1+'_'+cnt+'" onChange="top.fmain.chk_change(\'\',this,event);convert_height_weight(\''+cnt+'\',this.value); calculateBmi(\''+cnt+'\');"  id="new_unit'+cnt1+'_'+cnt+'" onChange="top.fmain.chk_change(\'\',this,event);convert_height_weight(\''+cnt+'\',this.value)" onclick="update_old_unit(this.value)" onfocus="update_old_unit(this.value)"><option value="lbs" >lbs</option><option value="kg" >kg</option></select>';
		td_val += 			'</div>';
		td_val += 		'</div>';
		td_val += 	'</div>';
		td_val += '</td>';
	}
	cnt1++;
	
	if(!global_js_arr.vital_dis_array['BMI']){
		td_val += '<td>';
		td_val += 	'<input type="text" onChange="calculateBmi(\''+cnt+'\');" onKeyUp="top.fmain.chk_change(\'\',this,event);" class="form-control" name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'">';
		td_val += 	'<input type="hidden" name="new_unit'+cnt1+'_'+cnt+'" id="new_unit'+cnt1+'_'+cnt+'" value="kg/sqr. m">';
		td_val += '</td>';
	}
	cnt1++;
	
	if(!global_js_arr.vital_dis_array['PAIN']){
		td_val += '<td>';
		td_val += '<select name="new_range'+cnt1+'_'+cnt+'" id="new_range'+cnt1+'_'+cnt+'" onChange="top.fmain.chk_change(\'\',this,event);" class="form-control minimal">';
		td_val += ''+pain_dropdown+'</select>';
		td_val += '</td>';
	}
	cnt1++;
	
	// BMI Comment Section
		td_val += '<td>';
		td_val += 	'<select class="form-control minimal selecicon" onKeyUp="top.fmain.chk_change(\'\',this,event);" name="new_comment'+cnt+'" id="new_comment'+cnt+'">';
		td_val +=		bmi_drop_down
		td_val += 	'</select>';
		td_val += '</td>';
		
		td_val += '<td>';
		td_val += 	'<input type="text" class="form-control" name="inhale_O2'+cnt+'" id="inhale_O2'+cnt+'" value="">';
		td_val += '</td>';
		cnt1++;
		
		td_val += '<td>';
		td_val += 	'<span class="glyphicon glyphicon-plus pointer" title="Add More" id="add_row_'+cnt+'" onClick="show_next(\''+cnt+'\');"></span>';
		td_val += '</td>';
		
	var tr = "<tr id='tbl_vs_data_row_"+cnt+"' class='"+td_class+"'>" + td_val + "</tr>";		
	
	var obj = $("#vs_table").last().append(tr);
	$('#last_cnt').val(cnt);
	$('#new_range1_'+cnt+'').focus();
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
}

function removeTableRow(id, cnt){
	if(id){
		window.top.show_loading_image('show');
		var webroot = top.JS_WEB_ROOT_PATH;
		var url = webroot+"/interface/Medical_history/vs/ajax/ajax_handler.php?showpage=vs&mode=delete&del_id="+id;
		$.ajax({
			type:'POST',
			url:url,
			success:function(response){
				if(response && response != 0){
					window.top.show_loading_image('hide');
					$('.data_row_'+id).remove();
					top.alert_notification_show('Record deleted successfully');
				}
			}
		});
	}
	else{
		$('#tbl_vs_data_row_'+cnt).fadeOut('fast');
		$('#tbl_vs_label_row_'+cnt).fadeOut('fast');
	} 
}


function graph_show_med_hx(val,pt){
	window.top.show_loading_image('show');
	var webroot = top.JS_WEB_ROOT_PATH;
	var url = webroot+"/interface/Medical_history/vs/ajax/ajax_handler.php?showpage=vs&patient="+pt+"&sign_id="+val+'&get_graph=yes';
	$.ajax({
		type:'POST',
		url:url,
		success:function(response){
			var result = $.parseJSON(response);
			if(result.status.length > 0){
				top.fAlert(result.status);
			}else{
				var graph_values = result.axisName.join(',');
				graph_values = graph_values.split(',');
				$('#myModal .modal-header .modal-title').html(result.graphTitle);
				$('#myModal').modal({
					backdrop: 'static',
					keyboard: false
				}).modal('show');
				line_chart(result.graphTitle,result.series,'shw_graph_file',graph_values[0],graph_values[1]);
			}
			window.top.show_loading_image('hide');
			return false;	
		}
	});
}

//btns --- 
self.focus();
top.btn_show("VS");
top.show_loading_image("hide");