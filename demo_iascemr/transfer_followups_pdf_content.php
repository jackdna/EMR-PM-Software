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


	if($pConfId){
		$getTransferFollowupDetails = $objManageData->getExtractRecord('transfer_followups', 'confirmation_id', $pConfId," *,if(signSurgeon1DateTime!='0000-00-00',date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p'),'') as signSurgeon1DateTimeFormat,if(signNurseDateTime!='0000-00-00',date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p'),'') as signNurseDateTimeFormat,if(signNurse1DateTime!='0000-00-00',date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p'),'') as signNurse1DateTimeFormat ");
	}
	if(is_array($getTransferFollowupDetails)){
		extract($getTransferFollowupDetails);
		$transferFollowupFormStatus = $form_status;
		//$contactedTime = date('h:i A', strtotime($contacted_time));
		$contactedTime = $objManageData->getTmFormat($contacted_time);
		if(($form_status <> 'completed' || $form_status <> 'not completed') && $contacted_time ==  '00:00:00')
		{
			$contactedTime = '';	
		}
		//$summaryCareTime = date('h:i A', strtotime($summary_of_care_time));
		$summaryCareTime = $objManageData->getTmFormat($summary_of_care_time);
		if(($form_status <> 'completed' || $form_status <> 'not completed') && $summary_of_care_time ==  '00:00:00')
		{
			$summaryCareTime = '';	
		}
		
		if($lv_running == '1' )	$lv_running	=	'No';
		elseif($lv_running == '2' )	$lv_running	=	'Yes';
		else $lv_running	=	'';
		
		if($airway_support == '1' )			$airway_support	=	'No';
		elseif($airway_support == '2' )		$airway_support	=	'Yes';
		else $airway_support	=	'';
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
	
if($transferFollowupFormStatus == 'completed' || $transferFollowupFormStatus=='not completed')
{
	$table_main.=$head_table."\n";
	$table_main.='<table style="width:700px;font-size:14px;border:1px solid #C0C0C0; margin-top:10px;" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="fheader bgcolor">Transfer &amp; Follow-up</td>
				</tr>
				<tr>
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							';
							
							$table_main.='
							<tr>
								<td style="width:200px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2"><b>Reason for Transfer</b></td>
								<td style="width:150px;text-align:right; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2">
								'.($transfer_reason ? $transfer_reason : '_____________').'
								</td>
							</tr>';
							
							if($transfer_reason_detail)
							{
								
								$table_main.='
								<tr>
									<td style="width:350px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="4">
										<b>Reason for Transfer Details:</b><br>'.$transfer_reason_detail .'
									</td>
								</tr>';
							}
							else
							{
							$table_main.='
								<tr>
									<td style="width:200px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2"><b>Reason for Transfer Details</b></td>
									<td style="width:150px;text-align:right; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2">_____________</td>
								</tr>';
							}
							
							$table_main.='
							<tr>
								<td style="width:150px; padding-top:5px;"><b>Hospital Contacted</b></td>
								<td style="width:50px;text-align:left; padding-top:5px;">
								'.($hospital_contacted == '1' ? 'Hospital' : '______' ).'
								</td>
								<td style="width:150px;text-align:right; padding-top:5px;" colspan="2" >
									<table style="width:150px; " cellpadding="0" cellspacing="0" align="right"> 
										<tr>
											<td style="width:55px; text-align:right">
												<b>Time:</b>
											</td>
											<td style="width:95px; text-align:left">
												'.($contactedTime ? $contactedTime : '_____________' ).'
											</td>
										</tr>
									</table>
								</td>
							</tr>';
							
							$table_main.='
							<tr>
								<td style="width:150px; padding-top:5px;border-bottom:1px solid #C0C0C0;"></td>
								<td style="width:200px;text-align:left; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="3">
									<table style="width:200px; " cellpadding="0" cellspacing="0"> 
										<tr>
											<td><b>Hospital&nbsp;Name:</b></td>
											'.($hospital_name ? '<td style="width:100px; text-align:left">'.$hospital_name.'</td>' 
																   : '<td style="width:100px; text-align:right">&nbsp;&nbsp;_____________</td>' ).'
										</tr>
									</table>
								</td>
							</tr>';
							
							$table_main.='
							<tr>
								<td style="width:200px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2"><b>Method of Transfer</b></td>
								<td style="width:150px;text-align:right; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2">
								'.($transfer_method ? $transfer_method : '_____________').'
								</td>
							</tr>';
							
							$table_main.='
							<tr>
								<td style="width:200px; padding-top:5px;border-bottom:1px solid #C0C0C0; border-right:1px solid #C0C0C0;" colspan="2"><b>Amubulance Provider:</b>
								'.($ambulance_provider ?  '<br>'.$ambulance_provider : '______' ).'
								</td>
								<td style="width:150px;text-align:left; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2">';
								if($signNurseId<>0 && $signNurseId<>"")
								{
									$table_main.="<b>Nurse:&nbsp;</b> ". $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
									$table_main.="<br><b>Electronically Signed:&nbsp;</b>Yes";
									$table_main.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signNurseDateTime);
								}
								else 
								{
									$table_main.="<b>Nurse:&nbsp;</b>________";
									$table_main.="<br><b>Electronically Signed:&nbsp;</b>________";
									$table_main.="<br><b>Signature Date:&nbsp;</b>________";
								}
							$table_main.='
								</td>
							</tr>';
						
							$table_main.='
							<tr>
								<td style="width:150px; padding-top:5px;border-bottom:1px solid #C0C0C0;" >&nbsp;</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0; " ><b>Yes/No</b></td>
								<td style="width:150px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2">&nbsp;</td>
							</tr>';
							
							$table_main.='
							<tr>
								<td style="width:150px; padding-top:5px;border-bottom:1px solid #C0C0C0;" ><b>IV Running</b></td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0; " >
								'.($lv_running ? $lv_running : '_____').'
								</td>
								<td style="width:150px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2">&nbsp;</td>
							</tr>';
							
							$table_main.='
							<tr>
								<td style="width:150px; padding-top:5px;border-bottom:1px solid #C0C0C0;"><b>Airway Support </b></td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0; " >
								'.($airway_support ? $airway_support : '_____').'
								</td>
								<td style="width:150px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2"><b>O2@:</b>
								'.($o2at ? $o2at : '_______').'
								</td>
							</tr>';
							
						$table_main.='
							
						</table>	
					</td>
					
								
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" style="width:350px;padding-top:5px;font-weight:bold;border-bottom:1px solid #C0C0C0;" class="bgcolor">Document Check List</td>							
							</tr>
							<tr>
								<td style="width:200px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">&nbsp;</td>
								<td style="width:150px;border-bottom:1px solid #C0C0C0;padding-top:5px; text-align:right"><b>Sent/ Not Sent/ N/A</b></td>
								
							</tr>';
							$documents	=	array(
																'transfer_forms'=>'Transfer Forms',
																'demographics'=>'Demographics',
																'chart_note'=> 'Chart Note',
																'lab_work' => 'Lab Work',
																'ekg'=>'EKG',
																'advance_directive'=>'Advance Directive<br>(if available)',
																'cpr_report' => 'CPR Report'
																								);
							foreach($documents as $key=>$document)
							{
								if($$key == '1') $$key = 'N/A'; 
								elseif($$key == '2') $$key = 'Not Sent'; 
								elseif($$key == '3') $$key = 'Sent'; 
								else $$key = '______' ;
								$table_main.='
									<tr>
										<td style="width:200px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">'.$document.'</td>
										<td style="width:150px;border-bottom:1px solid #C0C0C0;padding-top:5px; text-align:right">'.$$key.'</td>
									</tr>
									';
							}
							
							$table_main.='
							<tr>
								<td style="width:350px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2">
									<b>Patient Belongings:</b>
									'.($patient_belongings ?  '<br>'.$patient_belongings : '_____________' ).'
								</td>
							</tr>';
							
							$table_main.='
							<tr>
								<td style="width:350px; padding-top:5px;border-bottom:1px solid #C0C0C0;" colspan="2">
									<b>Additional Comments:</b>
									'.($additional_comments ?  '<br>'.$additional_comments : '_____________' ).'
								</td>
							</tr>';
															
	$table_main.='						
						</table>
					</td>
				</tr>
				
				<tr>
					<td style="width:700px;vertical-align:top;" colspan="2">
						<table style="width:700px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
	';
	
							
						// Vital Sign Grid Printing Section - Start
						
						if($vitalSignGridStatus)
						{
									$table_main.='<tr>';
									$table_main.='<td class="bgcolor bold bdrbtm" colspan="8" style="width:700px; vertical-align:middle; ">&nbsp;&nbsp;Vital Signs</td>';
									$table_main.='</tr>';
									
									
									$table_main.='<tr>';	
									$table_main.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Time</b></td>';
									$table_main.='<td colspan="2" class="bdrbtm" style="width:200px; border-right:1px solid #C0C0C0; vertical-align:middle;">&nbsp;<b>B/P</b></td>';
									$table_main.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Pulse</b></td>';
									$table_main.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>RR</b></td>';
									$table_main.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Temp<sup>O</sup> C</b></td>';
									$table_main.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>EtCO<sub>2</sub></b></td>';
									$table_main.='<td rowspan="2" class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;<b>OSat<sub>2</sub></b></td>';		
									$table_main.='</tr>';
									$table_main.='<tr>';	
									
									$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Systolic</b></td>';
									$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Diastolic</b></td>';
									
									$table_main.='</tr>';
										
									
									
									$condArr		=	array();
									$condArr['confirmation_id']	=	$pConfId ;
									$condArr['chartName']				=	'transfer_and_followups_form' ;
									
									$gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time','Asc');
						
									$gCounter	=	0;
									if(is_array($gridData) && count($gridData) > 0  )
									{
										foreach($gridData as $gridRow)
										{		
											$gCounter++;
											//$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
											$fieldValue1= $objManageData->getTmFormat($gridRow->start_time);
											$fieldValue2= $gridRow->systolic;
											$fieldValue3= $gridRow->diastolic;
											$fieldValue4= $gridRow->pulse;
											$fieldValue5= $gridRow->rr;
											$fieldValue6= $gridRow->temp;
											$fieldValue7= $gridRow->etco2;
											$fieldValue8= $gridRow->osat2;
											
											$table_main.='<tr>';	
											$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue1.'</td>';
											$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue2.'</td>';
											$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue3.'</td>';
											$table_main.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue4.'</td>';
											$table_main.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue5.'</td>';
											$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue6.'</td>';
											$table_main.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue7.'</td>';
											$table_main.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;'.$fieldValue8.'</td>';		
											$table_main.='</tr>';
												
										}
									}
									
									for($loop = $gCounter; $loop < 3; $loop++)
									{
											$table_main.='<tr>';	
											$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_main.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_main.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_main.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_main.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_main.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;</td>';		
											$table_main.='</tr>';
									}
						
						}
						// Vital Sign Grid Printing Section - End
	
	$table_main.='					
						</table>
					</td>
				</tr>
				
				<tr>
					<td style="width:700px;vertical-align:top;" colspan="2">
						<table style="width:700px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:750px;" valign="top">
									<table style="width:750px; " cellpadding="0" cellspacing="0">
										<tr>
											<td class="bgcolor bold bdrbtm" colspan="4" style="width:100%; vertical-align:middle; ">Summary of Care</td>
										</tr>
										
										<tr>
											<td colspan="4" style="width:700px; vertical-align:middle;" class="bdrbtm"><b>Summary of Care Notes :</b>
											'.($summary_of_care_notes ?  $summary_of_care_notes : '_____________' ).'
											</td>
										</tr>
										
										<tr>
											<td class="bdrbtm" style="width:350px; vertical-align:top; padding-top:3px; border-right:solid 1px #C0C0C0; " colspan="2">
											<b>Surgeon Reassessment : </b>'.($surgeon_reassessment ? 'Yes' : '_____' ).'
											<br>It is my medical judgement that this transfer will not create a medical hazard to the patient.
											</td>
											<td class="bdrbtm" style="width:200px; vertical-align:top; padding-top:3px; ">';
											
											if($signSurgeon1Id<>0 && $signSurgeon1Id<>"")
											{
												$table_main.="<b>Surgeon:&nbsp;</b> ". $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
												$table_main.="<br><b>Electronically Signed:&nbsp;</b>Yes";
												$table_main.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signSurgeon1DateTime);
											}
											else 
											{
												$table_main.="<b>Surgeon:&nbsp;</b>________";
												$table_main.="<br><b>Electronically Signed:&nbsp;</b>________";
												$table_main.="<br><b>Signature Date:&nbsp;</b>________";
											}
											$table_main.='
											</td>
											<td class="bdrbtm" style="width:150px; vertical-align:top; text-align:right; padding-top:3px; " >
												<table style="width:150px; " cellpadding="0" cellspacing="0" align="right"> 
													<tr>
														<td style="width:55px; text-align:right">
															<b>Time:</b>
														</td>
														<td style="width:95px; text-align:left">
															'.($summaryCareTime ? $summaryCareTime : '_____________' ).'
														</td>
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
				
				<tr>
					<td style="width:750px;vertical-align:top;" colspan="2">
						<table style="width:750px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:750px;" valign="top">
									<table style="width:750px; " cellpadding="0" cellspacing="0">
										<tr>
											<td class="bgcolor bold bdrbtm" colspan="3" style="width:100%; vertical-align:middle; ">Hospital Transfer Follow Up</td>
										</tr>
										
										<tr>
											<td class="bdrbtm " style="width:240px; vertical-align:middle; border-right:solid 1px #C0C0C0; "><b>Date Discharged from Hospital </b></td>
											<td class="bdrbtm" style="width:100px; vertical-align:middle; border-right:solid 1px #C0C0C0; "><b>Date </b></td>
											<td class="bdrbtm" style="width:360px; vertical-align:middle; " rowspan="2">';
											
											if($signNurse1Id<>0 && $signNurse1Id<>"")
											{
												$table_main.="<b>Nurse:&nbsp;</b> ". $signNurse1LastName.", ".$signNurse1FirstName." ".$signNurse1MiddleName;
												$table_main.="<br><b>Electronically Signed:&nbsp;</b>Yes";
												$table_main.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signNurse1DateTime);
											}
											else 
											{
												$table_main.="<b>Nurse:&nbsp;</b>________";
												$table_main.="<br><b>Electronically Signed:&nbsp;</b>________";
												$table_main.="<br><b>Signature Date:&nbsp;</b>________";
											}
											
											$dateDischarge	=	'';	
											if($date_discharge_from_hospital && $date_discharge_from_hospital <> '0000-00-00')
											{
												$dateDischarge	=	date('m-d-Y', strtotime($date_discharge_from_hospital));
											}
											$followupDate		=	'';	
											if($fDate && $fDate <> '0000-00-00')
											{
												$followupDate	=	date('m-d-Y', strtotime($fDate));
											}
											
											$table_main.='
											</td>
										</tr>
										
										<tr>
											<td class="bdrbtm" style="width:240px; vertical-align:middle; border-right:solid 1px #C0C0C0; ">
											'.($dateDischarge ? $dateDischarge : '__________________' ).'
											</td>
											<td class="bdrbtm" style="width:100px; vertical-align:middle; border-right:solid 1px #C0C0C0; ">
											'.($followupDate ? $followupDate : '__________________' ).'
											</td>
										</tr>
										<tr>
											<td colspan="3" style="width:750px; vertical-align:top; "><b>Discharge Comment:</b>
											'.($discharge_comments ?  $discharge_comments : '_____________' ).'
											</td>
										</tr>
											
									</table>
								</td>					
							</tr>
						</table>
					</td>
				</tr>
					
				
			</table>
	';
	
}
?>