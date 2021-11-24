function addnew(){
	document.forms.frm_new_profile.reset();
	document.forms.frm_new_profile.id.value='';
	$('#div_test_form').html('');
    $('#copy_profile_from').hide();
    $('#copy_profile').html('');
    $('#copy_pre_val').val('');
}

function load_test_page(id,profile_data,physician_id){
	id_data_arr 		= id.split('~@~');
	val_test_table 		= id_data_arr[0];
	val_test_id 		= id_data_arr[1];
	val_script_file		= id_data_arr[2];
	
	$('#test_id').val(val_test_id);
	tp					= '../../tests/'+val_script_file+'?callFromInterface=admin&test_master_id='+val_test_id;
	top.show_loading_image('hide');
	top.show_loading_image('show','200', 'Loading test profile form...');
	$.ajax({
		url:tp,
		type:'GET',
		complete:function(rd){
			top.show_loading_image('hide');
			r = rd.responseText;
			//a=window.open(); a.document.write(r);return;
			$('#div_test_form').html(r);
			
			if(typeof(profile_data)!='undefined' && profile_data!=''){
				fillEditData(profile_data);
			}
            $('#copy_profile_from').hide();
            if(typeof(val_test_id)!='undefined' && val_test_id!=''){
                show_copy_profile_dd(val_test_id);
            }

		}
	});

}

var isEditMode= false;
function editMe(id,profile_name,test_id_selval,physician_id,profile_data,favorite){
	document.forms.frm_new_profile.reset(); 
	$('#div_test_form').html('');
	$('#id').val(id);
	$('#profile_name').val(unescape(profile_name));
	
	id_data_arr 		= test_id_selval.split('~@~');
	val_test_table 		= id_data_arr[0];
	val_test_id 		= id_data_arr[1];
	val_script_file		= id_data_arr[2];
	
	$('#test_id').val(val_test_id);	
	$('#sel_test_id').val(test_id_selval);
	
	load_test_page(test_id_selval,profile_data,physician_id);
	
	$('#physician_id').val(physician_id);
	if(favorite==1) $('#favorite').prop('checked',true); else $('#favorite').prop('checked',false);
	isEditMode= true;
}

var fundus_elem_diagnosis = "";

function fillEditData(profile_data){
	top.show_loading_image('hide');
	var data_array = jQuery.parseJSON(profile_data);
	f = document.forms.test_form_frm;
	f.reset();
	e = f.elements;
	
	if($("#test_id").val()=="14"){
		fundus_elem_diagnosis = data_array['elem_diagnosis'];
	}
	
	for(i=0;i<e.length;i++){
		o = e[i];
		on	= o.name;
		on = on.trim();
		v	= data_array[on];
		
		if(on.indexOf('[]')!= '-1' && on.substr(on.length - 2)=='[]'){ /*if name of the element is an array*/
			name1 = on.substr(0, (on.length - 2));
			v1	= data_array[name1];
			elementarray = document.getElementsByName(on);
			if(elementarray.length){
				for(j=0;j<elementarray.length;j++){
					elem = elementarray[j];
					val = v1[j];
					if (elem.tagName == "INPUT" || elem.tagName == "SELECT" || elem.tagName == "TEXTAREA"){
						if (elem.type == "checkbox"){
							if(val==elem.value) $(elem).prop('checked',true);
						}else if(elem.type == "radio"){
							if(val==elem.value) $(elem).prop('checked',true);	
						}else if(elem.type!='submit' && elem.type!='button'){
							elem.value = val;
							if(elem.tagName == "TEXTAREA"){elem.value = elem.value.replace(/<br>/g,"\n");}
						}
					}
				}
			}
		}

		if(typeof(v)=='undefined')continue;
		if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
			if (o.type == "checkbox"){
				if(v==o.value) $(o).prop('checked',true);
			}else if(o.type == "radio"){
				if(v==o.value) $(o).prop('checked',true);	
			}else if(o.type!='submit' && o.type!='button'){
				o.value = v;
				if(on=='elem_diagnosis' && v=='Other' && typeof(checkDiagnosis)=='function'){checkDiagnosis(v);}
				if(o.tagName == "TEXTAREA"){o.value = o.value.replace(/<br>/g,"\n");}
			}
		}
	}		
}

function saveFormData(){
    $('.btn', top.document).prop('disabled', true);
	/*CHECKING BASIC DATA*/
	profile_id 		= $('#id').val();
	profile_name	= $('#profile_name').val();
	physician_id	= $('#physician_id').val();
	test_id			= $('#test_id').val();
	favorite		= $('#favorite').prop('checked') ? 1 : 0;
    
    var alert_msg='';
	if(profile_name==''){
        alert_msg+='Please enter Preference (Profile) Name.';
	}
    if(physician_id==''){
        alert_msg+='<br>Please select Physician';
	}
    if(test_id==''){
        alert_msg+='<br>Please select Test.';
	}
    if(alert_msg!='') {
        fAlert(alert_msg,'',"$('.btn', top.document).prop('disabled', false)",'','','Close')
		return false;
    }
	top.show_loading_image('hide');
	top.show_loading_image('show','400', 'Saving data...');
	url_part = 'profile_id='+profile_id+'&profile_name='+profile_name+'&physician_id='+physician_id+'&test_id='+test_id+'&favorite='+favorite;

	/*GETTING TEST FORM DATA*/
	f = document.forms.test_form_frm;
    if(typeof(document.forms.test_form_frm)=='undefined'){
        $('.btn', top.document).prop('disabled', false);
        top.show_loading_image('hide');
		return false;
    }
	e = f.elements;
	var string_form = '';
	for(i=0;i<e.length;i++){
		o = e[i];
		on	= o.name;
		//v	= arrAllShownRecords[pkId][on];
		if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
			if (o.type == "checkbox" || o.type == "radio") {
				v = $(o).prop('checked') ? o.value : '';
				if(o.type == "radio" && v=='') continue;
				string_form += on+'='+v+'&';
				//$('#'+oid).prop('checked',true);
			} else if(o.type!='submit' && o.type!='button') {
				v = o.value
				string_form += on+'='+v+'&';
			}
		}
	}
	//a= window.open();a.document.write(string_form);
	$.ajax({
		type: "POST",
		data: string_form,
		url:"interpretation_profiles_ajax.php?do=SaveMainData&"+url_part, 
		success:function(r){
            fAlert(r,'',"$('.btn', top.document).prop('disabled', false)",'','','Close');
            document.forms.frm_new_profile.reset();
            document.forms.frm_new_profile.id.value='';
            document.forms.test_form_frm.reset();
            $('#div_test_form').html('');
            $('#copy_profile_from').hide();
            $('#copy_profile').html('');
            $('#copy_pre_val').val('');
            $('#test_id').val('');
            loadSavedProfiles(physician_id);
            top.show_loading_image('hide');
		}
	});
}

function loadSavedProfiles(t,obj){
	$('#physician_id').val(t);
	$('#sel_test_id').prop('disabled',false);
	top.show_loading_image('hide');
	top.show_loading_image('show','400', 'Loading test profile form...');
	$.ajax({
	type: "GET",
	url:'interpretation_profiles_ajax.php?do=SavedProfiles&pro_id='+t, 
	success:function(r){
		top.show_loading_image('hide');
		if(r=='' || r==null) return;
		r = jQuery.parseJSON(r);
		if(typeof(r)=='undefined' || r==null) return;
	//	a=window.open();a.document.write(r);return;
	//	$('#div_saved_profiles').html(r);return;
		html = '';
		if(typeof(r['result'])=='undefined') return;
		s = r['result'];
		cnt = 1;
		for(y in s){
			row = s[y];
			//alert(row.id+', '+row.profile_data);// html = row.id+', '+row.profile_data;
			test_select_val = row.test_table+'~@~'+row.test_id+'~@~'+row.script_file;
			html += '<span style="padding:4px; display:block;" class="clear" id="row_'+row.id+'" onClick="editMe(\''+row.id+'\',\''+escape(row.profile_name)+'\',\''+test_select_val+'\',\''+row.physician_id+'\',\''+row.profile_data+'\',\''+row.favorite+'\');">&bull; '+row.profile_name+' ('+row.test_name+')&nbsp;&nbsp;<span class="glyphicon glyphicon-remove link_cursor icon_delete" ></span></span>';
			cnt++;
		}
		
		$('.left_phy_tree').find('span').text('+');
		$('.div_saved_profiles').html('');
		
		$(obj).find('span').text('-');
		$(obj).parent().find('.div_saved_profiles').html(html);
		
		$('.icon_delete').click(function(e){
			e.stopPropagation();
            //fAlert('Are you sure, want to delete?');
            //deleteRecord($(this).parent().prop("id"));
            fancyConfirm('Are you sure, want to delete?', '', 'top.fmain.deleteRecord("'+$(this).parent().prop('id')+'")');
            //top.fancyConfirm('Are you sure, want to delete?', '', 'top.fmain.all_data.iframe_allforms.deleteRecord("'+$(this).parent().prop('id')+'")');
		});
		return;
	}
	});
	return;	
}

function deleteRecord(id){
	obj = document.getElementById(id);
	top.show_loading_image('hide');
	top.show_loading_image('show','400', 'Deleting Profile...');
	tr_id	= id;
	//$(obj).parent().remove();
    $(obj).parent().find('span[id="'+tr_id+'"]').remove();
	tr_id = tr_id.substr(4);
    var physician_id=$('#physician_id').val();
	$.ajax({
		type: "POST",
		url:"interpretation_profiles_ajax.php?do=DeleteProfile&pro_id="+tr_id, 
		success:function(r){
            document.forms.frm_new_profile.reset();
            document.forms.frm_new_profile.id.value='';
            $('#div_test_form').html('');
            $('#copy_profile_from').hide();
            $('#copy_profile').html('');
            $('#copy_pre_val').val('');
			top.show_loading_image('hide');
            loadSavedProfiles(physician_id);
		}
	});
	return;
}


function show_copy_profile_dd(test_master_id) {
    if(!test_master_id || test_master_id=='')return false;
    var postData={test_master_id:test_master_id};
    $.ajax({
        type: "POST",
        url:'interpretation_profiles_ajax.php?do=load_copy_profile_dd',
        dataType:'JSON',
        data:postData,
        success: function(r){
            $('#copy_profile').html('');
            if(r) $('#copy_profile_from').show();
            $('#copy_profile').append('<option value="">-SELECT-</option>'+r);
            if($('#copy_pre_val').length > 0){
                var copyPreVal = $('#copy_pre_val').val();
                if(copyPreVal !== 'undefined' && copyPreVal != ''){
                    $('#copy_profile').find("option[value="+copyPreVal+"]").prop('selected', true);
                }               
            }
        }
    });
}

function copySavedProfiles(elem) {
    var test_id_selval=$(elem).find('option:selected').data('test_ids');
    var profile_data=$(elem).find('option:selected').data('profile_data');
    var physician_id=$(elem).find('option:selected').data('physician_id');
    
    var op_val=$(elem).find('option:selected').val();
    $('#copy_pre_val').val(op_val);
    
    if(profile_data) profile_data=JSON.stringify(profile_data);
       
	id_data_arr 		= test_id_selval.split('~@~');
	val_test_table 		= id_data_arr[0];
	val_test_id 		= id_data_arr[1];
	val_script_file		= id_data_arr[2];
	
	$('#sel_test_id').val(test_id_selval);

	load_test_page(test_id_selval,profile_data,physician_id);

}