<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
//SLIDER TOP POSITION $top = 153;	
$top = 0;	
$left = -185;	//SLIDER LET POSITION
$width = 175;	//SLIDER Width
//$right=177;
$right = -259;	//SLIDER LET POSITION
if(!$pConfId) { $pConfId=$_REQUEST['pConfId']; }

//gurleen laser
	$LeftOpLaserSlider='Operating Room';
	$procedureChkSliderLeftQry = "SELECT * FROM patientconfirmation WHERE patientConfirmationId = '".$pConfId."'";
	$procedureChkSliderLeftQryRes = imw_query($procedureChkSliderLeftQry);
	$procedureChkSliderLeftNumRow = imw_num_rows($procedureChkSliderLeftQryRes);
	if($procedureChkSliderLeftNumRow>0){
		$procedureChkSliderLeftQryRow = imw_fetch_array($procedureChkSliderLeftQryRes);
		$SliderLeft_patientPrimProc = $procedureChkSliderLeftQryRow['patient_primary_procedure'];
		$SliderLeft_patientPrimProcId = $procedureChkSliderLeftQryRow['patient_primary_procedure_id'];
	
		$primary_procedureSliderLeftQry = "SELECT * FROM procedures WHERE name = '".addslashes($SliderLeft_patientPrimProc)."' OR procedureAlias = '".addslashes($SliderLeft_patientPrimProc)."'";
		$primary_procedureSliderLeftRes = imw_query($primary_procedureSliderLeftQry);
		$primary_procedureSliderLeftNumRow = imw_num_rows($primary_procedureSliderLeftRes);
		if($primary_procedureSliderLeftNumRow<=0) {
			$primary_procedureSliderLeftQry = "SELECT * FROM procedures WHERE procedureId = '".$SliderLeft_patientPrimProcId."'";
			$primary_procedureSliderLeftRes = imw_query($primary_procedureSliderLeftQry);
			$primary_procedureSliderLeftNumRow = imw_num_rows($primary_procedureSliderLeftRes);
		}
		$patient_primary_procedure_categoryLeftID='';
		if($primary_procedureSliderLeftNumRow>0){
			$primary_procedureSliderLeftRow = imw_fetch_array($primary_procedureSliderLeftRes);
			$patient_primary_procedure_categoryLeftID = $primary_procedureSliderLeftRow['catId'];
			if($patient_primary_procedure_categoryLeftID==2){
				$LeftOpLaserSlider='Laser Procedure'; 
			}	
		}
	}

//gurleen lasers



//------------------------------	LEFT SLIDER	------------------------------
$image12="images/orange_discharge_summary.gif";
//$maincolor=array($dark_green,$dark_green,$title_post_op_nursing_order,$bgdark_orange_physician,$bgdark_blue_local_anes,$title_op_room_record,$dark_green,$dark_green,$title_discharge_summary_sheet,$dark_green,$title_Amendments_notes);

$maincolor = array("images/dark_green.gif", "images/dark_green.gif","images/dark_yellow_nurs_record.gif","images/dark_orange_physician_recor.gif","images/dark_blue_anes_record.gif","images/navyblue_op_room.gif", "images/dark_green.gif","images/orange_discharge_summary.gif","images/dark_green.gif", "images/violet_physician_notes.gif","images/dark_green.gif");
$menuListArr = array('Forms','Pre-Op Health', 'Nursing Record', 'Physician Orders', 'Anesthesia', $LeftOpLaserSlider,'Surgical','Discharge Summary', 'Post Op Inst. Sheet', 'Transfer & Follow-up','Amendment Notes', 'ePostIt');

//$maincolor = array("images/dark_green.gif", "images/dark_green.gif","images/dark_yellow_nurs_record.gif","images/dark_orange_physician_recor.gif","images/dark_blue_anes_record.gif","images/navyblue_op_room.gif","images/dark_yellow.gif", "images/dark_green.gif","images/orange_discharge_summary.gif","images/dark_green.gif", "images/violet_physician_notes.gif","images/dark_green.gif");
//$menuListArr = array('Consent Form','Pre-Op Health', 'Nursing Record', 'Physician Orders', 'Anesthesia', 'Operating Room','Laser Procedure','Surgical','Discharge Summary', 'Post Op Inst. Sheet', 'Physician Notes', 'ePostIt');

//GET MULTIPLE CONSENT FORMS
$consentFormTemplateSelectQry = "select consent_id,consent_alias,consent_delete_status from `consent_forms_template` WHERE consent_delete_status!='true' order by consent_id";
$consentFormTemplateSelectRes = imw_query($consentFormTemplateSelectQry) or die(imw_error()); 
$consentFormTemplateSelectNumRow = imw_num_rows($consentFormTemplateSelectRes);
$consentFormAliasArr = array($light_green);
if($consentFormTemplateSelectNumRow>0) {
	while($consentFormTemplateSelectRow = imw_fetch_array($consentFormTemplateSelectRes)) {
		
		$consentFormTemplateSelectConsentAlias = stripslashes($consentFormTemplateSelectRow['consent_alias']);	
		$consentFormTemplateDeleteStatus = $consentFormTemplateSelectRow['consent_delete_status'];
		
		if($consentFormTemplateSelectConsentAlias!='') {
			$consentFormAliasArr[] = $consentFormTemplateSelectConsentAlias;
			$consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow['consent_id'];
		}	
	}
	
}
//END GET MULTIPLE CONSENT FORMS 
//$subMenuListArr[0] = array($light_green,'Surgery', 'HIPAA', 'Assign Benefits', 'Insurance Card');
//print '<pre>';
//print_r($consentFormAliasArr);
$subMenuListArr[0] = $consentFormAliasArr;
$subMenuListArr[1] = array($light_green,'Health Questionnaire','H & P Clearance');
$subMenuListArr[2] = array($heading_post_op_nursing_order,'Pre-Op',  'Pre-Op Aldrete', 'Post-Op', 'Post-Op Aldrete');
$subMenuListArr[3] = array($bgmid_orange_physician,'Pre-Op ','Post-Op ' );
$subMenuListArr[4]  = array($bgmid_blue_local_anes,'MAC/Regional', 'Pre-Op General', 'General','General Nurse Notes');
$subMenuListArr[5]  = array($heading_op_room_record,'Intra-Op Record', 'Laser Procedure','Injection/Miscellaneous');
//$subMenuListArr[6]  = array($light_yellow, 'Laser Procedure');
$subMenuListArr[6]  = array($light_green,'Operative Report');
//$subMenuListArr[6]  = array($light_green,'QA Check List');
$subMenuListArr[7]  = array($heading_discharge_summary_sheet,'Discharge Summary');
$subMenuListArr[8]  = array($light_green,'Instruction Sheet','Medication Reconciliation Sheet');
$subMenuListArr[9]  = array($light_green,'Transfer & Follow-up');
$subMenuListArr[10]  = array($heading_Amendments_notes,'Amendments');
$subMenuListArr[11]  = array($light_green, 'ePostIt');
$sliderBar = "sliderBarLEFT";
$image = "imageLeft";
$leftCounter = count($subMenuListArr[0]);
include('slider2.php');

//------------------------------	LEFT SLIDER	------------------------------

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//------------------------------	RIGHT SLIDER   -------------------------------
$image = "imageRight";
$menuListArr = array('Amendments', 'Pre-Op Health', 'Nursing Record', 'Physician Order', 'Anesthesia', 'Operating Room', 'Surgical', 'Discharge Summary', 'Post-Op Inst. Sheet', 'Transfer & Follow-up','Consent Forms');
$subMenuListArr[0] = array('Amendment');
$subMenuListArr[1] = array('Questionnaire');
$subMenuListArr[2] = array('Pre-Op', 'Pre-Op Aldrete', 'Post-Op', 'Post-Op Aldrete');
$subMenuListArr[3] = array('Pre-Op', 'Post-Op');
$subMenuListArr[4]  = array('MAC/Regional');
$subMenuListArr[5]  = array('Intra-Op Record', 'Laser Procedure','Injection/Miscellaneous');
$subMenuListArr[6]  = array('Cataract');
$subMenuListArr[7]  = array('Discharge Summary Sheet');
$subMenuListArr[8]  = array('Instruction Sheet','Medication Reconciliation Sheet');
$subMenuListArr[9]  = array('Transfer & Follow-up');
$subMenuListArr[10]  = array('Surgery', 'HIPAA', 'Assign Benefits');


$sliderBar = "sliderBarRight";
$rightCounter = count($subMenuListArr[0]);
//include 'patientFormModal.php';

include('slider.php');
//------------------------------	RIGHT SLIDER	------------------------------

?>