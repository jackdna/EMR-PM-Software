$(document).ready(function(){
	
	/*Default Data*/
	//$('#applicationURL').val('http://192.168.1.165/iMedicWareR8-Dev');
	//$('#userId').val('admin');
	//$('#password').val('admin');
	//$('#accessToken').val('');
    
    /*Load API Call Details*/
    var apiDivs = $('div.apiList[data-apiCallFile]');
    
    $(apiDivs).each(function(index, div){
	var jsonFile = $(div).data('apicallfile');
	loadAPIInterface(jsonFile, div, index);
    });
    
    /*End Load API Call Details*/
	
	/*Set Call Log Modal - Body Height*/
	var totalHeight = screen.availHeight;
	$('#modalRequestLogData').css('max-height', totalHeight-305);
	
	$(document).on('click', '.apiName', function(){
		
		var apiDiv = $(this).attr('apiDiv');
		
		
		apiDiv = $('div[id="'+apiDiv+'"]');
		
		$(apiDiv).slideToggle('slow');
	});
	
	$('#modalURL').on('hidden.bs.modal', function () {
		$('input#applicationURL').focus();
	});
	
	$('#modalUserId').on('hidden.bs.modal', function () {
		$('input#userId').focus();
	});
	
	$('#modalPassword').on('hidden.bs.modal', function () {
		$('input#password').focus();
	});
	
	
	/*Clear Token*/
	$('input#userId, input#password').on('change', function(){
		$('#accessToken').val('');
	});
	$('[data-toggle="tooltip"]').tooltip();
});

var URLBK = '';
var patinetIdBK = '';

function viewCCD()
{
	pracriceName = URLBK.split('/').pop().toUpperCase();
	
	var CCDURL = URLBK+'/data/'+pracriceName+'/PatientId_'+patinetIdBK+'/tmp/imedic-api-'+patinetIdBK+'.xml';
	
	top.CCDFrame.src = CCDURL;
	$('#modalCCDViewer').modal('show')
}

function clearData(callName)
{
	var requestDiv = $('div[id="'+callName+'"]');
	var parameterFields = $(requestDiv).find('input');
	
	$(parameterFields).each( function(index, obj){
		obj.value = '';
	});
	
	var responseDiv = $(' .requestData', requestDiv);
	
	$('pre.rquestURL, pre.rquestParameters, pre.responseCode, pre.responseData', responseDiv).text('');
	$(responseDiv).slideUp('slow');
	
	
}

function triggerCall(callName)
{
	var requestDiv = $('div[id="'+callName+'"]');
	var requestMethod = $(requestDiv).closest('.apiList').data('method');
	if(requestMethod=='')
	{
	    requestMethod = 'GET';
	}
	
	var parameterFields = $(requestDiv).find('input');
	
	var auth = getRequiredParameters();
	
	if( auth === false)
		return false;
	
	var parameters = {accessToken:auth.token};
	var URL = auth.URL;
	
	if( URL === false)
		return false;
	
	URL = URL+'/'+callName;
	
	$(parameterFields).each( function(index, obj){
		
		var value = obj.value;
		
		parameters[obj.name] = value;
		
	});
	
	var responseDiv = $(' .requestData', requestDiv);
	
	patinetIdBK = (typeof(parameters.patientId)!==undefined)?parameters.patientId:'';
	
	var parametersBK = parameters;
	if(requestMethod === 'POST')
	{
	    parameters = JSON.stringify(parameters);
	}
	
	$.ajax({
	    url: URL,
	    method: requestMethod,
	    dataType: "json",
	    data: parameters,
	    dataElements: JSON.stringify(parametersBK),
	    contentType: 'application/json',
	    responseDiv: responseDiv,
	    callName: callName,
	    success: function(resp)
	    {
		resp = '';
	    }
	});
	
}

function getURL()
{
	var APIURL = $.trim( $('input#applicationURL').val() );
	
	if( /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*(:[0-9]{1,5})?(\/.*)?$/i.test(APIURL) === false )
	{
		$('#modalURL').modal('show');
		return false;
	}
	
	APIURL = APIURL.replace(/\/$/, "");
	
	URLBK = APIURL;
	
	APIURL = APIURL+'/IMWAPI';
	
	return APIURL;
}

function getRequiredParameters()
{
	
	var response = {};
	
	var APIURL = getURL();
	
	if( APIURL === false )
		return false;
	
	response.URL = APIURL;
	
	//Check Access Token
	var token = $.trim($('input#accessToken').val());
	
	if(token === '')
	{
		token = generateToken();
		
		if( token === false)
			return false;
	}
	
	response.token = token;
	
	return response;
}

function generateToken()
{
	var response = false;
	var userId = $.trim( $('input#userId').val() );
	
	if( userId === '' )
	{
		$('#modalUserId').modal('show');
		return false;
	}
	
	var password = $.trim( $('input#password').val() );
	
	if( password === '' )
	{
		$('#modalPassword').modal('show');
		return false;
	}
	
	var usrType = $.trim( $('input[name=userType]:checked').val() );
	if($('input[name=userType]:checked').length == 0){
		usrType = 2;
	}
	$('input[value='+usrType+']').prop('checked', true);
	//password = sha256(password);
	var parameters = {userId:userId, password:password, userType:usrType};
	var URL = getURL();
	
	if( URL === false)
		return false;
	
	var requestDiv = $('div[id="getToken"]');
	var responseDiv = $(' .requestData', requestDiv);
	
	$.ajax({
		url: URL+'/getToken',
		method: 'GET',
		async: false,
		dataType: "json",
		data: parameters,
		dataElements: JSON.stringify(parameters),
		contentType: 'application/json',
		responseDiv: responseDiv,
		success: function(resp)
		{
			if(resp.Success===1)
			{
				$('input#accessToken').val(resp.result.Token);
				response = resp.result.Token;
			}
		},
		error: function(resp)
		{
			var response = '';
			
			try {
				response = $.parseJSON(resp.responseText);
				
				if( typeof(response.Error) !== undefined )
					response = response.Error;
				else
					response = JSON.stringify(response, undefined, 2);
			}
			catch(e)
			{
				response = resp.responseText;
			}
			
			$('#modalTokenError .modal-body').text(response);
			$('#modalTokenError').modal('show');
		}
	});
	return response;
}


RequestCont = '';
/*Common Ajax Options*/
$.ajaxSetup({
	
	beforeSend: function()
	{
		$('#loader').show();
		
		var counter = parseInt($('input#modalRequestLogCounter').val());
		counter++;
		$('input#modalRequestLogCounter').val(counter);
		
		RequestCont = $('\n<pre id="Request'+counter+'">');
		$(RequestCont).append(counter+'\n');
		
		$(RequestCont).append('Request URL: '+this.url+'\n');
		$('pre.rquestURL', this.responseDiv).text(this.url);
		
		$(RequestCont).append('Request Json: \n');
		
		var parameters = JSON.parse(this.dataElements);
		parameters = JSON.stringify(parameters, undefined, 2);
		$(RequestCont).append(parameters+'\n\n');
		
		$('pre.rquestParameters', this.responseDiv).text(parameters);
		
		$('div#modalRequestLogData').prepend(RequestCont);
		
		$('div#modalRequestLogData > p').remove();
		
		/*Clean Response Containers*/
		$('pre.responseCode, pre.responseData', this.responseDiv).text('');
		
	},
	complete: function(resp)
	{
		$(RequestCont).append('Response Code: '+resp.status+'\n\n');
		$('pre.responseCode', this.responseDiv).text(resp.status);
		
		$(RequestCont).append('Response Data:\n');
		
		var response = '';
		
		try {
			response = $.parseJSON(resp.responseText);
			response = JSON.stringify(response, undefined, 2);
		}
		catch(e)
		{
			response = resp.responseText;
		}
		
		$(RequestCont).append(response);
		$('pre.responseData', this.responseDiv).text(response);
		
		response = null;
		
		$('#loader').hide();
		
		$(this.responseDiv).slideDown('slow');
		
		$('#viewIcon').css('visibility', 'hidden');
		
		if( this.callName === 'getCCDA' )
		{
			if( patinetIdBK !== '')
				$('#viewIcon').css('visibility', 'visible');
		}	
	}
	
});

function loadAPIInterface(jsonFile, element, elementIndex)
{
    elementIndex = ( typeof (elementIndex) === undefined ) ? 0 : elementIndex;
    
    var div = '';
    var apiDetails = '';
    var titleDiv = '';
    var parameter = '';
    var inputContainer = '';
    var parameterDescription = '';
    var buttonRow = '';
    var button = '';
    var requestData = '';
    
    $.ajax({
	beforeSend: false,
	complete: false,
	dataType: "json",
	url: 'CallsList/'+jsonFile+'.json',
	success: function(data) {
	    var updateDiv = data.name.toLowerCase().indexOf("update");
		
		//To differentiate Update call points from get call points -- use class --> 'updateDiv'
		var updateClass = '';
		if(updateDiv > -1) updateClass = 'updateDiv';
		
	    /*API Call Title*/
	    div = $('<div>').addClass('col-sm-5 apiName '+updateClass+'').attr("apiDiv", data.name).text(data.name);
	    $(element).append(div);
	    
	    /*Additional Elements*/
	    div = $('<div>').addClass('col-sm').text( $(element).data('method') );
	    $(element).append(div);
	    div = $('<div>').addClass('clearfix');
	    $(element).append(div);
	    
	    apiDetails = $('<div>').addClass('row').attr('id', data.name).hide();
	    
	    /*Description Text*/
	    $('<div>').addClass('col-sm-12').html(data.description).appendTo(apiDetails);
	    
	    /*Horizontal Line Separator*/
	    $('<div>').addClass('col-sm-12').append( $('<span>').addClass('hr') ).appendTo(apiDetails);
	    
	/*Parameters Section*/
	    if( data.parameters.length > 0 )
	    {
		/*Add Parameter Titles, Only if parameters Actually exists*/
		titleDiv = $('<div>').addClass('row paramsTitle');
		
		/*Parameter Titles*/
		$('<div>').addClass('col-sm-2').text('Parameter').appendTo(titleDiv);
		$('<div>').addClass('col-sm-1').text('Type').appendTo(titleDiv);
		$('<div>').addClass('col-sm-3').text('Value').appendTo(titleDiv);
		$('<div>').addClass('col-sm').text('Description').appendTo(titleDiv);
		
		/*Append to Details Container*/
		$('<div>').addClass('col-sm-12').append(titleDiv).appendTo(apiDetails);
		
		/*Separator*/
		$('<div>').addClass('col-sm-12').append( $('<span>').addClass('whiteBorder') ).appendTo(apiDetails);
		
		/*Parametes Display*/
		$(data.parameters).each(function(index, element){
		    
		    parameter = $('<div>').addClass('row');
		    
		    /*Parameter Elements*/
		    $('<div>').addClass('col-sm-2').text(element.name).appendTo(parameter);		    
		    $('<div>').addClass('col-sm-1').text(element.type).appendTo(parameter);
		    
		    /*Parameter Input Field*/
		    inputContainer = $('<div>').addClass('form-group');
		    $('<input>').addClass('form-control').attr( {name: element.name, id: element.name+elementIndex, type: 'text'} ).appendTo(inputContainer);
		    $('<div>').addClass('col-sm-3').append(inputContainer).appendTo(parameter);
		    
		    /*Parameter Details*/
		    parameterDescription = $('<div>').addClass('col-sm').html(element.description).appendTo(parameter);
		    if( element.required === true )
		    {
			$('<span>').addClass('required').appendTo(parameterDescription);
		    }
		    
		    $('<div>').addClass('col-sm-12').append(parameter).appendTo(apiDetails);
		    
		    /*Parameter Separator*/
		    $('<div>').addClass('col-sm-12').append( $('<span>').addClass('whiteBorder') ).appendTo(apiDetails);
		});
		
		/*Access Token*/
		parameter = $('<div>').addClass('row');
		$('<div>').addClass('col-sm-2').text('accessToken').appendTo(parameter);		    
		$('<div>').addClass('col-sm-1').appendTo(parameter);
		$('<div>').addClass('col-sm-3').appendTo(parameter);
		$('<div>').addClass('col-sm').text('This field does not need to be passed by the user. It will be added automatically from the session.').appendTo(parameter);
		$('<div>').addClass('col-sm-12').append(parameter).appendTo(apiDetails);

		/*Separator*/
		$('<div>').addClass('col-sm-12').append( $('<span>').addClass('whiteBorder') ).appendTo(apiDetails);
	    }
	/*End Parameters Section*/
	
	/*Action Buttons*/
	    buttonRow = $('<div>').addClass('row');
	    
	    button = $('<button>').addClass('btn btn-success').attr('type', 'button').text('Try It').on('click', function(){triggerCall(data.name)});
	    $('<div>').addClass('col-sm-1').append(button).appendTo(buttonRow);
	    
	    button = $('<button>').addClass('btn btn-danger').attr('type', 'button').text('Clear').on('click', function(){clearData(data.name)});
	    $('<div>').addClass('col-sm-1').append(button).appendTo(buttonRow);
	    
	    $('<div>').addClass('col-sm').appendTo(buttonRow);
	    
	    $('<div>').addClass('col-sm-12').append(buttonRow).appendTo(apiDetails);
	/*End Action Buttons*/
	
	    $('<div>').addClass('clearfix').appendTo(apiDetails);
	
	/*Response Container*/
	    requestData = $('<div>').addClass('row requestData').hide();
	    
	    $('<div>').addClass('col-sm-12').text('Request URL:').appendTo(requestData);
	    $('<div>').addClass('col-sm-12').append( $('<pre>').addClass('rquestURL') ).appendTo(requestData);
	    $('<div>').addClass('clearfix').appendTo(requestData);
	    
	    $('<div>').addClass('col-sm-12').text('Request Parameters:').appendTo(requestData);
	    $('<div>').addClass('col-sm-12').append( $('<pre>').addClass('rquestParameters') ).appendTo(requestData);
	    $('<div>').addClass('clearfix').appendTo(requestData);
	    
	    $('<div>').addClass('col-sm-12').text('Response Code:').appendTo(requestData);
	    $('<div>').addClass('col-sm-12').append( $('<pre>').addClass('responseCode') ).appendTo(requestData);
	    $('<div>').addClass('clearfix').appendTo(requestData);
	    
	    $('<div>').addClass('col-sm-12').text('Response Data:').appendTo(requestData);
	    $('<div>').addClass('col-sm-12').append( $('<pre>').addClass('responseData') ).appendTo(requestData);
	    $('<div>').addClass('clearfix').appendTo(requestData);
	    
	    $('<div>').addClass('col-sm-12').append(requestData).appendTo(apiDetails);
	/*End Response Container*/
	
	    $('<div>').addClass('col-sm-12 apiDetails').append(apiDetails).appendTo(element);
	}
    });
}
