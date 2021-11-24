function selectTemplate(id, name, tab){

	switch(tab){
		case 'collection':
			$('#templateName').val(name);
		break;
		
		case 'consent':
			$('#consent_form_name').val(name);
			$('#consent_form_id').val(id);
		break;
	}
	$('#edit_id').val(id);
	top.show_loading_image('show');
	$('#perform_action').val('update');
	document.template_form.submit();
}