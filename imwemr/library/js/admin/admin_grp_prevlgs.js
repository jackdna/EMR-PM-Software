var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('id','allergie_name');

//------------------------------------- Common functions


function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Records...');
	
	if(typeof(s)!='string' || s==''){s = 'Active';}
	s_url = "&s="+s;
	
	if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
	if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
	
	oso		= $('#ord_by_field').val(); //old_so
	soAD	= $('#ord_by_ascdesc').val();
	if(typeof(so)=='undefined' || so==''){
		so 		= $('#ord_by_field').val();
	}else{
		$('#ord_by_field').val(so);
		if(oso==so){
			if(soAD=='ASC') soAD = 'DESC';
			else  soAD = 'ASC';
		}else{
			soAD = 'ASC';
		}
		$('#ord_by_ascdesc').val(soAD);
	};
	if(soAD=='ASC') $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
	else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
	so_url='&so='+so+'&soAD='+soAD;
	ajaxURL = "gp_ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {
		showRecords(r);
		var t = $("#edid").val();
		if(t!=""&&typeof(t)!="undefined"&&t!="undefined"){ $("#edid").val(""); addNew(1,t); }  
	  }
	});
}
function showRecords(r){
	r = jQuery.parseJSON(r);
	result = r.records;
	h='';var no_record='yes';
	if(r != null){
		row = '';
		for(x in result){no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='gr_name' || y=='prevlgs'){
					row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='6' style='text-align:center; cursor: default;'>No record found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
	
}

function popup(sh){	
	
	var s = "hide" ;
	if(sh=="1"){
		s = "show" ;
		var h = $(window).height(); h = parseInt(h*60/100);
		$("#ele_priv_chk").css({"height":h, 'overflow':'auto'});
		
		/// TEST -- 
		/*
		var tt = "";
		$("#ele_priv_chk").find(":checkbox").each(function(){
				
				tt = "";
				if(typeof(this.title)!="undefined") { tt = this.title; }
				else{
					tt = $("#ele_priv_chk label[for='"+this.id+"']").html();	
				}
				if(this.checked){
				console.log(""+tt+" - "+this.name);
				}
			
			});
		
		//*/
		//---
		
	}
	
	$("#myModal").modal(s);
}
	
function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#adm_grp_prv_Id').val('');
		document.add_edit_frm.reset();
	}
	$('#add_edit_frm input').prop('disabled', false);
	$('#myModal .modal-header .modal-title').text(modal_title);
	popup(1);
	$("#el_main_priv .checkboxcolor").each(function(){ $(this).removeClass("checkboxcolor"); }); //
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	//
	onload_privilages_popup();
}	

function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();
	
	$('#id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		
		if($.inArray(o.phrase,formObjects)){
			on = o.name;
			if(on == "erx_chk"){ on="priv_erx"; }
			v = arrAllShownRecords[pkId][on];
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if (o.type == "checkbox" || o.type == "radio"){
					oid = on;					
					if(on == "priv_erx"){ oid="erx_chk"; }	
					$('#'+oid).prop('checked',v);
				} else if(o.type!='submit' && o.type!='button'){
					o.value = v;
				}
			}
		}
	}
}
	
	
function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	$('#add_edit_frm input').prop('disabled', false);
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	var msg = "";
	if($.trim($('#gr_name').val())==""){
		msg += "Please Enter Privilege Name.<br/>";
	}
	
	
	//Privileges
	var flg_prvlgs = parseInt($("#ele_priv_chk").find("div.checkbox").find(":checked[id!=priv_chart_finalize][title!='Select All']").length) +  //.not("div.checkboxcolor")
					parseInt($("#allprivdivs").find("div.checkbox").find(":checked").length);	
	if(flg_prvlgs<=0){
		msg += "Please Enter Privileges.";
	}
	
	//
	if(msg!=""){
		top.fAlert(msg);
		top.show_loading_image('hide');
		return false;
	}
	
	$.ajax({
		type: "POST",
		url: "gp_ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='enter_unique'){
				top.fAlert('Specialty "<b>'+$('#name').val()+'</b>" already exist.');		
				return false;
			}
			if(d.toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(d);
				console.log(d);
			}else{
				top.fAlert(d);
				console.log(d);
			}
			$('#myModal').modal('hide');
			LoadResultSet();
		}
	});
	
	}

	
function delete_gp(){
	pos_id = '';
	$('.chk_sel').each(function(){
		if($(this).is(':checked')){
			pos_id += $(this).val()+', ';
		}
	});
	if(pos_id!=''){
		deleteModifiers(pos_id);
		//top.fancyConfirm("Are you sure you want to delete?","", "window.top.fmain.deleteModifiers('"+pos_id+"')");
	}else{
		top.fAlert('No Record Selected.');
	}
}
function deleteModifiers(pos_id, flgwarn) {
	var msg, task;
	if(typeof(flgwarn)=="undefined"){		
		msg = 'Processing...';		
		task = 'get_users_for_del';
		pos_id = pos_id.substr(0,pos_id.length-2);
	}else{
		msg = 'Deleting Record(s)...';
		task = 'delete';
	}
	var pos_id_tmp = pos_id;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', msg);
	var frm_data = 'pkId='+pos_id+'&task='+task;	
	$.ajax({
		type: "POST",
		url: "gp_ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(typeof(flgwarn)!="undefined"){	
				if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
				else{top.fAlert(d+'Record delete failed. Please try again.');}
			}else{
				//d
				top.fancyConfirm(""+d,"", "window.top.fmain.deleteModifiers('"+pos_id_tmp+"', 1)");
			}
		}
	});
}




function sel_all_usrs(){
	var ser = $('#chk_sel_users').prop("checked");	
	$('#dv_usrs :checkbox').prop("checked", ser);	
}

function change_previleges(flgcnfm){	
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	
	
	var uids="";
	$('#dv_usrs :checked').each(function(){ if(this.value!=""){uids+=""+this.value+",";} }); uids = $.trim(uids);		
	if(uids==""){
		top.fAlert("Please select user(s)");
		top.show_loading_image('hide');
		return false;
	}
	$("#uids").val(""+uids);
	
	if($("#divPrevileges").hasClass("active")){
		if($('#ele_priv_chk :checked').length <= 0){		
			top.fAlert("Please select privileges");
			top.show_loading_image('hide');
			return false;
		}
		$("#el_privileges").val("");
	}else if($("#divGroupPrevileges").hasClass("active")){		
		if($("#el_privileges").val()==""){
			top.fAlert("Please select privileges");
			top.show_loading_image('hide');
			return false;
		}
	}
	
	//Waring
	if(typeof flgcnfm == "undefined"){
		top.show_loading_image('hide');
		top.fancyConfirm("This action cannot be undone. Are you sure to perform this action?", "Warning change privileges", "top.fmain.change_previleges(1)");
		return;	
	}	
	
	$('#add_edit_frm input').prop('disabled', false);
	var frm_data = $('#add_edit_frm').serialize()+'&task=save_previleges';
	
	$.ajax({
		type: "POST",
		url: "gp_ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');			
			if(d.toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(d);
				console.log(d);
			}else{
				top.fAlert(d);
				console.log(d);
			}
			if($("#priv_all_settings").prop("checked")){
				$("#priv_all_settings").triggerHandler("click");	
			}
		}
	});
	
}

function edit_grp_prv(i){top.$('#fmain').prop('src',"../admin/groupprivileges/index.php?edid="+i);}

function get_prvlgs(){
	$("#dv_prvlgs").html("");
	var i = $("#el_privileges").val();
	if(typeof(i)=="undefined" || i==""){ return; }

	$.get("gp_ajax.php?task=get_prvlgs&id="+i, function(d){
		if(typeof(d)=="undefined"){ d=""; }
		var we = (top.$("#groupPrivileges").length>0) ? "<button class=\"btn btn-primary\" onclick=\"edit_grp_prv("+i+")\">Edit</button>" : "";
		$("#dv_prvlgs").html("<p>"+d+"</p>"+we);	
	});
}

function show_log(){
	$("#logModal").modal("hide");
	top.show_loading_image('show');
	$.get("gp_ajax.php?task=show_log", function(d){
			top.show_loading_image('hide');
			if(d){
				$("#logModal .modal-body").html(d);
				$("#logModal").modal("show");
				
				var h = $(window).height(); h = parseInt(h*60/100);
				$("#logdv").css({"height":h, 'overflow':'auto'});
				
			}
		});	
}

$(document).ready(function(){
	
	if($("#page").val() == "grp_prvlgs"){
	
	LoadResultSet();	
	var hdrtitle = "Group Privileges";	
	var ar = [["document_submit","Add New","top.fmain.addNew();"], 
			
			["document_submit","Delete","top.fmain.delete_gp();"]];
	
	check_checkboxes();
	$('[data-toggle="tooltip"]').tooltip();
		
	}else if($("#page").val() == "change_prvlgs"){

	var hdrtitle = "Change Privileges";	
	var ar = [["document_submit","Save","top.fmain.change_previleges();"],
			["document_submit","Log","top.fmain.show_log();"]];		
		
		$(":checkbox[id*=chk_cat_]").bind("click", function(){			
			var t = this.id.replace(/chk_cat_/, 'collapse');
			$("#"+t+" :checkbox").prop("checked", this.checked);
			});
		$(":checkbox[id*=chk_sel_]").bind("click", function(){
				if(this.checked==false){
					$(this).parents(".panel").find(".panel-heading :checkbox").prop("checked", this.checked);
					$("#chk_sel_users").prop("checked", this.checked);	
				}else{
					if($(this).parents(".panel-body").find(":checked").length == $(this).parents(".panel-body").find(":checkbox").length){
						$(this).parents(".panel").find(".panel-heading :checkbox").prop("checked", this.checked);
						if($(":checkbox[id*=chk_cat_]").length == $(":checked[id*=chk_cat_]").length){
							$("#chk_sel_users").prop("checked", this.checked);
						}
					}	
				}
			});
			
		//resi			
		$("#usr_acrdn :checkbox").bind("click", function(){
				if($("#dv_usrcat_11 :checked").length>0 && $("#usr_acrdn :checked").length == $("#dv_usrcat_11 :checked").length){
					$(".chart_final").removeClass("invisible").addClass("visible");	
				}else{
					$(".chart_final").removeClass("visible").addClass("invisible");
					$("#priv_chart_finalize").prop("checked",false);	
				}
			});
	}
	
	//	
	set_header_title(hdrtitle);
	top.btn_show("ADMN",ar);
	top.show_loading_image('hide');
});




