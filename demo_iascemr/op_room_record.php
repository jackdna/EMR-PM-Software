<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$current_form_version = 5;
$tablename = "operatingroomrecords";
//SET COLOR VARIABLE TO ROWS
$title_op_room_record="#004587"; 
$bgcolor_op_room_record="#CFE1F7";
$border_op_room_record="#004587";
$heading_op_room_record="#80A7D6";
$rowcolor_op_room_record="#E2EDFB";
//END SET COLOR VARIABLE TO ROWS
?>
<!DOCTYPE html>
<html>
<head>
<title>Operating Room Record</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<script src="js/webtoolkit.aim.js"></script>
<style>
	.drsElement {
		position: absolute;
		border: 1px solid #333;
	}
	.drsMoveHandle {
		height: 20px;
		background-color: #CCC;
		border-bottom: 1px solid #666;
	}
	.bg	{	background-color:"#F1F4F0";		}
	.text_formb12{ font-family:"verdana"; font-size:11px; color:#0066FF; margin:-1.55em 0 0 25px;}
	.borderM{
		border-right-color:#000000;
		border-right-width:"3";
	}
	.borderMTB{
		border-bottom-color:#000000;
		border-bottom-width:1;
		border-top-color:#000000;
		border-top-width:3;
	}
	.borderML{
		border-left-color:#000000;
		border-left-width:3;
	}
	.leftbnone{
		border-bottom-color:#000000;
		border-bottom-width:1;
		border-top-color:#000000;
		border-top-width:1;
		border-right-width:1;
	}
	.checkM{
		display:block;
		line-height:1.4em;
		margin:46px 42px 42px 43px;
		width:43px;
		height:73px;
	}
</style>
<?php
$spec = '</head>
<body onLoad="'.$onloadFun.'; setScrollTopFn();" onClick="document.getElementById(\'divSaveAlert\').style.display = \'none\'; closeEpost();return top.frames[0].main_frmInner.hideSliders();">';
include("common/link_new_file.php");

//START INCLUDE PREDEFINE FUNCTIONS
	include_once("common/pre_define_diagnosis.php");
	include_once("common/post_define_diagnosis.php");
	include_once("common/pre_define_procedure.php");
	include_once("common/post_op_drops_pop.php");
	include_once("common/complications_pop.php");
	include_once("common/intraOpPostOpPop.php");
	include_once("common/nurse_notes_pop.php");
	
	
//START INCLUDE PREDEFINE FUNCTIONS

include_once("common/commonFunctions.php"); 
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
extract($_GET);
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$scan = $_REQUEST['scan'];
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}	
$patient_id = $_REQUEST["patient_id"];
if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}	
$configurationDetail= $objManageData->getExtractRecord('surgerycenter','surgeryCenterId','1','suppliesHostName, suppliesUsername, suppliesPassword, sx_plan_sheet_review');
$suppliesHostName 	= $configurationDetail['suppliesHostName'];
$suppliesUsername 	= $configurationDetail['suppliesUsername'];
$suppliesPassword 	= $configurationDetail['suppliesPassword'];
$sxPlanSheetReviewAdmin=$configurationDetail['sx_plan_sheet_review'];
$ftpSupp = false;
if(trim($suppliesHostName) && trim($suppliesUsername) && trim($suppliesPassword)) {
	$ftpSupp = true;
}
if($scan){
	unset($arrayRecord);
	$arrayRecord['user_id'] = $_SESSION['loginUserId'];
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['confirmation_id'] = $pConfId;
	$arrayRecord['form_name'] = 'intra_op_record_form';
	$arrayRecord['status'] = 'scaned';
	$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
	$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
}
	//CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
	$patient_id = $_REQUEST["patient_id"];
	$ascId = $_REQUEST["ascId"];
	$pConfId = $_REQUEST["pConfId"];
	$thisId = $_REQUEST["thisId"];
	if($innerKey=="") {
		$innerKey = $_REQUEST["innerKey"];
	}
	if($preColor=="") {
		$preColor = $_REQUEST["preColor"];
	}	
	$fieldName = "intra_op_record_form";
	$pageName = "op_room_record.php?patient_id=$patient_id&amp;pConfId=$pConfId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId";
	}
	include("left_link_hide.php");
	//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
	
	//FUNCTION TO GET USER SIGNATURE FROM USER TABLE
	function getUserSign($UserId,$UserType) {
		$ViewUserNameQry = "select * from `users` where  usersId = '".$UserId."' and user_type ='".$UserType."'";
		$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
		$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
		$UserSign = $ViewUserNameRow["signature"];
		return $UserSign;
	}
	//END FUNCTION TO GET USER SIGNATURE FROM USER TABLE
	//GET PATIENT DETAIL
	$OpRoom_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_REQUEST['patient_id']."'";
	$OpRoom_patientName_tblRes = imw_query($OpRoom_patientName_tblQry) or die(imw_error());
	$OpRoom_patientName_tblRow = imw_fetch_array($OpRoom_patientName_tblRes);
	$OpRoom_patientName = $OpRoom_patientName_tblRow["patient_fname"]." ".$OpRoom_patientName_tblRow["patient_mname"]." ".$OpRoom_patientName_tblRow["patient_lname"];
	$OpRoom_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$OpRoom_patientConfirm_tblRes = imw_query($OpRoom_patientConfirm_tblQry) or die(imw_error());
	$OpRoom_patientConfirm_tblRow = imw_fetch_array($OpRoom_patientConfirm_tblRes);
	
	

	$OpRoom_patientConfirmDosTemp = $OpRoom_patientConfirm_tblRow["dos"];
	$OpRoom_patientConfirmDos_split = explode("-",$OpRoom_patientConfirmDosTemp);
	$OpRoom_patientConfirmDos = $OpRoom_patientConfirmDos_split[1]."-".$OpRoom_patientConfirmDos_split[2]."-".$OpRoom_patientConfirmDos_split[0];
	$OpRoom_patientConfirmSurgeon = $OpRoom_patientConfirm_tblRow["surgeon_name"];
	$OpRoom_patientConfirmSiteTemp = $OpRoom_patientConfirm_tblRow["site"];
	$OpRoom_patientConfirmAnes_NA = $OpRoom_patientConfirm_tblRow["anes_NA"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($OpRoom_patientConfirmSiteTemp == 1) {
			$OpRoom_patientConfirmSite = "Left Eye";  //OD
		}else if($OpRoom_patientConfirmSiteTemp == 2) {
			$OpRoom_patientConfirmSite = "Right Eye";  //OS
		}else if($OpRoom_patientConfirmSiteTemp == 3) {
			$OpRoom_patientConfirmSite = "Both Eye";  //OU
		}else if($OpRoom_patientConfirmSiteTemp == 4) {
			$OpRoom_patientConfirmSite = "Left Upper Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 5) {
			$OpRoom_patientConfirmSite = "Left Lower Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 6) {
			$OpRoom_patientConfirmSite = "Right Upper Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 7) {
			$OpRoom_patientConfirmSite = "Right Lower Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 8) {
			$OpRoom_patientConfirmSite = "Bilateral Upper Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 9) {
			$OpRoom_patientConfirmSite = "Bilateral Lower Lid";
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$OpRoom_patientConfirmPrimProc = $OpRoom_patientConfirm_tblRow["patient_primary_procedure"];
	 $OpRoom_patientConfirmSecProc = $OpRoom_patientConfirm_tblRow["patient_secondary_procedure"];
	 if($OpRoom_patientConfirmSecProc!="N/A"){
	   $OpRoom_patientConfirmSecProcTemp="Yes";
	 }else{
	     $OpRoom_patientConfirmSecProcTemp=" ";
	 }
	 $OpRoom_patientConfirmAnesthesiologistId = $OpRoom_patientConfirm_tblRow["anesthesiologist_id"];
	 $OpRoom_patientConfirmNurseId = $OpRoom_patientConfirm_tblRow["nurseId"];
	 $OpRoom_patientConfirmSurgeonId = $OpRoom_patientConfirm_tblRow["surgeonId"];
	 $OpRoom_patientConfirmAnesthesiologistName = trim(stripslashes($OpRoom_patientConfirm_tblRow["anesthesiologist_name"]));
	
	//GET ANESTHESIOLOGIST NAME, NURSE NAME, SURGEON NAME 
	//$OpRoomAnesthesiologistName = getUserName($OpRoom_patientConfirmAnesthesiologistId,'Anesthesiologist');
	$OpRoomAnesthesiologistName = $objManageData->getUserName($_SESSION['loginUserId'],'Anesthesiologist');
	$OpRoomNurseName = $objManageData->getUserName($_SESSION['loginUserId'],'Nurse');
	$OpRoomSurgeonName = $objManageData->getUserName($OpRoom_patientConfirmSurgeonId,'Surgeon');
	//END GET ANESTHESIOLOGIST NAME, NURSE NAME, SURGEON NAME 

	$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId;

	//SAVE RECORD TO DATABASE
	if($_POST['SaveRecordForm']=='yes'){

		$text = $_REQUEST['getText'];
		$tablename = "operatingroomrecords";
	    $verifiedbyNurse = $_POST["chbx_vbyn"];
	    $verifiedbyNurseName = trim(addslashes($_POST["hidd_verifiedbyNurseName"]));
		
		$verifiedbySurgeon = addslashes($_POST["chbx_vbys"]) ;
	    $verifiedbyAnesthesiologist  = $_POST["chbx_vbya"];
		$sxPlanReviewedBySurgeon	= $_POST["chbx_sx_rbys"];
	    $verifiedbyAnesthesiologistName = trim(addslashes($_POST["hidd_verifiedbyAnesthesiologistName"]));
		
		$verifiedbyNurseTime="";
		if(strtotime($_REQUEST['verifiedbyNurseTime'])) {
			$verifiedbyNurseTimeExplode = explode(" ",$_REQUEST['verifiedbyNurseTime']);
			if((!$verifiedbyNurseTimeExplode[1] && !stristr($_REQUEST['verifiedbyNurseTime'],'P') && !stristr($_REQUEST['verifiedbyNurseTime'],'A')) || strtoupper($verifiedbyNurseTimeExplode[1])=='AM' || strtoupper($verifiedbyNurseTimeExplode[1])=='PM') {
				$verifiedbyNurseTime = $objManageData->setTmFormat($_REQUEST['verifiedbyNurseTime'],'static');
			}
		}
		$hidd_verifiedbyNurse = $_POST["hidd_chbx_vbyn"];
	    $hidd_verifiedbySurgeon =$_POST["hidd_chbx_vbys"] ;
		$hidd_sxPlanReviewedBySurgeon =$_POST["hidd_chbx_sx_rbys"];
		$hidd_sxPlanRvwBySrgnDtm =$_POST["hidd_sxPlanRvwBySrgnDtm"];
		$hidd_sxPlanReviewedBySurgeonChk =$_POST["hidd_sxPlanReviewedBySurgeonChk"];
	    $hidd_verifiedbyAnesthesiologist  = $_POST["hidd_chbx_vbya"];
		if($verifiedbyNurse=="") {
			$verifiedbyNurse = $hidd_verifiedbyNurse;
		}
		if($verifiedbyAnesthesiologist=="") {
			$verifiedbyAnesthesiologist = $hidd_verifiedbyAnesthesiologist;
		}
		//IF STILL NURSE CHECKBOX IS BLANK THEN LEAVE verifiedbyNurseName, verifiedbyAnesthesiologist FIELD BLANK
			if($verifiedbyNurse=="") {
				$verifiedbyNurseName = "";
			}
			if($verifiedbyAnesthesiologist=="") {
				$verifiedbyAnesthesiologistName = "";
			}
		//IF STILL NURSE CHECKBOX IS BLANK THEN LEAVE verifiedbyNurseName, verifiedbyAnesthesiologist FIELD BLANK
			
		if($verifiedbySurgeon=="") {
			$verifiedbySurgeon = $hidd_verifiedbySurgeon;
		}
		if($sxPlanReviewedBySurgeon=="") {
			$sxPlanReviewedBySurgeon = $hidd_sxPlanReviewedBySurgeon;
		}
		
		$preOpDiagnosis = addslashes($_POST["preOpDiagnosis"]);
		$operativeProcedures = addslashes($_POST["operativeProcedures"]);
		$product_control_na = $_POST["product_control_na"];
		$bssValue = $_POST["chbx_bss"];
		$iol_na = $_POST['iol_na'];
		$Epinephrine03 = $_POST["Epinephrine03"];
		$Vancomycin01 = $_POST["Vancomycin01"];
		$Vancomycin02 = $_POST["Vancomycin02"];
		$omidria	=	$_POST['omidria'];
		$InfusionOtherChk = $_POST["InfusionOtherChk"];
		if($InfusionOtherChk == "Yes") {
			$infusionBottleOther = addslashes($_POST["infusionBottleOther"]);
		}else{
			$infusionBottleOther = "";
		}
		/*
		$Healon = $_POST["Healon"];
		$Occucoat = $_POST["Occucoat"]; 
		$Provisc = $_POST["Provisc"];
		$Miostat = $_POST["Miostat"];
		$HealonGV = $_POST["HealonGV"];
		$Discovisc = $_POST["Discovisc"];
		$AmviscPlus = $_POST["AmviscPlus"];
		$TrypanBlue = $_POST["TrypanBlue"];
		$Healon5 = $_POST["Healon5"]; 
		$Viscoat = $_POST["Viscoat"];
		$Miochol = $_POST["Miochol"];
		
		$HealonList = $_POST["HealonList"];
		$OccucoatList = $_POST["OccucoatList"]; 
		$ProviscList = $_POST["ProviscList"];
		$MiostatList = $_POST["MiostatList"];
		$HealonGVList = $_POST["HealonGVList"];
		$DiscoviscList = $_POST["DiscoviscList"];
		$AmviscPlusList = $_POST["AmviscPlusList"];
		$Healon5List = $_POST["Healon5List"]; 
		$ViscoatList = $_POST["ViscoatList"];
		$MiocholList = $_POST["MiocholList"];*/
		$OtherSuppliesUsed = addslashes($_POST["OtherSuppliesUsed"]);
		$percent_txt = $_POST["percent_txt"];
		$percent = $_POST["percent"];
		$XylocaineMPF = $_POST["XylocaineMPF"];
		$manufacture = $_POST["manufacture"];
		$lensBrand = $_POST["lensBrand"];
		$iol_comments = addslashes($_POST["iol_comments"]);
		//$post2DischargeSummary = $_POST["post2DischargeSummary"];
		//$post2OperativeReport = $_POST["post2OperativeReport"];
		//$model = addslashes($_REQUEST["model"]);
		$model = addslashes($_REQUEST["model"]);
		$Diopter = $_POST["Diopter"];
		$iolConfirmedSurgeonSignOnFile = $_POST["iolConfirmedSurgeonSignOnFile"];
		$prep_solution_na = $_POST["prep_solution_na"];
		$Betadine = $_POST["Betadine"];
		$Saline = $_POST["Saline"];
		$Alcohol = $_POST["Alcohol"];
		$Prcnt5Betadinegtts = $_POST["Prcnt5Betadinegtts"];
		$proparacaine = $_POST["proparacaine"];
		$tetracaine = $_POST["tetracaine"];
		$tetravisc = $_POST["tetravisc"];
		$prepSolutionsOther = addslashes($_POST["prepSolutionsOther"]);
		$surgeryORNumber=$_REQUEST['surgeryORNumber'];
		$surgeryTimeIn="";
		$surgeryTimeIn = $objManageData->setTmFormat($_REQUEST['surgeryTimeIn'],'static');
		/*
		if(strtotime($_REQUEST['surgeryTimeIn'])) {
			$surgeryTimeInExplode = explode(" ",$_REQUEST['surgeryTimeIn']);
			if((!$surgeryTimeInExplode[1] && !stristr($_REQUEST['surgeryTimeIn'],'P') && !stristr($_REQUEST['surgeryTimeIn'],'A')) || strtoupper($surgeryTimeInExplode[1])=='AM' || strtoupper($surgeryTimeInExplode[1])=='PM') {
				$surgeryTimeIn = $objManageData->setTmFormat($_REQUEST['surgeryTimeIn'],'static');
			}
		}*/
		$anesStart=$_REQUEST['anesStartTime'];
		$anesEnd=$_REQUEST['anesEndTime'];
		//code to set anes time in DB
		$anesStarttime=$anesStart;
		$spilt_startTime = explode(" ",$anesStarttime);
		if($spilt_startTime[1]=="PM" || $spilt_startTime[1]=="pm"){
		 $spilt_anesStarttime = explode(":",$spilt_startTime[0]);
		 $spilt_anesStarttimeIncr =  $spilt_anesStarttime[0]+12;
		  $anesStartTime =  $spilt_anesStarttimeIncr.":". $spilt_anesStarttime[1].":.00";
		}elseif($spilt_startTime[1]=="AM" || $spilt_startTime[1]=="am"){
		   $spilt_anesStarttime = explode(":",$spilt_startTime[0]);
		  $anesStartTime = $spilt_anesStarttime[0].":".$spilt_anesStarttime[1].":.00";
		}
			
			$anesEndtime=$anesEnd;
			$spilt_EndTime = explode(" ",$anesEndtime);
			if($spilt_EndTime[1]=="PM" || $spilt_EndTime[1]=="pm"){
			 $spilt_anesEndtime = explode(":",$spilt_EndTime[0]);
			 $spilt_anesEndtimeIncr =  $spilt_anesEndtime[0]+12;
			  $anesEndTime =  $spilt_anesEndtimeIncr.":". $spilt_anesEndtime[1].":.00";
			}elseif($spilt_EndTime[1]=="AM" || $spilt_EndTime[1]=="am"){
			   $spilt_anesEndtime = explode(":",$spilt_EndTime[0]);
			  $anesEndTime = $spilt_anesEndtime[0].":".$spilt_anesEndtime[1].":.00";
			}
			
			//end code to set anes time in DB
			/*
			$surgeryStartTime="";
			if(strtotime($_REQUEST['surgeryStartTime'])) {
				$surgeryStartTimeExplode = explode(" ",$_REQUEST['surgeryStartTime']);
				if((!$surgeryStartTimeExplode[1] && !stristr($_REQUEST['surgeryStartTime'],'P') && !stristr($_REQUEST['surgeryStartTime'],'A')) || strtoupper($surgeryStartTimeExplode[1])=='AM' || strtoupper($surgeryStartTimeExplode[1])=='PM') {
					$surgeryStartTime = date("H:i:s",strtotime($_REQUEST['surgeryStartTime']));
				}
			}
			$surgeryEndTime="";
			if(strtotime($_REQUEST['surgeryEndTime'])) {
				$surgeryEndTimeExplode = explode(" ",$_REQUEST['surgeryEndTime']);
				if((!$surgeryEndTimeExplode[1] && !stristr($_REQUEST['surgeryEndTime'],'P') && !stristr($_REQUEST['surgeryEndTime'],'A')) || strtoupper($surgeryEndTimeExplode[1])=='AM' || strtoupper($surgeryEndTimeExplode[1])=='PM') {
					$surgeryEndTime = date("H:i:s",strtotime($_REQUEST['surgeryEndTime']));
				}
			}
			*/
			$surgeryStartTime	= "";
			$surgeryEndTime		= "";
			$surgeryTimeOut		= "";
			$surgeryStartTime 	= $objManageData->setTmFormat($_REQUEST['surgeryStartTime']);
			$surgeryEndTime 	= $objManageData->setTmFormat($_REQUEST['surgeryEndTime']);
			$surgeryTimeOut 	= $objManageData->setTmFormat($_REQUEST['surgeryTimeOut'],'static');
			/*
			$_REQUEST['surgeryStartTime'];
			$spilt_startTime = explode(" ",$surgeryStarttime);
			if($spilt_startTime[1]=="PM" || $spilt_startTime[1]=="pm"){
			 $spilt_surgerystartTime = explode(":",$spilt_startTime[0]);
			 $spilt_surgerystartTimeIncr = $spilt_surgerystartTime[0]+12;
			 $surgeryStartTime = $spilt_surgerystartTimeIncr.":".$spilt_surgerystartTime[1].":00";
			}elseif($spilt_startTime[1]=="AM" || $spilt_startTime[1]=="am"){
			  $spilt_surgerystartTime = explode(":",$spilt_startTime[0]);
			  $surgeryStartTime = $spilt_surgerystartTime[0].":".$spilt_surgerystartTime[1].":00";
			}

			$surgeryEndtime=$_REQUEST['surgeryEndTime'];
			
			$spilt_endTime = explode(" ",$surgeryEndtime);
			if($spilt_endTime[1]=="PM" || $spilt_endTime[1]=="pm"){
			 $spilt_surgeryendTime = explode(":",$spilt_endTime[0]);
			 $spilt_surgeryendTimeIncr = $spilt_surgeryendTime[0]+12;
			 $surgeryEndTime = $spilt_surgeryendTimeIncr.":".$spilt_surgeryendTime[1].":00";
			}else if($spilt_endTime[1]=="AM" || $spilt_endTime[1]=="am"){
			  $spilt_surgeryendTime = explode(":",$spilt_endTime[0]);
			  $surgeryEndTime = $spilt_surgeryendTime[0].":".$spilt_surgeryendTime[1].":00";
			}
			if(strtotime($_REQUEST['surgeryTimeOut'])) {
				$surgeryTimeOutExplode = explode(" ",$_REQUEST['surgeryTimeOut']);
				if((!$surgeryTimeOutExplode[1] && !stristr($_REQUEST['surgeryTimeOut'],'P') && !stristr($_REQUEST['surgeryTimeOut'],'A')) || strtoupper($surgeryTimeOutExplode[1])=='AM' || strtoupper($surgeryTimeOutExplode[1])=='PM') {
					$surgeryTimeOut = date("h:i A",strtotime($_REQUEST['surgeryTimeOut']));
				}
			}
			*/
			$pillow_under_knees = $_POST["pillow_under_knees"];
			$head_rest = $_POST["head_rest"];
			$safetyBeltApplied = $_POST["safetyBeltApplied"];
			$other_position = addslashes($_POST["other_position"]);
			if($_POST["other_position"]=="Yes") {
				$surgeryPatientPositionOther = addslashes($_POST["surgeryPatientPositionOther"]);
			}else {
				$surgeryPatientPositionOther = "";
			}
			
			$Solumedrol = $_POST["Solumedrol"];
			$Dexamethasone = $_POST["Dexamethasone"];
			$Kenalog = $_POST["Kenalog"];
			$Vancomycin = $_POST["Vancomycin"];
			$Trimaxi = $_POST["Trimaxi"];
			$injXylocaineMPF = $_POST["injXylocaineMPF"];
			$injMiostat = $_POST["injMiostat"];
			$PhenylLido = $_POST["PhenylLido"];
			$Ancef = $_POST["Ancef"];
			$Gentamicin = $_POST["Gentamicin"];
			$Depomedrol = $_POST["Depomedrol"];
			$postOpInjOther = addslashes($_POST["postOpInjOther"]);
			
			$SolumedrolList = $_POST["SolumedrolList"];
			$DexamethasoneList = $_POST["DexamethasoneList"];
			$KenalogList = $_POST["KenalogList"];
			$VancomycinList = $_POST["VancomycinList"];
			$TrimaxiList = $_POST["TrimaxiList"];
			$injXylocaineMPFList = $_POST["injXylocaineMPFList"];
			$injMiostatList = $_POST["injMiostatList"];
			
			$PhenylLidoList = $_POST["PhenylLidoList"];
			$AncefList = $_POST["AncefList"];
			$GentamicinList = $_POST["GentamicinList"];
			$DepomedrolList = $_POST["DepomedrolList"];
			
			$anesthesia_service = $_POST["anesthesia_service"];
			
			$TopicalBlock = $_POST["TopicalBlock"];		
			
			$patch = $_POST["patch"];
			$shield = $_POST["shield"];
			$needleSutureCount = $_POST["needleSutureCount"];
			$needleSutureCountNA = $_POST["needleSutureCountNA"];
			
			$collagenShield = $_POST["chbx_collagen_shield"];
			
			$Econopred = $_POST["Econopred"];
			$Zymar = $_POST["Zymar"];
			$Tobradax = $_POST["Tobradax"];
			$soakedInOtherChk = $_POST["soakedInOtherChk"];
			$soakedInOther = addslashes($_POST["soakedInOther"]);
			if($collagenShield<>"Yes") {
				//$soakedIn = "";
				$Econopred = "";
				$Zymar = "";
				$Tobradax = "";
				$soakedInOtherChk = "";
				$soakedInOther = "";
			}
			
			if($collagenShield=="Yes" && $soakedInOtherChk<>"Yes") {
				$soakedInOther = "";
			}
			$postOpDiagnosis = addslashes($_POST["postOpDiagnosis"]);
			$other_remain = addslashes($_POST["other_remain"]);
			$postOpDrops = addslashes($_POST["postOpDrops"]);
			$nurseNotes = addslashes($_POST["nurseNotes"]);

			$intraOpPostOpOrder = addslashes($_POST["intraOpPostOpOrder"]);
			
			$surgeonId1 = $_POST["GroupSurgeonList"];
			$anesthesiologistId = $OpRoom_patientConfirmAnesthesiologistId;
			$scrubTechId1 = $_POST["scrub_techList"];
			$scrubTechOther1 = addslashes($_POST["scrubTechOther1"]);
			if($scrubTechId1<>"other") {
				$scrubTechOther1 = "";
			}
			$scrubTechId2= $_POST["scrub_techList1"];
			$scrubTechOther2 = addslashes($_POST["scrubTechOther2"]);
			if($scrubTechId2<>"other") {
				$scrubTechOther2 = "";
			}
			$circulatingNurseId = $_POST["circulating_nurseList"];
			$NurseTitle				=	$_POST["nurseTitle"];	
			$NurseId = $_POST["nurseList"];
			$signOnFileSurgeon1 = $_POST["chbx_op_surg1"];
			$iol_serial_number=trim(addslashes($_POST['iol_serial_number']));
			
			if(!empty($_FILES["elem_iolscan"]["name"])){
				$iolName = $_FILES["elem_iolscan"]["name"];
				$iolTmp = $_FILES["elem_iolscan"]["tmp_name"];		
				$iolSize = $_FILES["elem_iolscan"]["size"];
				$iolTempFile = fopen($_FILES["elem_iolscan"]["tmp_name"], "r");		
				$iolImg = addslashes(fread($iolTempFile, $iolSize));
				$iol_type = $_FILES["elem_iolscan"]["type"];
				unset($arrayRecord);
				$arrayRecord['image_type'] = $iol_type;
				$arrayRecord['img_content'] = $iolImg;
				$arrayRecord['document_name'] = $iolName;
				$arrayRecord['document_size'] = $iolSize;
				$arrayRecord['confirmation_id'] = $_REQUEST["pConfId"];
				$objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
			}
		
		//START CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
			$chkUserSignDetails = $objManageData->getRowRecord('operatingroomrecords', 'confirmation_id', $_REQUEST["pConfId"]);
			if($chkUserSignDetails) {
				$chk_signNurseId = $chkUserSignDetails->signNurseId;
				$chk_signNurse1Id = $chkUserSignDetails->signNurse1Id;
				$chk_signAnesthesia2Id = $chkUserSignDetails->signAnesthesia2Id;
				
				$chk_form_status = $chkUserSignDetails->form_status;
				$chk_vitalSignGridStatus	=	$chkUserSignDetails->vitalSignGridStatus;
			
				$chk_surgeryStartTime	=	$chkUserSignDetails->surgeryStartTime;
				$chk_surgeryEndTime		=	$chkUserSignDetails->surgeryEndTime;
				
				//CHECK IOL SCAN
				$chk_iol_ScanUpload = $chkUserSignDetails->iol_ScanUpload;
				$chk_iol_ScanUpload2 = $chkUserSignDetails->iol_ScanUpload2;
				//CHECK IOL SCAN
				
				$chk_opRoomVersionNum 	= $chkUserSignDetails->version_num;
				$chk_opRoomVersionDate	= $chkUserSignDetails->version_date_time;
				
			}
		//END CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
		
		//START SET VERSION NUMBER
		$versionNumQry = "";
		$version_num	=	$chk_opRoomVersionNum;
		if(!$chk_opRoomVersionNum)
		{
			$version_date_time	=	$chk_opRoomVersionDate;
			if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
			{
				$version_date_time	=	date('Y-m-d H:i:s');
			}
					
			if($chk_form_status == 'completed' || $chk_form_status=='not completed'){
				$version_num = 1;
			}else{
				$version_num	=	$current_form_version;
			}
			
			$versionNumQry .= ", version_num =	'".$version_num."', version_date_time	=	'".$version_date_time."' ";
		}
		//END SET VERSION NUMBER

		if($version_num > 2) {
			$others_present = trim($_REQUEST['others_present']);
			$versionNumQry .= ", others_present = '".addslashes($others_present)."' ";	
		}
		if($version_num > 4) {
			$complications = trim($_REQUEST['complications']);
			$versionNumQry .= ", complications = '".addslashes($complications)."' ";	
		}
		
		$vitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($chk_form_status,$chk_vitalSignGridStatus,'oproom');
		$vitalSignGridQuery		=	'';
		if($chk_form_status <> 'completed' && $chk_form_status <> 'not completed')
		{
			$vitalSignGridQuery		=	", vitalSignGridStatus = '".$vitalSignGridStatus."'  ";	
		}
		
		// Check if surgery start time or surgery end time field values changed
		$start_time_staus	=	'0';
		if(				($surgeryStartTime 	&& $surgeryStartTime<> $objManageData->getTmFormat($chk_surgeryStartTime))
					 || ($surgeryEndTime 	&& $surgeryEndTime 	<> $objManageData->getTmFormat($chk_surgeryEndTime))
		)
		{
				$start_time_staus	=	'1';			 
		}
		
		
		// Save Suplies Information
		$suppRecordIdArr	=	$_REQUEST['hidd_suppRecordId'];
		$suppNameArr			=	$_REQUEST['hidd_suppName'];
		$suppQtyDisplayArr=	$_REQUEST['hidd_suppQtyDisplay'];
		$suppChkBoxArr		=	$_REQUEST['suppChkBox'] ;
		$suppListBoxArr		=	$_REQUEST['suppListBox'] ;
		$predefine_supp_id_arr  =	$_REQUEST['hidd_predefine_supp_id'] ;
		/* 
		* $totalSupplies - Holds the requested total supplies
		*/
		$totalSupplies		=	count($suppRecordIdArr);
		
		/*
		* $suppChkTickCntr - Holds count of used supplies
		*/
		$suppChkTickCntr	=	0;
		
		/*
		* $suppCompStatus - Holds the Supply checks completions status. 
		* Default Value is false, When  $totalSupplies ===  $suppChkTickCntr, then it turns true.
		*/
		$suppCompStatus	=	false	;
		
		if(is_array($suppRecordIdArr) && count($suppRecordIdArr) > 0)
		{
			foreach($suppRecordIdArr as $key=>$recordId)
			{
				$suppName			=	addslashes($suppNameArr[$key]);
				$suppQtyDisplay	=	$suppQtyDisplayArr[$key];
				$suppChkBox		=	isset($suppChkBoxArr[$key])	?	$suppChkBoxArr[$key]	:	0	;
				$suppListBox		=	isset($suppListBoxArr[$key])	?	$suppListBoxArr[$key]		:	''	;
				$predefine_supp_id	=	$predefine_supp_id_arr[$key];
				/*
				// Checking if supply name does not exist in predefine table 
				$whereArray			=	array('name =' => $suppName);
				$chkPredefSuppCount	=	$objManageData->getRowCount('predefine_suppliesused',$whereArray);
				$insertUpdateArray=	array();
				$insertUpdateArray['name'] 		= 	$suppName;
				$insertUpdateArray['qtyChkBox']	= 	$suppQtyDisplay;
				$insertUpdateArray['deleted']		= 	0;
				
				if($chkPredefSuppCount ==  0 )
				{	
					$whereArray			=	array('name =' => 'Other');
					$chkPredefSuppCatCount	=	$objManageData->getRowCount('supply_categories',$whereArray);
					if( !$chkPredefSuppCatCount ) 
					{
						$$insertSupplyCat = array();
						$insertSupplyCat['name'] = 'Other';
						$insertSupplyCat['date_created'] = date('Y-m-d H:i:s');
						$objManageData->addRecords($insertSupplyCat,'supply_categories');
					}
					$getSuppCatData = $objManageData->getRowRecord('supply_categories','name','Other','','','id');
					$supp_cat_id = $getSuppCatData->id;
					$insertUpdateArray['cat_id'] 	= 	$supp_cat_id;
					$predefine_supp_id = $objManageData->addRecords($insertUpdateArray,'predefine_suppliesused');
				}
				// End checking if supply name does not exist in predefine table
				*/
				
				if($suppChkBox) $suppChkTickCntr++;
				
				// If Qty Drop Down is set to appear and Checkbox is selected
				// and no any qty selected from dropdown then set to default X1
				if($suppQtyDisplay && $suppChkBox && empty($suppListBox))
				{
					$suppListBox = 'X1';
				}
				
				if(substr_count($recordId,'suppUniqueId') > 0 )
				{
					$insertArray['suppName']				=	$suppName;
					$insertArray['suppQtyDisplay']	=	$suppQtyDisplay;
					$insertArray['suppChkStatus']		=	$suppChkBox;
					$insertArray['suppList']				=	$suppListBox;
					$insertArray['templateId']			=	0;
					$insertArray['confirmation_id']	=	$_REQUEST["pConfId"];
					$insertArray['displayStatus']		=	1;
					$insertArray['predefine_supp_id']	=	$predefine_supp_id;
					
					//$chkIfExistWhere	=	array('suppName ='=>$suppName );
					//$chkIfExist	=	$objManageData->getRowCount('operatingroomrecords_supplies',$chkIfExistWhere);
					$chkIfExist	=	$objManageData->getExtractRecord('operatingroomrecords_supplies','suppName',"".$suppName."' And confirmation_id = '".$_REQUEST["pConfId"]." ");
					if($chkIfExist && $chkIfExist['displayStatus'] == 0)
					{
						$insertArray['suppQtyDisplay']	=	$chkIfExist['suppQtyDisplay'];
						$objManageData->UpdateRecord($insertArray,'operatingroomrecords_supplies','suppRecordId',$chkIfExist['suppRecordId']);
					}
					elseif (!$chkIfExist)
					{
						$objManageData->addRecords($insertArray,'operatingroomrecords_supplies');	
					}
				}
				else
				{
					$updateArray['suppChkStatus']	=	$suppChkBox;
					$updateArray['suppList']		=	$suppListBox;
					$objManageData->UpdateRecord($updateArray,'operatingroomrecords_supplies','suppRecordId',$recordId);
				}
			}
			
		}
		
		$suppCompStatus	=	($suppChkTickCntr > 0) ? true : false;
		// End Save Supplies Information;
			
			
			
		//SET FORM STATUS ACCORDING TO MANDATORY FIELD
			
				if(($verifiedbyNurse!="" && $verifiedbySurgeon!="")
				&& ($verifiedbyAnesthesiologist!="" || $OpRoom_patientConfirmAnes_NA=="Yes") 
				&& (trim($preOpDiagnosis)!="" || trim($postOpDiagnosis)!="") 
				&& ( trim($operativeProcedures)!="" || (trim($postOpDrops)!="" && defined("DISABLE_OPROOM_POSTOP_MED") && constant("DISABLE_OPROOM_POSTOP_MED")!="YES"))
				
				&& ($product_control_na!="" || $bssValue!="" || $Epinephrine03!="" || $Vancomycin01!="" || $Vancomycin02!="" || $omidria != '' || $InfusionOtherChk!=""
					|| $suppCompStatus || $OtherSuppliesUsed != "" )
				
				&& ($manufacture != "" || $model != "" || $Diopter != "" || $chk_iol_ScanUpload || $chk_iol_ScanUpload2 || $iol_na=="Yes")
			   
				&& ($prep_solution_na != "" || $Betadine != "" || $Saline != "" || $Alcohol != "" 
		            || $Prcnt5Betadinegtts != "" || trim($prepSolutionsOther) != "" || $proparacaine != '' || $tetracaine != '' || $tetravisc != '')
				
				&& ($surgeryORNumber != "" || $surgeryTimeIn != "" || $surgeryStartTime != "" || $surgeryEndTime != "" || $surgeryTimeOut != "" )
				
				&&($pillow_under_knees != "" || $head_rest != "" || $safetyBeltApplied != "" || $other_position!="" || $surgeryPatientPositionOther != "")
				
				&& ($anesthesia_service != "" || $TopicalBlock != "" ) //|| $collagenShield != ""
				
				&& ($scrubTechId1 != "" || $scrubTechId2 != "")
				
				&& ($chk_signNurseId<>"0" || $iol_na=="Yes")
				
				&& ($chk_signNurse1Id<>"0")
				)
				 {      
				  $form_status = "completed";
				   
				 }
				 else
				 {
				   $form_status = "not completed";
				   
				 }
				
			//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
				
			
		
	$ViewUserTypeQry = "select * from `users` where  usersId = '".$_SESSION['loginUserId']."'";
	$ViewUserTypeRes = imw_query($ViewUserTypeQry) or die(imw_error()); 
	$ViewUserTypeRow = imw_fetch_array($ViewUserTypeRes); 
	$vrfyBySrgn="";
	if($ViewUserTypeRow["user_type"]=="Surgeon") {
		if($sxPlanReviewedBySurgeon=="") {
			$hidd_sxPlanRvwBySrgnDtm = "";	
		}
		$vrfyBySrgn = "verifiedbySurgeon = '".$verifiedbySurgeon."', sxPlanReviewedBySurgeon = '".$sxPlanReviewedBySurgeon."', sxPlanReviewedBySurgeonDateTime = '".$hidd_sxPlanRvwBySrgnDtm."', ";
	}
	$vrfyByAns="";
	$vrfyByAnsName="";
	if($ViewUserTypeRow["user_type"]=="Anesthesiologist") {
		$vrfyByAns = "verifiedbyAnesthesiologist='".$verifiedbyAnesthesiologist."',";
		$vrfyByAnsName = "verifiedbyAnesthesiologistName='".$verifiedbyAnesthesiologistName."', ";
	}
	$vrfyByNurse="";
	$vrfyByNurseName="";
	if($ViewUserTypeRow["user_type"]=="Nurse") {
		$vrfyByNurse = "verifiedbyNurse='".$verifiedbyNurse."',";
		$vrfyByNurseName = "verifiedbyNurseName='".$verifiedbyNurseName."',";
	}
	
	$chkOpRoomRecordQry = "select form_status, iol_serial_number from `operatingroomrecords` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkOpRoomRecordRes = imw_query($chkOpRoomRecordQry) or die(imw_error()); 
	$chkOpRoomRecordNumRow = imw_num_rows($chkOpRoomRecordRes);
	if($chkOpRoomRecordNumRow>0) {
	  	//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkOpRoomRecordRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
			$previous_iol_serial_number = trim($chkFormStatusRow['iol_serial_number']);
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		
		//START CODE FOR ITEM DETAILS IN SUPPLIES
		if($iol_serial_number) {
			$itemDetailQry = "UPDATE predefine_suppliesused_item_detail SET used_status = '1' WHERE TRIM(serial_number) ='".$iol_serial_number."' AND serial_number!='' AND used_status = '0'";
			$itemDetailRes = imw_query($itemDetailQry) or die(imw_error()); 
		}
		if($previous_iol_serial_number && $previous_iol_serial_number != $iol_serial_number) {
			//RESET PREVIOUS SERIAL NUMBER IN Admin (IF PREVIOUS SERIAL NUMBER IS DIFFERENT FROM CURRENT SERIAL NUMBER)
			$prevItemDetailQry = "UPDATE predefine_suppliesused_item_detail SET used_status = '0' WHERE TRIM(serial_number) ='".$previous_iol_serial_number."' AND serial_number!='' AND used_status = '1'";
			$prevItemDetailRes = imw_query($prevItemDetailQry) or die(imw_error()); 
		}
		//END CODE FOR ITEM DETAILS IN SUPPLIES
		
		$SaveOpRoomRecordQry = "update `operatingroomrecords` set 									
									procedureSecondaryVerified = '$procedureSecondaryVerified',
									$vrfyByNurse
									$vrfyByNurseName
									$vrfyBySrgn
									$vrfyByAns
									$vrfyByAnsName
									sxPlanReviewedBySurgeonChk = '$hidd_sxPlanReviewedBySurgeonChk',
									verifiedbyNurseTime = '$verifiedbyNurseTime',
									preOpDiagnosis = '$preOpDiagnosis', 
									operativeProcedures = '$operativeProcedures', 
									product_control_na = '$product_control_na',
									bssValue = '$bssValue',
									iol_na = '$iol_na',
									Epinephrine03 = '$Epinephrine03',
									Vancomycin01 = '$Vancomycin01',
									Vancomycin02 = '$Vancomycin02',
									omidria	=	'$omidria',
									InfusionOtherChk = '$InfusionOtherChk',
									infusionBottleOther = '$infusionBottleOther',
									OtherSuppliesUsed = '$OtherSuppliesUsed', 
									percent_txt = '$percent_txt',
									percent = '$percent',
									manufacture = '$manufacture',
									lensBrand 	= '$lensBrand',
									iol_comments 	= '$iol_comments',
									model = '$model', 
									Diopter = '$Diopter',
									iolConfirmedSurgeonSignOnFile = '$iolConfirmedSurgeonSignOnFile', 
									prep_solution_na = '$prep_solution_na',
									Betadine = '$Betadine', 
									Saline = '$Saline',
									Alcohol = '$Alcohol',
									Prcnt5Betadinegtts = '$Prcnt5Betadinegtts', 
									proparacaine = '$proparacaine',
									tetracaine = '$tetracaine',
									tetravisc = '$tetravisc',
									prepSolutionsOther = '$prepSolutionsOther', 
									surgeryORNumber = '$surgeryORNumber', 
									surgeryTimeIn = '$surgeryTimeIn', 
									surgeryStartTime = '$surgeryStartTime', 
									surgeryEndTime = '$surgeryEndTime',
									surgeryTimeOut = '$surgeryTimeOut',
									anesStartTime='$anesStartTime',
									anesEndTime ='$anesEndTime',
									pillow_under_knees = '$pillow_under_knees',
									head_rest = '$head_rest',
									safetyBeltApplied = '$safetyBeltApplied',
									other_position = '$other_position',
									surgeryPatientPositionOther = '$surgeryPatientPositionOther', 
									Solumedrol = '$Solumedrol', 
									Dexamethasone = '$Dexamethasone', 
									Kenalog = '$Kenalog',
									Vancomycin = '$Vancomycin', 
									Trimaxi = '$Trimaxi', 
									injXylocaineMPF = '$injXylocaineMPF',
									injMiostat = '$injMiostat',
									PhenylLido = '$PhenylLido', 
									Ancef = '$Ancef',
									Gentamicin = '$Gentamicin',
									Depomedrol = '$Depomedrol', 
									postOpInjOther = '$postOpInjOther', 
									SolumedrolList = '$SolumedrolList',
									DexamethasoneList = '$DexamethasoneList',
									KenalogList = '$KenalogList',
									VancomycinList = '$VancomycinList',
									TrimaxiList = '$TrimaxiList',
									injXylocaineMPFList = '$injXylocaineMPFList',
									injMiostatList = '$injMiostatList',
									PhenylLidoList = '$PhenylLidoList',
									AncefList = '$AncefList',
									GentamicinList = '$GentamicinList',
									DepomedrolList = '$DepomedrolList',
									anesthesia_service = '$anesthesia_service', 
									TopicalBlock = '$TopicalBlock', 
									patch = '$patch', 
									shield = '$shield',
									needleSutureCount = '$needleSutureCount',
									needleSutureCountNA = '$needleSutureCountNA',
									collagenShield = '$collagenShield', 
									Econopred = '$Econopred',
									Zymar = '$Zymar',
									Tobradax = '$Tobradax',
									soakedInOtherChk = '$soakedInOtherChk',
									soakedInOther = '$soakedInOther',
									postOpDiagnosis = '$postOpDiagnosis',
									other_remain = '$other_remain', 
									postOpDrops = '$postOpDrops',
									nurseNotes = '$nurseNotes',
									intraOpPostOpOrder = '$intraOpPostOpOrder',
									surgeonId1 = '$surgeonId1', 
									anesthesiologistId = '$anesthesiologistId', 
									scrubTechId1 = '$scrubTechId1',
									scrubTechOther1 = '$scrubTechOther1',
									scrubTechId2 = '$scrubTechId2',
									scrubTechOther2 = '$scrubTechOther2',
									circulatingNurseId = '$circulatingNurseId',
									nurseTitle = '$NurseTitle',
									nurseId = '$NurseId',
									iol_serial_number='$iol_serial_number', ";
									
									if($iolImg){
										$SaveOpRoomRecordQry .= "iol_ScanUpload = '$iolImg',
																iol_type = '$iol_type',";
									}
			$SaveOpRoomRecordQry .= "form_status ='".$form_status."',
									save_manual = '1',									
									confirmation_id = '".$_REQUEST["pConfId"]."', 
									patient_id = '".$_REQUEST["patient_id"]."',
									start_time_status = '".$start_time_staus."'
									".$vitalSignGridQuery."
									".$versionNumQry."
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
	
		
	}
	else {
		$SaveOpRoomRecordQry = "insert into `operatingroomrecords` set 									
								    procedureSecondaryVerified = '$procedureSecondaryVerified',
									$vrfyByNurse
									$vrfyByNurseName
									$vrfyBySrgn
									$vrfyByAns
									$vrfyByAnsName
									sxPlanReviewedBySurgeonChk = '$hidd_sxPlanReviewedBySurgeonChk',
									verifiedbyNurseTime = '$verifiedbyNurseTime',
									preOpDiagnosis = '$preOpDiagnosis', 
									operativeProcedures = '$operativeProcedures', 
									product_control_na = '$product_control_na',
									bssValue = '$bssValue',
									iol_na = '$iol_na',
									Epinephrine03 = '$Epinephrine03',
									Vancomycin01 = '$Vancomycin01',
									Vancomycin02 = '$Vancomycin02',
									omidria = '$omidria',
									InfusionOtherChk = '$InfusionOtherChk',
									infusionBottleOther = '$infusionBottleOther',
									OtherSuppliesUsed = '$OtherSuppliesUsed', 
									percent_txt = '$percent_txt',
									percent = '$percent',
									manufacture	= '$manufacture', 
									lensBrand	= '$lensBrand',
									iol_comments 	= '$iol_comments', 
									model = '$model', 
									Diopter = '$Diopter',
									iolConfirmedSurgeonSignOnFile = '$iolConfirmedSurgeonSignOnFile', 
									prep_solution_na = '$prep_solution_na',
									Betadine = '$Betadine', 
									Saline = '$Saline',
									Alcohol = '$Alcohol',
									Prcnt5Betadinegtts = '$Prcnt5Betadinegtts', 
									proparacaine = '$proparacaine',
									tetracaine = '$tetracaine',
									tetravisc = '$tetravisc',
									prepSolutionsOther = '$prepSolutionsOther', 
									surgeryORNumber = '$surgeryORNumber', 
									surgeryTimeIn = '$surgeryTimeIn', 
									surgeryStartTime = '$surgeryStartTime', 
									surgeryEndTime = '$surgeryEndTime',
									surgeryTimeOut = '$surgeryTimeOut',
									anesStartTime='$anesStartTime',
									anesEndTime ='$anesEndTime',
									pillow_under_knees = '$pillow_under_knees',
									head_rest = '$head_rest',
									safetyBeltApplied = '$safetyBeltApplied',
									other_position = '$other_position',
									surgeryPatientPositionOther = '$surgeryPatientPositionOther', 
									Solumedrol = '$Solumedrol', 
									Dexamethasone = '$Dexamethasone', 
									Kenalog = '$Kenalog',
									Vancomycin = '$Vancomycin', 
									Ancef = '$Ancef',
									Gentamicin = '$Gentamicin',
									Depomedrol = '$Depomedrol', 
									postOpInjOther = '$postOpInjOther', 
									SolumedrolList = '$SolumedrolList',
									DexamethasoneList = '$DexamethasoneList',
									KenalogList = '$KenalogList',
									VancomycinList = '$VancomycinList',
									AncefList = '$AncefList',
									GentamicinList = '$GentamicinList',
									DepomedrolList = '$DepomedrolList',
									anesthesia_service = '$anesthesia_service', 
									TopicalBlock = '$TopicalBlock', 
									patch = '$patch', 
									shield = '$shield',
									needleSutureCount = '$needleSutureCount',
									needleSutureCountNA = '$needleSutureCountNA',
									collagenShield = '$collagenShield', 
									Econopred = '$Econopred',
									Zymar = '$Zymar',
									Tobradax = '$Tobradax',
									soakedInOtherChk = '$soakedInOtherChk',
									soakedInOther = '$soakedInOther',
									postOpDiagnosis = '$postOpDiagnosis',
									other_remain = '$other_remain', 
									postOpDrops = '$postOpDrops',
									nurseNotes = '$nurseNotes',
									intraOpPostOpOrder='$intraOpPostOpOrder',
									surgeonId1 = '$surgeonId1', 
									anesthesiologistId = '$anesthesiologistId', 
									scrubTechId1 = '$scrubTechId1',
									scrubTechOther1 = '$scrubTechOther1',
									scrubTechId2 = '$scrubTechId2',
									scrubTechOther2 = '$scrubTechOther2', 
									circulatingNurseId = '$circulatingNurseId', 
									nurseTitle = '$NurseTitle',
									nurseId = '$NurseId', 
									iol_serial_number='$iol_serial_number',";
									if($iolImg){									
										$SaveOpRoomRecordQry .= "iol_ScanUpload = '$iolImg', 
																iol_type = '$iol_type', ";
									}
			$SaveOpRoomRecordQry .= "form_status ='".$form_status."',									
									save_manual = '1',
									confirmation_id = '".$_REQUEST["pConfId"]."', 
									patient_id = '".$_REQUEST["patient_id"]."',
									start_time_status = '".$start_time_staus."'
									".$vitalSignGridQuery."
									".$versionNumQry."
									";
	}
	$SaveOpRoomRecordRes = imw_query($SaveOpRoomRecordQry) or die(imw_error());
	
	
	
	//SAVE ENTRY IN chartnotes_change_audit_tbl 
		
		$chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
									user_id='".$_SESSION['loginUserId']."' AND
									patient_id='".$_REQUEST["patient_id"]."' AND
									confirmation_id='".$_REQUEST["pConfId"]."' AND
									form_name='".$fieldName."' AND
									status = 'created'";
									
		$chkAuditChartNotesRes = imw_query($chkAuditChartNotesQry) or die(imw_error());	
		$chkAuditChartNotesNumRow = imw_num_rows($chkAuditChartNotesRes);	
		if($chkAuditChartNotesNumRow>0) {
			$SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',
										patient_id='".$_REQUEST["patient_id"]."',
										confirmation_id='".$_REQUEST["pConfId"]."',
										form_name='$fieldName',
										status='modified',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}else {
			$SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',
										patient_id='".$_REQUEST["patient_id"]."',
										confirmation_id='".$_REQUEST["pConfId"]."',
										form_name='$fieldName',
										status='created',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}					
		$SaveAuditChartNotesRes = imw_query($SaveAuditChartNotesQry) or die(imw_error());
	//END SAVE ENTRY IN chartnotes_change_audit_tbl
	
	
	//CODE TO CHECK SIGNATURE OF SURGEON,ANESTHESIOLOGIST,NURSE IN ALL CHARTS AND SET VALUE(red,green,blank) IN STUB TABLE
	$recentChartSaved = "";
	if(trim($surgeryTimeIn)) { $recentChartSavedQry = ", recentChartSaved = 'operatingroomrecords' ";}

	$chartSignedBySurgeon 	= chkSurgeonSignNew($_REQUEST["pConfId"]);
	$chartSignedByAnes 		= chkAnesSignNew($_REQUEST["pConfId"]);
	$chartSignedByNurse 	= chkNurseSignNew($_REQUEST["pConfId"]);
	$updateStubTblQry 		= "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."', chartSignedByAnes='".$chartSignedByAnes."', chartSignedByNurse='".$chartSignedByNurse."' ".$recentChartSavedQry." WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."' AND patient_confirmation_id!='0'";
	$updateStubTblRes 		= imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SIGNATURE OF SURGEON,ANESTHESIOLOGIST,NURSE IN ALL CHARTS AND SET VALUE(red,green,blank) IN STUB TABLE
	
	if($vitalSignGridStatus)
	{
			// Code start here to save vital sign grid data 
			$vitalSignGridRecordIdArr	=	$_POST['vitalSignGridRecordId'] ;
			
			if(is_array($vitalSignGridRecordIdArr) && count($vitalSignGridRecordIdArr) > 0 )
			{
				foreach($vitalSignGridRecordIdArr as $key => $gridRowId)	
				{
						$row		=	$key +1;
						$vTime		=	$_POST['vitalSignGrid_'.$row.'_1'];
						$vSystolic	=	$_POST['vitalSignGrid_'.$row.'_2'];//$vBp
						$vDiastolic	=	$_POST['vitalSignGrid_'.$row.'_3'];
						$vPulse		=	$_POST['vitalSignGrid_'.$row.'_4'];
						$vRR			=	$_POST['vitalSignGrid_'.$row.'_5'];
						$vTemp		=	$_POST['vitalSignGrid_'.$row.'_6'];
						$vEtco2		=	$_POST['vitalSignGrid_'.$row.'_7'];
						$vosat2		=	$_POST['vitalSignGrid_'.$row.'_8'];
						
						$vTime="";
						$vTime = $objManageData->setTmFormat($_POST['vitalSignGrid_'.$row.'_1']);
						/*
						if(strtotime($_POST['vitalSignGrid_'.$row.'_1'])) {
							$vTimeExplode = explode(" ",$_POST['vitalSignGrid_'.$row.'_1']);
							if((!$vTimeExplode[1] && !stristr($_POST['vitalSignGrid_'.$row.'_1'],'P') && !stristr($_POST['vitalSignGrid_'.$row.'_1'],'A')) || strtoupper($vTimeExplode[1])=='AM' || strtoupper($vTimeExplode[1])=='PM') {
								$vTime = $objManageData->setTmFormat($_POST['vitalSignGrid_'.$row.'_1']);
							}
						}*/
						
						if( $vTime && ($vSystolic || $vDiastolic || $vPulse || $vRR || $vTemp || $vEtco2 || $vosat2)  )
						{
							/*
							$timeSplit 		= explode(":",$vTime);
							$timeAmPm 	= strtolower($timeSplit[1][3]);
							if( $timeAmPm == "p" && $timeSplit[0] <> '12' ) {
								$timeSplit[0] = $timeSplit[0]+12;
							}
							elseif( $timeAmPm == "a" && $timeSplit[0] == '12' )
							{
								$timeSplit[0] = $timeSplit[0]-12;	
							}
							$vTime = $timeSplit[0].":".$timeSplit[1][0].$timeSplit[1][1].":00";
							*/
							//$vTime	=	date('H:i:s',strtotime($vTime));
							//echo $vTime.'<br>';
							$dataArray	=	array();
							$dataArray['chartName']			=	'intra_op_record_form';
							$dataArray['confirmation_id']	=	$pConfId ;
							$dataArray['start_time']			=	$vTime;
							$dataArray['systolic']				=	$vSystolic;
							$dataArray['diastolic']				=	$vDiastolic;
							$dataArray['pulse']					=	$vPulse;
							$dataArray['rr']						=	$vRR;
							$dataArray['temp']					=	$vTemp;
							$dataArray['etco2']					=	$vEtco2;
							$dataArray['osat2']					=	$vosat2;
							
							if($gridRowId)
							{
								$objManageData->UpdateRecord($dataArray,'vital_sign_grid','gridRowId',$gridRowId);
							}
							else
							{
								$chkRecords	=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$dataArray);
								if( !$chkRecords)
									$objManageData->addRecords($dataArray,'vital_sign_grid');	
							}
						}
						else
						{
							if($gridRowId)
							{
								$objManageData->DeleteRecord('vital_sign_grid','gridRowId',$gridRowId);	
							}
						}
				
						
				}
			}
			
			// Code end here to save vital sign grid data 
	}
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
	}
//END SAVE RECORD TO DATABASE


//VIEW RECORD FROM DATABASE

	//if($_POST['SaveRecordForm']==''){
		$ViewOpRoomRecordQry = "select *, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat, date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p') as signNurse1DateTimeFormat from `operatingroomrecords` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
		$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
		$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
		$formStatus 	=	$ViewOpRoomRecordRow["form_status"];
		$save_manual 	=	$ViewOpRoomRecordRow["save_manual"];
		$vitalSignGridStatus	=	$ViewOpRoomRecordRow['vitalSignGridStatus'];
		$vitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($formStatus,$vitalSignGridStatus,'oproom');
		
		$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
		$patientIdentityVerified = $ViewOpRoomRecordRow["patientIdentityVerified"];
		$siteVerified = $ViewOpRoomRecordRow["siteVerified"];
		$procedurePrimaryVerified = $ViewOpRoomRecordRow["procedurePrimaryVerified"];
		$anesthesiologist = $ViewOpRoomRecordRow["anesthesiologist"];
		$verifiedbyNurseName = trim(stripslashes($ViewOpRoomRecordRow["verifiedbyNurseName"]));
		$verifiedbyNurse = $ViewOpRoomRecordRow["verifiedbyNurse"];
		$verifiedbySurgeon = $ViewOpRoomRecordRow["verifiedbySurgeon"];
		$sxPlanReviewedBySurgeon = $ViewOpRoomRecordRow["sxPlanReviewedBySurgeon"];
		$sxPlanReviewedBySurgeonChk = $ViewOpRoomRecordRow["sxPlanReviewedBySurgeonChk"];
		$sxPrbsChk = "0";
		if(($sxPlanSheetReviewAdmin=="1" && $formStatus == "") || $sxPlanReviewedBySurgeonChk=="1") {
			$sxPrbsChk = "1";
		}
		$sxPlanReviewedBySurgeonDateTime = $ViewOpRoomRecordRow["sxPlanReviewedBySurgeonDateTime"];
		$sxPlanReviewedBySurgeonDateTimeFormat = $objManageData->getFullDtTmFormat($ViewOpRoomRecordRow["sxPlanReviewedBySurgeonDateTime"]);
		$verifiedbyAnesthesiologist = $ViewOpRoomRecordRow["verifiedbyAnesthesiologist"];
		$verifiedbyAnesthesiologistName = $ViewOpRoomRecordRow["verifiedbyAnesthesiologistName"];
		//$verifiedbyNurseTime = $ViewOpRoomRecordRow["verifiedbyNurseTime"];
		$verifiedbyNurseTime = $objManageData->getTmFormat($ViewOpRoomRecordRow["verifiedbyNurseTime"]);
		$preOpDiagnosis = $ViewOpRoomRecordRow["preOpDiagnosis"];
		$operativeProcedures = $ViewOpRoomRecordRow["operativeProcedures"];
		$product_control_na = $ViewOpRoomRecordRow["product_control_na"];
		$bssValue = $ViewOpRoomRecordRow["bssValue"];
	    $iol_na = $ViewOpRoomRecordRow["iol_na"];
		$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
		$Epinephrine03 = $ViewOpRoomRecordRow["Epinephrine03"];
		$Vancomycin01 = $ViewOpRoomRecordRow["Vancomycin01"];
		$Vancomycin02 = $ViewOpRoomRecordRow["Vancomycin02"];
		$omidria	=	$ViewOpRoomRecordRow["omidria"];
		$InfusionOtherChk = $ViewOpRoomRecordRow["InfusionOtherChk"];
		$infusionBottleOther = $ViewOpRoomRecordRow["infusionBottleOther"];
		$Healon = $ViewOpRoomRecordRow["Healon"];
		$Occucoat = $ViewOpRoomRecordRow["Occucoat"];
		$Provisc = $ViewOpRoomRecordRow["Provisc"];
		$Miostat = $ViewOpRoomRecordRow["Miostat"];
		$HealonGV = $ViewOpRoomRecordRow["HealonGV"];
		$Discovisc = $ViewOpRoomRecordRow["Discovisc"];
		$AmviscPlus = $ViewOpRoomRecordRow["AmviscPlus"];
		$TrypanBlue = $ViewOpRoomRecordRow["TrypanBlue"];
		$Healon5 = $ViewOpRoomRecordRow["Healon5"];
		$Viscoat = $ViewOpRoomRecordRow["Viscoat"];
		$Miochol = $ViewOpRoomRecordRow["Miochol"];
		$Trimaxi = $ViewOpRoomRecordRow["Trimaxi"];
		$injXylocaineMPF = $ViewOpRoomRecordRow["injXylocaineMPF"];
		$injMiostat = $ViewOpRoomRecordRow["injMiostat"];
		$PhenylLido = $ViewOpRoomRecordRow["PhenylLido"];
		$OtherSuppliesUsed = $ViewOpRoomRecordRow["OtherSuppliesUsed"];
		
		$HealonList = $ViewOpRoomRecordRow["HealonList"];
		$OccucoatList = $ViewOpRoomRecordRow["OccucoatList"];
		$ProviscList = $ViewOpRoomRecordRow["ProviscList"];
		$MiostatList = $ViewOpRoomRecordRow["MiostatList"];
		$HealonGVList = $ViewOpRoomRecordRow["HealonGVList"];
		$DiscoviscList = $ViewOpRoomRecordRow["DiscoviscList"];
		$AmviscPlusList = $ViewOpRoomRecordRow["AmviscPlusList"];
		$Healon5List = $ViewOpRoomRecordRow["Healon5List"];
		$ViscoatList = $ViewOpRoomRecordRow["ViscoatList"];
		$MiocholList = $ViewOpRoomRecordRow["MiocholList"];
		$TrimaxiList = $ViewOpRoomRecordRow["TrimaxiList"];
		$injXylocaineMPFList = $ViewOpRoomRecordRow["injXylocaineMPFList"];
		$injMiostatList = $ViewOpRoomRecordRow["injMiostatList"];
		
		$PhenylLidoList = $ViewOpRoomRecordRow["PhenylLidoList"];								
		$percent_txt = $ViewOpRoomRecordRow["percent_txt"];
		$percent = $ViewOpRoomRecordRow["percent"];
		$XylocaineMPF = $ViewOpRoomRecordRow["XylocaineMPF"];
		$manufacture = $ViewOpRoomRecordRow["manufacture"];
		$lensBrand = $ViewOpRoomRecordRow["lensBrand"];
		$iol_comments = $ViewOpRoomRecordRow["iol_comments"];
		//$post2DischargeSummary = $ViewOpRoomRecordRow["post2DischargeSummary"];
		//$post2OperativeReport = $ViewOpRoomRecordRow["post2OperativeReport"];
		$model =$ViewOpRoomRecordRow["model"];
		$Diopter =$ViewOpRoomRecordRow["Diopter"];
		$iolConfirmedSurgeonSignOnFile = $ViewOpRoomRecordRow["iolConfirmedSurgeonSignOnFile"];
	    $Betadine = $ViewOpRoomRecordRow["Betadine"];
		$prep_solution_na = $ViewOpRoomRecordRow["prep_solution_na"];
		$Saline = $ViewOpRoomRecordRow["Saline"];
		$Alcohol = $ViewOpRoomRecordRow["Alcohol"];
		$Prcnt5Betadinegtts = $ViewOpRoomRecordRow["Prcnt5Betadinegtts"];
		$proparacaine = $ViewOpRoomRecordRow["proparacaine"];
		$tetracaine = $ViewOpRoomRecordRow["tetracaine"];
		$tetravisc = $ViewOpRoomRecordRow["tetravisc"];
		$prepSolutionsOther = $ViewOpRoomRecordRow["prepSolutionsOther"];
		
		$surgeryORNumber	= $ViewOpRoomRecordRow["surgeryORNumber"];
		//$surgeryTimeIn		= $ViewOpRoomRecordRow["surgeryTimeIn"];
		$surgeryTimeIn		= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryTimeIn"]);
		
		$surgeryStartTime = $ViewOpRoomRecordRow["surgeryStartTime"];
		if($surgeryStartTime=="00:00:00" || $surgeryStartTime=="") {
			$surgeryStartTime = "";
		}else {
     	//$surgeryStartTime = date('h:i A', strtotime($surgeryStartTime));
		$surgeryStartTime = $objManageData->getTmFormat($surgeryStartTime);
			/*list($StartHours,$StartMinutes) = explode(":",$surgeryStartTime);
			if($StartHours>12){
			    $am_pm="PM";
			}
			else{
			  $am_pm="AM";
			}
			if($StartHours>=13){
			  $StartHours = $StartHours-12;
			   if(strlen($StartHours)==1){
			      $StartHours="0".$StartHours;
			   }
			}else
			{
			 //DO nothing
			}
			$surgeryStartTime = $StartHours.":".$StartMinutes." ".$am_pm;*/
		}
		$surgeryEndTime = $ViewOpRoomRecordRow["surgeryEndTime"];
		if($surgeryEndTime=="00:00:00" ||$surgeryEndTime=="") {
			$surgeryEndTime = "";
		}else {
    		//$surgeryEndTime = date('h:i A', strtotime($surgeryEndTime));
			$surgeryEndTime = $objManageData->getTmFormat($surgeryEndTime);
			/*
			list($EndHours,$EndMinutes) = explode(":",$surgeryEndTime);
			if($EndHours>=12){
			    $am_pm="PM";
			}
			else{
			  $am_pm="AM";
			}
			if($EndHours>=13){
			  $EndHours = $EndHours-12;
			   if(strlen($EndHours)==1){
			      $EndHours="0".$EndHours;
			   }
			}else
			{
			 //DO nothing
			}
			$surgeryEndTime = $EndHours.":".$EndMinutes." ".$am_pm;
			*/
		}
		//$surgeryTimeOut		= $ViewOpRoomRecordRow["surgeryTimeOut"];
		$surgeryTimeOut		= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryTimeOut"]);
		$pillow_under_knees = $ViewOpRoomRecordRow["pillow_under_knees"];
		$head_rest = $ViewOpRoomRecordRow["head_rest"];
		$safetyBeltApplied = $ViewOpRoomRecordRow["safetyBeltApplied"];
		$other_position = $ViewOpRoomRecordRow["other_position"];
		$surgeryPatientPositionOther = $ViewOpRoomRecordRow["surgeryPatientPositionOther"];
		$anesStartTime = $ViewOpRoomRecordRow["anesStartTime"];
		if($anesStartTime=="00:00:00" || $anesStartTime=="") {
			$anesStartTime = "";
		}
		
		$anesEndTime=$ViewOpRoomRecordRow["anesEndTime"];
		if($anesStartTime=="00:00:00" || $anesStartTime=="") {
			$anesStartTime = "";
		}
		
		$Solumedrol = $ViewOpRoomRecordRow["Solumedrol"];
		$Dexamethasone = $ViewOpRoomRecordRow["Dexamethasone"];
		$Kenalog = $ViewOpRoomRecordRow["Kenalog"];
		$Vancomycin = $ViewOpRoomRecordRow["Vancomycin"];
		$Ancef = $ViewOpRoomRecordRow["Ancef"];
		$Gentamicin = $ViewOpRoomRecordRow["Gentamicin"];
		$Depomedrol = $ViewOpRoomRecordRow["Depomedrol"];
		$postOpInjOther = $ViewOpRoomRecordRow["postOpInjOther"];
		
		$SolumedrolList = $ViewOpRoomRecordRow["SolumedrolList"];
		$DexamethasoneList = $ViewOpRoomRecordRow["DexamethasoneList"];
		$KenalogList = $ViewOpRoomRecordRow["KenalogList"];
		$VancomycinList = $ViewOpRoomRecordRow["VancomycinList"];
		$AncefList = $ViewOpRoomRecordRow["AncefList"];
		$GentamicinList = $ViewOpRoomRecordRow["GentamicinList"];
		$DepomedrolList = $ViewOpRoomRecordRow["DepomedrolList"];
		
		$anesthesia_service = $ViewOpRoomRecordRow["anesthesia_service"];
		
		$TopicalBlock = $ViewOpRoomRecordRow["TopicalBlock"];
		
		
		$patch = $ViewOpRoomRecordRow["patch"];
		$shield = $ViewOpRoomRecordRow["shield"];
		$needleSutureCount = $ViewOpRoomRecordRow["needleSutureCount"];
		$needleSutureCountNA = $ViewOpRoomRecordRow["needleSutureCountNA"];
		$collagenShield = $ViewOpRoomRecordRow["collagenShield"];
		$Econopred = $ViewOpRoomRecordRow["Econopred"];
		$Zymar = $ViewOpRoomRecordRow["Zymar"];
		$Tobradax = $ViewOpRoomRecordRow["Tobradax"];
		$soakedInOtherChk = $ViewOpRoomRecordRow["soakedInOtherChk"];
		$soakedInOther = $ViewOpRoomRecordRow["soakedInOther"];
		$postOpDiagnosis = $ViewOpRoomRecordRow["postOpDiagnosis"];
		
		if(trim($postOpDiagnosis)=="") {
			$postOpDiagnosis = $preOpDiagnosis;
		}
		
		$other_remain = $ViewOpRoomRecordRow["other_remain"];
		$complications = $ViewOpRoomRecordRow["complications"];
		$postOpDrops = $ViewOpRoomRecordRow["postOpDrops"]; //SEE THIS AT THE BOTTOM
		$nurseNotes = $ViewOpRoomRecordRow["nurseNotes"];
		$others_present = $ViewOpRoomRecordRow["others_present"];
		$intraOpPostOpOrder = $ViewOpRoomRecordRow['intraOpPostOpOrder'];
		$surgeonId1 = $ViewOpRoomRecordRow["surgeonId1"];
		$anesthesiologistId = $ViewOpRoomRecordRow["anesthesiologistId"];
		$scrubTechId1 = $ViewOpRoomRecordRow["scrubTechId1"];
		$scrubTechOther1 = $ViewOpRoomRecordRow["scrubTechOther1"];
		$scrubTechId2 = $ViewOpRoomRecordRow["scrubTechId2"];
		$scrubTechOther2 = $ViewOpRoomRecordRow["scrubTechOther2"];
		$circulatingNurseId = $ViewOpRoomRecordRow["circulatingNurseId"];
		$NurseTitle = $ViewOpRoomRecordRow["nurseTitle"];
		$NurseId = $ViewOpRoomRecordRow["nurseId"];
		$iolName = $ViewOpRoomRecordRow["iol_ScanUpload"];
		
		$signNurseId = $ViewOpRoomRecordRow["signNurseId"];
		$signNurseDateTime = $ViewOpRoomRecordRow["signNurseDateTime"];
		$signNurseDateTimeFormat = $ViewOpRoomRecordRow["signNurseDateTimeFormat"];
		$signNurseFirstName = $ViewOpRoomRecordRow["signNurseFirstName"];
		$signNurseMiddleName = $ViewOpRoomRecordRow["signNurseMiddleName"];
		$signNurseLastName = $ViewOpRoomRecordRow["signNurseLastName"];
		$signNurseStatus = $ViewOpRoomRecordRow["signNurseStatus"];
		
		$signNurse1Id = $ViewOpRoomRecordRow["signNurse1Id"];
		$signNurse1DateTime = $ViewOpRoomRecordRow["signNurse1DateTime"];
		$signNurse1DateTimeFormat = $ViewOpRoomRecordRow["signNurse1DateTimeFormat"];
		$signNurse1FirstName = $ViewOpRoomRecordRow["signNurse1FirstName"];
		$signNurse1MiddleName = $ViewOpRoomRecordRow["signNurse1MiddleName"];
		$signNurse1LastName = $ViewOpRoomRecordRow["signNurse1LastName"];
		$signNurse1Status = $ViewOpRoomRecordRow["signNurse1Status"];
		
		$signSurgeon1Id = $ViewOpRoomRecordRow["signSurgeon1Id"];
		$signSurgeon1FirstName = $ViewOpRoomRecordRow["signSurgeon1FirstName"];
		$signSurgeon1MiddleName = $ViewOpRoomRecordRow["signSurgeon1MiddleName"];
		$signSurgeon1LastName = $ViewOpRoomRecordRow["signSurgeon1LastName"];
		$signSurgeon1Status = $ViewOpRoomRecordRow["signSurgeon1Status"];
		
		$signSurgeon2Id = $ViewOpRoomRecordRow["signSurgeon2Id"];
		$signSurgeon2FirstName = $ViewOpRoomRecordRow["signSurgeon2FirstName"];
		$signSurgeon2MiddleName = $ViewOpRoomRecordRow["signSurgeon2MiddleName"];
		$signSurgeon2LastName = $ViewOpRoomRecordRow["signSurgeon2LastName"];
		$signSurgeon2Status = $ViewOpRoomRecordRow["signSurgeon2Status"];
	
		$signSurgeon3Id = $ViewOpRoomRecordRow["signSurgeon3Id"];
		$signSurgeon3FirstName = $ViewOpRoomRecordRow["signSurgeon3FirstName"];
		$signSurgeon3MiddleName = $ViewOpRoomRecordRow["signSurgeon3MiddleName"];
		$signSurgeon3LastName = $ViewOpRoomRecordRow["signSurgeon3LastName"];
		$signSurgeon3Status = $ViewOpRoomRecordRow["signSurgeon3Status"];
	
		$signAnesthesia1Id = $ViewOpRoomRecordRow["signAnesthesia1Id"];
		$signAnesthesia1FirstName = $ViewOpRoomRecordRow["signAnesthesia1FirstName"];
		$signAnesthesia1MiddleName = $ViewOpRoomRecordRow["signAnesthesia1MiddleName"];
		$signAnesthesia1LastName = $ViewOpRoomRecordRow["signAnesthesia1LastName"];
		$signAnesthesia1Status = $ViewOpRoomRecordRow["signAnesthesia1Status"];

		$signAnesthesia2Id = $ViewOpRoomRecordRow["signAnesthesia2Id"];
		$signAnesthesia2FirstName = $ViewOpRoomRecordRow["signAnesthesia2FirstName"];
		$signAnesthesia2MiddleName = $ViewOpRoomRecordRow["signAnesthesia2MiddleName"];
		$signAnesthesia2LastName = $ViewOpRoomRecordRow["signAnesthesia2LastName"];
		$signAnesthesia2Status = $ViewOpRoomRecordRow["signAnesthesia2Status"];
	
		$signScrubTech1Id = $ViewOpRoomRecordRow["signScrubTech1Id"];
		$signScrubTech1FirstName = $ViewOpRoomRecordRow["signScrubTech1FirstName"];
		$signScrubTech1MiddleName = $ViewOpRoomRecordRow["signScrubTech1MiddleName"];
		$signScrubTech1LastName = $ViewOpRoomRecordRow["signScrubTech1LastName"];
		$signScrubTech1Status = $ViewOpRoomRecordRow["signScrubTech1Status"];
	
		$signScrubTech2Id = $ViewOpRoomRecordRow["signScrubTech2Id"];
		$signScrubTech2FirstName = $ViewOpRoomRecordRow["signScrubTech2FirstName"];
		$signScrubTech2MiddleName = $ViewOpRoomRecordRow["signScrubTech2MiddleName"];
		$signScrubTech2LastName = $ViewOpRoomRecordRow["signScrubTech2LastName"];
		$signScrubTech2Status = $ViewOpRoomRecordRow["signScrubTech2Status"];
		
		$iol_serial_number=	trim($ViewOpRoomRecordRow["iol_serial_number"]);
		$version_num =  $ViewOpRoomRecordRow["version_num"];
		if(!($version_num) && ($formStatus == 'completed' || $formStatus == 'not completed')) { $version_num	=	1; }
		else if(!($version_num) && $formStatus <> 'completed' && $formStatus <> 'not completed') { $version_num	=	$current_form_version; }
		
//GETTING SURGEON ID AND PROCEDURE ID FROM PATIENT CONFIRMATION
	$OpRoom_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$OpRoom_patientConfirm_tblRes = imw_query($OpRoom_patientConfirm_tblQry) or die(imw_error());
	$OpRoom_patientConfirm_tblRow = imw_fetch_array($OpRoom_patientConfirm_tblRes);
	
	$patient_primary_procedure_id = $OpRoom_patientConfirm_tblRow['patient_primary_procedure_id'];
	$patient_secondary_procedure_id = $OpRoom_patientConfirm_tblRow['patient_secondary_procedure_id'];
	$patient_tertiary_procedure_id = $OpRoom_patientConfirm_tblRow['patient_tertiary_procedure_id'];
	$surgeonId_patientConfirm =  $OpRoom_patientConfirm_tblRow['surgeonId'];
//GETTING SURGEON ID AND PROCEDURE ID FROM PATIENT CONFIRMATION
	
//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
if($surgeonId_patientConfirm<>"") {
	
	$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId_patientConfirm' and del_status=''";
	$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
	while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
		$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
	}
	if(is_array($surgeonProfileIdArr)){
		$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
	} else {
		$surgeonProfileIdImplode = 0;
	}
	$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode)";
	$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
	$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
	if($selectSurgeonProcedureNumRow>0) {
		while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
			$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
			if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
				$templateFound = "true";
				$profileId = $selectSurgeonProcedureRow['profileId'];
			}		
		}
	}
	if($templateFound=="true") {
			$selectSurgeonPostOpDropQry = "select * from surgeonprofile where surgeonId = '$surgeonId_patientConfirm' AND surgeonProfileId = '$profileId' and del_status=''";
			$selectSurgeonPostOpDropRes = imw_query($selectSurgeonPostOpDropQry) or die(imw_error());
			$selectSurgeonPostOpRow = imw_fetch_array($selectSurgeonPostOpDropRes);
			$prefillIntraOpPostOpOrder = $selectSurgeonPostOpRow['intraOpPostOpOrder'];
			$prefillPostOpDrop = 	$selectSurgeonPostOpRow['postOpDrop'];
	}
	//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT POST-OP DROP
	//FROM SURGEON'S DEFAULT PROFILE 
		if($templateFound<>"true") {
			/*$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId_patientConfirm' AND defaultProfile = '1'";
			$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
			$selectSurgeonNumRow = imw_num_rows($selectSurgeonRes);
			if($selectSurgeonNumRow>0) {
				$selectSurgeonRow = imw_fetch_array($selectSurgeonRes);
				$prefillPostOpDrop = $selectSurgeonRow['postOpDrop'];
			}
			else
			*/
			{
				/* Start Procedure Preference Card if surgeon's profile/Default  Not found */
			
				$proceduresArr	=	array($patient_primary_procedure_id,$patient_secondary_procedure_id,$patient_tertiary_procedure_id);
				foreach($proceduresArr as $procedureId)
				{
					if($procedureId)
					{		
						$procPrefCardQry	=	"Select * From procedureprofile Where procedureId = '".$procedureId."' ";
						$procPrefCardSql	=	imw_query($procPrefCardQry) or die( 'Error at line no.'. (__LINE__).': '.imw_error());
						$procPrefCardCnt	=	imw_num_rows($procPrefCardSql);
						if($procPrefCardCnt > 0 )
						{
							$procPrefCardRow	=	imw_fetch_object($procPrefCardSql);
							$prefillIntraOpPostOpOrder = $procPrefCardRow->intraOpPostOpOrder;
							$prefillPostOpDrop	= $procPrefCardRow->postOpDrop;
							
							break; 
						}
					}
				}
				
				/* End Procedure Preference Card if surgeon's profile/Default  Not found */	
				
				
			}
		}	
	//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT POST-OP DROP
	//FROM SURGEON'S DEFAULT PROFILE 
	

}
//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE	

//SET $yetToSave TO TRUE IF THIS PAGE IS YET TO SAVE FIRST TIME
	$yetToSaveQry=imw_query("SELECT * FROM `chartnotes_change_audit_tbl` WHERE
										confirmation_id='".$pConfId."' and
										form_name='$fieldName' and status='created'");
			 $yetToSaveNumrows=imw_num_rows($yetToSaveQry);
			if($yetToSaveNumrows>0) {
				//DO NOTHING, 
			}else {
				$yetToSave = "true";
			}
//SET $yetToSave TO TRUE IF THIS PAGE IS YET TO SAVE FIRST TIME		


//PEFILL THE POST-OP DROP FROM SURGEON PROFILE IF THIS PAGE IS YET TO SAVE FIRST TIME		
	if($yetToSave=="true") {
		$postOpDrops = $prefillPostOpDrop;
		$intraOpPostOpOrder = $prefillIntraOpPostOpOrder;
	}
//PEFILL THE POST-OP DROP FROM SURGEON PROFILE IF THIS PAGE IS YET TO SAVE FIRST TIME
	
	$confirmationId	=	$_REQUEST["pConfId"] ;

	/*
	*
	*	Prefilling Supplies From Procedure Supplies Templates 
	*
	*/
	$whereOpSuppliesArray	=	array('confirmation_id = '=>$confirmationId );
	$chkOpSuppliesCount		=	$objManageData->getRowCount('operatingroomrecords_supplies',$whereOpSuppliesArray);
	if( !$formStatus || $chkOpSuppliesCount=='0' || $save_manual == '0')
	{
		$insertUpdateArray['displayStatus']	=	0; 
		$objManageData->updateRecords($insertUpdateArray,'operatingroomrecords_supplies','confirmation_id',$confirmationId);
		
		
		$whereArray		=	array('procedure_id = '=>$patient_primary_procedure_id, 'deleted =' => 0 );
		$chkTempCount	=	$objManageData->getRowCount('procedure_supplies',$whereArray);
		if($chkTempCount > 0)
		{ 
			$procSuppData	=	$objManageData->getRecord('procedure_supplies','',$whereArray);
		}
		else 
		{ 
			$whereArray		=	array('procedure_id = '=>0, 'deleted ='=> 0 );
			$procSuppData	=	$objManageData->getRecord('procedure_supplies','',$whereArray);	
		}
		
		$templateId	 =	$procSuppData['proc_supp_id'] ;
		$templateSupp=	$procSuppData['supplies'] ;
		
		if(!$templateSupp)
		{
			$defaultSupplies=	$objManageData->getDefault('predefine_suppliesused','suppliesUsedId');	
			$templateSupp	=	str_replace(", ",",",$defaultSupplies);
		}
		
		if( $templateSupp )
		{
			$whereArray		=	array( 'deleted'=>'0');
			$suppliesData	=	$objManageData->getMultiChkArrayRecords('predefine_suppliesused',$whereArray,0,0," And suppliesUsedId IN (".$templateSupp.") ");
			if(!$suppliesData)
			{
				$suppliesData	=	$objManageData->getMultiChkArrayRecords('predefine_suppliesused',$whereArray,0,0," And isDefault = '1' ");	
			}
			if( is_array($suppliesData) && count($suppliesData) > 0 )
			{
				foreach($suppliesData as $supply) 
				{
						
						$whereArray			=	array('confirmation_id = '=>$confirmationId, 'suppName =' => $supply->name );
						$chkSuppCount	=	$objManageData->getRowCount('operatingroomrecords_supplies',$whereArray);
						
						$insertUpdateArray['suppName'] 		= 	$supply->name;
						$insertUpdateArray['suppQtyDisplay']	= 	$supply->qtyChkBox;
						$insertUpdateArray['suppChkStatus']	= 	'';
						$insertUpdateArray['suppList'] 			= 	'';
						$insertUpdateArray['templateId']			= 	$templateId;
						$insertUpdateArray['confirmation_id'] = 	$confirmationId;
						$insertUpdateArray['displayStatus'] 	=	1;
						$insertUpdateArray['predefine_supp_id'] 	=	$supply->suppliesUsedId;
						
						if($chkSuppCount ==  0 )
						{	
							$objManageData->addRecords($insertUpdateArray,'operatingroomrecords_supplies');
						}
						else
						{
							$suppListData	=	$objManageData->getRecord('operatingroomrecords_supplies','',$whereArray);
							$objManageData->updateRecords($insertUpdateArray,'operatingroomrecords_supplies','suppRecordId',$suppListData['suppRecordId']);
						}
						
				} // end foreach $suppliesData 
				
			} // end if array $suppliesData count > 0  
			
		} // end if ($templateSupp )
		
	}// end if( $formStatus )
	
	/*
	*
	*	End Prefilling Supplies From Procedure Supplies Templates 
	*
	*/
	
?>
<script type="text/javascript">
//top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');

function startCallback() {	
	return true;
}
function completeCallback(response){
	setTimeout('getImage()', 1000);
}
function getImage(){
	var operatingRoomRecordsId = <?php echo $operatingRoomRecordsId; ?>;
	//document.frames['iframeIOL'].location.reload();
	document.getElementById('iframeIOL').src='geIolImage.php?operatingRoomRecordsId='+operatingRoomRecordsId;
	var objFrm = top.mainFrame.main_frmInner.document.frm_uploadIOLImage;
	if(objFrm.hidd_delImage.value=='yes' || objFrm.hidd_delImage.value=='yes2') {
		//alert(objFrm.hidd_delImage.value);
		
		//if(!top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgThumbNail') && !top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgThumbNail2')){
			//document.getElementById('iframeIOL').style.height = '0px';
			//document.getElementById('iframeIOL').style.width = '0px';
			top.mainFrame.main_frmInner.document.frm_uploadIOLImage.hidd_delImage.value='';
		//}
	}else {
		document.getElementById('iframeIOL').style.height = '200px';
		document.getElementById('iframeIOL').style.width = '100%';
		top.mainFrame.main_frmInner.document.frm_uploadIOLImage.hidd_delImage.value='';
	}	
}
function showImgDiv(){
	document.getElementById('imgDiv').style.display = 'block';
}
//CODE FOR SHOWING SIGNATURE ON FILE 

var surgeonarrSign = new Array();

function checkSign(objElem)
{
	var t = objElem.value;
	document.getElementById("surgeonSign_id").innerHTML= (t != "") ? surgeonarrSign[t] : "" ;
}
 
var scrubTecharrSign = new Array();

function checkscrubSign(objElem)
{
  var scrub = objElem.value;
  document.getElementById("scrubTecharrSign").innerHTML= (scrub != "") ? scrubTecharrSign[scrub] : "" ;
}
var scrubTecharrSign1 = new Array();

function checkscrubSign1(objElem)
{
  var scrub1 = objElem.value;
  document.getElementById("scrubTecharrSign1").innerHTML= (scrub1 != "") ? scrubTecharrSign1[scrub1] : "" ;
}
var circulatingNursearrSign= new Array();

function checkcirculatingNurse(objElem)
{
  var circulatingNurse = objElem.value;
  document.getElementById("circulatingNursearrSign").innerHTML= (circulatingNurse != "") ? circulatingNursearrSign[circulatingNurse] : "" ;
}
var NursearrSign= new Array();
function checkNurse(objElem)
{
  var Nurse = objElem.value;
  document.getElementById("NursearrSign").innerHTML= (Nurse != "") ? NursearrSign[Nurse] : "" ;
}

//END OF SHOWING SIGNATURE

//button swaping done by mamta
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

//button swaping done by mamta

//FUNCTION TO SET DEFAULT POSTOP DIAGNOSIS VALUE(IF EMPTY)
	function LTrim( value ) 
	{
		var re = /\s*((\S+\s*)*)/;
		return value.replace(re, "$1");
	}
	// Removes ending whitespaces
	function RTrim( value ) 
	{
		var re = /((\s*\S+)*)\s*/;
		return value.replace(re, "$1");
	}
	// Removes leading and ending whitespaces
	function trim( value ) 
	{
		return LTrim(RTrim(value));
	}
	
	function chkTxtAreaFun(postop_diag_area_id,perop_diag_area_id) {
		if(trim(document.getElementById(postop_diag_area_id).value)=="") {
			document.getElementById(postop_diag_area_id).value=document.getElementById(perop_diag_area_id).value;
		}
	}
//END FUNCTION TO SET DEFAULT POSTOP DIAGNOSIS VALUE(IF EMPTY)

//FUNCTION TO SET VALUE OF CHECKBOX OF NURSE, SURGEON , ANESTHESIOLOGIST
function setChbxValue(chbx_id,hidd_chbx_id) {
	if(document.getElementById(chbx_id).checked==true) {
		document.getElementById(hidd_chbx_id).value = "Yes";
	}else {
		document.getElementById(hidd_chbx_id).value = "";
	}	
}	
//END FUNCTION TO SET VALUE OF CHECKBOX OF NURSE, SURGEON , ANESTHESIOLOGIST


//FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE
	
	function noAuthorityFun(userName) {
		alert("You are not authorised to make signature of "+userName);
		return false;
	}
	function noSignInAdmin() {
		alert("You have yet to make signature in Admin");
		return false;
	}
	
	function alreadySignOnce(userTypeNum) {
		alert('You have already signed at '+userTypeNum);
	}
	//Display Signature Of Nurse
	function GetXmlHttpObject()
	{ 
				
		var objXMLHttp=null
		if (window.XMLHttpRequest)
		{
		objXMLHttp=new XMLHttpRequest()
		}
		else if (window.ActiveXObject)
		{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
		}
		return objXMLHttp
	}			

	function displayTimeReview(chbx_id, sx_plan_review_time_span_id, hidd_review_dt_tm_format_id, hidd_review_dt_tm_id)
	{
		if(document.getElementById(chbx_id).checked==true) {
			xmlHttp=GetXmlHttpObject()
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request")
				return
			}
			var url="user_agent.php"
			url=url+"?jsServerTimeRequest=fullDtTimeReview&pste="+Math.random();
			xmlHttp.onreadystatechange=function() {
				if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
				   var resp = xmlHttp.responseText;
				   var dtTmVal	=  resp.split("@@")[0];
				   var dtTmDb 	=  resp.split("@@")[1];
				   if(document.getElementById(hidd_review_dt_tm_format_id)) {
						if(document.getElementById(hidd_review_dt_tm_format_id).value != "" ) {
							dtTmVal = document.getElementById(hidd_review_dt_tm_format_id).value;
						}
						if(document.getElementById(hidd_review_dt_tm_id)) {
							if(document.getElementById(hidd_review_dt_tm_id).value!="0000-00-00 00:00:00" && document.getElementById(hidd_review_dt_tm_id).value !="") {
								dtTmDb = document.getElementById(hidd_review_dt_tm_id).value;
							}
							document.getElementById(hidd_review_dt_tm_id).value = dtTmDb;	
						}
				   }
				   if(document.getElementById(sx_plan_review_time_span_id)) {
						document.getElementById(sx_plan_review_time_span_id).innerHTML="("+dtTmVal+")";
				   }
				} 
			};
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}else {
			if(document.getElementById(sx_plan_review_time_span_id)) {
				document.getElementById(sx_plan_review_time_span_id).innerHTML="";
		   }
		}
	}
		
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {

		if(delSign) {
			document.getElementById(TDUserNameId).style.display = 'block';
			document.getElementById(TDUserSignatureId).style.display = 'none';
		}else {
			document.getElementById(TDUserNameId).style.display = 'none';
			document.getElementById(TDUserSignatureId).style.display = 'block';
		}

		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 

		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
		url=url+"&userIdentity="+userIdentity
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		if(delSign) {
			url=url+"&delSign=yes"
		}
		url=url+"&preColor="+preColor1
		xmlHttp.onreadystatechange=displayUserSignFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function displayUserSignFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				var objId = document.getElementById('hiddSignatureId').value;
				document.getElementById(objId).innerHTML=xmlHttp.responseText;
				top.frames[0].setPNotesHeight();
			}
	}
	//End Display Signature Of Nurse
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

//START FUNCTION TO GET MANUFACTURER LENS BRAND
	function getLensBrand(manufacturerName,pagename) {
		manufacturerName = manufacturerName.replace('&','~');
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url=pagename
		url=url+"?manufacture="+manufacturerName;
		url=url+"&patient_id="+patient_id1;
		url=url+"&pConfId="+pConfId1;
		xmlHttp.onreadystatechange=getLensBrandFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function getLensBrandFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				if(document.getElementById('lensBrandTD')){
					//document.getElementById('lensBrandTD').innerHTML=xmlHttp.responseText;
					$("#lensBrandTD").html("");
					$("#lensBrandTD").html(xmlHttp.responseText);
					//top.frames[0].setPNotesHeight();
					$("#lensBrand").selectpicker('refresh');
				}
			}
	}
//END FUNCTION TO GET MANUFACTURER LENS BRAND

function uploadImage(){
	var imgUpload = document.getElementById('elem_iolscan').value;
	if(imgUpload){
		var xmlHttp;
		try{		
			xmlHttp=new XMLHttpRequest();
		}
		catch (e){
			try{
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e){
				try{
					xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e){
					alert("Your browser does not support AJAX!");
					return false;
				}
			}
		}
		xmlHttp.onreadystatechange=function(){
			if(xmlHttp.readyState==4){
				var val = xmlHttp.responseText;
				if(val!=''){
					alert(val)
				}
			}
		}
		xmlHttp.open("GET","uploadIOLImage.php?image="+imgUpload,true);
		xmlHttp.send(null);
	}
}
function hide_footer() {
	top.document.getElementById("footer_button_id").style.display = "none";
}
function mainpageSetHiddField(objHid) {
	//SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE (TO CHECK SURGEON ALL SIGNATURE IN CHART NOTE, AND 'VARIFIED BY SURGEON' IN OPERATING ROOM RECORD)
		if(top.document.forms[0]){
			if(top.document.forms[0].hidd_chkDisplaySurgeonSign) {
				top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'true';
			}
		}
	//END SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE (TO CHECK SURGEON ALL SIGNATURE IN CHART NOTE, AND 'VARIFIED BY SURGEON' IN OPERATING ROOM RECORD)

				
}
function mainpageSetAnesthesiaHiddField(objHid) {
	//SET HIDDEN FIELD (hidd_chkDisplayAnesthesiaSign) TO TRUE AT MAINPAGE (TO CHECK ANESTHESIOLOGIST SIGNATURE IN ANESTHESIA CHART NOTE, AND 'VARIFIED BY ANESTHESIOLOGIST' IN OPERATING ROOM RECORD)
		if(top.document.forms[0]){
			if(top.document.forms[0].hidd_chkDisplayAnesthesiaSign) {
				top.document.forms[0].hidd_chkDisplayAnesthesiaSign.value = 'true';
			}
		}
	//END SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE (TO CHECK ANESTHESIOLOGIST SIGNATURE IN ANESTHESIA CHART NOTE, AND 'VARIFIED BY ANESTHESIOLOGIST' IN OPERATING ROOM RECORD)
}

function setScrollTopFn() {
	if(top.document.getElementById('hiddScrollTop')) {
		var frmScrlTop=parseFloat(top.document.getElementById('hiddScrollTop').value);
		frmScrlTop=parseFloat(frmScrlTop);
		if(top.mainFrame) {
			if(top.mainFrame.main_frmInner) {
				if(top.document.getElementById('hiddScrollTop').value>100){
					top.mainFrame.main_frmInner.document.body.scrollTop=parseFloat(frmScrlTop);
					if(document.getElementById('divSaveAlert')) {
						document.getElementById('divSaveAlert').style.top=frmScrlTop;
					}
					top.document.getElementById('hiddScrollTop').value='';
				}
			}	
		}	
	}	
}

var flag_sn = true;
function validate_sn() {
	var ftpSupp 		= '<?php echo $ftpSupp; ?>';
	if(!ftpSupp) { return true; }
	var obj 			= document.getElementById('iol_serial_number');
	var prev_serial_num = obj.getAttribute('previous-data');
	var serial_num 		= obj.value;
	
	if(trimNew(serial_num)!='') {
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
			alert ("Browser does not support HTTP Request")
			return
		}
		var url="op_room_record_supplies_ajax.php"
		url=url+"?serial_num="+serial_num+"&prev_serial_num="+prev_serial_num;
		xmlHttp.onreadystatechange=function() {
			if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
			   var resp = xmlHttp.responseText;
			   if(resp!="1"){
					if(document.getElementById("iol_serial_number")) {
						document.getElementById("iol_serial_number").value=prev_serial_num;
					}
					//alert(resp);
					top.modalAlert(resp);
					flag_sn = false;
			   }else {
					flag_sn = true;   
			   }
			} 
		};
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	return flag_sn;
}
</script>
<?php
// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
// GETTING FINALIZE STATUS

if($finalizeStatus=='true'){
	$onloadFun = "hide_footer();";
}
?>
<div id="post" style="display:none; position:absolute;"></div>
<?php
	//GET ANESTHESIOLOGIST SIGNATURE, NURSE SIGNATURE, SURGEON SIGNATURE 
		 $signatureOfRelievedbyNurse = getUserSign($NurseId,'Nurse');
	//END GET ANESTHESIOLOGIST SIGNATURE, NURSE SIGNATURE, SURGEON SIGNATURE 
	
	//show Nurse sign after saving d data
	if($NurseId)
	{
		if($signatureOfRelievedbyNurse) {
			$signatureOfRelievedbyNurseTemp='Yes';
		}	
		else
		{
		   $signatureOfRelievedbyNurseTemp='No';
		}
	}
	else
	{
	    $signatureOfRelievedbyNurseTemp='';
	}

//END VIEW RECORD FROM DATABASE


	//START CODE TO GET DOCUMENTS OF EKG H&P CONSENT
	$ekgHpLink = "";
	$anesConsentEkgHpArr = array('EKG', 'H&P','Ocular Hx','Health Questionnaire','Sx Planning Sheet');
	foreach($anesConsentEkgHpArr as $anesConsentEkgHpName) {
		$eKgHpCLickFun='';
		$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath 
							 FROM  scan_upload_tbl sut,scan_documents sd
							 WHERE sd.confirmation_id 	= '".$pConfId."'
							 AND   sut.confirmation_id 	= '".$pConfId."'
							 AND   sd.document_name 	= '".$anesConsentEkgHpName."'
							 AND   sd.document_id 		= sut.document_id
							 ORDER BY sd.document_id, sut.document_id
							"; 
		$scnFoldrEkgHpRes = imw_query($scnFoldrEkgHpQry) or die(imw_error());
		$scnFoldrEkgHpNumRow = imw_num_rows($scnFoldrEkgHpRes);
		if($scnFoldrEkgHpNumRow<=0) {
			if($anesConsentEkgHpName=='EKG' || $anesConsentEkgHpName=='H&P' || $anesConsentEkgHpName=='Sx Planning Sheet') {
				if($anesConsentEkgHpName=='EKG' || $anesConsentEkgHpName=='H&P') {
					$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath 
										 FROM  scan_upload_tbl sut,scan_documents sd 
										 WHERE sd.confirmation_id 	= '".$pConfId."'
										 AND   sut.confirmation_id 	= '".$pConfId."'
										 AND   sut.document_name like '%".$anesConsentEkgHpName."%'
										 AND   sd.document_id 		= sut.document_id
										 ORDER BY sd.document_id, sut.document_id
										"; 
				}
				if($anesConsentEkgHpName=='Sx Planning Sheet') {
					$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath 
										 FROM  scan_upload_tbl sut,scan_documents sd 
										 WHERE sd.confirmation_id 	= '".$pConfId."'
										 AND   sut.confirmation_id 	= '".$pConfId."'
										 AND   sd.document_name 	= 'Clinical'
										 AND   sut.pdfFilePath 	 LIKE '%Sx_Planing_Sheet_%'
										 AND   sd.document_id 		= sut.document_id
										 ORDER BY sd.document_id, sut.document_id
										"; 
				}
				$scnFoldrEkgHpRes = imw_query($scnFoldrEkgHpQry) or die(imw_error());
				$scnFoldrEkgHpNumRow = imw_num_rows($scnFoldrEkgHpRes);
				
			}
		}
		if($scnFoldrEkgHpNumRow>0) {
			while($scnFoldrEkgHpRow = imw_fetch_array($scnFoldrEkgHpRes)) {
				$scan_upload_id = $scnFoldrEkgHpRow['scan_upload_id'];
				$image_type = $scnFoldrEkgHpRow['image_type'];
				$pdfFilePath = $scnFoldrEkgHpRow['pdfFilePath'];
				
				if($image_type=='application/pdf') $image_type = 'pdf';
				$imageTypeNew = "\'".$image_type."\'";
				$scanUploadId = "\'".$scan_upload_id."\'";
				$pdfFilePathNew = "\'".$pdfFilePath."\'";
				$eKgHpCLickFun.= 'top.openImage('.$scanUploadId.','.$imageTypeNew.','.$pdfFilePathNew.');';
			}
		}
		if($eKgHpCLickFun!='') {
			$anesConsentEkgHpNameNew = $anesConsentEkgHpName;
			$anesConsentEkgHpNameNew = str_ireplace('H&P','H&amp;P',$anesConsentEkgHpNameNew);
			$anesConsentEkgHpNameNew = str_ireplace('Ocular Hx','OCX',$anesConsentEkgHpNameNew);
			$anesConsentEkgHpNameNew = str_ireplace('Health Questionnaire','HQ',$anesConsentEkgHpNameNew);
			$anesConsentEkgHpNameNew = str_ireplace('Sx Planning Sheet','SxP',$anesConsentEkgHpNameNew);
			$ekgHpLink.='<a href="#" class="btn-sm" onclick="'.$eKgHpCLickFun.'">'.$anesConsentEkgHpNameNew.'</a>&nbsp;';
		}
	}
	//END CODE TO GET DOCUMENTS OF EKG H&P CONSENT

//show all epost ravi
?>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<?php
	include("common/pre_defined_popup.php");
	include_once("common/preDefineModel.php");
?>
<div id="imgDiv" style="position:absolute;display:none;left:150px;top:100px;">
<table class="table_pad_bdr">
	<tr>
		<td>
			<iframe style="height:550px; width:650px;" name="imageUplades" src="opRoomImagePopUp.php?from=op_room_record&amp;id=<?php echo $operatingRoomRecordsId; ?>"></iframe>
		</td>
	</tr>
</table>		
</div>


<form enctype="multipart/form-data" name="frm_op_room" method="post" style="margin:0px;" action="op_room_record.php?SaveRecordForm=true<?php echo $saveLink;?>">
	<input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
	<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
	<input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">			
	<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
	<input type="hidden" name="saveRecord" id="saveRecord" value="true">
	<input type="hidden" name="getText" id="getText">
	<input type="hidden" name="scan" id="scan">
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId" value="">
	<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>"/>
	<input type="hidden" name="frmAction" id="frmAction" value="op_room_record.php">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">				
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
  <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
  <input type="hidden" id="vitalSignGridHolder" />
  <input type="hidden" name="hidd_sxPlanReviewedBySurgeonChk" id="hidd_sxPlanReviewedBySurgeonChk_id" value="<?php echo $sxPrbsChk;?>">
	<!-- <input type="hidden" name="hidd_anyOneImageExist" id="hidd_anyOneImageExist" value="" /> -->
	<div id="divSaveAlert" style="position:absolute; display:none; z-index:999;">
		<?php 
            $bgCol = $title_op_room_record;
            $borderCol = $title_op_room_record;
            include('saveDivPopUp.php'); 
        ?>
    </div>
    <div class="scheduler_table_Complete" id="" style="">
    
    	<?php
				$epost_table_name = "operatingroomrecords";
				include("./epost_list.php");
		?>
        <!--<div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_op">
            <span class="bg_span_op">
                Operating Room Record
            </span>
			<?php
				
				if($ekgHpLink) { ?>
					<span id="ekgHpLink" class="nowrap valignBottom"><?php echo $ekgHpLink; ?></span>
			<?php 
				} ?>
         </div>-->	
         
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_op">
            <div class="scanner_win new_s">
             <h4>
                <span>Intra Op Record</span>      
             </h4>
            </div>
        </div>    
        
        <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                <div class="panel panel-default new_panel new_panel  bg_panel_op">
                    <div class="panel-heading" id="allergy_div_a">
                        <a onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', '10', parseInt($(this).offset().left + 6),parseInt($(this).offset().top - $(document).scrollTop()-138)),document.getElementById('selected_frame_name_id').value='iframe_allergies_oproom_rec';" class="panel-title rob alle_link show-pop-trigger2 btn btn-default "> <span class="fa fa-caret-right"></span>  Allergies   </a>
                       
                    </div>
                   
                    <div class="panel-body">
                        <div class="inner_safety_wrap">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                   
                                   <div class="scheduler_table_Complete ">
                                       <div class="my_table_Checkall table_slider_head">
                                                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                    <thead class="cf">
                                                        <tr>
                                                            <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Name</th>
                                                            <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Reaction </th>
                                                        </tr>
                                                    </thead>
                                                 </table>
                                       </div>
                                       <div class="table_slider">          
                                          <div id="iframe_allergies_oproom_rec">
												<?php  
													$allgNameWidth=228;
													$allgReactionWidth=228;
													include("health_quest_spreadsheet.php");
												?>
											</div>
                                         </div>                

                                      </div>

                                </div>
                                
                            </div>	 
                         </div>
                    </div> 
                </div>
                
                <div class="clearfix margin_adjustment_only"></div>
           </div>
           
           <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                
                
                <div class="panel panel-default new_panel bg_panel_op">
                    <div class="panel-heading" id="medication_div_a">
                        <a onClick="return showPreDefineMedFnNew('medication_name', 'medication_detail', '10', parseInt($(this).offset().left + 8),parseInt($(this).offset().top - $(document).scrollTop()+58)),document.getElementById('selected_frame_name_id').value='iframe_medication_op_room_record_id';" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " data-placement="top"> <span class="fa fa-caret-right"></span>  Medications    </a>
                    </div>
                    <div class="panel-body">
                        <div class="inner_safety_wrap">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                   
                                   <div class="scheduler_table_Complete ">
                                       <div class="my_table_Checkall table_slider_head">
                                                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                    <thead class="cf">
                                                        <tr>
                                                            <th class="text-left col-md-5 col-lg-5 col-sm-5 col-xs-5">Name</th>
                                                            <th class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3">Dosage </th>
                                                            <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4">Sig </th>
                                                        </tr>
                                                    </thead>
                                                 </table>
                                       </div>
                                       <div class="table_slider">          
                                            <div id="iframe_medication_op_room_record">
												<?php  
                                                    $medicNameWidth=235;
                                                    $medicDetailWidth=235;
                                                    include("patient_prescription_medi_spreadsheet.php");
                                                ?>
                                            </div>     
                                         </div>                
                                      </div>

                                   
                                </div>
                                 
                            </div>	 
                         </div>
                    </div> 
                </div>
             </div>
        <?php
		if($anesStartTime) {
		$time_split2 = explode(":", $anesStartTime);
		if($time_split2[0]>12) {
			$am_pm2 = "PM";
		}else {
			$am_pm2 = "AM";
		}
		if($time_split2[0]>=13) {
			$time_split2[0] = $time_split2[0]-12;
			if(strlen($time_split2[0]) == 1) {
				$time_split2[0] = "0".$time_split2[0];
			}
		}else {
			//DO NOTHNING
		}
		$anesStarttime_ampm = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
		
		if($time_split2[0]=='00' && $time_split2[1]=='00') {
			 $anesStarttime_ampm='';
		}
		else {
			$anesStarttime_ampm = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
		}
	}	
	if($anesEndTime) {
		$time_split3 = explode(":", $anesEndTime);
		if($time_split3[0]>12) {
			$am_pm3 = "PM";
		}else {
			$am_pm3 = "AM";
		}
		if($time_split3[0]>=13) {
			$time_split3[0] = $time_split3[0]-12;
			if(strlen($time_split3[0]) == 1) {
				$time_split3[0] = "0".$time_split3[0];
			}
		}else {
			//DO NOTHNING
		}
		if($time_split3[0]=='00' && $time_split3[1]=='00') {
			 $anesEndtime_ampm='';
		}
		else {
			$anesEndtime_ampm = $time_split3[0].":".$time_split3[1]." ".$am_pm3;
		}
	}
		  
   $calcTopPosition1 = "260"; 
   $calcTopPosition2 = "200"; 

	$surgeryBckGroundColor=$chngBckGroundColor;
	if(trim($surgeryORNumber) || trim($surgeryTimeIn) || trim($surgeryStartTime) || trim($surgeryEndTime) || trim($surgeryTimeOut)){ $surgeryBckGroundColor=$whiteBckGroundColor; }
	?>
        <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                 <div class="panel panel-default new_panel bg_panel_op">
                      <div class="panel-heading">
                         <h3 class="panel-title rob"> Surgery  </h3>
                       </div>	
                      <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-center">
                                    <label> OR </label>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-center">
                                    <label> Time In </label>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-center">
                                    <label> Time Out </label>
                                </div>
                            </div>
                            <div class="clearfix margin_adjustment_only"></div>
                            <div class="row">
                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        	<div class="row">
                                            	<div class="col-md-12 col-sm-12 col-xs-6 col-lg-2">
                                                <small style="margin-top:9px;" class="full_width text-center" > Room</small>
  												</div>
                                                <div class="col-md-12 col-sm-12 col-xs-6 col-lg-10" id="room_ID">
                                                  <input type="hidden" id="bp" name="bp_hidden">
                                            			<input class="form-control"  type="text" onBlur="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onFocus="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" name="surgeryORNumber" id="bp_temp5" onKeyUp="displayText1=this.value;changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt(findPos_Y('room_ID'))-170,parseInt(findPos_X('room_ID'))+5,'flag5');changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');"  style=" <?php echo $surgeryBckGroundColor;?> " tabindex="1" value="<?php echo $surgeryORNumber;?>"/>
                                                </div>
                                            </div>
                                        		
                                        
                                          
                                        </div>
                                    </div>	
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">

                                            <div class="row">
                                            	<div class="col-md-12 col-sm-12 col-xs-6 col-lg-6">	                                            
                                                <small style="margin-top:9px;" class="full_width text-center">In Room Time</small> </div>
                                                <div class="col-md-12 col-sm-12 col-xs-6 col-lg-6" id="In_Room_Time">
                                                  <input class="form-control"  type="text" onBlur="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onFocus="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" name="surgeryTimeIn" id="bp_temp6" onKeyUp="displayText1=this.value;changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt(findPos_Y('In_Room_Time'))-170,parseInt(findPos_X('In_Room_Time'))+5,'flag6');<?php if($surgeryTimeIn=="") {?>clearVal_c();displayTimeAmPm('bp_temp6');changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7'); <?php } ?>"  style=" <?php echo $surgeryBckGroundColor;?> " tabindex="1" value="<?php echo $surgeryTimeIn;?>"/>
                                                </div>
                                            </div>
                                          
                                        </div>
                                        <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
                                        	<div class="row">
                                            	<div class="col-md-12 col-sm-12 col-xs-6 col-lg-6 padding_0">	                                        
                                            <small style="margin-top:9px;" class="full_width text-center" > Surgery Start Time </small> </div>
                                                <div class="col-md-12 col-sm-12 col-xs-6 col-lg-6" id="Surgery_Start_Time">
                                                    <input class="form-control"  type="text" onBlur="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');"  onFocus="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" name="surgeryStartTime" id="bp_temp" onKeyUp="displayText1=this.value;changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt(findPos_Y('Surgery_Start_Time'))-170,parseInt(findPos_X('Surgery_Start_Time'))+5,'flag1');<?php if($surgeryStartTime=="") {?>clearVal_c();displayTimeAmPm('bp_temp');changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7'); <?php } ?>" style=" <?php echo $surgeryBckGroundColor;?> "  value="<?php echo $surgeryStartTime;?>"  />
                                                </div>
                                            </div>
                                            
                                            
                                       
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                     <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
                                        	<div class="row">
                                            	<div class="col-md-12 col-sm-12 col-xs-6 col-lg-6">	                                        
                                         <small class="full_width text-center" style="margin-top:10px;">Surgery End Time 	</small> </div>
                                                <div class="col-md-12 col-sm-12 col-xs-6 col-lg-6"  id="Surgery_End_Time">
                                                    <input class="form-control"  type="text" onBlur="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onFocus="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" name="surgeryEndTime" id="bp_temp2" onKeyUp="displayText2=this.value;changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt(findPos_Y('Surgery_End_Time'))-170,parseInt(findPos_X('Surgery_End_Time'))+5,'flag2');<?php if($surgeryEndTime=="") {?>clearVal_c();displayTimeAmPm('bp_temp2');changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');<?php } ?>" style=" <?php echo $surgeryBckGroundColor;?> " value="<?php echo $surgeryEndTime;?>"  />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
                                        	<div class="row">
                                            	<div class="col-md-12 col-sm-12 col-xs-6 col-lg-6">	                                    
                                            <small class="full_width text-center" style="margin-top:10px;"> Out of Room </small></div>
                                                <div class="col-md-12 col-sm-12 col-xs-6 col-lg-6"  id="Out_of_Room">
                                                        <input class="form-control"  type="text" onBlur="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onFocus="changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" name="surgeryTimeOut" id="bp_temp7" onKeyUp="displayText1=this.value;changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt(findPos_Y('Out_of_Room'))-170,parseInt(findPos_X('Out_of_Room'))-35,'flag7');<?php if($surgeryTimeOut=="") {?>clearVal_c();displayTimeAmPm('bp_temp7');changeTxtGroupColor(5,'bp_temp5','bp_temp6','bp_temp','bp_temp2','bp_temp7'); <?php } ?>"  style=" <?php echo $surgeryBckGroundColor;?> " value="<?php echo $surgeryTimeOut;?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                      </div>
                 </div>
            </div>
            <!-- Time Out -->
                <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                 <div class="panel panel-default new_panel bg_panel_op">
                      <div class="panel-heading">
                      	<h3 class="panel-title rob"> Time Out  
                        </h3>
                        <div class="right_label hidden-sm done_label">
                                  <label class="text-center">  DONE   </label>		
                         </div>
                      </div>	
                      <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 col-lg-6 col-xs-6">
                                <div class="inner_safety_wrap sr-only" style="visibility:hidden;">
                                    <div class="row">
                                           <div class="col-md-3 col-sm-3 col-sm-offset-9 col-xs-3 col-xs-offset-9 col-lg-offset-9 col-lg-3 col-md-offset-9 text-center">
                                                <label>  DONE   </label>
                                            </div> 
                                    </div>	 
                                 </div>
                                <div class="inner_safety_wrap">
                                    <div class="row">
                                           <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 text-left">
                                                <label class="full_width f_size">Patient Identification Verified:</label>
                                           </div>   
                                           <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 text-left">
                                                <label class="full_width f_size"><?php echo $OpRoom_patientName;?></label>
                                            </div>                                                                               
                                    </div>	 
                                 </div>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                           <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 text-left">
                                                <label class="full_width f_size"> Site Verified:    </label>
                                           </div>   
                                           <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 text-left">
                                                <label class="full_width f_size"><?php echo $OpRoom_patientConfirmSite;?></label>
                                            </div>                                                                               
                                    </div>	 
                                 </div>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                           <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 text-left">
                                                <label class="full_width f_size"> Procedure Verified:   </label>
                                           </div>   
                                           <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 text-left">
                                                <label class="full_width f_size"><?php echo wordwrap($OpRoom_patientConfirmPrimProc,31,"<br>",1);?></label>
                                            </div>                                                                               
                                    </div>	 
                                 </div>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                           <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 text-left">
                                                <label class="full_width f_size"> Secondary Verified:   </label>
                                           </div>   
                                           <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 text-left">
                                                <label class="full_width f_size"> <?php echo wordwrap($OpRoom_patientConfirmSecProc,31,"<br>",1); ?> </label>
                                            </div>                                                                               
                                    </div>	 
                                 </div>		    
                                </div>	
                                <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm">
                                    <div class="clearfix margin_adjustment_only border-dashed"></div>
                                </div>
                                
                                <div class="col-md-6 col-sm-12 col-lg-6 col-xs-6">
                                <div class="inner_safety_wrap visible-sm">
                                    <div class="row">
                                            <div class="col-md-3 col-sm-3 col-sm-offset-9 col-xs-3 col-xs-offset-9 col-lg-offset-9 col-lg-3 col-md-offset-9 text-center">
                                                <label>DONE</label>
                                            </div> 
                                    </div>	 
                                 </div>
                                 <?php
									$ViewLoginUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
									$ViewLoginUserNameRes = imw_query($ViewLoginUserNameQry) or die(imw_error()); 
									$ViewLoginUserNameRow = imw_fetch_array($ViewLoginUserNameRes); 
									
									$loggedInUserName = trim($ViewLoginUserNameRow["lname"].", ".$ViewLoginUserNameRow["fname"]." ".$ViewLoginUserNameRow["mname"]);
									$loggedInUserType = $ViewLoginUserNameRow["user_type"];
									$loggedInSignatureOfUser = $ViewLoginUserNameRow["signature"];
								
									if($verifiedbyNurseName=="") {
										$verifiedbyNurseName = trim($OpRoomNurseName);
									}
								?>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                            <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 text-left">
                                                <div class="row">
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6">Nurse</label>
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6"><span class="colorChkBx" style=" <?php if($verifiedbyNurse) { echo $whiteBckGroundColor;}?>" ><?php echo stripslashes($verifiedbyNurseName); ?></span></label>
                                                </div>		                                                                            
                                            </div> 
                                            <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-center">
	                                            <input type="hidden" name="hidd_chbx_vbyn" id="hidd_chbx_vbyn_id" value="<?php echo $verifiedbyNurse;?>" >
												<input type="hidden" name="hidd_verifiedbyNurseName" id="hidd_verifiedbyNurseName_id" value="<?php echo stripslashes($verifiedbyNurseName);?>" >
                                                <label><input type="checkbox" onClick="setChbxValue('chbx_vbyn_id','hidd_chbx_vbyn_id');if(this.checked==true && document.getElementById('bp_temp4').value=='') {return displayTimeAmPm('bp_temp4');}changeChbxColor('chbx_vbys')"  <?php if($loggedInUserType<>"Nurse" || $loggedInUserName<>$verifiedbyNurseName) {echo "disabled"; }?>  value="Yes" id="chbx_vbyn_id" name="chbx_vbyn" <?php if($verifiedbyNurse) {echo "checked";}?>  />   </label>
                                            </div> 
                                    </div>	 
                                 </div>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                            <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 text-left">
                                                <div class="row">
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6">Surgeon</label>
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6"><span class="colorChkBx" style=" <?php if($verifiedbySurgeon) { echo $whiteBckGroundColor;}?>" ><?php echo stripslashes($OpRoomSurgeonName); ?></span></label>
                                               		<input type="hidden" name="hidd_chbx_vbys" id="hidd_chbx_vbys_id" value="<?php echo $verifiedbySurgeon;?>">
                                                </div>		                                                                            
                                            </div> 
                                            <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-center">
                                                <label><input onClick="setChbxValue('chbx_vbys_id','hidd_chbx_vbys_id');mainpageSetHiddField(this);" type="checkbox" <?php if($loggedInUserType<>"Surgeon" || $loggedInUserName<>$OpRoomSurgeonName) {echo "disabled"; }?> value="Yes" id="chbx_vbys_id" name="chbx_vbys" <?php if($verifiedbySurgeon) {echo "checked";}?>  > </label>
                                                
                                            </div> 
                                    </div>	 
                                 </div>
                                  <?php
									if($verifiedbyAnesthesiologistName=="") {//DISPLAY LOGGED-IN ANESTHESIOLOGIST
										$verifiedbyAnesthesiologistName = $OpRoomAnesthesiologistName;
									}
									if($verifiedbyAnesthesiologistName=="") {//DISPLAY ASSIGNED ANESTHESIOLOGIST
										$verifiedbyAnesthesiologistName = $OpRoom_patientConfirmAnesthesiologistName; 
									}
									
								 ?>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                            <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 text-left">
                                                <div class="row">
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6"> Anesthesia Provider</label>
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6"><span class="colorChkBx" style=" <?php if($verifiedbyAnesthesiologist) { echo $whiteBckGroundColor;}?>" ><?php echo stripslashes($verifiedbyAnesthesiologistName); ?></span></label>
                                                </div>		                                                                            
                                            </div> 
                                            <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-center">
                                                <label>  <input onClick="setChbxValue('chbx_vbya_id','hidd_chbx_vbya_id');mainpageSetAnesthesiaHiddField(this);"  type="checkbox" <?php if($loggedInUserType<>"Anesthesiologist" || $loggedInUserName<>$verifiedbyAnesthesiologistName) {echo "disabled"; }?> value="Yes" id="chbx_vbya_id" name="chbx_vbya" <?php if($verifiedbyAnesthesiologist) {echo "checked";}?>>   </label>
                                            </div> 
                                    </div>	 
                                 </div>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                            <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 text-left">
                                                <div class="row">
                                                	<input type="hidden" name="hidd_chbx_vbya" id="hidd_chbx_vbya_id" value="<?php echo $verifiedbyAnesthesiologist;?>" >
													<input type="hidden" name="hidd_verifiedbyAnesthesiologistName" id="hidd_verifiedbyAnesthesiologistName_id" value="<?php echo $verifiedbyAnesthesiologistName;?>" >
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6" > Time   </label>
                                                    <label class="col-md-6 col-sm-6 col-xs-6 col-lg-6" id="op_room_time"> <input class="form-control" type="text" name="verifiedbyNurseTime" id="bp_temp4" onKeyUp="displayText4=this.value" onClick="getShowNewPos(parseInt(findPos_Y('op_room_time'))+35,parseInt(findPos_X('op_room_time'))+15,'flag4');if(this.value=='') {return displayTimeAmPm('bp_temp4');}"  value="<?php echo $verifiedbyNurseTime;?>"> </label>
                                                </div>		                                                                            
                                            </div> 
                                            <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-center">
                                              &nbsp;
                                            </div> 
                                    </div>	 
                                 </div>
                                 <?php 
								 if($version_num > 1 && $sxPrbsChk=="1") {
								 ?>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                            <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 text-left">
                                                <div class="row">
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6">Sx Plan Sheet Reviewed (By Surgeon)</label>
                                                    <label class="f_size col-md-6 col-sm-6 col-xs-6 col-lg-6"><?php echo stripslashes($OpRoomSurgeonName); ?><span id="sx_plan_review_time_id" style="display:inline-block; padding-left:5px;"><?php if($sxPlanReviewedBySurgeon && $sxPlanReviewedBySurgeonDateTimeFormat) { echo '('.$sxPlanReviewedBySurgeonDateTimeFormat.')'; }?></span></label>
                                                    <input type="hidden" name="hidd_chbx_sx_rbys" id="hidd_chbx_sx_rbys_id" value="<?php echo $sxPlanReviewedBySurgeon;?>">
                                                    <input type="hidden" name="hidd_sxPlanRvwBySrgnDtm" id="hidd_sxPlanRvwBySrgnDtm_id" value="<?php echo $sxPlanReviewedBySurgeonDateTime;?>">
                                                    <input type="hidden" name="hidd_sxPlanRvwBySrgnDtmFormat" id="hidd_sxPlanRvwBySrgnDtmFormat_id" value="<?php echo $sxPlanReviewedBySurgeonDateTimeFormat;?>">
                                                    
                                                </div>		                                                                            
                                            </div> 
                                            <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-center">
                                                <label ><input onClick="setChbxValue('chbx_sx_rbys_id','hidd_chbx_sx_rbys_id');displayTimeReview('chbx_sx_rbys_id','sx_plan_review_time_id','hidd_sxPlanRvwBySrgnDtmFormat_id','hidd_sxPlanRvwBySrgnDtm_id');" type="checkbox" <?php if($loggedInUserType<>"Surgeon" || $loggedInUserName<>$OpRoomSurgeonName) {echo "disabled"; }?> value="Yes" id="chbx_sx_rbys_id" name="chbx_sx_rbys" <?php if($sxPlanReviewedBySurgeon) {echo "checked";}?>  > </label>
                                            </div> 
                                    </div>	 
                                 </div>
                                 <?php
								 }
								 ?>
                                 </div>
                                 <?php 
									$verifiedbyNurseTdDisplay='none';
									if($verifiedbyNurse=="Yes") { $verifiedbyNurseTdDisplay='block'; }
									
									$DiagnosisBackColor=$chngBckGroundColor;
									if($preOpDiagnosis || $postOpDiagnosis){
										 $DiagnosisBackColor=$whiteBckGroundColor; 
									}
									
									$defaultPreOpMed = '';
									if( defined("DISABLE_OPROOM_POSTOP_MED") && constant("DISABLE_OPROOM_POSTOP_MED") <> "YES" && $postOpDrops == '' && (($formStatus <> 'completed' && $formStatus <> 'not completed') || $save_manual == '0'))
									{
										$defaultPreOpMed = $objManageData->getDefault('postopdrops','name');
										$postOpDrops	 = $defaultPreOpMed;	
									}
										
									$Proc_Medic_BackColor=$chngBckGroundColor;
									if($operativeProcedures || ($postOpDrops && defined("DISABLE_OPROOM_POSTOP_MED") && constant("DISABLE_OPROOM_POSTOP_MED")!="YES")){
										 $Proc_Medic_BackColor=$whiteBckGroundColor; 
									}
									
									$opColumnDisplay = "inline-block";
									if( defined("DISABLE_OPROOM_POSTOP_MED") && constant("DISABLE_OPROOM_POSTOP_MED")=="YES") {
										$opColumnDisplay = "none;";
									}
								?>
                                <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
                                    <div class="clearfix margin_adjustment_only border-dashed"></div>
                                </div>  
                              <div class="clearfix"></div>  
                                <div class="col-md-6 col-sm-12 col-lg-6 col-xs-6">
                                    <div class="inner_safety_wrap">
                                          <div class="row">
                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-3" id="op_room_preopdiag">
                                                 <a data-placement="top" class="rob alle_link show-pop-list_g btn btn-default " onClick="return showPreDefineDiagnosisFn('perop_diag_area_id', '', 'no', parseInt($(this).offset().left),parseInt($(this).offset().top)-184),document.getElementById('selected_frame_name_id').value='';"> <span class="fa fa-caret-right"></span>  Pre-Op Diagnosis</a>
                                            </div>
                                            <div class="clearfix margin_adjustment_only visible-md"></div>
                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-9"> 
                                           		<div class="col-md-12 col-sm-6 col-xs-6 col-lg-12 padding_0">
                                               		<textarea class="form-control" id="perop_diag_area_id" onBlur="changeTxtGroupColor(2,'perop_diag_area_id','postop_diag_area_id');" onFocus="changeTxtGroupColor(2,'perop_diag_area_id','postop_diag_area_id');" onKeyUp="changeTxtGroupColor(2,'perop_diag_area_id','postop_diag_area_id');textAreaAdjust(this);" name="preOpDiagnosis"  style=" <?php echo $DiagnosisBackColor;?>" ><?php echo stripslashes($preOpDiagnosis);?></textarea></textarea>
                                               	</div>
                                            </div> 
                                         </div>
                                    </div>
                                </div>
                               <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm">
                                    <div class="clearfix margin_adjustment_only border-dashed"></div>
                                </div> 
                                <div class="col-md-6 col-sm-12 col-lg-6 col-xs-6">
                                    <div class="inner_safety_wrap">
                                          <div class="row">
                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-3" id="op_room_opproc">
                                                 <a data-placement="top" class="rob alle_link show-pop-list_me btn btn-default" onClick="return showProceduresFn('op_proced_area_id', '', 'no', parseInt($(this).offset().left - 220),parseInt($(this).offset().top)-264),document.getElementById('selected_frame_name_id').value='';"> <span class="fa fa-caret-right"></span> Operative Procedures</a>
                                            </div>
                                            <div class="clearfix margin_adjustment_only visible-md"></div>
                                           <div class="col-md-12 col-sm-6 col-xs-6 col-lg-9"> 
                                                <textarea class="form-control" id="op_proced_area_id" onBlur="changeTxtGroupColor(2,'op_proced_area_id','postop_drop_area_id');" onFocus="changeTxtGroupColor(2,'op_proced_area_id','postop_drop_area_id');" onKeyUp="changeTxtGroupColor(2,'op_proced_area_id','postop_drop_area_id');textAreaAdjust(this);" name="operativeProcedures" style=" <?php echo $Proc_Medic_BackColor;?> "><?php echo stripslashes($operativeProcedures);?></textarea>
                                            </div> 
                                         </div>
                                    </div>
                                </div>
                               <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" >
                                    <div class="clearfix margin_adjustment_only border-dashed"></div>
                                </div> 
                                  <div class="col-md-6 col-sm-12 col-lg-6 col-xs-6">
                                    <div class="inner_safety_wrap">
                                          <div class="row">
                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-3" id="op_room_post_op">
                                                 <a data-placement="top" class="rob alle_link show-pop-list_fundus btn btn-default " onClick="return showPostDefineDiagnosisFn('postop_diag_area_id', '', 'no', parseInt($(this).offset().left - 220),parseInt($(this).offset().top)-264),document.getElementById('selected_frame_name_id').value='';"> <span class="fa fa-caret-right"></span>  Post-Op Diagnosis  </a>
                                            </div>
                                            <div class="clearfix margin_adjustment_only visible-md"></div>
                             				<div class="col-md-12 col-sm-6 col-xs-6 col-lg-9"> 
                                                <textarea class="form-control" id="postop_diag_area_id" onBlur="changeTxtGroupColor(2,'perop_diag_area_id','postop_diag_area_id');" onFocus="changeTxtGroupColor(2,'perop_diag_area_id','postop_diag_area_id');" onKeyUp="changeTxtGroupColor(2,'perop_diag_area_id','postop_diag_area_id');textAreaAdjust(this);" name="postOpDiagnosis" onClick="chkTxtAreaFun('postop_diag_area_id','perop_diag_area_id');"  style=" <?php echo $DiagnosisBackColor;?>" rows="2" cols="50" tabindex="6"  ><?php echo stripslashes($postOpDiagnosis);?></textarea>
                                            </div> 
                                         </div>
                                    </div>
                                </div>
                               <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm">
                                    <div class="clearfix margin_adjustment_only border-dashed"></div>
                                </div> 
                                <div class="col-md-6 col-sm-12 col-lg-6 col-xs-6" style="display:<?php echo $opColumnDisplay;?>">
                                	<?php
										
									?>
                                    <div class="inner_safety_wrap">
                                          <div class="row">
                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-3" id="op_room_post_op_med">
                                                 <a data-placement="top" class="rob alle_link show-pop-list_preop btn btn-default " onClick="return showPostOpDropsFn('postop_drop_area_id', '', 'no', parseInt($(this).offset().left),parseInt($(this).offset().top)-184),document.getElementById('selected_frame_name_id').value='';"> <span class="fa fa-caret-right"></span>  Post-Op Orders   </a>
                                            </div>
                                            
                                            <div class="clearfix margin_adjustment_only visible-md"></div>
                                             <div class="col-md-12 col-sm-6 col-xs-6 col-lg-9"> 
                                                <textarea class="form-control" id="postop_drop_area_id" onBlur="changeTxtGroupColor(2,'op_proced_area_id','postop_drop_area_id');" onFocus="changeTxtGroupColor(2,'op_proced_area_id','postop_drop_area_id');" onKeyUp="changeTxtGroupColor(2,'op_proced_area_id','postop_drop_area_id');textAreaAdjust(this);" name="postOpDrops" style=" <?php echo $Proc_Medic_BackColor;?>"  ><?php echo stripslashes($postOpDrops);?></textarea>
                                            </div> 
                                         </div>
                                    </div>
                                </div>
                                
                                    
                            </div>
                            
                             
                                    
                            
                      </div>
                 </div>
            </div>
			<?php
								$condArray	=	array(); 
								$condArray['confirmation_id =']	=	$confirmationId ;
								$condArray['displayStatus =']		=	1 ;
								$condArray['suppChkStatus = ']		=	1 ;
								$anySupplyChecked	=	$objManageData->getRowCount('operatingroomrecords_supplies',$condArray);
								$ProductControlBackColor=$chngBckGroundColor;
								if($product_control_na || $bssValue || $Epinephrine03 || $Vancomycin01 || $Vancomycin02 || $omidria || $InfusionOtherChk || $anySupplyChecked || $OtherSuppliesUsed){
									 $ProductControlBackColor=$whiteBckGroundColor; 
								}
			?>
            <div class="clearfix"></div>
                <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                 <div class="panel panel-default new_panel bg_panel_op">
                      <div class="panel-heading">
                             <h3 class="panel-title rob">Product Control </h3>
                             <div class="right_label rob head_right_panel">
                                <label style="line-height:24px !important">
								<span class="colorChkBx" style="padding:0px;margin:0px; <?php echo $ProductControlBackColor;?>" onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');">
								<input <?php if($product_control_na=='Yes') echo 'CHECKED'; ?>  type="checkbox" value="Yes" id="product_control_na_id" name="product_control_na"></span> N/A </label> 
                             </div>
                      </div>    
                      <div class="panel-body" style="background:#fff;">

                            <div class="scheduler_table_Complete" id="p_control">
                                 <div class="my_table_Checkall">
                                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped " style="vertical-align:middle">														
                                            <caption class="text-left"> <label><span class="colorChkBx" style=" <?php echo $ProductControlBackColor;?>"   onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');"><input class="field"  type="checkbox" value="bss" id="chbx_bss_id" name="chbx_bss" <?php if($bssValue=="bss"){ echo "checked"; }?> ></span> BSS </label>  &nbsp; 
                                            <label> <span class="colorChkBx" style=" <?php echo $ProductControlBackColor;?>" onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');"><input type="checkbox" value="bssPlus" id="chbx_bssplus_id" name="chbx_bss" <?php if($bssValue=="bssPlus"){ echo "checked"; } ?> ></span> BSS Plus </label>  
                                            </caption>
                                            <tbody style="">
                                                <tr>
                                                	<th rowspan="5" class="bg_white">Added To Infusion Bottle</th>
                                                  <td class="text-left">
                                                        <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                            <label> Epinephrine 0.3ml (300mcg)	</label>	
                                                        </div>

                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                            <span class="colorChkBx" style=" <?php echo $ProductControlBackColor;?>" onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');" >
														<input type="checkbox" value="Yes" id="Epinephrine03_id"  name="Epinephrine03" <?php if($Epinephrine03=="Yes") { echo "checked";}?>  ></span>
                                                        </div>	
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left">
                                                        <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                            <label>Vancomycin 0.1 ml (10 mg)	</label>	
                                                        </div>

                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                          <span class="colorChkBx" style=" <?php echo $ProductControlBackColor;?>" onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');">
														<input type="checkbox" value="Yes" id="Vancomycin01_id"  name="Vancomycin01" <?php if($Vancomycin01=="Yes") { echo "checked";}?> ></span>
                                                        </div>	
                                                    </td>
                                                </tr>
                                                 <tr>
                                                    <td class="text-left">
                                                        <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                            <label>Vancomycin 0.2 ml (10 mg)	</label>	
                                                        </div>

                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                          <span class="colorChkBx" style=" <?php echo $ProductControlBackColor;?>" onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');">
														<input type="checkbox" value="Yes" id="Vancomycin02_id"  name="Vancomycin02" <?php if($Vancomycin02=="Yes") { echo "checked";}?>  tabindex="7" ></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left">
                                                        <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                            <label>Omidria </label>	
                                                        </div>

                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                          <span class="colorChkBx" style=" <?php echo $ProductControlBackColor;?>" onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');">
														<input type="checkbox" value="Yes" id="omidria_id"  name="omidria" <?php if($omidria=="Yes") { echo "checked";}?>  tabindex="7" ></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left height_inp_adj">
													<?php if($InfusionOtherChk=="Yes") { $infusionBottleOtherDisable="";} else { $infusionBottleOtherDisable="disabled"; }?>
                                                        <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
															<div class="row">
																<label class="col-md-5 col-sm-5 col-xs-5 col-lg-5">	Other   </label>	
																<Div class="col-md-7 col-sm-7 col-xs-7 col-lg-7">
																		<textarea class="form-control text_other" type="text" rows="1" cols="50" name="infusionBottleOther" id="add_inf_botl_area_id" <?php echo $infusionBottleOtherDisable;?> onKeyUp="textAreaAdjust(this);"/><?php echo stripslashes($infusionBottleOther);?></textarea>	
																</div>
															</div>
                                                            
														
                                                        </div>

                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                           <span class="colorChkBx" style=" <?php echo $ProductControlBackColor;?>" onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');chk_unchk_family_hist('InfusionOtherChk_id','add_inf_botl_area_id');">
														<input class="" type="checkbox" value="Yes" id="InfusionOtherChk_id"  name="InfusionOtherChk" <?php if($InfusionOtherChk=="Yes") { echo "checked";}?>  tabindex="7" ></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                    </table>
                                 </div>	
                            </div>
							<?php $supplyArr = array("X1","X2","X3","X4","X5");?>
                            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="wrap_right_inner_anesth">
                                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" id="tdSupplyId">
                                            <a data-placement="top" class="rob alle_link show-pop-list_preop btn btn-default margin_0" >Supplies</a>	  
                                        </div>
                                        
                                        <!-- Supplies Printing Starts Here -->
                                        <?PHP
												$moreAvailableSupplies	=	$objManageData->getAllRecords('predefine_suppliesused PS LEFT JOIN supply_categories SC on PS.cat_id = SC.id ',array('PS.name','SC.name as cat_name','PS.suppliesUsedId','PS.qtyChkBox'),array('PS.deleted ='=>0),'',array("PS.name='Other', PS.name "=>'Asc'));
												$moreAvailableArr	=	array() ;
												if(is_array($moreAvailableSupplies) && count($moreAvailableSupplies) > 0)
												{
													foreach($moreAvailableSupplies as $more)
													{
														if(strtolower($more->cat_name) == 'implant' && $ftpSupp == true) { 
															//IF HYBRENT CREDENTIALS ARE IN USE THEN DO NOT SHOW IMPLANT CATEGORY
															continue; 
														}
														$moreAvailableArr[$more->name]	=	$more ;
													}
												}
												
												// Code to get & list all in use supplies from operatingroomrecords_supplies table
												$condArray	=	array(); 
												$condArray['confirmation_id']	=	$confirmationId ;
												$condArray['displayStatus']		=	1 ;
												$suppliesUsed	=	$objManageData->getMultiChkArrayRecords('operatingroomrecords_supplies',$condArray,'suppName','Asc');
												
												$suppliesCounter = 0;
												
												if( is_array($suppliesUsed) && count($suppliesUsed) > 0 )
												{
													foreach($suppliesUsed as $supply)
													{	
														$suppliesCounter++; 
														$chkBoxId	=	'suppChkBox_'. $suppliesCounter ;
														$listBoxId=	'suppListBox_' . $suppliesCounter ;
														//if($moreAvailableArr[$supply->suppName]) 
															//unset($moreAvailableArr[$supply->suppName]);
														$supp_name = stripslashes($supply->suppName);
														$div_name = preg_replace('#[ -]+#', '-', strtolower($supp_name));
														$div_name = preg_replace('/[^A-Za-z0-9-]+/', '', $div_name);
										?>				
                                        					
															<div class="col-md-12 col-sm-4 col-xs-4 col-lg-4 supplies_list" style="min-height:35px;" data-supp-name="<?php echo $div_name;?>">
                                             					<div class="row">
                                                                	<div class="col-md-6 col-sm-6 col-xs-6 col-lg-7">
                                                                    	<input type="hidden" name="hidd_suppRecordId[<?=$suppliesCounter?>]" value="<?=$supply->suppRecordId?>" />
                                                                        <input type="hidden" name="hidd_suppName[<?=$suppliesCounter?>]" value="<?=stripslashes($supply->suppName)?>" />
                                                                        <input type="hidden" name="hidd_suppQtyDisplay[<?=$suppliesCounter?>]" value="<?=$supply->suppQtyDisplay?>" />
                                                                        <input type="hidden" name="hidd_predefine_supp_id[<?=$suppliesCounter?>]" value="<?=$supply->predefine_supp_id?>" />
                                                                    	<label class="f_size rob"> <?=$supp_name?></label>   
                                                                  	</div>
                                               
                                                					<div class="col-md-6 col-sm-6 col-xs-6 col-lg-5 padding_0" >
                                                                    	<div class="row">
                                                                        	<!-- CheckBox Printing Start  -->
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            	<label> 
                                                                                	<span class="colorChkBx" style=" <?php echo $ProductControlBackColor;?>" onClick="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');">
                                                                                    	<input type="checkbox" value="1" id="<?=$chkBoxId?>" class="specialCheck" onClick="javascript:chk_unchk_select('<?=$chkBoxId?>','<?=$listBoxId?>');" name="suppChkBox[<?=$suppliesCounter?>]" <?php if($supply->suppChkStatus ) { echo "checked";}?>  tabindex="7" >
                                                                                   	</span>
                                                                             	</label>
                                                                 			</div> 
                                                                        	<!-- CheckBox Printing End-->
                                                                            
                                                                            <!-- ListBox Printing Start-->
                                                                            <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                            <?php if($supply->suppQtyDisplay): ?>
                                                                            	<select class="selectpicker form-control" name="suppListBox[<?=$suppliesCounter?>]" id="<?=$listBoxId?>" <?php if( !$supply->suppChkStatus ) { echo "disabled";}?>>
                                                                                	<option value="" selected>-</option>
                                                                                    <?php for($k=0;$k<count($supplyArr);$k++) :?>
                                                                                    <option value="<?=$supplyArr[$k]?>" <?=($supply->suppList == $supplyArr[$k]  ? "selected" : '' )?>><?=$supplyArr[$k];?></option>
                                                                                    <?php endfor;?>	
                                                                            	</select>
                                                                          	<!-- ListBox Printing End-->
                                                                            <?php endif; ?>
                                                                            </div>
                                                    					</div>
                                                					</div>
                                            					</div>
                                        					</div>
                                        					
                                                            <div class="clearfix margin_adjustment_only visible-md"></div>		
                                        <?PHP			
													}
												}
										?>
                                        
                                        <div class="clearfix margin_adjustment_only" id="afterSupplies"></div>
                                        
                                        <!-- Supplies Printing Ends Here -->
                                        
                                        
                                        <div class="col-md-3 col-sm-2 col-xs-2 col-lg-3 plr_3">
                                        	<a class="margin_0 panel-title rob alle_link show-pop-trigger2 btn btn-default padding_5" style=" background:white; width:98%;" onMouseOver="this.style.background='#EEE';" onMouseOut="this.style.background='#FFF';" onClick="return showPreDefineSuppliesUsedFn('inputSupplyId', <?=$confirmationId?>, 'no', parseInt($(this).offset().left)-2, parseInt($(this).offset().top) - $('#evaluationPreDefineSuppliesUsedDiv').height() -2),document.getElementById('selected_frame_name_id').value='';"><i class="fa fa-caret-right"></i>&nbsp;Supply Used</a>  
                                          
                                          
                                       	</div>
                                        <div class="col-md-9 col-sm-10 col-xs-10 col-lg-9">
                                        	<div class="row">
                                          	<div class="col-md-10 col-sm-11 col-xs-11 col-lg-10 padding_0">
                                          		<input id="inputSupplyId" type="text" class="form-control" style="height:35px; margin-left:-2px;"  placeholder="Add More Supplies" data-counter="<?=$suppliesCounter?>" name="addMoreSupplies" onClick="return showPreDefineSuppliesUsedFn('inputSupplyId', <?=$confirmationId?>, 'no', parseInt($(this).offset().left)-2, parseInt($(this).offset().top) - $('#evaluationPreDefineSuppliesUsedDiv').height() -2),document.getElementById('selected_frame_name_id').value='';" autocomplete="off" />
                                          	</div>
                                            <div class="col-md-2 col-sm-1 col-xs-1 col-lg-2 padding_0">
                                            	<button type="button" class="btn btn-primary" id="addMoreSuppliesButton">Add</button>
                                           	</div>
                                        	</div>
                                      	</div>      
										
                                        <?php if($OtherSuppliesUsed): ?>
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                        	<div class="row">
                                            	<div class="col-md-3 col-sm-4 col-xs-4 col-lg-3">
                                                	<label class="f_size rob"> Other </label>   
                                              	</div>
                                                <div class="col-md-9 col-sm-6 col-xs-6 col-lg-9">
                                                	<input onKeyUp="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');" onBlur="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');" 
                                             		onFocus="changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');" type="text" name="OtherSuppliesUsed" id="op_room_OtherSuppliesUsed_id"  class="form-control" style=" <?php echo $ProductControlBackColor;?>" tabindex="7" value="<?php echo stripslashes($OtherSuppliesUsed);?>"  />
                                     			</div>
                                         	</div>
                                     	</div>
                                        <?php endif; ?>
                                        <div class="clearfix margin_adjustment_only visible-md"></div>
                               		</div>
                             	</div>
                            </div>
                                    
                      </div>   
                 </div>
                 <?php
							$QuesPhysBackColor=$chngBckGroundColor;
							if($pillow_under_knees || $head_rest || $safetyBeltApplied || $other_position) { $QuesPhysBackColor=$whiteBckGroundColor; }
				 ?>
                 <div class="panel panel-default new_panel bg_panel_op">
                          <div class="panel-heading">
                                 <h3 class="panel-title rob"> Patient Position </h3>
                                 
                          </div>    
                          <div class="panel-body" style="">
                                <div style="" class="inner_safety_wrap wrap_right_inner_anesth" id="">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                            <div id="sign_innern" class="inner_safety_wrap" style="">
                                                <div class="row">
                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                       <span class="rob full_width f_size overflow_span"> 
                                                        <label class="f_size rob"><span class="colorChkBx" style=" <?php echo $QuesPhysBackColor;?>" onClick="changeDiffChbxColor(4,'chbx_op_room_pillow_ukn_id','chbx_op_room_pillow_hs_id','safetyBeltApplied_id','chbx_op_room_pillow_other_id');"><input class="field"  type="checkbox" value="Yes" id="chbx_op_room_pillow_ukn_id" name="pillow_under_knees" <?php if($pillow_under_knees=="Yes") {  echo "checked";}?>  tabindex="7" ></span> Pillow Under Knee </label> &nbsp;
                                                        <label class="f_size rob"><span class="colorChkBx" style=" <?php echo $QuesPhysBackColor;?>" onClick="changeDiffChbxColor(4,'chbx_op_room_pillow_ukn_id','chbx_op_room_pillow_hs_id','safetyBeltApplied_id','chbx_op_room_pillow_other_id');"><input class="field"  type="checkbox" value="Yes" id="chbx_op_room_pillow_hs_id" name="head_rest" <?php if($head_rest=="Yes") {  echo "checked";}?>  tabindex="7" ></span>  	Head Rest	 </label> &nbsp;
                                                        <label class="f_size rob"> <span class="colorChkBx" style=" <?php echo $QuesPhysBackColor;?>" onClick="changeDiffChbxColor(4,'chbx_op_room_pillow_ukn_id','chbx_op_room_pillow_hs_id','safetyBeltApplied_id','chbx_op_room_pillow_other_id');"><input class="field"  type="checkbox" value="Yes" id="safetyBeltApplied_id" name="safetyBeltApplied" <?php if($safetyBeltApplied=="Yes") {  echo "checked";}?>  tabindex="7" ></span> Safety Belt Applied 	 </label> &nbsp;
                                                        <label class="f_size rob" ><span class="colorChkBx" style=" <?php echo $QuesPhysBackColor;?>" onClick="changeDiffChbxColor(4,'chbx_op_room_pillow_ukn_id','chbx_op_room_pillow_hs_id','safetyBeltApplied_id','chbx_op_room_pillow_other_id');disp_hide_checked_row_id('chbx_op_room_pillow_other_id','txt_field_patient_pos')"><input class="field"  type="checkbox" value="Yes" id="chbx_op_room_pillow_other_id" name="other_position" <?php if($other_position=="Yes") {  echo "checked";}?> ></span>  	Other </label> &nbsp;
													<?php if($other_position=="Yes") { $dislpay_patientpositionOther="inline-block";}else { $dislpay_patientpositionOther="none";}?>                                                    <input type="text" class="form-control collapse inline_form_text" id="txt_field_patient_pos" name="surgeryPatientPositionOther" style="  display:<?php echo $dislpay_patientpositionOther;?>; "  value="<?php echo $surgeryPatientPositionOther;?>"  />
                                                    	
                                    
                                                      </span>
                                                    </div>
                                                                                                                                          </div>
                                               
                                                                         
                                        
                                                <div class="clearfix"></div>
                                             </div>	
                                        </div>
                                 </div>
                              </div>  
                         </div>
                 
               </div>
			   <?php
					$operatingRoomRecordDetails = $objManageData->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);
					if(!$iol_ScanUpload) {	
						$iol_ScanUpload = $operatingRoomRecordDetails->iol_ScanUpload;
					}
					if(!$iol_ScanUpload2) {	
						$iol_ScanUpload2 = $operatingRoomRecordDetails->iol_ScanUpload2;
					}
					if($iol_ScanUpload || $iol_ScanUpload2){ $iol_src = "&amp;iol_ScanUpload=$iol_ScanUpload"; $iframe_iol_Height='height="200"'; $iframe_iol_width='width="400"'; }else {$iframe_iol_Height='height="0"';  }
					
					$defaultModel = '';
					if($model == '' && (($formStatus <> 'completed' && $formStatus <> 'not completed') || $save_manual == '0'))
					{
						$defaultModel = $objManageData->getDefault('model','name',"\n");
						$model	 = $defaultModel;	
					}
																			
					$IOL_BackColor=$chngBckGroundColor;
					if(($iol_na || $manufacture || $model || $Diopter || $iol_ScanUpload || $iol_ScanUpload2 )){
						 $IOL_BackColor=$whiteBckGroundColor; 
					}
					if($iol_ScanUpload || $iol_ScanUpload2) { $existIolImage='yes'; }
					
					$signNurseBackColor=$chngBckGroundColor;
					if($iol_na || $signNurseId) {  $signNurseBackColor =$whiteBckGroundColor; }
					?>
               <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                 <div class="panel panel-default bg_panel_op new_panel">
                      <div class="panel-heading">
                             <h3 class="panel-title rob">IOL </h3>
                             <div class="right_label rob head_right_panel">
                             <input type="hidden" name="hidd_anyOneImageExist" id="hidd_anyOneImageExist" value="<?php echo $existIolImage;?>" />
                                <label style="line-height:24px !important"> 
                                <span class="colorChkBx" style=" <?php echo $IOL_BackColor;?>"  onClick="changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');changeDiffChbxColor(2,'iol_na_id','nurse_show_color_id');">
                                	<input class="field" <?php if($iol_na=='Yes') echo 'CHECKED'; ?>  type="checkbox" value="Yes" id="iol_na_id" name="iol_na"></span> N/A </label> 
                             </div>
                      </div>    
                      <div class="panel-body" style="">
                            <div style="" class="inner_safety_wrap" id="">
                                <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                    <span class="rob f_size"><b> Scan/Upload: </b> IOL </span>	     	
                                </div>
                                <div class="clearfix margin_adjustment_only visible-md"></div>
                                <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                    <div class="row">
                                      <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                            <label class="rob f_size"><b> S/N </b>	 </label>	
                                        </div>

                                        <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8">
                                            <input style="display:none;" class="text_10 all_border" type="file" name="elem_iolscan"><input type="text" class="form-control" name="iol_serial_number"  id="iol_serial_number"value="<?php echo $iol_serial_number ?>" previous-data="<?php echo $iol_serial_number ?>" onBlur="validate_sn();">
                                        </div>	
                                    </div>	
                                </div>
                             </div>
                            <div class="clearfix margin_adjustment_only border-dashed"></div> 
                            <div id="below_summary_dummy" class="upload_inner collapse in full_width" style="height: auto; min-height:50px;">
                                
                              
                                 
                                 
                            </div>     		
                            <div class="clearfix"></div>
                          
                           	<iframe name="iframeIOL" id="iframeIOL" class="col-md-12 col-sm-12  col-xs-12 col-lg-12 " <?php echo $iframe_iol_Height;?> <?php echo $iframe_iol_width;?> frameborder="0" scrolling="no" src="geIolImage.php?operatingRoomRecordsId=<?php echo $operatingRoomRecordsId; ?>"></iframe>
                      </div>   
                 </div>
                 <div class="panel panel-default new_panel bg_panel_op">
                      <div class="panel-heading">
                             <h3 class="panel-title rob">IOL Manufacturer</h3>
                            
                      </div>    
                      <div class="panel-body" style="">
                            <div style="" class="inner_safety_wrap wrap_right_inner_anesth" id="">
                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                <div class="row">
                                                  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                        <label class="rob f_size"> Man 	 </label>	
                                                    </div>

                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                        <select class="selectpicker text_10" name="manufacture" onChange="changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');getLensBrand(this.value,'op_room_ajaxLensBrand.php');" id="manufacture_id" style=" width:150px;border:1px; <?php echo $IOL_BackColor;?>  " >
																	<option value="">Select</option>
														<?php
														$manQry = "SELECT `name` FROM manufacturer_lens_category ORDER BY `name`";
														$manRes = imw_query($manQry) or die(imw_error());
														$savedManExist='false';
														if(imw_num_rows($manRes)>0) {
															while($manRow = imw_fetch_array($manRes)) {
																$adminManName = $manRow['name'];
																if($manufacture==$adminManName) { $savedManExist='true';}?>
                                                        			<option value="<?php echo $adminManName;?>" <?php if($manufacture==$adminManName) { echo "selected";  }?>><?php echo $adminManName;?></option>
                                                        <?php			
															}
														}if($manufacture && $savedManExist=='false') {?>
																	<option value="<?php echo $manufacture;?>" selected><?php echo $manufacture;?></option>
														<?php		
														}?>
                                                        </select>		
                                                    </div>	
                                                </div>	
                                            </div>
                                            <div class="clearfix margin_adjustment_only visible-md"></div>
                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                <div class="row">
                                                  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                          <label class="rob f_size"> Lens Brand </label>	
                                                    </div>

                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8" id="lensBrandTD">
                                                          <select class="selectpicker text_10" name="lensBrand"  id="lensBrand" style=" width:130px;border:1px; " >
																	<option value="">Select</option>
														<?php
														
														$manLensQry = "SELECT mlb.name as lensName, mlc.name as catName FROM manufacturer_lens_brand mlb,manufacturer_lens_category mlc 
																	WHERE mlc.name='".$manufacture."' 
																	AND mlc.name!='' 
																	AND mlc.manufacturerLensCategoryId= mlb.catId
																	ORDER BY mlb.name";
														$manLensRes = imw_query($manLensQry) or die(imw_error());
														$savedLensExist='false';
														if(imw_num_rows($manLensRes)>0) {
															while($manLensRow = imw_fetch_array($manLensRes)) {
																$lensName = $manLensRow['lensName'];
																$catName = $manLensRow['catName'];
																if($lensBrand==$lensName) { $savedLensExist='true';}?>
																                                                     			<option value="<?php echo $lensName;?>" <?php if($lensBrand==$lensName) { echo "selected";  }?>><?php echo $lensName;?></option>
                                                        <?php			
															}
														}if($lensBrand && $savedLensExist=='false') {?>
																	<option value="<?php echo $lensBrand;?>" selected><?php echo $lensBrand;?></option>
														<?php		
														}
														
														?>
                                                        </select>	
                                                    </div>	
                                                </div>	
                                            </div>
                                             <div class="clearfix margin_adjustment_only"></div> 
                                             <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                             	<div class="row">
                                                  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4" id="op_room_model">
                                                        <a class="margin_0 panel-title rob alle_link show-pop-trigger2 btn btn-default " data-placement="top" data-target="webuiPopover3" onClick="return showPreDefineModelFn('textareaModelId', '', 'no', $(this).offset().left, parseInt($(this).offset().top-420)),document.getElementById('selected_frame_name_id').value='';"> <span class="fa fa-caret-right" ></span> Model </a>
                                                        <b style="margin-right:2px;margin-left:2px; color:#333;" id="tdErase" class="fa fa-eraser" title="Reset Model" onClick="javascript:document.getElementById('textareaModelId').value='';"></b>
                                                    </div>
                                                    
                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                          <textarea id="textareaModelId" onBlur="changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');" onKeyUp="changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');" onFocus="changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');" name="model" class="form-control" style=" <?php echo $IOL_BackColor;?> " readonly><?php echo stripslashes($model);?></textarea>
                                                    </div>	
                                                </div>	
                                            </div>
                                           	
                                            <div class="clearfix margin_adjustment_only visible-md"></div>
                                            
                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                <div class="row">
                                                  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                          <label class="rob f_size"> Diopter </label>	
                                                    </div>

                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
													   <input type="text" onBlur="changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');" onFocus="changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');" 
														onKeyUp="displayText3=this.value;changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');" name="Diopter" id="bp_temp3" onClick="getShowOpRoomPos(600,670,'flag3');<?php if($Diopter=="") {?>clearVal_c(); <?php } ?>"  class="form-control" style=" <?php echo $IOL_BackColor;?>" value="<?php echo $Diopter;?>"  />
                                                    </div>	
                                                </div>
                                          	</div>
                                            
                                            <div class="clearfix margin_adjustment_only"></div>
                                            
                                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                <div class="row">
                                                  <div class="col-md-4 col-sm-2 col-xs-2 col-lg-2">
                                                  	<label class="rob f_size"> IOL&nbsp;Comments </label>	
                                                 	</div>
                                                  
                                                  <div class="col-md-8 col-sm-10 col-xs-10 col-lg-10">
                                                  	<textarea id="iol_comments" name="iol_comments" class="form-control" onKeyUp="textAreaAdjust(this);"><?php echo stripslashes($iol_comments);?></textarea>
                                                  </div>	
                                                </div>	
                                            </div>
                             </div>
                         

                          </div>   
                     </div>
                     	
                        <?php
															//CODE RELATED TO NURSE SIGNATURE ON FILE
																if($loggedInUserType<>"Nurse") {
																	$loginUserName = $_SESSION['loginUserName'];
																	$callJavaFun = "return noAuthorityFunCommon('Nurse');";
																	$callJavaFunNurse1 = "return noAuthorityFunCommon('Nurse');";
																}else {
																	$loginUserId = $_SESSION["loginUserId"];
																	$callJavaFun = "document.frm_op_room.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse1');";
																	$callJavaFunNurse1 = "document.frm_op_room.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse2');";
																}
																$signOnFileStatus = "Yes";
																$TDnurseNameIdDisplay = "block";
																$TDnurseSignatureIdDisplay = "none";
																$NurseNameShow = $loggedInUserName;
																$signNurseDateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
																if($signNurseId<>0 && $signNurseId<>"") {
																	$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
																	$signOnFileStatus = $signNurseStatus;	
																	$TDnurseNameIdDisplay = "none";
																	$TDnurseSignatureIdDisplay = "block";
																	$signNurseDateTimeFormatNew = $objManageData->getFullDtTmFormat($signNurseDateTime);
																}
																//CODE TO REMOVE NURSE SIGNATURE
																	if($_SESSION["loginUserId"]==$signNurseId) {
																		$callJavaFunDel = "document.frm_op_room.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse1','delSign');";
																	}else {
																		$callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
																	}	
																//END CODE TO REMOVE NURSE SIGNATURE	
																	
															//END CODE RELATED TO NURSE SIGNATURE ON FILE
															
															//CODE RELATED TO SURGEON SIGNATURE ON FILE
																if($loggedInUserType<>"Surgeon") {
																	
																	$loginUserName = $_SESSION['loginUserName'];
																	$callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
																}else {
																	$loginUserId = $_SESSION["loginUserId"];
																	$callJavaFunSurgeon = "document.frm_op_room.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon1');";
																}					
																$surgeon1SignOnFileStatus = "Yes";
																$TDsurgeon1NameIdDisplay = "block";
																$TDsurgeon1SignatureIdDisplay = "none";
																$Surgeon1Name = $loggedInUserName;
																if($signSurgeon1Id<>0 && $signSurgeon1Id<>"") {
																	$Surgeon1Name = $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
																	$surgeon1SignOnFileStatus = $signSurgeon1Status;	
																	
																	$TDsurgeon1NameIdDisplay = "none";
																	$TDsurgeon1SignatureIdDisplay = "block";
																}
																//CODE TO REMOVE SURGEON 1 SIGNATURE	
																	if($_SESSION["loginUserId"]==$signSurgeon1Id) {
																		$callJavaFunSurgeonDel = "document.frm_op_room.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
																	}else {
																		$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
																	}
																//END CODE TO REMOVE SURGEON 1 SIGNATURE	
															//END CODE RELATED TO SURGEON SIGNATURE ON FILE
															
															//CODE RELATED TO ANESTHESIOLOGIST
																if($loggedInUserType<>"Anesthesiologist") {
																	$loginUserName = $_SESSION['loginUserName'];
																	$callJavaFunAnes = "return noAuthorityFunCommon('Anesthesiologist');";
																	$callJavaFunAnes2 = "return noAuthorityFunCommon('Anesthesiologist');";
																}else {
																	$loginUserId = $_SESSION["loginUserId"];
																	$callJavaFunAnes = "document.frm_op_room.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia1');";
																	$callJavaFunAnes2 = "document.frm_op_room.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia2');";
																	
																}
																$anesthesia1SignOnFileStatus = "Yes";
																$TDanesthesia1NameIdDisplay = "block";
																$TDanesthesia1SignatureIdDisplay = "none";
																$Anesthesia1Name = $loggedInUserName;
																
																if($signAnesthesia1Id<>0 && $signAnesthesia1Id<>"") {
																	$Anesthesia1Name = $signAnesthesia1LastName." ".$signAnesthesia1FirstName." ".$signAnesthesia1MiddleName;
																	$anesthesia1SignOnFileStatus = $signAnesthesia1Status;	
																	
																	$TDanesthesia1NameIdDisplay = "none";
																	$TDanesthesia1SignatureIdDisplay = "block";
																}
																//CODE TO REMOVE ANES 1 SIGNATURE
																	if($_SESSION["loginUserId"]==$signAnesthesia1Id) {
																		$callJavaFunAnesDel = "document.frm_op_room.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
																	}else {
																		$callJavaFunAnesDel = "alert('Only Dr. $Anesthesia1Name can remove this signature');";
																	}
																//END CODE TO REMOVE ANES 1 SIGNATURE
															//END CODE RELATED TO ANESTHESIOLOGIST
															?>
                     
                     <div class="panel panel-default new_panel bg_panel_op">
                          <div class="panel-heading">
                                 <h3 class="panel-title rob"> IOL and/or Consent Confirmed </h3>
                          </div>    
                          <div class="panel-body" style="">
                                <div style="" class="inner_safety_wrap wrap_right_inner_anesth" id="">
                                        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                               <div class="inner_safety_wrap" id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>;">
													<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signNurseBackColor?>;"onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature</a>
												</div>
												<div class="inner_safety_wrap collapse" id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>;">
													<span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b> ". $NurseNameShow; ?>  </a></span>	     
													<span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
													<span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signNurseDateTimeFormatNew; ?> <span class="fa fa-edit"></span></span></span>
												</div>
                                           </div>		
                                         <div class="clearfix margin_adjustment_only "></div>
                                         <div class="clearfix margin_adjustment_only "></div>
                                           <?php		
														$PrepSol_BackColor=$chngBckGroundColor;
														if($prep_solution_na || $Betadine || $Saline || $Alcohol || $Prcnt5Betadinegtts || $prepSolutionsOther || $proparacaine || $tetracaine || $tetravisc ){
															 $PrepSol_BackColor=$whiteBckGroundColor; 
														}
											?>	
                                           <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                    <div id="sign_innern" class="inner_safety_wrap" style="">
                                                        <span class="rob full_width f_size"> <b> Prep Solutions </b></span>	     
                                                        <span class="rob full_width f_size col-xs-12 padding_0">
                                                        		<label class="f_size col-xs-3 col-md-6 col-lg-3 padding_6">
                                                              <span class="colorChkBx" style=" <?php echo $PrepSol_BackColor;?>" onClick="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');">
                                                              <input <?php if($prep_solution_na=='Yes') echo 'CHECKED'; ?>  type="checkbox" value="Yes" id="prep_solution_na_id" name="prep_solution_na" tabindex="7"></span> N/A 	 </label>
                                                        	 	   
                                                            <label class="f_size col-xs-3 col-md-6 col-lg-3 padding_6"> <span class="colorChkBx" style=" <?php echo $PrepSol_BackColor;?>" onClick="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');">
                                <input type="checkbox" value="Yes" id="op_room_beta_id" name="Betadine" <?php if($Betadine=="Yes") { echo "checked";} ?> ></span> Betadine 10%	 </label>
                                
                                                            <label class="f_size col-xs-3 col-md-6 col-lg-3 padding_6"> <span class="colorChkBx" style=" <?php echo $PrepSol_BackColor;?>" onClick="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');">
                                <input type="checkbox" value="Yes" id="op_room_saline_id" name="Saline"  <?php if($Saline=="Yes") { echo "checked";} ?> tabindex="7" ></span> Saline	 </label>
                                                            
                                                            <label class="f_size col-xs-3 col-md-6 col-lg-3 padding_6"> <span class="colorChkBx" style=" <?php echo $PrepSol_BackColor;?>" onClick="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');">
                                <input type="checkbox" value="Yes" id="op_room_alcohal_id" name="Alcohol" <?php if($Alcohol=="Yes") { echo "checked";} ?> ></span>  Alcohol 	 </label>
                                                        	
                                                         		<label class="f_size col-xs-3 col-md-6 col-lg-3 padding_6"> <span class="colorChkBx" style=" <?php echo $PrepSol_BackColor;?>" onClick="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');">
														<input type="checkbox" value="Yes" id="op_room_gtts_id" name="Prcnt5Betadinegtts" <?php if($Prcnt5Betadinegtts=="Yes") { echo "checked";} ?>  tabindex="7" ></span> 5% Betadine gtts </label>
                                                            <!-- Proparacaine -->
                                                            <label class="f_size col-xs-3 col-md-6 col-lg-3 padding_6"> <span class="colorChkBx" style=" <?php echo $PrepSol_BackColor;?>" onClick="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');">
                                <input type="checkbox" value="Yes" id="op_room_proparacaine_id" name="proparacaine" <?php if($proparacaine=="Yes") { echo "checked";} ?>  tabindex="7" ></span> Proparacaine </label>
                                
                                                            <!-- Tetracaine -->
                                                            <label class="f_size col-xs-3 col-md-6 col-lg-3 padding_6"> <span class="colorChkBx" style=" <?php echo $PrepSol_BackColor;?>" onClick="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');">
                                <input type="checkbox" value="Yes" id="op_room_tetracaine_id" name="tetracaine" <?php if($tetracaine=="Yes") { echo "checked";} ?>  tabindex="7" ></span> Tetracaine </label>
                            
                                                            <!-- Tetravisc -->
                                                            <label class="f_size col-xs-3 col-md-6 col-lg-3 padding_6"> <span class="colorChkBx" style=" <?php echo $PrepSol_BackColor;?>" onClick="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');">
                                <input type="checkbox" value="Yes" id="op_room_tetravisc_id" name="tetravisc" <?php if($tetravisc=="Yes") { echo "checked";} ?>  tabindex="7" ></span> Tetravisc </label>
                             															
                                                          
                                                        </span>
                                                        <div class="clearfix margin_adjustment_only"></div>
                                                        <label class="f_size col-md-2 col-sm-4 col-xs-5 col-lg-1 rob padding_0">
                                                                Other	
                                                        </label>
                                                        <div class="col-md-10 col-sm-8 col-xs-7 col-lg-11">
                                                           <textarea id="op_prep_solution" onBlur="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');" onKeyUp="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');textAreaAdjust(this);" onFocus="changeDiffChbxColor(9,'prep_solution_na_id','op_room_beta_id','op_room_saline_id','op_room_alcohal_id','op_room_gtts_id','op_prep_solution','op_room_proparacaine_id','op_room_tetracaine_id','op_room_tetravisc_id');"  name="prepSolutionsOther" class="form-control" style=" <?php echo $PrepSol_BackColor;?> "><?php echo stripslashes($prepSolutionsOther);?></textarea>
                                                        </div>
                                                     </div>	
                                                </div>
                                                 <div class="clearfix margin_adjustment_only"></div> 
                                 </div>
                              </div>   
                         </div>
                    
                 
               </div>
               <div class="clearfix"></div>  
               <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                 
                   </div> 
				   <?php
				//CODE RELATED TO NURSE SIGNATURE ON FILE
					if($loggedInUserType<>"Nurse") {
						$loginUserName = $_SESSION['loginUserName'];
						$callJavaFun = "return noAuthorityFunCommon('Nurse');";
						$callJavaFunNurse1 = "return noAuthorityFunCommon('Nurse');";
					}else {
						$loginUserId = $_SESSION["loginUserId"];
						$callJavaFun = "document.frm_op_room.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse1');";
						$callJavaFunNurse1 = "document.frm_op_room.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse2');";
					}
					$signOnFileStatus = "Yes";
					$TDnurseNameIdDisplay = "block";
					$TDnurseSignatureIdDisplay = "none";
					$NurseNameShow = $loggedInUserName;
					$signNurseDateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
					if($signNurseId<>0 && $signNurseId<>"") {
						$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
						$signOnFileStatus = $signNurseStatus;	
						$TDnurseNameIdDisplay = "none";
						$TDnurseSignatureIdDisplay = "block";
						$signNurseDateTimeFormatNew = $signNurseDateTimeFormat;
						$signNurseDateTimeFormatNew = $objManageData->getFullDtTmFormat($signNurseDateTime);
					}
					//CODE TO REMOVE NURSE SIGNATURE
						if($_SESSION["loginUserId"]==$signNurseId) {
							$callJavaFunDel = "document.frm_op_room.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse1','delSign');";
						}else {
							$callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
						}	
					//END CODE TO REMOVE NURSE SIGNATURE	
						
				//END CODE RELATED TO NURSE SIGNATURE ON FILE
				
				//CODE RELATED TO SURGEON SIGNATURE ON FILE
					if($loggedInUserType<>"Surgeon") {
						
						$loginUserName = $_SESSION['loginUserName'];
						$callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
					}else {
						$loginUserId = $_SESSION["loginUserId"];
						$callJavaFunSurgeon = "document.frm_op_room.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon1');";
					}					
					$surgeon1SignOnFileStatus = "Yes";
					$TDsurgeon1NameIdDisplay = "block";
					$TDsurgeon1SignatureIdDisplay = "none";
					$Surgeon1Name = $loggedInUserName;
					if($signSurgeon1Id<>0 && $signSurgeon1Id<>"") {
						$Surgeon1Name = $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
						$surgeon1SignOnFileStatus = $signSurgeon1Status;	
						
						$TDsurgeon1NameIdDisplay = "none";
						$TDsurgeon1SignatureIdDisplay = "block";
					}
					//CODE TO REMOVE SURGEON 1 SIGNATURE	
						if($_SESSION["loginUserId"]==$signSurgeon1Id) {
							$callJavaFunSurgeonDel = "document.frm_op_room.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
						}else {
							$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
						}
					//END CODE TO REMOVE SURGEON 1 SIGNATURE	
				//END CODE RELATED TO SURGEON SIGNATURE ON FILE
				
				//CODE RELATED TO ANESTHESIOLOGIST
					if($loggedInUserType<>"Anesthesiologist") {
						$loginUserName = $_SESSION['loginUserName'];
						$callJavaFunAnes = "return noAuthorityFunCommon('Anesthesiologist');";
						$callJavaFunAnes2 = "return noAuthorityFunCommon('Anesthesiologist');";
					}else {
						$loginUserId = $_SESSION["loginUserId"];
						$callJavaFunAnes = "document.frm_op_room.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia1');";
						$callJavaFunAnes2 = "document.frm_op_room.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia2');";
						
					}
					$anesthesia1SignOnFileStatus = "Yes";
					$TDanesthesia1NameIdDisplay = "block";
					$TDanesthesia1SignatureIdDisplay = "none";
					$Anesthesia1Name = $loggedInUserName;
					
					if($signAnesthesia1Id<>0 && $signAnesthesia1Id<>"") {
						$Anesthesia1Name = $signAnesthesia1LastName." ".$signAnesthesia1FirstName." ".$signAnesthesia1MiddleName;
						$anesthesia1SignOnFileStatus = $signAnesthesia1Status;	
						
						$TDanesthesia1NameIdDisplay = "none";
						$TDanesthesia1SignatureIdDisplay = "block";
					}
					//CODE TO REMOVE ANES 1 SIGNATURE
						if($_SESSION["loginUserId"]==$signAnesthesia1Id) {
							$callJavaFunAnesDel = "document.frm_op_room.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
						}else {
							$callJavaFunAnesDel = "alert('Only Dr. $Anesthesia1Name can remove this signature');";
						}
					//END CODE TO REMOVE ANES 1 SIGNATURE
				//END CODE RELATED TO ANESTHESIOLOGIST
				?>
                   <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                     
                       </div>
                       <div class="clearfix"></div>
                       <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                         <div class="panel panel-default new_panel bg_panel_op">
                              <div class="panel-heading">
                                     <h3 class="panel-title rob">  Intra Op Inj </h3>
                                     
                              </div>    
                              <div class="panel-body" style="">
                                    <div style="" class="inner_safety_wrap wrap_right_inner_anesth" id="">
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                <div id="" class="inner_safety_wrap" style="">
                                                    <div class="row">
                                                       <div class="wrap_right_inner_anesth">    
                                                             <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-5 nowrap">
                                                                          <label for="op_room_solm_id" class="f_size rob"> 
                                                                          <input onClick="javascript:chk_unchk_family_hist('op_room_solm_id','SolumedrolListId');" type="checkbox" value="Yes" id="op_room_solm_id"  name="Solumedrol" <?php if($Solumedrol=="Yes"){ echo "checked";}?>>   
                                                                          &nbsp;Solumedrol</label>
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-7">
                                                                          <input class="form-control" type="text" name="SolumedrolList" id="SolumedrolListId"  value="<?php echo $SolumedrolList;?>" <?php if($Solumedrol=="Yes") { echo "";} else { echo "disabled";}?>>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                             <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 nowrap">
																	  <label class="f_size rob">
																	  <input type="checkbox" value="Yes" id="op_room_ancef_id" name="Ancef"  <?php if($Ancef=="Yes"){ echo "checked";}?> onClick="javascript:chk_unchk_family_hist('op_room_ancef_id','AncefListId');">&nbsp;&nbsp;Ancef</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                    <input class="form-control" type="text" name="AncefList" id="AncefListId"  value="<?php echo $AncefList;?>" <?php if($Ancef=="Yes") { echo "";} else { echo "disabled";}?>  >																		  
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-5 nowrap">
                                                                          <label class="f_size rob"> 
																		  <input onClick="javascript:chk_unchk_family_hist('op_room_Dexamethasone_id','DexamethasoneListId');" type="checkbox" value="Yes" id="op_room_Dexamethasone_id" name="Dexamethasone" <?php if($Dexamethasone=="Yes"){ echo "checked";}?>>&nbsp;&nbsp;Dexamethasone</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-7">
																	<input class="form-control" type="text" name="DexamethasoneList" id="DexamethasoneListId"  value="<?php echo $DexamethasoneList;?>" <?php if($Dexamethasone=="Yes") { echo "";} else { echo "disabled";}?>>
																	</div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                             <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 nowrap">
                                                                          <label class="f_size rob">
																		  <input  type="checkbox" value="Yes" id="op_room_Gentamicin_id" name="Gentamicin" <?php if($Gentamicin=="Yes"){ echo "checked";}?> onClick="javascript:chk_unchk_family_hist('op_room_Gentamicin_id','GentamicinListId');">&nbsp;&nbsp;Gentamicin</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                        
																	<input class="form-control" type="text" name="GentamicinList" id="GentamicinListId"  value="<?php echo $GentamicinList;?>" <?php if($Gentamicin=="Yes") { echo "";} else { echo "disabled";}?> >
																	</div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-5 nowrap">
                                                                          <label class="f_size rob"> 
																			<input  type="checkbox" value="Yes" id="op_room_Kenalog_id" name="Kenalog" <?php if($Kenalog=="Yes"){ echo "checked";}?> onClick="javascript:chk_unchk_family_hist('op_room_Kenalog_id','KenalogListId');">&nbsp;&nbsp;Kenalog</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-7">
                                                                         <input class="form-control" type="text" name="KenalogList" id="KenalogListId"  value="<?php echo $KenalogList;?>" <?php if($Kenalog=="Yes") { echo "";} else { echo "disabled";}?>>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                             <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 nowrap">
                                                                          <label class="f_size rob"> <input onClick="chk_unchk_family_hist('op_room_Depomedrol_id','DepomedrolListId');" type="checkbox" value="Yes" id="op_room_Depomedrol_id" name="Depomedrol" <?php if($Depomedrol=="Yes"){ echo "checked";}?>>&nbsp;&nbsp;Depomedrol</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                         <input class="form-control" type="text" name="DepomedrolList" id="DepomedrolListId"  value="<?php echo $DepomedrolList;?>" <?php if($Depomedrol=="Yes") { echo "";} else { echo "disabled";}?>>  
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-5 nowrap">
                                                                          <label class="f_size rob"><input type="checkbox" onClick="javascript:chk_unchk_family_hist('op_room_vanco_id','VancomycinListId');" name="Vancomycin" value="Yes" id="op_room_vanco_id" <?php if($Vancomycin=="Yes"){ echo "checked";}?>>&nbsp;&nbsp;Vancomycin</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-7">
                                                                          <input class="form-control" type="text" name="VancomycinList" id="VancomycinListId" value="<?php echo $VancomycinList;?>" <?php if($Vancomycin=="Yes") { echo "";} else { echo "disabled";}?>>
                                                                    </div>
                                                                </div>
                                                            </div>	  
                                                           	<div class="clearfix margin_small_only visible-md"></div>
                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 nowrap">
                                                                          <label class="f_size rob"><input type="checkbox" onClick="javascript:chk_unchk_family_hist('Trimaxi_id','TrimaxiListId');" name="Trimaxi" value="Yes" id="Trimaxi_id" <?php if($Trimaxi=="Yes"){ echo "checked";}?>>&nbsp;&nbsp;Tri-Moxi</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                          <input class="form-control" type="text" name="TrimaxiList" id="TrimaxiListId" value="<?php echo $TrimaxiList;?>" <?php if($Trimaxi=="Yes") { echo "";} else { echo "disabled";}?>>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-5 nowrap">
                                                                          <label class="f_size rob"><input type="checkbox" onClick="javascript:chk_unchk_family_hist('injXylocaineMPF_id','injXylocaineMPFListId');" name="injXylocaineMPF" value="Yes" id="injXylocaineMPF_id" <?php if($injXylocaineMPF=="Yes"){ echo "checked";}?>>&nbsp;&nbsp;Xylocaine MPF</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-7">
                                                                          <input class="form-control" type="text" name="injXylocaineMPFList" id="injXylocaineMPFListId" value="<?php echo $injXylocaineMPFList;?>" <?php if($injXylocaineMPF=="Yes") { echo "";} else { echo "disabled";}?>>
                                                                    </div>
                                                                </div>
                                                            </div>	  
                                                           	<div class="clearfix margin_small_only visible-md"></div>
                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 nowrap">
                                                                          <label class="f_size rob"><input type="checkbox" onClick="javascript:chk_unchk_family_hist('injMiostat_id','injMiostatListId');" name="injMiostat" value="Yes" id="injMiostat_id" <?php if($injMiostat=="Yes"){ echo "checked";}?>>&nbsp;&nbsp;Miostat</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                          <input class="form-control" type="text" name="injMiostatList" id="injMiostatListId" value="<?php echo $injMiostatList;?>" <?php if($injMiostat=="Yes") { echo "";} else { echo "disabled";}?>>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-5 ">
                                                                          <label class="f_size rob"><input type="checkbox" onClick="javascript:chk_unchk_family_hist('PhenylLido_id','PhenylLidoListId');" name="PhenylLido" value="Yes" id="PhenylLido_id" <?php if($PhenylLido=="Yes"){ echo "checked";}?>>&nbsp;&nbsp;Phenyl/Lido 1.5%/1%</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-7">
                                                                          <input class="form-control" type="text" name="PhenylLidoList" id="PhenylLidoListId" value="<?php echo $PhenylLidoList;?>" <?php if($PhenylLido=="Yes") { echo "";} else { echo "disabled";}?>>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 nowrap">
                                                                          <label class="f_size rob">Other</label>   
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                          <textarea id="postOpInjOther" name="postOpInjOther" class="form-control" onKeyUp="textAreaAdjust(this);"><?php echo stripslashes($postOpInjOther);?></textarea>  
                                                                    </div>
                                                                </div>
                                                             </div>	  
                                                                  
                                                             </div>
                                                             </div>
                                                             </div>   
                                                        
                                                          
                                                          
                                                         
                                                         </div>
                                                         <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                <div class="wrap_right_inner_anesth inner_safety_wrap">
                                                                      <span class="rob full_width f_size inline_select"> 
                                                                            <label class="f_size rob">  	Patch </label> &nbsp;
                                                                            <select name="patch" class="selectpicker form-control">
                                                                                <option value="" <?php if($patch=="") { echo "selected"; }?>>No</option>
                                                                                <option value="X1" <?php if($patch=="X1") { echo "selected"; }?>>X1</option>
                                                                                <option value="X2" <?php if($patch=="X2") { echo "selected"; }?>>X2</option>
                                                                                <option value="X3" <?php if($patch=="X3") { echo "selected"; }?>>X3</option>
                                                                            </select>
                                                                            <label class="f_size rob">&nbsp;<input type="checkbox" value="Yes" id="op_room_genta_id" name="shield" <?php if($shield=="Yes") { echo "checked"; }?> >&nbsp;Shield </label>&nbsp;
                                                                            <label class="f_size rob">	Needle/Suture count	 </label> &nbsp;
                                                                            <label class="f_size rob"> <b> Correct </b>	 </label> &nbsp;
                                                                            <label class="f_size rob"> <input onClick="javascript:checkSingle('needleSutureCountIdYes','needleSutureCount');" type="checkbox" value="Yes" name="needleSutureCount" id="needleSutureCountIdYes" <?php if($needleSutureCount=="Yes") { echo "checked"; }?>>&nbsp;&nbsp;Yes </label> &nbsp;
                                                                            <label class="f_size rob"> <input onClick="javascript:checkSingle('needleSutureCountIdNo','needleSutureCount');" type="checkbox" value="No" name="needleSutureCount" id="needleSutureCountIdNo" <?php if($needleSutureCount=="No") { echo "checked"; }?>>&nbsp;&nbsp;No </label> &nbsp;
                                                                             <label class="f_size rob"> <input type="checkbox" value="Yes" name="needleSutureCountNA" id="needleSutureCountNAId" <?php if($needleSutureCountNA=="Yes") { echo "checked"; }?>>&nbsp;&nbsp;N/A </label> 
                                                                            
                                                                          
                                                                       </span>
                                                                </div>

                                                                <?php
																if($version_num > 4)
																{
																?>
                                                                <div class="clearfix margin_adjustment_only border-dashed"></div>
                                                                <div class="inner_safety_wrap">
                                                                <?php	
                                                                  	$defaultComplications = '';
																	if($complications == '' && (($formStatus <> 'completed' && $formStatus <> 'not completed') || $save_manual == '0'))
																	{
																		$defaultComplications = $objManageData->getDefault('complications','name',"\n");
																		$complications	 = $defaultComplications;	
																	}	
																?>
                                                                	<div class="row">
                                                                        <div class="col-md-12 col-sm-6 col-xs-6 col-lg-3" id="nurse_note_div">
                                                                             <a class=" margin_0 rob alle_link show-pop-list_sle btn btn-default " data-placement="top" onClick="return showComplicationsFn('complications_area_id', '', 'no',  parseInt($(this).offset().left), parseInt($(this).offset().top)-184),document.getElementById('selected_frame_name_id').value='';">
                                                                                <span class="fa fa-caret-right"></span>  Complications  
                                                                             </a>
                                                                        </div>
                                                                        <div class="clearfix margin_adjustment_only visible-md"></div>
                                                                        <div class="col-md-12 col-sm-6 col-xs-6 col-lg-9"> 
                                                                            <textarea id="complications_area_id" name="complications" class="form-control" onKeyUp="textAreaAdjust(this);"><?php echo stripslashes($complications);?></textarea>
                                                                        </div> 
                                                                 	</div>
                                                            	</div>
                                                                <?php
																}
																?>

																<div class="clearfix margin_adjustment_only border-dashed"></div>
                                                                <div class="inner_safety_wrap">
                                                                <?php	
                                                                  	$defaultIntraOpPostOpOrder = '';
																	if($intraOpPostOpOrder == '' && (($formStatus <> 'completed' && $formStatus <> 'not completed') || $save_manual == '0'))
																	{
																		$defaultIntraOpPostOpOrder = $objManageData->getDefault('intra_op_post_op_order','name');

																		$intraOpPostOpOrder	 = $defaultIntraOpPostOpOrder;	
																	}	
																?>
                                                                    <div class="row">
                                                                        <div class="col-md-12 col-sm-6 col-xs-6 col-lg-3" id="nurse_note_div">
                                                                             <a class=" margin_0 rob alle_link show-pop-list_sle btn btn-default " data-placement="top" onClick="return showIntraOpPostOpOrderFn('intraOpPostOpOrderTxtId', '', 'no',  parseInt($(this).offset().left), parseInt($(this).offset().top)-184),document.getElementById('selected_frame_name_id').value='';">
                                                                                <span class="fa fa-caret-right"></span>  Post Op Orders  
                                                                             </a>
                                                                        </div>
                                                                        <div class="clearfix margin_adjustment_only visible-md"></div>
                                                                        <div class="col-md-12 col-sm-6 col-xs-6 col-lg-9"> 
                                                                            <textarea id="intraOpPostOpOrderTxtId" name="intraOpPostOpOrder" class="form-control" onKeyUp="textAreaAdjust(this);"><?php echo stripslashes($intraOpPostOpOrder);?></textarea>
                                                                        </div> 
                                                                     </div>

																</div>
                                                                <div class="clearfix margin_adjustment_only border-dashed"></div>
                                                                <div class="inner_safety_wrap">
                                                                <?php	
                                                                  	$defaultNurseNotes = '';
																	if($nurseNotes == '' && (($formStatus <> 'completed' && $formStatus <> 'not completed') || $save_manual == '0'))
																	{
																		$defaultNurseNotes = $objManageData->getDefault('oproomnursenotes','notes',"\n");
																		$nurseNotes	 = $defaultNurseNotes;	
																	}	
																?>
                                                                <div class="row">
                                                                    <div class="col-md-12 col-sm-6 col-xs-6 col-lg-3" id="nurse_note_div">
                                                                         <a class=" margin_0 rob alle_link show-pop-list_sle btn btn-default " data-placement="top" onClick="return showNurseNotesFn('nursenotes_area_id', '', 'no',  parseInt($(this).offset().left), parseInt($(this).offset().top)-184),document.getElementById('selected_frame_name_id').value='';">
                                                                         	<span class="fa fa-caret-right"></span>  Nurse Notes  
                                                                         </a>
                                                                    </div>
                                                                    <div class="clearfix margin_adjustment_only visible-md"></div>
                                                             		<div class="col-md-12 col-sm-6 col-xs-6 col-lg-9"> 
                                                                        <textarea id="nursenotes_area_id" name="nurseNotes" class="form-control" onKeyUp="textAreaAdjust(this);"><?php echo stripslashes($nurseNotes);?></textarea>
                                                                    </div> 
                                                                 </div>
                                                            </div>
															<?php	
															if($version_num > 2)	
															{ ?>
                                                                <div class="clearfix margin_adjustment_only border-dashed"></div>
                                                                <div class="inner_safety_wrap">
                                                                    <div class="row">
                                                                        <div class="col-md-12 col-sm-6 col-xs-6 col-lg-3" data-placement="top"> Others Present</div>
                                                                        <div class="clearfix margin_adjustment_only visible-md"></div>
                                                                        <div class="col-md-12 col-sm-6 col-xs-6 col-lg-9"> 
                                                                            <textarea id="others_present" name="others_present" class="form-control" ><?php echo stripslashes($others_present);?></textarea>
                                                                        </div> 
                                                                     </div>
                                                                </div>
															<?php
                                                            }?>
                                                         </div>
                                                    </div>
                                                </div>	
                                         </div>
                                     </div>
                                     <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                         <div class="panel panel-default new_panel bg_panel_op">
                                            <div class="panel-heading">
                                                <h3 class="panel-title rob"> Anesthesia Service</h3>
                                            </div> 
                                             	<?php
													$PatientPositionBackColor=$chngBckGroundColor;
													if($anesthesia_service || $TopicalBlock || $collagenShield) { $PatientPositionBackColor=$whiteBckGroundColor; }
												?>  
                                              <div class="panel-body">
                                                   <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 wrap_right_inner_anesth">
                                                        <label class="full_width rob f_size">
                                                           <span onClick="changeDiffChbxColor(8,'anesthesia_service_full','anesthesia_service_no','chbx_block_id','chbx_local_id','chbx_topical_id','chbx_collagen_shield_id','chbx_general_id','chbx_iv_sedation_id'); checkSingle('anesthesia_service_full','anesthesia_service');" class="colorChkBx" style=" <?php echo $PatientPositionBackColor;?>" ><input type="checkbox" value="full_anesthesia" id="anesthesia_service_full" name="anesthesia_service" <?php if($anesthesia_service=="full_anesthesia"){ echo "checked";}?>></span>&nbsp;Full Anesthesia service provided 		
                                                        </label>
                                                        <label class="full_width rob f_size">
                                                               <span onClick="changeDiffChbxColor(8,'anesthesia_service_full','anesthesia_service_no','chbx_block_id','chbx_local_id','chbx_topical_id','chbx_collagen_shield_id','chbx_general_id','chbx_iv_sedation_id');checkSingle('anesthesia_service_no','anesthesia_service')" class="colorChkBx" style=" <?php echo $PatientPositionBackColor;?>" ><input type="checkbox" value="no_anesthesia" id="anesthesia_service_no" name="anesthesia_service" <?php if($anesthesia_service=="no_anesthesia"){ echo "checked";}?> ></span>&nbsp;No Anesthesia service provided
                                                        </label>
                                                        <span class="full_width rob f_size">
                                                             <label class="rob f_size">
                                                                <span onClick="changeDiffChbxColor(8,'anesthesia_service_full','anesthesia_service_no','chbx_block_id','chbx_local_id','chbx_topical_id','chbx_collagen_shield_id','chbx_general_id','chbx_iv_sedation_id');checkSingle('chbx_block_id','TopicalBlock')" class="colorChkBx" style=" <?php echo $PatientPositionBackColor;?>" ><input class="field"  type="checkbox" value="Block" id="chbx_block_id" name="TopicalBlock" <?php if($TopicalBlock=="Block"){ echo "checked";}?> ></span>&nbsp;Block 
                                                             </label> 
                                                              <label class="rob f_size">
                                                                <span onClick="changeDiffChbxColor(8,'anesthesia_service_full','anesthesia_service_no','chbx_block_id','chbx_local_id','chbx_topical_id','chbx_collagen_shield_id','chbx_general_id','chbx_iv_sedation_id');checkSingle('chbx_local_id','TopicalBlock')" class="colorChkBx" style=" <?php echo $PatientPositionBackColor;?>" ><input class="field"  type="checkbox" value="Local" id="chbx_local_id" name="TopicalBlock" <?php if($TopicalBlock=="Local"){ echo "checked";}?>></span>&nbsp;Local 
                                                             </label> 
                                                              <label class="rob f_size">
                                                                 <span onClick="changeDiffChbxColor(8,'anesthesia_service_full','anesthesia_service_no','chbx_block_id','chbx_local_id','chbx_topical_id','chbx_collagen_shield_id','chbx_general_id','chbx_iv_sedation_id');checkSingle('chbx_topical_id','TopicalBlock')" class="colorChkBx" style=" <?php echo $PatientPositionBackColor;?>" ><input class="field"  type="checkbox" value="Topical" id="chbx_topical_id" name="TopicalBlock" <?php if($TopicalBlock=="Topical"){ echo "checked";}?>  tabindex="7" ></span>&nbsp;Topical 
                                                             </label>
                                                             <?php
															 if($version_num > 3) {
															 ?>
                                                             <label class="rob f_size">
                                                                 <span onClick="changeDiffChbxColor(8,'anesthesia_service_full','anesthesia_service_no','chbx_block_id','chbx_local_id','chbx_topical_id','chbx_collagen_shield_id','chbx_general_id','chbx_iv_sedation_id');checkSingle('chbx_general_id','TopicalBlock')" class="colorChkBx" style=" <?php echo $PatientPositionBackColor;?>" ><input class="field"  type="checkbox" value="General" id="chbx_general_id" name="TopicalBlock" <?php if($TopicalBlock=="General"){ echo "checked";}?>  tabindex="7" ></span>&nbsp;General 
                                                             </label>
                                                             <label class="rob f_size">
                                                                 <span onClick="changeDiffChbxColor(8,'anesthesia_service_full','anesthesia_service_no','chbx_block_id','chbx_local_id','chbx_topical_id','chbx_collagen_shield_id','chbx_general_id','chbx_iv_sedation_id');checkSingle('chbx_iv_sedation_id','TopicalBlock')" class="colorChkBx" style=" <?php echo $PatientPositionBackColor;?>" ><input class="field"  type="checkbox" value="IV Sedation" id="chbx_iv_sedation_id" name="TopicalBlock" <?php if($TopicalBlock=="IV Sedation"){ echo "checked";}?>  tabindex="7" ></span>&nbsp;IV Sedation 
                                                             </label>    
                                                             <?php
															 }
															 ?>
                                                        </span>
                                                        <?php
                                                        //this field is depriciated. show only in case of saved one
														if($collagenShield=="Yes"){
														?>
                                                        <label class="full_width rob f_size" data-toggle="collapse" data-target="#collagen_div">
                                                             <span onClick="changeDiffChbxColor(8,'anesthesia_service_full','anesthesia_service_no','chbx_block_id','chbx_local_id','chbx_topical_id','chbx_collagen_shield_id','chbx_general_id','chbx_iv_sedation_id');" class="colorChkBx" style=" <?php echo $PatientPositionBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_collagen_shield_id" name="chbx_collagen_shield" <?php if($collagenShield=="Yes") {echo "checked";}?>  tabindex="7" onClick="list_disp_txtarea('chbx_collagen_shield_id','TDsoakedInId','');" ></span>&nbsp;Collagen Shield
                                                        </label>
                                                       <?php 
														}
													   $collagen_div_class = "";
													   if($collagenShield=="Yes") {$collagen_div_class = "in";}?>
                                                        
                                                       <div id="collagen_div" class="collapse <?php echo $collagen_div_class; ?> full_width padding_15">
                                                                <label class="full_width rob f_size text-center"> Soaked in </label>
                                                                <div class="full_width well well-sm">
                                                                    <label class="full_width rob f_size"><input type="checkbox" value="Yes" id="Econopred_id"  name="Econopred" <?php if($Econopred=="Yes") { echo "checked";}?>  >&nbsp;Econopred </label>
                                                                    <label class="full_width rob f_size"><input type="checkbox" value="Yes" id="Zymar_id"  name="Zymar" <?php if($Zymar=="Yes") { echo "checked";}?>  >&nbsp;Zymar</label>
                                                                    <label class="full_width rob f_size"><input type="checkbox" value="Yes" id="Tobradax_id"  name="Tobradax" <?php if($Tobradax=="Yes") { echo "checked";}?>>&nbsp;Tobradax</label>
                                                                    <?php if($soakedInOtherChk=="Yes") { $soakedInOtherDisable="";} else { $soakedInOtherDisable="disabled"; }?>
                                                                    <span class="full_width rob f_size">
                                                                     <label class="rob f_size"><input type="checkbox" onClick="chk_unchk_family_hist('soakedInOtherChk_id','soakedInOtherId');" value="Yes" id="soakedInOtherChk_id"  name="soakedInOtherChk" <?php if($soakedInOtherChk=="Yes") { echo "checked";}?>  tabindex="7" />  Other</label>  
                                                                        <textarea name="soakedInOther" id="soakedInOtherId" class="form-control inline_form_text" <?php echo $soakedInOtherDisable;?> onKeyUp="textAreaAdjust(this);" ><?php echo stripslashes($soakedInOther);?></textarea>
                                                                    </span>
                                                               </div>
                                                       </div> 
                                                        
                                                   </div>
                                                   
                                                   <div id="" class="inner_safety_wrap">
                                                        <div class="well well-sm">
                                                            <div class="row">
                                                                <div class="col-md-12 col-sm-5 col-xs-4 col-lg-2">
                                                                    <label class="date_r f_size">
                                                                      Comments
                                                                    </label>
                                                                </div>
                                                                <div class="clearfix margin_small_only visible-md"></div>
                                                                <div class="col-md-12 col-sm-7 col-xs-8 col-lg-10 text-center">
                                                                    <textarea id="pre_op_nurse" name="other_remain" class="form-control" onKeyUp="textAreaAdjust(this);"><?php echo stripslashes($other_remain);?></textarea>
                                                                </div> 
                                                            </div>
                                                        </div> 
                                                     </div>
                                                        
                                              </div>
                                          </div>
                                        <div class="clearfix"></div>	  
                                        
                                        <?php if($vitalSignGridStatus) { ?>  
                                        <!-- Vital Sign Grid Starts Here -->
                                        <div class="row col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_op">
                                        <div class="scanner_win new_s">
                                         <h4>
                                            <span>Vital Signs</span>      
                                         </h4>
                                        </div>
                                    	</div>
                                        <div class="panel panel-default new_panel bg_panel_op" id="op-vital-grid" >
                                                      
                                                      <div class="panel-heading haed_p_clickable" >
                                                        <div class="  col-md-12 col-sm-12 col-xs-12 col-lg-12 rob" >
                                                              <div class=" row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                                                                  
                                                                  
                                                                  <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">Time</span>
                                                                  <span  class="col-md-8 col-lg-8 col-sm-8 col-xs-8">B/P<br>
                                                                  <span  class="col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">Systolic</span>
                                                                  <span  class="col-md-6 col-lg-6 col-sm-6 col-xs-6">Diastolic</span></span>
                                                              </div>
                                                          
                                                              <div class=" col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                                                                  
                                                                  
                                                                  <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">Pulse</span>
                                                                  <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">RR</span>
                                                                  <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">Temp<sup>O</sup> C</span>
                                                                  <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">EtCO<sub>2</sub></span>
                                                                  <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">OSat<sub>2</sub></span>
                                                              </div>
                                                              <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
                                                        </div>
                                                      </div>
                                                  
                                                      <div class="panel-body" >
                                                          <?php
                                                              $condArr		=	array();
                                                              $condArr['confirmation_id']	=	$pConfId ;
                                                              $condArr['chartName']	=	'intra_op_record_form' ;
                                                              
                                                              $gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time,gridRowId','Asc');
                                                              $gCounter	=	1;
                                                              if(is_array($gridData) && count($gridData) > 0  )
                                                              {
                                                                  foreach($gridData as $gridRow)
                                                                  {	
                          $fieldId1 = 'vitalSignGrid_'.$gCounter.'_1' ;	$fieldValue1= $objManageData->getTmFormat($gridRow->start_time);
                          $fieldId2 = 'vitalSignGrid_'.$gCounter.'_2' ;	$fieldValue2= $gridRow->systolic;
                          $fieldId3 = 'vitalSignGrid_'.$gCounter.'_3' ;	$fieldValue3= $gridRow->diastolic;
                          $fieldId4 = 'vitalSignGrid_'.$gCounter.'_4' ;	$fieldValue4= $gridRow->pulse;
                          $fieldId5 = 'vitalSignGrid_'.$gCounter.'_5' ;	$fieldValue5= $gridRow->rr;
                          $fieldId6 = 'vitalSignGrid_'.$gCounter.'_6' ;	$fieldValue6= $gridRow->temp;
                          $fieldId7 = 'vitalSignGrid_'.$gCounter.'_7' ;	$fieldValue7= $gridRow->etco2;
                          $fieldId8 = 'vitalSignGrid_'.$gCounter.'_8' ;	$fieldValue8= $gridRow->osat2;
                                                          ?>		
                              <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                <input type="hidden" name="vitalSignGridRecordId[]" value="<?=$gridRow->gridRowId?>"	/>
                                  <div class="row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                                    <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                      <input type="text" name="<?=$fieldId1?>" class="vitalSignGrid" id="<?=$fieldId1?>" value="<?=$fieldValue1?>" data-row-id = "<?=$gCounter?>?" data-record-id="<?=$gridRow->gridRowId?>" />
                                  </span>
                                      <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                        <input type="text" name="<?=$fieldId2?>" class="vitalSignGrid" id="<?=$fieldId2?>"  value="<?=$fieldValue2?>" />
                                    </span>
                                      <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                          <input type="text" name="<?=$fieldId3?>" class="vitalSignGrid" id="<?=$fieldId3?>"  value="<?=$fieldValue3?>" />
                                      </span>
                                  </div>
                                  <div class="  col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                                    <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3" >
                                        <input type="text" name="<?=$fieldId4?>" class="vitalSignGrid" id="<?=$fieldId4?>" value="<?=$fieldValue4?>"  />
                                    </span>
                                    <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                        <input type="text" name="<?=$fieldId5?>" class="vitalSignGrid" id="<?=$fieldId5?>"  value="<?=$fieldValue5?>" />
                                    </span>
                                    <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
                                        <input type="text" name="<?=$fieldId6?>" class="vitalSignGrid" id="<?=$fieldId6?>" value="<?=$fieldValue6?>"  />
                                    </span>
                                    <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                        <input type="text" name="<?=$fieldId7?>" class="vitalSignGrid" id="<?=$fieldId7?>" value="<?=$fieldValue7?>"  />
                                    </span>
                                    <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                        <input type="text" name="<?=$fieldId8?>" class="vitalSignGrid" id="<?=$fieldId8?>"  value="<?=$fieldValue8?>" />
                                    </span>
                                  </div>      
                          </div>
                              <div class="clearfix"></div>
                                  <?php
                                              $gCounter++ ;
                                          }
                                      }
                                  ?>
                                  
                                  <?php 
                                      $dataStartRow = $gCounter ;	
                                      for ($gRow = $dataStartRow; $gRow < ($dataStartRow+15) ; $gRow++)
                                      {
                                              $fieldId1 = 'vitalSignGrid_'.$gRow.'_1' ;
                                              $fieldId2 = 'vitalSignGrid_'.$gRow.'_2' ;
                                              $fieldId3 = 'vitalSignGrid_'.$gRow.'_3' ;
                                              $fieldId4 = 'vitalSignGrid_'.$gRow.'_4' ;
                                              $fieldId5 = 'vitalSignGrid_'.$gRow.'_5' ;
                                              $fieldId6 = 'vitalSignGrid_'.$gRow.'_6' ;
                                              $fieldId7 = 'vitalSignGrid_'.$gRow.'_7' ;
                                              $fieldId8 = 'vitalSignGrid_'.$gRow.'_8' ;
                                  ?>
                                          <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                            <input type="hidden" name="vitalSignGridRecordId[]" />
                                              <div class="row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                                                  <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                    <input type="text" name="<?=$fieldId1?>" class="vitalSignGrid timeBox" id="<?=$fieldId1?>" data-row-id = "<?=$gRow?>" />
                                                  </span>
                                                  <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                    <input type="text" name="<?=$fieldId2?>" class="vitalSignGrid" id="<?=$fieldId2?>"  />
                                                  </span>
                                                  <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                    <input type="text" name="<?=$fieldId3?>" class="vitalSignGrid" id="<?=$fieldId3?>"  />
                                                  </span>
                                  </div>
                                              <div class=" col-md-6 col-sm-7 col-xs-7 col-lg-7">
                                                <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
                                                    <input type="text" name="<?=$fieldId4?>" class="vitalSignGrid" id="<?=$fieldId4?>"  />
                                                </span>
                                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                    <input type="text" name="<?=$fieldId5?>" class="vitalSignGrid" id="<?=$fieldId5?>"  />
                                                </span>
                                                <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
                                                    <input type="text" name="<?=$fieldId6?>" class="vitalSignGrid" id="<?=$fieldId6?>"  />
                                                </span>
                                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                    <input type="text" name="<?=$fieldId7?>" class="vitalSignGrid" id="<?=$fieldId7?>"  />
                                                </span>
                                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                    <input type="text" name="<?=$fieldId8?>" class="vitalSignGrid" id="<?=$fieldId8?>"  />
                                                </span>
                        </div>
                                        </div>
                                          <div class="clearfix"></div>
                                                          <?php		
                                                              }
                                                          ?>
                                                          
                                                      
                                                      </div>
                                                      
                                          </div>
                                        <!-- Vital Sign Grid Ends Here -->
                                       	<?PHP } ?>   
                                          
                                          
                                          
                                          
                                      </div>
                                    <div class="clearfix"></div>
                                    <?php
									  //CODE RELATED TO SURGEON 1 SIGNATURE ON FILE AT THE BOTTOM
										if($loggedInUserType<>"Surgeon") {
											
											$loginUserName = $_SESSION['loginUserName'];
											$callJavaFunSurgeon2 = "return noAuthorityFunCommon('Surgeon');";
										}else if ($loggedInUserType=="Surgeon" && $_SESSION["loginUserId"]==$signSurgeon2Id) {
											$callJavaFunSurgeon2 = "return alreadySignOnce('Surgeon2');";
										}else {
											$loginUserId = $_SESSION["loginUserId"];
											$callJavaFunSurgeon2 = "document.frm_op_room.hiddSignatureId.value='TDsurgeon2SignatureId'; return displaySignature('TDsurgeon2NameId','TDsurgeon2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon2');";
										}					
										$surgeon2SignOnFileStatus = "Yes";
										$TDsurgeon2NameIdDisplay = "block";
										$TDsurgeon2SignatureIdDisplay = "none";
										$Surgeon2Name = $loggedInUserName;
										if($signSurgeon2Id<>0 && $signSurgeon2Id<>"") {
											$Surgeon2Name = $signSurgeon2LastName.", ".$signSurgeon2FirstName." ".$signSurgeon2MiddleName;
											$surgeon2SignOnFileStatus = $signSurgeon2Status;	
											
											$TDsurgeon2NameIdDisplay = "none";
											$TDsurgeon2SignatureIdDisplay = "block";
										}
										//CODE TO REMOVE SURGEON 2 SIGNATURE	
											if($_SESSION["loginUserId"]==$signSurgeon2Id) {
												$callJavaFunSurgeon2Del = "document.frm_op_room.hiddSignatureId.value='TDsurgeon2NameId'; return displaySignature('TDsurgeon2NameId','TDsurgeon2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon2','delSign');";
											}else {
												$callJavaFunSurgeon2Del = "alert('Only Dr. $Surgeon2Name can remove this signature');";
											}
										//END CODE TO REMOVE SURGEON 2 SIGNATURE
									//END CODE RELATED TO SURGEON 1 SIGNATURE ON FILE AT THE BOTTOM
									
									//CODE RELATED TO SURGEON 2 SIGNATURE ON FILE AT THE BOTTOM
										if($loggedInUserType<>"Surgeon") {
											
											$loginUserName = $_SESSION['loginUserName'];
											$callJavaFunSurgeon3 = "return noAuthorityFunCommon('Surgeon');";
										}else if ($loggedInUserType=="Surgeon" && $_SESSION["loginUserId"]==$signSurgeon1Id) {
											$callJavaFunSurgeon3 = "return alreadySignOnce('Surgeon1');";
										}else {
											$loginUserId = $_SESSION["loginUserId"];
											$callJavaFunSurgeon3 = "document.frm_op_room.hiddSignatureId.value='TDsurgeon3SignatureId'; return displaySignature('TDsurgeon3NameId','TDsurgeon3SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon3');";
										}					
										$surgeon3SignOnFileStatus = "Yes";
										$TDsurgeon3NameIdDisplay = "block";
										$TDsurgeon3SignatureIdDisplay = "none";
										$Surgeon3Name = $loggedInUserName;
										if($signSurgeon3Id<>0 && $signSurgeon3Id<>"") {
											$Surgeon3Name = $signSurgeon3LastName.", ".$signSurgeon3FirstName." ".$signSurgeon3MiddleName;
											$surgeon3SignOnFileStatus = $signSurgeon3Status;	
											
											$TDsurgeon3NameIdDisplay = "none";
											$TDsurgeon3SignatureIdDisplay = "block";
										}
										//CODE TO REMOVE SURGEON 3 SIGNATURE	
											if($_SESSION["loginUserId"]==$signSurgeon3Id) {
												$callJavaFunSurgeon3Del = "document.frm_op_room.hiddSignatureId.value='TDsurgeon3NameId'; return displaySignature('TDsurgeon3NameId','TDsurgeon3SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon3','delSign');";
											}else {
												$callJavaFunSurgeon3Del = "alert('Only Dr. $Surgeon3Name can remove this signature');";
											}
										//END CODE TO REMOVE SURGEON 3 SIGNATURE
									//END CODE RELATED TO SURGEON 2 SIGNATURE ON FILE AT THE BOTTOM
									
									//CODE RELATED TO ANESTHESIOLOGIST
										$anesthesia2SignOnFileStatus = "Yes";
										$TDanesthesia2NameIdDisplay = "block";
										$TDanesthesia2SignatureIdDisplay = "none";
										$Anesthesia2Name = $loggedInUserName;
										
										if($signAnesthesia2Id<>0 && $signAnesthesia2Id<>"") {
											$Anesthesia2Name = $signAnesthesia2LastName.", ".$signAnesthesia2FirstName." ".$signAnesthesia2MiddleName;
											$anesthesia2SignOnFileStatus = $signAnesthesia2Status;	
											
											$TDanesthesia2NameIdDisplay = "none";
											$TDanesthesia2SignatureIdDisplay = "block";
										}
										//CODE TO REMOVE ANES 2 SIGNATURE
											if($_SESSION["loginUserId"]==$signAnesthesia2Id) {
												$callJavaFunAnes2Del = "document.frm_op_room.hiddSignatureId.value='TDanesthesia2NameId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia2','delSign');";
											}else {
												$callJavaFunAnes2Del = "alert('Only Dr. $Anesthesia2Name can remove this signature');";
											}
										//END CODE TO REMOVE ANES 2 SIGNATURE
									//END CODE RELATED TO ANESTHESIOLOGIST
									
									//CODE RELATED TO Scrub Technician 1 SIGNATURE ON FILE AT THE BOTTOM
										if($loggedInUserType<>"Scrub Technician") {
											
											$loginUserName = $_SESSION['loginUserName'];
											$callJavaFunScrubTech1 = "return noAuthorityFunCommon('Scrub Technician');";
										}else if ($loggedInUserType=="Scrub Technician" && $_SESSION["loginUserId"]==$signScrubTech2Id) {
											$callJavaFunScrubTech1 = "return alreadySignOnce('Scrub Technician2');";
										}else {
											$loginUserId = $_SESSION["loginUserId"];
											$callJavaFunScrubTech1 = "document.frm_op_room.hiddSignatureId.value='TDscrubTech1SignatureId'; return displaySignature('TDscrubTech1NameId','TDscrubTech1SignatureId','op_room_record_ajaxSign.php','$loginUserId','ScrubTech1');";
										}					
										$scrubTech1SignOnFileStatus = "Yes";
										$TDscrubTech1NameIdDisplay = "block";
										$TDscrubTech1SignatureIdDisplay = "none";
										$ScrubTech1Name = $loggedInUserName;
										if($signScrubTech1Id<>0 && $signScrubTech1Id<>"") {
											$ScrubTech1Name = $signScrubTech1LastName.", ".$signScrubTech1FirstName." ".$signScrubTech1MiddleName;
											$scrubTech1SignOnFileStatus = $signScrubTech1Status;	
											
											$TDscrubTech1NameIdDisplay = "none";
											$TDscrubTech1SignatureIdDisplay = "block";
										}
									//END CODE RELATED TO Scrub Technician 1 SIGNATURE ON FILE AT THE BOTTOM
									
									//CODE RELATED TO Scrub Technician 2 SIGNATURE ON FILE AT THE BOTTOM
										if($loggedInUserType<>"Scrub Technician") {
											
											$loginUserName = $_SESSION['loginUserName'];
											$callJavaFunScrubTech2 = "return noAuthorityFunCommon('Scrub Technician');";
						
										}else if ($loggedInUserType=="Scrub Technician" && $_SESSION["loginUserId"]==$signScrubTech1Id) {
											$callJavaFunScrubTech2 = "return alreadySignOnce('Scrub Technician1');";
										}else {
											$loginUserId = $_SESSION["loginUserId"];
											$callJavaFunScrubTech2 = "document.frm_op_room.hiddSignatureId.value='TDscrubTech2SignatureId'; return displaySignature('TDscrubTech2NameId','TDscrubTech2SignatureId','op_room_record_ajaxSign.php','$loginUserId','ScrubTech2');";
										}					
										$scrubTech2SignOnFileStatus = "Yes";
										$TDscrubTech2NameIdDisplay = "block";
										$TDscrubTech2SignatureIdDisplay = "none";
										$ScrubTech2Name = $loggedInUserName;
										if($signScrubTech2Id<>0 && $signScrubTech2Id<>"") {
											$ScrubTech2Name = $signScrubTech2LastName.", ".$signScrubTech2FirstName." ".$signScrubTech2MiddleName;
											$scrubTech2SignOnFileStatus = $signScrubTech2Status;	
											
											$TDscrubTech2NameIdDisplay = "none";
											$TDscrubTech2SignatureIdDisplay = "block";
										}
									//END CODE RELATED TO Scrub Technician 2 SIGNATURE ON FILE AT THE BOTTOM
									
									//CODE RELATED TO CIRCULATING NURSE SIGNATURE ON FILE AT THE BOTTOM	
										$nurse1SignOnFileStatus = "Yes";
										$TDnurse1NameIdDisplay = "block";
										$TDnurse1SignatureIdDisplay = "none";
										$Nurse1NameShow = $loggedInUserName;
										//$signNurse1DateTimeFormatNew = date("m-d-Y h:i A");
										$signNurse1DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
										if($signNurse1Id<>0 && $signNurse1Id<>"") {
											$Nurse1NameShow = $signNurse1LastName.", ".$signNurse1FirstName." ".$signNurse1MiddleName;
											$nurse1SignOnFileStatus = $signNurse1Status;	
											$TDnurse1NameIdDisplay = "none";
											$TDnurse1SignatureIdDisplay = "block";
											//$signNurse1DateTimeFormatNew = $signNurse1DateTimeFormat;
											$signNurse1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signNurse1DateTime);
										}
										//CODE TO REMOVE NURSE SIGNATURE
											if($_SESSION["loginUserId"]==$signNurse1Id) {
												$callJavaFunNurse1Del = "document.frm_op_room.hiddSignatureId.value='TDnurse1NameId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse2','delSign');";
											}else {
												$callJavaFunNurse1Del = "alert('Only $Nurse1NameShow can remove this signature');";
											}	
										//END CODE TO REMOVE NURSE SIGNATURE
									//END CODE RELATED TO CIRCULATING NURSE SIGNATURE ON FILE AT THE BOTTOM
											
											$nurseSignBackColor1=$chngBckGroundColor;
											if($signNurse1Id!=0){
												$nurseSignBackColor1=$whiteBckGroundColor; 
											}
											$Scrub_BackColor=$chngBckGroundColor;
											if($scrubTechId1 || $scrubTechId2){
												 $Scrub_BackColor=$whiteBckGroundColor; 
											}
											
									?>		
										
                                    <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                         <div class="panel panel-default new_panel bg_panel_op">
                                              <div class="panel-heading" style="text-align:right">
                                                     <h3 class="panel-title rob">Electronically Signed </h3>
                                              </div>    
                                              <div class="panel-body" style="">
                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 select_adjust">
                                                        <Div class="row">
                                                            <div class="col-md-4 col-sm-4 col-xs-4">
                                                           	<span class="rob f_size"> <b> Scrub Tech 1 </b>  </span> 
                                                            </div>	
                                                            <div class="col-md-4 col-sm-4 col-xs-4 paddingLR_6 " style="<?php echo $Scrub_BackColor;?>" >
                                                            	<select name="scrub_techList" id="scrub_techList_id" class="selectpicker select-mandatory " onChange=" javascript:disp_hide_id('scrub_techList_id','scrubTechOther1_id');changeSelectpickerColor('.select-mandatory');">
                                                                <option value="">Select</option>
                                                                <?php
                                  $scrubTechstr="";
                                  $scrubTecharr=array();
                                  $scrubNameQry = "select * from `users` where user_type ='Nurse' OR user_type ='Scrub Technician' ORDER BY lname";
                                  $scrubNameRes = imw_query($scrubNameQry) or die(imw_error()); 
                                  while($scrubNameRow = imw_fetch_array($scrubNameRes)) {
                                    $scrubTechID = $scrubNameRow["usersId"];
                                    $scrubName = $scrubNameRow["lname"].", ".$scrubNameRow["fname"]." ".$scrubNameRow["mname"];
                                    $scrubsign = $scrubNameRow["signature"];
                                    $scrubisSigned = (!empty($scrubsign))?"Yes":"No";
                                    $scrubTechstr .= "\"".$scrubTechID."\":\"".$scrubisSigned."\",";
                                    $scrubTecharr[$scrubTechID] = $scrubisSigned;
                                    if($scrubNameRow["deleteStatus"]<>'Yes' || $scrubTechId1==$scrubTechID) {
                                ?>
                                                                <option value="<?php echo $scrubTechID;?>" <?php if($scrubTechId1==$scrubTechID) { echo "selected"; }?>><?php echo $scrubName;?></option>
                                                                <?php
                                                                    }
                                                                  }
                                                                ?>
                                                                <option value="other" <?php if($scrubTechId1=="other") { echo "selected"; }?>>Other</option>
                                                              </select> 
                                                            </div>
                                                            	
                                                            <div class="col-md-4 col-sm-4 col-xs-4">
                                                                <?php if($scrubTechId1=="other") { $display_scrubTechOther1_id="display"; } else { $display_scrubTechOther1_id="none";}?>
                                            <input type="text" name="scrubTechOther1" id="scrubTechOther1_id"   class="form-control" style=" display:<?php echo $display_scrubTechOther1_id;?>;height:27px;" tabindex="1" value="<?php echo $scrubTechOther1;?>"  />
                                                            </div>	
                                                        </Div>
                                                    </div>
                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 select_adjust">
                                                        <div class="row">
                                                            <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 ">
                                                           		<span class="rob f_size"> 
                                                           			<b>Nurse</b>
                                                          		</span>
                                                           	</div>
                                                            <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 ">      
                                                                <select name="nurseTitle" class="selectpicker" title="Nurse Title" data-width="50px">
                                                                	<option value="Supervising Nurse" <?=(($NurseTitle == 'Supervising Nurse') ? 'selected' : '')?> >Supervising </option>
                                                                    <option value="Circulating Nurse" <?=(($NurseTitle == 'Circulating Nurse' || $NurseTitle == '') ? 'selected' : '')?> >Circulating </option>
                                                                    <option value="Relief Nurse" <?=($NurseTitle == 'Relief Nurse' ? 'selected' : '')?> >Relief </option>
                                                              	</select>
                                                          	</div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 paddingLR_6">
                                                             <select name="nurseList" class="selectpicker"  onChange="checkNurse(this)"> <!-- onChange="checkNurse(this)"-->
																<option value="">Select</option>
																<?php
																	$Nursestr="";
																	$Nursearr=array();
																	$NurseNameQry = "select * from `users` where user_type ='Nurse' ORDER BY lname";
																	$NurseNameRes = imw_query($NurseNameQry) or die(imw_error()); 
																	while($NurseNameRow = imw_fetch_array($NurseNameRes)) {
																		$getNurseID = $NurseNameRow["usersId"];
																		$NurseName = $NurseNameRow["lname"].", ".$NurseNameRow["fname"]." ".$NurseNameRow["mname"];
																		$Nursesign = $NurseNameRow["signature"];
																		$NurseisSigned = (!empty($Nursesign))?"Yes":"No";
																		$Nursestr .= "\"".	$getNurseID."\":\"".  $NurseisSigned."\",";
																		$Nursearr[$getNurseID] = $NurseisSigned;
																		if($NurseNameRow["deleteStatus"]<>'Yes' || $NurseId==$getNurseID) {
																?>
																			<option value="<?php echo $getNurseID;?>" <?php if($NurseId==$getNurseID) { echo "selected"; }?>><?php echo $NurseName;?></option>
																<?php
																		}
																	}
																?>
															</select>
															<?php
																
															foreach($Nursearr as $id1 => $value1)
															{	
																echo "<script>NursearrSign['$id1']='$value1';</script>";
															}?> 
                                                            </div>	
                                                            <div class="col-md-4 col-sm-4 col-xs-4" style="text-align:right">
                                                                      <span id="NursearrSign" class="rob f_size" style="margin-right:20px;"><?php echo $signatureOfRelievedbyNurseTemp; ?></span>
                                                            </div>	
                                                        </Div>
                                                    </div>
                                                    <div class="clearfix margin_adjustment_only"></div>
                                                    <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        
                                                        <?php if($verifiedbySurgeon=='Yes') { ?>
                                                        <div class="inner_safety_wrap">
                                                            <span class="rob full_width"><span class="sign_link"> <?php echo "<b>Surgeon:</b>". " Dr. ". $OpRoomSurgeonName; ?></span></span>	     
                                                            <span class="rob full_width"> <b> Electronically Signed</b>&nbsp;<?php echo $surgeon1SignOnFileStatus;?></span>
                                                            <span class="rob full_width"> <b> Signature Date</b>&nbsp;<?php echo $OpRoom_patientConfirmDos.' '.$verifiedbyNurseTime;?></span>
                                                        </div>
                                                        <?php }else{ ?>
                                                        	<div class="inner_safety_wrap" id="TDsurgeon1NameId">
                                                            	Surgeon Signature 
                                                        	</div>
                                                        <?php } ?>
                                                   </div>        
                                                            
                                                    
                                              </div>   
                                         </div>
                                       </div>
                                       
                                       <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                         <div class="panel panel-default new_panel bg_panel_op">
                                              <div class="panel-heading" >
                                                     <h3 class="panel-title rob" >Electronically Signed </h3>
                                              </div>    
                                              <div class="panel-body" style="">
                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 select_adjust">
                                                        <div class="row">
                                                        <div class="full_width ">
															<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
																<div class="inner_safety_wrap" id="TDnurse1NameId" style="display:<?php echo $TDnurse1NameIdDisplay;?>;">
																	<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBackColor1?>;" onClick="javascript:<?php echo $callJavaFunNurse1;?>"> Nurse Signature </a>
																</div>
																<div class="inner_safety_wrap collapse" id="TDnurse1SignatureId" style="display:<?php echo $TDnurse1SignatureIdDisplay;?>;">
																	<span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunNurse1Del;?>"> <?php echo "<b>Nurse:</b> ". $Nurse1NameShow; ?>  </a></span>	     
																	<span class="rob full_width"> <b> Electronically Signed </b> <?php echo $nurse1SignOnFileStatus;?></span>
																	<span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signNurse1DateTimeFormatNew; ?> <span class="fa fa-edit"></span></span></span>
																</div>
															</div>
                                                         </div> 	
                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-2">
                                                           <span class="rob f_size"> <b> Scrub Tech 2 </b>  </span> 
                                                            </div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 paddingLR_6" style="<?php echo $Scrub_BackColor;?>">
                                                            <select name="scrub_techList1" id="scrub_techList1_id" class=" selectpicker select-mandatory " style="  <?php echo $Scrub_BackColor;?>" onChange="javascript:disp_hide_id('scrub_techList1_id','scrubTechOther2_id'); changeSelectpickerColor('.select-mandatory');" ><!-- onChange="checkscrubSign1(this)" -->
																<option value="">Select</option>
																<?php
																	$scrubTechstr1="";
																	$scrubTecharr1=array();
																	$scrubNameQry1 = "select * from `users` where user_type ='Nurse' OR user_type ='Scrub Technician' ORDER BY lname";
																	$scrubNameRes1 = imw_query($scrubNameQry1) or die(imw_error()); 
																	while($scrubNameRow1 = imw_fetch_array($scrubNameRes1)) {
																		$scrubTechID1 = $scrubNameRow1["usersId"];
																		$scrubName1 = $scrubNameRow1["lname"].", ".$scrubNameRow1["fname"]." ".$scrubNameRow1["mname"];
																		$scrubsign1 = $scrubNameRow1["signature"];
																		$scrubisSigned1 = (!empty($scrubsign1))?"Yes":"No";

																		$scrubTechstr1 .= "\"".$scrubTechID1."\":\"".$scrubisSigned1."\",";
																		$scrubTecharr1[$scrubTechID1] = $scrubisSigned1;
																		if($scrubNameRow1["deleteStatus"]<>'Yes' || $scrubTechId2==$scrubTechID1) {
																?>
																			<option value="<?php echo $scrubTechID1;?>" <?php if($scrubTechId2==$scrubTechID1) { echo "selected"; }?>><?php echo $scrubName1;?></option>
																<?php
																		}
																	}


																?>
																<option value="other"  <?php if($scrubTechId2=="other") { echo "selected"; }?>>Other</option>
															</select>
                                                            </div>	
                                                            <div class="col-md-4 col-sm-4 col-xs-4">
                                                                <?php if($scrubTechId2=="other") { $display_scrubTechOther2_id="display"; } else { $display_scrubTechOther2_id="none";}?>
											<input type="text" name="scrubTechOther2" id="scrubTechOther2_id"   class="form-control" style=" display:<?php echo $display_scrubTechOther2_id;?>;height:27px;" tabindex="1" value="<?php echo $scrubTechOther2;?>">
                                                            </div>	
                                                        </Div>
                                                    </div>
                                              </div>   
                                         </div>
                                       </div>
                                            
                                  </div>
</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="op_room_record.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "op_room_record.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM

if($finalizeStatus!='true'){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	
	</script>
	<?php
}else{
	?>
	<script>
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'none';
		
	</script>
	<?php
}
if($SaveForm_alert == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}

?>	
<script>
	function opRoomScanWinOpn(pageName,pConfirmId,scanIOL,IOLScan){
		var SW	=	window.screen.width ;
		var SH	=	window.screen.height;
		
		var	W	=	( SW > 1200 ) ?  1200	: SW ;
		var	H	=	W * 0.65
		window.open(pageName+'?pConfirmId='+pConfirmId+'&scanIOL='+scanIOL+'&IOLScan='+IOLScan,'scanWin','width='+W+',height='+H+',location=yes,status=yes');	
	}

	function changeImgSize(){
			var target = 100;
			var imgHeight = top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgThumbNail').height;
			var imgWidth = top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgThumbNail').width;
			
			if((imgHeight>=200) || (imgWidth>=200)){
				if(imgWidth > imgHeight){ 
					percentage = (target/imgWidth); 
				}else{ 
					percentage = (target/imgHeight);
				} 
				widthNew = imgWidth*percentage; 
				heightNew = imgHeight*percentage; 	
				top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgThumbNail').height = heightNew;
				top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgThumbNail').width = widthNew;
			}
		}
		
		//START THIS FUNCTION FOR SECOND IMAGE
		function changeImgSize2(){
			var target2 = 100;
			var imgHeight2 = document.getElementById('imgThumbNail2').height;
			var imgWidth2 = document.getElementById('imgThumbNail2').width;
			if((imgHeight2>=200) || (imgWidth2>=200)){
				if(imgWidth2 > imgHeight2){ 
					percentage2 = (target2/imgWidth2); 
				}else{ 
					percentage2 = (target2/imgHeight2);
				} 
				widthNew2 = imgWidth2*percentage2; 
				heightNew2 = imgHeight2*percentage2; 	
				document.getElementById('imgThumbNail2').height = heightNew2;
				document.getElementById('imgThumbNail2').width = widthNew2;	
			}
			
			
		}
		//END THIS FUNCTION FOR SECOND IMAGE
		
	// IMAGE THUMBNAIL
	if(document.getElementById('imgThumbNail')){

		setTimeout('changeImgSize()', 100);
	}
	if(document.getElementById('imgThumbNail2')){
		setTimeout('changeImgSize2()', 100);
	}
</script>
<form action="uploadIOLImage.php" name="frm_uploadIOLImage" enctype="multipart/form-data" method="post" onSubmit="changeDiffChbxColor(5,'manufacture_id','textareaModelId','bp_temp3','upload_image_id','iol_na_id');AIM.submit(this, {'onStart' : startCallback, 'onComplete' : completeCallback});">
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" id="below_summary" style="width:500px; height:50px;position:absolute; ">
        <div class="" style="">
            
            <div class="clearfix margin_adjustment_only "></div> 
            <div id="below_summary" class="upload_inner collapse in full_width" style="height: auto;">
                 <div class="col-md-12 col-sm-12  col-xs-12 col-lg-12">
                    <div class="inline_btns">
                        <input type="file" class="form-control" style=" <?php  echo $IOL_BackColor;?> " id="upload_image_id" name="uploadImage">
                    </div>
                    <div class="">
                       &nbsp;&nbsp;<input type="submit" name="uploadBtn" id="uploadBtn" value="Upload" class="btn btn-primary" />&nbsp;<a class="btn btn-primary" href="javascript:void(0)" onClick="javascript: if(confirm('Please Verify Lens')) { opRoomScanWinOpn('admin/scanPopUp.php','<?php echo $_REQUEST["pConfId"]; ?>','<?php echo $operatingRoomRecordsId; ?>','true'); document.frm_op_room.scan.value = 'true'; }"> Scan	</a>
                        
                    </div>
                 </div>
            </div>     		
         </div>
        <input type="hidden" name="pConfId" value="<?php echo $_REQUEST["pConfId"]; ?>" />
        <input type="hidden" name="operatingRoomRecordsId" value="<?php echo $operatingRoomRecordsId; ?>" />	
        <input type="hidden" name="hidd_delImage" id="hidd_delImage" value="" />
    </div>
</form>
<?php
if($finalizeStatus!='true'){
	include('privilege_buttons.php');
}?>
<?php
	include_once("common/preDefineSuppliesUsed.php");
?>

<script>
/*function addslashes( str ) {
    return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
}*/
function cslug(Text){
	return Text.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
}
function remove_supply(_this){
	var did = $(_this).attr('data-supp-div');
	$('div[data-supp-name="'+did+'"]').remove();
	
	var li = $('#evaluationPreDefineSuppliesUsedDiv').find('li[data-supp-slug="'+did+'"]');
	var Cn = li.attr('data-cat-name');
	var Sn = li.attr('data-supp-name');
	var Ch = li.attr('data-qcb');
	var Si = li.attr('data-supp-id');
	var onClick = "return getInnerHTMLsuppliesUsed('"+Sn+"','"+Ch+"','"+Cn+"','"+Si+"')";
	li.removeClass('pop-disabled').attr('onClick',onClick);
}
		
$(function(){
		var disable_click = function ()
		{
			$('div.supplies_list').each(function(){
				var did = $(this).attr('data-supp-name');
				$('#evaluationPreDefineSuppliesUsedDiv').find('li[data-supp-slug="'+did+'"]').addClass('pop-disabled').attr('onClick','javascript:void(0)');
			});
		}
		disable_click();
		
		$('body').on('click','#addMoreSuppliesButton',function(){
				
				var $this	=	$("#inputSupplyId");
				var Ival	=	$this.val();
				if( !Ival) return false;
				//var Cn  = $this.attr('data-cat-name');
				//var Cs	=	cslug(Cn);
				var Ns  = cslug(Ival);      
				var Dc	=	parseInt($this.attr('data-counter')) + 1 ; 				//	 Data Counter
				var did	=	Ns;
				var Qcb	=	parseInt($this.attr('data-qcb'))							//  Quantity Check Box Status ;
				Qcb			=	(Qcb === 1 || Qcb === 0)  ? Qcb	:	1;
				
				var Predefinesuppid	=	parseInt($this.attr('data-supp-id'))							//  Supply Id ;
				
				if( $('div[data-supp-name="'+did+'"]').length > 0)
				{
					$this.val('');
					return false;
				}
				var row	=	'';
				{
					var Pdef	=	$('#evaluationPreDefineSuppliesUsedDiv').find('div[data-supp-name="'+Ival+'"]')
					if(Pdef.length > 0 )
					{
							row	=	$('#evaluationPreDefineSuppliesUsedDiv').find('div[data-supp-name="'+Ival+'"]').eq(0);
							Qcb	=	parseInt(row.attr('data-qcb'));
					}
				}
				
				var Nam		=	Ival;
				var Id		=	'suppUniqueId';
				var chkId	=	'suppChkBox_'+Dc ;
				var listId=	'suppListBox_' +Dc ;
				
				var Html	=	printSuppliesHtml(Dc,Id,Nam,Qcb,chkId,listId,did,Predefinesuppid);
				
				//if(row)		row.remove();
				$("#afterSupplies").before(Html); 
				$this.attr('data-counter',Dc).val('');
				$('#evaluationPreDefineSuppliesUsedDiv').find('li[data-supp-slug="'+did+'"]').addClass('pop-disabled').attr('onClick','javascript:void(0)');
				changeDiffChbxColorNew(10,'product_control_na_id','chbx_bss_id','chbx_bssplus_id','Epinephrine03_id','Vancomycin01_id','Vancomycin02_id','InfusionOtherChk_id','.specialCheck','op_room_OtherSuppliesUsed_id','omidria_id');
				chk_unchk_select(''+chkId+'',''+listId+'');
				//if($("#childDiv").find('div').length == 0 ) $("#evaluationPreDefineSuppliesUsedDiv").remove();
		});
		
		var printSuppliesHtml	=	function(Dc,Id,Nam,Qcb,chkId,listId,did,Predefinesuppid)
		{
				var Html	=	'';
				Html		+=	'<div class="col-md-12 col-sm-4 col-xs-4 col-lg-4 supplies_list show-close" style="min-height:35px;" data-supp-name="'+did+'" >';
				Html		+=	'<div class="row">';
				
				Html		+=	'<div class="col-md-6 col-sm-6 col-xs-6 col-lg-7 nowrap">';
				Html 		+=	'<div data-supp-div="'+did+'" class="pull-left" style="padding-top:3px; cursor:pointer;" onClick="remove_supply(this);" title="Delete"><i class="fa fa-times-circle" style="color:red;" aria-hidden="true"></i></div>&nbsp;';
				Html		+=	'<input type="hidden" name="hidd_suppRecordId['+Dc+']" value="' + Id + '" />';
				Html		+=	'<input type="hidden" name="hidd_suppName['+Dc+']" value="' + Nam + '" />';
				Html		+=	'<input type="hidden" name="hidd_suppQtyDisplay['+Dc+']" value="'+Qcb+'" />';
				Html		+=	'<input type="hidden" name="hidd_predefine_supp_id['+Dc+']" value="'+Predefinesuppid+'" />';
				Html		+=	'<label class="f_size rob">' + Nam + '</label>';
				Html		+=	'</div>';
                                               
                Html		+=	'<div class="col-md-6 col-sm-6 col-xs-6 col-lg-5 padding_0">';
				Html		+=	'<div class="row">';
				//CheckBox Printing Start 
				Html		+=	'<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">';
				Html		+=	'<label> <span class="colorChkBx" style="" onClick="changeDiffChbxColorNew(10,\'product_control_na_id\',\'chbx_bss_id\',\'chbx_bssplus_id\',\'Epinephrine03_id\',\'Vancomycin01_id\',\'Vancomycin02_id\',\'InfusionOtherChk_id\',\'.specialCheck\',\'op_room_OtherSuppliesUsed_id\',\'omidria_id\');">';
				Html		+=	'<input type="checkbox" value="1" id="'+ chkId + '"  class="specialCheck" onClick="javascript:chk_unchk_select(\''+chkId+'\',\''+listId+'\');" name="suppChkBox['+Dc+']" tabindex="7" checked />';
				Html		+=	'</span></label>';
				Html		+=	'</div>';
				// CheckBox Printing End
				
				
                                                                  
				if(Qcb)
				{	         
					//ListBox Printing Start
					Html		+=	'<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">';
					Html		+=	'<select class="selectpicker form-control" name="suppListBox['+Dc+']" id="'+listId+'" disabled>';
					Html		+=	'<option value="" selected>-</option>';
					for(var k=1; k <= 5; k++) 
					{	
						Html		+=	'<option value="X'+k+'" >X'+k+'</option>';
					}
					Html		+=	'</select>';
					Html		+=	'</div>';
					// ListBox Printing End
				}
			
				Html		+=	'</div>';
				Html		+=	'</div>';
				Html		+=	'</div>';
				Html		+=	'</div>';
				Html		+=	'<div class="clearfix margin_adjustment_only visible-md"></div>'	;
			
			return Html; 
			
		}
});


var attachedIdPos;
if(document.getElementById('opRoomIolScanBtnId')) {
	attachedIdPos =parseInt(findPos_Y('opRoomIolScanBtnId'));
}
if(document.getElementById('opRoomIolUploadImageDivId')) {
	document.getElementById('opRoomIolUploadImageDivId').style.top=attachedIdPos+'px';
}	
if(document.getElementById('opRoomIolUploadBtnId')) {
	document.getElementById('opRoomIolUploadBtnId').style.top=attachedIdPos+'px';
}	
function restPos() {
    var p = $("#below_summary_dummy");
	var offset = p.offset();
	$("#below_summary_dummy").html("");
	$("#below_summary").css({"left":+offset.left,"top":+offset.top});

}
restPos();
//


</script>
<?php
include("print_page.php");
?><script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>