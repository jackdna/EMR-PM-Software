// Save Test Status regarding the commercial or Medicare
var error_status = 0;		
function saveCnTests()
{
	error_status = 0;
	prac_code_err = 0;
	ins_check_err = 0;
	$('.tests_tab_row').each(function(id,elem){
		var prac_cpt_cl_val = $(elem).find('.prac_cpt_cl').val();
		if($.trim(prac_cpt_cl_val) == ""){
			top.fAlert('Please fill the practice code','',$(elem));
			error_status = 1;
			return false;					
		} 
		//Insurance Comm. checkbox
		var ins_commercial_cl_status = $('.ins_commercial_cl',$(elem)).prop('checked');
		var ins_medicare_cl_status = $('.ins_medicare_cl',$(elem)).prop('checked');
		if(ins_commercial_cl_status == false && ins_medicare_cl_status == false){
			top.fAlert('Please fill the insurance','',$(elem));
			error_status = 1;
			return false;
		}
		//Site radio box
		var ou_cl_status = $('.ou_cl',$(elem)).prop('checked');
		var od_cl_status = $('.od_cl',$(elem)).prop('checked');
		var os_cl_status = $('.os_cl',$(elem)).prop('checked');
		if(ou_cl_status == false && od_cl_status == false && os_cl_status == false){
			top.fAlert('Please select the site','',$(elem));
			error_status = 1;
			return false;	
		}
	});
	if(error_status == 1){
		return false;	
	}
	parent.parent.show_loading_image('block');
	document.edit_cpt_tests.submit();			
}		

function hgRow(ths,msg){
	error_status = 1;
	top.fAlert(msg);
	ths.css({'background-color':'#e28b8b'});	
}
		
var gl_add_counter = 1;
function add_prac_cpt_test(test_id,ths)
{
	target_elem = $(ths).parent().parent();
	html_cnt = '<tr class="tests_tab_row warning"><td><input type="hidden" name="new_cpt_code[]" value="'+gl_add_counter+'" /><input type="hidden" name="new_test_id'+gl_add_counter+'" value="'+test_id+'" /></td>';				
	html_cnt += '<td align="text-center"><div class="col-xs-2 col-xs-offset-4"><div class="checkbox checkbox-inline"><input type="checkbox" class="ins_commercial_cl" name="new_ins_commercial'+gl_add_counter+'" id="new_ins_commercial'+gl_add_counter+'" value="1" /><label for="new_ins_commercial'+gl_add_counter+'"></label></div></div></td>';
	html_cnt += '<td align="text-center"><div class="col-xs-2 col-xs-offset-4"><div class="checkbox checkbox-inline"><input type="checkbox" class="ins_medicare_cl" name="new_ins_medicare'+gl_add_counter+'" id="new_ins_medicare'+gl_add_counter+'" value="1" /><label for="new_ins_medicare'+gl_add_counter+'"></label></div></div></td>';			
	html_cnt += '<td align="center"><div class="radio radio-inline"><input type="radio" class="ou_cl" name="new_site'+gl_add_counter+'" id="new_site'+gl_add_counter+'OU" value="OU" /><label for="new_site'+gl_add_counter+'OU"></label></div></td>';	
	html_cnt += '<td align="center"><div class="radio radio-inline"><input type="radio" class="od_cl" name="new_site'+gl_add_counter+'" id="new_site'+gl_add_counter+'OD" value="OD" /><label for="new_site'+gl_add_counter+'OD"></label></div></td>';	
	html_cnt += '<td align="center"><div class="radio radio-inline"><input type="radio" class="os_cl" name="new_site'+gl_add_counter+'" id="new_site'+gl_add_counter+'OS" value="OS" /><label for="new_site'+gl_add_counter+'OS"></label></div></td>';		
	html_cnt += '<td><input type="text" class="prac_cpt_cl form-control pull-left" name="new_practice_cpt'+gl_add_counter+'" value="" />&nbsp;&nbsp;<a href="#return false;" onClick="del_cpt_test(0,\' \',\' \',this)"><img border="0" src="../../../library/images/close_small.png" alt="" /></a></td>';
	html_cnt += '</tr>';
	target_elem.after(html_cnt);
	$('.prac_cpt_cl',target_elem.next()).each(function(id,elem)
	{
		$(elem).typeahead({source:prac_code_arr});	
		
	});	
	$('.prac_cpt_cl',target_elem.next()).blur(function()
	{
		prac_cpt_cl_val = $(this).val();
		if($.inArray(prac_cpt_cl_val,prac_code_arr) == -1 && prac_cpt_cl_val != "")
		{
			$(this).val('');	
		}				
	});				
	gl_add_counter++;	
}

function del_cpt_test(test_cpt_id,prac_cpt,test_name,ths)
{
	if(test_cpt_id == 0)
	{
		$(ths).parent().parent().remove();
		return false;
	}
	top.fancyConfirm("Are you sure you want to delete practice CPT - "+prac_cpt+" from "+test_name+"?","", "window.top.fmain.deleteCptTest('"+test_cpt_id+"')");
}

function deleteCptTest(test_cpt_id) {
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Deleting Record(s)...');
	frm_data = 'pkId='+test_cpt_id+'&task=delete';
	$.ajax({
		type: "POST",
		url: "cn_tests_ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='1'){
				top.alert_notification_show('Record Deleted');
				document.del_cpt_tests.submit();
			}else{top.fAlert(d+' Record delete failed. Please try again.');}
		}
	});
}

function save_new_test_cpt()
{			
	superbill_test_val = document.add_practice_cpt.superbill_test.value;
	if($.trim(superbill_test_val) == '')
	{
		top.fAlert('Please select the test');
		document.add_practice_cpt.superbill_test.focus();
		return false;	
	}
	prac_cpt_val = document.add_practice_cpt.practice_cpt.value;
	if($.trim(prac_cpt_val) == '')
	{
		top.fAlert('Please fill the practice CPT');	
		document.add_practice_cpt.practice_cpt.focus();
		return false;
	}
	
	commercial_cpt_val = $('#commercial_cpt').prop('checked');
	medicare_cpt_val = $('#medicare_cpt').prop('checked');
	if(commercial_cpt_val == false && medicare_cpt_val == false){
		top.fAlert('Please select the insurance');
		return false;				
	}	
	var site_1 = $('#site_1').prop('checked');
	var site_2 = $('#site_2').prop('checked');
	var site_3 = $('#site_3').prop('checked');						
	if(site_1 == false && site_2 == false && site_3 == false){
		top.fAlert('Please select the site');
		return false;	
	}
	document.add_practice_cpt.submit();
}

function show_data_form(fclose){		
	var ar = "divForm_cn_test";
	if($("#"+ar).length>0){
		if(fclose==1){ 
			$("#"+ar).modal('hide');
			$("#"+ar+" form :input[type!=button]").each(function(){if($(this).attr("name").indexOf("txt_submit") == -1){ $(this).val(""); } }); //saveBtn
		}else{
			$("#"+ar).modal('show');
		}
	}	
}

$(document).ready(function(){	

	var ar = [["saveCNTests","Save","top.fmain.saveCnTests();"],["addBtn_cn","Add New","top.fmain.show_data_form();"]];
	top.btn_show("ADMN",ar);
	set_header_title('Test CPT Preference');	
	
	$('.prac_cpt_cl').each(function(id,elem){
		$(elem).typeahead({source:prac_code_arr});
	});	
	
	$('.prac_cpt_cl').blur(function(){
		prac_cpt_cl_val = $(this).val();
		if($.inArray(prac_cpt_cl_val,prac_code_arr) == -1 && prac_cpt_cl_val != "")
		{
			$(this).val('');	
		}				
	});	

	$("#superbill_test").bind("change", function(){
			var x = $.trim($(this).find("option:selected").text());			
			if(x=="Ophthalmoscopy Optic Nerve & Macula" ||
			 x=="Ophthalmoscopy Retina drawing and scleral depression"){
				$("#rdsite_2, #rdsite_3").hide();	
			}else{
				$("#rdsite_2, #rdsite_3").show();
			}
		});	
	
	top.show_loading_image('none');	
});


