<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$patient_id = $_REQUEST['patient_id'];
$pConfId = $_REQUEST['pConfId'];
$get_http_path=$_REQUEST['get_http_path'];
$table_main="";
//GETTING CONFIRNATION DETAILS
	$Confirm_patientPrimProcHp = "";
	$Confirm_patientPrimaryProcedureIdHp = 0;
	$getConfirmationDetailsHp = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($getConfirmationDetailsHp){
		$Confirm_patientPrimProcHp = stripslashes($getConfirmationDetailsHp->patient_primary_procedure);
		$Confirm_patientPrimaryProcedureIdHp = $getConfirmationDetailsHp->patient_primary_procedure_id;
	}
	
//GETTING CONFIRNATION DETAILS
	$primary_procedureHpQry 			= "SELECT * FROM procedures WHERE name = '".addslashes($Confirm_patientPrimProcHp)."' OR procedureAlias='".addslashes($Confirm_patientPrimProcHp)."'";
	$primary_procedureHpRes 			= imw_query($primary_procedureHpQry);
	if(imw_num_rows($primary_procedureHpRes)<=0) {
		$primary_procedureHpQry 			= "SELECT * FROM procedures WHERE procedureId = '".$Confirm_patientPrimaryProcedureIdHp."'";
		$primary_procedureHpRes 			= imw_query($primary_procedureHpQry);
	}
	$patient_primary_procedure_categoryID='';
	if(imw_num_rows($primary_procedureHpRes)>0) {
		$primary_procedureHpRow 			= imw_fetch_array($primary_procedureHpRes);
		$patient_primary_procedure_categoryID = $primary_procedureHpRow['catId'];
	}


	if($pConfId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord('history_physicial_clearance', 'confirmation_id', $pConfId," *, if(date_of_h_p!='0000-00-00',date_format(date_of_h_p,'%m-%d-%Y'),'') as date_of_h_p_informat,if(signSurgeon1DateTime!='0000-00-00',date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p'),'') as signSurgeon1DateTimeFormat,if(signAnesthesia1DateTime!='0000-00-00',date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p'),'') as signAnesthesia1DateTimeFormat,if(signNurseDateTime!='0000-00-00',date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p'),'') as signNurseDateTimeFormat ");
	}

	if(is_array($getPreOpQuesDetails)){
		extract($getPreOpQuesDetails);
		$preHealthQuestFormStatus = $form_status;
	}
//START CODE TO GET SUB TYPE OF USER
$usrAllSubTypeArr = array();
$usrAllQry 							= "SELECT usersId,user_sub_type FROM users";
$usrAllRes							= imw_query($usrAllQry);
if(imw_num_rows($usrAllRes)>0) {
	while($usrAllRow				= imw_fetch_array($usrAllRes)) {
		$usrAllId 					= $usrAllRow["usersId"];
		$userAllSubType 			= $usrAllRow["user_sub_type"];
		$usrAllSubTypeArr[$usrAllId]= $userAllSubType;
	}
}
//END CODE TO GET SUB TYPE OF USER
	
$historyPhysicialFormStatus=$form_status;
if($historyPhysicialFormStatus=='completed' || $historyPhysicialFormStatus=='not completed'){	
$anes_allergy = "SELECT allergy_name,reaction_name FROM patient_allergies_tbl where patient_confirmation_id='".$pConfId."'";
$result_anes_allergy = imw_query($anes_allergy);
$num_result_anes_allergy = imw_num_rows($result_anes_allergy);	

$anes_med = "Select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id=$pConfId order by prescription_medication_name";
$result_anes_med = imw_query($anes_med);
$num_result_anes_med = imw_num_rows($result_anes_med);	

$table_main.=$head_table."\n";	
$table_main.='<table style="width:700px;font-size:14px;border:1px solid #C0C0C0; margin-top:10px;" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="fheader bgcolor">H &amp; P Clearance</td>
				</tr>
				<tr>
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding-top:5px;">History And Physical</td>
								<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center; padding-top:5px;">Yes/No</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">CAD/MIN(W/ WO Stent OR CABG)/PVD)';
								if($cadMI=="Yes" && trim($cadMIDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($cadMIDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($cadMI!=""){$table_main.=$cadMI;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">CVA/TIA/ Epilepsy, Neurological';
								if($cvaTIA=="Yes" && trim($cvaTIADesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($cvaTIADesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($cvaTIA!=""){$table_main.=$cvaTIA;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">HTN/ +/- CP/SOB on Exertion';
								if($htnCP=="Yes" && trim($htnCPDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($htnCPDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($htnCP!=""){$table_main.=$htnCP;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Anticoagulation therapy (i.e. Blood Thinners)';
								if($anticoagulationTherapy=="Yes" && trim($anticoagulationTherapyDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($anticoagulationTherapyDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($anticoagulationTherapy!=""){$table_main.=$anticoagulationTherapy;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Respiratory - Asthma / COPD / Sleep Apnea';
								if($respiratoryAsthma=="Yes" && trim($respiratoryAsthmaDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($respiratoryAsthmaDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($respiratoryAsthma!=""){$table_main.=$respiratoryAsthma;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Arthritis';
								if($arthritis=="Yes" && trim($arthritisDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($arthritisDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($arthritis!=""){$table_main.=$arthritis;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Diabetes';
								if($diabetes=="Yes" && trim($diabetesDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($diabetesDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($diabetes!=""){$table_main.=$diabetes;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Recreational Drug Use';
								if($recreationalDrug=="Yes" && trim($recreationalDrugDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($recreationalDrugDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($recreationalDrug!=""){$table_main.=$recreationalDrug;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">GI - GERD / PUD / Liver Disease / Hepatitis';
								if($giGerd=="Yes" && trim($giGerdDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($giGerdDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($giGerd!=""){$table_main.=$giGerd;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Ocular';
								if($ocular=="Yes" && trim($ocularDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($ocularDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($ocular!=""){$table_main.=$ocular;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Kidney Disease, Dialysis, G-U';
								if($kidneyDisease=="Yes" && trim($kidneyDiseaseDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($kidneyDiseaseDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($kidneyDisease!=""){$table_main.=$kidneyDisease;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">HIV, Autoimmune Diseases, Contagious Diseases';
								if($hivAutoimmune=="Yes" && trim($hivAutoimmuneDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($hivAutoimmuneDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($hivAutoimmune!=""){$table_main.=$hivAutoimmune;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">History of Cancer';
								if($historyCancer=="Yes" && trim($historyCancerDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($historyCancerDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($historyCancer!=""){$table_main.=$historyCancer;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Organ Transplant';
								if($organTransplant=="Yes" && trim($organTransplantDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($organTransplantDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($organTransplant!=""){$table_main.=$organTransplant;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">A Bad Reaction to Local or General Anesthesia';
								if($badReaction=="Yes" && trim($badReactionDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($badReactionDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($badReaction!=""){$table_main.=$badReaction;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
	
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">High Cholesterol';
								if($highCholesterol=="Yes" && trim($highCholesterolDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($highCholesterolDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($highCholesterol!=""){$table_main.=$highCholesterol;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
	
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Thyroid';
								if($thyroid=="Yes" && trim($thyroidDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($thyroidDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($thyroid!=""){$table_main.=$thyroid;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
	
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Ulcers';
								if($ulcer=="Yes" && trim($ulcerDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($ulcerDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($ulcer!=""){$table_main.=$ulcer;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
	
							$table_main.='
							<tr>
								<td colspan="2" style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Other:&nbsp;'.stripslashes($otherHistoryPhysical).'</td>
							</tr>';
							
							
							if($version_num > 1)
							{
								
								// Heart Exam Field
								$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Heart Exam done with stethoscope - Normal';
								if($heartExam=="No" && trim($heartExamDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.htmlentities(stripslashes($heartExamDesc)).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($heartExam!=""){$table_main.=$heartExam;}else{$table_main.="____";}
								$table_main.='</td>
								</tr>';
								
								
								//Lung Exam 		
								$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Lung Exam done with stethoscope - Normal';
								if($lungExam=="No" && trim($lungExamDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.htmlentities(stripslashes($lungExamDesc)).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px; text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($lungExam!=""){$table_main.=$lungExam;}else{$table_main.="____";}
								$table_main.='</td>
								</tr>';
								
							}
							if($version_num > 2) {
								$table_main.='
								<tr>
									<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Discussed Advanced Directives and Patient Rights and Responsibilities</td>
									<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
										if($discussedAdvancedDirective!=""){$table_main.=$discussedAdvancedDirective;}else{$table_main.="____";}
								$table_main.='
									</td>
								</tr>';
							}
	
							// Start Printing H&P Dynamic questions
							$getAddQuestions = $objManageData->getAllRecords('history_physical_ques','', array('confirmation_id = '=>$pConfId), array(),array('ques + 0'=>'ASC'));
							if( is_array($getAddQuestions) && count($getAddQuestions) > 0 ) {
								foreach($getAddQuestions as $qArr){
									$table_main.='<tr>
																	<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">'.stripslashes($qArr->ques);
																	if($qArr->ques_status=="Yes" && trim($qArr->ques_desc)){
																		$table_main.='<br>
																		<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
																			<tr>
																				<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
																				<td style="width:200px;">'.htmlentities(stripslashes($qArr->ques_desc)).'</td>
																			</tr>
																		</table>';
																	}
																	$table_main.='</td>
																	<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
																	if($qArr->ques_status!=""){$table_main.=$qArr->ques_status;}else{$table_main.="____";}
																	$table_main.='</td>
																</tr>';		
								}	
							}
								
						$table_main.='</table>
					</td>			
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" style="width:300px;padding-top:5px;font-weight:bold;border-bottom:1px solid #C0C0C0;">Allergies</td>							
							</tr>
							<tr>
								<td style="width:175px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">Name</td>
								<td style="width:150px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">Reaction</td>
							</tr>';
								if($num_result_anes_allergy>0){
									while($row_anes_allergy=imw_fetch_assoc($result_anes_allergy)){
										$table_main.='
										<tr>
											<td style="width:175px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.stripslashes(htmlentities($row_anes_allergy['allergy_name'])).'</td>
											<td style="width:150px;padding:5px;border-bottom:1px solid #C0C0C0;">'.stripslashes(htmlentities($row_anes_allergy['reaction_name'])).'</td>
										</tr>';
									}
								}else{
									for($i_alrg=1;$i_alrg<=3;$i_alrg++) {
										$table_main.='
										<tr>
											<td style="width:175px;padding:3px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:150px;padding:3px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
										</tr>';
									}

								}
						$table_main.='	
							<tr>
								<td colspan="2" style="width:300px;padding-top:5px;font-weight:bold;border-bottom:1px solid #C0C0C0;">Medications</td>							
							</tr>
							
							<tr>
								<td colspan="2" style="width:350px;">
									<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
												<tr>
													<td style="width:125px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Name</td>
													<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Dosage</td>
													<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Sig</td>
												</tr>';
										if($num_result_anes_med>0){
											while($row_anes_med=imw_fetch_assoc($result_anes_med)){
											$table_main.='
												<tr>
													<td style="width:125px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($row_anes_med['prescription_medication_name']).'</td>
													<td style="width:100px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($row_anes_med['prescription_medication_desc']).'</td>
													<td style="width:100px;padding:5px;border-bottom:1px solid #C0C0C0;">'.htmlentities($row_anes_med['prescription_medication_sig']).'</td>
												</tr>';														
											}
										}else {
											for($i_med=1;$i_med<=3;$i_med++) {
												$table_main.='
												<tr>
													<td style="width:125px;padding-top:5px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
													<td style="width:100px;padding-top:5px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
													<td style="width:100px;padding-top:5px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
												</tr>';
											}
										}
								$table_main.='		
									</table>
								</td>
							</tr>';							
							
							
							
							
									
							$table_main.='
							<tr>
								<td colspan="2" style="width:350px;">
									<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding-top:5px;">Day Of Surgery Notes</td>
											<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center; padding-top:5px;">Yes/No</td>
										</tr>
										<tr>
											<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">Date of Last Menstrual Cycle&nbsp;';
												if(trim($date_of_h_p_informat)){
													$table_main.=$date_of_h_p_informat.'<br>';
												}
											$table_main.='</td>
											<td style="width:50px;text-align:center;border-bottom:1px solid #C0C0C0; padding-top:5px;"></td>
										</tr>												
										<tr>
											<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Wear Contact Lenses';
											if($wearContactLenses=="Yes" && trim($wearContactLensesDesc)){
											$table_main.='<br>
												<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
													<tr>
														<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:250px;">'.stripslashes($wearContactLensesDesc).'</td>
													</tr>
												</table>';
											}
											$table_main.='</td>
											<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
											if($wearContactLenses!=""){$table_main.=$wearContactLenses;}else{$table_main.="____";}
											$table_main.='</td>
										</tr>
										<tr>
											<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Smoking';
											if($smoking=="Yes" && trim($smokingDesc)){
											$table_main.='<br>
												<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
													<tr>
														<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:250px;">'.stripslashes($smokingDesc).'</td>
													</tr>
												</table>';
											}
											$table_main.='</td>
											<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
											if($smoking!=""){$table_main.=$smoking;}else{$table_main.="____";}
											$table_main.='</td>
										</tr>
										<tr>
											<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Drink Alcohal';
											if($drinkAlcohal=="Yes" && trim($drinkAlcohalDesc)){
											$table_main.='<br>
												<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
													<tr>
														<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:250px;">'.stripslashes($drinkAlcohalDesc).'</td>
													</tr>
												</table>';
											}
											$table_main.='</td>
											<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
											if($drinkAlcohal!=""){$table_main.=$drinkAlcohal;}else{$table_main.="____";}
											$table_main.='</td>
										</tr>
										<tr>
											<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Have an automatic internal defibrillator';
											if($haveAutomatic=="Yes" && trim($haveAutomaticDesc)){
											$table_main.='<br>
												<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
													<tr>
														<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:250px;">'.stripslashes($haveAutomaticDesc).'</td>
													</tr>
												</table>';
											}
											$table_main.='</td>
											<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
											if($haveAutomatic!=""){$table_main.=$haveAutomatic;}else{$table_main.="____";}
											$table_main.='</td>
										</tr>
										<tr>
											<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Medical History obtained from';
											if($medicalHistoryObtained=="Yes" && trim($medicalHistoryObtainedDesc)){
											$table_main.='<br>
												<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
													<tr>
														<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:250px;">'.stripslashes($medicalHistoryObtainedDesc).'</td>
													</tr>
												</table>';
											}
											$table_main.='</td>
											<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
											if($medicalHistoryObtained!=""){$table_main.=$medicalHistoryObtained;}else{$table_main.="____";}
											$table_main.='</td>
										</tr>
										<tr>
											<td colspan="2" style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Notes:&nbsp;'.stripslashes($otherNotes).'</td>
										</tr>
									</table>
								   </td>
								</tr>';
				$table_main.='</table>
					</td>
				</tr>
			</table>';
	
$Anesthesia1PreFix = 'Dr.';
if($usrAllSubTypeArr[$signAnesthesia1Id]=='CRNA') {
	$Anesthesia1PreFix = '';
}				

$table_main.='<table cellpadding="0" cellspacing="0" style="border:1px solid #C0C0C0;width:735px;padding:5px; font-size:14px;">';
				$table_main.='
					<tr>
						<td style="width:250px;">';
							if($signSurgeon1Id<>0 && $signSurgeon1Id<>""){
								$table_main.="<b>Surgeon:&nbsp;</b> Dr. ". $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
								$table_main.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$table_main.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signSurgeon1DateTime);
							}else {
								$table_main.="<b>Surgeon:&nbsp;</b>________";
								$table_main.="<br><b>Electronically Signed:&nbsp;</b>________";
								$table_main.="<br><b>Signature Date:&nbsp;</b>________";
							}
						$table_main.='
						</td>
						<td style="width:250px;">';
							if($patient_primary_procedure_categoryID<>'2') {
								if($signAnesthesia1Id<>0 && $signAnesthesia1Id<>""){
									$table_main.="<b>Anesthesia Provider:&nbsp;</b>". " ".$Anesthesia1PreFix." ". $signAnesthesia1LastName.", ".$signAnesthesia1FirstName." ".$signAnesthesia1MiddleName;
									$table_main.="<br><b>Electronically Signed:&nbsp;</b>Yes";
									$table_main.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia1DateTime);
								}else {
									$table_main.="<b>Anesthesia Provider:&nbsp;</b>________";
									$table_main.="<br><b>Electronically Signed:&nbsp;</b>________";
									$table_main.="<br><b>Signature Date:&nbsp;</b>________";
								}
							}
						$table_main.='
						</td>
						<td style="width:250px;">';
							if($signNurseId<>0 && $signNurseId<>""){
								$table_main.="<b>Nurse:&nbsp;</b> ". $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
								$table_main.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$table_main.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signNurseDateTime);
							}else {
								$table_main.="<b>Nurse:&nbsp;</b>________";
								$table_main.="<br><b>Electronically Signed:&nbsp;</b>________";
								$table_main.="<br><b>Signature Date:&nbsp;</b>________";
							}
						$table_main.='
						</td>
					</tr>';
		$table_main.='</table>';
}
?>