var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('phrase_id','phrase');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Test...');
	
	if(typeof(s)!='string' || s==''){s = 'Active';}
	s_url = "&s="+s;
	if(typeof(p)=='undefined'){p_url='';}else if($.trim(p)){p_url='&p='+p;$('a').parent('li').removeClass('pointer active');$('#'+p).parent('li').addClass('pointer active');$('#alphabet').val(p);}
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
	ajaxURL = "ajax_vendor.php?task=show_list"+s_url+p_url+f_url+so_url;
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
		for(x in result){
			no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='vendor_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+tdVal+'"><label for="'+tdVal+'"></label></div></td>';}//alert(pkId+':'+y);
				rowData[y] = tdVal;
				if(y=='vendor_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='contact_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='type'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='vendor_address'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='tel_num'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='mobile'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='fax'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='email'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
				}
				
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='8' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
}

function addNew(ed,pkId){
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
			modal_title = 'Add New Record';
			$('#vendor_id').val(''); 
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
	
	if($.trim($('#vendor_name').val())==""){
		msg+="&nbsp;&bull;&nbsp;Manufacturer/Lab&nbsp;<br>";
	}
	
	if(msg){
		msg_val="<b>Please fill the following&nbsp;:-</b><br>"+msg;
		top.fAlert(msg_val, '', 'top.fmain.add_edit_frm.vendor_name.focus()');
		top.show_loading_image('hide');
		return false;
	}
	$.ajax({
		type: "POST",
		url: "ajax_vendor.php",
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
			var sel_apha=$('#alphabet').val();
			LoadResultSet(sel_apha);
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
		url: "ajax_vendor.php",
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
	$('#vendor_id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on = o.name;
			v = arrAllShownRecords[pkId][on];
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
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
function set_phone_format(objPhone, default_format){
	if(objPhone.value == "" || objPhone.value.length < 10){
		top.fAlert("Please Enter a valid phone number");
		objPhone.value = "";
	}else{
		var refinedPh = objPhone.value.replace(/[^0-9+]/g,"");					
		if(refinedPh.length < 10){
			top.fAlert("Please Enter a valid phone number");objPhone.value= "";
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
function emailvalidation(entered, alertbox){
	with (entered){
		apos=value.indexOf("@"); 
		dotpos=value.lastIndexOf(".");
		lastpos=value.length-1;
		if (apos<1 || dotpos-apos<2 || lastpos-dotpos>3 || lastpos-dotpos<2){
			if (alertbox){
				top.fAlert(alertbox); 
				entered.value = '';
			}
			return false;
		}
		else{
			return true;
		}
	}
} 

var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('Vendor');
	//start
	var alphabet='';
	var first="A",last="Z";
	var ch='';
	for(var i = first.charCodeAt(0); i <= last.charCodeAt(0); i++){
		ch=eval("String.fromCharCode("+i+")");
		cl='';if(ch=='A'){cl='pointer active';}
		alphabet+="<li class=\""+cl+"\"><a id=\""+ch+"\" onClick='LoadResultSet(\""+ch+"\")' style='cursor:pointer'>"+ch+"</a></li>";
	}
	$("#pagenation_alpha_order").html(alphabet);
//end	
});
show_loading_image('none');