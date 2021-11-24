var routest_code_typeahead_key_arr 		= new Array();
var routest_code_typeahead_val_arr 		= new Array();
var bodysite_codes_typeahead_key_arr 	= new Array();
var bodysite_codes_typeahead_val_arr 	= new Array();
var vfc_codes_typeahead_key_arr 		= new Array();
var vfc_codes_typeahead_val_arr 		= new Array();
var nip1_txt_typeahead_key_arr 			= new Array();
var nip1_txt_typeahead_val_arr 			= new Array();
var nip2_txt_typeahead_key_arr 			= new Array();
var nip2_txt_typeahead_val_arr 			= new Array();
var manufacture_txt_typeahead_key_arr 	= new Array();
var manufacture_txt_typeahead_val_arr 	= new Array();

//Filling typeahead value arr
//Routest codes
$.each(global_js_var.routest_code_arr,function(id,val){
	routest_code_typeahead_key_arr[val] = id;
	routest_code_typeahead_val_arr.push(val);
});

//Bodysite codes
$.each(global_js_var.bodysite_codes_arr,function(id,val){
	bodysite_codes_typeahead_key_arr[val] = id;
	bodysite_codes_typeahead_val_arr.push(val);
});

//VFC codes
$.each(global_js_var.vfc_codes_arr,function(id,val){
	vfc_codes_typeahead_key_arr[val] = id;
	vfc_codes_typeahead_val_arr.push(val);
});	

//NIP1 Txt codes
$.each(global_js_var.nip1_txt_arr,function(id,val){
	nip1_txt_typeahead_key_arr[val] = id;
	nip1_txt_typeahead_val_arr.push(val);
});		

//NIP2 Txt codes
$.each(global_js_var.nip2_txt_arr,function(id,val){
	nip2_txt_typeahead_key_arr[val] = id;
	nip2_txt_typeahead_val_arr.push(val);
});		

//Manufacturer names
$.each(global_js_var.manufacture_txt_arr,function(id,val){
	manufacture_txt_typeahead_key_arr[val] = id;
	manufacture_txt_typeahead_val_arr.push(val);
});		

function insertImmIdVizChange(olddata,obj,e, hidMedObj){
	e = e || event;		
	characterCode = e.keyCode;
	if(obj.type == "text" || obj.type == "textarea"){
		var newData = obj.value;
		if(characterCode != 9 && characterCode != 16 ){
			if(olddata != newData){
				var strValue = document.getElementById("hidImmIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}					
				document.getElementById("hidImmIdVizChange").value = strValue;
			}				
		}	
	}
	else if(obj.type == "checkbox"){
		var newData = "";
		if(obj.checked == true){
			newData = "checked";
		}
		if(olddata != newData){
			if(olddata != newData){
				var strValue = document.getElementById("hidImmIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}
				document.getElementById("hidImmIdVizChange").value = strValue;
			}
		}			
	}
	else if(obj.type == "radio"){					
		var newData = "";
		if(obj.checked == true){
			newData = "checked";
		}
		if(olddata != newData){
			if(olddata != newData){
				var strValue = document.getElementById("hidImmIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}
				document.getElementById("hidImmIdVizChange").value = strValue;
			}
		}
	}
	else if(obj.type == "select-one"){	
		var strValue = document.getElementById("hidImmIdVizChange").value;
		var intMedId = hidMedObj.value;
		if(strValue.search(intMedId) < 0){
			strValue = strValue + intMedId + ",";
		}
		document.getElementById("hidImmIdVizChange").value = strValue;
	}
}

function get_selected_imm_data(){
	$('#immunizations_form').attr('action','index.php?showpage=immunizations');
	$('#immunizations_form').submit();
}


function saveImmRegInfo(){
	var qryString="?reg_id="+dgi('reg_id').value+"&reg_status="+dgi('reg_status').value+"&publicity_code="+dgi('publicity_code').value+"&protection_indicator="+dgi('protection_indicator').value;
	qryString+="&indicator_eff_date="+dgi('indicator_eff_date').value+"&publicity_code_eff_date="+dgi('publicity_code_eff_date').value+"&imm_reg_status_eff_date="+dgi('imm_reg_status_eff_date').value;
	var url = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/immunizations/ajax/ajax_handler.php'+qryString+'&save_reg=yes';
	$.ajax({
	  url: url,
	  type:'GET',
	  success:function(response){
		if($.trim(response) != '' && $.trim(response) > 0){
			top.alert_notification_show('Immunization Registry Saved Successfully.');
		}
		$("#div_imm_reg_info").modal('hide');	
	  }
	});	
}

function setSnomedVal(obj1,obj2){
	infac_val=obj1.value.toLowerCase();
	$("#"+obj2).val("");
	if(infac_val=='varicella infection'){
		$("#"+obj2).val('38907003');
	}	
}

function setValueInHiddenField(name, value){
	var obj = document.getElementsByName(name);
	obj[0].value = value;
	if(name.substr(0,8)=="im_child"){
		if(value==true){
			obj[0].value = 1;
		}else{
			obj[0].value = 0;
		}
	}
}

function set_typeaheads(){
	$('.immunization_comments').each(function(id,elem){
		var nip1_typeahead = $(elem).typeahead();	
		nip1_typeahead.data('typeahead').source = nip1_txt_typeahead_val_arr;
		nip1_typeahead.data('typeahead').updater = function(item){
			set_typeahead_values($(elem),item,'immunization_comments',$('.im_comments_code',$(elem).parent()[0]));
			return item;
		};
	});
	
	
	$('.immunization_Manufacturer').each(function(id,elem){
		var manufacturer_typeahead = $(elem).typeahead();
		manufacturer_typeahead.data('typeahead').source = manufacture_txt_typeahead_val_arr;
		manufacturer_typeahead.data('typeahead').updater = function(item){
			set_typeahead_values($(elem),item,'immunization_Manufacturer',$('.immunization_Mfr_Code',$(elem).parent()[0]));
			return item;
		};		
	});
	
	$('.immunization_refusal_reason').each(function(id,elem){
		var refusal_reason_typeahead = $(elem).typeahead();
		refusal_reason_typeahead.data('typeahead').source = nip2_txt_typeahead_val_arr;
		refusal_reason_typeahead.data('typeahead').updater = function(item){
			set_typeahead_values($(elem),item,'immunization_refusal_reason',$('.im_refusal_reason_code',$(elem).parent()[0]));
			return item;
		};		
	});
	
	$('.immunization_Route_and_site').each(function(id,elem){
		var route_and_site_typeahead = $(elem).typeahead();
		route_and_site_typeahead.data('typeahead').source = routest_code_typeahead_val_arr;
		route_and_site_typeahead.data('typeahead').updater = function(item){
			set_typeahead_values($(elem),item,'immunization_Route_and_site',$('.im_route',$(elem).parent()[0]));
			return item;
		};		
	});
	
	$('.immunization_site').each(function(id,elem){
		var immu_site_typeahead = $(elem).typeahead();
		immu_site_typeahead.data('typeahead').source = bodysite_codes_typeahead_val_arr;
		immu_site_typeahead.data('typeahead').updater = function(item){
			set_typeahead_values($(elem),item,'immunization_site',$('.im_site',$(elem).parent()[0]));
			return item;
		};		
	});	
	
	$('.immunization_funding_program').each(function(id,elem){
		var immu_site_typeahead = $(elem).typeahead();
		immu_site_typeahead.data('typeahead').source = vfc_codes_typeahead_val_arr;
		immu_site_typeahead.data('typeahead').updater = function(item){
			set_typeahead_values($(elem),item,'immunization_funding_program',$('.im_funding_program',$(elem).parent()[0]));
			return item;
		};		
	});	
}

function set_typeahead_values(obj,item,call_from,search_obj){
	var arr_to_search = '';
	
	switch(call_from){
		case 'immunization_comments':
			arr_to_search = nip1_txt_typeahead_key_arr;
		break;

		case 'immunization_Manufacturer':
			arr_to_search = manufacture_txt_typeahead_key_arr;
		break;
		
		case 'immunization_refusal_reason':
			arr_to_search = nip2_txt_typeahead_key_arr;
		break;	
		
		case 'immunization_Route_and_site':
			arr_to_search = routest_code_typeahead_key_arr;
		break;	

		case 'immunization_site':
			arr_to_search = bodysite_codes_typeahead_key_arr;
		break;	

		case 'immunization_funding_program':
			arr_to_search = vfc_codes_typeahead_key_arr;
		break;		
		
		default:
			arr_to_search = '';	
	}
	
	if(arr_to_search.length > 0){
		var obj_val = arr_to_search[item];
		search_obj.val(obj_val);
	}	
}

function immunization_remove_tr(id,cnt,del_request){
	if(!del_request){
		top.fancyConfirm(global_js_var.arr_info_alert['delete'],'','top.fmain.immunization_remove_tr(\''+id+'\',\''+cnt+'\',\'yes\')');
	}else{
		if(id){
			var url = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/immunizations/ajax/ajax_handler.php?showpage=immunizations&mode=delete&del_id='+id+'';
			$.ajax({
				url:url,
				type:'GET',
				success:function(response){
					if($.trim(response) != '' && $.trim(response) > 0){
						top.alert_notification_show('Record deleted successfully');
						$('#immunization_row_'+cnt).fadeOut('fast').remove();
					}
				}
			});
		}
		else{
			$('#immunization_row_'+cnt).fadeOut('fast');
		}
	}
}

function setImmunizationAutoFill(objNumber,obj,total_rows){
	var strString=obj.value;
	if(strString!=""){
		var strArray=strString.split(" - ");
		if(document.getElementById("immunization_name"+objNumber) && strArray[1]){
			if(strArray[0] && strArray[0] != ""){
				document.getElementById("immunization_name"+objNumber).value = strArray[0]+" - "+strArray[1];
			}else{
				document.getElementById("immunization_name"+objNumber).value = strArray[1];
			}			
		}
		if(document.getElementById("immunization_cvx_code"+objNumber) && strArray[0]){
			document.getElementById("immunization_cvx_code"+objNumber).value = strArray[0];
		}
		if(document.getElementById("immunization_type"+objNumber) && strArray[2]){
			document.getElementById("immunization_type"+objNumber).value = strArray[2];
		}
		if(document.getElementById("immunization_Mfr_Code"+objNumber) && strArray[3]){
			document.getElementById("immunization_Mfr_Code"+objNumber).value = strArray[3];
		}
		if(document.getElementById("immunization_Manufacturer"+objNumber) && strArray[4]){
			document.getElementById("immunization_Manufacturer"+objNumber).value = strArray[4];
		}
		if(document.getElementById("imnzn_id"+objNumber) && strArray[5]){
			document.getElementById("imnzn_id"+objNumber).value = strArray[5];
		}
	}
}

function addNewRow(obj,cnt){
	top.show_loading_image("show");	
	var pre_cnt = cnt;
	
	var imgObj = $("#add_row_"+cnt);
	imgObj.attr('title','Delete Row');
	imgObj.attr('class','glyphicon glyphicon-remove pull-right');
	imgObj.removeAttr('onclick');
	imgObj.attr('onclick','immunization_remove_tr(\'\',\''+pre_cnt+'\')');
	imgObj.attr('id','');
	cnt++;
	
	var url = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/immunizations/ajax/ajax_handler.php?get_new_row=yes&row_cnt='+cnt;
	$.ajax({
		url:url,
		type:'GET',
		success:function(response){
			var obj = $("#immunization_table").last().append(response);
			$("#last_cnt").val(cnt);
			set_typeaheads();
			$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
			$('.selectpicker').selectpicker('refresh');
			 $('[data-toggle="tooltip"]').tooltip(); 
			top.show_loading_image("hide");	
		}
	});
}




function addNewPubRow(cnt){
	var row_data = "";
	if($("#addPubDivId_"+cnt)){
		var last_cnt_pub =  $("#last_cnt_pub_"+cnt).val();
		if(last_cnt_pub<=0){
			last_cnt_pub=1;
		}
		var getRows = last_cnt_pub-1;
		
		row_data += '<div id="addPubDivId_'+cnt+'_'+last_cnt_pub+'" class="addPubDivCls col-sm-12">';
			row_data += '<div class="row">';
				
				row_data += '<div class="col-sm-2 bg-success pb10">';
					row_data += '<div class="row">';
						row_data += '<div class="col-sm-6">';
							row_data += '<label for="immunization_type'+cnt+'_'+last_cnt_pub+'">Vaccine Type</label>';
							row_data += '<input type="text" name="immunization_type'+cnt+'_'+last_cnt_pub+'" id="immunization_type'+cnt+'_'+last_cnt_pub+'" class="form-control fl" onKeyUp="top.fmain.chk_change(\'\',this,event);" value="" onChange="setValueInHiddenField(\'im_type'+cnt+'_'+last_cnt_pub+'\', this.value)"/>';
							row_data += '<input type="hidden" name="im_type'+cnt+'_'+last_cnt_pub+'" value="" />';
						row_data += '</div>';
						row_data += '<div class="col-sm-6">';
							row_data += '<label for="immunization_cvx_code'+cnt+'_'+last_cnt_pub+'">CVX Code</label>';
							row_data += '<input type="text" name="immunization_cvx_code'+cnt+'_'+last_cnt_pub+'" id="immunization_cvx_code'+cnt+'_'+last_cnt_pub+'" class="form-control fl" onKeyUp="top.fmain.chk_change(\'\',this,event);" value="" onChange="setValueInHiddenField(\'im_cvx_code'+cnt+'_'+last_cnt_pub+'\', this.value)"/>';
							row_data += '<input type="hidden" name="im_cvx_code'+cnt+'_'+last_cnt_pub+'" value="" />';
						row_data += '</div>';
					row_data += '</div>';
				row_data += '</div>';
				
				row_data += '<div class="col-sm-2 bg-success pb10">';
					row_data += '<div class="row">';
						row_data += '<div class="col-sm-6">';
							row_data += '<label for="immunization_published_date'+cnt+'_'+last_cnt_pub+'">Published Date</label>';
							row_data += '<div class="input-group">';
								row_data += '<input type="text" name="immunization_published_date'+cnt+'_'+last_cnt_pub+'" id="immunization_published_date'+cnt+'_'+last_cnt_pub+'" class="datepicker form-control fl immunization_published_date" onBlur="checkdate(this)" onClick="getDate_and_setToField(\'immunization_published_date'+cnt+'_'+last_cnt_pub+'\', \'\')" onKeyUp="top.fmain.chk_change(\'\',this,event);" value="" onChange="top.fmain.chk_change(\'\',this,event); setValueInHiddenField(\'im_published_date'+cnt+'_'+last_cnt_pub+'\', this.value)"/>';
								row_data += '<label for="immunization_published_date'+cnt+'_'+last_cnt_pub+'" class="input-group-addon pointer">';
									row_data += '<span class="glyphicon glyphicon-calendar"></span>';
								row_data += '</label>';
							row_data += '</div>';
							row_data += '<input type="hidden" name="im_published_date'+cnt+'_'+last_cnt_pub+'" value="" />';
						row_data += '</div>';
						
						row_data += '<div class="col-sm-6">';
							row_data += '<label for="immunization_presented_date'+cnt+'_'+last_cnt_pub+'">Presented Date</label>';
							row_data += '<div class="input-group">';
								row_data += '<input type="text" name="immunization_presented_date'+cnt+'_'+last_cnt_pub+'" id="immunization_presented_date'+cnt+'_'+last_cnt_pub+'" class="datepicker form-control fl immunization_presented_date" onBlur="checkdate(this)" onClick="getDate_and_setToField(\'immunization_presented_date'+cnt+'_'+last_cnt_pub+'\',\'\')" onKeyUp="top.fmain.chk_change(\'\',this,event);" value="" onChange="top.fmain.chk_change(\'\',this,event); setValueInHiddenField(\'im_presented_date'+cnt+'_'+last_cnt_pub+'\', this.value)"/>';
								row_data += '<input type="hidden" name="im_presented_date'+cnt+'_'+last_cnt_pub+'" value="" />';
								row_data += '<label for="immunization_presented_date'+cnt+'_'+last_cnt_pub+'" class="input-group-addon pointer">';
									row_data += '<span class="glyphicon glyphicon-calendar"></span>';
								row_data += '</label>	';
							row_data += '</div>';
						row_data += '</div>';
					row_data += '</div>';
				row_data += '</div>';
				row_data += '<div class="col-sm-2"><div class="row"><div class="col-sm-2 bg-success pb10" style="height:61px;"></div></div></div>';
				
			row_data += '</div>';
		row_data += '</div>';
		row_data += '<div class="clearfix"></div>';
		
		
		$("#addPubDivId_"+cnt).append(row_data);
		$("#last_cnt_pub_"+cnt).val(parseInt(last_cnt_pub)+1);
		$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
	}
}

self.focus();
top.show_loading_image("hide");
//btns --- 
top.btn_show("IMM");

$(function(){
	set_typeaheads();	
	$('body').on('show.bs.modal','#div_imm_reg_info',function(){
		var btn_arr = [['Save','','top.fmain.saveImmRegInfo();']];
		set_modal_btns('div_imm_reg_info .modal-footer',btn_arr);
	});
});	


$(document).ready(function(){
	resultResp = $('#commonNoImmunizations').is(':checked');
    if(resultResp==true){
		$('#immunization_table input,#immunization_table textarea,#immunization_table select').attr('disabled','disabled');
		$('span.glyphicon.glyphicon-plus').css('visibility','hidden');    
	}
        
     $('#commonNoImmunizations').on('click',function(){
        resultResp=$('#commonNoImmunizations').is(':checked');
        if(resultResp==true)
            {
                $('#immunization_table input,#immunization_table textarea,#immunization_table select').attr('disabled','disabled');
                $('span.glyphicon.glyphicon-plus').css('visibility','hidden');    
            }
            else{
				$('#immunization_table input,#immunization_table textarea,#immunization_table select').removeAttr('disabled');
				$('span.glyphicon.glyphicon-plus').css('visibility','visible');    
			}
     });
});