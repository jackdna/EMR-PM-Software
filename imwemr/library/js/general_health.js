

var processing_btn = '<button class="btn btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Processing...</button>';
var approved_btn = '<button class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Successfully Approved</button>';
var declined_btn = '<button class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Successfully Declined</button>';

function unChkCorespondingCbk(meObj, corespondingObj, tdCbkUc, CbkUc, msterChkBox, msterChkBoxOwn)
{
	tdCbkUc = tdCbkUc || "";
	CbkUc = CbkUc || "";
	if(meObj.checked == true)
	{
		corespondingObj.checked = false;
		if(typeof(tdCbkUc) == "object" && typeof(CbkUc) == "object")
		{
			tdCbkUc.className= "hidden";
			CbkUc.checked = false;
		}
	}
	if(msterChkBox){
		msterChkBox.checked = false;
	}
	
	if(msterChkBoxOwn){
		if((msterChkBoxOwn.checked == true) && (meObj.checked == false)){
			msterChkBoxOwn.checked = false;
		}
	}
}

function TextBox_hide_by_checkbox(obj,tdCbkUc,CbkUc,state)
{
	if(obj.checked){
		tdCbkUc.className = "show";
		if(state == "checked"){
			CbkUc.checked = true;
		}
	}else{
		tdCbkUc.className = "hidden";
		CbkUc.checked = false;
	}
}

function disableItsFields()
{
	var arguments = disableItsFields.arguments;
	var meControl = arguments[0];
	if(document.getElementById(meControl).checked == true){
		for(var a=1 ; a < arguments.length ; a++){
			var controlName = arguments[a];
			if(document.getElementById(controlName)){
				var control = document.getElementById(controlName);			
				switch(control.type){
					case "checkbox":
						  control.checked = false;
						  break;
					case "text":
						  control.value = "";
						  break;				
				}		
			}		
		}
		
		//check if all negatives are checked
		var flgMstr = ($("div.reviwsyst :checked[id*=negChkBx]").length>=14) ? true : false;
		$("#cbkMasterROS").prop("checked", flgMstr);		
	}
	else{
		document.getElementById('cbkMasterROS').checked = false
		for(var a=1 ; a < arguments.length ; a++){
			var controlName = arguments[a];
			if(document.getElementById(controlName)){
				var control = document.getElementById(controlName);			
				switch(control.type){
					case "checkbox":						  
						control.disabled = false;
					break;
					case "text":						  
						control.disabled = false;
					break;				
				}		
			}		
		}
	}
}

function disableROSField(arr, op)
{
	if(op == "d"){
		for(var a=0 ; a < arr.length ; a++){
			var controlName = arr[a];
			if(document.getElementById(controlName)){
				var control = document.getElementById(controlName);			
				switch(control.type){
					case "checkbox":
						  control.checked = false;
						  break;
					case "text":
						  control.value = "";
						  break;				
				}		
			}		
		}
	}
	else if(op == "e"){
		for(var a=0 ; a < arr.length ; a++){
			var controlName = arr[a];
			if(document.getElementById(controlName)){
				var control = document.getElementById(controlName);			
				switch(control.type){
					case "checkbox":						  
						control.disabled = false;
					break;
					case "text":						  
						control.disabled = false;
					break;				
				}		
			}		
		}
	}
}

function disableAllROS(obj)
{
	var arrNegChkBxAllerImmBloLym = new Array('chkBxSeaAller','chkBxHayFever','txtBxAllerImmBloLym');
	var arrNegChkBxGenito = new Array('chkBxGenitalUlcers','chkBxDischarge','chkBxKidneyStones','chkBxBloodUrine','txtBxGenitourinary');
	var arrNegChkBxCardio = new Array('chkBxChestPain','chkBxShortBreath','chkBxIrregularRhythm','txtBxCardiovascular','chkBxCongHeartFailure','chkBxHgBldPrsr','chkBxLwBldPrsr','chkBxPcMkrDF');
	var arrNegChkBxHeadNeck = new Array('chkBxSinusProblems','chkBxPostNasalDrip','chkBxRunnyNose','chkBxDryMouth','chkBxHearingLoss','txtBxHeadNeckOther');
	var arrNegChkBxConInt = new Array('chkBxFever','chkBxWeightLoss','chkBxRash','chkBxFatigue','txtBxConstIntOther');
	var arrNegChkBxNeurPsycMuscu = new Array('chkBxHeadache','chkBxSeizures','chkBxNumbness','chkBxFaints','txtBxNeurPsycMuscu','chkBxMigraines','chkBxMltSclrs','chkBxStroke','chkBxNurAlzDis','chkBxNurParkDis','chkBxDimentia');
	var arrNegChkBxGastro = new Array('chkBxVomiting','chkBxUlcers','chkBxDiarrhea','chkBxBloodyStools','txtBxGastro','chkBxHepatitis','chkBxJaundice','chkBxConstipation');
	var arrNegChkBxResp = new Array('chkBxCough','chkBxBronchitis','chkBxShortnessBreath','chkBxAsthma','chkBxEmphysema','chkBxCOPD','chkBxTB','txtBxHeadRespiratory');
	var arrNegElementId = new Array('negChkBxAllerImmBloLym','negChkBxGenito','negChkBxCardio','negChkBxHeadNeck','negChkBxConInt','negChkBxNeurPsycMuscu','negChkBxGastro','negChkBxResp',
							'negChkBxIntgmntr', 'negChkBxPsychiatry', 'negChkBxBloodLymph', 'negChkBxMusculoskeletal','negChkBxEndocrine','negChkBxEye');
	var arrNegChkBxIntgmntr = new Array('chkBxRashes','chkBxWounds','chkBxBreastLumps','chkBxEczema','txtBxIntgmntrOther','chkBxDermatitis');
	var arrNegChkBxPsychiatry = new Array('chkBxDepression','chkBxAnxiety','chkBxParanoia','chkBxSleepPatterns','txtBxPsychiatry','chkBxMntlEmoFac', 'chkBxMemLoss');
	var arrNegChkBxBloodLymph = new Array('chkBxAnemia','chkBxBloodTransfusions','chkBxExcessiveBleeding','chkBxPurpura','txtBxBloodLymph','chkBxInfection');
	var arrNegChkBxMusculoskeletal = new Array('chkBxPain','chkBxJointAche','chkBxStiffness','chkBxSwelling','txtBxMusculoskeletal','chkBxParalysisFever');
	var arrNegChkBxEndocrine = new Array('chkBxMoodSwings','chkBxPolydipsia','txtBxEndocrineOther', 'chkBxHyperthyroidism','chkBxHypothyroidism');
	var arrNegChkBxEye = new Array('chkBxVisionLoss','chkBxEyepain','chkBxDoublevision','chkBxEyeHeadache','txtBxEyeOther');
	
	if(obj.checked == true){
		disableROSField(arrNegChkBxAllerImmBloLym, "d");
		disableROSField(arrNegChkBxGenito, "d");
		disableROSField(arrNegChkBxCardio, "d");
		disableROSField(arrNegChkBxHeadNeck, "d");
		disableROSField(arrNegChkBxConInt, "d");
		disableROSField(arrNegChkBxNeurPsycMuscu, "d");
		disableROSField(arrNegChkBxGastro, "d");
		disableROSField(arrNegChkBxResp, "d");		
		disableROSField(arrNegChkBxIntgmntr, "d");
		disableROSField(arrNegChkBxPsychiatry, "d");
		disableROSField(arrNegChkBxBloodLymph, "d");
		disableROSField(arrNegChkBxMusculoskeletal, "d");
		disableROSField(arrNegChkBxEndocrine, "d");
		disableROSField(arrNegChkBxEye, "d");
		
		for(var i in arrNegElementId){
			var objId = document.getElementById(arrNegElementId[i]);
			objId.checked = true;
		}
	}
	else if(obj.checked == false){
		disableROSField(arrNegChkBxAllerImmBloLym, "e");
		disableROSField(arrNegChkBxGenito, "e");
		disableROSField(arrNegChkBxCardio, "e");
		disableROSField(arrNegChkBxHeadNeck, "e");
		disableROSField(arrNegChkBxConInt, "e");
		disableROSField(arrNegChkBxNeurPsycMuscu, "e");
		disableROSField(arrNegChkBxGastro, "e");
		disableROSField(arrNegChkBxResp, "e");		
		disableROSField(arrNegChkBxIntgmntr, "e");
		disableROSField(arrNegChkBxPsychiatry, "e");
		disableROSField(arrNegChkBxBloodLymph, "e");
		disableROSField(arrNegChkBxMusculoskeletal, "e");
		disableROSField(arrNegChkBxEndocrine, "e");
		disableROSField(arrNegChkBxEye, "e");
		
		for(var i in arrNegElementId){
			var objId = document.getElementById(arrNegElementId[i]);
			objId.checked = false;
		}
	}
	
	//
	ros_highlighter(1);
}

function set_smoke_code(id){
		if(typeof(show_code_arr[id])=='undefined'){
			document.getElementById('smoking_code').value='';
		}else{
			document.getElementById('smoking_code').value=show_code_arr[id];
		}
}

function switchcontrolsMode(obj,mode){
	if(obj){
		otype = obj.type;	
		if(mode && obj){
			if(otype=='checkbox' || otype=='radio')
				obj.checked=false;
			else if(otype=='text' || otype=='textarea')
				obj.value='';
			else if(otype=='select-one' || otype=='select-multiple')
				obj.selectedIndex=0;
		}
		if(obj.otype != 'undefined'){
			obj.disabled = mode;
			if(otype=='select-one' || otype=='select-multiple')
				$("select").selectpicker('refresh');
			
		}
	}
}

function controlsMode(currentObj){
	switch(currentObj.name){
		case 'SmokingStatus':
			var arr_Controls = new Array("source_of_smoke", "imgBackSmokingSource", "source_of_smoke_other", "smoke_perday", "number_of_years_with_smoke","smoke_years_months", "offered_cessation_counseling", "txtDateOfferedCessationCounselling", "cessationCounselling");
			for(i=0; i<arr_Controls.length; i++){
				obj = dgi(arr_Controls[i]);					
				if(currentObj.value=='' || currentObj.value=='4 - Never smoked'){
					switchcontrolsMode(obj,true);
				}else{
					switchcontrolsMode(obj,false);
				}
			}
			break;
		case 'alcohal':
			var arr_Controls = new Array('source_of_alcohal_other', 'imgBackAlcohalSource', 'alcohal_quentity', 'alcohal_time', 'list_drugs', 'elem_otherSocial');
			for(i=0; i<arr_Controls.length; i++){
				obj = dgi(arr_Controls[i]);					
				if(currentObj.value==''){
					switchcontrolsMode(obj,true);
				}else{
					switchcontrolsMode(obj,false);
				}
			}
			break;
		case 'radio_family_smoke':
			var arr_Controls = new Array('smokers_in_relatives', 'smoke_description');
			for(i=0; i<arr_Controls.length; i++){
				obj = dgi(arr_Controls[i]);
				if(currentObj.value=='1' && dgi('family_smoke_yes').checked){
					switchcontrolsMode(obj,false);
				}else{
					switchcontrolsMode(obj,true);
				}
			}
			break;
		case 'offered_cessation_counseling':
			var arr_Controls = new Array("txtDateOfferedCessationCounselling", "cessationCounselling");
			for(i=0; i<arr_Controls.length; i++){
				obj = dgi(arr_Controls[i]);					
				if(currentObj.checked){
					switchcontrolsMode(obj,false);
				}else{
					switchcontrolsMode(obj,true);
				}
			}
			break;
	}
}
	
function check_ref_phy_name(){
	if(top.fmain.general_form.med_doctor){							
		if(top.fmain.general_form.med_doctor.value != ""){
			var refPhyNameNew = top.fmain.general_form.med_doctor.value;
			var arrPhyNameNew = refPhyNameNew.split(",");
			if(arrPhyNameNew.length < 2){									
				top.fAlert(vocabulary_gh.referring_physician);
				top.show_loading_image("hide");
				return false;
			}
		}
	}
	return true;
}
	
function check_smoking_status(){
	if(top.fmain.document.getElementById('patYearsOrlder').value == "yes" && top.fmain.general_form.SmokingStatus.value == ""){
		top.fAlert(vocabulary_gh.smoking_status,'','window.top.fmain.general_form.submit();');
		return false;
	}
	return true;
}

function chkAll(op, callObj, type){
	type = type || 'patient'
	var arrYesElement = new Array("selfHighBp", "selfHeart", "selfArthritis", "selfLung", "selfStroke", "selfThyroid", "elem_diab_u", "selfLDL", "selfUlcers", "selfCancer", "ghSelfOthers");
	var arrNoElement = new Array("selfHighBpN", "selfHeartN", "selfArthritisN", "selfLungN", "selfStrokeN", "selfThyroidN", "elem_diab_uN", "selfLDLN", "selfUlcersN", "selfCancerN", "ghSelfOthersN");
	
	if(type === 'family')
	{
		var arrYesElement = new Array("relHighBp", "relHeart", "relArthritis", "relLung", "relStroke", "relThyroid", "elem_diab_r", "relLDL", "relUlcers", "relCancer", "ghRelOthers");
		var arrNoElement = new Array("relHighBpN", "relHeartN", "relArthritisN", "relLungN", "relStrokeN", "relThyroidN", "elem_diab_rN", "relLDLN", "relUlcersN", "relCancerN", "ghRelOthersN");
	}
	if(callObj.checked == true){
		if(op == "yes"){
			for(var i in arrYesElement){
				var obj = document.getElementById(arrYesElement[i]);
				if(typeof(obj) == "object"){
					obj.checked = true;
				}
			}
			for(var i in arrNoElement){
				var obj = document.getElementById(arrNoElement[i]);
				if(typeof(obj) == "object"){
					obj.checked = false;
				}
			}
		}
		else if(op == "no"){
			for(var i in arrNoElement){
				var obj = document.getElementById(arrNoElement[i]);
				if(typeof(obj) == "object"){
					obj.checked = true;
				}
			}
			for(var i in arrYesElement){
				var obj = document.getElementById(arrYesElement[i]);
				if(typeof(obj) == "object"){
					obj.checked = false;
				}
			}
		}
	}
	else if(callObj.checked == false){
		if(op == "yes"){
			for(var i in arrYesElement){
				var obj = document.getElementById(arrYesElement[i]);
				if(typeof(obj) == "object"){
					obj.checked = false;
				}
			}
		}
		else if(op == "no"){
			for(var i in arrNoElement){
				var obj = document.getElementById(arrNoElement[i]);
				if(typeof(obj) == "object"){
					obj.checked = false;
				}
			}
		}
	}
}

// Add New Function for Blood Sugar & Cholesterol
var act_field, msg_date, msg_val, msg_new = '';
function save_new(action)
{
	if(action === '' || typeof action === 'undefined') return;
	
	if(action === 'bs_save')	{ 
		act_field = 'blood_sugar';
		var dt_field = 'this_'+act_field+'_date'; var val_field = 'this_'+act_field; 
		msg_val = vocabulary_gh.blood_sugar;
        msg_date = vocabulary_gh.blood_sugar_date; 
		msg_new = vocabulary_gh.new_entry_blood_sugar;
	}
	else if(action === 'c_save'){
		act_field = 'cholesterol';
		msg_val = vocabulary_gh.cholesterol;
        msg_date = vocabulary_gh.cholesterol_date; 
		msg_new = vocabulary_gh.new_entry_cholesterol;
		var dt_field = 'this_'+act_field+'_date'; var val_field = 'this_'+act_field+"_total"; 
	}
	
	if($("#"+dt_field).val() == ""){
		top.fAlert(msg_date,"",$("#"+dt_field,top.fmain.document));
		return false;
	}else if( $("#"+val_field).val() == "" ){
		top.fAlert(msg_val,"",$("#"+val_field,top.fmain.document));
		return false;
	}else{
		save_new_ajax('',action);
	}
}

function save_new_ajax(r,p)
{
	if( p )
	{
		var params = '';
		params += "action=gh_"+act_field+"_save";
		if(p == 'bs_save')
		{
			params += "&blood_sugar_date1="+$("#this_"+act_field+"_date").val();
			params += "&blood_sugar_mg1="+$("#this_"+act_field).val();
			params += "&blood_sugar_hba1c1="+$("#this_"+act_field+"_hba1c").val();
			params += "&blood_sugar_hba1c_val1="+$("#this_"+act_field+"_hba1c_val").val();
			params += "&blood_sugar_fasting1="+$("#this_"+act_field+"_fasting").val();
			params += "&blood_sugar_time_of_day1="+$("#this_"+act_field+"_time").val();
			params += "&blood_sugar_time_of_day_other1="+$("#this_"+act_field+"_other").val();
			params += "&blood_sugar_description1="+$("#this_"+act_field+"_desc").val();
			params += "&blood_sugar_rows=1";
		}
		else
		{
			params += "&cholesterol_date1="+$("#this_"+act_field+"_date").val();
			params += "&cholesterol_total1="+$("#this_"+act_field+"_total").val();
			params += "&cholesterol_triglycerides1="+$("#this_"+act_field+"_triglycerides").val();
			params += "&cholesterol_LDL1="+$("#this_"+act_field+"_LDL").val();
			params += "&cholesterol_HDL1="+$("#this_"+act_field+"_HDL").val();
			params += "&cholesterol_description1="+$("#this_"+act_field+"_desc").val();
			params += "&cholesterol_rows=1";
		}
		var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
		top.master_ajax_tunnel(url,top.fmain.save_new_ajax,params,'json');
		
	}
	else if( !p )
	{
		if(r.id1)	$("#this_"+act_field+"_id").val(r.id1);
		if(r.action == 'gh_blood_sugar_save')
		{
			load_bs_history();
			load_bs_graph();
		}
		else if( r.action == 'gh_cholesterol_save')
		{
			load_ch_history();
			load_ch_graph();
			load_ch_ldl_graph();
			load_ch_hdl_graph();
		}
		
	}
	return false;
}

// Save Blood Suagr
function save_blood_sugar (r)
{
	if(typeof r === 'undefined')
	{
        var count_rows=$('#blood_sugar_rows').val();
        for(var i=1;i<=count_rows;i++) {
            var dt_field = $('#blood_sugar_date'+i);
            var val_field = $('#blood_sugar_mg'+i);

            msg_val = vocabulary_gh.blood_sugar;
            msg_date = vocabulary_gh.blood_sugar_date;
        }

        if(dt_field.val()=="") {
            top.fAlert(msg_date);
            return false;
        }
				/*else if(val_field.val()=="") {
            fAlert(msg_val);
            return false;
        } */
				else {
            var formData  = 'action=gh_blood_sugar_save&';
            formData += $("#bs_add_body").find('input,select,textarea').serialize();
            var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
            top.master_ajax_tunnel(url,top.fmain.save_blood_sugar,formData,'json');
        }
	}
	else
	{
		$("#bs_add_body").find('input[type=text],textarea').val('')
		$("#bs_add_body").find('input[type=checkbox]').prop('checked',false)
		$("#bs_add_body").find('select').selectpicker('val','');
		$("[id^=div_blood_sugar_time_of_day]").removeClass('hidden');
		$("[id^=other_blood_sugar_time_of_day]").addClass('hidden');
		
		load_bs_history();
		load_bs_graph();
	}
	
	
}

// Load Blood Sugar History
function load_bs_history(r)
{
	if( typeof r === 'undefined' && typeof data !== 'object' )
	{
		var params  = 'action=blood_sugar_hx';
		var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
		top.master_ajax_tunnel(url,top.fmain.load_bs_history,params,'json');
	}
	else
	{
		//if(typeof data !== 'object')
		var data = r.bs_history;
		
		var html = '';
		$("#bs_history_header").css('padding-right',(data.length > 4 ? 12 : 0 ) + 'px');
		if(data.length > 0 && data !== 'No Records found')
		{
			for(var i in data)
			{
                var hba1c = '';
                if(data[i].hba1c != '' && data[i].hba1c_val != '') {
                    hba1c = data[i].hba1c_val+ ' - ' +data[i].hba1c;
                } else if(data[i].hba1c == '' && data[i].hba1c_val != '') {
                    hba1c = data[i].hba1c_val;
                } else if(data[i].hba1c != '' && data[i].hba1c_val == '') {
                    hba1c = data[i].hba1c;
                }

				html += '<tr id="bs_row_'+data[i].id+'">';
				html += '<td class="col-xs-2">'+data[i].date+'</td>';
				html += '<td class="col-xs-1">'+data[i].sugar_value+'</td>';
				html += '<td class="col-xs-2">'+hba1c+'</td>';
				html += '<td class="col-xs-1">'+data[i].is_fasting+'</td>';
				html += '<td class="col-xs-2">'+data[i].time_of_day+'</td>';
				html += '<td class="col-xs-3">'+data[i].description+'</td>';
				html += '<td class="col-xs-1"><img src="'+top.JS_WEB_ROOT_PATH+'/library/images/close_small.png" class="pointer" onclick="delete_blood_sugar('+data[i].id+', \'\','+data[i].id+');"></td>';
				html += '</tr>';
				
			}
		}
		else
		{
			html += '<tr><td class="alert alert-info" colspan="7">'+data+'</td></tr>';	
		}
		
		$("#bs_history_body").html(html);
		
	}
	
}

//Load Blood Sugar Graph
function load_bs_graph(r)
{
	if( typeof r === 'undefined' && typeof data !== 'object' )
	{
		var params  = 'action=blood_sugar_graph';
		var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
		top.master_ajax_tunnel(url,top.fmain.load_bs_graph,params,'json');
	}
	else
	{
		//if(typeof data !== 'object')
		var data = r.bs_graph;
		line_chart('Blood Sugar',data,'blood_sugar_graph','date','blood_sugar','0');
	}
}

// Add Blood Sugar Row
function add_blood_sugar_row()
{
	var t_obj = $("#blood_sugar_rows");
	var current_row = parseInt(t_obj.val());
	var next_row_id = current_row+1;
	if(next_row_id > 3){
		$("#bs_hx_header").css({'padding-right':'12px'});
	}
	var next_row_html = bs_row_html(next_row_id);
	$("#bs_add_body").append(next_row_html);
	var del_img = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_blood_sugar(\'\', \'\','+current_row+');"></span>';
	$("tr#bs_add_row_"+current_row+" td:last").html(del_img);
	t_obj.val(next_row_id);
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
	$(".selectpicker").selectpicker();
}

// Blood Sugar Row HTML
function bs_row_html(row_id)
{
	var html = '';
	html += '<tr id="bs_add_row_{index}">';
	//Date Column
	html += '<td class="col-xs-2">';
	html += '<div class="input-group">';
	html += '<input type="text" name="blood_sugar_date{index}" id="blood_sugar_date{index}" onKeyUp="chk_change(\'\',this,event);" onChange="chk_change(\'\',this,event);" value="" onBlur="checkdate(this);" title="'+top.jquery_date_format+'" class="datepicker form-control" onClick="getDate_and_setToField(this)" />';
	html += '<label class="input-group-addon pointer" for="blood_sugar_date{index}"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
	html += '</div>';
	html += '</td>';
	
	//Blood Sugar Column
	html += '<td class="col-xs-2">';
	html += '<input type="text" name="blood_sugar_mg{index}" id="blood_sugar_mg{index}" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control" />';
	html += '</td>';
	
	//HBA1C Column
	html += '<td class="col-xs-1">';
	html += '<input type="text" name="blood_sugar_hba1c_val{index}" id="blood_sugar_hba1c_val{index}" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control" />';
	html += '</td>';
    
	//HBA1C Column
	html += '<td class="col-xs-1">';
	html += '<select class="selectpicker" data-width="100%" data-container="#bs_selectContainer" data-title="Select" name="blood_sugar_hba1c{index}" id="blood_sugar_hba1c{index}" onChange="top.fmain.chk_change(\'\',this,event);" >';
	html += blood_sugar_opt_str;
	html += '</select>';
	html += '</td>';
	
	
	//IS Fasting Column
	html += '<td class="col-xs-1">';
	html += '<div class="checkbox">';
	html += '<input type="checkbox" name="blood_sugar_fasting{index}" id="blood_sugar_fasting{index}" onClick="chk_change(\'\',this,event);" value="1"/>';
	html += '<label for="blood_sugar_fasting{index}"></label>';
	html += '</div>';
	html += '</td>';
	
	//Time of Day Column
	html += '<td class="col-xs-2">';
	html += '<div id="div_blood_sugar_time_of_day{index}">';
	html += '<select name="blood_sugar_time_of_day{index}" id="blood_sugar_time_of_day{index}" class="selectpicker" onChange="javascript:show_hide(\'other_blood_sugar_time_of_day{index}\',\'div_blood_sugar_time_of_day{index}\',this); chk_change(\'\',this,event);" title="Please Select" data-width="100%" data-container="#selectContainer" data-size="6">';
	html += '<option value="0-" >Please Select</option>';
	html += '<option value="1-Morning" >Morning</option>';
	html += '<option value="2-Post Breakfast" >Post Breakfast</option>';
	html += '<option value="3-Afternoon" >Afternoon</option>';
	html += '<option value="4-Post Lunch" >Post Lunch</option>';
	html += '<option value="5-Evening" >Evening</option>';
	html += '<option value="6-Night" >Night</option>';
	html += '<option value="7-Post Dinner" >Post Dinner</option>';
	html += '<option value="8-Other" >Other</option>';
	html += '</select>';
	html += '</div>';
	
	html += '<div id="other_blood_sugar_time_of_day{index}" class="hidden">';
	html += '<div class="input-group">';
	html += '<input type="text" class="form-control" id="blood_sugar_time_of_day_other{index}" name="blood_sugar_time_of_day_other{index}" onKeyUp="chk_change(\'\',this,event);" value="" />';
	html += '<label class="input-group-addon btn back_other" data-tab-name="blood_sugar_time_of_day{index}"><i class="glyphicon glyphicon-arrow-left"></i></label>';
	html += '</div>';
	html += '</div>';
	html += '</td>';
	
	//Description Column
	html += '<td class="col-xs-3">';
	html += '<textarea name="blood_sugar_description{index}" id="blood_sugar_description{index}" rows="1" cols="10" onKeyUp="chk_change(\'\',this,event);" onFocus="getDate_and_setToField($(\'#blood_sugar_date{index}\'));" class="form-control"></textarea>';
	html += '</td>';
	
	html += '<td class="col-xs-1"></td>';
	html += '</tr>';
	
	row_id = parseInt(row_id)
	html =	html.replace(/{index}/g,row_id);
	
	return html;
}

// Delete Blood Sugar Row
function delete_blood_sugar(primary_key_id,str_mode,row_id)
{
	primary_key_id = (typeof primary_key_id == 'undefined') ? '' : primary_key_id;
	row_id = (typeof row_id == 'undefined') ? '' : row_id;
	
	if(primary_key_id == "" && row_id == '' ){
		top.fAlert(vocabulary_gh.nothing_to_delete_bs);
		return false;
	}
	if(primary_key_id)
		top.fancyConfirm(vocabulary_gh.delete_blood_sugar,"","window.top.fmain.delete_blood_sugar_ajax('"+primary_key_id+"', '"+str_mode+"')");
	else if(row_id){
		$("#bs_add_row_"+row_id).remove();
		$("#bs_hx_header").css('padding-right',(($("tr[id^='bs_add_row']").length > 3) ? 12 : 0) + 'px')
	}
}

// Delete Blood Sugar Row AJAX 
function delete_blood_sugar_ajax(primary_key_id, str_mode)
{
	var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php?action=delete_blood_sugar&primary_key_id="+primary_key_id;
	$.ajax({
		url : url,
		type:'POST',
		data : {},
		dataType:"json",
		beforeSend: function(){
			top.show_loading_image("show", 100);		
		},
		success: function(r){
			if(r.del_status)
			{
				if(str_mode == "from_main_page" || (str_mode !== "from_main_page" && primary_key_id == $("#this_blood_sugar_id").val()  ) )
				{
					$("#this_blood_sugar_date, #this_blood_sugar, #this_blood_sugar_hba1c, #this_blood_sugar_other, #this_blood_sugar_desc, #this_blood_sugar_id").val('');
					$("#this_blood_sugar_fasting").prop('checked',false);
					$("#this_blood_sugar_time").selectpicker('val','');
					$("#other_this_blood_sugar_time").addClass('hidden');
					$("#div_this_blood_sugar_time").removeClass('hidden');
				}
				
				$("#bs_row_"+primary_key_id).remove();
				$("#bs_history_header").css('padding-right',(($("tr[id^='bs_row']").length > 4) ? 12 : 0) + 'px')
				top.fAlert('Record deleted successfully');
			}
			
		},
		complete: function(){
			top.show_loading_image("hide");		
		}
		
	});
	
}

// Save Cholestrol
function save_cholesterol (r)
{
	if(typeof r === 'undefined')
	{
        var count_rows=$('#blood_sugar_rows').val();
        for(var i=1;i<=count_rows;i++) {
            var dt_field = $('#cholesterol_date'+i);
            var val_field = $('#cholesterol_total'+i);

            msg_val = vocabulary_gh.cholesterol;
            msg_date = vocabulary_gh.cholesterol_date; 
        }

        if(dt_field.val()=="") {
            top.fAlert(msg_date);
            return false;
        }else if(val_field.val()=="") {
            top.fAlert(msg_val);
            return false;
        } else {
            var formData  = 'action=gh_cholesterol_save&';
            formData += $("#ch_add_body").find('input,textarea').serialize();
            var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
            top.master_ajax_tunnel(url,top.fmain.save_cholesterol,formData,'json');
        }
		
	}
	else
	{
		$("#ch_add_body").find('input[type=text],textarea').val('')
		load_ch_history();
		load_ch_graph();
		load_ch_ldl_graph();
		load_ch_hdl_graph();
	}
	
	
}

// Load Cholesterol
function load_ch_history(r)
{
	if( typeof r === 'undefined' && typeof data !== 'object' )
	{
		var params  = 'action=cholesterol_hx';
		var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
		top.master_ajax_tunnel(url,top.fmain.load_ch_history,params,'json');
	}
	else
	{
		//if(typeof data !== 'object')
		var data = r.ch_history;
		var html = '';
		$("#ch_history_header").css('padding-right',(data.length > 4 ? 12 : 0 ) + 'px');
		if(data.length > 0  && data !== 'No Records found')
		{
			for(var i in data)
			{
				html += '<tr id="ch_row_'+data[i].id+'">';
				html += '<td class="col-xs-2">'+data[i].date+'</td>';
				html += '<td class="col-xs-1">'+data[i].cholesterol_total+'</td>';
				html += '<td class="col-xs-1">'+data[i].cholesterol_triglycerides+'</td>';
				html += '<td class="col-xs-1">'+data[i].cholesterol_LDL+'</td>';
				html += '<td class="col-xs-1">'+data[i].cholesterol_HDL+'</td>';
				html += '<td class="col-xs-5">'+data[i].description+'</td>';
				html += '<td class="col-xs-1"><img src="'+top.JS_WEB_ROOT_PATH+'/library/images/close_small.png" class="pointer" onclick="delete_cholesterol('+data[i].id+', \'\','+data[i].id+');"></td>';
				html += '</tr>';
				
			}
		}
		else
		{
			html += '<tr><td class="alert alert-info" colspan="7">'+data+'</td></tr>';	
		}
		
		$("#ch_history_body").html(html);
		
	}
	
}

//Load Cholesterol Graph
function load_ch_graph(r)
{
	if( typeof r === 'undefined' && typeof data !== 'object' )
	{
		var params  = 'action=cholesterol_graph';
		var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
		top.master_ajax_tunnel(url,top.fmain.load_ch_graph,params,'json');
	}
	else
	{
		//if(typeof data !== 'object')
		var data = r.ch_graph;
		line_chart('Cholesterol',data,'cholesterol_graph','date','cholesterol','0');
	}
}

//Load Cholesterol LDL Graph
function load_ch_ldl_graph(r)
{
	if( typeof r === 'undefined' && typeof data !== 'object' )
	{
		var params  = 'action=cholesterol_ldl_graph';
		var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
		top.master_ajax_tunnel(url,top.fmain.load_ch_ldl_graph,params,'json');
	}
	else
	{
		//if(typeof data !== 'object')
		var data = r.ch_ldl_graph;
		line_chart('Cholesterol LDL',data,'cholesterol_ldl_graph','date','cholesterol_ldl','0');
	}
}

//Load Cholesterol HDL Graph
function load_ch_hdl_graph(r)
{
	if( typeof r === 'undefined' && typeof data !== 'object' )
	{
		var params  = 'action=cholesterol_hdl_graph';
		var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php";
		top.master_ajax_tunnel(url,top.fmain.load_ch_hdl_graph,params,'json');
	}
	else
	{
		//if(typeof data !== 'object')
		var data = r.ch_hdl_graph;
		line_chart('Cholesterol HDL',data,'cholesterol_hdl_graph','date','cholesterol_hdl','0');
	}
}

// Add Cholesterol Row
function add_cholesterol_row()
{
	var t_obj = $("#cholesterol_rows");
	var current_row = parseInt(t_obj.val());
	var next_row_id = current_row + 1;
	if(next_row_id > 3){
		$("#ch_hx_header").css({'padding-right':'12px'});
	}
	var next_row_html = ch_row_html(next_row_id);
	$("#ch_add_body").append(next_row_html);
	var del_img = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_cholesterol(\'\', \'\','+current_row+');"></span>';
	$("tr#ch_add_row_"+current_row+" td:last").html(del_img);
	t_obj.val(next_row_id);
	$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
	
}

// Cholesterol Row HTML
function ch_row_html(row_id)
{
	var html = '';
	html += '<tr id="ch_add_row_{index}">';
	//Date Column
	html += '<td class="col-xs-2">';
	html += '<div class="input-group">';
	html += '<input type="text" class="form-control datepicker" name="cholesterol_date{index}" id="cholesterol_date{index}" title="mm-dd-yy" onBlur="checkdate(this);" onClick="getDate_and_setToField(this)" onKeyUp="chk_change(\'\',this,event);" onChange="chk_change(\'\',this,event);" value="" />';
	html += '<label class="input-group-addon pointer" for="cholesterol_date{index}"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
	html += '</div>';
	html += '</td>';
	
	//Cholesterol Total Column
	html += '<td class="col-xs-1">';
	html += '<input type="text" name="cholesterol_total{index}" id="cholesterol_total{index}" class="form-control" onKeyUp="chk_change(\'\',this,event);" value="" />';
	html += '</td>';
	
	//Cholesterol Triglycerides
	html += '<td class="col-xs-1">';
	html += '<input type="text" class="form-control" name="cholesterol_triglycerides{index}" id="cholesterol_triglycerides{index}" onKeyUp="chk_change(\'\',this,event);" value="" />';
	html += '</td>';
	
	
	//Cholesterol LDL
	html += '<td class="col-xs-1">';
	html += '<input type="text" name="cholesterol_LDL{index}" class="form-control" id="cholesterol_LDL{index}" onKeyUp="chk_change(\'\',this,event);" value="" />';
	html += '</td>';
	
	//Cholesterol HDL
	html += '<td class="col-xs-1">';
	html += '<input type="text" name="cholesterol_HDL{index}" class="form-control" id="cholesterol_HDL{index}" onKeyUp="chk_change(\'\',this,event);" value="" />';
	html += '</td>';
	
	//Cholesterol Description
	html += '<td class="col-xs-5">';
	html += '<textarea rows="1" cols="10" name="cholesterol_description{index}" id="cholesterol_description{index}" onKeyUp="chk_change(\'\',this,event);" onFocus="getDate_and_setToField($(\'#cholesterol_date{index}\'));" class="form-control" ></textarea>';
	html += '</td>';
	
	//Cholesterol Description
	html += '<td class="col-xs-1">&nbsp;</td>';
	
	html += '</tr>';
	
	row_id = parseInt(row_id);
	html =	html.replace(/{index}/g,row_id);
	
	return html;
}

// Delete Cholesterol Row
function delete_cholesterol(primary_key_id,str_mode, row_id)
{
	primary_key_id = (typeof primary_key_id == 'undefined') ? '' : primary_key_id;
	row_id = (typeof row_id == 'undefined') ? '' : row_id;
	
	if(primary_key_id == "" && row_id == ''){
		top.fAlert(vocabulary_gh.nothing_to_del_cholesterol);
		return false;
	}
	if(primary_key_id)
		top.fancyConfirm(vocabulary_gh.delete_cholesterol,"","window.top.fmain.delete_cholesterol_ajax('"+primary_key_id+"', '"+str_mode+"')");
	else if(row_id){
		$("#ch_add_row_"+row_id).remove();
		$("#ch_hx_header").css('padding-right',(($("tr[id^='ch_add_row']").length > 3) ? 12 : 0) + 'px');
	}
		
		
}

// Delete Cholesterol Row AJAX
function delete_cholesterol_ajax(primary_key_id, str_mode)
{
	var url = top.JS_WEB_ROOT_PATH + "/interface/Medical_history/ajax/ajax_handler.php?action=delete_cholesterol&primary_key_id="+primary_key_id;
	$.ajax({
		url : url,
		type:'POST',
		data : {},
		dataType:"json",
		beforeSend: function(){
			top.show_loading_image("show", 100);		
		},
		success: function(r){
			if(r.del_status)
			{
				if(str_mode == "from_main_page" || ( str_mode !== "from_main_page" && primary_key_id == $("#this_cholesterol_id").val() ))
				{
					$("#this_cholesterol_date, #this_cholesterol_total, #this_cholesterol_triglycerides, #this_cholesterol_LDL, #this_cholesterol_HDL, #this_cholesterol_desc,#this_cholesterol_id").val('');
				}
				$("#ch_row_"+primary_key_id).remove();
				$("#ch_history_header").css('padding-right',(($("tr[id^='ch_row']").length > 4) ? 12 : 0) + 'px')
				top.fAlert('Record deleted successfully');
			}
			
		},
		complete: function(){
			top.show_loading_image("hide");		
		}
		
	});
	
}

function chkCorrespondingChks(meObj,elemName,hideAllElems)
{			
	var resultElems=document.getElementsByName(elemName);
	
	if(hideAllElems==true)
	{
		for(i=0;i<resultElems.length;i++)
		{
				resultElems[i].parentNode.parentNode.className="hidden";
		}
		return false;
	}
	
	if(meObj.checked==true)
	{
		for(i=0;i<resultElems.length;i++)
		{
				resultElems[i].parentNode.parentNode.className="visible";
		}
	}
	else
	{
		for(i=0;i<resultElems.length;i++)
		{
				resultElems[i].parentNode.parentNode.className="hidden";
		}
	}
}

function unChkNegBox(objId1, objId2){
	document.getElementById(objId1).checked = false;
	document.getElementById(objId2).checked = false;
}

function showAD(_this)
{
	var s = $(_this).data('path');
	var i = '<img src="'+s+'" title="Advanced Directive" />';
	$("#ad_modal").find('.modal-body').html(i);
	$("#ad_modal").modal('show');	
}

function showpdf(id,pdf,image_form){
	if( (typeof id != "undefined") && (id != "") ){
		
		var w = 950 ;
		var h = 700 ;
		var l = parseInt((screen.availWidth - w ) / 2);
		var t = parseInt((screen.availHeight - h ) / 2);
	
		pdf = pdf || '';
		image_form = image_form || '';
		var n = "scan_"+id;
		var url = top.JS_WEB_ROOT_PATH + "/interface/chart_notes/logoImg.php?from=scanImage&scan_id="+id+"&headery="+pdf+"&image_form="+image_form;
		var v = window.open(url,"","width="+w+",height="+h+",resizable=1,scrollbars=1,top="+t+",left="+l+"");				
		v.focus;
	}
}

//GENERAL HEALTH AND VITAL SIGN SYNCED WORK ASSOCIATED FUNCTIONS
var old_unit7val = '';
function isNumeric(id){
	var isNotnumber = $('#'+id).val();
	  if(isNaN(isNotnumber)){ 
		  top.fAlert('Please enter the numeric value only');
		  $('#'+id).val('');
	  }	
	}
	
	function calculateBmi(id){ 
	var heightInFeet	= parseInt($("#bmi_height").val());
	var height_unit		= parseInt($("#bmi_height_unit").val());
	
	if(heightInFeet){
	   heightInFeet = parseInt(heightInFeet*12);
	}
	var height = (heightInFeet + height_unit);
	
	  var weight 			= $("#bmi_weight").val();
	  var weight_unit		=  $("#bmi_weight_unit").val();
	if(weight_unit!='lbs'){ 
		  weight			= convertUnit(weight,weight_unit,'lbs');
	  }
	  if(weight > 0 && height > 0){	
		  var finalBmi = weight/(height/100*height/100);
		  finalBmi = cal_bmi(weight, height);
	 	  $("#bmi_result").val(finalBmi);
	  }	
	}
	
	function convertUnit(val,fromUnit,ToUnit){
	  converted_value = ''; 
	  if(val!=''){ 
		  if(fromUnit=='lbs' && ToUnit=='kg'){
			  converted_value = (val * 0.4536);
		  }else if(fromUnit=='kg' && ToUnit=='lbs'){
			  converted_value = (val * 2.2046);			
		  }else if(fromUnit=='cm' && ToUnit=='m'){
			  converted_value = (val/100);			
		  }else if(fromUnit=='cm' && ToUnit=='inch'){
			  converted_value = (val * 0.3937);			
		  }else if(fromUnit=='m' && ToUnit=='cm'){
			  converted_value = (val * 100);			
		  }else if(fromUnit=='m' && ToUnit=='inch'){
			  converted_value = (val * 39.37);			
		  }else if(fromUnit=='inch' && ToUnit=='cm'){
			  converted_value = (val/0.3937);			
		  }else if(fromUnit=='inch' && ToUnit=='m'){
			  converted_value = (val/39.37);			
		  }
	  }
		return converted_value;	
	}
	
	function convert_height_weight(id,new_unit){ 
	
		calculateBmi(id);
		old_unit7val = new_unit;
	}
	
	
	function cal_bmi(lbs, ins)
	{
	   h2 = ins * ins;
	   bmi = lbs/h2 * 703
	   f_bmi = Math.floor(bmi);
	   diff  = bmi - f_bmi;
	   diff = diff * 10;
	   diff = Math.round(diff);
	
	
	
	   if (diff == 10)    // Need to bump up the whole thing instead
	   {
		  f_bmi += 1;
		  diff   = 0;
	   }
	   bmi = f_bmi + "." + diff;
	   return bmi;
	}
	
	function ros_highlighter(flg){
		if($(".reviwsyst").length<=0){return;}	
		
		var nmdn=0, txtntdn="";
		$(".reviwsyst .revsubbox").each(function(){
			if($(this).find(":checked").length==0 && $.trim($(this).find("input[type='text']").val())==""){
				var o = $(this).find(".head .valign_mid");
				o.addClass("text-danger");
				txtntdn += ""+o.html()+"<br/>";
			}else{ $(this).find(".head .valign_mid").removeClass("text-danger"); nmdn++; }			
			});
		
		if(typeof(flg)=="undefined"){	
		$(".reviwsyst .revsubbox input").on("click change", function(){ ros_highlighter(1); });
		}	
	}

$(function(){
	
	$("#cessationCounselling").trigger('change');
	$("#this_blood_sugar_time,#source_of_smoke").trigger('change');
	$(".selectpicker_new").each(function(i,elem){
		var id = $(elem).attr('id');
		var val = $(elem).val();
		if(val == 'Other')
		{
			$('#div_'+id).addClass('hidden');
			$('#other_'+id).removeClass('hidden');
		}
		
	});
	
	$('body').on('show.bs.modal','#bs_history',function(){
		var btn_arr = [['Save','','top.fmain.save_blood_sugar();']];
		set_modal_btns('bs_history .modal-footer',btn_arr);
	});
	
	$('body').on('show.bs.modal','#ch_history',function(){
		var btn_arr = [['Save','','top.fmain.save_cholesterol();']];
		set_modal_btns('ch_history .modal-footer',btn_arr);
	});
	
	$("#text_diabetes_id,#rel_text_diabetes_id").on('changed.bs.select', function(event, changedIndex, isSelected, previousValue ) {
		var $this = $(this);
		
		var v = $this.val();
		if( changedIndex == 0 && isSelected && ($.inArray('DM Type 2',v)) > -1 )	{
			var i = $.inArray('DM Type 2',v);
			v.splice(i,1);
		} else if( changedIndex == 1 && isSelected && ($.inArray('DM Type 1',v)) > -1 )	{
			var i = $.inArray('DM Type 1',v);
			v.splice(i,1);
		}
		$this.selectpicker('val',v);
	});
	
});


$(document).ready(function () {
		ros_highlighter();
	
	});