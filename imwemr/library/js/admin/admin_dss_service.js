function addnew(){
	document.forms.frm_new_profile.reset();
	document.forms.frm_new_profile.id.value='';
	// $('#div_test_form').html('');
}

function load_test_page(id){
	$('#test_id').val(id);
}

function saveDssFormData(){
	id = $('#id').val();
	svcName = $('#svcName').val();
	orderableItem = $('#orderableItem').val();
	svcIen = $('#dssServiceSpeciality').val();
	test_id = $('#test_id').val();

	if(svcName=='' || orderableItem=='' || svcIen==''){
		fAlert('Please select DSS service speciality.');
		return;
	}else if(test_id==''){
		fAlert('Please select Test.');
		return;
	}

	top.show_loading_image('hide');
	top.show_loading_image('show','400', 'Saving data...');
	
	$.ajax({
		url: top.JS_WEB_ROOT_PATH + "/interface/admin/chart_notes/dss_services_ajax.php",
		type: 'POST',
		data: {
			do: 'dssSaveData',
			id: id,
			svcName: svcName,
			orderableItem: orderableItem,
			svcIen: svcIen,
			test_id: test_id
		}
	}).done(function(response) {
		top.show_loading_image('hide');
		var res = $.parseJSON(response);
		if(res.status == 'error') {
			top.fAlert(res.msg);
			return false;
		}
		window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/chart_notes/dss_services.php';
	});
}

$(document).ready(function(e) {
	var ar = [["addTestTemplatesTab","Add New","top.fmain.addnew();"],
		  ["span","20"],
		  ["saveDssTestServiceTab","Save","top.fmain.saveDssFormData();"],
  		  ["span","20"],
		  ["loadTestTemplatesTab","Test Templates","top.fmain.loadTestTemplates();"]
		 ];
	top.btn_show("ADMN",ar);
	set_header_title('DSS Test / Service');	

});

function loadTestTemplates(){
	window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/chart_notes/test_templates.php';	
}

function getServiceData(){
	var name = $('#dssServiceSpeciality option:selected').attr('data-svcName');
	$('#svcName').val(name);
	var orderableItem = $('#dssServiceSpeciality option:selected').attr('data-orderableItem');
	$('#orderableItem').val(orderableItem);
}

function loadServData(id,test_id,service_ien,service_orderable_item,service_name) {
	if(test_id=='' || service_ien =='' || service_orderable_item =='' || service_name ==''){
		fAlert('Missing form values.');
		return;
	}
	$('#id').val(id);
	$('#sel_test_id').val(test_id);
	$('#test_id').val(test_id);
	$('#dssServiceSpeciality').val(service_ien);
	$('#orderableItem').val(service_orderable_item);
	$('#svcName').val(service_name);
}

function deleteRecord(id) {
	top.show_loading_image('show');
	$.ajax({
		url: top.JS_WEB_ROOT_PATH + "/interface/admin/chart_notes/dss_services_ajax.php",
		type: 'POST',
		data: {
			id: id,
			do: 'dssDelData'
		}
	}).done(function(response) {
		top.show_loading_image('hide');
		window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/chart_notes/dss_services.php';
	});
}