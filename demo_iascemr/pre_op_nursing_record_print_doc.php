<?php
		
		// GETTING CONFIRMATION DETAILS
	$PatConfPreOpNursing	=	$objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatusPreOpNursing = $PatConfPreOpNursing->finalize_status;	
	$allergiesNKDA_PatConfPreOpNursing = $PatConfPreOpNursing->allergiesNKDA_status;
	$confirmSitePreOpNursing	=	$PatConfPreOpNursing->site;
// GETTING CONFIRMATION DETAILS


	if($confirmSitePreOpNursing == 1) {
		$confirmSitePreOpNursing = "Left Eye";  //OD
	}else if($confirmSitePreOpNursing == 2) {
		$confirmSitePreOpNursing = "Right Eye";  //OS
	}else if($confirmSitePreOpNursing == 3) {
		$confirmSitePreOpNursing = "Both Eye";  //OU
	}else{
		$confirmSitePreOpNursing = "Operative Eye";  //OU
	}
			
		//VIEW RECORD FROM DATABASE
		$ViewPreopnursingQry = "select * from `preopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPreopnursingRes = imw_query($ViewPreopnursingQry) or die(imw_error()); 
		$ViewPreopnursingNumRow = @imw_num_rows($ViewPreopnursingRes);
		$ViewPreopnursingRow = @imw_fetch_array($ViewPreopnursingRes); 
		
		$preOpNursingId	=	$ViewPreopnursingRow['preOpNursingRecordId'];
		$version_num 			=	$ViewPreopnursingRow['version_num'];
		$versionDateTime	=	$ViewPreopnursingRow['version_date_time'];
		$comments = $ViewPreopnursingRow['comments'];
		$chbx_saline_lockStart = $ViewPreopnursingRow['chbx_saline_lockStart'];
		$chbx_saline_lock = $ViewPreopnursingRow['chbx_saline_lock'];
		$ivSelection = $ViewPreopnursingRow['ivSelection'];
		$ivSelectionOther = $ViewPreopnursingRow['ivSelectionOther'];
		$ivSelectionSide =	$ViewPreopnursingRow['ivSelectionSide'];
		$chbx_KVO	=	$ViewPreopnursingRow['chbx_KVO'];
		$chbx_rate = $ViewPreopnursingRow['chbx_rate'];
		$txtbox_rate = $ViewPreopnursingRow['txtbox_rate'];
		$chbx_flu =	$ViewPreopnursingRow['chbx_flu'];
		$txtbox_flu	= $ViewPreopnursingRow['txtbox_flu'];
		$gauge = $ViewPreopnursingRow['gauge'];
		$gauge_other	=	$ViewPreopnursingRow['gauge_other'];
		$gauge	=	($gauge == 'other')	?	$gauge_other	:	$gauge;
		$prefilMedicationStatus	=	$ViewPreopnursingRow['prefilMedicationStatus'];
		$txtbox_other_new = stripslashes($ViewPreopnursingRow['txtbox_other_new']);
		
		$preopNurseTime = $ViewPreopnursingRow["preopNurseTime"];
		$allergies_status_reviewed = $ViewPreopnursingRow["allergies_status_reviewed"];
		$foodDrinkToday = $ViewPreopnursingRow["foodDrinkToday"];
		$listFoodTake = stripslashes($ViewPreopnursingRow["listFoodTake"]);
		$labTest = $ViewPreopnursingRow["labTest"];
		$ekg = $ViewPreopnursingRow["ekg"];
		$consentSign = $ViewPreopnursingRow["consentSign"];
		$hp = $ViewPreopnursingRow["hp"];
		$admitted2Hospital = $ViewPreopnursingRow["admitted2Hospital"];
		$NA = $ViewPreopnursingRow["NA"];
		$bsValue = $ViewPreopnursingRow["bsValue"];
		$reason = stripslashes($ViewPreopnursingRow["reason"]);
		$healthQuestionnaire = $ViewPreopnursingRow["healthQuestionnaire"];
		$standingOrders = $ViewPreopnursingRow["standingOrders"];
		$patVoided = $ViewPreopnursingRow["patVoided"];
		
		$hearingAids = $ViewPreopnursingRow["hearingAids"];
		$hearingAidsRemoved = $ViewPreopnursingRow["hearingAidsRemoved"];
		$denture = $ViewPreopnursingRow["denture"];
		$dentureRemoved = $ViewPreopnursingRow["dentureRemoved"];
		
		$patientHeight = $ViewPreopnursingRow["patientHeight"];
		$patientWeight = $ViewPreopnursingRow["patientWeight"];
		if($patientWeight<>""){
			$height= @explode("'",$patientHeight);
			$feet=$height[0];
			$inch=$height[1];
		}
		$anyPain = $ViewPreopnursingRow["anyPain"];
		$painLevel = $ViewPreopnursingRow["painLevel"];
		$painLocation = $ViewPreopnursingRow["painLocation"];
		$doctorNotified = $ViewPreopnursingRow["doctorNotified"];
		$weight =@explode ("lb",$patientWeight);
		$weightlbs=$weight[0];
		$patientBMI	=	$ViewPreopnursingRow["patientBmi"];
		$vitalSignBp = $ViewPreopnursingRow["vitalSignBp"];
		$vitalSignP = $ViewPreopnursingRow["vitalSignP"];
		$vitalSignR = $ViewPreopnursingRow["vitalSignR"];
		$vitalSignTemp = $ViewPreopnursingRow["vitalSignTemp"];
		$preOpComments = $ViewPreopnursingRow["preOpComments"];
		$relivedNurseId = $ViewPreopnursingRow["relivedNurseId"];
		//$form_status =  $ViewPreopnursingRow["form_status"];
		$preNurseFormStatus = $ViewPreopnursingRow["form_status"];
		
		$signNurseId =  $ViewPreopnursingRow["signNurseId"];
		$signNurseFirstName =  $ViewPreopnursingRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewPreopnursingRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewPreopnursingRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewPreopnursingRow["signNurseStatus"];
		$signNurseDateTime =  $ViewPreopnursingRow["signNurseDateTime"];
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
			
		// Get Pre Op Physician Order Chart ID to get patient pre op medications
		$preOpPhysicianOrderRow	=	$objManageData->getExtractRecord('preopphysicianorders','patient_confirmation_id',$pConfId,'preOpPhysicianOrdersId');
		$preOpPhysicianOrderID	=	$preOpPhysicianOrderRow	['preOpPhysicianOrdersId'];
	
		$preOpPatientDetails	=	$objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $pConfId,'patientPreOpMediId','ASC'," And preOpPhyOrderId = '".$preOpPhysicianOrderID."' And sourcePage = '0' ");
		
		// End Get Pre Op Physician Order Chart ID to get patient pre op medications
		
		
		$preOpPhyQry = "Select version_num, form_status from preopphysicianorders Where patient_confirmation_id = ".$_REQUEST['pConfId']." ";
		$preOpPhySql = imw_query($preOpPhyQry);
		$preOpPhyRow = imw_fetch_object($preOpPhySql);
		
		$preOpPhyVersionNum = $preOpPhyRow->version_num;
		$preOpPhyFormStatus = $preOpPhyRow->form_status;  
		
		if(!($preOpPhyVersionNum) && ($preOpPhyFormStatus == 'completed' || $preOpPhyFormStatus == 'not completed')) { $preOpPhyVersionNum	=	1; }
		else if(!($preOpPhyVersionNum) && $preOpPhyFormStatus <> 'completed' && $preOpPhyFormStatus <> 'not completed') { $preOpPhyVersionNum	=	2; }
		
		
		//END VIEW RECORD FROM DATABASE	
					
		if($preNurseFormStatus=='completed' || $preNurseFormStatus=='not completed')
		{
			
		$table_main=$head_table."\n";	

		$allergy1 = "Select * from patient_allergies_tbl where patient_confirmation_id=$pConfId";
		$result = imw_query($allergy1);
		$num = @imw_num_rows($result);	
		$table_main.='<table cellpadding="0" cellspacing="0" style="width:700px;margin-top:5px; border:1px solid #C0C0C0; font-size:14px;">
				<tr>
					<td colspan="2" style="width:700px;font-size:15px; padding:10px 0 5px 0;text-decoration:underline; text-align:center; font-weight:bold;border-bottom:1px solid #C0C0C0;">Pre-Op Nursing Record</td>
				</tr>';
				
		
		
		// Row to print Allergies Medications	
		$table_main.='
				<tr>
					<td style="width:350px; vertical-align:top; ">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" style="width:350px;font-size:13px;"><b>Allergies/Drug Reaction&nbsp;NKA:</b>';
								if($allergiesNKDA_PatConfPreOpNursing){$table_main.="&nbsp;".$allergiesNKDA_PatConfPreOpNursing;}else{$table_main.="__";}
								$table_main.='<b>&nbsp;Allergies Reviewed:</b>';
								if($allergies_status_reviewed){$table_main.=$allergies_status_reviewed;}else{$table_main.="__";}
		$table_main.='			</td>
							</tr>	
							<tr>
								<td style="width:175px; height:20px;background:#C0C0C0;font-weight:bold;" valign="middle">Name</td>
								<td style="width:150px; height:20px;background:#C0C0C0;font-weight:bold;" valign="middle">Reaction</td>
							</tr>';
							//GETTING ALLERGIES REACTIONS TO DISPLAY
							$allergiesReactionDetails = $objManageData->getArrayRecords('patient_allergies_tbl', 'patient_confirmation_id', $pConfId);
							if(@count($allergiesReactionDetails)>0){
								foreach($allergiesReactionDetails as $allergyName){
									++$seq1;
									$pre_op_allergy_id[$seq1] = $allergyName->pre_op_allergy_id;
									$allergy[$seq1] = htmlentities($allergyName->allergy_name);
									$reaction[$seq1] = htmlentities($allergyName->reaction_name);		
								}
							}
							$table_main.='
								<tr>
									<td colspan="2" style="width:350px;verticle-align:top;">
										<table style=" width:350px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							';
							//GETTING ALLERGIES REACTIONS TO DISPLAY
							if($num>0){
								for($i_healthquest_allerg=1;$i_healthquest_allerg<=count($allergy);$i_healthquest_allerg++){
									if($allergy[$i_healthquest_allerg]){ 
										$table_main.='
												<tr> 
													<td style="width:175px; padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.stripslashes($allergy[$i_healthquest_allerg]).'</td>
													<td style="width:150px; padding:5px; border-bottom:1px solid #C0C0C0;">'.stripslashes($reaction[$i_healthquest_allerg]).'</td>
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
						
						$table_main.='</table>
									</td>
									
								</tr>
							</table>
						</td>
						
						<td style="width:350px; vertical-align:top;">
							
							<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
										<tr>
											<td colspan="3" style="width:350px;font-size:13px; vertical-align:top;"><b>Meds Taken Today</b>&nbsp;</td>
										</tr>
										<tr>
											<td style="width:125px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;background:#C0C0C0;">Name</td>
											<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;background:#C0C0C0;">Dosage</td>
											<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;background:#C0C0C0;">Sig</td>
										</tr>';
								$getMedicationDetails = $objManageData->getArrayRecords('patient_prescription_medication_tbl', 'confirmation_id', $pConfId,'prescription_medication_name','ASC');
								if(@count($getMedicationDetails)>0){
									foreach($getMedicationDetails as $medicationName){
										++$med_seq;
										$medication_id[$med_seq] = $medicationName->prescription_medication_id;
										$medication_name[$med_seq] = htmlentities($medicationName->prescription_medication_name);
										$medicationDetails[$med_seq] = htmlentities($medicationName->prescription_medication_desc);
										$medicationSig[$med_seq] = htmlentities($medicationName->prescription_medication_sig);
									}
								}
								
								if($getMedicationDetails) {
									for($i_healthquest_med=1;$i_healthquest_med<=count($medication_name);$i_healthquest_med++)
									{ 
										if($medication_name[$i_healthquest_med])
										{
											$table_main.='
											<tr>
												<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.stripslashes($medication_name[$i_healthquest_med]).'</td>
												<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.stripslashes($medicationDetails[$i_healthquest_med]).'</td>
												<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">'.stripslashes($medicationSig[$i_healthquest_med]).'</td>
											</tr>';
										}
									}
									
								}else {
									$table_main.='
										<tr>
											<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
										</tr>
										<tr>
											<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
										</tr>';
										
								}
						$table_main.='		
							</table>
						</td>';								
				$table_main.='	
					</tr>';
					// End Row to print Allergies Medications
		
		
		// Start Printing Pre op medication prefilled/Saved in Pre Op Physician Chart
		// and comments, Saline Lock/Gauge Fields
		
		if( ($version_num > 1 && $preOpPhyVersionNum <> 1) || $preOpPhyVersionNum > 1 )
		{		
				$table_main.='
						<tr><td colspan="2">
							<table style="width:700px; font-size:14px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:150px; text-align:center;height:20px;background:#C0C0C0;font-weight:bold;">Pre Op Orders</td>
									<td style="width:435px; text-align:center;height:20px; border-top:1px solid #C0C0C0;">On arrival the following drops will be given to the '.$confirmSitePreOpNursing.'</td>
									<td style="width:150px; text-align:center;height:20px;background:#C0C0C0;">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3" style="padding-top:10px; width:700px;text-align:center;font-weight:bold;border-top:1px solid #C0C0C0;">List of Pre-Op Medication Orders</td>
								</tr>
								<tr>
									<td colspan="3" style="width:600px;  padding-bottom:15px;">
										<table style="width:600px;font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:230px;height:23px;padding-left:10px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Medication</td>
												<td style="width:100px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Strength</td>
												<td style="width:100px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Directions</td>
												<td style="width:230px;height:23px;padding-left:10px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Time</td>
											</tr>';
											if(count($preOpPatientDetails)>0)
											{
												foreach($preOpPatientDetails as $detailsOfMedication)
												{
													$parientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
													$preDefined = $detailsOfMedication->medicationName;
													$strength = $detailsOfMedication->strength;
													$directions = $detailsOfMedication->direction;
													$timemeds[0] = $objManageData->getTmFormat($detailsOfMedication->timemeds);
										
													$timemeds[1] = $objManageData->getTmFormat($detailsOfMedication->timemeds1);
													$timemeds[2] = $objManageData->getTmFormat($detailsOfMedication->timemeds2);
													$timemeds[3] = $objManageData->getTmFormat($detailsOfMedication->timemeds3);
													$timemeds[4] = $objManageData->getTmFormat($detailsOfMedication->timemeds4);
													$timemeds[5] = $objManageData->getTmFormat($detailsOfMedication->timemeds5);
													$timemeds[6] = $objManageData->getTmFormat($detailsOfMedication->timemeds6);
													$timemeds[7] = $objManageData->getTmFormat($detailsOfMedication->timemeds7);
													$timemeds[8]=  $objManageData->getTmFormat($detailsOfMedication->timemeds8);
													$timemeds[9] = $objManageData->getTmFormat($detailsOfMedication->timemeds9);
								
													++$k;
													
													$disptr	=	($k==1)	?	'block'	:	'none';
									
													$dir  = explode('X',strtoupper($directions));
													$freq = substr(trim($dir[1]),0,1);
													$freq = $freq > 6 ? 6 : $freq;
													$minsDir = explode('Q',strtoupper($dir[1]));
													if(count($minsDir)<=1) $freq = '';
													$min=substr(trim($minsDir[1]),0,-3);
									
										
													$table_main.='<tr>
										<td style="padding:5px; width:230px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($preDefined).'</td>
										<td style="padding:5px;width:100px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($strength).'</td>
										<td style="padding:5px;width:100px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($directions).'</td>';
											 
									$table_main.='<td style="padding-left:5px;width:230px; border-bottom:1px solid #C0C0C0;vertical-align:top;">';		
									for($t=0;$t<=9;$t++)	
									{
										if($timemeds[$t]!=''){
											$table_main.=$timemeds[$t]."&nbsp;&nbsp;";
										}
										if($t==2){
											$table_main.="<br>";
										}
									}
									$table_main.='</td>
											</tr>'; 
												}
											}else{
											$table_main.='
											<tr>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											</tr>
											<tr>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											</tr>
											<tr>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
												<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											</tr>
											
									';
		}
		// End Printing Pre op medication prefilled/Saved in Pre Op Physician Chart
		
		$table_main.='
								</table>
							</td>	
						</tr>
						
						<tr>
							<td colspan="3" style="padding-top:5px;padding-bottom:5px;border-top:1px solid #C0C0C0;">
								<table style="width:600px; font-size:14px;" cellpadding="0" cellspacing="0"><tr>
									<td style="width:70px;font-weight:bold;">Comments:&nbsp;</td>
									<td style="width:630px;">';
									if(trim($comments)){
										$table_main.=stripslashes($comments);
									}else{
										$table_main.="_______________________";	
									}
									$table_main.='
									</td></tr>
								</table>
							</td>
						</tr>
						
						<tr>
							<td colspan="3" style="padding-top:5px;border-top:1px solid #C0C0C0; border-bottom:1px solid #C0C0C0;padding-bottom:5px;"><b>Start Saline Lock:</b>';
								$table_main	.=	($chbx_saline_lockStart!='') ? 'Yes' : '___';	
								$table_main	.=	'&nbsp;&nbsp;<b>IV:</b>';
								$table_main	.=	($chbx_saline_lock!='')	?	'Yes&nbsp;'	:	'___&nbsp;';	
								
								if($ivSelection!='' && $ivSelection!='other'){
									$table_main.="&nbsp;&nbsp;<b>".ucwords($ivSelection)."&nbsp;</b>".ucwords($ivSelectionSide);
								}
								if($ivSelection!='' && $ivSelection!='other' && ($chbx_saline_lock!='' || $chbx_saline_lockStart!='') ){
									$gauge_val = ($gauge ? $gauge : '___');
									$table_main.="&nbsp;&nbsp;<b>Gauge&nbsp;</b>".ucwords($gauge_val);
									
									$txtbox_other_new_val = ($txtbox_other_new ? $txtbox_other_new : '___');
									$table_main.="&nbsp;&nbsp;".ucwords($txtbox_other_new_val);
								}
								if($ivSelection!='' && $chbx_saline_lock!='' && $ivSelection!='other'){
									$table_main	.=	"&nbsp;&nbsp;<b>KVO:</b>";
									$table_main	.=	($chbx_KVO!='')	?	'Yes&nbsp;'	:	'___&nbsp;';	
									$table_main	.=	"&nbsp;<b>Rate:</b>";
									$table_main	.=	($chbx_rate!='')	?	'Yes&nbsp;'	:	'';	
									$table_main	.=	($txtbox_rate!='')	?	$txtbox_rate	:	'&nbsp;___&nbsp;';	
									$table_main	.=	'/hr&nbsp;&nbsp;';
									$table_main	.=	"&nbsp;<b>Flu:</b>";
									$table_main	.=	($chbx_flu!='')	?	'Yes&nbsp;'	:	'___&nbsp;';	
									$table_main	.=	($txtbox_flu!='')	?	$txtbox_flu	:	'';	
								}
								if($ivSelection=='other'){	 		
									$table_main	.=	'&nbsp;<b>Other:</b>&nbsp;'.stripslashes($ivSelectionOther);
								}
								$table_main	.=	'
							</td>
						</tr>
						
					</table>
				</td></tr>
				';			
		}
				
											
		$table_main.='
					<tr>
						<td style="width:350px;border:1px solid #C0C0C0; verticle-align:top;padding-top:5px;">
							<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:300px;height:20px; border-bottom:1px solid #C0C0C0;">
										Arrival Time:&nbsp;';
										if($preopNurseTime){$table_main.=$objManageData->getTmFormat($preopNurseTime);}else{$table_main.="_____";}
		$table_main.='</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
								</tr>
								
								<tr>
									<td style="width:300px;height:15px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Food or Drink Today</td>
									<td style="width:50px;height:15px;padding-top:5px;text-align:center;border-bottom:1px solid #C0C0C0;">';
									if($foodDrinkToday){$table_main.=$foodDrinkToday;}else{$table_main.="___";}
									$table_main.='</td>
								</tr>
								<tr>
									<td colspan="2" style="width:350px; padding-top:5px;border-bottom:1px solid #C0C0C0;">
										<table style="width:300px; font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:115px;font-weight:bold;  padding-top:5px;vertical-align:top;">List Food Taken:</td>
												<td style="width:243px; padding-top:5px;">';
									if($listFoodTake!="" && $foodDrinkToday=="Yes"){$table_main.=str_replace(',',',&nbsp;',$listFoodTake);}else{$table_main.="____________________";}
									$table_main.='</td>
											</tr>
										</table>
									</td>
								</tr>	
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px; vertical-align:top;">Lab Test</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
									if($labTest){$table_main.=$labTest;}else{$table_main.="___";}
									
									$table_main.='
									</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;">EKG</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;text-align:center; ">';
									if($ekg!=""){$table_main.=$ekg;}else{$table_main.="___";}
									
									$table_main.='
									</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;">Consent Signed</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px; text-align:center; ">';
									if($consentSign!=""){$table_main.=$consentSign;}else{$table_main.="___";}
									
									$table_main.='
									</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;">Admitted To Hospital in Past 30 Days';
									if($admitted2Hospital=="Yes"){$table_main.="<br>Reason:&nbsp;".$reason;}
									$table_main.='</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;text-align:center; ">';
									if($admitted2Hospital!=""){$table_main.=$admitted2Hospital;}else{$table_main.="___";}
									
									$table_main.='
									</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;padding-top:5px;">Blood Sugar:&nbsp;';
									if($NA!='' || $bsValue!=''){
										if($NA=='1') { $table_main.='N/A'; }
										if($bsValue!=""){$table_main.=$bsValue;	}else{$table_main.="&nbsp;___";}
									}
									if($version_num > 2) {
										$table_main.='<br><span style="font-weight:bold;">**Normal blood glucose level is lower than 140 mg/dL (7.8 mmol/L)</span>';
									}
									$table_main.='</td>
									<td style="width:50px;height:15px; padding-top:5px;text-align:center; ">&nbsp;</td>
								</tr>
							</table>
						</td>		
						
						<td style="width:350px; verticle-align:top;border:1px solid #C0C0C0;">
							<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;">Health Questionnaire</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;text-align:center;">';
										if($healthQuestionnaire){$table_main.=$healthQuestionnaire;}else{$table_main.="___";}
									$table_main.='</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;">Standing Orders</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;text-align:center;">';
									if($standingOrders!=""){$table_main.=$standingOrders;}else{$table_main.="___";}
									$table_main.='</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;vertical-align:top">Pat. Voided</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center;">';
									if($patVoided){$table_main.=$patVoided;}else{$table_main.="___";}
									$table_main.='</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;">Hearing Aids';
									if($hearingAids=="Yes" && $hearingAidsRemoved){
										if($hearingAidsRemoved=="Yes"){$table_main.="<br>Removed:&nbsp;Yes";}	
										if($hearingAidsRemoved=="No"){$table_main.="<br>Covered:&nbsp;Yes";}	
										
									}
									$table_main.='</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; text-align:center;">';
										if($hearingAids){$table_main.=$hearingAids;}else{$table_main.="___";}
															
										$table_main.='
									</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;">Denture';
									if($denture=="Yes" && $dentureRemoved){
										$table_main.="<br>&nbsp;&nbsp;Removed:&nbsp;".$dentureRemoved;	
									}
									$table_main.='</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; text-align:center;">';
										if($denture!=""){$table_main.=$denture;}else{$table_main.="___";}
															
										$table_main.='
									</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;">Any Pain</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;text-align:center;">';
									if($anyPain!=""){$table_main.=$anyPain;}else{$table_main.="___";}
									$table_main.='
									</td>
								</tr>
								<tr>
									<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;">
										<table style="width:300px; font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:100px;">Pain Level:&nbsp;';
													if($painLevel!=""){$table_main.=$painLevel;	}else{$table_main.="___";}
													$table_main.='
												</td>
												<td style="width:100px;">Location:&nbsp;';
													if($painLocation!=""){$table_main.=$painLocation;}else{$table_main.="___";}
													$table_main.='
												</td>
												<td style="width:100px;">Dr. Notified:&nbsp;</td>
											</tr>
										</table>
									</td>
									<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; text-align:center;">';
									if($doctorNotified!=""){$table_main.=$doctorNotified;}else{$table_main.="___";}
									$table_main.='</td>
								</tr>
								<tr>
									<td colspan="2" style="width:350px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;">
										<table style="width:350px; font-size:13px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:140px;">Height:&nbsp;';
													if($feet!=""){$table_main.=$feet;	}else{$table_main.="__";}
													$table_main.='&nbsp;ft&nbsp;';
													if($inch){$table_main.=$inch;	}else{$table_main.="__";}
													$table_main.='&nbsp;inch&nbsp;';
												$table_main.='</td>
												<td style="width:130px;">Weight:&nbsp;';
													if($weightlbs!=""){$table_main.=$weightlbs;}else{$table_main.="___";}
													$table_main.='&nbsp;lbs
												</td>
												<td style="width:80px;">BMI:&nbsp;';
													if($patientBMI !=""){$table_main.=$patientBMI;}else{$table_main.="___";}
													$table_main.='
												</td>
											</tr>
										</table>
									</td>
								</tr>
								
								<tr>
									<td colspan="2" style="width:350px;height:15px;vertical-align:top;border:1px solid #C0C0C0;">';
									$ViewPreopNurseVitalSignQry = "select * from `preopnursing_vitalsign_tbl` where  
									confirmation_id = '".$pConfId."' 			order by vitalsign_id";
									$ViewPreopNurseVitalSignRes = imw_query($ViewPreopNurseVitalSignQry) or die(imw_error()); 
									$ViewPreopNurseVitalSignNumRow = imw_num_rows($ViewPreopNurseVitalSignRes);
									if($ViewPreopNurseVitalSignNumRow>0) {
										$table_main.='<table style="width:350px;font-size:14px;" cellpadding="0" cellspacing="0">';				
											$k_pre=1;
											while($ViewPreopNurseVitalSignRow = imw_fetch_array($ViewPreopNurseVitalSignRes)) {
												$vitalsign_id=$ViewPreopNurseVitalSignRow["vitalsign_id"];  
												$vitalSignBp = $ViewPreopNurseVitalSignRow["vitalSignBp"];
												$vitalSignP = $ViewPreopNurseVitalSignRow["vitalSignP"];
												$vitalSignR = $ViewPreopNurseVitalSignRow["vitalSignR"];
												$vitalSignO2SAT = $ViewPreopNurseVitalSignRow["vitalSignO2SAT"];
												$vitalSignTemp  = $ViewPreopNurseVitalSignRow["vitalSignTemp"];
										
												$table_main.='<tr id="'.$vitalsign_id.'" >
																<td  style="width:20px;"  nowrap="nowrap" class="text_10b">BP</td>
																<td  style="width:50px;" nowrap="nowrap" class="text_10">'.$vitalSignBp.'</td>
																<td style="width:20px;"  class="text_10b">P</td>
																<td style="width:20px;"  class="text_10">'.$vitalSignP.'</td>
																<td style="width:20px;"  class="text_10b">R</td>
																<td style="width:20px;"  class="text_10">'.$vitalSignR.'</td>
																<td style="width:50px;"  align="right" class="text_10b">O2SAT</td>
																<td style="width:20px;"  class="text_10">'.$vitalSignO2SAT.'</td>
																<td style="width:40px;"  align="right" class="text_10b">Temp</td>
																<td style="width:20px;"  class="text_10">'.$vitalSignTemp.'</td>
																<td style="width:20px;" >&nbsp;</td>
															</tr>';
													
												$k_pre++;
											}
										$table_main.="</table>";
									}
			
									$table_main.='</td>
								</tr>
							</table>
						</td>
				</tr>
					
						
				<tr>
					<td colspan="2" style="padding-top:5px;width:700px;border-top:1px solid #C0C0C0;">';
						$preopnursecategoryQry = "SELECT * FROM preopnursequestionadmin WHERE 
						confirmation_id='".$pConfId."' GROUP BY categoryName ORDER BY id";
						$preopnursecategoryRes = imw_query($preopnursecategoryQry) or die(imw_error());
						$preopnursecategoryNumRow = imw_num_rows($preopnursecategoryRes);
						if($preopnursecategoryNumRow>0) {
							$k=0;
							$table_main.='<table  style="width:700px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:700px; font-weight:bold;"  align="left" valign="top">
													Preoperative Questions:
												</td>
											</tr>
											<tr>
												<td style="width:700px;">
													<table style="width:700px;font-size:14px;" cellpadding="0" cellspacing="0">
														';				
							while($preopnursecategoryRow = imw_fetch_array($preopnursecategoryRes)) {
								
								$categoryName = $preopnursecategoryRow['categoryName'];
								$k++;
						
								$preopnursequestionQry = "SELECT * FROM preopnursequestionadmin WHERE categoryName='".$categoryName."' AND confirmation_id='".$_REQUEST["pConfId"]."' AND preOpNurseOption!=''  ORDER BY id";
								$preopnursequestionRes = imw_query($preopnursequestionQry) or die(imw_error());
								$preopnursequestionNumRow = imw_num_rows($preopnursequestionRes);
								if($preopnursequestionNumRow>0) {
									$table_main.='<tr><td style="width:750px;padding-top:8px;border-top:1px solid #CCC;"><b>'.ucfirst($categoryName).'</b>-&nbsp;';
									$t=0;
									while($preopnursequestionRow=imw_fetch_array($preopnursequestionRes)) {
										$t++;
										//$preOpNurseQuestionId = $preopnursequestionRow['preOpNurseQuestionId'];
										$preOpNurseQuestionName = $preopnursequestionRow['preOpNurseQuestionName'];
										$question_pre_nurse_status = $preopnursequestionRow['preOpNurseOption'];
										$table_main.=$preOpNurseQuestionName.':&nbsp;'.$question_pre_nurse_status.'&nbsp;&nbsp;';
									}
							
								$table_main.='</td></tr>';		
								}
							}
							$table_main.='
									</table>
								</td>	
							</tr>
						</table>';
						}	
					
					$table_main.='</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:5px;width:700px; border:1px solid #C0C0C0;">
						<table style="width:700px;font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:170px; vertical-align:top; font-weight:bold;">Preoperative Comments:</td>
								<td style="width:470px;">';
									if($preOpComments){$table_main.=$preOpComments;}else{$table_main.="_________________";}
									$table_main.='
								</td>
							</tr>
						</table>			
					</td>
				</tr>
				<tr>
					<td colspan="2" style="width:700px; padding-top:10px;">';
						$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
						$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
						$ViewUserNameRow = @imw_fetch_array($ViewUserNameRes); 
						
						$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
						$loggedInUserType = $ViewUserNameRow["user_type"];
						$loggedInSignatureOfUser = $ViewUserNameRow["signature"];
						
						
						//$NurseNameShow = $loggedInUserName;
						 
						if($signNurseId<>0 && $signNurseId<>""){
							$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
							$signOnFileStatus = $signNurseStatus;	
						
						}
					$table_main.='<table style="width:700px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:250px;vertical-align:top;" ><b>Nurse:&nbsp;</b>
								'.(($NurseNameShow) ? $NurseNameShow : '_________').'
								<br><b>Electronically Signed:&nbsp;</b>
								'.(($signOnFileStatus=="Yes") ? $signOnFileStatus : '_________').'
								<br><b>Signature Date:&nbsp;</b>
								'.(($signOnFileStatus=="Yes") ? $objManageData->getFullDtTmFormat($signNurseDateTime) : '_________').'
								</td>';
					
								$table_main.='<td style="width:200px;" valign="top">&nbsp;</td>';
								
								
								$table_main.='<td style="width:250px;" valign="top"><b>Relief Nurse:&nbsp;</b>';
								
								$relivedNurseQry = "select * from users where user_type='Nurse' AND usersId='$relivedNurseId' ORDER BY lname";
								$relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
								$relivedNurseRow=@imw_fetch_array($relivedNurseRes);
								$relivedSelectNurseID = $relivedNurseRow["usersId"];
								$relivedNurseName = $relivedNurseRow["lname"].", ".$relivedNurseRow["fname"]." ".$relivedNurseRow["mname"];
								if($relivedNurseRow["lname"] && $relivedNurseRow["fname"]){
									$table_main.=$relivedNurseName;
								}else{$table_main.="__________";}
										
									
				$table_main.='		
								</td>
							</tr>	
									
						</table>
					</td>
					
				</tr>	
		';	
			
		$table_main.='</table> ';
		}