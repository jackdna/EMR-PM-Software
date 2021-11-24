<?php
$table_print.='<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="4" class="fheader" style="width:744px">Laser Procedure Record</td>
	</tr>
	<tr>
		<td colspan="4" class="bold bgcolor pd">History</td>
	</tr>';
	$bgcolor='';//die('hlo '.$laser_chk_chief_complaint);
	if(($laser_chk_chief_complaint)||($laser_chk_past_med_hx)){
		$bgcolor="bgcolor";
		$table_print.='<tr>
			<td style="width:130px;" class="bold bdrbtm">Chief&nbsp;Complaint:&nbsp;</td>
			<td style="width:210px;" class="bdrbtm">';
				if(trim($laser_chief_complaint_detail)){
					$table_print.=trim($laser_chief_complaint_detail);
				}else{$table_print.="_____________";}
		$table_print.='</td>
			<td style="width:180px;text-align:right;" class="bold bdrbtm">Hx. of Present Illness:&nbsp;</td>
			<td style="width:180px;" class="bdrbtm">';
				if(trim($laser_present_illness_hx_detail)){
					$table_print.=trim($laser_present_illness_hx_detail);
				}else{$table_print.="_____________";}
		$table_print.='</td>
		</tr>
		<tr>
			<td style="width:110px;" class="bold bdrbtm">Past&nbsp;Medical&nbsp;Hx:&nbsp;</td>
			<td style="width:220px;" class="bdrbtm">';
				if(trim($laser_past_med_hx_detail)){
					$table_print.=trim($laser_past_med_hx_detail);
				}else{$table_print.="_____________";}
		$table_print.='</td>
			<td style="width:190px; text-align:left;" class="bold bdrbtm">Ocular Medication &amp; Dosage:&nbsp;</td>
			<td style="width:180px;" class="bdrbtm">';
				if(trim($laser_medication_detail)){
					$table_print.=trim($laser_medication_detail);
				}else{$table_print.="_____________";}
		$table_print.='</td>
		</tr>';
	}
	$table_print.='<tr>
		<td colspan="4" style="width:700px;" class="bold bdrbtm $bgcolor">Allergies/Drug Reaction&nbsp;&nbsp;___NKA&nbsp;&nbsp;Allergies Reviewed:';
		if($allergies_status_laser=="Yes"){
			$table_print.='Yes';
		}else{$table_print.='___';}
		$table_print.='&nbsp;&nbsp;&nbsp;&nbsp;Time Out</td>
	</tr>
	<tr>
		<td colspan="2" style="width:300px;">
			<table style="300px;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:220px; height:20px;padding-left:5px;" class="bold">Name</td>
					<td style="width:80px; height:20px;" class="bold">Reaction</td>
				</tr>
			</table>
		</td>
		<td style="width:220px; height:10px;" class="bold">Timeout</td>
		<td style="width:80px; height:10px; text-align:right; padding-right:40px;" class="bold">Time</td>
		
	</tr>
	<tr>
		<td colspan="2" style="width:330px;">
			<table style="300px; border:1px solid #C0C0C0;" cellpadding="2" cellspacing="0">';
			if($num_lp>0){
				while($fetchRows_procedure = imw_fetch_assoc($result_lp)){
					$table_print.='
					<tr>
						<td style="width:200px; padding:4px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$fetchRows_procedure['allergy_name'].'</td>
						<td style="width:80px; padding:4px;border-bottom:1px solid #C0C0C0;">'.$fetchRows_procedure['reaction_name'].'</td>
					</tr>';
				}
			}else{
				$table_print.='
				<tr>
					<td style="width:220px;height:20px;" class="bdrbtm">&nbsp;</td>
					<td style="width:80px;height:20px;" class="bdrbtm">&nbsp;</td>
				</tr>
				<tr>
					<td style="width:220px;height:20px;" class="bdrbtm">&nbsp;</td>
					<td style="width:80px;height:20px;" class="bdrbtm">&nbsp;</td>
				</tr>';
			}
			$table_print.='
			</table>
		</td>
		
		<td colspan="2" style="width:400px;" valign="top">
			<table style="400px; border:1px solid #C0C0C0;" cellpadding="2" cellspacing="0">
				<tr>
					<td style="width:320px;height:20px;" class="bdrbtm">i.&nbsp;Patient identification verified by&nbsp;'.($verified_nurseName ? $verified_nurseName : '___________').'</td>
					<td style="width:80px;height:20px;" class="bdrbtm">'.$objManageData->getTmFormat($verified_nurseTimeout ? $verified_nurseTimeout : '____').'</td>
				</tr>';
				$verifiedSite = "Site";
				if($verified_surgeonName!=""){  $verifiedSite = $laser_patientConfirmSiteTemp;}
				$table_print.='
				<tr>
					<td style="width:320px;height:20px;" class="bdrbtm">ii.&nbsp;'.$verifiedSite.' and Patient verified by&nbsp;'.($verified_surgeonName ? $verified_surgeonName : '___________').'</td>
					<td style="width:80px;height:20px;" class="bdrbtm">'.$objManageData->getTmFormat($verified_surgeonTimeout ? $verified_surgeonTimeout : '____').'</td>
				</tr>
				<tr>
					<td colspan="2" class="bdrbtm" style="width:400px;">
						<table style="width:400px;" cellpadding="0" cellspacing="0">	
							<tr>
								<td style="width:120px;">Surgery Start Time</td>
								<td style="width:80px;white-space:nowrap;">';
								if(trim($proc_start_time)){
									$table_print.=trim($objManageData->getTmFormat($proc_start_time));
								}else{$table_print.="______";}
								$table_print.='
								</td>
								<td style="width:120px;">Surgery End Time</td>
								<td style="width:80px;white-space:nowrap;">';
								if(trim($proc_end_time)){
									$table_print.=trim($objManageData->getTmFormat($proc_end_time));
								}else{$table_print.="______";}
								$table_print.='
								</td>
							</tr>
						</table>
					</td>
				</tr>								
			</table>
		</td>
		
	</tr>';
if($stable_chbx=="Yes" || ($stable_other_chbx=="Yes" && trim($stable_other_txtbx)!="") 
		|| $best_correction_vision_R || $best_correction_vision_L 
		|| $glare_acuity_R || $glare_acuity_L || trim($asa_status) || trim($laser_other)){
		$table_print.='
	<tr>
		<td colspan="4" style="width:700px;height:20px;" class="bgcolor">
			<table style="width:700px;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:150px;" class="bold">Medical Evaluation</td>
					<td style="width:550px;">Patient reported medical status:&nbsp;&nbsp;
					&nbsp;Stable. no acute illness&nbsp;';
					if($stable_chbx=="Yes"){
						$table_print.="<b>".$stable_chbx."</b>";
					}else{$table_print.="____&nbsp;";}
					$table_print.="&nbsp;&nbsp;Other:&nbsp;";
					if($stable_other_chbx=="Yes" && trim($stable_other_txtbx)!=""){
						$table_print.="<b>".trim($stable_other_txtbx)."</b>";	
					}
					$table_print.='</td>
				</tr>
			</table>
		</td>
	</tr>';
		if($best_correction_vision_R || $best_correction_vision_L 
			|| $glare_acuity_R || $glare_acuity_L || trim($asa_status) || trim($laser_other)){
			$table_print.='
	
	<tr>
		<td colspan="4" style="height:20px; vertical-align:middle;" class="bdrbtm">
			<table style="width:700px;" cellpadding="0" cellspacing="0">	
				<tr>
					<td style="width:150px;">Best Corrected Vision</td>
					<td style="width:100px;" class="bold">R&nbsp;20 /';
					if($best_correction_vision_R){
						$table_print.=$best_correction_vision_R;
					}else{$table_print.="_____";}
					$table_print.='</td>
					<td style="width:100px;" class="bold">L&nbsp;20 /';
					if($best_correction_vision_L){
						$table_print.=$best_correction_vision_L;
					}else{$table_print.="_____";}
					$table_print.='</td>
					<td style="width:150px;text-align:center;">Glare Acuity</td>
					<td style="width:100px;" class="bold">R&nbsp;20 /';
					if($glare_acuity_R){
						$table_print.=$glare_acuity_R;
					}else{$table_print.="_____";}
					$table_print.='</td>
					<td style="width:100px;" class="bold">L&nbsp;20 /';
					if($glare_acuity_L){
						$table_print.=$glare_acuity_L;
					}else{$table_print.="_____";}
					$table_print.='</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="4" style="height:20px; vertical-align:middle;" class="bdrbtm">
			<table style="width:700px;" cellpadding="0" cellspacing="0">	
				<tr>
					<td style="width:70px;">ASA</td>
					<td style="width:130px;" class="bold">';
					if($asa_status){
						$table_print.=$asa_status;
					}else{$table_print.="_____";}
					$table_print.='</td>
					<td style="width:50px;" class="bold bdrbtm">Other:</td>
					<td  style="width:450px;" class="bdrbtm">';
						if(trim($laser_other)){
							$table_print.=trim($laser_other);
						}else{$table_print.="_____________";}
				$table_print.='</td>
				</tr>
			</table>
		</td>
	</tr>';
		}
	}
	$table_print.='
	<tr>
		<td style="width:110px;height:20px;" class="bgcolor bold bdrbtm">Pre Op Orders</td>
		<td colspan="2" style="width:410px;height:20px;" class="cbold bdrbtm">
			On arrival the following drops will be given to the '.$laser_patientConfirmSiteTemp.'
		</td>
		<td style="width:180px;height:20px;" class="bgcolor bdrbtm">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4" style="height:20px;" class="cbold">List of Pre-OP Medication Orders</td>
	</tr>
	<tr>
		<td colspan="4" style="width:600px;padding-bottom:5px;">
			<table style="width:600px;font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:230px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Medication</td>
					<td style="width:100px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Strength</td>
					<td style="width:100px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Directions</td>
					<td style="width:230px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Time</td>
				</tr>';
				if(count($preOpPatientDetails)>0){
				foreach($preOpPatientDetails as $detailsOfMedication){
					$parientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
					$preDefined = $detailsOfMedication->medicationName;
					$strength = $detailsOfMedication->strength;
					$directions = $detailsOfMedication->direction;
					$timemeds[0] = $objManageData->getTmFormat($detailsOfMedication->timemeds);
					
					$timemeds[1] = $objManageData->getTmFormat($detailsOfMedication->timemeds1);
					$timemeds[2] = $objManageData->getTmFormat($detailsOfMedication->timemeds2);
					$timemeds[3] = $objManageData->getTmFormat($detailsOfMedication->timemeds3);
					$timemeds[4] = $objManageData->getTmFormat($detailsOfMedication->timemeds4);
					$timemeds[5] = $objManageData->getTmFormat( $detailsOfMedication->timemeds5);
					$timemeds[6] = $objManageData->getTmFormat($detailsOfMedication->timemeds6);
					$timemeds[7] = $objManageData->getTmFormat($detailsOfMedication->timemeds7);
					$timemeds[8] = $objManageData->getTmFormat($detailsOfMedication->timemeds8);
					$timemeds[9] = $objManageData->getTmFormat($detailsOfMedication->timemeds9);

					++$k;
					if($k==1){
						$disptr='block';
					}else{
						$disptr='none';
					}
				
					$dir  = explode('X',strtoupper($directions));
					$freq = substr(trim($dir[1]),0,1);
					$freq = $freq > 6 ? 6 : $freq;
					$minsDir = explode('Q',strtoupper($dir[1]));
					if(count($minsDir)<=1) $freq = '';
					$min=substr(trim($minsDir[1]),0,-3);
				
					
				 $table_print.='<tr>
				  <td style="padding:5px;width:230px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($preDefined).'</td>
				  <td style="padding:5px;width:100px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($strength).'</td>
				  <td style="padding:5px;width:100px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($directions).'</td>';
						 
				$table_print.='<td style="padding-left:3px;width:240px; border-bottom:1px solid #C0C0C0;vertical-align:top;">';		
				for($t=0;$t<=9;$t++)	
				{
					if($timemeds[$t]!=''){
						$table_print.=$timemeds[$t]."&nbsp;&nbsp;";
					}
					if($t==2){
						$table_print.="<br>";
					}
				}
				$table_print.='</td>
						</tr>'; 
			}
		}else{
			$table_print.='<tr>
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
						</tr>';
			}
		$table_print.='</table>
			</td>	
		</tr>
		<tr>
			<td style="width:110px;" class="bdrbtm bold">Other Pre-Op Orders:&nbsp;</td>
			<td style="width:220px;" class="bdrbtm">';
			if($laser_other_pre_medication!=''){
				$table_print.=$laser_other_pre_medication;
			}else{
				$table_print.="________";
			}
		$table_print.='
			</td>
			<td style="width:190px;text-align:right;" class="bold bdrbtm">Comments:&nbsp;</td>
			<td style="width:180px;" class="bdrbtm">';
			if($laser_comments!=''){
				$table_print.=$laser_comments;	
			}
		$table_print.='</td>
		</tr>
		<tr>
			<td colspan="4" style="width:700px;" class="bdrbtm"><b>Pre-Op Diagnosis:&nbsp;</b>';
			if(trim($laser_pre_op_diagnosis)){
				$table_print.=trim($laser_pre_op_diagnosis);
			}
			$table_print.='</td>
		</tr>
		<tr>
			<td style="width:110px;height:20px;" class="bold bdrbtm">Procedure Notes</td>
			<td colspan="3" style="width:500px;height:20px;" class="cbold bdrbtm">
				Patient in satisfactory condition for proposed laser procedure:&nbsp;';
				if($chk_laser_patient_evaluated=="Yes"){
					$table_print.=$chk_laser_patient_evaluated;
				}
			$table_print.='
			</td>
		</tr>
		<tr>
			<td colspan="4" style="width:700px;" class="bdrbtm">
				<table style="width:700px;" cellpadding="0" cellspacing="0">
					<tr>	
						<td style="width:140px;white-space:nowrap;">PreLaser Vital Signs</td>
						<td style="width:60px;white-space:nowrap;"><b>BP</b>&nbsp;';
							if($prelaserVitalSignBP){
								$table_print.=$prelaserVitalSignBP;							
							}else{$table_print.="_______";}
						$table_print.='</td>
						<td style="width:60px;white-space:nowrap;"><b>P</b>&nbsp;';
							if($prelaserVitalSignP){
								$table_print.=$prelaserVitalSignP;							
							}else{$table_print.="_______";}
						$table_print.='</td>
						<td style="width:60px;white-space:nowrap;"><b>R</b>&nbsp;';
							if($prelaserVitalSignR){
								$table_print.=$prelaserVitalSignR;							
							}else{$table_print.="_______";}
						$table_print.='</td>
						<td style="width:110px;white-space:nowrap;"><b>Time</b>&nbsp;';
							if($prelaserVitalSignTime){
								$table_print.=$objManageData->getTmFormat($prelaserVitalSignTime);
							}else{$table_print.="_______";}
						$table_print.='</td>
						<td style="width:110px;text-align:center;">PreLaser IOP</td>
						<td style="width:50px;padding-left:3px;"><b>R</b>&nbsp;';
						if($pre_laser_IOP_R!='' && $pre_laser_IOP_na==''){
							$table_print.=$pre_laser_IOP_R;
						}else{$table_print.="____";}
						
						$table_print.='</td>
						<td style="width:50px;padding-left:3px;"><b>L</b>&nbsp;';
						if($pre_laser_IOP_L!='' && $pre_laser_IOP_na==''){
							$table_print.=$pre_laser_IOP_L;
						}else{$table_print.="____";}
						$table_print.='</td>
						<td style="width:60px;padding-left:3px;"><b>N/A</b>&nbsp;';
						if($pre_iop_na!=''){
							$table_print.=$pre_iop_na;
						}else{$table_print.="____";}
						$table_print.='</td>
					</tr>
				</table>
			</td>			
		</tr>';
		
		if($version_num_laser_proc > 1) {
			$table_print.='
		<tr>
			<td colspan="4" class="bdrbtm" style="width:740px;">
				<table style="width:740px;" cellpadding="0" cellspacing="0">
					<tr>	
						<td style="width:250px;white-space:nowrap;" class="bdrbtm"><b>Patient Discharged to Home With</b></td>
						<td style="width:100px;white-space:nowrap;text-align:right;border-right:1px solid #C0C0C0;" class="bdrbtm">'.($discharge_home?'Yes':'_____').'</td>
						<td style="width:290px;white-space:nowrap;" class="bdrbtm"><b>Patient Transferred to Hospital</b></td>
						<td style="width:100px;white-space:nowrap;text-align:right;" class="bdrbtm">'.($patient_transfer?'Yes':'_____').'</td>
					</tr>
					<tr>';
					if( $patients_relation == 'other' )	{
						$table_print.='<td colspan="2" style="width:350px;white-space:nowrap;border-right:1px solid #C0C0C0;"><b>Relationship: </b>'.($patients_relation_other?$patients_relation_other:'___________________').'</td>';
					}
					else {
						$table_print.=' 
						<td style="width:250px;white-space:nowrap;" ><b>Relationship</b></td>
						<td style="width:100px;white-space:nowrap;text-align:right;border-right:1px solid #C0C0C0;" >'.($patients_relation?$patients_relation:'_____').'</td>';
					}	
					$table_print.='<td style="width:290px;white-space:nowrap;" ><b>Discharge Time</b></td>
						<td style="width:100px;white-space:nowrap;text-align:right;">'.($discharge_time?$discharge_time:'_____').'</td>
					</tr>

				</table>		
			</td>
		</tr>';
		}
		$laserEyeSiteLabel='';
		if($Confirm_patientHeaderSite){$laserEyeSiteLabel = ' for '.strtolower($Confirm_patientHeaderSite);}
		if(($laser_chk_spot_duration=='on')||($laser_chk_spot_size=='on')||($laser_chk_power=='on')||
			($laser_chk_shots=='on')|| ($laser_chk_total_energy=='on')||($laser_chk_degree_of_opening=='on')||
			($laser_chk_exposure=='on')||($laser_chk_count=='on')){
			$table_print.='<tr>
			<td colspan="2" class="bdrbtm" style="width:330px;vertical-align:top;">
				<table style="width:330px;border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2" style="width:330px;" class="bdrbtm bgcolor cbold ">Laser Notes '.$laserEyeSiteLabel.'</td>
					</tr>';
					if($laser_chk_spot_duration=='on'){	
						$table_print.='
						<tr>
							<td style="width:90px;" class="bdrbtm bold pd">Spot Duration:</td>
							<td style="width:215px; " class="bdrbtm pd">';
							if(trim($laser_spot_duration_detail)){
								$table_print.=stripslashes($laser_spot_duration_detail);
							}else{
								$table_print.="_________";
							}
							$table_print.='</td>
						</tr>';
					}
					if($laser_chk_spot_size=='on'){
						$table_print.='
						<tr>
							<td style="width:90px;" class="bdrbtm bold pd">Spot Size:</td>
							<td style="width:215px;" class="bdrbtm pd">';
							if(trim($laser_spot_size_detail)){
								$table_print.=stripslashes($laser_spot_size_detail);
							}else{
								$table_print.="_________";
							}
							$table_print.='</td>
						</tr>';
					}
					if($laser_chk_power!=''){
						$table_print.='
						<tr>
							<td style="width:90px;" class="bdrbtm bold pd">Power:</td>
							<td style="width:215px;" class="bdrbtm pd">';
							if(trim($laser_chk_power!='')){
								$table_print.=stripslashes($laser_power_detail);
							}else{
								$table_print.="_________";
							}
							$table_print.='</td>
						</tr>';
					}
					if($laser_chk_shots!=''){
						$table_print.='
						<tr>
							<td style="width:90px;" class="bdrbtm bold pd"># of Shots:</td>
							<td style="width:215px;" class="bdrbtm pd">';
							if($laser_chk_shots!=''){
								$table_print.=stripslashes($laser_shots_detail);
							}else{
								$table_print.="_________";
							}
							$table_print.='</td>
						</tr>';
					}
					if($laser_chk_total_energy=='on'){
						$table_print.='
						<tr>
							<td style="width:90px;" class="bdrbtm bold pd">Total Energy:</td>
							<td style="width:215px;" class="bdrbtm pd">';
							if(trim($laser_total_energy_detail)){
								$table_print.=stripslashes($laser_total_energy_detail);
							}else{
								$table_print.="_________";
							}
							$table_print.='</td>
						</tr>';
					}
					if($laser_chk_degree_of_opening=='on'){
						$table_print.='
						<tr>
							<td style="width:90px;" class="bdrbtm bold pd">Degree of opening:</td>
							<td style="width:215px;" class="bdrbtm pd">';
							if(trim($laser_degree_of_opening_detail)){
								$table_print.=stripslashes($laser_degree_of_opening_detail);
							}else{
								$table_print.="_________";
							}
							$table_print.='</td>
						</tr>';
					}
					if($laser_chk_exposure=='on'){
						$table_print.='
						<tr>
							<td style="width:90px;" class="bdrbtm bold pd">Exposure:</td>
							<td style="width:215px;" class="bdrbtm pd">';
							if(trim($laser_exposure_detail)){
								$table_print.=stripslashes($laser_exposure_detail);
							}else{
								$table_print.="_________";
							}
							$table_print.='</td>
						</tr>';
					}
					if(trim($laser_count_detail)){
						$table_print.='
						<tr>
							<td style="width:90px;" class="bdrbtm bold pd">Count:</td>
							<td style="width:215px;" class="bdrbtm pd">';
							if($laser_chk_count!=''){
								$table_print.=stripslashes($laser_count_detail);
							}else{
								$table_print.="_________";
							}
							$table_print.='</td>
						</tr>';
					}
				$table_print.='
				</table>
			</td>
			<td colspan="2" class="bdrbtm" style="width:370px;">';
				if($laser_procedure_image_path!=""){
					 if(file_exists("admin/".$laser_procedure_image_path)){
						 $table_print.='
						<table style="width:370px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:370px;" class="cbold">Drawing Data</td>
							</tr>
							 <tr>		
								<td  style="width:370px;">';
									$imwLaserImageDir = "ascemr_images";
									$imwLaserDrawingFolder = $objManageData->getImedicDirPath($imwPatientIdLaser,$imwLaserImageDir);
									if($iDocOpNoteSave == "yes") {
										//$imwLaserImageName = str_ireplace("pdfFiles/laser_drawing_images/","",$laser_procedure_image_path);
										$imwLaserImageName = "laser_image_".$_REQUEST['pConfId']."_".$patient_id.'.jpg';
										$laserDrawingContent = file_get_contents("admin/".$laser_procedure_image_path);
										$imwLaserDrawingPath = $imwLaserDrawingFolder."/".$imwLaserImageName;
										file_put_contents($imwLaserDrawingPath,$laserDrawingContent);
										$imwLaserImgSrcPath = "/".$imwDirectoryName."/interface/main/uploaddir/PatientId_".$imwPatientIdLaser."/".$imwLaserImageDir."/".$imwLaserImageName;
										if(trim($imwPracticeName)) {
											$imwLaserImgSrcPath = "../../data/".$imwPracticeName."/PatientId_".$imwPatientIdLaser."/".$imwLaserImageDir."/".$imwLaserImageName;
										}
										$table_print.='<img src="'.$imwLaserImgSrcPath.'" width="370" height="174">';
									}else {
										$table_print.='<img src="../admin/'.$laser_procedure_image_path.'" width="370" height="174">';
									}
								$table_print.='
								</td>
							</tr>
						</table>';
					 }
				}else if($laser_procedure_image!=""){
					 $laserid=$_REQUEST['pConfId'];
					 $lasertable = 'laser_procedure_patient_table';
					 $laseridName = 'confirmation_id';
					 $laserdocSign = 'laser_procedure_image';
					 $qry = "select laser_procedure_image from laser_procedure_patient_table where confirmation_id = $laserid";
					 $pixleResPat = imw_query($qry);
					 list($laser_procedure_image) = imw_fetch_array($pixleResPat);
					 require_once("html2pdfnew/imgGdLaser.php");
					 drawOnImageLaser($laser_procedure_image,$imgName,'laserProcedure_Image.jpg');
					 $table_print.='
							<table style="width:370px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:370px;" class="cbold">Drawing Data</td>
								</tr>
								 <tr>		
									<td  style="width:370px;">';
									if(file_exists('html2pdfnew/laserProcedure_Image.jpg')){
										$table_print.='<img src="../html2pdfnew/laserProcedure_Image.jpg" width="370" height="174">';
									}
									$table_print.='
									</td>
								</tr>
							</table>';
						}
					$table_print.='
					</td>
				</tr>';
			}
		$table_print.='<tr>
			<td colspan="4" class="bdrbtm" style="width:700px;">
				<table style="width:700px;" cellpadding="0" cellspacing="0">	
					<tr>
						<td style="width:140px;">PostLaser Vitial Signs</td>
						<td style="width:60px;white-space:nowrap;"><b>BP</b>&nbsp;';
						if(trim($postlaserVitalSignBP)){
							$table_print.=trim($postlaserVitalSignBP);
						}else{$table_print.="______";}
						$table_print.='</td>
						<td style="width:60px;"><b>P</b>&nbsp;';
						if(trim($postlaserVitalSignP)){
							$table_print.=trim($postlaserVitalSignP);
						}else{$table_print.="______";}
						$table_print.='</td>
						<td style="width:60px;"><b>R</b>&nbsp;';
						if(trim($postlaserVitalSignR)){
							$table_print.=trim($postlaserVitalSignR);
						}else{$table_print.="______";}
						$table_print.='</td>
						<td style="width:110px;white-space:nowrap;"><b>Time</b>&nbsp;';
							if($postlaserVitalSignTime){
								$table_print.=$objManageData->getTmFormat($postlaserVitalSignTime);
							}else{$table_print.="_______";}
						$table_print.='</td>
						<td style="width:110px; text-align:center;">IOP Pressure</td>
						
						<td style="width:50px;padding-left:3px;"><b>R</b>&nbsp;';
						if(trim($iop_pressure_r) && ($iop_pressure_na=='')){
							$table_print.=trim($iop_pressure_r);
						}else{$table_print.="____";}
						$table_print.='</td>
						<td style="width:50px;padding-left:3px;"><b>L</b>&nbsp;';
						if(trim($iop_pressure_l) && ($iop_pressure_na=='')){
							$table_print.=trim($iop_pressure_l);
						}else{$table_print.="____";}
						$table_print.='</td>
						<td style="width:60px;"><b>N/A</b>&nbsp;';
						if($iop_pressure_na!=''){
							$table_print.=$iop_pressure_na;
						}else{$table_print.="____";}
						$table_print.='</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="bdrbtm"  style="width:520px;">
				<table style="width:330px;" cellpadding="0" cellspacing="0">
					<tr>
						<td class="bdrbtm" style="width:110px;"><b>Post Op Orders:</b></td>';
						$table_print.='
						<td class="bdrbtm" style="width:410px;border-right:1px solid #C0C0C0;">';
						if(trim($laser_post_progress_detail)){
							$table_print.=trim($laser_post_progress_detail);
						}else{$table_print.="_______";}
						$table_print.='</td>
					</tr>
					<tr>
						<td style="width:110px;"><b>Progress Note:</b></td>';
						$table_print.='
						<td style="width:410px;border-right:1px solid #C0C0C0;">';
						if(trim($laser_post_operative_detail)){
							$table_print.=trim($laser_post_operative_detail);
						}else{$table_print.="_______";}
						$table_print.='</td>
					</tr>
				</table>
			</td>
			<td class="bdrbtm" style="width:180px;">
				<b>Comments:&nbsp;</b>';
				if($post_comment){
					$table_print.=$post_comment;
				}else{
					$table_print.="_______";
				}
			$table_print.='
			</td>
		</tr>
		<tr>
			<td colspan="2"  class="pd">';
			if($signSurgeon1Status=="Yes"){	
				$table_print.='
					<b>Surgeon:&nbsp;</b> Dr '.stripslashes($signSurgeonName).'
					<br><b>Electronically Signed:&nbsp;</b>Yes
					<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($ViewlaserprocedureRow['signSurgeon1DateTime']);
					
			}else{
				$table_print.='
					<b>Surgeon:&nbsp;</b>________
					<br><b>Electronically Signed:&nbsp;</b>________
					<br><b>Signature Date:&nbsp;</b>________';
			}
			$table_print.='</td>
			<td colspan="2" style="padding-left:100px;">';
			if($signNurseStatus=="Yes"){	
				$table_print.='
					<b>Nurse:&nbsp;</b>'.stripslashes($signNurseName).'
					<br><b>Electronically Signed:&nbsp;</b>Yes
					<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($ViewlaserprocedureRow['signNurseDateTime']);
					
			}else{
				$table_print.='
					<b>Nurse:&nbsp;</b>________
					<br><b>Electronically Signed:&nbsp;</b>________
					<br><b>Signature Date:&nbsp;</b>________';
			}
			$table_print.='</td>
		</tr>
	</table>';
?>