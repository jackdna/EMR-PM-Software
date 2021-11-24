var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('physician_Reffer_id');
var all_referedPhysician = "";
var all_facility = "";
var status = $("#status").val();
var s = $("#ord_by_field").val();
var so = $("#ord_by_ascdesc").val();
var customSpeciality = new Array('Anesthesia','Cardiology','Cardiovascular surgery','Clinical laboratory sciences','Clinical Neurophysiology','Dermatology','Emergency medicine','Endocrinology','Family Medicine','Gastroenterology','General surgery','Geriatrics','Hematology','Hepatology','Infectious disease','Intensive care medicine','Maxillofacial surgery','Nephrology','Neurology','Neurosurgery','Obstetrics and gynecology','Oncology','Ophthalmology','Orthopedic surgery','Otolaryngology','Palliative care','Pathology','Pediatrics','Pediatric surgery','Physical medicine and rehabilitation','ENT','Plastic surgery','Proctology','Psychiatry','Pulmonology','Radiology','Rheumatology','Surgical oncology','Thoracic surgery','Transplant surgery','Trauma surgery','Urology','Vascular surgery');

//Direct Mail HTML Template
var tblheader = "<tr><th style='width:95%' colspan='2'><div class='row'><div class='col-sm-4'><span>Default Direct Email</span></div><div class='col-sm-8'>{DEFAULTDIRECT}</div></div></th><th style='width:3%'>Options</th></tr>";
var strTemplate = "<tr class='directMail_{ID}'><td style='width:2%'><div class='radio radio-inline'><input id='{ID}' type='radio' name='defaultMail' {DEFAULTVAL}/><label for='{ID}'></label></div></td> <td><input type='text' name='direct_mail[]' value='{EMAIL}' class='form-control direct_row_{ID}' onBlur='isEmail(this);'/><input type='hidden' name='direct_row_id[]' value='{ID}' class='form-control' /></td><td class='text-center'><span class='glyphicon glyphicon-remove pointer {DELROW}' data-id='{ID}' data-action='remove' onClick='removeTr(this);'></span><span class='glyphicon glyphicon-plus pointer {ADDROW}' data-id='{ID}' data-action='add' onClick='addTr();'></span></td></tr>";

function LoadResultSet(s,so,currLink,alpha,page,record_limit,searchStr,cont_num){
	//p=practice code, f=fac code, s=status, so=sort by;
	var cont_num = cont_num || 1;
	
	top.show_loading_image('none');
	if(typeof(s)!='string' || s==''){s = $('#status').val()}
	s_url = "&s="+s;
	$("#status").val(s);
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
	
	
	$('.adminnw .pointer span').removeAttr('class');
	if(soAD=='ASC')	$(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
	else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
	
	so_url='&so='+so+'&soAD='+soAD;
	if(typeof(alpha)=='undefined' || alpha==''){
		alpha = $('#pg_aplhabet').val();
	}else{
		$('#pg_aplhabet').val(alpha);
	}
	$('li').removeClass('active');
	$('#'+alpha).parent('li').addClass('active');
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
	//if(typeof(searchStr)!='undefined' && searchStr!=''){
		search_Url = "&searchStr="+$("#search_reff").val();
	//}
	pg_url = '&alpha='+alpha+'&page='+page+'&record_limit='+record_limit;		
	ajaxURL = "ajax.php?ajax_task=show_list"+s_url+so_url+pg_url+search_Url;
	$.ajax({
		url: ajaxURL,
		success: function(r){
			showRecords(r,cont_num);
		}
	});
}

var practiceInfo = "";
var practiceInfo_val_arr= "";
var ref_grp_arr = "";
var notice_days_arr = "";
var default_grp_arr = "";
var all_referedPhysician="";
var all_facility = "";	
var arr_texonomy = "";

function showRecords(r,cont_num){
	r = jQuery.parseJSON(r);
	result = r.records;
	ref_grp_arr = r.ref_grp_arr;
	notice_days_arr = r.notice_days_arr;
	default_grp_arr = r.default_grp_arr;
	all_referedPhysician = r.all_referedPhysician;
	all_facility = r.all_facility;
	arr_texonomy = r.arr_texonomy;
	practiceInfo = r.arr_practiceName;
	practiceInfo_val_arr= r.arr_practiceAddress;
	var total_pages = r.total_pages;
	h='';var no_record='yes';
	$("#search_reff").val(r.search_str);
	
	if(r != null){
		row = '';
		for(x in result){no_record='no';
			s = result[x];
			rowData = {};
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				rowData[y] = tdVal;
			}
			
			pkId = s.physician_Reffer_id;
			row += '<td class="text-center"><div class="checkbox"><input type="checkbox" name="id" id="id'+pkId+'" class="chk_sel" value="'+s.physician_Reffer_id+'"><label for="id'+pkId+'"></label></td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.name+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.address+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.NPI+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.MDCR+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.MDCD+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.Texonomy+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.start_date+'</td>';
			row += '<td onclick="addNew(1,\''+pkId+'\');">'+s.end_date+'</td>';
			row += '<td class="text-center">'+s.password_td+'</td>';
			row += '<td class="text-center">'+s.locked+'</td>';
			row += '<td class="text-center" >'+s.delete_status_td+'</td>';
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='12' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);
	fill_ref_grp_dropdown(ref_grp_arr);
	fill_notice_days_dropdown(notice_days_arr);
	fill_default_grp_dropdown(default_grp_arr);
	fill_all_referedPhysician();
	fill_all_facility();
	type_ahead_texonomy(arr_texonomy);
	type_ahead_practiceName(practiceInfo,practiceInfo_val_arr);	 
	num_paging(total_pages,cont_num)
	top.show_loading_image('hide');
}
function addNew(ed,pkId){
	
	$('#add_edit_frm input').removeClass('mandatory');
	$("#addNew_address").html('');
	if(typeof(ed)!='undefined' && ed!=''){$('#addNew_div h4#modal_title').text('Edit Referring Physician');}
	else
	{
		$('#addNew_div h4#modal_title').text('Add Referring Physician'); 
		$('#physician_Reffer_id').val(''); document.add_edit_frm.reset();
		$('#addNew_div select').selectpicker('refresh');
		var nz=0;
	}
	
	$('#addNew_div').modal('show');
	set_modal_height('addNew_div');

	$('#addNew_div').unbind('show.bs.modal');	
	$('#addNew_div').on('show.bs.modal', function(){
		setDirectMail();
	});

	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	$(":input").each(function (i) { $(this).attr('tabindex', i + 1); });
}
function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();
	$('#physician_Reffer_id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];
			
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if(o.type == "select-multiple"){
				  var tmp = [];
					for(var j in v){
						tmp.push(v[j].id);
					}
					$(o).selectpicker('val',tmp);
				}
				else if(o.type == "select-one"){
					$(o).selectpicker('val',v);
				}
				else if (o.type == "checkbox" || o.type == "radio"){
					for(j in v){
						oid = on.substr(0,(on.length-2))+'_'+v[j].replace(/\s/,'_');
						if(document.getElementById(oid) != "undefined" && document.getElementById(oid) != null && typeof(document.getElementById(oid)) != null){
							document.getElementById(oid).checked = true;
						}
					}
				} 
				else if(o.type!='submit' && o.type!='button'){
					if(v != "undefined" && v!=null)
					o.value = v;
				}
					
			}
		}
	}	
	
	$("#addNew_address").html('');
	if(arrAllShownRecords[pkId]['Addresses'] != "undefined"){
		for(j= 0;j<arrAllShownRecords[pkId]['Addresses'].length;j++){
			add_new_address();
			for(i=0;i<e.length;i++){
			o = e[i];
				if($.inArray(o.name,formObjects)){
					on	= o.name;
					v	= arrAllShownRecords[pkId]['Addresses'][j][on];
					if (o.tagName == "INPUT"){
						if(o.type!='submit' && o.type!='button'){
							if(v != "undefined" && v!=null)
							o.value = v;
						}
					}
				}
			}	
		}
	}	
	setDirectMail();
}

function saveFormData(){
	if(!validateForm(document.add_edit_frm))return false;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&ajax_task=save_update';
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
			$('#addNew_div').modal('hide');
			LoadResultSet();
		}
	});
}

function savePOData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_po').serialize()+'&ajax_task=save_update_po';
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			
			if(d.toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(d);
			}else{
				top.fAlert(d);
			}
			$('#addNew_PO').modal('hide');
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
		top.fancyConfirm("Are you sure you want to delete?","","top.fmain.deleteModifiers('"+pos_id+"')");
	}else{
		top.fAlert('No Record Selected.');
	}
}

function deleteModifiers(pos_id) {
	pos_id = pos_id.substr(0,pos_id.length-2);
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Deleting Record(s)...');
	frm_data = 'pkId='+pos_id+'&ajax_task=delete';
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

function fill_ref_grp_dropdown(ref_grp_arr){
	var options_val ='';
	for(index in ref_grp_arr){
		arr = ref_grp_arr[index];	
		options_val+="<option value='"+arr.ref_group_id+"'>"+arr.ref_group_name+"</option>";
	}
	$("#ref_phy_group").html(options_val).selectpicker('refresh');
}

function fill_notice_days_dropdown(notice_days_arr){
	var options_val = '';
	for(index in notice_days_arr){
		arr = notice_days_arr[index];	
		options_val+="<option value='"+arr.dayCount+"'>"+arr.label+"</option>";
	}
	$("#noticeDays").html(options_val).selectpicker('refresh');
}

function fill_default_grp_dropdown(default_grp_arr){
	var options_val = '';
	for(index in default_grp_arr){
		arr = default_grp_arr[index];	
		options_val+="<option value='"+arr.gro_id+"'>"+arr.name.replace(/\\/,'')+"</option>";
	}
	$("#default_group").html(options_val).selectpicker('refresh');;
}

function fill_all_referedPhysician(){
	var options_val = '';
	for(index in all_referedPhysician){
		arr = all_referedPhysician[index];
		options_val+="<option value='"+arr.id+"'>"+arr.name+"</option>";
	}
	$("#referedPhysician").html(options_val).selectpicker('refresh');
}

function fill_all_facility(){
	var options_val = '';
	for(index in all_facility){
		arr = all_facility[index];
		options_val+="<option value='"+arr.id+"'>"+arr.name+"</option>";
	}
	$("#default_facility").html(options_val).selectpicker('refresh');
}

function type_ahead_texonomy(arr_texonomy){
	$("#TexonomyId").typeahead({'source':arr_texonomy});
}

function type_ahead_practiceName(practiceInfo,practiceInfo_val_arr,i){
	if(practiceInfo){
		i = i || '';
        $("#PractiseName"+i).typeahead('destroy');
		$("#PractiseName"+i).typeahead({'source':practiceInfo,onSelect:function(item){fill_practice_address(i,item.value);}});
	}
}

function num_paging(total_pages, cont_num){
	var cnt_start = 1;
	var cnt_end = total_pages;
	cont_num = parseInt(cont_num);
	if(total_pages>20){
		cnt_end=20;
		if(cont_num>10){cnt_start=cont_num-10;cnt_end=cont_num+10;}
	}
	var alpha = $('#pg_aplhabet').val();
	var d_t=s_class=num_span="";
	var record_limit='';
	
	num_span	+=	'<nav aria-label="...">';
	num_span	+=	'<ul class="pagination">';
	
	if( cont_num !== 1 && cnt_end > 1)
	{
		var pc = 'onclick="$(\'#conr_'+(cont_num-1)+'\').trigger(\'click\');"';
		num_span	+=	'<li class="previous"><a href="#" id="prev" '+pc+'>Previous</a></li>';
	}
	
	for(var i=cnt_start;i<=cnt_end;i++){
		alpha = $("#pg_aplhabet").val();
		record_limit = $("#pg_aplhabet").val();
		var s_class = (i==cont_num) ? 'active' : '';
		var id = 'id="conr_'+i+'"';
		var oc = 'onclick="LoadResultSet(\'\',\'\',\'\',\''+alpha+'\','+i+',\'\',\'\',\''+i+'\')"';
		var cr = (s_class == 'active') ? '<span class="sr-only">(current)</span>' : '';
		num_span	+=	'<li class="'+s_class+'" ><a href="#" '+id+' '+oc+'>'+i+' '+cr+'</a></li>';
		if(total_pages<=i){
			break;
		}
	}
	if( cnt_end > 1 && cont_num < cnt_end ){
		var nc = 'onclick="$(\'#conr_'+(cont_num+1)+'\').trigger(\'click\');"';
		num_span	+=	'<li class="next" ><a href="#" id="next" '+nc+'>Next</a></li>';
	}
	num_span	+=	'</ul>';
	num_span	+=	'</nav>';
	$("#div_pages").html(num_span);
    if(alpha=='az') {
        $('.pgn_az li').addClass('active');
        $("#div_pages").html('');
        $(".recodpag").hide();
    } else {
        $('.pgn_az li').removeClass('active');
        $(".recodpag").show();
    }
}

function validate(field, index){
	this.ele_name = field.ele_name;
	this.ele_type = field.ele_type;
	this.ele_value = field.ele_value;
	this.minLength = (field.minLength!="undefined")?field.minLength:"";
	obj = document.getElementById(this.ele_name);
	switch(this.ele_type){
		case "char":
			return true;
			patt = /[a-zA-Z]/;
			return (this.ele_value == "" || !(patt.test(this.ele_value))) ? false:true;
		break;
		case "number":
			patt = /[0-9]/;
			return (this.ele_value == "" || !(patt.test(this.ele_value)) || (this.minLength!="" && this.ele_value.length < this.minLength))?false:true;
		break;
		case "alphanum":
			patt = /[0-9a-zA-Z\-]/;
			if(this.ele_value == ""){
				return false;
			}
			else if(!(patt.test(this.ele_value))){
				return false;
			}
			else if(this.minLength!="" && this.ele_value.length < this.minLength){
				fields[index]['msg'] = "Please Enter NPI#  as exactly  10 characters.";
				return false;
			}
		return true;
		case "password":
			objUN = document.getElementById(field.username);
			obConfirm = document.getElementById(field.confirm_password);
			objHidPass = document.getElementById('hid_password');
			var userFname = document.getElementById('FirstName').value;
			var userLname = document.getElementById('LastName').value;
			if(objUN.value != "" && this.ele_value == "" && objHidPass.value == ""){
				return false;
			}else if(this.ele_value != "" && objUN.value == ""){
				fields[index]['msg'] = "Please Enter Username.";
				return false;
			}else if(this.ele_value!="" && objUN.value!=""){
				if(this.ele_value.length < this.minLength){
					fields[index]['msg'] = "Must be at least 8 characters long.";
					return false;
				}
				if(!this.ele_value.match(/[0-9]/g) || !this.ele_value.match(/[a-zA-Z]/g)){
					fields[index]['msg'] = "Must contain alphanumeric characters";
					return false;
				}
				if( this.ele_value == objUN.value || this.ele_value == userFname || this.ele_value == userLname){
					fields[index]['msg'] = "Password can not have user First Name or Last Name or user login id.";
					return false;
				}
				if(this.ele_value !=""  && this.ele_value != obConfirm.value){
					fields[index]['msg'] = "Confirm password should match password.";
					return false;
				}
			} 
			return true;
		break;
	}
}

var fields = {};
fields[0] = {ele_name:"FirstName",ele_type:'char',displayName:"First Name"};
fields[1] = {ele_name:"LastName",ele_type:'char',displayName:"Last Name"};
fields[2] = {ele_name:"ZipCode",ele_type:'number',displayName:top.zipLabel};
fields[3] = {ele_name:"NPI",ele_type:'alphanum',minLength:10,displayName:"NPI"};
fields[4] = {ele_name:"password",ele_type:'password',username:"userName",confirm_password:'confirm_password',minLength:8,displayName:"Password"};

function validateAddress(f){
	//return false;
	valid_flag = 1;
	l = $('input[name^=Address1]').length;
	for(i = 0;i<l;i++){
		if($('input[name^=Address1]').get(i).value!="" || 
			$('input[name^=Address2]').get(i).value!="" ||
			$('input[name^=ZipCode]').get(i).value!="" ||
			$('input[name^=zip_ext]').get(i).value!="" ||
			$('input[name^=City]').get(i).value!="" ||
			$('input[name^=State]').get(i).value!=""
			){
			if($('input[name^=ZipCode]').get(i).value == ""){
				fAlert("Enter "+top.zipLabel);
				changeClass($('input[name^=ZipCode]').get(i),1)
				return false;
			}
		}
		
	}
	if(valid_flag == 0)return false;
	else return true;
}

function validateForm(f){
	if(!validateAddress(f))return false;
	fldArr = {};
	validFlag = true;
	
	for(var i in fields){
		fld_name = fields[i]['ele_name'];
		obj = document.getElementById(fld_name);
		fields[i]['ele_value'] = obj.value;
		if(!validate(fields[i],i)){
			fldArr[i] = fields[i];//
			changeClass(obj,1);
		}else{
			changeClass(obj,0);
		}
	}
	msg = "Enter following fields correctly:- <br>";
	for(var i in fldArr){
		validFlag = false;
		msg += " &bull; "+fldArr[i]['displayName']+" ";
		if(typeof(fldArr[i]['msg']) != "undefined"){
			msg += " ; "+fldArr[i]['msg'];
		}
		msg +="<br>";
	}
	if(!validFlag){
		fAlert(msg);
		return false;
	}
	return true;
}

function changeClass(obj, invalid){
	if(invalid)
	$(obj).addClass('mandatory');
	else
	$(obj).removeClass('mandatory');
}

function getFocusObj(obj){
		var objId = obj.id;
		if(document.getElementById(objId)){
			var str = document.getElementById(objId).value;
			setCaretPosition(objId, str.length);
		}
}

function setCaretPosition(elemId, caretPos) {
	var elem = document.getElementById(elemId);
	if(elem != null) {
		if(elem.createTextRange) {
			var range = elem.createTextRange();
			range.move('character', caretPos);
			range.select();
		}
		else {
			if(elem.selectionStart) {
				elem.focus();
				elem.setSelectionRange(0, caretPos);
			}
			else
				elem.focus();
		}
	}
}
function getFedralEin(){
	var facility = document.getElementById("default_facility").value;
	var url = "getPosFacility.php?facility="+facility;
	
	$.ajax({
		url : url,
		type:'POST',
		success:function(r){
			if(document.getElementById("TaxId")){
				document.getElementById("TaxId").value = r;
			}	
		}
	});
}

function create_xml(){
	top.show_loading_image('show','300', 'Creating referring physician cache');
	$.ajax({
		type: "POST",
		url: "ajax.php?ajax_task=createAllXML",
		success: function(d) {
			top.show_loading_image('hide');
			top.alert_notification_show(d);
		}
	});
}	
function export_csv(){
	window.location = "../admin/ReferringPhysician/ajax.php?ajax_task=export_csv",'Export Referring Physicians';
}

function lock_unlock(locked,physician_Reffer_id){
	top.show_loading_image('show');
	$.ajax({
		type: "POST",
		url: "ajax.php?ajax_task=lock_unlock&physician_Reffer_id="+physician_Reffer_id+"&locked="+locked,
		success: function(d) {
			LoadResultSet();
			top.show_loading_image('hide');
		}
	});
}

function set_status(status,physician_Reffer_id){
	top.show_loading_image('show');
	$.ajax({
		type: "POST",
		url: "ajax.php?ajax_task=set_status&physician_Reffer_id="+physician_Reffer_id+"&status="+status,
		success: function(d) {
			LoadResultSet();
			top.show_loading_image('hide');
		}
	});
}

function srh_referring_phy(){
	searchStr = $("#search_reff").val();
	LoadResultSet('','','','','','',searchStr);
}

function popup_dbl(divid,sourceid,destinationid,act,odiv){
	if(act=="single" || act=="all"){
		if(act=='single')	{
			$("#"+sourceid+" option:selected").appendTo("#"+destinationid);
		}else if(act=="all"){$("#"+sourceid+" option").appendTo("#"+destinationid);}
	}else if(act=="single_remove" || act=="all_remove"){
		if(act=="single_remove"){$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);}
		if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
		$("#"+destinationid).append($("#"+destinationid+" option").remove().sort(function(a, b) {
			var at = $(a).text(), bt = $(b).text();
			return (at > bt)?1:((at < bt)?-1:0);
		}));
		$("#"+destinationid).val('');
	}else{
		$("#"+destinationid+" option").remove();
		$("#"+odiv+" option").clone().appendTo("#"+destinationid);
		$("#"+divid).show("clip");
	}
}

function selected_ele_close(divid,sourceid,destinationid,div_cover,action){
	if(action=="done"){
		var sel_cnt=$("#"+sourceid+" option").length;
		$("#"+divid).hide("clip");
		$("#"+destinationid+" option").each(function(){$(this).remove();})
		$("#"+sourceid+" option").appendTo("#"+destinationid);
		$("#"+destinationid+" option").attr({"selected":"selected"});
		$("#"+div_cover).width(parseInt($("#"+destinationid).width())+'px');
		if(sel_cnt>8){
			$("#"+div_cover).width(parseInt($("#"+destinationid).width()-15)+"px");	
		}
	}else if(action=="close"){
		$("#"+divid).hide("clip");
	}
}

function set_default_address(obj){
	$("#addNew_address input[type=checkbox]").each(function(index, element) {
		if(obj != this)
        $(this).prop("checked",false);
    });
}


function del_address(index){
	ids = $("#address_del_id").val();
	jId = "#id_address["+index+"]";
	id = document.getElementById("id_address["+index+"]").value;
	$('#address_del_id').val(ids+","+id)
}
function add_new_address(){
	i = $('input[name^=Address1').length;
	
	var html = '';
	html += '<div class="section1" id="div_address'+i+'">';
	html += '<div class="head">';
	html += '<figure class="pull-left text-left"><div class="checkbox" style="margin:-2px 0 0 3px;"><input type="checkbox" id="default_address['+i+']" name="default_address['+i+']" value="1" onclick="set_default_address(this)"><label for="default_address['+i+']"> </label></div></figure>';
	html += '<span>Address & Contacts</span>';
	html += '<figure class="pull-right text-right"><img src="../../../library/images/closerd.png" alt="Delete" onclick="del_address('+i+');$(\'#div_address'+i+'\').remove();" class="pointer" width="20px" height="auto"></figure>';
	html += '</div>';
	html += '<div class="clearfix"></div>';
	html += '<div class="tblBg">';
	html += '<div class="row">';
	html += '<input type="hidden" id="id_address['+i+']" name="id_address['+i+']">';
	
	//-- Practice Name
	html += '<div class="col-sm-4">';
	html += '<div class="form-group"><label for="PractiseName'+i+'">Practice Name</label>';
	html += '<input type="text" class="form-control" name="PractiseName['+i+']" id="PractiseName'+i+'" value="" onChange="fill_practice_address();">';
	html += '</div>';
	html += '</div>';
	
	//-- Specialty
	html += '<div class="col-sm-4">';
	html += '<div class="form-group"><label for="specialty'+i+'">Specialty</label>';
	html += '<input name="specialty['+i+']" id="specialty'+i+'" type="text" class="form-control" value="">';
	html += '</div>';
	html += '</div>';
	
	//-- Street 1
	html += '<div class="col-sm-4">';
	html += '<div class="form-group"><label>Street 1</label>';
	html += '<input type="text" class="form-control" tabindex="9" name="Address1['+i+']" value="" >';
	html += '</div>';
	html += '</div>';
	
	//-- Street 2
	html += '<div class="col-sm-4">';
	html += '<div class="form-group"><label>Street 2</label>';
	html += '<input type="text" class="form-control" tabindex="10" name="Address2['+i+']" value="" >';
	html += '</div>';
	html += '</div>';
	
	//-- Zip
	
  html += '<div class="col-sm-4">';
	html += '<div class="form-group"><label for="ZipCode'+i+'">'+top.zipLabel+'</label>';
	html += '<div class="clearfix"></div>';
	html += '<div class="form-inline zipcod">';
	html += '<input maxlength="'+top.zip_length+'" type="text" class="form-control" name="ZipCode['+i+']" id="ZipCode'+i+'" size="'+top.zip_length+'" tabindex="11"  onBlur="zip_vs_state_R6(this,document.getElementsByName(\'City['+i+']\'),document.getElementsByName(\'State['+i+']\'));" value=""> ';
	if( top.zip_ext ){
		html += '<input type="text" maxlength="4" class="form-control" name="zip_ext['+i+']" id="zip_ext'+i+'" tabindex="11" value="" >';
	}
	html += '</div>';
	html += '</div>';
	html += '</div>';
	
	//City / Country
	html += '<div class="col-sm-3">';
	html += '	<div class="row">';
	html += '		<div class="col-sm-6">';
	html += '			<div class="form-group"><label for="rcity'+i+'">City</label>';
	html += '				<input type="text" class="form-control" name="City['+i+']" tabindex="12" size="12" id="rcity'+i+'" value="" >';
	html += '			</div>';
	html += '		</div>';
	html += '		<div class="col-sm-6">';
	html += '			<div class="form-group"><label for="rcountry'+i+'">Country</label>';
	html += '				<input type="text" class="form-control" name="country['+i+']" tabindex="12" size="12" id="rcountry'+i+'" value="" >';
	html += '			</div>';
	html += '		</div>';
	html += '	</div>';
	html += '</div>';
	
	//-- State
	html += '<div class="col-sm-1">';
	html += '<div class="form-group"><label for="rstate'+i+'">'+top.state_label+'</label>';
	html += '<input type="text" class="form-control" name="State['+i+']" maxlength="'+(top.state_val == 'abb' ? 2 : '')+'" tabindex="13" size="5" id="rstate'+i+'" value="" >';
	html += '</div>';
	html += '</div>';
	
	//-- Phone
	html += '<div class="col-sm-4">';
	html += '<div class="form-group"><label for="">Phone</label>';
	html += '<input type="text" class="form-control" onChange="set_phone_format(this,\''+top.phone_format+'\',\''+top.phone_min_length+'\',\'phone\',\'form-control mandatory\');" tabindex="14" name="physician_phone['+i+']" value="" >';
	html += '</div>';
	html += '</div>';
	
	//-- Fax
	html += '<div class="col-sm-4">';
	html += '<div class="form-group"><label for="">Fax</label>';
	html += '<input type="text" class="form-control" onChange="set_phone_format(this,\''+top.phone_format+'\',\''+top.phone_min_length+'\',\'fax\',\'form-control mandatory\');" tabindex="15" name="physician_fax['+i+']" value="" >';
	html += '</div>';
	html += '</div>';
	
	//-- Email
	html += '<div class="col-sm-4">';
	html += '<div class="form-group"><label for="">Email</label>';
	html += '<input type="text" class="form-control" tabindex="16" name="physician_email['+i+']" value="" >';
	html += '</div>';
	html += '</div>';
	
	html += '</div>';
	html += '<div class="clearfix"></div>';
	html += '</div>';
	
	$('#addNew_address').append(html);
	$(":input").each(function (i) { $(this).attr('tabindex', i + 1); });
	type_ahead_practiceName(practiceInfo,practiceInfo_val_arr,i);
	if(customSpeciality){
		$("#specialty"+i).typeahead({'source':customSpeciality});
	}
}

function show_chg_password_div(pkId){
	$('#div_chg_password').modal('show');
	$('#chg_password').val('');
	$('#chg_confirm_password').val('');
	$('#pkId').val(pkId);
}

function validatePassword(f){
	password = document.getElementById('chg_password');
	obConfirm = document.getElementById('chg_confirm_password');
	msg = new Array;
		if(password.value == "")
			msg[msg.length] = " - Password can not be empty";
		if(password.value.length < 8){
			msg[msg.length] = " - Must be at least 8 characters long";
		}
		if(!password.value.match(/[0-9]/g) || !password.value.match(/[a-zA-Z]/g)){
			msg[msg.length] = " - Must contain alphanumeric characters";
		}
		if(password.value !=""  && password.value != obConfirm.value){
			msg[msg.length] = " - Confirm password should match password";
		}
	if(msg.length > 0){
		fAlert("Password should match folloiwng: <br>"+msg.join("<br>"));
		return false;
	}
	return true;
}

function change_password(f){
	if(!validatePassword(f))
	return false;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $(f).serialize()+'&ajax_task=chg_password';
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d.toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(d);
				$('#div_chg_password').modal('hide');
				LoadResultSet();
			}else{
				top.fAlert(d);
				$('#chg_password').val('');
				$('#chg_confirm_password').val('');
			}
		}
	});
}

function fill_practice_address(i,v)
{ 
	if( typeof v === 'undefined') return;
	var key = '';
	$.each(practiceInfo,function(index,value){
		if( value == v) { key = index; return; }
	});
	i = i || '0';
	t = i == 0 ? '' : i;
	var str_val= practiceInfo_val_arr[key];
	var arr_str_val;
	if(str_val!='' && str_val.length>4){
		arr_str_val=str_val.split("~|~");
		$("input[name='Address1["+i+"]']").val(arr_str_val[1]);
		$("input[name='Address2["+i+"]']").val(arr_str_val[2]);
		$("input[name='ZipCode["+i+"]']").val(arr_str_val[3]);
		$("input[name='zip_ext["+i+"]']").val(arr_str_val[4]);
		$("input[name='City["+i+"]']").val(arr_str_val[5]);
		$("input[name='State["+i+"]']").val(arr_str_val[6]);
	}
}

//Get Direct mail for the retrived ref phy.
function getMultiDirectmail(refId){
	if(!refId || typeof(refId) == 'undefined' || refId == '') return false;
	
	$.ajax({
		url: "ajax.php",
		type: "GET",
		dataType:'JSON',
		data: {'refId' : refId, 'ajax_task' : 'get_multi_direct'},
		beforeSend:function(){
			top.show_loading_image('show');
		},
		success: function(response) {
			if(response){
				createDirectHTML(response, refId);
			}
			return false;
		},
		complete:function(){
			top.show_loading_image('hide');
		}
	});
}

//Adds a new Direct mail row
function addTr(obj){
	if(!obj){
		var nextId = $('#lastDirectCalled').val();
		if(nextId) nextId = parseInt(nextId)+1;
		
		var tempStr = strTemplate;
		var rendNumber = Math.floor((Math.random() * 100) + 1);

		tempStr = tempStr.replace(/{ADDROW}/g, '');
		tempStr = tempStr.replace(/{DELROW}/g, 'hide');

		tempStr = tempStr.replace(/{ID}/g, 'text_new_'+rendNumber);
		tempStr = tempStr.replace(/{EMAIL}/g, '');

		tempStr = tempStr.replace(/{DEFAULTVAL}/g, '');

		var lastObj = $('#directMultiple').find('.modal-body #directMailTbl').find('tr:last');
		if(lastObj.length){
			var lastTd = lastObj.find('td:last');
			//lastTd.empty();
			if(lastTd.find('.glyphicon-plus').length) lastTd.find('.glyphicon-plus').remove();

			if(lastTd.find('.glyphicon-remove').length == 0){
				lastTd.html("<span class='glyphicon glyphicon-remove pointer' data-id='' data-action='remove' onClick='removeTr(this);'>");
			}else{
				if(lastTd.find('.glyphicon-remove').hasClass('hide')) lastTd.find('.glyphicon-remove').removeClass('hide');
			}
		}

		$('#directMultiple').find('.modal-body #directMailTbl').append(tempStr);

		$('#lastDirectCalled').val(nextId);

	}
}

//Removes or delete rows from multi direct mail modal
function removeTr(obj){
	var dataId = $(obj).data('id');
	if(dataId == '' || $.isNumeric(dataId) == false){
		var parentObj = $(obj).closest('tr');
		if(parentObj.length){
			var nextId = $('#lastDirectCalled').val();
			if(nextId && nextId > 1){
				nextId = parseInt(nextId)-1;
				parentObj.remove();
			}	
		}
	}else{
		top.fancyConfirm("Are you sure you want to remove ?","","top.fmain.deleteDirectmail("+dataId+")");
	}
}

//Validate Email
function isEmail(obj) {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var status = regex.test($(obj).val());
	
	if(status == false) $(obj).val('');
}

//Save all the records
function multipleDirect(btnObj){
	var formData = $('#direct_multiple_add').serialize();
	$.ajax({
		url: "ajax.php",
		type: "POST",
		dataType:'JSON',
		data: 'ajax_task=save_multi_direct&'+formData,
		beforeSend:function(){
			top.show_loading_image('show');
		},
		success: function(response) {
			if(response == true){
                //Check if default direct is empty keep default email empty
                var defaultEmailEmpty = false;
                
                var defaultInput = $('#directMultiple input[name=defaultdirect]').val();
                if(defaultInput == '') defaultEmailEmpty = true;
                
				fAlert('Record saved successfully');
				$('#directMultiple').modal('hide');
                
				setDirectMail(defaultEmailEmpty);
				return false;
			}

			fAlert('Unable to process the request');
			return false;
			
		},
		complete:function(){
			top.show_loading_image('hide');
		}
	});
	
}

//Deletes Direct mail record
function deleteDirectmail(id){
	if(id == '' || typeof(id) == 'undefined'){
		fAlert('Unable to process request !');
		return false;
	}

	$.ajax({
		url: "ajax.php",
		type: "POST",
		dataType:'JSON',
		data: {'rowId' : id, 'ajax_task' : 'del_multi_direct_mail'},
		success: function(response) {
			if(response == true){
				$('#directMultiple').find('.modal-body #directMailTbl').find('tr.directMail_'+id).remove();
				return false;
			}

			fAlert('Unable to process the result !');
			return false;
		}
	});
}

//Resets the field
function resetField(obj){
	var resetVal = $(obj).data('value');
	if(resetVal){
		$(obj).parent().closest('.input-group').find('input[name=defaultdirect]').val(resetVal);
		$('#directMailTbl input[type=radio]').prop('checked', false);
	}
}

//Create and show Direct Mail modal html
function createDirectHTML(arrData, refId){
	var htmlStr = [];
	var strHtml = '';

	//Getting Default Email
	var defaultEmail = $('#add_edit_frm #direct_email').val();
	var tempheader = tblheader;
	tempheader = tempheader.replace(/{DEFAULTDIRECT}/g, '<div class="input-group"><input type="text" class="form-control" name="defaultdirect" value="'+defaultEmail+'" onBlur="isEmail(this);"><div class="input-group-addon" onClick="resetField(this);" data-value="'+defaultEmail+'"><span class="glyphicon glyphicon-refresh"></span></div></div> ');
	
	if(Object.keys(arrData).length > 0){
		var counter = 1;
		$.each(arrData, function(id, val){
			var ID = val['id'];	
			var email = val['email'];		
			var defaultVal = val['default'];
			if(defaultVal == 0) defaultVal = '';
			
			if(ID && email){
				var tempStr = strTemplate;
				
				if(counter !== Object.keys(arrData).length) tempStr = tempStr.replace(/{ADDROW}/g, 'hide');	
				else tempStr = tempStr.replace(/{ADDROW}/g, '');

				tempStr = tempStr.replace(/{DELROW}/g, '');

				tempStr = tempStr.replace(/{ID}/g, ID);
				tempStr = tempStr.replace(/{EMAIL}/g, email);

				if(defaultVal == 1) tempStr = tempStr.replace(/{DEFAULTVAL}/g, 'checked');
				else tempStr = tempStr.replace(/{DEFAULTVAL}/g, '');
				htmlStr.push(tempStr);
			}
			counter++;
		});
	}

	if(htmlStr.length){
		strHtml = htmlStr.join(' ');
		if(strHtml !== ''){
			var finalHtml = '<input type="hidden" value='+Object.keys(arrData).length+' id="lastDirectCalled" ><input type="hidden" id="directRefId" value='+refId+' name="directRefId" /><table id="directMailTbl" class="table tble-condensed table-bordered"><thead>'+tempheader+'</thead><tbody>'+strHtml+'</tbody></table>';
			$('#directMultiple').find('.modal-body').empty();
			$('#directMultiple').find('.modal-body').html(finalHtml);
			addTr();
		}
	}else{
		var finalHtml = '<input type="hidden" value=1 id="lastDirectCalled" ><input type="hidden" id="directRefId" value='+refId+' name="directRefId" /><table id="directMailTbl" class="table tble-condensed table-bordered"><thead>'+tempheader+'</thead><tbody>'+strHtml+'</tbody></table>';
			$('#directMultiple').find('.modal-body').empty();
			$('#directMultiple').find('.modal-body').html(finalHtml);
			addTr();
	}
}

//Check Phy current direct mail
function setDirectMail(defaultEmpty){
	var directMail = $('#addNew_div.modal').find('#direct_email').val();
    if(defaultEmpty == true){
        directMail = '';
        $('#addNew_div.modal').find('#direct_email').val('');
    }
	var refId = $('#addNew_div.modal').find('#physician_Reffer_id').val();
	
	$.ajax({
		url:'ajax.php',
		type:'GET',
		data:{'refId' : refId, 'directMail' : '', 'ajax_task' : 'check_direct_mail'},
		dataType : 'JSON',
		success:function(response){
			$('#addNew_div').find('#direct_email').val('');
			if(Object.keys(response).length){
				var totalCount = response.totalCount;
				var drctMail = response.mailText;

				if(totalCount == '') totalCount = 0;

				if(!refId){
					$('[data-target="#directMultiple"]').removeClass('text_purple').css('pointer-events','none');
					$('[data-target="#directMultiple"]').siblings('input').prop('readonly', false);
				}else{
					if($('[data-target="#directMultiple"]').hasClass('text_purple') == false) $('[data-target="#directMultiple"]').addClass('text_purple'); 
					$('[data-target="#directMultiple"]').css('pointer-events','');
					$('[data-target="#directMultiple"]').siblings('input').prop('readonly', true);
				}

				if(totalCount || totalCount == 0) $('#addNew_div').find('[data-target="#directMultiple"] strong').text('Direct Message ('+totalCount+')');
				if(drctMail) $('#addNew_div').find('#direct_email').val(drctMail);
			}
		}
	});
}

function showPO()
{	
	var po_ref_id=$("#physician_Reffer_id").val();
	if(po_ref_id)
	{
		$('#addNew_PO').modal('show');
		$("#physician_Reffer_id_for_po").val(po_ref_id);
		ajaxURL = "ajax.php?ajax_task=show_po_proc&ref_id="+po_ref_id;
		$.ajax({
			url: ajaxURL,
			success: function(r){
				$("#po_content_body").html(r);
			}
		});
	}
	
	//set_modal_height('addNew_PO');
}

$(document).on('click','.child', function(){

	$(this).closest('tr').find('.parent input:checkbox').prop('checked', true);
    //your code here

 });


$(function(){
	$('body').on('show.bs.modal','#addNew_div',function(){
		var btn_array = [['Save','Save','']];
		top.fmain.set_modal_btns('addNew_div .modal-footer:first',btn_array);
	});
	
	$('body').on('show.bs.modal','#div_chg_password',function(){
		var btn_array = [['Save','Save','']];
		top.fmain.set_modal_btns('div_chg_password .modal-footer:first',btn_array);
	});

	$('body').on('click', '#directMultiple input[name=defaultMail]', function(){
		var obj = $(this);
		var prntObj = obj.closest('tr');
		
		if(prntObj.length){
			var inputObj = prntObj.find('input.direct_row_'+obj.attr('id'));
			if(inputObj.length){
				var value = inputObj.val();
				if(value) $('#directMultiple input[name=defaultdirect]').val(value);
			}
		}
	});

	// Remove "," comma from Name Fields 
	$('body').on('chnage blur','#LastName,#FirstName,#MiddleName',function(){
		var v = $(this).val();
		v = v.trim();
		if( v ) {
			if( v.indexOf(",") !== -1 ) {
				v = v.replace(/[, ]+/g, " ", ' ').trim();
				$(this).val(v);
			}
		} 
	});
});

$(document).ready(function(){
	LoadResultSet();
	set_header_title('Referring Physicians');
	check_checkboxes(); //Checkbox selection
	$(".datepicker").datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true, scrollInput:false});
	$("#search_reff").keypress(function (evt){
		if(evt.keyCode==13){	srh_referring_phy(); }
	});
	
	if(customSpeciality){
		$("#specialty").typeahead({'source':customSpeciality});
	}

	$('body').on('show.bs.modal', '#directMultiple',function(){
		getMultiDirectmail($('#physician_Reffer_id').val());
	});
});

var ar = [["add_new","Add New","top.fmain.addNew();"],
		["dx_cat_del","Delete","top.fmain.deleteSelectet();"],
		["refer_phys_xml","Create XML for All","top.fmain.create_xml();"],
		["refer_phys_csv","Export CSV","top.fmain.export_csv();"]];
top.btn_show("ADMN",ar);