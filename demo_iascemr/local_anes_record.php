<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$current_form_version = 6;
$blEnableHTMLGrid = false;
$strUserAgent = $_SERVER['HTTP_USER_AGENT'];//echo $strUserAgent;
if(stristr($strUserAgent, 'Safari') == true) {
	$blEnableHTMLGrid = true;
}
elseif(stristr($strUserAgent, 'MSIE') == true){
	$pos = strpos($strUserAgent, 'MSIE');
	(int)substr($strUserAgent,$pos + 5, 3);
	if((int)substr($strUserAgent,$pos + 5, 3) > 8){
		$blEnableHTMLGrid = true;
	}
}else if(stristr($_SERVER['HTTP_USER_AGENT'],"rv:11") !== false){ //IE 11
	$blEnableHTMLGrid = true;
}

include_once("admin/classObjectFunction.php");
include_once("library/classes/local_anesthesia.php");
$objManageData = new manageData;
$objLocalAnesData = new LocalAnesthesia;
extract($_GET);
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];	
$patient_id = $_GET['patient_id'];
if(!$patient_id) {
	$patient_id = $_REQUEST['patient_id'];
}
$pConfId = $_REQUEST['pConfId'];
$cancelRecord = $_REQUEST['cancelRecord'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$slider_row="#CAD8FD";

//START CODE TO CREATE GRAPH WHEN PRINT
$strGridImagePath = "";
/*if($_REQUEST['submitMe'] && $_REQUEST["hiddPrintLocalAnesPage"]=='yes'){  //IN CASE OF PRINT
	$chkLocalAnesPrintDetails = $objManageData->getRowRecord('localanesthesiarecord', 'confirmation_id', $pConfId);
	$strGridImagePath = $chkLocalAnesPrintDetails->grid_image_path;	
	$startTime = $chkLocalAnesPrintDetails->startTime;	
	if((int)$_REQUEST["hidEnableHTMLGrid"] == 1 && $strGridImagePath){
		if(empty($strGridImagePath) == false){
			if(file_exists($strGridImagePath) == true){
				//unlink($strGridImagePath);
			}
		}		
		$imagePath = "sx_grid_images";
		$patientDir = "/PatientId_".$_REQUEST["patient_id"]."";
		$patientConfDir = "/PatientConfId_".$_REQUEST["pConfId"]."";			
		if(is_dir($imagePath.$patientDir.$patientConfDir) == false){
			mkdir($imagePath.$patientDir.$patientConfDir, 0777, true);
		}			
		$gridImgData 		= str_replace("data:image/png;base64,","",$_REQUEST['hidGridImgData']);
		$drawingFileName 	= '/'.end(explode("/",str_ireplace(".jpeg",".png",$strGridImagePath)));
		$drawingFilePath 	= str_ireplace(".jpeg",".png",$strGridImagePath);
		
		
		file_put_contents($drawingFilePath, base64_decode($gridImgData));
		
		$bakImgResource 	= imagecreatefromjpeg("sc-grid/images/bgTest.jpg");
		$canvasImgResource 	= imagecreatefrompng($drawingFilePath);										
		imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, 1001, 369);		
		
		//ADD Time Bar HERE --
		if(!empty($startTime)){
			//03:32A _ 3:30
			$startTime = str_replace(array("A","P"), "", $startTime);
			$startTime_arr = explode(":",$startTime);
			$start_time_hr = trim($startTime_arr[0]);
			$start_time_min = trim($startTime_arr[1]);
			if($start_time_min<=15){$start_time_min="0";}
			if($start_time_min>=30&&$start_time_min<=45){$start_time_min="30";}
			if($start_time_min>=45){$start_time_min="45";}
			
			$pw=29-15;
			$ph=1;
			$pd=48;//76-29;
			
			$tmp_textcolor = imagecolorallocate($bakImgResource, 0, 0, 0);
			
			// Write the string at the top left
			for($i=0,$w=0;$i<22;$i++){
				
				if($start_time_hr <"10" ){  $start_time_hr="0".(int)$start_time_hr; }
				if($start_time_min == "0"){  $start_time_min="00"; }
				$tmp_txt = $start_time_hr.":".$start_time_min;
				
				imagestring($bakImgResource, 1, $pw, $ph, $tmp_txt, $tmp_textcolor);
				
				$pw = (int)$pw+(int)$pd;
				
				//echo "\n".$tmp_txt;
				
				$start_time_min = (int)$start_time_min + 15;
				
				if($start_time_min == "60"){
					$start_time_hr = (int)$start_time_hr+1;
					$start_time_min ="0";
				}
				
				if($start_time_hr == "13"){$start_time_hr = "1";}
				
			}
		
		}
		
		//ADD Time Bar HERE --
				
		imagejpeg($bakImgResource, $drawingFilePath); 
		$drawingFileNameJ = str_replace(".png", ".jpeg", $drawingFileName);
		rename($imagePath.$patientDir.$patientConfDir.$drawingFileName, $imagePath.$patientDir.$patientConfDir.$drawingFileNameJ);
		imagedestroy($bakImgResource);
		imagedestroy($canvasImgResource);
	}
}*/
//END CODE TO CREATE GRAPH WHEN PRINT

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
if($blEnableHTMLGrid == true){
	?>
	<link rel="icon" type="image/png" href="sc-grid/images/scGrid.png" />
    <link type="text/css" href="sc-grid/grid.css" rel="stylesheet" />
    <?php
}
?>
<title>Surgerycenter EMR</title>
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
	#gridTxtDivId table td input[type="text"]{
		height:23px; border:1px solid <?php echo $border_blue_local_anes;?>;font-family:"verdana";
	}
	#blnkLblId td input[type="text"]{
		height:23px; border:1px solid #ccc; width:120px;font-family:"verdana";
	}
	#gridTxtDivId table td, #blnkLblId td{
		padding:0px; vertical-align:top;font-family:"verdana";
	}
</style>
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<!--gridTxtDivId-->
<?php
$tablename = "localanesthesiarecord";
include_once("common/commonFunctions.php"); 
//include("common/linkfile.php");//document.getElementById('divSaveAlert').style.display = 'none';
$spec = "</head><body onload='scnUpldPosSet();' class='slider_anesthesia' onClick=\"closeEpost(); return top.frames[0].main_frmInner.hideSliders();\" onunload=\"chart_log_del();\"> 
";
include("common/link_new_file.php");
//START INCLUDE PREDEFINE FUNCTIONS
	include_once("common/ekgLocalAnes_pop.php");  //LOCAL ANES
	include_once("common/evaluationLocalAnes_pop.php"); //LOCAL ANES
	include_once("common/post_op_evaluation_pop.php"); //LOCAL ANES
	include_once("common/dentationLocalAnes_pop.php"); //LOCAL ANES
//END INCLUDE PREDEFINE FUNCTIONS



//start code to read XML

$evaluationPreOpXMLFile = dirname(__FILE__)."/xml/evaluation.xml";
if(file_exists($evaluationPreOpXMLFile)){
	$values = array();
	$XML = file_get_contents($evaluationPreOpXMLFile);
	$values = $objManageData -> XMLToArray($XML);
	foreach($values as $key => $val){	
		$evaluationicationName = "";
		if( ($val["tag"] =="evaluationInfo") && ($val["type"]=="complete") && ($val["level"]=="2") ){		
			$evaluationName = str_replace("'","",$val["attributes"]["name"]);	
			$evaluationTitleArr[]="'".$evaluationName."'";
		}
	}
	if(count($evaluationTitleArr)>0){
		$evaluationTitle = join(',',$evaluationTitleArr);
	}
}
$postopevaluationXMLFile = dirname(__FILE__)."/xml/postopevaluation.xml";
if(file_exists($postopevaluationXMLFile)){
	$values = array();
	$XML = file_get_contents($postopevaluationXMLFile);
	$values = $objManageData -> XMLToArray($XML);
	foreach($values as $key => $val){	
		$postopevaluationName = "";
		if( ($val["tag"] =="postopevaluationInfo") && ($val["type"]=="complete") && ($val["level"]=="2") ){		
			$postopevaluationName = str_replace("'","",$val["attributes"]["name"]);	
			$postopevaluationTitleArr[]="'".$postopevaluationName."'";
		}
	}
	if(count($postopevaluationTitleArr)>0){
		$postopevaluationTitle = join(',',$postopevaluationTitleArr);
	}
}
//end code to read XML

// GETTING CONFIRMATION DETAILS 
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$surgeonId = $detailConfirmation->surgeonId;
	$anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
	$confimDOS = $detailConfirmation->dos;
	
	$confirmAnes_NA = $detailConfirmation->anes_NA;
	$signAnesthesiaIdBackColor=$chngBckGroundColor;
	if($confirmAnes_NA=='Yes') {
		$signAnesthesiaIdBackColor=$whiteBckGroundColor; 
	}
	
	//GET ASSIGNED SURGEON ID AND SURGEON NAME
		$localAnesAssignedAnesthesiaId = $detailConfirmation->anesthesiologist_id;
		$localAnesAssignedAnesthesiaName = stripslashes($detailConfirmation->anesthesiologist_name);
		$localAssignedSubAnesType = getUserSubTypeFun($localAnesAssignedAnesthesiaId);
	//END GET ASSIGNED SURGEON ID AND SURGEON NAME

// GETTING CONFIRMATION DETAILS   

// GETTING SURGEONS SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $surgeonId;
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails) {
		foreach($surgeonsDetails as $usersDetail){
			$signatureOfSurgeon = $usersDetail->signature;
		}
	}	
// GETTING SURGEONS SIGN YES OR NO
// GETTING SURGEONS SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $anesthesiologist_id;
	$anesthesiologistDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($anesthesiologistDetails) {
		foreach($anesthesiologistDetails as $usersDetail){
			$signatureOfAnesthesiologist = $usersDetail->signature;
		}
	}	
// GETTING SURGEONS SIGN YES OR NO

// GETTING LOGGED IN USER TYPE
	$detailLoggedUser = $objManageData->getRowRecord('users', 'usersId ', $_SESSION['loginUserId']);
	if($detailLoggedUser) {
		$logInUserType = $detailLoggedUser->user_type;
		$logInUserSubType = $detailLoggedUser->user_sub_type;
	}
// GETTING LOGGED IN USER TYPE

	if(!$cancelRecord){
		/////// FORM SHIFT TO RIGHT SLIDER
			$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$mac_regional_anesthesia_form = $getLeftLinkDetails->mac_regional_anesthesia_form;
			
			if($mac_regional_anesthesia_form=='true'){
				$formArrayRecord['mac_regional_anesthesia_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
			}
			//MAKE AUDIT STATUS VIEW
			if($_REQUEST['saveRecord']!='true'){
				unset($arrayRecord);
				$arrayRecord['user_id'] = $_SESSION['loginUserId'];
				$arrayRecord['patient_id'] = $_SESSION['patient_id'];
				$arrayRecord['confirmation_id'] = $pConfId;
				$arrayRecord['form_name'] = 'mac_regional_anesthesia_form'; 
				$arrayRecord['status'] = 'viewed';
				$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
				$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
			}
			//MAKE AUDIT STATUS VIEW
			
		////// FORM SHIFT TO RIGHT SLIDER
	}
	elseif($cancelRecord){
		$patient_id = ($_REQUEST['patient_id']!=0)?$_REQUEST['patient_id']:11;
		$fieldName="mac_regional_anesthesia_form";
		$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
		include("left_link_hide.php");
	}	
	
	$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;fieldName='.$fieldName;
// GETTTING PRIMARY AND SECONDARY PROCEDURES
	$procDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
		if($procDetails) {
			extract($procDetails);
		}	
// GETTTING PRIMARY AND SECONDARY PROCEDURES

$submitMe = $_REQUEST['submitMe'];

//UPDATING PATIENT STATUS IN STUB TABLE

 $patientdata=imw_query("select patientconfirmation.patientId,patientconfirmation.dos,
patientconfirmation.surgery_time,patient_data_tbl.patient_fname,patient_mname,
patient_data_tbl.patient_lname,patient_data_tbl.date_of_birth from patientconfirmation 
left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
where  patientconfirmation.patientConfirmationId='$pConfId'");
while($patient_data=imw_fetch_array($patientdata))
{
   $dos= $patient_data['dos'];
   $surgerytime=$patient_data['surgery_time'];
   $patient_fname=$patient_data['patient_fname'];
   $patient_mname=$patient_data['patient_mname'];
   $patient_lname=$patient_data['patient_lname'];
   $dob=$patient_data['date_of_birth'];
}

 $stub_data=imw_query("select * from stub_tbl where dos='$dos' && patient_first_name ='$patient_fname' && patient_middle_name='$patient_mname'
&& patient_last_name='$patient_lname' && patient_dob='$dob' && surgery_time='$surgerytime'");

while($stubtbl_data=imw_fetch_array($stub_data))
{
  $stub_id=$stubtbl_data['stub_id'];
} 
if($_REQUEST['submitMe'])
{
 //$update_status=imw_query("update stub_tbl set patient_status='IOA' where stub_id='$stub_id'");
}
//END UPDATING PATIENT STATUS IN STUB TABLE

if($submitMe && $_REQUEST["hiddPrintLocalAnesPage"]!='yes'){  
	$text = $_REQUEST['getText'];
	$tablename = "localanesthesiarecord";
	$localAnesthesiaRecordId = $_REQUEST['localAnesthesiaRecordId'];
	$med_grid_id = $_REQUEST['med_grid_id'];
	$med_grid_sec_id = $_REQUEST['med_grid_sec_id'];
	unset($arrayRecord);
	
	$arrayRecord['patientInterviewed'] = addslashes($_REQUEST['chbx_pat_inter']);
	$arrayRecord['chartNotesReviewed'] = addslashes($_REQUEST['chbx_chart']);
	$arrayRecord['fpExamPerformed'] = addslashes($_REQUEST['chbx_fp_exam']);
	$arrayRecord['npo'] = addslashes($_REQUEST['chbx_npo']);
	$arrayRecord['ansComment'] = addslashes($_REQUEST['ansComment']);
	
	$alertOriented = $_REQUEST['chbx_alert'];
	if(is_array($alertOriented)) {
		$alertOriented = @implode(",",$alertOriented);
	}
	$arrayRecord['alertOriented'] = addslashes($alertOriented);
	
	$arrayRecord['assistedByTranslator'] = addslashes($_REQUEST['chbx_assist']);
	
	$orStartTime 	= $objManageData->setTmFormat($_REQUEST["orStartTime"]);
	$startTime 		= $objManageData->setTmFormat($_REQUEST["startTime"]);
	$newStartTime1 	= $objManageData->setTmFormat($_REQUEST["newStartTime1"]);
	$newStartTime2 	= $objManageData->setTmFormat($_REQUEST["newStartTime2"]);
	$newStartTime3 	= $objManageData->setTmFormat($_REQUEST["newStartTime3"]);
	$orStopTime 	= $objManageData->setTmFormat($_REQUEST["orStopTime"]);
	$stopTime 		= $objManageData->setTmFormat($_REQUEST["stopTime"]);
	$newStopTime1 	= $objManageData->setTmFormat($_REQUEST["newStopTime1"]);
	$newStopTime2 	= $objManageData->setTmFormat($_REQUEST["newStopTime2"]);
	$newStopTime3 	= $objManageData->setTmFormat($_REQUEST["newStopTime3"]);
	/*
	$startTimeTemp = $_REQUEST["orStartTime"];
	//CODE TO CALCULATE OR START TIME
	if($startTimeTemp<>"") {
		$startTimesplit = explode(":",$startTimeTemp);
		$startTimesplit[0] = ($startTimesplit[0] == 12) ? 0 : $startTimesplit[0];
		$startTimeFindAmPM = $startTimesplit[1][2];
		if($startTimeFindAmPM == "P" || $startTimeFindAmPM == "p") {
			$startTimesplit[0] = $startTimesplit[0]+12;
		}
		$orStartTime = $startTimesplit[0].":".$startTimesplit[1][0].$startTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE OR START TIME
	
	
	$startTimeTemp = $_REQUEST["startTime"];
	//CODE TO CALCULATE START TIME
	if($startTimeTemp<>"") {
		$startTimesplit = explode(":",$startTimeTemp);
		$startTimesplit[0] = ($startTimesplit[0] == 12) ? 0 : $startTimesplit[0];
		$startTimeFindAmPM = $startTimesplit[1][2];
		if($startTimeFindAmPM == "P" || $startTimeFindAmPM == "p") {
			$startTimesplit[0] = $startTimesplit[0]+12;
		}
		$startTime = $startTimesplit[0].":".$startTimesplit[1][0].$startTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE START TIME
	
	
	$startTimeTemp = $_REQUEST["newStartTime1"];
	//CODE TO CALCULATE START TIME new 1
	if($startTimeTemp<>"") {
		$startTimesplit = explode(":",$startTimeTemp);
		$startTimesplit[0] = ($startTimesplit[0] == 12) ? 0 : $startTimesplit[0];
		$startTimeFindAmPM = $startTimesplit[1][2];
		if($startTimeFindAmPM == "P" || $startTimeFindAmPM == "p") {
			$startTimesplit[0] = $startTimesplit[0]+12;
		}
		$newStartTime1 = $startTimesplit[0].":".$startTimesplit[1][0].$startTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE START TIME new 1
	
	$startTimeTemp = $_REQUEST["newStartTime2"];
	//CODE TO CALCULATE START TIME new 2
	if($startTimeTemp<>"") {
		$startTimesplit = explode(":",$startTimeTemp);
		$startTimesplit[0] = ($startTimesplit[0] == 12) ? 0 : $startTimesplit[0];
		$startTimeFindAmPM = $startTimesplit[1][2];
		if($startTimeFindAmPM == "P" || $startTimeFindAmPM == "p") {
			$startTimesplit[0] = $startTimesplit[0]+12;
		}
		$newStartTime2 = $startTimesplit[0].":".$startTimesplit[1][0].$startTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE START TIME new 2
	
	$startTimeTemp = $_REQUEST["newStartTime3"];
	//CODE TO CALCULATE START TIME new 3
	if($startTimeTemp<>"") {
		$startTimesplit = explode(":",$startTimeTemp);
		$startTimesplit[0] = ($startTimesplit[0] == 12) ? 0 : $startTimesplit[0];
		$startTimeFindAmPM = $startTimesplit[1][2];
		if($startTimeFindAmPM == "P" || $startTimeFindAmPM == "p") {
			$startTimesplit[0] = $startTimesplit[0]+12;
		}
		$newStartTime3 = $startTimesplit[0].":".$startTimesplit[1][0].$startTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE START TIME new 3
	
	$stopTimeTemp = $_REQUEST["orStopTime"];
	//CODE TO CALCULATE OR STOP TIME
	if($stopTimeTemp<>"") {
		$stopTimesplit = explode(":",$stopTimeTemp);
		$stopTimesplit[0] = ($stopTimesplit[0] == 12) ? 0 : $stopTimesplit[0];
		$stopTimeFindAmPM = $stopTimesplit[1][2];
		if($stopTimeFindAmPM == "P" || $stopTimeFindAmPM == "p") {
			$stopTimesplit[0] = $stopTimesplit[0]+12;
		}
		$orStopTime = $stopTimesplit[0].":".$stopTimesplit[1][0].$stopTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE OR STOP TIME
	
	
	$stopTimeTemp = $_REQUEST["stopTime"];
	//CODE TO CALCULATE STOP TIME
	if($stopTimeTemp<>"") {
		$stopTimesplit = explode(":",$stopTimeTemp);
		$stopTimesplit[0] = ($stopTimesplit[0] == 12) ? 0 : $stopTimesplit[0];
		$stopTimeFindAmPM = $stopTimesplit[1][2];
		if($stopTimeFindAmPM == "P" || $stopTimeFindAmPM == "p") {
			$stopTimesplit[0] = $stopTimesplit[0]+12;
		}
		$stopTime = $stopTimesplit[0].":".$stopTimesplit[1][0].$stopTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE STOP TIME
	
	$stopTimeTemp = $_REQUEST["newStopTime1"];
	//CODE TO CALCULATE STOP TIME new 1 
	if($stopTimeTemp<>"") {
		$stopTimesplit = explode(":",$stopTimeTemp);
		$stopTimesplit[0] = ($stopTimesplit[0] == 12) ? 0 : $stopTimesplit[0];
		$stopTimeFindAmPM = $stopTimesplit[1][2];
		if($stopTimeFindAmPM == "P" || $stopTimeFindAmPM == "p") {
			$stopTimesplit[0] = $stopTimesplit[0]+12;
		}
		$newStopTime1 = $stopTimesplit[0].":".$stopTimesplit[1][0].$stopTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE STOP TIME new 1
		$stopTimeTemp = $_REQUEST["newStopTime2"];
	//CODE TO CALCULATE STOP TIME new 2 
	if($stopTimeTemp<>"") {
		$stopTimesplit = explode(":",$stopTimeTemp);
		$stopTimesplit[0] = ($stopTimesplit[0] == 12) ? 0 : $stopTimesplit[0];
		$stopTimeFindAmPM = $stopTimesplit[1][2];
		if($stopTimeFindAmPM == "P" || $stopTimeFindAmPM == "p") {
			$stopTimesplit[0] = $stopTimesplit[0]+12;
		}
		$newStopTime2 = $stopTimesplit[0].":".$stopTimesplit[1][0].$stopTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE STOP TIME new 2
		$stopTimeTemp = $_REQUEST["newStopTime3"];
	//CODE TO CALCULATE STOP TIME new 3 
	if($stopTimeTemp<>"") {
		$stopTimesplit = explode(":",$stopTimeTemp);
		$stopTimesplit[0] = ($stopTimesplit[0] == 12) ? 0 : $stopTimesplit[0];
		$stopTimeFindAmPM = $stopTimesplit[1][2];
		if($stopTimeFindAmPM == "P" || $stopTimeFindAmPM == "p") {
			$stopTimesplit[0] = $stopTimesplit[0]+12;
		}
		$newStopTime3 = $stopTimesplit[0].":".$stopTimesplit[1][0].$stopTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE STOP TIME new 3
	*/

	//CODE TO ENABLE/DISABLE TIME INTERVAL
	$hiddActiveTimeInterval = $_REQUEST['hiddActiveTimeInterval'];
	if(!$startTime || $startTime=="00:00:00"){
		$hiddActiveTimeInterval = "";	
	}
	//END CODE TO ENABLE/DISABLE TIME INTERVAL
	
	$arrayRecord['procedurePrimaryVerified'] = addslashes($_REQUEST['chbx_proced']);
	$arrayRecord['procedureSecondaryVerified'] = addslashes($_REQUEST['chbx_sec_veri']);
	$arrayRecord['siteVerified'] = addslashes($_REQUEST['chbx_site']);
	$arrayRecord['bp'] = htmlentities(addslashes($_REQUEST['bp1']));
	$arrayRecord['P'] = htmlentities(addslashes($_REQUEST['p']));
	$arrayRecord['rr'] = htmlentities(addslashes($_REQUEST['rr']));
	$arrayRecord['sao'] = htmlentities(addslashes($_REQUEST['sao']));
	$arrayRecord['bp_p_rr_time'] = '';
	$txt_bp_p_rr_time = "";
	if($_REQUEST['txt_bp_p_rr_time']){
		$txt_bp_p_rr_time = $objManageData->setTmFormat($_REQUEST['txt_bp_p_rr_time']);
		//$arrayRecord['bp_p_rr_time'] = date('Y-m-d H:i:s',strtotime($_REQUEST['txt_bp_p_rr_time']));
		$arrayRecord['bp_p_rr_time'] = $txt_bp_p_rr_time;
	}

	$arrayRecord['evaluation2'] = addslashes($_REQUEST['evaluation2']);
	
	$arrayRecord['stableCardiPlumFunction'] = addslashes($_REQUEST['chbx__cardiovesc']);
	$arrayRecord['planAnesthesia'] 			= addslashes($_REQUEST['chbx_plan']);
	$arrayRecord['allQuesAnswered'] 		= addslashes($_REQUEST['chbx_all_qus']);
	$arrayRecord['asaPhysicalStatus'] 		= addslashes($_REQUEST['phys_status']);

	$arrayRecord['remarks'] 				= addslashes($_REQUEST['txtarea_remarks']);
	$arrayRecord['evaluation'] 				= addslashes($_REQUEST['evaluation']);
	
	//$arrayRecord['chbx_enable_postop_desc'] = addslashes($_REQUEST['chbx_enable_postop_desc']);
	$arrayRecord['chbx_vss'] 				= addslashes($_REQUEST['chbx_vss']);
	$arrayRecord['chbx_atsf'] 				= addslashes($_REQUEST['chbx_atsf']);
	$arrayRecord['chbx_pa'] 				= addslashes($_REQUEST['chbx_pa']);
	$arrayRecord['chbx_nausea'] 			= addslashes($_REQUEST['chbx_nausea']);
	$arrayRecord['chbx_vomiting'] 			= addslashes($_REQUEST['chbx_vomiting']);
	$arrayRecord['chbx_dizziness'] 			= addslashes($_REQUEST['chbx_dizziness']);
	$arrayRecord['chbx_rd'] 				= addslashes($_REQUEST['chbx_rd']);
	$arrayRecord['chbx_aao'] 				= addslashes($_REQUEST['chbx_aao']);
	$arrayRecord['chbx_ddai'] 				= addslashes($_REQUEST['chbx_ddai']);
	$arrayRecord['chbx_pv'] 				= addslashes($_REQUEST['chbx_pv']);
	$arrayRecord['chbx_rtpog'] 				= addslashes($_REQUEST['chbx_rtpog']);
	$arrayRecord['chbx_pain'] 				= addslashes($_REQUEST['chbx_pain']);
	
	$arrayRecord['relivedPreNurseId'] 		= addslashes($_REQUEST['relivedPreNurseIdList']);
	$arrayRecord['relivedIntraNurseId'] 	= addslashes($_REQUEST['relivedIntraNurseIdList']);
	$arrayRecord['relivedPostNurseId'] 		= addslashes($_REQUEST['relivedPostNurseIdList']);
	
	$arrayRecord['routineMonitorApplied'] = addslashes($_REQUEST['chbx_routine']);
	$arrayRecord['hide_anesthesia_grid'] 	= addslashes($_REQUEST['hide_anesthesia_grid']);		
	$arrayRecordMedGrid['blank1_label'] 		= addslashes($_REQUEST['blank1_label']);
	$arrayRecordMedGrid['blank2_label'] 		= addslashes($_REQUEST['blank2_label']);
	$arrayRecordMedGrid['blank3_label'] 		= addslashes($_REQUEST['blank3_label']);
	$arrayRecordMedGrid['blank4_label'] 		= addslashes($_REQUEST['blank4_label']);
	$arrayRecordMedGrid['mgPropofol_label'] 	= addslashes($_REQUEST['mgPropofol_label']);
	$arrayRecordMedGrid['mgMidazolam_label'] 	= addslashes($_REQUEST['mgMidazolam_label']);
	$arrayRecordMedGrid['confirmation_id'] 		= addslashes($pConfId);	

	$arrayRecordMedGridSec['mgKetamine_label'] 	= addslashes($_REQUEST['mgKetamine_label']);
	$arrayRecordMedGridSec['mgLabetalol_label']	= addslashes($_REQUEST['mgLabetalol_label']);
	$arrayRecordMedGridSec['mcgFentanyl_label']	= addslashes($_REQUEST['mcgFentanyl_label']);
	$arrayRecordMedGridSec['confirmation_id'] 	= addslashes($pConfId);	
	
	//START 
	for($t=1;$t<=20;$t++) {
		$arrayRecordMedGrid['blank1_'.$t] 			= $_REQUEST['blank1_'.$t] 	? addslashes($_REQUEST['blank1_'.$t].'@@'.$_REQUEST['t_blank1_'.$t]) 		: '';
		$arrayRecordMedGrid['blank2_'.$t] 			= $_REQUEST['blank2_'.$t] 	? addslashes($_REQUEST['blank2_'.$t].'@@'.$_REQUEST['t_blank2_'.$t]) 		: '';
		$arrayRecordMedGrid['blank3_'.$t] 			= $_REQUEST['blank3_'.$t] 	? addslashes($_REQUEST['blank3_'.$t].'@@'.$_REQUEST['t_blank3_'.$t]) 		: '';
		$arrayRecordMedGrid['blank4_'.$t] 			= $_REQUEST['blank4_'.$t] 	? addslashes($_REQUEST['blank4_'.$t].'@@'.$_REQUEST['t_blank4_'.$t]) 		: '';
		$arrayRecordMedGrid['propofol_'.$t] 		= $_REQUEST['propofol_'.$t] ? addslashes($_REQUEST['propofol_'.$t].'@@'.$_REQUEST['t_propofol_'.$t]) 	: '';
		$arrayRecordMedGrid['midazolam_'.$t]		= $_REQUEST['midazolam_'.$t]? addslashes($_REQUEST['midazolam_'.$t].'@@'.$_REQUEST['t_midazolam_'.$t]) 	: '';
		
		$arrayRecordMedGridSec['Fentanyl_'.$t] 		= $_REQUEST['Fentanyl_'.$t] ? addslashes($_REQUEST['Fentanyl_'.$t].'@@'.$_REQUEST['t_Fentanyl_'.$t]) 	: '';
		$arrayRecordMedGridSec['ketamine_'.$t] 		= $_REQUEST['ketamine_'.$t]	? addslashes($_REQUEST['ketamine_'.$t].'@@'.$_REQUEST['t_ketamine_'.$t]) 	: '';
		$arrayRecordMedGridSec['labetalol_'.$t]		= $_REQUEST['labetalol_'.$t]? addslashes($_REQUEST['labetalol_'.$t].'@@'.$_REQUEST['t_labetalol_'.$t]) 	: '';
		$arrayRecordMedGridSec['spo2_'.$t]			= $_REQUEST['spo2_'.$t] 	? addslashes($_REQUEST['spo2_'.$t].'@@'.$_REQUEST['t_spo2_'.$t]) 			: '';
		$arrayRecordMedGridSec['o2lpm_'.$t] 		= $_REQUEST['o2lpm_'.$t] 	? addslashes($_REQUEST['o2lpm_'.$t].'@@'.$_REQUEST['t_o2lpm_'.$t]) 			: '';
	}
	//END
	
	/*
	$ekgBigRowValueSub = $_REQUEST['ekgBigRowValue'];
	$ekgBigRowValue = substr($ekgBigRowValueSub,0,33); 
	$ekgBigRowValue = addslashes($ekgBigRowValue);
	*/
	
	
	$ekgBigRowValue = $_REQUEST['ekgBigRowValue'];
	
	$arrayRecord['ekgBigRowValue'] = addslashes($ekgBigRowValue);
	$arrayRecord['orStartTime'] = addslashes($orStartTime);
	$arrayRecord['orStopTime'] = addslashes($orStopTime);
	$arrayRecord['startTime'] = addslashes($startTime);
	$arrayRecord['activeTimeInterval'] =addslashes( $hiddActiveTimeInterval);
	$arrayRecord['newStartTime1'] = addslashes($newStartTime1);
	$arrayRecord['newStartTime2'] = addslashes($newStartTime2);
	$arrayRecord['newStartTime3'] = addslashes($newStartTime3);
	
	$arrayRecord['stopTime'] = addslashes($stopTime);
	$arrayRecord['bsValue'] = htmlentities(addslashes($_REQUEST['bsValue']));
	$arrayRecord['NA'] = addslashes($_REQUEST['chkBoxNS']);
	
	$arrayRecord['newStopTime1'] = addslashes($newStopTime1);
	$arrayRecord['newStopTime2'] = addslashes($newStopTime2);
	$arrayRecord['newStopTime3'] = addslashes($newStopTime3);
	
	$arrayRecord['local_anes_revaluation2'] = addslashes($_REQUEST['local_anes_revaluation2']);	
	$arrayRecord['ivCatheter'] = addslashes($_REQUEST['chbx_no']);
	$arrayRecord['hand_right'] = addslashes($_REQUEST['chbx_hand_right']);
	$arrayRecord['hand_left'] = addslashes($_REQUEST['chbx_hand_left']);
	$arrayRecord['wrist_right'] = addslashes($_REQUEST['chbx_wrist_right']);
	$arrayRecord['wrist_left'] = addslashes($_REQUEST['chbx_wrist_left']);
	$arrayRecord['arm_right'] = addslashes($_REQUEST['chbx_arm_right']);
	$arrayRecord['arm_left'] = addslashes($_REQUEST['chbx_arm_left']);
	$arrayRecord['anti_right'] = addslashes($_REQUEST['chbx_anti_right']);
	$arrayRecord['anti_left'] = addslashes($_REQUEST['chbx_anti_left']);
	$arrayRecord['topi_peri_retro'] = addslashes($_REQUEST['chbx_topi_peri_retro']);	
	$arrayRecord['topical'] = addslashes($_REQUEST['topical']);	
	$arrayRecord['ivCatheterOther'] = addslashes(trim($_REQUEST['other_reg_anes']));
	$arrayRecord['lidocaine2'] = addslashes($_REQUEST['chbx_lido2']);
	$arrayRecord['lidocaine3'] = addslashes($_REQUEST['chbx_lido3']);
	$arrayRecord['Peribulbar'] = addslashes($_REQUEST['Peribulbar']);
	$arrayRecord['lidocaine4'] = addslashes($_REQUEST['chbx_lido4']);
	$arrayRecord['Bupiyicaine5'] = addslashes($_REQUEST['chbx_bupi']);
	$arrayRecord['Retrobulbar'] = addslashes($_REQUEST['Retrobulbar']);
	$arrayRecord['ugcc'] = addslashes($_REQUEST['chbx_epi']);
	$arrayRecord['Hyalauronidase'] = addslashes($_REQUEST['Hyalauronidase']);
	$arrayRecord['vanlindt'] = addslashes($_REQUEST['vanLindt']);
	$arrayRecord['regionalAnesthesiaOther'] = addslashes($_REQUEST['otherRegionalAnesthesia']);

	$arrayRecord['topical4PercentLidocaine'] = addslashes($_REQUEST['chbx_topical4PercentLidocaine']);
	$arrayRecord['Intracameral'] = addslashes($_REQUEST['Intracameral']);
	$arrayRecord['Intracameral1percentLidocaine'] = addslashes($_REQUEST['chbx_Intracameral1percentLidocaine']);
	$arrayRecord['Peribulbar2percentLidocaine'] = addslashes($_REQUEST['chbx_Peribulbar2percentLidocaine']);
	$arrayRecord['Retrobulbar4percentLidocaine'] = addslashes($_REQUEST['chbx_Retrobulbar4percentLidocaine']);
	$arrayRecord['Hyalauronidase4percentLidocaine'] = addslashes($_REQUEST['chbx_Hyalauronidase4percentLidocaine']);
	$arrayRecord['bupivacaine75'] = addslashes($_REQUEST['bupivacaine75']);
	$arrayRecord['marcaine75'] = addslashes($_REQUEST['marcaine75']);
	
	$arrayRecord['VanLindr'] = addslashes($_REQUEST['VanLindr']);
	$arrayRecord['VanLindrHalfPercentLidocaine'] = addslashes($_REQUEST['chbx_VanLindrHalfPercentLidocaine']);
	$arrayRecord['lidTxt'] = addslashes($_REQUEST['lidTxt']);
	$arrayRecord['lid'] = addslashes($_REQUEST['lid']);
	$arrayRecord['lidEpi5ug'] = addslashes($_REQUEST['chbx_lidEpi5ug']);
	$arrayRecord['otherRegionalAnesthesiaTxt1'] = addslashes($_REQUEST['otherRegionalAnesthesiaTxt1']);
	$arrayRecord['otherRegionalAnesthesiaDrop'] = addslashes($_REQUEST['otherRegionalAnesthesiaDrop']);
	$arrayRecord['otherRegionalAnesthesiaWydase15u'] = addslashes($_REQUEST['chbx_otherRegionalAnesthesiaWydase15u']);
	$arrayRecord['otherRegionalAnesthesiaTxt2'] = htmlentities(addslashes($_REQUEST['otherRegionalAnesthesiaTxt2']));
	
	$arrayRecord['ocular_pressure_na'] = addslashes($_REQUEST['chbx_ocular_pressure_na']);
	$arrayRecord['none'] = addslashes($_REQUEST['chbx_none']);
	$arrayRecord['digital'] = addslashes($_REQUEST['chbx_digi']);
	$arrayRecord['honanballon'] = addslashes($_REQUEST['honanBallon']);
	$arrayRecord['honanBallonAnother'] = addslashes($_REQUEST['honanBallonAnother']);
	
	$arrayRecord['anyKnowAnestheticComplication'] = addslashes($_REQUEST['chbx_anes']);
	$arrayRecord['stableCardiPlumFunction2'] = addslashes($_REQUEST['chbx_pulm']);
	$arrayRecord['satisfactoryCondition4Discharge'] = addslashes($_REQUEST['chbx_dis']);
	$arrayRecord['surgeonId'] = addslashes($surgeonId);	
	$arrayRecord['ascId'] = addslashes($ascId);
	$arrayRecord['confirmation_id'] = addslashes($pConfId);	
	$arrayRecord['patient_id'] = addslashes($patient_id);
	$arrayRecord['surgeonSign'] = addslashes($_REQUEST['elem_signature1']);
	$arrayRecord['anesthesiologistSign'] = addslashes($_REQUEST['elem_signature2']);	
	$arrayRecord['TopicalBlock1Block2'] = addslashes($_REQUEST['chbx_TopicalBlock1Block2']);
	$arrayRecord['Reblock'] = addslashes($_REQUEST['chbx_Reblock']);
	
	/*
	$arrayRecord['TopicalAspiration'] = $_REQUEST['TopicalAspiration'];
	$arrayRecord['TopicalFull'] = $_REQUEST['TopicalFull'];
	$arrayRecord['TopicalBeforeInjection'] = $_REQUEST['TopicalBeforeInjection'];
	$arrayRecord['TopicalRockNegative'] = $_REQUEST['TopicalRockNegative'];
	$arrayRecord['topicalComment'] 	= addslashes($_REQUEST['topicalComment']);
	*/
	$arrayRecord['Block1Block2Aspiration'] = addslashes($_REQUEST['Block1Block2Aspiration']);
	$arrayRecord['Block1Block2Full'] = addslashes($_REQUEST['Block1Block2Full']);
	$arrayRecord['Block1Block2BeforeInjection'] = addslashes($_REQUEST['Block1Block2BeforeInjection']);
	$arrayRecord['Block1Block2RockNegative'] = addslashes($_REQUEST['Block1Block2RockNegative']);
	$arrayRecord['Block1Block2Comment'] 	= addslashes($_REQUEST['Block1Block2Comment']);
	
	
	$getUserTypeQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
	$getUserTypeRes = imw_query($getUserTypeQry) or die(imw_error()); 
	$getUserTypeNumRow =imw_num_rows($getUserTypeRes);
	if($getUserTypeNumRow) {
		$getUserTypeRow = imw_fetch_array($getUserTypeRes); 
		$loggedUserType = $getUserTypeRow["user_type"];
		if($loggedUserType=='Anesthesiologist') {
			$saveByAnes='Yes';
			
			$arrayRecord['saveByAnes'] = 'Yes';
		}
	}
	
	$arrayRecord['applet_data'] = $_REQUEST['applet_data'];
	if($_REQUEST['applet_time_interval']==""){
	}
	else{
		$arrayRecord['applet_time_interval'] = $_REQUEST['applet_time_interval'];
	}
	$strGridImagePath = "";
	//START CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
		$chkUserSignDetails = $objManageData->getRowRecord('localanesthesiarecord', 'confirmation_id', $pConfId);
		if($chkUserSignDetails) {
			$chk_signAnesthesia1Id = $chkUserSignDetails->signAnesthesia1Id;
			$chk_signAnesthesia2Id = $chkUserSignDetails->signAnesthesia2Id;
			$chk_signAnesthesia3Id = $chkUserSignDetails->signAnesthesia3Id;
			$chk_signAnesthesia4Id = $chkUserSignDetails->signAnesthesia4Id;
			
			$chk_versionNum	= $chkUserSignDetails->version_num;
			$chk_versionDateTime	= $chkUserSignDetails->version_date_time;
			$chk_vitalSignGridStatus	=	$chkUserSignDetails->vitalSignGridStatus;
			//CHECK FORM STATUS
				$chk_form_status = $chkUserSignDetails->form_status;
			//CHECK FORM STATUS
			$strGridImagePath = $chkUserSignDetails->grid_image_path;
			
			$chk_anes_ScanUpload = $chkUserSignDetails->anes_ScanUpload;
			$chk_anes_ScanUploadPath = $chkUserSignDetails->anes_ScanUploadPath;
		}
	//END CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
	$vitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($chk_form_status,$chk_vitalSignGridStatus,'macAnes');
	if($chk_form_status <> 'completed' && $chk_form_status <> 'not completed')
	{
		$arrayRecord['vitalSignGridStatus']	=	$vitalSignGridStatus;
	}
		
	$version_num = $chk_versionNum;
	if(!$chk_versionNum)
	{
		$version_date_time = $chk_versionDateTime;
		if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
		{
			$version_date_time	=	date('Y-m-d H:i:s');
		}
				
		if($chk_form_status == 'completed' || $chk_form_status=='not completed'){
			$version_num = 1;
		}else{
			$version_num	=	$current_form_version;
		}
		
		$arrayRecord['version_num']	=	$version_num;
		$arrayRecord['version_date_time']	=	$version_date_time;
		
	}
	
	if($version_num > 1)
	{
		$arrayRecord['reliefNurseId']					= $_REQUEST['reliefNurseId'];
		$arrayRecord['confirmIPPSC_signin']		= addslashes($_REQUEST['chbx_ipp']);
		$arrayRecord['siteMarked'] 						= addslashes($_REQUEST['chbx_smpp']);
		$arrayRecord['patientAllergies'] 			= addslashes($_REQUEST['chbx_pa']);	
		$arrayRecord['difficultAirway']				= addslashes($_REQUEST['chbx_dar']);
		$arrayRecord['anesthesiaSafety']			= addslashes($_REQUEST['chbx_asc']);	
		$arrayRecord['allMembersTeam'] 				= addslashes($_REQUEST['chbx_adcpc']);	
		$arrayRecord['riskBloodLoss'] 				= addslashes($_REQUEST['chbx_rbl']);	
		$arrayRecord['bloodLossUnits'] 				= ($_REQUEST['chbx_rbl'] == 'Yes') ? addslashes($_REQUEST['rbl_no_of_units']) : '' ;	
	}
	if($version_num > 2) {
		$arrayRecord['mallampetti_score'] 	= addslashes($_REQUEST['mallampetti_score']);
		$arrayRecord['dentation'] 			= addslashes($_REQUEST['dentation']);
	}
	
	
	//CODE TO SET FORM STATUS 
	//$arrayRecord['form_status'] = 'completed';
	$formStatus = 'completed';
	
	if($chk_anes_ScanUploadPath || $chk_anes_ScanUpload) {//IF DOCUMENT IS SCANED OR UPLOADED THEN RECORD IS SAID TO BE COMPLETED.(No need to check below conditions)
		$formStatus = 'completed';	  
	}else
	{
		if($_REQUEST['chbx_pat_inter']=='' || $_REQUEST['chbx_chart']=='' 
	   || (!$_REQUEST['chbx_alert']) 
	   || $_REQUEST['chbx_proced']=='' 
	   //|| $_REQUEST['chbx_sec_veri']=='' 
	   || $_REQUEST['chbx_site']=='' 
	   || (constant('ANES_VITAL_SIGN_MANDATORY') <> 'OFF' && $_REQUEST['bp1']=="" && $_REQUEST['p']=="" && $_REQUEST['rr']=="" && $_REQUEST['sao']=="")
	   || $_REQUEST['evaluation2']=='' 
	   || $_REQUEST['chbx_all_qus']=='' 
	   || $_REQUEST['phys_status']=='' 
	   || trim($txt_bp_p_rr_time)=='' || trim($txt_bp_p_rr_time)=='00:00:00' 
		 || $startTime==""
	   || ($_REQUEST['chbx_no']==''
	   	   && $_REQUEST['chbx_hand_right']=='' && $_REQUEST['chbx_hand_left']==''
	   	   && $_REQUEST['chbx_wrist_right']=='' && $_REQUEST['chbx_wrist_left']==''
	   	   && $_REQUEST['chbx_arm_right']=='' && $_REQUEST['chbx_arm_left']==''
	   	   && $_REQUEST['chbx_anti_right']=='' && $_REQUEST['chbx_anti_left']==''
		   && trim($_REQUEST['other_reg_anes'])=='' && $_REQUEST['chbx_other11']==''
		  )
	   || ($_REQUEST['chbx_topical4PercentLidocaine']=='' && $_REQUEST['chbx_Intracameral1percentLidocaine']==''
	   	   && $_REQUEST['chbx_Peribulbar2percentLidocaine']=='' && $_REQUEST['chbx_Retrobulbar4percentLidocaine']==''
		   && $_REQUEST['chbx_Hyalauronidase4percentLidocaine']=='' && $_REQUEST['chbx_VanLindrHalfPercentLidocaine']==''	
		   && $_REQUEST['chbx_lidEpi5ug']=='' && $_REQUEST['chbx_otherRegionalAnesthesiaWydase15u']==''	&& $_REQUEST['chbx_TopicalBlock1Block2']==''
			 && $_REQUEST['bupivacaine75'] == '' && $_REQUEST['marcaine75'] == ''
		  )
	   || ($_REQUEST['chbx_ocular_pressure_na']=='' && $_REQUEST['chbx_none']=='' && $_REQUEST['chbx_digi']=='' && $_REQUEST['honanBallon']=='' && $_REQUEST['honanBallonAnother']=='' )
	   
	   || ($chk_signAnesthesia1Id=="0" && $confirmAnes_NA!='Yes')
	   || ($chk_signAnesthesia2Id=="0" && $confirmAnes_NA!='Yes')
	   || ($chk_signAnesthesia3Id=="0" && $confirmAnes_NA!='Yes')
	  
	   || ($_REQUEST['blank1_1']	=='' && $_REQUEST['blank1_2']   =='' && $_REQUEST['blank1_3']	 =='' && $_REQUEST['blank1_4'] ==''
		   && $_REQUEST['blank1_5']	=='' && $_REQUEST['blank1_6']   =='' && $_REQUEST['blank1_7']	 =='' && $_REQUEST['blank1_8'] ==''	
		   && $_REQUEST['blank1_9']	=='' && $_REQUEST['blank1_10']  =='' && $_REQUEST['blank1_11']	 =='' && $_REQUEST['blank1_12']=='' 
		   && $_REQUEST['blank1_13']=='' && $_REQUEST['blank1_14']  ==''&& $_REQUEST['blank1_15']	 =='' && $_REQUEST['blank1_16']=='' 
		   && $_REQUEST['blank1_17']=='' && $_REQUEST['blank1_18']  ==''	&& $_REQUEST['blank1_19']=='' && $_REQUEST['blank1_20']==''	   
	   
		   && $_REQUEST['blank2_1']	=='' && $_REQUEST['blank2_2']	==''   	&& $_REQUEST['blank2_3']	=='' && $_REQUEST['blank2_4']	==''
		   && $_REQUEST['blank2_5']	=='' && $_REQUEST['blank2_6']	==''	&& $_REQUEST['blank2_7']	=='' && $_REQUEST['blank2_8']	==''	
		   && $_REQUEST['blank2_9']	=='' && $_REQUEST['blank2_10']	==''  	&& $_REQUEST['blank2_11']	=='' && $_REQUEST['blank2_12']	=='' 
		   && $_REQUEST['blank2_13']=='' && $_REQUEST['blank2_14']	=='' 	&& $_REQUEST['blank2_15']	=='' && $_REQUEST['blank2_16']	=='' 
		   && $_REQUEST['blank2_17']=='' && $_REQUEST['blank2_18']	=='' 	&& $_REQUEST['blank2_19']	=='' && $_REQUEST['blank2_20']	==''

		   && $_REQUEST['blank3_1']	=='' && $_REQUEST['blank3_2']	==''   	&& $_REQUEST['blank3_3']	=='' && $_REQUEST['blank3_4']	==''
		   && $_REQUEST['blank3_5']	=='' && $_REQUEST['blank3_6']	==''	&& $_REQUEST['blank3_7']	=='' && $_REQUEST['blank3_8']	==''	
		   && $_REQUEST['blank3_9']	=='' && $_REQUEST['blank3_10']	==''  	&& $_REQUEST['blank3_11']	=='' && $_REQUEST['blank3_12']	=='' 
		   && $_REQUEST['blank3_13']=='' && $_REQUEST['blank3_14']	=='' 	&& $_REQUEST['blank3_15']	=='' && $_REQUEST['blank3_16']	=='' 
		   && $_REQUEST['blank3_17']=='' && $_REQUEST['blank3_18']	=='' 	&& $_REQUEST['blank3_19']	=='' && $_REQUEST['blank3_20']	==''
		   
		   && $_REQUEST['blank4_1']	=='' && $_REQUEST['blank4_2']	==''   	&& $_REQUEST['blank4_3']	=='' && $_REQUEST['blank4_4']	==''
		   && $_REQUEST['blank4_5']	=='' && $_REQUEST['blank4_6']	==''	&& $_REQUEST['blank4_7']	=='' && $_REQUEST['blank4_8']	==''	
		   && $_REQUEST['blank4_9']	=='' && $_REQUEST['blank4_10']	==''  	&& $_REQUEST['blank4_11']	=='' && $_REQUEST['blank4_12']	=='' 
		   && $_REQUEST['blank4_13']=='' && $_REQUEST['blank4_14']	=='' 	&& $_REQUEST['blank4_15']	=='' && $_REQUEST['blank4_16']	=='' 
		   && $_REQUEST['blank4_17']=='' && $_REQUEST['blank4_18']	=='' 	&& $_REQUEST['blank4_19']	=='' && $_REQUEST['blank4_20']	==''
		   
		   && $_REQUEST['propofol_1']	=='' && $_REQUEST['propofol_2']	==''   	&& $_REQUEST['propofol_3']	=='' && $_REQUEST['propofol_4']	==''
		   && $_REQUEST['propofol_5']	=='' && $_REQUEST['propofol_6']	==''	&& $_REQUEST['propofol_7']	=='' && $_REQUEST['propofol_8']	==''	
		   && $_REQUEST['propofol_9']	=='' && $_REQUEST['propofol_10']==''  	&& $_REQUEST['propofol_11']	=='' && $_REQUEST['propofol_12']=='' 
		   && $_REQUEST['propofol_13']	=='' && $_REQUEST['propofol_14']=='' 	&& $_REQUEST['propofol_15']	=='' && $_REQUEST['propofol_16']=='' 
		   && $_REQUEST['propofol_17']	=='' && $_REQUEST['propofol_18']=='' 	&& $_REQUEST['propofol_19']	=='' && $_REQUEST['propofol_20']==''
		   
		   && $_REQUEST['midazolam_1']	=='' && $_REQUEST['midazolam_2'] ==''   && $_REQUEST['midazolam_3']	=='' && $_REQUEST['midazolam_4'] ==''
		   && $_REQUEST['midazolam_5']	=='' && $_REQUEST['midazolam_6'] ==''	&& $_REQUEST['midazolam_7']	=='' && $_REQUEST['midazolam_8'] ==''	
		   && $_REQUEST['midazolam_9']	=='' && $_REQUEST['midazolam_10']==''  	&& $_REQUEST['midazolam_11']=='' && $_REQUEST['midazolam_12']=='' 
		   && $_REQUEST['midazolam_13']	=='' && $_REQUEST['midazolam_14']=='' 	&& $_REQUEST['midazolam_15']=='' && $_REQUEST['midazolam_16']=='' 
		   && $_REQUEST['midazolam_17']	=='' && $_REQUEST['midazolam_18']=='' 	&& $_REQUEST['midazolam_19']=='' && $_REQUEST['midazolam_20']==''
		   
		   && $_REQUEST['Fentanyl_1']	=='' && $_REQUEST['Fentanyl_2'] ==''   && $_REQUEST['Fentanyl_3']  =='' && $_REQUEST['Fentanyl_4'] ==''
		   && $_REQUEST['Fentanyl_5']	=='' && $_REQUEST['Fentanyl_6'] ==''	&& $_REQUEST['Fentanyl_7'] =='' && $_REQUEST['Fentanyl_8'] ==''	
		   && $_REQUEST['Fentanyl_9']	=='' && $_REQUEST['Fentanyl_10']==''  	&& $_REQUEST['Fentanyl_11']=='' && $_REQUEST['Fentanyl_12']=='' 
		   && $_REQUEST['Fentanyl_13']	=='' && $_REQUEST['Fentanyl_14']=='' 	&& $_REQUEST['Fentanyl_15']=='' && $_REQUEST['Fentanyl_16']=='' 
		   && $_REQUEST['Fentanyl_17']	=='' && $_REQUEST['Fentanyl_18']=='' 	&& $_REQUEST['Fentanyl_19']=='' && $_REQUEST['Fentanyl_20']==''
		  
		   && $_REQUEST['ketamine_1']	=='' && $_REQUEST['ketamine_2'] ==''   && $_REQUEST['ketamine_3']  =='' && $_REQUEST['ketamine_4'] ==''
		   && $_REQUEST['ketamine_5']	=='' && $_REQUEST['ketamine_6'] ==''	&& $_REQUEST['ketamine_7'] =='' && $_REQUEST['ketamine_8'] ==''	
		   && $_REQUEST['ketamine_9']	=='' && $_REQUEST['ketamine_10']==''  	&& $_REQUEST['ketamine_11']=='' && $_REQUEST['ketamine_12']=='' 
		   && $_REQUEST['ketamine_13']	=='' && $_REQUEST['ketamine_14']=='' 	&& $_REQUEST['ketamine_15']=='' && $_REQUEST['ketamine_16']=='' 
		   && $_REQUEST['ketamine_17']	=='' && $_REQUEST['ketamine_18']=='' 	&& $_REQUEST['ketamine_19']=='' && $_REQUEST['ketamine_20']==''

		   && $_REQUEST['labetalol_1']	=='' && $_REQUEST['labetalol_2'] ==''   && $_REQUEST['labetalol_3']  =='' && $_REQUEST['labetalol_4'] ==''
		   && $_REQUEST['labetalol_5']	=='' && $_REQUEST['labetalol_6'] ==''	&& $_REQUEST['labetalol_7'] =='' && $_REQUEST['labetalol_8'] ==''	
		   && $_REQUEST['labetalol_9']	=='' && $_REQUEST['labetalol_10']==''  	&& $_REQUEST['labetalol_11']=='' && $_REQUEST['labetalol_12']=='' 
		   && $_REQUEST['labetalol_13']	=='' && $_REQUEST['labetalol_14']=='' 	&& $_REQUEST['labetalol_15']=='' && $_REQUEST['labetalol_16']=='' 
		   && $_REQUEST['labetalol_17']	=='' && $_REQUEST['labetalol_18']=='' 	&& $_REQUEST['labetalol_19']=='' && $_REQUEST['labetalol_20']==''

		   && $_REQUEST['spo2_1']	=='' && $_REQUEST['spo2_2'] ==''    && $_REQUEST['spo2_3'] =='' && $_REQUEST['spo2_4'] ==''
		   && $_REQUEST['spo2_5']	=='' && $_REQUEST['spo2_6'] ==''	&& $_REQUEST['spo2_7'] =='' && $_REQUEST['spo2_8'] ==''	
		   && $_REQUEST['spo2_9']	=='' && $_REQUEST['spo2_10']==''  	&& $_REQUEST['spo2_11']=='' && $_REQUEST['spo2_12']=='' 
		   && $_REQUEST['spo2_13']	=='' && $_REQUEST['spo2_14']=='' 	&& $_REQUEST['spo2_15']=='' && $_REQUEST['spo2_16']=='' 
		   && $_REQUEST['spo2_17']	=='' && $_REQUEST['spo2_18']=='' 	&& $_REQUEST['spo2_19']=='' && $_REQUEST['spo2_20']==''

		   && $_REQUEST['o2lpm_1']	=='' && $_REQUEST['o2lpm_2'] ==''   && $_REQUEST['o2lpm_3'] =='' && $_REQUEST['o2lpm_4'] ==''
		   && $_REQUEST['o2lpm_5']	=='' && $_REQUEST['o2lpm_6'] ==''	&& $_REQUEST['o2lpm_7'] =='' && $_REQUEST['o2lpm_8'] ==''	
		   && $_REQUEST['o2lpm_9']	=='' && $_REQUEST['o2lpm_10']==''  	&& $_REQUEST['o2lpm_11']=='' && $_REQUEST['o2lpm_12']=='' 
		   && $_REQUEST['o2lpm_13']	=='' && $_REQUEST['o2lpm_14']=='' 	&& $_REQUEST['o2lpm_15']=='' && $_REQUEST['o2lpm_16']=='' 
		   && $_REQUEST['o2lpm_17']	=='' && $_REQUEST['o2lpm_18']=='' 	&& $_REQUEST['o2lpm_19']=='' && $_REQUEST['o2lpm_20']==''
		  
		  )
	   /*
	   || ($_REQUEST['chbx_enable_postop_desc']!='' 
		   && ($_REQUEST['chbx_vss']=='' 
			   || $_REQUEST['chbx_atsf']=='' 
			   || $_REQUEST['chbx_pa']=='' 
			   || $_REQUEST['chbx_nausea']=='' 
			   || $_REQUEST['chbx_vomiting']=='' 
			   || $_REQUEST['chbx_dizziness']=='' 
			   || $_REQUEST['chbx_rd']=='' 
			   || $_REQUEST['chbx_aao']=='' 
			   || $_REQUEST['chbx_ddai']=='' 
			   || $_REQUEST['chbx_pv']=='' 
			   || $_REQUEST['chbx_rtpog']=='' 
			   || $_REQUEST['chbx_pain']==''
			  )
		  )*/ 
		  
	  )
	  {
		
		$formStatus = 'not completed';
	  }
		
		 //check dow we need to make these fields compulsory
		if($_SESSION['loginUserType']!='Anesthesiologist')
		{
			if(($_REQUEST['chbx_anes']=='' && $_REQUEST['chbx_pulm']=='' && $_REQUEST['chbx_dis']=='')  || trim($_REQUEST['evaluation'])=='')
			{
				$formStatus = 'not completed';
			}
		}
		
		
		// Validate Chart if form version no. is 2 
		if($version_num > 1)
		{
			if(		 $_REQUEST['chbx_ipp']=='' || $_REQUEST['chbx_smpp']=='' 
					|| $_REQUEST['chbx_pa']==''  || $_REQUEST['chbx_dar']=='' 
					|| $_REQUEST['chbx_asc']=='' || $_REQUEST['chbx_adcpc']=='' 
					|| $_REQUEST['chbx_rbl'] == ''
					|| ( $chk_signAnesthesia4Id=="0" && $confirmAnes_NA!='Yes') 
					)
			{
					$formStatus = 'not completed';
			}
			
		}
		
	}
	  
	//END CODE TO SET FORM STATUS
	
	//START CODE TO RESET THE RECORD
	if($_REQUEST['hiddResetStatusId']=='Yes') {
		$formStatus									= '';
		$arrayRecord['applet_data'] 				= '';
		$arrayRecord['resetDateTime'] 				= date('Y-m-d H:i:s');
		$arrayRecord['resetBy'] 					= $_SESSION['loginUserId'];

		$arrayRecord['signAnesthesia1Id'] 			= '';
		$arrayRecord['signAnesthesia1FirstName'] 	= '';
		$arrayRecord['signAnesthesia1MiddleName'] 	= '';
		$arrayRecord['signAnesthesia1LastName'] 	= '';
		$arrayRecord['signAnesthesia1Status'] 		= '';
		$arrayRecord['signAnesthesia1DateTime'] 	= '0000-00-00 00:00:00';
		
		$arrayRecord['signAnesthesia2Id'] 			= '';
		$arrayRecord['signAnesthesia2FirstName'] 	= '';
		$arrayRecord['signAnesthesia2MiddleName'] 	= '';
		$arrayRecord['signAnesthesia2LastName'] 	= '';
		$arrayRecord['signAnesthesia2Status'] 		= '';
		$arrayRecord['signAnesthesia2DateTime'] 	= '0000-00-00 00:00:00';
		
		$arrayRecord['signAnesthesia3Id'] 			= '';
		$arrayRecord['signAnesthesia3FirstName'] 	= '';
		$arrayRecord['signAnesthesia3MiddleName'] 	= '';
		$arrayRecord['signAnesthesia3LastName'] 	= '';
		$arrayRecord['signAnesthesia3Status'] 		= '';
		$arrayRecord['signAnesthesia3DateTime'] 	= '0000-00-00 00:00:00';
		
		$arrayRecord['signAnesthesia4Id'] 				= '';
		$arrayRecord['signAnesthesia4FirstName'] 	= '';
		$arrayRecord['signAnesthesia4MiddleName'] = '';
		$arrayRecord['signAnesthesia4LastName'] 	= '';
		$arrayRecord['signAnesthesia4Status'] 		= '';
		$arrayRecord['signAnesthesia4DateTime'] 	= '0000-00-00 00:00:00';
		
		$arrayRecord['anes_ScanUpload'] 			= '';
		$arrayRecord['anes_ScanUploadType'] 		= '';
		$arrayRecord['anes_ScanStatus'] 			= '';
		$arrayRecord['anes_ScanUploadName'] 		= '';
		$arrayRecord['anes_ScanUploadPath'] 		= '';
		$arrayRecord['anes_ScanUploadDateTime'] 	= '';
		
	
		if($chk_anes_ScanUploadPath) {//delete scan/upload in case to Reset this page
			if(file_exists($chk_anes_ScanUploadPath)) {
				unlink($chk_anes_ScanUploadPath);	
			}
		}
	}
	//END CODE TO RESET THE RECORD
	
	// Start Saving Admin Additional Qustions
	if($version_num > 3) { // Save Only if form version is greater than 3
		$patQuesIDArr = $_POST['pat_ques_id'];
		if( is_array($patQuesIDArr) && count($patQuesIDArr) > 0 ) {
			foreach($patQuesIDArr as $qkey => $patQuesID ){

				$ques = $_POST['ques'][$qkey];
				$ftype = $_POST['ftype'][$qkey];
				$dtype = $_POST['dtype'][$qkey];
				$listOptions = ($ftype == 4) ? $_POST['list_options'][$qkey] : '';

				$answer = $_POST['ques_fld'][$qkey];
				$answer = is_array($answer) ? implode(";",$answer) : $answer;

				$ques = addslashes($ques);
				$answer = addslashes($answer);
				$listOptions = addslashes($listOptions);
				
				$chkQuery = "Select * From patient_mac_regional_questions Where confirmation_id = ".(int)$pConfId." And question = '".$ques."' ".($patQuesID ? " And id <> ".(int)$patQuesID." " : '');
				$chkSql = imw_query($chkQuery) or die($chkQuery.': '.imw_error() );
				$chkCnt = imw_num_rows($chkSql);
				
				if( $chkCnt > 0 ) {
					// Skip this question 
				}	
				else {
					if( $patQuesID ) {
						$sQry = "Update patient_mac_regional_questions SET answer = '".$answer."', modified_on = '".date('Y-m-d H:i:s')."', modified_by = ".(int)$_SESSION['loginUserId']." Where confirmation_id = ".(int)$pConfId." And id = ".(int)$patQuesID;
					} else {
						$sQry = "Insert Into patient_mac_regional_questions Set confirmation_id = ".(int)$pConfId.",  question = '".$ques."', f_type = '".$ftype."', d_type = '".$dtype."', list_options = '".$listOptions."', answer = '".$answer."', created_on = '".date('Y-m-d H:i:s')."', created_by = ".(int)$_SESSION['loginUserId']." ";
					}


					$r = imw_query($sQry) or die('Error in query @ line no. '.(__LINE__).' - '.$sQry.': '.imw_error());
				}

			}

		}
	}
	// End Saving Admin Additional Qustions
	$arrayRecord['form_status'] = $formStatus;
	if((int)$_REQUEST["hidEnableHTMLGrid"] == 1){
		if(empty($strGridImagePath) == false){
			if(file_exists($strGridImagePath) == true){
				unlink($strGridImagePath);
			}
		}		
		$imagePath = "sx_grid_images";
		$patientDir = "/PatientId_".$_REQUEST["patient_id"]."";
		$patientConfDir = "/PatientConfId_".$_REQUEST["pConfId"]."";			
		if(is_dir($imagePath.$patientDir.$patientConfDir) == false){
			mkdir($imagePath.$patientDir.$patientConfDir, 0777, true);
		}			
		$gridImgData = str_replace("data:image/png;base64,","",$_REQUEST['hidGridImgData']);
		$drawingFileName = "/ans_grid_".date("YmdHsi")."_".session_id().".png";
		$drawingFilePath = $imagePath.$patientDir.$patientConfDir.$drawingFileName;
		file_put_contents($drawingFilePath, base64_decode($gridImgData));
		
		$bakImgResource = imagecreatefromjpeg("sc-grid/images/bgTest.jpg");
		$canvasImgResource = imagecreatefrompng($drawingFilePath);										
		imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, 1001, 369);		
		
		//ADD Time Bar HERE --
		if(!empty($startTime)){
			//03:32A _ 3:30
			$startTime = str_replace(array("A","P"), "", $startTime);
			$startTime_arr = explode(":",$startTime);
			$start_time_hr = trim($startTime_arr[0]);
			$start_time_min = trim($startTime_arr[1]);
			if($start_time_min<=15){$start_time_min="0";}
			if($start_time_min>=30&&$start_time_min<=45){$start_time_min="30";}
			if($start_time_min>=45){$start_time_min="45";}
			
			$pw=29-15;
			$ph=1;
			$pd=48;//76-29;
			
			//$im = @imagecreatefromjpeg("bgTest.jpg");
			// White background and blue text			
			$tmp_textcolor = imagecolorallocate($bakImgResource, 0, 0, 0);
			
			// Write the string at the top left
			for($i=0,$w=0;$i<22;$i++){
				
				if($start_time_hr <"10" ){  $start_time_hr="0".(int)$start_time_hr; }
				if($start_time_min == "0"){  $start_time_min="00"; }
				$tmp_txt = $start_time_hr.":".$start_time_min;
				
				imagestring($bakImgResource, 1, $pw, $ph, $tmp_txt, $tmp_textcolor);
				
				$pw = (int)$pw+(int)$pd;
				
				//echo "\n".$tmp_txt;
				
				$start_time_min = (int)$start_time_min + 15;
				
				if($start_time_min == "60"){
					$start_time_hr = (int)$start_time_hr+1;
					$start_time_min ="0";
				}
				
				if($start_time_hr == "13"){$start_time_hr = "1";}
				
			}
		
		}
		
		//ADD Time Bar HERE --
				
		imagejpeg($bakImgResource, $drawingFilePath); 
		$drawingFileNameJ = str_replace(".png", ".jpeg", $drawingFileName);
		rename($imagePath.$patientDir.$patientConfDir.$drawingFileName, $imagePath.$patientDir.$patientConfDir.$drawingFileNameJ);
		imagedestroy($bakImgResource);
		imagedestroy($canvasImgResource);
		$arrayRecord['html_grid_data'] = $_REQUEST["hidAnesthesiaGridData"];
		$arrayRecord['grid_image_path'] = $imagePath.$patientDir.$patientConfDir.$drawingFileNameJ;
	}
	
	if($localAnesthesiaRecordId){
		$objManageData->updateRecords($arrayRecord, 'localanesthesiarecord', 'localAnesthesiaRecordId', $localAnesthesiaRecordId);
	}else{
		$objManageData->addRecords($arrayRecord, 'localanesthesiarecord');
	}
	//START SAVE MED GRID OF ANES GRAPH IN SEPARATE TABLE
	if($med_grid_id){
		$objManageData->updateRecords($arrayRecordMedGrid, 'localanesthesiarecordmedgrid', 'confirmation_id', $pConfId);
	}else{
		$objManageData->addRecords($arrayRecordMedGrid, 'localanesthesiarecordmedgrid');
	}
	if($med_grid_sec_id){
		$objManageData->updateRecords($arrayRecordMedGridSec, 'localanesthesiarecordmedgridsec', 'confirmation_id', $pConfId);
	}else{
		$objManageData->addRecords($arrayRecordMedGridSec, 'localanesthesiarecordmedgridsec');
	}
	//END SAVE MED GRID OF ANES GRAPH IN SEPARATE TABLE
	
	//MAKE AUDIT STATUS REPORT
	unset($arrayStatusRecord);
	$arrayStatusRecord['user_id'] = $_SESSION['loginUserId'];
	$arrayStatusRecord['patient_id'] = $_SESSION['patient_id'];
	$arrayStatusRecord['confirmation_id'] = $pConfId;
	$arrayStatusRecord['form_name'] = 'mac_regional_anesthesia_form';
	$arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
	//MAKE AUDIT STATUS REPORT

	//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'mac_regional_anesthesia_form';
		$conditionArr['status'] = 'created';
		$chkAuditStatus = $objManageData->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);	
		if($chkAuditStatus) {
			//MAKE AUDIT STATUS MODIFIED
			$arrayStatusRecord['status'] = 'modified';
		}else {
			//MAKE AUDIT STATUS CREATED
			$arrayStatusRecord['status'] = 'created';
		}
		$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');												
	//CODE END TO SET AUDIT STATUS AFTER SAVE
		
	
	
	//START CODE TO CHANGE ANESTHESIOLOGIST NAME IN HEADER
		$boolChangeAnesName='false';
		if($_REQUEST['hiddSaveByAnes']<>'Yes' && $loggedUserType=='Anesthesiologist' && !$_REQUEST['relivedIntraNurseIdList']) {
			//IF RELIEVED ANESTHESIOLOGIST NOT EXISTS AND 
			//IF RECORD SAVED FIRST TIME BY ANESTHESIOLOGIST THEN CHANGE IT IN HEADER ALSO
			$boolChangeAnesName='true';
			$changeAnesId = $_SESSION["loginUserId"];
		}else if($_REQUEST['relivedIntraNurseIdList']) { //reliveNurseId change into relieveAnesId acc. to new feedback
			//IF RELIEVED ANESTHESIOLOGIST EXISTS THEN CHANGE IT IN HEADER ALSO
			$boolChangeAnesName='true';
			$changeAnesId = $_REQUEST['relivedIntraNurseIdList'];
		}
		if($boolChangeAnesName=='true') { 
			if($changeAnesId) {
				$detailChangeUser = $objManageData->getRowRecord('users', 'usersId ', $changeAnesId);
				if($detailChangeUser) {
					if($detailChangeUser->mname) {
						$changeAnesMiddleName = ' '.$detailChangeUser->mname;
					}
					$changeAnesName = $detailChangeUser->fname.$changeAnesMiddleName.' '.$detailChangeUser->lname;
					$updateChangeAnesNameQry = "update patientconfirmation set anesthesiologist_id = '".$changeAnesId."', anesthesiologist_name = '".$changeAnesName."', anes_NA = '' where patientConfirmationId = '".$pConfId."'";
					$updateChangeAnesNameRes = imw_query($updateChangeAnesNameQry);
					
			?>
				<script>
					//SET ANESTHESIOLOGIST NAME IN HEADER ACCORDING TO LOGGED-IN OR RELIEF ANESTHESIOLOGIST
						var changeAnesName = '<?php echo $changeAnesName;?>';
						if(top.document.getElementById('headerAnesNameID')) {
							top.document.getElementById('headerAnesNameID').innerText=changeAnesName;
						}
					//END SET ANESTHESIOLOGIST NAME IN HEADER ACCORDING TO LOGGED-IN OR RELIEF ANESTHESIOLOGIST
				</script>
			<?php
				}
			}		
		}
	//START CODE TO CHANGE ANESTHESIOLOGIST NAME IN HEADER	
	
	//CODE START TO UPDATE ASSIST BY TRANSLATOR IN HEADER
	 $chbx_assistUpdate = $_POST['chbx_assist'];
	 if($chbx_assistUpdate=="") {
		 $chbx_assistUpdate = "no";
	 }
	 $updateAssistByTranslatorQry = "update patientconfirmation set assist_by_translator = '".$chbx_assistUpdate."' where patientConfirmationId = '$pConfId'";
	 $updateAssistByTranslatorRes = imw_query($updateAssistByTranslatorQry);
	 
	//CODE END TO UPDATE ASSIST BY TRANSLATOR IN HEADER
	
	
	//CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 	
	$patient_id = ($_REQUEST['patient_id']!=0)?$_REQUEST['patient_id']:11;
	
	//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
		$updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateAnesStubTblRes = imw_query($updateAnesStubTblQry) or die(imw_error());
	//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
	
	if($vitalSignGridStatus)
	{
			// Code start here to save vital sign grid data 
			$vitalSignGridRecordIdArr	=	$_POST['vitalSignGridRecordId'] ;
			
			if(is_array($vitalSignGridRecordIdArr) && count($vitalSignGridRecordIdArr) > 0 )
			{
				foreach($vitalSignGridRecordIdArr as $key => $gridRowId)	
				{
						$row		=	$key +1;
						$vTime		=	$_POST['vitalSignGrid_'.$row.'_1'];
						$vSystolic	=	htmlentities(addslashes($_POST['vitalSignGrid_'.$row.'_2']));//$vBp
						$vDiastolic	=	htmlentities(addslashes($_POST['vitalSignGrid_'.$row.'_3']));
						$vPulse		=	htmlentities(addslashes($_POST['vitalSignGrid_'.$row.'_4']));
						$vRR			=	htmlentities(addslashes($_POST['vitalSignGrid_'.$row.'_5']));
						$vTemp		=	htmlentities(addslashes($_POST['vitalSignGrid_'.$row.'_6']));
						$vEtco2		=	htmlentities(addslashes($_POST['vitalSignGrid_'.$row.'_7']));
						$vosat2		=	htmlentities(addslashes($_POST['vitalSignGrid_'.$row.'_8']));
						
						if( $vTime && ($vSystolic || $vDiastolic || $vPulse || $vRR || $vTemp || $vEtco2 || $vosat2)  )
						{
							/*
							$timeSplit 		= explode(":",$vTime);
							$timeAmPm 	= strtolower($timeSplit[1][3]);
							if( $timeAmPm == "p" && $timeSplit[0] <> '12'  ) {
								$timeSplit[0] = $timeSplit[0]+12;
							}
							elseif( $timeAmPm == "a" && $timeSplit[0] == '12' )
							{
								$timeSplit[0] = $timeSplit[0]-12;	
							}
							$vTime = $timeSplit[0].":".$timeSplit[1][0].$timeSplit[1][1].":00";
							*/
							$vTime	=	$objManageData->setTmFormat($vTime);
							$dataArray	=	array();
							$dataArray['chartName']			=	'mac_regional_anesthesia_form';
							$dataArray['confirmation_id']	=	$pConfId ;
							$dataArray['start_time']			=	$vTime;
							$dataArray['systolic']				=	$vSystolic;
							$dataArray['diastolic']				=	$vDiastolic;
							$dataArray['pulse']					=	$vPulse;
							$dataArray['rr']						=	$vRR;
							$dataArray['temp']					=	$vTemp;
							$dataArray['etco2']					=	$vEtco2;
							$dataArray['osat2']					=	$vosat2;
							if($gridRowId)
							{
								$objManageData->UpdateRecord($dataArray,'vital_sign_grid','gridRowId',$gridRowId);
							}
							else
							{
								$chkRecords	=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$dataArray);
								if( !$chkRecords)
								$objManageData->addRecords($dataArray,'vital_sign_grid');	
							}
						}
						else
						{
							if($gridRowId)
							{
								$objManageData->DeleteRecord('vital_sign_grid','gridRowId',$gridRowId);	
							}
						}
				
						
				}
			}
			
			// Code end here to save vital sign grid data 
	
	}
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
		/*
		if($formStatus == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
		}else if($formStatus=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
		}*/
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
	
}

?>


<script type="text/javascript">
	function changeSliderColor(){
		var setColor = '<?php echo $bglight_blue_local_anes; ?>';
		top.changeColor(setColor);
	}
	//top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');

function showLocalAnesTime(){
	var today=new Date();
    var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	var hid_tm = h+":"+m+":"+s;
	var dn="PM"
	if (h<12)
		dn="AM"
	if (h>12)
		h=h-12
	if (h==0)
		h=12
	if(h<10) h='0'+h
		m=checkTime1(m);
	t=h+":"+m+" "+dn;
   document.getElementById('bp_temp98').value=t;
   document.getElementById('currTime').value=t;
}
	//Applet
function get_App_Coords(objElem, id){
	var coords,appName;
	var objElemSign = document.getElementById('elem_signature'+id);
	appName = objElem.name;
	coords = getCoords(appName, id);	
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
function getCoords(appName, id){		
	var coords = document.applets["app_signature"+id].getSign();
	return coords;
}
function getclear_os(id){
	document.applets["app_signature"+id].clearIt();
	changeColorThis(255,0,0, id);
	document.applets["app_signature"+id].onmouseout();
}
function changeColorThis(r,g,b, id){				
	document.applets['app_signature'+id].setDrawColor(r,g,b);								
}
//Drawing Grid
var curIconTd="tdArr2";
function setAppIcon(str){
	var objTd = document.getElementById(str);
	var objCurTd = document.getElementById(curIconTd);
	var objApp = document.applets["signs"];
	if(objTd && objApp && (str != curIconTd))
	{
		setText("false");
		if(str == "tdArr1"){
			objApp.setIcon("Triangle_Down");
		}else if(str == "tdArr2"){
			objApp.setIcon("Triangle_Up");
		}else if(str == "tdRfill"){
			objApp.setIcon("Circle_Black");
		}else if(str == "tdRblank"){
			objApp.setIcon("Circle_White");
		}else if(str == "tdCross"){
			objApp.setIcon("Cross_Shape");
		}else if(str == "tdRText"){
			setText("true");
		}	
		curIconTd = str;
		objTd.style.backgroundColor = "#FFFFCC";
		objCurTd.style.backgroundColor = objCurTd.bgColor;
	}
}


function clearApp()
{
	if(confirm("Do you want to clear drawing?")){
		document.applets["signs"].clearIt();
		getAppValue();
	}
}

function undoApp()
{
	var coords = document.applets["signs"].unDoIt();
	document.getElementById("applet_data").value=coords;
}
function redoApp()
{
	var coords = document.applets["signs"].reDoIt();
	document.getElementById("applet_data").value=coords;
}
function setText(o)
{
	document.applets["signs"].activateText(o);
}
function getAppValue(){
	if(document.getElementById("signs")){
		var coords = document.applets["signs"].getDrawing();
		document.getElementById("applet_data").value=coords;
	}
} 
//Applet



//SHOW TIME INTERVAL
	function showInterval(objvalue,intervalId_td) {
		var startTime = objvalue;
		if(startTime.substr(2,1)==":") {
			if(startTime.length==6) {
				startTimeSplit = startTime.split(":");
				if(startTimeSplit[0].length==2 && startTimeSplit[1].length==3) {
					var startHours = startTime.substr(0,2);
					var startMins = startTime.substr(3,2);
					var startAmPm = startTime.substr(5,1);
					
					var startHoursPlusOne = parseInt(startHours)+1;
					var startHoursPlusTwo = parseInt(startHours)+2;
					var startHoursPlusThree = parseInt(startHours)+3;
					
					if(startHours == 8) {
						startHoursPlusOne = 9;
						startHoursPlusTwo = 10;
						startHoursPlusThree = 11;
					}
					if(startHours == 9) {
						startHoursPlusOne = 10;
						startHoursPlusTwo = 11;
						startHoursPlusThree = 12;
					}
					if(startHoursPlusOne > 12) {
						startHoursPlusOne = startHoursPlusOne-12;
					}
					if(startHoursPlusTwo > 12) {
						startHoursPlusTwo = startHoursPlusTwo-12;
					}
					if(startHoursPlusThree > 12) {
						startHoursPlusThree = startHoursPlusThree-12;
					}
					
					if(startHoursPlusOne<10) {
						startHoursPlusOne = "0"+startHoursPlusOne;
					}
					if(startHoursPlusTwo<10) {
						startHoursPlusTwo = "0"+startHoursPlusTwo;
					}
					if(startHoursPlusThree<10) {
						startHoursPlusThree = "0"+startHoursPlusThree;
					}
					var intervalTimeMin1;
					var intervalTimeMin2;
					var intervalTimeMin3;
					var intervalTimeMin4;
					var intervalTimeMin5;
					var intervalTimeMin6;
					var intervalTimeMin7;
					var intervalTimeMin8;
					var intervalTimeMin9;
					var intervalTimeMin10;
					
				


					if(startMins< 15) {
						intervalTimeMin1 = startHours+":00";
						intervalTimeMin2 = startHours+":15";
						intervalTimeMin3 = startHours+":30";
						intervalTimeMin4 = startHours+":45";
						intervalTimeMin5 = startHoursPlusOne+":00";
						intervalTimeMin6 = startHoursPlusOne+":15";
						intervalTimeMin7 = startHoursPlusOne+":30";
						intervalTimeMin8 = startHoursPlusOne+":45";
						intervalTimeMin9 = startHoursPlusTwo+":00";
						intervalTimeMin10 = startHoursPlusTwo+":15";
						
					}else if(startMins>=15 && startMins< 30) {
						intervalTimeMin1 = startHours+":15";
						intervalTimeMin2 = startHours+":30";
						intervalTimeMin3 = startHours+":45";
						intervalTimeMin4 = startHoursPlusOne+":00";
						intervalTimeMin5 = startHoursPlusOne+":15";
						intervalTimeMin6 = startHoursPlusOne+":30";
						intervalTimeMin7 = startHoursPlusOne+":45";
						intervalTimeMin8 = startHoursPlusTwo+":00";
						intervalTimeMin9 = startHoursPlusTwo+":15";
						intervalTimeMin10 = startHoursPlusTwo+":30";
						
					}else if(startMins>=30 && startMins< 45) {
						intervalTimeMin1 = startHours+":30";
						intervalTimeMin2 = startHours+":45";
						intervalTimeMin3 = startHoursPlusOne+":00";
						intervalTimeMin4 = startHoursPlusOne+":15";
						intervalTimeMin5 = startHoursPlusOne+":30";
						intervalTimeMin6 = startHoursPlusOne+":45";
						intervalTimeMin7 = startHoursPlusTwo+":00";
						intervalTimeMin8 = startHoursPlusTwo+":15";
						intervalTimeMin9 = startHoursPlusTwo+":30";
						intervalTimeMin10 = startHoursPlusTwo+":45";
						
					}else if(startMins>=45) {
						intervalTimeMin1 = startHours+":45";
						intervalTimeMin2 = startHoursPlusOne+":00";
						intervalTimeMin3 = startHoursPlusOne+":15";
						intervalTimeMin4 = startHoursPlusOne+":30";
						intervalTimeMin5 = startHoursPlusOne+":45";
						intervalTimeMin6 = startHoursPlusTwo+":00";
						intervalTimeMin7 = startHoursPlusTwo+":15";
						intervalTimeMin8 = startHoursPlusTwo+":30";
						intervalTimeMin9 = startHoursPlusTwo+":45";
						intervalTimeMin10 = startHoursPlusThree+":00";
					}			
				
					var showStartIntervalTime;
						showStartIntervalTime='<img src="images/tpixel.gif" width="25" height="1" />';
						showStartIntervalTime+= intervalTimeMin1;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="10" height="1" />';
						showStartIntervalTime+=intervalTimeMin2;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="10" height="1" />';
						showStartIntervalTime+=intervalTimeMin3;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin4;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="12" height="1" />';
						showStartIntervalTime+=intervalTimeMin5;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="12" height="1" />';
						showStartIntervalTime+=intervalTimeMin6;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin7;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin8;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="11" height="1" />';
						showStartIntervalTime+=intervalTimeMin9;
						showStartIntervalTime+='<img src="images/tpixel.gif" width="10" height="1" />';
						showStartIntervalTime+=intervalTimeMin10;
						
						if(document.getElementById(intervalId_td).style.display=='none') {
							document.getElementById(intervalId_td).style.display='block';
							document.getElementById(intervalId_td).innerHTML = showStartIntervalTime;
						}else {
							document.getElementById(intervalId_td).style.display='none';
						}
				}
				
			}
		}		
	}
//SHOW TIME INTERVAL
function saveTimeData(s)
{
	var startTime = s;
	if(startTime.substr(2,1)==":") {
		if(startTime.length==6) {
			startTimeSplit = startTime.split(":");
			if(startTimeSplit[0].length==2 && startTimeSplit[1].length==3) {
				
				//var s = document.getElementById("time_status").value;
				var tdVal = document.getElementById("applet_ids");
				tdVal.style.display='block';
				if(s) {
					//tdVal.innerHTML = "<applet name=\"test_intrval\" id=\"paint1\" code=\"TimeApplet.class\" archive=\"Time.jar\"  height=\"20\" width=\"980\"><param name = \"time\" value =\""+s+"\" ></applet>";
					var apt = "<applet name=\"test_intrval\" id=\"paint1\" code=\"TimeApplet.class\" codebase=\"common/applet/\"archive=\"Time.jar\" height=\"20\" width=\"985\"  ><param name = \"time\" value =\""+s+"\"></applet>";
					tdVal.innerHTML=apt;
					var st1 = document.applets("test_intrval").getAppletTime();
					document.getElementById("applet_time_interval").value = st1;
					tdVal.style.display='none';
				}
			}
		}
	}			
}

function checkTimeNew(s)
{
	
	var startTime = s;
	if(s) {
		if(startTime.substr(2,1)==":") {
			if(startTime.length==6) {
				startTimeSplit = startTime.split(":");
				if(startTimeSplit[0].length==2 && startTimeSplit[1].length==3) {
					var tdVal = document.getElementById("applet_ids");
					tdVal.style.display='block';
					
					var apt = "<applet name=\"test_intrval\" id=\"paint1\" code=\"TimeApplet.class\" codebase=\"common/applet/\"archive=\"Time.jar\" height=\"20\" width=\"985\" ><param name = \"time\" value =\""+s+"\"></applet>";
						tdVal.innerHTML=apt;
					var timeIntervalLocal = document.applets("test_intrval").getAppletTime();
					document.getElementById("applet_time_interval").value = timeIntervalLocal;
					
				}
			}
		}
	}else{
		alert('Please set Start Time');
	}				
}	


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
	
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {
		
		//START TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			var signCheck='true';
			var assignedAnesthesiaId = '<?php echo $localAnesAssignedAnesthesiaId;?>';
			var assignedAnesthesiaName = '<?php echo addslashes($localAnesAssignedAnesthesiaName);?>';
			var loggedInUserType = '<?php echo $loggedUserType;?>';
			var assignedSubAnesType = '<?php echo $localAssignedSubAnesType;?>';
			
			var assignedPrefix = 'Dr.';
			if(assignedSubAnesType=='CRNA') { assignedPrefix = ''; }
			
			if(loggedInUserId!=assignedAnesthesiaId && !delSign && loggedInUserType=='Surgeon') {
				var rCheck = confirmOtherAnesthesiologist("This patient is registered to "+assignedPrefix+" "+assignedAnesthesiaName+"\t\t\t\tAre you sure you want to sign the Chart notes of this patient");
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
		
			//SET HIDDEN FIELD (hidd_chkDisplayAnesthesiaSign) TO TRUE AT MAINPAGE
			if(userIdentity=='Anesthesia1' || userIdentity=='Anesthesia2' || userIdentity=='Anesthesia3' || userIdentity=='Anesthesia4'){
				if(top.document.forms[0]){
					if(top.document.forms[0].hidd_chkDisplayAnesthesiaSign) {
						top.document.forms[0].hidd_chkDisplayAnesthesiaSign.value = 'true';
					}
				}
			}		
			//END SET HIDDEN FIELD (hidd_chkDisplayAnesthesiaSign) TO TRUE AT MAINPAGE
		
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
		var signAnesthesiaIdBackColor1 = '';
		if(userIdentity=='Anesthesia3') {
			//signAnesthesiaIdBackColor1 = '<?php echo ($_SESSION['loginUserType']!='Anesthesiologist')?$signAnesthesiaIdBackColor:$whiteBckGroundColor;?>';
		}
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
		url=url+"&userIdentity="+userIdentity
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		if(delSign) {
			url=url+"&delSign=yes"
		}
		url=url+"&signAnesthesiaIdBackColor="+signAnesthesiaIdBackColor1
		
		url=url+"&preColor="+preColor1;
		
		xmlHttp.onreadystatechange=displayUserSignFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function displayUserSignFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				var objId = document.getElementById('hiddSignatureId').value;
				document.getElementById(objId).innerHTML=xmlHttp.responseText;
				top.frames[0].setPNotesHeight();
			}
	}
	
	//End Display Signature Of Nurse
	
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE 

function scnUpldPosSet() {
	if(document.getElementById('anesthesiaSummaryIolScanBtnId')) {
		if(document.getElementById('scn_pre_op_id')) {
			var scnLeftPos = findPos_X('scn_pre_op_id');
			document.getElementById('anesthesiaSummaryIolScanBtnId').style.position = 'absolute';
			document.getElementById('anesthesiaSummaryIolScanBtnId').style.left=scnLeftPos+'px';
			document.getElementById('anesthesiaSummaryIolScanBtnId').style.top=(findPos_Y('scn_pre_op_id'))+'px';
			
		}
	}
	if(document.getElementById('uploadImageDivId')) {
		if(document.getElementById('fl_pre_op_id')) {
			var flLeftPos = findPos_X('fl_pre_op_id');
			flLeftPos = parseFloat(flLeftPos+80);
			document.getElementById('uploadImageDivId').style.position = 'absolute';
			document.getElementById('uploadImageDivId').style.left=flLeftPos+'px';
			document.getElementById('uploadImageDivId').style.top=(findPos_Y('fl_pre_op_id'))+'px';
		}
	}
	if(document.getElementById('uploadBtnDivId')) {
		if(document.getElementById('upd_pre_op_id')) {
			var upLeftPos = findPos_X('fl_pre_op_id');
			upLeftPos = parseFloat(upLeftPos+370);
			document.getElementById('uploadBtnDivId').style.position = 'absolute';
			document.getElementById('uploadBtnDivId').style.left=upLeftPos+'px';
			document.getElementById('uploadBtnDivId').style.top=(findPos_Y('upd_pre_op_id'))+'px';
		}
	}
	
	if(document.getElementById('delBtnId')) {
		if(document.getElementById('del_btn_id')) {
			var delLeftPos = findPos_X('upd_pre_op_id');
			delLeftPos = parseFloat(delLeftPos+450);
			document.getElementById('delBtnId').style.position = 'absolute';
			document.getElementById('delBtnId').style.left=delLeftPos+'px';
			document.getElementById('delBtnId').style.top=(findPos_Y('del_btn_id'))+'px';
		}
	}
	
}
</script>
<script type="text/javascript" src="js/webtoolkit.aim.js"></script>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>
<?php
if($blEnableHTMLGrid == true){
	?>
	<script src="sc-grid/grid.js"></script>
    <?php
}
?>
<div id="post" style="display:none; position:absolute;"></div>

<?php 
// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
	//GET ASSIST BY TRANSLATOR VALUE
		$patientConfirm_assist_by_translator = $detailConfirmationFinalize->assist_by_translator;
	//END GET ASSIST BY TRANSLATOR VALUE 
	
	//GET AnesthesiologistId
		$patientConfirm_anesthesiologist_id = $detailConfirmationFinalize->anesthesiologist_id;
	//END AnesthesiologistId 
	
// GETTING FINALIZE STATUS
?>

<?php
//FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION
	function calculate_timeFun($MainTime) {
		global $objManageData;
		$time_split = explode(":",$MainTime);
		if($time_split[0]=='24') { //to correct previously saved records
			$MainTime = "12".":".$time_split[1].":".$time_split[2];
		}
		if($MainTime == "00:00:00") {
			$MainTime = "";
		}else {
			$MainTime = $objManageData->getTmFormat($MainTime);//date('h:iA',strtotime($MainTime));
			//$MainTime = substr($MainTime,0,-1);
		}
		/*
		$time_split = explode(":",$MainTime);
		if($time_split[0]>12) {
			$am_pm = "P";
		}else {
			$am_pm = "A";
		}
		if($time_split[0]>=13) {
			$time_split[0] = $time_split[0]-12;
			if(strlen($time_split[0]) == 1) {
				$time_split[0] = "0".$time_split[0];
			}
		}else {
			//DO NOTHNING
		}
		if($time_split[0]<>"00") {
			$MainTime = $time_split[0].":".$time_split[1].$am_pm;
		}else {
			$MainTime = "";
		}*/
		return $MainTime;
	}	
//END FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION

//START DISPLAY FP EXAM PERFORMED BY DEFAULT
$fpExamPerformedVisibility='visible';
//END DISPLAY FP EXAM PERFORMED BY DEFAULT
// GETTTING LOCAL ANES RECORD IF EXISTS
	$localAnesRecordDetails = $objManageData->getExtractRecord('localanesthesiarecord', 'confirmation_id', $pConfId, " *, date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat , date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat , date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat, date_format(signAnesthesia4DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia4DateTimeFormat ");
		$localanesFormStatus='';
		if($localAnesRecordDetails){
			$localAnesRecordDetails = array_map('stripslashes',$localAnesRecordDetails);
			extract($localAnesRecordDetails);
			
			$vitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($form_status,$vitalSignGridStatus,'macAnes');
			
			if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
			else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	$current_form_version; }
			$orStartTime=calculate_timeFun($orStartTime); //CODE TO DISPLAY OR START TIME
			$orStopTime=calculate_timeFun($orStopTime); //CODE TO DISPLAY OR STOP TIME
			$startTime=calculate_timeFun($startTime); //CODE TO DISPLAY START TIME
			$stopTime=calculate_timeFun($stopTime); //CODE TO DISPLAY STOP TIME
			$newStartTime1=calculate_timeFun($newStartTime1); //CODE TO DISPLAY New START TIME 1
			$newStopTime1=calculate_timeFun($newStopTime1); //CODE TO DISPLAY New STOP TIME 1
			$newStartTime2=calculate_timeFun($newStartTime2); //CODE TO DISPLAY New START TIME 2
			$newStopTime2=calculate_timeFun($newStopTime2); //CODE TO DISPLAY New STOP TIME 2
			$newStartTime3=calculate_timeFun($newStartTime3); //CODE TO DISPLAY New START TIME 3
			$newStopTime3=calculate_timeFun($newStopTime3); //CODE TO DISPLAY New STOP TIME 3
			$localanesFormStatus = $form_status;
			
			$localAnesRecordDetailsMedGrid = $objManageData->getExtractRecord('localanesthesiarecordmedgrid', 'confirmation_id', $pConfId);
			if($localAnesRecordDetailsMedGrid){
				$localAnesRecordDetailsMedGrid = array_map('stripslashes',$localAnesRecordDetailsMedGrid);
				extract($localAnesRecordDetailsMedGrid);
			}
			$localAnesRecordDetailsMedGridSec = $objManageData->getExtractRecord('localanesthesiarecordmedgridsec', 'confirmation_id', $pConfId);
			if($localAnesRecordDetailsMedGridSec){
				$localAnesRecordDetailsMedGridSec = array_map('stripslashes',$localAnesRecordDetailsMedGridSec);
				extract($localAnesRecordDetailsMedGridSec);
			}
		}
// GETTTING LOCAL ANES RECORD IF EXISTS  

//GET DEFUALT VALUES OF ASSIGNED ANESTHEOLOGIST FROM ADMIN PANEL
	if($form_status=="") {	
		//START GET EKG MEDICATION 
		$getEkgAdminTblDetails 	= $objManageData->getRowRecord('anes_ekg_admin_tbl', 'anes_ekg_admin_id', '1');
		$mgPropofol_label 		= $getEkgAdminTblDetails->mgPropofol_label;
		$mgMidazolam_label 		= $getEkgAdminTblDetails->mgMidazolam_label;
		$mgKetamine_label 		= $getEkgAdminTblDetails->mgKetamine_label;
		$mgLabetalol_label 		= $getEkgAdminTblDetails->mgLabetalol_label;
		$mcgFentanyl_label 		= $getEkgAdminTblDetails->mcgFentanyl_label;
		//END GET EKG MEDICATIOM
		
		$detailAnesthesiaProfile = $objManageData->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId ', $patientConfirm_anesthesiologist_id);
	
		if($detailAnesthesiaProfile) {	 
			
			//$anesthesia_profile_sign = $detailAnesthesiaProfile->anesthesia_profile_sign;
			$anesthesia_profile_sign_path = $detailAnesthesiaProfile->anesthesia_profile_sign_path;

			if(trim($anesthesia_profile_sign_path)) {
				$patientInterviewed 					= $detailAnesthesiaProfile->patientInterviewed;
				$chartNotesReviewed 					= $detailAnesthesiaProfile->chartNotesReviewed;
				$npo									= $detailAnesthesiaProfile->npo;
				$procedurePrimaryVerified 				= $detailAnesthesiaProfile->procedurePrimaryVerified;
				$procedureSecondaryVerified			 	= $detailAnesthesiaProfile->procedureSecondaryVerified;
				$siteVerified 							= $detailAnesthesiaProfile->siteVerified;
				$evaluation2 							= $detailAnesthesiaProfile->evaluation2;
				$dentation 								= $detailAnesthesiaProfile->dentation;
				$stableCardiPlumFunction 				= $detailAnesthesiaProfile->stableCardiPlumFunction;
				$planAnesthesia 						= $detailAnesthesiaProfile->planAnesthesia;
				$allQuesAnswered 						= $detailAnesthesiaProfile->allQuesAnswered;
			
				$routineMonitorApplied 					= $detailAnesthesiaProfile->routineMonitorApplied;	
				$hide_anesthesia_grid 					= $detailAnesthesiaProfile->hide_anesthesia_grid;
				$o2lpm_count 							= $detailAnesthesiaProfile->o2lpm_count;	
				if($o2lpm_count>0) {
					for($t=1;$t<=$o2lpm_count;$t++) {
						$o2lpm_ = "o2lpm_".$t;
						$$o2lpm_ = $detailAnesthesiaProfile->o2lpm_1;
					}
				}
				//echo '<br>'.$o2lpm_1;
				$ekgBigRowValue 						= $detailAnesthesiaProfile->ekgBigRowValue;	
				
				$anyKnowAnestheticComplication 			= $detailAnesthesiaProfile->anyKnowAnestheticComplication;
				$stableCardiPlumFunction2 				= $detailAnesthesiaProfile->stableCardiPlumFunction2;
				$satisfactoryCondition4Discharge 		= $detailAnesthesiaProfile->satisfactoryCondition4Discharge;
				$evaluation 							= $detailAnesthesiaProfile->evaluation;
				$chbx_enable_postop_desc				= $detailAnesthesiaProfile->chbx_enable_postop_desc;
				$chbx_vss 								= $detailAnesthesiaProfile->chbx_vss;
				$chbx_atsf 								= $detailAnesthesiaProfile->chbx_atsf;
				$chbx_pa 								= $detailAnesthesiaProfile->chbx_pa;
				$chbx_nausea 							= $detailAnesthesiaProfile->chbx_nausea;
				$chbx_vomiting 							= $detailAnesthesiaProfile->chbx_vomiting;
				$chbx_dizziness 						= $detailAnesthesiaProfile->chbx_dizziness;
				$chbx_rd 								= $detailAnesthesiaProfile->chbx_rd;
				$chbx_aao 								= $detailAnesthesiaProfile->chbx_aao;
				$chbx_ddai 								= $detailAnesthesiaProfile->chbx_ddai;
				$chbx_pv 								= $detailAnesthesiaProfile->chbx_pv;
				$chbx_rtpog 							= $detailAnesthesiaProfile->chbx_rtpog;
				$chbx_pain 								= $detailAnesthesiaProfile->chbx_pain;
				
				$remarks 								= $detailAnesthesiaProfile->remarks;
			
				$honanballon 							= $detailAnesthesiaProfile->honanballon;	
				$honanBallonAnother 					= $detailAnesthesiaProfile->honanBallonAnother;
				$none 									= $detailAnesthesiaProfile->NoneHonanBalloon;
				$digital 								= $detailAnesthesiaProfile->digital;
				$copyBaseLineVitalSigns 				= $detailAnesthesiaProfile->copyBaseLineVitalSigns ;
				
				$fpExamPerformed		 				= $detailAnesthesiaProfile->fpExamPerformed ;
				if($fpExamPerformed!='Yes') {
					$fpExamPerformedVisibility='hidden';
				}
				$ansComment				 				= $detailAnesthesiaProfile->ansComment ;

				$Block1Aspiration		 				= $detailAnesthesiaProfile->Block1Aspiration ;
				$Block1Full			 					= $detailAnesthesiaProfile->Block1Full ;
				$Block1BeforeInjection	 				= $detailAnesthesiaProfile->Block1BeforeInjection ;
				$Block1RockNegative						= $detailAnesthesiaProfile->Block1RockNegative ;
				$Block2Aspiration		 				= $detailAnesthesiaProfile->Block2Aspiration ;
				$Block2Full			 					= $detailAnesthesiaProfile->Block2Full ;
				$Block2BeforeInjection	 				= $detailAnesthesiaProfile->Block2BeforeInjection ;
				$Block2RockNegative						= $detailAnesthesiaProfile->Block2RockNegative ;
				
				$txtInterOpDrugs1 						= $detailAnesthesiaProfile->txtInterOpDrugs1 ;
				$txtInterOpDrugs2 						= $detailAnesthesiaProfile->txtInterOpDrugs2 ;
				
				$confirmIPPSC_signin		= stripslashes($detailAnesthesiaProfile->confirmIPPSC_signin);
				$siteMarked 						= stripslashes($detailAnesthesiaProfile->siteMarked);
				$patientAllergies 			= stripslashes($detailAnesthesiaProfile->patientAllergies);	
				$difficultAirway				= stripslashes($detailAnesthesiaProfile->difficultAirway);
				$anesthesiaSafety				= stripslashes($detailAnesthesiaProfile->anesthesiaSafety);	
				$allMembersTeam 				= stripslashes($detailAnesthesiaProfile->allMembersTeam);
				$riskBloodLoss					=	stripslashes($detailAnesthesiaProfile->riskBloodLoss);
				$bloodLossUnits					=	stripslashes($detailAnesthesiaProfile->bloodLossUnits);
			}
		}	
				
		//CHECK FOR HONAN BALLON FOR LOGGED IN ANESTHESIOLOGIST
		
		if($logInUserType=='Anesthesiologist') {
			$detailAnesthesiaProfileHonanBallon = $objManageData->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId ', $_SESSION['loginUserId']);
			if($detailAnesthesiaProfileHonanBallon) {	
				//$anesthesia_profile_signHonanBallon = $detailAnesthesiaProfileHonanBallon->anesthesia_profile_sign;
				$anesthesia_profile_sign_path_HonanBallon = $detailAnesthesiaProfileHonanBallon->anesthesia_profile_sign_path;
				if(trim($anesthesia_profile_sign_path_HonanBallon)) {
					
					$patientInterviewed 					= $detailAnesthesiaProfileHonanBallon->patientInterviewed;
					$chartNotesReviewed 					= $detailAnesthesiaProfileHonanBallon->chartNotesReviewed;
					$npo 									= $detailAnesthesiaProfileHonanBallon->npo;
					$procedurePrimaryVerified				= $detailAnesthesiaProfileHonanBallon->procedurePrimaryVerified;
					$procedureSecondaryVerified			 	= $detailAnesthesiaProfileHonanBallon->procedureSecondaryVerified;
					$siteVerified 							= $detailAnesthesiaProfileHonanBallon->siteVerified;
					$evaluation2 							= $detailAnesthesiaProfileHonanBallon->evaluation2;
					$dentation 								= $detailAnesthesiaProfileHonanBallon->dentation;
					$stableCardiPlumFunction 				= $detailAnesthesiaProfileHonanBallon->stableCardiPlumFunction;
					$planAnesthesia 						= $detailAnesthesiaProfileHonanBallon->planAnesthesia;
					$allQuesAnswered 						= $detailAnesthesiaProfileHonanBallon->allQuesAnswered;
				
					$routineMonitorApplied 					= $detailAnesthesiaProfileHonanBallon->routineMonitorApplied;	
					$hide_anesthesia_grid 					= $detailAnesthesiaProfileHonanBallon->hide_anesthesia_grid ;
					
					$o2lpm_count 							= $detailAnesthesiaProfileHonanBallon->o2lpm_count;	
					if($o2lpm_count>0) {
						for($t=1;$t<=$o2lpm_count;$t++) {
							$o2lpm_ = "o2lpm_".$t;
							$$o2lpm_ = $detailAnesthesiaProfileHonanBallon->o2lpm_1;
						}
					}
					
					$ekgBigRowValue 						= $detailAnesthesiaProfileHonanBallon->ekgBigRowValue;	
					
					$anyKnowAnestheticComplication 			= $detailAnesthesiaProfileHonanBallon->anyKnowAnestheticComplication;
					$stableCardiPlumFunction2 				= $detailAnesthesiaProfileHonanBallon->stableCardiPlumFunction2;
					$satisfactoryCondition4Discharge 		= $detailAnesthesiaProfileHonanBallon->satisfactoryCondition4Discharge;
					$evaluation 							= $detailAnesthesiaProfileHonanBallon->evaluation;
					$chbx_enable_postop_desc 				= $detailAnesthesiaProfileHonanBallon->chbx_enable_postop_desc;
					$chbx_vss 								= $detailAnesthesiaProfileHonanBallon->chbx_vss;
					$chbx_atsf 								= $detailAnesthesiaProfileHonanBallon->chbx_atsf;
					$chbx_pa 								= $detailAnesthesiaProfileHonanBallon->chbx_pa;
					$chbx_nausea 							= $detailAnesthesiaProfileHonanBallon->chbx_nausea;
					$chbx_vomiting 							= $detailAnesthesiaProfileHonanBallon->chbx_vomiting;
					$chbx_dizziness 						= $detailAnesthesiaProfileHonanBallon->chbx_dizziness;
					$chbx_rd 								= $detailAnesthesiaProfileHonanBallon->chbx_rd;
					$chbx_aao 								= $detailAnesthesiaProfileHonanBallon->chbx_aao;
					$chbx_ddai 								= $detailAnesthesiaProfileHonanBallon->chbx_ddai;
					$chbx_pv 								= $detailAnesthesiaProfileHonanBallon->chbx_pv;
					$chbx_rtpog 							= $detailAnesthesiaProfileHonanBallon->chbx_rtpog;
					$chbx_pain 								= $detailAnesthesiaProfileHonanBallon->chbx_pain;
					$remarks 								= $detailAnesthesiaProfileHonanBallon->remarks;

					$honanballon 							= $detailAnesthesiaProfileHonanBallon->honanballon;	
					$honanBallonAnother 					= $detailAnesthesiaProfileHonanBallon->honanBallonAnother;
					$none 									= $detailAnesthesiaProfileHonanBallon->NoneHonanBalloon;	
					$digital 								= $detailAnesthesiaProfileHonanBallon->digital;	
					$copyBaseLineVitalSigns 				= $detailAnesthesiaProfileHonanBallon->copyBaseLineVitalSigns ;
					$fpExamPerformed		 				= $detailAnesthesiaProfileHonanBallon->fpExamPerformed ;
					if($fpExamPerformed!='Yes') {
						$fpExamPerformedVisibility='hidden';
					}
					$ansComment				 				= $detailAnesthesiaProfileHonanBallon->ansComment ;
					/*
					$TopicalAspiration		 				= $detailAnesthesiaProfileHonanBallon->TopicalAspiration ;
					$TopicalFull			 				= $detailAnesthesiaProfileHonanBallon->TopicalFull ;
					$TopicalBeforeInjection	 				= $detailAnesthesiaProfileHonanBallon->TopicalBeforeInjection ;
					$TopicalRockNegative					= $detailAnesthesiaProfileHonanBallon->TopicalRockNegative ;
					*/
					$Block1Aspiration		 				= $detailAnesthesiaProfileHonanBallon->Block1Aspiration ;
					$Block1Full			 					= $detailAnesthesiaProfileHonanBallon->Block1Full ;
					$Block1BeforeInjection	 				= $detailAnesthesiaProfileHonanBallon->Block1BeforeInjection ;
					$Block1RockNegative						= $detailAnesthesiaProfileHonanBallon->Block1RockNegative ;
					$Block2Aspiration		 				= $detailAnesthesiaProfileHonanBallon->Block2Aspiration ;
					$Block2Full			 					= $detailAnesthesiaProfileHonanBallon->Block2Full ;
					$Block2BeforeInjection	 				= $detailAnesthesiaProfileHonanBallon->Block2BeforeInjection ;
					$Block2RockNegative						= $detailAnesthesiaProfileHonanBallon->Block2RockNegative ;
					
					$txtInterOpDrugs1 						= $detailAnesthesiaProfileHonanBallon->txtInterOpDrugs1 ;
					$txtInterOpDrugs2 						= $detailAnesthesiaProfileHonanBallon->txtInterOpDrugs2 ;
					
					$confirmIPPSC_signin		= stripslashes($detailAnesthesiaProfileHonanBallon->confirmIPPSC_signin);
					$siteMarked 						= stripslashes($detailAnesthesiaProfileHonanBallon->siteMarked);
					$patientAllergies 			= stripslashes($detailAnesthesiaProfileHonanBallon->patientAllergies);	
					$difficultAirway				= stripslashes($detailAnesthesiaProfileHonanBallon->difficultAirway);
					$anesthesiaSafety				= stripslashes($detailAnesthesiaProfileHonanBallon->anesthesiaSafety);	
					$allMembersTeam 				= stripslashes($detailAnesthesiaProfileHonanBallon->allMembersTeam);
					$riskBloodLoss					=	stripslashes($detailAnesthesiaProfileHonanBallon->riskBloodLoss);
					$bloodLossUnits					=	stripslashes($detailAnesthesiaProfileHonanBallon->bloodLossUnits);
				
				}
			}	
		}
		//END CHECK FOR HONAN BALLON FOR LOGGED IN ANESTHESIOLOGIST
	}	
	
	//IF RECORD NOT SAVED BY ANESTHESIOLOGIST AT LEAST ONCE THEN GET VALUE FROM ADMIN 
	if(($form_status=="completed" || $form_status=="not completed") && $saveByAnes=='Yes') {
		//DO NOTHING
	
	}
	else {	
		
		if($logInUserType=='Anesthesiologist') {
			$chkAnsId = $_SESSION['loginUserId'];
		}else{
			$chkAnsId = $patientConfirm_anesthesiologist_id;
		}		
		$detailAnesthesiaTopicalBlockProfile = $objManageData->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId ', $chkAnsId);
		if($detailAnesthesiaTopicalBlockProfile) {	
			
			//$anesthesia_topical_block_profile_sign = $detailAnesthesiaTopicalBlockProfile->anesthesia_profile_sign;
			$anesthesia_topical_block_profile_sign_path = $detailAnesthesiaTopicalBlockProfile->anesthesia_profile_sign_path;

			if(trim($anesthesia_topical_block_profile_sign_path)) {
				$Topicaltopical4PercentLidocaine 		= $detailAnesthesiaTopicalBlockProfile->Topicaltopical4PercentLidocaine;
				$TopicalIntracameral 					= $detailAnesthesiaTopicalBlockProfile->TopicalIntracameral;
				$TopicalIntracameral1percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->TopicalIntracameral1percentLidocaine;
				$TopicalPeribulbar						= $detailAnesthesiaTopicalBlockProfile->TopicalPeribulbar;
				$TopicalPeribulbar2percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->TopicalPeribulbar2percentLidocaine;
				$TopicalRetrobulbar 					= $detailAnesthesiaTopicalBlockProfile->TopicalRetrobulbar;
				$TopicalRetrobulbar4percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->TopicalRetrobulbar4percentLidocaine;
				$TopicalHyalauronidase4percentLidocaine	= $detailAnesthesiaTopicalBlockProfile->TopicalHyalauronidase4percentLidocaine;
				$TopicalVanLindr 						= $detailAnesthesiaTopicalBlockProfile->TopicalVanLindr;
				$TopicalVanLindrHalfPercentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->TopicalVanLindrHalfPercentLidocaine;
				$topical_bupivacaine75 	= $detailAnesthesiaTopicalBlockProfile->topical_bupivacaine75;
				$topical_marcaine75 	= $detailAnesthesiaTopicalBlockProfile->topical_marcaine75;
				$TopicallidTxt 							= $detailAnesthesiaTopicalBlockProfile->TopicallidTxt;
				$Topicallid 							= $detailAnesthesiaTopicalBlockProfile->Topicallid;
				$TopicallidEpi5ug 						= $detailAnesthesiaTopicalBlockProfile->TopicallidEpi5ug;
				$TopicalotherRegionalAnesthesiaTxt1 	= $detailAnesthesiaTopicalBlockProfile->TopicalotherRegionalAnesthesiaTxt1;
				$TopicalotherRegionalAnesthesiaDrop		= $detailAnesthesiaTopicalBlockProfile->TopicalotherRegionalAnesthesiaDrop;
				$TopicalotherRegionalAnesthesiaWydase15u= $detailAnesthesiaTopicalBlockProfile->TopicalotherRegionalAnesthesiaWydase15u;
				$TopicalotherRegionalAnesthesiaTxt2 	= $detailAnesthesiaTopicalBlockProfile->TopicalotherRegionalAnesthesiaTxt2;
			
				$Block1topical4PercentLidocaine 		= $detailAnesthesiaTopicalBlockProfile->Block1topical4PercentLidocaine;
				$Block1Intracameral 					= $detailAnesthesiaTopicalBlockProfile->Block1Intracameral;
				$Block1Intracameral1percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->Block1Intracameral1percentLidocaine;
				$Block1Peribulbar 						= $detailAnesthesiaTopicalBlockProfile->Block1Peribulbar;
				$Block1Peribulbar2percentLidocaine 		= $detailAnesthesiaTopicalBlockProfile->Block1Peribulbar2percentLidocaine;
				$Block1Retrobulbar 						= $detailAnesthesiaTopicalBlockProfile->Block1Retrobulbar;
				$Block1Retrobulbar4percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->Block1Retrobulbar4percentLidocaine;
				$Block1Hyalauronidase4percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->Block1Hyalauronidase4percentLidocaine;
				$Block1VanLindr 						= $detailAnesthesiaTopicalBlockProfile->Block1VanLindr;
				$Block1VanLindrHalfPercentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->Block1VanLindrHalfPercentLidocaine;
				$block1_bupivacaine75 	= $detailAnesthesiaTopicalBlockProfile->block1_bupivacaine75;
				$block1_marcaine75 	= $detailAnesthesiaTopicalBlockProfile->block1_marcaine75;
				$Block1lidTxt 							= $detailAnesthesiaTopicalBlockProfile->Block1lidTxt;
				$Block1lid 								= $detailAnesthesiaTopicalBlockProfile->Block1lid;
				$Block1lidEpi5ug 						= $detailAnesthesiaTopicalBlockProfile->Block1lidEpi5ug;
				$Block1otherRegionalAnesthesiaTxt1 		= $detailAnesthesiaTopicalBlockProfile->Block1otherRegionalAnesthesiaTxt1;
				$Block1otherRegionalAnesthesiaDrop 		= $detailAnesthesiaTopicalBlockProfile->Block1otherRegionalAnesthesiaDrop;
				$Block1otherRegionalAnesthesiaWydase15u = $detailAnesthesiaTopicalBlockProfile->Block1otherRegionalAnesthesiaWydase15u;
				$Block1otherRegionalAnesthesiaTxt2 		= $detailAnesthesiaTopicalBlockProfile->Block1otherRegionalAnesthesiaTxt2;
				$Block1Aspiration		 				= $detailAnesthesiaTopicalBlockProfile->Block1Aspiration ;
				$Block1Full			 					= $detailAnesthesiaTopicalBlockProfile->Block1Full ;
				$Block1BeforeInjection	 				= $detailAnesthesiaTopicalBlockProfile->Block1BeforeInjection ;
				$Block1RockNegative						= $detailAnesthesiaTopicalBlockProfile->Block1RockNegative ;
			
				
				$Block2topical4PercentLidocaine 		= $detailAnesthesiaTopicalBlockProfile->Block2topical4PercentLidocaine;
				$Block2Intracameral 					= $detailAnesthesiaTopicalBlockProfile->Block2Intracameral;
				$Block2Intracameral1percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->Block2Intracameral1percentLidocaine;
				$Block2Peribulbar 						= $detailAnesthesiaTopicalBlockProfile->Block2Peribulbar;
				$Block2Peribulbar2percentLidocaine 		= $detailAnesthesiaTopicalBlockProfile->Block2Peribulbar2percentLidocaine;
				$Block2Retrobulbar 						= $detailAnesthesiaTopicalBlockProfile->Block2Retrobulbar;
				$Block2Retrobulbar4percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->Block2Retrobulbar4percentLidocaine;
				$Block2Hyalauronidase4percentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->Block2Hyalauronidase4percentLidocaine;
				$Block2VanLindr 						= $detailAnesthesiaTopicalBlockProfile->Block2VanLindr;
				$Block2VanLindrHalfPercentLidocaine 	= $detailAnesthesiaTopicalBlockProfile->Block2VanLindrHalfPercentLidocaine;
				$block2_bupivacaine75 	= $detailAnesthesiaTopicalBlockProfile->block2_bupivacaine75;
				$block2_marcaine75 	= $detailAnesthesiaTopicalBlockProfile->block2_marcaine75;
				$Block2lidTxt 							= $detailAnesthesiaTopicalBlockProfile->Block2lidTxt;
				$Block2lid 								= $detailAnesthesiaTopicalBlockProfile->Block2lid;
				$Block2lidEpi5ug 						= $detailAnesthesiaTopicalBlockProfile->Block2lidEpi5ug;
				$Block2otherRegionalAnesthesiaTxt1 		= $detailAnesthesiaTopicalBlockProfile->Block2otherRegionalAnesthesiaTxt1;
				$Block2otherRegionalAnesthesiaDrop 		= $detailAnesthesiaTopicalBlockProfile->Block2otherRegionalAnesthesiaDrop;
				$Block2otherRegionalAnesthesiaWydase15u = $detailAnesthesiaTopicalBlockProfile->Block2otherRegionalAnesthesiaWydase15u;
				$Block2otherRegionalAnesthesiaTxt2 		= $detailAnesthesiaTopicalBlockProfile->Block2otherRegionalAnesthesiaTxt2;
				$Block2Aspiration		 				= $detailAnesthesiaTopicalBlockProfile->Block2Aspiration ;
				$Block2Full			 					= $detailAnesthesiaTopicalBlockProfile->Block2Full ;
				$Block2BeforeInjection	 				= $detailAnesthesiaTopicalBlockProfile->Block2BeforeInjection ;
				$Block2RockNegative						= $detailAnesthesiaTopicalBlockProfile->Block2RockNegative ;
				
				$confirmIPPSC_signin		= stripslashes($detailAnesthesiaTopicalBlockProfile->confirmIPPSC_signin);
				$siteMarked 						= stripslashes($detailAnesthesiaTopicalBlockProfile->siteMarked);
				$patientAllergies 			= stripslashes($detailAnesthesiaTopicalBlockProfile->patientAllergies);	
				$difficultAirway				= stripslashes($detailAnesthesiaTopicalBlockProfile->difficultAirway);
				$anesthesiaSafety				= stripslashes($detailAnesthesiaTopicalBlockProfile->anesthesiaSafety);	
				$allMembersTeam 				= stripslashes($detailAnesthesiaTopicalBlockProfile->allMembersTeam);
				$riskBloodLoss					=	stripslashes($detailAnesthesiaTopicalBlockProfile->riskBloodLoss);
				$bloodLossUnits					=	stripslashes($detailAnesthesiaTopicalBlockProfile->bloodLossUnits);
			}
		}
	}
	//END IF RECORD NOT SAVED BY ANESTHESIOLOGIST AT LEAST ONCE THEN GET VALUE FROM ADMIN
	 			
//GET DEFUALT VALUES OF ASSIGNED ANESTHEOLOGIST FROM ADMIN PANEL


$siteTemp = $site; // FROM PATIENT CONFIRMATION TABLE
// APPLYING NUMBERS TO PATIENT SITE
	if($siteTemp == 1) {
		$siteShow = "Left Eye";  //OS
	}
	else if($siteTemp == 2) {
		$siteShow = "Right Eye";  //OD
	}
	else if($siteTemp == 3) {
		$siteShow = "Both Eye";  //OU
	}
// END APPLYING NUMBERS TO PATIENT SITE

$tmpData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
$localAnesFinalizeStatus = trim($tmpData->finalize_status);

$blurInput = " onBlur=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\" ";
$keyPressInput = " onKeyPress=\"javascript:this.style.backgroundColor='#FFFFFF'\" ";
$focusInput = " onFocus=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\" ";
$keyUpInput = " onKeyUp=\"if(!this.value){this.style.backgroundColor='#F6C67A' }textAreaAdjust(this);\" ";

$dosageArr	=	array('blank3','blank4','blank1','blank2','propofol','midazolam','ketamine','labetalol','Fentanyl','spo2','o2lpm');
$fldNameArr =	array('blank3' => 'blank3', 'blank4' => 'blank4','blank1' => 'blank1', 'blank2' => 'blank2',
					  'propofol' => 'mgPropofol','midazolam' => 'mgMidazolam', 'ketamine' => 'mgKetamine', 'labetalol' => 'mgLabetalol',
					  'Fentanyl' => 'mcgFentanyl','spo2' => 'spo2', 'o2lpm' => 'o2lpm');
foreach($dosageArr as $dosage)
{
	$edit_var = trim($dosage).'_edit_icon';
	$$edit_var = ($logInUserType == 'Anesthesiologist' && $localAnesFinalizeStatus <> 'true' ) ? '&nbsp;<i class="fa fa-pencil ekg-edit-icon" data-med-type="'.$fldNameArr[$dosage].'" data-med-fld="'.$dosage.'"></i>':'';
	for($L = 1; $L <= 20 ; $L++)
	{
		$var		=	trim($dosage).'_'.$L;
		$t_var		=	't_'.$var;
		$tempArr	=	explode('@@',$$var);
		$$var		=	$tempArr[0];
		$$t_var		=	$tempArr[1];
		//echo $var.' : '.$$var.' * ' .$t_var .' : '.$$t_var .'<br>';
	}
}

//START SET BGCOLOR OF 90 TEXTBOX OF GRID
$gridTxtBackColor=$whiteBckGroundColor;
if($blank1_1=='' && $blank1_2=='' && $blank1_3=='' && $blank1_4==''
		   && $blank1_5 =='' && $blank1_6 =='' && $blank1_7 =='' && $blank1_8 ==''	
		   && $blank1_9 =='' && $blank1_10=='' && $blank1_11=='' && $blank1_12=='' 
		   && $blank1_13=='' && $blank1_14=='' && $blank1_15=='' && $blank1_16=='' 
		   && $blank1_17=='' && $blank1_18=='' && $blank1_19=='' && $blank1_20==''

		   && $blank2_1 =='' && $blank2_2 =='' && $blank2_3 =='' && $blank2_4 ==''
		   && $blank2_5 =='' && $blank2_6 =='' && $blank2_7 =='' && $blank2_8 ==''	
		   && $blank2_9 =='' && $blank2_10=='' && $blank2_11=='' && $blank2_12=='' 
		   && $blank2_13=='' && $blank2_14=='' && $blank2_15=='' && $blank2_16=='' 
		   && $blank2_17=='' && $blank2_18==''	
		   && $blank2_19=='' && $blank2_20==''

		   && $blank3_1 =='' && $blank3_2 =='' && $blank3_3 =='' && $blank3_4 ==''
		   && $blank3_5 =='' && $blank3_6 =='' && $blank3_7 =='' && $blank3_8 ==''	
		   && $blank3_9 =='' && $blank3_10=='' && $blank3_11=='' && $blank3_12=='' 
		   && $blank3_13=='' && $blank3_14=='' && $blank3_15=='' && $blank3_16=='' 
		   && $blank3_17=='' && $blank3_18==''	
		   && $blank3_19=='' && $blank3_20==''

		   && $blank4_1 =='' && $blank4_2 =='' && $blank4_3 =='' && $blank4_4 ==''
		   && $blank4_5 =='' && $blank4_6 =='' && $blank4_7 =='' && $blank4_8 ==''	
		   && $blank4_9 =='' && $blank4_10=='' && $blank4_11=='' && $blank4_12=='' 
		   && $blank4_13=='' && $blank4_14=='' && $blank4_15=='' && $blank4_16=='' 
		   && $blank4_17=='' && $blank4_18==''	
		   && $blank4_19=='' && $blank4_20==''

		   && $propofol_1 =='' && $propofol_2 =='' && $propofol_3 =='' && $propofol_4 ==''
		   && $propofol_5 =='' && $propofol_6 =='' && $propofol_7 =='' && $propofol_8 ==''	
		   && $propofol_9 =='' && $propofol_10=='' && $propofol_11=='' && $propofol_12=='' 
		   && $propofol_13=='' && $propofol_14=='' && $propofol_15=='' && $propofol_16=='' 
		   && $propofol_17=='' && $propofol_18==''	
		   && $propofol_19=='' && $propofol_20==''

		   && $midazolam_1 =='' && $midazolam_2 =='' && $midazolam_3 =='' && $midazolam_4 ==''
		   && $midazolam_5 =='' && $midazolam_6 =='' && $midazolam_7 =='' && $midazolam_8 ==''	
		   && $midazolam_9 =='' && $midazolam_10=='' && $midazolam_11=='' && $midazolam_12=='' 
		   && $midazolam_13=='' && $midazolam_14=='' && $midazolam_15=='' && $midazolam_16=='' 
		   && $midazolam_17=='' && $midazolam_18==''	
		   && $midazolam_19=='' && $midazolam_20==''

		   && $Fentanyl_1 =='' && $Fentanyl_2 =='' && $Fentanyl_3 =='' && $Fentanyl_4 ==''
		   && $Fentanyl_5 =='' && $Fentanyl_6 =='' && $Fentanyl_7 =='' && $Fentanyl_8 ==''	
		   && $Fentanyl_9 =='' && $Fentanyl_10=='' && $Fentanyl_11=='' && $Fentanyl_12=='' 
		   && $Fentanyl_13=='' && $Fentanyl_14=='' && $Fentanyl_15=='' && $Fentanyl_16=='' 
		   && $Fentanyl_17=='' && $Fentanyl_18==''	
		   && $Fentanyl_19=='' && $Fentanyl_20==''

		   && $ketamine_1 =='' && $ketamine_2 =='' && $ketamine_3 =='' && $ketamine_4 ==''
		   && $ketamine_5 =='' && $ketamine_6 =='' && $ketamine_7 =='' && $ketamine_8 ==''	
		   && $ketamine_9 =='' && $ketamine_10=='' && $ketamine_11=='' && $ketamine_12=='' 
		   && $ketamine_13=='' && $ketamine_14=='' && $ketamine_15=='' && $ketamine_16=='' 
		   && $ketamine_17=='' && $ketamine_18==''	
		   && $ketamine_19=='' && $ketamine_20==''
		  
		   && $labetalol_1 =='' && $labetalol_2 =='' && $labetalol_3 =='' && $labetalol_4 ==''
		   && $labetalol_5 =='' && $labetalol_6 =='' && $labetalol_7 =='' && $labetalol_8 ==''	
		   && $labetalol_9 =='' && $labetalol_10=='' && $labetalol_11=='' && $labetalol_12=='' 
		   && $labetalol_13=='' && $labetalol_14=='' && $labetalol_15=='' && $labetalol_16=='' 
		   && $labetalol_17=='' && $labetalol_18==''	
		   && $labetalol_19=='' && $labetalol_20==''

		   && $spo2_1 =='' && $spo2_2 =='' && $spo2_3 =='' && $spo2_4 ==''
		   && $spo2_5 =='' && $spo2_6 =='' && $spo2_7 =='' && $spo2_8 ==''	
		   && $spo2_9 =='' && $spo2_10=='' && $spo2_11=='' && $spo2_12=='' 
		   && $spo2_13=='' && $spo2_14=='' && $spo2_15=='' && $spo2_16=='' 
		   && $spo2_17=='' && $spo2_18==''	
		   && $spo2_19=='' && $spo2_20==''
		  
		   && $o2lpm_1 =='' && $o2lpm_2 =='' && $o2lpm_3 =='' && $o2lpm_4 ==''
		   && $o2lpm_5 =='' && $o2lpm_6 =='' && $o2lpm_7 =='' && $o2lpm_8 ==''	
		   && $o2lpm_9 =='' && $o2lpm_10=='' && $o2lpm_11=='' && $o2lpm_12=='' 
		   && $o2lpm_13=='' && $o2lpm_14=='' && $o2lpm_15=='' && $o2lpm_16=='' 
		   && $o2lpm_17=='' && $o2lpm_18==''	
		   && $o2lpm_19=='' && $o2lpm_20==''
		  
		  ) {
			$gridTxtBackColor=$chngBckGroundColor;
}
//START SET BGCOLOR OF 90 TEXTBOX OF GRID
?>
<script>

function chkTopicalBlockFun(objTopicalBlockId) {
	if(objTopicalBlockId=='chbx_TopicalId' || objTopicalBlockId=='chbx_Block1Id' || objTopicalBlockId=='chbx_Block2Id'  || objTopicalBlockId=='chbx_NAId') {
		var topical4PercentLidocaine		='';
		var Intracameral					='';
		var Intracameral1percentLidocaine	='';
		var Peribulbar						='';
		var Peribulbar2percentLidocaine		='';
		var Retrobulbar						='';
		var Retrobulbar4percentLidocaine	='';
		var Hyalauronidase4percentLidocaine	='';
		var VanLindr						='';
		var VanLindrHalfPercentLidocaine	='';
		var bupivacaine75	='';
		var marcaine75	='';
		var lidTxt							='';
		var lid								='';
		var lidEpi5ug						='';
		var otherRegionalAnesthesiaTxt1		=''
		var otherRegionalAnesthesiaDrop		='';
		var otherRegionalAnesthesiaWydase15u='';
		var otherRegionalAnesthesiaTxt2		='';
		var Block1Block2Aspiration		 	= '';
		var Block1Block2Full			 	= '';
		var Block1Block2BeforeInjection	 	= '';
		var Block1Block2RockNegative		= '';
		
		if(objTopicalBlockId=='chbx_TopicalId') {
			if(document.getElementById(objTopicalBlockId)) {
				if(document.getElementById(objTopicalBlockId).checked==true) {
					
					topical4PercentLidocaine 		 = '<?php echo $Topicaltopical4PercentLidocaine;?>';
					Intracameral 					 = '<?php echo $TopicalIntracameral;?>';
					Intracameral1percentLidocaine 	 = '<?php echo $TopicalIntracameral1percentLidocaine;?>';
					Peribulbar 						 = '<?php echo $TopicalPeribulbar;?>';
					Peribulbar2percentLidocaine 	 = '<?php echo $TopicalPeribulbar2percentLidocaine;?>';
					Retrobulbar 					 = '<?php echo $TopicalRetrobulbar;?>';
					Retrobulbar4percentLidocaine 	 = '<?php echo $TopicalRetrobulbar4percentLidocaine;?>';
					Hyalauronidase4percentLidocaine  = '<?php echo $TopicalHyalauronidase4percentLidocaine;?>';
					VanLindr 						 = '<?php echo $TopicalVanLindr;?>';
					VanLindrHalfPercentLidocaine 	 = '<?php echo $TopicalVanLindrHalfPercentLidocaine;?>';
					bupivacaine75 	 = '<?php echo $topical_bupivacaine75;?>';
					marcaine75 	 = '<?php echo $topical_marcaine75;?>';
					lidTxt 							 = '<?php echo $TopicallidTxt;?>';
					lid 							 = '<?php echo $Topicallid;?>';
					lidEpi5ug 						 = '<?php echo $TopicallidEpi5ug;?>';
					otherRegionalAnesthesiaTxt1 	 = '<?php echo $TopicalotherRegionalAnesthesiaTxt1;?>';
					otherRegionalAnesthesiaDrop 	 = '<?php echo $TopicalotherRegionalAnesthesiaDrop;?>';
					otherRegionalAnesthesiaWydase15u = '<?php echo $TopicalotherRegionalAnesthesiaWydase15u;?>';
					otherRegionalAnesthesiaTxt2 	 = '<?php echo $TopicalotherRegionalAnesthesiaTxt2;?>';
				
				}
			}
		}else if(objTopicalBlockId=='chbx_Block1Id') {
			if(document.getElementById(objTopicalBlockId)) {
				if(document.getElementById(objTopicalBlockId).checked==true) {
					topical4PercentLidocaine 		 = '<?php echo $Block1topical4PercentLidocaine;?>';
					Intracameral 					 = '<?php echo $Block1Intracameral;?>';
					Intracameral1percentLidocaine 	 = '<?php echo $Block1Intracameral1percentLidocaine;?>';
					Peribulbar 						 = '<?php echo $Block1Peribulbar;?>';
					Peribulbar2percentLidocaine 	 = '<?php echo $Block1Peribulbar2percentLidocaine;?>';
					Retrobulbar 					 = '<?php echo $Block1Retrobulbar;?>';
					Retrobulbar4percentLidocaine 	 = '<?php echo $Block1Retrobulbar4percentLidocaine;?>';
					Hyalauronidase4percentLidocaine  = '<?php echo $Block1Hyalauronidase4percentLidocaine;?>';
					VanLindr 						 = '<?php echo $Block1VanLindr;?>';
					VanLindrHalfPercentLidocaine 	 = '<?php echo $Block1VanLindrHalfPercentLidocaine;?>';
					bupivacaine75 	 = '<?php echo $block1_bupivacaine75;?>';
					marcaine75 	 = '<?php echo $block1_marcaine75;?>';
					lidTxt 							 = '<?php echo $Block1lidTxt;?>';
					lid 							 = '<?php echo $Block1lid;?>';
					lidEpi5ug 						 = '<?php echo $Block1lidEpi5ug;?>';
					otherRegionalAnesthesiaTxt1 	 = '<?php echo $Block1otherRegionalAnesthesiaTxt1;?>';
					otherRegionalAnesthesiaDrop 	 = '<?php echo $Block1otherRegionalAnesthesiaDrop;?>';
					otherRegionalAnesthesiaWydase15u = '<?php echo $Block1otherRegionalAnesthesiaWydase15u;?>';
					otherRegionalAnesthesiaTxt2 	 = '<?php echo $Block1otherRegionalAnesthesiaTxt2;?>';
				
					Block1Block2Aspiration		 	 = '<?php echo $Block1Aspiration;?>';
					Block1Block2Full			 	 = '<?php echo $Block1Full;?>';
					Block1Block2BeforeInjection	 	 = '<?php echo $Block1BeforeInjection;?>';
					Block1Block2RockNegative		 = '<?php echo $Block1RockNegative;?>';
				
				
				}
			}
		}else if(objTopicalBlockId=='chbx_Block2Id') {
			if(document.getElementById(objTopicalBlockId)) {
				if(document.getElementById(objTopicalBlockId).checked==true) {
					topical4PercentLidocaine 		 = '<?php echo $Block2topical4PercentLidocaine;?>';
					Intracameral 					 = '<?php echo $Block2Intracameral;?>';
					Intracameral1percentLidocaine 	 = '<?php echo $Block2Intracameral1percentLidocaine;?>';
					Peribulbar 						 = '<?php echo $Block2Peribulbar;?>';
					Peribulbar2percentLidocaine 	 = '<?php echo $Block2Peribulbar2percentLidocaine;?>';
					Retrobulbar 					 = '<?php echo $Block2Retrobulbar;?>';
					Retrobulbar4percentLidocaine 	 = '<?php echo $Block2Retrobulbar4percentLidocaine;?>';
					Hyalauronidase4percentLidocaine  = '<?php echo $Block2Hyalauronidase4percentLidocaine;?>';
					VanLindr 						 = '<?php echo $Block2VanLindr;?>';
					VanLindrHalfPercentLidocaine 	 = '<?php echo $Block2VanLindrHalfPercentLidocaine;?>';
					bupivacaine75 	 = '<?php echo $block2_bupivacaine75;?>';
					marcaine75 	 = '<?php echo $block2_marcaine75;?>';
					lidTxt 							 = '<?php echo $Block2lidTxt;?>';
					lid 							 = '<?php echo $Block2lid;?>';
					lidEpi5ug 						 = '<?php echo $Block2lidEpi5ug;?>';
					otherRegionalAnesthesiaTxt1 	 = '<?php echo $Block2otherRegionalAnesthesiaTxt1;?>';
					otherRegionalAnesthesiaDrop 	 = '<?php echo $Block2otherRegionalAnesthesiaDrop;?>';
					otherRegionalAnesthesiaWydase15u = '<?php echo $Block2otherRegionalAnesthesiaWydase15u;?>';
					otherRegionalAnesthesiaTxt2 	 = '<?php echo $Block2otherRegionalAnesthesiaTxt2;?>';
				
					Block1Block2Aspiration		 	 = '<?php echo $Block2Aspiration;?>';
					Block1Block2Full			 	 = '<?php echo $Block2Full;?>';
					Block1Block2BeforeInjection	 	 = '<?php echo $Block2BeforeInjection;?>';
					Block1Block2RockNegative		 = '<?php echo $Block2RockNegative;?>';
				
				}
			}
		}
		
		if(document.frm_local_anes_rec.chbx_topical4PercentLidocaine) {
			if(topical4PercentLidocaine=='Yes') {
				document.frm_local_anes_rec.chbx_topical4PercentLidocaine.checked=true;
			}else {
				document.frm_local_anes_rec.chbx_topical4PercentLidocaine.checked=false;
			}	
		}
		
		if(document.frm_local_anes_rec.Intracameral) {
			document.frm_local_anes_rec.Intracameral.value=Intracameral;
		}
		
		if(document.frm_local_anes_rec.chbx_Intracameral1percentLidocaine) {
			if(Intracameral1percentLidocaine=='Yes') {
				document.frm_local_anes_rec.chbx_Intracameral1percentLidocaine.checked=true;
			}else{
				document.frm_local_anes_rec.chbx_Intracameral1percentLidocaine.checked=false;
			}	
		}
		
		if(document.frm_local_anes_rec.Peribulbar) {
			document.frm_local_anes_rec.Peribulbar.value=Peribulbar;
		}
		
		if(document.frm_local_anes_rec.chbx_Peribulbar2percentLidocaine) {
			if(Peribulbar2percentLidocaine=='Yes') {
				document.frm_local_anes_rec.chbx_Peribulbar2percentLidocaine.checked=true;
			}else {
				document.frm_local_anes_rec.chbx_Peribulbar2percentLidocaine.checked=false;
			}	
		}
		
		if(document.frm_local_anes_rec.Retrobulbar) {
			document.frm_local_anes_rec.Retrobulbar.value=Retrobulbar;
		}
		
		if(document.frm_local_anes_rec.chbx_Retrobulbar4percentLidocaine) {
			if(Retrobulbar4percentLidocaine=='Yes') {
				document.frm_local_anes_rec.chbx_Retrobulbar4percentLidocaine.checked=true;
			}else {
				document.frm_local_anes_rec.chbx_Retrobulbar4percentLidocaine.checked=false;
			}	
		}
		
		if(document.frm_local_anes_rec.chbx_Hyalauronidase4percentLidocaine) {
			if(Hyalauronidase4percentLidocaine=='Yes') {
				document.frm_local_anes_rec.chbx_Hyalauronidase4percentLidocaine.checked=true;
			}else {
				document.frm_local_anes_rec.chbx_Hyalauronidase4percentLidocaine.checked=false;
			}	
		}
		
		if(document.frm_local_anes_rec.VanLindr) {
			document.frm_local_anes_rec.VanLindr.value=VanLindr;
		}
		
		if(document.frm_local_anes_rec.chbx_VanLindrHalfPercentLidocaine) {
			if(VanLindrHalfPercentLidocaine=='Yes') {
				document.frm_local_anes_rec.chbx_VanLindrHalfPercentLidocaine.checked=true;
			}else {
				document.frm_local_anes_rec.chbx_VanLindrHalfPercentLidocaine.checked=false;
			}
		}
		
		if(document.frm_local_anes_rec.bupivacaine75) {
			if(bupivacaine75=='Yes') {
				document.frm_local_anes_rec.bupivacaine75.checked=true;
			}else {
				document.frm_local_anes_rec.bupivacaine75.checked=false;
			}
		}
		
		if(document.frm_local_anes_rec.marcaine75) {
			if(marcaine75=='Yes') {
				document.frm_local_anes_rec.marcaine75.checked=true;
			}else {
				document.frm_local_anes_rec.marcaine75.checked=false;
			}
		}
		
		if(document.frm_local_anes_rec.lidTxt) {
			document.frm_local_anes_rec.lidTxt.value=lidTxt;
		}
		
		if(document.frm_local_anes_rec.lid) {	
			document.frm_local_anes_rec.lid.value=lid;
		}
		
		if(document.frm_local_anes_rec.chbx_lidEpi5ug) {
			if(lidEpi5ug=='Yes') {
				document.frm_local_anes_rec.chbx_lidEpi5ug.checked=true;
			}else {
				document.frm_local_anes_rec.chbx_lidEpi5ug.checked=false;
			}
		}
		
		if(document.frm_local_anes_rec.otherRegionalAnesthesiaTxt1) {	
			document.frm_local_anes_rec.otherRegionalAnesthesiaTxt1.value=otherRegionalAnesthesiaTxt1;
		}
		
		if(document.frm_local_anes_rec.otherRegionalAnesthesiaDrop) {	
			document.frm_local_anes_rec.otherRegionalAnesthesiaDrop.value=otherRegionalAnesthesiaDrop;
		}
		
		if(document.frm_local_anes_rec.chbx_otherRegionalAnesthesiaWydase15u) {
			if(otherRegionalAnesthesiaWydase15u=='Yes') {
				document.frm_local_anes_rec.chbx_otherRegionalAnesthesiaWydase15u.checked=true;
			}else {
				document.frm_local_anes_rec.chbx_otherRegionalAnesthesiaWydase15u.checked=false;
			}	
		}
		
		if(document.frm_local_anes_rec.otherRegionalAnesthesiaTxt2) {
			document.frm_local_anes_rec.otherRegionalAnesthesiaTxt2.value=otherRegionalAnesthesiaTxt2;	
		}
		if(document.frm_local_anes_rec.Block1Block2Aspiration) {
			if(Block1Block2Aspiration=='Yes') {
				document.frm_local_anes_rec.Block1Block2Aspiration.checked=true;
			}else {
				document.frm_local_anes_rec.Block1Block2Aspiration.checked=false;
			}
		}
		if(document.frm_local_anes_rec.Block1Block2Full) {
			if(Block1Block2Full=='Yes') {
				document.frm_local_anes_rec.Block1Block2Full.checked=true;
			}else {
				document.frm_local_anes_rec.Block1Block2Full.checked=false;
			}
		}
		if(document.frm_local_anes_rec.Block1Block2BeforeInjection) {
			if(Block1Block2BeforeInjection=='Yes') {
				document.frm_local_anes_rec.Block1Block2BeforeInjection.checked=true;
			}else {
				document.frm_local_anes_rec.Block1Block2BeforeInjection.checked=false;
			}
		}
		if(document.frm_local_anes_rec.Block1Block2RockNegative) {
			if(Block1Block2RockNegative=='Yes') {
				document.frm_local_anes_rec.Block1Block2RockNegative.checked=true;
			}else {
				document.frm_local_anes_rec.Block1Block2RockNegative.checked=false;
			}
		}
		
		changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');
		
	}		
	$(".selectpicker").selectpicker("refresh");		
}
//END FUNCTIONS TO GET VALUE FROM ADMIN PANEL
function showConsentForm(surgery_consent_id){
	alert(surgery_consent_id);
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	window.open('showSignedConsentForm.php?surgeryConsentId=<?php echo $surgery_consent_id; ?>','','width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');

}
function displayTropicalTest(id){
	if(id=="block1Span" || id=="block2Span"){
		if(document.getElementById("tropicalTestTr1")) {
			
			if(document.getElementById("chbx_Block1Id").checked==true || document.getElementById("chbx_Block2Id").checked==true){
				document.getElementById("tropicalTestTr1").style.display="block";
				document.getElementById("tropicalTestTr2").style.display="block";
			}else{
				document.getElementById("tropicalTestTr1").style.display="none";
				document.getElementById("tropicalTestTr2").style.display="none";
			}
			/*
			if(document.getElementById("tropicalTestTr1").style.display=="block"){
				document.getElementById("tropicalTestTr1").style.display="none";
				document.getElementById("tropicalTestTr2").style.display="none";
			}
			else if(document.getElementById("tropicalTestTr1").style.display=="none"){
				document.getElementById("tropicalTestTr1").style.display="block";
				document.getElementById("tropicalTestTr2").style.display="block";
			}*/
		}
	}else{
		if(document.getElementById("tropicalTestTr1")) {
			document.getElementById("tropicalTestTr1").style.display="none";
			document.getElementById("tropicalTestTr2").style.display="none";
		}
	}
}
function showAllEpost(){
	if(document.getElementById("epostMainDiv")){
		document.getElementById("epostMainDiv").style.display="block";
		document.getElementById("epostMainDiv").focus;
	}
}
</script>

<script type="text/javascript">
//START CODE TO MANAGE TWO SCROLL-BARS
var lastSeen = [0, 0];
function checkScroll(div1, div2) {
	if(!div1 || !div2) return;
	var control = null;
	//START CODE TO MANAGE TWO HORIZONTAL SCROLL-BAR
	if(div1.scrollLeft != lastSeen[0]) control = div1;
	else if(div2.scrollLeft != lastSeen[1]) control = div2;
	if(control == null) return;
	else div1.scrollLeft = div2.scrollLeft = control.scrollLeft;
	lastSeen[0] = div1.scrollLeft;
	lastSeen[1] = div2.scrollLeft;
	//END CODE TO MANAGE TWO HORIZONTAL SCROLL-BAR
	
	//START CODE TO MANAGE TWO VERTICAL SCROLL-BAR
	/*
	if(div1.scrollTop != lastSeen[0]) control = div1;
	else if(div2.scrollTop != lastSeen[1]) control = div2;
	if(control == null) return;
	else div1.scrollTop = div2.scrollTop = control.scrollTop;
	lastSeen[0] = div1.scrollTop;
	lastSeen[1] = div2.scrollTop;
	*/
	//END CODE TO MANAGE TWO VERTICAL SCROLL-BAR
}
//END CODE TO MANAGE TWO SCROLL-BARS

function lineApp(){
   var objApp = document.applets["signs"];
   //var objApp = document.applets['signs'];  
   
   //objApp.showLineSh(x,y); x start point left, y top point
    objApp.showLineSh(0,200);
}
function getTmSec(startTime) {
	var hvr=mnt=sec='';
	if(startTime.substr(2,1)==":") {
		if(startTime.length==6) {
			startTimeSplit = startTime.split(":");
			if(startTimeSplit[0].length==2 && startTimeSplit[1].length==3) {
				hvr = parseFloat(startTimeSplit[0]);
				mnt = startTimeSplit[1].substr(0,2);
				if(mnt == '00' || mnt == '15' || mnt == '30' || mnt == '45') { 
				}else {  
					for(var i=mnt;i>=1;i--) {
						mnt = parseFloat(mnt-1);
						if(mnt == '00' || mnt == '15' || mnt == '30' || mnt == '45') {
							break;
						}
					}
				}
				if(startTimeSplit[1].substr(2,1)=="P" && hvr!='12') {
					hvr = parseFloat(parseFloat(hvr)+12);
				}
				sec = parseFloat(parseFloat(hvr*3600)+parseFloat(mnt*60));
			}
		}
	}
	return sec;
}
function showHideEkgLineFn(obj) {
	var objApp = document.applets["signs"];
	var strtTm = document.frm_local_anes_rec.startTime.value;
	var strtSec = getTmSec(strtTm);
	var today=new Date();
	var cur_h=today.getHours();
	var cur_m=today.getMinutes();
	var cur_sec = parseFloat(parseFloat(cur_h*3600)+parseFloat(cur_m*60));
	var redLineLftPos=50;
	var new_sec;
	//alert(cur_sec);
	//alert(strtSec);
	if(strtSec && cur_sec>=strtSec) {
		new_sec = parseFloat(cur_sec-strtSec);
		redLineLftPos = parseFloat(parseFloat(parseFloat(new_sec)/60)*parseFloat(3.3));
		if(obj) {
			if(obj.checked==true) {
				//objApp.showLineSh(0,120);
				var objApp = document.applets["signs"]; 
				//var ret = objApp.showLineSh(1,200,0,0); //SHOW HORIZONTAL LINE
				
				var ret = objApp.showLineSh(1,redLineLftPos,1,1); //SHOW VERTICAL LINE 265
			}else {
				//objApp.showLineSh1(0,120);
				var objApp = document.applets["signs"]; 
				//var ret = objApp.showLineSh(0,200,0,0); //HIDE HORIZONTAL LINE
				var ret = objApp.showLineSh(0,redLineLftPos,1,1); //HIDE VERTICAL LINE
				//alert("Save String: "+ret);			
			}
		}
	}else {
		var msg = 'Please enter valid start time\n';
		msg+='Start time should be less than current time when select on Reblock\n';
		alert(msg);
	}		
}
function anesthesiaScanWinOpn(pageName,pConfirmId,scanANESTHESIA) {
	var SW	=	window.screen.width ;
	var SH	=	window.screen.height;
	
	var	W	=	( SW > 1200 ) ?  1200	: SW ;
	var	H	=	W * 0.65
	window.open(pageName+'?pConfirmId='+pConfirmId+'&scanANESTHESIA='+scanANESTHESIA+'&ANESTHESIAScan=true','scanWinANESTHESIA', 'width='+W+', height='+H+',location=yes,status=yes');
}
</script>
<?php
$displayEkgLine='none';
if($Reblock=='Yes'){ $displayEkgLine='block';}
$settings = $objManageData->loadSettings('asa_4,anes_mallampetti_score');
?>
<!-- <span id="ekgLineId" style="display:<?php //echo $displayEkgLine;?>; position:absolute; width:560px; top:685px; left:90px;  background-image:url(images/strike_image.jpg); background-repeat:repeat-x; background-position:center;">&nbsp;</span> -->
<div class="main_wrapper">
<form action="local_anes_record.php?submitMe=true" name="frm_local_anes_rec" id="frm_local_anes_rec" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
    <input type="hidden" name="hiddScanUploadStatus" id="hiddScanUploadStatus">
    <input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
	<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
	<input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">	
	<input type="hidden" name="thisId" id="thisId" value="<?php echo $thisId; ?>">
	<input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php if($_GET['patient_id']) {echo $_GET['patient_id'];}else { echo $_REQUEST['patient_id']; } ?>">
	<input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="localAnesthesiaRecordId" id="localAnesthesiaRecordId" value="<?php echo $localAnesthesiaRecordId; ?>">
    <input type="hidden" name="med_grid_id" id="med_grid_id" value="<?php echo $med_grid_id; ?>">
    <input type="hidden" name="med_grid_sec_id" id="med_grid_id" value="<?php echo $med_grid_sec_id; ?>">
	<input type="hidden" name="getText" id="getText">	
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
	<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>" >
	<input type="hidden" name="frmAction" id="frmAction" value="local_anes_record.php">	
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">		
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
	<input type="hidden" name="hiddSaveByAnes" id="hiddSaveByAnes" value="<?php echo $saveByAnes;?>">
	<input type="hidden" name="hiddResetStatusId" id="hiddResetStatusId">
    <input type="hidden" name="hidEnableHTMLGrid" id="hidEnableHTMLGrid" value="<?php echo ($blEnableHTMLGrid == true) ? "1" : "0"; ?>">        
	<input type="hidden" name="chbx_enable_postop_desc" id="chbx_enable_postop_desc" value="<?php echo $chbx_enable_postop_desc;?>">
	<input type="hidden" name="hiddActiveTimeInterval" id="hiddActiveTimeInterval" value="<?php echo $activeTimeInterval;?>">
	<input type="hidden" name="hiddPrintLocalAnesPage" id="hiddPrintLocalAnesPage">
    <input type="hidden" name="hide_anesthesia_grid" id="hide_anesthesia_grid" value="<?php echo $hide_anesthesia_grid;?>">

<?php 
$getPreOpNursingDetails = $objManageData->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfId);
$queryGetSignedConsentFormName="
	SELECT surgery_consent_id, consent_template_id, surgery_consent_alias, form_status
	FROM consent_multiple_form
	WHERE (form_status = 'completed'
			OR form_status = 'not completed')
	AND confirmation_id =$pConfId
	AND consent_category_id
	IN (SELECT category_id
		FROM `consent_category` 
		WHERE category_name = 'Surgical'
		OR category_name = 'Anesthesia Consent')
";
//echo 'Query ' . $queryGetSignedConsentFormName ; 
$rsGetSignedConsentFormName = imw_query($queryGetSignedConsentFormName);
$totalRowsGetSignedConsentFormName = imw_num_rows($rsGetSignedConsentFormName);
//Getting EKG & H&P
	//START CODE TO GET DOCUMENTS OF EKG H&P CONSENT
	$ekgHpLink = "";
	$anesConsentEkgHpArr = array('EKG', 'H&P', 'Consent', 'Sx Planning Sheet');
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
			if($anesConsentEkgHpName=='EKG' || $anesConsentEkgHpName=='H&P' || $anesConsentEkgHpName=='Sx Planning Sheet') {
				if($anesConsentEkgHpName=='EKG' || $anesConsentEkgHpName=='H&P') {
					$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath
										 FROM  scan_upload_tbl sut,scan_documents sd 
										 WHERE sd.confirmation_id 	= '".$pConfId."'
										 AND   sut.confirmation_id 	= '".$pConfId."'
										 AND   sut.document_name like '%".$anesConsentEkgHpName."%'
										 AND   sd.document_id 		= sut.document_id
										 ORDER BY sd.document_id, sut.document_id
										"; 
				}
				if($anesConsentEkgHpName=='Sx Planning Sheet') {
					$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath 
										 FROM  scan_upload_tbl sut,scan_documents sd 
										 WHERE sd.confirmation_id 	= '".$pConfId."'
										 AND   sut.confirmation_id 	= '".$pConfId."'
										 AND   sd.document_name 	= 'Clinical'
										 AND   sut.pdfFilePath 	 LIKE '%Sx_Planing_Sheet_%'
										 AND   sd.document_id 		= sut.document_id
										 ORDER BY sd.document_id, sut.document_id
										"; 
				}
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
			$anesConsentEkgHpNameNew = str_ireplace('Sx Planning Sheet','SxP',$anesConsentEkgHpNameNew);
			$ekgHpLink.='<a href="#" class="btn-sm" onclick="'.$eKgHpCLickFun.'">'.$anesConsentEkgHpNameNew.'</a>';
		}
	}
	//END CODE TO GET DOCUMENTS OF EKG H&P CONSENT
	?>	
	
		<div class="slider_content scheduler_table_Complete" id="" >
        	<?php	
					if($totalRowsGetSignedConsentFormName > 0) 
					{
						while($row = imw_fetch_array($rsGetSignedConsentFormName))
						{
							$onClickFunc =	"onClick=\"top.left_link_click(\'consent_multiple_form.php\',\'0\',\'0\',\'0\',\'".(($_GET['patient_id']) ? $_GET['patient_id'] : $_REQUEST['patient_id'])."\',\'".$pConfId."\',\'0\',\'".$row['consent_template_id']."\',\'".$row['surgery_consent_id']."\',\'0\');\" ";
							
                             $flag	=	'images/'.($row['form_status']=='not completed' ? 'red' : 'green').'_flag.png' ;
							$ekgHpLink .=	'<a href="#" class="btn-sm" '.$onClickFunc.'><img src="'.$flag.'" style="width:12px; height:14px; border:none;" /> '.subStrWord($row['surgery_consent_alias']).'&nbsp;</a>';	
						}
						
					}
			?>
            				
            
        	<?php
					$epost_table_name = "localanesthesiarecord";
					include("./epost_list.php");
			?>
                     <!--   
        			<div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_anesth">
                    	<span class="bg_span_anesth">Local/Regional Anesthesia Record</span>
						

						<?php
                                
                                if($totalRowsGetSignedConsentFormName > 0) 
                                {
                        ?>				
                                        <span id="SignedConsentForm" class="nowrap valignBottom">
                                        <?php 
                                                while($row = imw_fetch_array($rsGetSignedConsentFormName)) 
                                                {
                                        ?>		
                                                    <a href="#" style="font-size:13px;" class="link_slid" onClick=" top.left_link_click('consent_multiple_form.php','0','0','0','<?php if($_GET['patient_id']) {echo $_GET['patient_id'];}else { echo $_REQUEST['patient_id']; } ?>','<?php echo $pConfId; ?>','0','<?php echo $row['consent_template_id'];?>','<?php echo $row['surgery_consent_id'];?>','0');" >								
                                                            <?php 
                                                                    if($row['form_status']=='not completed')
                                                                    {
                                                                        echo '<img src="images/red_flag.png" style="width:12px; height:14px; border:none;">';
                                                                    }
                                                                    elseif ($row['form_status']=='completed')
                                                                    {
                                                                        echo '<img src="images/green_flag.png" style="width:12px; height:14px; border:none;">';
                                                                    }
                                                                    
                                                                    echo subStrWord($row['surgery_consent_alias']);
                                                            ?>
                                                    </a>&nbsp;  
                                        <?php 
                                                    }
                                        ?>
                                        </span>
                        <?php
                                
                                }
                        
                        ?>
                        
                        
                        
                        <?php  if($ekgHpLink) { ?>
                            <span id="ekgHpLink" class="nowrap valignBottom"><?php echo $ekgHpLink; ?></span>
                        <?php } ?>                        
                        
                  	</div>	
                    -->
                     <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
                        <?php 
							$bgCol = $bgdark_blue_local_anes;
							$borderCol = $tablebg_local_anes;
                            include('saveDivPopUp.php'); 
                        ?>
                    </div>	
                    
                    <div class="change_temp_div">
                                <div class="inline_btns" id="scn_pre_op_id">
                                	<!--<a href="javascript:void(0)" class="btn btn-primary"> Scan	</a>-->
                                    <!--SCAN BUTTON HERE-->
                                </div>
                                <div class="inline_btns" id="fl_pre_op_id">
                                	<!--BROWSE-->
                                    <!--<input type="file" class="form-control" />-->
                                </div>
                                <div class="inline_btns" id="upd_pre_op_id">
                                    <!--<a href="javascript:void(0)" class="btn btn-primary"> Upload </a>-->
                                    <!--UPLOAD BUTTON HERE-->
                                </div>
                                
                                <div class="inline_btns" id="del_btn_id">
                                    <!--<a href="javascript:void(0)" class="btn btn-primary"> Upload </a>-->
                                    <!--Delete BUTTON HERE-->
                                </div>
                                
                            </div>
                     <?php
							$dispLclMain = "inline-block";
							if($anes_ScanUploadPath || $anes_ScanUpload){ $dispLclMain = "none";}
						?>
            	<div  id="localMainId" style="display:<?php echo $dispLclMain;?>;">
              
              	<?php 
				//START COMMON CODE FOR ANESTHESIOLOGIST FOUR SIGNATURES
					$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
					$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
					$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
					
					$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
					$loggedInUserType = $ViewUserNameRow["user_type"];
					$loggedInSignatureOfUser = $ViewUserNameRow["signature"];
					
					if($loggedInUserType<>"Anesthesiologist") {
						$loginUserName 			= $_SESSION['loginUserName'];
						
						$callJavaFunBeforeIntra = "return noAuthorityFunCommon('Anesthesiologist');";
						$callJavaFunPreOp 		= "return noAuthorityFunCommon('Anesthesiologist');";
						$callJavaFunIntraOp 	= "return noAuthorityFunCommon('Anesthesiologist');";
						$callJavaFunPostOp 		= "return noAuthorityFunCommon('Anesthesiologist');";
					}else {
						$loginUserId = $_SESSION["loginUserId"];
						
						$callJavaFunPreOp 		= "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia1');";
						$callJavaFunIntraOp 	= "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia2');";
						$callJavaFunPostOp 		= "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia3SignatureId'; return displaySignature('TDanesthesia3NameId','TDanesthesia3SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia3');";
						$callJavaFunBeforeIntra = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia4SignatureId'; return displaySignature('TDanesthesia4NameId','TDanesthesia4SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia4');";
						
					}
				//END COMMON CODE FOR ANESTHESIOLOGIST FOUR SIGNATURES
				
				if($version_num > 1) { ?>
								<!-- Before Induction of Anesthesia Starts Here -->
                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_anesth" >
                	<div class="scanner_win new_s" style="position:relative;">
                  	<h4><span>The following items were verified before Induction of Anesthesia</span></h4>
                 	</div>
               	</div>
                
                <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                	<div class="panel panel-default bg_panel_anesth">
                  	<div class="panel-body " id="p_check_in">
                    
                    	
                    	<div class="row">
                      
                      	<Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        	<div class="inner_safety_wrap">
                          	
                            <p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-5"> Nurse and anesthesia care provider confirm:</p>
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-7">
                              <div class="inner_safety_wrap">
                                <div class="row">
                                  <label class="rob col-md-12 col-sm-12 col-xs-12 col-lg-3" for="n_select"> Relief Nurse / Anesthesia </label>
                                  <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-9">
                                    <select name="reliefNurseId" class="selectpicker">
                                      <option value="">Select</option>
                                      <?php
																				
																					$reliefNurseAnesQry = "select usersId,lname,fname,mname, deleteStatus from users where (user_type IN('Nurse','Anesthesiologist') or (user_type='Anesthesiologist' And user_sub_type='CRNA')) ORDER BY lname";
																					$reliefNurseAnesRes = imw_query($reliefNurseAnesQry) or die(imw_error());
																					while($reliefNurseAnesRow = imw_fetch_array($reliefNurseAnesRes))
																					{
																						$reliefNurseAnesID	= $reliefNurseAnesRow["usersId"];
																						$reliefNurseAnesName=	trim($reliefNurseAnesRow["lname"].", ".$reliefNurseAnesRow["fname"]." ".$reliefNurseAnesRow["mname"]);
																						$sel = ($reliefNurseId == $reliefNurseAnesID) ? 'selected' : '';
																						
																						if($reliefNurseAnesRow["deleteStatus"]<>'Yes' || $reliefNurseId == $reliefNurseAnesID)
																						{
																							echo '<option value="'.$reliefNurseAnesID.'" '.$sel.'>'.$reliefNurseAnesName.'</option>';		
																								
																						}
																						
																					}
                                    	?>
                                    </select>
                                  </Div>
                                </div>
                              </div>
                            </div>
            								
            								<div class="clearfix border-dashed margin_adjustment_only"></div>
                            
                            <div class="row">
                            	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                                <span class="col-md-8 col-sm-9 col-xs-8 col-lg-9">&nbsp;</span>
                                <span class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center bold">
                                  <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 ">Yes</label>
                                  <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">No</label>
                                  <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">N/A</label>
                                </span>
                              </div>
            
                              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 hidden-xs hidden-sm">
                                <span class="col-md-8 col-sm-9 col-xs-8 col-lg-9">&nbsp;</span>
                                <span class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center bold">
                                  <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">Yes</label>
                                  <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">No</label>
                                  <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4">N/A</label>
                                </span>
                              </div>
                           	</div>
                            
                            <div class="clearfix margin_adjustment_only "></div>
                            
                            <div class="row">
                              	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                                	<Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                  	<label class="date_r"> Confirmation of: identify, procedure, procedure site and consent(s) </label>
                                 	</Div>
                                  
                                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                                    <div class="">
                                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                      	<span class="colorChkBx" style=" <?php if($confirmIPPSC_signin) { echo $whiteBckGroundColor;}?>" >
                                        	<input type="checkbox" onClick="javascript:checkSingle('chbx_ipp_yes','chbx_ipp'),changeChbxColor('chbx_ipp')" <?php if($confirmIPPSC_signin=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ipp" id="chbx_ipp_yes" />
                                       	</span>
                                     	</div>
                                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($confirmIPPSC_signin) { echo $whiteBckGroundColor;}?>" >
                                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ipp_no','chbx_ipp'),changeChbxColor('chbx_ipp')" <?php if($confirmIPPSC_signin=='No') echo "CHECKED"; ?> value="No" name="chbx_ipp" id="chbx_ipp_no"  >
                                        </span> </div>
                                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($confirmIPPSC_signin) { echo $whiteBckGroundColor;}?>" >
                                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ipp_na','chbx_ipp'),changeChbxColor('chbx_ipp')" <?php if(stripslashes($confirmIPPSC_signin)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_ipp" id="chbx_ipp_na">
                                        </span> </div>
                                    </div>
                                  </Div>
                              	</div>
                                
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                               		<Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                  	<label class="date_r"> Site marked by person performing the procedure </label>
                                 	</Div>
                                  
                                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                                  	<div class="">
                                    	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                      	<span class="colorChkBx" style=" <?php if($siteMarked) { echo $whiteBckGroundColor;}?>" >
                                        	<input type="checkbox" onClick="javascript:checkSingle('chbx_smpp_yes','chbx_smpp'),changeChbxColor('chbx_smpp')" <?php if($siteMarked=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_smpp" id="chbx_smpp_yes" />
                                       	</span>
                                    	</div>
                                      
                                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                      	<span class="colorChkBx" style=" <?php if($siteMarked) { echo $whiteBckGroundColor;}?>" >
                                        	<input type="checkbox" onClick="javascript:checkSingle('chbx_smpp_no','chbx_smpp'),changeChbxColor('chbx_smpp')" <?php if($siteMarked=='No') echo "CHECKED"; ?> value="No" name="chbx_smpp" id="chbx_smpp_no"  />
                                      	</span>
                                     	</div>
                                      
                                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                      	<span class="colorChkBx" style=" <?php if($siteMarked) { echo $whiteBckGroundColor;}?>" >
                                        	<input type="checkbox" onClick="javascript:checkSingle('chbx_smpp_na','chbx_smpp'),changeChbxColor('chbx_smpp')" <?php if(stripslashes($siteMarked)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_smpp" id="chbx_smpp_na" />
                                      	</span>
                                    	</div>
                                   	</div>
                                 	</Div>
                               	</div>
                          	
                            </div>
                            
                            <div class="clearfix margin_adjustment_only"></div>
                            
                            <div class="row">
                            	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                              	<Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                	<label class="date_r"> Patient allergies </label>
                               	</Div>
                                
                                <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                                	<div class="">
                                  	
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($patientAllergies) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_pa_yes','chbx_pa'),changeChbxColor('chbx_pa')" <?php if($patientAllergies=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_pa" id="chbx_pa_yes" />
                                   	 	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($patientAllergies) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_pa_no','chbx_pa'),changeChbxColor('chbx_pa')" <?php if($patientAllergies=='No') echo "CHECKED"; ?> value="No" name="chbx_pa" id="chbx_pa_no" />
                                    	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($patientAllergies) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_pa_na','chbx_pa'),changeChbxColor('chbx_pa')" <?php if(stripslashes($patientAllergies)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_pa" id="chbx_pa_na" />
                                     	</span>
                                  	</div>
                                  </div>
                               	</Div>
                  						</div>
                              
                              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                              	<Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                	<label class="date_r"> Difficult airway or aspiration risk? </label>
                               	</Div>
                                
                                <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                                	<div class="">
                                  	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($difficultAirway) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_dar_yes','chbx_dar'),changeChbxColor('chbx_dar')" <?php if($difficultAirway=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_dar" id="chbx_dar_yes" />
                                    	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($difficultAirway) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_dar_no','chbx_dar'),changeChbxColor('chbx_dar')" <?php if($difficultAirway=='No') echo "CHECKED"; ?> value="No" name="chbx_dar" id="chbx_dar_no" />
                                     	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($difficultAirway) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_dar_na','chbx_dar'),changeChbxColor('chbx_dar')" <?php if(stripslashes($difficultAirway)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_dar" id="chbx_dar_na" />
                                     	</span>
                                   	</div>
                                  </div>
                               	</Div>
                  						</div>
                          	
                            </div>
                            
                            <div class="clearfix margin_adjustment_only"></div>
                            
                            <div class="row">
                            	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                              	<Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                	<label class="date_r">Risk of blood loss (>500 ml)</label>
                                  <span class="date_r" id="rblno_of_units" style="display:<?php echo $displayStatus=($riskBloodLoss=='Yes')? "inline-block" : "none"; ?>;"> # of units available:
                                  	<input type="text" name="rbl_no_of_units"  value="<?php echo $bloodLossUnits; ?>" id="rbl_no_of_units">
                                 	</span>
                               	</Div>
                                
                                <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                                	<div class="">
                                  	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($riskBloodLoss) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_yes','chbx_rbl'),changeChbxColor('chbx_rbl'),disp(document.frm_local_anes_rec.chbx_rbl,'rblno_of_units');" <?php if($riskBloodLoss=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_rbl" id="chbx_rbl_yes" />
                                    	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($riskBloodLoss) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_no','chbx_rbl'),changeChbxColor('chbx_rbl'),disp_none(document.frm_local_anes_rec.chbx_rbl,'rblno_of_units');" <?php if($riskBloodLoss=='No') echo "CHECKED"; ?> value="No" name="chbx_rbl" id="chbx_rbl_no" />
                                     	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($riskBloodLoss) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_na','chbx_rbl'),changeChbxColor('chbx_rbl'),disp_none(document.frm_local_anes_rec.chbx_rbl,'rblno_of_units');" <?php if(stripslashes($riskBloodLoss)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_rbl" id="chbx_rbl_na" />
                                     	</span>
                                   	</div>
                    							</div>
                  							</Div>
                              
                              </div>
                              
                              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                              	<Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                	<label class="date_r"> Anesthesia safety check completed </label>
                               	</Div>
                                
                                <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                                	<div class="">
                                  	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($anesthesiaSafety) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_asc_yes','chbx_asc'),changeChbxColor('chbx_asc')" <?php if($anesthesiaSafety=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_asc" id="chbx_asc_yes" />
                                    	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($anesthesiaSafety) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_asc_no','chbx_asc'),changeChbxColor('chbx_asc')" <?php if($anesthesiaSafety=='No') echo "CHECKED"; ?> value="No" name="chbx_asc" id="chbx_asc_no" />
                                     	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($anesthesiaSafety) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_asc_na','chbx_asc'),changeChbxColor('chbx_asc')" <?php if(stripslashes($anesthesiaSafety)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_asc" id="chbx_asc_na" />
                                     	</span>
                                   	</div>
                    							</div>
                  							</Div>
                  						</div>
                           	</div>
                            
                            <div class="rob col-lg-12 col-md-12 col-sm-12 col-xs-12 l_height_28">Briefing:</div>
                            
                            <div class="clearfix border-dashed margin_adjustment_only "></div>
                            
                            <div class="clearfix margin_adjustment_only"></div>
                          	
                            <Div class="row">
                            
                            	<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                              
                                    <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                        <label class="date_r"> All members of the team have discussed care plan and addressed concerns </label>
                                    </div>
                                      
                                    <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                      <div class="">
                                        
                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                          <span class="colorChkBx" style=" <?php if($allMembersTeam) { echo $whiteBckGroundColor;}?>" >
                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_adcpc_yes','chbx_adcpc'),changeChbxColor('chbx_adcpc')" <?php if($allMembersTeam=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_adcpc" id="chbx_adcpc_yes" />
                                          </span>
                                        </div>
                                        
                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                          <span class="colorChkBx" style=" <?php if($allMembersTeam) { echo $whiteBckGroundColor;}?>" >
                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_adcpc_no','chbx_adcpc'),changeChbxColor('chbx_adcpc')" <?php if($allMembersTeam=='No') echo "CHECKED"; ?> value="No" name="chbx_adcpc" id="chbx_adcpc_no" />
                                          </span>
                                        </div>
                                        
                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                          <span class="colorChkBx" style=" <?php if($allMembersTeam) { echo $whiteBckGroundColor;}?>" >
                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_adcpc_na','chbx_adcpc'),changeChbxColor('chbx_adcpc')" <?php if(stripslashes($allMembersTeam)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_adcpc" id="chbx_adcpc_na" />
                                          </span>
                                        </div>
                                        
                                      </div>
                                    </div>
                  
                            	</div>
							<?php
										  
								//CODE RELATED TO ANES 3 SIGNATURE ON FILE
									$anesthesia4SignOnFileStatus = "Yes";
									$TDanesthesia4NameIdDisplay = "block";
									$TDanesthesia4SignatureIdDisplay = "none";
									$Anesthesia4Name = $loggedInUserName;
									$Anesthesia4SubType = $logInUserSubType;
									$Anesthesia4PreFix = 'Dr.';
									$signAnesthesia4DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
									if($signAnesthesia4Id<>0 && $signAnesthesia4Id<>"") {
										$Anesthesia4Name = $signAnesthesia4LastName.", ".$signAnesthesia4FirstName." ".$signAnesthesia4MiddleName;
										$anesthesia4SignOnFileStatus = $signAnesthesia4Status;	
										$TDanesthesia4NameIdDisplay = "none";
										$TDanesthesia4SignatureIdDisplay = "block";
										$signAnesthesia4DateTimeFormatNew = $objManageData->getFullDtTmFormat($signAnesthesia4DateTime);
										$Anesthesia4SubType = getUserSubTypeFun($signAnesthesia4Id); //FROM common/commonFunctions.php
									}
									
									if($Anesthesia4SubType=='CRNA') {
										$Anesthesia4PreFix = '';
									}
								
									//CODE TO REMOVE ANES 4 SIGNATURE
										if($_SESSION["loginUserId"]==$signAnesthesia4Id) {
											$callJavaFunBeforeIntraDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia4NameId'; return displaySignature('TDanesthesia4NameId','TDanesthesia4SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia4','delSign');";
										}else {
											$callJavaFunBeforeIntraDel = "alert('Only $Anesthesia4PreFix ".addslashes($Anesthesia4Name)." can remove this signature');";
										}
									//END CODE TO REMOVE ANES 4 SIGNATURE	
									
								//END CODE RELATED TO ANES 4 SIGNATURE ON FILE								  
								  
                                ?>
							<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 "> 
                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 ">                               
                                    <div class="inner_safety_wrap" id="TDanesthesia4NameId" style="display:<?php echo $TDanesthesia4NameIdDisplay;?>;">
                                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signAnesthesiaIdBackColor;?>;" onClick="javascript:<?php echo $callJavaFunBeforeIntra;?>"> Anesthesia Provider Signature </a>
                                    </div>
                                    <div class="inner_safety_wrap collapse" id="TDanesthesia4SignatureId" style="display:<?php echo $TDanesthesia4SignatureIdDisplay;?>;">
                                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunBeforeIntraDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$Anesthesia4PreFix." ".$Anesthesia4Name; ?>  </a></span>	     
                                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $anesthesia4SignOnFileStatus;?></span>
                                        <span class="rob full_width"> <b> Signature Date</b> <?php echo $signAnesthesia4DateTimeFormatNew;?></span>
                                    </div>
                                </div>
                            </div>  
                            </Div>
                            
                         </div>
                      	</Div>
                    	</div>
                      
                   	</div>          
                 	</div> 
               	</div>
                    
                <!-- Before Induction of Anesthesia Ends Here -->
               	<?php } ?>     

                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_anesth" >
                  <div class="scanner_win new_s" style="position:relative;">
                      <h4><span >Pre-Operative</span></h4>
                  </div>
               	</div>
                    
                                    
                                        <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                                                 
                                                 <div class="panel panel-default bg_panel_anesth">
                                                  
                                                   <div class="panel-body " id="p_check_in">
                                                        <div class="row">
                                                            <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            	<div class="inner_safety_wrap">
                                                                    <div class="row">
                                                                        <div class="col-md-2 col-sm-6 col-xs-12 col-lg-2">
                                                                            <label for="chbx_pat_inter_id"   >
                                                                            	<span class="colorChkBx" style=" <?php if($patientInterviewed) { echo $whiteBckGroundColor;}?>" onClick="changeChbxColor('chbx_pat_inter');" >
                                                                                <input type="checkbox" <?php if($patientInterviewed=='Yes') echo "CHECKED"; ?> value="Yes" id="chbx_pat_inter_id" name="chbx_pat_inter"   tabindex="1" />
                                                                                </span>
                                                                                Patient Interviewed </label>
                                                                        </div>
                                                                        <div class="col-md-2 col-sm-6 col-xs-12 col-lg-2">
                                                                            <label for="chbx_chart_yes" >
                                                                                <span class="colorChkBx" style=" <?php if($chartNotesReviewed) { echo $whiteBckGroundColor;}?>">
                                                                                
                                                                                <input type="checkbox" onClick="javascript:checkSingle('chbx_chart_yes','chbx_chart'),changeChbxColor('chbx_chart');" <?php if($chartNotesReviewed=='Yes') echo "CHECKED"; ?> value="Yes" id="chbx_chart_yes" name="chbx_chart"  tabindex="2"></span> No change in H&P </label>
                                                                        </div>
                                                                        <Div class="clearfix visible-sm margin_adjustment_only"></Div>
                                                                         <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                                                                            <?php
																			if($version_num > 5) {?>
                                                                            <label for="chbx_chart_changed">
                                                                                <span class="colorChkBx" style=" <?php if($chartNotesReviewed) { echo $whiteBckGroundColor;}?>">
                                                                                <input type="checkbox" onClick="javascript:checkSingle('chbx_chart_changed','chbx_chart'),changeChbxColor('chbx_chart');" <?php if($chartNotesReviewed=='Changed') echo "CHECKED"; ?> value="Changed" id="chbx_chart_changed" name="chbx_chart" tabindex="2"></span>Changes in H&P documented</label>
                                                                        	<?php
																			}
																			?>
                                                                        </div>                                                                        
                                                                        <div class="col-md-5 col-sm-12 col-xs-12 col-lg-5">
                                                                              <?php
																			  if($version_num > 2) {
																			  ?>
                                                                              <div class="col-md-5 col-sm-6 col-xs-12 col-lg-5">
                                                                                  <label for="chbx_npo_id" ><span>
                                                                                    <input <?php if($npo=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_npo_id" name="chbx_npo"  tabindex="3" /></span>
                                                                                     NPO </label>
                                                                               </div>
																			   <?php
																			  }
																			  ?>
                                                                                <div class="col-md-3 col-sm-6 col-xs-12 col-lg-3">
																						<label for="chbx_alert_id">Alert and Awake </label>
                                                                                </div>		          
                                                                                <div class="col-md-4 col-sm-6 col-xs-12 col-lg-4">
                                                                                		<select data-title="selNoWidth" title="Select Alert & Awake" class="form-control selectpicker"  name="chbx_alert" id="chbx_alert_id" >
                                                                                            <option value="1" <?php if($alertOriented=="1") { echo "selected"; } else{ echo "selected" ; }?> >Oriented x3</option>
                                                                                            <option value="2" <?php if($alertOriented=="2") { echo "selected"; }?>>Oriented x2</option><!-- Alert -->
                                                                                            <!-- <option value="3" <?php //if($alertOriented=="3") { echo "selected"; }?>>Awake</option> -->
                                                                                            <option value="4" <?php if($alertOriented=="4") { echo "selected"; }?>>Confused</option>
                                                                                            <option value="5" <?php if($alertOriented=="5") { echo "selected"; }?>>Disoriented</option>
                                                                                            <option value="6" <?php if($alertOriented=="6") { echo "selected"; }?>>Combative</option>
                                                                                        </select>
                                                                                        
                                                                                </div>                                                                  
                                                                            
                                                                        </div>
                                                                    </div>	 
                                                                 </div>
                                                                 <div class="inner_safety_wrap">
                                                                    <div class="row">
                                                                        <div class="col-md-2 col-sm-6 col-xs-12 col-lg-2">
                                                                            <label for="chbx_assist_id">
                                                                            	<input <?php if($patientConfirm_assist_by_translator=='yes') echo "CHECKED"; ?> type="checkbox" value="yes" id="chbx_assist_id" name="chbx_assist"  tabindex="5" />&nbsp;
                                                                                 Assisted by Translator</label>
                                                                        </div>
                                                                        <div class="col-md-2 col-sm-6 col-xs-12 col-lg-2">
                                                                            <label for="chbx_proced_id">
                                                                            	<span class="colorChkBx" style=" <?php if($procedurePrimaryVerified) { echo $whiteBckGroundColor;}?>" onClick="changeChbxColor('chbx_proced');" ><input <?php if($procedurePrimaryVerified=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_proced_id" name="chbx_proced"  tabindex="6"></span>
                                                                                
                                                                                Procedure Verified <br />
                                                                                <span style="font-weight:80px; "><?php echo wordwrap($patient_primary_procedure,35,"<br>",1); ?></span>
                                                                           	</label>
                                                                        </div>
                                                                        <div class="clearfix visible-sm margin_adjustment_only"></Div>
                                                                         <div class="col-md-3 col-sm-6 col-xs-12 col-lg-3">
                                                                            <label for="chbx_sec_veri_id"> 
                                                                            <input <?php if($procedureSecondaryVerified=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_sec_veri_id" name="chbx_sec_veri"  tabindex="7">Secondary Verified&nbsp; <?php echo wordwrap($patient_secondary_procedure,8,"\n",1); ?></label>
                                                                        </div>
                                                                        <div class="col-md-5 col-sm-6 col-xs-12 col-lg-5">
                                                                            
                                                                            <div class="col-md-5 col-sm-6 col-xs-12 col-lg-5">
                                                                              <label for="chbx_site_id" ><span class="colorChkBx" style=" <?php if($siteVerified) { echo $whiteBckGroundColor;}?>" onClick="changeChbxColor('chbx_site');" >
                                                                                <input <?php if($siteVerified=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_site_id" name="chbx_site"  tabindex="8" />&nbsp;</span>
                                                                                 Site Verified &nbsp; <?php echo $siteShow; ?> </label>
                                                                            </div>
                                                                            <?php
																			if($version_num > 2 && ($settings['anes_mallampetti_score'] || trim($mallampetti_score))) {
																			?>
                                                                                <div class="col-md-3 col-sm-6 col-xs-12 col-lg-3">
                                                                                    <label for="mallampetti_score_id">Mallampetti&nbsp;Score</label>
                                                                                </div>
                                                                                <div class="col-md-4 col-sm-6 col-xs-12 col-lg-4">
                                                                                    <select  title="Select Mallampetti Score" class="form-control selectpicker"  name="mallampetti_score" id="mallampetti_score_id" >
                                                                                        <option value="" selected>Select</option>
                                                                                        <option value="Class 1" <?php if($mallampetti_score=="Class 1") { echo "selected"; }?>>Class 1</option>
                                                                                        <option value="Class 2" <?php if($mallampetti_score=="Class 2") { echo "selected"; }?>>Class 2</option>
                                                                                        <option value="Class 3" <?php if($mallampetti_score=="Class 3") { echo "selected"; }?>>Class 3</option>
                                                                                        <option value="Class 4" <?php if($mallampetti_score=="Class 4") { echo "selected"; }?>>Class 4</option>
                                                                                    </select>
                                                                                </div>
                                                                            <?php
																			}else {
																			?>
                                                                            	<div class="col-md-7 col-sm-6 col-xs-12 col-lg-7"></div>
                                                                            <?php
																			}
																			?>
                                                                            
                                                                        </div>
                                                                    </div>	 
                                                                    <div class="row">
                                                                        <div class="col-md-4 col-sm-6 col-xs-12 col-lg-4">
                                                                            <label for="chbx_fp_exam" style="visibility:<?php echo $fpExamPerformedVisibility;?>; " ><input type="checkbox" <?php if($fpExamPerformed=='Yes') echo "CHECKED"; ?> value="Yes" id="chbx_fp_exam" name="chbx_fp_exam"  tabindex="3">Pt reassessed, stable for anesthesia/surgery </label>
                                                                        </div>
                                                                        <div class="col-md-3 col-sm-6 col-xs-12 col-lg-3"></div>
                                                                        <div class="col-md-5 col-sm-6 col-xs-12 col-lg-5">
                                                                            <?php
																			if($version_num > 4) {
																				$ivSelection 		= ucfirst($getPreOpNursingDetails->ivSelection);
																				$ivSelectionOther 	= stripslashes($getPreOpNursingDetails->ivSelectionOther);
																				$ivSelectionVal 	= (strtolower($ivSelection)=='other') ? $ivSelectionOther : $ivSelection;
																				
																				$ivSelectionSide 	= ucfirst($getPreOpNursingDetails->ivSelectionSide);
																				
																				$gauge 				= $getPreOpNursingDetails->gauge;
																				$gauge_other 		= $getPreOpNursingDetails->gauge_other;
																				$gaugeVal 			= (strtolower($gauge)=='other') ? $gauge_other : $gauge;
																				?>
																				<div class="col-md-12 col-sm-6 col-xs-12 col-lg-12">
																					<label for="">  IV <span style="color:#0033CC; font-weight:normal; margin-right:80px;"><?php  echo ($ivSelectionVal ? $ivSelectionVal : "______");?></span></label>
																					<?php
																					if(strtolower($ivSelection)!='other') {?>
																						<label for="">  Right/Left <span style="color:#0033CC; font-weight:normal; margin-right:80px;"><?php echo ($ivSelectionSide && $ivSelection) ? $ivSelectionSide : "______";?></span></label>
																						<label for="">  Gauge <span style="color:#0033CC; font-weight:normal; margin-right:80px;"><?php echo ($gaugeVal && $ivSelection) ? $gaugeVal : "______";?></span></label>
																					<?php
																					}?>
																				</div>
																			<?php
																			}?>
                                                                        </div>
                                                                    </div>	 

                                                                 </div>
                                                            </Div>
                                                           
                                                        <!--   @2nd col Ends     -->
                                                      </div>
                                                  </div>          
                                               	
                                                </div> 
                                       	</div>
                                            
                                         <div class="clearfix margin_adjustment_only"></div>
                                           
                                           <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                         		<div class="panel panel-default bg_panel_anesth">
                                              		<div class="panel-heading">
                                                    	<a  class="panel-title rob alle_link show-pop-trigger2 btn btn-default " id="allergies_id" onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', '10', $(this).offset().left, parseInt( $(this).offset().top -  $(document).scrollTop() - 130 )),document.getElementById('selected_frame_name_id').value='iframe_allergies_local_anes_rec';"> <span class="fa fa-caret-right"></span>  Allergies   </a>
                                                    </div>
                                                    
                                                    <div class="panel-body">
                                                        <div class="inner_safety_wrap">
                                                            <div class="row">
                                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                   <!--- ---- Table --->
                                                                   <div class="scheduler_table_Complete ">
                                                                       	<div class="my_table_Checkall table_slider_head">
                                                                                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                                                    <thead class="cf">
                                                                                        <tr>
                                                                                            <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Name</th>
                                                                                            <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Reaction </th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                 </table>
                                                                       	</div>
                                                                       	<div id="iframe_allergies_local_anes_rec" class="table_slider">
                                                                       		<?php
																				$allgNameWidth		=	208;
																				$allgReactionWidth	=	208;
																				include("health_quest_spreadsheet.php");
                                                                    		?>
                                                                      	</div>
                      	
                    												</div>
                      												<!--- ---- Table --->
                                                                </div>
                                                                <!-- Col-3 ends  -->
                                                            </div>	 
                                                         </div>
                                                    </div> <!-- Panel Body -->
                                                </div>
                                                
                                                <div class="clearfix margin_adjustment_only"></div>
                                           </div>
                                           <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                            	
                                                
                                                <div class="panel panel-default bg_panel_anesth">
                                              		<div class="panel-heading">
                                                    	<a data-placement="top" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " onClick="return showPreDefineMedFnNew('medication_name', 'medication_detail', '10', $(this).offset().left, parseInt( $(this).offset().top -$(document).scrollTop() +65 )),document.getElementById('selected_frame_name_id').value='iframe_medication_local_anes_rec';"> <span class="fa fa-caret-right"></span>  Medications    </a>
                                                    </div>
                                                    
                                                    <div class="panel-body">
                                                        <div class="inner_safety_wrap">
                                                            <div class="row">
                                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                   <!--- ---- Table --->
                                                                   <div class="scheduler_table_Complete ">
                                                                       <div class="my_table_Checkall table_slider_head">
                                                                                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                                                    <thead class="cf">
                                                                                        <tr>
                                                                                        	<th class="text-left col-md-5 col-lg-5 col-sm-5 col-xs-5">Name</th>
                                                                                        	<th class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3">Dosage </th>
                                                                                        	<th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4">Sig </th>
                                                                                        </tr>
                                                                                        
                                                                                    </thead>
                                                                                 </table>
                                                                       </div>
                                                                       <div class="table_slider">          
                                                                       	<?PHP
																			include("patient_anesthesia_medi_spreadsheet.php");
																		?>
                                                                      	</div>
                      
                    													</div>
                      													<!--- ---- Table --->
                                                                   
                                                                </div>
                                                                 <!-- Col-3 ends  -->
                                                            </div>	 
                                                         </div>
                                                    </div> <!-- Panel Body -->
                                                </div>
                                             </div>
                                            
                                            
                                            
                                            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                                                 <div class="panel panel-default bg_panel_or">
                                                   <div id="p_check_in" class="panel-body ">
                                                       <div class="inner_safety_wrap">
                                                          <Div class="row">
                                                       		<Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 padding_0">
                                                            
					<?PHP
					
								//CODE START TO GET BP,P,RR,SAO2 FROM 'PREOP-NURSING RECORD' IF THIS CHARTNOTE IS YET TO SAVE FIRST TIME									
                                              
												if ($copyBaseLineVitalSigns=="Yes"){
                                                    //$getPreOpNursingDetails = $objManageData->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfId);
                                                   
                                                    if($getPreOpNursingDetails) {
                                                        $preopnursing_vitalsign_id = $getPreOpNursingDetails->preopnursing_vitalsign_id;
                                                        //echo  $pConfId;
                                                        if($preopnursing_vitalsign_id) { 
                                                        //echo  $copyBaseLineVitalSigns;
                                                
                                                            $ViewPreopNurseVitalHeaderSignQry = "select * from `preopnursing_vitalsign_tbl` where  vitalsign_id = '".$preopnursing_vitalsign_id."'";
                                                            $ViewPreopNurseVitalHeaderSignRes = imw_query($ViewPreopNurseVitalHeaderSignQry) or die(imw_error()); 
                                                            $ViewPreopNurseVitalHeaderSignNumRow = imw_num_rows($ViewPreopNurseVitalHeaderSignRes);
															
                                                            if($ViewPreopNurseVitalHeaderSignNumRow>0) {
                                                                $ViewPreopNurseVitalHeaderSignRow = imw_fetch_array($ViewPreopNurseVitalHeaderSignRes);
                                                            	$preOpNursingVitalSignBp 	= $ViewPreopNurseVitalHeaderSignRow["vitalSignBp"];
                                                                $preOpNursingVitalSignP 	= $ViewPreopNurseVitalHeaderSignRow["vitalSignP"];
                                                                $preOpNursingVitalSignR 	= $ViewPreopNurseVitalHeaderSignRow["vitalSignR"];
                                                                $preOpNursingVitalSignO2SAT = $ViewPreopNurseVitalHeaderSignRow["vitalSignO2SAT"];
                                                            }
                                                        }
                                                    }
                                                    if($form_status=='') {
                                                        $bp  	= $preOpNursingVitalSignBp;
                                                        $P  	= $preOpNursingVitalSignP;
                                                        $rr  	= $preOpNursingVitalSignR;
                                                        $sao	= $preOpNursingVitalSignO2SAT;
                                                    }
                                                }
												
												if($bp_p_rr_time <> '00:00:00' && !empty($bp_p_rr_time)){
													$bp_p_rr_time=$objManageData->getTmFormat($bp_p_rr_time);//date('h:i A',strtotime($bp_p_rr_time));
												}else{
													$bp_p_rr_time	=	'';	
												}
                                                
                                                
                                            //CODE END TO GET BP,P,RR,SAO2 FROM 'PREOP-NURSING RECORD' IF THIS CHARTNOTE IS YET TO SAVE FIRST TIME
                                            $bprsaoBckGroundColor=$chngBckGroundColor;
                                           	if(trim($bp) || trim($P) || trim($rr) || trim($bp) || trim($sao))
												$bprsaoBckGroundColor=$whiteBckGroundColor;
											
											$bprsaoFocusKeyup	=	'changeTxtGroupColor(4,\'bp_temp\',\'bp_temp3\',\'bp_temp4\',\'bp_temp5\');'  ;
											if( constant('ANES_VITAL_SIGN_MANDATORY') == 'OFF' )
											{
												$bprsaoBckGroundColor=$whiteBckGroundColor;	
												$bprsaoFocusKeyup = ''; 
											}
                                            ?>
                                            	
                                                              <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                <div class="row">
                                                                    <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12  text-center" for="txt_bp_p_rr_time"> Time: </label>    
                                                                    <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12  text-center">
																		<input class="form-control" id="txt_bp_p_rr_time" type="text" value="<?php echo $bp_p_rr_time; ?>" name="txt_bp_p_rr_time" maxlength="8"   onClick=" if(this.value=='') { clearVal_c();displayTimeAmPm('txt_bp_p_rr_time');this.style.backgroundColor='#FFFFFF'; }  " onFocus="changeTxtGroupColor(1,'txt_bp_p_rr_time');" style=" <?php if(trim(!$bp_p_rr_time)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?> " onKeyUp="displayText200=this.value;changeTxtGroupColor(1,'txt_bp_p_rr_time');" onBlur="changeTxtGroupColor(1,'txt_bp_p_rr_time');" />                                                                    </span>
                                                                </div>
                                                              </div>
                                                              <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                    <div class="row">
                                                                        <label class="col-md-12 col-lg-3 col-xs-12 col-sm-12  text-center" for="bp_temp"> BP: </label>    
                                                                        <span class="col-md-12 col-lg-9 col-xs-12 col-sm-12  text-center">
                                                                           <input  type="hidden" id="bp" name="bp_hidden" />
                                                                           <input type="hidden" name="currTime" />
                                                                           <input class="form-control" id="bp_temp" type="text" value="<?php echo $bp; ?>" name="bp1" maxlength="7" style=" <?php echo $bprsaoBckGroundColor;?>" onFocus="<?=$bprsaoFocusKeyup?>"  onKeyUp="displayText1=this.value;<?=$bprsaoFocusKeyup?>" onClick=" getShow(parseInt($(this).offset().top)-172,parseInt($(this).offset().left)-25,'flag1');" />
                                                                           
                                                                       	</span>
                                                                    </div>
                                                                </div>
                                                                 <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
                                                                    <div class="row">
                                                                        <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12  text-center" for="bp_temp3">
                                                                            P:
                                                                        </label>    
                                                                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12  text-center">
                                                                           <input id="bp_temp3" onFocus="<?=$bprsaoFocusKeyup?>" type="text" name="p" value="<?php echo $P; ?>" size="2" onKeyUp="displayText3=this.value;<?=$bprsaoFocusKeyup?>" class="form-control" style=" <?php echo $bprsaoBckGroundColor; ?>" onClick="getShow(parseInt($(this).offset().top)-172,parseInt($(this).offset().left)-47,'flag3');" />
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                 <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
                                                                    <div class="row">
                                                                        <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12  text-center" for="bp_temp4">
                                                                            RR:
                                                                        </label>    
                                                                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12  text-center">
                                                                           <input id="bp_temp4" onFocus="<?=$bprsaoFocusKeyup?>" type="text" value="<?php echo $rr; ?>" name="rr" size="2" onKeyUp="displayText4=this.value;<?=$bprsaoFocusKeyup?>" class="form-control" style="<?php echo $bprsaoBckGroundColor;?>" onClick="getShow(parseInt($(this).offset().top)-172,parseInt($(this).offset().left)-25,'flag4');" />
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                 <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
                                                                    <div class="row">
                                                                        <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12  text-center" for="bp_temp5">
                                                                            SaO2:
                                                                        </label>    
                                                                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12  text-center">
                                                                           <input id="bp_temp5" onFocus="<?=$bprsaoFocusKeyup?>" type="text" value="<?php echo $sao; ?>" name="sao" size="2" onKeyUp="displayText5=this.value;<?=$bprsaoFocusKeyup?>" class="form-control" style="<?php echo $bprsaoBckGroundColor;?>" onClick="getShow(parseInt($(this).offset().top)-172,parseInt($(this).offset().left)-25,'flag5');">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                
                                                            </Div>	
                                                            <Div class="clearfix margin_adjustment_only visible-sm" ></Div>
                                                            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6 padding_0">
																	<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
																	<?php
																		
                                                                        $defaultEvaluation2	=	'';
                                                                        if( $evaluation2 == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
                                                                        {
																			if(is_array($defaultLocalAnesEvalArr) && count($defaultLocalAnesEvalArr) > 0 )
																			{
																				$defaultLocalAnesEvalNameArr = array();
																				foreach($defaultLocalAnesEvalArr as $k => $v)
																				{
																					if($v['isDefault'] == '1')
																					{
																						$defaultLocalAnesEvalNameArr[] = $v['name'];
																					}
																				}
																				
																				$defaultEvaluation2 = implode(', ',$defaultLocalAnesEvalNameArr);
																				$evaluation2 = $defaultEvaluation2;		
																			}
																			
                                                                        }
                                                                    
                                                                        $defaultDentation	=	'';
                                                                        if( $dentation == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
                                                                        {
																			if(is_array($defaultLocalAnesDentArr) && count($defaultLocalAnesDentArr) > 0 )
																			{
																				$defaultLocalAnesDentNameArr = array();
																				foreach($defaultLocalAnesDentArr as $k => $v)
																				{
																					if($v['isDefault'] == '1')
																					{
																						$defaultLocalAnesDentNameArr[] = $v['name'];
																					}
																				}
																				
																				$defaultDentation = implode(', ',$defaultLocalAnesDentNameArr);
																				$dentation = $defaultDentation;		
																			}
																			
                                                                        }
                                                                    
                                                                    ?>
                                                                           <div class="row">
                                                                                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                                                    <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                                                                                        <a data-placement="top" id="evaluation_id" class="panel-title rob alle_link show-pop-trigger2 btn btn-default" onClick="return showEvaluationLocalAnesFn('local_anes_revaluation2_id', '', 'no', $(this).offset().left, (parseInt($(this).offset().top - 190 ) < $(document).scrollTop() ? parseInt($(this).offset().top + 30 ) : parseInt($(this).offset().top - 190 )  )),document.getElementById('selected_frame_name_id').value='&nbsp;';">  <span class="fa fa-play"></span>Evaluation  </a>
                                                                                    </div>
                                                                                    
                                                                                    <div class="clearfix visible-sm margin_adjustment_only"></div>
                                                                                    <div class="col-md-9 col-sm-12 col-xs-12 col-lg-9  text-center">
                                                                                        <textarea name="evaluation2" id="local_anes_revaluation2_id" <?php echo $keyPressInput.$focusInput.$keyUpInput;?> class="form-control" style="resize:none;<?php if(trim(!$evaluation2)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>"  tabindex="6"><?php echo str_ireplace('&','&amp;',stripslashes($evaluation2)); ?></textarea> 
                                                                                    </div> 
                                                                            	</div>
                                                                                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                                                    <?php
																					if($version_num > 2) {
																					?>
                                                                                    <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                                                                                        <a data-placement="top" id="dentation_id" class="panel-title rob alle_link show-pop-trigger2 btn btn-default" onClick="return showDentationLocalAnesFn('local_anes_dentation_id', '', 'no', $(this).offset().left, (parseInt($(this).offset().top - 190 ) < $(document).scrollTop() ? parseInt($(this).offset().top + 30 ) : parseInt($(this).offset().top - 190 )  )),document.getElementById('selected_frame_name_id').value='&nbsp;';">  <span class="fa fa-play"></span>  Dentition  </a>
                                                                                    </div>
                                                                                    
                                                                                    <div class="clearfix visible-sm margin_adjustment_only"></div>
                                                                                    <div class="col-md-9 col-sm-12 col-xs-12 col-lg-9  text-center">
                                                                                        <textarea name="dentation" id="local_anes_dentation_id" class="form-control" style="resize:none;" onKeyUp="textAreaAdjust(this);"  tabindex="6"><?php echo str_ireplace('&','&amp;',stripslashes($dentation)); ?></textarea> 
                                                                                    </div> 
                                                                                    <?php
																					}
																					?>
                                                                            	</div>
                                                                            </div>
                                                                    </div>			                                                            	
                                                            </Div>	
                                                          </Div>  
                                                       </div>
                                                       
                                                       <!--- -- - Second WRAP ----------------->
                                                       <div class="inner_safety_wrap">
                                                                    <div class="row">

                                                                         <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                                                                            <label for="chbx_cardiovesc_id">
                                                                            <input <?php if($stableCardiPlumFunction=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="chbx_cardiovesc_id" name="chbx__cardiovesc"  tabindex="7" /> Stable cardiovascular and Pulmonary function </label>
                                                                        </div>
                                                                        <?php 
																			if(($bsValue=="" || $NA=="") && $form_status==""){
																				//$getPreOpNursingDetails = $objManageData->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfId);
																				$bsValue = $getPreOpNursingDetails->bsValue;
																				$NA = $getPreOpNursingDetails->NA;
																			}
																			//if($bsValue){
                                                               			 ?>
                                                                
            	                                                        <div class="clearfix visible-sm margin_adjustment_only"></div>
                                                                        <div class="col-md-2 col-sm-6 col-xs-12 col-lg-2">
                                                                            <label for="b_s">Blood Sugar </label>
                                                                        </div>
                                                                    
                                                                        <div class="col-md-2 col-sm-6 col-xs-12 col-lg-2">
                                                                        
                                                                            <input  type="checkbox" name="chkBoxNS" id="chkBoxNS" <?php if($NA=="1") { echo "checked";  }?> value="1" onClick="javascript:if(this.checked==true){this.value='1';document.getElementById('bsValue').readOnly = true;document.getElementById('bsValue').value = '';}else{this.value='0';document.getElementById('bsValue').readOnly = false;}" />&nbsp;
                                                                            <label for="chkBoxNS">N/A</label>
                                                                        </div>
																	    
                                                                        <div class="col-md-5 col-sm-12 col-xs-12 col-lg-5">
                                                                         	<div class="row">
																				<div class="col-md-1 col-sm-1 col-xs-1 col-lg-1">
																						<label for="Value"> Value </label>
                                                                                </div>		          
                                                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                                		<input type="text" value="<?php echo $bsValue; ?>" class="form-control" maxlength="8" size="8" id="bsValue" name="bsValue"/>
																						<?php 
                                                                                        		if($NA=="1") 
																								{
																									echo "<script>document.getElementById('bsValue').readOnly = true; document.getElementById('bsValue').value = '';</script>";
																								}
																						?>
                                                                                </div>                                                                  
                                                                            </div>
                                                                        </div>
                                                                    </div>	 
                                                                 </div>
                                                       
                                                        <!--- -- - Second WRAP ----------------->
                                                 
                                                  <div class="inner_safety_wrap">
                                                                    <div class="row">
                                                                         <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                            <label for="chbx_plan_id"> 
                                                                           	<input <?php if($planAnesthesia=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_plan_id" name="chbx_plan"  tabindex="7">
																				Plan regional anesthesia with sedation.Risks,benefits and alternatives of anesthesia plan have been discussed.
                                                                            </label>
                                                                        </div>
                                                                        <Div class="clearfix"></Div>
                                                                        <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                                                                            <label for="chbx_all_qus">
                                                                            	<span  onClick="changeChbxColor('chbx_all_qus');" class="colorChkBx" style=" <?php if($allQuesAnswered) { echo $whiteBckGroundColor;}?>" ><input style="vertical-align:top; top:-1px; "  <?php if($allQuesAnswered=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_all_qus" name="chbx_all_qus"  tabindex="7"></span>All Questions Answered
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                                           <div class="row">
                                                                           		<div class="col-md-5 col-sm-6 col-xs-6 col-lg-4">
                                                                                 <label>
                                                                                	ASA Physical Status:
                                                                                </label> 
                                                                                </div>
                                                                                <?php
																																						 			
																																				$onClick = 'onClick="changeDiffChbxColor(3,\'phys_status_1\',\'phys_status_2\',\'phys_status_3\');"';
																																				if( $settings['asa_4'] || $asaPhysicalStatus == '4' ) {
																																					$onClick = 'onClick="changeDiffChbxColor(4,\'phys_status_1\',\'phys_status_2\',\'phys_status_3\',\'phys_status_4\');"';
																																				}
																																				
																																						 		?>
                                                                          			
                                                                           		<div class="col-md-5 col-sm-6 col-xs-6 col-lg-5">
                                                                                	<div class="row">
                                                                                    	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3"> 
                                                                                        	<label for="phys_status_1" onClick="javascript:checkSingle('phys_status_1','phys_status')">
                                                                                            	<span <?php echo $onClick;?> class="colorChkBx" style=" <?php if($asaPhysicalStatus) { echo $whiteBckGroundColor;}?>" ><input <?php if($asaPhysicalStatus=='1') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="1" id="phys_status_1" tabindex="7" /></span>&nbsp;I
                                                                                                 </label>
                                                                                        </div>	
                                                                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3"> 
                                                                                        	<label for="phys_status_2" onClick="javascript:checkSingle('phys_status_2','phys_status')" >
                                                                                            	<span <?php echo $onClick;?> class="colorChkBx" style=" <?php if($asaPhysicalStatus) { echo $whiteBckGroundColor;}?>" ><input <?php if($asaPhysicalStatus=='2') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="2" id="phys_status_2"  tabindex="7" /></span>&nbsp;II
                                                                                                </label>
                                                                                        </div>	
                                                                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3"> 
                                                                                        	<label for="phys_status_3" onClick="javascript:checkSingle('phys_status_3','phys_status')" >
                                                                                            	<span <?php echo $onClick;?> class="colorChkBx" style=" <?php if($asaPhysicalStatus) { echo $whiteBckGroundColor;}?>" ><input <?php if($asaPhysicalStatus=='3') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="3" id="phys_status_3" tabindex="7" /></span>&nbsp;III
                                                                                                 </label>
                                                                                        </div>
                                                                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                                        	<?php
																																														if($settings['asa_4'] || $asaPhysicalStatus == '4' ) {
																																													?>
                                                                                          		<label for="phys_status_4" onClick="javascript:checkSingle('phys_status_4','phys_status')" >
                                                                                            		<span <?php echo $onClick;?> class="colorChkBx" style=" <?php if($asaPhysicalStatus) { echo $whiteBckGroundColor;}?>" ><input <?php if($asaPhysicalStatus=='4') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="4" id="phys_status_4" tabindex="7" /></span>&nbsp;IV
                                                                                            	</label>
                                                                                       		<?php } ?>     	
                                                                                        </div>	
                                                                                    
                                                                                    </div>
                                                                                </div>	
                                                                           </div>
                                                                        </div>
                                                                         <?php
                                                                
                                                                //CODE RELATED TO ANES 1 SIGNATURE ON FILE
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
																		$signAnesthesia1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signAnesthesia1DateTime);
                                                                        $Anesthesia1SubType = getUserSubTypeFun($signAnesthesia1Id); //FROM common/commonFunctions.php
                                                                    }
                                                                    if($Anesthesia1SubType=='CRNA') {
                                                                        $Anesthesia1PreFix = '';
                                                                    }
                                                                    
                                                                    
                                                                    //CODE TO REMOVE ANES 1 SIGNATURE
                                                                        if($_SESSION["loginUserId"]==$signAnesthesia1Id) {
                                                                            $callJavaFunPreOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia1','delSign');";
                                                                        }else {
                                                                            $callJavaFunPreOpDel = "alert('Only $Anesthesia1PreFix ".addslashes($Anesthesia1Name)." can remove this signature');";
                                                                        }
                                                                    //END CODE TO REMOVE ANES 1 SIGNATURE	
                                                                //END CODE RELATED TO ANES 1 SIGNATURE ON FILE
                                                            ?>
                                                                        
                                                                        <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 visible-sm">
                                                                        	<div class="clearfix margin_adjustment_only"></div>
                                                                        </Div>
                                                                       <div class=" col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                                            <div class="inner_safety_wrap" id="TDanesthesia1NameId" style="display:<?php echo $TDanesthesia1NameIdDisplay;?>;">
                                                                                <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signAnesthesiaIdBackColor?>;" onClick="javascript:<?php echo $callJavaFunPreOp;?>"> Anesthesia Provider Signature </a>
                                                                            </div>
                                                                            <div class="inner_safety_wrap collapse" id="TDanesthesia1SignatureId" style="display:<?php echo $TDanesthesia1SignatureIdDisplay;?>;">
                                                                                <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer; " onClick="javascript:<?php echo $callJavaFunPreOpDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$Anesthesia1PreFix." ".$Anesthesia1Name; ?>  </a></span>	     
                                                                                <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $anesthesia1SignOnFileStatus;?></span>
                                                                                <span class="rob full_width"> <b> Signature Date</b> <?php echo $signAnesthesia1DateTimeFormatNew;?></span>
                                                                            </div>
                                                                       </div>
                                                                    </div>	 
                                                                 </div>
                                                        <!--- -- - Second WRAP ----------------->
                                                  </div>
                                                   </div>          
                                                 </div>
                                                 <div class="clearfix margin_adjustment_only"></div>
                                                 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_anesth" >
                                                    <div class="scanner_win new_s">
                                                     <h4>
                                                        <span>Holding area through Intra-Op</span>      
                                                     </h4>
                                                    </div>
                                                </div>
                                              <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12"  ><!--style="background-color:<?php echo $tablebg_local_anes; ?>;"-->
                                              		<div class="col-md-8 col-sm-12 col-xs-8 col-lg-8" id="HoldingDiv">
                                                        <Div class="row">
                                                         	<Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            	<div  class="row">
                                                                	<Div class="inner_left_slider_anesth">
                                                                    	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                        	<?php
																			if(constant("ANES_OR_START_STOP_TIME")=="YES") {
																			?>
                                                                            <div class="row">
                                                                            	<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                                                                	<small class="text-center" for="bp_temp238">OR Start Time   </small>
                                                                                    <input type="text" id="bp_temp238" name="orStartTime" maxlength="6" class="form-control" style=" <?php echo $whiteBckGroundColor?> "  value="<?php echo $orStartTime;?>" onKeyUp="displayText238=this.value" onClick="getShow(parseInt(findPos_Y('bp_temp238'))-190,parseInt(findPos_X('bp_temp238')),'flag238');<?php if($orStartTime=="") {?>clearVal_c();displayTime('bp_temp238');<?php } ?>"  />
                                                                                </div>                                     
                                                                                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                                                                	<small class="text-center" for="bp_temp239"> OR Stop Time   </small>
                                                                                    <input type="text" id="bp_temp239" name="orStopTime" class="form-control" value="<?php echo $orStopTime;?>" onKeyUp="displayText239=this.value" onClick="getShow(parseInt(findPos_Y('bp_temp239'))-190,parseInt(findPos_X('bp_temp239'))-90,'flag239');<?php if($orStopTime=="") {?>clearVal_c();return displayTime('bp_temp239'); <?php } ?>"  />
                                                                               	</div>	
                                                                            </div>
                                                                            <?php
																			}
																			?>
                                                                        </div>
                                                                    	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                        	<div class="row">
                                                                            	<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                                                                	<small class="text-center" for="bp_temp6"> Anes Start Time   </small>
                                                                                    <input onFocus="changeTxtGroupColor(1,'bp_temp6');"  type="text" id="bp_temp6" name="startTime" maxlength="6" class="form-control" style=" <?php if(trim(!$startTime)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?> "  value="<?php echo $startTime;?>" onKeyUp="displayText6=this.value;changeTxtGroupColor(1,'bp_temp6');" onClick="getShow(parseInt(findPos_Y('bp_temp6'))-190,parseInt(findPos_X('bp_temp6'))-90,'flag6');<?php if($startTime=="") {?>clearVal_c();displayTime('bp_temp6');changeTxtGroupColor(1,'bp_temp6'); <?php } ?>"  onBlur="changeTxtGroupColor(1,'bp_temp6');"  />
                                                                                </div>                                     
                                                                                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                                                                	<small class="text-center" for="bp_temp7"> Anes Stop Time   </small>
                                                                                    <input type="text" id="bp_temp7" name="stopTime" class="form-control" value="<?php echo $stopTime;?>" onKeyUp="displayText7=this.value" onClick="getShow(parseInt(findPos_Y('bp_temp7'))-190,parseInt(findPos_X('bp_temp7'))-90,'flag7');<?php if($stopTime=="") {?>clearVal_c();return displayTime('bp_temp7'); <?php } ?>"  />
                                                                               	</div>	
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                        	<div class="row">
                                                                            	<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                                                                	<small class="text-center" for="bp_temp100"> Start Time   </small>
                                                                                    <input type="text" id="bp_temp100" name="newStartTime2" maxlength="6" class="form-control" value="<?php echo $newStartTime2;?>" onKeyUp="displayText100=this.value;" onClick="getShow(parseInt(findPos_Y('bp_temp100'))-190,parseInt(findPos_X('bp_temp100'))-90,'flag100');<?php if($newStartTime2=="") {?>clearVal_c();displayTime('bp_temp100'); <?php } ?>"  />
                                                                                </div>                                     
                                                                                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                                                                	<small class="text-center" for="bp_temp101"> Stop Time   </small>
                                                                                	<input type="text" id="bp_temp101" name="newStopTime2" class="form-control" value="<?php echo $newStopTime2;?>" onKeyUp="displayText101=this.value" onClick="getShow(parseInt(findPos_Y('bp_temp101'))-190,parseInt(findPos_X('bp_temp101'))-90,'flag101');<?php if($newStopTime2=="") {?>clearVal_c();return displayTime('bp_temp101'); <?php } ?>"  />    
                                                                               	</div>	
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                        	<div class="row">
                                                                            	<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                                                                	<small class="text-center" for="bp_temp102"> Start Time   </small>
                                                                                    <input type="text" id="bp_temp102" name="newStartTime3" maxlength="6" class="form-control" value="<?php echo $newStartTime3;?>" onKeyUp="displayText102=this.value;" onClick="getShow(parseInt(findPos_Y('bp_temp102'))-190,parseInt(findPos_X('bp_temp102'))-90,'flag102');<?php if($newStartTime3=="") {?>clearVal_c();displayTime('bp_temp102'); <?php } ?>"   />
                                                                                </div>                                     
                                                                                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                                                                	<small class="text-center"> Stop Time   </small>
                                                                                	<input type="text" id="bp_temp103" name="newStopTime3" class="form-control" value="<?php echo $newStopTime3;?>" onKeyUp="displayText103=this.value" onClick="getShow(parseInt(findPos_Y('bp_temp103'))-190,parseInt(findPos_X('bp_temp103'))-90,'flag103');<?php if($newStopTime3=="") {?>clearVal_c();return displayTime('bp_temp103'); <?php } ?>"  />	    
                                                                              	</div>	
                                                                            </div>
                                                                        </div>
                                                                    </Div>
                                                                </div>
                                                            	
                                                            </Div>
                                                        </Div>
                                                    
                                                        <Div class="row">
                                                        	<Div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                            	<Div class="inner_left_slider_anesth">
																	
																	<div class="ekg-medbox-div">
																		<input value="<?php if($blank3_label){echo htmlentities($blank3_label);}elseif($blank3_label=='' && $txtInterOpDrugs3!=''){echo htmlentities($txtInterOpDrugs3);}  ?>" name="blank3_label"  type="text" id="txt_field07" tabindex="1" class="form-control"/>
																		<?php echo $blank3_edit_icon;?>
																	</div>
																	<div class="ekg-medbox-div">		
																		<input value="<?php if($blank4_label){echo htmlentities($blank4_label);}elseif($blank4_label=='' && $txtInterOpDrugs4!=''){echo htmlentities($txtInterOpDrugs4);}  ?>" name="blank4_label"  type="text" id="txt_field08" tabindex="1" class="form-control"/>
																		<?php echo $blank4_edit_icon;?>
																	</div>
																	<div class="ekg-medbox-div">
																		<input value="<?php if($blank1_label){echo htmlentities($blank1_label);}elseif($blank1_label=='' && $txtInterOpDrugs1!=''){echo htmlentities($txtInterOpDrugs1);}  ?>" name="blank1_label"  type="text" id="txt_field05" tabindex="1" class="form-control"/>
																		<?php echo $blank1_edit_icon;?>
																	</div>
																	<div class="ekg-medbox-div"> 
																		<input  class="form-control" value="<?php if($blank2_label){echo htmlentities($blank2_label);}elseif($blank2_label=='' && $txtInterOpDrugs2!=''){echo htmlentities($txtInterOpDrugs2);} ?>" name="blank2_label"  type="text" id="txt_field06"  tabindex="1"/>
																		<?php echo $blank2_edit_icon;?>
																	 </div>
																	<div class="ekg-medbox-div"> 
																		<input class="form-control" value="<?php echo htmlentities($mgPropofol_label);  ?>" name="mgPropofol_label"  type="text" id="mgPropofol_label_id" tabindex="1"/>
																		<?php echo $propofol_edit_icon;?>
																	 </div>
																	<div class="ekg-medbox-div"> 
																		<input class="form-control" value="<?php echo htmlentities($mgMidazolam_label);  ?>" name="mgMidazolam_label"  type="text" id="mgMidazolam_label_id" tabindex="1"/>
																		<?php echo $midazolam_edit_icon;?>
																	 </div>
																	<div class="ekg-medbox-div"> 
																		<input class="form-control" value="<?php echo htmlentities($mgKetamine_label);  ?>" name="mgKetamine_label"  type="text" id="mgKetamine_label_id" tabindex="1"/>
																		<?php echo $ketamine_edit_icon;?>
																	 </div>
																	<div class="ekg-medbox-div"> 
																		<input class="form-control" value="<?php echo htmlentities($mgLabetalol_label);  ?>" name="mgLabetalol_label"  type="text" id="mgLabetalol_label_id" tabindex="1"/>
																		<?php echo $labetalol_edit_icon;?>
																	 </div>
																	<div class="ekg-medbox-div"> 
																		<input class="form-control" value="<?php echo htmlentities($mcgFentanyl_label);  ?>" name="mcgFentanyl_label"  type="text" id="mcgFentanyl_label_id" tabindex="1"/>
																		<?php echo $Fentanyl_edit_icon;?>
																	 </div>
																	<div class="ekg-medbox-div">
																		<label style="height:29px; margin-bottom:5px; display:block" >SaO<sub>2</sub></label>
																		<?php echo $spo2_edit_icon;?>
																	 </div>
																	<div class="ekg-medbox-div">
																		<label style="height:29px; margin-bottom:5px;  display:block" >O<sub>2</sub>l/m</label>
																		<?php echo $o2lpm_edit_icon;?>
																	 </div>
																	
                                                             	</Div>
                                                            </Div>
                                                            <?php
																		$TxtBoxTopValue = "190";
																		$gridOnFocus='onFocus="changeMultiTxtGroupColor();"';
																		$gridKeyUp='changeMultiTxtGroupColor();';
															?>
                                                            
                                                            <Div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                                            
                                                            <Div class="inner_right_slider_anesth" id="gridTxtDivId" style=" overflow-y:hidden; overflow-x:scroll; width:100%; height:388px; padding:0; color:#FFFFFF ">
                                                                	<ul class="col-md-12 col-lg-12 col-xs-12 col-sm-12 graphUp padding_0 " style="width:990px; margin-left:25px; position:static">
                                                                    <?php 
																		$gridCounter	=	7;
																		foreach($dosageArr as $dosage)
																		{
																			echo '<li>';
																			for($L = 1; $L <= 20 ; $L++)
																			{
																				$gridCounter++ ;
																				if($gridCounter == 98 ) $gridCounter += 10;
																				$varName	=	$dosage.'_'.$L;
																				$t_varName	=	't_'.$varName;
																				$bRight		=	($L == 20) ? 'border:1px solid #ccccc;' : '' ;
																				
																				$timeStamp	=	($$varName && $$t_varName &&$$t_varName <> '0000-00-00 00:00:00')	?	$objManageData->getFullDtTmFormatLocalAnes($$t_varName) : '';
																				
																				
																				echo '<input 	id="bp_temp'.$gridCounter.'"  '.$gridOnFocus.'
																										autocomplete="off"
																										onKeyUp="displayText'.$gridCounter.'=this.value;'.$gridKeyUp.'" 
																										onClick="getShowLocalCalc(parseInt($(this).offset().top ),parseInt($(this).offset().left + $(this).outerWidth()),\'flag'.$gridCounter.'\');"
																										value="'.htmlentities($$varName).'" type="text"  
																										name="'.$varName.'" 
																										style="'.$bRight.' border-color:'.$border_blue_local_anes.' ; '.$gridTxtBackColor.'"
																										title="'.$timeStamp.'" /><input type="hidden" name="'.$t_varName.'" value="'.$timeStamp.'" />';
																			}
																			echo '</li>';
																		}
																	?>	

                                                                              
																				<?php
                                                                                    //CODE TO CALCULATE TIME INTERVAL OF 15 MINUTES EACH
                                                                                    if($startTime<>"") {
                                                                                        $intervalTimeSplit = explode(":",$startTime);
                                                                                            $intervalTimeSplitPlusOne = $intervalTimeSplit[0]+1;
                                                                                        if($intervalTimeSplitPlusOne > 12) {
                                                                                            $intervalTimeSplitPlusOne = $intervalTimeSplitPlusOne-12;
                                                                                        }
                                                                                        if(strlen($intervalTimeSplitPlusOne)==1) {
                                                                                            $intervalTimeSplitPlusOne = "0".$intervalTimeSplitPlusOne;
                                                                                        }
                                                                                        
                                                                                        $intervalTimeSplitPlusTwo = $intervalTimeSplit[0]+2;
                                                                                        if($intervalTimeSplitPlusTwo > 12) {
                                                                                            $intervalTimeSplitPlusTwo = $intervalTimeSplitPlusTwo-12;
                                                                                        }
                                                                                        if(strlen($intervalTimeSplitPlusTwo)==1) {
                                                                                            $intervalTimeSplitPlusTwo = "0".$intervalTimeSplitPlusTwo;
                                                                                        }
                                                                                        
                                                                                        $intervalTimeSplitPlusThree = $intervalTimeSplit[0]+3;
                                                                                        if($intervalTimeSplitPlusThree > 12) {
                                                                                            $intervalTimeSplitPlusThree = $intervalTimeSplitPlusThree-12;
                                                                                        }
                                                                                        if(strlen($intervalTimeSplitPlusThree)==1) {
                                                                                            $intervalTimeSplitPlusThree = "0".$intervalTimeSplitPlusThree;
                                                                                        }
                            
                            
                                                                                        if($intervalTimeSplit[1]< 15) {
                                                                                            $intervalTimeMin1 = $intervalTimeSplit[0].":00";
                                                                                            $intervalTimeMin2 = $intervalTimeSplit[0].":15";
                                                                                            $intervalTimeMin3 = $intervalTimeSplit[0].":30";
                                                                                            $intervalTimeMin4 = $intervalTimeSplit[0].":45";
                                                                                            $intervalTimeMin5 = $intervalTimeSplitPlusOne.":00";
                                                                                            $intervalTimeMin6 = $intervalTimeSplitPlusOne.":15";
                                                                                            $intervalTimeMin7 = $intervalTimeSplitPlusOne.":30";
                                                                                            $intervalTimeMin8 = $intervalTimeSplitPlusOne.":45";
                                                                                            $intervalTimeMin9 = $intervalTimeSplitPlusTwo.":00";
                                                                                            $intervalTimeMin10 = $intervalTimeSplitPlusTwo.":15";
                                                                                            
                                                                                        }else if($intervalTimeSplit[1]>=15 && $intervalTimeSplit[1]< 30) {
                                                                                            $intervalTimeMin1 = $intervalTimeSplit[0].":15";
                                                                                            $intervalTimeMin2 = $intervalTimeSplit[0].":30";
                                                                                            $intervalTimeMin3 = $intervalTimeSplit[0].":45";
                                                                                            $intervalTimeMin4 = $intervalTimeSplitPlusOne.":00";
                                                                                            $intervalTimeMin5 = $intervalTimeSplitPlusOne.":15";
                                                                                            $intervalTimeMin6 = $intervalTimeSplitPlusOne.":30";
                                                                                            $intervalTimeMin7 = $intervalTimeSplitPlusOne.":45";
                                                                                            $intervalTimeMin8 = $intervalTimeSplitPlusTwo.":00";
                                                                                            $intervalTimeMin9 = $intervalTimeSplitPlusTwo.":15";
                                                                                            $intervalTimeMin10 = $intervalTimeSplitPlusTwo.":30";
                                                                                            
                                                                                        }else if($intervalTimeSplit[1]>=30 && $intervalTimeSplit[1]< 45) {
                                                                                            $intervalTimeMin1 = $intervalTimeSplit[0].":30";
                                                                                            $intervalTimeMin2 = $intervalTimeSplit[0].":45";
                                                                                            $intervalTimeMin3 = $intervalTimeSplitPlusOne.":00";
                                                                                            $intervalTimeMin4 = $intervalTimeSplitPlusOne.":15";
                                                                                            $intervalTimeMin5 = $intervalTimeSplitPlusOne.":30";
                                                                                            $intervalTimeMin6 = $intervalTimeSplitPlusOne.":45";
                                                                                            $intervalTimeMin7 = $intervalTimeSplitPlusTwo.":00";
                                                                                            $intervalTimeMin8 = $intervalTimeSplitPlusTwo.":15";
                                                                                            $intervalTimeMin9 = $intervalTimeSplitPlusTwo.":30";
                                                                                            $intervalTimeMin10 = $intervalTimeSplitPlusTwo.":45";
                                                                                            
                                                                                        }else if($intervalTimeSplit[1]>=45) {
                                                                                            $intervalTimeMin1 = $intervalTimeSplit[0].":45";
                                                                                            $intervalTimeMin2 = $intervalTimeSplitPlusOne.":00";
                                                                                            $intervalTimeMin3 = $intervalTimeSplitPlusOne.":15";
                                                                                            $intervalTimeMin4 = $intervalTimeSplitPlusOne.":30";
                                                                                            $intervalTimeMin5 = $intervalTimeSplitPlusOne.":45";
                                                                                            $intervalTimeMin6 = $intervalTimeSplitPlusTwo.":00";
                                                                                            $intervalTimeMin7 = $intervalTimeSplitPlusTwo.":15";
                                                                                            $intervalTimeMin8 = $intervalTimeSplitPlusTwo.":30";
                                                                                            $intervalTimeMin9 = $intervalTimeSplitPlusTwo.":45";
                                                                                            $intervalTimeMin10 = $intervalTimeSplitPlusThree.":00";
                                                                                        }
                                                                                    }	
                                                                                    //END CODE TO CALCULATE TIME INTERVAL OF 15 MINUTES EACH
                                                                                ?>
                                                                   	</ul>
                                                           	
                                                            </Div>
                                                            
                                                            </Div>
                                                            
                                                        </Div>
                                                         <div class="clearfix margin_adjustment_only border-dashed"></div>
                                                         <div class="clearfix margin_adjustment_only"></div>
                                                         <Div class="row">
                                                         	
                                                            <Div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                            	<label class="full_width text-center line_height"> EKG </label>
                                                            </Div><!--showEvaluationLocalAnesFn('local_anes_revaluation2_id', '', 'no', $(this).offset().left, (parseInt($(this).offset().top - 180 ) < $(document).scrollTop() ? parseInt($(this).offset().top + 30 ) : parseInt($(this).offset().top - 180 )  ))-->
                                                         	<Div class="col-md-9 col-sm-9 col-xs-9 col-lg-9" for="ekgBigRowId" onClick="return showEkgBigRowDiv('ekgBigRowId', '','no',parseInt($(this).offset().left - 200), (parseInt($(this).offset().top - 280 ) < $(document).scrollTop() ? parseInt($(this).offset().top) : parseInt($(this).offset().top - 280 )  ));"><!--onClick="return showEkgBigRowDiv('ekgBigRowId', '','no','5', '460');"-->
                                                            	<input id="ekgBigRowId" value="<?php echo stripslashes($ekgBigRowValue); ?>" type="text" class="form-control" name="ekgBigRowValue"  />
                                                            </Div>
                                                         </Div>
                                                         
                                                         <?php
																			$fixDateToDisplayOldApplet = '2009-06-14';
																			if($confimDOS < $fixDateToDisplayOldApplet) {
															?>
                                                         <Div class="row">
                                                         	<Div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                            	<label class="full_width text-center line_height"> &nbsp;</label>
                                                            </Div>
                                                         	<Div class="col-md-9 col-sm-9 col-xs-9 col-lg-9" for="ekgBigRowId" id="intervalId">
                                                            	
                                                                					<span style="padding-left:20px;"><?php echo $intervalTimeMin1;?></span>
                                                                                    <span style="padding-left:4px;"><?php echo $intervalTimeMin2;?></span>
                                                                                    <span style="padding-left:4px;"><?php echo $intervalTimeMin3;?></span>
                                                                                    <span style="padding-left:3px;"><?php echo $intervalTimeMin4;?></span>
                                                                                    <span style="padding-left:3px;"><?php echo $intervalTimeMin5;?></span>
                                                                                    <span style="padding-left:2px;"><?php echo $intervalTimeMin6;?></span>
                                                                                    <span style="padding-left:4px;"><?php echo $intervalTimeMin7;?></span>
                                                                                    <span style="padding-left:2px;"><?php echo $intervalTimeMin8;?></span>
                                                                                    <span style="padding-left:2px;"><?php echo $intervalTimeMin9;?></span>
                                                                                    <span style="padding-left:2px;"><?php echo $intervalTimeMin10;?></span>
                                                                                    <a href="javascript:void(0);" style="padding-left:4px; float:right; " onClick="javascript:if(!document.frm_local_anes_rec.startTime.value) { alert('Please select start time'); } else { showInterval(document.frm_local_anes_rec.startTime.value,'intervalId'); }">
                                                                                    		<b class="fa fa-backward" style="font-size:18px;"></b>
                                                                                 	</a>
                                                       			
                                                          	</Div>
                                                      	</Div>
                                                        <?php
																			 }
														$anesthesia_grid_class="";
														if($hide_anesthesia_grid=="Yes") {
															$anesthesia_grid_class = "hide";
														}
														?>
                                                        
                                                        <div class="clearfix margin_adjustment_only"></div>
                                                        <div class="clearfix margin_adjustment_only border-dashed"></div>
                                                        <Div class="row <?php echo $anesthesia_grid_class;?>">
                                                        
                                                         		<Div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">                                                               
                                                                
                                                                        <Div class="inner_left_slider_anesth border_adj">
                                                                          
                                                                            <span class="left_controls_scroll bg_white" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funDrawDownTirangle', 'tdArr1');" : "setAppIcon('tdArr1')"; ?>" id="tdArr1"><b class="fa fa-caret-down"></b></span>
                                                                            
                                                                            <span	class="left_controls_scroll" 
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funDrawDownTirangle', 'tdArr1');" : "setAppIcon('tdArr1')"; ?>"
                                                                        	> Systolic  </span>
                                                                            <span class="left_controls_scroll bg_bl"
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funDrawDownTirangle', 'tdArr1');" : "setAppIcon('tdArr1')"; ?>"
                                                                           	> Pressure </span>
                                                                          	<span 	class="left_controls_scroll " 
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funDrawUpTirangle', 'tdArr2');" : "setAppIcon('tdArr2')"; ?>"
                                                                                        id="tdArr2"
                                                                     		> <b class="fa fa-caret-up"></b> </span>
                                                                            <span class="left_controls_scroll bg_bl" 
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funDrawUpTirangle', 'tdArr2');" : "setAppIcon('tdArr2')"; ?>"
                                                                     		> Diastolic</span>
                                                                            <span 	class="left_controls_scroll" 
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funDrawUpTirangle', 'tdArr2');" : "setAppIcon('tdArr2')"; ?>"
                                                                      		> Pressure </span>
                                                                            <span  class="left_controls_scroll bg_white" 
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funCircleWtInterColor', 'tdRfill');" : "setAppIcon('tdRfill')"; ?>"
                                                                                        id="tdRfill"
                                                                         	><b class="fa fa-circle" style="color:#333;"></b> </span>
                                                                            <span 	class="left_controls_scroll"
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funCircleWtInterColor', 'tdRfill');" : "setAppIcon('tdRfill')"; ?>"
                                                                   			> Heart </span>
                                                                            <span class="left_controls_scroll bg_bl"
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funCircleWtInterColor', 'tdRfill');" : "setAppIcon('tdRfill')"; ?>"
                                                                           	> Rate </span>
                                                                            <span 	class="left_controls_scroll" 
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funCircleWtOutInterColor', 'tdRblank');" : "setAppIcon('tdRblank')"; ?>" 
                                                                                        id="tdRblank"
                                                                        	> <b class="fa fa-circle-o" style="color:#333;"></b> </span>
 																			<span 	class="left_controls_scroll bg_bl"
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funCircleWtOutInterColor', 'tdRblank');" : "setAppIcon('tdRblank')"; ?>"
                                                                      		> Spontaneous</span>
                                                                            <span 	class="left_controls_scroll" 
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('funCircleWtOutInterColor', 'tdRblank');" : "setAppIcon('tdRblank')"; ?>"
                                                                           	> Respiration </span>
                                                                            <span 	class="left_controls_scroll bg_bl"
                                                                            			id="tdRText" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('text', 'tdRText');" : "setAppIcon('tdRText')"; ?>"
                                                                        	> <b> T </b> </span>
                                                                            
                                                                         	<span 	class="left_controls_scroll"
                                                                            			onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('text', 'tdRText');" : "setAppIcon('tdRText')"; ?>"
                                                                        	> Input Value</span>
                                                                            
                                                                            <span class="left_controls_scroll bg_white"> 
                                                                                <b style="margin-right:2px;margin-left:2px; color:#333;" id="tdErase" class="fa fa-eraser" onClick="<?php echo ($blEnableHTMLGrid == true) ? "erase(); setEvent('erase', 'tdErase');" : "clearApp();"; ?>"></b>
                                                                                <b style="margin-right:2px;margin-left:2px; color:#333;" id="tdUndo" class=" rotate_icon2 fa fa-mail-reply" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('undo', 'tdUndo' ); processUndo();" : "undoApp();"; ?>"></b>
                                                                                <b style="margin-right:2px;margin-left:2px; color:#333;" id="tdRedo" class=" rotate_icon fa fa-mail-forward" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('redo','tdRedo'); processRedo();" : "redoApp();"; ?>"></b>
                                                                                <?php		if($blEnableHTMLGrid == true)	{ ?>
                                                                                		<b style="margin-right:2px;margin-left:2px; color:#333;" id="tdDrag"  class="fa fa-hand-rock-o" onClick="setEvent('drag', 'tdDrag');"></b>
                                                                               	<?php		 }		?>
                                                                            
                                                                            </span>
                                                                       	</Div>
                                                                  	
                                                           		</Div>
                                                                
                                                                <Div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                                           				<Div class="inner_right_slider_anesth">
                                                                        	
                                                                			<?php 
                                                                                        if($confimDOS < $fixDateToDisplayOldApplet) 
                                                                                        { 
                                                                            ?>
                                                            									<APPLET code="Test2Mac.class" codebase="common/applet/" archive="signs.jar" name="signs" 
                                                                                                				id="signs" width="520" height="370">	
                                                                                                                
                                                                                                    <PARAM name="bgImg" value="images/icon/bgGrid.jpg">
                                                                                                    <param name="signs" value="<?php echo $applet_data;?>">
                                                                                                    <param name="iconSize" value="14">
                                                                                                    <param name="txtActivate" value="inactive">
                                                                        						</APPLET>
                                                                                				<input type = "hidden" name = "appletVal" id = "appletVal" /> 
																			<?php
                                                                                        } 
                                                                                        else
                                                                                        {
                                                                            ?>
                                                         	
																							<?php 
                                                                                                if($blEnableHTMLGrid == false)
                                                                                                {
                                                                                            ?>
                                                                                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="height:100%;">
                                                                                                            <img src="images/back1.gif" style="cursor:pointer; border:none; " onClick="javascript:if(!document.frm_local_anes_rec.startTime.value) { alert('Please select start time'); } else { checkTimeNew(document.frm_local_anes_rec.startTime.value); }" />
                                                                                                            <!--<object type="application/x-java-applet" name="test_state" id="test_state" width="32" height="390">
                                                                                                                        <param name="code" value="StatImage.class" />
                                                                                                                        <param name="codebase" value="common/applet/" />
                                                                                                                        <param name="archive" value="Stat.jar" />
                                                                                                            </object>-->
                                                                                                        </div>
                                                                                            <?php 
                                                                                                }
                                                                                            ?>
                                                                                            
                                                                                            <div id="gridApltDivId" style="overflow:hidden;overflow-x:scroll; width:100%; height:100%; background:white;">
                                                                                            	<div class="row">
                                                                                                	<?php 	if($blEnableHTMLGrid == false) {	 ?>
                                                                                                    	
                                                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height:23px;">
                                                                                                        	<span id="applet_ids">
                                                                                                            	<!--<applet name = "test_intrval" id="paint1" code="TimeApplet.class" codebase="common/applet/" archive="Time.jar" class="valignBottom" height="20" width="985">
                                                                                                                <param name="time" value ="">
                                                                                                                </applet>-->
                                                                                                                <object type="application/x-java-applet" name="test_intrval" id="paint1" width="985" height="20">
                                                                                                                <param name="code" value="TimeApplet.class" />
                                                                                                                <param name="codebase" value="common/applet/" />
                                                                                                                <param name="archive" value="Time.jar" />
                                                                                                                <param name="time" value ="">
                                                                                                          		</object>
                                                                                                       		</span>
                                                                                                    	</div>
                                                                                               		<?php 		}		?>
                                                                                                    
                                                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
                                                                                                    		<input type="hidden" name="applet_time_interval" id="applet_time_interval">   
                                                                                                           	<?php 	if($blEnableHTMLGrid == false) {  ?>
                                                                                                            
                                                                                                            	<object type="application/x-java-applet" name="signs" id="signs" width="965" height="370">
                                                                                                                	<param name="code" value="Test2Mac.class" />
                                                                                                                    <param name="codebase" value="common/applet/" />
                                                                                                                    <param name="archive" value="Graph.jar" />
                                                                                                                    <param name="bgImg" value="images/icon/bgTest.jpg">
                                                                                                                    <param name = "signs" value ="<?php echo $applet_data;?>">
                                                                                                                    <param name="iconSize" value="14">
                                                                                                                    <param name="txtActivate" value="inactive">
                                                                                                                    <param name="valLineThick" value="3">
                                                                                                            	</object>
                                                                                                          	<?php		}	
																															elseif($blEnableHTMLGrid == true)
																															{ 
																											?>
                                                                                                            						<input type="hidden" name="hidAnesthesiaGridData" id="hidAnesthesiaGridData" value="<?php echo $html_grid_data; ?>" />    
                                                                                                                                    <input type="hidden" name="hidGridImgData" id="hidGridImgData" />
                                                                                                                                    
																																	<?php 
																																				
																																				if($sect=="print_emr") 
																																				{
																																					/*$filename="new_html2pdf/tess_TimeInterval.jpg";
																																					//$new_name=$_REQUEST["thumbimage"];
																																					$dest_file = "new_html2pdf/tess_TimeInterval.jpg"; 
																																					$width=550;
																																					$height=30;
																																					$thumb = imagecreatetruecolor(550, 25);
																																					$source = imagecreatefromjpeg($filename);
																																					imagecopyresampled($thumb, $source, 0, 0, 0, 0, 550, 30, $width, $height);
																																					imagejpeg($thumb,$dest_file,"100");*/
																																					//imagejpeg($thumb,'',"100");
																																		
																																					$meterImage = '<img src="new_html2pdf/bgForPDF.jpg" height="257" width="38" >';
																																					$filename="new_html2pdf/tess.jpg";
																																					if($blEnableHTMLGrid == true && $grid_image_path){
																																						$filename=$grid_image_path;	
																																						$meterImage="";
																																					}
																																					//$new_name=$_REQUEST["thumbimage"];
																																					
																																					$dest_file = "new_html2pdf/tess.jpg"; 
																																					$width=490;
																																					$height=357;
																																					$thumb = imagecreatetruecolor(483, 357);
																																					$source = imagecreatefromjpeg($filename);
																																					imagecopyresampled($thumb, $source, 0, 0, 0, 0, 482, 357, $width, $height);
																																					imagejpeg($thumb,$dest_file,"100");
																																					//imagejpeg($thumb,'',"100");
																																					$table_pdf.='<div class="row">';
																																					if( file_exists('new_html2pdf/tess_TimeInterval.jpg')){
																																					$table_pdf.='<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="new_html2pdf/tess_TimeInterval.jpg" width="340" height="20"></div>';
																																					}
																																					$table_pdf.='<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
																																							<img src="new_html2pdf/tess.jpg" width="309" height="257">
																																					</div>
																																					</div>';
																																					echo $table_pdf;
																																				 }
																																				 else
																																				  {
																																	?>
                                                                                                            												 <div class"col-md-12 col-sm-12 col-lg-12 col-xs-12"  style="border:none; white-space:nowrap; " >
                                                                                                                                                                <div class"col-md-12 col-sm-12 col-lg-12 col-xs-12" style="min-height:25px;" >
                                                                                                                                                                	<div class"col-md-2 col-sm-2 col-lg-2 col-xs-2" style="float:left;  ">
                                                                                                                                                                    	<a href="javascript:void(0);" onClick="calculateTime('bp_temp6');">
                                                                                                                                                                            		<i class="fa fa-forward" 
                                                                                                                                                                                    	onClick="calculateTime('bp_temp6');document.frm_local_anes_rec.hiddActiveTimeInterval.value='Yes';">
                                                                                                                                                                                   	</i>
                                                                                                                                                                          	</a>
                                                                                                                                                                  	</div> 
                                                                                                                                                                    <div id="divGridTimer" class"col-md-10 col-sm-10 col-lg-10 col-xs-10" style="white-space:nowrap; margin-left:34px; "></div>
                                                                                                                                                                    
                                                                                                                                                                    
                                                                                                                                                              	</div>
                                                                                                                                                                
                                                                                                                                                                 <div class"col-md-12 col-sm-12 col-lg-12 col-xs-12"   id="divCanvas"   style="height:370px; min-width:990px; width:100%; ">
                                                                                                                                                                 		<canvas id="cCanvas" name="cCanvas" height="1001" width="100%" class="no-copy">                    	 
                                                                                                                                                                        </canvas>   
                                                                                                                                                                	</div>
                                                                                                                                                             	</div>	
                                                                                                           	<?php
																																					}
																															}
																											?>
                                                                                                    </div>
                                                                                            	</div>
                                                                                         	</div>
                                                                                                    
                                                            				<?PHP
																		
																						}
																			?>
                                                                            			<input type="hidden" name="applet_data" id="applet_data" value="<?php echo $applet_data;?>">	
                                                            
                                                                		</Div>
                                                            	</Div>
														
                                                        </Div>
                                                  		
                                                      	<?php if($vitalSignGridStatus) { ?>
                                                        <!-- Vital Sign Grid Starts Here -->
                                                        <div class="clearfix margin_adjustment_only"></div>
                                                        <div class="clearfix margin_adjustment_only border-dashed"></div>
                                                        <Div class="row haed_p_clickable" id="" >
               
<div class="clearfix margin_adjustment_only"></div>
                                     
<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_anesth">
    <div class="scanner_win new_s">
     <h4>
        <span>Vital Signs</span>      
     </h4>
     
    </div>
</div>                                               
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                
    <div class="panel panel-default bg_panel_anesth" id="vital-grid" >
    	<div class="panel-heading haed_p_clickable" >
        	<div class="  col-md-12 col-sm-12 col-xs-12 col-lg-12 rob" >
                <div class=" row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                	<span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">Time</span>
                    <span  class="col-md-8 col-lg-8 col-sm-8 col-xs-8">B/P<br>
                    <span  class="col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">Systolic</span>
                    <span  class="col-md-6 col-lg-6 col-sm-6 col-xs-6">Diastolic</span></span>
                </div>
                <div class=" col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                		<span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">Pulse</span>
                    <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">RR</sub></span>
                    <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">Temp<sup>O</sup> C</span>
                    <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">EtCO<sub>2</sub></span>
                    <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">OSat<sub>2</sub></span>
               	</div>
           		<span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
            </div>
            
        </div>
    
        <div class="panel-body" >
            <input type="hidden" id="vitalSignGridHolder" />
            <?php
                $condArr		=	array();
                $condArr['confirmation_id']	=	$pConfId ;
                $condArr['chartName']	=	'mac_regional_anesthesia_form' ;
                
                $gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time,gridRowId','Asc');
                $gCounter	=	1;
                if(is_array($gridData) && count($gridData) > 0  )
                {
                    foreach($gridData as $gridRow)
                    {	
                        $fieldId1 = 'vitalSignGrid_'.$gCounter.'_1' ;	$fieldValue1= $objManageData->getTmFormat($gridRow->start_time);
                        $fieldId2 = 'vitalSignGrid_'.$gCounter.'_2' ;	$fieldValue2= stripslashes($gridRow->systolic);
                        $fieldId3 = 'vitalSignGrid_'.$gCounter.'_3' ;	$fieldValue3= stripslashes($gridRow->diastolic);
                        $fieldId4 = 'vitalSignGrid_'.$gCounter.'_4' ;	$fieldValue4= stripslashes($gridRow->pulse);
                        $fieldId5 = 'vitalSignGrid_'.$gCounter.'_5' ;	$fieldValue5= stripslashes($gridRow->rr);
                        $fieldId6 = 'vitalSignGrid_'.$gCounter.'_6' ;	$fieldValue6= stripslashes($gridRow->temp);
                        $fieldId7 = 'vitalSignGrid_'.$gCounter.'_7' ;	$fieldValue7= stripslashes($gridRow->etco2);
						$fieldId8 = 'vitalSignGrid_'.$gCounter.'_8' ;	$fieldValue8= stripslashes($gridRow->osat2);
            ?>		
                        <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        
                            <input type="hidden" name="vitalSignGridRecordId[]" value="<?=$gridRow->gridRowId?>"	/>
                            <div class="row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                            	<span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                	<input type="text" name="<?=$fieldId1?>" class="vitalSignGrid" id="<?=$fieldId1?>" value="<?=$fieldValue1?>" data-row-id = "<?=$gCounter?>?" data-record-id="<?=$gridRow->gridRowId?>" />
                            	</span>
                                <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                	<input type="text" name="<?=$fieldId2?>" class="vitalSignGrid" id="<?=$fieldId2?>"  value="<?=$fieldValue2?>" />
                            	</span>
                                <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                	<input type="text" name="<?=$fieldId3?>" class="vitalSignGrid" id="<?=$fieldId3?>"  value="<?=$fieldValue3?>" />
                            	</span>
                           	</div>     
                            <div class="  col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                                	<span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3" >
                                		<input type="text" name="<?=$fieldId4?>" class="vitalSignGrid" id="<?=$fieldId4?>" value="<?=$fieldValue4?>"  />
                            		</span>
                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                	<input type="text" name="<?=$fieldId5?>" class="vitalSignGrid" id="<?=$fieldId5?>"  value="<?=$fieldValue5?>" />
                                </span>
                                <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
                                    <input type="text" name="<?=$fieldId6?>" class="vitalSignGrid" id="<?=$fieldId6?>" value="<?=$fieldValue6?>"  />
                                </span>
                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                    <input type="text" name="<?=$fieldId7?>" class="vitalSignGrid" id="<?=$fieldId7?>" value="<?=$fieldValue7?>"  />
                                </span>
                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                    <input type="text" name="<?=$fieldId8?>" class="vitalSignGrid" id="<?=$fieldId8?>"  value="<?=$fieldValue8?>" />
                                </span>
                        	</div>
                        </div>
                        <div class="clearfix"></div>
            <?php
                        $gCounter++ ;
                    }
                }
            ?>
            
            <?php 
                $dataStartRow = $gCounter ;	
                for ($gRow = $dataStartRow; $gRow < ($dataStartRow+15) ; $gRow++)
                {
                        $fieldId1 = 'vitalSignGrid_'.$gRow.'_1' ;
                        $fieldId2 = 'vitalSignGrid_'.$gRow.'_2' ;
                        $fieldId3 = 'vitalSignGrid_'.$gRow.'_3' ;
                        $fieldId4 = 'vitalSignGrid_'.$gRow.'_4' ;
                        $fieldId5 = 'vitalSignGrid_'.$gRow.'_5' ;
                        $fieldId6 = 'vitalSignGrid_'.$gRow.'_6' ;
                        $fieldId7 = 'vitalSignGrid_'.$gRow.'_7' ;
						$fieldId8 = 'vitalSignGrid_'.$gRow.'_8' ;
						
            ?>
                    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    	<input type="hidden" name="vitalSignGridRecordId[]" />
                        <div class="row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                        	<span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                        		<input type="text" name="<?=$fieldId1?>" class="vitalSignGrid timeBox" id="<?=$fieldId1?>" data-row-id = "<?=$gRow?>" />
                    		</span>
                            <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                <input type="text" name="<?=$fieldId2?>" class="vitalSignGrid" id="<?=$fieldId2?>"  />
                            </span>
                            <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                <input type="text" name="<?=$fieldId3?>" class="vitalSignGrid" id="<?=$fieldId3?>"  />
                            </span>
                       	</div>     
                    	<div class="  col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                        	<span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3" >
                            	<input type="text" name="<?=$fieldId4?>" class="vitalSignGrid" id="<?=$fieldId4?>"  />
                           	</span>
                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                <input type="text" name="<?=$fieldId5?>" class="vitalSignGrid" id="<?=$fieldId5?>"  />
                            </span>
                            <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
                                <input type="text" name="<?=$fieldId6?>" class="vitalSignGrid" id="<?=$fieldId6?>"  />
                            </span>
                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                <input type="text" name="<?=$fieldId7?>" class="vitalSignGrid" id="<?=$fieldId7?>"  />
                            </span>
                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                <input type="text" name="<?=$fieldId8?>" class="vitalSignGrid" id="<?=$fieldId8?>"  />
                            </span>
                    	</div>
                    </div>
                    <div class="clearfix"></div>
            <?php		
                }
            ?>
            
        
        </div>
        
    </div>
                                                </div>
                                                        </Div>
                                                    	<!-- Vital Sign Grid Ends Here -->
                                                      <?php }  ?>
                                                      
                                                        
                                                    </div><!-- Left Div End -->
                                                    	
                                                    <div class="clearfix margin_adjustment_only visible-sm"></div>
                                                    <div class="col-md-4 col-sm-12 col-xs-4 col-lg-4">
                                                    	<div class="panel panel-default bg_panel_anesth">
                                                        	<div class="panel-heading">
                                                            	<div class="col-md-10 col-sm-8 col-xs-8 col-lg-8">
                                                                	<span class="badge"> 1 </span> &nbsp;	<label class="" for="Routine">  Routine Monitors Applied </label>
                                                                </div>
                                                                <div class="col-md-2 col-sm-4 col-xs-4 col-lg-4 text-left">
                                                                	<input <?php if($routineMonitorApplied=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_routine_id" name="chbx_routine" tabindex="7">
                                                                </div>
                                                                
                                                                
                                                            </div>
                                                        	
                                                        </div>
                                                        <!-- IV Cather -->
                                                        <div class="panel panel-default bg_panel_anesth">
                                                        	<div class="panel-heading">
                                                            	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                	<span class="badge"> 2 </span> &nbsp;	<label class="" for="Routine">  IV Catheter </label>
                                                                </div>
                                                            </div>
                                                            <?php
																	$chbxNoIVBackColor=$chngBckGroundColor;
																	if($ivCatheter || $hand_right || $hand_left || $wrist_right || $wrist_left || $arm_right || $arm_left || $anti_right || $anti_left || $ivCatheterOther) { 
																		$chbxNoIVBackColor=$whiteBckGroundColor; 
																	}
															?>
                                                        	<div class="panel-body">
                                                            	 <div class="wrap_right_inner_anesth">   
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                        <label class="" for="NoI">  No IV </label>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-left" onClick="chk_unchk_Catheter('chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','other_reg_anes_id','chbx_other11_id');">
                                                                        <span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>" onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input type="checkbox" value="Yes" <?php if($ivCatheter=='Yes') echo 'CHECKED'; ?> id="chbx_no_id" name="chbx_no"  tabindex="7"></span>
                                                                    </div>
                                                                </div>    
                                                                <div class="clearfix"></div>
                                                                <div class="wrap_right_inner_anesth">    
                                                                 	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                    	<div class="row">
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                                <label> Hand </label>
                                                                            </div> 	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_hand_right_id">
                                                                              	<span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>" onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input type="checkbox" value="Yes" id="chbx_hand_right_id" name="chbx_hand_right" <?php if($hand_right=='Yes') echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> tabindex="7"></span>

                                                                                &nbsp;Right </label>
                                                                            </div> 	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_hand_left_id">
                                                                              	<span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>"  onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input type="checkbox" value="Yes" id="chbx_hand_left_id" name="chbx_hand_left" <?php if($hand_left=='Yes') echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> tabindex="7"></span></span>
                                                                                &nbsp; Left </label>
                                                                            </div> 	
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                                <div class="wrap_right_inner_anesth">    
                                                                 	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                    	<div class="row">
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                                <label> Wrist </label>
                                                                            </div> 	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_wrist_right_id">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>"  onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');"><input type="checkbox" value="Yes" id="chbx_wrist_right_id" name="chbx_wrist_right" <?php if($wrist_right=='Yes') echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> tabindex="7"></span>
                                                                              &nbsp;Right </label>
                                                                            </div> 	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_wrist_left_id">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>" onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input  type="checkbox" value="Yes" id="chbx_wrist_left_id" name="chbx_wrist_left" <?php if($wrist_left=='Yes') echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> tabindex="7"></span>
                                                                              &nbsp;Left </label>
                                                                            </div> 	
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                                <div class="wrap_right_inner_anesth">    
                                                                 	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                    	<div class="row">
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                                <label> Arm </label>
                                                                            </div> 	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_arm_right_id">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>" onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input  type="checkbox" value="Yes" id="chbx_arm_right_id" name="chbx_arm_right" <?php if($arm_right=='Yes') echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> tabindex="7"></span>
                                                                              &nbsp;  Right </label>
                                                                            </div> 	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_arm_left_id">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>" onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input type="checkbox" value="Yes" id="chbx_arm_left_id" name="chbx_arm_left" <?php if($arm_left=='Yes') echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> tabindex="7"></span>
                                                                             &nbsp; Left </label>
                                                                            </div> 	
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                                <div class="wrap_right_inner_anesth">    
                                                                 	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                    	<div class="row">
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                                <label> Antecubital </label>
                                                                            </div> 	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_anti_right_id">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>" onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input type="checkbox" value="Yes" id="chbx_anti_right_id" name="chbx_anti_right" <?php if($anti_right=='Yes') echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> tabindex="7"></span>
                                                                              &nbsp; Right </label>
                                                                            </div> 	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_anti_left_id">
                                                                              	<span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>" onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input type="checkbox" value="Yes" id="chbx_anti_left_id" name="chbx_anti_left" <?php if($anti_left=='Yes') echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> tabindex="7"></span>
                                                                                &nbsp; Left </label>
                                                                            </div> 	
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                                <div class="wrap_right_inner_anesth">    
                                                                 	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                    	<div class="row">
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                                <label for="other_1" onClick="javascript:disp_hide_checked_row_id('chbx_other11_id','other_reg_anes_id')">
                                                                               <span class="colorChkBx" style=" <?php echo $chbxNoIVBackColor;?>" onClick="changeDiffChbxColor(10,'chbx_no_id','chbx_hand_right_id','chbx_hand_left_id','chbx_wrist_right_id','chbx_wrist_left_id','chbx_arm_right_id','chbx_arm_left_id','chbx_anti_right_id','chbx_anti_left_id','chbx_other11_id');" ><input <?php if($ivCatheterOther) echo 'CHECKED'; ?> <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?> type="checkbox" value="Yes" id="chbx_other11_id" name="chbx_other11"  tabindex="7" ></span>
                                                                               	&nbsp;Other </label>
                                                                            </div> 	
                                                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
																				<textarea name="other_reg_anes" id="other_reg_anes_id" class="form-control" onKeyUp="textAreaAdjust(this);"  tabindex="6" <?php if($ivCatheter=="Yes") { echo "disabled"; }else {  }?>><?php echo stripslashes($ivCatheterOther); ?></textarea>
                                                                            </div> 	
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                             </div>
                                                        </div>
                                                        <div class="panel panel-default bg_panel_anesth">
                                                        	<div class="panel-heading">
                                                            	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                	<span class="badge"> 3 </span> &nbsp;	<label class="" for="Routine">    Local Anesthesia </label>
                                                                </div>
                                                            </div>
                                                            <?php
																	$chbxTopBlockBackColor=$chngBckGroundColor;
																	if($topical4PercentLidocaine || $Intracameral1percentLidocaine || $Peribulbar2percentLidocaine || $Retrobulbar4percentLidocaine || $Hyalauronidase4percentLidocaine || $VanLindrHalfPercentLidocaine || $lidEpi5ug || $otherRegionalAnesthesiaWydase15u || $TopicalBlock1Block2 || $bupivacaine75 || $marcaine75) { 
																		$chbxTopBlockBackColor=$whiteBckGroundColor; 
																	}
                                                           	?>                                 
                                                        	<div class="panel-body">
                                                            
                                                                <div class="wrap_right_inner_anesth">    
                                                                 	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                    	<div class="row">
                                                                            <div class="col-md-12 col-sm-2 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_TopicalId" onClick="javascript:checkSingle('chbx_TopicalId','chbx_TopicalBlock1Block2');<?php if(($form_status=="completed" || $form_status=="not completed") && $saveByAnes=='Yes') {/*DO NOTHING*/}else {?>chkTopicalBlockFun('chbx_TopicalId');<?php } ?>">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>"  id="tropicalSpan" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id'); displayTropicalTest(this.id);" ><input <?php if($TopicalBlock1Block2=='Topical') echo 'CHECKED'; ?> type="checkbox" value="Topical" id="chbx_TopicalId" name="chbx_TopicalBlock1Block2"></span>
                                                                              &nbsp;Topical</label>
                                                                            </div> 	
                                                                            <div class="clearfix visible-md"></div>
                                                                            <div class="col-md-6 col-sm-2 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_Block1Id" onClick="javascript:checkSingle('chbx_Block1Id','chbx_TopicalBlock1Block2');<?php  if(($form_status=="completed" || $form_status=="not completed") && $saveByAnes=='Yes') {/*DO NOTHING*/}else {?>chkTopicalBlockFun('chbx_Block1Id');<?php  } ?>">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" id="block1Span"   onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id'); displayTropicalTest(this.id);" ><input <?php if($TopicalBlock1Block2=='Block1')  echo 'CHECKED'; ?> type="checkbox" value="Block1"  id="chbx_Block1Id"  name="chbx_TopicalBlock1Block2"></span>
                                                                              &nbsp;Block</label>
                                                                            </div> 	
                                                                            <div class="col-md-6 col-sm-2 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_Block2Id"  onClick="javascript:checkSingle('chbx_Block2Id','chbx_TopicalBlock1Block2');<?php  if(($form_status=="completed" || $form_status=="not completed") && $saveByAnes=='Yes') {/*DO NOTHING*/}else {?>chkTopicalBlockFun('chbx_Block2Id');<?php  } ?>">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>"  id="block2Span"   onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id'); displayTropicalTest(this.id);"><input <?php if($TopicalBlock1Block2=='Block2')  echo 'CHECKED'; ?> type="checkbox" value="Block2"  id="chbx_Block2Id"  name="chbx_TopicalBlock1Block2"></span>
                                                                              
                                                                              &nbsp;Block2</label>
                                                                            </div> 	
                                                                            <div class="clearfix visible-md"></div>                                                                             <div class="col-md-6 col-sm-2 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_NAId" onClick="javascript:checkSingle('chbx_NAId','chbx_TopicalBlock1Block2');	     <?php  if(($form_status=="completed" || $form_status=="not completed") && $saveByAnes=='Yes') {/*DO NOTHING*/}else {?>chkTopicalBlockFun('chbx_NAId'); 	<?php } ?>">
                                                                              <span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" id="naSpan"       onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id'); displayTropicalTest(this.id);" ><input <?php if($TopicalBlock1Block2=='NA')  	  echo 'CHECKED'; ?> type="checkbox" value="NA"      id="chbx_NAId" 		name="chbx_TopicalBlock1Block2"></span>
                                                                              &nbsp;N/A </label>
                                                                            </div> 	
                                                                            <div class="col-md-6 col-sm-2 col-xs-4 col-lg-4">
                                                                              <label class="" for="chbx_ReblockId"> 
                                                                              	<span class="colorChkBx" id="reblockSpan" style=" <?php echo $whiteBckGroundColor;?>"  >
                                                                                                                <input <?php if($Reblock=='Yes'){	echo 'CHECKED';} ?>  type="checkbox" value="Yes" id="chbx_ReblockId" name="chbx_Reblock" <?php echo ($blEnableHTMLGrid == true) ? "onClick=\"showHideReblockLine(this, event);\"": "onClick=\"showHideEkgLineFn(this);\""; ?>>
                                                                                                            </span>
                                                                              &nbsp;ReBlock</label>
                                                                            </div> 	
                                                                            <div class="clearfix visible-md"></div>                                                                            
                                                                            
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                                <?php
																		$Block1Block2SpanDisplay='none';
																		if($TopicalBlock1Block2=='Block1' || $TopicalBlock1Block2=='Block2'){
																			$Block1Block2SpanDisplay ='block';
																		}	
																?>
                                                                <div class="clearfix margin_adjustment_only border-dashed"></div>
                                                                <div class="clearfix margin_adjustment_only"></div>
                                                                 <div class="wrap_right_inner_anesth" id="tropicalTestTr1" style=" background-color:<?php echo $bglight_blue_local_anes; ?>;display:<?php echo $Block1Block2SpanDisplay;?>; ">    
                                                                     <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                           <label for="Block1Block2Aspiration"> <input type="checkbox" value="Yes" <?php if($Block1Block2Aspiration=='Yes') echo 'CHECKED'; ?>  id="Block1Block2Aspiration" name="Block1Block2Aspiration" /> Aspiration	 </label>    
                                                                           &nbsp;
                                                                           <label for="Block1Block2Full"><input type="checkbox" value="Yes" <?php if($Block1Block2Full=='Yes') echo 'CHECKED'; ?>  id="Block1Block2Full" name="Block1Block2Full" /> Full EOM</label>                                                                    </div>
                                                                     
                                                                 </div>
                                                                 <div class="wrap_right_inner_anesth" id="tropicalTestTr2" style="display:<?php echo $Block1Block2SpanDisplay;?>; ">    
                                                                     <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                           <label for="Block1Block2BeforeInjection"> <input type="checkbox" value="Yes" <?php if($Block1Block2BeforeInjection=='Yes') echo 'CHECKED'; ?>  id="Block1Block2BeforeInjection" name="Block1Block2BeforeInjection" > Before Injection	 </label>    
                                                                    </div>
                                                                    
                                                                    
                                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                           <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                                                                           	<label for="Block1Block2Comment">Comment</label>
                                                                           </div>
                                                                           
                                                                           <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
                                                                           			<textarea name="Block1Block2Comment" id="Block1Block2Comment" onKeyUp="textAreaAdjust(this);" class="form-control" rows="1" cols="23" tabindex="6"><?php echo stripslashes($Block1Block2Comment); ?></textarea>
                                                                           </div>
                                                                           
                                                                    </div>
                                                                     
                                                                 </div>
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<label for="topical4PercentLidocaine_id">
                                                                     	 	<span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');">
                                                                            <input <?php if($topical4PercentLidocaine=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="topical4PercentLidocaine_id" name="chbx_topical4PercentLidocaine" ></span>&nbsp;4% lidocaine
                                                                     	</label>
                                                                     </div>
                                                                     <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label> <span class="colorChkBx"  onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');" style=" <?php echo $chbxTopBlockBackColor;?>" ><input type="checkbox" value="Yes" <?php if($Intracameral1percentLidocaine=='Yes') echo 'CHECKED'; ?> id="Intracameral1percentLidocaine_id" name="chbx_Intracameral1percentLidocaine" ></span>&nbsp;1% lidocaine MPF</label>   
                                                                            </div>
                                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                           		<div class="row">
                                                                                	<div class="col-md-8 col-sm-6 col-xs-6 col-lg-6">
                                                                                    	   <label>  Intracameral </label>    
                                                                                    </div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-4 padding_0">
                                                                                    	<select class="selectpicker form-control" title="select" name="Intracameral">
                                                                                        		<option value="" selected>&nbsp;</option>
                                                                                                <?php		for($i=0.5;$i<=10;$i+=0.5) 	{	?>
                                                                                                <option value="<?php echo $i;?>" <?php if($Intracameral==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                                                                <?php		}	?> 
                                                                                  		</select>
                                                                                     </div>
                                                                                     <div class="col-md-4 col-sm-6 col-xs-6 col-lg-2 padding_0">
                                                                                    	<small class="text-center">ml</small>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                    </div>
                                                                 
                                                                 </div>
                                                                <div class="clearfix margin_adjustment_only border-dashed visible-md"></div> 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label for="chbx_Peribulbar2percentLidocaine_id">
                                                                                  	<span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');" ><input type="checkbox" value="Yes" <?php if($Peribulbar2percentLidocaine=='Yes') echo 'CHECKED'; ?> id="chbx_Peribulbar2percentLidocaine_id" name="chbx_Peribulbar2percentLidocaine" ></span>&nbsp;2% lidocaine</label>   
                                                                            </div>
                                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                           		<div class="row">
                                                                                	<div class="col-md-8 col-sm-6 col-xs-6 col-lg-6">
                                                                                    	   <label>  Peribulbar </label>    
                                                                                    </div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-4 padding_0">
                                                                                    	<select class="selectpicker form-control" title="select" name="Peribulbar">
                                                                                        	<option value="" selected>&nbsp;</option>
																							<?php
                                                                                            for($i=1;$i<=20;$i+=0.5) {
                                                                                            ?>
                                                                                                <option value="<?php echo $i;?>" <?php if($Peribulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                     	</select>
                                                                                        
                                                                                        
                                                                                    </div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-2 padding_0">
                                                                                    	<small class="text-center">mls</small>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        	
                                                                        	
                                                                     
                                                                        </div>
                                                                    </div>
                                                                 </div>
                                                                 
                                                                
                                                                <div class="clearfix margin_adjustment_only border-dashed visible-md"></div>
                                                                
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label> <span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" ><input type="checkbox" value="Yes" <?php if($Retrobulbar4percentLidocaine=='Yes') echo 'CHECKED'; ?>  id="chbx_Retrobulbar4percentLidocaine_id" name="chbx_Retrobulbar4percentLidocaine" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');"></span>&nbsp;3% lidocaine</label>   
                                                                            </div>
                                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                           		<div class="row">
                                                                                	<div class="col-md-8 col-sm-6 col-xs-6 col-lg-6">
                                                                                    	   <label>Retrobulbar</label>    
                                                                                           <small class="text-left" style="font-size:9px; margin-top:-5px;"> Done By Surgeon </small>
                                                                                    </div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-4 padding_0">
                                                                                    	<select class="selectpicker form-control" title="select"  name="Retrobulbar">
                                                                                        	<option value="" selected>&nbsp;</option>
                                                                                        	 	<?php
                                                                                                        for($i=1;$i<=20;$i+=0.5) {
                                                                                                        ?>
                                                                                                            <option value="<?php echo $i;?>" <?php if($Retrobulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                                                                        <?php
                                                                                                        }
                                                                                             	?>
                                                                                       	</select>
                                                                                           
                                                                                        
                                                                                    </div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-2 padding_0">
                                                                                    <small class="text-center">mls</small>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>
                                                                 
                                                                  <div class="clearfix margin_adjustment_only border-dashed visible-md"></div> 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label for="chbx_Hyalauronidase4percentLidocaine_id">
                                                                                  	<span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');" ><input type="checkbox" value="Yes" <?php if($Hyalauronidase4percentLidocaine=='Yes') echo 'CHECKED'; ?>  id="chbx_Hyalauronidase4percentLidocaine_id" name="chbx_Hyalauronidase4percentLidocaine" ></span>&nbsp;4% lidocaine</label>   
                                                                            </div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">&nbsp;</div>
                                                                       	</div>
                                                                   	</div>
                                                                 </div>
                                                                 
                                                                 
                                                                 <div class="clearfix margin_adjustment_only border-dashed visible-md"></div> 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label for="chbx_VanLindrHalfPercentLidocaine_id">
                                                                                  <span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>"  onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');"><input type="checkbox" value="Yes" <?php if($VanLindrHalfPercentLidocaine=='Yes') echo 'CHECKED'; ?>   id="chbx_VanLindrHalfPercentLidocaine_id" name="chbx_VanLindrHalfPercentLidocaine" ></span>&nbsp;0.5% Bupivacaine</label>   
                                                                            </div>
                                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                           		<div class="row">
                                                                                	<div class="col-md-8 col-sm-6 col-xs-6 col-lg-6">
                                                                                    	   <label>Van Lindt </label>    
                                                                                          
                                                                                    </div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-4 padding_0">
                                                                                    	<select class="selectpicker form-control" title="Select" name="VanLindr">
                                                                                        	<option value="" selected>&nbsp;</option>
																							<?php
                                                                                            for($i=1;$i<=20;$i+=0.5) {
                                                                                            ?>
                                                                                                <option value="<?php echo $i;?>" <?php if($VanLindr==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                        
                                                                                       	</select>
                                                                                         
                                                                                       
                                                                                    </div>
                                                                                     <div class="col-md-4 col-sm-6 col-xs-6 col-lg-2 padding_0">
                                                                                     	<small class="text-center">mls</small>
                                                                                     </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>
                                                                 
																																 <?php if($version_num > 1) { ?>
                                                                 <!-- Start 0.75% Bupivacaine -->
                                                                 <div class="clearfix margin_adjustment_only border-dashed visible-md"></div> 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label for="chbx_bupivacaine75_id">
                                                                                  	<span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');" ><input type="checkbox" value="Yes" <?php if($bupivacaine75 == 'Yes') echo 'CHECKED'; ?>  id="chbx_bupivacaine75_id" name="bupivacaine75" ></span>&nbsp;0.75% Bupivacaine</label>   
                                                                            </div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">&nbsp;</div>
                                                                       	</div>
                                                                   	</div>
                                                                 </div>
                                                                 <!-- End 0.75% Bupivacaine -->
                                                                 
                                                                 <!-- Start  0.75% Marcaine -->
                                                                 <div class="clearfix margin_adjustment_only border-dashed visible-md"></div> 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label for="chbx_marcaine75_id">
                                                                                  	<span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');" ><input type="checkbox" value="Yes" <?php if($marcaine75 == 'Yes') echo 'CHECKED'; ?>  id="chbx_marcaine75_id" name="marcaine75" ></span>&nbsp;0.75% Marcaine</label>   
                                                                            </div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">&nbsp;</div>
                                                                       	</div>
                                                                   	</div>
                                                                 </div>
                                                                 <!-- End  0.75% Marcaine -->
                                                                 <?php } ?>
                                                                 
                                                                 <div class="clearfix margin_adjustment_only border-dashed visible-md"></div> 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label><span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');" ><input type="checkbox" value="Yes" <?php if($lidEpi5ug=='Yes') echo 'CHECKED'; ?> id="chbx_lidEpi5ug_id" name="chbx_lidEpi5ug" ></span>&nbsp;Epi 5 ug/ml</label> 
                                                                            </div>
                                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                           			<div class="col-md-8 col-sm-6 col-xs-6 col-lg-4 ">
                                                                                    <label>
                                                                                    	   <input  type="text" name="lidTxt" id="txt_field01" class="form-control" tabindex="1" value="<?php echo $lidTxt;?>" />
                                                                                    </label>
                                                                                      
                                                                                     </div>
                                                                                   <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 padding_0">lid</div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-4 padding_0">
                                                                                    	<select class="selectpicker form-control" title="Select" name="lid">
                                                                                        	<option value="" selected>&nbsp;</option>
																							<?php
                                                                                            for($i=1;$i<=20;$i+=0.5) {
                                                                                            ?>
                                                                                                <option value="<?php echo $i;?>" <?php if($lid==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                      	</select>


                                                                                    </div>
                                                                                    <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2"><small class="text-center">mls</small></div>
                                                                               
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>
                                                                 <div class="clearfix margin_adjustment_only border-dashed visible-md"></div> 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                            	  <label for="chbx_otherRegionalAnesthesiaWydase15u_id">
                                                                                  	<span class="colorChkBx" style=" <?php echo $chbxTopBlockBackColor;?>" onClick="changeDiffChbxColor(14,'chbx_TopicalId','chbx_Block1Id','chbx_Block2Id','chbx_NAId','topical4PercentLidocaine_id','chbx_bupivacaine75_id','chbx_marcaine75_id','Intracameral1percentLidocaine_id','chbx_Peribulbar2percentLidocaine_id','chbx_Retrobulbar4percentLidocaine_id','chbx_Hyalauronidase4percentLidocaine_id','chbx_VanLindrHalfPercentLidocaine_id','chbx_lidEpi5ug_id','chbx_otherRegionalAnesthesiaWydase15u_id');" ><input type="checkbox" value="Yes" <?php if($otherRegionalAnesthesiaWydase15u=='Yes') echo 'CHECKED'; ?> id="chbx_otherRegionalAnesthesiaWydase15u_id" name="chbx_otherRegionalAnesthesiaWydase15u" ></span>&nbsp;Wydase 15 u/ml </label>   
                                                                            </div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                           		<div class="row">
                                                                                	<div class="col-md-8 col-sm-6 col-xs-6 col-lg-4 padding_0">
 																						  <input type="text" name="otherRegionalAnesthesiaTxt1" value="<?php echo stripslashes($otherRegionalAnesthesiaTxt1);?>" id="otherRegionalAnesthesiaTxt1" class="form-control" tabindex="1" />
                                                                                          
                                                                                          
                                                                                    </div>
                                                                                    <div class="col-md-8 col-sm-6 col-xs-6 col-lg-2 padding_0"><small class="text-center">Other</small></div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-4 padding_0">
                                                                                    	<select class="selectpicker form-control" title="Select" name="otherRegionalAnesthesiaDrop">
                                                                                        	<option value="" selected>&nbsp;</option>
                                                                                           	<?php	
                                                                                           	for($i=1;$i<=20;$i+=0.5) {
                                                                                            ?>
                                                                                                <option value="<?php echo $i;?>" <?php if($otherRegionalAnesthesiaDrop==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                                        
                                                                                       	</select>
                                                                                       
                                                                                    </div>
                                                                                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-2 padding_0"><small class="text-center">mls</small></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>
                                                                <div class="clearfix margin_adjustment_only border-dashed visible-md"></div> 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-2 text-center">
                                                                            	  <label>Other</label>   
                                                                            </div>
                                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-10">
                                                                           		<input type="text" name="otherRegionalAnesthesiaTxt2" value="<?php echo stripslashes($otherRegionalAnesthesiaTxt2);?>"  class="form-control"  tabindex="1" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>
                                                                 
                                                            </div>
                                                        </div>
                                                        <div class="panel panel-default bg_panel_anesth">
                                                        	<div class="panel-heading">
                                                            	<div class="col-md-10 col-sm-8 col-xs-8 col-lg-8">
                                                                	<span class="badge"> 4 </span> &nbsp;	<label class="" for="">  Ocular Pressure </label>
                                                                </div>
                                                                <?php
																	$ocularPressureBackColor=$chngBckGroundColor;
																	if($ocular_pressure_na || $none || $digital || $honanballon || $honanBallonAnother) { 
																		$ocularPressureBackColor=$whiteBckGroundColor; 
																	}
																?>
                                                                <div class="col-md-2 col-sm-4 col-xs-4 col-lg-4 text-left">
                                                                	<label for="ocular_pressure_na_id"> <span class="colorChkBx" style=" <?php echo $ocularPressureBackColor;?>" onClick="changeDiffChbxColor(5,'ocular_pressure_na_id','chbx_none_id','chbx_digi_id','honanBallon','honanBallonAnother');" ><input <?php if($ocular_pressure_na=='Yes') echo 'CHECKED'; ?>  type="checkbox" value="Yes" id="ocular_pressure_na_id" name="chbx_ocular_pressure_na" tabindex="7"></span>&nbsp; N/A </label>   
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                        	<div class="panel-body">
                                                            	
                                                                <div class="wrap_right_inner_anesth">   
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                        <label for="chbx_none_id" class="">  None </label>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-left">
                                                                        <span class="colorChkBx" style=" <?php echo $ocularPressureBackColor;?>"  onClick="changeDiffChbxColor(5,'ocular_pressure_na_id','chbx_none_id','chbx_digi_id','honanBallon','honanBallonAnother');"><input <?php if($none=='Yes') echo 'CHECKED'; ?>  type="checkbox" value="Yes" id="chbx_none_id" name="chbx_none" tabindex="7"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="wrap_right_inner_anesth">   
                                                                    <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                        <label for="chbx_digi_id" class="">  Digital </label>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-left">
                                                                    	<span class="colorChkBx" style=" <?php echo $ocularPressureBackColor;?>" onClick="changeDiffChbxColor(5,'ocular_pressure_na_id','chbx_none_id','chbx_digi_id','honanBallon','honanBallonAnother');" ><input type="checkbox" <?php if($digital=='Yes') echo 'CHECKED'; ?> value="Yes" id="chbx_digi_id" name="chbx_digi" tabindex="7"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="wrap_right_inner_anesth">   
                                                                    <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                        <label for="Digital" class="">  Honan Balloon</label>
                                                                    </div>
                                                                    <div class="clearfix margin_small_only visible-md"></div>
                                                                    <div class="col-md-12 col-sm-6 col-xs-6 col-lg-6">
                                                                        <div class="row">
                                                                        	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-4 padding_0"> 
                                                                            <select class="selectpicker form-control" title="Select"  onChange="changeDiffChbxColor(5,'ocular_pressure_na_id','chbx_none_id','chbx_digi_id','honanBallon','honanBallonAnother');" style=" width:45; size:20;  border:1px;<?php echo $ocularPressureBackColor;?>" name="honanBallon" id="honanBallon">
                                                                            	<option value="" selected>&nbsp;</option>
                                                                                                        <?php
                                                                                                        for($i=10;$i<=50;$i+=10) {
                                                                                                        ?>
                                                                                                            <option value="<?php echo $i;?>" <?php if($honanballon==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                                                                        <?php
                                                                                                        }
                                                                                                        ?>                                                                             
                                                                            </select>
                                                                            </div>
                                                                            <div class="col-md-2 col-sm-2 col-xs-2 col-lg-3 padding_0"> 
                                                                            &nbsp;mm
                                                                            </div>
                                                                          	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-3 padding_0">		
                                                                            <select class="selectpicker form-control" title="Select" onChange="changeDiffChbxColor(5,'ocular_pressure_na_id','chbx_none_id','chbx_digi_id','honanBallon','honanBallonAnother');" style=" width:45; size:20;  border:1px;<?php echo $ocularPressureBackColor;?>" name="honanBallonAnother" id="honanBallonAnother">
                                                                            	<option value="" selected> &nbsp;</option>
                                                                                                        <?php
                                                                                                        for($i=1;$i<=10;$i++) {
                                                                                                        ?>
                                                                                                            <option value="<?php echo $i;?>" <?php if($honanBallonAnother==$i) echo 'selected'; ?>><?php echo $i;?></option>
                                                                                                        <?php
                                                                                                        }
                                                                                                        ?>                                                                                
                                                                            </select>
                                                                            </div>
                                                                             <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 padding_0"> 
                                                                           	 &nbsp;mins
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-5">
                                                                            	  <label> Comment </label>   
                                                                            </div>
                                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-7">
                                                                           		<textarea name="ansComment" id="ansComment" onKeyUp="textAreaAdjust(this);" class="form-control" tabindex="6"><?php echo stripslashes($ansComment); ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>
                                                                 
                                                                 <?php
																		//CODE RELATED TO ANES 2 SIGNATURE ON FILE
																			$anesthesia2SignOnFileStatus = "Yes";
																			$TDanesthesia2NameIdDisplay = "block";
																			$TDanesthesia2SignatureIdDisplay = "none";
																			$Anesthesia2Name = $loggedInUserName;
																			$Anesthesia2SubType = $logInUserSubType;
																			$Anesthesia2PreFix = 'Dr.';
																			$signAnesthesia2DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
																			if($signAnesthesia2Id<>0 && $signAnesthesia2Id<>"") {
																				$Anesthesia2Name = $signAnesthesia2LastName.", ".$signAnesthesia2FirstName." ".$signAnesthesia2MiddleName;
																				$anesthesia2SignOnFileStatus = $signAnesthesia2Status;	
																				$TDanesthesia2NameIdDisplay = "none";
																				$TDanesthesia2SignatureIdDisplay = "block";
																				$signAnesthesia2DateTimeFormatNew = $objManageData->getFullDtTmFormat($signAnesthesia2DateTime);
																				$Anesthesia2SubType = getUserSubTypeFun($signAnesthesia2Id); //FROM common/commonFunctions.php
																			}
																			if($Anesthesia2SubType=='CRNA') {
																				$Anesthesia2PreFix = '';
																			}
																			//CODE TO REMOVE ANES 2 SIGNATURE
																				if($_SESSION["loginUserId"]==$signAnesthesia2Id) {
																					$callJavaFunIntraOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia2NameId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia2','delSign');";
																				}
																				else {
																					$callJavaFunIntraOpDel = "alert('Only $Anesthesia2PreFix ".addslashes($Anesthesia2Name)." can remove this signature');";
																				}
																			//END CODE TO REMOVE ANES 2 SIGNATURE
																			
																		//END CODE RELATED TO ANES 2 SIGNATURE ON FILE	

																	?>
                                                                                            
                                                                 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                    <div class="inner_safety_wrap" id="TDanesthesia2NameId" style="display:<?php echo $TDanesthesia2NameIdDisplay;?>;">
                                                                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signAnesthesiaIdBackColor?>;" onClick="javascript:<?php echo $callJavaFunIntraOp;?>"> Anesthesia Provider Signature </a>
                                                                    </div>
                                                                    <div class="inner_safety_wrap collapse" id="TDanesthesia2SignatureId" style="display:<?php echo $TDanesthesia2SignatureIdDisplay;?>;">
                                                                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunIntraOpDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$Anesthesia2PreFix." ".$Anesthesia2Name; ?>  </a></span>	     
                                                                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $anesthesia2SignOnFileStatus;?></span>
                                                                        <span class="rob full_width"> <b> Signature Date</b> <?php echo $signAnesthesia2DateTimeFormatNew;?></span>
                                                                    </div>
                                                                 </div>
                                                                 
                                                                 <div class="wrap_right_inner_anesth">    
                                                                	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                     	<div class="row">
                                                                        	<div class="col-md-12 col-sm-6 col-xs-6 col-lg-5">
                                                                            	  <label> Anesthesia Provider </label>   
                                                                            </div>
                                                                            <div class="clearfix margin_small_only visible-md"></div>
                                                                            <div class="col-md-12 col-sm-6 col-xs-6 col-lg-7">
                                                                                <select class="selectpicker form-control" title="Select" name="relivedIntraNurseIdList">
                                                                                	<option value="" selected>&nbsp;</option>	
                                                                                                            <?php
                                                                                                            $relivedIntraNurseQry = "select * from users where user_type='Anesthesiologist' ORDER BY lname";
                                                                                                            $relivedIntraNurseRes = imw_query($relivedIntraNurseQry) or die(imw_error());
                                                                                                            while($relivedIntraNurseRow=imw_fetch_array($relivedIntraNurseRes)) {
                                                                                                                $relivedSelectIntraNurseID = $relivedIntraNurseRow["usersId"];
                                                                                                                $relivedIntraNurseName = $relivedIntraNurseRow["lname"].", ".$relivedIntraNurseRow["fname"]." ".$relivedIntraNurseRow["mname"];
                                                                                                                $sel="";
                                                                                                                if($relivedIntraNurseId==$relivedSelectIntraNurseID) {
                                                                                                                    $sel = "selected";
                                                                                                                } 
                                                                                                                else {
                                                                                                                    $sel = "";
                                                                                                                }
                                                                                                                if($relivedIntraNurseRow["deleteStatus"]<>'Yes' || $relivedIntraNurseId==$relivedSelectIntraNurseID) {						
                                                                                                            ?>	
                                                                                                                    <option value="<?php echo $relivedSelectIntraNurseID;?>" <?php echo $sel;?>><?php echo $relivedIntraNurseName;?></option>
                                                                                                            <?php
                                                                                                                }
                                                                                                            }
                                                                                                            ?>
                                                                                
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix margin_adjustment_only"></div>	
                                              </div>  
                                              
                                              <div class="clearfix margin_adjustment_only"></div>
                                                 
                                                 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_anesth">
                                                    <div class="scanner_win new_s">
                                                     <h4>
                                                        <span>Post Operative</span>      
                                                     </h4>
                                                    </div>
                                                </div>
                                              <?php
                                $anesPlumDisBackColor=$eve_bg_color=$chngBckGroundColor;
                                if($anyKnowAnestheticComplication || $stableCardiPlumFunction2 || $satisfactoryCondition4Discharge) { 
                                    $anesPlumDisBackColor=$whiteBckGroundColor; 
                                }
								if(trim(!$evaluation)){ $eve_bg_color=$chngBckGroundColor;}else {$eve_bg_color=$whiteBckGroundColor;}
								
								$PostOperativeClass='colorChkBx';
								//check dow we need to make these fields compulsory
								if($_SESSION['loginUserType']=='Anesthesiologist')
								{
									$anesPlumDisBackColor=$whiteBckGroundColor;
									$eve_bg_color=$whiteBckGroundColor;
									//$signAnesthesiaIdBackColor=$whiteBckGroundColor;
								}
								 
                                ?>	
                                              
                                         	<!--- - -- --   Sign -Out Ends Here -->
                                            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                                         	   	<div class="row">
                                                	<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                		<div class="wrap_right_inner_anesth">   
                                                            <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                <label for="chbx_anes_id">No known anesthetic complication </label>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-left">
                                                                <span class="colorChkBx" style=" <?php echo $anesPlumDisBackColor;?>" <?php if($_SESSION['loginUserType']!='Anesthesiologist'){?>onClick="changeDiffChbxColor(3,'chbx_anes_id','chbx_pulm_id','chbx_dis_id');"<?php }?> ><input <?php if($anyKnowAnestheticComplication=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_anes_id" name="chbx_anes"  tabindex="7" ></span>
                                                            </div>
                                                        </div>
                                                        <div class="wrap_right_inner_anesth">   
                                                            <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                <label for="chbx_pulm_id">Stable cardiovascular and pulmonary function </label>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-left">
                                                                <span class="colorChkBx" style=" <?php echo $anesPlumDisBackColor;?>" <?php if($_SESSION['loginUserType']!='Anesthesiologist'){?>onClick="changeDiffChbxColor(3,'chbx_anes_id','chbx_pulm_id','chbx_dis_id');"<?php }?>><input <?php if($stableCardiPlumFunction2=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_pulm_id" name="chbx_pulm" tabindex="7"></span>
                                                            </div>
                                                        </div>
                                                        <div class="wrap_right_inner_anesth">   
                                                            <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8">
                                                                <label class="" for="chbx_dis_id">   	 	Satisfactory condition for discharge </label>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 text-left">
                                                                <span class="colorChkBx" style=" <?php echo $anesPlumDisBackColor;?>" <?php if($_SESSION['loginUserType']!='Anesthesiologist'){?>onClick="changeDiffChbxColor(3,'chbx_anes_id','chbx_pulm_id','chbx_dis_id');"<?php }?>><input <?php if($satisfactoryCondition4Discharge=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_dis_id" name="chbx_dis" tabindex="7"></span>
                                                            </div>
                                                        </div>		    
                                                    </div>
                                                    <div class="clearfix margin_adjustment_only visible-sm border-dashed"></div>
                                                    <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                			<div class="inner_safety_wrap">    
                                                                 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
   <?php
	$defaultPostOpEval	=	'';
	if( $evaluation == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
	{
		$defaultPostOpEval	= $objManageData->getDefault('postopevaluation','name');
		$evaluation		= $defaultPostOpEval;
		
	}   
   
   ?>                                                                 <div class="row">
                                                                        <div class="col-md-2 col-sm-3 col-xs-3 col-lg-2">
                                                                              <a data-placement="top" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " onClick="return showPostopEvaluationFn('local_anes_revaluation1_id', '', 'no', $(this).offset().left, (parseInt( $(this).offset().top - 180 ) < $(document).scrollTop() ? parseInt( $(this).offset().top + 30 ) : parseInt( $(this).offset().top - 180 )  ) ),document.getElementById('selected_frame_name_id').value='';">
                                                                              	<span class="fa fa-caret-right"></span> Evaluation  </a>   
                                                                        </div>
                                                                        
                                                                        <div class="clearfix margin_adjustment_only visible-md"></div>
                                                                        <div class="col-md-10 col-sm-9 col-xs-9 col-lg-10">
                                                                             <textarea name="evaluation" id="local_anes_revaluation1_id" <?php if($_SESSION['loginUserType']!='Anesthesiologist'){echo $keyPressInput.$focusInput.$keyUpInput;}?> 
                                                                             				class="form-control" style="<?php echo $eve_bg_color;?> " rows="4" cols="100" tabindex="6"><?php echo stripslashes($evaluation);?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                             </div>
                                                             <div class="clearfix margin_adjustment_only visible-sm border-dashed"></div>
                                                             <div class="inner_safety_wrap">    
                                                                 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                    <div class="row">
                                                                        <div class="col-md-2 col-sm-3 col-xs-3 col-lg-2">
                                                                             <label> Remarks </label>
                                                                        </div>
                                                                        
                                                                        <div class="clearfix margin_adjustment_only visible-md"></div>
                                                                        <div class="col-md-10 col-sm-9 col-xs-9 col-lg-10">
                                                                             <textarea name="txtarea_remarks" id="txtarea_remarks" class="form-control" onKeyUp="textAreaAdjust(this);" rows="4" cols="100" tabindex="6"><?php echo stripslashes($remarks); ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                             </div>	    
                                                    </div> <!--- Col-6 Ends  -->
                                                </div> 
                                         	</div>	
                                            
                                            
                                                                                       
<!-- Add Pre Define Questions Here -->
<?php if($version_num > 3 ) { ?>
<!-- Display only if form version is greater than 3 -->
<?php
	
		$query = "Select id as pat_ques_id,question, f_type, d_type, list_options as options, answer From patient_mac_regional_questions Where confirmation_id = ".(int)$pConfId." Order By id Asc";
		$sql = imw_query($query) or die(imw_error());
		$cnt = imw_num_rows($sql);
	
		if( $cnt <= 0 && $form_status <> 'completed' && $form_status <> 'not completed' ) {
			$query = "Select id,question, f_type, d_type, options From predefine_mac_regional_questions Where deleted = 0 Order By sort_id Asc";
			$sql = imw_query($query) or die(imw_error());
			$cnt = imw_num_rows($sql);
		}
	
		if( $cnt > 0 ) {
?>

<div class="clearfix margin_adjustment_only"></div>
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_anesth">
		<div class="scanner_win new_s">
			<h4><span>Additional Questions</span></h4>
		</div>
	</div>
	
	<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
	
	<?php
		
		$looper = 0;
		$k = 0;
		
			//$prev_f_type = '';
			while( $row = imw_fetch_assoc($sql) ) { $looper++; $k++;
				/*if( !$prev_f_type ) $prev_f_type = $row['f_type'];
				
				if($prev_f_type && $prev_f_type <> $row['f_type'] ) {
					$k = 1;
					echo '<div class="clearfix border-dashed margin_adjustment_only">&nbsp;</div>';
				} */
				
				echo '<div class="col-xs-12 col-sm-12 col-md-6" style="margin-top:5px;">';
				echo $objLocalAnesData->mac_ques_html($row,$looper);
				echo '</div>';
				
				
				if( $k%2 == 0 ) {
					//$k = 0;
					echo '<div class="clearfix hidden-sm hidden-xs"></div>';
				} 
				
				//echo 'FR: '.$prev_f_type .'--'.$row['f_type'] . '==' .$k.'';
																							 
				//$prev_f_type = $row['f_type'];
			}	
		
?>
		
	</div>
	
	<div class="clearfix margin_adjustment_only">&nbsp;</div>
<?php } ?>
<?php } ?>
<!-- End Add Pre Define Questions Here -->
                                            
                                            <?php
                        //CODE RELATED TO ANES 3 SIGNATURE ON FILE
                            $anesthesia3SignOnFileStatus = "Yes";
                            $TDanesthesia3NameIdDisplay = "block";
                            $TDanesthesia3SignatureIdDisplay = "none";
                            $Anesthesia3Name = $loggedInUserName;
                            $Anesthesia3SubType = $logInUserSubType;
                            $Anesthesia3PreFix = 'Dr.';
                            $signAnesthesia3DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
                            if($signAnesthesia3Id<>0 && $signAnesthesia3Id<>"") {
                                $Anesthesia3Name = $signAnesthesia3LastName.", ".$signAnesthesia3FirstName." ".$signAnesthesia3MiddleName;
                                $anesthesia3SignOnFileStatus = $signAnesthesia3Status;	
                                $TDanesthesia3NameIdDisplay = "none";
                                $TDanesthesia3SignatureIdDisplay = "block";
								$signAnesthesia3DateTimeFormatNew = $objManageData->getFullDtTmFormat($signAnesthesia3DateTime);
                                $Anesthesia3SubType = getUserSubTypeFun($signAnesthesia3Id); //FROM common/commonFunctions.php
                            }
							
                            if($Anesthesia3SubType=='CRNA') {
                                $Anesthesia3PreFix = '';
                            }
                        
                            //CODE TO REMOVE ANES 3 SIGNATURE
                                if($_SESSION["loginUserId"]==$signAnesthesia3Id) {
                                    $callJavaFunPostOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia3NameId'; return displaySignature('TDanesthesia3NameId','TDanesthesia3SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia3','delSign');";
                                }else {
                                    $callJavaFunPostOpDel = "alert('Only $Anesthesia3PreFix ".addslashes($Anesthesia3Name)." can remove this signature');";
                                }
                            //END CODE TO REMOVE ANES 3 SIGNATURE	
                            
                        //END CODE RELATED TO ANES 3 SIGNATURE ON FILE
                        
                        //CODE RELATED TO SURGEON SIGNATURE ON FILE
                            if($loggedInUserType<>"Surgeon") {
                                $loginUserName = $_SESSION['loginUserName'];
                                $callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
                            }else {
                                $loginUserId = $_SESSION["loginUserId"];
                                $callJavaFunSurgeon = "document.frm_local_anes_rec.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Surgeon1');";
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
                            //CODE TO REMOVE SURGEON SIGNATURE	
                                if($_SESSION["loginUserId"]==$signSurgeon1Id) {
                                    $callJavaFunSurgeonDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Surgeon1','delSign');";
                                }else {
                                    $callJavaFunSurgeonDel = "alert('Only Dr. ".addslashes($Surgeon1Name)." can remove this signature');";
                                }
                            //END CODE TO REMOVE SURGEON SIGNATURE															
                        
                        //END CODE RELATED TO SURGEON SIGNATURE ON FILE
                        
                        
    
    
                    ?>
                    
                                            <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
                                                <div class="clearfix border-dashed margin_adjustment_only"></div>
                                            </Div>	
											<div class="clearfix margin_adjustment_only"></div>
                                           	<div class=" col-lg-4 col-md-4 col-sm-6 col-xs-12"  >
                                                <div class="inner_safety_wrap" id="TDanesthesia3NameId" style="display:<?php echo $TDanesthesia3NameIdDisplay;?>;">
                                                    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signAnesthesiaIdBackColor?>;" onClick="javascript:<?php echo $callJavaFunPostOp;?>"> Anesthesia Provider Signature </a>
                                                </div>
                                                <div class="inner_safety_wrap collapse" id="TDanesthesia3SignatureId" style="display:<?php echo $TDanesthesia3SignatureIdDisplay;?>;">
                                                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunPostOpDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$Anesthesia3PreFix." ".$Anesthesia3Name; ?>  </a></span>	     
                                                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $anesthesia3SignOnFileStatus;?></span>
                                                    <span class="rob full_width"> <b> Signature Date</b> <?php echo $signAnesthesia3DateTimeFormatNew;?></span>
                                                </div>
                                           </div>
                                           <div class="col-md-4 col-lg-4 col-xs-12 hidden-sm"></div>
                                           
                                           <?php
														$userTypeLabel="Anesthesia Provider";
														$userTypeQry="Anesthesiologist"; 
														if($relivedPostNurseId){
															 $relivedPostQry = "select user_type from users where usersId='".$relivedPostNurseId."' ORDER BY lname";
															 $relivedPostRes = imw_query($relivedPostQry) or die(imw_error());
															 $userTypeRow 	 = imw_fetch_assoc($relivedPostRes);
															 $userTypeChk	 = $userTypeRow['user_type'];
														}
														if($userTypeChk=="Nurse"){
															$userTypeLabel="Relief Nurse";	
															$userTypeQry="Nurse";
														}	 
															 
											?>
                                           <div class="col-md-4 col-sm-6 col-lg-4 col-xs-12 pull-right">
                                            <div class="inner_safety_wrap">
                                                <label for="r_nurse" class="col-md-12 col-lg-6 col-xs-12 col-sm-12"> <?php echo $userTypeLabel; ?>&nbsp;</label>
											   <div class="margin_adjustment_only hidden-lg"></div>			
                                                <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                  
                            						
                                                 	
                                                    <select name="relivedPostNurseIdList" class="selectpicker form-control" title="Select" >
                                                    	<option value="" selected>Select</option>
                                                        <?php
															$relivedPostNurseQry = "select * from users where user_type='".$userTypeQry."' ORDER BY lname";
			                       							$relivedPostNurseRes = imw_query($relivedPostNurseQry) or die(imw_error());
															while($relivedPostNurseRow=imw_fetch_array($relivedPostNurseRes)) 
															{
																$relivedSelectPostNurseID = $relivedPostNurseRow["usersId"];
																$relivedPostNurseName = $relivedPostNurseRow["lname"].", ".$relivedPostNurseRow["fname"]." ".$relivedPostNurseRow["mname"];
																$sel="";
																if($relivedPostNurseId==$relivedSelectPostNurseID) {
																	$sel = "selected";
																} 
																else {
																	$sel = "";
																}
																						
																if($relivedPostNurseRow["deleteStatus"]<>'Yes' || $relivedPostNurseId==$relivedSelectPostNurseID)
																 {
														?>
                                                        			<option value="<?php echo $relivedSelectPostNurseID;?>" <?php echo $sel;?>><?php echo $relivedPostNurseName;?></option>
                                                    	<?php
															
																}
															}
														?>
                                                	</select>	
                                                 </div>
                                            </div>
                                        </div>
                                           
                                           <Div class="clearfix margin_adjustment_only"></Div>          
	                                       <Div class="clearfix margin_adjustment_only"></Div>  
                                  
                                  
                                  </div>	
	
				</div>	

</form>
</div>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" method="post" action="local_anes_record.php?cancelRecord=true">
	<input type="hidden" name="patient_id" value="<?php echo $_GET['patient_id']; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->	
<?php 
//START CODE TO ENABLE/DISABLE TIME INTERVAL
if($blEnableHTMLGrid == true && ($activeTimeInterval=="Yes" || $finalizeStatus=="true") && $startTime!="00:00:00" && $startTime!=""){?>
	<script language="javascript" type="text/javascript">
			calculateTime('bp_temp6');
	</script>
<?php 
}
//END CODE TO ENABLE/DISABLE TIME INTERVAL
//CODE FOR FINALIZE FORM
	$finalizePageName = "local_anes_record.php";
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
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}
?>

<script>
window.setInterval("checkScroll(document.getElementById('gridTxtDivId'), document.getElementById('gridApltDivId'))", 1);

//SET ASSISTED BY TRANSLATOR VALUE IN HEADER
	if(document.getElementById('chbx_assist_id')) {
		if(document.getElementById('chbx_assist_id').checked==true) {
			top.document.getElementById('headerAssistID').checked=true;
		}else {
			top.document.getElementById('headerAssistID').checked=false;
		}
	}	
//END SET ASSISTED BY TRANSLATOR VALUE IN HEADER


//START FUNCTION TO SCAN/UPLOAD ANESTHESIA
function startCallback() {	
	return true;
}
function completeCallback(response){
	setTimeout('getImage()', 1000);
}
function getImage(){
	document.frm_local_anes_rec.submit();
}

var evaluationTitleArr 			= new Array();
<?php if($evaluationTitle!=""){ ?> var evaluationTitleArr = new Array(<?php echo $evaluationTitle;?>);<?php }?>
if(document.getElementById('local_anes_revaluation2_id')) { var evaluationTitleArr1 = new actb(document.getElementById('local_anes_revaluation2_id'),evaluationTitleArr);}

var postopevaluationTitleArr 	= new Array();
<?php if($postopevaluationTitle!=""){ ?> var postopevaluationTitleArr = new Array(<?php echo $postopevaluationTitle;?>);<?php }?>
if(document.getElementById('local_anes_revaluation1_id')) {var postopevaluationTitleArr1 = new actb(top.mainFrame.main_frmInner.document.getElementById('local_anes_revaluation1_id'),postopevaluationTitleArr);}
</script>

<script src="js/vitalSignGrid.js" type="text/javascript" ></script>

<script>
	$(function(){
		
		function GetCurrentDateTime(f1,f2)
		{
			var returnRes	=	'';
			var xmlHttp = GetXmlHttpObject()
			if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return false;
			}
			var url="user_agent.php"
			url=url+"?jsServerTimeRequest=fullDtTimeChart&pste="+Math.random();
			xmlHttp.onreadystatechange=function()
			{
				if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
				{ 
					returnRes	=	 xmlHttp.responseText ;
					f1.attr('title',returnRes).attr('data-original-title',returnRes);
					f2.val(returnRes);
					
				}
			};
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
			
			
		}

		var dosageObj		=	'input[name^="blank1"],input[name^="blank2"],input[name^="blank3"],input[name^="blank4"], input[name^="propofol"], input[name^="midazolam"], input[name^="Fentanyl"], input[name^="ketamine"], input[name^="labetalol"], input[name^="spo2"], input[name^="o2lpm"] ';
		
		$(dosageObj).click(function(){
			$(this).attr('data-previous-val',$(this).val().trim());
		});
		
		$(dosageObj).on( 'blur keyup',function(e) {
			var tVar		=	't_' + $(this).attr('name');
			var Val			=	$(this).val().trim();
            if( Val && Val != $(this).attr('data-previous-val' ))
			{
				GetCurrentDateTime($(this),$('input[name="' + tVar + '"]'))
			}
			else if( !Val )
			{
				$(this).attr('title','').attr('data-original-title','');
				$('input[name="' + tVar + '"]').val('');
			}
        });
		
		$(dosageObj).tooltip({

				  // place tooltip on the right edge
				  position: "center right",
			
				  // a little tweaking of the position
				  offset: [-2, 10],
			
				  // use the built-in fadeIn/fadeOut effect
				  effect: "fade",
			
				  // custom opacity setting
				  opacity: 0.7
	
		});

		<?php if($logInUserType == 'Anesthesiologist' && $localAnesFinalizeStatus <> 'true' ) { ?>
			$('body').on('focus', '#ekgMedGrid .datepickerTxt',function(){ 
				$(this).datetimepicker({ format: 'MM/DD/YYYY hh:mm:ss A',sideBySide:true,showClose:true,widgetPositioning: {vertical: 'auto',horizontal: 'right'} });
			});

			$(".ekg-edit-icon").click(function(){
				var med = $(this).data('med-type');
				var med_fld = $(this).data('med-fld');
				var top = parseInt($("#bp_temp8").offset().top)	;
				var left = parseInt($(this).offset().left);

				var f = (med == 'spo2' ? 'SaO2' : (med == 'o2lpm' ? 'O2l/m' : $("input[name="+med+"_label]").val() ));
				//console.log(med,f);
				var html = '<form id="ekgForm"><div class="col-xs-12">';
				html += '<input type="hidden" name="med_fld" value="'+med_fld+'" />';
				html += '<input type="hidden" name="confirmation_id" value="'+$("#pConfId").val()+'" />';
				for(var i = 0 ; i <=20 ; i++){
					var fldName = med_fld + '_' + i;
					var fldNameT = 't_' + med_fld + '_' + i;

					var val = i > 0 ? $("input[name="+fldName+"]").val() : 'Dosage';
					var val_t = i > 0 ? $("input[name="+fldNameT+"]").val() : 'Date/Time';
					var bgClass = i > 0 ? '' : 'ekg-grid-data-header';
					//console.log(fldName,fldNameT,val,val_t);
					html += '<div class="col-xs-1 '+bgClass+'">'+(i>0?i:'Sr.')+'</div>';
					html += '<div class="col-xs-4 '+bgClass+'">';
					html += (i > 0 ? '<input type="text" class="form-control" name="o'+fldName+'" id="o'+fldName+'" value="'+val+'" data-previous-val="'+val+'" />' : val);
					html += '</div>';
					html += '<div class="col-xs-7 '+bgClass+'">'; 
						if( i > 0  ) {
						html += '<div class="input-group">';
							html += '<input type="text" class="form-control datepickerTxt" name="o'+fldNameT+'" id="o'+fldNameT+'" value="'+val_t+'" data-previous-val="'+val_t+'" /> ';
							html += '<div class="input-group-addon">';
							html += '<label for="o'+fldNameT+'"><i class="glyphicon glyphicon-calendar pointer"></i></label>';
							html += '</div>';
						html += '</div>';
						}
						else html += val_t;
					html += '</div>';
					html += '<div class="clearfix margin_adjustment_only"></div>';
				}
				html += '</div>';
				html += '<div class="clearfix margin_adjustment_only">&nbsp;</div>';
				
				$("#ekgMedGrid .ekg-med-grid-data").html(html);
				$("#ekgMedGrid #med_name").html(f);
				//$("#ekgMedGrid").css( { 'left': (left+30)+'px', 'top': (top) + 'px' } ).show('fast');
				$("#ekgMedGrid").modal('show');

			});

			$("#ekgSaveBtn").click(function(){
				var len = $("#ekgMedGrid").find('form#ekgForm input[type=text]').length;
				if( len < 1 ) return false;

				var data = $("form#ekgForm").serialize();
				$.ajax({
					url:'library/ajax/save_ekg_meds.php',
					type:'post',
					dataType:'json',
					data:data,
					beforeSend:function(r){},
					success:function(r){
						if( r.success ){
							updateEKGData(r.data);
							modalAlert('Data saved successfully');
						}
						else {
							modalAlert('Error while saving. Please try again!!!');
						}
					},
					complete:function(r){
						$("#ekgMedGrid #med_name, #ekgMedGrid > .ekg-med-grid-data").html('');
						//$("#ekgMedGrid").hide('fast');
						$("#ekgMedGrid").modal('hide');
					}
				});

				return false;
			});
		<?php } ?>
	});

	function updateEKGData(r){
		for(var k in r ){
			var n = (k.substr(0,2) == 't_') ? '' : 't_'+k;
			$("input[name="+k+"]").val(r[k]);
			if(n) $("input[name="+k+"]").attr('title',r[n]).attr('data-original-title',r[n])
			
		}
		return true;
	}
</script>

<?php

	//START SCAN/UPLOAD ANESTHESIA
if($anes_ScanUploadPath || $anes_ScanUpload){
	$scnImgSrc = $anes_ScanUploadPath;
	
	if($anes_ScanUploadType == 'application/pdf') {
		if(!$anes_ScanUploadPath && $anes_ScanUpload){//CODE TO SHOW OLD SAVED RECORDS
			$scnImgSrc = "admin/logoImg.php?from=local_anesthesia_record&id=".$localAnesthesiaRecordId;	
		}?>	
   		<iframe src="<?php echo $scnImgSrc;?>" style="width:99%; height:80%;position:absolute; top:70px;"></iframe>
<?php
	}else {
		if(!$anes_ScanUploadPath && $anes_ScanUpload){//CODE TO SHOW OLD SAVED RECORDS
			$scnImgSrc = 'html2pdfnew/anesScanUpld.jpg';
			$bakImgResource = imagecreatefromstring($anes_ScanUpload);
			imagejpeg($bakImgResource,$scnImgSrc);
			$file=fopen($scnImgSrc,'w+');
			fputs($file,$anes_ScanUpload);
		}	

		$newSize=' width="150" height="100"';
		$priImageSize=array();
		if(file_exists($scnImgSrc)) {
			$priImageSize = getimagesize($scnImgSrc);
			if($priImageSize[0] > 395 && $priImageSize[1] < 840){
				$newSize = $objManageData->imageResize(680,400,710);						
				$priImageSize[0] = 710;
			}					
			elseif($priImageSize[1] > 840){
				$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],840);						
				$priImageSize[1] = 840;
			}
			else{					
				$newSize = $priImageSize[3];
			}							
			if($priImageSize[1] > 800 ){					
				echo '<newpage>';
			}		
		}
		?>
		<div class="valignTop" style="position:absolute; top:70px; padding-left:5px; padding-right:1px;">
			<img style="border:none; cursor:pointer;"  src="<?php echo $scnImgSrc;?>" <?php echo $newSize;?> onClick="window.open('<?php echo $scnImgSrc; ?>','winAnesScnUpld','scrollbars=yes,width=900,height=400,resizable=yes,location=yes,status=yes')">
		</div>
        
        <?php
	}
}

if($finalizeStatus!='true' && $permissionToWriteChart=="yes"){//from privilege_buttons.php	
	if($sect!="print_emr") {
	?>
	
        <div id="anesthesiaSummaryIolScanBtnId" style="visibility:visible; position:absolute; ">
            	<a href="javascript:void(0)" class="btn btn-primary" id="scanBtn" title="scan" onClick="javascript:anesthesiaScanWinOpn('admin/scanPopUp.php','<?php echo $_REQUEST["pConfId"]; ?>','<?php echo $localAnesthesiaRecordId; ?>'); "> Scan	</a>
      	</div>
        
        <form action="uploadAnesthesiaImage.php" name="frm_uploadAnesImage" enctype="multipart/form-data" method="post" onSubmit="return AIM.submit(this, {'onStart' : startCallback, 'onComplete' : completeCallback})">
            <div id="uploadImageDivId" style="position:absolute;  display:<?php echo $disDivIdDisplay;?> ">
                <input type="file" class="form-control" style=" <?php  echo $AnesthesiaBackColor;?> " id="upload_image_id" name="uploadImage" />
            </div>
            
            <div id="uploadBtnDivId" style=" visibility:visible;position:absolute;  display:<?php echo $disDivIdDisplay;?> ">
                <button type="submit" title="Upload" style="" class="btn btn-primary" value="Upload"  name="uploadBtn" id="uploadBtn" alt="Upload"  >Upload</button>
            	
            </div>	
            <input type="hidden" name="pConfId" value="<?php echo $_REQUEST["pConfId"]; ?>" />
            <input type="hidden" name="localAnesthesiaRecordId" value="<?php echo $localAnesthesiaRecordId; ?>" />	
            <input type="hidden" name="hidd_delImage" id="hidd_delImage" value="" />
        </form>
        
		<?php
        if($anes_ScanUploadPath || $anes_ScanUpload)
		{	//show delete button if any scan/upload document exist
        ?>
            <div style="visibility:visible;position:absolute; " id="delBtnId">
                <a style="cursor:hand; " class=" btn btn-group btn-danger"  name="deleteImage" id="deleteImage" onClick="if(confirm('Are you sure to delete the document ?')) {document.frm_uploadAnesImage.hidd_delImage.value='yes';document.getElementById('uploadBtn').click();}" ><b class="fa fa-trash"></b>&nbsp;Delete</a>
            </div>
        <?php
        }
	}
}
?>
<script>	
	//START CODE TO RUN IN CASE OF PRINT
	/*function printAnesthesiaGrid() {
		document.getElementById("hiddPrintLocalAnesPage").value = "yes";
		document.frm_local_anes_rec.submit();
		if(typeof(parent.submitfn) != 'undefined') {
			parent.submitfn();	
		}
	}
	
	var printAnesthesiaGridFrame = "<?php echo $_REQUEST['printAnesthesiaGridFrame'];?>"; //from local_anesthesia_record_pdf.php	
	if(printAnesthesiaGridFrame=='yes') {
		setTimeout('saveAnesthesiaGrid()',6000);
		setTimeout('printAnesthesiaGrid()',6100);
	}*/
	//END CODE TO RUN IN CASE OF PRINT
	
</script> 
<!--<div id="ekgMedGrid" class="col-lg-4 col-xs-5 ekg-med-grid">
<div style="position:relative;" id="ekgCal"></div>	
	<div class="col-xs-12 ekg-med-grid-header" >
		Add/Edit Dosage - <span id="med_name"></span>
		<span onclick="document.getElementById('ekgMedGrid').style.display='none';" class="ekg-med-grid-close">X</span>
		<span><button type="button" id="ekgSaveBtn" class="btn btn-sm btn-info ekg-med-save-btn">Save & Close</button></span>	
		
	</div>
	<div class="col-xs-12 ekg-med-grid-data">

	</div>
</div>-->
<?php if($logInUserType == 'Anesthesiologist' && $localAnesFinalizeStatus <> 'true' ) { ?>
<div id="ekgMedGrid" class="modal fade in" data-backdrop="true" style="top:0;margin:0;width:100%;"> <!--Common Alert Container-->
	<div class="modal-dialog " style="width:100%;height:93%;">
		<div class="modal-content" style="width:650px;margin:auto;height:100%;">
			<div class="modal-header" style="padding:6px 12px;">
				<button style="color:#FFFFFF;opacity:0.9" ype="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="color:#FFFFFF;" class="modal-title">Add/Edit Dosage - <span id="med_name"></span></h4>
			</div>
			<div class="modal-body ekg-med-grid-data" style="min-height:auto;" >
				
			</div>
			<div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
				<button style="margin-bottom:5px;margin-top:10px;" id="ekgSaveBtn" class="btn btn-primary btn-sm" >Save & Close</button>
				<button style="margin-bottom:5px;margin-top:10px;" id="ekgCloseBtn" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<?php
include("no_record.php");
include("print_page.php");
	?>
</body>
</html>