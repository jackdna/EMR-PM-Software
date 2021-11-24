var webroot = top.JS_WEB_ROOT_PATH;
//var date_global_format = window.opener.top.jquery_date_format;
if(typeof(window.top.jquery_date_format)!='undefined')var date_global_format = window.top.jquery_date_format;
else if(typeof(window.opener.top.jquery_date_format)!='undefined')var date_global_format = window.opener.top.jquery_date_format;

date_global_format = date_global_format.replace('d','dd').replace('m','mm').replace('Y','yyyy');
// Check in / Add new patient js functions
function show_multi_phy(op,phyType){
	op = op || 0;
	phyType = phyType || 0;
	
	var pTypeStr		=	"";
	var pTypeHidStr		=	"";
		
	if(phyType == 1)
	{
			ModalID			=	"referringPhysician";
			txtFieldArr		=	"txtRefPhyArr[]";
			pTypeStr		=	"strRefPhy";
			pTypeHidStr		=	"strRefPhyHid";
			hiddFieldTxt	=	"hidRefPhy";
	}
	else if(phyType == 4)
	{
			ModalID			=	"primaryCarePhysician";
			txtFieldArr		=	"txtPCPDemoArr[]";
			pTypeStr		=	"strPCPDemoPhy";
			pTypeHidStr		=	"strPCPDemoHid";
			hiddFieldTxt	=	"hidPCPDemo";
	}
	
	if(op == 1)
	{
		var arrPhy 		= new Array();
		var arrPhyHid 	= new Array();
		var strPhy 		= "";
		var strPhyHid 	= "";
		
		if(document.getElementsByName(txtFieldArr))
		{
				var objPhyArr = document.getElementsByName(txtFieldArr);
				for(var i = 0; i < objPhyArr.length; i++){
					var objPhyArrID = objPhyArr[i].id;
					var arrPhyArrID = objPhyArrID.split("-");
					var hidPhyArrID = "hidPhyArr-" + arrPhyArrID[1];
					if((document.getElementById(objPhyArrID)) && (document.getElementById(hidPhyArrID))){
						arrPhy[i] = document.getElementById(objPhyArrID).value;
						arrPhyHid[i] = document.getElementById(hidPhyArrID).value;
					}							
				}
				if(arrPhy.length > 0)
				{
					strPhy = arrPhy.join("!~#~!");
					strPhyHid = arrPhyHid.join("!~#~!");
				}
		}
		
		var d = 'request_from=scheduler&mode=get&phyType='+phyType+'&'+pTypeStr+'='+strPhy+'&'+pTypeHidStr+'='+strPhyHid;
		var url = webroot + '/interface/patient_info/ajax/muti_phy.php?'+d;
		
		top.master_ajax_tunnel(url,show_multi_phy_handler);
		
	}
	else if(op == 2){
				var selectedEffect = "blind";
				if(phyType == 1){
					var strTxtRefPhyArr = "";
					var strHidRefPhyArrID = "";	
					var strHidRefPhyIdID = "";				
					if(document.getElementsByName("txtRefPhyArr[]")){
						var objRefPhyArr = document.getElementsByName("txtRefPhyArr[]");
						for(var i = 0; i < objRefPhyArr.length; i++){
							var objRefPhyArrID = objRefPhyArr[i].id;
							var arrRefPhyArrID = objRefPhyArrID.split("-");
							var hidRefPhyArrID = "hidRefPhyArr-" + arrRefPhyArrID[1];
							var hidRefPhyIdID = "hidRefPhyId" + arrRefPhyArrID[1];
							if((document.getElementById(objRefPhyArrID)) && (document.getElementById(hidRefPhyArrID))){
								strTxtRefPhyArr += document.getElementById(objRefPhyArrID).value + "!$@$!";
								strHidRefPhyArrID += document.getElementById(hidRefPhyArrID).value + "!$@$!";
								if(document.getElementById(hidRefPhyIdID)){
									strHidRefPhyIdID += document.getElementById(hidRefPhyIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeleteRefPhyVal = document.getElementById("hidDeleteRefPhy").value;
					
					var d = 'request_from=scheduler&mode=save&phyType='+phyType+'&strTxtRefPhyArr='+strTxtRefPhyArr+'&strHidRefPhyIdID='+strHidRefPhyIdID+'&strHidRefPhyArrID='+strHidRefPhyArrID+'&hidDeleteRefPhyVal='+hidDeleteRefPhyVal;
					var url = webroot + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,show_multi_phy_save_handler);
				}
				else if(phyType == 2){
					var strTxtCoPhyArr = "";
					var strHidCoPhyArrID = "";
					var strHidCoPhyIdID = "";
					if(document.getElementsByName("txtCoPhyArr[]")){
						var objCoPhyArr = document.getElementsByName("txtCoPhyArr[]");
						for(var i = 0; i < objCoPhyArr.length; i++){
							var objCoPhyArrID = objCoPhyArr[i].id;
							var arrCoPhyArrID = objCoPhyArrID.split("-");
							var hidCoPhyArrID = "hidCoPhyArr-" + arrCoPhyArrID[1];
							var hidCoPhyIdID = "hidCoPhyId" + arrCoPhyArrID[1];
							if((document.getElementById(objCoPhyArrID)) && document.getElementById(hidCoPhyArrID)){
								strTxtCoPhyArr += document.getElementById(objCoPhyArrID).value + "!$@$!";
								strHidCoPhyArrID += document.getElementById(hidCoPhyArrID).value + "!$@$!";
								if(document.getElementById(hidCoPhyIdID)){
									strHidCoPhyIdID += document.getElementById(hidCoPhyIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeleteCoPhyVal = document.getElementById("hidDeleteCoPhy").value;
					
					var d = "request_from=scheduler&mode=save&phyType="+phyType+"&strTxtCoPhyArr="+strTxtCoPhyArr+"&strHidCoPhyIdID="+strHidCoPhyIdID+"&strHidCoPhyArrID="+strHidCoPhyArrID+"&hidDeleteCoPhyVal="+hidDeleteCoPhyVal;
					var url = webroot + '/interface/patient_info/ajax/muti_phy.php?'+d;
					
					top.master_ajax_tunnel(url,show_multi_phy_save_handler_2);
				}
				else if(phyType == 4){
					var strTxtPCPDemoArr = "";
					var strHidPCPDemoArrID = "";
					var strHidPCPDemoIdID = "";
					if(document.getElementsByName("txtPCPDemoArr[]")){
						var objPCPDemoArr = document.getElementsByName("txtPCPDemoArr[]");
						for(var i = 0; i < objPCPDemoArr.length; i++){
							var objPCPDemoArrID = objPCPDemoArr[i].id;
							var arrPCPDemoArrID = objPCPDemoArrID.split("-");
							var hidPCPDemoArrID = "hidPCPDemoArr-" + arrPCPDemoArrID[1];
							var hidPCPDemoIdID = "hidPCPDemoId" + arrPCPDemoArrID[1];
							if((document.getElementById(objPCPDemoArrID)) && document.getElementById(hidPCPDemoArrID)){
								strTxtPCPDemoArr += document.getElementById(objPCPDemoArrID).value + "!$@$!";
								strHidPCPDemoArrID += document.getElementById(hidPCPDemoArrID).value + "!$@$!";
								if(document.getElementById(hidPCPDemoIdID)){
									strHidPCPDemoIdID += document.getElementById(hidPCPDemoIdID).value + "!$@$!";
								}
							}							
						}
					}
					var hidDeletePCPDemoVal = document.getElementById("hidDeletePCPDemo").value;
					
					var d = "request_from=scheduler&mode=save&phyType="+phyType+"&strTxtPCPDemoArr="+strTxtPCPDemoArr+"&strHidPCPDemoArrID="+strHidPCPDemoArrID+"&strHidPCPDemoIdID="+strHidPCPDemoIdID+"&hidDeletePCPDemoVal="+hidDeletePCPDemoVal;
					var url = webroot + '/interface/patient_info/ajax/muti_phy.php?'+d;
					top.master_ajax_tunnel(url,show_multi_phy_save_handler_4);
				}
			}
}

function show_multi_phy_handler(respRes)
{
		var arrResp = respRes.split("!~-1-~!");
		var arrTemp = arrResp[1].split("~-~");
		var phyName = new Array();
		for(var a = 0; a <= arrTemp.length; a++){
			phyName[a] = arrTemp[a];
		}
		arrTemp = arrResp[2].split("~-~");
		var phyNameID = new Array();
		for(var a = 0; a <= arrTemp.length; a++){
			phyNameID[a] = arrTemp[a];
		}
		
		$("#"+ModalID).html(arrResp[0]);
		
		if(document.getElementsByName(txtFieldArr)){
			var objPhyArr = document.getElementsByName(txtFieldArr);
			for(var i = 0; i < objPhyArr.length; i++){
				var objPhyArrID = objPhyArr[i].id;
				var arrPhyArrID = objPhyArrID.split("-");
				var hidPhyArrID = hiddFieldTxt + "Arr-" + arrPhyArrID[1];
				if((document.getElementById(objPhyArrID)) && (document.getElementById(hidPhyArrID))){
				}							
			}
		}
		$("#"+ModalID).modal('show');
}

function mpay_div_show(visitCopayId,testCopayId,prevBalId){
	var copayVisitID = visitCopayId;
	var copayTestID = testCopayId;
	var PrevBalID = prevBalId;
	console.log($('#'+copayVisitID).val()+'==='+$('#'+copayTestID).val()+'===='+$('#'+PrevBalID).val());
	if($('#'+copayVisitID).val()==''|| typeof($('#'+copayVisitID).val()) == 'undefined'){firstCopay = 0;}else{firstCopay = parseInt($('#'+copayVisitID).val());}
	if($('#'+copayTestID).val()=='' || typeof($('#'+copayTestID).val()) == 'undefined'){secondCopay = 0;}else{secondCopay = parseInt($('#'+copayTestID).val())}
	if($('#'+PrevBalID).val()=='' || typeof($('#'+PrevBalID).val()) == 'undefined'){prevBal = '0.00';}else{prevBal = $('#'+PrevBalID).val();}
	$('#mpay_copay').val((firstCopay+secondCopay)+'.00');
	$('#mpay_noncopay').val(prevBal);
	$('#mpay_dueamount').val('0.00');
	$('#mpay_ask_div').modal('show');
}

function collect_n_send_mpay(){
	var qString = '../../mpay/index.php?from=checkin&copay='+$('#mpay_copay').val()+'&paid='+$('#mpay_noncopay').val()+'&balance='+$('#mpay_dueamount').val()+'&patient_id='+$('#edit_patient_id').val()+'&sch_id='+$('#sch_id').val();
	dgi('mpay_frame').src = qString;				
}

function hide_mpay_div(){$('#mpay_ask_div').modal('hide');$('#btn_mpay').val('Submit to Mpay');}

function show_multi_phy_save_handler(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("ref_phy_name").className = "form-control";
		document.getElementById("ref_phy_name").value = arrRespRes[1];
		document.getElementById("ref_phy_id").value = arrRespRes[2];
		document.getElementById("ref_phy_name").setAttribute('data-content',arrRespRes[3]);
		$("#"+ModalID).modal('hide');
	}
}

function show_multi_phy_save_handler_2(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("co_man_phy").className = "form-control";
		document.getElementById("co_man_phy").value = arrRespRes[1];
		document.getElementById("co_man_phy_id").value = arrRespRes[2];
		document.getElementById("co_man_phy").setAttribute('data-content',arrRespRes[3]);
		$("#"+ModalID).modal('hide');
	}		
}

function show_multi_phy_save_handler_4(respRes)
{
	var arrRespRes = respRes.split("-!-");
	if(arrRespRes[0] == "DONE"){
		document.getElementById("primary_care_name").className = "form-control";
		document.getElementById("primary_care_name").value = arrRespRes[1];
		document.getElementById("primary_care_phy_id").value = arrRespRes[2];
		document.getElementById("primary_care_name").setAttribute('data-content',arrRespRes[3]);
		$("#"+ModalID).modal('hide');
	}
}

function add_phy_row(add_image_id, del_image_id, intCounter, phyType)
{
	var objDelImg = $("#"+del_image_id);
	var objAddImg = $("#"+add_image_id);
	
	if(objAddImg){ objAddImg.addClass('hidden') }			
	if(objDelImg){ objDelImg.removeClass('hidden');	}
	
	var intCounterTemp = parseInt(intCounter) + 1;
	var divTrTag = document.createElement("div");
	divTrTag.id = "divTR" + "-" + phyType + "-" + intCounterTemp;
	divTrTag.className = "col-xs-12 margin-top-5";
	//divTrTag.style.marginBottom = "5px";
	
	var divTDTag1 = document.createElement("div");
	divTDTag1.className = "col-xs-2 text-center";
	divTDTag1.innerHTML = intCounterTemp;			
	divTrTag.appendChild(divTDTag1);
	
	var divTDTag2 = document.createElement("div");
	divTDTag2.className = "col-xs-9";
	
	if(phyType == 1){
		var txtId = "txtRefPhyArr-"+intCounterTemp;
	}
	else if(phyType == 2){
		var txtId = "txtCoPhyArr-"+intCounterTemp;
	}
	else if(phyType == 4){
		var txtId = "txtPCPDemoArr-"+intCounterTemp;
	}
	var txtBox = document.createElement("input");
	txtBox.type = "text";
	if(phyType == 1){
		txtBox.name = "txtRefPhyArr[]";
	}
	else if(phyType == 2){
		txtBox.name = "txtCoPhyArr[]";
	}
	else if(phyType == 4){
		txtBox.name = "txtPCPDemoArr[]";
	}
	txtBox.id = txtId;
	txtBox.value = "";
	txtBox.className = "form-control";
	if(phyType == 1){
		var hidId = "hidRefPhyArr-"+intCounterTemp;
	}
	else if(phyType == 2){
		var hidId = "hidCoPhyArr-"+intCounterTemp;
	}
	else if(phyType == 4){
		var hidId = "hidPCPDemoArr-"+intCounterTemp;
	}
	
	txtBox.setAttribute('onKeyup',"top.loadPhysicians(this,'"+hidId+"');");
	txtBox.setAttribute('onFocus',"top.loadPhysicians(this,'"+hidId+"');");
	
	var hidBox = document.createElement("input");
	hidBox.type = "hidden";
	if(phyType == 1){
		hidBox.name = "hidRefPhyArr[]";
	}
	else if(phyType == 2){
		hidBox.name = "hidCoPhyArr[]";
	}
	else if(phyType == 4){
		hidBox.name = "hidPCPDemoArr[]";
	}
	divTDTag2.appendChild(txtBox);
	hidBox.id = hidId;
	hidBox.value = "";
	divTDTag2.appendChild(hidBox);
	divTrTag.appendChild(divTDTag2);
	
	var divTDTag3 = document.createElement("div");
	divTDTag3.className = "col-xs-1";
	var imgDelId = "imgDel" + "-" + phyType + "-" + intCounterTemp;
	var imgAddId = "imgAdd" + "-" + phyType + "-" + intCounterTemp;
	var strImgHTML = "<span id=\""+imgDelId+"\" name=\""+imgDelId+"\" class=\"pointer hidden\" onClick=\"del_phy_row('"+imgDelId+"','"+intCounterTemp+"', '','"+phyType+"');\" ><i class=\"glyphicon glyphicon-remove\"></i></span>";
			
	strImgHTML += "<span id=\""+imgAddId+"\" name=\""+imgAddId+"\" onClick=\"add_phy_row('"+imgAddId+"','"+imgDelId+"', '"+intCounterTemp+"', '"+phyType+"');\" class=\"pointer\" ><i class=\"glyphicon glyphicon-plus\"></i></span>";
	
	divTDTag3.innerHTML = strImgHTML;
	
	divTrTag.appendChild(divTDTag3);
	if(phyType == 1){
		document.getElementById("divMultiPhyInner1").appendChild(divTrTag);
	}
	else if(phyType == 2){
		document.getElementById("divMultiPhyInner2").appendChild(divTrTag);
	}
	else if(phyType == 4){
		document.getElementById("divMultiPhyInner4").appendChild(divTrTag);
	}
	document.getElementById(txtId).focus();
}

function del_phy_row(del_image_id, intCounter, intPhyIdDB, phyType)
{
	var objDelImg = $("#"+del_image_id);
	
	intPhyIdDB = intPhyIdDB || 0;
	var divTrTag = "divTR" + "-" + phyType + "-" + intCounter
	if((intPhyIdDB > 0) && phyType == 1){				
		document.getElementById("hidDeleteRefPhy").value += intPhyIdDB+"~~"+intCounter+'-';
	}
	else if((intPhyIdDB > 0) && phyType == 2){				
		document.getElementById("hidDeleteCoPhy").value += intPhyIdDB+"~~"+intCounter+'-';
	}
	else if((intPhyIdDB > 0) && phyType == 4){				
		document.getElementById("hidDeletePCPDemo").value += intPhyIdDB+"~~"+intCounter+'-';
	}
	if(document.getElementById(divTrTag)){
		var divType = "divMultiPhyInner" + phyType;
		var objMainDiv = document.getElementById(divType);
		objMainDiv.removeChild(document.getElementById(divTrTag));
	}
}

var type_head_source = [];
function setHeardOther(obj,heard_about_us_textarea_cols,heard_abt_desc_val){	
	var heardAbtVal = $(obj).val();
	var objOther = $('#heardAbtOther');
	var objDesc = $('#heardAbtDesc');
	var img = $("#imgBackHeardAboutUs");
	var heardAboutSearch = ['Family','Friends','Doctor','Previous Patient.','Previous Patient'];

	if(heardAbtVal != '')
	{

		if(heardAbtVal == 'Other'){
			if($(objOther).hasClass('hide') === true){
				$(objOther).removeClass('hide');
			}	
		}else{
			if($(objOther).hasClass('hide') === false){
				$(objOther).addClass('hide');
			}
		}

		if( heardAbtVal !== 'Other' ) {
			var tmpArr = heardAbtVal.split("-");
			heardAbtVal = tmpArr[1].trim();
		}
		
		if($.inArray(heardAbtVal,heardAboutSearch ) !== -1 ) {
			if( heardAbtVal == 'Doctor') {
					$("#heardAbtSearch").attr('onkeyup',"top.loadPhysicians(this,'heardAbtSearchId')")
															.attr('onfocus',"top.loadPhysicians(this,'heardAbtSearchId')")
															.removeAttr('onKeydown');				
			}
			else {
				$("#heardAbtSearch").removeAttr('onkeyup onkeyup')
														.attr('onKeydown','if( event.keyCode == 13) { searchHeardAbout(); }');	
			}
			$("#tdHeardAboutSearch").removeClass('hidden').addClass('inline');
			$("#heardAbtDesc").removeClass('inline').addClass('hidden');
		}
		else {
			$("#heardAbtDesc").removeClass('hidden').addClass('inline');
			$("#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
			$('#heardAbtDesc').typeahead({source:type_head_source});
		}
	}
	else{
		$("#heardAbtDesc,#tdHeardAboutSearch").removeClass('inline').addClass('hidden');
	}
}

function validateAddress(f,callFrom){
	callFrom = callFrom || '';
	valid_flag = 1;
	$("div[id^=div_address]").each(function(index, element) {
	   div_id = "#"+$(this).attr("id");
	   div_input = div_id +" input[type=text]";
	   $(div_input).each(function(index, element) {
			if($(this).val() != ""){
				zip_ele = div_id+ " input[name^=postal_code]";
				if($(zip_ele).val() == ""){
					if(callFrom == "checkin")
					fAlert("Enter "+top.zipLabel); //, "","","", "",document.getElementById('div1')
					else
					fAlert("Enter "+top.zipLabel);
					changeClass(document.getElementById($(zip_ele).attr("id")),1);
					$("#div_all_addresses").scrollTop($(zip_ele).position().top);
					valid_flag = 0;
					return false;
				}else{
					changeClass(document.getElementById($(zip_ele).attr("id")))
				}
			}
	   });
	   if(valid_flag == 0)return false;
	});
	if(valid_flag == 0)return false;
	else return true;
}

function swap_visibility_of_objects(div_id,textfield_id,selectid){
	var div = $('#'+div_id+'');
	var text_field = $('#'+textfield_id+'');
	var selectbox = $('#'+selectid+'');
	
	if(div.hasClass('hide') === false){
		div.addClass('hide');
		text_field.val('');
		selectbox.val('').trigger('change');
		if(selectbox.hasClass('selectpicker') )
			selectbox.selectpicker('refresh');
		if(selectbox.val() == ''){
			$('#heardAbtDesc').hide();
		}
	}
}


function setHeardType(obj){
	var orignalVal = $(obj).val();
	var arrOrignalVal = orignalVal.split("-");
	if(arrOrignalVal.length > 1){
		var val = arrOrignalVal[1];
		if(val == 'Dr.'){
		//var adf = $('#heardAbtDesc').typeahead({source:customarrayphy});
		return false;
		}
			repObj = val.replace(' ','_');
		try{		
			//$('#heardAbtDesc').typeahead({source:eval(repObj)});
		}catch(e){
			var temp = new Array();
			//$('#heardAbtDesc').typeahead({source:eval(temp)});
		}	
	}
	else{
		var val = arrOrignalVal;	
	}		
}

function changeClassCombo(obj){
	var obj = $(obj);
	if(obj.val() != '' && (obj.hasClass('mandatory') === true)){
		obj.removeClass('mandatory');
	}else if(obj.val() == '' && obj.hasClass('mandatory') === false){
		obj.addClass('mandatory');
	}
	$(obj).selectpicker('refresh');
}

function search_email(evnt,obj,div_id){
	var keycode = evnt.keyCode || evnt.which; 
	var value = $(obj).val();
	if(value == ''){
		if($('#'+div_id+' select').hasClass('hide') === false){
			$('#'+div_id+'').find('select').addClass('hide');
			$(obj).val('');
		}
	}
	else if(value.search('@') > 0){
		var email = value.split('@');
		set_email_opt(email,div_id,keycode);
	}
}

function set_email_opt(email,div_id,keycode){
	
	var select_obj = $('#'+div_id+'').find('select');
	if($(select_obj).hasClass('hide') === true){
		$(select_obj).removeClass('hide');
	}
	$(select_obj).on('change',function(){
		var selectedVal = $(this).val();
		var final_email = email[0]+selectedVal;
		$('#'+div_id+'').parent().find('input').val(final_email);
		if($(select_obj).hasClass('hide') === false){
			$(select_obj).addClass('hide');
		}
	});
	
	if(keycode == 9){
		if($('#'+div_id+' select').hasClass('hide') === false){
			$('#'+div_id+' select').addClass('hide');
		}
	}
	
	hideSelectDrpdwn(div_id);
}

function hideSelectDrpdwn(div_id) {
	//Hide emails select options after @
	$(document).on('keyup keypress', function(e) {
	  var keyCode = e.keyCode || e.which;
		if(keyCode == 9){
			if($('#'+div_id+' select').hasClass('hide') === false){
				$('#'+div_id+' select').addClass('hide');
			}
		}
	});
	$(document).on('click', function() {
		if($('#'+div_id+' select').hasClass('hide') === false){
			$('#'+div_id+' select').addClass('hide');
		}
	});
}

function add_new_address(callFrom){
	callFrom = callFrom || '';
	i = $('input[name^=street').length;
	var html = '<div id="div_address'+i+'" class="col-sm-12 pdb10">';
		html += '<div class="row">';
			html += '<div class="col-sm-12">';
				html += '<div class="headinghd">';
					html += '<div class="row">';
						html += '<div class="col-sm-10">';
							html += '<div class="radio radio-inline">';
								html += '<input type="radio" name="all_communication" id="all_communication_'+i+'" value="'+i+'">';
								html += '<label for="all_communication_'+i+'"><strong style="font-family: robotobold;font-size:16px">ALL COMMUNICATIONS</strong></label>';
							html += '</div>';
						html += '</div>';	
						html += '<div class="col-sm-2 text-right">';
							html += '<span class="glyphicon glyphicon-remove pointer" style="color:red;vertical-align:sub;font-size:18px" onClick="$(\'#div_address'+i+'\').remove();"></span>';
						html += '</div>';
					html += '</div>';
				html += '</div>';		
			html += '</div>';
			html += '<div class="col-sm-12">';
				html += '<div class="row">';
					html += '<div class="col-sm-6">';
						html += '<label>Street1</label>';
						html += '<input name="street['+i+']" id="street" type="text" value="" class="form-control">';
					html += '</div>';
					html += '<div class="col-sm-6">';
						html += '<label>Street2</label>';
						html += '<input name="street2['+i+']" id="street2" type="text" class="form-control" value="">';
					html += '</div>';	
				html += '</div>';
			html += '</div>';
			html += '<div class="col-sm-6">';
				html += '<div class="row">';
					html += '<div class="col-sm-3">';
						html += '<label>'+top.zipLabel+'</label>';
						html += ' <input name="postal_code['+i+']" type="text" id="code'+i+'" onChange="zip_vs_state(this,\'city_'+i+'\',\'state_'+i+'\',\'country_code_'+i+'\',\'county_'+i+'\')" onBlur="zip_vs_state(this,\'city_'+i+'\',\'state_'+i+'\',\'country_code_'+i+'\',\'county_'+i+'\'); validate_zip(this);" value=""   maxlength="'+top.zip_length+'" class="form-control">';
					html += '</div>';
					html += '<div class="col-sm-3">';
						html += '<label> </label>';
						if(top.zip_ext){
							html += '<input name="zip_ext['+i+']" type="text" id="zip_ext" value="" maxlength="4" class="form-control"/>';	
						}
					html +=	'</div>';
					html += '<div class="col-sm-6">';
						html += '<label>City</label>';
						html += '<input name="city['+i+']" type="text" id="city_'+i+'" value="" class="form-control">	';
					html += '</div>';	
				html += '</div>';
			html += '</div>';	
			html += '<div class="col-sm-6">';
				html += '<div class="row">';
					html += '<div class="col-sm-4">';
						html += '<label>'+top.state_label+'</label>';
						html += '<input name="state['+i+']" type="text" maxlength="'+top.state_length+'" id="state_'+i+'" value="" class="form-control">';
					html += '</div>';
					html += '<div class="col-sm-4">';
						html += '<label>County</label>';
						html += '<input name="county'+i+']" type="text" class="form-control" id="county_'+i+'" value="">';
					html += '</div>';	
					html += '<div class="col-sm-4">';
						html += '<label>Country</label>	';
						html += '<input name="country_code['+i+']" type="text" class="form-control" id="country_code_'+i+'" value="'+top.int_country+'">';	
					html += '</div>';
					
				html += '</div>';	
			html += '</div>';
		html += '</div>';
	html += '</div>';
	$('#addNew_address').append(html);
}

function del_address(cnt,add_id){
	jId = "#div_address"+cnt;
	$(jId).remove();
	ids = $("#address_del_id").val();
	$("#address_del_id").val(ids+","+add_id);
}
function zip_vs_state(objZip,objCity,objState,objCountry,objCounty){
	// window.opener.top.stop_zipcode_validation -> Not working yet as constant not declared 
	objCity = $('#'+objCity+'');
	objState = $('#'+objState+'');
	objCountry = $('#'+objCountry+'');
	objCounty = $('#'+objCounty+'');
	
	zip_code = $(objZip).val();
	if(zip_code == ''){
		return false;
	}
	var url = webroot+"/interface/patient_info/ajax/demographics/add_city_state.php?zipcode="+zip_code;
	$.ajax({
		url:url,
		success:function(data)
		{
			var val=data.split("-");
			var city = $.trim(val[0]);
			var state = $.trim(val[1]);
			var country = $.trim(val[3]);
			var county = $.trim(val[4]); 
			if(city!="")
			{
				$(objCity).val(city);
				$(objState).val(state);
				if(country){$(objCountry).val(country);}
				if(county){$(objCounty).val(county);}
				return;
			}
			if(typeof(window.opener.top.int_country) !="undefined" && window.opener.top.int_country == "UK"){
				if(typeof(document.getElementById("zipCodeStatus"))!="undefined" && document.getElementById("zipCodeStatus") != null && document.getElementById("zipCodeStatus") != "undefined")
				document.getElementById("zipCodeStatus").value="OK";
				//return;
			}
			if(typeof(window.opener.top.stop_zipcode_validation) !="undefined" && window.opener.top.stop_zipcode_validation == "YES"){
				//DO NOT VALIDATE ZIPCODE
				return;
			}
			
			$(objZip).val("");
			changeClass(document.getElementById($(objZip).attr("id")),1);
			if(typeof(window.opener.top.fAlert) != "undefined")
				top.fAlert('Please enter correct '+top.zipLabel);
			else{
				top.fAlert('Please enter correct '+top.zipLabel);
			}
			$(objZip).select();
			$(objCity).val(" ");
			$(objState).val(" ");
			$(objCounty).val(" ");
		}
   });
}

function lostFocus(field){
	var arguments = lostFocus.arguments[1];
	if(trim(field.value)=="" && arguments=="mandatory"){
		if($(field).hasClass('mandatory') === false){
			$(field).addClass(arguments)
		}
	}
}

function callme(field, defaultClass){
	if(trim(field.value) != ""){
		if($(field).hasClass('mandatory') === true){
			$(field).removeClass('mandatory');
			$(field).addClass("form-control");
		}	
	}else{
		$(field).addClass(defaultClass);
	}
}

function changeClass(obj){
	if($(obj).val() != ''){
		if($(obj).hasClass('mandatory')){
			$(obj).removeClass('mandatory');
		}
	}
	else{
		if($(obj).hasClass('mandatory') === false){
			$(obj).addClass('mandatory');
			$(obj).val('');
		}
	}
}

function showHippaOtherTxtBox(obj1,obj2,obj3){
	var obj1Id = obj1;
	var obj2Id = obj2;
	var obj3Id = obj3;
	if($('#'+obj1Id+'').val() == 'Other'){
		if( $('#'+obj1Id+'').hasClass('selectpicker') ) 
			$('#'+obj1Id+'').selectpicker('hide');
		else
			$('#'+obj1Id+'').hide();

		$('#'+obj2Id+'').parent().removeClass('hide');
		$('#'+obj2Id+'').css('display','block');
		$('#'+obj3Id+'').css('display','block');
	}
}

function showHippaComboBox(obj1,obj2,obj3){
	console.log(obj1);
	var obj1Id = obj1;
	var obj2Id = obj2;
	var obj3Id = obj3;
	$('#'+obj2Id+'').value = "";
	$('#'+obj2Id+'').parent().addClass('hide');
	$('#'+obj2Id+'').css('display','none');
	$('#'+obj3Id+'').css('display','none');
	if( $('#'+obj1Id+'').hasClass('selectpicker') ) 
		$('#'+obj1Id+'').selectpicker('refresh').selectpicker('show');
	else 	
		$('#'+obj1Id+'').show().val('');
}


function showRow(obj){
	var show = obj.value;
	var checkType = false; var cardType = false; 
	for(i=1;i<=12;i++){
		objAll = dgi('pay_method_'+i);
		objPay = dgi('item_pay_'+i);
		if(objAll && objAll.name != obj.name && (objAll.value=='Check' || objAll.value=='EFT' || objAll.value=='Money Order')){
			checkType = true;
		}else if(objAll && objAll.name != obj.name && objAll.value=='Credit Card'){
			cardType = true;
		}
	}
	switch(show){
		case "Cash":
			if(!checkType)
				$("#checkRow").hide();
			if(!cardType)
				$("#creditCardRow").hide();
		break;
		case "Check":
			document.getElementById("checkRow").style.display = "inline-table";
			if(!cardType)
				$("#creditCardRow").hide();
		break;
		case "EFT":
			document.getElementById("checkRow").style.display = "inline-table";
			if(!cardType)
				$("#creditCardRow").hide();
		break;
		case "Money Order":
			document.getElementById("checkRow").style.display = "inline-table";
			if(!cardType)
				$("#creditCardRow").hide();
		break;
		case "Credit Card":
			document.getElementById("creditCardRow").style.display = "inline-table";
			if(!checkType)
				$("#checkRow").hide();
		break;
	}
}

function doDateCheck(from, to) {
	if(validate_date(to) && validate_date(from) ){
	if (from.value != "" && to.value != ""){
		if (parseDate(from.value, date_global_format) >= parseDate(to.value, date_global_format)) {	
			return true;
		}
		else {
			if (from.value == "" || to.value == ""){ 
			}
			else{ 
					fAlert("Date of birth can not be greater than current date.");
					to.value="";
					return false;
			   }
			}
		}
	}
}

function parseDate(input, format) { 
 // format = format || 'yyyy-mm-dd'; // default format 
  format = format || date_global_format;
  var parts = input.match(/(\d+)/g),  
      i = 0, fmt = {}; 
  // extract date-part indexes from the format 
  format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; }); 
 
  return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]); 
}

function checkAge(obj,yBox,mBox){ 
	var today = new Date();
	var date_global_format = window.opener.top.jquery_date_format;
	var d = obj.value;	
	if (!/\d{2}\-\d{2}\-\d{4}/.test(d)) {
		showMessage();
		return false;
	}

	d = d.split("-");
	var byr = parseInt(d[2]); 
	var nowyear = today.getFullYear();
	if (byr > nowyear || byr < 1900) {
		showMessage();
		return false;
	}
	if(date_global_format == "m-d-Y"){
		var dateMonth = parseInt(d[0],10); 
	}else if(date_global_format == "d-m-Y"){
		var dateMonth = parseInt(d[1],10); 
	}
	
	var bmth = dateMonth-1; 
	if (bmth <0 || bmth >11) { 
		showMessage() 
		return false;
	}
	
	if(date_global_format == "m-d-Y")
		var bdy = parseInt(d[1],10); 
	else if(date_global_format == "d-m-Y")	
		var bdy = parseInt(d[0],10); 
		
	var dim = daysInMonthNew(bmth,byr);
	if (bdy <1 || bdy > dim) {  
		showMessage();
		return false;
	}

	if(obj==null || typeof(obj)=='undefined'){
		obj = document.getElementById('dob');
		yBox = document.getElementById("patient_age");
		mBox = document.getElementById("patient_age_month");
	}
	getAge_in_YM(obj,yBox,mBox,1);
}

function getAge_in_YM(obj,yearBox,monthBox,newDemographics){
	if(obj.value != ''){
		var dat = new Date();
		var curday = dat.getDate();
		var curmon = dat.getMonth()+1;
		var curyear = dat.getFullYear();
		var arr_objVal = obj.value.split('-');
		
		if(window.opener.top.jquery_date_format == "m-d-Y"){
			var calday = arr_objVal[1];
			var calmon = arr_objVal[0];
			var calyear = arr_objVal[2];
		}else if(window.opener.top.jquery_date_format == "d-m-Y"){
			var calday = arr_objVal[0];
			var calmon = arr_objVal[1];
			var calyear = arr_objVal[2];
			
		}
		var curd = new Date(curyear,curmon-1,curday);
		var cald = new Date(calyear,calmon-1,calday);
		var diff =  Date.UTC(curyear,curmon,curday,0,0,0) - Date.UTC(calyear,calmon,calday,0,0,0);
		var dife = datediff(curd,cald);
		if(newDemographics){
			dgi(yearBox).innerHTML = dife[0];
			dgi(monthBox).innerHTML = dife[1];
		}else{
			yearBox.value = dife[0];
			monthBox.value = dife[1];
		}
	}
}

function datediff(date1, date2) {
	var y1 = date1.getFullYear();
	var m1 = date1.getMonth();
	var d1 = date1.getDate();
	var y2 = date2.getFullYear();
	var m2 = date2.getMonth();
	var d2 = date2.getDate();
	
	if (d1 < d2) {
		m1--;
		d1 += DaysInMonth(y2, m2);
	}
	if (m1 < m2) {
		y1--;
		m1 += 12;
	}
	return [y1 - y2, m1 - m2, d1 - d2];
}

function get_date_separator(date){
	date = date || '';
	separator = '-';
	if(date.search("-")!="-1")
	separator = "-";
	else if(date.search("/")!="-1")
	separator = "/";
	else if(date.search("/\\/")!="-1")
	separator = "\\";
	return separator;
}

function on_dob_changed(){
	//checkdate(dgi('dob'));
	if( dgi('dob').value == '' ) {
		dgi('patient_age').innerHTML = 0;
		dgi('patient_age_month').innerHTML = 0;
		return false;
	}
	checkAge(dgi('dob'), 'patient_age', 'patient_age_month');
}

function getRealTimeEligibilityCI(insRecId, askElFrom, strRootDir, schId, strAppDate, intClentWinH){
		if(strRootDir != ""){
			askElFrom = askElFrom || 0;
			document.getElementById("divAjaxLoader").style.display = "block";
			var url = strRootDir+'/interface/patient_info/ajax/make_270_edi.php?action=ins_eligibility&insRecId='+insRecId+'&askElFrom='+askElFrom+'&schId='+schId+'&strAppDate='+strAppDate;
			$.ajax({
				url:url,
				type:'GET',
				success:function(response){
					if(response){
						document.getElementById("divAjaxLoader").style.display = "none";	
						var strResp = $.parseJSON(response);
						var arrResp = strResp.data.split("~~");
						if(arrResp[0] == "1" || arrResp[0] == 1){
							var alertResp = "";
							if(arrResp[1] != ""){
								alertResp += "Patient Eligibility Or Benefit Information Status :"+arrResp[1]+"\n";
							}
							if(arrResp[2] != ""){
								alertResp += "With Insurance Type Code :"+arrResp[2]+"\n \n";
							}
							if(alertResp != ""){
								if(arrResp[3] == "A"){
									document.getElementById('imgEligibility').src = "../../../library/images/eligibility_green.png";
								}
								else if(arrResp[3] == "INA"){
									document.getElementById('imgEligibility').src = "../../../library/images/eligibility_red.png";
								}
								document.getElementById('imgEligibility').title = alertResp;
								var elId = parseInt(arrResp[4]);
								var strShowMsg = arrResp[5];
								if((elId > 0) && (strShowMsg) == "yes"){
									alertResp += "Would you like to set Co-Pay, Deductible and Co-Insurance!\n"
								}
								if((elId > 0) && (strShowMsg) == "yes"){
									if(confirm(alertResp) == true){
										var urlAmount = strRootDir + '/interface/patient_info/eligibility/eligibility_report.php?set_rte_amt=yes&id='+elId;
										var h = intClentWinH;
										window.open(urlAmount,'setAmountRTE','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=1280,height='+h+',left=10,top=10');
									}
								}
								else{
									top.fAlert(alertResp);
								}
							}
						}
						else if(arrResp[0] == "2" || arrResp[0] == 2){						
							if(arrResp[1] != ""){
								fAlert(arrResp[1]);
								document.getElementById('imgEligibility').src = "../../../images/eligibility_red.png";
								document.getElementById('imgEligibility').title = arrResp[1];
							}
						}
						else{
							fAlert(arrResp[0]);
						}
					}
				}
			});
		}
		
	}


function validate_date(objName, format) {
	date = typeof(objName)=="object" ? objName.value : objName;
	if(date!=''){
		format = format || date_global_format;
		separator = get_date_separator(format);
		var regex = "";
		switch(format){
			case "dd"+separator+"mm"+separator+"yyyy":
			var regex = new RegExp("(((0[1-9]|[12][0-9]|3[01])["+separator+"](0[13578]|1[02])["+separator+"](19|20[0-9]{2}))|((0[1-9]|[12][0-9]|3[0])["+separator+"](0[469]|1[1])["+separator+"](19|20[0-9]{2}))|((0[1-9]|1[0-9]|2[0-9])["+separator+"](02)["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((0[1-9]|1[0-9]|2[0-8])["+separator+"](02)["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{4})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "mm"+separator+"dd"+separator+"yyyy":
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{4})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "mm"+separator+"dd"+separator+"yy":top.fAlert("mm"+separator+"dd"+separator+"yy");
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "yyyy"+separator+"mm"+separator+"dd":
			var regex = new RegExp("(((19|20[0-9]{2})["+separator+"](0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01]))|((19|20[0-9]{2})["+separator+"](0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0]))|(((19|20)(04|08|[2468][048]|[13579][26]))["+separator+"](02)["+separator+"](0[1-9]|1[0-9]|2[0-9]))|(((19|20)[0-9]{2}["+separator+"](02)["+separator+"](0[1-9]|1[0-9]|2[0-8]))))");
			//var regex = new RegExp("([1-9]{4}["+separator+"][0-9]{2}["+separator+"][0-9]{2})");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "yyyy"+separator+"dd"+separator+"mm":
			var regex = new RegExp("(((19|20[0-9]{2})["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](0[13578]|1[02]))|((19|20[0-9]{2})["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](0[469]|1[1]))|(((19|20)(04|08|[2468][048]|[13579][26]))["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"](02))|(((19|20)[0-9]{2}["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"](02))))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			default://----mm-dd-yyyy
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
		}
		if(regex.test(date)){
			flag = true;
			objName.value = date;
		}
		else flag = false;
		return flag;
	}
}

function validate_ssn(objName,format,length){
	ssn = typeof(objName)=="object" ? objName.value : objName;
	format = format || top.ssn_format;
	length = length || top.ssn_length;
	ssn_reg_exp_js =  (typeof(top.ssn_reg_exp_js)!="undefined" && typeof(top.ssn_reg_exp_js)!=null && top.ssn_reg_exp_js!='') ? top.ssn_reg_exp_js : "[^0-9\-+]";
	regExp = new RegExp(ssn_reg_exp_js,'g');
	refinedSSN = ssn.replace(regExp,"");
	refinedSSN = refinedSSN.replace(/[^0-9A-Za-z+]/g,'');
	refinedSSN = refinedSSN.substr(0,length);
	ssnLength = refinedSSN.replace(/[^0-9A-Za-z+]/g,'').length;
	if(ssnLength >0 && ((format!='' && ssnLength != length) || (format=='' && ssnLength > length))){
		//invalid_input_msg(objName, "Please Enter a valid SSN");
		if(typeof(opener) != "undefined"){
			top.fAlert("Invalid Social Security Number (SSN)");
		}else{
			top.fAlert("Invalid Social Security Number (SSN)");
		}
		objName.value = '';
		return false;
	}else{
		switch(format){
			case "###-###-####":
			objName.value = refinedSSN.replace(/(\d{3})(\d{3})(\d{4})/,'$1-$2-$3');
			break;
			case "###-##-####":
			objName.value = refinedSSN.replace(/(\d{3})(\d{2})(\d{4})/,'$1-$2-$3');
			break;
			default:	
			objName.value = refinedSSN;
		}
	}
	return true;
}

function set_position(div_id,source_id){
	var divObj = $('#'+div_id+'');
	var obj = $('#'+source_id+'');
	var isShown = false;
	if(divObj.is(':visible')){
		divObj.css('display','');
	}else{
		divObj.show();
		isShown = true;
	}
	if(isShown == true){
		var objOffset = $(obj).position();
		var divOffset = $(divObj).position();
		var window_height = $(window).height();
		
		if($('#'+div_id+'').length > 0){
			divOffset.top = parseInt(objOffset.top + 100);
			divObj.css('position','absolute').css({
				top:divOffset.top,
			}).css('z-index','99999999999').css('display','block');
		}
	}
}

function change_to_mandatory(input_arr){
	$.each(input_arr,function(id,val){
		var obj = $('#'+val);
		if(obj.hasClass('mandatory') === false){
			obj.addClass('mandatory');
		}
	});
}

$(function(){		
	$('[data-toggle="tooltip"]').tooltip();
	$('.selectpicker').selectpicker();
	$('.date-pick').datetimepicker({
		timepicker:false,
		format:window.opener.jquery_date_format,
		//formatDate:'Y-m-d',
		scrollInput:false
	});
	
	$(window).load(function(){
		if(typeof(window.opener.top.innerDim)=='function'){
			var innerDim = window.opener.top.innerDim();
			if(innerDim['w'] > 1600) innerDim['w'] = 1600;
			if(innerDim['h'] > 900) innerDim['h'] = 900;
			//window.resizeTo(innerDim['w'],innerDim['h']);
			brows	= get_browser();
			if(brows!='ie') innerDim['h'] = innerDim['h']-35;
			var result_div_height = innerDim['h']-210;
			//$('.mainwhtbox').height(result_div_height+'px');
		}
	});
	
	$('[data-toggle="popover"]').popover();
	// Add Mandatory Class to fields 
    if(typeof(mandatory_field_arr)!='undefined') {
        $.each(mandatory_field_arr,function(i,v){
			var obj = $('#'+v);
			if( typeof obj !== 'undefined') {
				obj.addClass('mndt-chk');
				if(obj.val() == '' && obj.hasClass('mandatory') === false){
					obj.addClass('mandatory');
					if(v == 'language') { $('#otherLanguage').addClass('mandatory'); }
				}
			}
		});
		
		$("body").on('change keyup','input,select',function(){
			if($(this).hasClass('mndt-chk') && $(this).val() == ''  ) { 
				if($(this)[0].type == 'select-one' && $(this).hasClass('selectpicker') ) {
					$(this).selectpicker('setStyle', 'btn-warning','add');
					$(this).selectpicker('setStyle', 'btn-default','remove');
				}else $(this).addClass('mandatory');
			}
			else {
				if($(this)[0].type == 'select-one' && $(this).hasClass('selectpicker') ) {
					$(this).selectpicker('setStyle', 'btn-warning','remove');
					$(this).selectpicker('setStyle', 'btn-default','add');
				}else $(this).removeClass('mandatory');
			}
		});	
			
	}
	

	if(typeof(advisory_field_arr)!='undefined') {
        $.each(advisory_field_arr,function(i,v){
			var obj = $('#'+v);
			if( typeof obj !== 'undefined') {
				obj.addClass('advisory-chk');
				if(obj.val() == '' && obj.hasClass('advisory') === false ){
					obj.addClass('advisory');
					if(v == 'language') { $('#otherLanguage').addClass('advisory'); }
				}
			}
		});
		
		$("body").on('change keyup','input,select',function(){
			if($(this).hasClass('advisory-chk') && $(this).val() == ''  ) { 
				if($(this)[0].type == 'select-one' && $(this).hasClass('selectpicker') ) {
					$(this).selectpicker('setStyle', 'btn-warning','add');
					$(this).selectpicker('setStyle', 'btn-default','remove');
				}else $(this).addClass('advisory');
			}
			else {
				if($(this)[0].type == 'select-one' && $(this).hasClass('selectpicker') ) {
					$(this).selectpicker('setStyle', 'btn-warning','remove');
					$(this).selectpicker('setStyle', 'btn-default','add');
				}else $(this).removeClass('advisory');
			}
		});	
			
    }
	
	
	
	$('.selectpicker').each(function(id,elem){
		if($(elem).hasClass('mndt-chk') && $(elem).val() == ''){
			$(elem).selectpicker('setStyle', 'btn-warning');
		}
		else if($(elem).hasClass('advisory-chk') && $(elem).val() == ''){
			$(elem).selectpicker('setStyle', 'btn-warning');
		}
	});

	$('body').on('change blur','input[id^="fname"], input[id^="mname"], input[id^="lname"], input[id^="suffix"], input[id^="street"], input[id^="street2"] ',function(event){ 
		var v = $(this).val();
		var c = capitalize_letter(v);
		$(this).val(c);
	});
});	
