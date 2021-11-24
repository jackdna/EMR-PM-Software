var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('fu_id','ptVisit');
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
	ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
	$.ajax({
	  url: ajaxURL,
		success: function(r) {
			showRecords(r);
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
				if(y=='era_rule_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="fu_id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='era_trans_method' || y=='era_cas_code'){
					row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
					
					if(y == 'era_cas_code') row += '<td class="text-center"><button class="btn btn-success btn-sm" data-history="'+pkId+'" onClick="showHistory(this);" data-toggle="tooltip" title="Log" data-placement="top"><i class="glyphicon glyphicon-time"></i></button></td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='6' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	$('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });
	top.show_loading_image('hide');
}

//Get history for the selected row
function showHistory(obj){
	var rowId = ($(obj).data('history')) ? $(obj).data('history') : null;

	if(!rowId) return false;

	$.ajax({
		url: 'ajax.php',
		method: 'GET',
		dataType: 'JSON',
		data: {rowId: rowId, task: 'getHistory'},
		beforeSend: function(response){

		},
		success: function(response){

			var status = response.status;
			var respData = response.data;

			//If there is any error, alert user
			if(!status && typeof(response.data) == 'string'){
				fAlert(response.data);
				return false;
			}

			var htmlArr = new Array();
			$.each(respData, function(index,element){
				index += 1;

				var htmlStr = "<tr>";
				//htmlStr += '<td>'+index+'</td>';
				htmlStr += '<td>'+element.method+'</td>';
				htmlStr += '<td>'+element.codes+'</td>';
				htmlStr += '<td>'+element.date+'</td>';
				htmlStr += '<td>'+element.username+'</td>';

				htmlStr += '</tr>';

				htmlArr.push(htmlStr);
			});

			if(htmlArr.length > 0){
				var modalStr = htmlArr.join('');
				$('#historyModal').find('#historyResult').html(modalStr);
				$('#historyModal').modal('show');
			}
		},
		complete: function(){
			
		}
	});
}

function addNew(ed,pkId){
	$("#era_cas_code_nw option").prop("selected", false);
	fun_mselect("#era_cas_code_nw", 'refresh');
	
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#adm_epostId').val('');
		document.add_edit_frm.reset();
		
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}			

function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	
	var codes = fun_mselect("#era_cas_code_nw", 'val');
	codes = $.trim(codes);
	
	var msg="";
	if($.trim(codes)==""){
		msg+="<br/>\t\t- CAS Codes";
	}
	
	if($.trim($('#era_trans_method').val())==""){
		msg+="<br/>\t\t- Method";
		
	}
	
	if(msg!=""){
		top.fAlert("Please enter following :"+msg);
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
				top.fAlert(' "<b>'+$('#era_trans_method').val()+'</b>" method already exist.');		
				return false;
			}
			if(d.indexOf('enter_unique_2')!=-1){
				var ar = d.split(":");
				
				top.fAlert(''+ar[1]);		
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
		if($(this).is(':checked')){
			pos_id += $(this).val()+', ';
		}
	});
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
	$('#adm_epostId').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on = o.name;
			if(o.id == "era_cas_code_nw"){ on="era_cas_code"; }
			v = arrAllShownRecords[pkId][on];
			
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if (o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).attr('checked',true);
				} else if(o.type!='submit' && o.type!='button'){
					if(o.id == "era_cas_code_nw"){
						fun_mselect(o, "select", v, '', 1);
						fun_mselect('.selectpicker','width', '.form-group');
					}else{					
						o.value = v;
					}
				}
			}
		}
	}		
}

var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('ERA Rules');
	fun_mselect('.selectpicker', 'render');
	fun_mselect('.selectpicker','width', '.form-group');
});
show_loading_image('none');









