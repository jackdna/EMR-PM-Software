function submit_form(){
	if($("#auroresp_temp_name").val()==""){
		top.fAlert("Please enter template name.");
		return(false);	
	}
	$("#autoresp").submit();
}

function add_new(){
	CKEDITOR.instances['tempCont'].setData('');
	$("#autoresp")[0].reset();
	$("#auroresp_temp_status").attr('checked', false);
	$("#autoresp_tempId").val("");
	$("#autoresp_action").val("addNew");
	$('.selectpicker').selectpicker('refresh');
}

function loadTemplate(type, id){
	//This is executed when useer has saved data to load saved data
	if(js_php_arr.sel_id != '' && js_php_arr.sel_type != ''){
		$('#auto_resp_temp').find('[data-temp-type="'+js_php_arr.sel_type+'"]').addClass('in');
		js_php_arr.sel_id = '';
		js_php_arr.sel_type = '';
	}
	
	var data = getTemplateData(type, id);
	$("#auroresp_temp_name").val(data.name);
	$("#autoresp_catg").val(data.type);
	$("#auroresp_forward_email").val(data.forwarder);
	CKEDITOR.instances['tempCont'].setData(data.data);
	$("#autoresp_tempId").val(id);
	$("#autoresp_action").val("save");
	if(data.status==1){
		$("#auroresp_temp_status").attr('checked', true);
	}
	else{
		$("#auroresp_temp_status").attr('checked', false);
	}
	
	if(type=="4" || type=="5"){
		$("#forwardDiv").css('visibility', 'hidden');
	}
	else{
		$("#forwardDiv").css('visibility', 'visible');
	}
	$('.selectpicker').selectpicker('refresh');
}

function deleteTemp(ev, type, id){
	ev.stopPropagation();
	var data = getTemplateData(type, id);
	
	$("#autoresp_action").val('delete');
	$("#autoresp_tempId").val(id);
	$("#autoresp").submit();
}

function getTemplateData(type, id){
	type = parseInt(type);
	var data = "";
	switch (type){
		case 1:
			data = js_php_arr.appt_data[id];
		break;
		case 2:
			data = js_php_arr.prac_msg[id];
		break;
		case 4:
			data = js_php_arr.msg_regis[id];
		break;
		case 5:
			data = js_php_arr.msg_verify[id];
		break;
	}
	return data;
}

function set_elem_height(){
	var difference = '';
	var prnt_elem = $('.whtbox');
	var prnt_height = prnt_elem.height();
	var top_diff = $('#tempCont').offset();
	
	difference = prnt_height - parseInt((top_diff.top + 165));
	if($('#tempCont').data('height') != ''){
		difference = prnt_height - $('#tempCont').data('height');
	}
	
	if($('#tempCont').length > 0){
		$('#tempCont').data('height',difference);
		CKEDITOR.replace( 'tempCont', { width:'100%', height:difference+'px', toolbarStartupExpanded:true, toolbarCanCollapse:false} );
	}
	
	//If user have selected a template and perform any action on it i.e save,update,delete
	if(js_php_arr.sel_id && js_php_arr.sel_type){
		loadTemplate(js_php_arr.sel_type, js_php_arr.sel_id);
	}
}

var ar = [["save_template","Save","top.fmain.submit_form();"], ["add_template","Add New","top.fmain.add_new();"]];
top.btn_show("ADMN",ar);


$(function(){
	$('.collapse').on('show.bs.collapse', function(){
		$(this).prev('.panel').find(".glyphicon-menu-right").removeClass("glyphicon-menu-right").addClass("glyphicon-menu-down");
	}).on('hide.bs.collapse', function(){
		$(this).prev('.panel').find(".glyphicon-menu-down").removeClass("glyphicon-menu-down").addClass("glyphicon-menu-right");
	});
});

$(document).ready(function(){
	$('.variable_list li span').mouseover(function (){
		var text = $(this).text();
		var $this = $(this);
		var $input = $('<input type=text readonly>');
		$input.prop('value', text);
		$input.appendTo($this.parent());
		$input.focus();
		$input.select();
		$this.hide();
		$input.focusout(function(){
			$this.show();
			$input.remove();
		});
	});
	
	$("#autoresp_catg").change(function(){
		if($(this).val()=="4" || $(this).val()=="5"){
			$("#forwardDiv").css('visibility', 'hidden');
		}
		else{
			$("#forwardDiv").css('visibility', 'visible');
		}
	});
	
	set_header_title('Auto Responder');
});

$(window).load(function(){
	set_elem_height();		//Init. ck editor and its height dynamically
});
