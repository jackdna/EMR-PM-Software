var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('phrase_id','phrase');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Test...');
	
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
	else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');so_url='&so='+so+'&soAD='+soAD;
	ajaxURL = "ajax_cl_charges.php?task=show_list"+s_url+p_url+f_url+so_url;
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
	dx_code_list = r.dx_code_list;
	cpt_prac_code_list = r.cpt_prac_code_list;
	
	var cpt_prac_code_list_array=new Array();
	var cpt_prac_code_list_array_key=new Array();
	var cpt_prac_name_arr=new Array();
	
	for(x in cpt_prac_code_list){
	
		ff = cpt_prac_code_list[x];				
		cpt_prac_code_list_array_key.push(ff.cpt_fee_id);
		cpt_prac_code_list_array.push(ff.cpt_prac_code);
		cpt_prac_name_arr[ff.cpt_prac_code]=ff.cpt_fee_id;
	}

	h='';var no_record='yes';
	if(r != null){
		row = '';
		for(x in result){
			no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='cl_charge_id'){pkId = tdVal;}
				rowData[y] = tdVal;
				if(y=='name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='cpt_practice_code'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				
				if(y=='icd10'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='price'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='charge_list_status'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='5' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
	
	dx_options = '<option value=""></option>';
	for(x in dx_code_list){
		ff = dx_code_list[x];
		dx_options += '<option  value="'+ff.id+'">'+ff.icd10+'</option>';
	}			
	$('#dx_code_id').html(dx_options);

	$(document).ready(function(){
		$('.cpt_prac').each(function(id,elem){
			var autocomplete = $(elem).typeahead();
			autocomplete.data('typeahead').source = cpt_prac_code_list_array;
			autocomplete.data('typeahead').updater = function(item){					
				var selected_id = cpt_prac_name_arr[item];
				$('#cpt_fee_id').val(selected_id);
				return item;
			};
		});
	});
}
function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#cl_charge_id').val('');
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
	var msg="";
	if(document.getElementById('cpt_practice_code').value=='' || document.getElementById('cpt_fee_id').value=='' || document.getElementById('cpt_fee_id').value=='0'){
		msg+='&nbsp;&bull;&nbsp;Please select CPT Practice Code from the typeahead list.<br>';
		msg+='&nbsp;&bull;&nbsp;If no list showing then you need to add CPT codes under Contact Lens category under CPT tab.';
	}
	
	if(msg){
		msg_val="<b>Please fill the following&nbsp;:-</b><br>"+msg;
		top.fAlert(msg_val);
		top.show_loading_image('hide');
		return false;
	}
	$.ajax({
		type: "POST",
		url: "ajax_cl_charges.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='enter_unique'){
				top.fAlert('Record already exist.');		
				return false;
			}
			if(d.toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(d);
				//top.fAlert(d);
			}else{
				top.fAlert(d);
			}
			$('#myModal').modal('hide');
			LoadResultSet();
		}
	});
}

function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();
	$('#cl_charge_id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];
			if (o.tagName == "INPUT" || o.tagName == "SELECT"){
				if (o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).attr('checked',true);
				} else if(o.type!='submit' && o.type!='button'){
					o.value = v;
				}
			}
		}
	}		
}

var ar = [["add_new","Add New","top.fmain.addNew();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
LoadResultSet();
set_header_title('CL Charges');
});
show_loading_image('none');