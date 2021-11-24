<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php"); 
$tablename = "dischargesummarysheet";
?>
<!DOCTYPE html>
<html>
<head>
<title>Discharge Summary</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/webtoolkit.aim.js"></script>
<script src="js/dragresize.js"></script>
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
	div.AdditionalCode > div.dropdown-menu,
	div.AdditionalCodeICD10 > div.dropdown-menu
	{max-width:100% !important;}
	
	div.AdditionalCode li > a > span.text,
	div.AdditionalCodeICD10 li > a > span.text,
	{ max-width:99% !important; word-wrap:break-word; white-space:normal;}
	 
	a { font-weight:500; padding:2px 6px; border:1px solid transparent; }
	.btn-round-xs a{-webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px; padding:2px 1px; color:red; background:#fff; border:2px solid #3232f0; text-decoration:none !important; font-size:14px; color:#C00; }
.btn-round-xs a:hover , .btn-round-xs a:active , .btn-round-xs a:focus{text-decoration:underline; color:red; text-decoration:none !important;}
</style>
<?php
$spec = '</head>
<body onClick="document.getElementById(\'divSaveAlert\').style.display = \'none\'; closeEpost();  return top.frames[0].main_frmInner.hideSliders();">';
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
extract($_GET);
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$patient_id = $_REQUEST['patient_id'];
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }

$privileges = $_SESSION['userPrivileges'];
$privilegesArr = explode(', ',$privileges);
$preOpHealthQuesId = $_REQUEST['preOpHealthQuesId'];
$cancelRecord = $_REQUEST['cancelRecord'];
$scan = $_REQUEST['scan'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
if($scan){
	unset($arrayRecord);
	$arrayRecord['user_id'] = $_SESSION['loginUserId'];
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['confirmation_id'] = $pConfId;
	$arrayRecord['form_name'] = 'discharge_summary_form';
	$arrayRecord['status'] = 'scaned';
	$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
	$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
}

	//GETTING PATIENTCONFIRMATION DETAILS
	$confirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	$primary_procedure_id = $confirmationDetails->patient_primary_procedure_id;
	$primary_procedure_name = $confirmationDetails->patient_primary_procedure;
	$secondary_procedure_id = $confirmationDetails->patient_secondary_procedure_id;
	$tertiary_procedure_id = $confirmationDetails->patient_tertiary_procedure_id;
	$primary_procedure_is_inj_misc =	$confirmationDetails->prim_proc_is_misc;	
	$surgeonId = $confirmationDetails->surgeonId;
	$anesthesiologist_id = $confirmationDetails->anesthesiologist_id;
	$ascId = $confirmationDetails->ascId;
	//GET ASSIGNED SURGEON ID AND SURGEON NAME
	$dischargeSummaryAssignedSurgeonId = $confirmationDetails->surgeonId;
	$dischargeSummaryAssignedSurgeonName = stripslashes($confirmationDetails->surgeon_name);
	$surgeryTime = $confirmationDetails->surgery_time;
	$dos = $confirmationDetails->dos;
	$import_status = $confirmationDetails->import_status;
	$surgerySite=$confirmationDetails->site;
	$secondarySite	=	$confirmationDetails->secondary_site;
	$secondarySite	=	($secondarySite)	?	$secondarySite : $surgerySite;
	$tertiarySite	=	$confirmationDetails->tertiary_site;
	$tertiarySite	=	($tertiarySite)	?	$tertiarySite : $surgerySite;
	// Get Modifiers to autofill based upon Site and Procedure POE Period
	$sitePoeMod = $objManageData->proc_site_modifiers($patient_id, $dos, $surgerySite,$primary_procedure_id,$secondarySite,$secondary_procedure_id,$tertiarySite,$tertiary_procedure_id);
	
	if($surgerySite == 1) {
		$surgerySite = "Left Eye";  //OS
	}else if($surgerySite == 2) {
		$surgerySite = "Right Eye";  //OD
	}else if($surgerySite == 3) {
		$surgerySite = "Both Eye";  //OU
	}else if($surgerySite == 4) {
		$surgerySite = "Left Upper Lid";
	}else if($surgerySite == 5) {
		$surgerySite = "Left Lower Lid";
	}else if($surgerySite == 6) {
		$surgerySite = "Right Upper Lid";
	}else if($surgerySite == 7) {
		$surgerySite = "Right Lower Lid";
	}else if($surgerySite == 8) {
		$surgerySite = "Bilateral Upper Lid";
	}else if($surgerySite == 9) {
		$surgerySite = "Bilateral Lower Lid";
	}
	
	if($secondarySite == 1) {
		$secondarySite = "Left Eye";  //OS
	}else if($secondarySite == 2) {
		$secondarySite = "Right Eye";  //OD
	}else if($secondarySite == 3) {
		$secondarySite = "Both Eye";  //OU
	}else if($secondarySite == 4) {
		$secondarySite = "Left Upper Lid";
	}else if($secondarySite == 5) {
		$secondarySite = "Left Lower Lid";
	}else if($secondarySite == 6) {
		$secondarySite = "Right Upper Lid";
	}else if($secondarySite == 7) {
		$secondarySite = "Right Lower Lid";
	}else if($secondarySite == 8) {
		$secondarySite = "Bilateral Upper Lid";
	}else if($secondarySite == 9) {
		$secondarySite = "Bilateral Lower Lid";
	}
	
	if($tertiarySite == 1) {
		$tertiarySite = "Left Eye";  //OS
	}else if($tertiarySite == 2) {
		$tertiarySite = "Right Eye";  //OD
	}else if($tertiarySite == 3) {
		$tertiarySite = "Both Eye";  //OU
	}else if($tertiarySite == 4) {
		$tertiarySite = "Left Upper Lid";
	}else if($tertiarySite == 5) {
		$tertiarySite = "Left Lower Lid";
	}else if($tertiarySite == 6) {
		$tertiarySite = "Right Upper Lid";
	}else if($tertiarySite == 7) {
		$tertiarySite = "Right Lower Lid";
	}else if($tertiarySite == 8) {
		$tertiarySite = "Bilateral Upper Lid";
	}else if($tertiarySite == 9) {
		$tertiarySite = "Bilateral Lower Lid";
	}
	
	//END GET ASSIGNED SURGEON ID AND SURGEON NAME
	
	//GETTING PATIENTCONFIRMATION DETAILS
	//GETTING SURGEONDETAILS
	unset($conditionArr);
	$conditionArr['usersId'] = $surgeonId;
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if(is_array($surgeonsDetails)){
		foreach($surgeonsDetails as $usersDetail){
			$signatureOfSurgeon  = $usersDetail->signature;
			$fname_Surgeon  = $usersDetail->fname;
			$mname_Surgeon  = $usersDetail->mname;
			$lname_Surgeon  = $usersDetail->lname;
			$npi_Surgeon  = trim($usersDetail->npi);
			$nameOfSurgeon  = $lname_Surgeon.", ".$fname_Surgeon." ".$mname_Surgeon;
		}
	}
//GETTING SURGEONDETAILS

	//GETTING 	ANES DETAILS
	unset($conditionArr);
	$conditionArr['usersId'] = $anesthesiologist_id;
	$anesDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if(is_array($anesDetails)){
		foreach($anesDetails as $usersDetail){
			$signatureOfAnes= $usersDetail->signature;
			$fname_Anes  	= $usersDetail->fname;
			$mname_Anes  	= $usersDetail->mname;
			$lname_Anes  	= $usersDetail->lname;
			$npi_Anes  	 	= trim($usersDetail->npi);
			$nameOfAnes  	= $lname_Anes.", ".$fname_Anes." ".$mname_Anes;
		}
	}
	//GETTING ANES DETAILS


//GET LOGGED IN USER TYPE
	unset($conditionArr);
	$conditionArr['usersId'] = $_SESSION["loginUserId"];
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails){
		foreach($surgeonsDetails as $usersDetail)
		{
			$loggedUserType = $usersDetail->user_type;
		}
	}
//END GET LOGGED IN USER TYPE	

$primProcDxCodes	=	$secProcDxCodes	=	$terProcDxCodes	=	'';

//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
		//$cpt_id = $_REQUEST['show_td'];
		$cpt_id_arr = $cpt_id_default_arr = $cpt_id_anes_arr = $cpt_id_anes_default_arr = $dx_id_arr = $dx_id_default_arr = $dx_id_icd10_arr = $dx_id_default_icd10_arr = array();
		if($surgeonId<>"") {
			$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
			$selectSurgeonRes = imw_query($selectSurgeonQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error());
			while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
				$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
			}
			if(is_array($surgeonProfileIdArr)){
				$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
			}else {
				$surgeonProfileIdImplode = 0;
			}
			$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where procedureId !=  '0' AND profileId in ($surgeonProfileIdImplode) ORDER BY procedureId =  '".$primary_procedure_id."' DESC , procedureId =  '".$secondary_procedure_id."' DESC , procedureId =  '".$tertiary_procedure_id."' DESC , procedureName";
			$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error());
			$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
			if($selectSurgeonProcedureNumRow>0) {
				while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
					$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
					if($primary_procedure_id == $surgeonProfileProcedureId || $secondary_procedure_id == $surgeonProfileProcedureId || $tertiary_procedure_id == $surgeonProfileProcedureId) {
						$dischargeSheetFound = "true";
						/*
						$cpt_id = $selectSurgeonProcedureRow['cpt_id'];
						$cpt_id_default = $selectSurgeonProcedureRow['cpt_id_default'];
						$dx_id = $selectSurgeonProcedureRow['dx_id'];
						$dx_id_default = $selectSurgeonProcedureRow['dx_id_default'];
						
						$dx_id_icd10 = $selectSurgeonProcedureRow['dx_id_icd10'];
						$dx_id_default_icd10 = $selectSurgeonProcedureRow['dx_id_default_icd10'];
						*/
						
						$cpt_id_arr[] 				= $selectSurgeonProcedureRow['cpt_id'];
						$cpt_id_default_arr[] 		= $selectSurgeonProcedureRow['cpt_id_default'];
						
						$cpt_id_anes_arr[] 			= $selectSurgeonProcedureRow['cpt_id_anes'];
						$cpt_id_anes_default_arr[] 	= $selectSurgeonProcedureRow['cpt_id_anes_default'];
						
						$dx_id_arr[] 				= $selectSurgeonProcedureRow['dx_id'];
						$dx_id_default_arr[] 		= $selectSurgeonProcedureRow['dx_id_default'];
						
						$dx_id_icd10_arr[] 			= $selectSurgeonProcedureRow['dx_id_icd10'];
						$dx_id_default_icd10_arr[]	= $selectSurgeonProcedureRow['dx_id_default_icd10'];
						
						if($tertiary_procedure_id == $surgeonProfileProcedureId)
						{
							$terProcDxCodes	=	$selectSurgeonProcedureRow['dx_id_icd10'];		
						}
						
						if($secondary_procedure_id == $surgeonProfileProcedureId)
						{
							$secProcDxCodes	=	$selectSurgeonProcedureRow['dx_id_icd10'];		
						}
						
						if($primary_procedure_id == $surgeonProfileProcedureId)
						{
							$primProcDxCodes	=	$selectSurgeonProcedureRow['dx_id_icd10'];		
						}
						
						
					}		
				}
			}
				
			//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
			//FROM SURGEON'S DEFAULT PROFILE 
				/*if($dischargeSheetFound<>"true") {
					$selectSurgeonQry 				= "select * from surgeonprofile where surgeonId = '$surgeonId' AND defaultProfile = '1'";
					$selectSurgeonRes 				= imw_query($selectSurgeonQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error());
					while($selectSurgeonRow 		= imw_fetch_array($selectSurgeonRes)) {
						$surgeonProfileIdArrInst[] 	= $selectSurgeonRow['surgeonProfileId'];
					}
					if(is_array($surgeonProfileIdArrInst)){
						$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArrInst);
					}else {
						$surgeonProfileIdImplode = 0;
					}
					$selectSurgeonProcedureQry 		= "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) AND (cpt_id!='' or dx_id!='' or dx_id_icd10!='') order by procedureName";
					$selectSurgeonProcedureRes 		= imw_query($selectSurgeonProcedureQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error());
					$selectSurgeonProcedureNumRow 	= imw_num_rows($selectSurgeonProcedureRes);
					if($selectSurgeonProcedureNumRow>0) {
						$dischargeSheetFound = "true";
						$selectSurgeonProcedureRow 	= imw_fetch_array($selectSurgeonProcedureRes);
							$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
							/*
							$cpt_id 				= $selectSurgeonProcedureRow['cpt_id'];
							$cpt_id_default 		= $selectSurgeonProcedureRow['cpt_id_default'];
							$dx_id 					= $selectSurgeonProcedureRow['dx_id'];
							$dx_id_default 			= $selectSurgeonProcedureRow['dx_id_default'];
							
							$dx_id_icd10			= $selectSurgeonProcedureRow['dx_id_icd10'];
							$dx_id_default_icd10	= $selectSurgeonProcedureRow['dx_id_default_icd10'];
							* /
							$cpt_id_arr[] 				= $selectSurgeonProcedureRow['cpt_id'];
							$cpt_id_default_arr[] 		= $selectSurgeonProcedureRow['cpt_id_default'];
							$dx_id_arr[] 				= $selectSurgeonProcedureRow['dx_id'];
							$dx_id_default_arr[] 		= $selectSurgeonProcedureRow['dx_id_default'];
							
							$dx_id_icd10_arr[] 			= $selectSurgeonProcedureRow['dx_id_icd10'];
							$dx_id_default_icd10_arr[]	= $selectSurgeonProcedureRow['dx_id_default_icd10'];
							
					}
				}*/
				if(count($cpt_id_arr)>0 || count($cpt_id_anes_arr)>0 || count($dx_id_arr)>0 || count($dx_id_icd10_arr)>0) {
					$cpt_id 				= implode(",",array_filter(array_unique(explode(",",implode(",",$cpt_id_arr)))));	
					$cpt_id_default 		= implode(",",array_filter(array_unique(explode(",",implode(",",$cpt_id_default_arr)))));
					$cpt_id_anes			= implode(",",array_filter(array_unique(explode(",",implode(",",$cpt_id_anes_arr)))));	
					$cpt_id_anes_default 	= implode(",",array_filter(array_unique(explode(",",implode(",",$cpt_id_anes_default_arr)))));
					$dx_id 					= implode(",",array_filter(array_unique(explode(",",implode(",",$dx_id_arr)))));
					$dx_id_default 			= implode(",",array_filter(array_unique(explode(",",implode(",",$dx_id_default_arr)))));
					$dx_id_icd10 			= implode(",",array_filter(array_unique(explode(",",implode(",",$dx_id_icd10_arr)))));
					$dx_id_default_icd10 	= implode(",",array_filter(array_unique(explode(",",implode(",",$dx_id_default_icd10_arr)))));
				}
				
				$cpt_id						=	$cpt_id .(($cpt_id && $cpt_id_anes) ? ',' : ''). $cpt_id_anes ;
				$cpt_id_default				=	$cpt_id_default .(($cpt_id_default && $cpt_id_anes_default) ? ',' : ''). $cpt_id_anes_default ;
				
					
			//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
			//FROM SURGEON'S DEFAULT PROFILE  
		}
		
		
		/*****
		* Start Procedure Preference Card 
		*****/
		//$pro_cpt_id_arr = $pro_cpt_id_default_arr = $pro_cpt_id_anes_arr = $pro_cpt_id_anes_default_arr = $pro_dx_id_arr = $pro_dx_id_default_arr = $pro_dx_id_icd10_arr = $pro_dx_id_default_icd10_arr = array();
		$pro_cpt_id = $pro_cpt_id_default = $pro_cpt_id_anes = $pro_cpt_id_anes_default = $pro_dx_id = $pro_dx_id_default = $pro_dx_id_icd10 = $pro_dx_id_default_icd10 = '';
		if($dischargeSheetFound <> "true")
		{ 
			$proceduresArr	=	array($primary_procedure_id,$secondary_procedure_id,$tertiary_procedure_id);
			foreach($proceduresArr as $procedureId)
			{
				if($procedureId)
				{		
					$procPrefCardQry	=	"Select * From procedureprofile Where procedureId !=  '0' AND procedureId = '".$procedureId."' ";
					$procPrefCardSql		=	imw_query($procPrefCardQry) or die( 'Error at line no.'. (__LINE__).': '.imw_error());
					$procPrefCardCnt	=	imw_num_rows($procPrefCardSql);
					if($procPrefCardCnt > 0 )
					{
						$procPrefCardRow	=	imw_fetch_object($procPrefCardSql);
						
						$pro_cpt_id 			.= ','.$procPrefCardRow->cpt_id;
						$pro_cpt_id_default 	.= ','.$procPrefCardRow->cpt_id_default;
						$pro_cpt_id_anes 		.= ','.$procPrefCardRow->cpt_id_anes;
						$pro_cpt_id_anes_default.= ','.$procPrefCardRow->cpt_id_anes_default;
						$pro_dx_id 				.= ','.$procPrefCardRow->dx_id;
						$pro_dx_id_default 		.= ','.$procPrefCardRow->dx_id_default;
						$pro_dx_id_icd10		.= ','.$procPrefCardRow->dx_id_icd10;
						$pro_dx_id_default_icd10.= ','.$procPrefCardRow->dx_id_default_icd10;
						
						if($tertiary_procedure_id == $procPrefCardRow->procedureId)
						{
							$terProcDxCodes	=	$procPrefCardRow->dx_id_icd10;		
						}
						
						if($secondary_procedure_id == $procPrefCardRow->procedureId)
						{
							$secProcDxCodes	=	$procPrefCardRow->dx_id_icd10;		
						}
						
						if($primary_procedure_id == $procPrefCardRow->procedureId)
						{
							$primProcDxCodes	=	$procPrefCardRow->dx_id_icd10;		
						}
						
					}
				}
			}
			
			$cpt_id 			= implode(",",array_unique(array_filter(explode(",",$pro_cpt_id))));	
			$cpt_id_default 	= implode(",",array_unique(array_filter(explode(",",$pro_cpt_id_default))));
			$cpt_id_anes 		= implode(",",array_unique(array_filter(explode(",",$pro_cpt_id_anes))));	
			$cpt_id_anes_default= implode(",",array_unique(array_filter(explode(",",$pro_cpt_id_anes_default))));	
			$dx_id 				= implode(",",array_unique(array_filter(explode(",",$pro_dx_id))));	
			$dx_id_default 		= implode(",",array_unique(array_filter(explode(",",$pro_dx_id_default))));	
			$dx_id_icd10 		= implode(",",array_unique(array_filter(explode(",",$pro_dx_id_icd10))));	
			$dx_id_default_icd10= implode(",",array_unique(array_filter(explode(",",$pro_dx_id_default_icd10))));	
		
			$cpt_id				= $cpt_id .(($cpt_id && $cpt_id_anes) ? ',' : ''). $cpt_id_anes ;
			$cpt_id_default		= $cpt_id_default .(($cpt_id_default && $cpt_id_anes_default) ? ',' : ''). $cpt_id_anes_default ;
		}
		
		
		/*****
		* End Procedure Preference Card 
		*****/
		
		// Check If Procedure is Injection Procedure
		$primProcDetails	=	$objManageData->getRowRecord('procedures','procedureId',$primary_procedure_id,'','','catId');
		if( $primProcDetails->catId <> '2')
		{
			if($primary_procedure_is_inj_misc == '')
			{
				//$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $primProcDetails->catId);
				$primary_procedure_is_inj_misc		=	$objManageData->verifyProcIsInjMisc($primary_procedure_id);
				//($chkprocedurecatDetails->isMisc) ?	'true'	:	'';
			}
			
		}else
		{
				$primary_procedure_is_inj_misc	=	'';
		}
		// End Check If Procedure is Injection Procedure		
		
		
		/******************************************
		 Start Injection/Misc. Procedure Template
		******************************************/
		if( $primProcDetails->catId <> '2' && $primary_procedure_is_inj_misc )
		{
			$procedureDetails	=	array($primary_procedure_id,$secondary_procedure_id,$tertiary_procedure_id);
			if(is_array($procedureDetails) && count($procedureDetails) > 0 )  
			{
				$injMiscCptIdArr = $injMiscDefaultCptIdArr	 = $injMiscDxIdArr = $injMiscDefaultDxIdArr = $injMiscDxIcd10IdArr = $injMiscDefaultDxIcd10IdArr = array() ;
				$injMiscCptId = $injMiscDefaultCptId	 = $injMiscDxId = $injMiscDefaultDxId = $injMiscDxIcd10Id = $injMiscDefaultDxIcd10Id = '' ;
				foreach($procedureDetails as	$procedureID)
				{
					$fields	=	'procedureID,cpt_id,cpt_id_default,dx_id,dx_id_default,dx_id_icd10,dx_id_default_icd10';
					$defaultProfile	= $objManageData->injectionProfile($procedureID,$confirmationDetails->surgeonId,$fields);
					
					if($defaultProfile['profileFound'])
					{
						$injMiscCptIdArr[]						=	$defaultProfile['data']['cpt_id'];
						$injMiscDefaultCptIdArr[]			=	$defaultProfile['data']['cpt_id_default'];
						$injMiscDxIdArr[]							=	$defaultProfile['data']['dx_id'];
						$injMiscDefaultDxIdArr[]			=	$defaultProfile['data']['dx_id_default'];
						$injMiscDxIcd10IdArr[]				=	$defaultProfile['data']['dx_id_icd10'];
						$injMiscDefaultDxIcd10IdArr[]	=	$defaultProfile['data']['dx_id_default_icd10'];
						
						if($tertiary_procedure_id == $defaultProfile['data']['procedureID'])
						{
							$terProcDxCodes	=	$defaultProfile['data']['dx_id_icd10'];	
						}
						if($secondary_procedure_id == $defaultProfile['data']['procedureID'])
						{
							$secProcDxCodes	=	$defaultProfile['data']['dx_id_icd10'];		
						}
						
						if($primary_procedure_id == $defaultProfile['data']['procedureID'])
						{
							$primProcDxCodes	=	$defaultProfile['data']['dx_id_icd10'];		
						}
						
					}
				}
				
				if(count($injMiscCptIdArr)>0 || count($injMiscDxIdArr)>0 || count($injMiscDxIcd10IdArr)>0) 
				{
					
					$injMiscCptId 					= implode(",",array_filter(array_unique(explode(",",implode(",",$injMiscCptIdArr)))));	
					$injMiscDefaultCptId 		= implode(",",array_filter(array_unique(explode(",",implode(",",$injMiscDefaultCptIdArr)))));
					$injMiscDxId 						= implode(",",array_filter(array_unique(explode(",",implode(",",$injMiscDxIdArr)))));
					$injMiscDefaultDxId 		= implode(",",array_filter(array_unique(explode(",",implode(",",$injMiscDefaultDxIdArr)))));
					$injMiscDxIcd10Id 			= implode(",",array_filter(array_unique(explode(",",implode(",",$injMiscDxIcd10IdArr)))));
					$injMiscDefaultDxIcd10Id= implode(",",array_filter(array_unique(explode(",",implode(",",$injMiscDefaultDxIcd10IdArr)))));
				}
				
				$cpt_id 						= ($injMiscCptId)						?	$injMiscCptId						: $cpt_id;	
				$cpt_id_default			= ($injMiscDefaultCptId)		?	$injMiscDefaultCptId		: $cpt_id_default;
				$dx_id 							= ($injMiscDxId)						?	$injMiscDxId						: $dx_id;
				$dx_id_default			= ($injMiscDefaultDxId)			?	$injMiscDefaultDxId			: $dx_id_default;
				$dx_id_icd10 				= ($injMiscDxIcd10Id)				?	$injMiscDxIcd10Id				: $dx_id_icd10;
				$dx_id_default_icd10= ($injMiscDefaultDxIcd10Id)?	$injMiscDefaultDxIcd10Id: $dx_id_default_icd10;
				
			}
			
		}
		/******************************************
		 End Injection/Misc. Procedure Template
		******************************************/



		/******************************************
		 Start Laser Procedure Template
		******************************************/
		
		if( $primProcDetails->catId == '2')
		{
				unset($condArr);
				$condArr['1']	=	'1' ;
				$xtraCondition	=	" And catId = '2' And procedureId IN (".$primary_procedure_id."".
				$xtraCondition	.=	($secondary_procedure_id)	?	','.$secondary_procedure_id : '' ;
				$xtraCondition	.=	($tertiary_procedure_id)		?	','.	$tertiary_procedure_id : '' ;
				$xtraCondition	.=	 ")";
				$procedureDetails	=	$objManageData->getMultiChkArrayRecords('procedures',$condArr,'','',$xtraCondition);
				
				if(is_array($procedureDetails) && count($procedureDetails) > 0 )  
				{
					$laserCptIdArr = $laserDefaultCptIdArr	 = $laserDxIdArr = $laserDefaultDxIdArr = $laserDxIcd10IdArr = $laserDefaultDxIcd10IdArr = array() ;
					$laserCptId = $laserDefaultCptId	 = $laserDxId = $laserDefaultDxId = $laserDxIcd10Id = $laserDefaultDxIcd10Id = '' ;
					foreach($procedureDetails as $key=>$procData	)
					{
						$laserProcTempQry =	"SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$procData->procedureId."' And (FIND_IN_SET(".$confirmationDetails->surgeonId.",laser_surgeonID)) ORDER BY laser_templateID DESC LIMIT 0,1 ";
						$laserProcTempSql	=	imw_query($laserProcTempQry) or die('Error found at line no '.(__LINE_).': '. imw_error());
						$laserProcTempCnt	=	imw_num_rows($laserProcTempSql);
						if( $laserProcTempCnt == 0 )
						{
							$laserProcTempQry =	"SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$procData->procedureId."' And laser_surgeonID = 'all' ORDER BY laser_templateID DESC LIMIT 0,1 ";
							$laserProcTempSql	=	imw_query($laserProcTempQry) or die('Error found at line no. '.(__LINE_).': '. imw_error());
							$laserProcTempCnt	=	imw_num_rows($laserProcTempSql);	
						}
						
						if( $laserProcTempCnt > 0 )
						{
							while($laserProcTempRow	=	imw_fetch_object($laserProcTempSql) )
							{
								$procSurgeonId 	=	$confirmationDetails->surgeonId;
								$laserSurgeon		=	$laserProcTempRow->laser_surgeonID;
								
								if($laserSurgeon != "all")
								{
									$laserSurgeonExplode	=	explode(",",$laserSurgeon);
									$laserSurgeonCount	=	count($laserSurgeonExplode);
									
									if($laserSurgeonCount	==	1 )
									{
										if($procSurgeonId	==	$laserSurgeon )
										{
											$laserCptIdArr[]						=	$laserProcTempRow->cpt_id;
											$laserDefaultCptIdArr[]			=	$laserProcTempRow->cpt_id_default;
											$laserDxIdArr[]							=	$laserProcTempRow->dx_id;
											$laserDefaultDxIdArr[]			=	$laserProcTempRow->dx_id_default;
											$laserDxIcd10IdArr[]				=	$laserProcTempRow->dx_id_icd10;
											$laserDefaultDxIcd10IdArr[]	=	$laserProcTempRow->dx_id_default_icd10;
											
											if($tertiary_procedure_id == $procData->procedureId)
						{
												$terProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
											}
											if($secondary_procedure_id == $procData->procedureId)
											{
												$secProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
											}
											
											if($primary_procedure_id == $procData->procedureId)
											{
												$primProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
											}
											
											continue;
										}
									}
									
									$matchedSurgeon=false;
									if( $laserSurgeonCount > 1 )
									{
										for( $i=0; $i < $laserSurgeonCount; $i++ )
										{
											$match_surgeonid	=	$procSurgeonId;
											$surgeon					=	$laserSurgeonExplode[$i];
											if( $surgeon == $match_surgeonid)
											{
												$matchedSurgeon	=	true;
												$laserCptIdArr[]					=	$laserProcTempRow->cpt_id;
												$laserDefaultCptIdArr[]		=	$laserProcTempRow->cpt_id_default;
												$laserDxIdArr[]						=	$laserProcTempRow->dx_id;
												$laserDefaultDxIdArr[]			=	$laserProcTempRow->dx_id_default;
												$laserDxIcd10IdArr[]			=	$laserProcTempRow->dx_id_icd10;
												$laserDefaultDxIcd10IdArr[]	=	$laserProcTempRow->dx_id_default_icd10;
												
												if($tertiary_procedure_id == $procData->procedureId)
						{
													$terProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
												}
												if($secondary_procedure_id == $procData->procedureId)
												{
													$secProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
												}
												
												if($primary_procedure_id == $procData->procedureId)
												{
													$primProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
												}
											
												continue;
											}
										}
									}
									
								}
								else
								{ 
									$laserCptIdArr[]					=	$laserProcTempRow->cpt_id;
									$laserDefaultCptIdArr[]		=	$laserProcTempRow->cpt_id_default;
									$laserDxIdArr[]						=	$laserProcTempRow->dx_id;
									$laserDefaultDxIdArr[]		=	$laserProcTempRow->dx_id_default;
									$laserDxIcd10IdArr[]			=	$laserProcTempRow->dx_id_icd10;
									$laserDefaultDxIcd10IdArr[]=$laserProcTempRow->dx_id_default_icd10;
									
									if($tertiary_procedure_id == $procData->procedureId)
									{
										$terProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
									}
									if($secondary_procedure_id == $procData->procedureId)
									{
										$secProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
									}
									
									if($primary_procedure_id == $procData->procedureId)
									{
										$primProcDxCodes	=	$laserProcTempRow->dx_id_icd10;		
									}
											
								}
							}
						}
					}
					
					if(count($laserCptIdArr)>0 || count($laserDxIdArr)>0 || count($laserDxIcd10IdArr)>0) 
					{
						
						$laserCptId 					= implode(",",array_filter(array_unique(explode(",",implode(",",$laserCptIdArr)))));	
						$laserDefaultCptId 		= implode(",",array_filter(array_unique(explode(",",implode(",",$laserDefaultCptIdArr)))));
						$laserDxId 						= implode(",",array_filter(array_unique(explode(",",implode(",",$laserDxIdArr)))));
						$laserDefaultDxId 		= implode(",",array_filter(array_unique(explode(",",implode(",",$laserDefaultDxIdArr)))));
						$laserDxIcd10Id 			= implode(",",array_filter(array_unique(explode(",",implode(",",$laserDxIcd10IdArr)))));
						$laserDefaultDxIcd10Id= implode(",",array_filter(array_unique(explode(",",implode(",",$laserDefaultDxIcd10IdArr)))));
					}
					
					$cpt_id 						= ($laserCptId)						?	$laserCptId						: $cpt_id;	
					$cpt_id_default			= ($laserDefaultCptId)		?	$laserDefaultCptId		: $cpt_id_default;
					$dx_id 							= ($laserDxId)						?	$laserDxId						: $dx_id;
					$dx_id_default			= ($laserDefaultDxId)			?	$laserDefaultDxId			: $dx_id_default;
					$dx_id_icd10 				= ($laserDxIcd10Id)				?	$laserDxIcd10Id				: $dx_id_icd10;
					$dx_id_default_icd10= ($laserDefaultDxIcd10Id)?	$laserDefaultDxIcd10Id: $dx_id_default_icd10;
					
				}
				
			}
		
		/******************************************
		 End Laser Procedure Template
		******************************************/

		// Start Filter Dx Codes related to Primary & Secondary Procedures
		$primProcDxCodes	=	array_filter(array_unique(explode(",",$primProcDxCodes)));
		$secProcDxCodes		=	array_filter(array_unique(explode(",",$secProcDxCodes)));
		$terProcDxCodes		=	array_filter(array_unique(explode(",",$terProcDxCodes)));
		// End Filter Dx Codes related to Primary & Secondary Procedures
		
		
		
	if(!$cancelRecord){
		////// FORM SHIFT TO RIGHT SLIDER
			$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$discharge_summary_form = $getLeftLinkDetails->discharge_summary_form;	
			if($discharge_summary_form=='true'){
				$formArrayRecord['discharge_summary_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
				
			}
			//MAKE AUDIT STATUS VIEW
			if($_REQUEST['saveRecord']!='true'){
				unset($arrayRecord);
				$arrayRecord['user_id'] = $_SESSION['loginUserId'];
				$arrayRecord['patient_id'] = $patient_id;
				$arrayRecord['confirmation_id'] = $pConfId;
				$arrayRecord['form_name'] = 'discharge_summary_form';
				$arrayRecord['status'] = 'viewed';
				$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
				$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
			   }
			//MAKE AUDIT STATUS VIEW
			
		////// FORM SHIFT TO RIGHT SLIDER
	}
	elseif($cancelRecord){
		$fieldName="discharge_summary_form";
		$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
		include("left_link_hide.php");
	}	
		$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&fieldName='.$fieldName;
	
if($_REQUEST['saveRecord']=='true'){
	$text = $_REQUEST['getText'];
	$tablename = "dischargesummarysheet";
	//Save_eposts($text,$tablename);
	$dischargeSummarySheetIdDB = $_REQUEST['dischargeSummarySheetIdDB'];
  $procIdArr = $_REQUEST['chbx_fdt'];
	$diagIdArr = $_REQUEST['diagID'];
	$diagNameArr = $_REQUEST['diag_names'];
	$diagCodeArr = $_REQUEST['diag_codes'];
	if($_REQUEST['dx_code_type']=='icd10')
	{
		//get icd10 code seprately
		foreach($diagIdArr as $_key => $val)
		{
			list($icd10_id,$icd10_code)=explode('~:~',$val);
			$icd10_id_arr[]=$icd10_id;
			$icd10_code = explode('@@',$icd10_code);
			if(is_array($icd10_code) && count($icd10_code) > 0 )
			{
				foreach($icd10_code as $tmp_key => $tmp_val)
				{
					if($diagCodeArr[$icd10_id] == $tmp_val)
						unset($icd10_code[$tmp_key]);
				}
				$icd10_code = implode('@@',$icd10_code);
				if(empty($icd10_code)) $icd10_code = $diagCodeArr[$icd10_id];
			}
			$icd10_code_arr[]=$icd10_code;
			$icd10_name_arr[] = $diagNameArr[$icd10_id];
		}
		unset($diagIdArr);
		unset($diagNameArr);
	}
	//START CODE TO CHECK SURGEON SIGN NI DATABASE
		
		$chkSurgeonSigndischargeDetails = $objManageData->getRowRecord('dischargesummarysheet', 'confirmation_id', $pConfId);
		if($chkSurgeonSigndischargeDetails) {
			$chk_signSurgeon1Id = $chkSurgeonSigndischargeDetails->signSurgeon1Id;
			
			//CHECK FORM STATUS
			$chk_form_status = $chkSurgeonSigndischargeDetails->form_status;
			//CHECK FORM STATUS
			
			$chk_dis_ScanUpload = $chkSurgeonSigndischargeDetails->dis_ScanUpload; 
			$chk_dis_ScanUpload2  = $chkSurgeonSigndischargeDetails->dis_ScanUpload2 ; 
			
		}
	//END CODE TO CHECK SURGEON SIGN NI DATABASE 
	if((count($procIdArr)>0 || $_REQUEST['miscellaneousOther']!='' 
		|| $_REQUEST['comment']!='' || $_REQUEST['other1']!='' 
		|| $_REQUEST['other2']!=''
		|| ($_REQUEST['chbx_disAttached']!='' && ($chk_dis_ScanUpload || $chk_dis_ScanUpload2))
		) && $chk_signSurgeon1Id<>"0"){
	    $form_status = 'completed';
	}
	else {
		$form_status = 'not completed';
	}
  $procNames 		= $_REQUEST['procNames'];
	$procCodeNames	= $_REQUEST['procCodeNames'];
	//$diagID = $_REQUEST['diagID'];
	if(is_array($procIdArr)){
		foreach($procIdArr as $key => $procId){
			$procdure_names[] = $procNames[$procId];
			$procdure_code_names[] = $procCodeNames[$procId];
			$procdure_id[] = $procId;
		}	
		$dischargeProceduresNames 		= @implode('!,!', $procdure_names);
		$dischargeProceduresCodeNames	= @implode('##', $procdure_code_names);
		$dischargeProcdureIds			= @implode(',', $procdure_id);
		
		$diagnosisIdsArr = $diagnosisNamesArr = array();
		foreach($diagIdArr as $diagID)
		{
			$diagnosisIdsArr[]			= $diagID;
			$diagnosisNamesArr[]		= $diagNameArr[$diagID];	
		}
		$diagnosisIds					= @implode(',', $diagnosisIdsArr);
		$diagnosisNames					= @implode('@@', $diagnosisNamesArr);
		
	}
	//START IF ASSIGNED PROCEDURE NAME IS "UNLISTED" THEN USER CAN SAVE DIAGNOSIS  INDVIDUALLY
	if(is_array($diagIdArr) && trim($primary_procedure_name)=='Unlisted'){
		$diagnosisIdsArr = $diagnosisNamesArr = array();
		foreach($diagIdArr as $diagID)
		{
			$diagnosisIdsArr[]			= $diagID;
			$diagnosisNamesArr[]		= $diagNameArr[$diagID];	
		}
		$diagnosisIds					= @implode(',', $diagnosisIdsArr);
		$diagnosisNames					= @implode('@@', $diagnosisNamesArr);
	}
	//END   IF ASSIGNED PROCEDURE NAME IS "UNLISTED" THEN USER CAN SAVE DIAGNOSIS  INDVIDUALLY

	$icd10_id = @implode(',', $icd10_id_arr);
	$icd10_code = @implode(',', $icd10_code_arr);
	$icd10_name = @implode('@@', $icd10_name_arr);
	
	unset($arrayRecord);
	$arrayRecord['surgeon_knowledge'] = $_REQUEST['chbx_h_p'];
	$arrayRecord['dischargeSummarySheetDate'] = date('Y-m-d');
	$arrayRecord['dischargeSummarySheetTime'] = date('H:i:s');
	$arrayRecord['procedures_name'] = addslashes($dischargeProceduresNames);
	$arrayRecord['procedures_code_name'] = addslashes($dischargeProceduresCodeNames);
	$arrayRecord['procedures_code'] = $dischargeProcdureIds;
	$arrayRecord['disAttached'] = $_REQUEST['chbx_disAttached'];
	$arrayRecord['otherMiscellaneous'] = addslashes($_REQUEST['miscellaneousOther']);
	$arrayRecord['comment'] = addslashes($_REQUEST['comment']);
	$arrayRecord['diag_ids'] = $diagnosisIds;	
	$arrayRecord['diag_names'] = addslashes($diagnosisNames);	
	$arrayRecord['icd10_code'] = addslashes($icd10_code);
	$arrayRecord['icd10_name'] = addslashes($icd10_name);
	$arrayRecord['icd10_id'] = $icd10_id;	
	$arrayRecord['other1'] = addslashes($_REQUEST['other1']);
	$arrayRecord['other2'] = addslashes($_REQUEST['other2']);
	$arrayRecord['surgeonSign'] = $_REQUEST['elem_signature'];
	$arrayRecord['summarySaveDateTime'] = date('Y-m-d H:i:s');
	$arrayRecord['form_status'] = $form_status;
	$arrayRecord['surgeonId'] = $surgeonId;
	$arrayRecord['cpt_inte_sync_status'] = '0';
	$arrayRecord['confirmation_id'] = $pConfId;
	$cpt_inte_sync_flag = '0';
	if(constant("INTE_SYNC") == 'YES') {
		$cpt_inte_sync_flag = '1';
	}
	$arrayRecord['cpt_inte_sync_flag'] = $cpt_inte_sync_flag;
	if( $chk_form_status <> 'completed' && $chk_form_status <> 'not completed' && isset($_POST['disclaimer_txt']) ) {
		$arrayRecord['disclaimer_txt'] = addslashes($_POST['disclaimer_txt']);
	}
	$diag_ids=$diagnosisIds;//code for acc/superbill
	$proc_code=$dischargeProcdureIds;//code for acc/superbill
	
	$isDischargeSummarySaved = false;
	if($dischargeSummarySheetIdDB){	
		
		if(($privilegesArr[0]=='Staff' || $privilegesArr[0]=='Billing') && ($privilegesArr[1]=='Billing' || $privilegesArr[1]=='Staff')) {
			//UPDATE ONLY COMMENT BOX IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES
				unset($staffArrRecord);
				$staffArrRecord['cpt_inte_sync_status'] = '0';
				$staffArrRecord['cpt_inte_sync_flag'] = $cpt_inte_sync_flag;
				$staffArrRecord['comment'] = $_REQUEST['comment'];
				$objManageData->updateRecords($staffArrRecord, 'dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetIdDB);		
			//END UPDATE ONLY COMMENT BOX IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES
		}else {
			//ELSE UPDATE WHOLE RECORD
			$objManageData->updateRecords($arrayRecord, 'dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetIdDB);
			$isDischargeSummarySaved = true;
		}
		
		//set audit status created
		//end set audit status created
		
	}else{
		if(($privilegesArr[0]=='Staff' || $privilegesArr[0]=='Billing') && ($privilegesArr[1]=='Billing' || $privilegesArr[1]=='Staff')) {
			//ADD ONLY COMMENT BOX IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES
				unset($staffArrRecord);
				$staffArrRecord['comment'] = $_REQUEST['comment'];
				$objManageData->addRecords($staffArrRecord, 'dischargesummarysheet');
			//END ADD ONLY COMMENT BOX IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES	
		}else {
			//ELSE ADD WHOLE RECORD
			$objManageData->addRecords($arrayRecord, 'dischargesummarysheet');
			$isDischargeSummarySaved = true;
		}
	}
	
	//CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 	
	
	//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'discharge_summary_form';
		$conditionArr['status'] = 'created';
		$chkAuditStatus = $objManageData->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);	
		if($chkAuditStatus) {
			//MAKE AUDIT STATUS MODIFIED
			$chart_note_updateQry=imw_query("
											insert into chartnotes_change_audit_tbl set
											user_id ='".$_SESSION['loginUserId']."',
											patient_id =$patient_id,
											confirmation_id = $pConfId,
											form_name ='discharge_summary_form',
											status='modified',
											action_date_time = '".date('Y-m-d H:i:s')."'
											");
		}else {
			//MAKE AUDIT STATUS CREATED
			$chart_note_createdQry=imw_query("
											insert into chartnotes_change_audit_tbl set
											user_id ='".$_SESSION['loginUserId']."',
											patient_id =$patient_id,
											confirmation_id = $pConfId,
											form_name ='discharge_summary_form',
											status='created',
											action_date_time = '".date('Y-m-d H:i:s')."'
											");
		}
		
	//CODE END TO SET AUDIT STATUS AFTER SAVE
	
	/*** Save Super Bill ***/
	
			
		//superBillDxCodes_'+CptId+'
		$diagCodeType			=	$_REQUEST['dx_code_type'];
		$DxCodeFieldName		=	($diagCodeType == 'icd10')	?	'dxcode_icd10'	:	'dxcode_icd9';
		
		$superBillRecordIdArr	=	$_REQUEST['superBillRecordId'];		
		$superBillbillUserTypeArr		=	$_REQUEST['billUserType'];
		$superBillCptIdArr		=	$_REQUEST['superBillCptId'];	
		$superBillCptCodeArr	=	$_REQUEST['superBillCptCodes'];
		$superBillQuantityArr	=	$_REQUEST['superBillQuantity'];
		$superBillModifier1Arr	=	$_REQUEST['superBillModifier1'];
		$superBillModifier2Arr	=	$_REQUEST['superBillModifier2'];
		$superBillModifier3Arr	=	$_REQUEST['superBillModifier3'];
		
		if(is_array($superBillCptCodeArr) && count($superBillCptCodeArr) >  0)
		{
			foreach($superBillCptCodeArr as $key => $SB_cptCode)
			{
				$SB_recordId	=	(array_key_exists($key,$superBillRecordIdArr))	?	$superBillRecordIdArr[$key]		:	'';
				$SB_billUserType=	$superBillbillUserTypeArr[$key];
				$SB_cptId			=	$superBillCptIdArr[$key];
				$SB_dxCodes		=	implode(',',$_REQUEST['superBillDxCodes_'.$SB_cptId.'_'.$SB_billUserType]);
				$SB_quantity	=	$superBillQuantityArr[$key];
				$SB_modifier1	=	$superBillModifier1Arr[$key];
				$SB_modifier2	=	$superBillModifier2Arr[$key];
				$SB_modifier3	=	$superBillModifier3Arr[$key];
				
				//print_r($_REQUEST['superBillDxCodes_'.$SB_cptId.'_'.$SB_billUserType]);echo '<br>';
				//echo $SB_dxCodes .'<br><br>';
				
				unset($insertUpdateRecord);
				$insertUpdateRecord['confirmation_id']	=	$pConfId;
				$insertUpdateRecord['bill_user_type']		=	$SB_billUserType;
				$insertUpdateRecord['cpt_id']			=	$SB_cptId;
				$insertUpdateRecord['cpt_code']			=	$SB_cptCode;
				$insertUpdateRecord[$DxCodeFieldName]	=	$SB_dxCodes;
				$insertUpdateRecord['quantity']			=	$SB_quantity;
				$insertUpdateRecord['modifier1']		=	$SB_modifier1;
				$insertUpdateRecord['modifier2']		=	$SB_modifier2;
				$insertUpdateRecord['modifier3']		=	$SB_modifier3;
				$insertUpdateRecord['modified_by']	=	$_SESSION["loginUserId"];
				$insertUpdateRecord['modified_on']	=	date('Y-m-d H:i:s');
				
				unset($chkArray);
				$chkArray['confirmation_id']=	$pConfId;
				$chkArray['bill_user_type']=	$SB_billUserType;
				$chkArray['cpt_id']			=	$SB_cptId;
				$chkArray['cpt_code']		=	$SB_cptCode;
				$chkArray['deleted']		=	0	;
				
				
				if($SB_recordId)
				{ 
					//echo 'update'; print_r($insertUpdateRecord);
					$objManageData->UpdateRecord($insertUpdateRecord,'superbill_tbl','superbill_id',$SB_recordId);
				}
				else
				{
					$chkRecords	=	$objManageData->getMultiChkArrayRecords('superbill_tbl',$chkArray);
					if($chkRecords)
					{ }
					else
					{					
						//echo 'insert'; print_r($insertUpdateRecord);
						$objManageData->addRecords($insertUpdateRecord,'superbill_tbl');	
					}
				}
				
			}
		}
	
	/*** End Save Super Bill ***/
	
	/* Delete Super Bill Records*/
	$deleteSuperBillRecords		=	$_REQUEST['deleteSuperBillRecords'];
	$deleteSuperBillRecordsArr	=	explode(',',$deleteSuperBillRecords);
	$deleteSuperBillRecordsArr	=	array_unique(array_filter($deleteSuperBillRecordsArr));
	if(is_array($deleteSuperBillRecordsArr) && count($deleteSuperBillRecordsArr) > 0 )
	{
		foreach($deleteSuperBillRecordsArr as $recordId)
		{
			unset($insertUpdateRecord);
			$insertUpdateRecord['deleted']			=	1;
			$insertUpdateRecord['modified_by']	=	$_SESSION["loginUserId"];
			$insertUpdateRecord['modified_on']	=	date('Y-m-d H:i:s');
			
			$objManageData->UpdateRecord($insertUpdateRecord,'superbill_tbl','superbill_id',$recordId);
		}
		
	}
	
	/**/
	
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error());
	//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
	
	
	//Start Remove Discharge Summary Sheet Review Modal once chart saved
	echo "<script>
			if(top.document.getElementById('dischargeConfirmModal'))
			{
				top.document.getElementById('dischargeConfirmModal').remove();	
				top.document.getElementById('confirmType').value='';
			}
		</script>";	
	// End Remove Discharge Summary Sheet Review Modal once chart saved
	//start
	if(trim($ascId) && $form_status=="completed" && $imwSwitchFile == "sync_imwemr.php" && $import_status=="false" && constant('STOP_SYNC_SUPERBILL') != "YES" ) 
	{
		include_once("sync_superbill.php");
	}

	if(trim($ascId) && $form_status=="completed" )
	{
		/**
		 * Log Rabbit Mq messages
		 */
		//include_once(dirname(__FILE__).'/library/rabbitmq_integration/log_charges.php');
	}
	
	/*******HL7- DFT GENERATION***********/
	if($isDischargeSummarySaved && constant('DCS_DFT_GENERATION')==true && !in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman','albany','waltham', 'gnysx','mackool','islandeye','valleyeye'))){
		include_once(dirname(__FILE__)."/dft_hl7_generate.php");
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('palisades'))){
			include_once(dirname(__FILE__)."/dft_hl7_generate_doc_choice.php");
		}
	}
	/*******DFT GENERATION END************/
}

function getSelectedOption($laterality,$severity,$staging,$icd10CodeOrg,$icd10CodeDB,$icdLater)
{
	
	$posToFindFrom=0;//start search from 0 index
	//get selected values for icd 10
	if(trim($laterality))
	{
		//get posttition of first '-'
		$position=$posVal='';
		$posToFindFromTMP=($posToFindFrom)?$posToFindFrom+1:0;
		$position=strpos($icd10CodeOrg ,'-',$posToFindFromTMP);
		$laterSelId=substr($icd10CodeDB,$position,1);
		//$laterSelTxt=$icdLater[$laterality][$laterSelId];
		//list($laterSelTxt,$laterSelCode)=explode('~:~',$laterSelTxt);
		$posToFindFrom=$position;
	}
											
	if(trim($severity))
	{
		//get posttition of first '-'
		$position=$posVal='';
		$posToFindFromTMP=($posToFindFrom)?$posToFindFrom+1:0;
		$position=strpos($icd10CodeOrg ,'-',$posToFindFromTMP);
		$severitySelId=substr($icd10CodeDB,$position,1);
		//$severitySelTxt=$icdLater[$severity][$severitySelId];
		//list($severitySelTxt,$severitySelCode)=explode('~:~',$severitySelTxt);
		$posToFindFrom=$position; 
	}
										 
	if(trim($staging))
	{
		//get posttition of first '-'
		$position=$posVal='';
		$posToFindFromTMP=($posToFindFrom)?$posToFindFrom+1:0;
		$position=strpos($icd10CodeOrg ,'-',$posToFindFromTMP);
		$stagingSelId=substr($icd10CodeDB,$position,1);
		//$stagingSelTxt=$icdLater[$staging][$stagingSelId];
		//list($stagingSelTxt,$stagingSelCode)=explode('~:~',$stagingSelTxt);
		$posToFindFrom=$position; 	 
	}
	
	$return = array();
	$return['laterality'] = $laterSelId;
	$return['severity'] = $severitySelId;
	$return['staging'] = $stagingSelId;
	
	return $return;
}
	$procNameArray = $procCodeNameArray = array(); 	
//GETTING IF ALREADY EXISTS
	$dischargeDetails = $objManageData->getRowRecord("dischargesummarysheet", "confirmation_id", $pConfId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat ");
	if($dischargeDetails){
		$procedures_idDBExplodeArr=array();
		$diag_idDBExplodeArr=array();
		$dischargeSummarySheetIdDB = $dischargeDetails->dischargeSummarySheetId;
		$procedures_nameDB = $dischargeDetails->procedures_name;
		$procedures_codeDB = $dischargeDetails->procedures_code_name;
		$disAttached = $dischargeDetails->disAttached;		
		$otherMiscellaneousDB = $dischargeDetails->otherMiscellaneous;		
		$comment = $dischargeDetails->comment;		
		$dis_ScanUpload = $dischargeDetails->dis_ScanUpload;
		$dis_ScanUpload2 = $dischargeDetails->dis_ScanUpload2;
		$other1DB = $dischargeDetails->other1;
		$other2DB = $dischargeDetails->other2;
		$surgeon_knowledgeDB = $dischargeDetails->surgeon_knowledge;
		$surgeonSignDB = $dischargeDetails->surgeonSign;		
		$procedures_IdDB = $dischargeDetails->procedures_code;
		$diag_idDB = $dischargeDetails->diag_ids;
		$diag_nameDB = $dischargeDetails->diag_names;
		
		$disclaimer_txt = stripslashes($dischargeDetails->disclaimer_txt);
		$icd10_idDB = $dischargeDetails->icd10_id;
		$icd10_codeDB = $dischargeDetails->icd10_code;
		$icd10_nameDB = $dischargeDetails->icd10_name;	
		
		$procNameExplode			=	array_filter(explode("!,!",$procedures_nameDB));
		$procCodeNameExplode		=	array_filter(explode("##",$procedures_codeDB));
		$procedures_idDBExplodeArr	=	array_filter(explode(",", $procedures_IdDB));
		
		$procedureDBExists = false;
		if(is_array($procedures_idDBExplodeArr) && count($procedures_idDBExplodeArr) > 0)
		{
			$procedureDBExists =  true;
			foreach($procedures_idDBExplodeArr as $_key=>$_val)	
			{
				$procNameArray[$_val]		=	trim($procNameExplode[$_key]);
				$procCodeNameArray[$_val]	=	trim($procCodeNameExplode[$_key]);
			}
		}
		
		$icd10_idDBExplodeArr		= array_filter(explode(",", $icd10_idDB));	
		$icd10_codeDBExplodeArrTemp = array_filter(explode(",", $icd10_codeDB));
		$icd10_nameDBExplodeArrTemp = array_filter(explode("@@", $icd10_nameDB));
		
		for($ix=0;$ix<=sizeof($icd10_idDBExplodeArr);$ix++)
		{
			$icd10_codeDBExplodeArr[$icd10_idDBExplodeArr[$ix]]=$icd10_codeDBExplodeArrTemp[$ix];
			$icd10_nameDBExplodeArr[$icd10_idDBExplodeArr[$ix]]=$icd10_nameDBExplodeArrTemp[$ix];		
		}
		
		$diag_idDBExplodeArr	=	array_filter(explode(",", $diag_idDB));
		$diag_idDBExplodeArrTemp=	array_filter(explode("@@", $diag_nameDB));
		$diag_nameDBExplodeArr	=	array();
		if(is_array($diag_idDBExplodeArr) && count($diag_idDBExplodeArr) > 0 )
		{
			foreach($diag_idDBExplodeArr as $_key=>$diagID)
			{
				$diag_nameDBExplodeArr[$diagID] = $diag_idDBExplodeArrTemp[$_key];	
			}
		}
		/*for($ix=0;$ix<=sizeof($diag_idDBExplodeArr);$ix++)
		{
			$icd10_codeDBExplodeArr[$diag_idDBExplodeArr[$ix]]=$icd10_codeDBExplodeArrTemp[$ix];		
		}*/
	    $form_status=$dischargeDetails->form_status;
		
		$signSurgeon1Id=$dischargeDetails->signSurgeon1Id;
		$signSurgeon1DateTimeFormat=$dischargeDetails->signSurgeon1DateTimeFormat;
		$signSurgeon1FirstName=$dischargeDetails->signSurgeon1FirstName;
		$signSurgeon1MiddleName=$dischargeDetails->signSurgeon1MiddleName;
		$signSurgeon1LastName=$dischargeDetails->signSurgeon1LastName;
		$signSurgeon1Status=$dischargeDetails->signSurgeon1Status;
		
		$signSurgeon1DateTime= $dischargeDetails->signSurgeon1DateTime; 
		if($signSurgeon1DateTime<>'0000-00-00 00:00:00') {
			$date_surgeon=explode(' ',$signSurgeon1DateTime);
			$date_sign=explode('-',$date_surgeon[0]);
			$date_surgeon_sign= $date_sign[1].'/'.$date_sign[2].'/'.$date_sign[0];
		}
		if($form_status=='completed')
		{
		  $display_tr="block";
		}
		else
		{
		   $display_tr="none";
		}
	}
//GETTING IF ALREADY EXISTS


//get site code
$getSiteCodeQ=imw_query("SELECT id FROM  `icd10_laterality` where title in ('Site','Site with Lid')");
$siteCatIdArr = array();
while($getSiteCodeD=imw_fetch_assoc($getSiteCodeQ)){
	$siteCatIdArr[] = $getSiteCodeD["id"];	
}
$siteCatIdImp = '1';
if(count($siteCatIdArr)>0) {
	$siteCatIdImp = implode(",",$siteCatIdArr);	
}
//get site under codes
//"SELECT code,title FROM  `icd10_laterality` where under='$getSiteCodeD->id' and deleted=0"
$getSiteCodeUQ=imw_query("SELECT code,title FROM  `icd10_laterality` where under in (".$siteCatIdImp.") and deleted=0");
while($getSiteCodeUD = imw_fetch_object($getSiteCodeUQ))
{
	$siteCode = $getSiteCodeUD->code;
	$siteCodeTitle = strtolower($getSiteCodeUD->title);
	/*
	if($siteCodeTitle=='left upper lid') {
		$siteCode = '4';	
	}else if($siteCodeTitle=='left lower lid') {
		$siteCode = '5';	
	}else if($siteCodeTitle=='right upper lid') {
		$siteCode = '6';	
	}else if($siteCodeTitle=='right lower lid') {
		$siteCode = '7';	
	}else if($siteCodeTitle=='bilateral upper lid') {
		$siteCode = '8';	
	}else if($siteCodeTitle=='bilateral lower lid') {
		$siteCode = '9';	
	}*/
	$siteArr[strtolower($getSiteCodeUD->title)]=$siteCode;
}

//get typ of dx codes
$queryDxTyp=imw_query("select `diagnosis_code_type`, discharge_disclaimer, autofill_modifiers from `surgerycenter` Where surgeryCenterId = 1")or die('Error @ Line :'.(__LINE__).':-'.imw_error());
$dataDxTyp=imw_fetch_object($queryDxTyp);
$autofill_modifiers = $dataDxTyp->autofill_modifiers;
if($dataDxTyp->diagnosis_code_type)
{
	$dx_code_type=$dataDxTyp->diagnosis_code_type;
}
else $dx_code_type='icd9';
$defaultDischargeTxt = stripslashes($dataDxTyp->discharge_disclaimer);

if(trim($diag_idDB)!='' || ($dos <= '2015-09-30' && trim($icd10_idDB)=='')) {
	$dx_code_type='icd9';	
	
}else if(trim($icd10_idDB)!='') {
	$dx_code_type='icd10';	
}

if($dx_code_type=='icd10')
{
	$query=imw_query("SELECT code,title,under FROM  `icd10_laterality` where under!='' and deleted=0 Order By code");
	while($data=imw_fetch_object($query))
	{
		$icdLater[$data->under][$data->code]=$data->title.'~:~'.$data->code;
	}
}

//UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE
$ViewOpRoomRecordQry = "select operatingRoomRecordsId,iol_ScanUpload,iol_ScanUpload2,post2DischargeSummary from `operatingroomrecords` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error()); 
$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
if($ViewOpRoomRecordNumRow>0) {
			$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
			$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
			$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
			$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
			$post2DischargeSummary = $ViewOpRoomRecordRow["post2DischargeSummary"];
		}
//UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE

//START GET ASA CODE FROM GLOBAL FILE BASED ON ASA CHECK-MARKED IN LOCAL ANESTHESIA
$asaModifierCode = "";
$localAnesQry = "select asaPhysicalStatus from `localanesthesiarecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
$localAnesRes = imw_query($localAnesQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error().$localAnesQry); 
if(imw_num_rows($localAnesRes)>0) {
	$localAnesRow = imw_fetch_assoc($localAnesRes); 
	if(trim($localAnesRow["asaPhysicalStatus"])!="" && trim(constant("ASA_MODIFIER_CODE"))!="") {
		$asaModifierCode = constant("ASA_MODIFIER_CODE"); 	
	}
}
//END GET ASA CODE FROM GLOBAL FILE BASED ON ASA CHECK-MARKED IN LOCAL ANESTHESIA

?>
<script type="text/javascript">
function restPos() {
    var p = $("#below_summary_dummy");
	var offset = p.offset();
	//alert("top: "+offset.top+", left: "+offset.left);
	$("#below_summary_dummy").html("");
	//$("#below_summary").offset({ top: offset.top, left: offset.left });
	$("#below_summary").css({"left":+offset.left,"top":+offset.top});

}	
	function changeSliderColor(){
		var setColor = '<?php echo $rowcolor_discharge_summary_sheet; ?>';
		top.changeColor(setColor);
	}
	//top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');

//START FUNCTION TO SCAN/UPLOAD DISCHARGE SUMMARY SHEET
function startCallback() {	
	return true;
}
function completeCallback(response){
	setTimeout('getImage()', 1000);
}
function getImage(){
	var dischargeSummarySheetId = <?php echo $dischargeSummarySheetIdDB; ?>;
	//document.frames['iframeIOL'].location.reload();
	document.getElementById('iframeIOL').src="discharge_summary_image.php?dischargeSummarySheetId="+dischargeSummarySheetId;
	var objFrm = top.mainFrame.main_frmInner.document.frm_uploadDISImage;
	if(objFrm.hidd_delImage.value=='yes' || objFrm.hidd_delImage.value=='yes2') {
		//alert(objFrm.hidd_delImage.value);
		
		//if(!top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgThumbNail') && !top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgThumbNail2')){
			//document.getElementById('iframeIOL').style.height = '0px';
			//document.getElementById('iframeIOL').style.width = '0px';
			top.mainFrame.main_frmInner.document.frm_uploadDISImage.hidd_delImage.value='';
		//}
	}else {
		document.getElementById('iframeIOL').style.height = '150px';
		document.getElementById('iframeIOL').style.width = '100%';
		top.mainFrame.main_frmInner.document.frm_uploadDISImage.hidd_delImage.value='';
	}	
}
function showImgDiv(){
	document.getElementById('imgDiv').style.display = 'block';
}

//END FUNCTION TO SCAN/UIPLOAD DISCHARGE SUMMARY SHEET



//Applet
	function get_App_Coords(objElem){
		var coords,appName;
		var objElemSign = document.frm_pre_op_nurs_rec.elem_signature;
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
		var coords = document.applets["app_signature"].getSign();
		return coords;
	}
	function getclear_os(){
		document.applets["app_signature"].clearIt();
		changeColorThis(255,0,0);
		document.applets["app_signature"].onmouseout();
	}
	function changeColorThis(r,g,b){				
		document.applets['app_signature'].setDrawColor(r,g,b);								
	}
//Applet
function backGColor(idC, rowcolor, modC){
	/*
	if(modC == 0){
		bgC = rowcolor;
	}else{
		bgC = "#FFFFFF";
	}
	var lastColor = document.getElementById("lastTrC").value;
	var lastTr = document.getElementById("lastTrId").value;
	if((lastColor!='') && (lastTr!='')){
		document.getElementById(lastTr).style.background = lastColor;
	}
	document.getElementById("lastTrC").value = bgC;
	document.getElementById("lastTrId").value = "tr"+idC;	
	document.getElementById("tr"+idC).style.background = '#FFFFCC';
	*/
}

function backBgBlueColor(chbxId,rowId,prevColorId) {
	var obj_chbxId = document.getElementById(chbxId);
	var obj_rowId = document.getElementById(rowId);
	if(obj_chbxId) {
		if(obj_chbxId.checked==true) {
			//obj_rowId.data-checked="true";
			if(obj_rowId) {
				//obj_rowId.style.background = '#AED5F4';
				//obj_rowId.className = 'text_10b';
				$("#"+rowId).addClass("list-group-item-primary active");
				$("#"+rowId).find('div[id$="add_icd10"]').removeClass('hidden');
			}
		}else if(obj_chbxId.checked==false) {
			if(obj_rowId) {
				//obj_rowId.style.background = prevColorId;
				//obj_rowId.className = 'text_10';
				$("#"+rowId).removeClass("list-group-item-primary active");
				$("#"+rowId).find('div[id$="add_icd10"]').addClass('hidden');
			}
		}
	}
}
function tempDis(primary_id,procedure_id,sec_id,top) {
	if(primary_id!=procedure_id) { 
		if(sec_id!="0")
		{
			if(sec_id!=procedure_id)
			{
				document.getElementById('divprocedure').style.display = 'block';
				document.getElementById('divprocedure').style.top = top;
			}
		}
	}
}


//FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE
	
	function noAuthorityFun(userName) {
		alert("You are not authorised to make signature of "+userName);
		return false;
	}
	function noSignInAdmin() {
		alert("You have yet to make signature in Admin");
		return false;
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
	
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {

		//START TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			var signCheck='true';
			var assignedSurgeonId = '<?php echo $dischargeSummaryAssignedSurgeonId;?>';
			var assignedSurgeonName = '<?php echo $dischargeSummaryAssignedSurgeonName;?>';
			var loggedInUserType = '<?php echo $loggedUserType;?>';
			if(loggedInUserId!=assignedSurgeonId && !delSign && loggedInUserType=='Surgeon') {
				var rCheck='';
				//var rCheck = confirmOtherSurgeon("This patient is registered to Dr. "+assignedSurgeonName+"\t\t\t\tAre you sure you want to sign the Chart notes of this patient");
				if(rCheck==false) {
					signCheck='false';
				}else {
					signCheck='true';
				}
				
			}
		//END TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
		
		if(delSign) {
			document.getElementById(TDUserNameId).style.display = 'block';
			document.getElementById(TDUserSignatureId).style.display = 'none';
			
			//SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
			if(userIdentity=='Surgeon1'){
				if(top.document.forms[0]){
					if(top.document.forms[0].hidd_chkDisplaySurgeonSign) {
						top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'true';
					}
				}
			}		
			//END SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
		
		}else {
			if(signCheck=='true') {
				document.getElementById(TDUserNameId).style.display = 'none';
				document.getElementById(TDUserSignatureId).style.display = 'block';
			}	
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
		if(signCheck=='true') { //TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			xmlHttp.onreadystatechange=displayUserSignFun;
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
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

	function MM_openBrOpRoomWindow(theURL,winName,features) {
	  window.open(theURL,winName,features);
	}		
	function showImgWindow(strImgNumber) {
		if(!strImgNumber) {
			strImgNumber = '';
		}
		var opRoomId = '<?php echo $operatingRoomRecordsId;?>';
		MM_openBrOpRoomWindow('opRoomImagePopUp.php?from=op_room_record&id='+opRoomId+'&imgNmbr='+strImgNumber,'OpRoomImage','scrollbars=yes,width=900,height=400,resizable=yes,location=yes,status=yes');
	}

	function showHideScanDiv(obj_id,divID) {
		if(document.getElementById(obj_id)) {
			if(document.getElementById(obj_id).checked==true) {
				document.getElementById(divID).style.display='block';
				/*var attachedIdPos = '3';
				if(document.getElementById('chbx_disAttachedId')) {
					attachedIdPos =parseInt(findPos_Y('chbx_disAttachedId'))+25;	
				}
				if(document.getElementById('seeAttachId')) {
					document.getElementById('seeAttachId').style.display='block';
				}
				if(document.getElementById('imageTd')) {
					document.getElementById('imageTd').style.display='block';
				}
				if(document.getElementById('uploadImageDivId')) {
					document.getElementById('uploadImageDivId').style.display='block';
					document.getElementById('uploadImageDivId').style.top=attachedIdPos+'px';
				}
				if(document.getElementById('uploadBtnDivId')) {
					document.getElementById('uploadBtnDivId').style.display='block';
					document.getElementById('uploadBtnDivId').style.top=attachedIdPos+'px';
				}*/
			}else if(document.getElementById(obj_id).checked==false) {
				
				document.getElementById(divID).style.display='none';
				/*if(document.getElementById('seeAttachId')) {
					document.getElementById('seeAttachId').style.display='none';
				}
				if(document.getElementById('imageTd')) {
					document.getElementById('imageTd').style.display='none';
				}
				if(document.getElementById('uploadImageDivId')) {
					document.getElementById('uploadImageDivId').style.display='none';
				}
				if(document.getElementById('uploadBtnDivId')) {
					document.getElementById('uploadBtnDivId').style.display='none';
				}*/
			}
		}
	}
</script>

<div id="post" style="display:none; position:absolute;"></div>
<?php
// Get IDOC MODIFIERS 
$idoc_mod = idoc_modifiers();//print_r($idoc_mod);

// Get PROCEDURE UNITS
$proc_units = procedure_units();

// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
// GETTING FINALIZE STATUS
//show all epost ravi
?>
<script type="text/javascript">
	dragresize.apply(document);
</script>
<?php
	$dx_code_table	=		($dx_code_type == 'icd10') ? '' : '' ;
	
	
	//START TO COUNT ALL
            
			// Start code to get unique procedure from DB & Template
				$andProcQry="";
				if($cpt_id) {  
					$cpt_id=(substr($cpt_id,-1,1)==",") ? substr($cpt_id,0,(strlen($cpt_id)-1)) : $cpt_id;
					$cpt_id=($procedures_IdDB)	?	implode(',',array_filter(array_unique(explode(",",$cpt_id . ','.$procedures_IdDB))))	:	$cpt_id;
					$andProcQry = " AND procedureId IN(".$cpt_id.") ";
				}
			// End code to get unique procedure from DB & Template
			
			// *** Procedure Categories
            $countProcedureCat = array();	
            $getProcedureCatQry = "select * from `procedurescategory`";
            $getProcedureCatRes = imw_query($getProcedureCatQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error()); 
            while($getProcedureCatRow = imw_fetch_array($getProcedureCatRes)){
                $countProcedureCat[] = $getProcedureCatRow['proceduresCategoryId'];
            }
            if(is_array($countProcedureCat)){
                $procedureCategoryImplode =implode(',',$countProcedureCat);
            }
			
			/* Procedure List Based on categories and filtered from DB & Template	*/
            if($procedureCategoryImplode){
                $getProcedureQry = "select * from `procedures` where catId IN($procedureCategoryImplode) ".$andProcQry;
                $getProcedureRes = imw_query($getProcedureQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error()); 
                $getProcedureNumRow = imw_num_rows($getProcedureRes);
                if($getProcedureNumRow){
                    while($getProcedureRow = imw_fetch_array($getProcedureRes)){
                        $countProcedureId[] = $getProcedureRow['procedureId'];
                    }
                    if(is_array($countProcedureId)){
                        $procedureIdImplode =implode(',',$countProcedureId);
                    }	
                }
            }
           
		   //start code to pre-select the procedure code according to sugeon profile
            if($cpt_id_default && (count($procedures_idDBExplodeArr)<=0 || ($form_status!='completed' && $form_status!='not completed'))) {
				$procedures_idDBExplodeArr = explode(",",$cpt_id_default);
                $procedures_IdDB = $cpt_id_default;
            }
			//end code to pre-select the procedure code according to sugeon profile
            
            
            $DischargeBackColor=$chngBckGroundColor;
            if($procedures_IdDB || $otherMiscellaneousDB || $comment || $other1DB || $other2DB ||($disAttached && ($dis_ScanUpload || $dis_ScanUpload2)) ){
                 $DischargeBackColor=$whiteBckGroundColor; 
            }
        //END TO COUNT ALL	
		
		//*******Start Get All Procedures in array;
		unset($condArr);
		$condArr['1'] = '1';
		$condArr['del_status'] = '';
		//$CPTCodesData	=	$objManageData->getMultiChkArrayRecords('procedures',$condArr,'catId=20 Desc,catId,code,name','ASC',' and  procedureId Not IN ('.$procedureIdImplode.')');
		$CPTCodesData	=	$objManageData->getMultiChkArrayRecords('procedures',$condArr,'catId=20 Desc,name,code,catId','ASC',' and  procedureId Not IN ('.$procedureIdImplode.')');
		foreach($CPTCodesData as $CCA)
		{
			
			$CPTCodesArray[$CCA->procedureId]	=	array(	'name'=>$CCA->name,
																							'catId'=>$CCA->catId,
																							'code'=>$CCA->code,
																							'procedureAlias'=>$CCA->procedureAlias,
																							'del_status'=>$CCA->del_status,
																							'specialty_id'=>$CCA->specialty_id,
																							'codeFacility'=>$CCA->codeFacility,
																							'codePractice'=>$CCA->codePractice	);
		}
		//echo 'Count is '.count($CPTCodesArray).'<pre>'; print_r($CPTCodesArray);
	
	// End Get All Procedures in array.
		
?>		

<form class="wufoo topLabel" enctype="multipart/form-data" action="discharge_summary_sheet.php?saveRecord=true" name="frm_pre_op_nurs_rec" method="post" style="margin:0px;">	
<input type="hidden" name="dx_code_type" id="dx_code_type" value="<?php echo $dx_code_type;?>">
<input type="hidden" name="divId">
<input type="hidden" name="counter">
<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
<input type="hidden" name="dischargeSummarySheetIdDB" value="<?php echo $dischargeSummarySheetIdDB; ?>">	
<input type="hidden" name="getText" id="getText">
<input type="hidden" name="scan" id="scan">
<input type="hidden" name="SaveRecordForm" value="yes"> 
<input type="hidden" name="formIdentity" value="">
<input type="hidden" name="lastTrC">
<input type="hidden" name="lastTrId">
<input type="hidden" name="scan">
<input type="hidden" id="hiddPopUpField">
<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
<input type="hidden" name="go_pageval" value="<?php echo $tablename;?>">
<input type="hidden" name="frmAction" id="frmAction" value="discharge_summary_sheet.php">
<input type="hidden" name="SaveForm_alert" value="true">

<input type="hidden" id="primary_procedure_id" value="<?=$primary_procedure_id?>" />
<input type="hidden" id="secondary_procedure_id" value="<?=$secondary_procedure_id?>" />
<input type="hidden" id="procedureIdImplode" value="<?=$procedureIdImplode?>" />
<input type="hidden" id="TotalRows" value="0" />

	<!--slider_content-->
    <div class=" scheduler_table_Complete slider_content" id="" style="">
    	<?php
				$epost_table_name = "dischargesummarysheet";
				include("./epost_list.php");
		?>
        
        <div id="divSaveAlert" style="position:absolute; left:40%; display:none; z-index:1000; padding:10px; display:none">
            <?php 
					$bgCol = $title_discharge_summary_sheet;
					$borderCol = '#FF6600';
                include('saveDivPopUp.php'); 
            ?>
        </div>
        
        <div id="divprocedure" style="position:absolute; left:40%; display:none; z-index:1000; padding:10px">
            <?php 
                $bgCol = $title_discharge_summary_sheet;
                $borderCol = '#FF6600';
                include('Procedurediv.php'); 
            ?>
		</div>
		<?php							
            
        
        ?>
        <!-- GETTING PROCEDURES -->
         <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
          <!-- Start Html to Add Extra Cpt Codes Here	-->
        <?PHP	
			if(is_array($CPTCodesArray) && count($CPTCodesArray) > 0)
			{
		?>
        		<div id="" class="inner_safety_wrap">
                		<div class="row">
                        		<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">&nbsp;</div>
                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                	<select class="form-control selectpicker AdditionalCode" title="Add Cpt Codes" data-title="Add CPT Codes" data-live-search="true" data-style="btn-primary">
                                    <?php
									
                            	 		foreach($CPTCodesArray as $CPTCodeId =>$CPT)
										{
											if($CPT['del_status'] <> 'yes' )
											{
												//$txt	=	($CPT['code']	? '('.$CPT['code'].')' : '' ).$CPT['name'];
												$txt	=	($CPT['code']	? $CPT['code'].' - ' : '' ).$CPT['name'];
												$val	=	$CPTCodeId ;
												$dCatID	=	$CPT['catId'];
									?>
                            					<option value="<?=$val?>" data-cat-id="<?=$dCatID?>" data-proc-code ="<?=$CPT['code']?>"  data-proc-name="<?=$CPT['name']?>" data-proc-surgeon="<?=$CPT['codePractice']?>" data-proc-facility="<?=$CPT['codeFacility'];?>" data-proc-stop_parent_superbill="<?=constant('STOP_PARENT_SUPERBILL');?>"><?=$txt?></option>
                         			<?php 
											}
										}
									?>	
									</select>
               					</div>
             			</div>
          		</div>
       	<?php
			}
		?>
      	<!-- End Html to Add Extra Cpt Codes Here	-->
        <?php
        $getProcedureCats = $objManageData->getArrayRecords('procedurescategory','','','name','Asc');
		if(count($getProcedureCats)>0){
			//Start code to set category of G-Codes at the top
			$gCodeKey ='';
			foreach($getProcedureCats as $keyCt=> $categry){
				if((strtolower($categry->name))=='g-codes'){
					$gCodeKey = $keyCt;break;
				}
			}
			if($gCodeKey) {
				$getProcedureCatsTemp[$gCodeKey]=$getProcedureCats[$gCodeKey];
				unset($getProcedureCats[$gCodeKey]);
				array_unshift($getProcedureCats, $getProcedureCatsTemp[$gCodeKey]);
			}
			//End code to set category of G-Codes at the top
			foreach($getProcedureCats as $categry){
				$procCategoryId = $categry->proceduresCategoryId;
				$procCategory 	= $categry->name;
				//$cpt_id_default 		= $selectSurgeonProcedureRow['cpt_id_default'];
				$getProcedureQry = "SELECT procedureId,name,code,del_status,codePractice,codeFacility FROM procedures WHERE catId='".$procCategoryId."' $andProcQry ORDER BY code ASC, name ASC";
				$getProcedureRes = imw_query($getProcedureQry) or die($getProcedureQry.imw_error());
				$getProceduresDetails = array();
				if($getProcedureRes){
					while($getProcedureRow = imw_fetch_object($getProcedureRes)){
						if($procNameArray[$getProcedureRow->procedureId] && $getProcedureRow->name <> $procNameArray[$getProcedureRow->procedureId])
						{
							$getProcedureRow->name = $procNameArray[$getProcedureRow->procedureId];
						}
						if($procCodeNameArray[$getProcedureRow->procedureId] && $getProcedureRow->code <> $procCodeNameArray[$getProcedureRow->procedureId])
						{
							$getProcedureRow->code = $procCodeNameArray[$getProcedureRow->procedureId];
						}
						
						$getProceduresDetails[] = $getProcedureRow;
					}		
				}
				//$getProceduresDetails = $objManageData->getArrayRecords('procedures', 'catId', $procCategoryId,'code, name', 'ASC' );						
				
				$panelDisplayStatus	=	'none';
				if(count($getProceduresDetails)>0) 
					$panelDisplayStatus	=	'block'		
				
					?>
                   
                        <div class="panel panel-default bg_panel_sum" style="display:<?=$panelDisplayStatus?>" data-category="<?=$procCategory?>"  id="CPT_<?=$procCategoryId?>">
                            <div class="panel-heading haed_p_clickable ">
                                <h3 class="panel-title rob"> <?php echo $procCategory; ?> </h3>
                                <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
                           </div>
                            <div class="panel-body">
                            	<ul class="list-group checked-list-box" >
                                	<?php
										foreach($getProceduresDetails as $Procedures)
										{
											$Procedures_id 			= stripslashes($Procedures->procedureId);
											$Procedures_name 		= stripslashes($Procedures->name);
											$Procedures_code 		= stripslashes($Procedures->code);
											$Procedures_del_status 	= stripslashes($Procedures->del_status);
											$Procedures_code_practice= trim(stripslashes($Procedures->codePractice));
											$Procedures_code_facility= trim(stripslashes($Procedures->codeFacility));
											
											if($Procedures_del_status!='yes' || in_array($Procedures_id,$procedures_idDBExplodeArr)) 
											{
												++$c;
												$modOfC = $c%2;
												
												if($c%2==0) {	$rowcolorDischargeSummarySheet = $rowcolor_discharge_summary_sheet; }
												else  { $rowcolorDischargeSummarySheet ='#FFFFFF'; }
												
											//	unset($CPTCodesArray[$Procedures_id]);		//	Unset Cpt From Main CPT Array
												
											
									?>
                                            	<li class="list-group-item full_width"  id="tr<?php echo $c; ?>" onClick="backBgBlueColor('chbx_fdt_no<?php echo $Procedures_id;?>','tr<?php echo $c;?>','<?php echo $rowcolorDischargeSummarySheet;?>')"> <!--list-group-item-primary active-->
                                           		<input  type="hidden" name="procNames[<?php echo $Procedures_id;?>]" value="<?php echo $Procedures_name; ?>" />
                                                <input  type="hidden" name="procCodeNames[<?php echo $Procedures_id;?>]" value="<?php echo $Procedures_code; ?>" />
                                                <input  type="hidden" id="procedureCode_<?=$Procedures_id?>" value="<?php echo $Procedures_code; ?>" />   
                                           		<div class="col-md-1 col-sm-1 col-xs-1 col-lg-1  text-left">
                                                    <span class="clpChkBx colorChkBx" style=" <?php echo $DischargeBackColor;?>">
                                                    <input type="checkbox" <?php if(count($procedures_idDBExplodeArr)>0) if(in_array($Procedures_id, $procedures_idDBExplodeArr)) echo "CHECKED";?> value="<?php echo $Procedures_id; ?>" name="chbx_fdt[<?php echo $Procedures_id; ?>]" id="chbx_fdt_no<?php echo $Procedures_id; ?>" onClick="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');return tempDis('<?php echo $primary_procedure_id;?>','<?php echo $Procedures_id;?>','<?php echo $secondary_procedure_id;?>','<?php echo $top=$c*20;?>');" data-category="<?=$procCategory?>" data-surgeon-code="<?=$Procedures_code_practice?>" data-facility-code="<?=$Procedures_code_facility;?>" data-stop_parent_superbill="<?=constant('STOP_PARENT_SUPERBILL');?>" ></span>
                                                </div>
                                                
												<b class="padding_0 col-md-4 col-sm-4 col-xs-4 col-lg-4 text-left" onClick="checkThisChecked('chbx_fdt_no<?php echo $Procedures_id;?>','tr<?php echo $c;?>','<?php echo $rowcolorDischargeSummarySheet;?>');changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');return tempDis('<?php echo $primary_procedure_id;?>','<?php echo $Procedures_id;?>','<?php echo $secondary_procedure_id;?>','<?php echo $top=$c*20;?>');"> 
												<?php echo $Procedures_code; ?>
                                                </b>
                                                
                                                <span class="col-md-7 col-sm-7 col-xs-7 col-lg-7 text-left text_list" onClick="checkThisChecked('chbx_fdt_no<?php echo $Procedures_id;?>','tr<?php echo $c;?>','<?php echo $rowcolorDischargeSummarySheet;?>');changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');return tempDis('<?php echo $primary_procedure_id;?>','<?php echo $Procedures_id;?>','<?php echo $secondary_procedure_id;?>','<?php echo $top=$c*20;?>');">
                                                <?php echo $Procedures_name;?>
                                                </span>
                                            </li>
                                            <div class="clearfix"></div>
											<?php
											//START CODE TO CHANGE BGCOLOR OF SELECTED ROW AND MAKE TEXT IN BOLD
											
											if(count($procedures_idDBExplodeArr)>0) {
												if(in_array($Procedures_id, $procedures_idDBExplodeArr)) {
													echo "<script>backBgBlueColor('chbx_fdt_no".$Procedures_id."','tr".$c."','".$rowcolorDischargeSummarySheet."')</script>";
												}
											}
											//END CODE TO CHANGE BGCOLOR OF SELECTED ROW AND MAKE TEXT IN BOLD
										}
									}
									
									?>
                                </ul>
                                <!--</table>-->
                            
                            </div>   
                        </div>           
                        <div class="clearfix"></div>
					<?php
				
				if(trim($procCategory) == 'Miscellaneous' && $otherMiscellaneousDB){
					?>
                    <div class="panel panel-default bg_panel_sum">
                    <?php
						if(count($getProceduresDetails)<=0){
					?>
                            <div class="panel-heading haed_p_clickable ">
                            <h3 class="panel-title rob"> <?php echo $procCategory; ?> </h3>
                            <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
                       		</div>
					<?php
						}
					?> <div class="panel-body">
                    	<div class="clearfix margin_adjustment_only"></div> 
                                <div class="row">
                                    <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                        <label class="date_r"> Other </label>
                                    </div>
                                    <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                        
                                        <textarea id="Field1" name="miscellaneousOther" onFocus="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');" onBlur="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');" onKeyUp="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');"  class="form-control" style="resize:none; <?php echo $DischargeBackColor;?>"><?php echo stripslashes($otherMiscellaneousDB); ?></textarea> 
                                    </div> <!-- Col-3 ends  -->
                                </div>
                         </div>
                   
                    </div>
					<?php
					}
					?>
                    
                    <?php
			}
			
		}  //end of checking procedure cats      
		?>
        <div id="" class="inner_safety_wrap">
                    <div class="well">
                    <div class="row">
                            <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                <label class="date_r"> Comments </label>
                            </div>
                            <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                
                                <textarea id="comment_id" name="comment" onFocus="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');" onBlur="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');" onKeyUp="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');"  class="form-control" style="resize:none; <?php echo $DischargeBackColor;?>"><?php echo stripslashes($comment); ?></textarea> 
                            </div> <!-- Col-3 ends  -->
                        </div>
                    </div>
                    </div>
		
        
       <input type="hidden" name="deleteSuperBillRecords" id="deleteSuperBillRecords" />
       <?php
			 	$printSuperBills = array('Surgeon_S_2','Facility_F_3','Anesthesia_A_1');
				$superBillHtml = '';
				foreach($printSuperBills as $object)
				{
					$arr = explode('_',$object);
					$buTitle = $arr[0]; $buStr = $arr[1]; $buType= $arr[2];
					
					$superBills = array();
					$qrySuperbill =	"SELECT sb.* FROM superbill_tbl sb 
														INNER JOIN procedures pr ON(pr.procedureId = sb.cpt_id)
														INNER JOIN procedurescategory prc ON(prc.proceduresCategoryId = pr.catId)
														WHERE sb.confirmation_id = '".$pConfId."'
														AND sb.deleted = '0'
														And bill_user_type = '".$buType."'
														ORDER BY prc.name = 'G-Codes' DESC, sb.cpt_code";
					$resSuperbill = imw_query($qrySuperbill); 
					if($resSuperbill){
						while($rowSuperbill = imw_fetch_object($resSuperbill)){
							$superBills[] = $rowSuperbill;
						}
					}
					
					$superBillHtml .= '
						<div class="panel panel-default bg_panel_sum">
       				<div class="panel-heading haed_p_clickable">
            		<h3 class="panel-title rob"> Discharge Summary ('.$buTitle.')</h3>
            		<span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
            	</div>
							<div class="panel-body" id="SuperBillPanel">
                <ul class="list-group checked-list-box" id="SuperBill'.$buTitle.'">		
									<input type="hidden" id="superBillHeaderDisplayStatus'.$buStr.'" value="0" />';
									if(is_array($superBills) && count($superBills) > 0)
									{
										$superBillHead	='';
										$superBillHead	.=	'<li class="list-group-item full_width padding_0" id="superBillHeader'.$buStr.'">';
										$superBillHead	.=	'<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">';
										$superBillHead	.=	'<span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 "><b>CPT Codes</b></span>';
										$superBillHead	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 "><b>Unit</b></span>';
										$superBillHead	.=	'<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 "><b>Dx Codes</b></span>';
										$superBillHead	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 "><b>Mod1</b></span>';
										$superBillHead	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 "><b>Mod2</b></span>';
										$superBillHead	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 "><b>Mod3</b></span>';
										$superBillHead	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_0 ">&nbsp;</span>';
										$superBillHead	.=	'</div>';
										$superBillHead	.=	'</li>';
                   	$superBillHtml .= $superBillHead;
										
										foreach($superBills as $superBill)
										{
											$suffix  = $superBill->cpt_id.'_'.$superBill->bill_user_type;
											$superBillHtml	.=	'<li class="list-group-item full_width padding_0" data-cpt-id= "'.$superBill->cpt_id.'">';
											$superBillHtml	.=	'<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">';
											
											$superBillHtml	.=	'<span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 padding_2 "  >';
											$superBillHtml	.=	'<input type="hidden" name="superBillRecordId[]" id="superBillRecordId_'.$suffix.'" value="'.$superBill->superbill_id.'"  />';
											$superBillHtml	.=	'<input type="hidden" name="billUserType[]" id="billUserType_'.$suffix.'" value="'.$superBill->bill_user_type.'" />';
											$superBillHtml	.=	'<input type="hidden" name="superBillCptId[]" id="superBillCptId_'.$suffix.'" value="'.$superBill->cpt_id.'"  />';
											$superBillHtml	.=	'<input type="text" class="form-control" readonly name="superBillCptCodes[]" id="cptCode_'.$suffix.'" value="'.$superBill->cpt_code.'" />';
											$superBillHtml	.=	'</span>';
										
											$superBillHtml	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 ">';
											$superBillHtml	.=	'<input type="text" class="form-control" name="superBillQuantity[]" id="quantity_'.$suffix.'" min="1" value="'.($superBill->quantity ? $superBill->quantity : '').'" />';
											$superBillHtml	.=	'</span>';
											
											$superBillHtml	.=	'<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 padding_2">';
											$superBillHtml	.=	'<select name="superBillDxCodes_'.$suffix.'[]" id="dxCodes_'.$suffix.'" class="selectpicker" multiple title="Dx Codes" data-header="Dx Codes">';
											$SB_DxCodes	=	($dx_code_type == 'icd10') ? $superBill->dxcode_icd10 : $superBill->dxcode_icd9 ;
											$SB_DxCodes	=	explode(',',$SB_DxCodes);
											if(is_array($SB_DxCodes) && count($SB_DxCodes) > 0 )
											{
													foreach($SB_DxCodes as $DxVal)
													{
															$superBillHtml	.=	'<option value="'.$DxVal.'" selected>'.$DxVal.'</option>';	
													}
											}
											$superBillHtml	.=	'</select>';
											$superBillHtml	.=	'</span>';
											
											$superBillHtml	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 ">';
											$superBillHtml	.=	'<input type="text" class="form-control padding_2 modifiers" name="superBillModifier1[]" id="modifier1_'.$suffix.'" placeholder="Mod1" value="'.$superBill->modifier1.'" />';
											$superBillHtml	.=	'</span>';
											
											$superBillHtml	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 ">';
											$superBillHtml	.=	'<input type="text" class="form-control padding_2 modifiers" name="superBillModifier2[]" id="modifier2_'.$suffix.'" placeholder="Mod2" value="'.$superBill->modifier2.'" />';
											$superBillHtml	.=	'</span>';
											
											$superBillHtml	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 ">';
											$superBillHtml	.=	'<input type="text" class="form-control padding_2 modifiers" name="superBillModifier3[]" id="modifier3_'.$suffix.'" placeholder="Mod3" value="'.$superBill->modifier3.'" />';
											$superBillHtml	.=	'</span>';
											
											$superBillHtml	.=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_0 " style="padding-top:7px !important;" >';
											$superBillHtml	.=	'<a class="fa fa-times-circle-o" style="color:#e5870b; font-size:22px; margin-left:10px;" title="Remove" id="removeCpt_'.$suffix.'"></a>';
											$superBillHtml	.=	'</span>';
											
											$superBillHtml	.=	'</div>';
											$superBillHtml	.=	'</li>';		
															
											}
									
										
								}
					
					$superBillHtml .= '						
								</ul>
							</div>
						</div>				
					';	
					
				}
			 
			 	echo $superBillHtml;
			 ?>
       
       
                    
        </div>					
        <!-- END OF GETTING PROCEDURES -->
        
        <!-- GETTING DX CODES -->
        <?php
        if($dx_code_type=='icd10')
		{
			$icd10Qry="";
			if($dx_id_icd10) 
			{ 
				$dx_id_icd10=	($icd10_idDB)	?	implode(',',array_filter(array_unique(explode(",",$dx_id_icd10 . ','.$icd10_idDB))))	:	$dx_id_icd10;
				$icd10Qry = " AND id IN(".$dx_id_icd10.") "; 
			}

			unset($getDiagnosisDetailsTemp);
			//code to get ICD10 related to ICD9
			$icd10Query=imw_query("select * from icd10_data WHERE 1=1 AND icd10 !='' ".$icd10Qry." order by icd10 asc");
			while($icd10Data=imw_fetch_object($icd10Query))
			{
				$icd10Data->icd9=trim($icd10Data->icd9);
				$Icd10[$icd10Data->icd9]['icd10']=trim($icd10Data->icd10);
				$Icd10[$icd10Data->icd9]['laterality']=trim($icd10Data->laterality);
				$Icd10[$icd10Data->icd9]['staging']=trim($icd10Data->staging);
				$Icd10[$icd10Data->icd9]['severity']=trim($icd10Data->severity);	
				
				$getDiagnosisDetails[] = $icd10Data;
				
			}
		}
		else
		{
			$andDiagQry="";
			if($dx_id) 
			{ 
				$dx_id=(substr($dx_id,-1,1)==",") ? substr($dx_id,0,(strlen($dx_id)-1)) : $dx_id;
				$dx_id=	($diag_idDB)	?	implode(',',array_filter(array_unique(explode(",",$dx_id . ','.$diag_idDB))))	:	$dx_id;
				$andDiagQry = " AND diag_id IN(".$dx_id.") ";
			}

			$getDiagnosisQry = "SELECT diag_id,diag_code,del_status FROM diagnosis_tbl WHERE 1=1 ".$andDiagQry." ORDER BY diag_code ASC";
			$getDiagnosisRes = imw_query($getDiagnosisQry) or die($getDiagnosisQry.imw_error());
			$getDiagnosisDetails = array();
			if($getDiagnosisRes){
				while($getDiagnosisRow = imw_fetch_object($getDiagnosisRes)){
					$getDiagnosisDetails[] = $getDiagnosisRow;
				}		
			}
		}
		
		//start code to pre-select the diagnosis-icd9 code according to sugeon profile
		if($dx_id_default && (count($diag_idDBExplodeArr)<=0) || ($form_status!='completed' && $form_status!='not completed')) {
			$diag_idDBExplodeArr = explode(",",$dx_id_default);
		}
		
		//start code to pre-select the diagnosis-icd10 code according to sugeon profile
		if($dx_id_default_icd10 && (count($icd10_idDBExplodeArr)<=0) || ($form_status!='completed' && $form_status!='not completed')) {
			$icd10_idDBExplodeArr = explode(",",$dx_id_default_icd10);
			$default_icd_10=true;
		} else $default_icd_10=false;

	
		//*********Start Get All ICD 9 Codes in array
		unset($condArr);
		$condArr['1'] = '1';
		$condArr['del_status'] = '';
		
		$ICD9CodesData	=	$objManageData->getMultiChkArrayRecords('diagnosis_tbl',$condArr,'diag_code','Asc'," AND diag_id NOT IN($dx_id)");	
		foreach($ICD9CodesData as $ICD9)
		{
				$dataArray	=	array();	
				foreach($ICD9 as $fieldName=>$fieldValue)
				{
					$dataArray[$fieldName]	=	$fieldValue ;
				}
				$ICD9CodesArray[$ICD9->diag_id]	=	$dataArray ;
		}
		//echo 'Count is '.count($ICD9CodesArray).'<pre>'; print_r($ICD9CodesArray);		
		//End Get All ICD 9 Codes in array
		
		
		
		//*********Start Get All ICD 10Codes in array
			unset($condArr);
			$condArr['1'] = '1';
			$condArr['deleted'] = 0;	
			$primaryField	=	'id';
			
			
			$ICD10CodesData	=	$objManageData->getMultiChkArrayRecords('icd10_data',$condArr,'icd10,icd10_desc','Asc'," AND id NOT IN($dx_id_icd10)");
			foreach($ICD10CodesData as $ICD10)
			{
				$dataArray	=	array();	
				foreach($ICD10 as $fieldName=>$fieldValue)
				{
					$dataArray[$fieldName]	=	$fieldValue ;
				}
				$ICD10CodesArray[$ICD10->id]	=	$dataArray ;
			}
			//echo 'Count is '.count($ICD10CodesArray).'<pre>'; print_r($ICD10CodesArray);
		
		// End Get All ICD 10Codes in array
		
		?>
        <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6"> 
         <!-- Start Html to Add Extra Cpt Codes Here	-->
        <?PHP	
			if($dx_code_type == 'icd10')
			{
				if(is_array($ICD10CodesArray) && count($ICD10CodesArray) > 0)
				{
				$additionalICDCodeTitle	=	'Add DX-Codes (ICD10)';
		?>
        		<div id="" class="inner_safety_wrap">
                		<div class="row">
                        		<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">&nbsp;</div>
                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                	<select class="form-control selectpicker AdditionalCodeICD10" title=" <?=$additionalICDCodeTitle?>" data-title="<?=$additionalICDCodeTitle?> " data-live-search="true" data-style="btn-primary">
                                    <?php
									
                            	 		foreach($ICD10CodesArray as $ICD10CodeId =>$ICD10)
										{
											$dataAttributesList	=	''	;
											if($ICD10['deleted'] <> '1' )
											{
												$Code = trim($ICD10['icd10']);
												$Desc = trim($ICD10['icd10_desc']);
												$Laterality	=	$ICD10['laterality'];
												$Staging	=	$ICD10['staging'];
												$Severity	=	$ICD10['severity'];
												
												$dataAttributesList	.=	'data-diag-code="'.$Code.'" ';
												$dataAttributesList	.=	'data-diag-desc="'.$Desc.'" ';
												$dataAttributesList	.=	'data-diag-ltr="'.($Laterality ? $Laterality : '').'" ';
												$dataAttributesList	.=	'data-diag-stg="'.($Staging ? $Staging : '').'" ';
												$dataAttributesList	.=	'data-diag-svr="'.($Severity ? $Severity : '').'" ';
												$dataAttributesList	.=	'data-surgery-site="'.$surgerySite.'" ';
												
												
												
												$txt	=	($ICD10['icd10']	? '('.$ICD10['icd10'].')&nbsp;' : '' ).$ICD10['icd10_desc'];
												$val	=	$ICD10CodeId ;
												
									?>
                            					<option value="<?=$val?>" <?=$dataAttributesList?>><?=$txt?></option>
                         			<?php 
											}
										}
									?>	
									</select>
               					</div>
             			</div>
          		</div>
       	<?php
			}
			}
			else
			{
				if(is_array($ICD9CodesArray) && count($ICD9CodesArray) > 0)
				{
				$additionalICDCodeTitle	=	'Add DX-Codes (ICD9)';
		?>
        		<div id="" class="inner_safety_wrap">
                		<div class="row">
                        		<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">&nbsp;</div>
                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                	<select class="form-control selectpicker AdditionalCodeICD9" title=" <?=$additionalICDCodeTitle?>" data-title="<?=$additionalICDCodeTitle?> " data-live-search="true" data-style="btn-primary">
                                    <?php
									
                            	 		foreach($ICD9CodesArray as $ICD9CodeId =>$ICD9)
										{
											$dataAttributesList	=	''	;
											
											if($ICD9['del_status'] <> 'yes' )
											{
												$Code 		= trim($ICD9['diag_code']);
												//IF THERE IS NO COMMA INSERTED FOR DIAGNOSIS IN ADMIN THEN ADD COMMA IN ADMIN
												if(strpos($Code, ',') !== false){
													//DO NOTHING
												}
												else {
													$Code = ', '.$Code;
												}
												//END IF THERE IS NO COMMA INSERTED FOR DIAGNOSIS IN ADMIN THEN ADD COMMA IN ADMIN
												list($DiagCode, $DiagDesc) = explode(", ", $Code);
												
												$Code		=	trim($DiagCode);
												$Desc 		= trim($DiagDesc);
												$Alias		=	$ICD9['diag_alias'];
												
												$dataAttributesList	.=	'data-diag-code="'.$Code.'" ';
												$dataAttributesList	.=	'data-diag-desc="'.$Desc.'" ';
												$dataAttributesList	.=	'data-diag-alias="'.($Alias ? $Alias : '').'" ';
												$dataAttributesList	.=	'data-surgery-site="'.$surgerySite.'" ';
												
												$txt	=	($Code	? '('.$Code.')&nbsp;' : '' ).$Desc;
												$val	=	$ICD9CodeId ;
												
									?>
                            					<option value="<?=$val?>" <?=$dataAttributesList?>><?=$txt?></option>
                         			<?php 
											}
										}
									?>	
									</select>
               					</div>
             			</div>
          		</div>
       	<?php
			}
			}
		?>
      	<!-- End Html to Add Extra Cpt Codes Here	-->     
              <div class="panel panel-default bg_panel_sum">
                   <div class="panel-heading haed_p_clickable">
                    <h3 class="panel-title rob"> DX-Codes (<?php echo ($dx_code_type=='icd10')?'<b>ICD10</b>':'<b>ICD9</b>';?>)</h3>
                    <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
              		</div>
               <div class="panel-body" id="dx_code">
             	<ul class="list-group checked-list-box" id="DxCodes">
                
                     <?php							
						
						//$getDiagnosisDetails = $objManageData->getArrayRecords('diagnosis_tbl', '', '', 'diag_code', 'ASC');
						if(is_array($getDiagnosisDetails))
						{
							if($dx_code_type=='icd10')
							{
								foreach($getDiagnosisDetails as $diagnosisObj)
								{
									$diagnosisId = $diagnosisObj->id;
									$diagnosisCode = trim($diagnosisObj->icd10);
									$diagnosisDesc = trim($diagnosisObj->icd10_desc);
									if($icd10_nameDBExplodeArr[$diagnosisId] && $icd10_nameDBExplodeArr[$diagnosisId] <> $diagnosisDesc)
									{
										$diagnosisDesc = 	$icd10_nameDBExplodeArr[$diagnosisId];
									}
									
									$diagnosis_del_status = $diagnosisObj->deleted;
								
									if($diagnosis_del_status!='1' || in_array($diagnosisId,$icd10_idDBExplodeArr) ) 
									{
										++$s;
										++$c;
										$modOfC = $c%2;
										if($s%2==0) { $rowcolorDischargeSummarySheet = $rowcolor_discharge_summary_sheet; }
										else  { $rowcolorDischargeSummarySheet ='#FFFFFF'; }
									
										unset($ICD10CodesArray[$diagnosisId]);
									
										$icd_10_win = $site_replaced = $allowMultiple = false;
										$icd_10_win = stristr($diagnosisCode,'-');
										$icd10_org	=	$diagnosisCode;
										
										/*if($icd_10_win && ($diagnosisObj->laterality > 1 || $diagnosisObj->staging || $diagnosisObj->severity))
										{
											$allowMultiple = true;
										}
										else if($icd_10_win && ($diagnosisObj->laterality == 1 && ($diagnosisObj->staging || $diagnosisObj->severity)))
										{
											$allowMultiple = true;
										}*/
										if($icd_10_win && $diagnosisObj->laterality == 2) 
											$allowMultiple = true;
											
										//replace first '-' with site from icd 10 code
										$codeToReplace = $site = '';
										if($icd_10_win && ($diagnosisObj->laterality==1 || $diagnosisObj->laterality==2))
										{ 
											if($default_icd_10==true || (!in_array($diagnosisId,$icd10_idDBExplodeArr) && $default_icd_10==false))
											{
												$siteToWork	=	$surgerySite;
												if(in_array($diagnosisId,$primProcDxCodes)){
													$siteToWork	=	$surgerySite;
												}elseif(in_array($diagnosisId,$secProcDxCodes)){
													$siteToWork	=	$secondarySite;
												}elseif(in_array($diagnosisId,$terProcDxCodes)){
													$siteToWork	=	$tertiarySite;
												}
												
												
												if(strtolower($siteToWork) == 'left eye')	$site='left';
												elseif(strtolower($siteToWork) == 'right eye' )	$site='right';
												elseif(strtolower($siteToWork) == 'both eye' )	$site='both';
												else $site = trim(strtolower($siteToWork));
												
												if($site)
												{
													$under = 1;
													if(strpos($site,'lid')) $under = 2;
													
													if($codeToReplace = $siteArr[$site])
													{
														if($diagnosisObj->laterality == $under)
														{
															$diagnosisCode=preg_replace('/-/', $codeToReplace, $diagnosisCode, 1);
															$site_replaced=true;
														}
													}
												}//print'<pre>';print_r($siteArr);
												
												
											}
										}
										
										//to show selected one
										//$posToFindFrom=$position=$posVal=$laterSelId=$laterSelTxt=$laterSelCode=$severitySelTxt=$severitySelCode=$severitySelId=$stagingSelTxt=$stagingSelCode=$stagingSelId
										$icd10Sel='';
										if(in_array($diagnosisId,$icd10_idDBExplodeArr) && $default_icd_10==false)
										{
											$icd10Sel = $icd10_codeDBExplodeArr[$diagnosisId];
										}
										else
											$icd10Sel = $diagnosisCode;
										
										$icd10SelHtml = implode(', ',explode('@@',$icd10Sel));
										$checked= (count($icd10_idDBExplodeArr) && in_array($diagnosisId, $icd10_idDBExplodeArr)) ? 'checked' : '';
										$bgBlue = "backBgBlueColor('chbx_dig_no".$diagnosisId."','tr".$c."','".$rowcolorDischargeSummarySheet."');";
										$chkThis=	"checkThisChecked('chbx_dig_no".$diagnosisId."','tr".$c."','".$rowcolorDischargeSummarySheet."');";
										$openIt = "openItUp('chbx_dig_no".$diagnosisId."','list-inner".$s."');";
										$openIt	=	($icd_10_win) ? $openIt : '';
										
										$addMore= ($checked) ? '' : 'hidden';
									?>
                  		<li id="tr<?php echo $c; ?>"  class="list-group-item full_width"> <!--list-group-item-primary active-->
                      	<input type="hidden" name="diag_names[<?php echo $diagnosisId; ?>]" id="diag_names<?php echo $diagnosisId; ?>" value="<?=$diagnosisDesc?>" />
                        <input type="hidden" name="diag_codes[<?php echo $diagnosisId; ?>]" id="diag_codes<?php echo $diagnosisId; ?>" value="<?=$icd10_org?>" />
                        <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1">
                        	<input <?php echo $checked; ?> type="checkbox" value="<?php echo $diagnosisId.'~:~'.$icd10Sel; ?>" name="diagID[]" id="chbx_dig_no<?php echo $diagnosisId; ?>" onClick="<?php echo $bgBlue . $openIt; ?>" data-dxcode="<?=$icd10Sel?>" >
                      	</div>
                        
                        <b class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-left" id="<?php echo $s.'_icd10';?>" data-field="<?php echo $icd10_org;?>" onClick="<?php echo $chkThis . $openIt; ?>"> 
                           <?php echo $icd10SelHtml; ?>
                        </b>	
                        
                        <div class="col-md-7 col-sm-7 col-xs-7 col-lg-7 text-left text_list" id="<?php echo $s.'_icd10Desc';?>" data-field="<?php echo $diagnosisDesc;?>" onClick="<?php echo $chkThis . $openIt; ?>">
                            <?php echo $diagnosisDesc; ?>
                        </div>
                        <?php if($allowMultiple) { ?>
                        	<div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 padding_0 <?=$addMore?>"  id="<?php echo $s.'_add_icd10';?>" data-diag-ltr="<?php echo $diagnosisObj->laterality; ?>" data-diag-stg="<?php echo $diagnosisObj->staging; ?>" data-row-num="<?=$s?>" data-row-index="<?=$c?>" data-diag-svr="<?php echo $diagnosisObj->severity; ?>" data-diag-id="<?php echo $diagnosisId; ?>" >
                          	<span class="btn btn-primary btn-xs pull-right">
                            	<i class="glyphicon glyphicon-plus"></i>
                           	</span>
                          </div>
												<?php } ?>
                    </li>
                    <?php 
											if($icd_10_win)
											{
												//$index = 0;
												$icd10SelArr = explode('@@',$icd10Sel);
												if(is_array($icd10SelArr) && count($icd10SelArr) > 0 )
												{
													foreach($icd10SelArr as $index => $icd10Sel)
													{
														$list_id = 'list-inner'.$s.'_'.$index;
														$dispClass = (count($icd10_idDBExplodeArr) && in_array($diagnosisId, $icd10_idDBExplodeArr)) ? 'in' : '';
														$bgColor	= '#F0F0F0';
														if($allowMultiple && $index%2 == 0){
															$bgColor	= '#FDFDFD';
														}
														$matchWith = getSelectedOption($diagnosisObj->laterality,$diagnosisObj->severity,$diagnosisObj->staging,$icd10_org,$icd10Sel,$icdLater);
														
										?>
                            <li class="collapse <?php echo $dispClass;?> collapsible_li" id="<?=$list_id?>" data-row-index="<?=$index?>" style="background:<?=$bgColor?>">
                              <?php  
                                if(trim($diagnosisObj->laterality))
                                {	
                                  $char_to_replace=0;//get which '-' to replace
                                  echo' <div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">
                                          <b class=" padding_15">Site</b>';
                                  $siteCounter=0;
                                  //print'<pre>';print_r($siteArr);echo 'hi '.$site;
								  								foreach($icdLater[$diagnosisObj->laterality] as $key=>$value)
                                  {
                                    list($title,$code)=explode('~:~',$value);
																		
																		$selClass = ($siteArr[$site] == $code && $diagnosisObj->laterality == $under) ? 'btn-round-xs':'';
																		//$selClass = (trim(strtolower($surgerySite)) == trim(strtolower($title))) ? 'btn-round-xs':'';
																		if(!$selClass)
                                    {
                                      $selClass=($matchWith['laterality']==$code)?'btn-round-xs':'';	
                                    }
                                    $spanInit = $s.'_'.$index.'_site_';
                                    $spanID = $spanInit.$siteCounter;//echo 'hlo '.$code.'@@'.$siteArr[$site].'!!'.$site.'##';print'<pre>';print_r($siteArr);
                                    $_targetICDReplace = "targetICDreplace('".$s."_icd10','".$char_to_replace."','".$code."','chbx_dig_no".$diagnosisId."','tr".$c."','".$rowcolorDischargeSummarySheet."','".$spanInit."',".sizeof($icdLater[$diagnosisObj->laterality]).",'".$spanID."','".$s."_icd10Desc','".ucfirst($title)."');";
                                    echo'<span class="padding_15 '.$selClass.'" id="'.$spanID.'"><a href="javascript:void(0)" onClick="'.$_targetICDReplace.'" data-target="#'.$list_id.'" data-toggle="collapse in">'.ucfirst($title).'</a></span>';	
                                    $siteCounter++;
                                  }	
                                  echo'</div>';
                                }
                                if(trim($diagnosisObj->severity))
                                {	
                                  $severityCounter=0;
                                  $char_to_replace=($diagnosisObj->laterality)?1:0;//get which '-' to replace	
                                  if($diagnosisObj->laterality)echo'<div class="clearfix margin_adjustment_only border-dashed"></div>';
                                  echo' <div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">
                                  <b class=" padding_15">Severity</b>';
                                  //print'<pre>';print_r($icdLater);
								  foreach($icdLater[$diagnosisObj->severity] as $key=>$value)
                                  {
                                    list($title,$code)=explode('~:~',$value);
                                    $selClass=($matchWith['severity']==$code)?'btn-round-xs':'';
                                    $spanInit = $s.'_'.$index.'_severity_';
                                    $spanID = $spanInit.$severityCounter;
                                    
                                    $_targetICDReplace = "targetICDreplace('".$s."_icd10','".$char_to_replace."','".$code."','chbx_dig_no".$diagnosisId."','tr".$c."','".$rowcolorDischargeSummarySheet."','".$spanInit."',".sizeof($icdLater[$diagnosisObj->severity]).",'".$spanID."','".$s."_icd10Desc','".ucfirst($title)."');";
                                    
                                    echo'<span class=" padding_15 '.$selClass.'" id="'.$spanID.'"><a href="javascript:void(0)" onClick="'.$_targetICDReplace.'" data-target="#'.$list_id.'" data-toggle="collapse in">'.ucfirst($title).'</a></span>';	
                                    $severityCounter++;
                                  }	
                                  echo'</div>';
                                }
                                if(trim($diagnosisObj->staging))
                                {	
                                  $stagingCounter=0;
                                  $char_to_replace=($diagnosisObj->laterality)?1:0;//get which '-' to replace
                                  if($diagnosisObj->laterality || $diagnosisObj->severity)echo'<div class="clearfix margin_adjustment_only border-dashed"></div>';
                                  echo'  <div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">
                                  <b class=" padding_15">Staging</b>';
                                  foreach($icdLater[$diagnosisObj->staging] as $key=>$value)
                                  {
                                    list($title,$code)=explode('~:~',$value);
																		$selClass=($matchWith['staging']==$code)?'btn-round-xs':'';
                                    $spanInit = $s.'_'.$index.'_staging_';
                                    $spanID = $spanInit.$stagingCounter;
                                    
                                    $_targetICDReplace = "targetICDreplace('".$s."_icd10','".$char_to_replace."','".$code."','chbx_dig_no".$diagnosisId."','tr".$c."','".$rowcolorDischargeSummarySheet."','".$spanInit."',".sizeof($icdLater[$diagnosisObj->staging]).",'".$spanID."','".$s."_icd10Desc','".ucfirst($title)."');";
                                    
                                    echo'<span class=" padding_15 '.$selClass.'" id="'.$spanID.'"><a href="javascript:void(0)" onClick="'.$_targetICDReplace.'" data-target="#'.$list_id.'" data-toggle="collapse in">'.ucfirst($title).'</a></span>';	
                                    $stagingCounter++;
                                  }	
                                  echo'</div>';
                                }
                              ?>
                            </li>
                   	<?php
													}
												}
											 }
										?>
                    <div class="clearfix"></div>
                  	<?php
											//START CODE TO CHANGE BGCOLOR OF SELECTED ROW AND MAKE TEXT IN BOLD 
											if(count($icd10_idDBExplodeArr)>0) { 
												if(in_array($diagnosisId, $icd10_idDBExplodeArr)) { 
													echo "<script>backBgBlueColor('chbx_dig_no".$diagnosisId."','tr".$c."','".$rowcolorDischargeSummarySheet."')</script>";
												} 
											}
											//END CODE TO CHANGE BGCOLOR OF SELECTED ROW AND MAKE TEXT IN BOLD
						}
					}
				}
				else//show icd 9 codes
				{
					foreach($getDiagnosisDetails as $diagnosisObj){
					$diagnosisId = $diagnosisObj->diag_id;
					$diagnosis = $diagnosisObj->diag_code;
					$diagnosis_del_status = $diagnosisObj->del_status;
					if($diagnosis_del_status!='yes' || in_array($diagnosisId,$diag_idDBExplodeArr)) {
						//IF THERE IS NO COMMA INSERTED FOR DIAGNOSIS IN ADMIN THEN ADD COMMA IN ADMIN
						if(strpos($diagnosis, ',') !== false){
							//DO NOTHING
						}else {
							$diagnosis = ', '.$diagnosis;
						}
						//END IF THERE IS NO COMMA INSERTED FOR DIAGNOSIS IN ADMIN THEN ADD COMMA IN ADMIN
						
						list($diagnosisCode, $diagnosisDesc) = explode(", ", $diagnosis);
						$diagnosisCode=trim($diagnosisCode);
						
						if($diag_nameDBExplodeArr[$diagnosisId] && $diag_nameDBExplodeArr[$diagnosisId] <> $diagnosisDesc)
						{
							$diagnosisDesc = 	$diag_nameDBExplodeArr[$diagnosisId];
						}
						
						++$s;
						++$c;
						$modOfC = $c%2;
						if($s%2==0) { $rowcolorDischargeSummarySheet = $rowcolor_discharge_summary_sheet; }else  { $rowcolorDischargeSummarySheet ='#FFFFFF'; }
						
						unset($ICD9CodesArray[$diagnosisId]);
			?>
		   <li id="tr<?php echo $c; ?>"  class="list-group-item full_width"> <!--list-group-item-primary active-->
				<input type="hidden" name="diag_names[<?php echo $diagnosisId; ?>]" id="diag_names<?php echo $diagnosisId; ?>" value="<?=$diagnosisDesc?>" />									  
			<div class="col-md-1 col-sm-1 col-xs-1 col-lg-1  text-left">
				<input <?php if(count($diag_idDBExplodeArr)>0){ if(in_array($diagnosisId, $diag_idDBExplodeArr)){ echo "CHECKED"; }}?> type="checkbox" value="<?php echo $diagnosisId; ?>" name="diagID[]" id="chbx_dig_no<?php echo $diagnosisId; ?>" onClick="backBgBlueColor(<?php echo'\'chbx_dig_no'.$diagnosisId.'\',\'tr'.$c.'\',\''.$rowcolorDischargeSummarySheet.'\'';?>)" data-dxcode="<?=$diagnosisCode?>" />
				
			</div>
			
			<b class="col-md-2 col-sm-2 col-xs-2 col-lg-2 text-left" onClick="checkThisChecked(<?php echo'\'chbx_dig_no'.$diagnosisId.'\',\'tr'.$c.'\',\''.$rowcolorDischargeSummarySheet.'\'';?>);"> 
				<?php echo $diagnosisCode; ?>
			</b>	
			
			<div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 text-left text_list" onClick="checkThisChecked(<?php echo'\'chbx_dig_no'.$diagnosisId.'\',\'tr'.$c.'\',\''.$rowcolorDischargeSummarySheet.'\'';?>);">
				<?php echo $diagnosisDesc; ?>                                                
			</div>
		</li>
		
		 <div class="clearfix"></div>
		<?php
						//START CODE TO CHANGE BGCOLOR OF SELECTED ROW AND MAKE TEXT IN BOLD 
						if(count($diag_idDBExplodeArr)>0) { 
							if(in_array($diagnosisId, $diag_idDBExplodeArr)) { 
								
								echo "<script>backBgBlueColor('chbx_dig_no".$diagnosisId."','tr".$c."','".$rowcolorDischargeSummarySheet."')</script>";
							} 
						}
						//END CODE TO CHANGE BGCOLOR OF SELECTED ROW AND MAKE TEXT IN BOLD
						}
					}	
				}
			}
			?>
                      
                    </ul>
                 
                  
                  
                  </div>
                
                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <div class="clearfix margin_adjustment_only border-dashed"></div> 
                    </div>
                    <div class="clearfix margin_adjustment_only"></div>
                    
                      <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1  text-left">
                      <input <?php if($disAttached=='Yes') { echo 'checked'; }?> type="checkbox" value="Yes" name="chbx_disAttached" id="chbx_disAttachedId" onChange="restPos();showHideScanDiv('chbx_disAttachedId', 'below_summary');">
                      </div>
                      
                      <b class="padding_0 col-md-11 col-sm-11 col-xs-11 col-lg-11 text-left"><label for="chbx_disAttachedId">See Attached Discharge Summary</label> </b>
                     <div class="clearfix"></div>
                       
                    <div class="upload_inner" id="below_summary_dummy" style="height:60px">
                    <?php
					$disDivIdDisplay='none';
					if($disAttached=='Yes') { $disDivIdDisplay='block'; }
                            
					if($disDivIdDisplay!='none') {?>
                    
                    	<div class="col-md-12 col-sm-12  col-xs-12 col-lg-12">
                                <div class="inline_btns">
                                    <input type="file" class="form-control"/>
                                </div>
                                <div class="inline_btns">
                                    <a href="javascript:void(0)" class="btn btn-primary"> Scan	</a>
                                    <a href="javascript:void(0)" class="btn btn-primary"> Upload </a>
                                </div>
                         </div>
                    
                    <?php }
					$disDivIdDisplayImageTd='';
					if($disAttached!='Yes') { $disDivIdDisplayImageTd='display:none;'; }
					?>
                   </div>
                    
                    <div class="clearfix"></div>
                    <?php 
									
									$dischargeSummarySheetDetails = $objManageData->getRowRecord('dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetIdDB);
									if(!$dis_ScanUpload) {
										$dis_ScanUpload=$dischargeSummarySheetDetails->dis_ScanUpload;
									}
									if(!$dis_ScanUpload2) {
										$dis_ScanUpload2=$dischargeSummarySheetDetails->dis_ScanUpload2;
									}
									if($dis_ScanUpload || $dis_ScanUpload2){ $existIolImage='yes'; $dis_src = "&dis_ScanUpload=$dis_ScanUpload"; $iframe_dis_Height='height="150px"'; $iframe_dis_width='width="100%"'; }else {$iframe_dis_Height='height="0"';  }
									
									?>
									<input type="hidden" name="hidd_anyOneImageExist" id="hidd_anyOneImageExist" value="<?php echo $existIolImage;?>" />															
									<iframe name="iframeIOL" id="iframeIOL" frameborder="0" <?php echo $iframe_dis_Height;?> <?php echo $iframe_dis_width;?> scrolling="no" src="discharge_summary_image.php?dischargeSummarySheetId=<?php echo $dischargeSummarySheetIdDB; ?>"></iframe>
                    
                   
                    <div class="inner_safety_wrap" id="">
                        <div class="well well-sm">
                            <div class="row">
                                <div class="col-md-12 col-sm-5 col-xs-4 col-lg-4">
                                    <label class="date_r">
                                        Other 1
                                    </label>
                                </div>
                                     <div class="clearfix margin_small_only visible-md"></div>
                                <div class="col-md-12 col-sm-7 col-xs-8 col-lg-8 text-center">
                                    <textarea id="Field2"  style="resize:none;<?php echo $DischargeBackColor;?>" class="form-control" name="other1" onFocus="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');" onBlur="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');" onKeyUp="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');"><?php echo stripslashes($other1DB); ?></textarea>
                                </div> <!-- Col-3 ends  -->
                            </div>
                        </div> 
                     </div>
                     <div class="inner_safety_wrap" id="">
                        <div class="well well-sm">
                            <div class="row">
                                <div class="col-md-12 col-sm-5 col-xs-4 col-lg-4">
                                    <label class="date_r">
                                        Other 2
                                    </label>
                                </div>
                                     <div class="clearfix margin_small_only visible-md"></div>
                                <div class="col-md-12 col-sm-7 col-xs-8 col-lg-8 text-center">
                                    <textarea id="Field3" style="resize:none;<?php echo $DischargeBackColor;?>" class="form-control" name="other2" onFocus="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');" onBlur="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');" onKeyUp="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');"><?php echo stripslashes($other2DB); ?></textarea>
                                </div> <!-- Col-3 ends  -->
                            </div>
                        </div> 
                     </div>
                   	
					<?php
						// Start Update Hidden Field TotalRows to store Total Rows created above for Cpt & DxCodes
						echo '<script>document.getElementById("TotalRows").value='.$c.'</script>';
						// End Update Hidden Field TotalRows to store Total Rows created above for Cpt & DxCodes
					?>
                   	 
                    <?php
					$allergiesReactionDetails = $objManageData->getArrayRecords('patient_allergies_tbl', 'patient_confirmation_id', $_REQUEST['pConfId']);
					if($allergiesReactionDetails){
					?> 
                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <div class="clearfix margin_adjustment_only border-dashed"></div> 
                    </div>
                    <label class="full_width padding_15">Allergies/ Reactions</label>
                   <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                     <div class="inner_safety_wrap" id="">
                        <div class="scheduler_table_Complete">
                        <div class="my_table_Checkall">
                             <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped ">
                                    <thead class="cf">
                                        <tr>
                                            <th class="text-left col-md-6 col-sm-6 col-xs-6 col-lg-6">Name</th>
                                            <th class="text-left col-md-6 col-sm-6 col-xs-6 col-lg-6">Reaction</th>
                                        </tr>
                                    </thead>
                                    <tbody style="">
                                        <?php
                                        foreach($allergiesReactionDetails as $allergyName){
                                            $allergyNameShow = $allergyName->allergy_name;
                                            $reactionShow = $allergyName->reaction_name;	
                                        ?>
                                        <tr>
                                            <td class="text-left"><?php echo $allergyNameShow;?></td>
                                            <td class="text-left" ><?php echo $reactionShow;?></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                            </table>
                         </div>                
                      </div>
                     
                     </div>
                   </div>
                   <?php
					}
					if($iol_ScanUpload || $iol_ScanUpload2){
				   ?>
                   <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <div class="clearfix margin_adjustment_only border-dashed"></div> 
                    </div>
                    <div class="full_width padding_15 clearfix"> <label> IOL Scanned Image  </label></div>
                     <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-center">
                             <div class="img_wrap_discharge">
                             <?php if($iol_ScanUpload) { ?>
								<img class="thumbnail" id="imgThumbNail" style="cursor:pointer;" src="admin/logoImg.php?from=op_room_record&amp;id=<?php echo $operatingRoomRecordsId; ?>" onClick="showImgWindow();">
							 <?php } if($iol_ScanUpload2) { ?>
								<img class="thumbnail" id="imgThumbNail2" style="cursor:pointer;" src="admin/logoImg2.php?from=op_room_record&amp;id=<?php echo $operatingRoomRecordsId; ?>" onClick="showImgWindow('secondImage');">
							<?php  } ?>
                             </div>
                     </div>
                     <?php }?>
                             
          </div>
          		
               
     	</div>
         <!-- END OF GETTING DX CODES --> 
         <div class="clearfix"></div>
		  <p class="rob text-left padding_15"> I certify that the diagnosis and procedures performed are accurate and complete to the best of my knowledge .	</p>
		  <?php 

			$discharge_disclaimer = ($form_status <> 'completed' && $form_status <> 'not completed') ? $defaultDischargeTxt : $disclaimer_txt;
			if( $discharge_disclaimer ) {
				echo '<textarea name="disclaimer_txt" class="hidden">'.$discharge_disclaimer.'</textarea>';
				echo '<p class="rob text-left padding_15">'.$discharge_disclaimer.'</p>';
			}
		  ?>
        <?php 
		      //CODE RELATED TO SURGEON SIGNATURE ON FILE
				$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
				$ViewUserNameRes = imw_query($ViewUserNameQry) or die('Error @ Line :'.(__LINE__).':-'.imw_error()); 
				$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
				
				$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
				$loggedInUserType = $ViewUserNameRow["user_type"];
				$loggedInSignatureOfUser = $ViewUserNameRow["signature"];
				
				if($loggedInUserType<>"Surgeon") {
					$loginUserName = $_SESSION['loginUserName'];
					$callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
				}else {
					$loginUserId = $_SESSION["loginUserId"];
					$callJavaFunSurgeon = "document.frm_pre_op_nurs_rec.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','discharge_summary_sheet_ajaxSign.php','$loginUserId','Surgeon1');";
				}					
				$surgeon1SignOnFileStatus = "Yes";
				$TDsurgeon1NameIdDisplay = "block";
				$TDsurgeon1SignatureIdDisplay = "none";
				$Surgeon1Name = $loggedInUserName;
				$signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
				if($signSurgeon1Id<>0 && $signSurgeon1Id<>"") {
					$Surgeon1Name = $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
					$surgeon1SignOnFileStatus = $signSurgeon1Status;	
					$TDsurgeon1NameIdDisplay = "none";
					$TDsurgeon1SignatureIdDisplay = "block";
					$signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signSurgeon1DateTime);
				}
				//CODE TO REMOVE SURGEON 1 SIGNATURE	
					if($_SESSION["loginUserId"]==$signSurgeon1Id) {
						$callJavaFunSurgeonDel = "document.frm_pre_op_nurs_rec.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','discharge_summary_sheet_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
					}else {
						$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
					}
				//END CODE TO REMOVE SURGEON 1 SIGNATURE	
			//END CODE RELATED TO SURGEON SIGNATURE ON FILE
			
				$surgeon_BackColor=$chngBckGroundColor;
				if($signSurgeon1Id ){
					 $surgeon_BackColor=$whiteBckGroundColor; 
				}
		 ?>
         <div class="panel panel-default">
            <div class="panel panel-body">
                    <div class="full_width">
                        <div class="col-md-8 col-sm-7 col-lg-8 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
<div class="inner_safety_wrap" id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>;">
<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $surgeon_BackColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
</div>

<div class="inner_safety_wrap collapse" id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>;">
<span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>". " Dr. ".$Surgeon1Name; ?>  </a></span>	     
<span class="rob full_width"> <b> Electronically Signed </b> <?php echo $surgeon1SignOnFileStatus;?></span>
<span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signSurgeon1DateTimeFormatNew; ?> <span class="fa fa-edit fa-editsurg"></span></span></span>
</div>

</div>
                        </div>
                        <div class="col-md-4 col-sm-5 col-xs-12 col-lg-4 text-right">
                            <div class="inner_safety_wrap">
                                  <span class="rob"> <b>  Date: </b> <?php if($date_surgeon_sign) { echo $date_surgeon_sign; }else { echo date('m/d/Y');}?> </span>
                            </div>
                        
                        </div>
                        
                   </div>  
            </div>
          </div>
              
              
      <div class="clearfix"></div>
	</div>

</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" method="post" action="discharge_summary_sheet.php?cancelRecord=true">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->	

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "discharge_summary_sheet.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM

/*AMD Alert*/
include_once('no_record.php');
if( isset($_GET['finalize_alert']) && (bool)$_GET['finalize_alert'] === true && isset($_SESSION['amd_error']) )
{
	print '<script type="text/javascript">$("#noRecordModal > .modal-dialog").addClass("modal-lg");$("#noRecordModal > .modal-dialog p").removeClass("text-center").addClass("text-left");modalAlert("'.$_SESSION['amd_error'].'");</script>';
	unset($_SESSION['amd_error']);
}
if( isset($_GET['finalize_alert']) && (bool)$_GET['finalize_alert'] === true && isset($_SESSION['amd_charge_error']) && count($_SESSION['amd_charge_error']) > 0 )
{
	$aMsg = "<strong>Following Charges are not posted:</strong><br />";
	foreach($_SESSION['amd_charge_error'] as $aKey=>$aMsgText){
		$aMsg .= "<strong>$aKey: </strong>$aMsgText<br />" ;
	}
	
	print '<script type="text/javascript">$("#noRecordModal > .modal-dialog").addClass("modal-lg");$("#noRecordModal > .modal-dialog p").css("text-align", "left");modalAlert("'.$aMsg.'");</script>';
	unset($_SESSION['amd_charge_error']);
}
/*Emd AMD Alert*/

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

//START CODE, ONLY THE SIGNED SURGEON CAN MODIFY THIS CHARTNOTE(i.e DISABLE 'Save' AND 'Save&Print' BUTTON FOR OTHER THAN SIGNED SURGEON)
if($signSurgeon1Id<>0 && $signSurgeon1Id<>"" && $_SESSION["loginUserId"]<>$signSurgeon1Id) {
	?>
	<script>
		if(top.document.getElementById('saveBtn')) {
			top.document.getElementById('saveBtn').style.display = 'none';
		}
		if(top.document.getElementById('SavePrintBtn')) {
			top.document.getElementById('SavePrintBtn').style.display = 'none';
		}
	</script>
	<?php
}
//END CODE, ONLY THE SIGNED SURGEON CAN MODIFY THIS CHARTNOTE(i.e DISABLE 'Save' AND 'Save&Print' BUTTON FOR OTHER THAN SIGNED SURGEON)
?>

<script>
if(document.getElementById('imgThumbNail')){
	var target = 100;
	var imgHeight = document.getElementById('imgThumbNail').height;
	var imgWidth = document.getElementById('imgThumbNail').width;
	
	if(imgHeight>=250){
		if (imgWidth > imgHeight) { 
			percentage = (target/imgWidth); 
		} else { 
			percentage = (target/imgHeight);
		} 
		widthNew = imgWidth*percentage; 
		heightNew = imgHeight*percentage; 	
		document.getElementById('imgThumbNail').height = heightNew;
		document.getElementById('imgThumbNail').width = widthNew;
	}
  }
  
</script>

<!-- <div id="imgDiv" style="position:absolute;display:none;left:150px;top:50px;width:550px;height:500">
		<iframe width="550" height="500" name="imageUplades" src="opRoomImagePopUp.php?from=op_room_record&id=<?php echo $operatingRoomRecordsId; ?>"></iframe>
</div> -->

<script>
	function changeImgSize(){
			var target = 100;
			var imgHeight = top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgDischargeThumbNail').height;
			var imgWidth = top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgDischargeThumbNail').width;
			
			if((imgHeight>=200) || (imgWidth>=200)){
				if(imgWidth > imgHeight){ 
					percentage = (target/imgWidth); 
				}else{ 
					percentage = (target/imgHeight);
				} 
				widthNew = imgWidth*percentage; 
				heightNew = imgHeight*percentage; 	
				top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgDischargeThumbNail').height = heightNew;
				top.mainFrame.main_frmInner.iframeIOL.document.getElementById('imgDischargeThumbNail').width = widthNew;
			}
		}
		
		//START THIS FUNCTION FOR SECOND IMAGE
		function changeImgSize2(){
			var target2 = 100;
			var imgHeight2 = document.getElementById('imgDischargeThumbNail2').height;
			var imgWidth2 = document.getElementById('imgDischargeThumbNail2').width;
			if((imgHeight2>=200) || (imgWidth2>=200)){
				if(imgWidth2 > imgHeight2){ 
					percentage2 = (target2/imgWidth2); 
				}else{ 
					percentage2 = (target2/imgHeight2);
				} 
				widthNew2 = imgWidth2*percentage2; 
				heightNew2 = imgHeight2*percentage2; 	
				document.getElementById('imgDischargeThumbNail2').height = heightNew2;
				document.getElementById('imgDischargeThumbNail2').width = widthNew2;	
			}
			
			
		}
		//END THIS FUNCTION FOR SECOND IMAGE
		
	// IMAGE THUMBNAIL
	if(document.getElementById('imgDischargeThumbNail')){
		setTimeout('changeImgSize()', 100);
	}
	if(document.getElementById('imgDischargeThumbNail2')){
		setTimeout('changeImgSize2()', 100);
	}
	
</script>
<?php
if($sect!="print_emr") { ?>

    <form action="uploadDischargeImage.php" name="frm_uploadDISImage" enctype="multipart/form-data" method="post" onSubmit="changeTxtChkGroupColorDischarge('<?php echo $procedureIdImplode;?>','Field1','Field2','Field3','comment_id','upload_image_id');return AIM.submit(this, {'onStart' : startCallback, 'onComplete' : completeCallback})">
        <div class="upload_inner" id="below_summary" style="width:500px; position:absolute;display:<?php echo $disDivIdDisplay;?>">
            <div class="col-md-12 col-sm-12  col-xs-12 col-lg-12">
                <div id="uploadImageDivId" class="inline_btns">
                    <input type="file" class="form-control" id="upload_image_id" name="uploadImage" />
                </div>
                <div id="uploadBtnDivId" class="inline_btns">
                    <button name="uploadBtn" id="uploadBtn" class="btn btn-primary">Upload</button> 
                    
                    <a id="dischargeSummaryIolScanBtnId" href="javascript:void(0)" onClick="javascript:window.open('admin/scanPopUp.php?pConfirmId='+<?php echo $_REQUEST["pConfId"]; ?>+'&scanDISCHARGE='+<?php echo $dischargeSummarySheetIdDB; ?>+'&DISCHARGEScan=true','scanWinDISCHARGE', 'width=775, height=650,location=yes,status=yes'); document.getElementById('scan').value = 'true';" class="btn btn-primary"> Scan	</a>
                </div>
            </div>
        </div>

        <input type="hidden" name="pConfId" value="<?php echo $_REQUEST["pConfId"]; ?>" />
        <input type="hidden" name="dischargeSummarySheetId" value="<?php echo $dischargeSummarySheetIdDB; ?>" />	
        <input type="hidden" name="hidd_delImage" id="hidd_delImage" value="" />
    </form>
<?php
}
?>
<?php
if($finalizeStatus!='true'){
	$ascId = !($ascId) ? $confirmationDetails->ascId : $ascId;
	include('privilege_buttons.php');
}
include("print_page.php");
include 'common/preDefineModifiers.php';
?>
<script>
var attachedIdPos;
if(document.getElementById('chbx_disAttachedId')) {
	attachedIdPos =parseInt(findPos_Y('chbx_disAttachedId'))+25;
}
if(document.getElementById('uploadImageDivId')) {
	document.getElementById('uploadImageDivId').style.top=attachedIdPos+'px';
}	
if(document.getElementById('uploadBtnDivId')) {
	document.getElementById('uploadBtnDivId').style.top=attachedIdPos+'px';
}	

function targetICDreplace(div,placeToChange,value,digChk,rowId,prevColorId,checkIni,checkIni_length,obj,desc_div,titleToChange)
{
	var _obj = $("#"+obj); 
	var _hcls= _obj.hasClass('btn-round-xs');
	var _act = _hcls ? 'remove' : 'add';
	var word = '-'; var counter = 0; 
	var occArr = Array(); var icd10Arr = Array();

	var objArr  = obj.split('_'); 
	var row = objArr[1]; var index = objArr[1];
	var typ = objArr[3]; var t_val = objArr[4];
	
	value = _hcls ? '-' : value;
	
	// Getting Original Value
	var org_val = $("#"+div).attr('data-field');
	org_val=$.trim(org_val); 
	
	// find all occurrences forward
	occArr[0] = org_val.indexOf(word);
	if(org_val.length > parseInt(occArr[0]))
	{
		if(occArr[0]) occArr[1]=org_val.lastIndexOf(word);
	}
	if(occArr[0]==occArr[1])occArr[1]='';
	
	// Get Current Displaying Dx Codes
	var icd10Txt = $("#"+div).html();
	var _htmlArr = $.map(icd10Txt.split(","), $.trim);
	
	//Replace Code
	var icd10 = $.trim(_htmlArr[index]);
	for (var i = 0; i < icd10.length; i++){
		if(i==(occArr[placeToChange])){
			icd10Arr[i]=value;
		}else{
    	icd10Arr[i]=icd10.charAt(i);
		}
	}
	var icd10 = icd10Arr.join('');
	
	// Code to check occurance of value in another row
	var match_found = false;
	if(placeToChange == 0)
	{
		$.each(_htmlArr,function(i,v){
			if(i !== index) 
			{
				var m1 = v[occArr[placeToChange]];
				var m2 = icd10[occArr[placeToChange]] ;
				if(m1 == m2 && org_val != icd10 )  match_found = true
			}
		});
	}
	// If occurance found in another row then return false;
	if(match_found) return;
	
	//Toggle Highlight class
	for(var cn=0;cn<=checkIni_length;cn++)
	{
		var t = checkIni+cn;
		if(t !== obj) $("#"+t).removeClass('btn-round-xs');	
	}
	_obj.toggleClass('btn-round-xs');
	
	
	// Updating DxCode HTML
	_htmlArr[index] = icd10;
	var icd10FinalHtml = _htmlArr.join(', ');
	var icd10FinalVal  = _htmlArr.join('@@');
	$("#"+div).html(icd10FinalHtml);
	
	
	// Updating Checkbox Value
	var digChkVal = $("#"+digChk).val();
	var digChkArr = digChkVal.split('~:~');
	digChkVal=digChkArr[0]+'~:~'+icd10FinalVal;
	$("#"+digChk).val(digChkVal);
	$("#"+digChk).attr('data-dxcode',icd10FinalVal);
	$("#"+digChk).prop('checked', true);
	
	// Calling function Change Background Color If Not
	backBgBlueColor(digChk,rowId,prevColorId);
}
function checkThisChecked(digChk,rowId,prevColorId)
{
	
	if($("#"+digChk).prop('checked')==false)
	{
		$("#"+digChk).prop('checked', true);
	}
	else
	{
		$("#"+digChk).prop('checked', false);
	}
		
	backBgBlueColor(digChk,rowId,prevColorId);
}

function openItUp(chk,targetLi)
{
	if($('#'+chk).prop('checked')==true)
	{
		$("li[id^='"+targetLi+"_']").addClass('in');
	}
	else
	{
		$("li[id^='"+targetLi+"_']").removeClass('in');
	}	
}
</script> 

<script>
$(function(){
	
	var $_icdLaterArr	=	<?php echo json_encode($icdLater); ?>;
	var $_icdSiteArr	=	<?php echo json_encode($siteArr); ?>;
	var $_iDocModArr = <?php echo json_encode($idoc_mod); ?>;
	var $_sitePoeMod = <?php echo json_encode($sitePoeMod); ?>;
	var $_procUnitsArr = <?php echo json_encode($proc_units); ?>;
	String.prototype.ucwords = function() {
		return this.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			return letter.toUpperCase();
		});
	}
	
	function filter_cpt_modifiers(cpt_code)
	{
		var m = false; var a = false
		if(cpt_code !== ''){
			if( $_iDocModArr.cpt_code) { 
				m = true; a = $_iDocModArr.cpt_code;
			}
		}
		
		if( !m){
			$.each($_iDocModArr,function(key,value){
				if(m) return false; 
				if( key.indexOf(cpt_code) === 0) { a = value; m = true; }
			});
		}
		return a;
	}

	$('body').on('change','.AdditionalCode',function(){
			var $_this		=	$(this);
			var ProcId		=	$_this.val();	
			var ProcName	=	$_this.find('option:selected').attr('data-proc-name');;
			var DataCatID	=	$_this.find('option:selected').attr('data-cat-id');
			var CptPanelObj	=	$("#CPT_"+DataCatID+"");
			var DataCatName	=	CptPanelObj.attr('data-category');
			var DataProcCode=	$_this.find('option:selected').attr('data-proc-code');	

			var DataCptSurgeonCode		=	$_this.find('option:selected').attr('data-proc-surgeon');
			var DataCptFacilityCode		=	$_this.find('option:selected').attr('data-proc-facility');
			var DataStopParentSuperbill	=	$_this.find('option:selected').attr('data-proc-stop_parent_superbill');
			
			var PrmProcID	=	$("#primary_procedure_id").val();
			var SecProcID	=	$("#secondary_procedure_id").val();
			var TotalRows	=	parseInt($("#TotalRows").val());
			var ProcImplode	=	$("#procedureIdImplode").val();
			
			var $_html		=	'';
			
			TotalRows		=	TotalRows + 1 ;
			ProcImplode		=	ProcImplode + ','+ProcId;
			var Top			=	TotalRows*20;
			var PrevColor	=	'#FFFFFF';
			
			var $_fn_backBgColor	=	"backBgBlueColor('chbx_fdt_no"+ProcId+"','tr"+TotalRows+"','"+PrevColor+"');";
			var $_fn_chkGroupColor	=	"changeTxtChkGroupColorDischarge('"+ProcImplode+"','Field1','Field2','Field3','comment_id','upload_image_id');";
			var $_fn_tempDis	 =	"tempDis('"+PrmProcID+"','"+ProcId+"','"+SecProcID+"','"+Top+"');";
			var $_fn_checkThisChecked	=	"checkThisChecked('chbx_fdt_no"+ProcId+"','tr"+TotalRows+"','"+PrevColor+"');";
			
			// Start Html to append
			$_html	+=	'<li class="list-group-item full_width"  id="tr'+TotalRows+'" onClick="'+$_fn_backBgColor+'"> ';
			$_html	+=	'<input  type="hidden" name="procNames['+ProcId+']" value="'+ProcName+'" />';
			$_html	+=	'<input  type="hidden" name="procCodeNames['+ProcId+']" value="'+DataProcCode+'" />';
			$_html	+=	'<input  type="hidden" id="procedureCode_'+ProcId+'" value="'+DataProcCode+'" />';
			$_html	+=	'<div class="col-md-1 col-sm-1 col-xs-1 col-lg-1  text-left">';
			$_html	+=	'<span class="clpChkBx colorChkBx" style="background-color:white;">';
			$_html	+=	'<input type="checkbox" value="'+ProcId+'" name="chbx_fdt['+ProcId+']" id="chbx_fdt_no'+ProcId+'" onClick="'+$_fn_chkGroupColor+'return '+$_fn_tempDis+'" data-category="'+DataCatName+'" data-surgeon-code="'+DataCptSurgeonCode+'" data-facility-code="'+DataCptFacilityCode+'" data-stop_parent_superbill="'+DataStopParentSuperbill+'"></span>';
			$_html	+=	'</div>';
			
			$_html	+=	'<b class="padding_0 col-md-4 col-sm-4 col-xs-4 col-lg-4 text-left" onClick="'+$_fn_checkThisChecked + $_fn_chkGroupColor + +'return '+$_fn_tempDis+'">';
			$_html	+=	DataProcCode
			$_html	+=	'</b>';
			
			$_html	+=	'<span class="col-md-7 col-sm-7 col-xs-7 col-lg-7 text-left text_list" onClick="'+$_fn_checkThisChecked + $_fn_chkGroupColor + +'return '+$_fn_tempDis+'">';
			$_html	+=	ProcName
			$_html	+=	'</span>';
			
			$_html	+=	'</li>';
			$_html	+=	'<div class="clearfix"></div>';
			
			//Set Display status of category panel body
			
			CptPanelObj.find('ul').append($_html);
			var CDisplay	=	CptPanelObj.css('display');
			if(CDisplay == 'none')	CptPanelObj.toggle();
			
			// Updating Fields with new values
			$("#TotalRows").val(TotalRows);
			$("#procedureIdImplode").val(ProcImplode);
			
			// Refresh Selectptcker
			$_this.find('option:selected').remove();
			$_this.val('').selectpicker('refresh');
			
			// Triggering On Click Functions
			eval($_fn_backBgColor);
			eval($_fn_chkGroupColor);			
			eval($_fn_tempDis);		
			eval($_fn_checkThisChecked);		
			
			var chkBoxObj		=	$('#chbx_fdt_no'+ProcId+'');
			addCptInSuperBill(chkBoxObj,2);	
			addCptInSuperBill(chkBoxObj,3);	
	});
	
	$('body').on('change','.AdditionalCodeICD10',function(){
			
			var $_this		=	$(this);
			var DiagId		=	$_this.val();
			var DiagDesc	=	$_this.find('option:selected').attr('data-diag-desc');
			var DiagCode	=	$_this.find('option:selected').attr('data-diag-code');
			var DiagLtr		=	$_this.find('option:selected').attr('data-diag-ltr');
			var DiagSvr		=	$_this.find('option:selected').attr('data-diag-svr');
			var DiagStg		=	$_this.find('option:selected').attr('data-diag-stg');
			var DiagSite	=	$_this.find('option:selected').attr('data-surgery-site');
			var $_under   = '';
			var TotalRows		=	parseInt($("#TotalRows").val());
			TotalRows	=	TotalRows +1 ;
			
			var $_icd_10_win, $_site_replaced , $_allow_multiple = false;
			var $_icd10_org	=	''; 
			var $_charToReplace = '';
			var $_codeToReplace = '';
			var $_site = ''; 
			
			$_icd_10_win	= DiagCode.indexOf('-');
			$_icd10_org		= DiagCode;
			
			var $_is_site_lid		= DiagSite.toLowerCase().indexOf('lid');
			
			if($_icd_10_win && (DiagLtr == '1' || DiagLtr == '2') )
			{
				if( 'left eye' == DiagSite.toLowerCase() )				$_site='left';
				else if( 'right eye' == DiagSite.toLowerCase() )	$_site='right';
				else if( 'both eye' == DiagSite.toLowerCase() )		$_site='both';
				else $_site = DiagSite.toLowerCase();
				
				if($_site)
				{ 	
					$_under = ($_is_site_lid != -1 ) ? '2' : '1';
					if($_codeToReplace == $_icdSiteArr[$_site])
					{
						if( DiagLtr == $_under )	
						{
							DiagCode = DiagCode.replace('-', $_codeToReplace);
							$_site_replaced=true;
						}
					}
				}
			}
			
			/*if($_icd_10_win && (DiagLtr > 1 || DiagStg || DiagSvr))
			{
				$_allow_multiple = true;
			}
			else if($_icd_10_win && (DiagLtr == 1 && (DiagStg || DiagSvr)))
			{
				$_allow_multiple = true;
			}*/
			
			if($_icd_10_win && DiagLtr == 2) $_allow_multiple = true;
											
			var $_fn_backBgColor	=	"backBgBlueColor('chbx_dig_no"+DiagId+"','tr"+TotalRows+"','#FFFFFF');";
			var $_fn_openItUp		=	($_icd_10_win != '-1')	?	"openItUp('chbx_dig_no"+DiagId+"','list-inner"+TotalRows+"');"	:	'';
			var $_fn_checkThisChecked	=	"checkThisChecked('chbx_dig_no"+DiagId+"','tr"+TotalRows+"','#FFFFFF');";
			var $_fn_targetIcdReplace	=	'';
			
			var $_html	=	'';
			
			$_html	+=	'<li id="tr'+TotalRows+'"  class="list-group-item full_width">';
			$_html	+=	'<input type="hidden" name="diag_names['+DiagId+']" id="diag_names'+DiagId+'" value="'+DiagDesc+'" />';
			$_html	+=	'<input type="hidden" name="diag_codes['+DiagId+']" id="diag_codes'+DiagId+'" value="'+DiagCode+'" />';
			$_html	+=	'<div class="col-md-1 col-sm-1 col-xs-1 col-lg-1">';
			$_html	+=	'<input  type="checkbox" value="'+ DiagId + '~:~' +DiagCode+'" name="diagID[]" id="chbx_dig_no'+DiagId+'" onClick=" '+$_fn_backBgColor + $_fn_openItUp +'" data-dxcode="'+DiagCode+'" >';
			$_html	+=	'</div>';
			
			$_html	+=	'<b class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-left" id="'+TotalRows+'_icd10" data-field="'+$_icd10_org+'" onClick=" '+$_fn_checkThisChecked + $_fn_openItUp +'">';
			$_html	+=	DiagCode;
			$_html	+=	'</b>';
			
			$_html	+=	'<div class="col-md-7 col-sm-7 col-xs-7 col-lg-7 text-left text_list" id="'+TotalRows+'_icd10Desc" data-field="'+DiagDesc+'" onClick="'+$_fn_checkThisChecked + $_fn_openItUp +'">';
			$_html	+=	DiagDesc;
			$_html	+=	'</div>';
			
			if($_allow_multiple) 
			{
				$_html	+=	'<div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 padding_0"  id="'+TotalRows+'_add_icd10" data-diag-ltr="'+DiagLtr+'" data-diag-stg="'+DiagStg+'" data-row-num="'+TotalRows+'" data-row-index="'+TotalRows+'" data-diag-svr="'+DiagSvr+'" data-diag-id="'+DiagId+'" >';
				$_html	+=	'<span class="btn btn-primary btn-xs pull-right">';
				$_html	+=	'<i class="glyphicon glyphicon-plus"></i>';
				$_html	+=	'</span>';
				$_html	+=	'</div>';
			}
			
			$_html	+=	'</li>';
			
			
			if($_icd_10_win != '-1' )
			{
				$_html	+=	'<li class="collapse collapsible_li " id="list-inner'+TotalRows+'_0" style="background:#F0F0F0" data-row-index="0">';
				
				if(DiagLtr)
				{
					$_charToReplace	=	0;
					$_html	+=	'<div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">';
					$_html	+=	'<b class=" padding_15">Site</b>';
					var $_counter	=	0;
					var $_len =Object.keys($_icdLaterArr[DiagLtr]).length 
					var $_targetFunctionCalls = '';
					$.each($_icdLaterArr[DiagLtr], function(key,value)
					{
						var $_inArr	=	value.split('~:~');
						var $_code	=	$_inArr[1];
						var $_title		=	$_inArr[0].ucwords()
						var $_selected=	($_icdSiteArr[$_site] == $_code && DiagLtr == $_under )	?	true : false;
						var $_spanInit= TotalRows+'_0_site_';
						var $_spanID 	=	$_spanInit + $_counter;
				
						$_fn_targetIcdReplace	=	"targetICDreplace('"+TotalRows+"_icd10','"+$_charToReplace+"','"+$_code+"','chbx_dig_no"+DiagId+"','tr"+TotalRows+"','#FFFFFF','"+$_spanInit+"',"+$_len+",'"+$_spanID+"','"+TotalRows+"_icd10Desc','"+$_title+"');";
						
						$_html	+=	'<span class="padding_15 " id="'+$_spanID+'">';
						$_html	+=	'<a href="javascript:void(0)" onClick="'+$_fn_targetIcdReplace+'" data-target="#list-inner'+TotalRows+'_0" data-toggle="collapse in">'+$_title+'</a>';
						$_html	+=	'</span>';
						
						$_counter++;
						
						$_targetFunctionCalls	+=	($_selected)	?	$_fn_targetIcdReplace : '';
								
					});
					$_html	+=	'</div>';
						
				}
				
				if(DiagSvr)
				{
					$_charToReplace		=	(DiagLtr)	?	1	:	0;
					if(DiagLtr)
						$_html	+=	'<div class="clearfix margin_adjustment_only border-dashed"></div>';
					
					$_html	+=	'<div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">';
					$_html	+=	'<b class=" padding_15">Severity</b>';
					
					var $_counter	=	0;
					var $_len =Object.keys($_icdLaterArr[DiagSvr]).length 
					
					$.each($_icdLaterArr[DiagSvr], function(key,value)
					{
						var $_inArr	=	value.split('~:~');
						var $_code	=	$_inArr[1];
						var $_title		=	$_inArr[0].ucwords()
						var $_spanInit= TotalRows+'_0_severity_';
						var $_spanID 	=	$_spanInit + $_counter;
						
						$_fn_targetIcdReplace	=	"targetICDreplace('"+TotalRows+"_icd10','"+$_charToReplace+"','"+$_code+"','chbx_dig_no"+DiagId+"','tr"+TotalRows+"','#FFFFFF','"+$_spanInit+"',"+$_len+",'"+$_spanID+"','"+TotalRows+"_icd10Desc','"+$_title+"')";
						
						$_html	+=	'<span class="padding_15 " id="'+$_spanID+'">';
						$_html	+=	'<a href="javascript:void(0)" onClick="'+$_fn_targetIcdReplace+'" data-target="#list-inner'+TotalRows+'_0" data-toggle="collapse in">'+$_title+'</a>';
						$_html	+=	'</span>';
						
						$_counter++;
						
					});
					
					$_html	+=	'</div>';
				}
				
				
				if(DiagStg)
				{
					$_charToReplace		=	(DiagLtr)	?	1	:	0;
					if(DiagLtr || DiagSvr)
						$_html	+=	'<div class="clearfix margin_adjustment_only border-dashed"></div>';
					
					$_html	+=	'<div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">';
					$_html	+=	'<b class=" padding_15">Staging</b>';
					
					var $_counter	=	0;
					var $_len =Object.keys($_icdLaterArr[DiagStg]).length 
					
					$.each($_icdLaterArr[DiagStg], function(key,value)
					{
						var $_inArr		=	value.split('~:~');
						var $_code		=	$_inArr[1];
						var $_title		=	$_inArr[0].ucwords()
						var $_spanInit= TotalRows+'_0_staging_';
						var $_spanID 	=	$_spanInit + $_counter;
						
						$_fn_targetIcdReplace	=	"targetICDreplace('"+TotalRows+"_icd10','"+$_charToReplace+"','"+$_code+"','chbx_dig_no"+DiagId+"','tr"+TotalRows+"','#FFFFFF','"+$_spanInit+"',"+$_len+",'"+$_spanID+"','"+TotalRows+"_icd10Desc','"+$_title+"')";
						
						$_html	+=	'<span class="padding_15 " id="'+$_spanID+'">';
						$_html	+=	'<a href="javascript:void(0)" onClick="'+$_fn_targetIcdReplace+'" data-target="#list-inner'+TotalRows+'_0" data-toggle="collapse in">'+$_title+'</a>';
						$_html	+=	'</span>';
						
						$_counter++;
						
					});
					
					$_html	+=	'</div>';
				}
				
				$_html	+=	'</li>';	
			}
		
      $_html	+=	'<div class="clearfix"></div>';
			
			$("#DxCodes").append($_html);
			$("#TotalRows").val(TotalRows);
			
			// Refresh Selectptcker
			$_this.find('option:selected').remove();
			$_this.val('').selectpicker('refresh');
			
			// Triggering On Click Functions
			eval($_fn_backBgColor);
			eval($_fn_checkThisChecked);
			eval($_fn_openItUp);			
			eval($_targetFunctionCalls);
			
			$("ul#DxCodes li[id='tr"+TotalRows+"']").trigger('click');
			
	});
	
	$('body').on('change','.AdditionalCodeICD9',function(){
				
				var $_this			=	$(this);
				var DiagId		=	$_this.val();
				var DiagDesc	=	$_this.find('option:selected').attr('data-diag-desc');
				var DiagCode	=	$_this.find('option:selected').attr('data-diag-code');
				var DiagAlias	=	$_this.find('option:selected').attr('data-diag-alias');
				var DiagSite		=	$_this.find('option:selected').attr('data-surgery-site');
				var TotalRows	=	parseInt($("#TotalRows").val());
				TotalRows		=	TotalRows +1 ;
				
				var $_fn_backBgColor	=	"backBgBlueColor('chbx_dig_no"+DiagId+"','tr"+TotalRows+"','#FFFFFF');";
				var $_fn_checkThisChecked	=	"checkThisChecked('chbx_dig_no"+DiagId+"','tr"+TotalRows+"','#FFFFFF');";
			
				var $_html	=	'';
				
				$_html	+=	'<li id="tr'+TotalRows+'"  class="list-group-item full_width">';
				$_html	+=	'<input type="hidden" name="diag_names['+DiagId+']" id="diag_names'+DiagId+'" value="'+DiagDesc+'" />';
				$_html	+=	'<div class="col-md-1 col-sm-1 col-xs-1 col-lg-1  text-left">';
				$_html	+=	'<input type="checkbox" value="'+DiagId+'" name="diagID[]" id="chbx_dig_no'+DiagId+'" onClick="'+$_fn_backBgColor+'" data-dxcode="'+DiagCode+'">';
				$_html	+=	'</div>';
				
				$_html	+=	'<b class="col-md-2 col-sm-2 col-xs-2 col-lg-2 text-left" onClick="'+$_fn_checkThisChecked+'"> ';
				$_html	+=	DiagCode;
				$_html	+=	'</b>';
				
				$_html	+=	'<div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 text-left text_list" onClick="'+$_fn_checkThisChecked+'">';
				$_html	+=	DiagDesc
				$_html	+=	'</div>';
				
				$_html	+=	'</li>	';
				$_html	+=	'<div class="clearfix"></div>';
				
				$("#DxCodes").append($_html);
				$("#TotalRows").val(TotalRows);
				
				// Refresh Selectptcker
				$_this.find('option:selected').remove();
				$_this.val('').selectpicker('refresh');
				
				// Triggering On Click Functions
				eval($_fn_backBgColor);
				eval($_fn_checkThisChecked);
				
				$("ul#DxCodes li").trigger('click');
	});
	
	/*** Super Bill Code ***/
	
	$("body").on('click, focus', '.modifiers' , function()
	{	
		if($("#preDefineModifiers").hasClass('in'))
		{
			$("#preDefineModifiers").fadeOut(50);
			$("#hiddPopUpField").val('');
		}
		var Id		=	$(this).attr('id');
		var Left	=	$(this).offset().left;
		var Top	=	$(this).offset().top +$(this).outerHeight(true)  ;
		
		if( $(document).width() < ( Left + $("#preDefineModifiers").outerWidth(true) +15 ) )
		{
			Left	=	Left	+	( $(document).width() - (Left + $("#preDefineModifiers").outerWidth(true) +15 ))	;
		}
		
		$("#preDefineModifiers").css({'left':Left+'px','top':Top+'px'}).fadeIn(50);
		
		$("#hiddPopUpField").val(Id);
	});
	
	$("body").on("mouseleave","#preDefineModifiers", function(){
		if($("#hiddPopUpField").val())
		{
			$(this).fadeOut(100);	
			$("#hiddPopUpField").val('');
		}
	});

	//Collect All Selected Dx Codes
	var DxCodes	=	[] ;
	function collectDxCodes()
	{
		DxCodes	=	[];
		$("#DxCodes input[type='checkbox']:checked ").each(function(){
			
			var DxCode	=	$(this).attr('data-dxcode');
			DxCode = DxCode.split('@@');
			$.each(DxCode,function(i,v){
				if( v.indexOf('-') === -1)
				{
					DxCodes.push(v);
				}
			});
		});
		
	}
	collectDxCodes();
	
	function updateDxCodes(isLoaded)
	{ 
		$("ul#SuperBillSurgeon [id^='dxCodes_'], ul#SuperBillFacility [id^='dxCodes_'], ul#SuperBillAnesthesia [id^='dxCodes_']").each(function()
		{
			var $_this	=	$(this) ;
			
			//Collect Selected Dx Codes
			var tempArr	=	[];
			$_this.find('option:selected').each(function(){
				tempArr.push($(this).val());
			});
			
			var tempArr1	=	[];
			$_this.find('option:not(:selected)').each(function(){
				tempArr1.push($(this).val());
			});
			
			// Remove all existing Dx Codes in superbills
			$('option', $_this).remove();
			
			// Update New Dx Codes in super bills
			$.each(DxCodes,function(i,v)
			{
				var option = new Option(v,v);
				if(jQuery.inArray(v,tempArr) >= 0) {
					option.selected=true;
				}
				else if(isLoaded =='yes' && jQuery.inArray(v,DxCodes) >= 0)
				{
					option.selected=false		
				}
				else if(isLoaded =='no' && jQuery.inArray(v,tempArr1) >= 0 )
				{
					option.selected=false		
				}
				else
				{
					option.selected=true;	
				}
				$_this.append($(option));
				
			});		
			
			$_this.selectpicker('refresh');		//Rendering Selectpicker
		});	
	}
	updateDxCodes('yes');
	
	$("body").on('click','ul#DxCodes li ',function(){
		collectDxCodes();
		updateDxCodes('no');
	});
	
	function displaySuperBillHeader(type)
	{
		if( type )
		{
			var c = (type == '2' ? 'S' : (type == '1' ? 'A' : 'F' )); 
			var s	=	$("#superBillHeaderDisplayStatus"+type+"").val();
			if( s == '0' )
			{
					$("li#superBillHeader"+type+"").show(50);
					$("#superBillHeaderDisplayStatus"+type+"").val('1');		
			}
		}
	}
	
	function addCptInSuperBill(obj,type)
	{
		var CptId		=	obj.val();
		var CptCode	=	$("#procedureCode_"+CptId).val();
		var CptCatName=	obj.attr('data-category');
		var CptPracticeName			=	obj.attr('data-surgeon-code');
		var CptFacilityName			=	obj.attr('data-facility-code');
		var stopParentSuperbill 	=	obj.attr('data-stop_parent_superbill');
		var billUserType =	(CptCatName == 'Anesthesia') ? 1 : type;
		var suffix = CptId + '_' + billUserType;
		var billUserTypeTxt = '';
		if( billUserType == 1) { 
			billUserTypeTxt = 'Anesthesia';
		}
		else if( billUserType == 2) { 
			billUserTypeTxt = 'Surgeon';
			if(CptPracticeName == '' && stopParentSuperbill=='YES') {
				return;	
			}
		}
		else if( billUserType == 3) {
			billUserTypeTxt = 'Facility';
			if(CptFacilityName == '' && stopParentSuperbill=='YES') {
				return;	
			}
		}
		 
		var GroupObj	=	$("ul#SuperBill"+billUserTypeTxt+"");
		var ListObj		=	GroupObj.find('li[data-cpt-id="'+CptId+'"]');
		var HtmlObj		=	ListObj.find('input,select');
		
		if(CptCode && billUserType && GroupObj )
		{
			displaySuperBillHeader(billUserType);
			
			if(ListObj.length)
			{
				var DbObj =	ListObj.find('#superBillRecordId_'+suffix+'');
				if(DbObj.length){  
					var DbId	=	DbObj.val(); 
					var DelObj	=	$("#deleteSuperBillRecords");
					var DelVal	=	DelObj.val();
					var R	=	DelVal.replace(','+DbId,'');
					DelObj.val(R);
				}
				ListObj.show();
				HtmlObj.prop('disabled',false);
			}
			else
			{
				var Html	=	'';
				
				var mods	 =	filter_cpt_modifiers(CptCode);
				var mod1   =	(mods) ? mods.mod1 : '';
				var mod2   =	(mods) ? mods.mod2 : '';
				var mod3   =	(mods) ? mods.mod3 : '';
				//var units  =	(mods && mods.units) ? mods.units : '1';
				var units  =	($_procUnitsArr[CptId] && $_procUnitsArr[CptId].units && $_procUnitsArr[CptId].units!='0') ? $_procUnitsArr[CptId].units : '1';
				
				if( mod1 == '' || mod2 == '' || mod3 == '' ) {
					mods = $_sitePoeMod[CptCode];
					if( !mods ) mods = $_sitePoeMod['pri'];
					for(var i in mods){
						var k = mods[i];
						if( k && mod1 == '') mod1 = k;
						else if( k && mod2 == '') mod2 = k;
						else if( k && mod3 == '') mod3 = k;
					}
				}
				
				//START ADD ASA MODIFIER CODE (from global) FOR ANESTHESIA IF ANY OF 3 MODIFIERS IS BLANK
				var asaModifierCode = '<?php echo $asaModifierCode; ?>';
				if(asaModifierCode != "" && billUserType=='1' && (mod1 == '' || mod2 == '' || mod3 == '') ) {
					if(mod1 == '') {
						mod1 = asaModifierCode;
					}else if(mod2 == '') {
						mod2 = asaModifierCode;
					}else if(mod3 == '') {
						mod3 = asaModifierCode;
					}
				}
				//END ADD ASA MODIFIER CODE (from global) FOR ANESTHESIA IF ANY OF 3 MODIFIERS IS BLANK
				
				//START CODE IF SETTING IS OFF THEN STOP MODIFERS TO AUTOFILL
				
				var autofill_modifiers = '<?php echo $autofill_modifiers; ?>';
				if(!autofill_modifiers || autofill_modifiers == '0') {
					mod1 = mod2 = mod3 = '';	
				}
				//END CODE IF SETTING IS OFF THEN STOP MODIFERS TO AUTOFILL
				
				
				Html	+=	'<li class="list-group-item full_width padding_0" data-cpt-id= "'+CptId+'">';
				Html	+=	'<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0">';
				Html	+=	'<span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 padding_2"  >';
				Html	+=	'<input type="hidden" name="billUserType[]" id="billUserType_'+suffix+'" value="'+billUserType+'"  />';
				Html	+=	'<input type="hidden" name="superBillCptId[]" id="superBillCptId_'+suffix+'" value="'+CptId+'"  />';
				Html	+=	'<input type="text" class="form-control" readonly name="superBillCptCodes[]" id="cptCode_'+suffix+'" value="'+CptCode+'" />';
				Html	+=	'</span>';
				
				Html	+=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 ">';
				Html	+=	'<input type="text" class="form-control" name="superBillQuantity[]" id="quantity_'+suffix+'" min="1" value="'+units+'" />';
				Html	+=	'</span>';
				
				Html	+=	'<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 padding_2">';
				Html	+=	'<select name="superBillDxCodes_'+suffix+'[]" id="dxCodes_'+suffix+'" class="selectpicker" multiple title="Dx Codes" data-header="Dx Codes">';
				$.each(DxCodes,function(i,v){
					Html	+=	'<option value="'+v+'" selected>'+v+'</option>';	
				});
				Html	+=	'</select>';
				Html	+=	'</span>';
				
				Html	+=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 ">';
				Html	+=	'<input type="text" class="form-control padding_2 modifiers" name="superBillModifier1[]" id="modifier1_'+suffix+'" placeholder="Mod1" value="'+mod1+'" />';
				Html	+=	'</span>';
				
				Html	+=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 ">';
				Html	+=	'<input type="text" class="form-control padding_2 modifiers" name="superBillModifier2[]" id="modifier2_'+suffix+'" placeholder="Mod2" value="'+mod2+'" />';
				Html	+=	'</span>';
				
				Html	+=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_2 ">';
				Html	+=	'<input type="text" class="form-control padding_2 modifiers" name="superBillModifier3[]" id="modifier3_'+suffix+'" placeholder="Mod3" value="'+mod3+'" />';
				Html	+=	'</span>';
				
				Html	+=	'<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1 padding_0 " style="padding-top:7px !important;" >';
				Html	+=	'<a class="fa fa-times-circle-o" style="color:#e5870b; font-size:22px; margin-left:10px;" title="Remove" id="removeCpt_'+suffix+'"></a>';
				Html	+=	'</span>';
				
				Html	+=	'</div>';
				Html	+=	'</li>';
				
				GroupObj.append(Html);
				$('#dxCodes_'+suffix+'').selectpicker();
			}
		}
	}
	
	function removeCptInSuperBill(obj,type)
	{
		var CptId		=	obj.val();
		var CptCatName	=	obj.attr('data-category');
		var billUserType =	(CptCatName == 'Anesthesia') ? 1 : type;
		var suffix = CptId + '_' + billUserType;
		var billUserTypeTxt = '';
		if( billUserType == 1)  billUserTypeTxt = 'Anesthesia';
		else if( billUserType == 2)  billUserTypeTxt = 'Surgeon';
		else if( billUserType == 3)  billUserTypeTxt = 'Facility';
		
		var GroupObj	=	$("ul#SuperBill"+billUserTypeTxt+"");
		var ListObj	=	GroupObj.find('li[data-cpt-id="'+CptId+'"]');
		var HtmlObj	=	ListObj.find('input,select');
		var DbObj	=	ListObj.find('#superBillRecordId_'+suffix+'');
		if(DbObj.length){ 
			var DbId	=	DbObj.val(); 
			var DelObj	=	$("#deleteSuperBillRecords");
			var DelVal	=	DelObj.val();
			DelVal		+=	','+DbId;
			DelObj.val(DelVal);
		}
		ListObj.hide();
		
		// Check in Surgeon/Facility Superbill if cpt exists 
		var revBillUserTypeTxt = '';
		if( type == 2 ) revBillUserTypeTxt = 'Facility';
		else if( type == 3 ) revBillUserTypeTxt = 'Surgeon';
		
		var revGroupObj	=	$("ul#SuperBill"+revBillUserTypeTxt+"");
		var revListObj	=	revGroupObj.find('li[data-cpt-id="'+CptId+'"]');
		if( revListObj.css('display') == 'none' || type == 1 )
		{
			obj.prop('checked',false);
			var T	=	obj.attr('onClick');
			T	=	T.replace(/return/gi,'');
			var sArr	=	T .split(';');
			for(var i in sArr)
			{
				if(sArr[i])
				{
					var func	=	sArr[i]+';';
					eval(func);	
				}
			}
			
			var ObjId	=	obj.attr('id');
			var RowId=	obj.closest('li').attr('id')
			backBgBlueColor(ObjId,RowId,'#FFFFFF');
		}
		HtmlObj.prop('disabled',true);
		
	}
	
	$("body").on('click', ' [id^="CPT_"] li  ', function(){
		var chkBoxObj	=	$(this).find('input[type="checkbox"]');
		if(chkBoxObj.is(':checked'))
		{ 
			addCptInSuperBill(chkBoxObj,2);
			addCptInSuperBill(chkBoxObj,3);
		}
		else
		{
			removeCptInSuperBill(chkBoxObj,2);
			removeCptInSuperBill(chkBoxObj,3);
		}
	
	});
	
	$("body").on('click', ' [id^="removeCpt_"] ', function(){
		
		var Id	=	$(this).attr('id');
		var arr	=	Id.split('_');
		var chkBoxObj	=	$("#chbx_fdt_no"+arr[1]);
		var type = arr[2];
		removeCptInSuperBill(chkBoxObj,type);	
		
	});
	
	// Start Code to add Checked Cpt Codes in superbills on load
	var procDB = '<?php echo $procedureDBExists; ?>';
	$('[id^="CPT_"] li input[type="checkbox"]:checked ').each(function()
	{
		var chkBoxObj	=	$(this);
		if( !procDB )
		{
			addCptInSuperBill(chkBoxObj,2);
			addCptInSuperBill(chkBoxObj,3);
		}
	});
	// End Code to add Checked Cpt Codes in superbills on load
	
	/*** End Super Bill Code ***/	
	
	
	$('body').on('click','div[id$="add_icd10"]',function(){
		var $_this  = $(this);
		var DiagId	=	$_this.attr('data-diag-id');
		var DiagLtr	=	$_this.attr('data-diag-ltr');
		var DiagSvr	=	$_this.attr('data-diag-svr');
		var DiagStg	=	$_this.attr('data-diag-stg');
		var DataRow = $_this.attr('data-row-num');
		var DataInx = $_this.attr('data-row-index');
		
		var CodeDiv	=	$("#"+DataRow+"_icd10");
		var CodeChk	=	$('#chbx_dig_no' +DiagId);
		var OrigCode=	CodeDiv.attr('data-field');
		
		var CodeHtml= $.trim(CodeDiv.html());
		var CodeVal	= $.trim(CodeChk.val());
		CodeDiv.html(CodeHtml + ', ' + OrigCode);
		CodeChk.val(CodeVal+'@@'+OrigCode);
		
		
		var l_sub = $('li[id^="list-inner'+DataRow+'_"]').last();
		var l_index = parseInt(l_sub.attr('data-row-index'));
		
		var n_index = l_index + 1;
		var list_id = 'list-inner'+ DataRow + '_' + n_index;
		var bgColor = (n_index%2 == 0) ? '#FDFDFD' : '#F0F0F0'; 
		
		var $_html = '';
		var $_charToReplace = '';
		var $_codeToReplace = '';
		var $_targetFunctionCalls = '';
		
		$_html	+=	'<li class="collapse in collapsible_li " id="'+list_id+'" style="background:'+bgColor+'" data-row-index="'+n_index+'">';
		
		if(DiagLtr)
		{
			$_charToReplace	=	0;
			$_html	+=	'<div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">';
			$_html	+=	'<b class=" padding_15">Site</b>';
			var $_counter	=	0;
			var $_len =Object.keys($_icdLaterArr[DiagLtr]).length 
			
			$.each($_icdLaterArr[DiagLtr], function(key,value)
			{
				var $_inArr		=	value.split('~:~');
				var $_code		=	$_inArr[1];
				var $_title		=	$_inArr[0].ucwords();
				var $_spanInit= DataRow+'_'+n_index+'_site_';
				var $_spanID 	=	$_spanInit + $_counter;
				
				$_fn_targetIcdReplace	=	"targetICDreplace('"+DataRow+"_icd10','"+$_charToReplace+"','"+$_code+"','chbx_dig_no"+DiagId+"','tr"+DataInx+"','#FFFFFF','"+$_spanInit+"',"+$_len+",'"+$_spanID+"','"+DataRow+"_icd10Desc','"+$_title+"');";
				
				$_html	+= '<span class="padding_15 " id="'+$_spanID+'">';
				$_html	+= '<a href="javascript:void(0)" onClick="'+$_fn_targetIcdReplace+'" data-target="#'+list_id+'" data-toggle="collapse in">'+$_title+'</a>';
				$_html	+=	'</span>';
				
				$_counter++;
				
			});
			$_html	+=	'</div>';
			
			//$_targetFunctionCalls	+=	($_selClass)	?	$_fn_targetIcdReplace : '';
				
		}
		
		if(DiagSvr)
		{
			$_charToReplace	=	(DiagLtr)	?	1	:	0;
			if(DiagLtr) $_html	+=	'<div class="clearfix margin_adjustment_only border-dashed"></div>';
			
			$_html	+=	'<div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">';
			$_html	+=	'<b class=" padding_15">Severity</b>';
			
			var $_counter	=	0;
			var $_len =Object.keys($_icdLaterArr[DiagSvr]).length 
			
			$.each($_icdLaterArr[DiagSvr], function(key,value)
			{
				var $_inArr	=	value.split('~:~');
				var $_code	=	$_inArr[1];
				var $_title	=	$_inArr[0].ucwords()
				var $_spanInit= DataRow+'_'+n_index+'_severity_';
				var $_spanID 	=	$_spanInit + $_counter;
				
				$_fn_targetIcdReplace	=	"targetICDreplace('"+DataRow+"_icd10','"+$_charToReplace+"','"+$_code+"','chbx_dig_no"+DiagId+"','tr"+DataInx+"','#FFFFFF','"+$_spanInit+"',"+$_len+",'"+$_spanID+"','"+DataRow+"_icd10Desc','"+$_title+"')";
				
				$_html	+=	'<span class="padding_15 " id="'+$_spanID+'">';
				$_html	+=	'<a href="javascript:void(0)" onClick="'+$_fn_targetIcdReplace+'" data-target="#'+list_id+'" data-toggle="collapse in">'+$_title+'</a>';
				$_html	+=	'</span>';
				
				$_counter++;
				
				//$_targetFunctionCalls	+=	($_selClass)	?	$_fn_targetIcdReplace : '';
						
			});
			
			$_html	+=	'</div>';
		}
		
		if(DiagStg)
		{
			$_charToReplace		=	(DiagLtr)	?	1	:	0;
			if(DiagLtr || DiagSvr) $_html	+=	'<div class="clearfix margin_adjustment_only border-dashed"></div>';
			
			$_html	+=	'<div class="row col-md-11 col-sm-11 col-xs-11 col-lg-11">';
			$_html	+=	'<b class=" padding_15">Staging</b>';
			
			var $_counter	=	0;
			var $_len =Object.keys($_icdLaterArr[DiagStg]).length 
			var $_targetFunctionCalls = '';
			$.each($_icdLaterArr[DiagStg], function(key,value)
			{
				var $_inArr		=	value.split('~:~');
				var $_code		=	$_inArr[1];
				var $_title		=	$_inArr[0].ucwords()
				var $_spanInit= DataRow+'_'+n_index+'_staging_';
				var $_spanID 	=	$_spanInit + $_counter;
				
				$_fn_targetIcdReplace	=	"targetICDreplace('"+DataRow+"_icd10','"+$_charToReplace+"','"+$_code+"','chbx_dig_no"+DiagId+"','tr"+DataInx+"','#FFFFFF','"+$_spanInit+"',"+$_len+",'"+$_spanID+"','"+DataRow+"_icd10Desc','"+$_title+"')";
				
				$_html	+=	'<span class="padding_15 " id="'+$_spanID+'">';
				$_html	+=	'<a href="javascript:void(0)" onClick="'+$_fn_targetIcdReplace+'" data-target="#'+list_id+'" data-toggle="collapse in">'+$_title+'</a>';
				$_html	+=	'</span>';
				
				$_counter++;
				
				//$_targetFunctionCalls	+=	($_selClass)	?	$_fn_targetIcdReplace : '';
						
			});
			
			$_html	+=	'</div>';
		}
		
		$_html	+=	'</li>';
		l_sub.after($_html);
			
	});
	
});

</script>
<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>