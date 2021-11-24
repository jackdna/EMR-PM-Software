<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$current_form_version = 2;
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include_once("common/commonFunctions.php"); 
include_once("common/user_agent.php");	//Safari
$tablename = "laser_procedure_patient_table";
//include("common/linkfile.php");
$isHTML5OK = isHtml5OK();
?>
<!DOCTYPE html>
<html>
<head>
<title>Laser Procedure - Surgery Center EMR</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<script src="js/dragresize.js"></script>
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
	$spec = '</head>
	<body onLoad="top.changeColor(\''.$bgcolor_op_room_record.'\');" onClick="document.getElementById(\'divSaveAlert\').style.display = \'none\'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();">
	';
	include("common/link_new_file.php");
	//START INCLUDE PREDEFINE FUNCTIONS

	include_once("common/laserpredefine_sle_pop.php");
	include_once("common/laserpredefine_mental_state_pop.php");
	include_once("common/laserpredefine_fundus_exam_pop.php");
	include_once("common/pre_define_opmed.php"); //PRE OP  PHYSICIAN.
	include_once("common/pre_define_diagnosis.php"); 
	include_once("common/laserpredefine_spot_duration_pop.php");
	include_once("common/laserpredefine_spot_size_pop.php");
	include_once("common/laserpredefine_power_pop.php");
	include_once("common/laserpredefine_shots_pop.php");
	include_once("common/laserpredefine_total_energy_pop.php");
	include_once("common/laserspot_degree_of_opening_admin.php");
	include_once("common/laserpredefine_exposure_pop.php");
	include_once("common/laserpredefine_count_pop.php");
	//include_once("common/laserpredefine_anesthesia_pop.php");
	include_once("common/laserpredefine_post_progressnote_pop.php");
	include_once("common/pre_define_procedure_notes.php"); 
	include_once("common/laserpredefine_post_operative_status_pop.php");
//END INCLUDE PREDEFINE FUNCTIONS


extract($_GET);
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$patient_id = $_REQUEST["patient_id"];
$pConfId = $_REQUEST["pConfId"];
$cancelRecord = $_REQUEST['cancelRecord'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$preOpPhysicianOrdersId = $_REQUEST['preOpPhysicianOrdersId'];	


function unique_file_name($path){
	if(!file_exists($path)){
		return $path;		
	}else{
		$arrPathInfo = pathinfo($path);
		$dirname = $arrPathInfo['dirname'];
		$filename = $arrPathInfo['filename'];
		$basename = $arrPathInfo['basename'];
		$extension = $arrPathInfo['extension'];
		$str = random_string(8);
		$path = $dirname."/".$filename."_".$str.".".$extension;
		return unique_file_name($path);
	}
}
function random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}
function merge_images($arr = array(),$pConfId,$patient_id){
	
	$saveDBpath="";
	global $surgeryCenterDirectoryName;
	$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
	$drawingFolder = $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles/laser_drawing_images";
	if(!is_dir($drawingFolder)){		
		mkdir($drawingFolder, 0777);
	}
	if(empty($_REQUEST['hidDrawingTestImageP0']) == false){
		$baseImgPath 		= $rootServerPath.$_REQUEST['hidDrawingTestImageP0'];
		$arrPathInfo 		= pathinfo($baseImgPath);
		$baseFileNameTmp 	= $arrPathInfo['filename'];
		$unlink_path='';
		//$curDt = date('YmdHis');
		$curDt = $_REQUEST['hidd_curDt'];
		if(!$curDt) { 
			$curDt = date('Ymd_Hi');
		}
		if(stristr($baseFileNameTmp,$pConfId.'_'.$patient_id)) {
			$unlink_path = $baseImgPath;
			list($pth1) = explode('_'.$pConfId.'_'.$patient_id,$baseFileNameTmp);
			$baseFileName 		= $pth1.'_'.$pConfId.'_'.$patient_id.'_'.$curDt;
		}else {
			$baseFileName 		= $baseFileNameTmp.'_'.$pConfId.'_'.$patient_id.'_'.$curDt;
		}
		$baseFileExt 		= $arrPathInfo['extension'];
	}
	if(empty($baseImgPath)){
		$baseImgPath = dirname(__FILE__)."/drawing/images/white.png";
		$baseFileExt = "png";
	}
	if(empty($baseImgPath) == false){
		$baseImgResource = (strpos($baseImgPath,".png")!==false) ? imagecreatefrompng($baseImgPath): ( (strpos($baseImgPath,".jpg")!==false) ?imagecreatefromjpeg($baseImgPath):imagecreatefromgif($baseImgPath));
		list($baseW, $baseH) = getimagesize($baseImgPath);
		$inpData = $_REQUEST['hidCanvasImgData0'];
		$topImgData = str_replace("data:image/png;base64,","",$inpData);
		$topImgData =base64_decode($topImgData);
		$topImagePath = $drawingFolder."/".$baseFileName."_test.png";
		//$topImagePath = unique_file_name($topImagePath);
		$r = file_put_contents($topImagePath, $topImgData);
		$topImgResource = imagecreatefrompng($topImagePath);

		list($topW, $topH) = getimagesize($topImagePath);

		$mergeImgPath = $drawingFolder."/".$baseFileName.".".$baseFileExt;
		//$mergeImgPath = unique_file_name($mergeImgPath);
		$mergeImgReplace = "/".str_replace($rootServerPath,'',$mergeImgPath);
		imagesavealpha($topImgResource, true);
		imagealphablending($baseImgResource, true);
		imagecopy($baseImgResource, $topImgResource, 0, 0, 0, 0, $topW, $topH);
		switch($baseFileExt){
			case "png":
			imagepng($baseImgResource, $mergeImgPath);	
			break;
			case "jpg":
			imagejpeg($baseImgResource, $mergeImgPath);	
			break;
			case "gif":
			imagegif($baseImgResource, $mergeImgPath);	
			break;
		}
		$saveDBpath = str_ireplace($rootServerPath."/".$surgeryCenterDirectoryName."/admin/","",$mergeImgPath);
		@unlink($topImagePath);
		if($unlink_path) {
			@unlink($unlink_path);
		}
	}
	return $saveDBpath;
}

include("common/laserpredefine_chief_complaint_pop.php");

	$laserprocedureRecordpostedID=$_REQUEST['laserprocedureRecord_postup'];

//verified Nurse and surgeon
	$loginIdUser=$_SESSION["loginUserId"];
	$ViewUserNameVerifiedQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
	$ViewUserNameVerifiedRes = imw_query($ViewUserNameVerifiedQry) or die(imw_error()); 
	$ViewUserNameVerifiedRow = imw_fetch_array($ViewUserNameVerifiedRes); 

	$loggedInNurseVerifiedName = addslashes($ViewUserNameVerifiedRow["lname"].", ".$ViewUserNameVerifiedRow["fname"]." ".$ViewUserNameVerifiedRow["mname"]);
	$loggedInNurseVerifiedType = $ViewUserNameVerifiedRow["user_type"];
//end verified Nurse and surgeon

// GETTING CONFIRMATION DETAILS for surgeon sign
		$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
		$surgeonId = $detailConfirmation->surgeonId;
		$patient_primary_procedure_id = $detailConfirmation->patient_primary_procedure_id;
		$ascIdConfirm = $detailConfirmation->ascId;

	//START GET PATIENT DETAIL
		$imwPatientIdLaser = "";
		$laserPatientName_tblQry 	= "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_REQUEST['patient_id']."'";
		$laserPatientName_tblRes 	= imw_query($laserPatientName_tblQry) or die(imw_error());
		$laserPatientName_tblRow 	= imw_fetch_array($laserPatientName_tblRes);
		$imwPatientIdLaser 	   		= $laserPatientName_tblRow["imwPatientId"];
	//END GET PATIENT DETAIL
	
		//check whether procedure is laser procedure or not
		$str_procedure_category_laser = "SELECT * FROM procedures WHERE procedureId  = '".$patient_primary_procedure_id."'";
		$qry_procedure_category_laser = imw_query($str_procedure_category_laser);
		$fetchRows_procedure_category_laser = imw_fetch_array($qry_procedure_category_laser);
		$patient_laser_consent_categoryID = $fetchRows_procedure_category_laser['catId'];
		//check whether procedure is laser procedure or not

		//GET ASSIGNED SURGEON ID AND SURGEON NAME
			$preOpAssignedSurgeonId = $detailConfirmation->surgeonId;
			$preOpAssignedSurgeonName = stripslashes($detailConfirmation->surgeon_name);
		
		//END GET ASSIGNED SURGEON ID AND SURGEON NAME
	
			unset($conditionArr);
			$conditionArr['usersId'] = $surgeonId;
			$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
			if($surgeonsDetails){
				foreach($surgeonsDetails as $usersDetail)
				{
					$signatureOfSurgeon = $usersDetail->signature;
				}
			}	
/**************************************end for surgeon sign**************************************/
		//get site from header
			$laser_patientConfirmSiteTempSite = $detailConfirmation->site;
		// APPLYING NUMBERS TO PATIENT SITE
			$laser_patientConfirmSiteName='';
			if($laser_patientConfirmSiteTempSite == 1) {
				$laser_patientConfirmSiteTemp = "Left Eye";  //OD
				$laser_patientConfirmSiteName = "left eye";  //OD
			}else if($laser_patientConfirmSiteTempSite == 2) {
				$laser_patientConfirmSiteTemp = "Right Eye";  //OS
				$laser_patientConfirmSiteName = "right eye";  //OS
			}else if($laser_patientConfirmSiteTempSite == 3) {
				$laser_patientConfirmSiteTemp = "Both Eye";  //OU
				$laser_patientConfirmSiteName = "both eye";  //OU
			}else if($laser_patientConfirmSiteTempSite == 4) {
				$laser_patientConfirmSiteTemp = "Left Upper Lid";
				$laser_patientConfirmSiteName = "left upper lid";
			}else if($laser_patientConfirmSiteTempSite == 5) {
				$laser_patientConfirmSiteTemp = "Left Lower Lid";
				$laser_patientConfirmSiteName = "left lower lid";
			}else if($laser_patientConfirmSiteTempSite == 6) {
				$laser_patientConfirmSiteTemp = "Right Upper Lid";
				$laser_patientConfirmSiteName = "right upper lid";
			}else if($laser_patientConfirmSiteTempSite == 7) {
				$laser_patientConfirmSiteTemp = "Right Lower Lid";
				$laser_patientConfirmSiteName = "right lower lid";
			}else if($laser_patientConfirmSiteTempSite == 6) {
				$laser_patientConfirmSiteTemp = "Bilateral Upper Lid";
				$laser_patientConfirmSiteName = "bilateral upper lid";
			}else if($laser_patientConfirmSiteTempSite == 7) {
				$laser_patientConfirmSiteTemp = "Bilateral Lower Lid";
				$laser_patientConfirmSiteName = "bilateral lower lid";
			}else{
				$laser_patientConfirmSiteTemp = "Operative Eye";  //OU
			}
		// END APPLYING NUMBERS TO PATIENT SITE

		//END get site from header

//**************************************GET LOGGED IN USER TYPE
		unset($conditionArr);
		$conditionArr['usersId'] = $_SESSION["loginUserId"];
		$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
		if($surgeonsDetails){
			foreach($surgeonsDetails as $usersDetail)
			{
				$loggedUserType = $usersDetail->user_type;
			}
		}
/**************************************END GET LOGGED IN USER TYPE**************************************/
//first check

		$str_prelaserprocedure_templete_chk = "SELECT * FROM laser_procedure_patient_table WHERE  confirmation_id='$pConfId'";
		$qry_prelaserprocedure_templete_chk = imw_query($str_prelaserprocedure_templete_chk);
		$prelaserprocedure_templete_tblNumRow_chk = imw_num_rows($qry_prelaserprocedure_templete_chk);
		$fetchRows_preprocedurechk = imw_fetch_array($qry_prelaserprocedure_templete_chk);
		$laserprocedureRecordid=$fetchRows_preprocedurechk['laser_procedureRecordID'];
		$chk_form_status=$fetchRows_preprocedurechk['form_status'];


//SAVE Into TABLE PATIENT DETAIL
	if(!$cancelRecord){
			$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$laserpro_form = $getLeftLinkDetails->laser_procedure_form;
			if($laserpro_form=='true'){
				$formArrayRecord['laser_procedure_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
			}
	}elseif($cancelRecord){
				$fieldName = "laser_procedure_form";
				$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
				include("left_link_hide.php");
	}			
		$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&ascId='.$ascId.'&fieldName='.$fieldName;

	//MAKE AUDIT STATUS VIEW
	if($_POST['SaveRecordForm']!='yes'){
		unset($arrayRecord);
		$arrayRecord['user_id'] = $_SESSION['loginUserId'];
		$arrayRecord['patient_id'] = $patient_id;
		$arrayRecord['confirmation_id'] = $pConfId;
		$arrayRecord['form_name'] = 'laser_procedure_form';
		$arrayRecord['status'] = 'viewed';
		$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
		$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
	}
	//MAKE AUDIT STATUS VIEW

	if($_POST['SaveRecordForm']=='yes'){
		unset($arrayRecord);
		$medicationTime = $_REQUEST['medicationTime'];
		if(!$medicationTime){
			$medicationTime = time();
		}
		$str_sign = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='$pConfId'";
		$qry_sign = imw_query($str_sign);
		$res_sign = imw_fetch_array($qry_sign);
		$nurse_sign=$res_sign['signNurseId'];
		$surgeon_sign=$res_sign['signSurgeon1Id'];
		$chk_verified_surgeon_Id=$res_sign['verified_surgeon_Id'];
		$chk_verified_surgeon_Name=$res_sign['verified_surgeon_Name'];
		$chk_verified_surgeon_date=date('Y-m-d',strtotime($res_sign['verified_surgeon_timeout']));
		$chk_proc_start_time	=	$res_sign['proc_start_time'];
		$chk_proc_end_time		=	$res_sign['proc_end_time'];
		$chk_formStatus				=	$res_sign['form_status'];
		$chkLaserProcVersionNum = $res_sign['version_num'];
		$chkLaserProcVersionDate= $res_sign['version_date_time'];

		// check for form status
		if( (($_POST['txtarea_chief_complaint'] =='') && ($_POST['hiddchk_laser_chief_complaint']=='on') &&
			 ($_POST['txtarea_past_medicalhx'] == '' ) && ($_POST['hiddchk_laser_past_med_hx']) &&
			 ($_POST['txtarea_present_illness_hx'] == '') && ($_POST['hiddchk_laser_present_illness_hx']) &&
			 ($_POST['txtarea_medications'] == '') && ($_POST['hiddchk_laser_medication']) 
			)||
			($_REQUEST['hiddVerifiedNurseName']=='' || $chk_verified_surgeon_Name==''
			) ||
			
			(($_POST['txtarea_pre_op_Diagnosis']=='' && $_POST['hiddchk_laser_pre_op_diagnosis']=='on') 
			) ||
			(($_POST['txt_vitalSignBp_pre'] =='') &&
			 ($_POST['txt_vitalSignP_pre'] == '' ) &&
			 ($_POST['txt_vitalSignR_pre'] == '') &&
			 ($_POST['txtarea_spot_duration'] == '') &&
			 ($_POST["txtarea_spot_size"] == '') && 
			 ($_POST['txtarea_power'] == '' ) &&
			 ($_POST['txtarea_shots'] == '') &&
			 ($_POST['txtarea_total_energy'] == '') &&
			 ($_POST["txtarea_degree_of_opening"] == '') &&
			 ($_POST['txtarea_exposure'] == '' ) &&
			 ($_POST['txtarea_count'] == '') &&
			 ($_POST['txt_vitalSignBp_post'] == '') &&
			 ($_POST['txt_vitalSignP_post'] == '') &&
			 ($_POST['txt_vitalSignR_post'] == '') &&
			 ($_POST['txtarea_Post_ProgressNote'] == '') 
			) ||
			(($_POST["pre_laser_IOP_R"] == '') && 
			 ($_POST['pre_laser_IOP_L'] == '' ) &&
			 ($_POST['pre_iop_na'] == '' )
			)||
			(($_POST['iop_pressure_l'] == '') &&
			 ($_POST['iop_pressure_r'] == '') &&
			 ($_POST['iop_pressure_na'] == '')
			) ||
			($nurse_sign=="0") || ($surgeon_sign=="0")
		  )		
		{
				$form_status="not completed";
		}
		else
		{
				$form_status="completed";
		}
	//end check fr form status
	
	if($_POST['laserProcedure_image']=="0-0-0:;"){
		$_POST['laserProcedure_image']='';
	}
		$laserprocedureRecordpostedID=$_POST['laserprocedureRecord_postup'];
		$versionNumQry = "";
		$version_num	=	$chkLaserProcVersionNum;
		if(!$chkLaserProcVersionNum)
		{
			$version_date_time	=	$chkLaserProcVersionDate;
			if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
			{
				$version_date_time	=	date('Y-m-d H:i:s');
			}
					
			if($chk_formStatus == 'completed' || $chk_formStatus=='not completed'){
				$version_num 	= 1;
			}else{
				$version_num	=	$current_form_version;
			}
			$arrayRecord['version_num']			=	$version_num;
			$arrayRecord['version_date_time']	=	$version_date_time;
		}
		
		$arrayRecord['patient_id']=$patient_id;
		$arrayRecord['confirmation_id']=$pConfId;
		$arrayRecord['saveFromChart']	=	$_POST['saveFromChart'] ;
		
		$arrayRecord['allergies_status_reviewed']= $_POST["chbx_drug_react_reviewed"];
		
		$arrayRecord['chk_laser_chief_complaint']= $_POST['hiddchk_laser_chief_complaint'];
		$arrayRecord['laser_chief_complaint']=addslashes($_POST['txtarea_chief_complaint']);
		
		$arrayRecord['chk_laser_past_med_hx']= $_POST['hiddchk_laser_past_med_hx'];
		$arrayRecord['laser_past_med_hx']=addslashes($_POST['txtarea_past_medicalhx']);
		
		$arrayRecord['chk_laser_present_illness_hx']= $_POST['hiddchk_laser_present_illness_hx'];
		$arrayRecord['laser_present_illness_hx']=addslashes($_POST['txtarea_present_illness_hx']);
		
		$arrayRecord['chk_laser_medication']= $_POST['hiddchk_laser_medication'];
		$arrayRecord['laser_medication']=addslashes($_POST['txtarea_medications']);
		
		$arrayRecord['best_correction_vision_R']=addslashes($_POST['best_correction_vision_R']);
		$arrayRecord['best_correction_vision_L']=addslashes($_POST['best_correction_vision_L']);
		
		$arrayRecord['glare_acuity_R']=addslashes($_POST['glare_acuity_R']);
		$arrayRecord['glare_acuity_L']=addslashes($_POST['glare_acuity_L']);
		
		$arrayRecord['chk_laser_sle']= $_POST['hiddchk_laser_sle'];
		$arrayRecord['laser_sle']=addslashes($_POST['txtSLE']);
		
		$arrayRecord['chk_laser_mental_state']= $_POST['hiddchk_laser_mental_state'];
		$arrayRecord['laser_mental_state']=addslashes($_POST['txt_mental_state']);
		
		$arrayRecord['pre_laser_IOP_R']=addslashes($_POST['pre_laser_IOP_R']);
		$arrayRecord['pre_laser_IOP_L']=addslashes($_POST['pre_laser_IOP_L']);
		$arrayRecord['pre_iop_na']=addslashes($_POST['pre_iop_na']);

		$arrayRecord['chk_laser_fundus_exam']= $_POST['hiddchk_laser_fundus_exam'];
		$arrayRecord['laser_fundus_exam']=addslashes($_POST['txtarea_Fundus_Exam']);
		
		$arrayRecord['laser_comments']=addslashes($_POST['comments']);
		$arrayRecord['laser_other']=addslashes($_POST['txtarea_other']);
		
		$arrayRecord['chk_laser_pre_op_diagnosis']= $_POST['hiddchk_laser_pre_op_diagnosis'];
		$arrayRecord['pre_op_diagnosis']=addslashes($_POST['txtarea_pre_op_Diagnosis']);
		
		$arrayRecord['laser_other_pre_medication']=$_POST['otherPreOpOrders'];
		
		
		$arrayRecord['laser_procedure_notes']=addslashes($_POST['txtarea_laser_procedure_notes']);

		$arrayRecord['stable_chbx']=addslashes($_POST['stableChkBox']);
		$arrayRecord['stable_other_chbx']=addslashes($_POST['stableChkBoxOther']);
		$arrayRecord['stable_other_txtbx']="";
		if($_POST['stableChkBoxOther']=='Yes'){
			$arrayRecord['stable_other_txtbx']=addslashes($_POST['stableTxtBoxOther']);
		}	

		$arrayRecord['chk_laser_patient_evaluated']=addslashes($_POST['chk_laser_patient_evaluated']);
		$arrayRecord['prelaserVitalSignBP']=addslashes($_POST['txt_vitalSignBp_pre']);
		$arrayRecord['prelaserVitalSignP']=addslashes($_POST['txt_vitalSignP_pre']);
		$arrayRecord['prelaserVitalSignR']=addslashes($_POST['txt_vitalSignR_pre']);
		if($_REQUEST['txt_vitalSignTime_pre'])
		{
			//$arrayRecord['prelaserVitalSignTime'] = date('Y-m-d H:i:s',strtotime($_REQUEST['txt_vitalSignTime_pre']));
			$arrayRecord['prelaserVitalSignTime'] = date('Y-m-d').' '.$objManageData->setTmFormat($_REQUEST['txt_vitalSignTime_pre']);
		}
		
		$arrayRecord['chk_laser_spot_duration']= $_POST['hiddchk_laser_spot_duration'];
		$arrayRecord['laser_spot_duration']=addslashes($_POST['txtarea_spot_duration']);
		
		$arrayRecord['chk_laser_spot_size']= $_POST['hiddchk_laser_spot_size'];
		$arrayRecord['laser_spot_size']=addslashes($_POST['txtarea_spot_size']);
		
		$arrayRecord['chk_laser_power']= $_POST['hiddchk_laser_power'];
		$arrayRecord['laser_power']=addslashes($_POST['txtarea_power']);

		$arrayRecord['laser_os']=addslashes($_POST['laser_od']);
		$arrayRecord['laser_od']=addslashes($_POST['laser_os']);

		$arrayRecord['chk_laser_shots']= $_POST['hiddchk_laser_shots'];
		$arrayRecord['laser_shots']=addslashes($_POST['txtarea_shots']);

		$arrayRecord['chk_laser_total_energy']= $_POST['hiddchk_laser_total_energy'];
		$arrayRecord['laser_total_energy']=addslashes($_POST['txtarea_total_energy']);

		$arrayRecord['chk_laser_degree_of_opening']= $_POST['hiddchk_laser_degree_of_opening'];
		$arrayRecord['laser_degree_of_opening']=addslashes($_POST['txtarea_degree_of_opening']);

		$arrayRecord['chk_laser_exposure']= $_POST['hiddchk_laser_spot_exposure'];
		$arrayRecord['laser_exposure']=addslashes($_POST['txtarea_exposure']);

		$arrayRecord['chk_laser_count']= $_POST['hiddchk_laser_count'];
		$arrayRecord['laser_count']=addslashes($_POST['txtarea_count']);

		$arrayRecord['laser_anesthesia']=addslashes($_POST['txtarea_anesthesia']);
		
		$arrayRecord['postlaserVitalSignBP']=addslashes($_POST['txt_vitalSignBp_post']);
		$arrayRecord['postlaserVitalSignP']=addslashes($_POST['txt_vitalSignP_post']);
		$arrayRecord['postlaserVitalSignR']=addslashes($_POST['txt_vitalSignR_post']);
		if($_REQUEST['txt_vitalSignTime_post'])
		{
			//$arrayRecord['postlaserVitalSignTime'] = date('Y-m-d H:i:s',strtotime($_REQUEST['txt_vitalSignTime_post']));
			$arrayRecord['postlaserVitalSignTime'] = date('Y-m-d').' '.$objManageData->setTmFormat($_REQUEST['txt_vitalSignTime_post']);
		}
		$arrayRecord['iop_pressure_l']=addslashes($_POST['iop_pressure_l']);
		$arrayRecord['iop_pressure_r']=addslashes($_POST['iop_pressure_r']);
		$arrayRecord['iop_na']=addslashes($_POST['iop_pressure_na']);
		
		$arrayRecord['post_op_operative_comment']=addslashes($_POST['post_op_comments']);
		
		$arrayRecord['chk_laser_post_progress']= $_POST['hiddchk_laser_post_progress'];
		$arrayRecord['laser_post_progress']=addslashes($_POST['txtarea_Post_ProgressNote']);
		$arrayRecord['laser_medical_evaluation']=addslashes($_POST['laser_medical_evaluation']);
		
		
		$arrayRecord['chk_laser_post_operative']= $_POST['hiddchk_laser_post_operative'];
		$arrayRecord['laser_post_operative']=addslashes($_POST['txtarea_Post_Operative_Status']);
		
		$arrayRecord['medicationStartTime']=addslashes($_REQUEST['startTimeVal'][0]);
		
		$arrayRecord['laserprocedurePhysicianOrdersTime'] = $medicationTime;
		
		$arrayRecord['form_status']=$form_status;
		
		$arrayRecord['prefilMedicationStatus']=$_POST['hidd_prefilMedicationStatus'];
		
		$arrayRecord['surgeonSign'] = $_REQUEST['elem_signature1'];
		
		$arrayRecord['verified_nurse_Id'] = $_REQUEST['hiddVerifiedNurseId'];
		$arrayRecord['verified_nurse_name'] = addslashes($_REQUEST['hiddVerifiedNurseName']);
		if($_REQUEST['verified_nurse_timeout'])
		{
			//$arrayRecord['verified_nurse_timeout'] = date('Y-m-d H:i:s',strtotime($_REQUEST['verified_nurse_timeout']));
			$arrayRecord['verified_nurse_timeout'] = date('Y-m-d').' '.$objManageData->setTmFormat($_REQUEST['verified_nurse_timeout']);
		}
		
		$arrayRecord['asa_status'] = $_REQUEST['asa_status'];
		if($_REQUEST['proc_start_time'])
		{
			//$arrayRecord['proc_start_time'] =  date('Y-m-d H:i:s',strtotime($_REQUEST['proc_start_time']));
			$arrayRecord['proc_start_time'] = date('Y-m-d').' '.$objManageData->setTmFormat($_REQUEST['proc_start_time']);
		}
		if($_REQUEST['proc_end_time'])
		{
			//$arrayRecord['proc_end_time'] 	= date('Y-m-d H:i:s',strtotime($_REQUEST['proc_end_time']));
			$arrayRecord['proc_end_time'] = date('Y-m-d').' '.$objManageData->setTmFormat($_REQUEST['proc_end_time']);
		}
		
		// Check if surgery start time or surgery end time field values changed
		$start_time_status	=	'0';
		/*
		if(	($arrayRecord['proc_start_time'] && date('H:i:s', strtotime($arrayRecord['proc_start_time'])) <> date('H:i:s', strtotime($chk_proc_start_time)) )
			|| ($arrayRecord['proc_end_time'] && date('H:i:s', strtotime($arrayRecord['proc_end_time'])) <> date('H:i:s', strtotime($chk_proc_end_time)))
		)*/
		if(	($_REQUEST['proc_start_time'] && $objManageData->setTmFormat($_REQUEST['proc_start_time']) <> $objManageData->getTmFormat($chk_proc_start_time) )
			|| ($_REQUEST['proc_end_time'] && $objManageData->setTmFormat($_REQUEST['proc_end_time'])  <> $objManageData->getTmFormat($chk_proc_end_time) )
		)
		
		{
			$start_time_status	=	'1';			 
		}
		
		$arrayRecord['start_time_status'] = $start_time_status;
		
		$arrayRecord['chk_laser_procedure_image']= $_POST['hiddchk_laser_procedure_image'];
		if($_POST['hiddchk_laser_procedure_image']=='on') {
			if($isHTML5OK) {
				$arrImagesNew 								= 	merge_images($_REQUEST,$pConfId,$patient_id);
				$arrayRecord['laser_procedure_image_path']	= 	$arrImagesNew;		
			}else {
				$arrayRecord['laser_procedure_image']		= 	addslashes($_POST['laserProcedure_image']);	
			}
			
		}
		
		if( isset($_REQUEST['verified_surgeon_timeout'])) {
			$verified_surgeon_timeout = $objManageData->setTmFormat($_REQUEST['verified_surgeon_timeout']);
			//$verified_surgeon_timeout = date('Y-m-d H:i:s',strtotime($chk_verified_surgeon_date. ' '. $verified_surgeon_timeout));
			$verified_surgeon_timeout = date('Y-m-d',strtotime($chk_verified_surgeon_date)). ' '. $verified_surgeon_timeout;
			if( $_REQUEST['hiddVerifiedSurgeonId'] <= 0 ) {
				$verified_surgeon_timeout = '';	
			}
			if( $verified_surgeon_timeout) $arrayRecord['verified_surgeon_timeout'] = $verified_surgeon_timeout;
		}
		
		$arrayRecord['discharge_home'] = (int)$_REQUEST['discharge_home'];
		$arrayRecord['patients_relation'] = isset($_REQUEST['patients_relation']) ? addslashes($_REQUEST['patients_relation']) : '';
		$arrayRecord['patients_relation_other'] = isset($_REQUEST['patients_relation_other']) ? addslashes($_REQUEST['patients_relation_other']) : '';
		$arrayRecord['patient_transfer'] = (int)$_REQUEST['patient_transfer'];
		$arrayRecord['discharge_time'] = $objManageData->setTmFormat($_REQUEST['discharge_time']);
		$arrayRecord['sign_all_pre_op_order_status'] 	= '1';
		$arrayRecord['sign_all_post_op_order_status'] 	= '1';

		if($prelaserprocedure_templete_tblNumRow_chk == 0)
		{
			$laserprocedureRecordpostedID = $objManageData->addRecords($arrayRecord, 'laser_procedure_patient_table');
		}
		else
		{
			$objManageData->updateRecords($arrayRecord, 'laser_procedure_patient_table', 'laser_procedureRecordID', $laserprocedureRecordpostedID);
		}
		
		
		//MAKE AUDIT STATUS
		unset($arrayStatusRecord);
		$arrayStatusRecord['user_id'] = $_SESSION['loginUserId'];
		$arrayStatusRecord['patient_id'] = $patient_id;
		$arrayStatusRecord['confirmation_id'] = $pConfId;
		$arrayStatusRecord['form_name'] = 'laser_procedure_form';		
		$arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
		//MAKE AUDIT STATUS
		
		//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'laser_procedure_form';
		$conditionArr['status'] = 'created';
		$chkAuditStatus = $objManageData->getMultiChkArrayRecords('chartnotes_change_audit_tbl',$conditionArr);	
		if($chkAuditStatus) {
			//MAKE AUDIT STATUS MODIFIED
			$arrayStatusRecord['status'] = 'modified';
		}else {
			//MAKE AUDIT STATUS CREATED
			$arrayStatusRecord['status'] = 'created';
		}
		$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');												
		//CODE END TO SET AUDIT STATUS AFTER SAVE
		
		//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
			$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
			$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
		//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
			
		//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
			$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
			$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
		//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
		
		//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
			if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
				echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
			}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
				echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
			}
		//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)

		//START SEND LASER CHART TO IDOC OPERATIVE NOTE
		$iDocOpNoteSave = "";
		if(trim($ascIdConfirm) && $form_status=="completed" && $imwSwitchFile == "sync_imwemr.php" && $imwPatientIdLaser && $_REQUEST["pConfId"]) {
			$iDocOpNoteSave = "yes";
			include("laser_procedure_printpop.php");
		}
		//END SEND LASER CHART TO IDOC OPERATIVE NOTE
		
		//End Add or update full record
		
		//ALLERGIES
		 if($_POST['chbx_drug_react']=='Yes') {
				 imw_query("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfId'");
			 }
		//END ALLERGIES
		
		//PRE OP ORDERS
		$updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '".$_POST['chbx_drug_react']."' where patientConfirmationId = '$pConfId'";
		$updateNKDAstatusRes = imw_query($updateNKDAstatusQry);
		
		$insertId = $preOpPhysicianOrdersId;			
		$parientPreOpMediOrderId = $_REQUEST['parientPreOpMediOrderId'];
		$preOpPatientMediOrderArr = $_REQUEST['preOpMediOrderArr'];
		$preOpPatientStrengthArr = $_REQUEST['strengthArr'];
		$preOpPatientDirectionArr = $_REQUEST['directionArr'];
		$preOpPatienttimeArr = $_REQUEST['timemedsArr'];
		
		
		if($preOpPatientMediOrderArr) {
			foreach($preOpPatientMediOrderArr as $key => $medicationList){
				//CHECK MEDICATION ALREADY PREDEFINED OR NOT					
				unset($condArr);
				$condArr['medicationName'] = addslashes($medicationList);
				$condArr['strength'] = addslashes($preOpPatientStrengthArr[$key]);
				$condArr['directions'] = addslashes($preOpPatientDirectionArr[$key]);
				$getExistsOrNot = $objManageData->getMultiChkArrayRecords('preopmedicationorder', $condArr);
				if(count($getExistsOrNot)<=0){
					$objManageData->addRecords($condArr, 'preopmedicationorder');
				}		
		
			//CHECK MEDICATION ALREADY PREDEFINED OR NOT
				if(($medicationList=='') && ($parientPreOpMediOrderId[$key]!='')){
					$objManageData->delRecord('patientpreopmedication_tbl', 'patientPreOpMediId', $parientPreOpMediOrderId[$key]);
				}
				$tempr = $key+1;
				$preOpPatienttimeArr1 = $_REQUEST['starttimeExtra'.$tempr];
				
				if($medicationList!=''){
					unset($arrayRecord);
					$arrayRecord['preOpPhyOrderId'] = $insertId;
					$arrayRecord['patient_confirmation_id'] = $pConfId;
					$arrayRecord['medicationName'] = addslashes($medicationList);
					$arrayRecord['strength'] 	= addslashes($preOpPatientStrengthArr[$key]);
					$arrayRecord['direction'] 	= addslashes($preOpPatientDirectionArr[$key]);
					$arrayRecord['timemeds'] 	= $preOpPatienttimeArr[$key];
					
					$arrayRecord['timemeds1'] = $preOpPatienttimeArr1[0];
					$arrayRecord['timemeds2'] = $preOpPatienttimeArr1[1];
					$arrayRecord['timemeds3'] = $preOpPatienttimeArr1[2];
					$arrayRecord['timemeds4'] = $preOpPatienttimeArr1[3];
					$arrayRecord['timemeds5'] = $preOpPatienttimeArr1[4];
					$arrayRecord['timemeds6'] = $preOpPatienttimeArr1[5];
					$arrayRecord['timemeds7'] = $preOpPatienttimeArr1[6];
					$arrayRecord['timemeds8'] = $preOpPatienttimeArr1[7];
					$arrayRecord['timemeds9'] = $preOpPatienttimeArr1[8];
					if(!$parientPreOpMediOrderId[$key]){
						$objManageData->addRecords($arrayRecord, 'patientpreopmedication_tbl');
					}else{
						$objManageData->updateRecords($arrayRecord, 'patientpreopmedication_tbl', 'patientPreOpMediId',$parientPreOpMediOrderId[$key]);
						
					}
				}
			}
		}//SAVE Into TABLE PATIENT DETAIL END
	  
	}

//START CODE TO CHECK NURSE,SURGEON SIGN IN DATABASE(169)
	$chkNurseSignDetails = $objManageData->getRowRecord('laser_procedure_patient_table', 'confirmation_id', $pConfId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat");
	if($chkNurseSignDetails) {
		$signNurseId = $chkNurseSignDetails->signNurseId;
		$signSurgeon1Id = $chkNurseSignDetails->signSurgeon1Id;
		$verified_surgeonName = $chkNurseSignDetails->verified_surgeon_Name;
		$chk_laser_patient_evaluated = $chkNurseSignDetails->chk_laser_patient_evaluated;
		
		//CHECK FORM STATUS
			$chk_form_status = $chkNurseSignDetails->form_status;
		//CHECK FORM STATUS
	
		$signSurgeon1Id =$chkNurseSignDetails->signSurgeon1Id;
		$signSurgeon1DateTime	= $chkNurseSignDetails->signSurgeon1DateTime;
		$signSurgeon1DateTimeFormat	= $chkNurseSignDetails->signSurgeon1DateTimeFormat;
		$signSurgeon1FirstName =$chkNurseSignDetails->signSurgeon1FirstName;
		$signSurgeon1MiddleName =$chkNurseSignDetails->signSurgeon1MiddleName;
		$signSurgeon1LastName =$chkNurseSignDetails->signSurgeon1LastName;
		$signSurgeon1Status =$chkNurseSignDetails->signSurgeon1Status;
		
		$signNurseId =$chkNurseSignDetails->signNurseId;
		$signNurseDateTime =$chkNurseSignDetails->signNurseDateTime;
		$signNurseDateTimeFormat =$chkNurseSignDetails->signNurseDateTimeFormat;
		$signNurseFirstName =$chkNurseSignDetails->signNurseFirstName;
		$signNurseMiddleName =$chkNurseSignDetails->signNurseMiddleName;
		$signNurseLastName =$chkNurseSignDetails->signNurseLastName;
		$signNurseStatus =$chkNurseSignDetails->signNurseStatus;
	
	}
//END CODE TO CHECK NURSE SIGN IN DATABASE
		$str_prelaserprocedure_templete = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='$pConfId' ";
		
		$qry_prelaserprocedure_templete = imw_query($str_prelaserprocedure_templete);
		$prelaserprocedure_templete_tblNumRow = imw_num_rows($qry_prelaserprocedure_templete);
		$fetchRows_preprocedure1 = imw_fetch_array($qry_prelaserprocedure_templete);
		$laserprocedurePatientRecordid=$fetchRows_preprocedure1['patient_id'];
		$laserprocedureSaveFromChart=$fetchRows_preprocedure1['saveFromChart'];
		$prefilMedicationStatus = $fetchRows_preprocedure1['prefilMedicationStatus']	;
		
	if( $laserprocedureSaveFromChart == 1 || $laserprocedurePatientRecordid == 0 ){  
		// GETTING CONFIRMATION DETAILS
		$detailConfirmation_procedure = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
		$laserprocedure_Id = $detailConfirmation_procedure->patient_primary_procedure_id;
		
		$detailConfirmation_procedure->surgeonId = $detailConfirmation_procedure->surgeonId ? $detailConfirmation_procedure->surgeonId : 0;
		// GETTING laser procedure templete detail
		$str_procedure_templete = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$laserprocedure_Id."' and laser_surgeonID = '".$detailConfirmation_procedure->surgeonId."' Order By laser_templateID Desc ";
		
		$qry_procedure_templete = imw_query($str_procedure_templete);
		$procedure_templete_tblNumRow = imw_num_rows($qry_procedure_templete);
		if( $procedure_templete_tblNumRow == 0 ) {
			$str_procedure_templete = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$laserprocedure_Id."' and FIND_IN_SET(".$detailConfirmation_procedure->surgeonId.",laser_surgeonID) Order By laser_templateID Desc ";
		
			$qry_procedure_templete = imw_query($str_procedure_templete);
			$procedure_templete_tblNumRow = imw_num_rows($qry_procedure_templete);
			
			if($procedure_templete_tblNumRow == 0 ) {
				$str_procedure_templete = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$laserprocedure_Id."' and laser_surgeonID = 'all' Order By laser_templateID Desc ";

				$qry_procedure_templete = imw_query($str_procedure_templete);
				$procedure_templete_tblNumRow = imw_num_rows($qry_procedure_templete);
			}
		}
		while($fetchRows_procedure = imw_fetch_array($qry_procedure_templete)){
			$procedure_surgeonId = $detailConfirmation_procedure->surgeonId;
			$surgeon_select_explode=$fetchRows_procedure['laser_surgeonID'];

			if($surgeon_select_explode!="all"){
				$surgeon_select=explode(",",$surgeon_select_explode);
				$count_surgeon= count($surgeon_select);
				
				if($count_surgeon==1){ 
					if($procedure_surgeonId==$surgeon_select_explode){
						
						$laser_chk_chief_complaint=$fetchRows_procedure['laser_chk_chief_complaint'];
						$laser_chief_complaint_detail=stripslashes($fetchRows_procedure['laser_chief_complaint']);
						
						$laser_chk_present_illness_hx=$fetchRows_procedure['laser_chk_present_illness_hx'];
						$laser_present_illness_hx_detail=stripslashes($fetchRows_procedure['laser_present_illness_hx']);
						
						$laser_chk_past_med_hx=$fetchRows_procedure['laser_chk_past_med_hx'];
						$laser_past_med_hx_detail=stripslashes($fetchRows_procedure['laser_past_med_hx']);
						
						$laser_chk_medication=$fetchRows_procedure['laser_chk_medication'];
						$laser_medication_detail=stripslashes($fetchRows_procedure['laser_medication']);							
						
						$laser_chk_sle=$fetchRows_procedure['laser_chk_sle'];
						$laser_sle_detail=stripslashes($fetchRows_procedure['laser_sle']);
						
						$allergies_status_reviewed=$fetchRows_procedure['chbx_drug_react_reviewed'];
						
						$laser_chk_fundus_exam=$fetchRows_procedure['laser_chk_fundus_exam'];
						$laser_fundus_exam_detail=stripslashes($fetchRows_procedure['laser_fundus_exam']);
						
						$laser_chk_mental_state=$fetchRows_procedure['laser_chk_mental_state'];
						$laser_mental_state_detail=stripslashes($fetchRows_procedure['laser_mental_state']);
						
						$laser_chk_pre_op_diagnosis=$fetchRows_procedure['laser_chk_pre_op_diagnosis'];
						$laser_pre_op_diagnosis=stripslashes($fetchRows_procedure['laser_pre_op_diagnosis']);
						
						$laser_chk_spot_duration=$fetchRows_procedure['laser_chk_spot_duration'];
						$laser_spot_duration_detail=stripslashes($fetchRows_procedure['laser_spot_duration']);
						
						$laser_chk_spot_size=$fetchRows_procedure['laser_chk_spot_size'];
						$laser_spot_size_detail=stripslashes($fetchRows_procedure['laser_spot_size']);
						
						$laser_chk_power=$fetchRows_procedure['laser_chk_power'];
						$laser_power_detail=stripslashes($fetchRows_procedure['laser_power']);
						
						$laser_chk_shots=$fetchRows_procedure['laser_chk_shots'];
						$laser_shots_detail=stripslashes($fetchRows_procedure['laser_shots']);
						
						$laser_chk_total_energy=$fetchRows_procedure['laser_chk_total_energy'];
						$laser_total_energy_detail=stripslashes($fetchRows_procedure['laser_total_energy']);
						
						$laser_chk_degree_of_opening=$fetchRows_procedure['laser_chk_degree_of_opening'];
						$laser_degree_of_opening_detail=stripslashes($fetchRows_procedure['laser_degree_of_opening']);
						
						$laser_chk_exposure=$fetchRows_procedure['laser_chk_exposure'];
						$laser_exposure_detail=stripslashes($fetchRows_procedure['laser_exposure']);
						
						$laser_chk_count=$fetchRows_procedure['laser_chk_count'];
						$laser_count_detail=stripslashes($fetchRows_procedure['laser_count']);
						
						$laser_chk_post_progress=$fetchRows_procedure['laser_chk_post_progress'];
						$laser_post_progress_detail=stripslashes($fetchRows_procedure['laser_post_progress']);
						
						$laser_chk_post_operative=$fetchRows_procedure['laser_chk_post_operative'];
						$laser_post_operative_detail=stripslashes($fetchRows_procedure['laser_post_operative']);
						
						$laser_preop_medication	=	$fetchRows_procedure['laser_preop_medication'];
						
						$laser_chk_procedure_image=$fetchRows_procedure['laser_chk_procedure_image'];
						$laser_procedure_image = $fetchRows_procedure['laser_procedure_image'];
						
						break;
					}
				}
				$matchedSurgeon=false;
				if($count_surgeon>1)
				{
					for($i=0;$i<$count_surgeon;$i++)
					{
						$match_surgeonid=$procedure_surgeonId;
						$surgeon=$surgeon_select[$i];
						if($surgeon==$match_surgeonid)
						{
						$matchedSurgeon=true;
						$laser_chk_chief_complaint=$fetchRows_procedure['laser_chk_chief_complaint'];
						$laser_chief_complaint_detail=stripslashes($fetchRows_procedure['laser_chief_complaint']);
						
						$laser_chk_present_illness_hx=$fetchRows_procedure['laser_chk_present_illness_hx'];
						$laser_present_illness_hx_detail=stripslashes($fetchRows_procedure['laser_present_illness_hx']);
						
						$laser_chk_past_med_hx=$fetchRows_procedure['laser_chk_past_med_hx'];
						$laser_past_med_hx_detail=stripslashes($fetchRows_procedure['laser_past_med_hx']);
						
						$laser_chk_medication=$fetchRows_procedure['laser_chk_medication'];
						$laser_medication_detail=stripslashes($fetchRows_procedure['laser_medication']);							
						
						$laser_chk_sle=$fetchRows_procedure['laser_chk_sle'];
						$laser_sle_detail=stripslashes($fetchRows_procedure['laser_sle']);
						
						$allergies_status_reviewed=$fetchRows_procedure['chbx_drug_react_reviewed'];
						
						$laser_chk_fundus_exam=$fetchRows_procedure['laser_chk_fundus_exam'];
						$laser_fundus_exam_detail=stripslashes($fetchRows_procedure['laser_fundus_exam']);
						
						$laser_chk_mental_state=$fetchRows_procedure['laser_chk_mental_state'];
						$laser_mental_state_detail=stripslashes($fetchRows_procedure['laser_mental_state']);
						
						$laser_chk_pre_op_diagnosis=$fetchRows_procedure['laser_chk_pre_op_diagnosis'];
						$laser_pre_op_diagnosis=stripslashes($fetchRows_procedure['laser_pre_op_diagnosis']);
						
						$laser_chk_spot_duration=$fetchRows_procedure['laser_chk_spot_duration'];
						$laser_spot_duration_detail=stripslashes($fetchRows_procedure['laser_spot_duration']);
						
						$laser_chk_spot_size=$fetchRows_procedure['laser_chk_spot_size'];
						$laser_spot_size_detail=stripslashes($fetchRows_procedure['laser_spot_size']);
						
						$laser_chk_power=$fetchRows_procedure['laser_chk_power'];
						$laser_power_detail=stripslashes($fetchRows_procedure['laser_power']);
						
						$laser_chk_shots=$fetchRows_procedure['laser_chk_shots'];
						$laser_shots_detail=stripslashes($fetchRows_procedure['laser_shots']);
						
						$laser_chk_total_energy=$fetchRows_procedure['laser_chk_total_energy'];
						$laser_total_energy_detail=stripslashes($fetchRows_procedure['laser_total_energy']);
						
						$laser_chk_degree_of_opening=$fetchRows_procedure['laser_chk_degree_of_opening'];
						$laser_degree_of_opening_detail=stripslashes($fetchRows_procedure['laser_degree_of_opening']);
						
						$laser_chk_exposure=$fetchRows_procedure['laser_chk_exposure'];
						$laser_exposure_detail=stripslashes($fetchRows_procedure['laser_exposure']);
						
						$laser_chk_count=$fetchRows_procedure['laser_chk_count'];
						$laser_count_detail=stripslashes($fetchRows_procedure['laser_count']);
						
						$laser_chk_post_progress=$fetchRows_procedure['laser_chk_post_progress'];
						$laser_post_progress_detail=stripslashes($fetchRows_procedure['laser_post_progress']);
						
						$laser_chk_post_operative=$fetchRows_procedure['laser_chk_post_operative'];
						$laser_post_operative_detail=stripslashes($fetchRows_procedure['laser_post_operative']);
						
						$laser_preop_medication	=	$fetchRows_procedure['laser_preop_medication'];
						
						$laser_chk_procedure_image=$fetchRows_procedure['laser_chk_procedure_image'];
						$laser_procedure_image = $fetchRows_procedure['laser_procedure_image'];
						
						}
					}
				}
				if($matchedSurgeon==true) {
					break;
				}
			}
			else
			{ 
				$laser_chk_chief_complaint=$fetchRows_procedure['laser_chk_chief_complaint'];
				$laser_chief_complaint_detail=stripslashes($fetchRows_procedure['laser_chief_complaint']);
				
				$laser_chk_present_illness_hx=$fetchRows_procedure['laser_chk_present_illness_hx'];
				$laser_present_illness_hx_detail=stripslashes($fetchRows_procedure['laser_present_illness_hx']);
				
				$laser_chk_past_med_hx=$fetchRows_procedure['laser_chk_past_med_hx'];
				$laser_past_med_hx_detail=stripslashes($fetchRows_procedure['laser_past_med_hx']);
				
				$laser_chk_medication=$fetchRows_procedure['laser_chk_medication'];
				$laser_medication_detail=stripslashes($fetchRows_procedure['laser_medication']);							
				
				$laser_chk_sle=$fetchRows_procedure['laser_chk_sle'];
				$laser_sle_detail=stripslashes($fetchRows_procedure['laser_sle']);
				
				$allergies_status_reviewed=$fetchRows_procedure['chbx_drug_react_reviewed'];
				
				$laser_chk_fundus_exam=$fetchRows_procedure['laser_chk_fundus_exam'];
				$laser_fundus_exam_detail=stripslashes($fetchRows_procedure['laser_fundus_exam']);
				
				$laser_chk_mental_state=$fetchRows_procedure['laser_chk_mental_state'];
				$laser_mental_state_detail=stripslashes($fetchRows_procedure['laser_mental_state']);
				
				$laser_chk_pre_op_diagnosis=$fetchRows_procedure['laser_chk_pre_op_diagnosis'];
				$laser_pre_op_diagnosis=stripslashes($fetchRows_procedure['laser_pre_op_diagnosis']);
				
				$laser_chk_spot_duration=$fetchRows_procedure['laser_chk_spot_duration'];
				$laser_spot_duration_detail=stripslashes($fetchRows_procedure['laser_spot_duration']);
				
				$laser_chk_spot_size=$fetchRows_procedure['laser_chk_spot_size'];
				$laser_spot_size_detail=stripslashes($fetchRows_procedure['laser_spot_size']);
				
				$laser_chk_power=$fetchRows_procedure['laser_chk_power'];
				$laser_power_detail=stripslashes($fetchRows_procedure['laser_power']);
				
				$laser_chk_shots=$fetchRows_procedure['laser_chk_shots'];
				$laser_shots_detail=stripslashes($fetchRows_procedure['laser_shots']);
				
				$laser_chk_total_energy=$fetchRows_procedure['laser_chk_total_energy'];
				$laser_total_energy_detail=stripslashes($fetchRows_procedure['laser_total_energy']);
				
				$laser_chk_degree_of_opening=$fetchRows_procedure['laser_chk_degree_of_opening'];
				$laser_degree_of_opening_detail=stripslashes($fetchRows_procedure['laser_degree_of_opening']);
				
				$laser_chk_exposure=$fetchRows_procedure['laser_chk_exposure'];
				$laser_exposure_detail=stripslashes($fetchRows_procedure['laser_exposure']);
				
				$laser_chk_count=$fetchRows_procedure['laser_chk_count'];
				$laser_count_detail=stripslashes($fetchRows_procedure['laser_count']);
				
				$laser_chk_post_progress=$fetchRows_procedure['laser_chk_post_progress'];
				$laser_post_progress_detail=stripslashes($fetchRows_procedure['laser_post_progress']);
				
				$laser_chk_post_operative=$fetchRows_procedure['laser_chk_post_operative'];
				$laser_post_operative_detail=stripslashes($fetchRows_procedure['laser_post_operative']);
				
				$laser_preop_medication	=	$fetchRows_procedure['laser_preop_medication'];
						
				$laser_chk_procedure_image=$fetchRows_procedure['laser_chk_procedure_image'];
				$laser_procedure_image = $fetchRows_procedure['laser_procedure_image'];
				
				
			}
		}
	}
//End prefill for if the patient chart note if NOT SAVED and is to be saved
	else{
		$str_prelaserprocedure_templete11 = "SELECT *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat FROM laser_procedure_patient_table WHERE patient_id  = '$patient_id' AND confirmation_id='$pConfId'";
		
		$qry_prelaserprocedure_templete11 = imw_query($str_prelaserprocedure_templete11);
		$prelaserprocedure_templete_tblNumRow11 = imw_num_rows($qry_prelaserprocedure_templete11);
		$fetchRows_preprocedure = imw_fetch_array($qry_prelaserprocedure_templete11);

		$laserprocedureRecordpostedID=$fetchRows_preprocedure['laser_procedureRecordID'];
		$form_status =	$fetchRows_preprocedure['form_status'];
		$laser_chk_chief_complaint=$fetchRows_preprocedure['chk_laser_chief_complaint'];
		$laser_chief_complaint_detail=stripslashes($fetchRows_preprocedure['laser_chief_complaint']);
	
		$laser_chk_present_illness_hx=$fetchRows_preprocedure['chk_laser_present_illness_hx'];
		$laser_present_illness_hx_detail=stripslashes($fetchRows_preprocedure['laser_present_illness_hx']);
		
		$laser_chk_past_med_hx=$fetchRows_preprocedure['chk_laser_past_med_hx'];
		$laser_past_med_hx_detail=stripslashes($fetchRows_preprocedure['laser_past_med_hx']);
		
		$laser_chk_medication=$fetchRows_preprocedure['chk_laser_medication'];
		$laser_medication_detail=stripslashes($fetchRows_preprocedure['laser_medication']);							
		
		$laser_chk_sle=$fetchRows_preprocedure['chk_laser_sle'];
		$laser_sle_detail=stripslashes($fetchRows_preprocedure['laser_sle']);
		
		$laser_other_detail=stripslashes($fetchRows_preprocedure['laser_other']);
		
		$allergies_status_reviewed=$fetchRows_preprocedure['allergies_status_reviewed'];
		
		$laser_chk_fundus_exam=$fetchRows_preprocedure['chk_laser_fundus_exam'];
		$laser_fundus_exam_detail=stripslashes($fetchRows_preprocedure['laser_fundus_exam']);
		
		$laser_chk_mental_state=$fetchRows_preprocedure['chk_laser_mental_state'];
		$laser_mental_state_detail=stripslashes($fetchRows_preprocedure['laser_mental_state']);
		
		$laser_chk_pre_op_diagnosis=$fetchRows_preprocedure['chk_laser_pre_op_diagnosis'];
		$laser_pre_op_diagnosis=stripslashes($fetchRows_preprocedure['pre_op_diagnosis']);//
		
		$laser_chk_spot_duration=$fetchRows_preprocedure['chk_laser_spot_duration'];
		$laser_spot_duration_detail=stripslashes($fetchRows_preprocedure['laser_spot_duration']);
		
		$laser_chk_spot_size=$fetchRows_preprocedure['chk_laser_spot_size'];
		$laser_spot_size_detail=stripslashes($fetchRows_preprocedure['laser_spot_size']);
		
		$laser_chk_power=$fetchRows_preprocedure['chk_laser_power'];
		$laser_power_detail=stripslashes($fetchRows_preprocedure['laser_power']);
		
		$laser_chk_shots=$fetchRows_preprocedure['chk_laser_shots'];
		$laser_shots_detail=stripslashes($fetchRows_preprocedure['laser_shots']);
		
		$laser_chk_total_energy=$fetchRows_preprocedure['chk_laser_total_energy'];
		$laser_total_energy_detail=stripslashes($fetchRows_preprocedure['laser_total_energy']);
		
		$laser_chk_degree_of_opening=$fetchRows_preprocedure['chk_laser_degree_of_opening'];
		$laser_degree_of_opening_detail=stripslashes($fetchRows_preprocedure['laser_degree_of_opening']);
		
		$laser_chk_exposure=$fetchRows_preprocedure['chk_laser_exposure'];
		$laser_exposure_detail=stripslashes($fetchRows_preprocedure['laser_exposure']);

		$laser_chk_count=$fetchRows_preprocedure['chk_laser_count'];
		$laser_count_detail=stripslashes($fetchRows_preprocedure['laser_count']);

		$laser_chk_post_progress=$fetchRows_preprocedure['chk_laser_post_progress'];
		$laser_post_progress_detail=stripslashes($fetchRows_preprocedure['laser_post_progress']);
		$laser_medical_evaluation=stripslashes($fetchRows_preprocedure['laser_medical_evaluation']);							
		
		$laser_chk_post_operative=$fetchRows_preprocedure['chk_laser_post_operative'];
		$laser_post_operative_detail=stripslashes($fetchRows_preprocedure['laser_post_operative']);
		
		$laser_preop_medication	=	$fetchRows_preprocedure['laser_preop_medication'];
		
		$best_correction_vision_R	=	$fetchRows_preprocedure['best_correction_vision_R'];
		$best_correction_vision_L	=	$fetchRows_preprocedure['best_correction_vision_L'];

		$glare_acuity_R=$fetchRows_preprocedure['glare_acuity_R'];
		$glare_acuity_L=$fetchRows_preprocedure['glare_acuity_L'];
		
		$pre_laser_IOP_R=$fetchRows_preprocedure['pre_laser_IOP_R'];
		$pre_laser_IOP_L=$fetchRows_preprocedure['pre_laser_IOP_L'];
		$pre_iop_na=$fetchRows_preprocedure['pre_iop_na'];
		
		$laser_comments=stripslashes($fetchRows_preprocedure['laser_comments']);
		$laser_other_pre_medication=stripslashes($fetchRows_preprocedure['laser_other_pre_medication']);
		
		
		
		$laser_procedure_notes=stripslashes($fetchRows_preprocedure['laser_procedure_notes']);
		$stable_chbx=stripslashes($fetchRows_preprocedure['stable_chbx']);
		$stable_other_chbx=stripslashes($fetchRows_preprocedure['stable_other_chbx']);
		$stable_other_txtbx=stripslashes($fetchRows_preprocedure['stable_other_txtbx']);
		$chk_laser_patient_evaluated=stripslashes($fetchRows_preprocedure['chk_laser_patient_evaluated']);
		$prelaserVitalSignBP=stripslashes($fetchRows_preprocedure['prelaserVitalSignBP']);
		$prelaserVitalSignP=stripslashes($fetchRows_preprocedure['prelaserVitalSignP']);
		$prelaserVitalSignR=stripslashes($fetchRows_preprocedure['prelaserVitalSignR']);
		
		$postlaserVitalSignBP=stripslashes($fetchRows_preprocedure['postlaserVitalSignBP']);
		$postlaserVitalSignP=stripslashes($fetchRows_preprocedure['postlaserVitalSignP']);
		$postlaserVitalSignR=stripslashes($fetchRows_preprocedure['postlaserVitalSignR']);
		
		
		$iop_pressure_l=stripslashes($fetchRows_preprocedure['iop_pressure_l']);//
		$iop_pressure_r=stripslashes($fetchRows_preprocedure['iop_pressure_r']);//
		$iop_na=stripslashes($fetchRows_preprocedure['iop_na']);//
		
		$post_comment=stripslashes($fetchRows_preprocedure['post_op_operative_comment']);//
		
		$laser_os=stripslashes($fetchRows_preprocedure['laser_os']);
		$laser_od=stripslashes($fetchRows_preprocedure['laser_od']);
		
		$prefilMedicationStatus=$fetchRows_preprocedure['prefilMedicationStatus'];
		
		$signSurgeon1Id =$fetchRows_preprocedure['signSurgeon1Id'];
		$signSurgeon1DateTime =$fetchRows_preprocedure['signSurgeon1DateTime'];
		$signSurgeon1DateTimeFormat =$fetchRows_preprocedure['signSurgeon1DateTimeFormat'];
		$signSurgeon1FirstName =$fetchRows_preprocedure['signSurgeon1FirstName'];
		$signSurgeon1MiddleName =$fetchRows_preprocedure['signSurgeon1MiddleName'];
		$signSurgeon1LastName =$fetchRows_preprocedure['signSurgeon1LastName'];
		$signSurgeon1Status =$fetchRows_preprocedure['signSurgeon1Status'];
		
		$signNurseId =$fetchRows_preprocedure['signNurseId'];
		$signNurseDateTime =$fetchRows_preprocedure['signNurseDateTime'];
		$signNurseDateTimeFormat =$fetchRows_preprocedure['signNurseDateTimeFormat'];
		$signNurseFirstName =stripslashes($fetchRows_preprocedure['signNurseFirstName']);
		$signNurseMiddleName =stripslashes($fetchRows_preprocedure['signNurseMiddleName']);
		$signNurseLastName =stripslashes($fetchRows_preprocedure['signNurseLastName']);
		$signNurseStatus =$fetchRows_preprocedure['signNurseStatus'];
		
		$laser_chk_procedure_image=$fetchRows_preprocedure['chk_laser_procedure_image'];
		$laser_procedure_image =$fetchRows_preprocedure['laser_procedure_image'];
		$laser_procedure_image_path =$fetchRows_preprocedure['laser_procedure_image_path'];
		
		$verified_nurseID =$fetchRows_preprocedure['verified_nurse_Id'];
		$verified_nurseName =stripslashes($fetchRows_preprocedure['verified_nurse_name']);
		$verified_nurseStatus=$fetchRows_preprocedure['verified_nurse_Status'];
		$verified_nurseTimeout	=	$fetchRows_preprocedure['verified_nurse_timeout'];
		if($verified_nurseTimeout <> '0000-00-00 00:00:00' && !empty($verified_nurseTimeout)){
			//$verified_nurseTimeout=date('h:i A',strtotime($verified_nurseTimeout));
			$verified_nurseTimeout=$objManageData->getTmFormat($verified_nurseTimeout);
		}else{
			$verified_nurseTimeout	=	'';	
		}
		
		$verified_surgeonID =$fetchRows_preprocedure['verified_surgeon_Id'];
		$verified_surgeonName =stripslashes($fetchRows_preprocedure['verified_surgeon_Name']);
		$verified_surgeonStatus=$fetchRows_preprocedure['verified_surgeon_Status'];
		$verified_surgeonTimeout	=	$fetchRows_preprocedure['verified_surgeon_timeout'];
		if($verified_surgeonTimeout <> '0000-00-00 00:00:00' && !empty($verified_surgeonTimeout)){
			//$verified_surgeonTimeout=date('h:i A',strtotime($verified_surgeonTimeout));
			$verified_surgeonTimeout=$objManageData->getTmFormat($verified_surgeonTimeout);
		}else{
			$verified_surgeonTimeout	=	'';	
		}
		
		$asa_status	=	$fetchRows_preprocedure['asa_status'];
		/*$prelaserVitalSignTime	=	$fetchRows_preprocedure['prelaserVitalSignTime'];
		if($prelaserVitalSignTime <> '0000-00-00 00:00:00' && !empty($prelaserVitalSignTime)){
			//$prelaserVitalSignTime=date('h:i A',strtotime($prelaserVitalSignTime));
			$prelaserVitalSignTime=$objManageData->getTmFormat($prelaserVitalSignTime);
		}else{
			$prelaserVitalSignTime	=	'';	
		}*/
		if($fetchRows_preprocedure['prelaserVitalSignTime']=="0000-00-00 00:00:00" || $fetchRows_preprocedure['prelaserVitalSignTime']==""){
			$prelaserVitalSignTime = "";
		}else{
			$prelaserVitalSignTime = $objManageData->getTmFormat($fetchRows_preprocedure['prelaserVitalSignTime']);
		}
		
		$postlaserVitalSignTime	=	$fetchRows_preprocedure['postlaserVitalSignTime'];
		if($postlaserVitalSignTime <> '0000-00-00 00:00:00' && !empty($postlaserVitalSignTime)){
			//$postlaserVitalSignTime=date('h:i A',strtotime($postlaserVitalSignTime));
			$postlaserVitalSignTime=$objManageData->getTmFormat($postlaserVitalSignTime);
		}else{
			$postlaserVitalSignTime	=	'';	
		}
		
		$proc_start_time	=	$fetchRows_preprocedure['proc_start_time'];
		if($proc_start_time <> '0000-00-00 00:00:00' && !empty($proc_start_time)){
			//$proc_start_time=date('h:i A',strtotime($proc_start_time));
			$proc_start_time=$objManageData->getTmFormat($proc_start_time);
		}else{
			$proc_start_time	=	'';	
		}
		$proc_end_time	=	$fetchRows_preprocedure['proc_end_time'];
		if($proc_end_time <> '0000-00-00 00:00:00' && !empty($proc_end_time)){
			//$proc_end_time=date('h:i A',strtotime($proc_end_time));
			$proc_end_time=$objManageData->getTmFormat($proc_end_time);
		}else{
			$proc_end_time	=	'';	
		}
		
		$discharge_home = (int)$fetchRows_preprocedure['discharge_home'];
		$patients_relation = stripslashes($fetchRows_preprocedure['patients_relation']);
		$patients_relation_other = stripslashes($fetchRows_preprocedure['patients_relation_other']);
		$patient_transfer = (int)$fetchRows_preprocedure['patient_transfer'];
		$discharge_time = $fetchRows_preprocedure['discharge_time'];
		$discharge_time = ($discharge_time && $discharge_time <> '0000-00-00 00:00:00') ? $objManageData->getTmFormat($discharge_time) : '';
		$version_num =  $fetchRows_preprocedure["version_num"];
	}
	if(!$form_status) { $form_status = $chkNurseSignDetails->form_status; }
	if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
	else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	$current_form_version; }
	
	
//PRE OP ORDER PREFILL
	$laserProcDetailsQry = "select * from preopphysicianorders where patient_confirmation_id = '$pConfId' ";
	$laserProcDetailsRes = imw_query($laserProcDetailsQry) or die((__LINE__).'__'.imw_error());
	$laserProcDetailsNumRow = imw_num_rows($laserProcDetailsRes);
	if($laserProcDetailsNumRow>0) {
		$laserProcDetailsRow = imw_fetch_array($laserProcDetailsRes);
		//$prefilMedicationStatus = $laserProcDetailsRow['prefilMedicationStatus'];
		$preOpPhysicianOrdersId=$laserProcDetailsRow['preOpPhysicianOrdersId'];
	}

	//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
	
	$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
	$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
	while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
		$surgeonProfileIdArrLsr[] = $selectSurgeonRow['surgeonProfileId'];
	}
	if(is_array($surgeonProfileIdArrLsr)){
		$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArrLsr);
	}else {
		$surgeonProfileIdImplode = 0;
	}
	$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
	$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
	$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
	if($selectSurgeonProcedureNumRow>0) {
		while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
			$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
			if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
				
				$surgeonProfileIdFound = $selectSurgeonProcedureRow['profileId'];
			}
		}
		
		/*if($surgeonProfileIdFound) {*/
			$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonProfileId = '$surgeonProfileIdFound' and del_status=''";
		/*}else {	//ELSE SELECT DEFAULT PROFILE OF SURGOEN
			$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonId = '$surgeonId' AND defaultProfile = '1'";
		}*/
		//echo $selectSurgeonProfileFoundQry;
		$selectSurgeonProfileFoundRes = imw_query($selectSurgeonProfileFoundQry) or die(imw_error());
		$selectSurgeonProfileFoundNumRow = imw_num_rows($selectSurgeonProfileFoundRes);
		if($selectSurgeonProfileFoundNumRow > 0) {
			$selectSurgeonProfileFoundRow = imw_fetch_array($selectSurgeonProfileFoundRes);
			$postOpDropSurgeonProfile = stripslashes($selectSurgeonProfileFoundRow['postOpDrop']);
			$medicalEvaluationSurgeonProfile = stripslashes($selectSurgeonProfileFoundRow['medicalEvaluation']);
			$preOpOrdersFound = $selectSurgeonProfileFoundRow['preOpOrders'];
			//$preOpOrdersFoundExplode = explode(',',$preOpOrdersFound);
			
			
		}	
	}
	
	//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID			
	
	//PREFIL THE VALUES IN 'PATIENT PREOP MEDICATION' AT FIRST VIEW
	
			if($prefilMedicationStatus <> 'true' ) {
				$laser_preop_medication	=	(str_replace(',','',$laser_preop_medication)) ?		$laser_preop_medication :	$preOpOrdersFound ;
				$laser_preop_medication	=	explode(",",$laser_preop_medication);
				
				for($k=0;$k<=count($laser_preop_medication);$k++) {
					if($laser_preop_medication[$k]<>"") {
						$selectPreOpmedicationOrderQry = "select * from preopmedicationorder where preOpMedicationOrderId = '$laser_preop_medication[$k]'";
						$selectPreOpmedicationOrderRes = imw_query($selectPreOpmedicationOrderQry) or die(imw_error());
						$selectPreOpmedicationOrderRow = imw_fetch_array($selectPreOpmedicationOrderRes);
						
						$selectMedicationName = $selectPreOpmedicationOrderRow['medicationName'];
						$selectStrength = $selectPreOpmedicationOrderRow['strength'];
						$selectDirections = $selectPreOpmedicationOrderRow['directions'];
						
						$chkPatientPreOpMediQry = "Select * From patientpreopmedication_tbl Where
													patient_confirmation_id = '$pConfId'  And
													medicationName = '".addslashes($selectMedicationName)."'  And
													strength = '".addslashes($selectStrength)."' And
													direction = '".addslashes($selectDirections)."'  And
													sourcePage = 1
													";
						$chkPatientPreOpMediSql	=	imw_query($chkPatientPreOpMediQry);
						$chkPatientPreOpMediNum=	imw_num_rows($chkPatientPreOpMediSql);
						
						if($chkPatientPreOpMediNum == 0)
						{
							$insPatientPreOpMediQry = "insert into patientpreopmedication_tbl set
													preOpPhyOrderId = '$preOpPhysicianOrdersId',
													patient_confirmation_id = '$pConfId',
													medicationName = '".addslashes($selectMedicationName)."',
													strength = '".addslashes($selectStrength)."',
													direction = '".addslashes($selectDirections)."',
													sourcePage = 1
													"; 
						$insPatientPreOpMediRes = imw_query($insPatientPreOpMediQry) or die(imw_error());	
						}
						
						$prefilMedicationStatus = 'true';
					}
				}
			}
	
	//PREFIL THE VALUES IN 'PATIENT PREOP MEDICATION' AT FIRST VIEW	
		
		
	//SHOW DETAIL OF PATIENT PRE OP MEDICATION
	$preOpPatientDetails = $objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $_REQUEST["pConfId"],'patientPreOpMediId','ASC');
	//SHOW DETAIL OF PATIENT PRE OP MEDICATION

?>
<script type="text/javascript">
	var drawCntlNum=1;
	top.frames[0].setPNotesHeight();		
	top.document.getElementById('footer_button_id').style.display = 'inline-block';
	top.document.getElementById('saveBtn').style.display = 'inline-block';
	top.document.getElementById('CancelBtn').style.display = 'inline-block';
	top.document.getElementById('PrintBtn').style.display = 'inline-block';
	top.document.getElementById('SavePrintBtn').style.display = 'inline-block';
	top.document.getElementById('Finalized').style.display = 'inline-block';
	//top.document.getElementById('AmendmentFinalized').style.display = 'inline-block';
	
	function displayNew(){
		var objDisplay = document.getElementById('newMedicationTr').style.display;
		if(objDisplay=='none'){
			document.getElementById('newMedicationTr').style.display = 'inline-block';
		}else{
			document.getElementById('newMedicationTr').style.display = 'none';
		}
	}
	function chkFn(obj){
		alert(obj.checked)
	}
	function showPreOpMediDiv(name1,name2,c,posLeft,posTop){
	
	document.getElementById("PreOpMedicationDiv").style.display = 'inline-block';
	document.getElementById("PreOpMedicationDiv").style.left = posLeft+4+'px';
	document.getElementById("PreOpMedicationDiv").style.top = posTop-218+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
	}
	function showMedTime(){
		var oldRead = document.getElementById('time_med').innerHTML;
		var nowTime = document.getElementById('textTime').value;
		return false;
		var newTime = oldRead+'&nbsp;&nbsp;'+nowTime;
		var isNewVal = document.getElementById('timeToSave').value;
		if(isNewVal == ''){
			document.getElementById('time_med').innerHTML = newTime;
			document.getElementById('textTime').style.display="block";
			document.getElementById('textTime').value = nowTime;
		}
	}
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
	
	function save_medication_time_value() {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
	var medication_time=document.getElementById('textTime').value;
	var patient_id1 	= '<?php echo $_REQUEST["patient_id"];?>';
	var pConfId1 		= '<?php echo $_REQUEST["pConfId"];?>';
	
	var url="pre_op_physician_record_ajax.php";
	url=url+"?medicationTime="+medication_time+'&patient_id='+patient_id1+'&pConfId='+pConfId1;
	xmlHttp.onreadystatechange=AjaxTestingFunTimeMedicated
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	
	function AjaxTestingFunTimeMedicated() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 		
				document.getElementById("timeMedicatedId").innerHTML=xmlHttp.responseText;
				document.getElementById('timeToSave').value = '';
				
			}
	}
	
	function save_physician_time() {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return true;
			}
		var timeFieldsObj = document.getElementsByName('startTimeVal[]');
		var timeFieldsVal = timeFieldsObj(0).value;
		var patient_id1 	= '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 		= '<?php echo $_REQUEST["pConfId"];?>';
		
		
		var url="pre_op_medication_order_time_save.php";
		url=url+'?timeVal='+timeFieldsVal+'&patient_id='+patient_id1+'&pConfId='+pConfId1;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	
	function save_medication(frmVersion) {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return true;
			}
		var medication_name	= encodeURIComponent(document.getElementById('preOpMediOrder').value);
		var strength		= encodeURIComponent(document.getElementById('strength').value);
		var directions		= encodeURIComponent(document.getElementById('direction').value);	
		var patient_id1 	= '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 		= '<?php echo $_REQUEST["pConfId"];?>';
		
		var url="pre_op_medication_order_ajex.php";
		url=url+'?medication_name='+medication_name+'&strength='+strength+'&directions='+directions+'&sourcePage=1&patient_id='+patient_id1+'&pConfId='+pConfId1+'&frmVersion='+frmVersion;
		xmlHttp.onreadystatechange=AjaxTestFunPhysicianOrder 
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	
	function AjaxTestFunPhysicianOrder() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				document.getElementById("medicationTitle").style.display='block';
				document.getElementById("preOpPhysicianShowAjaxId").innerHTML=xmlHttp.responseText;
				document.getElementById('preOpMediOrder').value = '';
				document.getElementById('strength').value = '';
				document.getElementById('direction').value = '';
				//var win = window.open("about:blank");
				//win.document.write("<body>"+xmlHttp.responseText+"</body>");
			}
			/* Safari Compatible * /
			with(document){
				var txt1 = getElementsByName("preOpMediOrderArr[]");
				var txt2 = getElementsByName("strengthArr[]");
				var txt3 = getElementsByName("directionArr[]");
				var txt4 = getElementsByName("timemedsArr[]");
			}
			for(var i=0;i<txt1.length;i++){
				txt1[i].style.width = "120px";
			}
			for(var i=0;i<txt2.length;i++){
				txt2[i].style.width = "90px";
			}
			for(var i=0;i<txt3.length;i++){
				txt3[i].style.width = "175px";
			}
			/* Safari Compatible */
	}
//end	
//delete medication	
 	function delentry(id,DD,frmVersion){
			
		xmlHttp=GetXmlHttpObject();		
		if (xmlHttp==null){

			alert ("Browser does not support HTTP Request");
			return true;
		}		

		var patient_id1 	= '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 		= '<?php echo $_REQUEST["pConfId"];?>';
		
		var url='pre_op_med_del_ajax.php?delId='+id+'&patient_id='+patient_id1+'&pConfId='+pConfId1+'&frmVersion='+frmVersion;
		xmlHttp.onreadystatechange=AjaxTestDel
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
		function AjaxTestDel(){
			if (xmlHttp.readyState==4  || xmlHttp.readyState=="complete"){
				document.getElementById("preOpPhysicianShowAjaxId").innerHTML=xmlHttp.responseText;
			}
			/* Safari Compatible * /
			with(document){
				var txt1 = getElementsByName("preOpMediOrderArr[]");
				var txt2 = getElementsByName("strengthArr[]");
				var txt3 = getElementsByName("directionArr[]");
				var txt4 = getElementsByName("timemedsArr[]");
			}
			for(var i=0;i<txt1.length;i++){
				txt1[i].style.width = "120px";
			}
			for(var i=0;i<txt2.length;i++){
				txt2[i].style.width = "90px";
			}
			for(var i=0;i<txt3.length;i++){
				txt3[i].style.width = "175px";
			}
			/* Safari Compatible */
		}
//end delete medication

//Applet
function get_App_Coords_Laser(objElemLaser){
	var coords_Laser,appName_Laser;
	var objElemLaserImage = document.frm_laser_procedure.laserProcedure_image;
	//appName_Laser = objElemLaser.name;
	appName_Laser = objElemLaser;
	coords_Laser = getCoords(appName_Laser);
	objElemLaserImage.value= refineCoords(coords_Laser);
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
function getCoords(appName){
	var coords = "";
	if(document.applets && document.applets[appName]){	coords = document.applets[appName].getSign(); }
	return coords;
}

function getclear_Laser(objElemLaser){
	document.applets["app_Laser_Image"].clearIt();
	changeColorThis(0,0,0);
	get_App_Coords_Laser(objElemLaser)
}
function changeColorThis(r,g,b){				
	document.applets['app_Laser_Image'].setDrawColor(r,g,b);								
}

function changeColorLaser(r,g,b,objElem){				
	document.applets['app_Laser_Image'].setDrawColor(r,g,b);								
}

//Applet
//FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE 688
	
	function noAuthorityFun(userName) {
		alert("You are not authorised to make signature of "+userName);
		return false;
	}
	function noSignInAdmin() {
		alert("You have yet to make signature in Admin");
		return false;
	}
	
	//Display Signature Of Nurse
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {
		//alert(pagename);
		
		//START TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			var signCheck='true';
			var assignedSurgeonId = '<?php echo $preOpAssignedSurgeonId;?>';
			var assignedSurgeonName = '<?php echo $preOpAssignedSurgeonName;?>';
			var loggedInUserType = '<?php echo $loggedUserType;?>';
			var loggedInUserNameSrg = '<?php echo $loggedInNurseVerifiedName;?>';
			var PreSrg="Dr. ";

			if(loggedInUserId!=assignedSurgeonId && !delSign && loggedInUserType=='Surgeon') {
				var rCheck = confirmOtherSurgeon("This patient is registered to Dr. "+assignedSurgeonName+"\t\t\t\tAre you sure you want to sign the Chart notes of this patient");
				
				if(rCheck==false) {
					signCheck='false';
				}else {
					signCheck='true';
				}
			}
			/*
			if(loggedInUserType=="Surgeon"){
				if(document.forms[0].verify_signature.checked!=true) {
					document.forms[0].verify_signature.checked = true;
					document.forms[0].hiddVerifiedSurgeonId.value = assignedSurgeonId;
					document.forms[0].hiddVerifiedSurgeonName.value = assignedSurgeonName;
					document.getElementById('verifyBySrgId').innerText=PreSrg+loggedInUserNameSrg;
					document.getElementById('verifyBySrgId').style.backgroundColor = '#E2EDFB';
				}else {
					
					
					document.forms[0].verify_signature.checked=false;
					document.forms[0].hiddVerifiedSurgeonId.value = '';
					document.forms[0].hiddVerifiedSurgeonName.value = '';
					document.getElementById('verifyBySrgId').innerText='Surgeon Signature';
					document.getElementById('verifyBySrgId').style.backgroundColor = '#F6C67A';
					
				}
				
			}*/
		//END TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
		
		if(delSign) {
			document.getElementById(TDUserNameId).style.display = 'inline-block';
			document.getElementById(TDUserSignatureId).style.display = 'none';
			//SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
			if(userIdentity=='Surgeon1'){
				if(top.document.forms[0]){
					if(top.document.forms[0].hidd_chkDisplaySurgeonSign) {
						top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'true';
					}
					//START DELETE TIME OUT SIGNATURE
					document.forms[0].verify_signature.checked=false;
					//document.forms[0].hiddVerifiedSurgeonId.value = '';
					//document.forms[0].hiddVerifiedSurgeonName.value = '';
					document.getElementById('verifyBySrgId').innerText='Surgeon Signature';
					document.getElementById('verifyBySrgId').style.backgroundColor = '#F6C67A';
					//END DELETE TIME OUT SIGNATURE
				}
			}		
			//END SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
				
		}else {
			if(signCheck=='true') {	
				document.getElementById(TDUserNameId).style.display = 'none';
				document.getElementById(TDUserSignatureId).style.display = 'inline-block';
				
				//START ADD TIME OUT SIGNATURE FOR SURGEON
				if(loggedInUserType=="Surgeon"){	
					document.forms[0].verify_signature.checked = true;
					//document.forms[0].hiddVerifiedSurgeonId.value = assignedSurgeonId;
					//document.forms[0].hiddVerifiedSurgeonName.value = assignedSurgeonName;
					//document.forms[0].hiddVerifiedSurgeonId.value = loggedInUserId;
					//document.forms[0].hiddVerifiedSurgeonName.value = loggedInUserNameSrg;
					
					document.getElementById('verifyBySrgId').innerText=PreSrg+loggedInUserNameSrg;
					document.getElementById('verifyBySrgId').style.backgroundColor = '#E2EDFB';
					
				}
				//END ADD TIME OUT SIGNATURE FOR SURGEON			
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
		var url=pagename
		//alert(url);
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
				console.log(objId);
				if(objId == 'TDsurgeon1NameId')
				{
					document.getElementById('bp_temp16').value = '';
				}
				else
				{
					displayTimeAmPm('bp_temp16');
				}
			}
	}
	//End Display Signature Of Nurse
	function saveTimeBlur(id,autoIncrId,fieldName) {
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request");
		return true;
	}
	var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
	var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
	
	var url="pre_op_medication_order_blur_ajex.php";
	url=url+'?timeId='+id;
	url=url+"&autoIncrId="+autoIncrId;
	url=url+"&fieldName="+fieldName;
	url=url+"&patient_id="+patient_id1;
	url=url+"&pConfId="+pConfId1;
	
	xmlHttp.onreadystatechange=AjaxTestFunPhysicianOrderTimeId
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
function AjaxTestFunPhysicianOrderTimeId() {
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
		//ONLY SAVE TIME VALUES
	}
}

function addZero(num) {
	return (num >= 0 && num < 10) ? "0" + num : num + "";
}
function formatTime (dt) {
    var formatted = '';

	if (dt) {
		var hours24 = dt.getHours();
		var hours = ((hours24 + 11) % 12) + 1;
		formatted = [formatted, [addZero(hours), addZero(dt.getMinutes())].join(":"), hours24 > 11 ? "pm" : "am"].join(" ");            
	}
	return formatted;
}

//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE
function display_verifed_chk(){
	var loggedId = '<?php echo $loginIdUser;?>';
	var loggedNurseNameId = '<?php echo $loggedInNurseVerifiedName;?>';
	var loggedTypeId = '<?php echo $loggedInNurseVerifiedType;?>';
	var verifiedNurseFilled=document.forms[0].hiddVerifiedNurseName.value;
	
	if(loggedTypeId=="Nurse"){ 
		if(document.forms[0].verify_nurse.checked!=true) {
			document.forms[0].verify_nurse.checked=true;
			document.forms[0].hiddVerifiedNurseId.value = loggedId;
			document.forms[0].hiddVerifiedNurseName.value = loggedNurseNameId;
			document.getElementById('verifyByNrsId').innerText=loggedNurseNameId;
			document.getElementById('verifyByNrsId').style.backgroundColor = '#FFFFFF';
			displayTimeAmPm('bp_temp15');
			
		}else {
			if(verifiedNurseFilled == loggedNurseNameId){
				document.forms[0].verify_nurse.checked=false;
				document.forms[0].hiddVerifiedNurseId.value = '';
				document.forms[0].hiddVerifiedNurseName.value = '';
				document.getElementById('verifyByNrsId').innerText='Nurse Signature';
				document.getElementById('verifyByNrsId').style.backgroundColor = '#F6C67A';
				document.getElementById('bp_temp15').value='';
			}else{
				alert("Only "+ verifiedNurseFilled +" can remove this signature");
			}
		}
	}else{
		alert("NOT AUTHORIZED TO SIGN THE RECORD!");
	}
}

function chkRelationFlds(_this) {
	var obj = $(_this);
	var d = obj.is(':checked') ? false : true;
	var d1 = obj.is(':checked') && $("#patients_relation").val() == 'other' ? false : true;
	$("#patients_relation").prop('disabled',d);
	$("#patients_relation_other").prop('disabled',d1);
}

function chkRelationOther(_this){
	var obj = $(_this);
	var d = obj.val() == 'other' ? false : true;
	$("#patients_relation_other").prop('disabled',d);
}


</script>
<?php
// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;	
// GETTING CONFIRMATION DETAILS

	//START CODE TO GET DOCUMENTS OF EKG H&P CONSENT
	$ekgHpLink = "";
	$anesConsentEkgHpArr = array('H&P', 'Consent');
	foreach($anesConsentEkgHpArr as $anesConsentEkgHpName) {
		$eKgHpCLickFun='';
		$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath
							 FROM  scan_upload_tbl sut,scan_documents sd
							 WHERE sd.confirmation_id 	= '".$pConfId."'
							 AND   sut.confirmation_id 	= '".$pConfId."'
							 AND   sd.document_name 	= '".$anesConsentEkgHpName."'
							 AND   sd.document_id 		= sut.document_id
							 ORDER BY sd.document_id, sut.document_id
							"; 
		$scnFoldrEkgHpRes = imw_query($scnFoldrEkgHpQry) or die(imw_error());
		$scnFoldrEkgHpNumRow = imw_num_rows($scnFoldrEkgHpRes);
		if($scnFoldrEkgHpNumRow<=0) {
			if($anesConsentEkgHpName=='EKG' || $anesConsentEkgHpName=='H&P') {
				$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath 
									 FROM  scan_upload_tbl sut,scan_documents sd 
									 WHERE sd.confirmation_id 	= '".$pConfId."'
									 AND   sut.confirmation_id 	= '".$pConfId."'
									 AND   sut.document_name like '%".$anesConsentEkgHpName."%'
									 AND   sd.document_id 		= sut.document_id
									 ORDER BY sd.document_id, sut.document_id
									"; 
				$scnFoldrEkgHpRes = imw_query($scnFoldrEkgHpQry) or die(imw_error());
				$scnFoldrEkgHpNumRow = imw_num_rows($scnFoldrEkgHpRes);
			}
		}
		if($scnFoldrEkgHpNumRow>0) {
			while($scnFoldrEkgHpRow = imw_fetch_array($scnFoldrEkgHpRes)) {
				$scan_upload_id = $scnFoldrEkgHpRow['scan_upload_id'];
				$image_type = $scnFoldrEkgHpRow['image_type'];
				$pdfFilePath = $scnFoldrEkgHpRow['pdfFilePath'];
				
				if($image_type=='application/pdf') $image_type = 'pdf';
				$imageTypeNew = "\'".$image_type."\'";
				$scanUploadId = "\'".$scan_upload_id."\'";
				$pdfFilePathNew = "\'".$pdfFilePath."\'";
				$eKgHpCLickFun.= 'top.openImage('.$scanUploadId.','.$imageTypeNew.','.$pdfFilePathNew.');';
			}
		}
		if($eKgHpCLickFun!='') {
			$anesConsentEkgHpNameNew = $anesConsentEkgHpName;
			$anesConsentEkgHpNameNew = str_ireplace('H&P','H&amp;P',$anesConsentEkgHpNameNew);
			$anesConsentEkgHpNameNew = str_ireplace('Ocular Hx','OCX',$anesConsentEkgHpNameNew);
			$anesConsentEkgHpNameNew = str_ireplace('Health Questionnaire','HQ',$anesConsentEkgHpNameNew);
			$ekgHpLink.='<a href="#" class="btn-sm" onclick="'.$eKgHpCLickFun.'">'.$anesConsentEkgHpNameNew.'</a>&nbsp;';
		}
	}
	//END CODE TO GET DOCUMENTS OF EKG H&P CONSENT
?>
<script type="text/javascript">dragresize.apply(document);</script>
<?php

//BACKGROUND COLOR TO BE GIVEN TO <td>
$bgcolor_cond	= $rowcolor_op_room_record;
$bgcolor_white	= "#FFFFFF";

//START PREFILL POSTOP ORDER IF RECORD YET TO SAVE ONCE
	if($laserprocedureSaveFromChart == 1  || $laserprocedurePatientRecordid == 0) { 
		if(!$laser_post_progress_detail){
			$laser_post_progress_detail=$postOpDropSurgeonProfile;
		}
		$laser_medical_evaluation	= $medicalEvaluationSurgeonProfile;
	}
//START PREFILL POSTOP ORDER IF RECORD YET TO SAVE ONCE	
?>
<div id="post" style="display:none; position:absolute;"></div>
<div id="divSaveAlert" style="position:absolute;left:350px; top:150px; display:none; z-index:1000;">
	<?php 
		$bgCol = $title_op_room_record;
		$borderCol = $title_op_room_record;
		include('saveDivPopUp.php'); 
	?>
</div>
<form name="frm_laser_procedure" id="frm_laser_procedure" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
	<input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
	<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
	<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
	<input type="hidden" name="saveRecord" id="saveRecord" value="true">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">
	<input type="hidden" name="laserprocedureRecord_postup" id="laserprocedureRecord_postup" value="<?php echo $laserprocedureRecordid;?>">
	<input type="hidden" name="timeToSave" id="timeToSave">
	<input type="hidden" name="medicationTime" id="medicationTime" value="">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
	<input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
    <input type="hidden" name="saveFromChart" id="saveFromChart" value="0">
	<input type="hidden" name="ascId" id="ascId" value="<?php echo $ascId; ?>">
	<input type="hidden" name="thisId" id="thisId" value="<?php echo $thisId; ?>">
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
	<input type="hidden" name="frmAction" id="frmAction" value="laser_procedure.php">
	<input type="hidden" name="preOpPhysicianOrdersId" id="preOpPhysicianOrdersId" value="<?php echo $preOpPhysicianOrdersId; ?>">
	<input type="hidden" id="bp" name="bp_hidden" >
    <input type="hidden" id="hidd_isHTML5OK" name="hidd_isHTML5OK" value="<?php echo $isHTML5OK;?>" >
    <input type="hidden" id="hidd_curDt" name="hidd_curDt" value="<?php echo date("YmdHis");?>" >
    <input type="hidden" name="hiddchk_laser_procedure_image" id="hiddchk_laser_procedure_image" value="<?php echo $laser_chk_procedure_image ; ?>">
    <input type="hidden" name="hidd_prefilMedicationStatus" id="hidd_prefilMedicationStatus" value="true"> <!--<?php //echo $prefilMedicationStatus ; ?>"-->
    
	<input type="hidden" id="vitalSignGridHolder" />
    
<div class="slider_content scheduler_table_Complete" id="" style="" onClick="calCloseFun();preCloseFun('evaluationChiefComplaint');preCloseFun('evaluationpast_medicalHX');preCloseFun('evaluationpresent_illness_hx');preCloseFun('evaluationSLE');preCloseFun('evaluationmental_state');preCloseFun('evaluationfundus_exam');preCloseFun('evaluationPreDiagnosisDiv');preCloseFun('evaluationPost_ProgressNote');preCloseFun('evaluationpost_operative_status');preCloseFun('evaluationspot_duration');preCloseFun('evaluationspot_size');preCloseFun('evaluationpower');preCloseFun('evaluationshots');preCloseFun('evaluationtotal_energy');preCloseFun('evaluationdegree_of_opening');preCloseFun('evaluationcount');preCloseFun('evaluationanesthesia');preCloseFun('PreOpMedicationDiv');preCloseFun('evaluationPreOpMedDiv');preCloseFun('evaluationmedication');preCloseFun('evaluationPreDefineDiv');preCloseFun('evaluationexposure');preCloseFun('evaluationProcedureNotesDiv'); ">
	<!--
    <div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_op">
		<span class="bg_span_op">
			Laser Procedure
		</span>-->
		<?php
			$epost_table_name = "laser_procedure_patient_table";
			include("./epost_list.php");
		?>	
			
	<!--</div>	-->
	<!--- - -- --   Sign -Out Ends Here -->
	      
	<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
		<div class="panel panel-default bg_panel_op">
			<div class="panel-heading">
				   <h3 class="panel-title rob">History</h3>
			</div>
			<?php
				$historyBackColor=$chngBckGroundColor;
				if($laser_chief_complaint_detail || $laser_past_med_hx_detail || $laser_present_illness_hx_detail || $laser_medication_detail )
				{ 
					$historyBackColor=$whiteBckGroundColor; 
				}
				
?>
			<div class="panel-body">
				<div class="full_width">
<?php

	if($laser_chk_chief_complaint){  
?>
					<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
						<div class="inner_safety_wrap">
							<div class="row">
								<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
									<a class="rob alle_link show-pop-list_g btn btn-default" id='chief_complaint' onClick="return showChiefCompliant('txtarea_chief_complaint', '', 'no', parseInt(findPos_X('chief_complaint'))+10, parseInt(findPos_Y('chief_complaint')-220)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='';">
										<span class="fa fa-caret-right"></span> Chief Complaint
									</a>
								</div>
								<div class="clearfix margin_adjustment_only visible-xs"></div>
								<!--<div class="clearfix margin_adjustment_only visible-lg"></div>-->
								<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7"> 
									<textarea class="form-control" id="txtarea_chief_complaint" onBlur="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" onFocus="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" onKeyUp="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');"  name="txtarea_chief_complaint" style="<?php echo $historyBackColor;?>"><?php echo $laser_chief_complaint_detail;?></textarea>
                                    <input type="hidden" name="hiddchk_laser_chief_complaint" id="hiddchk_laser_chief_complaint" value="<?php echo $laser_chk_chief_complaint;?>">
								</div> 
						   </div>
						</div>
					</div>
<?php
	}
?>
					<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm visible-xs">
						<div class="clearfix margin_adjustment_only border-dashed"></div>
					</div>
<?php
	if($laser_chk_present_illness_hx){ 
?>
					<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
						<div class="inner_safety_wrap">
							  <div class="row">
								<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
									 <a class="rob alle_link show-pop-list_me btn btn-default " id='present_illness_hx' onClick="return showpresent_illness_hx('txtarea_present_illness_hx', '', 'no', parseInt(findPos_X('present_illness_hx'))+10, parseInt(findPos_Y('present_illness_hx')-220)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='';">
										<span class="fa fa-caret-right"></span> Hx. of Present Illness
									</a>
								</div>
                                
								<div class="clearfix margin_adjustment_only visible-xs"></div>
								<!--
                                <div class="clearfix margin_adjustment_only visible-lg"></div>
                                -->
								<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7">
									<textarea class="form-control" id="txtarea_present_illness_hx"  onBlur="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" onFocus="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" onKeyUp="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" name="txtarea_present_illness_hx" style="<?php echo $historyBackColor;?>"><?php echo $laser_present_illness_hx_detail;?></textarea>
                                    <input type="hidden" name="hiddchk_laser_present_illness_hx" id="hiddchk_laser_present_illness_hx" value="<?php echo $laser_chk_present_illness_hx;?>">
                                    
								</div> 
							 </div>
						</div>
					</div>
<?php
	}
?>
					<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
						<div class="clearfix margin_adjustment_only border-dashed"></div>
					</div>
<?php
	if($laser_chk_past_med_hx){ 
?>
					<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
						<div class="inner_safety_wrap">
							<div class="row">
								<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
									<a class="rob alle_link show-pop-list_fundus btn btn-default" id='past_medicalhx' onClick="return showpast_medicalHX('txtarea_past_medicalhx', '', 'no', parseInt(findPos_X('past_medicalhx'))+10, parseInt(findPos_Y('past_medicalhx')-220)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='';">
										<span class="fa fa-caret-right"></span> Past Medical Hx
									</a>
								</div>
								<div class="clearfix margin_adjustment_only visible-xs"></div>
								<!--<div class="clearfix margin_adjustment_only visible-lg"></div>-->
								<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7"> 
									<textarea class="form-control" id="txtarea_past_medicalhx" onBlur="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" onFocus="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" onKeyUp="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');"  name="txtarea_past_medicalhx" style="<?php echo $historyBackColor;?>"><?php echo $laser_past_med_hx_detail;?></textarea>
                                    <input type="hidden" name="hiddchk_laser_past_med_hx" id="hiddchk_laser_past_med_hx" value="<?php echo $laser_chk_past_med_hx;?>">
								</div> 
							</div>
						</div>
					</div>
<?php
	}
?>
					<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm visible-xs">
						<div class="clearfix margin_adjustment_only border-dashed"></div>
					</div>
<?php
	if($laser_chk_medication){ 
?>
					<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
						<div class="inner_safety_wrap">
							<div class="row">
								<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
									<a class="rob alle_link show-pop-list_preop btn btn-default" id='medications' onClick="return showmedication('txtarea_medications', '', 'no', parseInt(findPos_X('medications'))+10, parseInt(findPos_Y('medications')-220)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='';">
										<span class="fa fa-caret-right"></span> Ocular Medication & Dosage
									</a>
								</div>
								<div class="clearfix margin_adjustment_only visible-xs"></div>
								<!--<div class="clearfix margin_adjustment_only visible-lg"></div>-->
								<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7"> 
									<textarea class="form-control" id="txtarea_medications" onBlur="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" onFocus="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" onKeyUp="changeTxtGroupColor(4,'txtarea_chief_complaint','txtarea_past_medicalhx','txtarea_present_illness_hx','txtarea_medications');" name="txtarea_medications" style="<?php echo $historyBackColor;?>"><?php echo $laser_medication_detail;?></textarea>
                                    <input type="hidden" name="hiddchk_laser_medication" id="hiddchk_laser_medication" value="<?php echo $laser_chk_medication;?>">
								</div> 
							</div>
						</div>
					</div>
<?php
	}
?>
				</div>
			</div>
		</div>
	</div>

	<div class="full_width">
		<div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
			<div class="panel panel-default bg_panel_op">
				<div class="panel-heading">
					<a data-placement="top" class="panel-title rob alle_link show-pop-trigger2 btn btn-default" id='allergies' onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', '10', findPos_X('allergies')+10, (findPos_Y('allergies')-140)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='iframe_allergies_pre_op_nurse_rec';">
						<span class="fa fa-caret-right"></span> Allergies/Drug Reaction
					</a>
					<div class="right_label" style="top:10px;">
						<label for="chbx_drug_react">
							<input type="checkbox" onClick="javascript:txt_enable_disable_frame1('iframe_allergies_pre_op_nurse_rec','chbx_drug_react','Allergies_quest','Reaction_quest',10)" <?php if($allergiesNKDA_patientconfirmation_status=="Yes"){ echo 'CHECKED'; } ?> value="Yes" name="chbx_drug_react" id="chbx_drug_react" tabindex="7" /> NKA
						</label>
						<label for="chbx_drug_react_yes">
							<input type="checkbox" <?php if($allergies_status_reviewed=='Yes'){ echo 'CHECKED'; } ?> value="Yes" name="chbx_drug_react_reviewed" id="chbx_drug_react_yes"  tabindex="7" /> Allergies Reviewed
						</label>
					</div>
				</div>
				<div class="panel-body">
					<div class="inner_safety_wrap">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
							   <!--- ---- Table --->
								<div class="scheduler_table_Complete ">
									<div class="my_table_Checkall table_slider_head">
										<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf table-striped">
											<thead class="cf">
												<tr>
													<th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Name</th>
													<th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Reaction</th>
												</tr>
											</thead>
										</table>
									</div>
									<div id="iframe_allergies_pre_op_nurse_rec" class="table_slider">
<?php  
	$allgNameWidth=210;
	$allgReactionWidth=200;
	include("health_quest_spreadsheet.php");
?>									</div>                
								</div>
								<!--- ---- Table --->
							</div>
							<!-- Col-3 ends  -->
						</div>	 
					</div>
				</div> <!-- Panel Body -->
			</div>
		</div>
<?php
	$nurseVerifySignBackColor=$chngBckGroundColor;
	if($verified_nurseName){
		$nurseVerifySignBackColor=$whiteBckGroundColor; 
	}
?>
		<div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
			<div class="panel panel-default bg_panel_op">
				<div class="panel-heading">
                	
                    <div class="col-md-10 col-sm-9 col-xs-9 col-lg-10"><h3 class="panel-title rob">Time Out</h3></div>
                    <div class="col-md-2 col-sm-3 col-xs-3 col-lg-2"><h3 class="panel-title rob">Time</h3></div>
                    
					 
                    
                    
				</div>
                
				<div class="panel-body padding_0">
                	
					<div class="full_width inner_safety_wrap" style="margin-top:8px;">
						
                        <div class="col-md-9 col-sm-7 col-xs-7 col-lg-9" style="vertical-align:middle !important; ">
							<span class="badge"> 1 </span> &nbsp; <label for="verify_nurse" class="">Patient identification verified by <span id="verifyByNrsId" class="text_10b" onClick="javascript : display_verifed_chk();" style="cursor: pointer; <?php echo $nurseVerifySignBackColor;?>">
								<?php if($verified_nurseName!=""){ echo "<strong>".$verified_nurseName."</strong>"; } else { ?>Nurse Signature <?php }?></label>
						</div>
						<div class="col-md-1 col-sm-2 col-xs-2 col-lg-1 text-center padding_0">
							<input type="checkbox"  disabled name="verify_nurse" id="verify_nurse" tabindex="7" <?php if($verified_nurseName!=""){ echo "checked"; }else {} ?>>
								<input type="hidden" name="hiddVerifiedNurseId" id="hiddVerifiedNurseId" value="<?php echo $verified_nurseID;?>">
								<input type="hidden" name="hiddVerifiedNurseName" id="hiddVerifiedNursename" value="<?php echo $verified_nurseName;?>">
						</div>
                        <div class="col-md-2 col-sm-3 col-xs-3 col-lg-2 text-left padding_6 " style="margin-top:-6px;">
                        	<input type="text" class="form-control padding_2" name="verified_nurse_timeout" id="bp_temp15" style="height:28px;" value="<?php echo $verified_nurseTimeout;?>" onclick="getShowNewPos(parseInt(findPos_Y('bp_temp15'))-80,parseInt(findPos_X('bp_temp15'))-155,'flag15');">
                     	</div>
						
                        
                        <div class="clearfix"></div>
<?php
	//$surgeonVerifySignBackColor=$chngBckGroundColor;
	if(!$verified_surgeonName){
		$surgeonVerifySignBackColor=$chngBckGroundColor; 
	}
?>	
						<div class="col-md-9 col-sm-7 col-xs-7 col-lg-9">
							<span class="badge"> 2 </span> &nbsp;<label for="verify_signature" class=""> <?php if($verified_surgeonName!=""){echo $laser_patientConfirmSiteTemp;}else{echo "Site";} ?> and Patient verified  by <span id="verifyBySrgId" class="text_10b" style=" <?php echo $surgeonVerifySignBackColor;?> "><?php if($verified_surgeonName!=""){ echo "<strong> Dr. ".$verified_surgeonName."</strong>"; }else{ ?> Surgeon Signature<?php }?></span></label>
						</div>
						<div class="col-md-1 col-sm-2 col-xs-2 col-lg-1 text-center padding_0">
							<input type="checkbox" disabled name="verify_signature" id="verify_signature" tabindex="7" <?php if($verified_surgeonName!=""){ echo "checked"; }else {} ?>>
								<input type="hidden" name="hiddVerifiedSurgeonId" id="hiddVerifiedSurgeonId" value="<?php echo $verified_surgeonID;?>">
								<input type="hidden" name="hiddVerifiedSurgeonName" id="hiddVerifiedSurgeonname" value="<?php echo $verified_surgeonName;?>">
						</div>
                        
                        <div class="col-md-2 col-sm-3 col-xs-3 col-lg-2 text-left padding_6 " style="margin-top:-6px;">
                        	<input type="text" class="form-control padding_2" style="height:28px;" name="verified_surgeon_timeout" id="bp_temp16" value="<?php echo $verified_surgeonTimeout;?>" <?php if($loggedInNurseVerifiedType == 'Surgeon') { echo ' onclick="getShowNewPos(parseInt(findPos_Y(\'bp_temp16\'))-100,parseInt(findPos_X(\'bp_temp16\'))-155,\'flag16\');"';} else echo 'disabled'; ?> />
                            
                     	</div>
                        
                        <div class="clearfix"></div>
                        <div class="clearfix margin_adjustment_only border-dashed  "></div>
                        <div class="clearfix margin_adjustment_only "></div>
                        
                        <div class=" col-md-12 col-sm-6 col-xs-12 col-lg-6 " style="padding-right:6px;" >
                        	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5 padding_0 ">
                        		<label> Surgery Start Time</label>
                        	</div>
                            <div class="col-md-7 col-sm-7 col-xs-12 col-lg-7 padding_0 ">
                            	 <input type="text" class="form-control " style="height:28px;" name="proc_start_time" id="bp_temp17" value="<?php echo $proc_start_time;?>" onclick="displayTimeAmPm('bp_temp17');getShowNewPos(parseInt(findPos_Y('bp_temp17'))-160,parseInt(findPos_X('bp_temp17'))+40,'flag17');" />
                          	</div>
                     	</div>
                        <div class="clearfix margin_adjustment_only visible-xs visible-md "></div>
                        <div class=" col-md-12 col-sm-6 col-xs-12 col-lg-6 " style="padding-right:6px;" >
                        	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5 padding_0 ">
                        		<label> Surgery End Time</label>
                        	</div>
                            <div class="col-md-7 col-sm-7 col-xs-12 col-lg-7 padding_0  ">
                            	 <input type="text" class="form-control " style="height:28px;" name="proc_end_time" id="bp_temp18" value="<?php echo $proc_end_time;?>" onclick="displayTimeAmPm('bp_temp18');getShowNewPos(parseInt(findPos_Y('bp_temp18'))-160,parseInt(findPos_X('bp_temp18'))+40,'flag18');"  />
                          	</div>
                     	</div>
                        
                        <div class="clearfix margin_adjustment_only "></div>
                        <div class="clearfix margin_adjustment_only "></div>
                        
					</div>   
				</div> <!-- Panel Body -->
			</div>
		</div>
		
		<div class="clearfix"></div>
<?php				
	$physicalExamBackColor=$chngBckGroundColor;
	if($best_correction_vision_R || $best_correction_vision_L || $laser_sle_detail || $laser_mental_state_detail || $laser_fundus_exam_detail ){
		$physicalExamBackColor=$whiteBckGroundColor; 
	}
	$physicalExaminationDisplay='none';
	//if($laser_chk_sle=='on' || $laser_chk_fundus_exam=='on' || $laser_chk_mental_state=='on'){
		$physicalExaminationDisplay='inline-block';
	//}
?>
		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
			<div class="panel panel-default bg_panel_op">
				<div class="panel-heading haed_p_clickable">
					<h3 class="panel-title rob"> Medical Evaluation  </h3>
					<span class="clickable"><i class="glyphicon glyphicon-chevron-<?php echo ($physicalExaminationDisplay!="none")?'up':'down'; ?>"></i></span>
				</div>
				<div class="panel-body" style="display: <?php echo $physicalExaminationDisplay; ?>">
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 wrap_right_inner_anesth ">
						<div class="row">
							<div class="col-md-4 col-sm-5 col-xs-6 col-lg-3">
								<p class="rob l_height_28"> Patient reported medical status: </p>		
							</div>
							<div class="col-md-8 col-sm-7 col-lg-9 col-xs-6">
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 overflow_span2 height_adjust_label">
										<label for="stableChkBox">
											<input type="checkbox" id="stableChkBox" name="stableChkBox" value="Yes" <?php if($stable_chbx=='Yes'){?> checked="checked" <?php } ?> /> Stable. no acute illness
										</label> &nbsp;
										<label for="stableChkBoxOther" data-toggle="collapse" data-target="#stableTxtBoxOther">
											<input type="checkbox" name="stableChkBoxOther" value="Yes" id="stableChkBoxOther" <?php if($stable_other_chbx=='Yes'){?> checked="checked" <?php } ?>/> Other
										</label>
										<input class="form-control collapse inline_form_text <?php if($stable_other_chbx=='Yes'){?>in<?php } ?>" name="stableTxtBoxOther" id="stableTxtBoxOther" value="<?php echo $stable_other_txtbx; ?>" type="text" style="height:auto;" />
									</div>
								</div>	
							</div>                                                                                
						</div>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
						<div class="clearfix border-dashed margin_adjustment_only"></div>
					</div>
					<div class="wrap_right_inner_anesth">
						<div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
							<div class="row">
								<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5">
									<label class="f_size rob">
										<b>Best Corrected Vision</b>
									</label>   
								</div>
								<div class="col-md-7 col-sm-7 col-xs-7 col-lg-7">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                                        	<div class="row">
                                            	<label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 f_size rob padding_0 text-right">R 20/</label>
                                            	<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                            		<input type="text" class="form-control" id="bp_temp" onKeyUp="displayText1=this.value;" name="best_correction_vision_R" value="<?php echo $best_correction_vision_R;?>" maxlength="7" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp'))-200,parseInt(findPos_X('bp_temp')),'flag1');" />
                                          		</div>
                                            </div>
                                   		</div>
                                        
										<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                                        	<div class="row">
												<label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 f_size rob padding_0 text-right">L 20/</label>
                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                    <input type="text" class="form-control" id="bp_temp2" onKeyUp="displayText2=this.value;" name="best_correction_vision_L" value="<?php echo $best_correction_vision_L;?>" maxlength="3" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp2'))-200,parseInt(findPos_X('bp_temp2')),'flag2');" />  
                                                </div>
                                        	</div>
                                      	</div>
                                        
									</div>
                               	</div>
                                
							</div>
						</div>
						
						<div class="margin_adjustment_only visible-xs "></div>
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix visible-xs">
							<div class="clearfix border-dashed margin_adjustment_only"></div>
						</div>
                    
						<div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
							<div class="row">
								<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5">
									<label class="f_size rob">
										<b> Glare Acuity</b>
									</label>   
								</div>
								<div class="col-md-7 col-sm-7 col-xs-7 col-lg-7">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
											<div class="row">
												<label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 f_size rob padding_0 text-right">R 20/</label>
												<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
													<input type="text" class="form-control" id="bp_temp11" onKeyUp="displayText11=this.value;" name="glare_acuity_R" value="<?php echo $glare_acuity_R;?>" maxlength="7" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp11'))-200,parseInt(findPos_X('bp_temp11'))-20,'flag11');" />
												</div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
											<div class="row">
												<label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 f_size rob padding_0 text-right">L 20/</label>
												<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
													<input type="text" class="form-control" id="bp_temp12" onKeyUp="displayText12=this.value;" name="glare_acuity_L" value="<?php echo $glare_acuity_L;?>" maxlength="3" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp12'))-200,parseInt(findPos_X('bp_temp12'))-20,'flag12');">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Above Protion Ends -->
						<div class="clearfix"></div>
						<div class="full_width">
<?php
	if($laser_chk_sle){
?>
							<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix hidden-lg ">
								<div class="clearfix border-dashed margin_adjustment_only"></div>
							</div>
                        
							<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
								<div class="inner_safety_wrap">
									<div class="row">
										<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
											<a class="rob alle_link show-pop-list_g1 btn btn-default" id="sle_id" onClick="return showSLE('txtSLE', '', 'no', parseInt($(this).offset().left), (parseInt( $(this).offset().top -180 ) < $(document).scrollTop() ? parseInt( $(this).offset().top + 60 )  : parseInt( $(this).offset().top -185)  ) ),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> SLE
											</a>
										</div>
										<div class="clearfix margin_adjustment_only visible-xs"></div>
										<!--<div class="clearfix margin_adjustment_only visible-lg"></div>-->
										<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7"> 
											<textarea class="form-control" id="txtSLE" name="txtSLE"><?php echo $laser_sle_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_sle" id="hiddchk_laser_sle" value="<?php echo $laser_chk_sle;?>">
										</div> 
									</div>
								</div>
							</div>
<?php
	}
?>
							<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm visible-xs">
								<div class="clearfix margin_adjustment_only border-dashed"></div>
							</div>
<?php
	if($laser_chk_fundus_exam){
?>
							<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
								<div class="inner_safety_wrap">
									<div class="row">
										<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
											<a class="rob alle_link show-pop-list_g2 btn btn-default" id="fundus_Exam_id" onClick="return showFundus_Exam('txtarea_Fundus_Exam', '', 'no',  parseInt($(this).offset().left), (parseInt( $(this).offset().top -180 ) < $(document).scrollTop() ? parseInt( $(this).offset().top + 60 )  : parseInt( $(this).offset().top -185)  )),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Fundus Exam
											</a>
										</div>
										<div class="clearfix margin_adjustment_only visible-xs"></div>
										<!--<div class="clearfix margin_adjustment_only visible-lg"></div>-->
										<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7"> 
											<textarea class="form-control" id="txtarea_Fundus_Exam" name="txtarea_Fundus_Exam"><?php echo $laser_fundus_exam_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_fundus_exam" id="hiddchk_laser_fundus_exam" value="<?php echo $laser_chk_fundus_exam;?>">
										</div> 
									</div>
								</div>
							</div>
<?php
	}
?>
							<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
								<div class="clearfix margin_adjustment_only border-dashed"></div>
							</div>
<?php
	if($laser_chk_mental_state){
?>
							  <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
								<div class="inner_safety_wrap">
									  <div class="row">
										<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
											<a data-placement="top" class="rob alle_link show-pop-list_g3 btn btn-default" id="mental_state_id" onClick="return showmental_state('txt_mental_state', '', 'no', parseInt($(this).offset().left), (parseInt( $(this).offset().top - 180 ) < $(document).scrollTop() ? parseInt( $(this).offset().top + 60 ) : parseInt( $(this).offset().top -185)  )),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Mental State
											</a>
										</div>
										<div class="clearfix margin_adjustment_only visible-xs"></div>
										<!--<div class="clearfix margin_adjustment_only visible-lg"></div>-->
										<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7">
											<textarea class="form-control" id="txt_mental_state" name="txt_mental_state"><?php echo $laser_mental_state_detail;?></textarea>
										</div> 
									 </div>
								</div>
							</div>
<?php
	}
?>
							<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm visible-xs">
								<div class="clearfix margin_adjustment_only border-dashed"></div>
							</div> 
							<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
								<div class="inner_safety_wrap">
									<div class="row">
										<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
											<label>ASA</label>
										</div>
										<div class="clearfix margin_adjustment_only visible-xs"></div>
										<!--<div class="clearfix margin_adjustment_only visible-lg"></div>
                                        class="colorChkBx"
                                        style="background-color:#F6C67A"
                                        onclick="changeDiffChbxColor(3,'asa_status1','asa_status2','asa_status3');"
                                        -->
                                        <div class="col-md-7 col-sm-8 col-xs-12 col-lg-7"> 
                                        	
											<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
												<label for="asa_status1" onClick="javascript:checkSingle('asa_status1','asa_status');">
													<span ><input type="checkbox" value="I" id="asa_status1" name="asa_status" tabindex="1" <?=($asa_status == 'I' ? 'checked' : '' )?>></span> I
												</label>
											</div>
                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
												<label for="asa_status2" onClick="javascript:checkSingle('asa_status2','asa_status');">
													<span ><input type="checkbox" value="II" id="asa_status2" name="asa_status" tabindex="2" <?=($asa_status == 'II' ? 'checked' : '' )?>></span> II
												</label>
											</div>
                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
												<label for="asa_status3" onClick="javascript:checkSingle('asa_status3','asa_status');">
													<span ><input type="checkbox" value="III" id="asa_status3" name="asa_status" tabindex="3" <?=($asa_status == 'III' ? 'checked' : '' )?>></span> III
												</label>
											</div>
                                          	          
                                                    
										</div> 
									</div>
								</div>
							</div>
                            
							<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm visible-xs">
								<div class="clearfix margin_adjustment_only border-dashed"></div>
							</div> 
							<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
								<div class="inner_safety_wrap">
									<div class="row">
										<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5">
											<label>Others</label>
										</div>
										<div class="clearfix margin_adjustment_only visible-xs"></div>
										<!--<div class="clearfix margin_adjustment_only visible-lg"></div>-->
										<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7"> 
											<textarea class="form-control" id="txtarea_other" name="txtarea_other"><?php echo $laser_other_detail;?></textarea>
										</div> 
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
			<div class="panel panel-default bg_panel_op">
				<div class="panel-heading">
					<h3 class="panel-title rob"> Pre Op Orders  </h3>
				</div>
				<div id="p_check_in" class="panel-body ">
					<div class="row">
						<p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-12">
							On arrival the following drops will be given to the <?php echo $laser_patientConfirmSiteTemp; ?>
						</p>
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
							<div class="clearfix border-dashed margin_adjustment_only"></div>
						</div>  
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
							<div class="scanner_win new_s">
								<h4>
								   <span> List of Pre-OP Medication Orders</span>      
								</h4>
							</div>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
<?php
$preOpHeight='150';
if($sect=="print_emr"){
	$preOpHeightPrint=(count($preOpPatientDetails)*24);
	if($preOpHeightPrint>$preOpHeight){
		$preOpHeight = $preOpHeightPrint;	
	}
}
if($prelaserprocedure_templete_tblNumRow){
	$table_row = "";
	if($browserName=="IE"){
		$table_row = "block";
	}
	else{
		$table_row = "table-row";
	}
?>
							<div id="medicationTitle" class="full_width" style="display:<?php echo(count($preOpPatientDetails)>0)?'block':'none'; ?>">
								<div id="" class="inner_safety_wrap padding_adjust_post">
									<div class="row">
										<div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
											<div class="row">
												<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
													<label>Medication</label>
												</div>
												<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
													<label>Strength</label>
												</div>
												<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
													<label>Direction</label>
												</div>
												<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
													
												</div>
											</div>
										</div>   
									</div>
								</div>
							</div>
<?php
	if(count($preOpPatientDetails)>0){
?>
							<div class=" max-height-adjust-post full_width">
<?php
	}
?>
								<div id="preOpPhysicianShowAjaxId" class="inner_safety_wrap padding_adjust_post">
<?php
	if(count($preOpPatientDetails)>0){
?>
									<?php if(!$medicationStartTimeVal) $medicationStartTimeVal = "";?>
									<input type="hidden" name="hourVal" id="hourVal" value="<?php print substr($medicationStartTimeVal,0,2); ?>">
									<input type="hidden" name="minuteVal" id="minuteVal" value="<?php print substr($medicationStartTimeVal,3,-2); ?>">
									<input type="hidden" name="statusVal" id="statusVal" value="<?php print substr($medicationStartTimeVal,5); ?>">
									
<?php	
		$k=0;		
		foreach($preOpPatientDetails as $detailsOfMedication){
			$parientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
			$preDefined = $detailsOfMedication->medicationName;
			$strength = $detailsOfMedication->strength;
			$directions = $detailsOfMedication->direction;
			$timemeds = $objManageData->getTmFormat($detailsOfMedication->timemeds);
			$timemeds1=array();
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds1);
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds2);
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds3);
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds4);
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds5);
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds6);
			
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds7);
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds8);
			$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds9);
			++$k;
			
			if($k==1){
				$disptr = $table_row;
			}
			else{
				$disptr='none';
			}
			
			$dir = explode('X',strtoupper($directions));
			$freq = substr(trim($dir[1]),0,1);
			$freq = $freq > 6 ? 6 : $freq;
			$minsDir = explode('Q',strtoupper($dir[1]));
			$min=substr(trim($minsDir[1]),0,-3);
?>
									<div class="row medicine_row" id="deltr<?php echo $k; ?>">
										<input type="hidden" name="parientPreOpMediOrderId[]" id="IDS<?php echo $parientPreOpMediOrderId; ?>" value="<?php echo $parientPreOpMediOrderId; ?>">
										<div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
											<div class="row">
												<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3" id="deltd1<?php echo $k; ?>">
													<input type="text" class="form-control" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>">
												</div>
												<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3" id="deltd2<?php echo $k; ?>">
													<input type="text" class="form-control" name="strengthArr[]" value="<?php echo stripslashes($strength); ?>">
												</div>
												<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3" id="deltd3<?php echo $k; ?>">
													<input type="text" class="form-control" name="directionArr[]" value="<?php echo stripslashes($directions); ?>">
													<input type="hidden" name="feq[]" value="<?php print $freq; ?>">
													<input type="hidden" name="min[]" value="<?php print $min; ?>">
												</div>
												<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
													<div class="row">
														<div class="col-md-8 col-lg-8 col-xs-8 col-sm-8" id="deltd5<?php echo $k; ?>">
															<input type="text" placeholder="" class="form-control" id="starttime<?php echo $k;?>[]" maxlength="8" name="timemedsArr[]" value="<?php echo $timemeds;?>" onClick="if(!this.value) { return displayTimeAmPm('starttime<?php echo $k;?>[]');}" onDblClick="this.select();" onBlur="saveTimeBlur(this.value,'<?php echo $parientPreOpMediOrderId;?>','<?php echo "timemeds";?>');"><!--cnvrtStrToTime(this.id,this.value);-->
														</div>	
														<div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 text-center" id="deltd4<?php echo $k; ?>">
															<a href="javascript:void(0)" class="btn btn-danger" style="margin:0" onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>','1');">  X </a>
														</div>	
													</div>
												</div>
											</div>
										</div>	   
										<div class="clearfix visible-sm margin_adjustment_only"></div>
										<div class="col-md-5 col-lg-5 col-sm-12 col-xs-12">
											<div class="row">
<?php
			if($freq>1 ){
				for($td=1;$td<$freq;$td++){
					$timeStatusArr = explode(':',$medicationStartTimeVal);
					$minsInt = $min * $td;
					$timeVar = $minsInt + substr($timeStatusArr[1],0,2);
					$timeStatus = substr($timeStatusArr[1],3);
					if($timeVar>=60)
					{
						$timeStatusArr[0]=$timeStatusArr[0]+1;
						$timeVar= $timeVar - 60;
					}
					if($timeStatusArr[0]>12)
					{
						$timeStatusArr[0]= $timeStatusArr[0]-12;
					}
					if($timeVar < 10)
					{
						$timeVar= '0'.$timeVar;
					}
					if($timeStatusArr[0]!='')
					{
						$tdTime = $timeStatusArr[0].':'.$timeVar.''.$timeStatus;
					}
					else
					{
						$tdTime=' ';
					}
					$tdNew = $td-1;	
?>
												<div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
													<input type="text" class="form-control" name="starttimeExtra<?php echo $k;?>[]" id="starttimeExtraId<?php echo $k.$tdNew?>" onClick="if(!this.value){return displayTimeAmPm('starttimeExtraId<?php echo $k.$tdNew?>');}" value="<?php  echo($timemeds1[$tdNew]); ?>" onDblClick="this.select();" onBlur="saveTimeBlur(this.value,'<?php echo $parientPreOpMediOrderId;?>','<?php echo "timemeds".$td;?>');">
												</div>
<?php
				}
			}
?>
											</div>
										</div>	   
									</div>
<?php
		}
?>
<script type="text/javascript">
	function startTimeSet(){
		var direction = document.getElementsByName("directionArr[]");
		var freq = document.getElementsByName("feq[]");
		var mins = document.getElementsByName("min[]");
		var mins2 = 0;
		var mainDiv = '';
		var changeMin = '';											
		var curHour2 = 0;										
		var startTimeValObj = document.getElementsByName("startTimeVal[]");
		var timeVal = startTimeValObj(0).value;
		var d=1;
													
		for(i=0;i<timeVal.length;i++){
			if(timeVal.charAt(i) == ':'){
				break;
			}
		}
		var startMin = ++i;
		var mainHour = timeVal.substr(0,--i);
		var curHour = '';
		var mainMins = timeVal.substr(startMin,2);
		var curMin = '';
		for(i=0;i<direction.length;i++,d++){
			var status = timeVal.substr(timeVal.length-2);
			
			if(status == 'AM'){
				changeStatus = 'PM';
			}
			else{
				changeStatus = 'AM';
			}
			curHour = mainHour;
			curMin = mainMins;
			mainDiv = freq(i).value;
			mins2 = mins(i).value;
			var fillTd = document.getElementsByName("starttime"+d+"[]");
			changeMin = curMin;
			curHour2 = curHour;
			for(u=0;u<fillTd.length;u++){
				if(u > 0){
					changeMin = parseInt(changeMin) + parseInt(mins2);
				}													
				if(changeMin >= 60){
					changeMin = changeMin - 60;
					curHour2 = ++curHour;														
				}													
				if(mainHour ==12 && curMin >00)
				{
				changeStatus=status;
				}
				else
				{
				if(u > 0){
					if(curHour2 >= 12){
						status = changeStatus;
					}
				}
				}
				if(curHour2 > 12){
					curHour2 = curHour2 - 12 ;
				}
				if(eval(curHour2) < 10){
					if(curHour2.length){
						curHour2 = "0"+curHour2.substr(1,2);
					}
					else{
						curHour2 = "0"+curHour2;
					}
				}													
				if(changeMin.length < 2 || changeMin < 10){
					if(changeMin.length){
						changeMin = "0"+changeMin.substr(1,2);
					}
					else{
						changeMin = "0"+changeMin;
					}
				}
				fillTimeVal = curHour2+":"+changeMin+" "+status;
				fillTd(u).value = fillTimeVal;
				if(u == 0){
					startTimeValObj(0).value = fillTimeVal;	
				}
			}
		}
	}
</script>
<?php
	}
?>
								</div>
<?php
	if(count($preOpPatientDetails)>0){
?>
							</div>
<?php
	}
}
?>
							<div id="newMedicationTr" class="inner_safety_wrap collapse padding_adjust_post">
								<div class="row">
									<div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
										<h4> Add Medication Below:</h4>
										<div class="row">
											<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
												<input type="text" placeholder="Medication" class="form-control" name="preOpMediOrder" id="preOpMediOrder" onClick="return showPreOpMediDiv('preOpMediOrderAreaId', '','no',findPos_X('preOpMediOrder')-5, findPos_Y('preOpMediOrder')+32)">
											</div>
											<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
												<input type="text" placeholder="Strength" class="form-control" name="strength" id="strength">
											</div>
											<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
												<input type="text" placeholder="Direction" class="form-control" name="direction" id="direction">
											</div>
											<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
												<div class="row">
													<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
														<a href="javascript:void(0)" class="btn btn-info" style="margin:0" id="saveBtn" onClick="return save_medication(1);">  Save </a>
													</div>	
												</div>
											</div>
										</div>
									</div>
								</div>      
							</div>
							
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
									<a style="margin:0;margin-top: 10px;" class="btn btn-default" data-target="#newMedicationTr" data-toggle="collapse" href="javascript:void(0)">
										<span class="fa fa-plus-circle"></span> Add New Medication 
									</a>																	
								</div>
							</div>
								
							<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix padding_0">
								<div class="clearfix border-dashed margin_adjustment_only"></div>
							</div>
							
							<div class="inner_safety_wrap">
								<div class="row">
									<div class="clearfix margin_adjustment_only"></div>
									<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
										<div class="row">
											<div class="col-md-5 col-sm-4 col-xs-12 col-lg-5" for="text_comment">
												<a class="rob alle_link show-pop-list_g4 btn btn-default margin_0" id="otherPreOp_id" onClick="return showPreMedsFn('pre_op_phy_area_id', '', 'no', parseInt($(this).offset().left), ( ($(this).offset().top -180 ) < $(document).scrollTop() ? ($(this).offset().top + 60)  : ($(this).offset().top -185) )),document.getElementById('selected_frame_name_id').value='';">
													<span class="fa fa-caret-right"></span> Other Pre-Op Orders
												</a>
											</div>
											<div class="clearfix margin_adjustment_only visible-xs"></div>
											<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7">
												<textarea id="pre_op_phy_area_id"  name="otherPreOpOrders" style="resize:none;" class="form-control"><?php echo stripslashes($laser_other_pre_medication); ?></textarea>
											</div>
										</div>
									</div>
									<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12  visible-sm visible-xs">
										<div class="clearfix margin_adjustment_only border-dashed"></div>
									</div>
									<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
										<div class="row">
											<label class="col-md-5 col-sm-4 col-xs-12 col-lg-5"> Comments </label>
											<div class="clearfix visible-xs margin_adjustment_only"></div>
											<div class="col-md-7 col-sm-8 col-xs-12 col-lg-7">
												<textarea name="comments" style="resize:none;" class="form-control"><?php echo stripslashes($laser_comments); ?></textarea>
											</div>
										</div>
									</div>
									<div class="clearfix margin_adjustment_only"></div>
								</div>
							</div>
						</div>
					<!--   @2nd col Ends     -->
					</div>
				</div>          
			</div>
		</div>
		<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
<?php
	if($laser_chk_pre_op_diagnosis){
?>
			<div class="panel panel-default bg_panel_op">
				<div class="panel-body">
					<div class="inner_safety_wrap">
						<div class="row">
							<div class="col-md-3 col-sm-4 col-xs-12 col-lg-3">
								<a class="rob alle_link show-pop-list_g5 btn btn-default" id="perop_diag_id" onClick="return showPreDefineDiagnosisFn('perop_diag_area_id', '', 'no', findPos_X('perop_diag_id'), findPos_Y('perop_diag_id')-185),document.getElementById('selected_frame_name_id').value='';">
									<span class="fa fa-caret-right"></span> Pre-Op Diagnosis
								</a>
							</div>
                            <div class="clearfix visible-xs margin_adjustment_only"></div>
							<div class="col-md-9 col-sm-8 col-xs-12 col-lg-9"> 
								<textarea class="form-control" id="perop_diag_area_id" name="txtarea_pre_op_Diagnosis" onBlur="changeTxtGroupColor(2,'pre_op_phy_area_id','perop_diag_area_id');" onFocus="changeTxtGroupColor(2,'pre_op_phy_area_id','perop_diag_area_id');" onKeyUp="changeTxtGroupColor(2,'pre_op_phy_area_id','perop_diag_area_id');" style="<?php echo $pre_opBackColor;?>"><?php echo $laser_pre_op_diagnosis;?></textarea>
								<input type="hidden" name="hiddchk_laser_pre_op_diagnosis" value="<?php echo $laser_chk_pre_op_diagnosis;?>">
							</div> 
						</div>
					</div> 
				</div>
			</div>
<?php
	}
?>
		</div>
		<!--- PANEL CLOSED -->
		<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
			<div class="panel panel-default bg_panel_op">
				<div class="panel-heading">
					<h3 class="panel-title rob"> Procedure Notes  </h3>
				</div>
				<div class="panel-body">
					<div class="inner_safety_wrap">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
								<p class="rob l_height_28">
									<label for="chk_laser_patient_evaluated">
										<input style="margin-left:4px;" type="checkbox"  value="Yes" id="chk_laser_patient_evaluated" name="chk_laser_patient_evaluated" tabindex="7" <?php if($chk_laser_patient_evaluated =='Yes') { echo 'CHECKED'; } ?> /> Patient in satisfactory condition for proposed laser procedure
									</label>
								</p>		
							</div>
							<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
								<div class="clearfix border-dashed margin_adjustment_only"></div>	
							</div>
						</div>
<?php
	$procedureNoteBackColor=$chngBckGroundColor;
	if($prelaserVitalSignBP || $prelaserVitalSignP || $prelaserVitalSignR || $laser_spot_duration_detail || $laser_spot_size_detail || $laser_power_detail || $laser_shots_detail || $laser_total_energy_detail || $laser_degree_of_opening_detail || $laser_exposure_detail || $laser_count_detail || $postlaserVitalSignBP || $postlaserVitalSignP || $postlaserVitalSignR || $laser_post_progress_detail || $laser_post_operative_detail ){
		$procedureNoteBackColor=$whiteBckGroundColor;
	}
	
	$calculatorTopValue3 = "540";
	$calculatorTopChangeValue3 = "15";
	if($hearingAids=="Yes"){$calculatorTopChangeValue3 = $calculatorTopChangeValue3+20;}
	if($denture=="Yes"){$calculatorTopChangeValue3 = $calculatorTopChangeValue3+20;}
	$calculatorWeightSetValue3 = $calculatorTopValue3+$calculatorTopChangeValue3;
	
	$bpPrTempTopValue3 = "20";
	$calculatorBP_P_R_Temp_SetValue3 = $bpPrTempTopValue3+$calculatorTopValue3+$calculatorTopChangeValue3;
	
	$pre_operative_commentValue3 = "138";
	$pre_operative_commentSetValue = $pre_operative_commentValue3+$calculatorTopValue3+$calculatorTopChangeValue3;
?>	
						<div class="row">
							<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 wrap_right_inner_anesth">
								<div class="row">
									<div class="col-md-12 col-sm-3 col-xs-12 col-lg-3 text-left">
										<label class="f_size rob"><b>Pre­Laser Vital Signs</b></label>   
									</div>
									<div class="visible-md col-md-12">
										<div class="margin_adjustment_only clearfix border-dashed full_width padding_15"></div>	
									</div>
									<div class="col-md-12 col-sm-9 col-xs-12 col-lg-9">
										<div class="row">
											<div class="col-md-3 col-sm-4 col-xs-6 col-lg-3">
												<div class="row">
													<label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 f_size rob padding_0 text-right">BP</label>
													<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
														<input type="text" class="form-control" id="bp_temp5" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');displayText5=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp5'))-128,parseInt(findPos_X('bp_temp5'))-176,'flag5');" name="txt_vitalSignBp_pre" value="<?php echo $prelaserVitalSignBP;?>" maxlength="7" style="<?php echo $procedureNoteBackColor;?>">  
													</div>
												</div>
											</div>
											<div class="col-md-9 col-sm-8 col-xs-6 col-lg-9 padding_0">
												<div class="row">
													<div class="row col-md-4 col-sm-4 col-xs-4 col-lg-4 padding_0">
														<div class="row">
															<label class="col-md-5 col-sm-6 col-xs-6 col-lg-5 f_size rob padding_0 text-right">P</label>
															<div class="col-md-7 col-sm-6 col-xs-6 col-lg-7">
																<input type="text" class="form-control" id="bp_temp6" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');displayText6=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp6'))-128,parseInt(findPos_X('bp_temp6'))-176,'flag6');" name="txt_vitalSignP_pre" value="<?php echo $prelaserVitalSignP;?>" maxlength="3" style="<?php echo $procedureNoteBackColor;?>">
															</div>
														</div>
													</div>
													<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 padding_0">
														<div class="row">
															<label class="col-md-5 col-sm-6 col-xs-6 col-lg-5 f_size rob padding_0 text-right">R</label>
															<div class="col-md-7 col-sm-6 col-xs-6 col-lg-7">
																<input type="text" class="form-control" id="bp_temp7" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');displayText=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp7'))-128,parseInt(findPos_X('bp_temp7'))+40,'flag7');" name="txt_vitalSignR_pre" value="<?php echo $prelaserVitalSignR;?>" maxlength="3" style="<?php echo $procedureNoteBackColor;?>">
															</div>
														</div>
													</div>	
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 padding_0">
														<div class="row">
															<label class="col-md-5 col-sm-6 col-xs-6 col-lg-5 f_size rob padding_0 text-right">Time</label>
															<div class="col-md-7 col-sm-6 col-xs-6 col-lg-7">
																<input type="text" class="form-control" id="bp_temp13" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');displayText13=this.value" onClick="displayTimeAmPm('bp_temp13');getShowNewPos(parseInt(findPos_Y('bp_temp13'))-128,parseInt(findPos_X('bp_temp13'))+40,'flag13');" name="txt_vitalSignTime_pre" value="<?php echo $prelaserVitalSignTime;?>" style=" <?php echo $procedureNoteBackColor;?>" >
															</div>
														</div>
													</div>
                                                    
												</div>												
											</div>
										</div>
									</div>
								</div>
							</div>
<?php				
	$iolPreBackColor=$chngBckGroundColor;
	if($pre_laser_IOP_R || $pre_laser_IOP_L || $pre_iop_na ){
		$iolPreBackColor=$whiteBckGroundColor; 
	}
?>
							<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 wrap_right_inner_anesth">
								<div class="row">
									<div class="col-md-12 col-sm-4 col-xs-12 col-lg-4 text-left">
										<label class="f_size rob"><b> Pre­Laser IOP  </b></label>   
									</div>
									<div class="visible-md col-md-12">
										<div class=" margin_adjustment_only  clearfix border-dashed full_width padding_15"></div>
									</div>
									<div class="col-md-12 col-sm-8 col-xs-8 col-lg-8">
										<div class="row">
											<div class="col-md-4 col-sm-6 col-xs-6 col-lg-4">
												<div class="row">
													<label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 f_size rob padding_0 text-right">R</label>
													<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
														<input type="text" class="form-control" id="bp_temp3" onFocus="changeDiffChbxColor(3,'bp_temp3','bp_temp4','pre_iop_na');" onBlur="changeDiffChbxColor(3,'bp_temp3','bp_temp4','pre_iop_na');" onKeyUp="changeDiffChbxColor(3,'bp_temp3','bp_temp4','pre_iop_na');displayText3=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp3'))-128,parseInt(findPos_X('bp_temp3'))-176,'flag3');" name="pre_laser_IOP_R" value="<?php if($pre_iop_na=='Yes'){echo '';}else{echo $pre_laser_IOP_R;} ?>" maxlength="7" style="<?php echo $iolPreBackColor;?>" />
													</div>
												</div>
											</div>
											<div class="col-md-8 col-sm-6 col-xs-6 col-lg-8">
												<div class="row">
													<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
														<div class="row">
															<label class="col-md-5 col-sm-6 col-xs-6 col-lg-5 f_size rob padding_0 text-right">L</label>   
															<div class="col-md-7 col-sm-6 col-xs-6 col-lg-7">
																<input type="text" class="form-control" id="bp_temp4" onFocus="changeDiffChbxColor(3,'bp_temp3','bp_temp4','pre_iop_na');" onBlur="changeDiffChbxColor(3,'bp_temp3','bp_temp4','pre_iop_na');" onKeyUp="changeDiffChbxColor(3,'bp_temp3','bp_temp4','pre_iop_na');displayText4=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp4'))-128,parseInt(findPos_X('bp_temp4'))-176,'flag4');" name="pre_laser_IOP_L" value="<?php if($pre_iop_na=='Yes'){echo '';}else{echo $pre_laser_IOP_L;} ?>" maxlength="7" style="<?php echo $iolPreBackColor;?>" />
															</div>
														 </div>
													</div>
													<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
														<label for="pre_iop_na">
															<span class="colorChkBx" style="<?php echo $iolPreBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(3,'bp_temp3','bp_temp4','pre_iop_na');" <?php if($pre_iop_na=='Yes'){echo 'CHECKED';} ?> type="checkbox" value="Yes" id="pre_iop_na" name="pre_iop_na" tabindex="7" /></span> N/A
														</label>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="clearfix margin_adjustment_only"></div>
							
							<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 wrap_right_inner_anesth">
								<div class="full_width">
									<?php
									if( ($laser_chk_spot_duration=='on') || ($laser_chk_spot_size=='on' ) || ($laser_chk_power=='on') || ($laser_chk_shots=='on')  || ($laser_chk_total_energy=='on') || ($laser_chk_degree_of_opening=='on') || ($laser_chk_exposure=='on') || ($laser_chk_count=='on') || ($laser_chk_procedure_image) ) {
									?>
                                    <div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<p class="rob l_height_2">Laser Notes for <?php echo $laser_patientConfirmSiteName;?></p>
										</div>
									</div>
                                    
									<div class="clearfix margin_adjustment_only"></div>
                                    <?PHP } ?>
<?php

	if($laser_chk_spot_duration=='on'){	
?>
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
											<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="spot_duration_id" onClick="return showspot_duration('txtarea_spot_duration', '', 'no', parseInt(findPos_X('spot_duration_id')), parseInt(findPos_Y('spot_duration_id'))-420),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Spot Duration
											</a>
										</div>
										<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9"> 
											<textarea class="form-control" id="txtarea_spot_duration" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_spot_duration" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_spot_duration_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_spot_duration" value="<?php echo $laser_chk_spot_duration;?>">
										</div>    
									</div>
									<div class="clearfix margin_adjustment_only"></div>
<?php
	}
	
	if($laser_chk_spot_size=='on'){
?>
									<!-- spot size -->
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
											<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="spot_size_id" onClick="return showspot_size('txtarea_spot_size', '', 'no', parseInt(findPos_X('spot_size_id')), parseInt(findPos_Y('spot_size_id'))-420),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Spot Size
											</a>
										</div>
										<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9"> 
											<textarea class="form-control" id="txtarea_spot_size" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_spot_size" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_spot_size_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_spot_size" value="<?php echo $laser_chk_spot_size;?>">	
										</div>
									</div>
									<div class="clearfix margin_adjustment_only"></div>
<?php
	}
	
	if($laser_chk_power=='on'){
?>
									<!-- Power/Wattage -->
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
											<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="power_id" onClick="return showpower('txtarea_power', '', 'no', parseInt(findPos_X('power_id')), parseInt(findPos_Y('power_id'))-420),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Power
											</a>
										</div>
										<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9"> 
											<textarea class="form-control" id="txtarea_power" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_power" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_power_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_power" value="<?php echo $laser_chk_power;?>">
										</div>
									</div>
									<div class="clearfix margin_adjustment_only"></div>
<?php
	}
	
	if($laser_chk_shots=='on'){
?>
									<!-- # of Shots -->
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
											<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="shots_id" onClick="return showshots('txtarea_shots', '', 'no', parseInt(findPos_X('shots_id')), parseInt(findPos_Y('shots_id'))-420),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> # of Shots
											</a>
										</div>
										<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9"> 
											<textarea class="form-control" id="txtarea_shots" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_shots" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_shots_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_shots" value="<?php echo $laser_chk_shots;?>">
										</div>
									</div>
									<div class="clearfix margin_adjustment_only"></div>
<?php
	}
	
	if($laser_chk_total_energy=='on'){
		
?>
									<!-- Total Energy -->
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
											<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="total_energy_id" onClick="return showtotal_energy('txtarea_total_energy', '', 'no', parseInt(findPos_X('total_energy_id')), parseInt(findPos_Y('total_energy_id'))-420),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Total Energy
											</a>
										</div>
										<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9"> 
											<textarea class="form-control" id="txtarea_total_energy" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_total_energy" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_total_energy_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_total_energy" value="<?php echo $laser_chk_total_energy;?>">
										</div>
									</div>
									<div class="clearfix margin_adjustment_only"></div>
<?php
	}
	
	if($laser_chk_degree_of_opening=='on'){
?>
									<!--Degree of opening-->
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
											<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="degree_of_opening_id" onClick="return showdegree_of_opening('txtarea_degree_of_opening', '', 'no', parseInt(findPos_X('degree_of_opening_id')), parseInt(findPos_Y('degree_of_opening_id'))-420),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Degree of opening
											</a>
										</div>
										<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9"> 
											<textarea class="form-control" id="txtarea_degree_of_opening" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_degree_of_opening" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_degree_of_opening_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_degree_of_opening" value="<?php echo $laser_chk_degree_of_opening;?>">
										</div>
									</div>
									<div class="clearfix margin_adjustment_only"></div>
<?php
	}
	if($laser_chk_exposure=='on'){
?>
									<!-- exposure -->
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
											<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="exposure_id" onClick="return showexposure('txtarea_exposure', '', 'no', parseInt(findPos_X('exposure_id')), parseInt(findPos_Y('exposure_id'))-420),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Exposure
											</a>
										</div>
										<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9"> 
											<textarea class="form-control" id="txtarea_exposure" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_exposure" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_exposure_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_spot_exposure" value="<?php echo $laser_chk_exposure;?>">
										</div>
									</div>
									<div class="clearfix margin_adjustment_only"></div>
<?php
	}
	if($laser_chk_count=='on'){
?>
									<!-- count -->
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
											<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="count_id" onClick="return showcount('txtarea_count', '', 'no', parseInt(findPos_X('count_id')), parseInt(findPos_Y('count_id'))-420),document.getElementById('selected_frame_name_id').value='';">
												<span class="fa fa-caret-right"></span> Count
											</a>
										</div>
										<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9">
											<textarea class="form-control" id="txtarea_count" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_count" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_count_detail;?></textarea>
											<input type="hidden" name="hiddchk_laser_count" value="<?php echo $laser_chk_count;?>">
										</div>
									</div>
									<div class="clearfix margin_adjustment_only"></div>
<?php
	}
?>
								</div>
							</div>
							<!--- Drawing Portion -->
							<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
								<!--- Start Printing Discharge details -->
								<?php 
								if($version_num > 1) {	
									$patientRelDisabled = $discharge_home?false:true;
									$patientRelOtherDisabled = $discharge_home && $patients_relation == 'other'?false:true;
								?>
                                    <div class="row">
                                        <div class="col-xs-12 col-lg-7">
                                            <label for="discharge_home" onclick="javascript:checkSingle('discharge_home','discharge_home');">
                                                <span><input type="checkbox" value="1" id="discharge_home" name="discharge_home" onChange="javascript:chkRelationFlds(this);" <?php echo $discharge_home?'checked':'';?>></span> Patient Discharged to Home With
                                            </label>
    
                                            <div class="clearfix margin_adjustment_only"></div>
    
                                            <div class="row">
                                                <div class="col-xs-7 ">
                                                    <div class="input-group">
                                                        <label class="input-group-addon">Relationship</label>
                                                        <Select name="patients_relation" class="form-control minimal" id="patients_relation" <?php echo $patientRelDisabled?'disabled':'';?> onChange="javascript:chkRelationOther(this);"> 
                                                            <option value="Self" <?php if($patients_relation=="Self") { echo "selected";}?>>Self</option>
                                                            <option value="Family" <?php if($patients_relation=="Family") { echo "selected";}?>>Family</option>
                                                            <option value="Husband" <?php if($patients_relation=="Husband") { echo "selected";}?>>Husband</option>
                                                            <option value="Wife" <?php if($patients_relation=="Wife") { echo "selected";}?>>Wife</option>
                                                            <option value="Son" <?php if($patients_relation=="Son") { echo "selected";}?>>Son</option>
                                                            <option value="Daughter" <?php if($patients_relation=="Daughter") { echo "selected";}?>>Daughter</option>
                                                            <option value="Sister" <?php if($patients_relation=="Sister") { echo "selected";}?>>Sister</option>
                                                            <option value="Brother" <?php if($patients_relation=="Brother") { echo "selected";}?>>Brother</option>
                                                            <option value="Mother" <?php if($patients_relation=="Mother") { echo "selected";}?>>Mother</option>
                                                            <option value="Father" <?php if($patients_relation=="Father") { echo "selected";}?>>Father</option>
                                                            <option value="Friend" <?php if($patients_relation=="Friend") { echo "selected";}?>>Friend</option>
                                                            <option value="Transportation Driver" <?php if($patients_relation=="Transportation Driver") { echo "selected";}?>>Transportation Driver</option>
                                                            <option value="other" <?php if($patients_relation=="other") { echo "selected";}?>>Other</option>
                                                        </Select>
                                                    </div>
                                                </div>	
    
                                                <div class="col-xs-5">
                                                    <input class="form-control" id="patients_relation_other" name="patients_relation_other" placeholder="Other" type="text" <?php echo $patientRelOtherDisabled?'disabled':'';?> value="<?php echo $patients_relation_other; ?>" />
                                                </div>
    
                                            </div>	
                                        </div>
    
                                        <div class="col-xs-12 col-lg-5">
                                            
                                            <label for="patient_transfer" onclick="javascript:checkSingle('patient_transfer','patient_transfer');">
                                                <span><input type="checkbox" value="1" id="patient_transfer" name="patient_transfer" <?php echo $patient_transfer?'checked':'';?>></span> Patient Transferred to Hospital
                                            </label>
                                            <div class="clearfix margin_adjustment_only"></div>	
    
                                            <div class="col-xs-10 pull-right ">
                                                <div class="input-group">
                                                    <label class="input-group-addon" for="bp_temp19">Discharge Time</label>
                                                    <input type="text" class="form-control" id="bp_temp19" onClick="if(!this.value){ return displayTimeAmPm('bp_temp19');} getShowNewPos(parseInt(findPos_Y('bp_temp19'))-160,parseInt(findPos_X('bp_temp19')),'flag19');" name="discharge_time" value="<?php echo $discharge_time;?>" />
                                                </div>		
                                            </div>
    
                                        </div>
                                        
                                        <div class="clearfix margin_adjustment_only">&nbsp;</div>
                                    </div>
                                    <div class="clearfix margin_adjustment_only"></div>
                                    <!--- End Printing Discharge details -->	
<?php 
								}
								if($laser_chk_procedure_image){
?>
								<div class="well well-lg">
									<td style="width:100%;" class="valignTop">
										<table class="table_collapse">
											<tr>
												<td class="nowrap alignCenter" onMouseOut="get_App_Coords_Laser('app_Laser_Image');">
													<?php 
													$srgryDir="surgerycenter";
													$pathTmp = $_SERVER['PHP_SELF'];
													if($pathTmp) {
														$pathTmpExplode = explode('/',$pathTmp);
														$srgryDir = $pathTmpExplode[1];
													}
													?>
													<input type="hidden" name="laserProcedure_image" id="laserProcedure_image" value="<?php echo $laser_procedure_image ; ?>">
													
													<?php 
													 if($sect=="print_emr") {
														 require_once("html2pdfnew/imgGdLaser.php");
														 drawOnImageLaser($laser_procedure_image,$imgName,'laserProcedure_Image.jpg');
														 echo '<img src="html2pdfnew/laserProcedure_Image.jpg" width="300" height="245">';
													 }else if($isHTML5OK){
														 include_once('drawing/drawing.php');
													 }else {?>
														<object type="application/x-java-applet" name="app_Laser_Image" id="app_Laser_Image" width="425" height="200">
														  <param name="code" value="MyCanvasColored.class" />
														  <param name="codebase" value="common/applet/" />
														  <param name="bgImage" value="<?php echo '/'.$surgeryCenterDirectoryName;?>/images/laser_image.jpg" />
														  <param name="strpixls" value="<?php echo $laser_procedure_image ;?>" />
														  <param name="mode" value="edit" />
														  <param name="archive" value="DrawApplet.jar" />
														</object>
													<?php
														
													 }
													?>    
												</td>
												<td class="valignTop">
													<?php
													if(!$isHTML5OK){?>
													<table class="table_separate" style="width:15px; border-spacing:1px;">
														<tr>
															<td style="cursor:pointer; background:#FF0000; height:10px;" onClick="changeColorLaser(255,0,0,'app_Laser_Image')"></td>
														</tr>
														<tr>	
															<td style="cursor:pointer; background-color:#FFFF00; height:10px;" onClick="changeColorLaser(255,255,0,'app_Laser_Image')"></td>
														</tr>
														<tr>		
															<td style="cursor:pointer; background-color:#008000; height:10px;" onClick="changeColorLaser(0,128,0,'app_Laser_Image')"></td>
														</tr>
														<tr>		
															<td style="cursor:pointer; background-color:#800000; height:10px;" onClick="changeColorLaser(128,0,0,'app_Laser_Image')"></td>
														</tr>
														<tr>		
															<td style="cursor:pointer; background-color:#0000FF; height:10px;" onClick="changeColorLaser(0,0,255,'app_Laser_Image')"></td>													
														</tr>
														<tr>		
															<td style="cursor:pointer; background-color:#000000; height:10px;" onClick="changeColorLaser(0,0,0,'app_Laser_Image')"></td>													
														</tr>
														<tr>		
															<td style="cursor:pointer; background-color:#808080; height:10px;" onClick="changeColorLaser(128,128,128,'app_Laser_Image')"></td>													
														</tr>
														<tr>		
															<td style="cursor:pointer; background-color:#FF00FF; height:10px;" onClick="changeColorLaser(255,0,255,'app_Laser_Image')"></td>													
														</tr>
														<tr>		
															<td style="cursor:pointer; background-color:#00FF00; height:10px;" onClick="changeColorLaser(0,255,0,'app_Laser_Image')"></td>													
														</tr>
														<tr>		
															<td style="cursor:pointer; background-color:#00FFFF; height:10px;" onClick="changeColorLaser(0,255,255,'app_Laser_Image')"></td>													
														</tr>
														<tr><td style="height:60px;"></td></tr>
													</table>
													<table class="table_collapse">
														<tr>
															<td class="valignBottom"><img src="images/eraser.gif" onClick="return getclear_Laser('app_Laser_Image');"></td>	
														</tr>	
													</table>
													<?php
													}?>    
												</td>	
											</tr>
										</table>
									</td>
									<?php if($laser_chk_spot_duration=="" && $laser_chk_spot_size=="" && $laser_chk_power=="" && $laser_chk_shots=="" && $laser_chk_total_energy=="" && $laser_chk_degree_of_opening=="" && $laser_chk_exposure=="" && $laser_chk_count==""){?>	
									<td style="width:100%;">&nbsp;</td>
									<?php }
										else { ?>
									<td style="width:100%;">&nbsp;</td>
									<?php } ?>
								</div>
								
								<style type="text/css">
									.cCanvas{position: relative;}
								</style>
<?php
								}
?>
							</div>
							
							<div class="clearfix margin_adjustment_only"></div>
<?php 
	$calculatorTopValue1 = "1015";
	$calculatorTopChangeValue1 = "15";
	if($hearingAids=="Yes"){$calculatorTopChangeValue1 = $calculatorTopChangeValue1+20;}
	if($denture=="Yes"){$calculatorTopChangeValue1 = $calculatorTopChangeValue1+20;}
	$calculatorWeightSetValue1 = $calculatorTopValue1+$calculatorTopChangeValue1;
	$bpPrTempTopValue1 = "20";
	$calculatorBP_P_R_Temp_SetValue1 = $bpPrTempTopValue1+$calculatorTopValue1+$calculatorTopChangeValue1;
	
	$pre_operative_commentValue1 = "138";
	$pre_operative_commentSetValue11 = $pre_operative_commentValue1+$calculatorTopValue1+$calculatorTopChangeValue1;
?>
							<div class="full_width">
								<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 wrap_right_inner_anesth">
									<div class="row">
										<div class="col-md-12 col-sm-3 col-xs-12 col-lg-3 text-left">
											<label class="f_size rob"><b>Post­Laser Vital Signs</b></label>   
										</div>
										<div class="visible-md col-md-12">
											<div class=" margin_adjustment_only  clearfix border-dashed full_width padding_15"></div>
										</div>
										<div class="col-md-12 col-sm-9 col-xs-12 col-lg-9">
											<div class="row">
												<div class="col-md-3 col-sm-4 col-xs-6 col-lg-3">
													<div class="row">
													   <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 f_size rob padding_0 text-right"> BP </label>
														<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
															<input type="text" class="form-control" id="bp_temp8" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txt_vitalSignBp_post" value="<?php echo $postlaserVitalSignBP;?>" maxlength="7" style="<?php echo $procedureNoteBackColor;?>" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');displayText8=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp8')),parseInt(findPos_X('bp_temp8'))-200,'flag8');">  
														</div>
													 </div>
												</div>
												<div class="col-md-9 col-sm-8 col-xs-8 col-lg-9 padding_0">
													<div class="row">
														<div class="row col-md-4 col-sm-4 col-xs-4 col-lg-4 padding_0">
															<div class="row">
															   <label class="col-md-5 col-sm-6 col-xs-6 col-lg-5  f_size rob padding_0 text-right">P</label>   
																<div class="col-md-7 col-sm-6 col-xs-6 col-lg-7">
																	<input type="text" class="form-control" id="bp_temp9" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txt_vitalSignP_post" value="<?php echo $postlaserVitalSignP;?>" maxlength="3" style="<?php echo $procedureNoteBackColor;?>" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');displayText9=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp9')),parseInt(findPos_X('bp_temp9'))-190,'flag9');">  
																</div>
															 </div>    	
														</div>
														<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 padding_0">
															<div class="row">
																<label class="col-md-5 col-sm-6 col-xs-6 col-lg-5  f_size rob padding_0 text-right">R</label>
																<div class="col-md-7 col-sm-6 col-xs-6 col-lg-7">
																	<input type="text" class="form-control" id="bp_temp10" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txt_vitalSignR_post" value="<?php echo $postlaserVitalSignR;?>" maxlength="3" style="<?php echo $procedureNoteBackColor;?>" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');displayText10=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp10')),parseInt(findPos_X('bp_temp10'))-190,'flag10');">  
																</div>
															</div>
														</div>
                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 padding_0">
															<div class="row">
																<label class="col-md-5 col-sm-6 col-xs-6 col-lg-5  f_size rob padding_0 text-right">Time</label>
																<div class="col-md-7 col-sm-6 col-xs-6 col-lg-7">
																	<input type="text" class="form-control" id="bp_temp14" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txt_vitalSignTime_post" value="<?php echo $postlaserVitalSignTime;?>" style="<?php echo $procedureNoteBackColor;?>" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');displayText14=this.value" onClick="displayTimeAmPm('bp_temp14');getShowNewPos(parseInt(findPos_Y('bp_temp14')),parseInt(findPos_X('bp_temp14'))-190,'flag14');">  
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
<?php				
	$iolPressureBackColor=$chngBckGroundColor;
	if($iop_pressure_r || $iop_pressure_l || $iop_na ){
		 $iolPressureBackColor=$whiteBckGroundColor; 
	}
?>
								<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 wrap_right_inner_anesth">
									<div class="row">
										<div class="col-md-12 col-sm-4 col-xs-12 col-lg-4 text-left">
											<label class="f_size rob"><b> IOP  Pressure </b></label>   
										</div>
										<div class="visible-md col-md-12">
											<div class=" margin_adjustment_only  clearfix border-dashed full_width padding_15"></div>
										</div>
										<div class="col-md-12 col-sm-8 col-xs-8 col-lg-8">
											<div class="row">
												<div class="col-md-4 col-sm-6 col-xs-6 col-lg-4">
													<div class="row">
													   <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4 f_size rob padding_0 text-right">R</label>   
														<div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
															<input type="text" class="form-control" id="bp_temp23" name="iop_pressure_r" value="<?php if($iop_na=='Yes') { echo ''; } else { echo $iop_pressure_r; }?>" maxlength="7" style="<?php echo $iolPressureBackColor;?>" onFocus="changeDiffChbxColor(3,'bp_temp23','bp_temp24','iop_pressure_na');" onBlur="changeDiffChbxColor(3,'bp_temp23','bp_temp24','iop_pressure_na');" onKeyUp="changeDiffChbxColor(3,'bp_temp23','bp_temp24','iop_pressure_na');displayText23=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp23')),parseInt(findPos_X('bp_temp23'))-200,'flag23');">  
														</div>
													</div>
												</div>
												<div class="col-md-8 col-sm-6 col-xs-6 col-lg-8">
													<div class="row">
														<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
															<div class="row">
																<label class="col-md-5 col-sm-6 col-xs-6 col-lg-5  f_size rob padding_0 text-right">L</label>
																<div class="col-md-7 col-sm-6 col-xs-6 col-lg-7">
																	<input type="text" class="form-control" id="bp_temp24" name="iop_pressure_l" value="<?php if($iop_na=='Yes') { echo ''; } else {echo $iop_pressure_l;}?>" maxlength="7" style="<?php echo $iolPressureBackColor;?>" onFocus="changeDiffChbxColor(3,'bp_temp23','bp_temp24','iop_pressure_na');" onBlur="changeDiffChbxColor(3,'bp_temp23','bp_temp24','iop_pressure_na');" onKeyUp="changeDiffChbxColor(3,'bp_temp23','bp_temp24','iop_pressure_na');displayText24=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp24')),parseInt(findPos_X('bp_temp24'))-200,'flag24');">  
																</div>
															</div>    	
														</div>
														<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
															<label>
																<span class="colorChkBx" style="  <?php echo $iolPressureBackColor;?>"><input type="checkbox" <?php if($iop_na=='Yes') { echo 'CHECKED'; } ?> value="Yes" id="iop_pressure_na" name="iop_pressure_na" tabindex="7" onClick="changeDiffChbxColor(3,'bp_temp23','bp_temp24','iop_pressure_na');"></span> N/A</label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="clearfix margin_adjustment_only"></div>
							<div class="full_width">
								<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
									<div class="full_width wrap_right_inner_anesth">
										<div class="row">
                                        	<?php
												$defaultPostOpOrder	=	'';
												if( $laser_post_progress_detail == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
												{
													$defaultPostOpOrder	= $objManageData->getDefault('laserpredefine_postprogressnotes_tbl','name');
													$laser_post_progress_detail = $defaultPostOpOrder;
												}
											
											?>
											<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
												<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="post_progressnote_id" onClick="return showPost_ProgressNote('txtarea_Post_ProgressNote', '', 'no', parseInt(findPos_X('post_progressnote_id')), parseInt(findPos_Y('post_progressnote_id'))-420),document.getElementById('selected_frame_name_id').value='';">
													<span class="fa fa-caret-right"></span> Post Op Order
												</a>
											</div>
											<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9"> 
												<textarea class="form-control" id="txtarea_Post_ProgressNote" onBlur="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onFocus="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" onKeyUp="changeTxtGroupColor(17,'bp_temp5','bp_temp6','bp_temp7','txtarea_spot_duration','txtarea_spot_size','txtarea_power','txtarea_shots','txtarea_total_energy','txtarea_degree_of_opening','txtarea_exposure','txtarea_count','bp_temp8','bp_temp9','bp_temp10','txtarea_Post_ProgressNote','bp_temp13','bp_temp14');" name="txtarea_Post_ProgressNote" style="<?php echo $procedureNoteBackColor;?>"><?php echo $laser_post_progress_detail;?></textarea>
												<input type="hidden" name="hiddchk_laser_post_progress" value="<?php echo $laser_chk_post_progress;?>">
											</div>    
										</div>
									</div>
									<div class="margin_small_only"></div> 
									<div class="full_width wrap_right_inner_anesth">
<?php
	if($laser_chk_post_operative){
?>
										<div class="row">
                                        	<?php
												$defaultProgressNotes	=	'';
												if( $laser_post_operative_detail == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
												{
													$defaultProgressNotes	= $objManageData->getDefault('laserpredefine_postoperativestatus_tbl','name');
													$laser_post_operative_detail = $defaultProgressNotes;
												}
											
											?>
                                            
											<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3">
												<a class="margin_0 rob alle_link show-pop-list_g5 f_size btn btn-default" id="post_operative_status_id" onClick="return showpost_operative_status('txtarea_Post_Operative_Status', '', 'no', parseInt(findPos_X('post_operative_status_id')), parseInt(findPos_Y('post_operative_status_id'))-420),document.getElementById('selected_frame_name_id').value='';">
													<span class="fa fa-caret-right"></span> Progress Note
												</a>
											</div>
											<div class="col-md-9 col-sm-6 col-xs-6 col-lg-9">
												<textarea class="form-control" id="txtarea_Post_Operative_Status" name="txtarea_Post_Operative_Status"><?php echo $laser_post_operative_detail;?></textarea>
												<input type="hidden" name="hiddchk_laser_post_operative" value="<?php echo $laser_chk_post_operative;?>">
											</div>
										</div>
<?php
	}
?>
									</div>
								</div>
								<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
									
										<div class="row">
											<div for="text_comment" class="col-md-2 col-sm-12 col-xs-12 col-lg-2 text-left ">
												<label > Comments </label>
											</div>
											<div class="clearfix visible-sm margin_adjustment_only"></div>
											<div class="col-md-10 col-sm-12 col-xs-12 col-lg-10">
												<textarea class="form-control" style="resize:none;" id="post_op_comments" name="post_op_comments"><?php echo $post_comment;?></textarea>
											</div>
										</div>
									
								</div>	
							</div>
						</div>
					</div>
				</div>
			</div>  
		</div><!-- Panel Closed   -->

<?php
//CODE RELATED TO SURGEON SIGNATURE ON FILE
$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 

$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
$loggedInUserType = $ViewUserNameRow["user_type"];
$loggedInSignatureOfUser = $ViewUserNameRow["signature"];

if($loggedInUserType<>"Surgeon") {
	$loginUserName = $_SESSION['loginUserName'];
	$callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
}else {
	$loginUserId = $_SESSION["loginUserId"];
	$callJavaFunSurgeon = "document.frm_laser_procedure.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','laser_procedure_ajaxSign.php','$loginUserId','Surgeon1');";
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
	$signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signSurgeon1DateTime);
}
if($_SESSION["loginUserId"]==$signSurgeon1Id) {
	$callJavaFunSurgeonDel = "document.frm_laser_procedure.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','laser_procedure_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
}else {
	$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
}
//END CODE RELATED TO SURGEON SIGNATURE ON FILE

//CODE RELATED TO NURSE SIGNATURE ON FILE
	if($loggedInUserType<>"Nurse") {
		$loginUserName = $_SESSION['loginUserName'];
		$callJavaFun = "return noAuthorityFunCommon('Nurse');";
	}else {
		$loginUserId = $_SESSION["loginUserId"];
		$callJavaFun = "document.frm_laser_procedure.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','laser_procedure_ajaxSign.php','$loginUserId','Nurse1');";
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
		$signNurseDateTimeFormatNew = $objManageData->getFullDtTmFormat($signNurseDateTime);
	}
	if($_SESSION["loginUserId"]==$signNurseId) {
		$callJavaFunDel = "document.frm_laser_procedure.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','laser_procedure_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	}else {
		$callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
	}

//END CODE RELATED TO NURSE SIGNATURE ON FILE
	$surgeonSignBackColor=$chngBckGroundColor;
	if($signSurgeon1Id!=0){
		$surgeonSignBackColor==$whiteBckGroundColor; 
	}
	$nurseSignBackColor=$chngBckGroundColor;
	if($signNurseId!=0){
		$nurseSignBackColor==$whiteBckGroundColor; 
	}
?>

		<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
			<div  class="panel panel-default">
				<div class="panel-body">
					<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                        <div class="inner_safety_wrap" id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>;">
                            <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $surgeonSignBackColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
                        </div>
                        <div class="inner_safety_wrap collapse" id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>;">
                            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>". " Dr. ".$Surgeon1Name; ?>  </a></span>	     
                            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $surgeon1SignOnFileStatus;?></span>
                            <span class="rob full_width"> <b> Signature Date</b> <?php echo $signSurgeon1DateTimeFormatNew;?></span>
                        </div>
					</div>
				
					<div class="clearfix amrgin_adjustment_only visible-sm visible-xs "></div>
					
					<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
						
                        <div class="inner_safety_wrap" id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>;">
                            <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBackColor?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
                        </div>
                        <div class="inner_safety_wrap collapse" id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>;">
                            <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b> ". $NurseNameShow; ?>  </a></span>	     
                            <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
                            <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signNurseDateTimeFormatNew; ?> <span class="fa fa-edit"></span></span></span>
                        </div>
						
						
							
                        
                        
                        <!--<div class="inner_safety_wrap" id="TDnurseNameId">
							<a style="display:<?php echo $TDnurseNameIdDisplay; ?>" <?php if(!$signNoToggle_Nurse){ ?>data-target="#TDnurseSignatureId" data-toggle="collapse" <?php } ?>class="sign_link collapsed" href="javascript:void(0);" onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</a>
						</div>
						<div id="TDnurseSignatureId" class="inner_safety_wrap collapse <?php echo $TDnurseSignatureIdDisplay; ?>">
							<span class="sign_link rob full_width" onClick="javascript:<?php echo $callJavaFunDel;?>">Nurse: <?php echo $NurseNameShow; ?>  </span>	     
							<span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus; ?></span>
							<span class="rob full_width"> <b> Signature Date</b> <?php echo $signNurseDateTimeFormatNew; ?></span>
						</div>-->
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>                         
</form>

<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" id="frm_return_BlankMainForm" method="post" action="laser_procedure.php?cancelRecord=true">
	<input type="hidden" name="patient_id"	 value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId"		 value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId"		 value="<?php echo $ascId; ?>">
	<input type="hidden" name="innerKey"	 value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor"	 value="<?php echo $preColor; ?>">
	<input type="hidden" name="pConfId"		 value="<?php echo $pConfId; ?>">
	<input type="hidden" name="thisId"		 value="<?php echo $thisId; ?>">	

</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "laser_procedure.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM
	if($finalizeStatus!='true'){
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
	if($SaveForm_alert == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'inline-block';
	</script>
	<?php
}
?>
<script>
	var primary_procedure_cat="<?php echo $patient_laser_consent_categoryID;?>";
	if(primary_procedure_cat==2){
	//SET BP, P, R, TEMP VALUES IN HEADER
		top.document.getElementById('header_BP').innerText=document.getElementById('bp_temp5').value;
		top.document.getElementById('header_P').innerText=document.getElementById('bp_temp6').value;
		top.document.getElementById('header_R').innerText=document.getElementById('bp_temp7').value;
		//top.document.getElementById('header_O2SAT').innerText=document.getElementById('O2SAT').value;
		//top.document.getElementById('header_Temp').innerText=document.getElementById('temp').value;
		
	//SET BP, P, R, TEMP VALUES IN HEADER
	}
</script>

<?php 
	include("pre_op_meds_div.php");
	include("print_page.php");	
?><script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>