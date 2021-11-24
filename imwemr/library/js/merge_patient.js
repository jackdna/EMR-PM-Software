//array for field in html
var fields_arr = ['dob_patient','contact_patient','address_patient','primary_ins_patient','secondary_ins_patient','pt_fac_patient','pat_last_appt','pat_last_appt_proc','pat_last_appt_comment','pat_outStanding_patient','pat_ins_outStanding_patient'];

window.onerror = function(msg, page, line)
{
	var str = "Exception = "+ msg;
	str += "\n\nPage = "+page;
	str += "\n\nLine = "+line;
	top.fAlert(str);
}
var id_number;
function searchPatient(id_num){
	$("#div_loading_image").show();
	id_number = id_num;
	var patient_name_object = $("#merge_patient_name"+id_num);
	var find_by_object = $("#findBy"+id_num);
	
	var name = patient_name_object.val();
	var findBy = find_by_object.val();
	
	var msg = "";
	if(name == ""){
		msg = "Please enter last name to search\n";
	}
	if(findBy == ""){
		msg += "Please select search criterion from drop down";	
	}
	
	if(msg){
		top.fAlert(msg);
		$.each(fields_arr,function(id,val){
			$("#"+val+id_num).html("");
		});	
		$('#merge_patient_id'+id_num).val('');	
		$("#div_loading_image").hide();
	}else{
		if(isNaN(name)){
			var data = 	'val='+$.trim(name)+'&fld='+findBy;
			$.ajax({
				url:window.opener.top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/top_btn_ajax_handler.php',
				type:'POST',
				data:data,
				success:function(response){
					if(response != ''){
						show_modal('show_search_pat','Select Patient',response,'','','modal-lg');
					}else{
						$.each(fields_arr,function(id,val){
							$("#"+val+id_num).html("");
						});
						$('#merge_patient_id'+id_num).val('');	
						fAlert('No patient found');	
					}
				}
			});
		}else{
			getPatientName(name, id_num);
		}
	}
	$("#div_loading_image").hide();
	return false;
}
function searchPatientonPressEnter(e,obj){
	if(e.keyCode=="13"){
		obj.blur();
	}
}

function getName(name,id){
	if(id){
		getvalue(name,id);
	}
	else{
		top.fAlert('No Patient Found.');
	}	
}

function getvalue(name,id){
	var merge_patient_name = $("#merge_patient_name"+id_number);
	var merge_patient_id = $("#merge_patient_id"+id_number);
	getPatientName(id, id_number,name);
}

function getPatientName(id, id_num,name){	
id_number = id_num;
	if(id>0){
		var data = 'id='+id+'&get_pat_name=yes';	
		$.ajax({
			url:window.opener.top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/top_btn_ajax_handler.php',
			type:'POST',
			data:data,
			success:function(response){
				var result = $.parseJSON(response);
				if(result.full_name){
					$("#dob_patient"+id_num+"_label").html("D.O.B.");
					$("#contact_patient"+id_num+"_label").html("Contact No.");
					$("#address_patient"+id_num+"_label").html("Address");
					$("#primary_ins_patient"+id_num+"_label").html("Primary Ins.");
					$("#secondary_ins_patient"+id_num+"_label").html("Secondary Ins.");
					$("#pt_fac_patient"+id_num+"_label").html("Facility");
					$("#pat_last_appt"+id_num+"_label").html("Last Appointment on");
					$("#pat_last_appt_proc"+id_num+"_label").html("Procedure");
					$("#pat_last_appt_comment"+id_num+"_label").html("Comments");
					$("#pat_outStanding_patient"+id_num+"_label").html("Patient outstanding ");
					$("#pat_ins_outStanding_patient"+id_num+"_label").html("Insurance outstanding");
					
					$.each(fields_arr,function(id,val){
						$("#"+val+id_num).html("");
					});								
					
					$("#merge_patient_name" + id_num).val(result.full_name);
					$("#merge_patient_id"+id_num).val(result.id);
					
					if(result.dob){
						$("#dob_patient"+id_num).html(result.dob);
					}
					
					if(result.phone_home){
						$("#contact_patient"+id_num).html(result.phone_home);
					}
					
					if(result.address){
						$("#address_patient"+id_num).html(result.address);
					}
					
					if(result.patientFacility){
						$("#pt_fac_patient"+id_num).html(result.patientFacility);
					}
					
					if(result.ptLastAddtDate){
						$("#pat_last_appt"+id_num).html(result.ptLastAddtDate+" at "+result.ptLastAddtTime);
					}
					
					if(result.ptLastAddtProcedure){
						$("#pat_last_appt_proc"+id_num).html(result.ptLastAddtProcedure);
					}
					
					if(result.ptLastAddtComments){
						$("#pat_last_appt_comment"+id_num).html(result.ptLastAddtComments);
					}
					
					if(result.insPriType){
						$("#primary_ins_patient"+id_num).html(result.insPriCompName+'-'+result.insPriPolicyNo);
					}
					
					if(result.insSecType){
						$("#secondary_ins_patient"+id_num).html(result.insSecCompName+"-"+result.insSecPolicyNo);
					}
					
					if(result.pat_Due){
						$("#pat_outStanding_patient"+id_num).html(result.pat_Due);
					}
					
					if(result.insurance_Due){
						$("#pat_ins_outStanding_patient"+id_num).html(result.insurance_Due);
					}
					
					if(result.intMedHxPerform){
						$("#medHXStatusPatient"+id_num).val(result.intMedHxPerform);	
					}
					
					if(result.patinetAccountTransaction){
						$("#patinetAccountTransactionStatus"+id_num).val(result.patinetAccountTransaction);	
					}
					$('#show_search_pat').modal('hide');
				}else{
					fAlert("Patient does not exists.");
				}
			}
		});
	}
	else{	
		$("#merge_patient_name"+id_num).val("");				
		fAlert("Please enter Patient ID or Name.");	
	}
}


function merge_patient(object, patient1_id, patient1_name, patient2_id,direction){				
	var obj1 = $("#merge_patient_name1");
	var obj2 = $("#merge_patient_name2");
	var pt1_name = $('#'+patient1_name).val();
	var pt1_id = parseInt($('#'+patient1_id).val());
	var pt2_id = parseInt($('#'+patient2_id).val());
	if(obj1.val() == "" || obj2.val() == ""){
		fAlert("Please select two different patients.");
	}			
	else{
		if(pt1_id == pt2_id){
			fAlert("Patient cannot be merged to himself or herself.");
		}
		else if(pt1_id == 0){
			fAlert("Please Select Patient 1.");
		}
		else if(pt2_id == 0){
			fAlert("Please Select Patient 2.");
		}
		else{
			var mergeAlert = "";
			if(direction == "LTR"){
				var this_patient_name = pt1_name;
				var merge_patient_name = obj1.val();
				$("#this_patient_name").val(pt1_name);
				$('#this_patient_id').val(pt1_id);
				$("#merge_patient_id").val(pt2_id);						
				if($('#patinetAccountTransactionStatus1').val() == "1"){							
					mergeAlert += "<li>Patient has account transaction.  This will not be merged</li>";
				}
				if($('#medHXStatusPatient1').val() == "1"){							
					mergeAlert += "<li>All clinical information will be merged</li>";
				}
			}
			else if(direction == "RTL"){
				var this_patient_name = pt1_name;
				var merge_patient_name = obj2.val();
				$("#this_patient_name").val(pt1_name);
				$('#this_patient_id').val(pt1_id);
				$("#merge_patient_id").val(pt2_id);						
				if($('#patinetAccountTransactionStatus2').val() == "1"){							
					mergeAlert += "<li>Patient has Account transaction.  This will not be merged</li>";
				}
				if($('#medHXStatusPatient2').val() == "1"){							
					mergeAlert += "<li>All clinical information will be merged</li>";
				}
			}
			if (mergeAlert!="") {
				mergeAlert = "<ul>"+mergeAlert+"</ul>";
			}
			mergeAlert += "<div style='text-align:center;font-weight:bold'>Are you sure, you want to proceed?</div>"
			fancyConfirm(mergeAlert,'Merging patient warning(s)',"merge('yes')");
			
		}
	}
}

function merge(op){
	if(op == "yes"){
		var content = "<div class='row'><div class='col-sm-12 pt10'><div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'></div></div></div><div id='merge_status' class='col-sm-12 text-center'><b class='text-danger'>Please dont close or refresh the window till the process is completed</b></div></div>";
		show_modal('merge_pat_details','Merging Patient Data',content,'','','','','false');
		$('#merge_pat_details .modal-footer .btn').hide();
		var form_data = $('#frm').serialize();
		form_data += '&merge_patients=yes';
		$.ajax({
			url:window.opener.top.JS_WEB_ROOT_PATH+'/interface/patient_info/ajax/top_btn_ajax_handler.php',
			type:'POST',
			data:form_data,
			success:function(response){
				if(response != ''){
					var result = $.parseJSON(response);
					$('#merge_pat_details .progress').slideUp('fast');
					if(result.err_msg){
						$('#merge_pat_details .progress-bar').slideUp('fast');
						$('#merge_status').html(result.err_msg);
					}else{
						$('#merge_status').html('<b class="text-success">Records has been merged into '+result.new_name+' records.</b>');
						$('#merge_pat_details .modal-footer .btn').show();
					}
					setTimeout(function(){ $('#merge_pat_details').modal('hide'); }, 3000);
					$('#merge_pat_details').on('hidden.bs.modal', function () {
						window.location.reload();
						window.opener.top.fmain.location.reload();
					});
				}
				
			}
		});
	}
	else if(op == "no"){
		$("#margeAlert").hide();
	}
	$("#div_loading_image").hide();
}




/*	
function img_display(val){
	document.getElementById("load_image21").style.display = val;
}

function merge(op){
	if(op == "yes"){
		document.getElementById("margeAlert").style.display = "none";
		document.getElementById("camcelButton").style.display = "none";					
		var obj = document.getElementById("merge_patient_form");
		obj.style.display = "none";
		var obj = document.getElementById("loader");
		obj.style.display = "block";
		document.frm.submit();
	}
	else if(op == "no"){
		document.getElementById("margeAlert").style.display = "none";
	}
}
function close_window(authId){				
	if(authId != ""){
		window.opener.top.fmain.refresh_last_five(authId);
		window.close();
	}
} */