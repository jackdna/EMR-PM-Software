$(document).ready( function() {
	$('[data-toggle="tooltip"]').tooltip();
});
var arrRefIdNameFax=new Array(); //array to get phy fax for "Other Ref Phy(Last, First Name)" text field
//Reff. Phy. Names
var arr_ref_phy_name = new Array;
$.each(global_php_var.typeahead_arr['stringAllPhy'],function(id,val){
	$.each(val,function(id,val){
		if(id == 'name'){
			arr_ref_phy_name.push(val);
		}
	});
});

//Reff. Phy. Fax No.
$.each(global_php_var.typeahead_arr['stringAllPhyFax'],function(id,val){
	arrRefIdNameFax[id] = val;
});

function unCheckMedicationAllergies(boolTrueFalse){
	if(boolTrueFalse==true){
		$("#chkChartHippa").prop('checked',false);
		$("#chkIncludeNotes").prop('checked',false);
		$("#chkRecordRelease").prop('checked',false);
		$("#chkPatientLegalforms").prop('checked',false);
		$("#chkPatientDemographics").prop('checked',false);
		$("#chkPatientCommunication").prop('checked',false);
	}
}

function executeExportReport(){
	$("#hidexport_report").val("Yes");
	submitPrintRequest(true);
}

function submitPrintRequest(valReturn){
	///Chart Note Main Part//
	if($('#chkChartAll').is(':checked') || $('#chkRecordRelease').is(':checked')){
		if($("#chkRecordRelease").is(':checked')){
			$("#chkIncludeNotes").prop('checked',false);
			$("#divDisclosed").modal('show');
			$('#divDisclosed').on('shown.bs.modal', function () {
				$('#DisclosedBy').selectpicker('refresh');
            });
			//$("#divDisclosed").css({'display':'block'});
			if(valReturn){
				//alert(valReturn);
				$("#divDisclosed").modal('hide');
			}else{
				return false;
			}
		}
		if($("#chkRecordRelease").is(':checked') === false){
			$("#divDisclosed").modal('hide');
		}	
	}
		
	//End Chart Notes Main Part//
	if($("#glaucoma").is(':checked')){
		checkAll(false);
		vis2(false);
	}
	if($("#patientInfoAll").is(':checked')){
		checkAll(false);
		$("#glaucoma").prop('checked',false);
	}
	
	$('#div_loading_image').show();
	var iframe = $('<iframe>');
	iframe.attr({
		'id':'dynamiciFrame',
		'name':'dynamiciFrame',
		'onload':'top.$("#div_loading_image").hide();',
		'width':'100%',
		'wmode':'transparent',
		'height':($('.reportlft').height() + ($('#module_buttons').height() - 20))+'px'
	});
	$('#print_function').attr('target','dynamiciFrame');
	$('#content_box').html('');
	$('#content_box').append(iframe);
	$(iframe).on('load',function(){
		$('#div_loading_image').hide();
	});
}

function vis(ad){
	$('[name^=special_all]').each(function(id,elem){
		$(elem).prop('checked',ad);
	});
}

function vis1(ad){
	var newval = ad == true ? false : '';
	//Deselect All Other Checked//
	checkAll(newval);
	//Disable All Other Checked//
	$('[name^=patient_info]').each(function(id,elem){
		if($(elem).attr('id') != 'patientInfoAll'){
			$(elem).prop('checked',newval);
			$(elem).prop('disabled',ad);
		}
	});
	if($("#insurance_scan_id").hasClass('hide') === false){
		$("#insurance_scan_id").addClass('hide');
	}
	$("#insurance_scan").prop('checked',false);

	$('[name^=chk_summary_box]').each(function(id,elem){
		if($(elem).attr('id') == 'chk_summary_box'){
			if($(elem).prop("checked") == true){
				$(elem).trigger('click');
			}
		}
	});
}

function ins_scan(val){
	if(val == true){
		if($('#insurance_scan_id').hasClass('hide') === true){
			$('#insurance_scan_id').removeClass('hide');
		}
	}else{
		if($('#insurance_scan_id').hasClass('hide') === false){
			$('#insurance_scan_id').addClass('hide');
		}	
	}
	$("#insurance_scan").prop('checked',val);
}


function vis2(ad){
	$('#butIdElectronic').prop('disabled',true);
	newval = ad == true ? false : '';
	if(ad == true){
		//Deselect All Other Checked
		checkAll(false);
	}
		
	//Deselect All Other Checked//
	$('[name^=patient_info]').each(function(id,elem){
		if($(elem).attr('id') != 'face_sheet'){
			$(elem).prop('checked',newval);
			$(elem).prop('disabled',ad);	
		}
	});
	if(ad == true && $("#facesheetTemplateExist").val() != "yes"){
		if($("#face_sheet_scan_id").hasClass('hide') === true){
			$('#face_sheet_scan_id').removeClass('hide');
		}
		if($("#face_sheet_detail_id").hasClass('hide') === true){
			$("#face_sheet_detail_id").removeClass('hide');
		}			
	}	
	else{
		if($("#face_sheet_scan_id").hasClass('hide') === false){
			$("#face_sheet_scan_id").addClass('hide');
		}
		
		if($("#face_sheet_detail_id").hasClass('hide') === false){
			$("#face_sheet_detail_id").addClass('hide');
		}			
	}

	if($("#face_sheet_scan").length > 0){
		$("#face_sheet_scan").prop('checked',ad);
	}
	
	if($("#insurance_scan_id").length > 0){
		if($("#insurance_scan_id").hasClass('hide') == false){
			$("#insurance_scan_id").addClass('hide');
		}
	}
	if($("#insurance_scan").length > 0){
		$("#insurance_scan").prop('checked',false);
	}
}

function checkAll(ad){
	$('[name^=chart_nopro]').each(function(id,elem){
		if(global_php_var.call_from != 'wv'){
			$(elem).prop('checked',ad);	
		}else{
			var elem_id = $(elem).attr('id');
			if(elem_id != 'chkOcuMeds' && elem_id != 'chkSysMeds' && elem_id != 'chkAllergiesList' && elem_id != 'chkProblemList' && elem_id != 'chkgeneralhealth' && elem_id != 'chkocular'){
				$(elem).prop('checked',ad);	
			}
		}
	});
	unCheckMedicationAllergies(true);
	if(ad==true){
		//uncheck glucoma
		GlaucomaOption(false);
		//uncheck a/scan
		AscanOption(false);
		//uncheck face sheet
		$("#face_sheet").prop('checked',false); 
		//uncheck patient information
		vis2(false);
	}
}

function clinical_selected()
{
	//uncheck glucoma
	GlaucomaOption(false);
	//uncheck a/scan
	AscanOption(false);
	//uncheck patient information
	vis2(false);
}
function GlaucomaOption(ad){
	$("#glaucoma").prop('checked',ad);
}
function AscanOption(ad){
	$("#chkspecial_all").prop('checked',ad); 
}

function enableElectronicBT(obj){	
	var obj_id = $(obj).attr('id');
	if(obj_id != "patientInfoAll"){
		var arrCbkName = global_php_var.chk_arr;
		if(obj_id == "cbkDemographics"){
			if($(obj).is(':checked') === false){
				var blCHK = false;
				for(var counter = 0; counter < arrCbkName.length; counter++){
					if($('#'+arrCbkName[counter]).is(':checked')){
						blCHK = true
						counter = arrCbkName.length;
					}
				}
				if(blCHK == false){
					$('butIdElectronic').prop('disabled',true);	
				}
			}
			if($(obj).is(':checked')){
				$('#butIdElectronic').prop('disabled',false);
			}
		}
		else if(obj_id == "cbkMedHx"){
			if($(obj).is(':checked') === false){
				var blCHK = false;
				for(var counter = 0; counter < arrCbkName.length; counter++){
					if($('#'+arrCbkName[counter]).is(':checked')){
						blCHK = true
						counter = arrCbkName.length;
					}
				}
				if(blCHK == false){
					$('#butIdElectronic').prop('disabled',true);	
				}
			}
			if(obj.checked == true){
				$('#butIdElectronic').prop('disabled',false);
			}
		}
		else if(obj_id == "cbkInsurance"){
			if($(obj).is(':checked') === false){
				var blCHK = false;
				for(var counter = 0; counter < arrCbkName.length; counter++){
					if($('#'+arrCbkName[counter]).is(':checked')){
						blCHK = true
						counter = arrCbkName.length;
					}
				}
				if(blCHK == false){
					$('#butIdElectronic').prop('disabled',true);	
				}
			}
			if(obj.checked == true){
				$('#butIdElectronic').prop('disabled',false);
			}
		}
	}
	else if(obj_id == "patientInfoAll"){		
		if($(obj).is(':checked') === true){
			$('#butIdElectronic').prop('disabled',false);	
		}
		else{
			$('#butIdElectronic').prop('disabled',true);
		}
	}
	else{		
		$('#butIdElectronic').prop('disabled',true);
	}
}

function check_exclusion(status){
	var span_html="";
	if(status=="done"){
		var t = 0;
		$('[name^=chart_exclusion]').each(function(id,elem){
			if($(elem).is(':checked') === true){
				var onj_val = $(elem).attr('title').replace("_"," ");
				if(t == 0){
					span_html+="Exclusion: "+onj_val.replace("_"," ");		
				}else{
					span_html+=", "+onj_val.replace("_"," ");
				}
				t++;
			}
		});	
	}else{
		$('[name^=chart_exclusion]').each(function(id,elem){
			$(elem).prop('checked',false);
		});
	}
	$("#exlude_innerHTML").html(span_html);
	$('#exclusion_div').modal('hide');
}

function getElectronicData(patId){
	var arrCbkName = global_php_var.chk_arr;
	var option = "option";
	//option-Demographics-Insurance-Allergies-Medications-Immunizations-
	if($('#patientInfoAll').is(':checked') === false){
		for(var counter = 0; counter < arrCbkName.length; counter++){
			if(arrCbkName[counter] == "cbkDemographics"){
				if($('#'+arrCbkName[counter]).is(':checked')){
					option += "-Demographics";
				}
			}
			else if(arrCbkName[counter] == "cbkMedHx"){
				if($('#'+arrCbkName[counter]).is(':checked')){
					option += "-Allergies-Medications-Immunizations-Problem_List-Labs";
				}
			}
			else if(arrCbkName[counter] == "cbkInsurance"){
				if($('#'+arrCbkName[counter]).is(':checked')){
					option += "-Insurance";
				}
			}
		}
	}
	if($('#patientInfoAll').is(':checked')){
		var option = "option";
		option += "-Demographics";
		option += "-Allergies-Medications-Immunizations-Problem_List-Labs";
		option += "-Insurance";
	}
	
	if(trim(option) == "option"){
		fAlert('Please select Electronic option(s) to get Electronic Data');
	}
	else{
		$('#option').val(option);
	}
	createPatCCD(patId,option);
}

function createPatCCD(patId,option){		
	
	$("#div_loading_image").show();		
	var electronicDOSCCD = "";
	if($('#cbkElectronicDos').is(':checked')){
		if($('#cmbxElectronicDOS').length > 0){	
			electronicDOSCCD = $('#cmbxElectronicDOS').val();
		}
	}
	else if($('#cbkElectronicDos').is(':checked') === false){
		electronicDOSCCD = "all";
	}
	var url = window.opener.top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/top_btn_ajax_handler.php?pId='+patId+'&option='+option+'+"&electronicDOSCCD='+electronicDOSCCD+'';
	window.opener.top.master_ajax_tunnel(url,getResponseCCD);
}

function getResponseCCD(r,ajax){
	if(typeof(ajax) === 'undefined' || ajax === false){
		var validate = true;
		var msg = 'Please enter the following information under Medical Hx or Check the option(s) precede to export: \n';
		if($.trim(r) !== ''){
			var strResponseVal = r;
			var arrResponseValMain = strResponseVal.split("##");	
			if(parseInt(arrResponseValMain[1]) == 1){
				var responseVal = arrResponseValMain[0];						
				var arrResponseVal = responseVal.split("~~");
				var hashValue = "";
				hashValue = arrResponseVal[0];
				var fileName = "";	
				fileName = arrResponseVal[1];
				var hashValuePLAIN = "";
				hashValuePLAIN = arrResponseVal[2];	
				if(hashValue != "" && fileName != ""){																									
					$("#txtAreaHashValue").val(hashValue);						
					var clickValENC = "<button class='btn btn-primary' type='button' onClick=\"download_ccd_export('"+fileName+"');\">Download Encrypted CCD</button>";
					$("#linkToDownloadENC").html(clickValENC);
					$("#txtAreaHashValuePLAIN").val(hashValuePLAIN);										
					var default_file_nm = btoa('liaka_ccd.xml');
					var clickValPlain = "<button class='btn btn-primary' type='button' onClick=\"download_ccd_export('"+default_file_nm+"');\">Download Plain CCD</button>";
					$("#linkToDownloadPLAIN").html(clickValPlain);
				}	
			}
			else if(parseInt(arrResponseValMain[1]) == 0){							
				var responseVal = arrResponseValMain[0];						
				var arrResponseVal = responseVal.split("~~");														
				if(parseInt(arrResponseVal[0]) == 1 && parseInt(arrResponseVal[1]) == 1 && parseInt(arrResponseVal[2]) == 1 && parseInt(arrResponseVal[3]) == 1 && $('#patientInfoAll').is(':checked') === false && $('#cbkMedHx').is(':checked') === false){
					msg = msg + ' - Medical History. \n'; 	
				}
				else{
					if(parseInt(arrResponseVal[0]) == 1){
						msg = msg + ' - Patient Allergies. \n'; 	
					}
					if(parseInt(arrResponseVal[1]) == 1){
						msg = msg + ' - Patient Medication. \n'; 	
					}
					if(parseInt(arrResponseVal[2]) == 1){
						msg = msg + ' - Patient Problems. \n';
					}
					if(parseInt(arrResponseVal[3]) == 1){
						msg = msg + ' - Patient Labs(Result). \n';
					}
				}
				validate = false;
			}
			
			if(validate === true){
				$("#trElectronicData").show();					
				$("#div_loading_image").hide();
			}
			else{
				$("#div_loading_image").hide();	
				fAlert(msg);
				msg = '';
			}	
		}			
	}
}

function get_options_string(){
		var arrOption = new Array;
		$('[name^=chart_exclusion]').each(function(id,elem){
			if($(elem).is(':checked')){	
				var elem_val = $(elem).val();
				switch(elem_val){
					case "mu_data_set":
					arrOption[arrOption.length] = "mu_data_set";
					break;
					case "dos_facility":
					arrOption[arrOption.length] = "location_info";
					break;
					case "cc_and_history":
					arrOption[arrOption.length] = "reason_for_visit";
					break;
					case "referrals_to_other_providers":
					arrOption[arrOption.length] = "provider_referrals";
					break;
					case "visit_medication_immu":
					arrOption[arrOption.length] = "visit_medication_immu";
					break;
					case "diagnostic_tests_pending":
					arrOption[arrOption.length] = "diagnostic_tests_pending";
					break;
					case "clinical_instructions":
					arrOption[arrOption.length] = "clinical_instruc";
					break;
					case "future_sch_test":
					arrOption[arrOption.length] = "future_sch_test";
					break;
					case "future_appt":
					arrOption[arrOption.length] = "future_appointment";
					break;
					case "recommended_patient_decision_aids":
					arrOption[arrOption.length] = "recommended_patient_decision_aids";
					break;
					case "provider_name_and_contact":
					arrOption[arrOption.length] = "provider_info";
					break;
					default:
					arrOption[arrOption.length] = elem_val;
					break
				}	
			}
		});
		return arrOption;	
		
	}

function getElectronicDataWV(patId,obj){
	var form_id = $("#formIdToPrint").val()
	var strOptions = get_options_string();
	var strFormIds = $('#formIdToPrint').val().toString();
	enc_key = $('#enc_key').val();
	if(enc_key == ""){
		top.fAlert("Enter a key to continue");
		$('#div_enckey').modal('show');
		return false;
	}
	else if(enc_key.length < 16 || enc_key.length > 16){
		top.fAlert("Encryption key should be of 16 characters")
		return;
	}
	$('#div_enckey').modal('hide');
	arrFormId = strFormIds.split(',');
	arrTmp = {};
	for(i=0; i<arrFormId.length;i++){
		arrData = {};
		arrData.pat_id = patId;
		arrData.form_id = arrFormId[i];
		arrTmp[i] = arrData;
	}
	
	arrMed = new Array();
				$("input[name='pt_med_exclusion[]']:checked").each(function(index, element) {
                    arrMed[index] = $(this).val();
                });
	medications = arrMed.join('~~');
	arrAller =  new Array();
				$("input[name='pt_allergy_exclusion[]']:checked").each(function(index, element) {
                    arrAller[index] = $(this).val();
                });
	allergies = arrAller.join('~~');
	arrProblem = new Array();
				$("input[name='pt_prob_exclusion[]']:checked").each(function(index, element) {
                    arrProblem[arrProblem.length] = $(this).val();
                });
	problem_list = arrProblem.join('~~');	
	var url = window.opener.top.JS_WEB_ROOT_PATH+'/interface/reports/ccd/create_ccda_r2_xml.php';
	$.ajax({
			url:url,
			data:'arrData='+JSON.stringify(arrTmp)+'&ccdDocumentOptions='+strOptions+'&enc_key='+enc_key+'&create_type=printing'+'&medications='+medications+'&allergies='+allergies+'&problem_list='+problem_list,
			type:'POST',
			success:function(respData){
				var resultData = respData;
				arrResp = resultData.split('~~')
				$('#linkToDownloadENC').html(arrResp[0])
				$('#txtAreaHashValue').val(arrResp[1])
				$('#enc_key').val('');
			}
	});	
	$("#div_loading_image").hide();		
}

function download_ccd_export(filename,type){
	var url = window.opener.top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/top_btn_ajax_handler.php?download_ccd=yes&filename='+filename+'&type='+type;
	window.location = url;
}

function sendFaxFun(){
	$('#content_box').find('iframe').remove(); //CODE ADDED TO REMOVE PRINT PDF IFRAME TO AVOID GETTING HIDDEN fALERTS ANYMORE
	$('#send_fax_div').modal('show');
	var getFaxNo = $('#send_fax_number').val();
	$('#send_fax_div #send_close_btn').click(function(){
		if($('#send_fax_number').val()==''){
			fAlert('Please enter fax number');
			$('#send_fax_number').focus();
			return false;
		}else{
			document.print_function.faxSubmit.value=1;
			document.print_function.target='hiddIframe1';
			document.print_function.submit();
			$("#div_loading_image").show();
		}
	});
}
function ResetFaxStatus(msg){
	document.print_function.faxSubmit.value=0;
	$('#send_fax_number').val('');
	$('#send_fax_div').modal('hide');
	document.print_function.target='';
	if(msg!=''){
		fAlert(msg);
	}
}

function setFaxNumber(faxObj){
	var obj = faxObj;
	var getRefPhyDetail="";
	var getRefPhyFax="";
	var	getRefPhyName="";
	var obj_value = obj.val();
	var getObjVal = arrRefIdNameFax[obj_value];
	if(obj_value!="" && obj_value!="-" && typeof(getObjVal)!='undefined'){
		getRefPhyDetail=getObjVal.split("@@");
		getRefPhyID=getRefPhyDetail[0];
		getRefPhyFAX=getRefPhyDetail[1];
		if(getRefPhyFAX){
			$("#send_fax_number").val(getRefPhyFAX);	
		}else{
			$("#send_fax_number").val('');
		}
	}else{
		$("#send_fax_number").val();
	}
}


$(function() {
	checkAll(true);
	//$('#chkChartNotes').prop('checked', true);
	
	//Setting popup dimensions based on the resolution
	/* if(typeof(window.opener.top.innerDim)=='function'){
		var innerDim = window.opener.top.innerDim();
		if(innerDim['w'] > 1600) innerDim['w'] = 1600;
		if(innerDim['h'] > 900) innerDim['h'] = 900;
		window.resizeTo(innerDim['w'],innerDim['h']);
		brows	= get_browser();
		if(brows!='ie') innerDim['h'] = innerDim['h']-35;
		var result_div_height = innerDim['h']-210;
		$('.resultset_div').height(result_div_height+'px');
	} */
	
	$("#chkMedicationList, #chkOcuMeds, #chkSysMeds").click(function(){
			if($('#chkOcuMeds').is(':checked') || $('#chkSysMeds').is(':checked')){
				if($('#chkMedicationList').is(':checked'))
				$('#chkMedicationList').prop('checked',false);
			}
			if($('#chkMedicationList').is(':checked')){
				$('#chkOcuMeds').prop('checked',false);
				$('#chkSysMeds').prop('checked',false);
			}
	});
	
	//Setting Typeahead
		$('#selectReferringPhy').typeahead({
			source: global_php_var.typeahead_arr['stringAllPhy'],
			displayField: 'name',
			valueField: 'id',
			onSelect: function(obj) {
				//Returns obj.text => selected name, obj.value => id of selected name	
				$('#hiddselectReferringPhy').val(obj.value);
				if($('#hiddselectReferringPhy').length > 0){
					var fax_object = $('#hiddselectReferringPhy');
					setFaxNumber(fax_object);
				}	
			}
		});
		
		$('#DisclosedTo').typeahead({source:arr_ref_phy_name});
	
	/*Check Active Only for Summary*/
	$(':checkbox.checkActive').on('change', function(){
		
		var radioName = $(this).attr('linked-radio');
		var radioActiveAll = $(':radio[name="'+radioName+'"]');
			
		if( $(radioActiveAll).length <= 0)
			return false;
		
		if( $(this).is(':checked') === true )
		{
			$(radioActiveAll).prop({'disabled': false, 'checked':false});
			$(radioActiveAll[0]).prop('checked', true);
		}
		else
			$(radioActiveAll).prop({'checked':false, 'disabled': true});
	});
});