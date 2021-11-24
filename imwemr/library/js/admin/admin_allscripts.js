var startVal = 0;
var newVals = 0;
var hideLoader = true;

/*Default Configurations for ajax request*/
$.ajaxSetup({
	url: top.JS_WEB_ROOT_PATH+'/interface/admin/allscripts/ajax.php',
	method: 'POST',
	cache: false,
	beforeSend: function(){top.show_loading_image('show');},
	complete: function(){if(hideLoader === true) top.show_loading_image('hide');}
});

function syncData()
{
	var syncOpt = $( '#dictionaryName' ).val();
	
	var paramerter = '';
	if( syncOpt == 'problems' ){
		paramerter = {action: 'syncProblem', start: startVal, newCounter: newVals};
	}
	else if( syncOpt == 'icd10' ){
		paramerter = {action: 'syncICd10', start: startVal, newCounter: newVals};
	}
	else{
		paramerter = {action: 'syncData', option: syncOpt};
	}
	
	var ajaxProgressData = '';
	var msgDiv = $( '#div_loading_image #div_loading_text', top.document);
	
	$('#div_loading_image #div_loading_text', top.document).show();
	$(msgDiv).empty();
	
	var htmlStr = '<div class="row"><div class="col-sm-12" id="titleMsg"></div><div class="col-sm-12" id="problemMsg"></div><div class="col-sm-12" id="problemNew"></div></div>';
	$('#div_loading_image #div_loading_text', top.document).html(htmlStr);
	
	$.ajax({
		xhr: function() {
			var xhr = new XMLHttpRequest();
		
			xhr.addEventListener("progress", function(){
				var resp = xhr.responseText;
				if( resp.indexOf( '~~~' ) === -1 )
				{
					resp = resp.substring(ajaxProgressData.length);
					resp = resp.split( '~~' );
					resp = resp.pop();
					resp = jQuery.parseJSON(resp);
					
					if( resp.type == 'progress' )
					{
						console.log($( '#div_loading_image #div_loading_text #titleMsg' , top.document));
						$( '#div_loading_image #div_loading_text #titleMsg' , top.document).html( resp.message + '...<br />' );
					}
					else if( resp.type == 'problemProgress' )
					{
						var msg = resp.data;
						console.log($( '#div_loading_image #div_loading_text #problemMsg',top.document));
						$( '#div_loading_image #div_loading_text #problemMsg',top.document).html('Problems processed: '+msg.processed);
						$( '#div_loading_image #div_loading_text #problemNew',top.document).html('New entries added: '+msg.added);
					}
					return false;
					ajaxProgressData = xhr.responseText;
				}
			}, false);
		   return xhr;
		},
		data: paramerter,
		success: function(resp){
			if( ajaxProgressData.length < resp.length)
				resp = resp.substring(ajaxProgressData.length);
			resp = resp.split( '~~~' );
			
			if( resp.length >1 )
				resp = resp.pop();
			resp = $.trim(resp);
			resp = jQuery.parseJSON(resp);
			if(resp.type == 'continue' )
			{
				hideLoader = false;
				var msg = resp.data;
				startVal = msg.processed;
				newVals = msg.added;
				$( '#syncDictionary' ).trigger( 'click' );
			}
			else if(resp.type=='success')
			{
				hideLoader = true;
				window.location.href = top.JS_WEB_ROOT_PATH+'/interface/admin/allscripts/index.php?save=1';
			}
			else
			{
				top.fAlert(resp.message);
				hideLoader = true;
			}
		}
	});
}

/* Handle Credentials saving operation*/
function saveCredentials(){
	
	var appName		= $( '#appname' ).val();
	var userName	= $( '#username' ).val();
	var passWord	= $( '#password' ).val();
	var url			= $( '#url' ).val();
	var record_id	= $( '#cred_record_id' ).val();
	
	var ubqAppName	= $( '#ubqAppname' ).val();
	var ubqUserName	= $( '#ubqUsername' ).val();
	var ubqPassWord	= $( '#ubqPassword' ).val();
	var ubqUrl		= $( '#ubqUrl' ).val();
	var ubqEnabled	= $( '#ubqEnabled' ).is(':checked');

	var useOrdId	= $( '#useOrdId' ).is(':checked');
	var ehrUsername = $( '#ehrUsername' ).val();
	var ehrPassword = $( '#ehrPassword' ).val();
	
	var paramerter = {app: appName, appuser: userName, apppassword: passWord, appUrl: url, upbApp: ubqAppName, ubqAppuser: ubqUserName, ubqApppassword: ubqPassWord, ubqAppUrl: ubqUrl, record_id: record_id, ubqEnabled: ubqEnabled, useOrdId: useOrdId, ehrUsername: ehrUsername, ehrPassword: ehrPassword, action: 'saveCredentials'};
	
	$.ajax({
		data: paramerter,
		success: function(resp){
			window.location.href = top.JS_WEB_ROOT_PATH+'/interface/admin/allscripts/index.php?save=2';
		}
	});
}


$(document).ready(function(){
	set_header_title('Allscripts Unity Settings');
	
	/* Save API Credentials */
	$( '#saveCredentials' ).on( 'click', saveCredentials);
	
	/*Bind Function to object*/
	$( '#syncDictionary' ).on( 'click', syncData);
	
	top.show_loading_image('hide');
});