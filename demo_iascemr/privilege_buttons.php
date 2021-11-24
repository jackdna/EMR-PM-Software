<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$loginUserType = $_SESSION['loginUserType'];
$permissionToWriteChart='yes';
if($loginUserType=='Surgeon') {
	if($tablename=='preophealthquestionnaire'
		|| $tablename=='preopnursingrecord'	|| $tablename=='postopnursingrecord' 		
		|| $tablename=='localanesthesiarecord'		|| $tablename=='preopgenanesthesiarecord' 	
		|| $tablename=='genanesthesiarecord'		|| $tablename=='genanesthesianursesnotes' 	
		|| $tablename=='surgical_check_list'		
		) { $permissionToWriteChart='no';
	}
	
	if($tablename == 'dischargesummarysheet' && !trim($ascId))
	{
			$permissionToWriteChart='no';	
	}
	
	if(trim($practiceNameMatch)) { //FROM finalize_form.php  GIVE VEIW ONLY ACCESS IF LOGGED-IN SURGEON IS DIFFERENT FROM ASSIGNED SURGEON WITHIN SAME PRACTICE
		$permissionToWriteChart='no';
	}	
}else if($loginUserType=='Anesthesiologist') {
	if($tablename=='preophealthquestionnaire'
		|| $tablename=='preopnursingrecord' 		|| $tablename=='postopnursingrecord' 		
		|| $tablename=='preopphysicianorders'		|| $tablename=='postopphysicianorders' 		
		|| $tablename=='operativereport'			|| $tablename=='dischargesummarysheet' 		
		|| $tablename=='surgical_check_list' 		
		|| $tablename=='laser_procedure_patient_table' || $tablename=='injection'
		|| $tablename=='patient_medication_reconciliation_sheet'
		) {	$permissionToWriteChart='no';
	}
}else if($loginUserType=='Nurse') {
	if($tablename=='localanesthesiarecord' 			|| $tablename=='preopgenanesthesiarecord' 
		|| $tablename=='genanesthesiarecord'
		) { $permissionToWriteChart='no';
	}
}else if($loginUserType=='Coordinator') {
	if($tablename=='preopnursingrecord' 			|| $tablename=='postopnursingrecord' 
		|| $tablename=='preopphysicianorders' 		|| $tablename=='postopphysicianorders'
		|| $tablename=='localanesthesiarecord' 		|| $tablename=='preopgenanesthesiarecord'
		|| $tablename=='genanesthesiarecord' 		|| $tablename=='genanesthesianursesnotes'
		|| $tablename=='operatingroomrecords' 		|| $tablename=='operativereport'
		|| $tablename=='dischargesummarysheet' 		|| $tablename=='patient_instruction_sheet'
		|| $tablename=='laser_procedure_patient_table'
		|| $tablename=='surgical_check_list'		|| $tablename=='injection'
		|| $tablename=='patient_medication_reconciliation_sheet'
		) {	$permissionToWriteChart='no';
	}
}else if($loginUserType=='Staff') {
	if($tablename=='preopnursingrecord' 			|| $tablename=='postopnursingrecord' 
		|| $tablename=='preopphysicianorders' 		|| $tablename=='postopphysicianorders'
		|| $tablename=='localanesthesiarecord' 		|| $tablename=='preopgenanesthesiarecord'
		|| $tablename=='genanesthesiarecord' 		|| $tablename=='genanesthesianursesnotes'
		|| $tablename=='operatingroomrecords' 		|| $tablename=='operativereport'
		|| $tablename=='dischargesummarysheet'
		|| $tablename=='surgical_check_list'		|| $tablename=='laser_procedure_patient_table'
		|| $tablename=='injection'
		) {	$permissionToWriteChart='no';
	}
}else {
	$permissionToWriteChart='no';
}
//START ALLOW SUPER-USER TO ACCES OPERATIVE RECORD (OVERWRITE PERMISSIONS FOR OPERATIVE RECORD)
$privileges 	= $_SESSION['userPrivileges'];
$privilegesArr 	= array();
$privilegesArr 	= explode(', ', $privileges);
if($tablename=='operativereport' && in_array('Super User', $privilegesArr)) {
	$permissionToWriteChart='yes';
}
//END ALLOW SUPER-USER TO ACCES OPERATIVE RECORD (OVERWRITE PERMISSIONS FOR OPERATIVE RECORD)

//START ALLOW ALL USERS TO CREATE AMANDMENTS
if($tablename=='amendment') {
	$permissionToWriteChart='yes';	
}
//END ALLOW ALL USERS TO CREATE AMANDMENTS

$pConfId 	= $_REQUEST['pConfId'];
//start hiding button in case of chart is cancelled
//check patient status in pt comfirmation
$qCheckCancel=imw_query("select patient_status from stub_tbl  WHERE patient_confirmation_id='".$pConfId."' and (patient_status='Canceled' OR patient_status='No Show' OR patient_status='Aborted Surgery')");
if(imw_num_rows($qCheckCancel)>=1)$permissionToWriteChart='no';

if($permissionToWriteChart=='no') {
?>
	<script>
		//START CODE TO HIDE SAVE BUTTON RUN THROUGH AJAX IN THE CHART WHERE NOT REQUIRED
		var chartInnerButtons = new Array('preopNurseVitalSaveId','postopNurseVitalSaveId','preopPhyMedSaveId',
										  'genAnesNurseNewNotesSaveId','opRoomIolScanBtnId','opRoomIolUploadBtnId',
										  'laserProcedureMedSaveId','dischargeSummaryIolScanBtnId','uploadBtnDivId',
										  'uploadImageDivId','below_summary_dummy'
										 )
		for(var z=0;z<chartInnerButtons.length;z++) {
			if(document.getElementById(chartInnerButtons[z])) {
				document.getElementById(chartInnerButtons[z]).style.visibility='hidden';	
			}
		}
		//END CODE TO HIDE SAVE BUTTON RUN THROUGH AJAX IN THE CHART WHERE NOT REQUIRED
		
		//START CODE TO HIDE SAVE BUTTON IN CHARTNOTE WHERE NOT REQUIRED
			top.document.getElementById('footer_button_id').style.display = 'none';
			top.frames[0].displayFooterPrintButton();
		//END CODE TO HIDE SAVE BUTTON IN CHARTNOTE	WHERE NOT REQUIRED
	</script>
<?php			
}
elseif($permissionToWriteChart=='yes')
{
	
?>
	<script>
		$(document).ready(function()
		{ 
			if(top.document.getElementById('saveBtn').hasAttribute('disabled')){
				top.document.getElementById('saveBtn').removeAttribute('disabled');
			}
		});
	</script>
<?php
}

?>