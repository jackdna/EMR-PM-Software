<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$tablename = "patient_medication_reconciliation_sheet";
$signatureDate = date("m-d-Y h:i A");
?>
<!DOCTYPE html>
<html>
<head>
<title>Medication Reconciliation Sheet</title>
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

// Pre Defined Fields

$loginUserType		=	$loggedInUserType;
$loginUserId			=	$_SESSION["loginUserId"];
$formName  				=	'medicationReconciliationSheetForm';
$ajaxSigFileName	=	'medication_reconciliation_sheet_ajaxSign.php';

if(!$cancelRecord)
{
		////// FORM SHIFT TO RIGHT SLIDER 
		$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
		$medication_reconciliation_sheet_form = $getLeftLinkDetails->medication_reconciliation_sheet_form;
		if($medication_reconciliation_sheet_form=='true'){
			$formArrayRecord['medication_reconciliation_sheet_form'] = 'false';
			$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
		}
		//MAKE AUDIT STATUS VIEW
		if($_REQUEST['saveRecord']!='true'){
			unset($arrayRecord);
			$arrayRecord['user_id'] = $_SESSION['loginUserId'];
			$arrayRecord['patient_id'] = $patient_id;
			$arrayRecord['confirmation_id'] = $pConfId;
			$arrayRecord['form_name'] = 'medication_reconciliation_sheet_form';
			$arrayRecord['status'] = 'viewed';
			$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
			$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
		}
		//MAKE AUDIT STATUS VIEW
		////// FORM SHIFT TO RIGHT SLIDER 
	}
elseif($cancelRecord)
{
	$fieldName="medication_reconciliation_sheet_form";
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
	
		
//CHECK FORM STATUS AND SIGN-ACTIVATE
$chkFormStatusDetails = $objManageData->getRowRecord('patient_medication_reconciliation_sheet', 'confirmation_id', $pConfId);
if($chkFormStatusDetails) {
	$chk_formStatus = $chkFormStatusDetails->form_status;
	$chk_signSurgeon1Id = $chkFormStatusDetails->signSurgeon1Id;
	$chk_signNurseId = $chkFormStatusDetails->signNurseId;
}
//CHECK FORM STATUS AND SIGN-ACTIVATE

//SAVE RECORD	
if($_REQUEST['saveRecord'])
{
	
		// Validate Form
		$formStatus				=	'completed';
		$fieldToValidate	= array($_POST['drop_schedule'],$_POST['start_post_op_drops'],$_POST['resume_med']);
		
		if(!($chk_signNurseId) || !($chk_signSurgeon1Id))
		{
			$formStatus	=	'not completed';		
		}
		// End Form Validation
		
		$arrayRecord['drop_schedule']			=	$_POST['drop_schedule'];
		$arrayRecord['start_post_op_drops']		=	$_POST['start_post_op_drops'];
		$arrayRecord['resume_med']				=	$_POST['resume_med'];
		$arrayRecord['discontinue']				=	addslashes($_POST['discontinue']);
			
		$arrayRecord['form_status']				=	$formStatus;
		
		
		$recon_sheet_id	=	$_REQUEST['recon_sheet_id'];
		if(!$recon_sheet_id){
			$arrayRecord['confirmation_id']		=	$pConfId;	
			$recon_sheet_id = $objManageData->addRecords($arrayRecord, 'patient_medication_reconciliation_sheet');
		}else {
			$objManageData->updateRecords($arrayRecord, 'patient_medication_reconciliation_sheet', 'confirmation_id', $pConfId);
		}

		/********************************************************
			Update Last Dose Taken for HEalth Questionnaire Medications
		********************************************************/
		
			$idHealthQuestArr			=	$_POST['idHealthQuest'];
			$lastDoseTakenHealthQuestArr	=	$_POST['lastDoseTakenHealthQuest'];
			
			if(is_array($idHealthQuestArr) && count($idHealthQuestArr) > 0 )
			{
				foreach($idHealthQuestArr as $idHQ)
				{
					$lastDoseTakenHQ	=	addslashes($lastDoseTakenHealthQuestArr[$idHQ]);
					if($idHQ)
					{
						$updateQry	=	"Update patient_prescription_medication_healthquest_tbl Set prescription_medication_last_dose_taken = '".$lastDoseTakenHQ."' Where prescription_medication_id = '".$idHQ."' ";
						imw_query($updateQry) or die('Error Found @ Line No. '.(__LINE__).': '. imw_error());
					}
				}
			}
		
		/*********************************************************
			Update Last Dose Taken for HEalth Questionnaire Medications
		*********************************************************/
		
		
		/*******************************
			Creating Audit Status on Save
		********************************/
				
		//MAKE AUDIT STATUS CRATED OR MODIFIED
		unset($arrayStatusRecord);
		$arrayStatusRecord['user_id'] = $_SESSION['loginUserId'];
		$arrayStatusRecord['patient_id'] = $patient_id;
		$arrayStatusRecord['confirmation_id'] = $pConfId;
		$arrayStatusRecord['form_name'] = 'medication_reconciliation_sheet_form';	
		$arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
		//MAKE AUDIT STATUS CRATED OR MODIFIED
	
		//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'medication_reconciliation_sheet_form';
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
		
		/************************************
			End Creating Audit Status on Save
		*************************************/
		
		
		//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";
		//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
}	
	
	
	// GETTING Reconciliation sheet DETAILS	
		
	$reconDetails = $objManageData->getRowRecord('patient_medication_reconciliation_sheet', 'confirmation_id', $pConfId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat ");
	$recon_sheet_id				=	$reconDetails->recon_sheet_id;
	$drop_schedule				=	$reconDetails->drop_schedule;
	$start_post_op_drops	=	$reconDetails->start_post_op_drops;
	$resume_med						=	$reconDetails->resume_med;
	$discontinue					=	stripslashes($reconDetails->discontinue);
	
	// GETTING Reconciliation sheet DETAILS			
		
		
//GETTING PATIENT CONFIRMATION DETAILS

//GETTING PATIENT DETAILS
	if(!$patient_id) {
		$patient_id = $_REQUEST['patient_id'];
	}
	
	$patientDetails = $objManageData->getExtractRecord('patient_data_tbl', 'patient_id', $patient_id);
	if(count($patientDetails)>0){
		extract($patientDetails);
	}	
	
	//FETCH DATA FROM  TABLE
	$allergiesPreOp	=	$objManageData->getArrayRecords('patient_allergies_tbl','patient_confirmation_id',$pConfId);
	$healthQuestMed	=	$objManageData->getArrayRecords('patient_prescription_medication_healthquest_tbl','confirmation_id',$pConfId,'prescription_medication_name','Asc');
	//$medTakenTodayPreOp	=	$objManageData->getArrayRecords('patient_prescription_medication_tbl','confirmation_id',$pConfId,'prescription_medication_id','Asc'); 
	// END FETCH DATA FROM TABLE
	
	
	//$rightPadMedTaken	=	(is_array($medTakenTodayPreOp) && count($medTakenTodayPreOp) > 7 ) ? 15 : 0;
	$rightPadHQ				=	(is_array($healthQuestMed) && count($healthQuestMed) > 7) ? 15 : 0;
	$rightPadAllergies=	(is_array($allergiesPreOp) && count($allergiesPreOp) > 7) ? 15 : 0;
	
?>
<script>
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
	
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {

		if(delSign) {
			document.getElementById(TDUserNameId).style.display = 'block';
			document.getElementById(TDUserSignatureId).style.display = 'none';
			
			//SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
			if(userIdentity == 'Surgeon1'){
				if(top.document.forms[0]){
					if(top.document.forms[0].hidd_chkDisplaySurgeonSign) {
						top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'true';
					}
				}
			}		
			//END SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
		
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

</script>
<div id="post" style="display:none; position:absolute;"></div>
<script src="js/dragresize.js"></script>
<form name="medicationReconciliationSheetForm" id="medicationReconciliationSheetForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="medication_reconciliation_sheet.php?saveRecord=true">
	<input type="hidden" name="recon_sheet_id" id="recon_sheet_id" value="<?php echo $recon_sheet_id; ?>"> 
	<input type="hidden" name="getText" id="getText">
	<input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
	<input type="hidden" name="frmAction" id="frmAction" value="medication_reconciliation_sheet.php">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">			
	<input type="hidden" name="thisId" id="thisId" value="<?php echo $thisId; ?>">
	<input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="show_td" id="show_td" value="<?php print $instructionSheetId; ?>" >
	<input type="hidden" name="sig_count" id="sig_count" value="<?php print count($hiddenFields); ?>" >
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
	<input type="hidden" name="hiddInstrTmpltChangeId" id="hiddInstrTmpltChangeId">
	<input type="hidden" id="vitalSignGridHolder" />
  <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<?php
		$epost_table_name = "patient_medication_reconciliation_sheet";
		include("./epost_list.php");
	?>
  		<?php include('saveDivPopUp.php'); ?>
       
      <!--  Allergies -->
      <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
      	<div class="panel panel-default bg_panel_green">
            <div class="panel-heading">
              <h3 class="panel-title rob">Allergies/Drug Reaction</h3>
            </div>
            <div class="panel-body">
            
           		<div class="inner_safety_wrap">
              	<div class="row">
                	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                  	<div class="scheduler_table_Complete ">
                    	<div class="my_table_Checkall table_slider_head" style="padding-right:<?=$rightPadAllergies?>px !important;">
                      	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                        	<thead class="cf">
                          	<tr>
                            	<th class="text-left col-xs-4">Name</th>
                              <th class="text-left col-xs-8">Reaction</th>
                          	</tr>
                         	</thead>
                       	</table>
                      </div>
                      
                      <div class="table_slider" style="max-height:220px;" >
                      	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                      
                      	<?php
								
													$counter = 0;
													if($allergiesPreOp)
													{
															foreach($allergiesPreOp  as $key=>$val)
															{
																$counter++;
																echo '<tr>';
																echo '<td class="col-xs-4">';
																echo $val->allergy_name;
																echo '</td>';
																
																echo '<td class="col-xs-8">';
																echo $val->reaction_name;
																echo '</td>';
																
																echo '</tr>';
															}
													}
													for($i = ($counter) ;  $i < 3;  $i++)
													{
														echo '<tr >';
														echo '<td class="col-xs-4">&nbsp;</td>';
														echo '<td class="col-xs-8">&nbsp;</td>';
														echo '</tr>';		
													}
												
												?>
                      	</table>  
                     	</div>
                		</div>
                 	</div>
                  
                  
               	</div>
							</div>
              
          	</div>
       	</div>
      </div> 
      
     	<!--  Current Medications -->
      <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
      	<div class="panel panel-default bg_panel_green">
            <div class="panel-heading">
              <h3 class="panel-title rob">Current Medications</h3>
            </div>
            <div class="panel-body">
            
           		<div class="inner_safety_wrap">
              	<div class="row">
                	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                  	<div class="scheduler_table_Complete ">
                    	<div class="my_table_Checkall table_slider_head" style="padding-right:<?=$rightPadHQ?>px !important;">
                      	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                        	<thead class="cf">
                          	<tr>
                            	<th class="text-left col-xs-3">Name</th>
                              <th class="text-left col-xs-3">Dosage</th>
                              <th class="text-left col-xs-3">Sig</th>
                              <th class="text-left col-xs-3">Last Dose Taken</th>
                              <!--<th class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3">Reason For Medication</th>-->
                           	</tr>
                         	</thead>
                       	</table>
                      </div>
                      
                      <div class="table_slider" style="max-height:220px;" >
                      	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                      
                      	<?php
								
													$counter = 0;
													if($healthQuestMed)
													{
															foreach($healthQuestMed  as $key=>$val)
															{
																$rid	=	$val->prescription_medication_id;
																$reason = $val->prescription_medication_reason;
																$last_dose_taken = stripslashes($val->prescription_medication_last_dose_taken);
																$style	=	'style="background-color:white; border:solid 1px #DDD;"';
																$counter++;
																echo '<tr style="padding-left:0; background-color:#FFFFFF; ">';
																echo '<td class="col-xs-3">';
																echo $val->prescription_medication_name;
																echo '</td>';
																
																echo '<td class="col-xs-3">';
																echo $val->prescription_medication_desc;
																echo '</td>';
																
																echo '<td class="col-xs-3">';
																echo $val->prescription_medication_sig;
																echo '</td>';
																
																echo '<td class="col-xs-3">';
																echo '<input type="hidden" class="form-control" value="'.$rid.'" name="idHealthQuest[]" id="idHealthQuest'.$rid.'" />';
																echo '<input type="text" class="form-control"  value="'.$last_dose_taken.'" '.$style.' name="lastDoseTakenHealthQuest['.$rid.']" id="lastDoseTakenHealthQuest'.$rid.'" />';
																echo '</td>';
																
																echo '</tr>';
															}
													}
													for($i = ($counter) ;  $i < 3;  $i++)
													{
														echo '<tr style="padding-left:0; background-color:#FFFFFF; ">';
														echo '<td class="col-xs-3">&nbsp;</td>';
														echo '<td class="col-xs-3">&nbsp;</td>';
														echo '<td class="col-xs-3">&nbsp;</td>';
														echo '<td class="col-xs-3">&nbsp;</td>';
														echo '</tr>';		
													}
												
												?>
                      	</table>  
                     	</div>
                		</div>
                 	</div>
                  
                  
               	</div>
							</div>
              
          	</div>
       	</div>
      </div>
      
      <div class="clearfix border-dashed margin-adjustment-only"></div>
      
      
      <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">
      	<div class="panel-body " >
        	<div class="row">
          	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 ">
          	<ul class="list-group checked-list-box ">
            	<li class="list-group-item full_width">
              	<span class="" style=" <?php echo $whiteBckGroundColor;?> ">
                	<input type="checkbox" value="1" name="drop_schedule" id="drop_schedule" <?=($drop_schedule ? 'checked' : '')?> onClick="javascript:changeChbxColor('drop_schedule')">
               	</span>
                &nbsp;<label for="drop_schedule">Drop Schedule to be given at post-operative appointment</label>
            	</li>
              
              <li class="list-group-item full_width">
              	<span class="" style=" <?php echo $whiteBckGroundColor;?> ">
                	<input type="checkbox" value="1" name="resume_med" id="resume_med" <?=($resume_med ? 'checked' : '')?> onClick="javascript:changeChbxColor('resume_med')">
                </span>  
                &nbsp;<label for="resume_med">Resume all home medications</label>
            	</li>
          	</ul>
        	</div>
        	</div>  
    		</div>
   		</div>
      
      
   		<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 ">
      	<div class="panel-body " >
        	<div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 " >
              <ul class="list-group checked-list-box ">
                <li class="list-group-item full_width">
                	<span class="" style=" <?php echo $whiteBckGroundColor;?> ">
                  	<input type="checkbox" value="1" name="start_post_op_drops" id="start_post_op_drops" <?=($start_post_op_drops ? 'checked' : '')?> onClick="javascript:changeChbxColor('start_post_op_drops')">
                 	</span>   
                  &nbsp;<label for="start_post_op_drops">Start post-operative drops day of surgery</label>
                </li>
                
                <li class="list-group-item full_width ">
                	<div class="row">
                  	<?php
											//$discontinueBackColor	=	($discontinue)	?	'#FFF'	:	'#F6C67A'	;
											//onfocus="changeTxtGroupColor(1,'discontinue');" onkeyup="changeTxtGroupColor(1,'discontinue');" onblur="if(!this.value){this.style.backgroundColor='#F6C67A' }" style=" background-color:<?=$discontinueBackColor
										?>  
                    <div class="col-xs-12 col-md-2 ">
                    	<label for="discontinue">Discontinue</label>&nbsp;
                   	</div>
                    <div class="col-xs-12 col-md-10 ">
                   		<input type="text" class="form-control" value="<?=$discontinue?>" name="discontinue" id="discontinue" />
                   	</div>   
                	</div>  
                </li>
              </ul>
            </div>
    			</div>
        </div>
   		</div>   
      
      
      <div class="clearfix border-dashed margin-adjustment-only"></div>
      
      <div class=" col-lg-6 col-sm-12 col-md-6 col-xs-12">
      	<?php
					echo $objManageData->signatureHTML($tablename, 'Surgeon1', $loginUserId, $formName, $ajaxSigFileName, $pConfId);
				?>        
    	</div>
      
  		<div class=" col-lg-6 col-sm-12 col-md-6 col-xs-12">
      	<?php
					
					echo $objManageData->signatureHTML($tablename, 'Nurse', $loginUserId, $formName, $ajaxSigFileName, $pConfId);
				?>        
    	</div>
      
            
		

</form>	
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" method="post" action="medication_reconciliation_sheet.php?cancelRecord=true">
  <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
  <input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
  <input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->	
<?php
//CODE FOR FINALIZE FORM
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