// --- Header Func() ---
var global_obj = {};

function reload_frame(url){
	if(!url){
		url = window.location.href;
	}
	if(url.indexOf("&xml_id") > 0){
		url_arr = url.split('&xml_id');
		url = url_arr[0];
	}
	
	var opt_arr = new Array;	
	$('#ccd_xml option').each(function(id,elem){
		if($(elem).prop('selected') === true){
			var value = $(elem).val();
			opt_arr.push(value);	
		}
	});
	
	var sel_val = opt_arr.join(',');
	url = url+"&xml_id="+sel_val;
	window.location.href = url;
}

function submit_frame(){
	$('#frm').submit();
}

function check_form(form_name){
	var checked_arr = new Array;
	$('input[type=checkbox]').not('[name^="compliant"],[name^="chk_ocular"]').each(function(id,elem){
		if($(elem).is(':checked')){
			var elem_id = $(elem).attr('id');
			checked_arr.push($(elem).val());
		}
	});
	if(checked_arr.length == 0){
		top.fAlert('Please select a record to continue');
		return false;		
	}else{
		return true;
	}
	
}

function import_fun(){
	show_loading_img('block','Please wait....');	
	var imgVal = $('#u_img').val(); 
        if(imgVal==''){
			fAlert('Please provide a file to continue.');
			return false;
		}else{
			document.frm_import_medication.action = 'index.php?upload_file=yes&showpage='+global_php_var.curr_tab;
			document.frm_import_medication.submit();
		}
}

function change_tab(tab_name){
	var tab = $(tab_name).attr('id');
	var msg = '';
	if(tab == 'allergies'){
		msg = 'Please wait while Allergies retrieving information!';
	}else if(tab == 'medications'){
		msg = 'Please wait while Medication is retrieving information!';
	}else if(tab == 'problem_list'){
		msg = 'Please wait while Problem List retrieving information!';
	}
	show_loading_img('block',msg);	
	var url = global_php_var.webroot+'/interface/Medical_history/import/index.php?showpage='+tab+'&xml_id='+global_php_var.xml_id;
	window.location.href= url;
	//reload_frame(url);
}

function show_loading_img(action,msg){
	$('#div_loading_text').html(msg);
	$('#div_loading_image').css('display',action);
}

function setHeight() {
	var div_position = $('#content_block').position();  
	var footer_height = $('#core_buttons_bar').innerHeight();  
    windowHeight = parseInt($(window).innerHeight() - div_position.top - footer_height - 20);
	if($('#content_block').height() > 300){
		 $('#content_block').css('height', windowHeight).css('overflow-y','scroll').css('overflow-x','hidden');
	}
}


// --- Allergies Func() ---
function select_xml_chk(class_name, obj){
	if($(obj).is(":checked")){
		$('.'+class_name).each(function(id, elem) {
            $(elem).prop('checked',true);
        });
	}else{
		$('.'+class_name).each(function(id, elem) {
            $(elem).prop('checked',false);
        });
	}
}

$(window).resize(function() {
    setHeight();
});


$(function(){
	setHeight();	
	$('#merge').hide();
	$('.nav-tabs').on('click', 'li', function() {
		$('.nav-tabs li.active').removeClass('active');
		$(this).addClass('active');
	});
	$(".panel button.close span").on('click',function (e) {
		$('#upload_err').slideUp('slow');
	});
	
	//Displays sample csv format table
	$( "#show_csv_sample" ).bind({
		click: function() {
			var content =  '<table class="table table-bordered table-striped table-condensed">';
			content += 	'<tr class="text-nowrap">';
			content += 		'<th>Ocular</th>';	
			content += 		'<th>Name</th>';	
			content += 		'<th>Dosage</th>';	
			content += 		'<th>Sig.</th>';	
			content += 		'<th>Qty</th>';	
			content += 		'<th>Refills</th>';	
			content += 		'<th>Prescribed By</th>';	
			content += 		'<th>Begin Date</th>';	
			content += 		'<th>End Date</th>';	
			content += 		'<th>Comments</th>';	
			content += 	'</tr>';
			content += 	'<tr>';
			content += 		'<td></td>';	
			content += 		'<td>Ceftin</td>';	
			content += 		'<td class="text-right">1</td>';	
			content += 		'<td class="text-right">1</td>';	
			content += 		'<td class="text-right">1</td>';	
			content += 		'<td class="text-right">1</td>';		
			content += 		'<td></td>';	
			content += 		'<td></td>';	
			content += 		'<td>11/4/2010</td>';	
			content += 		'<td></td>';	
			content += 	'</tr>';
			content += 	'<tr>';
			content += 		'<td class="text-right">1</td>';	
			content += 		'<td>neomycin</td>';	
			content += 		'<td class="text-right">3</td>';	
			content += 		'<td class="text-right">1</td>';	
			content += 		'<td class="text-right">1</td>';	
			content += 		'<td></td>';	
			content += 		'<td></td>';	
			content += 		'<td></td>';	
			content += 		'<td>11/4/2010</td>';	
			content += 		'<td></td>';	
			content += 	'</tr>';	
			content += '</table>';
			show_modal('sample_csv_format','Sample CSV Format',content,'','','modal-lg');
		}
	});
	
	$('.nav-tabs #'+global_php_var.curr_tab+'').addClass('active');
	$('.datepicker').datetimepicker({timepicker:false,format:window.opener.top.jquery_date_format,autoclose: true,scrollInput:false});
	show_loading_img('none');	
	if(global_php_var.curr_tab == 'allergies' || global_php_var.curr_tab == 'problem_list'){
		$('#consolidate').show();
	}
	
	if(global_php_var.page_request == 'merge'){
		if(global_php_var.curr_tab == 'problem_list'){
			global_php_var.curr_tab = 'Problem List';
		}
		var btn_val = 'Merge '+global_php_var.curr_tab;	
		$('#merge').val(btn_val).show();
		$('#consolidate').hide();
	}
})