<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php

$patient_id = $_REQUEST['patient_id'];
$pConfId 	= $_REQUEST['pConfId'];
if(!$pConfId) 	{$pConfId = $_SESSION['pConfId'];  }
if(!$patient_id){$patient_id = $_SESSION['patient_id'];  }


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
	
//GETTING IF ALREADY EXISTS
$dischargeDetails = $objManageData->getRowRecord("dischargesummarysheet", "confirmation_id", $pConfId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat ");

$finalizeStatus = $dischargeDetails->finalize_status;

if($dischargeDetails->form_status <> 'completed' && $dischargeDetails->form_status <> 'not completed' && $loggedUserType == 'Surgeon' )
{
	include_once("common/commonFunctions.php"); 
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	extract($_GET);
	$idoc_mod = idoc_modifiers();

?>
	<script type="text/javascript" src="js/webtoolkit.aim.js"></script>
    <script>
		top.document.getElementById('confirmType').value = 'discharge' ;
	</script>
    <style>
		.table > tbody > tr > td { padding:4px !important; }
	</style>
    
    <div class="modal fade" id="dischargeConfirmModal" data-backdrop="false" style="min-width:1200px; top: 18px;"  >
 		<div class="modal-dialog modal-lg" style="min-width:1200px;">
        	<div class="modal-content">
		
            	<div class="modal-header text-center" style="width:100%">
            	
                    <div id="ModalTitle" style="position:absolute; text-align:center; width:100%; color:white"></div>
                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title rob" style="text-align:left; color:white;">
                    	<span style="width:305px; color:white;"></span>
                    
                    </h4>  
    			</div>
    		
            	<div class="clearfix margin_adjustment_only"></div>
            
    	        <div class="modal-body" style="postion:relative; max-height:450px; overflow:hidden; overflow-y:auto; width:100%; ">
                

<?php


	//GETTING PATIENTCONFIRMATION DETAILS
	$confirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	$primary_procedure_id = $confirmationDetails->patient_primary_procedure_id;
	$primary_procedure_name = $confirmationDetails->patient_primary_procedure;
	$secondary_procedure_id = $confirmationDetails->patient_secondary_procedure_id;
	$tertiary_procedure_id = $confirmationDetails->patient_tertiary_procedure_id;
	$primary_procedure_is_inj_misc =	$confirmationDetails->prim_proc_is_misc;	
	$surgeonId = $confirmationDetails->surgeonId;
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
			$nameOfSurgeon  = $lname_Surgeon.", ".$fname_Surgeon." ".$mname_Surgeon;
		}
	}
	//GETTING SURGEONDETAILS

	$primProcDxCodes	=	$secProcDxCodes	=	$terProcDxCodes = '';

	//GETTING SURGEON PROFILE FOR PRIMARY/SECONDARY/TERITARY PROCEDURE
		$cpt_id_arr = $cpt_id_default_arr = $cpt_id_anes_arr = $cpt_id_anes_default_arr = $dx_id_arr = $dx_id_default_arr = $dx_id_icd10_arr = $dx_id_default_icd10_arr = array();
		if($surgeonId<>"") {
			$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
			$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
			while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
				$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
			}
			if(is_array($surgeonProfileIdArr)){
				$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
			}else {
				$surgeonProfileIdImplode = 0;
			}
			$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
			$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
			$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
			if($selectSurgeonProcedureNumRow>0) {
				while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
					$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
					if($primary_procedure_id == $surgeonProfileProcedureId || $secondary_procedure_id == $surgeonProfileProcedureId || $tertiary_procedure_id == $surgeonProfileProcedureId) {
						$dischargeSheetFound = "true";
						
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
					$selectSurgeonRes 				= imw_query($selectSurgeonQry) or die(imw_error());
					while($selectSurgeonRow 		= imw_fetch_array($selectSurgeonRes)) {
						$surgeonProfileIdArrInst[] 	= $selectSurgeonRow['surgeonProfileId'];
					}
					if(is_array($surgeonProfileIdArrInst)){
						$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArrInst);
					}else {
						$surgeonProfileIdImplode = 0;
					}
					$selectSurgeonProcedureQry 		= "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) AND (cpt_id!='' or dx_id!='' or dx_id_icd10!='') order by procedureName";
					$selectSurgeonProcedureRes 		= imw_query($selectSurgeonProcedureQry) or die(imw_error());
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
		if($dischargeSheetFound<>"true")
		{
			$proceduresArr	=	array($primary_procedure_id,$secondary_procedure_id,$tertiary_procedure_id);
			foreach($proceduresArr as $procedureId)
			{
				if($procedureId)
				{		
					$procPrefCardQry	=	"Select * From procedureprofile Where procedureId = '".$procedureId."' ";
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
		
			$cpt_id				=	$cpt_id .(($cpt_id && $cpt_id_anes) ? ',' : ''). $cpt_id_anes ;
			$cpt_id_default		=	$cpt_id_default .(($cpt_id_default && $cpt_id_anes_default) ? ',' : ''). $cpt_id_anes_default ;
		}
		
		
		/*****
		* End Procedure Preference Card 
		*****/
		
		
		// Check If Procedure is Injection Procedure
		$primProcDetails	=	$objManageData->getRowRecord('procedures','procedureId',$primary_procedure_id,'','','catId');
		if( $primProcDetails->catId <> '2' )
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
					$fields	=	'cpt_id,cpt_id_default,dx_id,dx_id_default,dx_id_icd10,dx_id_default_icd10';
					$defaultProfile	= $objManageData->injectionProfile($procedureID,$surgeonId,$fields);
					
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
		
		/*
		*
		* Start Laser Procedure Template
		*
		*/
			//$primProcDetails	=	$objManageData->getRowRecord('procedures','procedureId',$primary_procedure_id,'','','catId');
			if( $primProcDetails->catId == '2')
			{
				unset($condArr);
				$condArr['1']	=	'1' ;
				$xtraCondition	=	" And catId = '2'  And procedureId IN (".$primary_procedure_id."".
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
						$laserProcTempQry =	"SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$procData->procedureId."' And (FIND_IN_SET(".$confirmationDetails->surgeonId.",laser_surgeonID))  ORDER BY laser_templateID DESC LIMIT 0,1 ";
						$laserProcTempSql	=	imw_query($laserProcTempQry) or die('Error found at line no '.(__LINE_).': '. imw_error());
						$laserProcTempCnt	=	imw_num_rows($laserProcTempSql);
						if( $laserProcTempCnt == 0 )
						{
							$laserProcTempQry =	"SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$procData->procedureId."' And laser_surgeonID = 'all'  ORDER BY laser_templateID DESC LIMIT 0,1  ";
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
					$cpt_id_default				= ($laserDefaultCptId)			?	$laserDefaultCptId			: $cpt_id_default;
					$dx_id 							= ($laserDxId)						?	$laserDxId						: $dx_id;
					$dx_id_default				= ($laserDefaultDxId)			?	$laserDefaultDxId			: $dx_id_default;
					$dx_id_icd10 				= ($laserDxIcd10Id)				?	$laserDxIcd10Id				: $dx_id_icd10;
					$dx_id_default_icd10	= ($laserDefaultDxIcd10Id)	?	$laserDefaultDxIcd10Id	: $dx_id_default_icd10;
					
				}
				
			}
		/*
		*
		* End Laser Procedure Template
		*
		*/

		// Start Filter Dx Codes related to Primary & Secondary Procedures
		$primProcDxCodes	=	array_filter(array_unique(explode(",",$primProcDxCodes)));
		$secProcDxCodes		=	array_filter(array_unique(explode(",",$secProcDxCodes)));
		$terProcDxCodes		=	array_filter(array_unique(explode(",",$terProcDxCodes)));
		// End Filter Dx Codes related to Primary & Secondary Procedures
		
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
		$getSiteCodeUQ=imw_query("SELECT code,title FROM `icd10_laterality` where under in (".$siteCatIdImp.") and deleted=0");
		while($getSiteCodeUD = imw_fetch_object($getSiteCodeUQ))
		{
			$siteCode = $getSiteCodeUD->code;
			$siteCodeTitle = strtolower($getSiteCodeUD->title);
			$siteArr[strtolower($getSiteCodeUD->title)]=$siteCode;
		}
		//$getSiteCodeUQ.close;
		//$getSiteCodeQ.close;

		//get typ of dx codes
		$queryDxTyp=imw_query("select `diagnosis_code_type` from `surgerycenter`")or die(imw_error());
		$dataDxTyp=imw_fetch_object($queryDxTyp);
		if($dataDxTyp->diagnosis_code_type)
		{
			$dx_code_type=$dataDxTyp->diagnosis_code_type;
		}
		else $dx_code_type='icd9';
		
		//$queryDxTyp.close;
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
			//$query.close;
		}

		//UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE
		$ViewOpRoomRecordQry = "select operatingRoomRecordsId,iol_ScanUpload,iol_ScanUpload2,post2DischargeSummary from `operatingroomrecords` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
		$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
		if($ViewOpRoomRecordNumRow>0) {
					$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
					$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
					$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
					$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
					$post2DischargeSummary = $ViewOpRoomRecordRow["post2DischargeSummary"];
				}
		//UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE

?>
<script type="text/javascript">

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
				}
			}else if(obj_chbxId.checked==false) {
				if(obj_rowId) {
					//obj_rowId.style.background = prevColorId;
					//obj_rowId.className = 'text_10';
					$("#"+rowId).removeClass("list-group-item-primary active");
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

</script>

<?php
	$dx_code_table	=		($dx_code_type == 'icd10') ? '' : '' ;
		
	//START TO COUNT ALL
	
	// Start code to get unique procedure from DB & Template
	$andProcQry="";
	if($cpt_id)
	{
		$cpt_id=(substr($cpt_id,-1,1)==",") ? substr($cpt_id,0,(strlen($cpt_id)-1)) : $cpt_id;
		$cpt_id=	($procedures_IdDB)	?	implode(',',array_filter(array_unique(explode(",",$cpt_id . ','.$procedures_IdDB))))	:	$cpt_id;
		$andProcQry = " AND procedureId IN(".$cpt_id.") "; 
	}
	// End code to get unique procedure from DB & Template
			
	// *** Procedure Categories
	$countProcedureCat = array();	
	$getProcedureCatQry = "select * from `procedurescategory`";
	$getProcedureCatRes = imw_query($getProcedureCatQry) or die(imw_error()); 
	while($getProcedureCatRow = imw_fetch_array($getProcedureCatRes)){
		$countProcedureCat[] = $getProcedureCatRow['proceduresCategoryId'];
	}
	if(is_array($countProcedureCat)){
		$procedureCategoryImplode =implode(',',$countProcedureCat);
	}
			
	/* Procedure List Based on categories and filtered from DB & Template	*/
	if($procedureCategoryImplode){
		$getProcedureQry = "select * from `procedures` where catId IN($procedureCategoryImplode) ".$andProcQry;
		$getProcedureRes = imw_query($getProcedureQry) or die(imw_error()); 
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
		
	
		
?>		




<!--slider_content-->
<!--<div class=" scheduler_table_Complete slider_content" id="" style="">-->
<?php
	$selCptCodesID = $selAnesCptCodes = $selCptCodes = $selDxCodes	= '';
?>
<table class="left-part-pre ">
	<tr>
    	<td style="width:50%;">
        	<table class="left-part-pre ">
        		<tr>
                	<td>
            			<table class=" table table-bordered">
                        	<tr>
                            	<td colspan="3" class="sectionHeader">CPT Codes</td>
                           	</tr>
                            <tr>
                            	<td style="width:8%; white-space:nowrap;" class="rowHeader">Cpt Code</td>
                                <td style="width:85%;" class="rowHeader">Description</td>
                                <td style="width:7%; white-space:nowrap;" class="rowHeader">Yes</td>
                           	</tr>     
                                
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
										$procCategory = $categry->name;
										//$cpt_id_default 		= $selectSurgeonProcedureRow['cpt_id_default'];
										$getProcedureQry = "SELECT procedureId,name,code,del_status FROM procedures WHERE catId='".$procCategoryId."' $andProcQry ORDER BY code ASC, name ASC";
										$getProcedureRes = imw_query($getProcedureQry) or die($getProcedureQry.imw_error());
										$getProceduresDetails = array();
										if($getProcedureRes){
											while($getProcedureRow = imw_fetch_object($getProcedureRes)){
												$getProceduresDetails[] = $getProcedureRow;
											}		
										}
										//$getProceduresDetails = $objManageData->getArrayRecords('procedures', 'catId', $procCategoryId,'code, name', 'ASC' );						
										
										
												
												if(is_array($getProceduresDetails) && count($getProceduresDetails) > 0 )
												{
											?>
										   		
												<tr>
                                                	<td colspan="3" class="subHeader"><?php echo $procCategory; ?></td>
                                                </tr>
                                                
                                          	<?php
                                                    foreach($getProceduresDetails as $Procedures)
                                                    {
                                                        $Procedures_id 			= stripslashes($Procedures->procedureId);
                                                        $Procedures_name 		= stripslashes($Procedures->name);
                                                        $Procedures_code 		= stripslashes($Procedures->code);
                                                        $Procedures_del_status 	= stripslashes($Procedures->del_status);
                                                    
                                                        if($Procedures_del_status!='yes' || in_array($Procedures_id,$procedures_idDBExplodeArr)) 
                                                        {
                                                            
															$isChecked		=	in_array($Procedures_id, $procedures_idDBExplodeArr) ? 'Yes' : '';
															$selCptCodesID	.=	($isChecked) ? ','.$Procedures_id : '';
															if($procCategory == 'Anesthesia')
															{
																$selAnesCptCodes.=	($isChecked) ? ','.$Procedures_code : '';	
															}
															else
															{
																$selCptCodes	.=	($isChecked) ? ','.$Procedures_code : '';
															}
															
                                                ?>
                                                            
                                                            <tr>
                                                                <td><?php echo $Procedures_code; ?></td>
                                                                <td><?php echo $Procedures_name;?></td>
                                                                <td><?php echo $isChecked;?></td>
                                                            </tr>
                                                            
                                                <?php
                                                        
                                                        }
                                                    }
                                                
                                                
												}
									
									}
									
								}  //end of checking procedure cats 
								
								$selCptCodesID	=	substr($selCptCodesID,1);
								$selCptCodes	=	substr($selCptCodes,1);
								$selAnesCptCodes=	substr($selAnesCptCodes,1);
								
							?>
                    	</table>
                   	</td>
              	</tr>
                
                <!-- Start Code for Super Bill Practice -->
                <tr>
                	<td>
            				<table class="table table-bordered">
                			<tr>
                      	<td colspan="6" class="sectionHeader">Discharge Summary (Surgeon)</td>
                    	</tr>
                      <tr>
                      	<td style="width:10%; white-space:nowrap;" class="rowHeader">CPT Codes</td>
                        <td style="width:7%; white-space:nowrap;" class="rowHeader">Unit</td>
                        <td style="width:50%;"class="rowHeader">Dx Codes</td>
                        <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod1</td>
                        <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod2</td>
                        <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod3</td>
                     	</tr>
                      <?php
								$cptArr	=	array_filter(explode(',',$selCptCodes));
								if(is_array($cptArr) && count($cptArr) > 0 )
								{
									foreach($cptArr as $key=>$cptCode)
									{
										$mods = array_key_exists($cptCode,$idoc_mod) ? $idoc_mod[$cptCode] : false;
										if(!$mods)
										{
											$mods = $idoc_mod[current(preg_grep('/^'.$cptCode.'/', array_keys($idoc_mod)))];
											if(!is_array($mods)) $mods = false;
										}
										
										echo '<tr>
														<td style="white-space:nowrap;" >'.$cptCode.'</td>
														<td style="white-space:nowrap;" >1</td>
														<td class="dischargeSummaryDx">&nbsp;</td>
														<td >'.$mods['mod1'].'</td>
														<td >'.$mods['mod2'].'</td>
														<td >'.$mods['mod3'].'</td>
													</tr>';
								
										
									}
								}
								else
								{
									echo '<tr><td colspan="6" >&nbsp;</td></tr>';
								}
							?>
                            
                     	</table>
                  	</td>
               	</tr>
                <!-- End Code for Super Bill Practice-->
                
                <!-- Start Code for Super Bill Facility-->
                <tr>
                	<td>
            			<table class="table table-bordered">
                			<tr>
                            	<td colspan="6" class="sectionHeader">Discharge Summary (Facility)</td>
                           	</tr>     
                        	
                            <tr>
                            	<td style="width:10%; white-space:nowrap;" class="rowHeader">CPT Codes</td>
                                <td style="width:7%; white-space:nowrap;" class="rowHeader">Unit</td>
                                <td style="width:50%;"class="rowHeader">Dx Codes</td>
                                <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod1</td>
                                <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod2</td>
                                <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod3</td>
                           	</tr>
                            <?php
								$cptArr	=	array_filter(explode(',',$selCptCodes));
								if(is_array($cptArr) && count($cptArr) > 0 )
								{
									foreach($cptArr as $key=>$cptCode)
									{
										$mods = array_key_exists($cptCode,$idoc_mod) ? $idoc_mod[$cptCode] : false;
										if(!$mods)
										{
											$mods = $idoc_mod[current(preg_grep('/^'.$cptCode.'/', array_keys($idoc_mod)))];
											if(!is_array($mods)) $mods = false;
										}
										
										echo '<tr>
												<td style="white-space:nowrap;" >'.$cptCode.'</td>
												<td style="white-space:nowrap;" >1</td>
												<td class="dischargeSummaryDx">&nbsp;</td>
												<td >'.$mods['mod1'].'</td>
												<td >'.$mods['mod2'].'</td>
												<td >'.$mods['mod3'].'</td>
											</tr>';
								
										
									}
								}
								else
								{
									echo '<tr><td colspan="6" >&nbsp;</td></tr>';
								}
							?>
                            
                     	</table>
                  	</td>
               	</tr>
                <!-- End Code for Super Bill Facility-->
                
                <!-- Start Code for Super Bill Anesthesia -->
                <tr>
                	<td>
            			<table class="table table-bordered">
                			<tr>
                      	<td colspan="6" class="sectionHeader">Discharge Summary (Anesthesia)</td>
                     	</tr>
                      <tr>
                     		<td style="width:10%; white-space:nowrap;" class="rowHeader">CPT Codes</td>
                        <td style="width:7%; white-space:nowrap;" class="rowHeader">Unit</td>
                        <td style="width:50%;"class="rowHeader">Dx Codes</td>
                        <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod1</td>
                        <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod2</td>
                        <td style="width:7%; white-space:nowrap;" class="rowHeader">Mod3</td>
                    	</tr>
                      <?php
								$anesCptArr	=	array_filter(explode(',',$selAnesCptCodes));
								if(is_array($anesCptArr) && count($anesCptArr) > 0 )
								{
									foreach($anesCptArr as $key=>$cptCode)
									{
										$mods = array_key_exists($cptCode,$idoc_mod) ? $idoc_mod[$cptCode] : false;
										if(!$mods)
										{
											$mods = $idoc_mod[current(preg_grep('/^'.$cptCode.'/', array_keys($idoc_mod)))];
											if(!is_array($mods)) $mods = false;
										}
										echo '<tr>
														<td style="white-space:nowrap;" >'.$cptCode.'</td>
														<td style="white-space:nowrap;" >1</td>
														<td class="dischargeSummaryDx">&nbsp;</td>
														<td >'.$mods['mod1'].'</td>
														<td >'.$mods['mod2'].'</td>
														<td >'.$mods['mod3'].'</td>
											  	</tr>';
									}
								}
								else
								{
									echo '<tr><td colspan="6" >&nbsp;</td></tr>';
									
								}
							?>
                            
                     	</table>
                  	</td>
               	</tr>
                <!-- End Code for Super Bill Anesthesia -->
                 
        	</table>
    	</td>
        <!-- END OF GETTING PROCEDURES -->
        
        <!-- GETTING DX CODES -->
        <td style="width:50%;">
        	<table class="left-part-pre ">
        		<tr>
                	<td>
            			<table class="table table-bordered">
                        	
                			
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
							//$icd10Query.close;
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
						if($dx_id_default_icd10 && (count($icd10_idDBExplodeArr)<=0) || ($form_status!='completed' && $form_status!='not completed')) 
						{
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
						<tr>
                        	<td colspan="3" class="sectionHeader">
                            	DX-Codes (<?php echo ($dx_code_type=='icd10')?'<b>ICD10</b>':'<b>ICD9</b>';?>)
                           	</td>
                       	</tr>
                        
                        <tr>
                            <td style="width:20%; white-space:nowrap;" class="rowHeader">Dx-Codes</td>
                            <td style="width:70%; word-break:loose;" class="rowHeader">Description</td>
                            <td style="width:10%; white-space:nowrap;" class="rowHeader">Yes</td>
                        </tr>  
                            
                       	<?php
                            $dischargeSummaryDx = '';
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
                                    
                                        $diagnosis_del_status = $diagnosisObj->deleted;
                                    
                                        if($diagnosis_del_status!='1' || in_array($diagnosisId,$icd10_idDBExplodeArr) )
                                        {
                                            ++$s;
                                            ++$c;
                                            
                                            $icd_10_win=false;
                                            $site_replaced=false;
                                            $icd_10_win=stristr($diagnosisCode,'-');
                                            $icd10_org=$diagnosisCode;
                                            //replace first '-' with site from icd 10 code
                                            $codeToReplace=$site='';
                                            if($icd_10_win && ($diagnosisObj->laterality==1 || $diagnosisObj->laterality==2) )
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
                                                    }
                                                }
                                            }
                                        
                                        
                                            //to show selected one
                                            $icd10Sel=$posToFindFrom=$position=$posVal=$laterSelId=$laterSelTxt=$laterSelCode=$severitySelTxt=$severitySelCode=$severitySelId=$stagingSelTxt=$stagingSelCode=$stagingSelId='';
                                            if(in_array($diagnosisId,$icd10_idDBExplodeArr) && $default_icd_10==false)
                                            {
                                                $icd10Sel=$icd10_codeDBExplodeArr[$diagnosisId];
                                            	
                                                $posToFindFrom=0;//start search from 0 index
                                                //get selected values for icd 10
                                                 if(trim($diagnosisObj->laterality))
                                                 {
                                                    //get posttition of first '-'
                                                    $position=$posVal='';
                                                    $posToFindFromTMP=($posToFindFrom)?$posToFindFrom+1:0;
                                                    $position=strpos($icd10_org ,'-',$posToFindFromTMP);
                                                    $laterSelId=substr($icd10_codeDBExplodeArr[$diagnosisId],$position,1);
                                                    $laterSelTxt=$icdLater[$diagnosisObj->laterality][$laterSelId];
                                                    list($laterSelTxt,$laterSelCode)=explode('~:~',$laterSelTxt);
                                                    $posToFindFrom=$position;
                                                 }
                                                 if(trim($diagnosisObj->severity))
                                                 {
                                                    //get posttition of first '-'
                                                    $position=$posVal='';
                                                    $posToFindFromTMP=($posToFindFrom)?$posToFindFrom+1:0;
                                                    $position=strpos($icd10_org ,'-',$posToFindFromTMP);
                                                    $severitySelId=substr($icd10_codeDBExplodeArr[$diagnosisId],$position,1);
                                                    $severitySelTxt=$icdLater[$diagnosisObj->severity][$severitySelId];
                                                    list($severitySelTxt,$severitySelCode)=explode('~:~',$severitySelTxt);
                                                    $posToFindFrom=$position; 
                                                }
                                             
                                                 if(trim($diagnosisObj->staging))
                                                 {
                                                    //get posttition of first '-'
                                                    $position=$posVal='';
                                                    $posToFindFromTMP=($posToFindFrom)?$posToFindFrom+1:0;
                                                    $position=strpos($icd10_org ,'-',$posToFindFromTMP);
                                                    $stagingSelId=substr($icd10_codeDBExplodeArr[$diagnosisId],$position,1);
                                                    $stagingSelTxt=$icdLater[$diagnosisObj->staging][$stagingSelId];
                                                    list($stagingSelTxt,$stagingSelCode)=explode('~:~',$stagingSelTxt);
                                                    $posToFindFrom=$position; 	 
                                                }
                                            }
                                            else
                                                $icd10Sel=$diagnosisCode;
                                        
											$isChecked = in_array($diagnosisId, $icd10_idDBExplodeArr) ? 'Yes' : '' ;
											if($isChecked && !strpos($icd10Sel,'-') )
											{
												$selDxCodes	.=	','.$diagnosisId.'~:~'.$icd10Sel;
												$dischargeSummaryDx .= ','.$icd10Sel;
												
											}
											else
											{
												$isChecked = ''; 	
											}
                                        ?>
                          
                                        <tr>
                                            <td><?php echo $icd10Sel; ?></td>
                                            <td><?php echo $diagnosisDesc; ?></td>
                                            <td><?php echo $isChecked; ?></td>
                                        </tr>
                           
                       
                                        <?php
                            
                                        }
                                    }
                                }
                                else//show icd 9 codes
                                {
                                    foreach($getDiagnosisDetails as $diagnosisObj)
                                    {
                                        $diagnosisId = $diagnosisObj->diag_id;
                                        $diagnosis = $diagnosisObj->diag_code;
                                        $diagnosis_del_status = $diagnosisObj->del_status;
                                        if($diagnosis_del_status!='yes' || in_array($diagnosisId,$diag_idDBExplodeArr)) 
                                        {
                                            //IF THERE IS NO COMMA INSERTED FOR DIAGNOSIS IN ADMIN THEN ADD COMMA IN ADMIN
                                            if(strpos($diagnosis, ',') !== false){
                                                //DO NOTHING
                                            }else {
                                                $diagnosis = ', '.$diagnosis;
                                            }
                                            //END IF THERE IS NO COMMA INSERTED FOR DIAGNOSIS IN ADMIN THEN ADD COMMA IN ADMIN
                                        
                                            list($diagnosisCode, $diagnosisDesc) = explode(", ", $diagnosis);
                                            $diagnosisCode=trim($diagnosisCode);
                                            
											$isChecked	=	in_array($diagnosisId, $diag_idDBExplodeArr) ? 'Yes' : '' ;
                                       		$selDxCodes	.=	($isChecked) ? ','.$diagnosisId.'~:~'.$diagnosisCode : '' ;
                                        ?>
                                            <tr>
                                                <td><?php echo $diagnosisCode; ?></td>
                                                <td><?php echo $diagnosisDesc; ?></td>
                                                <td><?php echo $isChecked;?></td>
                                            </tr>
                           
                        
                                        <?php
                                            
                                        }
                                    }	
                                }
                            }
							
							$selDxCodes	=	substr($selDxCodes,1); 
							$dischargeSummaryDx	=	substr($dischargeSummaryDx,1);
							$dischargeSummaryDx	=	str_replace(',','<br>',$dischargeSummaryDx);
							
                        ?>
                                    
                        </table>        
                    </td>
              	</tr>
         	</table>
      	</td>
        <!-- END OF GETTING DX CODES -->
</table>

<script>
	$(function(){
		var DX	=	'<?=$dischargeSummaryDx?>';
		$(".dischargeSummaryDx").html(DX);
	});

</script>
 


<form class="wufoo topLabel" enctype="multipart/form-data" name="frm_discharge_review" id="frm_discharge_review" method="post">	
	<input type="hidden" id="patient_idR" name="patient_idR" value="<?php echo $patient_id;?>">
    <input type="hidden" id="pConfIdR" name="pConfIdR" value="<?php echo $_REQUEST['pConfId'];?>">
    <input type="hidden" id="loggedInUserIdR" name="loggedInUserIdR" value="<?php echo $loginUser ;?>">
    <input type="hidden" id="dx_code_typeR" name="dx_code_typeR" value="<?php echo $dx_code_type;?>">
    <input type="hidden" id="dx_code_defaultR" name="dx_code_defaultR" value="<?php echo base64_encode($selDxCodes);?>">
    <input type="hidden" id="cpt_code_defaultR" name="cpt_code_defaultR" value="<?php echo base64_encode($selCptCodesID);?>">
</form>
				</div> <!-- End Modal Body -->
            
            	<div class="modal-footer">
                	<b style="float:left;"><i class="fa fa-hand-o-right"></i>&nbsp;Please confirm you have reviewed all the documents including discharge summary sheet!</b>
                    <a class="btn btn-primary" id="confirmBtnR" >Confirm</a>
                    <a class="btn btn-danger"  data-dismiss="modal">Close</a>
                    
                </div>
                
            </div><!-- End Modal Content -->
       	 
		</div> <!-- end Modal Dialogue -->

	</div><!-- End Modal -->
     		

<?PHP
}
?>
	<div class="modal fade" id="signAllConfirmModal" data-backdrop="false" style="max-width:500px; top:20%">
 		<div class="modal-dialog modal-lg" style="max-width:500px;">
        	<div class="modal-content">
            		
				<div class="modal-header text-center" >
            	
                    <div id="ModalTitle" style="position:absolute; text-align:center; width:100%; color:white"></div>
                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title rob" style="text-align:left; color:white;">
                    	<span class="" style="width:305px; color:white;">Confirmation</span>
                    </h4>  
    			</div>
                
                <div class="clearfix margin_adjustment_only"></div>
                
                <div class="modal-body" style="postion:relative; min-height:auto; ">
                	<p style="padding: 10px; font-weight:bold;" class="text-center"></p>
                </div>
                
                <div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
                	<a class="btn btn-primary" id="confirmBtn" data-dismiss="modal">Confirm</a>
                    <a class="btn btn-danger"  id="closeBtn" data-dismiss="modal">Close</a>
              	</div>
                
         	</div><!-- End Modal Content -->
        
	
    	</div> <!-- end Modal Dialogue -->

	</div><!-- End Modal -->
    