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
<title>Anesthesiologist Profile</title>
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
function showEvaluationLocalAnesAdminFn(name1, name2, c, posLeft, posTop){	
	//alert(top.frames[0].frames[0].document.getElementById("evaluationLocalAnesEvaluationAdminDiv"));
	//top.frames[0].frames[0].document.getElementById("evaluationLocalAnesEvaluationAdminDiv").style.display = 'inline-block';
	//top.frames[0].frames[0].document.getElementById("evaluationLocalAnesEvaluationAdminDiv").style.left = posLeft+'px';
	//top.frames[0].frames[0].document.getElementById("evaluationLocalAnesEvaluationAdminDiv").style.top = posTop+'px';
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
	top.frames[0].frames[0].document.getElementById("ekgLocalAnesAdminDiv").style.display = 'inline-block';
	top.frames[0].frames[0].document.getElementById("ekgLocalAnesAdminDiv").style.left = posLeft+'px';
	top.frames[0].frames[0].document.getElementById("ekgLocalAnesAdminDiv").style.top = posTop+'px';
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
	var objElemSign = document.frmSaveAnesthesiaProfile.anesthesia_profile_sign;
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
	var coords = document.applets["appAnes_signature"].getSign();
	return coords;
}
function getclear_os(objElem){
	document.applets["appAnes_signature"].clearIt();
	changeColorThis(255,0,0);
	//document.applets["appAnes_signature"].onmouseout();
	get_App_Coords(objElem);
}
function changeColorThis(r,g,b){				
	document.applets['appAnes_signature'].setDrawColor(r,g,b);								
}
//Applet


function enableDisableHonanFn() {
	/*
	var NoneHonanBalloon = document.frmSaveAnesthesiaProfile.chbx_NoneHonanBalloon;
	if(NoneHonanBalloon) {
		
		if(NoneHonanBalloon.checked==true) {	
			document.frmSaveAnesthesiaProfile.honanballon.value='';
			document.frmSaveAnesthesiaProfile.honanBallonAnother.value='';
			
			document.frmSaveAnesthesiaProfile.honanballon.disabled=true;
			document.frmSaveAnesthesiaProfile.honanBallonAnother.disabled=true;
		}else {
			document.frmSaveAnesthesiaProfile.honanballon.disabled=false;
			document.frmSaveAnesthesiaProfile.honanBallonAnother.disabled=false;
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

</script>
</head>

<body>
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$anesthesiologistList = $_REQUEST['anesthesiologistList'];
$saveRecord = $_POST['saveRecord'];

if($saveRecord=='Yes') {
	unset($arrayRecord);
	
	$arrayRecord['anesthesiologistId'] 						= $_REQUEST['anesthesiologistId'];
	
	$arrayRecord['patientInterviewed'] 						= $_REQUEST['patientInterviewed'];
	$arrayRecord['chartNotesReviewed'] 						= $_REQUEST['chartNotesReviewed'];
	$arrayRecord['procedurePrimaryVerified'] 				= $_REQUEST['procedurePrimaryVerified'];
	$arrayRecord['procedureSecondaryVerified'] 				= $_REQUEST['procedureSecondaryVerified'];
	$arrayRecord['siteVerified'] 							= $_REQUEST['siteVerified'];
	$arrayRecord['evaluation2'] 							= addslashes($_REQUEST['evaluation2']);
	$arrayRecord['dentation'] 								= addslashes($_REQUEST['dentation']);
	
	$arrayRecord['medsTakeToday'] 							= $_REQUEST['medsTakeToday'];
	$arrayRecord['ptMedication'] 							= $_REQUEST['ptMedication'];
	
	$arrayRecord['fpExamPerformed'] 						= $_REQUEST['fpExamPerformed'];
	$arrayRecord['ansComment'] 								= addslashes($_REQUEST['ansComment']);
	
	$arrayRecord['copyBaseLineVitalSigns'] 					= $_REQUEST['copyBaseLineVitalSigns'];
	$arrayRecord['npo'] 									= $_REQUEST['npo'];	
	$arrayRecord['stableCardiPlumFunction'] 				= $_REQUEST['stableCardiPlumFunction'];
	$arrayRecord['planAnesthesia'] 							= $_REQUEST['planAnesthesia'];
	$arrayRecord['allQuesAnswered'] 						= $_REQUEST['allQuesAnswered'];
	
	$arrayRecord['txtInterOpDrugs1'] 						= $_REQUEST['txtInterOpDrugs1'];
	$arrayRecord['txtInterOpDrugs2'] 						= $_REQUEST['txtInterOpDrugs2'];
	
	$arrayRecord['routineMonitorApplied'] 					= $_REQUEST['routineMonitorApplied'];
	$arrayRecord['hide_anesthesia_grid'] 					= $_REQUEST['hide_anesthesia_grid'];
	$arrayRecord['o2lpm_1'] 								= $_REQUEST['o2lpm_1'];
	$arrayRecord['o2lpm_count'] 							= $_REQUEST['o2lpm_count'];

	$ekgBigRowValueSub 										= $_REQUEST['ekgBigRowValue'];
	$ekgBigRowValue 										= addslashes(substr($ekgBigRowValueSub,0,33)); 
	
	$arrayRecord['ekgBigRowValue'] 							= $ekgBigRowValue;
	
	$arrayRecord['honanballon'] 							= $_REQUEST['honanballon'];
	$arrayRecord['honanBallonAnother'] 						= $_REQUEST['honanBallonAnother'];
	$arrayRecord['NoneHonanBalloon'] 						= $_REQUEST['chbx_NoneHonanBalloon'];
	$arrayRecord['digital'] 								= $_REQUEST['digital'];
	
	//START
	$arrayRecord['Topicaltopical4PercentLidocaine'] 		= $_REQUEST['chbx_Topicaltopical4PercentLidocaine'];
	$arrayRecord['TopicalIntracameral'] 					= $_REQUEST['TopicalIntracameral'];
	$arrayRecord['TopicalIntracameral1percentLidocaine'] 	= $_REQUEST['chbx_TopicalIntracameral1percentLidocaine'];
	$arrayRecord['TopicalPeribulbar'] 						= $_REQUEST['TopicalPeribulbar'];
	$arrayRecord['TopicalPeribulbar2percentLidocaine'] 		= $_REQUEST['chbx_TopicalPeribulbar2percentLidocaine'];
	$arrayRecord['TopicalRetrobulbar'] 						= $_REQUEST['TopicalRetrobulbar'];
	$arrayRecord['TopicalRetrobulbar4percentLidocaine'] 	= $_REQUEST['chbx_TopicalRetrobulbar4percentLidocaine'];
	$arrayRecord['TopicalHyalauronidase4percentLidocaine'] 	= $_REQUEST['chbx_TopicalHyalauronidase4percentLidocaine'];
	$arrayRecord['TopicalVanLindr'] 						= $_REQUEST['TopicalVanLindr'];
	$arrayRecord['TopicalVanLindrHalfPercentLidocaine'] 	= $_REQUEST['chbx_TopicalVanLindrHalfPercentLidocaine'];
	$arrayRecord['topical_bupivacaine75'] 	= $_REQUEST['topical_bupivacaine75'];
	$arrayRecord['topical_marcaine75'] 	= $_REQUEST['topical_marcaine75'];
	$arrayRecord['TopicallidTxt'] 							= $_REQUEST['TopicallidTxt'];
	$arrayRecord['Topicallid'] 								= $_REQUEST['Topicallid'];
	$arrayRecord['TopicallidEpi5ug'] 						= $_REQUEST['chbx_TopicallidEpi5ug'];
	$arrayRecord['TopicalotherRegionalAnesthesiaTxt1'] 		= $_REQUEST['TopicalotherRegionalAnesthesiaTxt1'];
	$arrayRecord['TopicalotherRegionalAnesthesiaDrop'] 		= $_REQUEST['TopicalotherRegionalAnesthesiaDrop'];
	$arrayRecord['TopicalotherRegionalAnesthesiaWydase15u'] = $_REQUEST['chbx_TopicalotherRegionalAnesthesiaWydase15u'];
	$arrayRecord['TopicalotherRegionalAnesthesiaTxt2'] 		= addslashes($_REQUEST['TopicalotherRegionalAnesthesiaTxt2']);
	
	$arrayRecord['TopicalAspiration'] 						= $_REQUEST['TopicalAspiration'];
	$arrayRecord['TopicalFull'] 							= $_REQUEST['TopicalFull'];
	$arrayRecord['TopicalBeforeInjection'] 					= $_REQUEST['TopicalBeforeInjection'];
	$arrayRecord['TopicalRockNegative'] 					= $_REQUEST['TopicalRockNegative'];
	
	$arrayRecord['Block1topical4PercentLidocaine'] 			= $_REQUEST['chbx_Block1topical4PercentLidocaine'];
	$arrayRecord['Block1Intracameral'] 						= $_REQUEST['Block1Intracameral'];
	$arrayRecord['Block1Intracameral1percentLidocaine'] 	= $_REQUEST['chbx_Block1Intracameral1percentLidocaine'];
	$arrayRecord['Block1Peribulbar'] 						= $_REQUEST['Block1Peribulbar'];
	$arrayRecord['Block1Peribulbar2percentLidocaine'] 		= $_REQUEST['chbx_Block1Peribulbar2percentLidocaine'];
	$arrayRecord['Block1Retrobulbar'] 						= $_REQUEST['Block1Retrobulbar'];
	$arrayRecord['Block1Retrobulbar4percentLidocaine'] 		= $_REQUEST['chbx_Block1Retrobulbar4percentLidocaine'];
	$arrayRecord['Block1Hyalauronidase4percentLidocaine'] 	= $_REQUEST['chbx_Block1Hyalauronidase4percentLidocaine'];
	$arrayRecord['Block1VanLindr'] 							= $_REQUEST['Block1VanLindr'];
	$arrayRecord['Block1VanLindrHalfPercentLidocaine'] 		= $_REQUEST['chbx_Block1VanLindrHalfPercentLidocaine'];
	$arrayRecord['block1_bupivacaine75'] 	= $_REQUEST['block1_bupivacaine75'];
	$arrayRecord['block1_marcaine75'] 	= $_REQUEST['block1_marcaine75'];
	$arrayRecord['Block1lidTxt'] 							= $_REQUEST['Block1lidTxt'];
	$arrayRecord['Block1lid'] 								= $_REQUEST['Block1lid'];
	$arrayRecord['Block1lidEpi5ug'] 						= $_REQUEST['chbx_Block1lidEpi5ug'];
	$arrayRecord['Block1otherRegionalAnesthesiaTxt1'] 		= $_REQUEST['Block1otherRegionalAnesthesiaTxt1'];
	$arrayRecord['Block1otherRegionalAnesthesiaDrop'] 		= $_REQUEST['Block1otherRegionalAnesthesiaDrop'];
	$arrayRecord['Block1otherRegionalAnesthesiaWydase15u'] 	= $_REQUEST['chbx_Block1otherRegionalAnesthesiaWydase15u'];
	$arrayRecord['Block1otherRegionalAnesthesiaTxt2'] 		= addslashes($_REQUEST['Block1otherRegionalAnesthesiaTxt2']);

	$arrayRecord['Block1Aspiration'] 						= $_REQUEST['Block1Aspiration'];
	$arrayRecord['Block1Full'] 								= $_REQUEST['Block1Full'];
	$arrayRecord['Block1BeforeInjection'] 					= $_REQUEST['Block1BeforeInjection'];
	$arrayRecord['Block1RockNegative'] 						= $_REQUEST['Block1RockNegative'];
	$arrayRecord['Block2Aspiration'] 						= $_REQUEST['Block2Aspiration'];
	$arrayRecord['Block2Full'] 								= $_REQUEST['Block2Full'];
	$arrayRecord['Block2BeforeInjection'] 					= $_REQUEST['Block2BeforeInjection'];
	$arrayRecord['Block2RockNegative'] 						= $_REQUEST['Block2RockNegative'];
		
	
	$arrayRecord['Block2topical4PercentLidocaine'] 			= $_REQUEST['chbx_Block2topical4PercentLidocaine'];
	$arrayRecord['Block2Intracameral'] 						= $_REQUEST['Block2Intracameral'];
	$arrayRecord['Block2Intracameral1percentLidocaine'] 	= $_REQUEST['chbx_Block2Intracameral1percentLidocaine'];
	$arrayRecord['Block2Peribulbar'] 						= $_REQUEST['Block2Peribulbar'];
	$arrayRecord['Block2Peribulbar2percentLidocaine'] 		= $_REQUEST['chbx_Block2Peribulbar2percentLidocaine'];
	$arrayRecord['Block2Retrobulbar'] 						= $_REQUEST['Block2Retrobulbar'];
	$arrayRecord['Block2Retrobulbar4percentLidocaine'] 		= $_REQUEST['chbx_Block2Retrobulbar4percentLidocaine'];
	$arrayRecord['Block2Hyalauronidase4percentLidocaine'] 	= $_REQUEST['chbx_Block2Hyalauronidase4percentLidocaine'];
	$arrayRecord['Block2VanLindr'] 							= $_REQUEST['Block2VanLindr'];
	$arrayRecord['Block2VanLindrHalfPercentLidocaine'] 		= $_REQUEST['chbx_Block2VanLindrHalfPercentLidocaine'];
	$arrayRecord['block2_bupivacaine75'] 	= $_REQUEST['block2_bupivacaine75'];
	$arrayRecord['block2_marcaine75'] 	= $_REQUEST['block2_marcaine75'];
	$arrayRecord['Block2lidTxt'] 							= $_REQUEST['Block2lidTxt'];
	$arrayRecord['Block2lid'] 								= $_REQUEST['Block2lid'];
	$arrayRecord['Block2lidEpi5ug'] 						= $_REQUEST['chbx_Block2lidEpi5ug'];
	$arrayRecord['Block2otherRegionalAnesthesiaTxt1'] 		= $_REQUEST['Block2otherRegionalAnesthesiaTxt1'];
	$arrayRecord['Block2otherRegionalAnesthesiaDrop'] 		= $_REQUEST['Block2otherRegionalAnesthesiaDrop'];
	$arrayRecord['Block2otherRegionalAnesthesiaWydase15u'] 	= $_REQUEST['chbx_Block2otherRegionalAnesthesiaWydase15u'];
	$arrayRecord['Block2otherRegionalAnesthesiaTxt2'] 		= addslashes($_REQUEST['Block2otherRegionalAnesthesiaTxt2']);
	//END
	
	$arrayRecord['anyKnowAnestheticComplication'] 			= $_REQUEST['anyKnowAnestheticComplication'];
	$arrayRecord['stableCardiPlumFunction2'] 				= $_REQUEST['stableCardiPlumFunction2'];
	$arrayRecord['satisfactoryCondition4Discharge'] 		= $_REQUEST['satisfactoryCondition4Discharge'];
	$arrayRecord['evaluation'] 								= addslashes($_REQUEST['evaluation']);
	/*
	$arrayRecord['chbx_enable_postop_desc'] 				= addslashes($_REQUEST['chbx_enable_postop_desc']);
	$arrayRecord['chbx_vss'] 								= addslashes($_REQUEST['chbx_vss']);
	$arrayRecord['chbx_atsf'] 								= addslashes($_REQUEST['chbx_atsf']);
	$arrayRecord['chbx_pa'] 								= addslashes($_REQUEST['chbx_pa']);
	$arrayRecord['chbx_nausea'] 							= addslashes($_REQUEST['chbx_nausea']);
	$arrayRecord['chbx_vomiting'] 							= addslashes($_REQUEST['chbx_vomiting']);
	$arrayRecord['chbx_dizziness'] 							= addslashes($_REQUEST['chbx_dizziness']);
	$arrayRecord['chbx_rd'] 								= addslashes($_REQUEST['chbx_rd']);
	$arrayRecord['chbx_aao'] 								= addslashes($_REQUEST['chbx_aao']);
	$arrayRecord['chbx_ddai'] 								= addslashes($_REQUEST['chbx_ddai']);
	$arrayRecord['chbx_pv'] 								= addslashes($_REQUEST['chbx_pv']);
	$arrayRecord['chbx_rtpog'] 								= addslashes($_REQUEST['chbx_rtpog']);
	$arrayRecord['chbx_pain'] 								= addslashes($_REQUEST['chbx_pain']);
	*/
	$arrayRecord['remarks'] 								= addslashes($_REQUEST['remarks']);
	
	$arrayRecord['confirmIPPSC_signin']		= addslashes($_REQUEST['chbx_ipp']);
	$arrayRecord['siteMarked'] 						= addslashes($_REQUEST['chbx_smpp']);
	$arrayRecord['patientAllergies'] 			= addslashes($_REQUEST['chbx_pa']);	
	$arrayRecord['difficultAirway']				= addslashes($_REQUEST['chbx_dar']);
	$arrayRecord['anesthesiaSafety']			= addslashes($_REQUEST['chbx_asc']);	
	$arrayRecord['allMembersTeam'] 				= addslashes($_REQUEST['chbx_adcpc']);
	$arrayRecord['riskBloodLoss']					= addslashes($_REQUEST['chbx_rbl']);	
	$arrayRecord['bloodLossUnits'] 				= ($_REQUEST['chbx_rbl'] == 'Yes') ? addslashes($_REQUEST['rbl_no_of_units']) : '' ;
	
	
	$anesthesia_profile_sign 								= $_REQUEST['anesthesia_profile_sign'];
	if(($anesthesia_profile_sign == '255-0-0:;') || ($anesthesia_profile_sign == '0-0-0:;') || ($anesthesia_profile_sign == '0-0-0:;;') || ($anesthesia_profile_sign == '255-0-0:;;') || ($anesthesia_profile_sign == '255-0-0:;;;') || ($anesthesia_profile_sign == '255-0-0:;0-0-0:;') || ($anesthesia_profile_sign == '255-0-0:;0-0-0:;;')){
		$anesthesia_profile_sign = '';
	}
	
	if($isHTML5OK) {
		$arrayRecord['anesthesia_profile_sign_path'] = $objManageData->save_user_image($_REQUEST,$_REQUEST['anesthesiologistId'],$_REQUEST['anes_fname'],'anes_sign');
	}else{
		$arrayRecord['anesthesia_profile_sign'] = $anesthesia_profile_sign;	
	}
	
	
	$chkAnesthesiologistIdDetails = $objManageData->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId', $_REQUEST['anesthesiologistId']);
	
	if($chkAnesthesiologistIdDetails){
		$c=$objManageData->UpdateRecord($arrayRecord, 'anesthesia_profile_tbl', 'anesthesiologistId', $_REQUEST['anesthesiologistId']);
	}else{
		$d=$objManageData->addRecords($arrayRecord, 'anesthesia_profile_tbl');
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
	$anesthesiologistDetails = $objManageData->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId', $anesthesiologistList);
	if($anesthesiologistDetails) {
		//extract($anesthesiologistDetails); 
		$anesthesiologistId 					= $anesthesiologistDetails->anesthesiologistId;
		$patientInterviewed 					= $anesthesiologistDetails->patientInterviewed;
		$chartNotesReviewed 					= $anesthesiologistDetails->chartNotesReviewed;
		$procedurePrimaryVerified 				= $anesthesiologistDetails->procedurePrimaryVerified;
		$procedureSecondaryVerified 			= $anesthesiologistDetails->procedureSecondaryVerified;
		$siteVerified 							= $anesthesiologistDetails->siteVerified;
		$evaluation2 							= $anesthesiologistDetails->evaluation2;
		$dentation 								= $anesthesiologistDetails->dentation;
		$medsTakeToday 							= $anesthesiologistDetails->medsTakeToday;
		$ptMedication 							= $anesthesiologistDetails->ptMedication;
		$copyBaseLineVitalSigns 				= $anesthesiologistDetails->copyBaseLineVitalSigns;
		$npo 									= $anesthesiologistDetails->npo;
		$fpExamPerformed 						= $anesthesiologistDetails->fpExamPerformed;
		$ansComment 							= $anesthesiologistDetails->ansComment;		
		$txtInterOpDrugs1 						= $anesthesiologistDetails->txtInterOpDrugs1;
		$txtInterOpDrugs2 						= $anesthesiologistDetails->txtInterOpDrugs2;
		
		$stableCardiPlumFunction 				= $anesthesiologistDetails->stableCardiPlumFunction;
		$planAnesthesia 						= $anesthesiologistDetails->planAnesthesia;
		$allQuesAnswered 						= $anesthesiologistDetails->allQuesAnswered;
		
		$routineMonitorApplied 					= $anesthesiologistDetails->routineMonitorApplied;
		$hide_anesthesia_grid 					= $anesthesiologistDetails->hide_anesthesia_grid;
		$o2lpm_1 								= $anesthesiologistDetails->o2lpm_1;
		$o2lpm_count 							= $anesthesiologistDetails->o2lpm_count;
		$ekgBigRowValue 						= $anesthesiologistDetails->ekgBigRowValue;
		
		$honanballon 							= $anesthesiologistDetails->honanballon;
		$honanBallonAnother 					= $anesthesiologistDetails->honanBallonAnother;
		$NoneHonanBalloon 						= $anesthesiologistDetails->NoneHonanBalloon;
		$digital 								= $anesthesiologistDetails->digital;
		
		$Topicaltopical4PercentLidocaine 		= $anesthesiologistDetails->Topicaltopical4PercentLidocaine;
		$TopicalIntracameral 					= $anesthesiologistDetails->TopicalIntracameral;
		$TopicalIntracameral1percentLidocaine 	= $anesthesiologistDetails->TopicalIntracameral1percentLidocaine;
		$TopicalPeribulbar 						= $anesthesiologistDetails->TopicalPeribulbar;
		$TopicalPeribulbar2percentLidocaine 	= $anesthesiologistDetails->TopicalPeribulbar2percentLidocaine;
		$TopicalRetrobulbar 					= $anesthesiologistDetails->TopicalRetrobulbar;
		$TopicalRetrobulbar4percentLidocaine 	= $anesthesiologistDetails->TopicalRetrobulbar4percentLidocaine;
		$TopicalHyalauronidase4percentLidocaine = $anesthesiologistDetails->TopicalHyalauronidase4percentLidocaine;
		$TopicalVanLindr 						= $anesthesiologistDetails->TopicalVanLindr;
		$TopicalVanLindrHalfPercentLidocaine 	= $anesthesiologistDetails->TopicalVanLindrHalfPercentLidocaine;
		$topical_bupivacaine75 	= $anesthesiologistDetails->topical_bupivacaine75;
		$topical_marcaine75 	= $anesthesiologistDetails->topical_marcaine75;
		$TopicallidTxt 							= $anesthesiologistDetails->TopicallidTxt;
		$Topicallid 							= $anesthesiologistDetails->Topicallid;
		$TopicallidEpi5ug 						= $anesthesiologistDetails->TopicallidEpi5ug;
		$TopicalotherRegionalAnesthesiaTxt1 	= $anesthesiologistDetails->TopicalotherRegionalAnesthesiaTxt1;
		$TopicalotherRegionalAnesthesiaDrop 	= $anesthesiologistDetails->TopicalotherRegionalAnesthesiaDrop;
		$TopicalotherRegionalAnesthesiaWydase15u= $anesthesiologistDetails->TopicalotherRegionalAnesthesiaWydase15u;
		$TopicalotherRegionalAnesthesiaTxt2 	= $anesthesiologistDetails->TopicalotherRegionalAnesthesiaTxt2;
		
		$TopicalAspiration 						= $anesthesiologistDetails->TopicalAspiration;
		$TopicalFull 							= $anesthesiologistDetails->TopicalFull;
		$TopicalBeforeInjection 				= $anesthesiologistDetails->TopicalBeforeInjection;
		$TopicalRockNegative 					= $anesthesiologistDetails->TopicalRockNegative;
	
		$Block1topical4PercentLidocaine 		= $anesthesiologistDetails->Block1topical4PercentLidocaine;
		$Block1Intracameral 					= $anesthesiologistDetails->Block1Intracameral;
		$Block1Intracameral1percentLidocaine 	= $anesthesiologistDetails->Block1Intracameral1percentLidocaine;
		$Block1Peribulbar 						= $anesthesiologistDetails->Block1Peribulbar;
		$Block1Peribulbar2percentLidocaine 		= $anesthesiologistDetails->Block1Peribulbar2percentLidocaine;
		$Block1Retrobulbar 						= $anesthesiologistDetails->Block1Retrobulbar;
		$Block1Retrobulbar4percentLidocaine 	= $anesthesiologistDetails->Block1Retrobulbar4percentLidocaine;
		$Block1Hyalauronidase4percentLidocaine 	= $anesthesiologistDetails->Block1Hyalauronidase4percentLidocaine;
		$Block1VanLindr 						= $anesthesiologistDetails->Block1VanLindr;
		$Block1VanLindrHalfPercentLidocaine 	= $anesthesiologistDetails->Block1VanLindrHalfPercentLidocaine;
		$block1_bupivacaine75 	= $anesthesiologistDetails->block1_bupivacaine75;
		$block1_marcaine75 	= $anesthesiologistDetails->block1_marcaine75;
		$Block1lidTxt 							= $anesthesiologistDetails->Block1lidTxt;
		$Block1lid 								= $anesthesiologistDetails->Block1lid;
		$Block1lidEpi5ug 						= $anesthesiologistDetails->Block1lidEpi5ug;
		$Block1otherRegionalAnesthesiaTxt1 		= $anesthesiologistDetails->Block1otherRegionalAnesthesiaTxt1;
		$Block1otherRegionalAnesthesiaDrop 		= $anesthesiologistDetails->Block1otherRegionalAnesthesiaDrop;
		$Block1otherRegionalAnesthesiaWydase15u = $anesthesiologistDetails->Block1otherRegionalAnesthesiaWydase15u;
		$Block1otherRegionalAnesthesiaTxt2 		= $anesthesiologistDetails->Block1otherRegionalAnesthesiaTxt2;
		
		$Block1Aspiration 						= $anesthesiologistDetails->Block1Aspiration;
		$Block1Full 							= $anesthesiologistDetails->Block1Full;
		$Block1BeforeInjection 					= $anesthesiologistDetails->Block1BeforeInjection;
		$Block1RockNegative 					= $anesthesiologistDetails->Block1RockNegative;
		
		$Block2Aspiration 						= $anesthesiologistDetails->Block2Aspiration;
		$Block2Full 							= $anesthesiologistDetails->Block2Full;
		$Block2BeforeInjection 					= $anesthesiologistDetails->Block2BeforeInjection;
		$Block2RockNegative 					= $anesthesiologistDetails->Block2RockNegative;
		
		$Block2topical4PercentLidocaine 		= $anesthesiologistDetails->Block2topical4PercentLidocaine;
		$Block2Intracameral 					= $anesthesiologistDetails->Block2Intracameral;
		$Block2Intracameral1percentLidocaine 	= $anesthesiologistDetails->Block2Intracameral1percentLidocaine;
		$Block2Peribulbar						= $anesthesiologistDetails->Block2Peribulbar;
		$Block2Peribulbar2percentLidocaine 		= $anesthesiologistDetails->Block2Peribulbar2percentLidocaine;
		$Block2Retrobulbar 						= $anesthesiologistDetails->Block2Retrobulbar;
		$Block2Retrobulbar4percentLidocaine 	= $anesthesiologistDetails->Block2Retrobulbar4percentLidocaine;
		$Block2Hyalauronidase4percentLidocaine 	= $anesthesiologistDetails->Block2Hyalauronidase4percentLidocaine;
		$Block2VanLindr 						= $anesthesiologistDetails->Block2VanLindr;
		$Block2VanLindrHalfPercentLidocaine 	= $anesthesiologistDetails->Block2VanLindrHalfPercentLidocaine;
		$block2_bupivacaine75 	= $anesthesiologistDetails->block2_bupivacaine75;
		$block2_marcaine75 	= $anesthesiologistDetails->block2_marcaine75;
		$Block2lidTxt 							= $anesthesiologistDetails->Block2lidTxt;
		$Block2lid 								= $anesthesiologistDetails->Block2lid;
		$Block2lidEpi5ug 						= $anesthesiologistDetails->Block2lidEpi5ug;
		$Block2otherRegionalAnesthesiaTxt1 		= $anesthesiologistDetails->Block2otherRegionalAnesthesiaTxt1;
		$Block2otherRegionalAnesthesiaDrop 		= $anesthesiologistDetails->Block2otherRegionalAnesthesiaDrop;
		$Block2otherRegionalAnesthesiaWydase15u = $anesthesiologistDetails->Block2otherRegionalAnesthesiaWydase15u;
		$Block2otherRegionalAnesthesiaTxt2 		= $anesthesiologistDetails->Block2otherRegionalAnesthesiaTxt2;
		
		$anyKnowAnestheticComplication 			= $anesthesiologistDetails->anyKnowAnestheticComplication;
		$stableCardiPlumFunction2 				= $anesthesiologistDetails->stableCardiPlumFunction2;
		$satisfactoryCondition4Discharge 		= $anesthesiologistDetails->satisfactoryCondition4Discharge;
		$evaluation 							= $anesthesiologistDetails->evaluation;
		/*
		$chbx_enable_postop_desc 				= $anesthesiologistDetails->chbx_enable_postop_desc;
		$chbx_vss 								= $anesthesiologistDetails->chbx_vss;
		$chbx_atsf 								= $anesthesiologistDetails->chbx_atsf;
		$chbx_pa 								= $anesthesiologistDetails->chbx_pa;
		$chbx_nausea 							= $anesthesiologistDetails->chbx_nausea;
		$chbx_vomiting 							= $anesthesiologistDetails->chbx_vomiting;
		$chbx_dizziness 						= $anesthesiologistDetails->chbx_dizziness;
		$chbx_rd 								= $anesthesiologistDetails->chbx_rd;
		$chbx_aao 								= $anesthesiologistDetails->chbx_aao;
		$chbx_ddai 								= $anesthesiologistDetails->chbx_ddai;
		$chbx_pv 								= $anesthesiologistDetails->chbx_pv;
		$chbx_rtpog 							= $anesthesiologistDetails->chbx_rtpog;
		$chbx_pain 								= $anesthesiologistDetails->chbx_pain;
		*/
		$remarks 								= $anesthesiologistDetails->remarks;
		
		$anesthesia_profile_sign 				= $anesthesiologistDetails->anesthesia_profile_sign;
		$anesthesia_profile_sign_path			= $anesthesiologistDetails->anesthesia_profile_sign_path;
		
		$confirmIPPSC_signin		= stripslashes($anesthesiologistDetails->confirmIPPSC_signin);
		$siteMarked 						= stripslashes($anesthesiologistDetails->siteMarked);
		$patientAllergies 			= stripslashes($anesthesiologistDetails->patientAllergies);	
		$difficultAirway				= stripslashes($anesthesiologistDetails->difficultAirway);
		$anesthesiaSafety				= stripslashes($anesthesiologistDetails->anesthesiaSafety);	
		$allMembersTeam 				= stripslashes($anesthesiologistDetails->allMembersTeam);
		$riskBloodLoss					= stripslashes($anesthesiologistDetails->riskBloodLoss);	
		$bloodLossUnits	 				= stripslashes($anesthesiologistDetails->bloodLossUnits);
	
	}	
//END VIEW RECORD FROM DATABASE

	if($anesthesiologistList) {
	
		//GET Anesthesiologist Name
		$userAnesthesiologistNameDetails = $objManageData->getRowRecord('users', 'usersId', $anesthesiologistList);
		$anesthesiologistName = $userAnesthesiologistNameDetails->lname.', '.$userAnesthesiologistNameDetails->fname; 
		//END GET Anesthesiologist Name
	?>	
    
<form name="frmSaveAnesthesiaProfile" class="wufoo topLabel" enctype="multipart/form-data" action="anesthesia_profile_save.php" method="post">
<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
<input type="hidden" name="divId" id="divId">
<input type="hidden" name="counter" id="counter">
<input type="hidden" name="secondaryValues" id="secondaryValues">
<input type="hidden" name="tertiaryValues" id="tertiaryValues">
<input type="hidden" name="anesthesiologistList" id="anesthesiologistList" value="<?php echo $anesthesiologistList;?>">
<input type="hidden" name="anesthesiologistId" id="anesthesiologistId" value="<?php echo $anesthesiologistList;?>">
<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
<input type="hidden" name="saveRecord" id="saveRecord" value="Yes">
<input type="hidden" name="anes_fname" id="anes_fname" value="<?php echo $userAnesthesiologistNameDetails->fname;?>">
<input type="hidden" id="bp" name="bp_hidden">

<div class="all_admin_content_agree wrap_inside_admin">	         
  <Div class="wrap_inside_admin scrollable_yes">
  	
    <!-- Start P3 Fields -->
    
  	<div class="striped_row_ans ">
    	<div class="scanner_win new_s">
      	<h4><Span>The following items were verified before Induction of Anesthesia</Span></h4>
    	</div>
      <div class="wrapper_ans_pro">
      	<Div class="wrapped_inner_ans_pro">
        	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
          	
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9">&nbsp;</span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 bold">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 ">Yes</label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">No</label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">N/A</label>
              </span>
          	</div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0 hidden-xs hidden-sm">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9">&nbsp;</span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 bold">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">Yes</label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">No</label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">N/A</label>
              </span>
          	</div>
            
            <div class="clearfix margin_adjustment_only border-dashed"></div>
            <div class="clearfix margin_adjustment_only ">&nbsp;</div>
            
            <div class=" col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
              	Confirmation of: identify, procedure, procedure site and consent(s)
              </span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
               		<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_ipp_yes','chbx_ipp')" value="Yes" name="chbx_ipp" id="chbx_ipp_yes" <?=($confirmIPPSC_signin == 'Yes' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_ipp_no','chbx_ipp')" value="No" name="chbx_ipp" id="chbx_ipp_no"<?=($confirmIPPSC_signin == 'No' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_ipp_na','chbx_ipp')" value="N/A" name="chbx_ipp" id="chbx_ipp_na" <?=($confirmIPPSC_signin == 'N/A' ? 'checked' : '')?> /> 
                </label>
              </span>
          	</div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
              	Site marked by person performing the procedure
              </span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
               		<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_smpp_yes','chbx_smpp')" value="Yes" name="chbx_smpp" id="chbx_smpp_yes" <?=($siteMarked == 'Yes' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_smpp_no','chbx_smpp')" value="No" name="chbx_smpp" id="chbx_smpp_no" <?=($siteMarked == 'No' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_smpp_na','chbx_smpp')" value="N/A" name="chbx_smpp" id="chbx_smpp_na" <?=($siteMarked == 'N/A' ? 'checked' : '')?> /> 
                </label>
              </span>
          	</div>
            
            <div class="clearfix margin_adjustment_only hidden-sm hidden-xs"></div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
              	Patient allergies
              </span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
               		<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_pa_yes','chbx_pa')" value="Yes" name="chbx_pa" id="chbx_pa_yes" <?=($patientAllergies == 'Yes' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_pa_no','chbx_pa')" value="No" name="chbx_pa" id="chbx_pa_no" <?=($patientAllergies == 'No' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_pa_na','chbx_pa')" value="N/A" name="chbx_pa" id="chbx_pa_na" <?=($patientAllergies == 'N/A' ? 'checked' : '')?> /> 
                </label>
              </span>
          	</div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
              	Difficult airway or aspiration risk?
              </span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
               		<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_dar_yes','chbx_dar')" value="Yes" name="chbx_dar" id="chbx_dar_yes" <?=($difficultAirway == 'Yes' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_dar_no','chbx_dar')" value="No" name="chbx_dar" id="chbx_dar_no" <?=($difficultAirway == 'No' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_dar_na','chbx_dar')" value="N/A" name="chbx_dar" id="chbx_dar_na" <?=($difficultAirway == 'N/A' ? 'checked' : '')?> /> 
                </label>
              </span>
          	</div>
            
            <div class="clearfix margin_adjustment_only hidden-sm hidden-xs"></div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 hidden-sm hidden-xs padding_0">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
              	Risk of blood loss (>500 ml)
                <span id="rblno_of_units" style="display:<?php echo $displayStatus=($riskBloodLoss=='Yes')? "inline-block" : "none"; ?>;">
                	# of units available:
                  <input type="text" class="form-control" style="display:inline-block; height:25px;width:70px !important;" name="rbl_no_of_units"  value="<?php echo $bloodLossUnits; ?>" id="rbl_no_of_units">
               	</span> 
              </span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
               		<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_rbl_yes','chbx_rbl');showBox();" value="Yes" name="chbx_rbl" id="chbx_rbl_yes" <?=($riskBloodLoss == 'Yes' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_rbl_no','chbx_rbl');showBox();" value="No" name="chbx_rbl" id="chbx_rbl_no" <?=($riskBloodLoss == 'No' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_rbl_na','chbx_rbl');showBox();" value="N/A" name="chbx_rbl" id="chbx_rbl_na" <?=($riskBloodLoss == 'N/A' ? 'checked' : '')?> /> 
                </label>
              </span>
           	</div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
              	Anesthesia safety check completed
              </span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
               		<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_asc_yes','chbx_asc')" value="Yes" name="chbx_asc" id="chbx_asc_yes" <?=($anesthesiaSafety == 'Yes' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_asc_no','chbx_asc')" value="No" name="chbx_asc" id="chbx_asc_no" <?=($anesthesiaSafety == 'No' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_asc_na','chbx_asc')" value="N/A" name="chbx_asc" id="chbx_asc_na" <?=($anesthesiaSafety == 'N/A' ? 'checked' : '')?> /> 
                </label>
              </span>
          	</div>
            
            <div class="clearfix margin_adjustment_only hidden-sm hidden-xs"></div>
            
            <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0 bold ">Briefing:</div>
            <div class="clearfix margin_adjustment_only border-dashed"></div>
            <div class="clearfix margin_adjustment_only "></div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding_0">
            	<span class="col-lg-9 col-md-9 col-sm-9 col-xs-9 padding_0 bold">
              	All members of the team have discussed care plan and addressed concerns
              </span>
              <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              	<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
               		<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_adcpc_yes','chbx_adcpc')" value="Yes" name="chbx_adcpc" id="chbx_adcpc_yes" <?=($allMembersTeam == 'Yes' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_adcpc_no','chbx_adcpc')" value="No" name="chbx_adcpc" id="chbx_adcpc_no" <?=($allMembersTeam == 'No' ? 'checked' : '')?> /> 
                </label>
                <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                	<input type="checkbox" onclick="javascript:checkSingleChkBox('chbx_adcpc_na','chbx_adcpc')" value="N/A" name="chbx_adcpc" id="chbx_adcpc_na" <?=($allMembersTeam == 'N/A' ? 'checked' : '')?> /> 
                </label>
              </span>
          	</div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 hidden-sm hidden-xs padding_0">&nbsp;</div>
            
          </div> 
      	</Div>
     	</div><!-- Wrapper Ans Pro-->
    </div>
    
    <!-- End P3 Fields --> 
    
     <div class="striped_row_ans ">
        <Div class="scanner_win new_s">
         <h4><Span>Pre-Operative Profile</Span></h4>
        </Div>
        <Div class="wrapper_ans_pro">
            <Div class="wrapped_inner_ans_pro">
                <ul class="list-group  custom_list_ans_pro">
                 <li class="list-group-item">
                <label><input  type="checkbox" <?php if($patientInterviewed=='Yes') echo "CHECKED"; ?> value="Yes" id="patientInterviewedId" name="patientInterviewed"  tabindex="7"><span class="padding_15">Patient Interviewed</span></label>
                </li><li class="list-group-item">
                <label><input <?php if($stableCardiPlumFunction=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="stableCardiPlumFunctionId" name="stableCardiPlumFunction"  tabindex="7"><span class="padding_15">Stable cardiovascular and Pulmonary function</span></label>
                </li><li class="list-group-item">
                <label><input type="checkbox" onclick="javascript:checkSingleChkBox('chartNotesReviewedYes','chartNotesReviewed');" <?php if($chartNotesReviewed=='Yes') echo "CHECKED"; ?> value="Yes" id="chartNotesReviewedYes" name="chartNotesReviewed"  tabindex="7"><span class="padding_15">No change in H&P </span></label>
                </li><li class="list-group-item">
               <label><input <?php if($planAnesthesia=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="planAnesthesiaId" name="planAnesthesia"  tabindex="7"><span class="padding_15">Risk, benefits and alternative discussed</span></label>
                </li><li class="list-group-item">
                <label><input <?php if($procedurePrimaryVerified=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="procedurePrimaryVerifiedId" name="procedurePrimaryVerified"  tabindex="7"><span class="padding_15">Procedure Verified</span></label>
                </li><li class="list-group-item">
                <label><input type="checkbox" onclick="javascript:checkSingleChkBox('chartNotesReviewedChanged','chartNotesReviewed');" <?php if($chartNotesReviewed=='Changed') echo "CHECKED"; ?> value="Changed" id="chartNotesReviewedChanged" name="chartNotesReviewed"  tabindex="7"><span class="padding_15">Changes in H&P documented</span></label>
                </li><li class="list-group-item">
               <label><input <?php if($procedureSecondaryVerified=='Yes') echo "CHECKED"; ?>   type="checkbox" value="Yes" id="procedureSecondaryVerifiedId" name="procedureSecondaryVerified"  tabindex="7"><span class="padding_15">Secondary Verified</span></label>
                </li><li class="list-group-item">
                <label><input <?php if($medsTakeToday=='Yes') echo "CHECKED"; ?>   type="checkbox" value="Yes" id="medsTakeTodayId" name="medsTakeToday"  tabindex="7"><span class="padding_15">Meds. Take Today</span></label>
                </li><li class="list-group-item">
                <label><input <?php if($siteVerified=='Yes') echo "CHECKED"; ?>   type="checkbox" value="Yes" id="siteVerifiedId" name="siteVerified"  tabindex="7"><span class="padding_15">Site Verified</span></label>
                </li><li class="list-group-item">
                <label><input <?php if($ptMedication=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="ptMedicationId" name="ptMedication"  tabindex="7"><span class="padding_15">Pt. Medication</span></label>
                </li><li class="list-group-item">
                <label><input <?php if($fpExamPerformed=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="fpExamPerformedId" name="fpExamPerformed"  tabindex="7"><span class="padding_15">Pt reassessed, stable for anesthesia/surgery</span></label>
                </li><li class="list-group-item">
               <label><input <?php if($copyBaseLineVitalSigns=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="copyBaseLineVitalSigns" name="copyBaseLineVitalSigns"  tabindex="7"><span class="padding_15">Copy Base Line Vital Signs</span></label>
                </li><li class="list-group-item">
               <label><input <?php if($npo=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="npoId" name="npo"  tabindex="7"><span class="padding_15">NPO</span></label>
                </li><li class="list-group-item">
                <label><input <?php if($allQuesAnswered=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="allQuesAnsweredId" name="allQuesAnswered"  tabindex="7"><span class="padding_15">All questions answered</span></label>
                </li>
                </ul>
           </Div>
               
               
            <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6 padding_0">
                <div class="form_reg">
                    <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                            <Div class="caption caption2">	
                                <label class="show-pop-list" data-placement="bottom"  href="javascript:void(0)" >
                                        <A href="javascript:void(0)"> Evaluation	</A> 
                                </label>
                            </Div>
                        </div> 
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                              <textarea name="evaluation2" style="resize:none;" id="local_anes_revaluation2_admin_id" class="form-control"><?php echo $evaluation2; ?></textarea> 
                        </div><!--------------------Col-ends-----------------------------> 	
                   </div> 
                   <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                            <Div class="caption caption2">	
                                <label class="show-pop-list_l" data-placement="bottom"  href="javascript:void(0)" >
                                        <A href="javascript:void(0)"> Dentation	</A> 
                                </label>
                            </Div>
                        </div> 
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                              <textarea name="dentation" style="resize:none;" id="local_anes_dentation_admin_id" class="form-control"><?php echo $dentation; ?></textarea> 
                        </div><!--------------------Col-ends-----------------------------> 	
                   </div> 
                     <div class="clearfix margin_adjustment_only">  </div>
                </div><!-- Form Reg -->
            </Div><!-- col-12 ends -->            
        </Div><!-- Wrapper Ans Pro-->
     </Div>   
      <div class="striped_row_ans ">
        <Div class="scanner_win new_s">
             <h4>
               <span>Holding area through Intra-Op Profile</Span>   
             </h4>
        </Div>
         <Div class="wrapper_ans_pro">
             <!-- Wrapper Ans Pro -->  
            <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                 <Div class="wrapped_inner_ans_pro">
                    <div class="row">
                        <Div class="col-md-3 col-sm-3 col-xs-12 col-lg-3">
                                <label> Honan Balloon	</label>
                        </Div>    
                        <Div class="col-md-5 col-sm-5 col-xs-12 col-lg-5">
                            <div class="row">
                                 <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                 <select class="selectpicker form-control" name="honanballon" id="honanballonId">
                                 <option value=""></option>
                                	<?php
                                	for($i=10;$i<=50;$i+=10) {
                                	?>
                                    <option value="<?php echo $i;?>" <?php if($honanballon==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                	<?php
                                	}
                                	?>
                            		</select>
                                    <small>mm</small>
                                 </div>                                                               
                                 <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                 <select name="honanBallonAnother" class="selectpicker form-control" id="honanBallonAnotherId">
                                    <option value=""></option>
                                    <?php
                                    for($i=1;$i<=10;$i++) {
                                    ?>
                                        <option value="<?php echo $i;?>" <?php if($honanBallonAnother==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                    <small>min</small>	
                                 </div>                                                   
                            </div>	
                        </Div>    
                        <Div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                <label> <input <?php if($NoneHonanBalloon=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" name="chbx_NoneHonanBalloon" id="NoneHonanBalloonId"  ><span class="padding_15">None Honan Balloon</span> </label>
                        </Div>
                        <div class="clearfix margin_adjustment_only"></div>   
                         
                        <Div class="col-lg-3 col-md-3 col-sm-3 hidden-xs"></Div> 
                        <Div class="col-md-5 col-sm-5 col-xs-12 col-lg-5">
                            <div class="row">
                                 <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                    <input id="bp_temp" onKeyUp="displayText1=this.value" onClick="getShowAdmin(40,100,'flag1');"  maxlength="5"  value="<?php echo $o2lpm_1; ?>" type="text" class="form-control width_adjust_input" name="o2lpm_1" style="border-color:<?php echo $border_blue_local_anes; ?> ;" />
                                    <small class="">02 l/m</small>                                                            
                                </div>                                                               
                                 <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                    <select name="o2lpm_count" id="o2lpm_count" class="selectpicker form-control">
                                    <option value=""></option>
                                    <?php 
                                        for($i=1;$i<=10;$i++){
                                    ?>										
                                    <option value="<?php echo $i;?>" <?php if($i==$o2lpm_count){ echo "selected";}?>><?php echo $i;?></option>
                                	<?php	}?>	
                                    </select>
                                     <small class="">Count</small>

                                 </div>                                                   
                            </div>	
                        </Div>    
                        <div class="col-lg-2 visible-lg"></div>
                        <div class="col-md-2 visible-md"></div>
                        <Div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                <label> <input type="checkbox" <?php if($digital=='Yes') echo "CHECKED"; ?> value="Yes" id="digitalId" name="digital"><span class="padding_15">Digital</span></label>
                        </Div>
                    </div>    
                 </Div>
            </Div>
            <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                 <!--<Div class="wrapped_inner_ans_pro">
                     <label> <input <?php //if($routineMonitorApplied=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="routineMonitorAppliedId" name="routineMonitorApplied"><span class="padding_15">Routine Monitor</span></label>
                 </Div>-->
                 <div class="wrap_text-boxes">
                     <div class="row">	
                         <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12 heigh-text-adjust">
                         	<label> <input <?php if($routineMonitorApplied=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="routineMonitorAppliedId" name="routineMonitorApplied"><span class="padding_15">Routine Monitor</span></label>
                         </Div>
                         <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12 heigh-text-adjust">
                         	<label> <input <?php if($hide_anesthesia_grid=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="hide_anesthesia_grid_id" name="hide_anesthesia_grid"><span class="padding_15">Hide Anesthesia Grid</span></label>
                         </Div>
                         <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12 heigh-text-adjust">
                          <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 ">
                                    <Div class="caption caption2">	
                                        <label  class="show-pop-trigger2" data-placement="top" href="" >
                                            <A href="javascript:void(0)"> EKG </A>
                                        </label>
                                        
                                    </Div> 
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                             <textarea class="form-control" name="ekgBigRowValue" id="ekgBigRowAdminId" style="resize:none;border-color:<?php echo $border_blue_local_anes; ?>" /><?php echo $ekgBigRowValue; ?></textarea>
                            </div>
                          </div>
                       </Div>
                       <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12  heigh-text-adjust ">
                         <div class="row">
                             <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                 <label for="comment">
                                    Comment
                                </label>
                             </div>
                             <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                             <textarea style="resize:none;" class="form-control" id="ansComment" name="ansComment"><?php echo $ansComment; ?></textarea>
                             
                             </div> 	
                         </div> 
                       </Div>
                     </div><!--- -- ---------- Row ends----------  -->  
                 </div>
            </Div>
           
           
               
               
           <div class="clearfix margin_adjustment_only" ></div>
           <!--- ---------------    Blocks Css----------------------- -->
             <div class="clearfix margin_adjustment_only"></div>
             <div class="clearfix margin_adjustment_only border-dashed" ></div>
             <div class="clearfix margin_adjustment_only"></div>
             <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                      <Div class="wrapped_inner_ans_pro">
                            <div class="head_block">
                                <h4 class="rob">
                                    Topical
                                </h4>	  
                            </div>
                            <div class="wrap_inner_block">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" style=" height:22px;background-color:<?php echo $bglight_blue_local_anes; ?>;">
                                           <label><input <?php if($Topicaltopical4PercentLidocaine=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_Topicaltopical4PercentLidocaine" name="chbx_Topicaltopical4PercentLidocaine" ><span class="padding_15">4% lidocaine</span></label>    
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input <?php if($TopicalIntracameral1percentLidocaine=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="TopicalIntracameral1percentLidocaine_id" name="chbx_TopicalIntracameral1percentLidocaine"><span class="padding_15">1% lidocaine MPF</span></label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small"> Intracameral</small>
                                                   <select class="selectpicker form-control" name="TopicalIntracameral" id="TopicalIntracameral">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=0.5;$i<=10;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($TopicalIntracameral==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                    <small>ml</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($TopicalPeribulbar2percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_TopicalPeribulbar2percentLidocaine_id" name="chbx_TopicalPeribulbar2percentLidocaine" ><span class="padding_15">2% lidocaine</span></label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small">Peribulbar</small>
                                                   <select class="selectpicker form-control"  name="TopicalPeribulbar" id="TopicalPeribulbar">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($TopicalPeribulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                    <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label> <input type="checkbox" value="Yes" <?php if($TopicalRetrobulbar4percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_TopicalRetrobulbar4percentLidocaine_id" name="chbx_TopicalRetrobulbar4percentLidocaine" ><span class="padding_15">3% lidocaine</span></label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small">Retrobulbar</small>
                                                   <select class="selectpicker form-control" name="TopicalRetrobulbar" id="TopicalRetrobulbar">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($TopicalRetrobulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                    <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                           <label><input type="checkbox" value="Yes" <?php if($TopicalHyalauronidase4percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_TopicalHyalauronidase4percentLidocaine_id" name="chbx_TopicalHyalauronidase4percentLidocaine" ><span class="padding_15">4% lidocaine</span></label>    
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label> <input type="checkbox" value="Yes" <?php if($TopicalVanLindrHalfPercentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_TopicalVanLindrHalfPercentLidocaine_id" name="chbx_TopicalVanLindrHalfPercentLidocaine" ><span class="padding_15">0.5% Bupivacaine</span> </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small">Van Lindit</small>
                                                   <select class="selectpicker form-control"  name="TopicalVanLindr" id="TopicalVanLindr">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($TopicalVanLindr==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>
                                                    <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    
																		<!-- Start 0.75% Bupivacaine -->
																		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                    	<label><input type="checkbox" value="Yes" <?php if($topical_bupivacaine75=='Yes') echo 'CHECKED'; ?> id="chbx_topical_bupivacaine75_id" name="topical_bupivacaine75" ><span class="padding_15">0.75% Bupivacaine</span></label>   
                                  	</div>
                                    <!-- End 0.75% Bupivacaine -->
                                                                        
                                    
                                    <!-- Start  0.75% Marcaine -->
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                    	<label><input type="checkbox" value="Yes" <?php if($topical_marcaine75 =='Yes') echo 'CHECKED'; ?> id="chbx_topical_marcaine75_id" name="topical_marcaine75" ><span class="padding_15">0.75% Marcaine</span></label>   
                                  	</div>
                                    <!-- End  0.75% Marcaine -->
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label> <input type="checkbox" value="Yes" <?php if($TopicallidEpi5ug=='Yes') echo 'CHECKED'; ?> id="chbx_TopicallidEpi5ug_id" name="chbx_TopicallidEpi5ug" ><span class="padding_15">Epi 5 ug/m</span>   </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                <Div class="row">
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-4">
                                                     <input type="text" name="TopicallidTxt" id="Topicaltxt_field01" class="form-control" value="<?php echo $TopicallidTxt;?>"/>
                                                  </div>
                                                  <div class="col-lg-1 visible-lg" style="min-height:25px">lid</div>
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                     <select class="selectpicker form-control" name="Topicallid" id="Topicallid">
                                                        <option value=""></option>
                                                        <?php
                                                        for($i=1;$i<=20;$i+=0.5) {
                                                        ?>
                                                            <option value="<?php echo $i;?>" <?php if($Topicallid==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>	
                                                    <small>mls</small>
                                                 </div>
                                                </Div> 
                                            </div>	
                                       </div>		
                                    </div> <!-- ------------	Col Ends		 ---------->
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label> <input type="checkbox" value="Yes" <?php if($TopicalotherRegionalAnesthesiaWydase15u=='Yes') echo 'CHECKED'; ?> id="chbx_TopicalotherRegionalAnesthesiaWydase15u_id" name="chbx_TopicalotherRegionalAnesthesiaWydase15u"><span class="padding_15">Wydase 15 u/ml</span>	    </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                <Div class="row">
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-4">
                                                    <small class=""> Other </small>
                                                    <input type="text" name="TopicalotherRegionalAnesthesiaTxt1" value="<?php echo stripslashes($TopicalotherRegionalAnesthesiaTxt1);?>" id="txt_Topicalfield01" class="form-control width_Adjust_ii"/>
                                                  </div>	
                                                   <div class="col-lg-1 visible-lg"></div>
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                    <select class="selectpicker form-control" name="TopicalotherRegionalAnesthesiaDrop" id="TopicalotherRegionalAnesthesiaDrop">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($TopicalotherRegionalAnesthesiaDrop==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                    <small>mls</small>
                                                 </div>
                                                </Div> 
                                            </div>	
                                       </div>		
                                    </div>
                                      <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                        <div class="row">
                                        
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                                    <label> Other </label>
                                                  </div>	
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                    <input class="form-control" type="text" name="TopicalotherRegionalAnesthesiaTxt2" id="TopicalotherRegionalAnesthesiaTxt2" value="<?php echo stripslashes($TopicalotherRegionalAnesthesiaTxt2);?>"  />
                                                 </div>
                                        </div>
                                     </div>   
                                 </div>	
                            </div>	
                      </Div>
             </Div>
             <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                      <Div class="wrapped_inner_ans_pro">
                            <div class="head_block">
                                <h4 class="rob">
                                      Block 1
                                </h4>	  
                            </div>
                            <div class="wrap_inner_block">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                           <label><input <?php if($Block1topical4PercentLidocaine=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_Block1topical4PercentLidocaine" name="chbx_Block1topical4PercentLidocaine"><span class="padding_15">4% lidocaine</span>  	 </label>    
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($Block1Intracameral1percentLidocaine=='Yes') echo 'CHECKED'; ?> id="Block1Intracameral1percentLidocaine_id" name="chbx_Block1Intracameral1percentLidocaine" ><span class="padding_15">1% lidocaine MPF</span> 		 </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small"> Intracameral</small>
                                                   <select class="selectpicker form-control" name="Block1Intracameral" id="Block1Intracameral">
                                                        <option value=""></option>
                                                        <?php
                                                        for($i=0.5;$i<=10;$i+=0.5) {
                                                        ?>
                                                            <option value="<?php echo $i;?>" <?php if($Block1Intracameral==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                        
                                                    </select>	
                                                    <small>ml</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label> <input type="checkbox" value="Yes" <?php if($Block1Peribulbar2percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Block1Peribulbar2percentLidocaine_id" name="chbx_Block1Peribulbar2percentLidocaine"><span class="padding_15">2% lidocaine</span>  		 </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small">Peribulbar</small>
                                                   <select class="selectpicker form-control" name="Block1Peribulbar" id="Block1Peribulbar">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block1Peribulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>
                                                    <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label> <input type="checkbox" value="Yes" <?php if($Block1Retrobulbar4percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Block1Retrobulbar4percentLidocaine_id" name="chbx_Block1Retrobulbar4percentLidocaine" ><span class="padding_15">3% lidocaine</span> 	 </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small">Retrobulbar</small>
                                                   <select class="selectpicker form-control" name="Block1Retrobulbar" id="Block1Retrobulbar">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block1Retrobulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                    <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                           <label> <input type="checkbox" value="Yes" <?php if($Block1Hyalauronidase4percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Block1Hyalauronidase4percentLidocaine_id" name="chbx_Block1Hyalauronidase4percentLidocaine" ><span class="padding_15">4% lidocaine</span> </label>    
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($Block1VanLindrHalfPercentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Block1VanLindrHalfPercentLidocaine_id" name="chbx_Block1VanLindrHalfPercentLidocaine" ><span class="padding_15">0.5% Bupivacaine</span> </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                               <small class="width_small">Van Lindit</small>
                                               <select class="selectpicker form-control" name="Block1VanLindr" id="Block1VanLindr">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block1VanLindr==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    
                                    <!-- Start 0.75% Block1 Bupivacaine -->
																		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                    	<label><input type="checkbox" value="Yes" <?php if($block1_bupivacaine75=='Yes') echo 'CHECKED'; ?> id="chbx_block1_bupivacaine75_id" name="block1_bupivacaine75" ><span class="padding_15">0.75% Bupivacaine</span></label>   
                                  	</div>
                                    <!-- End 0.75% Block1 Bupivacaine -->
                                                                        
                                    
                                    <!-- Start  0.75% Block1 Marcaine -->
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                    	<label><input type="checkbox" value="Yes" <?php if($block1_marcaine75 =='Yes') echo 'CHECKED'; ?> id="chbx_block1_marcaine75_id" name="block1_marcaine75" ><span class="padding_15">0.75% Marcaine</span></label>   
                                  	</div>
                                    <!-- End  0.75% Block1 Marcaine -->
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($Block1lidEpi5ug=='Yes') echo 'CHECKED'; ?> id="chbx_Block1lidEpi5ug_id" name="chbx_Block1lidEpi5ug"><span class="padding_15">Epi 5 ug/m</span>      </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                <Div class="row">
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-4">
                                                    <input type="text" name="Block1lidTxt" id="Block1lidTxt" class="form-control" value="<?php echo $Block1lidTxt;?>"/>
                                                  </div>
                                                  <div class="col-lg-1 visible-lg" style="min-height:25px">lid</div>
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                     <select class="selectpicker form-control" name="Block1lid" id="Block1lid">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block1lid==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                    <small>mls</small>
                                                 </div>
                                                </Div> 
                                            </div>	
                                       </div>		
                                    </div> <!-- ------------	Col Ends		 ---------->
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($Block1otherRegionalAnesthesiaWydase15u=='Yes') echo 'CHECKED'; ?> id="chbx_Block1otherRegionalAnesthesiaWydase15u_id" name="chbx_Block1otherRegionalAnesthesiaWydase15u"><span class="padding_15">Wydase 15 u/ml</span>    	    </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                <Div class="row">
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-4">
                                                    <small class=""> Other </small>
                                                    <input type="text" name="Block1otherRegionalAnesthesiaTxt1" value="<?php echo stripslashes($Block1otherRegionalAnesthesiaTxt1);?>" id="Block1otherRegionalAnesthesiaTxt1" class="form-control width_Adjust_ii"/>
                                                  </div>	
                                                   <div class="col-lg-1 visible-lg"></div>
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                    <select class="selectpicker form-control" name="Block1otherRegionalAnesthesiaDrop" id="Block1otherRegionalAnesthesiaDrop">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block1otherRegionalAnesthesiaDrop==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                    <small>mls</small>
                                                 </div>
                                                </Div> 
                                            </div>	
                                       </div>		
                                    </div>
                                      <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                        <div class="row">
                                        
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                                    <label> Other </label>
                                                  </div>	
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <input type="text" name="Block1otherRegionalAnesthesiaTxt2" id="Block1otherRegionalAnesthesiaTxt2" value="<?php echo stripslashes($Block1otherRegionalAnesthesiaTxt2);?>"  class="form-control" />
                                                 </div>
                                        </div>
                                     </div>
                                     <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                        <div class="row">
                                             <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                <label><input type="checkbox" value="Yes" <?php if($Block1Aspiration=='Yes') echo 'CHECKED'; ?> id="Block1Aspiration" name="Block1Aspiration"><span class="padding_15">Aspiration</span>    	    </label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                <label> <input type="checkbox" value="Yes" <?php if($Block1Full=='Yes') echo 'CHECKED'; ?> id="Block1Full" name="Block1Full" ><span class="padding_15">Full EOM</span></label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                <label><input type="checkbox" value="Yes" <?php if($Block1BeforeInjection=='Yes') echo 'CHECKED'; ?> id="Block1BeforeInjection" name="Block1BeforeInjection"><span class="padding_15">Before Injection</span></label>
                                            </div>	
                                                
                                       </div>		
                                    </div>
                                     
                                        
                                 </div>	
                            </div>	
                      </Div>
             </Div>
            <!---     col-12 ends        --->
            <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                      <Div class="wrapped_inner_ans_pro">
                            <div class="head_block">
                                <h4 class="rob">
                                    Block 2
                                </h4>	  
                            </div>
                            <div class="wrap_inner_block">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                           <label><input <?php if($Block2topical4PercentLidocaine=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_Block2topical4PercentLidocaine" name="chbx_Block2topical4PercentLidocaine"><span class="padding_15">4% lidocaine</span>  	 </label>    
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($Block2Intracameral1percentLidocaine=='Yes') echo 'CHECKED'; ?> id="Block2Intracameral1percentLidocaine_id" name="chbx_Block2Intracameral1percentLidocaine" ><span class="padding_15">1% lidocaine MPF</span> 		 </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small"> Intracameral</small>
                                                   <select class="selectpicker form-control" name="Block2Intracameral" id="Block2Intracameral">
                                                        <option value=""></option>
                                                        <?php
                                                        for($i=0.5;$i<=10;$i+=0.5) {
                                                        ?>
                                                            <option value="<?php echo $i;?>" <?php if($Block2Intracameral==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                        
                                                    </select>	
                                                    <small>ml</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label> <input type="checkbox" value="Yes" <?php if($Block2Peribulbar2percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Block2Peribulbar2percentLidocaine_id" name="chbx_Block2Peribulbar2percentLidocaine"><span class="padding_15">2% lidocaine</span>  		 </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small">Peribulbar</small>
                                                   <select class="selectpicker form-control" name="Block2Peribulbar" id="Block2Peribulbar">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block2Peribulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>
                                                    <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label> <input type="checkbox" value="Yes" <?php if($Block2Retrobulbar4percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Block2Retrobulbar4percentLidocaine_id" name="chbx_Block2Retrobulbar4percentLidocaine" ><span class="padding_15">3% lidocaine</span> 	 </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <small class="width_small">Retrobulbar</small>
                                                   <select class="selectpicker form-control" name="Block2Retrobulbar" id="Block2Retrobulbar">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block2Retrobulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                    <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                           <label> <input type="checkbox" value="Yes" <?php if($Block2Hyalauronidase4percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Block2Hyalauronidase4percentLidocaine_id" name="chbx_Block2Hyalauronidase4percentLidocaine" ><span class="padding_15">4% lidocaine</span> </label>    
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($Block2VanLindrHalfPercentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Block2VanLindrHalfPercentLidocaine_id" name="chbx_Block2VanLindrHalfPercentLidocaine" ><span class="padding_15">0.5% Bupivacaine</span> </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                               <small class="width_small">Van Lindit</small>
                                               <select class="selectpicker form-control" name="Block2VanLindr" id="Block2VanLindr">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block2VanLindr==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                <small>mls</small>
                                            </div>	
                                       </div>		
                                    </div>
                                    
                                    <!-- Start 0.75% Block2 Bupivacaine -->
																		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                    	<label><input type="checkbox" value="Yes" <?php if($block2_bupivacaine75=='Yes') echo 'CHECKED'; ?> id="chbx_block2_bupivacaine75_id" name="block2_bupivacaine75" ><span class="padding_15">0.75% Bupivacaine</span></label>   
                                  	</div>
                                    <!-- End 0.75% Block2 Bupivacaine -->
                                                                        
                                    
                                    <!-- Start  0.75% Block2 Marcaine -->
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                    	<label><input type="checkbox" value="Yes" <?php if($block2_marcaine75 =='Yes') echo 'CHECKED'; ?> id="chbx_block2_marcaine75_id" name="block2_marcaine75" ><span class="padding_15">0.75% Marcaine</span></label>   
                                  	</div>
                                    <!-- End  0.75% Block2 Marcaine -->
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($Block2lidEpi5ug=='Yes') echo 'CHECKED'; ?> id="chbx_Block2lidEpi5ug_id" name="chbx_Block2lidEpi5ug"><span class="padding_15">Epi 5 ug/m</span>      </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                <Div class="row">
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-4">
                                                    <input type="text" name="Block2lidTxt" id="Block2lidTxt" class="form-control" value="<?php echo $Block2lidTxt;?>"/>
                                                  </div>
                                                  <div class="col-lg-1 visible-lg" style="min-height:25px">lid</div>
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                     <select class="selectpicker form-control" name="Block2lid" id="Block2lid">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block2lid==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                    <small>mls</small>
                                                 </div>
                                                </Div> 
                                            </div>	
                                       </div>		
                                    </div> <!-- ------------	Col Ends		 ---------->
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                        <div class="row">
                                             <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                            <label><input type="checkbox" value="Yes" <?php if($Block2otherRegionalAnesthesiaWydase15u=='Yes') echo 'CHECKED'; ?> id="chbx_Block2otherRegionalAnesthesiaWydase15u_id" name="chbx_Block2otherRegionalAnesthesiaWydase15u"><span class="padding_15">Wydase 15 u/ml</span>    	    </label>
                                            </div>	
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                <Div class="row">
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-4">
                                                    <small class=""> Other </small>
                                                    <input type="text" name="Block2otherRegionalAnesthesiaTxt1" value="<?php echo stripslashes($Block2otherRegionalAnesthesiaTxt1);?>" id="Block2otherRegionalAnesthesiaTxt1" class="form-control width_Adjust_ii"/>
                                                  </div>	
                                                   <div class="col-lg-1 visible-lg"></div>
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                    <select class="selectpicker form-control" name="Block2otherRegionalAnesthesiaDrop" id="Block2otherRegionalAnesthesiaDrop">
                                                    <option value=""></option>
                                                    <?php
                                                    for($i=1;$i<=20;$i+=0.5) {
                                                    ?>
                                                        <option value="<?php echo $i;?>" <?php if($Block2otherRegionalAnesthesiaDrop==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    
                                                </select>	
                                                    <small>mls</small>
                                                 </div>
                                                </Div> 
                                            </div>	
                                       </div>		
                                    </div>
                                      <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                        <div class="row">
                                        
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-5">
                                                    <label> Other </label>
                                                  </div>	
                                                  <div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                   <input type="text" name="Block2otherRegionalAnesthesiaTxt2" id="Block2otherRegionalAnesthesiaTxt2" value="<?php echo stripslashes($Block2otherRegionalAnesthesiaTxt2);?>"  class="form-control" />
                                                 </div>
                                        </div>
                                     </div>
                                     <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                        <div class="row">
                                             <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                <label><input type="checkbox" value="Yes" <?php if($Block2Aspiration=='Yes') echo 'CHECKED'; ?> id="Block2Aspiration" name="Block2Aspiration"><span class="padding_15">Aspiration</span>    	    </label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                <label> <input type="checkbox" value="Yes" <?php if($Block2Full=='Yes') echo 'CHECKED'; ?> id="Block2Full" name="Block2Full" ><span class="padding_15">Full EOM</span></label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                <label><input type="checkbox" value="Yes" <?php if($Block2BeforeInjection=='Yes') echo 'CHECKED'; ?> id="Block2BeforeInjection" name="Block2BeforeInjection"><span class="padding_15">Before Injection</span></label>
                                            </div>	
                                                
                                       </div>		
                                    </div>
                                     
                                        
                                 </div>	
                            </div>	
                      </Div>
             </Div>
               <!---     col-12 ends        --->
             
              <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                  <Div class="wrapped_inner_ans_pro">
                        <div class="head_block">
                            <h4 class="rob">
                                Intra-Op Medication
                            </h4>	  
                        </div>
                        <div class="wrap_inner_block">
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 col-lg-6 col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                            <label for="Med1">Med1</label>
                                        </div>	
                                        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                             <input type="text" name="txtInterOpDrugs1" id="txtInterOpDrugs1" value="<?php echo stripslashes($txtInterOpDrugs1);?>" class="form-control" />
                                        </div>	 
                                    </div>
                                </div>
                              <div class="col-sm-6 col-xs-6 col-lg-6 col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                            <label for="Med2">Med2</label>
                                        </div>	
                                        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                            <input type="text" name="txtInterOpDrugs2" id="txtInterOpDrugs2" value="<?php echo stripslashes($txtInterOpDrugs2);?>"  class="form-control"/>
                                        </div>	 
                                    </div>
                                </div>
                            </div>
                        </div>
                 </Div>
              </Div> 
            <!---     col-12 ends        ---> 
        </Div>
      </div>   
        <div class="clearfix margin_adjustment_only"></div>
       <div class="striped_row_ans">  
        <div class="scanner_win new_s">
                 <h4><span>Post-Operative Profile</span></h4>
        </div>  
        <Div class="wrapper_ans_pro">
            <div class="wrapped_inner_ans_pro">
                <ul class="list-group custom_list_ans_pro custom_width_3_list_ans">
                  <li class="list-group-item"> <label><input <?php if($anyKnowAnestheticComplication=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="anyKnowAnestheticComplicationId" name="anyKnowAnestheticComplication"><span class="padding_15">No known anesthetic complication</span>   </label> 
                  </li>
                  <li class="list-group-item"> <label><input <?php if($stableCardiPlumFunction2=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="stableCardiPlumFunction2Id" name="stableCardiPlumFunction2"><span class="padding_15">Stable cardiovascular and pulmonary function</span>    </label>
                  </li>
                  <li class="list-group-item">  <label><input <?php if($satisfactoryCondition4Discharge=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="satisfactoryCondition4DischargeId" name="satisfactoryCondition4Discharge"><span class="padding_15">Satisfactory condition for discharge</span>    </label>
                  </li>
                </ul>
            </div>    
                <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 padding_0">
                <div class="form_reg">
                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                <Div class="caption caption2">	
                                    <label  class="show-pop-large" data-placement="top"  href="" >
                                          <A href=-"javascript:void(0)">  Evaluation		</A>
                                    </label>
                                    
                                   
                                </Div>
                            </div> 
                            <div class="clearfix"></div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <textarea name="evaluation" id="local_anes_revaluation1_admin_id" class="form-control" style="resize:none;"><?php echo $evaluation; ?></textarea>  
                             </div> <!----------------------- Full Inout col-12    ------------------------------>
                             <div class="clearfix margin_adjustment_only">  </div>
                 </div><!-- Form Reg -->
                 </div>
                 <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 padding_0">
                 <div class="form_reg">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="remarks">
                                         Remarks		
                                    </label>
                            </div> 
                            <div class="clearfix"></div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <textarea name="remarks" id="remarks" class="form-control" style="resize:none;"><?php echo $remarks; ?></textarea>
                                    <!--------------------Col-ends-----------------------------> 	
                             </div> <!----------------------- Full Inout col-12    ------------------------------>
                 </div>               
                </div>
                <div class="clearfix margin_adjustment_only"></div> 
                <div class="clearfix margin_adjustment_only border-dashed"></div>  
                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-center">
                        <label for="sign" class=""> 
                             Signature of <?php echo $anesthesiologistName;?> 
                        </label>
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
								$showAnesImgPath='';
								$anesthesia_profile_sign_path_encode='';
								if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
								if($anesthesia_profile_sign_path) {
									$showAnesImgPath = "/".$surgeryCenterWebrootDirectoryName."/admin/".$anesthesia_profile_sign_path;
									$anesthesia_profile_sign_path_encode = base64_encode($showAnesImgPath);	
								}
							?>
								<canvas id="sign" style="border:dashed 1px #333; " height="100" ></canvas>
								<input type="hidden" name="sig_datasign"  id="sig_datasign" value="<?php echo $anesthesia_profile_sign_path_encode;?>"/>
								<input type="hidden" name="sig_imgsign"  id="sig_imgsign" value="<?php echo $showAnesImgPath;?>" />
							
							
						<?php 
						}
						else
						{
						?>
								<span class="text_12 col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_0" style="background-color:#F1F4F0; ">
                                    <input type="hidden" name="elem_signature" value="<?php echo $elem_signature; ?>">
                                     <input type="hidden" name="anesthesia_profile_sign" id="anesthesia_profile_sign" value="<?php echo $anesthesia_profile_sign; ?>">
                                    <object type="application/x-java-applet" name="appAnes_signature" id="appAnes_signature" width="100%" height="100">
                                        <param name="code" value="MyCanvasColored.class" />
                                        <param name="codebase" value="../common/applet/" />
                                        <param name="archive" value="DrawApplet.jar" />
                                        <param name="bgImage" value="../images/white.jpg">
                                        <param name="strpixls" value="<?php echo $anesthesia_profile_sign;?>">
                                        <param name="mode" value="edit">
                                    </object>
                                </span>
						<?php
						}?> 
                    </div> <!----------------------- Full Inout col-12    ------------------------------>
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
                 <!-- Form REg ends -->
        </Div>
       </div>  
        
     </Div>  <!-- SCrollable Yes -->      
 </div>		<!-- All Admin Content Agree -->
                    
	<!--
    
<table class="table_collapse" style="border:none; padding:0px;" onClick="preCloseFun('evaluationLocalAnesEvaluationAdminDiv');preCloseFun('cal_pop_admin');preCloseFun('ekgLocalAnesAdminDiv');preCloseFun('postop_evaluationEvaluationAdminDiv');">
-->
    	
</form>	
<?php
}
include_once("../common/evaluationLocalAnesAdmin_pop.php");
include_once("../common/ekgLocalAnesAdmin_pop.php");
include_once("../common/post_op_evaluation_admin_pop.php");
include_once("../common/dentationLocalAnesAdmin_pop.php");

?>

</body>
</html>
