<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Pre-Op General Anesthesia</title>

<!-- Allow standalone mode on home screen. -->
<meta name="apple-mobile-web-app-capable" content="yes" />

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
session_start();
include_once("common/conDb.php");
$tablename = "preopgenanesthesiarecord";
//include("common/linkfile.php");
$spec = "</head><body onClick=\"closeEpost(); return top.frames[0].main_frmInner.hideSliders();\">";
include("common/link_new_file.php");

//START INCLUDE PREDEFINE FUNCTIONS
include_once("common/pre_define_medication.php"); //FOR PRE-OP GEN ANES(ALSO COMMON FOR PRE OP  HEALTH QUEST, LOCAL ANES)
//END INCLUDE PREDEFINE FUNCTIONS
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

$slider_row="#CAD8FD";
$page=explode("?",$pagename[2]);
extract($_GET);
$SaveForm_alert = $_REQUEST['SaveForm_alert'];


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
	
	$fieldName = "pre_op_genral_anesthesia_form";
	$pageName = "pre_op_general_anes.php?patient_id=$patient_id&pConfId=$pConfId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId";
	}
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK
$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId;

//SAVE RECORD TO DATABASE
if($_POST['SaveRecordForm']=='yes'){
	$text = $_REQUEST['getText'];
	$tablename = "preopgenanesthesiarecord";
	$HeartProblem = $_POST["chbx_hp"];
	$HighBloodPressure = $_POST["chbx_hbp"];
	$Stroke = $_POST["chbx_stroke"];
	$Diabetes = $_POST["chbx_db"];
	$BleedingProblems = $_POST["chbx_ble_prb"];
	$AsthmaLungDisease = $_POST["chbx_ast_ln_dis"];
	$HiatalHernia = $_POST["chbx_hia_her"];
	$LiverKidneyDisease = $_POST["chbx_liv_kd"];
	$MotionSickness = $_POST["chbx_mot_sic"];
	$ThyroidDisease = $_POST["chbx_thy_dis"];
	$SeizuresFainting = $_POST["chbx_sei_fai"];
	$NeurologicalDisease = $_POST["chbx_neur_dis"];
	$MentalDisease = $_POST["chbx_ment_dis"];
	$medicalHistoryOther = addslashes($_POST["medicalHistoryOther"]);
	
	$lastMenustrualPeriod = $_POST["lastMenustrualPeriod"];
		$lastMenustrualPeriod_split = explode("-",$lastMenustrualPeriod);
		$lastMenustrualPeriodTemp = $lastMenustrualPeriod_split[2]."-".$lastMenustrualPeriod_split[0]."-".$lastMenustrualPeriod_split[1];

	$pregnantDueDate = $_POST["pregnantDueDate"];
		$pregnantDueDate_split = explode("-",$pregnantDueDate);
		$pregnantDueDateTemp = $pregnantDueDate_split[2]."-".$pregnantDueDate_split[0]."-".$pregnantDueDate_split[1];
	
	$allergies2Medications = $_POST["chbx_allergies2Medications"];
	$current2Medications = $_POST["chbx_current2Medications"];
	$previousOperations = $_POST["chbx_previousOperations"];
	
	
	$probPrevAnesthesia = $_POST["chbx_prob_pre_anes"];
	
	
	if($probPrevAnesthesia=="Yes") {
		
		$pnv = $_POST["chbx_pnv"];
		$dc = $_POST["chbx_dc"];
		$probPrevAnesthesiaDesc = addslashes($_POST["probPrevAnesthesiaDesc"]);
	}else {
		$pnv = "";
		$dc = "";
		$probPrevAnesthesiaDesc = "";
	}	

	$familyHistoryAnesthesiaProblems = $_POST["chbx_fam_hist_anes"];
	$familyHistoryAnesthesiaProblemsDesc = addslashes($_POST["familyHistoryAnesthesiaProblemsDesc"]);
	
	$smoke = $_POST["chbx_no"];
	$smokeCigarettes = $_POST["chbx_cigaret"];
	$smokeCigars = $_POST["chbx_cigar"];
	$smokePipe = $_POST["chbx_pipe"];
	$smokePacks = $_POST["smokePacks"];
	$smokeYears = $_POST["smokeYears"];
	$smokeStopDate = $_POST["smokeStopDate"];
		$smokeStopDate_split = explode("-",$smokeStopDate);
		$smokeStopDateTemp = $smokeStopDate_split[2]."-".$smokeStopDate_split[0]."-".$smokeStopDate_split[1];
	
	if($smoke=="No") {
		$smokeCigarettes = "";
		$smokeCigars = "";
		$smokePipe = "";
		$smokePacks = "";
		$smokeYears = "";
		$smokeStopDateTemp = "";
	}
	$alcohol = $_POST["chbx_acl"];
	$alcoholWeeksList = $_POST["weeksList"];
	$alcoholNumber = $_POST["alcoholNumber"];
	
	
	$dentures = $_POST["chbx_dentures"];
	$cappedTeeth = $_POST["chbx_dnt_capteth"];
	$permanentBridge = $_POST["chbx_dnt_prmbrig"];
	$looseBrokenTeeth = $_POST["chbx_dnt_lbt"];
	$PeriodontalDisease = $_POST["chbx_PeriodontalDisease"];
	$otherDentalProblems = addslashes($_POST["otherDentalProblems"]);
	
	$preOpComplications = addslashes($_POST["preOpComplications"]);
	$whoUserType = $_POST["whoUserTypeList"];
	
	if($_POST['whoUserTypeList']=="") {
		$createdByUserIdList = $_POST['preOpGenBlankId_list'];
	}else if($_POST['whoUserTypeList']=="Anesthesiologist") {
		$createdByUserIdList = $_POST['preOpGenAnesthesiologistId_list'];
	}else if($_POST['whoUserTypeList']=="Nurse") {
		$createdByUserIdList = $_POST['preOpGenNurseId_list'];
	}
	
	$createdByUserId = $createdByUserIdList;
	$relivedNurseId = $_POST['relivedNurseIdList'];
	
	
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD 
		$form_status = "completed";
		if($HeartProblem=="" || $HighBloodPressure=="" || $Stroke==""
		  || $Diabetes=="" || $BleedingProblems=="" || $AsthmaLungDisease==""	    	
		  || $probPrevAnesthesia=="" || $familyHistoryAnesthesiaProblems=="" || $alcohol==""
		  
		  || ($smoke=='' && $smokeCigarettes=='' && $smokeCigars==''
		 	  && $smokePipe=='')
		  
		  || ($dentures=='' && $cappedTeeth=='' && $permanentBridge==''
		 	  && $looseBrokenTeeth=='' && $PeriodontalDisease=='')
		  || $createdByUserId=='' 
		) 
		{
			$form_status = "not completed";
		}
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	//START CODE TO RESET THE RECORD
	if($_REQUEST['hiddResetStatusId']=='Yes') {
		$form_status 	= '';
		$resetRecordQry	= "resetDateTime= '".date('Y-m-d H:i:s')."',
						   resetBy 		= '".$_SESSION['loginUserId']."',
						  ";

	}
	//END CODE TO RESET THE RECORD
	
	
	$chkPreopgenAnesQry = "select * from `preopgenanesthesiarecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkPreopgenAnesRes = imw_query($chkPreopgenAnesQry) or die(imw_error()); 
	$chkPreopgenAnesNumRow = imw_num_rows($chkPreopgenAnesRes);
	if($chkPreopgenAnesNumRow>0) {
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkPreopgenAnesRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE) 
		
		$SavePreopgenAnesQry = "update `preopgenanesthesiarecord` set 
									HeartProblem = '$HeartProblem',
									HighBloodPressure = '$HighBloodPressure', 
									Stroke = '$Stroke',
									Diabetes = '$Diabetes', 
									BleedingProblems = '$BleedingProblems',
									AsthmaLungDisease = '$AsthmaLungDisease', 
									HiatalHernia = '$HiatalHernia',
									LiverKidneyDisease = '$LiverKidneyDisease',
									MotionSickness = '$MotionSickness', 
									ThyroidDisease = '$ThyroidDisease', 
									SeizuresFainting = '$SeizuresFainting', 
									NeurologicalDisease = '$NeurologicalDisease',
									MentalDisease = '$MentalDisease', 
									medicalHistoryOther = '$medicalHistoryOther', 
									lastMenustrualPeriod = '$lastMenustrualPeriodTemp', 
									pregnantDueDate = '$pregnantDueDateTemp',
									allergies2Medications = '$allergies2Medications',
									current2Medications = '$current2Medications',
									previousOperations = '$previousOperations',
									probPrevAnesthesia = '$probPrevAnesthesia', 
									pnv = '$pnv',
									dc = '$dc',
									probPrevAnesthesiaDesc = '$probPrevAnesthesiaDesc',
									familyHistoryAnesthesiaProblems = '$familyHistoryAnesthesiaProblems', 
									familyHistoryAnesthesiaProblemsDesc = '$familyHistoryAnesthesiaProblemsDesc',
									smoke = '$smoke',
									smokeCigarettes = '$smokeCigarettes',
									smokeCigars = '$smokeCigars',
									smokePipe = '$smokePipe',
									smokePacks = '$smokePacks',
									smokeYears = '$smokeYears',
									smokeStopDate = '$smokeStopDateTemp',
									alcohol = '$alcohol',
									alcoholWeeksList = '$alcoholWeeksList',
									alcoholNumber = '$alcoholNumber',
									dentures = '$dentures',
									cappedTeeth = '$cappedTeeth',
									permanentBridge = '$permanentBridge',
									looseBrokenTeeth = '$looseBrokenTeeth',
									PeriodontalDisease = '$PeriodontalDisease',
									otherDentalProblems = '$otherDentalProblems',
									preOpComplications = '$preOpComplications',
									whoUserType = '$whoUserType',
									createdByUserId = '$createdByUserId',
									relivedNurseId = '$relivedNurseId',
									$resetRecordQry
									form_status ='".$form_status."',
									ascId='".$_REQUEST["ascId"]."' 
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
	}else {
		$SavePreopgenAnesQry = "insert into `preopgenanesthesiarecord` set 
									HeartProblem = '$HeartProblem',
									HighBloodPressure = '$HighBloodPressure', 
									Stroke = '$Stroke',
									Diabetes = '$Diabetes', 
									BleedingProblems = '$BleedingProblems',
									AsthmaLungDisease = '$AsthmaLungDisease', 
									HiatalHernia = '$HiatalHernia',
									LiverKidneyDisease = '$LiverKidneyDisease',
									MotionSickness = '$MotionSickness', 
									ThyroidDisease = '$ThyroidDisease', 
									SeizuresFainting = '$SeizuresFainting', 
									NeurologicalDisease = '$NeurologicalDisease',
									MentalDisease = '$MentalDisease', 
									medicalHistoryOther = '$medicalHistoryOther', 
									lastMenustrualPeriod = '$lastMenustrualPeriodTemp', 
									pregnantDueDate = '$pregnantDueDateTemp',
									allergies2Medications = '$allergies2Medications',
									current2Medications = '$current2Medications',
									previousOperations = '$previousOperations',
									probPrevAnesthesia = '$probPrevAnesthesia', 
									pnv = '$pnv',
									dc = '$dc',
									probPrevAnesthesiaDesc = '$probPrevAnesthesiaDesc',
									familyHistoryAnesthesiaProblems = '$familyHistoryAnesthesiaProblems', 
									familyHistoryAnesthesiaProblemsDesc = '$familyHistoryAnesthesiaProblemsDesc',
									smoke = '$smoke',
									smokeCigarettes = '$smokeCigarettes',
									smokeCigars = '$smokeCigars',
									smokePipe = '$smokePipe',
									smokePacks = '$smokePacks',
									smokeYears = '$smokeYears',
									smokeStopDate = '$smokeStopDateTemp',
									alcohol = '$alcohol',
									alcoholWeeksList = '$alcoholWeeksList',
									alcoholNumber = '$alcoholNumber',
									dentures = '$dentures',
									cappedTeeth = '$cappedTeeth',
									permanentBridge = '$permanentBridge',
									looseBrokenTeeth = '$looseBrokenTeeth',
									PeriodontalDisease = '$PeriodontalDisease',
									otherDentalProblems = '$otherDentalProblems',
									preOpComplications = '$preOpComplications',
									whoUserType = '$whoUserType',
									createdByUserId = '$createdByUserId',
									relivedNurseId = '$relivedNurseId',
									$resetRecordQry
									form_status ='".$form_status."',									
									confirmation_id='".$_REQUEST["pConfId"]."',
									patient_id = '".$_REQUEST["patient_id"]."'";
	}
	$SavePreopgenAnesRes = imw_query($SavePreopgenAnesQry) or die(imw_error());
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
		
   //DELETE ALLERGIES AND SET ALLERGIES STATUS
	if($_POST['chbx_allergies2Medications']=='No') {
	 	imw_query("delete from patient_allergies_tbl where patient_confirmation_id = '".$_REQUEST["pConfId"]."'");
		$patient_confirm_allergiesStatus = "Yes";
	}else{
		$patient_confirm_allergiesStatus = "";
	}
	$updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '".$patient_confirm_allergiesStatus."' where patientConfirmationId = '".$_REQUEST["pConfId"]."'";
	$updateNKDAstatusRes = imw_query($updateNKDAstatusQry);
	 
	//DELETE ALLERGIES AND SET ALLERGIES STATUS
	
	$save = 'true';
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

//VIEW RECORD FROM DATABASE
	//if($_POST['SaveRecordForm']==''){	
		//CODE TO CARRY FORWARD VALUES FROM HEALTH QUESTIONARRIE
		//END CODE TO CARRY FORWARD VALUES FROM HEALTH QUESTIONARRIE
		$Insur_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
		$Insur_patientConfirm_tblRes = imw_query($Insur_patientConfirm_tblQry) or die(imw_error());
		$Insur_patientConfirm_tblRow = imw_fetch_array($Insur_patientConfirm_tblRes);
		$Insur_patientConfirmDosTemp = $Insur_patientConfirm_tblRow["dos"];
		
		$ViewPreopgenAnesQry = "select * from `preopgenanesthesiarecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPreopgenAnesRes = imw_query($ViewPreopgenAnesQry) or die(imw_error()); 
		$ViewPreopgenAnesNumRow = imw_num_rows($ViewPreopgenAnesRes);
		$ViewPreopgenAnesRow = imw_fetch_array($ViewPreopgenAnesRes); 
		
		$HeartProblem = $ViewPreopgenAnesRow["HeartProblem"];
		$HighBloodPressure = $ViewPreopgenAnesRow["HighBloodPressure"]; 
		$Stroke = $ViewPreopgenAnesRow["Stroke"];
		$Diabetes = $ViewPreopgenAnesRow["Diabetes"]; 
		$BleedingProblems = $ViewPreopgenAnesRow["BleedingProblems"];
		$AsthmaLungDisease = $ViewPreopgenAnesRow["AsthmaLungDisease"]; 
		$HiatalHernia = $ViewPreopgenAnesRow["HiatalHernia"];
		$LiverKidneyDisease = $ViewPreopgenAnesRow["LiverKidneyDisease"];
		$MotionSickness = $ViewPreopgenAnesRow["MotionSickness"]; 
		$ThyroidDisease = $ViewPreopgenAnesRow["ThyroidDisease"]; 
		$SeizuresFainting = $ViewPreopgenAnesRow["SeizuresFainting"]; 
		$NeurologicalDisease = $ViewPreopgenAnesRow["NeurologicalDisease"];
		$MentalDisease = $ViewPreopgenAnesRow["MentalDisease"]; 
		$medicalHistoryOther = $ViewPreopgenAnesRow["medicalHistoryOther"]; 
		
		$lastMenustrualPeriodTemp = $ViewPreopgenAnesRow["lastMenustrualPeriod"]; 
			$lastMenustrualPeriod_split = explode("-",$lastMenustrualPeriodTemp);
			$lastMenustrualPeriod = $lastMenustrualPeriod_split[1]."-".$lastMenustrualPeriod_split[2]."-".$lastMenustrualPeriod_split[0];
		
		$pregnantDueDateTemp = $ViewPreopgenAnesRow["pregnantDueDate"];
			$pregnantDueDate_split = explode("-",$pregnantDueDateTemp);
			$pregnantDueDate = $pregnantDueDate_split[1]."-".$pregnantDueDate_split[2]."-".$pregnantDueDate_split[0];
		
		$allergies2Medications = $ViewPreopgenAnesRow["allergies2Medications"]; 
		$current2Medications = $ViewPreopgenAnesRow["current2Medications"]; 
		$previousOperations = $ViewPreopgenAnesRow["previousOperations"]; 
		
		$probPrevAnesthesia = $ViewPreopgenAnesRow["probPrevAnesthesia"]; 
		$pnv = $ViewPreopgenAnesRow["pnv"];
		$dc = $ViewPreopgenAnesRow["dc"];
		$probPrevAnesthesiaDesc = $ViewPreopgenAnesRow["probPrevAnesthesiaDesc"];
		
		$familyHistoryAnesthesiaProblems = $ViewPreopgenAnesRow["familyHistoryAnesthesiaProblems"]; 
		$familyHistoryAnesthesiaProblemsDesc = $ViewPreopgenAnesRow["familyHistoryAnesthesiaProblemsDesc"];
		$smoke = $ViewPreopgenAnesRow["smoke"];
		$smokeCigarettes = $ViewPreopgenAnesRow["smokeCigarettes"];
		$smokeCigars = $ViewPreopgenAnesRow["smokeCigars"];
		$smokePipe = $ViewPreopgenAnesRow["smokePipe"];
		$smokePacks = $ViewPreopgenAnesRow["smokePacks"];
		$smokeYears = $ViewPreopgenAnesRow["smokeYears"];
		$smokeStopDateTemp = $ViewPreopgenAnesRow["smokeStopDate"];
			$smokeStopDate_split = explode("-",$smokeStopDateTemp);
			$smokeStopDate = $smokeStopDate_split[1]."-".$smokeStopDate_split[2]."-".$smokeStopDate_split[0];
		
		$alcohol = $ViewPreopgenAnesRow["alcohol"];
		$alcoholWeeksList = $ViewPreopgenAnesRow["alcoholWeeksList"];
		$alcoholNumber = $ViewPreopgenAnesRow["alcoholNumber"];
		$dentures = $ViewPreopgenAnesRow["dentures"];
		$cappedTeeth = $ViewPreopgenAnesRow["cappedTeeth"];
		$permanentBridge = $ViewPreopgenAnesRow["permanentBridge"];
		$looseBrokenTeeth = $ViewPreopgenAnesRow["looseBrokenTeeth"];
		$PeriodontalDisease = $ViewPreopgenAnesRow["PeriodontalDisease"];
		$otherDentalProblems = $ViewPreopgenAnesRow["otherDentalProblems"];
		$preOpComplications = $ViewPreopgenAnesRow["preOpComplications"];
		$whoUserType = $ViewPreopgenAnesRow["whoUserType"];
		$createdByUserId = $ViewPreopgenAnesRow["createdByUserId"];
		$relivedNurseId = $ViewPreopgenAnesRow["relivedNurseId"];
		$form_status = $ViewPreopgenAnesRow["form_status"];
		$ascId = $ViewPreopgenAnesRow["ascId"];
		$confirmation_id = $ViewPreopgenAnesRow["confirmation_id"];
		$patient_id = $ViewPreopgenAnesRow["patient_id"];
	//}	<font face="Arial, Helvetica, sans-serif"></font>
//END VIEW RECORD FROM DATABASE


?>
<script src="js/epost.js"></script>
<script type="text/javascript">

	
	function changeSliderColor(){
		var setColor = '<?php echo $bglight_blue_local_anes; ?>';
		top.changeColor(setColor);
	}
	
top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');

//CODE TO SELECT DATE	
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
		
		mywindow=open('mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
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
		document.getElementById(q).value=fillDate;
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}
//END CODE TO SELECT DATE


//CODE TO DISPLAY USER NAMA IN DROP DOWN
function checkPreOpGenUserType(preOpGenUserName) {
	var preOpGenUserNameValue = preOpGenUserName.value;
	if(preOpGenUserNameValue=="Anesthesiologist")	{
		document.getElementById("preOpGen_AnesthesiologistId_div").style.display="block";
		document.getElementById("preOpGen_NurseId_div").style.display="none";
		document.getElementById("preOpGen_BlankId_div").style.display="none";
	}else if(preOpGenUserNameValue=="Nurse")	{
		document.getElementById("preOpGen_AnesthesiologistId_div").style.display="none";
		document.getElementById("preOpGen_NurseId_div").style.display="block";
		document.getElementById("preOpGen_BlankId_div").style.display="none";
	}else if(preOpGenUserNameValue=="")	{
		document.getElementById("preOpGen_AnesthesiologistId_div").style.display="none";
		document.getElementById("preOpGen_NurseId_div").style.display="none";
		document.getElementById("preOpGen_BlankId_div").style.display="block";
	}
}
//END CODE TO DISPLAY USER IN DROP DOWN
</script>
<!-- <body onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();"> -->
<div id="post" style="display:none;position:absolute;z-index:10"></div>
<?php
// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
	$allergiesNKDA_patientconfirmation_status = $detailConfirmationFinalize->allergiesNKDA_status;
// GETTING FINALIZE STATUS
//show all epost ravi
?>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<?php
include("common/pre_defined_popup.php");
?>
<div class="slider_content scheduler_table_Complete" id="" style="" onDblClick="closePreDefineDiv1()" onClick="calCloseFun();preCloseFun('evaluationPreDefineDiv');preCloseFun('evaluationPreDefineMedDiv');" onMouseOver="">
		<?php
			$epost_table_name = "preopgenanesthesiarecord";
			include("./epost_list.php");
		?>
        <!--
    	<div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_anesth">
        <span class="bg_span_anesth">
            Pre-Op General Anesthesia Record
        </span>
		
     </div>	
     -->
     <div id="divSaveAlert" style="position:absolute;left:35%; top:210px; display:none; z-index:999;">
			<?php 
                $bgCol = $bgdark_blue_local_anes;
                $borderCol = $bgdark_blue_local_anes;
                include('saveDivPopUp.php'); 
            ?>
        </div>
<form name="frm_pre_gn_an_rec" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
        <input type="hidden" name="divId" id="divId">
        <input type="hidden" name="counter" id="counter">
        <input type="hidden" name="secondaryValues" id="secondaryValues">
        <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
        <input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">			
        <input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
        <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
        <input type="hidden" name="saveRecord" id="saveRecord" value="true">
        <input type="hidden" name="getText" id="getText">
        <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
        <input type="hidden" name="frmAction" id="frmAction" value="pre_op_general_anes.php">
        <input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">			
        <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
        <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
        <input type="hidden" name="hiddResetStatusId" id="hiddResetStatusId">
        <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
            <div class="panel panel-default bg_panel_anesth">
                <div class="panel-heading">
                    <h3 class="panel-title rob"> Medical History </h3>
                    <div class="right_label top_yes_no color_jet">
                        <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 pull-right">
                            <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  Yes  </label>
                            <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  No  </label>	                                                        		
                            <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  &nbsp; </label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inner_safety_wrap">
                        <div class="row">
                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                <label class="date_r">
                                    Heart Problem
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                <div class="">
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                       
                                        <span class="colorChkBx" style=" <?php if($HeartProblem) { echo $whiteBckGroundColor;}?>" href="javascript:void(0)" onClick="checkSingle('chbx_hp_yes','chbx_hp')">
                                        <input type="checkbox" value="Yes" name="chbx_hp"  id="chbx_hp_yes" <?php if($HeartProblem=="Yes"){ echo "checked"; }?> onClick="changeChbxColor('chbx_hp')"></span>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                       <span class="colorChkBx" style=" <?php if($HeartProblem) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_hp_no','chbx_hp')">
                                       <input type="checkbox" value="No" name="chbx_hp" id="chbx_hp_no" <?php if($HeartProblem=="No"){ echo "checked"; }?> onClick="changeChbxColor('chbx_hp')">
                                       </span> 
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        &nbsp;
                                    </div>                                      
                                </div> 
                            </div> <!-- Col-3 ends  -->
                        </div>	 
                     </div>	
                    <div class="inner_safety_wrap">
                        <div class="row">
                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                <label class="date_r">
                                   High Blood Pressure
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                <div class="">
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                       <span class="colorChkBx" style=" <?php if($HighBloodPressure) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_hbp_yes','chbx_hbp')">
                                       <input type="checkbox" value="Yes" name="chbx_hbp" id="chbx_hbp_yes" <?php if($HighBloodPressure=="Yes"){ echo "checked"; }?> tabindex="7"  onClick="changeChbxColor('chbx_hbp')">
                                       </span> 
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                       <span class="colorChkBx" style=" <?php if($HighBloodPressure) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_hbp_no','chbx_hbp')">
                                       <input type="checkbox" value="No" name="chbx_hbp" id="chbx_hbp_no" <?php if($HighBloodPressure=="No"){ echo "checked"; }?> onClick="changeChbxColor('chbx_hbp')">
                                       </span> 
                                    </div>
                                     <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        &nbsp;
                                    </div>                                      
                                </div> 
                            </div> <!-- Col-3 ends  -->
                        </div>	 
                     </div>
                     <div class="inner_safety_wrap">
                        <div class="row">
                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                <label>
                                Stroke
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                <div class="">
                                 
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                         <span class="colorChkBx" style=" <?php if($Stroke) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_stroke_yes','chbx_stroke')">
                                         <input type="checkbox" value="Yes" name="chbx_stroke" id="chbx_stroke_yes" <?php if($Stroke=="Yes"){ echo "checked"; }?> onClick="changeChbxColor('chbx_stroke')">
                                         </span>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        <span class="colorChkBx" style=" <?php if($Stroke) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_stroke_no','chbx_stroke')">
                                         <input type="checkbox" value="No" name="chbx_stroke" id="chbx_stroke_no" <?php if($Stroke=="No"){ echo "checked"; }?> onClick="changeChbxColor('chbx_stroke')">
                                         </span> 
                                    </div>
                                     <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        &nbsp;
                                    </div>                                      
                                </div> 
                            </div> <!-- Col-3 ends  -->
                        </div>	 
                     </div>
                     
                     <div class="inner_safety_wrap">
                        <div class="row">
                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                <label class="date_r">
                                   Diabetes
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                <div class="">
                                 
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                       <span class="colorChkBx" style=" <?php if($Diabetes) { echo $whiteBckGroundColor;}?>"  onClick="checkSingle('chbx_db_yes','chbx_db')">
                                       <input type="checkbox" value="Yes" name="chbx_db" id="chbx_db_yes" <?php if($Diabetes=="Yes"){ echo "checked"; }?> onClick="changeChbxColor('chbx_db')">
                                       </span> 
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                       <span class="colorChkBx" style=" <?php if($Diabetes) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_db_no','chbx_db')">
                                       <input type="checkbox" value="No" name="chbx_db" id="chbx_db_no" <?php if($Diabetes=="No"){ echo "checked"; }?> onClick="changeChbxColor('chbx_db')">
                                       </span> 
                                    </div>
                                     <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        &nbsp;
                                    </div>                                      
                                </div> 
                            </div> <!-- Col-3 ends  -->
                        </div>	 
                     </div>
                </div>
            </div>           
       </div>
                                       
       <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
            <div class="panel panel-default bg_panel_anesth">
                <div class="panel-heading">
                    <h3 class="panel-title rob"> &nbsp;  &nbsp;</h3>
                    <div class="right_label top_yes_no color_jet">
                        <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 pull-right">
                            <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  Yes  </label>
                            <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  No  </label>	                                                        		
                            <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  &nbsp; </label>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="inner_safety_wrap">
                        <div class="row">
                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                <label class="date_r">
                                    Bleeding Problems
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                <div class="">
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        <span class="colorChkBx" style=" <?php if($BleedingProblems) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_ble_prb_yes','chbx_ble_prb')">
                                        <input type="checkbox" value="Yes" name="chbx_ble_prb" id="chbx_ble_prb_yes" <?php if($BleedingProblems=="Yes"){ echo "checked"; }?> onClick="changeChbxColor('chbx_ble_prb')">
                                        </span>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                         <span class="colorChkBx" style=" <?php if($BleedingProblems) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_ble_prb_no','chbx_ble_prb')">
                                         <input type="checkbox" value="No" name="chbx_ble_prb" id="chbx_ble_prb_no" <?php if($BleedingProblems=="No"){ echo "checked"; }?> onClick="changeChbxColor('chbx_ble_prb')">
                                          </span> 
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        &nbsp;
                                    </div>                                      
                                </div> 
                            </div> <!-- Col-3 ends  -->
                        </div>	 
                     </div>
                     <div class="inner_safety_wrap">
                        <div class="row">
                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                <label class="date_r">
                                   Asthma Lung Disease
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                <div class="">
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        <span class="colorChkBx" style=" <?php if($AsthmaLungDisease) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_ast_ln_dis_yes','chbx_ast_ln_dis')">
                                        <input type="checkbox" value="Yes" name="chbx_ast_ln_dis" id="chbx_ast_ln_dis_yes" <?php if($AsthmaLungDisease=="Yes"){ echo "checked"; }?> onClick="changeChbxColor('chbx_ast_ln_dis')">
                                        </span> 
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                         <span class="colorChkBx" style=" <?php if($AsthmaLungDisease) { echo $whiteBckGroundColor;}?>" onClick="checkSingle('chbx_ast_ln_dis_no','chbx_ast_ln_dis')">
                                          <input type="checkbox" value="No" name="chbx_ast_ln_dis" id="chbx_ast_ln_dis_no" <?php if($AsthmaLungDisease=="No"){ echo "checked"; }?> onClick="changeChbxColor('chbx_ast_ln_dis')">
                                          </span> 
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                        &nbsp;
                                    </div>                                      
                                </div> 
                            </div> <!-- Col-3 ends  -->
                        </div>	 
                     </div>	
                     <div id="descr_21" class="inner_safety_wrap wrap_right_inner_anesth" style="height: auto;">
                        <div class="well well_condensed">
                            <div class="row">
                                <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1">
                                    <label class="date_r">
                                        Other
                                    </label>
                                </div>
                                <div class="col-md-11 col-sm-11 col-xs-11 col-lg-11 text-center">
                                    <textarea id="Field3" name="medicalHistoryOther" class="form-control" style="resize:none;"  ><?php echo stripslashes($medicalHistoryOther);?></textarea>
                                </div> <!-- Col-3 ends  -->
                            </div>
                        </div> 
                     </div>
                </div>
            </div>           
       </div>
       
       <div class="clearfix margin_adjustment_only"></div>
     <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
            <div class="panel panel-default bg_panel_anesth">
                <div class="panel-heading">
                    <a class="panel-title rob alle_link show-pop-trigger2 btn btn-default" onClick="return showPreDefineFn('Allergies_quest', 'Reaction_quest', '10', parseInt($(this).offset().left), parseInt($(this).offset().top)-420),document.getElementById('selected_frame_name_id').value='iframe_allergies_pre_op_gen_anes';"> <span class="fa fa-caret-right"></span>  Allergies to Medications  </a>
                   <div class="right_label color_jet" style="top:10px;">
                        <label onClick="javascript:txt_enable_disable_frame1('iframe_allergies_pre_op_gen_anes','chbx_allergies_to_med_id','Allergies_quest','Reaction_quest',10)">  
						<?php
                        $allergy1 = "Select * from patient_allergies_tbl where patient_confirmation_id=$pConfId";
                        $result = imw_query($allergy1);
                        $num = imw_num_rows($result);													
                        ?>
                        <input type="checkbox"  value="No" id="chbx_allergies_to_med_id" name="chbx_allergies2Medications" <?php if($allergiesNKDA_patientconfirmation_status =='Yes') { echo "checked"; }?> >&nbsp; None</label>
                    </div>
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
                                            //$allgNameWidth="220px";
                                            //$allgReactionWidth="220px";
                                            //$allergies = $allergies2Medications;
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
       </div>
       
       <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
        <div class="panel panel-default bg_panel_anesth">
            <div class="panel-heading">
                <a id="cr_med_div" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " onClick="return showPreDefineMedFn('medication_name', 'medication_detail', '10', parseInt($(this).offset().left)+2, parseInt($(this).offset().top)-185),document.getElementById('selected_frame_name_id').value='iframe_medication_pre_op_gen_anes';"><span class="fa fa-caret-right"></span>&nbsp;Current Medications</a>                   	
                <div class="right_label color_jet" style="top:10px;">
                    <label onClick="javascript:txt_enable_disable_frame1('iframe_medication_pre_op_gen_anes','chbx_curr_to_med_id','medication_name','medication_detail',10)">
					<input type="checkbox" value="No" id="chbx_curr_to_med_id" name="chbx_current2Medications" <?php if($current2Medications=="No") { echo "checked"; }?>>&nbsp; None 
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
                                        $medicNameWidth=225;
                                        $medicDetailWidth=225;
                                        $sprdMedication = $current2Medications;
                                        include("patient_anesthesia_medi_spreadsheet.php");	
                                    ?>
                                 </div>                
                            </div>
<!--- ---- Table --->                     </div>
                         <!-- Col-3 ends  -->
                    </div>	 
                 </div>
            </div> <!-- Panel Body -->
        </div>
    </div>
	<div class="clearfix margin_adjustment_only"></div>
	<div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
		<div class="panel panel-default bg_panel_anesth">
			<div class="panel-heading">
				<h3 class="panel-title rob">Problem w/Previous Anesthesia</h3>
			</div>
<?php if($probPrevAnesthesia=="Yes") { $pnv_dc_desc_display = "block";$pnv_dc_desc_disable = ""; } else { $pnv_dc_desc_display = "none"; $pnv_dc_desc_disable = "disabled"; }?>
			<div class="panel-body">
				<div style="height: auto;" class="inner_safety_wrap wrap_right_inner_anesth" id="">
					<div class="well well_condensed">
						<div class="row">
							<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
								<div class="full_width">
									<label class="date_r f_size"><span class="colorChkBx" style=" <?php if($probPrevAnesthesia) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" onClick="checkSingle('chbx_prob_pre_anes_no','chbx_prob_pre_anes');chk_unchk_prevAnes('chbx_prob_pre_anes_yes','chbx_pnv_yes','chbx_dc_yes','probPrevAnesthesiaDesc_id');changeChbxColor('chbx_prob_pre_anes');" value="No" name="chbx_prob_pre_anes" id="chbx_prob_pre_anes_no" <?php if($probPrevAnesthesia=="No") { echo "checked"; } ?> tabindex="7" /></span> None </label>&nbsp;&nbsp;
									<label class="date_r f_size">
										<input type="checkbox" <?php echo $pnv_dc_desc_disable;?> value="Yes" name="chbx_pnv" id="chbx_pnv_yes"  <?php if($pnv=="Yes") { echo "checked"; } ?> tabindex="7" /> PNV
									</label>
								</div>
								<div class="full_width">
									<label class="date_r f_size">
										<span class="colorChkBx" style=" <?php if($probPrevAnesthesia) { echo $whiteBckGroundColor;}?>"><input type="checkbox" onClick="checkSingle('chbx_prob_pre_anes_yes','chbx_prob_pre_anes');chk_unchk_prevAnes('chbx_prob_pre_anes_yes','chbx_pnv_yes','chbx_dc_yes','probPrevAnesthesiaDesc_id');changeChbxColor('chbx_prob_pre_anes');" value="Yes" name="chbx_prob_pre_anes" id="chbx_prob_pre_anes_yes" <?php if($probPrevAnesthesia=="Yes") { echo "checked"; } ?> tabindex="7"/></span> Yes </label>&nbsp;&nbsp;&nbsp;&nbsp;
									<label class="date_r f_size">
										<input type="checkbox" <?php echo $pnv_dc_desc_disable;?> value="Yes" name="chbx_dc" id="chbx_dc_yes" <?php if($dc=="Yes") { echo "checked"; } ?> tabindex="7" /> DC
									</label>
								</div>                                        
							</div>
							<div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
								<textarea style="resize:none;" class="form-control" <?php echo $pnv_dc_desc_disable;?>  name="probPrevAnesthesiaDesc" id="probPrevAnesthesiaDesc_id" tabindex="6"><?php echo stripslashes($probPrevAnesthesiaDesc);?></textarea> 
							</div> <!-- Col-3 ends  -->
						</div>
					</div> 
				</div>
			</div>
		</div>           
	</div>
	<div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
		<div class="panel panel-default bg_panel_anesth">
			<div class="panel-heading">
				<h3 class="panel-title rob">Family History Of Anesthesia Problems</h3>
			</div>
			<div class="panel-body">
				<div style="height: auto;" class="inner_safety_wrap wrap_right_inner_anesth" id="">
					<div class="well well_condensed">
						<div class="row">
<?php if($familyHistoryAnesthesiaProblems=="No"){$fam_hist_disable = "";}else{$fam_hist_disable = "disabled";}?>
							<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
								<label class="date_r full_width f_size">
									<span class="colorChkBx" style=" <?php if($familyHistoryAnesthesiaProblems) { echo $whiteBckGroundColor;}?>"><input type="checkbox" onClick="checkSingle('chbx_fam_hist_anes_yes','chbx_fam_hist_anes');chk_unchk_family_hist('chbx_fam_hist_anes_no','familyHistoryAnesthesiaProblemsDesc_id');changeChbxColor('chbx_fam_hist_anes');" value="Yes" name="chbx_fam_hist_anes" id="chbx_fam_hist_anes_yes" <?php if($familyHistoryAnesthesiaProblems=="Yes") { echo "checked"; } ?>  tabindex="7" /></span> None </label>
								
								<label class="date_r full_width f_size">
									<span class="colorChkBx" style=" <?php if($familyHistoryAnesthesiaProblems) { echo $whiteBckGroundColor;}?>"><input type="checkbox" onClick="checkSingle('chbx_fam_hist_anes_no','chbx_fam_hist_anes');chk_unchk_family_hist('chbx_fam_hist_anes_no','familyHistoryAnesthesiaProblemsDesc_id');changeChbxColor('chbx_fam_hist_anes');" value="No" name="chbx_fam_hist_anes" id="chbx_fam_hist_anes_no" <?php if($familyHistoryAnesthesiaProblems=="No") { echo "checked"; } ?> tabindex="7" /></span> Yes </label>
							</div>
							
							<div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
								<textarea style="resize:none;" class="form-control" name="familyHistoryAnesthesiaProblemsDesc" id="familyHistoryAnesthesiaProblemsDesc_id" <?php echo $fam_hist_disable;?> tabindex="6"><?php echo stripslashes($familyHistoryAnesthesiaProblemsDesc);?></textarea> 
							</div> <!-- Col-3 ends  -->
						</div>
					</div> 
				</div>
			</div>
		</div>           
	</div>
	
	<div class="clearfix margin_adjustment_only"></div>
<?php
	$chbxSmokeCigPipeBackColor=$chngBckGroundColor;
	if($smoke || $smokeCigarettes || $smokeCigars || $smokePipe) { 
		$chbxSmokeCigPipeBackColor=$whiteBckGroundColor; 
	}
?>
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 bg_panel_anesth">
		<div class="panel panel-default bg_panel_anesth">
			<div class="panel-heading">
				<h3 class="panel-title rob"> Do You </h3>
			</div>
			<div class="panel-body">
				<div class="inner_safety_wrap wrap_right_inner_anesth padding_15">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-1">
							<label  class="margin_top_5">	Smoke: </label>
						</div>
						
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-11">
							<div class="row">
								<div class="col-md-4 col-sm-12 col-xs-6 col-lg-4">
									<div class="full_width wrap_right_inner_anesth">
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $chbxSmokeCigPipeBackColor;?>" ><input type="checkbox" onClick="chk_unchk_smoke('chbx_no_id','chbx_cigaret','chbx_cigar','chbx_pipe','bp_temp','bp_temp2','stop_when_id');changeDiffChbxColor(4,'chbx_no_id','chbx_cigaret','chbx_cigar','chbx_pipe');" value="No" name="chbx_no" id="chbx_no_id" <?php if($smoke=="No") { echo "checked";}?>  tabindex="7"/></span> No
										</label> &nbsp;
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $chbxSmokeCigPipeBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(4,'chbx_no_id','chbx_cigaret','chbx_cigar','chbx_pipe');" value="No" name="chbx_cigaret" id="chbx_cigaret" <?php if($smokeCigarettes=="No") { echo "checked";}?> <?php if($smoke=="No") { echo "disabled"; }else {  }?> tabindex="7" /></span> Cigarettes
										</label> &nbsp;
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $chbxSmokeCigPipeBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(4,'chbx_no_id','chbx_cigaret','chbx_cigar','chbx_pipe');" value="No" name="chbx_cigar" id="chbx_cigar" <?php if($smokeCigars=="No") { echo "checked";}?> <?php if($smoke=="No") { echo "disabled"; }else {  }?> tabindex="7"/></span> Cigars
										</label> &nbsp;
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $chbxSmokeCigPipeBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(4,'chbx_no_id','chbx_cigaret','chbx_cigar','chbx_pipe');" alue="No" name="chbx_pipe" id="chbx_pipe" <?php if($smokePipe=="No") { echo "checked";}?> <?php if($smoke=="No") { echo "disabled"; }else {  }?> tabindex="7"/></span> Pipe
										</label> &nbsp;
									</div>
								</div>
								<div class="col-md-8 col-sm-12 col-xs-6 col-lg-8">
									<div class="rob full_width f_size"> 
										<label class="f_size rob" for="bp_temp"> Packs/Day </label> &nbsp;
											<input class="form-control inline_form_text2" type="text" name="smokePacks" value="<?php echo $smokePacks;?>" onKeyUp="displayText1=this.value" onClick="getShow(405,430,'flag1');" maxlength="2" id="bp_temp" <?php if($smoke=="No") { echo "disabled"; }else {  }?>/><input type="hidden" id="bp" name="bp_hidden">&nbsp;
										
										<label class="f_size rob inline_form_text2" for="bp_temp2"> No. of Years </label>&nbsp;
											<input class="form-control inline_form_text2" type="text" name="smokeYears" value="<?php echo $smokeYears;?>" onKeyUp="displayText1=this.value" onClick="getShow(405,600,'flag2');"  maxlength="2" id="bp_temp2" <?php if($smoke=="No") { echo "disabled"; }else {  }?>/>&nbsp;

<?php if($smokeStopDate==0 || $smokeStopDate=="") { $smokeStopDate = date("m-d-Y"); } ?>

										<label class="f_size rob inline-block"> If Stopped when </label>&nbsp;
										
										<div class="inline-block">
                                           <div class=" input-group inline_input_group" style="top:0px;">
                                           	<div class="input-group   datepickertxt" style="position:relative; " >
												<input type="text" aria-describedby="basic-addon1"  id="stop_when_id" class="form-control  datepickertxt   " name="smokeStopDate" value="<?php echo $smokeStopDate; ?>"  maxlength="10" <?php if($smoke=="No") { echo "disabled"; }else {  }?> >
												<div class="input-group-addon datepicker ">
													<a href="javascript:void(0)">
														<span class="glyphicon glyphicon-calendar"></span>
													</a>
												</div>
											</div>
                                           
                                           </div>
											
										</div>
									</div>
								</div>
							</div>	
						</div>
						<div class="clearfix"></div>
						<div class="clearfix margin_small_only visible-md"></div>
						
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-1">
							<label class="margin_top_5"> Alcohol: </label>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-11">
							<div class="row">
								<div class="col-md-4 col-sm-12 col-xs-6 col-lg-4">
									<div class="full_width wrap_right_inner_anesth inline_select">
										<label class="f_size">
											<span class="colorChkBx" style=" <?php if($alcohol) { echo $whiteBckGroundColor;}?>"><input type="checkbox" onClick="checkSingle('chbx_acl_yes_id','chbx_acl');alcohol_chk_unchk('chbx_acl_yes_id','chbx_acl_no_id','weeksList_id','bp_temp3');changeChbxColor('chbx_acl');$('#weeksList_id').selectpicker('refresh');" value="Yes" name="chbx_acl" id="chbx_acl_yes_id"  <?php if($alcohol=="Yes") { echo "checked";}?> tabindex="7" /></span> Yes</label> &nbsp;
										<label class="f_size">
											<span class="colorChkBx" style=" <?php if($alcohol) { echo $whiteBckGroundColor;}?>"><input type="checkbox" onClick="checkSingle('chbx_acl_no_id','chbx_acl');alcohol_chk_unchk('chbx_acl_yes_id','chbx_acl_no_id','weeksList_id','bp_temp3');changeChbxColor('chbx_acl');" value="No" name="chbx_acl" id="chbx_acl_no_id" <?php if($alcohol=="No") { echo "checked";}?> tabindex="7" /></span> No</label> &nbsp;
										<label class="f_size" for="weeksList_id">Drinks/Week</label>&nbsp;
										<select class="selectpicker form-control" name="weeksList" id="weeksList_id" <?php if($alcohol=="Yes") { }else { echo "disabled"; }?>>
											<option value="">Select</option>
											<option value="1" <?php if($alcoholWeeksList=="1") { echo "selected"; }?>>1</option>
											<option value="2" <?php if($alcoholWeeksList=="2") { echo "selected"; }?>>2</option>
											<option value="3" <?php if($alcoholWeeksList=="3") { echo "selected"; }?>>3</option>
											<option value="4" <?php if($alcoholWeeksList=="4") { echo "selected"; }?>>4</option>
											<option value="5" <?php if($alcoholWeeksList=="5") { echo "selected"; }?>>5</option>
											<option value="6" <?php if($alcoholWeeksList=="6") { echo "selected"; }?>>6</option>
											<option value="7" <?php if($alcoholWeeksList=="7") { echo "selected"; }?>>7</option>
										</select>
									</div>
								</div>
							</div>	
						</div>
						<div class="clearfix"></div>
						<div class="clearfix margin_small_only visible-md"></div>
<?php
	$dentalBackColor=$chngBckGroundColor;
	if($dentures || $cappedTeeth || $permanentBridge || $looseBrokenTeeth || $PeriodontalDisease) { 
		$dentalBackColor=$whiteBckGroundColor; 
	}
?>
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-1">
							<label  class="margin_top_5"> Dental: </label>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12 col-lg-11">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
									<div class="full_width wrap_right_inner_anesth">
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $dentalBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(5,'chbx_dentures_id','chbx_dnt_capteth_id','chbx_dnt_prmbrig_id','chbx_dnt_lbt_id','chbx_PeriodontalDisease_id');" value="Yes" name="chbx_dnt_prmbrig" id="chbx_dnt_prmbrig_id" <?php if($permanentBridge=="Yes") { echo "checked"; }?>  tabindex="7" /></span> Normal </label> &nbsp;
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $dentalBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(5,'chbx_dentures_id','chbx_dnt_capteth_id','chbx_dnt_prmbrig_id','chbx_dnt_lbt_id','chbx_PeriodontalDisease_id');" value="Yes" name="chbx_dentures" id="chbx_dentures_id" <?php if($dentures=="Yes") { echo "checked"; }?>  tabindex="7"/></span> Dentures </label> &nbsp;
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $dentalBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(5,'chbx_dentures_id','chbx_dnt_capteth_id','chbx_dnt_prmbrig_id','chbx_dnt_lbt_id','chbx_PeriodontalDisease_id');" value="Yes" name="chbx_dnt_capteth" id="chbx_dnt_capteth_id" <?php if($cappedTeeth=="Yes") { echo "checked"; }?> tabindex="7" /></span> Capped Teeth </label>&nbsp;
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $dentalBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(5,'chbx_dentures_id','chbx_dnt_capteth_id','chbx_dnt_prmbrig_id','chbx_dnt_lbt_id','chbx_PeriodontalDisease_id');" value="Yes" name="chbx_dnt_lbt" id="chbx_dnt_lbt_id" <?php if($looseBrokenTeeth=="Yes") { echo "checked"; }?>  tabindex="7"/></span> Loose or Broken Teeth </label> &nbsp;
										<label class="f_size">
											<span class="colorChkBx" style=" <?php echo $dentalBackColor;?>"><input type="checkbox" onClick="changeDiffChbxColor(5,'chbx_dentures_id','chbx_dnt_capteth_id','chbx_dnt_prmbrig_id','chbx_dnt_lbt_id','chbx_PeriodontalDisease_id');" value="Yes" name="chbx_PeriodontalDisease" id="chbx_PeriodontalDisease_id" <?php if($PeriodontalDisease=="Yes") { echo "checked"; }?>  tabindex="7"/></span> Periodontal Disease </label> &nbsp;
										<div class="clearfix visible-sm"></div>
										<label class="f_size">
											Other
										</label> &nbsp;
										<input class="form-control inline_form_text2" type="text" name="otherDentalProblems"/>
									</div>
								</div>
							</div>	
						</div>
						<div class="clearfix"></div> 
					</div>
				</div>
			</div>	
		</div>
	</div>
	
	<div class="clearfix margin_adjustment_only"></div>
	
	<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
		<div class="panel panel-default bg_panel_op">
			<div class="panel-body">
				<div class="inner_safety_wrap" id="">
					<div class="well well-sm well_condensed">
						<div class="row">
							<div class="col-md-1 col-sm-1 col-xs-2 col-lg-1">
								<label class="date_r f_size">
								  Comments
								</label>
							</div>
							<div class="clearfix margin_small_only visible-md"></div>
							<div class="col-md-11 col-sm-11 col-xs-10 col-lg-11 text-center">
								<textarea style="resize:none;" class="form-control" name="preOpComplications" tabindex="6"><?php echo stripslashes($preOpComplications);?></textarea> 
							</div><!-- Col-3 ends  -->
						</div>
					</div> 
				  </div>	         
			</div>    
	   </div>
	</div>
	
	<div class="clearfix margin_adjustment_only"></div>
	
	<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
		<div class="panel panel-default bg_panel_op">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
						<div class="inner_safety_wrap">
							<label class="col-md-12 col-lg-6 col-xs-12 col-sm-12"> Who </label>
							<div class="margin_adjustment_only hidden-lg"></div>			
							<div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
								<select class="selectpicker form-control bs-select-hidden" name="whoUserTypeList" onChange="checkPreOpGenUserType(this)" > 
									<option value="">Who</option>
									<option value="Anesthesiologist" <?php if($whoUserType=='Anesthesiologist') { echo 'selected'; }?>>Anesthesia Provider</option>
									<option value="Nurse" <?php if($whoUserType=='Nurse') { echo 'selected'; }?>>Nurse</option>
								</select>
							</div>
						</div>
					</div>

					<div class="col-md-4 col-sm-12 col-lg-4 col-xs-12 ">
						<div class="inner_safety_wrap">
							<label class="col-md-12 col-lg-6 col-xs-12 col-sm-12"> Created By</label>
							<div class="margin_adjustment_only hidden-lg"></div>
<?php
if($whoUserType==''){$blankDisplay = "block";}else{$blankDisplay = "none";}
if($whoUserType=='Anesthesiologist'){$AnesthesiologistDisplay = "block";}else{$AnesthesiologistDisplay = "none";}
if($whoUserType=='Nurse'){$NurseDisplay = "block";}else{$NurseDisplay = "none";}
?>
							<div class="col-md-12 col-lg-6 col-xs-12 col-sm-12" id="preOpGen_BlankId_div" style="display:<?php echo $blankDisplay;?>;<?php echo $chngBckGroundColor;?>;">
								<select class="selectpicker form-control bs-select-hidden" name="preOpGenBlankId_list" id="preOpGen_BlankId"> 
									<option value="">Created By</option>
								</select>
							</div>
<?php
$createdByBackColor=$chngBckGroundColor;
if($createdByUserId){ $createdByBackColor=$whiteBckGroundColor;}
?>
							<div class="col-md-12 col-lg-6 col-xs-12 col-sm-12"  id="preOpGen_AnesthesiologistId_div" style="display:<?php echo $AnesthesiologistDisplay;?>;<?php echo $createdByBackColor;?>;">
								<select class="selectpicker form-control bs-select-hidden" onChange="changeDiffChbxColor(1,'preOpGen_AnesthesiologistId');" name="preOpGenAnesthesiologistId_list" id="preOpGen_AnesthesiologistId" > 
									<option value="">Created By</option>
<?php 
$preOpGenUserNameQry = "select * from users where user_type = 'Anesthesiologist' ORDER BY lname";
$preOpGenUserNameRes = imw_query($preOpGenUserNameQry) or die(imw_error());
$preOpGenUserNumRow = imw_num_rows($preOpGenUserNameRes);
if($preOpGenUserNumRow) {
	while($preOpGenUserRow = imw_fetch_array($preOpGenUserNameRes)) {
		$preOpGenUserId = $preOpGenUserRow["usersId"];
		$preOpGenUserName = $preOpGenUserRow["lname"].", ".$preOpGenUserRow["fname"]." ".$preOpGenUserRow["mname"];
		if($preOpGenUserRow["deleteStatus"]<>'Yes' || $createdByUserId==$preOpGenUserId) {	
?>
									<option value="<?php echo $preOpGenUserId;?>" <?php if($createdByUserId==$preOpGenUserId) { echo 'selected'; }?>><?php echo $preOpGenUserName;?></option>
<?php
		}
	}
}
?>
								</select>
							</div>
							
							<div class="col-md-12 col-lg-6 col-xs-12 col-sm-12" style="display:<?php echo $NurseDisplay;?>;<?php echo $createdByBackColor;?>;" id="preOpGen_NurseId_div" >
								<select class="selectpicker form-control bs-select-hidden" onChange="changeDiffChbxColor(1,'preOpGen_NurseId');" name="preOpGenNurseId_list" id="preOpGen_NurseId"> 
									<option value="">Created By</option>
<?php 
$preOpGenUserNameQry = "select * from users where user_type = 'Nurse' ORDER BY lname";
$preOpGenUserNameRes = imw_query($preOpGenUserNameQry) or die(imw_error());
$preOpGenUserNumRow = imw_num_rows($preOpGenUserNameRes);
if($preOpGenUserNumRow) {
	while($preOpGenUserRow = imw_fetch_array($preOpGenUserNameRes)) {
		$preOpGenUserId = $preOpGenUserRow["usersId"];
		$preOpGenUserName = $preOpGenUserRow["lname"].", ".$preOpGenUserRow["fname"]." ".$preOpGenUserRow["mname"];
		if($preOpGenUserRow["deleteStatus"]<>'Yes' || $createdByUserId==$preOpGenUserId) {	
?>
									<option value="<?php echo $preOpGenUserId;?>" <?php if($createdByUserId==$preOpGenUserId) { echo 'selected'; }?>><?php echo $preOpGenUserName;?></option>
<?php
		}
	}
}
?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
						<div class="inner_safety_wrap">
							<label class="col-md-12 col-lg-6 col-xs-12 col-sm-12"> Relief Nurse</label>
							<div class="margin_adjustment_only hidden-lg"></div>			
							<div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
								<select class="selectpicker form-control bs-select-hidden" name="relivedNurseIdList"> 
									<option value="">Select</option>	
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

</form>




</div>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="pre_op_general_anes.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
</form>
<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "pre_op_general_anes.php";
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