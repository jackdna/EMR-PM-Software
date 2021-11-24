<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
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
<?php
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
require_once("common/user_agent.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
	
$consentMultipleId =$_REQUEST['consentMultipleId'];
$tablename = "consent_multiple_form";
//$signatureDate = date("m-d-Y h:i A");
$signatureDate = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
//PURGE
$consentMultipleAutoIncrId = $_REQUEST['consentMultipleAutoIncrId'];
$hiddPurgestatus = $_REQUEST['hiddPurgestatus'];
if($_REQUEST['consentMultipleAutoIncrId']){
	$andConsentIdQry = ' AND surgery_consent_id='.$_REQUEST['consentMultipleAutoIncrId'];
	$ampConsentAutoId = '&amp;consentMultipleAutoIncrId='.$_REQUEST['consentMultipleAutoIncrId'];
}else{
	//DO NOTHING
}
//PURGE
$spec = "
</head>
<body style=\"overflow-x:hidden; overflow-y:scroll;\"  onClick=\"document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();\">
";
include("common/link_new_file.php");


$pagename=explode("/", $_SERVER['REQUEST_URI']);
$page=explode("?",$pagename[2]);
$pagename=$page[0];
extract($_GET);

$SaveForm_alert = $_REQUEST['SaveForm_alert'];
//PURGE
$hiddConsentPurgeStatus = $_REQUEST['hiddConsentPurgeStatus'];
//PURGE
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
	
	$fieldName = "consent_multiple_form";
	$pageName = "consent_multiple_form.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId&amp;consentMultipleId=$consentMultipleId$andConsentIdQry";
	
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON 
		$pageName = "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId&amp;consentMultipleId=$consentMultipleId";
	}
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 

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

//GET PATIENT DETAIL
	$genderArray = array("m"=>"Male","f"=>"Female");
	$Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_REQUEST['patient_id']."'";
	$Consent_patientName_tblRes = imw_query($Consent_patientName_tblQry) or die(imw_error());
	$Consent_patientName_tblRow = imw_fetch_array($Consent_patientName_tblRes);
	$Consent_patientName = $Consent_patientName_tblRow["patient_lname"].", ".$Consent_patientName_tblRow["patient_fname"]." ".$Consent_patientName_tblRow["patient_mname"];
	
	
	$Consent_patientNameDobTemp = $Consent_patientName_tblRow["date_of_birth"];
		$Consent_patientNameDob_split = explode("-",$Consent_patientNameDobTemp);
		$Consent_patientNameDob = $Consent_patientNameDob_split[1]."-".$Consent_patientNameDob_split[2]."-".$Consent_patientNameDob_split[0];
	

	$Consent_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$Consent_patientConfirm_tblRes = imw_query($Consent_patientConfirm_tblQry) or die(imw_error());
	$Consent_patientConfirm_tblRow = imw_fetch_array($Consent_patientConfirm_tblRes);
	$finalizeStatus = $Consent_patientConfirm_tblRow["finalize_status"];
	$Consent_patientConfirmDosTemp = $Consent_patientConfirm_tblRow["dos"];
		$Consent_patientConfirmDos_split = explode("-",$Consent_patientConfirmDosTemp);
		$Consent_patientConfirmDos = $Consent_patientConfirmDos_split[1]."-".$Consent_patientConfirmDos_split[2]."-".$Consent_patientConfirmDos_split[0];

	//START GET ARRIVAL TIME
	$stubWaitingDetailArr = $objManageData->getStubWaitingDetail($_REQUEST["pConfId"],$Consent_patientConfirm_tblRow["dos"])	;
	$arrivalTime = ($stubWaitingDetailArr[0]) ? $stubWaitingDetailArr[0] : '';	
	//END GET ARRIVAL TIME

	$Consent_patientConfirmSurgeon = $Consent_patientConfirm_tblRow["surgeon_name"];
	$Consent_patientConfirmSiteTemp = $Consent_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($Consent_patientConfirmSiteTemp == 1) {
			$Consent_patientConfirmSite = "Left Eye";  //OD
		}else if($Consent_patientConfirmSiteTemp == 2) {
			$Consent_patientConfirmSite = "Right Eye";  //OS
		}else if($Consent_patientConfirmSiteTemp == 3) {
			$Consent_patientConfirmSite = "Both Eye";  //OU
		}else if($Consent_patientConfirmSiteTemp == 4) {
			$Consent_patientConfirmSite = "Left Upper Lid";  //OU
		}else if($Consent_patientConfirmSiteTemp == 5) {
			$Consent_patientConfirmSite = "Left Lower Lid";  //OU
		}else if($Consent_patientConfirmSiteTemp == 6) {
			$Consent_patientConfirmSite = "Right Upper Lid";  //OU
		}else if($Consent_patientConfirmSiteTemp == 7) {
			$Consent_patientConfirmSite = "Right Lower Lid";  //OU
		}else if($Consent_patientConfirmSiteTemp == 8) {
			$Consent_patientConfirmSite = "Bilateral Upper Lid";  //OU
		}else if($Consent_patientConfirmSiteTemp == 9) {
			$Consent_patientConfirmSite = "Bilateral Lower Lid";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$Consent_patientConfirmPrimProc = $Consent_patientConfirm_tblRow["patient_primary_procedure"];
	$Consent_patientConfirmSecProc 	= $Consent_patientConfirm_tblRow["patient_secondary_procedure"];
	$Consent_patientConfirmTeriProc	= $Consent_patientConfirm_tblRow["patient_tertiary_procedure"];

	//GET ASSIGNED SURGEON ID AND SURGEON NAME AND SURGEON TYPE
		$consentAssignedSurgeonId 	= $Consent_patientConfirm_tblRow["surgeonId"];
		$consentAssignedSurgeonName = stripslashes($Consent_patientConfirm_tblRow["surgeon_name"]);
	//END GET ASSIGNED SURGEON ID AND SURGEON NAME AND SURGEON TYPE
	
	$Consent_patientConfirmAnes_NA 	= $Consent_patientConfirm_tblRow["anes_NA"];
	$signAnesthesiaIdBackColor=$chngBckGroundColor;
	if($Consent_patientConfirmAnes_NA=='Yes') {
		$signAnesthesiaIdBackColor=$whiteBckGroundColor; 
	}
	
	//START GET ASSIGNED ANES NAME
	$Consent_patientConfirmAnes = "";
	$anesthesiologist_id_confirm = $Consent_patientConfirm_tblRow["anesthesiologist_id"];
	if($anesthesiologist_id_confirm) {
		$Consent_patientConfirmAnes = $objManageData->getUserName($anesthesiologist_id_confirm,'Anesthesiologist');	
	}
	//END GET ASSIGNED ANES NAME
	
//END GET PATIENT DETAIL

//PURGE
$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;ascId='.$ascId.'&amp;consentMultipleId='.$consentMultipleId.$ampConsentAutoId;

if(($_POST['SaveRecordForm']=='yes')&&($hiddConsentPurgeStatus=="yes")){
		$SaveConsentSurgeryQry = "update `consent_multiple_form` set 
									consent_purge_status ='true'
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'  AND consent_template_id='".$consentMultipleId."'".$andConsentIdQry;
		$SaveConsentSurgeryRes = imw_query($SaveConsentSurgeryQry) or die(imw_error());

		$UpdateConsentEpostQry = "update `eposted` set 
									epost_consent_purge_status ='true'
									WHERE patient_conf_id ='".$_REQUEST["pConfId"]."'  AND consent_template_id='".$consentMultipleId."' AND consentAutoIncId='".$_REQUEST['consentMultipleAutoIncrId']."'";
		$UpdateConsentEpostRes = imw_query($UpdateConsentEpostQry) or die(imw_error());
		//INSERT A BLANK NEW ENTRY FOR SAME CONSENT FORM

		$consentnewQry = "select consent_category_id from `consent_forms_template` where consent_id='".$consentMultipleId."'";
		$consentnewRes = imw_query($consentnewQry) or die(imw_error()); 
		$consentnewRow = imw_fetch_array($consentnewRes);
		$consent_new_cat_id=$consentnewRow['consent_category_id'];		
		
		$blankInsertPurgeQry = "insert into consent_multiple_form  set 
							confirmation_id = '".$_REQUEST['pConfId']."',
							consent_category_id ='$consent_new_cat_id',
							consent_template_id = '$consentMultipleId',
							left_navi_status='true',
							form_status= ''";
		$blankInsertRes = imw_query($blankInsertPurgeQry) or die(imw_error());

		//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
			$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
			$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
		//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
		
		//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
			$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
			$updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateAnesStubTblRes = imw_query($updateAnesStubTblQry) or die(imw_error());
		//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
		
		//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
			$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
			$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
		//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
	
	//REFRESH SLIDER
			echo "<script>top.frames[0].location='blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId&amp;consentMultipleId=$consentMultipleId'</script>";

}
/****************PURGE*****************/

//SAVE RECORD IN DATABASE
if($_POST['SaveRecordForm']=='yes' && $hiddConsentPurgeStatus==""){
	$sig_count = $_POST['sig_count'];
	$show_td = $_POST['show_td'];
	//START
	$getConsentSurgeryQry = "select * from `consent_multiple_form` where  confirmation_id = '".$_REQUEST["pConfId"]."' AND consent_template_id='".$show_td."' AND consent_template_id!='0' AND surgery_consent_data!=' '".$andConsentIdQry;
	$getConsentSurgeryRes = imw_query($getConsentSurgeryQry) or die(imw_error()); 
	$getConsentSurgeryNumRow = imw_num_rows($getConsentSurgeryRes);
	if($getConsentSurgeryNumRow>0) { 
		$getConsentSurgeryRow = imw_fetch_array($getConsentSurgeryRes);
		
		$consent_form_content_data = stripslashes($getConsentSurgeryRow['surgery_consent_data']);
		$modifyFormStatus = $getConsentSurgeryRow['form_status'];
	}else {	
	
		$getConsentDataAdminDetails = $objManageData->getRowRecord('consent_forms_template', 'consent_id', $show_td);
		if($getConsentDataAdminDetails) {
			$consent_form_content_data = stripslashes($getConsentDataAdminDetails->consent_data);
		}
	}
	//END
	
	//START MAKE VALUE IN {} AS CASE SENSITIVE	
	$consent_form_content_data= str_ireplace("{ASSISTANT_SURGEON_SIGNATURE}","{SIGNATURE}",$consent_form_content_data);
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
							$consent_form_content_data = str_ireplace("\n","",$consent_form_content_data);
							//$consent_form_content_data = str_ireplace("  ","",$consent_form_content_data);
							$consent_form_content_data = preg_replace('/<textarea rows="2" cols="80" name="'.$name.$c.'">(.*?)<\/textarea>/','<textarea rows="2" cols="80" name="'.$name.$c.'"> '.$_POST[$name.$c].' </textarea>',$consent_form_content_data);
							$c++;
						}
					}
					
					
				}
				/*
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
					
				}
				*/
						
			}
		}	
	//MODIFY TEXTBOXES AFTER SAVED ATLEAST ONCE	
	
	$form_status = "completed";//BY DEFAULT VALUE
	//SAVE SIGNATURE
			for($ps=1;$ps<=$sig_count;$ps++){
				$postData = $_POST['sigData'.$ps];
				if($postData!='' && $postData!= 'undefined'  && $postData != '000000000000000000000000000000000000000000000000000000000000000000000000') {
					$path =  realpath(dirname(__FILE__).'/SigPlus_images').'/sign_'.$_REQUEST["pConfId"].'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
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
					//--- Save Array Fields --------
					$sig_data['signature_content'] = $postData;
					$sig_data['consent_template_id'] = $show_td;
					$sig_data['confirmation_id'] = $_REQUEST["pConfId"];
					$sig_data['signature_count'] = $ps;
					$sig_data['signature_image_path'] = addslashes($path);
					
					$chk_sig_data_qry = "select consent_form_signature_id from consent_form_signature 
							where confirmation_id = '".$_REQUEST["pConfId"]."'
							and consent_template_id = '$show_td'
							and signature_count = ".$ps."";
					$chk_sig_data_res = imw_query($chk_sig_data_qry);
					$chk_sig_data_num_row = imw_num_rows($chk_sig_data_res);
					
					if($chk_sig_data_num_row>0) {	
						//DO NOTHING
					}
					else{
						$sig_insert_id = $objManageData->addRecords($sig_data, 'consent_form_signature');
					}
				}	
			}
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
				}else if(!$imgSrc) {
					$consent_form_content_data .= '{SIGNATURE}';
					$form_status = "not completed";	//IF PATIENT DOES NOT SIGNED THE CHART
				}else if($imgSrc=='{SIGNATURE}') {
					$consent_form_content_data .= '{SIGNATURE}';
					$form_status = "not completed";	//IF PATIENT DOES NOT SIGNED THE CHART
				}else {
					$consent_form_content_data .= '<img src="SigPlus_images/'.$imgSrc.'" width="150" height="83">'.$sigDtTmSave;
				}
				$consent_form_content_data .= $row_arr[$c];
			}
			
		//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
			$consent_form_content_data= str_ireplace("{PATIENT ID}","<b>".$Consent_patientName_tblRow["patient_id"]."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{PATIENT FIRST NAME}",$Consent_patientName_tblRow["patient_fname"],$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{MIDDLE INITIAL}",$Consent_patientName_tblRow["patient_mname"],$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{LAST NAME}",$Consent_patientName_tblRow["patient_lname"],$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{DOB}","<b>".$Consent_patientNameDob."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{PATIENT GENDER}","<b>".$genderArray[$Consent_patientName_tblRow["sex"]]."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{SURGEON NAME}","<b>".$Consent_patientConfirmSurgeon."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{ARRIVAL TIME}","<b>".$arrivalTime."</b>",$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{ANES NAME}","<b>".$Consent_patientConfirmAnes."</b>",$consent_form_content_data);
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
			$consent_form_content_data= str_ireplace("{ASC NAME}",$_SESSION['loginUserFacilityName'],$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{ASC ADDRESS}",$ascInfoArr[0],$consent_form_content_data);
			$consent_form_content_data= str_ireplace("{ASC PHONE}",$ascInfoArr[1],$consent_form_content_data);
		//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 
		
	//SAVE SIGNATURE
	
	$text = $_REQUEST['getText'];
	$tablename = "consent_multiple_form";
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	$chkConsentSurgeryQry = "select * from `consent_multiple_form` where  confirmation_id = '".$_REQUEST["pConfId"]."' AND consent_template_id='".$consentMultipleId."' AND consent_template_id!='0' $andConsentIdQry";
	$chkConsentSurgeryRes = imw_query($chkConsentSurgeryQry) or die($imw_error()); 
	$chkConsentSurgeryNumRow = imw_num_rows($chkConsentSurgeryRes);
	if($chkConsentSurgeryNumRow>0) {
		$chkFormStatusRow = imw_fetch_array($chkConsentSurgeryRes);
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
			if(($chk_signAnesthesia1Id=='0' || $chk_signAnesthesia1Id=='') && $Consent_patientConfirmAnes_NA!='Yes') {
				$form_status = "not completed";
			}	
		}
		if($_POST["hidd_signWitness1Activate"]=='yes') {
			if($chk_signWitness1Id=='0' || $chk_signWitness1Id=='') {
				$form_status = "not completed";
			}	
		}
		
		//END SET FORM STATUS ACCORDING TO MANDATORY FIELD  
	}	
	//PURGE
	$show_autoId =$_POST['show_autoId'];
	if($show_autoId!=""){
		$andConsentAutoIdQry = ' AND surgery_consent_id='.$_REQUEST['show_autoId'];
	}else{ /*DO NOTING */ }
	//PURGE
	
	if($chkConsentSurgeryNumRow>0) {
		//if($chk_surgery_consent_data) {
			//DO NOT UPDATE
		//}else {
			$SaveConsentSurgeryQry = "update `consent_multiple_form` set 
										surgery_consent_data = '".addslashes($consent_form_content_data)."',
										surgery_consent_sign = '".$_POST["consentSurgery_patient_sign"]."', 
										form_status ='".$form_status."',
										surgery_consent_name='".addslashes($_POST["surgery_consent_name"])."',
										surgery_consent_alias='".addslashes($_POST["surgery_consent_alias"])."',
										sigStatus='".$_POST["sigStatus"]."',
										signSurgeon1Activate='".$_POST["hidd_signSurgeon1Activate"]."',
										signNurseActivate='".$_POST["hidd_signNurseActivate"]."',
										signAnesthesia1Activate='".$_POST["hidd_signAnesthesia1Activate"]."',
										signWitness1Activate='".$_POST["hidd_signWitness1Activate"]."'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'  AND consent_template_id='".$consentMultipleId."'".$andConsentAutoIdQry;
		//}	

		$SaveConsentSurgeryRes = imw_query($SaveConsentSurgeryQry) or die($SaveConsentSurgeryQry);
	}else {
		$SaveConsentSurgeryQry = "insert into `consent_multiple_form` set 
									surgery_consent_data = '".$_POST["surgery_consent_data"]."',
									surgery_consent_sign = '".$_POST["consentSurgery_patient_sign"]."', 
									form_status ='".$form_status."',
									surgery_consent_name='".addslashes($_POST["surgery_consent_name"])."',
									surgery_consent_alias='".addslashes($_POST["surgery_consent_alias"])."',
									consent_template_id='".$_POST["consentMultipleId"]."',
									sigStatus='".$_POST["sigStatus"]."',
									signSurgeon1Activate='".$_POST["hidd_signSurgeon1Activate"]."',
									signNurseActivate='".$_POST["hidd_signNurseActivate"]."',
									signAnesthesia1Activate='".$_POST["hidd_signAnesthesia1Activate"]."',
									signWitness1Activate='".$_POST["hidd_signWitness1Activate"]."',
									confirmation_id='".$_REQUEST["pConfId"]."'";
		$SaveConsentSurgeryRes = imw_query($SaveConsentSurgeryQry) or die($SaveConsentSurgeryQry);
	}
	
	//SAVE ENTRY IN chartnotes_change_audit_tbl 
		
		$chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
									user_id='".$_SESSION['loginUserId']."' AND
									patient_id='".$_REQUEST["patient_id"]."' AND
									confirmation_id='".$_REQUEST["pConfId"]."' AND
									form_name='".$fieldName."' AND
									consent_template_id='".$_POST["consentMultipleId"]."' AND
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
										consent_template_id='".$_POST["consentMultipleId"]."',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}else {
			$SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',
										patient_id='".$_REQUEST["patient_id"]."',
										confirmation_id='".$_REQUEST["pConfId"]."',
										form_name='$fieldName',
										status='created',
										consent_template_id='".$_POST["consentMultipleId"]."',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}					
		$SaveAuditChartNotesRes = imw_query($SaveAuditChartNotesQry) or die(imw_error());
	//END SAVE ENTRY IN chartnotes_change_audit_tbl
	
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
	
	//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
		$updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateAnesStubTblRes = imw_query($updateAnesStubTblQry) or die(imw_error());
	//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
	
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."','consent');</script>";
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
	
}
//END SAVE RECORD IN DATABASE

//VIEW RECORD FROM DATABASE
	$ViewConsentSurgeryQry = "select * from `consent_multiple_form` where  confirmation_id = '".$_REQUEST["pConfId"]."'  AND consent_template_id='".$consentMultipleId."' AND consent_template_id!='0'".$andConsentIdQry;
	$ViewConsentSurgeryRes = imw_query($ViewConsentSurgeryQry) or die($imw_error()); 
	$ViewConsentSurgeryNumRow = imw_num_rows($ViewConsentSurgeryRes);
	$ViewConsentSurgeryRow = imw_fetch_array($ViewConsentSurgeryRes); 
	
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
	$signSurgeon1SignDate 		= $ViewConsentSurgeryRow["signSurgeon1DateTime"];
	
	
	$signNurseActivate 			= $ViewConsentSurgeryRow["signNurseActivate"];
	$signNurseId 				= $ViewConsentSurgeryRow["signNurseId"];
	$signNurseFirstName 		= $ViewConsentSurgeryRow["signNurseFirstName"];
	$signNurseMiddleName 		= $ViewConsentSurgeryRow["signNurseMiddleName"];
	$signNurseLastName 			= $ViewConsentSurgeryRow["signNurseLastName"];
	$signNurseStatus 			= $ViewConsentSurgeryRow["signNurseStatus"];
	$signNurseSignDate 			= $ViewConsentSurgeryRow["signNurseDateTime"];
	
	$signAnesthesia1Activate 	= $ViewConsentSurgeryRow["signAnesthesia1Activate"];
	$signAnesthesia1Id 			= $ViewConsentSurgeryRow["signAnesthesia1Id"];
	$signAnesthesia1FirstName 	= $ViewConsentSurgeryRow["signAnesthesia1FirstName"];
	$signAnesthesia1MiddleName 	= $ViewConsentSurgeryRow["signAnesthesia1MiddleName"];
	$signAnesthesia1LastName 	= $ViewConsentSurgeryRow["signAnesthesia1LastName"];
	$signAnesthesia1Status 		= $ViewConsentSurgeryRow["signAnesthesia1Status"];
	$signAnesthesia1SignDate 	= $ViewConsentSurgeryRow["signAnesthesia1DateTime"];
	
	$signWitness1Activate 		= $ViewConsentSurgeryRow["signWitness1Activate"];
	$signWitness1Id 			= $ViewConsentSurgeryRow["signWitness1Id"];
	$signWitness1FirstName 		= $ViewConsentSurgeryRow["signWitness1FirstName"];
	$signWitness1MiddleName 	= $ViewConsentSurgeryRow["signWitness1MiddleName"];
	$signWitness1LastName 		= $ViewConsentSurgeryRow["signWitness1LastName"];
	$signWitness1Status 		= $ViewConsentSurgeryRow["signWitness1Status"];
	$signWitness1SignDate 		= $ViewConsentSurgeryRow["signWitness1DateTime"];
	
	$form_status = $ViewConsentSurgeryRow["form_status"];
	//PURGE
	$purge_status = $ViewConsentSurgeryRow["consent_purge_status"];
	//PURGE
	$saveLink = $saveLink."&amp;form_status=".$form_status;
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE	
		if(trim($surgery_consent_data)=="") {
			$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = '".$consentMultipleId."'";
			$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
			$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
			$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes); 
				
			$surgery_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
			$surgery_consent_name = $ViewConsentTemplateRow["consent_name"];
			$surgery_consent_alias = stripslashes($ViewConsentTemplateRow["consent_alias"]);
		}
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE		

	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
		$surgery_consent_data= str_ireplace("&#39;","'",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{PATIENT ID}","<b>".$Consent_patientName_tblRow["patient_id"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{PATIENT FIRST NAME}",$Consent_patientName_tblRow["patient_fname"],$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{MIDDLE INITIAL}",$Consent_patientName_tblRow["patient_mname"],$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{LAST NAME}",$Consent_patientName_tblRow["patient_lname"],$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{DOB}","<b>".$Consent_patientNameDob."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{PATIENT GENDER}","<b>".$genderArray[$Consent_patientName_tblRow["sex"]]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{SURGEON NAME}","<b>".$Consent_patientConfirmSurgeon."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{ARRIVAL TIME}","<b>".$arrivalTime."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{ANES NAME}","<b>".$Consent_patientConfirmAnes."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{SITE}","<b>".$Consent_patientConfirmSite."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{PROCEDURE}","<b>".$Consent_patientConfirmPrimProc."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{TERTIARY PROCEDURE}","<b>".$Consent_patientConfirmTeriProc."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{DATE}","<b>".date('m-d-Y')."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{ASC NAME}",$_SESSION['loginUserFacilityName'],$surgery_consent_data);
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
		$sig_data = '<table class="alignLeft" style="border:none;" id="consentSigIpadId'.$ds.'"><tr>';
		if($browserPlatform == "iPad") {
			$sig_data .= '<td style="width:320px;height:90px;" class="consentObjectBeforSign" >   
								<img style="cursor:pointer; float:right; margin-top:50px;" src="images/pen.png" id="SigPen'.$ds.'" onclick="OnSignIpadPhy(\''.$patient_id.'\',\''.$pConfId.'\',\'ptConsent\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\')">
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
								<img style="cursor:pointer;" src="images/pen.png" id="SignBtn'.$ds.'" onclick="OnSign'.$ds.'();"><br>
								<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="images/touch.svg" id="SignBtnTouch'.$ds.'" name="SignBtnTouch'.$ds.'" onclick="clearAllTabletState();OnSignIpadPhy(\''.$patient_id.'\',\''.$pConfId.'\',\'ptConsent\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\')"><br> 
								<img style="cursor:pointer;" src="images/eraser.gif" id="button'.$ds.'" alt="Clear Sign"  onclick="OnClear'.$ds.'();">
						  </td>
							';
		}
		$sig_data .='</tr></table>';
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
					<td class="alignLeft">
						<table style="border:none">
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
									<img style="cursor:pointer;" src="images/pen.png" id="SignBtn'.$ds.'" onclick="OnSign'.$ds.'();"><br>
									<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="images/touch.svg" id="SignBtnTouch'.$ds.'" name="SignBtnTouch'.$ds.'" onclick="clearAllTabletState();OnSignIpadPhy(\''.$patient_id.'\',\''.$pConfId.'\',\'ptConsent\',\'consentSigIpadId'.$ds.'\',\''.$ds.'\')"><br> 
									<img style="cursor:pointer;" src="images/eraser.gif" id="button'.$ds.'" alt="Clear Sign" onclick="OnClear'.$ds.'();">											
								</td>
							</tr>
						</table>
					</td>	
				';
				$s++;
				$hiddenFields[] = true;
			}
			$content_row .= '
				<table class="alignCenter" style=" width:145px; border:1px; border-style:solid;">
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
		<table id="content_'.$consent_form_id.'" class="table_separate alignLeft" style="display:'.$display.'; width:99%;" >
			<tr>
				<td colspan="'.count($sig_arr).'">'.$surgery_consent_data.'</td>
			<tr>
		</table>
	';

	//END SIGNATURE CODE
		
//END VIEW RECORD FROM DATABASE
?>
<script src="js/epost.js"></script>
<script>
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
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>','<?php echo $purge_status;?>');


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
</script>


<script>
	//CONFIRM BOX WITH YES NO BUTTON
	function confirmOtherSurgeon(str){
		execScript('n = msgbox("'+str+'","4116","Please Confirm")', "vbscript");
		return(n == 6);
	}
	//CONFIRM BOX WITH YES NO BUTTON
		
//Display Signature Of USER USING VBSCRIPT
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {
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
		
			//SET HIDDEN FIELD (hidd_signWitness1Done) TO BLANK IF SIGNATURE REMOVED
			if(userIdentity=='Witness1'){
				if(document.getElementById('hidd_signWitness1Done')) {
					document.getElementById('hidd_signWitness1Done').value = '';
				}
				
			}		
			//END SET HIDDEN FIELD (hidd_signWitness1Done) TO BLANK IF SIGNATURE REMOVED
			
		}else {
			if(signCheck=='true') {	
				document.getElementById(TDUserNameId).style.display = 'none';
				document.getElementById(TDUserSignatureId).style.display = 'block';
				
				//SET HIDDEN FIELD (hidd_signWitness1Done) TO true IF WITNEDD SIGNATURE ADDED
				if(userIdentity=='Witness1'){
					if(document.getElementById('hidd_signWitness1Done')) {
						document.getElementById('hidd_signWitness1Done').value = 'true';
					}
					
				}		
				//END SET HIDDEN FIELD (hidd_signWitness1Done) TO true IF WITNEDD SIGNATURE ADDED

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
		var consentMultipleId1 = '<?php echo $_REQUEST["consentMultipleId"];?>';
		var hiddPurgestatus1 = '<?php echo $_REQUEST["hiddPurgestatus"];?>';
		var signAnesthesiaIdBackColor1 = '<?php echo $signAnesthesiaIdBackColor;?>';
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
		url=url+"&userIdentity="+userIdentity
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		url=url+"&consentMultipleId="+consentMultipleId1
		url=url+"&hiddPurgestatus="+hiddPurgestatus1
		if(delSign) {
			url=url+"&delSign=yes"
		}
		url=url+"&signAnesthesiaIdBackColor="+signAnesthesiaIdBackColor1
		if(signCheck=='true') { //TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			xmlHttp.onreadystatechange=displayUserSignFun;
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}	
	}
</script>
<script>	
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
			var signMsg = "";
			
			//START CODE TO VALIDATE MANDATORY SIGNATURE
			var msgVal = "Please make following signature(s) :-";
			
			//SIGPAD SIGNATURE
			/*
			if(signNum=='1' || signNum && (signNum == '<?php echo count($hiddenFields);?>')) {
				signMsg = msgVal;
				signMsg += '\n\t Sign'+signNum;
			}else if(signNum) {
				signMsg = msgVal;
				var signNumArr = new Array();
				var signNumArr = signNum.split(',');
				
				for(var a = 0; a<signNumArr.length; a++) {
					signMsg += '\n\t Sign'+signNumArr[a];	
				}
			}*/
			
			//WITNESS SIGNATURE (SINGLE CLICK)
			if(document.getElementById("hidd_signWitness1Activate") && document.getElementById("hidd_signWitness1Activate")!= "undefined") {
				if(document.getElementById("hidd_signWitness1Activate").value=='yes' && document.getElementById("hidd_signWitness1Done").value=='') {
					if(!signMsg) {
						signMsg = msgVal;	
					}
					signMsg += '\n\t Witness Signature';
				}
			}
			if(signMsg) {
				alert(signMsg);	
				return false;
			}
			//END CODE TO VALIDATE MANDATORY SIGNATURE
			
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
<div id="post" style="display:none; position:absolute;"></div>
<?php 
	if($consentMultipleId) {
		$consentMultipleIdQry = " AND consent_template_id = '$consentMultipleId'";
	}	
	if($_REQUEST['consentMultipleAutoIncrId']){
		$consentAutoIncId = " AND consentAutoIncId = '$consentMultipleAutoIncrId'";
	}
	//show all epost ravi
?>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>


<?php
	if(stripslashes($ViewConsentSurgeryRow["surgery_consent_data"])) {
		$alreadySaveMessage = '&amp;alreadySave=true';
	}
?> 


<div class="slider_content scheduler_table_Complete" id="" style="">
	<form name="frm_consent_multiple" id="frm_consent_multiple" class="wufoo topLabel" enctype="multipart/form-data" method="post" style=" margin:0px; " action="consent_multiple_form.php?saveRecord=true<?php echo $saveLink.$alreadySaveMessage;?>&amp;SaveForm_alert=true&amp;save_printRecord=true&amp;consentMultipleId=<?php echo $consentMultipleId;?>&amp;consentMultipleAutoIncrId=<?php echo $consentMultipleAutoIncrId;?>&amp;hiddPurgestatus=<?php echo $hiddPurgestatus;?>" >	
		<?php 
        for($h=0;$h<count($hiddenFields);$h++){
        ?>
        <input type="hidden" name="sigData<?php echo($h+1); ?>" id="sigData<?php echo($h+1); ?>" value="">
        <input type="hidden" name="hiddSigIpadId<?php echo($h+1); ?>" id="hiddSigIpadId<?php echo($h+1); ?>" value="">
        <?php
        }
    	?>
        <input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
        <input type="hidden" name="Save_PrintForm" id="Save_PrintForm" value="yes">
        <input type="hidden" name="formIdentity" id="formIdentity" value="">
        <input type="hidden" name="getText" id="getText">
        <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>"/>	
        <input type="hidden" name="frmAction" id="frmAction" value="consent_multiple_form.php">		
        <input type="hidden" name="consentMultipleId" id="consentMultipleId" value="<?php echo $consentMultipleId;?>"/>	
        <input type="hidden" name="surgery_consent_name" id="surgery_consent_name" value="<?php echo $surgery_consent_name;?>"/>	
        <input type="hidden" name="surgery_consent_alias" id="surgery_consent_alias" value="<?php echo $surgery_consent_alias;?>"/>	
        <input type="hidden" name="show_td" id="show_td" value="<?php print $consentMultipleId; ?>" >
        <input type="hidden" name="sig_count" id="sig_count" value="<?php print count($hiddenFields); ?>" >
        <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
        <!-- PURGE -->
        <input type="hidden" name="hiddform_status" id="hiddform_status" value="<?php echo $form_status?>">
        <input type="hidden" name="hiddConsentPurgeStatus" id="hiddConsentPurgeStatus" value="">
        <input type="hidden" name="hiddPurgestatus" id="hiddPurgestatus" value="<?php echo $purge_status;?>">
        <input type="hidden" name="consentMultipleAutoIncrId" id="consentMultipleAutoIncrId" value="<?php echo $consentMultipleAutoIncrId;?>">
        <input type="hidden" name="show_autoId" id="show_autoId" value="<?php echo $consentMultipleAutoIncrId;?>">
	    	<input type="hidden" id="vitalSignGridHolder" />
      	<input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
        <!-- PURGE -->
        <?php
        $cntSigImage =  substr_count($consent_content,'<img src="SigPlus_images/sign_');
        ?>
        <input type="hidden" name="hiddCntSigImageId" id="hiddCntSigImageId" value="<?php echo $cntSigImage;?>">
        <?php
				$epost_table_name = "consent_multiple_form";
				include("./epost_list.php");
			?>	                    
        <!--
        <div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_green">
            <span class="bg_span_green">
                <?php echo stripslashes($surgery_consent_name);?>
            </span>
			
         </div>-->
        <div class="scanner_win new_s bg_green_Sty">
         <h4>
            <span>PATIENT FORM</span>      
         </h4>
        </div>
        <Div class="consent_wrap_slider">
             <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12   cf table-hover">
                    <tr>
                        <td class="alignLeft" >
                            <div id="divSaveAlert" style="position:absolute; left:350px; top:200px; display:none;">
                                <?php 
                                    $bgCol = '#dfe3ee';
                                    $borderCol = '#dfe3ee';
                                    include('saveDivPopUp.php'); 
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr class="valignTop" >
                      <td class="alignLeft" >
                        <table class=" alignCenter" style="width:99%; " >
                            <?php //PURGE
                            if($purge_status=="true"){  ?>
                                <tr>
                                    <td class="alignCenter" colspan="2" style="font-weight:bold; font-size:24px; color:#FF0000; ">Consent Form Purged</td>	
                                </tr>		
                            <?php 	
                            } else{  ?>				
                            
                                <tr style="height:22px; background-color:#FFFFFF;">
                                    <td style="width:1%;"></td>
                                    <td colspan="2" class="text_10b"></td>
                                </tr>
                            <?php
                            } //PURGE ?>	
                            <tr style="height:300px; background-color:#FFFFFF;" >
                                <td style="width:1%;"></td>
                                <td class="text_10 valignTop" id="consentFormContent"  style="width:95%;height:22px;">
                                    <?php
                                        if($img_content){
                                            ?>
                                            <img style="cursor:pointer;" src="admin/logoImg.php?from=Consent&amp;scan_upload_id=<?php echo $scan_upload_id; ?>">
                                            <?php
                                        }else{
                                             echo $consent_content;
                                        }
                                        ?>
                                </td>
                                <td style="width:2%;"></td>
                            </tr>
                            <tr style="height:22px; background-color:#FFFFFF;">
                                <td style="width:1%;"></td>
                                <td colspan="2" class="text_10b"></td>
                            </tr>
                        </table>
                     </td>	
                   </tr> 
                   
                   
                   
                   <?php
                   if(($sigStatus=='old') && ($consentSurgery_patient_sign!='')) {
                   ?>
                   <tr>
                        <td class="alignCenter valignTop" style="width:99%;">
                            <table style="border-collapse:collapse; border:1px; border-style:solid; border-color:#D1E0C9; background-color:#F1F4F0; width:99%;">
                                <tr style="background-color:#FFFFFF; height:6px;"><td colspan="5"></td></tr>
                                <tr style="height:80px;">
                                    <td style="width:41px;"></td>
                                    <td class="text_10b nowrap"><?php echo $Consent_patientName.' : ';?></td>
                                    <?php
                                    
                                    if(($sigStatus=='old') && ($consentSurgery_patient_sign!='')) {
                                    ?>
                                    <td class="text_10" style="width:780px;"  onmouseout="get_App_Coords('app_ConsentSurgerySignature')">
                                        
                                        <applet name="app_ConsentSurgerySignature" code="MyCanvasColored.class" 
                                            archive="DrawApplet.jar" style="width:250px; height:65px;"
                                            codebase="<?php echo $surgeryCenterDirectoryName.'/';?>common/applet/" >
                                            <param name="bgImage" value="images/white.jpg">
                                            <param name="strpixls" value="<?php echo $consentSurgery_patient_sign;?>">
                                            <param name="mode" value="edit">
                                        </applet>
                                        <img src="images/eraser.gif" onClick="return getclear_os('app_ConsentSurgerySignature');">
                                    </td> 
                                    <?php
                                        $sigStatus='old';
                                    }else {
                                    
                                    ?>
                                    <td class="text_10 consentObjectBeforSign" style="width:260px;" >
                                        
                                        <OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height=75
                                            id=SigPlus1 name=SigPlus1
                                            style="HEIGHT: 65px; WIDTH: 250px; LEFT: 0px; TOP: 0px;" 
                                            VIEWASTEXT>
                                            <PARAM NAME="_Version" VALUE="131095">
                                            <PARAM NAME="_ExtentX" VALUE="4842">
                                            <PARAM NAME="_ExtentY" VALUE="1323">
                                            <PARAM NAME="_StockProps" VALUE="0">
                                        </OBJECT>
            
                                    </td>
                                    <td class="text_10 alignLeft" style="width:440px;" >
                                        &nbsp;<img style="cursor:pointer; vertical-align:top" src="images/pen.png" id="SignBtn" onClick="OnSign();"><br><br>
                                        &nbsp;<img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="images/touch.svg" id="SignBtnTouch" name="SignBtnTouch" onclick="OnSignIpadPhy('<?php echo $patient_id;?>','<?php echo $pConfId;?>','ptConsent','consentSigIpadId','')"><br> 
                                        &nbsp;<img style="cursor:pointer; vertical-align:top" src="images/eraser.gif" id="ClearBtn" alt="Clear Sign" onClick="OnClear();">
                                    </td>
                                    <?php
                                        $sigStatus='new';
                                    }
                                    ?>
                                    <INPUT TYPE="hidden" name="consentSurgery_patient_sign" id="consentSurgery_patient_sign" value="<?php echo $consentSurgery_patient_sign;?>">
                                    <input type="hidden" name="sigStatus" id="sigStatus" value="<?php echo $sigStatus;?>">
                                    <td style="width:40px;"></td>
                                </tr> 
                                <tr style="background-color:#FFFFFF; height:6px;"><td colspan="5"></td></tr>
                         </table>
                     </td>
                 </tr>
                 <?php
                }
                
                if($signSurgeon1Activate=='yes' || $chkSignSurgeon1Activate=='yes' || $signNurseActivate=='yes' || $chkSignNurseActivate=='yes') {
                ?>		 
                 <tr>
                    <td class="alignCenter valigntop" style="width:99%;">
                        <table style=" width:99%; border:1px; border-style:solid; border-color:#D1E0C9; border-collapse:collapse; background-color:#F1F4F0;">
                            <tr style="background-color:#FFFFFF; height:6px;"><td colspan="4"></td></tr>
                                <tr style="height:22px;">
                                    <td class="text_10b nowrap" style="width:40%; padding-left:20px;">
                                    
                                    <?php
                                        if($signSurgeon1Activate=='yes' || $chkSignSurgeon1Activate=='yes') {
                                        
                                            
                                        //CODE RELATED TO SURGEON Electronically Signed
                                            if($loggedInUserType<>"Surgeon") {
                                                $loginUserName = $_SESSION['loginUserName'];
                                                $callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
                                            }else {
                                                $loginUserId = $_SESSION["loginUserId"];
                                                $callJavaFunSurgeon = "document.frm_consent_multiple.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Surgeon1');";
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
                                                //$signSurgeon1DateTimeFormatNew=date("m-d-Y h:i A",strtotime($signSurgeon1SignDate));
												$signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signSurgeon1SignDate);
												
                                            }
                                            if($_SESSION["loginUserId"]==$signSurgeon1Id) {
                                                $callJavaFunSurgeonDel = "document.frm_consent_multiple.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
                                            }else {
                                                $callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
                                            }
                                        //END CODE RELATED TO SURGEON Electronically Signed
                                     
                                     ?>
                                                
                                                <div id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>; " >
                                                    <table class="table_collapse">
                                                        <tr>
                                                            <td class="text_10b alignLeft valignMiddle nowrap" style=" width:20%;cursor:pointer;<?php echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunSurgeon;?>">
                                                                Surgeon&nbsp;Signature
                                                            </td><td>&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>; ">	
                                                    <table style="border:none; border-collapse:collapse;">
                                                        <tr>
                                                            <td class="text_10 alignLeft valignMiddle nowrap" style="cursor:pointer; padding-right:7px;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>">
                                                                <?php echo "<b>Surgeon:</b>". " Dr. ". $Surgeon1Name; ?>
                                                            </td>
                                                            
                                                        </tr>
                                                        <tr>
                                                            <td class="text_10 alignLeft valignMiddle nowrap">
                                                                <b>Electronically Signed :&nbsp;</b>
                                                                <?php echo $surgeon1SignOnFileStatus;?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text_10" nowrap>
                                                                <b>Signature Date:&nbsp;</b>
                                                                <?php echo $signSurgeon1DateTimeFormatNew;?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <input type="hidden" name="hidd_signSurgeon1Activate" id="hidd_signSurgeon1Activate" value="yes">
                                    <?php
                                        }
                                 ?>
                                    </td>
                                    <td class="text_10 alignLeft" style="width:15%;  border:0px solid #CCCCCC;">
                                    <?php
                                        if($signNurseActivate=='yes' || $chkSignNurseActivate=='yes') {
                                    
                                            //CODE RELATED TO NURSE Electronically Signed
                                                if($loggedInUserType<>"Nurse") {
                                                    $loginUserName = $_SESSION['loginUserName'];
                                                    $callJavaFun = "return noAuthorityFunCommon('Nurse');";
                                                }else {
                                                    $loginUserId = $_SESSION["loginUserId"];
                                                    $callJavaFun = "document.frm_consent_multiple.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Nurse1');";
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
                                                    //$signNurseDateTimeFormatNew=date("m-d-Y h:i A",strtotime($signNurseSignDate));
													$signNurseDateTimeFormatNew = $objManageData->getFullDtTmFormat($signNurseSignDate);
                                                }
                                                if($_SESSION["loginUserId"]==$signNurseId) {
                                                    $callJavaFunDel = "document.frm_consent_multiple.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Nurse1','delSign');";
                                                }else {
                                                    $callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
                                                }
                                            //END CODE RELATED TO NURSE Electronically Signed
                                    
                                    ?>
                                            
                                            <div id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>; " >
                                                <table style="border:none; border-collapse:collapse;">
                                                    <tr>
                                                        <td class="text_10b nowrap"   style="cursor:pointer;<?php echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</td>
                                                    </tr>
                                                </table>
                                                
                                            </div>
                                            <div id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>; ">	
                                                <table class="table_collapse">
                                                    <tr>
                                                        <td class="text_10 nowrap" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"><?php echo "<b>Nurse:</b>"." ".$NurseNameShow; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text_10 nowrap">
                                                            <b>Electronically Signed :&nbsp;</b>
                                                            <?php echo $signOnFileStatus;?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text_10" nowrap>
                                                            <b>Signature Date:&nbsp;</b>
                                                            <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signNurseDateTimeFormatNew; ?> <span class="fa fa-edit"></span></span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <input type="hidden" name="hidd_signNurseActivate" id="hidd_signNurseActivate" value="yes">	
                                            
                                    <?php
                                        }
                                    ?>
                                    
                                    </td>
                                    <td style="width:2%"></td>
                            </tr> 
                            <tr style="background-color:#FFFFFF; height:6px;"><td colspan="4"></td></tr>
                         </table>		 
                    </td>
                 </tr>
                 <?php
                 }
                 if($signAnesthesia1Activate=='yes' || $chkSignAnesthesia1Activate=='yes' || $signWitness1Activate=='yes' || $chkSignWitness1Activate=='yes') {
                    
                ?>		 
                 <tr>
                    <td class="valignTop alignCenter" style="width:99%;">
                        <table style=" width:99%;padding:0px; background-color:#F1F4F0; border:1px; border-style:solid; border-color:#D1E0C9;" >
                            <tr style=" background-color:#FFFFFF; height:6px;"><td colspan="4"></td></tr>
                                <tr style="height:22px;">
                                    <td class="text_10b nowrap" style="width:40%; padding-left:20px;">
                                    
                                    <?php
                                        if($signAnesthesia1Activate=='yes' || $chkSignAnesthesia1Activate=='yes') {
                                        
                                            
                                        //CODE RELATED TO Anesthesiologist Electronically Signed
                                            if($loggedInUserType<>"Anesthesiologist") {
                                                $loginUserName = $_SESSION['loginUserName'];
                                                $callJavaFunAnesthesia = "return noAuthorityFunCommon('Anesthesiologist');";
                                            }else {
                                                $loginUserId = $_SESSION["loginUserId"];
                                                $callJavaFunAnesthesia = "document.frm_consent_multiple.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Anesthesia1');";
                                            }					
                                            $anesthesia1SignOnFileStatus = "Yes";
                                            $TDanesthesia1NameIdDisplay = "block";
                                            $TDanesthesia1SignatureIdDisplay = "none";
                                            $Anesthesia1Name = $loggedInUserName;
                                            $Anesthesia1SubType = $logInUserSubType;
                                            $Anesthesia1PreFix = 'Dr.';
                                            $signAnesthesia1DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
                                            if($signAnesthesia1Id<>0 && $signAnesthesia1Id<>"") {
                                                $Anesthesia1Name = $signAnesthesia1LastName.", ".$signAnesthesia1FirstName." ".$signAnesthesia1MiddleName;
                                                $anesthesia1SignOnFileStatus = $signAnesthesia1Status;	
                                                
                                                $TDanesthesia1NameIdDisplay = "none";
                                                $TDanesthesia1SignatureIdDisplay = "block";
                                                //$signAnesthesia1DateTimeFormatNew=date("m-d-Y h:i A",strtotime($signAnesthesia1SignDate));
												$signAnesthesia1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signAnesthesia1SignDate);
                                                $Anesthesia1SubType = getUserSubTypeFun($signAnesthesia1Id); //FROM common/commonFunctions.php
                                            }
                                            if($Anesthesia1SubType=='CRNA') {
                                                $Anesthesia1PreFix = '';
                                            }
                                            
                                            //CODE TO REMOVE ANES 1 SIGNATURE
                                                if($_SESSION["loginUserId"]==$signAnesthesia1Id) {
                                                    $callJavaFunAnesthesiaDel = "document.frm_consent_multiple.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
                                                }else {
                                                    $callJavaFunAnesthesiaDel = "alert('Only $Anesthesia1PreFix $Anesthesia1Name can remove this signature');";
                                                }
                                            //END CODE TO REMOVE ANES 1 SIGNATURE	
                                            
                                        //END CODE RELATED TO SURGEON Electronically Signed
                                     
                                     ?>
                                                
                                                <div id="TDanesthesia1NameId" style="display:<?php echo $TDanesthesia1NameIdDisplay;?>; " >
                                                    <table class="table_collapse" style="border:none;">
                                                        <tr>
                                                            <td class="text_10b alignLeft valignMiddle nowrap" style=" width:20%;cursor:pointer;<?php echo $signAnesthesiaIdBackColor;?>  " onClick="javascript:<?php echo $callJavaFunAnesthesia;?>">
                                                                Anesthesia Provider&nbsp;Signature
                                                            </td><td>&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div id="TDanesthesia1SignatureId" style="display:<?php echo $TDanesthesia1SignatureIdDisplay;?>; ">	
                                                    <table style=" border-collapse:collapse; border:none;">
                                                        <tr>
                                                            <td class="alignLeft text_10 valignMiddle nowrap" style="cursor:pointer; padding-right:7px;" onClick="javascript:<?php echo $callJavaFunAnesthesiaDel;?>">
                                                                <?php echo "<b>Anesthesia Provider:</b>". " $Anesthesia1PreFix ". $Anesthesia1Name; ?>
                                                            </td>
                                                            
                                                        </tr>
                                                        <tr>
                                                            <td class="alignLeft text_10 valignMiddle nowrap">
                                                                <b>Electronically Signed :&nbsp;</b>
                                                                <?php echo $anesthesia1SignOnFileStatus;?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="alignLeft text_10" nowrap>
                                                                <b>Signature Date:&nbsp;</b>
                                                                <?php echo $signAnesthesia1DateTimeFormatNew;?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <input type="hidden" name="hidd_signAnesthesia1Activate" id="hidd_signAnesthesia1Activate" value="yes">
                                    <?php
                                        }
                                 ?>
                                    </td>
                                    <td class="text_10 alignLeft" style="width:15%;  ">
                                    <?php
                                        if($signWitness1Activate=='yes' || $chkSignWitness1Activate=='yes') {
                                            //GET USER DETAIL(FOR WITNESS SIGNATURE)
                                                if($signWitness1Id) {
                                                    $ViewWitnessUserNameQry = "select * from `users` where  usersId = '".$signWitness1Id."'";
                                                    $ViewWitnessUserNameRes = imw_query($ViewWitnessUserNameQry) or die(imw_error()); 
                                                    $ViewWitnessUserNameRow = imw_fetch_array($ViewWitnessUserNameRes); 
                                                    $witnessUserType 		= $ViewWitnessUserNameRow["user_type"];
                                                }
                                            //END GET USER DETAIL(FOR WITNESS SIGNATURE)
                                            
                                            //CODE RELATED TO NURSE Electronically Signed
                                                if($witnessUserType && ($loggedInUserType<>$witnessUserType)) {
                                                    $loginUserName = $_SESSION['loginUserName'];
                                                    $callJavaFunWitness = "return noAuthorityFunCommon('".$witnessUserType."');";
                                                }else {
                                                    $loginUserId = $_SESSION["loginUserId"];
                                                    $callJavaFunWitness = "document.frm_consent_multiple.hiddSignatureId.value='TDwitness1SignatureId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Witness1');";
                                                }
                                            
                                                $signOnFileWitness1Status = "Yes";
                                                $TDwitness1NameIdDisplay = "block";
                                                $TDwitness1SignatureIdDisplay = "none";
                                                $Witness1NameShow = $loggedInUserName;
                                                $signWitness1DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
                                                $signWitness1Done = "";
												if($signWitness1Id<>0 && $signWitness1Id<>"") {
                                                    $Witness1NameShow = $signWitness1LastName.", ".$signWitness1FirstName." ".$signWitness1MiddleName;
                                                    $signOnFileWitness1Status = $signWitness1Status;	
                                                    
                                                    $TDwitness1NameIdDisplay = "none";
                                                    $TDwitness1SignatureIdDisplay = "block";
                                                    //$signWitness1DateTimeFormatNew = date("m-d-Y h:i A",strtotime($signWitness1SignDate));
                                                	$signWitness1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signWitness1SignDate);
													$signWitness1Done = "true";
												}
                                                if($_SESSION["loginUserId"]==$signWitness1Id) {
                                                    $callJavaFunWitnessDel = "document.frm_consent_multiple.hiddSignatureId.value='TDwitness1NameId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','consent_multiple_form_ajaxSign.php','$loginUserId','Witness1','delSign');";
                                                }else {
                                                    $callJavaFunWitnessDel = "alert('Only $Witness1NameShow can remove this signature');";
                                                }
                                            //END CODE RELATED TO NURSE Electronically Signed
                                        
                                        
                                    ?>
                                            
                                            <div id="TDwitness1NameId" style="display:<?php echo $TDwitness1NameIdDisplay;?>; " >
                                                <table style="border:none; border-collapse:collapse;">
                                                    <tr>
                                                        <td class="text_10b nowrap" style="cursor:pointer;<?php echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunWitness;?>">Witness&nbsp;Signature</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div id="TDwitness1SignatureId" style="display:<?php echo $TDwitness1SignatureIdDisplay;?>; ">	
                                                <table style="border:none; border-collapse:collapse;">
                                                    <tr>
                                                        <td class="text_10 nowrap" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitnessDel;?>"><?php echo "<b>Witness:</b>"." ".$Witness1NameShow; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text_10 nowrap" >
                                                            <b>Electronically Signed :&nbsp;</b>
                                                            <?php echo $signOnFileWitness1Status;?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text_10" nowrap>
                                                            <b>Signature Date:&nbsp;</b>
                                                            <?php echo $signWitness1DateTimeFormatNew;?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <input type="hidden" name="hidd_signWitness1Activate" id="hidd_signWitness1Activate" value="yes">
                                            <input type="hidden" name="hidd_signWitness1Done" id="hidd_signWitness1Done" value="<?php echo $signWitness1Done;?>">	
                                    <?php
                                        }
                                    ?>
                                    
                                    </td>
                                    <td style="width:2%;"></td>
                            </tr> 
                            <tr style=" background-color:#FFFFFF; height:6px;"><td colspan="4"></td></tr>
                         </table>		 
                    </td>
                 </tr>
                 <?php
                 }
                 ?>
                 <tr>
                        <td> <img src="images/tpixel.gif" style="border:none; width:1px; height:15px;"></td>
                 </tr>
                <?php //PURGE	
                if($purge_status=="true"){  ?>
                    <tr>
                        <td class="alignCenter" style="font-weight:bold; font-size:24px; color:#FF0000; ">Consent Form Purged</td>	
                    </tr>		
                <?php 
                } else{  ?>				
                    <tr style=" background-color:#FFFFFF; height:10px;">
                        <td class="text_10b">&nbsp;</td>
                    </tr>
                <?php 	
                }  //PURGE ?>						 
            </table>
        </Div>
	 
        
</form>
</div>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="consent_multiple_form.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->

<script >
	/*
	var getArea = document.getElementsByTagName('textarea');
	var areaTag = new Array();
	var getTags = document.getElementsByTagName('input');
	var arrTag = new Array();
	var a = 0;
	for(var i=0;i<getTags.length;i++)
	{
		if(document.getElementsByTagName('input')[i].type != 'hidden')
		{
			arrTag[a] = document.getElementsByTagName('input')[i].name;
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
		//var taglength = tagName.length;
		//alert(tagName);
		//if(taglength > 1){
			for(var l in tagName){
				//tagName[l]
				//alert(tagName[l]);
				
			}
		//}
		//alert(taglength);
		//for(var l = 0; l < taglength; l++){
		//alert(tagName[l].name);
				
				
			//}
		
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
	$finalizePageName = "consent_multiple_form.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM
if($finalizeStatus!='true'  && $purge_status!='true'){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();
		var form_status = "<?php echo $form_status;?>";
		if(form_status == "completed") {
			if(typeof(top.frames[0].remove_save_buttons)!='undefined') {
				top.frames[0].remove_save_buttons();
			}
		}
		
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
if($SaveForm_alert == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}
include("print_page.php");
?><script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>