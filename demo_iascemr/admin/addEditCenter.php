<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
if($_POST){
	$surgeryCenterId = $_POST['surgeryCenterId'];
	$sergeryCenerName = addslashes($_POST['sergeryCenerName']);
	$sergeryCenerAddress = addslashes($_POST['sergeryCenerAddress']);
	$sergeryCenerAddress2 = addslashes($_POST['sergeryCenerAddress2']);
	
	$elem_city = addslashes($_POST['elem_city']);
	$elem_state = $_POST['elem_state'];
	$elem_zip = $_POST['elem_zip'];
		
	$sergeryCenerPhone = $_POST['sergeryCenerPhone'];
	$contactName = addslashes($_POST['sergeryCenerContactName']);
	$sergeryCenerFax = $_POST['sergeryCenerFax'];
	$sergeryCenerEmail = $_POST['sergeryCenerEmail'];
	$sergeryCenerNPI = $_POST['sergeryCenerNPI'];
	$sergeryCenerFederal = $_POST['sergeryCenerFederal'];
	$elem_billLocation = $_POST['elem_billLocation'];
	$elem_diagnosis_code_type = $_POST['elem_diagnosis_code_type'];
	$elem_acceptAssignment = $_POST['elem_acceptAssignment'];
	$elem_documentsExpireDays = $_POST['elem_documentsExpireDays'];
	$elem_suppliesHostName 		= trim($_POST['elem_suppliesHostName']);
	$elem_suppliesPortNumber 	= trim($_POST['elem_suppliesPortNumber']);
	$elem_suppliesUsername 		= trim($_POST['elem_suppliesUsername']);
	$elem_suppliesPassword 		= $_POST['elem_suppliesPassword'];
	$elem_suppliesPathFromSftp 	= trim($_POST['elem_suppliesPathFromSftp']);
	$elem_suppliesPathToSftp 	= trim($_POST['elem_suppliesPathToSftp']);
	$elem_maxRecentlyUsedPass = $_POST['elem_maxRecentlyUsedPass'];
	$elem_maxLoginAttempts = $_POST['elem_maxLoginAttempts'];
	$elem_maxPassExpiresDays = $_POST['elem_maxPassExpiresDays'];
	$elem_ascId = $_POST['elem_ascId'];	
	$elem_show_religion	=	$_POST['elem_show_religion'];
	$elem_safety_check_list	= $_POST['elem_safety_check_list'];

	$elem_oproom	=	$_POST['elem_oproom'];
	$elem_macAnes	=	$_POST['elem_macAnes'];
	$elem_genAnes	=	$_POST['elem_genAnes'];
	$elem_transferFollowup	=	$_POST['elem_transferFollowup'];
	$elem_vital_time_slot	=	$_POST['elem_vital_time_slot'];
	$elem_peer_review	=	$_POST['elem_peer_review'];
	$elem_fire_risk_analysis	=	$_POST['elem_fire_risk_analysis'];
	
	$elem_loginLegalNotice = addslashes($_POST['editor1']);	
	$elem_discharge_disclaimer = addslashes($_POST['elem_discharge_disclaimer']);
	$elem_finalizeDays = $_POST['elem_finalizeDays'];
	$elem_finalizeWarningDays = $_POST['elem_finalizeWarningDays'];
	$logoMode = $_REQUEST['logoMode'];
	$large_top = $_POST['large_top'];
	$large_bottom = $_POST['large_bottom'];
	$large_inner = $_POST['large_inner'];
	$small_top = $_POST['small_top'];
	$small_bottom = $_POST['small_bottom'];
	$small_inner = $_POST['small_inner'];
	$group_institution_list = $_POST['group_institution_list'];
	$group_anesthesia_list = $_POST['group_anesthesia_list'];
	$group_practice_list = $_POST['group_practice_list'];
	$elem_small_label_enable_surgeon		=	$_POST['elem_small_label_enable_surgeon'];
	$elem_small_label_enable_procedure		=	$_POST['elem_small_label_enable_procedure'];
	$elem_small_label_enable_patient_mrn 	=	$_POST['elem_small_label_enable_patient_mrn'];
	$elem_small_label_enable_patient_gender	=	$_POST['elem_small_label_enable_patient_gender'];
	$elem_small_label_enable_patient_dos	=	$_POST['elem_small_label_enable_patient_dos'];
	$elem_small_label_enable_patient_dob	=	$_POST['elem_small_label_enable_patient_dob'];
	$elem_small_label_enable_site			=	$_POST['elem_small_label_enable_site'];
	$elem_large_label_enable_surgeon		=	$_POST['elem_large_label_enable_surgeon'];
	$elem_large_label_enable_procedure		=	$_POST['elem_large_label_enable_procedure'];
	$elem_large_label_enable_patient_mrn 	=	$_POST['elem_large_label_enable_patient_mrn'];
	$elem_large_label_enable_patient_gender	=	$_POST['elem_large_label_enable_patient_gender'];
	$elem_large_label_enable_patient_dos	=	$_POST['elem_large_label_enable_patient_dos'];
	$elem_large_label_enable_patient_dob	=	$_POST['elem_large_label_enable_patient_dob'];
	$elem_large_label_enable_site	=	$_POST['elem_large_label_enable_site'];
	
	$asa_4 = isset($_POST['elem_asa_4']) ? $_POST['elem_asa_4'] : 0;
	$autofill_modifiers = isset($_POST['elem_autofill_modifiers']) ? $_POST['elem_autofill_modifiers'] : 0;
	$sx_plan_sheet_review = isset($_POST['elem_sx_plan_sheet_review']) ? $_POST['elem_sx_plan_sheet_review'] : 0;
	$anes_mallampetti_score = isset($_POST['elem_anes_mallampetti_score']) ? $_POST['elem_anes_mallampetti_score'] : 0;
	
	if($logoMode=='delete') {
		$logoName = "no-file.jpg";
		$tmp = "../images/whiteOneByOne.jpg";
		$logoType = "images/jpg";		
		
		$PSize = @filesize($tmp);
		$oTempFile = fopen($tmp, "r");		
		$elem_surgeryCenterLogo = addslashes(fread($oTempFile, $PSize));
		
		$sql = "UPDATE surgerycenter SET ";
		$sql .= "surgeryCenterLogo = '$elem_surgeryCenterLogo',
				logoName = '$logoName',
				logoType = '$logoType'";
		$sql .= " WHERE  surgeryCenterId = '$surgeryCenterId'";
		//echo $sql;
		$sqlQry = imw_query($sql);// or die(imw_error());
		?>
<script>
	top.frames[0].frames[0].location = 'surgeryCenter.php';
</script>
		<?php
	}
	
	if(!empty($_FILES["elem_surgeryCenterLogo"]["name"])){
		$logoName = $_FILES["elem_surgeryCenterLogo"]["name"];
		$tmp = $_FILES["elem_surgeryCenterLogo"]["tmp_name"];
		$logoType = $_FILES["elem_surgeryCenterLogo"]["type"];
		/*if(($logoType == "image/pjpeg") || ($logoType == "image/gif")){
			$fileFormat = "Known Format";
			//UPLOAD
				move_uploaded_file($_FILES["elem_surgeryCenterLogo"]["tmp_name"], "uploadedImage/".$_FILES["elem_surgeryCenterLogo"]["name"]);
			//UPLOAD
		}else{
			$_FILES["elem_surgeryCenterLogo"]["name"] = '';
			$fileFormat = "Un-Known Format";
		}*/
		
		$PSize = $_FILES["elem_surgeryCenterLogo"]["size"];
		$oTempFile = fopen($_FILES["elem_surgeryCenterLogo"]["tmp_name"], "r");		
		$elem_surgeryCenterLogo = addslashes(fread($oTempFile, $PSize));
		
		
			
	}
	if($surgeryCenterId){
		$sql = "UPDATE surgerycenter SET ";
	}else{
		$sql = "INSERT INTO surgerycenter SET ";
	}
	$sql .= "name = '$sergeryCenerName',
			address = '$sergeryCenerAddress',
			address2 = '$sergeryCenerAddress2',
			city = '$elem_city',
			state = '$elem_state',
			zip = '$elem_zip',
			phone = '$sergeryCenerPhone', 
			contactName = '$contactName',
			fax = '$sergeryCenerFax', 
			email = '$sergeryCenerEmail',
			npi = '$sergeryCenerNPI',
			federalEin = '$sergeryCenerFederal',
			billLocation = '$elem_billLocation',
			diagnosis_code_type = '$elem_diagnosis_code_type',
			ascId_present = '$elem_ascId',
			acceptAssignment = '$elem_acceptAssignment',
			show_religion = '$elem_show_religion',
			safety_check_list = '$elem_safety_check_list',
			documentsExpireDays = '$elem_documentsExpireDays',
			vital_sign_oproom = '$elem_oproom',
			vital_sign_macAnes = '$elem_macAnes',
			vital_sign_genAnes = '$elem_genAnes',
			vital_sign_transferFollowup = '$elem_transferFollowup',
			vital_time_slot = '$elem_vital_time_slot',
			peer_review = '$elem_peer_review',
			fire_risk_analysis = '$elem_fire_risk_analysis',
			asa_4 = '".$asa_4."',
			autofill_modifiers = '".$autofill_modifiers."',
			sx_plan_sheet_review = '".$sx_plan_sheet_review."',
			anes_mallampetti_score = '".$anes_mallampetti_score."',
			small_label_enable_surgeon 			= '".$elem_small_label_enable_surgeon."',
			small_label_enable_procedure 		= '".$elem_small_label_enable_procedure."',
			small_label_enable_patient_mrn 		= '".$elem_small_label_enable_patient_mrn."',
			small_label_enable_patient_gender	= '".$elem_small_label_enable_patient_gender."',
			small_label_enable_patient_dos		= '".$elem_small_label_enable_patient_dos."',
			small_label_enable_patient_dob		= '".$elem_small_label_enable_patient_dob."',
			small_label_enable_site				= '".$elem_small_label_enable_site."',
			large_label_enable_surgeon 			= '".$elem_large_label_enable_surgeon."',
			large_label_enable_procedure 		= '".$elem_large_label_enable_procedure."',
			large_label_enable_patient_mrn 		= '".$elem_large_label_enable_patient_mrn."',
			large_label_enable_patient_gender	= '".$elem_large_label_enable_patient_gender."',
			large_label_enable_patient_dos		= '".$elem_large_label_enable_patient_dos."',
			large_label_enable_patient_dob		= '".$elem_large_label_enable_patient_dob."',
			large_label_enable_site		= '".$elem_large_label_enable_site."',
			discharge_disclaimer		= '".$elem_discharge_disclaimer."',
			";
			
	if(!empty($_FILES["elem_surgeryCenterLogo"]["name"])){
		$sql .= "surgeryCenterLogo = '$elem_surgeryCenterLogo',
				logoName = '$logoName',
				logoType = '$logoType',";
	}			
	$sql .= "hippaCompliancy = '',
			suppliesHostName 		= '".$elem_suppliesHostName."',
			suppliesPortNumber 		= '".$elem_suppliesPortNumber."',
			suppliesUsername 		= '".$elem_suppliesUsername."',
			suppliesPassword 		= '".$elem_suppliesPassword."',
			suppliesPathFromSftp	= '".$elem_suppliesPathFromSftp."',
			suppliesPathToSftp		= '".$elem_suppliesPathToSftp."',
			maxRecentlyUsedPass = '$elem_maxRecentlyUsedPass',
			maxLoginAttempts = '$elem_maxLoginAttempts',
			maxPassExpiresDays = '$elem_maxPassExpiresDays',
			loginLegalNotice = '$elem_loginLegalNotice',
			finalizeDays = '$elem_finalizeDays',
			finalizeWarningDays = '$elem_finalizeWarningDays'";
	if($surgeryCenterId){
		$sql .= " WHERE  surgeryCenterId = '$surgeryCenterId'";
	}
	$sqlQry = imw_query($sql);
	if($surgeryCenterId && $sqlQry)
	{
		echo "<script>top.frames[0].alert_msg('update');</script>";
	}
	else
	{
		echo "<script>top.frames[0].alert_msg('success');</script>";
	}
	$sql_label="UPDATE label_size SET 
					large_top = '$large_top',
					large_bottom = '$large_bottom',
					large_inner = '$large_inner',
					small_top = '$small_top',
					small_bottom = '$small_bottom',
					small_inner = '$small_inner'
					where l_id='1'";
	$sqlQry_label = imw_query($sql_label);
	
	$sqlUpdateUser="UPDATE users SET hippaReviewedStatus = 'Changed'";
	$sqlQryUpdateUser = imw_query($sqlUpdateUser);
}
?>
<script>
	top.frames[0].frames[0].location = 'surgeryCenter.php';
</script>