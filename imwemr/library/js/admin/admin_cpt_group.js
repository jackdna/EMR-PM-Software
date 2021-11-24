var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('phrase_id','phrase');

function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading CPT Groups...');
	
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
	cpt_code_arr=r.cpt_code_arr;
	h='';var no_record='yes';
	if(r != null){
		row = '';
		for(x in result){no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='cpt_group_id'){pkId = tdVal;} 
				rowData[y] = tdVal;
				if(y=='cpt_group_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='cpt_code_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='status'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='2' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
	var cpt_code_array=new Array();
	var g=0;
	for(c1 in cpt_code_arr){
		g++;
		cc1 = cpt_code_arr[c1];	
		cpt_code_array.push(cc1.cpt_prac_code);
	}
	$('#cpt_code_name').typeahead({source:cpt_code_array});
}
function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#cpt_group_id').val('');
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
	if($.trim($('#cpt_group_name').val())==""){
		msg="&nbsp;&bull;&nbsp;Enter CPT Group Name";
	}
	if($.trim($('#cpt_code_name').val())==""){
		msg+="<br>&nbsp;&bull;&nbsp;Enter CPT Code";
	}
	if(msg){
		top.fAlert(msg);
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
				var grp_name=$('#cpt_group_name').val();
				top.fAlert("Record '<b>"+grp_name+"</b>' already exist.");		
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
function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();
	$('#cpt_group_id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on	= o.name;
			//alert(arrAllShownRecords[]);
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
	check_checkboxes();
	set_header_title('CPT Groups');	
});
show_loading_image('none');