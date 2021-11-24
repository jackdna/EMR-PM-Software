//Global object
var global_obj = {};
var vocab_val = $.parseJSON(vocabulary_arr);

    //Fetch ICD10 codes for typeahead
	function set_typeahead_arr(){
        getDataFilePath  = zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=getICD10Data";
        
		$('#list_problem_0').each(function(indx){bind_autocomp($(this), getDataFilePath,'mhx'); });
    }
    
    //Function is not in use.
	function set_typeahead_arr_old(){
		$.ajax({
			type:'GET',
			url:top.JS_WEB_ROOT_PATH+'/interface/Medical_history/problem_list/ajax/ajax_handler.php?get_typeahead_arr=yes',
			success:function(response){
				var result = $.parseJSON(response);
				global_obj.strSnowmed_ct = result.strSnowmed_ct;
				global_obj.strTHDesc = result.strTHDesc;	
				global_obj.strTHDesc2 = result.strTHDesc2;	
				global_obj.strTHPracCode = result.strTHPracCode;
				global_obj.desc_prac_code = result.desc_prac_code;
				
				//Problem list typeahead
				var autocomplete = $('#list_problem_0').typeahead();
				autocomplete.data('typeahead').source = global_obj.strTHDesc;
				autocomplete.data('typeahead').updater = function(item){
					var value = global_obj.desc_prac_code[item];
					$('#ccda_code').val(value);
					return item;
				};	
			}
		});
	}	

	function fill_pl_code(med,code,index){
		//$('#list_problem_'+index).val(med);
		$('#ccda_code').val(code);
	}

	function toggle_other_dropdown(other_div_id,main_dropdown_id,value){
		var other_obj = $('#'+other_div_id+'');
		var main_drop_obj = $('#'+main_dropdown_id+'');
			
		if(value == 'Other'){
			if(other_obj.hasClass('hide')){
				other_obj.removeClass('hide');
			}
			if(main_drop_obj.hasClass('hide') === false){
				main_drop_obj.addClass('hide');
			}
		}else{
			if(other_obj.hasClass('hide') === false){
				other_obj.addClass('hide');
			}
		}
	}
	
	//Delete problem list
	function del_records(confirm){
		var val_arr = new Array;
		$('#div_disable').css('display','block');
		var res = $("input[name='chk_records[]']:checked").length;
		if(res<=0){
			top.fAlert("Please select at least one record");
			$('#div_disable').hide();
			return false;	
		}
		
		
		if(!confirm){
			top.fancyConfirm(vocab_val.delete,"",'top.fmain.del_records("yes")','');
		}else{
			$('input[name^=chk_records]').each(function(id,elem){
				var element = $(elem);
				if(element.prop('checked') === true){
					var value = element.val();
					val_arr.push(value);
				}
			});
			
			var delete_ids = val_arr.join(',');
			var url = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/problem_list/ajax/ajax_handler.php?mode=delete&del_check=yes';
			var data = 'del_ids='+delete_ids;
			$.ajax({
				url:url,
				type:'POST',
				data:data,
				success:function(response){
					if($.trim(response) != ''){
						if(response > 0){
							top.alert_notification_show('Record deleted successfully');
							$.each(val_arr,function(id,val){
								$('.trIDS'+val).remove();
								$('.prob_list_'+val).remove();
								$('#trIDS'+val).remove();
							});
						}
					}
				}
			});
			$('#div_disable').css('display','none');
		}
		
	}
	
	function modify_ProblemData(primary_key_id, pid){
		var url = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/problem_list/ajax/ajax_handler.php?edit_id='+primary_key_id+'&pid='+pid;
		$.ajax({
			url:url,
			type:'GET',
			success:function(response){
				var result = $.parseJSON(response);
				var operator_name = result.oper_name;
				var prob_list_arr = $.makeArray(result.problem_list_arr);
				
				//Setting Modification values
				$('#operator_name_0').val(operator_name);
				$('#hiden_val_field').val(prob_list_arr[0].id);
				$('#dss_prblm_id').val(prob_list_arr[0].dss_prblm_id);

				// set patient problem id
				top.fmain.$('#dssPatSCPopup').attr('data-scid', prob_list_arr[0].id);
				
				//Filling edit data values
				$('#list_problem_0').val(prob_list_arr[0].problem_name); //Textarea
				$('#prob_type').val(prob_list_arr[0].prob_type).selectpicker('refresh'); //Prob type dropdown
				
				//Setting Status fields
				$('#list_status_0 select.selectpicker').val(prob_list_arr[0].status).selectpicker('refresh');
				if(prob_list_arr[0].status == 'Other'){
					if($('#list_status_0').hasClass('hide') === false){
						$('#list_status_0').addClass('hide');
					}
					if($('#list_status_other_0').hasClass('hide') === true){
						$('#list_status_other_0').removeClass('hide');
					}
					$('#list_status_other_0 input.form-control').val(prob_list_arr[0].status);
				}else{
					if($('#list_status_0').hasClass('hide') === true){
						$('#list_status_0').removeClass('hide');
					}
					if($('#list_status_other_0').hasClass('hide') === false){
						$('#list_status_other_0').addClass('hide');
					}
				}
				
				//CCDA input field
				$('#ccda_code').val(prob_list_arr[0].ccda_code);
                //console.log(prob_list_arr[0].service_eligibility);
                
                // $('#service_eligibility').prop('checked', false);
                // if(prob_list_arr[0].service_eligibility==1) {$('#service_eligibility').prop('checked', true);}
                // $('#service_eligibility').val(prob_list_arr[0].service_eligibility);
				
				//Onset date and time
				if(prob_list_arr[0].onset_date != '' || prob_list_arr[0].new_OnsetTime != ''){
					$("#list_date_0").val(prob_list_arr[0].onset_date);
					$("#list_date_time0").val(prob_list_arr[0].new_OnsetTime);
				}else{
					top.fmain.getDate_and_setToField("list_date_0","list_date_time0");
				}
			}
		});
	}
	
	function show_child_rows(obj,id){
		var hide_call = $(obj).data('call');
		if($(obj).hasClass('glyphicon-triangle-bottom') === true){
			$(obj).removeClass('glyphicon-triangle-bottom');
			$(obj).addClass('glyphicon-triangle-top');
		}else{
			$(obj).removeClass('glyphicon-triangle-top');
			$(obj).addClass('glyphicon-triangle-bottom');
		}
		
		$('.trIDS'+id).each(function(id,elem){
			if(hide_call == 'no') {
				if($(elem).hasClass('hide') === true){
					$(elem).removeClass('hide');
				}
			}else{
				if($(elem).hasClass('hide') === false){
					$(elem).addClass('hide');
				}
			}	
		});
		if(hide_call == 'no') {
			$(obj).attr('data-call','yes');
			$(obj).data('call','yes');
		}else{
			$(obj).attr('data-call','no');
			$(obj).data('call','no');
		}
	}
	
	function check_umls_pl(obj,index){
		index = index || "0";
		medName = $.trim($(obj).val());
		if(medName != "" && $("#ccda_code").val()==""){
		$.ajax({
				type: "POST",
				url: top.JS_WEB_ROOT_PATH+"/interface/Medical_history/problem_list/ajax/ajax_handler.php?medName="+encodeURI(medName)+"&index="+index,
				success: function(response){
					 if(response != null && typeof(response)!='undefined' && response!=''){
						$('#div_umls .modal-body').html(response);
						$('#div_umls').modal({
							backdrop: 'static',
							keyboard: false
						}).modal('show');
					}else{
						$('#div_umls').modal('hide');
					} 
				}
			});
		}
	}
	
	function setProListOpts(value,url){
		var win_url = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/index.php?showpage=problem_list&sopt='+value;
		if(url){
			win_url = url+value;
		}
		top.show_loading_image('show');
		var url = win_url;
		window.location.href=url;
	}
	
	function open_med_window(val_to_search){
		window.open("http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c="+val_to_search+"&mainSearchCriteria.v.cs=2.16.840.1.113883.6.103&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en","ProblemList","height=700,width=1000,top=50,left=50,scrollbars=yes");
	}
	
	
	function check4DxDesc_new(obj1){
		
		ptProbList_fillInDxDesc(obj1,top.JS_WEB_ROOT_PATH);	
	}

	self.focus();
	//btns ---
	top.btn_show("PL");
	//btns ---
	top.show_loading_image('hide');	

	function set_window_height(){
		var available_height = window.innerHeight;
		var rht_lower_sec_height = $('#sub_table_List').outerHeight(true);
		var rht_mnm_content_height = parseInt(available_height - (rht_lower_sec_height+10));
		
		$('#problem_list_content').height(rht_mnm_content_height);
	}	
	

$(window).resize(function(){
	set_window_height();
});
	
$('document').ready(function(){
	set_window_height();
	set_typeahead_arr();
	top.show_loading_image("none");
	
	$("#select_all").on('click',function(){
		var status = true;
		if($(this).prop('checked') === false){
			status = false;
		}
		
		$("input[type=checkbox]").each(function(id,elem){
            if(elem.id!='service_eligibility')
			$(elem).prop('checked',status);
		});
	});
	
    $("#service_eligibility").on('click',function(){
        if($(this).is(':checked')==true){$('#service_eligibility').val(1);$('#service_eligibility').prop('checked', true);
        }else {$('#service_eligibility').val(0);$('#service_eligibility').prop('checked', false);}
    });
});

