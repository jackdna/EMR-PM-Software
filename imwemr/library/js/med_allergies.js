function onKeyDown(func1) {
	if(event.keyCode == 122) {
		event.keyCode = 0;	
		event.returnValue = false;
		if(func1) {
			eval(func1);
		}
	}
}

function statusOfAllInputs()
{
	var obj = $("#allergy_row_data").find('input,select,textarea');
	var img_obj = $('img[id^="add_row_"]');
	
	if( $('#commonNoAllergies').is(':checked')){
		obj.prop('disabled', true);
		img_obj.hide(10);
	}
	else{
		obj.prop('disabled', false);
		img_obj.show(10);
	}
	//$('.selectpicker').selectpicker('refresh');
}

var srchon=0;
function load_allergy_ta(flgAll){
	if(srchon!=0){ srchon=2; return;}
	
	var frm = "0";
	var tr = $('#myModal .modal-body .row tr[id*=allergy_row]:last');
	if(typeof(tr)!="undefined" && tr.length>0){ 
		var trid = tr[0].id; 
		trid = $.trim(trid.replace(/allergy_row_/, ''));
		if(typeof(trid)!="undefined" || trid == ""){
			frm = $.trim(""+trid);	
		}
	}
	
	var srch = $.trim($("#allergy_name").val());	
	if(typeof(srch)!="undefined" &&  srch != ""){ if(srch.length<3){return;} frm = "0";srch="&srch="+srch;   }else{srch="";}

	if(typeof(flgAll)!="undefined" &&  flgAll == "1"){frm = "All";srch="";$("#allergy_name").val("");}
	srchon=1;
	top.show_loading_image('show');
	$.get(top.JS_WEB_ROOT_PATH+'/interface/Medical_history/allergies/ajax/ajax_handler.php?get_allergies_modal=1&from='+frm+srch, function(d){
			top.show_loading_image('hide'); if(srchon==2){ srchon=0; load_allergy_ta();return;}else{ srchon=0; }
			if(d!=''){
				if(frm=="0" || frm == "All"){
					$('#myModal .modal-body .row').html(d); 
				}else{
					$('#allergies_name_tbl tr:last').after(d);	
				}
			}
		});
}

/** Add/Modify Allergies name **/
function addNewAllergie(event,textBox){//For mouse right click
	var input_id = $(textBox).attr('id');
	if (event.button==2){
		document.oncontextmenu = new Function("return false");
		$('#input_id_val').html(input_id);
		$('#myModal').modal('show');
		var data = $.trim($('#myModal .modal-body .row').html());
		if(data.indexOf('Loading! Please wait')!=-1||data==""){ load_allergy_ta();	}
		return false;
	}
}

function save_allergy_data(obj,save_allergy_data){
	var action = $(obj).val();
	if($('#allergy_name').val() == ''){
		top.fAlert("Please provide some text to proceed");
		$('#allergy_name').focus();
		return false;
	}else{
		var form_data = $('#'+save_allergy_data).serialize();
		$.ajax({
			url:top.JS_WEB_ROOT_PATH+'/interface/Medical_history/allergies/ajax/ajax_handler.php?save_data=yes',
			data:form_data,
			type:'POST',
			success:function(response){
				var result = $.parseJSON(response);
				if(result.counter == 0){
					top.fAlert(result.return_val);
				}else{
					top.alert_notification_show(result.return_val);
				}
				var allergy_name = result.allergy_name;
				var input_id = $('#input_id_val').html();
				$('#'+input_id).val(allergy_name);
				$('#myModal').modal('hide');
				set_typeahead('refresh');
				$('#myModal .modal-body .row').html("");
				modify_allergy_name('add');
			}
		});
	}
}

function modify_allergy_name(action,record_id,allergy_name_val){
	if(action == 'edit'){
		$('#allergy_id').val(record_id);
		var allergy_name = $.trim($('#'+allergy_name_val).html());
		$('#allergy_name').val(allergy_name);
		$('#save_btn').val('Update');
		$('#add_btn').removeClass('hide');
	}else if(action == 'add'){
		$('#allergy_id').val('');
		$('#allergy_name').val('');
		$('#save_btn').val('Save');
		$('#add_btn').addClass('hide');
		$('#allergy_name').focus();
	}else if(action == 'del'){
		var row_count = allergy_name_val.split('_');	
		$.ajax({
			url:top.JS_WEB_ROOT_PATH+'/interface/Medical_history/allergies/ajax/ajax_handler.php?allergy_modify=yes&del_id='+record_id,
			type:'GET',
			success:function(response){
				if($.trim(response) != '' && $.trim(response) > 0){
					$('#allergy_row_'+row_count[3]).remove();
					top.alert_notification_show('Record delete successfully');
					$("#allergy_name").val('');
					$('#myModal .modal-body .row').html("");
					load_allergy_ta();
				} 
			}
		});
	}
}

function setAllergyName(allergy_name){
	var input_id = $('#input_id_val').html();
	$('#'+input_id).val(allergy_name);
	close_allergy();
}

function set_typeahead(){
	//Setting allergies typeahead array
	$.ajax({
		url:top.JS_WEB_ROOT_PATH+'/interface/Medical_history/allergies/ajax/ajax_handler.php?get_xml_arr=yes',
		type:'GET',
		dataType:'JSON',
		success:function(response){
			var typeahead_arr = response;
			if(global_php_var.eRx_patient_id == ''){
				$('[id^=textTitleA]').each(function(id,elem){
					$(elem).typeahead('destroy');
					$(elem).typeahead({source:typeahead_arr,items:-1,scrollBar:true});
				});
			}
		}
	});
}

function deleteAllergy_remove_tr(id,mdName, obj,webroot, msg,row_count){
	$("#hidDelID").val(id);
	$("#hidDelMed").val(mdName);
	var conf;
	var data = 'med_id='+id+'&med_name='+mdName;
	if(id != ''){
		$.ajax({
			url:top.JS_WEB_ROOT_PATH+'/interface/Medical_history/allergies/ajax/ajax_handler.php?delete_allergy=yes',
			data:data,
			type:'POST',
			success:function(response){
				if($.trim(response) != '' && $.trim(response) > 0){
					top.alert_notification_show('Record deleted successfully');	
					$('#tblag_'+row_count).remove();
				}
			}	
		});
	}else{
		$('#tblag_'+row_count).remove();
	}
}

function addNewRow(cnt){
	
	var imgObj = $("#add_row_"+cnt);
	imgObj.attr('title','Delete Row');
	imgObj.attr('class','glyphicon glyphicon-remove');
	imgObj.attr('onclick','deleteAllergy_remove_tr(\'\',\'\',this,\'\',\'\',\''+cnt+'\')');
	imgObj.attr('id','');
	cnt++;
	
	if(global_php_var.callFrom != 'WV'){
		var chk_change = 'top.fmain.chk_change(\'\',this,event);';
	}
	cnt++;
	var td1 = "<td>";
			td1 += "<select name='ag_occular_drug"+cnt+"' id='ag_occular_drug"+cnt+"' class='form-control minimal dropup' data-dropup-auto='false' data-width='100%' onChange='"+chk_change+"'>";
				td1 += "<option value='fdbATDrugName'>Drug</option><option value='fdbATIngredient'>Ingredient</option>";
				td1 += "<option value='fdbATAllergenGroup'>Allergen</option>";
			td1 += "</select>";
		td1 += "</td>";
		
	var td2 = "<td>";
			td2 += "<input type='text' id='textTitleA"+cnt+"' tabindex='"+cnt+"' onChange='allergy_change_fun();' onKeyUp=\"search_erx_allergy(this.value, '"+cnt+"');"+chk_change+"\" name='ag_title"+cnt+"' value='' class='form-control' onMouseDown='addNewAllergie(event,this);'><input type='hidden' id='hiddenTitleA"+cnt+"' name='hiddenTitleA'"+cnt+"' value='' />";
		td2 += "</td>";
	
	var td3 = "<td>";
		td3 += "<div class='input-group'>";
			td3 += "<input type='text' id='ag_begindate"+cnt+"' tabindex='"+cnt+"' name='ag_begindate"+cnt+"' onKeyUp='"+chk_change+"' onChange=\"top.fmain.chk_change(\'\',this,event);checkdate(this);\" value='' class='datepicker allergy_bg_date form-control' onBlur=''>";
			td3 += "<label for='ag_begindate"+cnt+"' class='input-group-addon'>";
				td3 += "<span class='glyphicon glyphicon-calendar'></span>";
			td3 += "</label>";
		td3 += "</div>";
		td3 += "</td>";
	
	var td4 = "<td>";
		td4 += "<input type=\"hidden\" name=\"ag_reaction_code"+cnt+"\" id=\"ag_reaction_code"+cnt+"\" value=\"\" /><textarea class='form-control' id='ag_comments"+cnt+"' tabindex='"+cnt+"' rows='1' name='ag_comments"+cnt+"' onKeyUp='"+chk_change+"'></textarea>";
		td4 += "</td>";
	
	var tdN = "<td>";
			tdN += "<select name='ag_severity"+cnt+"' id='severity"+cnt+"' class='form-control minimal dropup' title='select' onChange='"+chk_change+"' data-dropup-auto='false' data-width='100%' >";
			tdN += "<option value=\"\">Select</option>";
			var tmp_counter = -1;
			for( var tmp in global_php_var.severityArr)
			{
				tmp_counter++;
				tdN += "<option value='"+tmp+"'>"+global_php_var.severityArr[tmp]['value']+"</option>";
			}
			tdN += "</select>";
		tdN += "</td>";
			
	var td5 = "<td>";
			td5 += "<select name='ag_status"+cnt+"' id='status"+cnt+"' class='form-control minimal dropup' onChange='"+chk_change+"' data-dropup-auto='false' data-width='100%' >";
				td5 += "<option value='Active' selected>Active</option>";
				td5 += "<option value='Suspended'>Suspended</option>";
				td5 += "<option value='Aborted'>Aborted</option>";
			td5 += "</select>";
		td5 += "</td>";
		
	var td6 = "<td>";
			td6 += "<input type='text' id='ccda_code"+cnt+"' tabindex='"+cnt+"' name='ccda_code"+cnt+"' onKeyUp='"+chk_change+"' onChange='"+chk_change+"' value='' class='allergy_bg_date form-control'>";
		td6 += "</td>";
	var td7 = "<td class='text-center'>";
			td7 += "<a href='#' title='Changes History' data-toggle='popover' data-trigger='focus' data-content='No History' data-html='true' data-placement='left'><img src='../../library/images/search.png' width='20px' height='auto'></a>";
		td7 += "</td>";
	var td8 = "<td class='text-center'>";
			td8 += "<span id='add_row_"+cnt+"' class='glyphicon glyphicon-plus pointer' alt='Add More' onClick='addNewRow("+cnt+");' ></span>";
		td8 += "</td>";
	
	var tr = "<tr id='tblag_"+cnt+"'>" + td1 + td2 + td3 + td4 + tdN+ td5 + td6 + td7 + td8 + "</tr>";
	$("#allergies_tb").last().append(tr);
	
	$('#ag_occular_drug'+cnt).focus();
	if(global_php_var.eRx_patient_id == ''){
		set_typeahead();
	}
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
	//$('.selectpicker').selectpicker('refresh');
	$("#last_cnt").val(cnt);
	$('[data-toggle="popover"]').popover();
}

function formSubmit(){
	document.getElementById("allergies_form").action = '';
	document.getElementById("allergies_form").submit();
}	

function entsub(val,type_number){
	if(window.event && window.event.keyCode == 13){
		getFacilityId(val,type_number);
	}
}

function indexEntCheck(){
	if(document.getElementById('tat_table')){
		indexEnt('check');
	}else{
		indexEnt();
	}
}	

function indexEnt(str_mode){
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

function insertAllergIdVizChange(olddata,obj,e, hidMedObj){
	e = e || event;
	//alert(obj.type);
	characterCode = e.keyCode;
	if(obj.type == "text" || obj.type == "textarea" || obj.type == "hidden"){
		var newData = obj.value;
		//alert(newData);
		if(characterCode != 9 && characterCode != 16 ){
			if(olddata != newData || olddata == ""){
				var strValue = document.getElementById("hidAllergyIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}					
				document.getElementById("hidAllergyIdVizChange").value = strValue;
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
				var strValue = document.getElementById("hidAllergyIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}
				document.getElementById("hidAllergyIdVizChange").value = strValue;
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
				var strValue = document.getElementById("hidAllergyIdVizChange").value;
				var intMedId = hidMedObj.value;
				if(strValue.search(intMedId) < 0){
					strValue = strValue + intMedId + ",";
				}
				document.getElementById("hidAllergyIdVizChange").value = strValue;
			}
		}
	}
	else if(obj.type == "select-one"){	
		//alert(obj.type);
		var strValue = document.getElementById("hidAllergyIdVizChange").value;
		var intMedId = hidMedObj.value;
		if(strValue.search(intMedId) < 0){
			strValue = strValue + intMedId + ",";
		}
		document.getElementById("hidAllergyIdVizChange").value = strValue;
	}
}

function allergy_change_fun(){
	$('#allergy_change').val('yes');
}

//btns ---
if(global_php_var.callFrom != 'WV'){
	top.btn_show("ALRG");
}

//Below functions are not working as eRx skipped as it is skipped

function getFacilityId(val,type_number){
	if(val){
		var name = 'fac_obj_id_'+type_number;
		var allergy_type = $("#ag_occular_drug"+type_number).val();	
		if(global_php_var.Allow_erx_medicare.toLowerCase() == 'yes' && $.trim(global_php_var.eRx_patient_id) != ''){
			//window.open('getAlergyId.php?allergyName='+val+'&fieldName='+name+'&allergy_type='+allergy_type+'&type_number='+type_number,'erx_allergy','scrollbars=1,width=700,height=500');
		}
	}
}

var call_pending = false;
function search_erx_allergy(str_val, record_no){
	var ecode = parseInt(event.keyCode);
	var keys_not = [9,13,16,17,18,19,20,27,33,34,35,36,37,38,39,40,45];
	if(str_val){
		if(str_val.length >= 3 && call_pending == false && $.inArray(ecode,keys_not) == '-1' ){
			if(global_php_var.Allow_erx_medicare.toLowerCase() == 'yes' && $.trim(global_php_var.eRx_patient_id) != ''){
				call_pending = true;
				var name = 'fac_obj_id_'+record_no; var record_id = 'textTitleA' + record_no;
				var allergy_type = $("#ag_occular_drug"+record_no).val();
				var this_offset =$("#"+record_id).offset();
				var this_top = this_offset.top;
				
				$.ajax({
					url: 'allergies/search_erx_allergy.php?allergyName='+str_val+'&fieldName='+name+'&allergy_type='+allergy_type+'&type_number='+record_no,
					dataType:'json',
					beforeSend:function(){ top.show_loading_image('show', 50, 'Please wait'); },
					complete:function(){ top.show_loading_image('hide'); },
					success: function(resp){
						call_pending = false;
						//$("#"+record_id).typeahead({source:resp,onSelect:function(item){console.log(item);}}).trigger('focus');
						var tmp = $("#"+record_id).typeahead();
						tmp.data('typeahead').source = resp;
						tmp.data('typeahead').lookup();
						tmp.data('typeahead').select = function(){
							var $selectedItem = this.$menu.find('.active');
							if($selectedItem.length) {
								var value = $selectedItem.attr('data-value');
                var text = this.$menu.find('.active a').text();
										
                this.$element.val(this.updater(text)).change();
								var inx = this.$element.attr('tabindex');
								$("#hiddenTitleA"+inx).val(value);
								if($("#ag_begindate"+inx).val() == ""){
									var today_date = $("#today_date").val();
									$("#ag_begindate"+inx).val(today_date);
								}
								
							}
            	return this.hide();
						};
					}
				});
			}
		}
	}
}

function filter_table_allergy(str,table)
{
	if($.trim(str)==""){$('#myModal .modal-body .row').html(''); }
	load_allergy_ta();
	
	/*
		
		str = str.replace(/ +/g, ' ').toLowerCase();
		var tbl = $("#"+table + " > tbody");
		var tr = tbl.find('tr');
		
		tr.show().filter(function() {
			var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
			return !~text.indexOf(str);
		}).hide();
		
	*/
}

$(function(){
	set_typeahead();
	//--- PATIENT NKA ---
	var disable = Boolean(global_php_var.disable); 
	if(disable === true){
		$("#commonNoAllergies").attr('disabled','disabled');
	}
	
	$("#btSaveAllergies_btn").click(function(){
		$(this).attr("disabled",true);
		$("#allergies_form").submit();
	});

	$("#myModal .modal-body").scroll(function() {
	    //if($(window).scrollTop() == $(document).height() - $(window).height()) {
		 if($("#myModal .modal-body").scrollTop() + $("#myModal .modal-body").height() + 15 >= $("#myModal .modal-body .row").height()){    
		   // ajax call get data from server and append to the div
			load_allergy_ta(); 
	    }
	});	

});

function get_rx_code(_this,index)
{
	index = index || 0;
	if( index )
	{ 
		var rx 	= $(_this).val();
		var rxc	=	$("#ag_reaction_code"+index);
		var rxv = rxc.val();
		var rxs = (event.type == 'focus' && rxv) ? false : true;
		if( rx && rxs ){
			
			$.ajax({ url : top.JS_WEB_ROOT_PATH + '/interface/Medical_history/allergies/ajax/ajax_handler.php',
							 type:'post', data:{CxC:'1',name:rx}, 
							 success: function(r){ rxc.val(r).trigger('change'); } });	
		}
	}
	return false;
	
}

function close_allergy(){
	$('#myModal .modal-body .row').html("");
	modify_allergy_name('add');
	$('#myModal').modal('hide');	
}
