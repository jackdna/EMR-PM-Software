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

//------------------------------	LEFT SLIDER	------------------------------
$image12="images/orange_discharge_summary.gif";
//$maincolor=array($dark_green,$dark_green,$title_post_op_nursing_order,$bgdark_orange_physician,$bgdark_blue_local_anes,$title_op_room_record,$dark_green,$dark_green,$title_discharge_summary_sheet,$dark_green,$title_Amendments_notes);
if(!$pConfId) { $pConfId=$_REQUEST['pConfId']; }

$maincolor = array("images/dark_green.gif", "images/dark_green.gif","images/dark_yellow_nurs_record.gif","images/dark_orange_physician_recor.gif","images/dark_blue_anes_record.gif","images/navyblue_op_room.gif", "images/dark_green.gif","images/orange_discharge_summary.gif","images/dark_green.gif", "images/violet_physician_notes.gif","images/dark_green.gif");
$menuListArr = array('Consent Form','Pre-Op Health', 'Nursing Record', 'Physician Orders', 'Anesthesia', 'Operating Room','Surgical','Discharge Summary', 'Post Op Inst. Sheet', 'Physician Notes', 'ePostIt');

//$maincolor = array("images/dark_green.gif", "images/dark_green.gif","images/dark_yellow_nurs_record.gif","images/dark_orange_physician_recor.gif","images/dark_blue_anes_record.gif","images/navyblue_op_room.gif","images/dark_yellow.gif", "images/dark_green.gif","images/orange_discharge_summary.gif","images/dark_green.gif", "images/violet_physician_notes.gif","images/dark_green.gif");
//$menuListArr = array('Consent Form','Pre-Op Health', 'Nursing Record', 'Physician Orders', 'Anesthesia', 'Operating Room','Laser Procedure','Surgical','Discharge Summary', 'Post Op Inst. Sheet', 'Physician Notes', 'ePostIt');

//GET MULTIPLE CONSENT FORMS
$consentFormTemplateSelectQry = "select consent_id,consent_alias,consent_delete_status from `consent_forms_template` order by consent_id";
$consentFormTemplateSelectRes = imw_query($consentFormTemplateSelectQry) or die(imw_error()); 
$consentFormTemplateSelectNumRow = imw_num_rows($consentFormTemplateSelectRes);
$consentFormAliasArr = array($light_green);
if($consentFormTemplateSelectNumRow>0) {
	while($consentFormTemplateSelectRow = imw_fetch_array($consentFormTemplateSelectRes)) {
		
		$consentFormTemplateSelectConsentAlias = stripslashes($consentFormTemplateSelectRow['consent_alias']);	
		$consentFormTemplateDeleteStatus = $consentFormTemplateSelectRow['consent_delete_status'];
		
		//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
		$consentFormTemplateDeleteStatus;
		if($consentFormTemplateDeleteStatus=='true') {
			$consentFormTemplateSelectConsentAlias='';
		}
		//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
		
		
		$consentFormSelectQry = "select surgery_consent_alias from `consent_multiple_form` where  confirmation_id = '".$pConfId."' AND consent_template_id='".$consentFormTemplateSelectRow['consent_id']."'";
		$consentFormSelectRes = imw_query($consentFormSelectQry) or die(imw_error()); 
		$consentFormSelectNumRow = imw_num_rows($consentFormSelectRes);
		$consentFormSelectRow = imw_fetch_array($consentFormSelectRes);
		$consentFormSelectConsentAlias = $consentFormSelectRow['surgery_consent_alias'];	
		if(!$consentFormSelectConsentAlias) {
			if($consentFormTemplateSelectConsentAlias!='') {
				$consentFormSelectConsentAlias=$consentFormTemplateSelectConsentAlias;
			}
		}
		if($consentFormSelectConsentAlias!='') {
			$consentFormAliasArr[] = $consentFormSelectConsentAlias;
			$consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow['consent_id'];
		}	
	}
	
}
//END GET MULTIPLE CONSENT FORMS 
//$subMenuListArr[0] = array($light_green,'Surgery', 'HIPAA', 'Assign Benefits', 'Insurance Card');
$subMenuListArr[0] = $consentFormAliasArr;
$subMenuListArr[1] = array($light_green,'Health Questionnaire');
$subMenuListArr[2] = array($heading_post_op_nursing_order,'Pre-Op', 'Post-Op');
$subMenuListArr[3] = array($bgmid_orange_physician,'Pre-Op ', 'Post-Op ');
$subMenuListArr[4]  = array($bgmid_blue_local_anes,'MAC/Regional', 'Pre-Op General', 'General','General Nurse Notes');
$subMenuListArr[5]  = array($heading_op_room_record,'Intra-Op Record', 'Laser Procedure');
//$subMenuListArr[6]  = array($light_yellow, 'Laser Procedure');
$subMenuListArr[6]  = array($light_green,'Operative Report');
//$subMenuListArr[6]  = array($light_green,'QA Check List');
$subMenuListArr[7]  = array($heading_discharge_summary_sheet,'Discharge Summary');
$subMenuListArr[8]  = array($light_green,'Instruction Sheet');
$subMenuListArr[9]  = array($heading_Amendments_notes,'Amendments');
$subMenuListArr[10]  = array($light_green, 'ePostIt');
$sliderBar = "sliderBarLEFT";
$image = "imageLeft";
$leftCounter = count($subMenuListArr[0]);
include('slider2.php');

//------------------------------	LEFT SLIDER	------------------------------

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//------------------------------	RIGHT SLIDER   -------------------------------
$image = "imageRight";
$menuListArr = array('Amendments', 'Pre-Op Health', 'Nursing Record', 'Physician Order', 'Anesthesia', 'Operating Room', 'Surgical', 'Discharge Summary', 'Post-Op Inst. Sheet', 'Consent Forms');
$subMenuListArr[0] = array('Amendment');
$subMenuListArr[1] = array('Questionnaire');
$subMenuListArr[2] = array('Pre-OP', 'Post-Op');
$subMenuListArr[3] = array('Pre-OP', 'Post-Op');
$subMenuListArr[4]  = array('MAC/Regional');
$subMenuListArr[5]  = array('Intra-Op Record', 'Laser Procedure');
$subMenuListArr[6]  = array('Cataract');
$subMenuListArr[7]  = array('Discharge Summary Sheet');
$subMenuListArr[8]  = array('Instruction sheet');
$subMenuListArr[9]  = array('Surgery', 'HIPAA', 'Assign Benefits');

$sliderBar = "sliderBarRight";
$rightCounter = count($subMenuListArr[0]);
include('slider.php');
//------------------------------	RIGHT SLIDER	------------------------------

?>


