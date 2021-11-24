var cpt_code_array=new Array();
var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('id','title');
var cpt_fee_array=new Array();var rev_array=new Array();var mod_array=new Array();
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Pre Auth Templates...');
	
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
	ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {
		showRecords(r);
	  }
	});
}
function showRecords(r){
	r = JSON.parse(r);
	result = r.records;
	dx_code_arr=r.dx_code_arr;
	cpt_code_arr=r.cpt_code_arr;
	cpt_fee_column_arr=r.cpt_fee_column;
	revenue_code_array=r.revenue_code_arr;
	mod_code_array=r.mod_code_arr;
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
				if(y=='template_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='2' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
	
	var e=0;
	var dx_code_array=new Array();
	for(d in dx_code_arr){
		e++;
		dd1 = dx_code_arr[d];
		dx_code_array.push(dd1.dx_code);
	}
	
	$('.t_wid').each(function(id,elem){
		$(elem).typeahead({source:dx_code_array});
	});	
	
	var g=0;
	for(c1 in cpt_code_arr){
		g++;
		cc1 = cpt_code_arr[c1];	
		cpt_code_array.push(cc1.cpt_prac_code);
	}
	var f=0;
	for(f1 in cpt_fee_column_arr){
		f++;
		ff1 = cpt_fee_column_arr[f1];	
		cpt_fee_array[ff1.cpt_prac_code]=ff1.cpt_fee;
	}
	var rev=0;
	
	for(re in revenue_code_array){
		rev++;
		re1 = revenue_code_array[re];	
		rev_array.push(re1.r_code);
	}
	var modr=0;
	
	for(mo in mod_code_array){
		modr++;
		me1 = mod_code_array[mo];	
		mod_array.push(me1.mod_prac_code);
	}
}
function addNew(ed,pkId){
	var modal_title = '';
	$("#auth_main_tbl tbody tr" ).each(function(){this.parentNode.removeChild( this ); });
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#id').val('');
		document.add_edit_frm.reset();
		addNewRow('');
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}

function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	if($.trim($('#template_name').val())==""){
		top.fAlert("&bull; Enter the template name<br>");
		top.show_loading_image('hide');
		return false;
	}
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='enter_unique'){
				top.fAlert('Record already exist.');		
				return false;
			}
			if(d.toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(d);
			}else{
				top.fAlert(d);
			}
			$('#myModal').modal('hide');
			LoadResultSet();
		}
	});
}
function deleteSelectet(){
	pos_id = '';
	$('.chk_sel').each(function(){
		if($(this).prop("checked")){
			pos_id += $(this).val()+', ';
		}
	})
	if(pos_id!=''){
		top.fancyConfirm("Are you sure you want to delete?","", "window.top.fmain.deleteModifiers('"+pos_id+"')");
	}else{
		top.fAlert('No Record Selected.');
	}
}
function deleteModifiers(pos_id) {
	pos_id = pos_id.substr(0,pos_id.length-2);
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Deleting Record(s)...');
	frm_data = 'pkId='+pos_id+'&task=delete';
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
			else{top.fAlert(d+'Record delete failed. Please try again.');}
		}
	});
}
function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();
	$('#id').val(pkId);
	var proc_select_box_arr=new Array();
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on	= o.name;var obn=o.id;
			v	= arrAllShownRecords[pkId][on];
			v1	= arrAllShownRecords[pkId][obn];
			if(on=='last_cnt'){
				if(v>0){
					var d;
					for(var c=0;c<=v;c++){
						addNewRow(c);
					}
				}else{
					addNewRow('');
				}
			}var objid=obn.substring(0,12);
			if(objid=="diagText_all"){
				if(v1 && typeof(v1)!="undefined"){
					var arr_val=v1+"~||~"+obn;
					proc_select_box_arr.push(arr_val);
				}
			}
			var objuid=obn.substring(0,6);
			if(objuid=="units_"){
				if(v=="" || typeof(v)=="undefined"){
					v=1;
				}
			}
							
			if(typeof(v)=="undefined"){v="";}
			if (o.tagName == "INPUT" || o.tagName == "SELECT"){
				if (o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).attr('checked',true);
				} else if(o.type!='submit' && o.type!='button'){
					if(on!='last_cnt')o.value = v;
				}
			}
		}
	}
	if($("#last_cnt").val()=="" ||$("#last_cnt").val()==0){
		$("#last_cnt").val(1);
	}	crt_dx_dropdown();
	for(var i=0;i<proc_select_box_arr.length;i=i+1) {
		var cpt_id_val=proc_select_box_arr[i];
		cpt_arr_val=cpt_id_val.split("~||~");
		checked_selected(cpt_arr_val[1],cpt_arr_val[0]);
	}
}
function addNewRow(pre_cnt){
	var td_val=tr_val="";
	if(pre_cnt=="" || parseInt(pre_cnt)==0){
		pre_cnt=1;
	}else{
		var cnt_t=$("#last_cnt").val();
		pre_cnt=(parseInt(cnt_t)+parseInt(1));
	}
	$("#last_cnt").val(pre_cnt);
	td_val = '<td><input id="detail_id_'+pre_cnt+'" name="detail_id_'+pre_cnt+'" type="hidden"><input id="procedureText_'+pre_cnt+'" name="procedureText_'+pre_cnt+'" type="text" class="form-control" class="cpt_typehead" onchange="set_fee_val(this.value,'+pre_cnt+')"></td>';
	td_val += '<td><input id="revcode_'+pre_cnt+'" type="text"  name="revcode_'+pre_cnt+'" class="form-control"></td>';
	td_val += '<td><select id="diagText_all_'+pre_cnt+'" name="diagText_all_'+pre_cnt+'[]" class="diagText_all_css selectpicker" multiple data-title="Select Dx Code"></select></td>';
	td_val += '<td><input id="mod1Text_'+pre_cnt+'" type="text" name="mod1Text_'+pre_cnt+'" class="form-control"></td>';
	td_val += '<td><input id="mod2Text_'+pre_cnt+'" type="text" name="mod2Text_'+pre_cnt+'"  class="form-control"></td>';
	td_val += '<td><input id="mod3Text_'+pre_cnt+'" type="text" name="mod3Text_'+pre_cnt+'" class="form-control"></td>';
	td_val += '<td><input type="text" id="units_'+pre_cnt+'" name="units_'+pre_cnt+'"  value="1"  class="form-control" onblur="set_fee_val(\'\','+pre_cnt+');"></td>';
	td_val += '<td><div class=\"input-group\"><label for="charges_'+pre_cnt+'" class=\"input-group-addon\">'+currency+'</label><input type=\"text\" id="charges_'+pre_cnt+'" name="charges_'+pre_cnt+'" class=\"form-control\"></div></td>';
	td_val += '<td><input type="text" id="comments_'+pre_cnt+'" name="comments_'+pre_cnt+'" class="form-control" onblur="addNewRow('+pre_cnt+');"></td>';
	td_val += '<td style="text-align:center;white-space:nowrap;"><img id="add_row_'+pre_cnt+'" src=\"../../../../library/images/add_small.png\" alt="Add More" onClick="addNewRow('+pre_cnt+');">&nbsp;&nbsp;&nbsp;<img id="add_row_'+pre_cnt+'" src=\"../../../../library/images/close_small.png\" onClick="removeTableRow('+pre_cnt+');"></td>';	
	tr_val = '<tr id="tr_'+pre_cnt+'">' + td_val + '</tr>';
	$("#auth_main_tbl").append(tr_val);
	$('.selectpicker').selectpicker('refresh');
	$('[name^=procedureText_]').each(function(id,elem){
		$(elem).typeahead({source:cpt_code_array});
	});
	crt_dx_dropdown();	
	$('[name^=revcode_]').each(function(id,elem){
		$(elem).typeahead({source:rev_array});
	});
	$('[name^=mod1Text_]').each(function(id,elem){
		$(elem).typeahead({source:mod_array});
	});
	$('[name^=mod2Text_]').each(function(id,elem){
		$(elem).typeahead({source:mod_array});
	});
	$('[name^=mod3Text_]').each(function(id,elem){
		$(elem).typeahead({source:mod_array});
	});
	$('#procedureText_'+pre_cnt).focus();
	$('.selectpicker').selectpicker('refresh');
}
function removeTableRow(id){
	//$("#detail_id_"+id).val("");
	$("#procedureText_"+id).val("");
	$("#revcode_"+id).val("");
	$("#diagText_all_"+id).val("");
	$("#mod1Text_"+id).val("");
	$("#mod2Text_"+id).val("");
	$("#mod3Text_"+id).val("");
	$("#units_"+id).val("");
	$("#charges_"+id).val("");
	$("#comments_"+id).val("");
	var pre_cnt=$("#last_cnt").val();
	var pkId=$('#detail_id_'+id).val()
	if(parseInt(pre_cnt)>1){
		$("#tr_"+id).hide();
		if(pkId){
			frm_data = 'task=delete_sub&pkId='+pkId;
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: frm_data,
				success: function(d){
				}
			});
		}
	}
}
function crt_dx_dropdown(){
	var all_rec = new Array;
	$(".t_wid").each(function(id,elem) {
		if($(elem).val()!=""){
			var cpt_val_num=$(elem).val();
			var cpt_num_val=cpt_val_num.replace(".", "_");
			all_rec.push(cpt_num_val);
		}
	});
	
	$("select.diagText_all_css").each(function(id,elem){
		var all_opt_data = "";
		var sel_val_arr=$(elem).val();
		if(typeof sel_val_arr !="undefined" && sel_val_arr!=null && sel_val_arr!=''){
			for(x in all_rec){
				if(all_rec[x]!=""){
					var sel_opt="";
					var yy=parseInt(x)+1;
					var chk_sel_rec= all_rec[x];
					if($.inArray(chk_sel_rec,sel_val_arr)!=-1){
						sel_opt="selected";
					}
					chk_sel_rec=chk_sel_rec.replace(".", "_");
					chk_opt_rec=all_rec[x].replace("_", ".");
					all_opt_data += '<option value="'+chk_sel_rec+'" '+sel_opt+'>'+chk_opt_rec+'</option>';
				}
			}
		}else{
			for(x in all_rec){
				if(all_rec[x]!=""){
					var yy=parseInt(x)+1;
					var option_val=all_rec[x];
					option_val=option_val.replace(".", "_");
					chk_opt_rec=all_rec[x].replace("_", ".");
					all_opt_data += '<option value="'+option_val+'">'+chk_opt_rec+'</option>';
				}
			}
		}
		$(elem).html(all_opt_data);
		$(elem).selectpicker('refresh');
	});
}
function set_fee_val(obj_val,id){
	if(obj_val==""){obj_val=$("#procedureText_"+id).val();}
	if(obj_val){
		var fee_val=parseFloat(cpt_fee_array[obj_val]);
		var unit_val=parseFloat($("#units_"+id).val());
		if(unit_val){
			fee_val=parseFloat(fee_val*unit_val);
			fee_val=fee_val.toFixed(2);
		}
		$("#charges_"+id).val(fee_val);
	}
}
function checked_selected(obj,cpt_arr_str){
	var data_str=cpt_arr_str;
	dataarray=data_str.split(",");
	var i = 0, size = dataarray.length;
	for(i; i < size; i++){
		var optionVal=dataarray[i];
		$("#"+obj).find("option[value="+optionVal+"]").prop("selected", "selected");
	} 
	$("#"+obj).selectpicker('refresh');
}
var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('Pre Auth Templates');
	$('.selectpicker').selectpicker('refresh');
	$(".t_wid").on('blur',function(){
		crt_dx_dropdown();
	});
	$('body').on('shown.bs.modal','#myModal',function(){
		set_modal_height('myModal');
	});
});
show_loading_image('none');