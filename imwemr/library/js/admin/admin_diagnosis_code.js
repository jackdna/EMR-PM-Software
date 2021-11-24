var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('diagnosis_id','dx_code');
var category_loaded = 0;
var d_val="";
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300','Loading Dx Code...');
	if(typeof(s)!='string' || s==''){s = 'Active';}
	s_url = "&s="+s;
	if($("#search").val()){p=$("#search").val();}
	var	dx_array = new Array();
	$('#dx_catagory option:selected').each(function() {
		dx_array.push($(this).val());
	});
	f = dx_array;
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
	var select_id = $(currLink).find('select').attr('id');
	if(!select_id){
		if(soAD=='ASC') $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
		else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
	}
	so_url='&so='+so+'&soAD='+soAD;
	ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
	$.ajax({
	  url: ajaxURL,
	  success: function(r){
		showRecords(r);
	  }
	});
}
function showRecords(r){
	r = JSON.parse(r);
	result = r.records;
	dx_cat= r.dx_cat;
	h='';
	var no_record="yes";
	if(r != null){
		row = '';
		var r=1;
		for(x in result){
			no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='diagnosis_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:13px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='category'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='dx_code'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='d_prac_code'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='pqriCode'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';						
				}
				if(y=='recall'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='diag_description'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='snowmed_ct'){
					row+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
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
	var cat_name_arr=new Array();
	
	$(document).ready(function(){
		//alert($("#cat_exist").val());
		if(category_loaded == 0){
			var dx_cat_arr = new Array();
			var dx_cat_arr_key = new Array();
			var cat_html="";
			r=1;
			for(x in dx_cat){
				ff = dx_cat[x];				
				dx_cat_arr_key.push(ff.diag_cat_id);
				dx_cat_arr.push(ff.category);
				cat_name_arr[ff.category]=ff.diag_cat_id;
				var sel="";
				if(r==1){ sel=" selected='selected' ";}
				cat_html+="<option "+sel+" value='"+ff.diag_cat_id+"'>"+ff.category+"</option>";r++;
			}
			$("#dx_catagory").html(cat_html);
			$('#dx_catagory').selectpicker('refresh');
															
			category_loaded = 1;
			$('.dx_cat').each(function(id,elem){
				$(elem).typeahead({
				 source:dx_cat_arr,
					 updater:function(item){
						 return item;
					 }
				});
			});	
			$("#category").blur(function(){
				$("#diag_cat_id").val("");
				if(typeof cat_name_arr[$.trim($("#category").val())] != "undefined"){
					$("#diag_cat_id").val(cat_name_arr[$.trim($("#category").val())]);
				}
			});		
		}
	});
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#diagnosis_id').val('');
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
	msg="";
	if($.trim($("#category").val())==""){
		msg+=" &bull; Enter the Category Name";
	}
	if($.trim($("#dx_code").val())==""){
		msg+="<br> &bull; Select the Dx Code";
	}
	if($.trim($("#d_prac_code").val())==""){
		msg+="<br> &bull; Enter the Practice Code";
	}
	if($.trim($("#diag_description").val())==""){
		msg+="<br> &bull; Enter the Description";
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
			//top.fAlert(d);
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
	$('#diagnosis_id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.dx_code,formObjects)){
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];//alert(v);
			if(o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if(o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).attr('checked',true);
				}else if(o.type!='submit' && o.type!='button'){
					o.value = v;
				}
			}
		}
	}		
}
function make_csv(){
	var data = "dx_code_csv.php?field=no;"
	document.getElementById('export_csv').src = data;
}
function catgory(){
	top.fmain.location.href = '../admin/billing/diagnosis_code/diag_categories.php';
}

var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_del","Delete","top.fmain.deleteSelectet();"],["dx_csv","Export CSV","top.fmain.make_csv();"], ["dx_cat","Manage Category","top.fmain.catgory();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	//LoadResultSet();
	check_checkboxes();
	set_header_title('Dx Codes');
	$("#dx_code").blur(function(){
		if($("#dx_code").val()){$("#d_prac_code").val($("#dx_code").val());}
	});
	$("#search").keypress(
		function (evt){
			if(evt.keyCode==13){	
				LoadResultSet(this.value,'','','','');
			}
		});
});
show_loading_image('none');