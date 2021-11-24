var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('pos_fac_grp_id','pos_facility_group','fac_group_address','fac_group_city','fac_group_state','fac_group_zip','fac_phone');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	parent.parent.parent.show_loading_image('none');
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Facility Group...');
	
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
	pos= r.pos;
	h='';
	if(r != null){
		row = '';
		var r=1;
		for(x in result){
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='pos_fac_grp_id'){r++;pkId = tdVal;
				row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='pos_facility_group'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
                if(y=='fac_group_address'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';						
				}
				if(y=='fac_group_city'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';							
				}
				if(y=='fac_group_state'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';							
				}
				if(y=='fac_group_zip'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';							
				}
				if(y=='fac_phone'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';							
				}
				if(y=='fac_fax'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';							
				}
				if(y=='fac_tax_id'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';							
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;
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
		document.add_edit_frm.reset();
		$('#pos_fac_grp_id').val("");	
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}

function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	msg="";
	if($.trim($("#pos_facility_group").val())==""){
		msg+="<br> &bull; Enter the POS Facility Group";
	}
    if($.trim($("#fac_group_zip").val())==""){
		msg+="<br> &bull; Enter the "+top.zipLabel;
	}
	if(msg!=""){
		top.show_loading_image('hide');	
		var amsg="<b>Please Fill in the Following :-</b><br>"+msg;
		top.fAlert(amsg);
		return false;
	}
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d){
			top.show_loading_image('hide');
			if(d=='enter_unique'){
				top.fAlert("Poe is NOT saved.Please enter unique POE name.");
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
			else{top.fAlert('Record delete failed. Please try again.');}
		}
	});
}
function fillEditData(pkId){
	f = document.add_edit_frm;
	if(f){document.add_edit_frm.reset();}
	e = f.elements;
	$('#pos_fac_grp_id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.poe_name,formObjects)){
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];//alert(v);
			if(o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if(o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).prop('checked',true);
				}else if(o.type!='submit' && o.type!='button'){
					o.value = v;
				}
			}
		}
	}		
}
function set_phone_format(objPhone, default_format){
    var phntxt='phone';if(objPhone.id=='fac_fax') phntxt='fax';
	if(objPhone.value == "" || objPhone.value.length < 10){
		//invalid_input_msg(objPhone, "Please Enter a valid phone number");
		top.fAlert("Please Enter a valid "+phntxt+" number", '', '', '250px;');
		objPhone.value = "";
	}else{
		var refinedPh = objPhone.value.replace(/[^0-9+]/g,"");					
		if(refinedPh.length < 10){
			//invalid_input_msg(objPhone, "Please Enter a valid phone number");
			top.fAlert("Please Enter a valid "+phntxt+" number", '', '', '250px;');objPhone.value= "";
		}else{
			switch(default_format){
				case "###-###-####":
					objPhone.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(###) ###-####":
					objPhone.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				default:
					objPhone.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
			}
		}
	}
}

var ar = [["add_new","Add New","top.fmain.addNew();"],["pos_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	set_header_title('POS Facilities Group');
});
show_loading_image('none');