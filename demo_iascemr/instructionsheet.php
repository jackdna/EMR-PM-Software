<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$tablename = "patient_instruction_sheet";
$signatureDate = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html>
<head>
<title>Instruction Sheet</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
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
</style>
<script src="js/epost.js"></script> 
<?php
include_once("common/user_agent.php");
$spec = '</head>
<body onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();">';
include("common/link_new_file.php");
include_once("admin/fckeditor/fckeditor.php");
include_once("common/commonFunctions.php");
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$patient_id = $_REQUEST['patient_id'];
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }

$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}	
$cancelRecord = $_REQUEST['cancelRecord'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
if(!$pConfId) {
	$pConfId = $_REQUEST['pConfId'];
}
if(!$patient_id) {
	$patient_id = $_REQUEST['patient_id'];
}

//GET USER DETAIL(FOR USER SIGNATURE)
	$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
	$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
	$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
	
	$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
	$loggedInUserType = $ViewUserNameRow["user_type"];
	$loggedInSignatureOfUser = $ViewUserNameRow["signature"];
	$logInUserSubType = $ViewUserNameRow["user_sub_type"];
//END GET USER DETAIL(FOR USER SIGNATURE)

//START GET VOCABULARY OF ASC
$ascInfoArr = $objManageData->getASCInfo($_SESSION["facility"]);
//END GET VOCABULARY OF ASC

	if(!$cancelRecord){
		////// FORM SHIFT TO RIGHT SLIDER 
			$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$post_op_instruction_sheet_form = $getLeftLinkDetails->post_op_instruction_sheet_form;
			if($post_op_instruction_sheet_form=='true'){
				$formArrayRecord['post_op_instruction_sheet_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
			}
			//MAKE AUDIT STATUS VIEW
			if($_REQUEST['saveRecord']!='true'){
				unset($arrayRecord);
				$arrayRecord['user_id'] = $_SESSION['loginUserId'];
				$arrayRecord['patient_id'] = $patient_id;
				$arrayRecord['confirmation_id'] = $pConfId;
				$arrayRecord['form_name'] = 'post_op_instruction_sheet_form';
				$arrayRecord['status'] = 'viewed';
				$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
				$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
			}
			//MAKE AUDIT STATUS VIEW
		////// FORM SHIFT TO RIGHT SLIDER 
	}
	elseif($cancelRecord)
	{
		$fieldName="post_op_instruction_sheet_form";
		$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
		include("left_link_hide.php");
	}
	$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&fieldName='.$fieldName;

//GETTING PATIENT CONFIRMATION DETAILS
	$confirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if(count($confirmationDetails)>0){
		extract($confirmationDetails);
		$primary_procedure_is_inj_misc =	$prim_proc_is_misc;	
	}
	//START GET ARRIVAL TIME
	$stubWaitingDetailArr = $objManageData->getStubWaitingDetail($pConfId,$dos)	;
	$arrivalTime = ($stubWaitingDetailArr[0]) ? $stubWaitingDetailArr[0] : '';	
	//END GET ARRIVAL TIME
	
	//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
		$instructionSheetId = $_REQUEST['show_td'];
		if($surgeonId<>"" && !$instructionSheetId) {
			
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
					if($patient_primary_procedure_id == $surgeonProfileProcedureId && $_REQUEST['hiddInstrTmpltChangeId']!='yes') {
						$instructionSheetFound = "true";
						$instructionSheetId = $selectSurgeonProcedureRow['instructionSheetId'];
					}		
				}
			}	
			//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
			//FROM SURGEON'S DEFAULT PROFILE 
				/*if($instructionSheetFound <> "true") {
					$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' AND defaultProfile = '1'";
					$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
					while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
						$surgeonProfileIdArrInst[] = $selectSurgeonRow['surgeonProfileId'];
					}
					if(is_array($surgeonProfileIdArrInst)){
						$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArrInst);
					}else {
						$surgeonProfileIdImplode = 0;
					}
					$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) AND instructionSheetId!='' order by procedureName";
					$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
					$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
					if($selectSurgeonProcedureNumRow>0) {
						$selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes);
							$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
							$instructionSheetId = $selectSurgeonProcedureRow['instructionSheetId'];
							
					}
				}	*/
			//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
			//FROM SURGEON'S DEFAULT PROFILE  
		}
		
		// Start Instruction Sheet ID From Procedure Preference Card		
		if(!$instructionSheetId && $_REQUEST['hiddInstrTmpltChangeId']!='yes')
		{
			$proceduresArr	=	array($patient_primary_procedure_id,$patient_secondary_procedure_id,$patient_tertiary_procedure_id);
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
						$instructionSheetId	= $procPrefCardRow->instructionSheetId;
						
						break;
					}
				}
			}
			
		}
		// End  Instruction Sheet ID From Procedure Preference Card		
		
		
		// Check If Procedure is Injection Procedure
		$primProcDetails	=	$objManageData->getRowRecord('procedures','procedureId',$patient_primary_procedure_id,'','','catId');
		if( $primProcDetails->catId <> '2' )
		{
			if($primary_procedure_is_inj_misc == '')
			{
				//$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $primProcDetails->catId);
				$primary_procedure_is_inj_misc		=	$objManageData->verifyProcIsInjMisc($patient_primary_procedure_id);
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
			$procedureDetails	=	array($patient_primary_procedure_id,$patient_secondary_procedure_id,$patient_tertiary_procedure_id);
			if(is_array($procedureDetails) && count($procedureDetails) > 0 )  
			{
				$injMiscInstructionSheetId	=	'';
				foreach($procedureDetails as	$procedureID)
				{
					$fields	=	'instructionSheetID';
					$defaultProfile	= $objManageData->injectionProfile($procedureID,$surgeonId,$fields);
					
					if($defaultProfile['profileFound'])
					{
						$injMiscInstructionSheetId		=	$defaultProfile['data']['instructionSheetID'];
						break;
					}
				}
				$instructionSheetId= ($injMiscInstructionSheetId && $_REQUEST['hiddInstrTmpltChangeId']!='yes')?	$injMiscInstructionSheetId: $instructionSheetId;
			}
		}
		/******************************************
		 End Injection/Misc. Procedure Template
		******************************************/
		
		
		/******************************************
		 Start Laser Procedure Template
		******************************************/
		
			$primProcDetails	=	$objManageData->getRowRecord('procedures','procedureId',$patient_primary_procedure_id,'','','catId');
			if( $primProcDetails->catId == '2')
			{
				unset($condArr);
				$condArr['1']	=	'1' ;
				$xtraCondition	 =	" And catId = '2'  And procedureId IN (".$patient_primary_procedure_id."".
				$xtraCondition	.=	($patient_secondary_procedure_id)	?	','.$patient_secondary_procedure_id : '' ;
				$xtraCondition	.=	($patient_tertiary_procedure_id)		?	','.	$patient_tertiary_procedure_id : '' ;
				$xtraCondition	.=	 ") ";
				
				$orderBy	 =	" FIELD(procedureId, ".$patient_primary_procedure_id."";
				$orderBy	.=	($patient_secondary_procedure_id)	?	','.$patient_secondary_procedure_id : '' ;
				$orderBy	.=	($patient_tertiary_procedure_id)		?	','.	$patient_tertiary_procedure_id : '' ;
				$orderBy	.=	")";
				
				$procedureDetails	=	$objManageData->getMultiChkArrayRecords('procedures',$condArr,$orderBy,'',$xtraCondition);
				
				if(is_array($procedureDetails) && count($procedureDetails) > 0 )  
				{
					$laserInstructionSheetId = '';
					foreach($procedureDetails as $key=>$procData	)
					{
						$laserProcTempQry =	"SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$procData->procedureId."' And (FIND_IN_SET(".$surgeonId.",laser_surgeonID))   ";
						$laserProcTempSql	=	imw_query($laserProcTempQry) or die('Error found at line no '.(__LINE_).': '. imw_error());
						$laserProcTempCnt	=	imw_num_rows($laserProcTempSql);
						if( $laserProcTempCnt == 0 )
						{
							$laserProcTempQry =	"SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$procData->procedureId."' And laser_surgeonID = 'all'  Order by laser_templateID Desc Limit 1";
							$laserProcTempSql	=	imw_query($laserProcTempQry) or die('Error found at line no. '.(__LINE_).': '. imw_error());
							$laserProcTempCnt	=	imw_num_rows($laserProcTempSql);	
						}
						
						if( $laserProcTempCnt > 0 )
						{
							while($laserProcTempRow	=	imw_fetch_object($laserProcTempSql) )
							{
								$procSurgeonId 	=	$surgeonId;
								$laserSurgeon		=	$laserProcTempRow->laser_surgeonID;
								
								if($laserSurgeon != "all")
								{
									$laserSurgeonExplode	=	explode(",",$laserSurgeon);
									$laserSurgeonCount	=	count($laserSurgeonExplode);
									
									if($laserSurgeonCount	==	1 )
									{ 
										if($procSurgeonId	==	$laserSurgeon )
										{
											$laserInstructionSheetId		=	$laserProcTempRow->instructionSheetId;
											break;
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
												$laserInstructionSheetId		=	$laserProcTempRow->instructionSheetId;
											}
										}
									}
									if($matchedSurgeon==true) {
										break;
									}
								}
								else
								{ 
									$laserInstructionSheetId		=	$laserProcTempRow->instructionSheetId;
								}
							}
						}
						
						if($laserInstructionSheetId) break; 
					}
					
					$instructionSheetId	=	($laserInstructionSheetId && $_REQUEST['hiddInstrTmpltChangeId']!='yes')	?	$laserInstructionSheetId	:	$instructionSheetId;
				
				}
				
			}
			
		/******************************************
		 End Laser Procedure Template
		******************************************/
		
		
		
		
//CHECK FORM STATUS AND SIGN-ACTIVATE
	$chkFormStatusDetails = $objManageData->getRowRecord('patient_instruction_sheet', 'patient_confirmation_id', $pConfId);
	if($chkFormStatusDetails) {
		$chk_form_status = $chkFormStatusDetails->form_status;
		
		$chk_signSurgeon1Id = $chkFormStatusDetails->signSurgeon1Id;
		$chk_signNurseId = $chkFormStatusDetails->signNurseId;
		$chk_signWitness1Id = $chkFormStatusDetails->signWitness1Id;
		
	}
//CHECK FORM STATUS AND SIGN-ACTIVATE

//SAVE RECORD	
if($_REQUEST['saveRecord'] && $_REQUEST['hiddInstrTmpltChangeId']!='yes'){
	$sig_count = $_POST['sig_count'];
	$show_td = $_POST['show_td'];
	
	$text = $_REQUEST['getText'];
	$tablename = "patient_instruction_sheet";
	
	$patient_instruction_id = $_REQUEST['patient_instruction_id'];	
	//$temp_data = $_REQUEST['FCKeditor1'];
	//$temp_data = addslashes($temp_data);
	$templtIdQry='';
	if($show_td) { $templtIdQry=" AND template_id='".$show_td."'"; }
	//START
	$getinstructionSheetQry = "select * from `patient_instruction_sheet` where  patient_confirmation_id = '".$_REQUEST["pConfId"]."' AND (instruction_sheet_data!=' ' OR instruction_sheet_data!='') $templtIdQry";
	$getinstructionSheetRes = imw_query($getinstructionSheetQry) or die(imw_error()); 
	$getinstructionSheetNumRow = imw_num_rows($getinstructionSheetRes);
	if($getinstructionSheetNumRow>0) { 
		$getinstructionSheetRow = imw_fetch_array($getinstructionSheetRes);
		$instruction_sheet_data = stripslashes($getinstructionSheetRow['instruction_sheet_data']);
		$modifyFormStatus = $getinstructionSheetRow['form_status'];
	}else {	
	
		$getInstructionSheetAdminDetails = $objManageData->getRowRecord('instruction_template', 'instruction_id', $show_td);
		if($getInstructionSheetAdminDetails) {
			$instruction_sheet_data = stripslashes($getInstructionSheetAdminDetails->instruction_desc);
		}
	}
	//END
	
	//START MAKE VALUE IN {} AS CASE SENSITIVE	
	$instruction_sheet_data= str_ireplace("{TEXTBOX_XSMALL}","{TEXTBOX_XSMALL}",$instruction_sheet_data);
	$instruction_sheet_data= str_ireplace("{TEXTBOX_SMALL}","{TEXTBOX_SMALL}",$instruction_sheet_data);
	$instruction_sheet_data= str_ireplace("{TEXTBOX_MEDIUM}","{TEXTBOX_MEDIUM}",$instruction_sheet_data);
	$instruction_sheet_data= str_ireplace("{TEXTBOX_LARGE}","{TEXTBOX_LARGE}",$instruction_sheet_data);
	//END MAKE VALUE IN {} AS CASE SENSITIVE
	
	$arrStr = array("{TEXTBOX_XSMALL}","{TEXTBOX_SMALL}","{TEXTBOX_MEDIUM}","{TEXTBOX_LARGE}");
	for($j = 0;$j<count($arrStr);$j++)
	{
	
		if($arrStr[$j] == '{TEXTBOX_XSMALL}')
		{
			$name = 'xsmall';
			$size = 1;
		}
		else if($arrStr[$j] == '{TEXTBOX_SMALL}')
		{
			$name = 'small';
			$size = 30;
		}
		else if($arrStr[$j] == '{TEXTBOX_MEDIUM}')
		{
			$name = 'medium';
			$size = 60;
		}
		else if($arrStr[$j] == '{TEXTBOX_LARGE}')
		{
			$name = 'large';
			$size = 120;
			
		}
		$repVal = '';
		
		
		if(substr_count($instruction_sheet_data,$arrStr[$j]) >= 1)
		{
			
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$instruction_sheet_data);
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'" size="'.$size.'"  maxlength="'.$size.'">';
					$c++;
				}
				$repVal .= end($arrExp);
				$instruction_sheet_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$instruction_sheet_data);
				
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<textarea rows="2" cols="80" name="'.$name.$c.'"> '.$_POST[$name.$c].' </textarea>';
					$c++;
				}
				$repVal .= end($arrExp);
				$instruction_sheet_data = $repVal;
			}
		}
		/*
		else
		{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$repVal = str_ireplace($arrStr[$j],'<input type="text" name="'.$name.'" value="'.$_POST[$name].'" size="'.$size.'" >',$instruction_sheet_data);
				$instruction_sheet_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$repVal = str_ireplace($arrStr[$j],'<textarea rows="2" cols="80" name="'.$name.'"> '.$_POST[$name].' </textarea>',$instruction_sheet_data);
				$instruction_sheet_data = $repVal;
			}
			
		}
		*/
		 
	}
	//START MODIFY TEXTBOXES AFTER SAVED ATLEAST ONCE	
	if($modifyFormStatus=='completed' || $modifyFormStatus=='not completed') {
		$arrModifyStr = array('name="xsmall','name="small','name="medium','name="large');
		for($j = 0;$j<count($arrModifyStr);$j++)
			{
			
				if($arrModifyStr[$j] == 'name="xsmall')
				{
					$name = 'xsmall'; 
					$size = 1;
				}
				else if($arrModifyStr[$j] == 'name="small')
				{
					$name = 'small'; ;
					$size = 30;
				}
				else if($arrModifyStr[$j] == 'name="medium')
				{
					$name = 'medium';
					$size = 60;
				}
				else if($arrModifyStr[$j] == 'name="large')
				{
					$name = 'large';
					$size = 120;
					
				}
				$repModifyVal = '';
				if(substr_count($instruction_sheet_data,$arrModifyStr[$j]) >= 1)
				{
					$cntSubstr =  substr_count($instruction_sheet_data,$arrModifyStr[$j]);
					if($arrModifyStr[$j] == 'name="xsmall' || $arrModifyStr[$j] == 'name="small' || $arrModifyStr[$j] == 'name="medium')
					{
						$c = 1;
						for($p = 0;$p<$cntSubstr;$p++) {
							$txtBoxReplace = str_ireplace('<input type="text"  name="'.$name.$c.'" value="','<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'"',$instruction_sheet_data);
							$instruction_sheet_data = $txtBoxReplace;
							$txtBoxExplode = explode('<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'"',$instruction_sheet_data);
							$txtBoxFurtherExplode = explode(' size="'.$size.'"',$txtBoxExplode[1]);
							$getpos = strpos($txtBoxFurtherExplode[0],'"');
							$txtBoxFurtherExplodeSubStr = substr($txtBoxExplode[1],$getpos+1);
							//if($_POST[$name.$c]) {
								$instruction_sheet_data = $txtBoxExplode[0].'<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'"'.$txtBoxFurtherExplodeSubStr;
							//}
							$c++;
						}
					}
					else if($arrModifyStr[$j] == 'name="large')
					{
						$c = 1;
						for($p = 0;$p<$cntSubstr;$p++) {
							$instruction_sheet_data = str_ireplace("\n","",$instruction_sheet_data);
							$instruction_sheet_data = preg_replace('/<textarea rows="2" cols="80" name="'.$name.$c.'"> (.*?) <\/textarea>/','<textarea rows="2" cols="80" name="'.$name.$c.'"> '.$_POST[$name.$c].' </textarea>',$instruction_sheet_data);
							$c++;
						}
					}
					
					
				}
				/*
				else if(substr_count($instruction_sheet_data,$arrModifyStr[$j]) == 1)
				{
					if($arrModifyStr[$j] == 'name="xsmall' || $arrModifyStr[$j] == 'name="small' || $arrModifyStr[$j] == 'name="medium')
					{
						$txtBoxExplode = explode('<input type="text" name="'.$name.'" value="',$instruction_sheet_data);
						$txtBoxSizeExplode = explode('" size="'.$size.'" >',$instruction_sheet_data);
						
						$repModifyValTemp = '<input type="text" name="'.$name.'" value="'.$_POST[$name].'" size="'.$size.'" >';
						$repModifyVal = $txtBoxExplode[0].$repModifyValTemp.$txtBoxSizeExplode[1];
						$instruction_sheet_data = $repModifyVal;
					}
					else if($arrModifyStr[$j] == 'name="large')
					{
						$txtAreaExplode = explode('<textarea rows="2" cols="80" name="'.$name.'"> ',$instruction_sheet_data);
						$txtAreaSizeExplode = explode(' </textarea>',$instruction_sheet_data);
						
						$repModifyValTemp = '<textarea rows="2" cols="80" name="'.$name.'"> '.$_POST[$name].' </textarea>';
						$repModifyVal = $txtAreaExplode[0].$repModifyValTemp.$txtAreaSizeExplode[1];
						$instruction_sheet_data = $repModifyVal;
					}
				}
				*/
						
			}
		}	
	//MODIFY TEXTBOXES AFTER SAVED ATLEAST ONCE		
	
	$form_status = 'completed';//BY DEFAULT VALUE
	//SAVE SIGNATURE
		$signDt = date('d_m_y_h_i_s');
		for($ps=1;$ps<=$sig_count;$ps++){
			$postData = $_POST['sigData'.$ps];
			if($postData!='' && $postData!= 'undefined'  && $postData != '000000000000000000000000000000000000000000000000000000000000000000000000') {
				$path =  realpath(dirname(__FILE__).'/SigPlus_images').'/sign_instruction'.$_REQUEST["pConfId"].'_'.$signDt.'_'.$ps.'.jpg';
				if(class_exists("COM") && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
					$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
					$aConn->InitSigPlus();
					$aConn->SigCompressionMode = 2;
					$aConn->SigString=$postData;
					$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
					$aConn->ImageXSize = 500; //width of resuting image in pixels
					$aConn->ImageYSize =165; //height of resulting image in pixels
					$aConn->ImagePenWidth = 11; //thickness of ink in pixels
					$aConn->JustifyMode = 5;  //center and fit signature to size
					$patientSignArr[$ps] = $path;
					$aConn->WriteImageFile("$path");
				}else {
					$objManageData->getSigImage($postData,$path,$rootServerPath);//TO STORE DATA IN SIGPLUS
					$patientSignArr[$ps] = $path;
				}
			}
		}	
	
		//CODE TO SET FORM STATUS 
		//-- get signature applets ----
		$instruction_sheet_data= str_ireplace( '{DATE}','<b>'.date('m-d-Y').'</b>', $instruction_sheet_data);
		$instruction_sheet_data= str_ireplace("{SIGNATURE}","{SIGNATURE}", $instruction_sheet_data);
		$row_arr = explode('{SIGNATURE}',$instruction_sheet_data);
		$instruction_sheet_data = $row_arr[0];
		$sigDtTmSave = '<br><div style="font-weight:normal;"><b>Signature Date:</b>&nbsp;'.$signatureDate.'</div>';
		for($c=1;$c<count($row_arr);$c++){
			$imgNameArr = explode('/',$patientSignArr[$c]);
			$imgSrc = end($imgNameArr);
			$hiddSigIpadIdImg = trim($_REQUEST['hiddSigIpadId'.$c]);
			if($hiddSigIpadIdImg) {//FOR TOUCH SIGNATURE
				$instruction_sheet_data .= '<img src="'.$hiddSigIpadIdImg.'" width="150" height="83">'.$sigDtTmSave;	
			}else if(!$imgSrc) {
				$instruction_sheet_data .= '{SIGNATURE}';
				$form_status = "not completed";	//IF PATIENT DOES NOT SIGNED THE CHART
			}else if($imgSrc=='{SIGNATURE}') {
				$instruction_sheet_data .= '{SIGNATURE}';
				$form_status = "not completed";	//IF PATIENT DOES NOT SIGNED THE CHART
			}else {
				$instruction_sheet_data .= '<img src="SigPlus_images/'.$imgSrc.'" width="150" height="63">'.$sigDtTmSave;
			}
			$instruction_sheet_data .= $row_arr[$c];
		}
		
		if(!$instruction_sheet_data) {
			$form_status = "not completed";
		}
		if($_POST["hidd_signSurgeon1Activate"]=='yes') {
			if($chk_signSurgeon1Id=='0' || $chk_signSurgeon1Id=='') {
				$form_status = "not completed";
			}	
		}
		if($_POST["hidd_signNurseActivate"]=='yes') {
			if($chk_signNurseId=='0' || $chk_signNurseId=='') {
				$form_status = "not completed";
			}		
		}
		if($_POST["hidd_signWitness1Activate"]=='yes') {
			if($chk_signWitness1Id=='0' || $chk_signWitness1Id=='') {
				$form_status = "not completed";
			}	
		}
		
	//END CODE TO SET FORM STATUS 
	
	$arrayRecord['patient_confirmation_id'] = $pConfId;
	$arrayRecord['instruction_sheet_data'] 	= addslashes($instruction_sheet_data);
	$arrayRecord['form_status'] 			= $form_status;
	
	$arrayRecord['signSurgeon1Activate']	= $_POST["hidd_signSurgeon1Activate"];
	$arrayRecord['signNurseActivate']		= $_POST["hidd_signNurseActivate"];
	$arrayRecord['signWitness1Activate']	= $_POST["hidd_signWitness1Activate"];
	$arrayRecord['template_id']				= $_POST["show_td"];
	
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	unset($arrayStatusRecord);
	$arrayStatusRecord['user_id'] = $_SESSION['loginUserId'];
	$arrayStatusRecord['patient_id'] = $patient_id;
	$arrayStatusRecord['confirmation_id'] = $pConfId;
	$arrayStatusRecord['form_name'] = 'post_op_instruction_sheet_form';	
	$arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	
	if($_REQUEST["hiddPurgeResetStatusId"]=="Yes") {
		
		$form_status							= "";
		$arrayRecord['instruction_sheet_data'] 	= "";
		$arrayRecord['form_status'] 			= "";
		
		$arrayRecord['signSurgeon1Activate']	= "";
		$arrayRecord['signNurseActivate']		= "";
		$arrayRecord['signWitness1Activate']	= "";
		$arrayRecord['template_id']				= "0";

		$arrayRecord['signSurgeon1Id']			= "0"; 
		$arrayRecord['signSurgeon1FirstName']	= ""; 
		$arrayRecord['signSurgeon1MiddleName']	= ""; 
		$arrayRecord['signSurgeon1LastName']	= ""; 
		$arrayRecord['signSurgeon1Status']		= ""; 
		$arrayRecord['signSurgeon1DateTime']	= "0000-00-00 00:00:00"; 
		$arrayRecord['signNurseId']				= "0"; 
		$arrayRecord['signNurseFirstName']		= ""; 
		$arrayRecord['signNurseMiddleName']		= ""; 
		$arrayRecord['signNurseLastName']		= ""; 
		$arrayRecord['signNurseStatus']			= ""; 
		$arrayRecord['signNurseDateTime']		= "0000-00-00 00:00:00"; 
		$arrayRecord['signWitness1Id']			= "0"; 
		$arrayRecord['signWitness1FirstName']	= ""; 
		$arrayRecord['signWitness1MiddleName']	= ""; 
		$arrayRecord['signWitness1LastName']	= ""; 
		$arrayRecord['signWitness1Status']		= ""; 
		$arrayRecord['signWitness1DateTime']	= "0000-00-00 00:00:00"; 
		
		
	}
	
	if(!$patient_instruction_id){
		$patient_instruction_id = $objManageData->addRecords($arrayRecord, 'patient_instruction_sheet');
	}else {
		
		$objManageData->updateRecords($arrayRecord, 'patient_instruction_sheet', 'patient_confirmation_id', $pConfId);
		
	}
	//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'post_op_instruction_sheet_form';
		$conditionArr['status'] = 'created';
		$chkAuditStatus = $objManageData->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);	
		if($chkAuditStatus) {
			//MAKE AUDIT STATUS MODIFIED
			$arrayStatusRecord['status'] = 'modified';
			
		}else {
			//MAKE AUDIT STATUS CREATED
			$arrayStatusRecord['status'] = 'created';
			
		}
		$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');												
	//CODE END TO SET AUDIT STATUS AFTER SAVE
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";
		/*
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}*/
		
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)

}	
	
	// GETTING INSTRUCTION SHEET DETAILS	
		//GETTING IF ALREADY EXISIS
			$instDetails = $objManageData->getRowRecord('patient_instruction_sheet', 'patient_confirmation_id', $pConfId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat");
			$template_id = '';
			if($instDetails){
				$patient_instruction_id = $instDetails->patient_instruction_id;
				
				if($_REQUEST['hiddInstrTmpltChangeId']!='yes') {
					if($instructionData=='') {
						$instructionData = stripslashes($instDetails->instruction_sheet_data);
					}	
					$signSurgeon1Activate 		= $instDetails->signSurgeon1Activate;
					$signSurgeon1Id 			= $instDetails->signSurgeon1Id;
					$signSurgeon1DateTime		= $instDetails->signSurgeon1DateTime;
					$signSurgeon1DateTimeFormat	= $instDetails->signSurgeon1DateTimeFormat;
					$signSurgeon1FirstName 		= $instDetails->signSurgeon1FirstName;
					$signSurgeon1MiddleName 	= $instDetails->signSurgeon1MiddleName;
					$signSurgeon1LastName 		= $instDetails->signSurgeon1LastName;
					$signSurgeon1Status 		= $instDetails->signSurgeon1Status;
					
					$signNurseActivate 			= $instDetails->signNurseActivate;
					$signNurseDateTime			= $instDetails->signNurseDateTime;
					$signNurseDateTimeFormat	= $instDetails->signNurseDateTimeFormat;
					$signNurseId 				= $instDetails->signNurseId;
					$signNurseFirstName 		= $instDetails->signNurseFirstName;
					$signNurseMiddleName 		= $instDetails->signNurseMiddleName;
					$signNurseLastName 			= $instDetails->signNurseLastName;
					$signNurseStatus 			= $instDetails->signNurseStatus;
					
					$signWitness1Activate 		= $instDetails->signWitness1Activate;
					$signWitness1DateTime		= $instDetails->signWitness1DateTime;
					$signWitness1DateTimeFormat	= $instDetails->signWitness1DateTimeFormat;
					$signWitness1Id 			= $instDetails->signWitness1Id;
					$signWitness1FirstName 		= $instDetails->signWitness1FirstName;
					$signWitness1MiddleName 	= $instDetails->signWitness1MiddleName;
					$signWitness1LastName 		= $instDetails->signWitness1LastName;
					$signWitness1Status 		= $instDetails->signWitness1Status;
					$template_id 				= $instDetails->template_id;
				}	
				
			}
			if(!$instructionData){ $instructionSheet_BackColor=$chngBckGroundColor;	}		
			if($template_id) { //IF THIS RECORD EXIST IN DATABASE THEN
      	$instructionSheetId = $template_id;
     	}
		//GETTING IF ALREADY EXISIS	
		
		//GETTING IF instructionData DOES NOT ALREADY EXISIS	
			if(!$instructionData) { 
				//echo $instructionSheetId;
				$instructionDetails = $objManageData->getRowRecord('instruction_template', 'instruction_id', $instructionSheetId);
				$instructionData = stripslashes($instructionDetails->instruction_desc);
			}	
			if(!$instructionData) { 
				$instructionDataStatus='false';
			}	
		//GETTING IF instructionData DOES NOT ALREADY EXISIS
		
	// GETTING INSTRUCTION SHEET DETAILS
//GETTING PATIENT CONFIRMATION DETAILS

//GETTING PATIENT DETAILS
	if(!$patient_id) {
		$patient_id = $_REQUEST['patient_id'];
	}
	
	$patientDetails = $objManageData->getExtractRecord('patient_data_tbl', 'patient_id', $patient_id);
	if(count($patientDetails)>0){
		extract($patientDetails);
	}	
	
	$instruction_patientNameDobTemp = $date_of_birth;
	$instruction_patientNameDob_split = explode("-",$instruction_patientNameDobTemp);
	$instruction_patientNameDob = $instruction_patientNameDob_split[1]."-".$instruction_patientNameDob_split[2]."-".$instruction_patientNameDob_split[0];
	
	//SET DOS FROM patientConfirmation TABLE
		$instruction_patientConfirmDosTemp = $dos;
		$instruction_patientConfirmDos_split = explode("-",$instruction_patientConfirmDosTemp);
		$instruction_patientConfirmDos = $instruction_patientConfirmDos_split[1]."-".$instruction_patientConfirmDos_split[2]."-".$instruction_patientConfirmDos_split[0];
	//END SET DOS FROM patientConfirmation TABLE
	
	//FETCH DATA FROM OPEARINGROOMRECORD TABLE
		$diagnosisQry=imw_query("select preOpDiagnosis , postOpDiagnosis from operatingroomrecords where patient_id='".$patient_id."' and confirmation_id='".$pConfId."'");
		$diagnosisRes=imw_fetch_array($diagnosisQry);	
		$preopdiagnosis= $diagnosisRes["preOpDiagnosis"];
		$postopdiagnosis= $diagnosisRes["postOpDiagnosis"];
		if(trim($postopdiagnosis)=="") {
			$postopdiagnosis = $preopdiagnosis;
		}
	// END FETCH DATA FROM OPEARINGROOMRECORD TABLE
	$instructionData= str_ireplace("&#39;","'",$instructionData);
	$instructionData = str_ireplace( '{PATIENT ID}', '<b>'.ucfirst($patient_id).'</b>', $instructionData);
	$instructionData = str_ireplace( '{PATIENT FIRST NAME}', '<b>'.ucfirst($patient_fname).'</b>', $instructionData);
	$instructionData = str_ireplace( '{MIDDLE INITIAL}', '<b>'.ucfirst(substr($patient_mname, 0, 1)).'</b>', $instructionData);
	$instructionData = str_ireplace( '{LAST NAME}', '<b>'.ucfirst($patient_lname).'</b>', $instructionData);
	$instructionData = str_ireplace( '{DOB}', '<b>'.$instruction_patientNameDob.'</b>', $instructionData);
	$instructionData = str_ireplace( '{DOS}', '<b>'.$instruction_patientConfirmDos.'</b>', $instructionData);
	$instructionData = str_ireplace( '{SURGEON NAME}', '<b>'.ucfirst($surgeon_name).'</b>', $instructionData);
	$instructionData = str_ireplace( '{ARRIVAL TIME}', '<b>'.$arrivalTime.'</b>', $instructionData);	

	//GETTING SITE, PRIMARY, SECONDARY PROCEDURE DETAILS AND CURRENT DATE
	if($site=='1'){	$site = 'left';	}
	if($site=='2'){	$site = 'right';}
	if($site=='3'){	$site = 'both';	}
	if($site=='4'){	$site = 'left upper lid';	}
	if($site=='5'){	$site = 'left lower lid';	}
	if($site=='6'){	$site = 'right upper lid';	}
	if($site=='7'){	$site = 'right lower lid';	}
	if($site=='8'){	$site = 'bilateral upper lid';	}
	if($site=='9'){	$site = 'bilateral lower lid';	}
	$instructionData = str_ireplace( '{SITE}', '<b>'.ucwords($site.' Site').'</b>', $instructionData);
	$instructionData = str_ireplace( '{PROCEDURE}', '<b>'.ucfirst($patient_primary_procedure).'</b>', $instructionData);
	$instructionData = str_ireplace( '{SECONDARY PROCEDURE}', '<b>'.ucfirst($patient_secondary_procedure).'</b>', $instructionData);
	$instructionData = str_ireplace( '{TERTIARY PROCEDURE}', '<b>'.ucfirst($patient_tertiary_procedure).'</b>', $instructionData);
	$instructionData = str_ireplace( '{PRE-OP DIAGNOSIS}','<b>'.$preopdiagnosis.'</b>',$instructionData);
	$instructionData = str_ireplace( '{POST-OP DIAGNOSIS}','<b>'.$postopdiagnosis.'</b>',$instructionData);
	$instructionData = str_ireplace( '{DATE}','<b>'.date('m-d-Y').'</b>', $instructionData);
	
	//GETTING SITE DETAILS

	$instructionData = str_ireplace('{TEXTBOX_XSMALL}',"{TEXTBOX_XSMALL}",$instructionData);
	$instructionData = str_ireplace('{TEXTBOX_SMALL}',"{TEXTBOX_SMALL}",$instructionData);
	$instructionData = str_ireplace('{TEXTBOX_MEDIUM}',"{TEXTBOX_MEDIUM}",$instructionData);
	$instructionData = str_ireplace('{TEXTBOX_LARGE}',"{TEXTBOX_LARGE}",$instructionData);
	
	preg_match_all("/{TEXTBOX_XSMALL}/", $instructionData, $TEXTBOX_XSMALL_matches);
	for($xsi = 1; $xsi <= count($TEXTBOX_XSMALL_matches[0]); $xsi++){
		$instructionData = preg_replace('/{TEXTBOX_XSMALL}/',"<input type='text' name='xsmall".$xsi."' size='1' maxlength='1'>",$instructionData, 1);
	}
	preg_match_all("/{TEXTBOX_SMALL}/", $instructionData, $TEXTBOX_SMALL_matches);
	for($xsi = 1; $xsi <= count($TEXTBOX_SMALL_matches[0]); $xsi++){
		$instructionData = preg_replace('/{TEXTBOX_SMALL}/',"<input type='text' name='small".$xsi."' size='30' maxlength='30'>",$instructionData, 1);
	}
	preg_match_all("/{TEXTBOX_MEDIUM}/", $instructionData, $TEXTBOX_MEDIUM_matches);
	for($xsi = 1; $xsi <= count($TEXTBOX_MEDIUM_matches[0]); $xsi++){
		$instructionData = preg_replace('/{TEXTBOX_MEDIUM}/',"<input type='text' name='medium".$xsi."' size='60' maxlength='60'>",$instructionData, 1);
	}
	preg_match_all("/{TEXTBOX_LARGE}/", $instructionData, $TEXTBOX_LARGE_matches);
	for($xsi = 1; $xsi <= count($TEXTBOX_LARGE_matches[0]); $xsi++){
		$instructionData = preg_replace('/{TEXTBOX_LARGE}/',"<textarea name='large".$xsi."' cols='80' rows='2'></textarea>",$instructionData, 1);
	}
	
	/*
	$instructionData = str_ireplace('{TEXTBOX_XSMALL}',"<input type='text' name='xsmall' size='1' maxlength='1'>",$instructionData);
	$instructionData = str_ireplace('{TEXTBOX_SMALL}',"<input type='text' name='small' size='30' maxlength='30'>",$instructionData);
	$instructionData = str_ireplace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$instructionData);
	$instructionData = str_ireplace('{TEXTBOX_LARGE}',"<textarea name='large' cols='80' rows='2'></textarea>",$instructionData);
	*/

	//CODE TO ACTIVATE,DEACTIVATE SURGEON'S SIGNATURE (AND REPLACE VARIABLES)
		//START MAKE VALUE IN {} AS CASE SENSITIVE
			$instructionData= str_ireplace("{Surgeon's Signature}","{Surgeon's Signature}",$instructionData);
			$instructionData= str_ireplace("{Surgeon's&nbsp;Signature}","{Surgeon's&nbsp;Signature}",$instructionData);
		//END MAKE VALUE IN {} AS CASE SENSITIVE
	$chkSignSurgeon1Var = stristr($instructionData,"{Surgeon's Signature}");
	$chkSignSurgeon1VarNew = stristr($instructionData,"{Surgeon's&nbsp;Signature}");
	
	$chkSignSurgeon1Activate='';
	if($chkSignSurgeon1Var || $chkSignSurgeon1VarNew) {
		$chkSignSurgeon1Activate = 'yes';
	}
	$instructionData= str_ireplace("{Surgeon's Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Surgeon's&nbsp;Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Surgeon&#39;s Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Surgeon&#39;s&nbsp;Signature}"," ",$instructionData);
	
	//END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
	
	//CODE TO ACTIVATE,DEACTIVATE NURSE'S SIGNATURE (AND REPLACE VARIABLES)
		//START MAKE VALUE IN {} AS CASE SENSITIVE
			$instructionData= str_ireplace("{Nurse's Signature}","{Nurse's Signature}",$instructionData);
			$instructionData= str_ireplace("{Nurse's&nbsp;Signature}","{Nurse's&nbsp;Signature}",$instructionData);
		//END MAKE VALUE IN {} AS CASE SENSITIVE	
	$chkSignNurseVar = stristr($instructionData,"{Nurse's Signature}");
	$chkSignNurseVarNew = stristr($instructionData,"{Nurse's&nbsp;Signature}");
	$chkSignNurseActivate='';
	if($chkSignNurseVar || $chkSignNurseVarNew) {
		$chkSignNurseActivate = 'yes';
	}
	$instructionData= str_ireplace("{Nurse's Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Nurse's&nbsp;Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Nurse&#39;s Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Nurse&#39;s&nbsp;Signature}"," ",$instructionData);
	
	//END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
	
	
	//CODE TO ACTIVATE,DEACTIVATE Witness SIGNATURE (AND REPLACE VARIABLES)
		//START MAKE VALUE IN {} AS CASE SENSITIVE
			$instructionData= str_ireplace("{Witness Signature}","{Witness Signature}",$instructionData);
			$instructionData= str_ireplace("{Witness&nbsp;Signature}","{Witness&nbsp;Signature}",$instructionData);
		//END MAKE VALUE IN {} AS CASE SENSITIVE
	$chkSignWitness1Var = stristr($instructionData,"{Witness Signature}");
	$chkSignWitness1VarNew = stristr($instructionData,"{Witness&nbsp;Signature}");
	
	$chkSignWitness1Activate='';
	if($chkSignWitness1Var || $chkSignWitness1VarNew) {
		$chkSignWitness1Activate = 'yes';
	}
	$instructionData= str_ireplace("{Witness Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness&nbsp;Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness&#39;s Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness&#39;s&nbsp;Signature}"," ",$instructionData);
	
	//END CODE TO ACTIVATE,DEACTIVATE Witness SIGNATURE (AND REPLACE VARIABLES)

	$instructionData= str_ireplace("{SIGNATURE}","{SIGNATURE}",$instructionData);

	$instructionData= str_ireplace("{ASC NAME}",$_SESSION['loginUserFacilityName'],$instructionData);
	$instructionData= str_ireplace("{ASC ADDRESS}",$ascInfoArr[0],$instructionData);
	$instructionData= str_ireplace("{ASC PHONE}",$ascInfoArr[1],$instructionData);

//GETTING PATIENT DETAILS
	//START SIGNATURE CODE
	$row_arr = explode('{START APPLET ROW}',$instructionData);
	$sig_arr = explode('{SIGNATURE}',$row_arr[0]);
	//echo 'hlo'.count($sig_arr);
	$ds=1;
	$sig_data = '';
	for($s=1;$s<count($sig_arr);$s++,$ds++){
		$sig_data = '<table class="alignLeft" style="border:none;" id="instructionSigIpadId'.$ds.'"><tr>';
		if($browserPlatform == "iPad") {
			$sig_data .= '<td style="width:320px;height:90px;" class="consentObjectBeforSign" >   
								<img style="cursor:pointer; float:right; margin-top:50px;" src="images/pen.png" id="SigPen'.$ds.'" onclick="OnSignIpadPhy(\''.$patient_id.'\',\''.$pConfId.'\',\'ptInstruction\',\'instructionSigIpadId'.$ds.'\',\''.$ds.'\')">
						  </td>';
		}else {
			$sig_data .= '<td style="width:145px;" class="consentObjectBeforSign" id="tdObject'.$ds.'">
								<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
										id=SigPlus'.$ds.' name=SigPlus'.$ds.'
										style="HEIGHT: 90px; WIDTH: 320px; LEFT: 0px; TOP: 0px;" 
										VIEWASTEXT>
										<PARAM NAME="_Version" VALUE="131095">
										<PARAM NAME="_ExtentX" VALUE="4842">
										<PARAM NAME="_ExtentY" VALUE="1323">
										<PARAM NAME="_StockProps" VALUE="0">
								</OBJECT>
						  </td>
						  <td class="valignBottom" id="Sign_icon_'.$ds.'">
								<img style="cursor:pointer;" src="images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" onclick="OnSign'.$ds.'();"><br>
								<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="images/touch.svg" id="SignBtnTouch'.$ds.'" name="SignBtnTouch'.$ds.'" onclick="clearAllTabletState();OnSignIpadPhy(\''.$patient_id.'\',\''.$pConfId.'\',\'ptInstruction\',\'instructionSigIpadId'.$ds.'\',\''.$ds.'\')"><br> 
								<img style="cursor:pointer;" src="images/eraser.gif" id="button'.$ds.'" alt="Clear Sign" name="ClearBtn'.$ds.'" onclick="OnClear'.$ds.'();">
						  </td>';
			
		}
		$sig_data .='</tr></table>';
		$str_data = $sig_arr[$s];
		$sig_arr[$s] = $sig_data;
		$sig_arr[$s] .= $str_data;
		$hiddenFields[] = true;
		
	}
	
	$instructionData = implode(' ',$sig_arr);
	$content_row = '';
	for($ro=1;$ro<count($row_arr);$ro++){
		if($row_arr[$ro]){
			$sig_arr1 = explode('{SIGNATURE}',$row_arr[$ro]);
			$td_sign = '';
			for($t=0;$t<count($sig_arr1)-1;$t++,$ds++){
				$sig_arr1[$t] = str_ireplace('&nbsp;','',$sig_arr1[$t]);
				$td_sign .= '
					<td class="alignLeft">
						<table class="table_pad_bdr">
							<tr><td>'.$sig_arr1[$t].'</td></tr>
							<tr>
								<td class="consentObjectBeforSign" id="tdObject'.$ds.'">
									<OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
											id=SigPlus'.$ds.' name=SigPlus'.$ds.'
											style="HEIGHT: 90px; WIDTH: 320px; LEFT: 0px; TOP: 0px;" 
											VIEWASTEXT>
											<PARAM NAME="_Version" VALUE="131095">
											<PARAM NAME="_ExtentX" VALUE="4842">
											<PARAM NAME="_ExtentY" VALUE="1323">
											<PARAM NAME="_StockProps" VALUE="0">
									</OBJECT>
								</td>
								<td class="valignBottom" id="Sign_icon_'.$ds.'">
									<img style="cursor:pointer;" src="images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" onclick="OnSign'.$ds.'();"><br>
									<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="images/touch.svg" id="SignBtnTouch'.$ds.'" name="SignBtnTouch'.$ds.'" onclick="clearAllTabletState();OnSignIpadPhy(\''.$patient_id.'\',\''.$pConfId.'\',\'ptInstruction\',\'instructionSigIpadId'.$ds.'\',\''.$ds.'\')"><br> 
									<img style="cursor:pointer;" src="images/eraser.gif" id="button'.$ds.'" name="ClearBtn'.$ds.'" alt="Clear Sign" onclick="OnClear'.$ds.'();">											
								</td>
							</tr>
						</table>
					</td>	
				';
				$s++;
				$hiddenFields[] = true;
			}
			$content_row .= '
				<table style="width:145px; border:1px solid;">
					<tr>
						'.$td_sign.'						
					</tr>
				</table>
			';
		}
	}	
	$jh = 1;
	
	$instructionData .= $content_row;
	//--- get all content of instruction sheet -------	
	/*
	$consent_content = '
		<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="left" cellpadding="1" cellspacing="1" border="0">
			<tr>
				<td colspan="'.count($sig_arr).'">'.$instructionData.'</td>
			<tr>
		</table>
	';*/

	//END SIGNATURE CODE
		

//GETTING SURGEONS DETAILS
	$surgeonDetails = $objManageData->getExtractRecord('users', 'usersId', $surgeonId);
	if(is_array($surgeonDetails)){
		extract($surgeonDetails);
	}
//GETTING SURGEONS DETAILS


if($innerKey=='') {
	$innerKey = 17;
}
?>
<script>
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
	
	function OnClear() {
	   document.SigPlus1.ClearTablet();
	   document.frm_consent_multiple.consentSurgery_patient_sign.value='';
	}
	function OnClear2() {
	   //SigPlus2.ClearTablet();
	}
	
	function OnCancel() {
	   SigPlus1.TabletState = 0;
	}
	
	function OnSign() {
		//SigPlus2.TabletState = 0;
		document.SigPlus1.TabletState = 1;
	}
	function OnSign2() {
		document.SigPlus1.TabletState = 0;
		//SigPlus2.TabletState = 1;
	}
	
//Display Signature Of USER
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {

		
		
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
				
			//SET HIDDEN FIELD (hidd_chkDisplayAnesthesiaSign) TO TRUE AT MAINPAGE
			if(userIdentity=='Anesthesia1'){
				if(top.document.forms[0]){
					if(top.document.forms[0].hidd_chkDisplayAnesthesiaSign) {
						top.document.forms[0].hidd_chkDisplayAnesthesiaSign.value = 'true';
					}
				}
			}		
			//END SET HIDDEN FIELD (hidd_chkDisplayAnesthesiaSign) TO TRUE AT MAINPAGE
		
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
//End Display Signature Of USER	

//SIGNATURE CODE
	var chkAtleastOneSign = false; //NOT SIGNED ATLEAST ONE
	function chkAtleastOne() {
		var cnt_hid_field = '<?php echo count($hiddenFields)?>';
		var mn;
		chkAtleastOneSign =true; //ACC. TO NEW REQUIREMENT, NO SIGNATUER CHECK REQUIRED AND ALLOW FORM TO SAVE
		return chkAtleastOneSign;
	}	
	
	function clearAllTabletState(c){
		c = (typeof c === 'undefined') ? '' : c;
		c = parseInt(c);

		var v = document.getElementById('sig_count').value;
		v = parseInt(v);
		for( var i = 1; i <= v; i++) {
			if( c && c == i) continue;
			if( document.getElementById("SigPlus"+i) ) {
				if(document.getElementById("SigPlus"+i).TabletState == 1)
					document.getElementById("SigPlus"+i).TabletState = 0;

				if( document.getElementById('tdObject'+i) )	
					document.getElementById('tdObject'+i).className = 'consentObjectBeforSign';
			}
		}
	}

	<?php
	$jh = 1;
	for($h=0;$h<count($hiddenFields);$h++,$jh++){
		$str = '';
		for($s=0;$s<count($hiddenFields);$s++){
			if($s+1 != $jh){
				$str .= 'if(document.getElementById("SigPlus'.($s+1).'")){
							document.getElementById("SigPlus'.($s+1).'").TabletState = 0;
						 }';
			}
		}
		print '
			function OnSign'.$jh.'(){
				if(document.getElementById("SigPlus'.$jh.'")){
					'.$str.'
					clearAllTabletState('.$jh.');
					document.getElementById("SigPlus'.$jh.'").TabletState = 1;
					document.getElementById("tdObject'.$jh.'").className="consentObjectAfterSign";
				}
				
			}
		';
		print '
			function OnClear'.$jh.'(){
			   if(document.getElementById("SigPlus'.$jh.'")){
				   document.getElementById("SigPlus'.$jh.'").ClearTablet();
			   }

			}
		';
		$saveStr .= '
			if(document.getElementById("SigPlus'.$jh.'")){
				if(document.getElementById("SigPlus'.$jh.'").NumberOfTabletPoints==0){
					/* alert("Please sign '.$jh.' to continue"); 
					return false; */
				}
				else{
					document.getElementById("SigPlus'.$jh.'").SigCompressionMode=1;
					document.getElementById("sigData'.$jh.'").value=document.getElementById("SigPlus'.$jh.'").SigString;
					
				}
			}
		';	
	}
	for($dis=0;$dis<count($sig_con);$dis++){
		if($patientSign == false){
			print '
				if(document.getElementById("Sign_icon_'.($dis+1).'")) {
					document.getElementById("Sign_icon_'.($dis+1).'").style.display="none";
				}';
		}
	}
	?>
	function SetSig(){
		if(chkAtleastOne() || document.getElementById("hiddCntSigImageId").value>0) {
			<?php print $saveStr; ?>
			return true;
		}else {
			alert('Please make atleast one signature to continue');
			return false;
		}	
	}
	function LoadSig() {
		<?php print $displaySign; ?>
	}
//SIGNATURE CODE

	function chngInstTmplt(objVal) {
		if(objVal) {
			document.instructionSheetForm.show_td.value=objVal;
			document.instructionSheetForm.hiddInstrTmpltChangeId.value='yes';
			if(parent.parent) {
				parent.parent.show_loading_image('block');
			}
			document.instructionSheetForm.submit();
		}
	}
</script>	
<div id="post" style="display:none; position:absolute;"></div>
<?php
//show all epost ravi
?>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
	
	function showInstTemplate() {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null) {
			alert ("Browser does not support HTTP Request");
			return;
		}
		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		
		var url="instruction_ajax_template.php"
		url=url+"?template_id="+tempId
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		
		xmlHttp.onreadystatechange=showInstTemplateFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}	
	function showInstTemplateFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
			/*
			if ( document.all ) { // If Internet Explorer.
				oDOM.body.innerHTML =xmlHttp.responseText
			}*/				
			top.frames[0].setPNotesHeight();
		}
	}
	
</script>

<form name="instructionSheetForm" id="instructionSheetForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="instructionsheet.php?saveRecord=true">
	<?php
		for($h=0;$h<count($hiddenFields);$h++){
		?>
		<input type="hidden" name="sigData<?php print($h+1); ?>" id="sigData<?php print($h+1); ?>">
		<input type="hidden" name="hiddSigIpadId<?php echo($h+1); ?>" id="hiddSigIpadId<?php echo($h+1); ?>" value="">
		<?php
		}
	?>
	<input type="hidden" name="patient_instruction_id" id="patient_instruction_id" value="<?php echo $patient_instruction_id; ?>"> 
	<input type="hidden" name="getText" id="getText">
	<input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
	<input type="hidden" name="frmAction" id="frmAction" value="instructionsheet.php">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">			
	<input type="hidden" name="thisId" id="thisId" value="<?php echo $thisId; ?>">
	<input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="show_td" id="show_td" value="<?php print $instructionSheetId; ?>" >
	<input type="hidden" name="sig_count" id="sig_count" value="<?php print count($hiddenFields); ?>" >
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
	<input type="hidden" name="hiddInstrTmpltChangeId" id="hiddInstrTmpltChangeId">
	<input type="hidden" id="vitalSignGridHolder" />
    <input type="hidden" name="hiddPurgeResetStatusId" id="hiddPurgeResetStatusId">
    
	<?php
	$cntSigImage =  substr_count($instructionData,'<img src="SigPlus_images/sign_instruction');
	?>
	<input type="hidden" name="hiddCntSigImageId" id="hiddCntSigImageId" value="<?php echo $cntSigImage;?>">
    		
       	<?php
				$epost_table_name = "patient_instruction_sheet";
				include("./epost_list.php");
		?>
        <!--         
        <div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_green">
            <span class="bg_span_green">
                Instruction Sheet
            </span>
            
        </div>
        -->
		<?php 
		if($surgeonId) {
			$templateListsDetail = $objManageData->getArrayRecords('instruction_template','','', 'instruction_name', 'ASC');
		}
		?>
        <div class="scanner_win new_s bg_green_Sty">
         <Div class="change_temp_div">
        		<label class="rob col-md-3 col-sm-3 col-xs-3 col-lg-3 text-right nowrap" for="n_select">
                            Change Template
                </label>
                <Div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                	<select name="templateList" id="templateList" class="selectpicker form-control" onChange="javascript:chngInstTmplt(this.value);">
                        <option value="">Select</option>
                        <?php
                        
                        
                        if($templateListsDetail){
                            foreach($templateListsDetail as $key => $list){
                                $templateSelected='';
                                if($instructionSheetId==$list->instruction_id) {
                                    $templateSelected = 'selected';
                                }
                        ?>
                                <option value="<?php echo $list->instruction_id;?>" <?php echo $templateSelected;?>><?php echo stripslashes($list->instruction_name);?></option>
                        <?php		
                            }
                        }								
                        ?>
                    </select>
                </Div>
         </Div>	
         <h4>
            <span style=" <?php echo $instructionSheet_BackColor;?> ">
            	Patient Instruction Sheet</span>
         </h4>
      
        </div>
        <div id="divSaveAlert" style="position:absolute; left:350px; top:30px; display:none; z-index:2">
            <?php 
                $bgCol = '#779169';
                $borderCol = '#779169';
                include('saveDivPopUp.php'); 
            ?>
        </div>
        <Div class="consent_wrap_slider">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="op_right_main">
                   <?php echo $instructionData; ?>
                </div>
            </div>
			<?php
            if($signSurgeon1Activate=='yes' || $chkSignSurgeon1Activate=='yes' || $signNurseActivate=='yes' || $chkSignNurseActivate=='yes') {
				if($signSurgeon1Activate=='yes' || $chkSignSurgeon1Activate=='yes') {
				
					
				//CODE RELATED TO SURGEON SIGNATURE ON FILE
					if($loggedInUserType<>"Surgeon") {
						$loginUserName = $_SESSION['loginUserName'];
						$callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
					}else {
						$loginUserId = $_SESSION["loginUserId"];
						$callJavaFunSurgeon = "document.instructionSheetForm.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','instruction_sheet_ajaxSign.php','$loginUserId','Surgeon1');";
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
					if($_SESSION["loginUserId"]==$signSurgeon1Id) {
						$callJavaFunSurgeonDel = "document.instructionSheetForm.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','instruction_sheet_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
					}else {
						$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
					}
				//END CODE RELATED TO SURGEON SIGNATURE ON FILE
			 
			 ?>
						
                    <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="inner_safety_wrap" id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>;">
                                <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
                            </div>
                            <div class="inner_safety_wrap collapse" id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>;">
                                <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>". " Dr. ". $Surgeon1Name; ?>  </a></span>	     
                                <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $surgeon1SignOnFileStatus;?></span>
                                <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="patient_confirmation_id"> <?php echo $signSurgeon1DateTimeFormatNew; ?> <span class="fa fa-edit fa-editsurg"></span></span></span>
                            </div>
                            <input type="hidden" name="hidd_signSurgeon1Activate" value="yes">
                   </div>
                   
						
			<?php
				}
				
				if($signNurseActivate=='yes' || $chkSignNurseActivate=='yes') {
			
					//CODE RELATED TO NURSE SIGNATURE ON FILE
						if($loggedInUserType<>"Nurse") {
							$loginUserName = $_SESSION['loginUserName'];
							$callJavaFun = "return noAuthorityFunCommon('Nurse');";
						}else {
							$loginUserId = $_SESSION["loginUserId"];
							$callJavaFun = "document.instructionSheetForm.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','instruction_sheet_ajaxSign.php','$loginUserId','Nurse1');";
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
						if($_SESSION["loginUserId"]==$signNurseId) {
							$callJavaFunDel = "document.instructionSheetForm.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','instruction_sheet_ajaxSign.php','$loginUserId','Nurse1','delSign');";
						}else {
							$callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
						}
					//END CODE RELATED TO NURSE SIGNATURE ON FILE
			
			?>
					
                   <div class="col-md-4 col-sm-4 col-lg-4 col-xs-12">
                        <div class="inner_safety_wrap" id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>;">
                            <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
                        </div>
                        <div class="inner_safety_wrap collapse" id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>;">
                            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b> ". $NurseNameShow; ?>  </a></span>	     
                            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
                            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="patient_confirmation_id"> <?php echo $signNurseDateTimeFormatNew; ?> <span class="fa fa-edit"></span></span></span>
                        </div>
                        <input type="hidden" name="hidd_signNurseActivate" value="yes">	
                    </div>
			<?php
				}
			}
			if($signWitness1Activate=='yes' || $chkSignWitness1Activate=='yes') {
				//GET USER DETAIL(FOR WITNESS SIGNATURE)
					if($signWitness1Id) {
						$ViewWitnessUserNameQry = "select * from `users` where  usersId = '".$signWitness1Id."'";
						$ViewWitnessUserNameRes = imw_query($ViewWitnessUserNameQry) or die(imw_error()); 
						$ViewWitnessUserNameRow = imw_fetch_array($ViewWitnessUserNameRes); 
						$witnessUserType 		= $ViewWitnessUserNameRow["user_type"];
					}
				//END GET USER DETAIL(FOR WITNESS SIGNATURE)
				
				//CODE RELATED TO NURSE SIGNATURE ON FILE
					if($witnessUserType && ($loggedInUserType<>$witnessUserType)) {
						$loginUserName = $_SESSION['loginUserName'];
						$callJavaFunWitness = "return noAuthorityFunCommon('".$witnessUserType."');";
					}else {
						$loginUserId = $_SESSION["loginUserId"];
						$callJavaFunWitness = "document.instructionSheetForm.hiddSignatureId.value='TDwitness1SignatureId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','instruction_sheet_ajaxSign.php','$loginUserId','Witness1');";
					}
				
					$signOnFileWitness1Status = "Yes";
					$TDwitness1NameIdDisplay = "block";
					$TDwitness1SignatureIdDisplay = "none";
					$Witness1NameShow = $loggedInUserName;
					$signWitness1DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
					if($signWitness1Id<>0 && $signWitness1Id<>"") {
						$Witness1NameShow = $signWitness1LastName.", ".$signWitness1FirstName." ".$signWitness1MiddleName;
						$signOnFileWitness1Status = $signWitness1Status;	
						$TDwitness1NameIdDisplay = "none";
						$TDwitness1SignatureIdDisplay = "block";
						$signWitness1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signWitness1DateTime);
					}
					if($_SESSION["loginUserId"]==$signWitness1Id) {
						$callJavaFunWitnessDel = "document.instructionSheetForm.hiddSignatureId.value='TDwitness1NameId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','instruction_sheet_ajaxSign.php','$loginUserId','Witness1','delSign');";
					}else {
						$callJavaFunWitnessDel = "alert('Only $Witness1NameShow can remove this signature');";
					}
				//END CODE RELATED TO NURSE SIGNATURE ON FILE
			
			
		?>
				<div class="col-md-4 col-sm-4 col-lg-4 col-xs-12">
					<div class="inner_safety_wrap" id="TDwitness1NameId" style="display:<?php echo $TDwitness1NameIdDisplay;?>;">
						<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColor;?>" onClick="javascript:<?php echo $callJavaFunWitness;?>"> Witness Signature </a>
					</div>
					<div class="inner_safety_wrap collapse" id="TDwitness1SignatureId" style="display:<?php echo $TDwitness1SignatureIdDisplay;?>;">
						<span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitnessDel;?>"> <?php echo "<b>Witness:</b>"." ".$Witness1NameShow; ?>  </a></span>	     
						<span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileWitness1Status;?></span>
						<span class="rob full_width"> <b> Signature Date</b> <?php echo $signWitness1DateTimeFormatNew;?></span>
					</div>            
					<input type="hidden" name="hidd_signWitness1Activate" id="hidd_signWitness1Activate" value="yes">	
				</div>    
		<?php
			}
            ?>
            </Div>         
		

</form>	
	<!-- WHEN CLICK ON CANCEL BUTTON -->
	<form name="frm_return_BlankMainForm" method="post" action="instructionsheet.php?cancelRecord=true">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
		<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
	</form>
	<!-- END WHEN CLICK ON CANCEL BUTTON -->	

<script>
/*
var getArea = document.getElementsByTagName('textarea');
	var areaTag = new Array();
	var getTags = document.forms[0].getElementsByTagName('input');
	var arrTag = new Array();
	var a = 0;
	for(var i=0;i<getTags.length;i++)
	{
		if(document.forms[0].getElementsByTagName('input')[i].type != 'hidden')
		{
			arrTag[a] = document.forms[0].getElementsByTagName('input')[i].name;
			a++;
		}
	}
	
	for(var l=0;l<getArea.length;l++)
	{
		areaTag[l] = getArea[l].name;
		
	}
	
	var remArea = removeDuplicateElement(areaTag);
	for(var p=0;p<remArea.length;p++)
	{
		var areaName = document.getElementsByName(remArea[p]);
		
		var d=1;
		for(var b=0;b<areaName.length;b++)
		{
			if(areaName.length > 1)
			{
				areaName[b].name = areaName[b].name + d;
				d++;
			}
		} 
	}
	var ad = removeDuplicateElement(arrTag);
	for(var j=0;j<ad.length;j++)
	{
		var tagName = document.getElementsByName(ad[j]);
		var s = 1;
		for(var l=0;l<tagName.length;l++)
		{
			
			if(tagName.length > 1)
			{
				tagName[l].name = tagName[l].name + s;
				s++
			}
			
		}
		
	}
	
function removeDuplicateElement(arrayName)
  {
	var newArray=new Array();
	label:for(var b=0; b<arrayName.length;b++)
	{  
	  for(var c=0; c<newArray.length;c++ )
	  {
		if(newArray[c]==arrayName[b]) 
		continue label;
	  }
	  newArray[newArray.length] = arrayName[b];
	}
	return newArray;
  }

*/ 
</script>
<?php
//CODE FOR FINALIZE FORM
	$form_status = $chk_form_status;
	$finalizePageName = "instructionsheet.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM

if($finalize_status!='true'){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	
	</script>
	<?php
	include('privilege_buttons.php');
}else{
	?>
	<script>
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'none';
	</script>
	<?php
}
if($SaveForm_alert == 'true' && $_REQUEST['hiddInstrTmpltChangeId']!='yes'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}

?>
<script>
if(parent.parent) {
	parent.parent.show_loading_image('none');
}
</script>
<?php include("print_page.php");?>
<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>