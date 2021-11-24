

function statusOfAllInputs()
{
	var obj = $("#surgery_ocu_table,#surgery_sys_table,#surgery_implant_table").find('input,select,textarea');
	var img_obj = $('img[id^="add_row_"]');
	
	if( $('#no_sur_chk').is(':checked') )
	{
		obj.prop('disabled', true);
		$('#surgery_implant_filter').find('select').prop('disabled', true);
		img_obj.hide(10);
	}
	else
	{
		obj.prop('disabled', false);
		$('#surgery_implant_filter').find('select').prop('disabled', false);
		img_obj.show(10);
	}
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

function insertSxProIdVizChange(olddata,obj,e, hidMedObj)
{				
	e = e || event;				
	characterCode = e.keyCode;
	if(obj.type == "text" || obj.type == "textarea"){
		var newData = obj.value;
		
		if(characterCode != 9 && characterCode != 16 ){
			if(olddata != newData){
				var strValue = document.getElementById("hidSXProIdVizChange").value;
				var intMedId = (hidMedObj) ? hidMedObj.value : "";
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}					
				document.getElementById("hidSXProIdVizChange").value = strValue;
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
				var strValue = document.getElementById("hidSXProIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}
				document.getElementById("hidSXProIdVizChange").value = strValue;
                if(document.getElementById('change_data')) {
                    document.getElementById('change_data').value='yes';
                }
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
				var strValue = document.getElementById("hidSXProIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}
				document.getElementById("hidSXProIdVizChange").value = strValue;
				document.getElementById('change_value').value='yes';
			}
		}
	}
	else if(obj.type == "select-one"){	
		var strValue = document.getElementById("hidSXProIdVizChange").value;
		var intMedId = hidMedObj.value;
		if(strValue.search(intMedId) < 0){
			strValue = strValue + intMedId + ",";
		}
		document.getElementById("hidSXProIdVizChange").value = strValue;
	}
}

function bind_sx_typeahead()
{
	$("[id^='sx_title_text']").each(function(id,elem){
		$(elem).typeahead('destroy');
		$(elem).typeahead({
			source:sx_typeahead,
			items:-1,scrollBar:true,
		});
	});
	
	$("[id^='sg_comments']").each(function(id,elem){
		$(elem).typeahead('destroy');
		$(elem).typeahead({
			source:phrases_typeahead,
		});
	});
	
	
}

function removeTableRow(id,cnt)
{
	if(id == ''){
		$('#sx_title_text'+cnt).val('');
		$('#sx_tr'+cnt).fadeOut();
	}
	else
	{
		var scf = $("#subcallFrom").val(); 
		if(callFrom != 'WV')
		{
			top.fancyConfirm(vocabulary_sx.delete,"", "window.top.show_loading_image('show');window.top.fmain.document.location.href='"+top.JS_WEB_ROOT_PATH +"/interface/Medical_history/index.php?showpage=sx_procedures&mode=delete&del_id="+id+"'","return false");
		}
		else
		{
			if(confirm("Are you sure to want deleting this record!")){
					document.location.href = top.JS_WEB_ROOT_PATH+"/interface/Medical_history/index.php?showpage=sx_procedures&callFrom=WV&divH="+document.getElementById("divH").value+"&mode=delete&del_id="+id;
			}				
		}
	}
}

function addNewRow(cnt1,type)
{
	type = type || '';
	cnt = document.getElementById('last_cnt').value;
	var pre_cnt = cnt1;
	var imgObj = $("#add_row_"+cnt1);
	imgObj.attr('title','Delete Row');
	imgObj.attr('class','glyphicon glyphicon-remove');
	imgObj.attr('onclick','removeTableRow(\'\','+pre_cnt+');');
	imgObj.attr('id','');
	cnt++;
	
	var chk_change = (callFrom !== 'WV') ? "chk_change('',this,event);" : '';
	
	var html = '';
	html += '<tr id="sx_tr'+cnt+'" >';
	
	if(type == 'IMPLANT')
	{
		html += '<td>';
		html += '<input type="hidden" name="sg_id'+cnt+'" id="sg_id'+cnt+'" value="0" >';
		html += '<input type="hidden" name="sg_occular'+cnt+'" id="sg_occular'+cnt+'" value="'+(type == 'IMPLANT' ? '9' : '')+'" />';
		//html += '<input type="text" id="sx_title_text'+cnt+'" tabindex="'+cnt+'" name="sx_title_text'+cnt+'" value="" class="form-control" onclick="implantable_devices(this, '+cnt+');" />';
		html += '<input type="text" id="sx_title_text'+cnt+'" tabindex="'+cnt+'" name="sx_title_text'+cnt+'" onclick="implantable_devices(this, '+cnt+');" onKeyUp="'+chk_change+'  insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));" value="" class="form-control" onChange="changeSxVal(this);" />';
		html += '</td>';
	} else {
		html += '<td>';
		html += '<input type="hidden" name="sg_id'+cnt+'" id="sg_id'+cnt+'" value="0" >';
		html += '<input type="hidden" name="sg_occular'+cnt+'" id="sg_occular'+cnt+'" value="'+(type == 'OCU' ? '6' : '5')+'" />';
		html += '<input type="text" id="sx_title_text'+cnt+'" tabindex="'+cnt+'" name="sx_title_text'+cnt+'" onKeyUp="'+chk_change+'  insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));" value="" class="form-control" onChange="changeSxVal(this);" />';
		html += '</td>';
	}
	
	
	//Sites
	
	if(type == 'OCU')
	{
		html += '<td class="text-center">';
		html += '<div class="radio">';
		html += '<input type="radio" name="sx_site'+cnt+'" id="md_ou'+cnt+'" value="3" tabindex="'+cnt+'" onClick="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\')); chkBoxSetting(\'md_od'+cnt+'\', \'md_os'+cnt+'\')" >';
		html += '<label for="md_ou'+cnt+'"></label>';
		html += '</div>';
		html += '</td>';
		
		html += '<td class="text-center">';
		html += '<div class="radio">';
		html += '<input type="radio" name="sx_site'+cnt+'" id="md_od'+cnt+'" value="2" tabindex="'+cnt+'" onClick="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\')); chkBoxSetting(\'md_os'+cnt+'\', \'md_ou'+cnt+'\');" >';
		html += '<label for="md_od'+cnt+'"></label>';
		html += '</div>';
		html += '</td>';
		
		html += '<td class="text-center">';
		html += '<div class="radio">';
		html += '<input type="radio" name="sx_site'+cnt+'" id="md_os'+cnt+'" value="1" tabindex="'+cnt+'" onClick="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\')); chkBoxSetting(\'md_od'+cnt+'\', \'md_ou'+cnt+'\');" >';
		html += '<label for="md_os'+cnt+'"></label>';
		html += '</div>';
		html += '</td>';
		
	}
	
	// Date Of Procedure
	html += '<td>';
	html += '<div class="col-sm-8">';
	html += '<div class="input-group">';
	html += '<input type="text" tabindex="'+cnt+'" name="sg_begindate'+cnt+'" id="sg_begindate'+cnt+'" onKeyUp="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));changeSxVal();" onChange="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));" value="" maxlength="10" class="date-pick1 dt_surgery form-control" title="'+top.inter_date_format+'" onBlur="insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));check_sx_beg_date(this);">';
	html += '<label for="sg_begindate'+cnt+'" class="input-group-addon btn">';
	html += '<i class="glyphicon glyphicon-calendar "></i>';
	html += '</label>';
	html += '</div>';
	html += '</div>';
	html += '<div class="col-sm-4">';
	html += '<input type="text" tabindex="'+cnt+'" name="sg_begtime'+cnt+'" id="sg_begtime'+cnt+'" onKeyUp="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));" value="" class="form-control" onChange="changeSxVal();">';
	html += '</div>';
	html += '</td>';
	
	//Physician
	html += '<td>';
	html += '<input type="hidden" tabindex="'+cnt+'" name="referredby_id'+cnt+'" id="referredby_id'+cnt+'" onKeyUp="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));" value="" onChange="changeSxVal();">';
	html += '<input type="text" tabindex="'+cnt+'" name="sg_referredby'+cnt+'" id="sg_referredby'+cnt+'" onKeyUp="top.loadPhysicians(this,\'referredby_id'+cnt+'\');'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));" value="" class="form-control" onChange="changeSxVal();" onFocus="top.loadPhysicians(this,\'referredby_id'+cnt+'\');">';
	html += '</td>';
	
	if(type == 'IMPLANT')
	{
		html += '<td>';
		html += '<input type="text" tabindex="'+cnt+'" name="assign_auth'+cnt+'" id="assign_auth'+cnt+'" onKeyUp="insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));" value="" class="form-control" onChange="changeSxVal();">';
		html += '</td>';
	}
	
	//Comments
	html += '<td>';
	html += '<textarea class="form-control" tabindex="'+cnt+'" rows="1" id="sg_comments'+cnt+'" name="sg_comments'+cnt+'" onChange="changeSxVal();" onKeyUp="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));"></textarea>';
	html += '</td>';
	
	if(type == 'IMPLANT')
	{
		html += '<td>';
		html += '<select class="select minimal form-control" data-width="100%" name="surgery_type'+cnt+'" id="surgery_type'+cnt+'"  onclick="comment_detail(this, document.getElementById(\'sg_id'+cnt+'\'));" onChange="changeSxVal();'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));">';
		html += '<option value="">Select</option>';
		html += '<option value="active" >Active</option>';
		html += '<option value="inactive" >Inactive</option>';
		html += '</select>';
		html += '</td>';
	} else {
		// TYPE
		html += '<td>';
		html += '<select class="select minimal form-control" name="surgery_type'+cnt+'" id="surgery_type'+cnt+'"  onChange="changeSxVal();'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));">';
		html += '<option value="surgery" >Surgery</option>';
		html += '<option value="procedure" >Procedure</option>';
		html += '<option value="intervention" >Intervention</option>';
		html += '</select>';
		html += '</td>';
	}
	
	if(type == 'OCU' || type == 'SYS')
	{
		html += '<td>';
		html += '<select class="select minimal form-control" data-width="100%" name="procedure_status'+cnt+'" id="procedure_status'+cnt+'" onChange="changeSxVal();'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));">';
		html += '<option value="">Select</option>';
		html += '<option value="pending" >Pending</option>';
		html += '<option value="completed" >Completed</option>';
		html += '</select>';
		html += '</td>';
	}
	
	//SNOMED CR
	html += '<td>';
	html += '<input type="text" tabindex="'+cnt+'" name="ccda_code'+cnt+'" id="ccda_code'+cnt+'" onKeyUp="'+chk_change+' insertSxProIdVizChange(\'\',this,event, document.getElementById(\'sg_id'+cnt+'\'));" value="" class="form-control" onChange="changeSxVal();">';
	html += '</td>';
	
    if(type == 'OCU' || type == 'SYS') {
        //REFUSAL CR
        html += '<td class="text-center">';
        html += '<div class="checkbox">';
        html += '<input type="checkbox" class="checkbox" name="refusal'+cnt+'" id="refusal'+cnt+'" value="1" tabindex="'+cnt+'" onChange="check_refusal('+cnt+');">';
        html += '<label for="refusal'+cnt+'"></label>';
        html += '</div>';
        html += '</td>'
    }
	//Delete
	html += '<td align="center">';
	html += '<span id="add_row_'+cnt+'" class="glyphicon glyphicon-plus pointer" alt="Add More" onclick="addNewRow('+cnt+',\''+type+'\');"></span>';
	html += '</td>';
	
	html += '</tr>';
	
	if(type == "OCU")
		var obj = $("#surgery_ocu_table").last().append(html);
	else if(type == "SYS")
		var obj = $("#surgery_sys_table").last().append(html);
	else if(type == "IMPLANT")
		var obj = $("#surgery_implant_table").last().append(html);
		
	bind_sx_typeahead();
	$('#sx_title_text'+cnt).focus();
	
	document.getElementById("last_cnt").value = cnt;
	
	//$('.selectpicker').selectpicker('refresh');
	//$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
    date_picker1();
	
}

$("body").on('change', '.checkbox',function() {
    if(this.checked) {
		var row_id =   this.id;
		$("#myModal").modal('show');
		$("#rowID").val(row_id);	
	}
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
	if(refusal_snomed != "" ){
		$("#refusal"+ID).val(1);
		$("#refusal_reason"+ID).val(refusal_reason); 
		$("#refusal_snomed"+ID).val(refusal_snomed); 
	}
	$("#myModal").modal('hide');
}

function changeSxVal(ele)
{
	$('#change_value').val('yes');
}

function check_sx_beg_date(date_val_obj)
{
	var RegExpSla=/[^a-z]/g;
	var date_split_val='';
	var date_val=date_val_obj.value;
	if(date_val!=''){
		var format_check=0;
		date_split_val=date_val.split("-");
		if(date_split_val.length>0){
			if(date_split_val[0].length>4){
				format_check=1;
			}
		}
		if(!date_val.match(RegExpSla) || format_check==1){
			top.fAlert("Please enter a valid Date");	
			date_val_obj.value="";
		}
	}
}