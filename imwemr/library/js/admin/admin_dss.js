var startVal = 0;
var newVals = 0;
var hideLoader = true;

/*Default Configurations for ajax request*/
$.ajaxSetup({
	url: top.JS_WEB_ROOT_PATH+'/interface/admin/dss/ajax.php',
	method: 'POST',
	cache: false,
	beforeSend: function(){top.show_loading_image('show');},
	complete: function(){if(hideLoader === true) top.show_loading_image('hide');}
});

/* Handle Credentials saving operation*/
function saveCredentials() {
    var cred_record_id = $('#cred_record_id').val();
    var accessCode = $('#accessCode').val();
    var verifyCode = $('#verifyCode').val();
    var menuContext = $('#menuContext').val();
    var url = $('#url').val();
	
	var paramerter = {
        record_id: cred_record_id,
        accessCode: accessCode, 
        verifyCode: verifyCode, 
        menuContext: menuContext,
        appUrl: url, 
        action: 'saveCredentials'
    };
    
	$.ajax({
		data: paramerter,
		success: function(resp){
			window.location.href = top.JS_WEB_ROOT_PATH+'/interface/admin/dss/index.php?save=2';
		}
	});
}


$(document).ready(function(){
	set_header_title('DSS Settings');
	
	/* Save API Credentials */
	$( '#saveCredentials' ).on( 'click', saveCredentials);
	
	top.show_loading_image('hide');
});