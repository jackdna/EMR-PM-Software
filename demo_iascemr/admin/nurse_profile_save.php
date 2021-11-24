<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("../common/user_agent.php");
$isHTML5OK = isHtml5OK();
$browserArr = browser();
$chkVersionArr = array(9,10);
$dataTopPos = "1100";
if($isHTML5OK && in_array($browserArr["version"],$chkVersionArr)) {
	$dataTopPos = "400";	
}
?>
<!DOCTYPE html>
<html>
<head>
<style>
	.sigdrw{border:1px solid orange;display:inline-block;}
</style>
<title>Nurse Profile</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("adminLinkfile.php"); ?>
<script type="text/javascript" src="../js/jquery-1.9.0.min.js" ></script>
<script type="text/javascript" src="../js/simple_drawing.js"></script>

<link rel="stylesheet" href="../css/jquery.webui-popover.css" />
<script src="../js/jquery.webui-popover.js"></script>	
<script src="../js/external-tooltip.js"></script>	
    
<script>
var preDefineCloseOut;
function preDefineOpenCloseFun() {
	document.getElementById("hiddPreDefineId").value = "preDefineOpenYes";
}
function preCloseFun(Id) {
	if(document.getElementById("hiddPreDefineId")) {
		if(document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
			if(document.getElementById(Id)) {
				if(document.getElementById(Id).style.display == "block"){
					document.getElementById(Id).style.display = "none"; 
					//document.getElementById("hiddPreDefineId").value = "";
				}
			}
			if(top.frames[0].frames[0].document.getElementById(Id)) {
				if(top.frames[0].frames[0].document.getElementById(Id).style.display == "block"){
					top.frames[0].frames[0].document.getElementById(Id).style.display = "none"; 
					//top.frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
				}
			}
		}
		
	}
}

function checkSingleChkBox(elemId,grpName)
{
	var obgrp = document.getElementsByName(grpName);
	var objele = document.getElementById(elemId);
	var len = obgrp.length;		
	if(objele.checked == true)
	{		
		for(var i=0;i<len;i++)
		{
			if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) )
			{
				//obgrp[i].click();
				obgrp[i].checked=false;
			}
		}	
	}
}
function disp_one_hide_other_onchangeNewAdmin(drop_down_id,one_id,other_id,chbx_heparin_lockStart,chbx_iv,iv_sub_id) {
		
		//if(document.getElementById(chbx_heparin_lockStart).checked==true) {
			if(document.getElementById(drop_down_id).value=='other') {
				document.getElementById(one_id).style.display="none";
				document.getElementById(other_id).style.display="inline-block";
			} else if(document.getElementById(drop_down_id).value!=''  && document.getElementById(drop_down_id).value!='other') {
				if(document.getElementById(chbx_heparin_lockStart).checked==true || document.getElementById(chbx_iv).checked==true) {
					document.getElementById(one_id).style.display="inline-block";
				}else {
					document.getElementById(one_id).style.display="none";
				}
				if(document.getElementById(chbx_iv).checked==true) {
					if(document.getElementById(iv_sub_id)) {
						document.getElementById(iv_sub_id).style.display="inline-block";
					}
				}else {
					if(document.getElementById(iv_sub_id)) {
						document.getElementById(iv_sub_id).style.display="none";
					}
				}
				document.getElementById(other_id).style.display="none";
			} else {
				document.getElementById(one_id).style.display="none";
				document.getElementById(other_id).style.display="none";
			}
		/*
		}else {
			document.getElementById(one_id).style.display="none";
			document.getElementById(other_id).style.display="none";
		}*/
}

function showEvaluationLocalNurseAdminFn(name1, name2, c, posLeft, posTop){	
	//alert(top.frames[0].frames[0].document.getElementById("evaluationLocalNurseEvaluationAdminDiv"));
	//top.frames[0].frames[0].document.getElementById("evaluationLocalNurseEvaluationAdminDiv").style.display = 'inline-block';
	//top.frames[0].frames[0].document.getElementById("evaluationLocalNurseEvaluationAdminDiv").style.left = posLeft+'px';
	//top.frames[0].frames[0].document.getElementById("evaluationLocalNurseEvaluationAdminDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	if(document.getElementById("hiddPreDefineId")) {
		document.getElementById("hiddPreDefineId").value = "";
		preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
	}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}

function getShowAdmin(posTop,posLeft,flag) {
	var p = $("#bp_temp");
	var offset = p.offset();
	posLeft=offset.left+100;
	posTop=offset.top;

	document.getElementById("bp").value = flag;
	top.frames[0].frames[0].document.getElementById("cal_pop_admin").style.display = "block";
	top.frames[0].frames[0].document.getElementById("cal_pop_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].document.getElementById("cal_pop_admin").style.top = posTop+'px';
	if(document.getElementById("hiddCalPopId")) {
		document.getElementById("hiddCalPopId").value = "";
		if(document.getElementById("hiddCalPopId").value == "") {
			tCloseOut = setTimeout("calOpenCloseFun()", 100);
		}
	}
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
}


function showEkgBigRowAdminDiv(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].document.getElementById("ekgLocalNurseAdminDiv").style.display = 'inline-block';
	top.frames[0].frames[0].document.getElementById("ekgLocalNurseAdminDiv").style.left = posLeft+'px';
	top.frames[0].frames[0].document.getElementById("ekgLocalNurseAdminDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
}

function showPostopEvaluationAdminFn(name1, name2, c, posLeft, posTop){	
	//alert(top.frames[0].frames[0].document.getElementById("postop_evaluationEvaluationDiv"));
	top.frames[0].frames[0].document.getElementById("postop_evaluationEvaluationAdminDiv").style.display = 'inline-block';
	top.frames[0].frames[0].document.getElementById("postop_evaluationEvaluationAdminDiv").style.left = posLeft+'px';
	top.frames[0].frames[0].document.getElementById("postop_evaluationEvaluationAdminDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
}

//Applet
function get_App_Coords(objElem){
	var coords,appName;
	var objElemSign = document.frmSaveNurseProfile.nurse_profile_sign;
	appName = objElem.name;
	coords = getCoords(appName);
	objElemSign.value = refineCoords(coords);
}
function refineCoords(coords){	
	isEmpty = coords.lastIndexOf(";");	
	if(isEmpty == -1){
		coords += ";";	
	}else{
		coords = coords.substr(0,isEmpty+1);		
	}		
	return coords;	
}
function getCoords(){
	var coords = document.applets["appNurse_signature"].getSign();
	return coords;
}
function getclear_os(objElem){
	document.applets["appNurse_signature"].clearIt();
	changeColorThis(255,0,0);
	//document.applets["appNurse_signature"].onmouseout();
	get_App_Coords(objElem);
}
function changeColorThis(r,g,b){				
	document.applets['appNurse_signature'].setDrawColor(r,g,b);								
}
//Applet


function enableDisableHonanFn() {
	/*
	var NoneHonanBalloon = document.frmSaveNurseProfile.chbx_NoneHonanBalloon;
	if(NoneHonanBalloon) {
		
		if(NoneHonanBalloon.checked==true) {	
			document.frmSaveNurseProfile.honanballon.value='';
			document.frmSaveNurseProfile.honanBallonAnother.value='';
			
			document.frmSaveNurseProfile.honanballon.disabled=true;
			document.frmSaveNurseProfile.honanBallonAnother.disabled=true;
		}else {
			document.frmSaveNurseProfile.honanballon.disabled=false;
			document.frmSaveNurseProfile.honanBallonAnother.disabled=false;
		}
		*
	}
	*/		
}

function checkSingleMedAdmin(chbxId1,chbxId2) {
	if(document.getElementById(chbxId1) && document.getElementById(chbxId2)) {
		if(document.getElementById(chbxId1).checked==true) {
			document.getElementById(chbxId2).checked=false;
		}
	
	}
}

function showBox($_this)
{
	var obj = document.getElementById('rblno_of_units');
	if(document.getElementById('chbx_rbl_yes').checked)
	{
		obj.style.display = 'inline-block';
	}
	else
	{
		obj.style.display = 'none';
	}
	
}
var chkEmpty1=false;
var chkEmpty2=false;
function copyValue(obj) {
	
	//alert(obj.name);
	if (obj.name=='txtInterOpDrugs1'){
	//alert(obj.value);
		if(document.getElementById('txtInterOpDrugs2').value==''){
			chkEmpty1 = true;
			document.getElementById('txtInterOpDrugs2').value=document.getElementById('txtInterOpDrugs1').value;
		}
		else if(chkEmpty1 == true){
			document.getElementById('txtInterOpDrugs2').value=document.getElementById('txtInterOpDrugs1').value;
		}
		else{
			chkEmpty2=false;
		}
	}
	else if(obj.name=='txtInterOpDrugs2'){
		if(document.getElementById('txtInterOpDrugs1').value==''){
			chkEmpty2 = true;
			document.getElementById('txtInterOpDrugs1').value=document.getElementById('txtInterOpDrugs2').value;
		}
		else if(chkEmpty2 == true){
			document.getElementById('txtInterOpDrugs1').value=document.getElementById('txtInterOpDrugs2').value;
		}
		else{
			chkEmpty1=false;
		}
	}
}

var isHTML5OK="<?php echo $isHTML5OK;?>";
$(document).ready(function () {
if(isHTML5OK=="1"){	

	$(".wrap_inside_admin").scroll(function(e) {
		
		$("canvas").each(function()
		{
			var $this=	$(this);
			var T	=	$this.offset().top
			var L	=	$this.offset().left-15;
			
			var SD	=	$("#sig_data"+this.id+"").val()
			var SM	=	$("#sig_img"+this.id+"").val()
			if( SD === '' || SM !== '')
			{
				$this.attr('data-top-pos',T);
				$this.attr('data-left-pos',L); 
			
				oSimpDrw[this.id]['left_pos']	=	L;
				oSimpDrw[this.id]['top_pos']	=	T;
			}
			
			
		});
		
	});
	
	$("canvas").each(function() { 
		
		var $this=	$(this);
		var T	=	$this.offset().top;
		var L	=	$this.offset().left;
		var W	=	$this.parent().width();
		
		$this.attr('data-top-pos',T);
		$this.attr('data-left-pos',L); 
		$this.attr('width',W); 	
			
		oSimpDrw[this.id]	=	new SimpleDrawing(this.id);
		oSimpDrw[this.id].init();
		
		
	});
	
}
});
function getClear(ap){	
	if(typeof(isHTML5OK)!="undefined" && isHTML5OK==1){
		if(oSimpDrw && oSimpDrw[ap]){oSimpDrw[ap].clearCanvas();	}		
	}
}	

//START FUNCTION TO FIND POSITION FROM LEFT
function findPos_X_custom(id){
	var obj = document.getElementById(id);
	var leftPanel =	parseFloat($('.sidebar-wrap-op').outerWidth(true));
	var posX = obj.offsetLeft;
	while(obj.offsetParent){
		posX=posX+obj.offsetParent.offsetLeft;
		if(obj==document.getElementsByTagName('body')[0]){break}
		else{obj=obj.offsetParent;}
	}
	var posXNew = parseFloat(posX - leftPanel);
	return(posXNew);
}
//END FUNCTION TO FIND POSITION FROM LEFT

//START FUNCTION TO FIND POSITION FROM TOP
function findPos_Y_custom(id){
	var obj = document.getElementById(id);
	var posY = obj.offsetTop;
	while(obj.offsetParent){
		posY=posY+obj.offsetParent.offsetTop;
		if(obj==document.getElementsByTagName('body')[0]){break}
		else{obj=obj.offsetParent;}
	}
	return(posY);
}
//END FUNCTION TO FIND POSITION FROM TOP


function showFoodListAdminFn(name1, name2, c, posLeft, posTop){
	//alert(top.frames[0].frames[0].document.getElementById("listContent_food_taken"));
	top.frames[0].frames[0].frames[0].document.getElementById("listContent_food_taken").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("listContent_food_taken").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("listContent_food_taken").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}

</script>
</head>

<body>
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$nurseList = $_REQUEST['nurseList'];
$saveRecord = $_POST['saveRecord'];
include("../common/food_list_pop_admin.php");
	
if($saveRecord=='Yes') {
	unset($arrayRecord);
	$arrayExclude = array('selected_frame_name','divId','counter','secondaryValues','tertiaryValues','nurseList','hiddPreDefineId','saveRecord','nurse_fname','bp_hidden','sig_datasign','sig_imgsign','profile_id');
	
	$fieldMap = array('chbx_fdt' => 'foodDrinkToday', 'txtarea_list_food_take' => 'listFoodTake', 'chbx_lab_test' => 'labTest', 'chbx_ekg' => 'ekg', 'chbx_cons_sign' => 'consentSign', 'chbx_h_p' => 'hp', 'chbx_admit_to_hosp' => 'admitted2Hospital', 'txtarea_admit_to_hosp' => 'reason', 'chbx_hlt_ques' => 'healthQuestionnaire', 'chbx_stnd_odrs' => 'standingOrders', 'chbx_pat_void' => 'patVoided', 'chbx_hearingAids' => 'hearingAids', 'chbx_hearingAidsRemoved' => 'hearingAidsRemoved', 'chbx_denture' => 'denture', 'chbx_dentureRemoved' => 'dentureRemoved', 'chbx_anyPain' => 'anyPain', 'chbx_doctorNotified' => 'doctorNotified', 'chbx_removedIntact' => 'removedIntact', 'chbx_aox3' => 'patient_aox3', 'chbx_patientReleased2Adult' => 'patientReleased2Adult', 'chbx_patient_transfer' => 'patient_transfer' );
	
	foreach($_POST as $k => $v) {
		
		if($fieldMap[$k]) $k = $fieldMap[$k];
		
		if( !in_array($k,$arrayExclude) )
			$arrayRecord[$k] = imw_real_escape_string($v);
	}
	
	$arrayRecord['removedIntact']  = isset($arrayRecord['removedIntact']) ? $arrayRecord['removedIntact'] : '';
	$arrayRecord['patient_aox3']  = isset($arrayRecord['patient_aox3']) ? $arrayRecord['patient_aox3'] : '';
	$arrayRecord['patient_transfer']  = isset($arrayRecord['patient_transfer']) ? $arrayRecord['patient_transfer'] : '';
	$arrayRecord['patientReleased2Adult']  = isset($arrayRecord['patientReleased2Adult']) ? $arrayRecord['patientReleased2Adult'] : '';
	
	$arrayRecord['chbx_saline_lockStart']  = isset($arrayRecord['chbx_saline_lockStart']) ? $arrayRecord['chbx_saline_lockStart'] : '';
	$arrayRecord['chbx_saline_lock']  = isset($arrayRecord['chbx_saline_lock']) ? $arrayRecord['chbx_saline_lock'] : '';
	
	if( $arrayRecord['chbx_saline_lock']=='iv' && $arrayRecord['ivSelection'] <> '' &&  $arrayRecord['ivSelection'] <> 'other'  ) {
		
		$arrayRecord['chbx_KVO']  = isset($arrayRecord['chbx_KVO']) ? $arrayRecord['chbx_KVO'] : '';
		$arrayRecord['chbx_rate']  = isset($arrayRecord['chbx_rate']) ? $arrayRecord['chbx_rate'] : '';
		//$arrayRecord['txtbox_rate']  = $arrayRecord['chbx_rate'] ? $arrayRecord['txtbox_rate'] : '';
		$arrayRecord['chbx_flu']  = isset($arrayRecord['chbx_flu']) ? $arrayRecord['chbx_flu'] : '';
		//$arrayRecord['txtbox_flu']  = $arrayRecord['chbx_flu'] ? $arrayRecord['txtbox_flu'] : '';
	} 
	else if( ( $arrayRecord['chbx_saline_lock'] == 'iv' && ( $arrayRecord['ivSelection'] == '' || $arrayRecord['ivSelection'] == 'other') ) || ( $arrayRecord['chbx_saline_lock'] == '' && $arrayRecord['chbx_saline_lockStart'] == '')  ) {
		$arrayRecord['chbx_KVO'] = '';
		$arrayRecord['chbx_rate']  = '';
		$arrayRecord['txtbox_rate']  = '';
		$arrayRecord['chbx_flu']  = '';
		$arrayRecord['txtbox_flu']  = '';
		$arrayRecord['ivSelectionSide']  = '';
		$arrayRecord['gauge']  = '';
		$arrayRecord['gauge_other']  = '';
		$arrayRecord['txtbox_other_new']  = '';
	} else if( $arrayRecord['chbx_saline_lock'] <> 'iv' ) {
		$arrayRecord['chbx_KVO'] = '';
		$arrayRecord['chbx_rate']  = '';
		$arrayRecord['txtbox_rate']  = '';
		$arrayRecord['chbx_flu']  = '';
		$arrayRecord['txtbox_flu']  = '';
	}
	
	$arrayRecord['ivSelectionSide']  = isset($arrayRecord['ivSelectionSide']) ? $arrayRecord['ivSelectionSide'] : '';
	$arrayRecord['foodDrinkToday']  = isset($arrayRecord['foodDrinkToday']) ? $arrayRecord['foodDrinkToday'] : '';
	$arrayRecord['labTest']  = isset($arrayRecord['labTest']) ? $arrayRecord['labTest'] : '';
	$arrayRecord['ekg']  = isset($arrayRecord['ekg']) ? $arrayRecord['ekg'] : '';
	$arrayRecord['consentSign']  = isset($arrayRecord['consentSign']) ? $arrayRecord['consentSign'] : '';
	$arrayRecord['hp']  = isset($arrayRecord['hp']) ? $arrayRecord['hp'] : '';
	$arrayRecord['admitted2Hospital']  = isset($arrayRecord['admitted2Hospital']) ? $arrayRecord['admitted2Hospital'] : '';
	
	$arrayRecord['healthQuestionnaire']  = isset($arrayRecord['healthQuestionnaire']) ? $arrayRecord['healthQuestionnaire'] : '';
	$arrayRecord['standingOrders']  = isset($arrayRecord['standingOrders']) ? $arrayRecord['standingOrders'] : '';
	$arrayRecord['patVoided']  = isset($arrayRecord['patVoided']) ? $arrayRecord['patVoided'] : '';
	
	$arrayRecord['hearingAids']  = isset($arrayRecord['hearingAids']) ? $arrayRecord['hearingAids'] : '';
	$arrayRecord['denture']  = isset($arrayRecord['denture']) ? $arrayRecord['denture'] : '';
	$arrayRecord['anyPain']  = isset($arrayRecord['anyPain']) ? $arrayRecord['anyPain'] : '';
	$arrayRecord['doctorNotified']  = isset($arrayRecord['doctorNotified']) ? $arrayRecord['doctorNotified'] : '';
	
	$arrayRecord['listFoodTake']  = ($arrayRecord['foodDrinkToday'] <> 'Yes') ? '' : $arrayRecord['listFoodTake']; 
	$arrayRecord['reason']  = ($arrayRecord['admitted2Hospital'] <> 'Yes') ? '' : $arrayRecord['reason']; 
	$arrayRecord['hearingAidsRemoved']  = ($arrayRecord['hearingAids'] <> 'Yes') ? '' : $arrayRecord['hearingAidsRemoved']; 
	$arrayRecord['dentureRemoved']  = ($arrayRecord['denture'] <> 'Yes') ? '' : $arrayRecord['dentureRemoved']; 
	$arrayRecord['patientsRelation']  = ($arrayRecord['patientReleased2Adult'] <> 'Yes') ? '' : $arrayRecord['patientsRelation'];
	$arrayRecord['patientsRelationOther']  = ($arrayRecord['patientReleased2Adult'] <> 'Yes') ? '' : $arrayRecord['patientsRelationOther'];
	$arrayRecord['patientsRelationOther']  = ($arrayRecord['patientReleased2Adult'] == 'Yes' && $arrayRecord['patientsRelation'] <> 'other' ) ? '' : $arrayRecord['patientsRelationOther'];
	
	$nurse_profile_sign = $_REQUEST['nurse_profile_sign'];
	if(($nurse_profile_sign == '255-0-0:;') || ($nurse_profile_sign == '0-0-0:;') || ($nurse_profile_sign == '0-0-0:;;') || ($nurse_profile_sign == '255-0-0:;;') || ($nurse_profile_sign == '255-0-0:;;;') || ($nurse_profile_sign == '255-0-0:;0-0-0:;') || ($nurse_profile_sign == '255-0-0:;0-0-0:;;')){
		$nurse_profile_sign = '';
	}
	
	if($isHTML5OK) {
		$arrayRecord['nurse_profile_sign_path'] = $objManageData->save_user_image($_REQUEST,$_REQUEST['nurseId'],$_REQUEST['nurse_fname'],'nurse_sign');
	}else{
		$arrayRecord['nurse_profile_sign'] = $nurse_profile_sign;	
	}
	
	$chkNurseIdDetails = $objManageData->getRowRecord('nurse_profile_tbl', 'nurseId', $_REQUEST['nurseId']);
	
	if($chkNurseIdDetails){
		$arrayRecord['modified_by'] = $_SESSION['loginUserId'];
		$arrayRecord['modified_date_time'] = date('Y-m-d H:i:s');
		$c=$objManageData->UpdateRecord($arrayRecord, 'nurse_profile_tbl', 'nurseId', $_REQUEST['nurseId']);
	}else{
		$arrayRecord['created_by'] = $_SESSION['loginUserId'];
		$arrayRecord['created_date_time'] = date('Y-m-d H:i:s');
		$d=$objManageData->addRecords($arrayRecord, 'nurse_profile_tbl');
	}
	
	if($c)
	{
		echo "<script>top.frames[0].alert_msg('update')</script>";
	}
	if($d)
	{
		echo "<script>top.frames[0].alert_msg('success')</script>";
	}
}

//VIEW RECORD FROM DATABASE
	$nurseDetails = $objManageData->getRowRecord('nurse_profile_tbl', 'nurseId', $nurseList);
	if($nurseDetails) {
		//extract($nurseDetails); 
		
		$nurse_profile_sign = $nurseDetails->nurse_profile_sign;
		$nurse_profile_sign_path = $nurseDetails->nurse_profile_sign_path;
		
		$labTest = $nurseDetails->labTest;
		$ekg = $nurseDetails->ekg;
		$consentSign = $nurseDetails->consentSign;
		$hp = $nurseDetails->hp;
		$admitted2Hospital = $nurseDetails->admitted2Hospital;
		$healthQuestionnaire	 = $nurseDetails->healthQuestionnaire;
		$standingOrders = $nurseDetails->standingOrders;
		$patVoided = $nurseDetails->patVoided;
		$hearingAids = $nurseDetails->hearingAids;
		$hearingAidsRemoved = $nurseDetails->hearingAidsRemoved;
		$anyPain = $nurseDetails->anyPain;
		$painLevel = $nurseDetails->painLevel;
		$painLocation = $nurseDetails->painLocation;
		$doctorNotified = $nurseDetails->doctorNotified;
		$denture = $nurseDetails->denture;
		$dentureRemoved = $nurseDetails->dentureRemoved;
		$foodDrinkToday = $nurseDetails->foodDrinkToday;
		$listFoodTake = stripslashes($nurseDetails->listFoodTake);
		$preOpComments = stripslashes($nurseDetails->preOpComments);
		$reason = stripslashes($nurseDetails->reason);
		$relivedNurseIdPre = $nurseDetails->relivedNurseIdPre;
		$comments = stripslashes($nurseDetails->comments);
		$chbx_saline_lockStart = $nurseDetails->chbx_saline_lockStart;
		$chbx_saline_lock = $nurseDetails->chbx_saline_lock;
		$ivSelection = $nurseDetails->ivSelection;
		$ivSelectionOther = $nurseDetails->ivSelectionOther;
		$ivSelectionSide = $nurseDetails->ivSelectionSide;
		$chbx_KVO = $nurseDetails->chbx_KVO;
		$chbx_rate = $nurseDetails->chbx_rate;
		$txtbox_rate = $nurseDetails->txtbox_rate;
		$chbx_flu = $nurseDetails->chbx_flu;
		$txtbox_flu = $nurseDetails->txtbox_flu;
		$gauge = $nurseDetails->gauge;
		$gauge_other = stripslashes($nurseDetails->gauge_other);
		$txtbox_other_new = $nurseDetails->txtbox_other_new;
		$postOpSite = $nurseDetails->postOpSite;
		$nourishKind = $nurseDetails->nourishKind;
		$removedIntact = $nurseDetails->removedIntact;
		$patient_aox3 = $nurseDetails->patient_aox3;
		$patient_transfer = $nurseDetails->patient_transfer;
		$other_mental_status = stripslashes($nurseDetails->other_mental_status);
		$patientReleased2Adult = $nurseDetails->patientReleased2Adult;
		$patientsRelation = $nurseDetails->patientsRelation;
		$patientsRelationOther = $nurseDetails->patientsRelationOther;
		$relivedNurseId = $nurseDetails->relivedNurseId;
		$recoveryComments = stripslashes($nurseDetails->recoveryComments);
		$nurseId = $nurseDetails->nurseId;
		
	}	
//END VIEW RECORD FROM DATABASE

if($nurseList) {
	
		//GET Nurse Name
		$userNurseNameDetails = $objManageData->getRowRecord('users', 'usersId', $nurseList);
		$nurseName = $userNurseNameDetails->lname.', '.$userNurseNameDetails->fname; 
		//END GET Nurse Name
	?>	
    
<form name="frmSaveNurseProfile" class="wufoo topLabel" enctype="multipart/form-data" action="nurse_profile_save.php" method="post">
<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
<input type="hidden" name="divId" id="divId">
<input type="hidden" name="counter" id="counter">
<input type="hidden" name="secondaryValues" id="secondaryValues">
<input type="hidden" name="tertiaryValues" id="tertiaryValues">
<input type="hidden" name="nurseList" id="nurseList" value="<?php echo $nurseList;?>">
<input type="hidden" name="nurseId" id="nurseId" value="<?php echo $nurseList;?>">
<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
<input type="hidden" name="saveRecord" id="saveRecord" value="Yes">
<input type="hidden" name="nurse_fname" id="nurse_fname" value="<?php echo $userNurseNameDetails->fname;?>">

<div class="all_admin_content_agree wrap_inside_admin">	         
  <Div class="wrap_inside_admin scrollable_yes">
  	
    <!-- Start P3 Fields -->
    
  	<div class="striped_row_ans ">
    	<div class="scanner_win new_s">
      	<h4><Span>Pre-Op Nursing</Span></h4>
    	</div>
      <div class="wrapper_ans_pro">
      	<Div class="wrapped_inner_ans_pro">
          	
          	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  ">
          		<div class=" col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
								<div class="row">
									<div class=" col-lg-2 col-md-3 col-sm-3 col-xs-3">
										Comments
									</div>
									<div class=" col-lg-10 col-md-9 col-sm-9 col-xs-9">
										<textarea class="form-control" style="resize:none;" name="comments"><?php echo stripslashes($comments); ?></textarea>
									</div>
								</div>
							</div>
						</div>
         	
          	<div class="clearfix margin_adjustment_only "></div>
            <div class="clearfix margin_adjustment_only border-dashed"></div>
            <div class="clearfix margin_adjustment_only ">&nbsp;</div>
            
           	<div class="col-md-12 col-lg-2 col-xs-12 col-sm-12 padding_0">
							<label for="chbx_saline_lockStart" class="">
								<input type="checkbox" name="chbx_saline_lockStart" id="chbx_saline_lockStart" <?php if($chbx_saline_lockStart=='saline')echo "checked"; ?> value="saline" onClick="disp_one_hide_other_onchangeNewAdmin('ivSelection_id','lft_rgt_id','other_id','chbx_saline_lockStart','chbx_iv','iv_sub_id');"/>Start Saline Lock </label> 
							<label for="chbx_iv">
								<input type="checkbox" name="chbx_saline_lock" id="chbx_iv" <?php if($chbx_saline_lock=='iv')echo "checked"; ?> value="iv" onClick="disp_one_hide_other_onchangeNewAdmin('ivSelection_id','lft_rgt_id','other_id','chbx_saline_lockStart','chbx_iv','iv_sub_id');" />&nbsp;IV</label>
						</div>
           
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-10">
              <div class="row">
                
                <div class="col-md-2 col-lg-1 col-xs-3 col-sm-2 padding_0">
                  <select class="selectpicker form-control-pic" name="ivSelection" id="ivSelection_id" onChange="javascript:disp_one_hide_other_onchangeNewAdmin('ivSelection_id','lft_rgt_id','other_id','chbx_saline_lockStart','chbx_iv','iv_sub_id');">
                    <option value="">No IV</option>
                    <option value="hand" <?php if($ivSelection=='hand') echo "SELECTED"; ?>>Hand</option>
                    <option value="wrist" <?php if($ivSelection=='wrist') echo "SELECTED"; ?>>Wrist</option>
                    <option value="arm" <?php if($ivSelection=='arm') echo "SELECTED"; ?>>Arm</option>
                    <option value="antecubital" <?php if($ivSelection=='antecubital') echo "SELECTED"; ?>>Antecubital</option>
                    <option value="other" <?php if($ivSelection=='other') echo "SELECTED"; ?>>Other</option>
                  </select>
                </div>
                
                <div class="row col-md-10 col-lg-11 col-xs-9 col-sm-10" style="white-space:nowrap;">
                  
                  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="lft_rgt_id" style="display:<?php if($ivSelection=='' || $ivSelection=='other' || ($chbx_saline_lockStart!='saline' && $chbx_saline_lock!='iv')) echo "none"; ?>;">
                    
                    <div class="col-md-5 col-lg-5 col-xs-5 col-sm-5 padding_0  ">
                      <div class="col-md-2 col-lg-2 col-xs-3 col-sm-3 padding_6">
                        <input type="checkbox" name="ivSelectionSide" <?php if($ivSelectionSide=='right') echo "CHECKED"; ?> value="right" id="chbx_ec_right" onClick="javascript:checkSingleChkBox('chbx_ec_right','ivSelectionSide')"/><label>Right</label>
                      </div>
                      <div class="col-md-2 col-lg-2 col-xs-3 col-sm-3 padding_6" style="padding-left:5px; ">
                        
                        <input type="checkbox" name="ivSelectionSide"  <?php if($ivSelectionSide=='left') echo "CHECKED"; ?> value="left" id="chbx_ec_left" onClick="javascript:checkSingleChkBox('chbx_ec_left','ivSelectionSide')" /><label > Left</label>&nbsp;
                      </div>
                      <div class="col-md-4 col-lg-4 col-xs-2 col-sm-3 ">
                        <select class="selectpicker form-control-pic" name="gauge" id="gauge_id" onChange="if($(this).val() == 'other'){ $('#gauge_other_id').fadeIn('fast'); }else{$('#gauge_other_id').fadeOut('fast');}">
                          <option value="" >Select Gauge</option>
                          <option value="20g" <?php if($gauge=='20g') echo "SELECTED"; ?>>20g</option>
                          <option value="22g" <?php if($gauge=='22g') echo "SELECTED"; ?>>22g</option>
                          <option value="24g" <?php if($gauge=='24g') echo "SELECTED"; ?>>24g</option>
                          <option value="other" <?php if($gauge=='other') echo "SELECTED"; ?>>Other</option>
                        </select>	
                        <input class="form-control" type="text" name="gauge_other" id="gauge_other_id"  value="<?=$gauge_other?>" style="display:<?=($gauge == 'other' ? 'block' : 'none')?>; " placeholder="Others Gauge" />
                        
                      </div>
      <div class="col-md-4 col-lg-4 col-xs-4 col-sm-3 ">
                        <input type="text" class="form-control col-md-3 col-lg-2 col-xs-3 col-sm-3" name="txtbox_other_new" value="<?php echo $txtbox_other_new;?>" >
                      </div>
                    </div>
                    
                    
        <?php if($chbx_saline_lock=='iv' && $ivSelection!='' && $ivSelection!='other') { $iv_sub_id_display =  'block'; }else { $iv_sub_id_display = 'none'; } ?>
                    
                    <div id="iv_sub_id" class="col-md-7 col-lg-7 col-xs-7 col-sm-7" style="display:<?php echo $iv_sub_id_display; ?> ">
                      <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_6">
                        <input type="checkbox" name="chbx_KVO"  value="Yes" <?php if($chbx_KVO=='Yes') { echo "checked";  }?>>
                        <label>KVO</label>
                      </div>
                      <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_6">
                        <input type="checkbox" name="chbx_rate" value="Yes" <?php if($chbx_rate=='Yes') { echo "checked";  }?>>
                        <label >Rate</label>
                      </div>
                      <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 padding_6 " >
                        <div class="form-group" >
                          <input type="text" class="form-control" name="txtbox_rate" id="txtbox_rate" style="float:left;width:70%;display:inline-block;" value="<?php echo $txtbox_rate;?>"><label for="txtbox_rate"> /hr</label>
                          
                        </div>
                        
                      </div>
                      <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_6">
                        <input type="checkbox" name="chbx_flu"  value="Yes" <?php if($chbx_flu=='Yes') { echo "checked";  }?>>
                        <label>Flu&nbsp;</label>
                      </div>
                      <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_6">
                        <input type="text" class="form-control col-md-3 col-lg-2 col-xs-3 col-sm-3" name="txtbox_flu" value="<?php echo $txtbox_flu;?>" >
                      </div>
                    </div>
    
                  </div>
                  
                  <div id="other_id" style="display:<?php if($ivSelection=='other') {echo "block";}else {echo "none";} ?> ; " class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                    <textarea id="Field3" name="ivSelectionOther" class="form-control col-md-12 col-lg-12 col-xs-12 col-sm-12" ><?php echo stripslashes($ivSelectionOther) ; ?></textarea>
                  </div>
                
                </div>
                
              </div>
            </div>
            
            <div class="clearfix margin_adjustment_only "></div>
            <div class="clearfix margin_adjustment_only border-dashed"></div>
            <div class="clearfix margin_adjustment_only ">&nbsp;</div>
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  ">
          	
							<div class=" col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
								<div class="row">

								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 head_block">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9">&nbsp;</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 bold">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 "><h4 class="rob">Yes</h4></label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><h4 class="rob">No</h4></label>
									</span>
								</div>

								<div class="clearfix margin_adjustment_only ">&nbsp;</div>

								<div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bold">
										Food or Drink Today
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" name="chbx_fdt" id="chbx_fdt_yes" onClick="javascript:checkSingleChkBox('chbx_fdt_yes','chbx_fdt'),enable_chk_unchk_admin('chbx_fdt_yes','chbx_fdt_no','txtarea_list_food_take');" <?php if($foodDrinkToday=="Yes") { echo "checked"; }?> value="Yes" />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" name="chbx_fdt" id="chbx_fdt_no" value="No" onClick="javascript:checkSingleChkBox('chbx_fdt_no','chbx_fdt'),enable_chk_unchk_admin('chbx_fdt_yes','chbx_fdt_no','txtarea_list_food_take');" <?php if($foodDrinkToday=="No") { echo "checked"; }?> />
										</label>
									</span>
									
									<div  class="col-lg-9 col-md-9 col-sm-9 col-xs-9 ">
										<div class="well">
											<div class="row">
												<div class="col-lg-3 col-md-3 col-sm-2 col-xs-2 plr5 bold">
													<a data-placement="top" class="" style="color:#800080;cursor:pointer;" id="precomment_admin_id" onClick="return showFoodListAdminFn('txtarea_list_food_take', '', 'no', $(this).offset().left, parseInt($(this).offset().top - 185 )),document.getElementById('selected_frame_name_id').value='';"> List Food Taken	</a>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 text-center">
													<textarea id="txtarea_list_food_take" style="resize:none;" class="form-control" name="txtarea_list_food_take" <?php if($foodDrinkToday=="No" || $foodDrinkToday=="") { echo "disabled"; }?> tabindex="6"><?php echo stripslashes($listFoodTake);?></textarea>
												</div> <!-- Col-3 ends  -->
											</div>
										</div> 
									</div>
								</div>	

								<!-- LAB TEST -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bold">
										Lab Test 
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_lab_test_yes','chbx_lab_test')" value="Yes" name="chbx_lab_test" id="chbx_lab_test_yes" <?php if($labTest=="Yes") { echo "checked"; }?>  />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_lab_test_no','chbx_lab_test')" value="No" name="chbx_lab_test" id="chbx_lab_test_no" <?php if($labTest=="No") { echo "checked"; }?> />
										</label>
									</span>
								</div>

								<!-- EKG -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bold">
										EKG
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_ekg_yes','chbx_ekg')" value="Yes" name="chbx_ekg" id="chbx_ekg_yes" <?php if($ekg=="Yes") { echo "checked"; }?> />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_ekg_no','chbx_ekg')" value="No" name="chbx_ekg" id="chbx_ekg_no" <?php if($ekg=="No") { echo "checked"; }?> />
										</label>
									</span>
								</div>

								<!-- Consent Signed -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bold">
										Consent Signed
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_cons_sign_yes','chbx_cons_sign')" value="Yes" name="chbx_cons_sign" id="chbx_cons_sign_yes" <?php if($consentSign=="Yes") { echo "checked"; }?> />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_cons_sign_no','chbx_cons_sign')" value="No" name="chbx_cons_sign" id="chbx_cons_sign_no" <?php if($consentSign=="No") { echo "checked"; }?> />
										</label>
									</span>
								</div>

								<!-- H & P -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bold">
										H & P
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_h_p_yes','chbx_h_p')" value="Yes" name="chbx_h_p" id="chbx_h_p_yes" <?php if($hp=="Yes") { echo "checked"; }?> />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_h_p_no','chbx_h_p')" value="No" name="chbx_h_p" id="chbx_h_p_no" <?php if($hp=="No") { echo "checked"; }?> />
										</label>
									</span>
								</div>

								<!-- Admitted To Hospital in Past 30 Days -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bold">
										Admitted To Hospital in Past 30 Days
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox"  onClick="javascript:checkSingleChkBox('chbx_admit_to_hosp_yes','chbx_admit_to_hosp');disp_new_admin(this,'descr_11');" value="Yes" name="chbx_admit_to_hosp" id="chbx_admit_to_hosp_yes" <?php if($admitted2Hospital=="Yes") { echo "checked"; }?> class="<?php if($admitted2Hospital=="Yes") { echo "uncollapse"; }?>" />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_admit_to_hosp_no','chbx_admit_to_hosp');disp_none_new_admin(this,'descr_11');" value="No" name="chbx_admit_to_hosp" id="chbx_admit_to_hosp_no" <?php if($admitted2Hospital=="No") { echo "checked"; }?> />
										</label>
									</span>
								</div>

								<!-- Reason -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0 <?php if($admitted2Hospital<>"Yes") { echo "hide"; }?>" id="descr_11">
									<div  class="col-lg-9 col-md-9 col-sm-9 col-xs-9 ">
										<div class="well">
											<div class="row">
												<div class="col-lg-3 col-md-3 col-sm-2 col-xs-2 plr5">
													<label class="date_r">Reason</label>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 text-center">
													<textarea class="form-control" style="resize:none;" name="txtarea_admit_to_hosp" tabindex="6"><?php echo stripslashes($reason);?></textarea> 
												</div> <!-- Col-3 ends  -->
											</div>
										</div> 
									</div>
								</div>

								</div>
								
								<div class="clearfix margin_adjustment_only ">&nbsp;</div>
            
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
										<div class="row">
											<span class="col-lg-3 col-md-4 col-sm-3 col-xs-3 bold">
												<a style="color:#800080;cursor:pointer;" id="precomment_id" onClick="return showPreCommentsFnNewAdmin('preOpComments', '', 'no',$(this).offset().left, parseInt($(this).offset().top - 185 )),document.getElementById('selected_frame_name_id').value='';">Preoperative Comments</a>
											</span>
											<span class="col-lg-9 col-md-8 col-sm-9 col-xs-9">
												<textarea class="form-control" style="resize:none;" id="preOpComments" name="preOpComments"><?php echo stripslashes($preOpComments);?></textarea>
											</span>
										</div>
									</div>
									
									
									<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
										<div class="row">
											<label class="col-md-4 col-lg-4 col-xs-12 col-sm-12" for="relivedNurseIdPre">Relief Nurse</label>
											<div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
												<select class="selectpicker form-control" name="relivedNurseIdPre" id="relivedNurseIdPre" data-width="80%"> 
													<option value="">Select</option>	
													<?php
													$relivedNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
													$relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
													while($relivedNurseRow=imw_fetch_array($relivedNurseRes)) {
														$relivedSelectNurseID 			= $relivedNurseRow["usersId"];
														$relivedNurseName = $relivedNurseRow["lname"].", ".$relivedNurseRow["fname"]." ".$relivedNurseRow["mname"];
														$sel="";
														if($relivedNurseIdPre==$relivedSelectNurseID) {
															$sel = "selected";
														} 
														else {
															$sel = "";
														}

														if($relivedNurseRow["deleteStatus"]<>'Yes' || $relivedNurseId==$relivedSelectNurseID) {
													?>	
															<option value="<?php echo $relivedSelectNurseID;?>" <?php echo $sel;?>><?php echo $relivedNurseName;?></option>
													<?php
														}
													}
													?>
												</select>
											</div>
										</div>
									</div>
									
									
								</div>
								
							</div>
            
            
							<div class=" col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
								<div class="row">

								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-xs hidden-sm head_block">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9">&nbsp;</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 bold">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><h4 class="rob">Yes</h4></label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><h4 class="rob">No</h4></label>
									</span>
								</div>

								<div class="clearfix margin_adjustment_only ">&nbsp;</div>

								<!-- Health Questionnaire  -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
										Health Questionnaire 
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_hlt_ques_yes','chbx_hlt_ques');" value="Yes" name="chbx_hlt_ques" id="chbx_hlt_ques_yes" <?=($healthQuestionnaire == 'Yes' ? 'checked' : '')?> /> 
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_hlt_ques_no','chbx_hlt_ques')" value="No" name="chbx_hlt_ques" id="chbx_hlt_ques_no" <?php if($healthQuestionnaire=="No") { echo "checked"; }?> />
										</label>
									</span>
								</div>


								<!-- Standing Orders  -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
										Standing Orders 
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_stnd_odrs_yes','chbx_stnd_odrs'); " value="Yes" name="chbx_stnd_odrs" id="chbx_stnd_odrs_yes" <?php if($standingOrders=="Yes") { echo "checked"; }?> />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_stnd_odrs_no','chbx_stnd_odrs');" value="No" name="chbx_stnd_odrs" id="chbx_stnd_odrs_no" <?php if($standingOrders=="No") { echo "checked"; }?> />
										</label>
									</span>
								</div>

								<!-- Pat. Voided  -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
										Pat. Voided
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_pat_void_yes','chbx_pat_void')" value="Yes" name="chbx_pat_void" id="chbx_pat_void_yes" <?php if($patVoided=="Yes") { echo "checked"; }?> />

										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_pat_void_no','chbx_pat_void')" value="No" name="chbx_pat_void" id="chbx_pat_void_no" <?php if($patVoided=="No") { echo "checked"; }?> tabindex="7"/>

										</label>
									</span>
								</div>

								<!-- Hearing Aids  -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
										Hearing Aids
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_hearingAids_yes','chbx_hearingAids'),disp_new_admin(this,'descr_h_aid');" <?php if($hearingAids=='Yes') echo "CHECKED"; ?> name="chbx_hearingAids" value="Yes" id="chbx_hearingAids_yes"  />

										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_hearingAids_no','chbx_hearingAids'),disp_none_new_admin(this,'descr_h_aid');" <?php if($hearingAids=='No') echo "CHECKED"; ?>  name="chbx_hearingAids" value="No" id="chbx_hearingAids_no" />

										</label>
									</span>
								</div>

								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0 <?php if($hearingAids<>'Yes') echo "hide"; ?>" id="descr_h_aid">
									<div class="well">
											<div class="row">	
												<label class="date_r" for="removed">
												 <input class="" type="checkbox" onClick="checkyes_admin('chbx_hearingAids_yes','chbx_hearingAidsRemoved_yes','chbx_hearingAids_no'),checkSingleChkBox('chbx_hearingAidsRemoved_yes','chbx_hearingAidsRemoved')"  <?php if($hearingAidsRemoved=="Yes") echo "Checked";  ?> name="chbx_hearingAidsRemoved" value="Yes" id="chbx_hearingAidsRemoved_yes" /> Removed
												</label> &nbsp; &nbsp;
												 <label class="date_r" for="covered">
												 <input class="" type="checkbox" onClick="checkyes_admin('chbx_hearingAids_yes','chbx_hearingAidsRemoved_no','chbx_hearingAids_no'),checkSingleChkBox('chbx_hearingAidsRemoved_no','chbx_hearingAidsRemoved')" <?php if($hearingAidsRemoved=="No") echo "Checked";  ?> name="chbx_hearingAidsRemoved" value="No" id="chbx_hearingAidsRemoved_no" /> Covered
												</label>
											</div> 
									</div>		 
								</div>

								<!-- Denture  -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
										Denture
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_denture_yes','chbx_denture');disp_new_admin(this,'descr_h_den');" <?php if($denture=='Yes') echo "CHECKED"; ?> name="chbx_denture" value="Yes" id="chbx_denture_yes"  />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_denture_no','chbx_denture'); disp_none_new_admin(this,'descr_h_den');" <?php if($denture=='No') echo "CHECKED"; ?>  name="chbx_denture" value="No" id="chbx_denture_no" />
										</label>
									</span>
								</div>

								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0 <?php if($denture<>'Yes') echo "hide"; ?>" id="descr_h_den">
									<div class="well">
										 <div class="row">
											<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
												Removed
											</div>
											 <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8">
												 <label class="date_r"  for="chbx_dentureRemoved_yes">
														 <input class="" type="checkbox" onClick="checkyes_admin('chbx_denture_yes','chbx_dentureRemoved_yes','chbx_denture_no'),checkSingleChkBox('chbx_dentureRemoved_yes','chbx_dentureRemoved')" <?php if($dentureRemoved=="Yes") echo "Checked";  ?> name="chbx_dentureRemoved" value="Yes" id="chbx_dentureRemoved_yes" /> Yes
													</label> &nbsp; &nbsp;
													 <label class="date_r" for="no">
														 <input class="" type="checkbox" onClick="checkyes_admin('chbx_denture_yes','chbx_dentureRemoved_no','chbx_denture_no'),checkSingleChkBox('chbx_dentureRemoved_no','chbx_dentureRemoved')" <?php if($dentureRemoved=="No") echo "checked";  ?> name="chbx_dentureRemoved" value="No" id="chbx_dentureRemoved_no" /> No
													</label>        
											 </div>
										 </div>   	
									</div> 
								</div>

								<!-- Any Pain  -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
										Any Pain
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_anyPain_yes','chbx_anyPain');" value="Yes" name="chbx_anyPain" id="chbx_anyPain_yes" <?php if($anyPain=="Yes") { echo "checked"; }?> />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_anyPain_no','chbx_anyPain');" value="No" name="chbx_anyPain" id="chbx_anyPain_no" <?php if($anyPain=="No") { echo "checked"; }?> />
										</label>
									</span>
								</div>

								<!-- Pain Level  -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<div class="col-md-6 col-sm-12 col-xs-6 col-lg-6 padding_0">
										<div class="row">
											<div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
												<label class="date_r" for="Pain">
													Pain Level
												</label>		        
											</div>

											<div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
												<select class="form-control selectpicker" name="painLevel" id="Pain" data-width="100%"> 
													<option value=""></option>
														<?php
														for($i=0;$i<=10;$i++) {
														?>
															<option value="<?php echo $i;?>" <?php if($painLevel==$i) echo 'selected'; ?>><?php echo $i;?></option>
														<?php
														}
														?>
													</select>
												</select>
											</div>
										</div>
									</div>

									<div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
										<Div class="row">
											<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-right">
												<label class="date_r" for="location">
													 Location
													</label>		        
											</div>

											<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
													<input class="form-control" type="text"  name="painLocation" value="<?php echo $painLocation;?>" size="7" id="location"/>
											</div>
										</Div>
									</div> <!-- Col-3 ends  -->


								</div>

								<!-- Dr. Notified  -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">
									<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
										Dr. Notified
									</span>
									<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_doctorNotified_yes','chbx_doctorNotified');" value="Yes" name="chbx_doctorNotified" id="chbx_doctorNotified_yes" <?php if($doctorNotified=="Yes") { echo "checked"; }?> />
										</label>
										<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
											<input type="checkbox" onClick="javascript:checkSingleChkBox('chbx_doctorNotified_no','chbx_doctorNotified');"  value="No" name="chbx_doctorNotified" id="chbx_doctorNotified_no" <?php if($doctorNotified=="No") { echo "checked"; }?> tabindex="7" />
										</label>
									</span>
								</div>

								</div>
							</div>
            
        		</div> 
      	</Div>
     	</div><!-- Wrapper Ans Pro-->
    </div>
    
   	<!-- End P3 Fields --> 
    
		<div class="striped_row_ans ">
				<Div class="scanner_win new_s">
				 <h4><span>Post-Op Nursing</span></h4>
				</Div>
				<Div class="wrapper_ans_pro">

				<div class=" col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
					<div class="row">

						<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6 ">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<div class="caption caption2">
										<a style="color:#800080;cursor:pointer;" id="pos_op_site" onClick="return showPostSiteFnAdmin('postOpSite', '', 'no', $(this).offset().left, parseInt($(this).offset().top - 185 )),document.getElementById('selected_frame_name_id').value='';"><b>Post-Operative Site</b></a>
									</div>
								</div>
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<textarea id="postOpSite" class="form-control" style="resize:none;" name="postOpSite"><?php echo stripslashes($postOpSite);?></textarea>	
								</div>
							</div>	
						</div>

						<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<div class="caption caption2">
										<a id="nourishmentkind" style="color:#800080;cursor:pointer;" onClick="return showNourishmentKindAdmin('nourishKind', '', 'no', $(this).offset().left, parseInt($(this).offset().top - 185 )),document.getElementById('selected_frame_name_id').value='';"><b>Nourishment Kind</b></a>
									</div>
								</div>
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<textarea id="nourishKind" class="form-control" name="nourishKind" style="resize:none;"><?php echo stripslashes($nourishKind);?></textarea>
								</div>
							</div>
						</div>

						<div class="clearfix margin_adjustment_only">  </div>

						<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<a id="comment_id" style="color:#800080;cursor:pointer;" onClick="return showRecoveryCommentsFnAdmin('recoveryComments', '', 'no', $(this).offset().left, parseInt($(this).offset().top - 185 )),document.getElementById('selected_frame_name_id').value='';"><b>Comments</b></a>
								</div>
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<textarea id="recoveryComments" name="recoveryComments" class="form-control" style="resize:none;"><?php echo stripslashes($recoveryComments);?></textarea> 
								</div>
							</div>
						</div>

						<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<label for="relivedNurseId"> Relief Nurse</label>
								</div>
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<select class="selectpicker form-control" name="relivedNurseId" id="relivedNurseId"> 
										<option value="">Select</option>	
										<?php
											$relivedNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
											$relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
											while($relivedNurseRow=imw_fetch_array($relivedNurseRes)) {
												$relivedSelectNurseID = $relivedNurseRow["usersId"];
												$relivedNurseName = $relivedNurseRow["lname"].", ".$relivedNurseRow["fname"]." ".$relivedNurseRow["mname"];
												$sel="";
												if($relivedNurseId==$relivedSelectNurseID) {
													$sel = "selected";
												} 
												else {
													$sel = "";
												}
												if($relivedNurseRow["deleteStatus"]<>'Yes' || $relivedNurseId==$relivedSelectNurseID) {						
											?>	
													<option value="<?php echo $relivedSelectNurseID;?>" <?php echo $sel;?>><?php echo $relivedNurseName;?></option>
											<?php
												}
											}
										?>                                            
									</select>
								</div>
							</div>
						</div>
					</div>			
				</div>

				<div class=" col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="row">

							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 head_block">
								<h4 class="rob">IV Discontinued</h4>
							</div>

							<div class="clearfix margin_adjustment_only "></div>


							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"  >
								<label for="other_mental_status" class="text-nowrap">Other Mental Status</label>
								<textarea class="form-control" style="height:85px;" name="other_mental_status" id="other_mental_status" placeholder="Other Mental Status..."><?php echo $other_mental_status;?></textarea>		   
							</div>

							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"  >
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  >
										<label for="pt_label">
											<input type="checkbox" name="chbx_removedIntact" id="chbx_removedIntact_id" <?php if($removedIntact=="Yes") { echo "checked";}?> value="Yes" />&nbsp;Removed Intact/Pressure Dressing Applied
										</label>		   
									</div>

									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  >
										<label for="pt_label">
											<input type="checkbox" name="chbx_aox3" value="Yes" id="chbx_aox3_id" <?php if($patient_aox3=="Yes") { echo "checked";}?> />&nbsp;Patient Awake, Alert and Oriented times 3
										</label>		   
									</div>
                                    
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  >
										<label for="pt_label">
											<input type="checkbox" name="chbx_patient_transfer" value="Yes" id="chbx_patient_transfer_id" <?php if($patient_transfer=="Yes") { echo "checked";}?> />&nbsp;Patient Transferred To Hospital
										</label>		   
									</div>

									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  >
										<label for="chbx_relationship_id" data-toggle="collapse" data-target="#toggle_rel">
											<input type="checkbox" name="chbx_patientReleased2Adult" value="Yes" <?php if($patientReleased2Adult=="Yes") { echo "checked"; }?> id="chbx_relationship_id"/>&nbsp;Patient Discharged To Home Via
										</label>
										<Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 collapse padding_0 <?php echo ($patientReleased2Adult=='Yes' ? 'in':'');?>" id="toggle_rel">
											<Div class="row">
												<label class="date_r col-md-4 col-sm-4 col-xs-12 col-lg-4" for="relationlist_id">Relationship </label>	
												<label class="col-md-8 col-sm-8 col-xs-12 col-lg-8"> 
													<Select name="patientsRelation" class="selectpicker form-control" id="relationlist_id" onChange="javascript:disp_hide_id_admin('relationlist_id','txt_otherRelation_id');"> 
														<option value="Family" <?php if($patientsRelation=="Family") { echo "selected";}?>>Family</option>
														<option value="Husband" <?php if($patientsRelation=="Husband") { echo "selected";}?>>Husband</option>
														<option value="Wife" <?php if($patientsRelation=="Wife") { echo "selected";}?>>Wife</option>
														<option value="Son" <?php if($patientsRelation=="Son") { echo "selected";}?>>Son</option>
														<option value="Daughter" <?php if($patientsRelation=="Daughter") { echo "selected";}?>>Daughter</option>
														<option value="Sister" <?php if($patientsRelation=="Sister") { echo "selected";}?>>Sister</option>
														<option value="Brother" <?php if($patientsRelation=="Brother") { echo "selected";}?>>Brother</option>
														<option value="Mother" <?php if($patientsRelation=="Mother") { echo "selected";}?>>Mother</option>
														<option value="Father" <?php if($patientsRelation=="Father") { echo "selected";}?>>Father</option>
														<option value="Friend" <?php if($patientsRelation=="Friend") { echo "selected";}?>>Friend</option>
														<option value="Transportation Driver" <?php if($patientsRelation=="Transportation Driver") { echo "selected";}?>>Transportation Driver</option>
														<option value="other" <?php if($patientsRelation=="other") { echo "selected";}?>>Other</option>
													</Select> 
												</label>
												<?php if($patientsRelation=="other") { $patientsRelationOtherDisplay = "block";} else { $patientsRelationOtherDisplay = "none";} ?>
											</Div>     
											<div id="txt_otherRelation_id" class="well" style="display:<?php echo $patientsRelationOtherDisplay;?>">
												<input class="form-control" name="patientsRelationOther" placeholder="Other" type="text" value="<?php echo $patientsRelationOther; ?>" />
											</div>
										</Div>	   
									</div>
								</div>
						</div>	   
					</div>
				</div>     

				<div class="clearfix margin_adjustment_only"></div>
				<div class="clearfix margin_adjustment_only border-dashed"></div>

				<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-center">
					<label for="sign" class="">Signature of <?php echo $nurseName;?></label>
				</div>

				<div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
					<div class=" col-lg-4 col-md-4 col-sm-3 col-xs-3 text-center"></div>

					<div class=" col-lg-4 col-md-4 col-sm-6 col-xs-6 text-center">
						<div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="col-md-10 col-lg-10 col-xs-9 col-sm-9" ><!--style="cursor:crosshair"-->
								<div class="clearfix margin_adjustment_only"></div>
									<div class="img_sign_wrap">
									<?php
										if($isHTML5OK){
											$showNurseImgPath='';
											$nurse_profile_sign_path_encode='';
											if($nurse_profile_sign_path) {
												$showNurseImgPath = "/".$surgeryCenterDirectoryName."/admin/".$nurse_profile_sign_path;
												$nurse_profile_sign_path_encode = base64_encode($showNurseImgPath);	
											}
									?>
											<canvas id="sign" style="border:dashed 1px #333; " height="100" ></canvas>
											<input type="hidden" name="sig_datasign"  id="sig_datasign" value="<?php echo $nurse_profile_sign_path_encode;?>"/>
											<input type="hidden" name="sig_imgsign"  id="sig_imgsign" value="<?php echo $showNurseImgPath;?>" />
									<?php
										}	else {
									?>
											<span class="text_12 col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_0" style="background-color:#F1F4F0; ">
												<input type="hidden" name="elem_signature" value="<?php echo $elem_signature; ?>">
												<input type="hidden" name="nurse_profile_sign" id="nurse_profile_sign" value="<?php echo $nurse_profile_sign; ?>">
												<object type="application/x-java-applet" name="appNurse_signature" id="appNurse_signature" width="100%" height="100">
													<param name="code" value="MyCanvasColored.class" />
													<param name="codebase" value="../common/applet/" />
													<param name="archive" value="DrawApplet.jar" />
													<param name="bgImage" value="../images/white.jpg">
													<param name="strpixls" value="<?php echo $nurse_profile_sign;?>">
													<param name="mode" value="edit">
												</object>
											</span>
									<?php
										}
									?> 
									</div> <!--------	--------------- Full Inout col-12    ------------------------------>
							</div>	
							<div class="col-md-2 col-lg-2 col-xs-3 col-sm-3" style="max-height:100px; min-height:100px;">
								<img src="../images/eraser.gif" onClick="return getClear('sign');" style="position:absolute; bottom:0; left:0;">
							</div>
							<!-------------------Form Reg-----------------------------> 		
						</div>
					</div>

					<div class=" col-lg-4 col-md-4 col-sm-3 col-xs-3 text-center"></div>
				</div>

				<div class="clearfix margin_adjustment_only">  </div>    

				</Div><!-- Wrapper Ans Pro-->
		</div>   
    	   
 		<div class="clearfix margin_adjustment_only"></div>
      
 </Div>  <!-- SCrollable Yes -->      
</div>		<!-- All Admin Content Agree -->
                    
	
</form>	
<?php
}
include_once("../common/pre_comments_admin.php");
include_once("../common/post_site_admin.php");  //POST OP  NURSING
include_once("../common/nourishment_kind_admin.php"); //POST OP  NURSING
include_once("../common/recovery_comments_pop_admin.php")

?>

</body>
</html>
