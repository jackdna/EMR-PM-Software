var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('id','title');
function LoadResultSet(p,f,s,so,currLink,alpha,page,record_limit,searchStr,cont_num){//p=practice code, f=fac code, s=status, so=sort by;
    var cont_num = cont_num || 1;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Sx...');
	
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
	ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url+pg_url+search_Url;
	$.ajax({
        url: ajaxURL,
        success: function(r) {
            showRecords(r, cont_num);
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
				if(y=='title'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='2' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);	
    num_paging(total_pages,cont_num);
	top.show_loading_image('hide');
}
function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#id').val('');
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
	if($.trim($('#title').val())==""){
		top.fAlert("&bull; Enter the Sx name<br>");
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
			else{top.fAlert(d+'Record delete failed. Please try again.');}
		}
	});
}
function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();
	$('#id').val(pkId);
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

var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('SX');	
    
    
    $("#search").keypress(function (evt){ if(evt.keyCode==13){ srh_records(); } });
	
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
    
    
});
show_loading_image('none');