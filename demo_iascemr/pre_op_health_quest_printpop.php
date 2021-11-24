<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include_once("common/conDb.php");
//print '<pre>';
$root=($_SERVER['DOCUMENT_ROOT']);
include("common_functions.php");
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
// allergies_status_reviewed(table field t be added)
$patient_id = $_REQUEST['patient_id'];
$pConfId = $_REQUEST['pConfId'];
$get_http_path=$_REQUEST['get_http_path'];
include("new_header_print.php");

$lable="Pre-Op Health Questionnaire";
//Getting allergies to uncheck
// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$allergiesNKDA_status = $detailConfirmation->allergiesNKDA_status;
	$noMedicationStatus = $detailConfirmation->no_medication_status;
	$noMedicationComments = $detailConfirmation->no_medication_comments;
	
// GETTING CONFIRMATION DETAILS
$table_inner1="";
$table_main="";
//adminHealthquestionare
	$selectAdminQuestionsQry="select * from healthquestioner";
	$selectAdminQuestions=imw_query($selectAdminQuestionsQry);
	$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
	$i=0;
	while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
	{
	foreach($ResultselectAdminQuestions as $key=>$value){
			$question[$i][$key]=$value;
		}
		$i++;	
	}
	
	//echo $question[2]['question'];
//End adminHealthquestionare


 	$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'preophealthquestionnaire' AND patient_conf_id = '$pConfId' ";
	$rsNotes =imw_query($query_rsNotes);
	$totalRows_rsNotes =imw_num_rows($rsNotes);


	//$table = 'allergies';
	//include("common/pre_defined_popup.php");
	if($preOpHealthQuesId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord('preophealthquestionnaire', 'preOpHealthQuesId', $preOpHealthQuesId," *, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat ");
	}else if($pConfId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord('preophealthquestionnaire', 'confirmation_id', $pConfId," *, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat ");
	}
	if(is_array($getPreOpQuesDetails)){
		extract($getPreOpQuesDetails);
		$preHealthQuestFormStatus = $form_status;
	}
	?>
	
	<!--<img align="right"  src="http://'.$_SERVER['HTTP_HOST'].'/surgerycenter/images/'.$img.'" width="175" height="28">-->
	
<?php
$allergy1 = "Select allergy_name,reaction_name from patient_allergies_tbl where patient_confirmation_id=$pConfId";
$result = imw_query($allergy1);
$num = imw_num_rows($result);	
if($num >0) {
	$allergiesValueNKA = '__';
	while($row = imw_fetch_array($result)) {
		$allergy1_rows[] = 	$row;	
	}
	if($num==1 && trim(strtoupper($allergy1_rows[0]["allergy_name"]))=="NKA") {
		$allergiesValueNKA = 'Yes';
	}
}else if($allergiesNKDA_status=="Yes") {
	$allergiesValueNKA = 'Yes';
}else {
	$allergiesValueNKA = '__';
}
$medication_health = "Select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_prescription_medication_healthquest_tbl where confirmation_id='".$pConfId."' order by prescription_medication_name";
$medresult_health = imw_query($medication_health);
$num_medhealth = @imw_num_rows($medresult_health);

$table_main.=$head_table."\n";	
$table_main.='<table style="width:700px;font-size:14px;border:1px solid #C0C0C0; margin-top:10px;" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="fheader bgcolor">Pre-Op Health Questionnaire</td>
				</tr>
				<tr>
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding-top:5px;">Have you ever had</td>
								<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center; padding-top:5px;">Yes/No</td>
							</tr>
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">Heart Trouble/Heart Attack';
									if($heartTrouble=="Yes" && trim($heartTroubleDesc)){
										$table_main.='<br>
										<table style="width:250px; padding-top:5px; font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($heartTroubleDesc).'</td>
											</tr>
										</table>';
									}
								$table_main.='</td>
								<td style="width:50px;text-align:center;border-bottom:1px solid #C0C0C0; padding-top:5px;">';
								if($heartTrouble!=""){$table_main.=$heartTrouble;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Stroke';
								if($stroke=="Yes" && trim($strokeDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:200px;">'.stripslashes($strokeDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($stroke!=""){$table_main.=$stroke;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">HighBP';
								if($HighBP=="Yes" && trim($HighBPDesc)){
									$table_main.='<br><table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($HighBPDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($HighBP!=""){$table_main.=$HighBP;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Anticoagulation therapy (i.e. Blood Thinners)';
								if($anticoagulationTherapy=="Yes" && trim($anticoagulationTherapyDesc)){
								$table_main.='<br><table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($anticoagulationTherapyDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($anticoagulationTherapy!=""){$table_main.=$anticoagulationTherapy;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Asthma, Sleep Apnea, Breathing Problems';
									if($asthma=="Yes" && trim($asthmaDesc)){
										$table_main.='<br><table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($asthmaDesc).'</td>
											</tr>
										</table>';
									}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($asthma!=""){$table_main.=$asthma;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Tuberculosis';
								if($tuberculosis=="Yes" && trim($tuberculosisDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($tuberculosisDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($tuberculosis!=""){$table_main.=$tuberculosis;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Diabetes';
								if($insulinDependence){
									$table_main.='<br>';
									if($insulinDependence=="Yes"){
										$table_main.='<i><b>Insulin Dependent:</b>&nbsp;Yes</i>';
									}else if($insulinDependence=="No"){
										$table_main.='<i><b>Non-Insulin Dependent:</b>&nbsp;Yes</i>';	
									}
								}
								if($diabetes=="Yes" && trim($diabetesDesc)){
									$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.htmlentities(stripslashes($diabetesDesc)).'</td>
											</tr>
										</table>';
								}
							$table_main.='</td>
								<td style="width:50px;padding-top:5px;text-align:center;border-bottom:1px solid #C0C0C0;">';
								if($diabetes!=""){	$table_main.=$diabetes;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Epilepsy, Convulsions, Parkinson\'s, Vertigo';
								if($epilepsy=="Yes" && trim($epilepsyDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($epilepsyDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($epilepsy!=""){$table_main.=$epilepsy;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Restless Leg Syndrome';
								if($restlessLegSyndrome=="Yes" && trim($restlessLegSyndromeDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($restlessLegSyndromeDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($restlessLegSyndrome!=""){$table_main.=$restlessLegSyndrome;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
								$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Hepatitis';
								if($hepatitis=="Yes" && ($hepatitisA=='true' || $hepatitisB=='true' || $hepatitisC=='true')){
									$table_main.='<br>';
									if($hepatitisA=='true'){
										$table_main.='<b>A:</b>&nbsp;Yes';			
									}
									if($hepatitisB=='true'){
										$table_main.='&nbsp;<b>B:</b>&nbsp;Yes';			
									}
									if($hepatitisC=='true'){
										$table_main.='&nbsp;<b>C:</b>&nbsp;Yes';			
									}	
								}
								if($hepatitis=="Yes" && trim($hepatitisDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($hepatitisDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($hepatitis!=""){	$table_main.=$hepatitis;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Kidney Disease, Dialysis';
								
								if($kidneyDisease=="Yes" && trim($kidneyDiseaseDesc)){
								$table_main.='<br>
									<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">';
										if($shunt!=""){
											$table_main.='
											<tr>
												<td colspan="2"><b>Do you have a Shunt:</b>&nbsp;'.$shunt.'</td>
											</tr>	';
										}
										if($fistula!=""){
											$table_main.='
											<tr>
												<td colspan="2"><b>Fistula:</b>&nbsp;'.$fistula.'</td>
											</tr>';	
										}
										$table_main.='<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($kidneyDiseaseDesc).'</td>
											</tr>';
										
								$table_main.='</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($kidneyDisease!=""){	$table_main.=$kidneyDisease;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">HIV, Autoimmune Diseases';
								if($hivAutoimmuneDiseases=="Yes" && trim($hivTextArea)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($hivTextArea).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($hivAutoimmuneDiseases!=""){$table_main.=$hivAutoimmuneDiseases;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">History of cancer';
								$brestCancerLeftRight = '';
								if($cancerHistory=="Yes" &&$brestCancerLeft){
									if($brestCancerLeft=="Yes"){
										$brestCancerLeftRight = "Left";
									}else if($brestCancerLeft=="No"){
										$brestCancerLeftRight = "Right";
									}
									if($brestCancerLeft){
										$table_main.='<br><i><b>Breast Cancer:</b>&nbsp;'.$brestCancerLeftRight.'</i>';
									}
								}
								if($cancerHistory=="Yes" && trim($cancerHistoryDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($cancerHistoryDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($cancerHistory!=""){	$table_main.=$cancerHistory;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Organ Transplant';
								if($organTransplant=="Yes" && trim($organTransplantDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($organTransplantDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($organTransplant!=""){	$table_main.=$organTransplant;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">A Bad Reaction to Local or General Anesthesia';
								if($anesthesiaBadReaction=="Yes" && trim($anesthesiaBadReactionDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($anesthesiaBadReactionDesc).'</td>
											</tr>';
										$table_main.='</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($anesthesiaBadReaction!=""){	$table_main.=$anesthesiaBadReaction;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td colspan="2" style="width:350px;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								$table_main.='
										<table style="width:300px;padding-top:0px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:50px;font-weight:bold;font-style:italic;">Other:&nbsp;</td>
												<td style="width:250px;">';
													if($otherTroubles!=""){	$table_main.=$otherTroubles;}else{$table_main.="____";}
								$table_main.='	</td>
											</tr>
										</table>
								</td>
							</tr>';
						$table_main.='</table>
					</td>			
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" style="width:360px; font-size:13px;"><b>Allergies/Drug Reaction&nbsp;NKA:</b>';
								$table_main.=$allergiesValueNKA;
								$table_main.='&nbsp;<b>Allergies Reviewed:</b>';
								if($allergies_status_reviewed){$table_main.=$allergies_status_reviewed;}else{$table_main.="___";}
								$table_main.='</td>
							</tr>
							<tr>
								<td style="width:175px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;border-top:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
								<td style="width:150px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;border-top:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Reaction</td>
							</tr>';
								if($num>0){
									foreach($allergy1_rows as $fetchRows_health) {
									$table_main.='
										<tr>
											<td style="width:175px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($fetchRows_health['allergy_name']).'</td>
											<td style="width:150px;padding:5px;border-bottom:1px solid #C0C0C0;">'.htmlentities($fetchRows_health['reaction_name']).'</td>
										</tr>';
									}
								}else{
									$table_main.='
										<tr>
											<td style="width:175px;padding:3px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:150px;padding:3px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
										</tr>
										<tr>
											<td style="width:175px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:150px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
										</tr>';
								}
									$table_main.='
									<tr>
										<td colspan="2" style="width:350px;">
											<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
												<tr>
													<td style="width:215px;padding-top:10px;"><strong>Take&nbsp;Prescription&nbsp;Medications</strong></td>
													<td style="width:135px;padding-top:10px;"><strong>No&nbsp;Medication:</strong>';
														if($noMedicationStatus == "Yes"){$table_main.="Yes";}else{$table_main.="___";}
									$table_main.='					
													</td>
												</tr>
												<tr>
													<td colspan="2" style="width:350px;padding-top:10px;"><strong>Comments:</strong>';
														if($noMedicationStatus == "Yes" && trim($noMedicationComments)){$table_main.=$noMedicationComments;}else{$table_main.="__________";}
									$table_main.='					
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="width:350px;">
											<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
														<tr>
															<td style="width:125px;font-weight:bold;padding-top:5px;border-top:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
															<td style="width:100px;font-weight:bold;padding-top:5px;border-top:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Dosage</td>
															<td style="width:100px;font-weight:bold;padding-top:5px;border-top:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Sig</td>
														</tr>';
												if($num_medhealth>0){
													while($fetchRows_medhealth=imw_fetch_assoc($medresult_health)){
													$table_main.='
														<tr>
															<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($fetchRows_medhealth['prescription_medication_name']).'</td>
															<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($fetchRows_medhealth['prescription_medication_desc']).'</td>
															<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($fetchRows_medhealth['prescription_medication_sig']).'</td>
														</tr>';														
													}
												}else {
													for($q=1;$q<=3;$q++) {
														$table_main.='
														<tr>
															<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
															<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
															<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
														</tr>';
													}
												}
										$table_main.='		
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="width:350px;">
											<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
												<tr>
													<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding-top:5px;">Do You</td>
													<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center; padding-top:5px;">Yes/No</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Use a Wheel Chair, Walker or Cane';
													if($walker=="Yes" && trim($walkerDesc)){
													$table_main.='<br>
														<table style="width:300px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:230px;">'.stripslashes($walkerDesc).'</td>
															</tr>
														</table>';
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($walker!=""){$table_main.=$walker;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Wear Contact lenses';
													if($contactLenses=="Yes" && trim($contactLensesDesc)){
													$table_main.='<br>
														<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:250px;">'.stripslashes($contactLensesDesc).'</td>
															</tr>
														</table>';
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($contactLenses!=""){	$table_main.=$contactLenses;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Smoke';
													if($smoke=="Yes" && trim($smokeHowMuch)){
													$table_main.='<br>														
															<b><i>How much:</i></b>&nbsp;'.stripslashes($smokeHowMuch);
													}
													if($smoke=="Yes"){
													
													$table_main.='<br>														
															<b><i>Patient advised not to smoke 24 H prior to surgery:</i></b>&nbsp;';
															if($smokeAdvise=="Yes"){	$table_main.=$smokeAdvise;}else{$table_main.="____";}
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($smoke!=""){	$table_main.=$smoke;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Drink Alcohol';
													if($alchohol=="Yes" && trim($alchoholHowMuch)){
														$table_main.='<br><b><i>How much:</i></b>&nbsp;'.stripslashes($alchoholHowMuch);
													}
													if($alchohol=="Yes"){
													
													$table_main.='<br>														
															<b><i>Patient advised not to drink 24 H prior to surgery:</i></b>&nbsp;';
															if($alchoholAdvise=="Yes"){	$table_main.=$alchoholAdvise;}else{$table_main.="____";}
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($alchohol!=""){	$table_main.=$alchohol;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Have an automatic internal defibrillator';
													if($autoInternalDefibrillator=="Yes" && trim($autoInternalDefibrillatorDesc)){
													$table_main.='<br>
														<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:180px;">'.stripslashes($autoInternalDefibrillatorDesc).'</td>
															</tr>
														</table>';
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($autoInternalDefibrillator!=""){	$table_main.=$autoInternalDefibrillator;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Have any Metal Prosthetics';
													if($metalProsthetics=="Yes" && trim($notes)){
													$table_main.='<br>
														<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:180px;">'.stripslashes($notes).'</td>
															</tr>
														</table>';
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($metalProsthetics!=""){$table_main.=$metalProsthetics;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
											</table>
										   </td>
										</tr>';
						$table_main.='</table>
					</td>
				</tr>
			</table>';
		//adminHealthquestionare
	$selectAdminQuestionsQry="select * from healthquestioner";
	$selectAdminQuestions=imw_query($selectAdminQuestionsQry);
	$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
	$inc=0;
	while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
	{
	foreach($ResultselectAdminQuestions as $key=>$value){
		$question[$i][$key]=$value;
	}
	$inc++;	
	}
	
	//echo $question[2]['question'];
	//End adminHealthquestionare
	$getQuesQry=imw_query("select * from healthquestionadmin where confirmation_id='$pConfId'");
	$k=0;
	$QuesnumRows=imw_num_rows($getQuesQry);
	while($getQuesRes=imw_fetch_array($getQuesQry)){
	foreach($getQuesRes as $key=>$val){
	$quest[$k][$key]=$val;
	}
	$k++;
	}
	if($QuesnumRows>0){
		$t = 0;
		$table_main.='<table style="width:700px; font-size:13px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0"><tr>';
		for($k=0;$k<ceil($QuesnumRows/2);$k++)
		{
		$i = $k;
		$quest[$k]['adminQuestion']; 
		$questionid[]=$quest[$k]['adminQuestionStatus'];
		$tr=0;								
		$endTd = $endTd<= $QuesnumRows ? $t + 2 : $QuesnumRows;
			for($t=$t;$t<$endTd;$t++){
					$table_main.='<td style="width:300px;padding-left:5px; padding-top:5px;border-bottom:1px solid #C0C0C0;">'.$quest[$t]['adminQuestion'];
					if($quest[$t]['adminQuestionStatus']=="Yes" && trim($quest[$t]['adminQuestionDesc'])){
						$table_main.='<br>
							<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
									<td style="width:180px;">'.stripslashes($quest[$t]['adminQuestionDesc']).'</td>
								</tr>
							</table>';
					}
					$table_main.='</td>';
					if($t<$QuesnumRows){
						$table_main.='<td style="width:55px;text-align:center;border-right:1px solid #C0C0C0;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
							if($quest[$t]['adminQuestionStatus']){
								$table_main.=$quest[$t]['adminQuestionStatus'];	
							}else{
								$table_main.="___";		
							}
						$table_main.='</td>';
						$tr++;	
					}
				if($tr%2==0){$table_main.='</tr><tr>';}	
			}
		}
		$table_main.='</tr></table>';
	}		

//CODE RELATED TO NURSE SIGNATURE ON FILE
$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 

$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
$loggedInUserType = $ViewUserNameRow["user_type"];
$loggedInSignatureOfUser = $ViewUserNameRow["signature"];

//$S = "Yes";
//$NurseNameShow = $loggedInUserName;

if($signNurseId<>0 && $signNurseId<>"") {
	$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
	$signOnFileStatus = $signNurseStatus;	

}
//End CODE RELATED TO NURSE SIGNATURE ON FILE

//START CODE RELATED TO WITNESS SIGNATURE ON FILE
if($signWitness1Id<>0 && $signWitness1Id<>"") {
	$Witness1NameShow = $signWitness1LastName.", ".$signWitness1FirstName." ".$signWitness1MiddleName;
	$signOnFileWitness1Status = $signWitness1Status;	

}
//END CODE RELATED TO WITNESS SIGNATURE ON FILE

//image for signature


 $preid=$pConfId;
 $pertable = 'preophealthquestionnaire';
 $preidName = 'confirmation_id';
 $predocSign = 'patientSign';
 $predocSignwit = 'witnessSign';
$qry = "select patientSign from preophealthquestionnaire where confirmation_id = $pConfId";
$pixleResPat = imw_query($qry);
list($patientSign) = imw_fetch_array($pixleResPat);
require_once("imgGd.php");
drawOnImage($patientSign,$imgName,'patientSign.jpg');

$qry = "select witnessSign from preophealthquestionnaire where confirmation_id = $pConfId";
$pixleResWit = imw_query($qry);
list($witnessSign) = imw_fetch_array($pixleResWit);
require_once("imgGd.php");
drawOnImage($witnessSign,$imgName,'witnessSign.jpg');
//print_r(getAppletImage($preid,$pertable,$preidName,$predocSignwit,$signImage,$alt,"1234.jpg"));
/*******End of signature*****/

$table_main.='<table cellpadding="0" cellspacing="0" style="border:1px solid #C0C0C0;width:700px;padding:5px; font-size:14px;">';
				$table_main.='
					<tr>
						<td style="width:400px;"><b>Emergency Contact Person:</b>&nbsp;';
						if($emergencyContactPerson!=""){
							$table_main.=$emergencyContactPerson;
						}else{
							$table_main.="___________";
						}
								
					$table_main.='<br><b>Patient Signature:</b></td>
						<td style="width:270px;">';
							$table_main.='<b>Tel.</b>&nbsp;';
							if($emergencyContactPhone){
								$table_main.=$emergencyContactPhone;
							}else{
								$table_main.="___________";
							}
					$table_main.='<br><b>Witness Name:</b>&nbsp;';
					if($witnessname){
						$table_main.=$witnessname;
					}else{
						$table_main.="________";
					}
					$table_main.='</td>
					</tr>';
				$table_main.='
					<tr>
						<td style="width:450px;vertical-align:top;padding-top:5px;">';
						if($patient_sign_image_path){
							if(file_exists($patient_sign_image_path)){
								$table_main.='<img src="../'.$patient_sign_image_path.'" width="150" height="83">&nbsp;';
							}
						}else{
							$table_main.="________";
						}
						$table_main.='&nbsp;&nbsp;&nbsp;<b>Date:</b>&nbsp;';
						if($objManageData->changeDateMDY($dateQuestionnaire)!='00-00-0000'){
							$table_main.=$objManageData->changeDateMDY($dateQuestionnaire);
						}else{
							$table_main.="________";	
						}
						$table_main.='</td>';
						$table_main.='<td style="width:277px;vertical-align:top;padding-top:5px;">';
						
						
						if($Witness1NameShow!=""){ 
							$table_main.="<br><b>Witness:&nbsp;</b>".$Witness1NameShow;
							$table_main.="<br><b>Electronically Signed:&nbsp;</b>Yes";
							$table_main.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signWitness1DateTime);
						}else if(trim($witness_sign_image_path)!=""){
							$table_main.="<br><b>Witness Signature:&nbsp;</b>";
							$table_main.="<br><img alt='' src='../".$witness_sign_image_path."' style='width:150px; height:65px;'>";
						}else {
							$table_main.="<br><b>Witness:&nbsp;</b>________";
							$table_main.="<br><b>Electronically Signed:&nbsp;</b>________";
							$table_main.="<br><b>Signature Date:&nbsp;</b>________";
						}
						$table_main.='</td>
					</tr>';
							
				$table_main.='<tr >';
				if($NurseNameShow!=""){	
					$table_main.='<td valign="top"><b>Nurse:</b>&nbsp;'.$NurseNameShow.'</td>';
				}
				if($signOnFileStatus!=""){	
					$table_main.='<td valign="top"><b>Electronically Signed:</b>&nbsp;'.$signOnFileStatus.'</td>';
				}
				$table_main.='</tr>';
		$table_main.='</table>';
/*
$img_logo = showThumbImages('html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+8;
$tr_arrh=explode("<tr",$table_main);	
$rowCounth= count($tr_arrh);	


		$totalPage_Counth = count($tr_arrh);
		
		if($totalPage_Counth > 38){
			$totalPageCounth = ceil($totalPage_Counth / 38);
		}
		else{
			$totalPageCounth = 1;
		}
		$cur_pageh = 1;	
		$statementDatah = '';
	*/	
$table_printh=$table_main;
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut = fputs(fopen('new_html2pdf/pdffile.html','w+'),$table_printh);
fclose($fileOpen);
?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<?php 
if($preHealthQuestFormStatus=='completed' || $preHealthQuestFormStatus=='not completed') {
?>

<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
	</tr>
</table>


<form name="printFrm" action="new_html2pdf/createPdf.php?op=p" method="post">
</form> 
	
<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	
