<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("../common/conDb.php");
include("../common/iOLinkCommonFunction.php");
$previousWaitingId 					= $_REQUEST['previousWaitingId'];
$currentWatingId 					= $_REQUEST['currentWatingId'];
$msgInfo							= array();
$docAlreadyExist					= true;
$selQry 							= "SELECT * FROM iolink_consent_filled_form WHERE fldPatientWaitingId='".$previousWaitingId."'";
$selRes 							= imw_query($selQry) or die(imw_error());
if(imw_num_rows($selRes)) {
	while($selRow = imw_fetch_array($selRes)) {
		$surgery_consent_id 		= $selRow['surgery_consent_id'];
		$surgery_consent_name 		= $selRow['surgery_consent_name'];
		$surgery_consent_alias		= $selRow['surgery_consent_alias'];
		$surgery_consent_data		= $selRow['surgery_consent_data'];
		$surgery_consent_sign		= $selRow['surgery_consent_sign'];
		$ascId						= $selRow['ascId'];
		$confirmation_id			= $selRow['confirmation_id'];
		$form_status				= $selRow['form_status'];
		$eposted					= $selRow['eposted'];
		$consent_template_id		= $selRow['consent_template_id'];
		$consent_category_id		= $selRow['consent_category_id'];
		$left_navi_status			= $selRow['left_navi_status'];
		$consent_purge_status		= $selRow['consent_purge_status'];
		$sigStatus					= $selRow['sigStatus'];
		$signSurgeon1Activate		= $selRow['signSurgeon1Activate'];
		$signSurgeon1Id				= $selRow['signSurgeon1Id'];
		$signSurgeon1FirstName		= $selRow['signSurgeon1FirstName'];
		$signSurgeon1MiddleName		= $selRow['signSurgeon1MiddleName'];
		$signSurgeon1LastName		= $selRow['signSurgeon1LastName'];
		$signSurgeon1Status			= $selRow['signSurgeon1Status'];
		$signSurgeon1DateTime		= $selRow['signSurgeon1DateTime'];
		$signNurseActivate			= $selRow['signNurseActivate'];
		$signNurseId				= $selRow['signNurseId'];
		$signNurseFirstName			= $selRow['signNurseFirstName'];
		$signNurseMiddleName		= $selRow['signNurseMiddleName'];
		$signNurseLastName			= $selRow['signNurseLastName'];
		$signNurseStatus			= $selRow['signNurseStatus'];
		$signNurseDateTime			= $selRow['signNurseDateTime'];
		$signAnesthesia1Activate	= $selRow['signAnesthesia1Activate'];
		$signAnesthesia1Id			= $selRow['signAnesthesia1Id'];
		$signAnesthesia1FirstName	= $selRow['signAnesthesia1FirstName'];
		$signAnesthesia1MiddleName	= $selRow['signAnesthesia1MiddleName'];
		$signAnesthesia1LastName	= $selRow['signAnesthesia1LastName'];
		$signAnesthesia1Status		= $selRow['signAnesthesia1Status'];
		$signAnesthesia1DateTime	= $selRow['signAnesthesia1DateTime'];
		$signWitness1Activate		= $selRow['signWitness1Activate'];
		$signWitness1Id				= $selRow['signWitness1Id'];
		$signWitness1FirstName		= $selRow['signWitness1FirstName'];
		$signWitness1MiddleName		= $selRow['signWitness1MiddleName'];
		$signWitness1LastName		= $selRow['signWitness1LastName'];
		$signWitness1Status			= $selRow['signWitness1Status'];
		$signWitness1DateTime		= $selRow['signWitness1DateTime'];
		$fldPatientWaitingId		= $selRow['fldPatientWaitingId'];
		$patient_id					= $selRow['patient_id'];
		$consentSignedStatus		= $selRow['consentSignedStatus'];
		$consentGroupName			= $selRow['consentGroupName']; 
		
		$chkScnExistQry 			= "SELECT * FROM iolink_consent_filled_form WHERE fldPatientWaitingId='".$currentWatingId."' AND consent_template_id='".$consent_template_id."' AND consent_category_id='".$consent_category_id."'  AND consent_template_id!='0'";		
		$chkScnExistRes 			= imw_query($chkScnExistQry) or die(imw_error()).$msgInfo[]=imw_error();
		if(imw_num_rows($chkScnExistRes)>0) {
			$insrtUpdtQry 			= "UPDATE ";
			$whereQry				= "WHERE fldPatientWaitingId='".$currentWatingId."' AND consent_template_id='".$consent_template_id."' AND consent_category_id='".$consent_category_id."'  AND consent_template_id!='0'";	
		}else {
			$insrtUpdtQry 			= "INSERT INTO ";
			$whereQry				= "";
			$docAlreadyExist		= false;
		}
		$insrtUpdtQry 			   .= " iolink_consent_filled_form SET
										fldPatientWaitingId			= '".$currentWatingId."',
										patient_id					= '".$patient_id."',
										surgery_consent_name 		= '".addslashes($surgery_consent_name)."',
										surgery_consent_alias		= '".addslashes($surgery_consent_alias)."',
										surgery_consent_data		= '".addslashes($surgery_consent_data)."',
										surgery_consent_sign		= '".$surgery_consent_sign."',
										ascId						= '".$ascId."',
										confirmation_id				= '".$confirmation_id."',
										form_status					= '".$form_status."',
										eposted						= '".$eposted."',
										consent_template_id			= '".$consent_template_id."',
										consent_category_id			= '".$consent_category_id."',
										left_navi_status			= '".$left_navi_status."',
										consent_purge_status		= '".$consent_purge_status."',
										sigStatus					= '".$sigStatus."',
										signSurgeon1Activate		= '".$signSurgeon1Activate."',
										signSurgeon1Id				= '".$signSurgeon1Id."',
										signSurgeon1FirstName		= '".addslashes($signSurgeon1FirstName)."',
										signSurgeon1MiddleName		= '".addslashes($signSurgeon1MiddleName)."',
										signSurgeon1LastName		= '".addslashes($signSurgeon1LastName)."',
										signSurgeon1Status			= '".$signSurgeon1Status."',
										signSurgeon1DateTime		= '".$signSurgeon1DateTime."',
										signNurseActivate			= '".$signNurseActivate."',
										signNurseId					= '".$signNurseId."',
										signNurseFirstName			= '".addslashes($signNurseFirstName)."',
										signNurseMiddleName			= '".addslashes($signNurseMiddleName)."',
										signNurseLastName			= '".addslashes($signNurseLastName)."',
										signNurseStatus				= '".$signNurseStatus."',
										signNurseDateTime			= '".$signNurseDateTime."',
										signAnesthesia1Activate		= '".$signAnesthesia1Activate."',
										signAnesthesia1Id			= '".$signAnesthesia1Id."',
										signAnesthesia1FirstName	= '".addslashes($signAnesthesia1FirstName)."',
										signAnesthesia1MiddleName	= '".addslashes($signAnesthesia1MiddleName)."',
										signAnesthesia1LastName		= '".addslashes($signAnesthesia1LastName)."',
										signAnesthesia1Status		= '".$signAnesthesia1Status."',
										signAnesthesia1DateTime		= '".$signAnesthesia1DateTime."',
										signWitness1Activate		= '".$signWitness1Activate."',
										signWitness1Id				= '".$signWitness1Id."',
										signWitness1FirstName		= '".addslashes($signWitness1FirstName)."',
										signWitness1MiddleName		= '".addslashes($signWitness1MiddleName)."',
										signWitness1LastName		= '".addslashes($signWitness1LastName)."',
										signWitness1Status			= '".$signWitness1Status."',
										signWitness1DateTime		= '".$signWitness1DateTime."',
										consentSignedStatus			= '".$consentSignedStatus."',
										consentGroupName			= '".$consentGroupName."'																				
										$whereQry
									 ";
		$insrtUpdtRes				= imw_query($insrtUpdtQry) or die(imw_error()).$msgInfo[]=imw_error();						
	}
}

if(count($msgInfo)==0) {
	$msg = 'Document(s) Copied';	
	/*
	if($docAlreadyExist==true) { 
		$msg='Document(s) Already Copied'; 
	}else {  
		setReSyncroStatus($currentWatingId,'consentForm');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
	}*/
	setReSyncroStatus($currentWatingId,'consentForm');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
	echo $msg;	
}else {
	echo(implode("<br>",$msg_info)); 	
}
?>
