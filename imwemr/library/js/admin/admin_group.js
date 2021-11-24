var boolVal=false;
	var state_length = temp_arr['state_length'];
	var state_label = temp_arr['state_label'];
	var zip_length = temp_arr['zip_size'];
	var int_country = temp_arr['int_country'];
	var zip_ext = temp_arr['zip_ext_status'];
	var webroot = temp_arr['webroot'];
	var wn_height = temp_arr['wn_height'];
	function check_data(){
		
		var foucs_val = false;
		var msg = '';
		var facZip = groups.group_Zip.value;		
		
		if(groups.name.value==""){
			//msg = msg + '•   Please enter Group name.\n';
			msg = msg + temp_arr.AlertMessages.Name;
			groups.name.className = "form-control mandatory";
			if(foucs_val == false){
				groups.name.focus();
				foucs_val = true;
			}
		}		
		if(groups.group_NPI.value==""){
			//msg = msg + '•   Please enter Group NPI#.\n';
			msg = msg + temp_arr.AlertMessages.Group_NPI; 
			groups.group_NPI.className = "form-control mandatory";
			if(foucs_val == false){
				groups.group_NPI.focus();			
				foucs_val = true;
			}
		}
		if(groups.group_NPI.value.length < 10 && groups.group_NPI.value != ''){
			//msg = msg + '•   Please enter NPI#  as exactly 10 characters.\n'; 
			msg = msg + temp_arr.AlertMessages.Group_NPI_Value_Length;
			groups.group_NPI.className = "form-control mandatory";
			if(foucs_val == false){
				groups.group_NPI.focus();
				foucs_val = true;
			}
		}
		if(groups.group_Federal_EIN.value==""){
			//msg = msg + '•   Please enter Group Federal EIN#.\n'; 
			msg = msg + temp_arr.AlertMessages.Group_Federal_EIN;
			groups.group_Federal_EIN.className = "form-control mandatory";
			if(foucs_val == false){
				groups.group_Federal_EIN.focus();
				foucs_val = true;
			}
		}
		if(groups.group_Address1.value==""){
			//msg = msg + '•   Please enter Group Address1.\n';
			msg = msg + temp_arr.AlertMessages.Group_Address;
			groups.group_Address1.className = "form-control mandatory";
			if(foucs_val == false){
				groups.group_Address1.focus();
				foucs_val = true;
			}
		}
		if(groups.group_Zip.value==""){
			//msg = msg + '•   Please enter Group Zip code.\n';
			msg = msg + temp_arr.AlertMessages.Group_Zip;
			groups.group_Zip.className = "form-control mandatory";
			if(foucs_val == false){
				groups.group_Zip.focus();
				foucs_val = true;
			}
		}
		else{
			groups.group_Zip.onChange = '';
			var temp = zip_vs_state(groups.group_Zip.value,'add_groups');
			
		}
		if(groups.group_City.value==""){
			//msg = msg + '•   please enter Group City name.\n'; 
			msg = msg + temp_arr.AlertMessages.Group_City;
			groups.group_City.className = "form-control mandatory";
			if(foucs_val == false){
				groups.group_City.focus();
				foucs_val = true;
			}
		}
		if(groups.group_State.value==""){
			//msg = msg + '•   Please enter Group State name.\n';
			msg = msg + temp_arr.AlertMessages.Group_State;
			groups.group_State.className = "form-control mandatory";
			if(foucs_val == false){
				groups.group_State.focus();
				foucs_val = true;
			}
		}
		if(groups.Contact_Name.value==""){
			//msg = msg + '•   Please enter Contact Name.\n';
			msg = msg + temp_arr.AlertMessages.Contact_Name;
			groups.Contact_Name.className = "form-control mandatory";
			if(foucs_val == false){
				groups.Contact_Name.focus();
				foucs_val = true;
			}
		}		
		if(groups.group_Telephone.value==""){
			//msg = msg + '•   Please enter Group Telephone.\n'; 
			msg = msg + temp_arr.AlertMessages.Group_Telephone;
			groups.group_Telephone.className = "form-control mandatory";
			if(foucs_val == false){
				groups.group_Telephone.focus();
				foucs_val = true;
			}
		}		
		var testresults = false;
		var str = document.groups.group_Email.value
		if(str != ''){
			testresults = checkemail(str);
		}else{
			testresults = true;
		}
		if(testresults == false){
			//msg = msg + '•  Please enter a valid email address.\n';
			msg = msg + temp_arr.AlertMessages.Group_Email;
			if(foucs_val == false){
				groups.group_Email.select();
				foucs_val = true;
			}
		}
		if(facZip != ''){
			
		}		
		
		if(msg == ''){	
			if(document.getElementById("hiddZipCodeValid").value=='yes') {
				top.show_loading_image('block');
				document.groups.submit();
			}
			top.show_loading_image('block');
			document.groups.submit();
		}else{			 
			 top.show_loading_image('none');	
			 //alert ('ttt'+msg+'==');
			 top.fAlert(msg);
			 return false;
		}		    
	}	

	function checkemail(str){
		var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		if (filter.test(str))
			testresults=true
		else{
			testresults=false
		}
		return (testresults)
	}
	
	function changeClass(obj){
		if(obj.value != ''){
			obj.className = 'form-control';
		}
	}
	top.show_loading_image('none');
	
	function confCheck(check){
		if(check == true){
			document.getElementById('allowInstitution').value = '1';
		}
		else if(check == false){
			document.getElementById('allowInstitution').value = '0';
		}
		check_data();
	}
	
	function checkInstitution(){
		if((document.getElementById('txtgroup_institution').value == 0) && (document.getElementById('alreadyInstitution').value == 1) && (document.getElementById('group_institution').checked == true) && (document.getElementById('alreadyInstitutionGrpId').value != '') && (document.getElementById('alreadyInstitutionGrpId').value != document.getElementById('gro_id').value)){						
			var arrConfFun = new Array();
			arrConfFun[0] = "window.top.fmain.confCheck(true)";
			arrConfFun[1] = "window.top.fmain.confCheck(false)";
			var dd = top.fancyConfirm(temp_arr.AlertMessages.Check_Institution,"", arrConfFun[0], arrConfFun[1]);			
		}
		else{
			if(document.getElementById('group_institution').checked== true)
			{
				confCheck(true);
			} else {
				confCheck(false);
			}
		}
	}

	//to swap the layout divs color
	function change_layout_color(div_id){
		arr_blocks = new Array('groupInfo_table','mailingAdd_table','contacts_table','HouseInfo','accessTable','remitte_address');
		section_highlight(div_id,arr_blocks);
	}
	function set_phone_format_js(obj,phone_format){
		var refinedPh = $(obj).val();
		refinedPh = refinedPh.replace(/[^0-9+]/g,"");
		$(obj).val(refinedPh);
		set_phone_format(obj,phone_format,top.phone_length,'phone','form-control mandatory');
	}
	
	function get_anes_npi(){
		var npi_val = $('#anesthesia_npi_html').find('input[id=optional_anes_npi]').val();
		$('#group_form').find('input[name=optional_anes_npi]').val(npi_val);
	}
	
	function show_anes_npi(e,obj){
		var val = $('#group_form').find('input[name=optional_anes_npi]').val();
		var elem = $('#group_anesthesia').prop('checked');
		if(elem === true){
			if($(obj).data("bs.popover")){
				$(obj).popover('destroy');
				return false;
			}
			
			$(obj).popover({
				trigger:'focus',
				html: true,
				placement: 'bottom',
				content: function () {
					return '<div class="row"><div id="anesthesia_npi_html" class="col-sm-12 form-inline"><label for="optional_anes_npi">Optional Anesthesia NPI</label><input id="optional_anes_npi" type="text" class="form-control" value="'+val+'" onblur="get_anes_npi()"></div></div>';
				}
			});
			$(obj).popover('show');
			
			$(obj).on('hide.bs.popover', function (){
				get_anes_npi()
			});
		}else{
			if($(obj).data("bs.popover")){
				$(obj).popover('destroy');
			}
			top.fAlert('This practice group is not marked as Anesthesia.');
		}
	}
	
	
	function checkemail(str){
		var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		if (filter.test(str))
			testresults=true
		else{
			testresults=false
		}
		return (testresults)
	}
	
	function changeClass(obj){
		if(obj.value != ''){
			obj.className = 'form-control';
		}
	}
	top.show_loading_image('none');

	function checkInstitution_email(){
		var foucs_val = false;
		var msg = '';
		var testresults = false;
		var str = document.groups.config_email.value
		if(str != ''){
			testresults = checkemail(str);
		}
		
		if(str != '' && testresults == false){
			//msg = msg + '•  Please enter a valid email address.\n';
			msg = msg+  '-Please enter a valid Email Address<br>';
			if(foucs_val == false){
				groups.config_email.focus();
				foucs_val = true;
			}
		}
		
		if(msg == ''){	
			document.groups.submit();
		}else{			 
			 parent.show_loading_image('none');	
			 top.fAlert(msg);
			 return false;
		}	
	}
	
	// JavaScript Document
	document.onkeydown = keyCatcher;
	function keyCatcher() 
	{
		var e = event.srcElement.tagName;
		if (event.keyCode == 8 && e != "INPUT" && e != "TEXTAREA") 
		{
			event.cancelBubble = true;
			event.returnValue = false;
		}
	}
	
	function confirm_del(id){
		var sel_id = new Array();
		$('.chk_sel').each(function(id,elem){
			if($(elem).prop('checked') === true){
				var value = $(elem).val();
				sel_id.push(value);
			}
		});
		
		if(sel_id.length > 0){
			var tmp = temp_arr['delete_msg'];
			var reason_field = tmp + "<br /><form name='del_group_form_"+id+"' id='del_group_form_"+id+"' method='POST' action=''>Reason: <textarea name='group_del_reason' id='group_del_reason' class='form-control' style='vertical-align:text-top;' onblur='top.fmain.onBlur_reason(this.value);'></textarea><input type='hidden' name='gro_id' id='gro_id' value='"+id+"'></form>";
			top.fancyConfirm(reason_field,"", "top.fmain.delete_group('"+sel_id.join(',')+"');");
		}else{
			top.fAlert('Please select atleast one group to continue.');
			return false;
		}
		
		
	}
		
	function delete_group(id){
		var del_reason = $("#hidd_reason_text").val();
		if($.trim(del_reason)==""){
			top.fAlert("Please enter reason for deletion.");	
		}else{
			var post_data = "ajax_request=yes&group_del_reason="+del_reason+"&gro_id="+id;
			$.ajax({
					url:top.JS_WEB_ROOT_PATH+'/interface/admin/groups/ajax.php',
					data:post_data,
					method:'POST',
					beforeSend: function(){
						top.show_loading_image('show');
					},
					success: function(resp,status){
						dt = resp;
						
						top.closeDialog();
						top.show_loading_image('hide');
						
						if(dt=="error"){
							top.fAlert("Group not deleted.");
						}
						else{
							top.fmain.location.href= top.JS_WEB_ROOT_PATH + '/interface/admin/groups/index.php'
						}
					}
			});
		}
	}

	function onBlur_reason(Reasonval){
		$("#hidd_reason_text").val(Reasonval);
	}
	
	function get_group_listing(grp_id){
		var frm_data = 'ajax_request=yes&get_listing='+grp_id;
		$.ajax({
			url: top.JS_WEB_ROOT_PATH+"/interface/admin/groups/ajax.php",
			type:'POST',
			data: frm_data,
			success:function(response){
				$('#group_tbl_body').html(response);
			}
		});	
	}
	
	//Multiple NPI functions
	function display_npi_div(){
		var default_no='';
		var saved_npi=0;
		var group_id = temp_arr.group_selected_id;
		
		var post_data = "ajax_request=yes&npi_request=yes&npi_mode=view&ins_grp_arr="+temp_arr.ins_drop_down+"&group_id="+group_id;
		$.ajax({ 
			type: "POST",
			data:post_data,
			url: top.JS_WEB_ROOT_PATH+"/interface/admin/groups/ajax.php",
			success: function(data){
				if (data.indexOf('~') >= 0){
					var arrData = data.split('~');
					if(arrData[1]>0){
						$('#table_npi').html(arrData[0]);
						$('#totNPIRows').val(arrData[1]);
						$('#default_npi_num').val(arrData[2]);	
						$('.selectpicker').selectpicker('refresh');						
					}
				}	
			}
		});	

		if(saved_npi<=0){
			addNPIRows(0);
		}
		$('#npi_div').modal('show');
		var btn_array = [['Save','','top.fmain.save_npi();']];
		top.fmain.set_modal_btns('npi_div .modal-footer:first',btn_array);
		set_modal_height('npi_div');
		$('.selectpicker').selectpicker('refresh');
	}

	function addNPIRows(rowNo){
		var rowData='';
		if(rowNo>0){
			var imgObj = document.getElementById("add_npi_row"+rowNo);
			imgObj.title = 'Delete Row';
			imgObj.src = top.JS_WEB_ROOT_PATH+'/library/images/closerd.png';
			imgObj.onclick=function(){ 
				$("#npiRow"+rowNo).remove(); 
			}
		}
		
		i=rowNo+1;
		if(rowNo>0){ imgObj.id=i;}
		
		var ins_type_options = '';
		$.each(temp_arr.ins_drop_down,function(id,val){
			ins_type_options += '<option value="'+val+'">'+val+'</option>';
		});
		
		rowData+='<tr id="npiRow'+i+'"><td><input type="text" class="form-control" name="npi_name'+i+'" id="npi_name'+i+'" value=""/></td>';
		rowData+='<td><select name="ins_type'+i+'" id="ins_type'+i+'" class="selectpicker" data-width="100%" data-size="5" data-title="Please select" data-container="#select_box">';
		rowData+=ins_type_options;
		rowData+='</select></td>';
		rowData+='<td class="text-center"><div class="radio"><input type="radio" name="default_npi" id="default_npi'+i+'" value="1" onClick="$(\'#default_npi_num\').val('+i+');" /><label for="default_npi'+i+'"></label></div></td>';
		rowData+='<td class="pt10 text-center pointer" style="vertical-align:middle"><img id="add_npi_row'+i+'" src="'+top.JS_WEB_ROOT_PATH+'/library/images/add_icon.png" alt="Add More" onClick="addNPIRows('+i+');" ></td>';
		rowData+='</tr>';

		if(i=='1'){
			$('#table_npi').html(rowData);
		}else{
			$("#npiRow"+rowNo).after(rowData);
		}

		$('#totNPIRows').val(i);
		$('.selectpicker').selectpicker('refresh');
		set_modal_height('npi_div');
	}


	function save_npi(){
		parent.show_loading_image('show');
		var frm_data = $("#form_multiple_npi").serializeArray();
		$.ajax({ 
			type: "POST",
			url: top.JS_WEB_ROOT_PATH+"/interface/admin/groups/ajax.php",
			data:frm_data,
			success: function(data){
					var arr = data.split('~');
					if(arr[0]=='parent_npi_saved' && arr[1]!=''){
						$('#group_NPI').val(arr[1]);
					}
					top.fAlert('NPI list saved successfully!');
					parent.show_loading_image('hide');
					$('#npi_div').modal('hide');
			}
		});	
	}	
	
	
	function removeNPI(rowNo){
		parent.show_loading_image('show');
		var id_val = $('#id'+rowNo).val();
		if(id_val>0){
			var post_data = 'ajax_request=yes&npi_request=yes&npi_mode=delete&id='+id_val;
			$.ajax({ 
				type: "POST",
				url: top.JS_WEB_ROOT_PATH+"/interface/admin/groups/ajax.php",
				data:post_data,
				success: function(data){
					$("#npiRow"+rowNo).remove();
					parent.show_loading_image('hide');
				}
			});
		}
		parent.show_loading_image('hide');
	}
	
	set_header_title('Business Unit');
		
	var ar = [["new_entry","Add New","top.fmain.location.href = '"+top.JS_WEB_ROOT_PATH+"/interface/admin/groups/index.php?addnew=y';"],['delete','Delete','top.fmain.confirm_del();']];
	top.btn_show("ADMN",ar);

	$(function(){
		if(grp_email.length > 0){
			$("#email_config_form").modal('show');
		}else{
			$("#group_form").modal('show');
			if($('#loginLegalNotices').length > 0){
				CKEDITOR.replace( 'loginLegalNotices', { width:'100%', height:'200px', toolbarStartupExpanded:false} );
			}
		}
		
		if($("#group_form").data('bs.modal')){
			var btn_array = [['Save','','top.fmain.checkInstitution();']];
			top.fmain.set_modal_btns('group_form .modal-footer',btn_array);
		}
		
		if($("#email_config_form").data('bs.modal')){
			var btn_array = [['Done','','top.fmain.checkInstitution_email();']];
			top.fmain.set_modal_btns('email_config_form .modal-footer',btn_array);
		}
		
		set_modal_height('group_form');
	});
	
	$(document).ready(function(){
		get_group_listing(temp_arr['group_selected_id']);
		check_checkboxes();
	});
	
	$(window).resize(function(){
		set_modal_height('group_form');
	});	
	