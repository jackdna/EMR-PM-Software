<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$table.='
		<table cellpadding="0" cellspacing="0" style="width:700px;border:1px solid #C0C0C0;font-size:13px;">
			<tr>
				<td class="fheader" colspan="4" style="width:700px;">SAFETY CHECKLIST</td>
			</tr>	
			<tr>
				<td class="bgcolor pd bold" style="width:300px;">PROCEDURE CHECK-IN</td>
				<td class="cbold bgcolor" style="width:50px;">Yes/No</td>
				<td class="bgcolor pd" style="width:300px;">&nbsp;</td>
				<td class="cbold bgcolor" style="width:50px;">Yes/No</td>
			</tr>
			<tr>
				<td class="pd bold bdrbtm" colspan="4" style="width:700px;">In Holding Area</td>
			</tr>
			<tr>
				<td class="bdrbtm" colspan="4" style="width:700px; vertical-align:top;text-align:left;">
					<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
						<tr>
							<td class="bold" style="width:365px; vertical-align:top;text-align:left;">
								Patient/patient representative actively confirms with Nurse:
							</td>
							<td  style="width:140px; vertical-align:top;text-align:left;">';
								if($signNurse1Id){
									$table.=$signNurse1LastName.", ".$signNurse1FirstName." ".$signNurse1MiddleName."<br><b>Electronically Signed:&nbsp;</b>Yes";   
								}else {
									$table.='________';
								}	
							$table.='
							</td>
							<td style="width:210px;vartical-align:top;text-align:right;">';
								$getReliefNurseName="";
								$table.="<b>Relief Nurse / Anesthesia:</b><br>";
								if($reliefNurse1){
									$getReliefNurseName=getUsrNm($reliefNurse1);
									$table.=$getReliefNurseName[0];   
								}else{$table.='________';}	
							$table.='
							</td>
						</tr>';
						if($signNurse1Id){
						$table.='	
						<tr>
							<td>&nbsp;</td>
							<td colspan="2">
								<b>Signature Date:&nbsp;</b>'.date("m-d-Y h:i A",strtotime($signNurse1DateTime));
							$table.='
							</td>
						</tr>';
						}
					$table.='	
					</table>
				</td>
			</tr>
			<tr>
				<td class="bdrbtm"  style="text-align:left;">Identity</td>
				<td class="cbold bdrbtm" style="width:50px;">';
				 	if($identity){ $table.= stripslashes($identity); }else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm" style="text-align:left;width:290px;">Procedure and procedure site</td>
				<td class="cbold bdrbtm" style="width:50px;">';
				 if($procedureAndProcedureSite){ $table.=stripslashes($procedureAndProcedureSite); }else{$table.="__";}
				$table.=' </td>
			</tr>
			<tr>
				<td class="bdrbtm" style="text-align:left;">Site Marked and Verified</td>
				<td class="cbold bdrbtm"> ';
					 if($siteMarkedByPerson) {$table.=stripslashes($siteMarkedByPerson); }else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm" style="text-align:left; ">Consent(s)</td>
				<td class="cbold bdrbtm">';
				if($consent){ $table.=stripslashes($consent); }else{$table.="__"; }
				$table.=' </td>
			</tr>
			<tr>
				<td class="bgcolor pd bold" colspan="4" style="width:700px;">Nurse confirms presence of:</td>
			</tr>
			<tr>
				<td class="bdrbtm">History and physical</td>
				<td class="cbold bdrbtm">';
					if($historyAndPhysical) { $table.=stripslashes($historyAndPhysical); }else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Preanesthesia assessment</td>
				<td class="cbold bdrbtm">';
				if($preanesthesiaAssessment){ $table.=stripslashes($preanesthesiaAssessment);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="bdrbtm">Diagnostic and radiologic test results</td>
				<td class="cbold bdrbtm">';
				if($diagnosticAndRadiologic) { $table.=stripslashes($diagnosticAndRadiologic);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Blood product</td>
				<td class="cbold bdrbtm">';
				 if($bloodProduct) { $table.=stripslashes($bloodProduct);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="bdrbtm">Any special equipment, devices, implants</td>
				<td class="cbold bdrbtm"> ';
				if($anySpecialEquipment) { $table.=stripslashes($anySpecialEquipment); }else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Beta blocker medication given</td>
				<td class="cbold bdrbtm">';
			 	if($betaBlockerMedication){ $table.=stripslashes($betaBlockerMedication); } else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="bdrbtm">Venous thromboembolism prophylaxis ordered</td>
				<td class="cbold bdrbtm">';
				if($venousThromboembolism){ $table.=stripslashes($venousThromboembolism);} else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Normothermia measures</td>
				<td class="cbold bdrbtm">';
				if($jormothermiaMeasures) { $table.=stripslashes($jormothermiaMeasures);}else{$table.="__"; }
				$table.='</td>
			</tr>';
			
			if($version_num < 2) 
			{
			$table.='
			<tr>
				<td class="bgcolor pd bold" colspan="4" style="width:700px;">SIGN-IN</td>
			</tr>
			<tr>
				<td class="pd bold bdrbtm" colspan="4">Before Induction of Anesthesia</td>
			</tr>
			<tr>
				<td class="bdrbtm" colspan="4" style="text-align:left; vertical-align:top;">
					<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
						<tr>
							<td style="width:280px;">
								<strong>Nurse and anesthesia care provider confirm:&nbsp;</strong>
							</td>
							<td style="width:200px; vertical-align:top;text-align:left;">';
								if($signNurse2Id){
									$table.=$signNurse2LastName.", ".$signNurse2FirstName." ".$signNurse2MiddleName."<br><b>Electronically Signed:&nbsp;</b>Yes"; 
								}else {
									$table.='__________';
								}	
								
							$table.='
							</td>
							<td style="width:235px;vartical-align:top;text-align:right;">';
								$getReliefNurse2Name="";
								$table.="<b>Relief Nurse / Anesthesia:</b><br>";
								if($reliefNurse2){
									$getReliefNurse2Name=getUsrNm($reliefNurse2);
									$table.=$getReliefNurse2Name[0];   
								}else{
									$table.='_________';	
								}	
							$table.='
							</td>
						</tr>';
						if($signNurse2Id){
						$table.='	
						<tr>
							<td>&nbsp;</td>
							<td colspan="2">
								<b>Signature Date:&nbsp;</b>'.date("m-d-Y h:i A",strtotime($signNurse2DateTime)).'
							</td>
						</tr>';
						}
				$table.='							
					</table>
				</td>		
			</tr>
			
			<tr>
				<td class="bdrbtm" style="width:300px;">Confirmation of: identify, procedure, procedure site and consent(s)</td>
				<td class="bdrbtm cbold" style="text-align:center; vartical-align:top;">';
				if($confirmIPPSC_signin) { $table.=stripslashes($confirmIPPSC_signin); }else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm" style="text-align:left;width:300px;">Site marked by person performing the procedure</td>
				<td class="bdrbtm cbold"> ';
				if($siteMarked) { $table.=stripslashes($siteMarked); }else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="bdrbtm" style="width:300px;">Patient allergies</td>
				<td class="bdrbtm cbold" style="text-align:center;" >'; 
				if($patientAllergies) { $table.=stripslashes($patientAllergies); }else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm" style="text-align:left;width:290px;">Difficult airway or aspiration risk?</td>
				<td class="bdrbtm cbold" style="text-align:center;"> ';
				if($difficultAirway) { $table.=stripslashes($difficultAirway);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="bdrbtm" style="text-align:left; width:290px;">
					<table cellpadding="0" cellspacing="0" style="width:290px; font-size:13px;">	
						<tr>
							<td style="width:200px;">Risk of blood loss (>500 ml)</td>
						</tr>
						<tr>
							<td style="width:200px;padding-left:10px;">';
								if($riskBloodLoss=="Yes"){
									$table.='<b># of units available:&nbsp;</b>';
									$table.=htmlentities(stripslashes($bloodLossUnits)); 
									if(!$bloodLossUnits){$table.="____";}
								}else{ $table.="&nbsp;"; }
							$table.='
							</td>
						</tr>
					</table>
				</td>
				<td class="bdrbtm cbold" style="vertical-align:top;" > ';
				 if($riskBloodLoss) { $table.=stripslashes($riskBloodLoss);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm" style="vertical-align:top;" >Anesthesia safety check completed</td>
				<td class="bdrbtm cbold" style="vertical-align:top;">';
				 if($anesthesiaSafety) { $table.=stripslashes($anesthesiaSafety);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="pd bdrbtm bold" colspan="4" style="text-align:left;">Briefing:</td>
			</tr>
			<tr>
				<td class="bdrbtm" style="text-align:left;width:300px;">All members of the team have discussed care plan and addressed concerns</td>
				<td class="bdrbtm cbold" style="vertical-align:top;">';
				 if($allMembersTeam) { $table.=stripslashes($allMembersTeam);}else{$table.="__"; }
				$table.='</td>
				<td style="text-align:left; ">&nbsp;</td>
				<td style="text-align:center; ">&nbsp;</td>
			</tr>
			';
			}
			$table.='
			<tr>
				<td colspan="4" class="bgcolor pd bold" style="width:300px;vartical-align:bottom;">TIME-OUT</td>
			</tr>
			<tr>
				<td colspan="4" class="bold bdrbtm">Before Incision</td>
			</tr>
			<tr>
				<td class="bdrbtm" colspan="4" style="text-align:left; vertical-align:top;">
					<table style="width:700px;font-size:13px;" cellspacing="0" cellpadding="0">
						<tr>
							<td class="bold" style="width:240px; vertical-align:top;text-align:left">
								Initiated by designated team Member:&nbsp;
							</td>
							<td style="width:220px; vertical-align:top;text-align:left;">';
								if($signNurse3Id){
									$table.=$signNurse3LastName.", ".$signNurse3FirstName." ".$signNurse3MiddleName."<br><b>Electronically Signed:&nbsp;</b>Yes";
								
								}else {
									$table.='______________';
								}	
								$table.='
							</td>
							<td style="width:170px;padding-left:40px;vartical-align:top;text-align:right;">';
								$getReliefNurse3Name="";
								if($reliefNurse3){
									$getReliefNurse3Name=getUsrNm($reliefNurse3);
									$table.="<b>Relief Nurse / Anesthesia:</b><br>".$getReliefNurse3Name[0];   
								}	
								$table.='
							</td>
						</tr>';
					if($signNurse3Id){	
						$table.='	
						<tr>
							<td>&nbsp;</td>
							<td colspan="2"><b>Signature Date:&nbsp;</b>'.date("m-d-Y h:i A",strtotime($signNurse3DateTime)).'</td>
						</tr>';
					}
						$table.='		
					</table>	
				</td>
			</tr>
			<tr>
				<td class="bdrbtm">Introduction of team member</td>
				<td class="bdrbtm cbold">';
				if($introducationTeamMember) { $table.=stripslashes($introducationTeamMember);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">&nbsp;</td>
				<td class="bdrbtm">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4" class="bdrbtm bold">All:</td>
			</tr>
			<tr>
				<td class="bdrbtm" style="text-align:left; width:300px;">Confirmation of: identify, procedure, procedure site and consent(s)</td>
				<td class="bdrbtm cbold">';
				 if($confirmIPPSC) { $table.=stripslashes($confirmIPPSC);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Site is marked and visible</td>
				<td class="bdrbtm cbold">';
				if($siteMarkedAndVisible) { $table.=stripslashes($siteMarkedAndVisible);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="bdrbtm">Relevant Images properly labeled and displayed</td>
				<td class="bdrbtm cbold">';
				if($relevantImages) { $table.=stripslashes($relevantImages);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Any equipment concern?(Yes/No)</td>
				<td class="bdrbtm cbold">';
				 if($anyEquipmentConcern){ $table.=stripslashes($anyEquipmentConcern);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td colspan="4" class="bdrbtm bold">Anticipated Critical Events</td>
			</tr>
			<tr>
				<td colspan="4" class="bdrbtm bold">Surgeon:</td>
			</tr>
			<tr>
				<td colspan="4" class="bdrbtm bold">States the following:</td>
			</tr>
			<tr>
				<td class="bdrbtm">critical or nonroutine steps</td>
				<td class="bdrbtm cbold">';
				if($criticalStep) { $table.=stripslashes($criticalStep);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">case duration:</td>
				<td class="bdrbtm cbold">';
				if($caseDuration){ $table.=stripslashes($caseDuration);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="bdrbtm">anticipated blood loss</td>
				<td class="bdrbtm cbold">';
				if($anticipatedBloodLoss) { $table.=stripslashes($anticipatedBloodLoss);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">&nbsp;</td>
				<td class="bdrbtm">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4" class="bdrbtm bold">Anesthesia provider:</td>
			</tr>
			<tr>
				<td class="bdrbtm">Antibiotic prophylaxis within one hour before incision</td>
				<td class="bdrbtm cbold">';
				if($antibioticProphylaxis) { $table.=stripslashes($antibioticProphylaxis);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Additional concerns?</td>
				<td class="bdrbtm cbold">';
				 if($anesthesiaAdditionalConcerns) { $table.= stripslashes($anesthesiaAdditionalConcerns); }else{$table.="__"; } 
				$table.='</td>
			</tr>
			<tr> 
				<td colspan="4" class="bdrbtm">Scrub and circulating nurse:</td>
			</tr>
			<tr>
				<td class="bdrbtm">Sterilization Class 5 indicators have been confirmed</td>
				<td class="bdrbtm cbold">';
				 if($sterilizationIndicators) { $table.= stripslashes($sterilizationIndicators);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Additional concerns?</td>
				<td class="bdrbtm cbold">';
				if($nurseAdditionalConcerns) { $table.=stripslashes($nurseAdditionalConcerns);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td colspan="4" class="bgcolor bdrbtm bold">SIGN-OUT</td>
			</tr>
			<tr>
				<td colspan="4" class="bdrbtm bold">Before the Patient Leaves the operating Room</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align:left; vertical-align:top;">
					<table style="width:700px;" cellspacing="0" cellspacing="0">
						<tr>
							<td style="width:110px;" class="bdrbtm bold">Nurse confirms:&nbsp;</td>
							<td style="width:390px;" class="bdrbtm">';
								if($signNurse4Id){
									$table.=$signNurse4LastName.", ".$signNurse4FirstName." ".$signNurse4MiddleName;
								}else {
									$table.='______________';
								}
								if($signNurse4Id){
									$table.='<br><b>Electronically Signed:&nbsp;</b>Yes<br><b>Signature Date:&nbsp;</b>'.date("m-d-Y h:i A",strtotime($signNurse4DateTime));
								}
								$table.='
							</td>
							<td style="width:230px;" class="bdrbtm">';
								$getReliefNurse4Name="";
								$table.="<b>Relief Nurse / Anesthesia:</b><br>";
								if($reliefNurse4){
									$getReliefNurse4Name=getUsrNm($reliefNurse4);
									$table.=$getReliefNurse4Name[0];   
								}	
								$table.='
							</td>
						</tr>
					</table>
				</td>		
			</tr>
			<tr>
				<td  class="bdrbtm" style="width:290px;">Name of operative procedure Completion of sponge, sharp and instrument counts</td>
				<td  class="bdrbtm cbold">';
				if($nameOperativeProcedure) { $table.=stripslashes($nameOperativeProcedure);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">Specimens identified and labeled</td>
				<td class="bdrbtm cbold">';
				if($specimensIdentified) { $table.= stripslashes($specimensIdentified);}else{$table.="__"; }
				$table.='</td>
			</tr>
			<tr>
				<td class="bdrbtm">Any equipment problem to be addressed</td>
				<td class="bdrbtm cbold">';
				if($anyEquipmentProblem) { $table.= stripslashes($anyEquipmentProblem);}else{$table.="__"; }
				$table.='</td>
				<td colspan="2" class="bdrbtm bold">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4" class="bdrbtm"><b>Comments:&nbsp;</b>';
					$table.= htmlentities(stripslashes($comments)); 
					$table.='
				</td>
			</tr>
		</table>';
?>