<?php
/**
 * Under MIT License
 * Use, Modify, Distribute under MIT License.
 * MIT License 2019
 * 
 */
 
session_start();
include_once("common/conDb.php");
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
include_once("common/commonFunctions.php"); 
include("common/iOLinkCommonFunction.php");
include("common/user_agent.php");
$signatureDate = date("m-d-Y h:i A");
$patient_id=$_REQUEST['patient_id'];
$intPatientWaitingId=$_REQUEST['intPatientWaitingId'];
$consentMultipleId=$_REQUEST['intConsentTemplateId'];
$consentAllMultipleId=$_REQUEST['consentAllMultipleId'];
$tablename = "iolink_consent_filled_form";
//PURGE
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$signDateTime = date("Y-m-d H:i:s");

//START GET VOCABULARY OF ASC
$ascInfoArr = $objManageData->getASCInfo($_SESSION["iolink_facility_id"]);
//END GET VOCABULARY OF ASC

//GET USER DETAIL(FOR USER SIGNATURE)
	$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["iolink_loginUserId"]."'";
	$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
	$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
	
	$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
	$loggedInUserType = $ViewUserNameRow["user_type"];
	$loggedInSignatureOfUser = $ViewUserNameRow["signature"];
	$logInUserSubType = $ViewUserNameRow["user_sub_type"];
	$loggedInUserFName = $ViewUserNameRow["fname"];
	$loggedInUserMName = $ViewUserNameRow["mname"];
	$loggedInUserLName = $ViewUserNameRow["lname"];
//END GET USER DETAIL(FOR USER SIGNATURE)

//START GET PATIENT PERSONAL DETAIL
$genderArray = array("m"=>"Male","f"=>"Female");
$Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
$Consent_patientName_tblRes = imw_query($Consent_patientName_tblQry) or die(imw_error());
$Consent_patientName_tblRow = imw_fetch_array($Consent_patientName_tblRes);
$Consent_patientName = $Consent_patientName_tblRow["patient_lname"].", ".$Consent_patientName_tblRow["patient_fname"]." ".$Consent_patientName_tblRow["patient_mname"];
$Consent_patientNameDobTemp = $Consent_patientName_tblRow["date_of_birth"];
$Consent_patientNameDob_split = explode("-",$Consent_patientNameDobTemp);
$Consent_patientNameDob = $Consent_patientNameDob_split[1]."-".$Consent_patientNameDob_split[2]."-".$Consent_patientNameDob_split[0];
//END GET PATIENT PERSONAL DETAIL

//START GET PATIENT OTHER DETAIL
	$Consent_patientConfirm_tblQry = "SELECT * FROM `patient_in_waiting_tbl` WHERE `patient_in_waiting_id` = '".$intPatientWaitingId."'";
	$Consent_patientConfirm_tblRes = imw_query($Consent_patientConfirm_tblQry) or die(imw_error());
	if(imw_num_rows($Consent_patientConfirm_tblRes)>0) {
		$Consent_patientConfirm_tblRow = imw_fetch_array($Consent_patientConfirm_tblRes);
		$Consent_patientConfirmDosTemp = $Consent_patientConfirm_tblRow["dos"];
		$Consent_patientConfirmDos_split = explode("-",$Consent_patientConfirmDosTemp);
		$Consent_patientConfirmDos = $Consent_patientConfirmDos_split[1]."-".$Consent_patientConfirmDos_split[2]."-".$Consent_patientConfirmDos_split[0];
	
		
		$Consent_surgeon_fname = $Consent_patientConfirm_tblRow["surgeon_fname"];
		$Consent_surgeon_mname = $Consent_patientConfirm_tblRow["surgeon_mname"];
		$Consent_surgeon_lname = $Consent_patientConfirm_tblRow["surgeon_lname"];
		
		$Consent_patientConfirmSurgeon = $Consent_surgeon_lname.", ".$Consent_surgeon_fname." ".$Consent_surgeon_mname;
		$Consent_patientConfirmSite = $Consent_patientConfirm_tblRow["site"];
		$Consent_patientConfirmPrimProc = $Consent_patientConfirm_tblRow["patient_primary_procedure"];
		$Consent_patientConfirmSecProc 	= $Consent_patientConfirm_tblRow["patient_secondary_procedure"];
		$Consent_patientConfirmTeriProc	= $Consent_patientConfirm_tblRow["patient_tertiary_procedure"];
	
	}
//END GET PATIENT OTHER DETAIL 

//SAVE RECORD IN DATABASE
if($_POST['SaveRecordForm']=='yes'){
	
	$sig_count = $_POST['sig_count'];
	$show_td = $_POST['show_td'];
	//START
	$getConsentSurgeryQry ="SELECT 
								* 
							FROM 
								`iolink_consent_filled_form` 
							WHERE  
								fldPatientWaitingId = '".$_REQUEST["intPatientWaitingId"]."' 
							AND 
								consent_template_id='".$show_td."' 
							AND 
								consent_template_id!='0' 
							AND 
								surgery_consent_data!=' '".$andConsentIdQry;
								
	$getConsentSurgeryRes = @imw_query($getConsentSurgeryQry) or die(imw_error()); 
	$getConsentSurgeryNumRow = @imw_num_rows($getConsentSurgeryRes);
	if($getConsentSurgeryNumRow>0) { 
		$getConsentSurgeryRow = @imw_fetch_array($getConsentSurgeryRes);
		
		$consent_form_content_data = stripslashes($getConsentSurgeryRow['surgery_consent_data']);
		$modifyFormStatus = $getConsentSurgeryRow['form_status'];
	}else {	
	
		$getConsentDataAdminDetails = $objManageData->getRowRecord('consent_forms_template', 'consent_id', $show_td);
		if($getConsentDataAdminDetails) {
			$consent_form_content_data = stripslashes($getConsentDataAdminDetails->consent_data);
		}
	}
	//END
	$consent_form_content_data = str_ireplace("&#39;","'",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{ASSISTANT_SURGEON_SIGNATURE}","{SIGNATURE}",$consent_form_content_data);
	
	//START MAKE VALUE IN {} AS CASE SENSITIVE	
	$consent_form_content_data= str_ireplace("{TEXTBOX_XSMALL}","{TEXTBOX_XSMALL}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{TEXTBOX_SMALL}","{TEXTBOX_SMALL}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{TEXTBOX_MEDIUM}","{TEXTBOX_MEDIUM}",$consent_form_content_data);
	$consent_form_content_data= str_ireplace("{TEXTBOX_LARGE}","{TEXTBOX_LARGE}",$consent_form_content_data);
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
		
		
		if(substr_count($consent_form_content_data,$arrStr[$j]) >= 1)
		{
			
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$consent_form_content_data);
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'" size="'.$size.'"  maxlength="'.$size.'">';
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$c = 1;
				$arrExp = explode($arrStr[$j],$consent_form_content_data);
				
				for($p = 0;$p<count($arrExp)-1;$p++)
				{
					$repVal .= $arrExp[$p].'<textarea rows="2" cols="80" name="'.$name.$c.'"> '.$_POST[$name.$c].' </textarea>';
					$c++;
				}
				$repVal .= end($arrExp);
				$consent_form_content_data = $repVal;
			}
		}
		/*
		else
		{
			if($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}')
			{
				$repVal = str_ireplace($arrStr[$j],'<input type="text" name="'.$name.'" value="'.$_POST[$name].'" size="'.$size.'" >',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}
			else if($arrStr[$j] == '{TEXTBOX_LARGE}')
			{
				$repVal = str_ireplace($arrStr[$j],'<textarea rows="2" cols="80" name="'.$name.'"> '.$_POST[$name].' </textarea>',$consent_form_content_data);
				$consent_form_content_data = $repVal;
			}
			
		}*/
		 
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
				if(substr_count($consent_form_content_data,$arrModifyStr[$j]) >= 1)
				{
					$cntSubstr =  substr_count($consent_form_content_data,$arrModifyStr[$j]);
					if($arrModifyStr[$j] == 'name="xsmall' || $arrModifyStr[$j] == 'name="small' || $arrModifyStr[$j] == 'name="medium')
					{
						$c = 1;
						
						for($p = 0;$p<$cntSubstr;$p++) {
							$txtBoxReplace = str_ireplace('<input type="text"  name="'.$name.$c.'" value="','<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'"',$consent_form_content_data);
							$consent_form_content_data = $txtBoxReplace;
							$txtBoxExplode = explode('<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'"',$consent_form_content_data);
							$txtBoxFurtherExplode = explode(' size="'.$size.'"',$txtBoxExplode[1]);
							$getpos = strpos($txtBoxFurtherExplode[0],'"');
							$txtBoxFurtherExplodeSubStr = substr($txtBoxExplode[1],$getpos+1);
							//if($_POST[$name.$c]) {
								$consent_form_content_data = $txtBoxExplode[0].'<input type="text"  name="'.$name.$c.'" value="'.$_POST[$name.$c].'"'.$txtBoxFurtherExplodeSubStr;
							//}
							$c++;
						}
					}
					else if($arrModifyStr[$j] == 'name="large')
					{
						$c = 1;
						for($p = 0;$p<$cntSubstr;$p++) {
							$consent_form_content_data = preg_replace('/<textarea rows="2" cols="80" name="'.$name.$c.'"> (.*?) <\/textarea>/','<textarea rows="2" cols="80" name="'.$name.$c.'"> '.$_POST[$name.$c].' </textarea>',$consent_form_content_data);
							$c++;
						}
					}
					
					
				}/*
				else if(substr_count($consent_form_content_data,$arrModifyStr[$j]) == 1)
				{
					if($arrModifyStr[$j] == 'name="xsmall' || $arrModifyStr[$j] == 'name="small' || $arrModifyStr[$j] == 'name="medium')
					{
						$txtBoxExplode = explode('<input type="text" name="'.$name.'" value="',$consent_form_content_data);
						$txtBoxSizeExplode = explode('" size="'.$size.'" >',$consent_form_content_data);
						
						$repModifyValTemp = '<input type="text" name="'.$name.'" value="'.$_POST[$name].'" size="'.$size.'" >';
						$repModifyVal = $txtBoxExplode[0].$repModifyValTemp.$txtBoxSizeExplode[1];
						$consent_form_content_data = $repModifyVal;
					}
					else if($arrModifyStr[$j] == 'name="large')
					{
						$txtAreaExplode = explode('<textarea rows="2" cols="80" name="'.$name.'"> ',$consent_form_content_data);
						$txtAreaSizeExplode = explode(' </textarea>',$consent_form_content_data);
						
						$repModifyValTemp = '<textarea rows="2" cols="80" name="'.$name.'"> '.$_POST[$name].' </textarea>';
						$repModifyVal = $txtAreaExplode[0].$repModifyValTemp.$txtAreaSizeExplode[1];
						$consent_form_content_data = $repModifyVal;
					}
				}*/
						
			}
		}	
	//MODIFY TEXTBOXES AFTER SAVED ATLEAST ONCE	
	
	//SAVE SIGNATURE
		for($ps=1;$ps<=$sig_count;$ps++){
			$postData = $_POST['sigData'.$ps];
			if($postData!='' && $postData!= 'undefined'  && $postData != '000000000000000000000000000000000000000000000000000000000000000000000000') {
				$dtCur = date('d_m_y_h_i_s');
				$filePathSave = 'SigPlus_images/sign_iolink_'.$_REQUEST["intPatientWaitingId"].'_'.$dtCur.'_'.$ps.'.jpg';
				$path =  realpath(dirname(__FILE__)).'/'.$filePathSave;
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
					$patientSignArr[$ps] = $filePathSave;
					$aConn->WriteImageFile("$path");
				}
				else {
					$objManageData->getSigImage($postData,$path,$rootServerPath);
					$patientSignArr[$ps] = $filePathSave;
				}
				
				//--- Save Array Fields --------
				unset($sigDataArr);
				$sigDataArr['signature_content'] = $postData;
				$sigDataArr['consent_template_id'] = $show_td;
				$sigDataArr['patient_in_waiting_id'] = $_REQUEST["intPatientWaitingId"];
				$sigDataArr['patient_id'] = $_REQUEST["patient_id"];
				$sigDataArr['signature_count'] = $ps;
				$sigDataArr['signature_image_path'] = addslashes($filePathSave);
				
				$chk_sig_data_qry ="SELECT 
										consent_form_signature_id 
									FROM 
										iolink_consent_form_signature 
									WHERE 
										patient_in_waiting_id = '".$_REQUEST["intPatientWaitingId"]."'
									AND 
										consent_template_id = '$show_td'
									AND 
										signature_count = ".$ps."";
				
				$chk_sig_data_res = imw_query($chk_sig_data_qry);
				$chk_sig_data_num_row = imw_num_rows($chk_sig_data_res);
				
				if($chk_sig_data_num_row>0) {	
					$chk_sig_data_row = imw_fetch_array($chk_sig_data_res);
					$consent_form_signature_id = $chk_sig_data_row['consent_form_signature_id'];
					$sig_insert_id = $consent_form_signature_id;
					//UDPATE RECORD
					$objManageData->updateRecords($sigDataArr, 'iolink_consent_form_signature', 'consent_form_signature_id', $consent_form_signature_id);
				}
				else{
					//INSERT RECORD
					$sig_insert_id = $objManageData->addRecords($sigDataArr, 'iolink_consent_form_signature');
				}
			}
		}
		
		$form_status = "completed";//BY DEFAULT VALUE
		$consentSignedStatus = 'true';
		//-- get signature applets ----
		$consent_form_content_data= str_ireplace("{SIGNATURE}","{SIGNATURE}",$consent_form_content_data);
		$row_arr = explode('{SIGNATURE}',$consent_form_content_data);
		$consent_form_content_data = $row_arr[0];
		$sigDtTmSave = '<br><div style="font-weight:normal;"><b>Signature Date:</b>&nbsp;'.$signatureDate.'</div>';
		for($c=1;$c<count($row_arr);$c++){
			$imgNameArr = explode('/',$patientSignArr[$c]);
			$imgSrc = end($imgNameArr);
			$hiddSigIpadIdImg = trim($_REQUEST['hiddSigIpadId'.$c]);
			
			if($hiddSigIpadIdImg) {//FOR TOUCH SIGNATURE
				$consent_form_content_data .= '<img src="'.$hiddSigIpadIdImg.'" width="150" height="83">'.$sigDtTmSave;	
			}
			else if(!$imgSrc) {
				$consent_form_content_data .= '{SIGNATURE}';
				$form_status = "not completed";	//IF PATIENT DOES NOT SIGNED THE CHART
				$consentSignedStatus = '';
			}
			else if($imgSrc=='{SIGNATURE}') {
				$consent_form_content_data .= '{SIGNATURE}';
				$form_status = "not completed";	//IF PATIENT DOES NOT SIGNED THE CHART
			}
			else {
				$consent_form_content_data .= '<img src="SigPlus_images/'.$imgSrc.'" width="150" height="83">'.$sigDtTmSave;
			}
			$consent_form_content_data .= $row_arr[$c];
		}
			
			
		//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
			$consent_form_content_data= str_ireplace("{PATIENT ID}","<b>".$Consent_patientName_tblRow["patient_id"]."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{PATIENT FIRST NAME}","<b>".$Consent_patientName_tblRow["patient_fname"]."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{MIDDLE INITIAL}","<b>".$Consent_patientName_tblRow["patient_mname"]."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{LAST NAME}","<b>".$Consent_patientName_tblRow["patient_lname"]."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{PATIENT GENDER}","<b>".$genderArray[$Consent_patientName_tblRow["sex"]]."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{DOB}","<b>".$Consent_patientNameDob."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{SURGEON NAME}","<b>".$Consent_patientConfirmSurgeon."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{SITE}","<b>".$Consent_patientConfirmSite."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{PROCEDURE}","<b>".$Consent_patientConfirmPrimProc."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{TERTIARY PROCEDURE}","<b>".$Consent_patientConfirmTeriProc."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{DATE}","<b>".date('m-d-Y')."</b>",$consent_form_content_data);
			
			$consent_form_content_data= str_ireplace("{Surgeon's Signature}"," ",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{Nurse's Signature}"," ",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{Anesthesiologist's Signature}"," ",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{Witness Signature}"," ",$consent_form_content_data);
			
			$consent_form_content_data= str_ireplace("{Surgeon's&nbsp;Signature}"," ",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{Nurse's&nbsp;Signature}"," ",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{Anesthesiologist's&nbsp;Signature}"," ",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{Witness&nbsp;Signature}"," ",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{ASC NAME}",$_SESSION['iolink_loginUserFacilityName'],$consent_form_content_data);	
			$consent_form_content_data= str_ireplace("{ASC ADDRESS}",$ascInfoArr[0],$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{ASC PHONE}",$ascInfoArr[1],$consent_form_content_data);			
		//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 
		
	//SAVE SIGNATURE
	
	$text = $_REQUEST['getText'];
	$tablename = "iolink_consent_filled_form";
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	$chkConsentSurgeryQry ="SELECT 
								* 
							FROM 
								`iolink_consent_filled_form` 
							WHERE 
								fldPatientWaitingId = '".$_REQUEST["intPatientWaitingId"]."' 
							AND 
								consent_template_id='".$consentMultipleId."' 
							AND 
								consent_template_id!='0' $andConsentIdQry";
								
	$chkConsentSurgeryRes = @imw_query($chkConsentSurgeryQry) or die($imw_error()); 
	$chkConsentSurgeryNumRow = @imw_num_rows($chkConsentSurgeryRes);
	if($chkConsentSurgeryNumRow>0) {
		$chkFormStatusRow = @imw_fetch_array($chkConsentSurgeryRes);
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		$chk_form_status = $chkFormStatusRow['form_status'];
		$chk_surgery_consent_data = $chkFormStatusRow['surgery_consent_data'];
		//END CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		
		//SET FORM STATUS ACCORDING TO MANDATORY FIELD  
		$chk_signSurgeon1Id = $chkFormStatusRow['signSurgeon1Id'];
		$chk_signNurseId = $chkFormStatusRow['signNurseId'];
		
		$chk_signAnesthesia1Id = $chkFormStatusRow['signAnesthesia1Id'];
		$chk_signWitness1Id = $chkFormStatusRow['signWitness1Id'];
		
		
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
		
		if($_POST["hidd_signAnesthesia1Activate"]=='yes') {
			if($chk_signAnesthesia1Id=='0' || $chk_signAnesthesia1Id=='') {
				$form_status = "not completed";
			}	
		}
		if($_POST["hidd_signWitness1Activate"]=='yes') {
			if($chk_signWitness1Id=='0' || $chk_signWitness1Id=='') {
				$form_status = "not completed";
			}	
		}
		
		//END SET FORM STATUS ACCORDING TO MANDATORY FIELD  
	}else {
		//START CHECK THIS IF RECORD NOT EXIST
		if($_POST["hidd_signSurgeon1Activate"]=='yes' || $_POST["hidd_signNurseActivate"]=='yes' || $_POST["hidd_signAnesthesia1Activate"]=='yes') {
			$form_status = "not completed";
		}
		
		if($_POST["hidd_signWitness1Activate"]=='yes') {
			if($_POST['signWitness1Id'] =='0' || $_POST['signWitness1Id']  =='') {
				$form_status = "not completed";
			}	
		}
		
		//END CHECK THIS IF RECORD NOT EXIST
	}	
	//PURGE
	$show_autoId =$_POST['show_autoId'];
	if($show_autoId!=""){
		$andConsentAutoIdQry = ' AND surgery_consent_id='.$_REQUEST['show_autoId'];
	}else{ /*DO NOTING */ }
	//PURGE
	
	
	//START GET CONSENT CATEGORY-ID
	$getConsentCategoryIdDetails = $objManageData->getRowRecord('consent_forms_template', 'consent_id', $_POST["intConsentTemplateId"]);
	if($getConsentCategoryIdDetails) {
		$consent_category_id = stripslashes($getConsentCategoryIdDetails->consent_category_id);
	}
	//END GET CONSENT CATEGORY-ID
	
	if($chkConsentSurgeryNumRow>0) {
		//if($chk_surgery_consent_data) {
			//DO NOT UPDATE
		//}else {
			//'signWitness1Id','signWitness1FirstName','signWitness1MiddleName','signWitness1LastName','signWitness1Status','signWitness1DateTime'
			$SaveConsentSurgeryQry = "update `iolink_consent_filled_form` set 
										surgery_consent_data 		= '".addslashes($consent_form_content_data)."',
										surgery_consent_sign 		= '".$_POST["consentSurgery_patient_sign"]."', 
										form_status 				= '".$form_status."',
										consent_category_id 		= '".$consent_category_id."',
										surgery_consent_name		= '".addslashes($_POST["surgery_consent_name"])."',
										surgery_consent_alias		= '".addslashes($_POST["surgery_consent_alias"])."',
										sigStatus					= '".$_POST["sigStatus"]."',
										signWitness1Activate		= '".$_POST["hidd_signWitness1Activate"]."',
										signWitness1Id				= '".$_POST["signWitness1Id"]."',
										signWitness1FirstName		= '".$_POST["signWitness1FirstName"]."',
										signWitness1MiddleName		= '".$_POST["signWitness1MiddleName"]."',
										signWitness1LastName		= '".$_POST["signWitness1LastName"]."',
										signWitness1Status			= '".$_POST["signWitness1Status"]."',
										signWitness1DateTime		= '".$_POST["signWitness1DateTime"]."',
										
										signSurgeon1Activate		= '".$_POST["hidd_signSurgeon1Activate"]."',
										signSurgeon1Id				= '".$_POST["signSurgeon1Id"]."',
										signSurgeon1FirstName		= '".$_POST["signSurgeon1FirstName"]."',
										signSurgeon1MiddleName		= '".$_POST["signSurgeon1MiddleName"]."',
										signSurgeon1LastName		= '".$_POST["signSurgeon1LastName"]."',
										signSurgeon1Status			= '".$_POST["signSurgeon1Status"]."',
										signSurgeon1DateTime		= '".$_POST["signSurgeon1DateTime"]."',
										
										
										signAnesthesia1Activate		= '".$_POST["hidd_signAnesthesia1Activate"]."',
										signAnesthesia1Id				= '".$_POST["signAnesthesia1Id"]."',
										signAnesthesia1FirstName		= '".$_POST["signAnesthesia1FirstName"]."',
										signAnesthesia1MiddleName		= '".$_POST["signAnesthesia1MiddleName"]."',
										signAnesthesia1LastName		= '".$_POST["signAnesthesia1LastName"]."',
										signAnesthesia1Status			= '".$_POST["signAnesthesia1Status"]."',
										signAnesthesia1DateTime		= '".$_POST["signAnesthesia1DateTime"]."',
										
										
										signNurseActivate		= '".$_POST["hidd_signNurseActivate"]."',
										signNurseId				= '".$_POST["signNurseId"]."',
										signNurseFirstName		= '".$_POST["signNurseFirstName"]."',
										signNurseMiddleName		= '".$_POST["signNurseMiddleName"]."',
										signNurseLastName		= '".$_POST["signNurseLastName"]."',
										signNurseStatus			= '".$_POST["signNurseStatus"]."',
										signNurseDateTime		= '".$_POST["signNurseDateTime"]."',
										
										patient_id ='".$patient_id."',
										consentSignedStatus ='".$consentSignedStatus."',
										consent_save_date_time='".$_POST['patient_signed_data']."' 								
										WHERE fldPatientWaitingId='".$_REQUEST["intPatientWaitingId"]."'  AND consent_template_id='".$consentMultipleId."'".$andConsentAutoIdQry;
		//}	

		$SaveConsentSurgeryRes = @imw_query($SaveConsentSurgeryQry) or die($SaveConsentSurgeryQry);
	}else {
		$SaveConsentSurgeryQry = "insert into `iolink_consent_filled_form` set 
									surgery_consent_data 		= '".addslashes($consent_form_content_data)."',
									surgery_consent_sign 		= '".$_POST["consentSurgery_patient_sign"]."', 
									form_status 				= '".$form_status."',
									consent_category_id 		= '".$consent_category_id."',
									surgery_consent_name		= '".addslashes($_POST["surgery_consent_name"])."',
									surgery_consent_alias		= '".addslashes($_POST["surgery_consent_alias"])."',
									consent_template_id			= '".$_POST["intConsentTemplateId"]."',
									sigStatus					= '".$_POST["sigStatus"]."',
									
									signWitness1Activate		= '".$_POST["hidd_signWitness1Activate"]."',
									signWitness1Id				= '".$_POST["signWitness1Id"]."',
									signWitness1FirstName		= '".$_POST["signWitness1FirstName"]."',
									signWitness1MiddleName		= '".$_POST["signWitness1MiddleName"]."',
									signWitness1LastName		= '".$_POST["signWitness1LastName"]."',
									signWitness1Status			= '".$_POST["signWitness1Status"]."',
									signWitness1DateTime		= '".$_POST["signWitness1DateTime"]."',	
									
									signSurgeon1Activate		= '".$_POST["hidd_signSurgeon1Activate"]."',
									signSurgeon1Id				= '".$_POST["signSurgeon1Id"]."',
									signSurgeon1FirstName		= '".$_POST["signSurgeon1FirstName"]."',
									signSurgeon1MiddleName		= '".$_POST["signSurgeon1MiddleName"]."',
									signSurgeon1LastName		= '".$_POST["signSurgeon1LastName"]."',
									signSurgeon1Status			= '".$_POST["signSurgeon1Status"]."',
									signSurgeon1DateTime		= '".$_POST["signSurgeon1DateTime"]."',


									signAnesthesia1Activate		= '".$_POST["hidd_signAnesthesia1Activate"]."',
									signAnesthesia1Id				= '".$_POST["signAnesthesia1Id"]."',
									signAnesthesia1FirstName		= '".$_POST["signAnesthesia1FirstName"]."',
									signAnesthesia1MiddleName		= '".$_POST["signAnesthesia1MiddleName"]."',
									signAnesthesia1LastName		= '".$_POST["signAnesthesia1LastName"]."',
									signAnesthesia1Status			= '".$_POST["signAnesthesia1Status"]."',
									signAnesthesia1DateTime		= '".$_POST["signAnesthesia1DateTime"]."',


									signNurseActivate		= '".$_POST["hidd_signNurseActivate"]."',
									signNurseId				= '".$_POST["signNurseId"]."',
									signNurseFirstName		= '".$_POST["signNurseFirstName"]."',
									signNurseMiddleName		= '".$_POST["signNurseMiddleName"]."',
									signNurseLastName		= '".$_POST["signNurseLastName"]."',
									signNurseStatus			= '".$_POST["signNurseStatus"]."',
									signNurseDateTime		= '".$_POST["signNurseDateTime"]."',
									
									fldPatientWaitingId			= '".$_REQUEST["intPatientWaitingId"]."',
									patient_id 					= '".$patient_id."',
									consentSignedStatus 		= '".$consentSignedStatus."',
									consent_save_date_time='".$_POST['patient_signed_data']."'";
		$SaveConsentSurgeryRes = @imw_query($SaveConsentSurgeryQry) or die($SaveConsentSurgeryQry);
	}
	setReSyncroStatus($_REQUEST["intPatientWaitingId"],'consentForm');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
	echo "<script>top.location.href='new_consent_form_page.php?intPatientWaitingId=".$intPatientWaitingId."&patient_id=".$patient_id."&intConsentTemplateId=".$_REQUEST["intConsentTemplateId"]."&consentAllMultipleId=".$consentAllMultipleId."';</script>";
	
}
//END SAVE RECORD IN DATABASE

//VIEW RECORD FROM DATABASE
$blShowSaveButton = false;

$ViewConsentSurgeryQry="SELECT 
							* 
						FROM 
							`iolink_consent_filled_form` 
						WHERE 
							fldPatientWaitingId = '".$_REQUEST["intPatientWaitingId"]."'  
						AND 
							consent_template_id='".$_REQUEST["intConsentTemplateId"]."'";
							
$ViewConsentSurgeryRes = @imw_query($ViewConsentSurgeryQry) or die($imw_error()); 
$ViewConsentSurgeryNumRow = @imw_num_rows($ViewConsentSurgeryRes);
$ViewConsentSurgeryRow = @imw_fetch_array($ViewConsentSurgeryRes); 
	
$consentSurgery_patient_sign = $ViewConsentSurgeryRow["surgery_consent_sign"];
$surgery_consent_data 		= stripslashes($ViewConsentSurgeryRow["surgery_consent_data"]);
$surgery_consent_name 		= $ViewConsentSurgeryRow["surgery_consent_name"];
$surgery_consent_alias 		= stripslashes($ViewConsentSurgeryRow["surgery_consent_alias"]);
$sigStatus 					= $ViewConsentSurgeryRow["sigStatus"];

$signSurgeon1Activate 		= $ViewConsentSurgeryRow["signSurgeon1Activate"];
$signSurgeon1Id 			= $ViewConsentSurgeryRow["signSurgeon1Id"];
$signSurgeon1FirstName 		= $ViewConsentSurgeryRow["signSurgeon1FirstName"];
$signSurgeon1MiddleName 	= $ViewConsentSurgeryRow["signSurgeon1MiddleName"];
$signSurgeon1LastName 		= $ViewConsentSurgeryRow["signSurgeon1LastName"];
$signSurgeon1Status 		= $ViewConsentSurgeryRow["signSurgeon1Status"];

$signNurseActivate 			= $ViewConsentSurgeryRow["signNurseActivate"];
$signNurseId 				= $ViewConsentSurgeryRow["signNurseId"];
$signNurseFirstName 		= $ViewConsentSurgeryRow["signNurseFirstName"];
$signNurseMiddleName 		= $ViewConsentSurgeryRow["signNurseMiddleName"];
$signNurseLastName 			= $ViewConsentSurgeryRow["signNurseLastName"];
$signNurseStatus 			= $ViewConsentSurgeryRow["signNurseStatus"];

$signAnesthesia1Activate 	= $ViewConsentSurgeryRow["signAnesthesia1Activate"];
$signAnesthesia1Id 			= $ViewConsentSurgeryRow["signAnesthesia1Id"];
$signAnesthesia1FirstName 	= $ViewConsentSurgeryRow["signAnesthesia1FirstName"];
$signAnesthesia1MiddleName 	= $ViewConsentSurgeryRow["signAnesthesia1MiddleName"];
$signAnesthesia1LastName 	= $ViewConsentSurgeryRow["signAnesthesia1LastName"];
$signAnesthesia1Status 		= $ViewConsentSurgeryRow["signAnesthesia1Status"];

$signWitness1Activate 		= $ViewConsentSurgeryRow["signWitness1Activate"];
$signWitness1Id 			= $ViewConsentSurgeryRow["signWitness1Id"];
$signWitness1FirstName 		= $ViewConsentSurgeryRow["signWitness1FirstName"];
$signWitness1MiddleName 	= $ViewConsentSurgeryRow["signWitness1MiddleName"];
$signWitness1LastName 		= $ViewConsentSurgeryRow["signWitness1LastName"];
$signWitness1Status 		= $ViewConsentSurgeryRow["signWitness1Status"];
$signWitness1DateTime 		= $ViewConsentSurgeryRow["signWitness1DateTime"];

$form_status = $ViewConsentSurgeryRow["form_status"];
	
	
//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE	
if(trim($surgery_consent_data)=="") {
	$blShowSaveButton = true;
	$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = '".$_REQUEST["intConsentTemplateId"]."'";
	$ViewConsentTemplateRes = @imw_query($ViewConsentTemplateQry) or die(imw_error()); 
	$ViewConsentTemplateNumRow = @imw_num_rows($ViewConsentTemplateRes);
	$ViewConsentTemplateRow = @imw_fetch_array($ViewConsentTemplateRes); 
		
	$surgery_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
	$surgery_consent_name = $ViewConsentTemplateRow["consent_name"];
	$surgery_consent_alias = stripslashes($ViewConsentTemplateRow["consent_alias"]);
}
//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE		

//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 
$surgery_consent_data= str_ireplace("&#39;","'",$surgery_consent_data);			
$surgery_consent_data= str_ireplace("{PATIENT ID}","<b>".$Consent_patientName_tblRow["patient_id"]."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{PATIENT FIRST NAME}","<b>".$Consent_patientName_tblRow["patient_fname"]."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{MIDDLE INITIAL}","<b>".$Consent_patientName_tblRow["patient_mname"]."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{LAST NAME}","<b>".$Consent_patientName_tblRow["patient_lname"]."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{PATIENT GENDER}","<b>".$genderArray[$Consent_patientName_tblRow["sex"]]."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{DOB}","<b>".$Consent_patientNameDob."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{SURGEON NAME}","<b>".$Consent_patientConfirmSurgeon."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{SITE}","<b>".$Consent_patientConfirmSite."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{PROCEDURE}","<b>".$Consent_patientConfirmPrimProc."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{TERTIARY PROCEDURE}","<b>".$Consent_patientConfirmTeriProc."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{DATE}","<b>".date('m-d-Y')."</b>",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{ASC NAME}",$_SESSION['iolink_loginUserFacilityName'],$surgery_consent_data);	
$surgery_consent_data= str_ireplace("{ASC ADDRESS}",$ascInfoArr[0],$surgery_consent_data);
$surgery_consent_data= str_ireplace("{ASC PHONE}",$ascInfoArr[1],$surgery_consent_data);

/*
$surgery_consent_data = str_ireplace('{TEXTBOX_XSMALL}',"<input type='text' name='xsmall' size='1' maxlength='1'>",$surgery_consent_data);
$surgery_consent_data = str_ireplace('{TEXTBOX_SMALL}',"<input type='text' name='small' size='30' maxlength='30'>",$surgery_consent_data);
$surgery_consent_data = str_ireplace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$surgery_consent_data);
$surgery_consent_data = str_ireplace('{TEXTBOX_LARGE}',"<textarea name='large' cols='80' rows='2'></textarea>",$surgery_consent_data);
*/
$surgery_consent_data = str_ireplace('{TEXTBOX_XSMALL}',"{TEXTBOX_XSMALL}",$surgery_consent_data);
$surgery_consent_data = str_ireplace('{TEXTBOX_SMALL}',"{TEXTBOX_SMALL}",$surgery_consent_data);
$surgery_consent_data = str_ireplace('{TEXTBOX_MEDIUM}',"{TEXTBOX_MEDIUM}",$surgery_consent_data);
$surgery_consent_data = str_ireplace('{TEXTBOX_LARGE}',"{TEXTBOX_LARGE}",$surgery_consent_data);

preg_match_all("/{TEXTBOX_XSMALL}/", $surgery_consent_data, $TEXTBOX_XSMALL_matches);
for($xsi = 1; $xsi <= count($TEXTBOX_XSMALL_matches[0]); $xsi++){
	$surgery_consent_data = preg_replace('/{TEXTBOX_XSMALL}/',"<input type='text' name='xsmall".$xsi."' size='1' maxlength='1'>",$surgery_consent_data, 1);
}
preg_match_all("/{TEXTBOX_SMALL}/", $surgery_consent_data, $TEXTBOX_SMALL_matches);
for($xsi = 1; $xsi <= count($TEXTBOX_SMALL_matches[0]); $xsi++){
	$surgery_consent_data = preg_replace('/{TEXTBOX_SMALL}/',"<input type='text' name='small".$xsi."' size='30' maxlength='30'>",$surgery_consent_data, 1);
}preg_match_all("/{TEXTBOX_MEDIUM}/", $surgery_consent_data, $TEXTBOX_MEDIUM_matches);
for($xsi = 1; $xsi <= count($TEXTBOX_MEDIUM_matches[0]); $xsi++){
	$surgery_consent_data = preg_replace('/{TEXTBOX_MEDIUM}/',"<input type='text' name='medium".$xsi."' size='60' maxlength='60'>",$surgery_consent_data, 1);
}preg_match_all("/{TEXTBOX_LARGE}/", $surgery_consent_data, $TEXTBOX_LARGE_matches);
for($xsi = 1; $xsi <= count($TEXTBOX_LARGE_matches[0]); $xsi++){
	$surgery_consent_data = preg_replace('/{TEXTBOX_LARGE}/',"<textarea name='large".$xsi."' cols='80' rows='2'></textarea>",$surgery_consent_data, 1);
}
		
//CODE TO ACTIVATE,DEACTIVATE SURGEON'S SIGNATURE (AND REPLACE VARIABLES)
//START MAKE VALUE IN {} AS CASE SENSITIVE
$surgery_consent_data= str_ireplace("{Surgeon's Signature}","{Surgeon's Signature}",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{Surgeon's&nbsp;Signature}","{Surgeon's&nbsp;Signature}",$surgery_consent_data);
//END MAKE VALUE IN {} AS CASE SENSITIVE
$chkSignSurgeon1Var = stristr($surgery_consent_data,"{Surgeon's Signature}");
$chkSignSurgeon1VarNew = stristr($surgery_consent_data,"{Surgeon's&nbsp;Signature}");
		
$chkSignSurgeon1Activate='';
if($chkSignSurgeon1Var || $chkSignSurgeon1VarNew) {
	$chkSignSurgeon1Activate = 'yes';
}
$surgery_consent_data= str_ireplace("{Surgeon's Signature}"," ",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{Surgeon's&nbsp;Signature}"," ",$surgery_consent_data);
//END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
		
//CODE TO ACTIVATE,DEACTIVATE NURSE'S SIGNATURE (AND REPLACE VARIABLES)
//START MAKE VALUE IN {} AS CASE SENSITIVE
$surgery_consent_data= str_ireplace("{Nurse's Signature}","{Nurse's Signature}",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{Nurse's&nbsp;Signature}","{Nurse's&nbsp;Signature}",$surgery_consent_data);
//END MAKE VALUE IN {} AS CASE SENSITIVE	
$chkSignNurseVar = stristr($surgery_consent_data,"{Nurse's Signature}");
$chkSignNurseVarNew = stristr($surgery_consent_data,"{Nurse's&nbsp;Signature}");
$chkSignNurseActivate='';
if($chkSignNurseVar || $chkSignNurseVarNew) {
	$chkSignNurseActivate = 'yes';
}
$surgery_consent_data= str_ireplace("{Nurse's Signature}"," ",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{Nurse's&nbsp;Signature}"," ",$surgery_consent_data);
//END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
		
//CODE TO ACTIVATE,DEACTIVATE Anesthesiologist's SIGNATURE (AND REPLACE VARIABLES)
//START MAKE VALUE IN {} AS CASE SENSITIVE	
$surgery_consent_data= str_ireplace("{Anesthesiologist's Signature}","{Anesthesiologist's Signature}",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{Anesthesiologist's&nbsp;Signature}","{Anesthesiologist's&nbsp;Signature}",$surgery_consent_data);
//END MAKE VALUE IN {} AS CASE SENSITIVE
$chkSignAnesthesia1Var = stristr($surgery_consent_data,"{Anesthesiologist's Signature}");
$chkSignAnesthesia1VarNew = stristr($surgery_consent_data,"{Anesthesiologist's&nbsp;Signature}");
$chkSignAnesthesia1Activate='';
if($chkSignAnesthesia1Var || $chkSignAnesthesia1VarNew) {
	$chkSignAnesthesia1Activate = 'yes';
}
$surgery_consent_data= str_ireplace("{Anesthesiologist's Signature}"," ",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{Anesthesiologist's&nbsp;Signature}"," ",$surgery_consent_data);
//END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
		
//CODE TO ACTIVATE,DEACTIVATE Anesthesiologist's SIGNATURE (AND REPLACE VARIABLES)
//START MAKE VALUE IN {} AS CASE SENSITIVE
$surgery_consent_data= str_ireplace("{Witness Signature}","{Witness Signature}",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{Witness&nbsp;Signature}","{Witness&nbsp;Signature}",$surgery_consent_data);
//END MAKE VALUE IN {} AS CASE SENSITIVE
$chkSignWitness1Var = stristr($surgery_consent_data,"{Witness Signature}");
$chkSignWitness1VarNew = stristr($surgery_consent_data,"{Witness&nbsp;Signature}");

$chkSignWitness1Activate='';
if($chkSignWitness1Var || $chkSignWitness1VarNew) {
	$chkSignWitness1Activate = 'yes';
}
$surgery_consent_data= str_ireplace("{Witness Signature}"," ",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{Witness&nbsp;Signature}"," ",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{SIGNATURE}","{SIGNATURE}",$surgery_consent_data);
$surgery_consent_data= str_ireplace("{ASSISTANT_SURGEON_SIGNATURE}","{SIGNATURE}",$surgery_consent_data);

//END CODE TO ACTIVATE,DEACTIVATE Witness's AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
	
//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 
	
//START SIGNATURE CODE
$row_arr = explode('{START APPLET ROW}',$surgery_consent_data);
$sig_arr = explode('{SIGNATURE}',$row_arr[0]);
$ds=1;
$sig_data = '';
for($s=1;$s<count($sig_arr);$s++,$ds++){
	
	$sig_data = '
			<table  class="alignLeft" style="border:none;"  id="consentSigIpadId'.$ds.'" >
				<tr>';
	if($browserPlatform == "iPad") {
			$sig_data .= '<td style="width:320px;height:90px;" class="consentObjectBeforSign" >   
								<img style="cursor:pointer; float:right; margin-top:50px;" src="images/pen.png" id="SigPen'.$ds.'" onclick="OnSignIpadPhy(\''.$patient_id.'\',\''.$intPatientWaitingId.'\',\'ptConsent\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\',\''.$consentMultipleId.'\')">
						  </td>';
	}else {
			$sig_data .= '
					<td width="145" style="border:solid 1px" bordercolor="#FF9900" class="consentObjectBeforSign" id="tdObject'.$ds.'">   
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
					<td valign="bottom" id="Sign_icon_'.$ds.'">
						<img style="cursor:pointer;" src="images/pen.png" id="SignBtn'.$ds.'"  onClick="OnSign'.$ds.'();"><br>
						<img style="cursor:pointer;" title="Touch Signature" src="images/touch.svg" id="SignBtnTouch'.$ds.'" name="SignBtnTouch'.$ds.'" onclick="OnSignIpadPhy(\''.$patient_id.'\',\''.$intPatientWaitingId.'\',\'ptConsent\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\',\''.$consentMultipleId.'\')"><br>
						<img style="cursor:pointer;" src="images/eraser.gif" id="button'.$ds.'" alt="Clear Sign"  onClick="OnClear'.$ds.'();">
					</td>
				
		';
	}
	$sig_data .= '</tr>
			</table>';
	$str_data = $sig_arr[$s];
	$sig_arr[$s] = $sig_data;
	$sig_arr[$s] .= $str_data;
	$hiddenFields[] = true;
}
	
$surgery_consent_data = implode(' ',$sig_arr);
$content_row = '';
for($ro=1;$ro<count($row_arr);$ro++){
	if($row_arr[$ro]){
		$sig_arr1 = explode('{SIGNATURE}',$row_arr[$ro]);
		$td_sign = '';

		for($t=0;$t<count($sig_arr1)-1;$t++,$ds++){
			$sig_arr1[$t] = str_ireplace('&nbsp;','',$sig_arr1[$t]);
			$td_sign .= '
				<td align="left" >
					<table border="0">
						<tr><td>'.$sig_arr1[$t].'</td></tr>
						<tr>
							<td style="border:solid 1px" bordercolor="#FF9900" class="consentObjectBeforSign" id="tdObject'.$ds.'">
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
							<td valign="bottom" id="Sign_icon_'.$ds.'">
								<img style="cursor:pointer;" src="images/pen.png" id="SignBtn'.$ds.'" name="SignBtn'.$ds.'" onclick="OnSign'.$ds.'();"><br>
								<img style="cursor:pointer;" title="Touch Signature" src="images/touch.svg" id="SignBtnTouch'.$ds.'" name="SignBtnTouch'.$ds.'" onclick="OnSignIpadPhy(\''.$patient_id.'\',\''.$intPatientWaitingId.'\',\'ptConsent\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\')"><br>
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
			<table width="145" border="1" align="center">
				<tr>
					'.$td_sign.'						
				</tr>
			</table>
		';
	}
}	
$jh = 1;
	
$surgery_consent_data .= $content_row;
//--- get all content of consent forms -------	

$consent_content = '
	<table id="content_'.$consent_form_id.'" style="display:'.$display.'" width="100%" align="left" cellpadding="1" cellspacing="1" border="0">
		<tr>
			<td colspan="'.count($sig_arr).'">'.$surgery_consent_data.'</td>
		<tr>
	</table>
';
//echo $consent_content;	



//UPLOAD SCANNED IMAGE FROM iolink_scan_consent TABLE
	$ViewIolinkScanRecordQry = "SELECT 
									scan_consent_id,
									scan1Upload 
								FROM 
									`iolink_scan_consent` 
								WHERE 
									patient_in_waiting_id = '".$_REQUEST["intPatientWaitingId"]."' 
								AND 
									consent_template_id='".$consentMultipleId."' 
								AND 
									consent_template_id!='0'";
									
	$ViewIolinkScanRecordRes 	= imw_query($ViewIolinkScanRecordQry) or die(imw_error()); 
	$ViewIolinkScanRecordNumRow = imw_num_rows($ViewIolinkScanRecordRes);
	if($ViewIolinkScanRecordNumRow>0) {
		$ViewIolinkScanRecordRow = imw_fetch_array($ViewIolinkScanRecordRes); 
		$scan_consent_id = $ViewIolinkScanRecordRow["scan_consent_id"];
		$scan1Upload = $ViewIolinkScanRecordRow["scan1Upload"];
	}	
//UPLOAD SCANNED IMAGE FROM iolink_scan_consent TABLE

?>
<html>
<head>
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<script src="js/epost.js"></script>
</head>
<body> 
<script>
function showImgWindow(strImgNumber) {
	if(!strImgNumber) {
		strImgNumber = '';
	}
	var opRoomId = '<?php echo $operatingRoomRecordsId;?>';
	MM_openBrOpRoomWindow('opRoomImagePopUp.php?from=op_room_record&id='+opRoomId+'&imgNmbr='+strImgNumber,'OpRoomImage','scrollbars=yes,width=900,height=400,resizable=yes,location=yes,status=yes');
}

/*
function saveConsentForm(){
	var flag=true;
	var newfun  = SetSig();
	if(!newfun) {
		flag=false;
	}
	
	if(flag==true) {
		document.frm_consent_multiple.submit();
	}	
}
*/
//Applet
function get_App_Coords(objElem){
	var coords,appName;
	var objElemSign = document.frm_consent_multiple.consentSurgery_patient_sign;
	appName = objElem;
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
	var coords = document.applets["app_ConsentSurgerySignature"].getSign();
	return coords;
}
function getclear_os(objNewElem){
	document.applets["app_ConsentSurgerySignature"].clearIt();
	changeColorThis(255,0,0);
	get_App_Coords(objNewElem);
}
function changeColorThis(r,g,b){				
	document.applets['app_ConsentSurgerySignature'].setDrawColor(r,g,b);								
}
//Applet
	function changeSliderColor(){
		top.changeColor('#BCD2B0');
	}
//	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>','<?php echo $purge_status;?>');
</script>

<script LANGUAGE="Javascript">

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

function iolink_displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {
	
	var loggedInUserFName = '<?php echo $loggedInUserFName;?>';
	var loggedInUserMName = '<?php echo $loggedInUserMName;?>';
	var loggedInUserLName = '<?php echo $loggedInUserLName;?>';
	var signOnFileStatus  = 'Yes';
	var signUserDateTime  = '<?php echo $signDateTime;?>';
	var arr_hidden_fields = new Array();
	var arr_sig_values = new Array();
	arr_sig_values = [loggedInUserId,loggedInUserFName,loggedInUserMName,loggedInUserLName,signOnFileStatus,signUserDateTime]; 
	var l;
	if(userIdentity == 'Witness1') { arr_hidden_fields = ['signWitness1Id','signWitness1FirstName','signWitness1MiddleName','signWitness1LastName','signWitness1Status','signWitness1DateTime']; }
	if(userIdentity == 'Surgeon1') { arr_hidden_fields = ['signSurgeon1Id','signSurgeon1FirstName','signSurgeon1MiddleName','signSurgeon1LastName','signSurgeon1Status','signSurgeon1DateTime']; }
	if(userIdentity == 'Anesthesia1') { arr_hidden_fields = ['signAnesthesia1Id','signAnesthesia1FirstName','signAnesthesia1MiddleName','signAnesthesia1LastName','signAnesthesia1Status','signAnesthesia1DateTime']; }
	if(userIdentity == 'Nurse1') { arr_hidden_fields = ['signNurseId','signNurseFirstName','signNurseMiddleName','signNurseLastName','signNurseStatus','signNurseDateTime']; }
	
	l=arr_hidden_fields.length;
	if(delSign) {
		document.getElementById(TDUserNameId).style.display = 'block';
		document.getElementById(TDUserSignatureId).style.display = 'none';
		for(var i=0;i<l;i++){
			if(document.getElementById(arr_hidden_fields[i])) {
				document.getElementById(arr_hidden_fields[i]).value = '';	
			}
		}
	}else {
		document.getElementById(TDUserNameId).style.display = 'none';
		document.getElementById(TDUserSignatureId).style.display = 'block';
		for(var i=0;i<l;i++){
			if(document.getElementById(arr_hidden_fields[i])) {
				document.getElementById(arr_hidden_fields[i]).value = arr_sig_values[i];	
			}
		}
	}
}
//Display Signature Of USER
//CONFIRM BOX WITH YES NO BUTTON
function confirmOtherSurgeon(str){
	execScript('n = msgbox("'+str+'","4116","Please Confirm")', "vbscript");
	return(n == 6);
}
//CONFIRM BOX WITH YES NO BUTTON
		
function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,loggedInUserName,userIdentity,delSign) {
		//START TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			var signCheck='true';
			var assignedSurgeonId = '<?php echo $consentAssignedSurgeonId;?>';
			var assignedSurgeonName = '<?php echo $consentAssignedSurgeonName;?>';
			var loggedInUserType = '<?php echo $loggedInUserType;?>';
			if(loggedInUserId!=assignedSurgeonId && !delSign && loggedInUserType=='Surgeon') {
				var rCheck = confirmOtherSurgeon("This patient is registered to Dr. "+assignedSurgeonName+"\t\t\t\tAre you sure you want to sign the Chart notes of this patient");
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
		var intPatientWaitingId1 = '<?php echo $_REQUEST["intPatientWaitingId"];?>';
		var consentMultipleId1 = '<?php echo $_REQUEST["intConsentTemplateId"];?>';
		var hiddPurgestatus1 = '<?php echo $_REQUEST["hiddPurgestatus"];?>';
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
		url=url+"&userIdentity="+userIdentity
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&intPatientWaitingId="+intPatientWaitingId1
		url=url+"&consentMultipleId="+consentMultipleId1
		url=url+"&hiddPurgestatus="+hiddPurgestatus1
		if(delSign) {
			url=url+"&delSign=yes"
		}
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
//End Display Signature Of USER
		

//SIGNATURE CODE
	var chkAtleastOneSign = false; //NOT SIGNED ATLEAST ONE
	function chkAtleastOne() {
		var cnt_hid_field = '<?php echo count($hiddenFields)?>';
		var mn;
		chkAtleastOneSign =true; //ACC. TO NEW REQUIREMENT, NO SIGNATUER CHECK REQUIRED AND ALLOW FORM TO SAVE
		return chkAtleastOneSign;
	}	
	
	<?php
	$jh = 1;
	$saveStr .= 'var signNum = ""; ';
	for($h=0;$h<count($hiddenFields);$h++,$jh++){
		$str = '';
		for($s=0;$s<count($hiddenFields);$s++){
			if($s+1 != $jh){
				//$str .= 'document.getElementById("SigPlus'.($s+1).'").TabletState = 0;';
				$str .= 'if(document.getElementById("SigPlus'.($s+1).'")){
							document.getElementById("SigPlus'.($s+1).'").TabletState = 0;
						 }';
				
			}
		}
		print '
			function OnSign'.$jh.'(){
				if(document.getElementById("SigPlus'.$jh.'")){
					'.$str.'
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
					
					if(signNum) {
						signNum = signNum+",'.$jh.'";
					}else {
						signNum = '.$jh.';
					}
					//alert("Please sign '.$jh.' to continue"); 
					//return false;
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
</script>

	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	 <form name="frm_consent_multiple" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="consentFormDetails.php?saveRecord=true<?php echo $saveLink.$alreadySaveMessage;?>&SaveForm_alert=true&save_printRecord=true&intPatientWaitingId=<?php echo $_REQUEST['intPatientWaitingId'];?>&intConsentTemplateId=<?php echo $_REQUEST['intConsentTemplateId'];?>" >
		<?php
			for($h=0;$h<count($hiddenFields);$h++){
			?>
			<input type="hidden" name="sigData<?php print($h+1) ?>" id="sigData<?php print($h+1) ?>" value="">
			<input type="hidden" name="hiddSigIpadId<?php echo($h+1); ?>" id="hiddSigIpadId<?php echo($h+1); ?>" value="">
			<?php
			}
		?>
			<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
			<input type="hidden" name="Save_PrintForm" id="Save_PrintForm" value="yes">
			<input type="hidden" name="formIdentity" id="formIdentity" value="">
			<input type="hidden" name="getText" id="getText">
			<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>"/>	
			<input type="hidden" name="frmAction" id="frmAction" value="consentFormDetails.php">		
			<input type="hidden" name="intConsentTemplateId" id="intConsentTemplateId" value="<?php echo $intConsentTemplateId;?>"/>	
			<input type="hidden" name="surgery_consent_name" id="surgery_consent_name" value="<?php echo $surgery_consent_name;?>"/>	
			<input type="hidden" name="surgery_consent_alias" id="surgery_consent_alias" value="<?php echo $surgery_consent_alias;?>"/>	
			<input type="hidden" name="show_td" id="show_td" value="<?php print $consentMultipleId; ?>" >
			
			<input type="hidden" name="intPatientWaitingId" id="intPatientWaitingId" value="<?php echo $intPatientWaitingId;?>">
			<input type="hidden" name="intConsentTemplateId" id="intConsentTemplateId" value="<?php echo $consentMultipleId;?>">
			<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id;?>">
			<input type="hidden" name="consentAllMultipleId" id="consentAllMultipleId" value="<?php echo $consentAllMultipleId;?>">

			<input type="hidden" name="sig_count" id="sig_count" value="<?php print count($hiddenFields); ?>" >
			<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
			<!-- PURGE -->
			<input type="hidden" name="hiddform_status" id="hiddform_status" value="<?php echo $form_status?>">
			<input type="hidden" name="hiddConsentPurgeStatus" id="hiddConsentPurgeStatus" value="">
			<input type="hidden" name="hiddPurgestatus" id="hiddPurgestatus" value="<?php echo $purge_status;?>">
			<input type="hidden" name="consentMultipleAutoIncrId" id="consentMultipleAutoIncrId" value="<?php echo $consentMultipleAutoIncrId;?>">
			<input type="hidden" name="show_autoId" id="show_autoId" value="<?php echo $consentMultipleAutoIncrId;?>">
			
			<!-- PURGE -->
 
 <!--patient consent date----->           
            <input type="hidden" name="patient_signed_data" id="patient_signed_data"  value="<?php echo $timestamp = date('Y-m-d H:i:s');?>">
  <!--patient consent date----->
             
            <!-- WITNESS SIGNATURE -->
            <input type="hidden" name="signWitness1Id" id="signWitness1Id" value="<?php echo $signWitness1Id;?>">	
            <input type="hidden" name="signWitness1FirstName" id="signWitness1FirstName" value="<?php echo $signWitness1FirstName;?>">	
            <input type="hidden" name="signWitness1MiddleName" id="signWitness1MiddleName" value="<?php echo $signWitness1MiddleName;?>">	
            <input type="hidden" name="signWitness1LastName" id="signWitness1LastName" value="<?php echo $signWitness1LastName;?>">	
            <input type="hidden" name="signWitness1Status" id="signWitness1Status" value="<?php echo $signWitness1Status;?>">	
            <input type="hidden" name="signWitness1DateTime" id="signWitness1DateTime" value="<?php echo $signWitness1DateTime;?>">	
            <!-- WITNESS SIGNATURE -->
            
            
            <!-- SURGEON SIGNATURE -->
            <input type="hidden" name="signSurgeon1Id" id="signSurgeon1Id" value="<?php echo $signSurgeon1Id;?>">	
            <input type="hidden" name="signSurgeon1FirstName" id="signSurgeon1FirstName" value="<?php echo $signSurgeon1FirstName;?>">	
            <input type="hidden" name="signSurgeon1MiddleName" id="signSurgeon1MiddleName" value="<?php echo $signSurgeon1MiddleName;?>">	
            <input type="hidden" name="signSurgeon1LastName" id="signSurgeon1LastName" value="<?php echo $signSurgeon1LastName;?>">	
            <input type="hidden" name="signSurgeon1Status" id="signSurgeon1Status" value="<?php echo $signSurgeon1Status;?>">	
            <input type="hidden" name="signSurgeon1DateTime" id="signSurgeon1DateTime" value="<?php echo $signSurgeon1DateTime;?>">	
            <!-- SURGEON SIGNATURE -->
            
            <!-- NURSE SIGNATURE -->
            <input type="hidden" name="signNurseId" id="signNurseId" value="<?php echo $signNurse1Id;?>">	
            <input type="hidden" name="signNurseFirstName" id="signNurseFirstName" value="<?php echo $signNurseFirstName;?>">	
            <input type="hidden" name="signNurseMiddleName" id="signNurseMiddleName" value="<?php echo $signNurseMiddleName;?>">	
            <input type="hidden" name="signNurseLastName" id="signNurseLastName" value="<?php echo $signNurseLastName;?>">	
            <input type="hidden" name="signNurseStatus" id="signNurseStatus" value="<?php echo $signNurseStatus;?>">	
            <input type="hidden" name="signNurseDateTime" id="signNurseDateTime" value="<?php echo $signNurseDateTime;?>">	
            <!-- NURSE SIGNATURE -->
            
            
            <!-- ANESTHESIA SIGNATURE -->
            <input type="hidden" name="signAnesthesia1Id" id="signAnesthesia1Id" value="<?php echo $signAnesthesia1Id;?>">	
            <input type="hidden" name="signAnesthesia1FirstName" id="signAnesthesia1FirstName" value="<?php echo $signAnesthesia1FirstName;?>">	
            <input type="hidden" name="signAnesthesia1MiddleName" id="signAnesthesia1MiddleName" value="<?php echo $signAnesthesia1MiddleName;?>">	
            <input type="hidden" name="signAnesthesia1LastName" id="signAnesthesia1LastName" value="<?php echo $signAnesthesia1LastName;?>">	
            <input type="hidden" name="signAnesthesia1Status" id="signAnesthesia1Status" value="<?php echo $signAnesthesia1Status;?>">	
            <input type="hidden" name="signAnesthesia1DateTime" id="signAnesthesia1DateTime" value="<?php echo $signAnesthesia1DateTime;?>">	
            <!-- ANESTHESIA SIGNATURE -->
            
            
            
            
			<?php
			//$cntSigImage =  substr_count($consent_content,'<img src="SigPlus_images/sign_');
			$cntSigImage =  substr_count($consent_content,'<img src="html2pdfnew/sign_');
			
			?>
			<input type="hidden" name="hiddCntSigImageId" id="hiddCntSigImageId" value="<?php echo $cntSigImage;?>">
			<tr>
				<td><img src="images/tpixel.gif" width="1" height="2"></td>
			</tr>
			<tr>
				<td  valign="top" align="center">
					<table width="30%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="3" align="right"><img src="images/left.gif" width="3" height="24"></td>
							<td nowrap valign="middle" bgcolor="#BCD2B0" align="center" class="text_10b" ><?php echo stripslashes($surgery_consent_name);?><!-- Consent for Surgery & Anesthesia --></td>
							<td align="left" valign="top" width="3"><img src="images/right.gif" width="3" height="24"></td>
							<td>&nbsp;</td><td nowrap id="epostDelId"> <?php while($row = @imw_fetch_array($rsNotes)) { if($totalRows_rsNotes > 0) { ?> <img src="images/sticky_note.gif" onMouseOver="showEpost('<?php echo $row['epost_id'];?>','<?php echo $intPatientWaitingId;?>')">  <?php } } ?></td>
						</tr>
				  </table>
				</td>
			</tr>
			<tr>
			   <td><img src="images/tpixel.gif" width="4" height="1"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:350px; padding-top:25px;">
					<div id="divSaveAlert" style="position:absolute; left:350; top:200; display:none;">
						<?php 
							$bgCol = '#BCD2B0';
							$borderCol = '#BCD2B0';
							include('saveDivPopUp.php'); 
						?>
					</div>
				</td>
			</tr>
			<tr  valign="top" height="300">
			  <td bgcolor="#ECF1EA" align="left">
				
				<table width="99%"  align="center" border="0" cellpadding="0" cellspacing="0" class="all_border">
					<tr height="22" bgcolor="#D1E0C9">
						<td width="1%"></td>
						<td class="text_10b" colspan="2">Patient Consent</td>
					</tr>
					<?php //PURGE
					if($purge_status=="true"){ /* ?>
						<tr>
							<td align="center" colspan="2" style="font-weight:bold; font-size:24px; color:#FF0000; ">Consent Form Purged</td>	
						</tr>		
					<?php 	
					*/
					} else{  ?>				
					
						<tr height="10" bgcolor="#FFFFFF">
							<td width="1%"></td>
							<td colspan="2" class="text_10b"></td>
						</tr>
					<?php
					} //PURGE ?>	
					<tr height="300" bgcolor="#FFFFFF">
						<td width="1%"></td>
						<td height="22" width="95%" class="text_10" valign="top">
							<?php
								if($img_content){
									?>
									<img style="cursor:pointer;" src="admin/logoImg.php?from=Consent&scan_upload_id=<?php echo $scan_upload_id; ?>">
									<?php
								}else{
									 echo $consent_content;
								}
								?>
						</td>
						<td width="2%"></td>
					</tr>
					<tr height="22" bgcolor="#FFFFFF">
						<td width="1%"></td>
						<td colspan="2" class="text_10b"></td>
					</tr>
				</table>
				
			 </td>	
		   </tr> 
		   
		   <tr>
				<td> <img src="images/tpixel.gif" width="1" height="3"></td>
		   </tr>
		   <?php
		
		if($signSurgeon1Activate=='yes' || $chkSignSurgeon1Activate=='yes' || $signNurseActivate=='yes' || $chkSignNurseActivate=='yes') {
		?>		 
		 <tr>
			<td width="99%"  valign="top" align="center">
				<table cellpadding="0"  cellspacing="0" rules="none"  border="1" bgcolor="#F1F4F0" bordercolor="#D1E0C9"   width="99%">
					<tr bgcolor="#FFFFFF" height="6"><td colspan="9"></td></tr>
						<tr height="22">
							<td width="2%"></td>
							<td class="text_10b" nowrap width="48%">
		 					
							<?php
								if($signSurgeon1Activate=='yes' || $chkSignSurgeon1Activate=='yes') {
								
									
								//CODE RELATED TO SURGEON SIGNATURE ON FILE
									if($loggedInUserType<>"Surgeon") {
										$loginUserName = $_SESSION['iolink_loginUserName'];
										$callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
									}else {
										$loginUserId = $_SESSION["iolink_loginUserId"];
										//$callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
										$callJavaFunSurgeon = "document.frm_consent_multiple.hiddSignatureId.value='TDsurgeon1SignatureId'; return iolink_displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','','$loginUserId','Surgeon1');";
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
									
									if($signSurgeon1Id && $_SESSION["iolink_loginUserId"]<>$signSurgeon1Id) {
										$callJavaFunSurgeonDel = "alert('Only $Surgeon1Name can remove this signature');";
									}else {
										$callJavaFunSurgeonDel = "document.frm_consent_multiple.hiddSignatureId.value='TDsurgeon1NameId'; return iolink_displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','','$loginUserId','Surgeon1','delSign');";
									}
								//END CODE RELATED TO SURGEON SIGNATURE ON FILE
							 
							 ?>
										
										<div id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>; " >
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td nowrap width="20%" align="left" class="text_10b" valign="middle" style="cursor:pointer;<?php echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunSurgeon;?>">
														Surgeon Signature
													</td><td>&nbsp;</td>
												</tr>
											</table>
										</div>
										<div id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>; ">	
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td nowrap align="left" class="text_10" valign="middle" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>">
														<?php echo "<b>Surgeon:</b>". " Dr. ". $Surgeon1Name; ?><img src="images/tpixel.gif" width="7" height="1" />
													</td>
													
												</tr>
												<tr>
													<td nowrap align="left" class="text_10" valign="middle">
														<b>Signature On File :&nbsp;</b>
														<?php echo $surgeon1SignOnFileStatus;?>
													</td>
												</tr>
											</table>
										</div>
										<input type="hidden" name="hidd_signSurgeon1Activate" id="hidd_signSurgeon1Activate" value="yes">
							<?php
								}
						 ?>
							</td>
							<td width="48%" class="text_10" colspan="1" align="right">
							<?php
								if($signNurseActivate=='yes' || $chkSignNurseActivate=='yes') {
							
									//CODE RELATED TO NURSE SIGNATURE ON FILE
										if($loggedInUserType<>"Nurse") {
											$loginUserName = $_SESSION['iolink_loginUserName'];
											$callJavaFun = "return noAuthorityFunCommon('Nurse');";
										}else {
											$loginUserId = $_SESSION["iolink_loginUserId"];
											//$callJavaFun = "return noAuthorityFunCommon('Nurse');";
											$callJavaFun = "document.frm_consent_multiple.hiddSignatureId.value='TDnurseSignatureId'; return iolink_displaySignature('TDnurseNameId','TDnurseSignatureId','','$loginUserId','Nurse1');";
										}
									
										$signOnFileStatus = "Yes";
										$TDnurseNameIdDisplay = "block";
										$TDnurseSignatureIdDisplay = "none";
										$NurseNameShow = $loggedInUserName;
										
										if($signNurseId<>0 && $signNurseId<>"") {
											$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
											$signOnFileStatus = $signNurseStatus;	
											
											$TDnurseNameIdDisplay = "none";
											$TDnurseSignatureIdDisplay = "block";
										}
										
										if($signNurseId && $_SESSION["iolink_loginUserId"]<>$signNurseId) {
											$callJavaFunSurgeonDel = "alert('Only $NurseNameShow can remove this signature');";
										}else {
											$callJavaFunDel = "document.frm_consent_multiple.hiddSignatureId.value='TDnurseNameId'; return iolink_displaySignature('TDnurseNameId','TDnurseSignatureId','','$loginUserId','Nurse1','delSign');";
										}
									//END CODE RELATED TO NURSE SIGNATURE ON FILE
							
							?>
									
									<div id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>; " >
										<table border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td class="text_10b" nowrap  style="cursor:pointer;<?php echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</td>
											</tr>
										</table>
									</div>
									<div id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>; ">	
										<table border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td class="text_10" nowrap style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"><?php echo "<b>Nurse:</b>"." ".$NurseNameShow; ?></td>
											</tr>
											<tr>
												<td class="text_10" nowrap>
													<b>Signature On File :&nbsp;</b>
													<?php echo $signOnFileStatus;?>
												</td>
											</tr>
										</table>
									</div>
									<input type="hidden" name="hidd_signNurseActivate" id="hidd_signNurseActivate" value="yes">	
									
							<?php
								}
							?>
							
							</td>
							<td width="2%"></td>
					</tr> 
					<tr bgcolor="#FFFFFF" height="6"><td colspan="9"></td></tr>
				 </table>		 
			</td>
		 </tr>
		 <?php
		 }
		 if($signAnesthesia1Activate=='yes' || $chkSignAnesthesia1Activate=='yes' || $signWitness1Activate=='yes' || $chkSignWitness1Activate=='yes') {
			
		?>		 
		 <tr>
			<td width="99%"  valign="top" align="center">
				<table cellpadding="0"  cellspacing="0" rules="none"  border="1" bgcolor="#F1F4F0" bordercolor="#D1E0C9"   width="99%">
					<tr bgcolor="#FFFFFF" height="6"><td colspan="9"></td></tr>
						<tr height="22">
							<td width="2%"></td>
							<td class="text_10b" nowrap width="48%">
		 					
							<?php
								if($signAnesthesia1Activate=='yes' || $chkSignAnesthesia1Activate=='yes') {
								
									
								//CODE RELATED TO Anesthesiologist SIGNATURE ON FILE
									if($loggedInUserType<>"Anesthesiologist") {
										$loginUserName = $_SESSION['iolink_loginUserName'];
										$callJavaFunAnesthesia = "return noAuthorityFunCommon('Anesthesiologist');";
									}else {
										$loginUserId = $_SESSION["iolink_loginUserId"];
										//$callJavaFunAnesthesia = "return noAuthorityFunCommon('Anesthesiologist');";
										$callJavaFunAnesthesia = "document.frm_consent_multiple.hiddSignatureId.value='TDanesthesia1SignatureId'; return iolink_displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','','$loginUserId','Anesthesia1');";
									}					
									$anesthesia1SignOnFileStatus = "Yes";
									$TDanesthesia1NameIdDisplay = "block";
									$TDanesthesia1SignatureIdDisplay = "none";
									$Anesthesia1Name = $loggedInUserName;
									$Anesthesia1SubType = $logInUserSubType;
									$Anesthesia1PreFix = 'Dr.';

									if($signAnesthesia1Id<>0 && $signAnesthesia1Id<>"") {
										$Anesthesia1Name = $signAnesthesia1LastName.", ".$signAnesthesia1FirstName." ".$signAnesthesia1MiddleName;
										$anesthesia1SignOnFileStatus = $signAnesthesia1Status;	
										
										$TDanesthesia1NameIdDisplay = "none";
										$TDanesthesia1SignatureIdDisplay = "block";
										$Anesthesia1SubType = getUserSubTypeFun($signAnesthesia1Id); //FROM common/commonFunctions.php
									}
									if($Anesthesia1SubType=='CRNA') {
										$Anesthesia1PreFix = '';
									}
									
									//CODE TO REMOVE ANES 1 SIGNATURE
									if($signAnesthesia1Id && $_SESSION["iolink_loginUserId"]<>$signAnesthesia1Id) {
										$callJavaFunAnesthesiaDel = "alert('Only $Anesthesia1PreFix $Anesthesia1Name can remove this signature');";
									}else {
										$callJavaFunAnesthesiaDel = "document.frm_consent_multiple.hiddSignatureId.value='TDanesthesia1NameId'; return iolink_displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','','$loginUserId','Anesthesia1','delSign');";
									}
									//END CODE TO REMOVE ANES 1 SIGNATURE	
									
								//END CODE RELATED TO ANESTHESIA SIGNATURE ON FILE
							 
							 ?>
										
										<div id="TDanesthesia1NameId" style="display:<?php echo $TDanesthesia1NameIdDisplay;?>; " >
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td nowrap width="20%" align="left" class="text_10b" valign="middle" style="cursor:pointer;<?php echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunAnesthesia;?>">
														Anesthesiologist Signature
													</td><td>&nbsp;</td>
												</tr>
											</table>
										</div>
										<div id="TDanesthesia1SignatureId" style="display:<?php echo $TDanesthesia1SignatureIdDisplay;?>; ">	
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td nowrap align="left" class="text_10" valign="middle" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunAnesthesiaDel;?>">
														<?php echo "<b>Anesthesiologist:</b>". " $Anesthesia1PreFix ". $Anesthesia1Name; ?><img src="images/tpixel.gif" width="7" height="1" />
													</td>
													
												</tr>
												<tr>
													<td nowrap align="left" class="text_10" valign="middle">
														<b>Signature On File :&nbsp;</b>
														<?php echo $anesthesia1SignOnFileStatus;?>
													</td>
												</tr>
											</table>
										</div>
										<input type="hidden" name="hidd_signAnesthesia1Activate" id="hidd_signAnesthesia1Activate" value="yes">
							<?php
								}
						 ?>
							</td>
							<td width="48%" class="text_10" colspan="1" align="right">
							<?php
								if($signWitness1Activate=='yes' || $chkSignWitness1Activate=='yes') {
									//GET USER DETAIL(FOR WITNESS SIGNATURE)
										if($signWitness1Id) {
											$ViewWitnessUserNameQry = "select * from `users` where  usersId = '".$signWitness1Id."'";
											$ViewWitnessUserNameRes = @imw_query($ViewWitnessUserNameQry) or die(imw_error()); 
											$ViewWitnessUserNameRow = @imw_fetch_array($ViewWitnessUserNameRes); 
											$witnessUserType 		= $ViewWitnessUserNameRow["user_type"];
										}
									//END GET USER DETAIL(FOR WITNESS SIGNATURE)
									
									//CODE RELATED TO NURSE SIGNATURE ON FILE
										if($witnessUserType && ($loggedInUserType<>$witnessUserType)) {
											$loginUserName = $_SESSION['iolink_loginUserName'];
											$callJavaFunWitness = "return noAuthorityFunCommon('".$witnessUserType."');";
										}else {
											$loginUserId = $_SESSION["iolink_loginUserId"];
											//$callJavaFunWitness = "return noAuthorityFunCommon('".$witnessUserType."');";
											$callJavaFunWitness = "document.frm_consent_multiple.hiddSignatureId.value='TDwitness1SignatureId'; return iolink_displaySignature('TDwitness1NameId','TDwitness1SignatureId','','$loginUserId','Witness1');";
										}
										
										$signOnFileWitness1Status = "Yes";
										$TDwitness1NameIdDisplay = "block";
										$TDwitness1SignatureIdDisplay = "none";
										$Witness1NameShow = $loggedInUserName;
										
										if($signWitness1Id<>0 && $signWitness1Id<>"") {
											$Witness1NameShow = $signWitness1LastName.", ".$signWitness1FirstName." ".$signWitness1MiddleName;
											$signOnFileWitness1Status = $signWitness1Status;	
											
											$TDwitness1NameIdDisplay = "none";
											$TDwitness1SignatureIdDisplay = "block";
										}
										if($signWitness1Id && $_SESSION["iolink_loginUserId"]<>$signWitness1Id) {
											$callJavaFunWitnessDel = "alert('Only $Witness1NameShow can remove this signature');";
										}else {
											$callJavaFunWitnessDel = "document.frm_consent_multiple.hiddSignatureId.value='TDwitness1NameId'; return iolink_displaySignature('TDwitness1NameId','TDwitness1SignatureId','','$loginUserId','Witness1','delSign');";	
										}
										
									//END CODE RELATED TO NURSE SIGNATURE ON FILE
								
								
							?>
									
									<div id="TDwitness1NameId" style="display:<?php echo $TDwitness1NameIdDisplay;?>; " >
										<table border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td class="text_10b" nowrap  style="cursor:pointer;<?php echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunWitness;?>">Witness Signature</td>
											</tr>
										</table>
									</div>
									<div id="TDwitness1SignatureId" style="display:<?php echo $TDwitness1SignatureIdDisplay;?>; ">	
										<table border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td class="text_10" nowrap style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitnessDel;?>"><?php echo "<b>Witness:</b>"." ".$Witness1NameShow; ?></td>
											</tr>
											<tr>
												<td class="text_10" nowrap>
													<b>Signature On File :&nbsp;</b>
													<?php echo $signOnFileWitness1Status;?>
												</td>
											</tr>
										</table>
									</div>
									<input type="hidden" name="hidd_signWitness1Activate" id="hidd_signWitness1Activate" value="yes">	
							<?php
								}
							?>
							
							</td>
							<td width="2%"></td>
					</tr> 
					<tr bgcolor="#FFFFFF" height="6"><td colspan="9"></td></tr>
				 </table>		 
			</td>
		 </tr>
		 <?php
		 }
		 ?>
		 <tr>
				<td> <img src="images/tpixel.gif" width="1" height="15"></td>
		 </tr>
		 <?php
		 if($scan1Upload) {
		 /*?>
		 <tr>
			<td width="99%"  valign="top" align="center">
				<table cellpadding="0"  cellspacing="0" rules="none"  border="1" bgcolor="#F1F4F0" bordercolor="#D1E0C9"   width="99%">
					<tr bgcolor="#FFFFFF" height="6"><td colspan="9"></td></tr>
					<tr height="22">
						<td width="2%"></td>
						<td class="text_10b" nowrap width="48%">
								<img style="cursor:pointer; " id="imgThumbNail" border="0" height="100" width="150" src="admin/logoImg.php?from=iolink_scan_consent&id=<?php echo $scan_consent_id; ?>" onClick="showImgWindow();">&nbsp;&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
		*/
		}?>
			<tr height="10" bgcolor="#FFFFFF">
				<td width="1%"></td>
				<td class="text_10b"></td>
			</tr>
							 
	</form>
</table>
<script>
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
</script>
<?php
if($blShowSaveButton == true){
	?>
	<script>
		if(top.document.getElementById("anchorShow")) {
			top.document.getElementById("anchorShow").style.display = 'block';
		}
		if(top.document.getElementById("deleteSelected")) {
			top.document.getElementById("deleteSelected").style.display = 'none';
		}
		if(top.document.getElementById("PrintBtn")) {
			top.document.getElementById("PrintBtn").style.display = 'none';
		}
		if(top.document.getElementById("iolinkUploadBtn")) {
			top.document.getElementById("iolinkUploadBtn").style.display = 'none';
		}
		if(top.document.getElementById("multiUploadImgBtn")) {
			top.document.getElementById("multiUploadImgBtn").style.display = 'none';
		}
	</script>	
	<?php
}else{
	?>
	<script>
		if(top.document.getElementById("anchorShow")) {
			top.document.getElementById("anchorShow").style.display = 'none';
		}
	</script>
	<?php
}
?>
</body>