var arrAllShownRecords = new Array;
var totalRecords	   = 0;
var category_loaded = 0;

function LoadResultSet(p,f,s,so,currLink,alpha,page,record_limit,searchStr,cont_num){//p=practice code, f=fac code, s=status, so=sort by;
    var cont_num = cont_num || 1;

    parent.parent.show_loading_image('block','300', 'Loading ICD10...');
   if(typeof(s)!='string' || s==''){s = 'Active';}
	s_url = "&s="+s;
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

	pg_url = '&alpha='+alpha+'&page='+page+'&record_limit='+record_limit;	
    ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url+pg_url;

	$.ajax({
		url: ajaxURL,
		success: function(r) {
			showRecords(r,cont_num);
		}
	});
}
var dx_name_array=new Array();

function showRecords(r,cont_num){
	r 				= JSON.parse(r);
	result 			= r.records;
	icd10_cat 		= r.icd10_cat;
	icd10_lat 		= r.icd10_laterality;
	unique_parents	= r.unique_parents;
    
    var total_pages = r.total_pages;
    
	h='';
					
	//STATUS
	var statusArr = {'0':"Active", '1':"Active till 09-30-16", '2':'Active from 10-01-16'};
	var status_html = '';
	$.each(statusArr,function(status_id,status_val){
		status_html+='<option value="'+status_id+'">'+status_val+'</option>';
	});
	$("#status").html(status_html);
	
	//CATEGORY
	var cat_name_arr = new Array();
	var cat_html = '';
	for(x in icd10_cat){
		cc = icd10_cat[x];				
		cat_name_arr[cc.id]=cc.title;
		cat_html+='<option value="'+cc.id+'">'+cc.title+'</option>';
	}
	$("#cat_id").html(cat_html);
	
	//LATERALITY
	var lat_name_arr = new Array();
	var lat_html = stag_html = sever_html = '<option value=""></option>';
	for (x in icd10_lat){
		ll = icd10_lat[x];
		lat_name_arr[ll.id] = ll.title;
		if(ll.id==1 || ll.id==2){
			lat_html += '<option value="'+ll.id+'">'+ll.title+'</option>';
		}else if(ll.id==3){
			sever_html += '<option value="'+ll.id+'">'+ll.title+'</option>';
		}else{
			stag_html += '<option value="'+ll.id+'">'+ll.title+'</option>';
		}
	}
	$('#laterality').html(lat_html);
	$('#staging').html(stag_html);
	$('#severity').html(sever_html);
	var show_status="";
	if(r != null){
		row = '';
		for(x in result){
			s = result[x];
			rowData = new Array();
			row1 = '<tr>';
			for(y in s){
				tdVal = s[y];
				//alert(y+' => '+tdVal);
				if(y=='id'){
					pkId = tdVal;
					row1 += '<td style="width:20px; padding-left:13px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';
					isParent = false;
					if(typeof(unique_parents[pkId])!='undefined'){isParent = true;}
				}
				rowData[y] = tdVal;
				if(y=='cat_id'){
					if(typeof(cat_name_arr[tdVal])!='undefined'){
						tdVal=cat_name_arr[tdVal];
					}else{tdVal="";}
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='icd9'){
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='icd9_desc'){
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='icd10'){
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
					if(tdVal!=""){
						dx_name_array[x]=tdVal+'; ';
					}
				}
				if(y=='breadCrumb'){
					if(tdVal==''){
						tdVal=rowData['icd10_desc'];
						rowData['IDstr'] = '';
					}else{
						idNameArr = getBreadCrumbIdNamesArr(tdVal);
						IDarr = idNameArr[0];
						NMarr = idNameArr[1];
						breadCrumb = IDstr = '';
						for(x in NMarr){
							if(breadCrumb!=''){breadCrumb += '> '; IDstr +='> ';}
							breadCrumb += NMarr[x];
							IDstr 	   += IDarr[x];
						}
						tdVal = breadCrumb;
						rowData['IDstr'] = IDstr;
						rowData['NMstr'] = breadCrumb;
					}
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='laterality'){
					latval = lat_name_arr[tdVal];
					if(typeof(latval)!='undefined') tdVal = latval;
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='staging'){
					latval = lat_name_arr[tdVal];
					if(typeof(latval)!='undefined') tdVal = latval;
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='severity'){
					latval = lat_name_arr[tdVal];
					if(typeof(latval)!='undefined') tdVal = latval;
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='status'){
					if(tdVal==1){
						show_status="Active till &nbsp;09-30-16";
					}else if(tdVal==2){
						show_status="Active from &nbsp;10-01-16";
					}else{
						show_status="Active";
					}
					row1	+= '<td onclick="addNew(1,\''+pkId+'\');">'+show_status+'</td>';
				}
			}
			if(isParent==false){
				totalRecords++;
				row1 += '</tr>';
				row  += row1;
			}
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	$('#result_set').html(h);
    num_paging(total_pages,cont_num);
	top.show_loading_image('hide');
	$(document).ready(function(){
		if(category_loaded == 0){
			$("#dx_catagory").html(cat_html);
			$('#dx_catagory').selectpicker('refresh');
			category_loaded = 1;
		}
	})
	$("#master_codes").typeahead({source:dx_name_array});
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
		if(i==cont_num){d_t=""+i+"";s_class='selected';}
		num_span +=" <span class='num_cnt "+s_class+"' id=\"conr_"+i+"\" onclick='LoadResultSet(\"\",\"\",\"\",\"\",\"\",\""+alpha+"\","+i+",\"\",\"\",\""+i+"\")'>"+d_t+"</span> ";
		if(total_pages<=i){
			break;
		}
	}
	$("#div_pages").html(num_span);
}

function getBreadCrumbIdNamesArr(b){
	var IDarr = new Array();
	var BCarr = new Array();
	a = b.split('~::~');
	for(x in a){
		b = a[x].split(':~:');
		IDarr[x] = b[0];
		BCarr[x] = b[1];
	}
	return Array(IDarr,BCarr);
}
function addNew(ed,pkId,isParent){
	$('#add_node_form').addClass('hide');
	$('#myModal').modal('show');
	$('#myModal table').empty();
	if(typeof(ed)!='undefined' && ed!=''){
		$('#myModal .modal-header .modal-title').text('Edit Record');
		if(typeof(pkId)!='undefined' && pkId>0){
			fillEditData(pkId,isParent);
		}
	}else{
		$('#myModal .modal-header .modal-title').text('Add New Record');
		//$("#myModal").load(location.href + " #myModal > *");
		$('#id').val('');
		document.add_edit_frm.reset();
		$('#span_breadCrumb').html('');
	}
}

function saveFormData(node){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	icd_catag = $.trim($("#cat_id").val());
	icd10_val = $.trim($("#icd10").val());
	icd10_des = $.trim($("#icd10_desc").val());
	icd10_staging = $.trim($("#staging").val());
	icd10_severity = $.trim($("#severity").val());
    var page = $('#page').val();
	msg="";
	
	if(icd_catag==""){
		msg+=" &bull; Please select the Category<br>";
	}
	if(icd10_des==""){
		msg+=" &bull; Enter the ICD Description<br>";
	}
	if(icd10_val==""){
		msg+=" &bull; Enter the ICD-10 Code<br>";
	}
	if(icd10_staging!="" && icd10_severity!=''){
		msg+=" &bull; Please select either Staging or Severity<br>";
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
			if(typeof(node)=='string' && node=='yes'){
				$('#addNew_div').hide();
			}else{
				if(d.toLowerCase().indexOf('success') > 0){
					top.alert_notification_show(d);
				}else{
					top.fAlert(d);
				}
				$('#myModal').modal('hide');
                LoadResultSet("","","","","","","","","",""+page+"");
			}
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
		a = confirm('Are you sure, want to delete?');
		if(!a) return false;
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
				if(d=='1'){top.alert_notification_show('Record deleted.'); LoadResultSet();}
				else{top.fAlert('Record delete failed. Please try again.');}
			}
		});
	}else{
		top.fAlert('No Record Selected.');
	}
}
function fillEditData(pkId){
	document.add_edit_frm.reset();
	$('#span_breadCrumb').html('');
	parent_id	= parseInt(arrAllShownRecords[pkId]['parent_id']);
	IDstr		= arrAllShownRecords[pkId]['IDstr'];
	if(IDstr!=''){
		IDarr		= IDstr.split('> ');
		fillForm(IDarr[0],'normal');
		var LastID = 0;
		for(x in IDarr){
			LastID = IDarr[x];
			if(x>=1) fillForm(IDarr[x],'nodes');
		}
		var formObjects	= new Array('icd10','icd9','icd9_desc','laterality','staging','severity','master_codes','group_heading','no_bilateral','status');
		for(i=0;i<formObjects.length;i++){
			o = formObjects[i];
			on	= $('#'+o);
			v	= arrAllShownRecords[LastID][o];
			if(o=='no_bilateral'){
				if(v==1){
					on.prop('checked',true);
				}else{
					on.prop('checked',false);
				}
			}else{
				on.val(v);
			}
		}
	}else{
		fillForm(pkId,'normal');
	}
}
function fillForm(pkId,mode){
	if(mode=='normal'){
		var formObjects	= new Array('id','cat_id','icd10','icd10_desc','icd9','icd9_desc','laterality','staging','severity','master_codes','group_heading','no_bilateral','status');
		$('#id').val(pkId);
		for(i=0;i<formObjects.length;i++){
			o = formObjects[i];
			v	= arrAllShownRecords[pkId][o];
			on	= $('#'+o);
			if(o=='no_bilateral'){
				if(v==1){
					on.prop('checked',true);
				}else{
					on.prop('checked',false);
				}
			}else{
				on.val(v);
			}
		}
		$('#node_count').val('0');
		if($('#node_ac').hasClass('hide') === false){
			$('#node_ac').addClass('hide');
		}
		
		if($('#node_de').hasClass('hide') === true){
			$('#node_de').removeClass('hide');
		}
	}else if(mode=='nodes'){
		AddNode();
		node_count 	= parseInt($('#node_count').val());
		obj_nid		= $('#node_id'+node_count);
		obj_ndesc	= $('#node_desc'+node_count);
		obj_nid.val(pkId);
		obj_ndesc.val(arrAllShownRecords[pkId]['icd10_desc']);
	}
}

function AddNode(){	
	node_count 	= parseInt($('#node_count').val());
	cnt 		= node_count+1;
	txt_width	= 94-(5*cnt);
	if($('#add_node_form').hasClass('hide') === true){
		$('#add_node_form').removeClass('hide');
	}
	tr		= '<tr class="pt10">';
	tr     += '<td>Sub-node (Level '+cnt+'):&nbsp;</td>';
	tr     += '<td class="text-right"><img src="../../../../library/images/node_line.png"><input type="hidden" name="node_id'+cnt+'" id="node_id'+cnt+'" value=""><input type="text" style="width:'+txt_width+'%;" name="node_desc'+cnt+'" id="node_desc'+cnt+'" value=""></td>';
	tr     += '<td><img src="../../../../library/images/add_small.png" onclick="AddNode()"></td>';
	tr	   += '</tr>';
	$('#add_node_form').append(tr);
	$('#node_count').val(cnt);
	$('#nodeaction').val('add_node');
	if($('#node_de').hasClass('hide') === false){
		$('#node_de').addClass('hide');
	}
	
	if($('#node_ac').hasClass('hide') === true){
		$('#node_ac').removeClass('hide');
	}
	o = $('#add_node_form');
	if(o.height()>200){
		//o.css({'display':'table','height':'200px','overflow':'hidden','overflow-y':'scroll'});
		
	}
}

function delAllNodes(n_ids){
	$('#add_node_form').html('');
	$('#nodeaction').val('end_node');
	$('#node_count').val('0');
	if($('#node_de').hasClass('hide') === true){
		$('#node_de').removeClass('hide');
	}
	
	if($('#node_ac').hasClass('hide') === false){
		$('#node_ac').addClass('hide');
	}
	//alert(n_ids);
	ajaxURL = "ajax.php?del_ids="+n_ids;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {
		//alert(r);
	  }
	});
}

function showAddNode(mode){
	if(typeof(mode)=='undefined') mode=0;
	ode = $('#node_de');
	oac = $('#node_ac');
	if(mode==1){
		if(ode.hasClass('hide') === false){
			ode.addClass('hide');
		}
		
		if(oac.hasClass('hide') === true){
			oac.removeClass('hide');
		}
		AddNode();
	}
	else{
		if(confirm('Are you sure to delete the nodes?')){
			var cnt=$('#node_count').val();
				if(typeof(cnt)!='undefined'){
				var node_ids='';
				for(var i=1;i<=cnt;i++){
					if(i==1){node_ids+=$('#node_id'+i).val();
					}else{node_ids+=','+$('#node_id'+i).val();}
				}
			}
			if(oac.hasClass('hide') === false){
				oac.addClass('hide');
			}
			
			if(ode.hasClass('hide') === true){
				ode.removeClass('hide');
			}
			
			delAllNodes(node_ids);
		}
	}
}


var ar = [["add_new","Add New","top.fmain.addNew();"],["pos_del","Delete","top.fmain.deleteSelectet();"],["dx_cat","Manage Category","top.fmain.catgory();"],
	 ["dx_cat","Manage Laterality","top.fmain.laterality();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
LoadResultSet();
check_checkboxes();
set_header_title('ICD-10');
});
show_loading_image('none');
function catgory(){top.fmain.location.href = '../admin/billing/icd10/diag_categories.php';}
function laterality(){top.fmain.location.href = '../admin/billing/icd10/diag_laterality.php';}