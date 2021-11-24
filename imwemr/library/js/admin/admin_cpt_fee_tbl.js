var arrAllShownRecords = facilities = new Array();
	var totalRecords	   = 0;
	var formObjects		   = new Array('cpt_fee_id','cpt4_code');
	var multi_select_catagory_loaded=0;
	function LoadResultSet(p,f,s,so,currLink,filter){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading CPT codes...');
	cpt_array = new Array();
	$('#cpt_categories option:selected').each(function() {
		cpt_array.push($(this).val());
	});
	f = cpt_array;
	if(typeof(s)!='string' || s==''){s = 'Active';}
	s_url = "&s="+s;
	if($("#search").val()){p=$("#search").val();}
	if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
	if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
	if(typeof(filter)=='undefined'){filter='';};

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
	var select_id = $(currLink).find('select').attr('id');
	if(!select_id){
		if(soAD=='ASC') $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
		else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');

	}
	so_url='&so='+so+'&soAD='+soAD;

	ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url+'&status='+filter;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {
		showRecords(r);
	  }
	});
}
var dx_code_array=new Array();
var mod_code_array=new Array();
function showRecords(r){
	r = JSON.parse(r);
	var no_record="yes";
	result 	= r.records;
	cpt_cat = r.cpt_cat;
	rev_code=r.rev_code;
	tos_code=r.tos_code;
	dept_code=r.dept_code;
	poe_code=r.poe_code;
	dx_code=r.dx_code;
	mod_code=r.mod_code;
	var rev_code_array=new Array();
	var rev_code_type_array=new Array();
	var rev_code_array_key=new Array();
	for(m in rev_code){
		rr = rev_code[m];
		rev_code_array[rr.r_id]=rr.r_code;
		rev_code_type_array.push(rr.r_code);
		rev_code_array_key[rr.r_code]=rr.r_id;
	}
	var tos_code_array=new Array();
	var tos_code_array_key=new Array();
	var tos_code_type_array=new Array();
	for(t in tos_code){
		tt = tos_code[t];
		tos_code_array[tt.tos_id]=tt.tos_prac_cod;
		tos_code_type_array.push(tt.tos_prac_cod);
		tos_code_array_key[tt.tos_prac_cod]=tt.tos_id;
	}
	var dept_code_array=new Array();
	var dept_code_array_key=new Array();
	var dept_code_type_array=new Array();
	for(d in dept_code){
		dd = dept_code[d];
		dept_code_array[dd.DepartmentId]=dd.DepartmentCode;
		dept_code_type_array.push(dd.DepartmentCode);
		dept_code_array_key[dd.DepartmentCode]=dd.DepartmentId;
	}
	var poe_code_array=new Array();
	var poe_code_array_key=new Array();
	var poe_code_type_array=new Array();
	for(p in poe_code){
		pp = poe_code[p];
		poe_code_array[pp.poe_messages_id]=pp.poe_name;
		poe_code_type_array.push(pp.poe_name);
		poe_code_array_key[pp.poe_name]=pp.poe_messages_id;
	}
	var dx_code_array_key=new Array();
	for(d1 in dx_code){
		dd1 = dx_code[d1];
		dx_code_array[dd1.diagnosis_id]=dd1.d_prac_code;
		dx_code_array_key[dd1.d_prac_code]=dd1.diagnosis_id;
		if(dd1.icd10_desc && dd1.icd10_desc!=""){ dx_code_array["10"+dd1.diagnosis_id]=dd1.icd10_desc; }
		if(dd1.icd9 && dd1.icd9!=""){ dx_code_array["20"+dd1.diagnosis_id]=dd1.icd9;	}
	}
	var mod_code_array_key=new Array();
	for(m1 in mod_code){
		mm1 = mod_code[m1];
		mod_code_array[mm1.modifiers_id]=mm1.mod_prac_code;
		mod_code_array_key[mm1.mod_description]=mm1.mod_prac_code;			
		if(mm1.mod_description && mm1.mod_description!=""){ mod_code_array["50"+mm1.modifiers_id]=mm1.mod_description; }
	}
	h='';
	if(r != null){
		row = '';
		row_class = '';
		for(x in result){
			no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='cpt_fee_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:14px;"><div class="checkbox"><input type="checkbox" name="chk_sel" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(tdVal==null){tdVal="";}
				if(y=='cpt_category'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
                if(y=='cpt_category2'){
					var cpt_cat2 = '';
					if(tdVal == 1){
						cpt_cat2 = 'Service';
					}
					if(tdVal == 2){
						cpt_cat2 = 'Material';
					}
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+cpt_cat2+'</td>';
				}
				if(y=='cpt4_code'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='not_covered'){
					var ins_billed = 'Yes';
					if(tdVal == 1){
						ins_billed = 'No';
					}
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+ins_billed+'</td>';
				}
				if(y=='cpt_prac_code'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='cpt_desc'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='cpt_comments'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='cvx_code'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='rev_code'){
					if(typeof rev_code_array[tdVal] != "undefined"){
						tdVal=rev_code_array[tdVal];
					}else{tdVal="";}
					rowData['rev_code_v']=tdVal;
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='tos_id'){
					if(typeof tos_code_array[tdVal] != "undefined"){
						tdVal=tos_code_array[tdVal];
					}else{tdVal="";}
					rowData['tos_id_v']=tdVal;
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='units'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='mod1'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='mod2'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='mod3'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='mod4'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='departmentId'){
					if(typeof dept_code_array[tdVal] != "undefined"){
						tdVal=dept_code_array[tdVal];
					}else{tdVal="";}
					rowData['departmentId_v']=tdVal;
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='elem_poe'){
					if(typeof poe_code_array[tdVal] != "undefined"){
						tdVal=poe_code_array[tdVal];
					}else{tdVal="";}
					rowData['elem_poe_v']=tdVal;
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
	if(no_record=='yes'){h+="<tr><td colspan='17' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);
	top.show_loading_image('hide');

	fac_options = '<option value=""></option>';
	for(x in cpt_cat){
		ff = cpt_cat[x];
		fac_options += '<option  value="'+ff.cpt_cat_id+'">'+ff.cpt_category+'</option>';
	}
	$('#cpt_cat_id').html(fac_options);

	$(document).ready(function(){
		if(multi_select_catagory_loaded==0){
			fac_catagory="";
			var r=1;
			for(c in cpt_cat){
				cc = cpt_cat[c];
				var sel="";
				if(r==1){sel=" selected ";}
				fac_catagory+='<option '+sel+' value="'+cc.cpt_cat_id+'">'+cc.cpt_category+'</option>';
				r++;
			}
			$("#cpt_categories").html(fac_catagory);
			$('#cpt_categories').selectpicker('refresh');
			multi_select_catagory_loaded=1;

	//=====================Rev Code TypeAhead===========================//
		$('#rev_code_v').typeahead({source:rev_code_type_array});
		$("#rev_code_v").blur(function(){$("#rev_code").val("");
			if($.trim(this.value)){
				var cod_val=$.trim(this.value);
				if(typeof rev_code_array_key[cod_val] != "undefined"){
					$("#rev_code").val(rev_code_array_key[cod_val]);
				}
			}
		});
	//==================================================================//

	//=====================TOS Code TypeAhead===========================//
		$('#tos_id_v').typeahead({source:tos_code_type_array});
		$("#tos_id_v").blur(function(){$("#tos_id").val("");
			if($.trim(this.value)){
				var cod_val=$.trim(this.value);
				if(typeof tos_code_array_key[cod_val] != "undefined"){
					$("#tos_id").val(tos_code_array_key[cod_val]);
				}
			}
		});
	//==================================================================//

	//=====================Dept Code TypeAhead===========================//
		$('#departmentId_v').typeahead({source:dept_code_type_array});
		$("#departmentId_v").blur(function(){$("#departmentId").val("");
			if($.trim(this.value)){
				var cod_val=$.trim(this.value);
				if(typeof dept_code_array_key[cod_val] != "undefined"){
					$("#departmentId").val(dept_code_array_key[cod_val]);
				}
			}
		});
	//==================================================================//

	//=====================POE Code TypeAhead===========================//
		$('#elem_poe_v').typeahead({source:poe_code_type_array});
		$("#elem_poe_v").blur(function(){$("#elem_poe").val("");
			if($.trim(this.value)){
				var cod_val=$.trim(this.value);
				if(typeof poe_code_array_key[cod_val] != "undefined"){
					$("#elem_poe").val(poe_code_array_key[cod_val]);
				}
			}
		});
	//==================================================================//

	//=====================Modifier Code TypeAhead===========================//
		var mod_typeahead_arr = new Array();
		$.each(mod_code_array,function(id,val){
			if(typeof(val) !== 'undefined'){
				mod_typeahead_arr.push(val);
			}
		});
		mod_typeahead_arr.sort();
		$('.mod_atypehead').each(function(id,elem){
			$(elem).typeahead({source:mod_typeahead_arr});
			$(elem).blur(function(){
				if($.trim(this.value)){
					var cod_val=$.trim(this.value);
					if(typeof mod_code_array_key[cod_val]!= "undefined"){
						$(elem).val(mod_code_array_key[cod_val]);
					}
				}
			});
		});
	//==================================================================//
	}
	});
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#cpt_fee_id').val('');
		document.add_edit_frm.reset();
	}
	addNewRow('');
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}

function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	$('#cpt_fee_id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.name,formObjects)){
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if (o.type == "checkbox" || o.type == "radio") {
					oid = on+'_'+v;
					$('#'+oid).attr('checked',true);
				} else if(o.type!='submit' && o.type!='button') {
					if(typeof(v)!='undefined'){
						o.value = v;
					}
				}
			}
		}
	}
	var dx_codes = "";
	dx_codes = (arrAllShownRecords[pkId]['dx_codes']).split(',');
	var g=0;
	var val_new=0;
	var tot_val_new = parseInt(dx_codes.length)/12;
	if(tot_val_new>1){
		for(f=1;f<tot_val_new;f++){
			val_new = parseInt(f)*12;
			addNewRow(val_new);
		}
	}
	for(k=0;k<dx_codes.length;k++){
		g = parseInt(k)+1;
		$('#dx_code_'+g).val(dx_codes[k]);
	}
}

function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	msg="";
	if($.trim($("#cpt_cat_id").val())==""){
		msg+=" &bull; Select the Category";
	}
	if($.trim($("#cpt4_code").val())==""){
		msg+="<br> &bull; Enter the Cpt4 Code";
	}
	if($.trim($("#cpt_prac_code").val())==""){
		msg+="<br> &bull; Enter the Practice Code";
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
		success: function(d) {
			top.show_loading_image('hide');
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
function catgory(){
	top.fmain.location.href = '../admin/billing/cpt_fee_tbl/cpt_categories.php';
}

function addNewRow(val){
	if(val==""){
		$('#dx_tbl').html('<div id="top_row_id"></div>');
		document.getElementById('last_cnt').value = 0;
	}
	var pre_cnt = document.getElementById('last_cnt').value;
	var pre_cnt_start = parseInt(pre_cnt)+1;
	var next_cnt = parseInt(pre_cnt_start) + 11;
	var td_val = '';
	var style_td="";
	td_val += '<div class="col-sm-11"><div class="row">';
	for(i=pre_cnt_start;i<=next_cnt;i++){
		td_val += ' <div class="col-sm-1"><label for="dx_code_'+i+'">Dx'+i+'</label><input class="dx_atypehead form-control" name="dx_code_'+i+'" id="dx_code_'+i+'" type="text"></div>';
	}
	td_val += '</div></div>';
	td_val += '<div class="col-sm-1 text-left">';
	td_val += '<br /><img class="link_cursor" src="../../../../library/images/add_icon.png" alt="Add More" onClick="addNewRow('+next_cnt+');">';
	if(val!=""){
		td_val += '&nbsp;&nbsp;<img class="link_cursor" src="../../../../library/images/closerd.png" alt="Delete Row" onClick="removeTableRow('+next_cnt+');">';
	}
	td_val += '</div>';
	var tr = ' <div class="row" id="dx_row_'+next_cnt+'">' + td_val + '</div>';
	if(val==""){
		var load_id=document.getElementById('last_cnt').value;
		if(load_id>0){
			$(tr).insertAfter('#'+load_id);
		}else{
			if($('#dx_row_'+next_cnt+'').length == 0){
				$("#top_row_id").last().append(tr);
			}
		}
	}else{
		$(tr).insertAfter('#dx_row_'+val);
	}
	document.getElementById("last_cnt").value = next_cnt;
	set_typeahed();
}

function removeTableRow(cnt){
	document.getElementById('dx_row_'+cnt).style.display='none';
	var cnt_start = parseInt(cnt)-11;
	for(i=cnt_start;i<=cnt;i++){
		document.getElementById('dx_code_'+i).value="";
	}
}

function checkValidDxCode(o) {
    var v = $.trim(o.value);
    if (v != "") {
        $.ajax({
            type: "GET",
            url: "ajax.php?p_dx=" + encodeURI(v) + "&task=checkValidDxCode",
            async: false,
            success: function (d) {
                d = JSON.parse(d);
                if (d.flg == "OK") {
                    o.value = d.icd10;
                } else {
                    o.value = "";
                }
            },
        });
    }
}
//function  checkValidDxCode(o){
//	var v = $.trim(o.value);
//	if(v!=""){ $.get("ajax.php?p_dx="+encodeURI(v)+"&task=checkValidDxCode",function(d){ if(d.flg=="OK"){ o.value = d.icd10;  }else{ o.value = ""; }   },'json');	}
//}

function refresh_result(obj){
	var cpt_array_cat = new Array();
	$("#"+obj+" option:selected").each(function(id, elem){
			var value = $(elem).val();
			cpt_array_cat.push(value);
	});
	var cpt_cat_string = cpt_array_cat.join(',');
	LoadResultSet('',cpt_cat_string,'','','');
}

function set_typeahed(){
	var dx_typeahead_arr = new Array();
	$.each(dx_code_array,function(id,val){
		if(typeof(val) !== 'undefined'){
			dx_typeahead_arr.push(val);
		}
	});
	var last_cnt_loop = document.getElementById('last_cnt').value;
	$('.dx_atypehead').each(function(id,elem){
		$(elem).bind("blur",function(){
			checkValidDxCode(elem);
		});
		var s = $(elem).typeahead({source:dx_typeahead_arr});
	});
}

function make_csv(){
	var cpt_array = new Array();
	$('#cpt_categories option:selected').each(function() {
		cpt_array.push($(this).val());
	});
	cpt_cat_id = cpt_array;
	var practice_code = $('#practice_code').val();

	if(typeof(cpt_cat_id) == "undefined"){ cpt_cat_id=""; }
	if(typeof(practice_code) == "undefined"){ practice_code=""; }
	var url = top.JS_WEB_ROOT_PATH+"/interface/admin/billing/cpt_fee/cpt_csv.php?cpt_cat_id="+cpt_cat_id+"&practice_code="+practice_code;
	window.location=url;
}

var ar = [["add_new","Add New","top.fmain.addNew();"],["pos_del","Delete","top.fmain.deleteSelectet();"],["dx_cat","Manage Category","top.fmain.catgory();"],["export_cpt","Export CSV", "top.fmain.make_csv();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('CPT');
	$("#cpt4_code").blur(function(){
		var cpt=$.trim($("#cpt4_code").val());
		if(cpt){$("#cpt_prac_code").val(cpt);}
	});
	$("#search").keypress(function (evt){
		if(evt.keyCode==13){
			LoadResultSet(this.value,'','','','');
		}
	});
	show_loading_image('none');
	set_typeahed();
});
