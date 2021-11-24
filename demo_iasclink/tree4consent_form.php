<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php	
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

include("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<?php
$patient_id=isset($_REQUEST['patient_id']) ? $_REQUEST['patient_id'] : 0;
$intPatientWaitingId=isset($_REQUEST['intPatientWaitingId']) ? $_REQUEST['intPatientWaitingId'] : 0;
$intConsentTemplateId=isset($_REQUEST['intConsentTemplateId']) ? $_REQUEST['intConsentTemplateId'] : '';
$consentAllMultipleId= isset($_REQUEST['consentAllMultipleId']) ? $_REQUEST['consentAllMultipleId'] : '';

if(!$consentAllMultipleId) { $consentAllMultipleId='0'; }
$consentAllMultipleIdArr = array();
$consentAllMultipleIdArr = explode(',',$consentAllMultipleId);

include("dhtmlgoodies_tree.class.php");
$tree = new dhtmlgoodies_tree();

function getQryRes($qry){
	$qryId = imw_query($qry);
	$return = array();
	while($data = imw_fetch_assoc($qryId)){
		$return[] = $data;
	}
	return $return;
}

$tree->addToArray(1,"Signed Forms",0,"");
//$tree->addToArray(1,"Signed Forms",0,"","","images/close_fold.png","admin/copy_consent_forms.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&consentAllMultipleId=$consentAllMultipleId&scanEKG=true","Signed Forms","images/icon_copy1.png","Copy Previous Consent Forms");

//---- Get Patient Consent Forms Signed Date(s)-------
$p=1;

//START CODE FOR SIGNED RECORD
//$qry = "SELECT surgery_consent_id, surgery_consent_name, consent_template_id  from iolink_consent_filled_form where fldPatientWaitingId=$intPatientWaitingId AND consentGroupName='' ORDER BY surgery_consent_name" ;
$qry = "SELECT surgery_consent_id, surgery_consent_name, consent_template_id  from iolink_consent_filled_form where fldPatientWaitingId=$intPatientWaitingId ORDER BY surgery_consent_name" ;
$patientSignConsentFormCreatedDate = getQryRes($qry);
//$consentSignedTemplateIdArr=array();
for($z=0;$z<count($patientSignConsentFormCreatedDate);$z++){
	
	$p++;
	//$formCreatedDate=$patientSignConsentFormCreatedDate[$z]['surgery_consent_name'];
	//$tree->addToArray($p,$formCreatedDate,1);	

	//$consentFormId = $patientSignConsentFormCreatedDate[$z]['surgery_consent_id'];
	$consentFormName = $patientSignConsentFormCreatedDate[$z]['surgery_consent_name'];
	$consentTemplateId = $patientSignConsentFormCreatedDate[$z]['consent_template_id'];
	$consentSignedTemplateIdArr[]= $patientSignConsentFormCreatedDate[$z]['consent_template_id'];
	$consentFormName = trim(ucwords($consentFormName));

	//$tree->addToArray($p,$consentFormName,$f,"consentFormDetails.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&intConsentTemplateId=$consentFormId","consent_data","images/dhtmlgoodies_sheet.gif");
	$tree->addToArray($p,$consentFormName,1,"print_consent_form.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$consentTemplateId&amp;consentAllMultipleId=$consentAllMultipleId","consent_data","images/dhtmlgoodies_sheet.gif");
}
//END CODE FOR SIGNED RECORD

//START CODE NOT TO DISPLAY SIGNED FORM AT BELOW
$andConsentSignedTemplateIdQry='';
if($consentSignedTemplateIdArr) {
	$consentSignedTemplateIdArr = array_unique($consentSignedTemplateIdArr);
	$consentSignedTemplateId = implode(',',$consentSignedTemplateIdArr);
	$andConsentSignedTemplateIdQry = 'AND consent_id NOT IN('.$consentSignedTemplateId.')';
}
//END CODE NOT TO DISPLAY SIGNED FORM AT BELOW

//GET SELECTED CATEGORY
//$getSelectedCategoryQry = "select consent_category_id, consent_id, consent_name from consent_forms_template where consent_id  IN($consentAllMultipleId) order by consent_name";
$getSelectedCategoryQry = "select consent_category_id, consent_id, consent_name from consent_forms_template where consent_delete_status!='true' order by consent_name";
$getSelectedCategoryRes = imw_query($getSelectedCategoryQry);
$getSelectedCategoryNumRow= imw_num_rows($getSelectedCategoryRes);
$getSelectedCategoryIdArr = array();
if($getSelectedCategoryNumRow>0) {
	while($getSelectedCategoryRow = imw_fetch_array($getSelectedCategoryRes)) {
		$getSelectedCategoryIdArr[] = $getSelectedCategoryRow['consent_category_id'];
	}
}
$getSelectedCategoryId='0';
if($getSelectedCategoryIdArr) {
	$getSelectedCategoryId = implode(',',$getSelectedCategoryIdArr);
}
//END GET SELECTED CATEGORY

//---- get consent forms -------


$qry1 = "SELECT category_id, category_name from consent_category WHERE category_id IN($getSelectedCategoryId) AND category_status!='true'  order by category_name";
$consentCatName = getQryRes($qry1);
$c = $p;
$d = 0;
for($i=0;$i<count($consentCatName);$i++){
	$consentCategoryNameId = $consentCatName[$i]['category_id'];
	$consentCategoryName = trim(ucwords($consentCatName[$i]['category_name']));
	$c++;
	$d = $c;
	
	$qry2 = "select consent_id, consent_name from consent_forms_template where consent_category_id  = '$consentCategoryNameId' AND consent_delete_status!='true' $andConsentSignedTemplateIdQry order by consent_name";
	$consentDetail = getQryRes($qry2);
	$folderOnceDisplay=false;
	for($a=0;$a<count($consentDetail);$a++){
		$consentFormId = $consentDetail[$a]['consent_id'];
		//if(in_array($consentFormId,$consentAllMultipleIdArr)) {
			//START DISPLY FOLDER NAME
			if($consentDetail) {
				if($folderOnceDisplay==false) {
					$tree->addToArray($c,$consentCategoryName,"0","");
					$folderOnceDisplay=true;
				}
			}
			//END DISPLY FOLDER NAME
		
			//START DISPLAY CONSENT FORM NAMES
			$consentFormName = trim(ucwords($consentDetail[$a]['consent_name']));
			$c++;
			$tree->addToArray($c,$consentFormName,$d,"consentFormDetails.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$consentFormId&amp;consentAllMultipleId=$consentAllMultipleId","consent_data","images/dhtmlgoodies_sheet.gif");
			//END DISPLAY CONSENT FORM NAMES
		//}
	}	
}
//START CODE FOR ANESTHESIA-CONSENT SCAN RECORD
$t_anesthesiaConsent=$c+1;
$tree->addToArray($t_anesthesiaConsent,"Consent",0,"","","images/close_fold.png","admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&amp;scanAnesthesiaConsent=true","Consent");
$h_anesthesiaConsent=$t_anesthesiaConsent+1;
$chkAnesthesiaConsentScanedQry = "SELECT scan_consent_id,consent_template_id,document_name  FROM iolink_scan_consent WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND iolink_scan_folder_name='consent' AND patient_id='".$patient_id."'";
$chkAnesthesiaConsentScanedRow = getQryRes($chkAnesthesiaConsentScanedQry);
for($z_anesthesiaConsent=0;$z_anesthesiaConsent<count($chkAnesthesiaConsentScanedRow);$z_anesthesiaConsent++){
	
	$AnesthesiaConsentScanedAutoId = $chkAnesthesiaConsentScanedRow[$z_anesthesiaConsent]['scan_consent_id'];
	$AnesthesiaConsentScanedFormName = $chkAnesthesiaConsentScanedRow[$z_anesthesiaConsent]['document_name'];
	$maskAnesthesiaConsent = $chkAnesthesiaConsentScanedRow[$z_anesthesiaConsent]['mask'];
	$AnesthesiaConsentScanedTemplateId = $chkAnesthesiaConsentScanedRow[$z_anesthesiaConsent]['consent_template_id'];
	
	if($maskAnesthesiaConsent){
		$arrMaskAnesthesiaConsent = explode('.',$maskAnesthesiaConsent);
		$maskAnesthesiaConsentFilename = $arrMaskAnesthesiaConsent[0];
	}
	else{
		$arrAnesthesiaConsentFilename = explode('.',$AnesthesiaConsentScanedFormName);
		$maskAnesthesiaConsentFilename = $AnesthesiaConsentScanedFormName;
		if($arrAnesthesiaConsentFilename[1] == "jpg"){
			$maskAnesthesiaConsentFilename = $arrAnesthesiaConsentFilename[0];
		}
	}
	$tree->addToArray($h_anesthesiaConsent,$maskAnesthesiaConsentFilename,$t_anesthesiaConsent,"showImg.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$AnesthesiaConsentScanedTemplateId&amp;consentAllMultipleId=$consentAllMultipleId&amp;scan_consent_id=$AnesthesiaConsentScanedAutoId","consent_data","images/dhtmlgoodies_sheet.gif");
	$h_anesthesiaConsent++;
}
//END CODE FOR ANESTHESIA-CONSENT SCAN RECORD

//START CODE FOR PT-INFO SCAN RECORD
$r=$h_anesthesiaConsent+1;
$tree->addToArray($r,"Patient Info",0,"","","images/close_fold.png","admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&amp;scanPtInfo=true","Patient Info");
//$tree->addToArray($r+1,'New Document',$r,"admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingIdamp;&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&scanPtInfo=true","consent_data","images/dhtmlgoodies_sheet.gif");

$j=$r+1;
$chkConsentPtInfoScanedQry = "SELECT scan_consent_id,consent_template_id,document_name,mask  FROM iolink_scan_consent WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND iolink_scan_folder_name='ptInfo' AND patient_id='".$patient_id."'";
$chkConsentPtInfoScanedRow = getQryRes($chkConsentPtInfoScanedQry);
$consentSignedTemplateIdArr=array();


for($z=0;$z<count($chkConsentPtInfoScanedRow);$z++){
	$consentPtInfoScanedAutoId = $chkConsentPtInfoScanedRow[$z]['scan_consent_id'];
	$consentPtInfoScanedFormName = $chkConsentPtInfoScanedRow[$z]['document_name'];
	$maskPtInfo = $chkConsentPtInfoScanedRow[$z]['mask'];
	$consentPtInfoScanedTemplateId = $chkConsentPtInfoScanedRow[$z]['consent_template_id'];
	if($maskPtInfo){
		$arrMaskPtInfo = explode('.',$maskPtInfo);
		$maskPtInfoFilename = $arrMaskPtInfo[0];
	}
	else{
		$arrPtInfoFilename = explode('.',$consentPtInfoScanedFormName);
		$maskPtInfoFilename = $consentPtInfoScanedFormName;
		if($arrPtInfoFilename[1] == "jpg"){
			$maskPtInfoFilename = $arrPtInfoFilename[0];
		}
		
	}
	
	$tree->addToArray($j,$maskPtInfoFilename,$r,"showImg.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$consentPtInfoScanedTemplateId&amp;consentAllMultipleId=$consentAllMultipleId&amp;scan_consent_id=$consentPtInfoScanedAutoId","consent_data","images/dhtmlgoodies_sheet.gif");
	$j++;
}

//END CODE FOR PT-INFO SCAN RECORD

//START CODE FOR CLINICAL SCAN RECORD
$t=$j+1;
$tree->addToArray($t,"Clinical",0,"","","images/close_fold.png","admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&amp;scanClinical=true","Clinical");
$h=$t+1;
$chkConsentClinicalScanedQry = "SELECT scan_consent_id,consent_template_id,document_name,mask  FROM iolink_scan_consent WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND iolink_scan_folder_name='clinical' AND patient_id='".$patient_id."'";
$chkConsentClinicalScanedRow = getQryRes($chkConsentClinicalScanedQry);
for($z=0;$z<count($chkConsentClinicalScanedRow);$z++){
	
	$consentClinicalScanedAutoId = $chkConsentClinicalScanedRow[$z]['scan_consent_id'];
	$consentClinicalScanedFormName = $chkConsentClinicalScanedRow[$z]['document_name'];
	$consentClinicalScanedTemplateId = $chkConsentClinicalScanedRow[$z]['consent_template_id'];
	$maskClinical = $chkConsentClinicalScanedRow[$z]['mask'];
	if($maskClinical){
		$arrMaskClinical = explode('.',$maskClinical);
		$maskClinicalFilename = $arrMaskClinical[0];
	}
	else{
		$arrClinicalFilename = explode('.',$consentClinicalScanedFormName);
		$maskClinicalFilename = $consentClinicalScanedFormName;
		if($arrClinicalFilename[1] == "jpg"){
			$maskClinicalFilename = $arrClinicalFilename[0];
		}
	}
	$tree->addToArray($h,$maskClinicalFilename,$t,"showImg.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$consentClinicalScanedTemplateIdamp;&consentAllMultipleId=$consentAllMultipleId&amp;scan_consent_id=$consentClinicalScanedAutoId","consent_data","images/dhtmlgoodies_sheet.gif");
	$h++;
}
//END CODE FOR CLINICAL SCAN RECORD


//START CODE FOR IOL SCAN RECORD
$t=$h+1;
$tree->addToArray($t,"IOL",0,"","","images/close_fold.png","admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&amp;scanIOLFolder=true","IOL");
$h=$t+1;
$chkConsentIOLScanedQry = "SELECT scan_consent_id,consent_template_id,document_name,mask  FROM iolink_scan_consent WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND iolink_scan_folder_name='iol' AND patient_id='".$patient_id."'";
$chkConsentIOLScanedRow = getQryRes($chkConsentIOLScanedQry);
for($z=0;$z<count($chkConsentIOLScanedRow);$z++){
	
	$consentIOLScanedAutoId = $chkConsentIOLScanedRow[$z]['scan_consent_id'];
	$consentIOLScanedFormName = $chkConsentIOLScanedRow[$z]['document_name'];
	$consentIOLScanedTemplateId = $chkConsentIOLScanedRow[$z]['consent_template_id'];
	$maskIOL = $chkConsentIOLScanedRow[$z]['mask'];
	if($maskIOL){
		$arrMaskIOL = explode('.',$maskIOL);
		$maskIOLFilename = $arrMaskIOL[0];
	}
	else{
		$arrIOLFilename = explode('.',$consentIOLScanedFormName);
		$maskIOLFilename = $consentIOLScanedFormName;
		if($arrIOLFilename[1] == "jpg"){
			$maskIOLFilename = $arrIOLFilename[0];
		}
	}
	$tree->addToArray($h,$maskIOLFilename,$t,"showImg.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$consentIOLScanedTemplateIdamp;&consentAllMultipleId=$consentAllMultipleId&amp;scan_consent_id=$consentIOLScanedAutoId","consent_data","images/dhtmlgoodies_sheet.gif");
	$h++;
}
//END CODE FOR IOL SCAN RECORD


//START CODE FOR H&P SCAN RECORD
$t_HP=$h+1;
$tree->addToArray($t_HP,"H&P",0,"","","images/close_fold.png","admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&amp;scanHP=true","H&P");
$h_HP=$t_HP+1;
$tree->addToArray($h_HP,'History & Physical Clearance',$t_HP,"history_physical_clearance.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId","consent_data","images/dhtmlgoodies_sheet.gif");	
$chkHpScanedQry = "SELECT scan_consent_id,consent_template_id,document_name,mask  FROM iolink_scan_consent WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND iolink_scan_folder_name='h&p' AND patient_id='".$patient_id."'";
$chkHpScanedRow = getQryRes($chkHpScanedQry);
for($z_HP=0;$z_HP<count($chkHpScanedRow);$z_HP++){
	$h_HP++;
	$HpScanedAutoId = $chkHpScanedRow[$z_HP]['scan_consent_id'];
	$HpScanedFormName = $chkHpScanedRow[$z_HP]['document_name'];
	$HpScanedTemplateId = $chkHpScanedRow[$z_HP]['consent_template_id'];
	$maskHp = urldecode($chkHpScanedRow[$z_HP]['mask']);
	if($maskHp){
		$arrMaskHp = explode('.',$maskHp);
		$maskHpFilename = $arrMaskHp[0];
	}
	else{
		$arrHpFilename = explode('.',$HpScanedFormName);
		$maskHpFilename = $HpScanedFormName;
		if($arrHpFilename[1] == "jpg"){
			$maskHpFilename = $arrHpFilename[0];
		}
	}
	$tree->addToArray($h_HP,$maskHpFilename,$t_HP,"showImg.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$HpScanedTemplateId&amp;consentAllMultipleId=$consentAllMultipleId&amp;scan_consent_id=$HpScanedAutoId","consent_data","images/dhtmlgoodies_sheet.gif");
	
}
//END CODE FOR H&P SCAN RECORD

//START CODE FOR EKG SCAN RECORD
$t_EKG=$h_HP+1;
$tree->addToArray($t_EKG,"EKG",0,"","","images/close_fold.png","admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&amp;scanEKG=true","EKG");
$h_EKG=$t_EKG+1;
$chkEkgScanedQry = "SELECT scan_consent_id,consent_template_id,document_name,mask  FROM iolink_scan_consent WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND iolink_scan_folder_name='ekg' AND patient_id='".$patient_id."'";
$chkEkgScanedRow = getQryRes($chkEkgScanedQry);
for($z_EKG=0;$z_EKG<count($chkEkgScanedRow);$z_EKG++){
	
	$EkgScanedAutoId = $chkEkgScanedRow[$z_EKG]['scan_consent_id'];
	$EkgScanedFormName = $chkEkgScanedRow[$z_EKG]['document_name'];
	$EkgScanedTemplateId = $chkEkgScanedRow[$z_EKG]['consent_template_id'];
	$maskEkg = $chkEkgScanedRow[$z_EKG]['mask'];
	if($maskEkg){
		$arrMaskEkg = explode('.',$maskEkg);
		$maskEkgFilename = $arrMaskEkg[0];
	}
	else{
		$arrEkgFilename = explode('.',$EkgScanedFormName);
		$maskEkgFilename = $EkgScanedFormName;
		if($arrEkgFilename[1] == "jpg"){
			$maskEkgFilename = $arrEkgFilename[0];
		}
	}
	$tree->addToArray($h_EKG,$maskEkgFilename,$t_EKG,"showImg.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$EkgScanedTemplateId&amp;consentAllMultipleId=$consentAllMultipleId&amp;scan_consent_id=$EkgScanedAutoId","consent_data","images/dhtmlgoodies_sheet.gif");
	$h_EKG++;
}
//END CODE FOR EKG SCAN RECORD

//START CODE FOR EKG SCAN RECORD
$t_ocularHx=$h_EKG+1;
$tree->addToArray($t_ocularHx,"Ocular Hx",0,"","","images/close_fold.png","admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&amp;scanOcularHx=true","Ocular Hx");
$h_ocularHx=$t_ocularHx+1;
$chkOcularHxScanedQry = "SELECT scan_consent_id,consent_template_id,document_name,mask  FROM iolink_scan_consent WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND iolink_scan_folder_name='ocularHx' AND patient_id='".$patient_id."'";
$chkOcularHxScanedRow = getQryRes($chkOcularHxScanedQry);
for($z_ocularHx=0;$z_ocularHx<count($chkOcularHxScanedRow);$z_ocularHx++){
	
	$OcularHxScanedAutoId = $chkOcularHxScanedRow[$z_ocularHx]['scan_consent_id'];
	$OcularHxScanedFormName = $chkOcularHxScanedRow[$z_ocularHx]['document_name'];
	$OcularHxScanedTemplateId = $chkOcularHxScanedRow[$z_ocularHx]['consent_template_id'];
	$maskOcularHx = $chkOcularHxScanedRow[$z_ocularHx]['mask'];
	if($maskOcularHx){
		$arrMaskHx = explode('.',$maskOcularHx);
		$maskOcularHxFilename = $arrMaskHx[0];
	}
	else{
		$arrOcularHxFilename = explode('.',$OcularHxScanedFormName);
		$maskOcularHxFilename = $OcularHxScanedFormName;
		if($arrOcularHxFilename[1] == "jpg"){
			$maskOcularHxFilename = $arrOcularHxFilename[0];
		}
	
	}
	$tree->addToArray($h_ocularHx,$maskOcularHxFilename,$t_ocularHx,"showImg.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$OcularHxScanedTemplateId&amp;consentAllMultipleId=$consentAllMultipleId&amp;scan_consent_id=$OcularHxScanedAutoId","consent_data","images/dhtmlgoodies_sheet.gif");
	$h_ocularHx++;
}
//END CODE FOR EKG SCAN RECORD

//START CODE FOR HEALTH-QUEST FORM

$q_healthQuest=$h_ocularHx+1;
$tree->addToArray($q_healthQuest,"Health Questionnaire",0,"","","images/close_fold.png","admin/scanPopUp.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId&amp;scanHealthQuest=true","Health Questionnaire");
$r_healthQuest = $q_healthQuest+1;
$tree->addToArray($r_healthQuest,'Pre-op Health Questionnaire',$q_healthQuest,"iolink_pre_op_health_quest.php?patient_in_waiting_id=$intPatientWaitingId&amp;patient_id=$patient_id&amp;scaniOLinkConsentId=&amp;consentAllMultipleId=$consentAllMultipleId","consent_data","images/dhtmlgoodies_sheet.gif");

	//START CODE TO SCAN HEALTH-QUEST FORM
	$chkHealthQuestScanedQry = "SELECT scan_consent_id,consent_template_id,document_name,mask  FROM iolink_scan_consent WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND iolink_scan_folder_name='healthQuest' AND patient_id='".$patient_id."'";
	$chkHealthQuestScanedRow = getQryRes($chkHealthQuestScanedQry);
	for($z_healthQuest=0;$z_healthQuest<count($chkHealthQuestScanedRow);$z_healthQuest++){
		$r_healthQuest++;
		$HealthQuestScanedAutoId = $chkHealthQuestScanedRow[$z_healthQuest]['scan_consent_id'];
		$HealthQuestScanedFormName = $chkHealthQuestScanedRow[$z_healthQuest]['document_name'];
		$HealthQuestScanedTemplateId = $chkHealthQuestScanedRow[$z_healthQuest]['consent_template_id'];
		$maskHealthQuest = $chkHealthQuestScanedRow[$z_healthQuest]['mask'];
		if($maskHealthQuest){
			$arrMaskHealthQuest = explode('.',$maskHealthQuest);
			$maskHealthQuestFilename = $arrMaskHealthQuest[0];
		}
		else{
			$arrHealthQuestFilename = explode('.',$HealthQuestScanedFormName);
			$maskHealthQuestFilename = $HealthQuestScanedFormName;
			if($arrHealthQuestFilename[1] == "jpg"){
				$maskHealthQuestFilename = $arrHealthQuestFilename[0];
			}
		}
		$tree->addToArray($r_healthQuest,$maskHealthQuestFilename,$q_healthQuest,"showImg.php?intPatientWaitingId=$intPatientWaitingId&amp;patient_id=$patient_id&amp;intConsentTemplateId=$HealthQuestScanedTemplateId&amp;consentAllMultipleId=$consentAllMultipleId&amp;scan_consent_id=$HealthQuestScanedAutoId","consent_data","images/dhtmlgoodies_sheet.gif");
	}
	//END CODE TO SCAN HEALTH-QUEST FORM

//END CODE FOR HEALTH-QUEST FORM

$tree->writeCSS();
$tree->writeJavascript();
?>
</head>
<body>
<?php
$tree->drawTree();
?>

<script>
function setPNotesHeight() {
	//DO NOTHING
}	
</script>
</body>
</html>