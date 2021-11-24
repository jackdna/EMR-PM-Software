<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include("common_functions.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}	

include_once("new_header_print.php");
$lable="Pre-Op General Anesthesia Record";
$table_main="";	
		$Insur_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
		$Insur_patientConfirm_tblRes = imw_query($Insur_patientConfirm_tblQry) or die(imw_error());
		$Insur_patientConfirm_tblRow = imw_fetch_array($Insur_patientConfirm_tblRes);
		$Insur_patientConfirmDosTemp = $Insur_patientConfirm_tblRow["dos"];
		$finalizeStatus = $Insur_patientConfirm_tblRow["finalize_status"];
		
		$preopgenAnesFormStatus='';
		$ViewPreopgenAnesQry = "select * from `preopgenanesthesiarecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPreopgenAnesRes = imw_query($ViewPreopgenAnesQry) or die(imw_error()); 
		$ViewPreopgenAnesNumRow = imw_num_rows($ViewPreopgenAnesRes);
		$ViewPreopgenAnesRow = imw_fetch_array($ViewPreopgenAnesRes); 
		
		$preopgenAnesFormStatus = $ViewPreopgenAnesRow["form_status"];
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
		$whoUserTypeLabel = ($whoUserType == 'Anesthesiologist') ? 'Anesthesia&nbsp;Provider' : $whoUserType ;
		$createdByUserId = $ViewPreopgenAnesRow["createdByUserId"];
		$relivedNurseId = $ViewPreopgenAnesRow["relivedNurseId"];
		$form_status = $ViewPreopgenAnesRow["form_status"];
		$ascId = $ViewPreopgenAnesRow["ascId"];
		$confirmation_id = $ViewPreopgenAnesRow["confirmation_id"];
		$patient_id = $ViewPreopgenAnesRow["patient_id"];

		
//END VIEW RECORD FROM DATABASE
	
	?>
	<!--<img align="right"  src="http://'.$_SERVER['HTTP_HOST'].'/surgerycenter/images/'.$img.'" width="175" height="28">-->

<?php

$table_main.=$head_table."\n";	
$table_main.='<table style="width:700px;font-size:14px;border:1px solid #C0C0C0; margin-top:10px;" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" style="text-align:center;font-size:15px;font-weight:bold;padding:10px 0 5px 0;text-decoration:underline;">Pre-Op General Anesthesia Record</td>
				</tr>
				<tr>
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding-top:5px;padding-left:5px;">Medical History</td>
								<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center;padding-top:5px;">Yes/No</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Heart Problem</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;vertical-align:top;text-align:center; ">';
								if($HeartProblem){$table_main.=$HeartProblem;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;padding-left:5px;vertical-align:top;">High Blood Pressure</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($HighBloodPressure){$table_main.=$HighBloodPressure;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Stroke</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($Stroke){$table_main.=$Stroke;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Diabetes</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($Diabetes){$table_main.=$Diabetes;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
						</table>
					</td>

					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding-top:5px;">&nbsp;</td>
								<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center; padding-top:5px;">Yes/No</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Bleeding Problems</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($HeartProblem){$table_main.=$HeartProblem;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Asthma Lung Disease</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($HeartProblem){$table_main.=$HeartProblem;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								
								<td colspan="2" style="width:350px;height:43px;border-bottom:1px solid #C0C0C0;padding-top:5px;padding-left:5px;vertical-align:top; "><span style="padding-right:5px;font-weight:bold;">Other:&nbsp;</span>';
								if(trim($medicalHistoryOther)){$table_main.=stripslashes($medicalHistoryOther);}else{$table_main.="________________________________________";}
								
								$table_main.='
								</td>
							</tr>
						</table>
					</td>					
				</tr>
				<tr>
					<td style="width:350px; vertical-align:top; ">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" style="width:350px; background:#C0C0C0; height:20px;padding-left:5px;"><b>Allergies to Medications</b>
								
								</td>
							</tr>
							<tr>
								<td style="width:175px;padding:5px;">Name</td>
								<td style="width:150px;padding:5px;">Reaction</td>
							</tr>';
							
							//GETTING ALLERGIES REACTIONS TO DISPLAY
							$allergiesReactionDetails = $objManageData->getArrayRecords('patient_allergies_tbl', 'patient_confirmation_id', $_REQUEST["pConfId"]);
							if(@count($allergiesReactionDetails)>0){
								foreach($allergiesReactionDetails as $allergyName){
									++$seq1;
									$pre_op_allergy_id[$seq1] = $allergyName->pre_op_allergy_id;
									$allergy[$seq1] = $allergyName->allergy_name;
									$reaction[$seq1] = $allergyName->reaction_name;		
								}
							}
							$table_main.='
							<tr>
								<td colspan="2" style="width:350px;verticle-align:top;padding-bottom:5px;">
									<table style=" width:350px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
								';
							//GETTING ALLERGIES REACTIONS TO DISPLAY
							if(@count($allergiesReactionDetails)>0){
								for($i_healthquest_allerg=1;$i_healthquest_allerg<=count($allergy);$i_healthquest_allerg++){
									if($allergy[$i_healthquest_allerg]){ 
										$table_main.='
												<tr> 
													<td style="width:175px; padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.stripslashes(htmlentities($allergy[$i_healthquest_allerg])).'</td>
													<td style="width:150px; padding:5px; border-bottom:1px solid #C0C0C0;">'.stripslashes(htmlentities($reaction[$i_healthquest_allerg])).'</td>
												</tr>';
									}
								}
								
							}else{
								$table_main.='
									<tr>
										<td style="width:175px;border-bottom:1px solid #C0C0C0;height:15px;">&nbsp;</td>
										<td style="width:150px;border-bottom:1px solid #C0C0C0;height:15px;">&nbsp;</td>
									</tr>
									<tr>
										<td style="width:175px;border-bottom:1px solid #C0C0C0;height:15px;">&nbsp;</td>
										<td style="width:150px;border-bottom:1px solid #C0C0C0;height:15px;">&nbsp;</td>
									</tr>';
							}
							$table_main.='
									</table>
								</td>
							</tr>								
						</table>
					</td>';
					$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id=$pConfId ORDER BY prescription_medication_name");
					$medsRes=imw_query($medsQry);
					$medsnum=@imw_num_rows($medsRes);
					$table_main.='
					<td style="width:350px; vertical-align:top; ">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="3" style="width:350px;font-weight:bold; background:#C0C0C0; height:20px;padding-left:5px;">Current Medications</td>
							</tr>
							<tr>
								<td style="width:125px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Name</td>
								<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Dosage</td>
								<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Sig</td>
							</tr>';
					if($medsnum>0){
						while($rowAllergies=imw_fetch_assoc($medsRes)){
						$table_main.='
							<tr>
								<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($rowAllergies['prescription_medication_name']).'</td>
								<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($rowAllergies['prescription_medication_desc']).'</td>
								<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">'.htmlentities($rowAllergies['prescription_medication_sig']).'</td>
							</tr>';														
						}
					}else {
						for($q=1;$q<=3;$q++) {
							$table_main.='
							<tr>
								<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
								<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
								<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
							</tr>';
						}
							
					}
			$table_main.='		
						</table>
					</td>
				</tr>
				<tr>
					<td style="width:350px; height:20px; text-align:center;font-weight:bold;background:#C0C0C0;">Problem w/Previous Anesthesia</td>
					<td style="width:350px; height:20px; text-align:center;font-weight:bold;background:#C0C0C0;">Family History Of Anesthesia Problems</td>
				</tr>
				<tr>
					<td style="width:350px;padding-bottom:5px;border-right:1px solid #C0C0C0;">
						<table style="width:350px;font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td rowspan="2" style="width:75px;height:40px;font-size:14px;font-weight:bold;text-align:center;">';
								if($probPrevAnesthesia=="Yes"){
									$table_main.="Yes";
								}else if($probPrevAnesthesia=="No"){
									$table_main.="None";	
								}else{
									$table_main.="None/Yes<br>____";		
								}
								$table_main.='
								</td>
								<td style="width:75px;height:20px;">PNV:&nbsp;';
								if($pnv=="Yes" && $probPrevAnesthesia=="Yes"){
									$table_main.="<b>Yes</b>";
								}else{
									$table_main.="___";
								}
								$table_main.='
								</td>
								<td rowspan="2" style="width:197px;height:40px; padding-left:5px;vertical-align:top">';
								if(trim($probPrevAnesthesiaDesc)!="" && $probPrevAnesthesia=="Yes"){
									$table_main.=$probPrevAnesthesiaDesc;
								}
								$table_main.='</td>
							</tr>
							<tr>
								
								<td style="width:75px;height:20px;">DC:&nbsp;&nbsp;&nbsp;';
								if($dc=="Yes"){
									$table_main.="<b>Yes</b>";
								}else{
									$table_main.="___";
								}
							
							$table_main.='
								</td>
							</tr>
						</table>
					</td>
					<td style="width:350px;padding-left:5px;border-left:1px solid #C0C0C0;padding-bottom:5px;">
						<table style="width:350px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="height:40px; width:75px;vertical-align:middle;text-align:center;">';
								if($familyHistoryAnesthesiaProblems=="Yes"){
									$table_main.="<b>None</b>";	
								}else if($familyHistoryAnesthesiaProblems=="No"){
									$table_main.="<b>Yes</b>";	
								}else{
									$table_main.="<b>Yes/None</b>:&nbsp;___";
								}
								$table_main.='</td>
								<td style="height:40px; width:275px;vertical-align:top">';
								if(trim($familyHistoryAnesthesiaProblemsDesc)!=""){
									$table_main.=$familyHistoryAnesthesiaProblemsDesc;
								}
								$table_main.='</td>								
							</tr>
						</table>';
					$table_main.='</td>
				</tr>
				<tr>
					<td colspan="2" style="width:700px; height:20px; text-align:center;font-weight:bold;border-top:1px solid #C0C0C0;background:#C0C0C0;">Do You</td>
				</tr>
				<tr>
					<td colspan="2" style="width:700px; height:20px;padding-left:5px;padding-top:8px;"><b>Smoke:&nbsp;</b>';
					if($smoke=="No"){
						$table_main.="No";	
					}else{
						if($smoke=="" && $smokeCigarettes!=""){
							$table_main.="Yes";
						}else{
							$table_main.="Yes/No:&nbsp;__";
						}
						$table_main.="&nbsp;&nbsp;<b>Cigarettes:&nbsp;</b>".$smoke;
						if($smokeCigarettes=="No"){
							$table_main.="Yes";	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>Cigars:&nbsp;</b>";
						if($smokeCigars=="No"){
							$table_main.="Yes";	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>Pipe:&nbsp;</b>";
						if($smokePipe=="No"){
							$table_main.="Yes";	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>Packs/Day:&nbsp;</b>";
						if($smokePacks!=""){
							$table_main.=$smokePacks;	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>No. of Years:&nbsp;</b>";
						if($smokeYears!=""){
							$table_main.=$smokeYears;	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>If Stopped when:</b>";
						if($smokeStopDate!="" && $smokeStopDate!="--"){
							$table_main.=$smokeStopDate;	
						}else{
							$table_main.="___";		
						}
					}
					$table_main.='</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:5px;padding-left:5px; height:20px; border-top:1px solid #C0C0C0;"><b>Alcoho:&nbsp;</b>';
					if(trim($alcohol)){
						$table_main.=$alcohol;
					}else{
						$table_main.="___";	
					}
					if(trim($alcohol)=="Yes"){
						$table_main.="<b>&nbsp;&nbsp;Drinks/Week:&nbsp;</b>";
						if(trim($alcoholWeeksList)){
							$table_main.=$alcoholWeeksList;
						}else{
							$table_main.="___";	
						}
					}
					$table_main.='</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:5px;padding-left:5px; height:20px;width:700px; border-top:1px solid #C0C0C0;">
						<table style="width:700px;font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:60px;vertical-align:top;"><b>Dental:</b></td>
								<td style="width:550px;line-height:1.3;"><b>Normal:&nbsp;</b>';
									if($permanentBridge=="Yes"){
										$table_main.=$permanentBridge;
									}else{
										$table_main.="___";	
									}
									$table_main.="&nbsp;&nbsp;<b>Dentures:&nbsp;</b>";
									if($dentures=="Yes"){
										$table_main.=$dentures;	
									}else{
										$table_main.="___";		
									}
									$table_main.="&nbsp;&nbsp;<b>Capped Teeth:&nbsp;</b>";
									if($cappedTeeth=="Yes"){
										$table_main.=$cappedTeeth;	
									}else{
										$table_main.="___";		
									}
									$table_main.="&nbsp;&nbsp;<b>Loose or Broken Teeth:&nbsp;</b>";
									if($looseBrokenTeeth=="Yes"){
										$table_main.=$cappedTeeth;	
									}else{
										$table_main.="___";		
									}
									$table_main.="<br><span style='padding-left:35px;'><b>Periodontal Disease:&nbsp;</b>";
									if($looseBrokenTeeth=="Yes"){
										$table_main.=$cappedTeeth;	
									}else{
										$table_main.="___";		
									}
									$table_main.="&nbsp;&nbsp;<b>Other:&nbsp;</b>";
									if(trim($otherDentalProblems)){
										$table_main.=$otherDentalProblems;	
									}else{
										$table_main.="______";		
									}
									$table_main.='</span>
								</td>
							</tr>		
						</table>
					</td>		
				</tr>
				<tr>
					<td colspan="2" style="width:700px; height:20px; padding-top:5px;border-top:1px solid #C0C0C0;">
						<b>Comments:&nbsp;</b>';
					if(trim($preOpComplications)){
						$table_main.=stripslashes($preOpComplications);
					}
					$table_main.='</td>
				</tr>';
				
					$preOpGenUserNameQry = "select * from users where user_type = '$whoUserType' and usersId='$createdByUserId' ORDER BY lname";
					$preOpGenUserNameRes = imw_query($preOpGenUserNameQry) or die(imw_error());
					$preOpGenUserNumRow = imw_num_rows($preOpGenUserNameRes);
					$preOpGenUserRow = imw_fetch_array($preOpGenUserNameRes);
					$preOpGenUserId = $preOpGenUserRow["usersId"];
					$preOpGenUserName = $preOpGenUserRow["lname"].", ".$preOpGenUserRow["fname"]." ".$preOpGenUserRow["mname"];
					
					$relivedNurseQry = "select * from users where user_type='Nurse' AND usersId='$relivedNurseId'  ORDER BY lname";
					$relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
					$relivedNurseRow=imw_fetch_array($relivedNurseRes);
					$relivedSelectNurseID = $relivedNurseRow["usersId"];
					$relivedNurseName = $relivedNurseRow["lname"].", ".$relivedNurseRow["fname"]." ".$relivedNurseRow["mname"];
				$table_main.='
				<tr>
					<td colspan="2" style="width:700px;border-top:1px solid #C0C0C0;padding-top:10px;">
						<table style="width:700px; font-size:14px;" cellpadding="0">
							<tr>
								<td style="width:180px;"><b>Who:&nbsp;</b>';
									if($whoUserType){
										$table_main.=$whoUserTypeLabel;		
									}else{
										$table_main.="_________";			
									}
						$table_main.='
								</td>
								<td style="width:280px;"><b>Created By:&nbsp;</b>'; 
									if($preOpGenUserName){
										$table_main.=$preOpGenUserName;
									}else{
										$table_main.="_________";
									}
						$table_main.='
								</td>
								<td style="width:240px;"><b>Relief Nurse:&nbsp;</b>'; 
									if($relivedNurseName){
										$table_main.=$relivedNurseName;
									}else{
										$table_main.="_________";
									}
						$table_main.='
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>';

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut = fputs($fileOpen,$table_main);
fclose($fileOpen);
$URL='http://'.$_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';
/*echo"<script>window.open('testPdf.html?pConfId=','','')</script>";*/
?>
<body>
    <form name="printFrm" action="new_html2pdf/createPdf.php?op=p" method="post">
    </form>
    <script language="javascript">
        function submitfn(){
            document.printFrm.submit();
        }
    </script>
    
    <?php 
    if($preopgenAnesFormStatus=='completed' || $preopgenAnesFormStatus=='not completed') {
    ?>
    <table bgcolor="#FFFFFF"  style="font:verdana; font-size:14;" width="100%" height="100%">
        <tr>
            <td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
        </tr>
    </table>
    <script type="text/javascript">
    	submitfn();
    </script>
    <?php
    }else{
        echo "<center>Please verify/save this form before print</center>";
    }
    ?>	
</body>