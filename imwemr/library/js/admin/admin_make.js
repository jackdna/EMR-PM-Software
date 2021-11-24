var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('phrase_id','phrase');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Test...');
	
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
	ajaxURL = "ajax_make.php?task=show_list"+s_url+p_url+f_url+so_url;
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
	dx_code_list = r.dx_code_list;
	cpt_prac_code_list = r.cpt_prac_code_list;
	vender_name_list = r.vender_name_list;
	var cpt_prac_code_list_array=new Array();
	var vender_name_list_array=new Array();
	var typeahead_arr = new Array();
	
	for(x in cpt_prac_code_list){
		ff = cpt_prac_code_list[x];				
		cpt_prac_code_list_array.push(ff.cpt_prac_code);
		
		var tmp_val = new Array();
		tmp_val['cpt_fee_id'] = ff.cpt_fee_id
		tmp_val['cpt4_code'] = ff.cpt4_code
		tmp_val['cpt_fee'] = ff.cpt_fee
		typeahead_arr[ff.cpt_prac_code] = tmp_val;
	}
	for(y in vender_name_list){
		gg = vender_name_list[y]
		vender_name_list_array.push(gg.vendor_name);
	}
	
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
				if(y=='make_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='manufacturer'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='style'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='type'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='base_curve'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='diameter'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='cpt4_code'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='cpt_practice_code'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				if(y=='price'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">'+tdVal+'</td>';
				}
				
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='9' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
	
	dx_options = '<option value=""></option>';
	for(x in dx_code_list){
		ff = dx_code_list[x];
		dx_options += '<option  value="'+ff.diagnosis_id+'">'+ff.dx_code+'</option>';
	}			
	$('#dx_code_id').html(dx_options);

	$(document).ready(function(){
		$('.cpt_prac').each(function(id,elem){
			var autocomplete = $(elem).typeahead();
			autocomplete.data('typeahead').source = cpt_prac_code_list_array;
			autocomplete.data('typeahead').updater = function(item){
				var selected_prac_code = typeahead_arr[item];
				$("#cpt_fee_id").val(selected_prac_code['cpt_fee_id']);
				$("#cpt4_code").val(selected_prac_code['cpt4_code']);
				$("#price").val(selected_prac_code['cpt_fee']);
				return item;
			};	
			$(elem).blur(function(){
				$("#cpt_fee_id").val("");
				$("#cpt4_code").val("");
				$("#price").val("");
				if(typeof typeahead_arr[$.trim($(this).val())] != "undefined"){
					var selectedPracCode = typeahead_arr[$.trim($(this).val())];
					$("#cpt_fee_id").val(selectedPracCode['cpt_fee_id']);
					$("#cpt4_code").val(selectedPracCode['cpt4_code']);
					$("#price").val(selectedPracCode['cpt_fee']);
				}
			});
			
		});
		$('.manufac_vender').each(function(id,elem){
			var autocomplete = $(elem).typeahead();
			autocomplete.data('typeahead').source = vender_name_list_array;
		});
	});
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#make_id').val('');
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
	if($.trim($('#manufacturer').val())==""){
		msg+="&nbsp;&bull;&nbsp;Enter Manufacturer<br>";
	}
	if(msg){
		msg_val="<b>Please fill the following&nbsp;:-</b><br>"+msg;
		top.fAlert(msg_val);
		top.show_loading_image('hide');
		return false;
	}
	$.ajax({
		type: "POST",
		url: "ajax_make.php",
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
		url: "ajax_make.php",
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
	$('#make_id').val(pkId);
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
function setCurrSign(obj){
	var field = document.getElementById(obj.id);
	if(field.value == '' || field.value == null)
	{
		field.select();
		field.value = '$';
	}
	else
	{
		return false;
	}
}
function priceValid(obj)
{
	var field = document.getElementById(obj.id);
	
	if(field.value != '')
	{
		var val = field.value.replace("$","");
		
		if(val.length <= 3)
		{
			if(val.indexOf('.') == 1)
			{
				field.value = '$'+val+"0";
			}
			else if(val.indexOf('.') < 0)
			{
				field.value = '$'+val+".00";
			}
			//field.value = '$'+val+".00";
		}
		else if(val.length == 4)
		{
			if(val.indexOf('.') == 1)
			{
				field.value = '$'+val;	
			}
			else if(val.indexOf('.') == 2)
			{
				field.value = '$'+val+"0";	
			}
			else if(val.indexOf('.') < 0)
			{
				var setDec = val.split('');
				setDec.splice(3,0,".");
				setDec.splice(5,0,"0");
				field.value = '$';
				
				for(var i=0;i<setDec.length;i++)
				{
					field.select();
					field.value += setDec[i];
				}
			}
		}
		else if(val.length > 4)
		{
			field.value = '$';
			if(val.indexOf('.') == 1)
			{
				var setDecimal = val.split('');
				for(var j=0;j<4;j++)
				{
					field.value += setDecimal[j];
				}		
			}
			else if(val.indexOf('.') == 2)
			{
				var setDecimal = val.split('');
				for(var j=0;j<5;j++)
				{
					field.value += setDecimal[j];
				}		
			}
			else if(val.indexOf('.') == 3)
			{
				var setDecimal = val.split('');
				for(var j=0;j<6;j++)
				{
					field.value += setDecimal[j];
				}		
			}
			else if(val.indexOf('.') < 0)
			{
				var setDecimal = val.split('');
				setDecimal.splice(3,0,".");
				
				
				for(var j=0;j<6;j++)
				{
					field.select();
					field.value += setDecimal[j];
				} 
			}
		}
	}
}

function checkFieldData(id){
	var field = document.getElementById(id);
	var getNum = field.value.replace("$","");
	getNum = getNum.replace(".","");
	if(isNaN(getNum) || getNum.indexOf('.') > -1){
		field.className = "mandatory form-control";
		fAlert('&bull; Field must contain a numeric value<br>');
		field.value = '';
		return false;
	}
}
var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('Make');
});
show_loading_image('none');			