//Success/Error msg
function showError(msg,type,alert){
	if(msg == '') return ;
	if(type == '') type = 'error';
	
	var elem = $('#alertDiv');
	var opp_elem  = $('#successDiv');
	if(type == 'success'){
		elem = $('#successDiv');
		opp_elem = $('#alertDiv');
	}
	
	if(msg.length && msg != ''){
		if(elem.hasClass('hide') == true){
			elem.find('span').html(msg);
			elem.removeClass('hide');
		}else{
			elem.find('span').html(msg);
		}
		
		if(opp_elem.hasClass('hide') == false){
			opp_elem.find('span').html('');
			opp_elem.addClass('hide');
		}
	}else{
		if(elem.hasClass('hide') == false){
			elem.addClass('hide');
		}
		if(opp_elem.hasClass('hide') == false){
			opp_elem.find('span').html('');
			opp_elem.addClass('hide');
		}
	}
	if(alert) fAlert(msg);
}

//Check Zip
function checkZip(ths){
	top.show_loading_image('show');
	var file = $(ths).prop('files')[0];
	if(file){
		var fName = file.name;
		var fSize = file.size;
		var fType = file.type;
		
		if(fName != '' && fSize > 0 && (  fType.indexOf('zip') != -1 || fType.indexOf('xml') != -1)){
			showError('Valid File','success');	
			$('#uploadZip').prop('disabled',false);
			$('#taskVal').val('uploadZip');
		}else{
			showError('Invalid File','');
			$('#uploadZip').prop('disabled',true);
			$('#taskVal').val('checkZip');
		}
	}
	
	
	top.show_loading_image('hide');
	return false;
}

//Validating selected patients
function checkPatients(){
	//Disable Import button
	if(phpArr.arrUnique){
		var counter = 0;
		$.each(phpArr.arrUnique, function(id,val){
			if(id) counter++;
		});
		
		if(counter > 0){
			fAlert('Some duplicate patients exists !. Please verify them first');
			//handleButtons(true);
			return false;
		}
		//showError(phpArr.Error, '');
	}
	
	if($.trim(phpArr.zipName) == ''){
		showError('Unable to proceed. Please refresh the page and uplaod file again');
		$('#checkAll').trigger('click');
		return false;
	}
	
	var msg = '';
	if($('#providerId').val() == ''){
		msg += 'Provider ';
	}
	
	if($('#facilityId').val() == ''){
		if(msg != ''){
			msg += ', Facility';
		}else{
			msg = 'Facility ';
		}
	}
	
	if(msg != ''){
		msg = 'Please select - '+msg;
		showError(msg, '', 'alert');
		return false;
	}
	$('#page_buttons #import_pt', top.document).prop('disabled',true);
	phpArr.checkedBoxes = $('.chkPtBox:checked', top.fmain.document).length;
	top.show_loading_image('show');
	$('.chkPtBox:checked', top.fmain.document).each(function(id,elem){
		var data = $(elem).data();
		var ptNm = data.nm;
		var elemId = data.elem;
		var fileName = data.filename;
		importPatients($(elem), ptNm, elemId, fileName);
	});
	$('#checkAll').trigger('click').remove();
}

//Start Importaing Patients
function importPatients(ths, ptNm, elemId, fileName){
	var zipName = phpArr.zipName;
	var providerId = $('#providerId').val();
	var facId = $('#facilityId').val();
	var ptId = $(ths).val();
	var status =  false;
	var elem = $(ths).parent().parent();
	
	if(ptId != '' && zipName != ''){
		var dataStr = 'zipName='+zipName+'&pt_id='+ptId+'&task=importPatient&provId='+providerId+'&facilityId='+facId+'&ptNm='+ptNm+'&fileName='+fileName;
		
		//Starting Ajax request
		$.ajax({
			url:phpArr.ajaxUrl,
			data:dataStr,
			type:'POST',
			dataType:'JSON',
			beforeSend:function(){
				//top.show_loading_image('show','Please wait...');
			},success:function(response){
				status = response.status;
				elem.empty();
				if(status == true){
					elem.html('<span class="glyphicon glyphicon-ok-sign text-green font-18 " disabled></span>');
					
					phpArr.globCounter++;
					
					if(phpArr.globCounter == phpArr.checkedBoxes){
						top.show_loading_image('hide');
						top.fAlert('All Patients Imported');
						$('#page_buttons #import_pt', top.document).prop('disabled',true);
						
					}
				}else{
					elem.html('<span class="glyphicon glyphicon-remove-sign text-red" disabled></span>');	
				}
				
			},
			complete:function(resp){
				//top.show_loading_image('hide');
			}
		});
	}
	return status;
}



// Disable/enable buttons based on selection
function handleButtons(status){
	//Disable buttons if no check boxs are selected
	if(status){
		$('#page_buttons #import_pt', top.document).prop('disabled',status);
	}else{
		if($('.chkPtBox:checked', top.fmain.document).length == 0){
			$('#page_buttons #import_pt', top.document).prop('disabled',true);
		}else{
			$('#page_buttons #import_pt', top.document).prop('disabled',false);
		}
	}
	
}

function resetImport(){
	window.location.href =  phpArr.webRoot+'/interface/reports/cqm_import_2020/index.php';
}

function setPtDemo(ths){
	var dtArr = $(ths).data();
	
	var ptId = dtArr.id;
	var uniqueId = dtArr.unique;
	
	//Unique Array
	var uniArr = phpArr.arrUnique;
	//Unique ID Loop
	$.each(uniArr,function(id, val){
		if(id == uniqueId){
			//Patient Id loop
			$.each(val, function(pId, PVal){
				//Remove wrong matched patient
				if(PVal != ptId){
					delPat(PVal);
				}else if(PVal == ptId){
					//Make this patient correct matched one 
					var ptElem = $('#pt_'+PVal);
					if(ptElem.hasClass('panel-danger') == true){
						ptElem.removeClass('panel-danger');
					}
					
					if(ptElem.hasClass('panel-default') == false){
						ptElem.addClass('panel-default');
					}
					
					//Enable Checkbox
					ptElem.find('#checkBox_'+PVal).prop('disabled', false);
					ptElem.find('#checkBox_'+PVal).prop('checked', true);
					$(ths).addClass('hide');
					
					//Remove Duplicate Id 
					ptElem.find('.same_doc_id').addClass('hide');
				}
			});
			delete phpArr.arrUnique[id];
		}
	});
	//console.log(phpArr.arrUnique);
}

function delPat(id){
	var dataStr = 'pt_id='+id+'&task=delPt';
	$.ajax({
		url:phpArr.ajaxUrl,
		data:dataStr,
		type:'POST',
		dataType:'JSON',
		beforeSend:function(){
			top.show_loading_image('show');
		},
		success:function(response){
			if(response > 0) $('#pt_'+id).remove();
			else fAlert('Some error occurred. Please try again !');
		},
		complete:function(){
			top.show_loading_image('hide');
		}
	});
}

$(function(){
	//Init. Bootstrap Tooltip
	$('[data-toggle="tooltip"]').tooltip(); 
	
	//If success msg exists - show success msg
	if(phpArr.Status && phpArr.Status != ''){
		showError(phpArr.Status, 'success');
	}
	
	//If error msg exists - show error msg
	if(phpArr.Error && phpArr.Error != ''){
		showError(phpArr.Error, '');
	}
	
	//If jsButton exists - show functions buttons
	if(phpArr.jsButton && phpArr.jsButton != ''){
		top.btn_show('ADMN', phpArr.jsButton);
	}else{
		top.btn_show('ADMN', []);
	}
	
	//Check/uncheck all
	$("#checkAll").click(function(){
		$('input:checkbox.chkPtBox').prop('checked', this.checked);
		handleButtons();
	});
	
	//Handle buttons on checkbox change
	$('input:checkbox.chkPtBox').on('change',function(){
		handleButtons();
	});
	
	top.show_loading_image('hide');
});


