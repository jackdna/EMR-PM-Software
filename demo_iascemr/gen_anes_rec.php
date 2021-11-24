<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$tablename = "genanesthesiarecord";
include_once("common/commonFunctions.php");
//include("common/linkfile.php");

//draw--
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


//draw--

?>
<!DOCTYPE html>
<html>
<head>
<title>Operating Room Record</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
	
	#div_cnvs_anes{border:1px solid black;background:url(sc-grid/images/bgGridBig.jpg) no-repeat;background-color:white;display:inline-block;height:645px;width:619px;}
	
	.drsElement {
		position: absolute;
		border: 1px solid #333;
	}
	.drsMoveHandle {
		height: 20px;
		background-color: #CCC;
		border-bottom: 1px solid #666;
	}
	.bg{
	background-color:"#F1F4F0";
	
	
	}
	.text_formb12 { font-family:"verdana"; font-size:11px; color:#0066FF; margin:-1.55em 0 0 25px;}

	.borderMTB
	{
	border-bottom-color:#000000;
	border-bottom-width:1;
	border-top-color:#000000;
	border-top-width:3;
	}
	.borderML
	{border-left-color:#000000;
	border-left-width:3;
	}
	.leftbnone
	{
	border-bottom-color:#000000;
	border-bottom-width:1;
	border-top-color:#000000;
	border-top-width:1;
	border-right-width:1;
	}
		.rightbnone
	{
	
	   border-left-color:#000000;
	   border-left-width:3;
	
	
	
	}
	.checkM
	{
	display:block;
	line-height:1.4em;
	margin:46px 42px 42px 43px;
	width:43px;
	height:73px;
		}
	</style>
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<?php
$spec = '</head>
<body onLoad="top.changeColor(\''.$tablebg_local_anes.'\');" onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();">';
include("common/link_new_file.php");
//START INCLUDE PREDEFINE FUNCTIONS
	include_once("common/evaluation_pop.php"); //PRE OP  PHYSICIAN
//END INCLUDE PREDEFINE FUNCTIONS


extract($_GET);
$SaveForm_alert = $_REQUEST['SaveForm_alert'];

$blurInput = "onBlur=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\"";
$keyPressInput = "onKeyPress=\"javascript:this.style.backgroundColor='#FFFFFF'\"";
$focusInput = "onFocus=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\"";
$keyUpInput = "onKeyUp=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\"";


//CODE TO DISABLE SLIDER LINK AT SINGLE CLICK  
	$patient_id = $_REQUEST["patient_id"];
	$pConfId = $_REQUEST["pConfId"];
	$thisId = $_REQUEST["thisId"];
	if($innerKey=="") {
		$innerKey = $_REQUEST["innerKey"];
	}
	if($preColor=="") {
		$preColor = $_REQUEST["preColor"];
	}	
	$fieldName = "genral_anesthesia_form";
	$pageName = "gen_anes_rec.php?patient_id=$patient_id&pConfId=$pConfId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
	}
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;preColor='.$preColor;


//GET COMMON VARIABLES FOR THIS PAGE
	$selectPatientProcedureQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$selectPatientProcedureRes = imw_query($selectPatientProcedureQry) or die(imw_error());
	$selectPatientProcedureNumRow = imw_num_rows($selectPatientProcedureRes);
	if($selectPatientProcedureNumRow>0) {
		$selectPatientProcedureRow = imw_fetch_array($selectPatientProcedureRes);
		
		$patient_primary_procedure = $selectPatientProcedureRow["patient_primary_procedure"];
		$patient_secondary_procedure = $selectPatientProcedureRow["patient_secondary_procedure"];
		
		$anesthesiologist_id = $selectPatientProcedureRow["anesthesiologist_id"];
		//START CODE TO SET ANES. SIGN/COLOR MANDATORY OR NOT
		$confirmAnes_NA = $selectPatientProcedureRow["anes_NA"];
		$signAnesthesiaIdBackColor=$chngBckGroundColor;
		if($confirmAnes_NA=='Yes') {
			$signAnesthesiaIdBackColor=$whiteBckGroundColor; 
		}
		//END CODE TO SET ANES. SIGN/COLOR MANDATORY OR NOT
		
		$getanesthesiologistNameQry = "select * from users where usersId='$anesthesiologist_id' and user_type='Anesthesiologist'";
		$getanesthesiologistNameRes = imw_query($getanesthesiologistNameQry) or die(imw_error());
		$getanesthesiologistNameRow = imw_fetch_array($getanesthesiologistNameRes); 
		$getanesthesiologistName = $getanesthesiologistNameRow["lname"].", ".$getanesthesiologistNameRow["fname"]." ".$getanesthesiologistNameRow["mname"];
		
		// GETTING NURSE SIGN OR NOT		
			$signatureOfAnestheologist = $getanesthesiologistNameRow["signature"];
		// GETTING NURSE SIGN OR NOT 
		
	}
	//GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD
		/*
		$selectPreOpNursingQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectPreOpNursingRes = imw_query($selectPreOpNursingQry) or die(imw_error());
		$selectPreOpNursingNumRow = imw_num_rows($selectPreOpNursingRes);
		if($selectPreOpNursingNumRow>0) {
			$selectPreOpNursingRow = imw_fetch_array($selectPreOpNursingRes);
			$patientHeight = stripslashes($selectPreOpNursingRow["patientHeight"]);
			
			if(trim($patientHeight)<>"" || $patientHeight<>"") {
				$patientHeightsplit = explode("'",$patientHeight); 
				$patientHeight = $patientHeightsplit[0]."' ".$patientHeightsplit[1].'"';
				
				$patientHeightInches = ($patientHeightsplit[0]*12)+$patientHeightsplit[1];
			}else {
				$patientHeight = "";
			}
			$patientWeight = $selectPreOpNursingRow["patientWeight"];
			if($patientWeight<>"") {
				$patientWeight = $patientWeight." lbs";
			}
			//CODE TO CALCULATE BMI VALUE
				if((trim($patientHeight)<>"" || $patientHeight<>"") && $patientWeight<>"") {
					
					$BMIvalueTemp = $patientWeight * 703/($patientHeightInches*$patientHeightInches);
					$BMIvalue = number_format($BMIvalueTemp,2,".","");
				}
			//END CODE TO CALCULATE BMI VALUE 
		}	
		*/
	//END GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD
	
	
//END GET COMMON VARIABLES FOR THIS PAGE

//SAVE RECORD TO DATABASE
if($_POST['SaveRecordForm']=='yes'){
	$text = $_REQUEST['getText'];
	$tablename = "genanesthesiarecord";
	
	$alertOriented = $_POST["chbx_gen_anes_alert"];
	$assistedByTranslator = $_POST["chbx_assistedByTranslator"];
	$bp = $_POST["txt_bp"];
	$P = $_POST["txt_P"];
	$rr = $_POST["txt_rr"];
	$sao = $_POST["txt_sao"];
	$anesthesiaClass = $_POST["chbx_gen_anes_class"];
	$patientVerified = $_POST["chbx_patientVerified"];	
	$BMIvalue = $_POST["txt_BMIvalue"];
	$PatientReassessed = $_POST["chbx_PatientReassessed"];
	$MachineEquipment = $_POST["chbx_MachineEquipment"];
	$o2n2oavailable = $_POST["chbx_o2n2oavailable"];
	$reserveTanksChecked = $_POST["chbx_reserveTanksChecked"];
	$positivePressureAvailable = $_POST["chbx_positivePressureAvailable"];
	$maskTubingPresent = $_POST["chbx_maskTubingPresent"];
	$vaporizorFilled = $_POST["chbx_vaporizorFilled"];
	$absorberFunctional = $_POST["chbx_absorberFunctional"];
	$gasEvacuatorFunctional = $_POST["chbx_gasEvacuatorFunctional"];
	$o2AnalyzerFunctional = $_POST["chbx_o2AnalyzerFunctional"];
	$ekgMonitor = $_POST["chbx_ekgMonitor"];
	$endoTubes = $_POST["chbx_endoTubes"];
	$laryngoscopeBlades = $_POST["chbx_laryngoscopeBlades"];
	$others = $_POST["chbx_others"];
	$othersDesc = $_POST["othersDesc"];
	
	
	$startTime = $objManageData->setTmFormat($_POST["startTime"]);
	$stopTime = $objManageData->setTmFormat($_POST["stopTime"]);
	/*
	$startTimeTemp = $_POST["startTime"];
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
	
	$stopTimeTemp = $_POST["stopTime"];
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
	*/
	$macMiller = $_POST["gen_anes_mac"];
	if($macMiller =="mac") {
		$mac = "mac";
		$macValue = $_POST["gen_anes_submac"];
		$millar = "";
		$millarValue = "";
	}else if($macMiller =="miller") {
		$mac = "";
		$macValue = "";
		$millar = "millar";
		$millarValue = $_POST["gen_anes_miller"];
	}else {
		$mac = "";
		$macValue = "";
		$millar = "";
		$millarValue = "";
	}

	$Tube_lma_mask = $_POST["chbx_et_lm_msk"];
	if($Tube_lma_mask =="etTube") {
		$etTube = "etTube";
		$etTubeSize = $_POST["chbx_et_tubeSize"];
		$lma = "";
		$lmaSize = "";
		$mask = "";
	}else if($Tube_lma_mask =="lma") {
		$etTube = "";
		$etTubeSize = "";
		$lma = "lma";
		$lmaSize = $_POST["lma_sub"];
		$mask = "";
	}else if($Tube_lma_mask =="mask") {
		$etTube = "";
		$etTubeSize = "";
		$lma = "";
		$lmaSize = "";
		$mask = "mask";
	}else {
		$etTube = "";
		$etTubeSize = "";
		$lma = "";
		$lmaSize = "";
		$mask = "";
	}

	$teethUnchanged = $_POST["chbx_teethUnchanged"];
	$Monitor_ekg = $_POST["chbx_ekg"];
	$Monitor_etco2 = $_POST["chbx_etco2"];
	$Monitor_etco2Sat = $_POST["chbx_etco2Sat"];
	$Monitor_o2Temp = $_POST["chbx_o2Temp"];
	$Monitor_PNS = $_POST["chbx_PNS"];
	
	$genAnesSignApplet = $_POST["elem_signs"];
	//Start Canvas Drawing --
	$cnvs_anes_drw_coords = $_POST["elem_cnvs_anes_drw_coords"];
	$cnvs_anes_drw = $_POST["elem_cnvs_anes_drw"];
	$cnvs_anes_drw = str_replace("data:image/png;base64,","",$cnvs_anes_drw);	
	$up_img="";
	if(!empty($cnvs_anes_drw_coords) && !empty($cnvs_anes_drw) && !empty($_REQUEST["patient_id"])){
		$cnvs_anes_drw =  base64_decode($cnvs_anes_drw);
		if(!empty($cnvs_anes_drw)){
		$updir = dirname(__FILE__)."/admin/pdfFiles/gen_anes_detail";
		if(!is_dir($updir)){
			mkdir($updir,0777,true);
		}
		
		if(is_dir($updir)){
			$up_img = "gar_".$_REQUEST["patient_id"]."_".$_REQUEST["pConfId"].".png"; 	
			file_put_contents($updir."/".$up_img, $cnvs_anes_drw);
			if(file_exists($updir."/".$up_img)){
				//--
				$img_w = 619;
				$img_h = 645;
				$im = imagecreate($img_w, $img_h+20);
				$background_color = imagecolorallocate($im, 255, 255, 255);
				$text_color = imagecolorallocate($im, 0, 0, 0);
				
				if(!empty($startTime)){
				$startTime_org = $startTime;
				$startTime_arr = explode(":",$startTime);
				$startTime_arr[1] = abs($startTime_arr[1]);
				if(!empty($startTime_arr[1])){
					if($startTime_arr[1]<15){ $startTime_arr[1]="00"; }
					else if($startTime_arr[1]<30){ $startTime_arr[1]="15"; }
					else if($startTime_arr[1]<45){ $startTime_arr[1]="30"; }
					else if($startTime_arr[1]<59){ $startTime_arr[1]="45"; }
				}
				$startTime = $startTime_arr[0].":".$startTime_arr[1];
				
				$lf = 140;
				for($i=0;$i<10;$i++){					
					imagestring($im, 1, $lf , 10, $startTime , $text_color);
					$lf = $lf + 49;					
					$startTime_arr = explode(":",$startTime);
					$startTime_arr[1] = abs($startTime_arr[1]);
					$startTime_arr[1] = $startTime_arr[1] + 15;
					if($startTime_arr[1]>=60){$startTime_arr[1]="00";
					$startTime_arr[0] = abs($startTime_arr[0]);
					$startTime_arr[0] = $startTime_arr[0] +1;
					if($startTime_arr[0]>=12){$startTime_arr[0]="0";}
					if($startTime_arr[0]<10){$startTime_arr[0] = "0".$startTime_arr[0]; } 
					}
					$startTime = $startTime_arr[0].":".$startTime_arr[1];					
				}
				$startTime = $startTime_org;
				}
				///*
				$dest = imagecreatefromjpeg('sc-grid/images/bgGridBig.jpg');
				imagecopy($im, $dest, 0, 20, 0, 0, $img_w, $img_h);				
				$tmp = str_replace(".png",".jpg",$up_img);
				imagejpeg($im, $updir."/".$tmp);
				imagedestroy($dest);
				imagedestroy($im);
				
				$dest = imagecreatefromjpeg($updir."/".$tmp);
				$src = imagecreatefrompng($updir."/".$up_img);
				imagecopy($dest, $src, 0, 20, 0, 0, $img_w, $img_h);
				imagejpeg($dest, $updir."/".$tmp);	
				if(file_exists($updir."/".$tmp)){  unlink($updir."/".$up_img); $up_img = $tmp; }
				imagedestroy($src);
				imagedestroy($dest);
				if(!empty($up_img)){ $up_img = "pdfFiles/gen_anes_detail/".$up_img; }
				
				//*/
				//*/
				
				//--
			}
		}
		}
	}
	//End Canvas Drawing --
	
	$armsTuckedLeft = $_POST["chbx_armsTuckedLeft"];
	$armsTuckedRight = $_POST["chbx_armsTuckedRight"];
	$armsArmboardsLeft = $_POST["chbx_armsArmboardsLeft"];
	$armsArmboardsRight = $_POST["chbx_armsArmboardsRight"];
	
	$eyeTapedLeft = $_POST["chbx_eyeTapedLeft"];
	$eyeLubedLeft = $_POST["chbx_eyeLubedLeft"];
	/*
	$eyeTapedRight = $_POST["chbx_eyeTapedRight"];
	$eyeLubedRight = $_POST["chbx_eyeLubedRight"];
	*/
	$pressurePointsPadded = $_POST["chbx_pressurePointsPadded"];
	$bss = $_POST["chbx_bss"];
	$warning = $_POST["chbx_warning"];
	$temp = $_POST["txt_temp"];
	$StableCardioRespiratory = $_POST["chbx_StableCardioRespiratory"];
	
	$graphComments = addslashes($_POST["graphComments"]);
	if(trim($graphComments)=='comment'){ $graphComments='';}
	
	$evaluation = addslashes($_POST["txtarea_evaluation"]);
	$comments = addslashes(trim($_POST["txtarea_comments"]));
	$anesthesiologistId = $_POST["anesthesiologistId"];
	$anesthesiologistSign = $_POST["anesthesiologistSign"];
	$relivedNurseId = $_POST["relivedNurseIdList"];
	
	//START CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
		$chkUserSignDetails = $objManageData->getRowRecord('genanesthesiarecord', 'confirmation_id', $pConfId);
		if($chkUserSignDetails) {
			$chk_signAnesthesia1Id = $chkUserSignDetails->signAnesthesia1Id;
			$chk_signAnesthesia2Id = $chkUserSignDetails->signAnesthesia2Id;
			$chk_form_status = $chkUserSignDetails->form_status;
			$chk_vitalSignGridStatus	=	$chkUserSignDetails->vitalSignGridStatus;
			$chk_versionNum = $chkUserSignDetails->version_num;
			$chk_versionDateTime = $chkUserSignDetails->version_date_time;
		}
	//END CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
		$vitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($chk_form_status,$chk_vitalSignGridStatus,'genAnes');
		$vitalSignGridQuery		=	'';
		if($chk_form_status <> 'completed' && $chk_form_status <> 'not completed')
		{
			$vitalSignGridQuery		=	", vitalSignGridStatus = '".$vitalSignGridStatus."'  ";	
		}
		
		$version_num = $chk_versionNum;
		$versionNumQry	=	'';
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
				$version_num	=	2;
			}
			
			$versionNumQry	=	" version_num = '".$version_num."', version_date_time = '".$version_date_time."',  ";
			
		}
		
		if($version_num > 1)
		{
			$reliefNurseId					= $_POST['reliefNurseId'];
			$confirmIPPSC_signin		= addslashes($_POST['chbx_ipp']);
			$siteMarked 						= addslashes($_POST['chbx_smpp']);
			$patientAllergies 			= addslashes($_POST['chbx_pa']);	
			$difficultAirway				= addslashes($_POST['chbx_dar']);
			$anesthesiaSafety				= addslashes($_POST['chbx_asc']);	
			$allMembersTeam 				= addslashes($_POST['chbx_adcpc']);	
			$riskBloodLoss 					= addslashes($_POST['chbx_rbl']);	
			$bloodLossUnits 				= ($_POST['chbx_rbl'] == 'Yes') ? addslashes($_POST['rbl_no_of_units']) : '' ;	
		}
	
		$PatientReassessed = $_POST["chbx_PatientReassessed"];
		$MachineEquipment = $_POST["chbx_MachineEquipment"];

	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		
		 if($anesthesiaClass==""
		 
		 ||($PatientReassessed=="" && $MachineEquipment=="")
		 || ($startTime=="")
		 || ($stopTime=="")
		 //|| ($macMiller=="")
		 || ($Tube_lma_mask=="")
		 || ($Monitor_ekg=="" && $Monitor_etco2=="" && $Monitor_etco2Sat=="" && $Monitor_o2Temp=="" && $Monitor_PNS=="")
		 
		 || ($armsTuckedLeft=="" && $armsTuckedRight=="" && $armsArmboardsLeft=="" && $armsArmboardsRight=="") 
		 //|| ($eyeTapedLeft=="" && $eyeTapedRight=="" && $eyeLubedLeft=="" && $eyeLubedRight=="")
		 || ($pressurePointsPadded=="" && $bss=="" && $warning=="")
		 || trim($evaluation)==""
		 || ($bp=="" && $P=="" && $rr=="" && $sao=="" && $StableCardioRespiratory=="")
		 || ($chk_signAnesthesia1Id=="0" && $confirmAnes_NA!='Yes')
		)
		
		{
			$form_status = "not completed";
		}
		
		if($version_num > 1 && $form_status == "completed")
		{
			if( 		$confirmIPPSC_signin == '' || $siteMarked == '' || $patientAllergies == ''
					||	$difficultAirway == '' || $anesthesiaSafety == '' || $allMembersTeam == '' || $riskBloodLoss == ''
					||	($chk_signAnesthesia2Id=="0" && $confirmAnes_NA!='Yes')
				)
			{
					$form_status = "not completed";	
			}
		}
		
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	//START CODE TO RESET THE RECORD
	$resetRecordQry='';
	if($_REQUEST['hiddResetStatusId']=='Yes') {
		$form_status 		= 	'';
		$genAnesSignApplet	=	'';
		$resetRecordQry 	= 	"signAnesthesia1Id			='',
								 signAnesthesia1FirstName	='', 
								 signAnesthesia1MiddleName	='',
								 signAnesthesia1LastName	='', 
								 signAnesthesia1Status		='',
								 signAnesthesia1DateTime	='',
								 signAnesthesia2Id			='',
								 signAnesthesia2FirstName	='', 
								 signAnesthesia2MiddleName	='',
								 signAnesthesia2LastName	='', 
								 signAnesthesia2Status		='',
								 signAnesthesia2DateTime	='',
								 version_num				='0',
								 version_date_time			='0000-00-00',
								 resetDateTime 				= '".date('Y-m-d H:i:s')."',
								 resetBy 					= '".$_SESSION['loginUserId']."',
								";
		
	}
	//END CODE TO RESET THE RECORD
	
	$chkgenAnesQry = "select * from `genanesthesiarecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkgenAnesRes = imw_query($chkgenAnesQry) or die(imw_error()); 
	$chkgenAnesNumRow = imw_num_rows($chkgenAnesRes);
	if($chkgenAnesNumRow>0) {
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkgenAnesRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		$SavegenAnesQry = "update `genanesthesiarecord` set
									drawing_path='".imw_real_escape_string($up_img)."',
									drawing_coords='".imw_real_escape_string($cnvs_anes_drw_coords)."',
									alertOriented = '$alertOriented',
									assistedByTranslator = '$assistedByTranslator',
									bp = '$bp',
									P = '$P',
									rr = '$rr',
									sao = '$sao',
									anesthesiaClass = '$anesthesiaClass',
									patientVerified = '$patientVerified',
									BMIvalue = '$BMIvalue',
									PatientReassessed = '$PatientReassessed',
									MachineEquipment = '$MachineEquipment',
									o2n2oavailable = '$o2n2oavailable',
									reserveTanksChecked = '$reserveTanksChecked',
									positivePressureAvailable = '$positivePressureAvailable',
									maskTubingPresent = '$maskTubingPresent',
									vaporizorFilled = '$vaporizorFilled',
									absorberFunctional = '$absorberFunctional',
									gasEvacuatorFunctional = '$gasEvacuatorFunctional',
									o2AnalyzerFunctional = '$o2AnalyzerFunctional',
									ekgMonitor = '$ekgMonitor',
									endoTubes = '$endoTubes',
									laryngoscopeBlades = '$laryngoscopeBlades',
									others = '$others',
									othersDesc = '$othersDesc',
									startTime = '$startTime',
									stopTime = '$stopTime',
									mac = '$mac',
									macValue = '$macValue', 
									millar = '$millar',
									millarValue = '$millarValue', 
									etTube = '$etTube',
									etTubeSize = '$etTubeSize', 
									lma = '$lma',
									lmaSize = '$lmaSize',
									mask = '$mask',
									teethUnchanged = '$teethUnchanged',
									Monitor_ekg = '$Monitor_ekg', 
									Monitor_etco2 = '$Monitor_etco2', 
									Monitor_etco2Sat = '$Monitor_etco2Sat',
									Monitor_o2Temp = '$Monitor_o2Temp', 
									Monitor_PNS = '$Monitor_PNS', 
									genAnesSignApplet = '$genAnesSignApplet',
									armsTuckedLeft = '$armsTuckedLeft', 
									armsTuckedRight = '$armsTuckedRight', 
									armsArmboardsLeft = '$armsArmboardsLeft',
									armsArmboardsRight = '$armsArmboardsRight', 
									eyeTapedLeft = '$eyeTapedLeft',
									eyeLubedLeft = '$eyeLubedLeft',
									pressurePointsPadded = '$pressurePointsPadded',
									bss = '$bss',
									warning = '$warning',
									temp = '$temp',
									StableCardioRespiratory = '$StableCardioRespiratory',
									graphComments = '$graphComments',
									evaluation = '$evaluation',
									comments = '$comments',
									anesthesiologistId = '$anesthesiologistId',
									anesthesiologistSign = '$anesthesiologistSign',
									relivedNurseId = '$relivedNurseId',
									reliefNurseId					= '$reliefNurseId',
									confirmIPPSC_signin		= '$confirmIPPSC_signin',
									siteMarked 						= '$siteMarked',
									patientAllergies 			= '$patientAllergies',
									difficultAirway				= '$difficultAirway',
									anesthesiaSafety			= '$anesthesiaSafety',
									allMembersTeam 				= '$allMembersTeam',
									riskBloodLoss 				= '$riskBloodLoss',
									bloodLossUnits				=	'$bloodLossUnits',	
									$resetRecordQry
									$versionNumQry
									form_status ='".$form_status."',
									ascId='".$_REQUEST["ascId"]."'
									".$vitalSignGridQuery." 
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
	}else {
		$SavegenAnesQry = "insert into `genanesthesiarecord` set 
									drawing_path='".imw_real_escape_string($up_img)."',
									drawing_coords='".imw_real_escape_string($cnvs_anes_drw_coords)."',
									alertOriented = '$alertOriented',
									assistedByTranslator = '$assistedByTranslator',
									bp = '$bp',
									P = '$P',
									rr = '$rr',
									sao = '$sao',
									anesthesiaClass = '$anesthesiaClass',
									patientVerified = '$patientVerified',
									BMIvalue = '$BMIvalue',
									PatientReassessed = '$PatientReassessed',
									MachineEquipment = '$MachineEquipment',
									o2n2oavailable = '$o2n2oavailable',
									reserveTanksChecked = '$reserveTanksChecked',
									positivePressureAvailable = '$positivePressureAvailable',
									maskTubingPresent = '$maskTubingPresent',
									vaporizorFilled = '$vaporizorFilled',
									absorberFunctional = '$absorberFunctional',
									gasEvacuatorFunctional = '$gasEvacuatorFunctional',
									o2AnalyzerFunctional = '$o2AnalyzerFunctional',
									ekgMonitor = '$ekgMonitor',
									endoTubes = '$endoTubes',
									laryngoscopeBlades = '$laryngoscopeBlades',
									others = '$others',
									othersDesc = '$othersDesc',
									startTime = '$startTime',
									stopTime = '$stopTime',
									mac = '$mac',
									macValue = '$macValue', 
									millar = '$millar',
									millarValue = '$millarValue', 
									etTube = '$etTube',
									etTubeSize = '$etTubeSize', 
									lma = '$lma',
									lmaSize = '$lmaSize',
									mask = '$mask',
									teethUnchanged = '$teethUnchanged',
									Monitor_ekg = '$Monitor_ekg', 
									Monitor_etco2 = '$Monitor_etco2', 
									Monitor_etco2Sat = '$Monitor_etco2Sat',
									Monitor_o2Temp = '$Monitor_o2Temp', 
									Monitor_PNS = '$Monitor_PNS', 
									genAnesSignApplet = '$genAnesSignApplet',
									armsTuckedLeft = '$armsTuckedLeft', 
									armsTuckedRight = '$armsTuckedRight', 
									armsArmboardsLeft = '$armsArmboardsLeft',
									armsArmboardsRight = '$armsArmboardsRight', 
									eyeTapedLeft = '$eyeTapedLeft',
									eyeLubedLeft = '$eyeLubedLeft',
									pressurePointsPadded = '$pressurePointsPadded',
									bss = '$bss',
									warning = '$warning',
									temp = '$temp',
									StableCardioRespiratory = '$StableCardioRespiratory',
									graphComments = '$graphComments',
									evaluation = '$evaluation',
									comments = '$comments',
									anesthesiologistId = '$anesthesiologistId',
									anesthesiologistSign = '$anesthesiologistSign',
									relivedNurseId = '$relivedNurseId',
									reliefNurseId					= '$reliefNurseId',
									confirmIPPSC_signin		= '$confirmIPPSC_signin',
									siteMarked 						= '$siteMarked',
									patientAllergies 			= '$patientAllergies',
									difficultAirway				= '$difficultAirway',
									anesthesiaSafety			= '$anesthesiaSafety',
									allMembersTeam 				= '$allMembersTeam',
									riskBloodLoss 				= '$riskBloodLoss',
									bloodLossUnits				=	'$bloodLossUnits',
									$resetRecordQry
									$versionNumQry
									form_status ='".$form_status."',									
									confirmation_id='".$_REQUEST["pConfId"]."',
									patient_id = '".$_REQUEST["patient_id"]."'
									".$vitalSignGridQuery." 
									";
	}
	$SavegenAnesRes = imw_query($SavegenAnesQry) or die(imw_error());
	
	//SAVE ENTRY IN chartnotes_change_audit_tbl 
		
		$chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
									user_id='".$_SESSION['loginUserId']."' AND
									patient_id='".$_REQUEST["patient_id"]."' AND
									confirmation_id='".$_REQUEST["pConfId"]."' AND
									form_name='".$fieldName."' AND
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
										action_date_time='".date("Y-m-d H:i:s")."'";
		}else {
			$SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',
										patient_id='".$_REQUEST["patient_id"]."',
										confirmation_id='".$_REQUEST["pConfId"]."',
										form_name='$fieldName',
										status='created',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}					
		$SaveAuditChartNotesRes = imw_query($SaveAuditChartNotesQry) or die(imw_error());
	//END SAVE ENTRY IN chartnotes_change_audit_tbl
	
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
					$vSystolic	=	$_POST['vitalSignGrid_'.$row.'_2'];//$vBp
					$vDiastolic	=	$_POST['vitalSignGrid_'.$row.'_3'];
					$vPulse		=	$_POST['vitalSignGrid_'.$row.'_4'];
					$vRR			=	$_POST['vitalSignGrid_'.$row.'_5'];
					$vTemp		=	$_POST['vitalSignGrid_'.$row.'_6'];
					$vEtco2		=	$_POST['vitalSignGrid_'.$row.'_7'];
					$vosat2		=	$_POST['vitalSignGrid_'.$row.'_8'];
					
					if( $vTime && ($vSystolic || $vDiastolic || $vPulse || $vRR || $vTemp || $vEtco2 || $vosat2)  )
					{
						/*
						$timeSplit 		= explode(":",$vTime);
						$timeAmPm 	= strtolower($timeSplit[1][3]);
						if( $timeAmPm == "p" && $timeSplit[0] <> '12' ) {
							$timeSplit[0] = $timeSplit[0]+12;
						}
						elseif( $timeAmPm == "a" && $timeSplit[0] == '12' )
						{
							$timeSplit[0] = $timeSplit[0]-12;	
						}
						$vTime = $timeSplit[0].":".$timeSplit[1][0].$timeSplit[1][1].":00";
						*/
						$vTime	=	date('H:i:s',strtotime($vTime));
			
						$dataArray	=	array();
						$dataArray['chartName']			=	'genral_anesthesia_form';
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
	}
	
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		/*
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}*/
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
}

//END SAVE RECORD TO DATABASE

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
			//$MainTime = date('h:iA',strtotime($MainTime));
			//$MainTime = substr($MainTime,0,-1);
		}
		return $MainTime;
	}	
//END FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION

//VIEW RECORD FROM DATABASE
	//if($_POST['SaveRecordForm']==''){	
		$ViewgenAnesQry 	= "select *, date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat, date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat from `genanesthesiarecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewgenAnesRes 	= imw_query($ViewgenAnesQry) or die(imw_error()); 
		$ViewgenAnesNumRow 	= imw_num_rows($ViewgenAnesRes);
		$ViewgenAnesRow 	= imw_fetch_array($ViewgenAnesRes); 
		if($ViewgenAnesNumRow>0) {
			$alertOriented 					= $ViewgenAnesRow["alertOriented"];
			$assistedByTranslator 			= $ViewgenAnesRow["assistedByTranslator"];
			$bp 							= $ViewgenAnesRow["bp"];
			$P 								= $ViewgenAnesRow["P"];
			$rr 							= $ViewgenAnesRow["rr"];
			$sao 							= $ViewgenAnesRow["sao"];
			$anesthesiaClass 				= $ViewgenAnesRow["anesthesiaClass"];
			$o2n2oavailable 				= $ViewgenAnesRow["o2n2oavailable"];
			$patientVerified 				= $ViewgenAnesRow["patientVerified"];
			$BMIvalue 						= stripslashes(trim($ViewgenAnesRow["BMIvalue"]));
			$PatientReassessed 				= $ViewgenAnesRow["PatientReassessed"];
			$MachineEquipment 				= $ViewgenAnesRow["MachineEquipment"];
			$reserveTanksChecked 			= $ViewgenAnesRow["reserveTanksChecked"];
			$positivePressureAvailable 		= $ViewgenAnesRow["positivePressureAvailable"];
			$maskTubingPresent 				= $ViewgenAnesRow["maskTubingPresent"];
			$vaporizorFilled 				= $ViewgenAnesRow["vaporizorFilled"];
			$absorberFunctional 			= $ViewgenAnesRow["absorberFunctional"];
			$gasEvacuatorFunctional 		= $ViewgenAnesRow["gasEvacuatorFunctional"];
			$o2AnalyzerFunctional 			= $ViewgenAnesRow["o2AnalyzerFunctional"];
			$ekgMonitor 					= $ViewgenAnesRow["ekgMonitor"];
			$endoTubes 						= $ViewgenAnesRow["endoTubes"];
			$laryngoscopeBlades 			= $ViewgenAnesRow["laryngoscopeBlades"];
			$others 						= $ViewgenAnesRow["others"];
			$othersDesc 					= $ViewgenAnesRow["othersDesc"];
			
			$startTime 						= $ViewgenAnesRow["startTime"];
			$startTime						= calculate_timeFun($startTime); //CODE TO DISPLAY START TIME
			$stopTime 						= $ViewgenAnesRow["stopTime"];
			$stopTime						= calculate_timeFun($stopTime); //CODE TO DISPLAY STOP TIME
			
			$mac 							= $ViewgenAnesRow["mac"];
			$macValue 						= $ViewgenAnesRow["macValue"];
			$millar 						= $ViewgenAnesRow["millar"];
			$millarValue 					= $ViewgenAnesRow["millarValue"];
			$etTube 						= $ViewgenAnesRow["etTube"];
			$etTubeSize 					= $ViewgenAnesRow["etTubeSize"];
			$lma 							= $ViewgenAnesRow["lma"];
			$lmaSize 						= $ViewgenAnesRow["lmaSize"];
			$mask 							= $ViewgenAnesRow["mask"];
			
			$teethUnchanged 				= $ViewgenAnesRow["teethUnchanged"];
			$Monitor_ekg 					= $ViewgenAnesRow["Monitor_ekg"];
			$Monitor_etco2 					= $ViewgenAnesRow["Monitor_etco2"];
			$Monitor_etco2Sat 				= $ViewgenAnesRow["Monitor_etco2Sat"];
			$Monitor_o2Temp 				= $ViewgenAnesRow["Monitor_o2Temp"];
			$Monitor_PNS 					= $ViewgenAnesRow["Monitor_PNS"];
			
			
			$genAnesSignApplet 				= $ViewgenAnesRow["genAnesSignApplet"];
			
			$armsTuckedLeft 				= $ViewgenAnesRow["armsTuckedLeft"];
			$armsTuckedRight 				= $ViewgenAnesRow["armsTuckedRight"];
			$armsArmboardsLeft 				= $ViewgenAnesRow["armsArmboardsLeft"];
			$armsArmboardsRight 			= $ViewgenAnesRow["armsArmboardsRight"];
			
			$eyeTapedLeft 					= $ViewgenAnesRow["eyeTapedLeft"];
			$eyeLubedLeft 					= $ViewgenAnesRow["eyeLubedLeft"];
			/*
			$eyeTapedLeft 					= $ViewgenAnesRow["eyeTapedLeft"];
			$eyeLubedLeft 					= $ViewgenAnesRow["eyeLubedLeft"];
			*/
			$pressurePointsPadded 			= $ViewgenAnesRow["pressurePointsPadded"];
			$bss = $ViewgenAnesRow["bss"];
			$warning = $ViewgenAnesRow["warning"];
			
			$temp 											= $ViewgenAnesRow["temp"];
			$StableCardioRespiratory 		= $ViewgenAnesRow["StableCardioRespiratory"];
			$graphComments 							= $ViewgenAnesRow["graphComments"];
			$evaluation 								= $ViewgenAnesRow["evaluation"];
			$comments 									= $ViewgenAnesRow["comments"];
			$anesthesiologistId 				= $ViewgenAnesRow["anesthesiologistId"];
			$anesthesiologistSign 			= $ViewgenAnesRow["anesthesiologistSign"];
			$relivedNurseId 						= $ViewgenAnesRow["relivedNurseId"];
			
			$signAnesthesia1Id 					=  $ViewgenAnesRow["signAnesthesia1Id"];
			$signAnesthesia1DateTime 	= $ViewgenAnesRow["signAnesthesia1DateTime"];
			$signAnesthesia1DateTimeFormat 	= $ViewgenAnesRow["signAnesthesia1DateTimeFormat"];
			$signAnesthesia1FirstName 	=  $ViewgenAnesRow["signAnesthesia1FirstName"];
			$signAnesthesia1MiddleName 	=  $ViewgenAnesRow["signAnesthesia1MiddleName"];
			$signAnesthesia1LastName 		=  $ViewgenAnesRow["signAnesthesia1LastName"]; 
			$signAnesthesia1Status 			=  $ViewgenAnesRow["signAnesthesia1Status"];
			
			$signAnesthesia2Id 					=  $ViewgenAnesRow["signAnesthesia2Id"];
			$signAnesthesia2DateTime 	= $ViewgenAnesRow["signAnesthesia2DateTime"];
			$signAnesthesia2DateTimeFormat 	= $ViewgenAnesRow["signAnesthesia2DateTimeFormat"];
			$signAnesthesia2FirstName 	=  $ViewgenAnesRow["signAnesthesia2FirstName"];
			$signAnesthesia2MiddleName 	=  $ViewgenAnesRow["signAnesthesia2MiddleName"];
			$signAnesthesia2LastName 		=  $ViewgenAnesRow["signAnesthesia2LastName"]; 
			$signAnesthesia2Status 			=  $ViewgenAnesRow["signAnesthesia2Status"];
			
			$reliefNurseId					= $ViewgenAnesRow["reliefNurseId"];
			$confirmIPPSC_signin		= $ViewgenAnesRow["confirmIPPSC_signin"];
			$siteMarked 						= $ViewgenAnesRow["siteMarked"];
			$patientAllergies 			= $ViewgenAnesRow["patientAllergies"];
			$difficultAirway				= $ViewgenAnesRow["difficultAirway"];
			$anesthesiaSafety				= $ViewgenAnesRow["anesthesiaSafety"];
			$allMembersTeam 				= $ViewgenAnesRow["allMembersTeam"];
			$riskBloodLoss 					= $ViewgenAnesRow["riskBloodLoss"];
			$bloodLossUnits					=	$ViewgenAnesRow["bloodLossUnits"];
			$version_num						=	$ViewgenAnesRow["version_num"];
			$vitalSignGridStatus		=	$ViewgenAnesRow["vitalSignGridStatus"];
			
			$form_status						=	$ViewgenAnesRow["form_status"];
			$ascId 									= $ViewgenAnesRow["ascId"];
			$confirmation_id 				= $ViewgenAnesRow["confirmation_id"];
			$patient_id 						= $ViewgenAnesRow["patient_id"];
			$elem_cnvs_anes_drw_file				= $ViewgenAnesRow["drawing_path"];
			$elem_cnvs_anes_drw_coords				= $ViewgenAnesRow["drawing_coords"];
			
		
			
		}
	//}
		
		$vitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($form_status,$vitalSignGridStatus,'genAnes');
		
		if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
		else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	2; }
			
		
		
//GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD
		$selectPreOpNursingQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectPreOpNursingRes = imw_query($selectPreOpNursingQry) or die(imw_error());
		$selectPreOpNursingNumRow = imw_num_rows($selectPreOpNursingRes);
		if($selectPreOpNursingNumRow>0) {
			$selectPreOpNursingRow = imw_fetch_array($selectPreOpNursingRes);
			$patientHeight = stripslashes($selectPreOpNursingRow["patientHeight"]);
			
			if(trim($patientHeight)<>"" || $patientHeight<>"") {
				$patientHeightsplit = explode("'",$patientHeight); 
				$patientHeight = $patientHeightsplit[0]."' ".$patientHeightsplit[1].'"';
				
				$patientHeightInches = ($patientHeightsplit[0]*12)+$patientHeightsplit[1];
			}else {
				$patientHeight = "";
			}
			$patientWeight = $selectPreOpNursingRow["patientWeight"];
			if($patientWeight<>"") {
				$patientWeight = $patientWeight." lbs";
			}
			//CODE TO CALCULATE BMI VALUE
				if(!$BMIvalue) {
					if((trim($patientHeight)<>"" || $patientHeight<>"") && $patientWeight<>"") {
						
						$BMIvalueTemp = $patientWeight * 703/($patientHeightInches*$patientHeightInches);
						$BMIvalue = number_format($BMIvalueTemp,2,".","");
					}
				}	
			//END CODE TO CALCULATE BMI VALUE 
		}	
	
	//END GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD
			
//END VIEW RECORD FROM DATABASE

?>

<script type="text/javascript">
	function changeSliderColor(){
		var setColor = '<?php echo $bglight_blue_local_anes; ?>';
		top.changeColor(setColor);
	}
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
//Applet
function get_App_Coords(objElem){
	var coords,appName;
	var objElemSign = document.frm_gen_anes_rec.anesthesiologistSign;
	appName = objElem.name;
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
	var coords = document.applets["app_genAnesRecSignature"].getSign();
	return coords;
}
function getclear_os(){
	document.applets["app_genAnesRecSignature"].clearIt();
	changeColorThis(255,0,0);
	document.applets["app_genAnesRecSignature"].onmouseout();
}
function changeColorThis(r,g,b){				
	document.applets['app_genAnesRecSignature'].setDrawColor(r,g,b);								
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
		objTd.style.backgroundColor = "#ffffcc";
		objCurTd.style.backgroundColor = "white";
	}
}

function setText(o)
{
	document.applets["signs"].activateText(o);
}

function clearApp()
{
	if(confirm("Do you want to clear the applet?")){
		document.applets["signs"].clearIt();
		getAppValue();
	}
}
function undoApp()
{
	var coords = document.applets["signs"].unDoIt();
	document.getElementById("elem_signs").value=coords;
}
function redoApp()
{
	var coords = document.applets["signs"].reDoIt();
	document.getElementById("elem_signs").value=coords;
}

function getAppValue()
{
	var coords = "";
	if(document.applets["signs"] && typeof(document.applets["signs"].getDrawing) != 'undefined') {
		coords = document.applets["signs"].getDrawing();
		document.getElementById("elem_signs").value=coords;
	}
	$("#div_cnvs_anes").trigger("mouseout");
} 
//Applet



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
		
		if(delSign) {
			document.getElementById(TDUserNameId).style.display = 'block';
			document.getElementById(TDUserSignatureId).style.display = 'none';
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
		var signAnesthesiaIdBackColor1 = '<?php echo $signAnesthesiaIdBackColor;?>';
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
		url=url+"&preColor="+preColor1
		url=url+"&signAnesthesiaIdBackColor="+signAnesthesiaIdBackColor1
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

</script>
<?php
if($blEnableHTMLGrid == true){
	?>
	<script src="sc-grid/fabric.min.js"></script>
	<script src="sc-grid/fabric.more.js"></script>
	<script src="sc-grid/grid_anes.js"></script>
    <?php
}
?>
<div id="post" style="display:none;position:absolute;"></div>
<?php
// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
// GETTING FINALIZE STATUS
//show all epost ravi
?>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<?php
//START CODE TO GET USED CONSENT FORM
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
$rsGetSignedConsentFormName = imw_query($queryGetSignedConsentFormName);
$totalRowsGetSignedConsentFormName = imw_num_rows($rsGetSignedConsentFormName);
//END CODE TO GET USED CONSENT FORM

//Getting EKG & H&P
$ekgHpLink = "";
//START
$anesEkgHpCntr=0; 
$anesConsentEkgHpArr = array('EKG', 'H&P', 'Consent', 'Sx Planning Sheet');
foreach($anesConsentEkgHpArr as $anesConsentEkgHpName) {
	unset($conditionArr);
	$conditionArr['confirmation_id'] = $pConfId;
	$conditionArr['document_name'] = $anesConsentEkgHpName;	
	if($anesConsentEkgHpName == 'Sx Planning Sheet') {
		$conditionArr['document_name'] = 'Clinical';			
	}
	$getImagesAConsentEkgHpDetails = $objManageData->getMultiChkArrayRecords('scan_documents', $conditionArr);
	if(count($getImagesAConsentEkgHpDetails)>0){
		foreach($getImagesAConsentEkgHpDetails as $getImagesAConsentEkgHp){
			$anesEkgHp_document_id = $getImagesAConsentEkgHp->document_id;
			$anesEkgHp_document_name = $getImagesAConsentEkgHp->document_name;
			
			//SHOW FOLDER OR NOT 
			unset($conditionArr);
			$conditionArr['confirmation_id'] = $pConfId;
			$conditionArr['document_id'] = $anesEkgHp_document_id;													
			$extraCondGenAnes = "";
			if($anesConsentEkgHpName == 'Sx Planning Sheet') {
				$extraCondGenAnes = " AND pdfFilePath LIKE '%Sx_Planing_Sheet_%' ";
			}
			$getImagesAConsentUploadDetails = $objManageData->getMultiChkArrayRecords('scan_upload_tbl', $conditionArr,'','',$extraCondGenAnes);
			if(count($getImagesAConsentUploadDetails)>0){
				foreach($getImagesAConsentUploadDetails as $getImagesAConsentUpload){
					$anesEkgHpCntr++;
					$anesEkgHp_scan_upload_id = $getImagesAConsentUpload->scan_upload_id;
					$anesEkgHp_image_type = $getImagesAConsentUpload->image_type;
					$pdfFilePath = $getImagesAConsentUpload->pdfFilePath;
					if($anesEkgHp_image_type=='application/pdf') $anesEkgHp_image_type = 'pdf';
					//if($anesConsentEkgHpName=='Anesthesia Consent') {$anesConsentEkgHpName='A.Consent';  }
					$anesEkgHp_imageTypeNew = "\'".$anesEkgHp_image_type."\'";
					
					
					$anesEkgHp_scanUploadId = "\'".$anesEkgHp_scan_upload_id."\'";
					$pdfFilePathNew = "\'".$pdfFilePath."\'";
					$anesConsentEkgHp_NameNew = $anesConsentEkgHpName;
					$anesConsentEkgHp_NameNew = str_ireplace('H&P','H&amp;P',$anesConsentEkgHp_NameNew);
					$anesConsentEkgHp_NameNew = str_ireplace('Sx Planning Sheet','SxP',$anesConsentEkgHp_NameNew);
					$ekgHpLink.='<a href="#" class="btn-sm" onclick="return top.openImage('.$anesEkgHp_scanUploadId.','.$anesEkgHp_imageTypeNew.','.$pdfFilePathNew.');">'.$anesConsentEkgHp_NameNew.'</a>&nbsp;';
					if($anesEkgHpCntr==10) { $ekgHpLink.='<br>'; }
				}
				
			}
		
		}
	}
}


$scanFolderDetailLists = $objManageData->getArrayRecords('scan_documents', 'confirmation_id', $pConfId, 'document_id');
$ekgHpCntr=0;
if(count($scanFolderDetailLists)>0){
	//echo 'd'; 
	foreach($scanFolderDetailLists as $FolderList){	
		//echo 'd';			
		$document_id = $FolderList->document_id;
		$document_name = $FolderList->document_name;
		//SHOW FOLDER OR NOT 
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['document_id'] = $document_id;													
		$getImagesOrNotDetails = $objManageData->getMultiChkArrayRecords('scan_upload_tbl', $conditionArr);
		
		if(count($getImagesOrNotDetails)>0){
		//echo 'd';
			foreach($getImagesOrNotDetails as $imageDetails){
				$imageName = $imageDetails->document_name;
				$image_type = $imageDetails->image_type;
				$pdfFilePath = $imageDetails->pdfFilePath;
				$parent_sub_doc_id = $imageDetails->parent_sub_doc_id;
				if($parent_sub_doc_id!=0) continue;
				if($image_type=='application/pdf') $image_type = 'pdf';
				$scan_upload_id = $imageDetails->scan_upload_id;
				$lenOfName = strlen($imageName);
				/*if($lenOfName>15){
					$imageName = substr($imageName, 0, 16).'..';
				}*/	
				
				if(strstr(urldecode(strtoupper($imageName)),'EKG')==true || strstr(urldecode(strtoupper($imageName)),'H&P')==true){																				
					$ekgHpCntr++;
					
					if(strstr(urldecode(strtoupper($imageName)),'EKG')==true){
						$fileName = 'EKG';
					}
					elseif(strstr(urldecode(strtoupper($imageName)),'H&P')==true){
						$fileName = 'H&P';
					}
					
					$imageTypeNew = "\'".$image_type."\'";
					//if($imageTypeNew=='application/pdf') { $imageTypeNew='pdf'; }
					$scanUploadId = "\'".$scan_upload_id."\'";
					$pdfFilePathNew = "\'".$pdfFilePath."\'";
					$anesConsentEkgHpNameNew = $fileName;
					$anesConsentEkgHpNameNew = str_ireplace('H&P','H&amp;P',$anesConsentEkgHpNameNew);
					$ekgHpLink.='<a href="#" class="btn-sm" onclick="return top.openImage('.$scanUploadId.','.$imageTypeNew.','.$pdfFilePathNew.');">'.$anesConsentEkgHpNameNew.'</a>&nbsp;';
					if($ekgHpCntr==10) { $ekgHpLink.='<br>'; }
				}									
			}
		}
	}
}
//END


//START COMMON CODE FOR ANESTHESIOLOGIST THREE SIGNATURE
$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 

$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
$loggedInUserType = $ViewUserNameRow["user_type"];
$loggedInSignatureOfUser = $ViewUserNameRow["signature"];
$logInUserSubType = $ViewUserNameRow["user_sub_type"];

if($loggedInUserType<>"Anesthesiologist") {
	$loginUserName = $_SESSION['loginUserName'];
	$callJavaFun = "return noAuthorityFunCommon('Anesthesiologist');";
	$callJavaFunBeforeIntra = "return noAuthorityFunCommon('Anesthesiologist');";
}else {
	$loginUserId = $_SESSION["loginUserId"];
	$callJavaFun = "document.frm_gen_anes_rec.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','gen_anes_rec_ajaxSign.php','$loginUserId','Anesthesia1');";
	$callJavaFunBeforeIntra = "document.frm_gen_anes_rec.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','gen_anes_rec_ajaxSign.php','$loginUserId','Anesthesia2');";
	
}
//END COMMON CODE FOR ANESTHESIOLOGIST THREE SIGNATURE


?>
<div class="main_wrapper">

<form name="frm_gen_anes_rec" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
	<input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
	<input type="hidden" name="selected_frame_name"	id="selected_frame_name_id"	value="">
	<input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">			
	<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
	<input type="hidden" name="saveRecord" id="saveRecord" value="true">
	<input type="hidden" name="anesthesiologistId" id="anesthesiologistId" value="<?php echo $anesthesiologist_id;?>">
	<input type="hidden" name="getText"	id="getText">
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
	<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>" >
	<input type="hidden" name="frmAction" id="frmAction" value="gen_anes_rec.php">	
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">	
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
	<input type="hidden" name="hiddResetStatusId" id="hiddResetStatusId">
	<input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">

	<div class="slider_content scheduler_table_Complete" id=""  onDblClick="closePreDefineDiv();closeCal2();" onClick="calCloseFun();preCloseFun('evaluationPreDefineDiv');preCloseFun('evaluationPreDefineMedDiv');preCloseFun('evaluationEvaluationDiv');">
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
            		$epost_table_name = "genanesthesiarecord";
                    include("./epost_list.php");
          	?>
           	<!--<div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_anesth">
                <span class="bg_span_anesth">General Anesthesia Record </span>
                
                
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
                
            </div>-->
        
    		<div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
                        <?php 
							$bgCol = $bgdark_blue_local_anes;
							$borderCol = $tablebg_local_anes;
                            include('saveDivPopUp.php'); 
                        ?>
     		</div>
        <?php
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
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_yes','chbx_rbl'),changeChbxColor('chbx_rbl'),disp(document.frm_gen_anes_rec.chbx_rbl,'rblno_of_units');" <?php if($riskBloodLoss=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_rbl" id="chbx_rbl_yes" />
                                    	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($riskBloodLoss) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_no','chbx_rbl'),changeChbxColor('chbx_rbl'),disp_none(document.frm_gen_anes_rec.chbx_rbl,'rblno_of_units');" <?php if($riskBloodLoss=='No') echo "CHECKED"; ?> value="No" name="chbx_rbl" id="chbx_rbl_no" />
                                     	</span>
                                   	</div>
                                    
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                    	<span class="colorChkBx" style=" <?php if($riskBloodLoss) { echo $whiteBckGroundColor;}?>" >
                                      	<input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_na','chbx_rbl'),changeChbxColor('chbx_rbl'),disp_none(document.frm_gen_anes_rec.chbx_rbl,'rblno_of_units');" <?php if(stripslashes($riskBloodLoss)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_rbl" id="chbx_rbl_na" />
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
									$anesthesia2SignOnFileStatus = "Yes";
									$TDanesthesia2NameIdDisplay = "block";
									$TDanesthesia2SignatureIdDisplay = "none";
									$Anesthesia2Name = $loggedInUserName;
									$Anesthesia2SubType = $logInUserSubType;
									$Anesthesia2PreFix = 'Dr.';
									//$signAnesthesia2DateTimeFormatNew = date("m-d-Y h:i A");
									$signAnesthesia2DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
									//$signAnesthesia2DateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
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
											$callJavaFunBeforeIntraDel = "document.frm_gen_anes_rec.hiddSignatureId.value='TDanesthesia2NameId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','gen_anes_rec_ajaxSign.php','$loginUserId','Anesthesia2','delSign');";
										}else {
											$callJavaFunBeforeIntraDel = "alert('Only $Anesthesia2PreFix $Anesthesia2Name can remove this signature');";
										}
									//END CODE TO REMOVE ANES 4 SIGNATURE	
									
								//END CODE RELATED TO ANES 4 SIGNATURE ON FILE								  
								  
                                ?>
															<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 ">
                              <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 ">                                
                                <div class="inner_safety_wrap" id="TDanesthesia2NameId" style="display:<?php echo $TDanesthesia2NameIdDisplay;?>;">
                                    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signAnesthesiaIdBackColor;?>;" onClick="javascript:<?php echo $callJavaFunBeforeIntra;?>"> Anesthesia Provider Signature </a>
                                </div>
                                <div class="inner_safety_wrap collapse" id="TDanesthesia2SignatureId" style="display:<?php echo $TDanesthesia2SignatureIdDisplay;?>;">
                                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunBeforeIntraDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$Anesthesia2PreFix." ".$Anesthesia2Name; ?>  </a></span>	     
                                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $anesthesia2SignOnFileStatus;?></span>
                                    <span class="rob full_width"> <b> Signature Date</b> <?php echo $signAnesthesia2DateTimeFormatNew;?></span>
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
				
        
        
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
            
                    <div class="panel panel-default bg_panel_anesth">
                            
                            <div id="p_check_in" class="panel-body ">
                            
                                    <div class="row">
                                            
                                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                    
                                                        <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                                
                                                                <div class="row">
                                                                            
                                                                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                                    <label for="first_check"> 
                                                                                            <span class="colorChkBx" style=" <?php echo $whiteBckGroundColor;?>" >
                                                                                                <input type="checkbox" value="Yes" id="chbx_patientVerified_id" name="chbx_patientVerified"  <?php if($patientVerified=="Yes") { echo "checked"; } ?>  tabindex="7" />
                                                                                            </span>
                                                                                            &nbsp;Patient <span style="color:#0033CC">Verified </span>
                                                                                    </label> &nbsp;
                                                                                    <label for="">  Patient Height  &nbsp; <span style="color:#0033CC"><?php echo $patientHeight;?></span></label> &nbsp;
                                                                                    <label for="">  Patient Weight &nbsp; <span style="color:#0033CC"><?php echo $patientWeight;?></span></label> &nbsp;
                                                                                    <label for="">  BMI:</label> &nbsp;
                                                                                    <input type="text" class="form-control inline_form_text2" name="txt_BMIvalue" id="BMIvalueId" value="<?php echo $BMIvalue;?>" />
                                                                            </div>
                                                                            
                                                                </div>	 
                                                           
                                                        </div>
                                                        
                                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                <div class="clearfix margin_adjustment_only border-dashed"></div>
                                                        </div>
                                                        
                                                        <div class="full_width">
                                                                
                                                                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                                        <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                                                <div class="full_width">
                                                                                        <div class="row">
                                                                                                <div class="col-md-3 col-sm-12 col-xs-3 col-lg-3">
                                                                                                        <label> Procedure   <span style="color:#0033CC"> Verified </span>  </label>
                                                                                                </div>
                                                                                                
                                                                                                <div class="clearfix visible-sm margin_adjustment_only"></div>
                                                                                                
                                                                                                <div class="col-md-9 col-sm-12 col-xs-9 col-lg-9  text-center">
                                                                                                
                                                                                                    <div class="well well-sm f_size margin_bottom_0">
                                                                                                        <?php echo $patient_primary_procedure;?>
                                                                                                    </div>
                                                                                                
                                                                                                </div> <!-- Col-3 ends  -->
                                                                                        
                                                                                        </div>
                                                                                
                                                                                </div>
                                                                        
                                                                        </div>
                                                                
                                                                </div>
                                                                
                                                                <div class="clearfix visible-sm margin_small_only"></div>
                                                                
                                                                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                                        
                                                                        <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                                                
                                                                                <div class="full_width">
                                                                                
                                                                                        <div class="row">
                                                                                                
                                                                                                <div class="col-md-3 col-sm-12 col-xs-3 col-lg-3">
                                                                                                        <label> Secondary    Verified   </label>
                                                                                                </div>
                                                                                                
                                                                                                <div class="clearfix visible-sm margin_adjustment_only"></div>
                                                                                                
                                                                                                <div class="col-md-9 col-sm-12 col-xs-9 col-lg-9  text-center">
                                                                                                        
                                                                                                        <div class="well well-sm f_size margin_bottom_0">&nbsp;
                                                                                                                <?php echo $patient_secondary_procedure;?>
                                                                                                        </div>
                                                                                                        
                                                                                                </div> <!-- Col-3 ends  -->
                                                                                                
                                                                                        </div>
                                                                                
                                                                                </div>
                                                                                    
                                                                        </div>
                                                                        
                                                                </div>
                                                                
                                                        </div>
                                                        
                                            </div>
                                            <!--   @2nd col Ends     -->
                                            
                                    </div>
                            
                            </div>          
                    
                    </div> 
                    
            </div>
        
        
        	<div class="clearfix margin_adjustment_only"></div>
        	
            
             
        	<div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
        	
        			<div class="panel panel-default bg_panel_anesth">
                    		
                            <div class="panel-heading" onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', '10', parseInt($(this).offset().left)+15, parseInt( $(this).offset().top -$(document).scrollTop() - 132 )),document.getElementById('selected_frame_name_id').value='iframe_allergies_gen_anes_rec';">
                            		<a data-placement="top" class="panel-title rob alle_link show-pop-trigger2 btn btn-default "> <span class="fa fa-caret-right"></span>  Allergies   </a>
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
                                                                        
                                                                        <div class="table_slider">          
                                                                       			<?php  
																							$allgNameWidth			=		"228";
																							$allgReactionWidth		=		"228";
																							include("health_quest_spreadsheet.php");
																				?> 			
                                                                         </div>                
                      											
                                                                </div>
                                                                <!--- ---- Table --->
                                                                
                                                     	</div>
                                                       	<!-- Col-3 ends  -->
                                                        
                                               	</div>	 
                                                
                                 	</div>
                          	
                            </div>
                            <!-- Panel Body -->
                            
              		</div>
                    
   			</div>
                                           
          	
            <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
            		
                    	<div class="panel panel-default bg_panel_anesth">
                        
                        			<div class="panel-heading">
                                    		<a class="panel-title rob alle_link show-pop-trigger2 btn btn-default" onClick="return showPreDefineMedFnNew('medication_name', 'medication_detail', '10', ($(this).offset().left)+10, parseInt($(this).offset().top -$(document).scrollTop()+58) ),document.getElementById('selected_frame_name_id').value='iframe_medication_gen_anes_rec';"> <span class="fa fa-caret-right"></span>  Medication    </a>                   					
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
                                                                            		<?php  
                                                          										$medicNameWidth			=		210;
																							 	$medicDetailWidth			=		210;
																								include("patient_anesthesia_medi_spreadsheet.php");	
																					?>
                                                                           	</div>
                                                                            
                      											  	</div>
                                                                    <!--- ---- Table --->
															</div>
                                                            
                                                    		<!-- Col-3 ends  -->
                                                 	</div>	 
                                                    
                                       		</div>
                                            
                                 	</div> 
                                    
                                    <!-- Panel Body -->
                       	
                        </div>
                        
        	</div>
            
            
            
            
            <div class="clearfix margin_adjustment_only"></div>
            <?php
						$chbxReassEquipBackColor=$chngBckGroundColor;
						if($PatientReassessed || $MachineEquipment) { 
							$chbxReassEquipBackColor=$whiteBckGroundColor; 
						}
			?>
            <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
            
            		<div class="panel panel-default bg_panel_anesth">
                    		
                            <div class="panel-body">
                            		
                                    <div class="full_width wrap_right_inner_anesth">
                                    		
                                            	<label class="f_size" for="chbx_PatientReassessed_id">
                                                		<span class="colorChkBx" onClick="changeDiffChbxColor(2,'chbx_PatientReassessed_id','chbx_MachineEquipment_id');" style=" <?php echo $chbxReassEquipBackColor;?>"><input type="checkbox" value="Yes" id="chbx_PatientReassessed_id" name="chbx_PatientReassessed"  <?php if($PatientReassessed=="Yes") { echo "checked"; } ?>  tabindex="7" />
                                                        </span>&nbsp;
                                                         Patient Reassessed
                                               	</label> &nbsp;
                                                
                                                <label class="f_size" for="chbx_MachineEquipment_id">
                                                		<span class="colorChkBx" onClick="changeDiffChbxColor(2,'chbx_PatientReassessed_id','chbx_MachineEquipment_id');" style=" <?php echo $chbxReassEquipBackColor;?>"><input type="checkbox" value="Yes" id="chbx_MachineEquipment_id" name="chbx_MachineEquipment"  <?php if($MachineEquipment=="Yes") { echo "checked"; } ?>  tabindex="7" />
                                                        </span>&nbsp;
                                                        Machine & Equipment Completed
                                            	</label> &nbsp;
                                                
                                                <label class="f_size"> <b> Anesthesia Class: </b></label> &nbsp;
                                                <label class="f_size" for="chbx_gen_anes_class_1_id"  onClick="javascript:checkSingle('chbx_gen_anes_class_1_id','chbx_gen_anes_class')"> 
                                                		<span class="colorChkBx" onClick="changeChbxColor('chbx_gen_anes_class')" style=" <?php if($anesthesiaClass) { echo $whiteBckGroundColor;}?>"><input type="checkbox" value="one" id="chbx_gen_anes_class_1_id" name="chbx_gen_anes_class" <?php if($anesthesiaClass=="one") { echo "checked"; }?>  tabindex="7" />
                                                       	</span>&nbsp;
                                                        I
                                              	</label> &nbsp;
                                                
                                                <label class="f_size" for="chbx_gen_anes_class_2_id" onClick="javascript:checkSingle('chbx_gen_anes_class_2_id','chbx_gen_anes_class')"> 
                                                		<span class="colorChkBx" onClick="changeChbxColor('chbx_gen_anes_class')" style=" <?php if($anesthesiaClass) { echo $whiteBckGroundColor;}?>">
                                                        <input type="checkbox" value="two" id="chbx_gen_anes_class_2_id" name="chbx_gen_anes_class" <?php if($anesthesiaClass=="two") { echo "checked"; }?>  tabindex="7" />
                                                        </span>&nbsp;
                                                        II
                                              	</label> &nbsp;                                                        
                                                
                                                <label class="f_size for_abs_left" for="chbx_gen_anes_class_3_id" onClick="javascript:checkSingle('chbx_gen_anes_class_3_id','chbx_gen_anes_class')">
                                                		<span class="colorChkBx" onClick="changeChbxColor('chbx_gen_anes_class')" style=" <?php if($anesthesiaClass) { echo $whiteBckGroundColor;}?>">
                                                        	<input type="checkbox" value="three" id="chbx_gen_anes_class_3_id" name="chbx_gen_anes_class" <?php if($anesthesiaClass=="three") { echo "checked"; }?>  tabindex="7" >
                                                      	</span>&nbsp;
                                                		III 
                                                        
                                              	</label> &nbsp;
                                 	
                                    </div>
                                    
                        	</div>
                            
               		</div>
                    
     		</div>
            
            <div class="clearfix margin_adjustment_only"></div>
            
            
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
            <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
            
            		<div class="panel panel-default bg_panel_anesth">
                    		
                            <div class="panel-body">
                            		
                                    <div class="full_width wrap_right_inner_anesth">
                                    	
                                        <div id="intervalIdGeneralAnes"  class="col-lg-11 col-md-11 col-sm-11 col-xs-10"style="display:none; ">
                                        	<ul class="intervals">
                                            	<li><?php echo $intervalTimeMin1;?></li>
                                                <li><?php echo $intervalTimeMin2;?></li>
                                            	<li><?php echo $intervalTimeMin3;?></li>
                                                <li><?php echo $intervalTimeMin4;?></li>
                                                <li><?php echo $intervalTimeMin5;?></li>
                                                <li><?php echo $intervalTimeMin6;?></li>
                                                <li><?php echo $intervalTimeMin7;?></li>
                                                <li><?php echo $intervalTimeMin8;?></li>
                                                <li><?php echo $intervalTimeMin9;?></li>
                                                <li><?php echo $intervalTimeMin10;?></li>
                                          	</ul>      
										</div>
										
										<div style="float:right" class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                                        		
                                                <a href="javascript:void(0)" onClick="javascript:if(!document.frm_gen_anes_rec.startTime.value) { alert('Please select start time'); } else { showIntervalMain(document.frm_gen_anes_rec.startTime.value,'intervalIdGeneralAnes'); }" style="float:right;">
                                                	<span class="abs_arrow_left"> <i class="fa fa-arrow-left"></i> </span>
                                             	</a>
                                      	</div>
                                    	
                                        
                                    </div>
                                    
                        	</div>
                            
               		</div>
                    
     		</div>
            
            <div class="clearfix margin_adjustment_only"></div>
           
            
           <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
            
            		<div class="row">
                    		
                            <div class="col-md-8 col-sm-12 col-xs-8 col-lg-8">   
                            
                            		<div class="row">
                                    
                                    		<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                            
                                            		<div class="inner_left_slider_anesth">
                                                    
                                                    		<div class="row">
                                                            
                                                            			<label class="full_width padding_15"> Start Time  </label>
                                                                    	
                                                                    	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                        		<div class="input-group">
                                                                                		<input onFocus="changeTxtGroupColor(1,'bp_temp5');"  type="text" id="bp_temp5" name="startTime" class="form-control" style=" <?php if(trim(!$startTime)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?> " onKeyUp="displayText5=this.value;changeTxtGroupColor(1,'bp_temp5');" onClick="getShow(70,50,'flag5');top.frames[0].setPNotesHeight();<?php if($startTime=="") {?>clearVal_c();displayTime('bp_temp5');changeDiffChbxColor(1,'bp_temp5'); <?php } ?>"   tabindex="1" value="<?php echo $startTime;?>"  />
                                                                                        <div class="input-group-addon" onClick="displayTime('bp_temp5');changeDiffChbxColor(1,'bp_temp5');">
                                                                                        	<span class="fa fa-play"  >   </span>
                                                                                      	</div>
                                                                               	</div>
                                                                     	</div>	
                                                    		</div>
                                                            
                                                            <div class="clearfix"></div>	
                                                            
                                                            <div class="row">
                                                            		
                                                                    	<label class="full_width padding_15"> Stop Time  </label>
                                                                        
                                                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                        		
                                                                                <div class="input-group">
                                                                                	
                                                                                    <input onFocus="changeTxtGroupColor(1,'bp_temp6');" type="text" id="bp_temp6" name="stopTime" class="form-control" style=" <?php if(trim(!$stopTime)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?> " onKeyUp="displayText6=this.value;changeTxtGroupColor(1,'bp_temp6');" onClick="getShow(80,50,'flag6');top.frames[0].setPNotesHeight();<?php if($startTime=="") {?>clearVal_c();displayTime('bp_temp6');changeDiffChbxColor(1,'bp_temp6'); <?php } ?>"   tabindex="1" value="<?php echo $stopTime;?>"  />
                                                                                    
                                                                                    <div class="input-group-addon" onClick="displayTime('bp_temp6');changeDiffChbxColor(1,'bp_temp6');">
                                                                                    	<span class="fa fa-stop"  ></span>
                                                                                  	</div>
                                                                               
                                                                               	</div>	
                                                                       	
                                                                        </div>	
                                                                        
                                                         	</div>
                                                            
                                                            <div class="clearfix"></div>	
                                                            
                                                            <div class="row">
                                                            		
                                                                    <?php
																			
																			$display_macSubId	=	($mac == "mac") 	?	true	:	false;
																			
																	?>			
                                                                    <label class="padding_15 f_size full_width" onClick="javascript:checkSingle('gen_anes_mac_id','gen_anes_mac'),disp_one_hide_other('gen_anes_mac_subid','gen_anes_miller_subid')"  >
                                                                    		<input type="checkbox" value="mac" id="gen_anes_mac_id" name="gen_anes_mac" <?php if($mac=="mac") { echo "checked"; }?>  tabindex="7" />
                                                                            &nbsp;MAC 
                                                                  	</label>
                                                                    
                                                  					<label class="full_width padding_15 " style=" display:<?=($display_macSubId ?  'block' : 'none')?>;" id="gen_anes_mac_subid">
                                                                    		
                                                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('gen_anes_submac_1_id','gen_anes_submac')">
                                                                            		<input type="checkbox" value="one" id="gen_anes_submac_1_id" name="gen_anes_submac" <?php if($macValue=="one") { echo "checked"; }?>  tabindex="7" />
                                                                                    &nbsp;1
                                                                          	</label>
                                                                        	<label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('gen_anes_submac_2_id','gen_anes_submac')">
                                                                            		<input type="checkbox" value="two" id="gen_anes_submac_2_id" name="gen_anes_submac" <?php if($macValue=="two") { echo "checked"; }?>  tabindex="7" />
                                                                                    &nbsp;  2 
                                                                           	</label>
                                                                        	<label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('gen_anes_submac_3_id','gen_anes_submac')">
                                                                            		<input type="checkbox" value="three" id="gen_anes_submac_3_id" name="gen_anes_submac" <?php if($macValue=="three" || $macValue=="") { echo "checked"; }?>  tabindex="7" />
                                                                                    &nbsp;3
                                                                          	</label>
                                                                            
                                                                        	<label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('gen_anes_submac_4_id','gen_anes_submac')">
                                                                            		<input class="field"   type="checkbox" value="four" id="gen_anes_submac_4_id" name="gen_anes_submac" <?php if($macValue=="four") { echo "checked"; }?>  tabindex="7" />
                                                                                    &nbsp; 4
                                                                          	</label>
                                                                  	
                                                                    </label>
                                                                    
                                                                  	
                                                                    <label class="f_size full_width padding_15" onClick="javascript:checkSingle('gen_anes_miller_id','gen_anes_mac'),disp_one_hide_other('gen_anes_miller_subid','gen_anes_mac_subid')">
                                                                    		<input type="checkbox" value="miller" id="gen_anes_miller_id" name="gen_anes_mac" <?php if($millar=="millar") { echo "checked"; }?>  tabindex="7" />
                                                                            &nbsp;Miller
                                                                   	</label>
                                                                	   
																	<?php 		$display_millarSubId		=		($millar=="millar")	?	'inline-block' 	:		'none';		?>
																			
																				
                                                                    <label class="full_width padding_15 " id="gen_anes_miller_subid" style="display:<?php echo $display_millarSubId;?>;" >
                                                                    	
                                                                        <div class="full_width">
                                                                        		
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('gen_anes_miller_0_id','gen_anes_miller')">
                                                                                		<input type="checkbox" value="zero" id="gen_anes_miller_0_id" name="gen_anes_miller" <?php if($millarValue=="zero") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;0
                                                                              	</label>
                                                                                
                                                                        		<label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('gen_anes_miller_1_id','gen_anes_miller')">
                                                                                		<input type="checkbox" value="one" id="gen_anes_miller_1_id" name="gen_anes_miller" <?php if($millarValue=="one") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;1
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('gen_anes_miller_2_id','gen_anes_miller')">
                                                                                		<input type="checkbox" value="two" id="gen_anes_miller_2_id" name="gen_anes_miller" <?php if($millarValue=="two" || $millarValue=="") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;2
                                                                               	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('gen_anes_miller_3_id','gen_anes_miller')">
                                                                                		<input type="checkbox" value="three" id="gen_anes_miller_3_id" name="gen_anes_miller" <?php if($millarValue=="three") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;3
                                                                             	</label>
                                                                                
                                                                    	</div>	
                                                                        
                                                                        <div class="clearfix"></div>
                                                                        
                                                                 	</label>     
                                                                    
                                                                    
                                                                    
                                                                    <?php
																				if($etTube=="etTube")
																				{
																						$display_etTubeSubId		=	"inline-block";
																						$display_etTubemainId	=	"none";
																				}
																				else
																				{ 
																						$display_etTubeSubId		=	"none"; 
																						$display_etTubemainId	=	"inline-block";
																				}
																	?>
                                                                    
                                                                    <label class="f_size full_width padding_15" onClick="javascript:checkSingle('chbx_et_lm_msk_1_id','chbx_et_lm_msk'),disp_one_hide_other('et_tube_size_id','lma_size_id'),disp_one_hide_other('lma_id','et_id')">
                                                                    		<span class="colorChkBx" onClick="changeChbxColor('chbx_et_lm_msk')" style=" <?php if($etTube || $lma || $mask) { echo $whiteBckGroundColor;}?>" >
                                                                            	<input type="checkbox" value="etTube" id="chbx_et_lm_msk_1_id" name="chbx_et_lm_msk" <?php if($etTube=="etTube") { echo "checked"; }?>  tabindex="7" />
                                                                           	</span>
                                                                            &nbsp;<span style="display:<?php echo $display_etTubemainId;?>;" id="et_id">ET Tube</span>
                                                                	</label>
                                                                    
                                                                    <label class="full_width padding_15 " id="et_tube_size_id" style=" display:<?php echo $display_etTubeSubId;?>; white-space:nowrap;">
                                                                    		<span>ET Tube Size</span>
                                                                            
                                                                            <div class="full_width">
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_1_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="one" id="et_tube_1_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="one") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;1 
                                                                             	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_1_5_id','chbx_et_tubeSize')"> 
                                                                                		<input type="checkbox" value="oneHalf" id="et_tube_1_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="oneHalf") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;1.5
                                                                              	</label>
                                                                                        
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_2_id','chbx_et_tubeSize')">
                                                                                		<input  type="checkbox" value="two" id="et_tube_2_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="two") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;2
                                                                                </label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_2_5_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="twoHalf" id="et_tube_2_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="twoHalf") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;2.5
                                                                           		</label>
                                                                                
                                                                           </div>	
                                                                           	
                                                                            <div class="clearfix"></div>
                                                                            
                                                                            <div class="full_width">
                                                                            	
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_3_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="three" id="et_tube_3_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="three" || $etTubeSize=="") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;3
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_3_5_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="threeHalf" id="et_tube_3_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="threeHalf") { echo "checked"; }?>  tabindex="7" /> 
                                                                                        &nbsp;3.5
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_4_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="four" id="et_tube_4_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="four") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;4
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_4_5_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="fourHalf" id="et_tube_4_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="fourHalf") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;4.5
                                                                            	</label>
                                                                                
                                                                           	</div>
                                                                            
                                                                            <div class="clearfix"></div>
                                                                            
                                                                            <div class="full_width">
                                                                            	
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_5_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="five" id="et_tube_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="five") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;5
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_5_5_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="fiveHalf" id="et_tube_5_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="fiveHalf") { echo "checked"; }?>  tabindex="7"  /> 
                                                                                        &nbsp;5.5
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_6_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="six" id="et_tube_6_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="six") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;6
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_6_5_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="sixHalf" id="et_tube_6_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="sixHalf") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;6.5
                                                                            	</label>
                                                                                
                                                                           	</div>
                                                                            
                                                                            <div class="clearfix"></div>
                                                                            
                                                                            <div class="full_width">
                                                                            	
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_7_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="seven" id="et_tube_7_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="seven") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;7
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_7_5_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="sevenHalf" id="et_tube_7_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="sevenHalf") { echo "checked"; }?>  tabindex="7" /> 
                                                                                        &nbsp;7.5
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_8_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="eight" id="et_tube_8_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="eight") { echo "checked"; }?>  tabindex="7"  />
                                                                                        &nbsp;8
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_8_5_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="eightHalf" id="et_tube_8_5_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="eightHalf") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;8.5
                                                                            	</label>
                                                                                
                                                                           	</div>
                                                                            
                                                                            <div class="clearfix"></div>
                                                                            
                                                                            <div class="full_width">
                                                                            	
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('et_tube_9_id','chbx_et_tubeSize')">
                                                                                		<input type="checkbox" value="nine" id="et_tube_9_id" name="chbx_et_tubeSize" <?php if($etTubeSize=="nine") { echo "checked"; }?>  tabindex="9"  />
                                                                                        &nbsp;7
                                                                              	</label>
                                                                                
                                                                        	</div>
                                                                            
                                                                   	</label>
                                                                    
                                                                    <?php
																				if($lma=="lma")
																				{
																						$display_lmaSubId		=		"inline-block";
																						$display_lmaMainId		=		"none";
																				}
																				else
																				{
																						$display_lmaSubId		=		"none"	;
																						$display_lmaMainId		=		"inline-block"; 
																				}
																	?>
                                                                    
                                                                    <label class="f_size full_width padding_15" onClick="javascript:checkSingle('chbx_et_lm_msk_2_id','chbx_et_lm_msk'),disp_one_hide_other('lma_size_id','et_tube_size_id'),disp_one_hide_other('et_id','lma_id')">
                                                                        		<span class="colorChkBx" onClick="changeChbxColor('chbx_et_lm_msk')" style=" <?php if($etTube || $lma || $mask) { echo $whiteBckGroundColor;}?>" >
                                                                                		<input type="checkbox" value="lma" <?php if($lma=="lma") { echo "checked"; }?> id="chbx_et_lm_msk_2_id" name="chbx_et_lm_msk"  tabindex="7" />
                                                                               	</span>
                                                                                &nbsp;<span style="display:<?php echo $display_lmaMainId;?>; " id="lma_id" >LMA</span>
                                                                 	</label>
                                                                    
                                                                    <label class="full_width padding_15 " id="lma_size_id" style="display:<?php echo $display_lmaSubId;?>; ">
                                                                    		<span>LMA Size</span>
                                                                            
                                                                            <div class="full_width">
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('lma_sub_1_5_id','lma_sub')">
                                                                                		<input type="checkbox" value="oneHalf" id="lma_sub_1_5_id" name="lma_sub" <?php if($lmaSize=="oneHalf" || $lmaSize=="") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;1.5
                                                                             	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('lma_sub_2_id','lma_sub')"> 
                                                                                		<input type="checkbox" value="two" id="lma_sub_2_id" name="lma_sub" <?php if($lmaSize=="two") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;2
                                                                              	</label>
                                                                                        
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('lma_sub_2_5_id','lma_sub')">
                                                                                		<input type="checkbox" value="twoHalf" id="lma_sub_2_5_id" name="lma_sub" <?php if($lmaSize=="twoHalf") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;2.5
                                                                                </label>
                                                                                
                                                                        	</div>	
                                                                           	
                                                                            <div class="clearfix"></div>
                                                                            
                                                                            <div class="full_width">
                                                                            	
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('lma_sub_3_id','lma_sub')" >
                                                                                		<input type="checkbox" value="three" id="lma_sub_3_id" name="lma_sub" <?php if($lmaSize=="three") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;3
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('lma_sub_4_id','lma_sub')">
                                                                                		<input type="checkbox" value="four" id="lma_sub_4_id" name="lma_sub" <?php if($lmaSize=="four") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;4
                                                                              	</label>
                                                                                
                                                                                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3" onClick="javascript:checkSingle('lma_sub_5_id','lma_sub')">
                                                                                		<input type="checkbox" value="five" id="lma_sub_5_id" name="lma_sub" <?php if($lmaSize=="five") { echo "checked"; }?>  tabindex="7" />
                                                                                        &nbsp;4
                                                                            	</label>
                                                                                
                                                                           	</div>
                                                                            
                                                                  	</label>
                                                                    
                                                                        
                                                                        
                                                                 	<label class="f_size full_width padding_15" onClick="javascript:checkSingle('chbx_et_lm_msk_3_id','chbx_et_lm_msk'),all_hide('et_tube_size_id','lma_size_id'),all_disp('et_id','lma_id')">
                                               								<span class="colorChkBx" onClick="changeChbxColor('chbx_et_lm_msk')" style=" <?php if($etTube || $lma || $mask) { echo $whiteBckGroundColor;}?>" >
                                                                            	<input type="checkbox" value="mask" id="chbx_et_lm_msk_3_id" name="chbx_et_lm_msk" <?php if($mask=="mask") { echo "checked"; }?>  tabindex="7" />
                                                                           	</span>&nbsp;Mask
                                                                 	</label>
                                                                    
                                                                    <label class="f_size full_width padding_15"> 
                                                                    		<span class="colorChkBx" style=" <?php echo $whiteBckGroundColor;?>" >
                                                                            	<input type="checkbox" value="Yes" id="chbx_teethUnchanged_id" name="chbx_teethUnchanged" <?php if($teethUnchanged=="Yes") { echo "checked"; } ?>  tabindex="7" />
                                                                          	</span>&nbsp;Teeth Unch
                                                                  	</label>
                                                                    
                                                                    <label class="full_width padding_15"> Monitor  </label>
                                                                    
                                                                    <?php
																			$chbxMonitorEkgBackColor		=		$chngBckGroundColor;
																			if($Monitor_ekg || $Monitor_etco2 || $Monitor_etco2Sat || $Monitor_o2Temp || $Monitor_PNS) { 
																					$chbxMonitorEkgBackColor=$whiteBckGroundColor; 
																			}
																	?>
                                                                  	
                                                                    <label class="f_size full_width padding_15">
                                                                    		<span class="colorChkBx" onClick="changeDiffChbxColor(5,'chbx_ekg_id','chbx_etco2_id','chbx_etco2Sat_id','chbx_o2Temp_id','chbx_PNS_id');" style=" <?php echo $chbxMonitorEkgBackColor;?>" >
                                                                                		<input type="checkbox" value="ekg" id="chbx_ekg_id" name="chbx_ekg" <?php if($Monitor_ekg=="ekg") { echo "checked"; }?>  tabindex="7" />
                                                                          	</span>&nbsp;EKG
                                                                  	</label>
                                                                    
                                                                    <label class="f_size full_width padding_15">
                                                                       			<span class="colorChkBx" onClick="changeDiffChbxColor(5,'chbx_ekg_id','chbx_etco2_id','chbx_etco2Sat_id','chbx_o2Temp_id','chbx_PNS_id');" style=" <?php echo $chbxMonitorEkgBackColor;?>" >
                                                                                		<input type="checkbox" value="etco2" id="chbx_etco2_id" name="chbx_etco2" <?php if($Monitor_etco2=="etco2") { echo "checked"; }?>  tabindex="7" />
                                                                             	</span>&nbsp;ETCO2
                                                                   	</label>
                                                             			
                                                                  	<label class="f_size full_width padding_15">
                                                                       			<span class="colorChkBx" style=" <?php echo $chbxMonitorEkgBackColor;?>" onClick="changeDiffChbxColor(5,'chbx_ekg_id','chbx_etco2_id','chbx_etco2Sat_id','chbx_o2Temp_id','chbx_PNS_id');" >
                                                                                		<input type="checkbox" value="etco2Sat" id="chbx_etco2Sat_id" name="chbx_etco2Sat" <?php if($Monitor_etco2Sat=="etco2Sat") { echo "checked"; }?>  tabindex="7" />
                                                                             	</span>&nbsp;O2Sat
                                                                  	</label>
                                                                        
                                                                        
                                                                 	<label class="f_size full_width padding_15">
                                                                       			<span class="colorChkBx" onClick="changeDiffChbxColor(5,'chbx_ekg_id','chbx_etco2_id','chbx_etco2Sat_id','chbx_o2Temp_id','chbx_PNS_id');" style=" <?php echo $chbxMonitorEkgBackColor;?>" >
                                                                                		<input type="checkbox" value="o2Temp" id="chbx_o2Temp_id" name="chbx_o2Temp" <?php if($Monitor_o2Temp=="o2Temp") { echo "checked"; }?>  tabindex="7" >
                                                                              	</span>&nbsp;Temp
                                                                    </label>
                                                                        
                                                                  	<label class="f_size full_width padding_15">
                                                                        		<span class="colorChkBx" onClick="changeDiffChbxColor(5,'chbx_ekg_id','chbx_etco2_id','chbx_etco2Sat_id','chbx_o2Temp_id','chbx_PNS_id');" style=" <?php echo $chbxMonitorEkgBackColor;?>" >
                                                                                		<input type="checkbox" value="PNS" id="chbx_PNS_id" name="chbx_PNS" <?php if($Monitor_PNS=="PNS") { echo "checked"; }?>  tabindex="7" />
                                                                              	</span>&nbsp;PNS
                                                                    </label>
                                                                       
                                                           </div>
                                                                    
                                                 	</div>
                                                    
                                          	</div>	
                                                
                                           	<div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                                		<?php 
                                        							if($sect=="print_emr") 
																	{
																		$img_src="";
																		if(!empty($elem_cnvs_anes_drw_file)){
																			$updir = "admin";
																			$img_src = $updir."/".$elem_cnvs_anes_drw_file;
																			if(!file_exists($img_src)){$img_src="";}
																		}
																		
																		if(empty($img_src)){
																		include("imageSc/imgGd.php");
																		$imgName = 'bgGridBig.jpg'; $img_src = "html2pdf/tess.jpg";
																		drawOnImage2($genAnesSignApplet,$imgName,$img_src);
																		}
																			
																		echo '<table class="table_pad_bdr alignRight"  ><tr><td class="alignRight" style="padding-left:100px;"><img src="'.$img_src.'"  height="350"></td><tr><table>';
																	}
												else if($blEnableHTMLGrid == true){
													?>
													<div id="div_cnvs_anes">
														<canvas id="cnvs_anes" height="645" width="619" ></canvas>
														<input type="hidden" id="elem_cnvs_anes_drw" name="elem_cnvs_anes_drw" value="">
														<input type="hidden" id="elem_cnvs_anes_drw_coords" name="elem_cnvs_anes_drw_coords" value='<?php echo $elem_cnvs_anes_drw_coords; ?>'>
														<input type="hidden" id="elem_drw_icon_sel" name="elem_drw_icon_sel" value="">
													</div>
													<?php
												}					
																	else
																	{
														?><!--onMouseOut="getAppValue()"-->
                                                        					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                                                            		<object type="application/x-java-applet" width="100%" height="645" name="signs" id="signs">
                                                                                    <param name="code" value="Test2.class" />
                                                                                    <param name="codebase" value="common/applet/" />
                                                                                    <PARAM name="bgImg" value="images/icon/bgGridBig.jpg">
                                                                                    <param name="signs" value="<?php echo $genAnesSignApplet;?>">
                                                                                    <param name="iconSize" value="14">
                                                                                    <param name="txtActivate" value="inactive">
                                                                                    <param name="archive" value="signs.jar" />
                                                                                   	</object>
                                                                                    <input type="hidden" name="elem_signs" id="elem_signs" value="<?php echo $genAnesSignApplet;?>"	/>
                                                                         	</div>		
                                                        
                                                        
                                                        <?PHP
														
																	}
														
														?>
                                            </div>	
                                    		<?php if($vitalSignGridStatus) { ?>
                                        <div class="row col-md-12 col-sm-12 col-xs-12 col-lg-12">                       
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
                                                                <span  class="col-md-6 col-lg-6 col-sm-6 col-xs-6">Diastolic</span>
                                                              </span>
                                                          </div>
                                                          <div class=" col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                                                              <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">Pulse</span>
                                                              <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">RR</span>
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
                                                          $condArr['chartName']	=	'genral_anesthesia_form' ;
                                                          
                                                          $gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time','Asc');
                                                          $gCounter	=	1;
                                                          if(is_array($gridData) && count($gridData) > 0  )
                                                          {
                                                              foreach($gridData as $gridRow)
                                                              {	
                                                                  //$fieldId1 = 'vitalSignGrid_'.$gCounter.'_1' ;	$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
																  $fieldId1 = 'vitalSignGrid_'.$gCounter.'_1' ;	$fieldValue1=  $objManageData->getTmFormat($gridRow->start_time);
                                                                  $fieldId2 = 'vitalSignGrid_'.$gCounter.'_2' ;	$fieldValue2= $gridRow->systolic;
                                                                  $fieldId3 = 'vitalSignGrid_'.$gCounter.'_3' ;	$fieldValue3= $gridRow->diastolic;
                                                                  $fieldId4 = 'vitalSignGrid_'.$gCounter.'_4' ;	$fieldValue4= $gridRow->pulse;
                                                                  $fieldId5 = 'vitalSignGrid_'.$gCounter.'_5' ;	$fieldValue5= $gridRow->rr;
                                                                  $fieldId6 = 'vitalSignGrid_'.$gCounter.'_6' ;	$fieldValue6= $gridRow->temp;
                                                                  $fieldId7 = 'vitalSignGrid_'.$gCounter.'_7' ;	$fieldValue7= $gridRow->etco2;
                                                                  $fieldId8 = 'vitalSignGrid_'.$gCounter.'_8' ;	$fieldValue8= $gridRow->osat2;
                                                      ?>		
                                                                  <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                  
                                                                      <input type="hidden" name="vitalSignGridRecordId[]" value="<?=$gridRow->gridRowId?>"	/>
                                                                      <div class=" row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                                                                      
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
                                                                      <div class=" col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                                                                          <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
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
                                                                  <div class=" row col-md-6 col-sm-5 col-xs-5 col-lg-5">
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
                                                                  <div class=" col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                                                                      <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
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
                                                  </div>
                                          <div class="clearfix margin_adjustment_only"></div>
                                  			</div>
                                       	<?php } ?>
                                    
                        	</div>
                            
                            <div class="clearfix margin_adjustment_only visible-sm"></div>
                            
                            <div class="col-md-4 col-sm-12 col-xs-4 col-lg-4">
                            
                            		<div class="panel panel-default bg_panel_anesth">
                                    
                                    		<div class="panel-heading">
                                            
                                            		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                    
                                                    		<label for="" class="">  Arms </label>
                                                            
                                                  	</div>
                                           	</div>
                                            
                                            <?php
														$chbxArmsBackColor=$chngBckGroundColor;
														if($armsTuckedLeft || $armsTuckedRight || $armsArmboardsLeft || $armsArmboardsRight) { 
																$chbxArmsBackColor=$whiteBckGroundColor; 
														}
											?>
                                			
                                            <div class="panel-body">
                                            
                                            		<div class="wrap_right_inner_anesth">    
                                                    
                                                    		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            
                                                            		<div class="row">
                                                                    		
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		<label> Tucked </label>
                                                                            </div>
                                                                            
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		
                                                                                    <label for="chbx_armsTuckedLeft_id" class="">
                                                                                    	<span class="colorChkBx" onClick="changeDiffChbxColor(4,'chbx_armsTuckedLeft_id','chbx_armsTuckedRight_id','chbx_armsArmboardsLeft_id','chbx_armsArmboardsRight_id');" style=" <?php echo $chbxArmsBackColor;?>" >
                                                                                        	<input type="checkbox" value="left" id="chbx_armsTuckedLeft_id" name="chbx_armsTuckedLeft" <?php if($armsTuckedLeft=="left") { echo "checked"; } ?>  tabindex="7" >
                                                                                     	</span>&nbsp;L
                                                                                        
                                                                                	</label>
                                                                           	
                                                                            </div> 	
                                                                            
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		
                                                                            		<label for="chbx_armsTuckedRight_id" class="">
                                                                                    		<span class="colorChkBx" onClick="changeDiffChbxColor(4,'chbx_armsTuckedLeft_id','chbx_armsTuckedRight_id','chbx_armsArmboardsLeft_id','chbx_armsArmboardsRight_id');" style=" <?php echo $chbxArmsBackColor;?>" >
                                                                                            	<input type="checkbox" value="right" id="chbx_armsTuckedRight_id" name="chbx_armsTuckedRight" <?php if($armsTuckedRight=="right") { echo "checked"; } ?>  tabindex="7" />
                                                                                        	</span>&nbsp; R
                                                                                   	</label>
                                                                                    
                                                                           	</div>
                                                                            
                                                                  	
                                                                    </div>
                                                                    
                                                       		</div> 
                                                 	
                                                    </div>
                                                    
                                                    <div class="wrap_right_inner_anesth">    
                                                        
                                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                        
                                                            <div class="row">
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                    <label> Armboards </label>
                                                                </div> 	
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                        <label for="chbx_armsArmboardsLeft_id" class="">
                                                                                <span class="colorChkBx" onClick="changeDiffChbxColor(4,'chbx_armsTuckedLeft_id','chbx_armsTuckedRight_id','chbx_armsArmboardsLeft_id','chbx_armsArmboardsRight_id');" style=" <?php echo $chbxArmsBackColor;?>" >
                                                                                    <input type="checkbox" value="left" id="chbx_armsArmboardsLeft_id" name="chbx_armsArmboardsLeft" <?php if($armsArmboardsLeft=="left") { echo "checked"; } ?>  tabindex="7" />
                                                                                </span>&nbsp; L 
                                                                        </label>
                                                                </div>
                                                                
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                        <label for="left1" class="">
                                                                                <span class="colorChkBx" onClick="changeDiffChbxColor(4,'chbx_armsTuckedLeft_id','chbx_armsTuckedRight_id','chbx_armsArmboardsLeft_id','chbx_armsArmboardsRight_id');" style=" <?php echo $chbxArmsBackColor;?>" >
                                                                                        <input type="checkbox" value="right" id="chbx_armsArmboardsRight_id" name="chbx_armsArmboardsRight" <?php if($armsArmboardsRight=="right") { echo "checked"; } ?>  tabindex="7" />
                                                                                </span>&nbsp;R
                                                                        </label>
                                                                </div> 
                                                                    
                                                            </div>
                                                        
                                                        </div> 
                                                    
                                                    </div>
                                                                 
                                          	</div>
                                            
                                  	</div>
                                    
                                    <div class="panel panel-default bg_panel_anesth">
                                    
                                    		<div class="panel-heading">
                                            
                                            		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                    		
                                                            <label for="" class="">  Eyes </label>
                                                   
                                                   	</div>
                                                    
                                      		</div>
                                            
                                            
                                            <div class="panel-body">
                                            
                                            		<div class="wrap_right_inner_anesth">    
                                                    
                                                    		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            		
                                                                    	<div class="row">
                                                                    	
                                                                        	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		<label> Taped </label>
                                                                           	</div> 	
                                                                            
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		<label for="chbx_eyeTapedLeft_id" onClick="javascript:checkSingle('chbx_eyeTapedLeft_id','chbx_eyeTapedLeft');">
                                                                                    		<input type="checkbox" value="Yes" id="chbx_eyeTapedLeft_id" name="chbx_eyeTapedLeft"  <?php if($eyeTapedLeft=="Yes") { echo "checked"; } ?>  tabindex="7" />
                                                                                            &nbsp; Yes 
                                                                                  	</label>
                                                                          	</div> 	
                                                                            
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		<label for="left1" onClick="javascript:checkSingle('chbx_eyeTapedRight_id','chbx_eyeTapedLeft');">
                                                                                    		<input type="checkbox" value="No" id="chbx_eyeTapedRight_id" name="chbx_eyeTapedLeft"  <?php if($eyeTapedLeft=="No") { echo "checked"; } ?>  tabindex="7" />
                                                                                            &nbsp;No 
                                                                                   	</label>
                                                                          	</div>
                                                                            
                                                                    	</div>
                                                         	
                                                            </div> 
                                                  
                                                  	</div>
                                                    
                                                    <div class="wrap_right_inner_anesth">
                                                    		
                                                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            
                                                            		<div class="row">
                                                                    		
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		<label> Lubed </label>
                                                                           	</div>
                                                                            
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		<label for="chbx_eyeLubedLeft_id" onClick="javascript:checkSingle('chbx_eyeLubedLeft_id','chbx_eyeLubedLeft');">
                                                                                    		<input type="checkbox" value="Yes" id="chbx_eyeLubedLeft_id" name="chbx_eyeLubedLeft" <?php if($eyeLubedLeft=="Yes") { echo "checked"; } ?>  tabindex="7" >
                                                                                            &nbsp;Yes 
                                                                                    </label>
                                                                           	</div> 	
                                                                          	
                                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                            		<label for="chbx_eyeLubedRight_id" onClick="javascript:checkSingle('chbx_eyeLubedRight_id','chbx_eyeLubedLeft');">
                                                                                    		<input type="checkbox" value="No" id="chbx_eyeLubedRight_id" name="chbx_eyeLubedLeft" <?php if($eyeLubedLeft=="No") { echo "checked"; } ?>  tabindex="7" />  No 
                                                                                    </label>
                                                                          	</div>
                                                                            
                                                                  	</div>
                                                                    
                                                         	</div> 
                                                           
                                              		</div>
                                                    <?php
																$chbxPresTethWarBackColor=$chngBckGroundColor;
																if($pressurePointsPadded || $bss || $warning) { 
																	$chbxPresTethWarBackColor=$whiteBckGroundColor; 
																}
													?>	
                                         			<div class="wrap_right_inner_anesth">
                                                    
                                                    		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            
                                                            		<label for="" class="full_width">
                                                                    		<span class="colorChkBx" onClick="changeDiffChbxColor(3,'chbx_pressurePointsPadded_id','chbx_bss_id','chbx_warning_id');" style=" <?php echo $chbxPresTethWarBackColor;?>" >
                                                                            		<input type="checkbox" value="Yes" id="chbx_pressurePointsPadded_id" name="chbx_pressurePointsPadded" <?php if($pressurePointsPadded=="Yes") { echo "checked"; } ?>  tabindex="7" />
                                                                           	</span>&nbsp;Pressure Points Padded 
                                                                    </label>
                                                                    
                                                                    <label for="" class="full_width">
                                                                    		<span class="colorChkBx" onClick="changeDiffChbxColor(3,'chbx_pressurePointsPadded_id','chbx_bss_id','chbx_warning_id');" style=" <?php echo $chbxPresTethWarBackColor;?>" >
                                                                            		<input type="checkbox" value="Yes" id="chbx_bss_id" name="chbx_bss" <?php if($bss=="Yes") { echo "checked"; } ?>  tabindex="7" />
                                                                         	</span>&nbsp;BSS 
                                                                    </label>
                                                                    
                                                                    <label for="" class="full_width">
                                                                    		<span class="colorChkBx" onClick="changeDiffChbxColor(3,'chbx_pressurePointsPadded_id','chbx_bss_id','chbx_warning_id');" style=" <?php echo $chbxPresTethWarBackColor;?>" >
                                                                            		<input type="checkbox" value="Yes" id="chbx_warning_id" name="chbx_warning" <?php if($warning=="Yes") { echo "checked"; } ?>  tabindex="7" >
                                                                          	</span>&nbsp;Warming 
                                                                    </label>
                                                           
                                                           </div> 
                                                            
                                                   	</div>
                                                    
                                                 	<div class="wrap_right_inner_anesth">    
                                                    
                                                    		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            
                                                            		<div class="row">
                                                                    
                                                                    		<label class="col-md-4 col-lg-4 col-xs-12 col-sm-12"> Device temp  </label>
                                                                            <div class="clearfix visible-sm"></div>
                                                                            <div class="col-md-8 col-sm-12 col-xs-12 col-lg-8">
                                                                                    		<input type="text" id="txt_field01" name="txt_temp" class="form-control" tabindex="1" value="<?php echo $temp;?>"  />
                                                                            </div>	
                                                                            
                                                                  	</div>
                                                                    
                                                        	</div> 
                                                            
                                              		</div>
                                                
                                                	<div class="clearfix margin_small_only"></div>
                                                    
                                                    <div class="inner_left_slider_anesth border_adj">
                                                    		
                                                            <span class="left_controls_scroll bg_white" id="tdArr1"  onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('2');" : "setAppIcon('tdArr1')"; ?>" ><b class="fa fa-caret-down blue"></b></span>
                                                            <span class="left_controls_scroll" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('2');" : "setAppIcon('tdArr1')"; ?>"> Systolic Pressure   </span>
                                                            <span class="left_controls_scroll  bg_bl" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('3');" : "setAppIcon('tdArr2')"; ?>" id="tdArr2"> <b class="fa fa-caret-up green"></b> </span>
                                                            <span class="left_controls_scroll bg_white" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('3');" : "setAppIcon('tdArr2')"; ?>"> Diastolic Pressure</span>
                                                            <span class="left_controls_scroll " onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('1');" : "setAppIcon('tdRfill')"; ?>" id="tdRfill"> <b style="color:#333;" class="fa fa-circle"></b> </span>
                                                            <span class="left_controls_scroll bg_bl" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('1');" : "setAppIcon('tdRfill')"; ?>" > Heart </span>
                                                            <span class="left_controls_scroll bg_white" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('0');" : "setAppIcon('tdRblank')"; ?>" id="tdRblank"> <b class="fa fa-circle-o"></b> </span>
                                                            <span class="left_controls_scroll" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('0');" : "setAppIcon('tdRblank')"; ?>"> Respiration </span>
                                                            <span class="left_controls_scroll bg_bl" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('text');" : "setAppIcon('tdRText')"; ?>" id="tdRText"> <b> T </b> </span>
                                                            <span class="left_controls_scroll bg_white" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('text');" : "setAppIcon('tdRText')"; ?>"> Input Value</span>
                                                                        <div class="clearfix  margin_small_only"></div>
                                                                        <div class="padding_15 full_bl">
                                                                        	<?php if(!trim($graphComments)) { $graphComments='comment'; } ?>
	                                                                       	<textarea id="graphCommentsId" name="graphComments" onClick="javascript:if(this.value=='comment') { this.value=''; }" onBlur="if(!this.value) { this.value='comment'; }" class="form-control" style="resize:none;" placeholder="Comment" rows="2" cols="50" tabindex="6"  ><?php echo stripslashes($graphComments);?></textarea>
                                                                      	</div>
                                                                        <div class="clearfix margin_small_only"></div>
                                                                        <span class="left_controls_scroll bg_white"> 
                                                                            <b class="fa fa-eraser" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('erase');" : "clearApp()"; ?>" style="margin-right:2px;margin-left:2px; color:#333;"></b>
                                                                            <b class=" rotate_icon2 fa fa-mail-reply"  onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('undo');" : "undoApp()"; ?>" style="margin-right:2px;margin-left:2px; color:#333;"></b>
                                                                            <b class=" rotate_icon fa fa-mail-forward" onClick="<?php echo ($blEnableHTMLGrid == true) ? "setEvent('redo');" : "redoApp()"; ?>"  style="margin-right:2px;margin-left:2px; color:#333;"></b>
                                                                        
                                                                        </span>
                                                                    </div>
                                                                    
                                                                 
                                      		</div> <!-- SYSTOLIC -->
                                            
                                	</div>
                          	
                            </div>
                            
          			</div>
                    
           	</div>
            
                                             
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
            
            		<div class="panel panel-default bg_panel_op">
                    
                    		<div class="panel-body">
                            
                            		<div class="row">
                                    
                                    		<div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                            
                                            		<div class="row">
                                                    	<?php
															$defaultEvaluation	=	'';
															if( $evaluation == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
															{
																$defaultEvaluation	= $objManageData->getDefault('evaluation','name');
																$evaluation		= $defaultEvaluation;
															}
														
														?>
                                                    
                                                        <div class="col-md-3 col-sm-4 col-xs-4 col-lg-2">
                                                        
                                                                <a onClick="return showEvaluationFn('local_anes_revaluation1_id', '', 'no', $(this).offset().left, parseInt($(this).offset().top - 190)),document.getElementById('selected_frame_name_id').value='';" class="panel-title rob alle_link show-pop-trigger22 btn btn-default " data-placement="top">
                                                                    <span class="fa fa-caret-right"></span>  Evaluation
                                                                </a>
                                                               
                                                        </div>
                                                       
                                                        <div class="col-md-9 col-sm-8 col-xs-8 col-lg-10">
                                                                <textarea id="local_anes_revaluation1_id" <?php echo $keyPressInput.' '.$focusInput.' '.$keyUpInput;?> class="form-control" name="txtarea_evaluation" style=" <?php if(trim(!$evaluation)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?> " rows="2" cols="50" tabindex="6"   ><?php echo trim(stripslashes($evaluation));?></textarea>
                                                                
                                                        </div>	
                                         			
                                                    </div> 
                                          	
                                            </div>
                                            
                                            <div class="clearfix margin_small_only visible-sm"></div>
                                            
                                            <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                            
                                            		<div class="row">
                                                    
                                                    		<div class="col-md-3 col-sm-4 col-xs-4 col-lg-2">
                                                            
                                                            		<label> Comments  </label>
                                                           	
                                                            </div>
                                                            
                                                            <div class="col-md-9 col-sm-8 col-xs-8 col-lg-10">
                                                            		
                                                                    <textarea class="form-control" name="txtarea_comments" rows="2" cols="50" tabindex="6"  ><?php echo stripslashes($comments);?></textarea>
                                                          	</div>	
                                                            
                                               		</div>
                                                    
                                         	</div>
                                            
                                            <div class="clearfix margin_adjustment_only margin_small_only"></div>
                                            
                                            <?php
														$BPRSaoStableBackColor=$chngBckGroundColor;
														if($bp || $P || $rr || $sao || $StableCardioRespiratory) { 
															$BPRSaoStableBackColor=$whiteBckGroundColor; 
														}
											?>
                                            <div class="inner_safety_wrap padding_15">
                                            
                                            		<div class="row">
                                                    
                                                    		<div class="col-md-2 col-sm-12 col-xs-12 col-lg-2">
                                                            
                                                            		<label for="bp" class="full_width text-left"><b>  PACU </b></label>    
                                                                    
                                                          	</div>
                                                            
                                                            <div class="col-sm-12 visible-sm">
                                                            
                                                            		<div class="clearfix  margin_small_only border-dashed"></div>
                                                           	
                                                            </div>
                                                            
                                                       		<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                            	
                                                            		<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                    		
                                                                            <div class="row">
                                                                            			<label for="bp_temp" class="col-md-12 col-lg-4 col-xs-12 col-sm-12  text-center">BP: </label>    
                                                                                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12  text-center">
                                                                                        		<input id="bp_temp" onFocus="changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');"  type="text" name="txt_bp" value="<?php echo $bp;?>" maxlength="7" size="7" class="form-control"  style=" <?php echo $BPRSaoStableBackColor;?>" onKeyUp="displayText1=this.value;changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');" onClick="getShow(parseInt(findPos_Y('bp_temp'))+30,parseInt(findPos_X('bp_temp')),'flag1');top.frames[0].setPNotesHeight();" />
                                                                                                <input type="hidden" id="bp" name="bp_hidden">	
                                                                                      	</span>
                                                                        	</div>
                                                                            
                                                                    </div>
                                                                    
                                                                    <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                    
                                                                    		<div class="row">
                                                                            		
                                                                                    <label for="bp_temp2" class="col-md-12 col-lg-4 col-xs-12 col-sm-12  text-center">P:</label>
                                                                                    
                                                                                    <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12  text-center">
                                                                                    
                                                                                    		<input id="bp_temp2" onFocus="changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');" type="text" name="txt_P" value="<?php echo $P;?>" size="1" maxlength="3" class="form-control" style=" <?php echo $BPRSaoStableBackColor;?>" onKeyUp="displayText2=this.value;changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');" onClick="getShow(parseInt(findPos_Y('bp_temp2'))+30,parseInt(findPos_X('bp_temp2')),'flag2');top.frames[0].setPNotesHeight();"/>
                                                                                  	          
                                                                                    </span>
                                                                                    
                                                                            	</div>
                                                                                
                                                                  	</div>
                                                                    
                                                                    <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                    
                                                                    			<div class="row">
                                                                                
                                                                                		<label for="rr" class="col-md-12 col-lg-4 col-xs-12 col-sm-12  text-center">RR:</label>    
                                                                                        
                                                                                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12  text-center">
                                                                                        			<input id="bp_temp3" onFocus="changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');" type="text" name="txt_rr" value="<?php echo $rr;?>" size="1" maxlength="3" class="form-control" style=" <?php echo $BPRSaoStableBackColor;?>" onKeyUp="displayText3=this.value;changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');" onClick="getShow(parseInt(findPos_Y('bp_temp3'))+30,parseInt(findPos_X('bp_temp3')),'flag3');top.frames[0].setPNotesHeight();"/>
                                                                                      	</span>
                                                                                        
                                                                              	</div>
                                                                                
                                                              		</div>
                                                                    
                                                                    <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                                    
                                                                    			<div class="row">
                                                                                		
                                                                                        <label for="bp_temp4" class="col-md-12 col-lg-4 col-xs-12 col-sm-12  text-center">SaO2:</label>    
                                                                                        
                                                                                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12  text-center">
                                                                                        		<input id="bp_temp4" onFocus="changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');" type="text" name="txt_sao" value="<?php echo $sao;?>" size="1" maxlength="3" class="form-control" style=" <?php echo $BPRSaoStableBackColor;?>" onKeyUp="displayText4=this.value;changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');" onClick="getShow(parseInt(findPos_Y('bp_temp4'))+30,parseInt(findPos_X('bp_temp4')),'flag4');top.frames[0].setPNotesHeight();"/>
                                                                            			
                                                                                        </span>
                                                                                        
                                                                           		</div>
                                                                                
                                                                	</div>
                                                           
                                                           	</div>
                                                            
                                                            <div class="full_width padding_15 visible-sm">
                                                            	
                                                                	<div class="clearfix margin_small_only border-dashed"></div>
                                                                    
                                                        	</div>
                                                            
                                                            <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                                                            
                                                            		<label for="chbx_StableCardioRespiratory_id" class="full_width text-left"  onClick="changeDiffChbxColor(5,'bp_temp','bp_temp2','bp_temp3','bp_temp4','chbx_StableCardioRespiratory_id');">
                                                                    		<span class="colorChkBx"   style=" <?php echo $BPRSaoStableBackColor;?>" >
                                                                            	<input type="checkbox" value="Yes" id="chbx_StableCardioRespiratory_id" name="chbx_StableCardioRespiratory"  <?php if($StableCardioRespiratory=="Yes") { echo "checked"; } ?>  tabindex="7" />
                                                                         	</span>
                                                                    		&nbsp;Stable Cardio Respiratory  
                                                                 	</label>    
                                                      		</div>
                                                                
                                                                
                                                   	</div>
                                           	
                                            </div>
                                            
                                     </div>
                                     
                      		</div>
                            
                 	</div>
                    
           	</div>
            
            <div class="clearfix margin_adjustment_only"></div>
            
            
            <?php
					
						
						$anesthesia1SignOnFileStatus = "Yes";
						$TDanesthesia1NameIdDisplay = "block";
						$TDanesthesia1SignatureIdDisplay = "none";
						$Anesthesia1Name = $loggedInUserName;
						$Anesthesia1SubType = $logInUserSubType;
						$Anesthesia1PreFix = 'Dr.';
						//$signAnesthesia1DateTimeFormatNew = date("m-d-Y h:i A");
			   			//$signAnesthesia1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
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
								$callJavaFunDel = "document.frm_gen_anes_rec.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','gen_anes_rec_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
							}else {
								$callJavaFunDel = "alert('Only $Anesthesia1PreFix $Anesthesia1Name can remove this signature');";
							}
						//END CODE TO REMOVE ANES 1 SIGNATURE	
					?>
            
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
            
            		<div class="panel panel-default bg_panel_op">
                    
                    		<div class="panel-body">
                            
                            		<div class="row">
                                    
                                    		<div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
                                                <div class="inner_safety_wrap" id="TDanesthesia1NameId" style="display:<?php echo $TDanesthesia1NameIdDisplay;?>;">
                                                    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signAnesthesiaIdBackColor?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Anesthesia Provider Signature </a>
                                                </div>
                                                <div class="inner_safety_wrap collapse" id="TDanesthesia1SignatureId" style="display:<?php echo $TDanesthesia1SignatureIdDisplay;?>;">
                                                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$Anesthesia1PreFix." ".$Anesthesia1Name; ?>  </a></span>	     
                                                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $anesthesia1SignOnFileStatus;?></span>
                                                    <span class="rob full_width"> <b> Signature Date</b> <?php echo $signAnesthesia1DateTimeFormatNew;?></span>
                                                </div>
                                            
                                            </div>
                                                        <div class="col-md-4  col-sm-12 col-lg-4 col-xs-12 pull-right">
                                                            <div class="inner_safety_wrap">
                                                                <label class="col-md-12 col-lg-6 col-xs-12 col-sm-12" for="relivedNurseIdList"> Relief Nurse</label>
                                                               <div class="margin_adjustment_only hidden-lg"></div>			
                                                                <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                                 <select class="selectpicker form-control" id="relivedNurseIdList" name="relivedNurseIdList" title="Select"> 
                                                                     <option value="" selected>Select</option>	
																		<?php
                                                                        $relivedNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
                                                                        $relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
                                                                        while($relivedNurseRow=imw_fetch_array($relivedNurseRes)) {
                                                                            $relivedSelectNurseID = $relivedNurseRow["usersId"];
                                                                            $relivedNurseName = $relivedNurseRow["lname"].", ".$relivedNurseRow["fname"]." ".$relivedNurseRow["mname"];
                                                                            $sel="";
                                                                            if($relivedNurseId==$relivedSelectNurseID) {
                                        
                                                                                $sel = "selected";
                                                                            } 
                                                                            else {
                                                                                $sel = "";
                                                                            }
                                                                            if($relivedNurseRow["deleteStatus"]<>'Yes' || $relivedNurseId==$relivedSelectNurseID) {							
                                                                        ?>	
                                                                                <option value="<?php echo $relivedSelectNurseID;?>" <?php echo $sel;?>><?php echo $relivedNurseName;?></option>
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
        
        

	
	</div>
    
    
</form>

</div>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="gen_anes_rec.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
</form>

<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
<script>

	top.frames[0].setPNotesHeight();
</script>
<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "gen_anes_rec.php";
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
include("print_page.php");
?>
</body>
</html>