function falert(msg, hideLoader)
{
	modal({
			type: 'alert',
			title: 'imwemr',
			text: msg,
			callback: function(result) {
				if(hideLoader){
					top.$("#loading").hide();
					top.main_iframe.admin_iframe.$("#loading").hide();
				}
			}
		});		
}

function falertSuccess(msg)
{
	modal({
			type: 'success',
			title: 'imwemr',
			text: msg
		});	
}
function fconfirm(msg, call, arg1, arg2)
{
	var confirmWidth = (arg1=='verify') ? '660px' : false;
	modal({
			type: 'confirm',
			title: 'imwemr',
			text: msg,
			customWidth: confirmWidth,
			callback: function(result) {
				if(typeof(arg1)!=="undefined" && arg1!='verify' && typeof(arg2)!=="undefined"){
					call(result, arg1, arg2);
				}
				else if(arg1=='verify'){
					var termsStatus = top.$('#framesDataAgree').is(':checked');
					call(result, termsStatus);
				}
				else{
					call(result);
				}
			}
		});	
}

function fprompt(title,text,call,required)
{
	required = (typeof(required)==="undefined")?false:Boolean(required);
	modal({
			type: 'prompt', 
			title: title, 
			text: text,
			inputRequired: required, 
			buttonText: {
				ok: 'OK',
				yes: 'Yes',
				cancel: 'Cancel'
			},callback: function(result) {
				call(result);
			}
		});
}
var global_obj='';
function fcustomReasonFake(obj)
{
	global_obj=obj;
}
function fcustomReason(obj)
{
	if(typeof($('#modal-window').attr('id'))=='undefined'){
		$( "body" ).append('<div id="modal-window" style="position: fixed; width: 100%; height: 100%; top: 0px; left: 0px; z-index: 1050; overflow: auto; display:none"><div class="modal-box modal-size-normal" style="position: absolute; top: 50%; left: 50%; margin-top: -102.5px; margin-left: -280px;"><div class="modal-inner"><div class="modal-title"><h3 id="modal-window-title">imwemr</h3><a class="modal-close-btn" onClick="closeMe()"></a></div><div id="modal-window-detail" class="modal-text">detail will be here</div><div class="modal-buttons"><a class="modal-btn" onClick="closeMe()">Cancel</a><a class="modal-btn btn-light-blue" onClick="submitMe();">Confirm</a></div></div></div></div>');
	}
	global_obj=obj;
	var html=global_obj.$("#reason_div_to_show").html();
	$('#modal-window').show();	
	$('#modal-window-detail').html(html);	
}

function closeMe()
{
	$('#modal-window').hide();	
}

function submitMe()
{
	var reason=$('#status_reason').val();
	var reduce_qty=$("#reduce_qty").is(':checked');
	var update_wholesale=$("#update_wholesale").is(':checked');
	
	if(reduce_qty==true)
	global_obj.$("#reduc_stock").val('yes');
	else
	global_obj.$("#reduc_stock").val('no');
	
	
	if(reduce_qty==true)
	global_obj.$("#updateitems_ws").val('yes');
	else
	global_obj.$("#reduc_stock").val('no');
	
	global_obj.$("#reason_id").val(reason);
	$('#modal-window').hide();	
	global_obj.$("#firstform1").submit();	
}