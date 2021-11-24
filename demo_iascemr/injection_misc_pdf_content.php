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
	
		
		$getInjectionMiscDetails = $objManageData->getExtractRecord('injection', 'confirmation_id', $pConfId," *,if(signSurgeon1DateTime!='0000-00-00',date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p'),'') as signSurgeon1DateTimeFormat,if(signNurse1DateTime!='0000-00-00',date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p'),'') as signNurse1DateTimeFormat,if(signNurse2DateTime!='0000-00-00',date_format(signNurse2DateTime,'%m-%d-%Y %h:%i %p'),'') as signNurse2DateTimeFormat ");
	}
	if(is_array($getInjectionMiscDetails)){
		extract($getInjectionMiscDetails);
		
		$injectionprocedureRecordpostedID = $injId;
		$injectionMiscFormStatus = $form_status;
		
		if($injectionMiscFormStatus <> 'completed' || $injectionMiscFormStatus <> 'not completed')
		{
			//$preVitalTime		=	($preVitalTime <> '00:00:00')		?	date('h:i A',strtotime($preVitalTime))	:	'';
			//$timeoutTime		=	($timeoutTime <> '00:00:00')		?	date('h:i A',strtotime($timeoutTime))		:	'';
			//$startTime			=	($startTime <> '00:00:00')			?	date('h:i A',strtotime($startTime))			:	'';
			//$endTime				=	($endTime <> '00:00:00')				?	date('h:i A',strtotime($endTime))				:	'';
			//$postVitalTime	=	($postVitalTime <> '00:00:00')	?	date('h:i A',strtotime($postVitalTime))	:	'';
			//$postIopTime		=	($postIopTime <> '00:00:00')		?	date('h:i A',strtotime($postIopTime))		:	'';
			
			$preVitalTime		=	($preVitalTime <> '00:00:00')		?	$objManageData->getTmFormat($preVitalTime)	:	'';
			$timeoutTime		=	($timeoutTime <> '00:00:00')		?	$objManageData->getTmFormat($timeoutTime)		:	'';
			$startTime			=	($startTime <> '00:00:00')			?	$objManageData->getTmFormat($startTime)			:	'';
			$endTime				=	($endTime <> '00:00:00')				?	$objManageData->getTmFormat($endTime)				:	'';
			$postVitalTime	=	($postVitalTime <> '00:00:00')	?	$objManageData->getTmFormat($postVitalTime)	:	'';
			$postIopTime		=	($postIopTime <> '00:00:00')		?	$objManageData->getTmFormat($postIopTime)		:	'';	
		}
		
		$blankLine				=	'_____';
		$blankLineLong		=	'__________';
		$preVitalTime			=	($preVitalTime)	?	$preVitalTime		:	$blankLine;
		$preVitalBp				=	($preVitalBp)		?	$preVitalBp			:	$blankLine;
		$preVitalPulse		=	($preVitalPulse)?	$preVitalPulse	:	$blankLine;
		$preVitalResp			=	($preVitalResp)	?	$preVitalResp		:	$blankLine;
		$preVitalSpo			=	($preVitalSpo)	?	$preVitalSpo		:	$blankLine;
		
		$timeoutTime			=	($timeoutTime)	?	$timeoutTime		:	$blankLine;
		$timeoutProcVerified=	($timeoutProcVerified)	?	'Yes'	:	$blankLine;
		$timeoutSiteVerified=	($timeoutSiteVerified)	?	'Yes'	:	$blankLine;
		
		$chkConsentSigned	=	($chkConsentSigned)?	'Yes'				:	$blankLine;
		$startTime				=	($startTime)		?	$startTime			:	$blankLine;
		$endTime					=	($endTime)			?	$endTime				:	$blankLine;
		$procedureComments=	($procedureComments)	?	$procedureComments	:	$blankLineLong;
		
		$complications		=	($complications)?	$complications	:	$blankLine;
		$comments					=	($comments)			?	$comments				:	$blankLineLong;
		
		
		$postVitalTime		=	($postVitalTime)	?	$postVitalTime	:	$blankLine;
		$postVitalBp			=	($postVitalBp)		?	$postVitalBp		:	$blankLine;
		$postVitalPulse		=	($postVitalPulse)	?	$postVitalPulse	:	$blankLine;
		$postVitalResp		=	($postVitalResp)	?	$postVitalResp	:	$blankLine;
		$postVitalSpo			=	($postVitalSpo)		?	$postVitalSpo		:	$blankLine;
		
		$postIop					=	($postIop)				?	$postIop				:	$blankLine;
		$postIopSite			=	($postIopSite)		?	$postIopSite		:	$blankLine;
		$postIopTime			=	($postIopTime)		?	$postIopTime		:	$blankLine;
		
		
		
		$Nurse1Name				=	$signNurse1LastName.", ".$signNurse1FirstName." ".$signNurse1MiddleName;
		$Nurse1NameShow		=	($signNurse1Id)		?	$Nurse1Name			:	$blankLineLong;	
		$Nurse1SignStatus	=	($signNurse1Id)		?	$signNurse1Status:$blankLineLong;	
		$Nurse1SignDateTime=($signNurse1Id)		?	$objManageData->getFullDtTmFormat($signNurse1DateTime):$blankLineLong;	
		
		$Nurse2Name				=	$signNurse2LastName.", ".$signNurse2FirstName." ".$signNurse2MiddleName;
		$Nurse2NameShow		=	($signNurse2Id)		?	$Nurse2Name			:	$blankLineLong;	
		$Nurse2SignStatus	=	($signNurse2Id)		?	$signNurse2Status:$blankLineLong;	
		$Nurse2SignDateTime=($signNurse2Id)		?	$objManageData->getFullDtTmFormat($signNurse2DateTime):$blankLineLong;	
		
		$Surgeon1Name				=	$signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
		$Surgeon1NameShow		=	($signSurgeon1Id)		?	'Dr. '.$Surgeon1Name			:	$blankLineLong;	
		$Surgeon1SignStatus	=	($signSurgeon1Id)		?	$signSurgeon1Status	:	$blankLineLong;	
		$Surgeon1SignDateTime=($signSurgeon1Id)		?	$objManageData->getFullDtTmFormat($signSurgeon1DateTime):$blankLineLong;	
		
		
		//Start Creating HTML for Pre Op Medications
		$dataArray	=	$innArray	=	array();
		$medValue		=	$lotValue	=	$endLoop = $dataCount = $innValue =	'';
		
		$dataArray	=	explode('~@~',$preOpMeds);
		$dataCount	=	count($dataArray);
		$endLoop		=	($dataCount < 3) ? 3	:	$dataCount;
		$preOpMedHTML=	'';
		for($loop = 0; $loop < $endLoop ; $loop++)
		{ 
			$innValue	=	trim($dataArray[$loop]);
			$innArray	=	explode('@#@',$innValue);
			$medValue	=	$innArray[0];
			$lotValue	=	$innArray[1];
			
			$preOpMedHTML	.=	'<tr>';
			$preOpMedHTML	.=	'<td style="width:175px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop" >'.$medValue.'</td>';
			$preOpMedHTML	.=	'<td style="width:175px;" nowrap="nowrap" class="text_10 pl5 bdrtop ">'.$lotValue.'</td>';
			$preOpMedHTML	.=	'</tr>';
		}
		
		
		//Start Creating HTML for Intravitreal Medications
		$dataArray	=	$innArray	=	array();
		$medValue		=	$lotValue	=	$endLoop = $dataCount = $innValue =	'';
		
		$dataArray	=	explode('~@~',$intravitrealMeds);
		$dataCount	=	count($dataArray);
		$endLoop		=	($dataCount < 3) ? 3	:	$dataCount;
		$intravitrealMedHTML=	'';
		for($loop = 0; $loop < $endLoop ; $loop++)
		{ 
			$innValue	=	trim($dataArray[$loop]);
			$innArray	=	explode('@#@',$innValue);
			$medValue	=	$innArray[0];
			$lotValue	=	$innArray[1];
			
			$intravitrealMedHTML	.=	'<tr>';
			$intravitrealMedHTML	.=	'<td style="width:175px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop" >'.$medValue.'</td>';
			$intravitrealMedHTML	.=	'<td style="width:175px;" nowrap="nowrap" class="text_10 pl5 bdrtop ">'.$lotValue.'</td>';
			$intravitrealMedHTML	.=	'</tr>';
		}
		
		
		//Start Creating HTML for Post Op Medications
		$dataArray	=	$innArray	=	array();
		$medValue		=	$lotValue	=	$endLoop = $dataCount = $innValue =	'';
		
		$dataArray	=	explode('~@~',$postOpMeds);
		$dataCount	=	count($dataArray);
		$endLoop		=	($dataCount < 3) ? 3	:	$dataCount;
		$postOpMedHTML=	'';
		for($loop = 0; $loop < $endLoop ; $loop++)
		{ 
			$innValue	=	trim($dataArray[$loop]);
			$innArray	=	explode('@#@',$innValue);
			$medValue	=	$innArray[0];
			$lotValue	=	$innArray[1];
			
			$postOpMedHTML	.=	'<tr>';
			$postOpMedHTML	.=	'<td style="width:175px;" nowrap="nowrap" class="text_10 bdrrght_new pl5 bdrtop" >'.$medValue.'</td>';
			$postOpMedHTML	.=	'<td style="width:175px;" nowrap="nowrap" class="text_10 pl5 bdrtop ">'.$lotValue.'</td>';
			$postOpMedHTML	.=	'</tr>';
		}
		
		
		
		
	}

		
if($injectionMiscFormStatus == 'completed' || $injectionMiscFormStatus=='not completed')
{
	$table_main.=$head_table."\n";
	$table_main.='<table style="width:700px;font-size:14px;border:1px solid #C0C0C0; margin-top:10px;" cellpadding="0" cellspacing="0">';
	
	$table_main.='
				<tr>
					<td colspan="2" style="width:700px;" class="fheader bgcolor">Injection/Miscellaneous</td>
				</tr>';
	
	
	// Start Printing Pre Op Vital Signs
	$table_main.='
				<tr>
					<td colspan="2" style="width:700px;vertical-align:top; ">
						<table style="width:100% font-size:14px; background:#FFF;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="10" style="width:100%;" class="bdrbtm bgcolor"><b>Vital Sign - Pre Op</b></td>
							</tr>
							<tr>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>Time:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$preVitalTime.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>BP:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$preVitalBp.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>P:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$preVitalPulse.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>R:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$preVitalResp.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>Spo2:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$preVitalSpo.'</td>
							</tr>
						</table>
					</td>
				</tr>';
		
				
	// Start Printing Timeout & Procedure Block			
	$table_main.='
				<tr>
					<td style="width:350px;vertical-align:top;" class="bdrBtmRght">
						<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm bgcolor" colspan="2" style="width:100%;"><b>TimeOut</b></td>
							</tr>
							<tr>
								<td style="width:280px;" nowrap="nowrap" class="text_10 bdrbtm bold" >Site Verified</td>
								<td style="width:70px;" nowrap="nowrap" class="text_10 bdrbtm pr5 txt_rgt">'.$timeoutSiteVerified.'</td>
							</tr>
							<tr>
								<td style="width:280px;" nowrap="nowrap" class="text_10 bdrbtm bold" >Procedure Verified</td>
								<td style="width:70px;" nowrap="nowrap" class="text_10 bdrbtm pr5 txt_rgt" >'.$timeoutProcVerified.'</td>
							</tr>
							<tr>
								<td style="width:280px;" nowrap="nowrap" class="text_10 bdrbtm bold">Time</td>
								<td style="width:70px;" nowrap="nowrap" class="text_10 bdrbtm pr5 txt_rgt">'.$timeoutTime.'</td>
							</tr>
							<tr>
								<td colspan="2" style="width:350px;" nowrap="nowrap" class="text_10" >
									<span class="pt5"><b>Nurse Signature:	</b>'.$Nurse1NameShow.'</span><br>
									<span class="pt5"><b>Electronically Signed:	</b>'.$Nurse1SignStatus.'</span><br>
									<span class="pt5"><b>Signature Date	:	</b>'.$Nurse1SignDateTime.'</span><br>
								</td>
							</tr>
							
						</table>
					</td>
					
					<td style="width:350px;vertical-align:top;" class="bdrbtm">
						<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm bgcolor" colspan="2" style="width:100%;"><b>Procedure</b></td>
							</tr>
							
							<tr>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrbtm bold" >Site </td>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrbtm pr5 txt_rgt">'.$patientConfirmSiteName.'</td>
							</tr>
							
							<tr>
								<td style="width:350px;" colspan="2" nowrap="nowrap" class="text_10 bdrbtm pr5" ><b>Procedure&nbsp;</b>
								<span class="txt_rgt ">'.$patientConfirmProcedureName.'</span></td>
							</tr>
							
							<tr>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrbtm bold" >Consent Signed </td>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrbtm pr5 txt_rgt">'.$chkConsentSigned.'</td>
							</tr>
							
							<tr>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrbtm" >
									<b>Start Time&nbsp;</b><span class="txt_rgt">'.$startTime.'</span>
								</td>
								
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrbtm pl5 pr5" >
									<b>End Time&nbsp;</b><span class="txt_rgt">'.$endTime.'</span>
								</td>
							</tr>
							
							<tr>
								<td colspan="2" style="width:350px;" nowrap="nowrap" class="text_10 pt5 ">
									<b>Comment:&nbsp;</b>'.$procedureComments.'<br><br>
								</td>
							</tr>
							
						</table>
					</td>
					
					
				</tr>';	
				
	
	// Start Printing Pre Op/Intravitreal Medications Block			
	$table_main.='
				<tr>
					<td style="width:350px;vertical-align:top;" class="bdrBtmRght">
						<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm bgcolor" style="width:100%" colspan="2" valign="middle"><b>Pre Op Meds</b></td>
							</tr>
							<tr>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrrght bold pt5" >Medication</td>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bold pt5">#Lot</td>
							</tr>
							'.$preOpMedHTML.'
						</table>
					</td>
					
					<td style="width:350px;vertical-align:top;" class="bdrbtm">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm bgcolor" style="width:100%" colspan="2" valign="middle"><b>Intravitreal Meds</b></td>
							</tr>
							<tr>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrrght bold pt5" >Medication</td>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bold pt5">#Lot</td>
							</tr>
							'.$intravitrealMedHTML.'
						</table>
					</td>
					
					
				</tr>';
				
	// Start Printing Post Op Medications/Complications Block			
	$table_main.='
				<tr>
					<td style="width:350px;vertical-align:top;" class="bdrBtmRght">
						<table style="width:350px;  font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm bgcolor" style="width:100%;" colspan="2" valign="middle"><b>Post Op Meds</b></td>
							</tr>
							<tr>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bdrrght bold pt5">Medication</td>
								<td style="width:175px;" nowrap="nowrap" class="text_10 bold pt5">#Lot</td>
							</tr>
							'.$postOpMedHTML.'
						</table>
					</td>
					
					<td style="width:350px;vertical-align:top;" class="bdrbtm">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bdrbtm bgcolor" style="width:100%" colspan="2" valign="middle"><b>Complications</b></td>
							</tr>
							<tr>
								<td class="bdrbtm pt5" style="width:250px;"><b>Complications</b></td>
								<td class="bdrbtm txt_rgt pt5 pr5" style="width:100px;">'.$complications.'</td>
							</tr>
							<tr>
								<td class="pt5" colspan="2" valign="middle" style="width:350px;"><b>Comments:&nbsp;</b>'.$comments.'</td>
							</tr>
							
							
						</table>
					</td>
					
					
				</tr>';										
	
	// Start Printing Post Op Vital Signs & IOP Values
	$table_main.='
				<tr>
					<td colspan="2" style="width:700px;vertical-align:top;">
						<table style="width:700px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="10" class="bdrbtm bgcolor" style="width:100%;"><b>Vital Sign - Post Op</b></td>
							</tr>
							<tr>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>Time:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$postVitalTime.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>BP:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$postVitalBp.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>P:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$postVitalPulse.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>R:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$postVitalResp.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>Spo2:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$postVitalSpo.'</td>
							</tr>
							<tr>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>IOP:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$postIop.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>Site:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$postIopSite.'</td>
									<td  style="width:30px;" nowrap="nowrap" class="text_10 bdrbtm pt5"><b>Time:</b></td>
									<td  style="width:110px;" nowrap="nowrap" class="text_10 bdrbtm pt5">'.$postIopTime.'</td>
									<td  style="width:280px;" colspan="4" nowrap="nowrap" class="text_10 bdrbtm pt5">&nbsp;</td>
							</tr>
							
						</table>
					</td>
				</tr>';				
	
	
	// Start Printing Nurse2 and Surgeon1 Signature
	$table_main.='
				<tr>
					<td colspan="2" valign="middle" style="width:700px; height:30px;" nowrap="nowrap" class="bgcolor bold">
							Patient discharged to home in good condition with responsible adult.
					</td>
				</tr>
				<tr>
					<td style="width:350px;vertical-align:top;" nowrap="nowrap" class="text_10 bdrbtm">
							<span class="pt5"><b>Nurse Signature:	</b>'.$Nurse2NameShow.'</span><br>
							<span class="pt5"><b>Electronically Signed:	</b>'.$Nurse2SignStatus.'</span><br>
							<span class="pt5"><b>Signature Date	:	</b>'.$Nurse2SignDateTime.'</span><br>
					</td>
					
					<td style="width:350px;vertical-align:top;" nowrap="nowrap" class="text_10 bdrbtm">
							<span class="pt5"><b>Surgeon Signature:	</b> '.$Surgeon1NameShow.'</span><br>
							<span class="pt5"><b>Electronically Signed:	</b>'.$Surgeon1SignStatus.'</span><br>
							<span class="pt5"><b>Signature Date	:	</b>'.$Surgeon1SignDateTime.'</span><br>
					</td>
					
					
				</tr>';	
					
				
	$table_main.='</table>';
	
}
?>