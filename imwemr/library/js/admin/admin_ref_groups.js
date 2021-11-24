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
	  success: function(r){
		showRecords(r);
	  }
	});
}
var ref_code_array = new Array;
var ref_name_array=new Array;
var ref_id_concat='';
var arr_reff_ids_exists=new Array;
var typeahead_ref_phy_name = new Array;
function showRecords(r){
	r = jQuery.parseJSON(r);
	result = r.records;
	ref_code_arr=r.ref_code_arr;
	ref_id_concat=r.ref_id_concat;
	var k=0;
	for(c2 in ref_id_concat){
		k++;
		cc2 = ref_id_concat[c2];
		ref_code_array[cc1.physician_Reffer_id]=cc1.reff_phy_ids_exist;
		ref_name_array[cc1.reff_name]=cc1.physician_Reffer_id;
	}
	
	h='';var no_record='yes';
	var g=0;
	for(c1 in ref_code_arr){
		g++;
		cc1 = ref_code_arr[c1];	
		typeahead_ref_phy_name.push(cc1.reff_name);		
		ref_code_array[cc1.physician_Reffer_id]=cc1.reff_name;
		ref_name_array[cc1.reff_name]=cc1.physician_Reffer_id;
	}
	if(r != null){
		row = '';
		var new_reff_ids="";
		for(x in result){no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='ref_group_id'){pkId = tdVal;}
				rowData[y] = tdVal;
				if(y=='ref_group_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='ref_id'){
					var reff_names="";
					if(tdVal){
						var ref_ids=tdVal.split(",");
						if(typeof(ref_ids)!="undefined" && s['status']!='In-Active'){
							new_reff_ids+=ref_ids+",";
						}
						for(var i=0;i<ref_ids.length;i++){
							if(ref_ids[i] && typeof(ref_code_array[ref_ids[i]])!="undefined")	{
								reff_names+=ref_code_array[ref_ids[i]];	
							}
						}
					tdVal=reff_names;	
					}
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='status'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	
	arr_reff_ids_exists=new_reff_ids.split(",");
	if(no_record=='yes'){h+="<tr><td colspan='4' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
	$("#ref_id").typeahead({source:typeahead_ref_phy_name});
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#ref_group_id').val('');
		document.add_edit_frm.reset();
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	$("#ref_id").typeahead({source:typeahead_ref_phy_name});
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}

function saveFormData(){
	if($.trim($('#ref_id').val())!=""){var eff_py_ids=set_ids($('#ref_id').val());}
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	
	var msg="";
	if($.trim($('#ref_group_name').val())==""){
		msg="&nbsp;&bull;&nbsp;Enter Group Name";
	}
	if($.trim($('#ref_id').val())==""){
		msg+="<br>&nbsp;&bull;&nbsp;Enter Ref Phy Name";
	}
	var ref_phy_id_new = $('#ref_phy_id').val();
	var ref_phy_id_new_arr = ref_phy_id_new.split(",");
	
	var ref_phy_id_db_new = $('#ref_phy_id_db').val();
	var ref_phy_id_db_new_arr = ref_phy_id_db_new.split(",");
	
	var boolrefPhyExist = false;
	var msg_refCode = "";
	
	for(var q=0;q<ref_phy_id_new_arr.length;q++) {
		if($.inArray(ref_phy_id_new_arr[q], arr_reff_ids_exists) !== -1 )	{
			if($.inArray(ref_phy_id_new_arr[q], ref_phy_id_db_new_arr) !== -1 )	{
				//DO NOT CHECK THEN	
			}else {
				boolrefPhyExist=true;
				msg_refCode+="<br>&nbsp;&bull;&nbsp;"+ref_code_array[ref_phy_id_new_arr[q]];
			}
		}
	}
	
	if(boolrefPhyExist && msg=="") {
		msg+="<b>Following Referring Physician(s) is/are occuring in multiple Groups:</b>"+msg_refCode;	
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
				var grp_name=$('#ref_group_name').val();
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
	$('#ref_group_name').val(pkId);
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
			if(on=="ref_id"){var reff_names_fill=""
				var ref_ids=v.split(",");
				if(ref_ids.length>1){
					for(var s=0;s<ref_ids.length;s++){
						if(ref_ids[s] && typeof(ref_code_array[ref_ids[s]])!="undefined")	{
							reff_names_fill+=ref_code_array[ref_ids[s]];	
						}
					}
				}else{
					reff_names_fill=ref_code_array[ref_ids];
				}
			}
		}
	}
			
	$('#ref_id').val(reff_names_fill);
}
function set_ids(ref_phy_name){
	var reff_ids_fill=name11="";
	var ref_id_already_exist="";
	if(ref_phy_name){
		var ref_ids=ref_phy_name.split(";");
		if(ref_ids.length>1){
			var j=0;
			for(var h=0;h<ref_ids.length;h++){
				var reff_phy_name=ref_ids[h].replace(";","");
				reff_phy_name=$.trim(reff_phy_name);
				reff_phy_name=reff_phy_name+"; ";
				if(ref_ids[h] && typeof(ref_name_array[reff_phy_name])!="undefined" )	{
					if(j==0){
						reff_ids_fill=ref_name_array[reff_phy_name];		
					}else{
						reff_ids_fill+=","+ref_name_array[reff_phy_name];		
					}
					j++;
					
				}
			}
			if(ref_id_already_exist){
				return 	ref_id_already_exist;
			}
		}else{
			reff_phy_name=reff_phy_name.replace(";","");
			reff_phy_name=$.trim(reff_phy_name);
			reff_ids_fill=ref_name_array[ref_phy_name+"; "];
			
		}
	}
	$("#ref_phy_id").val(reff_ids_fill);
}


var ar = [["add_new","Add New","top.fmain.addNew();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
LoadResultSet();
check_checkboxes();
set_header_title('Ref. Groups');
});
show_loading_image('none');