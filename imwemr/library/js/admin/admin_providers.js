
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*
File: providers.js
Purpose: Define functions for providers, reports etc...
Access Type: Direct
*/
// JavaScript Document

var arrCoreLangMsg = [];
if( typeof vocabulary !== 'undefined' && vocabulary !== '' )
{
	arrCoreLangMsg[0] = vocabulary.pro_temp_type;
	arrCoreLangMsg[1] = vocabulary.fname;
	arrCoreLangMsg[2] = vocabulary.lname;
	arrCoreLangMsg[3] = vocabulary.npi_value_length;
	arrCoreLangMsg[4] = vocabulary.new_pass;
	arrCoreLangMsg[5] = vocabulary.confirm_pass;
	arrCoreLangMsg[6] = vocabulary.checkPassowrd;
	arrCoreLangMsg[7] = vocabulary.pro_pass_new;
	arrCoreLangMsg[8] = vocabulary.confirm_pass_new;
	arrCoreLangMsg[9] = vocabulary.delete_provider;
	arrCoreLangMsg[10] = vocabulary.alphaNum;
	arrCoreLangMsg[11] = vocabulary.proPassLength;
	arrCoreLangMsg[12] = vocabulary.proPassFnameLnameNot;
	arrCoreLangMsg[13] = vocabulary.voc_password_change_success;
	arrCoreLangMsg[14] = vocabulary.voc_password_recently_used;
	arrCoreLangMsg[15] = vocabulary.voc_password_exists;
	arrCoreLangMsg[16] = 'Confirm Password does not matches';
    arrCoreLangMsg[17] = vocabulary.voc_username_exists;
	arrCoreLangMsg[18] = " - Please Enter Privileges. <br\/>";
}


var ajax_file = top.JS_WEB_ROOT_PATH + "/interface/admin/providers/ajax.php";
function get_provider_listing(keyword,sort_term,field){    
	var srh_del_status = "";
	top.$('#del_provider').show();
	if(keyword == "show all"){
		document.getElementById("search_provider_name").value = "";
	}
	if(keyword == "deleted"){
		document.getElementById("search_provider_name").value = "";
		srh_del_status='&srh_del_status=1';
		top.$('#del_provider').hide();
	}
	var search_term = "";
	if(keyword == "search"){
		search_term = document.getElementById("search_provider_name").value;
		$("show_prov_drop").selectpicker('val','show all');
	}
	
	var url_dt = ajax_file+"?req=listing";
	if(search_term != "" && search_term != "Search..."){
		url_dt += "&search_term="+search_term;                                
	}else{
		document.getElementById("search_provider_name").value = "";
		url_dt += srh_del_status;  
	}
	
	oso		= $('#ord_by_field').val(); //old_so
	sort_type	= $('#ord_by_ascdesc').val();
	if(typeof(sort_term)=='undefined' || sort_term==''){
		sort_term 		= $('#ord_by_field').val();
	}else{
		$('#ord_by_field').val(sort_term);
		if(oso==sort_term){
			if(sort_type=='ASC') sort_type = 'DESC';
			else  sort_type = 'ASC';
		}else{
			sort_type = 'ASC';
		}
		$('#ord_by_ascdesc').val(sort_type);
	};
	url_dt+='&sort_term='+sort_term+'&sort_type='+sort_type;
	$('th').find('span').removeAttr('class');
	
	if(sort_type=='ASC') $(field).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
	else $(field).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
	
	$.ajax({url:url_dt,type:'POST',beforeSend: function(){top.show_loading_image('show');},complete:function(r){top.show_loading_image('hide');},success:function(r){$("#result_set").html(r);}});
	
}

function lock_account(int_action, int_user_id){
	var url = ajax_file+"?req=lockunlock&user_id="+int_user_id+"&doaction="+int_action;
	$.ajax({type: "POST",url: url,success: function(r){get_provider_listing();}});
}

function changepasswords(id){
	var url = ajax_file+"?req=resetPassword&myid="+id;
	$.ajax({type: "POST",url: url,success: function(r){ $("#reset_password").html(r); $("#reset_password_div").modal('show');var btn_array = [['Save','','top.fmain.resetPassSave();']];
	top.fmain.set_modal_btns('reset_password_div .modal-footer',btn_array); }});
}

function directCredentials(id){
	id = id || 0;
	id= parseInt(id);
	if(id <= 0 ) return ;
	
	var url = ajax_file+"?req=directCred&myid="+id;
	$.ajax({type: "POST",url: url,success: function(r){ $("#direct_credentials").html(r); $("#direct_credentials_div").modal('show');var btn_array = [['Save','','top.fmain.directCredSave();']];
	top.fmain.set_modal_btns('direct_credentials_div .modal-footer',btn_array);$('.selectpicker').selectpicker('refresh'); }});
}

function directCredSave(){
	var _form =	$("#direct_cred_form");
	var url = ajax_file+'?req=directCredSave';
	$.ajax({
		url : url,
		type:'POST',
		data:_form.serialize(),
		beforeSend: function(){
			top.show_loading_image('show');
		},
		complete:function(r){
			top.show_loading_image('hide');
		},
		success: function(r){
			if(r) top.fAlert(r);
			$("#direct_credentials_div").modal('hide');
			$("#direct_credentials_div").remove();
		}
	});
}

function zeissCredentials(id){
	id = id || 0;
	id= parseInt(id);
	if(id <= 0 ) return ;
	
	var url = ajax_file+"?req=zeissCred&myid="+id;
	$.ajax({type: "POST",url: url,success: function(r){ $("#zeiss_credentials").html(r); $("#zeiss_credentials_div").modal('show'); }});
}

function zeissCredSave()
{
	var _form =	$("#zeiss_cred_form");
	var url = ajax_file+'?req=zeissCredSave';
		$.ajax({
			url : url,
			type:'POST',
			data:_form.serialize(),
			beforeSend: function(){
				top.show_loading_image('show');
			},
			complete:function(r){
				top.show_loading_image('hide');
			},
			success: function(r){
				if(r) top.fAlert(r);
				$("#zeiss_credentials_div").remove();
			}
		});	
	
}

function load_provider(pro_id,srh_del_status){
	var srh_del_status_inc="";
	if(srh_del_status>0){
		srh_del_status_inc = "srh_del_status=1&";
	}
	document.location.href='index.php?'+srh_del_status_inc+'pro_id='+pro_id;
}

function show_reset_alert(jsAlertNo){	
	var fancyMsg = "";
	fancyMsg = arrCoreLangMsg[jsAlertNo];	
	
	top.fAlert(fancyMsg)
	if(jsAlertNo == 13){
		$('#reset_password_div').modal('hide').remove();
	}
	
}

function check_reset_password(obj_password,obj_reset_password){
	if(obj_password && obj_password){
		var password_txt=$('#'+obj_password);
		var password_reset_txt=$('#'+obj_reset_password);
	}
	var str=password_txt.val();
	var charExists=numericExists=false;
	for(var i=0; i<password_txt.val().length; i++){
		var strChar = str.charAt(i);
		if(isNaN(strChar)){
			charExists = true;
		}else{
			numericExists = true;
		}
	}
	if(password_txt.val().length<8){
		show_reset_alert(11);
		return false;
	}else if(password_txt.val()!=password_reset_txt.val()){
		show_reset_alert(16);
		return false;
	}else if(charExists==false || numericExists==false){
		show_reset_alert(10);
		return false;
	}
	
	if(password_txt.val()){
		var pro_pass=password_txt.val();
		var pro_pass_enc=$("#pro_pass_old").val();
		var json_password_div = JSON.parse(document.getElementById("json_password_div").innerHTML);
		if(json_password_div[pro_pass_enc] && pro_pass_enc){
			//alert("This password is not available. Please choose another.");
			show_reset_alert(15);
			password_txt.focus();
			return false;
		}
	}
	
	encode_pwd(password_txt,password_reset_txt);
	hashed_val = password_txt.val();
	if(hashed_val.length!=32 && hashed_val.length!=64){
		alert('Password encryption failed. Security exception. Can\'t proceed.');
		return false;	
	}
	
	return true;
}

function encode_pwd(password_txt,password_reset_txt){
	if (hAlg == "MD5")	password_txt.val(md5(password_txt.val()));
	else password_txt.val(top.Sha256.hash(password_txt.val()));
	password_reset_txt.val(password_txt.val());
}

function countCharResFn(obj, prevPass, id){
	var str = obj.value;
	var len = str.length;
	var letterExists ='';
	var specialCharExists ='';
	var numericExists ='';
	for(var i=0; i<len; i++){
		var strChar = str.charAt(i);
		if(isLetter(strChar)) {
			letterExists = "true";		
		}else if(isNaN(strChar)){
			specialCharExists = "true";
		}else{
			numericExists = "true";
		}
	}	
	if(str!=""){
		if((letterExists != "true") || (specialCharExists != "true") || (numericExists != "true")){
			var tempId = obj.id;
			//alert("Must contain alphanumeric characters.");
			obj.value="";
			show_reset_alert(10);
			//obj.focus();
			return false;		
		}
		
		if(len<8){
			var tempId = obj.id;
			//alert("Must be at least 8 characters long.");
			show_reset_alert(11);
			//obj.focus();
			return false;
		}
		
		password_encode(str);
		return true;
	}
}

function password_encode(pass_val){
	var url = ajax_file + "?req=password_encode&get_password_text="+pass_val;
	$.ajax({type: "POST",url: url,success: function(r){ $("#pro_pass_old").val(r);}});
}

function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}

function resetPassSave()
{
	if(check_reset_password('pro_pass_new','confirm_pass_new'))
	{
		var _form =	$("#reset_pass_form");
		var url = ajax_file+'?req=resetPasswordSave';
		$.ajax({
			url : url,
			type:'POST',
			data:_form.serialize(),
			beforeSend: function(){
				top.show_loading_image('show');
			},
			complete:function(r){
				top.show_loading_image('hide');
			},
			success: function(r){
				show_reset_alert(r);
			}
		});
	}
	
	return false;
}

function init_providers()
{
	$("select .selectpicker").selectpicker();
	$('body').on('keyup','#search_provider_name',function(event){
		if(event.keyCode == '13' )
			get_provider_listing('search')	
	});
	var ar = [["add_provider","Add New","top.fmain.add_new_form();"],
						["del_provider","Delete","return top.fmain.delete_provider();"]];
	top.btn_show("ADMN",ar);
}

//to swap the layout divs color
function change_layout_color(div_id, marker_id, retain_mode){
	arr_blocks = new Array('fac_div','Schedule_div','privilege_div','ID_div');
	section_highlight(div_id,arr_blocks,retain_mode);
}

function checkdata(){
	SetSig();
	var focus_on_this = "";
	var pass = false;
	var conf = false;
	var msg = "";
	var fname = trimStr(document.getElementById("pro_fname").value);
	var lname = trimStr(document.getElementById("pro_lname").value);
	var pro_temp_type_bl = false;
	var fname_bl = false;
	var lname_bl = false;
	var pro_temp_type = document.getElementById("pro_type").value;
	var pro_type = "";
	if(pro_temp_type != ""){
		arr_pro_type = pro_temp_type.split("-");
		pro_type = arr_pro_type[0];
	}else{
		msg = arrCoreLangMsg[0];
		
		pro_temp_type_bl = true;
		if(focus_on_this == ""){
			focus_on_this = "pro_type";
		}
	}
		
	if(fname == ""){
		//msg += "•   Please enter First Name.\n";
		msg += arrCoreLangMsg[1];		
		fname_bl = true;
		if(focus_on_this == ""){
			focus_on_this = "pro_fname";
		}
	}
	if(lname == ""){
		//msg+="•   Please enter Last Name.\n";
		msg += arrCoreLangMsg[2];		
		lname_bl = true;
		if(focus_on_this == ""){
			focus_on_this = "pro_lname";
		}
	}
	
	//Privileges
	var flg_prvlgs = parseInt($("#ele_priv_chk").find("div.checkbox").find(":checked[id!=priv_chart_finalize]").length) +  //.not("div.checkboxcolor")
					parseInt($("#allprivdivs").find("div.checkbox").find(":checked").length);	
	if(flg_prvlgs<=0){
		msg += arrCoreLangMsg[18];
	}
	
	var npi_value = document.getElementById("user_npi").value;
	var imwk_value = document.getElementById("imw_key");
	if(pro_type == 1 || pro_type == 11 || pro_type == 12 ||  pro_type==21 || pro_type==7 || npi_value!=''){
		//var default_group = document.getElementById("default_group").value;
		
		//if(default_group == ""){
		//	msg += '•   Please Select Default Group\n';
		//}
		if(npi_value.length < 10){
			//msg += '- Please enter NPI# as exactly 10 characters.\n<br>';
			msg += arrCoreLangMsg[3];
			if(focus_on_this == ""){
				focus_on_this = "user_npi";
			}
		}
		if(imwk_value && $.trim(imwk_value.value)=="" && imwk_value.style.visibility!="hidden"){
			msg += '- Please enter Key.<br>';	
		}
	}
	if(document.getElementById("pro_user").value != ""){
		if(document.getElementById("old_pro_user").value!=document.getElementById("pro_user").value){
			var pro_user= document.getElementById("pro_user").value;
		
			var json_username_div = JSON.parse(document.getElementById("json_username_div").innerHTML);
			if(json_username_div[pro_user]){
				msg = arrCoreLangMsg[17];
				if(focus_on_this == ""){
					focus_on_this = "pro_user";
				}
			}
		}
	}else msg += '- Please enter Username.<br>';
	
	if(document.getElementById("pro_pass_news")){
		var pro_pass=document.getElementById("pro_pass_news").value;
		var pro_pass_enc=$("#pro_pass_old").val();
		var json_password_div = JSON.parse(document.getElementById("json_password_div").innerHTML);
		if(json_password_div[pro_pass_enc] && pro_pass_enc){
			msg = "This password is not available. Please choose another.";
			focus_on_this = "pro_pass_news";
		}
	}

	if(document.getElementById("user_npi").value != ""){
		if(document.getElementById("user_npi_old").value!=document.getElementById("user_npi").value){
			var pro_user_npi= document.getElementById("user_npi").value;
			var json_usernpi_div = JSON.parse(document.getElementById("json_usernpi_div").innerHTML);
			if(json_usernpi_div[pro_user_npi]){
				msg = "NPI number is already assigned to other user. Please enter correct NPI number.";
			}
			if(focus_on_this == ""){
				focus_on_this = "pro_user";
			}
		}
	}
		
	if(document.getElementById("pro_id").value == "" && document.getElementById("pro_user").value != ""){
		var new_pass = document.getElementById("pro_pass_news").value;
		var confirm_pass = document.getElementById("confirm_pass_new").value;
		if(new_pass == ""){
			//msg += "•   Please Enter Password\n";
			msg += arrCoreLangMsg[4];
			if(focus_on_this == ""){
				focus_on_this = "pro_pass_news";
			}
			pass=true;
		}else if(confirm_pass==""){
			//msg += "•   Please Confirm Password\n"
			msg += arrCoreLangMsg[5];
			if(focus_on_this == ""){
				focus_on_this = "confirm_pass_new";
			}
			conf=true;
		}
	}	
	if(typeof(msg)=="undefined" || msg == '' || msg == 'undefined'){
		if(document.getElementById("pro_id").value == ""){
			if(pass){
				countCharFn(document.getElementById("pro_pass_news"), '', '');
			}
			if(confirm_pass){
				if(checkPassowrd("pro_pass_news", "confirm_pass_new")){
					confChecked = true;	
				}else{
					return false;	
				}
			}
		}
		document.getElementById("selected_sch_facs").value = $("#sch_fac_id").val();
		save_provider_ajx();
		//document.frmprovider.submit();
	}else{
		top.show_loading_image('none');
		if(pro_temp_type_bl == true){
			document.getElementById("pro_type").className = "selectpicker form-control mandatory";
		}
		if(fname_bl == true){
			document.getElementById("pro_fname").className = "form-control mandatory";
		}
		if(lname_bl == true){
			document.getElementById("pro_lname").className = "form-control mandatory";
		}
		//alert (msg);
		top.fAlert(msg);
		msg='';		
		if(focus_on_this != ""){
			document.getElementById(focus_on_this).focus();
		}
		return false;
	}
}

function checkPassowrd(pass_field, pass_confirm_field){
	var new_pass = document.getElementById(pass_field).value;
	var confirm_pass = document.getElementById(pass_confirm_field).value;
	
	if(new_pass != confirm_pass){
        if(confirm_pass==''){
            top.fAlert(arrCoreLangMsg[5]);
        } else {
            top.fAlert(arrCoreLangMsg[16]);
        }
		return false;		
	}
	return true;
}

function change_input_class(obj){
	if(obj.value == "") $(obj).addClass('mandatory');
	else $(obj).removeClass('mandatory');
}

function add_new_form(){
	top.fmain.location.href = top.JS_WEB_ROOT_PATH + "/interface/admin/providers/index.php?add_new=yes";
}

function scanPatientImage(pro_id){
	top.popup_win(top.JS_WEB_ROOT_PATH + "/interface/admin/providers/webcam/flash.php?provider_id="+pro_id,"proImg","toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=630,left=150,top=60");
}

function do_scroll(){
	var hashVal = window.location.hash.substr(1);$('#div_provider_listing').scrollTop(hashVal);clearInterval(a);
	pro_id = js_GET('pro_id');if(pro_id){pro_id = 'p'+pro_id;$('#'+pro_id).css('background-color','#9F3');}
}

function scheduler_settings_display(obj_name){
	if(obj_name.checked == true){
		show_hide_scheduler_opts("enabled");
	}	
	if(obj_name.checked == false){
		show_hide_scheduler_opts("disabled");
	}
}

function show_hide_scheduler_opts(str_scheduler_status){
	if(str_scheduler_status == "enabled"){
		document.getElementById('Enable_Scheduler').checked = true;
		for(i = 3; i <= 10; i++){
			if(document.getElementById('EnableOpt'+i))
			document.getElementById('EnableOpt'+i).style.visibility = '';
		}
	}else{
		document.getElementById('Enable_Scheduler').checked = false;
		for(i = 3; i <= 10; i++){
			if(document.getElementById('EnableOpt'+i))
			document.getElementById('EnableOpt'+i).style.visibility = 'hidden';
		}
	}
}

function trimStr(input_string){
	return input_string.replace(/^\s+|\s+$/g,'');
}

// Signature Function
function SetSig(){
	if(document.getElementById("SigPlus1")){
		document.getElementById("SigPlus1").SigCompressionMode=1;
		if(document.getElementById("SigData1")) {
			document.getElementById("SigData1").value=document.getElementById("SigPlus1").SigString;
		}
	}
	return true;
}			

// Applet Signature	- Applet Function
function getAssessmentSign(num,n,coords,sdata,simg)
{
	if(typeof(num) == "undefined"){num=1;}	
	var v1="td_signature_applet"+num;
	var v2="dv_ShowSign"+num;
	//var v3="elem_signCoords"+num;
	//var v4="hdSignCoordsOriginal"+num;
	var v5="elem_sign_path"; //"elem_sign"+num; 	
	
	if(typeof(n) == "undefined")
	{
		var w = 491;
		var h = 125;
		var opd= 0; 
		var tmp = $('#'+v1).position();
		if($("#"+v2).length<=0)
		{
			var final_flg = 0;	
			var strpixls="";
			var img =  "<iframe library/=\"0\" id=\"ifrm_signApp"+num+"\" src=\"signApplet.php?final_flg="+final_flg+"&signType="+num+"\" border=\"0\" height=\"100%\" width=\"100%\" scrolling=\"0\" frameborder=\"0\"></iframe>";
			var str = "<div id=\""+v2+"\" style=\"position:absolute;width:"+w+"px;height:"+h+"px;background-color:white;border:1px solid black;z-Index:2;overflow:hidden;\">"+
			img+"</div>";
			$('#'+v1).append(str);
		}else{
			$("#"+v2).show();
		}
		$("#"+v2).css({"left":tmp.left,"top":opd+tmp.top});
	}
	else if(n==1){			
		var fid = 0;
		$('#'+v1+' img').remove();
		$("#"+v2).hide();
		var final_flg = 0;
		var proId=""+$("#pro_id").val();
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,""+v1);		
		$.post('signApplet.php',{ 'elem_formAction':'GetSign','strpixls':''+coords+'','fid':''+fid,'signType':num,'final_flg':final_flg,'proId':proId,'sData':sdata,'sImg':simg},function(data){ 
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,""+v1);					
				if(data!=''||data!='0'){ 
				var u = "../../..";
				$('#'+v1).append("<img src=\""+u+"/data/"+top.practice_dir+data+"\" alt=\"sign\" style=\"width:225px; height:60px;\" >");
				$('input[name='+v5+']').val(data);
			}  });		
	}
	else if(n==2){
		$("#"+v2).hide();
	}else if(n==3){ //Clear Sign			
		/*26-11 : Del of signature should simply erase the signature not delete the entire signature box.*/
		if(confirm("Are you sure to delete this signature?"))
		{					
			if($('#'+v1+' img').length>0){
				$('#'+v1+' img').remove();
				$('input[name='+v5+']').val("");
			}
		}
	}
}

//===================IPAD SIGNATURE WORK FOR ADMIN-PROVIDER TAB STARTS HERE===================
function OnSignIpadPhy(patient_id,sigFor,idInnerHTML,signSeqNum){
	window.open(top.JS_WEB_ROOT_PATH + "/interface/common/chartNoteSignatureIPad.php?patient_id="+patient_id+"&sigFor="+sigFor+"&idInnerHTML="+idInnerHTML+"&signSeqNum="+signSeqNum, "chartNoteSignature"); 
	//ONCLICK OF IPAD ICON, IT REDIRECT TO INTERFACE/chart_notes/CHARTNOTESIGNATURE.PHP FILE, WHERE SIGNATURE WILL CREATE AND THEIR SAVING PATH SET WHERE SOFTCOPY OF SIGNATURE WILL BE SAVED.
}
function image_DIV(imageSrc,div,id,seqNum){
	//console.log(imageSrc);console.log(div);console.log(id);console.log(seqNum);
	id = id || '';
	seqNum = seqNum || '';
	if(imageSrc){
		if(imageSrc.trim() != "") {			
			if(div == "adminProvider"){  //IS THE CASE MADE IN CHARTNOTESIGNATUREIPAD FILE FOR ADMIN PROVIDER TAB WORK
				if(typeof(document.getElementById('elem_sign_path')) != "undefined") {
					document.getElementById('elem_sign_path').value=imageSrc; //HIDDEN ID SEND FROM LOGIC.PHP FILE TO POST THE CREATED SIGNATURE PATH THROUGH FORM FOR DISPLAY/SAVING/UPDATING SIGNATURE PATH INTO DATABASE PURPOSES
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<img src='../../../data/"+top.practice_dir+"/"+imageSrc+"' style='height:60px;width:225px;'>");	//CREATED SIGNATURE IMAGE WHICH WILL BE DISPLAYED INTO PROVIDER SIGNATURE BOX
					
				}
			}
			else if(div == "pro_image")
			{
				var h = "<img src="+imageSrc+" class=\"img-responsive img-thumbnail\" width='80' height='60'>"
				$("#pro_image").html('');
				$("#pro_image").html(h);
			}
		}
	}
} 
//===================IPAD SIGNATURE WORK FOR ADMIN-PROVIDER TAB END HERE===================

function select_priv(){
	var collection = document.getElementById('ele_priv_chk').getElementsByTagName('INPUT');
	var grant="";y=1;title_val="";
	for (var x=1; x<collection.length; x++) {
		if ((collection[x].type.toUpperCase()=='CHECKBOX') && ((collection[x].checked)==true) && (typeof(collection[x].title)!="undefined")){
			title_val=collection[x].title;
			if(title_val && typeof(title_val)!='undefined'){
				if(y==1){
					grant+=title_val;
				}else{
					grant+=", "+title_val;
				}
				y++;
			}
		}
	}
	if($('#priv_grant') && grant){
		//$('#priv_grant').html(grant);
	}
}

function change_form_values(user_details)
{
	$("#follow_physician_div").hide();
	if(document.getElementById("pro_type").value == ""){
		$("#pro_type").selectpicker('setStyle', 'btn-warning','add').selectpicker('setStyle', 'btn-default','remove');
	}else{
		$("#pro_type").selectpicker('setStyle', 'btn-default','add').selectpicker('setStyle', 'btn-warning','remove');
	}
    if($("#pro_type option:selected").text()=='Resident') {
        $('.chart_final').removeClass('invisible').addClass('visible');
    } else {
        $('.chart_final').removeClass('visible').addClass('invisible');
    }
	unset_privileges();
	if(user_details != ""){
		set_privileges(user_details);
	}else{
		show_hide_scheduler_opts("disabled");
		show_hide_id_opts("enabled");
	}
	
}

function show_hide_id_opts(str_id_status){
	//#external_id
	var id = $("#user_npi,#pro_upin,#MedicareId,#TaxonomyId,#pro_tax,#MedicaidId,#pro_drug,#pro_lic,#group_name");
	if(str_id_status == "enabled"){
		id.prop('disabled',false);
		if(document.getElementById('imw_key')){$('#imw_key').prop('disabled',false);}
	}else{
		id.val('').prop('disabled',true);
		if(document.getElementById('imw_key')){$('#imw_key').prop('disabled',true);}
	}
	$("#group_name").selectpicker('refresh');
}

function set_privileges(user_details)
{
	var arr_user_details = user_details.split("-");
	var user_type_id = arr_user_details[0];
	var arr_user_priv = arr_user_details[1].split(";");
	var str_scheduler_status = arr_user_details[2];
	selectDeselect_all_admin("el_main_priv",false,"rep");
	for(i = 0; i < arr_user_priv.length; i++){
		var tmp_name = arr_user_priv[i];
		var arr_name = tmp_name.split(":");
		var chk_name = arr_name[0];
		var chk_val = arr_name[1];
		if(chk_val == 1){
			document.getElementById(chk_name).checked = true;
			if(chk_name=="priv_all_settings"){
				selectDeselect_all_admin("el_main_priv",true,"rep");
			}
		}	
	}
	
	var val_to_set_clinical = 0;
	if(document.getElementById("priv_cl_work_view").checked == true){
		val_to_set_clinical++;
	}
	if(document.getElementById("priv_cl_tests").checked == true){
		val_to_set_clinical++;
	}
	if(document.getElementById("priv_cl_medical_hx").checked == true){
		val_to_set_clinical++;
	}
	if(document.getElementById("priv_cl_work_view").checked == true && document.getElementById("priv_cl_tests").checked == true && document.getElementById("priv_cl_medical_hx").checked == true){
		//document.getElementById("priv_clinical").checked = true;
	}
	if(document.getElementById("sp_Clinical")){
		document.getElementById("sp_Clinical").innerHTML = val_to_set_clinical++;;
	}
	var val_to_set_reports = 0;
	if(document.getElementById("priv_sc_scheduler").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_sc_house_calls").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_sc_recall_fulfillment") && document.getElementById("priv_sc_recall_fulfillment").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_bi_front_desk") && document.getElementById("priv_bi_front_desk").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_bi_ledger") && document.getElementById("priv_bi_ledger").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_bi_prod_payroll") && document.getElementById("priv_bi_prod_payroll").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_bi_ar") && document.getElementById("priv_bi_ar").checked == true){
		val_to_set_reports++;
	}
	/*if(document.getElementById("priv_bi_statements").checked == true){
		val_to_set_reports++;
	}*/
	if(document.getElementById("priv_bi_end_of_day") && document.getElementById("priv_bi_end_of_day").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_cl_clinical").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_cl_visits") && document.getElementById("priv_cl_visits").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_cl_ccd").checked == true){
		val_to_set_reports++;
	}
	if(document.getElementById("priv_cl_order_set") && document.getElementById("priv_cl_order_set").checked == true){
		val_to_set_reports++;
	}	
	if(document.getElementById("priv_sc_scheduler").checked == true && document.getElementById("priv_sc_house_calls").checked == true && document.getElementById("priv_sc_recall_fulfillment").checked == true && document.getElementById("priv_bi_front_desk").checked == true && document.getElementById("priv_bi_ledger").checked == true && document.getElementById("priv_bi_prod_payroll").checked == true && document.getElementById("priv_bi_ar").checked == true && document.getElementById("priv_bi_end_of_day").checked == true && document.getElementById("priv_cl_clinical").checked == true && document.getElementById("priv_cl_visits").checked == true && document.getElementById("priv_cl_ccd").checked == true && document.getElementById("priv_cl_order_set").checked == true){
		document.getElementById("priv_Reports").checked = false;
	}
	if(document.getElementById("sp_Reports"))
		document.getElementById("sp_Reports").innerHTML = val_to_set_reports;
	
	var val_to_set_vo = 0;
	if(document.getElementById("priv_vo_clinical").checked == true){
		val_to_set_vo++;
	}
	if(document.getElementById("priv_vo_pt_info").checked == true){
		val_to_set_vo++;
	}
	if(document.getElementById("priv_vo_acc"))
	{
		if(document.getElementById("priv_vo_acc").checked == true){
			val_to_set_vo++;
		}
	}
	if(document.getElementById("priv_vo_clinical").checked == true && document.getElementById("priv_vo_pt_info").checked == true && document.getElementById("priv_vo_acc").checked == true){
		document.getElementById("priv_View_Only").checked = true;
	}
	if(document.getElementById("sp_View_Only"))
		document.getElementById("sp_View_Only").innerHTML = val_to_set_vo;

	if(user_type_id == 1 || user_type_id == 11 || user_type_id == 12 || user_type_id == 19 ||  user_type_id == 21 || user_type_id==7 || user_type_id==9){
		show_hide_id_opts("enabled");	
	}else{
		show_hide_id_opts("disabled");	
	}
	document.getElementById('erx_chk').checked=true;
	//if(document.getElementById('privilege_cdc'))document.getElementById('privilege_cdc').disabled=true;
	if(document.getElementById('privilege_cdc'))document.getElementById('privilege_cdc').checked=false;
	if(user_type_id == 1 ||user_type_id == 2 || user_type_id == 11 ||user_type_id == 12 ||  user_type_id == 19 ||  user_type_id == 21 || user_type_id==9){
		//if(document.getElementById('privilege_cdc'))document.getElementById('privilege_cdc').disabled=false;
	} $("#follow_physician_div").hide();
	if(user_type_id == 3 ||user_type_id == 13){
		$("#follow_physician_div").show();
	}
	/*
	var iolink_name = "priv_iOLink";
	if(document.getElementById(iolink_name)){
		if(user_type_id == 6){
			document.getElementById(iolink_name).disabled = false;
		}else{
			document.getElementById(iolink_name).disabled = true;
		}
	}
	*/
	show_hide_scheduler_opts(str_scheduler_status);
}

function unset_privileges()
{
	var val_to_set = 0;
	if(document.getElementById("priv_clinical"))
		document.getElementById("priv_clinical").checked = false;
	document.getElementById("priv_cl_work_view").checked = false;
	document.getElementById("priv_cl_tests").checked = false;
	document.getElementById("priv_cl_medical_hx").checked = false;
	if(document.getElementById("sp_Clinical"))
		document.getElementById("sp_Clinical").innerHTML = val_to_set;
	if(document.getElementById("priv_Front_Desk")){
		document.getElementById("priv_Front_Desk").checked = false;
	}
	document.getElementById("priv_Billing").checked = false;

	document.getElementById("priv_Accounting").checked = false;
	
	document.getElementById("priv_Security").checked = false;
	
	if(document.getElementById("priv_Reports")){document.getElementById("priv_Reports").checked = false;}
	document.getElementById("priv_sc_scheduler").checked = false;
	document.getElementById("priv_sc_house_calls").checked = false;
	if(document.getElementById("priv_sc_recall_fulfillment"))document.getElementById("priv_sc_recall_fulfillment").checked = false;
	if(document.getElementById("priv_bi_front_desk"))document.getElementById("priv_bi_front_desk").checked = false;
	if(document.getElementById("priv_bi_ledger"))document.getElementById("priv_bi_ledger").checked = false;
	if(document.getElementById("priv_bi_prod_payroll"))document.getElementById("priv_bi_prod_payroll").checked = false;
	if(document.getElementById("priv_bi_ar"))document.getElementById("priv_bi_ar").checked = false;
	//document.getElementById("priv_bi_statements").checked = false;
	if(document.getElementById("priv_bi_end_of_day"))document.getElementById("priv_bi_end_of_day").checked = false;
	document.getElementById("priv_cl_clinical").checked = false;
	if(document.getElementById("priv_cl_visits"))document.getElementById("priv_cl_visits").checked = false;
	document.getElementById("priv_cl_ccd").checked = false;
	if(document.getElementById("priv_cl_order_set"))document.getElementById("priv_cl_order_set").checked = false;
	if(document.getElementById("sp_Reports"))
		document.getElementById("sp_Reports").innerHTML = val_to_set;

	if(document.getElementById("priv_View_Only")){document.getElementById("priv_View_Only").checked = false;}
	document.getElementById("priv_vo_clinical").checked = false;
	document.getElementById("priv_vo_pt_info").checked = false;
	if(document.getElementById("priv_vo_acc"))
		document.getElementById("priv_vo_acc").checked = false;
	if(document.getElementById("sp_View_Only"))
		document.getElementById("sp_View_Only").innerHTML = val_to_set;

	document.getElementById("priv_Sch_Override").checked = false;
	if(document.getElementById("priv_pt_Override"))
		document.getElementById("priv_pt_Override").checked = false;

	document.getElementById("priv_admin").checked = false;
	document.getElementById("priv_all_settings").checked = false;

	document.getElementById("priv_Optical").checked = false;

	document.getElementById("priv_iOLink").checked = false;
	document.getElementById("erx_chk").checked = false;
}

function set_admin_privileges()
{
	
	//if(document.getElementById("priv_admin").checked == true){
	if(document.getElementById("priv_all_settings").checked == true){
		store_privileges();

		document.getElementById("priv_admin").checked = true;
		document.getElementById("priv_clinical").checked = true;
		document.getElementById("priv_cl_work_view").checked = true;
		document.getElementById("priv_cl_tests").checked = true;
		document.getElementById("priv_cl_medical_hx").checked = true;
		if(document.getElementById("sp_Clinical"))
			document.getElementById("sp_Clinical").innerHTML = 3;

		document.getElementById("priv_Front_Desk").checked = true;

		document.getElementById("priv_Billing").checked = true;
		document.getElementById("priv_edit_financials").checked = true;

		document.getElementById("priv_Accounting").checked = true;
		
		document.getElementById("priv_Security").checked = true;
		
		document.getElementById("priv_Reports").checked = true;
		document.getElementById("priv_sc_scheduler").checked = true;
		document.getElementById("priv_sc_house_calls").checked = true;
		document.getElementById("priv_sc_recall_fulfillment").checked = true;
		document.getElementById("priv_bi_front_desk").checked = true;
		document.getElementById("priv_bi_ledger").checked = true;
		document.getElementById("priv_bi_prod_payroll").checked = true;
		document.getElementById("priv_bi_ar").checked = true;
		//document.getElementById("priv_bi_statements").checked = true; 
		document.getElementById("priv_bi_end_of_day").checked = true;
		document.getElementById("priv_cl_clinical").checked = true;
		document.getElementById("priv_cl_visits").checked = true;
		document.getElementById("priv_cl_ccd").checked = true;
		document.getElementById("priv_cl_order_set").checked = true;
		document.getElementById("erx_chk").checked = true;
		
		if(document.getElementById("sp_Reports"))
			document.getElementById("sp_Reports").innerHTML = 13;

		document.getElementById("priv_View_Only").checked = false;
		document.getElementById("priv_vo_clinical").checked = false;
		document.getElementById("priv_purge_del_chart").checked = false;
		document.getElementById("priv_record_release").checked = false;
		document.getElementById("priv_vo_pt_info").checked = false;
		if(document.getElementById("priv_vo_acc"))
			document.getElementById("priv_vo_acc").checked = false;
		if(document.getElementById("sp_View_Only"))
			document.getElementById("sp_View_Only").innerHTML = 0;

		document.getElementById("priv_Sch_Override").checked = true;
		if(document.getElementById("priv_pt_Override"))
		document.getElementById("priv_pt_Override").checked = true;

		document.getElementById("priv_Optical").checked = true;
		document.getElementById("priv_pt_coordinate").checked = true;
		
	}else{
		unset_admin_privileges();
	}
}

function unset_admin_privileges()
{	
	if(stored_priv_details != ""){
		unset_privileges();
		var arr_stored_priv_details = stored_priv_details.split(",");
		for(i = 0; i < arr_stored_priv_details.length-1; i++){
			var str_priv_name = arr_stored_priv_details[i];
			document.getElementById(str_priv_name).checked = true;
		}
		increment_select_number("sp_Clinical");
		increment_select_number("sp_Reports");
		increment_select_number("sp_View_Only");
		unstore_privileges();
	}
}

function countCharFn(obj, prevPass, id){
	
	if(document.getElementById('pro_fname')){
		var userFname = document.getElementById('pro_fname').value;
	}else{
		var userFname = "";
	}
	if(document.getElementById('pro_lname')){
		var userLname = document.getElementById('pro_lname').value;
	}else{
		var userLname = "";
	}
	if(document.getElementById('pro_user')){
		var loginName = document.getElementById('pro_user').value;	
	}else{
		var loginName = "";
	}
	var str = obj.value;
	var len = str.length;
	var letterExists ='';
	var specialCharExists ='';
	var numericExists ='';
	for(var i=0; i<len; i++){
		var strChar = str.charAt(i);
		if(isLetter(strChar)) {
			letterExists = "true";		
		}else if(isNaN(strChar)){
			specialCharExists = "true";
		}else{
			numericExists = "true";
		}
	}
	if(str!=""){
		if((letterExists != "true") || (specialCharExists != "true") || (numericExists != "true")){
			var tempId = obj.id;
			obj.value="";
			top.fAlert(arrCoreLangMsg[10],"",top.fmain.document.getElementById(""+tempId+""));
			return false;		
		}
		
		if(len<8){
			var tempId = obj.id;
			top.fAlert(arrCoreLangMsg[11],"",top.fmain.document.getElementById(""+tempId+""));
			return false;
		}
		if(document.getElementById('pro_fname')){
			if((str==userFname && userFname != "") || (str==userLname && userLname != "") || (str==loginName && loginName != "")){
				var tempId = obj.id;
				top.fAlert(arrCoreLangMsg[12],"",top.fmain.document.getElementById(""+tempId+""));
				return false;
			}
		}
		password_encode(str);
		return true;
	}
}

function selectDeselect(aId, aChecked1)
{
	var collection = document.getElementById(aId).getElementsByTagName('INPUT');
	if(document.getElementById('selDes').checked==true){
		aChecked=true;
	}else {
		aChecked=false;
	}
	
	for (var x=1; x<collection.length; x++) {
		if (collection[x].type.toUpperCase()=='CHECKBOX')
			collection[x].click();
		collection[x].checked = aChecked;
	}
}

function checkbox_checked(obj_val,obj_id){
	var obj_ids=obj_id.split(",");var cur_obj;
	for(var i=0;i<obj_ids.length;i++){
		cur_obj=obj_ids[i];
		if(document.getElementById(cur_obj)){
			document.getElementById(cur_obj).checked=obj_val;
		}
	}
}

/*Function for  Hide and Show divs for provider priviliges pop up  */
function showNhide_divs(obj){
	$('#span'+obj).toggleClass('glyphicon-menu-down glyphicon-menu-up');
	$("#"+obj).toggle("blind");
}

function load_fed_ein(obj){
	var raw_val = obj.value;
	if(raw_val != ""){
		var arr_val = raw_val.split("^");
		if(document.getElementById("pro_tax")){
			document.getElementById("pro_tax").value = arr_val[1];
		}
	}else{
		if(document.getElementById("pro_tax")){
			document.getElementById("pro_tax").value = "";
		}
	}
}

function delProConfCheck(chk, int_user_id, str_user_name){
	var delReason;
	if($("#hidd_reason_text")){delReason=$("#hidd_reason_text").val();}
	int_user_id = int_user_id || 0; //defaly 0
	str_user_name = str_user_name || ""; //defaly ""
	if(chk == true){
		if($.trim(delReason)==""){top.fAlert('Please enter reason for deletion.');return false;}
		delete_provider(int_user_id, str_user_name, true,delReason);
	}
	else if(chk == false){
		return false
	}
}

function onBlur_reason(Reasonval){$("#hidd_reason_text").val(Reasonval);}

function delete_provider(int_user_id, str_user_name,blStartDelProcess,delReason){	
	blStartDelProcess = blStartDelProcess || false; //defaly false
	var pro_id = '';
	$('.chk_sel').each(function(){
		if($(this).is(':checked'))
			pro_id+=','+$(this).val();
	});
	pro_id = pro_id.substr(1);
	if(pro_id){
		if(blStartDelProcess == false){
			var reason_field="<br />Reason: <textarea style='vertical-align:text-top;width:250px; overflow:auto;' onblur='top.fmain.onBlur_reason(this.value);'></textarea>";
			var arrConfFun = new Array();
			arrConfFun[0] = "top.fmain.delProConfCheck(true, '"+escape(int_user_id)+"', '"+escape(str_user_name)+"')";
			arrConfFun[1] = "top.fmain.delProConfCheck(false)";		
			top.fancyConfirm(arrCoreLangMsg[9]+reason_field,'', arrConfFun[0], arrConfFun[1]);		
			return
		}
		
		//var doproceed = confirm("Are you sure you want to delete this provider?");
		var doproceed = blStartDelProcess;
		if(doproceed){
			var url= ajax_file + "?req=delete_selected&del_user_ids="+pro_id+"&str_user_name="+str_user_name+"&delReason="+delReason;
			$.ajax({
				url:url,
				type:'POST',
				success:function(r){
					get_provider_listing();	
					top.fmain.location.href = top.JS_WEB_ROOT_PATH + "/interface/admin/providers/index.php";
				}
				
			});
		}
	}else{
		top.fAlert('No Record Selected.');
	}
}

function sel_all(stat){
	$(".chk_sel").prop('checked',stat);
}
			
var stored_priv = false;
var stored_priv_details = "";
function store_privileges(){

	stored_priv_details = "";

	/*if(document.getElementById("priv_clinical").checked == true){
		stored_priv_details += "priv_clinical,";
	}*/
	if(document.getElementById("priv_cl_work_view").checked == true){
		stored_priv_details += "priv_cl_work_view,";
	}
	if(document.getElementById("priv_cl_tests").checked == true){
		stored_priv_details += "priv_cl_tests,";
	}
	if(document.getElementById("priv_cl_medical_hx").checked == true){
		stored_priv_details += "priv_cl_medical_hx,";
	}
	if(document.getElementById("priv_Front_Desk").checked == true){
		stored_priv_details += "priv_Front_Desk,";
	}
	if(document.getElementById("priv_Billing").checked == true){
		stored_priv_details += "priv_Billing,";
	}
	if(document.getElementById("priv_Accounting").checked == true){
		stored_priv_details += "priv_Accounting,";
	}
	if(document.getElementById("priv_Security").checked == true){
		stored_priv_details += "priv_Security,";
	}
	if(document.getElementById("priv_Reports").checked == true){
		stored_priv_details += "priv_Reports,";
	}
	if(document.getElementById("priv_sc_scheduler").checked == true){
		stored_priv_details += "priv_sc_scheduler,";
	}
	if(document.getElementById("priv_sc_house_calls").checked == true){
		stored_priv_details += "priv_sc_house_calls,";
	}
	if(document.getElementById("priv_sc_recall_fulfillment").checked == true){
		stored_priv_details += "priv_sc_recall_fulfillment,";
	}
	if(document.getElementById("priv_bi_front_desk").checked == true){
		stored_priv_details += "priv_bi_front_desk,";
	}
	if(document.getElementById("priv_bi_ledger").checked == true){
		stored_priv_details += "priv_bi_ledger,";
	}
	if(document.getElementById("priv_bi_prod_payroll").checked == true){
		stored_priv_details += "priv_bi_prod_payroll,";
	}
	if(document.getElementById("priv_bi_ar").checked == true){
		stored_priv_details += "priv_bi_ar,";
	}
	/*if(document.getElementById("priv_bi_statements").checked == true){
		stored_priv_details += "priv_bi_statements,";
	}*/
	if(document.getElementById("priv_bi_end_of_day").checked == true){
		stored_priv_details += "priv_bi_end_of_day,";
	}
	if(document.getElementById("priv_cl_clinical").checked == true){
		stored_priv_details += "priv_cl_clinical,";
	}
	if(document.getElementById("priv_cl_visits").checked == true){
		stored_priv_details += "priv_cl_visits,";
	}
	if(document.getElementById("priv_cl_ccd").checked == true){
		stored_priv_details += "priv_cl_ccd,";
	}
	if(document.getElementById("priv_cl_order_set").checked == true){
		stored_priv_details += "priv_cl_order_set,";
	}
	if(document.getElementById("priv_View_Only").checked == true){
		stored_priv_details += "priv_View_Only,";
	}
	if(document.getElementById("priv_vo_clinical").checked == true){
		stored_priv_details += "priv_vo_clinical,";
	}
	if(document.getElementById("priv_vo_pt_info").checked == true){
		stored_priv_details += "priv_vo_pt_info,";
	}
	if(document.getElementById("priv_vo_acc"))
	{
		if(document.getElementById("priv_vo_acc").checked == true){
			stored_priv_details += "priv_vo_acc,";
		}
	}
	if(document.getElementById("priv_Sch_Override").checked == true){
		stored_priv_details += "priv_Sch_Override,";
	}
	if(document.getElementById("priv_pt_Override"))
	{
		if(document.getElementById("priv_pt_Override").checked == true){
			stored_priv_details += "priv_pt_Override,";
		}
	}
	if(document.getElementById("priv_Optical").checked == true){
		stored_priv_details += "priv_Optical,";
	}
	if(document.getElementById("priv_iOLink").checked == true){
		stored_priv_details += "priv_iOLink,";
	}
	stored_priv = true;
}

function unstore_privileges(){
	stored_priv = false;
	stored_priv_details = "";
}

function show_privilages(o){
	if(typeof(o)!="undefined" && o.id == "el_privileges"){
		if($("#el_privileges").val() > 0){ return; }		
	}
	onload_privilages_popup();
	$('#new_priv_div').modal('show');
	set_modal_height("new_priv_div");
	var btn_array = [['Done','','select_priv();$(\'#close_priv\').trigger(\'click\');']];
	top.fmain.set_modal_btns('new_priv_div .modal-footer',btn_array);
}

function save_provider_ajx(){
	$('#new_priv_div input, #priv_div_modal input').prop('disabled', false);
	var frm_data = $('[name^=frmprovider]').serialize();
	$.ajax({
		url: top.JS_WEB_ROOT_PATH+'/interface/admin/providers/ajax.php',
		data:frm_data+'&req=save',
		type:'POST',
		dataType:'JSON',
		beforeSend:function(){
			top.show_loading_image('show');
		},
		success:function(response){
			if(response.err != 'Provider details have been saved successfully.'){
				top.fAlert(response.err);
				if(response.focus){
					$('[name^='+response.focus+']').focus();
				}
				return false;
			}else{
				top.alert_notification_show(response.err);
				location.href = response.page_url;
			}
		},
		complete:function(){
			top.show_loading_image('hide');	
		}
	});
}

window.onload = function(){
	var hashVal = window.location.hash.substr(1);
	if(hashVal!="" && hashVal!="#") a = setInterval(do_scroll,1000);
}

if( typeof init_page !== 'undefined' && init_page )
{
	$(document).ready(function(){
		init_providers();
		set_header_title('Users');

		set_modal_height("provider_add_edit_div");
		var btn_array = [['Save','','return top.fmain.checkdata();']];
		top.fmain.set_modal_btns('provider_add_edit_div .modal-footer',btn_array);

	});
}

$(window).resize(function(){
	set_modal_height("provider_add_edit_div");
});

function setColorPicker(colorVal){
	if(colorVal == '') colorVal = '#FFFFFF';
		$(".grid_color_picker11").spectrum({
		color:colorVal,	
		showInput: true,
		className: "full-spectrum",
		showInitial: true,
		showPalette: true,
		showSelectionPalette: true,
		showAlpha: true,
		maxPaletteSize: 10,
		preferredFormat: "hex",
		localStorageKey: "spectrum.demo",
		allowEmpty: true	
	});
}

/* Grid Color Picker */
	
///////////////////////////////////
//grid color picker code end here	
///////////////////////////////////

function twCredSave(action, proId, validate){
	if(proId == '' || typeof(proId) == 'undefined') return false;
	if(action == '' || typeof(action) == 'undefined') return false;
	if(validate == '') validate = false;
	
	var userNameTW = '';
	var allScriptUser = '';
	var allScriptPass = '';
	var allScriptEntryCode = '';
	var successMsg = '';
	
	var htmlStr = 	'<div class="row">';
		htmlStr 	+= 	'<input type="hidden"  name="imwUsername" value="{USERNAME}" /> ';
		htmlStr 	+= 	'<input type="hidden"  name="imwProvId" value="'+proId+'" /> ';
		htmlStr 	+= 	'<div class="col-sm-12">';
		htmlStr 	+= 		'<div class="form-group">';
		htmlStr 	+= 			'<label for="">EHR Username</label>';
		htmlStr 	+= 			'<input type="text" name="ehr_useranme" id="ehr_useranme" value="{ALLSCRIPTUSER}" class="form-control" autocomplete="off" />';
		htmlStr 	+= 		'</div>';
		htmlStr 	+= 	'</div>';
		
		/*htmlStr 	+= 	'<div class="col-sm-12">';
		htmlStr 	+= 		'<div class="form-group">';
		htmlStr 	+= 			'<label for="">EHR Password</label>';
		htmlStr 	+= 			'<input type="password" name="ehr_password" value="{ALLSCRIPTPASS}" class="form-control" autocomplete="off" />';
		htmlStr 	+= 		'</div>';
		htmlStr 	+= 	'</div>';*/
		
		htmlStr 	+= 	'<div class="col-sm-12">';
		htmlStr 	+= 		'<div class="form-group">';
		htmlStr 	+= 			'<label for="">Entry Code</label>';
		htmlStr 	+= 			'<input type="text" name="entry_code" value="{ALLSCRIPTENTRY}" class="form-control" autocomplete="off" />';
		htmlStr 	+= 		'</div>';
		htmlStr 	+= 	'</div>';	
		
	htmlStr 	+= 	'</div>';
	
	var footerBtn = '<button class="btn btn-success" id="saveTWCred" type="button" onClick="top.fmain.twCredSave(\'save\', '+proId+');">Save</button> <button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>';
	
	switch(action){
		case 'show':
			$.ajax({
				url: top.JS_WEB_ROOT_PATH + "/interface/admin/providers/ajax.php",
				type: 'POST',
				dataType:'JSON',
				data:{callFrom:action, provId:proId, req:'touchworksCredSave'},
				beforeSend:function(){
					top.show_loading_image('show');
				},
				success:function(response){
					if(!response){
						top.fAlert('Problem Retreiving Credantials !');
						return false;
					}
					
					htmlStr = htmlStr.replace("{USERNAME}", response.username);
					htmlStr = htmlStr.replace("{ALLSCRIPTUSER}", response.as_username);
					/*htmlStr = htmlStr.replace("{ALLSCRIPTPASS}", response.as_password);*/
					htmlStr = htmlStr.replace("{ALLSCRIPTENTRY}", response.as_entry_code);
					
					show_modal('touchWorkModal','TouchWorks EHR Credentials',htmlStr,footerBtn);
				},
				complete:function(){
					top.show_loading_image('hide');
				}
			});
		break;
		
		case 'save':
			var modal = $('#touchWorkModal');
			
			var userName11 = modal.find('input[name=imwUsername]').val();
			var twUser = modal.find('input[name=ehr_useranme]').val();
			/*var twPass = modal.find('input[name=ehr_password]').val();*/
			var twEntryCode = modal.find('input[name=entry_code]').val();
			
			 /*|| twPass == ''*/
			if(twUser == ''){
				top.fAlert('Insufficient TouchWorks Login details !');
				return false;
			}
			
			//Checking Name
			if(userName11 != twUser && (validate === false || typeof(validate) == 'undefined')){
				top.fancyConfirm("iMW username and TouchWorks EHR username are not same.\nDo you want to proceed?","","top.fmain.twCredSave('save', "+proId+", true);");
				return false;
			}
			
			/*twPass:twPass, */
			$.ajax({
				url: top.JS_WEB_ROOT_PATH + "/interface/admin/providers/ajax.php",
				type: 'POST',
				dataType:'JSON',
				data:{callFrom:action, provId:proId, req:'touchworksCredSave', twUser:twUser, twEntryCode:twEntryCode, imwUser:userName11},
				beforeSend:function(){
					top.show_loading_image('show');
				},
				success:function(response){
					if(!response){
						top.fAlert('Problem Saving Credantials !');
						return false;
					}
					
					htmlStr = htmlStr.replace("{USERNAME}", response.username);
					htmlStr = htmlStr.replace("{ALLSCRIPTUSER}", response.as_username);
					/*htmlStr = htmlStr.replace("{ALLSCRIPTPASS}", response.as_password);*/
					htmlStr = htmlStr.replace("{ALLSCRIPTENTRY}", response.as_entry_code);
					
					if(response.Success) successMsg = response.Success;
					
					if(successMsg !== ''){
						$('#touchWorkModal').modal('hide');
						top.fAlert(successMsg);
						return false;
					}
					show_modal('touchWorkModal','TouchWorks EHR Credentials',htmlStr,footerBtn);
				},
				complete:function(){
					top.show_loading_image('hide');
				}
			});
			
		break;
	}
	
}

function resetToOldPriv() {
    var proId = $('#pro_id').val();
    if(!proId){return false;}
    $.ajax({
        url: top.JS_WEB_ROOT_PATH + "/interface/admin/providers/ajax.php",
        type: 'POST',
        dataType:'JSON',
        data:{provId:proId, req:'resetToOldPriv'},
        beforeSend:function(){
            top.show_loading_image('show');
        },
        success:function(response){
            $.each(response.user_priviliges, function(key, val) {
                if(val==1) {
                    $("#"+key).prop('checked', true);
                } else {
                    $("#"+key).prop('checked', false);
                }
            });
        },
        complete:function(){
            top.show_loading_image('hide');
        }
    });
}

/* DSS electronic signature code for user */
function dssUserInfoSave(action, proId, validate){
	if(proId == '' || typeof(proId) == 'undefined') return false;
	if(action == '' || typeof(action) == 'undefined') return false;
	if(validate == '') validate = false;
	
	var successMsg = '';
	
	var htmlStr = 	'<div class="row">';
		htmlStr 	+= 	'<input type="hidden"  name="imwProvId" value="'+proId+'" /> ';
		htmlStr 	+= 	'<div class="col-sm-12">';
		htmlStr 	+= 		'<div class="form-group">';
		htmlStr 	+= 			'<label for="">Electronic Signature Code</label>';
		htmlStr 	+= 			'<input type="text" name="electronicSignature" id="electronicSignature" value="{ELECSIGNATURE}" class="form-control" autocomplete="off" />';
		htmlStr 	+= 		'</div>';
		htmlStr 	+= 	'</div>';
	htmlStr 	+= 	'</div>';
	
	var footerBtn = '<button class="btn btn-success" id="saveDSSInfo" type="button" onClick="top.fmain.dssUserInfoSave(\'save\', '+proId+');">Save</button><button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>';
	
	switch(action){
		case 'show':
			$.ajax({
				url: top.JS_WEB_ROOT_PATH + "/interface/admin/providers/ajax.php",
				type: 'POST',
				dataType:'JSON',
				data:{callFrom:action, provId:proId, req: 'dssUserInfoSave'},
				beforeSend:function(){
					top.show_loading_image('show');
				},
				success:function(response){
					
					if(!response){
						top.fAlert('No response received!');
						return false;
					}
					if(response.dss_elec_sign != null)
						htmlStr = htmlStr.replace("{ELECSIGNATURE}", response.dss_elec_sign);
					else
						htmlStr = htmlStr.replace("{ELECSIGNATURE}", '');
					
					show_modal('DSSModal','DSS User Info',htmlStr,footerBtn);
				},
				complete:function(){
					top.show_loading_image('hide');
				}
			});
		break;
		
		case 'save':
			var modal = $('#DSSModal');
			
			var dss_elec_sign = modal.find('input[name=electronicSignature]').val();
			
			if(dss_elec_sign == ''){
				top.fAlert('Not allowed to be empty.');
				return false;
			}
			
			$.ajax({
				url: top.JS_WEB_ROOT_PATH + "/interface/admin/providers/ajax.php",
				type: 'POST',
				dataType:'JSON',
				data:{callFrom:action, provId:proId, req:'dssUserInfoSave', electronicSignature:dss_elec_sign},
				beforeSend:function(){
					top.show_loading_image('show');
				},
				success:function(response){
					if(!response){
						top.fAlert('No response received!');
						return false;
					}
					var respMsg = '';
					if(response.Success) respMsg = response.Success;
					if(response.Error) respMsg = response.Error;
					
					if(respMsg !== ''){
						if(response.Success) $('#DSSModal').modal('hide');
						top.fAlert(respMsg);
						return false;
					}
				},
				complete:function(){
					top.show_loading_image('hide');
				}
			});
			
		break;
	}
	
}

function priv_chart_finalize(ths) {
    if($(ths).is(':checked')) {
        $('#priv_chart_finalize').prop('checked', true);
    } else {
        $('#priv_chart_finalize').prop('checked', false);
    }
}


function portal_refill_direct(id){
	id = id || 0;
	id= parseInt(id);
	if(id <= 0 ) return ;
	
	var url = ajax_file+"?req=refill_direct&provid="+id;
	$.ajax({type: "POST",url: url,success: function(r){ $("#refill_direct_access_portal").html(r); $("#portal_refill_direct_div").modal('show');var btn_array = [['Save','','top.fmain.savePortalRefillDirect();']];
	top.fmain.set_modal_btns('portal_refill_direct_div .modal-footer',btn_array);$('.selectpicker').selectpicker('refresh'); }});
}

function savePortalRefillDirect() {
	var _form =	$("#refill_direct_form");
	var url = ajax_file+'?req=savePortalRefillDirect';
	$.ajax({
		url : url,
		type:'POST',
		data:_form.serialize(),
		beforeSend: function(){
			top.show_loading_image('show');
		},
		complete:function(r){
			top.show_loading_image('hide');
		},
		success: function(r){
			if(r) top.fAlert(r);
			$("#portal_refill_direct_div").modal('hide');
			$("#portal_refill_direct_div").remove();
		}
	});
}

function disp_providers(){
	var ths=$('#view_all_provider_financials');
	var group_name=$("#pro_group option:selected").text();
	
	if($(ths).is(':checked')==false && (group_name!='Physicians' && group_name!='Technicians' && group_name!='Nurse')){		
		$('#div_rpt_financial_providers').css('visibility', 'visible');
	}else{
		$('#rpt_financial_providers').selectpicker('val','');	
		$('#rpt_financial_providers').selectpicker('refresh');
		$('#div_rpt_financial_providers').css('visibility', 'hidden');
	}
}