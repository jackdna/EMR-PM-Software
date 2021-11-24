<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$tablename = "preopnursingrecord";
include_once("common/commonFunctions.php");
//include("common/linkfile.php");
include("common/link_new_file.php");
//START INCLUDE PREDEFINE FUNCTIONS
	include_once("common/food_list_pop.php"); //PRE OP  NURSING
	include_once("common/pre_comments.php"); //PRE OP  NURSING
//END INCLUDE PREDEFINE FUNCTIONS

include_once("admin/classObjectFunction.php");

$objManageData = new manageData;
extract($_GET);
$SaveForm_alert = $_REQUEST['SaveForm_alert'];

		//GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION  
			if($nurseId=="" || $nurseId==0) {
				$ViewNurseIdQry = "select * from `patientconfirmation` where  patientConfirmationId = '".$_REQUEST["pConfId"]."'";
				$ViewNurseIdRes = imw_query($ViewNurseIdQry) or die(imw_error()); 
				$ViewNurseIdRow = imw_fetch_array($ViewNurseIdRes); 
				$nurseId = $ViewNurseIdRow["nurseId"];
			}	
		//END GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION
	//GET NURSE NAME
		$ViewNurseNameQry = "select * from `users` where  usersId = '".$nurseId."'";
		$ViewNurseNameRes = imw_query($ViewNurseNameQry) or die(imw_error()); 
		$ViewNurseNameRow = imw_fetch_array($ViewNurseNameRes); 
		$NurseName = $ViewNurseNameRow["lname"].", ".$ViewNurseNameRow["fname"]." ".$ViewNurseNameRow["mname"];
	//END GET NURSE NAME

	//CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
		$patient_id = $_REQUEST["patient_id"];
		$pConfId = $_REQUEST["pConfId"];
		
		$thisId = $_REQUEST["thisId"];
		if($innerKey=="") {
			$innerKey = $_REQUEST["innerKey"];
		}
		if($preColor=="") {
			$preColor = $_REQUEST["preColor"];
		}	
		
		$fieldName = "pre_op_nursing_form";
		$pageName = "pre_op_nursing_record.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
		if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
			$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
		}
		include("left_link_hide.php");
	//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 

	$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&ascId='.$ascId;

	
	// GETTING NURSE SIGN OR NOT
	unset($conditionArr);
	$conditionArr['usersId'] = $nurseId;
	$nurseDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($nurseDetails) {
		foreach($nurseDetails as $nurse){
			$signatureOfNurse = $nurse->signature;
		}
	}	
// GETTING NURSE SIGN OR NOT

	//SAVE RECORD TO DATABASE
if($_POST['SaveRecordForm']=='yes'){
	
//CODE FOR DYNAMIC OPTIONS FROM ADMIN	
	$chkPreOpNurseQry = "select * from `preopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkPreOpNurseRes = imw_query($chkPreOpNurseQry) or die(imw_error()); 
	$chkPreOpNurseNumRow = imw_num_rows($chkPreOpNurseRes);
	if($chkPreOpNurseNumRow>0) {
		//CODE START TO CHECK FORM STATUS
			$chkPreOpNurseFormStatusRow = imw_fetch_array($chkPreOpNurseRes);
			$chkPreOpNurseFormStatus = $chkPreOpNurseFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS
	}
	
	if($chkPreOpNurseFormStatus!='completed' && $chkPreOpNurseFormStatus!='not completed') {	
		
		$chkpreopnursequestionadminQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='".$_REQUEST["pConfId"]."'";
		$chkpreopnursequestionadminRes = imw_query($chkpreopnursequestionadminQry) or die(imw_error());
		$chkpreopnursequestionadminNumRow = imw_num_rows($chkpreopnursequestionadminRes);
		if($chkpreopnursequestionadminNumRow>0) {
			//DO NOTHING
		}else {
			$preOpNurseSavecategoryQry = "SELECT * FROM preopnursecategory ORDER BY categoryId";
			$preOpNurseSavecategoryRes = imw_query($preOpNurseSavecategoryQry) or die(imw_error());
			$preOpNurseSavecategoryNumRow = imw_num_rows($preOpNurseSavecategoryRes);
			if($preOpNurseSavecategoryNumRow>0) {
				$k=0;
				while($preOpNurseSavecategoryRow = imw_fetch_array($preOpNurseSavecategoryRes)) {
					$categoryId = $preOpNurseSavecategoryRow['categoryId'];
					$categoryName = $preOpNurseSavecategoryRow['categoryName'];
					$k++;
			
					$preOpNurseSavequestionQry = "SELECT * FROM preopnursequestion WHERE preOpNurseCatId='".$categoryId."'";
					$preOpNurseSavequestionRes = imw_query($preOpNurseSavequestionQry) or die(imw_error());
					$preOpNurseSavequestionNumRow = imw_num_rows($preOpNurseSavequestionRes);
					if($preOpNurseSavequestionNumRow>0) {
						$t=0;
						while($preOpNurseSavequestionRow=imw_fetch_array($preOpNurseSavequestionRes)) {
							$t++;
							$preOpNurseSaveQuestionId = $preOpNurseSavequestionRow['preOpNurseQuestionId'];
							$preOpNurseSaveQuestionName = $preOpNurseSavequestionRow['preOpNurseQuestionName'];
							$preOpNurseSaveChkBoxQuestionName = str_replace(' ','_',$preOpNurseSaveQuestionName);
						
							$inspreOpNurseAdminQry = "INSERT INTO preopnursequestionadmin SET 
													   categoryName='".$categoryName."',
													   preOpNurseQuestionName='".$preOpNurseSaveQuestionName."',
													   preOpNurseOption='".$_REQUEST[$preOpNurseSaveChkBoxQuestionName.$k.$t]."',
													   confirmation_id	='".$_REQUEST["pConfId"]."',
													   patient_id	='".$patient_id."'
													 ";
							$inspreOpNurseAdminRes = imw_query($inspreOpNurseAdminQry) or die(imw_error());						 
						}//END INNER WHILE
					}//END IF($preOpNurseSavequestionNumRow>0)
				}//END OUTER WHILE
			}//END IF($preOpNurseSavecategoryNumRow>0)
		}//END ELSE PART		
	}else if($chkPreOpNurseFormStatus=='completed' || $chkPreOpNurseFormStatus=='not completed') {	
		
		$preOpNurseSavecategoryQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='".$_REQUEST["pConfId"]."' GROUP BY categoryName ORDER BY id";
		$preOpNurseSavecategoryRes = imw_query($preOpNurseSavecategoryQry) or die(imw_error());
		$preOpNurseSavecategoryNumRow = imw_num_rows($preOpNurseSavecategoryRes);
		if($preOpNurseSavecategoryNumRow>0) {
			$k=0;
			while($preOpNurseSavecategoryRow = imw_fetch_array($preOpNurseSavecategoryRes)) {
				$categoryName = $preOpNurseSavecategoryRow['categoryName'];
				$k++;
				$preOpNurseSavequestionQry = "SELECT * FROM preopnursequestionadmin WHERE categoryName='".$categoryName."' AND confirmation_id ='".$_REQUEST["pConfId"]."' ORDER BY id";
				$preOpNurseSavequestionRes = imw_query($preOpNurseSavequestionQry) or die(imw_error());
				$preOpNurseSavequestionNumRow = imw_num_rows($preOpNurseSavequestionRes);
				if($preOpNurseSavequestionNumRow>0) {
					$t=0;
					while($preOpNurseSavequestionRow=imw_fetch_array($preOpNurseSavequestionRes)) {
						$t++;
						$preOpNurseSaveId = $preOpNurseSavequestionRow['id'];
						$preOpNurseSaveQuestionName = $preOpNurseSavequestionRow['preOpNurseQuestionName'];
						$preOpNurseSaveChkBoxQuestionName = str_replace(' ','~',$preOpNurseSaveQuestionName);
						$preOpNurseSaveOption = $preOpNurseSavequestionRow['preOpNurseOption'];
					
						$chkpreopnursequestionadminQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='".$_REQUEST["pConfId"]."' AND id='".$preOpNurseSaveId."'";
						$chkpreopnursequestionadminRes = imw_query($chkpreopnursequestionadminQry) or die(imw_error());
						$chkpreopnursequestionadminNumRow = imw_num_rows($chkpreopnursequestionadminRes);
						if($chkpreopnursequestionadminNumRow>0) {
							
							$updatePreOpNurseAdminQry = "UPDATE preopnursequestionadmin SET 
													   preOpNurseOption='".$_REQUEST[$preOpNurseSaveChkBoxQuestionName.$k.$t]."'
													   WHERE id='".$preOpNurseSaveId."'
													   AND confirmation_id	='".$_REQUEST["pConfId"]."'
													 ";
							
							/*
							$updatePreOpNurseAdminQry = "UPDATE preopnursequestionadmin SET 
													   preOpNurseOption='".$_REQUEST['preOpNurseChkBoxQuestionName'.$k.$t]."'
													   WHERE id='".$preOpNurseSaveId."'
													   AND confirmation_id	='".$_REQUEST["pConfId"]."'
													 ";
							*/
							$updatePreOpNurseAdminRes = imw_query($updatePreOpNurseAdminQry) or die(imw_error());						 
							
						}else {
							//DO NOTHING
						}
					
					}
				}
			}
		}	
	}	
//END CODE FOR DYNAMIC QUESTION FROM ADMIN
	
	$text = $_REQUEST['getText'];
	$tablename = "preopnursingrecord";
	$allergies_status_reviewed = $_POST["chbx_drug_react_reviewed"];
	$preopNurseTime = $_POST['preopNurseTime'];
	$foodDrinkToday = $_POST["chbx_fdt"];
	$listFoodTake = addslashes($_POST["txtarea_list_food_take"]);
	$labTest = $_POST["chbx_lab_test"];
	$ekg = $_POST["chbx_ekg"];
	$consentSign = $_POST["chbx_cons_sign"];
	$hp = $_POST["chbx_h_p"];
	$admitted2Hospital = $_POST["chbx_admit_to_hosp"];
	$reason = addslashes($_POST["txtarea_admit_to_hosp"]);
	if($admitted2Hospital=="" || $admitted2Hospital=="No") {
		$reason = "";
	}
	$healthQuestionnaire = $_POST["chbx_hlt_ques"];
	$standingOrders = $_POST["chbx_stnd_odrs"];
	$patVoided = $_POST["chbx_pat_void"];
	
	$hearingAids = $_POST["chbx_hearingAids"];
	$hearingAidsRemoved = $_POST["chbx_hearingAidsRemoved"];
	if($hearingAids=="" || $hearingAids=="No") {
		$hearingAidsRemoved = "";
	}
	$denture = $_POST["chbx_denture"];
	$dentureRemoved = $_POST["chbx_dentureRemoved"];
	if($denture=="" || $denture=="No") {
		$dentureRemoved = "";
	}
	$anyPain = $_POST["chbx_anyPain"];
	$painLevel = $_POST["painLevel"];
	$painLocation = addslashes($_POST["painLocation"]);
	$doctorNotified = $_POST["chbx_doctorNotified"];
	$feet = $_POST["txt_feet"];
	$inch = $_POST["txt_inch"];
	if($feet<>"" && $inch<>"") {
		$patientHeight = addslashes($feet."'".$inch);
	}else {
		$patientHeight = "";
	}
	$patientWeight = $_POST["txt_patientWeight"];
	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignBpsub  = $_POST["txt_vitalSignBp"];
	$vitalSignBpsubstr  = 	substr($vitalSignBpsub,0,7);
	$vitalSignBp  = $vitalSignBpsubstr;
	//END OF CODE
	
	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignPsub  = $_POST["txt_vitalSignP"];
	$vitalSignPsubstr = substr($vitalSignPsub,0,3); 
	$vitalSignP  = $vitalSignPsubstr;
	//END OF CODE
	
	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignRsub  = $_POST["txt_vitalSignR"];
	$vitalSignRsubstr = substr($vitalSignRsub,0,3);
	$vitalSignR  = $vitalSignRsubstr;
	//END OF CODE 

	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignO2SATsub  = $_POST["txt_vitalSignO2SAT"];
	$vitalSignO2SATsubstr = substr($vitalSignO2SATsub,0,3);
	$vitalSignO2SAT  = $vitalSignO2SATsubstr;
	//END OF CODE 
	
	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignTemp  = $_POST["txt_vitalSignTemp"];
	//END OF CODE
	$preOpComments = addslashes(trim($_POST["txtarea_pre_operative_comment"]));
	$relivedNurseId = $_POST["relivedNurseIdList"];
	
	//START CODE TO CHECK NURSE SIGN IN DATABASE
		$chkNurseSignDetails = $objManageData->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfId);
		if($chkNurseSignDetails) {
			$chk_signNurseId = $chkNurseSignDetails->signNurseId;
		}
	//END CODE TO CHECK NURSE SIGN IN DATABASE 
	
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		if($foodDrinkToday=="" || $labTest=="" || $ekg=="" 
		 || $consentSign=="" || $hp=="" || $admitted2Hospital=="" || $healthQuestionnaire=="" 
		 || $standingOrders=="" || $patVoided=="" || $hearingAids=="" || $denture=="" 
		/* || $vitalSignBp=="" || $vitalSignP=="" || $vitalSignR=="" || $vitalSignO2SAT=="" || $vitalSignTemp=="" */
		 || $chk_signNurseId=="0")
		{
			$form_status = "not completed";
		}
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	$chkPreopnursingQry = "select * from `preopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkPreopnursingRes = imw_query($chkPreopnursingQry) or die(imw_error()); 
	$chkPreopnursingNumRow = imw_num_rows($chkPreopnursingRes);
	if($chkPreopnursingNumRow>0) {
	  	//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkPreopnursingRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		//CODE TO MAKE preOpComments FIELD EMPTY 
		imw_query("update `preopnursingrecord` set preOpComments='' 
	  				WHERE confirmation_id='".$_REQUEST["pConfId"]."'"
				  );
		//CODE TO MAKE preOpComments FIELD EMPTY 		  
		$SavePreopnursingQry = "update `preopnursingrecord` set 
									preopNurseTime = '$preopNurseTime',
									foodDrinkToday = '$foodDrinkToday',
									allergies_status_reviewed = '$allergies_status_reviewed',
									listFoodTake = '$listFoodTake', 
									labTest = '$labTest',
									ekg = '$ekg', 
									consentSign = '$consentSign',
									hp = '$hp', 
									admitted2Hospital = '$admitted2Hospital',
									reason = '$reason',
									healthQuestionnaire = '$healthQuestionnaire', 
									standingOrders = '$standingOrders', 
									patVoided = '$patVoided', 
									hearingAids = '$hearingAids',
									hearingAidsRemoved = '$hearingAidsRemoved',
									denture = '$denture',
									anyPain = '$anyPain',
									painLevel = '$painLevel',
									painLocation = '$painLocation',
									doctorNotified = '$doctorNotified',
									dentureRemoved = '$dentureRemoved',
									patientHeight = '$patientHeight',
									patientWeight = '$patientWeight',
									vitalSignBp  = '$vitalSignBp',
									vitalSignP  = '$vitalSignP',
									vitalSignR  = '$vitalSignR',
									vitalSignO2SAT = '$vitalSignO2SAT',
									vitalSignTemp  = '$vitalSignTemp',
									preOpComments = '$preOpComments', 
									relivedNurseId = '$relivedNurseId',
									preopnursingSaveDateTime = '".date("Y-m-d H:i:s")."',
									form_status ='".$form_status."',
									ascId='".$_REQUEST["ascId"]."' 
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
	}else{
		$SavePreopnursingQry = "insert into `preopnursingrecord` set 
									preopNurseTime = '$preopNurseTime',
									foodDrinkToday = '$foodDrinkToday',
									allergies_status_reviewed = '$allergies_status_reviewed',
									listFoodTake = '$listFoodTake', 
									labTest = '$labTest',
									ekg = '$ekg', 
									consentSign = '$consentSign',
									hp = '$hp', 
									admitted2Hospital = '$admitted2Hospital',
									reason = '$reason',
									healthQuestionnaire = '$healthQuestionnaire', 
									standingOrders = '$standingOrders', 
									patVoided = '$patVoided', 
									hearingAids = '$hearingAids',
									hearingAidsRemoved = '$hearingAidsRemoved',
									denture = '$denture',
									anyPain = '$anyPain',
									painLevel = '$painLevel',
									painLocation = '$painLocation',
									doctorNotified = '$doctorNotified',
									dentureRemoved = '$dentureRemoved',
									patientHeight = '$patientHeight',
									patientWeight = '$patientWeight',
									vitalSignBp  = '$vitalSignBp',
									vitalSignP  = '$vitalSignP',
									vitalSignR  = '$vitalSignR',
									vitalSignO2SAT = '$vitalSignO2SAT',
									vitalSignTemp  = '$vitalSignTemp',
									preOpComments = '$preOpComments', 
									relivedNurseId = '$relivedNurseId',
									preopnursingSaveDateTime = '".date("Y-m-d H:i:s")."',
									form_status ='".$form_status."',
									confirmation_id='".$_REQUEST["pConfId"]."'";
	}
	$SavePreopnursingRes = imw_query($SavePreopnursingQry) or die($SavePreopnursingQry.imw_error());
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
	
	//delete allregy(if chbx_drug_react==yes) when save button clicked and set allergies status in patient confirmation
	 if($_POST['chbx_drug_react']=='Yes') {
		 imw_query("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfId'");
	 }
	 $updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '".$_POST['chbx_drug_react']."' where patientConfirmationId = '$pConfId'";
	 $updateNKDAstatusRes = imw_query($updateNKDAstatusQry);
	//end delete(if chbx_drug_react==yes) when save button clicked and set allergies status in patient confirmation
	
	$save = 'true';
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		/*
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.frames[0].location='blankform.php?frameHref=pre_op_nursing_record.php&SaveForm_alert=true$saveLink';</script>";
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.frames[0].location='blankform.php?frameHref=pre_op_nursing_record.php&SaveForm_alert=true$saveLink';</script>";
		}
		*/
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
		$ViewPreopnursingQry = "select * from `preopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPreopnursingRes = imw_query($ViewPreopnursingQry) or die(imw_error()); 
		$ViewPreopnursingNumRow = imw_num_rows($ViewPreopnursingRes);
		$ViewPreopnursingRow = imw_fetch_array($ViewPreopnursingRes); 
		
		$preopnursing_vitalsign_id = $ViewPreopnursingRow["preopnursing_vitalsign_id"];
		$allergies_status_reviewed = $ViewPreopnursingRow["allergies_status_reviewed"];
		$preopNurseTime = $ViewPreopnursingRow["preopNurseTime"];
		$foodDrinkToday = $ViewPreopnursingRow["foodDrinkToday"];
		$listFoodTake = $ViewPreopnursingRow["listFoodTake"];
		$labTest = $ViewPreopnursingRow["labTest"];
		$ekg = $ViewPreopnursingRow["ekg"];
		$consentSign = $ViewPreopnursingRow["consentSign"];
		$hp = $ViewPreopnursingRow["hp"];
		$admitted2Hospital = $ViewPreopnursingRow["admitted2Hospital"];
		$reason = $ViewPreopnursingRow["reason"];
		$healthQuestionnaire = $ViewPreopnursingRow["healthQuestionnaire"];
		$standingOrders = $ViewPreopnursingRow["standingOrders"];
		$patVoided = $ViewPreopnursingRow["patVoided"];
		
		$hearingAids = $ViewPreopnursingRow["hearingAids"];
		$hearingAidsRemoved = $ViewPreopnursingRow["hearingAidsRemoved"];
		$denture = $ViewPreopnursingRow["denture"];
		$dentureRemoved = $ViewPreopnursingRow["dentureRemoved"];
		
		$anyPain = $ViewPreopnursingRow["anyPain"];
		$painLevel = $ViewPreopnursingRow["painLevel"];
		$painLocation = stripslashes($ViewPreopnursingRow["painLocation"]);
		$doctorNotified = $ViewPreopnursingRow["doctorNotified"];
		$patientHeight = $ViewPreopnursingRow["patientHeight"];
		$patientWeight = $ViewPreopnursingRow["patientWeight"];
		if($patientWeight<>"") {
			$height= explode("'",$patientHeight);
			$feet=$height[0];
			$inch=$height[1];
		}
		$weight =explode ("lb",$patientWeight);
		if($weight[0]) {
			$patientWeight=$weight[0];
		}
		$vitalSignBp = $ViewPreopnursingRow["vitalSignBp"];
		$vitalSignP = $ViewPreopnursingRow["vitalSignP"];
		$vitalSignR = $ViewPreopnursingRow["vitalSignR"];
		$vitalSignO2SAT = $ViewPreopnursingRow["vitalSignO2SAT"];
		$vitalSignTemp = $ViewPreopnursingRow["vitalSignTemp"];
		$preOpComments = $ViewPreopnursingRow["preOpComments"];
		$relivedNurseId = $ViewPreopnursingRow["relivedNurseId"];
		$form_status =  $ViewPreopnursingRow["form_status"];
		
		$signNurseId =  $ViewPreopnursingRow["signNurseId"];
		$signNurseFirstName =  $ViewPreopnursingRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewPreopnursingRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewPreopnursingRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewPreopnursingRow["signNurseStatus"];
		
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
	
	//}
//END VIEW RECORD FROM DATABASE

?>
<script>
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');

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
	
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,delSign) {
		//alert(loggedInUserId);
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
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		if(delSign) {
			url=url+"&delSign=yes"
		}
		
		xmlHttp.onreadystatechange=displayUserSignFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function displayUserSignFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{ 
			var objId = document.getElementById('hiddSignatureId').value;
			document.getElementById(objId).innerHTML=xmlHttp.responseText;
		}
	}
	
	//End Display Signature Of Nurse
	
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

//FUNCTION TO CHECK IF HIDE OR DISPLAY
	function chk_hide_show(id) {
		var k=document.frm_pre_op_nurs_rec.rowK.value;
		if(document.getElementById(id).style.display=="block") {
			++k;
		}else {
			--k;
		}
		
		document.frm_pre_op_nurs_rec.rowK.value=k;
	}
//END FUNCTION TO CHECK IF HIDE OR DISPLAY

//SAVE VITAL SIGN (BP,P,R,O2SAT,TEMP...)
	function save_vitalsign_value() {
		var m=document.frm_pre_op_nurs_rec.rowM.value;
		++m;
		document.frm_pre_op_nurs_rec.rowM.value=m;
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
			
		var vitalSignBP_main_ajax = document.frm_pre_op_nurs_rec.vitalSignBP_main.value
		var vitalSignP_main_ajax = document.frm_pre_op_nurs_rec.vitalSignP_main.value
		var vitalSignR_main_ajax = document.frm_pre_op_nurs_rec.vitalSignR_main.value
		var vitalSignO2SAT_main_ajax = document.frm_pre_op_nurs_rec.vitalSignO2SAT_main.value
		var vitalSignTemp_main_ajax = document.frm_pre_op_nurs_rec.vitalSignTemp_main.value

		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		
		var url="pre_op_nursing_record_ajax.php";
		url=url+"?vitalSignBP_main="+vitalSignBP_main_ajax
		url=url+"&vitalSignP_main="+vitalSignP_main_ajax
		url=url+"&vitalSignR_main="+vitalSignR_main_ajax
		url=url+"&vitalSignO2SAT_main="+vitalSignO2SAT_main_ajax
		url=url+"&vitalSignTemp_main="+vitalSignTemp_main_ajax
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1

		xmlHttp.onreadystatechange=AjaxTestingFun
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	
	}

	function AjaxTestingFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				document.getElementById("vital_sign_main_id").innerHTML=xmlHttp.responseText;
			}
	}
	
//END SAVE VITAL SIGN (BP,P,R,O2SAT,TEMP...  )

//DELETE VITAL SIGN (BP,P,R,O2SAT,TEMP...  )
	function delentry(id){
		var ask = confirm("Are you sure to delete the record.")
		if(ask==true){
			var m=document.frm_pre_op_nurs_rec.rowM.value;
			--m;
			if(m==0){
				document.getElementById('vital_sign_2_id').style.display = 'block';
			}
			document.frm_pre_op_nurs_rec.rowM.value=m;
			xmlHttp=GetXmlHttpObject();		
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request");
				return true;
			}		
			
			if(document.getElementById('hidd_preopnursing_vitalsign_id')) {
				var display_BaseLine=true;
				var hidd_vital_id = document.getElementById('hidd_preopnursing_vitalsign_id').value;
				if(hidd_vital_id) {
					if(hidd_vital_id==id) {
						display_BaseLine=false;
					}
				}else {
					display_BaseLine=false;
				}
				if(display_BaseLine==false) {
					if(top.document.getElementById('header_BP')) {
						top.document.getElementById('header_BP').innerText='';
					}
					if(top.document.getElementById('header_P')) {
						top.document.getElementById('header_P').innerText='';
					}
					if(top.document.getElementById('header_R')) {
						top.document.getElementById('header_R').innerText='';
					}
					if(top.document.getElementById('header_O2SAT')) {
						top.document.getElementById('header_O2SAT').innerText='';
					}
					if(top.document.getElementById('header_Temp')) {
						top.document.getElementById('header_Temp').innerText='';
					}
					
				}
			}
			
			var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
			var url='pre_op_nursing_record_vital_del_ajax.php?delId='+id+'&row='+m+'&pConfId='+pConfId1;
			xmlHttp.onreadystatechange=AjaxTestDel
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
	}		
	function AjaxTestDel(){
		if (xmlHttp.readyState==4  || xmlHttp.readyState=="complete"){
		document.getElementById("vital_sign_main_id").innerHTML=xmlHttp.responseText;
	
		}
		
	}
//END DELETE VITAL SIGN (BP,P,R,O2SAT,TEMP...  )
	
//FUNCTION TO CLEAR THE VALUE OF BP,P,R,O2SAT,TEMP...	
	function clearAll_preopnursing(t1,t2,t3,t4)
	{
		document.getElementById(t1).value="";
		document.getElementById(t2).value="";
		document.getElementById(t3).value="";
		document.getElementById(t4).value="";
	}
//END FUNCTION TO CLEAR THE VALUE OF BP,P,R,O2SAT,TEMP...	

function doSomething(e) {
	var rightclick;
	if (!e) var e = window.event;
	if (e.which) rightclick = (e.which == 3);
	else if (e.button) rightclick = (e.button == 2);
	alert('Rightclick: ' + rightclick + e.button); // true or false
}
function changeBaseLine(id, BP_change,P_change,R_change,O2SAT_change,Temp_change) {
	if(confirm('Do you want to make it as Base Line Vital Sign')) {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null) {
			alert ("Browser does not support HTTP Request");
			return;
		} 
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url='pre_op_nursing_header_vital_change_ajax.php?chngId='+id+'&pConfId='+pConfId1;
		//xmlHttp.onreadystatechange=AjaxChangeBaseLine
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
		
		if(document.getElementById('hidd_preopnursing_vitalsign_id')) {
			document.getElementById('hidd_preopnursing_vitalsign_id').value=id;
		}
		
		if(top.document.getElementById('header_BP')) {
			top.document.getElementById('header_BP').innerText=BP_change;
		}
		if(top.document.getElementById('header_P')) {
			top.document.getElementById('header_P').innerText=P_change;
		}
		if(top.document.getElementById('header_R')) {
			top.document.getElementById('header_R').innerText=R_change;
		}
		if(top.document.getElementById('header_O2SAT')) {
			top.document.getElementById('header_O2SAT').innerText=O2SAT_change;
		}
		if(top.document.getElementById('header_Temp')) {
			top.document.getElementById('header_Temp').innerText=Temp_change;
		}
		
	
	}
}

</script>
<body onLoad="top.changeColor('<?php echo $bgcolor_pre_op_nursing_order; ?>');" onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();">
	<div id="post" style="display:none;"></div>
	
<?php 

// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;	
// GETTING CONFIRMATION DETAILS

$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'preopnursingrecord' AND patient_conf_id = '$pConfId' ";
$rsNotes =imw_query($query_rsNotes);
$totalRows_rsNotes =imw_num_rows($rsNotes);
?>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" onClick="calCloseFun();preCloseFun('evaluationPreDefineDiv');preCloseFun('evaluationPreDefineMedDiv');preCloseFun('evaluationFoodListDiv');preCloseFun('evaluationPreCommentsDiv');"  onMouseOver="" bgcolor="<?php echo $bgcolor_pre_op_nursing_order; ?>">
	<tr>
		<td><img src="images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td  valign="top" align="center">
			<table border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td width="6" align="right"><img src="images/leftyellow_post_op_nurse_order.gif" width="3" height="24"></td>
					<td width="229" align="center" valign="middle" bgcolor="<?php echo $title_pre_op_nursing_order; ?>" class="text_10b1" >	<span style="color:<?php echo $title2_color;?>">Pre-Op Nursing Record</span></td>
					<td align="left" valign="top" width="10"><img src="images/rightyellow_post_op_nurse_order.gif" width="3" height="24"></td>
					<td>&nbsp;</td><td nowrap id="epostDelId"> <?php while($row = imw_fetch_array($rsNotes)) { if($totalRows_rsNotes > 0) { ?> <img src="images/sticky_note.gif" onMouseOver="showEpost('<?php echo $row['epost_id'];?>')">  <?php } } ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><img src="images/tpixel.gif" width="4" height="1"></td>
	</tr>
	<tr>
		<td align="left" style="padding-left:350px; padding-top:25px;">
			<div id="divSaveAlert" style="position:absolute;left:350; top:200; display:none;">
				<?php 
					$bgCol = $title_pre_op_nursing_order;
					$borderCol = $title_pre_op_nursing_order;
					include('saveDivPopUp.php'); 
				?>
			</div>
		</td>
	</tr>
	<form name="frm_pre_op_nurs_rec" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="">
	<input type="hidden" name="divId">
	<input type="hidden" name="counter">
	<input type="hidden" name="secondaryValues">
	<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
	<input type="hidden" name="formIdentity" value="healthQues">			
	<input type="hidden" name="SaveRecordForm" value="yes">
	<input type="hidden" name="saveRecord" value="true">
	<input type="hidden" name="getText" id="getText">
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
	<input type="hidden" name="go_pageval" value="<?php echo $tablename;?>">
	<input type="hidden" name="frmAction" value="pre_op_nursing_record.php">
	<input type="hidden" name="SaveForm_alert" value="true">
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
	<tr>
		<td>
			<table  width="99%" align="center" cellpadding="0" border="0" cellspacing="0" bgcolor="#FFFFFF" class="all_border " style="border-color:<?php echo $border_pre_op_nursing_order; ?>">
				<tr>
					<td><img src="images/tpixel.gif" width="4" height="1"></td>
					<td width="510"  valign="top">
						<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr align="left">
								<td height="22" colspan="4" class="text_10b">
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr><td colspan="5"><img src="images/tpixel.gif"></img></td></tr>
										<tr align="left" bgcolor="<?php echo $heading_pre_op_nursing_order; ?>">
											<td colspan="5" height="22"  class="text_10b" nowrap style="color:#800080;cursor:hand;" onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', '10', '35', '80'),document.getElementById('selected_frame_name_id').value='iframe_allergies_pre_op_nurse_rec';">
												<img  src="images/tpixel.gif" width="4" height="5">Allergies/Drug Reaction
											</td>
											<td height="20" width="5%" onClick="javascript:txt_enable_disable_frame1('iframe_allergies_pre_op_nurse_rec','chbx_drug_react','Allergies_quest','Reaction_quest',10)">
												<!--input style="vertical-align:top;" class="checkbox"  type="checkbox" <?php if($allergies_status =='Yes'){ echo 'CHECKED'; } ?> value="Yes"  name="chbx_drug_react" id="chbx_drug_react_no" tabindex="7"-->
												<input style="vertical-align:top;" class="checkbox"  type="checkbox" <?php if($allergiesNKDA_patientconfirmation_status=="Yes"){ echo 'CHECKED'; } ?> value="Yes"  name="chbx_drug_react" id="chbx_drug_react_no" tabindex="7">
											</td>
											<td valign="middle"  nowrap  class="text_10b" style="color:#800080;">NKDA</td>
											<td width="5%" ><!--onClick="javascript:txt_enable_frame1('iframe_health_quest','Allergies_quest','Reaction_quest',10)"-->
												 
												<input style="vertical-align:top; " class="checkbox"  type="checkbox" <?php if($allergies_status_reviewed=='Yes'){ echo 'CHECKED'; } ?>  value="Yes" name="chbx_drug_react_reviewed" id="chbx_drug_react_yes"  tabindex="7" >
											</td>
											<td valign="middle"  nowrap  class="text_10b" style="color:#800080;">Allergies Reviewed</td>
										
										</tr>
									</table>	
								</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td  colspan="4" align="left" valign="middle">
									<table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
										<tr height="22" bgcolor="#FFFFFF">
											<td>&nbsp;<img src="images/tpixel.gif" width="25" height="1"></td>
											<td width="436" class="text_10  pad_top_bottom">Name</td>
											<td width="512" colspan="4" class="text_10  pad_top_bottom">
												<img src="images/tpixel.gif" width="25" height="1">Reaction
											</td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td colspan="6" bgcolor="#F1F4F0">
												<!-- <iframe name="iframe_allergies_pre_op_nurse_rec" src="health_quest_spreadsheet.php?pConfId=<?php echo $pConfId; ?>&patient_id=<?php echo $patient_id; ?>&ascId=<?php echo $ascId; ?>&allgNameWidth=240&allgReactionWidth=240" width="100%" height="95"  frameborder="0"  scrolling="yes" ></iframe> -->   
												<div id="iframe_allergies_pre_op_nurse_rec" style="height:95px; overflow:auto; ">
													<?php  
														$allgNameWidth=220;
														$allgReactionWidth=220;
														include("health_quest_spreadsheet.php");
													?>
												</div>
											</td>
										</tr>  
									</table>
								</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td colspan="4" class="text_10b"></td>
							</tr>
							<tr><td><img src="images/tpixel.gif" width="5" height="2"></td></tr>
							<tr bgcolor="<?php echo $heading_pre_op_nursing_order; ?>">
								<td height="20" class="text_10b"><img src="images/tpixel.gif" width="15" height="1"></td>
								<td height="20" align="left" class="text_10b"><span style="color:<?php echo $title2_color;?>">Yes</span></td>
								<td width="7%" height="20" align="left" class="text_10b"><span style="color:<?php echo $title2_color;?>">No</span></td>
								<td align="left" class="text_10 pad_top_bottom"></td>
							</tr>
							<tr bgcolor="#FFFFFF" >
								<td colspan="4" height="20" align="left" valign="middle" class="text_10 pad_top_bottom">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"  >
										<tr>
											<td width="20%" nowrap align="left"  class="text_10 pad_top_bottom" valign="middle"><img src="images/tpixel.gif" width="4" height="1">Time</td>
											<td width="80%" nowrap align="left" class="text_10" ><input  type="text" name="preopNurseTime" id="bp_temp6" onKeyUp="displayText6=this.value" onClick="getShowNewPos(150,190,'flag6');clearVal_c();return displayTimeAmPm('bp_temp6');" maxlength="8" size="8"  tabindex="1"  value="<?php echo $preopNurseTime;//echo date('h:i A');?>" class="field text" style=" border:1px solid #ccccc; width:70px;"/></td>
										</tr>
									</table>										  
								</td>
							</tr>
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
								<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Food or Drink Today</td>
								<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_fdt_yes','chbx_fdt'),enable_chk_unchk('chbx_fdt_yes','chbx_fdt_no','txtarea_list_food_take');"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_fdt" id="chbx_fdt_yes" <?php if($foodDrinkToday=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_fdt_no','chbx_fdt'),enable_chk_unchk('chbx_fdt_yes','chbx_fdt_no','txtarea_list_food_take');"><input class="field checkbox" type="checkbox" value="No" name="chbx_fdt" id="chbx_fdt_no" <?php if($foodDrinkToday=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" class="text_10 pad_top_bottom"></td>
							</tr>
							<tr bgcolor="#FFFFFF" >
								<td colspan="4" height="20" align="left" valign="middle" class="text_10 pad_top_bottom">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"  >
										<tr>
											<td width="20%" nowrap align="left"  class="text_10b pad_top_bottom" style="color:#800080;cursor:hand;" onClick="return showFoodListFn('Field3', '', 'no', '5', '95'),document.getElementById('selected_frame_name_id').value='';" valign="middle"><img src="images/tpixel.gif" width="4" height="1">List Food Taken</td>
											<td width="80%" nowrap align="center" class="text_10" ><textarea id="txtarea_list_food_take"  name="txtarea_list_food_take" class="field textarea justi " <?php if($foodDrinkToday=="No" || $foodDrinkToday=="") { echo "disabled"; }?> style="border:1px solid #cccccc; width:330px; " rows="10" cols="50" tabindex="6"  ><?php echo stripslashes($listFoodTake);?></textarea></td>
										</tr>
									</table>										  
								</td>
							</tr>
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
								<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Lab Test</td>
								<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_lab_test_yes','chbx_lab_test')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_lab_test" id="chbx_lab_test_yes" <?php if($labTest=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_lab_test_no','chbx_lab_test')"><input class="field checkbox" type="checkbox" value="No" name="chbx_lab_test" id="chbx_lab_test_no" <?php if($labTest=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" class="text_10 pad_top_bottom"></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="82%" height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">EKG</td>
								<td width="8%" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_ekg_yes','chbx_ekg')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_ekg" id="chbx_ekg_yes" <?php if($ekg=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td width="7%" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_ekg_no','chbx_ekg')"><input class="field checkbox" type="checkbox" value="No" name="chbx_ekg" id="chbx_ekg_no" <?php if($ekg=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td width="3%" align="left" class="text_10 pad_top_bottom"></td>
							</tr>
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
								<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Consent Signed</td>
								<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_cons_sign_yes','chbx_cons_sign')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_cons_sign" id="chbx_cons_sign_yes" <?php if($consentSign=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_cons_sign_no','chbx_cons_sign')"><input class="field checkbox" type="checkbox" value="No" name="chbx_cons_sign" id="chbx_cons_sign_no" <?php if($consentSign=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" class="text_10 pad_top_bottom"></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">H & P</td>
								<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_h_p_yes','chbx_h_p')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_h_p" id="chbx_h_p_yes" <?php if($hp=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_h_p_no','chbx_h_p')"><input class="field checkbox" type="checkbox" value="No" name="chbx_h_p" id="chbx_h_p_no" <?php if($hp=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" class="text_10 pad_top_bottom"></td>
							</tr>
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>" id="adm_hosp_id_main">
								<td height="11" align="left" valign="middle" class="text_10 pad_top_bottom">
									<div><img src="images/tpixel.gif" width="2" height="1">Admitted To Hospital in Past 30 Days</div>
									<?php if($admitted2Hospital=='Yes') { $displayAdm2hospTxtArea="display"; }else { $displayAdm2hospTxtArea="none"; }?>
									<table cellpadding="0" cellspacing="0" width="100%" align="center" id="adm_hosp_id"  style="display:<?php echo $displayAdm2hospTxtArea;?>; " >
										<tr><td colspan="3"><img src="images/tpixel.gif" width="2" height="4"></tr>
										<tr>
											<td width="6%">&nbsp;</td>
											<td width="15%" align="left" valign="top" class="text_10 pad_top_bottom">Reason</td>
											<td width="79%"  align="left"  class="text_10"><textarea id="Field3" name="txtarea_admit_to_hosp" class="field textarea justi " style="border:1px solid #cccccc; width:330px; " rows="10" cols="50" tabindex="6"  ><?php echo stripslashes($reason);?></textarea></td>
										</tr>
									</table> 									
								</td>
								<td  valign="top" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_admit_to_hosp_yes','chbx_admit_to_hosp'),disp(document.frm_pre_op_nurs_rec.chbx_admit_to_hosp,'adm_hosp_id')"><input class="field checkbox"   type="checkbox" value="Yes" name="chbx_admit_to_hosp" id="chbx_admit_to_hosp_yes" <?php if($admitted2Hospital=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td valign="top" align="left" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_admit_to_hosp_no','chbx_admit_to_hosp'),disp_none(document.frm_pre_op_nurs_rec.chbx_admit_to_hosp,'adm_hosp_id')"><input class="field checkbox"   type="checkbox" value="No" name="chbx_admit_to_hosp" id="chbx_admit_to_hosp_no" <?php if($admitted2Hospital=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td id="arr_3" onClick="javascript:disp_rev(document.frm_pre_op_nurs_rec.chbx_hist_can,'adm_hosp_id','arr_3')" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
							</tr>
						</table>
					</td>
					<td><img src="images/tpixel.gif" width="2" height="1"></td>
					<td width="470"  valign="top">
						<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="4" align="left"></td>
							</tr>
							<tr align="left" bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" valign="top">
								<td height="22" colspan="4" valign="middle" class="text_10b" style="color:#800080;cursor:hand;" onClick="return showPreDefineMedFnNew('medication_name', 'medication_detail', '10', '575', '80'),document.getElementById('selected_frame_name_id').value='iframe_medication_pre_op_nurse';"><img src="images/tpixel.gif" width="4" height="5">Meds Taken Today</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td  colspan="4" align="left" valign="middle">
									<table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
										<tr bgcolor="#FFFFFF">
											<td height="22">&nbsp;<img src="images/tpixel.gif" width="25" height="1"></td>
											<td width="436" class="text_10  pad_top_bottom">Name</td>
											<td width="512" colspan="4" class="text_10  pad_top_bottom"><img src="images/tpixel.gif" width="15" height="1">Details</td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td colspan="6" bgcolor="#F1F4F0">
												<!-- <iframe name="iframe_medication_pre_op_nurse" src="patient_prescription_medi_spreadsheet.php?pConfId=<?php echo $pConfId; ?>&patient_id=<?php echo $patient_id; ?>&ascId=<?php echo $ascId; ?>&medicNameWidth=216&medicDetailWidth=216" width="100%" height="95"  frameborder="0"  scrolling="yes"></iframe>  -->  
												<div id="iframe_medication_pre_op_nurse" style="height:95px; overflow:auto; ">
													<?php  
														$medicNameWidth=216;
														$medicDetailWidth=216;
														include("patient_prescription_medi_spreadsheet.php");
													?>
												</div>
											</td>
										</tr> 
									</table>
								</td>
							</tr>	
							<tr>
								<td  colspan="4" align="left" valign="middle"><img src="images/tpixel.gif" width="2" height="3"></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td height="20" valign="middle" bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" class="text_10b" onClick="disp_hide_row_id('vital_sign_2_id')"><img src="images/tpixel.gif" width="5" height="1"><img src="images/vsign.gif"  style="cursor:hand;" alt="Vital signs"/></td>
								<td height="20" bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" align="left" class="text_10b"><span style="color:<?php echo $title2_color;?>">Yes</span></td>
								<td  bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" height="20" align="left" class="text_10b"><span style="color:<?php echo $title2_color;?>">No</span></td>
								<td  bgcolor="<?php echo $heading_pre_op_nursing_order; ?>" height="20" align="left" class="text_10b"></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="83%" height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Health Questionnaire</td>
								<td width="7%" align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hlt_ques_yes','chbx_hlt_ques')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_hlt_ques" id="chbx_hlt_ques_yes" <?php if($healthQuestionnaire=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td width="7%" align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_hlt_ques_no','chbx_hlt_ques')"><input class="field checkbox" type="checkbox" value="No" name="chbx_hlt_ques" id="chbx_hlt_ques_no" <?php if($healthQuestionnaire=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td width="3%" align="left" valign="top" class="text_10 pad_top_bottom"></td>
							</tr>
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
								<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Standing Orders</td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_stnd_odrs_yes','chbx_stnd_odrs')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_stnd_odrs" id="chbx_stnd_odrs_yes" <?php if($standingOrders=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_stnd_odrs_no','chbx_stnd_odrs')"><input class="field checkbox" type="checkbox" value="No" name="chbx_stnd_odrs" id="chbx_stnd_odrs_no" <?php if($standingOrders=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom"></td>
							</tr>
							
							<tr bgcolor="#FFFFFF">
								<td height="39" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Pat. Voided</td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_pat_void_yes','chbx_pat_void')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_pat_void" id="chbx_pat_void_yes" <?php if($patVoided=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_pat_void_no','chbx_pat_void')"><input class="field checkbox" type="checkbox" value="No" name="chbx_pat_void" id="chbx_pat_void_no" <?php if($patVoided=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom"></td>
							</tr>
							<tr bgcolor="#FFFFFF"><td><img src="images/tpixel.gif" width="2" height="2"></td></tr>
							
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>" >
								<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom">
									<div><img src="images/tpixel.gif" width="4" height="1">Hearing Aids</div>
									<table cellpadding="0" cellspacing="0" width="98%" align="center" id="hearingAids_id" style="display:<?php if($hearingAids=='Yes') echo "block"; else echo "none"; ?>;">
										<tr height="22">
											<td width="5%">&nbsp;</td>
											<td width="13%" valign="middle" class="text_10">Removed</td>
											<td width="13%" align="left" class="text_10 " valign="top" onClick="checkyes('chbx_hearingAids_yes','chbx_hearingAidsRemoved_yes','chbx_hearingAids_no'),checkSingle('chbx_hearingAidsRemoved_yes','chbx_hearingAidsRemoved')">
												<input class="field checkbox" type="checkbox" <?php if($hearingAidsRemoved=="Yes") echo "Checked";  ?> name="chbx_hearingAidsRemoved" value="Yes" id="chbx_hearingAidsRemoved_yes" tabindex="7" >
											</td>
											<td width="13%" valign="middle" class="text_10">Covered</td>
											<td width="13%" onClick="checkyes('chbx_hearingAids_yes','chbx_hearingAidsRemoved_no','chbx_hearingAids_no'),checkSingle('chbx_hearingAidsRemoved_no','chbx_hearingAidsRemoved')"><input class="field checkbox" type="checkbox" <?php if($hearingAidsRemoved=="No") echo "Checked";  ?> name="chbx_hearingAidsRemoved" value="No" id="chbx_hearingAidsRemoved_no" tabindex="7" ></td>
											<td width="42%">&nbsp;</td>
										</tr>
									</table>
								</td>
								<td valign="top" onClick="javascript:checkSingle('chbx_hearingAids_yes','chbx_hearingAids'),disp(document.frm_pre_op_nurs_rec.chbx_hearingAids,'hearingAids_id');chk_hide_show('hearingAids_id');" align="left" class="text_10 pad_top_bottom"><input class="field checkbox" <?php if($hearingAids=='Yes') echo "CHECKED"; ?> name="chbx_hearingAids" type="checkbox" value="Yes" id="chbx_hearingAids_yes"  tabindex="7" ></td>
								<td valign="top" onClick="javascript:checkSingle('chbx_hearingAids_no','chbx_hearingAids'),disp_none(document.frm_pre_op_nurs_rec.chbx_hearingAids,'hearingAids_id');chk_hide_show('hearingAids_id');" align="left" class="text_10 pad_top_bottom"><input class="field checkbox" <?php if($hearingAids=='No') echo "CHECKED"; ?>  name="chbx_hearingAids" type="checkbox" value="No" id="chbx_hearingAids_no" tabindex="7" ></td>
								<td id="arrow_1" onClick="javascript:disp_rev(document.frm_pre_op_nurs_rec.chbx_diab,'hearingAids_id','arrow_1');chk_hide_show('hearingAids_id');" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
							</tr>
							<tr bgcolor="#FFFFFF" >
								<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom">
									<div><img src="images/tpixel.gif" width="4" height="1">Denture</div>
									<table cellpadding="0" cellspacing="0" width="98%" align="center" id="denture_id" style="display:<?php if($denture=='Yes') echo "block"; else echo "none"; ?>;">
										<tr height="22">
											<td width="5%">&nbsp;</td>
											<td width="31%" valign="middle" class="text_10">Removed</td>
											<td width="7%" valign="middle" class="text_10">Yes</td>
											<td width="13%" align="left" class="text_10 " valign="top" onClick="checkyes('chbx_denture_yes','chbx_dentureRemoved_yes','chbx_denture_no'),checkSingle('chbx_dentureRemoved_yes','chbx_dentureRemoved')">
												<input class="field checkbox" type="checkbox" <?php if($dentureRemoved=="Yes") echo "Checked";  ?> name="chbx_dentureRemoved" value="Yes" id="chbx_dentureRemoved_yes" tabindex="7" >
											</td>
											<td width="6%" valign="middle" class="text_10">No</td>
											<td width="13%" onClick="checkyes('chbx_denture_yes','chbx_dentureRemoved_no','chbx_denture_no'),checkSingle('chbx_dentureRemoved_no','chbx_dentureRemoved')"><input class="field checkbox" type="checkbox" <?php if($dentureRemoved=="No") echo "checked";  ?> name="chbx_dentureRemoved" value="No" id="chbx_dentureRemoved_no" tabindex="7" ></td>
											<td width="24%">&nbsp;</td>
										</tr>
									</table>
								</td>
								<td valign="top" onClick="javascript:checkSingle('chbx_denture_yes','chbx_denture'),disp(document.frm_pre_op_nurs_rec.chbx_denture,'denture_id');chk_hide_show('denture_id');" align="left" class="text_10 pad_top_bottom"><input class="field checkbox" <?php if($denture=='Yes') echo "CHECKED"; ?> name="chbx_denture" type="checkbox" value="Yes" id="chbx_denture_yes"  tabindex="7" ></td>
								<td valign="top" onClick="javascript:checkSingle('chbx_denture_no','chbx_denture'),disp_none(document.frm_pre_op_nurs_rec.chbx_denture,'denture_id');chk_hide_show('denture_id');" align="left" class="text_10 pad_top_bottom"><input class="field checkbox" <?php if($denture=='No') echo "CHECKED"; ?>  name="chbx_denture" type="checkbox" value="No" id="chbx_denture_no" tabindex="7" ></td>
								<td id="arrow_2" onClick="javascript:disp_rev(document.frm_pre_op_nurs_rec.chbx_diab,'denture_id','arrow_2');chk_hide_show('denture_id');" align="center" valign="top" class="text_10 "  style="padding-top:3px; "><img  src="images/block.gif" width="11" height="13" border="0" style="cursor:hand; " /></td>
							</tr>
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
								<td height="22" align="left" valign="middle" class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="4" height="1">Any Pain</td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_anyPain_yes','chbx_anyPain')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_anyPain" id="chbx_anyPain_yes" <?php if($anyPain=="Yes") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_anyPain_no','chbx_anyPain')"><input class="field checkbox" type="checkbox" value="No" name="chbx_anyPain" id="chbx_anyPain_no" <?php if($anyPain=="No") { echo "checked"; }?> tabindex="7" ></td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom"></td>
							</tr>							
							<tr>
								<td colspan="3"  align="left" valign="middle" class="text_10 pad_top_bottom">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"  >
										<tr height="24">
											 <td width="10%" nowrap align="left"  class="text_10 pad_top_bottom"   valign="middle">
											 	<img src="images/tpixel.gif" width="4" height="1">Pain Level<img src="images/tpixel.gif" width="10" height="1">
											 </td>
											 <td width="10%" nowrap align="left"  class="text_10 pad_top_bottom"   valign="middle">	
												<img src="images/tpixel.gif" width="4" height="1">
												<select class="field text_10" style=" border:1px; vertical-align:top;" name="painLevel">
													<option value=""></option>
													<?php
													for($i=0;$i<=10;$i++) {
													?>
														<option value="<?php echo $i;?>" <?php if($painLevel==$i) echo 'selected'; ?>><?php echo $i;?></option>
													<?php
													}
													?>
												</select>
											</td>
											<td width="10%" nowrap align="left"   class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="8" height="1">Location<img src="images/tpixel.gif" width="4" height="1"></td>
											<td width="10%" nowrap align="left"   class="text_10 pad_top_bottom">
												<input  type="text" name="painLocation" value="<?php echo $painLocation;?>" size="7" class="field text"  style="width:80px;border: 1px solid #cccccc;" ><img src="images/tpixel.gif" width="1" height="1" />
											</td>
											<td width="10%" nowrap align="right"   class="text_10 pad_top_bottom"><img src="images/tpixel.gif" width="13" height="1">Dr. Notified<img src="images/tpixel.gif" width="4" height="1"></td>
											<td width="25%" align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_doctorNotified_yes','chbx_doctorNotified')"><input class="field checkbox" type="checkbox" value="Yes" name="chbx_doctorNotified" id="chbx_doctorNotified_yes" <?php if($doctorNotified=="Yes") { echo "checked"; }?> tabindex="7" ></td>
											<td width="25%" align="left" valign="middle" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_doctorNotified_no','chbx_doctorNotified')"><input class="field checkbox" type="checkbox" value="No" name="chbx_doctorNotified" id="chbx_doctorNotified_no" <?php if($doctorNotified=="No") { echo "checked"; }?> tabindex="7" ></td>
										</tr>
									</table>										  
								</td>
								<td align="left" valign="middle" class="text_10 pad_top_bottom"></td>

							</tr>
							
							<input type="hidden"  id="rowK" name="row" value="<?php //echo $k;?>">
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>" >
								<td colspan="4" height="24" align="left" valign="middle" class="text_10 pad_top_bottom">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"  >
										<tr height="24">
											<td width="20%" nowrap align="left"  class="text_10 pad_top_bottom"   valign="middle"><img src="images/tpixel.gif" width="4" height="1">Height<img src="images/tpixel.gif" width="9" height="1"></td>
											<td width="38"  nowrap align="left"  valign="middle" class="text_10" >
												 <select name="txt_feet" class="field text_10" style="vertical-align:top; ">
													<option value="" <?php if($feet=="") { echo "selected"; }?>>Ft</option>
													<option value="3" <?php if($feet==3) { echo "selected"; }?>>3</option>
													<option value="4" <?php if($feet==4) { echo "selected"; }?>>4</option>
													<option value="5" <?php if($feet==5) { echo "selected"; }?>>5</option>
													<option value="6" <?php if($feet==6) { echo "selected"; }?>>6</option>
												</select>
											</td>
											<td width="9" align="center" valign="middle">ft</td>
											<td width="42" align="center">
												<select name="txt_inch" class="field text_10" style="vertical-align:top; ">
													<option value="">In</option>
													<option value="0" <?php if($inch=="0") { echo "selected"; }?>>0</option>
													<option value="1" <?php if($inch=="1") { echo "selected"; }?>>1</option>
													<option value="2" <?php if($inch=="2") { echo "selected"; }?>>2</option>
													<option value="3" <?php if($inch=="3") { echo "selected"; }?>>3</option>
													<option value="4" <?php if($inch=="4") { echo "selected"; }?>>4</option>
													<option value="5" <?php if($inch=="5") { echo "selected"; }?>>5</option>
													<option value="6" <?php if($inch=="6") { echo "selected"; }?>>6</option>
													<option value="7" <?php if($inch=="7") { echo "selected"; }?>>7</option>
													<option value="8" <?php if($inch=="8") { echo "selected"; }?>>8</option>
													<option value="9" <?php if($inch=="9") { echo "selected"; }?>>9</option>
													<option value="10" <?php if($inch=="10") { echo "selected"; }?>>10</option>
													<option value="11" <?php if($inch=="11") { echo "selected"; }?>>11</option>
												</select>
											</td>
											<td width="" valign="middle"><img src="images/tpixel.gif" width="4" height="1">inch</td>
											<?php 
												$calculatorTopValue = "122";
												$calculatorTopChangeValue = "20";
												if($hearingAids=="Yes") { $calculatorTopChangeValue = $calculatorTopChangeValue+20; }
												if($denture=="Yes") { $calculatorTopChangeValue = $calculatorTopChangeValue+20; }
												$calculatorWeightSetValue = $calculatorTopValue+$calculatorTopChangeValue;
												
												$bpPrTempTopValue = "20";
												$calculatorBP_P_R_Temp_SetValue = $bpPrTempTopValue+$calculatorTopValue+$calculatorTopChangeValue;
												
												$pre_operative_commentValue = "138";
												$pre_operative_commentSetValue = $pre_operative_commentValue+$calculatorTopValue+$calculatorTopChangeValue;
											?>
											 <td width="20%" nowrap align="center"  class="text_10 pad_top_bottom"   valign="middle"><img src="images/tpixel.gif" width="4" height="1">Weight</td>
											<td width="46" nowrap  valign="bottom" align="left" class="text_10" ><img src="images/tpixel.gif" width="5" height="1"><input type="text"  class="field text text_10" id="bp_temp5" name="txt_patientWeight" size="2" value="<?php echo $patientWeight; ?>" tabindex="1" style="border: 1px solid #cccccc;" onKeyUp="displayText5=this.value" onClick="getShowNewPos('<?php echo $calculatorWeightSetValue;?>',715,'flag5');"/></td><td valign="middle"><img src="images/tpixel.gif" width="4" height="1">lbs</td>
										  
										</tr>
									</table>										  
								</td>
							</tr>
							<input type="hidden"  id="hidd_preopnursing_vitalsign_id" name="hidd_preopnursing_vitalsign_id" value="<?php echo $preopnursing_vitalsign_id;?>">
							<tr  bgcolor="#FFFFFF" align="left" >
								<td colspan="4" id="vital_sign_main_id">
									<table width="95%" align="left" cellpadding="0" cellspacing="0" border="0">
										<?php
											$ViewPreopNurseVitalSignQry = "select * from `preopnursing_vitalsign_tbl` where  confirmation_id = '".$_REQUEST["pConfId"]."' order by vitalsign_id";
											$ViewPreopNurseVitalSignRes = imw_query($ViewPreopNurseVitalSignQry) or die(imw_error()); 
											$ViewPreopNurseVitalSignNumRow = imw_num_rows($ViewPreopNurseVitalSignRes);
											if($ViewPreopNurseVitalSignNumRow>0) {
												$m=1;
												while($ViewPreopNurseVitalSignRow = imw_fetch_array($ViewPreopNurseVitalSignRes)) {
													$vitalsign_id=$ViewPreopNurseVitalSignRow["vitalsign_id"];  
													$vitalSignBp = $ViewPreopNurseVitalSignRow["vitalSignBp"];
													$vitalSignP = $ViewPreopNurseVitalSignRow["vitalSignP"];
													$vitalSignR = $ViewPreopNurseVitalSignRow["vitalSignR"];
													$vitalSignO2SAT = $ViewPreopNurseVitalSignRow["vitalSignO2SAT"];
													$vitalSignTemp = $ViewPreopNurseVitalSignRow["vitalSignTemp"];
													//$vitalSignTime = $ViewPreopNurseVitalSignRow["vitalSignTime"];
													if($m%2==0) {
														$bg_color_pre_op_nurse = $rowcolor_pre_op_nursing_order;
													}else {
														$bg_color_pre_op_nurse = "#FFFFFF";
													} 
													
												?>
													<TR align="left">
														
														<td width="9%"  nowrap="nowrap" class="text_10b" style="color:#800080; cursor:hand;" onClick="changeBaseLine('<?php echo $vitalsign_id; ?>','<?php echo $vitalSignBp;?>','<?php echo $vitalSignP;?>','<?php echo $vitalSignR;?>','<?php echo $vitalSignO2SAT;?>','<?php echo $vitalSignTemp;?>');"><img src="images/tpixel.gif" width="4" height="1">BP </td> <!-- onMouseDown="doSomething(event);" -->
														<td width="18%"  nowrap="nowrap" class="text_10"><?php echo $vitalSignBp;?><img src="images/tpixel.gif" width="1" height="1" /></td>
														<td width="4%"   class="text_10b">P<img src="images/tpixel.gif" width="2" height="1" /></td>
														<td width="13%"   class="text_10"><?php echo $vitalSignP;?></td>
														<td width="4%"   class="text_10b">R<img src="images/tpixel.gif" width="2" height="1" /></td>
														<td width="10%"   class="text_10"><?php echo $vitalSignR;?></td>
														<td width="9%"   class="text_10b" align="right">&nbsp;O<sub>2</sub>SAT<img src="images/tpixel.gif" width="2" height="1" /></td>
														<td width="10%"   class="text_10" align="left"><?php echo $vitalSignO2SAT;?></td>
														<td width="14%" align="right"   class="text_10b">Temp<img src="images/tpixel.gif" width="6" height="1" /></td>
														<td width="13%"   class="text_10"><?php echo $vitalSignTemp;?></td>
														<td width="43%"><img src="images/close.jpg" alt="delete" onClick="delentry(<?php echo $vitalsign_id; ?>);"></td>
													<TR>
										<?php
													$m++;
												}
											}else {
												//DO NOTHING
											}
										?>
									</table>		  
								</td>
							</tr>
							<?php 
							//$calculatorTopValue = $k*22+65;
							//$calculatorTopChangeValue = "0";
							
							if($ViewPreopNurseVitalSignNumRow>0) { $Displayvital_sign_2_id="none"; $calculatorTopChangeValue=($ViewPreopNurseVitalSignNumRow*20);}else{ $Displayvital_sign_2_id="block";} ?>
							<tr  bgcolor="#FFFFFF" align="left" id="vital_sign_2_id" style="display:<?php echo $Displayvital_sign_2_id;?> ">
								<td colspan="4" >
									<table width="98%" align="left" cellpadding="0" cellspacing="0" border="0">
										<TR align="left">
											<input type="hidden"  id="rowM" name="rowM" value="<?php echo $m;?>">
											<td width="9%"  nowrap="nowrap" class="text_10b"><img src="images/tpixel.gif" width="4" height="1">BP </td>
											<td width="18%"  nowrap="nowrap" class="text_10"><input id="bp_temp" type="text" name="vitalSignBP_main" value="" maxlength="7" size="7"/ class="field text"  style="width:55px;border: 1px solid #cccccc;" onKeyUp="displayText1=this.value" onClick="getShowNewPos(<?php echo $calculatorBP_P_R_Temp_SetValue;?>,530,'flag1');clearVal_c();"><input type="hidden" id="bp" name="bp_hidden"><img src="images/tpixel.gif" width="1" height="1" /></td>
											<td width="4%"   class="text_10b">P<img src="images/tpixel.gif" width="2" height="1" /></td>
											<td width="13%"   class="text_10"><input id="bp_temp2" type="text" name="vitalSignP_main" value="" size="3" maxlength="3" class="field text" style="width:30px;border: 1px solid #cccccc;" onKeyUp="displayText2=this.value" onClick="getShowNewPos(<?php echo $calculatorBP_P_R_Temp_SetValue;?>,610,'flag2');clearVal_c();"/></td>
											<td width="4%"   class="text_10b">R<img src="images/tpixel.gif" width="2" height="1" /></td>
											<td width="10%"   class="text_10"><input id="bp_temp3" type="text" name="vitalSignR_main" value="" size="3" maxlength="3" class="field text" style="width:30px;border: 1px solid #cccccc;" onKeyUp="displayText3=this.value" onClick="getShowNewPos(<?php echo $calculatorBP_P_R_Temp_SetValue;?>,645,'flag3');clearVal_c();"/></td>
											<td width="9%"   class="text_10b" align="right">&nbsp;O<sub>2</sub>SAT<img src="images/tpixel.gif" width="2" height="1" /></td>
											<td width="10%"   class="text_10" align="left"><input id="bp_temp4" type="text" name="vitalSignO2SAT_main" value="" size="3" maxlength="3" class="field text" style="width:30px;border: 1px solid #cccccc;" onKeyUp="displayText4=this.value" onClick="getShowNewPos(<?php echo $calculatorBP_P_R_Temp_SetValue;?>,700,'flag4');clearVal_c();"/></td>
											
											<td width="14%" align="right"   class="text_10b">Temp<img src="images/tpixel.gif" width="6" height="1" /></td>
											<td width="13%"   class="text_10"><input id="bp_temp7" type="text" name="vitalSignTemp_main" value="" maxlength="14"  size="14" class="field text" style="width:55px;border: 1px solid #cccccc;" onKeyUp="displayText7=this.value" onClick="getShowTemp(<?php echo $calculatorBP_P_R_Temp_SetValue;?>,780,'flag7');clearVal_c();"/></td>
											<td width="13%"   class="text_10" onClick="javascript:save_vitalsign_value();clearAll_preopnursing('bp_temp','bp_temp2','bp_temp3','bp_temp4','bp_temp7');save_hide_row_idTemp('vital_sign_2_id','bp_temp','bp_temp2','bp_temp3','bp_temp4','bp_temp7'); ">&nbsp;<img src="images/save.jpg" alt="save"></td><!-- onClick="javascript:if(document.getElementById('bp_temp4').value=='') {alert('Please select Time!');} else { save_vitalsign_value(); save_hide_row_idTemp('vital_sign_2_id','bp_temp','bp_temp2','bp_temp3','bp_temp4'); clearAll('bp_temp','bp_temp2','bp_temp3'); }" -->
											<!-- txt_vitalSignO2SAT -->
										<TR>
									</table>		  
								</td>
							</tr>
							<tr height="22" bgcolor="#FFFFFF">
								<td colspan="5"></td>
							</tr>
							
						</table>
					</td>
					<td><img src="images/tpixel.gif" width="4" height="1"></td>
				</tr>
				<?php
				if($form_status!='completed' && $form_status!='not completed') {	
				?>
				<tr >
					<td colspan="5">
						<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="all_border" style="border-color:<?php echo $border_pre_op_nursing_order; ?>">
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>"> 
								<?php
								//echo $form_status;
								$preopnursecategoryQry = "SELECT * FROM preopnursecategory ORDER BY categoryId";
								$preopnursecategoryRes = imw_query($preopnursecategoryQry) or die(imw_error());
								$preopnursecategoryNumRow = imw_num_rows($preopnursecategoryRes);
								if($preopnursecategoryNumRow>0) {
									$k=0;
									while($preopnursecategoryRow = imw_fetch_array($preopnursecategoryRes)) {
										$categoryId = $preopnursecategoryRow['categoryId'];
										$categoryName = $preopnursecategoryRow['categoryName'];
										$k++;
										
										$preopnursequestionQry = "SELECT * FROM preopnursequestion WHERE preOpNurseCatId='".$categoryId."'";
										$preopnursequestionRes = imw_query($preopnursequestionQry) or die(imw_error());
										$preopnursequestionNumRow = imw_num_rows($preopnursequestionRes);
										if($preopnursequestionNumRow>0) {
				?>
											<td bgcolor="#FFFFFF"><img src="images/tpixel.gif" width="4" height="1"></td>
											<td width="480"  valign="top">
												<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
													<tr align="left">
														<td  width="19%" class="text_10b"><?php echo $categoryName;?></td>
														
														<?php
																$t=0;
																while($preopnursequestionRow=imw_fetch_array($preopnursequestionRes)) {
																	$t++;
																	$preOpNurseQuestionId = $preopnursequestionRow['preOpNurseQuestionId'];
																	$preOpNurseQuestionName = $preopnursequestionRow['preOpNurseQuestionName'];
																	$preOpNurseChkBoxQuestionName = str_replace(' ','_',$preOpNurseQuestionName);
																?>
																	<td width="27%" class="text_10" nowrap align="left"><?php echo $preOpNurseQuestionName;?><input type="checkbox" name="<?php echo $preOpNurseChkBoxQuestionName.$k.$t;?>" value="<?php echo 'Yes';//$preOpNurseQuestionName;?>"></td>
																<?php	
																	if($t>=1 && $preopnursequestionNumRow==1) { echo '<td width="27%" class="text_10" nowrap align="left">&nbsp;</td><td width="27%" class="text_10" nowrap align="left">&nbsp;</td>';}
																	if($t>=2 && $preopnursequestionNumRow==2) { echo '<td width="27%" class="text_10" nowrap align="left">&nbsp;</td>';}
																	
																	if(($t%3)==0) { echo '</tr><tr align="left"><td width="19%" class="text_10b">&nbsp;</td>'; }
																}
																				
														?>
													</tr>
												</table>
											</td>
									<?php
										}
										if(($k%2)==0) { echo '</tr><tr bgcolor="#FFFFFF">'; }
									}
								}	
								?>
							</tr>
						</table>
					</td> 
				</tr>
				<?php
				}
				else if($form_status=='completed' || $form_status=='not completed') {
				?>
				<tr >
					<td colspan="5">
						<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="all_border" style="border-color:<?php echo $border_pre_op_nursing_order; ?>">
							<tr bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>"> 
								<?php
								//echo $form_status;
								$preopnursecategoryQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id='".$_REQUEST["pConfId"]."' GROUP BY categoryName ORDER BY id";
								$preopnursecategoryRes = imw_query($preopnursecategoryQry) or die(imw_error());
								$preopnursecategoryNumRow = imw_num_rows($preopnursecategoryRes);
								if($preopnursecategoryNumRow>0) {
									$k=0;
									while($preopnursecategoryRow = imw_fetch_array($preopnursecategoryRes)) {
										$categoryName = $preopnursecategoryRow['categoryName'];
										$k++;
								
										$preopnursequestionQry = "SELECT * FROM preopnursequestionadmin WHERE categoryName='".$categoryName."' AND confirmation_id='".$_REQUEST["pConfId"]."' ORDER BY id";
										$preopnursequestionRes = imw_query($preopnursequestionQry) or die(imw_error());
										$preopnursequestionNumRow = imw_num_rows($preopnursequestionRes);
										if($preopnursequestionNumRow>0) {
								?>		
											<td bgcolor="#FFFFFF"><img src="images/tpixel.gif" width="4" height="1"></td>
											<td width="480"  valign="top">
												<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
													<tr align="left">
														<td  width="19%" class="text_10b"><?php echo $categoryName;?></td>
														<?php
																$t=0;
																while($preopnursequestionRow=imw_fetch_array($preopnursequestionRes)) {
																	$t++;
																	//$preOpNurseQuestionId = $preopnursequestionRow['preOpNurseQuestionId'];
																	$preOpNurseQuestionName = $preopnursequestionRow['preOpNurseQuestionName'];
																	$preOpNurseChkBoxQuestionName = str_replace(' ','~',$preOpNurseQuestionName);
																	$preOpNurseOption = $preopnursequestionRow['preOpNurseOption'];
																?>
																	<td width="27%" class="text_10" nowrap align="left"><?php echo $preOpNurseQuestionName;?><input type="checkbox" name="<?php echo $preOpNurseChkBoxQuestionName.$k.$t;?>" value="<?php echo 'Yes';?>" <?php if($preOpNurseOption=='Yes') { echo 'checked'; }?>></td>
																	<!-- <td width="27%" class="text_10" nowrap align="left" onClick="javascript:checkSingleByName('<?php //echo 'preOpNurseChkBoxQuestionName'.$k.$t;?>','<?php //echo 'preOpNurseChkBoxQuestionName'.$k;?>')";><?php //echo $preOpNurseQuestionName;?><input type="checkbox" id="<?php //echo 'preOpNurseChkBoxQuestionName'.$k.$t;?>" name="<?php //echo 'preOpNurseChkBoxQuestionName'.$k;?>" value="<?php //echo 'Yes';?>" <?php //if($preOpNurseOption=='Yes') { echo 'checked'; }?>>
																		<input type="hidden" name="<?php //echo 'preOpNurseChkBoxQuestionName'.$k.$t;?>">
																	</td> -->
																<?php	
																	
																	//START CODE TO MAKE BLANK <td> IF NOT EXIST
																	if($t>=1 && $preopnursequestionNumRow==1) { echo '<td width="27%" class="text_10" nowrap align="left">&nbsp;</td><td width="27%" class="text_10" nowrap align="left">&nbsp;</td>';}
																	if($t>=2 && $preopnursequestionNumRow==2) { echo '<td width="27%" class="text_10" nowrap align="left">&nbsp;</td>';}
																	//END CODE TO MAKE BLANK <td> IF NOT EXIST
																	
																	if(($t%3)==0) { echo '</tr><tr align="left"><td width="19%" class="text_10b">&nbsp;</td>'; }
																}
														?>
													</tr>
												</table>
											</td>
									<?php
										}
										if(($k%2)==0) { echo '</tr><tr bgcolor="#FFFFFF">'; }
									}
								}	
								?>
							</tr>
						</table>
					</td> 
				</tr>
				<?php
				}
				?> 
				<td><img src="images/tpixel.gif" width="4" height="1"></td>							
			</table>
		</td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td valign="top">
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF" class="all_border"  style="border-color:<?php echo $border_pre_op_nursing_order; ?>">
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
				<tr align="left" valign="middle" bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
					<td class="text_10b" style="color:#800080; cursor:hand;" onClick="return showPreCommentsFnNew('pre_operative_comment_id', '', 'no', '5', '<?php echo $pre_operative_commentSetValue;?>'),document.getElementById('selected_frame_name_id').value='';"><img src="images/tpixel.gif" width="10" height="1">Preoperative Comments</td>
					<td colspan="3" class="text_10" align="left">
					<?php
						if($preOpComments) {
							$preOpCommentsWithTime = $preOpComments;
						}else {
							$preOpCommentsWithTime = "";
						}
					?>
					</td>
					<td colspan="5" class="text_10" style="width:600px; ">
						<textarea  id="pre_operative_comment_id" name="txtarea_pre_operative_comment" class="field textarea justi text_10" style=" border:1px solid #cccccc; width:600px; " rows="10" cols="50" tabindex="6"  ><?php echo stripslashes($preOpCommentsWithTime);?></textarea>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td valign="top">
			<table align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF" class="all_border" style="border-color:<?php echo $border_post_op_nursing_order;?>;"  >
				<tr><td height="8" colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td>
				</tr>
				<tr align="left" valign="middle" bgcolor="<?php echo $rowcolor_post_op_nursing_order;?>">
					<td height="25" nowrap="nowrap" class="text_10b"> 
						<?php
							$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
							$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
							$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
							
							$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
							$loggedInUserType = $ViewUserNameRow["user_type"];
							$loggedInSignatureOfNurse = $ViewUserNameRow["signature"];
							
							if($loggedInUserType<>"Nurse") {
								$loginUserName = $_SESSION['loginUserName'];
								$callJavaFun = "return noAuthorityFunCommon('Nurse');";
							}else {
								$loginUserId = $_SESSION["loginUserId"];
								$callJavaFun = "document.frm_pre_op_nurs_rec.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_nursing_record_ajaxSign.php','$loginUserId');";
							}
						
							$signOnFileStatus = "Yes";
							$TDnurseNameIdDisplay = "block";
							$TDnurseSignatureIdDisplay = "none";
							$NurseNameShow = $loggedInUserName;
							
							if($signNurseId<>0 && $signNurseId<>"") {
								$NurseNameShow = $signNurseName;
								$signOnFileStatus = $signNurseStatus;	
								
								$TDnurseNameIdDisplay = "none";
								$TDnurseSignatureIdDisplay = "block";
							}
							if($_SESSION["loginUserId"]==$signNurseId) {
								$callJavaFunDel = "document.frm_pre_op_nurs_rec.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_nursing_record_ajaxSign.php','$loginUserId','delSign');";
							}else {
								$callJavaFunDel = "alert('Only $signNurseName can remove this signature');";
							}
						?>
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td nowrap class="text_10" width="80%">
									<div id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>; " >
										<table cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td  class="text_10b" style="cursor:hand; " onClick="javascript:<?php echo $callJavaFun;?>">
													<img src="images/tpixel.gif" width="15" height="1" />
													Nurse Signature 
													<img src="images/tpixel.gif" width="40" height="1" />
												</td>
											</tr>
										</table>
									</div>	
									<div id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>; ">															
										<table  cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td class="text_10" style="cursor:hand;" onClick="javascript:<?php echo $callJavaFunDel;?>">
													<img src="images/tpixel.gif" width="15" height="1" />
													<span style="color:<?php echo $title2_color;?>">
														<?php echo "<b>Nurse :</b>"." ".$NurseNameShow;?>
													</span>
												</td>
											</tr>
											<tr>
												<td nowrap class="text_10"><img src="images/tpixel.gif" width="15" height="1" /><?php echo '<b>&nbsp;Electronically Signed :</b>&nbsp;'; echo $signOnFileStatus; /*if($signatureOfNurse) echo 'Yes'; else echo 'No'; */?></td>
											</tr>
										</table>	
									</div>		
								</td>
								<td nowrap class="text_10b" valign="middle" align="right"><img src="images/tpixel.gif" width="100" height="1" />Relief Nurse <img src="images/tpixel.gif" width="10" height="1" /> </td>
								<td  nowrap class="text_10" valign="middle">
									<select name="relivedNurseIdList" class="text_10" style=" width:150">
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
																		
											?>	
												<option value="<?php echo $relivedSelectNurseID;?>" <?php echo $sel;?>><?php echo $relivedNurseName;?></option>
											<?php
											}
											?>
									</select>
	
								</td>
							</tr>
							
						</table>
					</td> 
					<td align="right">&nbsp;</td>
				</tr>
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
			</table>
		</td>
	</tr>
	</form>
	<!-- WHEN CLICK ON CANCEL BUTTON -->
	<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="pre_op_nursing_record.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
	</form>
	<!-- END WHEN CLICK ON CANCEL BUTTON -->

	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="28"></td>
	</tr>
</table>
</body>
<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "pre_op_nursing_record.php";
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
if($save == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
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
	//SET BP, P, R, TEMP VALUES IN HEADER
		/*
		if(document.getElementById('bp_temp')) {
			top.document.getElementById('header_BP').innerText=document.getElementById('bp_temp').value;
		}
		if(document.getElementById('bp_temp2')) {
			top.document.getElementById('header_P').innerText=document.getElementById('bp_temp2').value;
		}
		if(document.getElementById('bp_temp3')) {
			top.document.getElementById('header_R').innerText=document.getElementById('bp_temp3').value;
		}
		if(document.getElementById('bp_temp4')) {
			top.document.getElementById('header_O2SAT').innerText=document.getElementById('bp_temp4').value;
		}
		if(document.getElementById('bp_temp7')) {
			top.document.getElementById('header_Temp').innerText=document.getElementById('bp_temp7').value;
		}
		*/
	//SET BP, P, R, TEMP VALUES IN HEADER
</script>