var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('phrase_id','phrase');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Test...');
	
	if(typeof(s)!='string' || s==''){s = 'Active';}
	s_url = "&s="+s;
	//start
	if(typeof(p)=='undefined'){p_url='';}else if($.trim(p)){p_url='&p='+p;$('a').parent('li').removeClass('pointer active');$('#'+p).parent('li').addClass('pointer active');$('#alphabet').val(p);}
	//end
	//if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
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
	ajaxURL = "ajax_lenses.php?task=show_list"+s_url+p_url+f_url+so_url;
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
	pos_facility_list = r.pos_facility_list;
	var pos_facility_array=new Array();
	var pos_facility_array_key=new Array();
	for(f in pos_facility_list){
		ff = pos_facility_list[f];
		pos_facility_array[ff.pos_facility_id]=ff.facilityPracCode;
		pos_facility_array_key[ff.facilityPracCode]=ff.pos_facility_id;
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
				if(y=='optical_lenses_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='vendor_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">&nbsp;'+tdVal+'</td>';
				}
				if(y=='Tab_val'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
				}
				if(y=='lens_type'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
				}
				if(y=='lens_material'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
				}
				if(y=='lab_name'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">&nbsp;'+tdVal+'</td>';
				}
				if(y=='Cost_Price'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
				}
				if(y=='Retail_Price'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
				}
				if(y=='pos_facility_id'){
					if(typeof pos_facility_array[tdVal] != "undefined"){
						tdVal=pos_facility_array[tdVal];
					}else{tdVal="";}
					row	+= '<td onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='9' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);
	fac_options = '<option value="0"> Select </option>';
	for(x in pos_facility_list){
		ff = pos_facility_list[x];
		fac_options += '<option  value="'+ff.pos_facility_id+'">'+ff.facilityPracCode+' - '+ff.pos_prac_code+'</option>';
	}			
	$('#pos_facility_id').html(fac_options);		
	top.show_loading_image('hide');
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';
		$('#divLogoLink').css('display','block');
	} else {
		modal_title = 'Add New Record';
		$('#optical_lenses_id').val(''); 
		$('#divLogoLink').css('display','none');
		document.add_edit_frm.reset();
		show_lense_type($('#Tab_val').val());
		check_lens_material();
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}

function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();
	$('#optical_lenses_id').val(pkId);
	var proArr1 = new Array("Creation","Varilux");
	var matArr1 = new Array("Glass","Hi Index","Plastic","Polycarbonate","Trivax");
	var chl_pro = chl_mat = 0;
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if (o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).attr('checked',true);
				} else if(o.type!='submit' && o.type!='button'){
					if(on=="patient_discount_actual" || on=="family_discount_actual")
					{
						v = '$'+v;
					}
					o.value = v;
					if(on=="progresive_text")
					{
						for(var g=0;g<proArr1.length;g++)
						{
							if(v==proArr1[g])
							{
								chl_pro=1;
							}
						}
						if(chl_pro==0)
						{
							document.getElementById("Progresive_val").style.display = 'none';
							document.getElementById("progresive_text").value = 'Other';
							document.getElementById("progresive_txt").style.display = 'block';
						}
					}
					if(on=="lens_material")
					{
						for(var f=0;f<matArr1.length;f++)
						{
							if(v==matArr1[f])
							{
								chl_mat=1;
							}
						}
						if(chl_mat==0)
						{
							document.getElementById("lens_material1").style.display = 'none';
							document.getElementById("lens_material").value = 'Other';
							document.getElementById("lens_material2").style.display = 'block';
						}
					}
				}
			}
		}
	}
	show_lense_type($('#Tab_val').val());
	check_lens_material();		
}

function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	var msg="";
	
	if($.trim($('#vendor_name').val())==""){
		msg+="&nbsp;&bull;&nbsp;Lab Name<br>";
	}
	if(msg){
		msg_val="<b>Please fill the following&nbsp;:-</b><br>"+msg;
		top.fAlert(msg_val);
		top.show_loading_image('hide');
		return false;
	}
	
	$.ajax({
		type: "POST",
		url: "ajax_lenses.php",
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
			var sel_apha=$('#alphabet').val();
			$('#myModal').modal('hide');
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
		url: "ajax_lenses.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
			else{top.fAlert(d+'Record delete failed. Please try again.');}
		}
	});
}

function show_lense_type(obj){
	var lensArr1 = new Array('Single Vision','BiFocal','TriFocal','Progresive');
	var lensArr = new Array('single_vision','BiFocal','TriFocal','Progresive');
	for(var i=0;i<lensArr1.length;i++){
		if(obj == lensArr1[i]){
			document.getElementById(lensArr[i]+"_val").style.display = 'block';
		}
		else{
			document.getElementById(lensArr[i]+"_val").style.display = 'none';
		}
	}
	if(obj!="Progresive")
	{
		document.getElementById("progresive_txt").style.display = 'none';
	}
	if(obj=="Progresive" && $('#progresive_text').val()=="Other")
	{
		document.getElementById("progresive_txt").style.display = 'block';
		document.getElementById("Progresive_val").style.display = 'none';
	}
	else
	{
		document.getElementById("progresive_txt").style.display = 'none';
	}
}

function check_lens_material(){
	var val_lens_material = document.getElementById("lens_material").value;
	if(val_lens_material == 'Other'){
		document.getElementById("lens_material1").style.display = 'none';
		document.getElementById("lens_material2").style.display = 'block';
		document.getElementById("lens_material3").focus();
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

function priceValid(obj){
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
		}
		else if(val.length == 4){
			if(val.indexOf('.') == 1){
				field.value = '$'+val;	
			}
			else if(val.indexOf('.') == 2){
				field.value = '$'+val+"0";	
			}
			else if(val.indexOf('.') < 0){
				var setDec = val.split('');
				setDec.splice(3,0,".");
				setDec.splice(5,0,"0");
				field.value = '$';
				for(var i=0;i<setDec.length;i++){
					field.select();
					field.value += setDec[i];
				}
			}
		}
		else if(val.length > 4)
		{
			field.value = '$';
			if(val.indexOf('.') == 1){
				var setDecimal = val.split('');
				for(var j=0;j<4;j++){
					field.value += setDecimal[j];
				}		
			}
			else if(val.indexOf('.') == 2){
				var setDecimal = val.split('');
				for(var j=0;j<5;j++){
					field.value += setDecimal[j];
				}		
			}
			else if(val.indexOf('.') == 3){
				var setDecimal = val.split('');
				for(var j=0;j<6;j++){
					field.value += setDecimal[j];
				}		
			}
			else if(val.indexOf('.') < 0){
				var setDecimal = val.split('');
				setDecimal.splice(3,0,".");
				for(var j=0;j<6;j++){
					field.select();
					field.value += setDecimal[j];
				} 
			}
		}
	}
}

function discountOpt(obj){
	var fieldchk = false;
	var fieldval = obj.value;
	if(obj.value){
		var firstChr = fieldval.charAt(0);
		var lastChr = fieldval.charAt(fieldval.length-1);
		if(firstChr != '$' && lastChr != '%'){
			fieldchk = true;
		}
		if(firstChr == '$' && lastChr == '%'){
			fieldchk = true;
		}
		if(fieldchk == true){
			top.fAlert("Discount could be in actual or in percentage");
			obj.value = '';
			obj.focus();				
		}
		if(firstChr == '%'){
			top.fAlert("Please enter percentage after value");
			obj.value = '';
			obj.focus();
			return false;
		}
		if(lastChr == '$'){
			top.fAlert("Please enter $ Sign before actual amount");
			obj.value = '';
			obj.focus();
			return false;
		}			
	}
}

function change_progresive(obj){
	if(obj == 'Other'){
		document.getElementById("progresive_txt").style.display = 'block';
		document.getElementById("progresive_text1").style.display = 'block';
		document.getElementById("Progresive_val").style.display = 'none';
		document.getElementById("progresive_text1").focus();
	}
	else{
		document.getElementById("progresive_text").selectedIndex = 0;
		document.getElementById("progresive_txt").style.display = 'none';
		document.getElementById("progresive_text1").style.display = 'none';
		document.getElementById("Progresive_val").style.display = 'block';
	}
}

function checkField(){
	if(document.getElementById("lens_material3").value == ''){
		document.getElementById("lens_material").selectedIndex = 0;
		document.getElementById("lens_material1").style.display = 'block';
		document.getElementById("lens_material2").style.display = 'none';
	}
}

function checkProgField(){
	if(document.getElementById("progresive_text1").value == ''){
		document.getElementById("progresive_text").selectedIndex = 0;
		document.getElementById("Progresive_val").style.display = 'block';
		document.getElementById("progresive_txt").style.display = 'none';
	}
}

function checkFieldData(id)
{
	var field = document.getElementById(id);
	var getNum = field.value.replace("$","");
	getNum = getNum.replace(".","");
	if(isNaN(getNum) || getNum.indexOf('.') > -1)
	{
		top.fAlert('Field must contain a numeric value');
		field.value = '';
		return false;
	}
}
var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('Lenses');
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