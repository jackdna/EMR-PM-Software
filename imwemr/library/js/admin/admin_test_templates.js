function load_testTemplates(){
	var data = "action=fetch_list";
	$.ajax({
		url: 'test_templates_ajax.php',
		data: data,
		method: 'POST',
		success: function(resp){
			top.show_loading_image('hide');
			$("#tests_data").html(resp);
			switch_val();
		}
	});
}
function saveFormData(){
	top.show_loading_image('hide');
	var data = $("#test_templates").serialize();
	$.ajax({
		url:'test_templates_ajax.php',
		data: data,
		method: 'POST',
		beforeSend: function(obj){
			top.show_loading_image('show','300', 'Loading Records...');
		},
		success: function(resp){
			load_testTemplates();
			top.alert_notification_show("Test Templates Saved Successfully.");
			top.show_loading_image('hide');
		}
	});
}
function switch_allVals(obj){
	if($(obj).is(':checked')) {
		$(".test_chkbx").prop("checked",true);
	}
	else{
		$(".test_chkbx").prop("checked",false);
	}
}
function switch_val(){
	$("#select_all").prop("checked", true);
	$(".test_chkbx").each(function(){
		if(!$(this).is(":checked")) {
			$("#select_all").prop("checked", false);
		}
	});
}

function EditRecord(ed,pkId,t_manager,version){
	if(typeof(version)!='undefined' && parseInt(version)>0){
		//FOR R8 NEW TEMPLATE BASED TESTS
		addNew(pkId,t_manager,version);
	}else{
		//FOR ALREADY CREATED TESTS BEFORE R8
		var modal_title = '';
		if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';
			$('#t_template_id').val(pkId);
			if(typeof(t_manager)!='undefined'){
				t_manager=='1' ? document.add_edit_frm.t_manager.checked=true : document.add_edit_frm.t_manager.checked=false;
			}
			$("#temp_name").val(ed);
		}
		else {
			modal_title = 'Add New Record';
			$('#t_template_id').val('');
			document.add_edit_frm.reset();
		}
		$('#myModal .modal-header .modal-title').text(modal_title);
		$('#myModal').modal('show');
	}
}

function addNew(pkId,t_manager,version){ //alert('pkid='+pkId+', t_manager='+t_manager+', version='+version);
	var modal_title = '';
	if(typeof(pkId)!='undefined' && parseInt(pkId)>0){modal_title = 'Edit Record';
		$('#t_template_id').val(pkId);
		if(typeof(t_manager)!='undefined'){
			t_manager=='1' ? $('#t_manager1').prop('checked',true) : $('#t_manager1').prop('checked',false);
		}
	}else{
		modal_title = 'Add New Record';
		$('#t_template_id').val(pkId);
		//document.add_edit_frm_v2.reset();
	}
	$('#myModal2 .modal-header .modal-title').text(modal_title);
	template_test_url = '../../tests/test_template_custom.php?tId='+pkId;

	$('#myModal2').one('shown.bs.modal', function (e) {// this handler is detached after it has run once
		$('#template_test_frame').attr('src',template_test_url);
		footer_btn_ttemplates('hide');
	}).on('hidden.bs.modal', function(e) {// this handler is detached after it has run once
		$('#template_test_frame').attr('src','about:blank');
		load_testTemplates();
		footer_btn_ttemplates();
	}).modal({backdrop: 'static'});
}
	
function saveTemplate(){
	df=document.add_edit_frm;
	var tempId = $("#t_template_id").val();
	var tempName = $("#temp_name").val();
	var t_manager = df.t_manager.checked ? '1' : '0';
	var action = "";
	if(tempId=="") {action="addTemplate";}
	else{action="editTemplate";}
	var data = "action="+action+"&tempName="+tempName+"&templateId="+tempId+"&t_manager="+t_manager;
	$.ajax({
		url:'test_templates_ajax.php',
		data: data,
		method: 'POST',
		beforeSend: function(obj){
			$('#myModal').modal('hide');
			top.show_loading_image('show','300', 'Loading Records...');
		},
		success: function(resp){
			load_testTemplates();
			if (resp=="success") {
				top.alert_notification_show("Template Saved Successfully.");
			}
			else{
				top.alert_notification_show("Error in Saving Template.");
			}
			top.show_loading_image('hide');
			$('#myModal').modal('hide');
		}
	});
}

function footer_btn_ttemplates(flag){
	if (typeof(flag)=='undefined') flag = 'show';
	if(flag=='show'){
	  if(typeof(isDssEnable) !== 'undefined' && isDssEnable == 1) {
		var ar = [["addTestTemplatesTab","Add New","top.fmain.addNew();"],
			  ["span","20"],
			  ["saveTestTemplatesTab","Save","top.fmain.saveFormData();"],
  			  ["span","20"],
			  ["testPreferences","Test Preferences","top.fmain.testPreferences();"],
			  ["dssServices","DSS Services","top.fmain.dssServices();"]
			 ];
	  } else {
		var ar = [["addTestTemplatesTab","Add New","top.fmain.addNew();"],
			  ["span","20"],
			  ["saveTestTemplatesTab","Save","top.fmain.saveFormData();"],
  			  ["span","20"],
			  ["testPreferences","Test Preferences","top.fmain.testPreferences();"],
			 ];
	  }
		top.btn_show("ADMN",ar);	
	}else{
		var ar = [];
		top.btn_show("ADMN",ar);	
	}
}
$(document).ready(function(){
	load_testTemplates();
	footer_btn_ttemplates();
	set_header_title('Test Templates');	
});

function dssServices(){
	window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/chart_notes/dss_services.php';	
}