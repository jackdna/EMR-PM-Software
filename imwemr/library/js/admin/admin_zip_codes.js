var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('id','title');
var sub_sat=new Array();
	sub_sat[0]="Permissible";
	sub_sat[1]="Not Permissible";
	sub_sat[2]="Brand";
var eye_array=new Array("PO","OU","OS","OD","RLL","RUL","LLL","LUL","O/O","IV","IM","Topical","L/R Ear","Both Ears");
var use_array=new Array("qd","qhs","qAM","qid","bid","tid","qod","__hrs","__Xdaily");
function LoadResultSet(p,f,s,so,currLink){
	var cont_num=1;
	top.show_loading_image('hide');
	top.show_loading_image('show','300','Loading Zip Codes...');
	var srch_zipcode=$('#zip_search').val();
	if(srch_zipcode){s=srch_zipcode;}
	if(typeof(s)=='undefined'){s_url='';}else if($.trim(s)){s_url='&s='+s;}
	if(typeof(p)=='undefined'){p_url='';}else if($.trim(p)){p_url='&p='+p;$('a').parent('li').removeClass('pointer active');$('#'+p).parent('li').addClass('pointer active');$('#alphabet').val(p);}
	if(typeof(f)=='undefined'){f_url='';}else if($.trim(f)){f_url='&f='+f;cont_num=f;}
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
		success: function(r) {//a=window.open();a.document.write(r); ///*dataType: "json",*/
		showRecords(r,cont_num,p);
	  }
	});
}
function showRecords(r,con_num,chp){
	r = jQuery.parseJSON(r);
	result = r.records;
	cnt_rows=r.cnt_rows;
	qry_ck=r.qry;
	h='';var no_record='yes';
	if(r != null){
		row = '';
		for(x in result){no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='zip_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+tdVal+'"><label for="'+tdVal+'"></label></div></td>';}rowData[y] = tdVal;
				if(y=='zip_code_ext'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='city'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='state_abb'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='state'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='county'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='country'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='8' style='text-align:center; '>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	var num_span="";
	$("#num_count").html('');
	var cnt_start=1;
	cnt_end=cnt_rows;
	if(cnt_rows>25){
		cnt_end=25;
		if(con_num>10){cnt_start=parseInt(con_num)-10;cnt_end=parseInt(con_num)+10;}
	}
	var c_alpha=$('#alphabet').val();var d_t=s_class="";
	for(var t=cnt_start;t<=cnt_end;t++){
		s_class='';d_t=t;if(t==con_num){d_t=""+t+"";s_class='selected';}
		num_span+=" <span class='num_cnt "+s_class+"' id=\"conr_"+t+"\" onclick='LoadResultSet(\""+c_alpha+"\",\""+t+"\")'>"+d_t+"</span> ";
		if(cnt_rows<=t){
			break;
		}
	}
	//num_span=num_span+"<span class='num_cnt' style='margin-left:50px'>Next</span>";
	$("#num_count").html(num_span);
	top.show_loading_image('hide');
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#zip_id').val('');
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
	if($.trim($('#zip_code').val())==""){
		msg+= "&nbsp;&bull; "+top.zipLabel+" <br>";
	}
	if($.trim($('#city').val())==""){
		msg+= "&nbsp;&bull; City<br>";
	}
	if($.trim($('#state_abb').val())==""){
		msg+= "&nbsp;&bull; State Abb<br>";
	}
	if($.trim($('#state').val())==""){
		msg+= "&nbsp;&bull; State<br>";
	}
	if(msg){
		msg_ = "<b>Please fill in the following:-</b><br>"+msg;
		top.fAlert(msg_);
		top.show_loading_image('hide');
		return false;
	}
	var zipcode_ele=$('#zip_code').val();
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='enter_unique'){
				top.fAlert(top.zipLabel+" '"+zipcode_ele+"' already exists for this city.");
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
	$('#zip_id').val(pkId);
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

var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title(top.zipLabel); //'Zip Code'
	var alphabet='';
	var first="A",last="Z";
	var ch='';
	for(var i = first.charCodeAt(0); i <= last.charCodeAt(0); i++){
		ch=eval("String.fromCharCode("+i+")");
		cl='';if(ch=='A'){cl='pointer active';}
		alphabet+="<li class=\""+cl+"\"><a id=\""+ch+"\" onClick='LoadResultSet(\""+ch+"\")' style='cursor:pointer'>"+ch+"</a></li>";
	}$("#pagenation_alpha_order").html(alphabet);
	$("#zip_search").keypress(function (evt){
		if(evt.keyCode==13){	
			LoadResultSet('','',this.value,'','');
		}
	});
	$("#zip_search").blur(function(){
		if(this.value){
		LoadResultSet('','',this.value,'','');
		}
	});
});
show_loading_image('none');