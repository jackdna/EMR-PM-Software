var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var typeahead_ref_phy_name = '';
var formObjects		   = new Array('phrase_id','phrase');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Facility Groups...');
	
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
var ref_code_array= new Array;
var ref_name_array = new Array;
var typeahead_ref_phy_name = new Array;
function showRecords(r){
	r = jQuery.parseJSON(r);
	result = r.records;
	fac_code_arr=r.fac_code_arr;
	h='';var no_record='yes';
	var g=0;
	for(c1 in fac_code_arr){
		g++;
		cc1 = fac_code_arr[c1];	
		typeahead_ref_phy_name.push(cc1.fac_name);
		ref_code_array[cc1.id]=cc1.fac_name;
		ref_name_array[cc1.fac_name]=cc1.id;
	}
	if(r != null){
		row = '';
		for(x in result){no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='fac_group_id'){pkId = tdVal;}
				rowData[y] = tdVal;
				if(y=='fac_group_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='fac_id'){
					var reff_names="";
					if(tdVal){
						var ref_ids=tdVal.split(",");
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
	if(no_record=='yes'){h+="<tr><td colspan='3' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
	$("#fac_id").typeahead({source:typeahead_ref_phy_name});
}
function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#fac_group_id').val('');
		document.add_edit_frm.reset();
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	$("#fac_id").typeahead({source:typeahead_ref_phy_name});
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}
function saveFormData(){
	
	var eff_py_ids=set_ids($('#fac_id').val());
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	var msg="";
	if($.trim($('#fac_group_name').val())==""){
		msg="&nbsp;&bull;&nbsp;Enter Group Name";
	}
	if($.trim($('#fac_id').val())==""){
		msg+="<br>&nbsp;&bull;&nbsp;Enter Facility";
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
				var grp_name=$('#fac_group_name').val();
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
	$('#fac_group_name').val(pkId);
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
			if(on=="fac_id"){var reff_names_fill=""
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
			
	$('#fac_id').val(reff_names_fill);
}
function set_ids(ref_phy_name){
	var reff_ids_fill = new Array;
	if(ref_phy_name){
		ref_phy_name = ref_phy_name.replace(",",";");
		var ref_ids=ref_phy_name.split(";");
		if(ref_ids.length>1){
			var j=0;
			for(var h=0;h<ref_ids.length;h++){
				var reff_phy_name=ref_ids[h].replace(";","");
				reff_phy_name=$.trim(reff_phy_name);
				reff_phy_name=reff_phy_name+"; ";
				if(ref_ids[h] && typeof(ref_name_array[reff_phy_name])!="undefined")	{
					if(j==0){
						reff_ids_fill.push(ref_name_array[reff_phy_name]);		
					}else{
						reff_ids_fill.push(ref_name_array[reff_phy_name]);		
					}
					j++;
					
				}
			}
		}else{
			reff_phy_name=reff_phy_name.replace(";","");
			reff_phy_name=$.trim(reff_phy_name);
			reff_ids_fill.push(ref_name_array[ref_phy_name+"; "]);
		}
	}
	var reff_id_str = reff_ids_fill.join();
	$("#ref_phy_id").val(reff_id_str);
	
}

var ar = [["add_new","Add New","top.fmain.addNew();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
LoadResultSet();
check_checkboxes();
set_header_title('Fac. Groups');
});
show_loading_image('none');