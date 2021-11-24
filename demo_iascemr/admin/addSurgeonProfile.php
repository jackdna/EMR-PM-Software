<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData 		= new manageData;
$andProfileDelCond = "  AND del_status ='' ";

//START GETTING DX CODE TYPE
$sqlStr = "SELECT * FROM surgerycenter WHERE surgeryCenterId = '1'";
$sqlQry = imw_query($sqlStr);
$rowsCount = imw_num_rows($sqlQry);
$diagnosis_code_type = 'icd9';
if($rowsCount>0){
	$sqlRows = imw_fetch_array($sqlQry);
	$diagnosis_code_type= $sqlRows['diagnosis_code_type'];
}
//END GETTING DX CODE TYPE

//get data for prefrence card if any
if($_POST['pref_card'])
{
	$query=imw_query("select * from procedureprofile where procedureId='$_POST[pref_card]'")or die(imw_error());
	if(imw_num_rows($query)>=1)
	{
		$pref_card_data=imw_fetch_object($query);
		
	}
}
$profileId 			= $_REQUEST['profile'];
$seqNmbr 			= $_REQUEST["seqNmbr"];
$intraOpPostOpOrder	= addslashes($_REQUEST['intraOpPostOpOrder']);	
$postOpDrop 		= addslashes($_REQUEST['postOpDrop']);
$otherPreOpOrders 	= addslashes($_REQUEST['otherPreOpOrders']);
//$medicalEvaluation 	= addslashes($_REQUEST['medicalEvaluation']);

$surgeonsListId 	= $_REQUEST['surgeonsList'];

$surgeonId 			= $_REQUEST['surgeonId'];
if($surgeonId=='') {
	$surgeonId 		= $_REQUEST['surgeonsList'];
}
$profileName 		= $_REQUEST['elem_profileName'];

if($profileName=='') {
	if($_REQUEST['profileId']<>'') {
		$profilenameQry 	= imw_query("select * from surgeonprofile where surgeonProfileId = '".$_REQUEST['profileId']."'".$andProfileDelCond) or die(imw_error());
		if(imw_num_rows($profilenameQry)>0) {
			$profilenameRow = imw_fetch_array($profilenameQry);
			$profileName 	= $profilenameRow['profileName'];
			//$postOpDrop 	= $profilenameRow['postOpDrop'];
		}
	}
}	
$profileId = $_REQUEST['profile_insId'];
if($profileId=='') {
	$profileId = $_REQUEST['profileId'];
}

$defaultProfile = $_REQUEST['defaultProfile']; //DEFAULT PROFILE CHECKBOX
//$postOpDrop = $_REQUEST['postOpDrop']; 
$hidd_defaultProfileStatus = $_REQUEST['hidd_defaultProfileStatus']; //DEFAULT PROFILE STATUS

//IF DEFAULT PROFILE IS EMPTY THEN FETCH IT FROM TABLE
	
	if($hidd_defaultProfileStatus<>'true') {
		$defaultProfileQry = imw_query("select * from surgeonprofile where surgeonProfileId = '".$profileId."'".$andProfileDelCond) or die(imw_error());
		if(imw_num_rows($defaultProfileQry)>0) {
			$defaultProfileRow = imw_fetch_array($defaultProfileQry);
			$defaultProfile = $defaultProfileRow['defaultProfile'];
			//$postOpDrop  = $defaultProfileRow['postOpDrop'];
		}
	}
	//IF defaultProfile VARIABLE IS POSTED AND IS EMPTY THEN SET IT TO ZERO
		if($hidd_defaultProfileStatus=='true' && !$defaultProfile) {
			$defaultProfile=0;
		}
	//IF defaultProfile VARIABLE IS POSTED AND IS EMPTY THEN SET IT TO ZERO
		
//IF DEFAULT PROFILE IS EMPTY THEN FETCH IT FROM TABLE

//DELETE RECORD
	$sbtDelProfileId = $_POST['sbtDelProfileId'];
	if($sbtDelProfileId<>'') {
		/*
		$deleteSurgeonProfileProcedureQry = "delete from surgeonprofileprocedure where profileId = '$sbtDelProfileId'";
		$deleteSurgeonProfileProcedureRes = imw_query($deleteSurgeonProfileProcedureQry) or die('error1');
	
		$deleteSurgeonProfileQry = "delete from surgeonprofile where surgeonProfileId = '".$sbtDelProfileId."'.$andProfileDelCond";
		$deleteSurgeonProfileRes = imw_query($deleteSurgeonProfileQry) or die(imw_error('error2'));
		*/
		$deleteSurgeonProfileQry = "UPDATE surgeonprofile SET del_status = 'yes' where surgeonProfileId = '".$sbtDelProfileId."'";
		$deleteSurgeonProfileRes = imw_query($deleteSurgeonProfileQry) or die(imw_error('error2'));

		$counter=1;
		if($deleteSurgeonProfileRes)
		{
			echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
		}
		
		
	}
//DELETE RECORD
//SAVE RECORD
if($_REQUEST["sbtSaveSurgeonProfile"]=="true") {
	$counterId = $_REQUEST['counterId'];
	
	//START SET ATLEAST ONE PROFILE AS DEFAULT PROFILE
	$chkDefaultProfileQry = "SELECT * FROM surgeonprofile WHERE surgeonId = '".$surgeonId."' AND defaultProfile='1'".$andProfileDelCond;
	$chkDefaultProfileRes = imw_query($chkDefaultProfileQry) or die(imw_error());
	$chkDefaultProfileNumRow = imw_num_rows($chkDefaultProfileRes);
	if($chkDefaultProfileNumRow<=0 && $defaultProfile==0) {
		//$defaultProfile=1;
	}
	//START SET ATLEAST ONE PROFILE AS DEFAULT PROFILE
	
	$insSurgeonProfileQry = "update surgeonprofile set 
								profileName 			= '".$profileName."',
								defaultProfile 			= '".$defaultProfile."',
								surgeonId 				= '".$surgeonId."',
								intraOpPostOpOrder		= '".$intraOpPostOpOrder."',
								postOpDrop 				= '".$postOpDrop."',
								otherPreOpOrders 		= '".$otherPreOpOrders."'
								where surgeonProfileId 	= '".$profileId."'
							";
	
	$insSurgeonProfileRes = imw_query($insSurgeonProfileQry) or die(imw_error());		

	//SET DEFAULT PROFILE OF OTHER FIELD UNCHECKED
		if($defaultProfile<>'0') {
			$setdefaultProfileZeroQry = "update surgeonprofile set 
										defaultProfile = '0'
										WHERE surgeonId = '$surgeonId'
										AND surgeonProfileId != '".$profileId."'
										";
			$setdefaultProfileZeroRes = imw_query($setdefaultProfileZeroQry) or die(imw_error());									
		}
	//SET DEFAULT PROFILE OF OTHER FIELD UNCHECKED
	
	//SAVE PROCEDUREID TEMPLATEID INSTRUCTIONSHEETID 
	$chkSurgeonProfileProcedureQry = "select * from surgeonprofileprocedure where profileId = '".$profileId."' order by id";
	$chkSurgeonProfileProcedureRes = imw_query($chkSurgeonProfileProcedureQry) or die(imw_error());
	$chkSurgeonProfileProcedureNumRow = imw_num_rows($chkSurgeonProfileProcedureRes);
	$profProcIdArr = array();
	if($chkSurgeonProfileProcedureNumRow > 0) {
		while($chkSurgeonProfileProcedureRow=imw_fetch_array($chkSurgeonProfileProcedureRes)) {
			//$idAutoIncr[] 		= $chkSurgeonProfileProcedureRow['id'];
			$profileIdTemp 		= $chkSurgeonProfileProcedureRow['profileId'];
			$procedureIdTemp	= $chkSurgeonProfileProcedureRow['procedureId'];
			$profProcIdArr[] 	= $profileIdTemp.'@@'.$procedureIdTemp;
		}
		
		//$deleteSurgeonProfileProcedureQry = "delete from surgeonprofileprocedure where profileId = '".$profileId."'";
		//$deleteSurgeonProfileProcedureRes = imw_query($deleteSurgeonProfileProcedureQry) or die(imw_error());
		
		
	}
	
	$procedureIdActive = 0;
	for($k=1;$k<=$counterId;$k++) {
		$procedureId 		= $_REQUEST['procedureId'.$k];
		$procedureIdActive	.= ', '.$procedureId;
		$procedureName 		= $_REQUEST['elem_procedureName'.$k];
		$operativeReportTemplateId = $_REQUEST['elem_operativeReportTemplateIdList'.$k];
		$elem_instructionsId = $_REQUEST['elem_instructionsIdList'.$k];
		$elem_consentIdArr   = $_REQUEST['elem_consentIdList'.$k];
		$dxCodeValue		 = $_REQUEST['dxCodeVal'.$k];
		$dxCodeDefaultValue	 = $_REQUEST['dxCodeDefaultVal'.$k];	
		$cptCodeValue		 = $_REQUEST['cptCodeVal'.$k];
		$cptCodeDefaultValue = $_REQUEST['cptCodeDefaultVal'.$k];	
		$cptCodeAnesValue		 = $_REQUEST['cptCodeAnesVal'.$k];
		$cptCodeAnesDefaultValue = $_REQUEST['cptCodeAnesDefaultVal'.$k];	


		$profProcId 		= $_REQUEST['profile_insId'].'@@'.$procedureId;
		$insUpdateQry 		= " INSERT INTO ";
		$insUpdateWhrQry 	= $cptDxCodeQry= "";
		if($_REQUEST['dxCodeTyp'.$k]=='icd10')
		{
			$cptDxCodeQry 		= " dx_id_icd10				= '".$dxCodeValue."',
								dx_id_default_icd10 		= '".$dxCodeDefaultValue."',";	
		}
		else
		{
			$cptDxCodeQry 		= " dx_id				= '".$dxCodeValue."',
								dx_id_default 		= '".$dxCodeDefaultValue."',";		
		}
		$cptDxCodeQry 		.= "cpt_id				= '".$cptCodeValue."',
								cpt_id_default 		= '".$cptCodeDefaultValue."',
								cpt_id_anes			= '".$cptCodeAnesValue."',
								cpt_id_anes_default = '".$cptCodeAnesDefaultValue."', ";
		
		if(count($profProcIdArr)>0) {
			if(in_array($profProcId,$profProcIdArr)) {
				$insUpdateQry 		= " UPDATE ";
				$insUpdateWhrQry 	= " WHERE profileId = '".$_REQUEST['profile_insId']."' AND procedureId = '".$procedureId."' ";
				$cptDxCodeQry		= "";
			}
		}
		
		$insSurgeonProfileProcedureQry = $insUpdateQry." surgeonprofileprocedure set
												profileId 			= '".$_REQUEST['profile_insId']."',
												procedureId 		= '".$procedureId."',
												procedureName 		= '".addslashes($procedureName)."',
												operativeTemplateId = '".$operativeReportTemplateId."',
												instructionSheetId 	= '".$elem_instructionsId."',
												".$cptDxCodeQry."
												consentTemplateId 	= '".$elem_consentIdArr."'
												".$insUpdateWhrQry; 
		
		$insSurgeonProfileProcedureRes = imw_query($insSurgeonProfileProcedureQry) or die(imw_error().$insSurgeonProfileProcedureQry);
		
		if($insSurgeonProfileProcedureRes && count($profProcIdArr)>0)
		{
			echo "<script>top.frames[0].alert_msg('update')</script>";
		}
		else
		{
			echo "<script>top.frames[0].alert_msg('success')</script>";
		}
		
	}
		
	if($_REQUEST['profile_insId']) {
		$delInActiveProcQry = "DELETE FROM surgeonprofileprocedure WHERE profileId = '".$_REQUEST['profile_insId']."' AND procedureId NOT IN(".$procedureIdActive.")";		
		$delInActiveProcRes = imw_query($delInActiveProcQry) or die(imw_error().$delInActiveProcQry);
	}
	//die();
	//SAVE PROCEDUREID TEMPLATEID INSTRUCTIONSHEETID 
}	
//SAVE RECORD
//if(!$_REQUEST["sbtSaveSurgeonProfile"]) {
	$postOpropQry = imw_query("select * from surgeonprofile where surgeonProfileId = '".$_REQUEST['profileId']."'".$andProfileDelCond) or die(imw_error());
		
		if(imw_num_rows($postOpropQry)>0) {
			$postOpropRow = imw_fetch_array($postOpropQry);
			$intraOpPostOpOrder	= stripslashes($postOpropRow['intraOpPostOpOrder']);
			$postOpDrop 		= stripslashes($postOpropRow['postOpDrop']);
			$otherPreOpOrders 	= stripslashes($postOpropRow['otherPreOpOrders']);
			//$medicalEvaluation 	= stripslashes($postOpropRow['medicalEvaluation']);
		}
//}


?>
<!DOCTYPE html>
<html>
<head>
<title>Pre Defines</title>
<?php include("adminLinkfile.php");?>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
					//top.frames[0].frames[0].document.getElementById(Id).style.display = "none"; 
					//top.frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
				}
			}
		}
		
	}
}

function showPreOpMediDiv(name1, name2, name3,mediID, c, posLeft, posTop){
/*
	document.getElementById("preOpMediOrderDiv").style.display = 'block';
	document.getElementById("preOpMediOrderDiv").style.left = posLeft;
	document.getElementById("preOpMediOrderDiv").style.top = posTop;
	//document.getElementById("preOpMediOrderDiv").style.position = "absolute";
	//document.getElementById("preOpMediOrderDiv").style.zIndex = -200;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	document.getElementById("tertiaryValues").value = name3;
	document.getElementById("mediID").value = mediID;
*/
	top.frames[0].frames[0].$('#preOpMediOrderDiv').modal({
		show: true,
		backdrop: true,
		keyboard: true
	});
	
	
	
	/*top.frames[0].frames[0].document.getElementById("preOpMediOrderDiv").style.display = 'block';
	top.frames[0].frames[0].document.getElementById("preOpMediOrderDiv").style.left = posLeft;
	top.frames[0].frames[0].document.getElementById("preOpMediOrderDiv").style.top = posTop;*/
	//document.getElementById("preOpMediOrderDiv").style.position = "absolute";
	//document.getElementById("preOpMediOrderDiv").style.zIndex = -200;
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	document.getElementById("tertiaryValues").value = name3;
	document.getElementById("mediID").value = mediID;
	document.getElementById("mediCatID").value = 'mediCatID';
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
		//if(top.frames[0]){
			//top.setPNotesHeight();
		//}
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
}


function showSurgeonExistingForm(surgeonId, profileId, seqNmbr) {
	//alert(top.frames[0].frames[0].frames[0].name);
	
	top.frames[0].frames[0].frames[0].location.href = 'addSurgeonProcedure.php?surgeonsList='+surgeonId+'&profileId='+profileId+'&seqNmbr='+seqNmbr;
	//document.frames[0].frameSrc.source.value = 'addSurgeonProcedure.php';	
	
	/*top.frames[0].document.getElementById('saveButton').style.display = 'none';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
	//alert(top.frames[0].document.getElementById('cancelButton'));
	top.frames[0].document.getElementById('cancelButton').style.display = 'none';
	top.frames[0].document.getElementById('backButton').style.display = 'none';*/
}


function showIntraOpPostOpOrderAdminFn(name1, name2, c, posLeft, posTop){	

	top.frames[0].frames[0].$('#intraOpPostOpAdminDiv').modal({
		show: true,
		backdrop: true,
		keyboard: true
	});	
		
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

//SHOW POST OP DROP PREDEFINE DIV
function showPostOpDropsAdminFn(name1, name2, c, posLeft, posTop){	

	/*top.frames[0].frames[0].document.getElementById("evaluationPostOpDropsAdminDiv").style.display = 'block';
	top.frames[0].frames[0].document.getElementById("evaluationPostOpDropsAdminDiv").style.left = posLeft+'px';
	top.frames[0].frames[0].document.getElementById("evaluationPostOpDropsAdminDiv").style.top = posTop+'px';*/
	
	top.frames[0].frames[0].$('#evaluationPostOpDropsAdminDiv').modal({
		show: true,
		backdrop: true,
		keyboard: true
		});	
		
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

function showotherPreOpOrdersAdminFn(name1, name2, c, posLeft, posTop){	

	/*top.frames[0].frames[0].document.getElementById("evaluationPostOpDropsAdminDiv").style.display = 'block';
	top.frames[0].frames[0].document.getElementById("evaluationPostOpDropsAdminDiv").style.left = posLeft+'px';
	top.frames[0].frames[0].document.getElementById("evaluationPostOpDropsAdminDiv").style.top = posTop+'px';*/
	
	top.frames[0].frames[0].$('#otherPreOpOrdersAdminDiv').modal({
		show: true,
		backdrop: true,
		keyboard: true
		});	
		
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

function predifineCloseAdmin(id) {
	if(id=='preOpMediOrderDiv')
	{
		top.frames[0].frames[0].$('#preOpMediOrderDiv').modal({
		show: false,
		backdrop: true,
		keyboard: true
		});	
	}
	else
	top.frames[0].frames[0].document.getElementById(id).style.display = 'none';
	//onMouseMove="predifineCloseAdmin('evaluationPostOpDropsAdminDiv');predifineCloseAdmin('preOpMediOrderDiv');"
}

	
//END SHOW POST OP DROP PREDEFINE DIV

function dxCodeWinOpn(pageName,pro_id,dxCode,diagnosis_code_type,cnt,pref_card){
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	window.open(pageName+'?pro_id='+pro_id+'&dxCode='+dxCode+'&diagnosis_code_type='+diagnosis_code_type+'&cnt='+cnt+'&pref_card='+pref_card,'dx_win','width='+W+',height='+H+',resizable=1');	
}
function cptCodeWinOpn(pageName,pro_id,cptCode,cnt,pref_card){
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	
	window.open(pageName+'?pro_id='+pro_id+'&cptCode='+cptCode+'&cnt='+cnt+'&pref_card='+pref_card,'cpt_win','width='+W+',height='+H+',resizable=1');	
}

function cptCodeAWinOpn(pageName,pro_id,cptCodeA,cnt,pref_card){
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	
	window.open(pageName+'?pro_id='+pro_id+'&cptCodeA='+cptCodeA+'&cnt='+cnt+'&pref_card='+pref_card,'cpt_win','width='+W+',height='+H+',resizable=1');	
}


var ORLoad	=	function()
{
		
		var WH	=	$(window).height();
		var SH		=	$(".head_tab_inline").outerHeight(true);
		var HH		=	$(".tab-slider").outerHeight(true);
		
		var AH		=	WH	-	(SH + HH);
		
		//$(".scheduler_table_Complete").css({ 'overflow' : 'hidden' , 'min-height' : AH+'px' , 'height' : AH+'px' , 'max-height' : AH+'px' });
		
		
};
$(window).load(function() 	{  ORLoad(); });
$(window).resize(function() 	{  ORLoad(); });

</script>
</head>
<body>
<?php
//include("../common/preOpMediOrderPopUp.php");
?>

<form name="frmSurgeonProfile" action="addSurgeonProfile.php" target="_parent" method="post">
	<input type="hidden" name="profile" id="profile" value="<?php echo $profileId; ?>">
	<input type="hidden" name="frmName" id="frmName" value="frmSurgeonProfile">
	<input type="hidden" name="surgeonId" id="surgeonId" value="<?php echo $surgeonId; ?>">
	<input type="hidden" name="sbtSaveSurgeonProfile" id="sbtSaveSurgeonProfile" value="true">
	<input type="hidden" name="sbtDelProfile" id="sbtDelProfile" value="">	
	<input type="hidden" name="elem_profileName" id="elem_profileName" value="<?php echo $profileName?>">
	<input type="hidden" name="defaultProfile" id="defaultProfile" value="<?php echo $defaultProfile?>">
	<input type="hidden" name="hidd_defaultProfileStatus" id="hidd_defaultProfileStatus" value="<?php echo $hidd_defaultProfileStatus?>">
	
	<input type="hidden" name="selected_frame_name_id" id="selected_frame_name_id" value="">
	<input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
	<input type="hidden" name="tertiaryValues" id="tertiaryValues">
	<input type="hidden" name="mediID" id="mediID">
    <input type="hidden" name="mediCatID" id="mediCatID">
	<input type="hidden" name="profile_insId" id="profile_insId" value="">
	<input type="hidden" name="profileId" id="profileId" value="<?php echo $profileId;?>">
	<input type="hidden" name="seqNmbr" id="seqNmbr" value="<?php echo $_REQUEST['seqNmbr'];?>">
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
    
    <Div class="all_admin_content_agree wrap_inside_admin">
    	<Div class="wrap_inside_admin  adj_tp_table">
    		<div class="scheduler_table_Complete">
            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf table-striped">
            <thead>
            <tr>
            	<th style="min-width:50%; width:50%; max-width:50%; text-align:left"><?php echo $profileName?></th>
                <th style="min-width:50%; width:50%; max-width:50%; text-align:right">
				<?php 
				/*$defaultProfileImageSrc = "fa-times";
				if($defaultProfile=='1') {
					$defaultProfileImageSrc = "fa-check";
				}*/
				?>
                <!-- <span class="btn btn-group"> <b class="fa <?php echo $defaultProfileImageSrc;?>"></b> Default Profile</span>-->
                </th>
            </tr>
            </thead>
            </table>
            
                <div class="my_table_Checkall adj_tp_table">
                <div class="scheduler_table_Complete ">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12  table-condensed cf table-striped" onClick="preCloseFun('evaluationPostOpDropsAdminDiv');preCloseFun('preOpMediOrderDiv');preCloseFun('otherPreOpOrdersAdminDiv');">
              
		
		<tr>
			<td style="color:#800080;cursor:pointer; font-weight:bold" onClick="return showPreOpMediDiv('medication_quest', 'strength_quest', 'directions_quest','mediID', '20', '5', '0'),document.getElementById('selected_frame_name_id').value='iframe_surgeonProfileSpeadsheet';" > Pre Op Orders <!-- onClick="return showProceduresFn('op_proced_area_id', '', 'no', '510', '365')" -->
			</td>	
		</tr>	
       <tr><td><table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf table-striped" onClick="preCloseFun('evaluationPostOpDropsAdminDiv');preCloseFun('preOpMediOrderDiv');preCloseFun('otherPreOpOrdersAdminDiv');">
            
            <tr>
                <th class="text_10b text-center" style="width:33%;">Medication</th>
                <th class="text_10b text-center" style="width:33%;">Strength</th>
                <th class="text_10b text-center" style="width:auto;">Direction</th>
            </tr>
            
            <tbody>
            <tr>
                <td colspan="3">
                     <iframe name="iframe_surgeonProfileSpeadsheet" src="surgeonProfileSpeadsheet.php?profileId=<?php echo $_REQUEST['profileId']; ?>&amp;surgeonId=<?php echo $surgeonId; ?>&amp;allgNameWidth=270&amp;allgReactionWidth=270&amp;pref_card=<?php echo $_POST['pref_card'];?>" style="width:100%; height:135px; padding:0px;"  frameborder="0"  scrolling="yes" class="inner_iframe"></iframe>    
                </td>
            </tr>
            </tbody>
        </table></td></tr> 
		<tr class="alignLeft">
			<td>
			<table style="width:100%;" onClick="preCloseFun('evaluationPostOpDropsAdminDiv');preCloseFun('preOpMediOrderDiv');preCloseFun('otherPreOpOrdersAdminDiv');">
				<tr>
					
					<td style=" padding-left:20px;padding-right:5px; color:#800080; cursor:pointer;font-weight:bold; text-align:right; width:10%" onClick="return showIntraOpPostOpOrderAdminFn('intraOpPostOpOrderId', '', 'no', '50', '320'),document.getElementById('selected_frame_name_id').value='';">Intra&nbsp;Op&nbsp;Post&nbsp;Op&nbsp;Order</td>
                    <td class="text_10 alignLeft" style="width:23%;">
						<textarea  id="intraOpPostOpOrderId" name="intraOpPostOpOrder"  class="field textarea justi text_10" style="border:1px solid #cccccc; width:97%; height:40px; "  tabindex="6"><?php echo $intraOpPostOpOrder.$pref_card_data->intraOpPostOpOrder;?></textarea>
					</td>
                    <td  style=" padding-left:20px;padding-right:5px; color:#800080; cursor:pointer;font-weight:bold; text-align:right; width:10%" onClick="return showPostOpDropsAdminFn('postOpDropId', '', 'no', '50', '320'),document.getElementById('selected_frame_name_id').value='';">Post-Op Drops </td>
					<td class="text-left" style="width:23%">
						<textarea  id="postOpDropId" name="postOpDrop"  class="form-control" style="border:1px solid #cccccc; width:97%; height:40px;" tabindex="6"  ><?php echo $postOpDrop.$pref_card_data->postOpDrop;?></textarea>
					</td>
                    <td class="text_10b nowrap alignLeft" style="padding-left:20px;padding-right:5px; color:#800080; cursor:pointer;font-weight:bold; text-align:right; width:10% " onClick="return showotherPreOpOrdersAdminFn('otherPreOpOrdersId', '', 'no', '50', '320'),document.getElementById('selected_frame_name_id').value='';">Other Pre-Op Orders</td>
                    <td class="text_10 alignLeft" style="width:23%;">
						<textarea  id="otherPreOpOrdersId" name="otherPreOpOrders"  class="form-control" style="border:1px solid #cccccc; width:97%; height:40px; "  tabindex="6"  ><?php echo $otherPreOpOrders.$pref_card_data->otherPreOpOrders;?></textarea>
					</td>
                    
                    <!--<td class="text_10b nowrap alignLeft" style="padding-left:20px;padding-right:5px;width:140px; ">Medical&nbsp;Evaluation</td>
                    <td class="text_10 alignLeft">
						<textarea  id="medicalEvaluationId" name="medicalEvaluation"  class="field textarea justi text_10" style="border:1px solid #cccccc; width:280px; height:40px; "  tabindex="6"  ><?php //echo $medicalEvaluation;?></textarea>
					</td>-->
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td>
				<table style="width:100%;" onClick="preCloseFun('evaluationPostOpDropsAdminDiv');preCloseFun('preOpMediOrderDiv');preCloseFun('otherPreOpOrdersAdminDiv');">
					<tr>
						<td style="width:1px;"></td>
						<td>
							<div style="height:130px; overflow:auto; overflow-x:hidden;">
								<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf table-striped table-bordered">
									<?php
									$surgeonProfileProcedureId=array();
									$chkBox = $_REQUEST['chkBox'];
									//if($chkBox=="") { 
										if($_REQUEST['profileId']<>'') {
											$selectSurgeonProfileProcedureQry 			= "select * from surgeonprofileprocedure where profileId = '".$_REQUEST['profileId']."' AND procedureName != '' order by procedureName";
											$selectSurgeonProfileProcedureRes 			= imw_query($selectSurgeonProfileProcedureQry) or die(imw_error());
											while($selectSurgeonProfileProcedureRow 	= imw_fetch_array($selectSurgeonProfileProcedureRes)) {
												if(is_array($chkBox)) {
													if(in_array($selectSurgeonProfileProcedureRow['procedureId'],$chkBox)) {
														$procIdTmp 						= $selectSurgeonProfileProcedureRow['procedureId'];
														$proAutoId[$procIdTmp] 			= $selectSurgeonProfileProcedureRow['id'];
														$surgeonProfileProcedureId[] 	= $selectSurgeonProfileProcedureRow['procedureId'];
														$opRepTemplateId[$procIdTmp] 	= $selectSurgeonProfileProcedureRow['operativeTemplateId'];
														$insSheetId[$procIdTmp] 		= $selectSurgeonProfileProcedureRow['instructionSheetId'];
														$consentTemplateId[$procIdTmp] 	= $selectSurgeonProfileProcedureRow['consentTemplateId'];
														/*
														$proCptId[$procIdTmp] 			= $selectSurgeonProfileProcedureRow['cpt_id'];
														$proCptIdDefault[$procIdTmp] 	= $selectSurgeonProfileProcedureRow['cpt_id_default'];
														$proDxId[$procIdTmp] 			= $selectSurgeonProfileProcedureRow['dx_id'];
														$proDxIdDefault[$procIdTmp] 	= $selectSurgeonProfileProcedureRow['dx_id_default'];
														*/
													}/*else {
														$surgeonProfileProcedureId[] 	= $selectSurgeonProfileProcedureRow['procedureId'];
														$opRepTemplateId[] 				= $selectSurgeonProfileProcedureRow['operativeTemplateId'];
														$insSheetId[] 					= $selectSurgeonProfileProcedureRow['instructionSheetId'];
													
													}*/
												}else {
													$procIdTmp 							= $selectSurgeonProfileProcedureRow['procedureId'];
													$proAutoId[$procIdTmp] 				= $selectSurgeonProfileProcedureRow['id'];
													$surgeonProfileProcedureId[] 		= $selectSurgeonProfileProcedureRow['procedureId'];
													$opRepTemplateId[$procIdTmp] 		= $selectSurgeonProfileProcedureRow['operativeTemplateId'];
													$insSheetId[$procIdTmp] 			= $selectSurgeonProfileProcedureRow['instructionSheetId'];
													$consentTemplateId[$procIdTmp] 		= $selectSurgeonProfileProcedureRow['consentTemplateId'];
													/*
													$proCptId[$procIdTmp] 				= $selectSurgeonProfileProcedureRow['cpt_id'];
													$proCptIdDefault[$procIdTmp] 		= $selectSurgeonProfileProcedureRow['cpt_id_default'];
													$proDxId[$procIdTmp] 				= $selectSurgeonProfileProcedureRow['dx_id'];
													$proDxIdDefault[$procIdTmp] 		= $selectSurgeonProfileProcedureRow['dx_id_default'];
													*/
												}
												
											}
											
											if(!is_array($chkBox)) {
												$chkBox=array();
											}
											if(!is_array($chkBox)) {
												$surgeonProfileProcedureId=array();
											}
											//if(is_array($chkBox)) {
												$chkBoxTemp= array_merge($chkBox,$surgeonProfileProcedureId);
												$chkBox = array_unique($chkBoxTemp);
											/*
											}else {
												$chkBox = $surgeonProfileProcedureId;
											}*/	
										}
									//} 
									if($chkBox) {
									?>
									<tr class="alignLeft" >
										<td style="width:150px;">
											<label class="text_10b" style="background:background-color:#F1F4F0;">Codes</label>
										</td>
										<td>
											<label class="text_10b" style="background:background-color:#F1F4F0;">Procedure</label>
										</td>
										<td>
											<label class="text_10b" style="background:background-color:#F1F4F0;">Operative Report</label>									</td>
										<td>
											<label class="text_10b" style="background:background-color:#F1F4F0;">Instruction Sheet</label>									</td>
										<td>
											<label class="text_10b" style="background:background-color:#F1F4F0;">Consent Template</label>										<input type="hidden" name="counterId" value="<?php echo count($chkBox);?>">
										</td>
									</tr>
									<?php
										$i=0;
										//print_r($chkBox);
											if($chkBox) {
												$chkBoxImplodeTemp 			= implode("','",$chkBox);
												if($chkBoxImplodeTemp) {
													$sortChkBoxIdQry 		= "select procedureId from procedures where procedureId in('$chkBoxImplodeTemp') order by `name`";
													$sortChkBoxIdRes 		= imw_query($sortChkBoxIdQry) or die(imw_error());
													$sortChkBoxId 			= array();
													while($sortChkBoxIdRow 	= imw_fetch_array($sortChkBoxIdRes)) {
														$sortChkBoxId[] 	= $sortChkBoxIdRow['procedureId'];
													}
												}
											}	
										
										if($sortChkBoxId) {
											foreach($sortChkBoxId as $prededines_id){
												$i++;
												//echo $prededines_id.",";
												//$objManageData->delRecord($table, $idField, $prededines_id);
												$proQry = "select * from procedures where procedureId='$prededines_id' order by `name`";
												$proRes = imw_query($proQry) or die(imw_error());
												$proRow = imw_fetch_array($proRes);
												$procedureName = stripslashes($proRow["name"]);
												if($i%2==0) {
													$bgcolor = "";
												}else { $bgcolor = "#FFFFFF"; }
											//echo $insSheetId[$i-1];
										?>
									<tr class="alignLeft valignMiddle" style="background-color:<?php echo $bgcolor;?>;">
										
										<td style=" color:#800080; cursor:pointer;">
                                            
                                            <div id="cptCode" onClick="cptCodeWinOpn('cpt_dx_profile.php','<?php echo $proAutoId[$prededines_id]; ?>','yes','<?php echo $i; ?>','<?php if($pref_card_data->procedureId==$prededines_id){echo $_POST['pref_card'];}?>');" class="btn btn-success padding_6" >
												CPT
											</div>
											<div id="cptCodeA" onClick="cptCodeAWinOpn('cpt_dx_profile.php','<?php echo $proAutoId[$prededines_id]; ?>','yes','<?php echo $i; ?>','<?php if($pref_card_data->procedureId==$prededines_id){echo $_POST['pref_card'];}?>');" class="btn btn-warning padding_6" >
												CPT-A
											</div>
                                            <div id="dxCode" onClick="dxCodeWinOpn('cpt_dx_profile.php','<?php echo $proAutoId[$prededines_id]; ?>','yes','<?php echo $diagnosis_code_type; ?>','<?php echo $i; ?>','<?php if($pref_card_data->procedureId==$prededines_id){echo $_POST['pref_card'];}?>');"  class="btn btn-primary padding_6"  >
												Dx
											</div>
                                            
										</td>
										<td><?php echo $procedureName;?>
											<input type="hidden" name="elem_procedureName<?php echo $i;?>" value="<?php echo $procedureName;?>">
											<input type="hidden" name="procedureId<?php echo $i;?>" value="<?php echo $prededines_id;?>">
											<input type="hidden" name="elem_procedureName<?php echo $i;?>" value="<?php echo $procedureName;?>">
										</td>
										<td>
										<select class="form-control" name="elem_operativeReportTemplateIdList<?php echo $i;?>" >
                                            <option value="">Select</option>
                                            <?php										
                                            $preOpTemplates = $objManageData->getArrayRecords('operative_template','surgeonId',$surgeonId,'template_name','asc');
											$condition_arr['1']='1';
											$communityTemplateLists = $objManageData->getMultiChkArrayRecords('operative_template', $condition_arr, $orderBy=0, $sortOrder=0, " AND surgeonId='0'");
											if($preOpTemplates){
											?>
                                        <optgroup label="Surgeon Templates" data-icon="glyphicon glyphicon-hand-right" class="optgroup">
                                            <?php
                                            foreach($preOpTemplates as $templates){
                                                $selected="";
												//check for selected
												if($opRepTemplateId[$prededines_id]==$templates->template_id)$selected=" selected";											
												elseif($pref_card_data->operativeTemplateId==$templates->template_id && $pref_card_data->procedureId==$prededines_id)$selected=" selected";
												?>
                                                <!--<option value="<?php //echo $templates->template_id; ?>" <?php //if($opRepTemplateId[$i-1]==$templates->template_id) echo "SELECTED"; ?>><?php //echo stripslashes($templates->template_name); ?></option>-->
                                                <option value="<?php echo $templates->template_id; ?>" <?php echo $selected; ?>><?php echo stripslashes($templates->template_name); ?></option>
                                                <?php
                                            }//while end here
											?>
                                            </optgroup>
                                            <?php
											}//if end here
											if($communityTemplateLists){
											?>
											<optgroup label="Community Templates" data-icon="glyphicon glyphicon-hand-right" class="optgroup">
											<?php
												foreach($communityTemplateLists as $key => $list){
													$selected="";
													//check for selected
													if($opRepTemplateId[$prededines_id]==$list->template_id)$selected=" selected";											
													elseif($pref_card_data->operativeTemplateId==$list->template_id && $pref_card_data->procedureId==$prededines_id)$selected=" selected";
											?>
													<option value="<?php echo $list->template_id;?>" <?php echo $selected;?>><?php echo stripslashes($list->template_name);?></option>
											<?php }?>
											</optgroup>
											<?php }?>
                                        </select>
										</td>
										<td>
                                        <select name="elem_instructionsIdList<?php echo $i;?>" class="form-control" >
                                            <option value="">Select</option>
                                            <?php
                                            $insSheetTemplates = $objManageData->getArrayRecords('instruction_template','','','instruction_name','asc');
                                            foreach($insSheetTemplates as $templates){
												$selected="";
												//check for selected
												if($insSheetId[$prededines_id]==$templates->instruction_id)$selected=" selected";											
												elseif($pref_card_data->instructionSheetId==$templates->instruction_id && $pref_card_data->procedureId==$prededines_id)$selected=" selected";
                                                ?>
                                                <!--<option value="<?php //echo $templates->instruction_id; ?>" <?php //if($insSheetId[$i-1]==$templates->instruction_id) echo "SELECTED"; ?>><?php //echo stripslashes($templates->instruction_name); ?></option>-->
                                                <option value="<?php echo $templates->instruction_id; ?>" <?php echo $selected; ?>><?php echo stripslashes($templates->instruction_name); ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
										</td>
										<td>
                                        <?php
                                        //put default values if pref card is selected for one click save
										$dxCodeVal=$dxCodeTyp=$dxCodeDefaultVal=$cptCodeVal=$cptCodeDefaultVal=$consentTemplateIds='';
										if($pref_card_data->procedureId==$prededines_id)
										{
											$dxCodeTyp=$diagnosis_code_type;
											if($diagnosis_code_type=='icd9')
											{
												$dxCodeVal=$pref_card_data->dx_id;
												$dxCodeDefaultVal=$pref_card_data->dx_id_default;
											}
											else
											{
												$dxCodeVal=$pref_card_data->dx_id_icd10;
												$dxCodeDefaultVal=$pref_card_data->dx_id_default_icd10;	
											}
											$cptCodeVal=$pref_card_data->cpt_id;
											$cptCodeDefaultVal=$pref_card_data->cpt_id_default;
											$cptCodeAnesVal=$pref_card_data->cpt_id_anes;
											$cptCodeAnesDefaultVal=$pref_card_data->cpt_id_anes_default;
											$consentTemplateIds=$pref_card_data->consentTemplateId;
										}
										?>
											<input type="hidden" id="dxCodeVal<?php echo $i; ?>" name="dxCodeVal<?php echo $i; ?>" value="<?php echo $dxCodeVal;?>" >														 											
                                            <input type="hidden" id="dxCodeTyp<?php echo $i; ?>" name="dxCodeTyp<?php echo $i; ?>" value="<?php echo $dxCodeTyp;?>" >														 											
                                            <input type="hidden" id="dxCodeDefaultVal<?php echo $i; ?>" name="dxCodeDefaultVal<?php echo $i; ?>" value="<?php echo $dxCodeDefaultVal;?>">
											<input type="hidden" id="cptCodeVal<?php echo $i; ?>" name="cptCodeVal<?php echo $i; ?>" value="<?php echo $cptCodeVal;?>" >														 											
                                            <input type="hidden" id="cptCodeDefaultVal<?php echo $i; ?>" name="cptCodeDefaultVal<?php echo $i; ?>" value="<?php echo $cptCodeDefaultVal;?>">
                                            <input type="hidden" id="cptCodeAnesVal<?php echo $i; ?>" name="cptCodeAnesVal<?php echo $i; ?>" value="<?php echo $cptCodeAnesVal;?>" >														 											
                                            <input type="hidden" id="cptCodeAnesDefaultVal<?php echo $i; ?>" name="cptCodeAnesDefaultVal<?php echo $i; ?>" value="<?php echo $cptCodeAnesDefaultVal;?>">
											<input type="hidden" name="elem_consentIdList<?php echo $i;?>" id="pro_cons<?php echo $i;?>" value="<?php echo $consentTemplateId[$prededines_id].$consentTemplateIds; ?>">
											<a href="javascript:;" onClick="window.open('procedure_consent.php?pro_id=<?php echo $proAutoId[$prededines_id]; ?>&amp;cont=<?php echo $i;?>&amp;pref_card=<?php if($pref_card_data->procedureId==$prededines_id){echo $_POST['pref_card'];}?>','','width=350,height=500,resizable=1');" class="btn btn-info" id="procedureButton<?php echo $i;?>" alt="Procedure Consents">Procedure Consents</a>
										</td>	
									</tr>
									<?php
										}
									}	
								}
								?>		
								</table>
							</div>	
						</td>
						<td style="width:1px;"></td>
					</tr>
				</table>
			</td>
		</tr>				
	</table>
                </div>
                    
    
                </div>
            </div>
        </Div>
    </Div>
    
            
    
        
	
	
</form>	

<form name="frmSurgeonDeleteProfile" action="addSurgeonProfile.php" method="post">
	<input type="hidden" name="sbtDelProfile" value="">
	<input type="hidden" name="sbtDelProfileId" id="sbtDelProfileId" value="<?php echo $profileId;?>">
	<input type="hidden" name="surgeonId" value="<?php echo $surgeonId; ?>">
	<input type="hidden" name="seqNmbr" value="<?php echo $_REQUEST['seqNmbr'];?>">
</form>
	<?php
	if(is_array($chkBox)) {
		$chkBoxImplode = implode(',',$chkBox);
	}
	?>
	<form name="frmSurgeonBack" action="addSurgeonProcedure.php?surgeonId=<?php echo $surgeonId; ?>&amp;surgeonsList=<?php echo $surgeonId; ?>&amp;profileId=<?php echo $profileId;?>" method="post">
		<input type="hidden" name="pref_card" id="pref_card" value="<?php echo $_POST['pref_card'];?>">
		<input type="hidden" name="profileName" id="profileName" value="<?php echo $profileName;?>">
		<input type="hidden" name="chkBoxBack" id="chkBoxBack" value="<?php echo $chkBoxImplode;?>">
		<input type="hidden" name="defaultProfile" value="<?php echo $defaultProfile;?>">
	</form>
<?php
if($_REQUEST["sbtSaveSurgeonProfile"]=="true") {
	if($profileId<>'' && $seqNmbr=='') {
		$seqNmbrQry = imw_query("select * from surgeonprofile where surgeonId = '$surgeonId'".$andProfileDelCond) or die(imw_error());
		$seqNmbr = imw_num_rows($seqNmbrQry); 
	}
?>
	<script>
		var surgeonsListID = '<?php echo $surgeonId;?>';
		if(surgeonsListID=='') {
			var surgeonsListID = document.frmSurgeonProfile.surgeonId.value;
		}
		var profId = '<?php echo $profileId;?>';
		var seqNmbr = '<?php echo $seqNmbr;?>';
		var surgeonProfileFrame = eval(surgeonProfileFrame);
		top.frames[0].frames[0].location.href = 'surgeonprofile.php?surgeonId='+surgeonsListID+'&surgeonsList='+surgeonsListID+'&profileId='+profId+'&seqNmbr='+seqNmbr+'&sbtloc=yes';
	</script>
<?php
}

if($sbtDelProfileId<>"") {
?>
	<script>
		var surgeonsListID = '<?php echo $surgeonId;?>';
		top.frames[0].frames[0].location.href = 'surgeonprofile.php?surgeonId='+surgeonsListID+'&surgeonsList='+surgeonsListID+'&sbtloc=del';
	</script>

<?php	
}

if($profileId<>'') {
?>
	<script>
		top.frames[0].frames[0].show_hideButtons('<?php echo $profileId;?>');
	</script>
<?php
}
?>	
</body>
</html>