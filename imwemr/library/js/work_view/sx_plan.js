js_array = js_php_arr;

function addToric(nc){
	if(typeof(nc)=="undefined" || nc==""){
		var c=1, tmp=1;
		$("#tbl_toric input[id*=el_toric]").each(function(){  tmp = $(this).attr("id"); tmp=tmp.replace("el_toric","");});	
		//
		c = parseInt(tmp)+1;
	}else{
		var c = nc;
	}
	if($.isNumeric(c)){
		var tt=""+
		"<tr>"+
			"<td><input type=\"text\" id=\"el_toric"+c+"\" name=\"el_toric"+c+"\" value=\"\" class=\"form-control\"></td>"+
			"<td><input type=\"text\" id=\"el_power"+c+"\" name=\"el_power"+c+"\" value=\"\" class=\"form-control\"></td>"+
			"<td><input type=\"text\" id=\"el_axis"+c+"\" name=\"el_axis"+c+"\" value=\"\" class=\"form-control\"></td>"+
			"<td onclick=\"delToric('"+c+"')\"><img src=\""+js_array.web_root+"/library/images/min.png\"></td>"+
		"</tr>"+
		"";
		$("#tbl_toric").append(tt);
	}
	
}

function delToric(c){	
	$("#tbl_toric input[id*=el_toric]").each(function(){
		var tmp = $(this).attr("id"); 
		tmp = tmp.replace("el_toric",""); 
		if(c == tmp){ 
			$(this).parent("td").parent("tr").remove();  
		} 
	});	
}

function openToricWin(url){
	var w="1200px", h=$(window).innerHeight()+"px", sxid = js_array.id_chart_sx_plan_sheet;
	window.open(js_array.web_root+"/addons/toric/toric.php?id="+url+"&sx_planning_sheet_id="+sxid,"win_toric",'location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width='+w+',height='+h);
}


function getCapturedImageInfo(patient_id, image_path, image_id){
	//alert(patient_id+' - '+image_path+' - '+image_id);
	//set_toric_calc_image_data(image_id,image_path);
}

function set_toric_calc_image_data(new_img_id,thumb_image_url){
	//put image and id
	var tmp_val = "t-"+new_img_id;
	var str="<div class=\"div_th_img  col-sm-1\"><img src=\""+thumb_image_url+"\" alt=\"img\" class=\"img-thumbnail\"><input type=\"hidden\" name=\"el_toric_img_id[]\" value=\""+new_img_id+"\"><input type=\"checkbox\" name=\"el_img_checked[]\" value=\""+tmp_val+"\" class=\"hide\"></div>";
	$("#dv_fstrip").find('.row:first').append(str);
	$("#dv_fstrip").removeClass('hide');	
	
	//APPLY CLASS TO LAST ADDED IMAGE	
	$(".div_th_img").last().bind("click", function(){ $(this).children(":input[type=checkbox][name*=el_img_checked]")[0].click();  if($(this).children(":input[type=checkbox][name*=el_img_checked]").prop("checked")){ $(this).addClass("imgselected");  }else{  $(this).removeClass("imgselected");  }   });
}

function sps_load_prev_values(id){		
	if(id=="" || typeof(id)=="undefined"){ return; }
	$.get("sx_planning_sheet.php?ld_prv_val="+id, function(d){
		
		$.each(d,function(id,val){
			var elem = $(document.getElementById(id));
			var type = elem.prop('type');
			
			if(!elem.length){
				if(id == "el_prem_lens"){
					$("#el_prem_lens_"+val).prop("checked", true);	
				}					
				if(id == "el_plan_ak"){  
					$("#el_plan_ak_"+val).prop("checked", true); 
				}
				
				if(id == "el_predict_sel"){
					var val_arr = val.split(',');
					$('#predict_sel_val').selectpicker('val',val_arr).selectpicker('refresh');
				}
				
				if(id.indexOf("el_toric") != -1 || id.indexOf("el_power") != -1 || id.indexOf("el_axis") != -1){
					var c = id.replace("el_toric","").replace("el_power","").replace("el_axis","");
					addToric(c);
					elem.val(val);
				}
			}
			
		});
		
		$.each(d,function(id,val){
			var elem = $(document.getElementById(id));
			var type = elem.prop('type');
			
			if(elem.length){
				if(type == "text" || type == "textarea" || type == "select-one"){
					
					//if(val != '') elem.val(val);		
					elem.val(val);		
				}
				else if(type == "checkbox"){
					elem.prop("checked", function(){ 
						return val == elem.val() ? true : false;  
					});  
				}
			}
		});
		
		$("#div_sx_pop_up").modal('hide');		
	}, "json");	
}

function get_eyes_details(value){
	$.ajax({
		url:js_array.web_root+'/interface/chart_notes/sx_planning_sheet.php?eye='+value,
		type:'POST',
		dataType:'JSON',
		success:function(d){
			if(d && d.ref && typeof(d.ref)!="undefined"){$("#el_refraction").val(""+d.ref);	}
			if(d && d.oref && typeof(d.oref)!="undefined"){	$("#el_othr_eye_ref").val(""+d.oref);	}
			
			if(d && d.kflat && typeof(d.kflat)!="undefined"){	$("#el_k_flat").val(""+d.kflat);	}
			if(d && d.ksteep && typeof(d.ksteep)!="undefined"){	$("#el_k_steep").val(""+d.ksteep);}
			if(d && d.kaxis && typeof(d.kaxis)!="undefined"){	$("#el_k_axis").val(""+d.kaxis);	}
			if(d && d.kcyl && typeof(d.kcyl)!="undefined"){	$("#el_k_cyl").val(""+d.kcyl);	}			
			if(d && d.okflat && typeof(d.okflat)!="undefined"){	$("#el_ok_flat").val(""+d.okflat);	}
			if(d && d.oksteep && typeof(d.oksteep)!="undefined"){	$("#el_ok_steep").val(""+d.oksteep);	}
			if(d && d.okaxis && typeof(d.okaxis)!="undefined"){	$("#el_ok_axis").val(""+d.okaxis);	}
			if(d && d.okcyl && typeof(d.okcyl)!="undefined"){	$("#el_ok_cyl").val(""+d.okcyl);	}
			if(d && d.pachy && typeof(d.pachy)!="undefined"){	$("#el_pachy").val(""+d.pachy);	}
			if(d && d.lensSummary && typeof(d.lensSummary)!="undefined"){	$("#el_lens_sle_summary").val(d.lensSummary);	}
			if(d && d.pupilDilate && typeof(d.pupilDilate)!="undefined"){	$("#el_pupildilated").val(d.pupilDilate);	}
		}
	});
}

function get_test_images(){
	var t_id_chart_sx_plan_sheet = $("#elem_sx_plan_sheet_id").val();
	var t_el_img_checked=$("#el_img_checked_db").val();
	
	var t_el_ids_iol =$("#el_ids_iol").val(); if(typeof(t_el_ids_iol)=="undefined"){ t_el_ids_iol="";  }
	var t_el_ids_ascan=$("#el_ids_ascan").val(); if(typeof(t_el_ids_ascan)=="undefined"){ t_el_ids_ascan="";  }
	var t_el_ids_oct=$("#el_ids_oct").val(); if(typeof(t_el_ids_oct)=="undefined"){ t_el_ids_oct="";  }
	var t_el_ids_topo=$("#el_ids_topo").val(); if(typeof(t_el_ids_topo)=="undefined"){ t_el_ids_topo="";  }
	var t_el_ids_vf=$("#el_ids_vf").val(); if(typeof(t_el_ids_vf)=="undefined"){ t_el_ids_vf="";  }
	
	var url="sx_planning_sheet.php?refresh_flim_strip=1&t_id_chart_sx_plan_sheet="+t_id_chart_sx_plan_sheet+"&t_el_img_checked="+t_el_img_checked+"&t_el_ids_iol="+t_el_ids_iol+"&t_el_ids_ascan="+t_el_ids_ascan+"&t_el_ids_oct="+t_el_ids_oct+"&t_el_ids_topo="+t_el_ids_topo+"&t_el_ids_vf="+t_el_ids_vf ;    		
	$("#dv_fstrip").find('.row:first').html("<div id=\"dvloading\" style=\"\" >Loading</div>");
	$.get(url, function(d){
		$("#dv_fstrip").find('.row:first').html(d);
		$('[data-toggle="tooltip"]').tooltip(); 
		if(d!=""){
			if($("#dv_fstrip").hasClass('hide') == true){
				$("#dv_fstrip").removeClass('hide');
			}
		}else{
			if($("#dv_fstrip").hasClass('hide') == false){
				$("#dv_fstrip").addClass('hide');
			}
		} 
	});
	
}

function set_elem_height(){
	//Setting max height of toric table rows
	var astig_left_prnt = $('.astig_assess');
	var astig_right_prnt = $('.astig_plan');
	var target_astig = $('.toric_wrapper');
	
	
	var left_height = astig_left_prnt.height();
	var right_height = astig_right_prnt.height();
	var toric_max_height = (left_height - right_height);
	target_astig.css({
		'height':toric_max_height - 32,
		'max-height':toric_max_height - 32,
		'overflowY':'auto'
	});
}

function currenttime(){
	var Digital=new Date()
	var hours=Digital.getHours()
	var minutes=Digital.getMinutes()
	var seconds=Digital.getSeconds()
	var dn="PM"
	if(hours<12)
	dn="AM"
	if(hours>12)
	hours=hours-12
	if(hours==0)
	hours=12
	if(minutes<=9)
	minutes="0"+minutes
	if(seconds<=9)
	seconds="0"+seconds
	var ctime=hours+":"+minutes+' '+dn
	return ctime;
}

function print_sx_plan(id){
	//document.frm_sx_plan_sheet.action = js_array.web_root+"/interface/chart_notes/sx_planning_sheet_print.php?final_flag=0&get_dos="+id;
	//document.frm_sx_plan_sheet.target="_blank";
	document.print_sx_plan.submit();
}

function get_sx_dos(obj){
	var data = $(obj).data();
	var db_dos = '';
	if(data.id){
		window.location.href = 'sx_planning_sheet.php?get_sx_id='+data.id;
		/* if(js_array.dos_arr[data.id]){
			var dt_arr = js_array.dos_arr[data.id];
			db_dos = dt_arr.sx_pl_dos;
		} */
	}else{
		fAlert('Please select a valid date');
		return false;
	}
}

function new_sx_sheet(){
	window.location.href = 'sx_planning_sheet.php?get_frm=new';
}

function save_sx_plan_sheet(){
	document.frm_sx_plan_sheet.action = 'save_sx_plan_sheet.php';
	document.frm_sx_plan_sheet.submit();
}

function fullScreen(){ 
	window.moveTo(0,0); 
	window.resizeTo(screen.availWidth,screen.availHeight); 
	
	var window_height = (window.innerHeight - ($('.purple_bar').height() + $('#tbl_button').height() + 35));
	$('.main_content_wrapper').css({
		'height':window_height,
		'overflowY':'auto'
	});
} 

$(window).resize(function(){
	fullScreen();
});

$(function(){
/* 	var btn_arr = new Array;
	if(js_array.elem_per_vo != 1 && ((js_array.finalize_flag != 1))){	//Show save button if old form is not selected
		btn_arr.push(["get_consent_detail","Save","save_sx_plan_sheet();"]);
	}
	btn_arr.push(["printSxBtnId","Print","print_sx_plan('"+js_array.form_id+"')"]);
	top.btn_show("ADMN",btn_arr); */
});

function check_form(){
	if($('#sx_pl_date').val() == '' || $('#sx_pl_date').val() == '00-00-0000'){
		$('#sx_pl_date').addClass('mandatory');
		$('html, body').animate({
			scrollTop: $("#sx_pl_date").offset().top
		}, 100);
		return false;
	}
	return true;
}

function showImage(obj){
	var imgSrc = $(obj).data('img');
	if(imgSrc !== '' && typeof(imgSrc) !== 'undefined'){
		var ext = imgSrc.split('.').pop();
		switch(ext) {
	        case 'jpg':
	        case 'png':
	        case 'gif':
				$('#div_img_modal .modal-header .modal-title').text('imwemr');
				$('#div_img_modal .modal-body').html('<img src="'+imgSrc+'" alt="img" width="100%" height="100%" class="img-responsive"/>');
				$('#div_img_modal').modal('show');
	        break;                        
	        case 'zip':
	        case 'rar':
	            
	        break;
	        case 'pdf':
	           window.open(imgSrc, 'NewPdfWin');
	        break;
	        default:
	            
	    }
	}
}

function setVSVal(obj){
	var txt = $(obj).find('a').text();
	if(txt !== '') $('#el_prev_eye_va').val(txt);
}

function getLenses(phyId,call_load){
	
	call_load = call_load || '';
	
	if( !call_load ) { 
		if( $('#iol_lock').length > 0 ){
			if( $("#iol_lock").is(':checked') ){
				$('#el_surgeon_id').val($('#el_surgeon_id').data('prev'));
				top.fAlert('IOL values locked.');
				return false;
			}
		}
	}
	
	var chartId = $('#elem_sx_plan_sheet_id').val();
	if(phyId == ''){
		$('#tbl_lens tbody').empty();
		$('#tbl_lens tbody').html('<tr><td colspan="9" class="text-center blutab">No Surgeon selected</td></tr>');
		$('#tbl_toric tbody').html('<tr><td colspan="5" class="text-center">Nothing selected</td></tr>');
	}
	
	$.ajax({
		url:js_array.web_root+'/interface/chart_notes/sx_planning_sheet.php?getProvIOL='+phyId+'&chartId='+chartId,
		type:'POST',
		dataType:'JSON',
		success:function(response){
			if(response && typeof(response) !== 'undefined' && $.isEmptyObject(response) === false){
				var rowCounter = 1;
				var staticLensFields = '';
				var optArr = new Array;
				
				$.each(response, function(id1, lensVal1){
					var LensId11 = (lensVal1['ID']) ? lensVal1['ID'] : '';
					
					if(LensId11 !== ''){
						optArr.push(LensId11);
					}
				});
				optArr = jQuery.unique( optArr );
				
				$.each(response, function(id, lensVal){
					var css_tr_bgc = (lensVal['Used']) ? " class=\"hylight\" " : "";
					var counter = 1;
					
					var LensId = (lensVal['ID']) ? lensVal['ID'] : '';
					delete lensVal['ID'];
					
					var staticInpFields = '';
					$.each(lensVal, function(key, val){
						var fieldName = '';
						var bgClass = (counter == 1) ? 'blutab' : '';
						
						fieldName = 'EL_IOL_'+key+'_'+rowCounter;
						
						if(key == 'Used'){
							var chkSelected = (css_tr_bgc == '') ? '' : 'checked';
							staticInpFields += '<td class="'+bgClass+'"><div class="checkbox"><input class="usedChkBox '+key+'" type="checkbox" id="'+fieldName+'" name="'+fieldName+'"  autocomplete="off" '+chkSelected+'><label for="'+fieldName+'"></label></div></td>';
						}else if(key == 'Type'){
							var lensValues = '';
							if(LensId !== ''){
								lensValues = js_array.iolLensArr[LensId]['lensType'];
								
								var stringOptions = '';
								$.each(optArr, function(optId,optVal){
									var selected = (optVal == LensId) ? 'selected' : '';
									if(stringOptions == '') stringOptions += '<option value="">Please select</option>';
									stringOptions += '<option value="'+optVal+'" '+selected+'>'+js_array.iolLensArr[optVal]['lensType']+'</option>';
								});
								
								staticInpFields += '<td class="'+bgClass+'"><select id="'+fieldName+'" name="'+fieldName+'" class="form-control minimal iolNewAdd '+key+'" data-value="" onChange="addthisOpt(this);">'+stringOptions+'</select></td>';
								//staticInpFields += '<td class="'+bgClass+'"><input type="text" id="'+fieldName+'" name="'+fieldName+'" value="'+lensValues+'" class="form-control iolNewAdd" data-value="'+LensId+'" disabled></td>';
							}else{
								strOpt = '';
								$.each(js_array.iolLensArr, function(id, val){
									if(strOpt == '') strOpt += '<option value="">Please select</option>';
									strOpt += '<option value="'+id+'">'+val['lensType']+'</option>';
								});
								
								staticInpFields += '<td class="'+bgClass+'"><select class="form-control minimal iolNewAdd '+key+'" name="'+fieldName+'" id="'+fieldName+'" onChange="addthisOpt(this);" data-value="">'+strOpt+'</select></td>';
							}
						}else{
							//Fucntion to populate Pwr, Cyl, Axis on change
							var populateValue = '';
							//if(key == 'Pwr' || key == 'Cyl' || key == 'Axis'){
								populateValue = 'onChange="setIolValue(this, \''+key+'\'); "';
							//}
							staticInpFields += '<td class="'+bgClass+'"><input data-select="EL_IOL_Type_'+rowCounter+'" type="text" id="'+fieldName+'" name="'+fieldName+'" value="'+val+'" class="form-control '+key+'" '+populateValue+'></td>';
						}
						counter++;
					});
					
					staticLensFields += '<tr data-value="'+LensId+'">'+staticInpFields+'</tr>';
					
					rowCounter++;
				});
				
				if(staticLensFields !== ''){
					$('#tbl_lens tbody').empty();
					$('#tbl_lens tbody').html(staticLensFields);
				}
				
				//Manage The Plan Dropdown
				setIolModelDropdown(phyId,chartId,optArr,call_load);
				
			}else{
				$('#tbl_lens tbody').empty();
				$('#tbl_lens tbody').html('<tr><td colspan="9" class="text-center blutab"> No Lens found </td></tr>');
				
				$('#tbl_toric tbody').html('<tr><td colspan="5" class="text-center">Nothing selected</td></tr>');
			}
		}
	});	
}

function setIolModelDropdown(phyId,chartId,optArr,call_load){
	call_load = call_load || ''; 
	$.ajax({
		url:js_array.web_root+'/interface/chart_notes/sx_planning_sheet.php?getIOlModel='+chartId+'&phyId='+phyId,
		type:'POST',
		dataType:'JSON',
		success:function(response){
			var htmlStr = '';
			if(response && typeof(response) !== 'undefined' && $.isEmptyObject(response) === false){
				$.each(response, function(id, iolVal){
					var dropDownOpt = '';
					$.each(optArr, function(optId,optVal){
						var selected = (optVal == iolVal['Type']) ? 'selected' : '';
						
						if(dropDownOpt == '') dropDownOpt += '<option value="">Please select</option>';
						dropDownOpt += '<option value="'+optVal+'" '+selected+'>'+js_array.iolLensArr[optVal]['lensType']+'</option>';
					});
					
					
					var rowName = js_array.staticIolModel[iolVal['Index']];
					
					var functionVal = "setOtherPlan(this);";
					var className = 'otherFields';
					var dataVal = "data-value='"+rowName.toLowerCase()+"'";
					
					if(rowName.toLowerCase() == 'primary') functionVal = "setThisPlan(this.value);";
					if(rowName.toLowerCase() == 'primary') className = "";
					//if(rowName.toLowerCase() == 'primary') dataVal = "data-value='primary'";
					
					htmlStr +='<tr>';
						htmlStr += '<td><div class="row"><div class="col-sm-4">'+rowName+'</div><div class="col-sm-8"><select name="iolPlan_'+iolVal['Index']+'" class="form-control minimal '+className+'" id="iolPlanDrop" onChange="'+functionVal+'" data-row="'+rowName.toLowerCase()+'">'+dropDownOpt+'<select></div></div></td>';
						htmlStr += '<td><input type="text" id="el_power_'+iolVal['Index']+'" name="el_power_'+iolVal['Index']+'" value="'+iolVal['Power']+'" class="form-control Pwr" '+dataVal+' readonly></td>';
						htmlStr += '<td><input type="text" id="el_toric_'+iolVal['Index']+'" name="el_toric_'+iolVal['Index']+'" value="'+iolVal['Model']+'" class="form-control Cyl" '+dataVal+' readonly></td>';
						htmlStr += '<td><input type="text" id="el_axis_'+iolVal['Index']+'" name="el_axis_'+iolVal['Index']+'" value="'+iolVal['Axis']+'" class="form-control Axis" '+dataVal+' readonly></td>';
					htmlStr +='</tr>';
				});	
				
				if(htmlStr !== ''){
					$('#tbl_toric tbody').html(htmlStr);
					setThisPlan($('#iolPlanDrop').val(),call_load);
					
					$('#tbl_toric tr').find('.otherFields').each(function(id,elem){
						setOtherPlan($(elem),call_load);
					});
				}
			}else{
				//$('#tbl_toric tbody').html('<tr><td colspan="5" class="text-center">No</td></tr>');
			}
		}
	});
	
	/* var iolModelStr = '';
	if(js_array.staticIolModel){
		$.each(js_array.staticIolModel, function(id, val){
			iolModelStr += '';
		});
	} */
	
	
	
	/* $('#tbl_toric').find('select').each(function(id, elem){
		$(elem).empty();
		$(elem).html(dropDownOpt);
	}); */
}

function addthisOpt(obj){
	
	if( $('#iol_lock').length > 0 ){
		if( $("#iol_lock").is(':checked') ){
			$(obj).val($(obj).data('prev'));
			top.fAlert('IOL values locked.');
			return false;
		}
	}
	
	var arr = new Array;
	var valArr = new Array;
	
	//Checking if selected value is not already selected
	if(chkValues(obj) === false){
		alert('The selected value is already selected. Please choose another one');
		$(obj).val('');
	}
	
	$('#tbl_lens').find('.iolNewAdd').each(function(){
		var type = $(this)[0].type;
		
		if(type == 'text'){
			var optString = $(this).val();
			var optSelval = $(this).data('value');
		}else{
			var optString = $(this).find('option:selected').text();
			var optSelval = $(this).val();
		}
		$(this).parent().parent().data('value', optSelval);
		$(this).data('value', optSelval);
		var optStr = '<option value="'+optSelval+'">'+optString+'</option>';
		if(jQuery.inArray(optSelval,valArr) == -1){
			if(optSelval != ''){
				arr.push(optStr);
				valArr.push(optSelval);
			} 
		}
	});
	
	var optString1 = '<option value="">Please select</option>';
	if(arr.length){
		optString1 += arr.join('');
		$('#tbl_lens tbody tr td:not(:first-child)').removeClass('blutab');
	}	
	
	$('#tbl_toric').find('input[name^="el_power_"]').val('');
	$('#tbl_toric').find('input[name^="el_toric_"]').val('');
	$('#tbl_toric').find('input[name^="el_axis_"]').val('');
	$('#tbl_toric').find('select').each(function(id, elem){
		$(elem).empty();
		$(elem).html(optString1);
	});
	
}

function chkValues(obj){
	var value = $(obj).val();
	var validate = true;
	
	$('#tbl_lens tbody td').find('select').not($(obj)).each(function(id, elem){
		//alert($(elem).val()+'----'+value);
		if($(elem).val() == value){
			validate = false;
		}
	});
	
	return validate;
}

function setThisPlan(SelId,call_load){
	
	call_load = call_load || '';
	if( !call_load) {
		if( $('#iol_lock').length > 0 ){
			if( $("#iol_lock").is(':checked') ){
				$("[name=iolPlan_0]").val($("[name=iolPlan_0]").data('prev'));
				top.fAlert('IOL values locked.');
				return false;
			}
		}
	}
	$('#tbl_lens tbody tr td:not(:first-child)').removeClass('blutab');
	
	if(SelId == '') return true;
	
	$('#tbl_lens tbody tr td:not(:first-child)').removeClass('blutab');
	$('#tbl_lens tbody tr').each(function(id, elem){
		var pwrVal = '';
		var cylVal = '';
		var axisVal = '';
		
		var dataArr = $(elem).data('value');
		if(dataArr == SelId){
			$(elem).find('td').addClass('blutab');
			
			//Pwr Value
			pwrVal = $(elem).find('input.Pwr').val();
			
			//Cyl Value
			cylVal = $(elem).find('input.Cyl').val();
			
			//Axis Value
			axisVal = $(elem).find('input.Axis').val();
			
			var toricElem = $('#tbl_toric');
			
			//if(pwrVal !== ''){
				toricElem.find('input[data-value="primary"].Pwr').val(pwrVal);
			//}
			
			//if(cylVal !== ''){
				toricElem.find('input[data-value="primary"].Cyl').val(cylVal);
			//}
			
			//if(axisVal !== ''){
				toricElem.find('input[data-value="primary"].Axis').val(axisVal);
			//}
			
		}
	});
}

function setIolValue(obj, callFrom){
	
	if( callFrom == 'Pwr' || callFrom == 'Cyl' || callFrom == 'Axis' ) {
		if( $('#iol_lock').length > 0 ){
			if( $("#iol_lock").is(':checked') ){
				$(obj).val($(obj).data('prev'));
				top.fAlert('IOL values locked.');
				return false;
			}
		}
	}
	
	var elemVal = $(obj).val();
	var parentSel = $(obj).data('select');
	
	var findVal = $('#'+parentSel).val();
	
	var toricElem = $('#tbl_toric');
	
	toricElem.find('tr').each(function(id,elem){
		$(elem).find('select').each(function(ID, element){
			if($(element).val() == findVal){
				$(elem).find('input.'+callFrom+'').val(elemVal);
			}
		});
	});
	
	
	//if($(obj).parent().hasClass('blutab')) 
}

function setOtherPlan(obj,call_load){
	
	call_load = call_load || '';
	
	if( !call_load ) {
		if( $('#iol_lock').length > 0 ){
			if( $("#iol_lock").is(':checked') ){
				$(obj).val($(obj).data('prev'));
				top.fAlert('IOL values locked.');
				return false;
			}
		}
	}
	var rowName = $(obj).data('row');
	var value = $(obj).val();
	var toricElem = $('#tbl_toric');
	
	if(value == ''){
		toricElem.find('input[data-value="'+rowName+'"].Pwr').val('');
		toricElem.find('input[data-value="'+rowName+'"].Cyl').val('');
		toricElem.find('input[data-value="'+rowName+'"].Axis').val('');
	}else{
		$('#tbl_lens tr').each(function(id, elem){
			$(elem).find('select').each(function(ID, element){
				if($(element).val() == value){
					var pwr = $(elem).find('input.Pwr').val();
					var cyl = $(elem).find('input.Cyl').val();
					var axis = $(elem).find('input.Axis').val();
					
					
					//if(pwr !== ''){
						toricElem.find('input[data-value="'+rowName+'"].Pwr').val(pwr);
					//}
					
					//if(cyl !== ''){
						toricElem.find('input[data-value="'+rowName+'"].Cyl').val(cyl);
					//}
					
					//if(axis !== ''){
						toricElem.find('input[data-value="'+rowName+'"].Axis').val(axis);
					//}
					
					return false;
				}
			});
		});
	}
	
}

function saveSxData(){
	$('#tbl_lens').find('input:disabled').each(function(i,v){
		if($(this).data('value')){
			$(this).prop('disabled',false);
			$(this).val($(this).data('value'));
		}
	});
	document.frm_sx_plan_sheet.submit();
}

$(function(){
	$("#tbl_lens").on("click",'.usedChkBox', function(){ 
		if($(this).prop("checked")){ 
			$("#tbl_lens .usedChkBox").not($(this)).prop("checked", false); 
			$("#tbl_lens tr").removeClass("hylight");  
			$(this).parent().parent().addClass("hylight") 
		} 
	});
	
	$('body').on('focus blur',"#el_surgeon_id,[id^=EL_IOL_Type_],[id^=EL_IOL_Pwr_],[id^=EL_IOL_Cyl_],[id^=EL_IOL_Axis_],[name^=iolPlan_],[id^=el_power_],[id^=el_toric_],[id^=el_axis_]",function(){ $(this).data('prev',$(this).val()); });
	
	$('[data-toggle="tooltip"]').tooltip(); 
});

$(document).ready(function(){
	$('.selectpicker').selectpicker();
	$( "#el_date_surgery,  #el_prev_eye_date, #sx_pl_date" ).datetimepicker({timepicker:false,format:js_array.js_date_format,autoclose: true,scrollInput:false});
	
	$( "#el_date_surgery" ).val(js_array.el_date_surgery);
	$( "#el_prev_eye_date" ).val(js_array.el_prev_eye_date);
	//set_elem_height();
	
	$("#tbl_ks input[name*=el_k_given]").bind("click", function(){ 
		if($(this).prop("checked")){ 
			$("#tbl_ks :checked[name*=el_k_given][name!="+this.name+"]").prop("checked", false);  
		} 
	});
	
	$(".div_th_img").bind("click", function(){ $(this).children(":input[type=checkbox][name*=el_img_checked]")[0].click();  if($(this).children(":input[type=checkbox][name*=el_img_checked]").prop("checked")){ $(this).addClass("imgselected");  }else{  $(this).removeClass("imgselected");  }   });
	
	if(js_array.save_status != ''){
		top.alert_notification_show(js_array.save_status);
	}
	getLenses($('#el_surgeon_id').val(),true);
	
});

function on_proc_change(val)
{
	/*var op_str="<span>PO Evalution Mapping</span>";
	if(val)
		{
			var val_arr=val.split('~');
			op_str+=val_arr[1]+" Post OP";
		}else op_str+="None";
	$("#po_eva_map").html(op_str);*/
	$("#1_Day").prop("checked", false);
	$("#1_Week").prop("checked", false);
	$("#1_Month").prop("checked", false);
	if(val)
	{
		var val_arr=val.split('~');
		if(val_arr[1])
		{
			var op_arr=val_arr[1].split(',');
			for(var i=0;i<=op_arr.length;i++)
			{
	 console.log(op_arr[i]);
				var idVal=op_arr[i].replace(" ", "_");
				$("#"+idVal).prop("checked", true);
			}
		}
		
	}
}