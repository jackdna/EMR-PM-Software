var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('id','comment');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Laterality...');
	
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
	ajaxURL = "diag_laterality_ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {//a=window.open();a.document.write(r); ///*dataType: "json",*/
		showRecords(r);
	  }
	});
}
function showRecords(r){
	r = JSON.parse(r);
	result = r.records;
	icd10_lat = r.icd10_l;
	
	var lat_name_arr = new Array();
	var lat_html = '<option value=""></option>';
	for (x in icd10_lat){
		ll = icd10_lat[x];
		lat_name_arr[ll.id] = ll.title;
		lat_html += '<option value="'+ll.id+'">'+ll.title+'</option>';
	}
	$('#under').html(lat_html);
	h='';
	if(r != null){
		row = '';
		for(x in result){
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='title'){
					td1='';
					if(s['under']==0){
						td1='style="font-weight:bold;"';
					}
					row	+= '<td '+td1+' onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='abbr'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='code'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='under'){
					if(tdVal!='' && typeof(lat_name_arr[tdVal])!='undefined'){
						tdVal=lat_name_arr[tdVal];
					}else{
						tdVal="";
					}
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#diag_cat_id').val('');
		document.add_edit_frm.reset();
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}
function saveFormData(){
	var cat_nm = $("#title").val();
	if($.trim(cat_nm)=="")
	{
		top.fAlert('Please Enter the Laterality Name');
		return false;	
	}
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	$.ajax({
		type: "POST",
		url: "diag_laterality_ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='enter_unique'){
				top.fAlert('Catagory already exist.');		
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
	})
	if(pos_id!=''){
		top.fancyConfirm("Are you sure you want to delete?","","window.top.fmain.deleteModifiers('"+pos_id+"')");
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
		url: "diag_laterality_ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='1'){top.alert_notification_show('Record deleted.'); LoadResultSet();}
			else{top.fAlert('Record delete failed. Please try again.');}
		}
	});
}
function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	$('#id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.comment,formObjects)){
			on	= o.name;
			//alert(arrAllShownRecords);
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
function daigList(){
	var btnArr = new Array();
	top.btn_show("ADMN",btnArr);
	parent.parent.show_loading_image('inline');
	top.fmain.location.href = "../admin/billing/icd10/index.php";
}

var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"],["dx_cat","Diagnosis List","top.fmain.daigList();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
});
show_loading_image('none');
set_header_title('Laterality');