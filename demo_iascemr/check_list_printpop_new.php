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
			</tr>';
			
$table.='			
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
								<b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signNurse1DateTime);
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
				<td class="bdrbtm">Any special equipment, devices, implants</td>
				<td class="cbold bdrbtm"> ';
				if($anySpecialEquipment) { $table.=stripslashes($anySpecialEquipment); }else{$table.="__"; }
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
								<b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signNurse2DateTime).'
							</td>
						</tr>';
						}
				$table.='							
					</table>
				</td>		
			</tr>
			
			<tr>
				<td class="bdrbtm" style="width:300px;">Confirmation of: identify, procedure, procedure site and consent(s)</td>
				<td class="bdrbtm cbold" style="text-align:center; vertical-align:top;">';
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
					&nbsp;
				</td>
				<td class="bdrbtm cbold" style="vertical-align:top;" >&nbsp; ';
				 
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
			</tr>';
			}
			$table.='
			<tr>
				<td colspan="4" class="bgcolor pd bold" style="width:300px;vartical-align:bottom;">TIME-OUT</td>
			</tr>';
			
			if($fire_risk_active_status == "Yes") {
				
				if($fire_risk_score == '1')
				{
					$fire_risk_score	.=	' (Low Risk)';
				}
				elseif($fire_risk_score == '2')
				{
					$fire_risk_score	.=	' (Low Risk w/Potential to convert)';
				}
				elseif($fire_risk_score == '3')
				{
					$fire_risk_score	.=	' (High Risk)';
				}
				else
				{
					$fire_risk_score	=	'';	
				}
				$table.='			
						<tr>
							<td class="pd bold bdrbtm" colspan="4" style="width:700px;">Fire Risk Assessment Guide</td>
						</tr>
						<tr>
							<td class="bdrbtm"  style="text-align:left;">Surgical Site Above Xiphoid (incision above waist)</td>
							<td class="cbold bdrbtm" style="width:50px;">';
								if($surgical_xiphoid){ $table.= stripslashes($surgical_xiphoid); }else{$table.="__"; }
							$table.='</td>
							<td class="bdrbtm" style="text-align:left;width:290px;">Open Oxygen Source (nasal cannula, oxygen face mask)</td>
							<td class="cbold bdrbtm" style="width:50px;">';
							 if($oxygen_source){ $table.=stripslashes($oxygen_source); }else{$table.="__";}
							$table.=' </td>
						</tr>
						<tr>
							<td class="bdrbtm"  style="text-align:left;" colspan="2">Fire Risk Score: <b>'.
								($fire_risk_score ? stripslashes($fire_risk_score) : "____").'
							</b></td>
							<td class="bdrbtm" style="text-align:left;width:290px;">Available Ignition Source (cautery, laser, fiber optic light source)</td>
							<td class="cbold bdrbtm" style="width:50px;">';
							 if($ignition_source){ $table.=stripslashes($ignition_source); }else{$table.="__";}
							$table.=' </td>
						</tr>			
						
						';
					
			}			
			$table.='
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
							<td colspan="2"><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signNurse3DateTime).'</td>
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
				<td colspan="4" class="bdrbtm bold">Anticipated Critical Events</td>
			</tr>
			
			<tr>
				<td colspan="4" class="bdrbtm bold">Anesthesia provider:</td>
			</tr>
			<tr>
				<td class="bdrbtm">Antibiotic prophylaxis within one hour before incision</td>
				<td class="bdrbtm cbold">';
				if($antibioticProphylaxis) { $table.=stripslashes($antibioticProphylaxis);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">&nbsp;</td>
				<td class="bdrbtm cbold">&nbsp;</td>
			</tr>
			<tr> 
				<td colspan="4" class="bdrbtm">Scrub and circulating nurse:</td>
			</tr>
			<tr>
				<td class="bdrbtm">Sterilization Class 5 indicators have been confirmed</td>
				<td class="bdrbtm cbold">';
				 if($sterilizationIndicators) { $table.= stripslashes($sterilizationIndicators);}else{$table.="__"; }
				$table.='</td>
				<td class="bdrbtm">&nbsp;</td>
				<td class="bdrbtm cbold">&nbsp;</td>
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
									$table.='<br><b>Electronically Signed:&nbsp;</b>Yes<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signNurse4DateTime);
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
				<td class="bdrbtm" style="width:290px;">Specimens identified and labeled</td>
				<td class="bdrbtm cbold">';
				if($specimensIdentified) { $table.= stripslashes($specimensIdentified);}else{$table.="__"; }
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