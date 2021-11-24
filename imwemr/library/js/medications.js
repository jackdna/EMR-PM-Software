

function med_change_val(){
	$('#change_data').val('yes');
}

function setTxtArea(ctrlId)
{
	if($("#"+ctrlId).is(':checked'))	
		$('#comments').prop('disabled',false);
	else
		$('#comments').prop('disabled',true);
	
}

function statusOfAllInputs()
{
	var obj = $(".modules").find('input,select,textarea');
	var img_obj = $('img[id^="add_row_"]');
	
	if( $('#commonNoMedications').is(':checked') )
	{
		obj.prop('disabled', true);
		img_obj.hide(10);
	}
	else
	{
		obj.prop('disabled', false);
		img_obj.show(10);
	}
}

function open_dur()
{
	var wn = window.open('medications/erx_dur.php','dur_window','width=1280,height=650');
	wn.moveTo(0,0);
	wn.focus();
}

function chkBoxSetting(chk1, chk2, chk3)
{
	if(document.getElementById(chk1)){
		document.getElementById(chk1).checked=false;			
	}
	if(document.getElementById(chk2)){
		document.getElementById(chk2).checked=false;			
	}
	if(document.getElementById(chk3)){
		document.getElementById(chk3).checked=false;			
	}
}

function insertMedIdVizChange(olddata,obj,e, hidMedObj)
{				
	e = e || event;				
	characterCode = e.keyCode;
	if(obj.type == "text" || obj.type == "textarea"){
		var newData = obj.value;
		
		if(characterCode != 9 && characterCode != 16 ){
			if(olddata != newData){
				var strValue = document.getElementById("hidMedIdVizChange").value;
				var intMedId = (hidMedObj) ? hidMedObj.value : "";
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}					
				document.getElementById("hidMedIdVizChange").value = strValue;
			}				
		}	
	}
	else if(obj.type == "checkbox"){
		var newData = "";
		if(obj.checked == true){
			newData = "checked";
		}
		if(olddata != newData){
			if(olddata != newData){
				var strValue = document.getElementById("hidMedIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}
				document.getElementById("hidMedIdVizChange").value = strValue;
				document.getElementById('change_data').value='yes';
			}
		}			
	}
	else if(obj.type == "radio"){					
		var newData = "";
		if(obj.checked == true){
			newData = "checked";
		}
		if(olddata != newData){
			if(olddata != newData){
				var strValue = document.getElementById("hidMedIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}
				document.getElementById("hidMedIdVizChange").value = strValue;
				document.getElementById('change_data').value='yes';
			}
		}
	}
	else if(obj.type == "select-one"){	
		var strValue = document.getElementById("hidMedIdVizChange").value;
		var intMedId = hidMedObj.value;
		if(strValue.search(intMedId) < 0){
			strValue = strValue + intMedId + ",";
		}
		document.getElementById("hidMedIdVizChange").value = strValue;
	}
}

function checkdatetime(o)
{
	var s = $.trim(o.value);	
	if(s!=""){
		var matches = s.match(/^(\d{2})\-(\d{2})\-(\d{4}) (\d{2}):(\d{2}):(\d{2})$/);
		if (matches === null) {
			top.fAlert("Please enter valid date and time!");	
			o.value="";
		}
	}
}

function indexEnt(str_mode)
{
		var evt = window.event;
		if(evt.keyCode == 13){
			if(str_mode == "check"){
				return false;
			}
			var objId = evt.srcElement.id;
			var name = objId.substr(0,objId.length-1);
			
			var entName = parseInt(objId.substr(objId.length-1,objId.length));
			entName = entName+1;
			var setEnt =  name+entName;
			
			var field = document.getElementById(setEnt);
			try{
				field.focus();
			}catch(err){
				return false;
			}
			evt.cancelBubble = true;
		}
	}
	
function indexEntCheck()
{
	if(document.getElementById('tat_table')){
		indexEnt('check');
	}else{
		indexEnt();
	}
}

function check_umls(obj,index)
{
	index = index || "0";
	medName = top.trim(obj.value);
	if(medName != "" && document.getElementById("ccda_code"+index).value==""){
	top.show_loading_image('show');
	$.ajax({
			type: "POST",
			url: top.JS_WEB_ROOT_PATH+"/interface/Medical_history/medications/check_umls.php?medName="+encodeURI(medName)+"&index="+index,
			complete: function(r){
				response = r.responseText;
				if(response != null && typeof(response)!='undefined' && response!=''){
					$('#umls_content').html(response);
					$('#med_umls_fdb_modal').modal('show');
					
				}else{
					$('#div_umls').modal('hide');
					
				}
				top.show_loading_image('hide');
			}
			});
	}
	
}

function fill_med_code(med,code,index)
{
	$('#textTitle'+index).val(med);
	$('#ccda_code'+index).val(code);
	$('#fdb_id'+index).val('');
	
	if(document.getElementById('ccda_code'+index))
	{
		var ccda_old_val = $('#ccda_code_hidd'+index).val();
		var tmp = document.getElementById('ccda_code'+index).value;
		if(parent && parent.chkChange && typeof(parent.chkChange)!="undefined"){	parent.chkChange(tmp,document.getElementById('ccda_code'+index)); }
		insertMedIdVizChange(ccda_old_val,document.getElementById('ccda_code'+index),event,document.getElementById('med_id'+index));
		med_change_val();
	}
	check_fdb(index);
}

function check_fdb(index)
{
	return false;
	
	//Skipped ajax requested Erx Functionality 
	
	/*var medName = $('#textTitle'+index).val();
	if(medName!=""){
		top.show_loading_image("show",100);
		$.ajax({
				type: "POST",
				url: top.WRP+"/interface/admin/console/Medication_type_ahead/check_fdb.php?med_name="+encodeURI(medName)+"&index="+index,
				complete: function(r){
					response = r.responseText;
					top.show_loading_image("hide");
					if(response != null && typeof(response)!='undefined' && response!=''){
						$('#fdb_content',window.document).html(response);
					}
					
				}
		});
	}*/
}

function openWindowCCDImport(path,ccda_type)
{
	path = encodeURI(path);
	ccda_type = encodeURI(ccda_type);
	window.open(top.JS_WEB_ROOT_PATH + "/interface/reports/ccd/display_imported_ccd.php?path="+path+"&ccda_type="+ccda_type);
}

function med_change_val()
{
	$('#change_data').val('yes');
}

function setCBKOcular(obj, cbk)
{
	var objVal = obj.value;
	var arrObjVal = new Array();		
	arrObjVal = objVal.split('*');		
	obj.value = trim(arrObjVal[0]);
	if(arrObjVal.length > 1){
		if(document.getElementById(cbk)){
			document.getElementById(cbk).checked = true;
		}
	}		
}

function addNewMedicine(event,textBox)
{
	//For mouse right click 
	if (event.button==2)
	{
		$("#admin_medications_modal").modal('show');
		$("#hidd_click_field_id").val(textBox.id);
		$("#medicineName").data('sub-action','insert').data('record-id',0).val('');
		$("#medicineNameSave").html('Save');
		document.oncontextmenu = new Function("return false");
		return false;			
	}
}

function del_admin_medicine(id)
{
	id = parseInt(id);
	if(id > 0 )
	{
		$.ajax({
			url : top.JS_WEB_ROOT_PATH + '/interface/Medical_history/ajax/ajax_handler.php',
			dataType:"json",
			type:'POST',
			data : {'action':'admin_medicine','subAction' : 'delete', 'recordId' : id },
			success:function(r)
			{
				if(r.result === 1)
				{
					top.fAlert('Record Deleted Successfully');
					$("#admin_medications_data").html(r.data);
					medicine_typeahead('refresh');
				}
				else
				{
					top.fAlert('Something went wrong....Please try again...!!!');
				}
			}
			
		});
	}
	return false;
}

var ty_med_title = []; var ty_ccda_code = {}; var ty_fbd_id = {};
var ty_doses_code = {}; var ty_sig_code = {};
function medicine_typeahead(type)
{
	if(type === 'refresh')
	{
		$.ajax({
			url : top.JS_WEB_ROOT_PATH + '/interface/Medical_history/ajax/ajax_handler.php',
			dataType:"json",
			type:'POST',
			data : {'action':'medicine_typeahead'},
			success:function(r)
			{
				ty_med_title = r.medicationTitleArr;
				ty_ccda_code = r.medication_ccdacode_Arr;
				ty_doses_code = r.medication_doses_Arr;
				ty_sig_code = r.medication_sig_Arr;
				ty_fbd_id = r.fdb_id_arr;
				bind_med_typeahead();
			}
		});
	}
	else
	{
		bind_med_typeahead();
	}
}

function bind_med_typeahead()
{
	$("[id^='textTitle']").each(function(id,elem){
		$(elem).typeahead('destroy');
		$(elem).typeahead({
				source:ty_med_title,
				items:-1,scrollBar:true,
				onSelect:function(item){
					var v = item.value;
					var i = $(elem).attr('tabindex');
					$("#ccda_code"+i).val(ty_ccda_code[v]);
					//doses
					$("#md_dosage"+i).val(ty_doses_code[v]);
					//sig
					$("#md_sig"+i).val(ty_sig_code[v]);
					$("#fdb_id"+i).val(ty_fbd_id[v]);
				}
		});
	});
}

function removeTableRow(id,cnt)
{
	if(id == ''){
		$('#textTitle'+cnt).val('');
		$('#md_dosage'+cnt).val('');
		$('#tbl_md_row_'+cnt).fadeOut();
	}
	else
	{
		var scf = $("#subcallFrom").val(); 
		var prv_frmid = $("#prv_frmid").val();		
		if(typeof(prv_frmid)!="undefined" && prv_frmid!=""){
			if(confirm("Are you sure to want deleting this record!")){
				document.location.href = top.JS_WEB_ROOT_PATH+"/interface/Medical_history/index.php?showpage=medication&callFrom=WV&subcall="+scf+"&divH="+document.getElementById("divH").value+"&mode=delete&del_id="+id+"&prv_frmid="+prv_frmid;
			}
		}else if(isDssEnable){
			top.fancyConfirm("Are you sure to want deleting this record! Please validate your Electronic Signature.","","top.dssValidateElectronicSignature('show', "+id+", '"+callFrom+"')","top.rt_false()");
		} else {
			if(callFrom != 'WV')
			{
				top.fancyConfirm(vocabulary.delete,"", "window.top.show_loading_image('show');window.top.fmain.document.location.href='"+top.JS_WEB_ROOT_PATH +"/interface/Medical_history/index.php?showpage=medication&mode=delete&del_id="+id+"'","window.top.rt_false()");
			}
			else
			{
				if(confirm("Are you sure to want deleting this record!")){
					document.location.href = top.JS_WEB_ROOT_PATH+"/interface/Medical_history/index.php?showpage=medication&callFrom=WV&subcall="+scf+"&divH="+document.getElementById("divH").value+"&mode=delete&del_id="+id;
				}
			}
		}	
	}
}

function addNewRow(cnt1,type)
{
	type = type || '';
	cnt = document.getElementById('cnt').value;
	var pre_cnt = cnt1;
	var imgObj = $("#add_row_"+cnt1);
	imgObj.attr('title','Delete Row');
	imgObj.attr('class','glyphicon glyphicon-remove');
	imgObj.attr('onclick','removeTableRow(\'\','+pre_cnt+');');
	imgObj.attr('id','');
	cnt++;
	
	var selCols = $("#sel_columns").val();
	var temp  = "'md_occular"+cnt+"'";
	var eye_checked = (type == 4) ? "checked" : '';	
	var chk_change = (callFrom !== 'WV') ? "chk_change('',this,event);" : '';
	
	var html = '';
	html += '<tr id="tbl_md_row_'+cnt+'" >';
	//Title Column
	html += '<td>';
	html += '<input type="hidden" name="med_type'+cnt+'" id="med_type'+cnt+'" value="'+type+'">';
		html += '<input type="hidden" name="med_tw_ddi'+cnt+'" id="med_tw_ddi'+cnt+'" value="" />';
		html += '<input type="hidden" name="med_tw_id'+cnt+'" id="med_tw_id'+cnt+'" value="" />';
	html += '<input type="hidden" name="med_id'+cnt+'" id="med_id'+cnt+'" value="" >';
	//html += '<div class="input-group">';
	html += '<input type="text" class="form-control" id="textTitle'+cnt+'" tabindex="'+cnt+'" name="md_medication'+cnt+'" value="" onKeyUp="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));"  onMouseDown="addNewMedicine(event,this);" oncontextmenu="return false" onChange="'+chk_change+' setCBKOcular(this,\'md_occular'+cnt+'\'); med_change_val();" onfocus="check_umls(this,'+cnt+')">';
	//html += '</div>';
	html += '</td>';
	
	// Dosage
	html += '<td>';
	html += '<input type="text" id="md_dosage'+cnt+'" tabindex="'+cnt+'" name="md_dosage'+cnt+'" onKeyUp="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" value="" class="form-control" onKeyDown="indexEnt();" onChange="med_change_val();">';
	html += '</td>';
	
	//Sites
	
	if(type == 4)
	{
		html += '<td class="text-center" width="20">';
		html += '<div class="radio">';
		html += '<input type="radio" name="md_occular'+cnt+'" id="md_ou'+cnt+'" value="3" tabindex="'+cnt+'" onClick="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); chkBoxSetting(\'md_od'+cnt+'\', \'md_os'+cnt+'\', \'md_po'+cnt+'\')" >';
		html += '<label for="md_ou'+cnt+'"></label>';
		html += '</div>';
		html += '</td>';
		
		html += '<td class="text-center" width="20">';
		html += '<div class="radio">';
		html += '<input type="radio" name="md_occular'+cnt+'" id="md_od'+cnt+'" value="2" tabindex="'+cnt+'" onClick="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); chkBoxSetting(\'md_os'+cnt+'\', \'md_ou'+cnt+'\', \'md_po'+cnt+'\');" >';
		html += '<label for="md_od'+cnt+'"></label>';
		html += '</div>';
		html += '</td>';
		
		html += '<td class="text-center" width="20">';
		html += '<div class="radio">';
		html += '<input type="radio" name="md_occular'+cnt+'" id="md_os'+cnt+'" value="1" tabindex="'+cnt+'" onClick="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); chkBoxSetting(\'md_od'+cnt+'\', \'md_ou'+cnt+'\', \'md_po'+cnt+'\');" >';
		html += '<label for="md_os'+cnt+'"></label>';
		html += '</div>';
		html += '</td>';
		
		html += '<td class="text-center" width="20">';
		html += '<div class="radio">';
		html += '<input type="radio" name="md_occular'+cnt+'" id="md_po'+cnt+'" value="4" tabindex="'+cnt+'" onClick="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); chkBoxSetting(\'md_od'+cnt+'\', \'md_ou'+cnt+'\', \'md_os'+cnt+'\');" />';
		html += '<label for="md_po'+cnt+'"></label>';
		html += '</div>';
		html += '</td>';
	}
	
  //Sig.
	html += '<td>';
	html += '<textarea class="form-control" id="md_sig'+cnt+'" tabindex="'+cnt+'" name="md_sig'+cnt+'" onKeyDown="indexEnt();" onChange="med_change_val();" onKeyUp="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" rows="1" onMouseDown="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" title=""></textarea>';
	html += '</td>';
	
	if(type == 1)
	{
		html += '<td>';
		html += '<select class="form-control minimal" data-width="70px" data-size="10" name="md_route'+cnt+'" id="md_route'+cnt+'" tabindex="'+cnt+'" onChange="'+chk_change+' med_change_val(); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" title="Select" data-container="#select-container" >';
		html += routeOptions;
		html += '</select>';
		html += '</td>';	
	}
  // Compliant YES
	html += '<td class="text-center" width="20">';
	html += '<div class="radio">';
	html += '<input type="radio" name="compliant'+cnt+'" id="comp_yes'+cnt+'" value="1" tabindex="'+cnt+'" onClick="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); chkBoxSetting(\'comp_no'+cnt+'\');" >';
	html += '<label for="comp_yes'+cnt+'"></label>';
  html += '</div>';
	html += '</td>';
	
	// Compliant No
	html += '<td class="text-center" width="20">';
	html += '<div class="radio">';
	html += '<input type="radio" name="compliant'+cnt+'" id="comp_no'+cnt+'" value="0" tabindex="'+cnt+'" onClick="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); chkBoxSetting(\'comp_yes'+cnt+'\');" >';
	html += '<label for="comp_no'+cnt+'" ></label>';
	html += '</div>';
	html += '</td>';
	
	// Begin Date
	html += '<td class="BeginDateTime '+(($.inArray('Begin-Date-Time',selCols) === -1) ? 'hide' : '')+'">';
	html += '<div class="input-group">';
	html += '<input type="text" class="datepicker form-control" id="md_begindate'+cnt+'" tabindex="'+cnt+'" name="md_begindate'+cnt+'" onKeyUp="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); med_change_val();" onChange="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); med_change_val();"  value="" onKeyDown="indexEnt();" maxlength="10" title="'+top.inter_date_format+'" onBlur="checkdate(this); '+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); med_change_val();" >';
	html += '<label for="md_begindate'+cnt+'" class="input-group-addon btn">';
	html += '<i class="glyphicon glyphicon-calendar "></i>';
	html += '</label>';
	html += '</div>';
	html += '</td>';
	
	// Begin Time
	html += '<td class="BeginDateTime '+(($.inArray('Begin-Date-Time',selCols) === -1) ? 'hide' : '')+'" >';
	html += '<input type="text" class="form-control" name="md_begtime'+cnt+'" id="md_begtime'+cnt+'" value="" onChange="'+chk_change+' med_change_val(); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));">';
	html += '</td>';
	
	// End Date
	html += '<td class="EndDateTime '+(($.inArray('End-Date-Time',selCols) === -1) ? 'hide' : '')+'">';
	html += '<div class="input-group">';
	html += '<input type="text" class="datepicker form-control" id="md_enddate'+cnt+'" tabindex="'+cnt+'" name="md_enddate'+cnt+'" onKeyUp="'+chk_change+'insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" onChange="insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); '+chk_change+' med_change_val();" value="" maxlength="10" onKeyDown="indexEnt();"  title="'+top.inter_date_format+'" onBlur="checkdate(this); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); '+chk_change+' med_change_val();" >';
	html += '<label for="md_enddate'+cnt+'" class="input-group-addon btn">';
	html += '<i class="glyphicon glyphicon-calendar "></i>';
	html += '</label>';
	html += '</div>';
	html += '</td>';
	
	// End Time
	html += '<td class="EndDateTime '+(($.inArray('End-Date-Time',selCols) === -1) ? 'hide' : '')+'" >';
	html += '<input type="text" class="form-control" name="md_endtime'+cnt+'" id="md_endtime'+cnt+'" value="" onChange="'+chk_change+' med_change_val(); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));">';
	html += '</td>';
	
	//Last Taken Date
	if(type == 4)
	{
		html += '<td class="LastTakenDate '+(($.inArray('Last-Taken-Date',selCols) === -1) ? 'hide' : '')+'">';
		html += '<div class="input-group">';
		html += '<input type="text" class="datetimepicker form-control" id="md_lasttakendate'+cnt+'" tabindex="'+cnt+'" name="md_lasttakendate'+cnt+'" onKeyUp="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" onChange="insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); '+chk_change+' med_change_val();" value="" onKeyDown="indexEnt();"  title="'+top.inter_date_format+'" onBlur="checkdatetime(this); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); '+chk_change+' med_change_val();" >';
		html += '<label for="md_lasttakendate'+cnt+'" class="input-group-addon btn">';
	html += '<i class="glyphicon glyphicon-calendar "></i>';
	html += '</label>';
		html += '</div>';
		html += '</td>';
	}
	
	//Comments
	html += '<td>';
	html += '<textarea id="med_comments'+cnt+'" tabindex="'+cnt+'" name="med_comments'+cnt+'" onKeyUp="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" class="form-control" onKeyDown="indexEnt();" onChange="med_change_val();" rows="1" onMouseDown="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));"></textarea> 	';
	html += '</td>';
	
	// Ordered By
	html += '<td class="OrderedBy '+(($.inArray('Ordered-By',selCols) === -1) ? 'hide' : '')+'">';
	html += '<select class="form-control minimal" data-width="70px" data-size="10" name="md_prescribedby'+cnt+'" id="md_prescribedby'+cnt+'" tabindex="'+cnt+'" onChange="'+chk_change+' med_change_val(); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" title="Select" data-container="#select-container" >';
	html += phyOptions;
	html += '</select>';
	html += '</td>';
	
	// Status
	html += '<td>';
	html += '<select class="form-control minimal" style="width:75px;" data-width="75px" name="cbMedicationStatus'+cnt+'" id="cbMedicationStatus'+cnt+'" tabindex="'+cnt+'" onChange="'+chk_change+' med_change_val(); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));" data-container="#select-container">';
	html += '<option label="Active" value="Active" selected>Active</option>';
	html += '<option label="Order" value="Order" >Order</option>';
	html += '<option label="Stop" value="Stop">Stop</option>'
	html += '<option label="Renew" value="Renew">Renew</option>';
	html += '<option label="Discontinue" value="Discontinue">Discontinue</option>';
	html += '<option label="Administered" value="Administered">Administered</option>';
	html += '</select>';
	html += '</td>';
	
	//Rx Nome Code
	html += '<td>';
	html += '<input type="hidden" id="ccda_code_hidd'+cnt+'" name="ccda_code_hidd'+cnt+'" value="">';
	html += '<input type="text" id="ccda_code'+cnt+'" tabindex="'+cnt+'" name="ccda_code'+cnt+'" onChange="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); med_change_val();"  class="form-control" onKeyDown="indexEnt();" value="">';
	html += '<input type="hidden" id="fdb_id'+cnt+'" tabindex="'+cnt+'" name="fdb_id'+cnt+'" value="" onChange="'+chk_change+' insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\')); med_change_val();">';
	html += '</td>';
	
	//Vision Problem
	html += '<td class="text-center">';
	html += '<div class="checkbox">';
	html += '<input type="checkbox" id="ocular_med_chkbox_'+cnt+'" name="ocular_med_chkbox_'+cnt+'" title="Ocular" '+eye_checked+' value="1" onClick="chk_change('+(type == '4' ? true :false)+',this,event); insertMedIdVizChange('+(type == '4' ? true : false)+',this,event, document.getElementById(\'med_id'+cnt+'\'));">';
	html += '<label for="ocular_med_chkbox_'+cnt+'"></label>';
	html += '</div>';
	html += '</td>';
	
    if(isDssEnable){
        //DSS Service eligibility (Is this Medication is service connected eligibility?)
        html += '<td class="text-center Eligibility">';
        html += '<div class="checkbox">';
        html += '<input type="checkbox" class="" name="service_eligibility'+cnt+'" id="service_eligibility'+cnt+'" value="0" tabindex="'+cnt+'" onChange="'+chk_change+'dss_value_change(this);med_change_val(); insertMedIdVizChange(\'\',this,event,  document.getElementById(\'med_id'+cnt+'\'));">';
        html += '<label for="service_eligibility'+cnt+'"></label>';
        html += '</div>';
        html += '</td>';
    }
	//REFUSAL
	html += '<td class="text-center Refusal '+(($.inArray('Refusal',selCols) === -1) ? 'hide' : '')+'">';
	html += '<div class="checkbox">';
	html += '<input type="checkbox" class="checkbox" name="refusal'+cnt+'" id="refusal'+cnt+'" value="1" tabindex="'+cnt+'" onChange="check_refusal('+cnt+');">';
	html += '<label for="refusal'+cnt+'"></label>';
	html += '</div>';
	html += '<input type="hidden" name="refusal_reason'+cnt+'" id="refusal_reason'+cnt+'" value="" onChange="'+chk_change+' med_change_val(); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));">';
	html += '<input type="hidden" name="refusal_snomed'+cnt+'" id="refusal_snomed'+cnt+'" value="" onChange="'+chk_change+' med_change_val(); insertMedIdVizChange(\'\',this,event, document.getElementById(\'med_id'+cnt+'\'));">';
	html += '</td>';
	
	// History
	html += '<td class="Hx '+(($.inArray('Hx',selCols) === -1) ? 'hide' : '')+'">';
	html += '<img src="'+top.JS_WEB_ROOT_PATH+'/library/images/search.png" alt="Hx" class="pointer" data-toggle="tooltip" data-placement="left" data-title="" data-content="" data-html="true">';
	html += '</td>';
	
	//Delete
	html += '<td>';
	html += '<span id="add_row_'+cnt+'" class="glyphicon glyphicon-plus pointer" alt="Add More" onclick="addNewRow('+cnt+','+type+');"></span>';
	html += '</td>';
	
	html += '</tr>';
	
	
	if(type == "4")
		var obj = $("#medication_table_ocu").last().append(html);
	else if(type == "1")
		var obj = $("#medication_table_sys").last().append(html);
		
	bind_med_typeahead();
	$('#textTitle'+cnt).focus();
	
	document.getElementById("last_cnt").value = cnt;
	document.getElementById('cnt').value = cnt;
	
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
	$('.datetimepicker').datetimepicker({format:top.jquery_date_time_format,step:5,autoclose: true,scrollInput:false});
	$(".selectpicker").selectpicker();
}

function fn_ocu_site_chk(arg1)
{
	arrMed = new Array();
	$('#medication_table_ocu').find('[id^="textTitle"]').each(function() {
		if(this.value != '')
		{
			id = this.id; title = 'textTitle';
			index = id.substring(title.length,id.length);
			dosageID = 'md_occular'+index;
			ocluarChk = 'ocular_med_chkbox_'+index;
			len = document.getElementsByName(dosageID).length;
			flag = 0;
			for(i=0;i<len;i++){
				if(document.getElementsByName(dosageID)[i].checked == true){
					flag = 1
					break;
				}
				if(flag == 1)break;
			}
			if(flag == 0 && $('#'+ocluarChk).is(":checked")==true){
				if(this.value != '')
				arrMed[arrMed.length] = this.value;
			}
		}
	});
	
	if(arrMed.length>0)
	{
		var browser_ver = ( 'safari' == get_browser() ) ? true : false;
		if(browser_ver != true){			
			if(opener && typeof(opener)!="undefined" && opener.top && typeof(opener.top)!="undefined")
				opener.top.show_loading_image('hide');
			else if(typeof(top)!="undefined")
				top.show_loading_image('hide');
		}
		else{
			top.show_loading_image('hide');
		}
		
		msg = "Site for these medications not entered:-<br>";
		for(i = 0;i<arrMed.length;i++){
			msg += "<br>\t-"+arrMed[i];
		}
		if(msg != ''){
			top.fAlert(msg);
			return false;
		}
		return false;	
	}else{
		if(document.getElementById('subcallFrom').value == "grid")
		{
			top.show_loading_image('show');
		}
		
		if(callFrom == 'WV')
		{
			document.medications_form.submit();
		}
		
		
		return true;
	}
}

function med_exter_records(r)
{
	if(typeof r === 'undefined')
	{
		url = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/ajax/ajax_handler.php?action=med_external';
		top.master_ajax_tunnel(url,top.fmain.med_exter_records,'','json');
	}
	else
	{
		$("#med_external_data").html(r.data);
		if( !$("#med_external_modal").hasClass('in'))
		{
			$("#med_external_modal").modal('show');
		}
	}
	
}

function med_history_records(r,id)
{
	id = parseInt(id)
	
	if(id > 0 )
	{
		url = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/ajax/ajax_handler.php?action=med_history&id='+id;
		top.master_ajax_tunnel(url,top.fmain.med_history_records,'','json');
	}
	else if(typeof id === 'undefined' || !id)
	{
        $("#med_history_data").html(r.data);
        if(!r.data) {
            $("#med_history_data").html('<tr><td class="text-center bg bg-info">No Record Found</td></tr>');
        }
		if( !$("#med_history_modal").hasClass('in'))
		{
			$("#med_history_modal").modal('show');
		}
	}
	
	return false;
}

$(function(){
	
	$('[data-toggle="popover"]').popover(); 
	$('[data-toggle="tooltip"]').tooltip(); 
	/*$(".selectpicker_new").each(function(i,elem){
		var id = $(elem).attr('id');
		var val = $(elem).val();
		if(val == 'Other')
		{
			$('#div_'+id).addClass('hidden');
			$('#other_'+id).removeClass('hidden');
		}
		
	});*/
	
	$("#searchby").change(function(){
		url = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/index.php?showpage='+$("#curr_tab").val();
		if( $("#callFrom").val() == 'WV')
		{
			url += '&callFrom='+$("#callFrom").val()+'&divH='+$("#divH").val() 
		}
		$("#medications_form").attr('action',url).submit();
	});
	
	$('body').on('show.bs.modal','#med_history_modal',function(){
		var btn_arr = [];
		set_modal_btns('med_history_modal .modal_footer',btn_arr);
	});
	
	$("body").on('change', '.checkbox',function() {
		if(this.checked) {
			var row_id =   this.id;
				$("#myModal").modal('show');
				$("#rowID").val(row_id);	
			}
	});
	
});

function check_refusal(id){
	$("#refusal_row").val(id);
	$('#refusal_reason').val($("#refusal_reason"+id).val());
	$('#refusal_snomed').val($("#refusal_snomed"+id).val());
}
function check_refusal_values(){
	var ID = $("#refusal_row").val();
    
	var refusal_reason	= $('#refusal_reason').val();
	var refusal_snomed	= $('#refusal_snomed').val();
    if(!ID) {
        return false;
    }
	//if(refusal_snomed != "" && ID != ""){
		$("#refusal"+ID).val(1);
		$("#refusal_reason"+ID).val(refusal_reason); 
		$("#refusal_snomed"+ID).val(refusal_snomed); 
	//}
	$("#myModal").modal('hide');
}

//specify if DSS medication is service connected eligibility
if(isDssEnable){
    function dss_value_change(obj){
        if($(obj).is(':checked')==true){$(obj).val(1);$(obj).prop('checked', true);}
        else {$(obj).val(0);$(obj).prop('checked', false);}
    }
}
