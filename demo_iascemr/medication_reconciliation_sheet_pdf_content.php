<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$patient_id 	= $_REQUEST['patient_id'];
$pConfId 			= $_REQUEST['pConfId'];
$get_http_path=	$_REQUEST['get_http_path'];
$table_main		=	"";
	

if($pConfId){
		
		// Start Getting Confirmation Table Details
		$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
		$patientConfirmSiteTempSite 	=	$detailConfirmation->site;
		$patientConfirmProcedureName	=	$detailConfirmation->patient_primary_procedure;
	
		// APPLYING NUMBERS TO PATIENT SITE
		$patientConfirmSiteName='';
		if($patientConfirmSiteTempSite == 1) {
			$patientConfirmSiteName = "Left Eye";  			//OD
		}else if($patientConfirmSiteTempSite == 2) {
			$patientConfirmSiteName = "Right Eye";  		//OS
		}else if($patientConfirmSiteTempSite == 3) {
			$patientConfirmSiteName = "Both Eye";  			//OU
		}else{
			$patientConfirmSiteName = "Operative Eye";  //OU
		}
		// END APPLYING NUMBERS TO PATIENT SITE
	
		
		$getReconSheetDetails = $objManageData->getExtractRecord('patient_medication_reconciliation_sheet', 'confirmation_id', $pConfId," *,if(signSurgeon1DateTime!='0000-00-00',date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p'),'') as signSurgeon1DateTimeFormat,if(signNurseDateTime!='0000-00-00',date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p'),'') as signNurseDateTimeFormat");
	}

if($getReconSheetDetails){
		extract($getReconSheetDetails);
		
		$reconSheetFormStatus = $form_status;
		
		$blankLine				=	'_____';
		$blankLineLong		=	'__________';
		
		//$aspirin					=	($aspirin)	?	$aspirin		:	$blankLine;
		//$allergic_med				=	($allergic_med)		?	$allergic_med			:	$blankLine;
		//$allergic_betadine=	($allergic_betadine)?	$allergic_betadine	:	$blankLine;
		//$allergic_latex		=	($allergic_latex)	?	$allergic_latex		:	$blankLine;
		$drop_schedule		=	($drop_schedule)	?	'Yes'	:	$blankLine;
		$start_post_op_drops=	($start_post_op_drops)	?	'Yes'	:	$blankLine;
		$resume_med				=	($resume_med)	?	'Yes'	:	$blankLine;
		
		$discontinue			=	($discontinue)			?	$discontinue				:	$blankLineLong;
		//$allergic_med_detail=	($allergic_med_detail)			?	$allergic_med_detail				:	$blankLineLong;
		
		$NurseName				=	$signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		$NurseNameShow		=	($signNurseId)		?	$NurseName				:	$blankLineLong;	
		$NurseSignStatus	=	($signNurseId)		?	$signNurseStatus	:	$blankLineLong;	
		$NurseSignDateTime=	($signNurseId)		?	$objManageData->getFullDtTmFormat($signNurseDateTime)	:	$blankLineLong;	
		
		$Surgeon1Name				=	$signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
		$Surgeon1NameShow		=	($signSurgeon1Id)		?	'Dr. '.$Surgeon1Name			:	$blankLineLong;	
		$Surgeon1SignStatus	=	($signSurgeon1Id)		?	$signSurgeon1Status	:	$blankLineLong;	
		$Surgeon1SignDateTime=($signSurgeon1Id)		?	$objManageData->getFullDtTmFormat($signSurgeon1DateTime):$blankLineLong;	
		
		
		$allergiesPreOp	=	$objManageData->getArrayRecords('patient_allergies_tbl','patient_confirmation_id',$pConfId);
		$healthQuestMed	=	$objManageData->getArrayRecords('patient_prescription_medication_healthquest_tbl','confirmation_id',$pConfId,'prescription_medication_name','ASC');
		$medTakenTodayPreOp	=	$objManageData->getArrayRecords('patient_prescription_medication_tbl','confirmation_id',$pConfId,'prescription_medication_name','ASC'); 
		
		//Start Creating HTML for Allergies
		$dataArray	=	$innArray	=	array();
		$medValue		=	$dosValue	=	$endLoop = $dataCount = $innValue =	'';
		
		$dataArray	=	$allergiesPreOp;
		$dataCount	=	count($dataArray);
		$endLoop		=	($dataCount < 3) ? 3	:	$dataCount;
		$allergiesHTML=	'';
		for($loop = 0; $loop < $endLoop ; $loop++)
		{ 
			$innArray	=	$dataArray[$loop];
			$allergy	=	$innArray->allergy_name;
			$reaction	=	$innArray->reaction_name;
			
			$allergiesHTML	.=	'<tr>';
			$allergiesHTML	.=	'<td style="width:250px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop" >'.htmlentities($allergy).'</td>';
			$allergiesHTML	.=	'<td style="width:450px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop ">'.htmlentities($reaction).'</td>';
			$allergiesHTML	.=	'</tr>';
		}
		
		//Start Creating HTML for Pre Op Medications
		$dataArray	=	$innArray	=	array();
		$medValue		=	$dosValue	=	$endLoop = $dataCount = $innValue =	'';
		
		$dataArray	=	$healthQuestMed;
		$dataCount	=	count($dataArray);
		$endLoop		=	($dataCount < 3) ? 3	:	$dataCount;
		$healthQuestMedHTML=	'';
		for($loop = 0; $loop < $endLoop ; $loop++)
		{ 
			$innArray	=	$dataArray[$loop];
			$medValue	=	$innArray->prescription_medication_name;
			$dosValue	=	$innArray->prescription_medication_desc;
			$sigValue	=	$innArray->prescription_medication_sig;
			$lastDoseTakenValue	=	$innArray->prescription_medication_last_dose_taken;
			
			$healthQuestMedHTML	.=	'<tr>';
			$healthQuestMedHTML	.=	'<td style="width:250px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop" >'.htmlentities($medValue).'</td>';
			$healthQuestMedHTML	.=	'<td style="width:150px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop ">'.htmlentities($dosValue).'</td>';
			$healthQuestMedHTML	.=	'<td style="width:150px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop ">'.htmlentities($sigValue).'</td>';
			$healthQuestMedHTML	.=	'<td style="width:150px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop ">'.$lastDoseTakenValue.'</td>';
			$healthQuestMedHTML	.=	'</tr>';
		}
		
}

		
if($reconSheetFormStatus == 'completed' || $reconSheetFormStatus=='not completed')
{
	$table_main.=$head_table."\n";
	$table_main.='<table style="width:700px;font-size:14px;border:1px solid #C0C0C0; margin-top:10px;" cellpadding="0" cellspacing="0">';
	
	$table_main.='
				<tr>
					<td colspan="2" style="width:700px;" class="fheader bgcolor">Medication Reconciliation Sheet</td>
				</tr>';
	
	
	// Start Printing Allergies
	$table_main.='
				<tr>
					<td style="width:700px;vertical-align:top;" class="bdrBtmRght" colspan="2" >
						<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm bgcolor" style="width:100%" colspan="2" valign="middle"><b>Allergies/Drug Reaction</b></td>
							</tr>
							<tr>
								<td style="width:250px;" nowrap="nowrap" class="text_10 bdrrght bold pt5">Name</td>
								<td style="width:450px;" nowrap="nowrap" class="text_10 bdrrght bold pt5">Reaction</td>
							</tr>
							'.$allergiesHTML.'
						</table>
					</td>
				</tr>
				
				';
				
	
	// Start Printing Health Questionnaire Medications Block			
	$table_main.='
				<tr>
					<td style="width:700px;vertical-align:top;" class="bdrBtmRght" colspan="2" >
						<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm bgcolor" style="width:100%" colspan="4" valign="middle"><b>Current Medications</b></td>
							</tr>
							<tr>
								<td style="width:250px;" nowrap="nowrap" class="text_10 bdrrght bold pt5" >Name</td>
								<td style="width:150px;" nowrap="nowrap" class="text_10 bdrrght bold pt5">Dosage</td>
								<td style="width:150px;" nowrap="nowrap" class="text_10 bdrrght bold pt5">Sig</td>
								<td style="width:150px;" nowrap="nowrap" class="text_10 bdrrght bold pt5">Last Dose Taken</td>
							</tr>
							'.$healthQuestMedHTML.'
						</table>
					</td>
				</tr>
				
				';
				
				
	// Start Printing Post Op Medications/Complications Block			
	$table_main.='
				<tr>
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm pt5" style="width:280px;"><b>Drop Schedule to be given at post-operative appointment</b></td>
								<td class="bdrbtm txt_rgt pt5 pr5" style="width:70px;">'.$drop_schedule.'</td>
							</tr>
							
							<tr>
								<td class="bdrbtm pt5" style="width:280px;"><b>Resume all home medications</b></td>
								<td class="bdrbtm txt_rgt pt5 pr5" style="width:70px;">'.$resume_med.'</td>
							</tr>
							
						</table>
					</td>
					
					<td style="width:350px;vertical-align:top;" class="bdrbtm">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm pt5" style="width:280px;"><b>Start post-operative drops day of surgery</b></td>
								<td class="bdrbtm txt_rgt pt5 pr5" style="width:70px;">'.$start_post_op_drops.'</td>
							</tr>
							
							<tr>
								<td class="pt5" colspan="2" valign="middle" style="width:350px;"><b>Discontinue:&nbsp;</b>'.$discontinue.'</td>
							</tr>
							
							
						</table>
					</td>
					
					
				</tr>';										
	
	// Start Printing Nurse2 and Surgeon1 Signature
	$table_main.='
				<tr>
					<td style="width:350px;vertical-align:top;" nowrap="nowrap" class="text_10 bdrbtm">
							<span class="pt5"><b>Surgeon Signature:	</b> '.$Surgeon1NameShow.'</span><br>
							<span class="pt5"><b>Electronically Signed:	</b>'.$Surgeon1SignStatus.'</span><br>
							<span class="pt5"><b>Signature Date	:	</b>'.$Surgeon1SignDateTime.'</span><br>
					</td>
					
					<td style="width:350px;vertical-align:top;" nowrap="nowrap" class="text_10 bdrbtm">
							<span class="pt5"><b>Nurse Signature:	</b>'.$NurseNameShow.'</span><br>
							<span class="pt5"><b>Electronically Signed:	</b>'.$NurseSignStatus.'</span><br>
							<span class="pt5"><b>Signature Date	:	</b>'.$NurseSignDateTime.'</span><br>
					</td>
					
				</tr>';	
					
				
	$table_main.='</table>';
	
}
?>