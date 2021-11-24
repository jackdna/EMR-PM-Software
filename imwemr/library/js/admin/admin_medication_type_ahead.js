var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('id','medicine_name');
var custom_array_name="";
var custom_array_id="";
custom_array_id = (typeof temp_js_arr.upc_arr !== 'undefined') ? temp_js_arr.upc_arr : [];
custom_array_name = temp_med_js_arr;
function LoadResultSet(p,f,s,so,currLink,alpha,page,record_limit,searchStr,cont_num){//p=practice code, f=fac code, s=status, so=sort by;
	var cont_num = cont_num || 1;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Medication...');
	
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
	
	if(typeof(alpha)=='undefined' || alpha==''){
		alpha = $('#pg_aplhabet').val();
	}else{
		$('#pg_aplhabet').val(alpha);
	}
	$('a').parent('li').removeClass('pointer active');
	$('#'+alpha).addClass('activealpha');
	$('#'+alpha).parent('li').addClass('pointer active')
	
	if(typeof(page)=='undefined' || page==''){
		page = $('#page').val();
	}
	if(typeof(record_limit)=='undefined' || record_limit==''){
		record_limit = $('#record_limit').val();
	}
	else{
		$('#record_limit').val(record_limit);
	}
	search_Url = "";
	if(typeof(searchStr)!='undefined' && searchStr!=''){
		search_Url = "&searchStr="+searchStr;
	}
	pg_url = '&alpha='+alpha+'&page='+page+'&record_limit='+record_limit;		
	//ajaxURL = "ajax.php?ajax_task=show_list"+s_url+so_url+pg_url+search_Url;
	ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url+pg_url+search_Url;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {
		showRecords(r,cont_num);
	  }
	});
}
function showRecords(r,cont_num){
	r = jQuery.parseJSON(r);
	result = r.records;
	var total_pages = r.total_pages;
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
				if(y=='medicine_name'){
					row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');" style="white-space:normal; width:105px;">&nbsp;'+tdVal+'</td>';
				}
				if(y=='ocular'){cls='';
						if(tdVal=='1'){
						cls='conf';
					}
					tdVal="";
					row	+= '<td class="leftborder '+cls+'" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='glucoma'){
					cls='';
					if(tdVal=='1'){
						cls='conf';
					}
					tdVal="";
					row	+= '<td class="leftborder '+cls+'" style="padding-left:5px;" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='ret_injection'){
					cls='';
					if(tdVal=='1'){
						cls='conf';
					}
					tdVal="";
					row	+= '<td class="leftborder '+cls+'" style="padding-left:5px;" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='alias'){
					row	+= '<td class="leftborder" style="width:105px;padding-left:5px;" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='description'){
					row	+= '<td class="leftborder" style="padding-left:5px;" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='prescription'){cls='';
					if(tdVal=='1'){
						cls='conf';
					}
					tdVal="";
					row	+= '<td  class="leftborder '+cls+'" style="padding-left:5px;" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='alert'){cls='';
					if(tdVal=='1'){
						cls='conf';
					}
					tdVal="";
					row	+= '<td  class="leftborder '+cls+'" style="padding-left:5px;" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='ccda_code'){
					row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='fdb_id'){
					row	+= '<td colspan=2 class="leftborder" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='10' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);
	num_paging(total_pages,cont_num);
	top.show_loading_image('hide');
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){
		modal_title = 'Edit Record';
		$("#opt_med_name").prop('disabled',true);	
	}
	else {
		modal_title = 'Add New Record';
		$('#id').val(''); 
		document.add_edit_frm.reset();
		$("#alertmsg").prop('disabled',true);
		$("#opt_med_name").prop('disabled',true);
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}		

function saveFormData(){
	if($('#opt_med_id').val()=='' || $('#opt_med_id').val()==0)getTrioVal();
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	if($.trim($('#medicine_name').val())==""){
		top.fAlert("Enter The Medicine Name");
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
			else{top.fAlert(d+'Record delete failed. Please try again.');}
		}
	});
}
function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();
	$('#id').val(pkId);
	$("#alertmsg").prop('disabled',true);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];
			
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if (o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).prop('checked',true);
					if(oid=='alert_1'){
						$("#alertmsg").prop('disabled',true);
						if($("#alert_1").is(":checked")){
							$("#alertmsg").removeAttr('disabled');
						}
					}
					else if(oid=='tracked_inventory_1'){
						$("#opt_med_name").prop('disabled',true);
						if($("#tracked_inventory_1").is(":checked")){
							$("#opt_med_name").removeAttr('disabled');
						}
					}
				} else if(o.type!='submit' && o.type!='button'){
					o.value = v;
				}
			}
		}
	}	
	if($('#ocular_1').prop('checked') == true){
		$("#divMedOrder").show();
		$("#divMedOrder1").show();
	}
	else {
		$("#divMedOrder").hide();
		$("#divMedOrder1").hide();
	}
}
function check_umls(obj){
	medName = top.trim(obj.value);
	if(medName!=""){
		top.show_loading_image('block');
		$('#fdb_content').html('');
		$.ajax({
			type: "POST",
			url: top.WRP+"/interface/Medical_history/medications/check_umls.php?medName="+encodeURI(medName),
			complete: function(r){
				check_fdb();
				response = r.responseText;
				if(response != null && typeof(response)!='undefined' && response!=''){
					$("#div_disable").css("display", "block"); 
					$('#umls_content').html(response);
					$('#div_umls').show();
				}else{
					$('#div_umls').hide();
					$("#div_disable").css("display", "none"); 
				}
				top.show_loading_image('none');
			}
		});
	}
}
function fill_med_code(med,code,index){
	index = index || "";
	$('#medicine_name').val(med);
	$('#ccda_code').val(code);
	$('#fdb_id').val('');
	//check_fdb();
}
function check_fdb(){
	var medName = $('#medicine_name').val();
	if(medName!=""){
		parent.parent.show_loading_image('block');	
		$.ajax({
			type: "POST",
			url: "check_fdb.php?med_name="+encodeURI(medName),
			complete: function(r){
				response = r.responseText;
				if(response != null && typeof(response)!='undefined' && response!=''){
					$('#fdb_content').html(response);
				}
				parent.parent.show_loading_image('none');
			}
		});
	}
}
function fill_fdb_code(fdb,index){
	$('#fdb_id').val(fdb);
}
function close_uml_div(){
	$('#umls_content').html('');
	$('#fdb_content').html('');
	$("#div_disable").css("display","none"); 
	$("#div_umls").hide();
}
function export_import_csv(mode){
	if(mode == "export")
	window.location= top.JS_WEB_ROOT_PATH+"/interface/admin/console/Medication_type_ahead/export_import.php?mode="+mode;
	else if(mode == "show_import_frm"){
		$('#import_div').modal('show');
		}else if(mode == "import"){
		if(file_validation($("#csv_file"))){
			top.show_loading_image('show','250', 'Importing data...');
			$("#import_frm").submit();
		}
	}
}
function file_validation(obj){
	file = $(obj).val();
	file_ext = file.split(".").reverse();
	if(file == ""){
		top.fAlert("Please select file to upload.");
		return false;
	}
	if(file_ext[0] != "csv"){
		top.fAlert("Please select CSV file to import");
		return false;
	}
	return true;
}
function progressHandlingFunction(e){
	if(e.lengthComputable){
		$('progress').prop({value:e.loaded,max:e.total});
	}
}
function toggle_order_div(){
	if($('#ocular_1').prop('checked') == true){
		$('#divMedOrder').show();
		$('#divMedOrder1').show();
	}else{
		$('#divMedOrder').hide();
		$('#divMedOrder1').hide();
	}
}
function checkUPC(chkObj){
	if(chkObj.checked==true){
		$("#opt_med_name").prop('disabled',false);
		$("#opt_med_name").focus();
		$("#opt_med_name").val($("#medicine_name").val());
		
		med_upc_txt=$("#medicine_name").val();
		for(var i=0; i<custom_array_id.length; i++){
			var custom_array=custom_array_id[i].split('~');
			var custom_array_sub=custom_array[1].split(':-');
			med_id=custom_array[0];
			med_name=custom_array_sub[0];
			med_upc=custom_array_sub[1];
			if(med_upc_txt){
				if(custom_array_sub[0].toLowerCase()==med_upc_txt.toLowerCase()){
					$("#opt_med_name").val(med_name);
					$("#opt_med_id").val(med_id);
					$("#opt_med_upc").val(med_upc);
					break;
				}
				else{
					$("#opt_med_name").val('');
					$("#opt_med_id").val('');
					$("#opt_med_upc").val('');	
				}
			}
		}
	
	}
	else{
		 $("#opt_med_name").prop('disabled',true);
		 $("#opt_med_name").val('');
	}
}

function getTrioVal(){
	var med_upc_txt=$('#opt_med_name').val();
	for(var i=0; i<custom_array_id.length; i++){
		var custom_array=custom_array_id[i].split('~');
		var custom_array_sub=custom_array[1].split(':-');
		med_id=custom_array[0];
		med_name=custom_array_sub[0];
		med_upc=custom_array_sub[1];
		if(med_upc_txt){
			if(custom_array[1]==med_upc_txt){
				$("#opt_med_name").val(med_name);
				$("#opt_med_id").val(med_id);
				$("#opt_med_upc").val(med_upc);
				break;
			}
			else{
				$("#opt_med_name").val('');
				$("#opt_med_id").val('');
				$("#opt_med_upc").val('');	
			}
		}
	}
}
function num_paging(total_pages, cont_num){
    $('#page').val(cont_num);
	var cnt_start = 1;
	var cnt_end = total_pages;
	if(total_pages>25){
		cnt_end=25;
		if(cont_num>10){cnt_start=parseInt(cont_num)-10;cnt_end=parseInt(cont_num)+10;}
	}
	var alpha = $('#pg_aplhabet').val();
	var d_t=s_class=num_span="";
	for(var i=cnt_start;i<=cnt_end;i++){
		alpha = $("#pg_aplhabet").val();
		record_limit = $("#pg_aplhabet").val();
		s_class='';d_t=i;
		if(i==cont_num){d_t=""+i+"";s_class='active';}
		num_span +="<li class='pointer "+s_class+"'><a class='num_cnt' id=\"conr_"+i+"\" onclick='LoadResultSet(\"\",\"\",\"\",\"\",\"\",\""+alpha+"\","+i+",\"\",\"\",\""+i+"\")'>"+d_t+"</a><li>";
		if(total_pages<=i){
			break;
		}
	}
	$("#div_pages").html(num_span);
}
function srh_records(){
	searchStr = $("#search").val();
	LoadResultSet('','','','','','','','',searchStr);
}
var ar= [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"],
		["csv_export_medicine","Export CSV","window.top.fmain.export_import_csv('export');"],
		["csv_import_medicine","Import CSV","window.top.fmain.export_import_csv('show_import_frm');"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('Med.'); 
	
	$("#search").keypress(function (evt){ if(evt.keyCode==13){ srh_records(); } });
	$('#opt_med_name').typeahead({source:custom_array_name,scrollBar:true});
	// start creating alphabet HTML
	var first="A",last="Z";alphabet= '';
	var ch='';
	var alphaNum="";
	alphabet+="<li class=\"num\"><a id=\"0-9\" onClick='LoadResultSet(\"\",\"\",\"\",\"\",\"\",\"0-9\")' style='cursor:pointer;'>0-9</a></li>";
	for(var i = first.charCodeAt(0); i <= last.charCodeAt(0); i++){
		ch=eval("String.fromCharCode("+i+")");
		cl='';
		if(ch=='A'){cl='pointer active';}
		status = $("#status").val();
		s = $("#ord_by_field").val();
		so = $("#ord_by_ascdesc").val();
		alphabet+="<li class=\""+cl+"\"><a id=\""+ch+"\" onClick='LoadResultSet(\"\",\"\",\"\",\"\",\"\",\""+ch+"\")' style='cursor:pointer'>"+ch+"</a></li>";
	}
	$("#pagenation_alpha_order").html(alphabet);
	// end creating alphabet HTML
	
	$("#alertmsg").prop('disabled',true);
	$("#alert_1").change(function(){
		if($("#alert_1").is(":checked")){
			$("#alertmsg").removeAttr('disabled');
		}else{
			$("#alertmsg").prop('disabled',true);
		}
	});
});
top.show_loading_image('none');