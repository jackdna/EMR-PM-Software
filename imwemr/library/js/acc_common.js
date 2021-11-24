
function ajax_fun(action_type,ajax_fun_ex_arg){
	if(action_type!=""){
		var url="acc_ajax.php?action_type="+action_type+"&"+ajax_fun_ex_arg;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				resp = jQuery.parseJSON(resp);
				if(typeof(resp.current_caseid)!= "undefined"){
					var current_caseid = resp.current_caseid;
					if(current_caseid<=0){
						top.fAlert("No Insurance case exists for this patient.");
					}
				}
				
				if(typeof(resp.pay_comm)!= "undefined"){
					var pay_comm_imp =resp.pay_comm;
					if(pay_comm_imp!="" && typeof(pay_comm_imp) !="undefined"){
						var title ='Scheduled Alert';
						top.fancyAlert(pay_comm_imp,title, '', '', 'Done', 'Close', "window.top.fmain.pay_comm_close('done');", '{}','','',600);
					}
				}
				
				if(typeof(resp.status_name)!= "undefined"){
					var status_name =resp.status_name;
					if(status_name!=""){
						top.fAlert("Patient account status is <font color='#ff0000'><b>"+status_name+"</b></font>.");
					}
				}
				
				if(typeof(resp.notes)!= "undefined"){
					var msg=resp.notes;
					if(msg!=""){
						var title="Accounting Comments";
						var btn1="OK";
						var btn2="";
						top.fAlert(msg,title);
					}
				}
				
				if(typeof(resp.poe)!= "undefined"){
					var poe=resp.poe;
					if(poe!="" && poe!=null){
						//top.fAlert(poe,title);
						$("body").append(""+poe);
						if(poe.indexOf("hidden")==-1){ $("#poeModal").modal('show');  }
					}
				}
				
				/*if(typeof(resp.adm_alt)!= "undefined"){
					var adm_alt=resp.adm_alt;
					if(adm_alt!="" && adm_alt!=null){
						document.getElementById('adm_div').innerHTML=adm_alt;
					}
				}
				
				if(typeof(resp.pat_spec_alt)!= "undefined"){
					var pat_spec_alt=resp.pat_spec_alt;
					if(pat_spec_alt!="" && pat_spec_alt!=null){
						document.getElementById('pat_spec_div').innerHTML=pat_spec_alt;
					}
				}*/
				
				if(typeof(resp.prev_stmt_butt)!= "undefined"){
					var prev_stmt_butt = resp.prev_stmt_butt;
					if(prev_stmt_butt>0){
						top.$("#statement").addClass("active");
					}
				}
				
				if(typeof(resp.pay_comm_butt)!= "undefined"){
					var pay_comm_butt = resp.pay_comm_butt;
					if(pay_comm_butt>0){
						top.$("#notes").addClass("active");
					}
				}
				
				if(typeof(resp.ins_desc)!= "undefined"){
					var ins_desc = resp.ins_desc;
					if(ins_desc!=""){
						var title ='Insurance Description';
						top.fAlert(ins_desc,title);
					}
				}
				
				if(typeof(resp.pat_notes)!= "undefined"){
					var pat_notes = resp.pat_notes;
					if(pat_notes!=""){
						var title ='Patient Notes';
						top.fAlert("<div style=\"max-height:300px;overflow-y:scroll;\">"+pat_notes+"</div>",title);
					}
				}
				
				if(typeof(resp.ass_plan_rg)!= "undefined"){
					var ass_plan_rg = resp.ass_plan_rg;
					$('#assesMentTemp').html(ass_plan_rg);
				}
				
				if(typeof(resp.ci_pp_data)!= "undefined"){
					var ci_pp_data = resp.ci_pp_data;
					$('#ci_pp').html(ci_pp_data);
				}
				
				if(typeof(resp.un_post_amount)!= "undefined"){
					var un_post_amount = resp.un_post_amount;
					top.$('#acc_ci_unpost_pay_id').html(un_post_amount);
					if(un_post_amount!=""){
						top.$('#acc_ci_unpost_pay_id').show();
					}else{
						top.$('#acc_ci_unpost_pay_id').hide();
					}
					//document.getElementById('unpost_pay_id').innerHTML=un_post_amount;
				}
				
				if(typeof(resp.final_show_pre_amt)!= "undefined"){
					var final_show_pre_amt = resp.final_show_pre_amt;
					//document.getElementById('unpost_pre_pay_id').innerHTML=final_show_pre_amt;
					top.$('#acc_pp_unpost_pay_id').html(final_show_pre_amt);
					if(final_show_pre_amt!=""){
						top.$('#acc_pp_unpost_pay_id').show();
					}else{
						top.$('#acc_pp_unpost_pay_id').hide();
					}
				}
                
                top.$('.acc_ins_active', top.document).html('');
                if(typeof(resp.pri_sec_active_ins)!= "undefined"){
					var active_insurance = resp.pri_sec_active_ins;
					top.$('.acc_ins_active', top.document).html(active_insurance);
				}
			}
		});
	}
}

function pay_comm_close(val){
	if(val=="done"){
		$.ajax({
			url: "set_pat_session.php",
			success: function(resp){
			}
		});
	}
}

function get_accept_assignment(priInsId,enc){
	//"AA  - Courtesy Billing"
	//"NAA - Courtesy Billing"
	//"NAA - No Courtesy Billing"
	var enc_chk="";
	if(typeof(enc)!= "undefined"){
		enc_chk=enc;
	}
	if(enc_chk!=''){	
		if(priInsId==1){
			 top.$('#acc_accept_assignment_div').html("NAA - CB");
			 top.$('#acc_accept_assignment_div').attr('title', 'NAA - Courtesy Billing');
		}else if(priInsId==2){
			 top.$('#acc_accept_assignment_div').html("NAA - No CB");
			 top.$('#acc_accept_assignment_div').attr('title', 'NAA - No Courtesy Billing');
		}else{
			 top.$('#acc_accept_assignment_div').html("AA");
			 top.$('#acc_accept_assignment_div').attr('title', 'Accept Assignment');
		}
	}else{
		$.ajax({
			url: "get_ins_detail.php?insCompanyId="+priInsId,
			success: function(resp){
				if(resp){
					if(resp==1){
						 top.$('#acc_accept_assignment_div').html("NAA - CB");
						 top.$('#acc_accept_assignment_div').attr('title', 'NAA - Courtesy Billing');
					}else if(resp==2){
						 top.$('#acc_accept_assignment_div').html("NAA - No CB");
						 top.$('#acc_accept_assignment_div').attr('title', 'NAA - No Courtesy Billing');
					}else{
						 top.$('#acc_accept_assignment_div').html("AA");
						 top.$('#acc_accept_assignment_div').attr('title', 'Accept Assignment');
					}
				}			
			}
		});
	}
}

function show_ap_div(){
	$('#AssesmentDiv').modal('show');
}
function icd10_fun(){
	var old_enc_icd10 = $("#enc_icd10").val();
	if(old_enc_icd10>0){
		$("#enc_icd10").val(0);
	}else{
		$("#enc_icd10").val(1);
	}
	 set_icd10('yes');
}
function check_in_div(val){
	$('#patient_pre_payments_list').modal('hide');
	$('#check_in_out_list').modal('show');
	set_modal_height('check_in_out_list');
}
function patient_pre_payments_div(val){
	$('#check_in_out_list').modal('hide');
	$('#patient_pre_payments_list').modal('show');
	set_modal_height('patient_pre_payments_list');
}
function add_charge_list(id){
	top.show_loading_image("show","150");
	if($("#acc_view_chr_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	top.fmain.$("#post_action").val(id);
	$("#superbill_frm").submit();
}
function chk_superbill(type){
	if(type=="close"){
		document.getElementById('chk_dos_id').style.display='none';
	}else{
		var val = document.getElementById('chart_dos').value;
		if(val==""){
			alert("Please select the chart note DOS");
		}else{
			var id=-1;
			var str= val.split('--');
			var enc_id=str[0];
			var chartid=str[1];
			var winFeatures	= "top=0,left=20,width=1100,height=700,toolbar=no,menubar=no,scrollbars=no,statusbar=yes,resizable=yes";		
			chartid = (typeof chartid == "undefined") ? -1 : chartid;	
			top.popup_win("acc_superbill.php?id="+id+"&enc_id="+enc_id+"&form_id="+chartid,winFeatures);	
			document.getElementById('chk_dos_id').style.display='none';
		}
	}
}
function ajax_chart_dos_fun(action_type){
	if($("#acc_view_chr_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	top.show_loading_image("show","150");
	if(action_type=="chart_note_dos"){
		var url="chart_note_dos_ajax.php";
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				var butt = '<input type="button" name="done" value="Create SuperBill" class="btn btn-primary" id="done"  onClick="chk_superbill(\'done\');">';
				if(resp==""){
					resp = "There are no uncharged DOS pending.";
					butt = '';
				}
				butt += '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
				show_modal('chk_dos_id','Chart Note DOS',resp,butt);
				top.show_loading_image("hide");
			}
		});
	}
}
function edit_superbill(sup_id,enc_id){
	if($("#acc_view_chr_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	var winFeatures	= "top=0,left=20,width=1100,height=700,toolbar=no,menubar=no,scrollbars=no,statusbar=yes,resizable=yes";		
	top.popup_win("acc_superbill.php?id="+sup_id+"&enc_id="+enc_id,winFeatures);	
}
function open_superbill(val){
	if(val==1){
		val=0;
	}else{
		val=1;
	}
	window.location.href="superbill_charges.php?list_type="+val;
}
function chkSelection(){
	$(".chk_box_ccn_css").attr("checked",false);
	if($("#acc_view_chr_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	var flag=0;
	var len = document.charge_list_frm.elements.length;
	var has_trans=0;
	var show_enc_code = "0";
	var show_claim_num = "";
	var claim_ctrl_pri_val="";
	for(var i=0; i<len; i++){
		var chkStatus = document.charge_list_frm.elements[i].checked;
		if(chkStatus==true)
			flag=flag+1;
		if(document.charge_list_frm.elements[i].checked==true){
			var chl_id=document.charge_list_frm.elements[i].value;
			if(document.getElementById("chkbx_del_"+chl_id)){
				has_trans = has_trans+1;
				if(document.getElementById("chkbx"+chl_id)){
					if(document.getElementById("chkbx"+chl_id).value>0){
						show_enc_code += ", "+document.getElementById("chkbx_enc_del_"+chl_id).value;
					}
				}
			}
			
			if(document.getElementById("claim_ctrl_pri_"+chl_id)){
				if(document.getElementById("claim_ctrl_pri_"+chl_id).value!=""){
					claim_ctrl_pri_val=document.getElementById("claim_ctrl_pri_"+chl_id).value;
					show_claim_num += '<div class="checkbox"><input type="checkbox" name="cc_pri_'+chl_id+'" id="cc_pri_'+chl_id+'" onclick="top.fmain.chk_ccn('+chl_id+',this);"><label for="cc_pri_'+chl_id+'">Notify Payor – Claim # '+claim_ctrl_pri_val+'</label></div>';
				}
			}
		}	
	}
	if(flag==0){
		top.fAlert('Please select checkbox to void.');
	}else{
		var ask="";
		if(has_trans>0){
			var show_enc_code_str=show_enc_code.replace('0,','');
			ask +="Transaction has been made for Encounter(s) "+show_enc_code_str+".<br>";
		}
		ask += "Do you want to void all selected records?";
		if(show_claim_num!=""){
			ask +="<br>"+show_claim_num;
		}
		top.fancyConfirm(ask,'', "window.top.fmain.document.charge_list_frm.submit()","window.top.removeMessi()");	
	}
}

function chk_ccn(id,obj){
	var claim_ctrl_pri="claim_ctrl_pri_"+id;
	window.top.fmain.document.getElementById(claim_ctrl_pri).checked=obj.checked;
}
function chk_all(){
	if($('#chkbx_all').is(':checked')){
		$(".chk_box_css").prop("checked",true);
	}else{
		$(".chk_box_css").prop("checked",false);
	}
}

function checkCreteria(val){
	if(typeof(val)!="undefined" && val!=""){
		if(val=="all"){
			if($('#all_chrg').is(':checked')==true){
				$("#unpaid_chrg").attr("checked",false);
			}else{
				$("#unpaid_chrg").attr("checked",true);
			}
		}else if(val=="unpaid"){
			if($('#unpaid_chrg').is(':checked')==true){
				$("#all_chrg").attr("checked",false);
			}else{
				$("#all_chrg").attr("checked",true);
			}
		}
		$("#dosFromDate,#dosToDate,#inscase").val('');
		$("#eId").val('active');
		$(".selectpicker").selectpicker('refresh');
	}else{
		var dFrom = $("#dosFromDate").val();
		var dTo = $("#dosToDate").val();
		var caseType = $("#inscase").val();
		var eId = $("#eId").val();
		
		var dFrom_exp=dFrom.split('-');
		var dFrom_final=dFrom_exp[2]+dFrom_exp[0]+dFrom_exp[1];
		
		var dTo_exp=dTo.split('-');
		var dTo_final=dTo_exp[2]+dTo_exp[0]+dTo_exp[1];
		
		if(dFrom_final>dTo_final){
			top.fAlert("DOS From date should be greater than DOS To date.")
			return false;
		}
		
		if(dFrom!=""){
			if(dTo==""){
				top.fAlert("Please select both DOS From and DOS To.")
				return false;
			}
		}
		if(dTo!=""){
			if(dFrom==""){
				top.fAlert("Please select both DOS From and DOS To.")
				return false;
			}
		}
		
		if((dFrom=="") && (dTo=="") && (caseType=="") && (eId=="")){
			top.fAlert("Please select search criteria.")
			return false;
		}
	}
	document.dateOfServiceFrm.submit();
}

function show_auth_info(case_id,auth_name){
	var url="ajax_auth_info_div.php?auth_name="+auth_name+"&case_id="+case_id;
	$.ajax({
		url:url,
		type:'GET',
		data:'',
		success:function(response){
			show_modal('div_auth_id','Authorization Info', response);
		}	
	});
}

function save_recalls(){
	if($("#acc_view_chr_only").val()==2){
		view_only_acc_call(0);
		return false;
	}
	var proc_ids=$("#sel_proc_ids").val();
	var recall=$("#recall_month").val();
	if(proc_ids==""){
		top.fAlert("Please select or enter a  Procedure.");
		document.getElementById("sel_proc_ids").focus();
		return false;
	}else if(recall==""){
		top.fAlert("Please select a recall month.");
		document.getElementById("recall_month").focus();
		return false;
	}
	top.show_loading_image("show","150");
	$("#post_action").val("save");
	document.recal_Form.submit();
}
function del_enc_trans(del_id,enc_id,trans_mode,extra_id,cnfrm){
	if($("#acc_view_pay_only").val()==1  || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	if (typeof(cnfrm)=="undefined") {
		top.fancyConfirm('Are you sure you want to remove this record?','','top.fmain.del_enc_trans('+del_id+','+enc_id+',"'+trans_mode+'",'+extra_id+',true)');
		return;
	}
	else{
		window.location.href="../accounting/makePayment.php?del_id="+del_id+"&encounter_id="+enc_id+"&trans_mode="+trans_mode+"&extra_id="+extra_id;
	}
}
function edit_enc_trans(edit_id,enc_id,trans_mode,none_edit){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	if(none_edit>0){
		top.fAlert("This Write-Off cannot be edited from here, please change allowed amount to modify this write-off.");
		return false;
	}
	top.close_popwin('edit_trans');
	top.popup_win("../accounting/edit_trans.php?edit_id="+edit_id+'&enc_id='+enc_id+'&trans_mode='+trans_mode,'width=1025,height=600,top=35,left=0,scrollbars=yes,resizable=yes');
}
function OpenBalWin(eId){
	top.popup_win("../accounting/balance_view.php?eId="+eId,'width=850,height=610,top=50,left=75');
}

function print_post(){
	window.focus();
	window.print();
}
function doWhat(){
	if(document.reviewChargeFrm.allCharges.checked==true){
		document.reviewChargeFrm.dosFrom.value="";
		document.reviewChargeFrm.dosTo.value="";
		document.reviewChargeFrm.amount.value="";
		document.reviewChargeFrm.unPaid.checked=false;
		document.reviewChargeFrm.cashChk.checked=false;
		document.reviewChargeFrm.checkChk.checked=false;
		document.reviewChargeFrm.cCardChk.checked=false;
		document.reviewChargeFrm.etfChk.checked=false;
		document.reviewChargeFrm.submit();
	}
}
function unPaidShow(){
	if(document.reviewChargeFrm.unPaid.checked==true){
		document.reviewChargeFrm.amount.value="";
		document.reviewChargeFrm.dosFrom.value="";
		document.reviewChargeFrm.dosTo.value="";
		document.reviewChargeFrm.allCharges.checked=false;
		document.reviewChargeFrm.cashChk.checked=false;
		document.reviewChargeFrm.checkChk.checked=false;
		document.reviewChargeFrm.cCardChk.checked=false;
		document.reviewChargeFrm.etfChk.checked=false;		
	}
	document.reviewChargeFrm.submit();
}
function unselectEvery(){
	document.reviewChargeFrm.unPaid.checked=false;
	document.reviewChargeFrm.allCharges.checked=false;	
	document.reviewChargeFrm.cashChk.checked=false;
	document.reviewChargeFrm.checkChk.checked=false;
	document.reviewChargeFrm.cCardChk.checked=false;
	document.reviewChargeFrm.etfChk.checked=false;
	document.reviewChargeFrm.amount.value="";
	document.reviewChargeFrm.submit();
}
function checkCreteriaRev(){
	document.reviewChargeFrm.unPaid.checked = false;
	document.reviewChargeFrm.allCharges.checked=false;
	
	var objCash =  document.reviewChargeFrm.cashChk;
	var objChk =  document.reviewChargeFrm.checkChk;
	var objCC =  document.reviewChargeFrm.cCardChk;
	var objEtf =  document.reviewChargeFrm.etfChk;
		
	var dosF = document.reviewChargeFrm.dosFrom.value;
	var dosT = document.reviewChargeFrm.dosTo.value;
	
	var dosF_exp=dosF.split('-');
	var dosF_final=dosF_exp[2]+dosF_exp[0]+dosF_exp[1];
	
	var dosT_exp=dosT.split('-');
	var dosT_final=dosT_exp[2]+dosT_exp[0]+dosT_exp[1];
	//alert(dosF_final+-+dosT_final);
	
	var eId = document.reviewChargeFrm.encounterId_srh.value;
	var amount = document.reviewChargeFrm.amount.value;
	
	
	if((dosF=="") && (dosT=="") && (eId=="") && ((amount=="") || (amount=="Amount")) &&
	   (objCash.checked == false) && (objChk.checked == false) && (objCC.checked == false) && (objEtf.checked == false)){
		var msg = "<b>Please select</b><br>";
		msg = msg+"- Date From and Date To<br>";
		msg = msg+"- OR Encounter<br>";
		msg = msg+"- OR Amount<br>";
		msg = msg+"- OR Cash<br>";
		msg = msg+"- OR CC<br>";
		msg = msg+"- OR Ch<br>";
		msg = msg+"- OR ETF<br>";
		
		top.fAlert(msg)
		return false;
	}
	if(dosF_final>dosT_final){
		top.fAlert("Date From cannot be greater than Date To.")
		return false;
	}
	if(dosF!=""){
		if(dosT==""){
			top.fAlert("Please select both DOS From and DOS To.")
			return false;
		}
	}
	if(dosT!=""){
		if(dosF==""){
			top.fAlert("Please select both DOS From and DOS To.")
			return false;
		}
	}
	
	if((objCash.checked == true) || (objChk.checked == true) || (objCC.checked == true) || (objEtf.checked == true)){
		if((amount=="") || (amount=="Amount")){
			top.fAlert("Please enter any amount.")
			return false;
		}
	}
	document.reviewChargeFrm.submit();		
}
function uncheckAll(){
	document.getElementById("allCharges").checked=false;
}
function editPaymentList(eId,del_charge_list_id){
	top.show_loading_image("show",300);	
	window.location.href='../accounting/makePayment.php?encounter_id='+eId+'&del_charge_list_id='+del_charge_list_id;
}
function show_multi_proc(){
	if($("#proc_details").val()==""){
		$("#proc_details").val('1');
		var proc_lenth=document.getElementsByName("proc_details_row[]").length;
		for(i = 0 ; i < proc_lenth; i++){
			var proc_name = document.getElementsByName("proc_details_row[]");
			var td_id=proc_name[i].value;
			var collec = document.getElementById("td_proc_details_row_"+td_id);
			collec.style.display= 'table-row';
		}
		$('.era_rows').show();
	}else{
		$("#proc_details").val('');
		var proc_lenth=document.getElementsByName("proc_details_row[]").length;
		for(f = 0 ; f < proc_lenth; f++){
			var proc_name = document.getElementsByName("proc_details_row[]");
			var td_id=proc_name[f].value;
			var collec = document.getElementById("td_proc_details_row_"+td_id);
			collec.style.display= 'none';
		}
		$('.era_rows').hide();
	}
}
function show_enc_proc(val,enc){
	var chld_arr=val.split(',');
	if(chld_arr.length>1){
		for(i=0;i<chld_arr.length;i++){
			var td_id=chld_arr[i];
			if(document.getElementById("td_proc_details_row_"+td_id).style.display=='none'){
				var collec = document.getElementById("td_proc_details_row_"+td_id);
				collec.style.display= 'table-row';
			}else{
				var collec = document.getElementById("td_proc_details_row_"+td_id);
				collec.style.display= 'none';
			}
		}
	}
	var enc_id=enc;
	if(enc_id>0){
		var td_id=enc_id;
		if(document.getElementById("td_enc_era_row_"+td_id)){
			$('.era_row_'+td_id).toggle();
		}
	}
}

function getToolTip(id){
	if(id>0){
		var url="../patient_info/insurance/insuranceResult.php?dofrom=acc_reviewpt&id="+id;
		$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					document.getElementById('ins_show_div').innerHTML = resp;
				}
		});
		var curPos = getPosition();
		$('#ins_show_div').fadeIn();
		document.getElementById('ins_show_div').style.pixelTop = curPos.y;
		document.getElementById('ins_show_div').style.pixelLeft = curPos.x+10;
	}else{
		$('#ins_show_div').fadeOut()
	}
}

function show_right_div(val,pay_detail,pay_name,pay_price){

	if(event.button==2){
		document.oncontextmenu = function () { return false;  } 
		/* var left_set=400;
		var top_set=70;
		document.getElementById('pending_payment_div').style.display='block';
		document.getElementById('pending_payment_div').style.top=top_set+'px';
		document.getElementById('pending_payment_div').style.left=left_set+'px'; */
		document.getElementById('click_payment_id').value=val;
		document.getElementById('click_payment_detail_id').value=pay_detail;
		document.getElementById('click_rec').innerHTML=pay_name+' Payment : $'+pay_price;
		document.getElementById('click_rec_comm_td').style.display='none';
		var content = $('#pending_payment_div_content').html(); 
		show_modal('pending_payment_div','CI/CO Payments',content);
	}
}
function show_ppp_right_div(val,pay_price,pay_comm){

	if(event.button==2){
		document.oncontextmenu = function () { return false;  } 
		/* var left_set=400;
		var top_set=220;
		document.getElementById('pending_payment_div').style.display='block';
		document.getElementById('pending_payment_div').style.top=top_set+'px';
		document.getElementById('pending_payment_div').style.left=left_set+'px'; */
		$('#click_payment_id').val(val);
		$('#click_payment_type_id').val('ppp');
		$('#click_rec').html('Prepayments : $'+pay_price);
		$('#click_rec_comm').html('');
		if(pay_comm!=""){
			$('#click_rec_comm_td').show();
			$('#click_rec_comm').html(pay_comm);
		}else{
			$('#click_rec_comm_td').hide();
		}
		var content = $('#pending_payment_div_content').html(); 
		show_modal('pending_payment_div','Prepayments',content);
	}
}
function close_payment_div(){
	document.getElementById('pending_payment_div').style.display='none';
}
function process_payment(chld,encid,pay_amt,copay){
	if(document.getElementById("acc_view_pay_only").value==1){
		view_only_acc_call(0);
		return false;
	}
	var payment_id=document.getElementById('click_payment_id').value;
	var payment_detail_id=document.getElementById('click_payment_detail_id').value;
	var click_payment_type_id=document.getElementById('click_payment_type_id').value;
	close_payment_div();
	top.show_loading_image("show","150");
	var url = '';
	if(click_payment_type_id!=""){
		url = "apply_patient_pre_payment.php?chld="+chld+"&payment_id="+payment_id+"&encounter_id="+encid+"&pay_amt="+pay_amt+"&copay_chk="+copay;
	}else{
		url = "check_in_out_payment.php?chld="+chld+"&payment_id="+payment_id+"&payment_detail_id="+payment_detail_id+"&encounter_id="+encid+"&pay_amt="+pay_amt+"&copay_chk="+copay;
	}
	
	$.ajax({
		type:'GET',
		url:url,
		success:function(response){
			var result = response;
			result = trim(result);
			var len = result.length;
			if(click_payment_type_id!=""){
				if(result){
					if(parent.document.getElementById('unpost_pre_pay_id')){
						parent.document.getElementById('unpost_pre_pay_id').innerHTML=result;
					}
				}
				window.location.href="check_in_out_acc.php";
			}else{
				if(result){
					if(parent.document.getElementById('unpost_pay_id')){
						parent.document.getElementById('unpost_pay_id').innerHTML=result;
					}
				}
				window.location.href="check_in_out_acc.php?show_pay_trans_id="+payment_id;
			}
		}
	});
}

function show_payments_div(id,val){
	var details_id ="details_payment_"+id;
	if(val=='show'){
		if($('.'+details_id)){
			$('.'+details_id).removeClass('hide');
			$('#up_arrow_id_'+id).show();
			$('#down_arrow_id_'+id).hide();
		}
	}else{
		if($('.'+details_id)){
			$('.'+details_id).addClass('hide');
			$('#up_arrow_id_'+id).hide();
			$('#down_arrow_id_'+id).show();
		}
	}
}
function edit_acc_pay(pay_id,pay_detail_id,stop_mode){
	if(document.getElementById("acc_view_pay_only").value==1  || document.getElementById("acc_edit_financials").value==0){
		view_only_acc_call(0);
		return false;
	}
	if(stop_mode>0){
		top.fAlert("CI/CO Payment can not be edited as the transaction has been made");
		return false;
	}
	window.open('cico_payment_edit.php?edit_pay_id='+pay_id+'&edit_detail_id='+pay_detail_id,'EditCICOPayment','width=925,height=300,top=75,left=35,scrollbars=yes,resizable=yes');	
}
function del_acc_pay(pay_id,pay_detail_id,stop_mode){
	if(document.getElementById("acc_view_pay_only").value==1  || document.getElementById("acc_edit_financials").value==0){
		view_only_acc_call(0);
		return false;
	}
	if(stop_mode>0){
		top.fAlert("CI/CO Payment can not be voided as the transaction has been made");
		return false;
	}
	var ask = confirm("Void selected transaction !");
	if(ask==true){
		window.location.href='check_in_out_acc.php?del_pay_id='+pay_id+'&del_detail_id='+pay_detail_id+'&show_pay_trans_id='+pay_id;
	}
}
function del_pre_pay(pay_id,stop_mode){
	if(document.getElementById("acc_view_pay_only").value==1  || document.getElementById("acc_edit_financials").value==0){
		view_only_acc_call(0);
		return false;
	}
	if(stop_mode>0){
		top.fAlert("Patient Pre Payment can not be voided as the transaction has been made");
		return false;
	}
	var ask = confirm("Void selected transaction !");
	if(ask==true){
		window.location.href='check_in_out_acc.php?del_pre_pay_id='+pay_id;
	}
}
function edit_pre_pay(pay_id,stop_mode){
	if(document.getElementById("acc_view_pay_only").value==1  || document.getElementById("acc_edit_financials").value==0){
		view_only_acc_call(0);
		return false;
	}
	if(stop_mode>0){
		top.fAlert("Patient Pre Payment can not be edited as the transaction has been made");
		return false;
	}
	window.open('patient_pre_payment_edit.php?edit_pay_id='+pay_id,'EditPatientPrePayment','width=925,height=300,top=75,left=35,scrollbars=yes,resizable=yes');	
}
function save_chk_in_out(){
	if($("#acc_view_pay_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	if($('.chk_box_css:checked').length==0){
		top.fAlert('Please select checkbox to manual payment.');
	}else{
		top.fmain.$("#post_action").val('manual');
		document.manual_frm.submit();
	}
}
function refund_ci_co_pmt(){
	if($("#acc_view_pay_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	window.open('../accounting/refund_ci_co_pmt.php','EditCiCoPmt','width=925,height=640,top=75,left=35,scrollbars=yes,resizable=yes');	
}
function unapply_fun(pay_id,del_by){
	if(document.getElementById("acc_view_pay_only").value==1  || document.getElementById("acc_edit_financials").value==0){
		view_only_acc_call(0);
		return false;
	}

	var ask="";
	ask += "Are you sure to unapply the transaction?";
	top.fancyConfirm(ask,'', "window.top.fmain.document.location.href='../accounting/check_in_out_acc.php?del_pcpi_pay_id="+pay_id+"&del_by="+del_by+"'","return false");	
}
function getPosition(e) {
	e = window.event;
	var cursor = {x:0, y:0};
	var de = document.documentElement;
	var b = document.body;
	cursor.x = e.clientX + 
		(de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
	cursor.y = e.clientY + 
		(de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	return cursor;
}
function crt_dx_dropdown(cnt,sel_dx){
	var all_rec = new Array;
	if(typeof cnt =="undefined" || cnt==null){
		var cnt = '';
	}
	if(typeof sel_dx =="undefined" || sel_dx==null){
		var sel_dx = '';
	}
	$(".dx_box_12").each(function() {
		if($(this).val()!=""){
        	all_rec.push($(this).val());
		}else{
			all_rec.push('');
		}
    });
	
	//alert(all_rec);
	$("select.diagText_all_css").each(function(){
		var all_opt_data = "";
		var sel_val_arr=$(this).val();
		var sel_val_dx_id=this.id;
		if(typeof sel_val_arr !="undefined" && sel_val_arr!=null && sel_val_arr!=''){
			if(cnt>0){
				var sel_cnt=0;
			}else{
				var sel_cnt=4-(sel_val_arr.length);
			}
			for(x in all_rec){
				if(all_rec[x]!=""){
					var sel_opt="";
					var sel_val_arr_spt="";
					if(sel_val_arr.length>0){
						sel_val_arr_spt=sel_val_arr.join(',');
					}
					//var sel_val_arr_spt=sel_val_arr.split('**');
					var yy=parseInt(x)+1;
					var chk_sel_rec= all_rec[x]+'**'+yy;
					var chk_sel_rec_cnt= '**'+yy+',';
					if(sel_val_arr_spt!=""){
						sel_val_arr_spt = sel_val_arr_spt+',';
					}
					if(sel_val_arr_spt.indexOf(chk_sel_rec_cnt)>0){
						sel_opt="selected";
					}
					if(sel_val_dx_id=="diagText_all_1" && sel_cnt>0 && sel_opt==""){
						sel_opt="selected";
						sel_cnt=sel_cnt-1;
					}
					all_opt_data += '<option value="'+chk_sel_rec+'" '+sel_opt+'>'+all_rec[x]+'</option>';
				}
			}
		}else{
			for(x in all_rec){
				if(all_rec[x]!=""){
					var sel_opt="";
					var yy=parseInt(x)+1;
					var chk_sel_dx="diagText_all_"+cnt;
					if(all_rec[x]==sel_dx && sel_dx!="" && sel_val_dx_id==chk_sel_dx){
						sel_opt="selected";
					}
					if(sel_val_dx_id=="diagText_all_1" && yy<=4){
						sel_opt="selected";
					}
					all_opt_data += '<option value="'+all_rec[x]+'**'+yy+'" '+sel_opt+'>'+all_rec[x]+'</option>';
				}
			}
		}
		$(this).html(all_opt_data);
		$(this).selectpicker('refresh');
	});
}
function crt_dx_dropdown_icd10(){
	var all_rec = new Array;
	var all_rec_new = new Array;
	var icd10_popup=0;
	var eid = $('#enc_id_read').val();
	$(".old_dx_box_12").each(function() {
		if($(this).val()!=""){
        	all_rec.push($(this).val());
		}else{
			all_rec.push('');
		}
    });
	$(".dx_box_12").each(function() {
		if($(this).val()!=""){
        	all_rec_new.push($(this).val());
		}else{
			all_rec_new.push('');
		}
		if($(this).val().substr(($(this).val().length-1),1)=='-'){
			icd10_popup = 1;
		}
    });
	$("select.diagText_all_css").each(function(){
		var all_opt_data = "";
		var sel_val_arr=$(this).val();
		var sel_val_dx_id=this.id;
		if(typeof sel_val_arr !="undefined" && sel_val_arr!=null && sel_val_arr!=''){
			for(x in all_rec){
				if(all_rec[x]!=""){
					var sel_opt="";
					//var sel_val_arr_spt=sel_val_arr.split('**');
					var yy=parseInt(x)+1;
					var chk_sel_rec= all_rec[x]+'**'+yy;
					if($.inArray(chk_sel_rec,sel_val_arr)!=-1){
						sel_opt="selected";
					}
					var chk_sel_rec_new= all_rec_new[x]+'**'+yy;
					all_opt_data += '<option value="'+chk_sel_rec_new+'" '+sel_opt+'>'+all_rec_new[x]+'</option>';
				}
			}
		}
		$(this).html(all_opt_data);
		$(this).selectpicker('refresh');
	});
	$(".old_dx_box_12").each(function() {
		$(this).val('');
    });
	if(icd10_popup>0){
		top.popup_win("../../interface/accounting/edit_icd10.php?eid="+eid,'ICD10','width=840,height=620,top=10,left=30,scrollbars=yes');
	}
}

function sb_copy_dx_codes(){
	var copyvalues=[];
	$("select.diagText_all_css" ).each(function(id,elem){ 
		var id = this.id;
		var indx_proc = id.replace("diagText_all_","procedureText_");
		var val= $('#'+indx_proc).val();
		var indx = id.replace("diagText_all_","");
		if(indx == "1"){
			//get Dx values to copy	
			$(this).find('option:selected').each(function(){ 
			   copyvalues.push($(this).val());
			});
		}else{
			if(val==""){ 
			}else{
				if(copyvalues.length>0){
					$("select#diagText_all_"+indx+"").find("option").each(function(){
						if(copyvalues.indexOf($(this).val())!=-1 && ($(this).is('selected')===false)){
							$(this).prop("selected",true);
						}
					});		
				}
			}
			$("select[id=diagText_all_"+indx+"]").selectpicker("refresh");
		}
		chk_adm_dx(indx);
	});
}
function OpenPaymentWin(eId){
	if($("#acc_view_chr_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	var eId=$('#enc_id_read').val();
	if(eId==""){
		top.fAlert("No encounter is selected.");
	}else{
		top.popup_win("../../interface/accounting/quickPay.php?eId="+eId,'width=975,height=675,top=10,left=10,scrollbars=yes');
	}
}

function OpenAscStateWin(){
	var eId=$('#enc_id_read').val();
	if(eId==""){
		top.fAlert("No encounter is selected.");
	}else{
		var w=675;
		var h=275;
		var x= (screen.width/2)-(w/2);
		var y=(screen.height/2)-(h/2);
		top.popup_win("../../interface/reports/asc_state_result.php?eId="+eId,'width='+w+',height='+h+',top='+y+',left='+x+',scrollbars=yes');
	}
}
	
	function procFromSuperBill(){
		top.popup_win("getProcFromsuperBill.php",'','width=300,height=150,top=150,left=300');
	}
	
	
	
	function change_action(e_id){
		top.popup_win("receipt.php?e_id="+e_id,'','width=1000,height=600,top=50,left=50');
	}
	
	function reload_frm(val){
		top.change_main_Selection(top.document.getElementById(val));
	}
	
	function check(k,st){
		if($("#acc_view_chr_only").val()==1){
			view_only_acc_call(0);
			return false;
		}
		var display_order_num=1;
		$(".display_order_cls").each(function() {
			$(this).val(display_order_num);
			++display_order_num;
		});
		$('#post_chrg_chk').val(st);
		var posFacilityCode ="";
		if($('#posFacilityCode')){
			posFacilityCode = $('#posFacilityCode').val();
		}
		if ((!posFacilityCode) || $('#caseTypeText')){
			var insOrPat = $('#caseTypeText').val();	
			var msg = "<b>Please Enter the following fields.</b><br>";
			var flag=0;
			var alert_id="";
			if(!posFacilityCode){
				msg += "- POS Facility<br>";
				++flag; 
				if(alert_id==""){
					alert_id="posFacilityCode";
				}
			}
			
			f1=$('#caseTypeText').val();
			f3=$('#primaryInsText').val();
			f4=$('#st_date').val();
			f5=$('#end_date').val();
			f7=$('#procedureText_1').val();
			f11=$('#tosText').val();
			f12=$('#posText').val();
			f15=$('#dos').val();
			f16=$('#primary_provider_id').val();
		
			if(f1==""){ 
				msg+="- Case Type<br>"; ++flag; 
				if(alert_id==""){
					alert_id="caseTypeText";
				}
			}
			if(f15==""){
				msg+="- Date Of Service<br>"; ++flag;
				if(alert_id==""){
					alert_id="dos";
				}
			}
			if(insOrPat!='Self' && $('#chk_self').is(':checked')==false){
				if(f3==""){
					msg+="- Primary Ins.Co<br>"; ++flag;
					if(alert_id==""){
						alert_id="primaryInsText";
					}
				}
				if((f4=="") || (f4=="00-00-0000")) { 
					msg+="- Start Date<br>"; ++flag; 
					if(alert_id==""){
						alert_id="st_date";
					}
				}
			}	
			if((f16=="") || (f16==0)){
				msg+="- Billing Provider<br>"; ++flag;
				if(alert_id==""){
					alert_id="primary_provider_id";
				}
			}
			if($("#billing_type").val()==1 && $("#admit_date").val()==""){
				msg+="- Admit Date <br>"; ++flag;
				if(alert_id==""){
					alert_id="admit_date";
				}
			}
			if($("#admit_time").hasClass("mandatory")){
				msg+="- Start Time <br>"; ++flag;
				if(alert_id==""){
					alert_id="admit_time";
				}
			}
			/*if(trim($('#acc_anesthesia_id').html())!="" && $("#disch_date").val()==""){
				msg+="- Discharge Date <br>"; ++flag;
				if(alert_id==""){
					alert_id="disch_date";
				}
			}*/
			if($("#disch_time").hasClass("mandatory")){
				msg+="- End Time <br>"; ++flag;
				if(alert_id==""){
					alert_id="disch_time";
				}
			}	
			
			if($("#report_type_code").val()!= ""  && $("#transmission_code").val()== ""){
				msg+="- Transmission Code <br>"; ++flag;
				if(alert_id==""){
					alert_id="transmission_code";
				}
			}
			
			var dx_cont=0;
			var proc_cont=0;
			var last_cnt=$("#last_cnt").val();
			var proc_code_id=0;
			for(var j=1;j<=last_cnt;j++){
				var chr_id="chkbx_"+j;
				var chk_old_app_amt="chk_old_app_amt_"+j;
				var net_amt_chk="netAmt_"+j;
				var proc_id="procedureText_"+j;
				var diagText_all="diagText_all_"+j;
				var chk_de_cr="chk_de_cr_"+j;
				var netAmt_old_chk="netAmt_old_"+j;
				var onset_date_chk="onset_date_"+j;
				
				
				if($('#'+chr_id)){
					if($('#'+chr_id).is(':checked')==true){
						proc_code_id+=","+$('#'+chr_id+':checked').val();
						if(($('#'+proc_id).val()=="")  && proc_cont==0){
							if($('#'+diagText_all).val()!=""){
								msg+="- Procedure Code.<br>"; 
								++flag;
								proc_cont++;
							}
						}
						if($('#'+diagText_all).length>0 && $('#'+proc_id).val()!=""){
							if($('#'+diagText_all).val()=="" || $('#'+diagText_all).val()==null){
								msg+=$('#'+proc_id).val()+" - Dx Codes should be specified.<br>"; 
								++flag;
							}
						}
						if($('#'+chk_old_app_amt).length>0){
							if(parseFloat($('#'+chk_old_app_amt).val())>parseFloat($('#'+net_amt_chk).val())){
								msg+=$('#'+proc_id).val()+" - Charges should be more then allowed amount ($"+$('#'+chk_old_app_amt).val()+").<br>"; 
								++flag;
							}
						}
						if($('#'+chk_de_cr).length>0){
							if(parseFloat($('#'+netAmt_old_chk).val())!=parseFloat($('#'+net_amt_chk).val())){
								msg+=$('#'+proc_id).val()+" - Procedure have debit/credit transaction, can not modify. <br>"; 
								++flag;
							}
						}	
						/*if($('#'+onset_date_chk).length>0){
							var dos_dat = Date.parse(f15); 
							var onset_dat = Date.parse($('#'+onset_date_chk).val()); 
							if($('#'+onset_date_chk).val()!="" && onset_dat>dos_dat){
								msg+=$('#'+proc_id).val()+" - Onset Date should not be greater than DOS Date. <br>"; 
								++flag;
							}
						}*/
					}
				
				}
			}
			var chk_icd10_alert_val='';
			if(st=="yes"){
				$(".dx_box_12").each(function() {
					if($(this).val().indexOf("-")!=-1 && $(this).val().indexOf(".")!=-1){	
						chk_icd10_alert_val+=$(this).val()+', ';
					}
				});
				chk_icd10_alert_val = chk_icd10_alert_val.substr(0,((chk_icd10_alert_val.length)-2));
				if(chk_icd10_alert_val!=''){
					msg+=" - Please complete the DX Code(s) "+chk_icd10_alert_val+" <br>"; 
					++flag;
				}
			}
			$('#chkbox_for_post').val(proc_code_id);
			if(flag<=0){
				if(st=='yes'){
					if($('#chk_frm_sub').val()=='yes'){
						top.show_loading_image("show","150");
						if(top.$("#save")){
							top.$("#save").prop("disabled",true);
							if(top.$("#post_charges")){
								top.$("#post_charges").prop("disabled",true);
							}
						}
						document.enter_charges.submit();
					}else{
						postCharges();
					}
				}else{
					top.show_loading_image("show","150");
					if(top.$("#save")){
						top.$("#save").prop("disabled",true);
					}
					document.enter_charges.submit();
				}
				return true;
			}else{
				top.show_loading_image("hide");
				top.fAlert(msg);
				return false;
			}
		}
	}
	
	function proc_comment_txt(id,old_proc){
		var proc_id="procedureText_"+id;
		var notes_txt="notes_"+id;
		var mod1="mod1Text_"+id;
		var mod2="mod2Text_"+id;
		var mod3="mod3Text_"+id;
		var mod4="mod4Text_"+id;
		var units="units_"+id;
		var char_chk="charges_"+id;
		var net_amt_chk="netAmt_"+id;
		var cpt_tax="cpt_tax_"+id;
		var acc_anes_unit = $('#acc_anes_unit').val();
		if($('#'+proc_id).val()!=""){
			var xyz = $('#'+proc_id).val();
			
			var url="acc_ajax.php?action_type=cpt_comment&proc_code="+encodeURIComponent(xyz);
			$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					var result = resp;
					result=trim(result);
					var ARR_result = JSON.parse(result);
					var val_arr = ARR_result['cpt_comment_val'].split("~~~");
					$('#'+mod1).val(val_arr[0]);
					$('#'+mod2).val(val_arr[1]);
					$('#'+mod3).val(val_arr[2]);
					$('#'+mod4).val(val_arr[6]);
					if(acc_anes_unit>0){
						$('#'+units).val(parseFloat(val_arr[3])+parseFloat(acc_anes_unit));
					}else{
						$('#'+units).val(val_arr[3]);
					}
					$('#'+notes_txt).val(val_arr[4]);	
					$('#'+cpt_tax).val(val_arr[5]);
					
					
					if($('#'+units).val()>0){
						var u = $('#'+units).val();
					}else{
						var u = 1;
						if(acc_anes_unit>0){
							$('#'+units).val(parseFloat(u)+parseFloat(acc_anes_unit));
						}else{
							$('#'+units).val('1');
						}	
					}
					var fee = $('#'+char_chk).val(); 
					var tPrice=fee*u;
					var fee = trim(fee);
					var tPrice = tPrice.toFixed(2);
					$('#'+char_chk).val(fee);		
					$('#'+net_amt_chk).val(tPrice);
					set_rev_rate(id);
					addTax(id);
				}
			});
		}
	}
	function ajaxFunction1(id,old_proc){
		var pInsId = $('#getPriInsId').val();
		var id_chk=id;
		var proc_id="procedureText_"+id;
		var unit_chk="units_"+id;
		var char_chk="charges_"+id;
		var net_amt_chk="netAmt_"+id;
		var proc_txt="procedureText_"+id;
		var proc_txt="procedureText_"+id;
		var app_proc_dx_code="app_proc_dx_code_"+id;		
		
		
		var v = $('#'+proc_id).val();
		if(v==""){
			top.fAlert("Please select or enter a Procedure.")
			return false;
		}
		if(v=='RTN-CHK'){
			pInsId=0;
		}
		if(top.$("#save")){
			top.$("#save").prop("disabled",true);
		}
		if(top.$("#post_charges")){
			top.$("#post_charges").prop("disabled",true);
		}
		
		var xyz=$('#'+proc_txt).val();
		
		var url="get_price.php?str="+encodeURIComponent(xyz)+"&pInsId="+pInsId;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				var val = resp;
				var val_arr = val.split(", ");
				var code = val_arr[0];
				var fee = val_arr[1];
				var dx_codes = val_arr[2];
				if(id==1){
					$('#app1_1').val($('#auth_no').val());
				}
				if(old_proc=='no'){
					if($('#'+unit_chk).val()>0){
						var u = $('#'+unit_chk).val();
					}else{
						var u = 1;
					}
					var tPrice=fee*u;
					var fee = trim(fee);
					var tPrice = tPrice.toFixed(2);
					$('#'+char_chk).val(fee);		
					$('#'+net_amt_chk).val(tPrice);
					$('#'+app_proc_dx_code).val(dx_codes);
				}	
				if(old_proc=='no' && code==''){
					$('#'+char_chk).val('0.00');		
					$('#'+net_amt_chk).val('0.00');
					$('#'+proc_id).val(xyz);
					$('#'+app_proc_dx_code).val('');
				}else{
					$('#'+proc_id).val(code.replace(/~~~/g,","));
					$('#'+app_proc_dx_code).val(dx_codes);
				}
				refractionChkFun(xyz,char_chk,net_amt_chk);
				if(code==""){
					$('#'+proc_txt).val("");
					$('#'+app_proc_dx_code).val('');
				}
				if(top.$("#save")){
					top.$("#save").prop("disabled",false);
				}
				if(top.$("#post_charges")){
					top.$("#post_charges").prop("disabled",false);
				}
				set_rev_rate(id);
				addTax(id);
			}
		});
		proc_comment_txt(id_chk,old_proc);
	}
	
	function totalAmt(id){
	
		var proc_txt="procedureText_"+id;
		var unit_chk="units_"+id;
		var char_chk="charges_"+id;
		var net_amt_chk="netAmt_"+id;
		
		var proc = $('#'+proc_txt).val();
		if(proc==""){
			top.fAlert("Please select Procedure code")
			return false;
		}
		var u = $('#'+unit_chk).val();
		var p = $('#'+char_chk).val();
		do{
			if(p.indexOf(",")!=-1){
				var p = p.replace(",", "")
			}
		}while(p.indexOf(",")!=-1)
		
		if(isNaN(u)){
			top.fAlert("Please enter a valid numeric Charge amount.")
			$('#'+unit_chk).val("");
			return false;
		}
		if(isNaN(p)){
			top.fAlert("Please enter a valid numeric Charge amount.")
			$('#'+net_amt_chk).val("");
			return false;
		}
		var c = u * p;
		var c = (c.toFixed(2));
		$('#'+net_amt_chk).val(c);
		set_rev_rate(id);
		if(proc.toLowerCase()!="tax"){
			addTax(id);
		}
	}
	
	function chkValidation(id){
		if($('#enc_icd10').val()>0){
		}else{
			var f1 = $('#'+id).val();
			var url="acc_ajax.php?action_type=dx_code&dx="+f1;
			$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					var result = resp;
					result=trim(result);
					var ARR_result = JSON.parse(result);
					$('#'+id).val(ARR_result['dx_code_val']);
				}
			});
		}
	}
	
	function postCharges(){	
		var last_cnt=$("#last_cnt").val();
		var chld_ids="";
		var proc_codes="";
		var chk_sel=0;
		for(var j=1;j<=last_cnt;j++){
			var chr_id="chkbx_"+j;
			var chld_id="chld_id_"+j;
			var proc_txt="procedureText_"+j;
			var proc_self_txt="proc_selfpay_"+j;
			if($('#'+chr_id).length>0){
				if($('#'+chr_id).is(':checked')){
					chk_sel=1;
					chld_ids+= $('#'+chld_id).val()+',';
					proc_codes+= $('#'+proc_txt).val()+', ';
				}
			}
		}
		if(chk_sel==0){
			for(var j=1;j<=last_cnt;j++){
				var chr_id="chkbx_"+j;
				var chld_id="chld_id_"+j;
				var proc_txt="procedureText_"+j;
				var proc_self_txt="proc_selfpay_"+j;
				if($('#'+proc_txt).val()!=""){
					chld_ids+= $('#'+chld_id).val()+',';
					proc_codes+= $('#'+proc_txt).val()+', ';
				}
			}
		}
		var eId=$('#enc_id_read').val();
		var post=$('#chargesPosted').val();
		var printStatus=$('#initialPrinted').val();
		if(printStatus=='false'){	
			top.popup_win("../../interface/accounting/postCharges.php?eId="+eId+"&chld_ids="+chld_ids+"&proc_codes="+proc_codes,'width=670,height=355,top=250,left=400');
		}else{
			if(post=="false"){
				top.fAlert("Charges are already Re-submitted.")
				return false;
			}else{
				top.popup_win("../../interface/accounting/postCharges.php?eId="+eId+"&chld_ids="+chld_ids+"&proc_codes="+proc_codes,'width=670,height=355,top=250,left=400');
			}
		}
	}
	
	function emptyNotes(obj){
		var val = obj.value;
		if(val=="Comment"){
			obj.value="";
		}
	}
	
	function loadCaseInfo(eId){
		if($('#caseTypeText')){
			if($('#chk_self').is(':checked')){
				var id = 0;
			}else{
				var caseType = $('#caseTypeText').val();
				var case_arr= caseType.split("-");
				var case_len=case_arr.length;
				var case_final=case_arr[case_len-1];
				var id = trim(case_final);
				var dos=$('#dos_id').val();
			}
			var url = "getInsComp.php?ins="+id+"&eid="+eId+"&dos_send="+dos;
			$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					var result = resp;
					result=trim(result);
					var ARR_result = JSON.parse(result);
					for(arr_key in ARR_result){
						if(ARR_result[arr_key]==null){ARR_result[arr_key]='';}	
					}
					if(ARR_result['effective_date']!='--'){
						$('#st_date').val(ARR_result['effective_date']);
					}
					if(ARR_result['expiration_date']!='--'){
						$('#end_date').val(ARR_result['expiration_date']);
					}
					$('#primaryInsId').val(ARR_result['primaryInsCoName']);
					$('#getPriInsId').val(ARR_result['primaryInsId']);
					$('#secondaryInsId').val(ARR_result['secondaryInsCoName']);
					$('#getSecInsId').val(ARR_result['secondaryInsId']);
					$('#tertiaryInsId').val(ARR_result['tertiaryInsCoName']);
					$('#getTriInsId').val(ARR_result['tertiaryInsId']);

					if(ARR_result['copay']>0){
						if($('#pay_copay_chg')){
							$('#pay_copay_chg').val(ARR_result['copay']);
						}
					}else{
						$('#pay_copay_chg').val('0.00');
					}	
					if(ARR_result['secCopayAmt']>0){
						if($('#pay_copay_chg2')){
							$('#pay_copay_chg2').val(ARR_result['secCopayAmt']);
						}
					}else{
						$('#pay_copay_chg2').val('0.00');
					}
					
					
					$('#pri_scan_card').html(ARR_result['pri_scan_card1'].replace(/__/g,","));
					$('#pri_scan_card2').html(ARR_result['pri_scan_card2'].replace(/__/g,","));
					$('#sec_scan_card').html(ARR_result['sec_scan_card1'].replace(/__/g,","));
					$('#sec_scan_card2').html(ARR_result['sec_scan_card2'].replace(/__/g,","));
					$('#ter_scan_card').html(ARR_result['ter_scan_card1'].replace(/__/g,","));
					$('#ter_scan_card2').html(ARR_result['ter_scan_card2'].replace(/__/g,","));
					$('#optionalReferral').val(ARR_result['refNo2']);
					$('#reffer_physician').val(ARR_result['reffName']);
					$('#refferingPhysician').val(ARR_result['reffPhyId']);
					$('#pri_institutional_type').val(ARR_result['primary_institutional_type']);
					
					//reload_simple_menu('auth_rec_id',ARR_result['auth_drop_val'].replace(/__/g,","));
					reload_simple_menu('reff_rec_id',ARR_result['reff_drop_val'].replace(/__/g,","));
					get_accept_assignment($('#getPriInsId').val());
					$('#referral').val(ARR_result['refNo1']);
					//$('#auth_id').val(ARR_result['auth_id']);
					//$('#auth_no').val(ARR_result['auth_no']);
					//$('#auth_amount').val(ARR_result['auth_amount']);
					anes_fun();
				}
			});
		}
		set_auto_state();
		set_auth_drop(eId);
	}
	
	function GetXmlHttpObject()
	{ 
		var objXMLHttp=null
		if (window.XMLHttpRequest)
		{
		objXMLHttp=new XMLHttpRequest()
		}
		else if (window.ActiveXObject)
		{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
		}
		return objXMLHttp;
	}
	
	function checkPosFn(id){
		if(id>0){
			var pos = $("#pos_"+id).val();
			var posFacilityCodeChange="posFacilityCode_"+id;
		}else{
			var pos = $("#posText").val();
			var posFacilityCodeChange="posFacilityCode";
		}
		var url="acc_ajax.php?action_type=pos_fac_drop&pos_id="+pos;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				var result = resp;
				result=trim(result);
				var ARR_result = JSON.parse(result);
				$("#"+posFacilityCodeChange).html(ARR_result['pos_fac_drop_val']);
				//$("#"+posFacilityCodeChange).selectpicker('refresh');
			}
		});
	}
	
	//------- Function To Add Dot By Default After Three Character ----------
	function fillDates(val){
		var dateCheck = checkdate(val);
	}
	
	function hidecopayprice(cpt_id,id){
		var cptid=cpt_id;
		var copay_proc_id="proc_copay_"+id;
		var copay_amt=eval($('#pay_copay_chg').val())+eval($('#pay_copay_chg2').val());
		if(cptid==""){
		 $('#'+copay_proc_id).val('0.00');
		}else{
			if(cptid==99212 || cptid==99213 || cptid==99214 || cptid==99215 || 
			   cptid==99201 || cptid==99202 || cptid==99203 || cptid==99204 || 
			   cptid==99205 || cptid==99241 || cptid==99242 || cptid==99243 ||
			   cptid==99244 || cptid==99245 || cptid==92012 || cptid==92013 ||
			   cptid==92014 || cptid==92002 || cptid==92003 || cptid==92004 ){
				   $('#'+copay_proc_id).val(copay_amt.toFixed(2));
			   }else{
				  $('#'+copay_proc_id).val('0.00');
			   }
		}
	}
	
	function edit_copay_ins(){
		if($('#encounterIdText').val()>0 && $('#encounterIdText').val()!=""){
			msgs='Are you sure, you want to change copay in insurance?';
			var foot='<button type="button" class="btn btn-default" onclick="yes_ins_copay()">Yes</button><button type="button" class="btn btn-default" onclick="no_ins_copay()">No</button>';
			show_modal('edit_copay_ins_div','',msgs,foot);
		}		
	}
	
	function yes_ins_copay(){
		var copay_pri=$('#pay_copay_chg').val();
		var copay_sec=$('#pay_copay_chg2').val();
		var copay_tri=$('#pay_copay_chg3').val();
		var getPriInsId=$('#getPriInsId').val();
		var getSecInsId=$('#getSecInsId').val();
		var encounter_id=$('#encounterIdText').val();
		var caseType=$('#caseTypeText').val();
		var c =	caseType.indexOf("-");
		var ins_case_type = trim(caseType.substr(c+1));
		chg_ins_copay(copay_pri,getPriInsId,copay_sec,getSecInsId,copay_tri,ins_case_type,encounter_id,'yes');
	}
	
	function no_ins_copay(){
		var copay_pri=$('#pay_copay_chg').val();
		var copay_sec=$('#pay_copay_chg2').val();
		var copay_tri=$('#pay_copay_chg3').val();
		var getPriInsId=$('#getPriInsId').val();
		var getSecInsId=$('#getSecInsId').val();
		var encounter_id=$('#encounterIdText').val();
		var caseType=$('#caseTypeText').val();
		var c =	caseType.indexOf("-");
		var ins_case_type = trim(caseType.substr(c+1));
		chg_ins_copay(copay_pri,getPriInsId,copay_sec,getSecInsId,copay_tri,ins_case_type,encounter_id,'no');
	}
	
	function chg_ins_copay(copay_pri,getPriInsId,copay_sec,getSecInsId,copay_tri,ins_case_type,encounter_id,ins){
		var url="change_ins_copay.php?enc_id="+encounter_id+"&copay_ins_pri="+copay_pri+"&getPriInsId="+getPriInsId+"&copay_ins_sec="+copay_sec+"&getSecInsId="+getSecInsId+"&copay_ins_tri="+copay_tri+"&ins_case_type="+ins_case_type+"&ins="+ins;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				var result = resp;
				$('#edit_copay_ins_div').modal('hide');
			}
		});
	}
	function show_div(id){
		if(id){
			var ids=eval(id)+1;
			var chr_id="chkbx_"+id;
			var net_amt_chk="netAmt_"+id;
			var proc_id="procedureText_"+id;
			var diagText_all="diagText_all_"+id;
			if($('#'+chr_id)){
				if($('#'+net_amt_chk).val()=="" && $('#'+proc_id).val()=="" && $('#'+diagText_all).val()==""){
					$("#"+chr_id).prop("checked",false);
				}else{
					$("#"+chr_id).prop("checked",true);
				}
			}
		}else{
			var old_rec=$('#old_rec').val();
			var ids=eval(old_rec)+1;
		}
		var last_cnt= $('#last_cnt').val();
		var past_proc_id="procedureText_"+last_cnt;
		if($('#'+past_proc_id)){
			if($('#'+past_proc_id).val()!="" && last_cnt==id){
				addNewRow(last_cnt);
			}
		}
		tot_charges();
	}		
	function del_chld(s){
		if($("#acc_view_chr_only").val()==1  || $("#acc_edit_financials").val()==0){
			view_only_acc_call(0);
			return false;
		}
		if(typeof s !="undefined"){
			if($('#chld_id_'+s).val()>0){
				$(".chk_box_css").prop("checked",false);
				$("#chkbx_"+s).prop("checked",true);
			}else{
				$('#chkbx_'+s).prop("checked",false);
				$('#procedureText_'+s).val("");
				$('#diagText_all_'+s).val("");
				$('#'+s).hide();
				addNewRow($('#last_cnt').val());
				return false;
			}
		}
		var flag=0;
		var chld_id="";
		var chld_ids="";
		var proc_code_alls="";
		var last_cnt=$("#last_cnt").val();
		for(var i=1;i<=last_cnt;i++){
			var chr_id="chkbx_"+i;
			var chr_id_del="chkbx_del_"+i;
			var proc_code="procedureText_"+i;
			if($('#'+chr_id)){
				if($('#'+chr_id).is(':checked') && $('#'+chr_id+':checked').val()!='yes'){
					if($('#'+chr_id_del).length > 0){
						if($('#'+chr_id_del).val()=='no'){
							proc_code_all=$('#'+proc_code).val();
							proc_code_alls+=proc_code_all+',';
						}
					}else{	
						var chld_id=$('#'+chr_id+':checked').val();
						chld_ids+=chld_id+',';
					}
					flag=flag+1;
				}	
			}
		}
		if(flag<=0){
			top.show_loading_image("hide");
			if($('#enc_id_read').val()==''){
				top.fAlert("No Data has been saved yet.");
			}else{
				top.fAlert("No Record is selected. Please select to void.");
			}
		}
		if(flag>0){
			var msg="";
			if(proc_code_alls){
				var show_proc_code=proc_code_alls.substr(0,proc_code_alls.length-1);
				msg=show_proc_code+" will not be voided  as the transaction has been made.<br>";
				top.show_loading_image("hide");
				top.fAlert(msg);
				return false;
			}
			top.show_loading_image("hide");
			var ask=msg+"Are you sure to continue?";
			var enc_id=$('#encounterIdText').val();

			top.fancyConfirm(ask,'',"window.top.fmain.document.location.href='../accounting/accounting_rec_del.php?chld_id="+chld_ids+"&enc_id="+enc_id+"'");
		}
	}
	
	function chk_all_self_proc(){
		if($('#chk_self').is(':checked')){
			$('.proc_selfpay').prop('checked',true);
		}else{
			$('.proc_selfpay').prop('checked',false);
		}
	}
	function getmodeDescJs(obj){
		var ret = "";
		var modeCode=obj.value;
		modeCode = myTrim( modeCode );
		for(var i=0;i<str_arr_desc_code.length;i++){
			if(str_arr_desc_code[i]==modeCode){
				obj.value=str_arr_proc_code[i];
				break;
			}
		}
		for(var i=0;i<str_arr_proc_desc_code.length;i++){
			if(str_arr_proc_desc_code[i]==modeCode){
				obj.value=str_arr_proc_code[i];
				break;
			}
		}	
	}
	function focus_div(val){
		var nam=val.name;
		var arr_val=nam.split('_');
		var new_id=eval(arr_val[1])+1;
		var new_nam='procedureText_'+new_id;
		if(document.getElementById(new_nam)){
			if(document.getElementById(new_nam).style.display!='none'){
				alert(new_nam);
				document.getElementById(new_nam).focus();
			}
		}
	}
	
	function tot_charges(){
		var tot_chg=0.00;
		var last_cnt=$('#last_cnt').val();
		for(var j=1;j<=last_cnt;j++){
			var chr_id="netAmt_"+j;
			if($('#'+chr_id)){
				if($('#'+chr_id).val()!=""){
					tot_chg=parseFloat(tot_chg)+parseFloat($('#'+chr_id).val());
				}
			}
		}	
		$('#read_tot_charges').val(tot_chg.toFixed(2));
	}
	function show_rev_div(){
		var last_cnt=$("#last_cnt").val();
		var group_id = $('#groups').val().split('_');
		var chk_gro_id_arr = $('#chk_gro_id').val().split(',');
		if($("#billing_type").val()!=2){
			$('#rev_code_txt').hide();
			$('#proc_code_txt').hide();
			
			for(var j=1;j<=last_cnt;j++){
				var rev_id="rev_code_td_"+j;
				var rev_id_input="revcode_"+j;
				var proc_id="proc_code_td_"+j;
				var proc_id_input="proccode_"+j;
				if($('#'+rev_id)){
					$('#'+rev_id).hide();
					$('#'+rev_id_input).prop( "disabled", true );
					$('#'+proc_id).hide();
					$('#'+proc_id_input).prop( "disabled", true );
				}
			}	
		}else{
			$('#rev_code_txt').show();
			$('#proc_code_txt').show();
			for(var j=1;j<=last_cnt;j++){
				var rev_id="rev_code_td_"+j;
				var rev_id_input="revcode_"+j;
				var proc_id="proc_code_td_"+j;
				var proc_id_input="proccode_"+j;
				if($('#'+rev_id)){
					$('#'+rev_id).show();
					$('#'+rev_id_input).prop( "disabled", false );
					$('#'+proc_id).show();
					$('#'+proc_id_input).prop( "disabled", false );
					if($('#revcode_'+j).val()=="" && $('#procedureText_'+j).val()!="" && server_name=='shoreline'){
						rev_ajaxfunction(j);
					}
				}
			}	
		}
	}
	
	function rev_ajaxfunction(val){
		var cpt_id_chk="procedureText_"+val;
		var rev_id_chk="revcode_"+val;
		var cptid_chk=$('#'+cpt_id_chk).val();
		var url="acc_ajax.php?action_type=rev_code&proc_code="+cptid_chk;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				var result = resp;
				result=trim(result);
				var ARR_result = JSON.parse(result);
				$('#'+rev_id_chk).val(ARR_result['rev_code_val']);
			}
		});
	}
	
	function case_type_id_sch(pat_id,dat){
		var dat_exp=dat.split('-');
		var dat_final=dat_exp[2]+'-'+dat_exp[0]+'-'+dat_exp[1];
		var url="acc_ajax.php?action_type=curr_case&pat_id="+pat_id+"&dat="+dat_final;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				var result = resp;
				result=trim(result);
				var ARR_result = JSON.parse(result);
				if(ARR_result['curr_case']!=""){
					$('#caseTypeText').selectpicker('val',ARR_result['curr_case']);
					loadCaseInfo();
				}
			}
		});
	}
	
	function set_frm_post(){
		$('#chk_frm_sub').val('yes');		
	}
		
	function show_scanned(obj,id,val,type){
		var img_src = $(obj).data('src');
		var img_type = $(obj).data('type');
		if( img_type == 'pdf')
			var modal_src = '<object style="width:100%; height:365px;" type="application/pdf" data="'+img_src+'"></object>';
		else
			var modal_src = '<img src="'+img_src+'" style="max-width:100%; width:auto; height:auto;" >';
		
		var modal_content = '<div class="row"><div class="col-sm-12" style="min-height:365px;">'+modal_src+'</div></div>';
		var modal_footer = '<div class="row"><div class="col-sm-12 text-center"><button class="btn btn-danger" data-dismiss="modal">Close</button></div></div>';
		show_modal('ins_scan_modal','Insurance Scan Documents',modal_content,modal_footer,'400','modal-lg');
		//window.open('show_scan_img_acc.php?id='+id+'&val='+val+'&type='+type,'scan','');
	}
	function show_ins_tab(){
		top.change_main_Selection(top.document.getElementById('Patient_Info'));
		top.fmain.location.href='../main/patient_tabs.php?tab=Insurance';
	}
	function set_auth_drop(id){
		var phy_name = $('#primary_provider_id').val();
		var dos_id = $('#dos_id').val();
		var dat_exp=dos_id.split('-');
		var dat_final=dat_exp[2]+'-'+dat_exp[0]+'-'+dat_exp[1];
		var last_cnt=$('#last_cnt').val();
		var proc_code_val ="";
		for(var i=1;i<=last_cnt;i++){
			var proc_id="procedureText_"+i;
			if($('#'+proc_id) && $('#'+proc_id).val()!=""){
				proc_code_val +=$('#'+proc_id).val()+';';
			}
		}
		
		if($('#chk_self').is(':checked')){
			var ins_id = 0;
		}else{
			var caseType = $('#caseTypeText').val();	
			var case_arr= caseType.split("-");
			var case_len=case_arr.length;
			var case_final=case_arr[case_len-1];
			var ins_id = trim(case_final);
		}
		
		var url="acc_ajax.php?action_type=auth_info&phy_name="+phy_name+"&dat="+dat_final+"&ins_id="+ins_id+"&enc_proc_codes="+proc_code_val;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				var result = resp;
				result=trim(result);
				var ARR_result = JSON.parse(result);
				reload_simple_menu('auth_rec_id',ARR_result['auth_drop_val'].replace(/__/g,","));
				if(id!="show_only_drop"){
					$("#auth_id").val(ARR_result['auth_id']);
					$("#auth_no").val(ARR_result['auth_no']);
					$("#auth_amount").val(ARR_result['auth_amount']);
				}
			}
		});
	}
	function set_auth_info(obj){
		var caseType = document.enter_charges.caseTypeText.value;	
		var case_arr= caseType.split("-");
		var case_len=case_arr.length;
		var case_final=case_arr[case_len-1];
		var ins_case_id = trim(case_final);
		var auth_number_chk=obj.value;
		document.getElementById('app1_1').value = auth_number_chk;
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			top.fAlert("Browser does not support HTTP Request");
			return;
		} 		
		var url="acc_ajax.php?action_type=pat_auth&auth_number="+auth_number_chk+"&ins_case_id="+ins_case_id;
		$.ajax({
			type:'GET',
			url:url,
			success:function(response){
				var result = response;
				result=trim(result);
				var ARR_result = JSON.parse(result);
				$("#auth_amount").val(ARR_result['auth_amount']);
				$("#auth_id").val(ARR_result['auth_id']);
			}
		});
	}
	function addNewRow(val,cpt_dx){
		var cpt_code="";
		var cpt_id="";
		var cpt_unit="";
		var dx_arr="";
		if(typeof cpt_dx !="undefined"){
			cpt_code=cpt_dx.cptCode;
			cpt_id=cpt_dx.procId;
			cpt_unit=cpt_dx.units;
			dx_arr=cpt_dx.arrDx;
		}
		var pre_cnt = $('#last_cnt').val();
		var display_order_id="display_order_"+val;
		var enc_id_read = $('#enc_id_read').val();
		if(val==""){
			if($("#display_order_"+pre_cnt).length){
				var new_display_ord_val=parseInt($("#display_order_"+pre_cnt).val())+1;
			}else{
				var new_display_ord_val=1;
			}
		}else{
			if(val>0){
				var new_display_ord_val=parseInt($("#display_order_"+val).val())+1;
			}else{
				var new_display_ord_val=1;
			}
		}
		for(k=0;k<=pre_cnt;k++){
			if($("#display_order_"+k)){
				if(parseInt($("display_order_"+k).val())>=parseInt(new_display_ord_val)){
					$("#display_order_"+k).val(parseInt($("#display_order_"+k).val())+1);
				}
			}
		}
		pre_cnt++;
		var bgcolor = (pre_cnt%2) == 0 ? '#F4F9EE' : '#FFFFFF';
		var show_dx_wid="300";
		if(pre_cnt==1){
			//$('#dx_code_head').css({'min-width':'295px'});
			show_dx_wid="280";
		}
		var td_val  = '<td><span class="glyphicon glyphicon-remove pointer" alt="Delete Row" onclick="del_chld('+pre_cnt+');"></td>';
		td_val += '<td><input type="hidden" name="cpt_tax_'+pre_cnt+'" id="cpt_tax_'+pre_cnt+'" value=""><input type="hidden" id="display_order_'+pre_cnt+'" name="display_order_'+pre_cnt+'" class="display_order_cls" value="'+new_display_ord_val+'"><input type="hidden" id="chld_id_'+pre_cnt+'" name="chld_id_'+pre_cnt+'" value=""><div class="checkbox"><input type="checkbox" id="chkbx_'+pre_cnt+'" name="chkbx_'+pre_cnt+'" class="chk_box_css" value="yes" onChange="set_frm_post();"><label for="chkbx_'+pre_cnt+'"></label></div></td>';
		td_val += '<td class="text-center"><div class="checkbox"><input type="checkbox" name="proc_selfpay_'+pre_cnt+'" id="proc_selfpay_'+pre_cnt+'" onClick="show_div('+pre_cnt+');"><label for="proc_selfpay_'+pre_cnt+'"></label></div></td>';
		td_val += '<td><input id="procedureText_'+pre_cnt+'" name="procedureText_'+pre_cnt+'" data-sort="contain" value="'+cpt_code+'" type="text" class="form-control" style="width:110px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}"  onBlur="rev_ajaxfunction('+pre_cnt+'); hidecopayprice(this.value,'+pre_cnt+'); show_div('+pre_cnt+'); set_rev_rate('+pre_cnt+');" onChange="ajaxFunction1('+pre_cnt+',\'no\'); set_frm_post(); tos_ajaxfunction('+pre_cnt+'); chk_visit_code(); set_rev_rate('+pre_cnt+'); set_auth_drop('+enc_id_read+');"></td>';
		td_val += '<td id="rev_code_td_'+pre_cnt+'"><input id="revcode_'+pre_cnt+'" type="text"  name="revcode_'+pre_cnt+'" class="form-control" onkeypress="{if (event.keyCode==13)return focus_div(this);}"  onBlur="show_div('+pre_cnt+');" onChange="set_frm_post();"></td>';
		td_val += '<td id="proc_code_td_'+pre_cnt+'"><input id="proccode_'+pre_cnt+'" type="text"  name="proccode_'+pre_cnt+'" class="form-control" onkeypress="{if (event.keyCode==13)return focus_div(this);}"  onBlur="show_div('+pre_cnt+');" onChange="set_frm_post();"></td>';
		td_val += '<td class="text-left text-nowrap"><span><input type="hidden" name="app_proc_dx_code_'+pre_cnt+'" id="app_proc_dx_code_'+pre_cnt+'" value=""><select id="diagText_all_'+pre_cnt+'" name="diagText_all_'+pre_cnt+'[]" class="diagText_all_css selectpicker" data-actions-box="true" data-title="Select Dx Codes" data-container="#dxdrop" multiple="multiple" onChange="show_div('+pre_cnt+'); set_frm_post(); chk_adm_dx('+pre_cnt+');"></select></span>';
		if(pre_cnt==1){
			td_val += '<span style="padding-left:3px;"><a title="Copy Dx Codes" class="glyphicon glyphicon-copy" style="color: green;" onclick="sb_copy_dx_codes();" href="javascript:void(0);"></a></span>';
		}
		td_val += '</td>';
		td_val += '<td><input id="mod1Text_'+pre_cnt+'" type="text" name="mod1Text_'+pre_cnt+'" class="form-control" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+'); getmodeDescJs(this);" onChange="set_frm_post();"></td>';
		td_val += '<td><input id="mod2Text_'+pre_cnt+'" type="text" name="mod2Text_'+pre_cnt+'"  class="form-control" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+'); getmodeDescJs(this);" onChange="set_frm_post();"></td>';
		td_val += '<td><input id="mod3Text_'+pre_cnt+'" type="text" name="mod3Text_'+pre_cnt+'" class="form-control" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+'); getmodeDescJs(this);" onChange="set_frm_post();"></td>';
		td_val += '<td><input id="mod4Text_'+pre_cnt+'" type="text" name="mod4Text_'+pre_cnt+'" class="form-control" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+'); getmodeDescJs(this);" onChange="set_frm_post();"></td>';
		td_val += '<td><input type="hidden" id="proc_copay_'+pre_cnt+'" name="proc_copay_'+pre_cnt+'"  value="0.00" class="form-control"  onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+');"><input type="text" id="units_'+pre_cnt+'" name="units_'+pre_cnt+'"  value="'+cpt_unit+'"  class="form-control" onChange="set_frm_post(); return totalAmt('+pre_cnt+');" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+');"></td>';
		td_val += '<td><div class="input-group"><div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div><input type="text" id="charges_'+pre_cnt+'" name="charges_'+pre_cnt+'" class="form-control" style="width:80px;" onChange="set_frm_post(); return totalAmt('+pre_cnt+');" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+');"></div></td>';
		td_val += '<td id="netChargesTd_'+pre_cnt+'"><div class="input-group"><div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div><input type="text" id="netAmt_'+pre_cnt+'" name="netAmt_'+pre_cnt+'" class="form-control" style="width:80px;" onkeypress="{if (event.keyCode==13)return focus_div(this);}" onBlur="return totalAmt('+pre_cnt+'); show_div('+pre_cnt+');" onChange="set_frm_post();"></div></td>';
		td_val += '<td><input type="text" id="app1_'+pre_cnt+'" name="app1_'+pre_cnt+'"  class="form-control" style="width:80px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+');" onChange="set_frm_post();"></td>';
		td_val += '<td><div class="input-group"><input id="date_'+pre_cnt+'" type="text"  name="app1_date_'+pre_cnt+'" class="form-control date-pick" style="width:90px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="checkdate(this); show_div('+pre_cnt+');" onChange="set_frm_post();"><label class="input-group-addon pointer" for="date_'+pre_cnt+'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label></div></td>';
		td_val += '<td><input type="text" id="app2_'+pre_cnt+'" name="app2_'+pre_cnt+'"  class="form-control" style="width:80px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+');" onChange="set_frm_post();"></td>';
		td_val += '<td><div class="input-group"><input type="text" id="app2_date_'+pre_cnt+'" name="app2_date_'+pre_cnt+'" class="form-control date-pick" style="width:90px;" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="checkdate(this); show_div('+pre_cnt+');" onChange="set_frm_post();"><label class="input-group-addon pointer" for="app2_date_'+pre_cnt+'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label></div></td>';
		td_val += '<td><input type="text" id="rev_rate_'+pre_cnt+'"  name="rev_rate_'+pre_cnt+'" class="form-control" style="width:70px;" onBlur="show_div('+pre_cnt+');" onChange="set_frm_post();"></td>';
		td_val += '<td><textarea id="notes_'+pre_cnt+'" name="notes_'+pre_cnt+'" class="form-control" rows="1" style="width:180px;" onClick="return emptyNotes(this);" onKeyPress="{if (event.keyCode==13)return focus_div(this);}" onBlur="show_div('+pre_cnt+');" onChange="set_frm_post();"></textarea></td>';
		
		var tr = '<tr id="'+pre_cnt+'" class="text-center">' + td_val + '</tr>';
		if(val==""){
			var load_id=$('#last_cnt').val();
			if(load_id>0){
				$(tr).insertAfter('#'+load_id);
			}else{
				$("#acc_main_tbl").last().append(tr);
			}
		}else{
			$(tr).insertAfter('#'+val);
		}
		$('#last_cnt').val(pre_cnt);
		show_rev_div();
		if($('#procedureText_'+pre_cnt)){
			var obj7 = $('#procedureText_'+pre_cnt).typeahead({source:customarrayProcedure,scrollBar:true});
			var obj8 = $('#mod1Text_'+pre_cnt).typeahead({source:customarrayModifiers});
			var obj9 = $('#mod2Text_'+pre_cnt).typeahead({source:customarrayModifiers});
			var obj10 = $('#mod3Text_'+pre_cnt).typeahead({source:customarrayModifiers});
			var obj11 = $('#mod4Text_'+pre_cnt).typeahead({source:customarrayModifiers});
			if($('#revcode_'+pre_cnt)){
				var obj19 = $('#revcode_'+pre_cnt).typeahead({source:customarrayrev});
			}
			if($('#proccode_'+pre_cnt)){
				var obj20 = $('#proccode_'+pre_cnt).typeahead({source:customarrayproc});
			}
		}
		$('.date-pick').datetimepicker({timepicker:false,format:top.global_date_format,formatDate:'Y-m-d'});
		if(typeof cpt_dx !="undefined"){
			$('#chkbx_'+pre_cnt).prop("checked",true);
			ajaxFunction1(pre_cnt,'no');
		}
		crt_dx_dropdown(pre_cnt,dx_arr);
	}
	function removeTableRow(id,cnt){		
		if(id == ''){
			$('#chkbx_'+cnt).prop("checked",false);
			$('#procedureText_'+cnt).val("");
			$('#diagText_all_'+cnt).val("");
			$('#'+cnt).hide();
		}
		else{
			var last_cnt=$("#last_cnt").val();
			for(var i=1;i<=last_cnt;i++){
				var chr_id="chkbx_"+i;
				if($('#'+chr_id).length>0){
					if($('#'+chr_id).is(':checked')){
						$('#'+chr_id).prop("checked",false);
					}
				}
			}	
			$('#chkbx_'+cnt).prop("checked",true);
			del_chld();
		}
	}
	function myTrim(str,f)
	{
		str = str.replace(/^\s+|\s+$/, '');
		if(f==1)str = str.replace(/^(\&nbsp\;)+|(\&nbsp\;)+$/g, '');
		return str;
	}
	
	function getSelPQRICodes(){
		var oDiv = document.getElementById("divChoosePqriCodes");
		var selectedCode = document.getElementById("elem_procOrder").value;
		var oElems = oDiv.getElementsByTagName("INPUT");
		var len = oElems.length;
		var arrChoosenPqri = new Array();
		for(var i=0;i<len;i++){
			if((oElems[i].type == "checkbox") && (oElems[i].checked == true)){
				var tmpCode = oElems[i].value;
				var tmpDx = oElems[i].getAttribute("dx");
				if(selectedCode.indexOf(tmpCode) == -1){ //Check if already exists
					arrChoosenPqri[arrChoosenPqri.length] = [tmpCode,tmpDx];
				}
			}
		}
		oDiv.style.display="none";
		if(arrChoosenPqri.length > 0){
			for(var x in arrChoosenPqri){
				var cpt = arrChoosenPqri[x][0];
				var dx = arrChoosenPqri[x][1];
				
				var tmpDesc = cpt;
				var oVal = {"cptCode":cpt,"procId":"","units":1,"arrDx":[dx]};
				var auto_id=document.getElementById('pqri_pop_row').value;
				/*if(auto_id<=1){
					auto_id='top_row_id';
				}*/
				addNewRow(auto_id,oVal);
				//opAddCptRow(1,oVal,1); //Insert Always After Visit code as money is involved
			}
		}
	}
	function checkDB4Code_fun(obj){
		if ($('#tat_table').length)return;
		var id= $('#last_cnt').val();
		checkDB4Code(obj,'Dx','account');
		$('#pqri_pop_row').val(id);
	}
	function multi_dx_shw(val,dis){
		if(val){
			var cursor_point = getPosition();
			document.getElementById("multi_cpt_shw_id").style.top= (parseInt(cursor_point.y) + parseInt(0))+'px';
			document.getElementById("multi_cpt_shw_id").style.left= (parseInt(cursor_point.x)+parseInt(20))+'px';
			document.getElementById("multi_cpt_shw_id").innerHTML=val;
			document.getElementById("multi_cpt_shw_id").style.display=dis;
		}
	}
	function refractionChkFun(xyz,char_chk,net_amt_chk){
		var refractionChk = $('#vip_ref_not_collect').val();
		if(refractionChk>0 && $('#vipPatient').is(':checked')){
			if(xyz!=""){
				if(xyz=="92015" || xyz=="Refraction" || xyz=="refraction"){
					$('#'+char_chk).val('0.00');	
					$('#'+net_amt_chk).val('0.00');
				}
			}else{
				var last_cnt=$('#last_cnt').val();
				for(var i=1;i<=last_cnt;i++){
					var chr_id="chkbx_"+i;
					var char_chk="charges_"+i;
					var net_amt_chk="netAmt_"+i;
					var proc_id="procedureText_"+i;
					if($('#'+proc_id)){
						var xyz = $('#'+proc_id).val();
						if((xyz=="92015" || xyz=="Refraction" || xyz=="refraction")){
							$('#'+chr_id).prop("checked",true);
							$('#'+char_chk).val('0.00');
							$('#'+net_amt_chk).val('0.00');
						}
					}
				}
				tot_charges();
			}
		}
	}
	
//=======get current time function ===============//
function getCurrentTime(obj){
	if(obj.value==""){
		var date_obj = new Date();
		var date_obj_hours = date_obj.getHours();
		var date_obj_mins = date_obj.getMinutes();
		if(date_obj_hours<10){
			 date_obj_hours = "0"+date_obj_hours;
		}
		if(date_obj_mins<10){
			date_obj_mins="0"+date_obj_mins;
		}	
		obj.value= date_obj_hours+":"+date_obj_mins;
	}	
}
function anes_fun(anes_box){
	var group_id = $('#groups').val().split('_');
	if(typeof(anes_box) == "undefined"){
		if(group_id[1]>0){
			$("#billing_type").val('1');
		}else if(group_id[2]>0){
			if($("#pri_institutional_type").val()=='INST_PROF'){
				$("#billing_type").val('3');
			}else{
				$("#billing_type").val('2');
			}
		}else{
			$("#billing_type").val('3');
		}
	}
	$("#billing_type").selectpicker('refresh');
	var anes_chk_box = $("#billing_type").val();
	if(anes_chk_box==1){
		if($('#admit_time').val()!=""){
			$('#admit_time').val($('#admit_time').val().replace('.',':'));
			var chk_adm_format=$('#admit_time').val().split(' ');
			var chk_time_adm = chk_adm_format[0].split(':');
			if(typeof(chk_time_adm[1]) == "undefined"){
				chk_time_adm[1]='00';
			}
			var chk_adm_am_pm=""+chk_adm_format[1];
			if(chk_adm_format[1]!=""){
				chk_adm_am_pm=chk_adm_am_pm.toUpperCase();
			}
			if(chk_adm_am_pm=="AM" ||chk_adm_am_pm=="PM"){
				if(chk_adm_am_pm=="PM"){
					$('#admit_time').val(parseInt(12)+parseInt(chk_time_adm[0])+':'+chk_time_adm[1]);
				}else{
					$('#admit_time').val(chk_time_adm[0]+':'+chk_time_adm[1]);
				}	
			}else{
				$('#admit_time').val(chk_time_adm[0]+':'+chk_time_adm[1]);
			}
		}
		if($('#disch_time').val()!=""){
			$('#disch_time').val($('#disch_time').val().replace('.',':'));
			var chk_disch_format=$('#disch_time').val().split(' ');
			var chk_time_disch = chk_disch_format[0].split(':');
			if(typeof(chk_time_disch[1]) == "undefined"){
				chk_time_disch[1]='00';
			}
			var chk_disch_am_pm=""+chk_disch_format[1];
			if(chk_disch_format[1]!=""){
				chk_disch_am_pm=chk_disch_am_pm.toUpperCase();
			}
			if(chk_disch_am_pm=="AM" ||chk_disch_am_pm=="PM"){
				if(chk_disch_am_pm=="PM"){
					$('#disch_time').val(parseInt(12)+parseInt(chk_time_disch[0])+':'+chk_time_disch[1]);
				}else{
					$('#disch_time').val(chk_time_disch[0]+':'+chk_time_disch[1]);
				}	
			}else{
				$('#disch_time').val(chk_time_disch[0]+':'+chk_time_disch[1]);
			}
		}

		var anes_time_divisor = $('#anes_time_divisor').val();
		if($('#admit_time').val()==""){
			$('#admit_time').addClass("form-control mandatory");
		}else{
			$('#admit_time').removeClass("mandatory");
		}
		if($('#disch_time').val()==""){
			$('#disch_time').addClass("form-control mandatory");
		}else{
			$('#disch_time').removeClass("mandatory");
		}
		var start_time = $('#admit_time').val().split(':');
		var end_time = $('#disch_time').val().split(':');
		var anes_time_hour=(end_time[0]-start_time[0])*60;
		var anes_time_mint=end_time[1]-start_time[1];
		var anes_time = parseInt(anes_time_hour)+parseInt(anes_time_mint);
		if(anes_time>0){
		}else{
			anes_time=0;
		}
		var anes_unit =0;
		var anes_unit_get=""+anes_time/anes_time_divisor;
		if(anes_unit_get>0){
			var anes_unit_exp = anes_unit_get.split('.');
			if(anes_unit_exp[1]>0){
				anes_unit=anes_unit_get;
			}else{
				anes_unit=anes_unit_get;
			}
		}
		$('#acc_anes_time').val(anes_time);
		$('#acc_anes_unit').val(parseFloat(anes_unit).toFixed(2));
	}else{
		$('#acc_anes_time').val('0');
		$('#acc_anes_unit').val('0');
		$('#admit_time').removeClass("mandatory");
		$('#disch_time').removeClass("mandatory");
	}
}

function set_reff_drop(id){
	var ref_phy = $('#refferingPhysician').val();
	var dos_id = $('#dos_id').val();
	if($('#chk_self').is(':checked')){
		var ins_id = 0;
	}else{
		var caseType = $('#caseTypeText').val();	
		var case_arr= caseType.split("-");
		var case_len=case_arr.length;
		var case_final=case_arr[case_len-1];
		var ins_id = trim(case_final);
	}
	var dat_exp=dos_id.split('-');
	var dat_final=dat_exp[2]+'-'+dat_exp[0]+'-'+dat_exp[1];
	var url="acc_ajax.php?action_type=referral_info&ref_phy="+ref_phy+"&dat="+dat_final+"&ins_id="+ins_id;
	$.ajax({
		type: "POST",
		url: url,
		success: function(resp){
			var result = resp;
			result=trim(result);
			var ARR_result = JSON.parse(result);
			reload_simple_menu('reff_rec_id',ARR_result['reff_drop_val'].replace(/__/g,","));
			$("#referral").val(ARR_result['reff_no']);
		}
	});
}
function tos_ajaxfunction(cont){
	if(cont==1){
		var proc_id="procedureText_"+cont;
		var proc_code = $('#'+proc_id).val();	
		
		var url="acc_ajax.php?action_type=tos_code&proc_code="+proc_code;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				var result = resp;
				result=trim(result);
				var ARR_result = JSON.parse(result);
				if(ARR_result['tos_code_val'] != null && ARR_result['tos_code_val']!="undefined"){
					$("#tosText_id").val(ARR_result['tos_code_val']);
				}
			}
		});
	}
}
function chk_adm_dx(cont){
	var proc_code = $("#procedureText_"+cont).val();	
	var diagText_all = $("#diagText_all_"+cont).val();
	var app_proc_dx_code = $("#app_proc_dx_code_"+cont).val();
	var app_proc_dx_code_arr=app_proc_dx_code.split("~~~");
	var dx_alert ="";
	var dx_grt_than_four_alert="";
	if(diagText_all){
		for(i=0;i<diagText_all.length;i++){
			$("select#diagText_all_"+cont).find("option").each(function(index,value){
				if($(this).val()==diagText_all[i]){
					if($(this).is(':selected')){
						dx_grt_than_four_alert++;
						if(dx_grt_than_four_alert>4){
							$(this).prop('selected',false);
							$(this).removeAttr("selected");
						}
					}
				}
			});
		}
	}
	if(dx_grt_than_four_alert>4){
		crt_dx_dropdown();
		alert("You can not select more than 4 Dx codes for procedure "+proc_code+".");
	}
	if(app_proc_dx_code!="" && $('#enc_icd10').val()>0){
		for(i=0;i<diagText_all.length;i++){
			diagText_all_exp=diagText_all[i].split("**");
				var q1=""+diagText_all_exp[0].slice(0,-1)+"-";
				var q2=""+diagText_all_exp[0].slice(0,-2)+"--";
				var q3=""+diagText_all_exp[0].slice(0,-3)+"-x-";
				var q4=""+diagText_all_exp[0].slice(0,-3)+"-X-";
			if($.inArray(diagText_all_exp[0],app_proc_dx_code_arr)==-1 && $.inArray(q1,app_proc_dx_code_arr)==-1 && $.inArray(q2,app_proc_dx_code_arr)==-1 && $.inArray(q3,app_proc_dx_code_arr)==-1 && $.inArray(q4,app_proc_dx_code_arr)==-1){
				$("select#diagText_all_"+cont).find("option").each(function(index,value){
					if($(this).val()==diagText_all[i]){
						if($(this).is(':selected')){
							if(dx_alert==""){
								dx_alert += diagText_all_exp[0];
							}else{
								dx_alert += ', '+diagText_all_exp[0];
							}
							$(this).prop('selected',false);
							$(this).removeAttr("selected");
						}
					}
				});
			}
		}
		if(dx_alert!=""){
			crt_dx_dropdown();
			alert("Dx code(s) "+dx_alert+" can not be applied for procedure "+proc_code+".");
		}
	}
	if(proc_code!=""){
		$("#mod1Text_"+cont).focus();
	}
}
function chk_visit_code(){
	if(typeof(sb_multi_vst_cd_noalert)!="undefined" && sb_multi_vst_cd_noalert==1){ return true; }
	var last_cnt = $('#last_cnt').val();
	var enc_id_read = $('#enc_id_read').val();
	var dos = $('#dos_id').val();
	var code = 0;
	var chk_cont = 0;
	chk_visit_code_arr_splt=chk_visit_code_arr.split(",");
	for(var j=1;j<=last_cnt;j++){
		var proc_id="procedureText_"+j;
		if($('#'+proc_id)){
			if($('#'+proc_id).val()!=""){
				if($.inArray($('#'+proc_id).val(),chk_visit_code_arr_splt)==-1){
				}else{
					chk_cont = parseInt(chk_cont)+1;
				}
			}
		}
	}
	if(chk_cont>1){
		top.fAlert("Visit code already exists for DOS "+dos);
	}else{
		if(chk_cont==1){
			var dat_exp=dos.split('-');
			var dat_final=dat_exp[2]+'-'+dat_exp[0]+'-'+dat_exp[1];
			var url="acc_ajax.php?action_type=visit_code&enc_id="+enc_id_read+"&dat="+dat_final+"&visit_code="+visit_code;
			$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					var result = resp;
					result=trim(result);
					var ARR_result = JSON.parse(result);
					if(ARR_result['visit_count']>0){
						chk_cont = parseInt(chk_cont)+parseInt(ARR_result['visit_count']);
						if(chk_cont>1){
							top.fAlert("Visit code already exists for DOS "+dos);
						}
					}
				}
			});
		}
	}
}
function set_ref_phy(){
	set_reff_drop();
	if(medicare_imp!='' && server_name=='lehigh'){
		var Payer_id_pro = trim($('#Payer_id_pro').val());
		var Payer_id = trim($('#Payer_id').val());
		var phy_name = trim($('#primary_provider_id').val());
		var medicare_arr=medicare_imp.split(',');
		if($.inArray(Payer_id,medicare_arr)==-1 && $.inArray(Payer_id_pro,medicare_arr)==-1){
		}else{
			$('#reffer_physician').val(phy_name.replace('.',''));
			$('#refferingPhysician').val('');
			set_reff_drop();
		}
	}
}
 
function getTdTitle(id,div)
{
    title= $("#"+id + ' option:selected').attr('data-header');
	$('#'+div).attr('title', title);
}


function generateControleNo(pat_id)
{
	var control_no='';
	var pat_id=pat_id;
	var dos=$("#dos_id").val();
	if(dos)
	{
		var dosArr=dos.split('-');
		var year=dosArr[2].substring(2);
		control_no=dosArr[0]+dosArr[1]+year;
		
	}
	control_no+=pat_id;
	$("#control_no").val(control_no);			
}
function set_auto_state(){
	$(".auto_state_css").hide();
	var caseTypeText = $("#caseTypeText").val();
	var caseTypeTextArr=caseTypeText.split('-');
	if(caseTypeTextArr[0]=="Auto"){
		$(".auto_state_css").show();
	}
}
function set_rev_rate(id){
	var net_amt_chk="netAmt_"+id;
	var rev_rate_chk="rev_rate_"+id;
	var group_id = $('#groups').val().split('_');
	var chk_gro_id_arr = $('#chk_gro_id').val().split(',');
	if($.inArray(group_id[0],chk_gro_id_arr)==-1){
	}else{
		if(server_name=="sheepshead"){
			$('#'+rev_rate_chk).val("24/1408");
		}else if(server_name=='shoreline'){
			$('#'+rev_rate_chk).val("A3/"+$('#'+net_amt_chk).val());
		}
	}
	if($("#billing_type").val()==2 && server_name=='manahan'){
		$('#'+rev_rate_chk).val("A3/"+$('#'+net_amt_chk).val());
	}
}
function dxcode9_to_10(){
	var arrICD9 = {};
	$("input[name^='elem_assessment_dxcode'],input[name^='diagText_']").each(function(index, element) {
		id = $(this).attr('id');
		val = $(this).val();
		if(val != ""){
			arrICD9[index] = {'id':id,'val':val};
		}
	});
	
	if(Object.keys(arrICD9).length>0){
		$.ajax({
			url:'../../interface/chart_notes/requestHandler.php?elem_formAction=icd9_to_10&arrICD9='+JSON.stringify(arrICD9),
			dataType: "json",	
			success:function(obj){
				for (var key in obj) 
				{	jKey = "#"+key;
					arr = obj[key];
					value = arr['val'];
					multiple = arr['multiple'];
					laterality = 0;
					if(arr['laterality']!=''){
						laterality = arr['laterality'];
					}
					if($('#old_'+key)){
						$('#old_'+key).val($('#'+key).val());
					}
					$('#lit_'+key).val(laterality);
					if(value != null && value!="undefined")
					$(jKey).val(value);
					else
					$(jKey).val('');
					$(jKey).prop('title','');
					if(value != null && value!="undefined" && value!="" && (value.substr((value.length-1),1)=='-' || multiple == 1)){
						if(value.substr((value.length-1),1)=='-'){
							$(jKey).addClass('mandatory');
						}else{
							$(jKey);
						}
					}
				}
				crt_dx_dropdown_icd10();
			}
		});
	}
}
function set_icd10(action){
	if(action=="dos"){
		var dos_val = $('#dos_id').val();
		var dos_exp = dos_val.split('-');
		if(dos_exp.length>1){
			var dos_stg = dos_exp[2]+dos_exp[0]+dos_exp[1];
		}else{
			var dos_stg = dos_val.substr(4,4)+dos_val.substr(0,2)+dos_val.substr(2,2);
		}
		if(dos_stg>=20151001){
			if($('#enc_icd10').val()==1){return false;}
			$('#enc_icd10').val(1);
		}else{
			if($('#enc_icd10').val()==0){return false;}
			$('#enc_icd10').val(0);
		}
	}
	var chk_dx_val ="";
	if($('#enc_icd10').val()>0){
		ask = "Are you sure to change all the dx codes?";
	}else{
		ask = "Are you sure to empty all the dx codes?";
	}
	for(var j=1;j<=12;j++){
		if($('#diagText_'+j).val()!=""){
			chk_dx_val = $('#diagText_'+j).val();
		}
	}
	if(chk_dx_val!=""){
		top.fancyConfirm(ask,'',"window.top.fmain.set_dx_typeahead('yes');","window.top.fmain.set_dx_typeahead('no');");
	}else{
		set_dx_typeahead('yes');
	}
}
function set_dx_typeahead(action){
	if(action=='no'){
		if($('#enc_icd10').val()>0){
			$('#enc_icd10').val(0);
		}else{
			$('#enc_icd10').val(1);
		}
		return false;
	}
	if(action=='yes'){
		if($('#enc_icd10').val()>0){
			dxcode9_to_10();
		}else{
			for(var j=1;j<=12;j++){
				$('#diagText_'+j).val('');
			}
			crt_dx_dropdown();
		}
	}
	if($('#enc_icd10').val()>0){
		$(".dx_box_12").typeahead('destroy');
		getDataFilePath  = zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=getICD10Data";
		$(".dx_box_12" ).each(function(indx){bind_autocomp($(this), getDataFilePath); });
	}else{
		$(".dx_box_12" ).each(function(indx){ bind_autocomp($(this), ''); });
		$(".dx_box_12").typeahead({source:customarrayDiag});
	}
}
function ins_hx(){
	$.ajax({
		url: "../../interface/patient_info/ajax/insurance/act_exp_open_insu_case.php",
		success: function(resp){
			resp = jQuery.parseJSON(resp);
			if(typeof(resp.html)!= "undefined"){
				show_modal('ins_hx_id','Patient All Insurance History', resp.html,'','','modal-lg');
			}			
		}
	});
}

function postCharges_pmt(){
	var print_ch_arr1=new Array();
	var f=1;
	var tot_print_arr = $("input[name^='chkbx']").length;
	for(f=0;f<=tot_print_arr;f++){		
		if($('#chkbx'+f).length>0){
			var print_ch_arr=$('#chkbx'+f).val();
			print_ch_arr1.push(print_ch_arr);
		}
	}
	if(print_ch_arr1!=""){
		print_ch_arr1=print_ch_arr1+',';
	}

	if($("#encounter_id").length>0){
		if($("#encounter_id").val()>0){
			var eId=$("#encounter_id").val();
		}
	}else if($("#enc_id_read").length>0){
		if($("#enc_id_read").val()>0){
			var eId=$("#enc_id_read").val();
		}
	}
	
	top.popup_win("../../interface/accounting/postCharges.php?eId="+eId+"&chld_ids="+print_ch_arr1,'width=670,height=355,top=250,left=400');
}

function tx_balance(){
	/*$("#paidBy").val("Patient");
	$("#paymentClaims").val("Tx Balance");
	$("#paymentClaims").selectpicker('refresh');
	paymentModeFn();
	$("#who_paid_td,#insCoNames,#cas_box,#changeMethod,#checkRow,#creditCardRow,#refund_note,#adjust_other,#cr_deb_note,#write_off_box,#adj_off_box,#insCoNames_ref,#discount_box,#statement_pmt_td").hide();
	*/
}

function credit_tbl_id(eid){
	var url = "makePayment.php?encounter_id="+eid+"&debit_manage=yes";
	window.location.href=url;
}

function copy_method(seq){
	var method_pattern = /Update Allow Amt/;
	var method_exists = false;
	if(seq>1){
		for(f=1;f<seq;f++){
			method_exists = method_pattern.test($("#payment_method_"+seq).val());
			if($("#payment_method_"+f).val()!="" && $("#payment_method_"+seq).val()==""){
				$("#payment_method_"+seq).val($("#payment_method_"+f).val()).change();
				$("#check_cc_no_"+seq).val($("#check_cc_no_"+f).val());
				$("#paid_date_"+seq).val($("#paid_date_"+f).val());
				$("#facility_id_"+seq).val($("#facility_id_"+f).val());
			}else if(method_exists==true){
				if($("#payment_method_"+f).val()!=""){
					$("#payment_method_"+seq).val($("#payment_method_"+f).val()).change();
					$("#check_cc_no_"+seq).val($("#check_cc_no_"+f).val());
					$("#paid_date_"+seq).val($("#paid_date_"+f).val());
					$("#facility_id_"+seq).val($("#facility_id_"+f).val());
				}
			}
		}
	}
}

function claimChange(){
	var objClaims = $("#paymentClaims");
	var paid_by=$("#paidBy").val();
	$("#who_paid_td").show();
	if(objClaims.val() == "Paid" || objClaims.val() == "Deposit" || objClaims.val() == "Interest Payment" || objClaims.val() == "Negative Payment"){		
		var ref_met_chg='<select name="paymentMode" id="paymentMode" class="selectpicker" data-width="100%" onChange="return showRow(this.value);"><option value="Cash">Cash</option><option value="Check" selected>Check</option><option value="Credit Card">Credit Card</option><option value="EFT">EFT</option><option value="Money Order">Money Order</option><option value="VEEP">VEEP</option></select>';
		$("#pay_all_meth").html(ref_met_chg);

		var paymentMethod = $("#paymentMode").val();
		if($("#statement_pmt_td").length>0){
			if($("#statement_count").val()>0 && $("#paidBy").val()=='Patient'){
				$("#statement_pmt_td").show();
			}
		}
		$("#changeMethod").show();
		$("#write_off_box").hide();
		$("#discount_box").hide();
		
		if(paymentMethod == 'Check'){
			$("#checkRow").show();
			$("#write_off_box").hide();
		}
		else if(paymentMethod == 'Credit Card'){
			$("#creditCardRow").show();
		}
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#pay_all_meth").show();
		$("#creditCardRow").hide();
		$("#write_off_box").hide();
		$("#adj_off_box").hide();
		$("#insCoNames_ref").hide();
		if(objClaims.val() == "Paid" && paid_by=="Insurance"){
			$("#cas_box").show();
		}else{
			$("#cas_box").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
		$("#paymentMode").selectpicker('refresh');
	}else if(objClaims.val() == "Write Off"){
		$("#write_off_box").show();
		if(paid_by=="Insurance"){
			$("#cas_box").show();
		}else{
			$("#cas_box").hide();
		}
		$("#discount_box").hide();
		$("#changeMethod").hide();
		$("#checkRow").hide();
		$("#creditCardRow").hide();	
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#adj_off_box").hide();
		$("#insCoNames_ref").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td")){
			$("#ci_co_payments_td").hide();
		}
	}else if(objClaims.val() == "Adjustment" || objClaims.val() == "Over Adjustment"){
		$("#write_off_box").hide();
		$("#cas_box").hide();
		$("#discount_box").hide();
		$("#changeMethod").show();
		$("#checkRow").hide();
		$("#creditCardRow").hide();	
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#adj_off_box").show();
		$("#insCoNames_ref").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		var paymentMethod = $("#paymentMode").val();
		if(paymentMethod == 'Check'){
			$("#checkRow").show();
		}
		if($("#patient_pre_payments_td")){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td")){
			$("#ci_co_payments_td").hide();
		}
	}else if(objClaims.val() == "Refund"){		
	
		var ref_met_chg='<select name="paymentMode" id="paymentMode" class="selectpicker" data-width="100%" onChange="return showRow(this.value);"><option value="Cash">Cash</option><option value="Check" selected>Check</option><option value="Credit Card">Credit Card</option><option value="EFT">EFT</option><option value="Money Order">Money Order</option><option value="VEEP">VEEP</option></select>';
		$("#pay_all_meth").html(ref_met_chg);
		
		$("#changeMethod").show();
		var paymentMethod = $("#paymentMode").val();
		if(paymentMethod == 'Check'){
			$("#checkRow").show();
		}
		else if(paymentMethod == 'Credit Card'){
			$("#creditCardRow").show();
		}
		else if(paymentMethod == 'Cash'){
			$("#checkRow").hide();
		}
		$("#refund_note").show();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#creditCardRow").hide();
		$("#pay_all_meth").show();
		$("#write_off_box").hide();
		$("#discount_box").hide();
		$("#adj_off_box").hide();
		$("#cas_box").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
		$("#insCoNames_ref").show();
		$("#paymentMode").selectpicker('refresh');
	}else if(objClaims.val() == "Returned Check"){		
	
		var ref_met_chg='<select name="paymentMode" id="paymentMode" class="selectpicker" data-width="100%" onChange="return showRow(this.value);"><option value="Cash">Cash</option><option value="Check" selected>Check</option><option value="Credit Card">Credit Card</option><option value="EFT">EFT</option><option value="Money Order">Money Order</option><option value="VEEP">VEEP</option></select>';
		$("#pay_all_meth").html(ref_met_chg);
		
		$("#changeMethod").show();
		var paymentMethod = $("#paymentMode").val();
		if(paymentMethod == 'Check'){
			$("#checkRow").show();
		}
		else if(paymentMethod == 'Credit Card'){
			$("#creditCardRow").show();
		}
		else if(paymentMethod == 'Cash'){
			$("#checkRow").hide();
		}
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#creditCardRow").hide();
		$("#pay_all_meth").show();
		$("#write_off_box").hide();
		$("#discount_box").hide();
		$("#adj_off_box").hide();
		$("#cas_box").hide();
		$("#insCoNames_ref").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
		$("#paymentMode").selectpicker('refresh');
	}
	else if(objClaims.val() == "Debit_Credit"){		
		var paymentMethod = $("#paymentMode").val();
		$("#changeMethod").hide();
		if(paymentMethod == 'Check'){
			$("#checkRow").hide();	
		}
		else if(paymentMethod == 'Credit Card'){
			$("#creditCardRow").hide();
		}
		$("#refund_note").hide();
		$("#adjust_other").show();
		$("#cr_deb_note").show();
		$("#creditCardRow").hide();
		$("#write_off_box").hide();
		$("#discount_box").hide();
		$("#adj_off_box").hide();
		$("#cas_box").hide();
		$("#insCoNames_ref").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
	}else if(objClaims.val() == 'check_in_out'){
		$("#paidBy").val("Patient");
		paymentModeFn();
		$("#ci_co_payments_td").show();
		$("#changeMethod").hide();
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#write_off_box").hide();
		$("#discount_box").hide();
		$("#adj_off_box").hide();
		$("#pay_all_meth").hide();
		$("#cas_box").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		$("#paidBy").selectpicker('refresh');
	}else if(objClaims.val() == 'Discount'){
		$("#discount_box").show();
		$("#changeMethod").hide();
		$("#checkRow").hide();
		$("#creditCardRow").hide();	
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#write_off_box").hide();
		$("#adj_off_box").hide();
		$("#insCoNames_ref").hide();
		$("#cas_box").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
	}else if(objClaims.val() == 'Denied'){
		if(paid_by=="Insurance"){
			$("#cas_box").show();
		}else{
			$("#cas_box").hide();
		}
		$("#changeMethod").hide();	
		$("#checkRow").hide();
		$("#creditCardRow").hide();	
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#write_off_box").hide();
		$("#adj_off_box").hide();
		$("#insCoNames_ref").hide();
		$("#discount_box").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
	}else if(objClaims.val() == 'Tx Balance'){
		$("#paidBy").val("Patient");
		paymentModeFn();
		$("#who_paid_td").hide();
		$("#cas_box").hide();
		$("#changeMethod").hide();
		$("#checkRow").hide();
		$("#creditCardRow").hide();	
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#write_off_box").hide();
		$("#adj_off_box").hide();
		$("#insCoNames_ref").hide();
		$("#discount_box").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
		$("#paidBy").selectpicker('refresh');
	}else if(objClaims.val() == 'Update Allow Amt'){
		$("#paidBy").val("Patient");
		paymentModeFn();
		$("#who_paid_td").hide();
		$("#cas_box").hide();
		$("#changeMethod").hide();
		$("#checkRow").hide();
		$("#creditCardRow").hide();	
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#write_off_box").hide();
		$("#adj_off_box").hide();
		$("#insCoNames_ref").hide();
		$("#discount_box").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td")){
			$("#ci_co_payments_td").hide();
		}
		$("#paidBy").selectpicker('refresh');
	}else if(objClaims.val() == 'Prepayment'){
		$("#paidBy").val("Patient");
		paymentModeFn();
		$("#who_paid_td").show();
		$("#cas_box").hide();
		$("#changeMethod").hide();
		$("#checkRow").hide();
		$("#creditCardRow").hide();	
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#write_off_box").hide();
		$("#adj_off_box").hide();
		$("#insCoNames_ref").hide();
		$("#discount_box").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").show();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
		$("#paidBy").selectpicker('refresh');
	}else{
		$("#cas_box").hide();
		$("#changeMethod").hide();
		$("#checkRow").hide();
		$("#creditCardRow").hide();	
		$("#refund_note").hide();
		$("#adjust_other").hide();
		$("#cr_deb_note").hide();
		$("#write_off_box").hide();
		$("#adj_off_box").hide();
		$("#insCoNames_ref").hide();
		$("#discount_box").hide();
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt_td").hide();
		}
		if($("#patient_pre_payments_td").length>0){
			$("#patient_pre_payments_td").hide();
		}
		if($("#ci_co_payments_td").length>0){
			$("#ci_co_payments_td").hide();
		}
	}
}
function outstanding_pat(val){
	var chkboxArr = $("input[name^='chkbx']").length;
	var j = 1;
	var deb_chld_id = 0;
	var deb_amt = 0;
	var deb_ins_type= 0;
	var no_ovr_paid = 0;
	var ovr_paid_grt = 0;
	var tot_chk_ovr = 0;
	if(chkboxArr>0){
		$("input[name^='chkbx']").each(function(index, element){
			if($('#chkbx'+j).is(':checked')){
				if($("#payment_method_"+j).val()=="Debit"){
					if($("#overPayments_chk"+j).val()>0){
						no_ovr_paid=1;
						if($("#pri_paid_"+j).val()>0){
							deb_amt = $("#pri_paid_"+j).val();
							deb_ins_type=1;
						}else if($("#sec_paid_"+j).val()>0){
							deb_amt = $("#sec_paid_"+j).val();
							deb_ins_type=2;
						}else if($("#ter_paid_"+j).val()>0){
							deb_amt = $("#ter_paid_"+j).val();
							deb_ins_type=3;
						}else if($("#pat_paid_"+j).val()>0){
							deb_amt = $("#pat_paid_"+j).val();
							deb_ins_type=0
						}
						if(deb_amt>0){
							tot_chk_ovr++;
							deb_chld_id = $('#chkbx'+j).val();
							if(parseFloat($("#overPayments_chk"+j).val())>=parseFloat(deb_amt)){
								ovr_paid_grt=1;
							}
						}
					}
				}
			}
			j++;
		});
	}
	
	if(no_ovr_paid==0){
		top.fAlert("Please enter the over paid amount and select the method as Debit");
		return false;
	}else if(deb_amt==0){
		top.fAlert("Debit amount can not be zero");
		return false;
	}else if(ovr_paid_grt==0){
		top.fAlert("Debit amount can not be greater than over paid amount");
		return false;
	}else if(tot_chk_ovr>1){
		top.fAlert("Please select only one over paid procedure");
		return false;
	}
	
	var patient_id = $('#patient_id').val();
	top.popup_win('credit_transactions.php?deb_patient_id='+patient_id+'&deb_chld_id='+deb_chld_id+'&deb_amt='+deb_amt+'&deb_ins_type='+deb_ins_type);
}

function paymentModeFn(){
	var whoWillPay = $("#paidBy").val();
	if(whoWillPay=='Patient'){
		$("#paymentMode").val('Cash');
		$("#checkRow,#insCoNames,#creditCardRow,#cas_box").hide();
		if($("#cas_code1").length>0){$("#cas_code1").val("");}
		if($("#cas_code").length>0){$("#cas_code").val("");}
		if($("#statement_pmt_td").length>0){
			if($("#statement_count").val()>0){
				if($("#paymentClaims").val()=='Returned Check'){
					$("#statement_pmt").prop("checked",false);
					$("#statement_pmt_td").hide()
				}else{
					$("#statement_pmt_td").show();
				}
			}
		}
	}
	if(whoWillPay=='Res. Party'){
		$("#paymentMode").val('Check');
		$("#checkRow").show();
		$("#insCoNames,#creditCardRow,#cas_box").hide();
		if($("#cas_code1").length>0){$("#cas_code1").val("");}
		if($("#cas_code").length>0){$("#cas_code").val("");}
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt").prop("checked",false);
			$("#statement_pmt_td").hide()
		}
	}
	if(whoWillPay=='Insurance'){
		$("#paymentMode").val('Check');
		$("#checkRow,#insCoNames").show();
		$("#creditCardRow,#cas_box").hide();
		if($("#cas_code1").length>0){$("#cas_code1").val("");}
		if($("#cas_code").length>0){$("#cas_code").val("");}
		if($("#paymentClaims").val()=="Paid" || $("#paymentClaims").val()=="Write Off" || $("#paymentClaims").val()=="Denied"){
			$("#cas_box").show();
		}
		if($("#statement_pmt_td").length>0){
			$("#statement_pmt").prop("checked",false);
			$("#statement_pmt_td").hide()
		}
	}
	$("#paymentMode").selectpicker('refresh');
}
function showRow(val){
	var show = val;
	var check_pattern = /Check/;
	var check_exists = check_pattern.test(val);
	if(show=="Cash"){
		$("#checkRow").hide();
		$("#creditCardRow").hide();
	}
	if(show=="Check" || show=="EFT" || show=="Money Order" || show=="VEEP" || check_exists==true){
		$("#checkRow").show();
		$("#creditCardRow").hide();
	}
	if(show=="Credit Card"){
		$("#checkRow").hide();
		$("#creditCardRow").show();
	}
}
function cCCompany(){
	if($("#creditCardCoId").val()=='Others'){
		$("#creditCardCoTd").html('');
		var ccCo = '<input width="60" type="text" name="creditCardCo" id="creditCardCoId" class="text_10" size="10" >';
		document.getElementById("creditCardCoTd").innerHTML=ccCo;
		top.fmain.document.getElementById('creditCardCoTd').innerHTML = '<input width="60" type="text" name="creditCardCo" id="creditCardCoId" class="text_10" size="10" >';
	}
}
function printReceipt_all(){
	if($("#encounter_id").length>0){
		if($("#encounter_id").val()>0){
			var eId=$("#encounter_id").val();
		}
	}else if($("#enc_id_read").length>0){
		if($("#enc_id_read").val()>0){
			var eId=$("#enc_id_read").val();
		}
	}
	
	var print_ch_arr1=new Array();
	var f=1;
	$("input[name^='chkbx']").each(function(index, element){
		if($('#chkbx'+f).length>0){
			var print_ch_arr=$('#chkbx'+f).val();
			print_ch_arr1.push(print_ch_arr);
		}
		f++;
	});
	
	if(!ch_id){
		var ch_id=print_ch_arr1;
	}
	var parWidth = document.body.clientWidth;
	var parHeight = document.body.clientHeight+80;
	top.popup_win("../accounting/receipt.php?eId="+eId+'&ch_id='+ch_id,'width='+parWidth+',height='+parHeight+',top=10,left=40,scrollbars=yes,resizable=yes');
}

function applyFn(a,b,c){
	if($("#acc_view_pay_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	var alrt=false;
	if($('#commentsArea').length>0){
		if($('#paymentClaims').length>0){
			var chk_paymentClaim = $('#paymentClaims').val();
		}
		if($('#paidAmountNow').val() > 0 || $('#copayAmount').val() > 0 || chk_paymentClaim == 'Tx Balance'){
		}else{	
			if($('#commentsArea').val()!=""){
				top.$("#"+a).prop("disabled",true);
				top.$("#"+b).prop("disabled",true);
				if(c=="print"){
					$("#apply").val(b);
				}else{
					$("#apply").val(a);
				}
				document.makePaymentFrm.submit();
				alrt=true;
			}
		}
	}
	if(alrt==false){
		var flag = 0;
		var arr_apply_copay=new Array(99212,99213,99214,99215,99201,99202,99203,99204,99205,99241,99242,99243,99244,99245,92012,92013,92014,92002,92003,92004);
		
		var msg = "Please Enter following information\n";
		if(parseInt($("#copayAmount").val())>parseInt($("#copayAmount_hid").val()) && parseInt($("#copayAmount").val())>0){
			if($('#encounter_id').val()>0){
				top.fAlert("Copay Amount can not greater than "+$("#copayAmount_hid").val());
				return false;
			}
		}
		
		var paidBy = $('#paidBy').val();
		if($("#paymentMode").length>0){
			var f1 = $("#paymentMode").val();
		}
		
		if($("#paymentClaims").length>0){
			var paymentClaim = $("#paymentClaims").val();
		}
		
		if(paymentClaim == 'check_in_out'){
			var sel_patient_pre_payments_amt=0;
			if($("#sel_ci_co_payments").length>0){
				sel_ci_co_payments_amt = $("#sel_ci_co_payments option:selected").text();
			}
			var un_post_amount_chk = $("#un_post_amount_chk").val();
			var paidAmountNow_chk = $("#paidAmountNow").val()+$("#copayAmount").val();
			if(parseInt(paidAmountNow_chk)>parseInt(sel_ci_co_payments_amt)){
				top.fAlert("Paid amount can not be greater than Check In/Out Payment");
				return false;
			}
		}
		
		if(paymentClaim == 'Prepayment'){
			var sel_patient_pre_payments_amt=0;
			if($("#sel_ci_co_payments").length>0){
				sel_patient_pre_payments_amt = $("#sel_patient_pre_payments option:selected").text();
			}
			var paidAmountNow_chk = $("#paidAmountNow").val()+$("#copayAmount").val();
			if(parseInt(paidAmountNow_chk)>parseInt(sel_patient_pre_payments_amt)){
				top.fAlert("Paid amount can not be greater than Prepayment.");
				return false;
			}
		}
		
		if(paidBy=='Insurance'){
			if($("#insProviderCoId").length>0){
				if($("#insProviderCoId").val()==''){
					top.fAlert("Please select insurance company, N/A is allowed.")
					return false;
				}
			}
			if($("#insProviderCoId").length>0){
				var insCoIS = $("#insProviderCoId option:selected").index();
				var insId = $("#insProviderCoId").val();
				var insDash = insId.indexOf("-");
				if(insDash>0){
					var insId_exp = insId.split("-");
					$("#insSelected").val(insId_exp[1]);
					var insProviderName = insId_exp[0];
				}else{
					$("#insSelected").val(insCoIS);
				}
				
				
			}
			if(document.getElementById("insProviderCoId")){
				var insCoIS = document.getElementById("insProviderCoId").selectedIndex;
				var insId = document.getElementById("insProviderCoId").value;
				var insDash = insId.indexOf("-");
				if(insDash>0){
					var insId_exp = insId.split("-");
					document.getElementById("insSelected").value = insId_exp[1];
					var insProviderName = insId_exp[0];
				}else{
					document.getElementById("insSelected").value = insCoIS;
				}
			}
		}
		
		if(f1=="Cash"){
			if($("#paidBy").length>0)
				var paidBy = $("#paidBy").val();
			if($("#date1").length>0)
				var date1 = $("#date1").val();
			if($("#paidAmountId").length>0)
				var paidAmt = $("#paidAmountId").val();
	
			if(date1==""){ msg+="-Paid Date\n";	++flag; }
			if(paidBy==""){ msg+="-Paid By\n";	++flag; }
			if(flag>0){
				top.fAlert(msg)
				return false;
			}
	
		}
		if(paymentClaim == 'Paid' || paymentClaim == 'Deposit' || paymentClaim == "Interest Payment" || paymentClaim == "Refund"){
			if(f1=="Check" || f1=="EFT" || f1=="Money Order" || f1=="VEEP"){		
				if($("#checkNoId").length>0)
					var chkNo = $("#checkNoId").val();
				if($("#date1").length>0)
					var date1 = $("#date1").val();
				if($("#paidBy").length>0)
					var paidBy = $("#paidBy").val();
				if($("#paidAmountId".length>0))
					var paidAmt = $("#paidAmountId").val();
		
				if(chkNo==""){ msg+="-Check No\n";	++flag; }
				if(date1==""){ msg+="-Paid Date\n";	++flag; }
				if(paidBy==""){ msg+="-Paid By\n";	++flag; }
				
				if(flag>0){
					top.fAlert(msg)
					return false;
				}
			}
		}
		
		if(f1=="Credit Card"){
			if(paymentClaim != 'Debit_Credit' && paymentClaim != 'Refund'){
				var creditCNo = $("#cCNoId").val();
				var creditCardCo = $("#creditCardCoId").val();
				var date1 = $("#date1").val();
				var expDate = $("#date2").val();
				var paidBy = $("#paidBy").val();
				var paidAmt = $("#paidAmountId").val();
		
				if(creditCNo==""){ msg+="-Credit card #"; ++flag; }
				if(creditCardCo==""){ msg+="-Credit card type.\n"; ++flag; }
				if(paidBy==""){ msg+="-Paid By\n"; ++flag; }
				if(date1==""){ msg+="-Paid Date\n"; ++flag; }
				if(flag>0){
					top.fAlert(msg)
					return false;
				}				
			}
	
		}
	
		var adjust_other = $("#adjust_other_txt").val();
		if(paymentClaim == 'Debit_Credit'){
			var chkboxArr = $("input[name^='chkbx']").length;
			var j = 1;
			var con_deb=0;
			var bal_chk_for_copay_chk_first=0;
			if(chkboxArr>0){
				if(adjust_other!='other_procedure'){
					for(i=0;i<chkboxArr;i++,j++){	
						if($("#chkbx"+j).is(':checked')){
							con_deb++;
							var payNewVal = $("#payNew"+j).val();
							var overPayment_chk = $("#overPayments_chk"+j).val();
							var bal_chk_for_copay_chk = $("#bal_chk_for_copay"+j).val();
							
							if(parseFloat(overPayment_chk)>0){
								var chk_ovr_pay_deb = $("#payNew"+j).val();
								if((parseFloat(payNewVal)>parseFloat(overPayment_chk))){
									top.fAlert("Debit amount can not be greater than overpaid amount");
									return false;
								}else{
									bal_chk_for_copay_chk_first = $("#payNew"+j).val();
								}
							}else{
								var tot_val=$("#paidAmountNow").val();
								if(con_deb>1 && adjust_other!='other_procedure'){
									if((parseFloat(tot_val)>parseFloat(bal_chk_for_copay_chk_first))){
										top.fAlert("Credit amount can not be greater than debit amount");
										return false;
									}	
								}
							}
							if(parseFloat(overPayment_chk)>0){
							}else{
								if(parseFloat(payNewVal)>parseFloat(bal_chk_for_copay_chk)){
									top.fAlert("Credit amount can not be greater than new balance");
									return false;
								}
							}	
							if(parseFloat(chk_ovr_pay_deb)>0){
								var tot_val_chk=$("#paidAmountNow").val();
								if(parseFloat(tot_val_chk)>parseFloat(chk_ovr_pay_deb)){
									top.fAlert("Credit amount can not be greater than debit amount");
									return false;
								}
							}
						}
					}
					if(con_deb<2 && adjust_other!='other_procedure'){
						top.fAlert("Please select procedure to credit");
						return false;
					}
				}	
			}
		}
		if(paymentClaim == 'Write Off' && $("#write_off_code").val()==""){
			top.fAlert("Please select the Write off code");
			return false;
		}
		if(paymentClaim == 'Discount' && $("#discount_code").val()==""){
			top.fAlert("Please select the Discount code");
			return false;
		}
		if(paymentClaim == 'Tx Balance'){
			var chkboxArr = $("input[name^='chkbx']").length;
			var j = 1;
			var pri_due = 0;
			var sec_due = 0;
			var tri_due = 0;
			var pat_due = 0;
			var tot_bal=0;
			var tx_alert="";
			if(chkboxArr>0){
				$("input[name^='chkbx']").each(function(index, element){
					if($('#chkbx'+j).is(':checked')){
						if($("#pri_due_"+j).length>0){
							pri_due = $("#pri_due_"+j).val();
						}
						if($("#sec_due_"+j).length>0){
							sec_due = $("#sec_due_"+j).val();
						}
						if($("#tri_due_"+j).length>0){
							tri_due = $("#tri_due_"+j).val();
						}
						pat_due = $("#pat_due_"+j).val();
						
						var tot_bal = parseFloat(pri_due)+parseFloat(sec_due)+parseFloat(tri_due)+parseFloat(pat_due);
						var new_bal = $("#bal_chk_for_copay"+j).val();
						var Proc_code = $("#cptIdTd"+j).html();
						if(parseFloat(tot_bal.toFixed(2))>parseFloat(new_bal)){
							tx_alert += Proc_code.trim()+" procedure Ins Due+Pat Due can not be greater than New Balance.\n";
						}else if(parseFloat(tot_bal.toFixed(2))<parseFloat(new_bal)){
							tx_alert += Proc_code.trim()+" procedure Ins Due+Pat Due can not be less than New Balance.\n";
						}
					}
					j++;
				});
			}
			if(tx_alert!=""){
				top.fAlert(tx_alert);
				return false;
			}
		}
        
        var no_pos_device=false;
        if(typeof($('#tsys_device_url'))!='undefined' && $('#tsys_device_url').val()=='no_pos_device') {
            no_pos_device=true;
        }
        
        $("#valueBtn").val(c);
        if(typeof(pos_device)!='undefined' && pos_device && no_pos_device==false) {
            totalAmt=calccRefundAmt();
        }

        /* IM-7563:- Unable to Post Refunds  
        if(typeof(totalAmt)!='undefined' && totalAmt>0 && typeof(pos_device)!='undefined' && pos_device && no_pos_device==false) {
            return_transaction(totalAmt);
        } else {
            pos_submit_frm();
        }
        */
       
        pos_submit_frm();
	}
		
}

function pos_submit_frm() {
    var a="applySubmit";
    var b="applyRecieptSubmit";
    var c=$("#valueBtn").val();
    
    top.$("#"+a).prop("disabled",true);
    top.$("#"+b).prop("disabled",true);
    if(c=="print"){
        $("#apply").val(b);
    }else{
        $("#apply").val(a);
    }
    document.makePaymentFrm.submit();
}

function calccRefundAmt() {
    var j = 1;
    var totalAmt=0;
    var temptotalAmt=0;
    $("input[name^='chkbx']").each(function(index, element){
        if($('#chkbx'+j).is(':checked')){
            var paymode=$("#payment_method_"+j).val();
            
            if(paymode.indexOf('Refund - Credit Card')!= -1){
                var pri_paid_amt=0;
                var sec_paid_amt=0;
                var ter_paid_amt=0;
                var pat_paid_amt=0;
                if($("#pri_paid_"+j).length>0 && $("#pri_paid_"+j).val()>0){
                    pri_paid_amt=$("#pri_paid_"+j).val();
                }
                if($("#sec_paid_"+j).length>0 && $("#sec_paid_"+j).val()>0){
                    sec_paid_amt=$("#sec_paid_"+j).val();
                }
                if($("#ter_paid_"+j).length>0 && $("#ter_paid_"+j).val()>0){
                    ter_paid_amt=$("#ter_paid_"+j).val();
                }
                if($("#pat_paid_"+j).length>0 && $("#pat_paid_"+j).val()>0){
                    pat_paid_amt=$("#pat_paid_"+j).val();
                }

                totalAmt=parseFloat(pri_paid_amt)+parseFloat(sec_paid_amt)+parseFloat(ter_paid_amt)+parseFloat(pat_paid_amt);
                
                var payModeArr=paymode.split(' - ');
                
                var paymentClaim=$.trim(payModeArr[0]);
                var f1=$.trim(payModeArr[1]);
                var cardType=$.trim(payModeArr[2]);
                
                totalAmt=parseFloat(totalAmt);
                temptotalAmt=totalAmt+temptotalAmt;
                totalAmt=temptotalAmt;
            }
        }
        j++;
    });
    
    return totalAmt;
}

function expDate(id){
    id=id?id:'date2';
	var expireDate = $("#"+id).val();
	if(expireDate!=''){
		var strLen = expireDate.length;
		var posSlash = expireDate.indexOf("/");
		var posDash = expireDate.indexOf("-");
		if((posSlash == 2) || (posDash == 2)){
			var formatExp = true;
		}else{
			if((posSlash == 1) || (posDash == 1)){
				var expireDate = '0'+expireDate;
				
			}else{
				var formatExp = false;
			}
		}
		var mm = expireDate.substr(0,2);
		var yy = expireDate.substr(3);
		
		if((formatExp==false) || (mm>12) || (mm<=0) || (yy<=0)){
			if(strLen==7){
				if(formatExp==false){
					top.fAlert("Please enter CC Exp date in the format mm/yy.")
					$("#"+id).val('');
				}else{
					if((mm>12) || (mm<=0) || (yy<=0)){
						top.fAlert("Please enter CC Exp date in the format mm/yy.")
						$("#"+id).val('');
					}
					yySess = expireDate.substr(3, 2);
					if(yySess!=20){
						top.fAlert("Please enter year correctly as yy.")
						$("#"+id).val('');
					}
				}
			}else{
				top.fAlert("Please enter CC Exp date in the format mm/yy.")
				$("#"+id).val('');
			}
		}
	}
}

function show_tran_rec(obj){
	var val = $(obj).text();
	if(val=="All"){
		$('.deleted').addClass('hide');
		$(obj).text('Active');
	}else{
		$('.deleted').removeClass('hide');
		$(obj).text('All');
	}
}

function changeAmt(copy,seq){
	var pend_copay=0;
	if($('#chkbx'+seq).is(':checked')==true){
		pend_copay=copy-$('#tot_copay_paid_'+seq).val();
		if(pend_copay>0){
			pend_copay = pend_copay.toFixed(2);
			$('#pat_paid_'+seq).val(pend_copay);
		}else{
			$('#pat_paid_'+seq).val(0.00);
		}
	}else{
		$('#pat_paid_'+seq).val(0.00);
	}
}

function deductChange(s){
	$("#chkbx"+s).attr("checked",true);
}

function paymentChange_bydeduct(s,deduct_amt,old_deductamt){
	if(refractionChk(s)){
		if(isNaN(deduct_amt)){
			top.fAlert("Please enter only numeric values.");
			return false;
		}else{	
			if(parseFloat(deduct_amt)>parseFloat(old_deductamt)){
				if(deduct_amt>0){
					if($("#payNew"+s).val()>0 || $("#chk_any_allow").val()>0){
						$("#paymentClaims").val("Paid");
					}else{
						$("#paymentClaims").val("Deductible");
						$("#changeMethod").hide();
					}
				}else{
					if($("#payNew"+s).val()>0 || $("#chk_any_allow").val()>0){
						$("#paymentClaims").val("Paid");
					}else{
						$("#paymentClaims").val("Paid");
						$("#changeMethod").show();
					}
				}
				$('#paymentClaims').selectpicker('refresh');
			}else{
				$("deductibleText"+s).val(old_deductamt);
				return false;
			}
		}	
	}
}
function ChargesChange(s){
	$("#approvedText"+s).val($("#totalFee"+s).val());
}
function paymentChange_all(s,type){
	if(type=="allow"){
		if($("#payment_method_"+s).val()==""){
			if($("#pri_paid_"+s).length){
				$("#payment_method_"+s).val("Update Allow Amt - Primary");
			}else{
				$("#payment_method_"+s).val("Update Allow Amt - Patient");
			}
		}
		checkChkBox(s,'approvedText');
		checkPaymentBox(s);
	}
	/*if($("#paymentClaims").val()!='Prepayment'){
		if($("#payNew"+s).val()>0 || type=="allow"){
			$("#paymentClaims").val("Paid");
			$("#changeMethod").show();
			$("#who_paid_td").show();
			$("#chk_any_allow").val(1);
			$('#paymentClaims').selectpicker('refresh');
		}
	}*/
}

function checkChkBox(s,id){
	var method_pattern = /Update Allow Amt/;
	var method_exists = false;
	method_exists = method_pattern.test($("#payment_method_"+s).val());
	if(id=="pmt_notes_"){
		$("#chkbx"+s).prop('checked',true);
	}else if(id=="payment_method_" || id=="approvedText"){
		$("#chkbx"+s).prop('checked',true);
		if($("#payment_method_"+s).val().indexOf("Cash") != -1){
			$("#check_cc_no_"+s).val('');
		}else if($("#payment_method_"+s).val().indexOf("Check") != -1 || $("#payment_method_"+s).val().indexOf("EFT") != -1 || $("#payment_method_"+s).val().indexOf("Money Order") != -1 || $("#payment_method_"+s).val().indexOf("VEEP") != -1){
			if($("#default_check_no").val()!=""){
				$("#check_cc_no_"+s).val($("#default_check_no").val());
			}
		}
		var parent_label="";
		if($("#payment_method_"+s).val().indexOf("Write Off") != -1 || method_exists==true){
			parent_label = "Write off Code";
			var wrt_drop=new Array("Discount Code","Adj Code");
			var wrt_drop_val="_wrt";
		}else if($("#payment_method_"+s).val().indexOf("Discount") != -1){
			parent_label = "Discount Code";
			var wrt_drop=new Array("Write off Code","Adj Code");
			var wrt_drop_val="_dis";
		}else if($("#payment_method_"+s).val().indexOf("Adjustment") != -1){
			parent_label = "Adj Code";
			var wrt_drop=new Array("Write off Code","Discount Code");
			var wrt_drop_val="_adj";
		}else{
			parent_label = "";
			var wrt_drop=new Array("Write off Code","Discount Code","Adj Code");
			var wrt_drop_val="_cas";
		}
		
		$("#write_off_code_"+s).val('');
		if(parent_label!=""){
			$("#write_off_code_"+s).find('optgroup[label^="'+parent_label+'"]').removeClass('hide');
			$("#write_off_code_"+s).find('optgroup[label^="'+parent_label+'"] option').prop('disabled', false);
			if(parent_label=="Write off Code"){
				$("#write_off_code_"+s).find('optgroup[label^="'+parent_label+'"] option[value^="'+$('#default_write_code').val()+wrt_drop_val+'"]').prop('selected', true);
			}
			if(parent_label=="Adj Code"){
				$("#write_off_code_"+s).find('optgroup[label^="'+parent_label+'"] option[value^="'+$('#default_adj_code').val()+wrt_drop_val+'"]').prop('selected', true);
			}
		}
		$.each(wrt_drop, function( index, value ){
			var dataVal = value;
			$("#write_off_code_"+s).find('optgroup[label^="'+dataVal+'"] option').prop('disabled', true);
			$("#write_off_code_"+s).find('optgroup[label^="'+dataVal+'"]').addClass('hide');
		});
		
		$("#write_off_code_"+s).selectpicker('refresh');
	}else{
		var chkPayValNull = $("#"+id+s).val();
		var checkValueType = checkNaN(chkPayValNull, id+s, 0);
		if($("#chkbx"+s).length>0){
			$("#chkbx"+s).prop('checked',true);
		}
	}
	acc_total();
}
function checkNaN(str, obj, appDedAmt){
	if(isNaN(str)){
		top.fAlert("Please enter only numeric values")
		var appDedAmt = (appDedAmt.toFixed(2));
		$('#'+obj).val(appDedAmt);
		$('#'+obj).focus();
		return false;
	}
}
function addCommentsFn(){
	if($('#commentsArea').val()!=""){
		var flag = false;
		if($('#encCommentsInt').is(':checked')){
			var flag = true;
		}
		if($('#encCommentsExt').is(':checked')){
			var flag = true;
		}
		if(flag==true){
			document.makePaymentFrm.submit();
		}else{
			top.fAlert("Please select any comment type.")
		}
	}else{
		top.fAlert("Please enter comments.")
	}
}
function editComment(commId, type,task_for,task_onreminder){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
    var dtFormat= top.global_date_format || opener.top.global_date_format || 'm-d-Y';
	var comments = $("#commentTd"+commId).html();
	comments=comments.replace(/<br>?\/?>/g, "");
	var reminder_date = $("#reminder_date"+commId).html();
	var selectAssignFor = $("#selectAssignFor").html();
    var task_for_arr=[];if(typeof(task_for)!='undefined' && task_for!='') task_for_arr=task_for.split(',');
	$("#commentTd"+commId).html('<table><tr class="align_td_top"><td><textarea rows="2" cols="92" onfocus="get_operator_name_and_date('+commId+')" name="commentsEditArea'+commId+'" id="commentsEditArea'+commId+'" class="form-control">'+comments+'</textarea></td><td width="15px" style="border:none;">&nbsp;</td><td onClick="return editCommentDetails('+commId+');" title="Save" style="cursor:pointer;border:none;"><img src="../../library/images/save.gif" alt="Save" style="cursor:pointer;border:none;"></td></tr></table>');
	$("#editType"+commId).html('<select name="typeComment'+commId+'" id="typeComment'+commId+'" class="selectpicker" data-width="100%"><option value=""></option><option value="Internal">Internal</option><option value="External">External</option></select>');
	$("#reminder_date"+commId).html('<div class="input-group"><input type="text" name="comment_reminder_date'+commId+'" id="comment_reminder_date'+commId+'" value="'+reminder_date+'" class="form-control date-pick"><label class="input-group-addon pointer" for="comment_reminder_date'+commId+'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label></div>');
	$("#task_for"+commId).html('<select name="type_task_for'+commId+'[]" id="type_task_for'+commId+'" class="selectpicker" data-width="200" data-actions-box="true" data-live-search="true" data-size="5" multiple data-selected-text-format="count > 1" title="Assign as a Notes/Task for">'+selectAssignFor+'</select>');
	$("#typeComment"+commId).val(type);
	$("#typeComment"+commId).selectpicker('refresh');
	$("#type_task_for"+commId+" option:selected").prop("selected",false);
	$("select#type_task_for"+commId).find("option").each(function(index,value){
		if($.inArray($(this).val(),task_for_arr)!=-1){
			$(this).prop("selected",true);
		}
	});
	$("#type_task_for"+commId).selectpicker('refresh');
	$("#editTd"+commId).html('<img src="../../library/images/edit.png" alt="Edit" border="0">');
	$('.date-pick').datetimepicker({
		timepicker:false,
		format:dtFormat,
		formatDate:'Y-m-d',
		scrollInput:false
	});
        $('#commentsEditArea'+commId+'').typeahead({source:phraseArr});
	var task_on_reminder = 'no';
	var task_check = ' ';
	if(task_onreminder==1) {
		task_on_reminder = 'yes';
		task_check =' checked="checked" ';
	}
	$("#taskOnReminderTd"+commId).html('<div class="checkbox"><input type="checkbox" '+task_check+' id="task_on_reminder'+commId+'" value="'+task_on_reminder+'"  name="task_on_reminder'+commId+'" onClick="task_reminder_date('+commId+');"/><label for="task_on_reminder'+commId+'">&nbsp;</label></div>');
	return false;
}
function delComment(comId, eId,cnfrm){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	if (typeof(cnfrm)=="undefined") {
		top.fancyConfirm('Are you sure you want to void this comment?','','top.fmain.delComment('+comId+','+eId+',true)');
		return;
	}
	else{
		window.location.href=top.WRP+"/interface/accounting/chargeDetails.php?encounter_id="+eId+"&del="+comId;
	}
}
function editCommentDetails(commId){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	var newComments = $("#commentsEditArea"+commId).val();
	var typeComment = $("#typeComment"+commId).val();
	var reminder_date = $("#comment_reminder_date"+commId).val();
	var type_task_for = $("#type_task_for"+commId).val();
	var task_on_reminder = $("#task_on_reminder"+commId).val();
	if(typeComment==''){
		top.fAlert("Please select Comment type.")
		return false;
	}
	$.ajax({
		url: zPath+"/accounting/editComments.php?commId="+commId+"&newComments="+escape(newComments)+"&typeComment="+typeComment+"&reminder_date="+reminder_date+"&type_task_for="+type_task_for+"&task_on_reminder="+task_on_reminder,
		success: function(resp){
			if(resp){
				top.fAlert("Notes updated successfully.");
				resp = jQuery.parseJSON(resp);
                var comments = resp.newComments;
                var commDate = resp.todate;
                var Oper = resp.OperatorName;
                var type_task_for_name = resp.task_assign_for_name;
                var type_task_for = resp.type_task_for;
				var task_onreminder = resp.task_on_reminder;

				$("#commentTd"+commId).html(comments);
				$("#editTd"+commId).html("<a class='text_10b' href='javascript:void(0);' onClick='editComment(\""+commId+"\",\""+typeComment+"\",\""+type_task_for+"\");'><img src='../../library/images/edit.png' alt='Edit'style='border:none;'></a> ");
				$("#editType"+commId).html(typeComment);
				$("#commentDateTd"+commId).html(commDate);
				$("#operName"+commId).html(Oper);
				$("#reminder_date"+commId).html(reminder_date);
				$("#task_for"+commId).html(type_task_for_name);
				var task_on_reminder = 'no';
				var task_check = ' ';
				if(task_onreminder=="1") {
					task_on_reminder = 'yes';
					task_check =' checked="checked" ';
                }			
				$("#taskOnReminderTd"+commId).html('<div class="checkbox"><input type="checkbox" '+task_check+' id="task_on_reminder'+commId+'" value="'+task_on_reminder+'"  name="task_on_reminder'+commId+'" onClick="task_reminder_date('+commId+');" /><label for="task_on_reminder'+commId+'">&nbsp;</label></div>');
            }
		}
	});
}



function delPaymentId(payId, eId, payAmt, payDetailId, overPayAmt){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	if(!overPayAmt)	overPayAmt = 0;
	var ask = confirm("Void selected transaction !")
	if(ask==true){
		window.location.href='../accounting/chargeDetails.php?encounter_id='+eId+'&payIdDel='+payId+'&payAmt='+payAmt+'&payDetailId='+payDetailId+'&overPayAmt='+overPayAmt;
	}
}
function delNegPaymentId(payId, eId, payAmt, payDetailId, overPayAmt){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	if(!overPayAmt)	overPayAmt = 0;
	var ask = confirm("Void selected transaction !")
	if(ask==true){
		window.location.href='../accounting/chargeDetails.php?encounter_id='+eId+'&payIdDelNeg='+payId+'&payAmt='+payAmt+'&payDetailId='+payDetailId+'&overPayAmt='+overPayAmt;
	}
}
function EditDenial(id, eId){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	window.open("../accounting/editDenial.php?DenialId="+id+'&eId='+eId,'paymentDenial','width=925,height=300,top=35,left=25,scrollbars=yes,resizable=yes');
}

function delCoPayWOFn(eId, idWO){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	var ask = confirm("Void selected transaction !");
	if(ask==true){
		window.location.href='../accounting/chargeDetails.php?encounter_id='+eId+'&delCoPayWO='+true+'&idWO='+idWO;
	}
}
function delCoPayPaymentFn(eId, pDId, pId ,copayamt){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	var ask = confirm("Void selected transaction !");
	if(ask==true){
		window.location.href='../accounting/chargeDetails.php?encounter_id='+eId+'&delCoPayPayment='+true+'&pDId='+pDId+'&pId='+pId+'&dcopayamt='+copayamt;
	}
}
function editCoPayPayment(eId,pId,pDId){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	window.open("../accounting/edit_copay_payment.php?eId="+eId+"&pId="+pId+"&pDId="+pDId,'editPayment','width=1025,height=600,top=35,left=0,scrollbars=yes,resizable=yes');	
}

function delcredit(cid, eId, chid){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	var ask = confirm("Void selected credit !")
	if(ask==true){
		top.location.href="../accounting/chargeDetails.php?encounter_id="+eId+"&delcreditId="+cid+"&chid="+chid;
	}
}

function editcredit(id, eId){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	window.open("../accounting/editcredits.php?cId="+id+'&eId='+eId,'editCredit','width=925,height=300,top=35,left=25,scrollbars=yes,resizable=yes');
}
function editPaymentFn(dpayId, payId, eId){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	window.open("../accounting/editPayments.php?payId="+payId+'&eId='+eId,'editPayment','width=1025,height=600,top=35,left=0,scrollbars=yes,resizable=yes');	
}
function editNegPaymentFn(dpayId, payId, eId){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	window.open("../accounting/editNegPayments.php?payId="+payId+'&eId='+eId,'editPayment','width=1025,height=600,top=35,left=0,scrollbars=yes,resizable=yes');	
}
function editDeductible(id, eId){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	window.open("../accounting/editDeductible.php?DeductibleId="+id+'&eId='+eId,'deductPayment','width=925,height=300,top=35,left=25,scrollbars=yes,resizable=yes');
}

function removeCommas(val){
	if(typeof(val) !="undefined" && val!=null && val!=''){
		if(val.indexOf(",") != -1){
			do{
				var val = val.replace(",", "");
			}while(val.indexOf(",") != -1)
		}
		if(val.indexOf("$") != -1){
			do{
				var val = val.replace("$", "");
			}while(val.indexOf("$") != -1)
		}
		return val;
	}
}


//START APPROVED BLUR
function approvedBlur(s, wAmt, theObj){
	if(refractionChk(s)){
	var copay = 0;
	if(theObj){
		var presentAppAmt = removeCommas($("#appActualText"+s).val());
		var totalFee = removeCommas($("#totalFee"+s).val())
		var maxAlloewd = totalFee;	
		var newBalanceTdAmt = removeCommas($("#bal_chk_for_copay"+s).val());
		var maxAlloewd = totalFee - newBalanceTdAmt;
		//otherMaxAllowAmt = totalFee - newBalanceTdAmt;
		otherLessAllowAmt = presentAppAmt - newBalanceTdAmt;
		otherMaxAllowAmt = presentAppAmt - newBalanceTdAmt;
		otherMaxAllowAmt = totalFee - otherMaxAllowAmt;		
		otherMaxAllowAmt = otherMaxAllowAmt.toFixed(2);		
		var newAppValue = theObj.value;
		newAppValue = parseFloat(newAppValue);
		// GREATER VALUE
		if(newAppValue>totalFee){
			top.fAlert("Allowed amount should not be greater than total charges");
			theObj.value = presentAppAmt;
			$("#payNew"+s).val(0.00);
			return false;
			
		}
		if((newAppValue>totalFee) && (newAppValue<otherLessAllowAmt)){
			var writeOffAmt = totalFee - newAppValue;
			var writeOffAmt_final = parseFloat(wAmt) + parseFloat(writeOffAmt);
			writeOffAmt_final = writeOffAmt_final.toFixed(2);
			writeOffAmt = writeOffAmt.toFixed(2);
			$('writeOffTd'+s).html('$'+writeOffAmt_final);
			return true;
		}	
		
	}
	var approvedTextEle, deductibleTextEle, payNewEle, paidAmtTextEle, paidAmtPrevEle, totalFeeEle, creditAmtTdEle, overPaymentNowEle, overPaymentEle, adj_amtEle;
	if($('#minus_copay'+s).length>0){
		var minus_copay = $('#minus_copay'+s).val();
	}
	var copay_paid = 0;
	if($('#copay_paid'+s).length>0){
		copay_paid = $('#copay_paid'+s).val();
	}
		
	var approvedTextEle = removeCommas($('#approvedText'+s).val());
	var deductibleTextEle = removeCommas($('#deductibleText'+s).val());
	var payNewEle = removeCommas($('#payNew'+s).val());
	var paidAmtTextEle = removeCommas($('#paidAmtText'+s).val());
	var paidAmtPrevEle = removeCommas($('#paidAmtPrev'+s).text());
	var totalFeeEle = removeCommas($('#totalFee'+s).val());
	var creditAmtTdEle =0.00;
	var overPaymentNowEle = removeCommas($('#overPaymentNow'+s).val());
	var overPaymentEle = removeCommas($('#overPaidPrev'+s).text());
	if($('#adj_amt'+s).length){
		var adj_amtEle = removeCommas($('#adj_amt'+s).val());
	}
	
	//MANAGE WRITE OFF AMOUNT
		var writeOffAmt = totalFeeEle - approvedTextEle;
		var writeOffAmt_fnl = parseFloat(wAmt) + parseFloat(writeOffAmt);
			
		$('#writeOffTd'+s).html('$'+writeOffAmt_fnl.toFixed(2));
	//MANAGE WRITE OFF AMOUNT
	
	//MANAGE AMOUNT TO PAY
		var payAmount = totalFeeEle - writeOffAmt - paidAmtPrevEle -  copay_paid - overPaymentEle - wAmt;
		//alert(totalFeeEle +'-'+ writeOffAmt +'-'+ paidAmtPrevEle +'-'+  copay_paid +'-'+ overPaymentEle +'-'+ wAmt);
		var minus_amount_pay=String(payAmount);
		var minus_sign=minus_amount_pay.substr(0,1);
		if(minus_sign=='-'){
			payAmount=0.00;
		}
		if(payAmount==0 || payAmount==0.00){
			payAmount=parseFloat(overPaymentEle);
		}
		$('#payNew'+s).val(payAmount.toFixed(2));
		$('#paidAmtText'+s).val(payAmount.toFixed(2));
	//MANAGE AMOUNT TO PAY	
	
	//MANAGE OVER PAYMENT
		$('#overPaymentNow'+s).val(0.00);
		$('#overPayment'+s).val(overPaymentEle);
	//MANAGE OVER PAYMENT
	selectChanges(s)
	getTotalPayAmt()
	}
}
//END APPROVED BLUR

//START PAYMENT CHANGE
function paymentChange(s, wAmt){
	var cptId = $('#cptIdTd'+s).html();
	var refractionChk = $('#refractionChk').val();
	if(cptId == 92015 && refractionChk == 'No'){
		top.fAlert("Refraction can not be collected.")
		return false
	}else{
		/*
		var copay = 0;
		var approvedTextEle = removeCommas($('#approvedText'+s).val());
		var deductibleTextEle = removeCommas($('#deductibleText'+s).val());
		var payNewEle = removeCommas($('#payNew'+s).val());
		var paidAmtTextEle = removeCommas($('#paidAmtText'+s).val());
		var paidAmtPrevEle = removeCommas($('#paidAmtPrev'+s).text());
		var totalFeeEle = removeCommas($('#totalFee'+s).html());
		var adj_amtEle = 0;
		if($('#adj_amt'+s).length){
			adj_amtEle = removeCommas($('#adj_amt'+s).val());
		}
		
		var creditAmtTdEle = 0.00;
		var overPaymentEle = removeCommas($('#overPaidPrev'+s).text());
		//WRITE OFF AMOUNT
			var writeOffAmt = totalFeeEle - approvedTextEle;
			var writeOffAmt = writeOffAmt.toFixed(2);
			var minus_copay_chk=$('#minus_copay'+s).val();
		//WRITE OFF AMOUNT
		//MANAGE AMOUNT TO PAY AND OVER PAY
			var payAmount = totalFeeEle - writeOffAmt - paidAmtPrevEle - creditAmtTdEle - wAmt - minus_copay_chk - adj_amtEle;
			if(payNewEle>payAmount){			
				if(payAmount<0){
					var payAmount=-payAmount;
					if(parseFloat(payAmount)>=parseFloat(overPaymentEle)){
						var overPaying =parseFloat(payNewEle)+parseFloat(payAmount);
					}else{
						var overPaying = parseFloat(payNewEle)+parseFloat(payAmount);
					}
					var totalOverPaid = parseFloat(overPaymentEle)+parseFloat(overPaying);
					var payAmount=0;
				}else{
					var overPaying = payNewEle-payAmount;
					var totalOverPaid = parseFloat(overPaymentEle)+parseFloat(overPaying);
				}
				
				$('#paidAmtText'+s).val(payAmount.toFixed(2));
				$('#overPaymentNow'+s).val(overPaying.toFixed(2));
				$('#overPayment'+s).val(totalOverPaid.toFixed(2));
			}else{
				$('#paidAmtText'+s).val(payNewEle);
				$('#overPaymentNow'+s).val(0.00);
				$('#overPayment'+s).val(overPaymentEle);
			}
		getTotalPayAmt();*/
	}
	
}
// START CHECK PAYMENT MADE OR NOT
var tot= "";
function checkPaymentBox(s, wAmt,tot){
	var cptId = $('#cptIdTd'+s).html();
	var refractionChk = $('#refractionChk').val();
	var newBalanceTdAmt = removeCommas($("#bal_chk_for_copay"+s).val());
	
	if(cptId == 92015 && refractionChk == 'No'){
		top.fAlert("Refraction can not be collected.")
		return false
	}
	var globalCPTId_false='';
	if($('#chkbx'+s).is(':checked')==true){
		if(parseFloat($("#totalFee"+s).val())>=parseFloat($("#approvedText"+s).val())){	
			if(parseFloat($("#appActualText"+s).val())>parseFloat($("#approvedText"+s).val())){
				newBalanceTdAmt=newBalanceTdAmt-($("#appActualText"+s).val()-$("#approvedText"+s).val());
			}else{
				newBalanceTdAmt=parseFloat(newBalanceTdAmt)+parseFloat($("#approvedText"+s).val()-$("#appActualText"+s).val());
			}
			newBalanceTdAmt = newBalanceTdAmt.toFixed(2);
		}
		if(newBalanceTdAmt<=0){
			newBalanceTdAmt='';
		}
		if($("#pri_due_"+s).val()>0){
			$("#pri_paid_"+s).val(newBalanceTdAmt);
		}else if($("#sec_due_"+s).val()>0){
			$("#sec_paid_"+s).val(newBalanceTdAmt);
		}else if($("#ter_due_"+s).val()>0){
			$("#ter_paid_"+s).val(newBalanceTdAmt);
		}else if($("#pat_due_"+s).val()>0){
			$("#pat_paid_"+s).val(newBalanceTdAmt);
		}
		copy_method(s);
	}else{
		if($("#pri_paid_"+s)){$("#pri_paid_"+s).val('');}
		if($("#sec_paid_"+s)){$("#sec_paid_"+s).val('');}
		if($("#ter_paid_"+s)){$("#ter_paid_"+s).val('');}
		if($("#pat_paid_"+s)){$("#pat_paid_"+s).val('');}
	}
}
// END CHECK PAYMENT MADE OR NOT
function checkForAnyNumericCharacter(strValue){
	var reg = new RegExp("[0-9]+");
	if(reg.test(strValue)){
		return false;
	}
	return true;
}
function getTotalPayAmt(){
	var copay = 0;
	var TotalPayAmount = 0;	
	var frmEle = document.makePaymentFrm.elements.length;
	var frmObj = document.makePaymentFrm;
	for(i=0;i<frmEle;i++){
		var eleName = frmObj.elements[i].name;
		if(eleName.indexOf('payNew') != -1){
			var amtToPay = removeCommas(frmObj.elements[i].value);
			if((amtToPay=='-0.00') || (amtToPay<0)){
				frmObj.elements[i].value = amtToPay;
				$("#paymentClaims").val("Negative Payment");
				$("#paymentClaims").selectpicker('refresh');
			}
			var TotalPayAmount = TotalPayAmount + parseFloat(amtToPay);
		}
	}
	
	// IF COPAY PAYMENT IS CHECKED
	if($('#coPayChk').length>0){
		var CopayChk = $('#coPayChk').checked;
		if($('#coPayChk').is(':checked')){
			TotalPayAmount = TotalPayAmount;
		}
	}
	
	var tot_print_arr = $("input[name^='chkbx']").length;
	var ovr_ref="";
	var ovr_ref_minus="0";
	var deb_ovr_cnt=0;
	if(tot_print_arr>0){
		for(f=1;f<=tot_print_arr;f++){
			if($('#chkbx'+f).is(':checked')===true  && $('#overPayments_chk'+f).val()>0){
				var ovr_ref=removeCommas($('#payNew'+f).val());
				var ovr_ref_minus = parseFloat(ovr_ref) + parseFloat(ovr_ref_minus);
			}else{
				if($('#chkbx'+f).is(':checked')===true){
					deb_ovr_cnt++;
				}
			}
		}
	
	}
	//refund minus the over paid amount into total amount
	
	if(ovr_ref_minus>0 && deb_ovr_cnt>0){
		TotalPayAmount = parseFloat(TotalPayAmount) - parseFloat(ovr_ref_minus);
	}
	//refund minus the over paid amount into total amount
	
	/* SETTING AMOUNT FOR OUTER */
	var eleLen = document.forms[0].elements.length;
	for(var k=0; k<eleLen; k++){
		var eleName = document.forms[0].elements[k].name;
		if(eleName=='paidAmountNow'){
			if(checkForAnyNumericCharacter(TotalPayAmount)==true){
				TotalPayAmount=0;
			}
			document.forms[0].elements[k].value = TotalPayAmount.toFixed(2);
		}
	}
	/* SETTING AMOUNT FOR OUTER */
}
function selectChanges(s){
	if(refractionChk(s)){
		$('#chkbx'+s).prop("checked",true);
		getTotalPayAmt()
	}
}

function editWriteOff(wOffId, eId, cLDId, none_edit){
	if(none_edit>0){
		top.fAlert("This Write-Off cannot be edited from here, please change allowed amount to modify this write-off.");
		return false;
	}else{
		if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
			view_only_acc_call(0);
			return false;
		}
		window.open("../accounting/editWriteOff.php?wOffId="+wOffId+'&eId='+eId+'&cLDId='+cLDId,'editWriteOff','width=925,height=350,top=75,left=35,scrollbars=yes,resizable=yes');	
	}
}

function del_tx_pay(id, eId, cDetailsId){
	if($("#acc_view_pay_only").val()==1 || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	var ask = confirm("Void selected transaction !");
	if(ask==true){
		window.location.href='../accounting/chargeDetails.php?encounter_id='+eId+'&delTxId='+id+'&cDetailsId='+cDetailsId;
	}
}
function refractionChk(s){
	var cptId = $('#cptIdTd'+s).html();
	var refractionChk = $('#refractionChk').val();;
	if(cptId == 92015 && refractionChk == 'No'){
		top.fAlert("Refraction can not be collected.");
		$("#chkbx"+s).prop('checked',false);
		return false;
	}else{
		return true;
	}
}
function copay_minus_procedure(tot2){
	var arr_apply_copay_chk=new Array(99212,99213,99214,99215,99201,99202,99203,99204,99205,99241,99242,99243,99244,99245,92012,92013,92014,92002,92003,92004);
	var s=1;
	var tot2=tot2;
	if(tot2){
		for(s=1;s<=tot2;s++){
			if(document.getElementById('coPayChk') && document.getElementById('proc_copay')){
				if(document.getElementById('chkbx'+s).checked==true && document.getElementById('coPayChk').checked==true && document.getElementById('chkbx'+s).value==document.getElementById('proc_copay').value){
					for(c=0;c<arr_apply_copay_chk.length;c++){
						if(document.getElementById('cptIdTd'+s).innerHTML==arr_apply_copay_chk[c]){
							if(parseFloat(document.getElementById('bal_chk_for_copay'+s).value)>=parseFloat(top.document.getElementById('copayAmount').value)){
								var new_minus_copay=parseFloat(document.getElementById('bal_chk_for_copay'+s).value)-parseFloat(top.document.getElementById('copayAmount').value);
								if(document.getElementById('payNew'+s).value!=new_minus_copay){
									document.getElementById('payNew'+s).value=new_minus_copay.toFixed(2);
									document.getElementById('paidAmtText'+s).value=new_minus_copay.toFixed(2);
									var eleLen_chk = top.document.forms[0].elements.length;
									for(var k=0; k<eleLen_chk; k++){
										var eleName_chk = top.document.forms[0].elements[k].name;
										if(eleName_chk=='paidAmountNow'){
											var pad_amts_now = parseFloat(top.document.forms[0].elements[k].value)-parseFloat(top.document.getElementById('copayAmount').value);
											if(pad_amts_now<0){
												pad_amts_now=0;
											}
											top.document.forms[0].elements[k].value=pad_amts_now.toFixed(2);
										}
										if(eleName_chk=='paidAmount'){
											document.getElementById('paidAmount').value=top.document.forms[0].elements[k].value;
											top.document.forms[0].elements[k].value = top.document.forms[0].elements[k].value;
										}
									}	
								}	
							}
						}else{
							if(parseFloat(document.getElementById('bal_chk_for_copay'+s).value)>=parseFloat(top.document.getElementById('copayAmount').value)){
								var new_minus_copay=parseFloat(document.getElementById('bal_chk_for_copay'+s).value)-parseFloat(top.document.getElementById('copayAmount').value);
								if(document.getElementById('payNew'+s).value!=new_minus_copay){
									document.getElementById('payNew'+s).value=new_minus_copay.toFixed(2);
									document.getElementById('paidAmtText'+s).value=new_minus_copay.toFixed(2);
									var eleLen_chk = top.document.forms[0].elements.length;
									for(var k=0; k<eleLen_chk; k++){
										var eleName_chk = top.document.forms[0].elements[k].name;
										if(eleName_chk=='paidAmountNow'){
											var pad_amts_now = parseFloat(top.document.forms[0].elements[k].value)-parseFloat(top.document.getElementById('copayAmount').value);
											if(pad_amts_now<0){
												pad_amts_now=0;
											}
											top.document.forms[0].elements[k].value=pad_amts_now.toFixed(2);
										}
										if(eleName_chk=='paidAmount'){
											document.getElementById('paidAmount').value=top.document.forms[0].elements[k].value;
											top.document.forms[0].elements[k].value = top.document.forms[0].elements[k].value;
										}
									}	
								}	
							}
						}	
					}
				}
			}	
		}	
	}	
}

function set_write_off_id(id,top){
	var chk_id=id;
	var approvedText="approvedText"+id;
	var totalFee="totalFee"+id;
	if($('#show_write_code').val()>0){
		var set_id="write_off_code"+id;
		var wrt_id=$('#show_write_code').val();
		$('#'+set_id).val(wrt_id);
	}else{
		if(parseFloat(removeCommas($('#'+totalFee).val()))>parseFloat($('#'+approvedText).val())){
			$('#wrt_but_id').attr('onclick','set_write_off('+id+')');
			$('#write_off_div').modal('show');
		}
	}
}
function set_write_off(id){
	if($("#show_write_code").val()==""){
		top.fAlert("Please select the Write off code");
		return false;
	}else{	
		var set_id="write_off_code"+id;
		var wrt_id=$('#show_write_code').val();
		if($('#'+set_id).length>0){
			$('#'+set_id).val(wrt_id);
		}
		$('#write_off_div').modal('hide');
	}
	
}

function getDiscount(ids){
	var id_arr=ids.split(',');
	var adm_dis=0;
	var dis_type="";
	var show_dis_amt="";
	adm_dis = $('#adm_dis').val();
	var show_dis_amt=$('#show_dis_amt').val();
	if(adm_dis>0){
		if(id_arr.length>0){
			var ask = "Cash discount of "+show_dis_amt+" is available for this encounter, do you want to apply discount now?";
			top.fancyConfirm(ask,'',"window.top.fmain.setDiscount('"+ids+"','yes');","window.top.fmain.setDiscount('"+ids+"','no');");
		}
	}
}
function setDiscount(ids,stat){
	if(stat=="no"){
		var pat_id =$('#patient_id').val();
		var enc_id =$('#enc_id_read').val();
		var url="discount_msg_hide.php?pat_id="+pat_id+"&enc_id="+enc_id;
		$.ajax({
				type: "POST",
				url: url,
				success: function(resp){
					return false;
				}
		});
	}else{
		var id_arr=ids.split(',');
		var adm_dis=0;
		var dis_type="";
		var show_dis_amt="";
		var sub_dis="";
		adm_dis = $('#adm_dis').val();
		var show_dis_amt=$('#show_dis_amt').val();
		var discount_code_chk=$('#discount_code_chk').val();
		if(adm_dis>0){
			if(id_arr.length>0){
				for(i=0;i<id_arr.length;i++){
					var id=id_arr[i];
					var pay_amt=$("#pat_paid_"+id);
					var newBalanceTdAmt = parseFloat(removeCommas($("#bal_chk_for_copay"+id).val()));
					var totalFeeEle = parseFloat(removeCommas($('#totalFee'+id).val()));
					dis_type = $('#dis_type').val();
					if(newBalanceTdAmt>=totalFeeEle){
						if(dis_type=="%"){
							var final_pay_amt= (totalFeeEle*adm_dis)/100;
						}else{
							var final_pay_amt= adm_dis;
						}
						if(final_pay_amt<0){
							final_pay_amt=0;
						}
						pay_amt.val(final_pay_amt);
						$("#payment_method_"+id).val("Discount");
						checkChkBox(id,'payment_method_');
						if(discount_code_chk>0){
							$("#write_off_code_"+id).selectpicker('val', discount_code_chk+'_dis');
						}
						sub_dis="yes";
					}
				}
				if(sub_dis!=""){
					$('#cash_dis').val('yes');
					applyFn('applySubmit', 'applyRecieptSubmit');
				}
			}
		}
	}
}

function sel_chk(id){
	var chk_box="chl_chk_box_"+id;
	if($('#'+chk_box)){
		$('#'+chk_box).prop("checked",true);
	}
}

function day_search(){
	var msg="<b>Please select the following fields.</b><br>";
	var flag=0;
	if($("#provider_srh").val()=="" || $("#provider_srh").val() == null){
		msg+="- Provider<br>"; ++flag;
	}
	if($("#acc_fac").val()=="" || $("#acc_fac").val() == null){
		msg+="- Facility<br>"; ++flag;
	}
	if(flag>0){
		fAlert(msg);
		return false;
	}else{
		
		var dFrom = $("#dos_frm").val();
		var dTo = $("#dos_to").val();
		
		var dFrom_exp=dFrom.split('-');
		var dFrom_final=dFrom_exp[2]+dFrom_exp[0]+dFrom_exp[1];
		
		var dTo_exp=dTo.split('-');
		var dTo_final=dTo_exp[2]+dTo_exp[0]+dTo_exp[1];
		var diff_days = dTo_final-dFrom_final;
		if(diff_days>0){
			var ask = "Warning – Running Day Charges for more than a day will be slow and it will slow down your system.";
			fancyConfirm(ask,'',"document.srh_frm.submit()",'','','','Ok','Cancel');	
		}else{
			document.srh_frm.submit();
		}
	}
}

function edit_enc_dx(id){
	window.open("../accounting/edit_enc_dx.php?edit_id="+id,'EditDX','width=1080,height=430,top=35,left=25,scrollbars=yes,resizable=yes');
}

function acc_redirect(pt_id){
    //To check restrict access of patient before load
    $.when(window.opener.top.check_for_break_glass_restriction(pt_id)).done(function(response){
        window.opener.top.removeMessi();
        if(response.rp_alert=='y') {
            var patId=response.patId;
            var bgPriv=response.bgPriv;
            var rp_alert=response.rp_alert;
            window.opener.top.core_restricted_prov_alert(patId, bgPriv, '');
        }else{
            window.opener.top.core_set_pt_session(top.fmain, pt_id, '../accounting/superbill_charges.php');
        }
    });
}

function show_ci_co_info(patient_id,amt,dos,sch_id){
	if(amt>0){
		var url="cico_day_charges_list.php?patient_id="+patient_id+"&dos="+dos+"&sch_id="+sch_id;
		$.ajax({
			type:'GET',
			url:url,
			success:function(response){
				var result=response;
				show_modal('ci_co_div','Check In / Check Out Info',result);
			}
		});
	}
	return false;
}

function post_charges_fun(val){
	if($("#acc_view_chr_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	var total_ids = document.getElementById('total_ids').value;
	if(total_ids>0){
		for(j=0;j<=total_ids;j++){
			var cpt_code= "cpt_code_"+j;
			if(document.getElementById(cpt_code)){
				if(document.getElementById(cpt_code).value==""){
					alert("Please select or enter a Procedure.");
					document.getElementById(cpt_code).focus();
					return false;
				}
			}
		}
	}
	var alrt_enc="";
	if(val==""){
		$(".chl_chk_box_css").each(function() {
			var chl_chk_box_id=$(this).attr('id');
			if(document.getElementById(chl_chk_box_id).checked==true){
				var chl_dx_chk_box_css=chl_chk_box_id.replace("chl_chk_box_","diagText_all_css_mul_");
				var enc_id_txt=$(".enc_id_css_"+chl_chk_box_id.replace("chl_chk_box_","")).val();
				var alert_flag="";
				var chl_dx_chk_box_id=$(this).attr('id');
				var chk_val = $(this).val();
				$('input[name^=diagText_all]').each(function(id,elem){
					var elem = $(elem);
					var name = elem.attr('name');
					if(name.search(chk_val) != -1){
						if(alert_flag=="" && (($(elem).val()).indexOf("-"))!=-1){
							alrt_enc+=enc_id_txt+', '; 
							alert_flag=1;
							document.getElementById(chl_chk_box_id).checked=false;
						}
					}
				});
			}
		});
	}
	if(alrt_enc!=""){
		alrt_enc = alrt_enc.substr(0,((alrt_enc.length)-2));
		fAlert("Cannot POST encounters ("+alrt_enc+") due to incomplete DX Codes","","post_charges_fun(\'post\')","","","Close");
		return false;
	}
	document.getElementById('post_charges').disabled='true';
	//document.getElementById('day_loading').style.display='block';
	document.getElementById('print_frm').value="charges";
	document.post_charge.submit();
}

function post_payment_fun(){
	if(document.getElementById("acc_view_pay_only").value==1){
		view_only_acc_call(0);
		return false;
	}
	var total_ids = document.getElementById('total_ids').value;
	if(total_ids>0){
		for(j=0;j<=total_ids;j++){
			var cpt_code= "cpt_code_"+j;
			if(document.getElementById(cpt_code)){
				if(document.getElementById(cpt_code).value==""){
					alert("Please select or enter a Procedure.");
					document.getElementById(cpt_code).focus();
					return false;
				}
			}
		}
	}
	document.getElementById('post_payments').disabled='true';
	//document.getElementById('day_loading').style.display='block';
	document.getElementById('print_frm').value="payment";
	document.post_charge.submit();
}

function print_save(){
	if($("#acc_view_chr_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	var total_ids = document.getElementById('total_ids').value;
	if(total_ids>0){
		for(j=0;j<=total_ids;j++){
			var cpt_code= "cpt_code_"+j;
			if(document.getElementById(cpt_code)){
				if(document.getElementById(cpt_code).value==""){
					alert("Please select or enter a Procedure.");
					document.getElementById(cpt_code).focus();
					return false;
				}
			}
		}
	}
	document.getElementById('print_frm').value="print";
	document.post_charge.submit();
}

function ajaxFunction2(id,old_proc){
	//var pInsId = document.getElementById('caseTypeText_'+id).value;
	if(document.getElementById('pri_ins_id_'+id)){
		var pInsId = document.getElementById('pri_ins_id_'+id).value;
	}else{
		for(k=id;k>0;k--){
			if(document.getElementById('pri_ins_id_'+k)){
				var pInsId = document.getElementById('pri_ins_id_'+k).value;
				break;
			}
		}
	}
	var proc_id="cpt_code_"+id;
	var unit_chk="units_"+id;
	var char_chk="proc_charges_"+id;
	var net_amt_chk="total_amt_"+id;
	var proc_txt="cpt_code_"+id;
	
	var v = document.getElementById(proc_id).value;
	if(v==""){
		alert("Please select or enter a Procedure.")
		//document.getElementById(proc_id).focus();
		return false;
	}
	
	var xyz=document.getElementById(proc_txt).value;
	$.ajax({
		url:"get_price.php?str="+xyz+"&pInsId="+pInsId,
		type:'GET',
		success:function(response){
			var val = response;
			//var i = val.indexOf(",");
			var val_arr = val.split(", ");
			var code = val_arr[0];
			var fee = val_arr[1];
			var dx_codes = val_arr[2];
			//if(old_proc=='no'){
				document.getElementById(unit_chk).value=1;
				var u = document.getElementById(unit_chk).value;
				var tPrice=fee*u;
				if((fee=="") || (fee==0)){
					//alert("No charge assigned.")
				}
				var fee = trim(fee);
				var tPrice = tPrice.toFixed(2);
				document.getElementById(char_chk).value=fee;		
				document.getElementById(net_amt_chk).value=tPrice;
			//}	
			var newVal = code.replace(/~~~/g,",");
			document.getElementById(proc_id).value=newVal;
			document.getElementById(proc_id).tooltip('hide').attr('data-original-title',newVal).tooltip('fixTitle').tooltip('show');
		}
	});
}

function sel_unpost(){
	var chk_box=false;
	if(document.getElementById('all_chk').checked==true){
		chk_box=true;
	}
	if(document.getElementById('total_ids_imp')){
		var val=document.getElementById('total_ids_imp').value;
		var un_arr=val.split(',');
		for(i=0;i<un_arr.length;i++){
			id='chl_chk_box_'+un_arr[i];
			if(document.getElementById(id)){
				document.getElementById(id).checked=chk_box;
			}
		}
	}
}
function sel_unpost_hcfa(){
	document.getElementById('all_chk').checked='true';
	sel_unpost();
	if(document.getElementById('total_ids_imp')){
		var val=document.getElementById('total_ids_imp').value;
		var un_arr=val.split(',');
		for(i=0;i<un_arr.length;i++){
			id='hcfa_chk_box_'+un_arr[i];
			if(document.getElementById(id)){
				document.getElementById(id).checked='true';
			}
		}
	}
}

function totalAmtDay(id){
	
	var proc_txt="cpt_code_"+id;
	var unit_chk="units_"+id;
	var char_chk="proc_charges_"+id;
	var net_amt_chk="total_amt_"+id;
	
	var proc = document.getElementById(proc_txt).value;
	if(proc==""){
		alert("Please select Procedure code")
		return false;
	}
	var u = document.getElementById(unit_chk).value;
	var p = document.getElementById(char_chk).value;
	do{
		if(p.indexOf(",")!=-1){
			var p = p.replace(",", "")
		}
	}while(p.indexOf(",")!=-1)
	
	if(isNaN(u)){
		alert("Please enter a valid numeric Charge amount.")
		document.getElementById(unit_chk).value="";
		return false;
	}
	if(isNaN(p)){
		alert("Please enter a valid numeric Charge amount.")
		document.getElementById(net_amt_chk).value="";
		return false;
	}
	var c = u * p;
	var c = (c.toFixed(2));
	document.getElementById(net_amt_chk).value=c;
}

function loadCaseInfoDay(num,eId,pid,sup_proc_amt){
	if(document.getElementById('caseTypeText_'+num)){
		var caseType = document.getElementById('caseTypeText_'+num).value;	
		var case_arr= caseType.split("-");
		var case_len=case_arr.length;
		var case_final=case_arr[case_len-1];
		var id = trim(case_final);
		//alert(id);
		$.ajax({
			type:'GET',
			url:"getInsComp.php?ins="+id+"&eid="+eId+"&pid="+pid,
			success:function(response){
				var result = response;
				//alert(result);
				result=trim(result);
				var ARR_result = JSON.parse(result);
				if(document.getElementById("pri_ins_"+num)){
					if(ARR_result['primaryInsCoName']!="" && ARR_result['primaryInsCoName']!=null){
						document.getElementById("pri_ins_"+num).value = ARR_result['primaryInsCoName'];
						document.getElementById("pri_ins_"+num).title=ARR_result['primaryInsCoName'];
					}else{
						document.getElementById("pri_ins_"+num).value = "";
						document.getElementById("pri_ins_"+num).title="";
					}
				}
				if(document.getElementById("pri_ins_id_"+num)){
					if(ARR_result['primaryInsId']!=null){
						document.getElementById("pri_ins_id_"+num).value = ARR_result['primaryInsId'];
					}else{
						document.getElementById("pri_ins_id_"+num).value = "";
					}	
				}
				
				if(document.getElementById("sec_ins_"+num)){
					if(ARR_result['secondaryInsCoName']!="" && ARR_result['secondaryInsCoName']!=null){
						document.getElementById("sec_ins_"+num).value = ARR_result['secondaryInsCoName'];
						document.getElementById("sec_ins_"+num).title=ARR_result['secondaryInsCoName'];
					}else{
						document.getElementById("sec_ins_"+num).value = "";
						document.getElementById("sec_ins_"+num).title="";
					}
				}
				
				if(document.getElementById("sec_ins_id_"+num)){
					if(ARR_result['secondaryInsId']!=null){
						document.getElementById("sec_ins_id_"+num).value = ARR_result['secondaryInsId'];
					}else{
						document.getElementById("sec_ins_id_"+num).value = "";
					}
				}
				
				flag=0;
				if(sup_proc_amt=="yes"){
					//ajaxFunction1(num,'yes')
				}
			}
		});
	}
	if($("."+self_pay_chk_box_css)){
		if($('#caseTypeText_'+num).val()=="Self"){
			var self_pay_chk_box_css =  "self_pay_chk_box_css_"+eId;
			$("."+self_pay_chk_box_css).prop("checked",true);
		}else{
			var self_pay_chk_box_css =  "self_pay_chk_box_css_"+eId;
			$("."+self_pay_chk_box_css).prop("checked",false);
		}
	}
}
function crt_dx_dropdown_day(id,dx_arr){
	/*var diagText_all_css_mul = ".diagText_all_css_mul_"+id;
	var all_rec = dx_arr;
	$(diagText_all_css_mul).each(function(){
		var all_opt_data = "";
		var sel_val_dx_id=this.id;
		var sel_val_arr=$("#"+sel_val_dx_id).val();
		if(typeof sel_val_arr !="undefined" && sel_val_arr!=null && sel_val_arr!=''){
			for(x in all_rec){
				if(all_rec[x]!=""){
					var sel_opt="";
					var yy=parseInt(x)+1;
					var chk_sel_rec= all_rec[x]+'**'+yy;
					if($.inArray(chk_sel_rec,sel_val_arr)!=-1){
						sel_opt="selected";
					}
					all_opt_data += '<option value="'+chk_sel_rec+'" '+sel_opt+'>'+all_rec[x]+'</option>';
				}
			}
		}
		$("#"+sel_val_dx_id).html(all_opt_data);
		$("#"+sel_val_dx_id).selectpicker('refresh');
	});
	sel_chk(id);*/
}
function del_records(frm_name){
	if($("#acc_view_chr_only").val()==1){
		view_only_acc_call(0);
		return false;
	}
	if($('.chk_box_css:checked').length==0){
		top.fAlert('Please select checkbox to void.');
	}else{
		top.fmain.$("#post_action").val('del');
		var ask="";
		ask += "Do you want to void all selected records?";
		top.fancyConfirm(ask,'', "window.top.fmain.document."+frm_name+".submit()","return false");	
	}
}
function print_hcfa_ub(validChargeListId,charge_list_detail_ids,InsComp,printHcfa,batch_file_submitte_id){
	var url="../billing/hcfa_ub_by_era.php?vchl="+validChargeListId+"&vchld="+charge_list_detail_ids+"&InsComp="+InsComp+"&printHcfa="+printHcfa+"&batch_file_submitte_id="+batch_file_submitte_id;
	window.open(url,'hcfa','width=10px,height=10px,top=250px,left=250px,scrollbars=yes,resizable=1');	
}
function viewDetailsFn(eFileId, SVCId, idPat){
	var url="../billing/era_post_payments_popup.php?era_manual_payments=yes&send_era_id="+eFileId+'&send_era_pat_id='+idPat+'&send_era_chk_id='+SVCId;
	window.open(url,'Claim_file','top=50,left=100,width=1100,height=650,scrollbars=yes,resizable=1');	
}
function show_other_opt(){
	if($('#general').is(':checked')==true){
		$('#enc_dos').attr('disabled',true);
		$('#enc_id').attr('disabled',true);
		$('#notes_type').attr('disabled',true);
	}else{
		$('#enc_dos').attr('disabled',false);
		$('#enc_id').attr('disabled',false);
		$('#notes_type').attr('disabled',false);
	}
	$('#enc_dos,#enc_id,#notes_type').selectpicker('refresh');
}

function note_sub(){
	if($('#general').is(':checked')==true){
		if($("#notes").val()==""){
			top.fAlert('Please enter the Note');
			return false;
		}else{
			if($('#save').length>0){
				$('#save').attr('disabled',true);
			}
			document.note_form.submit();
		}
	}else{
		if($("#enc_dos").val()==""){
			top.fAlert('Please select the DOS');
			return false;
		}else if($("#enc_id").val()==""){
			top.fAlert('Please select the Encounter Id');
			return false;
		}else if($("#notes").val()==""){
			top.fAlert('Please enter the Note');
			return false;
		}else{
			if($('#save').length>0){
				$('#save').attr('disabled',true);
			}
			document.note_form.submit();
		}
	}
}
function get_enc_fun(enc_dos,pat_id){
	$.ajax({
	type: "POST",
	url: "get_encounter.php?enc_dos="+enc_dos+"&pat_id="+pat_id,
	success: function(r){
			$('#enc_id').html(r);
			$('#enc_id').selectpicker('refresh');
		}
	});
}
function get_dos_fun(enc_dos,pat_id){
	$('#enc_dos').val(enc_dos);
	get_enc_fun(enc_dos,pat_id);
}
function edit_day_charges(edit_id,id_num){
	var ref_phy = $("#reff_phy_id"+id_num).val();
	window.open("../accounting/edit_day_charges.php?edit_id="+edit_id+"&id_num="+id_num+"&ref_phy="+ref_phy,'EditDX','width=1080,height=430,top=35,left=25,scrollbars=yes,resizable=yes');
}
function showEP(ptid,eid,wn_height){
    //To check restrict access of patient before load
    $.when(top.check_for_break_glass_restriction(ptid)).done(function(response){
        top.removeMessi();
        if(response.rp_alert=='y') {
            var patId=response.patId;
            var bgPriv=response.bgPriv;
            var rp_alert=response.rp_alert;
            top.core_restricted_prov_alert(patId, bgPriv, '','',eid);
        }else{
            if(typeof(top.arr_opened_popups['set_session'])!="undefined"){
                //top.arr_opened_popups['set_session'].close();
				delete top.arr_opened_popups['set_session'];
            }
            url = '../billing/set_session.php?patient='+ptid+'&eid='+eid+'&md=ep';
            sc_wd=(screen.availWidth-20);
            top.popup_win(url,"left=0,top=0,resizeable=1,scrollbars=1,menubar=0,toolbar=0,status=0,width="+sc_wd+",height="+wn_height);
        }
    });
}

function editNotes(obj){
	var obj = $(obj);
	var dataArr = obj.data();
	
	//var notes = obj.siblings('textarea').val();
	var notes = dataArr.notes;
	var modal = $('#assignModal');
	
	if(modal.length){
		var taskAssign = dataArr.task;
		var taskDone = dataArr.taskdone;
		var taskFor = dataArr.taskfor.toString();
		var taskValid = taskFor.indexOf(',') != -1;
		if(taskValid) taskFor = taskFor.split(',');
		var comId = dataArr.comid;
		
		if(taskAssign == 2) modal.find('.modal-body [type=checkbox]#task').prop('checked', true);
		if(taskDone == 2) modal.find('.modal-body [type=checkbox]#taskdone').prop('checked', true);
		if(comId) modal.find('.modal-body #commentId').val(comId);
		
		if(taskDone == 1 && taskAssign == 1 && taskFor == '') modal.find('.modal-body [type=checkbox]#taskdone').prop('checked', false).prop('disabled', true);
		
		if(taskFor.length) modal.find('.modal-body #taskfor').selectpicker('val', taskFor);
		if(notes) modal.find('.modal-body #taskNote').html(notes);
	}
	
	modal.modal('show');
	
	$('#saveAssignTask').on('click', function(){
		document.assignTaskForm.submit();
	});
	
	modal.on('hide.bs.modal', function(){
		modal.find('.modal-body [type=checkbox]#task').prop('checked', false);
		modal.find('.modal-body [type=checkbox]#taskdone').prop('checked', false).prop('disabled', false);
		$('#taskfor').selectpicker('val', '').selectpicker('refresh');
	});
}

if(typeof(renew_title)!='function'){
	function renew_title(o,s){ $(o).attr('title',""+s).tooltip('fixTitle').tooltip();}
}

function acc_total(){
	var ngt_payment=0;
	var ngt_adjustment=0;
	var ngt_deduct=0;
	var ngt_denied=0;
	$(".payment_method_css").each(function() {
		var amt = 0;
		if($(this).val()!=''){
			var j= $(this).data('seq-id');
			if($("#pri_paid_"+j).val()>0 || $("#pri_paid_"+j).val()<0){
				amt = parseFloat(amt) + parseFloat($("#pri_paid_"+j).val().replace('-',''));
			}
			if($("#sec_paid_"+j).val()>0 || $("#sec_paid_"+j).val()<0){
				amt = parseFloat(amt) + parseFloat($("#sec_paid_"+j).val().replace('-',''));
			}
			if($("#ter_paid_"+j).val()>0 || $("#ter_paid_"+j).val()<0){
				amt = parseFloat(amt) + parseFloat($("#ter_paid_"+j).val().replace('-',''));
			}
			if($("#pat_paid_"+j).val()>0 || $("#pat_paid_"+j).val()<0){
				amt = parseFloat(amt) + parseFloat($("#pat_paid_"+j).val().replace('-',''));
			}
			
			if($(this).val().indexOf("Negative Payment") != -1){
				amt = '-'+amt;
			}

			if($(this).val().indexOf("Paid") != -1 || $(this).val().indexOf("Deposit") != -1 || $(this).val().indexOf("Interest Payment") != -1 || $(this).val().indexOf("Negative Payment") != -1){
				ngt_payment = parseFloat(ngt_payment)+parseFloat(amt);
			}else if($(this).val().indexOf("Discount") != -1 || $(this).val().indexOf("Write Off") != -1 || $(this).val().indexOf("Adjustment") != -1 || $(this).val().indexOf("Refund") != -1){
				ngt_adjustment = parseFloat(ngt_adjustment)+parseFloat(amt);
			}else if($(this).val().indexOf("Deductible") != -1){
				ngt_deduct = parseFloat(ngt_deduct)+parseFloat(amt);
			}else if($(this).val().indexOf("Denied") != -1){
				ngt_denied = parseFloat(ngt_denied)+parseFloat(amt);
			}
		}
	});
	
	$('#ngt_payment_td,#ngt_adjustment_td,#ngt_deduct_td,#ngt_denied_td').hide();
	
	if(ngt_payment>0 || ngt_payment<0){
		$("#ngt_payment").html(('$'+ngt_payment.toFixed(2)).replace('$-','-$'));
		$('#ngt_payment_td').show();
	}
	if(ngt_adjustment>0){
		$("#ngt_adjustment").html('$'+ngt_adjustment.toFixed(2));
		$('#ngt_adjustment_td').show();
	}
	if(ngt_deduct>0){
		$("#ngt_deduct").html('$'+ngt_deduct.toFixed(2));
		$('#ngt_deduct_td').show();
	}
	if(ngt_denied>0){
		$("#ngt_denied").html('$'+ngt_denied.toFixed(2));
		$('#ngt_denied_td').show();
	}
}
function addTax(pre_cnt){
	var billing_facility_id = $('#billing_facility_id').val();
	var last_cnt = $('#last_cnt').val();
	if($('#procedureText_1').val()!="" && last_cnt>1){
		var tax_proc_code = 0;
		var fac_tax_per=0;
		var netAmt=0;
		for(var j=1;j<=last_cnt;j++){
			if($('#chkbx_'+j).length>0){
				if($('#procedureText_'+j).val().toLowerCase()=="tax"){
					tax_proc_code=j;
				}
			}
		}
		if(Object.keys(fac_tax_arr).length){
			fac_tax_per = fac_tax_arr[billing_facility_id];
			if(fac_tax_per>0 || tax_proc_code>0){
				for(var j=1;j<=last_cnt;j++){
					if($('#chkbx_'+j).length>0){
						if($('#procedureText_'+j).val().toLowerCase()!="tax" && $('#cpt_tax_'+j).val()>0 && $('#procedureText_'+j).val()!=""){
							netAmt += ($('#netAmt_'+j).val()*fac_tax_per)/100;
						}
					}
				}
				netAmt = netAmt.toFixed(2);
				if(tax_proc_code==0 && netAmt>0){
					if($('#procedureText_'+pre_cnt).val().toLowerCase()!="tax"){
						$('#procedureText_'+last_cnt).val('Tax');
						$('#units_'+last_cnt).val('1');
						$('#charges_'+last_cnt).val(netAmt);
						$('#netAmt_'+last_cnt).val(netAmt);
						$('#display_order_'+last_cnt).val(parseInt(last_cnt)+1);
						//$("#proc_selfpay_"+last_cnt).prop("checked",true);
						show_div(last_cnt);
					}else{
						$('#charges_'+pre_cnt).val(netAmt);
						$('#netAmt_'+pre_cnt).val(netAmt);
						$('#display_order_'+pre_cnt).val(parseInt(last_cnt)+1);
					}
				}else{
					$('#units_'+tax_proc_code).val('1');
					$('#charges_'+tax_proc_code).val(netAmt);
					$('#netAmt_'+tax_proc_code).val(netAmt);
					$('#display_order_'+tax_proc_code).val(parseInt(last_cnt)+1);
					//$("#proc_selfpay_"+tax_proc_code).prop("checked",true);
				}
			}
		}
	}
}

function task_done(commId) {
    var taskDone = '1';
    if($("#taskdone"+commId).is(':checked')==true){
        taskDone = '2';
        $.ajax({
		url: zPath+"/accounting/editComments.php?commId="+commId+"&taskDone="+taskDone,
            success: function(resp){
                if(resp=='success'){
                    $("#taskdoneTd"+commId).html('');
                    $("#taskdoneTd"+commId).html('<img src="'+zPath+'/../library/images/confirm.gif" width="16px" />');
                    top.fAlert("Task marked done successfully.");
                }			
            }
        });
    }
}

function get_operator_name_and_date(commId){
    if(default_user_selected){
        var t = operator+', '+current_date(top.jquery_date_format,'',true) + ': \n' + $("#commentsEditArea"+commId).val();
        $("#commentsEditArea"+commId).val(t);
        set_caret_position("commentsEditArea"+commId, 13);
    }
}

function del_batch_notes(del_id,enc_id,trans_mode,extra_id,cnfrm){
	if($("#acc_view_pay_only").val()==1  || $("#acc_edit_financials").val()==0){
		view_only_acc_call(0);
		return false;
	}
	if (typeof(cnfrm)=="undefined") {
		top.fancyConfirm('Are you sure you want to remove this record?','','top.del_batch_notes('+del_id+','+enc_id+',"'+trans_mode+'",'+extra_id+',true)');
		return;
	}else{
		var url="../accounting/acc_ajax.php?action_type=del_comment&del_comment_id="+del_id;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				resp = jQuery.parseJSON(resp);
				if(typeof(resp.del_pay_comm)!= "undefined"){
					var pay_comm_id =resp.del_pay_comm;
					$('#CommentTr'+pay_comm_id).hide();
				}
			}
		});
	}
}
function set_credited_prov(){
	if($('#primary_provider_id').val()!="" && ($('#secondary_provider_id').val()=="" || $('#enc_id_read').val()=="")){
		$('#secondary_provider_id').val($('#primary_provider_id').val());
	}
}

function task_reminder_date(commId){
	commId = commId || '';
	if(commId) {
		if($('#task_on_reminder'+commId).is(':checked')==true){
			$('#task_on_reminder'+commId).attr('checked',true);
			$('#task_on_reminder'+commId).val('yes');
		}else{
			$('#task_on_reminder'+commId).attr('checked',false);
			$('#task_on_reminder'+commId).val('no');
		}
	} else {
        if($('#task_on_reminder').is(':checked')==true){
            $('#task_on_reminder').attr('checked',true);
            $('#task_on_reminder').val('yes');
        }else{
            $('#task_on_reminder').attr('checked',false);
            $('#task_on_reminder').val('no');
        }
    }
}