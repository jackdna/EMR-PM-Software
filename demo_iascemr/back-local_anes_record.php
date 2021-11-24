<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$tablename = "localanesthesiarecord";
include("common/linkfile.php");
include_once("admin/classObjectFunction.php"); 
$objManageData = new manageData;
extract($_GET);
//print_r($_REQUEST);
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
//print_r($_GET);
$patient_id = $_GET['patient_id'];
//$ascId = $_SESSION['ascId'];
$pConfId = $_SESSION['pConfId'];
$cancelRecord = $_REQUEST['cancelRecord'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];

$slider_row="#CAD8FD";


// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$surgeonId = $detailConfirmation->surgeonId;
	$anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
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




	if(!$cancelRecord){
		////// FORM SHIFT TO RIGHT SLIDER
			$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$mac_regional_anesthesia_form = $getLeftLinkDetails->mac_regional_anesthesia_form;
			
			if($mac_regional_anesthesia_form=='true'){
				$formArrayRecord['mac_regional_anesthesia_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
				?>
				<script>					
					top.document.forms[0].frameHref.value = 'local_anes_record.php?thisId=4';
					top.document.forms[0].patient_id.value = '<?php echo $patient_id; ?>';
					top.document.forms[0].pConfId.value = '<?php echo $pConfId; ?>';
					//top.document.forms[0].ascId.value = '<?php echo $ascId; ?>';
					top.document.forms[0].innerKey.value = '<?php echo $innerKey; ?>';
					top.document.forms[0].preColor.value = '<?php echo $preColor; ?>';
					top.document.forms[0].submit();
				</script>
				<?php
				exit;
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

		$fieldName="mac_regional_anesthesia_form";
		$pageName = "blank_mainform.php?patient_id=$patient_id&pConfId=$pConfId";
		include("left_link_hide.php");
		$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&fieldName='.$fieldName;
	}	
	/*
	elseif($cancelRecord){
		////// FORM SHIFT TO LEFT SLIDER
		
		$formArrayRecord['mac_regional_anesthesia_form'] = 'true';
		$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
		?>
		<script>					
			top.document.forms[0].frameHref.value = 'blank_mainform.php?thisId=4';
			top.document.forms[0].patient_id.value = '<?php echo $patient_id; ?>';
			top.document.forms[0].pConfId.value = '<?php echo $pConfId; ?>';
			top.document.forms[0].ascId.value = '<?php echo $ascId; ?>';
			top.document.forms[0].innerKey.value = '<?php echo $innerKey; ?>';
			top.document.forms[0].preColor.value = '<?php echo $preColor; ?>';
			top.document.forms[0].submit();
		</script>
		<?php
		////// FORM SHIFT TO LEFT SLIDER
	}*/

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
left join patient_data_tbl on patientconfirmation.ascId = patient_data_tbl.asc_id
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
 $update_status=imw_query("update stub_tbl set patient_status='IOA' where stub_id='$stub_id'");
}
//END UPDATING PATIENT STATUS IN STUB TABLE


if($submitMe){
	$text = $_REQUEST['getText'];
	$tablename = "localanesthesiarecord";
	//Save_eposts($text,$tablename); 
	$localAnesthesiaRecordId = $_REQUEST['localAnesthesiaRecordId'];
	unset($arrayRecord);
	
	$arrayRecord['patientInterviewed'] = $_REQUEST['chbx_pat_inter'];
	$arrayRecord['chartNotesReviewed'] = $_REQUEST['chbx_chart'];
	$arrayRecord['alertOriented'] = $_REQUEST['chbx_alert'];
	$arrayRecord['assistedByTranslator'] = $_REQUEST['chbx_assist'];
	
	$startTimeTemp = $_REQUEST["startTime"];
	//CODE TO CALCULATE START TIME
	if($startTimeTemp<>"") {
		$startTimesplit = explode(":",$startTimeTemp);
		$startTimeFindAmPM = $startTimesplit[1][2];
		if($startTimeFindAmPM == "P" || $startTimeFindAmPM == "p") {
			$startTimesplit[0] = $startTimesplit[0]+12;
		}
		/*
		if($startTimesplit[0]==12 && ($startTimeFindAmPM == "A" || $startTimeFindAmPM == "a")) {
			$startTimesplit[0] = "00";
		}
		*/
		$startTime = $startTimesplit[0].":".$startTimesplit[1][0].$startTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE START TIME
	
	$stopTimeTemp = $_REQUEST["stopTime"];
	//CODE TO CALCULATE STOP TIME
	if($stopTimeTemp<>"") {
		$stopTimesplit = explode(":",$stopTimeTemp);
		$stopTimeFindAmPM = $stopTimesplit[1][2];
		if($stopTimeFindAmPM == "P" || $stopTimeFindAmPM == "p") {
			$stopTimesplit[0] = $stopTimesplit[0]+12;
		}
		/*
		if($stopTimesplit[0]==12 && ($stopTimeFindAmPM == "A" || $stopTimeFindAmPM == "a")) {
			$stopTimesplit[0] = "00";
		}
		*/
		$stopTime = $stopTimesplit[0].":".$stopTimesplit[1][0].$stopTimesplit[1][1].":00";
	}
	//END CODE TO CALCULATE STOP TIME
	
	$arrayRecord['procedurePrimaryVerified'] = $_REQUEST['chbx_proced'];
	$arrayRecord['procedureSecondaryVerified'] = $_REQUEST['chbx_sec_veri'];
	//$arrayRecord['bp'] = $_REQUEST['bp1'].', '.$_REQUEST['bp2'];
	$arrayRecord['bp'] = $_REQUEST['bp1'];
	$arrayRecord['P'] = $_REQUEST['p'];
	$arrayRecord['rr'] = $_REQUEST['rr'];
	$arrayRecord['sao'] = $_REQUEST['sao'];
	$apTimeTemp = $_REQUEST['apTime'];
	//$arrayRecord['apTime'] = $_REQUEST['apTime'];
	
	//apTime saved in database
 
	   $time_splitLocAnes = explode(" ",$apTimeTemp);
	   
	if($time_splitLocAnes[1]=="PM" || $time_splitLocAnes[1]=="pm") {
		
		$time_splitLocAnestime = explode(":",$time_splitLocAnes[0]);
		$apTimeTempIncr=$time_splitLocAnestime[0]+12;
		$apTimeTemp = $apTimeTempIncr.":".$time_splitLocAnestime[1].":00";
		
	}elseif($time_splitLocAnes[1]=="AM" || $time_splitLocAnes[1]=="am") {
		$time_splitLocAnestime = explode(":",$time_splitLocAnes[0]);
		$apTimeTemp=$time_splitLocAnestime[0].":".$time_splitLocAnestime[1].":00";
		if($time_splitLocAnestime[0]=="00" && $time_splitLocAnestime[1]=="00") {
			$apTimeTemp=$time_splitLocAnestime[0].":".$time_splitLocAnestime[1].":01";
		}
	}
	$arrayRecord['apTime'] = $apTimeTemp;
   //apTime saved in database

	$arrayRecord['evaluation2'] = $_REQUEST['evaluation2'];
	$arrayRecord['stableCardiPlumFunction'] = $_REQUEST['chbx__cardiovesc'];
	$arrayRecord['planAnesthesia'] = $_REQUEST['chbx_plan'];
	$arrayRecord['allQuesAnswered'] = $_REQUEST['chbx_all_qus'];
	$arrayRecord['asaPhysicalStatus'] = $_REQUEST['phys_status'];
	$arrayRecord['other'] = $_REQUEST['other'];
	$arrayRecord['evaluation'] = $_REQUEST['evaluation'];
	$arrayRecord['remarks'] = $_REQUEST['txtarea_remarks'];
	$arrayRecord['relivedPreNurseId'] = $_REQUEST['relivedPreNurseIdList'];
	$arrayRecord['relivedIntraNurseId'] = $_REQUEST['relivedIntraNurseIdList'];
	$arrayRecord['relivedPostNurseId'] = $_REQUEST['relivedPostNurseIdList'];
	
	$arrayRecord['routineMonitorApplied'] = $_REQUEST['chbx_routine'];	
	$arrayRecord['blank1_label'] = $_REQUEST['blank1_label'];
	$arrayRecord['blank2_label'] = $_REQUEST['blank2_label'];
	
	//$arrayRecord['blank1'] = $_REQUEST['blank1'];//
	
	$arrayRecord['blank1_1'] = $_REQUEST['blank1_1'];
	$arrayRecord['blank1_2'] = $_REQUEST['blank1_2'];
	$arrayRecord['blank1_3'] = $_REQUEST['blank1_3'];
	$arrayRecord['blank1_4'] = $_REQUEST['blank1_4'];
	$arrayRecord['blank1_5'] = $_REQUEST['blank1_5'];
	$arrayRecord['blank1_6'] = $_REQUEST['blank1_6'];
	$arrayRecord['blank1_7'] = $_REQUEST['blank1_7'];
	$arrayRecord['blank1_8'] = $_REQUEST['blank1_8'];
	$arrayRecord['blank1_9'] = $_REQUEST['blank1_9'];
	$arrayRecord['blank1_10'] = $_REQUEST['blank1_10'];
	
	//$arrayRecord['blank2'] = $_REQUEST['blank2'];//
	
	$arrayRecord['blank2_1'] = $_REQUEST['blank2_1'];
	$arrayRecord['blank2_2'] = $_REQUEST['blank2_2'];
	$arrayRecord['blank2_3'] = $_REQUEST['blank2_3'];
	$arrayRecord['blank2_4'] = $_REQUEST['blank2_4'];
	$arrayRecord['blank2_5'] = $_REQUEST['blank2_5'];
	$arrayRecord['blank2_6'] = $_REQUEST['blank2_6'];
	$arrayRecord['blank2_7'] = $_REQUEST['blank2_7'];
	$arrayRecord['blank2_8'] = $_REQUEST['blank2_8'];
	$arrayRecord['blank2_9'] = $_REQUEST['blank2_9'];
	$arrayRecord['blank2_10'] = $_REQUEST['blank2_10'];
	
	//$arrayRecord['propofol'] = $_REQUEST['propofol'];//
	$arrayRecord['propofol_1'] = $_REQUEST['propofol_1'];
	$arrayRecord['propofol_2'] = $_REQUEST['propofol_2'];
	$arrayRecord['propofol_3'] = $_REQUEST['propofol_3'];
	$arrayRecord['propofol_4'] = $_REQUEST['propofol_4'];
	$arrayRecord['propofol_5'] = $_REQUEST['propofol_5'];
	$arrayRecord['propofol_6'] = $_REQUEST['propofol_6'];
	$arrayRecord['propofol_7'] = $_REQUEST['propofol_7'];
	$arrayRecord['propofol_8'] = $_REQUEST['propofol_8'];
	$arrayRecord['propofol_9'] = $_REQUEST['propofol_9'];
	$arrayRecord['propofol_10'] = $_REQUEST['propofol_10'];

	//$arrayRecord['midazolam'] = $_REQUEST['midazolam'];	//
	$arrayRecord['midazolam_1'] = $_REQUEST['midazolam_1'];
	$arrayRecord['midazolam_2'] = $_REQUEST['midazolam_2'];
	$arrayRecord['midazolam_3'] = $_REQUEST['midazolam_3'];
	$arrayRecord['midazolam_4'] = $_REQUEST['midazolam_4'];
	$arrayRecord['midazolam_5'] = $_REQUEST['midazolam_5'];
	$arrayRecord['midazolam_6'] = $_REQUEST['midazolam_6'];
	$arrayRecord['midazolam_7'] = $_REQUEST['midazolam_7'];
	$arrayRecord['midazolam_8'] = $_REQUEST['midazolam_8'];
	$arrayRecord['midazolam_9'] = $_REQUEST['midazolam_9'];
	$arrayRecord['midazolam_10'] = $_REQUEST['midazolam_10'];
	
	//$arrayRecord['diprivan'] = $_REQUEST['diprivan'];
	
	//$arrayRecord['Fentanyl'] = $_REQUEST['Fentanyl'];//	
	$arrayRecord['Fentanyl_1'] = $_REQUEST['Fentanyl_1'];
	$arrayRecord['Fentanyl_2'] = $_REQUEST['Fentanyl_2'];
	$arrayRecord['Fentanyl_3'] = $_REQUEST['Fentanyl_3'];
	$arrayRecord['Fentanyl_4'] = $_REQUEST['Fentanyl_4'];
	$arrayRecord['Fentanyl_5'] = $_REQUEST['Fentanyl_5'];
	$arrayRecord['Fentanyl_6'] = $_REQUEST['Fentanyl_6'];
	$arrayRecord['Fentanyl_7'] = $_REQUEST['Fentanyl_7'];
	$arrayRecord['Fentanyl_8'] = $_REQUEST['Fentanyl_8'];
	$arrayRecord['Fentanyl_9'] = $_REQUEST['Fentanyl_9'];
	$arrayRecord['Fentanyl_10'] = $_REQUEST['Fentanyl_10'];
	
	//$arrayRecord['ketamine'] = $_REQUEST['ketamine'];//
	$arrayRecord['ketamine_1'] = $_REQUEST['ketamine_1'];
	$arrayRecord['ketamine_2'] = $_REQUEST['ketamine_2'];
	$arrayRecord['ketamine_3'] = $_REQUEST['ketamine_3'];
	$arrayRecord['ketamine_4'] = $_REQUEST['ketamine_4'];
	$arrayRecord['ketamine_5'] = $_REQUEST['ketamine_5'];
	$arrayRecord['ketamine_6'] = $_REQUEST['ketamine_6'];
	$arrayRecord['ketamine_7'] = $_REQUEST['ketamine_7'];
	$arrayRecord['ketamine_8'] = $_REQUEST['ketamine_8'];
	$arrayRecord['ketamine_9'] = $_REQUEST['ketamine_9'];
	$arrayRecord['ketamine_10'] = $_REQUEST['ketamine_10'];
	
	//$arrayRecord['labetalol'] = $_REQUEST['labetalol'];//
	$arrayRecord['labetalol_1'] = $_REQUEST['labetalol_1'];
	$arrayRecord['labetalol_2'] = $_REQUEST['labetalol_2'];
	$arrayRecord['labetalol_3'] = $_REQUEST['labetalol_3'];
	$arrayRecord['labetalol_4'] = $_REQUEST['labetalol_4'];
	$arrayRecord['labetalol_5'] = $_REQUEST['labetalol_5'];
	$arrayRecord['labetalol_6'] = $_REQUEST['labetalol_6'];
	$arrayRecord['labetalol_7'] = $_REQUEST['labetalol_7'];
	$arrayRecord['labetalol_8'] = $_REQUEST['labetalol_8'];
	$arrayRecord['labetalol_9'] = $_REQUEST['labetalol_9'];
	$arrayRecord['labetalol_10'] = $_REQUEST['labetalol_10'];
	
	//$arrayRecord['spo2'] = $_REQUEST['SpO'];//
	$arrayRecord['spo2_1'] = $_REQUEST['spo2_1'];
	$arrayRecord['spo2_2'] = $_REQUEST['spo2_2'];
	$arrayRecord['spo2_3'] = $_REQUEST['spo2_3'];
	$arrayRecord['spo2_4'] = $_REQUEST['spo2_4'];
	$arrayRecord['spo2_5'] = $_REQUEST['spo2_5'];
	$arrayRecord['spo2_6'] = $_REQUEST['spo2_6'];
	$arrayRecord['spo2_7'] = $_REQUEST['spo2_7'];
	$arrayRecord['spo2_8'] = $_REQUEST['spo2_8'];
	$arrayRecord['spo2_9'] = $_REQUEST['spo2_9'];
	$arrayRecord['spo2_10'] = $_REQUEST['spo2_10'];
	
	//$arrayRecord['o2lpm'] = $_REQUEST['O2lpm'];//
	$arrayRecord['o2lpm_1'] = $_REQUEST['o2lpm_1'];
	$arrayRecord['o2lpm_2'] = $_REQUEST['o2lpm_2'];
	$arrayRecord['o2lpm_3'] = $_REQUEST['o2lpm_3'];
	$arrayRecord['o2lpm_4'] = $_REQUEST['o2lpm_4'];
	$arrayRecord['o2lpm_5'] = $_REQUEST['o2lpm_5'];
	$arrayRecord['o2lpm_6'] = $_REQUEST['o2lpm_6'];
	$arrayRecord['o2lpm_7'] = $_REQUEST['o2lpm_7'];
	$arrayRecord['o2lpm_8'] = $_REQUEST['o2lpm_8'];
	$arrayRecord['o2lpm_9'] = $_REQUEST['o2lpm_9'];
	$arrayRecord['o2lpm_10'] = $_REQUEST['o2lpm_10'];
	
	//$arrayRecord['ekg'] = $_REQUEST['ekg'];	
	$arrayRecord['startTime'] = $startTime;
	$arrayRecord['stopTime'] = $stopTime;
	$arrayRecord['local_anes_revaluation2'] = $_REQUEST['local_anes_revaluation2'];	
	$arrayRecord['ivCatheter'] = $_REQUEST['chbx_no'];
	$arrayRecord['hand_right'] = $_REQUEST['chbx_hand_right'];
	$arrayRecord['hand_left'] = $_REQUEST['chbx_hand_left'];
	$arrayRecord['wrist_right'] = $_REQUEST['chbx_wrist_right'];
	$arrayRecord['wrist_left'] = $_REQUEST['chbx_wrist_left'];
	$arrayRecord['arm_right'] = $_REQUEST['chbx_arm_right'];
	$arrayRecord['arm_left'] = $_REQUEST['chbx_arm_left'];
	$arrayRecord['anti_right'] = $_REQUEST['chbx_anti_right'];
	$arrayRecord['anti_left'] = $_REQUEST['chbx_anti_left'];
	$arrayRecord['topi_peri_retro'] = $_REQUEST['chbx_topi_peri_retro'];	
	$arrayRecord['topical'] = $_REQUEST['topical'];	
	$arrayRecord['ivCatheterOther'] = $_REQUEST['other_reg_anes'];
	$arrayRecord['lidocaine2'] = $_REQUEST['chbx_lido2'];
	$arrayRecord['lidocaine3'] = $_REQUEST['chbx_lido3'];
	$arrayRecord['Peribulbar'] = $_REQUEST['peribulbar'];
	$arrayRecord['lidocaine4'] = $_REQUEST['chbx_lido4'];
	$arrayRecord['Bupiyicaine5'] = $_REQUEST['chbx_bupi'];
	$arrayRecord['Retrobulbar'] = $_REQUEST['retrobulbar'];
	$arrayRecord['ugcc'] = $_REQUEST['chbx_epi'];
	$arrayRecord['Hyalauronidase'] = $_REQUEST['hyalauronidase'];
	$arrayRecord['regionalAnesthesiaOther'] = $_REQUEST['otherRegionalAnesthesia'];
	$arrayRecord['vanlindt'] = $_REQUEST['vanLindt'];
	$arrayRecord['none'] = $_REQUEST['chbx_none'];
	$arrayRecord['digital'] = $_REQUEST['chbx_digi'];
	$arrayRecord['honanballon'] = $_REQUEST['honanBallon'];
	$arrayRecord['anyKnowAnestheticComplication'] = $_REQUEST['chbx_anes'];
	$arrayRecord['stableCardiPlumFunction2'] = $_REQUEST['chbx_pulm'];
	$arrayRecord['satisfactoryCondition4Discharge'] = $_REQUEST['chbx_dis'];
	$arrayRecord['surgeonId'] = $surgeonId;	
	$arrayRecord['ascId'] = $ascId;
	$arrayRecord['confirmation_id'] = $pConfId;	
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['surgeonSign'] = $_REQUEST['elem_signature1'];
	$arrayRecord['anesthesiologistSign'] = $_REQUEST['elem_signature2'];	
	$arrayRecord['applet_data'] = $_REQUEST['applet_data'];	
	
	//CODE TO SET FORM STATUS 
	$arrayRecord['form_status'] = 'completed';
	
	if($_REQUEST['chbx_pat_inter']=='' || $_REQUEST['chbx_chart']=='' 
	   || $_REQUEST['chbx_alert']=='' || $_REQUEST['chbx_assist']==''
	   || $_REQUEST['phys_status']=='' 
	   || ($_REQUEST['chbx_hand_right']=='' && $_REQUEST['chbx_hand_left']=='')
	   || ($_REQUEST['chbx_wrist_right']=='' && $_REQUEST['chbx_wrist_left']=='')
	   || ($_REQUEST['chbx_arm_right']=='' && $_REQUEST['chbx_arm_left']=='')
	   || ($_REQUEST['chbx_anti_right']=='' && $_REQUEST['chbx_anti_left']=='')
	   || $_REQUEST['chbx_topi_peri_retro']==''
	   || ($_REQUEST['chbx_none']=='' && $_REQUEST['chbx_digi']=='' && $_REQUEST['honanBallon']=='')
	   || ($_REQUEST['chbx_anes']=='' && $_REQUEST['chbx_pulm']=='' && $_REQUEST['chbx_dis']=='')
	   || trim($_REQUEST['evaluation'])==''
	  )
	  {
		$arrayRecord['form_status'] = 'not completed';
	  }
	//END CODE TO SET FORM STATUS
	
	if($localAnesthesiaRecordId){
		$objManageData->updateRecords($arrayRecord, 'localanesthesiarecord', 'localAnesthesiaRecordId', $localAnesthesiaRecordId);
	}else{
		$objManageData->addRecords($arrayRecord, 'localanesthesiarecord');
	}	
	//CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 	
	$patient_id = ($_REQUEST['patient_id']!=0)?$_REQUEST['patient_id']:11;
	?>
		<script>					
			top.document.forms[0].frameHref.value = 'local_anes_record.php?thisId=4';
			top.document.forms[0].patient_id.value = '<?php echo $patient_id; ?>';
			top.document.forms[0].pConfId.value = '<?php echo $pConfId; ?>';
			//top.document.forms[0].ascId.value = '<?php echo $ascId; ?>';
			top.document.forms[0].innerKey.value = '<?php echo $innerKey; ?>';
			top.document.forms[0].preColor.value = '<?php echo $preColor; ?>';
			top.document.forms[0].SaveForm_alert.value = 'true';
			top.document.forms[0].submit(); 
		</script>
	<?php
	//END CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 
}

?>

<script type="text/javascript">
	function changeSliderColor(){
		var setColor = '<?php echo $bglight_blue_local_anes; ?>';
		top.changeColor(setColor);
	}
	top.yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');

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
   //document.getElementById('newTime').innerHTML=t;
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
	//document.applets["signs"].unDoIt();	
	//getAppValue();
	var coords = document.applets["signs"].unDoIt();
	document.getElementById("applet_data").value=coords;
}
function redoApp()
{
	//document.applets["signs"].reDoIt();
	//getAppValue();
	var coords = document.applets["signs"].reDoIt();
	document.getElementById("applet_data").value=coords;
}
function setText(o)
{
	document.applets["signs"].activateText(o);
}
function getAppValue()
{
	//alert("Hello1")
	var coords = document.applets["signs"].getDrawing();
	document.getElementById("applet_data").value=coords;
	//alert("bye2")
} 

//Applet



//SHOW TIME INTERVAL
	function showInterval(objvalue,intervalId_td) {
		//alert(objvalue);
		
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
						
						//alert(showStartIntervalTime + document.getElementById('intervalId').innerHTML);
						document.getElementById(intervalId_td).innerHTML = showStartIntervalTime;
				}
				
			}
		}		
		
		
		//var startTimeSplit = split(':',startTime);
		//alert(startTimeSplit[0]);
	}
//SHOW TIME INTERVAL


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
		//var ascId1 = '<?php echo $_REQUEST["ascId"];?>';

		
		//var url="pre_op_nursing_record_ajaxSign.php";
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
		url=url+"&userIdentity="+userIdentity
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		//url=url+"&ascId="+ascId1
		if(delSign) {
			url=url+"&delSign=yes"
		}
		//alert(url);
		//alert(vitalSignBP_main_ajax+"\n"+vitalSignP_main_ajax+"\n"+vitalSignR_main_ajax+"\n"+vitalSignTime_main_ajax);
		
		xmlHttp.onreadystatechange=displayUserSignFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function displayUserSignFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				
				//alert(xmlHttp.responseText);
				var objId = document.getElementById('hiddSignatureId').value;
				document.getElementById(objId).innerHTML=xmlHttp.responseText;
				top.setPNotesHeight();
			}
	}
	
	//End Display Signature Of Nurse
	
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

</script>
<script src="js/epost.js"></script>
<body onClick="document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.main_frmInner.hideSliders();">
<div id="post" style="display:none;"></div>
<?php
$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'localanesthesiarecord' AND patient_conf_id = '$pConfId' ";
$rsNotes =imw_query($query_rsNotes);
$totalRows_rsNotes =imw_num_rows($rsNotes);

//include("common/pre_defined_popup.php");
//FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION
	function calculate_timeFun($MainTime) {
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
		if($time_split[0]=="00") {
			$time_split[0] = 12; 
		}
		$MainTime = $time_split[0].":".$time_split[1].$am_pm;
		return $MainTime;
	}	
//END FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION

// GETTTING LOCAL ANES RECORD IF EXISTS
	$localAnesRecordDetails = $objManageData->getExtractRecord('localanesthesiarecord', 'confirmation_id', $pConfId);
		if($localAnesRecordDetails){
			extract($localAnesRecordDetails);
			//list($bp1, $bp2) = explode(", ", $bp);
			
			$startTime=calculate_timeFun($startTime); //CODE TO DISPLAY START TIME
			$stopTime=calculate_timeFun($stopTime); //CODE TO DISPLAY STOP TIME
			
			//CODE TO SET $apTime 
				if($apTime=="00:00:00" || $apTime=="") {
					
				$apTime=date("h:i A");
				}else {
					
					$apTime=$apTime;
					
					$time_split_apTime = explode(":",$apTime);
					if($time_split_apTime[0]>=12) {
						$am_pm = "PM";
					}else {
						$am_pm = "AM";
					}
					if($time_split_apTime[0]>=13) {
						$time_split_apTime[0] = $time_split_apTime[0]-12;
						if(strlen($time_split_apTime[0]) == 1) {
							$time_split_apTime[0] = "0".$time_split_apTime[0];
						}
					}else {
						//DO NOTHNING
					}
					//echo $time_split_apTime[1];
					$apTime = $time_split_apTime[0].":".$time_split_apTime[1]." ".$am_pm;
				}
			//END CODE TO SET apTime
			
		}
// GETTTING LOCAL ANES RECORD IF EXISTS

?>
<form action="local_anes_record.php?submitMe=true" name="frm_local_anes_rec" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
	<input type="hidden" name="divId">
	<input type="hidden" name="counter">
	<input type="hidden" name="secondaryValues">
	<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
	<input type="hidden" name="formIdentity" value="healthQues">	
	<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="patient_id" value="<?php echo $_GET['patient_id']; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<!--<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">-->
	<input type="hidden" name="localAnesthesiaRecordId" value="<?php echo $localAnesthesiaRecordId; ?>">
	<input type="hidden" name="getText" id="getText">	
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
<table bgcolor="<?php echo $tablebg_local_anes; ?>" width="985" border="0" onDblClick="closePreDefineDiv();closeCal2();" onMouseOver="closePreDefineDiv(); medi_close('evaluationPreDefineMedDiv'); medi_close('evaluationEvaluationDiv');medi_close('evaluationLocalAnesEvaluationDiv');" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td><img src="form_design/images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td  valign="top" align="center">
			<table border="0" align="center" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="6" align="right"><img src="images/leftblu.gif" width="3" height="24"></td>
					<td width="80%"  nowrap="nowrap" align="center" valign="middle" bgcolor="<?php echo $bgdark_blue_local_anes ;?>"  class="text_10b1" ><span style=" color:<?php echo $title1_color ;?>">Local/Regional Anesthesia Record</span></td>
					<td align="left" valign="top" width="10"><img src="images/rightblue.gif" width="3" height="24"></td>
					<td>&nbsp;</td><td nowrap id="epostDelId"> <?php while($row = imw_fetch_array($rsNotes)) { if($totalRows_rsNotes > 0) { ?> <img src="images/sticky_note.gif" onMouseOver="showEpost('<?php echo $row['epost_id'];?>')">  <?php } } ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><img src="images/tpixel.gif" width="4" height="1"></td>
	</tr>
	<tr>
		<td align="left" style="padding-left:350px; padding-top:25px;">
			<div id="divSaveAlert" style="position:absolute;left:350; top:220; display:none;">
				<?php 
					$bgCol = $bgdark_blue_local_anes;
					$borderCol = $tablebg_local_anes;
					//$leftSrc = 'images/leftblu.gif';
					//$rightSrc = 'images/rightblue.gif';
					include('saveDivPopUp.php'); 
				?>
			</div>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table class="txt" width="99%" height="100%" bgcolor="#FFFFFF" align="center"   bordercolor="#7CBA95" cellpadding="0" cellspacing="0" style="border:1px solid #AFCAA0; ">
				<tr>
					<td  valign="top"></td>
				<tr>
				<tr >
					<td valign="top"  width="100%">
						<table border="0" align="left" cellpadding="0" cellspacing="0" >
							<tr>
								<td width="4" align="right"><img src="images/leftblu.gif" width="3" height="24"></td>
								<td width="66%"  nowrap="nowrap" align="left" valign="middle" bgcolor="<?php echo $bgdark_blue_local_anes ;?>"  class="text_10b1" ><span style=" color:<?php echo $title1_color ;?>">Pre-Operative</span></td>
								<td align="left" valign="top" width="10"><img src="images/rightblue.gif" width="3" height="24"></td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="text_10" valign="top" width="100%" height="100%">
						<table border="0" rules="none" cellspacing="0" class="all_border" style="border-color:<?php echo $border_blue_local_anes ;?>"  width="100%" height="100%">
							<tr class="text_10" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
								<td colspan="1" width="16%"><img src="images/tpixel.gif" />Patient Interviewed</td>
								<td width="5%" height="22" align="center" valign="top" class="text_10 pad_top_bottom">
									<input class="field checkbox"  type="checkbox" <?php if($patientInterviewed=='Yes') echo "CHECKED"; ?> value="Yes" id="chbx_pat_inter_id" name="chbx_pat_inter"  tabindex="7"></td>
								<td width="23%">Chartnote Reviewed</td>
								<td width="8%" height="22" align="center" valign="top" class="text_10 pad_top_bottom">
									<input class="field checkbox"  type="checkbox" <?php if($chartNotesReviewed=='Yes') echo "CHECKED"; ?> value="Yes" id="chbx_chart" name="chbx_chart"  tabindex="7">							  </td>
								<td width="19%" nowrap><img src="images/tpixel.gif" width="2" />Alert and Oriented X 3</td>
								<td width="6%" height="22" align="center" valign="top" class="text_10 pad_top_bottom">
									<input class="field checkbox"  type="checkbox" <?php if($alertOriented=='Yes') echo "CHECKED"; ?> value="Yes" id="chbx_alert" name="chbx_alert"  tabindex="7">
								</td>
								<td width="19%" align="right">Assisted by Translator <img src="images/tpixel.gif" width="3" /></td>
								<td width="4%" height="22" align="left" valign="top" class="text_10 pad_top_bottom">
									<input class="field checkbox" <?php if($assistedByTranslator=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_assist_id" name="chbx_assist"  tabindex="7">
								</td>
							</tr>
							<tr class="text_10">
								<td class="text_10"><img src="images/tpixel.gif"/>Procedure Verified</td>
								<td></td>
								<td align="left" class="text_10"><span style="font-weight=100;"><?php echo $patient_primary_procedure; ?></span></td>
								<td align="center"><input <?php if($procedurePrimaryVerified=='Yes') echo "CHECKED"; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_proced_id" name="chbx_proced"  tabindex="7"></td>
								<td align="left" class="text_10">Secondary Verified<img src="images/tpixel.gif" /></td>
								<td valign="top" class="text_10 pad_top_bottom"></td>
								<td align="center"><?php echo $patient_secondary_procedure; ?></td>
								<td><input <?php if($procedureSecondaryVerified=='Yes') echo "CHECKED"; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_sec_veri_id" name="chbx_sec_veri"  tabindex="7"></td>
							</tr>
							<tr class="text_10" bgcolor="<?php echo $bglight_blue_local_anes; ?>" height="22">
								<td class="text_10b"  colspan="1" align="left" style="color:#800080;cursor:hand;" onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', '10', '35', '120'),document.getElementById('selected_frame_name_id').value='iframe_allergies_local_anes_rec';"><img src="images/tpixel.gif" /><img src="images/tpixel.gif" /> Allergies </td>
								<td colspan="3"> <span style="font-weight=100;"></span></td>
								<td class="text_10b"  colspan="4" align="left" style="color:#800080;cursor:hand;" onClick="return showPreDefineMedFnNew('medication_name', 'medication_detail', '10', '530', '120'),document.getElementById('selected_frame_name_id').value='iframe_medication_local_anes_rec';">Medications<img src="images/tpixel.gif" width="17" height="1" />
									<span style="font-weight=100;"></span>
								</td>
							</tr>							
							<tr  class="text_10">
								<!-- ALERGIES FRAME 1 -->
								<td colspan="4" align="left" width="50%">
									<table width="100%" border="0" cellpadding="0" align="left" cellspacing="0" bordercolor="#333333">
										<tr bgcolor="#FFFFFF">
											<td>&nbsp;<img src="images/tpixel.gif" width="25" height="1"></td>
											<td width="436" class="text_10  pad_top_bottom">Name</td>
											<td width="512" colspan="4" class="text_10  pad_top_bottom">
												<img src="images/tpixel.gif" width="20" height="1">Reaction
											</td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td colspan="6" bgcolor="#F1F4F0" class="text_10">
												<!-- <iframe name="iframe_allergies_local_anes_rec" src="local_anes_rec_allergies_spreadsheet.php" width="495" height="95"  frameborder="0"  scrolling="yes" ></iframe> -->   
												<iframe name="iframe_allergies_local_anes_rec" src="health_quest_spreadsheet.php?pConfId=<?php echo $pConfId; ?>&patient_id=<?php echo $patient_id; ?>&ascId=<?php echo $ascId; ?>&allgNameWidth=240&allgReactionWidth=240" width="100%" height="95"  frameborder="0"  scrolling="yes" ></iframe>   
											</td>
										</tr>  
									</table>
								</td>
								<!-- ALERGIES FRAME 1 -->
								
								<!-- ALERGIES FRAME 2 -->
								<td colspan="4" align="left" width="50%">
									<table width="100%"  class="text_10" border="0" cellpadding="0" align="left" cellspacing="0" bordercolor="#333333" >
										<tr bgcolor="#FFFFFF">
											<td>&nbsp;<img src="images/tpixel.gif" width="25" height="1"></td>
											<td width="436" class="text_10  pad_top_bottom">Name</td>
											<td width="512" colspan="4" class="text_10  pad_top_bottom">
												<img src="images/tpixel.gif" width="20" height="1">Details
											</td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td colspan="6" bgcolor="#F1F4F0">
												<!-- <iframe name="iframe_medication_local_anes_rec" src="local_anes_rec_medication_spreadsheet.php" width="100%"   height="95"  frameborder="0"  scrolling="yes" ></iframe>      -->
												<iframe name="iframe_medication_local_anes_rec" src="patient_prescription_medi_spreadsheet.php?pConfId=<?php echo $pConfId; ?>&patient_id=<?php echo $patient_id; ?>&ascId=<?php echo $ascId; ?>&medicNameWidth=220&medicDetailWidth=220" width="100%"   height="95"  frameborder="0"  scrolling="yes" ></iframe>     
											</td>
										</tr>
									</table>
								</td>
								<!-- ALERGIES FRAME 2 -->
							</tr>
							<tr>
								<td align="left" colspan="8" valign="middle">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tr height="50" valign="middle" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
											<input type="hidden" id="bp" name="bp_hidden"><input type="hidden" name="currTime">
											<td width="38" align="left" class="text_10">&nbsp;BP:</td>															
											<td width="62" colspan"2" align="left">
												<table>
													<tr>
														<td>
															<input id="bp_temp" type="text" value="<?php echo $bp; ?>" name="bp1" maxlength="6" size="6" class="text_10 all_border" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ;" onKeyUp="displayText1=this.value" onClick="getShow(280,25,'flag1');" >												
														</td>
														<!-- 
														<td> / </td>
														<td><input id="bp_temp2" type="text" value="<?php echo $bp2; ?>" size="2" name="bp2" onKeyUp="displayText2=this.value" class="text_10 all_border" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ;" onClick="getShow(250,70,'flag2');"></td>
													 	-->
													</tr>
												</table>
											</td>
											<td width="27"></td>
											<td width="22" align="left" class="text_10">P:</td>
											<td width="22" align="left"><input id="bp_temp3" type="text" name="p" value="<?php echo $P; ?>" size="2" onKeyUp="displayText3=this.value" class="text_10 all_border" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ;" onClick="getShow(280,110,'flag3');"></td>
											<td width="27"></td>
											<td width="39" align="left" class="text_10">RR:</td>
											<td width="22" align="left"><input id="bp_temp4" type="text" value="<?php echo $rr; ?>" name="rr" size="2" onKeyUp="displayText4=this.value" class="text_10 all_border" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ;" onClick="getShow(280,170,'flag4');"></td>
											<td width="27"></td>
											<td width="52" align="left" class="text_10">SaO<sub>2</sub>:</td>
											<td width="22" align="left"><input id="bp_temp5" type="text" value="<?php echo $sao; ?>" name="sao" size="2" onKeyUp="displayText5=this.value" class="text_10 all_border" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ;" onClick="getShow(280,240,'flag5');"></td>
											<td width="27"></td>
											<td width="31" align="left" valign="middle"><img src="images/clock.gif" onClick="return displayTimeAmPm('bp_temp98');"/>
										  </td>												
											<td width="21"></td>
											<!-- <td align="left" width="100" id="newTime" nowrap><?php echo $apTime; ?></td>	 -->										
											<td align="left" width="140" nowrap><input type="text"  tabindex="1"  name="apTime" id="bp_temp98" onKeyUp="displayText98=this.value" onClick="getShow(280,300,'flag98');"  value="<?php echo $apTime;?>" class="field text_10" style=" border:1px solid #ccccc; width:80px;"/></td>	
											<td width="20"></td>
											<td width="113"  align="right" class="text_10b" style="color:#800080;cursor:hand;" onClick="return showEvaluationLocalAnesFn('local_anes_revaluation2_id', '', 'no', '380', '100'),document.getElementById('selected_frame_name_id').value='';">&nbsp;Evaluation</td>
											<td width="484"  align="center" valign="middle" class="text_10 pad_top_bottom">
												<textarea name="evaluation2" id="local_anes_revaluation2_id" class="field textarea justi text_10" style="border:1px solid #cccccc; width:350px; " rows="4" cols="100" tabindex="6"><?php echo $evaluation2; ?></textarea>
											</td>											
										</tr>
										<tr align="left" >
											<td colspan="19" class="text_10">
												<span style="font-weight=100;"><img src="images/tpixel.gif" width="7" height="1" />Stable cardiovascular and Pulmonary function</span>
												<input <?php if($stableCardiPlumFunction=='Yes') echo "CHECKED"; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_cardiovesc_id" name="chbx__cardiovesc"  tabindex="7">
											</td>											
									  	</tr>
										<tr align="left" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
											<td colspan="19" class="text_10">
												<span style="font-weight=100;"><img src="images/tpixel.gif" width="7" height="1" />Plan regional anesthesia with sedation.Risks,benefits and alternatives of anesthesia plan have been discussed.</span>
												<input <?php if($planAnesthesia=='Yes') echo "CHECKED"; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_plan_id" name="chbx_plan"  tabindex="7">
											</td>
									  	</tr>
										<tr class="text_10">
											<td colspan="19" align="left">
												<table border="0" cellpadding="0" cellspacing="0" class="text_10" align="center" width="100%">
													
													<tr valign="middle">
														<td nowrap="nowrap" colspan="3">
															<img src="images/tpixel.gif" width="4" />All questions answered<img src="images/tpixel.gif" width="4" />		
															<input <?php if($allQuesAnswered=='Yes') echo "CHECKED"; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_all_qus_id" name="chbx_all_qus"  tabindex="7">
														</td>
														<td class="text_10" nowrap>ASA Physical Status<span style="font-weight:100"></span></td>
														<td>I </td>
														<td onClick="javascript:checkSingle('phys_status_1','phys_status')"><input <?php if($asaPhysicalStatus=='1') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="1" id="phys_status_1"  class="field checkbox" tabindex="7" /></td>
														<td>II </td>
														<td onClick="javascript:checkSingle('phys_status_2','phys_status')"><input <?php if($asaPhysicalStatus=='2') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="2" id="phys_status_2"  class="field checkbox" tabindex="7" /></td>
														<td>III </td>
														<td onClick="javascript:checkSingle('phys_status_3','phys_status')"><input <?php if($asaPhysicalStatus=='3') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="3" id="phys_status_3"  class="field checkbox" tabindex="7" /></td>
														<!-- <td>IV </td>
														<td onClick="javascript:checkSingle('phys_status_4','phys_status')"><input <?php if($asaPhysicalStatus=='4') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="4" id="phys_status_4"  class="field checkbox" tabindex="7" /></td>
														<td>V</td>
														<td onClick="javascript:checkSingle('phys_status_5','phys_status')"><input <?php if($asaPhysicalStatus=='5') echo "CHECKED"; ?> type="checkbox" name="phys_status" value="5" id="phys_status_5"  class="field checkbox" tabindex="7" /></td>
														 -->
														 <td align="left" valign="middle" class="text_10">Other
															<input type="text" id="txt_field05" name="other" class="field text text_10" style=" vertical-align:middle; border:1px solid #ccccc; width:160px;" tabindex="1" value="<?php echo $other; ?>"/>
														</td>
														<td width="10"></td>
														<?php
														//START COMMON CODE FOR ANESTHESIOLOGIST THREE SIGNATURE
															$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
															$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
															$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
															
															$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
															$loggedInUserType = $ViewUserNameRow["user_type"];
															$loggedInSignatureOfUser = $ViewUserNameRow["signature"];
															
															if($loggedInUserType<>"Anesthesiologist") {
																$loginUserName = $_SESSION['loginUserName'];
																
																$callJavaFunPreOp = "return noAuthorityFun('Anesthesiologist');";
																$callJavaFunIntraOp = "return noAuthorityFun('Anesthesiologist');";
																$callJavaFunPostOp = "return noAuthorityFun('Anesthesiologist');";
																
															
															//}else if ($loggedInUserType=="Anesthesiologist" && !$loggedInSignatureOfUser) {
																//$callJavaFunPreOp = "return noSignInAdmin();";
																//$callJavaFunIntraOp = "return noSignInAdmin();";
																//$callJavaFunPostOp = "return noSignInAdmin();";
																
															}else {
																$loginUserId = $_SESSION["loginUserId"];
																
																$callJavaFunPreOp = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia1');";
																$callJavaFunIntraOp = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia2');";
																$callJavaFunPostOp = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia3SignatureId'; return displaySignature('TDanesthesia3NameId','TDanesthesia3SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia3');";
																
															}
														//END COMMON CODE FOR ANESTHESIOLOGIST THREE SIGNATURE
															
															//CODE RELATED TO ANES 1 SIGNATURE ON FILE
																$anesthesia1SignOnFileStatus = "Yes";
																$TDanesthesia1NameIdDisplay = "block";
																$TDanesthesia1SignatureIdDisplay = "none";
																$Anesthesia1Name = $loggedInUserName;
																
																if($signAnesthesia1Id<>0 && $signAnesthesia1Id<>"") {
																	$Anesthesia1Name = $signAnesthesia1LastName.", ".$signAnesthesia1FirstName." ".$signAnesthesia1MiddleName;
																	$anesthesia1SignOnFileStatus = $signAnesthesia1Status;	
																	
																	$TDanesthesia1NameIdDisplay = "none";
																	$TDanesthesia1SignatureIdDisplay = "block";
																}
																
																//CODE TO REMOVE ANES 1 SIGNATURE
																	if($_SESSION["loginUserId"]==$signAnesthesia1Id) {
																		$callJavaFunPreOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia1','delSign');";
																	}else {
																		$callJavaFunPreOpDel = "alert('Only Dr. $Anesthesia1Name can remove this signature');";
																	}
																//END CODE TO REMOVE ANES 1 SIGNATURE	
															//END CODE RELATED TO ANES 1 SIGNATURE ON FILE
														?>
														<td  nowrap colspan="2" class="text_10">
															<div id="TDanesthesia1NameId" style="display:<?php echo $TDanesthesia1NameIdDisplay;?>; " >
																<table cellpadding="0" cellspacing="0" border="0">
																	<tr>
																		<td  class="text_10b" style="cursor:hand; " onClick="javascript:<?php echo $callJavaFunPreOp;?>">
																			<img src="images/tpixel.gif" width="15" height="1" />
																			Anesthesiologist Signature
																			<img src="images/tpixel.gif" width="40" height="1" />
																		</td>
																	</tr>
																	
																</table>
															</div>	
															<div id="TDanesthesia1SignatureId" style="display:<?php echo $TDanesthesia1SignatureIdDisplay;?>; ">															
																					
																<table border="0" cellpadding="0" cellspacing="0" class="text_10" align="center">
																	<tr>
																		<td nowrap class="text_10" style="cursor:hand;" onClick="javascript:<?php echo $callJavaFunPreOpDel;?>"><?php echo "<b>Anesthesiologist :</b>"." Dr. ". $Anesthesia1Name; ?></td>
																	</tr>
																	<tr>
																		<td class="text_10" align="right" nowrap="nowrap" colspan="14">
																			<b>Signature on File : </b><?php /*if($signatureOfAnesthesia1)*/ echo $anesthesia1SignOnFileStatus; //else echo 'No'; ?>
																			<img src="images/tpixel.gif" width="75" height="1">
																		</td>
																	</tr>
																</table>
															</div>	
														</td>
													</tr>
													<!-- <tr>
														<td valign="top" colspan="19" width="100%">
															<table border="0" style="border-color:<?php echo $border_blue_local_anes;?>;" align="center" cellpadding="0" cellspacing="0" width="100%"  bgcolor="#FFFFFF" class="all_border">
																<tr>
																	<td colspan="3"><img src="images/tpixel.gif" width="1" height="5"></td>
																</tr>
																<tr align="left" valign="middle" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																	<td width="10"></td>
																	
																	<td colspan="2" nowrap  class="text_10b">Relief Nurse<img src="images/tpixel.gif" width="5" height="5">
																		<select name="relivedPreNurseIdList" class="text_10" style=" width:120">
																			<option value="">Select</option>	
																				<?php
																				$relivedPreNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
																				$relivedPreNurseRes = imw_query($relivedPreNurseQry) or die(imw_error());
																				while($relivedPreNurseRow=imw_fetch_array($relivedPreNurseRes)) {
																					$relivedSelectPreNurseID = $relivedPreNurseRow["usersId"];
																					$relivedPreNurseName = $relivedPreNurseRow["lname"].", ".$relivedPreNurseRow["fname"]." ".$relivedPreNurseRow["mname"];
																					$sel="";
																					if($relivedPreNurseId==$relivedSelectPreNurseID) {
																						$sel = "selected";
																					} 
																					else {
																						$sel = "";
																					}
																											
																				?>	
																					<option value="<?php echo $relivedSelectPreNurseID;?>" <?php echo $sel;?>><?php echo $relivedPreNurseName;?></option>
																				<?php
																				}
																				?>
																		</select>
																	</td>
																	<td bgcolor="<?php echo $bglight_blue_local_anes; ?>" class="text_10"></td>
																</tr>
																<tr>
																	<td colspan="3"><img src="images/tpixel.gif" width="1" height="5"></td>
																</tr>
															</table>
														</td>
													</tr> -->			
													<tr>
														<td bgcolor="#ECF1EA" valign="top"></td>
													<tr>
													<tr >
														<td valign="top" colspan="19" width="100%">
															<table border="0" align="left" cellpadding="0" cellspacing="0">
																<tr>
																	<td width="4" align="right"><img src="images/leftblu.gif" width="3" height="24"></td>
																	<td width="66%"  nowrap="nowrap" align="left" valign="middle" bgcolor="<?php echo $bgdark_blue_local_anes ;?>"  class="text_10b1" ><span style=" color:<?php echo $title1_color ;?>">Intra-Operative</span></td>
																	<td align="left" valign="top" width="10"><img src="images/rightblue.gif" width="3" height="24"></td>
																	<td>&nbsp;</td>
																</tr>
															</table>
														</td>
													</tr>	
													
													<tr>
														<td colspan="19" valign="top" width="100%">
															<table width="100%" border="0" class="text_10b"  bordercolor="#D1E0C9" cellspacing="1" bgcolor="<?php echo $tablebg_local_anes; ?>">
																<tr>
																	<td valign="top" width="65%">
																		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="text_10">
																			<tr>
																				
																				<td width="20%">																				
																					<table width="100%" cellpadding="0"  class="text_10">
																						<tr  height="19">
																							<td align="left">
																								<img src="images/tpixel.gif" width="3" height="1" /><input value="<?php echo $blank1_label; ?>" name="blank1_label"  type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; background-color:<?php //echo $tablebg_local_anes; ?>; width:120px; height:20px;" tabindex="1"/><img src="images/tpixel.gif" width="5" height="1" />
																							</td>
																						</tr>
																						<tr height="22" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td align="left">
																								<img src="images/tpixel.gif" width="3" height="1" /><input value="<?php echo $blank2_label; ?>" name="blank2_label"  type="text" id="txt_field06" class="field text"  style="  border:1px solid #ccccc; background-color:<?php //echo $bglight_blue_local_anes; ?>; width:120px; height:21px;" tabindex="1"/><img src="images/tpixel.gif" width="5" height="1" />
																							</td>
																						</tr>
																						<tr height="20" >
																							<td align="left"><img src="images/tpixel.gif" width="3" height="1" />mg Propofol<img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>
																						<tr height="24" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td align="left"><img src="images/tpixel.gif" width="3" height="1" />mg Midazolam<img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>
																						<!-- <tr height="22" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td align="left"><img src="images/tpixel.gif" width="3" height="1" />Diprivan<img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>-->
																						 
																						<tr height="20" >
																							<td align="left"><img src="images/tpixel.gif" width="3" height="1" />mg Ketamine<img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>
																						<tr height="24" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td align="left"><img src="images/tpixel.gif" width="3" height="1" />mg Labetalol<img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>
																						<tr height="18" >
																							<td align="left"><img src="images/tpixel.gif" width="3" height="1" />mg SaO<sub>2</sub><img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>
																						<tr height="22" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td align="left"><img src="images/tpixel.gif" width="3" height="1" />mg O<sub>2</sub>l/m<img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>
																						<tr height="22" >
																							<td align="left" nowrap><img src="images/tpixel.gif" width="3" height="1" />mcg Fentanyl<img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>
																						<tr height="25" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td align="left"><img src="images/tpixel.gif" width="3" height="1" /><!-- EKG --><img src="images/tpixel.gif" width="5" height="1" /></td>
																						</tr>
																					</table>
																				</td>
																				<td width="79%" valign="top" align="right">
																					<table width="100%" cellpadding="0" cellspacing="0"  class="text_10">
																						<tr   height="22">
																							<td class="text_10b" align="right" nowrap>
																								<!-- <input type="text" value="<?php echo $blank1; ?>" name="blank1" id="txt_field05" class="field text"  style="border:1px solid #ccccc; width:500px; height:22" tabindex="1"/> -->
																								<input id="bp_temp8" onKeyUp="displayText8=this.value" onClick="getShow(225,180,'flag8');" value="<?php echo $blank1_1; ?>" type="text" class="text_10 all_border" name="blank1_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp9" onKeyUp="displayText9=this.value" onClick="getShow(225,240,'flag9');" value="<?php echo $blank1_2; ?>" type="text" class="text_10 all_border" name="blank1_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp10" onKeyUp="displayText10=this.value" onClick="getShow(225,280,'flag10');" value="<?php echo $blank1_3; ?>" type="text" class="text_10 all_border" name="blank1_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:22" /><input id="bp_temp11" onKeyUp="displayText11=this.value" onClick="getShow(225,320,'flag11');" value="<?php echo $blank1_4; ?>" type="text" class="text_10 all_border" name="blank1_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp12" onKeyUp="displayText12=this.value" onClick="getShow(225,360,'flag12');" value="<?php echo $blank1_5; ?>" type="text" class="text_10 all_border" name="blank1_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp13" onKeyUp="displayText13=this.value" onClick="getShow(225,400,'flag13');" value="<?php echo $blank1_6; ?>" type="text" class="text_10 all_border" name="blank1_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp14" onKeyUp="displayText14=this.value" onClick="getShow(225,440,'flag14');" value="<?php echo $blank1_7; ?>" type="text" class="text_10 all_border" name="blank1_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp15" onKeyUp="displayText15=this.value" onClick="getShow(225,480,'flag15');" value="<?php echo $blank1_8; ?>" type="text" class="text_10 all_border" name="blank1_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp16" onKeyUp="displayText16=this.value" onClick="getShow(225,520,'flag16');" value="<?php echo $blank1_9; ?>" type="text" class="text_10 all_border" name="blank1_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp17" onKeyUp="displayText17=this.value" onClick="getShow(225,560,'flag17');" value="<?php echo $blank1_10; ?>" type="text" class="text_10 all_border" name="blank1_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /> 
																							
																							</td>
																						</tr>
																						<tr height="25" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td class="text_10b" align="right" nowrap >
																								<!-- <input type="text" value="<?php echo $blank2; ?>" name="blank2" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height:22" tabindex="1"/> -->
																								<input id="bp_temp18" onKeyUp="displayText18=this.value" onClick="getShow(225,180,'flag18');" value="<?php echo $blank2_1; ?>" type="text" class="text_10 all_border" name="blank2_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:23" /><input id="bp_temp19" onKeyUp="displayText19=this.value" onClick="getShow(225,240,'flag19');" value="<?php echo $blank2_2; ?>" type="text" class="text_10 all_border" name="blank2_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:23" /><input id="bp_temp20" onKeyUp="displayText20=this.value" onClick="getShow(225,280,'flag20');" value="<?php echo $blank2_3; ?>" type="text" class="text_10 all_border" name="blank2_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:23" /><input id="bp_temp21" onKeyUp="displayText21=this.value" onClick="getShow(225,320,'flag21');" value="<?php echo $blank2_4; ?>" type="text" class="text_10 all_border" name="blank2_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:23" /><input id="bp_temp22" onKeyUp="displayText22=this.value" onClick="getShow(225,360,'flag22');" value="<?php echo $blank2_5; ?>" type="text" class="text_10 all_border" name="blank2_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:23" /><input id="bp_temp23" onKeyUp="displayText23=this.value" onClick="getShow(225,400,'flag23');" value="<?php echo $blank2_6; ?>" type="text" class="text_10 all_border" name="blank2_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:23" /><input id="bp_temp24" onKeyUp="displayText24=this.value" onClick="getShow(225,440,'flag24');" value="<?php echo $blank2_7; ?>" type="text" class="text_10 all_border" name="blank2_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:23" /><input id="bp_temp25" onKeyUp="displayText25=this.value" onClick="getShow(225,480,'flag25');" value="<?php echo $blank2_8; ?>" type="text" class="text_10 all_border" name="blank2_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:23" /><input id="bp_temp26" onKeyUp="displayText26=this.value" onClick="getShow(225,520,'flag26');" value="<?php echo $blank2_9; ?>" type="text" class="text_10 all_border" name="blank2_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:23" /><input id="bp_temp27" onKeyUp="displayText27=this.value" onClick="getShow(225,560,'flag27');" value="<?php echo $blank2_10; ?>" type="text" class="text_10 all_border" name="blank2_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:23" /> 
																							
																							</td>
																						</tr>
																						<tr height="22">
																							<td class="text_10b" align="right" nowrap>
																								<input id="bp_temp28" onKeyUp="displayText28=this.value" onClick="getShow(225,180,'flag28');" value="<?php echo $propofol_1; ?>" type="text" class="text_10 all_border" name="propofol_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp29" onKeyUp="displayText29=this.value" onClick="getShow(225,240,'flag29');" value="<?php echo $propofol_2; ?>" type="text" class="text_10 all_border" name="propofol_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp30" onKeyUp="displayText30=this.value" onClick="getShow(225,280,'flag30');" value="<?php echo $propofol_3; ?>" type="text" class="text_10 all_border" name="propofol_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:22" /><input id="bp_temp31" onKeyUp="displayText31=this.value" onClick="getShow(225,320,'flag31');" value="<?php echo $propofol_4; ?>" type="text" class="text_10 all_border" name="propofol_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp32" onKeyUp="displayText32=this.value" onClick="getShow(225,360,'flag32');" value="<?php echo $propofol_5; ?>" type="text" class="text_10 all_border" name="propofol_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp33" onKeyUp="displayText33=this.value" onClick="getShow(225,400,'flag33');" value="<?php echo $propofol_6; ?>" type="text" class="text_10 all_border" name="propofol_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp34" onKeyUp="displayText34=this.value" onClick="getShow(225,440,'flag34');" value="<?php echo $propofol_7; ?>" type="text" class="text_10 all_border" name="propofol_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp35" onKeyUp="displayText35=this.value" onClick="getShow(225,480,'flag35');" value="<?php echo $propofol_8; ?>" type="text" class="text_10 all_border" name="propofol_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp36" onKeyUp="displayText36=this.value" onClick="getShow(225,520,'flag36');" value="<?php echo $propofol_9; ?>" type="text" class="text_10 all_border" name="propofol_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp37" onKeyUp="displayText37=this.value" onClick="getShow(225,560,'flag37');" value="<?php echo $propofol_10; ?>" type="text" class="text_10 all_border" name="propofol_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /> 
																							</td>
																						</tr>
																						<tr height="22" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td class="text_10b" align="right" nowrap>
																								<!-- <input value="<?php echo $midazolam; ?>" name="midazolam" type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height:22" tabindex="1"/> -->
																								<input id="bp_temp38" onKeyUp="displayText38=this.value" onClick="getShow(225,180,'flag38');" value="<?php echo $midazolam_1; ?>" type="text" class="text_10 all_border" name="midazolam_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp39" onKeyUp="displayText39=this.value" onClick="getShow(225,240,'flag39');" value="<?php echo $midazolam_2; ?>" type="text" class="text_10 all_border" name="midazolam_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp40" onKeyUp="displayText40=this.value" onClick="getShow(225,280,'flag40');" value="<?php echo $midazolam_3; ?>" type="text" class="text_10 all_border" name="midazolam_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:22" /><input id="bp_temp41" onKeyUp="displayText41=this.value" onClick="getShow(225,320,'flag41');" value="<?php echo $midazolam_4; ?>" type="text" class="text_10 all_border" name="midazolam_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp42" onKeyUp="displayText42=this.value" onClick="getShow(225,360,'flag42');" value="<?php echo $midazolam_5; ?>" type="text" class="text_10 all_border" name="midazolam_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp43" onKeyUp="displayText43=this.value" onClick="getShow(225,400,'flag43');" value="<?php echo $midazolam_6; ?>" type="text" class="text_10 all_border" name="midazolam_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp44" onKeyUp="displayText44=this.value" onClick="getShow(225,440,'flag44');" value="<?php echo $midazolam_7; ?>" type="text" class="text_10 all_border" name="midazolam_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp45" onKeyUp="displayText45=this.value" onClick="getShow(225,480,'flag45');" value="<?php echo $midazolam_8; ?>" type="text" class="text_10 all_border" name="midazolam_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp46" onKeyUp="displayText46=this.value" onClick="getShow(225,520,'flag46');" value="<?php echo $midazolam_9; ?>" type="text" class="text_10 all_border" name="midazolam_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp47" onKeyUp="displayText47=this.value" onClick="getShow(225,560,'flag47');" value="<?php echo $midazolam_10; ?>" type="text" class="text_10 all_border" name="midazolam_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /> 
																							
																							</td>
																						</tr>
																						<!-- <tr  height="22" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td class="text_10b"><input value="<?php echo $diprivan; ?>" name="diprivan" type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height=22; " tabindex="1" /></td>
																						</tr> -->
																						
																						<tr height="22"  >
																							<td class="text_10b" align="right" nowrap>
																								<!-- <input value="<?php echo $ketamine; ?>" name="ketamine" type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height=22;"  tabindex="1"/> -->
																								<input id="bp_temp48" onKeyUp="displayText48=this.value" onClick="getShow(225,180,'flag48');" value="<?php echo $ketamine_1; ?>" type="text" class="text_10 all_border" name="ketamine_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp49" onKeyUp="displayText49=this.value" onClick="getShow(225,240,'flag49');" value="<?php echo $ketamine_2; ?>" type="text" class="text_10 all_border" name="ketamine_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp50" onKeyUp="displayText50=this.value" onClick="getShow(225,280,'flag50');" value="<?php echo $ketamine_3; ?>" type="text" class="text_10 all_border" name="ketamine_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:22" /><input id="bp_temp51" onKeyUp="displayText51=this.value" onClick="getShow(225,320,'flag51');" value="<?php echo $ketamine_4; ?>" type="text" class="text_10 all_border" name="ketamine_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp52" onKeyUp="displayText52=this.value" onClick="getShow(225,360,'flag52');" value="<?php echo $ketamine_5; ?>" type="text" class="text_10 all_border" name="ketamine_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp53" onKeyUp="displayText53=this.value" onClick="getShow(225,400,'flag53');" value="<?php echo $ketamine_6; ?>" type="text" class="text_10 all_border" name="ketamine_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp54" onKeyUp="displayText54=this.value" onClick="getShow(225,440,'flag54');" value="<?php echo $ketamine_7; ?>" type="text" class="text_10 all_border" name="ketamine_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp55" onKeyUp="displayText55=this.value" onClick="getShow(225,480,'flag55');" value="<?php echo $ketamine_8; ?>" type="text" class="text_10 all_border" name="ketamine_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp56" onKeyUp="displayText56=this.value" onClick="getShow(225,520,'flag56');" value="<?php echo $ketamine_9; ?>" type="text" class="text_10 all_border" name="ketamine_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp57" onKeyUp="displayText57=this.value" onClick="getShow(225,560,'flag57');" value="<?php echo $ketamine_10; ?>" type="text" class="text_10 all_border" name="ketamine_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /> 
																							
																							</td>
																						</tr>
																						<tr height="22" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td class="text_10b" align="right" nowrap>
																								<!-- <input value="<?php echo $labetalol; ?>" name="labetalol" type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height=22;"  tabindex="1"/> -->
																								<input id="bp_temp58" onKeyUp="displayText58=this.value" onClick="getShow(225,180,'flag58');" value="<?php echo $labetalol_1; ?>" type="text" class="text_10 all_border" name="labetalol_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp59" onKeyUp="displayText59=this.value" onClick="getShow(225,240,'flag59');" value="<?php echo $labetalol_2; ?>" type="text" class="text_10 all_border" name="labetalol_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp60" onKeyUp="displayText60=this.value" onClick="getShow(225,280,'flag60');" value="<?php echo $labetalol_3; ?>" type="text" class="text_10 all_border" name="labetalol_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:22" /><input id="bp_temp61" onKeyUp="displayText61=this.value" onClick="getShow(225,320,'flag61');" value="<?php echo $labetalol_4; ?>" type="text" class="text_10 all_border" name="labetalol_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp62" onKeyUp="displayText62=this.value" onClick="getShow(225,360,'flag62');" value="<?php echo $labetalol_5; ?>" type="text" class="text_10 all_border" name="labetalol_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp63" onKeyUp="displayText63=this.value" onClick="getShow(225,400,'flag63');" value="<?php echo $labetalol_6; ?>" type="text" class="text_10 all_border" name="labetalol_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp64" onKeyUp="displayText64=this.value" onClick="getShow(225,440,'flag64');" value="<?php echo $labetalol_7; ?>" type="text" class="text_10 all_border" name="labetalol_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp65" onKeyUp="displayText65=this.value" onClick="getShow(225,480,'flag65');" value="<?php echo $labetalol_8; ?>" type="text" class="text_10 all_border" name="labetalol_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp66" onKeyUp="displayText66=this.value" onClick="getShow(225,520,'flag66');" value="<?php echo $labetalol_9; ?>" type="text" class="text_10 all_border" name="labetalol_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp67" onKeyUp="displayText67=this.value" onClick="getShow(225,560,'flag67');" value="<?php echo $labetalol_10; ?>" type="text" class="text_10 all_border" name="labetalol_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /> 
																							
																							</td>	
																						</tr>
																						<tr height="22" >
																							<td class="text_10b" align="right" nowrap>
																								<!-- <input value="<?php echo $spo2; ?>" name="SpO" type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height=22;"  tabindex="1" /> -->
																								<input id="bp_temp68" onKeyUp="displayText68=this.value" onClick="getShow(225,180,'flag68');" value="<?php echo $spo2_1; ?>" type="text" class="text_10 all_border" name="spo2_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp69" onKeyUp="displayText69=this.value" onClick="getShow(225,240,'flag69');" value="<?php echo $spo2_2; ?>" type="text" class="text_10 all_border" name="spo2_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp70" onKeyUp="displayText70=this.value" onClick="getShow(225,280,'flag70');" value="<?php echo $spo2_3; ?>" type="text" class="text_10 all_border" name="spo2_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:22" /><input id="bp_temp71" onKeyUp="displayText71=this.value" onClick="getShow(225,320,'flag71');" value="<?php echo $spo2_4; ?>" type="text" class="text_10 all_border" name="spo2_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp72" onKeyUp="displayText72=this.value" onClick="getShow(225,360,'flag72');" value="<?php echo $spo2_5; ?>" type="text" class="text_10 all_border" name="spo2_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp73" onKeyUp="displayText73=this.value" onClick="getShow(225,400,'flag73');" value="<?php echo $spo2_6; ?>" type="text" class="text_10 all_border" name="spo2_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp74" onKeyUp="displayText74=this.value" onClick="getShow(225,440,'flag74');" value="<?php echo $spo2_7; ?>" type="text" class="text_10 all_border" name="spo2_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp75" onKeyUp="displayText75=this.value" onClick="getShow(225,480,'flag75');" value="<?php echo $spo2_8; ?>" type="text" class="text_10 all_border" name="spo2_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp76" onKeyUp="displayText76=this.value" onClick="getShow(225,520,'flag76');" value="<?php echo $spo2_9; ?>" type="text" class="text_10 all_border" name="spo2_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp77" onKeyUp="displayText77=this.value" onClick="getShow(225,560,'flag77');" value="<?php echo $spo2_10; ?>" type="text" class="text_10 all_border" name="spo2_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /> 
																							
																							</td>	
																						</tr>
																						<tr height="22" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td  class="text_10b" align="right" nowrap>
																							<!-- <input value="<?php echo $o2lpm; ?>" name="O2lpm" type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height=22;"  tabindex="1" /> -->
																								<input id="bp_temp78" onKeyUp="displayText78=this.value" onClick="getShow(225,180,'flag78');" value="<?php echo $o2lpm_1; ?>" type="text" class="text_10 all_border" name="o2lpm_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp79" onKeyUp="displayText79=this.value" onClick="getShow(225,240,'flag79');" value="<?php echo $o2lpm_2; ?>" type="text" class="text_10 all_border" name="o2lpm_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp80" onKeyUp="displayText80=this.value" onClick="getShow(225,280,'flag80');" value="<?php echo $o2lpm_3; ?>" type="text" class="text_10 all_border" name="o2lpm_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:22" /><input id="bp_temp81" onKeyUp="displayText81=this.value" onClick="getShow(225,320,'flag81');" value="<?php echo $o2lpm_4; ?>" type="text" class="text_10 all_border" name="o2lpm_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp82" onKeyUp="displayText82=this.value" onClick="getShow(225,360,'flag82');" value="<?php echo $o2lpm_5; ?>" type="text" class="text_10 all_border" name="o2lpm_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp83" onKeyUp="displayText83=this.value" onClick="getShow(225,400,'flag83');" value="<?php echo $o2lpm_6; ?>" type="text" class="text_10 all_border" name="o2lpm_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp84" onKeyUp="displayText84=this.value" onClick="getShow(225,440,'flag84');" value="<?php echo $o2lpm_7; ?>" type="text" class="text_10 all_border" name="o2lpm_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:22" /><input id="bp_temp85" onKeyUp="displayText85=this.value" onClick="getShow(225,480,'flag85');" value="<?php echo $o2lpm_8; ?>" type="text" class="text_10 all_border" name="o2lpm_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp86" onKeyUp="displayText86=this.value" onClick="getShow(225,520,'flag86');" value="<?php echo $o2lpm_9; ?>" type="text" class="text_10 all_border" name="o2lpm_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /><input id="bp_temp87" onKeyUp="displayText87=this.value" onClick="getShow(225,560,'flag87');" value="<?php echo $o2lpm_10; ?>" type="text" class="text_10 all_border" name="o2lpm_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:22" /> 
																							
																							</td>	
																						</tr>
																						<tr height="24">
																							<td class="text_10b" align="right" nowrap>
																								<!-- <input value="<?php echo $Fentanyl; ?>" name="Fentanyl" type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height=22;" tabindex="1"/> -->
																								<input id="bp_temp88" onKeyUp="displayText88=this.value" onClick="getShow(225,180,'flag88');" value="<?php echo $Fentanyl_1; ?>" type="text" class="text_10 all_border" name="Fentanyl_1" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:24" /><input id="bp_temp89" onKeyUp="displayText89=this.value" onClick="getShow(225,240,'flag89');" value="<?php echo $Fentanyl_2; ?>" type="text" class="text_10 all_border" name="Fentanyl_2" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:24" /><input id="bp_temp90" onKeyUp="displayText90=this.value" onClick="getShow(225,280,'flag90');" value="<?php echo $Fentanyl_3; ?>" type="text" class="text_10 all_border" name="Fentanyl_3" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes;?>; width:50px; height:24" /><input id="bp_temp91" onKeyUp="displayText91=this.value" onClick="getShow(225,320,'flag91');" value="<?php echo $Fentanyl_4; ?>" type="text" class="text_10 all_border" name="Fentanyl_4" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:24" /><input id="bp_temp92" onKeyUp="displayText92=this.value" onClick="getShow(225,360,'flag92');" value="<?php echo $Fentanyl_5; ?>" type="text" class="text_10 all_border" name="Fentanyl_5" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:24" /><input id="bp_temp93" onKeyUp="displayText93=this.value" onClick="getShow(225,400,'flag93');" value="<?php echo $Fentanyl_6; ?>" type="text" class="text_10 all_border" name="Fentanyl_6" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:24" /><input id="bp_temp94" onKeyUp="displayText94=this.value" onClick="getShow(225,440,'flag94');" value="<?php echo $Fentanyl_7; ?>" type="text" class="text_10 all_border" name="Fentanyl_7" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:45px; height:24" /><input id="bp_temp95" onKeyUp="displayText95=this.value" onClick="getShow(225,480,'flag95');" value="<?php echo $Fentanyl_8; ?>" type="text" class="text_10 all_border" name="Fentanyl_8" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:24" /><input id="bp_temp96" onKeyUp="displayText96=this.value" onClick="getShow(225,520,'flag96');" value="<?php echo $Fentanyl_9; ?>" type="text" class="text_10 all_border" name="Fentanyl_9" style="border-left:1px solid; border-right:0px; border-top:1px solid; border-bottom:1px solid;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:24" /><input id="bp_temp97" onKeyUp="displayText97=this.value" onClick="getShow(225,560,'flag97');" value="<?php echo $Fentanyl_10; ?>" type="text" class="text_10 all_border" name="Fentanyl_10" style="border:1px solid #ccccc;border-color:<?php echo $border_blue_local_anes; ?> ; width:50px; height:24" /> 
																							
																							</td>	
																						</tr>
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
																						<tr height="26" valign="bottom" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td class="text_10" style="font-size:12px; " nowrap id="intervalId">
																								<!-- <input value="<?php echo $ekg; ?>" name="ekg" type="text" id="txt_field05" class="field text"  style="  border:1px solid #ccccc; width:500px; height=22;"  tabindex="1" /> -->
																								<img src="images/tpixel.gif" width="20" height="1" />
																								<?php echo $intervalTimeMin1;?>
																								<img src="images/tpixel.gif" width="4" height="1" />
																								<?php echo $intervalTimeMin2;?>
																								<img src="images/tpixel.gif" width="4" height="1" />
																								<?php echo $intervalTimeMin3;?>
																								<img src="images/tpixel.gif" width="3" height="1" />
																								<?php echo $intervalTimeMin4;?>
																								<img src="images/tpixel.gif" width="3" height="1" />
																								<?php echo $intervalTimeMin5;?>
																								<img src="images/tpixel.gif" width="2" height="1" />
																								<?php echo $intervalTimeMin6;?>
																								<img src="images/tpixel.gif" width="4" height="1" />
																								<?php echo $intervalTimeMin7;?>
																								<img src="images/tpixel.gif" width="2" height="1" />
																								<?php echo $intervalTimeMin8;?>
																								<img src="images/tpixel.gif" width="2" height="1" />
																								<?php echo $intervalTimeMin9;?>
																								<img src="images/tpixel.gif" width="2" height="1" />
																								<?php echo $intervalTimeMin10;?>
																							</td>	
																						</tr>
																					</table>
																				</td>
																				<!-- -->
																			</tr>
																			<tr>
																				<td colspan="2">
																					<table border="0" cellpadding="0" cellspacing="0">
																						<tr>
																							<td>
																								<!-- Menu -->
																								<table width="100%"  class="text_10" border="0">
																									<tr>
																										<td  class="text_10" nowrap valign="top" >
																											Start Time<input onBlur="showInterval(this.value,'intervalId');"  type="text" id="bp_temp6" name="startTime" class="text" style=" border:1px solid #ccccc; width:55px; vertical-align:bottom; "  value="<?php echo $startTime;?>" onKeyUp="displayText6=this.value" onClick="getShow(470,5,'flag6');"    />
																										</td>
																									</tr>
																									<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																										<td  class="text_10" nowrap>
																											Stop Time<input type="text" id="bp_temp7" name="stopTime" class="field text" style=" border:1px solid #ccccc; width:55px; vertical-align:bottom;"  value="<?php echo $stopTime;?>" onKeyUp="displayText7=this.value" onClick="getShow(470,5,'flag7');"  />
																										</td>
																									</tr>
																									<tr style="cursor:hand;" bgcolor="#FFFFFF" onClick="setAppIcon('tdArr1')" id="tdArr1">
																										<td align="center" width="130"><img src="images/arrow1.png" /><!-- <img src="images/block.gif" /> --></td>
																									</tr>
																									<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>"  style="cursor:hand;" onClick="setAppIcon('tdArr1')">
																										<td align="center" >Systolic</td>
																									</tr>
																									<tr style="cursor:hand;" onClick="setAppIcon('tdArr1')">
																										<td align="center" >Pressure</td>
																									</tr>
																									<tr style="cursor:hand;background-color:#FFFFCC;" bgcolor="<?php echo $bglight_blue_local_anes; ?>"  onclick="setAppIcon('tdArr2')"  id="tdArr2">
																										<td align="center" ><img src="images/arrow.png" /><!-- <img src="images/none.gif" /> --></td>
																									</tr>
																									<tr style="cursor:hand;" onClick="setAppIcon('tdArr2')">
																										<td align="center" >Diastolic</td>
																									</tr>
																									<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" style="cursor:hand;" onClick="setAppIcon('tdArr2')">
																										<td align="center"> Pressure</td>
																									</tr>
																									<tr bgcolor="#FFFFFF" style="cursor:hand;" onClick="setAppIcon('tdRblank')"  id="tdRblank">
																										<td align="center" ><img src="images/radio_blank.png"  /></td>
																									</tr>
																									<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" style="cursor:hand;" onClick="setAppIcon('tdRblank')">
																										<td align="center" >Heart</td>
																									</tr>
																									<tr style="cursor:hand;" onClick="setAppIcon('tdRblank')">
																										<td align="center" >Rate</td>
																									</tr>
																									<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" style="cursor:hand;" onClick="setAppIcon('tdRfill')"  id="tdRfill">
																										<td align="center" ><img src="images/radio_fill.png"/></td>
																									</tr>
																									<tr style="cursor:hand;" onClick="setAppIcon('tdRfill')">
																										<td align="center" >Spontaneous </td>
																									</tr>
																									<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" style="cursor:hand;" onClick="setAppIcon('tdRfill')">
																										<td align="center">Respiration</td>
																									</tr>
																									<tr height="14"><td align="center" id="tdRText" onClick="setAppIcon('tdRText')" style="cursor:hand;"><b>T</b></td></tr>
																									<tr height="14" bgcolor="<?php echo $bglight_blue_local_anes; ?>" onClick="setAppIcon('tdRText')" style="cursor:hand;"><td align="center">Input value</td></tr>
																									<tr>																					
																										<td valign="bottom" align="right">
																											<table width="60%">
																												<tr>
																													<td><img src="images/eraser.gif" alt="clear" onClick="clearApp()"></td>
																													<td><img src="images/undo.png" alt="undo" onClick="undoApp()"></td>
																													<td><img src="images/redo.png" alt="redo" onClick="redoApp()"></td>																											
																												</tr>
																											</table>
																										</td>																					
																									</tr>
																								</table>		
																								<!-- Menu -->
																							</td>
																							<td>
																								<!-- Applet -->
																								<table  class="text_10" >
																									<tr>
																										<td width="520">
																											<APPLET code="Test2Mac.class" codebase="common/applet/" archive="signs.jar"
																												width="520" height="370" name="signs" onmouseout="getAppValue()">
																												<PARAM name="bgImg" value="images/icon/bgGrid.jpg">
																												<param name="signs" value="<?php echo $applet_data;?>">
																												<param name="iconSize" value="14">
																												<param name="txtActivate" value="inactive">
																											</APPLET>																						
																											<input type="hidden" name="applet_data" value="<?php echo $applet_data;?>">																						
																										</td>																					
																									</tr>
																								</table>
																								<!-- Applet -->
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>											
																		</table>
																	</td>
																	<td valign="top" width="35%">
																		<table width="100%" border="1" cellpadding="0" cellspacing="0" class="text_10" bordercolor="#FFFFFF">
																			<tr style="color:<?php echo $title1_color ;?>" bgcolor="<?php echo $bgmid_blue_local_anes ; ?>"  class="text_10b">
																				<td><img src="images/tpixel.gif" /> 1. <img src="images/tpixel.gif" /> Routine Monitors Applied</td>
																				<td height="22" align="center" valign="top" class="text_10 pad_top_bottom">
																					<input <?php if($routineMonitorApplied=='Yes') echo "CHECKED"; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_routine_id" name="chbx_routine" tabindex="7">
																				</td>
																			</tr>
																			<tr bgcolor="<?php echo $bgmid_blue_local_anes ; ?>">
																				<td colspan="2" height="22" class="text_10b"><img src="images/tpixel.gif" />
																					<span style="color:<?php echo $title1_color ;?>"> 2.</span>
																					<img src="images/tpixel.gif"/>
																					<span style="color:<?php echo $title1_color ;?>">IV Catheter</span>
																				</td>
																			</tr>
																			<tr style="color:<?php echo $title1_color ;?>" bgcolor="<?php echo $bgmid_blue_local_anes; ?>" >
																				<td height="22"><img src="images/tpixel.gif" width="2"/>No IV</td>
																				<td height="22" align="center" valign="top" class="text_10 pad_top_bottom"><input class="field checkbox"  type="checkbox" value="Yes" <?php if($ivCatheter=='Yes') echo 'CHECKED'; ?> id="chbx_no_id" name="chbx_no"  tabindex="7"></td>
																			</tr>
																			<tr>
																				<td colspan="2" align="left">
																					<table width="100%" class="text_10" border="0" cellpadding="0" cellspacing="0">
																						<tr height="22">
																							<td  width="25%">Hand</td>
																							<td width="20%" align="right">Right</td>
																							<td width="15%" height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" value="Yes" id="chbx_hand_right_id" name="chbx_hand_right" <?php if($hand_right=='Yes') echo 'CHECKED'; ?> tabindex="7">
																							</td>
																							<td width="20%" align="right">Left</td>
																							<td width="20%"  height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" value="Yes" id="chbx_hand_left_id" name="chbx_hand_left" <?php if($hand_left=='Yes') echo 'CHECKED'; ?> tabindex="7">
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" height="22">
																				<td colspan="2" align="center">
																					<table width="100%" class="text_10" border="0" cellpadding="0" cellspacing="0">
																						<tr>		
																							<td width="25%" >Wrist</td>
																							<td width="20%" align="right">Right</td>
																							<td width="15%" height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" value="Yes" id="chbx_wrist_right_id" name="chbx_wrist_right" <?php if($wrist_right=='Yes') echo 'CHECKED'; ?> tabindex="7">
																							</td>
																							<td width="20%" align="right">Left</td>
																							<td width="20%"  height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" value="Yes" id="chbx_wrist_left_id" name="chbx_wrist_left" <?php if($wrist_left=='Yes') echo 'CHECKED'; ?> tabindex="7">
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<tr>
																				<td colspan="2" align="center">
																					<table width="100%" class="text_10" border="0" cellpadding="0" cellspacing="0">
																						<tr>
																							<td  width="25%" >Arm</td>
																							<td width="20%" align="right">Right</td>
																							<td width="15%" height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" value="Yes" id="chbx_arm_right_id" name="chbx_arm_right" <?php if($arm_right=='Yes') echo 'CHECKED'; ?> tabindex="7">
																							</td>
																							<td width="20%" align="right">Left</td>
																							<td width="20%"  height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" value="Yes" id="chbx_arm_left_id" name="chbx_arm_left" <?php if($arm_left=='Yes') echo 'CHECKED'; ?> tabindex="7">
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" height="22">
																				<td colspan="2" align="center">
																					<table width="100%" class="text_10" border="0" cellpadding="0" cellspacing="0">
																						<tr>
																							<td  width="25%">Antecubital</td>
																							<td width="20%" align="right">Right</td>
																							<td width="15%" height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" value="Yes" id="chbx_anti_right_id" name="chbx_anti_right" <?php if($anti_right=='Yes') echo 'CHECKED'; ?> tabindex="7">
																							</td>
																							<td width="20%" align="right">Left</td>
																							<td width="20%" height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" value="Yes" id="chbx_anti_left_id" name="chbx_anti_left" <?php if($anti_left=='Yes') echo 'CHECKED'; ?> tabindex="7"><img src="images/tpixel.gif" width="44" height="8" />
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<tr height="22">
																				<td colspan="2" align="center">
																					<table width="100%" class="text_10" border="0" cellpadding="0" cellspacing="0">
																						<tr>
																							<td width="20%">Other</td>
																							<td width="20%" align="right"></td>
																							<td colspan="2" width="40%" height="22" align="left" valign="top" class="text_10 pad_top_bottom">
																								<textarea name="other_reg_anes" id="other_reg_anes_id" class="field textarea text_10" style="border:1px solid #cccccc; width:150px; height:20px; " rows="1" cols="50" tabindex="6"><?php echo $ivCatheterOther; ?></textarea>
																							</td>
																							<td width="20%"  height="22" align="left" valign="top" class="text_10 pad_top_bottom" onClick="javascript:disp_hide_checked_row_id('chbx_other11_id','other_reg_anes_id')">	<input class="field checkbox" <?php if($ivCatheterOther) echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_other11_id" name="chbx_other11"  tabindex="7" ></td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<tr height="22">
																				<td colspan="2" align="center">
																					<table width="100%" class="text_10" border="0" cellpadding="0" cellspacing="0">
																						<tr style="color:<?php echo $title1_color ;?>" bgcolor="<?php echo $bgmid_blue_local_anes ; ?>">
																							<td height="22" colspan="4" align="left" class="text_10b">
																								<img src="images/tpixel.gif"/><span style="color:<?php echo $title1_color ;?>">3.</span>
																								<img src="images/tpixel.gif"/><span style="color:<?php echo $title1_color ;?>">Regional Anesthesia</span>
																							</td>
																						</tr>
																						<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" height="22">
																							<td width="25%" valign="top">Topical</td>
																							<td width="18%">
																								<select class="text_10" style="  width:70;border:1px;" name="topical">
																									<option value="">Select</option>
																									<?php
																									for($i=1;$i<=20;$i+=0.5) {
																									?>
																										<option value="<?php echo $i;?>" <?php if($topical==$i) echo 'selected'; ?>><?php echo $i;?></option>
																									<?php
																									}
																									?>
																								</select>
																							</td>
																							<td width="35%" align="right" style="font-weight:200" nowrap>2% lidocaine</td>
																							<td width="22%" align="center" height="22" valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_lido2_id','chbx_topi_peri_retro');">
																								<!-- <input <?php //if($lidocaine2=='Yes') echo 'CHECKED'; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_lido2_id" name="chbx_lido2"  tabindex="7" > -->
																								<input <?php if($topi_peri_retro=='lidocaine2') echo 'CHECKED'; ?> class="field checkbox"  type="checkbox" value="lidocaine2" id="chbx_lido2_id" name="chbx_topi_peri_retro"  >
																							</td>
																						</tr>
																						<tr height="22">
																							<td></td>
																							<td colspan="2" align="right" style="font-weight:200">3% lidocaine</td>
																							<td height="22" align="center"  valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_lido3_id','chbx_topi_peri_retro');">
																								<!-- <input <?php //if($lidocaine3=='Yes') echo 'CHECKED'; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_lido3_id" name="chbx_lido3" tabindex="7"> -->
																								<input <?php if($topi_peri_retro=='lidocaine3') echo 'CHECKED'; ?> class="field checkbox"  type="checkbox" value="lidocaine3" id="chbx_lido3_id" name="chbx_topi_peri_retro"   >
																							</td>
																						</tr>
																						<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" height="22">
																							<td width="25%">Peribulbar</td>
																							<td width="18%">
																								<select class="text_10" style=" width:70; border:1px;" name="peribulbar">
																									<option value="">Select</option>
																									<?php
																									for($i=1;$i<=20;$i+=0.5) {
																									?>
																										<option value="<?php echo $i;?>" <?php if($Peribulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
																									<?php
																									}
																									?>
																									
																								</select>
																							</td>
																							<td align="right" style="font-weight:200">4% lidocaine</td>
																							<td width="22%" height="22" align="center"  valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_lido4_id','chbx_topi_peri_retro');">
																								<!-- <input class="field checkbox" <?php //if($lidocaine4=='Yes') echo "CHECKED"; ?>  type="checkbox" value="Yes" id="chbx_lido4_id" name="chbx_lido4"  tabindex="7"> -->
																								<input type="checkbox" value="lidocaine4" <?php if($topi_peri_retro=='lidocaine4') echo 'CHECKED'; ?> class="field checkbox"   id="chbx_lido4_id" name="chbx_topi_peri_retro"/ >
																							</td>
																						</tr>
																						<tr height="22">
																							<td colspan="1"></td>
																							<td colspan="2" style="font-weight:200" align="right">0.5% Bupivicane</td>
																							<td height="22" align="center"  valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_bupi_id','chbx_topi_peri_retro');">
																								<!-- <input class="field checkbox" <?php //if($Bupiyicaine5=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" id="chbx_bupi_id" name="chbx_bupi" tabindex="7"> -->
																								<input <?php if($topi_peri_retro=='Bupiyicaine5') echo 'CHECKED'; ?> class="field checkbox"  type="checkbox" value="Bupiyicaine5" id="chbx_bupi_id" name="chbx_topi_peri_retro"   >
																							</td>
																						</tr>																						
																						<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>" height="22">
																							<td width="25%">Retrobulbar</td>
																							<td width="18%">
																								<select class="text_10" style=" width:70;border:1px;" name="retrobulbar">
																									<option value="">Select</option>
																									<?php
																									for($i=1;$i<=20;$i+=0.5) {
																									?>
																										<option value="<?php echo $i;?>" <?php if($Retrobulbar==$i) echo 'selected'; ?>><?php echo $i;?></option>
																									<?php
																									}
																									?>
																								</select>
																							</td>
																							<td width="35%" align="right"  style="font-weight:200px;" >epi .5 ug/cc</td>
																							<td  width="22%" height="22" align="center"  valign="top" class="text_10 pad_top_bottom" onClick="javascript:checkSingle('chbx_epi_id','chbx_topi_peri_retro');">
																								<!-- <input <?php //if($ugcc=='Yes') echo "CHECKED"; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_epi_id" name="chbx_epi"  tabindex="7"> -->
																								<input <?php if($topi_peri_retro=='ugcc') echo 'CHECKED'; ?> class="field checkbox"  type="checkbox" value="ugcc" id="chbx_epi_id" name="chbx_topi_peri_retro"   >
																							</td>
																						</tr>
																						<tr height="22">
																							<td width="25%">Hyalauronidase</td>
																							<td width="18%">
																								<select class="text_10" style=" width:70;border:1px;" name="hyalauronidase">
																									<option value="">Select</option>
																									<?php
																									for($i=1;$i<=20;$i+=0.5) {
																									?>
																										<option value="<?php echo $i;?>" <?php if($Hyalauronidase==$i) echo 'selected'; ?>><?php echo $i;?></option>
																									<?php
																									}
																									?>
																								</select>
																							</td>
																							<td colspan="1"></td>
																							<td height="22"   align="center"  valign="top" class="text_10 pad_top_bottom">
																								<!-- <input class="field checkbox"  type="checkbox" value="Yes" id="chbx_haluro_id" name="chbx_haluro"  tabindex="7" > -->
																							</td>		
																						</tr height="22">
																						<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td align="left" colspan="1" width="25%">Other</td>
																							<td colspan="3">
																								<input type="text" name="otherRegionalAnesthesia" id="txt_field01" class="field text" style=" border:1px solid #ccccc;width:180px;" tabindex="1" value="<?php echo $regionalAnesthesiaOther; ?>"/>
																							</td>	
																						</tr>
																						<tr height="22">
																							<td width="25%" align="left" colspan="1">Van Lindr</td>
																							<td colspan="3">
																								<select class="text_10" style="  width:70; border:1px;" name="vanLindt">
																									<option value="">Select</option>
																									<?php
																									for($i=1;$i<=20;$i+=0.5) {
																									?>
																										<option value="<?php echo $i;?>" <?php if($vanlindt==$i) echo 'selected'; ?>><?php echo $i;?></option>
																									<?php
																									}
																									?>
																								</select>
																							</td>
																						</tr>
																						<tr height="16"></tr>
																						<tr style="color:<?php echo $title1_color ;?>" bgcolor="<?php echo $bgmid_blue_local_anes ; ?>">
																							<td colspan="4" height="22">
																								<img src="images/tpixel.gif" />4.<img src="images/tpixel.gif" />
																								Ocular Pressure
																							</td>
																						</tr>
																						<tr>
																							<td colspan="2" style="font-weight:500"> <img src="images/tpixel.gif" width="1"/>None</td>
																							<td></td>
																							<td height="22" width="5%" align="left" valign="middle" class="text_10 pad_top_bottom">
																								<input class="field checkbox" <?php if($none=='Yes') echo 'CHECKED'; ?>  type="checkbox" value="Yes" id="chbx_none_id" name="chbx_none" tabindex="7">
																							</td>
																							<td width="22%"></td>
																						</tr>
																						<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																							<td colspan="2" style="font-weight:200">Digital</td>
																							<td></td>
																							<td height="22"  align="left" valign="middle" class="text_10 pad_top_bottom">
																								<input class="field checkbox"  type="checkbox" <?php if($digital=='Yes') echo 'CHECKED'; ?> value="Yes" id="chbx_digi_id" name="chbx_digi" tabindex="7">
																							</td>
																							<td></td>
																						</tr>
																						<tr height="22">
																							<td width="45%"><span style="font-weight:100">Honan Ballon</span> </td>
																							<td colspan="2" align="left"><img src="images/tpixel.gif" height="2" width="1" />
																								<select class="text_10" style=" width:70; size:20;  border:1px;" name="honanBallon">
																									<option value="">Select</option>
																									<?php
																									for($i=1;$i<=20;$i+=0.5) {
																									?>
																										<option value="<?php echo $i;?>" <?php if($honanballon==$i) echo 'selected'; ?>><?php echo $i;?></option>
																									<?php
																									}
																									?>
																								</select>
																							</td>
																							<td align="left" valign="middle" class="text_10 pad_top_bottom">
																						  		<!-- <input class="field checkbox"  type="checkbox" value="Yes" id="chbx_honnan_id" name="chbx_honnan"  tabindex="7" ><img src="images/tpixel.gif" width="5" /> -->
																							</td>
																						</tr>
																						<tr style="color:<?php echo $title1_color ;?>" bgcolor="<?php echo $bgmid_blue_local_anes ; ?>">
																							<td colspan="4" height="22"></td>
																						</tr>
																						<?php
																							//CODE RELATED TO ANES 2 SIGNATURE ON FILE
																								$anesthesia2SignOnFileStatus = "Yes";
																								$TDanesthesia2NameIdDisplay = "block";
																								$TDanesthesia2SignatureIdDisplay = "none";
																								$Anesthesia2Name = $loggedInUserName;
																								if($signAnesthesia2Id<>0 && $signAnesthesia2Id<>"") {
																									$Anesthesia2Name = $signAnesthesia2LastName.", ".$signAnesthesia2FirstName." ".$signAnesthesia2MiddleName;
																									$anesthesia2SignOnFileStatus = $signAnesthesia2Status;	
																									
																									$TDanesthesia2NameIdDisplay = "none";
																									$TDanesthesia2SignatureIdDisplay = "block";
																								}
																								//CODE TO REMOVE ANES 2 SIGNATURE
																									if($_SESSION["loginUserId"]==$signAnesthesia2Id) {
																										$callJavaFunIntraOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia2NameId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia2','delSign');";
																									}else {
																										$callJavaFunIntraOpDel = "alert('Only Dr. $Anesthesia2Name can remove this signature');";
																									}
																								//END CODE TO REMOVE ANES 2 SIGNATURE
																								
																							//END CODE RELATED TO ANES 2 SIGNATURE ON FILE	

																						?>
																						<tr>
																							<td colspan="4" style="font-weight:500">
																								<div id="TDanesthesia2NameId" style="display:<?php echo $TDanesthesia2NameIdDisplay;?> " >
																									<table width="100%" border="0" cellpadding="0" cellspacing="0">
																										<tr>
																											<td  nowrap class="text_10b" height="22" style="cursor:hand; " onClick="javascript:<?php echo $callJavaFunIntraOp;?>">
																												 <img src="images/tpixel.gif" width="1"/> 
																												Anesthesiologist Signature
																											</td>
																										</tr>
																									</table>
																								</div>	
																								<div id="TDanesthesia2SignatureId" style="display:<?php echo $TDanesthesia2SignatureIdDisplay;?>; ">	
																									<table width="100%" border="0" cellpadding="0" cellspacing="0">
																										<tr>
																											<td nowrap class="text_10" height="22" style="cursor:hand;" onClick="javascript:<?php echo $callJavaFunIntraOpDel;?>">
																												 <img src="images/tpixel.gif" width="1"/> 
																												<?php echo "<b>Anesthesiologist :</b>"." Dr. ". $Anesthesia2Name; ?>
																											</td>
																										</tr>
																										<tr  bgcolor="<?php echo $bglight_blue_local_anes; ?>" >
																											<td class="text_10" height="22">
																												 <img src="images/tpixel.gif" width="1"/> 
																												<b>Signature on File : </b><?php echo $anesthesia2SignOnFileStatus; ?>
																											</td>
																										</tr>
																									</table>
																								</div>	
																							</td>
																							
																							
																							<td width="22%"></td>
																						</tr>
																						
																						<tr style="color:<?php echo $title1_color ;?>" bgcolor="<?php echo $bgmid_blue_local_anes ; ?>">
																							<td colspan="4" height="22"></td>
																						</tr>
																						<tr>
																							<td colspan="3" style="font-weight:500"> <img src="images/tpixel.gif" width="1"/>
																								<?php echo "<b>Relief Nurse  </b>";?>
																								<select name="relivedIntraNurseIdList" class="text_10" style=" width:120">
																									<option value="">Select</option>	
																										<?php
																										$relivedIntraNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
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
																																	
																										?>	
																											<option value="<?php echo $relivedSelectIntraNurseID;?>" <?php echo $sel;?>><?php echo $relivedIntraNurseName;?></option>
																										<?php
																										}
																										?>
																								</select>
																							</td>
																							<td></td>
																							
																							<td width="22%"></td>
																						</tr>
																						
																					
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<!-- <tr >
														<td valign="top" colspan="19" width="100%">
															<table border="0" style="border-color:<?php echo $border_blue_local_anes;?>;" align="center" cellpadding="0" cellspacing="0" width="100%"  bgcolor="#FFFFFF" class="all_border">
																<tr>
																	<td colspan="4"><img src="images/tpixel.gif" width="1" height="5"></td>
																</tr>
																<tr align="left" valign="middle" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
																	<td width="10"></td>
																	<td width="300" nowrap colspan="1" class="text_10"><?php echo "<b>Anesthesiologist :</b>"." Dr. ". $anesthesiologist_name; ?></td>
																	<td nowrap  colspan="1" class="text_10"><b>Signature on File : </b><?php if($signatureOfAnesthesiologist) echo 'Yes'; else echo 'No'; ?></td>
																	<td bgcolor="<?php echo $bglight_blue_local_anes; ?>" class="text_10"></td>
																</tr>
																<tr >
																	<td colspan="4"><img src="images/tpixel.gif" width="1" height="5"></td>
																</tr>
															</table>
														</td>
													</tr> -->													
												</table>
											</td>	
										</tr>
							  	  </table>									
								</td>
							</tr>				
					  </table>
					</td>
				</tr>
				<tr>
					<td bgcolor="#ECF1EA" valign="top"></td>
				<tr>
				<tr >
					<td valign="top"  width="100%"  style="border-color:<?php echo $border_blue_local_anes ;?>">
						<table border="0" align="left" cellpadding="0" cellspacing="0" >
							<tr>
								<td width="4" align="right"><img src="images/leftblu.gif" width="3" height="24"></td>
								<td width="66%"  nowrap="nowrap" align="left" valign="middle" bgcolor="<?php echo $bgdark_blue_local_anes ;?>"  class="text_10b1" ><span style=" color:<?php echo $title1_color ;?>">Post-Operative</span></td>
								<td align="left" valign="top" width="10"><img src="images/rightblue.gif" width="3" height="24"></td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr class="text_10" bgcolor="">
		<td  align="left" >
			<table border="0" cellpadding="0" cellspacing="0" width="99%" align="center" style="border-color:<?php echo $border_blue_local_anes;?>;" class="all_border">
				<tr class="text_10" bgcolor="">
					<td width="50%"  align="left" class="right_border" style="border-color:#FFFFFF; ">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" class="text_10">
				
							<tr>
								<td width="1%" align="left" ></td>
								<td width="89%" align="left" >No know anesthetic complication</td>
								<td width="10%" height="22" align="left" valign="top">
									<input align="middle" <?php if($anyKnowAnestheticComplication=='Yes') echo 'CHECKED'; ?> class="field checkbox"  type="checkbox" value="Yes" id="chbx_anes_id" name="chbx_anes"  tabindex="7" >
								</td>
							</tr>
							<tr bgcolor="<?php echo $bglight_blue_local_anes; ?>">
								<td  align="left" ></td>
								<td nowrap>Stable cardiovascular and pulmonary function</td>
								<td height="22" valign="top" class="text_10 pad_top_bottom">
									<input class="field checkbox" <?php if($stableCardiPlumFunction2=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_pulm_id" name="chbx_pulm" tabindex="7">
								</td>
							</tr>
							<tr>
								<td  align="left" ></td>
								<td >Satisfactory condition for discharge</td>
								<td valign="top" height="22" class="text_10 pad_top_bottom">
									<input class="field checkbox" <?php if($satisfactoryCondition4Discharge=='Yes') echo 'CHECKED'; ?> type="checkbox" value="Yes" id="chbx_dis_id" name="chbx_dis" tabindex="7">
								</td>
							</tr>
						</table>
					</td>
					<td  align="left">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr class="text_10b">
								<td align="left" style="color:#800080;cursor:hand;" onClick="return showEvaluationFn('local_anes_revaluation1_id', '', 'no', '380', '1090'),document.getElementById('selected_frame_name_id').value='';" ><img src="images/tpixel.gif" width="8" height="1" />Evaluation<img src="images/tpixel.gif" width="8" height="1" /></td>
								<td align="left" valign="top" class="purpleM">
									<textarea name="evaluation" id="local_anes_revaluation1_id" class="field textarea justi text_10" style="border:1px solid #cccccc; width:350px; " rows="4" cols="100" tabindex="6"><?php echo $evaluation; ?></textarea>
								</td>
							</tr>
							<tr class="text_10b" bgcolor="#FFFFFF">
								<td align="left" ><img src="images/tpixel.gif" width="8" height="1" />Remarks</td>
								<td align="left" valign="top" class="purpleM">
									<textarea name="txtarea_remarks" id="" class="field textarea justi text_10" style="border:1px solid #cccccc; width:350px; " rows="4" cols="100" tabindex="6"><?php echo $remarks; ?></textarea>
								</td>
							</tr>
						</table>
					</td>
				</tr>			
		  </table>
		</td>
	</tr>
	<!-- <tr>
		<td> <img src="images/tpixel.gif" width="1" height="5"></td>
	</tr> -->
	<tr>
		<td valign="top">
			<table border="0" style="border-color:<?php echo $border_blue_local_anes;?>;" align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF" class="all_border">
				<tr>
					<td colspan="3"><img src="images/tpixel.gif" width="1" height="5"></td>
				</tr>
				<?php
					//CODE RELATED TO ANES 3 SIGNATURE ON FILE
						$anesthesia3SignOnFileStatus = "Yes";
						$TDanesthesia3NameIdDisplay = "block";
						$TDanesthesia3SignatureIdDisplay = "none";
						$Anesthesia3Name = $loggedInUserName;
						if($signAnesthesia3Id<>0 && $signAnesthesia3Id<>"") {
							$Anesthesia3Name = $signAnesthesia3LastName.", ".$signAnesthesia3FirstName." ".$signAnesthesia3MiddleName;
							$anesthesia3SignOnFileStatus = $signAnesthesia3Status;	
							
							$TDanesthesia3NameIdDisplay = "none";
							$TDanesthesia3SignatureIdDisplay = "block";
						}
					
						//CODE TO REMOVE ANES 3 SIGNATURE
							if($_SESSION["loginUserId"]==$signAnesthesia3Id) {
								$callJavaFunPostOpDel = "document.frm_local_anes_rec.hiddSignatureId.value='TDanesthesia3NameId'; return displaySignature('TDanesthesia3NameId','TDanesthesia3SignatureId','local_anes_record_ajaxSign1.php','$loginUserId','Anesthesia3','delSign');";
							}else {
								$callJavaFunPostOpDel = "alert('Only Dr. $Anesthesia3Name can remove this signature');";
							}
						//END CODE TO REMOVE ANES 3 SIGNATURE	
						
					//END CODE RELATED TO ANES 3 SIGNATURE ON FILE
					
					//CODE RELATED TO SURGEON SIGNATURE ON FILE
						if($loggedInUserType<>"Surgeon") {
							
							$loginUserName = $_SESSION['loginUserName'];
							$callJavaFunSurgeon = "return noAuthorityFun('Surgeon');";
						
						//}else if ($loggedInUserType=="Surgeon" && !$loggedInSignatureOfUser) {
							//$callJavaFunSurgeon = "return noSignInAdmin();";
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
								$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
							}
						//END CODE TO REMOVE SURGEON SIGNATURE															
					
					//END CODE RELATED TO SURGEON SIGNATURE ON FILE
					
					


				?>
				<tr align="left" valign="middle" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
					<td width="10"></td>
					<td nowrap colspan="2" class="text_10">
						<div id="TDanesthesia3NameId" style="display:<?php echo $TDanesthesia3NameIdDisplay;?>; " >
							<table border="0" cellpadding="0" cellspacing="0" >
								<tr>
									<td style="cursor:hand; " class="text_10b" nowrap onClick="javascript:<?php echo $callJavaFunPostOp;?>">
										Anesthesiologist Signature
									</td>
								</tr>
								
							</table>
						</div>
						<div id="TDanesthesia3SignatureId" style="display:<?php echo $TDanesthesia3SignatureIdDisplay;?>; ">	
							<table border="0" cellpadding="0" cellspacing="0" >
								<tr>
									<td class="text_10" nowrap style="cursor:hand;" onClick="javascript:<?php echo $callJavaFunPostOpDel;?>">
										<?php echo "<b>Anesthesiologist :</b>"." Dr. ". $Anesthesia3Name; ?>
									</td>
								</tr>
								<tr>
									<td  class="text_10" nowrap>
										<b>Signature on File : </b><?php echo $anesthesia3SignOnFileStatus; ?>
									</td>
								</tr>
							</table>
						</div>
					</td>
					
					<td width="70"></td>
					<td nowrap  colspan="2" class="text_10">
						<div id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>; " >
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td style="cursor:hand; " class="text_10b" nowrap onClick="javascript:<?php echo $callJavaFunSurgeon;?>">
										Surgeon Signature
									</td>
								</tr>
							</table>
						</div>
						<div id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>; ">	
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td class="text_10" nowrap style="cursor:hand;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>">
										<?php echo "<b>Surgeon :</b>"." Dr. ".$Surgeon1Name; ?>
									</td>
								</tr>
								<tr>
									<td class="text_10" nowrap><b>Signature on File : </b><?php echo $surgeon1SignOnFileStatus;?>   </td>
								</tr>
							</table>
						</div>
					</td>
					<td width="70"></td>
					<td nowrap  colspan="2" class="text_10b">
						Relief Nurse&nbsp;
						<select name="relivedPostNurseIdList" class="text_10" style=" width:120">
							<option value="">Select</option>	
								<?php
								$relivedPostNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
								$relivedPostNurseRes = imw_query($relivedPostNurseQry) or die(imw_error());
								while($relivedPostNurseRow=imw_fetch_array($relivedPostNurseRes)) {
									$relivedSelectPostNurseID = $relivedPostNurseRow["usersId"];
									$relivedPostNurseName = $relivedPostNurseRow["lname"].", ".$relivedPostNurseRow["fname"]." ".$relivedPostNurseRow["mname"];
									$sel="";
									if($relivedPostNurseId==$relivedSelectPostNurseID) {
										$sel = "selected";
									} 
									else {
										$sel = "";
									}
															
								?>	
									<option value="<?php echo $relivedSelectPostNurseID;?>" <?php echo $sel;?>><?php echo $relivedPostNurseName;?></option>
								<?php
								}
								?>
						</select>						
					</td>
					<td bgcolor="<?php echo $bglight_blue_local_anes; ?>" class="text_10"></td>
				</tr>
				<tr align="left" valign="middle" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
					<td width="10"></td>
					 <td width="200" class="text_10" nowrap></td> 
					<td align="left" width="50" class="text_10"></td>
					<td></td>
					<td width="200" class="text_10" nowrap>  </td>
					<td align="left" width="50" class="text_10"></td>
					<td colspan="3" bgcolor="<?php echo $bglight_blue_local_anes; ?>" class="text_10"></td>
				</tr>
				<tr>
					<td colspan="3"><img src="images/tpixel.gif" width="1" height="5"></td>
				</tr>
			</table>
		</td>
	</tr>		
</table>
</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="moveLeftToSlider" method="post" action="local_anes_record.php?cancelRecord=true">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->	
</body>
<?php
if($finalizeStatus!='true'){
	?>
	<script>
		top.setPNotesHeight();
		top.document.getElementById('Finalized').style.display = 'none';
	</script>
	<?php
}else{
	?>
	<script>
		top.setPNotesHeight();		
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
