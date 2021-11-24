<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
//print_r($_REQUEST);
extract($_REQUEST);

?>
<html>
<head>
<title>Pending chart notes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
</head>
  <?php 
  	
  ?>
  
<body  bgcolor="#ECF1EA" style="margin:0px;">
<form action="" name="pandingCharts" method="post">
	<table cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#ECF1EA">
		<tr>
			<td bgcolor="#FBD78D" valign="top" align="center" colspan="8" class="text_homeb">Pending chart notes</td>
		</tr>
		<tr>
			<td width="10%"  class="text_homeb">Patient name:</td>
			<td colspan="7" align="center" class="text_homeb">Pending Chart Note Name</td>
		</tr>
	</table>
	<div id="pandingChart" style="width:980px; height:530px; overflow:auto; "> 
		<table cellpadding="3" cellspacing="0" width="100%" border="0" bgcolor="#ECF1EA">
		<?php
		if($surgeonID){
			$patientConfQuery = "select patientConfirmationId,patientId  from patientconfirmation where surgeonId = '".$surgeonID."' and dos = '$dos'"; 
			$patientConfRes = imw_query($patientConfQuery) or die($patientConfQuery.imw_error());
			$patientConfNumRow = imw_num_rows($patientConfRes);
			while($patientConfRow = imw_fetch_array($patientConfRes)) {
				?>	
					<tr>
				<?php
				$patientId = $patientConfRow['patientId'];
				$patientConfId = $patientConfRow['patientConfirmationId'];
				
				$patientStubIdQuery = "select stub_id from stub_tbl where patient_confirmation_id = '".$patientConfId."'"; 
				$patientStubIdRes = imw_query($patientStubIdQuery) or die($patientStubIdQuery.imw_error());
				extract(imw_fetch_array($patientStubIdRes));
			
				$patDataTblQuery = "select patient_fname,patient_mname,patient_lname from patient_data_tbl where patient_id  = ".$patientId; 			
				$patDataTblRes = imw_query($patDataTblQuery) or die($patDataTblQuery.imw_error());
				extract(imw_fetch_array($patDataTblRes));
				?>	
					<td nowrap class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td>
				<?php			
				////////////////
				$chkConsentSignAllQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$patientConfId."' AND signSurgeon1Activate='yes' AND consent_purge_status!='true'";
				$chkConsentSignAllRes = imw_query($chkConsentSignAllQry) or die($chkConsentSignAllQry.imw_error());
				$chkConsentAllSignNumRow = imw_num_rows($chkConsentSignAllRes);
				if($chkConsentAllSignNumRow>0) {
					$chkConsentSignSurgeon1Activate='';
					while($chkConsentSignRow=imw_fetch_array($chkConsentSignAllRes)) {
						$chkConsentFormName = $chkConsentSignRow['surgery_consent_name'];
						$chkConsentSignSurgeon1Activate=$chkConsentSignRow['signSurgeon1Activate'];
						$chkConsentSignSurgeon1Id=$chkConsentSignRow['signSurgeon1Id'];
						$chkConsentFrmFormStatus=$chkConsentSignRow['form_status'];
						$surgeryConsentId = $chkConsentSignRow['surgery_consent_id'];
						$consentTemplateId = $chkConsentSignRow['consent_template_id'];
						if($chkConsentFrmFormStatus == 'not completed' and $chkConsentSignSurgeon1Id == 0){
							?>
							<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=consent_multiple_form.php&consentMultipleId=<?php echo $consentTemplateId; ?>&consentMultipleAutoIncrId=<?php echo $surgeryConsentId; ?>','ChartNote<?php echo $stub_id; ?>');"><?php echo $chkConsentFormName; ?></a></td>					
							<?php
						}
					}
				}
				///////////
				$preOpPhyQuery = "select form_status,signSurgeon1Id from preopphysicianorders where patient_confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
				$preOpPhyNumRow = imw_num_rows($preOpPhyRes);
				if($preOpPhyNumRow){		
					extract(imw_fetch_array($preOpPhyRes));
					//if($form_status=='not completed' and $signSurgeon1Id==0){	
					if($form_status=='not completed'){	
					?>
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=pre_op_physician_orders.php','ChartNote<?php echo $stub_id; ?>');">Pre-Op Physician Orders</a></td>					
						<!-- <td width="15%" align="left" class="text_print link_slid_right">Pre-Op Physician Orders</td> -->					
					<?php
					}
				}	
				$preOpPhyQuery = "select form_status,signSurgeon1Id from postopphysicianorders where patient_confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
				$preOpPhyNumRow = imw_num_rows($preOpPhyRes);			
				if($preOpPhyNumRow){		
					extract(imw_fetch_array($preOpPhyRes));
					//if($form_status=='not completed' and $signSurgeon1Id==0){	
					if($form_status=='not completed'){	
					?>
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=post_op_physician_orders.php','ChartNote<?php echo $stub_id; ?>');">Post-Op Physician Orders</a></td>					
						<!-- <td nowrap align="left" class="text_print link_slid_right">Post-Op Physician Orders</td> -->					
					<?php
					}
				}	
				$opRoomQuery = "select form_status,verifiedbySurgeon from operatingroomrecords where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$opRoomRes = imw_query($opRoomQuery) or die($opRoomQuery.imw_error());
				$opRoomNumRow = imw_num_rows($opRoomRes);
				if($opRoomNumRow){		
					extract(imw_fetch_array($opRoomRes));
					//if($form_status=='not completed' and $verifiedbySurgeon==""){	
					if($form_status=='not completed'){	
					?>
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=op_room_record.php','ChartNote<?php echo $stub_id; ?>');">Operating Room Record</a></td>					
						<!-- <td nowrap align="left" class="text_print link_slid_right">Operating Room Record</td>	 -->				
					<?php
					}
				}				
				$laserProQuery = "select form_status,signSurgeon1Id from laser_procedure_patient_table where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$laserProRes = imw_query($laserProQuery) or die($laserProQuery.imw_error());
				$laserProNumRow = imw_num_rows($laserProRes);
				if($laserProNumRow){		
					extract(imw_fetch_array($laserProRes));
					//if($form_status=='not completed' and $signSurgeon1Id==0){	
					if($form_status=='not completed'){	
					?>
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=laser_procedure.php','ChartNote<?php echo $stub_id; ?>');">Laser Procedure</a></td>					
						<!-- <td nowrap align="left" class="text_print link_slid_right">Laser Procedure</td> -->
					<?php
					}
				}
				$opretiveReportQuery = "select form_status,signSurgeon1Id from operativereport where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$opretiveReportRes = imw_query($opretiveReportQuery) or die($opretiveReportQuery.imw_error());
				$opretiveReportNumRow = imw_num_rows($opretiveReportRes);
				if($opretiveReportNumRow){		
					extract(imw_fetch_array($opretiveReportRes));
					//if($form_status=='not completed' and $signSurgeon1Id==0){	
					if($form_status=='not completed'){	
					?>					
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=operative_record.php','ChartNote<?php echo $stub_id; ?>');">Operative Report</a></td>					
						<!-- <td nowrap align="left" class="text_print link_slid_right">Operative Report</td> -->
					<?php
					}
				}
				$disSumSheetQuery = "select form_status,signSurgeon1Id from dischargesummarysheet where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$disSumSheetRes = imw_query($disSumSheetQuery) or die($disSumSheetQuery.imw_error());
				$disSumSheetNumRow = imw_num_rows($disSumSheetRes);
				if($disSumSheetNumRow){		
					extract(imw_fetch_array($disSumSheetRes));
					if($form_status=='not completed'){					
					?>					
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=discharge_summary_sheet.php','ChartNote<?php echo $stub_id; ?>');">Discharge Summary Sheet</a></td>					
					<?php
					}
				}	
				$instructionSheetQuery = "select form_status,signSurgeon1Id,signSurgeon1Activate from patient_instruction_sheet where patient_confirmation_id = ".$patientConfId." and signSurgeon1Activate = 'yes'"; 			
				$instructionSheetRes = imw_query($instructionSheetQuery) or die($instructionSheetQuery.imw_error());
				$instructionSheetNumRow = imw_num_rows($instructionSheetRes);
				if($instructionSheetNumRow){		
					extract(imw_fetch_array($instructionSheetRes));
					if($signSurgeon1Id==0){	
					?>
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=instructionsheet.php','ChartNote<?php echo $stub_id; ?>');">Instruction Sheet</a></td>					
						<!-- <td nowrap align="left" class="text_print link_slid_right">Instruction Sheet</td> -->
					<?php
					}
				}	
				?>	
					</tr>
				<?php			
			}
			if($patientConfNumRow == 0){
				echo "<script>alert('You do not have any pending chart(s).');</script>";
			}
		}
		elseif($anesthesiologistID){
			$patientConfQuery = "select patientConfirmationId,patientId  from patientconfirmation where anesthesiologist_id = '".$anesthesiologistID."' and dos = '$dos'"; 
			$patientConfRes = imw_query($patientConfQuery) or die($patientConfQuery.imw_error());
			$patientConfNumRow = imw_num_rows($patientConfRes);
			while($patientConfRow = imw_fetch_array($patientConfRes)) {
			?>	
				<tr>
			<?php
				$patientId = $patientConfRow['patientId'];
				$patientConfId = $patientConfRow['patientConfirmationId'];
				
				$patientStubIdQuery = "select stub_id from stub_tbl where patient_confirmation_id = '".$patientConfId."'"; 
				$patientStubIdRes = imw_query($patientStubIdQuery) or die($patientStubIdQuery.imw_error());
				extract(imw_fetch_array($patientStubIdRes));
			
				$patDataTblQuery = "select patient_fname,patient_mname,patient_lname from patient_data_tbl where patient_id  = ".$patientId; 			
				$patDataTblRes = imw_query($patDataTblQuery) or die($patDataTblQuery.imw_error());
				extract(imw_fetch_array($patDataTblRes));
				?>	
					<td nowrap class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td>
				<?php			
				//////////
				$chkConsentSignAllAnesthesiaQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$patientConfId."' AND signAnesthesia1Activate='yes' AND consent_purge_status!='true'";
				$chkConsentSignAllAnesthesiaRes = imw_query($chkConsentSignAllAnesthesiaQry) or die($chkConsentSignAllAnesthesiaQry.imw_error());
				$chkConsentAllSignAnesthesiaNumRow = imw_num_rows($chkConsentSignAllAnesthesiaRes);
				if($chkConsentAllSignAnesthesiaNumRow>0) {
					$chkConsentSignAnesthesia1Activate='';
					while($chkConsentSignAnesthesiaRow=imw_fetch_array($chkConsentSignAllAnesthesiaRes)) {
						$chkConsentFormName = $chkConsentSignAnesthesiaRow['surgery_consent_name'];
						$chkConsentSignAnesthesia1Activate = $chkConsentSignAnesthesiaRow['signAnesthesia1Activate'];
						$chkConsentSignAns1Id = $chkConsentSignAnesthesiaRow['signAnesthesia1Id'];
						$chkConsentFrmFormAnsStatus = $chkConsentSignAnesthesiaRow['form_status'];
						$surgeryConsentId = $chkConsentSignAnesthesiaRow['surgery_consent_id'];
						$consentTemplateId = $chkConsentSignAnesthesiaRow['consent_template_id'];
						if($chkConsentFrmFormAnsStatus == 'not completed' and $chkConsentSignAns1Id == 0){
							?>
							<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=consent_multiple_form.php&consentMultipleId=<?php echo $consentTemplateId; ?>&consentMultipleAutoIncrId=<?php echo $surgeryConsentId; ?>','ChartNote<?php echo $stub_id; ?>');"><?php echo $chkConsentFormName; ?></a></td>					
							<?php
						}
					}
				}
				//////////		
				$preOpPhyQuery = "select form_status,signAnesthesia1Id,signAnesthesia2Id,signAnesthesia3Id from localanesthesiarecord where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
				$preOpPhyNumRow = imw_num_rows($preOpPhyRes);
				if($preOpPhyNumRow){		
					extract(imw_fetch_array($preOpPhyRes));
					//if($form_status=='not completed' and ($signAnesthesia1Id==0 or $signAnesthesia2Id==0 or $signAnesthesia3Id==0)){	
					if($form_status=='not completed'){	
					?>
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=local_anes_record.php','ChartNote<?php echo $stub_id; ?>');">MAC/Local/Regional Anesthesia Record</a></td>					
						<!-- <td width="15%" align="left" class="text_print link_slid_right">Pre-Op Physician Orders</td> -->					
					<?php
					}
				}	
				$preOpPhyQuery = "select form_status,signAnesthesia1Id from genanesthesiarecord where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
				$preOpPhyNumRow = imw_num_rows($preOpPhyRes);			
				if($preOpPhyNumRow){		
					extract(imw_fetch_array($preOpPhyRes));
					//if($form_status=='not completed' and $signAnesthesia1Id==0){	
					if($form_status=='not completed'){	
					?>
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=gen_anes_rec.php','ChartNote<?php echo $stub_id; ?>');">General Anesthesia Record</a></td>					
						<!-- <td nowrap align="left" class="text_print link_slid_right">Post-Op Physician Orders</td> -->					
					<?php
					}
				}	
				$opRoomQuery = "select form_status,verifiedbyAnesthesiologist from operatingroomrecords where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
				$opRoomRes = imw_query($opRoomQuery) or die($opRoomQuery.imw_error());
				$opRoomNumRow = imw_num_rows($opRoomRes);
				if($opRoomNumRow){		
					extract(imw_fetch_array($opRoomRes));
					//if($form_status=='not completed' and $verifiedbyAnesthesiologist==""){	
					if($form_status=='not completed'){	
					?>
						<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=op_room_record.php','ChartNote<?php echo $stub_id; ?>');">Operating Room Record</a></td>					
						<!-- <td nowrap align="left" class="text_print link_slid_right">Operating Room Record</td>	 -->				
					<?php
					}
				}
						
			?>	
			</tr>
			<?php			
		}
		if($patientConfNumRow == 0){
				echo "<script>alert('You do not have any pending chart(s).');</script>";
		}
	}
	elseif($nurseID){
		$patientConfQuery = "select patientConfirmationId,patientId  from patientconfirmation where nurseID = '".$nurseID."' and dos = '$dos'"; 
		$patientConfRes = imw_query($patientConfQuery) or die($patientConfQuery.imw_error());
		$patientConfNumRow = imw_num_rows($patientConfRes);
		while($patientConfRow = imw_fetch_array($patientConfRes)) {
		?>	
			<tr>
		<?php
			$patientId = $patientConfRow['patientId'];
			$patientConfId = $patientConfRow['patientConfirmationId'];
			
			$patientStubIdQuery = "select stub_id from stub_tbl where patient_confirmation_id = '".$patientConfId."'"; 
			$patientStubIdRes = imw_query($patientStubIdQuery) or die($patientStubIdQuery.imw_error());
			extract(imw_fetch_array($patientStubIdRes));
		
			$patDataTblQuery = "select patient_fname,patient_mname,patient_lname from patient_data_tbl where patient_id  = ".$patientId; 			
			$patDataTblRes = imw_query($patDataTblQuery) or die($patDataTblQuery.imw_error());
			extract(imw_fetch_array($patDataTblRes));
			?>	
				<td nowrap class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td>
			<?php			
			//////////
			$chkConsentSignAllNurseQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$patientConfId."' AND signNurseActivate='yes' AND consent_purge_status!='true' and form_status IS NOT NULL";
			$chkConsentSignAllNurseRes = imw_query($chkConsentSignAllNurseQry) or die($chkConsentSignAllNurseQry.imw_error());
			$chkConsentAllSignNurseNumRow = imw_num_rows($chkConsentSignAllNurseRes);
			if($chkConsentAllSignNurseNumRow>0) {
				$chkConsentSignNurse1Activate='';
				while($chkConsentSignNurseRow=imw_fetch_array($chkConsentSignAllNurseRes)) {
					$chkConsentFormName = $chkConsentSignNurseRow['surgery_consent_name'];
					$chkConsentSignNurse1Activate = $chkConsentSignNurseRow['signNurseActivate'];
					$chkConsentSignNurse1Id = $chkConsentSignNurseRow['signNurseId'];
					$chkConsentFrmFormNurseStatus = $chkConsentSignNurseRow['form_status'];
					$surgeryConsentId = $chkConsentSignNurseRow['surgery_consent_id'];
					$consentTemplateId = $chkConsentSignNurseRow['consent_template_id'];
					if($chkConsentFrmFormNurseStatus == 'not completed' and $chkConsentSignNurse1Id == 0){
						?>
						<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=consent_multiple_form.php&consentMultipleId=<?php echo $consentTemplateId; ?>&consentMultipleAutoIncrId=<?php echo $surgeryConsentId; ?>','ChartNote<?php echo $stub_id; ?>');"><?php echo $chkConsentFormName; ?></a></td>					
						<?php
					}
				}
			}
			//////////			
			$preOpPhyQuery = "select form_status,signNurseId from preophealthquestionnaire where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
			$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
			$preOpPhyNumRow = imw_num_rows($preOpPhyRes);
			if($preOpPhyNumRow){		
				extract(imw_fetch_array($preOpPhyRes));
				//if($form_status=='not completed' and $signNurseId==0){	
				if($form_status=='not completed'){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=pre_op_health_quest.php','ChartNote<?php echo $stub_id; ?>');">Pre-Op Health Questionnaire</a></td>					
					<!-- <td width="15%" align="left" class="text_print link_slid_right">Pre-Op Physician Orders</td> -->					
				<?php
				}
			}	
			$preOpPhyQuery = "select form_status,signNurseId from preopnursingrecord where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
			$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
			$preOpPhyNumRow = imw_num_rows($preOpPhyRes);			
			if($preOpPhyNumRow){		
				extract(imw_fetch_array($preOpPhyRes));
				//if($form_status=='not completed' and $signNurseId == 0){	
				if($form_status=='not completed'){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=pre_op_nursing_record.php','ChartNote<?php echo $stub_id; ?>');">Pre-Op Nursing Record</a></td>					
					<!-- <td nowrap align="left" class="text_print link_slid_right">Post-Op Physician Orders</td> -->					
				<?php
				}
			}	
			$opRoomQuery = "select form_status,signNurseId from postopnursingrecord where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
			$opRoomRes = imw_query($opRoomQuery) or die($opRoomQuery.imw_error());
			$opRoomNumRow = imw_num_rows($opRoomRes);
			if($opRoomNumRow){		
				extract(imw_fetch_array($opRoomRes));
				//if($form_status=='not completed' and $signNurseId==0){	
				if($form_status=='not completed'){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=post_op_nursing_record.php','ChartNote<?php echo $stub_id; ?>');">Post-Op Nursing Record</a></td>					
					<!-- <td nowrap align="left" class="text_print link_slid_right">Operating Room Record</td>	 -->				
				<?php
				}
			}	
			$preOpPhyQuery = "select form_status,signNurseId from preopphysicianorders where patient_confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
			$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
			$preOpPhyNumRow = imw_num_rows($preOpPhyRes);
			if($preOpPhyNumRow){		
				extract(imw_fetch_array($preOpPhyRes));
				//if($form_status=='not completed' and $signNurseId==0){	
				if($form_status=='not completed'){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=pre_op_physician_orders.php','ChartNote<?php echo $stub_id; ?>');">Pre-Op Physician Orders</a></td>					
					<!-- <td width="15%" align="left" class="text_print link_slid_right">Pre-Op Physician Orders</td> -->					
				<?php
				}
			}		
			$preOpPhyQuery = "select form_status,signNurseId from postopphysicianorders where patient_confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
			$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
			$preOpPhyNumRow = imw_num_rows($preOpPhyRes);			
			if($preOpPhyNumRow){		
				extract(imw_fetch_array($preOpPhyRes));
				//if($form_status=='not completed' and $signNurseId==0){	
				if($form_status=='not completed'){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=post_op_physician_orders.php','ChartNote<?php echo $stub_id; ?>');">Post-Op Physician Orders</a></td>					
					<!-- <td nowrap align="left" class="text_print link_slid_right">Post-Op Physician Orders</td> -->					
				<?php
				}
			}
			$preOpPhyQuery = "select form_status,signNurseId from genanesthesianursesnotes where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
			$preOpPhyRes = imw_query($preOpPhyQuery) or die($preOpPhyQuery.imw_error());
			$preOpPhyNumRow = imw_num_rows($preOpPhyRes);
			if($preOpPhyNumRow){		
				extract(imw_fetch_array($preOpPhyRes));
				//if($form_status=='not completed' and $signNurseId==0 ){	
				if($form_status=='not completed'){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=gen_anes_nurse_notes.php','ChartNote<?php echo $stub_id; ?>');">General Anesthesia Nurses Notes</a></td>					
					<!-- <td width="15%" align="left" class="text_print link_slid_right">Pre-Op Physician Orders</td> -->					
				<?php
				}
			}	
			$opRoomQuery = "select form_status,verifiedbyNurse from operatingroomrecords where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
			$opRoomRes = imw_query($opRoomQuery) or die($opRoomQuery.imw_error());
			$opRoomNumRow = imw_num_rows($opRoomRes);
			if($opRoomNumRow){		
				extract(imw_fetch_array($opRoomRes));
				//if($form_status=='not completed' and $verifiedbyAnesthesiologist==""){	
				if($form_status=='not completed'){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=op_room_record.php','ChartNote<?php echo $stub_id; ?>');">Operating Room Record</a></td>					
					<!-- <td nowrap align="left" class="text_print link_slid_right">Operating Room Record</td>	 -->				
				<?php
				}
			}	
			$laserProQuery = "select form_status,signNurseId from laser_procedure_patient_table where confirmation_id = ".$patientConfId." and form_status IS NOT NULL"; 			
			$laserProRes = imw_query($laserProQuery) or die($laserProQuery.imw_error());
			$laserProNumRow = imw_num_rows($laserProRes);
			if($laserProNumRow){		
				extract(imw_fetch_array($laserProRes));
				//if($form_status=='not completed' and $signNurseId==0){	
				if($form_status=='not completed'){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=laser_procedure.php','ChartNote<?php echo $stub_id; ?>');">Laser Procedure</a></td>					
					<!-- <td nowrap align="left" class="text_print link_slid_right">Laser Procedure</td> -->
				<?php
				}
			}	
			$instructionSheetQuery = "select form_status,signNurseId,signNurseActivate from patient_instruction_sheet where patient_confirmation_id = ".$patientConfId." and signNurseActivate = 'yes'"; 			
			$instructionSheetRes = imw_query($instructionSheetQuery) or die($instructionSheetQuery.imw_error());
			$instructionSheetNumRow = imw_num_rows($instructionSheetRes);
			if($instructionSheetNumRow){		
				extract(imw_fetch_array($instructionSheetRes));
				if($signNurseId==0){	
				?>
					<!-- <td width="24%" class="text_homeb"><?php echo $patient_lname.','.$patient_fname; ?></td> -->
					<td nowrap align="left" ><a class="text_print link_slid_right" href="javascript:top.setwin('ChartNote','<?php echo $stub_id; ?>','mainpage.php?patient_id=<?php echo $patientId;?>&pConfId=<?php echo $patientConfId; ?>&multiwin=yes&stub_id=<?php echo $stub_id; ?>&myPage=instructionsheet.php','ChartNote<?php echo $stub_id; ?>');">Instruction Sheet</a></td>					
					<!-- <td nowrap align="left" class="text_print link_slid_right">Instruction Sheet</td> -->
				<?php
				}
			}	
		?>	
		</tr>
		<?php			
	}
		if($patientConfNumRow == 0){
			echo "<script>alert('You do not have any pending chart(s).');</script>";
		}
	}

	?>
	</table>
	</div>
	<div id="back"> 
	<table width="100%">
		<tr>
			<td align="center">
				<a id="backHistory" href="javascript:history.go(-1);" style="display:block;" onClick="MM_swapImage('backHistoryBt','','images/back_new.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('backHistoryBt','','images/back_new.gif',1)"><img src="images/back_new.gif" name="backHistoryBt" border="0" id="backHistoryBt" alt="Back"></a>
				<!-- <a href="#" onClick="history.go(-1)">Back</a>  -->
			</td>
		</tr>	
	</table>
	</div>
</form>

</body>
</html>
