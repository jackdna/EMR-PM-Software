// Electronic Billing JavaScript Document

// TAKING INSTANCE OF PARENT/MASTER FUNCTION
if(typeof(window.top.popup_win)=='function') 	var	popup_win = window.top.popup_win;
else if(typeof(window.opener.top.popup_win)=='function') var popup_win = window.opener.top.popup_win;

if(typeof(window.top.JS_WEB_ROOT_PATH)!='undefined')var JS_WEB_ROOT_PATH = window.top.JS_WEB_ROOT_PATH;
else if(typeof(window.opener.top.JS_WEB_ROOT_PATH)!='undefined')var JS_WEB_ROOT_PATH = window.opener.top.JS_WEB_ROOT_PATH;

// TO VIEW ub FORM AFTER CLICKING ON PATIENT NAME IN CLAIM LIST WHILE VIEWING CLAIM FILE DETAIL.
function print_hcfa_ub(validChargeListId,charge_list_detail_ids,InsComp,printHcfa){
	var url=JS_WEB_ROOT_PATH+"/interface/billing/hcfa_ub_by_era.php?vchl="+validChargeListId+"&vchld="+charge_list_detail_ids+"&InsComp="+InsComp+"&printHcfa="+printHcfa;
	
	popup_win(url);	
}

//SEND COMMERTIAL BATCH FILE TO CLEARING HOUSE
function sendFile(){
	document.downloadFrm.action = 'curl.php';
	document.downloadFrm.submit();
}

//SEND MEDICATE BATCH FILE TO ABILITY (VISION SHARE)
function send_vision_share(op, insCompId){
	document.downloadFrm.action = 'vision_share_837_batch_submit.php?gpId='+op+'&insCompId='+insCompId;
	document.downloadFrm.submit();
}

//Select Print Paper Claim Type
function selectChkBox(obj){
	var chkBoxArr = new Array("PrintCms","PrintCms_white","Printub","WithoutPrintub");
	var obj_id=obj.id;
	if($("#"+obj_id).is(':checked')){
		for(i in chkBoxArr){
			if(chkBoxArr[i] != obj.id){
				$("#"+chkBoxArr[i]).prop("checked",false);
			}
		}
	}
	else{
		$("#PrintCms").prop("checked",true);
	}
}

//Process Paper Claim
function printProcess()
{
	document.frm_billing.submit();
}

//Print Paper Claim
function check_data(){
	var obj = document.getElementsByName("chl_chk_box[]");
	var msg = false;
	var file_name="";
	for(i=0;i<obj.length;i++){
		if(obj[i].checked == true){
			msg = true;
		}
	}
	if(msg == false){
		top.fAlert('Please select any Patient to print');
		return false;
	}
	
	top.show_loading_image('show');
	var frm_data = $('#frm_billing_res').serialize();
	var print_ins_type = $("#print_ins_type").val();
	var print_paper_type = $("#print_paper_type").val();
	
	if(print_paper_type=="Printub" || print_paper_type=="WithoutPrintub"){
		file_name="print_ub.php";
	}else{
		file_name="print_hcfa_form.php";
	}
	
	var u = file_name+"?InsComp="+print_ins_type+"&print_paper_type="+print_paper_type;	
	
	$.ajax({
		url:u,
		type:"POST",
		data:frm_data,
		success:function(r){
			window.open(r,"PrintUB","resizable=1,width=650,height=450");
			top.show_loading_image('hide');
			printProcess();
		}
	});
}

//Re-Print Paper Claim
function re_print(){
	var parWidth = 900;
	var win_height = 590;
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/billing/re_print.php',"width="+parWidth+",scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");

}



