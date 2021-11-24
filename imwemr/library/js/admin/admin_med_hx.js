var arrAllShownRecords = facilities = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('id','spl_id','ques','answer_type');
var strComboMedTab	   = '';
var strComboMedTab1	   = '';

function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Questions...');
	strComboMedTab = $('#strComboMedTab').val();
	strComboMedTab1 = $('#strComboMedTab1').val();
	
	if(typeof(s)!='string' || s==''){s = 'Active';}
	s_url = "&s="+s;
	
	if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
	if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
	
	f_strComboMedTab='&f_strComboMedTab='+strComboMedTab;
	f_strComboMedTab1='&f_strComboMedTab1='+strComboMedTab1;

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
			
	ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url+f_strComboMedTab+f_strComboMedTab1;
	$.ajax({
	  url: ajaxURL,
	  success: function(r) {
		showRecords(r);
	  }
	});
}

function showRecords(r){
	r = jQuery.parseJSON(r);
	result 		= r.records;
	arrCategories 	= r.arrCategories;

	h='';
	if(r != null){
		row = '';
		for(x in result){
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='spl_id' || y=='ques'){
					if(y=='spl_id'){
						if(tdVal>0){
							if(typeof(arrCategories[tdVal]) != "undefined")
							tdVal = arrCategories[tdVal]['name'];
						}else{ tdVal='All'; }
					}
					row	+= '<td class="leftborder alignLeft" style="padding-left:5px" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;
		}
		h = row;
	}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
	
	cat_options = '<option value="0">All</option>';
	for(x in arrCategories){
		cat_options += '<option value="'+x+'">'+arrCategories[x].name+'</option>';
	}			
	$('#spl_id').html(cat_options);
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#id').val(''); 
		$('#divAnswerOpts').html(defaultAnsOpt());
		document.add_edit_frm.reset();
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}	

function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	$('#id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.name,formObjects)){
			on	= o.name;
			v	= arrAllShownRecords[pkId][on];
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if (o.type == "checkbox" || o.type == "radio") {
					oid = on+'_'+v;
					$('#'+oid).attr('checked',true);
					if(v=='0'){
						document.getElementById("divShoOp").style.display = "none";
					}else{
						document.getElementById("divShoOp").style.display = "block";
					}
				} else if(o.type!='submit' && o.type!='button') {
					o.value = v;
				}
			}
		}
	}
	
	var id= document.getElementById('id').value;
	frm_data = 'question_id='+id;
	$.ajax({
		type: "POST",
		url: "answers.php",
		data: frm_data,
		success: function(d) {
			var arrData= d.split('~~');
			if(arrData[0]>0){
				$('#divAnswerOpts').html(arrData[1]);
				$('#totAnswerOpts').val(arrData[0]);
			}else{
				$('#divAnswerOpts').html(defaultAnsOpt());
			}
		}
	});		
}

function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update+&f_strComboMedTab='+strComboMedTab+'&f_strComboMedTab1='+strComboMedTab1;
	var msg="";
	if($.trim($('#ques').val())==""){
		msg+="&nbsp;&bull;&nbsp;Please Enter Question<br>";
	}
	
	if(msg){
		msg_val="<b>Please fill the following&nbsp;:-</b><br>"+msg;
		top.fAlert(msg_val);
		top.show_loading_image('hide');
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
	});
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

function showDivShoOp(op){
	if(op == "show"){
		document.getElementById("divShoOp").style.display = "block";
	}
	else if(op == "hide"){
		document.getElementById("divShoOp").style.display = "none";
	}
}

function showOptionAns(op){
	if(op == 'show'){
		document.getElementById("divAnsOpContainer").style.display = "block";
	}
	else if(op == 'hide'){
		document.getElementById("divAnsOpContainer").style.display = "none";
	}
}

function defaultAnsOpt(){
	var defaultAnsOpt='<div id="divTR1" class="row" style="margin-bottom:5px;"><div class="col-sm-1 text-center">1</div>';
	defaultAnsOpt+='<div class="col-sm-10">';
	defaultAnsOpt+='<input type="text" name="txtAnsOptionArr1" id="txtAnsOptionArr1" value="" class="form-control"/>';
	defaultAnsOpt+='</div>';
	defaultAnsOpt+='<div class=\"col-sm-1\">';
	defaultAnsOpt+=' <img src=\"../../../library/images/close_small.png\" id="imgDel1" name="imgDel1" style="display:none;" onClick="delAnsOpRow(this,\'1\');"/>';
	defaultAnsOpt+=' <img src=\"../../../library/images/add_small.png\" id="imgAdd1" name="imgAdd1" onClick="addAnsOpRow(this,document.getElementById(\'imgDel1\'), \'1\');"/>';
	defaultAnsOpt+='</div></div>';
	
	return defaultAnsOpt;
}

function addAnsOpRow(objAddImg, objDelImg, intCounter){
	if(objAddImg){
		objAddImg.style.display="none";
	}			
	if(objDelImg){
		objDelImg.style.display="block";	
	}
	
	var intCounterTemp = parseInt(intCounter) + 1;
	var divTrTag = document.createElement("div");
	divTrTag.id = "divTR" + intCounterTemp
	divTrTag.className = "row";
	divTrTag.style.marginBottom = "5px";
	
	var divTDTag1 = document.createElement("div");
	divTDTag1.className = "col-sm-1 text-center";
	divTDTag1.innerHTML = intCounterTemp;			
	divTrTag.appendChild(divTDTag1);
	
	var divTDTag2 = document.createElement("div");
	divTDTag2.className = "col-sm-10";
	
	var txtId = "txtAnsOptionArr"+intCounterTemp;
	
	var txtBox = document.createElement("input");
	txtBox.type = "text";
	txtBox.name = "txtAnsOptionArr"+intCounterTemp;
	txtBox.id = txtId;
	txtBox.value = "";
	txtBox.className = "form-control";
	divTDTag2.appendChild(txtBox);
	
	divTrTag.appendChild(divTDTag2);
	
	var divTDTag3 = document.createElement("div");
	divTDTag3.className = "col-sm-1";
	var imgDelId = "imgDel"+intCounterTemp;
	var imgAddId = "imgAdd"+intCounterTemp;
	var strImgHTML = " <img src=\"../../../library/images/close_small.png\" id=\""+imgDelId+"\" name=\""+imgDelId+"\" style=\"display:none;\" onClick=\"delAnsOpRow(this,'"+intCounterTemp+"');\"/>";
	strImgHTML += " <img src=\"../../../library/images/add_small.png\" id=\""+imgAddId+"\" name=\""+imgAddId+"\" onClick=\"addAnsOpRow(this,document.getElementById('"+imgDelId+"'), '"+intCounterTemp+"');\"/>";
				
	divTDTag3.innerHTML = strImgHTML;
	
	divTrTag.appendChild(divTDTag3);
	
	document.getElementById("divAnswerOpts").appendChild(divTrTag);
	document.getElementById("totAnswerOpts").value = intCounterTemp;
	document.getElementById(txtId).focus();
}

function delAnsOpRow(objDelImg, intCounter, intOpValueIdDB){
	intOpValueIdDB = intOpValueIdDB || 0;
	var divTrTag = "divTR" + intCounter;
	if(intOpValueIdDB > 0){				
		document.getElementById("hidId"+intCounter).value += intOpValueIdDB+'-';
	}
	if(document.getElementById(divTrTag)){
		var objMainDiv = document.getElementById("divAnswerOpts");
		objMainDiv.removeChild(document.getElementById(divTrTag));
	}
}
var ar = [["add_new","Add New","top.fmain.addNew();"],["pos_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	strComboMedTabTitle = strComboMedTab;
	if(strComboMedTab=="General Health" || strComboMedTab=="Ocular"){
		strComboMedTabTitle = strComboMedTab+' Questions';
	}
	set_header_title(strComboMedTabTitle);
});
show_loading_image('none');