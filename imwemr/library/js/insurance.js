// Insurance JS File
// Code Written By : Gurpreet Singh
var auth_provider = [];
var cpt_data_src = [];
function exist_ins_check(patient_id)
{
	var copy_from_ins = $("#copy_from_ins_case").val();
	var copy_to_ins_case = $("#copy_to_ins_case").val();
	var copy_ins_data_from = ($("#copy_ins_data_from").length > 0 ) ? $("#copy_ins_data_from").val().split('__') : new Array();
	var copy_ins_data_to = $("#copy_ins_data_to").val();
	
	if(copy_ins_data_from[0] == '')
	{
		top.fAlert('There is no insurance company for copy.');
		return;
	}
	if(copy_from_ins == copy_to_ins_case && copy_ins_data_from[0] == copy_ins_data_to)
	{
		top.fAlert('Copy same insurance case and company type not allowed.');
		return;
	}

	var ins_type = copy_ins_data_to;
	var ins_case_id = copy_to_ins_case;
	
	var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/insurance/ajax.php';
	var params = 'action=check_exist_ins&ins_type='+ins_type+'&ins_case_id='+ins_case_id+'&patient_id='+patient_id;
	top.master_ajax_tunnel(url,top.fmain.xhr_ajax,params,'json');
	
}

function getRealTimeEligibility(insRecId, askElFrom)
{
		askElFrom = askElFrom || 0;
		var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/ajax/make_270_edi.php";
		var params = 'action=ins_eligibility&insRecId='+insRecId+'&askElFrom='+askElFrom;
		top.master_ajax_tunnel(url,top.fmain.xhr_ajax,params,'json');
}	

function getPreAuthorization(insRecId)
{
	return ;
	//top.popup_win("../claims_authorization.php?ptInsDataId="+insRecId,"claim_auth_window","width=920,height=500, left=150, top=80,toolbar=0, scroolbars=1, location=no, statusbar=0,menubar=0, resizable=0");
}


function insOpenCaseSummary(url){
	var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/ajax/insurance/act_exp_open_insu_case.php";
	var params = 'action=ins_history';
	top.master_ajax_tunnel(url,top.fmain.xhr_ajax,params,'json');
	//window.open(url,"Insurance Open Case Summary","width=800, height=210, top=150, left= 250, resizable=yes, scrollbars=yes");	
}

function openScanDocument(id,scan_for,ins_data_id)
{
	var url = top.JS_WEB_ROOT_PATH +'/interface/patient_info/insurance/scan/scan_refferal_case.php?scan_id='+id+'&scan_for='+scan_for+'&ins_data_id='+ins_data_id;
	if( window.opener ) window.open(url,'scan_reff','BLANK');
	else top.popup_win(url,'scan_reff','');
}
	
// Function to Change Save Insurance Cases
function choose_form()
{
	document.insuranceCaseFrm.case_status.value = '';
	document.insuranceCaseFrm.chooseNewform.value = '';
	if($("#choose_prevcase").val() != '')
	{
		$("#inactivePriInsComp,#inactiveSecInsComp,#inactiveTerInsComp").val('').selectpicker('refresh');
		document.insuranceCaseFrm.submit();
	}
	else{
		top.fAlert("Choose Case First",'',"window.top.fmain.document.insuranceCaseFrm.submit()");
	}
}

function checkInsClaim(r)
{
	if(typeof r !== 'undefined')
	{
		var val_arr = r.split("~~~");
		if(val_arr[0]!=''){//medicare
			$('#insCliamVal').val(val_arr[0]);
		}
		if(val_arr[1]!=''){//medicare
			$('#i1accept_assignment').val(val_arr[1]);
		}
	}
	else
	{
		if($('#i1accept_assignment').length >  0){
			$('#i1accept_assignment').val("0");
		}
		$('#insCliamVal').val("0");
		var pri_ins_pro_id = $('#insprovider1').val();
		var getPriInsID=pri_ins_pro_id.split("*");
		priInsId=getPriInsID[1];
		if(priInsId)
		{
			var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/ajax/insurance/get_ins_claim.php";
			var params = 'insCompanyId='+priInsId;
			top.master_ajax_tunnel(url,top.fmain.checkInsClaim,params);
		}
	}
	
	return;	
}

function chkFuture(objId,obj)
{
		var todayDate = Date.parse(document.getElementById(objId).value);
		var objDate = Date.parse(obj.value);
		if(objDate){
			if(objDate <= todayDate){
				var msg = "Referral End date should be in the future";
				if(top.fmain)
					top.fAlert(msg);				
				else
					alert(msg);
				obj.value = '';
				obj.focus();
			}
		}		
}
	
// Function To perform action based on triggering Click
// Event on New || Del || Opened Cases || Closed Cases Buttons
function choose_newform(val,cnfrm)
{
	  var flag = false;
	  var msg = '';
	  $("#chooseNewform").val(val);
	  if(val == 'New')
		{
			$("#case_id, #case_resp_party").val('');
			$("#case_startdate").val(js_today_date);
			$("#choose_prevcase, #inscasetype, #inactivePriInsComp, #inactiveSecInsVal, #inactiveTerInsVal ").val('').selectpicker('refresh');
			$("#case_status").val('Open').selectpicker('refresh');
			$("#openCase").val('Open Case');
			$("#chooseNewform").val('OpenCase')
			flag = false;
	  }
	  
		if(val == 'Delete')
		{
			if($("#choose_prevcase").val() == '')
			{
				msg = 'Please Select Any Case To Delete.';
				top.fAlert(msg);
				flag = false;
			}
			else
			{
				if(typeof(cnfrm)=='undefined'){
					top.fancyConfirm('Do you want to remove this case?','',"window.top.fmain.choose_newform('"+val+"',true);");
					$("#chooseNewform").val('updateCase');
					return;				
				}else{
					$("#inactivePriInsComp, #inactiveSecInsVal, #inactiveTerInsVal ").val('').selectpicker('refresh');
					flag = true;
				}
			}	
	  }
	  if(val == "Closed Cases" || val == 'Opened Cases')
		{
				$("#inactivePriInsComp, #inactiveSecInsVal, #inactiveTerInsVal ").val('').selectpicker('refresh');	
				document.insuranceCaseFrm.chooseNewform.value = 'changeStatus';
				
			 	if(val == "Closed Cases"){
					document.insuranceCaseFrm.case_status.value = 'Close';
					document.insuranceCaseFrm.closeOpenBtVal.value = "Closed Cases";						
			 	}
		 		else
				{
					document.insuranceCaseFrm.case_status.value = 'Open';
					document.insuranceCaseFrm.closeOpenBtVal.value = "Opened Cases";
		 		}
		 		flag = true;
		}
	  if(flag == true){
		  document.insuranceCaseFrm.submit();	
	  }
}

// Function to process Validate & Save/Update New/Existing Insurance Case
function saveCase(val)
{	
	var msg = '';
	var msgFlag = false;
	var curObj = document.insuranceCaseFrm;
	
	if(curObj.case_status.value == 'Open' && curObj.choose_prevcase.value == '' ){
		if(curObj.inscasetype.value == ''){
			msg = "Please Select New Case Type.\n";
		}
	}
	
	if(curObj.case_status.value == 'Close'){
		if(curObj.choose_prevcase.value == 'Select Case' || curObj.choose_prevcase.value == ''){
			msg = "Please Select Previous Case to Close.\n";
		}
	}
	
	if(val == 'Open Case'){
		if(curObj.case_status.value == 'Close'){
			msg += "New Case Status Should Be Open.\n";
		}
	}
	
	if(top.jquery_date_format != "undefined")
	{
		arrStartDate = fnArrDate(curObj.case_startdate.value,top.jquery_date_format);
		Start_Date = new Date(arrStartDate[0],arrStartDate[1]-1,arrStartDate[2]);
		
		arrStartDate = fnArrDate(curObj.case_enddate.value,top.jquery_date_format);
		End_Date = new Date(arrStartDate[0],arrStartDate[1]-1,arrStartDate[2]);
	}else{
		var Start_Date = Date.parse(curObj.case_startdate.value);
		var End_Date = Date.parse(curObj.case_enddate.value);	
	}
	if(Start_Date != '' && End_Date != '' && Start_Date > End_Date){
		msg += "Start date should be less than End date.\n";
		curObj.case_startdate.focus();
	}
	
	if(val == 'Update Case'){
		curObj.chooseNewform.value = val;
	}
	
	if(msg == ''){		
		curObj.submit();
	}
	else{
		top.fAlert(msg);
		return false;
	}
}

// Function to show tooltip on Insurance Provide DropDown/Input Box
function getToolTip(id,ins_name, providerRCOId,type)
{
		type = type || 1;
		providerRCOId = providerRCOId || "";
		if(id)
		{
			var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/ajax/insurance/ajax.php";
			var params = "action=ins_comp_practice_code&id="+id+"&providerRCOId="+providerRCOId;
			
			top.master_ajax_tunnel(url,top.fmain.xhr_ajax,params,'json');
			var curPos = getPosition();
			
			var t_pos = curPos.y
			var l_pos = 200;
			
			if(ins_name){
				t_pos = t_pos - 35;
				l_pos =  200;
			}
			
			$("#tool_tip_div").css({'top':t_pos+'px','left': l_pos+'px'}); 
			$("#tool_tip_div").html('Please Wait....').show('fast');
			
		}
}

// Fucntion to Close Tooltip if appeared
function closeToolTip()
{
	$("#tool_tip_div").hide('fast').html('');
}

function get_co_ins_handler(r)
{
	if(typeof(r) != 'undefined')
	{
		if($('#i1co_ins').length > 0 )
		{
			$('#i1co_ins').val(r);
		}
	}
	return;
}
function get_co_ins(id)
{
	var textbox_value = id;
	var ins_id = $('#i1provider').val();
	if(ins_id == "")
	{
		var position = parseInt(textbox_value.indexOf('*'))+1;
		var ins_id = textbox_value.substr(position);
	}
	var url = top.JS_WEB_ROOT_PATH + "/interface/patient_info/ajax/insurance/get_co_ins.php";
	var params = 'ins_id='+ins_id;
	top.master_ajax_tunnel(url,top.fmain.get_co_ins_handler,params);
	
}
	
function FillName(name,id,crossMapInvisionPlanCode,crossMapIdxInvRCOId,type)
{
		crossMapInvisionPlanCode = crossMapInvisionPlanCode || "";
		crossMapIdxInvRCOId = crossMapIdxInvRCOId || "";
		type = type || 1;
		var f1 = 'insprovider' + type;
		var f2 = 'i' + type + 'provider';
		var f3 = 'i' + type + 'providerRCOCode';
		var f4 = 'i' + type + 'providerRCOId';
		
		var Name = name.substr(0,12);		
		$("#"+f1).val(Name);
		$("#"+f2).val(id);
		$("#"+f3).val(crossMapInvisionPlanCode);
		$("#"+f4).val(crossMapIdxInvRCOId);
		
		if(name !== 'Unassigned'){
			$("#"+f1).addClass("form-control ");
		}
		var pv = $("#"+f1).data('prev-val');
		top.chk_change_in_form(pv,document.getElementById(f1),'InsTabDb');
}

function check_ins_exist(id, ins_type)
{
	switch(ins_type)
	{
			case 'primary':
				var ins_provider_id = $('#i1provider').val();
				if(!(id=="" && ins_provider_id == ""))
				{
					if(id.indexOf('*') == '-1')
					{
						if(ins_provider_id == "")
						{
							$('#insprovider1').val('');
							top.fAlert("Please Select Primary Insurance provider From Drop Down.");
							$('#insprovider1').focus();
							return false;
						}
						else
						{
							return true;
						}	
					}
					else
					{
						return true;
					}
				}
				else{
					return true;
				}
				break;
			
			case 'secondary':
				var ins_provider_id = $('#i2provider').val();
				if(!(id=="" && ins_provider_id == ""))
				{
					if(id.indexOf('*') == '-1')
					{
						if(ins_provider_id == "")
						{
							$('#insprovider2').val('');
							top.fAlert("Please Select Secondary Insurance provider From Drop Down.");
							$('#insprovider2').focus();
							return false;
						}	
						else
						{
							return true;
						}
					}
					else
					{
						return true;
					}
				}
				else{
					return true;
				}
				break;		
			case 'tertiary':
				var ins_provider_id = $('#').val();
				if(!(id=="" && ins_provider_id == ""))
				{
					if(id.indexOf('*') == '-1')
					{
						if(ins_provider_id == "")
						{
							$('#insprovider3').val('');
							top.fAlert("Please Select Tertiary Insurance provider From Drop Down.");
							$('#insprovider3').focus();
							return false;
						}
						else{
							return true;
						}	
					}
					else{
						return true;
					}
				}
				else
				{
					return true;
				}
				break;			
						
	}
}

// Function to Show Expired Refferal Cases
function show_expired_ref(ins_type_df,ins_data_id,ins_provider)
{
	var url = 'expired_referrals.php?ins_type='+ins_type_df+"&ins_data_id="+ins_data_id+"&ins_provider="+ins_provider;
	window.open(url,'_blank','width=1050,height=650,scrollbars=yes');	
}

	
// Function to Remove Refferal Case Grid 
var temp_this = '';
function del_reff_ins_act(ths,del_id,user_confirm)
{
	if(typeof ths === 'boolean' && typeof temp_this === 'object')
	{
		ths = temp_this;
	}
	if(typeof ths !== 'object') return; 
	
	var popup = (top.fmain) ? false : true;
	var div = popup ? $(ths).parent().parent().parent().parent() : $(ths).parent().parent().parent().parent().parent();
	var a = $(ths).parent();
	if( a[0].nodeName.toLowerCase() === 'td') { div = $(ths).parent().parent(); }
	
	del_id = del_id || 0;
	if(del_id == 0)
	{			
		div.remove();
	}
	else
	{
		
		if(typeof(user_confirm)=='undefined'){
			user_confirm=false;
			temp_this = ths;
			var callback = 'top.del_reff_ins_act('+true+','+del_id+','+true+')';
			var msg = "Are you sure? You want to delete this referral?";
			if( popup )
			{ 
				if(confirm(msg))
					eval(callback);	
			}
			else
			{
				callback = 'top.fmain.del_reff_ins_act('+true+','+del_id+','+true+')';
				top.fancyConfirm(msg,'',""+callback+"");
			}
			
			return false;
		}
			
		top.show_loading_image("show");
		$.ajax({
				url : top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/insurance/del_user_reff.php',
				type : 'POST',
				data : {'del_id' : del_id },
				complete : function()
				{
					top.show_loading_image("hide");
					div.remove();				
				}
		})
	}
	
}			

// To Add New Row in Referral Grid
function addRef(_this,i_type,i_key,ins_data_id)
{
	var rows = parseInt(_this.getAttribute('data-rows'));
	
	i_type = i_type.trim();
	var s_name= i_type.substr(0,3);
	var total	= parseInt(rows) + 1;
	var html = '';
	html	+=	'<div class="col-sm-12 table_grid margin-top-5 " id="'+i_type+'_refferal_'+total+'" >';	 	
	
	html	+=	'<div class="row">';
	html	+=	'<div class="col-sm-12 margin-top-5">';
	html	+=	'<div class="row">';
	html	+=	'<div class="col-sm-7">';
	html	+=	'<label class="sub-heading">Referral</label>';
	html	+=	'</div>';
	html	+=	'<div class="col-sm-5 text-right">';
	html	+=	'<a href="javascript:foo();" class="btn btn-success btn-xs" id="scanner_image_'+s_name+'_'+total+'" onClick="openScanDocument(\'\',\''+i_type+'_reff\',\''+ins_data_id+'\');">';
	html	+=	'<img src="'+top.JS_WEB_ROOT_PATH+'/library/images/scanner.png" alt="Referral scan document" width="20"/>';
	html	+=	'</a>';
	html	+=	'</div>';
	html	+=	'</div>';
	html	+=	'</div>';
	
  
	html	+=	'<div class="clearfix"></div>';
	
	html	+=	'<div class="col-sm-12">';
	var tmp = 'ref'+i_key+'_phyId'+total;
	html	+=	'<input type="hidden" name="ref'+i_key+'_phyId[]" id="'+tmp+'" value="" />';
	html	+=	'<input type="hidden" name="ref_id_'+s_name+'[]" id="ref_id_'+s_name+''+total+'" value="" />';
	html	+=	'<div class="row">';
	
	html	+=	'<div class="col-sm-3">';
	html	+=	'<label>Ref. Physician</label><br>';
	html	+=	'<div class="input-group">';
	//loadPhysicians(this,\'ref'+i_key+'_phyId'+total+'\');
	//loadPhysicians(this,\''+tmp+'\');
	html	+=	'<input type="text" name="ref'+i_key+'_phy[]" id="ref'+i_key+'_phy'+total+'" value="" class="form-control"  data-search-by="" data-action="search_physician" data-text-box="ref'+i_key+'_phy'+total+'" data-id-box="'+tmp+'" size="25" onKeyUp="top.loadPhysicians(this,\''+tmp+'\');top.chk_change_in_form (\'\',this,\'InsTabDb\',event); chk_change(\'\',this.value,event);" onBlur="lost_focus(this,\'form-control\');" onKeyPress="javascript:document.getElementById(\''+tmp+'\').value=\'\';chk_change(\'\',this.value,event); save_data(event);" onFocus="top.loadPhysicians(this,\''+tmp+'\'); get_focus_obj(this);">';
	html	+=	'<label class="input-group-addon btn search_physician" data-source="ref'+i_key+'_phy'+total+'"><i class="glyphicon glyphicon-search"></i></label>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	
	html	+=	'<div class="col-sm-3">';
	html	+=	'<label for="eff'+i_key+'_date'+total+'">Start Date</label><br>';
	html	+=	'<div class="input-group">';
	html	+=	'<input class="datepicker reff_start_date_cl form-control" type="text" name="eff'+i_key+'_date[]" id="eff'+i_key+'_date'+total+'" value="" size="11" onBlur="top.checkdate(this); lost_focus(this,\'form-control\');"  maxlength="10" onKeyUp="top.chk_change_in_form (\'\',this,\'InsTabDb\',event); chk_change(\'\',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">'
	html	+=	'<label class="input-group-addon btn" for="eff'+i_key+'_date'+total+'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	html	+=	'<div class="col-sm-3">';
	html	+=	'<label for="end'+i_key+'_date'+total+'">End Date</label><br>';
	html	+=	'<div class="input-group">';
	html	+=	'<input type="text" class="datepicker reff_end_date_cl form-control" name="end'+i_key+'_date[]" id="end'+i_key+'_date'+total+'" value="" size="11" onBlur="top.checkdate(this); chkFuture(\'eff'+i_key+'_date'+total+'\',this); lost_focus(this,\'form-control\');"  onChange="top.checkdate(this);"  maxlength="10" onKeyUp="top.chk_change_in_form (\'\',this,\'InsTabDb\',event); chk_change(\'\',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" />';
	html	+=	'<label class="input-group-addon btn" for="end'+i_key+'_date'+total+'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	
	html	+=	'<div class="col-sm-3">';
	html	+=	'<label>Visits</label><br>';
	html	+=	'<input type="hidden" name="'+s_name+'NoRef[]" id="'+s_name+'NoRef'+total+'" value=""/>';
	html	+=	'<input type="hidden" name="'+s_name+'UsedRef[]" id="'+s_name+'UsedRef'+total+'" value=""/>';
	html	+=	'<input type="text"  name="no_ref'+i_key+'[]" id="no_ref'+i_key+''+total+'" value="" size="3" class="form-control" onKeyUp="top.chk_change_in_form (\'\',this,\'InsTabDb\',event); chk_change(\'\',this.value,event);" onBlur="lost_focus(this,\'form-control\');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">';
	html	+=	'</div>';
	
	html	+=	'</div>';
	html	+=	'</div>';
	
	html	+=	'<div class="clearfix"></div>';
	
	
  html	+=	'<div class="col-sm-12">';
	html	+=	'<div class="row">';
	
	html	+=	'<div class="col-sm-3">';
	html	+=	'<label>Referral#</label><br>';
	html	+=	'<input type="text" name="reffral_no'+i_key+'[]" id="reffral_no'+i_key+''+total+'" value="" size="11" class="form-control " onKeyUp="top.chk_change_in_form (\'\',this,\'InsTabDb\',event); chk_change(\'\',this.value,event);" onBlur="lost_focus(this,\'form-control\');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">';
	html	+=	'</div>';
	
	html	+=	'<div class="col-sm-3">';
	html	+=	'<label for="reff'+i_key+'_date'+total+'">Ref. Date</label><br>';
	html	+=	'<div class="input-group">';
	html	+=	'<input type="text" name="reff'+i_key+'_date[]" id="reff'+i_key+'_date'+total+'" value="" size="11" onBlur="top.checkdate(this); lost_focus(this,\'form-control\');"  maxlength="10" class="form-control datepicker" onKeyUp="top.chk_change_in_form (\'\',this,\'InsTabDb\',event); chk_change(\'\',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">';
	html	+=	'<label class="input-group-addon btn" for="reff'+i_key+'_date'+total+'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	html	+=	'<div class="col-sm-6">';
	html	+=	'<label>Notes</label><br>';
	html	+=	'<textarea style="height:34px;" name="note'+i_key+'[]" id="note'+i_key+''+total+'" cols="40" rows="1" class="form-control" onKeyUp="top.chk_change_in_form (\'\',this,\'InsTabDb\',event); chk_change(\'\',this.value,event);" onKeyPress="save_data(event);" onBlur="lost_focus(this,\'form-control\');" onFocus="get_focus_obj(this);"></textarea>';
	html	+=	'</div>';
	
	html	+=	'</div>';
	html	+=	'</div>';
	html	+=	'</div>';
	//html	+=	'<div class="clearfix">&nbsp;</div>';
	html	+=	'</div>';
	
	var ref_id = 'ref_id_'+s_name+rows;
	var ref_id_val = $("#"+ref_id).val();
	ref_id_val = ref_id_val || 0;
	var del_image ='<img onclick="del_reff_ins_act(this,'+ref_id_val+');" class="pointer" src="'+ top.JS_WEB_ROOT_PATH +'/library/images/close1.png" alt="Delete Referral" title="Delete Referral" width="24" />&nbsp;';
	
	$('#'+i_type+'ReffCont').append(html);
	$("#scanner_image_"+s_name+"_"+rows).before(del_image);
	
	
	_this.setAttribute('data-rows',total);
	bind_datepicker();
}	

// To Add New Row in Auth Grid
function add_auth(_this,i_type,i_key)
{
	var rows = parseInt(_this.getAttribute('data-rows'));
	i_type = i_type.trim();
	var s_name= i_type.substr(0,3);
	var total	= parseInt(rows) + 1;
	
	var authUser = _this.getAttribute('data-auth-user');
	var operator_id=_this.getAttribute('data-auth-user-id');
	var auth_id = 0;
	
	var del_img = '<img src="'+ top.JS_WEB_ROOT_PATH +'/library/images/close1.png" class="pointer" title="Delete Auth Information" onClick="delete_auth_info('+auth_id+','+rows+',\''+i_type+'\');" width="24" />';
	
	var html = '';
	html	+=	'<div class="row table_grid margin-top-5" id="'+i_type+'_auth_information_'+total+'" >';
	html	+=	'<div class="col-sm-12 margin-top-5 ">';
	html	+=	'<div class="row">';
	html	+=	'<div class="col-sm-10">';
	html	+=	'<label class="sub-heading">Authorization</label>';
	html	+=	'</div>';
	html	+=	'<div class="col-sm-2 text-right" id="'+i_type+'AuthDelBtnDiv'+total+'"></div>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	html	+=	'<div class="clearfix"></div>';
                
  html	+=	'<div class="col-sm-12">';
	html	+=	'<input type="hidden" name="auth_cur_'+s_name+'_'+total+'" id="auth_cur_'+s_name+'_'+total+'" value="'+authUser+'">';
	html	+=	'<input type="hidden" name="auth_id_'+s_name+'_'+total+'" id="auth_id_'+s_name+'_'+total+'" value="">';
	html	+=	'<input type="hidden" name="auth_user_'+s_name+'_'+total+'" id="auth_user_'+s_name+'_'+total+'" value="'+operator_id+'">';
	
	html	+=	'<div class="col-sm-12">';
	html	+=	'<div class="row">';
	
	html	+=	'<div class="col-sm-2">';
	html	+=	'<label>Authorization#</label><br>';
	html	+=	'<input type="text" class="form-control" name="auth_nam_'+s_name+'_'+total+'" value="" id="auth_nam_'+s_name+'_'+total+'" size="20" onKeyUp="top.chk_change_in_form(\'\',this,\'InsTabDb\',event);">';
	html	+=	'</div>';
	
	html	+=	'<div class="col-sm-2">';
	html	+=	'<label for="auth_provider_'+s_name+'_'+total+'">Provider</label><br>';
	html	+=	'<select class="form-control minimal" name="auth_provider_'+s_name+'_'+total+'" id="auth_provider_'+s_name+'_'+total+'" onChange="top.chk_change_in_form(\'\',this,\'InsTabDb\',event);">';
	html	+=	'<option value="" selected>Select Provider</option>';
	for(var z in auth_provider){
		html	+=	'<option value="'+auth_provider[z]+'">'+z+'</option>';	
	}
	html	+=	'</select>';
	html	+=	'</div>';
	
	html	+=	'<div class="col-sm-4">';
	html	+=	'<label for="auth_cpt_codes_'+s_name+'_'+total+'">CPT Codes</label><br>';
	html	+=	'<input type="text" class="form-control" name="auth_cpt_codes_'+s_name+'_'+total+'" value="" id="auth_cpt_codes_'+s_name+'_'+total+'" data-sort="contain" data-provide="multiple" data-seperator="semicolon"  onKeyUp="top.chk_change_in_form(\'\',this,\'InsTabDb\',event);" />';
	html	+=	'</div>';
	
	
	html	+=	'<div class="col-sm-2">';
	html	+=	'<label for="auth_dat_'+s_name+'_'+total+'">Date</label><br>';
	html	+=	'<div class="input-group">';
	html	+=	'<input type="text" class="form-control datepicker" name="auth_dat_'+s_name+'_'+total+'" value="" id="auth_dat_'+s_name+'_'+total+'" size="11" title="mm-dd-yyyy" onBlur="top.checkdate(this);" onKeyUp="top.chk_change_in_form(\'\',this,\'InsTabDb\',event);" >';
	html	+=	'<label for="auth_dat_'+s_name+'_'+total+'" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	
	html	+=	'<div class="col-sm-2">';
	html	+=	'<label for="auth_end_dat_'+s_name+'_'+total+'">End Date</label><br>';
	html	+=	'<div class="input-group">';
	html	+=	'<input type="text" class="form-control datepicker" name="auth_end_dat_'+s_name+'_'+total+'" value="" id="auth_end_dat_'+s_name+'_'+total+'" size="11" title="mm-dd-yyyy" onBlur="top.checkdate(this);" onKeyUp="top.chk_change_in_form(\'\',this,\'InsTabDb\',event);">';
	html	+=	'<label for="auth_end_dat_'+s_name+'_'+total+'" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	
	html	+=	'</div>';
	html	+=	'</div>';
	
	html	+=	'<div class="clearfix"></div>';
	
	html	+=	'<div class="col-sm-12">';
	html	+=	'<div class="row">';
	
	html	+=	'<div class="col-sm-2">';
	html	+=	'<label>Visits</label><br>';
	html	+=	'<input type="text" class="form-control" name="auth_visit_value_'+s_name+'_'+total+'" value="" id="auth_visit_value'+s_name+'_'+total+'"  onKeyUp="top.chk_change_in_form(\'\',this,\'InsTabDb\',event);">';
	html	+=	'</div>';
	
	html	+=	'<div class="col-sm-2">';
	html	+=	'<label>Amount</label><br>';
	html	+=	'<input type="text" class="form-control" name="'+s_name+'AuthAmount_'+total+'" value="" onKeyUp="top.chk_change_in_form(\'\',this,\'InsTabDb\',event);" />';
	html	+=	'</div>';
	
	html	+=	'<div class="col-sm-6">';
	html	+=	'<label>Comments</label><br>';
	html	+=	'<textarea name="auth_comment_'+s_name+'_'+total+'" class="form-control" id="auth_comment_'+s_name+'_'+total+'" cols="60" rows="1" onKeyUp="top.chk_change_in_form(\'\',this,\'InsTabDb\',event);" style="height:auto;"></textarea>';
	html	+=	'</div>';
	
	html	+=	'<div class="col-sm-2">';
	html	+=	'<label>Operator</label><br>';
	html	+=	'<input type="text" class="form-control" readonly name="auth_oper_'+s_name+'_'+total+'" id="auth_oper_'+s_name+'_'+total+'" size="16" value="'+authUser+'">';
	html	+=	'</div>';
	html	+=	'</div>';
	html	+=	'</div>';
	
	//html	+=	'<div class="clearfix">&nbsp;</div>';
	html	+=	'</div>';
	html	+=	'</div>';
								
	_this.setAttribute('data-rows',total);
	//_this.setAttribute('data-auth-id',0);
	var del_id =	i_type+'AuthDelBtnDiv'+rows;
	$("#"+del_id).html(del_img);
	
	$("#auth_main_table"+i_key).append(html);
	$("#last_auth_inf_cnt_"+s_name).val(total);
	bind_typeahead($('#auth_cpt_codes_'+s_name+'_'+total));
	bind_datepicker();
}

function delete_auth_info_for_popup(auth_id)
{
	auth_id = auth_id || 0
	row_id = 'auth_row_id_'+auth_id;

	if( auth_id ) 
	{
		$confirm = confirm("Are you sure you want to delete this record?");
		if( $confirm )
		{
			top.show_loading_image("show");
			$.ajax({
				url : top.JS_WEB_ROOT_PATH +"/interface/patient_info/ajax/insurance/delete_auth_info.php",
				type : 'POST',
				data : { 'id' : auth_id },
				success : function(r)
				{
					top.show_loading_image("hide");
					if(r == 1)
					{
						top.fAlert('Record Deleted Successfully');
						$("#"+row_id).fadeOut('fast');					
					}
					else
					{
						top.fAlert('Error while deleting record. Please try again!!!');					
					}
				}
			});
			top.show_loading_image("hide");
		}
	} else {
		top.fAlert('Error: missing parameters');
	}
}

//To Delete Auth Info Grid
function delete_auth_info(auth_id,grid_id,i_type,user_confirm)
{
	auth_id = auth_id || 0
	grid_id = grid_id || 0;
	
	if(auth_id)
	{
			if(typeof(user_confirm)=='undefined')
			{
				user_confirm = false;
				top.fancyConfirm("Are you sure you want to delete this record?",'',"window.top.fmain.delete_auth_info("+auth_id+","+grid_id+",'"+i_type+"',true)");
				return false;
			}
			top.show_loading_image("show");
			$.ajax({
				url : top.JS_WEB_ROOT_PATH +"/interface/patient_info/ajax/insurance/delete_auth_info.php",
				type : 'POST',
				data : { 'id' : auth_id },
				success : function(r)
				{
					top.show_loading_image("hide");
					if(r == 1)
					{
						top.fAlert('Record Deleted Successfully');
						$("#"+i_type+"_auth_information_"+grid_id).fadeOut('fast');					
					}
					else
					{
						top.fAlert('Error while deleting record. Please try again!!!');
						
					}
				}
			});
			
			top.show_loading_image("hide");
		}
		else{
			$("#"+i_type+"_auth_information_"+grid_id).fadeOut('fast');
		}
	}
	
// To Show/Hide Referral/Auth Grids
function showSub(id,strVal)
{
		var obj_div = $("#"+id);
		if(obj_div.hasClass('hidden') && strVal=="Yes")
		{
			obj_div.removeClass('hidden');
		}
		else
		{
			obj_div.addClass('hidden');
		}
}	


// Function to Perform action when change
// occur for Sub.Relation DropDown
function popUpRelation()
{
	var arguments = popUpRelation.arguments;
	callFrom=arguments[1];
	var s_name = callFrom.substr(0,3);
	arguments[4] = arguments[4] || 1;
	var i_key = arguments[4];
	var lname1 = "";
	var fname1 = "";		
	
	if(arguments[0] != "self")
	{
		if(typeof(arguments[2])!='boolean'){ arguments[2] = true; }
		if(arguments[2])
		{
			top.fancyConfirm("Clear Contact Details?","","window.top.fmain.popUpRelation('"+arguments[0]+"','"+arguments[1]+"',false,true,'"+arguments[4]+"')","window.top.fmain.popUpRelation('"+arguments[0]+"','"+arguments[1]+"',false,false,'"+arguments[4]+"')");
			return;
		}
		
		bk_action = arguments[3];
		if(bk_action == false)
		{
			return false;	
		}
		var ln = 'lastName' + i_key ;
		var hn = 'hid_' + s_name + '_subscriber_exits_our_sys';
		var fn =  'i'+i_key+'subscriber_fname';
		
		lname1 = document.getElementById(ln).value;
		if(arguments[0] != "Spouse"){	
			fname1 = document.getElementById(fn).value;
		}
		document.getElementById(hn).value = "no";	
		
		lname1 = (lname1 === '') ? document.getElementById("hidlastName").value : lname1;
		if(lname1)
		{
			var tmp = $("#lastName"+i_key);
			$("#search_patient_result #sp_ajax").data('iKey',i_key);
			search_patient(tmp);
			popUpRelationValue(i_key);
		}
		else{
			top.fAlert("Please enter last name to precede search");
		}
	}
	else
	{
		var i = 'sub_'+s_name+'_pat_id';
		document.getElementById(i).value = document.getElementById("hid_pat_id").value;
	}	
}

// Function to fill Relation info in their respective fields
//pid,title,iterfname,itermname,iterlname,itersuffix,status,dob_format,sex,street,zip,city,state,ss,phone_home,phone_biz,phone_cell,street2,zipext
function popUpRelationValue(call_from,d)
{
	call_from = call_from || '1';
	var has_data = (typeof d !== 'undefined') ? true : false;
	var s_name = 'pri';
	if(call_from == '2') s_name ='sec';
	else if(call_from == '3') s_name ='ter'; 
	
	$("#sub_"+s_name+"_pat_id")[0].value = has_data ? d.id : '' ;
	
	$("#i"+call_from+"subscriber_fname")[0].value=has_data ? d.fname : '' ;
	$("#i"+call_from+"subscriber_mname")[0].value =has_data ? d.mname : '' ;
	$("#lastName"+call_from)[0].value=has_data ? d.lname : '' ;
	$("#suffix_rel_"+s_name)[0].value=has_data ? d.suffix : '' ;	
	$("#i"+call_from+"subscriber_DOB")[0].value = has_data ? d.DOB : '' ;
	//$("#i"+call_from+"subscriber_sex")[0].value = (has_data) ? d.sex : '' ;
	$("#i"+call_from+"subscriber_street")[0].value = has_data ? d.street : '' ;
	$("#i"+call_from+"subscriber_street_2")[0].value = has_data ? d.street2 : '' ;
	$("#code"+call_from)[0].value = has_data ? d.postal_code : '' ;
	$("#city"+call_from)[0].value = has_data ? d.city : '' ;
	$("#state"+call_from)[0].value = has_data ? d.state : '' ;
	$("#i"+call_from+"subscriber_ss")[0].value = has_data ? d.ss : '' ;
	$("#i"+call_from+"subscriber_phone")[0].value = has_data ? core_phone_format(d.phone_home) : '' ;
	$("#i"+call_from+"subscriber_biz_phone")[0].value = has_data ? core_phone_format(d.phone_biz) : '' ;
	$("#i"+call_from+"subscriber_biz_phone_ext")[0].value = has_data ? d.phone_biz_ext : '' ;
	$("#i"+call_from+"subscriber_mobile")[0].value = has_data ? core_phone_format(d.phone_cell) : '' ;
	if($("#zip_ext"+call_from).length > 0 )
	$("#zip_ext"+call_from)[0].value = has_data ? d.zip_ext : '' ;
	$("#hid_"+s_name+"_subscriber_exits_our_sys")[0].value = has_data ? 'yes' : '' ; 
	//$("#i"+call_from+"subscriber_sex").selectpicker('val',(has_data ? d.sex : ''));
	$("#i"+call_from+"subscriber_sex").val(has_data ? d.sex : '');
}	


function getvalue(obj,s_name,i_key)
{
		s_name = s_name || 'pri';
		i_key = i_key || 1;
		
		if(obj.value == "self" || obj.value == "Self")
		{
			document.getElementById("i"+i_key+"subscriber_fname").value = patient_info.fname;
			document.getElementById("i"+i_key+"subscriber_mname").value = patient_info.mname;
			document.getElementById('lastName'+i_key).value = patient_info.lname;
			document.getElementById("i"+i_key+"subscriber_DOB").value = getDateFormat(patient_info.DOB);
			document.getElementById("i"+i_key+"subscriber_ss").value = patient_info.ss;
			if(patient_info.sex != "")
				$("#i"+i_key+"subscriber_sex").selectpicker('val',patient_info.sex);
			document.getElementById("i"+i_key+"subscriber_street").value = patient_info.street;
			document.getElementById("i"+i_key+"subscriber_street_2").value = patient_info.street2;
			document.getElementById("code"+i_key+"").value = patient_info.postal_code;
			if(document.getElementById("zip_ext"+i_key+"") != "undefined" && document.getElementById("zip_ext"+i_key+"") != null)
			document.getElementById("zip_ext"+i_key+"").value = patient_info.zip_ext;
			document.getElementById("city"+i_key+"").value = patient_info.city;
			document.getElementById("state"+i_key+"").value = patient_info.state;

			document.getElementById("i"+i_key+"subscriber_phone").value = (patient_info.phone_home ? core_phone_format(patient_info.phone_home) : '');
			document.getElementById("i"+i_key+"subscriber_biz_phone").value = (patient_info.phone_biz ? core_phone_format(patient_info.phone_biz) : '');
			document.getElementById("i"+i_key+"subscriber_biz_phone_ext").value = (patient_info.phone_biz_ext ? patient_info.phone_biz_ext : '');
			document.getElementById("i"+i_key+"subscriber_mobile").value = (patient_info.phone_cell ? core_phone_format(patient_info.phone_cell) : '');
			document.getElementById('hid_'+s_name+'_subscriber_exits_our_sys').value = "yes";
			document.getElementById("suffix_rel_"+s_name+"").value = patient_info.suffix;
			$("#i"+i_key+"subscriber_ss").triggerHandler('change');	
		}
		
}

function copy_insurance_div(dis,ins_com)
{
	document.getElementById('copy_ins_name').value = '';
	document.getElementById('copy_ins_comp_id').style.display = dis;
	
	if(dis == 'block'){
		document.getElementById('copy_ins_name').value = ins_com;
	}
}

function showPracMendAlertInsurence()
{
	var pri_relationship=$('#i1subscriber_relationship').val();
	var priInsClaimVal = $('#insCliamVal').val();
	if(priInsClaimVal=="1" && pri_relationship!="self"){
		top.fAlert('• For Medicare Insurance, Relationship Should be Self');
		return false;
	}
	msg = 'Please enter the following \n';
	msg_mandatory = "Following fields are mandatory : \n"		
	var alertActive = false;
	var alertActive_mandatory = false;
	
	var tmp_chk_arr1 = ['ref1_phy','eff1_date','end1_date','no_ref1','reffral_no1','reff1_date','note1'];
	var tmp_chk_arr2 = ['ref2_phy','eff2_date','end2_date','no_ref2','reffral_no2','reff2_date','note2'];	
	var tmp_chk_arr3 = ['ref3_phy','eff3_date','end3_date','no_ref3','reffral_no3','reff3_date','note3'];
	
	$.each(mandatory,function(i,v){
		if(typeof i === 'string')
		{
				var obj = $("#"+i);
				var t = '';
				var is_reff_fld = false;
				var reff_div_id = false;

				if($.inArray(i,tmp_chk_arr1)  !== -1 ){
					is_reff_fld = true; reff_div_id = 'sub_1';
					obj = $('[id^="'+i+'"]');
				}
				else if($.inArray(i,tmp_chk_arr2) !== -1){
					is_reff_fld = true;reff_div_id = 'sub_2';
					obj = $('[id^="'+i+'"]');
				}
				else if($.inArray(i,tmp_chk_arr3) !== -1){
					is_reff_fld = true;reff_div_id = 'sub_3';
					obj = $('[id^="'+i+'"]');
				}
				
			
				if(obj.length)
				{
						var t_msg = vocabulary[i];
						t_msg = t_msg.replace(/\\n/g,'');
						
						if(reff_div_id)
						{
							if(is_reff_fld && $("#"+reff_div_id).css('display') == 'block')
							{
								//console.log(i +'==' +obj.length + '----' + is_reff_fld + '--' + reff_div_id);
								var mandatoryAlert= true; var advisoryAlert = true;
								obj.each(function(){
										if( mandatoryAlert == false || advisoryAlert == false) return false;
										var id = $(this).attr('id');
										if($("#"+id).val() == '' && v == '1')
										{
											msg = msg + '<br>' + t_msg ;
											alertActive = true;
										} else mandatoryAlert = false;
										
										if($("#"+id).val() == '' && v == '2')
										{
											msg_mandatory = msg_mandatory + '<br>' +  t_msg;
											alertActive_mandatory = true;
										} else advisoryAlert = false;
								});
								
							}
						}
						else
						{
							if(obj.val() === '' && v == '1' ) 
							{
								msg = msg + '<br>' + t_msg;
								alertActive = true;
							}
							
							if(obj.val() === '' && v == '2' ) 
							{
								msg_mandatory = msg_mandatory + '<br>' + t_msg;
								alertActive_mandatory = true;
							}
							
							
						}
				}
		
		}
		
	});
	
	var pri_ins_pro_id = $('#insprovider1').val();
	var sec_ins_pro_id = $('#insprovider2').val();
	var ter_ins_pro_id = $('#insprovider3').val();
			
	var pri_ins_return = check_ins_exist(pri_ins_pro_id, 'primary');
	var sec_ins_return = check_ins_exist(sec_ins_pro_id, 'secondary');
	var tri_ins_return = check_ins_exist(ter_ins_pro_id, 'tertiary');
	
	if(pri_ins_return == true && sec_ins_return == true && tri_ins_return == true)
	{			
		if(pri_ins_pro_id == '' && dgi('self_pay_provider').checked==false){
			top.fAlert('• Primary Ins. Provider is required.');
			return false;
		}
		else{
			if(alertActive_mandatory == true && dgi('self_pay_provider').checked==false){
				
				top.fAlert(msg_mandatory);
			}
			else if(alertActive == true && dgi('self_pay_provider').checked==false){
				msg = msg + '<br>Do you want to continue.\n'; 
				top.fancyConfirm(msg, "", "window.top.fmain.askSepAccount();");
				return false;
			}
			else{
				validateInsurance("Insurance");
			}		
		}
	}
	
	
}

function askSepAccount()
{
	var confInsSub = "";
	if(document.getElementById("hid_pri_subscriber_exits_our_sys"))
	{							
		if(document.getElementById("hid_pri_subscriber_exits_our_sys").value == "no" && document.getElementById('i1subscriber_fname').value != "" && document.getElementById('lastName1').value != ""){																										
			document.getElementById('divAskSepAccountPri').style.display = "block";
		}								
	}	
	if(document.getElementById("hid_sec_subscriber_exits_our_sys"))
	{
		if(document.getElementById("hid_sec_subscriber_exits_our_sys").value == "no" && document.getElementById('i2subscriber_fname').value != "" && document.getElementById('lastName2').value != ""){																										
			document.getElementById('divAskSepAccountSec').style.display = "block";
		}								
	}	
	if(document.getElementById("hid_ter_subscriber_exits_our_sys"))
	{							
		if(document.getElementById("hid_ter_subscriber_exits_our_sys").value == "no" && document.getElementById('i3subscriber_fname').value != "" && document.getElementById('lastName3').value != ""){																										
			document.getElementById('divAskSepAccountTer').style.display = "block";
		}								
	}	
	if(document.getElementById('divAskSepAccountPri').style.display == "block" || document.getElementById('divAskSepAccountSec').style.display == "block" || document.getElementById('divAskSepAccountTer').style.display == "block"){
		document.getElementById('divAskSepAccount').style.display = "block";
	}
	else{
		//parent.document.getElementById('load_image21').style.display = 'block';
		validateInsurance("Insurance");	
	}		
}
	
var blPreviousInsChk = false;
var valConfirmMsg = '';
function validateInsurance(tab_save,i_key)
{
	msg = ''; msgs = ''; valConfirmMsg = '';
	i_key = i_key || 1;
	s_name = 'pri'; s_name_u = 'Pri'; i_type = 'primary'; i_type_u = 'Primary';
	if(i_key == 2) { s_name = 'sec'; s_name_u = 'Sec'; i_type = 'secondary'; i_type_u = 'secondary' }
	if(i_key == 3) { s_name = 'ter'; s_name_u = 'Ter'; i_type = 'tertiary'; i_type_u = 'Tertiary' }
	
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/ ; 
	
	cDate=Date.parse($("#from_date_subscriber"+i_key).val()); 
	dDate=Date.parse($("#i"+i_key+"subscriber_DOB").val()); 
	
	if(top.jquery_date_format != "undefined"){
		arrActDate = fnArrDate($("#i"+i_key+"effective_date").val(),top.jquery_date_format);
		actDate = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
		
		arrExpDate = fnArrDate($("#i"+i_key+"expiration_date").val(),top.jquery_date_format);
		expDate = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
		
	}else{
		actDate=Date.parse($("#i"+i_key+"effective_date").val()); 
		expDate=Date.parse($("#i"+i_key+"expiration_date").val()); 
	}
	
	expPriDate=Date.parse($("#"+s_name+"ExpirationDate").val());
	actPriDate=Date.parse($("#actPrevious"+s_name_u).val());
	
	var primaryInsNames = $("#"+i_type+"InsName").val();
	var primaryInsNames2 = $("#insprovider"+i_key).val();
	
	var patientName = myvar.$("#patientName").val();
	var expPreviousPriDate = $("#i"+i_key+"effective_date").val();
	
	var newmsg="";
	if(tab_save!="Insurance")
	{
		if (myvar.$("#i"+i_key+"provider").val() == '')
		{
			msg  = msg + '   '+i_type_u+' Provider Name is required.<br>';
			$("#insprovider"+i_key).select();
			$("#insprovider"+i_key).addClass('mandatory');
		}
		if($("i"+i_key+"policy_number").val() == ''){
			msg  = msg + '   '+i_type_u+' subscriber policy no is required.<br>'; 
			$("i"+i_key+"policy_number").addClass('mandatory');
		}	
		
		if($("#i"+i_key+"effective_date").val() == ''){
			msg  = msg + '   '+i_type_u+' Activation Date is required.<br>'; 
			$("#i"+i_key+"effective_date").addClass('mandatory');	
		}
		
		if($("#"+i_type+"Id").val() != $("#"+i_type+"MainId").val())
		{
			if($("#actPrevious"+s_name_u).val() != ''){
				if(actDate <= actPriDate){
					msg  = msg + '   '+i_type_u+' Activation Date Must Be Greater Than Previous Activation Date.<br>';
				}
			}
			
			if($("#i"+i_key+"expiration_date").val() != ''){
				if(actDate >= expDate){
					msg  = msg + '   '+i_type_u+' Expiration Date Must Be Greater Than Activation Date.<br>'; 
				}
				else if(primaryInsNames2 != ''){
					msgs += ''+i_type_u+' Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+primaryInsNames2+' for '+patientName+' ?<br>';
				}
			}	
		
			if($("#"+i_type+"Id").val() != ''){
				if($("#"+s_name+"ExpirationDate").val() != ''){			
					if(actDate <= expPriDate){
						msg  = msg + '   '+i_type_u+' Activate Date Must Be Greater Than Previous Expiration Date.<br>';		
					}
				}
				else{
					$("#expPrevious"+s_name_u+"").value = expPreviousPriDate;
					msgs += ''+i_type_u+' Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+primaryInsNames+' for '+patientName+' ?<br>';
				}
			}
		}
		else{
			if($("#i"+i_key+"expiration_date").val() != ''){			
				if(actDate >= expDate){
					msg  = msg + '   '+i_type_u+' Expiration Date Must Be Greater Than Previous Activate Date.<br>';		
				}
				else{
					$("#expPrevious"+s_name_u).value = expPreviousPriDate;
					msgs += ''+i_type_u+' Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+primaryInsNames+' for '+patientName+' ?<br>';
				}
			}			
		}
		
		if($("#i"+i_key+"subscriber_relationship").val() == ''){
			msg  = msg + '   '+i_type_u+' subscriber Relations is required.<br>'; 
			$("#i"+i_key+"subscriber_relationship").addClass('mandatory');
		}
		if($("#i"+i_key+"subscriber_fname").val() == ''){
			msg  = msg + '   '+i_type_u+' subscriber First Name is required.<br>'; 
			$("#i"+i_key+"subscriber_fname").addClass('mandatory');
		}
		if ($("#lastName"+i_key).val() == ''){
			msg  = msg + '   '+i_type_u+' subscriber Last Name is required.<br>'; 
			$("#lastName"+i_key).addClass('mandatory');
		}
		
		if($("#i"+i_key+"subscriber_DOB").val() != '00-00-0000')
		{ 
			 if(dDate >= cDate){
				msg = msg + '  - '+i_type_u+' Date of Birth Should Be Past Date.<br>';
				$("#i"+i_key+"subscriber_DOB").addClass('mandatory');	
			} 
		}
		
		if ($("#insprovider2").val() != 'Unassigned'){
			msg = validateInsurance('',2);
		}
		
		if ($("#insprovider3").val() != 'Unassigned'){
			msg = validateInsurance('',3);		
		}
	}
	else{//after prac. mand.	
		msg = validateInsuranceNew();	
		if( valConfirmMsg ) msgs = valConfirmMsg;
	}
	
	if(i_key === 2 || i_key === 3) return msg;
	
	
	if(msg == true){
		myvar.insuranceCaseFrm.chooseNewform.value = 'Save';				
		if(msgs){
			top.fancyConfirm(msgs,"","window.top.fmain.hideConfirmYesNo(1)", "window.top.fmain.hideConfirmYesNo(0)");
		}
		else{
			top.show_loading_image('hide');
			myvar.insuranceCaseFrm.submit();
		}
	}
	else{	
		if (blPreviousInsChk == true){
			myvar.insuranceCaseFrm.reset();				
		}
	}
}


function validateInsuranceNew(msg,i_key)
{
	var msgs = '';
	i_key = i_key || 1;
	s_name = 'pri'; s_name_u = 'Pri'; i_type = 'primary'; i_type_u = 'Primary';
	if(i_key == 2) { s_name = 'sec'; s_name_u = 'Sec'; i_type = 'secondary'; i_type_u = 'Secondary' }
	if(i_key == 3) { s_name = 'ter'; s_name_u = 'Ter'; i_type = 'tertiary'; i_type_u = 'Tertiary' }
	
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/  ;
	cDate=Date.parse($("#from_date_subscriber"+i_key).val()); 
	dDate=Date.parse($("#i"+i_key+"subscriber_DOB").val());
	 
	if(top.jquery_date_format != "undefined"){
		arrActDate = fnArrDate($("#i"+i_key+"effective_date").val(),top.jquery_date_format);
		actDate = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
		
		arrExpDate = fnArrDate($("#i"+i_key+"expiration_date").val(),top.jquery_date_format);
		expDate = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
		
	}else{
		actDate=Date.parse($("#i"+i_key+"effective_date").val()); 
		expDate=Date.parse($("#i"+i_key+"expiration_date").val()); 
	}
	
	expPriDate=Date.parse($("#"+s_name+"ExpirationDate").val());
	actPriDate=Date.parse($("#actPrevious"+s_name_u).val());
	
	var primaryInsNames = $("#"+i_type+"InsName").val();
	var primaryInsNames2 = $("#insprovider"+i_key).val();

	
	var patientName = myvar.$("#patientName").val();
	var expPreviousPriDate = $("#i"+i_key+"effective_date").val();

	if(primaryInsNames == '')
		primaryInsNames = $("#insprovider"+i_key).val();
	
	var newmsg="";
	var msg = "";
	var blChkforChagePriIns = false;
	if($("#"+i_type+"Id").val() != "" &&  $("#"+i_type+"MainId").val() != ""){
		if($("#"+i_type+"Id").val() != $("#"+i_type+"MainId").val()){
				blChkforChagePriIns = true;
		}
	}
	if(blChkforChagePriIns == true){		
		if($("#actPrevious"+s_name_u).val() != ''){
			if(actDate <= actPriDate){
				msg  += '   '+i_type_u+' Activation Date Must Be Greater Than Previous Activation Date.<br>';
			}
		}
			
		if($("#i"+i_key+"expiration_date").val() != ''){
			if(actDate > expDate){
				msg  +=  '   '+i_type_u+' Expiration Date Must Be Greater Than Activation Date.<br>'; 
			}
			else if(primaryInsNames2 != ''){
				exitingExpirationDate=$("#"+s_name+"ExitingExpirationDate").value;
				if($("#i"+i_key+"expiration_date").val() != exitingExpirationDate){
					$("#expPrevious"+s_name_u).value = expPreviousPriDate;
					msgs += ''+i_type_u+' Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+primaryInsNames2+' for '+patientName+' ?<br>';
				}
			}
		}	
		
		if($("#"+i_type+"Id").val() != ''){
			
			if($("#"+s_name+"ExpirationDate").val() != ''){		
			
				
				if(actDate <= expPriDate){
					msg  +=  '   '+i_type_u+' Activate Date Must Be Greater Than Previous Expiration Date.<br>';		
				}
				
				exitingExpirationDate=$("#"+s_name+"ExitingExpirationDate").val(); 
				insurenceProviderExit=$("#insurenceProviderExit"+s_name_u).val();
				newinsprovider=$("#insprovider"+i_key).val();
				if(insurenceProviderExit!=newinsprovider){
					if($("#"+s_name+"ExitingExpirationDate").val() =="00-00-0000"){
						msg  += 'Please expire previous '+i_type+' insurance.<br>';
						blPreviousInsChk = true;
					}
				}
				else if(insurenceProviderExit == newinsprovider && $("#i"+i_key+"expiration_date").val() == ""){
					if($("#"+s_name+"ExitingExpirationDate").val() =="00-00-0000"){
						//msg  = msg + 'Please expire previous insurance before inserting new one.2p\n';
					}
				}
			}
			else{
				
				$("#expPrevious"+s_name_u).val(expPreviousPriDate);
				if($("#"+s_name+"ExitingExpirationDate").val() == "00-00-0000"){
					msg  += 'Please expire previous '+i_type+' insurance.<br>';
					blPreviousInsChk = true;
				}
				exitingExpirationDate=$("#"+s_name+"ExitingExpirationDate").val();
				if($("#i"+i_key+"expiration_date").val() != exitingExpirationDate){
					msgs += ''+i_type_u+' Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+primaryInsNames+' for '+patientName+' ?<br>';
					
				}
			}
		}
	}
	else{
		
		if($("#i"+i_key+"expiration_date").val() != ''){			
			if(actDate > expDate){
				msg  +=  '   '+i_type_u+' Expiration Date Must Be Greater Than Previous Activate Date.<br>';		
			}
			else
			{				
				$("#expPrevious"+s_name_u).val(expPreviousPriDate);
				
				exitingExpirationDate=$("#"+s_name+"ExitingExpirationDate").val(); 
				
				if($("#i"+i_key+"expiration_date").val() !=exitingExpirationDate){
					msgs += ''+i_type_u+' Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+primaryInsNames+' for '+patientName+' ?<br>';
				}
			}
		}			
	}

	if($("#i"+i_key+"subscriber_DOB").val() != '00-00-0000')
	{
		if(dDate >= cDate){
			msg +=  '  - '+i_type_u+' Date of Birth Should Be Past Date.<br>';
			$("#i"+i_key+"subscriber_DOB").addClass('mandatory')			
		} 
	}
	
	if($('#i'+i_key+'referalreq').val() == "Yes")
	{
		var pri_ref_ch_flag = 0;
		$('#'+i_type+'ReffCont div.table_grid').each(function()
		{	
			if(pri_ref_ch_flag == 0)
			{
				reff_start_date = $('.reff_start_date_cl',$(this)).val();
				reff_end_date = $('.reff_end_date_cl',$(this)).val();
				
				if(top.jquery_date_format != "undefined"){
					arrActDate = fnArrDate(reff_start_date,top.jquery_date_format);
					actRefTerDate = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
					
					arrExpDate = fnArrDate(reff_end_date,top.jquery_date_format);
					endRefTerDate = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
					
				}else{
					actRefTerDate=Date.parse(reff_start_date); 
					endRefTerDate=Date.parse(reff_end_date); 
				}
				
				
				if(actRefTerDate >= endRefTerDate)
				{
					msg  += '   '+i_type_u+' Referral End Date Must Be greater Than Start Effected.<br>'; 	
					pri_ref_ch_flag = 1;
				}
			}
		});
	}	
	
	
	if(i_key === 2 || i_key === 3) {
		valConfirmMsg += msgs
		return msg;
	}
	else
	{
		valConfirmMsg += msgs
		if ($("#insprovider2").val() != 'Unassigned'){
			msg += validateInsuranceNew(msg,2);
		}
		if ($("#insprovider3").val() != 'Unassigned'){
			msg += validateInsuranceNew(msg,3);		
		}
		
	}
	
	if(msg){
		top.fAlert(msg);
		return false;
	}
	else{
		return true;
	}
	
}

function hideConfirmYesNo(obj)
{	
	if(obj == 1){		
		myvar.insuranceCaseFrm.submit();
	}
	else{
	top.show_loading_image('hide');	//top.fmain.document.getElementById('load_image21').style.display='none';
	}
	if(typeof(myvar.document.getElementById('div_slotys2'))!="undefined" && myvar.document.getElementById('div_slotys2') !== null )
	{
		myvar.document.getElementById('div_slotys2').style.visibility = 'hidden';
		myvar.document.getElementById('div_slotys2').innerHTML='';
	}
}
	
function switch_ins(val,type)
{
	var ins_obj = document.getElementsByName("compId[]");	
	if(val == 'Primary'){
		document.reArrangeFrm.name_Secondary[0].checked = false;
		document.reArrangeFrm.name_Secondary[1].checked = false;
		document.reArrangeFrm.name_Secondary[2].checked = false;
		document.reArrangeFrm.name_Tertiary[0].checked = false;
		document.reArrangeFrm.name_Tertiary[1].checked = false;
		document.reArrangeFrm.name_Tertiary[2].checked = false;
		
		if(type == 'Primary'){			
			document.reArrangeFrm.name_Secondary[1].checked = true;
			document.reArrangeFrm.name_Tertiary[2].checked = true;
		}
		else if(type == 'Secondary'){
			document.reArrangeFrm.name_Secondary[0].checked = true;
			document.reArrangeFrm.name_Tertiary[2].checked = true;
		}
		else if(type == 'Tertiary'){
			document.reArrangeFrm.name_Secondary[1].checked = true;
			document.reArrangeFrm.name_Tertiary[0].checked = true;
		}
	}
	else if(val == 'Secondary'){
		document.reArrangeFrm.name_Primary[0].checked = false;
		document.reArrangeFrm.name_Primary[1].checked = false;
		document.reArrangeFrm.name_Primary[2].checked = false;
		document.reArrangeFrm.name_Tertiary[0].checked = false;
		document.reArrangeFrm.name_Tertiary[1].checked = false;
		document.reArrangeFrm.name_Tertiary[2].checked = false;
		if(type == 'Primary'){
			document.reArrangeFrm.name_Primary[1].checked = true;				
			document.reArrangeFrm.name_Tertiary[2].checked = true;
		}
		else if(type == 'Secondary'){
			document.reArrangeFrm.name_Primary[0].checked = true;				
			document.reArrangeFrm.name_Tertiary[2].checked = true;
		}
		else if(type == 'Tertiary'){
			document.reArrangeFrm.name_Primary[0].checked = true;				
			document.reArrangeFrm.name_Tertiary[1].checked = true;
		}
	}
	else if(val == 'Tertiary'){
		document.reArrangeFrm.name_Primary[0].checked = false;
		document.reArrangeFrm.name_Primary[1].checked = false;
		document.reArrangeFrm.name_Primary[2].checked = false;
		document.reArrangeFrm.name_Secondary[0].checked = false;
		document.reArrangeFrm.name_Secondary[1].checked = false;
		document.reArrangeFrm.name_Secondary[2].checked = false;
		if(type == 'Primary'){			
			document.reArrangeFrm.name_Primary[2].checked = true;
			document.reArrangeFrm.name_Secondary[1].checked = true;
		}
		else if(type == 'Secondary'){
			document.reArrangeFrm.name_Primary[0].checked = true;
			document.reArrangeFrm.name_Secondary[2].checked = true;
		}
		else if(type == 'Tertiary'){
			document.reArrangeFrm.name_Primary[0].checked = true;
			document.reArrangeFrm.name_Secondary[1].checked = true;
		}
	}
}

function make_null_provider_id(obj)
{
	if(obj.id == "insprovider1"){
		document.getElementById('i1provider').value = '';		
	}
	else if(obj.id == "insprovider2"){
		document.getElementById('i2provider').value = '';
	}
	else if(obj.id == "insprovider3"){
		document.getElementById('i3provider').value = '';
	}
	
}

function lost_focus(field)
{
	//console.log(arguments);
	//var arguments = lost_focus.arguments[1];console.log(arguments);
	var arguments = arguments[0].className;//lost_focus.arguments[1];
	var hasMandatory = arguments.indexOf("mandatory-chk") >= 0 ? true : false;
	var hasAdvisory = arguments.indexOf("advisory-chk")  >= 0 ? true : false;
	if( hasMandatory ) {
		if( trim(field.value)==""  ) field.className='form-control mandatory-chk mandatory';
		else field.className ='form-control mandatory-chk';
	}
	else if( hasAdvisory ) {
		if( trim(field.value)==""  ) field.className='form-control advisory-chk advisory';
		else field.className ='form-control advisory-chk';
	}
}

function changeClassCombo()
{
	var arguments = changeClassCombo.arguments;
	var v = arguments[0].value;
	var c = arguments[0].className;
	var h = c.indexOf("mandatory-chk") >= 0 ? true : false;
	var a = c.indexOf("advisory-chk") >= 0 ? true : false;
	if(v !="" && h){
		arguments[0].className="form-control minimal mandatory-chk";
	}
	else if(v !="" && a){
		arguments[0].className="form-control minimal advisory-chk";
	}
	else if(v == "" && h){
		arguments[0].className="form-control minimal mandatory-chk mandatory";
	}
	else if(v == "" && a){
		arguments[0].className="form-control minimal advisory-chk advisory";
	}
}

function changeBackground(id)
{
	
	var preObj;
	if(document.getElementById("preObjBack")) {
		preObj = document.getElementById("preObjBack").value;
		if(preObj!=''){
			if(document.getElementById(preObj)) {
				document.getElementById(preObj).style.background = '#F4F9EE';
			}
		}
		document.getElementById("preObjBack").value = id;
	}
	
	if(document.getElementById(id)) {
		document.getElementById(id).style.background = '#FFFFCC';
	}
}

function scan_card(type,id)
{
	var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/insurance/scan/scan_card.php?';
	url += 'type='+type;
	url += '&isRecordExists='+id;
	top.popup_win(url,'lic','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1');	
}
	
function bind_datepicker(e)
{
	$('.datepicker').datetimepicker({lazyInit:true,timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
}

function setSepAccHidVal(choice)
{
	if(choice == "ok")
	{
		if(document.getElementById('cbkAskSepAccountPri').checked == true && document.getElementById('divAskSepAccountPri').style.display == "block"){
			document.getElementById("hid_create_acc_pri_ins_sub").value = "yes";
		}
		else{
			document.getElementById("hid_create_acc_pri_ins_sub").value = "no";
		}
		
		if(document.getElementById('cbkAskSepAccountSec').checked == true && document.getElementById('divAskSepAccountSec').style.display == "block"){
			document.getElementById("hid_create_acc_sec_ins_sub").value = "yes";
		}
		else{
			document.getElementById("hid_create_acc_sec_ins_sub").value = "no";
		}	
		
		if(document.getElementById('cbkAskSepAccountTer').checked == true && document.getElementById('divAskSepAccountTer').style.display == "block"){
			document.getElementById("hid_create_acc_ter_ins_sub").value = "yes";
		}
		else{
			document.getElementById("hid_create_acc_ter_ins_sub").value = "no";
		}
	}
	else if(choice == "cancel"){
		document.getElementById("hid_create_acc_pri_ins_sub").value = "no";
		document.getElementById("hid_create_acc_sec_ins_sub").value = "no";
		document.getElementById("hid_create_acc_ter_ins_sub").value = "no";			
	}
	document.getElementById("divAskSepAccount").style.display = "none";
	parent.document.getElementById('load_image21').style.display = 'block';
	validateInsurance("Insurance");					
}

function show_scanned(_this){
	var s = $(_this).data('src');
	var modal_popup = parseInt($("#show_ins_scan_in_modal").val());
	if( modal_popup ) {
		var h = $(_this).attr('title');
		var i = '<img src="'+s+'" title="'+h+'" />';
		$("#scan_card_show").find('h4#modal_title').html(h);
		$("#scan_card_show").find('.modal-body').html(i).addClass('text-center');
		$("#scan_card_show").fadeIn('fast');
	}
	else {
		top.popup_win(s,'location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width=800,height=700');
	}
	
}

function showPdf(url){ /* Scanned PDF*/
	var ev = window.event;
	if(ev){
		ev.cancelBubble = true;
		if (ev.stopPropagation) ev.stopPropagation();
	}
	top.popup_win(url, 'width=800', 'height=700');
}

function hideDrop(_this){
	$(_this).siblings('[data-toggle=\"dropdown\"]').parent().removeClass('open');
}

function change_case_chk(case_id){
	if(case_id > 0){
		var url=top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/insurance/get_ins_id.php?caseId='+case_id;
		$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					top.show_loading_image("hide");
					$("#copy_ins_data_from").html(resp);
					$('.selectpicker').selectpicker('refresh');
					//$("#change_ins_comp").html(resp);
				}
		});
	}
}

function getNewCom(obj){		
	document.getElementById("divOpen").value = obj;
	document.insuranceCaseFrm.submit();
}

function saveAuthHxData(type) {
	
	type = typeof type ==='undefined' ? '' : type;
	type = type ? type : 'primary';
	
	var b  = $("#authHxBody"+type);
	var cd = false; // Change Detected
	
	var idArr = []; var valArr = [];
	$.each(b.find('[name^=authEndDate]'),function(u,v){
		var arr = $(v).data();
		var p = arr.prevValue;
		var c = arr.counter;
		var v = $(v).val();
		var i = $("#authID_"+c).val();
		
		if( v !== p ) {
			cd = true;
			idArr.push(i);
			valArr.push(v);
		}
	});
	
	if( !cd ) {
		top.fAlert('No change detected!!!');
		return false;
	}
	
	var u = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/insurance/ajax.php';
	$.post(u,{action:'updateAuthHxDates',ids:idArr,val:valArr},function(r){
		top.alert_notification_show('Auth Hx updated successfully.');
	},"json").always(function(){
		top.show_loading_image('hide');
		top.fmain.location.reload(true);
	});
	
}

function bind_typeahead(obj) {
			if( typeof obj !== 'object' ) return false;
			
			obj.typeahead({items:3,scrollBar:true,ajax:{
                        url: top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/insurance/ajax.php?action=cptData',
                        timeout: 500,
                        items:3,scrollBar:true,
                        triggerLength: 1,
                        method: "get",
                        loadingClass: "loading-circle",
                        preProcess: function (data) {
                            return data.data;
                        }
                }});
			
		}

$(function () {
 	
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
	bind_datepicker();
	
	$("body").on('click','#sp_ajax_btn',function(e){ search_patient($('#sp_ajax')); });
	$("body").on('keyup','#sp_ajax',function(event){ if( event.keyCode == 13) search_patient($('#sp_ajax')); });
	
	$("body").on('click','[data-grid="resp"]',function(e){
			var i = $(this).data('row'); var t = $(this).data('grid'); 
			var c = $(this).data('i-key'); fill_grid_info(i,t,c);
	});
	
	$("body").on('click','.search_physician',function(e)
	{
		var d = $(this).data('source'); var o = $("#"+d); 
		search_physician(o);
	});
	
	$("body").on('click','a[data-click="pick_physician"]',function(e){ 
		var d = $(this).data(); $("#"+d.idBox).val(d.refId); $("#"+d.textBox).val(d.name);
		$("#phy_ajax").val('');
		$("#search_physician_result .modal-body").html('<div class="loader"></div>');
		$("#search_physician_result").modal('hide');
	});
	
	
	$("body").on('focus','.table_grid',function(){
		var d = $(this).attr('id');
		changeBackground(d);
	});
	
	// Onload 
	//var url = top.JS_WEB_ROOT_PATH + '/interface/patient_info/ajax/insurance/ajax.php?action=insCompsAnchors';
	//top.master_ajax_tunnel(url,top.fmain.xhr_ajax,'','json');
	
	top.show_loading_image('hide');
	
	if(typeof(selectpicker) == 'function') $('.selectpicker').selectpicker();
	// Add Mandatory Class to fields 

	$.each(mandatory_fld,function(i,v){
		var obj = false;
		if( $('#'+v).length > 0  ) obj = $('#'+v);
		else if( $("[id^="+v+"]").length > 0) obj = $("[id^="+v+"]")

		if( obj )	obj.addClass('mandatory-chk');
	});

	if( typeof(advisory_fld) !== 'undefined' ) {
		$.each(advisory_fld,function(i,v){
			var obj = false;
			if( $('#'+v).length > 0  ) obj = $('#'+v);
			else if( $("[id^="+v+"]").length > 0) obj = $("[id^="+v+"]")
			
			if( obj )	obj.addClass('advisory-chk');
		});
	}
	
	$("body").on("keycustom","[id=insprovider1]",function(){
		lost_focus(this,'form-control');
	});
	
	// Triggering events on window load
	$('select.mandatory-chk:not([id$="subscriber_relationship"]),select.advisory-chk:not([id$="subscriber_relationship"])').trigger('change');
	$('input.mandatory-chk:not([id=insprovider1]),input.advisory-chk:not([id=insprovider1]),textarea.mandatory-chk,textarea.advisory-chk').trigger('blur');
	$('[id=insprovider1]').trigger('keycustom');
	$("#hidChkChangeInsTabDb",top.document).val('no');
	$("#hidChkInsTabDbStatus",top.document).val('loaded');
});