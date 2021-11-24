<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include("common/conDb.php");
include('connect_imwemr.php');
include("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<!--<style>
form li div,form li span, body{ margin:0px; padding:0px;}
</style>-->
<?php
$spec = '
</head>
<body>';
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
$pConfId = $_REQUEST['pConfId'];
$multiwin = $_REQUEST['multiwin']; // FOR OPENING MULTIPLE WINDOW
$loginUserId = $_SESSION['loginUserId'];
$loginUserType = $_SESSION['loginUserType'];
$reConfirmId = $_REQUEST['reConfirmId'];

//START GET SCHEDULE STATUS ID FROM
$imwSchStatusIdArr = array();
$imwSchStatusQry = "SELECT id, status_name, status FROM ".$imw_db_name.".schedule_status ORDER BY id";
$imwSchStatusRes = imw_query($imwSchStatusQry) or die($imwSchStatusQry.' Error found @ Line No. '.(__LINE__).': '.imw_error());				
if(imw_num_rows($imwSchStatusRes)>0) {
	while($imwSchStatusRow 	= imw_fetch_assoc($imwSchStatusRes)) {
		$imwSchStatusId 	= $imwSchStatusRow["id"];
		$imwSchStatusName 	= trim(ucwords(strtolower(stripslashes($imwSchStatusRow["status_name"]))));
		$imwSchActiveStatus	= stripslashes($imwSchStatusRow["status"]);
		$imwSchStatusIdArr[$imwSchStatusName] = $imwSchStatusId;	
	}
}

$andstubIdQry=" ";
if(!$pConfId) {
	$andstubIdQry = " AND stub_id = '".$_REQUEST["stub_id"]."' AND stub_id != '0' ";	
}
$demographicSaveVisi='hidden';
if($loginUserType=='Staff' || $loginUserType=='Nurse' || $loginUserType=='Coordinator' || $loginUserType=='Anesthesiologist') {  
	$demographicSaveVisi = 'visibile';
}

//GET LOGED IN USER NAME
$getUserDetails = $objManageData->getRowRecord('users', 'usersId', $loginUserId);
$userLogedIn = $getUserDetails->fname.' '.$getUserDetails->lname;
$loggedInUserName = trim($getUserDetails->lname.', '.$getUserDetails->fname.' '.$getUserDetails->mname);
//$LoginUserName = $getUserDetails->fname.' '.$getUserDetails->mname.' '.$getUserDetails->lname;

// GET SURGERY CENTER DETAILS
$getSurgeryCenterDetails = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', '1');
$SurgeryCenterName = $getSurgeryCenterDetails->name;
$maxDaysToExpire	=	$getSurgeryCenterDetails->maxPassExpiresDays;	
$show_religion = (int) $getSurgeryCenterDetails->show_religion;

if($pConfId && !$reConfirmId){
	$getPatientId = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
		$patient_id = $getPatientId->patientId;
	?>
	<script>				
		var pId = <?php echo $patient_id; ?>;
		var pConfId = <?php echo $pConfId; ?>;
		var multiwin = '<?php echo $multiwin; ?>';
		location.href='mainpage.php?patient_id='+pId+'&pConfId='+pConfId+'&multiwin='+multiwin;
	</script>	
	<?php
}

//FUNCTION EDIT BY SURINDER 
function dateDiff($dformat, $endDate, $beginDate){
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;}

//END  FUNCTION EDIT BY SURINDER


// FIRST VIEW CHECKS
	$stubTableDetails = $objManageData->getRowRecord('stub_tbl', 'stub_id', $_REQUEST['stub_id']);
	$patient_first_name = $stubTableDetails->patient_first_name;
	$patient_middle_name = $stubTableDetails->patient_middle_name;
	$patient_last_name = $stubTableDetails->patient_last_name;
	$patient_sex = $stubTableDetails->patient_sex;
	$patient_site = $stubTableDetails->site;
	$sec_patient_site = $stubTableDetails->stub_secondary_site;
	$ter_patient_site = $stubTableDetails->stub_tertiary_site;
	$surgeon_fname = $stubTableDetails->surgeon_fname;
	$surgeon_mname = $stubTableDetails->surgeon_mname;
	$surgeon_lname = $stubTableDetails->surgeon_lname;
	
	if($surgeon_mname){
		$surgeon_mname = ' '.$surgeon_mname;
	}
	$surgeon_name = $surgeon_fname.$surgeon_mname.' '.$surgeon_lname;
	
	$surgery_time = $stubTableDetails->surgery_time;
	$pickup_time =  $stubTableDetails->pickup_time;
	$arrival_time =  $stubTableDetails->arrival_time;
	//$asc_id = $stubTableDetails->ascId;
	$patient_address1 = $stubTableDetails->patient_street1;
	$patient_address2 = $stubTableDetails->patient_street2;
	$patient_home_phone = $stubTableDetails->patient_home_phone;
	$patient_work_phone = $stubTableDetails->patient_work_phone;
	$patient_city = $stubTableDetails->patient_city;
	$patient_state = $stubTableDetails->patient_state;
	$patient_zip = $stubTableDetails->patient_zip;
	$imwPatientId = $stubTableDetails->imwPatientId;
	$patient_language = $stubTableDetails->patient_language;
	$patient_race = $stubTableDetails->patient_race;
	$patient_religion = $stubTableDetails->patient_religion;
	$patient_ethnicity = $stubTableDetails->patient_ethnicity;
	
	$iolink_patient_in_waiting_id = $stubTableDetails->iolink_patient_in_waiting_id;
	if(!$iolink_patient_in_waiting_id) {$iolink_patient_in_waiting_id	=	$_REQUEST['iolink_patient_in_waiting_id'];}
	
	$patient_id_stub = $stubTableDetails->patient_id_stub;
	$patient_dob_temp = $stubTableDetails->patient_dob;
		$patient_dob_split = explode("-",$patient_dob_temp);
		$patient_dob = $patient_dob_split[1]."-".$patient_dob_split[2]."-".$patient_dob_split[0];
	$patient_prim_proc = trim(stripslashes($stubTableDetails->patient_primary_procedure));
	if($patient_prim_proc) {
		if(strpos($patient_prim_proc, 'Right Eye') !== false){
			$patient_prim_procExplode  = explode('Right Eye',$patient_prim_proc);
			$patient_prim_proc = trim(trim($patient_prim_procExplode[0]).' '.trim($patient_prim_procExplode[1]));
			$patient_site = "right";
		}
		if(strpos($patient_prim_proc, 'Left Eye') !== false){
			$patient_prim_procExplode  = explode('Left Eye',$patient_prim_proc);
			$patient_prim_proc = trim(trim($patient_prim_procExplode[0]).' '.trim($patient_prim_procExplode[1]));
			$patient_site = "left";
		}
		if(strpos($patient_prim_proc, 'Both Eye') !== false){
			$patient_prim_procExplode  = explode('Both Eye',$patient_prim_proc);
			$patient_prim_proc = trim(trim($patient_prim_procExplode[0]).' '.trim($patient_prim_procExplode[1]));
			$patient_site = "both";
		}
	}
	
	$patient_anes_fname = $stubTableDetails->anesthesiologist_fname;
	$patient_anes_mname = $stubTableDetails->anesthesiologist_mname;
	$patient_anes_lname = $stubTableDetails->anesthesiologist_lname;

	if($patient_anes_mname) {
		$patient_anes_mname = ' '.$patient_anes_mname;
	}
	$patient_anes_name = $patient_anes_fname.$patient_anes_mname.' '.$patient_anes_lname;
	
	$patient_dos_temp = $stubTableDetails->dos;
	$patient_dos_split = explode("-",$patient_dos_temp);
	$patient_dos = $patient_dos_split[1]."-".$patient_dos_split[2]."-".$patient_dos_split[0];

	if(!$imwPatientId) {$imwPatientId	=	$_REQUEST['imwPatientId'];}
	if(!$patient_id_stub) {$patient_id_stub	=	$_REQUEST['patient_id'];}
	
	
	$imw_patient_image_path = '';
	if($imwPatientId) {
		@imw_close($link);
		include("connect_imwemr.php");
		$iDocPtQry = "SELECT p_imagename FROM patient_data WHERE pid = '".$imwPatientId."' and p_imagename !='' LIMIT 0,1";
		$iDocPtRes = imw_query($iDocPtQry) or die(imw_error());	
		if(imw_num_rows($iDocPtRes)>0) {
			$iDocPtRow = imw_fetch_array($iDocPtRes);
			if(trim($iDocPtRow["p_imagename"])) {
				$imw_patient_image_path = $iDocPtRow["p_imagename"];
			}
		}
		@imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
		include("common/conDb.php");
	}
	
	
	//CHECK PATIENT IF ALREADY CONFIRMED 
	if(!$reConfirmId && $_POST["pt_conf_submit"]==""){
		
		if($patient_id_stub) {
			$patientMatchConfirmStr 	= "SELECT patient_id, patient_image_path FROM patient_data_tbl 
											WHERE patient_id = '".$patient_id_stub."'";
		}else {
			$patientMatchConfirmStr 	= "SELECT patient_id, patient_image_path FROM patient_data_tbl 
											WHERE imwPatientId = '".$imwPatientId."' and imwPatientId!=''";
			
		}	
		$patientMatchConfirmQry 	= imw_query($patientMatchConfirmStr);
		$patientMatchConfirmRows 	= imw_num_rows($patientMatchConfirmQry);
		if($patientMatchConfirmRows<=0){
			$patientMatchConfirmStr 	= "SELECT patient_id, patient_image_path FROM patient_data_tbl 
											WHERE patient_fname = '".trim(addslashes($patient_first_name))."'
											AND patient_lname 	= '".trim(addslashes($patient_last_name))."'
											AND zip 		  	= '".trim(addslashes($patient_zip))."'
											AND date_of_birth 	= '".$patient_dob_temp."'";
			$patientMatchConfirmQry 	= imw_query($patientMatchConfirmStr);
			$patientMatchConfirmRows 	= imw_num_rows($patientMatchConfirmQry);
		}
		$patientDataConfirmRow 		= imw_fetch_array($patientMatchConfirmQry);
		if($patientMatchConfirmRows>0){
			$PatientDataMatchId 	= $patientDataConfirmRow["patient_id"];
			$scemr_patient_image_path = $patientDataConfirmRow["patient_image_path"];
			
			//$chkPatientAlreadyConfirmedQry 		= "SELECT patientConfirmationId FROM patientconfirmation where patientId='$PatientDataMatchId' and dos='$patient_dos_temp' ".$andstubIdQry;
			
			$chkPatientAlreadyConfirmedQry = "SELECT pc.patientConfirmationId FROM patientconfirmation pc 
											  INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.stub_id = '".$_REQUEST["stub_id"]."')
											  WHERE pc.patientId='$PatientDataMatchId'";
			
			
			$chkPatientAlreadyConfirmedRes 		= imw_query($chkPatientAlreadyConfirmedQry) or die(imw_error());
			$chkPatientAlreadyConfirmedNumRow 	= imw_num_rows($chkPatientAlreadyConfirmedRes);
			if($chkPatientAlreadyConfirmedNumRow>0) {
			?>
			<script>
				//alert('Patient already confirmed');
				//document.getElementById('divConfirmAlert').style.display = 'block';
			</script>
			<?php
			}
		}	
	}	
	//CHECK PATIENT IF ALREADY CONFIRMED
	
	$assist_by_trans = $stubTableDetails->assisted_by_translator;
	$patient_sec_proc = trim($stubTableDetails->patient_secondary_procedure);
	$patient_ter_proc = trim($stubTableDetails->patient_tertiary_procedure);
	
	//$patient_conf_nurse = $stubTableDetails->confirming_nurse;
	$patient_conf_nurse_fname = $stubTableDetails->confirming_nurse_fname;
	$patient_conf_nurse_mname = $stubTableDetails->confirming_nurse_mname;
	$patient_conf_nurse_lname = $stubTableDetails->confirming_nurse_lname;
	if($patient_conf_nurse_mname) {
		$patient_conf_nurse_mname = ' '.$patient_conf_nurse_mname;
	}
	$patient_conf_nurse = $patient_conf_nurse_fname.$patient_conf_nurse_mname.' '.$patient_conf_nurse_lname;

	$patient_status = $stubTableDetails->patient_status;
	
	$patientCheckInOutTime = "";
	if($patient_status=="Checked-In") {
		$patientCheckInOutTime = $stubTableDetails->checked_in_time;
	}else if($patient_status=="Checked-Out") {
		$patientCheckInOutTime = $stubTableDetails->checked_out_time;
	}
	// GETTING SURGEON ID FROM USERS 
	//if(!$surgeonId){
		$showDropSurgeonsDown = 'true';
	//}
	
		// GETTING PRIMARY PROCEDURE ID FROM PROCEDURES 
		
	$getProcedureIdQry = imw_query("SELECT * FROM procedures WHERE del_status!='yes' AND ((name != '' AND trim(name) = '$patient_prim_proc') OR (procedureAlias != '' AND trim(procedureAlias) = '$patient_prim_proc'))");
	if(imw_num_rows($getProcedureIdQry)>0){
		$getProcedureIdRow = imw_fetch_array($getProcedureIdQry);
	    $PrimaryProcedureId = $getProcedureIdRow['procedureId'];
		$patient_prim_proc = trim($getProcedureIdRow['name']); 
	}
	$showPrimaryProcedureDropDown = 'true';
	/*
	if(!$PrimaryProcedureId){
		$showPrimaryProcedureDropDown = 'true';
	}
	if($reConfirmId){ //IF PATIENT IS RE-CONFIRMING
		$showPrimaryProcedureDropDown = 'true';
	}*/

   // GETTING Secondary PROCEDURE ID FROM PROCEDURES 
    $getSecProcedureIdQry = imw_query("SELECT * FROM procedures WHERE del_status!='yes' AND ((name != '' AND trim(name) = '$patient_sec_proc') OR (procedureAlias != '' AND trim(procedureAlias) = '$patient_sec_proc'))");
	if(imw_num_rows($getSecProcedureIdQry)>0){
		$getSecProcedureIdRow = imw_fetch_array($getSecProcedureIdQry);
	    $SecondaryProcedureId = $getSecProcedureIdRow['procedureId'];
		$patient_sec_proc = trim($getSecProcedureIdRow['name']);
	}
	$showSecondaryProcedureDropDown = 'true';
	
	$getTerProcedureIdQry = imw_query("SELECT * FROM procedures WHERE del_status!='yes' AND ((name != '' AND trim(name) = '$patient_ter_proc') OR (procedureAlias != '' AND trim(procedureAlias) = '$patient_ter_proc'))");
	if(imw_num_rows($getTerProcedureIdQry)>0){
		$getTerProcedureIdRow = imw_fetch_array($getTerProcedureIdQry);
	    $TertiaryProcedureId = $getTerProcedureIdRow['procedureId'];
		$patient_ter_proc = trim($getTerProcedureIdRow['name']);
	}
	//patient_ter_proc
	/*
	if(!$SecondaryProcedureId){
		$showSecondaryProcedureDropDown = 'true';
	}
	if($reConfirmId){ //IF PATIENT IS RE-CONFIRMING
		$showSecondaryProcedureDropDown = 'true';
	}
   */
	//if(!$anes_id){
		$showAnesDropDown = 'true';
	//}

	//if(!$confNurse_id){
		$showConfNurseDropDown = 'true';
	//}
	$conditionArr = '';
	
	//CODE TO CALCULATE AGE OF PATIENT
	if($stubTableDetails->patient_dob!="" && $stubTableDetails->patient_dob!="0000-00-00"){
		$tmp_date = $stubTableDetails->patient_dob;
		$patient_age=$objManageData->dob_calc($tmp_date);
	}
	//END CODE TO CALCULATE AGE OF PATIENT
	
	//if($patient_status != 'Scheduled'){
		// CONFIRMATION
		if($reConfirmId && $_POST["pt_conf_submit"]=="") {
			$confirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $reConfirmId, 'patientConfirmationId');
			$patientId = $confirmationDetails->patientId; 
			$cId = $confirmationDetails->patientConfirmationId;
			$ascId = $confirmationDetails->ascId;
			$patient_site_no = $confirmationDetails->site;
			if($patient_site_no == "1") {
				$patient_site = "left";
			}else if($patient_site_no == "2") {
				$patient_site = "right";
			}else if($patient_site_no == "3") {
				$patient_site = "both";
			}else if($patient_site_no == "4") {
				$patient_site = "left upper lid";
			}else if($patient_site_no == "5") {
				$patient_site = "left lower lid";
			}else if($patient_site_no == "6") {
				$patient_site = "right upper lid";
			}else if($patient_site_no == "7") {
				$patient_site = "right lower lid";
			}else if($patient_site_no == "8") {
				$patient_site = "bilateral upper lid";
			}else if($patient_site_no == "9") {
				$patient_site = "bilateral lower lid";
			}
			
			$sec_patient_site_no = $confirmationDetails->secondary_site;
			if($sec_patient_site_no == "1") {
				$sec_patient_site = "left";
			}else if($sec_patient_site_no == "2") {
				$sec_patient_site = "right";
			}else if($sec_patient_site_no == "3") {
				$sec_patient_site = "both";
			}else if($sec_patient_site_no == "4") {
				$sec_patient_site = "left upper lid";
			}else if($sec_patient_site_no == "5") {
				$sec_patient_site = "left lower lid";
			}else if($sec_patient_site_no == "6") {
				$sec_patient_site = "right upper lid";
			}else if($sec_patient_site_no == "7") {
				$sec_patient_site = "right lower lid";
			}else if($sec_patient_site_no == "8") {
				$sec_patient_site = "bilateral upper lid";
			}else if($sec_patient_site_no == "9") {
				$sec_patient_site = "bilateral lower lid";
			}
			
			$ter_patient_site_no = $confirmationDetails->tertiary_site;
			if($ter_patient_site_no == "1") {
				$ter_patient_site = "left";
			}else if($ter_patient_site_no == "2") {
				$ter_patient_site = "right";
			}else if($ter_patient_site_no == "3") {
				$ter_patient_site = "both";
			}else if($ter_patient_site_no == "4") {
				$ter_patient_site = "left upper lid";
			}else if($ter_patient_site_no == "5") {
				$ter_patient_site = "left lower lid";
			}else if($ter_patient_site_no == "6") {
				$ter_patient_site = "right upper lid";
			}else if($ter_patient_site_no == "7") {
				$ter_patient_site = "right lower lid";
			}else if($ter_patient_site_no == "8") {
				$ter_patient_site = "bilateral upper lid";
			}else if($ter_patient_site_no == "9") {
				$ter_patient_site = "bilateral lower lid";
			}
			
			$surgeonId = $confirmationDetails->surgeonId;
			$patient_prim_proc = trim(stripslashes($confirmationDetails->patient_primary_procedure));
			$PrimaryProcedureId = $confirmationDetails->patient_primary_procedure_id;
			$patient_sec_proc = trim(stripslashes($confirmationDetails->patient_secondary_procedure));
			$SecondaryProcedureId = $confirmationDetails->patient_secondary_procedure_id;
			$patient_ter_proc = trim(stripslashes($confirmationDetails->patient_tertiary_procedure));
			$TertiaryProcedureId = $confirmationDetails->patient_tertiary_procedure_id;
			
			//patient_ter_proc
			$anes_NA = $confirmationDetails->anes_NA; 
			$anes_id = $confirmationDetails->anesthesiologist_id; 
			$confNurse_id = $confirmationDetails->nurseId; 
			$patient_dos_temp = $confirmationDetails->dos;
				$patient_dos_split = explode("-",$patient_dos_temp);
				$patient_dos = $patient_dos_split[1]."-".$patient_dos_split[2]."-".$patient_dos_split[0];
			$assist_by_transConfirm = $confirmationDetails->assist_by_translator;
			if($assist_by_transConfirm) {
				$assist_by_trans = $assist_by_transConfirm;
			}
			$advanceDirective = $confirmationDetails->advanceDirective;
			$discharge_status = $confirmationDetails->discharge_status;
			$no_publicity = $confirmationDetails->no_publicity;
			$surgery_time = $confirmationDetails->surgery_time;
			$pickup_time = $confirmationDetails->pickup_time;
			$arrival_time = $confirmationDetails->arrival_time;
							
			$getPatientDetails = $objManageData->getRowRecord('patient_data_tbl', 'patient_id', $patientId);
				$patient_first_name = $getPatientDetails->patient_fname;
				$patient_middle_name = $getPatientDetails->patient_mname;
				$patient_last_name = $getPatientDetails->patient_lname;
				$patient_sex = $getPatientDetails->sex;
				
				$patient_address1 = $getPatientDetails->street1;
				$patient_address2 = $getPatientDetails->street2;
				$patient_home_phone = $getPatientDetails->homePhone;
				$patient_work_phone = $getPatientDetails->workPhone;
				$patient_language = $getPatientDetails->language;
				$patient_race = $getPatientDetails->race;
				$patient_religion = $getPatientDetails->religion;
				$patient_ethnicity = $getPatientDetails->ethnicity;
				$patient_city = $getPatientDetails->city;
				$patient_state = $getPatientDetails->state;
				$patient_zip = $getPatientDetails->zip;
				$patient_dob_temp = $getPatientDetails->date_of_birth;
					$patient_dob_split = explode("-",$patient_dob_temp);
					$patient_dob = $patient_dob_split[1]."-".$patient_dob_split[2]."-".$patient_dob_split[0];
				
				$scemr_patient_image_path = $getPatientDetails->patient_image_path;
		}	
	//}

	if(!$ascId) {
		// GENRATE ASC ID
		$ascIdPresent = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
		$ascId = $ascIdPresent->ascId_present + 1;	
		$ascId_hidden	= $ascId;		
		
	}else {
		$ascId_hidden='';
	}
// FIRST VIEW CHECKS


// START SAVE BUTTON CLICKED
$pt_conf_submit = $_POST["pt_conf_submit"];
	if($pt_conf_submit <> "") {	
		$ptOverride = false;	
		// APPLYING NUMBERS TO PATIENT SITE
		$patient_site = $_POST["patient_site_list"];		
		if($patient_site == "left") {
			$patient_site_no = 1;
		}else if($patient_site == "right") {
			$patient_site_no = 2;
		}else if($patient_site == "both") {
			$patient_site_no = 3;
		}else if($patient_site == "left upper lid") {
			$patient_site_no = 4;
		}else if($patient_site == "left lower lid") {
			$patient_site_no = 5;
		}else if($patient_site == "right upper lid") {
			$patient_site_no = 6;
		}else if($patient_site == "right lower lid") {
			$patient_site_no = 7;
		}else if($patient_site == "bilateral upper lid") {
			$patient_site_no = 8;
		}else if($patient_site == "bilateral lower lid") {
			$patient_site_no = 9;
		}

		$sec_patient_site = $_POST["sec_patient_site_list"];
		if($sec_patient_site == "left") {
			$sec_patient_site_no = 1;
		}else if($sec_patient_site == "right") {
			$sec_patient_site_no = 2;
		}else if($sec_patient_site == "both") {
			$sec_patient_site_no = 3;
		}else if($sec_patient_site == "left upper lid") {
			$sec_patient_site_no = 4;
		}else if($sec_patient_site == "left lower lid") {
			$sec_patient_site_no = 5;
		}else if($sec_patient_site == "right upper lid") {
			$sec_patient_site_no = 6;
		}else if($sec_patient_site == "right lower lid") {
			$sec_patient_site_no = 7;
		}else if($sec_patient_site == "bilateral upper lid") {
			$sec_patient_site_no = 8;
		}else if($sec_patient_site == "bilateral lower lid") {
			$sec_patient_site_no = 9;
		}
		
		$ter_patient_site = $_POST["ter_patient_site_list"];
		if($ter_patient_site == "left") {
			$ter_patient_site_no = 1;
		}else if($ter_patient_site == "right") {
			$ter_patient_site_no = 2;
		}else if($ter_patient_site == "both") {
			$ter_patient_site_no = 3;
		}else if($ter_patient_site == "left upper lid") {
			$ter_patient_site_no = 4;
		}else if($ter_patient_site == "left lower lid") {
			$ter_patient_site_no = 5;
		}else if($ter_patient_site == "right upper lid") {
			$ter_patient_site_no = 6;
		}else if($ter_patient_site == "right lower lid") {
			$ter_patient_site_no = 7;
		}else if($ter_patient_site == "bilateral upper lid") {
			$ter_patient_site_no = 8;
		}else if($ter_patient_site == "bilateral lower lid") {
			$ter_patient_site_no = 9;
		}
		
	$patient_dob = $_POST["dob"];
		$patient_dob_split = explode("-",$patient_dob);
		$patient_dob_temp = $patient_dob_split[2]."-".$patient_dob_split[0]."-".$patient_dob_split[1];	

	$patient_dos = $_POST["dos"];
		$patient_dos_split = explode("-",$patient_dos);
		$patient_dos_temp = $patient_dos_split[2]."-".$patient_dos_split[0]."-".$patient_dos_split[1];

	$assist_by_trans = $_POST['assist_by_trans'];
	$advanceDirective = $_POST['chbxAdvanceDirective'];
	$discharge_status = $_POST['discharge_status'];
	$no_publicity = $_POST['chk_no_publicity'];
	$surgery_time = $_POST["surgery_time"];
	$pickup_time = $_POST["pickup_time"];
	$arrival_time = $_POST["arrival_time"];
	//CODE TO CALCULATE AGE OF PATIENT
	if($_POST["dob"]!="" && $_POST["dob"]!="00-00-0000"){ //mm-dd-yyyy
		$tmp_date = $_POST["dob"];
		$patient_age=$objManageData->dob_calc($tmp_date);
	}
	//END CODE TO CALCULATE AGE OF PATIENT
	
	$language 	= $_POST["language"];
	if($language=='Other' && $_POST['otherLanguage']!=""){
		$language = "Other -- ".$_POST['otherLanguage'];
	}	
	$race 		= $_POST["race"];
	$religion 	= isset($_POST["religion"]) ? trim(addslashes(ucwords($_POST["religion"]))) : '';
	$ethnicity 	= $_POST["ethnicity"];
	
	// INSERT PATIENT DATA TABLE
			//$arrayPatientRecord['asc_id'] = $ascId;
			$arrayPatientRecord['patient_fname'] 	= trim(addslashes(ucwords($_POST["first_name"])));
			$arrayPatientRecord['patient_mname'] 	= trim(addslashes(ucwords($_POST["middle_name"])));
			$arrayPatientRecord['patient_lname'] 	= trim(addslashes(ucwords($_POST["last_name"])));
			$arrayPatientRecord['street1'] 			= trim(addslashes(ucwords($_POST["address1"])));
			$arrayPatientRecord['street2'] 			= trim(addslashes(ucwords($_POST["address2"])));
			$arrayPatientRecord['city'] 			= trim(addslashes(ucwords($_POST["city"])));
			$arrayPatientRecord['state'] 			= trim(ucwords($_POST["state"]));
			$arrayPatientRecord['zip'] 				= trim($_POST["zip"]);
			$arrayPatientRecord['date_of_birth'] 	= $patient_dob_temp;
			$arrayPatientRecord['sex'] 				= $_POST["sex_list"];
			$arrayPatientRecord['homePhone'] 		= trim(addslashes($_POST["home_phone"]));
			$arrayPatientRecord['workPhone'] 		= trim(addslashes($_POST["work_phone"]));
			$arrayPatientRecord['imwPatientId'] 	= $imwPatientId;
			$arrayPatientRecord["language"] 		= trim(addslashes($language));
			$arrayPatientRecord["race"]				=(is_array($race))?implode(",",$race):$race;
			$arrayPatientRecord["ethnicity"]		=(is_array($ethnicity))?implode(",",$ethnicity):$ethnicity;
			$arrayPatientRecord["religion"]			=addslashes($religion);
			
			
		//START CHEK IF PATIENT EXISTS THEN ALLOCATE PREVIOUS PATIENT ID ELSE INSERT NEW ENTRY OF PATIENT WITH NEW PATIENT ID
			
			//START GET PATIENT-ID IF PREVIOUSLY EXIST IN STUB-TABLE
			/*
			if($imwPatientId && !$patient_id_stub) {
				$getPatientIdStubQry 	= "SELECT patient_id_stub FROM stub_tbl 
											WHERE imwPatientId='".$imwPatientId."'
											AND (patient_id_stub!='0' AND patient_id_stub!='')
											ORDER BY stub_id DESC LIMIT 0,1
											";
				$getPatientIdStubRes 	= imw_query($getPatientIdStubQry) or die(imw_error());
				$getPatientIdStubNumRow = imw_num_rows($getPatientIdStubRes);
				if($getPatientIdStubNumRow>0) {
					$getPatientIdStubRow= imw_fetch_array($getPatientIdStubRes);
					$patient_id_stub 	= $getPatientIdStubRow["patient_id_stub"];
				}
			}*/
			//END GET PATIENT-ID IF PREVIOUSLY EXIST IN STUB-TABLE
			if($patient_id_stub) {
				$patientMatchStr = "SELECT patient_id FROM patient_data_tbl 
										WHERE patient_id = '".$patient_id_stub."'
										";
			}else {
				$patientMatchStr = "SELECT patient_id FROM patient_data_tbl 
										WHERE imwPatientId = '".$imwPatientId."' and imwPatientId!=''";
			}
			$patientMatchQry 	= imw_query($patientMatchStr);
			$patientMatchRows 	= imw_num_rows($patientMatchQry);
			if($patientMatchRows<=0){
				$patientMatchStr = "SELECT patient_id FROM patient_data_tbl 
										WHERE patient_fname = '".trim(addslashes($_POST["first_name"]))."'
										AND patient_lname 	= '".trim(addslashes($_POST["last_name"]))."'
										AND zip 		  	= '".trim(addslashes($_POST["zip"]))."'
										AND date_of_birth 	= '".$patient_dob_temp."'";
				$patientMatchQry 	= imw_query($patientMatchStr);
				$patientMatchRows 	= imw_num_rows($patientMatchQry);
			}
			
			
			if($patientMatchRows>0){
				$patientDataRow = imw_fetch_array($patientMatchQry);
				
				$objManageData->updateRecords($arrayPatientRecord, 'patient_data_tbl', 'patient_id', $patientDataRow["patient_id"]);
				$insertPatientDataId = $patientDataRow["patient_id"];
			}else {
				$insertPatientDataId = $objManageData->addRecords($arrayPatientRecord, 'patient_data_tbl');
			}
			
			// Chk if patient already confirmed 
			if( !$reConfirmId) {
				$chkPatientAlreadyConfirmedQry = "SELECT pc.patientConfirmationId FROM patientconfirmation pc 
															INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.stub_id = '".$_REQUEST["stub_id"]."')
															WHERE pc.patientId='$insertPatientDataId'";

				$chkPatientAlreadyConfirmedRes 		= imw_query($chkPatientAlreadyConfirmedQry) or die(imw_error());
				$chkPatientAlreadyConfirmedNumRow 	= imw_num_rows($chkPatientAlreadyConfirmedRes);
				if($chkPatientAlreadyConfirmedNumRow>0) {
					$tmpRes = imw_fetch_assoc($chkPatientAlreadyConfirmedRes);
					$reConfirmId = $tmpRes['patientConfirmationId'];
					$ptOverride =  true;
				}

			}
			// End chk if patient already confirmed 
			
			//START CODE TO ADD PATIENT PHOTO
			if($insertPatientDataId && !trim($_REQUEST["scemr_patient_image_path"]) && trim($_REQUEST["imw_patient_image_path"])) {
				
				$ptImageNameExplode = explode('/',trim($_REQUEST["imw_patient_image_path"]));
				$ptImagePath 		= 'pdfFiles/patient_images/patient_id_'.$insertPatientDataId.'_'.$ptImageNameExplode[2];
				$ptImageDirName 	= 'admin/pdfFiles/patient_images';
				$ptImageNameNew 	= 'patient_id_'.$insertPatientDataId.'_'.$ptImageNameExplode[2];
				$ptImwFullPath		= $rootServerPath.'/'.$imwDirectoryName.'/interface/main/uploaddir'.$_REQUEST["imw_patient_image_path"];
				$ptScemrImageFolder = $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.$ptImageDirName;
				if(!is_dir($ptScemrImageFolder)){		
					mkdir($ptScemrImageFolder);
				}
				if(file_exists($ptImwFullPath)) {
					$ptImageContent 			= file_get_contents($ptImwFullPath);
					$ptImageScemrPutPdfFileName = $ptScemrImageFolder.'/'.$ptImageNameNew;
					file_put_contents($ptImageScemrPutPdfFileName,$ptImageContent);
					unset($arrayPatientRecord);
					$arrayPatientRecord['patient_image_path'] 	= $ptImagePath;
					$objManageData->updateRecords($arrayPatientRecord, 'patient_data_tbl', 'patient_id', $insertPatientDataId);
				}
					
			}
			//END CODE TO ADD PATIENT PHOTO
			
			//START CODE TO UPDATE PATIENT-ID IN STUB-TABLE
			if($insertPatientDataId) {
					unset($arrayStubRecord);
					$arrayStubRecord['patient_id_stub']		= $insertPatientDataId;
					$arrayStubRecord['patient_first_name'] 	= trim(addslashes($_POST["first_name"]));
					$arrayStubRecord['patient_middle_name'] = trim(addslashes($_POST["middle_name"]));
					$arrayStubRecord['patient_last_name'] 	= trim(addslashes($_POST["last_name"]));
					$arrayStubRecord['patient_street1'] 	= trim(addslashes($_POST["address1"]));
					$arrayStubRecord['patient_street2'] 	= trim(addslashes($_POST["address2"]));
					$arrayStubRecord['patient_city'] 		= trim(addslashes($_POST["city"]));
					$arrayStubRecord['patient_state'] 		= trim($_POST["state"]);
					$arrayStubRecord['patient_zip'] 		= trim($_POST["zip"]);
					$arrayStubRecord['patient_dob'] 		= $patient_dob_temp;
					$arrayStubRecord['patient_sex'] 		= $_POST["sex_list"];
					$arrayStubRecord['patient_home_phone'] 	= trim(addslashes($_POST["home_phone"]));
					$arrayStubRecord['patient_work_phone'] 	= trim(addslashes($_POST["work_phone"]));
					if( isset($_POST["religion"]) )
						$arrayStubRecord['patient_religion'] 	= trim(addslashes($_POST["religion"]));

					$objManageData->updateRecords($arrayStubRecord, 'stub_tbl', 'stub_id', $_REQUEST['stub_id']);
			
					//START CODE TO SET PATIENT APPOINTMENT STATUS IN imwemr
					if($_REQUEST['patient_status_list']=='Canceled' || $_REQUEST['patient_status_list']=='No Show' || $_REQUEST['patient_status_list']=='Aborted Surgery' || $_REQUEST['patient_status_list']=='Scheduled' || $_REQUEST['patient_status_list']=='Checked-In' || $_REQUEST['patient_status_list']=='Checked-Out') {
						$connectionFileName					= '';
						$closeConnectionFileName			= '';
						if($imwSwitchFile == 'sync_imwemr.php') {
							$connectionFileName 			= 'connect_imwemr.php';
							$closeConnectionFileName 		= $link_imwemr;
						}else if($imwSwitchFile == 'sync_imwemr_remote.php') {
							$connectionFileName 			= 'connect_imwemr_remote.php';
							$closeConnectionFileName 		= $link_imwemr_remote;
						}
						if($connectionFileName) {
							$getStubImwApptIdQry 			= "SELECT * FROM stub_tbl WHERE stub_id = '".$_REQUEST['stub_id']."'";
							$getStubImwApptIdRes 			= imw_query($getStubImwApptIdQry) or die($getStubImwApptIdQry.imw_error());
							$getStubImwApptIdRow 			= imw_fetch_array($getStubImwApptIdRes);
							$stubImwApptId	 				= $getStubImwApptIdRow["appt_id"];
							$stubComment	 				= $getStubImwApptIdRow["comment"];
							
							$imwApptStatusId = '202';
							$imwApptComments = 'Rescheduled by '.$loggedInUserName;
							if($_REQUEST['patient_status_list']=='Canceled') {
								$imwApptComments = 'Cancelled by '.$loggedInUserName;
								$imwApptStatusId = '18';
							}
							else if($_REQUEST['patient_status_list']=='No Show') {
								$imwApptComments = 'No Show by '.$loggedInUserName;
								$imwApptStatusId = '3';
							}
							else if($_REQUEST['patient_status_list']=='Aborted Surgery') {
								$imwApptComments = 'Aborted Surgery by '.$loggedInUserName;
								$imwApptStatusId = $imwSchStatusIdArr['Aborted Surgery'];
							}
							else if($_REQUEST['patient_status_list']=='Scheduled') {
								$imwApptComments = 'Rescheduled by '.$loggedInUserName;
								$imwApptStatusId = '202';
							}else if($_REQUEST['patient_status_list']=='Checked-In') {
								$imwApptComments = 'Checked-In by '.$loggedInUserName;
								$imwApptStatusId = '13';
							}else if($_REQUEST['patient_status_list']=='Checked-Out') {
								$imwApptComments = 'Checked-Out by '.$loggedInUserName;
								$imwApptStatusId = '11';
							}
							include($connectionFileName); // imwemr connection
							//logApptChangedStatus($intApptId, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId='18', $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername='surgercenter', $strNewApptComments='No Reason', $intNewApptProcedureId, $blUpdateNew = false,$connectionFileName,$closeConnectionFileName)
							$blUpdateNew = false;
							$imwComments = trim($stubComment.' '.$imwApptComments);
							logApptChangedStatus($stubImwApptId, $patient_dos_temp, '', '', $imwApptStatusId, '', '', 'surgerycenter', addslashes($imwComments), '', $blUpdateNew,$connectionFileName,$closeConnectionFileName);
							
							$updateImwApptStatusQry 		= "UPDATE schedule_appointments 
																SET sa_patient_app_status_id='".$imwApptStatusId."',
																sa_comments='".addslashes($imwComments)."' 
																WHERE sa_app_start_date='".$patient_dos_temp."' 
																AND id='".$stubImwApptId."'";
							$updateImwApptStatusRes 		= imw_query($updateImwApptStatusQry) or die($updateImwApptStatusQry.imw_error());
							@imw_close($closeConnectionFileName); //CLOSE IMWEMR CONNECTION
							include("common/conDb.php");  //SURGERYCENTER CONNECTION	
						
							//START UPDATE PATIENT STATUS IN IOLINK
							$iolink_patient_in_waiting_id = trim($iolink_patient_in_waiting_id);
							if(($_REQUEST['patient_status_list']=='Canceled' || $_REQUEST['patient_status_list']=='Scheduled') && $iolink_patient_in_waiting_id && $iolink_patient_in_waiting_id!='0') {
								$updtIolinkWaitingTblQry = "UPDATE patient_in_waiting_tbl SET patient_status='".$_REQUEST['patient_status_list']."',comment='".addslashes($imwComments)."'
															WHERE  patient_in_waiting_id = '".$iolink_patient_in_waiting_id."' AND dos = '".$patient_dos_temp."'";
								$updtIolinkWaitingTblRes = imw_query($updtIolinkWaitingTblQry) or die($updtIolinkWaitingTblQry.imw_error());
							}
							//END UPDATE PATIENT STATUS IN IOLINK
											
						
						}
					}		
					//END CODE TO SET PATIENT APPOINTMENT STATUS IN imwemr			
			}
			//END CODE TO UPDATE PATIENT-ID IN STUB-TABLE
		
		//END CHEK IF PATIENT EXISTS THEN ALLOCATE PREVIOUS PATIENT ID ELSE INSERT NEW ENTRY OF PATIENT WITH NEW PATIENT ID	
	
		//CHECK IF PATIENT ALREADY CONFIRMED
			if($reConfirmId=="") {
				//$chkPatientAlreadyConfirmedQry 		= "SELECT patientConfirmationId FROM patientconfirmation where patientId='$insertPatientDataId' and dos='$patient_dos_temp' ".$andstubIdQry;
				$chkPatientAlreadyConfirmedQry = "SELECT pc.patientConfirmationId FROM patientconfirmation pc 
												  INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.stub_id = '".$_REQUEST["stub_id"]."')
												  WHERE pc.patientId='$insertPatientDataId'";
				
				$chkPatientAlreadyConfirmedRes 		= imw_query($chkPatientAlreadyConfirmedQry) or die(imw_error());
				$chkPatientAlreadyConfirmedNumRow 	= imw_num_rows($chkPatientAlreadyConfirmedRes);
				if($chkPatientAlreadyConfirmedNumRow>0) {
				?>
				<script>
					//alert('Patient already confirmed');
					location.href='home.php?saveConfirm=true';
					//location.href='home.php';
				</script>
				<?php
					exit();
				}
			}	
		//END CHECK IF PATIENT ALREADY CONFIRMED
	
		$ascId = $_POST["ascId"];
		$ascId_hidden = $_POST["ascId_hidden"];
		
		//START CHECK TO NOT EXIST DUPLICATE ENTRY OF ASC-ID
		$chkAscId='';
		if($reConfirmId) {
			$chkConfirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $reConfirmId, 'patientConfirmationId');
			if($chkConfirmationDetails) {
				$chkAscId = $chkConfirmationDetails->ascId;
			}
		}	
		if(!$reConfirmId || !$chkAscId) {
			// SET ASC ID
			$ascIdPresent = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
			$ascId = $ascIdPresent->ascId_present + 1;	
			$ascId_hidden	= $ascId;		
		}
		//END CHECK TO NOT EXIST DUPLICATE ENTRY OF ASC-ID
		
		// UPDATE LATEST ASCID
			if($ascId_hidden <> "") {
				$ascIdArr['ascId_present'] = $ascId_hidden;
				$updateAscIdPresent = $objManageData->updateRecords($ascIdArr, 'surgerycenter', 'surgeryCenterId', 1);
			}
		// END UPDATE LATEST ASCID
	
	// INSERT PATIENT DATA TABLE
	
	// INSERT CONFIRMATION TABLE
			 $surgeon_name_id = addslashes($_POST["surgeon_name_id"]);
			 
			 //$surgeon_id = $_POST["surgeonId"];	
			 //if($surgeon_id == ''){
			 	$surgeon_id = $surgeon_name_id;
			 //}
			// if(!$surgeon_name)	 {
				unset($conditionArr);
				$conditionArr['usersId'] = $surgeon_name_id;
				$conditionArr['user_type'] = 'Surgeon';				
				$getSurgeonName = $objManageData->getMultiChkArrayRecords('users', $conditionArr);
			 	if(count($getSurgeonName)>0){
					foreach($getSurgeonName as $surgeons){
						 if($surgeons->mname) {
						 	$surgeonsMiddleName = ' '.$surgeons->mname;
						 }
						 $surgeon_name = $surgeons->fname.$surgeonsMiddleName.' '.$surgeons->lname;
					}
				}
			 //}
			 
			 $anes_name_id = addslashes($_POST["anes_name_id"]);
			 $anes_id = $anes_name_id;
			 //if(!$anes_name)	 {
			 	unset($conditionArr);
				$conditionArr['usersId'] 	= $anes_name_id;
				$conditionArr['user_type'] 	= 'Anesthesiologist';				
				$getAnesName = $objManageData->getMultiChkArrayRecords('users', $conditionArr);
			 	if(count($getAnesName)>0){
					foreach($getAnesName as $Anesthesia){
						 if($Anesthesia->mname) {
						 	$AnesthesiaMiddleName = ' '.$Anesthesia->mname;
						 }
						 $anes_name = $Anesthesia->fname.$AnesthesiaMiddleName.' '.$Anesthesia->lname;
					}
				}
			 //}
			 //START CODE TO SET ANES NAME AND ID (IF ANSE NAME IS N/A)	
			 $anes_NA='';
			 if($anes_id=='0') {
			 	$anes_name='N/A';
				$anes_NA='Yes';
			 }
			 //END CODE TO SET ANES NAME AND ID (IF ANSE NAME IS N/A)
			 $conf_nurse_id = addslashes($_POST["conf_nurse_id"]);
			 $confNurse_id = $conf_nurse_id;
			 //if(!$conf_nurse)	 {
			 	unset($conditionArr);
				$conditionArr['usersId'] = $conf_nurse_id;
				//$conditionArr['user_type'] = 'Nurse';				
				$getNurseName = $objManageData->getMultiChkArrayRecords('users', $conditionArr);
			 	if(count($getNurseName)>0){
					foreach($getNurseName as $NurseStaff){
						 if($NurseStaff->mname) {
						 	$NurseStaffMiddlename = ' '.$NurseStaff->mname;
						 }
						 $conf_nurse = $NurseStaff->fname.$NurseStaffMiddlename.' '.$NurseStaff->lname;
					}
				}
			 //}	
			
			//primary procedure id
			list($primary_procedure,$PrimaryProcedure_id) = explode("$$",$_POST["prim_proc"]);
			
			/*
			$primaryprocedure = imw_query("select `procedureId` from procedures where del_status!='yes' AND ((name='$primary_procedure' AND name!='') OR (procedureAlias='$primary_procedure' AND procedureAlias!=''))");
			$primary_proc_data=imw_fetch_array($primaryprocedure);
			$PrimaryProcedure_id = $primary_proc_data['procedureId'];
			*/
			//secondary procedure id
			if($_POST["sec_proc"]!='')
		  	{
				list($secondary_procedure,$SecondaryProcedure_id) = explode("$$",$_POST["sec_proc"]);
				
			  	/*
				$secondaryprocedure		=	imw_query("select procedureId from procedures where del_status!='yes' AND ((name='$secondary_procedure' AND name!='')  OR (procedureAlias='$secondary_procedure' AND procedureAlias!=''))");
			  	$secondary_proc_data		=	imw_fetch_array($secondaryprocedure);
			  	$SecondaryProcedure_id 	= 	$secondary_proc_data['procedureId'];
				*/
	       	}
		 	else
		 	{
		  		$secondary_procedure='N/A';
		  		$SecondaryProcedure_id='0';
		 	}

		 	$tertiary_procedure='';
		 	$TertiaryProcedure_id='0';
			if($_POST["ter_proc"]!='') {
				list($tertiary_procedure,$TertiaryProcedure_id) = explode("$$",$_POST["ter_proc"]);
				
				/*
				$tertiary_procedure		=	addslashes($_POST["ter_proc"]);
				$tertiaryprocedure		=	imw_query("select procedureId from procedures where del_status!='yes' AND ((name='$tertiary_procedure' AND name!='')  OR (procedureAlias='$tertiary_procedure' AND procedureAlias!=''))");
				$tertiary_proc_data		=	imw_fetch_array($tertiaryprocedure);
				$TertiaryProcedure_id 	= 	$tertiary_proc_data['procedureId'];
				*/
			 }
		 
			$arrayRecord['patientId'] 						= $insertPatientDataId;
			$arrayRecord['dos'] 							= $patient_dos_temp;
			$arrayRecord['surgery_time'] 					= $surgery_time;
			$arrayRecord['pickup_time'] 					= $pickup_time;
			$arrayRecord['arrival_time'] 					= $arrival_time;
			$arrayRecord['ascId'] 							= $ascId;
			$arrayRecord['assist_by_translator'] 			= $assist_by_trans;
			$arrayRecord['advanceDirective'] 				= $advanceDirective;
			$arrayRecord['discharge_status'] 				= $discharge_status;
			$arrayRecord['no_publicity'] 				= $no_publicity;
			$arrayRecord['patient_primary_procedure'] 		= addslashes($primary_procedure);
			$arrayRecord['patient_primary_procedure_id'] 	= $PrimaryProcedure_id;
			$arrayRecord['patient_secondary_procedure'] 	= addslashes($secondary_procedure);
			$arrayRecord['patient_secondary_procedure_id']	= $SecondaryProcedure_id;
			$arrayRecord['patient_tertiary_procedure'] 		= addslashes($tertiary_procedure);
			$arrayRecord['patient_tertiary_procedure_id']	= $TertiaryProcedure_id;
			
			$arrayRecord['site'] 							= $patient_site_no;
			$arrayRecord['secondary_site'] 					= $sec_patient_site_no;
			$arrayRecord['secondary_site_description']		= $sec_patient_site;
			$arrayRecord['tertiary_site'] 					= $ter_patient_site_no;
			$arrayRecord['tertiary_site_description']		= $ter_patient_site;
			$arrayRecord['zip'] 							= $_POST["zip"];
			$arrayRecord['surgeonId'] 						= $surgeon_id;
			$arrayRecord['surgeon_name'] 					= $surgeon_name;
			$arrayRecord['anes_NA'] 						= $anes_NA;
			$arrayRecord['anesthesiologist_name'] 			= addslashes($anes_name);
			$arrayRecord['anesthesiologist_id'] 			= $anes_id;			
			$arrayRecord['confirm_nurse'] 					= addslashes($conf_nurse);
			$arrayRecord['nurseId'] 						= $confNurse_id;
			//$arrayRecord['patientStatus'] 					= 'Checked-In';
			$arrayRecord['patientStatus'] 					= $_REQUEST['patient_status_list'];
			
			$arrayRecord['dateConfirmation'] 				= date("Y-m-d H:i:s");
			$arrayRecord['imwPatientId'] 				= $imwPatientId;
			if($reConfirmId) {
				$objManageData->updateRecords($arrayRecord, 'patientconfirmation', 'patientConfirmationId', $reConfirmId);
				$insertConfirmationId = $reConfirmId;
				
				/* Code to update procedure category if update*/
				/*$procCatQry	=	"Select PC.isMisc From procedures P JOIN procedurescategory PC On P.catId = PC.proceduresCategoryId Where P.procedureId = '".$PrimaryProcedure_id."' ";
		 		$procCatSql	=	imw_query($procCatQry) or die('Error Found: '.imw_error());
		 		$procCatRes	=	imw_fetch_assoc($procCatSql);
				$isMiscProc	=	($procCatRes['isMisc'])	?	'true'	:	'';*/
				$isInjMiscProc	=	$objManageData->verifyProcIsInjMisc($PrimaryProcedure_id);
				
				$procCatUpdateQry = "update patientconfirmation set prim_proc_is_misc = '".$isInjMiscProc."' where patientConfirmationId = '".$insertConfirmationId."' And prim_proc_is_misc <> '' ";
				imw_query($procCatUpdateQry);
				/* End Code to update procedure category if update*/
				
			}else {
				//CHECK IF PATIENT ALREADY CONFIRMED (IF NOT THEN INSERT NEW ENTRY TO CONFIRM PATIENT)
					//$chkPatientAlreadyConfirmedQry 		= "SELECT patientConfirmationId FROM patientconfirmation where patientId='$insertPatientDataId' and dos='$patient_dos_temp' ".$andstubIdQry;
					$chkPatientAlreadyConfirmedQry = "SELECT pc.patientConfirmationId FROM patientconfirmation pc 
													  INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.stub_id = '".$_REQUEST["stub_id"]."')
													  WHERE pc.patientId='$insertPatientDataId'";
					
					$chkPatientAlreadyConfirmedRes 		= imw_query($chkPatientAlreadyConfirmedQry) or die(imw_error());
					$chkPatientAlreadyConfirmedNumRow 	= imw_num_rows($chkPatientAlreadyConfirmedRes);
					if($chkPatientAlreadyConfirmedNumRow>0) {
						//DO NOTHING
						/*
						$chkPatientAlreadyConfirmedRow = imw_fetch_array($chkPatientAlreadyConfirmedRes);
						$insertConfirmationId = $chkPatientAlreadyConfirmedRow['patientConfirmationId'];
						
						$objManageData->updateRecords($arrayRecord, 'patientconfirmation', 'patientConfirmationId', $insertConfirmationId);
						*/
					}else { //(IF NOT ALREADY CONFIRMED THEN INSERT NEW ENTRY TO CONFIRM PATIENT)
				
						$insertConfirmationId = $objManageData->addRecords($arrayRecord, 'patientconfirmation');
					}
				//CHECK IF PATIENT ALREADY CONFIRMED
			}
	// INSERT CONFIRMATION TABLE
	
	//START INSERTING ALLERGIES REACTION SPREADSHEET VALUES
		$count_sprd = $_POST["count_patientconf_allerg_sprd"];
		for($i_sprd=1;$i_sprd<=$count_sprd;$i_sprd++) {
			$Allergies_conf_value	= $_POST["Allergies_conf$i_sprd"];
			$Reaction_conf_value 	= $_POST["Reaction_conf$i_sprd"];
			if($Allergies_conf_value<>"" || $Reaction_conf_value<>"") {
				$sprd_ins_query 	= "insert into patient_allergies_tbl set
										patient_confirmation_id = '$insertConfirmationId',	
										asc_id = '$ascId',
										patient_id = '$insertPatientDataId',
										allergy_name = '$Allergies_conf_value',
										reaction_name = '$Reaction_conf_value'";
				$sprd_ins_res 		= imw_query($sprd_ins_query) or die(imw_error());
			}
		}
		//INSERT LEFT MENU FORMS STATUS
		if(!$reConfirmId || ($reConfirmId && $ptOverride)) { //IF PATIENT IS NOT RE-CONFIRMING THEN RUN THIS CODE
			$chk_left_menu_ins_query=imw_query("select `id` from `left_navigation_forms` WHERE confirmationId='".$insertConfirmationId."' AND patient_id='".$insertPatientDataId."'");
			if(imw_num_rows($chk_left_menu_ins_query)>0) {
				$left_menu_ins_query = "UPDATE left_navigation_forms set asc_id = '$ascId', confirmationId = '$insertConfirmationId', patient_id = '$insertPatientDataId' WHERE confirmationId='".$insertConfirmationId."' AND patient_id='".$insertPatientDataId."'";
			}else {
				$left_menu_ins_query = "INSERT INTO left_navigation_forms set asc_id = '$ascId', confirmationId = '$insertConfirmationId', patient_id = '$insertPatientDataId'";
			}
			$left_menu_ins_res 		 = imw_query($left_menu_ins_query) or die(imw_error());		
		} // END IF PATIENT IS NOT RE-CONFIRMING THEN RUN THIS CODE
		
		$stub_patient_primary_procedure 	= addslashes($primary_procedure);
		$stub_patient_secondary_procedure 	= addslashes($secondary_procedure);
		$stub_patient_tertiary_procedure 	= addslashes($tertiary_procedure);
		
		//UPDATE PRIMARY PROCEDURE IN STUB TABLE 
			if(trim($stub_patient_primary_procedure)<>"") {
				$updtpatient_primary_procedure = "patient_primary_procedure = '$stub_patient_primary_procedure',";
			}
			//if(trim($stub_patient_secondary_procedure)<>"") {
				$updtpatient_secondary_procedure = "patient_secondary_procedure = '$stub_patient_secondary_procedure',";
			//}
			//if(trim($stub_patient_tertiary_procedure)<>"") {
				$updtpatient_tertiary_procedure = "patient_tertiary_procedure = '$stub_patient_tertiary_procedure',";
			//}
		//END UPDATE PRIMARY PROCEDURE IN STUB TABLE 
				
		//SET PATIENT SITE IN STUB TABLE 
			if($patient_site) {
				$updtPatientSite = "site = '$patient_site',";
			}
			if($sec_patient_site)
			{
				$updtSecPatientSite = " stub_secondary_site = '$sec_patient_site', ";
			}
			if($ter_patient_site){
				$updtTerPatientSite = " stub_tertiary_site = '$ter_patient_site', ";
			}
		//END SET PATIENT SITE IN STUB TABLE 
		$updtChkInTime='';
		$updtChkOutTime='';
		$chkInOutTime = $objManageData->getTmFormat(date("H:i:s"));
		if(trim($_REQUEST['checkInOutTime'])) {
			//$chkInOutTime = $_REQUEST['checkInOutTime'];
			$chkInOutTime = $objManageData->setTmFormat($_REQUEST['checkInOutTime']);
		}
		if($_REQUEST['patient_status_list']=='Checked-In') { 
			$updtChkInTime = "checked_in_time = '".$chkInOutTime."', "; 
		}else if($_REQUEST['patient_status_list']=='Checked-Out') { 
			$updtChkOutTime = "checked_out_time = '".$chkInOutTime."', recentChartSaved = '', "; 
		}
		
		$firstTimeCheckIn = false;
		if($ascId_hidden <> "") { //SET PATIENT STATUS TO CHECKED-IN AT ALLOCATION OF ASC-ID
			// UPDATE STATUS IN STUB TABLE 
				$update_stub_status_qry = "update `stub_tbl` set 
											patient_status = '".$_REQUEST['patient_status_list']."', 
											$updtChkInTime
											$updtChkOutTime
											$updtpatient_primary_procedure
											$updtpatient_secondary_procedure
											$updtpatient_tertiary_procedure
											$updtPatientSite
											$updtSecPatientSite
											$updtTerPatientSite
											patient_confirmation_id = '$insertConfirmationId'
											WHERE stub_id = '".$_REQUEST["stub_id"]."'";
				//$update_stub_status_res = 	imw_query($update_stub_status_qry) or die(imw_error());	
				$firstTimeCheckIn = true;
			// END UPDATE SCAN DOCUMENTS, SCAN UPLOAD 
		
		}else {
				$update_stub_status_qry = "update `stub_tbl` set 
											patient_status = '".$_REQUEST['patient_status_list']."', 
											$updtChkInTime
											$updtChkOutTime
											$updtpatient_primary_procedure
											$updtpatient_secondary_procedure
											$updtpatient_tertiary_procedure
											$updtPatientSite
											$updtSecPatientSite
											$updtTerPatientSite
											patient_confirmation_id = '$insertConfirmationId'
											WHERE stub_id = '".$_REQUEST["stub_id"]."'";
		}
		$update_stub_status_res = 	imw_query($update_stub_status_qry) or die(imw_error());	
		
		//update cheklist form show status at first check in 
		
		if( $firstTimeCheckIn ) {
			// get show status from admin
			$adminShowCheckList = $objManageData->loadSettings('safety_check_list');
			//get current saved status from check list 
			$checklistShowStatus = $objManageData->getChartShowStatus($insertConfirmationId,'checklist');
			if( !$checklistShowStatus ) {
				
				//get current form status for checklist page
				$checklistStatusQry = "Select form_status From surgical_check_list where confirmation_id = '".(int)$insertConfirmationId."' ";
				$checklistStatusSql = imw_query($checklistStatusQry) or die($checklistStatusQry.imw_error());
				$checklistStatusCnt = imw_num_rows($checklistStatusSql);
				if( $checklistStatusCnt) $checklistStatusRes = imw_fetch_assoc($checklistStatusSql);
				$checklistFormStatus = $checklistStatusRes['form_status'];

				//1 to show checklist page
				//2 to hide checklist page
				$checklistStatus = ($adminShowCheckList['safety_check_list'] == 1) ? 1 : 2;
				$checkliststatus = ($checklistStatus <> 1 && ($checklistFormStatus == 'completed' || $checklistFormStatus == 'not completed' )) ? 1 : $checklistStatus; 
				
				// Add check list show status into confirmation table
				$checklistShowStatusUpQry = "Update patientconfirmation Set show_checklist = '".$checkliststatus."' Where patientConfirmationId = '".(int)$insertConfirmationId."' ";
				$checklistShowStatusUpSql = imw_query($checklistShowStatusUpQry) or die($checklistShowStatusUpQry . imw_erro());
			}
			
		}
		//update cheklist form show status at first check in

		/************ADDING CODE TO SEND SIU FOR CHECK-IN EVENT*********/
		if($firstTimeCheckIn && $update_stub_status_res){
			if(defined('HL7_SIU_GENERATION') && constant('HL7_SIU_GENERATION') === true && isset($imwVer) && $imwVer === 'R8' && isset($imwPracticeName) && !empty($imwPracticeName)){
				$patient_in_waiting_id_bk = $iolink_patient_in_waiting_id;
				
				$sqlIdocApptId = "SELECT `appt_id`,`imwPatientId` FROM `stub_tbl` WHERE `stub_id`='".$_REQUEST["stub_id"]."' LIMIT 1";
				$respIdocApptId = imw_query($sqlIdocApptId); echo imw_error();
				if( $respIdocApptId && imw_num_rows($respIdocApptId) > 0 )
				{
					$idocApptIdrs = imw_fetch_assoc($respIdocApptId);
					$idocApptId = $idocApptIdrs['appt_id'];
					$idocPatId	= $idocApptIdrs['imwPatientId'];
					$URIbk = $_SERVER['REQUEST_URI'];
					$_SERVER['REQUEST_URI'] = $imwPracticeName;
					$_SERVER['HTTP_HOST']	= $imwPracticeName;
					$ignoreAuth = true;
					include('connect_imwemr.php');

					$curlFields = array();
					$curlFields['MsgType'] 			= 'SIU';
					$curlFields['PatId'] 			= $idocPatId;
					$curlFields['SchId'] 			= $idocApptId;
					$curlFields['SubMsgType'] 		= 13;
					
					$url = $imwPracticeURL.'/hl7sys/api/index.php';
					$cur = curl_init();
					curl_setopt($cur,CURLOPT_URL,$url);
					curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($cur, CURLOPT_POSTFIELDS, $curlFields);
					$data = curl_exec($cur);
					if (curl_errno($cur)){
					//	die("Curl Error (HL7): " . curl_error($cur));
					}
					curl_close($cur);
					$_SERVER['REQUEST_URI'] = $URIbk;
					unset($URIbk, $makeHL7);
					include('common/conDb.php');
				}
				
			}

			/*END HL7 SIU message for the added Patient*/
			$patient_in_waiting_id = $patient_in_waiting_id_bk;	/*Backup waiting ID*/
			unset($patient_in_waiting_id_bk);
			
		}
		/***************HL7 CODE END************************************/

		if(!$reConfirmId || ($reConfirmId && $ptOverride = true ) ) { // IF PATIENT IS NOT RE-CONFIRMING THEN RUN THIS CODE
				
				$update_scan_upload_qry = "update `scan_upload_tbl` set 
											confirmation_id = '$insertConfirmationId' 
											WHERE patient_id = '$insertPatientDataId'
											AND confirmation_id = '0'
											AND dosOfScan = '$patient_dos_temp'
											".$andstubIdQry;
				$update_scan_upload_res = imw_query($update_scan_upload_qry) or die(imw_error());										
			
			//END UPDATE SCAN DOCUMENTS, SCAN UPLOAD
			//INSERT NEW ENTRY OF SCAN DOCUMENT WITH PATIENT ID 
											
				$chk_insert_scan_document_qry1 = "select document_id from scan_documents where document_name = 'Pt. Info' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp' ".$andstubIdQry;
				$chk_insert_scan_document_res1 = imw_query($chk_insert_scan_document_qry1) or die(imw_error());
				$chk_insert_scan_document_numrow1 = imw_num_rows($chk_insert_scan_document_res1);
				if($chk_insert_scan_document_numrow1>0) {
					
					$update_scan_document_qry1 = "update `scan_documents` set 
												confirmation_id = '$insertConfirmationId' 
												WHERE patient_id = '$insertPatientDataId'
												AND document_name = 'Pt. Info'
												AND confirmation_id = '0'
												AND dosOfScan = '$patient_dos_temp'
												".$andstubIdQry;
					$update_scan_document_res1 = imw_query($update_scan_document_qry1) or die(imw_error());										
					
				}else {	
					$chk_update_scan_document_qry1 = "select document_id from scan_documents where document_name = 'Pt. Info' AND patient_id = '".$insertPatientDataId."' AND confirmation_id = '".$insertConfirmationId."'";
					$chk_update_scan_document_res1 = imw_query($chk_update_scan_document_qry1) or die(imw_error());
					$chk_update_scan_document_numrow1 = imw_num_rows($chk_update_scan_document_res1);
					if($chk_update_scan_document_numrow1<=0) {
					
						$insert_scan_document_qry1 = "insert into `scan_documents` set 
													document_name = 'Pt. Info',
													patient_id = '$insertPatientDataId',
													dosOfScan = '$patient_dos_temp',
													confirmation_id = '$insertConfirmationId',
													stub_id = '".$_REQUEST["stub_id"]."'
													";
						$insert_scan_document_res1 = imw_query($insert_scan_document_qry1) or die(imw_error());
					
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
						$insert_scan_log_qry1 = "insert into `scan_log_tbl` set 
													document_id = '".imw_insert_id()."',
													document_name = 'Pt. Info',
													patient_id = '$insertPatientDataId',
													confirmation_id = '$insertConfirmationId',
													document_date_time = '".date('Y-m-d H:i:s')."',
													document_file_name = 'patient_confirm.php',
													document_encounter = 'pt_info_1',
													stub_id = '".$_REQUEST["stub_id"]."'
													";
						$insert_scan_log_res1 = imw_query($insert_scan_log_qry1) or die(imw_error());
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
					}
				}
				
				$chk_insert_scan_document_qry2 = "select document_id from scan_documents where document_name = 'Clinical' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp'";
				$chk_insert_scan_document_res2 = imw_query($chk_insert_scan_document_qry2) or die(imw_error());
				$chk_insert_scan_document_numrow2 = imw_num_rows($chk_insert_scan_document_res2);
				if($chk_insert_scan_document_numrow2>0) {
					
					$update_scan_document_qry2 = "update `scan_documents` set 
												confirmation_id = '$insertConfirmationId' 
												WHERE patient_id = '$insertPatientDataId'
												AND document_name = 'Clinical'
												AND confirmation_id = '0'
												AND dosOfScan = '$patient_dos_temp'
												".$andstubIdQry;
					$update_scan_document_res2 = imw_query($update_scan_document_qry2) or die(imw_error());										
				}else {	
					
					$chk_update_scan_document_qry2 = "select document_id from scan_documents where document_name = 'Clinical' AND patient_id = '".$insertPatientDataId."' AND confirmation_id = '".$insertConfirmationId."' ".$andstubIdQry;
					$chk_update_scan_document_res2 = imw_query($chk_update_scan_document_qry2) or die(imw_error());
					$chk_update_scan_document_numrow2 = imw_num_rows($chk_update_scan_document_res2);
					if($chk_update_scan_document_numrow2<=0) {
					
						$insert_scan_document_qry2 = "insert into `scan_documents` set 
													document_name = 'Clinical',
													patient_id = '$insertPatientDataId',
													dosOfScan = '$patient_dos_temp',
													confirmation_id = '$insertConfirmationId',
													stub_id = '".$_REQUEST["stub_id"]."'
													";
						$insert_scan_document_res2 = imw_query($insert_scan_document_qry2) or die(imw_error());
					
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
						$insert_scan_log_qry2 = "insert into `scan_log_tbl` set 
													document_id = '".imw_insert_id()."',
													document_name = 'Clinical',
													patient_id = '$insertPatientDataId',
													confirmation_id = '$insertConfirmationId',
													document_date_time = '".date('Y-m-d H:i:s')."',
													document_file_name = 'patient_confirm.php',
													document_encounter = 'clinical_1',
													stub_id = '".$_REQUEST["stub_id"]."'
													";
						$insert_scan_log_res2 = imw_query($insert_scan_log_qry2) or die(imw_error());
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
					}
				}

				$chk_insert_scan_document_qry3 = "select document_id from scan_documents where document_name = 'IOL' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp'";
				$chk_insert_scan_document_res3 = imw_query($chk_insert_scan_document_qry3) or die(imw_error());
				$chk_insert_scan_document_numrow3 = imw_num_rows($chk_insert_scan_document_res3);
				if($chk_insert_scan_document_numrow3>0) {
					
					$update_scan_document_qry3 = "update `scan_documents` set 
												confirmation_id = '$insertConfirmationId' 
												WHERE patient_id = '$insertPatientDataId'
												AND document_name = 'IOL'
												AND confirmation_id = '0'
												AND dosOfScan = '$patient_dos_temp'
												".$andstubIdQry;
					$update_scan_document_res3 = imw_query($update_scan_document_qry3) or die(imw_error());										
				}else {	
					
					$chk_update_scan_document_qry3 = "select document_id from scan_documents where document_name = 'IOL' AND patient_id = '".$insertPatientDataId."' AND confirmation_id = '".$insertConfirmationId."' ".$andstubIdQry;
					$chk_update_scan_document_res3 = imw_query($chk_update_scan_document_qry3) or die(imw_error());
					$chk_update_scan_document_numrow3 = imw_num_rows($chk_update_scan_document_res3);
					if($chk_update_scan_document_numrow3<=0) {
					
						$insert_scan_document_qry3 = "insert into `scan_documents` set 
													document_name = 'IOL',
													patient_id = '$insertPatientDataId',
													dosOfScan = '$patient_dos_temp',
													confirmation_id = '$insertConfirmationId',
													stub_id = '".$_REQUEST["stub_id"]."'
													";
						$insert_scan_document_res3 = imw_query($insert_scan_document_qry3) or die(imw_error());
					
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
						$insert_scan_log_qry3 = "insert into `scan_log_tbl` set 
													document_id = '".imw_insert_id()."',
													document_name = 'IOL',
													patient_id = '$insertPatientDataId',
													confirmation_id = '$insertConfirmationId',
													document_date_time = '".date('Y-m-d H:i:s')."',
													document_file_name = 'patient_confirm.php',
													document_encounter = 'iol_1',
													stub_id = '".$_REQUEST["stub_id"]."'
													";
						$insert_scan_log_res3 = imw_query($insert_scan_log_qry3) or die(imw_error());
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
					}
				}

				$scnFolderArr = array('H&P','EKG','Health Questionnaire','Ocular Hx','Consent');
				foreach($scnFolderArr as $scnFldNme) {
					$update_scan_document_qry3 = "update `scan_documents` set 
												confirmation_id = '".$insertConfirmationId."' 
												WHERE patient_id = '".$insertPatientDataId."'
												AND document_name = '".$scnFldNme."'
												AND confirmation_id = '0'
												AND dosOfScan = '".$patient_dos_temp."'
												".$andstubIdQry;
					$update_scan_document_res3 = imw_query($update_scan_document_qry3) or die(imw_error());										
				}
					
			
			//END INSERT NEW ENTRY OF SCAN DOCUMENT WITH PATIENT ID
		
		
			// UPDATE EPOST-IT
				$update_epost_qry = "update `eposted` set 
											patient_conf_id = '$insertConfirmationId' 
											WHERE patient_id = '$insertPatientDataId'
											AND patient_conf_id = '0'
											".$andstubIdQry;
				$update_epost_res = imw_query($update_epost_qry) or die(imw_error());										
			//END UPDATE EPOST-IT
		
		}// END IF PATIENT IS NOT RE-CONFIRMING THEN RUN THIS CODE
		
		//AFTER ALL INSERTION , REDIRECTS TO SCHEDULER PAGE
		
		?>
		<script>
			var pId = '<?php echo $insertPatientDataId; ?>';
			var pConfId = '<?php echo $insertConfirmationId; ?>';			
			//location.href='mainpage.php?patient_id='+pId+'&pConfId='+pConfId;
			location.href='home.php?saveConfirm=true';
		</script>		  <!--REDIRECTING TO FORMS APPLICATION-->
		<?php
	//END REDIRECTS TO SCHEDULER PAGE
}
// END SAVE BUTTON CLICKED
?>
<script type="text/javascript">
	var today = new Date();
	var day = today.getDate();
	var month = today.getMonth()
	var year = y2k(today.getYear());
	var mon=month+1;
	if(mon<=9){
		mon='0'+mon;
	}
	var todaydate=mon+'-'+day+'-'+year;
	function y2k(number){
		return (number < 1000)? number+1900 : number;
	}
	function newWindow(q){
		
		mywindow=open('mycal1.php?md='+q,'','width=205,height=260,top=200,left=300');
		//mywindow.location.href = 'mycal1.php?md='+q;
		if(mywindow.opener == null)
			mywindow.opener = self;
	}
	function restart(q){
		fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
		if(q==8){
			if(fillDate > todaydate){
				alert("Date Of Service can not be a future date")
				return false;
			}
		}
		//document.getElementById("date"+q).value=fillDate;
		document.getElementById("date"+q).value=fillDate;
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}


function doDateCheck(from, to) {
	if(chkdate(to) && chkdate(from) ){
	
	if (Date.parse(from.value) >= Date.parse(to.value)) {
	//alert("The dates are valid.");
	}
	else {
		if (from.value == "" || to.value == ""){ 
		//alert("Both dates must be entered.");
		}
		else{ 
		to.value="";
		alert("Date of birth can not be greater than current date.");
		   }
		}
	}
}

function  redirect_page(pagename) {
	$('#myModal .modal-body>p').addClass('text-center');
	$('#myModal .modal-body>p').html("Are you sure to cancel.");
	$("#missing_feilds").addClass('hidden');
	$("#cancel_yes").removeClass('hidden');
	$("#cancel_no").removeClass('hidden');
	$('#myModal').modal('show');
}

function validtion_pt_confirm(){
	
	$(".mandatory_multi").removeClass('mandatory_multi');
	
	var msg = '<b>Please enter following fields:</b><br /><ul style="margin-bottom:0;">';
	var flag = 0;
	f1 = document.frm_patient_confirm.first_name.value;
	f2 = document.frm_patient_confirm.last_name.value;
	f3 = document.frm_patient_confirm.sex_list.value;
	f3A = document.frm_patient_confirm.patient_site_list.value;
	
	if(document.frm_patient_confirm.surgeon_name_id) {
		f4 = document.frm_patient_confirm.surgeon_name_id.value;
	}else {
		f4 = document.frm_patient_confirm.surgeon_name.value;
	}
	f4A = document.frm_patient_confirm.patient_status_list.value;

	f4B = document.frm_patient_confirm.discharge_status.value;
	if(document.frm_patient_confirm.anes_name_id) {
		f5 = document.frm_patient_confirm.anes_name_id.value;
	}else {
		f5 = document.frm_patient_confirm.anes_name.value;
	}
	if(document.frm_patient_confirm.conf_nurse_id) {
		f6 = document.frm_patient_confirm.conf_nurse_id.value;
	}else {
		f6 = document.frm_patient_confirm.conf_nurse.value;
	}
	f7 = document.frm_patient_confirm.address1.value;
	f8 = document.frm_patient_confirm.city.value;
	f9 = document.frm_patient_confirm.state.value;
	f10 = document.frm_patient_confirm.zip.value;
	f11 = document.frm_patient_confirm.dob.value;
	f12 = document.frm_patient_confirm.prim_proc.value;
	//f13 = document.frm_patient_confirm.sec_proc.value;
	f14 = document.frm_patient_confirm.dos.value;
	f15 = document.frm_patient_confirm.home_phone.value;
	f16 = document.frm_patient_confirm.work_phone.value;
	
	if(f1==''){	msg+='<li>First Name</li>'; ++flag; $("#first_name").addClass('mandatory_multi');}
	if(f2==''){	msg+='<li>Last Name</li>'; ++flag; $("#last_name").addClass('mandatory_multi');}
	if(f3==''){	msg+='<li>Sex</li>'; ++flag; $("#sex_list_id").next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');}
	if(f3A==''){msg+='<li>Site</li>'; ++flag; $("#pos_op_site_id").next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');}
	
	if(f4==''){	msg+='<li>Surgeon Name</li>'; ++flag; $("#surgeon_name_id").next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');}
	if(f4A==''){msg+='<li>Patient Status</li>'; ++flag; $("#patient_status_list_id").next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');}
	if( f4A == 'Checked-Out' && f4B == '')
	{
		msg+='<li>Discharge Status</li>'; ++flag; 
		$("#discharge_status").addClass('required').selectpicker('refresh').trigger('change');
	}
	if(f5==''){	msg+='<li>Anesthesiologist Name</li>'; ++flag; $("#anes_name_id").next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');}
	if(f6==''){	msg+='<li>Checked-in By</li>'; ++flag; $("#conf_nurse_id").next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');}
	if(f7==''){	msg+='<li>Address 1</li>'; ++flag; $("#op_prep_solution").addClass('mandatory_multi');}
	if(f8==''){	msg+='<li>City</li>'; ++flag; $("#city").addClass('mandatory_multi');}
	if(f9==''){	msg+='<li>State</li>'; ++flag; $("#state").addClass('mandatory_multi');}
	if(f10==''){msg+='<li>Zip</li>'; ++flag; $("#zip").addClass('mandatory_multi');}
	if(f11==''){ msg+='<li>Date Of Birth</li>'; ++flag; $("#date1").addClass('mandatory_multi');}
	if(f12==''){ msg+='<li>Primary Procedure</li>'; ++flag; $("#prim_proc").next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');}
	//if(f13==''){ msg+='. Secondary Procedure\n'; ++flag; }
	if(f14==''){ msg+='<li>DOS</li>'; ++flag; $("#date2").addClass('mandatory_multi');}
	if((f15=='') && (f16=='')){
		msg+='<li>Home Phone Or Work Phone</li>'; ++flag;
		$("#home_phone").addClass('mandatory_multi');
		//$("#work_phone").addClass('mandatory_multi');
	}
	
	msg+='</ul>';
	if(flag>0){
		$('#myModal .modal-body>p').removeClass('text-center');
		$('#myModal .modal-body>p').html(msg);
		$("#missing_feilds").removeClass('hidden');
		$("#cancel_yes").addClass('hidden');
		$("#cancel_no").addClass('hidden');
		$('#myModal').modal('show');
		return false;
	}else{
		//alert('Hi')
		document.frm_patient_confirm.submit();
		return true;
	}	
}

function chkPtStatus(patientStatus,objCheckInOutTimeId) {
	if(objCheckInOutTimeId) {
		objCheckInOutTimeId.disabled=true;
		if(patientStatus=="Checked-In" || patientStatus=="Checked-Out") {
			objCheckInOutTimeId.disabled=false;		
		}
	}
	
	var dsObj = $("#discharge_status");
	if( patientStatus == "Checked-Out")
	{
		dsObj.addClass('required').selectpicker('refresh').trigger('change');
	}
	else
	{
		dsObj.removeClass('required').selectpicker('refresh').next("div.bootstrap-select").children('button.dropdown-toggle').removeClass('mandatory_multi');
	}
}
function showHippaOtherTxtBox(obj1,obj2,obj3){
	var obj1Id = obj1;
	var obj2Id = obj2;
	var obj3Id = obj3;
	if(document.getElementById(obj1Id).value == 'Other'){
			$("#"+obj1Id).selectpicker('hide');
			document.getElementById(obj2Id).style.display = 'inline-block';
			document.getElementById(obj3Id).style.display = 'inline-block';		
	}
}

function showHippaComboBox(obj1,obj2,obj3){
	var obj1Id = obj1;
	var obj2Id = obj2;
	var obj3Id = obj3;
	document.getElementById(obj2Id).value = "";
	document.getElementById(obj2Id).style.display = 'none';
	document.getElementById(obj3Id).style.display = 'none';		
	//document.getElementById(obj1Id).style.display = 'block';
	$("#"+obj1Id).selectpicker('show');	
	document.getElementById(obj1Id).selectedIndex = 0;
}

//START CODE FOR MULTI SURGERYCENTER
function topInViewport(element) {
    return $(element).offset().top >= $(window).scrollTop() && $(element).offset().top          <= $(window).scrollTop() + $(window).height();
}
$(window).on("load resize scroll",function(e){
    topInViewport($("#my_modal"))
});
$(document).ready(function() {
	  $(".dropdown").click(            
		function() {
			$(this).children().closest('.dropdown-menu', this).stop( true, true ).slideToggle("fast");
			$(this).siblings().removeClass('active');
			$(this).siblings().children('.dropdown-menu').hide();						
			$(this).addClass('active');
			$(this).children().closest('.dropdown-menu').children('li').on('click',function(){
				
				$(this).parent().closest('.dropdown-menu').slideUp('fast');
				
			 });
			$(this).children().closest('.dropdown-menu').children('li a').on('click',function(){
				
				$(this).parent().parent().closest('.dropdown-menu').slideUp('fast');
				
			 });
		});
		
		$(".dropdown-menu #a_allactivecancelled").click(function(){
			$("#allactivecancelled").hide();	
		});
		
		$(".dropdown-menu .radioFilter").prop('checked','true',function(){
			$("#allactivecancelled").hide();	
		});						
		
		/*$(".selectpicker").selectpicker(function(){	
				
		});*/
		
		$('.edit_btn').on('click',function(){
		  
		$('#my_modal').modal({
			show: true,
			backdrop: true,
			keyboard: false
		});
	  
	 });
	 
	$('.datetimepicker, .datepickertxt').datetimepicker({format: 'MM-DD-YYYY'});	 
	$(window).load(function()
	{
		var LDL	=	function()
		{
			var H	=	parent.top.$("#div_middle").height()- $("#div_innr_btn",top.frames[0]).outerHeight();
			
		}
		LDL();
		$(window).resize(function(e) {
		   LDL();
		});
		
		
	});


});

//END CODE FOR MULTI SURGERYCENTER 
</script>
<div id="post" style="display:none;"></div>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
dragresize.apply(document);	
$(document).ready(function(){
		
	var height = $("#my_modal").height();
	
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.80;
	window.moveTo(0,0);
	window.resizeTo(W,height+95);
	var photoLeftPos = (W/1.3);
	$("#ptPhotoMainDiv").css("left",photoLeftPos); //TO SET LEFT POSITION OF PATIENT PHOTO (IF EXISTS)
	
	$("#mydiv").css({top: 200, left: 200});
	$("div.required").removeClass('required'); /*Remove required class from selector picker divs*/
	
	$(".required").on('input',function(){
		if($(this).val()=="") {
			$(this).addClass("mandatory_multi");
		}else{
			$(this).removeClass("mandatory_multi");
		}
	});
	
	$('body').on('change',".required",function(){
		if($(this).val()=="") {
			$(this).next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');
		}else{
			$(this).next("div.bootstrap-select").children('button.dropdown-toggle').removeClass('mandatory_multi');
		}
	});
	
	$('body').on('change loaded.bs.select',"#prim_proc,#sec_proc,#ter_proc",function(){
		
		var d = $(this).find('option:selected').data('del');
		if( d == 1) {
			$(this).selectpicker('setStyle','btn-danger','add');
		}
		else 
			$(this).selectpicker('setStyle','btn-danger','remove');
	});

	$(".required").each(function(){
		if($(this).val()=="") {
			if($(this).is('select')) {
				$(this).next("div.bootstrap-select").children('button.dropdown-toggle').addClass('mandatory_multi');
			}
			else{
				if($(this).attr('id')=="home_phone" || $(this).attr('id')=="work_phone") {
					if($("#home_phone").val()=="" && $("#work_phone").val()=="") {
						$("#home_phone").addClass("mandatory_multi");
					}
				}
				else{
					$(this).addClass("mandatory_multi");
				}
			}
		}
	});
});
</script>


<?php include("common/pre_defined_popup.php"); 

//GET AGE OF PATIENT
$patient_ageNew=$objManageData->dob_calc($patient_dob_temp);
//END GET AGE OF PATIENT
?>

<!--START NEW DESIGN-->
<form name="frm_patient_confirm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="patient_confirm.php">
    <input type="hidden" name="divId" id="divId">
    <input type="hidden" name="counter" id="counter">
    <input type="hidden" name="stub_id" value="<?php echo $_REQUEST['stub_id']; ?>">
    <input type="hidden" name="secondaryValues" id="secondaryValues">
    <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
    <input type="hidden" name="ascId_hidden" id="ascId_hidden" value="<?php echo $ascId_hidden;?>">
    <input type="hidden" name="primary_procedure_id" value="<?php echo $PrimaryProcedureId;?>">
    <input type="hidden" name="secondary_procedure_id" value="<?php echo $SecondaryProcedureId;?>">
    <input type="hidden" name="tertiary_procedure_id" value="<?php echo $TertiaryProcedureId;?>">
    <input type="hidden" name="surgery_time" value="<?php echo $surgery_time;?>">
    <input type="hidden" name="pickup_time" value="<?php echo $pickup_time;?>">
    <input type="hidden" name="arrival_time" value="<?php echo $arrival_time;?>">
    <input type="hidden" name="reConfirmId" value="<?php echo $reConfirmId;?>">
    <input type="hidden" name="imwPatientId" value="<?php echo $imwPatientId; ?>">
    <input type="hidden" name="patient_id_stub" value="<?php echo $patient_id_stub; ?>">
    <input type="hidden" name="ascId" value="<?php echo $ascId;?>">
    <input type="hidden" name="pt_conf_submit" value="Save">
    <input type="hidden" name="imw_patient_image_path" value="<?php echo $imw_patient_image_path;?>">
    <input type="hidden" name="scemr_patient_image_path" value="<?php echo $scemr_patient_image_path;?>">
	<input type="hidden" name="iolink_patient_in_waiting_id" value="<?php echo $iolink_patient_in_waiting_id; ?>">
    <div class="modal_multi relative_full_width_modal" id="my_modal">
      <div class="modal_multi-dialog modal_multi-lg" style="width:1165px; " id="my_modal_child">
        <div class="modal_multi-content">
          <div class="modal_multi-header text-center">
            <button style="color:#FFFFFF;opacity:0.9" type="button" class="close" data-dismiss="modal_multi" aria-label="Close" onClick="javascript:redirect_page('home.php');"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal_multi-title rob"><span style="color:#FFFFFF">Patient Check In </span><span class="ASC"> ASC-<?php echo $ascId;?> </span></h4>
            
                    
            		<?php
					//START GET PATIENT PHOTO
					$ptPhotoPath = "";
					if(trim($scemr_patient_image_path)) {
						$ptPhotoPath = trim($scemr_patient_image_path);
						$ptPhotoFullPath = 'admin/'.$scemr_patient_image_path;	
					}else if(trim($imw_patient_image_path)) {
						$ptPhotoPath = trim($imw_patient_image_path);
						$ptPhotoFullPath = '../'.$imwDirectoryName.'/interface/main/uploaddir'.$ptPhotoPath;	
					}
					if($ptPhotoPath && file_exists($ptPhotoFullPath)) {
						$newSize = 'width="150" height="112"';
						$priImageSize = getimagesize($ptPhotoFullPath);	
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],150);
						?>
						<div id="ptPhotoMainDiv" class="drsElement drsMoveHandle" style="top:10px;  width:150px; height:112px;position:absolute;z-index:12">
							<div style="width:150px; height:112px;" class="<?php echo $rsNote_bk_class; ?>">
								<a class="btn btn-danger padding_2" onClick="document.getElementById('ptPhotoMainDiv').style.display='none';" title="Delete"  href="javascript:void(0)" style="cursor:hand; position:absolute; right:0px; top:0px;z-index:12">
									<b class="fa fa-times"></b>
								</a>
								<img src="<?php echo $ptPhotoFullPath;?>" <?php echo $newSize; ?> >
							</div>
							<div class="clearfix"></div>
						</div>                    
						<?php
					}
					//END GET PATIENT PHOTO
						/*
						*
						* Epost Alerts Display starts here
						*
						*/
            
						$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'alert' AND patient_id='".$_REQUEST['patient_id']."' AND patient_conf_id = '$pConfId'  ".$andstubIdQry;
						$rsNotes =imw_query($query_rsNotes);
						$totalRows_rsNotes =imw_num_rows($rsNotes);
					?>
            		<div id="epostMainDiv" style="position:absolute; transparent ; top:30px; left:10px; width:auto height:auto; "> 
                    		<?php 
								if($totalRows_rsNotes>0)
								{
									$intCountChild = 0;
									$top = 0;
									$left = 0;
									$rsNotes1 = array();
									while($row = imw_fetch_array($rsNotes)) {
                       						$rsNotes1[]	=	$row;
											$tableID		= $row['epost_id'];
											//echo showEpostDiv($tableID,$pConfId,$intCountChild,$top,$left);
											$qryGetEpostedata = "SELECT epost_data,T_time, TIME_FORMAT(T_time, '%l:%i %p') as ePostTime,
                                               	 									consent_template_id, consentAutoIncId
																					FROM eposted WHERE epost_id = '$tableID' AND
																					patient_conf_id = '$pConfId' ";
                        					$resGetEpostedata	=	imw_query($qryGetEpostedata) or die(imw_error());
											$intTotRowRetrive = imw_num_rows($resGetEpostedata);
											$rowResGetEpostedata = imw_fetch_assoc($resGetEpostedata);
											$consentMultipleAutoIncrId = $rowResGetEpostedata['consentAutoIncId'];
											$consentTemplateIdEpost = $rowResGetEpostedata['consent_template_id'];
											//$EpostTime = $rowResGetEpostedata['ePostTime'];
											$EpostTime = $objManageData->getTmFormat($rowResGetEpostedata['T_time']);
											$epostdata = stripslashes($rowResGetEpostedata['epost_data']);
                       						
											if($intTotRowRetrive>0) 
											{
								?>
                                				<div id="epostMainDivChild<?php echo $intCountChild;?>" class="drsElement drsMoveHandle" style="top:<?php echo $top;?>px; left:<?php echo $left;?>px;width:310px;position:absolute;background-color:transparent;border:0px none;z-index:11">
                                                		<div class="epostHead <?php echo $rsNote_bk_class; ?>" style="width: 100%;text-align:left;border-top-right-radius:5px;border-top-left-radius:5px;">
                                                        		<span style=""> Patient Alert </span>
                                                                <span style=""><?php echo $EpostTime; ?></span>
                                                                <button style="opacity: .9;" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="document.getElementById('epostMainDivChild<?php echo $intCountChild;?>').style.display='none';">
                                                                		<span aria-hidden="true" style="">×</span>
                                                               	</button>
                                                     	</div>
                                                        
                                                        <div class="text-left" style="width: 100%;border: 1px solid #ababab;border-top: none;border-bottom:1px solid #EEEEEE;background-color:#FFFFFF;height:80px; overflow-y: auto;overflow-x:auto; padding: 5px;">
                                                        		<?php echo $epostdata; ?>
                                                      	</div>
                                                        
                                                        <div class="text-left" style="width:100%;border: 1px solid #ababab;border-top:none;border-bottom-right-radius:5px;border-bottom-left-radius:5px;background-color:#FFFFFF;padding:5px;">
                                                        	<?php if($loginUserType <> 'Surgeon') { ?>
                                                          <a id="CancelBtn" class="btn btn-danger epost_del" onClick="deleteEpost(<?php echo $tableID; ?>,'<?php echo $consentTemplateIdEpost;?>','<?php echo $consentMultipleAutoIncrId?>','<?php echo $pConfId;?>');" href="javascript:void(0)">
                                                          			<b class="fa fa-times"></b>&nbsp;Delete
                                                    			</a>
                                                          <?php } ?>
                                                        
                                                      	</div>
                                                        
                                                        <div class="clearfix"></div>
                                          	</div>
                                            
                        		<?php 
												$intCountChild++;
												$left+=320;
												if($left==960)
												{
													//$top = 0;
													$left = 0;
													$top+=160;
												}
											}
											
                   					 }
								}
							?>
                	</div>
                
          </div>
          <div class="modal_multi-body">
                <div class="form_inside_modal_multi">
                 <!--  ModalS Forms STARTEd -->
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="first_name"> 
                                        Patient Name		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <div class="row">                            
                                        <div class="col-md-5 col-lg-5 col-xs-5 col-sm-5">
                                            <input type="text" name="first_name" id="first_name" class="form-control required capitalize" tabindex="1" value="<?php echo $patient_first_name;?>">
                                            <small class="text-left itlc_padd_11" for="first_name">First Name</small>
                                        </div>
                                        <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_0">
                                            <input type="text" name="middle_name" id="middle_name" class="form-control capitalize" tabindex="1" value="<?php echo $patient_middle_name;?>">
                                            <small class="text-left itlc_padd_11" for="middle_name">MI</small>
                                        </div>
                                        <div class="col-md-5 col-lg-5 col-xs-5 col-sm-5">
                                            <input type="text" name="last_name" id="last_name" class="form-control required capitalize" tabindex="1" value="<?php echo $patient_last_name;?>">
                                            <small class="text-left itlc_padd_11" for="last_name">Last Name</small>
                                        </div>
                                   </div>   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="date1"> 
                                         DOB <?php if($patient_ageNew) {?>(Age  <?php echo $patient_ageNew;?>)	<?php }?>
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <input type="hidden" name="from_date_byram" value="<?php echo(date("m-d-Y"));?>" />
                                	<div class="input-group datepickertxt">
                                    		<input type="text" aria-describedby="basic-addon1" id="date1" name="dob" placeholder="MM-DD-YYYY" class="form-control  datepickertxt required" tabindex="1" value="<?php echo $patient_dob;?>">
                                      <div class="input-group-addon datepicker">
                                            <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                        </div>
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix visible-sm"></div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="sex_list_id"> 
                                        Sex		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <select name="sex_list" id="sex_list_id" class="selectpicker field text_10 required" style="width:115px;border:1px solid #cccccc;">
                                        <option value="">Select</option>
                                        <option value="m" <?php if($patient_sex=="m") { echo "selected"; }?> >Male</option>
                                        <option value="f" <?php if($patient_sex=="f") { echo "selected"; }?> >Female</option>
                                    </select>
                                     
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                     <div class="clearfix hidden-sm"></div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="patient_status_list_id"> 
                                         Patient Status		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <select name="patient_status_list" id="patient_status_list_id" class="selectpicker required"  style="width:115px;border:1px solid #cccccc;" onChange="javascript:chkPtStatus(this.value,document.getElementById('checkInOutTimeId'));">
                                        <option value="">Select</option>
                                        <option value="Checked-In" <?php if($patient_status=="Checked-In" || $patient_status=="Scheduled") { echo "selected"; }?> >Checked-In</option>
                                        <option value="Checked-Out" <?php if($patient_status=="Checked-Out") { echo "selected"; }?> >Checked-Out</option>
                                        <option value="Canceled" <?php if($patient_status=="Canceled") { echo "selected"; }?>>Cancelled</option>
                                        <option value="No Show" <?php if($patient_status=="No Show") { echo "selected"; }?>>No Show</option>
                                        <option value="Aborted Surgery" <?php if($patient_status=="Aborted Surgery") { echo "selected"; }?>>Aborted Surgery</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix visible-sm"></div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="checkInOutTimeId"> 
                                         CI/CO Time	
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <div class="input-group">
                                      <span id="basic-addon1" class="input-group-addon" onClick="displayTimeAmPm('checkInOutTimeId');" style="cursor:pointer;"><b class="fa fa-clock-o"></b></span>
                                      <input type="text" aria-describedby="basic-addon1" name="checkInOutTime" id="checkInOutTimeId" onClick="if(this.value=='') {displayTimeAmPm('checkInOutTimeId');}" class="form-control" tabindex="1" value="<?php echo $objManageData->getTmFormat($patientCheckInOutTime);?>"  />
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix visible-sm"></div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="chbxAdvanceDirective_yes"> 
                                        Advanced Directive		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <select class="selectpicker" name="chbxAdvanceDirective" id="chbxAdvanceDirective_yes" tabindex="1">
                                        <option value="">Select Any</option>
                                        <option value="Yes" <?php if($advanceDirective=='Yes') echo "SELECTED"; ?> > Yes </option>
                                        <option value="No" <?php if($advanceDirective=='No') echo "SELECTED"; ?>> No </option>
                                     </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="clearfix visible-sm"></div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="op_prep_solution"> 
                                         Address		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <div class="row">                            
                                        <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                          <textarea id="op_prep_solution" name="address1"  class="form-control required capitalize"  tabindex="1" style="height:35px;"  ><?php echo stripslashes($patient_address1);?></textarea>
                                          <small class="text-left itlc_padd_11" for="Address1">Address1</small>
                                        </div>
                                        <div class="clearfix visible-xs margin_clear_adjust"></div>
                                        <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                          <textarea name="address2" id="address2" class="form-control capitalize" style="height:35px; " tabindex="1"  ><?php echo stripslashes($patient_address2);?></textarea>
                                          <small class="text-left itlc_padd_11" for="Address2">Address2</small>
                                        </div>
                                       <div class="clearfix visible-xs margin_clear_adjust"></div>
                                        <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                            <div class="row">                            
                                                <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 padding_adjust_modal">
                                                  <input type="text"  name="city" id="city" class="form-control required capitalize" tabindex="1" value="<?php echo stripslashes($patient_city);?>"  />
                                                  <small class="text-left itlc_padd_11" for="City">City</small>
                                                </div>
                                              <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 padding_adjust_modal ">
                                                  <input type="text"  name="state" id="state" class="form-control required capitalize" tabindex="1" value="<?php echo stripslashes($patient_state);?>"  />
                                                  <small class="text-left itlc_padd_11" for="State">State</small>
                                              </div>
                                               <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 padding_adjust_modal">
                                                  <input type="text"  name="zip" id="zip" class="form-control required" tabindex="1" value="<?php echo $patient_zip;?>"  />
                                                  <small class="text-left itlc_padd_11" for="Zip">Zip</small>
                                              </div>
                                           </div>   
                                        </div>
                                   </div>   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix visible-sm"></div>
                     <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="home_phone"> 
                                            Home Phone	
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <input type="text" aria-describedby="basic-addon1"  name="home_phone" id="home_phone" class="form-control required" tabindex="1" value="<?php echo $patient_home_phone;?>"  />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="work_phone"> 
                                         Work Phone	
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                      <input type="text" aria-describedby="basic-addon1"  name="work_phone" id="work_phone" class="form-control required" tabindex="1" value="<?php echo $patient_work_phone;?>"  />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    	<div class="form_inner_m">
                      	<div class="row">
                        	<!-- Discharge Status -->
                          <div class="col-md-7 col-lg-7 col-xs-7 col-sm-6">
                              <label class="text-left" for="discharge_status"> 
                                   Discharge Status
                              </label>
                          </div>
                          
                          <!-- Opt Out -->
                          <div class="col-md-5 col-lg-5 col-xs-5 col-sm-6 text-center">
                          	<label class="text-left" for="chk_no_publicity">No Publicity</label>
                         	</div>      
                          
                          <div class="col-lg-7 col-md-7 col-sm-6 col-xs-7">
                          	<select name="discharge_status" id="discharge_status" class="selectpicker <?=($patient_status=="Checked-Out"?'required':'')?>" >
                              <option value="">Select Any</option>
                              <option value="1" <?php echo ($discharge_status == 1 ? 'selected' :'');?> e >Discharged to Home</option>
                              <option value="2" <?php echo ($discharge_status == 2 ? 'selected' :'');?>>Transferred to Hospital</option>
                              <option value="3" <?php echo ($discharge_status == 3 ? 'selected' :'');?>>Discharged to Nursing Home</option>
                              <option value="50" <?php echo ($discharge_status == 50 ? 'selected' :'');?>>Discharged to Hospice</option>
                              <option value="21" <?php echo ($discharge_status == 21 ? 'selected' :'');?>>Discharged to Law Enforcement</option>
                            </select> 
                        	</div>
                          
                          <div class="col-lg-5 col-md-5 col-sm-6 col-xs-5 margin_top_5 text-center">
                          	<div class="checkbox-inline">
                            	<input type="checkbox" name="chk_no_publicity" id="chk_no_publicity" value="1" <?php echo ($no_publicity == 1 ? 'checked' :'');?> />
                           	</div> 	
                        	</div>
                          
                        </div>
                     	</div>
                    </div>
                   <div class="clearfix margin_clear_adjust_full"></div>
                   <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="surgeon_name_id"> 
                                        Surgeon
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <select name="surgeon_name_id"  id="surgeon_name_id" class="selectpicker required">
                                        <option value="">Select Any</option>
                                        <?php
                                        $getSurgeosDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
                                        foreach($getSurgeosDetails as $surgeonsList){
                                            $usersId = $surgeonsList->usersId;
                                            $surgeonFname = trim($surgeonsList->fname);
                                            $surgeonLname = trim($surgeonsList->lname);
                                            $surgeonMname = trim($surgeonsList->mname);
                                            if($surgeonMname) {
                                                $surgeonMname = ' '.$surgeonMname;
                                            }
                                            $surgeonName = $surgeonFname.$surgeonMname.' '.$surgeonLname;
                                            $surgeon_deleteStatus = $surgeonsList->deleteStatus;
                                            if($surgeon_deleteStatus=="Yes") {
                                            }else{
                                                $surgeonSelected="";
                                                if($reConfirmId) {  //IF PATIENT IS RECONFIRMING THEN MATCH 'SURGEON ID' WITH EXISTING 'SURGEON ID' 
                                                    if($surgeonId == $usersId) {
                                                        $surgeonSelected = "selected";
                                                    }
                                                }else {  //IF PATIENT IS CONFIRMING FROM IMW THEN MATCH 'SURGEON NAME' WITH 'SURGEON NAME' FROM IMW(i.e stub_tbl) 
                                                    if($surgeon_name == trim($surgeonName)) {
                                                        $surgeonSelected = "selected";
                                                    }
                                                }
                                            ?>
                                                <option value="<?php echo $usersId; ?>" <?php echo $surgeonSelected;?>><?php echo stripslashes($surgeonLname.', '.$surgeonFname.' '.$surgeonMname); ?></option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>                                 
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="date2"> 
                                         DOS	
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <div class="input-group datetimepicker">
                                      <input type="text" aria-describedby="basic-addon1" id="date2" name="dos" class="form-control datepickertxt required" tabindex="1" value="<?php echo $patient_dos;?>"  />
                                    	 <div class="input-group-addon datepicker">
                                            <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                        </div>
                                     </div>
                                     
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix visible-sm"></div>
                   
                     
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="assist_by_trans"> 
                                        Assisted By Translator		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <select class="selectpicker" name="assist_by_trans" id="assist_by_trans" tabindex="1">
                                        <option value="">Select Any</option>
                                        <option value="yes" <?php if($assist_by_trans=="yes") { echo "SELECTED"; }?> > Yes </option>
                                        <option value="no" <?php if($assist_by_trans=="no") { echo "SELECTED"; }?> > No </option>
                                     </select>
                                </div>
                            </div>
                        </div>
                    </div>
                   <div class="clearfix visible-sm"></div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                    <label class="text-left" for="prim_proc"> 
                                       Primary Procedure
                                    </label>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                    <label class="text-left" for="prim_proc"> 
                                       Primary Site
                                    </label>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                     <select name="prim_proc" id="prim_proc" class="selectpicker show-tick required" data-toggle='tooltip' data-trigger='focus' data-placement='top'>
                                        <option value="">Select Any</option>
                                        <?php
                                        $getProcedureDetails = imw_query("select * from  procedures order by `name`");
                                        while($PrimaryProcedureList=imw_fetch_array($getProcedureDetails)){
                                            $ProcedureId = $PrimaryProcedureList['procedureId'];
                                            $ProcedureCatergoryId = $PrimaryProcedureList['catId'];
                                            $ProcedureName = trim(stripslashes($PrimaryProcedureList['name']));
                                            $ProcedureAliasName = trim(stripslashes($PrimaryProcedureList['procedureAlias']));
                                            $priDelStatus = trim($PrimaryProcedureList['del_status']);
                                            
											if($ProcedureCatergoryId=='2'){
                                                $category_display="(LP)&nbsp;";
                                            }
                                            else{
                                                $category_display="";
                                            }
                                            
                                            if($priDelStatus=="yes" && $ascId_hidden!="") {//if procedure is deleted and not checked-in then do not show this procedure in dropdown
                                                continue;	
                                            }
                                            $priSel = "";
											if($PrimaryProcedureId && $PrimaryProcedureId == $ProcedureId) {
                                                $priSel = "selected";
                                            }
											if(!$PrimaryProcedureId && $patient_prim_proc && ($patient_prim_proc==$ProcedureName || $patient_prim_proc==$ProcedureAliasName)) { 
                                                $priSel = "selected"; 
                                            }
                                            if(!$priSel && $priDelStatus=="yes") {//if procedure is deleted and not in selected list then do not show this procedure in dropdown
                                                continue;	
                                            }
											if($priDelStatus=="yes" && $PrimaryProcedureId && $PrimaryProcedureId != $ProcedureId) {//if procedure is deleted and selected id not matched with deleted procedure id then do not show this procedure in dropdown
                                                continue;	
                                            }
											
											$priTmpSel = false;
											if($PrimaryProcedureId == $ProcedureId && $ProcedureName <> $patient_prim_proc)
											{
												$priTmpSel = true; $priSel = ""; $priStyle = ($priDelStatus=="yes") ? 'style="color:red;" data-del="1" ' : '';
												echo '<option value="'.$patient_prim_proc.'$$'.$PrimaryProcedureId.'" '.$priStyle.' selected >'.$patient_prim_proc.'</option>';
											}
											
											if( $priTmpSel && $priDelStatus=="yes" ) continue;

											$priColor = ($priDelStatus=="yes" && $PrimaryProcedureId && $PrimaryProcedureId == $ProcedureId ) ? 'style="color:red;"' : '';
											$priDataIcon = ($priDelStatus=="yes" && $priSel) ? 'data-del="1"' : 'data-del="0"';
											
                                            ?>
                                            <option value="<?php echo $ProcedureName.'$$'.$ProcedureId; ?>" <?php echo $priSel;?> <?php echo $priColor.' '.$priDataIcon; ?> ><?php echo $category_display.$ProcedureName;?></option>
                                            <?php	  
                                        }
                                        ?>
                                    </select>
                                </div>
								<?php 
                                if($ascId_hidden && !$sec_patient_site && ($SecondaryProcedureId || $SecondaryProcedureName)) {
                                    $sec_patient_site	=		$patient_site;
                                    $onChangePrimProc	=	'oncChange="secSiteUpdate();"';
                                }
								if($ascId_hidden && !$ter_patient_site && ($TertiaryProcedureId || $TertiaryProcedureName)) {
                                    $ter_patient_site	=		$patient_site;
                                }
                                ?>
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                     <select name="patient_site_list" id="pos_op_site_id" class="selectpicker required " data-toggle="tooltip" data-trigger="focus" data-placement="top"  <?=$onChangePrimProc?> >
                                        <option value="" >Select Any</option>
                                        <option value="left" <?php if($patient_site == "left") { echo "selected"; }?> >Left</option>
                                        <option value="right" <?php if($patient_site == "right") { echo "selected"; }?> >Right</option>
                                        <option value="both" <?php if($patient_site == "both") { echo "selected"; }?> >Bilateral</option>
                                        <option value="left upper lid" 	<?php if($patient_site=='left upper lid') 	{ echo 'selected'; }?>>Left Upper Lid</option>
                                        <option value="left lower lid" 	<?php if($patient_site=='left lower lid') 	{ echo 'selected'; }?>>Left Lower Lid</option>
                                        <option value="right upper lid" <?php if($patient_site=='right upper lid') 	{ echo 'selected'; }?>>Right Upper Lid</option>
                                        <option value="right lower lid" <?php if($patient_site=='right lower lid')	{ echo 'selected'; }?>>Right Lower Lid</option>
                                        <option value="bilateral upper lid" <?php if($patient_site=='bilateral upper lid') 	{ echo 'selected'; }?>>Bilateral Upper Lid</option>
                                        <option value="bilateral lower lid" <?php if($patient_site=='bilateral lower lid')	{ echo 'selected'; }?>>Bilateral Lower Lid</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                    <label class="text-left" for="sec_proc"> 
                                       Secondary Procedure
                                    </label>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                    <label class="text-left" for="sec_pos_op_site_id"> 
                                        Secondary Site
                                    </label>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                    <select name="sec_proc" id="sec_proc" class="selectpicker show-tick" onChange="secSiteUpdate();" data-placement='top'>
                                        <option value="">Select Any</option>
                                        <?php
                                        $getsecondaryDetails = imw_query("select * from procedures order by `name`");
                                        while($SecondaryProcedureList=imw_fetch_array($getsecondaryDetails)){
                                            $SecondaryProcedureid = $SecondaryProcedureList['procedureId'];
                                            $SecondaryProcedureCatergoryId = $SecondaryProcedureList['catId'];
                                            $SecondaryProcedureName = trim(stripslashes($SecondaryProcedureList['name']));
                                            $SecondaryAliasProcedureName = trim(stripslashes($SecondaryProcedureList['procedureAlias']));
                                            $secDelStatus = trim($SecondaryProcedureList['del_status']);
                                            if($SecondaryProcedureCatergoryId=='2'){
                                                $SecProcCategoryDisplay="(LP)&nbsp;";
                                            }
                                            else{
                                                $SecProcCategoryDisplay="";
                                            }	
                                            if($secDelStatus=="yes" && $ascId_hidden!="") {//if procedure is deleted and not checked-in then do not show this procedure in dropdown
                                                continue;	
                                            }
                                            $secSel = "";
											if($SecondaryProcedureId && $SecondaryProcedureId == $SecondaryProcedureid) {
                                                $secSel = "selected";
                                            }
											if(!$SecondaryProcedureId && $patient_sec_proc &&($patient_sec_proc==$SecondaryProcedureName || $patient_sec_proc==$SecondaryAliasProcedureName)) { 
                                                $secSel = "selected"; 
                                            }
                                            if(!$secSel && $secDelStatus=="yes") {//if procedure is deleted and not in selected list then do not show this procedure in dropdown
                                                continue;	
                                            }
											if($secDelStatus=="yes" && $SecondaryProcedureId && $SecondaryProcedureId != $SecondaryProcedureid) {//if procedure is deleted and selected id not matched with deleted procedure id then do not show this procedure in dropdown
                                                continue;	
                                            }
											
											$secTmpSel = false;
											if($SecondaryProcedureId == $SecondaryProcedureid && $SecondaryProcedureName <> $patient_sec_proc)
											{
												$secTmpSel = true; $secSel = ""; $secStyle = ($secDelStatus=="yes") ? 'style="color:red;" data-del="1" ' : '';
												echo '<option value="'.$patient_sec_proc.'$$'.$SecondaryProcedureid.'" '.$secStyle.' selected >'.$patient_sec_proc.'</option>';
											}

											if( $secTmpSel && $secDelStatus=="yes" ) continue;

											$secColor = ($secDelStatus=="yes" && $SecondaryProcedureId && $SecondaryProcedureId == $SecondaryProcedureid ) ? 'style="color:red;"' : '';
											$secDataIcon = ($secDelStatus=="yes" && $secSel) ? 'data-del="1" ' : 'data-del="0"';
                                            ?>
                                            <option value="<?php echo $SecondaryProcedureName.'$$'.$SecondaryProcedureid; ?>" <?php echo $secSel; ?> <?php echo $secColor.' '.$secDataIcon;?> ><?php echo $SecProcCategoryDisplay.$SecondaryProcedureName; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>                                 
                                </div>
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                     <select name="sec_patient_site_list" id="sec_pos_op_site_id" class="selectpicker " data-toggle="tooltip" data-trigger="focus" data-placement="top">
                                        <option value="">Select Any</option>
                                        <option value="left" <?php if($sec_patient_site=="left") { echo "selected"; }?> >Left</option>
                                        <option value="right" <?php if($sec_patient_site=="right") { echo "selected"; }?> >Right</option>
                                        <option value="both" <?php if($sec_patient_site=="both") { echo "selected"; }?> >Bilateral</option>
                                        <option value="left upper lid" 	<?php if($sec_patient_site=='left upper lid') 	{ echo 'selected'; }?>>Left Upper Lid</option>
                                        <option value="left lower lid" 	<?php if($sec_patient_site=='left lower lid') 	{ echo 'selected'; }?>>Left Lower Lid</option>
                                        <option value="right upper lid" <?php if($sec_patient_site=='right upper lid') 	{ echo 'selected'; }?>>Right Upper Lid</option>
                                        <option value="right lower lid" <?php if($sec_patient_site=='right lower lid')	{ echo 'selected'; }?>>Right Lower Lid</option>
                                        <option value="bilateral upper lid" <?php if($sec_patient_site=='bilateral upper lid') 	{ echo 'selected'; }?>>Bilateral Upper Lid</option>
                                        <option value="bilateral lower lid" <?php if($sec_patient_site=='bilateral lower lid')	{ echo 'selected'; }?>>Bilateral Lower Lid</option>
                                    </select>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="clearfix visible-sm"></div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                    <label class="text-left" for="ter_proc"> 
                                       Tertiary Procedure
                                    </label>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                    <label class="text-left" for="ter_pos_op_site_id"> 
                                        Tertiary Site
                                    </label>
                                </div>
                                
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                     <select name="ter_proc" id="ter_proc" class="selectpicker show-tick" onChange="terSiteUpdate();">
                                        <option value="">Select Any</option>
                                        <?php
                                        $getTertiaryDetails = imw_query("select * from  procedures order by `name`");
                                        while($TertiaryProcedureList=imw_fetch_array($getTertiaryDetails)){
                                            $TertiaryProcedureid = $TertiaryProcedureList['procedureId'];
                                            $TertiaryProcedureCatergoryId = $TertiaryProcedureList['catId'];
                                            $TertiaryProcedureName = trim(stripslashes($TertiaryProcedureList['name']));
                                            $TertiaryAliasProcedureName = trim(stripslashes($TertiaryProcedureList['procedureAlias']));
                                            $terDelStatus = trim($TertiaryProcedureList['del_status']);
                                            if($TertiaryProcedureCatergoryId=='2'){
                                                $TerProcCategoryDisplay="(LP)&nbsp;";
                                            }
                                            else{
                                                $TerProcCategoryDisplay="";
                                            }
                                            
                                            if($terDelStatus=="yes" && $ascId_hidden!="") {//if procedure is deleted and not checked-in then do not show this procedure in dropdown
                                                continue;	
                                            }
                                            $terSel = "";
											if($TertiaryProcedureId && $TertiaryProcedureId == $TertiaryProcedureid) {
                                                $terSel = "selected";
                                            }
											if(!$TertiaryProcedureId && $patient_ter_proc &&($patient_ter_proc==$TertiaryProcedureName || $patient_ter_proc==$TertiaryAliasProcedureName)) { 
                                                $terSel = "selected"; 
                                            }
                                            if(!$terSel && $terDelStatus=="yes") {//if procedure is deleted and not in selected list then do not show this procedure in dropdown
                                                continue;	
                                            }
											if($terDelStatus=="yes" && $TertiaryProcedureId && $TertiaryProcedureId != $TertiaryProcedureid) {//if procedure is deleted and selected id not matched with deleted procedure id then do not show this procedure in dropdown
                                                continue;	
                                            }
											
											$terTmpSel = false;
											if($TertiaryProcedureId == $TertiaryProcedureid && $TertiaryProcedureName <> $patient_ter_proc)
											{
												$terTmpSel = true; $terSel = ""; $terStyle = ($terDelStatus=="yes") ? 'style="color:red;" data-del="1" ' : '';
												echo '<option value="'.$patient_ter_proc.'$$'.$TertiaryProcedureId.'" '.$terStyle.' selected >'.$patient_ter_proc.'</option>';
											}
											
											if( $terTmpSel && $terDelStatus=="yes" ) continue;

											$terColor = ($terDelStatus=="yes" && $TertiaryProcedureId && $TertiaryProcedureId == $TertiaryProcedureid ) ? 'style="color:red;"' : '';
											$terDataIcon = ($terDelStatus=="yes" && $terSel) ? 'data-del="1"' : 'data-del="0"';	
                                            ?>
                                            <option value="<?php echo $TertiaryProcedureName.'$$'.$TertiaryProcedureid; ?>" <?php echo $terSel; ?> <?php echo $terColor.' '.$terDataIcon;?> ><?php echo $TerProcCategoryDisplay.$TertiaryProcedureName; ?></option>
                                          	<?php	  
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                     <select name="ter_patient_site_list" id="ter_pos_op_site_id" class="selectpicker " data-toggle="tooltip" data-trigger="focus" data-placement="top">
                                        <option value="">Select Any</option>
                                        <option value="left" <?php if($ter_patient_site=="left") { echo "selected"; }?> >Left</option>
                                        <option value="right" <?php if($ter_patient_site=="right") { echo "selected"; }?> >Right</option>
                                        <option value="both" <?php if($ter_patient_site=="both") { echo "selected"; }?> >Bilateral</option>
                                        <option value="left upper lid" 	<?php if($ter_patient_site=='left upper lid') 	{ echo 'selected'; }?>>Left Upper Lid</option>
                                        <option value="left lower lid" 	<?php if($ter_patient_site=='left lower lid') 	{ echo 'selected'; }?>>Left Lower Lid</option>
                                        <option value="right upper lid" <?php if($ter_patient_site=='right upper lid') 	{ echo 'selected'; }?>>Right Upper Lid</option>
                                        <option value="right lower lid" <?php if($ter_patient_site=='right lower lid')	{ echo 'selected'; }?>>Right Lower Lid</option>
                                        <option value="bilateral upper lid" <?php if($ter_patient_site=='bilateral upper lid') 	{ echo 'selected'; }?>>Bilateral Upper Lid</option>
                                        <option value="bilateral lower lid" <?php if($ter_patient_site=='bilateral lower lid')	{ echo 'selected'; }?>>Bilateral Lower Lid</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                     
                     <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="anes_name_id"> 
                                       Anesthesiologist
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <select name="anes_name_id" id="anes_name_id" class="selectpicker required">
                                        <option value="">Select Any</option>
                                        <?php
                                        $getAnesthesDetails = $objManageData->getArrayRecords('users', 'user_type', 'Anesthesiologist','lname','ASC');
                                        foreach($getAnesthesDetails as $anesList){
                                            $anesUsersId = $anesList->usersId;
                                            $anesFname = $anesList->fname;
                                            $anesLname = $anesList->lname;
                                            $anesMname = $anesList->mname;
                                            if($anesMname) {
                                                $anesMname = ' '.$anesMname;
                                            }
                                            $anesName = $anesFname.$anesMname.' '.$anesLname;
                                            $anes_deleteStatus = $anesList->deleteStatus;
                                            if($anes_deleteStatus=="Yes") {
                                            }else{
                                                $anesSelected="";
                                                if($reConfirmId) {  //IF PATIENT IS RECONFIRMING THEN MATCH 'ANES ID' WITH EXISTING 'ANES ID' 
                                                    if($anes_id == $anesUsersId) {
                                                        $anesSelected = "selected";
                                                    }
                                                }else {  //IF PATIENT IS CONFIRMING FROM IMW THEN MATCH 'ANES NAME' WITH 'ANES NAME' FROM IMW(i.e stub_tbl) 
                                                    if($patient_anes_name == $anesName) {
                                                        $anesSelected = "selected";
                                                    }
                                                }	
                                            ?>
                                                <option value="<?php echo $anesUsersId; ?>" <?php echo $anesSelected;?>><?php echo stripslashes($anesLname.', '.$anesFname.' '.$anesMname); ?></option>
                                            <?php
                                            }
                                        }
                                        ?>
                                        <option value="0" <?php if($anes_id == '0' && $anes_NA=='Yes') {echo "selected";}?>>N/A</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                     </div>
                    <div class="clearfix visible-sm"></div> 
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="conf_nurse_id"> 
                                       Checked-in By
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <select name="conf_nurse_id" id="conf_nurse_id" class="selectpicker required">
                                        <option value="">Select Any</option>
                                        <?php
                                        $getStaffNurseQry = imw_query("SELECT * FROM users
                                                                            WHERE user_type IN('Nurse','Staff','Coordinator')
                                                                            ORDER BY `lname`");
                                        while($getStaffNurseRows = imw_fetch_assoc($getStaffNurseQry)){
                                            $passChangedLatDate	=	$getStaffNurseRows['passCreatedOn'];
											$userStatus = $objManageData->getUserStatus($passChangedLatDate,$maxDaysToExpire);
											
											$staffNurseId = $getStaffNurseRows['usersId'];
                                            $staffNurseFname = $getStaffNurseRows['fname'];
                                            $staffNurseMname = $getStaffNurseRows['mname'];
                                            $staffNurseLname = $getStaffNurseRows['lname'];
                                            if($staffNurseMname) {
                                                $staffNurseMname = ' '.$staffNurseMname;
                                            }
                                            $staffNurseName = $staffNurseFname.$staffNurseMname.' '.$staffNurseLname;
                                            
                                            $staffNurse_deleteStatus = $getStaffNurseRows['deleteStatus'];
                                            if($userStatus=="Expired") {
                                            	//DO NOT SHOW EXPIRED USERS
											}else if($staffNurse_deleteStatus=="Yes") {
                                            	//DO NOT SHOW DELETED USERS
											}else{
                                            
                                                $selChk="";
                                                if($reConfirmId) {  //IF PATIENT IS RECONFIRMING THEN MATCH 'NURSE ID' WITH EXISTING 'NURSE ID' 
                                                    if($confNurse_id == $staffNurseId) {
                                                        $selChk = "selected";
                                                    }
                                                }else {  //IF PATIENT IS CONFIRMING FROM IMW THEN MATCH 'NURSE NAME' WITH 'NURSE NAME' FROM IMW(i.e stub_tbl) 
                                                    if($patient_conf_nurse == $staffNurseName) {
                                                        $selChk = "selected";
                                                    }else if($getStaffNurseRows['usersId']==$loginUserId) { //BY DEFAULT LOGGED IN NURSE OR STAFF WILL BE SELECTED 
                                                        $selChk = "selected";
                                                    }
                                                }	
                                            
                                            ?>
                                                <option value="<?php echo $staffNurseId; ?>" <?php echo $selChk;?>><?php echo stripslashes($staffNurseLname.', '.$staffNurseFname.' '.$staffNurseMname); ?></option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                     
                     <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="language"> 
                                       Language
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <?php
                                        $arrLanguage = array('English','Spanish','French','German','Russian','Japanese','Portuguese','Italian');
                                        sort($arrLanguage);
                                        $arrLanguage[] = 'Declined to Specify';
                                        $arrLanguage[] = 'Other';
                                        $other_language=substr($patient_language,0,5);
                                        if($other_language=='Other'){
                                            $other_language_val=substr($patient_language,9);
                                        }
    
                                    ?>                                                                        
                                    <select name='language' id="language" class="selectpicker"  onChange="showHippaOtherTxtBox(this.id,'otherLanguage','imgBackLanguage'); " >
                                        <option value="">Select Any</option>
                                        <?php																		
                                            foreach ($arrLanguage as $s) {																				
                                                echo "<option  value='".$s."'";
                                                if (strtolower($s) == strtolower($patient_language) || strtolower($s) == strtolower($other_language)){
                                                    echo " selected";
                                                    echo ">".ucfirst($s)."</option>\n";
                                                }
                                                else{
                                                    echo ">".ucfirst($s)."</option>\n";
                                                }																				
                                            }																				
                                        ?>
                                    </select>
                                    <input class="form-control" name="otherLanguage" id="otherLanguage"  value="<?php echo stripslashes($other_language_val); ?>" style="display:none; width:220px;"/> 
                                    <img src="images/back_arrow.png" id="imgBackLanguage"  style="display:none; margin-top:2px; border:0px;" onClick="showHippaComboBox('language','otherLanguage','imgBackLanguage');">
                                    <script type="text/javascript">
                                        showHippaOtherTxtBox('language','otherLanguage','imgBackLanguage');
                                    </script>
                                </div>
                            </div>
                        </div>
                     </div>
                    <div class="clearfix visible-sm"></div> 
                     <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="race">Race</label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<?php
                                        $arrRace = array(
                                        "American Indian or Alaska Native" => "American Indian or Alaska Native",
                                        "Asian" => "Asian",
                                        "Black or African American" => "Black or African American",
                                        "Native Hawaiian or Other Pacific Islander" => "Native Hawaiian or Other Pacific Islander",
                                        "Latin American" => "Latin American",
                                        "White" => "White",											
                                        "Declined to Specify" => "Declined to Specify");
                                    $arrTemp = array(trim($patient_race),"Other",trim($rowGetPatientData["ptOtherRace"]));
                                    ?>
                                    <select name='race[]' id="race" class="selectpicker" multiple="multiple">
                                        <option value="">Select Any</option>
                                        <?php 
                                            $dis_race="hidden";		
                                            $pt_race=explode(",",$patient_race);																
                                            foreach ($arrRace as $k=>$s) {	$dis_race="hidden";																			
                                                echo "<option value='".$s."'";
                                                if(in_array("Other",$pt_race)){$dis_race="visible";}
                                                if(in_array($s,$pt_race)){
                                                    echo " selected";
                                                    echo ">".ucfirst($k)."</option>\n";
                                                }
                                                else{
                                                    echo ">".ucfirst($k)."</option>\n";
                                                }																				
                                            }																				
                                        ?>
                                    </select>                                       
                                </div>
                            </div>
                        </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="ethnicity"> 
                                        Ethnicity
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<?php
                                        $arrEthnicity = array(
                                        "African Americans" => "African Americans",
                                        "American" => "American",
                                        "American Indians" => "American Indians",
                                        "Chinese" => "Chinese",
                                        "European Americans" => "European Americans",
                                        "Hispanic or Latino" => "Hispanic or Latino",
                                        "Jewish" => "Jewish",
                                        "Not Hispanic or Latino" => "Not Hispanic or Latino",
                                        "Unknown" => "Unknown",
                                        "Declined to Specify" => "Declined to Specify");																			
                                    $arrTemp = array(trim($patient_ethnicity),"Other",trim($rowGetPatientData["ptOtherEthnicity"]));
                                    $ptEthnicity=explode(",",$patient_ethnicity);
                                    ?>
                                    <select name='ethnicity[]' id="ethnicity" class="selectpicker" multiple="multiple">
                                        <option value="">Select Any</option>
                                        <?php
                                            $varBlock="none";																		
                                            foreach ($arrEthnicity as $k=>$s) {																				
                                                echo "<option value='".$s."'";
                                                if(in_array($s,$ptEthnicity)){
                                                    $varBlock="none";
                                                    if(in_array("Other",$ptEthnicity)){$varBlock="block";}
                                                    echo " selected";
                                                    echo ">".ucfirst($k)."</option>\n";
                                                }
                                                else{
                                                    echo ">".ucfirst($k)."</option>\n";
                                                }																				
                                            }																				
                                        ?>
                                    </select>                                      
                                </div>
                            </div>
                        </div>
                     </div>
					<?php if( $show_religion ) { ?>					
					 <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label class="text-left" for="religion"> 
                                        Religion
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<div class="col-xs-<?php echo $col_width;?>">
										<input type="text" id="religion" name="religion" placeholder="Religion" class="form-control capitalize" tabindex="1" value="<?php echo $patient_religion;?>" />	
									</div>
                                </div>
                            </div>
                        </div>
					 </div>
					<?php } ?>

					 
                   <!--  ModalS Forms Closed -->
                </div>
          </div>
          <div class="modal_multi-footer">
            <a href="javascript:void(0)" class="btn btn-primary" style="visibility:<?php echo $demographicSaveVisi;?>;" onClick="javascript:return validtion_pt_confirm();">  <b class="fa fa-save"></b>  Save changes  </a>
            <a href="javascript:void(0)" class="btn btn-default" data-dismiss="modal_multi" onClick="javascript:redirect_page('home.php');">   <b class="fa fa-close"></b>  Cancel  </a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
<!--END NEW DESIGN-->
</form>

<div id="myModal" class="modal fade in" style="top:20%"> <!--Common Alert Container-->
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header" style="padding:6px 12px;">
				<button style="color:#FFFFFF;opacity:0.9" ype="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="color:#FFFFFF;" class="modal-title">Patient Check In</h4>
			</div>
			<div class="modal-body" style="min-height:auto;">
				<p style="padding: 10px;" class="text-center"></p>
			</div>
			<div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
				<button id="cancel_yes" class="btn btn-primary hidden" onclick="location.href='home.php?cancelConfirm=true'">Yes</button>
				<button id="cancel_no" class="btn btn-danger hidden" data-dismiss="modal">No</button>
				<button style="margin-left:0;" id="missing_feilds" class="btn btn-primary hidden" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
<script>
	function secSiteUpdate()
	{
		var secSiteN	=	''
		var secSite		=	$('#sec_pos_op_site_id').val();
		var secProc		=	$("#sec_proc").val();
		
		if(secProc && !secSite)
		{
			secSiteN	=	$("#pos_op_site_id").val();
		}
		else if(secProc && secSite)
		{
			secSiteN	=	secSite
		}
		
		$('#sec_pos_op_site_id').val(secSiteN);
		$('#sec_pos_op_site_id').selectpicker('refresh');
	}
	function terSiteUpdate()
	{
		var terSiteN	=	''
		var terSite		=	$('#ter_pos_op_site_id').val();
		var terProc		=	$("#ter_proc").val();
		
		if(terProc && !terSite)
		{
			terSiteN	=	$("#pos_op_site_id").val();
		}
		else if(terProc && terSite)
		{
			terSiteN	=	terSite
		}
		
		$('#ter_pos_op_site_id').val(terSiteN);
		$('#ter_pos_op_site_id').selectpicker('refresh');
	}

</script>
</body>
</html>