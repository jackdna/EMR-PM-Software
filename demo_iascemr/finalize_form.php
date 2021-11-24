<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php

include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
//SHOW DIV ALERT IF FORM IS FINALIZED
	$finalize_alert = $_REQUEST['finalize_alert'];
	
	if($finalize_alert=='true') {
	?>
		<div id="divFinalizeAlert" style="position:absolute;left:-13500px; top:200; ">
			<?php //include('finalizeDivPopUp.php'); ?>
		</div>
	<?php	
	}
//SHOW DIV ALERT IF FORM IS FINALIZED
if(!$pConfId) {
	$pConfId 	= $_REQUEST['pConfId'];
}
if(!$patient_id) {
	$patient_id = $_REQUEST['patient_id'];
}

//START CODE IF "A" IN SAN IS GREEN THEN ADD NARCOTICS DATA (FROM MAC/REGIONAL ANES) FOR NARCOTICS REPORT 
$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
if(strtolower(trim($chartSignedByAnes)) == "green") {
	$objManageData->calculate_narcotics_data($_REQUEST["pConfId"]); 	
}
//END CODE IF "A" IN SAN IS GREEN THEN ADD NARCOTICS DATA (FROM MAC/REGIONAL ANES) FOR NARCOTICS REPORT 

//START CODE OF HL7- DFT GENERATION FOR ALBANY(PPMC) IF LOCAL ANESTHESIA AND DISCHARGE SUMMARY IS COMPLETED
if($_REQUEST["SaveForm_alert"]=='true' && $form_status == "completed" && ($tablename == "localanesthesiarecord" || $tablename == "dischargesummarysheet") ) {
	if(constant('DCS_DFT_GENERATION')==true && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','mackool'))){

		/*
		 * PRL Check - Waltham
		 * Check if ann the surgeon charts are completed and signed.
		 * Setting this to true allways here, Because this is under the condition of discharge summary completed
		 * and executed on sign all by surgeon option.
		*/
		if( $tablename == "dischargesummarysheet" && strtolower($GLOBALS["LOCAL_SERVER"]) ==  'waltham' )
		{	$log_prl_hl7 = true;
			/*
			 * Sugeon - Sign all check
			*/
			/*$sql = "SELECT `chartSignedBySurgeon` FROM `stub_tbl` WHERE `patient_confirmation_id`=".(INT)$pConfId;
			$resp = imw_query($sql);
			if( $resp && imw_num_rows($resp) > 0 )
			{
				$resp = imw_fetch_assoc($resp);
				if( trim($resp['chartSignedBySurgeon']) === 'green' )
				{
					$log_prl_hl7 = true;
				}
			}*/
		}

		$sendToVal = (in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham')))?'PPMC':'KARNEY';
		$getHL7SentStatusQry = "SELECT id,sent FROM hl7_sent WHERE sch_id = '".$pConfId."' AND msg_type = 'DFT' AND send_to ='".$sendToVal."' LIMIT 0,1 ";
		$getHL7SentStatusRes= imw_query($getHL7SentStatusQry) or die($getHL7SentStatusQry.imw_error());
		$getHL7SentStatusNumRow =imw_num_rows($getHL7SentStatusRes);
		if($getHL7SentStatusNumRow <= 0) { //IF DFT NOT GENERATED, ONLY THEN GENERATE DFT
			include(dirname(__FILE__)."/dft_hl7_generate.php");
		}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham'))){
			$getHL7SentStatusRS =imw_fetch_assoc($getHL7SentStatusRes);
			if($getHL7SentStatusRS['sent']=='0') include(dirname(__FILE__)."/dft_hl7_generate.php");
		}
	}
}
//END CODE OF HL7- DFT GENERATION FOR ALBANY(PPMC) IF LOCAL ANESTHESIA AND DISCHARGE SUMMARY IS COMPLETED

// FINALIZE CURR PATIENT CONFIRMATION 
if($_REQUEST['finalizeForm']=='true') {
	
	$udpateFinalizeStatusQry= "update patientconfirmation set finalize_status = '".$_REQUEST['finalizeForm']."' where patientConfirmationId = '$pConfId'";
	$udpateFinalizeStatusRes= imw_query($udpateFinalizeStatusQry);
	$objManageData->calculateCost($pConfId);
	$objManageData->calculate_narcotics_data($pConfId); //ADD NARCOTICS DATA (FROM MAC/REGIONAL ANES) FOR NARCOTICS REPORT
	// Store Finalize history in finalize_history table 
	$FHistory	=	$objManageData->getRowRecord('finalize_history',  'patient_confirmation_id', $pConfId, 'finalize_history_id', 'DESC', ' count(*) as resultRows ');

	$insertRecords		=	array();
	$insertRecords['patient_confirmation_id']		=	$pConfId;
	$insertRecords['finalize_action']						=	'finalize';
	$insertRecords['finalize_action_script	']		=	'manual';
	$insertRecords['finalize_action_type']			=	($FHistory->resultRows > 0 ) ? 'revised' : 'original' ;
	$insertRecords['finalize_action_user_id']		=	$_SESSION['loginUserId'];
	$insertRecords['finalize_action_datetime']	=	date('Y-m-d H:i:s' );
	
	$objManageData->addRecords($insertRecords, 'finalize_history');
	// End store finalize history
	
	//calculate cost for surgery
			
	//DESTROY ALL EPOST-IT
		$chk_allEpostQry 	= "select * from `eposted` where patient_conf_id = '$pConfId'";
		$chk_allEpostRes 	= imw_query($chk_allEpostQry);
		$chk_allEpostNumRow = imw_num_rows($chk_allEpostRes);
		if($chk_allEpostNumRow>0) {
			$delAllEpostQry = "delete from  `eposted` where patient_conf_id = '$pConfId'";
			$delAllEpostRes = imw_query($delAllEpostQry);
		}
			
	//END DESTROY ALL EPOST-IT

	//DESTROY ALL PATIENT ALERT	
	$updateAlrtQry="UPDATE iolink_patient_alert_tbl SET alert_disabled = 'yes',alert_disabled_date_time = '".date('Y-m-d H:i:s')."', alert_disabled_by = '".$_SESSION["loginUserId"]."', disabled_section='chart finalize' WHERE patient_id  = '".$patient_id."'";
	$updateAlrtRes = imw_query($updateAlrtQry);			
	//END DESTROY ALL PATIENT ALERT	
	
	/*******HL7- DFT GENERATION***********/
	if(constant('DCS_DFT_GENERATION')==true && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman','gnysx'))){
		include(dirname(__FILE__)."/dft_hl7_generate.php");
	}
	/*******DFT GENERATION END************/
	
	/*Post charges to Advanced MD, by using Advanced MD API call*/
	if( defined( 'AMD_POST_CHARGES' ) && AMD_POST_CHARGES === "YES" && (int)$pConfId > 0 )
	{
		$apptConfirmationID = $pConfId;
		$apptPatientId = $patient_id;
		
		$callFromSC = true;
		include_once(dirname(__DIR__).'/'.$iolinkDirectoryName.'/library/amd/amd_post_charges.php');
	}
	/*Post charges to Advanced MD, by using Advanced MD API call*/
	?>
	<script>
		
		
		var thisId1 	= '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 	= '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 	= '<?php echo $_REQUEST["preColor"];?>';
		
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 	= '<?php echo $_REQUEST["pConfId"];?>';
		var ascId1 		= '<?php echo $_REQUEST["ascId"];?>';
		var finalizePageName 	= '<?php echo $finalizePageName;?>';
		
		if(typeof(top.sxAlertFinalizeFun!="undefined")) {
			top.sxAlertFinalizeFun();//FUNCTION CREATED IN header.php
		}
		top.frames[0].alert_msg('finalize');
		top.frames[0].location 	= 'blankform.php?frameHref='+finalizePageName+'&patient_id='+patient_id1+'&pConfId='+pConfId1+'&finalize_alert=true&thisId='+thisId1+'&innerKey='+innerKey1+'&preColor='+preColor1;
	</script>
	
	<?php
	
}

// FINALIZE CURR PATIENT CONFIRMATION

//FUNCTION TO GET STATUS OF EACH FORM  AS COMPLETED OR NOT COMPLETED
	function getFinalizeFormStatus($tablename,$pConfId) {					
		$formStatusQry = "select * from $tablename where confirmation_id = '".$pConfId."'";
		$formStatusRes = imw_query($formStatusQry) or die(imw_error());
		$formStatusRow = imw_fetch_array($formStatusRes);
		$formStatusName= $formStatusRow["form_status"];
		return $formStatusName;
	}	
	function getFinalizeAnotherFormStatus($tablename,$pConfId) {
		$formStatusQry = "select * from $tablename where patient_confirmation_id = '".$pConfId."'";
		$formStatusRes = imw_query($formStatusQry) or die(imw_error());
		$formStatusRow = imw_fetch_array($formStatusRes);
		$formStatusName= $formStatusRow["form_status"];
		return $formStatusName;
	}
//END FUNCTION TO GET STATUS OF EACH FORM  AS COMPLETED OR NOT COMPLETED

if(constant('CHECKLIST_DATE')) {
	$chkListDtConfirmQry 	= "SELECT patientConfirmationId FROM patientconfirmation WHERE patientConfirmationId='".$pConfId."' AND dos >= '".constant('CHECKLIST_DATE')."'";	
	$chkListDtConfirmRes 	= imw_query($chkListDtConfirmQry) or die($chkListDtConfirmQry.imw_error());
	$chkListDtConfirmNumRow = imw_num_rows($chkListDtConfirmRes);
	
}
$chkListDateExist="";
if($chkListDtConfirmNumRow>0 || !constant('CHECKLIST_DATE')) {
	$chkListDateExist = "yes";
}


//CODE TO GET FORM STATUS
	$checkListFinalizeFormStatus 					= getFinalizeFormStatus("surgical_check_list",$pConfId);
	$surgeryConsentFinalizeFormStatus 				= getFinalizeFormStatus("surgery_consent_form",$pConfId);
	$HippaConsentFinalizeFormStatus		 			= getFinalizeFormStatus("hippa_consent_form",$pConfId);
	$BenefitConsentFinalizeFormStatus 				= getFinalizeFormStatus("benefit_consent_form",$pConfId);
	$InsuranceConsentFinalizeFormStatus 			= getFinalizeFormStatus("insurance_consent_form",$pConfId);
	$dischargeSummaryFinalizeFormStatus 			= getFinalizeFormStatus("dischargesummarysheet",$pConfId);
	$preopHealthQuestFinalizeFormStatus 			= getFinalizeFormStatus("preophealthquestionnaire",$pConfId);		
	$historyPhysicialFinalizeFormStatus 			= getFinalizeFormStatus("history_physicial_clearance",$pConfId);			
	$preopNursingFinalizeFormStatus 				= getFinalizeFormStatus("preopnursingrecord",$pConfId);
	$postopNursingFinalizeFormStatus 				= getFinalizeFormStatus("postopnursingrecord",$pConfId);		
	$macRegionalAnesthesiaFinalizeFormStatus 		= getFinalizeFormStatus("localanesthesiarecord",$pConfId);
	$preopGenralAnesthesiaFinalizeFormStatus 		= getFinalizeFormStatus("preopgenanesthesiarecord",$pConfId);
	$GenralAnesthesiaFinalizeFormStatus 			= getFinalizeFormStatus("genanesthesiarecord",$pConfId);
	$genralAnesthesiaNursesNotesFinalizeFormStatus 	= getFinalizeFormStatus("genanesthesianursesnotes",$pConfId);		
	$OpRoomRecordFinalizeFormStatus 				= getFinalizeFormStatus("operatingroomrecords",$pConfId);
	$surgicalOperativeRecordFinalizeFormStatus 		= getFinalizeFormStatus("operativereport",$pConfId);
	$AmendmentsNotesFinalizeFormStatus 				= getFinalizeFormStatus("amendment",$pConfId);		
	$preopPhysicianFinalizeFormStatus 				= getFinalizeAnotherFormStatus("preopphysicianorders",$pConfId);
	$postopPhysicianFinalizeFormStatus 				= getFinalizeAnotherFormStatus("postopphysicianorders",$pConfId);
	$InstructionSheetFinalizeFormStatus 			= getFinalizeAnotherFormStatus("patient_instruction_sheet",$pConfId);
	$LaserProcedureFinalizeFormStatus 				= getFinalizeFormStatus("laser_procedure_patient_table",$pConfId);
	$InjectionMiscFinalizeFormStatus 				= getFinalizeFormStatus("injection",$pConfId);
//END CODE TO GET FORM STATUS

$surgeryCenterSettings = $objManageData->loadSettings('safety_check_list');
$showCheckListAdmin = $surgeryCenterSettings['safety_check_list'] ? 'true' : 'false';
$showCheckList = ( $showCheckListAdmin <> 'true' && ($checkListFinalizeFormStatus == 'completed' || $checkListFinalizeFormStatus == 'not completed')) ? 'true' : $showCheckListAdmin;
$showCheckListStatus = 	$objManageData->getChartShowStatus($pConfId,'checklist');
$showCheckList = $showCheckListStatus ? ($showCheckListStatus == 1 ? 'true' : ($showCheckListStatus == 2 ? 'false' : $showCheckList)) : $showCheckList;

?>
<form name="finalizeMe" method="post" action="<?php echo $finalizePageName;?>?finalizeForm=true">
	<input type="hidden" name="patient_id" 	value="<?php echo $patient_id; 	?>">
	<input type="hidden" name="pConfId"  	value="<?php echo $pConfId; 	?>">
	<input type="hidden" name="ascId"    	value="<?php //echo $ascId; 	?>">
	<input type="hidden" name="thisId" 	 	value="<?php echo $thisId; 		?>">
	<input type="hidden" name="innerKey" 	value="<?php echo $innerKey; 	?>">
	<input type="hidden" name="preColor" 	value="<?php echo $preColor; 	?>">
	
	<input type="hidden" name="chkListDateExist" 						id="chkListDateExist"					value="<?php echo $chkListDateExist;					?>">
	<input type="hidden" name="showCheckList" 						id="showCheckList"					value="<?php echo $showCheckList;					?>">

	<input type="hidden" name="checkListFinalizeFormStatus" 			id="checkListFinalizeFormStatus" 		value="<?php echo $checkListFinalizeFormStatus; 		?>">
	<input type="hidden" name="surgeryConsentFinalizeFormStatus" 		id="surgeryConsentFinalizeFormStatus" 	value="<?php echo $surgeryConsentFinalizeFormStatus; 	?>">
	<input type="hidden" name="HippaConsentFinalizeFormStatus" 			id="HippaConsentFinalizeFormStatus" 	value="<?php echo $HippaConsentFinalizeFormStatus; 		?>">
	<input type="hidden" name="BenefitConsentFinalizeFormStatus" 		id="BenefitConsentFinalizeFormStatus" 	value="<?php echo $BenefitConsentFinalizeFormStatus; 	?>">
	<input type="hidden" name="InsuranceConsentFinalizeFormStatus" 		id="InsuranceConsentFinalizeFormStatus" value="<?php echo $InsuranceConsentFinalizeFormStatus; 	?>">
	<input type="hidden" name="dischargeSummaryFinalizeFormStatus" 		id="dischargeSummaryFinalizeFormStatus" value="<?php echo $dischargeSummaryFinalizeFormStatus; 	?>">
	<input type="hidden" name="preopHealthQuestFinalizeFormStatus" 		id="preopHealthQuestFinalizeFormStatus" value="<?php echo $preopHealthQuestFinalizeFormStatus; 	?>">
    <input type="hidden" name="historyPhysicialFinalizeFormStatus" 		id="historyPhysicialFinalizeFormStatus"  value="<?php echo $historyPhysicialFinalizeFormStatus; 	?>">
	<input type="hidden" name="preopNursingFinalizeFormStatus" 			id="preopNursingFinalizeFormStatus" 	value="<?php echo $preopNursingFinalizeFormStatus; 		?>">
	<input type="hidden" name="postopNursingFinalizeFormStatus" 		id="postopNursingFinalizeFormStatus" 	value="<?php echo $postopNursingFinalizeFormStatus; 	?>">
	<input type="hidden" name="macRegionalAnesthesiaFinalizeFormStatus" id="macRegionalAnesthesiaFinalizeFormStatus" value="<?php echo $macRegionalAnesthesiaFinalizeFormStatus; ?>">
	<input type="hidden" name="preopGenralAnesthesiaFinalizeFormStatus" id="preopGenralAnesthesiaFinalizeFormStatus" value="<?php echo $preopGenralAnesthesiaFinalizeFormStatus; ?>">
	<input type="hidden" name="GenralAnesthesiaFinalizeFormStatus" 		id="GenralAnesthesiaFinalizeFormStatus" 	 value="<?php echo $GenralAnesthesiaFinalizeFormStatus; 	 ?>">
	<input type="hidden" name="genralAnesthesiaNursesNotesFinalizeFormStatus" id="genralAnesthesiaNursesNotesFinalizeFormStatus" value="<?php echo $genralAnesthesiaNursesNotesFinalizeFormStatus; 	?>">
	<input type="hidden" name="OpRoomRecordFinalizeFormStatus" 			id="OpRoomRecordFinalizeFormStatus" 	value="<?php echo $OpRoomRecordFinalizeFormStatus; 		?>">
	<input type="hidden" name="surgicalOperativeRecordFinalizeFormStatus" 	  id="surgicalOperativeRecordFinalizeFormStatus" 	 value="<?php echo $surgicalOperativeRecordFinalizeFormStatus; 		?>">
	<!-- <input type="hidden" name="AmendmentsNotesFinalizeFormStatus" 	id="AmendmentsNotesFinalizeFormStatus" 	value="<?php //echo $AmendmentsNotesFinalizeFormStatus; ?>"> -->
	<input type="hidden" name="preopPhysicianFinalizeFormStatus" 		id="preopPhysicianFinalizeFormStatus" 	value="<?php echo $preopPhysicianFinalizeFormStatus; 	?>">
	<input type="hidden" name="postopPhysicianFinalizeFormStatus" 		id="postopPhysicianFinalizeFormStatus" 	value="<?php echo $postopPhysicianFinalizeFormStatus; 	?>">
	<input type="hidden" name="InstructionSheetFinalizeFormStatus" 		id="InstructionSheetFinalizeFormStatus" value="<?php echo $InstructionSheetFinalizeFormStatus; 	?>">
	<input type="hidden" name="LaserProcedureFinalizeFormStatus" 		id="LaserProcedureFinalizeFormStatus" 	value="<?php echo $LaserProcedureFinalizeFormStatus; 	?>">
  <input type="hidden" name="InjectionMiscFinalizeFormStatus" 		id="InjectionMiscFinalizeFormStatus" 	value="<?php echo $InjectionMiscFinalizeFormStatus; 	?>">

</form>

<?php
// GETTING CONFIRMATION DETAILS
	$detailFinalStatusConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeFormStatus = trim($detailFinalStatusConfirmation->finalize_status);	
// GETTING CONFIRMATION DETAILS

//START GET MATCHED PRACTICE OF LOGGED-IN SURGEON AND ASSIGNED SURGEON
	$surgeonIdConf = $detailFinalStatusConfirmation->surgeonId;

	$surgeryQry="select * from surgerycenter where surgeryCenterId='1' LIMIT 0,1";
	$surgeryRes= imw_query($surgeryQry) or die(imw_error());
	$surgeryRow=imw_fetch_array($surgeryRes);
	$practiceNameMatch = "";
	if($_SESSION['loginUserType'] == "Surgeon" && $_SESSION['loginUserId'] != $surgeonIdConf && $surgeryRow['peer_review'] == 'Y') {
		$practiceNameMatch = $objManageData->getPracMatchUserId($_SESSION['loginUserId'],$surgeonIdConf); //USE THIS IN privilege_buttons.php
		//GIVE VEIW ONLY ACCESS IF LOGGED-IN SURGEON IS DIFFERENT FROM ASSIGNED SURGEON WITHIN SAME PRACTICE
	}
//END GET MATCHED PRACTICE OF LOGGED-IN SURGEON AND ASSIGNED SURGEON

$priviliges		=	explode(",",$_SESSION['userPrivileges']);
$priviliges		=	array_map('trim',$priviliges);
$priviligesTo	=	array('Admin','Super User');
$hasAdminPrv	=	(count(array_intersect($priviligesTo,$priviliges)) > 0 ) ? 1 : 0;

if($finalizeFormStatus=='true'  && $hasAdminPrv == 1){
		$stubD	=	$objManageData->getRowRecord('stub_tbl','patient_confirmation_id',$pConfId,'stub_id','ASC','stub_id');
?>
	<script>
		var UV	=	"#unfinalized"+<?=$pConfId?>;
		top.$(UV).attr('onclick',"top.unfinalize('<?=$pConfId?>','<?=$patient_id?>', '<?=$ascId?>', '<?=$stubD->stub_id?>');");
		if(top.$(UV).hasClass('displayNone') )
		{
			top.$(UV).removeClass('displayNone').addClass('displayInlineBlock');
		}
		
	</script>
<?PHP
	
} 
else
{
?>	
	<script>
		var UV	=	"#unfinalized"+<?=$pConfId?>;
		top.$(UV).attr('onclick',"");
		if(top.$(UV).hasClass('displayInlineBlock'))
		{
			top.$(UV).removeClass('displayInlineBlock').addClass('displayNone');
		}
	</script>
<?php	
}

if($finalizeFormStatus=='true' || $_SESSION['loginUserType']!='Nurse' || $_SESSION['loginUserType']!='Surgeon') {
	?>
	<script>
		//START DO NOT SHOW EDIT ICON FOR NURSE IF RECORD IS FINALIZE. ALSO DO NOT SHOW THIS ICON IF LOGIN WITH OTHER THAN NURSE
		$(document).ready(function() {
			var sessUserType = "<?php echo $_SESSION['loginUserType'];?>";
			var sessUserId = "<?php echo $_SESSION['loginUserId'];?>";
			var surgeonIdConf = "<?php echo $surgeonIdConf;?>";
			var finalizeFormStatusEdit = "<?php echo $finalizeFormStatus;?>";
			$(".dynamic_sig_dt .fa-edit").each(function(){
				if(finalizeFormStatusEdit == 'true') {
					$(this).remove();	
				}else {
				
					if(sessUserType != "Nurse") {
						if($(this).hasClass('fa-editsurg')) {
							
						}else {
							$(this).remove();
						}
					}
					
					if(sessUserType != "Surgeon" || sessUserId != surgeonIdConf) {
						if($(this).hasClass('fa-editsurg')) {
							$(this).remove();
						}
					}
				}
			})
		 });
		 //END DO NOT SHOW EDIT ICON FOR NURSE IF RECORD IS FINALIZE. ALSO DO NOT SHOW THIS ICON IF LOGIN WITH OTHER THAN NURSE
	
	</script>
<?php
}
if($finalizeFormStatus=='true'){
	?>
	<script>
		top.$("#finalized"+<?php echo $pConfId; ?>+"").html('Finalized');		
		top.frames[0].setPNotesHeight();
		top.frames[0].displayFooterPrintButton();	
		
	</script>
    
<?php
//START CODE TO REMOVE ALL MANDATORY CHECKS IF CHART IS FINALIZED AND FLAG OF CHART IS GREEN 
if($historyPhysicialFinalizeFormStatus == "completed" || $preopPhysicianFinalizeFormStatus == "completed" || $postopPhysicianFinalizeFormStatus == "completed" || $macRegionalAnesthesiaFinalizeFormStatus == "completed" || $postopNursingFinalizeFormStatus == "completed")
{  
	
?>
<script>
    $(document).ready(function() {
        var currForm = top.mainFrame.main_frmInner.document.forms[0];
		var currFrmNameAction = currForm.frmAction.value;
		var formStatus = '';
		if(currFrmNameAction=='history_physicial_clearance.php')	{ formStatus = '<?=$historyPhysicialFinalizeFormStatus?>' ; }
		else if(currFrmNameAction=='pre_op_physician_orders.php')	{ formStatus = '<?=$preopPhysicianFinalizeFormStatus?>' ; }
		else if(currFrmNameAction=='post_op_physician_orders.php')	{ formStatus = '<?=$postopPhysicianFinalizeFormStatus?>' ; }
		else if(currFrmNameAction=='local_anes_record.php')			{ formStatus = '<?=$macRegionalAnesthesiaFinalizeFormStatus?>' ; }
		else if(currFrmNameAction=='post_op_nursing_record.php')	{ formStatus = '<?=$postopNursingFinalizeFormStatus?>' ; }
		
		if( (currFrmNameAction=='history_physicial_clearance.php' || currFrmNameAction=='pre_op_physician_orders.php' || currFrmNameAction=='post_op_physician_orders.php' || currFrmNameAction=='local_anes_record.php' || currFrmNameAction=='post_op_nursing_record.php') && (formStatus != '' && formStatus == 'completed') ) {
			$( "input[type*='checkbox']").each(function(){
				$(this).parent().removeClass("colorChkBx");	
				$(this).parent().css('background-color', 'white');
			})
			$( ".sign_link").each(function(){
				$(this).css('background-color', 'white');
			})
			$( "input[type*='text']").each(function(){
				$(this).css('background-color', 'white');
			})
			$( ".selectpicker").each(function(){
				$(this).parent().css('background-color', '#FFFFFF');
			})
			$( "textarea").each(function(){
				$(this).css('background-color', '#FFFFFF');
			})
		}
    });
</script>
<?php
}
}
//END CODE TO REMOVE ALL MANDATORY CHECKS IF CHART IS FINALIZED AND FLAG OF CHART IS GREEN

// START GETTING DISCHARGE SUMMARY SHEET FORM STATUS
$disSheetStatus = '';
if( strtolower($_SESSION['loginUserType']) == 'surgeon' ){
	$disSheetRes = $objManageData->getExtractRecord('dischargesummarysheet','confirmation_id',$pConfId,'form_status');
	$disSheetStatus = trim($disSheetRes['form_status']);
}
// END GETTING DISCHARGE SUMMARY SHEET FORM STATUS
?>
<script>
//START IF SURGEON CLICK ON 'SIGN ALL BUTTON' BY OPENING ANY CHARTNOTE THEN RUN THIS CODE	
	if(top.document.getElementById('hidd_SaveOnSingAllSurgeon')) { //(FROM mainpage.php)
		if(top.document.getElementById('hidd_SaveOnSingAllSurgeon').value=='true') {
			var d = '<?php echo $disSheetStatus;?>';
			if(d != 'completed' && d != 'not completed')
				top.document.getElementById('confirmType').value = 'discharge';
			
			top.document.getElementById('hidd_SaveOnSingAllSurgeon').value='';
			top.myTimerSurgeonSignAll(); //(FROM mainpage.php)
		}
	}	
//END IF SURGEON CLICK ON 'SIGN ALL BUTTON' BY OPENING ANY CHARTNOTE THEN RUN THIS CODE	

//START IF ANESTHESIOLOGIST CLICK ON 'SIGN ALL BUTTON' BY OPENING ANY CHARTNOTE THEN RUN THIS CODE	
	if(top.document.getElementById('hidd_SaveOnSingAllAnes')) { //(FROM mainpage.php)
		if(top.document.getElementById('hidd_SaveOnSingAllAnes').value=='true') {
			top.document.getElementById('hidd_SaveOnSingAllAnes').value='';
			top.myTimerAnesSignAll(); //(FROM mainpage.php)
		}
	}	
//END IF ANESTHESIOLOGIST CLICK ON 'SIGN ALL BUTTON' BY OPENING ANY CHARTNOTE THEN RUN THIS CODE	

var tablename_display_butt = '<?php echo $tablename;?>';
var pConfId = '<?php echo $_REQUEST['pConfId'];?>';

<!--PURGE-->
	if(top.document.getElementById("PurgeBtn")) {
		top.document.getElementById("PurgeBtn").style.display = "none";
		if(tablename_display_butt=="consent_multiple_form" || tablename_display_butt=="patient_instruction_sheet"){
				top.document.getElementById("PurgeBtn").style.display = "inline-block";
				if(tablename_display_butt=="patient_instruction_sheet") {
					var form_status = '<?php echo $form_status;?>';
					getFormPurgeReset(pConfId,form_status);
				}
		}else{
			top.document.getElementById("PurgeBtn").style.display = "none";
		}
	}	

<!--PURGE-->

<!--RESET-->
	if(top.document.getElementById("ResetBtn")) {
		top.document.getElementById("ResetBtn").style.display = "none";
		if(tablename_display_butt=="preophealthquestionnaire" || tablename_display_butt=="history_physicial_clearance" || tablename_display_butt=="localanesthesiarecord"  || tablename_display_butt=="preopgenanesthesiarecord" || tablename_display_butt=="genanesthesiarecord" || tablename_display_butt=="genanesthesianursesnotes" || tablename_display_butt=="transfer_followups"){
				top.document.getElementById("ResetBtn").style.display = "inline-block";
		}else{
			top.document.getElementById("ResetBtn").style.display = "none";
		}
	}	

<!--RESET-->

//START CODE TO SET SCROLL TOP
if(top.document.getElementById('hiddScrollTop')) {
	var frmScrlTop=parseFloat(top.document.getElementById('hiddScrollTop').value);
	frmScrlTop=parseFloat(frmScrlTop);
	if(top.mainFrame) {
		if(top.mainFrame.main_frmInner) {
			if(top.document.getElementById('hiddScrollTop').value>100){
				top.mainFrame.main_frmInner.document.body.scrollTop=parseFloat(frmScrlTop);
				if(tablename_display_butt=="localanesthesiarecord" && frmScrlTop>615) 	{ frmScrlTop=615; 	}
				if(tablename_display_butt=="genanesthesiarecord" && frmScrlTop>155) 	{ frmScrlTop=155; 	}
				if(tablename_display_butt=="genanesthesianursesnotes" && frmScrlTop>80) { frmScrlTop=80;	}
				if(document.getElementById('divSaveAlert')) {
					document.getElementById('divSaveAlert').style.top=frmScrlTop;
				}
				if(tablename_display_butt!='operatingroomrecords') {
					top.document.getElementById('hiddScrollTop').value='';
				}	
			}
		}	
	}	
}	
//END CODE TO SET SCROLL TOP

</script>